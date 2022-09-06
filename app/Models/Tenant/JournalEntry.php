<?php

namespace App\Models\Tenant;

use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class JournalEntry
 * @package App\Models\Tenant
 * @property int id
 * @property int tenant_id
 * @property int transactable_id
 * @property string transactable_type
 * @property float amount
 * @property bool is_reversed
 * @property Carbon transaction_date
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class JournalEntry extends Model
{
    use SoftDeletes;

    protected $table = 'journal_entries';
    protected $guarded = [];

    public function line_items()
    {
        return $this->hasMany(LineItem::class);
    }

    public function transactable()
    {
        return $this->morphTo();
    }

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(new TenantScope);
    }
}
