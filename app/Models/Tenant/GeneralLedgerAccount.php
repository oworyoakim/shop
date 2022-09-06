<?php

namespace App\Models\Tenant;

use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class GeneralLedgerAccount
 * @package App\Models\Tenant
 * @property int id
 * @property int tenant_id
 * @property string name
 * @property string account_type
 * @property string parent_id
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class GeneralLedgerAccount extends Model
{
    use SoftDeletes;

    protected $table = 'general_ledger_accounts';
    protected $guarded = [];

    const TYPE_ASSET = 'asset';
    const TYPE_LIABILITY = 'liability';

    const ASSET_GLA_NAME = 'Asset';
    const LIABILITY_GLA_NAME = 'Liability';

    const CAPITAL_GLA_NAME = 'Capital';
    const CASH_GLA_NAME = 'Cash';
    const BANK_GLA_NAME = 'Bank';
    const PURCHASES_GLA_NAME = 'Purchases';
    const SALES_GLA_NAME = 'Sales';
    const EXPENSES_GLA_NAME = 'Expenses';
    const SALES_VAT_GLA_NAME = 'Sales Vat';
    const PURCHASES_VAT_GLA_NAME = 'Purchases Vat';
    const SALES_DISCOUNTS_GLA_NAME = 'Sales Discounts';
    const PURCHASES_DISCOUNTS_GLA_NAME = 'Purchases Discounts';
    const ACCOUNTS_RECEIVABLE_GLA_NAME = 'Accounts Receivable';
    const ACCOUNTS_PAYABLE_GLA_NAME = 'Accounts Payable';
    const DRAWINGS_GLA_NAME = 'Drawings';

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(new TenantScope);
    }

    public function line_items()
    {
        return $this->hasMany(LineItem::class);
    }

    public function computeBalance()
    {
        $builder = DB::table('line_items')
                     ->join('journal_entries', 'line_items.journal_entry_id', '=', 'journal_entries.id')
                     ->whereRaw("1=1 AND journal_entries.deleted_at IS NULL AND journal_entries.is_reversed = 0 AND line_items.general_ledger_account_id = {$this->id}");
        if ($this->account_type === self::TYPE_LIABILITY)
        {
            $builder->selectRaw("SUM(line_items.credit_record - line_items.debit_record) as 'balance'");
        } else
        {
            $builder->selectRaw("SUM(line_items.debit_record - line_items.credit_record) as 'balance'");
        }

        $gla_balance = $builder->first();

        if (empty($gla_balance))
        {
            return 0;
        }
        return floatval($gla_balance->balance);
    }
}
