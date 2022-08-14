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
