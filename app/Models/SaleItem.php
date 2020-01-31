<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleItem extends Model
{
    use SoftDeletes;

    protected $table = 'sale_items';

    protected $guarded = [];

    public function item()
    {
        return $this->belongsTo(Item::class,'item_id');
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }

    public function returnsAmount()
    {
        return $this->returns * $this->sell_price;
    }

    public function returnsInwardsAmount()
    {
        $amount = $this->returnsAmount();
        //$discount = round($amount * $this->discount_rate / 100,0);
        $discount = 0;
        return $amount - $discount;
    }

    public function discountOnReturns()
    {
        $amount = $this->returnsAmount();
        $discount = ceil(round($amount * $this->discount_rate / 100, 2) / 100) * 100;
        return $discount;
    }

    public function netReturns()
    {
        $amount = $this->returnsAmount();
        $discount = ceil(round($amount * $this->discount_rate / 100, 2) / 100) * 100;
        return $amount - $discount;
    }

    public function amountAfterReturns()
    {
        $amount = $this->gross_amount - $this->returnsAmount();
        return $amount;
    }

    public function netAmountAfterReturns()
    {
        $amount = $this->amountAfterReturns();
        $discount = $this->discountOnReturns();
        return $amount - $discount;
    }
}
