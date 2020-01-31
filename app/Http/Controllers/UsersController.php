<?php

namespace App\Http\Controllers;

use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Validator;
use stdClass;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $users = User::all()->transform(function (User $item) {
                $user = new stdClass();
                $user->id = $item->id;
                $user->firstName = $item->first_name;
                $user->lastName = $item->last_name;
                $user->email = $item->email;
                $user->username = $item->username;
                $user->avatar = $item->avatar;
                $user->permissions = $item->permissions;
                $user->currentBalance = $item->getBalance();
                // user role
                $user->role = null;
                if ($role = $item->roles()->first())
                {
                    $user->role = new stdClass();
                    $user->role->id = $role->id;
                    $user->role->slug = $role->slug;
                    $user->role->name = $role->name;
                    $user->role->permissions = $role->permissions;
                }
                // user branch
                $user->branch = null;
                if ($item->branch)
                {
                    $user->branch = new stdClass();
                    $user->branch->id = $item->branch->id;
                    $user->branch->name = $item->branch->name;
                }
                $user->isAdmin = $item->inRole('admin');
                $user->isManager = $item->inRole('manager');
                $user->isCashier = $item->inRole('cashier');
                $user->canBeEdited = Sentinel::hasAnyAccess(['users.update']);
                $user->canBeDeleted = Sentinel::hasAnyAccess(['users.delete']);
                $user->canBeLocked = $item->isActive() && Sentinel::hasAnyAccess(['users.lock']);
                $user->canBeUnlocked = !$item->isActive() && Sentinel::hasAnyAccess(['users.unlock']);
                return $user;
            });
            return response()->json($users);
        } catch (Exception $ex)
        {
            Log::error("GET_USERS: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function store(Request $request)
    {
        try
        {
            if (!Sentinel::hasAnyAccess(['users.create']))
            {
                throw new Exception('Permission Denied!');
            }

            $firstName = $request->get('firstName');
            $lastName = $request->get('lastName');
            $email = $request->get('email');
            $password = $request->get('password');
            $username = $request->get('username');
            $gender = $request->get('gender');
            $phone = $request->get('phone');
            $address = $request->get('address');
            $country = $request->get('country');
            $city = $request->get('city');
            $branchId = $request->get('branchId');
            $roleId = $request->get('roleId');

            $rules = [
                'firstName' => 'required|alpha',
                'lastName' => 'required|alpha',
                'gender' => 'required|alpha',
                'username' => 'required|alphanumeric|unique:users',
                'password' => 'required',
                'roleId' => 'required',
                'phone' => 'required',
                'address' => 'required',
            ];

            if ($email)
            {
                $rules['email'] = 'email|unique:users';
            }

            $credentials = [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'username' => $username,
                'email' => $email,
                'phone' => $phone,
                'gender' => $gender,
                'address' => $address,
                'city' => $city,
                'country' => $country,
                'password' => $password,
                'branch_id' => $branchId,
                'avatar' => '/images/avatar.png',
                'password_last_changed' => Carbon::now(),
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails())
            {
                $errors = "";
                foreach ($validator->errors()->messages() as $key => $messages)
                {
                    $errors .= "<p class='text-small'>" . ucfirst($key) . ": " . implode('<br/>', $messages) . "</p>";
                }
                throw new Exception("Validation Error: {$errors}");
            }

            $role = Sentinel::findRoleById($roleId);

            if (!$role)
            {
                throw new Exception("Role not found!");
            }
            settings()->beginTransaction();
            // register the user
            $user = Sentinel::registerAndActivate($credentials);
            // attach this user to the role
            $user->roles()->attach($role);
            settings()->commitTransaction();
            return response()->json("Record Saved!");
        } catch (Exception $ex)
        {
            settings()->rollbackTransaction();
            Log::error("CREATE_USER: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function update(Request $request)
    {
        try
        {
            if (!Sentinel::hasAnyAccess(['users.update']))
            {
                throw new Exception('Permission Denied!');
            }

            $id = $request->get('id');
            $user = Sentinel::findUserById($id);
            if (!$user)
            {
                throw new Exception('User Not Found!');
            }

            $firstName = $request->get('firstName');
            $lastName = $request->get('lastName');
            $phone = $request->get('phone');
            $email = $request->get('email');
            $address = $request->get('address');
            $branchId = $request->get('branchId');
            $roleId = $request->get('roleId');
            $gender = $request->get('gender');
            $password = $request->get('password');
            $country = $request->get('country');
            $city = $request->get('city');

            $rules = [
                'firstName' => 'required|alpha',
                'lastName' => 'required|alpha',
                'gender' => 'required|alpha',
                'roleId' => 'required',
                'phone' => 'required',
                'address' => 'required',
            ];

            if ($email && $user->email != $email)
            {
                $rules['email'] = 'email|unique:users';
            }

            $credentials = [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'phone' => $phone,
                'gender' => $gender,
                'address' => $address,
                'city' => $city,
                'country' => $country,
                'branch_id' => $branchId,
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails())
            {
                $errors = "";
                foreach ($validator->errors()->messages() as $key => $messages)
                {
                    $errors .= "<p class='text-small'>" . ucfirst($key) . ": " . implode('<br/>', $messages) . "</p>";
                }
                throw new Exception("Validation Error: {$errors}");
            }

            $role = Sentinel::findRoleById($roleId);
            if (!$role)
            {
                throw new Exception("Role not found!");
            }
            if ($password)
            {
                $credentials['password'] = $password;
                $credentials['password_last_changed'] = Carbon::now();
            }
            // update here
            settings()->beginTransaction();

            $user->update($credentials);

            $userRole = $user->roles()->first();

            if (!$userRole || $userRole->id != $roleId)
            {
                // attach role here
                $user->roles()->attach($role);
            }
            settings()->commitTransaction();
            return response()->json("Record Saved!");
        } catch (Exception $ex)
        {
            settings()->rollbackTransaction();
            Log::error("UPDATE_USER: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function delete(Request $request)
    {
        try
        {
            if (!Sentinel::hasAnyAccess(['users.delete']))
            {
                throw new Exception('Permission Denied!');
            }

            $userId = $request->get('user_id');
            $user = User::find($userId);
            if (!$user)
            {
                throw new Exception('User Not Found!');
            }

            $user->roles()->detach();

            $user->delete();

            return response()->json('User deleted!');

        } catch (Exception $ex)
        {
            Log::error("DELETE_USER: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function lock(Request $request)
    {
        try
        {
            if (!Sentinel::hasAnyAccess(['users.lock']))
            {
                throw new Exception("Permission Denied!");
            }

            $userId = $request->get('user_id');
            $user = Sentinel::getUserRepository()->findById($userId);

            if (!$user)
            {
                throw new Exception("User not found!");
            }
            settings()->beginTransaction();
            $user->update([
                'active' => false
            ]);
            settings()->commitTransaction();
            return response()->json('User Locked!');
        } catch (Exception $ex)
        {
            settings()->rollbackTransaction();
            Log::error("LOCK_USER: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function unlock(Request $request)
    {
        try
        {
            if (!Sentinel::hasAnyAccess(['users.unlock']))
            {
                throw new Exception("Permission Denied!");
            }

            $userId = $request->get('user_id');
            $user = Sentinel::getUserRepository()->findById($userId);

            if (!$user)
            {
                throw new Exception("User not found!");
            }
            settings()->beginTransaction();
            $user->update([
                'active' => true
            ]);
            settings()->commitTransaction();
            return response()->json('User Unlocked!');
        } catch (Exception $ex)
        {
            settings()->rollbackTransaction();
            Log::error("UNLOCK_USER: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function getPermissions(Request $request)
    {
        try
        {
            $userId = $request->get('user_id');
            if (!$userId)
            {
                throw new Exception('User Required!');
            }
            $user = User::find($userId);
            if (!$user)
            {
                throw new Exception('User Not Found!');
            }

            $permissions = $user->permissions;

            return response()->json($permissions);

        } catch (Exception $ex)
        {
            Log::error("GET_USER_PERMISSIONS: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function updatePermissions(Request $request)
    {
        try
        {
            if (!Sentinel::hasAnyAccess(['users.update']))
            {
                throw new Exception('Permission Denied!');
            }
            $userId = $request->get('user_id');
            $user = User::find($userId);
            if (!$user)
            {
                throw new Exception('User Not Found!');
            }
            $permissions = $request->get('permissions');

            $user->permissions = array();

            foreach ($permissions as $permission)
            {
                $user->addPermission($permission);
            }

            $user->save();

            return response()->json("User Permissions Updated!");
        } catch (Exception $ex)
        {
            Log::error("UPDATE_USER_PERMISSIONS: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

}
