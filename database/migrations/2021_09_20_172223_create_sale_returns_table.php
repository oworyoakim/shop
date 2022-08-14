<?php

use App\Models\Tenant\SaleReturn;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSaleReturnsTable extends Migration
{
    protected $table = 'sale_returns';

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
            $table->unsignedBigInteger('sale_id');
            $table->unsignedBigInteger('item_id');
            $table->timestamp('returned_at');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->decimal('quantity',10,2)->default(0.00);
            $table->decimal('price',21,2)->default(0.00);
            $table->decimal('gross_amount', 21, 2)->default(0.00);
            $table->decimal('vat_amount', 21, 2)->default(0.00);
            $table->decimal('net_amount', 21, 2)->default(0.00);
            $table->decimal('paid_amount', 21, 2)->default(0.00);
            $table->decimal('due_amount', 21, 2)->default(0.00);
            $table->timestamp('due_date')->nullable();
            $table->timestamp('settled_at')->nullable();
            $table->enum('status', [
                SaleReturn::STATUS_PENDING,
                SaleReturn::STATUS_PARTIAL,
                SaleReturn::STATUS_SETTLED
            ])->default(SaleReturn::STATUS_SETTLED);
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
