<?php

namespace App\Models;

use App\Traits\Commentable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use SoftDeletes, Commentable;
    protected $table = 'sales';
    protected $dates = ['deleted_at', 'transact_date'];

    protected $guarded = [];

    public function orderlines()
    {
        return $this->hasMany(SaleItem::class, 'sale_id');
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
        return $this->hasOne(SalesReturn::class, 'sale_id');
    }

    public function receivable()
    {
        return $this->hasOne(SalesReceivable::class, 'sale_id');
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
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeCanceled(Builder $query)
    {
        return $query->where('status', 'canceled');
    }

    public function scopeUncanceled(Builder $query)
    {
        return $query->whereNotIn('status', ['canceled']);
    }

    public function scopeSettled(Builder $query)
    {
        return $this->scopeUncanceled($query)->where('payment_status', ['settled']);
    }

    public function scopeReceivables(Builder $query)
    {
        return $this->scopeUncanceled($query)->where('payment_status', 'partial');
    }

    public function scopeReturned(Builder $query)
    {
        return $query->whereIn('status', ['partially_returned', 'fully_returned']);
    }

    public function daysReturnedSales(Builder $query, $date)
    {
        return $this->scopeReturned($query)->whereDate('transact_date', $date);
    }

    public function returnsInwards()
    {
        return $this->orderlines()
                    ->whereIn('status', ['partial', 'returned'])
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
