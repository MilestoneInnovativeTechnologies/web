<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class LogUserLogin
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
		public $Name, $Email, $Role, $IP, $Browser, $Version, $OS;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($User, $Role)
    {
        $this->Name = \App\Models\Partner::find($User->partner)->name;
				$this->Email = $User->email;
				$this->Role = $Role;
				$this->IP = request()->server('REMOTE_ADDR');
				$this->Browser = request()->server('HTTP_USER_AGENT');
				//$Browser = get_browser(null,true);
				//$this->Browser = $Browser['browser'];
				//$this->Version = $Browser['version'];
				//$this->OS = $Browser['platform'];
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
