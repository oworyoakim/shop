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
            $table->timestamp('sold_at');
            $table->decimal('gross_amount', 21, 2)->default(0.00);
            $table->decimal('vat_rate', 5, 2)->default(0.00);
            $table->decimal('vat_amount', 21, 2)->default(0.00);
            $table->decimal('discount_rate', 5, 2)->default(0.00);
            $table->decimal('discount_amount', 21, 2)->default(0.00);
            $table->decimal('net_amount', 21, 2)->default(0.00);
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
