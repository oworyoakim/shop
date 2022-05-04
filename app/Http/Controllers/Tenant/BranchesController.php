<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\TenantBaseController;
use App\Models\Branch;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use stdClass;

class BranchesController extends TenantBaseController
{
    public function index()
    {
        try
        {
            $branches = Branch::all()->transform(function (Branch $item) {
                $branch = new stdClass();
                $branch->id = $item->id;
                $branch->name = $item->name;
                $branch->phone = $item->phone;
                $branch->email = $item->email;
                $branch->country = $item->country;
                $branch->city = $item->city;
                $branch->address = $item->address;
                $branch->balance = $item->getBalance();

                $branch->manager = null;

                $manager = $item->manager();
                if($manager){
                    $branch->manager = new stdClass();
                    $branch->manager->id = $manager->id;
                    $branch->manager->firstName = $manager->first_name;
                    $branch->manager->lastName = $manager->last_name;
                    $branch->manager->fullName = $manager->fullName();
                    $branch->manager->email = $manager->email;
                    $branch->manager->balance = $manager->getBalance();
                }

                $branch->canBeEdited = Sentinel::hasAnyAccess(['branches.update']);
                $branch->canBeDeleted = Sentinel::hasAnyAccess(['branches.delete']);
                $branch->canBeLocked = $item->isActive() && Sentinel::hasAnyAccess(['branches.lock']);
                $branch->canBeUnlocked = !$item->isActive() && Sentinel::hasAnyAccess(['branches.unlock']);
                return $branch;
            });
            return response()->json($branches);
        } catch (Exception $ex)
        {
            Log::error("GET_BRANCHES: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function store(Request $request)
    {
        try
        {
            if (!Sentinel::hasAnyAccess(['branches.create']))
            {
                throw new Exception("Permission Denied!");
            }
            $user = Sentinel::getUser();
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:branches',
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
            $name = $request->get('name');
            $phone = $request->get('phone');
            $email = $request->get('email');
            $country = $request->get('country');
            $city = $request->get('city');
            $address = $request->get('address');

            settings()->beginTransaction();

            Branch::create([
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
                'country' => $country,
                'city' => $city,
                'address' => $address,
                'user_id' => $user->getUserId(),
            ]);
            settings()->commitTransaction();
            return response()->json('Branch Created!');
        } catch (Exception $ex)
        {
            settings()->rollbackTransaction();
            Log::error("CREATE_BRANCH: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function update(Request $request)
    {
        try
        {
            if (!Sentinel::hasAnyAccess(['branches.update']))
            {
                throw new Exception("Permission Denied!");
            }
            $branchId = $request->get('id');
            $branch = Branch::query()->find($branchId);

            if (!$branch)
            {
                throw new Exception("Branch not found!");
            }

            $name = $request->get('name');
            $phone = $request->get('phone');
            $email = $request->get('email');
            $country = $request->get('country');
            $city = $request->get('city');
            $address = $request->get('address');

            if ($name != $branch->name)
            {
                $validator = Validator::make($request->all(), [
                    'name' => 'required|unique:branches',
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
                $branch->name = $name;
            }
            $branch->phone = $phone;
            $branch->email = $email;
            $branch->country = $country;
            $branch->city = $city;
            $branch->address = $address;
            $branch->save();
            return response()->json('Branch Updated!');
        } catch (Exception $ex)
        {
            Log::error("UPDATE_BRANCH: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function delete(Request $request)
    {
        try
        {
            if (!Sentinel::hasAnyAccess(['branches.delete']))
            {
                throw new Exception("Permission Denied!");
            }

            $branchId = $request->get('branch_id');
            $branch = Branch::query()->find($branchId);

            if (!$branch)
            {
                throw new Exception("Branch not found!");
            }
            settings()->beginTransaction();
            $branch->delete();
            settings()->commitTransaction();
            return response()->json('Branch Deleted!');
        } catch (Exception $ex)
        {
            settings()->rollbackTransaction();
            Log::error("DELETE_BRANCH: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function lock(Request $request)
    {
        try
        {
            if (!Sentinel::hasAnyAccess(['branches.lock']))
            {
                throw new Exception("Permission Denied!");
            }

            $branchId = $request->get('branch_id');
            $branch = Branch::query()->find($branchId);

            if (!$branch)
            {
                throw new Exception("Branch not found!");
            }
            settings()->beginTransaction();
            $branch->active = false;
            $branch->save();
            settings()->commitTransaction();
            return response()->json('Branch Locked!');
        } catch (Exception $ex)
        {
            settings()->rollbackTransaction();
            Log::error("LOCK_BRANCH: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function unlock(Request $request)
    {
        try
        {
            if (!Sentinel::hasAnyAccess(['branches.unlock']))
            {
                throw new Exception("Permission Denied!");
            }

            $branchId = $request->get('branch_id');
            $branch = Branch::query()->find($branchId);

            if (!$branch)
            {
                throw new Exception("Branch not found!");
            }
            settings()->beginTransaction();
            $branch->active = true;
            $branch->save();
            settings()->commitTransaction();
            return response()->json('Branch Unlocked!');
        } catch (Exception $ex)
        {
            settings()->rollbackTransaction();
            Log::error("UNLOCK_BRANCH: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
