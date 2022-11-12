<?php

namespace App\Http\Controllers\Tenant;

use App\Events\SaleCompleted;
use App\Http\Controllers\TenantBaseController;
use App\Models\Tenant\GeneralLedgerAccount;
use App\Models\Tenant\JournalEntry;
use App\Models\Tenant\LineItem;
use App\Models\Tenant\Sale;
use App\Models\Tenant\SaleItem;
use App\ShopHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class StoreSaleController extends TenantBaseController
{
    public function store(Request $request)
    {
        try
        {
            $loggedInUser = $this->getUser();
            //dd($request->all());
            if (!$loggedInUser->isCashier() && !$loggedInUser->hasAnyAccess(['tenant.sales.create']))
            {
                return response()->json([
                    'message' => 'Permission Denied!'
                ], Response::HTTP_FORBIDDEN);
            }

            $saleGLA = GeneralLedgerAccount::query()->where([
                'name' => GeneralLedgerAccount::SALES_GLA_NAME,
                'account_type' => GeneralLedgerAccount::TYPE_ASSET,
            ])->first();

            if(!$saleGLA) {
                return response()->json([
                    'message' => 'Account not configured for purchases. No purchases ledger account'
                ], Response::HTTP_FORBIDDEN);
            }

            DB::transaction(function () use ($request, $loggedInUser, $saleGLA){
                $branchId = $request->get('branch_id');
                $customerId = $request->get('customer_id');

                $items = collect($request->get('items'));

                $grossAmount = $items->sum(fn($item) => $item['quantity'] * $item['price']);

                $paidAmount = intval($request->get('paid'));
                $taxRate = floatval($request->get('vat'));
                $discountRate = floatval($request->get('discount'));

                $taxAmount = ceil($grossAmount * $taxRate / 100);
                $discountAmount = ceil($grossAmount * $discountRate / 100);

                $netAmount = $grossAmount - $discountAmount + $taxAmount;

                $dueAmount = $grossAmount + $taxAmount - $paidAmount;

                if ($dueAmount == $grossAmount)
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

                $sale = Sale::query()->create([
                    'tenant_id' => $loggedInUser->tenant_id,
                    'barcode' => $transactionCode,
                    'transaction_date' => Carbon::parse($transactionDate),
                    'customer_id' => $customerId,
                    'branch_id' => $branchId,
                    'amount' => $grossAmount,
                    'discount' => $discountRate,
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
                    $price = $product['price'];

                    // attach item
                    SaleItem::query()->create([
                        'tenant_id' => $loggedInUser->tenant_id,
                        'sale_id' => $sale->id,
                        'item_id' => $itemId,
                        'price' => $price,
                        'quantity' => $quantity,
                        'discount' => $discountRate,
                        'vat' => $taxRate,
                    ]);
                }

                // write line items
                $journalEntry = JournalEntry::query()->create([
                    'tenant_id' => $loggedInUser->tenant_id,
                    'transactable_id' => $sale->id,
                    'transactable_type' => Sale::class,
                    'transaction_date' => $transactionDate,
                    'amount' => $grossAmount,
                ]);

                $lineItems = [];

                // Credit Sales (Gross Amount)
                $lineItems[] = new LineItem([
                    'tenant_id' => $loggedInUser->tenant_id,
                    'general_ledger_account_id' => $saleGLA->id,
                    'credit_record' => $grossAmount,
                ]);

                // Debit Cash (Paid Amount)
                if($paidAmount > 0){
                    $lineItems[] = new LineItem([
                        'tenant_id' => $loggedInUser->tenant_id,
                        'general_ledger_account_id' => $loggedInUser->general_ledger_account_id,
                        'debit_record' => $paidAmount,
                    ]);
                }

                // Debit Receivables (Due Amount)
                if($dueAmount > 0){
                    $accountsReceivableGLA = GeneralLedgerAccount::query()->where([
                        'name' => GeneralLedgerAccount::ACCOUNTS_RECEIVABLE_GLA_NAME
                    ])->first();

                    if($accountsReceivableGLA){
                        $lineItems[] = new LineItem([
                            'tenant_id' => $loggedInUser->tenant_id,
                            'general_ledger_account_id' => $accountsReceivableGLA->id,
                            'debit_record' => $dueAmount,
                        ]);
                    }
                }

                // Credit Sales Tax (Tax Payable) (Tax Amount)
                if($taxAmount > 0){
                    $saleTaxGLA = GeneralLedgerAccount::query()->where([
                        'name' => GeneralLedgerAccount::SALES_VAT_GLA_NAME
                    ])->first();

                    if($saleTaxGLA){
                        $lineItems[] = new LineItem([
                            'tenant_id' => $loggedInUser->tenant_id,
                            'general_ledger_account_id' => $saleTaxGLA->id,
                            'credit_record' => $taxAmount,
                        ]);
                    }
                }

                // Debit Sales Discount (Discount Allowed) (Discount Amount)
                $saleDiscountGLA = null;
                if($discountAmount > 0){
                    $saleDiscountGLA = GeneralLedgerAccount::query()->where([
                        'name' => GeneralLedgerAccount::SALES_DISCOUNTS_GLA_NAME
                    ])->first();

                    if($saleDiscountGLA){
                        $lineItems[] = new LineItem([
                            'tenant_id' => $loggedInUser->tenant_id,
                            'general_ledger_account_id' => $saleDiscountGLA->id,
                            'debit_record' => $discountAmount,
                        ]);
                    }
                }

                // Credit Receivable (Discount Amount)
                if($dueAmount > $discountAmount && !empty($accountsReceivableGLA)) {
                    $lineItems[] = new LineItem([
                        'tenant_id' => $loggedInUser->tenant_id,
                        'general_ledger_account_id' => $accountsReceivableGLA->id,
                        'credit_record' => $discountAmount,
                    ]);
                } else {
                    // Credit Cash (Discount Amount)
                    $lineItems[] = new LineItem([
                        'tenant_id' => $loggedInUser->tenant_id,
                        'general_ledger_account_id' => $loggedInUser->general_ledger_account_id,
                        'credit_record' => $discountAmount,
                    ]);
                }

                $journalEntry->line_items()->saveMany($lineItems);

                // update user balance
                if($paidAmount > 0)
                {
                    $loggedInUser->increment('balance', $paidAmount);
                }

                // dispatch an event
                SaleCompleted::dispatch($sale);
            });
            return response()->json(['message' => 'Transaction Successful!'], Response::HTTP_CREATED);
        } catch (\Throwable $ex)
        {
            return $this->handleJsonRequestException("COMPLETE_SALE_TRANSACTION", $ex);
        }
    }
}
