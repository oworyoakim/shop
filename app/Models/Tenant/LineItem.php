<?php

namespace App\Models\Tenant;

use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class LineItem
 * @package App\Models\Tenant
 * @property int id
 * @property int tenant_id
 * @property int journal_entry_id
 * @property int general_ledger_account
 * @property float credit_record
 * @property float debit_record
 * @property bool is_reversed
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class LineItem extends Model
{
    use SoftDeletes;

    protected $table = 'line_items';
    protected $guarded = [];

    public function journal_entry()
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function general_ledger_account()
    {
        return $this->belongsTo(GeneralLedgerAccount::class);
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new TenantScope);
    }
}
