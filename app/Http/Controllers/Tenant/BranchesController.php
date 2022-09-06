<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\TenantBaseController;
use App\Models\Tenant\Branch;
use App\Models\Tenant\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BranchesController extends TenantBaseController
{
    public function index()
    {
        try
        {
            $loggedInUser = $this->getUser();

            $branches = Branch::all()->map(function (Branch $item) use ($loggedInUser) {
                $branch = $item->getDetails();
                $branch->canBeEdited = $loggedInUser->hasAnyAccess(['tenant.branches.update']);
                $branch->canBeDeleted = $loggedInUser->hasAnyAccess(['tenant.branches.delete']);
                $branch->canBeLocked = $item->isActive() && $loggedInUser->hasAnyAccess(['tenant.branches.lock']);
                $branch->canBeUnlocked = !$item->isActive() && $loggedInUser->hasAnyAccess(['tenant.branches.unlock']);
                return $branch;
            });
            return response()->json($branches);
        } catch (\Throwable $ex)
        {
            return $this->handleJsonRequestException('GET_BRANCHES', $ex);
        }
    }

    public function store(Request $request)
    {
        try
        {
            $loggedInUser = $this->getUser();
            if (!$loggedInUser->hasAnyAccess(['tenant.branches.create']))
            {
                return response()->json("Permission Denied!", Response::HTTP_FORBIDDEN);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required',
            ]);

            if ($validator->fails())
            {
                $errors = "";
                foreach ($validator->errors()->messages() as $key => $messages)
                {
                    $errors .= "<p class='text-small'>" . ucfirst($key) . ": " . implode('<br/>', $messages) . "</p>";
                }
                return response()->json("Validation Error: {$errors}", Response::HTTP_BAD_REQUEST);
            }
            $name = $request->get('name');
            $phone = $request->get('phone');
            $email = $request->get('email');
            $country = $request->get('country');
            $city = $request->get('city');
            $address = $request->get('address');

            $branch = Branch::query()->create([
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
                'country' => $country,
                'city' => $city,
                'address' => $address,
                'user_id' => $loggedInUser->id,
                'tenant_id' => $loggedInUser->tenant_id,
            ]);
            return response()->json('Branch Created!');
        } catch (\Throwable $ex)
        {
            return $this->handleJsonRequestException('CREATE_BRANCH', $ex);
        }
    }

    public function update(Request $request, $id)
    {
        try
        {
            $loggedInUser = $this->getUser();

            if (!$loggedInUser->hasAnyAccess(['tenant.branches.update']))
            {
                return response()->json("Permission Denied!", Response::HTTP_FORBIDDEN);
            }

            $branch = Branch::query()->find($id);

            if (!$branch)
            {
                return response()->json("Branch not found!", Response::HTTP_FORBIDDEN);
            }

            $name = $request->get('name');
            $phone = $request->get('phone');
            $email = $request->get('email');
            $country = $request->get('country');
            $city = $request->get('city');
            $address = $request->get('address');

            $validator = Validator::make($request->all(), [
                'name' => 'required',
            ]);

            if ($validator->fails())
            {
                $errors = "";
                foreach ($validator->errors()->messages() as $key => $messages)
                {
                    $errors .= "<p class='text-small'>" . ucfirst($key) . ": " . implode('<br/>', $messages) . "</p>";
                }
                return response()->json("Validation Error: {$errors}", Response::HTTP_BAD_REQUEST);
            }

            $branch->name = $name;
            $branch->phone = $phone;
            $branch->email = $email;
            $branch->country = $country;
            $branch->city = $city;
            $branch->address = $address;
            $branch->save();
            return response()->json('Branch Updated!');
        } catch (\Throwable $ex)
        {
            return $this->handleJsonRequestException('UPDATE_BRANCH', $ex);
        }
    }

    public function delete(Request $request, $id)
    {
        try
        {
            $loggedInUser = $this->getUser();

            if (!$loggedInUser->hasAnyAccess(['tenant.branches.delete']))
            {
                return response()->json("Permission Denied!", Response::HTTP_FORBIDDEN);
            }

            $branch = Branch::query()->find($id);

            if (!$branch)
            {
                return response()->json("Branch not found!", Response::HTTP_FORBIDDEN);
            }
            $branch->delete();
            return response()->json('Branch Deleted!');
        } catch (\Throwable $ex)
        {
            return $this->handleJsonRequestException('DELETE_BRANCH', $ex);
        }
    }

    public function lock(Request $request, $id)
    {
        try
        {
            $loggedInUser = $this->getUser();

            if (!$loggedInUser->hasAnyAccess(['tenant.branches.lock']))
            {
                return response()->json("Permission Denied!", Response::HTTP_FORBIDDEN);
            }

            $branch = Branch::query()->find($id);

            if (!$branch)
            {
                return response()->json("Branch not found!", Response::HTTP_FORBIDDEN);
            }
            $branch->update(['active' => false]);
            return response()->json('Branch Locked!');
        } catch (\Throwable $ex)
        {
            return $this->handleJsonRequestException('LOCK_BRANCH', $ex);
        }
    }

    public function unlock(Request $request, $id)
    {
        try
        {
            $loggedInUser = $this->getUser();

            if (!$loggedInUser->hasAnyAccess(['tenant.branches.unlock']))
            {
                return response()->json("Permission Denied!", Response::HTTP_FORBIDDEN);
            }

            $branch = Branch::query()->find($id);

            if (!$branch)
            {
                return response()->json("Branch not found!", Response::HTTP_FORBIDDEN);
            }

            $branch->update(['active' => true]);
            return response()->json('Branch Unlocked!');
        } catch (\Throwable $ex)
        {
            return $this->handleJsonRequestException('UnLOCK_BRANCH', $ex);
        }
    }
}
