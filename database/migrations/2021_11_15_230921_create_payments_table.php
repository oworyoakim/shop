<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
    protected $table = 'payments';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('payment_date');
            $table->enum('payment_type',[
                'expenses',
                'tax_paid',
                'bank_deposit',
                'bank_withdrawal',
                'bank_interest',
                'bank_loan',
                'bank_loan_interest',
                'bank_overdraft',
                'debts_received',
                'debts_paid',
                'bad_debts_written_off',
                'capital',
                'interest_on_capital',
                'short_term_loan',
                'long_term_loan',
                'goodwill',
                'wages_paid',
                'salaries_paid',
                'discount_allowed',
                'discount_received',
                'commission_paid',
                'commission_received',
                'depreciation',
                'drawings',
                'interest_on_drawings',
                'carriage_inward',
                'carriage_outward',
                'insurance',
            ]);
            $table->decimal('amount', 21, 2)->default(0.00);
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
