<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class LogSentMail
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
		
		public $Subject, $View, $To, $Key, $CC, $BCC;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($To, $Subject, $View, $Extra=[])
    {
			$this->Subject = $Subject;
			$this->View = $View;
			$this->To = $this->GetToAddresses($To);
			$this->Key = (array_key_exists("Key",$Extra) && $Extra["Key"]) ? $Extra["Key"] : $this->MailKey();
			$this->CC = (array_key_exists("CC",$Extra) && $Extra["CC"]) ? $this->GetToAddresses($Extra["CC"]) : NULL;
			$this->BCC = (array_key_exists("BCC",$Extra) && $Extra["BCC"]) ? $this->GetToAddresses($Extra["BCC"]) : NULL;
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
	
		private function MailKey(){
			return md5(date("YmdHis").mt_rand());
		}
	
		private function GetToAddresses($To){
			
			if(is_string($To)) return (filter_var($To,FILTER_VALIDATE_EMAIL))?:"";
			if(gettype($To) == "object") {
				if($To instanceof \Illuminate\Support\Collection) return $To->implode('email','|');
				if($To->email) return $To->email;
			}
			if(gettype($To) == "array"){
				$Emails = [];
				foreach($To as $K => $V){
					$Emails[] = $this->GetToAddresses($V);
				}
				return implode("|",$Emails);
			}
		}
	
		private function is_assocArray(array $array) {
			return count(array_filter(array_keys($array), 'is_string')) > 0;
		}
}
