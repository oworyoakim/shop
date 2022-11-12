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

    public function store(Request $request)
    {
        try
        {
            $loggedInUser = $this->getUser();
            //dd($request->all());
            if (!$loggedInUser->hasAnyAccess(['tenant.sales.create']))
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

            $cashGLA = GeneralLedgerAccount::query()->where([
                'name' => GeneralLedgerAccount::CASH_GLA_NAME,
                'account_type' => GeneralLedgerAccount::TYPE_ASSET,
            ])->first();

            if(!$cashGLA) {
                return response()->json([
                    'message' => 'Account not configured for purchases. No cash ledger account'
                ], Response::HTTP_FORBIDDEN);
            }

            DB::transaction(function () use ($request, $loggedInUser, $saleGLA, $cashGLA){
                $branchId = $request->get('branch_id');
                $customerId = $request->get('customer_id');
                $grossAmount = intval($request->get('amount'));
                $paidAmount = intval($request->get('paid'));
                $items = $request->get('items');
                $taxRate = floatval($request->get('vat_rate'));
                $discountRate = floatval($request->get('discount_rate'));

                $taxAmount = ceil($grossAmount * $taxRate / 100);
                $discountAmount = ceil($grossAmount * $discountRate / 100);

                $netAmount = $grossAmount - $discountAmount + $taxAmount;

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

                $sale = Sale::query()->create([
                    'tenant_id' => $loggedInUser->tenant_id,
                    'barcode' => $transactionCode,
                    'transaction_date' => Carbon::parse($transactionDate),
                    'customer_id' => $customerId,
                    'branch_id' => $branchId,
                    'gross_amount' => $grossAmount,
                    'discount_rate' => $discountRate,
                    'discount_amount' => $discountAmount,
                    'vat_rate' => $taxRate,
                    'vat_amount' => $taxAmount,
                    'net_amount' => $netAmount,
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
                    SaleItem::query()->create([
                        'tenant_id' => $loggedInUser->tenant_id,
                        'sale_id' => $sale->id,
                        'item_id' => $itemId,
                        'sale_price' => $costPrice,
                        'quantity' => $quantity,
                        'vat_rate' => $taxRate,
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

                // Credit Sales (Net Amount)
                $lineItems[] = new LineItem([
                    'tenant_id' => $loggedInUser->tenant_id,
                    'general_ledger_account_id' => $saleGLA->id,
                    'credit_record' => $netAmount,
                ]);

                // Debit Cash (Paid Amount)
                if($paidAmount > 0){
                    $lineItems[] = new LineItem([
                        'tenant_id' => $loggedInUser->tenant_id,
                        'general_ledger_account_id' => $cashGLA->id,
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
            return $this->handleJsonRequestException("COMPLETE_PURCHASE_TRANSACTION", $ex);
        }
    }
}
