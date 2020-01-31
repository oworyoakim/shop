<?php
/**
 * Created by PhpStorm.
 * User: Yoakim
 * Date: 9/30/2018
 * Time: 4:25 PM
 */

namespace App\Repositories;

use App\Models\Sale;
use App\Models\Setting;
use App\Models\Expense;
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
            if ($setting = Setting::query()->where('setting_key', $key)->first())
            {
                return $setting->setting_value;
            }
            return null;
        });
    }

    public function set($key, $value)
    {
        $cacheKey = strtoupper(env('CACHE_KEY') . ".settings.{$key}");
        Cache::forget($cacheKey);
        Cache::put($cacheKey, $value, Carbon::now()->addHours(1));
        return Setting::query()->updateOrCreate(['setting_key' => $key], ['setting_value' => $value]);
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
        return DB::table('stocks')
                 ->select('items.id', 'items.barcode', 'items.title', 'stocks.sell_price as sellPrice', 'stocks.cost_price as costPrice', 'stocks.discount','stocks.status', 'stocks.quantity', 'units.slug as unit', 'categories.title as category')
                 ->leftJoin('items', 'items.id', '=', 'stocks.item_id')
                 ->leftJoin('units', 'items.unit_id', '=', 'units.id')
                 ->leftJoin('categories', 'items.category_id', '=', 'categories.id')
                 ->where('items.barcode', $barcode)
                 ->where('stocks.status', '=', 'active')
                 ->first();
    }

    public function getSalableItems(int $branch_id = null)
    {
        if ($branch_id)
        {
            return DB::table('stocks')
                     ->select('items.id', 'items.barcode', 'items.title', 'stocks.sell_price as sellPrice', 'stocks.cost_price as costPrice', 'stocks.quantity', 'stocks.discount','stocks.status', 'units.slug as unit', 'categories.title as category')
                     ->leftJoin('items', 'items.id', '=', 'stocks.item_id')
                     ->leftJoin('units', 'items.unit_id', '=', 'units.id')
                     ->leftJoin('categories', 'items.category_id', '=', 'categories.id')
                     ->where('items.account', '<>', 'purchases')
                     ->where('stocks.branch_id', $branch_id)
                     ->where('stocks.status', '=', 'active')
                     ->get();
        }
        return DB::table('stocks')
                 ->select('items.id', 'items.barcode', 'items.title', 'stocks.sell_price as sellPrice', 'stocks.cost_price as costPrice', 'stocks.quantity', 'stocks.discount', 'stocks.status','units.slug as unit', 'categories.title as category')
                 ->leftJoin('items', 'items.id', '=', 'stocks.item_id')
                 ->leftJoin('units', 'items.unit_id', '=', 'units.id')
                 ->leftJoin('categories', 'items.category_id', '=', 'categories.id')
                 ->where('items.account', '<>', 'purchases')
                 ->where('stocks.status','=', 'active')
                 ->get();
    }

    public function getPurchasableItems(int $branch_id = null)
    {
        if ($branch_id)
        {
            return DB::table('items')
                     ->select('items.id', 'items.barcode', 'items.title', 'stocks.sell_price as sellPrice', 'stocks.cost_price as costPrice', 'stocks.quantity', 'stocks.discount', 'units.slug as unit', 'categories.title as category')
                     ->leftJoin('stocks', 'items.id', '=', 'stocks.item_id')
                     ->leftJoin('units', 'items.unit_id', '=', 'units.id')
                     ->leftJoin('categories', 'items.category_id', '=', 'categories.id')
                     ->where('items.account', '<>', 'sales')
                     ->where('stocks.branch_id', $branch_id)
                     ->get();
        }
        return DB::table('items')
                 ->select('items.id', 'items.barcode', 'items.title', 'stocks.sell_price as sellPrice', 'stocks.cost_price as costPrice', 'stocks.quantity', 'stocks.discount', 'units.slug as unit', 'categories.title as category')
                 ->leftJoin('stocks', 'items.id', '=', 'stocks.item_id')
                 ->leftJoin('units', 'items.unit_id', '=', 'units.id')
                 ->leftJoin('categories', 'items.category_id', '=', 'categories.id')
                 ->where('items.account', '<>', 'sales')
                 ->get();
    }

    public function getStocks(int $branch_id = null)
    {
        if ($branch_id)
        {
            return DB::table('stocks')
                     ->select('items.id', 'items.barcode', 'items.title', 'stocks.sell_price as sellPrice', 'stocks.cost_price as costPrice', 'stocks.quantity', 'stocks.discount','stocks.status', 'units.slug as unit', 'categories.title as category')
                     ->leftJoin('items', 'items.id', '=', 'stocks.item_id')
                     ->leftJoin('units', 'items.unit_id', '=', 'units.id')
                     ->leftJoin('categories', 'items.category_id', '=', 'categories.id')
                     ->where('stocks.branch_id', $branch_id)
                     ->get();
        }
        return DB::table('stocks')
                 ->select('items.id', 'items.barcode', 'items.title', 'stocks.sell_price as sellPrice', 'stocks.cost_price as costPrice', 'stocks.quantity', 'stocks.discount', 'stocks.status','units.slug as unit', 'categories.title as category')
                 ->leftJoin('items', 'items.id', '=', 'stocks.item_id')
                 ->leftJoin('units', 'items.unit_id', '=', 'units.id')
                 ->leftJoin('categories', 'items.category_id', '=', 'categories.id')
                 ->get();
    }

    public function getItemStock(int $item_id, $branch_id = null)
    {
        if($branch_id){
            return DB::table('stocks')
                     ->select('items.id', 'items.barcode', 'items.title', 'items.account', 'stocks.branch_id as branch', 'stocks.sell_price as sellPrice', 'stocks.cost_price as costPrice', 'stocks.quantity', 'stocks.discount','stocks.status', 'units.slug as unit', 'categories.title as category')
                     ->leftJoin('items', 'items.id', '=', 'stocks.item_id')
                     ->leftJoin('units', 'items.unit_id', '=', 'units.id')
                     ->leftJoin('categories', 'items.category_id', '=', 'categories.id')
                     ->where('stocks.branch_id', $branch_id)
                     ->where('stocks.item_id', $item_id)
                     ->first();
        }
        return DB::table('stocks')
                 ->select('items.id', 'items.barcode', 'items.title', 'items.account', 'stocks.branch_id as branch', 'stocks.sell_price as sellPrice', 'stocks.cost_price as costPrice', 'stocks.quantity', 'stocks.discount','stocks.status', 'units.slug as unit', 'categories.title as category')
                 ->leftJoin('items', 'items.id', '=', 'stocks.item_id')
                 ->leftJoin('units', 'items.unit_id', '=', 'units.id')
                 ->leftJoin('categories', 'items.category_id', '=', 'categories.id')
                 ->where('stocks.item_id', $item_id)
                 ->first();
    }

    public function totalSales(Carbon $start_date, Carbon $end_date, $branch_id = null)
    {
        if ($branch_id)
        {
            return Sale::uncanceled()
                       ->where('branch_id', $branch_id)
                       ->whereDate('created_at', '>=', $start_date)
                       ->whereDate('created_at', '<=', $end_date)
                       ->sum('net_amount');
        }
        return Sale::uncanceled()
                   ->whereDate('created_at', '>=', $start_date)
                   ->whereDate('created_at', '<=', $end_date)
                   ->sum('net_amount');
    }

    public function totalSettled(Carbon $start_date, Carbon $end_date, $branch_id = null)
    {
        if ($branch_id)
        {
            return Sale::settled()
                       ->where('branch_id', $branch_id)
                       ->whereDate('created_at', '>=', $start_date)
                       ->whereDate('created_at', '<=', $end_date)
                       ->sum('net_amount');
        }
        return Sale::settled()
                   ->whereDate('created_at', '>=', $start_date)
                   ->whereDate('created_at', '<=', $end_date)
                   ->sum('net_amount');
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
        $activeSales = $sales->whereNotIn('status', ['canceled']);
        $salesInfo->totalSales = $activeSales->sum('net_amount');
        $salesInfo->salesCount = $activeSales->count();

        // canceled sales
        $canceled = $sales->where('status', 'canceled');
        $salesInfo->totalCanceled = $canceled->sum('net_amount');
        $salesInfo->canceledCount = $canceled->count();

        return $salesInfo;
    }

    public function daysTotalSales(Carbon $date,$branch_id = null)
    {
        if ($branch_id)
        {
            return Sale::uncanceled()
                       ->where('branch_id', $branch_id)
                       ->whereDate('created_at', $date)
                       ->sum('net_amount');
        }
        return Sale::uncanceled()
                   ->whereDate('created_at', $date)
                   ->sum('net_amount');
    }

    public function daysTotalExpenses(Carbon $date,$branch_id = null)
    {
        //return 0;
        if ($branch_id)
        {
            return Expense::approved()
                          ->where('branch_id', $branch_id)
                          ->whereDate('requested_at', $date)
                          ->sum('approved_amount');
        }
        return Expense::approved()
                      ->whereDate('requested_at', $date)
                      ->sum('approved_amount');
    }

    public function monthsTotalSales(Carbon $date, $branch_id = null)
    {
        if ($branch_id)
        {
            return Sale::uncanceled()
                       ->where('branch_id', $branch_id)
                       ->whereYear('created_at', $date->year)
                       ->whereMonth('created_at', $date->month)
                       ->sum('net_amount');
        }
        return Sale::uncanceled()
                   ->whereYear('created_at', $date->year)
                   ->whereMonth('created_at', $date->month)
                   ->sum('net_amount');
    }

    public function monthsTotalExpenses(Carbon $date, $branch_id = null)
    {
        if ($branch_id)
        {
            return Expense::approved()
                          ->where('branch_id', $branch_id)
                          ->whereYear('requested_at', $date->year)
                          ->whereMonth('requested_at', $date->month)
                          ->sum('approved_amount');
        }
        return Expense::approved()
                      ->whereYear('requested_at', $date->year)
                      ->whereMonth('requested_at', $date->month)
                      ->sum('approved_amount');
    }

    public function daysTotalSettled(Carbon $date,$branch_id = null)
    {
        if ($branch_id)
        {
            return Sale::settled()
                       ->where('branch_id', $branch_id)
                       ->whereDate('created_at', $date)
                       ->sum('net_amount');
        }
        return Sale::settled()
                   ->whereDate('created_at', $date)
                   ->sum('net_amount');
    }

    public function monthsTotalSettled(Carbon $date, $branch_id = null)
    {
        if ($branch_id)
        {
            return Sale::settled()
                       ->where('branch_id', $branch_id)
                       ->whereYear('created_at', $date->year)
                       ->whereMonth('created_at', $date->month)
                       ->sum('net_amount');
        }
        return Sale::settled()
                   ->whereYear('created_at', $date->year)
                   ->whereMonth('created_at', $date->month)
                   ->sum('net_amount');
    }

}
