<?php

namespace App\Models\Tenant;

use App\Traits\Tenant\Commentable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class PurchaseReturn
 * @package App\Models
 * @property int id
 * @property int tenant_id
 * @property int purchase_id
 * @property int item_id
 * @property int user_id
 * @property int supplier_id
 * @property double quantity
 * @property double price
 * @property double gross_amount
 * @property double vat_amount
 * @property double net_amount
 * @property double paid_amount
 * @property double due_amount
 * @property string status
 * @property Carbon returned_at
 * @property Carbon due_date
 * @property Carbon settled_at
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class PurchaseReturn extends Model
{
    use SoftDeletes, Commentable;

    protected $table = 'purchase_returns';

    protected $dates = ['due_date', 'returned_at', 'settled_at', 'deleted_at'];

    protected $guarded = [];

    const STATUS_PENDING = 'pending';
    const STATUS_PARTIAL = 'partial';
    const STATUS_SETTLED = 'settled';


    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }


    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return float
     */
    public function balance()
    {
        return $this->net_amount - $this->paid_amount;
    }

    public function scopeUnsettled(Builder $query){
        return $query->whereIn('status',[self::STATUS_PENDING,self::STATUS_PARTIAL]);
    }

    public function scopeSettled(Builder $query){
        return $query->where('status',self::STATUS_SETTLED);
    }

}
