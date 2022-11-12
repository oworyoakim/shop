<?php

use App\Models\Tenant\Sale;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesTable extends Migration
{
    protected $table = 'sales';

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
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('barcode')->unique();
            $table->timestamp('transaction_date');
            $table->decimal('amount', 21, 2);
            $table->decimal('discount', 5, 2)->default(0.00);
            $table->decimal('vat', 5, 2)->default(0.00);
            $table->enum('status', [
                Sale::STATUS_PENDING,
                Sale::STATUS_COMPLETED,
                Sale::STATUS_CANCELED,
                Sale::STATUS_PARTIALLY_RETURNED,
                Sale::STATUS_FULLY_RETURNED
            ])->default(Sale::STATUS_COMPLETED);
            $table->enum('payment_status', [
                Sale::PAYMENT_STATUS_PENDING,
                Sale::PAYMENT_STATUS_SETTLED,
                Sale::PAYMENT_STATUS_PARTIAL,
                Sale::PAYMENT_STATUS_CANCELED
            ])->default(Sale::PAYMENT_STATUS_SETTLED);
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
