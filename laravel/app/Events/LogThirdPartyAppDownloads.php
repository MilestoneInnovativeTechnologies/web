<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class LogThirdPartyAppDownloads
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
		public $Author, $Name, $File, $Status;
		public $IP, $Browser;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($Ary,$Status)
    {
			$this->Author = $Ary['author'];
			$this->Name = $Ary['name'];
			$this->File = $Ary['file'];
			$this->Status = $Status;
			$this->IP = request()->server('REMOTE_ADDR');
			$this->Browser = request()->server('HTTP_USER_AGENT');
			if($Ary['downloads'] > 0) \App\Models\ThirdPartyApplication::find($Ary['code'])->increment_download($Ary['code']);
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
