<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class Category
 * @package App\Models
 * @property int id
 * @property int tenant_id
 * @property string title
 * @property string slug
 * @property string description
 * @property int user_id
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class Category extends Model
{
    use SoftDeletes;

    protected $table = 'categories';
    protected $dates = ['deleted_at'];

    protected $guarded = [];

    public function items(){
        return $this->hasMany(Item::class,'category_id');
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
}
