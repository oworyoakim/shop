<?php

use App\Models\Tenant\Supplier;
use Illuminate\Database\Seeder;

class SuppliersTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Supplier::query()->truncate();
        Supplier::create([
            'name' => 'Supplier 1',
            'phone' => '+256770000000',
            'email' => 'supplier@shop.kim',
            'address' => 'Kikuubo',
            'city' => 'Kampala',
            'country' => 'Uganda',
        ]);
    }

}


