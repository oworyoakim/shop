<?php

namespace App\Models\Landlord;

use App\Models\Tenant\Customer;
use App\Models\Tenant\ExpenseCategory;
use App\Models\Tenant\ExpenseSubcategory;
use App\Models\Tenant\Setting;
use App\Models\Tenant\Supplier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
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
        'credentials.loginName' => 'required',
        'credentials.loginPassword' => 'required',
    ];

    public function scopeActive(Builder $query){
        return $query->where('authorized',true);
    }

    public function scopeInactive(Builder $query){
        return $query->where('authorized',false);
    }

    public function seedSettings()
    {
        Log::info("Updating tenant settings", ['subdomain' => $this->subdomain]);
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
        Log::info("Updated tenant settings", ['subdomain' => $this->subdomain]);
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

}
