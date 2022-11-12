<?php

namespace App\Models\Tenant;

use App\Traits\Tenant\Commentable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class SalePayment
 * @package App\Models
 * @property int id
 * @property int tenant_id
 * @property int branch_id
 * @property int user_id
 * @property int sale_id
 * @property int customer_id
 * @property double amount
 * @property Carbon transaction_date
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class SalePayment extends Model
{
    use SoftDeletes, Commentable;

    protected $table = 'sale_payments';
    protected $dates = ['deleted_at', 'transaction_date'];

    protected $guarded = [];

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function journal_entry() {
        return $this->morphOne(JournalEntry::class, 'transactable');
    }



}
