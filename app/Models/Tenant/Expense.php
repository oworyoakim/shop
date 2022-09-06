<?php

namespace App\Models\Tenant;

use App\Models\Landlord\Tenant;
use App\Models\Scopes\TenantScope;
use App\Traits\Tenant\Commentable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class Expense
 * @package App\Models
 * @property int id
 * @property Carbon expense_date
 * @property string transcode
 * @property string comment
 * @property int expense_category_id
 * @property int expense_type_id
 * @property int tenant_id
 * @property int branch_id
 * @property int user_id
 * @property double amount
 * @property double vat_rate
 * @property double vat_amount
 * @property double approved_amount
 * @property string status
 * @property string receipt
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class Expense extends Model
{
    use SoftDeletes, Commentable;

    protected $table = 'expenses';
    protected $dates = ['deleted_at', 'expense_date'];
    protected $guarded = [];

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_DECLINED = 'declined';
    const STATUS_CANCELED = 'canceled';

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new TenantScope);
    }

    public function journal_entry() {
        return $this->morphOne(JournalEntry::class, 'transactable');
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function expense_category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function expense_subcategory()
    {
        return $this->belongsTo(ExpenseSubcategory::class, 'expense_subcategory_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUncanceled(Builder $query)
    {
        return $query->whereNotIn('status',[self::STATUS_CANCELED]);
    }

    public  function scopeCanceled(Builder $query)
    {
        return $query->where('status', self::STATUS_CANCELED);
    }

    public  function scopePending(Builder $query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public  function scopeApproved(Builder $query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public  function scopeDeclined(Builder $query)
    {
        return $query->where('status', self::STATUS_DECLINED);
    }

    public function scopePrepaid(Builder $query)
    {
        $date = Carbon::today()->toDateString();
        return $this->scopeApproved($query)->whereDate('transaction_date','>', $date);
    }

}
