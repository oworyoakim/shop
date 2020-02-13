<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->increments('id');
            $table->string('transcode')->unique();
            $table->timestamp('transact_date');
            $table->unsignedInteger('customer_id')->nullable();
            $table->unsignedInteger('branch_id');
            $table->decimal('gross_amount',15,2)->default(0.00);
            $table->decimal('vat_rate',5,2)->default(0.00);
            $table->decimal('vat_amount',15,2)->default(0.00);
            $table->decimal('discount_rate',5,2)->default(0.00);
            $table->decimal('discount_amount',15,2)->default(0.00);
            $table->decimal('net_amount',15,2)->default(0.00);
            $table->enum('status',['pending','completed','canceled','partially_returned','fully_returned'])->default('completed');
            $table->enum('payment_status',['settled','partial','pending','canceled'])->default('settled');
            $table->string('receipt')->nullable();
            $table->unsignedInteger('user_id');
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
        Schema::dropIfExists('sales');
    }
}
