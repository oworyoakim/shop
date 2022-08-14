<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    public $timestamps = false;
    protected $table = "permissions";

    const CATEGORY_TENANT = 'tenant';
    const CATEGORY_LANDLORD = 'landlord';

    public function scopeForTenant($query)
    {
        return $query->where('category', static::CATEGORY_TENANT);
    }

    public function scopeForLandlord($query)
    {
        return $query->where('category', static::CATEGORY_LANDLORD);
    }

    public function getDetails()
    {
        $permission = new \stdClass();
        $permission->id = $this->id;
        $permission->title = $this->title;
        $permission->slug = $this->slug;
        $permission->description = $this->description;
        $permission->parentId = $this->parent_id;
        $permission->category = $this->category;
        return $permission;
    }

    public function parent()
    {
        return $this->hasOne(Permission::class, 'id', 'parent_id');
    }

}
