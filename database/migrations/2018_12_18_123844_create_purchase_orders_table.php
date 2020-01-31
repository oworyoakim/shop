<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('purchase_order_code')->unique();
            $table->timestamp('order_date');
            $table->unsignedInteger('supplier_id')->nullable();
            $table->unsignedInteger('branch_id')->nullable();
            $table->decimal('gross_amount',15,2)->default(0.00);
            $table->decimal('vat_rate',5,2)->default(0.00);
            $table->decimal('vat_amount',15,2)->default(0.00);
            $table->decimal('discount_rate',5,2)->default(0.00);
            $table->decimal('discount_amount',15,2)->default(0.00);
            $table->decimal('net_amount',15,2)->default(0.00);
            $table->enum('status',['pending','completed','canceled'])->default('completed');
            $table->unsignedInteger('user_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_orders');
    }
}
