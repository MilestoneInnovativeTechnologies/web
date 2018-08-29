<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Http\Controllers\KeyCodeController;

class ProductDownloadLinks extends Mailable
{
    use Queueable, SerializesModels;
	
		public $Product, $ProductId, $Description, $Key, $Editions, $Packages, $DownloadKeys = [], $To;
		protected $KCC;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($Product, $To)
    {
				$this->To = $To;
				$this->KCC = new KeyCodeController();
        $this->setBasic($Product);
				$this->setEdtnsPckgs($Product);
				$this->setDownloadKeys();
				
				$this->set_view_subject("emails.download_software", $this->Product . " Download Details");
    }
	
		private function set_view_subject($view, $subject){

				$this->view = $view;
				$this->subject = $subject;

		}


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view($this->view)->subject($this->subject);
    }
	
	private function setBasic($Product){
		$this->Product = $Product->name;
		$this->ProductId = $Product->code;
		$this->Description = $Product->description_public;
		$this->Key = $this->getNewMailKey();
	}
	
	private function setEdtnsPckgs($Product){
		$Editions = []; $Packages = [];
		$Product->Editions->each(function($item, $key) use(&$Editions,&$Packages) {
			if($item->Packages->isNotEmpty()){
				$EdPkgs = [];
				$item->Packages->each(function($item2, $key2) use(&$Packages,&$EdPkgs){
					$Packages[$item2->code] = [$item2->name,$item2->description_public,$item2->type];
					$EdPkgs[] = $item2->code;
				});
				$Editions[$item->code] = [$item->name,$item->pivot->description,$EdPkgs];
			}
		});
		$this->Editions = $Editions;
		$this->Packages = $Packages;
	}
	
	private function getNewMailKey(){
		return md5(date("YmdHis"));
	}
	
	private function setDownloadKeys(){
		$Key = $this->Key;
		$Product = $this->ProductId;
		foreach($this->Editions as $EdtnCode => $EdtnArray){
			foreach($EdtnArray[2] as $PkgCode){
				$this->DownloadKeys[$EdtnCode][$PkgCode] = $this->downloadKey($Key,$Product,$EdtnCode,$PkgCode);
			}
		}
	}
	
	private function downloadKey($Key,$Product,$Edition,$Package){
		$PArray = ['key','product','edition','package'];
		$PVals = [$Key,$Product,$Edition,$Package];
		return $this->KCC->KeyEncode($PArray,$PVals);
	}
	
}
