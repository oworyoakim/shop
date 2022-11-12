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
            $table->unsignedBigInteger('purchase_order_id');
            $table->unsignedBigInteger('item_id');
            $table->decimal('price',21,2)->default(0.00);
            $table->decimal('quantity',10,2)->default(0.00);
            $table->decimal('amount',21,2)->default(0.00);
            $table->decimal('discount',5,2)->default(0.00);
            $table->decimal('vat',5,2)->default(0.00);
            $table->timestamps();
            $table->unique(['purchase_order_id','item_id'], 'purchase_order_item_unique');
            $table->foreign('tenant_id')
                  ->references('id')
                  ->on('tenants')
                  ->restrictOnDelete()
                  ->cascadeOnUpdate();
            $table->foreign('item_id')
                  ->references('id')
                  ->on('items')
                  ->restrictOnDelete()
                  ->cascadeOnUpdate();
            $table->foreign('purchase_order_id')
                  ->references('id')
                  ->on('purchase_orders')
                  ->restrictOnDelete()
                  ->cascadeOnUpdate();
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
