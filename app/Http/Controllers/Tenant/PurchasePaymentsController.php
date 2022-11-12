<?php

namespace App\Http\Controllers\Tenant;

use App\Events\PurchasePaymentMade;
use App\Http\Controllers\TenantBaseController;
use App\Models\Tenant\GeneralLedgerAccount;
use App\Models\Tenant\JournalEntry;
use App\Models\Tenant\LineItem;
use App\Models\Tenant\Purchase;
use App\Models\Tenant\PurchasePayment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PurchasePaymentsController extends TenantBaseController
{
    public function store(Request $request, Purchase $purchase)
    {
        try
        {
            $loggedInUser = $this->getUser();
            //dd($loggedInUser);
            //dd($request->all());
            if (!$loggedInUser->hasAnyAccess(['tenant.purchases.create']))
            {
                return response()->json([
                    'message' => 'Permission Denied!'
                ], Response::HTTP_FORBIDDEN);
            }

            $accountsPayableGLA = GeneralLedgerAccount::query()->where([
                'name' => GeneralLedgerAccount::ACCOUNTS_PAYABLE_GLA_NAME
            ])->first();

            if(!$accountsPayableGLA) {
                return response()->json([
                    'message' => 'Accounts Payable ledger account not configured.'
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

            DB::transaction(function () use ($request, $loggedInUser, $purchase, $accountsPayableGLA, $cashGLA){
                $amount = intval($request->get('amount'));
                $transaction_date = $request->get('transaction_date');

                if(empty($transaction_date)){
                    $transaction_date = Carbon::today()->toDateString();
                }

                $payment = PurchasePayment::query()->create([
                    'tenant_id' => $loggedInUser->tenant_id,
                    'purchase_id' => $purchase->id,
                    'branch_id' => $purchase->branch_id,
                    'user_id' => $loggedInUser->id,
                    'supplier_id' => $purchase->supplier_id,
                    'amount' => $amount,
                    'transaction_date' => $transaction_date,
                ]);

                // write line items
                $journalEntry = JournalEntry::query()->create([
                    'tenant_id' => $loggedInUser->tenant_id,
                    'transactable_id' => $payment->id,
                    'transactable_type' => PurchasePayment::class,
                    'transaction_date' => $transaction_date,
                    'amount' => $amount,
                ]);

                $lineItems = [];

                // Credit Cash (Amount)
                $lineItems[] = new LineItem([
                    'tenant_id' => $loggedInUser->tenant_id,
                    'general_ledger_account_id' => $cashGLA->id,
                    'credit_record' => $amount,
                ]);

                // Debit Payable (Amount)
                $lineItems[] = new LineItem([
                    'tenant_id' => $loggedInUser->tenant_id,
                    'general_ledger_account_id' => $accountsPayableGLA->id,
                    'debit_record' => $amount,
                ]);

                $journalEntry->line_items()->saveMany($lineItems);

                // update user balance
                $loggedInUser->decrement('balance', $amount);

                // dispatch an event
                PurchasePaymentMade::dispatch($purchase, $payment);
            });

            return response()->json(['message' => 'Transaction Successful!'], Response::HTTP_ACCEPTED);
        } catch (\Throwable $ex)
        {
            return $this->handleJsonRequestException("COMPLETE_PURCHASE_PAYMENT", $ex);
        }
    }
}
