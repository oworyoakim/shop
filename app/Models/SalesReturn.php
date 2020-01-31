<?php

namespace App\Models;

use App\Traits\Commentable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesReturn extends Model
{
    use SoftDeletes,Commentable;

    protected $table = 'sales_returns';

    protected $dates = ['due_date','deleted_at'];

    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transaction(){
        return $this->belongsTo(Sale::class,'transcode','transcode');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer(){
        return $this->belongsTo(Customer::class);
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
