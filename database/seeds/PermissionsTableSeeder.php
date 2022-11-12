<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seedLandlordPermissions();
        $this->seedTenantPermissions();
    }

    private function seedLandlordPermissions()
    {
        Permission::updateOrCreate([
            'slug' => 'admin.dashboard',
            'category' => Permission::CATEGORY_LANDLORD,
        ], [
            'title' => 'Admin Dashboard',
            'description' => 'Admin dashboard',
        ]);
        Permission::updateOrCreate([
            'slug' => 'admin.tenant.statistics',
            'category' => Permission::CATEGORY_LANDLORD,
        ], [
            'title' => 'Tenant Statistics',
            'description' => 'Tenant Statistics',
        ]);
        Permission::updateOrCreate([
            'slug' => 'settings',
            'category' => Permission::CATEGORY_LANDLORD,
        ], [
            'title' => 'Settings',
            'description' => 'Manage settings',
        ]);

        // Tenants
        if ($parent = Permission::updateOrCreate([
            'slug' => 'tenants',
            'category' => Permission::CATEGORY_LANDLORD,
        ], [
            'title' => 'Tenants',
            'description' => 'Manage tenants',
        ]))
        {
            Permission::updateOrCreate([
                'slug' => 'tenants.view',
                'category' => Permission::CATEGORY_LANDLORD,
            ], [
                'title' => 'View tenants',
                'description' => 'View tenants',
                'parent_id' => $parent->id
            ]);
            Permission::updateOrCreate([
                'slug' => 'tenants.create',
                'category' => Permission::CATEGORY_LANDLORD,
            ], [
                'title' => 'Create tenants',
                'description' => 'Create tenants',
                'parent_id' => $parent->id
            ]);
            Permission::updateOrCreate([
                'slug' => 'tenants.update',
                'category' => Permission::CATEGORY_LANDLORD,
            ], [
                'title' => 'Update tenants',
                'description' => 'Update tenants',
                'parent_id' => $parent->id
            ]);
            Permission::updateOrCreate([
                'slug' => 'tenants.block',
                'category' => Permission::CATEGORY_LANDLORD,
            ], [
                'title' => 'Block tenants',
                'description' => 'Block tenants',
                'parent_id' => $parent->id
            ]);
            Permission::updateOrCreate([
                'slug' => 'tenants.unblock',
                'category' => Permission::CATEGORY_LANDLORD,
            ], [
                'title' => 'Unblock tenants',
                'description' => 'Unblock tenants',
                'parent_id' => $parent->id
            ]);
            Permission::updateOrCreate([
                'slug' => 'tenants.show',
                'category' => Permission::CATEGORY_LANDLORD,
            ], [
                'title' => 'Show tenants',
                'description' => 'Show tenants',
                'parent_id' => $parent->id
            ]);
        }

        // Manage units
        if ($permission = Permission::updateOrCreate([
            'slug' => 'units',
            'title' => 'Units',
            'description' => 'Manage Units',
        ]))
        {
            Permission::updateOrCreate([
                'slug' => 'units.create',
                'title' => 'Create units',
                'description' => 'Create units',
                'parent_id' => $permission->id,
            ]);
            Permission::updateOrCreate([
                'slug' => 'units.update',
                'title' => 'Update units',
                'description' => 'Update units',
                'parent_id' => $permission->id,
            ]);
        }

        // Manage users
        if ($parent = Permission::updateOrCreate([
            'slug' => 'users',
            'category' => Permission::CATEGORY_LANDLORD,
        ], [
            'title' => 'Users',
            'description' => 'Manage users',
        ]))
        {
            Permission::updateOrCreate([
                'slug' => 'users.view',
                'category' => Permission::CATEGORY_LANDLORD,
            ], [
                'title' => 'View users',
                'description' => 'View users',
                'parent_id' => $parent->id
            ]);
            Permission::updateOrCreate([
                'slug' => 'users.create',
                'category' => Permission::CATEGORY_LANDLORD,
            ], [
                'title' => 'Create users',
                'description' => 'Create users',
                'parent_id' => $parent->id
            ]);
            Permission::updateOrCreate([
                'slug' => 'users.update',
                'category' => Permission::CATEGORY_LANDLORD,
            ], [
                'title' => 'Update users',
                'description' => 'Update users',
                'parent_id' => $parent->id
            ]);
            Permission::updateOrCreate([
                'slug' => 'users.delete',
                'category' => Permission::CATEGORY_LANDLORD,
            ], [
                'title' => 'Delete users',
                'description' => 'Delete users',
                'parent_id' => $parent->id
            ]);
            Permission::updateOrCreate([
                'slug' => 'users.block',
                'category' => Permission::CATEGORY_LANDLORD,
            ], [
                'title' => 'Block users',
                'description' => 'Block users',
                'parent_id' => $parent->id
            ]);
            Permission::updateOrCreate([
                'slug' => 'users.unblock',
                'category' => Permission::CATEGORY_LANDLORD,
            ], [
                'title' => 'Unblock users',
                'description' => 'Unblock users',
                'parent_id' => $parent->id
            ]);
            Permission::updateOrCreate([
                'slug' => 'users.show',
                'category' => Permission::CATEGORY_LANDLORD,
            ], [
                'title' => 'Show users',
                'description' => 'Show users',
                'parent_id' => $parent->id
            ]);
            Permission::updateOrCreate([
                'slug' => 'users.permissions',
                'category' => Permission::CATEGORY_LANDLORD,
            ], [
                'title' => 'Manage permissions',
                'description' => 'Manage permissions',
                'parent_id' => $parent->id
            ]);
            Permission::updateOrCreate([
                'slug' => 'users.permissions.own',
                'category' => Permission::CATEGORY_LANDLORD,
            ], [
                'title' => 'Manage own permissions',
                'description' => 'Manage own permissions',
                'parent_id' => $parent->id
            ]);
        }
    }

    private function seedTenantPermissions()
    {
        // settings
        Permission::query()->updateOrCreate([
            'slug' => 'tenant.settings',
            'category' => Permission::CATEGORY_TENANT,
        ], [
            'title' => 'Settings',
            'description' => 'Manage Settings',
        ]);

        // Admin Dashboard
        Permission::updateOrCreate([
            'slug' => 'tenant.admin.dashboard',
            'category' => Permission::CATEGORY_TENANT,
        ], [
            'title' => 'Admin Dashboard',
            'description' => 'Admin Dashboard',
        ]);

        // Items Permissions
        if ($permission = Permission::updateOrCreate([
            'slug' => 'tenant.items',
            'category' => Permission::CATEGORY_TENANT,
            ],[
        'title' => 'Items',
        'description' => 'Manage Items',
    ]))
        {
            Permission::updateOrCreate([
                'slug' => 'tenant.items.create',
                'category' => Permission::CATEGORY_TENANT,
            ], [
                'title' => 'Create items',
                'description' => 'Create items',
                'parent_id' => $permission->id,
            ]);
            Permission::updateOrCreate([
                'slug' => 'tenant.items.update',
                'category' => Permission::CATEGORY_TENANT,
            ], [
                'title' => 'Update items',
                'description' => 'Update items',
                'parent_id' => $permission->id,
            ]);
            Permission::updateOrCreate([
                'slug' => 'tenant.items.delete',
                'category' => Permission::CATEGORY_TENANT,
            ], [
                'title' => 'Delete items',
                'description' => 'Delete items',
                'parent_id' => $permission->id,
            ]);
            Permission::updateOrCreate([
                'slug' => 'tenant.items.print_barcode',
                'category' => Permission::CATEGORY_TENANT,
            ], [
                'title' => 'Print item barcode',
                'description' => 'Print item barcode',
                'parent_id' => $permission->id,
            ]);
            Permission::updateOrCreate([
                'slug' => 'tenant.items.units',
                'category' => Permission::CATEGORY_TENANT,
            ], [
                'title' => 'Manage item units',
                'description' => 'Manage item units',
                'parent_id' => $permission->id,
            ]);
        }

        // Categories Permissions
        if ($permission = Permission::updateOrCreate([
            'slug' => 'tenant.categories',
            'category' => Permission::CATEGORY_TENANT,
            ], [
            'title' => 'Categories',
            'description' => 'Manage Categories',
        ]))
        {
            Permission::updateOrCreate([
                'slug' => 'tenant.categories.create',
                'category' => Permission::CATEGORY_TENANT,
                ], [
                'title' => 'Create categories',
                'description' => 'Create categories',
                'parent_id' => $permission->id,
            ]);
            Permission::updateOrCreate([
                'slug' => 'tenant.categories.update',
                'category' => Permission::CATEGORY_TENANT,
                ], [
                'title' => 'Update categories',
                'description' => 'Update categories',
                'parent_id' => $permission->id,
            ]);
            Permission::updateOrCreate([
                'slug' => 'tenant.categories.delete',
                'category' => Permission::CATEGORY_TENANT,
                ], [
                'title' => 'Delete categories',
                'description' => 'Delete categories',
                'parent_id' => $permission->id,
            ]);
        }

        // Expenses Permissions
        if ($permission = Permission::updateOrCreate([
            'slug' => 'tenant.expenses',
            'category' => Permission::CATEGORY_TENANT,
            ], [
            'title' => 'Expenses',
            'description' => 'Manage Expenses',
        ]))
        {
            Permission::updateOrCreate([
                'slug' => 'tenant.expenses.create',
                'category' => Permission::CATEGORY_TENANT,
                ], [
                'title' => 'Create expenses',
                'description' => 'Create expenses',
                'parent_id' => $permission->id,
            ]);
            Permission::updateOrCreate([
                'slug' => 'tenant.expenses.update',
                'category' => Permission::CATEGORY_TENANT,
                ], [
                'title' => 'Update expenses',
                'description' => 'Update expenses',
                'parent_id' => $permission->id,
            ]);
            Permission::updateOrCreate([
                'slug' => 'tenant.expenses.delete',
                'category' => Permission::CATEGORY_TENANT,
                ], [
                'title' => 'Delete expenses',
                'description' => 'Delete expenses',
                'parent_id' => $permission->id,
            ]);
            Permission::updateOrCreate([
                'slug' => 'tenant.expenses.approve',
                'category' => Permission::CATEGORY_TENANT,
                ], [
                'title' => 'Approve expenses',
                'description' => 'Approve expenses',
                'parent_id' => $permission->id,
            ]);
        }

        // Users Permissions
        if ($permission = Permission::updateOrCreate([
            'slug' => 'tenant.users',
            'category' => Permission::CATEGORY_TENANT,
            ], [
            'title' => 'Users',
            'description' => 'Manage Users',
        ]))
        {
            Permission::updateOrCreate([
                'slug' => 'tenant.users.create',
                'category' => Permission::CATEGORY_TENANT,
                ], [
                'title' => 'Create users',
                'description' => 'Create users',
                'parent_id' => $permission->id,
            ]);
            Permission::updateOrCreate([
                'slug' => 'tenant.users.update',
                'category' => Permission::CATEGORY_TENANT,
                ], [
                'title' => 'Update users',
                'description' => 'Update users',
                'parent_id' => $permission->id,
            ]);
            Permission::updateOrCreate([
                'slug' => 'tenant.users.delete',
                'category' => Permission::CATEGORY_TENANT,
                ], [
                'title' => 'Delete users',
                'description' => 'Delete users',
                'parent_id' => $permission->id,
            ]);
            Permission::updateOrCreate([
                'slug' => 'tenant.users.lock',
                'category' => Permission::CATEGORY_TENANT,
                ], [
                'title' => 'Lock users',
                'description' => 'Lock users',
                'parent_id' => $permission->id,
            ]);

            Permission::updateOrCreate([
                'slug' => 'tenant.users.unlock',
                'category' => Permission::CATEGORY_TENANT,
                ], [
                'title' => 'Unlock users',
                'description' => 'Unlock users',
                'parent_id' => $permission->id,
            ]);

            Permission::updateOrCreate([
                'slug' => 'tenant.users.permissions',
                'category' => Permission::CATEGORY_TENANT,
                ], [
                'title' => 'Manage user permissions',
                'description' => 'Manage user permissions',
                'parent_id' => $permission->id,
            ]);

            Permission::updateOrCreate([
                'slug' => 'tenant.users.own_permissions',
                'category' => Permission::CATEGORY_TENANT,
                ], [
                'title' => 'Manage own permissions',
                'description' => 'Manage own permissions',
                'parent_id' => $permission->id,
            ]);
        }

        // Branches Permissions
        if ($permission = Permission::updateOrCreate([
            'slug' => 'tenant.branches',
            'category' => Permission::CATEGORY_TENANT,
            ], [
            'title' => 'Branches',
            'description' => 'Manage Branches',
        ]))
        {
            Permission::updateOrCreate([
                'slug' => 'tenant.branches.create',
                'category' => Permission::CATEGORY_TENANT,
                ], [
                'title' => 'Create branches',
                'description' => 'Create branches',
                'parent_id' => $permission->id,
            ]);
            Permission::updateOrCreate([
                'slug' => 'tenant.branches.update',
                'category' => Permission::CATEGORY_TENANT,
                ], [
                'title' => 'Update branches',
                'description' => 'Update branches',
                'parent_id' => $permission->id,
            ]);
            Permission::updateOrCreate([
                'slug' => 'tenant.branches.delete',
                'category' => Permission::CATEGORY_TENANT,
                ], [
                'title' => 'Delete branches',
                'description' => 'Delete branches',
                'parent_id' => $permission->id,
            ]);
            Permission::updateOrCreate([
                'slug' => 'tenant.branches.lock',
                'category' => Permission::CATEGORY_TENANT,
                ], [
                'title' => 'Lock branches',
                'description' => 'Lock branches',
                'parent_id' => $permission->id,
            ]);
            Permission::updateOrCreate([
                'slug' => 'tenant.branches.unlock',
                'category' => Permission::CATEGORY_TENANT,
                ], [
                'title' => 'Unlock branches',
                'description' => 'Unlock branches',
                'parent_id' => $permission->id,
            ]);
        }

        // Stocks Permissions
        if ($permission = Permission::updateOrCreate([
            'slug' => 'tenant.stocks',
            'category' => Permission::CATEGORY_TENANT,
        ], [
            'title' => 'Stocks',
            'description' => 'Manage Stocks',
        ]))
        {
            Permission::updateOrCreate([
                'slug' => 'tenant.stocks.view',
                'category' => Permission::CATEGORY_TENANT,
            ], [
                'title' => 'View stocks',
                'description' => 'View stocks',
                'parent_id' => $permission->id,
            ]);
            Permission::updateOrCreate([
                'slug' => 'tenant.stocks.adjust',
                'category' => Permission::CATEGORY_TENANT,
            ], [
                'title' => 'Adjust stocks',
                'description' => 'Adjust stocks',
                'parent_id' => $permission->id,
            ]);
            Permission::updateOrCreate([
                'slug' => 'tenant.stocks.transfer',
                'category' => Permission::CATEGORY_TENANT,
            ], [
                'title' => 'Transfer stocks',
                'description' => 'Transfer stocks',
                'parent_id' => $permission->id,
            ]);
        }

        // Customers Permissions
        if ($permission = Permission::updateOrCreate([
            'slug' => 'tenant.customers',
            'category' => Permission::CATEGORY_TENANT,
        ], [
            'title' => 'Customers',
            'description' => 'Manage Customers',
        ]))
        {
            Permission::updateOrCreate([
                'slug' => 'tenant.customers.create',
                'category' => Permission::CATEGORY_TENANT,
            ], [
                'title' => 'Create customers',
                'description' => 'Create customers',
                'parent_id' => $permission->id,
            ]);
            Permission::updateOrCreate([
                'slug' => 'tenant.customers.update',
                'category' => Permission::CATEGORY_TENANT,
            ], [
                'title' => 'Update customers',
                'description' => 'Update customers',
                'parent_id' => $permission->id,
            ]);
            Permission::updateOrCreate([
                'slug' => 'tenant.customers.delete',
                'category' => Permission::CATEGORY_TENANT,
            ], [
                'title' => 'Delete customers',
                'description' => 'Delete customers',
                'parent_id' => $permission->id,
            ]);
        }

        // Suppliers Permissions
        if ($permission = Permission::updateOrCreate([
            'slug' => 'tenant.suppliers',
            'category' => Permission::CATEGORY_TENANT,
        ], [
            'title' => 'Suppliers',
            'description' => 'Manage Suppliers',
        ]))
        {
            Permission::updateOrCreate([
                'slug' => 'tenant.suppliers.create',
                'category' => Permission::CATEGORY_TENANT,
            ], [
                'title' => 'Create suppliers',
                'description' => 'Create suppliers',
                'parent_id' => $permission->id,
            ]);
            Permission::updateOrCreate([
                'slug' => 'tenant.suppliers.update',
                'category' => Permission::CATEGORY_TENANT,
            ], [
                'title' => 'Update suppliers',
                'description' => 'Update suppliers',
                'parent_id' => $permission->id,
            ]);
            Permission::updateOrCreate([
                'slug' => 'tenant.suppliers.delete',
                'category' => Permission::CATEGORY_TENANT,
            ], [
                'title' => 'Delete suppliers',
                'description' => 'Delete suppliers',
                'parent_id' => $permission->id,
            ]);
        }

        // Sales Permissions
        if ($permission = Permission::updateOrCreate([
            'slug' => 'tenant.sales',
            'category' => Permission::CATEGORY_TENANT,
        ], [
            'title' => 'Sales',
            'description' => 'Manage Sales',
        ]))
        {
            Permission::updateOrCreate([
                'slug' => 'tenant.sales.create',
                'category' => Permission::CATEGORY_TENANT,
            ], [
                'title' => 'Create sales',
                'description' => 'Create sales',
                'parent_id' => $permission->id,
            ]);
            Permission::updateOrCreate([
                'slug' => 'tenant.sales.update',
                'category' => Permission::CATEGORY_TENANT,
            ], [
                'title' => 'Update sales',
                'description' => 'Update sales',
                'parent_id' => $permission->id,
            ]);
            Permission::updateOrCreate([
                'slug' => 'tenant.sales.delete',
                'category' => Permission::CATEGORY_TENANT,
            ], [
                'title' => 'Delete sales',
                'description' => 'Delete sales',
                'parent_id' => $permission->id,
            ]);
            Permission::updateOrCreate([
                'slug' => 'tenant.sales.cancel',
                'category' => Permission::CATEGORY_TENANT,
            ], [
                'title' => 'Cancel sales',
                'description' => 'Cancel sales',
                'parent_id' => $permission->id,
            ]);
            Permission::updateOrCreate([
                'slug' => 'tenant.sales.refund',
                'category' => Permission::CATEGORY_TENANT,
            ], [
                'title' => 'Refund sales',
                'description' => 'Refund sales',
                'parent_id' => $permission->id,
            ]);
            Permission::updateOrCreate([
                'slug' => 'tenant.sales.block',
                'category' => Permission::CATEGORY_TENANT,
            ], [
                'title' => 'Block sales',
                'description' => 'Block sales',
                'parent_id' => $permission->id,
            ]);

            Permission::updateOrCreate([
                'slug' => 'tenant.sales.receivables',
                'category' => Permission::CATEGORY_TENANT,
            ], [
                'title' => 'Manage sales receivables',
                'description' => 'Manage sales receivables',
                'parent_id' => $permission->id,
            ]);
        }

        // Purchases Permissions
        if ($permission = Permission::updateOrCreate([
            'slug' => 'tenant.purchases',
            'category' => Permission::CATEGORY_TENANT,
        ], [
            'title' => 'Purchases',
            'description' => 'Manage Purchases',
        ]))
        {
            Permission::updateOrCreate([
                'slug' => 'tenant.purchases.create',
                'category' => Permission::CATEGORY_TENANT,
            ], [
                'title' => 'Create purchases',
                'description' => 'Create purchases',
                'parent_id' => $permission->id,
            ]);
            Permission::updateOrCreate([
                'slug' => 'tenant.purchases.update',
                'category' => Permission::CATEGORY_TENANT,
            ], [
                'title' => 'Update purchases',
                'description' => 'Update purchases',
                'parent_id' => $permission->id,
            ]);
            Permission::updateOrCreate([
                'slug' => 'tenant.purchases.delete',
                'category' => Permission::CATEGORY_TENANT,
            ], [
                'title' => 'Delete purchases',
                'description' => 'Delete purchases',
                'parent_id' => $permission->id,
            ]);
            Permission::updateOrCreate([
                'slug' => 'tenant.purchases.cancel',
                'category' => Permission::CATEGORY_TENANT,
            ], [
                'title' => 'Cancel purchases',
                'description' => 'Cancel purchases',
                'parent_id' => $permission->id,
            ]);
            Permission::updateOrCreate([
                'slug' => 'tenant.purchases.refund',
                'category' => Permission::CATEGORY_TENANT,
            ], [
                'title' => 'Refund purchases',
                'description' => 'Refund purchases',
                'parent_id' => $permission->id,
            ]);
            Permission::updateOrCreate([
                'slug' => 'tenant.purchases.block',
                'category' => Permission::CATEGORY_TENANT,
            ], [
                'title' => 'Block purchases',
                'description' => 'Block purchases',
                'parent_id' => $permission->id,
            ]);

            Permission::updateOrCreate([
                'slug' => 'tenant.purchases.payables',
                'category' => Permission::CATEGORY_TENANT,
            ], [
                'title' => 'Manage purchases payables',
                'description' => 'Manage purchases payables',
                'parent_id' => $permission->id,
            ]);
        }

        // Purchase Orders Permissions
        if ($permission = Permission::updateOrCreate([
            'slug' => 'tenant.purchase_orders',
            'category' => Permission::CATEGORY_TENANT,
        ], [
            'title' => 'Purchase Orders',
            'description' => 'Manage Purchase Orders',
        ]))
        {
            Permission::updateOrCreate([
                'slug' => 'tenant.purchase_orders.create',
                'category' => Permission::CATEGORY_TENANT,
            ], [
                'title' => 'Create purchase orders',
                'description' => 'Create purchase orders',
                'parent_id' => $permission->id,
            ]);
            Permission::updateOrCreate([
                'slug' => 'tenant.purchase_orders.update',
                'category' => Permission::CATEGORY_TENANT,
            ], [
                'title' => 'Update purchase orders',
                'description' => 'Update purchase orders',
                'parent_id' => $permission->id,
            ]);
            Permission::updateOrCreate([
                'slug' => 'tenant.purchase_orders.delete',
                'title' => 'Delete purchase orders',
                'description' => 'Delete purchase orders',
                'parent_id' => $permission->id,
            ]);
            Permission::updateOrCreate([
                'slug' => 'tenant.purchase_orders.cancel',
                'category' => Permission::CATEGORY_TENANT,
            ], [
                'title' => 'Cancel purchase orders',
                'description' => 'Cancel purchase orders',
                'parent_id' => $permission->id,
            ]);
        }

        // Reports Permissions
        if ($permission = Permission::updateOrCreate([
            'slug' => 'tenant.reports',
            'category' => Permission::CATEGORY_TENANT,
        ], [
            'title' => 'Reports',
            'description' => 'View Reports',
        ]))
        {
            Permission::updateOrCreate([
                'slug' => 'tenant.reports.sales',
                'category' => Permission::CATEGORY_TENANT,
            ], [
                'title' => 'View sales reports',
                'description' => 'View sales reports',
                'parent_id' => $permission->id,
            ]);

            Permission::updateOrCreate([
                'slug' => 'tenant.reports.purchases',
                'category' => Permission::CATEGORY_TENANT,
            ], [
                'title' => 'View purchases reports',
                'description' => 'View purchases reports',
                'parent_id' => $permission->id,
            ]);

            Permission::updateOrCreate([
                'slug' => 'tenant.reports.receivables',
                'category' => Permission::CATEGORY_TENANT,
            ], [
                'title' => 'View receivables reports',
                'description' => 'View receivables reports',
                'parent_id' => $permission->id,
            ]);

            Permission::updateOrCreate([
                'slug' => 'tenant.reports.payables',
                'category' => Permission::CATEGORY_TENANT,
            ], [
                'title' => 'View payables reports',
                'description' => 'View payables reports',
                'parent_id' => $permission->id,
            ]);

            Permission::updateOrCreate([
                'slug' => 'tenant.reports.income',
                'category' => Permission::CATEGORY_TENANT,
            ], [
                'title' => 'Income Statement',
                'description' => 'View Income Statement reports',
                'parent_id' => $permission->id,
            ]);
            Permission::updateOrCreate([
                'slug' => 'tenant.reports.balance_sheet',
                'category' => Permission::CATEGORY_TENANT,
            ], [
                'title' => 'Balance Sheet',
                'description' => 'View Balance Sheet reports',
                'parent_id' => $permission->id,
            ]);
        }
    }

}
