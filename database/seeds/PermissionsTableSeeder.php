<?php

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permissions')->truncate();
        // settings
        Permission::create([
            'slug' => 'settings',
            'title' => 'Settings',
            'description' => 'Manage Settings',
        ]);

        // Admin Dashboard
        Permission::create([
            'slug' => 'admin.dashboard',
            'title' => 'Admin Dashboard',
            'description' => 'Admin Dashboard',
        ]);

        // Items Permissions
        if ($permission = Permission::create([
            'slug' => 'items',
            'title' => 'Items',
            'description' => 'Manage Items',
        ]))
        {
            Permission::create([
                'slug' => 'items.create',
                'title' => 'Create items',
                'description' => 'Create items',
                'parent_id' => $permission->id,
            ]);
            Permission::create([
                'slug' => 'items.update',
                'title' => 'Update items',
                'description' => 'Update items',
                'parent_id' => $permission->id,
            ]);
            Permission::create([
                'slug' => 'items.delete',
                'title' => 'Delete items',
                'description' => 'Delete items',
                'parent_id' => $permission->id,
            ]);
            Permission::create([
                'slug' => 'items.print_barcode',
                'title' => 'Print item barcode',
                'description' => 'Print item barcode',
                'parent_id' => $permission->id,
            ]);
            Permission::create([
                'slug' => 'items.units',
                'title' => 'Manage item units',
                'description' => 'Manage item units',
                'parent_id' => $permission->id,
            ]);
        }
        // Categories Permissions
        if ($permission = Permission::create([
            'slug' => 'categories',
            'title' => 'Categories',
            'description' => 'Manage Categories',
        ]))
        {
            Permission::create([
                'slug' => 'categories.create',
                'title' => 'Create categories',
                'description' => 'Create categories',
                'parent_id' => $permission->id,
            ]);
            Permission::create([
                'slug' => 'categories.update',
                'title' => 'Update categories',
                'description' => 'Update categories',
                'parent_id' => $permission->id,
            ]);
            Permission::create([
                'slug' => 'categories.delete',
                'title' => 'Delete categories',
                'description' => 'Delete categories',
                'parent_id' => $permission->id,
            ]);
        }
        // Expenses Permissions
        if ($permission = Permission::create([
            'slug' => 'expenses',
            'title' => 'Expenses',
            'description' => 'Manage Expenses',
        ]))
        {
            Permission::create([
                'slug' => 'expenses.create',
                'title' => 'Create expenses',
                'description' => 'Create expenses',
                'parent_id' => $permission->id,
            ]);
            Permission::create([
                'slug' => 'expenses.update',
                'title' => 'Update expenses',
                'description' => 'Update expenses',
                'parent_id' => $permission->id,
            ]);
            Permission::create([
                'slug' => 'expenses.delete',
                'title' => 'Delete expenses',
                'description' => 'Delete expenses',
                'parent_id' => $permission->id,
            ]);
        }
        // Users Permissions
        if ($permission = Permission::create([
            'slug' => 'users',
            'title' => 'Users',
            'description' => 'Manage Users',
        ]))
        {
            Permission::create([
                'slug' => 'users.create',
                'title' => 'Create users',
                'description' => 'Create users',
                'parent_id' => $permission->id,
            ]);
            Permission::create([
                'slug' => 'users.update',
                'title' => 'Update users',
                'description' => 'Update users',
                'parent_id' => $permission->id,
            ]);
            Permission::create([
                'slug' => 'users.delete',
                'title' => 'Delete users',
                'description' => 'Delete users',
                'parent_id' => $permission->id,
            ]);
            Permission::create([
                'slug' => 'users.lock',
                'title' => 'Lock users',
                'description' => 'Lock users',
                'parent_id' => $permission->id,
            ]);

            Permission::create([
                'slug' => 'users.unlock',
                'title' => 'Unlock users',
                'description' => 'Unlock users',
                'parent_id' => $permission->id,
            ]);

            Permission::create([
                'slug' => 'users.roles',
                'title' => 'Manage user roles',
                'description' => 'Manage user roles',
                'parent_id' => $permission->id,
            ]);
        }
        // Branches Permissions
        if ($permission = Permission::create([
            'slug' => 'branches',
            'title' => 'Branches',
            'description' => 'Manage Branches',
        ]))
        {
            Permission::create([
                'slug' => 'branches.create',
                'title' => 'Create branches',
                'description' => 'Create branches',
                'parent_id' => $permission->id,
            ]);
            Permission::create([
                'slug' => 'branches.update',
                'title' => 'Update branches',
                'description' => 'Update branches',
                'parent_id' => $permission->id,
            ]);
            Permission::create([
                'slug' => 'branches.delete',
                'title' => 'Delete branches',
                'description' => 'Delete branches',
                'parent_id' => $permission->id,
            ]);
            Permission::create([
                'slug' => 'branches.lock',
                'title' => 'Lock branches',
                'description' => 'Lock branches',
                'parent_id' => $permission->id,
            ]);
            Permission::create([
                'slug' => 'branches.unlock',
                'title' => 'Unlock branches',
                'description' => 'Unlock branches',
                'parent_id' => $permission->id,
            ]);
        }

        // Stocks Permissions
        if ($permission = Permission::create([
            'slug' => 'stocks',
            'title' => 'Stocks',
            'description' => 'Manage Stocks',
        ]))
        {
            Permission::create([
                'slug' => 'stocks.view',
                'title' => 'View stocks',
                'description' => 'View stocks',
                'parent_id' => $permission->id,
            ]);
            Permission::create([
                'slug' => 'stocks.adjust',
                'title' => 'Adjust stocks',
                'description' => 'Adjust stocks',
                'parent_id' => $permission->id,
            ]);
            Permission::create([
                'slug' => 'stocks.transfer',
                'title' => 'Transfer stocks',
                'description' => 'Transfer stocks',
                'parent_id' => $permission->id,
            ]);
        }

        // Customers Permissions
        if ($permission = Permission::create([
            'slug' => 'customers',
            'title' => 'Customers',
            'description' => 'Manage Customers',
        ]))
        {
            Permission::create([
                'slug' => 'customers.create',
                'title' => 'Create customers',
                'description' => 'Create customers',
                'parent_id' => $permission->id,
            ]);
            Permission::create([
                'slug' => 'customers.update',
                'title' => 'Update customers',
                'description' => 'Update customers',
                'parent_id' => $permission->id,
            ]);
            Permission::create([
                'slug' => 'customers.delete',
                'title' => 'Delete customers',
                'description' => 'Delete customers',
                'parent_id' => $permission->id,
            ]);
        }

        // Suppliers Permissions
        if ($permission = Permission::create([
            'slug' => 'suppliers',
            'title' => 'Suppliers',
            'description' => 'Manage Suppliers',
        ]))
        {
            Permission::create([
                'slug' => 'suppliers.create',
                'title' => 'Create suppliers',
                'description' => 'Create suppliers',
                'parent_id' => $permission->id,
            ]);
            Permission::create([
                'slug' => 'suppliers.update',
                'title' => 'Update suppliers',
                'description' => 'Update suppliers',
                'parent_id' => $permission->id,
            ]);
            Permission::create([
                'slug' => 'suppliers.delete',
                'title' => 'Delete suppliers',
                'description' => 'Delete suppliers',
                'parent_id' => $permission->id,
            ]);
        }

        // Sales Permissions
        if ($permission = Permission::create([
            'slug' => 'sales',
            'title' => 'Sales',
            'description' => 'Manage Sales',
        ]))
        {
            Permission::create([
                'slug' => 'sales.create',
                'title' => 'Create sales',
                'description' => 'Create sales',
                'parent_id' => $permission->id,
            ]);
            Permission::create([
                'slug' => 'sales.update',
                'title' => 'Update sales',
                'description' => 'Update sales',
                'parent_id' => $permission->id,
            ]);
            Permission::create([
                'slug' => 'sales.delete',
                'title' => 'Delete sales',
                'description' => 'Delete sales',
                'parent_id' => $permission->id,
            ]);
            Permission::create([
                'slug' => 'sales.cancel',
                'title' => 'Cancel sales',
                'description' => 'Cancel sales',
                'parent_id' => $permission->id,
            ]);
            Permission::create([
                'slug' => 'sales.refund',
                'title' => 'Refund sales',
                'description' => 'Refund sales',
                'parent_id' => $permission->id,
            ]);
            Permission::create([
                'slug' => 'sales.block',
                'title' => 'Block sales',
                'description' => 'Block sales',
                'parent_id' => $permission->id,
            ]);

            Permission::create([
                'slug' => 'sales.receivables',
                'title' => 'Manage sales receivables',
                'description' => 'Manage sales receivables',
                'parent_id' => $permission->id,
            ]);
        }

        // Purchases Permissions
        if ($permission = Permission::create([
            'slug' => 'purchases',
            'title' => 'Purchases',
            'description' => 'Manage Purchases',
        ]))
        {
            Permission::create([
                'slug' => 'purchases.create',
                'title' => 'Create purchases',
                'description' => 'Create purchases',
                'parent_id' => $permission->id,
            ]);
            Permission::create([
                'slug' => 'purchases.update',
                'title' => 'Update purchases',
                'description' => 'Update purchases',
                'parent_id' => $permission->id,
            ]);
            Permission::create([
                'slug' => 'purchases.delete',
                'title' => 'Delete purchases',
                'description' => 'Delete purchases',
                'parent_id' => $permission->id,
            ]);
            Permission::create([
                'slug' => 'purchases.cancel',
                'title' => 'Cancel purchases',
                'description' => 'Cancel purchases',
                'parent_id' => $permission->id,
            ]);
            Permission::create([
                'slug' => 'purchases.refund',
                'title' => 'Refund purchases',
                'description' => 'Refund purchases',
                'parent_id' => $permission->id,
            ]);
            Permission::create([
                'slug' => 'purchases.block',
                'title' => 'Block purchases',
                'description' => 'Block purchases',
                'parent_id' => $permission->id,
            ]);

            Permission::create([
                'slug' => 'purchases.payables',
                'title' => 'Manage purchases payables',
                'description' => 'Manage purchases payables',
                'parent_id' => $permission->id,
            ]);
        }

        // Purchase Orders Permissions
        if ($permission = Permission::create([
            'slug' => 'purchase_orders',
            'title' => 'Purchase Orders',
            'description' => 'Manage Purchase Orders',
        ]))
        {
            Permission::create([
                'slug' => 'purchase_orders.create',
                'title' => 'Create purchase orders',
                'description' => 'Create purchase orders',
                'parent_id' => $permission->id,
            ]);
            Permission::create([
                'slug' => 'purchase_orders.update',
                'title' => 'Update purchase orders',
                'description' => 'Update purchase orders',
                'parent_id' => $permission->id,
            ]);
            Permission::create([
                'slug' => 'purchase_orders.delete',
                'title' => 'Delete purchase orders',
                'description' => 'Delete purchase orders',
                'parent_id' => $permission->id,
            ]);
            Permission::create([
                'slug' => 'purchase_orders.cancel',
                'title' => 'Cancel purchase orders',
                'description' => 'Cancel purchase orders',
                'parent_id' => $permission->id,
            ]);
        }

        // Reports Permissions
        if ($permission = Permission::create([
            'slug' => 'reports',
            'title' => 'Reports',
            'description' => 'View Reports',
        ]))
        {
            Permission::create([
                'slug' => 'reports.sales',
                'title' => 'View sales reports',
                'description' => 'View sales reports',
                'parent_id' => $permission->id,
            ]);

            Permission::create([
                'slug' => 'reports.purchases',
                'title' => 'View purchases reports',
                'description' => 'View purchases reports',
                'parent_id' => $permission->id,
            ]);

            Permission::create([
                'slug' => 'reports.receivables',
                'title' => 'View receivables reports',
                'description' => 'View receivables reports',
                'parent_id' => $permission->id,
            ]);

            Permission::create([
                'slug' => 'reports.payables',
                'title' => 'View payables reports',
                'description' => 'View payables reports',
                'parent_id' => $permission->id,
            ]);

            Permission::create([
                'slug' => 'reports.income',
                'title' => 'Income Statement',
                'description' => 'View Income Statement reports',
                'parent_id' => $permission->id,
            ]);
            Permission::create([
                'slug' => 'reports.balance_sheet',
                'title' => 'Balance Sheet',
                'description' => 'View Balance Sheet reports',
                'parent_id' => $permission->id,
            ]);
        }
    }

}
