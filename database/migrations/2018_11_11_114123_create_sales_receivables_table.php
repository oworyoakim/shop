<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesReceivablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_receivables', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('sale_id')->unique();
            $table->timestamp('transact_date');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('branch_id');
            $table->unsignedInteger('customer_id')->nullable();
            $table->decimal('amount',15,2)->default(0.00);
            $table->decimal('paid',15,2)->default(0.00);
            $table->enum('status',['pending','partial','settled'])->default('pending');
            $table->timestamp('due_date')->nullable();
            $table->timestamp('settled_at')->nullable();
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
        Schema::dropIfExists('sales_receivables');
    }
}
