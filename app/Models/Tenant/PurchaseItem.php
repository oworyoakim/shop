<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class PurchaseItem
 * @package App/Models
 * @property int id
 * @property int tenant_id
 * @property  int purchase_id
 * @property  int item_id
 * @property  double cost_price
 * @property  double gross_amount
 * @property  double quantity
 * @property  double returns
 * @property  float discount_rate
 * @property  double discount_amount
 * @property  double net_amount
 * @property string status
 * @property  Carbon expiry_date
 * @property  Carbon created_at
 * @property  Carbon updated_at
 * @property  Carbon deleted_at
 */
class PurchaseItem extends Model
{
    use SoftDeletes;

    protected $table = 'purchase_items';
    protected $dates = ['expiry_date','deleted_at'];
    protected $guarded = [];

    const STATUS_PENDING = 'pending';
    const STATUS_CANCELED = 'canceled';
    const STATUS_COMPLETED = 'completed';
    const STATUS_PARTIAL = 'partial';
    const STATUS_RETURNED = 'returned';



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
