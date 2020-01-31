<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use SoftDeletes;

    protected $table = 'purchase_orders';
    protected $dates = ['order_date'];
    protected $fillable = [
        'reference_id',
        'order_date',
        'status',
        'gross_amount',
        'net_amount',
        'vat_rate',
        'vat_amount',
        'discount_rate',
        'discount_amount',
        'supplier_id',
        'branch_id',
        'user_id'
    ];

    public function orderlines(){
        return $this->hasMany(PurchaseOrderItem::class,'reference_id','reference_id');
    }

    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }

    public function branch(){
        return $this->belongsTo(Branch::class);
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public static function canceledOrders(){
        return static::where('status','=','canceled');
    }

    public static function pendingOrders(){
        return static::where('status','=','pending');
    }

    public static function completedOrders(){
        return static::where('status','=','completed');
    }

}
