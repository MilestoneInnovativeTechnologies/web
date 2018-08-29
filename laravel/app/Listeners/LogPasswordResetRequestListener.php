<?php

namespace App\Listeners;

use App\Events\LogPasswordResetRequest;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Storage;

class LogPasswordResetRequestListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  LogPasswordResetRequest  $event
     * @return void
     */
		private $Path = "customlog/PasswordResetRequest";
		private $Head = ["Day","Date","Time","Name","Email","Roles","Requested from IP","Sent Code"];
    public function handle(LogPasswordResetRequest $event)
    {
        $Name = $event->Name;
				$Email = $event->Email;
				$Roles = implode(", ",$event->Roles);
				$IP = $event->IP;
				$Code = $event->Code;
				
				$Time = $this->getTime();
				$Text = $this->getContent([$Name, $Email, $Roles, $IP, $Code]);
				$Content = $this->getContent([$Time, $Text]);
				
				Storage::append($this->getLogFile($this->getContent($this->Head)),$Content);
    }
	
		private function getTime(){
			$TimeArray = [date("D"),date("d/M/Y"),date("H:i:s")];
			return $this->getContent($TimeArray);
		}
	
		private function getContent($Ary){
			return implode("\t",$Ary);
		}

		private function getLogFile($head = ""){
			$Path = $this->Path . "/" . date("Ym") . ".log";
			return $this->PathVerify($Path,$head);
		}
	
		private function PathVerify($Path,$Default=""){
			if(!Storage::exists($Path)) Storage::put($Path,$Default);
			return $Path;
		}
}
