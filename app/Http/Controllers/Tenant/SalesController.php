<?php

namespace App\Http\Controllers\Tenant;



use App\Http\Controllers\TenantBaseController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SalesController extends TenantBaseController
{

    public function index(Request $request)
    {
        try
        {
            return response()->json('Ok');
        } catch (\Throwable $ex)
        {
            return $this->handleJsonRequestException("GET_SALES", $ex);
        }
    }

    public function store(Request $request)
    {
        try
        {
            return response()->json('Ok');
        } catch (\Throwable $ex)
        {
            return $this->handleJsonRequestException("COMPLETE_SALE_TRANSACTION", $ex);
        }
    }

}
