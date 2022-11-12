<?php

namespace Database\Seeders;

use App\Models\Landlord\Tenant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GeneralLedgerAccountsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (Tenant::all() as $tenant) {
            $tenant->seedGeneralLedgerAccounts();
        }
    }
}
