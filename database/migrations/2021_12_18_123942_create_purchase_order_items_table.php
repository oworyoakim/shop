<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseOrderItemsTable extends Migration
{
    protected $table = 'purchase_order_items';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('purchase_order_id');
            $table->unsignedBigInteger('item_id');
            $table->decimal('price',21,2)->default(0.00);
            $table->decimal('quantity',10,2)->default(0.00);
            $table->decimal('gross_amount',21,2)->default(0.00);
            $table->decimal('discount_rate',5,2)->default(0.00);
            $table->decimal('discount_amount',21,2)->default(0.00);
            $table->decimal('vat_rate',5,2)->default(0.00);
            $table->decimal('vat_amount',21,2)->default(0.00);
            $table->decimal('net_amount',21,2)->default(0.00);
            $table->timestamps();
            $table->unique(['purchase_order_id','item_id'], 'purchase_order_item_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->table);
    }
}
