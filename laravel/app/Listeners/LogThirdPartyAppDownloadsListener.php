<?php

namespace App\Listeners;

use App\Events\LogThirdPartyAppDownloads;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogThirdPartyAppDownloadsListener
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
     * @param  LogThirdPartyAppDownloads  $event
     * @return void
     */

		private $Path = "customlog/ThirdPartyApplicationDownloads";
		private $Head = ["Day","Date","Time","Author","File Name","File Path","Status","IP Address","Browser"];
		private $Content = ["Author","Name","File","Status","IP","Browser"];
	
		public function handle(LogThirdPartyAppDownloads $event)
    {
        $ContentArray = $this->getTimeParams();
				foreach($this->Content as $F) $ContentArray[] = $event->$F;
				$Content = $this->getContent($ContentArray);
				$File = $this->getLogFile($this->getContent($this->Head));
				\Storage::append($File,$Content);
    }

		private function getTimeParams(){
				return [date("D"),date("d/M/Y"),date("H:i:s")];
		}
	
		private function getContent($Ary){
				return implode("\t",$Ary);
		}

		private function getLogFile($head = ""){
				$Path = $this->Path . "/" . date("Ym") . ".log";
				return $this->PathVerify($Path,$head);
		}
	
		private function PathVerify($Path,$Default=""){
				if(!\Storage::exists($Path)) \Storage::put($Path,$Default);
				return $Path;
    }
}
