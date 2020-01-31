<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('category_id')->index();
            $table->unsignedInteger('unit_id')->index();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('barcode')->unique();
            $table->text('description')->nullable();
            $table->enum('account',['sales','purchases','both'])->default('both');
            $table->string('avatar')->nullable();
            $table->decimal('margin',5,2)->default(0.00);
            $table->unsignedInteger('user_id')->nullable();
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
        Schema::dropIfExists('items');
    }
}
