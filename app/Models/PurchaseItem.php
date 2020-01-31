<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseItem extends Model
{
    use SoftDeletes;

    protected $table = 'purchases';
    protected $guarded = [];



    public function item(){
        return $this->belongsTo(Item::class,'item_id');
    }

    public function purchase(){
        return $this->belongsTo(Purchase::class,'purchase_id');
    }

    public function returnsOutwardsAmount(){
        $amount = $this->returns * $this->cost_price;
        //$discount = round($amount * $this->discount_rate / 100,0);
        $discount = 0;
        return $amount - $discount;
    }

    public function grossAmountAfterReturns(){
        $amount = ($this->quantity - $this->returns) * $this->cost_price;
        return $amount;
    }

    public function netAmountAfterReturns(){
        $amount = ($this->quantity - $this->returns) * $this->cost_price;
        $discount = round($amount * $this->discount_rate / 100,0);
        return $amount - $discount;
    }
}
