<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class LogCustomerUpdateDownload
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
		public $Customer, $Product, $Edition, $Package, $ReqVersion, $DwnVersion;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($CID, $VER, $Data)
    {
        $this->storeCustomer($CID);
				$this->ReqVersion = $VER;
				$this->DwnVersion = $Data->version_numeric;
				$this->Product = $Data->Product->name;
				$this->Edition = $Data->Edition->name;
				$this->Package = $Data->Package->name;
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
	
		private function storeCustomer($CID){
			$this->Customer = \App\Models\Partner::whereCode($CID)->first()->name;
		}
}
