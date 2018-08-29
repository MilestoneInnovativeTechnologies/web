<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class LogDatabaseBackupDownload
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
		public $User, $Customer, $File, $Size;
	
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($User,$Backup)
    {
      $this->User = $User->Partner->name;
			$this->Customer = $Backup->Customer->name;
			$this->File = $Backup->file;
			$this->Size = $Backup->size;
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
