<?php

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::query()->truncate();

        Role::create([
            'slug' => 'admin',
            'name' => 'Administrator',
            'permissions' => [
                "admin.dashboard" => true,
                "settings" => true,
                "users" => true,
                "users.create" => true,
                "users.update" => true,
                "users.delete" => true,
                "users.roles" => true,
                "branches" => true,
                "branches.create" => true,
                "branches.update" => true,
                "branches.delete" => true,
            ]
        ]);


        Role::create([
            'slug' => 'manager',
            'name' => 'Branch Manager',
            'permissions' => [
                "manager.dashboard" => true,
                "sales" => true,
                "expenses" => true,
                "stocks" => true,
                "users" => true,
            ]
        ]);

        Role::create([
            'slug' => 'cashier',
            'name' => 'Branch cashier',
            'permissions' => [
                "cashier.dashboard" => true,
                "sales" => true,
            ]
        ]);
    }

}
