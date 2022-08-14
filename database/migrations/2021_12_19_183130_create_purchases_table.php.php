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
            $table->unsignedBigInteger('purchase_order_id')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('barcode')->unique();
            $table->timestamp('purchased_at');
            $table->decimal('gross_amount', 21, 2)->default(0.00);
            $table->decimal('vat_rate', 5, 2)->default(0.00);
            $table->decimal('vat_amount', 21, 2)->default(0.00);
            $table->decimal('discount_rate', 5, 2)->default(0.00);
            $table->decimal('discount_amount', 21, 2)->default(0.00);
            $table->decimal('net_amount', 21, 2)->default(0.00);
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
