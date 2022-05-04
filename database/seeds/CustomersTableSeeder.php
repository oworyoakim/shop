<?php
namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomersTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Customer::query()->truncate();
        Customer::create([
            'name' => 'Walk-In Customer',
            'phone' => '+2567xxxxxxxx',
        ]);
    }

}


