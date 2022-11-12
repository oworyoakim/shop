<?php

namespace App\Http\Controllers\Tenant;

use App\Events\SalePaymentMade;
use App\Http\Controllers\TenantBaseController;
use App\Models\Tenant\GeneralLedgerAccount;
use App\Models\Tenant\JournalEntry;
use App\Models\Tenant\LineItem;
use App\Models\Tenant\Sale;
use App\Models\Tenant\SalePayment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SalePaymentsController extends TenantBaseController
{
    public function store(Request $request, Sale $sale)
    {
        try
        {
            $loggedInUser = $this->getUser();
            //dd($loggedInUser);
            //dd($request->all());
            if (!$loggedInUser->hasAnyAccess(['tenant.sales.payments.create']))
            {
                return response()->json([
                    'message' => 'Permission Denied!'
                ], Response::HTTP_FORBIDDEN);
            }

            $accountsReceivableGLA = GeneralLedgerAccount::query()->where([
                'name' => GeneralLedgerAccount::ACCOUNTS_RECEIVABLE_GLA_NAME
            ])->first();

            if(!$accountsReceivableGLA) {
                return response()->json([
                    'message' => 'Accounts Receivable ledger account not configured.'
                ], Response::HTTP_FORBIDDEN);
            }

            $cashGLA = GeneralLedgerAccount::query()->where([
                'name' => GeneralLedgerAccount::CASH_GLA_NAME,
                'account_type' => GeneralLedgerAccount::TYPE_ASSET,
            ])->first();

            if(!$cashGLA) {
                return response()->json([
                    'message' => 'Account not configured for sales. No cash ledger account'
                ], Response::HTTP_FORBIDDEN);
            }

            DB::transaction(function () use ($request, $loggedInUser, $sale, $accountsReceivableGLA, $cashGLA){
                $amount = intval($request->get('amount'));
                $transaction_date = $request->get('transaction_date');

                if(empty($transaction_date)){
                    $transaction_date = Carbon::today()->toDateString();
                }

                $payment = SalePayment::query()->create([
                    'tenant_id' => $loggedInUser->tenant_id,
                    'sale_id' => $sale->id,
                    'branch_id' => $sale->branch_id,
                    'user_id' => $loggedInUser->id,
                    'customer_id' => $sale->customer_id,
                    'amount' => $amount,
                    'transaction_date' => $transaction_date,
                ]);

                // write line items
                $journalEntry = JournalEntry::query()->create([
                    'tenant_id' => $loggedInUser->tenant_id,
                    'transactable_id' => $payment->id,
                    'transactable_type' => SalePayment::class,
                    'transaction_date' => $transaction_date,
                    'amount' => $amount,
                ]);

                $lineItems = [];

                // Debit Cash (Amount)
                $lineItems[] = new LineItem([
                    'tenant_id' => $loggedInUser->tenant_id,
                    'general_ledger_account_id' => $cashGLA->id,
                    'debit_record' => $amount,
                ]);

                // Credit Receivable (Amount)
                $lineItems[] = new LineItem([
                    'tenant_id' => $loggedInUser->tenant_id,
                    'general_ledger_account_id' => $accountsReceivableGLA->id,
                    'credit_record' => $amount,
                ]);

                $journalEntry->line_items()->saveMany($lineItems);

                // update user balance
                $loggedInUser->increment('balance', $amount);

                // dispatch an event
                SalePaymentMade::dispatch($sale, $payment);
            });
            return response()->json(['message' => 'Transaction Successful!'], Response::HTTP_ACCEPTED);
        } catch (\Throwable $ex)
        {
            return $this->handleJsonRequestException("COMPLETE_SALE_PAYMENT", $ex);
        }
    }
}
