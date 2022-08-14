<?php

namespace App\Models\Tenant;

use App\Traits\Commentable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class SaleReceivable
 * @package App\Models
 * @property int id
 * @property int tenant_id
 * @property int sale_id
 * @property int user_id
 * @property int customer_id
 * @property int branch_id
 * @property double amount
 * @property double paid
 * @property string status
 * @property Carbon due_date
 * @property Carbon paid_at
 * @property Carbon settled_at
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class SaleReceivable extends Model
{
    use SoftDeletes, Commentable;

    protected $table = 'sale_receivables';
    protected $dates = ['deleted_at', 'due_date', 'paid_at', 'settled_at'];

    protected $guarded = [];

    const STATUS_PENDING = 'pending';
    const STATUS_PARTIAL = 'partial';
    const STATUS_SETTLED = 'settled';

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeSettled(Builder $query)
    {
        return $query->where('status', self::STATUS_SETTLED);
    }

    public function scopeUnsettled(Builder $query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_PARTIAL]);
    }

    public function balance()
    {
        return $this->amount - $this->paid;
    }


}
