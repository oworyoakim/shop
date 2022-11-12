<?php

use App\Models\Tenant\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    private $table = 'users';
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
            $table->unsignedBigInteger('general_ledger_account_id');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('name');
            $table->enum('group', User::TYPES);
            $table->enum('gender', User::GENDERS)->nullable();
            $table->date('dob')->nullable();
            $table->string('email')->nullable();
            $table->string('username')->unique();
            $table->string('phone')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->string('avatar')->nullable();
            $table->decimal('credit_limit', 19, 2)->default(0.00);
            $table->decimal('balance', 19, 2)->default(0.00);
            $table->string('password');
            $table->text('permissions')->nullable();
            $table->string('login_token')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->timestamp('password_last_changed')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->foreign('tenant_id')
                  ->references('id')
                  ->on('tenants')
                  ->restrictOnDelete()
                  ->cascadeOnUpdate();

            $table->foreign('general_ledger_account_id')
                  ->references('id')
                  ->on('general_ledger_accounts')
                  ->restrictOnDelete()
                  ->cascadeOnUpdate();

            $table->foreign('branch_id')
                  ->references('id')
                  ->on('branches')
                  ->nullOnDelete()
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
        Schema::drop($this->table);
    }
}
