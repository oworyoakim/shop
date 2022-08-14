<?php

namespace Database\Seeders;

use App\ShopHelper;
use Illuminate\Database\Seeder;

class TenantTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'name' => 'Test Supermarket',
            'subdomain' => 'test',
            'email' => 'test@shop.test',
            'phone' => '+2567770000000',
            'address' => 'Kampala',
            'country' => 'Uganda',
            'city' => 'Kampala',
            'website' => null,
            'password' => 'password',
        ];
        $tenant = ShopHelper::createTenant($data);
    }
}
