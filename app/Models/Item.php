<?php

namespace App\Models;

use App\Traits\Commentable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class Item
 * @package App\Models
 * @property int id
 * @property int category_id
 * @property int unit_id
 * @property string title
 * @property string slug
 * @property string description
 * @property string barcode
 * @property string account
 * @property string avatar
 * @property float margin
 * @property int user_id
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class Item extends Model
{
    use SoftDeletes, Commentable;

    protected $table = 'items';
    protected $dates = ['deleted_at'];
    protected $guarded = [];

    const ACCOUNT_SALES_ONLY = 'sales';
    const ACCOUNT_PURCHASES_ONLY = 'purchases';
    const ACCOUNT_BOTH = 'both';

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class, 'item_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function salesLines()
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * @param string $barcode
     * @return bool
     */
    public static function exists($barcode)
    {
        $itemsCount = Item::query()->where(function ($query) use ($barcode) {
            $query->where('barcode', $barcode)->orWhere('secondary_barcode', $barcode);
        })->count();
        return !!$itemsCount;
    }


    public static function generateBarcode(User $user)
    {
        do
        {
            $time = str_shuffle((string)Carbon::now()->getTimestamp());
            $rand = str_shuffle((string)random_int(900000, 999999));
            $barcode = "{$rand}{$user->getUserId()}{$time}";
            // do this until we get a string that does not start or end with 0
            do
            {
                $barcode = str_shuffle(strrev($barcode));
                $firstChar = substr($barcode, 0, 1);
                $lastChar = substr($barcode, -1, 1);
            } while ($firstChar == '0' || $lastChar == '0');
            $notValidBarcode = self::exists($barcode);
        } while ($notValidBarcode);
        return $barcode;
    }
}
