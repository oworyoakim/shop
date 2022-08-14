<?php

namespace Database\Seeders;

use App\Models\Landlord\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Admin::query()->updateOrCreate([
            'username' => 'admin',
        ], [
            'first_name' => 'Owor',
            'last_name' => 'Yoakim',
            'email' => 'admin@shop.test',
            'password' => Hash::make('admin'),
            'group' => Admin::GROUP_ADMINISTRATORS,
            'permissions' => []
        ]);
    }
}
