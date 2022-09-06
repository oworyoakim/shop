<?php

namespace App;

use App\Models\Landlord\Tenant;
use App\Models\Tenant\Expense;
use App\Models\Tenant\Item;
use App\Models\Tenant\ItemStock;
use App\Models\Tenant\Sale;
use App\Models\Tenant\Stock;
use App\Models\Tenant\Setting;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShopHelper
{
    /**
     * Creates a tenant and seeds tenant specific data
     *
     * @param array $data
     *
     * @return Tenant
     * @throws \Exception
     */
    public static function createTenant(array $data)
    {
        return DB::transaction(function () use ($data) {
            $attributes = Arr::except($data, ['username', 'password']);
            Log::info("Creating Tenant", $attributes);
            // create the tenant
            $tenant = Tenant::query()->create($attributes);
            Log::info("Created Tenant", ['tenant_id' => $tenant->id, 'subdomain' => $tenant->subdomain]);
            //dd($tenant);
            Log::info("Seed Tenant Settings", ['tenant_id' => $tenant->id, 'subdomain' => $tenant->subdomain]);
            // update settings
            $tenant->updateSettings();
            Log::info("Seed tenant leagues ratings", ['tenant_id' => $tenant->id, 'subdomain' => $tenant->subdomain]);
            // seed suppliers
            $tenant->seedSuppliers();
            // seed customers
            $tenant->seedCustomers();
            // seed GLAs
            $tenant->seedGeneralLedgerAccounts();
            // seed expense categories and subcategories
            $tenant->seedExpenseCategoriesAndSubCategories();
            // create tenant admin user
            $tenant->createAdminUser($data['password']);
            // return the created tenant
            return $tenant;
        });
    }

    /**
     * Fetch a tenant by subdomain
     * @param $subdomain
     *
     * @return Tenant|null
     */
    public static function getTenantBySubdomain($subdomain): ?Tenant
    {
        $cid = strtolower("tenant:{$subdomain}");
        $tenant = Cache::get($cid);
        if(!empty($tenant)) {
            return $tenant;
        }

        $tenant = Tenant::query()
                        ->where('subdomain', $subdomain)
                        ->first();

        Cache::put($cid, $tenant, Carbon::now()->addHours(24));

        return $tenant;
    }

    /**
     * @param null $tenant_id
     *
     * @return Setting
     * @throws \Exception
     */
    public static function getTenantSettings($tenant_id = null){
        if(empty($tenant_id)) {
            if(!Auth::guard('tenant')->check()){
                throw new \Exception("You must be logged in to access the settings cache");
            }
            $user = Auth::guard('tenant')->user();
            $tenant_id = $user->tenant_id;
        }

        $cid = "tenant:{$tenant_id}:settings";

        $settings = Cache::get($cid);

        if(!empty($settings)) {
            return $settings;
        }

        $settings = Setting::query()->firstOrCreate(['tenant_id' => $tenant_id]);

        Cache::put($cid, $settings, Carbon::now()->addHours(24));

        return $settings;
    }

    public static function toNearestHundredsLower($num) {
        return floor($num / 100) * 100;
    }

    public static function toNearestHundredsUpper($num) {
        return ceil($num / 100) * 100;
    }

    public static function getItemByBarcode(string $barcode)
    {
        $builder = ItemStock::query();
        $builder->where('status', Stock::STATUS_ACTIVE);
        $builder->where(function ($query) use ($barcode) {
            $query->where('barcode', $barcode)->orWhere('secondary_barcode', $barcode);
        });
        return $builder->first();
    }

    public static function getSalableItems(int $branch_id = null)
    {
        $builder = ItemStock::query();
        $builder->whereIn('account', [Item::ACCOUNT_SALES_ONLY, Item::ACCOUNT_BOTH]);
        $builder->where('status', Stock::STATUS_ACTIVE);
        if ($branch_id)
        {
            $builder->where('branch_id', $branch_id);
        }
        return $builder->get();
    }

    public static function getPurchasableItems(int $branch_id = null)
    {
        $builder = ItemStock::query();
        $builder->whereIn('account', [Item::ACCOUNT_PURCHASES_ONLY, Item::ACCOUNT_BOTH]);
        if ($branch_id)
        {
            $builder->where('branch_id', $branch_id);
        }
        return $builder->get();
    }

    public static function getStocks(int $branch_id = null)
    {
        $builder = ItemStock::query();
        if ($branch_id)
        {
            $builder->where('branch_id', $branch_id);
        }
        return $builder->get();
    }

    public static function getItemStock(int $item_id, $branch_id = null)
    {
        $builder = ItemStock::query();
        $builder->where('id', $item_id);
        if ($branch_id)
        {
            $builder->where('branch_id', $branch_id);
        }
        return $builder->first();
    }

    public static function totalSales(Carbon $start_date, Carbon $end_date, $branch_id = null)
    {
        $builder = Sale::uncanceled();
        $builder->whereDate('created_at', '>=', $start_date);
        $builder->whereDate('created_at', '<=', $end_date);
        if ($branch_id)
        {
            $builder->where('branch_id', $branch_id);
        }
        return $builder->sum('net_amount');
    }

    public static function totalSettled(Carbon $start_date, Carbon $end_date, $branch_id = null)
    {
        $builder = Sale::settled();
        $builder->whereDate('created_at', '>=', $start_date);
        $builder->whereDate('created_at', '<=', $end_date);
        if ($branch_id)
        {
            $builder->where('branch_id', $branch_id);
        }
        return $builder->sum('net_amount');
    }

    public static function getSalesInfo(Carbon $start_date, Carbon $end_date, $branch_id = null)
    {
        $salesInfo = new stdClass();
        $start = $end_date->clone();
        $end = $start_date->clone();
        if ($end->lt($start))
        {
            $temp = $end;
            $end = $start;
            $start = $temp;
        }

        $salesInfo->startDate = $start->toDateString();
        $salesInfo->endDate = $end->toDateString();

        //sales
        $salesBuilder = Sale::query()
                            ->whereDate('created_at', '>=', $start->toDateString())
                            ->whereDate('created_at', '<=', $end->toDateString());
        if ($branch_id)
        {
            $salesBuilder->where('branch_id', $branch_id);
        }
        $sales = $salesBuilder->get();


        // active sales
        $activeSales = $sales->whereNotIn('status', [Sale::STATUS_CANCELED]);
        $salesInfo->totalSales = $activeSales->sum('net_amount');
        $salesInfo->salesCount = $activeSales->count();

        // canceled sales
        $canceled = $sales->where('status', Sale::STATUS_CANCELED);
        $salesInfo->totalCanceled = $canceled->sum('net_amount');
        $salesInfo->canceledCount = $canceled->count();

        return $salesInfo;
    }

    public static function daysTotalSales(Carbon $date, $branch_id = null)
    {
        $builder = Sale::uncanceled();
        $builder->whereDate('created_at', $date);
        if ($branch_id)
        {
            $builder->where('branch_id', $branch_id);
        }
        return $builder->sum('net_amount');
    }

    public static function daysTotalExpenses(Carbon $date, $branch_id = null)
    {
        $builder = Expense::approved();
        $builder->whereDate('requested_at', $date);
        if ($branch_id)
        {
            $builder->where('branch_id', $branch_id);
        }
        return $builder->sum('approved_amount');
    }

    public static function monthsTotalSales(Carbon $date, $branch_id = null)
    {
        $builder = Sale::uncanceled();
        $builder->whereYear('created_at', $date->year);
        $builder->whereMonth('created_at', $date->month);
        if ($branch_id)
        {
            $builder->where('branch_id', $branch_id);
        }
        return $builder->sum('net_amount');
    }

    public static function monthsTotalExpenses(Carbon $date, $branch_id = null)
    {
        $builder = Expense::approved();
        $builder->whereYear('requested_at', $date->year);
        $builder->whereMonth('requested_at', $date->month);
        if ($branch_id)
        {
            $builder->where('branch_id', $branch_id);
        }
        return $builder->sum('approved_amount');
    }

    public static function daysTotalSettled(Carbon $date, $branch_id = null)
    {
        $builder = Sale::settled();
        $builder->whereDate('created_at', $date);
        if ($branch_id)
        {
            $builder->where('branch_id', $branch_id);
        }
        return $builder->sum('net_amount');
    }

    public static function monthsTotalSettled(Carbon $date, $branch_id = null)
    {
        $builder = Sale::settled();
        $builder->whereYear('created_at', $date->year);
        $builder->whereMonth('created_at', $date->month);
        if ($branch_id)
        {
            $builder->where('branch_id', $branch_id);
        }
        return $builder->sum('net_amount');
    }
}
