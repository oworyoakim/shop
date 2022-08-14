<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class PurchaseOrderItem
 * @package App\Models
 * @property int id
 * @property int tenant_id
 * @property int purchase_order_id
 * @property int item_id
 * @property double price
 * @property double quantity
 * @property double gross_amount
 * @property double net_amount
 * @property double discount_rate
 * @property double discount_amount
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class PurchaseOrderItem extends Model
{
    protected $table = 'purchase_order_items';
    protected $guarded = [];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function order()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }
}
