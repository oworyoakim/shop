<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\TenantBaseController;
use Illuminate\Http\Request;

class CashierController extends TenantBaseController
{
    public function index(Request $request)
    {
        try
        {
            $data = [];
            return response()->json($data);
        } catch (\Throwable $ex)
        {
            return $this->handleJsonRequestException('SHOP_DASHBOARD', $ex);
        }
    }

    public function getShopInfo(Request $request)
    {
        try
        {
            $shopInfo = new \stdClass();
            $shopInfo->branchId = 1;
            $shopInfo->branchName = "";
            $shopInfo->branchBalance = 0;
            $shopInfo->branchCashiersBalance = 0;
            $shopInfo->branchStockAtHand = 0;
            $shopInfo->vatRate = 0;
            $shopInfo->canCreateItem = true;
            $shopInfo->canCreateSupplier = true;

            return response()->json($shopInfo);
        } catch (\Throwable $ex)
        {
            return $this->handleJsonRequestException('GET_SHOP_INFO', $ex);
        }
    }

    public function completeSaleTransaction(Request $request)
    {
        try
        {
            return response()->json('Ok');
        } catch (\Throwable $ex)
        {
            return $this->handleJsonRequestException('COMPLETE_SALE_TRANSACTION', $ex);
        }
    }

    public function cancelSaleTransaction(Request $request)
    {
        try
        {
            return response()->json('Ok');
        } catch (\Throwable $ex)
        {
            return $this->handleJsonRequestException('CANCEL_SALE_TRANSACTION', $ex);
        }
    }

    public function cancelPurchaseTransaction(Request $request)
    {
        try
        {
            return response()->json('Ok');
        } catch (\Throwable $ex)
        {
            return $this->handleJsonRequestException('CANCEL_PURCHASE_TRANSACTION', $ex);
        }
    }
}
