<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchasesPayable;
use App\Models\Stock;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Exception;

class StorePurchaseController extends Controller
{
    public function __invoke(Request $request)
    {
        try
        {
            //dd($request->all());
            if (!Sentinel::hasAnyAccess(['purchases.create']))
            {
                throw new Exception('Permission Denied!');
            }
            $user = Sentinel::getUser();
            $branchId = $request->get('branchId');
            $supplierId = $request->get('supplierId');
            $grossAmount = intval($request->get('grossAmount'));
            $netAmount = intval($request->get('netAmount'));
            $paidAmount = intval($request->get('paidAmount'));
            $dueDate = $request->get('dueDate');
            $transactDate = $request->get('paymentDate');
            $discountAmount = intval($request->get('discount'));
            $taxAmount = intval($request->get('taxAmount'));
            $taxRate = floatval($request->get('taxRate'));
            $items = $request->get('items');

            $dueAmount = $netAmount - $paidAmount;

            if ($dueAmount == $netAmount)
            {
                $paymentStatus = 'pending';
            } elseif ($dueAmount == 0)
            {
                $paymentStatus = 'settled';
            } else
            {
                $paymentStatus = 'partial';
            }

            $discountRate = 0;
            if ($discountAmount > 0)
            {
                $discountRate = round(($discountAmount * 100 / $grossAmount), 2);
            }

            $status = 'completed';
            if ($paymentStatus == 'pending')
            {
                $status = 'pending';
            }

            settings()->beginTransaction();
            // create transaction
            $transcode = Purchase::generateTransactionCode($user);
            $purchase = Purchase::query()->create([
                'transcode' => $transcode,
                'transact_date' => Carbon::parse($transactDate),
                'supplier_id' => $supplierId,
                'branch_id' => $branchId,
                'gross_amount' => $grossAmount,
                'vat_rate' => $taxRate,
                'vat_amount' => $taxAmount,
                'discount_rate' => $discountRate,
                'discount_amount' => $discountAmount,
                'net_amount' => $netAmount,
                'status' => $status,
                'payment_status' => $paymentStatus,
                'user_id' => $user->getUserId(),
            ]);

            // attach Items
            foreach ($items as $barcode => $product)
            {
                $quantity = $product['quantity'];
                $itemId = $product['id'];
                $costPrice = $product['price'];
                $itemGrossAmount = $quantity * $costPrice;
                $itemDiscountRate = $product['discount'];
                $itemDiscountAmount = toNearestHundredsLower(round(($itemDiscountRate * $itemGrossAmount / 100), 2));
                $itemNetAmount = $itemGrossAmount - $itemDiscountAmount;

                $item = Item::query()->find($itemId);
                if (!$item)
                {
                    throw new Exception("Invalid Item with barcode:  {$barcode}");
                }

                // attach item
                PurchaseItem::query()->create([
                    'purchase_id' => $purchase->id,
                    'item_id' => $item->id,
                    'cost_price' => $costPrice,
                    'quantity' => $quantity,
                    'gross_amount' => $itemGrossAmount,
                    'discount_rate' => $itemDiscountRate,
                    'discount_amount' => $itemDiscountAmount,
                    'net_amount' => $itemNetAmount,
                ]);

                // Update Stocks
                // compute sell price if this item is not for purchases only
                $sellPrice = 0;
                if ($item->account != Item::ACCOUNT_PURCHASES_ONLY)
                {
                    $sellPrice = toNearestHundredsUpper(round((($item->margin + 100) * $costPrice) / 100, 2));
                }

                $stock = Stock::query()->where([
                    'item_id' => $product['id'],
                    'branch_id' => $branchId
                ])->first();

                if ($stock)
                {
                    // update
                    $stock->quantity += $quantity;
                    $stock->cost_price = $costPrice;
                    $stock->sell_price = $sellPrice;
                    $stock->status = Stock::STATUS_ACTIVE;
                    $stock->save();
                } else
                {
                    // create new
                    Stock::query()->create([
                        'quantity' => $quantity,
                        'cost_price' => $costPrice,
                        'sell_price' => $sellPrice,
                        'item_id' => $item->id,
                        'branch_id' => $branchId,
                        'user_id' => $user->id
                    ]);
                }
            }

            // create payables if any
            if ($paidAmount == 0 || $dueAmount > 0)
            {
                if (!$dueDate)
                {
                    throw new Exception("Due date required for credit purchases!");
                }
                PurchasesPayable::query()->create([
                    'purchase_id' => $purchase->id,
                    'transact_date' => Carbon::parse($transactDate),
                    'branch_id' => $branchId,
                    'user_id' => $user->id,
                    'supplier_id' => $supplierId,
                    'amount' => $dueAmount,
                    'due_date' => Carbon::parse($dueDate),
                ]);
            }
            // update user balance
            // TODO: Implement user current balance update
            settings()->commitTransaction();
            return response()->json('Transaction Successful!');
        } catch (Exception $ex)
        {
            settings()->rollbackTransaction();
            Log::error("COMPLETE_PURCHASE_TRANSACTION: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
