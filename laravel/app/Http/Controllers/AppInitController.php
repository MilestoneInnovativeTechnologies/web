<?php

namespace App\Http\Controllers;

use App\Models\CustomerRegistration;
use Illuminate\Http\Request;

use Storage;

class AppInitController extends Controller
{
	
	private $GuestLogItems = ['pid','cmp','brc','app','ver','eml','phn'/*,'hdk','prs','ops','com','dbn','isd'*/];
	private $CustomerLogItems = ['cus','seq','prd','edn','ver','key'];
	private $Path = "customlog/AppInit"; //Static function 'SetProductVersion','GetProductVersion' using same path
	protected $MapData = [];
	protected $VersionData = [];
  
	public function init($key){
		$Ary = $this->getPVArray($key);
		$isCustomer = $this->isCustomer($Ary);
		if($isCustomer) return $this->logCustomer($Ary);
		if(!$this->isIgnorable($Ary)) return $this->logGuest($Ary);
		return 0;
	}
	
	private function getPVArray($key){
		list($P,$V) = KeyCodeController::Decode($key);
		return array_combine($P,$V);
	}
	
	private function isCustomer($Ary){
		return array_key_exists('cus',$Ary);
	}
	
	private function logGuest($Ary){
		$this->storeGuestLog($Ary);
		$isMap = $this->isMapExist($Ary['pid'],$Ary['brc']);
		if(!$isMap) return 0;
		$MapDetails = $this->getMapDetails($Ary['pid'],$Ary['brc']);
		$this->logMap($Ary['pid'],$Ary['brc'],$MapDetails);
		$MapData = $this->unsetMapData($Ary['pid'],$Ary['brc']);
		$this->setMapContent($MapData);
		return KeyCodeController::Encode(['cus','seq','prd','edn'],$MapDetails);
	}
	
	private function storeGuestLog($Ary){
		$GuestLogItems = $this->GuestLogItems;
		$ContentArray = $this->getLogContents($GuestLogItems,$Ary);
		array_push($ContentArray,"","","","","","");
		$TimeArray = $this->getLogTime();
		$Content = implode("\t",array_merge($TimeArray,$ContentArray));
		$LogPath = $this->getGuestFilePath();
		Storage::append($LogPath,$Content);
	}
	
	private function logCustomer($Ary){
		$version = $this->storeCustomerLog($Ary);
		$isVerChange = $this->isVersionChange($Ary);
		if($isVerChange) event(new \App\Events\UpdateCustomerVersion($Ary['cus'],$Ary['seq'],$Ary['ver'],$this->getVersion($Ary['cus'],$Ary['seq'])));
		return $version;
	}
	
	private function getLogContents($Fields, $Array){
		return array_map(function($Field) use ($Array){
			if(array_key_exists($Field,$Array)) return $Array[$Field];
			return "";
		},$Fields);
	}
	
	private function getLogTime(){
		return [date("D"),date("d/M/Y"),date("H:i:s")];
	}
	
	public function getGuestFilePath($NegOffset = 0){
		$File = "G" . (date("YW",strtotime("-".$NegOffset." week"))) . ".log";
		return $this->PrepPath($File);
	}
	
	public function getCustomerFilePath($NegOffset = 0){
		$File = "C" . (date("YW",strtotime("-".$NegOffset." week"))) . ".log";
		return $this->PrepPath($File);
	}
	
	private function isMapExist($PID,$BRC){
		$this->MapData = $MapData = $this->getMapData();
		return (array_key_exists($PID,$MapData) && array_key_exists($BRC,$MapData[$PID]));
	}
	
	private function getMapData(){
		return json_decode(Storage::get($this->getMapFile()),true);
	}
	
	private function getMapFile(){
		$File = "map.json";
		$Path = $this->PrepPath($File);
		return $this->PathVerify($Path,"[]");
	}
	
	private function getMapDetails($PID,$BRC){
		return $this->MapData[$PID][$BRC];
	}
	
