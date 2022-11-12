<?php

namespace App\Models\Tenant;

use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class Supplier
 * @package App\Models
 * @property int id
 * @property int tenant_id
 * @property string name
 * @property string email
 * @property string phone
 * @property string city
 * @property string country
 * @property string address
 * @property int user_id
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class Supplier extends Model
{
    use SoftDeletes;

    protected $table = 'suppliers';
    protected $dates = ['deleted_at'];

    protected $guarded = [];

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

    public function supplies(){
        return $this->belongsTo(Purchase::class);
    }

    public function returns(){
        return $this->hasMany(PurchaseReturn::class);
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
}
