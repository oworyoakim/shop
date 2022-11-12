<?php

use App\Models\Tenant\Stock;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStocksTable extends Migration
{
    protected $table = 'stocks';
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
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('item_id');
            $table->unsignedInteger('quantity')->default(0);
            $table->timestamp('expiry_date')->nullable();
            $table->decimal('cost_price',21,2)->default(0.00);
            $table->decimal('sell_price',21,2)->default(0.00);
            $table->decimal('discount',5,2)->default(0.00);
            $table->decimal('secondary_discount',5,2)->default(0.00);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('purchase_id')->nullable();
            $table->enum('status',[
                Stock::STATUS_ACTIVE,
                Stock::STATUS_EXPIRED,
                Stock::STATUS_FINISHED
            ])->default(Stock::STATUS_ACTIVE);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('tenant_id')
                  ->references('id')
                  ->on('tenants')
                  ->restrictOnDelete()
                  ->cascadeOnUpdate();
            $table->foreign('branch_id')
                  ->references('id')
                  ->on('branches')
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
