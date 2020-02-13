<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class PurchaseOrder
 * @package App/Models
 * @property int id
 * @property string order_code
 * @property  int user_id
 * @property  int branch_id
 * @property  int supplier_id
 * @property  double gross_amount
 * @property  float vat_rate
 * @property  double vat_amount
 * @property  float discount_rate
 * @property  double discount_amount
 * @property  double net_amount
 * @property string status
 * @property  Carbon order_date
 * @property  Carbon created_at
 * @property  Carbon updated_at
 * @property  Carbon deleted_at
 */
class PurchaseOrder extends Model
{
    use SoftDeletes;

    protected $table = 'purchase_orders';
    protected $dates = ['order_date','deleted_at'];
    protected $guarded = [];

    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELED = 'canceled';


    public function orderlines()
    {
        return $this->hasMany(PurchaseOrderItem::class, 'purchase_order_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeCanceled(Builder $query)
    {
        return $query->where('status', self::STATUS_CANCELED);
    }

    public function scopePending(Builder $query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeCompleted(Builder $query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

}
