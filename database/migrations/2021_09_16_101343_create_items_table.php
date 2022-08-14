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
            $table->unsignedBigInteger('tenant_id')->index();
            $table->unsignedBigInteger('category_id')->index();
            $table->unsignedBigInteger('unit_id')->index();
            $table->string('title');
            $table->string('slug');
            $table->string('barcode')->unique();
            $table->string('secondary_barcode')->nullable();
            $table->text('description')->nullable();
            $table->enum('account',[
                Item::ACCOUNT_PURCHASES_ONLY,
                Item::ACCOUNT_SALES_ONLY,
                Item::ACCOUNT_BOTH,
            ])->default(Item::ACCOUNT_BOTH);
            $table->string('avatar')->nullable();
            $table->decimal('margin',5,2)->default(0.00);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unique(['tenant_id', 'slug'], 'tenant_item_slug_unique');
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
