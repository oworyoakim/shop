<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Comment;
use App\Models\Item;
use App\Models\ItemStock;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchasesPayable;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use stdClass;
use DNS1D;

class CashierController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $data = [];
            return response()->json($data);
        } catch (Exception $ex)
        {
            Log::error("SHOP_DASHBOARD: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function getShopInfo(Request $request)
    {
        try
        {
            $shopInfo = new stdClass();
            $shopInfo->branchId = 1;
            $shopInfo->branchName = "";
            $shopInfo->branchBalance = 0;
            $shopInfo->branchCashiersBalance = 0;
            $shopInfo->branchStockAtHand = 0;
            $shopInfo->vatRate = 0;
            $shopInfo->canCreateItem = true;
            $shopInfo->canCreateSupplier = true;

            return response()->json($shopInfo);
        } catch (Exception $ex)
        {
            Log::error("GET_SHOP_INFO: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function cancelSaleTransaction(Request $request)
    {
        try
        {
            return response()->json('Ok');
        } catch (Exception $ex)
        {
            Log::error("CANCEL_SALE_TRANSACTION: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function cancelPurchaseTransaction(Request $request)
    {
        try
        {
            return response()->json('Ok');
        } catch (Exception $ex)
        {
            Log::error("CANCEL_PURCHASE_TRANSACTION: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
