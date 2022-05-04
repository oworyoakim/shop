<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class Customer
 * @package App\Models
 * @property int id
 * @property string name
 * @property string email
 * @property string phone
 * @property string city
 * @property string country
 * @property string address
 * @property int user_id
 * @property int tenant_id
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class Customer extends Model
{
    use SoftDeletes;

    protected $table = 'customers';
    protected $dates = ['deleted_at'];

    protected $guarded = [];

    public function orders() {
        return $this->hasMany(Sale::class);
    }

    public function returns() {
        return $this->hasMany(SalesReturn::class);
    }

    public function receivables() {
        return $this->hasMany(SalesReceivable::class);
    }
}
