<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class Stock
 * @package App\Models
 * @property int id
 * @property int tenant_id
 * @property int branch_id
 * @property int item_id
 * @property int quantity
 * @property double cost_price
 * @property double sell_price
 * @property double discount
 * @property int purchase_id
 * @property int user_id
 * @property string status
 * @property Carbon expiry_date
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class Stock extends Model {

    use SoftDeletes;
    protected $table = 'stocks';
    protected $dates = ['deleted_at','expiry_date'];
    protected $guarded = [];

    const STATUS_ACTIVE = 'active';
    const STATUS_FINISHED = 'finished';
    const STATUS_EXPIRED = 'expired';



    public function scopeActive(Builder $query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeExpired(Builder $query)
    {
        return $query->where('status', self::STATUS_EXPIRED);
    }

    public function branch() {
        return $this->belongsTo(Branch::class);
    }

    public function item() {
        return $this->belongsTo(Item::class);
    }

    public function user() {
        return $this->belongsTo(User::class,'user_id');
    }

    public function purchase() {
        return $this->belongsTo(Purchase::class,'purchase_id');
    }

}
