<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('tenant_id');
            $table->string('transaction_code')->unique();
            $table->timestamp('transaction_date');
            $table->unsignedInteger('purchase_order_id')->nullable();
            $table->unsignedInteger('supplier_id')->nullable();
            $table->unsignedInteger('branch_id')->nullable();
            $table->decimal('gross_amount',19,2)->default(0.00);
            $table->decimal('vat_rate',5,2)->default(0.00);
            $table->decimal('vat_amount',19,2)->default(0.00);
            $table->decimal('discount_rate',5,2)->default(0.00);
            $table->decimal('discount_amount',19,2)->default(0.00);
            $table->decimal('net_amount',19,2)->default(0.00);
            $table->enum('status',['pending','completed','partially_returned','fully_returned','canceled'])->default('completed');
            $table->enum('payment_status',['settled','partial','pending','canceled'])->default('settled');
            $table->string('receipt')->nullable();
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
        Schema::dropIfExists('purchases');
    }
}
