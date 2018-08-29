<?php

namespace App\Listeners;

use App\Events\LogDirectDownload;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Storage;

class LogDirectDownloadListener
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
     * @param  LogDirectDownload  $event
     * @return void
     */
		private $Path = "customlog/DirectLinkDownload";
		private $Head = ["Day","Date","Time","Author","Author Roles","Product","Edition","Package","Type","Action"];
		private $Content = ["Author","AuthorRoles","Product","Edition","Package","Type","Action"];

    public function handle(LogDirectDownload $event)
    {
        $ContentArray = $this->getTimeParams();
				foreach($this->Content as $F) $ContentArray[] = $event->$F;
				$Content = $this->getContent($ContentArray);
				$File = $this->getLogFile($this->getContent($this->Head));
				Storage::append($File,$Content);
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
			if(!Storage::exists($Path)) Storage::put($Path,$Default);
			return $Path;
		}
}
