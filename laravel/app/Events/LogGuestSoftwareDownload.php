<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class LogGuestSoftwareDownload
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
	
		public $Key, $Product, $Edition, $Package, $Text;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($Key, $Product, $Edition, $Package, $Text = "")
    {
        $this->Key = $Key;
        $this->Product = $Product;
        $this->Edition = $Edition;
        $this->Package = $Package;
        $this->Text = $Text;
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
