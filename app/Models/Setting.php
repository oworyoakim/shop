<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class Setting
 * @package App\Models
 * @property int id
 * @property string key
 * @property string value
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Setting extends Model
{
    protected $table = 'settings';
    protected $guarded = [];
}
