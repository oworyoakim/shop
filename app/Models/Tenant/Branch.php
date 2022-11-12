<?php

namespace App\Models\Tenant;

use App\Models\Landlord\Tenant;
use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;


/**
 * Class Branch
 * @package App\Models
 * @property int id
 * @property int general_ledger_account_id
 * @property int tenant_id
 * @property string name
 * @property string email
 * @property string phone
 * @property string city
 * @property string country
 * @property string address
 * @property float balance
 * @property bool active
 * @property int user_id
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class Branch extends Model
{

    use SoftDeletes;

    protected $table = "branches";
    protected $dates = ['deleted_at'];

    const STATUS_ACTIVE = true;
    const STATUS_INACTIVE = false;

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

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function general_ledger_account()
    {
        return $this->belongsTo(GeneralLedgerAccount::class, 'general_ledger_account_id');
    }

    public function suppliers()
    {
        return $this->hasMany(Supplier::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'branch_id');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'branch_id');
    }

    public function receivables()
    {
        return $this->hasMany(SalePayment::class, 'branch_id');
    }

    public function purchase_payments()
    {
        return $this->hasMany(PurchasePayment::class, 'branch_id');
    }

    public function employees()
    {
        return $this->hasMany(User::class, 'branch_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return User|null
     */
    public function manager()
    {
        return $this->employees()->where('group', User::TYPE_MANAGERS)->first();
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return !!$this->active;
    }

}
