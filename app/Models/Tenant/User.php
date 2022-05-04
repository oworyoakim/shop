<?php
/**
 * Created by PhpStorm.
 * User: Yoakim
 * Date: 9/23/2018
 * Time: 12:54 AM
 */

namespace App\Models\Tenant;

use App\Traits\PermissionsTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * Class User
 * @package App\Models
 * @property int id
 * @property string first_name
 * @property string last_name
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
    use Notifiable;
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


    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getFullAddressAttribute()
    {
        return $this->address . ', ' . $this->city . ' ' . $this->country;
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'user_id');
    }

    public function receivables()
    {
        return $this->hasMany(SalesReceivable::class, 'user_id');
    }


    public function expenses()
    {
        return $this->hasMany(Expense::class, 'user_id');
    }

    public function purchases()
    {
        return $this->hasMany(PurchaseItem::class, 'user_id');
    }

    public function payables()
    {
        return $this->hasMany(PurchasesPayable::class, 'user_id');
    }

    public function uncanceledSales()
    {
        return $this->sales()->uncanceled();
    }


    public function uncanceledPurchases()
    {
        return $this->purchases()->uncanceled();
    }

    public function uncanceledExpenses()
    {
        return $this->expenses()->uncanceled();
    }


    public function daysSales($date)
    {
        return $this->uncanceledSales()->whereDate('transaction_date', $date);
    }

    public function daysReceivables($date)
    {
        return $this->receivables()->whereDate('transaction_date', $date);
    }


    public function daysTotalReceivables($date)
    {
        return $this->daysReceivables($date)
                    ->get()
                    ->reduce(function (SalesReceivable $receivable, $amount) {
                        return $amount + $receivable->balance();
                    }, 0);
    }

    public function monthsReceivables($date)
    {
        $year = Carbon::parse($date)->year;
        $month = Carbon::parse($date)->month;
        return $this->receivables()
                    ->whereYear('transaction_date', $year)
                    ->whereMonth('transaction_date', $month);
    }


    public function monthsTotalReceivables($date)
    {
        return $this->monthsTotalReceivables($date)
                    ->get()
                    ->reduce(function (SalesReceivable $receivable, $amount) {
                        return $amount + $receivable->balance();
                    }, 0);
    }


    public function monthsSales($date)
    {
        $year = Carbon::parse($date)->year;
        $month = Carbon::parse($date)->month;
        return $this->uncanceledSales()
                    ->whereYear('transaction_date', $year)
                    ->whereMonth('transaction_date', $month);
    }


    public function overallTotalReceivables()
    {
        return $this->receivables()
                    ->get()
                    ->reduce(function (SalesReceivable $receivable, $amount) {
                        return $amount + $receivable->balance();
                    }, 0);
    }

    public function daysPurchases($date)
    {
        return $this->uncanceledPurchases()->whereDate('transaction_date', $date);
    }


    public function daysPayables($date)
    {
        return $this->payables()->unsettled()->whereDate('transaction_date', $date);
    }

    public function daysTotalPayables($date)
    {
        return $this->daysPayables($date)
                    ->get()
                    ->reduce(function (PurchasesPayable $payable, $amount) {
                        return $amount + $payable->balance();
                    }, 0);
    }


    public function monthsPurchases($date)
    {
        $year = Carbon::parse($date)->year;
        $month = Carbon::parse($date)->month;
        return $this->uncanceledPurchases()
                    ->whereYear('transaction_date', $year)
                    ->whereMonth('transaction_date', $month);
    }

    public function monthsPayables($date)
    {
        $year = Carbon::parse($date)->year;
        $month = Carbon::parse($date)->month;
        return $this->payables()
                    ->whereYear('transaction_date', $year)
                    ->whereMonth('transaction_date', $month);
    }

    public function monthsTotalPayables($date)
    {
        return $this->monthsPayables($date)
                    ->get()
                    ->reduce(function (PurchasesPayable $payable, $amount) {
                        return $amount + $payable->balance();
                    }, 0);
    }

    public function overallTotalPayables()
    {
        return $this->payables()
                    ->get()
                    ->reduce(function (PurchasesPayable $payable, $amount) {
                        return $amount + $payable->balance();
                    }, 0);
    }

    public function daysExpenses($date)
    {
        return $this->uncanceledExpenses()->whereDate('expense_date', $date);
    }


    public function monthsExpenses($date)
    {
        $year = Carbon::parse($date)->year;
        $month = Carbon::parse($date)->month;
        return $this->uncanceledExpenses()
                    ->whereYear('expense_date', $year)
                    ->whereMonth('expense_date', $month);
    }

    public function daysReturnsInwards($date)
    {
        return $this->daysSales($date)
                    ->whereIn('payment_status', [Sale::STATUS_PARTIALLY_RETURNED, Sale::STATUS_FULLY_RETURNED]);
    }

    public function daysTotalReturnsInwards($date)
    {
        return $this->daysReturnsInwards($date)
                    ->get()
                    ->reduce(function (Sale $sale, $amount) {
                        return $amount + $sale->returnsInwards();
                    }, 0);
    }

    public function monthsReturnsInwards($date)
    {
        return $this->monthsSales($date)
                    ->whereIn('payment_status', [Sale::STATUS_PARTIALLY_RETURNED, Sale::STATUS_FULLY_RETURNED]);
    }

    public function monthsTotalReturnsInwards($date)
    {
        return $this->monthsReturnsInwards($date)
                    ->get()
                    ->reduce(function (Sale $sale, $amount) {
                        return $amount + $sale->returnsInwards();
                    }, 0);
    }

    public function overallReturnsInwards()
    {
        return $this->uncanceledSales()
                    ->whereIn('payment_status', [Sale::STATUS_PARTIALLY_RETURNED, Sale::STATUS_FULLY_RETURNED]);
    }

    public function overallTotalReturnsInwards()
    {
        return $this->overallReturnsInwards()
                    ->get()
                    ->reduce(function (Sale $sale, $amount) {
                        return $amount + $sale->returnsInwards();
                    }, 0);
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
}
