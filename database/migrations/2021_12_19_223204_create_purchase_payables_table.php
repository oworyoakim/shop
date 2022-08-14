<?php

use App\Models\Tenant\PurchasePayable;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchasePayablesTable extends Migration
{
    protected $table = 'purchase_payables';
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
            $table->unsignedBigInteger('purchase_id');
            $table->unsignedBigInteger('supplier_id');
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('user_id');
            $table->decimal('amount',21,2)->default(0.00);
            $table->decimal('paid',21,2)->default(0.00);
            $table->enum('status',[
                PurchasePayable::STATUS_PENDING,
                PurchasePayable::STATUS_PARTIAL,
                PurchasePayable::STATUS_SETTLED
            ])->default(PurchasePayable::STATUS_PENDING);
            $table->timestamp('due_date');
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('settled_at')->nullable();
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
