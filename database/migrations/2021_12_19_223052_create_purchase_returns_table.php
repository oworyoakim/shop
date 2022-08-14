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
            $table->decimal('quantity',10,2)->default(0.00);
            $table->decimal('price',21,2)->default(0.00);
            $table->decimal('gross_amount',21,2)->default(0.00);
            $table->decimal('vat_amount',21,2)->default(0.00);
            $table->decimal('net_amount',21,2)->default(0.00);
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
