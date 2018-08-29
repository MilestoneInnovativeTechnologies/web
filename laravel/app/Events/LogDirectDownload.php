<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class LogDirectDownload
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
		public $Author, $AuthorRoles, $Product, $Edition, $Package, $Type, $Action;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($USR, $ARY, $DAT)
    {
        $Partner = \App\Models\Partner::whereCode($USR)->with('Roles')->first();
				$this->Author = $Partner->name;
				$this->AuthorRoles = $Partner->Roles->implode('name',', ');
				if($DAT){
					$this->Action = 'Download Submitted.';
					$this->Product = $DAT->Product->name;
					$this->Edition = $DAT->Edition->name;
					$this->Package = $DAT->Package->name;
					$this->Type = $ARY['type'];
				} else {
					$this->Action = 'Record doesn\'t exists.';
					$this->Product = $ARY['product'];
					$this->Edition = $ARY['edition'];
					$this->Package = $ARY['package'];
					$this->Type = $ARY['type'];
				}
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
