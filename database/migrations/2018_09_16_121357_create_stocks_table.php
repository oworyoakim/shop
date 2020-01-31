<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('branch_id');
            $table->unsignedInteger('item_id');
            $table->unsignedInteger('quantity')->default(0);
            $table->timestamp('expiry_date')->nullable();
            $table->decimal('cost_price',15,2)->default(0.00);
            $table->decimal('sell_price',15,2)->default(0.00);
            $table->decimal('discount',5,2)->default(0.00);
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('purchase_id')->nullable();
            $table->enum('status',['active,expired'])->default('active');
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
        Schema::dropIfExists('stocks');
    }
}
