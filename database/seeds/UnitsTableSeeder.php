<?php

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitsTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Unit::query()->truncate();
        Unit::create([
            'title' => 'Kilograms',
            'slug' => 'kgs',
            'description' => 'Kilograms (1000 grams)',
        ]);
        Unit::create([
            'title' => 'Pieces',
            'slug' => 'pcs',
            'description' => 'Pieces',
        ]);
        Unit::create([
            'title' => 'Packets',
            'slug' => 'pkts',
            'description' => 'Packets',
        ]);
        Unit::create([
            'title' => 'Litres',
            'slug' => 'ltrs',
            'description' => 'Litres',
        ]);
        Unit::create([
            'title' => 'Bars',
            'slug' => 'bars',
            'description' => 'Bars',
        ]);
        Unit::create([

            'title' => 'Crates',
            'slug' => 'crts',
            'description' => 'Crates',
        ]);
        Unit::create([
            'title' => 'Bottles',
            'slug' => 'btls',
            'description' => 'Bottles',
        ]);
    }

}


