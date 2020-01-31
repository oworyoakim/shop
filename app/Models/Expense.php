<?php

namespace App\Models;

use App\Traits\Commentable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Expense extends Model
{
    use SoftDeletes, Commentable;

    protected $table = 'expenses';
    protected $dates = ['deleted_at', 'expense_date'];

    protected $guarded = [];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function type()
    {
        return $this->belongsTo(ExpenseType::class, 'expense_type_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeUncanceled(Builder $query)
    {
        return $query->where('status','<>', 'canceled');
    }

    public  function scopeCanceled(Builder $query)
    {
        return $query->where('status', 'canceled');
    }

    public  function scopePending(Builder $query)
    {
        return $query->where('status', 'pending');
    }

    public  function scopeApproved(Builder $query)
    {
        return $query->where('status', 'approved');
    }

    public  function scopeDeclined(Builder $query)
    {
        return $query->where('status', 'declined');
    }

    public function scopePrepaid(Builder $query)
    {
        $date = Carbon::today()->toDateString();
        return $this->scopeApproved($query)->whereDate('transact_date','>', $date);
    }

}
