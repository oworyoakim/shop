<?php

namespace App\Models;

use App\Traits\Commentable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchasesReturn extends Model
{
    use SoftDeletes,Commentable;

    protected $table = 'purchases_returns';

    protected $dates = ['due_date','transact_date','settled_at','deleted_at'];

    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transaction(){
        return $this->belongsTo(Purchase::class,'transcode','transcode');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    /**
     * @return mixed
     */
    public function balance(){
        return $this->net_amount - $this->paid_amount;
    }

    /**
     * @return mixed
     */
    public static function returnsUnpaid(){
        return static::where('status','<>','settled');
    }

    /**
     * @return mixed
     */
    public static function returnsPaid(){
        return static::where('status','=','settled');
    }

}
