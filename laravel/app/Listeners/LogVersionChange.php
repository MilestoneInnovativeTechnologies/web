<?php

namespace App\Listeners;

use App\Events\UpdateCustomerVersion;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Storage;

class LogVersionChange
{
		protected $Path = "customlog/VersionUpdate";
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
     * @param  UpdateCustomerVersion  $event
     * @return void
     */
    public function handle(UpdateCustomerVersion $event)
    {
			$CUS = $event->CUS;
			$SEQ = $event->SEQ;
			$VER = $event->VER;
			$OLD = $event->OLD;
			
			$File = $this->getVersionLogFile();
			$Content = $this->getContent($CUS, $SEQ, $VER, $OLD);
			
			Storage::append($File,$Content);
    }
	
		private function getVersionLogFile(){
			return $this->Path . "/" . date("Ym") . ".log";
		}
	
		private function getContent($CUS, $SEQ, $VER, $OLD){
			$ContentArray = [date("D"),date("d/M/Y"),date("H:i:s"), $CUS, $SEQ, $OLD, $VER];
			return implode("\t",$ContentArray);
		}
}
