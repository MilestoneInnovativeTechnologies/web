<?php

namespace App\Listeners;

use App\Events\UpdateCustomerVersion;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateCustomerVersionDatabase
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
     * @param  UpdateCustomerVersion  $event
     * @return void
     */
    public function handle(UpdateCustomerVersion $event)
    {
			$CUS = $event->CUS;
			$SEQ = $event->SEQ;
			$VER = $event->VER;
			return \App\Models\CustomerRegistration::whereCustomer($CUS)->whereSeqno($SEQ)->update(['version'	=>	$VER]);
    }

}
