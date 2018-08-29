<?php

namespace App\Listeners;

use App\Events\LogSentMail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Storage;

class LogSentMailListener implements ShouldQueue
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
     * @param  LogSentMail  $event
     * @return void
     */
		private $Path = "customlog/SentMail";
		private $Head = ["Day","Date","Time","Key","Subject","To","CC","BCC","View"];
		private $Content = ["Key","Subject","To","CC","BCC","View"];

		public function handle(LogSentMail $event)
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
