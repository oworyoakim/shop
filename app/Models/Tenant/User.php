<?php
/**
 * Created by PhpStorm.
 * User: Yoakim
 * Date: 9/23/2018
 * Time: 12:54 AM
 */

namespace App\Models\Tenant;

use App\Models\Landlord\Tenant;
use App\Models\Scopes\TenantScope;
use App\Traits\PermissionsTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Carbon;

/**
 * Class User
 * @package App\Models
 * @property int id
 * @property string name
 * @property string group
 * @property string gender
 * @property string username
 * @property string email
 * @property string phone
 * @property string city
 * @property string country
 * @property string address
 * @property string avatar
 * @property bool active
 * @property int general_ledger_account_id
 * @property int tenant_id
 * @property int branch_id
 * @property double current_balance
 * @property string password
 * @property string permissions
 * @property Carbon dob
 * @property Carbon last_login
 * @property Carbon password_last_changed
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class User extends Authenticatable
{
    use PermissionsTrait;

    protected $table = 'users';
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login' => 'datetime',
        'password_last_changed' => 'datetime',
        'permissions' => 'array',
    ];

    const GENDER_MALE = 'male';
    const GENDER_FEMALE = 'female';
    const GENDERS = [
        self::GENDER_MALE,
        self::GENDER_FEMALE,
    ];

    const STATUS_ACTIVE = true;
    const STATUS_INACTIVE = false;

    const TYPE_CASHIERS = "cashiers";
    const TYPE_MANAGERS = "managers";
    const TYPE_ADMINISTRATORS = "administrators";
    const TYPE_ACCOUNTANTS = "accountants";
    const TYPE_SUPERVISORS = "supervisors";

    const TYPES = [
        self::TYPE_CASHIERS,
        self::TYPE_MANAGERS,
        self::TYPE_ADMINISTRATORS,
        self::TYPE_ACCOUNTANTS,
        self::TYPE_SUPERVISORS,
    ];

    public function getUserId() {
        return $this->id;
    }

    public function getFullAddressAttribute()
    {
        return $this->address . ', ' . $this->city . ' ' . $this->country;
    }

    public function general_ledger_account()
    {
        return $this->belongsTo(GeneralLedgerAccount::class, 'general_ledger_account_id');
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'user_id');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'user_id');
    }

    public function purchases()
    {
        return $this->hasMany(PurchaseItem::class, 'user_id');
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return !!$this->active;
    }

    /**
     * @return bool
     */
    public function isBlocked()
    {
        return !$this->active;
    }

    /**
     * @return bool
     */
    public function isManager()
    {
        return $this->group === self::TYPE_MANAGERS;
    }

    /**
     * @return bool
     */
    public function isCashier()
    {
        return $this->group === self::TYPE_CASHIERS;
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return $this->group === self::TYPE_ADMINISTRATORS;
    }

    /**
     * @return bool
     */
    public function isSupervisor()
    {
        return $this->group === self::TYPE_SUPERVISORS;
    }

    /**
     * @return bool
     */
    public function isAccountant()
    {
        return $this->group === self::TYPE_ACCOUNTANTS;
    }

    /**
     * @return bool
     */
    public function isShopUser()
    {
        return $this->isManager() || $this->isCashier();
    }

    /**
     * @return mixed
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @return mixed
     */
    public function computedBalance()
    {
        return $this->general_ledger_account->computedBalance();
    }
}
