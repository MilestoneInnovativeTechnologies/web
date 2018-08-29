<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GeneralUpload as GU;

class GeneralUploadController extends Controller
{
	
	public $upload_disk = 'generalupload';
	
	
	public function index(){
		return view('gu.index');
	}
	
	public function form(){
		return view('gu.form');
	}
	
	public function details($code){
		return view('gu.details',compact('code'));
	}
	
	public function edit($code){
		$gu = GU::find($code);
		return view('gu.edit',compact('gu'));
	}
	
	public function update($code, Request $request){
		$this->validate($request,$this->validate_rules);
		$gu = GU::find($code); $gu->update($request->only('name','description','customer','ticket','overwrite'));
		return redirect()->back()->with(['info' => true, 'type' => 'success', 'text' => 'Form updated successfully']);
	}
	
	public function store(Request $request){
		$this->validate($request,$this->validate_rules);
		$CA = $this->Request2Create($request->all());
		$GU = GU::create($CA);
		return redirect()->route('gu.index')->with(['info' => true, 'type' => 'success', 'text' => 'Form Created successfully. Link to form is <br>'.$GU->Form]);
	}
	
	public function store_file($file, $code){
		if($file->extension()) return $file->store($this->UploadFilePath($code),$this->upload_disk);
		$ext = mb_strrchr($file->getClientOriginalName(),'.');
		$filename = $file->hashName(); if(mb_substr($filename,-1) == ".") $filename = mb_substr($filename,0,-1);
		return $file->storeAs($this->UploadFilePath($code),$filename.$ext,$this->upload_disk);
	}
	
	public function store_from_taskwork($details){
		$CA = $this->Request2Create($details);
		$GU = GU::create($CA);
		$Link = $this->GetFormLink($GU);
		return array_merge($GU->toArray(),['form' => $Link]);
	}
	
	public function delete($code){
		$this->drop($code);
		$this->UpdateGU($code,['deleted' => 'Y']);		
		return redirect()->route('gu.index')->with(['info' => true, 'type' => 'warning', 'text' => 'Form deleted successfully']);
	}
	
	public function drop($code){
		//$gu = GU::find($code);
		\Storage::disk($this->upload_disk)->deleteDirectory($this->UploadFilePath($code));
		$gu = $this->UpdateGU($code,['file' => null, 'time' => 0, 'size' => 0]);
		return redirect()->back()->with(['info' => true, 'type' => 'warning', 'text' => 'File in the form deleted successfully']);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function send_formlink_email(Request $request){
		if(filter_var($request->email,FILTER_VALIDATE_EMAIL) === false) return 'Invalid Email.';
		if(!$request->form) return 'Invalid Form Code.';
		\Mail::queue(new \App\Mail\GUFLink(GU::find($request->form),$request->email));
		return $request->all();
	}
	
	public function send_formfile_email(Request $request){
		if(filter_var($request->email,FILTER_VALIDATE_EMAIL) === false) return 'Invalid Email.';
		if(!$request->form) return 'Invalid Form Code.';
		\Mail::queue(new \App\Mail\GUFDownloadLink(GU::find($request->form),$request->email));
		return $request->all();
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	private $validate_rules = [
    'customer' => 'nullable|exists:partners,code',
    'ticket' => 'nullable|exists:tickets,code',
	];
	
	private function Request2Create($request){
		$FA = ['name','description','customer','ticket','file','size','time','overwrite','created_by','deleted'];
		$CA = ['code' => null, 'created_by' => $this->getAuthUser()->partner];
		foreach($FA as $FN) if(array_key_exists($FN,$request) && ($request[$FN] || is_null($request[$FN]))) $CA[$FN] = $request[$FN];
		return $CA;
	}
	
	private function GetFormLink($GU){
		if(!is_object($GU)) $GU = GU::find($GU);
		$Name = $GU->name; $Desc = $GU->description; $Code = $GU->code;
		$PAry = ['name','description','code']; $VAry = [$Name,$Desc,$Code];
		$Key = \App\Http\Controllers\KeyCodeController::Encode($PAry, $VAry);
		return Route('general.uploadform',$Key);
	}
	
	private function GetDownloadLink($GU){
		if(!is_object($GU)) $GU = GU::find($GU);
		$Name = $GU->name; $Desc = $GU->description; $Code = $GU->code;
		$PAry = ['name','description','code']; $VAry = [$Name,$Desc,$Code];
		$Key = \App\Http\Controllers\KeyCodeController::Encode($PAry, $VAry);
		return Route('download.generalform.uploaded',$Key);
	}
	
	private function UploadFilePath($Code){
		return $Code;
	}
	
	private function getAuthUser(){
		return (Auth()->user())?:(Auth()->guard("api")->user());
	}
	
	
	static function AlterOverwrite($code){
		$AVs = ['Y' => 'N', 'N' => 'Y'];
		$gu = GU::find($code);
		$gu->update(['overwrite' => $AVs[$gu->overwrite]]);
		return $gu;
	}
	static function DropFile($code){
		$UPArray = ['file' => null, 'time' => 0, 'size' => 0];
		$gu = GU::find($code);
		$gu->update($UPArray);
		return $gu;
	}
	static function DeleteForm($code){
		$UPArray = ['deleted' => 'Y'];
		$gu = GU::find($code);
		$gu->update($UPArray);
		return $gu;
	}
	static function UpdateGU($Code, $Data){
		$gu = GU::find($Code);
		$gu->update($Data);
		return $gu;
	}

}
