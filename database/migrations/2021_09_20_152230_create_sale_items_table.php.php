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
            $table->decimal('sell_price', 21, 2)->default(0.00);
            $table->decimal('quantity', 10, 2)->default(0.00);
            $table->decimal('returns', 10, 2)->default(0.00);
            $table->decimal('gross_amount', 21, 2)->default(0.00);
            $table->decimal('net_amount', 21, 2)->default(0.00);
            $table->decimal('discount_rate', 21, 2)->default(0.00);
            $table->decimal('discount_amount', 21, 2)->default(0.00);
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
