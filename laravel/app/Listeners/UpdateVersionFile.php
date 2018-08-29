<?php

namespace App\Listeners;

use App\Events\UpdateCustomerVersion;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Storage;

class UpdateVersionFile
{
		
		private $Path = "customlog/AppInit";
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
			
			$Data = $this->getVersionData();
			
			if(!array_key_exists($CUS,$Data)) $Data[$CUS] = [];
			if(!array_key_exists($SEQ,$Data[$CUS])) $Data[$CUS][$SEQ] = 0;
			
			$Data[$CUS][$SEQ] = $VER;
			
			$this->setVersionData($Data);
			
    }

		private function getVersionData(){
			return json_decode(Storage::get($this->getVersionFile()), true);
		}

		private function getVersionFile(){
			$Path = $this->Path . "/V" . date("Ym") . ".json";
			return $this->PathVerify($Path,[]);
		}
		
		private function setVersionData($Data){
			$File = $this->getVersionFile();
			Storage::put($File,json_encode($Data));
		}
	
		private function PathVerify($Path,$Default=""){
			if(!Storage::exists($Path)) Storage::put($Path,$Default);
			return $Path;
		}

}
