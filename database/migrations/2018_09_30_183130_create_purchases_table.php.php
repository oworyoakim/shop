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
            $table->increments('id');
            $table->unsignedBigInteger('transcode')->unique();
            $table->timestamp('transact_date');
            $table->unsignedInteger('supplier_id')->nullable();
            $table->unsignedInteger('branch_id')->nullable();
            $table->decimal('gross_amount',15,2)->default(0.00);
            $table->decimal('vat_rate',5,2)->default(0.00);
            $table->decimal('vat_amount',15,2)->default(0.00);
            $table->decimal('discount_rate',5,2)->default(0.00);
            $table->decimal('discount_amount',15,2)->default(0.00);
            $table->decimal('net_amount',15,2)->default(0.00);
            $table->enum('status',['pending','received','partially_returned','fully_returned','canceled'])->default('received');
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
