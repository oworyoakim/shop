<?php

namespace Tests\Feature;

use App\Events\PurchasePaymentMade;
use App\Models\Landlord\Tenant;
use App\Models\Tenant\Branch;
use App\Models\Tenant\Purchase;
use App\Models\Tenant\PurchasePayment;
use App\Models\Tenant\Supplier;
use App\Models\Tenant\User;
use App\Traits\Tenant\UsesLoggedInUser;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class PurchasePaymentsControllerTest extends TestCase
{
    use DatabaseTransactions, WithFaker, UsesLoggedInUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tenant = Tenant::where('subdomain', 'test')->first();
        $this->user = User::where(['tenant_id' => $this->tenant->id, 'username' => $this->tenant->subdomain])->first();
        $this->branch = Branch::updateOrCreate(['tenant_id' => $this->tenant->id, 'name' => $this->faker->company])->first();
        $this->supplier = Supplier::updateOrCreate(['tenant_id' => $this->tenant->id, 'name' => $this->faker->company])->first();
        $this->amount = $this->faker->numberBetween(100000, 500000);
        $this->base_url = "http://{$this->tenant->subdomain}.". config('app.url_base') . '/v1';
    }

    public function test_purchase_payment_can_be_made_by_authenticated_users() {
        Event::fake();
        // Make purchase with partial payment
        $paid = round($this->amount * 60 / 100); // Paid 60%
        $payload = [
            'branch_id' => $this->branch->id,
            'supplier_id' => $this->supplier->id,
            'amount' => $this->amount,
            'paid' => $paid,
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

        $this->actingAs($this->user, 'tenant');

        $url = "{$this->base_url}/purchases";
        $response = $this->post($url, $payload, ['Accept' => 'application/json']);
        $response->assertCreated();

        $purchase = Purchase::query()
                            ->whereIn('payment_status', [
                                Purchase::PAYMENT_STATUS_PARTIAL,
                                Purchase::PAYMENT_STATUS_PENDING
                            ])
                            ->latest('id')
                            ->first();

        $this->assertNotNull($purchase);

        $amount = round($this->amount * 20 / 100);

        $payload = [
            'amount' => $amount,
            'transaction_date' => Carbon::now()->toDateString(),
        ];
        $url = "{$this->base_url}/purchases/{$purchase->id}/pay";
        $response = $this->put($url, $payload);

        $response->assertStatus(Response::HTTP_ACCEPTED);

        $payment = PurchasePayment::query()->latest('id')->first();
        $this->assertNotNull($payment);

        $this->assertEquals($purchase->id, $payment->purchase_id);
        $this->assertEquals($amount, $payment->amount);
        $debit_record = $payment->journal_entry->line_items()->sum('debit_record');
        $credit_record = $payment->journal_entry->line_items()->sum('credit_record');
        $this->assertTrue($debit_record > 0);
        $this->assertTrue($credit_record > 0);
        $this->assertEquals($debit_record, $credit_record);
        $this->assertEquals($amount, $credit_record);
        Event::assertDispatched(PurchasePaymentMade::class);
    }
}
