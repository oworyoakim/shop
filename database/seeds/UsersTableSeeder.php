<?php

use App\Models\Role;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminCredentials = [
            "email" => 'admin@shop.kim',
            "username" => 'admin',
            "password" => 'admin',
            "first_name" => 'Admin',
            "last_name" => 'Admin',
            "avatar" => '/images/avatar.png',
            "password_last_changed" => Carbon::now(),
        ];
        if ($adminRole = Role::where('slug', 'admin')->first())
        {
            $adminUser = Sentinel::registerAndActivate($adminCredentials);
            $adminUser->roles()->attach($adminRole);
        }
    }
}

