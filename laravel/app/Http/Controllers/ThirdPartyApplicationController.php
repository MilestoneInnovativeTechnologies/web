<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ThirdPartyApplicationController extends Controller
{

	protected $Model;
	
	public $max_download_size = 25;
	
	public function __construct(){
		$Apply = ['store','updatefile','update','download'];
		$this->middleware(function($request, $next){
			$segs = $request->segments();  switch (count($segs)){ case 2: list($prefix,$action) = $segs; break; case 3: list($prefix,$code,$action) = $segs; break; case 4: list($prefix,$code,$action,$key) = $segs; break; default: list($prefix) = $segs; break; }
			$this->Model = $Model = (isset($code)) ? \App\Models\ThirdPartyApplication::find($code) : new \App\Models\ThirdPartyApplication;
			return (in_array($action,$Model->available_actions)) ? $next($request) : redirect()->back()->with(['info'	=>	true, 'type'	=>	'warning', 'text'	=>	'The requested action is not available right now.']);
		})->only($Apply);
	}
	
	public function store(Request $request){
		//return $request->all();
		$val_rules = $this->Model->_validation();
		$validator = \Validator::make($request->all(),$val_rules['rules'],$val_rules['messages']);
		if($validator->fails()) return redirect()->back()->withErrors($validator)->withInput();
		$this->Model = call_user_func_array([$this->Model,'create_new'],$request->only('code','name','description','version','vendor_url','public'));
		if($request->hasFile('file')){
			$FileInfo = $this->upload_file($request->file('file'));
			$this->Model->data_update($FileInfo);
		}
		return redirect()->route('tpa.index')->with(['info' => true, 'type' => 'success', 'text' => 'New software details created successfully.']);
	}
	
	public function updatefile(Request $request){
		if($request->hasFile('file')){
			if($this->Model->file) $this->delete_file($this->Model->file);
			$FileInfo = $this->upload_file($request->file('file'));
			$this->Model->data_update($FileInfo);
		} elseif($request->filename) {
			$request->merge(['file' => $request->filename]);
			$this->Model->data_update($request->only(['file','size','extension']));
		} else return redirect()->back()->with(['info' => true, 'type' => 'warning', 'text' => 'Nothing to update.']);
		return redirect()->back()->with(['info' => true, 'type' => 'success', 'text' => 'File details updated successfully.']);
	}
	
	public function update(Request $request){
		$val_rules = $this->Model->_validation();
		if($this->Model->code == $request->code) unset($val_rules['rules']['code']);
		$validator = \Validator::make($request->all(),$val_rules['rules'],$val_rules['messages']);
		if($validator->fails()) return redirect()->back()->withErrors($validator)->withInput();
		$this->Model = call_user_func_array([$this->Model,'data_update'],[$request->only('code','name','description','version','vendor_url','public')]);
		return redirect()->back()->with(['info' => true, 'type' => 'success', 'text' => 'Software details updated successfully.']);
	}
	
	public function download($code,$key){
		$Array = \App\Http\Controllers\KeyCodeController::Decode($key); $Data = array_combine($Array[0],$Array[1]);
		$StatusArray = ['DOWNLOAD SUBMITTED','DOWNLOAD LIMIT EXCEEDS','FILE MISSING'];
		$Status = (\Storage::disk($Data['disk'])->exists($Data['file'])) ? (( intval($Data['downloads'])>0 && !$this->Model->is_downloadable_limit($Data['code']) ) ? 1 : 0) : 2;
		event(new \App\Events\LogThirdPartyAppDownloads($Data,$StatusArray[$Status]));
		if($Status !== 0) return $StatusArray[$Status];
		$As = $Data['name'] .'.'. $Data['extension'];
		if(\Storage::disk($Data['disk'])->size($Data['file']) < 1024*1024*$this->max_download_size)
			return response()->download(\Storage::disk($Data['disk'])->getDriver()->getAdapter()->applyPathPrefix($Data['file']),$As);
		return redirect(\Storage::disk($Data['disk'])->url($Data['file']));
	}
	
	private function delete_file($file){
		\Storage::disk($this->Model->storage_disk)->delete($file);
	}
	
	private function upload_file($request_file){
		$Disk = $this->Model->storage_disk; $Path = $this->Model->storage_path;
		$extension = $request_file->extension()?:mb_substr(mb_strrchr($request_file->getClientOriginalName(),'.'),1);
		$size = $request_file->getClientSize();
		$Name = mb_strstr($request_file->hashName(),".",true);
		$File = $Name . '.' . $extension;
		$path = $request_file->storeAs($Path,$File,$Disk);
		return ['file' => $path, 'size' => $size, 'extension' => $extension];
	}
	
}
