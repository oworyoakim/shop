<?php

namespace App\Http\Controllers\Tenant;

use App\Events\PurchaseCompleted;
use App\Http\Controllers\TenantBaseController;
use App\Models\Tenant\GeneralLedgerAccount;
use App\Models\Tenant\JournalEntry;
use App\Models\Tenant\LineItem;
use App\Models\Tenant\Purchase;
use App\Models\Tenant\PurchaseItem;
use App\ShopHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class StorePurchaseController extends TenantBaseController
{
    public function store(Request $request)
    {
        try
        {
            $loggedInUser = $this->getUser();
            //dd($request->all());
            if (!$loggedInUser->hasAnyAccess(['tenant.purchases.create']))
            {
                return response()->json([
                    'message' => 'Permission Denied!'
                ], Response::HTTP_FORBIDDEN);
            }

            $purchaseGLA = GeneralLedgerAccount::query()->where([
                'name' => GeneralLedgerAccount::PURCHASES_GLA_NAME,
                'account_type' => GeneralLedgerAccount::TYPE_LIABILITY,
            ])->first();

            if(!$purchaseGLA) {
                return response()->json([
                    'message' => 'Account not configured for purchases. No purchases ledger account'
                ], Response::HTTP_FORBIDDEN);
            }

            DB::transaction(function () use ($request, $loggedInUser, $purchaseGLA){
                $branchId = $request->get('branch_id');
                $supplierId = $request->get('supplier_id');
                $grossAmount = intval($request->get('amount'));
                $paidAmount = intval($request->get('paid'));
                $items = $request->get('items');
                $taxRate = floatval($request->get('vat'));

                $taxAmount = ceil($grossAmount * $taxRate / 100);

                $netAmount = $grossAmount - $taxAmount;

                $dueAmount = $grossAmount - $paidAmount;

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

                $status = 'completed';
                if ($paymentStatus == 'pending')
                {
                    $status = 'pending';
                }

                $transactionDate = $request->get('transaction_date');
                if($transactionDate) {
                    $transactionDate = Carbon::now()->toDateString();
                }

                // create transaction
                $transactionCode = ShopHelper::generateTransactionCode($loggedInUser);

                $purchase = Purchase::query()->create([
                    'barcode' => $transactionCode,
                    'transaction_date' => Carbon::parse($transactionDate),
                    'supplier_id' => $supplierId,
                    'tenant_id' => $loggedInUser->tenant_id,
                    'branch_id' => $branchId,
                    'amount' => $grossAmount,
                    'vat' => $taxRate,
                    'status' => $status,
                    'payment_status' => $paymentStatus,
                    'user_id' => $loggedInUser->getUserId(),
                ]);

                // attach Items
                foreach ($items as $barcode => $product)
                {
                    $quantity = $product['quantity'];
                    $itemId = $product['id'];
                    $costPrice = $product['price'];

                    // attach item
                    PurchaseItem::query()->create([
                        'tenant_id' => $loggedInUser->tenant_id,
                        'purchase_id' => $purchase->id,
                        'item_id' => $itemId,
                        'price' => $costPrice,
                        'quantity' => $quantity,
                        'vat' => $taxRate,
                    ]);
                }

                // write line items
                $journalEntry = JournalEntry::query()->create([
                    'tenant_id' => $loggedInUser->tenant_id,
                    'transactable_id' => $purchase->id,
                    'transactable_type' => Purchase::class,
                    'transaction_date' => $transactionDate,
                    'amount' => $grossAmount,
                ]);

                $lineItems = [];

                // Debit Purchases (Net Amount)
                $lineItems[] = new LineItem([
                    'tenant_id' => $loggedInUser->tenant_id,
                    'general_ledger_account_id' => $purchaseGLA->id,
                    'debit_record' => $netAmount,
                ]);

                // Credit Cash (Paid Amount)
                if($paidAmount > 0){
                    $lineItems[] = new LineItem([
                        'tenant_id' => $loggedInUser->tenant_id,
                        'general_ledger_account_id' => $loggedInUser->general_ledger_account_id,
                        'credit_record' => $paidAmount,
                    ]);
                }

                // Credit Payable (Due Amount)
                if($dueAmount > 0){
                    $accountsPayableGLA = GeneralLedgerAccount::query()->where([
                        'name' => GeneralLedgerAccount::ACCOUNTS_PAYABLE_GLA_NAME
                    ])->first();

                    if($accountsPayableGLA){
                        $lineItems[] = new LineItem([
                            'tenant_id' => $loggedInUser->tenant_id,
                            'general_ledger_account_id' => $accountsPayableGLA->id,
                            'credit_record' => $dueAmount,
                        ]);
                    }

                }

                // Debit Sales Tax (Receivable) (Tax Amount)
                if($taxAmount > 0){
                    $purchaseTaxGLA = GeneralLedgerAccount::query()->where([
                        'name' => GeneralLedgerAccount::PURCHASES_VAT_GLA_NAME
                    ])->first();

                    if($purchaseTaxGLA){
                        $lineItems[] = new LineItem([
                            'tenant_id' => $loggedInUser->tenant_id,
                            'general_ledger_account_id' => $purchaseTaxGLA->id,
                            'debit_record' => $taxAmount,
                        ]);
                    }
                }

                $journalEntry->line_items()->saveMany($lineItems);

                // update user balance
                if($paidAmount > 0)
                {
                    $loggedInUser->decrement('balance', $paidAmount);
                }

                // dispatch an event
                PurchaseCompleted::dispatch($purchase);
            });

            return response()->json(['message' => 'Transaction Successful!'], Response::HTTP_CREATED);
        } catch (\Throwable $ex)
        {
            return $this->handleJsonRequestException("COMPLETE_PURCHASE_TRANSACTION", $ex);
        }
    }
}
