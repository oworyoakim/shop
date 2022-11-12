<?php

namespace App\Models\Tenant;

use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class SaleItem
 * @package App/Models
 * @property int id
 * @property int tenant_id
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
    protected $table = 'sale_items';

    protected $guarded = [];

    const STATUS_PENDING = 'pending';
    const STATUS_CANCELED = 'canceled';
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
        $discount = ceil(round($amount * $this->discount_rate / 100,0) / 100) * 100;
        return $amount - $discount;
    }

    public function discountOnReturns()
    {
        $amount = $this->returnsAmount();
        return ceil(round($amount * $this->discount_rate / 100, 2) / 100) * 100;
    }

    public function netReturns()
    {
        $amount = $this->returnsAmount();
        $discount = ceil(round($amount * $this->discount_rate / 100, 2) / 100) * 100;
        return $amount - $discount;
    }

    public function amountAfterReturns()
    {
        return $this->gross_amount - $this->returnsAmount();
    }

    public function netAmountAfterReturns()
    {
        $amount = $this->amountAfterReturns();
        $discount = $this->discountOnReturns();
        return $amount - $discount;
    }
}
