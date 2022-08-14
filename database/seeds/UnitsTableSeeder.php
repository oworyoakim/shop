<?php
namespace Database\Seeders;

use App\Models\Tenant\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class UnitsTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $units = [
            [
                'title' => 'Grams',
                'slug' => 'grm',
                'description' => 'Grams',
            ],
            [
                'title' => 'Kilograms',
                'slug' => 'kgs',
                'description' => 'Kilograms (1000 grams)',
            ],
            [
                'title' => 'Pieces',
                'slug' => 'pcs',
                'description' => 'Pieces',
            ],
            [
                'title' => 'Packets',
                'slug' => 'pkts',
                'description' => 'Packets',
            ],
            [
                'title' => 'Litres',
                'slug' => 'ltrs',
                'description' => 'Litres',
            ],
            [
                'title' => 'Bars',
                'slug' => 'bars',
                'description' => 'Bars',
            ],
            [
                'title' => 'Crates',
                'slug' => 'crts',
                'description' => 'Crates',
            ],
            [
                'title' => 'Bottles',
                'slug' => 'btls',
                'description' => 'Bottles',
            ],
            [
                'title' => 'Bags',
                'slug' => 'bags',
                'description' => 'Bags',
            ],
            [
                'title' => 'Meters',
                'slug' => 'mtrs',
                'description' => 'Meters',
            ],
            [
                'title' => 'Foots',
                'slug' => 'fts',
                'description' => 'Foots',
            ],
            [
                'title' => 'Yards',
                'slug' => 'yrds',
                'description' => 'Yards',
            ]
        ];

        foreach ($units as $unit) {
            Unit::query()->updateOrCreate(Arr::only($unit, 'slug'), Arr::except($unit, 'slug'));
        }
    }

}


