<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\LandlordBaseController;
use App\Models\Landlord\Unit;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UnitsController extends LandlordBaseController
{
    public function index()
    {
        try
        {
            $loggedInUser = $this->getUser();
            $units = Unit::all()->map(function (Unit $unit) use ($loggedInUser) {
                $unit->canBeEdited = $loggedInUser->hasAnyAccess(['units.update']);
                $unit->canBeDeleted = $loggedInUser->hasAnyAccess(['units.delete']);
                return $unit;
            });
            return response()->json($units);
        } catch (\Throwable $ex)
        {
            return $this->handleJsonRequestException("GET_UNITS", $ex);
        }
    }

    public function store(Request $request)
    {
        try
        {
            $loggedInUser = $this->getUser();
            if (!$loggedInUser->hasAnyAccess(['units.create']))
            {
                return response()->json([
                    'message' => "Permission Denied!"
                ], Response::HTTP_FORBIDDEN);
            }

            $validator = Validator::make($request->all(), [
                'title' => 'required|alpha',
            ]);

            if ($validator->fails())
            {
                return response()->json([
                    'message' => "Validation Error",
                    'errors' => $validator->errors()->messages()
                ], Response::HTTP_BAD_REQUEST);
            }

            $title = $request->get('title');
            $description = $request->get('description');
            Unit::query()->create([
                'title' => $title,
                'slug' => Str::slug($title),
                'description' => $description,
                'user_id' => $loggedInUser->getUserId()
            ]);
            return response()->json('Unit Created!');
        } catch (\Throwable $ex)
        {
            return $this->handleJsonRequestException("CREATE_UNIT", $ex);
        }
    }

    public function update(Request $request, Unit $unit)
    {
        try
        {
            $loggedInUser = $this->getUser();
            if (!$loggedInUser->hasAnyAccess(['units.update']))
            {
                return response()->json([
                    'message' => "Permission Denied!"
                ], Response::HTTP_FORBIDDEN);
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
                    return response()->json([
                        'message' => "Validation Error",
                        'errors' => $validator->errors()->messages()
                    ], Response::HTTP_BAD_REQUEST);
                }
                $unit->slug = Str::slug($title);
            }
            $unit->title = $title;
            $unit->description = $description;
            $unit->save();
            return response()->json('Unit Updated!');
        } catch (\Throwable $ex)
        {
            return $this->handleJsonRequestException("UPDATE_UNIT", $ex);
        }
    }

    public function delete(Request $request, Unit $unit)
    {
        try
        {
            $loggedInUser = $this->getUser();
            if (!$loggedInUser->hasAnyAccess(['units.delete']))
            {
                return response()->json([
                    'message' => "Permission Denied!"
                ], Response::HTTP_FORBIDDEN);
            }
            $unit->delete();
            return response()->json('Unit Deleted!');
        } catch (\Throwable $ex)
        {
            return $this->handleJsonRequestException("DELETE_UNIT", $ex);
        }
    }
}
