<?php

namespace App\Models;

use App\Traits\Commentable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class PurchasesPayable
 * @package App\Models
 * @property int id
 * @property int purchase_id
 * @property int user_id
 * @property int supplier_id
 * @property int branch_id
 * @property double amount
 * @property double paid
 * @property string status
 * @property Carbon transact_date
 * @property Carbon due_date
 * @property Carbon settled_at
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class PurchasesPayable extends Model
{
    use SoftDeletes, Commentable;

    protected $table = 'purchases_payables';

    protected $dates = ['deleted_at', 'due_date', 'settled_at'];

    protected $guarded = [];

    const STATUS_PENDING = 'pending';
    const STATUS_PARTIAL = 'partial';
    const STATUS_SETTLED = 'settled';

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeUnsettled(Builder $query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_PARTIAL]);
    }

    public function scopeSettled(Builder $query)
    {
        return $query->where('status', self::STATUS_SETTLED);
    }

    public function balance()
    {
        return $this->amount - $this->paid;
    }


}
