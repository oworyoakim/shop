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
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->timestamp('returned_at');
            $table->decimal('quantity',10,2);
            $table->decimal('price',21,2);
            $table->decimal('amount', 21, 2);
            $table->decimal('vat', 5, 2)->default(0.00);
            $table->decimal('discount', 5, 2)->default(0.00);
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
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->restrictOnDelete()
                  ->cascadeOnUpdate();
            $table->foreign('customer_id')
                  ->references('id')
                  ->on('customers')
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
