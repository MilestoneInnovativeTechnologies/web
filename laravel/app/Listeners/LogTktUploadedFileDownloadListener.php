<?php

namespace App\Listeners;

use App\Events\LogTktUploadedFileDownload;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Storage;

class LogTktUploadedFileDownloadListener
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
     * @param  LogTktUploadedFileDownload  $event
     * @return void
     */

		private $Path = "customlog/TicketChatUploadedFileDownload";
		private $Head = ["Day","Date","Time","Chat ID","Ticket","File Name","File Path","Requested IP","Browser"/*,"Browser version","OS Platform"*/];
		private $Content = ["ConversationID","Ticket","Name","File","IP","Browser"/*,"Version","OS"*/];

    public function handle(LogTktUploadedFileDownload $event)
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
			$Path = $this->Path . "/" . date("Ym") . ".log";
			return $this->PathVerify($Path,$head);
		}
	
		private function PathVerify($Path,$Default=""){
			if(!Storage::exists($Path)) Storage::put($Path,$Default);
			return $Path;
		}
}
