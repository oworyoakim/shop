<?php

namespace Tests\Feature;

use App\Events\PurchaseCompleted;
use App\Models\Landlord\Tenant;
use App\Models\Tenant\Branch;
use App\Models\Tenant\GeneralLedgerAccount;
use App\Models\Tenant\Purchase;
use App\Models\Tenant\Supplier;
use App\Models\Tenant\User;
use App\Traits\Tenant\UsesLoggedInUser;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class StorePurchaseControllerTest extends TestCase
{
    use DatabaseTransactions, WithFaker, UsesLoggedInUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tenant = Tenant::where('subdomain', 'test')->first();
        $this->user = User::where(['tenant_id' => $this->tenant->id, 'username' => $this->tenant->subdomain])->first();
        $branch_name = $this->faker->company;
        // Get the GLAs
        $asset_gla = GeneralLedgerAccount::updateOrCreate([
            'name' => GeneralLedgerAccount::ASSET_GLA_NAME,
            'account_type' => GeneralLedgerAccount::TYPE_ASSET,
            'tenant_id' => $this->tenant->id
        ]);

        $cash_gla = GeneralLedgerAccount::updateOrCreate([
            'name' => GeneralLedgerAccount::CASH_GLA_NAME,
            'account_type' => GeneralLedgerAccount::TYPE_ASSET,
            'parent_id' => $asset_gla->id,
            'tenant_id' => $this->tenant->id
        ]);
        // Create the Branch's Cash GLA
        $gla = GeneralLedgerAccount::updateOrCreate([
            'name' => $branch_name,
            'account_type' => GeneralLedgerAccount::TYPE_ASSET,
            'parent_id' => $cash_gla->id,
            'tenant_id' => $this->tenant->id
        ]);
        $this->branch = Branch::updateOrCreate([
            'tenant_id' => $this->tenant->id,
            'name' => $branch_name,
            'general_ledger_account_id' => $gla->id
        ])->first();

        $this->supplier = Supplier::updateOrCreate(['tenant_id' => $this->tenant->id, 'name' => $this->faker->company])->first();
        $this->amount = $this->faker->numberBetween(100000, 500000);
        $this->base_url = "http://{$this->tenant->subdomain}.". config('app.url_base') . '/v1';
    }

    public function test_ordinary_purchase_can_be_created_by_authenticated_users() {
        Event::fake();
        $vat_rate = 0;
        $payload = [
            'branch_id' => $this->branch->id,
            'supplier_id' => $this->supplier->id,
            'amount' => $this->amount,
            'paid' => $this->amount,
            'vat' => $vat_rate,
            'transaction_date' => Carbon::now()->toDateString(),
            'items' => [
                '1FPS7621' => [
                    'id' => 8,
                    'quantity' => 5,
                    'price' => 1500,
                ],
                '1QJI9673' => [
                    'id' => 13,
                    'quantity' => 2,
                    'price' => 3000,
                ],
                '1BVW5171' => [
                    'id' => 10,
                    'quantity' => 3,
                    'price' => 1500,
                ],
            ],
        ];

        $url = "{$this->base_url}/purchases";
        $this->actingAs($this->user, 'tenant');
        $response = $this->post($url, $payload, ['Accept' => 'application/json']);
        $response->assertCreated();

        $purchase = Purchase::query()->latest('id')->first();

        $this->assertEquals($this->amount, $purchase->amount);
        $debit_record = $purchase->journal_entry->line_items()->sum('debit_record');
        $credit_record = $purchase->journal_entry->line_items()->sum('credit_record');
        $this->assertTrue($debit_record > 0);
        $this->assertTrue($credit_record > 0);
        $this->assertEquals($debit_record, $credit_record);
        $this->assertEquals($this->amount, $credit_record);
        Event::assertDispatched(PurchaseCompleted::class);
    }

    public function test_taxed_purchase_can_be_created_by_authenticated_users() {
        Event::fake();
        $vat_rate = 15; // Charged 15% on tax
        $paid = round($this->amount * 60 / 100); // Paid 60%

        $payload = [
            'branch_id' => $this->branch->id,
            'supplier_id' => $this->supplier->id,
            'amount' => $this->amount,
            'paid' => $paid,
            'vat' => $vat_rate,
            'transaction_date' => Carbon::now()->toDateString(),
            'items' => [
                '1FPS7621' => [
                    'id' => 8,
                    'quantity' => 5,
                    'price' => 1500,
                ],
                '1QJI9673' => [
                    'id' => 13,
                    'quantity' => 2,
                    'price' => 3000,
                ],
            ],
        ];

        $url = "{$this->base_url}/purchases";
        $this->actingAs($this->user, 'tenant');
        $response = $this->post($url, $payload, ['Accept' => 'application/json']);
        $response->assertCreated();

        $purchase = Purchase::query()->latest('id')->first();

        $this->assertEquals($this->amount, $purchase->amount);
        $this->assertEquals($vat_rate, $purchase->vat);
        $debit_record = $purchase->journal_entry->line_items()->sum('debit_record');
        $credit_record = $purchase->journal_entry->line_items()->sum('credit_record');
        $this->assertTrue($debit_record > 0);
        $this->assertTrue($credit_record > 0);
        $this->assertEquals($debit_record, $credit_record);
        $this->assertEquals($this->amount, $credit_record);
        Event::assertDispatched(PurchaseCompleted::class);
    }

}
