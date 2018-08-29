<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class LogPrintObjectDownloadFromMail
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
		public	$IP, $Browser, $Customer, $Product, $Function, $POCode;
				
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($Customer, $Product, $Function, $POCode)
    {
				$this->IP = request()->server('REMOTE_ADDR');
				$this->Browser = request()->server('HTTP_USER_AGENT');
				$this->Customer = $Customer;
				$this->Product = $Product;
				$this->Function = $Function;
				$this->POCode = $POCode;
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
