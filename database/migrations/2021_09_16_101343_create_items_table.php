<?php

use App\Models\Tenant\Item;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
    private $table = 'items';

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
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('unit_id');
            $table->string('title');
            $table->string('barcode')->unique();
            $table->string('secondary_barcode')->nullable();
            $table->text('description')->nullable();
            $table->string('avatar')->nullable();
            $table->decimal('margin',5,2)->default(0.00);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('tenant_id')
                  ->references('id')
                  ->on('tenants')
                  ->restrictOnDelete()
                  ->cascadeOnUpdate();
            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories')
                  ->restrictOnDelete()
                  ->cascadeOnUpdate();
            $table->foreign('unit_id')
                  ->references('id')
                  ->on('units')
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
