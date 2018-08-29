<?php

namespace App\Listeners;

use App\Events\LogCustomerUpdateDownload;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogCustomerUpdateDownloadListener
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
     * @param  LogCustomerUpdateDownload  $event
     * @return void
     */
    public function handle(LogCustomerUpdateDownload $event)
    {
			$Content = implode("\t",[date("D"),date("d/M/Y"),date("H:i:s"),$event->Customer,$event->Product,$event->Edition,$event->Package,$event->ReqVersion,$event->DwnVersion]);
			\Storage::append("customlog/CustomerUpdateDownload/".(date("YW")).".log",$Content);
    }
}
