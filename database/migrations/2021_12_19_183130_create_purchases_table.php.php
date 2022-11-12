<?php

use App\Models\Tenant\Purchase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchasesTable extends Migration
{
    protected $table = 'purchases';

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
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('purchase_order_id')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('barcode')->unique();
            $table->timestamp('transaction_date');
            $table->decimal('amount', 21, 2)->default(0.00);
            $table->decimal('discount', 5, 2)->default(0.00);
            $table->decimal('vat', 5, 2)->default(0.00);
            $table->enum('status', [
                Purchase::STATUS_PENDING,
                Purchase::STATUS_COMPLETED,
                Purchase::STATUS_PARTIALLY_RETURNED,
                Purchase::STATUS_FULLY_RETURNED,
                Purchase::STATUS_CANCELED
            ])->default(Purchase::STATUS_COMPLETED);
            $table->enum('payment_status', [
                Purchase::PAYMENT_STATUS_SETTLED,
                Purchase::PAYMENT_STATUS_PARTIAL,
                Purchase::PAYMENT_STATUS_PENDING,
                Purchase::PAYMENT_STATUS_CANCELED
            ])->default(Purchase::PAYMENT_STATUS_SETTLED);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('tenant_id')
                  ->references('id')
                  ->on('tenants')
                  ->restrictOnDelete()
                  ->cascadeOnUpdate();
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->restrictOnDelete()
                  ->cascadeOnUpdate();
            $table->foreign('branch_id')
                  ->references('id')
                  ->on('branches')
                  ->restrictOnDelete()
                  ->cascadeOnUpdate();
            $table->foreign('supplier_id')
                  ->references('id')
                  ->on('suppliers')
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
