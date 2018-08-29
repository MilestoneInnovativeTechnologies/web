<?php

namespace App\Listeners;

use App\Events\ConvUpdateUserActivity;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ConvUpdateUserActivityListener
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
     * @param  ConvUpdateUserActivity  $event
     * @return void
     */
    public function handle(ConvUpdateUserActivity $event)
    {
      $CSFC = new \App\Http\Controllers\ConversationSupportFileController();
			$CSFC->add_user($event->tkt, $event->user);
    }
}
