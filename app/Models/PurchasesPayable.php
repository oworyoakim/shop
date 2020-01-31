<?php

namespace App\Models;

use App\Traits\Commentable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchasesPayable extends Model
{
    use SoftDeletes,Commentable;

    protected $table = 'purchases_payables';

    protected $dates = ['deleted_at','due_date','settled_at'];

    protected $guarded = [];

    public function purchase(){
        return $this->belongsTo(Purchase::class,'purchase_id');
    }

    public function branch(){
        return $this->belongsTo(Branch::class,'branch_id');
    }

    public function supplier(){
        return $this->belongsTo(Supplier::class,'supplier_id');
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public  function scopeUnsettled(Builder $query){
        return $query->where('status','<>','settled');
    }

    public  function scopeSettled(Builder $query){
        return $query->where('status','settled');
    }

    public function balance(){
        return $this->amount - $this->paid;
    }


}
