<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int id
 * @property  int tenant_id
 * @property string body
 * @property  int user_id
 * @property  int commentable_id
 * @property  string commentable_type
 * @property  Carbon created_at
 * @property  Carbon updated_at
 */
class Comment extends Model
{
    protected $table = 'comments';
    protected $guarded = [];

    public function commentable()
    {
        return $this->morphTo();
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
}
