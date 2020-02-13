<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSaleItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('sale_id');
            $table->unsignedInteger('item_id');
            $table->decimal('sell_price',15,2)->default(0.00);
            $table->decimal('quantity',10,2)->default(0.00);
            $table->decimal('returns',10,2)->default(0.00);
            $table->decimal('gross_amount',15,2)->default(0.00);
            $table->decimal('net_amount',15,2)->default(0.00);
            $table->decimal('discount_rate',15,2)->default(0.00);
            $table->decimal('discount_amount',15,2)->default(0.00);
            $table->enum('status',['pending','completed','partial','returned','canceled'])->default('completed');
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['sale_id','item_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sale_items');
    }
}
