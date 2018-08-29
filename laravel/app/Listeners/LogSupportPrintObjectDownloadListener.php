<?php

namespace App\Listeners;

use App\Events\LogSupportPrintObjectDownload;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogSupportPrintObjectDownloadListener
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
     * @param  LogSupportPrintObjectDownload  $event
     * @return void
     */

		private $Path = "customlog/SupportPrintObjectDownload";
		private $Head = ["Day","Date","Time","Mail","Code","Function Name","Requested IP","Browser"];
		private $Content = ["Mail","Code","Name","IP","Browser"];

	
		public function handle(LogSupportPrintObjectDownload $event)
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
			$Path = $this->Path . "/" . date("YW") . ".log";
			return $this->PathVerify($Path,$head);
		}
	
		private function PathVerify($Path,$Default=""){
			if(!\Storage::exists($Path)) \Storage::put($Path,$Default);
			return $Path;
		}
}
