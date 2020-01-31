<?php

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::query()->truncate();
        settings()->set('company_name', 'Shop');
        settings()->set('company_address', '');
        settings()->set('company_website', 'http://shop.kim');
        settings()->set('company_country', 'UGANDA');
        settings()->set('portal_address', 'P.O BOX 12345678, KAMPALA');
        settings()->set('company_currency', 'UGX');
        settings()->set('company_logo', '/images/logo.jpg');
        settings()->set('company_email', 'info@shop.kim');
        settings()->set('company_otheremail', 'support@shop.kim');
        settings()->set('enable_vat_on_sales', 0);
        settings()->set('sales_vat', 18);
        settings()->set('enable_vat_on_purchases', 0);
        settings()->set('purchases_vat', 18);
        settings()->set('enable_vat_on_expenses', 0);
        settings()->set('expenses_vat', 18);
        settings()->set('enable_discount', 0);
        settings()->set('discount', 0);
        settings()->set('enable_global_margin', 0);
        settings()->set('profit_margin', 15);
        settings()->set('invoice_disclaimer', 'Goods once sold, are not refundable!');
        settings()->set('print_barcode_with_logo', 0);
        settings()->set('user_password_days', 30);
    }

}


