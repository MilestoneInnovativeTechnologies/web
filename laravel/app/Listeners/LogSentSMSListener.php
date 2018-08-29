<?php

namespace App\Listeners;

use App\Events\LogSentSMS;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogSentSMSListener
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
     * @param  LogSentSMS  $event
     * @return void
     */
		private $Path = "customlog/SentSMS";
		private $EOP = "==========================================";

    public function handle(LogSentSMS $event)
    {
				$Content = $this->getContent($event);
				$File = $this->getLogFile();
				\Storage::append($File,$Content);
    }

		private function getContent($event){
			$Lines = ['Time: ' . date("D d/M/Y H:i:s")];
			$Lines[] = 'To: ' . $event->To . ' ('.$event->Name.')';
			$Lines[] = '';
			$Lines[] = $event->Sms;
			$Lines[] = $this->EOP;
			return implode("\r\n",$Lines);
		}

		private function getLogFile(){
			$Path = $this->Path . "/" . date("YW") . ".log";
			return $this->PathVerify($Path);
		}
	
		private function PathVerify($Path){
			if(!\Storage::exists($Path)) \Storage::put($Path,$this->EOP);
			return $Path;
		}
}
