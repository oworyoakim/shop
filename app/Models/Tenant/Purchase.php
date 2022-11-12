<?php

namespace App\Models\Tenant;

use App\Models\Scopes\TenantScope;
use App\Traits\Tenant\Commentable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class Purchase
 * @package App/Models
 * @property int id
 * @property string barcode
 * @property  int user_id
 * @property  int tenant_id
 * @property  int branch_id
 * @property  int purchase_order_id
 * @property  int supplier_id
 * @property  double gross_amount
 * @property  float vat_rate
 * @property  double vat_amount
 * @property  float discount_rate
 * @property  double discount_amount
 * @property  double net_amount
 * @property string status
 * @property  string payment_status
 * @property  string receipt
 * @property  Carbon transaction_date
 * @property  Carbon created_at
 * @property  Carbon updated_at
 * @property  Carbon deleted_at
 */
class Purchase extends Model
{
    use SoftDeletes, Commentable;
    protected $table = 'purchases';
    protected $dates = ['deleted_at', 'transaction_date'];

    protected $guarded = [];

    const STATUS_PENDING = 'pending';
    const STATUS_CANCELED = 'canceled';
    const STATUS_COMPLETED = 'completed';
    const STATUS_PARTIALLY_RETURNED = 'partially_returned';
    const STATUS_FULLY_RETURNED = 'fully_returned';

    const PAYMENT_STATUS_PENDING = 'pending';
    const PAYMENT_STATUS_PARTIAL = 'partial';
    const PAYMENT_STATUS_SETTLED = 'settled';
    const PAYMENT_STATUS_CANCELED = 'canceled';

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

    public function scopeCanceled(Builder $query)
    {
        return $query->where('status', self::STATUS_CANCELED);
    }

    public function scopeUncanceled(Builder $query)
    {
        return $query->whereNotIn('status', [self::STATUS_CANCELED]);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function orderlines()
    {
        return $this->hasMany(PurchaseItem::class, 'purchase_id');
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class, 'purchase_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function returns()
    {
        return $this->hasOne(PurchaseReturn::class, 'purchase_id');
    }

    public function payments()
    {
        return $this->hasMany(PurchasePayment::class, 'purchase_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeReturned(Builder $query)
    {
        return $query->whereIn('status', [self::STATUS_PARTIALLY_RETURNED, self::STATUS_FULLY_RETURNED]);
    }

    public function scopePayables(Builder $query)
    {
        return $this->scopeUncanceled($query)->where('payment_status', self::PAYMENT_STATUS_PARTIAL);
    }

    public function returnsOutwards()
    {
        return $this->orderlines()
                    ->whereIn('status', [PurchaseItem::STATUS_PARTIAL, PurchaseItem::STATUS_RETURNED])
                    ->get()
                    ->reduce(function (PurchaseItem $item, $amount) {
                        return $amount + $item->returnsOutwardsAmount();
                    }, 0);
    }

    public function grossAmountAfterReturns()
    {
        return $this->orderlines()
                    ->get()
                    ->reduce(function (PurchaseItem $item, $amount) {
                        return $amount + $item->grossAmountAfterReturns();
                    }, 0);
    }


    public function taxAmountAfterReturns()
    {
        $amount = $this->grossAmountAfterReturns();
        $discount = $this->discountAmountAfterReturns();
        $amount -= $discount;
        $tax = round($amount * $this->vat_rate / 100, 0);

        return $tax;
    }

    public function netAmountAfterReturns()
    {
        $amount = $this->grossAmountAfterReturns();
        $tax = round($amount * $this->vat_rate / 100, 0);
        return $amount - $tax;
    }

    public function dueAmountAfterReturns()
    {
        $amount = $this->netAmountAfterReturns();
        return $amount - $this->paid_amount;
    }

}
