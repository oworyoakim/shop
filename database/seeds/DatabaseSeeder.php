<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        echo("\nTo seed for development type: \n     php artisan db:seed --class=DevDatabaseSeeder \n");
        echo("\nTo seed for production type: \n     php artisan db:seed --class=ProductionDatabaseSeeder \n\n");
    }
}
