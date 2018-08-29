<?php

namespace App\Listeners;

use App\Events\LogGuestSoftwareDownload;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Storage;

class LogGuestSoftwareDownloadListener
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
     * @param  LogGuestSoftwareDownload  $event
     * @return void
     */
    public function handle(LogGuestSoftwareDownload $event)
    {
			$Content = implode("\t",[date("D d/M/Y"),date("H:i:s"),$event->Key,$event->Product,$event->Edition,$event->Package,$event->Text]);
			Storage::append("customlog/GuestMailDownload/".(date("YW")).".log",$Content);
    }
}
