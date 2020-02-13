<?php
/**
 * Created by PhpStorm.
 * User: Yoakim
 * Date: 9/30/2018
 * Time: 4:25 PM
 */

namespace App\Repositories;

use App\Models\Item;
use App\Models\ItemStock;
use App\Models\Sale;
use App\Models\Setting;
use App\Models\Expense;
use App\Models\Stock;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use stdClass;

class SystemRepository implements ISystemRepository
{
    public function get($key)
    {
        $cacheKey = strtoupper(env('CACHE_KEY') . ".settings.{$key}");
        return Cache::remember($cacheKey, Carbon::now()->addHours(1), function () use ($key) {
            if ($setting = Setting::query()->where('key', $key)->first())
            {
                return $setting->value;
            }
            return null;
        });
    }

    public function set($key, $value)
    {
        $cacheKey = strtoupper(env('CACHE_KEY') . ".settings.{$key}");
        Cache::forget($cacheKey);
        Cache::put($cacheKey, $value, Carbon::now()->addHours(1));
        return Setting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public function beginTransaction()
    {
        DB::beginTransaction();
    }

    public function commitTransaction()
    {
        DB::commit();
    }

    public function rollbackTransaction()
    {
        DB::rollBack();
    }

    public function getItemByBarcode(string $barcode)
    {
        $builder = ItemStock::query();
        $builder->where('status', Stock::STATUS_ACTIVE);
        $builder->where(function ($query) use ($barcode) {
            $query->where('secondaryBarcode', $barcode)->orWhere('barcode', $barcode);
        });
        return $builder->first();
    }

    public function getSalableItems(int $branch_id = null)
    {
        $builder = ItemStock::query();
        $builder->whereIn('account', [Item::ACCOUNT_SALES_ONLY, Item::ACCOUNT_BOTH]);
        $builder->where('status', Stock::STATUS_ACTIVE);
        if ($branch_id)
        {
            $builder->where('branchId', $branch_id);
        }
        return $builder->get();
    }

    public function getPurchasableItems(int $branch_id = null)
    {
        $builder = ItemStock::query();
        $builder->whereIn('account', [Item::ACCOUNT_PURCHASES_ONLY, Item::ACCOUNT_BOTH]);
        if ($branch_id)
        {
            $builder->where('branchId', $branch_id);
        }
        return $builder->get();
    }

    public function getStocks(int $branch_id = null)
    {
        $builder = ItemStock::query();
        if ($branch_id)
        {
            $builder->where('branchId', $branch_id);
        }
        return $builder->get();
    }

    public function getItemStock(int $item_id, $branch_id = null)
    {
        $builder = ItemStock::query();
        $builder->where('id', $item_id);
        if ($branch_id)
        {
            $builder->where('branchId', $branch_id);
        }
        return $builder->first();
    }

    public function totalSales(Carbon $start_date, Carbon $end_date, $branch_id = null)
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

    public function totalSettled(Carbon $start_date, Carbon $end_date, $branch_id = null)
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

    public function getSalesInfo(Carbon $start_date, Carbon $end_date, $branch_id = null)
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

    public function daysTotalSales(Carbon $date, $branch_id = null)
    {
        $builder = Sale::uncanceled();
        $builder->whereDate('created_at', $date);
        if ($branch_id)
        {
            $builder->where('branch_id', $branch_id);
        }
        return $builder->sum('net_amount');
    }

    public function daysTotalExpenses(Carbon $date, $branch_id = null)
    {
        $builder = Expense::approved();
        $builder->whereDate('requested_at', $date);
        if ($branch_id)
        {
            $builder->where('branch_id', $branch_id);
        }
        return $builder->sum('approved_amount');
    }

    public function monthsTotalSales(Carbon $date, $branch_id = null)
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

    public function monthsTotalExpenses(Carbon $date, $branch_id = null)
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

    public function daysTotalSettled(Carbon $date, $branch_id = null)
    {
        $builder = Sale::settled();
        $builder->whereDate('created_at', $date);
        if ($branch_id)
        {
            $builder->where('branch_id', $branch_id);
        }
        return $builder->sum('net_amount');
    }

    public function monthsTotalSettled(Carbon $date, $branch_id = null)
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
