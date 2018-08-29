<?php

namespace App\Listeners;

use App\Events\LogPrintObjectDownloadFromMail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogPrintObjectDownloadFromMailListener
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
     * @param  LogPrintObjectDownloadFromMail  $event
     * @return void
     */

		private $Path = "customlog/PrintObjectDownloadFromMail";
		private $Head = ["Day","Date","Time","Customer", "Product", "Function", "POCode", "IP", "Browser"];
		private $Content = ["Customer", "Product", "Function", "POCode", "IP", "Browser"];

	
		public function handle(LogPrintObjectDownloadFromMail $event)
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
