<?php

namespace App\Models;

use App\Traits\Commentable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use SoftDeletes, Commentable;
    protected $table = 'purchases';
    protected $dates = ['deleted_at', 'transact_date'];

    protected $guarded = [];

    public function scopeCanceled(Builder $query)
    {
        return $query->where('status', 'canceled');
    }

    public function scopeUncanceled(Builder $query)
    {
        return $query->whereNotIn('status', ['canceled']);
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
        return $query->whereIn('status', ['partially_returned', 'fully_returned']);
    }

    public function scopePayables(Builder $query)
    {
        return $this->scopeUncanceled($query)->where('payment_status', 'partial');
    }

    public function returnsOutwards()
    {
        return $this->orderlines()
                    ->whereIn('status', ['partial', 'returned'])
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

}
