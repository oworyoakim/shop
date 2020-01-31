<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('expense_date');
            $table->string('transcode')->unique();
            $table->text('comment')->nullable();
            $table->unsignedInteger('expense_type_id');
            $table->decimal('amount',15,2);
            $table->decimal('vat_rate',15,2)->nullable();
            $table->decimal('vat_amount',15,2)->nullable();
            $table->decimal('approved_amount',15,2)->nullable();
            $table->enum('status',['pending','approved','declined','canceled'])->default('pending');
            $table->unsignedInteger('branch_id');
            $table->unsignedInteger('user_id');
            $table->string('receipt')->nullable();
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
        Schema::dropIfExists('expenses');
    }
}
