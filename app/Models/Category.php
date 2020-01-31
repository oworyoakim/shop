<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
