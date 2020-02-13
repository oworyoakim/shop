<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class SaleItem
 * @package App/Models
 * @property int id
 * @property  int sale_id
 * @property  int item_id
 * @property  double sell_price
 * @property  double gross_amount
 * @property  double quantity
 * @property  double returns
 * @property  float discount_rate
 * @property  double discount_amount
 * @property  double net_amount
 * @property string status
 * @property  string comment
 * @property  Carbon created_at
 * @property  Carbon updated_at
 * @property  Carbon deleted_at
 */
class SaleItem extends Model
{
    use SoftDeletes;

    protected $table = 'sale_items';

    protected $guarded = [];

    const STATUS_PENDING = 'pending';
    const STATUS_CANCELED = 'canceled';
    const STATUS_COMPLETED = 'completed';
    const STATUS_PARTIAL = 'partial';
    const STATUS_RETURNED = 'returned';

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
