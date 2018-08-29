<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\KeyCodeController;

use Storage;

class CommonController extends Controller
{
  
	public function softwareUpdateDownload($key){
		list($PArray, $VArray) = KeyCodeController::Decode($key);
		$Array = array_combine($PArray,$VArray);
		$Data = \App\Models\PackageVersion::where(["product"	=>	$Array['product'],"edition"	=>	$Array['edition'],"package"	=>	$Array['package'],"status"	=>	'APPROVED'])
			->latest("version_sequence")
			->with(["Package"	=>	function($Q){
				$Q->whereType("Update");
			},"Product","Edition"])
			->get()
			->filter(function($V,$K){
				return $V->Package;
			});
		if($Data->count()){
			$First = $Data->first();
			if(array_key_exists('key',$Array)) event(new \App\Events\LogMailDownload($Array['key'],$First->Product->name,$First->Edition->name,"Download Submitted"));
			elseif(array_key_exists('user',$Array)) event(new \App\Events\LogDirectDownload($Array['user'],$Array,$First));
			return $this->download($First->file);
		} else {
			if(array_key_exists('key',$Array)) event(new \App\Events\LogMailDownload($Array['key'],$Array['product'],$Array['edition'],"Content doesn't exist"));
			elseif(array_key_exists('user',$Array)) event(new \App\Events\LogDirectDownload($Array['user'],$Array,null));
			return response("Requested content for download doesn't exist.",404);
		}
	}
  
	public function softwareOnetimeDownload($key){
		list($PArray, $VArray) = KeyCodeController::Decode($key);
		$Array = array_combine($PArray,$VArray);
		$Data = \App\Models\PackageVersion::where(["product"	=>	$Array['product'],	"edition"	=>	$Array['edition'],	"package"	=>	$Array['package'],	"status"	=>	'APPROVED'])
			->latest("version_sequence")
			->with(["Package"	=>	function($Q){
				$Q->whereType("Onetime");
			},"Product","Edition"])
			->get()
			->filter(function($V,$K){
				return $V->Package;
			});
		if($Data->count()){
			$First = $Data->first();
			if(array_key_exists('key',$Array)) event(new \App\Events\LogGuestSoftwareDownload($Array['key'],$First->Product->name,$First->Edition->name,$First->Package->name,"Download Submitted"));
			elseif(array_key_exists('user',$Array)) event(new \App\Events\LogDirectDownload($Array['user'],$Array,$First));
			return $this->download($First->file);
		} else {
			if(array_key_exists('key',$Array)) event(new \App\Events\LogGuestSoftwareDownload($Array['key'],$Array['product'],$Array['edition'],$Array['package'],"Content doesn't exist"));
			elseif(array_key_exists('user',$Array)) event(new \App\Events\LogDirectDownload($Array['user'],$Array,null));
			return response("Requested content for download doesn't exist.",404);
		}
	}
	
	public function softwareDownload($key){
		list($PArray, $VArray) = KeyCodeController::Decode($key);
		$Ary = array_combine($PArray, $VArray);
		if($Ary['expiry'] < time()) return response("Link Expired.",404);
		if(array_key_exists('customer_download',$Ary)) return $this->customerUpdateDownload($Ary);
		$Fun = 'software'.$Ary['type'].'Download';
		return $this->$Fun($key);
	}
	
	public function ticketUploadedFileDownload($tkt, $cid){
		$DBData = \App\Models\TicketCoversation::withoutGlobalScopes()->where(['ticket' => $tkt, 'id' => $cid])->first();
		if($DBData){
			$CA = json_decode($DBData->content,true);
			$File = $CA['file']; $Name = $CA['name'];
			event(new \App\Events\LogTktUploadedFileDownload($tkt, $cid, $Name, $File));
			return $this->download($File,$Name);
		}
		event(new \App\Events\LogTktUploadedFileDownload($tkt, $cid, 'UNKNOWN', 'DATABASE RECORD DOES NOT EXISTS.'));
		return response("Record Empty.",404);
	}
	
	public function support_print_object_download($key){
		list($PArray, $VArray) = KeyCodeController::Decode($key); $Ary = array_combine($PArray, $VArray);
		event(new \App\Events\LogSupportPrintObjectDownload($Ary['mail'], $Ary['code'], $Ary['name']));
		return $this->download($Ary['link']);
	}
	
