<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\TenantBaseController;
use App\Models\Tenant\Stock;
use Illuminate\Http\Request;

class StocksController extends TenantBaseController
{
    public function index(Request $request) {
        try {
            $branch_id = $request->get('branch_id');

            $stocks = Stock::query()
                           ->join('branches', function ($join) use ($branch_id) {
                               $join->on('stocks.branch_id', '=', 'branches.id')->where('branches.id', $branch_id);
                           })
                           ->join('items', 'items.id', '=', 'stocks.item_id')
                           ->join('units', 'items.unit_id', '=', 'units.id')
                           ->join('categories', 'items.category_id', '=', 'categories.id')
                           ->select([
                               'items.id',
                               'items.barcode',
                               'items.title',
                               'items.account',
                               'stocks.branch_id',
                               'branches.name as branch_name',
                               'stocks.sell_price',
                               'stocks.cost_price',
                               'stocks.quantity',
                               'stocks.discount',
                               'units.slug as unit',
                               'categories.title as category'
                           ])
                           ->paginate(10);

            return response()->json($stocks);
        }catch (\Throwable $ex) {
            return $this->handleJsonRequestException("GET_STOCKS", $ex);
        }
    }
}
