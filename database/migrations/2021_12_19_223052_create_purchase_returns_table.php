<?php

use App\Models\Tenant\PurchaseReturn;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseReturnsTable extends Migration
{
    protected $table = 'purchase_returns';
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
            $table->unsignedBigInteger('purchase_id');
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamp('returned_at');
            $table->unsignedBigInteger('supplier_id');
            $table->decimal('quantity',10,2);
            $table->decimal('price',21,2);
            $table->decimal('amount',21,2);
            $table->decimal('vat',5,2)->default(0.00);
            $table->decimal('discount',5,2)->default(0.00);
            $table->decimal('paid_amount',21,2)->default(0.00);
            $table->decimal('due_amount',21,2)->default(0.00);
            $table->timestamp('due_date')->nullable();
            $table->timestamp('settled_at')->nullable();
            $table->enum('status', [
                PurchaseReturn::STATUS_PENDING,
                PurchaseReturn::STATUS_PARTIAL,
                PurchaseReturn::STATUS_SETTLED
            ])->default(PurchaseReturn::STATUS_SETTLED);
            $table->timestamps();
            $table->softDeletes();
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
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->restrictOnDelete()
                  ->cascadeOnUpdate();
            $table->foreign('supplier_id')
                  ->references('id')
                  ->on('suppliers')
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
