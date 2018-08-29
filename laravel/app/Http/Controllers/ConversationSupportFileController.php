<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Storage;

class ConversationSupportFileController extends Controller
{
	
	protected $ticket_storage_path = 'ticket/convsupportfile';
	
	private function GetPathText($tkt){
		return implode("/",[$this->ticket_storage_path,$tkt.'.json']);
	}
  
	private function IsFileExists($Path){
		return Storage::exists($Path);
	}
	
	private function PutData($Path, $Data){
		Storage::put($Path,json_encode($Data));
	}
	
	private function GetData($Path){
		$this->VerifyFile($Path);
		return json_decode(Storage::get($Path),true);
	}
	
	private function CreateFile($Path, $Data){
		$this->PutData($Path,$Data);
		return $Path;
	}
	
	private function VerifyFile($Path){
		return ($this->IsFileExists($Path))?$Path:$this->CreateFile($Path,$this->GetInitFileData());
	}

	private function CreateSupportFile($tkt){
		$Path = $this->VerifyFile($this->GetPathText($tkt));
		$this->InitSupportFile($Path);
	}
	
	private function GetTimeFactor(){
		return time();
	}
	
	private function GetInitFileData(){
		$CR = $this->GetTimeFactor(); $Data = ['start' => $CR, 'last_active_time' => $CR, 'users' => [], 'user_start_time' => [], 'user_last_active_time' => [], 'last_conv_id' => 0, 'user_last_conv_id' => [], 'last_conv_time' => 0];
		return $Data;
	}
	
	private function InitSupportFile($Path){
		$this->PutData($Path,$this->GetInitFileData());
	}
	
	private function GetTktContent($tkt){
		$Path = $this->GetPathText($tkt);
		return $this->GetData($Path);
	}
	
	private function UpdateUser($Data, $user){
		return (in_array($user, $Data['users'])) ? $this->UpdateUserActivity($Data, $user, $this->GetTimeFactor()) : $this->AddUser($Data, $user, $this->GetTimeFactor());
	}
	
	private function UpdateUserActivity($Data, $User, $Time){
		return $this->SetUserActiveTime($Data, $User, $Time);
	}
	
	private function UpdateActivity($Data, $Time){
		$Data['last_active_time'] = $Time;
		return $Data;
	}
	
	private function UpdateLastConvTime($Data, $Time){
		$Data['last_conv_time'] = $Time;
		return $Data;
	}
	
	private function SetUserActiveTime($Data, $User, $Time){
		$Data['user_last_active_time'][$User] = $Time;
		return $Data;
	}
	
	private function AddUser($Data, $User, $Time){
		$Data['users'][] = $User;
		$Data['user_start_time'][$User] = $Time;
		return $this->SetUserActiveTime($Data, $User, $Time);
	}
	
	private function AddConv($Data, $User, $CID){
		$Data = $this->AddUserConv($Data, $User, $CID);		
	}
	
	private function UpdateUserConv($Data, $User, $CID){
		$Data['user_last_conv_id'][$User] = $CID;
		return $Data;
	}
	
	private function UpdateConv($Data, $CID){
		$Data['last_conv_id'] = $CID;
		return $Data;
	}
	
	
	
	
	
	
	
	public function add_user($tkt, $user){
		$Data = $this->GetTktContent($tkt);
		$Data = $this->UpdateUser($Data, $user);
		$Data = $this->UpdateActivity($Data, $this->GetTimeFactor());
		$this->PutData($this->GetPathText($tkt),$Data);
	}
	
	public function add_user_conv($tkt, $user, $conv){
		$Data = $this->GetTktContent($tkt);
		$Data = $this->UpdateConv($Data, $conv);
		$Data = $this->UpdateUserConv($Data, $user, $conv);
		$Data = $this->UpdateUser($Data, $user);
		$Data = $this->UpdateActivity($Data, $this->GetTimeFactor());
		$Data = $this->UpdateLastConvTime($Data, $this->GetTimeFactor());
		$this->PutData($this->GetPathText($tkt),$Data);
	}
	
	public function get_last_conv($tkt){
		$Data = $this->GetTktContent($tkt);
		return $Data['last_conv_id'];
	}
	
	public function create_support_file($tkt, $user){
		$this->CreateSupportFile($tkt);
		$this->add_user($tkt, $user);
	}
	
	public function update_user_activity($tkt, $user){
		$this->add_user($tkt, $user);
	}
	
	public function get_last_activity_time($tkt){
		$Data = $this->GetTktContent($tkt);
		return $Data['last_active_time'];
	}
	
	public function get_last_conv_time($tkt){
		$Data = $this->GetTktContent($tkt);
		return $Data['last_conv_time'];
	}
	
	public function get_users($tkt){
		$Data = $this->GetTktContent($tkt);
		return $Data['users'];
	}
	
	
	
	
	
	
	
	
	
	
	
}