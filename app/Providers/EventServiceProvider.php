<?php

namespace App\Providers;

use App\Events\PurchaseCompleted;
use App\Events\PurchasePaymentMade;
use App\Events\SaleCompleted;
use App\Events\SalePaymentMade;
use App\Listeners\UpdateItemsStocks;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        PurchaseCompleted::class => [
            UpdateItemsStocks::class,
        ],

        PurchasePaymentMade::class => [

        ],

        SaleCompleted::class => [
            UpdateItemsStocks::class,
        ],

        SalePaymentMade::class => [

        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
