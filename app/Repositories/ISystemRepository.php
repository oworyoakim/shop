<?php
/**
 * Created by PhpStorm.
 * User: Yoakim
 * Date: 9/30/2018
 * Time: 4:24 PM
 */

namespace App\Repositories;

use Illuminate\Support\Carbon;
use stdClass;

interface ISystemRepository
{
    public function get($key);

    public function set($key, $value);

    public function getItemByBarcode(string $barcode);

    public function getSalableItems(int $branch_id = null);

    public function getStocks(int $branch_id = null);

    public function getItemStock(int $item_id, $branch_id = null);

    public function totalSales(Carbon $start_date, Carbon $end_date, $branch_id = null);

    public function totalSettled(Carbon $start_date, Carbon $end_date, $branch_id = null);

    public function getSalesInfo(Carbon $start_date, Carbon $end_date, $branch_id = null);

    public function daysTotalSales(Carbon $date, $branch_id = null);

    public function daysTotalExpenses(Carbon $date, $branch_id = null);

    public function daysTotalSettled(Carbon $date, $branch_id = null);

    public function monthsTotalSettled(Carbon $date, $branch_id = null);

    public function monthsTotalSales(Carbon $date, $branch_id = null);

    public function monthsTotalExpenses(Carbon $date, $branch_id = null);

}
