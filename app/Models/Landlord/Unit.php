<?php

namespace App\Models\Landlord;

use App\Models\Tenant\Item;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class Unit
 * @package App\Models
 * @property int id
 * @property string title
 * @property string slug
 * @property string description
 * @property int user_id
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class Unit extends Model
{
    use SoftDeletes;

    protected $table = 'units';
    protected $dates = ['deleted_at'];

    protected $guarded = [];

    public function items()
    {
        return $this->hasMany(Item::class, 'unit_id');
    }
}
