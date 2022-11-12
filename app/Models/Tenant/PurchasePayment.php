<?php

namespace App\Models\Tenant;

use App\Traits\Tenant\Commentable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class PurchasePayment
 * @package App\Models
 * @property int id
 * @property int tenant_id
 * @property int purchase_id
 * @property int user_id
 * @property int supplier_id
 * @property int branch_id
 * @property double amount
 * @property Carbon transaction_date
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class PurchasePayment extends Model
{
    use SoftDeletes, Commentable;

    protected $table = 'purchase_payments';

    protected $dates = ['deleted_at', 'transaction_date'];

    protected $guarded = [];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function journal_entry() {
        return $this->morphOne(JournalEntry::class, 'transactable');
    }

}
