<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class LogPasswordResetRequest
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
		public $Name, $Email, $Roles, $IP, $Code;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($Partner, $Email, $Code)
    {
        $this->IP = request()->server('REMOTE_ADDR');
        $Partner = \App\Models\Partner::whereCode($Partner)->with(['Logins.Roles'])->whereHas('Logins',function($Q)use($Email){ $Q->whereEmail($Email); })->get();
				$this->Roles = $Partner->first()->Logins->first()->Roles->pluck('rolename')->toArray();
				$this->Email = $Email;
				$this->Name = $Partner->first()->name;
				$this->Code = $Code;
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