	public function print_object_download($key){
		list($PArray, $VArray) = KeyCodeController::Decode($key); $Ary = array_combine($PArray, $VArray);
		event(new \App\Events\LogPrintObjectDownloadFromMail($Ary['customer'], $Ary['product'], $Ary['function_name'], $Ary['po_code']));
		return $this->download($Ary['file']);
	}
	
	public function customerUpdateDownload($Array){
		$ORM = \App\Models\PackageVersion::where(["product"	=>	$Array['product'],"edition"	=>	$Array['edition'],"package"	=>	$Array['package'],"status"	=>	'APPROVED'])->latest("version_sequence")->take(1)
			->with('Product','Edition','Package');
		if($ORM->count()){
			$Data = $ORM->first();
			event(new \App\Events\LogCustomerUpdateDownload($Array['customer'],$Array['version'],$Data));
			return $this->download($Data->file);//response()->download(storage_path("app/".$Data->file));
		}
		return response("Requested content for download doesn't exist.",404);
	}
	
	private function download($Path, $As = null){
		if(Storage::size($Path)/1048576 < 30) return response()->download(storage_path("app/".$Path),$As);
		$Filename = basename($Path);
		$Target = "FullSetup/" . $Filename;
		
		return redirect($Target);
		
	}
	
	static function ItemIDsJoinForDB($IDArray){
		return "-" . implode("-", (array) $IDArray) . "-";
	}
	
	static function ItemIDsExtractFromDB($IDs){
		return explode("-",mb_substr($IDs,1,-1));
	}
	
	public function general_upload_form($Key){
		list($PAry, $VAry) = KeyCodeController::Decode($Key); $Ary = array_combine($PAry, $VAry);
		$GUF = \App\Models\GeneralUpload::find($Ary['code']);
		return view('home.general_upload_form',compact('GUF'));
	}
	
	public function generalform_uploaded_download($Key){
		list($PAry, $VAry) = KeyCodeController::Decode($Key); $Ary = array_combine($PAry, $VAry);
		$GUF = \App\Models\GeneralUpload::find($Ary['code']);
		//return $GUF->name.mb_strrchr($GUF->file,'.');
		return $this->disk_download($GUF->upload_disk,$GUF->file,(date('ymdHis.',$GUF->time).$GUF->name.mb_strrchr($GUF->file,'.')));
	}
	
	private function disk_download($Disk, $Path, $As = null){
		if(Storage::disk($Disk)->exists($Path)) return response()->download(Storage::disk($Disk)->getDriver()->getAdapter()->applyPathPrefix($Path),$As);
	}
	
	public function browser_display($path, $disk = 'local'){
		return response()->file(Storage::disk($disk)->getDriver()->getAdapter()->applyPathPrefix($path));
	}
	
	public function file_download($path, $disk = 'local'){
		return response()->download(Storage::disk($disk)->getDriver()->getAdapter()->applyPathPrefix($path));
	}
	
	public function database_backup_download($Key){
		if(!$this->getAuthUser()) return '';
		list($PAry, $VAry) = KeyCodeController::Decode($Key); $Ary = array_combine($PAry, $VAry);
		if($DBB = \App\Models\DatabaseBackup::find($Ary['id'])) event(new \App\Events\LogDatabaseBackupDownload($this->getAuthUser(),$DBB));
		if(\App\Models\Customer::find($Ary['customer'])) return $this->disk_download('local',$Ary['link'],$Ary['name'].(date('.dMy.hia.',strtotime($DBB->updated_at)).$DBB->format)); //return $this->file_download($Ary['link']);
	}
	
	private function getAuthUser(){
		return (Auth()->user())?:Auth()->guard('api')->user();
	}
	
	public function webmail_track($key){
		$this->mwb_add_trck($key);
		return $this->browser_display('emails/copyright.png');
	}
	
	private function mwb_add_trck($key){
		$data = $this->Key2Array($key);
		(new \App\Http\Controllers\WebMailLogController)->new_receipt($data['mail'],$data['receiver']);
	}
	
	private function Key2Array($Key){
		list($PAry, $VAry) = KeyCodeController::Decode($Key); return array_combine($PAry, $VAry);
	}
}