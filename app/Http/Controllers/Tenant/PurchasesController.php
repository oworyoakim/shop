<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\TenantBaseController;
use App\Models\Tenant\Purchase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PurchasesController extends TenantBaseController
{
    public function index(Request $request) {
        try
        {
            $loggedInUser = $this->getUser();
            $purchases = Purchase::query()->paginate(10);

            $items = $purchases->getCollection();
            $modifiedItems = $items->map(function ($item) use ($loggedInUser){
                $item->canBeEdited = $loggedInUser->hasAnyAccess(['tenant.purchases.update']);
                $item->canBeDeleted = $loggedInUser->hasAnyAccess(['tenant.purchases.delete']);
                return $item;
            });
            $purchases->setCollection($modifiedItems);

            return response()->json($purchases);
        } catch (\Throwable $ex)
        {
            return $this->handleJsonRequestException("GET_PURCHASES", $ex);
        }
    }
}
