<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stock extends Model {

    use SoftDeletes;
    protected $table = 'stocks';
    protected $dates = ['deleted_at'];

    protected $guarded = [];

    public function scopeActive(Builder $query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired(Builder $query)
    {
        return $query->where('status', 'expired');
    }

    public function branch() {
        return $this->belongsTo(Branch::class);
    }

    public function item() {
        return $this->belongsTo(Item::class);
    }

    public function user() {
        return $this->belongsTo(User::class,'user_id');
    }

    public function purchase() {
        return $this->belongsTo(Purchase::class,'purchase_id');
    }

}
