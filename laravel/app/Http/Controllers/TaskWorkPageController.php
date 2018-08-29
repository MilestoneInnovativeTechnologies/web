<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TaskWorkPageController extends Controller
{
	
	public function create_customer_cookie(Request $request){
		return \App\Http\Controllers\CustomerCookieController::AddCookie($request->customer, $request->name, $request->value, $this->getAuthUser()->partner);
	}
	
	public function remove_customer_cookie(Request $request){
		$customer = $request->customer; $cookie = $request->cookie;
		if(\App\Models\CustomerCookie::where(['customer' => $customer, 'id' => $cookie])->get()->isEmpty()) return 0;
		\App\Http\Controllers\CustomerCookieController::RemoveCookie($cookie);
		return $cookie;
	}
	
	public function create_customer_connection(Request $request){
		return \App\Http\Controllers\CustomerRemoteConnectionController::AddConnection($request->customer, $request->appname, $request->login, $request->secret, $request->remarks, $this->getAuthUser()->partner);
	}
	
	public function remove_customer_connection(Request $request){
		$customer = $request->customer; $connection = $request->connection;
		if(\App\Models\CustomerRemoteConnection::where(['customer' => $customer, 'id' => $connection])->get()->isEmpty()) return 0;
		\App\Http\Controllers\CustomerRemoteConnectionController::RemoveConnection($connection);
		return $connection;
	}
	
	public function get_customer_product_po_functions($customer, $reg_seq){
		return \App\Models\CustomerPrintObject::where(['customer' => $customer,'reg_seq' => $reg_seq])->pluck('function_name','function_code');
	}
	
	public function add_print_object($customer, $reg_seq, Request $request){
		$CPOC = new \App\Http\Controllers\CustomerPrintObjectController;
		$funDets = $CPOC->CallMethod('NewPOFunctionDetails',[$request]); if(!is_array($funDets)) return 'Function code or Function name cannot be empty';
		if(is_null($customer) || is_null($reg_seq)) return 'Customer and Product seems to be empty. Please corret it.';
		if(!$request->hasFile('po_file')) return 'Print Object file cannot be empty.';
		$file = $CPOC->CallMethod('HandlePOFile',[$request->po_file, $customer, $reg_seq]);
		$preview = null; if($request->hasFile('po_preview_image')) $preview = $CPOC->CallMethod('HandlePOPFile',[$request->po_preview_image, $customer, $reg_seq]);
		$print_name = $CPOC->CallMethod('NewPrintName',[$request]);
		$PO = $CPOC->CallMethod('AddPrintObject',[$customer,$reg_seq,$funDets[0],$funDets[1],$file,$print_name,$preview]);
		$CPOC->CallMethod('ActivatePrintObject',[$PO->code]);
		return $PO->load('User');
	}
	
	public function get_print_object_history($customer, $reg_req, Request $request){
		return \App\Models\CustomerPrintObject::where(['customer' => $customer, 'reg_seq' => $reg_req, 'function_code' => $request->fcode])->select('customer','code','user','time','status')->withOutGlobalScope('active')->get();
	}
	
	public function create_upload_form($Customer, $Ticket, Request $request){
		$GUC = new \App\Http\Controllers\GeneralUploadController;
		return $GUC->store_from_taskwork(array_merge($request->only('name','description','overwrite'),['customer' => $Customer, 'ticket' => $Ticket]));
	}
	
	public function check_general_file_uploaded(Request $request){
		return (\App\Models\GeneralUpload::whereNotNull('file')->whereCode($request->code)->first()) ?: $request->code;
	}
	
	public function change_generalupload_overwrite(Request $request){
		 return \App\Http\Controllers\GeneralUploadController::AlterOverwrite($request->code);
	}
	
	public function drop_generalupload_file(Request $request){
		 return \App\Http\Controllers\GeneralUploadController::DropFile($request->code);
	}
	
	public function delete_generalupload(Request $request){
		 return \App\Http\Controllers\GeneralUploadController::DeleteForm($request->code);
	}
	
	public function send_chat_transcript($user, $tkt, Request $request){
		$Ticket = \App\Models\Ticket::with(['Conversations.User' => function($Q){ $Q->select('code','name'); }])->whereCode($tkt)->first();
		if($Ticket->Conversations->isEmpty() || empty($request->emails)) return;
		$Mail = \App\Libraries\Mail::init()->queue(new \App\Mail\TKTSendChatTranscript($Ticket));
		foreach($request->emails as $email) $Mail->to($email); $Mail->send();
	}
	
	public function get_ticket_users($user, $tkt){
		$Model = \App\Models\Ticket::whereCode($tkt)->with(['Customer.Logins','Team.Team.Logins','Createdby.Logins'])->first(); $Data = []; $Role = []; $Codes = [];
		$Data[$Model->Customer->code] = [$Model->Customer->name, $Model->Customer->Logins[0]->email]; $Codes[] = $Role['customer'][] = $Model->Customer->code;
		$Distributor = $this->GetDistributor($Model->customer); $Data[$Distributor[0]] = [$Distributor[1],$Distributor[2]]; $Codes[] = $Role['distributor'][] = $Distributor[0];
		$Data[$Model->Team->Team->code] = [$Model->Team->Team->name, $Model->Team->Team->Logins[0]->email]; $Codes[] = $Role['supportteam'][] = $Model->Team->Team->code;
		if(!in_array($Model->Createdby->code,$Codes)){
			$Data[$Model->Createdby->code] = [$Model->Createdby->name, $Model->Createdby->Logins[0]->email]; $Codes[] = $Role['createdby'][] = $Model->Createdby->code;
		}
		$users = $this->GetTicketUsers($tkt); $users = array_diff($users,$Codes);
		if(!empty($users)){
			$Partners = \App\Models\Partner::whereIn('code',$users)->with('Logins')->get();
			if($Partners->isNotEmpty()){
				foreach($Partners as $Partner){
					$Data[$Partner->code] = [$Partner->name, $Partner->Logins[0]->email];
					$Role['others'][] = $Partner->code;
				}
			}
		}
		return [$Data,$Role];
	}
	
	private function GetDistributor($PartnerCode){
		$Partner = \App\Models\Partner::with('ParentDetails.Logins','ParentDetails.Roles')->whereCode($PartnerCode)->first();
		if($Partner->ParentDetails[0]->Roles->contains('name','distributor')) return [$Partner->ParentDetails[0]->code,$Partner->ParentDetails[0]->name,$Partner->ParentDetails[0]->Logins[0]->email];
		return $this->GetDistributor($Partner->ParentDetails[0]->code);
	}
	
	private function GetTicketUsers($tkt){
		$CSFC = new \App\Http\Controllers\ConversationSupportFileController();
		return $CSFC->get_users($tkt);
	}

	
	
	
	
	
	
	
	
	
	
	
	
	private function getAuthUser(){
		return (Auth()->user())?:(Auth()->guard("api")->user());
	}

}
