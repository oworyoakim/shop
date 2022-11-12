<?php

namespace App\Events;

use App\Models\Tenant\Purchase;
use App\Models\Tenant\PurchasePayment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PurchasePaymentMade
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Purchase $purchase;
    public PurchasePayment $payment;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Purchase $purchase, PurchasePayment $payment)
    {
        $this->purchase = $purchase;
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
