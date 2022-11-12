<?php

namespace Tests\Feature;

use App\Events\PurchaseCompleted;
use App\Events\SaleCompleted;
use App\Models\Landlord\Tenant;
use App\Models\Tenant\Branch;
use App\Models\Tenant\Customer;
use App\Models\Tenant\GeneralLedgerAccount;
use App\Models\Tenant\Purchase;
use App\Models\Tenant\Sale;
use App\Models\Tenant\Supplier;
use App\Models\Tenant\User;
use App\Traits\Tenant\UsesLoggedInUser;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;

class StoreSaleControllerTest extends \Tests\TestCase
{
    use DatabaseTransactions, WithFaker, UsesLoggedInUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tenant = Tenant::where('subdomain', 'test')->first();

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
        $branch_gla = GeneralLedgerAccount::updateOrCreate([
            'name' => $branch_name,
            'account_type' => GeneralLedgerAccount::TYPE_ASSET,
            'parent_id' => $cash_gla->id,
            'tenant_id' => $this->tenant->id
        ]);
        $this->branch = Branch::updateOrCreate([
            'tenant_id' => $this->tenant->id,
            'name' => $branch_name,
            'general_ledger_account_id' => $branch_gla->id
        ])->first();
        // Create the User's Cash GLA
        $name = $this->faker->name;
        $user_gla = GeneralLedgerAccount::updateOrCreate([
            'name' => $name,
            'account_type' => GeneralLedgerAccount::TYPE_ASSET,
            'parent_id' => $cash_gla->id,
            'tenant_id' => $this->tenant->id
        ]);
        $this->user = User::updateOrCreate([
            'tenant_id' => $this->tenant->id,
            'branch_id' => $this->branch->id,
            'username' => 'testcashier',
            'group' => User::TYPE_CASHIERS,
        ], [
            'general_ledger_account_id' => $user_gla->id,
            'name' => $name,
            'password' => Hash::make('password'),
            'active' => true
        ]);

        $this->customer = Customer::updateOrCreate(['tenant_id' => $this->tenant->id, 'name' => $this->faker->name]);
        $this->base_url = "http://{$this->tenant->subdomain}.". config('app.url_base') . '/v1';
    }

    public function test_ordinary_sale_can_be_created_by_authenticated_users() {
        Event::fake();
        $vat_rate = 0;
        $discount_rate = 0;
        $payload = [
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customer->id,
            'vat' => $vat_rate,
            'discount' => $discount_rate,
            'transaction_date' => Carbon::now()->toDateString(),
            'items' => [
                '1FPS7621' => [
                    'id' => 8,
                    'quantity' => 3,
                    'price' => 2000,
                ],
                '1QJI9673' => [
                    'id' => 13,
                    'quantity' => 4,
                    'price' => 3500,
                ],
            ],
        ];

        $items = collect($payload['items']);
        $amount = $items->sum(fn($item) => $item['quantity'] * $item['price']);
        $payload['amount'] = $payload['paid'] = $amount;

        $url = "{$this->base_url}/sales";
        $this->actingAs($this->user, 'tenant');
        $response = $this->post($url, $payload, ['Accept' => 'application/json']);
        $response->assertCreated();

        $sale = Sale::query()->latest('id')->first();

        $this->assertEquals($amount, $sale->amount);
        $this->assertEquals($items->count(), $sale->sale_items->count());
        $debit_record = $sale->journal_entry->line_items()->sum('debit_record');
        $credit_record = $sale->journal_entry->line_items()->sum('credit_record');
        $this->assertTrue($debit_record > 0);
        $this->assertTrue($credit_record > 0);
        $this->assertEquals($debit_record, $credit_record);
        $this->assertEquals($amount, $credit_record);
        Event::assertDispatched(SaleCompleted::class);
    }

    public function test_discounted_and_taxed_sale_can_be_created_by_authenticated_users() {
        Event::fake();
        $vat_rate = 15; // Charged 15% on tax
        $discount_rate = 3; // Charged 3% on gross amount

        $items = collect([
            '1FPS7621' => [
                'id' => 8,
                'quantity' => 3,
                'price' => 2000,
            ],
            '1QJI9673' => [
                'id' => 13,
                'quantity' => 2,
                'price' => 3300,
            ],
        ]);

        $amount = $items->sum(fn($item) => $item['quantity'] * $item['price']);
        $netAmount = $amount - ceil($amount * $discount_rate / 100) + ceil($amount * $vat_rate / 100);
        $paid = ceil($netAmount * 60 / 100); // Paid 60%

        $payload = [
            'branch_id' => $this->branch->id,
            'customer_id' => $this->customer->id,
            'paid' => $paid,
            'vat' => $vat_rate,
            'discount' => $discount_rate,
            'transaction_date' => Carbon::now()->toDateString(),
            'items' => $items->all(),
        ];

        $url = "{$this->base_url}/sales";
        $this->actingAs($this->user, 'tenant');
        $response = $this->post($url, $payload, ['Accept' => 'application/json']);
        $response->assertCreated();

        $sale = Sale::query()->latest('id')->first();

        $this->assertEquals($amount, $sale->amount);
        $this->assertEquals($vat_rate, $sale->vat);
        $debit_record = $sale->journal_entry->line_items()->sum('debit_record');
        $credit_record = $sale->journal_entry->line_items()->sum('credit_record');
        $this->assertTrue($debit_record > 0);
        $this->assertTrue($credit_record > 0);
        $this->assertEquals($debit_record, $credit_record);
        Event::assertDispatched(SaleCompleted::class);
    }

}
