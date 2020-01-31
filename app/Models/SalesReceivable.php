<?php

namespace App\Models;

use App\Traits\Commentable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesReceivable extends Model
{
    use SoftDeletes,Commentable;

    protected $table = 'sales_receivables';
    protected $dates = ['transact_date','deleted_at','due_date','settled_at'];

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
    public function branch(){
        return $this->belongsTo(Branch::class);
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
    public static function receivablesUnsettled(){
        return static::where('status','<>','settled');
    }

    /**
     * @return mixed
     */
    public static function receivablesSettled(){
        return static::where('status','=','settled');
    }

    /**
     * @return mixed
     */
    public function balance(){
        return $this->amount - $this->paid;
    }


}
