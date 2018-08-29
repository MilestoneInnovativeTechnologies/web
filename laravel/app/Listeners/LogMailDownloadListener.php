<?php

namespace App\Listeners;

use App\Events\LogMailDownload;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Storage;

class LogMailDownloadListener
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
     * @param  LogMailDownload  $event
     * @return void
     */
    public function handle(LogMailDownload $event)
    {
			$Content = implode("\t",[date("D d/M/Y"),date("H:i:s"),$event->Key,$event->Product,$event->Edition,$event->Text]);
			Storage::append("customlog/MailUpdateDownload/".(date("YW")).".log",$Content);
    }
}
