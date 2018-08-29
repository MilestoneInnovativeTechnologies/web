<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class LogSupportPrintObjectDownload
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
		public $Code, $Name, $Mail, $IP, $Browser;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($Mail, $Code, $Name)
    {
				$this->IP = request()->server('REMOTE_ADDR');
				$this->Browser = request()->server('HTTP_USER_AGENT');
				$this->Code = $Code;
				$this->Name = $Name;
				$this->Mail = $Mail;
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
