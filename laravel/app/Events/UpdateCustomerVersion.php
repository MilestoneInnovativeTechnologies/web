<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UpdateCustomerVersion
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
		
		public $CUS, $SEQ, $VER, $OLD;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($CUS, $SEQ, $VER, $OLD)
    {
        $this->CUS = $CUS;
        $this->SEQ = $SEQ;
        $this->VER = $VER;
        $this->OLD = $OLD;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
