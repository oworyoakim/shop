<?php

namespace App\Events;

use App\Models\Tenant\Sale;
use App\Models\Tenant\SalePayment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SalePaymentMade
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Sale $sale;
    public SalePayment $payment;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Sale $sale, SalePayment $payment)
    {
        $this->sale = $sale;
        $this->payment = $payment;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
