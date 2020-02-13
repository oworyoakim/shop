<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class Supplier
 * @package App\Models
 * @property int id
 * @property string name
 * @property string email
 * @property string phone
 * @property string city
 * @property string country
 * @property string address
 * @property int user_id
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class Supplier extends Model
{
    use SoftDeletes;

    protected $table = 'suppliers';
    protected $dates = ['deleted_at'];

    protected $guarded = [];

    public function supplies(){
        return $this->belongsTo(Purchase::class);
    }

    public function returns(){
        return $this->hasMany(PurchasesReturn::class);
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function payables(){
        return $this->hasMany(PurchasesPayable::class);
    }
}
