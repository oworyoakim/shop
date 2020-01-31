<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOtherIncomesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('other_incomes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('other_income_type_id');
            $table->unsignedInteger('branch_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->decimal('amount',15,2)->default(0.00);
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
        Schema::dropIfExists('other_incomes');
    }
}