	private function unsetMapData($PID,$BRC){
		$MapData = $this->MapData;
		unset($MapData[$PID][$BRC]); if(empty($MapData[$PID])) unset($MapData[$PID]);
		return $MapData;
	}
	
	private function setMapContent($MapData){
		Storage::put($this->getMapFile(),json_encode($MapData));
	}
	
	public function map(Request $request){
		$Fields = ['pid','brc','cus','seq','prd','edn']; $AllFields = true;
		foreach($Fields as $Field) $AllFields = ($AllFields && $request->$Field);
		if(!$AllFields) return ['false','Required fields are empty'];
		$frc = ($request->frc && $request->frc != "0" && $request->frc != 0) ? true : false;
		return $this->addMap($request->only('pid','brc','cus','seq','prd','edn','hdk','prs','ops','com','dbn','isd'),$frc);
	}
	
	private function addMap($Ary, $Frc){
		$isMap = $this->isMapExist($Ary['pid'],$Ary['brc']);
		if($isMap){
			if(!$Frc) return [true,false,$this->MapData[$Ary['pid']][$Ary['brc']],[$Ary['pid'],$Ary['brc'],$Ary['cus'],$Ary['seq'],$Ary['prd'],$Ary['edn'],$Ary['hdk'],$Ary['prs'],$Ary['ops'],$Ary['com'],$Ary['dbn'],$Ary['isd']]];
			else return $this->updateMapData($Ary);
		}
		return $this->addMapData($Ary);
	}
	
	private function updateMapData($Ary){
		$PID = $Ary['pid']; $BRC = $Ary['brc'];
		$CUS = $Ary['cus']; $SEQ = $Ary['seq'];
		$MapData = $this->putMapData($PID, $BRC, $CUS, $SEQ, $Ary['prd'], $Ary['edn']);
		$this->setMapContent($MapData);
		$this->updateRegistration($CUS, $SEQ, $Ary);
		return [true,true];
	}
	
	private function addMapData($Ary){
		$MapData = $this->MapData;
		$PID = $Ary['pid']; $BRC = $Ary['brc'];
		if(!array_key_exists($PID,$MapData)) $MapData[$PID] = [];
		if(!array_key_exists($BRC,$MapData[$PID])) $MapData[$PID][$BRC] = [];
		$CUS = $Ary['cus']; $SEQ = $Ary['seq'];
		$MapData = $this->putMapData($PID, $BRC, $CUS, $SEQ, $Ary['prd'], $Ary['edn']);
		$this->setMapContent($MapData);
        $this->updateRegistration($CUS, $SEQ, $Ary);
		return [true,true];
	}

	private function updateRegistration($CUS, $SEQ, $Ary){
        $Data = array_combine(['product_id','hard_disk','processor','os','computer','database','installed_on'],array_only($Ary,['pid','hdk','prs','ops','com','dbn','isd']));
        $Data['installed_on'] = date('Y-m-d',strtotime($Data['installed_on']));
        CustomerRegistration::where('customer',$CUS)->where('seqno',$SEQ)->update($Data);
    }
	
	private function putMapData($PID, $BRC, $CUS, $SEQ, $PRD, $EDN){
		$MapData = $this->MapData;
		$MapData[$PID][$BRC] = [$CUS, $SEQ, $PRD, $EDN];
		$this->MapData = $MapData;
		return $MapData;
	}

	private function storeCustomerLog($Ary){
		$CustomerLogItems = $this->CustomerLogItems;
		$ContentArray = $this->getLogContents($CustomerLogItems,$Ary);
		$TimeArray = $this->getLogTime();
		$Content = implode("\t",array_merge($TimeArray,$ContentArray));
		$LogPath = $this->getCustomerFilePath();
		Storage::append($LogPath,$Content);
		return static::GetProductVersion($Ary['prd'],$Ary['edn']);
	}
	
	private function isVersionChange($Ary){
		$this->VersionData = $VersionData = $this->getVersionData();
		$CUS = $Ary['cus']; $SEQ = $Ary['seq'];
		$Version = $this->getVersion($CUS, $SEQ);
		$VER = $Ary['ver'];
		return ($VER != $Version);
	}
	
