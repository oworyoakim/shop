<?php

use App\Models\Tenant\SaleItem;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSaleOrderItemsTable extends Migration
{
    protected $table = 'sale_order_items';

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
            $table->unsignedBigInteger('sale_order_id');
            $table->unsignedBigInteger('item_id');
            $table->decimal('sell_price', 21, 2)->default(0.00);
            $table->decimal('quantity', 10, 2)->default(0.00);
            $table->decimal('amount', 21, 2)->default(0.00);
            $table->decimal('discount', 5, 2)->default(0.00);
            $table->decimal('vat', 5, 2)->default(0.00);
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->unique(['sale_order_id', 'item_id'], 'sale_order_item_unique');
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
            $table->foreign('sale_order_id')
                  ->references('id')
                  ->on('sale_orders')
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
