<?php

use App\Models\Tenant\SaleItem;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSaleItemsTable extends Migration
{
    protected $table = 'sale_items';

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
            $table->unsignedBigInteger('sale_id');
            $table->unsignedBigInteger('item_id');
            $table->decimal('price', 21, 2);
            $table->decimal('quantity', 10, 2);
            $table->decimal('discount', 5, 2)->default(0.00);
            $table->decimal('vat', 5, 2)->default(0.00);
            $table->decimal('returns', 10, 2)->default(0.00);
            $table->enum('status', [
                SaleItem::STATUS_PENDING,
                SaleItem::STATUS_COMPLETED,
                SaleItem::STATUS_PARTIAL,
                SaleItem::STATUS_RETURNED,
                SaleItem::STATUS_CANCELED
            ])->default(SaleItem::STATUS_COMPLETED);
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->unique(['sale_id', 'item_id'], 'sale_item_unique');
            $table->foreign('tenant_id')
                  ->references('id')
                  ->on('tenants')
                  ->restrictOnDelete()
                  ->cascadeOnUpdate();
            $table->foreign('sale_id')
                  ->references('id')
                  ->on('sales')
                  ->restrictOnDelete()
                  ->cascadeOnUpdate();
            $table->foreign('item_id')
                  ->references('id')
                  ->on('items')
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
