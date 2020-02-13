<?php
/**
 * Created by PhpStorm.
 * User: Yoakim
 * Date: 1/12/2019
 * Time: 12:34 PM
 */

use App\Models\Item;
use App\Models\User;
use App\Repositories\SystemRepository;
use Illuminate\Support\Carbon;

if (!function_exists('settings'))
{
    function settings()
    {
        return new SystemRepository();
    }
}

if (!function_exists('toNearestHundredsLower'))
{
    function toNearestHundredsLower($num) {
        return floor($num / 100) * 100;
    }
}


if (!function_exists('toNearestHundredsUpper'))
{
    function toNearestHundredsUpper($num) {
        return ceil($num / 100) * 100;
    }
}

