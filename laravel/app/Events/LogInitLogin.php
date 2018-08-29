<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class LogInitLogin
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
		public $Code, $IP, $Browser;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($code)
    {
				$this->Code = $code;
				$this->IP = request()->server('REMOTE_ADDR');
				$this->Browser = request()->server('HTTP_USER_AGENT');
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
