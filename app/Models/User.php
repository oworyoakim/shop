<?php
/**
 * Created by PhpStorm.
 * User: Yoakim
 * Date: 9/23/2018
 * Time: 12:54 AM
 */

namespace App\Models;

use Cartalyst\Sentinel\Users\EloquentUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class User
 * @package App\Models
 * @property int id
 * @property string first_name
 * @property string last_name
 * @property string gender
 * @property string username
 * @property string email
 * @property string phone
 * @property string city
 * @property string country
 * @property string address
 * @property string avatar
 * @property bool active
 * @property int branch_id
 * @property double current_balance
 * @property string password
 * @property string permissions
 * @property Carbon dob
 * @property Carbon last_login
 * @property Carbon password_last_changed
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class User extends EloquentUser
{
    use SoftDeletes;

    protected $table = 'users';
    protected $dates = ['last_login','password_last_changed'];

    protected $guarded = [];

    protected $loginNames = ['email','username'];

    const GENDER_MALE = 'male';
    const GENDER_FEMALE = 'female';

    const STATUS_ACTIVE = true;
    const STATUS_INACTIVE = false;

    public static function byEmail($email)
    {
        return static::whereEmail($email)->first();
    }

    public static function byUsername($username)
    {
        return static::whereUsername($username)->first();
    }


    public function fullName()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function fullAddress()
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
        return $this->uncanceledSales()->whereDate('transact_date', $date);
    }

    public function daysReceivables($date)
    {
        return $this->receivables()->whereDate('transact_date', $date);
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
                    ->whereYear('transact_date', $year)
                    ->whereMonth('transact_date', $month);
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
                    ->whereYear('transact_date', $year)
                    ->whereMonth('transact_date', $month);
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
        return $this->uncanceledPurchases()->whereDate('transact_date', $date);
    }


    public function daysPayables($date)
    {
        return $this->payables()->unsettled()->whereDate('transact_date', $date);
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
                    ->whereYear('transact_date', $year)
                    ->whereMonth('transact_date', $month);
    }

    public function monthsPayables($date)
    {
        $year = Carbon::parse($date)->year;
        $month = Carbon::parse($date)->month;
        return $this->payables()
                    ->whereYear('transact_date', $year)
                    ->whereMonth('transact_date', $month);
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
     * @return Branch|null
     */
    public function getActiveBranch()
    {
        return $this->branch ?: null;
    }

    /**
     * @return Role|null
     */
    public function getActiveRole()
    {
        return $this->roles()
                    ->withPivot(['active'])
                    ->wherePivot('active', true)
                    ->first();
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return !!$this->active;
    }

    /**
     * @return mixed
     */
    public function getBalance()
    {
        return $this->current_balance;
    }
}
