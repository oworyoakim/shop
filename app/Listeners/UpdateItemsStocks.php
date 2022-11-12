<?php

namespace App\Listeners;

use App\Models\Tenant\Stock;
use App\ShopHelper;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateItemsStocks
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param object $event
     *
     * @return void
     */
    public function handle($event)
    {
        try
        {
            $orderlines = $event->purchase->orderlines()->with(['item'])->get();
            DB::transaction(function () use ($event, $orderlines) {
                foreach ($orderlines as $orderline)
                {
                    // Update Stocks
                    // compute sell price if this item is not for purchases only
                    $sellPrice = 0;
                    if ($orderline->item->margin > 0)
                    {
                        $sellPrice = ShopHelper::toNearestHundredsUpper(round((($orderline->item->margin + 100) * $orderline->cost_price) / 100, 2));
                    }

                    $stock = Stock::query()->where([
                        'item_id' => $orderline->item_id,
                        'branch_id' => $event->purchase->branch_id
                    ])->first();

                    if ($stock)
                    {
                        // update
                        $stock->quantity += $orderline->quantity;
                        $stock->cost_price = $orderline->cost_price;
                        $stock->sell_price = $sellPrice;
                        $stock->status = Stock::STATUS_ACTIVE;
                        $stock->save();
                    } else
                    {
                        // create new
                        Stock::query()->create([
                            'quantity' => $orderline->quantity,
                            'cost_price' => $orderline->cost_price,
                            'sell_price' => $sellPrice,
                            'item_id' => $orderline->item_id,
                            'branch_id' => $event->purchase->branch_id,
                        ]);
                    }
                }
            });
        } catch (\Throwable $ex)
        {
            Log::error("UpdateItemsStocksEventError", [
                'Message' => $ex->getMessage(),
                'File' => $ex->getFile(),
                'Line' => $ex->getLine(),
                'EventPayload' => $event->purchase->getAttributes()
            ]);
        }
    }
}
