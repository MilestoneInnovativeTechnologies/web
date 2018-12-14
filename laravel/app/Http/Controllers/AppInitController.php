<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\KeyCodeController;

use Storage;

class AppInitController extends Controller
{
	
	private $GuestLogItems = ['pid','cmp','brc','app','ver','eml','phn'];
	private $CustomerLogItems = ['cus','seq','ver','key'];
	private $Path = "customlog/AppInit";
	protected $MapData = [];
	protected $VersionData = [];
  
	public function init($key){
		$Ary = $this->getPVArray($key);
		$isCustomer = $this->isCustomer($Ary);
		if($isCustomer) return $this->logCustomer($Ary);
		return $this->logGuest($Ary);
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
		return KeyCodeController::Encode(['cus','seq'],$MapDetails);
	}
	
	private function storeGuestLog($Ary){
		$GuestLogItems = $this->GuestLogItems;
		$ContentArray = $this->getLogContents($GuestLogItems,$Ary);
		$TimeArray = $this->getLogTime();
		$Content = implode("\t",array_merge($TimeArray,$ContentArray));
		$LogPath = $this->getGuestFilePath();
		Storage::append($LogPath,$Content);
	}
	
	private function logCustomer($Ary){
		$this->storeCustomerLog($Ary);
		$isVerChange = $this->isVersionChange($Ary);
		if(!$isVerChange) return 1;
		event(new \App\Events\UpdateCustomerVersion($Ary['cus'],$Ary['seq'],$Ary['ver'],$this->getVersion($Ary['cus'],$Ary['seq'])));
		return 1;
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
		return $this->addMap($request->only('pid','brc','cus','seq','prd','edn'),$frc);
	}
	
	private function addMap($Ary, $Frc){
		$isMap = $this->isMapExist($Ary['pid'],$Ary['brc']);
		if($isMap){
			if(!$Frc) return [true,false,$this->MapData[$Ary['pid']][$Ary['brc']],[$Ary['pid'],$Ary['brc'],$Ary['cus'],$Ary['seq'],$Ary['prd'],$Ary['edn']]];
			else return $this->updateMapData($Ary);
		}
		return $this->addMapData($Ary);
	}
	
	private function updateMapData($Ary){
		$PID = $Ary['pid']; $BRC = $Ary['brc'];
		$CUS = $Ary['cus']; $SEQ = $Ary['seq'];
		$PRD = $Ary['prd']; $EDN = $Ary['edn'];
		$MapData = $this->putMapData($PID, $BRC, $CUS, $SEQ, $PRD, $EDN);
		$this->setMapContent($MapData);
		return [true,true];
	}
	
	private function addMapData($Ary){
		$MapData = $this->MapData;
		$PID = $Ary['pid']; $BRC = $Ary['brc'];
		if(!array_key_exists($PID,$MapData)) $MapData[$PID] = [];
		if(!array_key_exists($BRC,$MapData[$PID])) $MapData[$PID][$BRC] = [];
		$CUS = $Ary['cus']; $SEQ = $Ary['seq']; $PRD = $Ary['prd']; $EDN = $Ary['edn'];
		$MapData = $this->putMapData($PID, $BRC, $CUS, $SEQ, $PRD, $EDN);
		$this->setMapContent($MapData);
		return [true,true];
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
		Storage::put($this->getLogMapFile(),$Content);
	}
	
}
