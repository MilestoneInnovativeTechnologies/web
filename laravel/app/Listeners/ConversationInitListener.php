<?php

namespace App\Listeners;

use App\Events\ConversationInit;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ConversationInitListener
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
     * @param  ConversationInit  $event
     * @return void
     */
    public function handle(ConversationInit $event)
    {
      $CSFC = new \App\Http\Controllers\ConversationSupportFileController();
			$CSFC->create_support_file($event->tkt, $event->user);
    }
}
