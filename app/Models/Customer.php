<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $table = 'customers';
    protected $dates = ['deleted_at'];

    protected $guarded = [];

    public function orders() {
        return $this->hasMany(Sale::class);
    }

    public function returns() {
        return $this->hasMany(SalesReturn::class);
    }

    public function receivables() {
        return $this->hasMany(SalesReceivable::class);
    }
}
