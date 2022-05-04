<?php

namespace App\Models\Tenant;

use App\Models\Landlord\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class ExpenseCategory
 * @package App\Models
 * @property int id
 * @property int tenant_id
 * @property string title
 * @property string description
 * @property string recurrence
 * @property int user_id
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class ExpenseCategory extends Model
{
    use SoftDeletes;

    protected $table = 'expense_categories';
    protected $dates = ['deleted_at'];
    protected $guarded = [];

    public function expense_subcategories()
    {
        return $this->hasMany(ExpenseSubcategory::class, 'expense_category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
