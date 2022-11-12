<?php

use App\Models\Tenant\PurchaseItem;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseItemsTable extends Migration
{
    protected $table = 'purchase_items';

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
            $table->unsignedBigInteger('purchase_id');
            $table->unsignedBigInteger('item_id');
            $table->decimal('price', 21, 2);
            $table->decimal('quantity', 10, 2);
            $table->decimal('discount', 5, 2)->default(0.00);
            $table->decimal('vat', 5, 2)->default(0.00);
            $table->decimal('returns', 10, 2)->default(0.00);
            $table->timestamp('expiry_date')->nullable();
            $table->enum('status', [
                PurchaseItem::STATUS_COMPLETED,
                PurchaseItem::STATUS_PARTIAL,
                PurchaseItem::STATUS_RETURNED
            ])->default(PurchaseItem::STATUS_COMPLETED);
            $table->timestamps();
            $table->unique(['purchase_id', 'item_id'], 'purchase_item_unique');
            $table->foreign('tenant_id')
                  ->references('id')
                  ->on('tenants')
                  ->restrictOnDelete()
                  ->cascadeOnUpdate();
            $table->foreign('purchase_id')
                  ->references('id')
                  ->on('purchases')
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
