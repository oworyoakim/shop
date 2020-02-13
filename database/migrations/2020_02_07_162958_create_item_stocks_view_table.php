<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateItemStocksViewTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = DB::table('items')
                 ->select('items.id', 'items.barcode', 'items.secondary_barcode as secondaryBarcode', 'items.title', 'items.account', 'stocks.branch_id as branchId','stocks.sell_price as sellPrice', 'stocks.cost_price as costPrice', 'stocks.quantity as stockQty', 'stocks.discount', 'stocks.secondary_discount as secondaryDiscount', 'stocks.status', 'units.id as unitId', 'units.slug as unit', 'categories.id as categoryId', 'categories.title as category')
                 ->leftJoin('stocks', 'items.id', '=', 'stocks.item_id')
                 ->leftJoin('units', 'items.unit_id', '=', 'units.id')
                 ->leftJoin('categories', 'items.category_id', '=', 'categories.id')
                 ->toSql();
        $query = "CREATE VIEW item_stocks AS ({$sql});";
        DB::statement($query);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW item_stocks;");
    }
}
