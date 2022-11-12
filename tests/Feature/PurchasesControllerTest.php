<?php

namespace Tests\Feature;

use App\Models\Landlord\Tenant;
use App\Models\Tenant\Branch;
use App\Models\Tenant\User;
use App\Traits\Tenant\UsesLoggedInUser;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;

class PurchasesControllerTest extends \Tests\TestCase
{
    use DatabaseTransactions, WithFaker, UsesLoggedInUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tenant = Tenant::where('subdomain', 'test')->first();
        $this->user = User::where(['tenant_id' => $this->tenant->id, 'username' => $this->tenant->subdomain])->first();
        $this->branch = Branch::updateOrCreate(['tenant_id' => $this->tenant->id, 'name' => $this->faker->company])->first();
        $this->base_url = "http://{$this->tenant->subdomain}.". config('app.url_base') . '/v1';
    }

}
