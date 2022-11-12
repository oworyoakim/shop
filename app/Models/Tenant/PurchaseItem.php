<?php

namespace App\Models\Tenant;

use App\Models\Scopes\TenantScope;
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
 * @property  double quantity
 * @property  float vat_rate
 * @property string status
 * @property  Carbon expiry_date
 * @property  Carbon created_at
 * @property  Carbon updated_at
 */
class PurchaseItem extends Model
{

    protected $table = 'purchase_items';
    protected $dates = ['expiry_date', 'deleted_at'];
    protected $guarded = [];

    const STATUS_COMPLETED = 'completed';
    const STATUS_PARTIAL = 'partial';
    const STATUS_RETURNED = 'returned';

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

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    public function returnsOutwardsAmount()
    {
        $amount = $this->returns * $this->cost_price;
        $tax = round($amount * $this->vat_rate / 100, 0);
        return $amount + $tax;
    }

    public function grossAmountAfterReturns()
    {
        return ($this->quantity - $this->returns) * $this->cost_price;
    }

    public function netAmountAfterReturns()
    {
        $amount = ($this->quantity - $this->returns) * $this->cost_price;
        $tax = round($amount * $this->vat_rate / 100, 0);
        return $amount + $tax;
    }
}
