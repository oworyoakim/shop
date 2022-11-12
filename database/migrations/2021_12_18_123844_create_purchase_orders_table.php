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
            $table->decimal('amount',21,2);
            $table->decimal('discount',5,2)->default(0.00);
            $table->decimal('vat',5,2)->default(0.00);
            $table->enum('status',[
                PurchaseOrder::STATUS_PENDING,
                PurchaseOrder::STATUS_COMPLETED
            ])->default(PurchaseOrder::STATUS_COMPLETED);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('tenant_id')
                  ->references('id')
                  ->on('tenants')
                  ->restrictOnDelete()
                  ->cascadeOnUpdate();
            $table->foreign('supplier_id')
                  ->references('id')
                  ->on('suppliers')
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
