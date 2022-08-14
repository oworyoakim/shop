<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    private $table = 'settings';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table,function(Blueprint $table){
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('name')->nullable();
            $table->string('short_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('currency_symbol')->nullable();
            $table->text('slogan')->nullable();
            $table->text('invoice_disclaimer')->nullable();
            $table->boolean('enable_tax')->default(false);
            $table->unsignedInteger('tax_percent')->default(18);
            $table->boolean('enable_bonus')->default(false);
            $table->unsignedInteger('bonus_percent')->default(0);
            $table->unsignedInteger('cancel_order_limit')->default(30);
            $table->unsignedInteger('numeric_percent')->default(60);
            $table->string('logo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop($this->table);
    }
}

