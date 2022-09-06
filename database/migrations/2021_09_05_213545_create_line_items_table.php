<?php

use App\Models\Tenant\GeneralLedgerAccount;
use App\Models\Tenant\JournalEntry;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private $table = 'line_items';
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
            $table->unsignedBigInteger('journal_entry_id');
            $table->unsignedBigInteger('general_ledger_account_id');
            $table->decimal('debit_record', 21, 2)->default(0);
            $table->decimal('credit_record', 21, 2)->default(0);
            $table->boolean('is_reversed')->default(false);
            $table->foreign('journal_entry_id')
                  ->references('id')
                  ->on('journal_entries')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();

            $table->foreign('general_ledger_account_id')
                  ->references('id')
                  ->on('general_ledger_accounts')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();

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
};
