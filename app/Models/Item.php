<?php

namespace App\Models;

use App\Traits\Commentable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes,Commentable;

    protected $table = 'items';
    protected $dates = ['deleted_at'];
    protected $guarded = [];

    public function category(){
        return $this->belongsTo(Category::class,'category_id');
    }

    public function unit(){
        return $this->belongsTo(Unit::class,'unit_id','id');
    }

    public function stocks(){
        return $this->hasMany(Stock::class,'item_id');
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function salesLines() {
        return $this->hasMany(SaleItem::class);
    }


}
