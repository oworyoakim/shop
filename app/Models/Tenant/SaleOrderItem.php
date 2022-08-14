<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class SaleOrderItem
 * @package App\Models
 * @property int id
 * @property int tenant_id
 * @property int sale_order_id
 * @property int item_id
 * @property double price
 * @property double quantity
 * @property double gross_amount
 * @property double net_amount
 * @property double discount_rate
 * @property double discount_amount
 * @property double vat_rate
 * @property double vat_amount
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class SaleOrderItem extends Model
{

    protected $table = 'sale_order_items';
    protected $guarded = [];

    public function item(){
        return $this->belongsTo(Item::class);
    }

    public function order(){
        return $this->belongsTo(SaleOrder::class,'sale_order_id');
    }
}
