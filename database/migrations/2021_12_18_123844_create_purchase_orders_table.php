<?php

use App\Models\Tenant\PurchaseOrder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseOrdersTable extends Migration
{
    protected $table = 'purchase_orders';
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
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->string('order_code')->unique();
            $table->timestamp('order_date');
            $table->decimal('gross_amount',21,2)->default(0.00);
            $table->decimal('vat_rate',5,2)->default(0.00);
            $table->decimal('vat_amount',21,2)->default(0.00);
            $table->decimal('discount_rate',5,2)->default(0.00);
            $table->decimal('discount_amount',21,2)->default(0.00);
            $table->decimal('net_amount',21,2)->default(0.00);
            $table->enum('status',[
                PurchaseOrder::STATUS_PENDING,
                PurchaseOrder::STATUS_COMPLETED
            ])->default(PurchaseOrder::STATUS_COMPLETED);
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
