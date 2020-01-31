<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseType extends Model
{
    use SoftDeletes;

    protected $table = 'expense_types';
    protected $dates = ['deleted_at'];

    protected $guarded = [];

    public function expenses(){
        return $this->hasMany(Expense::class);
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
}
