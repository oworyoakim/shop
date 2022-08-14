<?php

use App\Models\Tenant\SaleReceivable;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSaleReceivablesTable extends Migration
{
    protected $table = 'sale_receivables';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->decimal('amount',21,2)->default(0.00);
            $table->decimal('paid',21,2)->default(0.00);
            $table->enum('status', [
                SaleReceivable::STATUS_PENDING,
                SaleReceivable::STATUS_PARTIAL,
                SaleReceivable::STATUS_SETTLED
            ])->default(SaleReceivable::STATUS_PENDING);
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
