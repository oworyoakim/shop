<?php

namespace App\Models\Tenant;

use App\Models\Scopes\TenantScope;
use App\Traits\Tenant\Commentable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class Sale
 * @package App/Models
 * @property int id
 * @property int tenant_id
 * @property string transcode
 * @property  int user_id
 * @property  int branch_id
 * @property  int customer_id
 * @property  double gross_amount
 * @property  float vat_rate
 * @property  double vat_amount
 * @property  float discount_rate
 * @property  double discount_amount
 * @property  double net_amount
 * @property string status
 * @property  string payment_status
 * @property  string receipt
 * @property  Carbon transact_date
 * @property  Carbon created_at
 * @property  Carbon updated_at
 * @property  Carbon deleted_at
 */
class Sale extends Model
{
    use SoftDeletes, Commentable;
    protected $table = 'sales';
    protected $dates = ['deleted_at', 'sold_at'];
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

    public function sale_items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function returns()
    {
        return $this->hasOne(SaleReturn::class, 'sale_id');
    }

    public function receivable()
    {
        return $this->hasOne(SaleReceivable::class, 'sale_id');
    }

    public function paidAmount()
    {
        if ($this->receivable)
        {
            return $this->net_amount - $this->receivable->amount + $this->receivable->paid;
        }
        return $this->net_amount;
    }

    public function dueAmount()
    {
        return $this->net_amount - $this->paidAmount();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeCanceled(Builder $query)
    {
        return $query->where('status', self::STATUS_CANCELED);
    }

    public function scopeUncanceled(Builder $query)
    {
        return $query->whereNotIn('status', [self::STATUS_CANCELED]);
    }

    public function scopeSettled(Builder $query)
    {
        return $this->scopeUncanceled($query)->where('payment_status', self::PAYMENT_STATUS_SETTLED);
    }

    public function scopeReceivables(Builder $query)
    {
        return $this->scopeUncanceled($query)->where('payment_status', self::PAYMENT_STATUS_PARTIAL);
    }

    public function scopeReturned(Builder $query)
    {
        return $query->whereIn('status', [self::STATUS_PARTIALLY_RETURNED, self::STATUS_FULLY_RETURNED]);
    }

    public function daysReturnedSales(Builder $query, $date)
    {
        return $this->scopeReturned($query)->whereDate('transact_date', $date);
    }

    public function returnsInwards()
    {
        return $this->orderlines()
                    ->whereIn('status', [SaleItem::STATUS_PARTIAL, SaleItem::STATUS_RETURNED])
                    ->get()
                    ->reduce(function (SaleItem $orderline, $amount) {
                        return $amount + $orderline->returnsInwardsAmount();
                    }, 0);
    }


    public function grossReturns()
    {
        return $this->orderlines()
                    ->get()
                    ->reduce(function (SaleItem $item, $amount) {
                        return $amount + $item->netReturns();
                    }, 0);
    }

    public function taxOnReturns()
    {
        $amount = $this->grossReturns();
        $tax = floor(round($amount * $this->vat_rate / 100, 2) / 100) * 100;
        return $tax;
    }

    public function netReturns()
    {
        $amount = $this->grossReturns();
        $tax = $this->taxOnReturns();
        return $amount + $tax;
    }


    public function grossAmountAfterReturns()
    {
        return $this->orderlines()
                    ->get()
                    ->reduce(function (SaleItem $item, $amount) {
                        return $amount + $item->amountAfterReturns();
                    }, 0);
    }

    public function discountAmountAfterReturns()
    {
        $amount = $this->grossAmountAfterReturns();
        $discount = ceil(round($amount * $this->discount_rate / 100, 0) / 100) * 100;

        return $discount;
    }

    public function taxAmountAfterReturns()
    {
        $amount = $this->grossAmountAfterReturns();
        $discount = $this->discountAmountAfterReturns();
        $amount -= $discount;
        $tax = ceil(round($amount * $this->vat_rate / 100, 0) / 100) * 100;

        return $tax;
    }

    public function netAmountAfterReturns()
    {
        $amount = $this->grossAmountAfterReturns();
        $discount = $this->discountAmountAfterReturns();
        $amount -= $discount;
        $tax = $this->taxAmountAfterReturns();
        return $amount + $tax;
    }

    public function dueAmountAfterReturns()
    {
        $amount = $this->netAmountAfterReturns();
        //return $amount - $this->paid_amount;
        return $amount;
    }
}
