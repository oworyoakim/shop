<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class ExpenseType
 * @package App\Models
 * @property int id
 * @property string title
 * @property string description
 * @property string recurrence
 * @property int user_id
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class ExpenseType extends Model
{
    use SoftDeletes;

    protected $table = 'expense_types';
    protected $dates = ['deleted_at'];
    protected $guarded = [];

    const RECURRENCE_NONE = 'none';
    const RECURRENCE_DAILY = 'daily';
    const RECURRENCE_MONTHLY = 'monthly';
    const RECURRENCE_YEARLY = 'yearly';


    public function expenses(){
        return $this->hasMany(Expense::class);
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
}
