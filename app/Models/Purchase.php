<?php

namespace App\Models;

use App\Traits\Commentable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class Purchase
 * @package App/Models
 * @property int id
 * @property string transcode
 * @property  int user_id
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
 * @property  Carbon transact_date
 * @property  Carbon created_at
 * @property  Carbon updated_at
 * @property  Carbon deleted_at
 */
class Purchase extends Model
{
    use SoftDeletes, Commentable;
    protected $table = 'purchases';
    protected $dates = ['deleted_at', 'transact_date'];

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
        return $this->hasOne(PurchasesReturn::class, 'purchase_id');
    }

    public function payable()
    {
        return $this->hasOne(PurchasesPayable::class, 'purchase_id');
    }

    public function paidAmount()
    {
        if ($this->payable)
        {
            return $this->net_amount - $this->payable->amount + $this->payable->paid;
        }
        return $this->net_amount;
    }

    public function dueAmount()
    {
        return $this->net_amount - $this->paidAmount();
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

    public function discountAmountAfterReturns()
    {
        $amount = $this->grossAmountAfterReturns();
        $discount = round($amount * $this->discount_rate / 100, 0);

        return $discount;
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
        $discount = $this->discountAmountAfterReturns();
        $amount -= $discount;
        $tax = round($amount * $this->vat_rate / 100, 0);
        return $amount - $tax;
    }

    public function dueAmountAfterReturns()
    {
        $amount = $this->netAmountAfterReturns();
        return $amount - $this->paid_amount;
    }

    public static function generateTransactionCode(User $user)
    {
        do
        {
            $time = str_shuffle((string)Carbon::now()->getTimestamp());
            $code = "{$user->getUserId()}{$time}";
            // do this until we get a string that does not start or end with 0
            do
            {
                $code = str_shuffle(strrev($code));
                $firstChar = substr($code, 0, 1);
            } while ($firstChar == '0');
            $notValidCode = self::exists($code);
        } while ($notValidCode);
        return $code;
    }

    /**
     * @param string $code
     * @return bool
     */
    public static function exists($code)
    {
        $purchasesCount = Purchase::query()
                                  ->where('transcode', $code)
                                  ->count();
        return !!$purchasesCount;
    }

}
