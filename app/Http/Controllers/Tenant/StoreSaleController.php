<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\TenantBaseController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class StoreSaleController extends TenantBaseController
{
    public function index(Request $request)
    {
        try
        {
            return response()->json('Ok');
        } catch (\Exception $ex)
        {
            Log::error("COMPLETE_SALE_TRANSACTION: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
