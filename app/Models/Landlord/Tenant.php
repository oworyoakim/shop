<?php

namespace App\Models\Landlord;

use App\Models\Permission;
use App\Models\Tenant\Customer;
use App\Models\Tenant\ExpenseCategory;
use App\Models\Tenant\ExpenseSubcategory;
use App\Models\Tenant\GeneralLedgerAccount;
use App\Models\Tenant\OrderReport;
use App\Models\Tenant\Setting;
use App\Models\Tenant\Supplier;
use App\Models\Tenant\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class Tenant extends Model {
    protected $table = "tenants";
    protected $hidden = ['created_at', 'updated_at','deleted_at'];
    protected $guarded = [];
    protected $casts = [
        'authorized' => 'boolean',
    ];

    const VALIDATION_RULES = [
        'subdomain' => 'required|alpha_num|min:3|max:15|unique:tenants',
        'email' => 'nullable|email|unique:tenants',
        'name' => 'required',
        'phone' => 'required|string',
        'country' => 'required|string',
        'city' => 'nullable|string',
        'address' => 'nullable|string',
        'website' => 'nullable|string',
        'credentials.loginName' => 'required|same:subdomain',
        'credentials.loginPassword' => 'required',
    ];

    public function scopeActive(Builder $query){
        return $query->where('authorized',true);
    }

    public function scopeInactive(Builder $query){
        return $query->where('authorized',false);
    }

    public function scopeWithBranchesCount(Builder $builder){
        return $builder->selectSub(function ($query){
            $query->from('branches')
                  ->whereColumn('tenant_id', 'tenants.id')
                  ->selectRaw("COUNT(*)")
                  ->limit(1);
        }, 'branches');
    }

    public function scopeWithCurrency(Builder $builder){
        return $builder->selectSub(function ($query){
            $query->from('settings')
                  ->whereColumn('tenant_id', 'tenants.id')
                  ->select("currency")
                  ->limit(1);
        }, 'currency');
    }

    public function updateSettings()
    {
        Log::info("Updating Tenant Settings", ['subdomain' => $this->subdomain]);
        Setting::query()->updateOrCreate(['tenant_id' => $this->id], [
            'name' => $this->name,
            'short_name' => null,
            'email' => $this->email,
            'currency' => 'UGX',
            'slogan' => 'Your Shopping Partner!',
            'invoice_disclaimer' => 'Goods Once Sold Are Not Returnable!',
            'enable_tax' => false,
            'tax_percent' => 15,
            'enable_bonus' => false,
            'bonus_percent' => 10,
            'cancel_order_limit' => 10,
            'numeric_percent' => 75,
            'logo' => $this->logo,
        ]);
        Log::info("Updated Tenant Settings", ['subdomain' => $this->subdomain]);
        return $this;
    }

    public function seedExpenseCategoriesAndSubCategories()
    {
        Log::info("Seeding tenant expense categories and subcategories", ['subdomain' => $this->subdomain]);
        $categories = [
            [
                'title' => "Operations",
                'subcategories' => [
                    ['title' => "Data"],
                    ['title' => "DsTv"],
                    ['title' => "Electricity"],
                    ['title' => "Paper Rolls"],
                    ['title' => "Generator Fuel"],
                    ['title' => "Salaries"],
                    ['title' => "Salary Advance"],
                    ['title' => "Staff Allowance"],
                    ['title' => "Branch Setup"],
                    ['title' => "Rent"],
                    ['title' => "Garbage"],
                    ['title' => "Liquid Soap"],
                    ['title' => "Transport"],
                    ['title' => "Cartridge Refill"],
                    ['title' => "Mobile Money Charges"],
                    ['title' => "Printer Repairs"],
                    ['title' => "Computer Repairs"],
                    ['title' => "Electrical Repairs"],
                    ['title' => "TV Repairs"],
                    ['title' => "Computer Purchase"],
                    ['title' => "Furniture"],
                    ['title' => "License"],
                    ['title' => "Lunch"],
                    ['title' => "Water"],
                    ['title' => "Shortage"],
                ],
            ],
        ];
        foreach ($categories as $category){
            $expenseCategory = ExpenseCategory::query()->updateOrCreate([
                'tenant_id' => $this->id,
                'title' => $category['title'],
            ],[
                'title' => $category['title'],
            ]);
            foreach ($category['subcategories'] as $subcategory) {
                ExpenseSubcategory::query()->updateOrCreate([
                    'tenant_id' => $this->id,
                    'expense_category_id' => $expenseCategory->id,
                    'title' => $subcategory['title'],
                ], $subcategory);
            }
        }
        Log::info("Seeded tenant expense categories and subcategories", ['subdomain' => $this->subdomain]);
        return $this;
    }

    public function seedSuppliers()
    {
        Log::info("Seeding Tenant Suppliers", ['subdomain' => $this->subdomain]);
        Supplier::query()->updateOrCreate([
            'tenant_id' => $this->id,
            'name' => 'Default Supplier'
        ], [
            'phone' => '+256770000000',
            'email' => 'supplier@shop.kim',
            'address' => 'Kikuubo',
            'city' => 'Kampala',
            'country' => 'Uganda',
        ]);
        Log::info("Seeded Tenant Suppliers", ['subdomain' => $this->subdomain]);
        return $this;
    }

    public function seedCustomers()
    {
        Log::info("Seeding Tenant Customers", ['subdomain' => $this->subdomain]);
        Customer::query()->updateOrCreate([
            'tenant_id' => $this->id,
            'name' => 'Default Customer',
        ],[
            'phone' => '+256770000000',
            'email' => 'customer@shop.kim',
            'address' => 'Kikuubo',
            'city' => 'Kampala',
            'country' => 'Uganda',
        ]);

        Log::info("Seeded Tenant Customers", ['subdomain' => $this->subdomain]);
        return $this;
    }

    public function seedGeneralLedgerAccounts()
    {
        Log::info("Seeding Tenant GeneralLedgerAccounts", ['subdomain' => $this->subdomain]);
        // Assets
        $asset_gla = GeneralLedgerAccount::updateOrCreate([
            'name' => GeneralLedgerAccount::ASSET_GLA_NAME,
            'account_type' => GeneralLedgerAccount::TYPE_ASSET
        ]);

        GeneralLedgerAccount::createOrUpdate([
            'name' => GeneralLedgerAccount::CASH_GLA_NAME,
            'account_type' => GeneralLedgerAccount::TYPE_ASSET,
            'parent_id' => $asset_gla->id
        ]);

        GeneralLedgerAccount::createOrUpdate([
            'name' => GeneralLedgerAccount::BANK_GLA_NAME,
            'account_type' => GeneralLedgerAccount::TYPE_ASSET,
            'parent_id' => $asset_gla->id
        ]);

        GeneralLedgerAccount::createOrUpdate([
            'name' => GeneralLedgerAccount::SALES_GLA_NAME,
            'account_type' => GeneralLedgerAccount::TYPE_ASSET,
            'parent_id' => $asset_gla->id
        ]);

        GeneralLedgerAccount::createOrUpdate([
            'name' => GeneralLedgerAccount::ACCOUNTS_RECEIVABLE_GLA_NAME,
            'account_type' => GeneralLedgerAccount::TYPE_ASSET,
            'parent_id' => $asset_gla->id
        ]);

        GeneralLedgerAccount::createOrUpdate([
            'name' => GeneralLedgerAccount::PURCHASES_VAT_GLA_NAME,
            'account_type' => GeneralLedgerAccount::TYPE_ASSET,
            'parent_id' => $asset_gla->id
        ]);

        GeneralLedgerAccount::createOrUpdate([
            'name' => GeneralLedgerAccount::PURCHASES_DISCOUNTS_GLA_NAME,
            'account_type' => GeneralLedgerAccount::TYPE_ASSET,
            'parent_id' => $asset_gla->id
        ]);

        GeneralLedgerAccount::createOrUpdate([
            'name' => GeneralLedgerAccount::DRAWINGS_GLA_NAME,
            'account_type' => GeneralLedgerAccount::TYPE_ASSET,
            'parent_id' => $asset_gla->id
        ]);


        // Liabilities
        $liability_gla = GeneralLedgerAccount::updateOrCreate([
            'name' => GeneralLedgerAccount::LIABILITY_GLA_NAME,
            'account_type' => GeneralLedgerAccount::TYPE_LIABILITY
        ]);

        GeneralLedgerAccount::createOrUpdate([
            'name' => GeneralLedgerAccount::PURCHASES_GLA_NAME,
            'account_type' => GeneralLedgerAccount::TYPE_LIABILITY,
            'parent_id' => $liability_gla->id
        ]);

        GeneralLedgerAccount::createOrUpdate([
            'name' => GeneralLedgerAccount::CAPITAL_GLA_NAME,
            'account_type' => GeneralLedgerAccount::TYPE_LIABILITY,
            'parent_id' => $liability_gla->id
        ]);

        GeneralLedgerAccount::createOrUpdate([
            'name' => GeneralLedgerAccount::ACCOUNTS_PAYABLE_GLA_NAME,
            'account_type' => GeneralLedgerAccount::TYPE_LIABILITY,
            'parent_id' => $liability_gla->id
        ]);

        GeneralLedgerAccount::createOrUpdate([
            'name' => GeneralLedgerAccount::SALES_VAT_GLA_NAME,
            'account_type' => GeneralLedgerAccount::TYPE_LIABILITY,
            'parent_id' => $liability_gla->id
        ]);

        GeneralLedgerAccount::createOrUpdate([
            'name' => GeneralLedgerAccount::SALES_DISCOUNTS_GLA_NAME,
            'account_type' => GeneralLedgerAccount::TYPE_LIABILITY,
            'parent_id' => $liability_gla->id
        ]);

        GeneralLedgerAccount::createOrUpdate([
            'name' => GeneralLedgerAccount::EXPENSES_GLA_NAME,
            'account_type' => GeneralLedgerAccount::TYPE_LIABILITY,
            'parent_id' => $liability_gla->id
        ]);

        Log::info("Seeded Tenant GeneralLedgerAccounts", ['subdomain' => $this->subdomain]);
        return $this;
    }

    public function createAdminUser(string $password){
        Log::info("Creating Tenant Admin User", ['subdomain' => $this->subdomain]);
        // Seed Tenant Admin User
        // Give all the permissions
        $permissions = [];
        foreach (Permission::forTenant()->get() as $permission) {
            $permissions[$permission->slug] = true;
        }

        $adminCredentials = [
            "password" => Hash::make($password),
            "first_name" => 'Admin',
            "last_name" => 'Admin',
            "group" => User::TYPE_ADMINISTRATORS,
            "avatar" => '/images/avatar.png',
            'active' => true,
            'permissions' => $permissions,
        ];
        // create the admin user
        User::query()->updateOrCreate([
            "tenant_id" => $this->id,
            "username" => $this->subdomain,
        ], $adminCredentials);

        Log::info("Created Tenant Admin User", ['subdomain' => $this->subdomain]);
        return $this;
    }

}
