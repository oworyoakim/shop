<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use stdClass;

class UnitsController extends Controller
{
    public function index()
    {
        try
        {
            $units = Unit::all()->transform(function (Unit $item) {
                $unit = new stdClass();
                $unit->id = $item->id;
                $unit->slug = $item->slug;
                $unit->title = $item->title;
                $unit->description = $item->description;
                $unit->canBeEdited = Sentinel::hasAnyAccess(['items.units']);
                $unit->canBeDeleted = Sentinel::hasAnyAccess(['items.units']);
                return $unit;
            });
            return response()->json($units);
        } catch (Exception $ex)
        {
            Log::error("GET_UNITS: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function store(Request $request)
    {
        try
        {
            if (!Sentinel::hasAnyAccess(['items.units']))
            {
                throw new Exception("Permission Denied!");
            }

            $validator = Validator::make($request->all(), [
                'title' => 'required|unique:units|alpha',
            ]);
            if ($validator->fails())
            {
                $errors = "";
                foreach ($validator->errors()->messages() as $key => $messages)
                {
                    $errors .= "<p class='text-small'>" . ucfirst($key) . ": " . implode('<br/>', $messages) . "</p>";
                }
                throw new Exception("Validation Error: {$errors}");
            }
            $title = $request->get('title');
            $description = $request->get('description');
            $user = Sentinel::getUser();
            Unit::create([
                'title' => $title,
                'slug' => Str::slug($title),
                'description' => $description,
                'user_id' => $user->getUserId()
            ]);
            return response()->json('Unit Created!');
        } catch (Exception $ex)
        {
            Log::error("CREATE_UNIT: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function update(Request $request)
    {
        try
        {
            if (!Sentinel::hasAnyAccess(['items.units']))
            {
                throw new Exception("Permission Denied!");
            }

            $unitId = $request->get('id');
            $unit = Unit::query()->find($unitId);

            if (!$unit)
            {
                throw new Exception("Unit not found!");
            }

            $title = $request->get('title');
            $description = $request->get('description');
            if ($title != $unit->title)
            {
                $validator = Validator::make($request->all(), [
                    'title' => 'required|unique:units|alpha',
                ]);
                if ($validator->fails())
                {
                    $errors = "";
                    foreach ($validator->errors()->messages() as $key => $messages)
                    {
                        $errors .= "<p class='text-small'>" . ucfirst($key) . ": " . implode('<br/>', $messages) . "</p>";
                    }
                    throw new Exception("Validation Error: {$errors}");
                }
                $unit->slug = Str::slug($title);
            }
            $unit->title = $title;
            $unit->description = $description;
            $unit->save();
            return response()->json('Unit Updated!');
        } catch (Exception $ex)
        {
            Log::error("UPDATE_UNIT: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function delete(Request $request)
    {
        try
        {
            if (!Sentinel::hasAnyAccess(['items.units']))
            {
                throw new Exception("Permission Denied!");
            }

            $unitId = $request->get('unit_id');
            $unit = Unit::query()->find($unitId);

            if (!$unit)
            {
                throw new Exception("Unit not found!");
            }
            settings()->beginTransaction();
            $unit->items()->delete();
            $unit->delete();
            settings()->commitTransaction();
            return response()->json('Unit Deleted!');
        } catch (Exception $ex)
        {
            settings()->rollbackTransaction();
            Log::error("DELETE_UNIT: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
