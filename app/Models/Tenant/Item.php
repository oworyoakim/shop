<?php

namespace App\Models\Tenant;

use App\Models\Landlord\Unit;
use App\Models\Scopes\TenantScope;
use App\Traits\Tenant\Commentable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class Item
 * @package App\Models
 * @property int id
 * @property int tenant_id
 * @property int category_id
 * @property int unit_id
 * @property string title
 * @property string slug
 * @property string description
 * @property string barcode
 * @property string account
 * @property string avatar
 * @property float margin
 * @property int user_id
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class Item extends Model
{
    use SoftDeletes, Commentable;

    protected $table = 'items';
    protected $dates = ['deleted_at'];
    protected $guarded = [];

    const ACCOUNT_SALES_ONLY = 'sales';
    const ACCOUNT_PURCHASES_ONLY = 'purchases';
    const ACCOUNT_BOTH = 'both';

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

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sales_items()
    {
        return $this->hasMany(SaleItem::class);
    }
}
