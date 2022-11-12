<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DevDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AdminTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);
        $this->call(UnitsTableSeeder::class);
        $this->call(TenantTableSeeder::class);
    }
}
