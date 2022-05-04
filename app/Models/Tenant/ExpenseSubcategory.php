<?php

namespace App\Models\Tenant;

use App\Models\Landlord\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class ExpenseSubcategory
 * @package App\Models
 * @property int id
 * @property int tenant_id
 * @property int expense_category_id
 * @property string title
 * @property string description
 * @property string recurrence
 * @property int user_id
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class ExpenseSubcategory extends Model
{
    use SoftDeletes;

    protected $table = 'expense_subcategories';
    protected $dates = ['deleted_at'];
    protected $guarded = [];

    const RECURRENCE_NONE = 'none';
    const RECURRENCE_DAILY = 'daily';
    const RECURRENCE_MONTHLY = 'monthly';
    const RECURRENCE_YEARLY = 'yearly';


    public function expenses()
    {
        return $this->hasMany(Expense::class, 'expense_subcategory_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function expense_category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }
}