	private function getVersionData(){
		return json_decode(Storage::get($this->getVersionFile()), true);
	}
	
	private function getVersionFile($NegOffset = 0){
		$File = "V" . (date("Ym",strtotime("-".$NegOffset." month"))) . ".json";
		return $this->PathVerify($this->PrepPath($File),"{}");
	}
	
	private function getVersion($CUS, $SEQ){
		$VersionData = $this->VersionData;
		return (array_key_exists($CUS,$VersionData) && array_key_exists($SEQ,$VersionData[$CUS])) ? $VersionData[$CUS][$SEQ] : 0;
	}
	
	private function PrepPath($File){
		return $this->Path . "/" . $File;
	}
	
	static function PathVerify($Path,$Default=""){
		if(!Storage::exists($Path)) Storage::put($Path,$Default);
		return $Path;
	}
	
	private function logMap($pid, $brc, $map){
		$Ary = $this->getlogMapArray();
		if(!array_key_exists($pid,$Ary)) $Ary[$pid] = [];
		if(!array_key_exists($brc,$Ary[$pid])) $Ary[$pid][$brc] = [];
		$Ary[$pid][$brc] = $map;
		$this->setLogMap($Ary);
	}
	
	public function getlogMapArray($NegOffset = 0){
		$File = $this->getLogMapFile($NegOffset);
		return json_decode(Storage::get($File),true);
	}
	
	private function getLogMapFile($NegOffset = 0){
		$File = "Mapped" . (date("Ym",strtotime("-".$NegOffset." month"))) . ".json";
		return $this->PathVerify($this->PrepPath($File),"{}");
	}
	
	private function setLogMap($Content){
		if(is_array($Content)) return $this->setLogMap(json_encode($Content));
		return Storage::put($this->getLogMapFile(),$Content);
	}

	private function isIgnorable($Ary){
        $ignorable = $this->getIgnorableContents();
        foreach ($ignorable as $key => $ignoreArray){
            if(!empty($ignoreArray) && in_array($Ary[$key],$ignoreArray))
                return true;
        }
        return false;
    }

    public function getIgnorableContents(){
        return json_decode(Storage::get($this->getIgnorableFile()),true);
    }

    private function getIgnorableFile(){
        $File = "ignore.json";
        return $this->PathVerify($this->PrepPath($File),json_encode(["pid"=>[],"hdk"=>[],"prs"=>[]]));
    }

    public function addIgnorableContent($Field,$Value){
        $ignorable = $this->getIgnorableContents();
        if(!array_key_exists($Field,$ignorable)) $ignorable[$Field] = [];
        if(!in_array($Value,$ignorable[$Field])) array_push($ignorable[$Field],$Value);
        $this->setIgnorableContents($ignorable);
    }

    private function setIgnorableContents($ignorable){
	    $File = $this->getIgnorableFile();
	    Storage::put($File,json_encode($ignorable));
    }

	static function SetProductVersion($PRD,$EDN,$VER){
	    $File = "customlog/AppInit/PRDVERSION.json";
	    $VS = \Illuminate\Support\Facades\Storage::exists($File) ? json_decode(\Illuminate\Support\Facades\Storage::get($File),true) : [];
	    if(!array_key_exists($PRD,$VS)) $VS[$PRD] = []; if(!array_key_exists($EDN,$VS[$PRD])) $VS[$PRD][$EDN] = '0';
	    $VS[$PRD][$EDN] = $VER; \Illuminate\Support\Facades\Storage::put($File,json_encode($VS));
    }

    static function GetProductVersion($PRD,$EDN){
        $File = "customlog/AppInit/PRDVERSION.json";
        $VS = \Illuminate\Support\Facades\Storage::exists($File) ? json_decode(\Illuminate\Support\Facades\Storage::get($File),true) : [];
        return (array_key_exists($PRD,$VS) && array_key_exists($EDN,$VS[$PRD])) ? $VS[$PRD][$EDN] : 0;
    }
	
}
