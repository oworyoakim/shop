<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;

    protected $table = 'suppliers';
    protected $dates = ['deleted_at'];

    protected $guarded = [];

    public function supplies(){
        return $this->belongsTo(Purchase::class);
    }

    public function returns(){
        return $this->hasMany(PurchasesReturn::class);
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function payables(){
        return $this->hasMany(PurchasesPayable::class);
    }
}
