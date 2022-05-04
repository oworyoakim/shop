<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;


/**
 * Class Branch
 * @package App\Models
 * @property int id
 * @property int tenant_id
 * @property string name
 * @property string email
 * @property string phone
 * @property string city
 * @property string country
 * @property string address
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
        return $this->hasMany(SalesReceivable::class, 'branch_id');
    }

    public function payables()
    {
        return $this->hasMany(PurchasesPayable::class, 'branch_id');
    }

    public function employees()
    {
        return $this->hasMany(User::class, 'branch_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    /**
     * @return User|null
     */
    public function manager()
    {
        foreach ($this->employees as $employee)
        {
            if ($employee->group === User::TYPE_MANAGERS)
            {
                return $employee;
            }
        }
        return null;
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
        return $this->employees()->get()->reduce(function ($balance, User $employee) {
            return $balance + $employee->getBalance();
        }, 0);
    }


    /**************     SALES HELPERS                 ******/


    public function overallSales()
    {
        return $this->sales()->uncanceled();
    }

    public function overallTotalSales()
    {
        return $this->overallSales()->sum('net_amount');
    }


    public function daysSales($date)
    {
        return $this->overallSales()->whereDate('transaction_date', $date);
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


    public function daysTotalSales($date)
    {
        return $this->daysSales($date)->sum('net_amount');
    }

    public function monthsSales($date)
    {
        $year = Carbon::parse($date)->year;
        $month = Carbon::parse($date)->month;
        return $this->overallSales()
                    ->whereYear('transaction_date', $year)
                    ->whereMonth('transaction_date', $month);
    }


    public function monthsTotalSales($date)
    {
        return $this->monthsSales($date)->sum('net_amount');
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
        return $this->monthsReceivables($date)
                    ->get()
                    ->reduce(function (SalesReceivable $receivable, $amount) {
                        return $amount + $receivable->balance();
                    }, 0);
    }

    public function daysTotalSalesTax($date)
    {
        return $this->daysSales($date)->sum('vat_amount');
    }

    public function monthsTotalSalesTax($date)
    {
        return $this->monthsSales($date)->sum('vat_amount');
    }

    public function daysTotalSalesDiscount($date)
    {
        return $this->daysSales($date)->sum('discount_amount');
    }

    public function monthsTotalSalesDiscount($date)
    {
        return $this->monthsSales($date)->sum('discount_amount');
    }

    public function canceledSales()
    {
        return $this->sales()->canceled();
    }

    public function daysSalesCancelled($date)
    {
        return $this->canceledSales()->whereDate('transaction_date', $date);
    }

    public function daysTotalSalesCancelled($date)
    {
        return $this->daysSalesCancelled($date)->sum('net_amount');
    }

    public function monthsSalesCancelled($date)
    {
        $year = Carbon::parse($date)->year;
        $month = Carbon::parse($date)->month;
        return $this->canceledSales()
                    ->whereYear('transaction_date', $year)
                    ->whereMonth('transaction_date', $month);
    }

    public function monthsTotalSalesCancelled($date)
    {
        return $this->monthsSalesCancelled($date)->sum('net_amount');
    }

    public function daysSalesReturned($date)
    {
        return $this->sales()
                    ->returned()
                    ->whereDate('transaction_date', $date);
    }

    public function daysTotalSalesReturned($date)
    {
        return $this->daysSalesReturned($date)
                    ->get()
                    ->reduce(function (Sale $sale, $amount) {
                        return $amount + $sale->returnsInwards();
                    }, 0);
    }

    public function monthsSalesReturned($date)
    {
        $year = Carbon::parse($date)->year;
        $month = Carbon::parse($date)->month;
        return $this->sales()
                    ->returned()
                    ->whereYear('transaction_date', $year)
                    ->whereMonth('transaction_date', $month);
    }

    public function monthsTotalSalesReturned($date)
    {
        return $this->monthsSalesReturned($date)
                    ->get()
                    ->reduce(function (Sale $sale, $amount) {
                        return $amount + $sale->returnsInwards();
                    }, 0);
    }

    public function latestSales()
    {
        return $this->overallSales()
                    ->latest()
                    ->take(5);
    }

    /**************   END SALES    HELPERS     ***********/


    /**************   PURCHASES HELPERS         ***********/

    public function overallPurchases()
    {
        return $this->purchases()->uncanceled();
    }

    public function overallTotalPurchases()
    {
        return $this->overallPurchases()->sum('net_amount');
    }


    public function daysPurchases($date)
    {
        return $this->overallPurchases()
                    ->whereDate('transaction_date', $date);
    }

    public function daysTotalPurchases($date)
    {
        return $this->daysPurchases($date)->sum('net_amount');
    }

    public function monthsPurchases($date)
    {
        $year = Carbon::parse($date)->year;
        $month = Carbon::parse($date)->month;
        return $this->overallPurchases()
                    ->whereYear('transaction_date', $year)
                    ->whereMonth('transaction_date', $month);
    }

    public function monthsTotalPurchases($date)
    {
        return $this->monthsPurchases($date)->sum('net_amount');
    }

    public function daysPayables($date)
    {
        return $this->payables()->whereDate('transaction_date', $date);
    }

    public function daysTotalPayables($date)
    {
        return $this->daysPayables($date)
                    ->get()
                    ->reduce(function (PurchasesPayable $payable, $amount) {
                        return $amount + $payable->balance();
                    }, 0);
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

    public function daysTotalPurchaseTax($date)
    {
        return $this->daysPurchases($date)->sum('vat_amount');
    }

    public function monthsTotalPurchasesTax($date)
    {
        return $this->monthsPurchases($date)->sum('vat_amount');
    }

    public function daysTotalPurchaseDiscount($date)
    {
        return $this->daysPurchases($date)->sum('discount_amount');
    }

    public function monthsTotalPurchasesDiscount($date)
    {
        return $this->monthsPurchases($date)->sum('discount_amount');
    }

    public function daysPurchasesCancelled($date)
    {
        return $this->purchases()
                    ->canceled()
                    ->whereDate('transaction_date', $date);
    }

    public function daysTotalPurchasesCancelled($date)
    {
        return $this->daysPurchasesCancelled($date)->sum('net_amount');
    }


    public function monthsPurchasesCancelled($date)
    {
        $year = Carbon::parse($date)->year;
        $month = Carbon::parse($date)->month;
        return $this->purchases()
                    ->canceled()
                    ->whereYear('transaction_date', '=', $year)
                    ->whereMonth('transaction_date', '=', $month);
    }

    public function monthsTotalPurchasesCancelled($date)
    {
        return $this->monthsPurchasesCancelled($date)->sum('net_amount');
    }

    public function daysPurchasesReturned($date)
    {
        return $this->purchases()
                    ->returned()
                    ->whereDate('transaction_date', $date);
    }

    public function daysTotalPurchasesReturned($date)
    {
        return $this->daysPurchasesReturned($date)
                    ->get()
                    ->reduce(function (Purchase $purchase, $amount) {
                        return $amount + $purchase->returnsOutwards();
                    }, 0);
    }

    public function monthsPurchasesReturned($date)
    {
        $year = Carbon::parse($date)->year;
        $month = Carbon::parse($date)->month;
        return $this->purchases()
                    ->returned()
                    ->whereYear('transaction_date', $year)
                    ->whereMonth('transaction_date', $month);
    }

    public function monthsTotalPurchasesReturned($date)
    {
        return $this->monthsPurchasesReturned($date)
                    ->get()
                    ->reduce(function (Purchase $purchase, $amount) {
                        return $amount + $purchase->returnsOutwards();
                    }, 0);
    }

    public function latestPurchases()
    {
        return $this->purchases()
                    ->uncanceled()
                    ->latest()
                    ->take(5);
    }

    /**************   END PURCHASES    HELPERS     ***********/


    /**************   EXPENSES HELPERS         ***********/

    public function overallExpenses()
    {
        return $this->expenses()->uncanceled();
    }

    public function overallTotalExpenses()
    {
        return $this->overallExpenses()->sum('approved_amount');
    }

    public function daysExpenses($date)
    {
        return $this->overallExpenses()
                    ->whereDate('expense_date', $date);
    }

    public function daysTotalExpenses($date)
    {
        return $this->daysExpenses($date)->sum('approved_amount');
    }

    public function monthsExpenses($date)
    {
        $year = Carbon::parse($date)->year;
        $month = Carbon::parse($date)->month;
        return $this->overallExpenses()
                    ->whereYear('expense_date', $year)
                    ->whereMonth('expense_date', $month);
    }

    public function monthsTotalExpenses($date)
    {
        return $this->monthsExpenses($date)->sum('approved_amount');
    }

    public function daysTotalExpensesTax($date)
    {
        return $this->daysExpenses($date)->sum('vat_amount');
    }

    public function monthsTotalExpensesTax($date)
    {
        return $this->monthsExpenses($date)->sum('vat_amount');
    }

    public function latestExpenses()
    {
        return $this->overallExpenses()
                    ->latest()
                    ->take(5);
    }

    /**************    END EXPENSES HELPERS         ***********/


}
