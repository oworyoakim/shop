<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_returns', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('sale_id')->unique();
            $table->timestamp('transact_date');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('customer_id')->nullable();
            $table->decimal('gross_amount',15,2)->default(0.00);
            $table->decimal('vat_amount',15,2)->default(0.00);
            $table->decimal('net_amount',15,2)->default(0.00);
            $table->decimal('paid_amount',15,2)->default(0.00);
            $table->decimal('due_amount',15,2)->default(0.00);
            $table->timestamp('due_date')->nullable();
            $table->timestamp('settled_at')->nullable();
            $table->enum('status',['pending','partial','settled'])->default('settled');
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
        Schema::dropIfExists('sales_returns');
    }
}
