<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('purchase_order_id');
            $table->unsignedInteger('item_id');
            $table->decimal('price',15,2)->default(0.00);
            $table->decimal('quantity',10,2)->default(0.00);
            $table->decimal('gross_amount',15,2)->default(0.00);
            $table->decimal('net_amount',15,2)->default(0.00);
            $table->decimal('discount_rate',15,2)->default(0.00);
            $table->decimal('discount_amount',15,2)->default(0.00);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['purchase_order_id','item_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_order_items');
    }
}
