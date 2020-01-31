<?php
/**
 * Created by PhpStorm.
 * User: Yoakim
 * Date: 1/12/2019
 * Time: 12:34 PM
 */

use App\Repositories\SystemRepository;

if (!function_exists('settings'))
{
    function settings()
    {
        return new SystemRepository();
    }
}

