<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Str;

class RolesController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $perms = [];
            $permissions = Permission::where('parent_id', 0)->get();
            foreach ($permissions as $permission)
            {
                array_push($perms, $permission);
                $subs = Permission::where('parent_id', $permission->id)->get();
                foreach ($subs as $sub)
                {
                    array_push($perms, $sub);
                }
            }
            $data = [
                'roles' => Role::all()->transform(function (Role $role) {
                    $role->perms = array_keys($role->getPermissions());
                    return $role;
                }),
                'permissions' => $perms,
            ];
            return response()->json($data);
        } catch (Exception $ex)
        {
            Log::error("GET_ROLES: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function store(Request $request)
    {
        try
        {
            if (!Sentinel::hasAnyAccess(['users.roles']))
            {
                throw new Exception('Permission Denied!');
            }

            $name = $request->get('name');
            $slug = Str::slug($name);

            $oldRole = Role::whereSlug($slug)->first();

            if ($oldRole)
            {
                throw new Exception("Role {$name} already exists");
            }

            $role = new Role;
            $role->name = $name;
            $role->slug = $slug;
            $role->description = $request->get('description');
            $role->save();
            return response()->json("Record Saved!");
        } catch (Exception $ex)
        {
            Log::error("CREATE_ROLE: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function update(Request $request)
    {
        try
        {
            if (!Sentinel::hasAnyAccess(['users.roles']))
            {
                throw new Exception('Permission Denied!');
            }

            $id = $request->get('id');
            $role = Role::find($id);
            if (!$role)
            {
                throw new Exception('Role Not Found!');
            }

            $name = $request->get('name');
            $slug = Str::slug($name);

            $oldRole = Role::whereSlug($slug)->first();

            if ($oldRole && $oldRole->id != $role->id)
            {
                throw new Exception("Role {$name} already exists");
            }

            $role->name = $name;
            $role->slug = $slug;
            $role->description = $request->get('description');
            $role->save();
            return response()->json("Record Saved!");
        } catch (Exception $ex)
        {
            Log::error("UPDATE_ROLE: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function delete(Request $request)
    {
        try
        {
            if (!Sentinel::hasAnyAccess(['users.roles']))
            {
                throw new Exception('Permission Denied!');
            }

            $role_id = $request->get('role_id');
            $role = Role::find($role_id);
            if (!$role)
            {
                throw new Exception('Role Not Found!');
            }

            $role->users()->detach();

            $role->delete();

            return response()->json('Role deleted!');

        } catch (Exception $ex)
        {
            Log::error("DELETE_ROLE: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function getPermissions(Request $request)
    {
        try
        {
            $role_id = $request->get('role_id');
            if (!$role_id)
            {
                throw new Exception('Role Required!');
            }
            $role = Role::find($role_id);
            if (!$role)
            {
                throw new Exception('Role Not Found!');
            }

            $permissions = $role->permissions;

            return response()->json($permissions);

        } catch (Exception $ex)
        {
            Log::error("GET_PERMISSIONS: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function updatePermissions(Request $request)
    {
        try
        {
            if (!Sentinel::hasAnyAccess(['users.roles']))
            {
                throw new Exception('Permission Denied!');
            }
            $role_id = $request->get('role_id');
            $role = Role::find($role_id);
            if (!$role)
            {
                throw new Exception('Role Not Found!');
            }
            $permissions = $request->get('permissions');

            $role->permissions = array();

            foreach ($permissions as $permission)
            {
                $role->addPermission($permission);
            }

            $role->save();

            return response()->json("Role Permissions Updated!");
        } catch (Exception $ex)
        {
            Log::error("UPDATE_PERMISSIONS: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

}
