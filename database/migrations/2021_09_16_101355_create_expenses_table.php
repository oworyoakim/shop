<?php

use App\Models\Tenant\Expense;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpensesTable extends Migration
{
    private $table = 'expenses';

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
            $table->unsignedBigInteger('expense_category_id');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->timestamp('requested_at');
            $table->string('barcode')->unique();
            $table->decimal('amount',21,2);
            $table->decimal('vat_rate',21,2)->nullable();
            $table->decimal('vat_amount',21,2)->nullable();
            $table->decimal('approved_amount',21,2)->nullable();
            $table->enum('status',[
                Expense::STATUS_PENDING,
                Expense::STATUS_APPROVED,
                Expense::STATUS_DECLINED,
                Expense::STATUS_CANCELED
            ])->default(Expense::STATUS_PENDING);
            $table->string('receipt_number')->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('tenant_id')
                  ->references('id')
                  ->on('tenants')
                  ->restrictOnDelete()
                  ->cascadeOnUpdate();

            $table->foreign('expense_category_id')
                  ->references('id')
                  ->on('expense_categories')
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
