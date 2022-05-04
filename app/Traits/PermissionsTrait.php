<?php

namespace App\Traits;

trait PermissionsTrait
{
    public function setPermissionsAttribute($value)
    {
        $this->attributes['permissions'] = json_encode($value);
    }

    /**
     * Checks if a user has the specified permission
     * @param string $permission
     *
     * @return bool
     */
    public function hasAccess(string $permission) {
        return !empty($this->permissions[$permission]);
    }

    /**
     * Checks if a user has at least one of the specified permissions
     * @param array $permissions
     *
     * @return bool
     */
    public function hasAnyAccess(array $permissions = []) {
        foreach ($permissions as $permission){
            if($this->hasAccess($permission)) {
                return true;
            }
        }
        return false;
    }
}
