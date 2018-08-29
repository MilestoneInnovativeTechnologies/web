<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Libraries\SMS;

class TicketController extends Controller
{
	
	protected $tkt = '';
	protected $ticket_supportdoc_path = "ticket/supportdoc";
	protected $ticket_attachment_path = "ticket/attachments";
	
	
	public function __construct(){
		$Apply = ['view','edit','update','delete','entitle','reassign','doreassign','manage_tasks','communicate','reopen','closure','doclosure','complete','docomplete','feedback','submitfeedback','recreate','dorecreate','req_complete','req_complete_mail','dismiss','dodismiss'];
		$this->middleware(function($request, $next){
			$segs = $request->segments(); $tkt = $segs[1]; $action = $segs[2]; $tktObj = \App\Models\Ticket::with(['Cstatus'])->whereCode($tkt)->first();
			if(!$tktObj) return back()->with(['info'	=>	true, 'type'	=>	'danger', 'text'	=>	'Ticket requested doesn\'t exists.']);
			if(!in_array($action,$tktObj->available_actions)) return back()->with(['info'	=>	true, 'type'	=>	'danger', 'text'	=>	'The action, '.$action.', is not available for this Ticket.']);
			$this->tkt = $tktObj;
			return $next($request);
		})->only($Apply);

	}
	
	private $view_paginate_length = 30;
	
	public function tickets_new(){
		return $this->ViewTickets(['NEW','REOPENED'],'index');
	}
	
	public function tickets_opened(){
		return $this->ViewTickets('OPENED','index');
	}
	
	public function tickets_inprogress(){
		return $this->ViewTickets('INPROGRESS','index');
	}
	
	public function tickets_holded(){
		return $this->ViewTickets('HOLD','index');
	}
	
	public function tickets_closed(){
		return $this->ViewTickets('CLOSED','index');
	}
	
	public function tickets_completed(){
		$ORM = Ticket::with('Category','Customer','Product','Edition','Team.Team','Cstatus','Customer.Details','Customer.Logins','Closure','Status')->latest();
		$ORM = $this->ORMCurrentStatus($ORM,'COMPLETED');
		if(Request()->search_text) $ORM = $this->ORMAppendSearch($ORM,Request()->search_text);
		return $this->PaginateAndView($ORM,'completed');
	}
	
	public function index(){
		return $this->ViewTickets(['INPROGRESS','HOLD','NEW','OPENED','REOPENED'],'index');
	}

	public function create(){
		return view('tkt.form');
	}
	
	public function store(Request $request){
		$Validate = $this->getTicketValidation($request);
		if($Validate->fails()) return redirect()->back()->withInput()->withErrors($Validate);
		$customer = ($request->customer)?:$this->getAuthUser()->partner;
		$Ticket = $this->CreateTicketBasic($customer,$request->only('title','description','category','product'));
		$this->TicketCategoryUpdate($Ticket,$request);
		$this->AddAttachments($Ticket,$request);
		$this->SubmitTicketToSupportTeam($Ticket->code, $Ticket->customer, $Ticket->product, $Ticket->edition);
		$this->OpenTicketStatus($Ticket->code);
		$this->DisableOnDemandCategory($customer, $request->product, $request->category);
		if($request->srq) $this->ApplyToServiceRequest($Ticket,$request->srq);
		$Ticket = Ticket::whereCode($Ticket->code)->with(['Customer' => function($Q){ $Q->with('Details','Logins'); },'Category','Product','Edition','Team.Team' => function($Q){ $Q->with('Details','Logins'); },'CreatedBy' => function($Q){ $Q->with('Roles','Logins'); }])->first();
		$this->MailNewTicketInfo($Ticket); $this->SmsNewTicketInfo($Ticket);
		return redirect()->back()->with(['info'	=>	true, 'type'	=>	'success',	'text'	=>	'Support Ticket submitted successfully.']);
	}
	
	private function getTicketValidation($request){
		$Rule = ['title'	=>	'required']; $Message = ['title.required'	=>	'Please enter the title of your ticket'];
		if($this->getAuthUser()->rolename != "customer"){
			$Rule['customer']	=	'required'; $Message['customer.required'] = 'Please select the customer for which the ticket is for.';
			$Rule['product']	=	'required'; $Message['product.required'] = 'Please select the product of customer for which the ticket is for.';
		}
		return \Validator::make($request->all(),$Rule,$Message);
	}
	
	public function view($tkt){
		return view('tkt.view');
	}
	
	public function edit($tkt){
		return view('tkt.edit');
	}
	
	public function update(Request $request){
		$tkt = $this->tkt;
		foreach($request->only(['title','description']) as $field => $fvalue) if($tkt->$field != $fvalue)	$tkt->$field = $fvalue;
		if($request->product != $tkt->seqno) { $PE = $this->GetProductEditionFromCustomerSequence($tkt->customer,$request->product); if($PE) { $tkt->seqno = $request->product; $tkt->product = $PE[0]; $tkt->edition = $PE[1]; } }
		if($request->category != $tkt->category) { $tkt->category = $request->category; $tkt->priority = $this->GetPriorityOfCategory($request->category); }
		$tkt->save();
		$this->TicketCategoryUpdate($tkt,$request);
		$this->UpdateTicketAttachments($tkt,$request);
		$this->DisableOnDemandCategory($tkt->customer, $request->product, $request->category);
		return redirect()->back()->with(['info'	=>	true, 'type'	=>	'success',	'text'	=>	'Ticket updated successfully.']);
	}
	
	public function delete($tkt){
		return ($this->tkt->delete()) ? redirect()->route('tkt.index')->with(['info'	=>	true, 'type'	=>	'warning',	'text'	=>	'Ticket deleted successfully.']) : redirect()->route('tkt.index')->with(['info'	=>	true, 'type'	=>	'danger',	'text'	=>	'Error in deleting Ticket.']);
	}
	
	public function entitle($tkt){
		return view('tkt.entitle');
	}
	
	public function entitled($tkt, Request $request){
		$Ticket = Ticket::find($tkt);
		if(!$request->category) $request->merge(['category' => null]);
		//if(!in_array($Ticket->Cstatus->status,['NEW','OPENED'])) return redirect()->back()->withInput()->with(['info'	=>	true, 'type'	=>	'danger',	'text'	=>	'Tickets of Status NEW, OPENED are only modifiable.']);
		$Ticket->fill($request->only('ticket_type','priority','category'))->save();
		$this->TicketCategoryUpdate($Ticket,$request);
		$this->DisableOnDemandCategory($Ticket->customer, $Ticket->seqno, $request->category);
		return redirect()->back()->with(['info'	=>	true, 'type'	=>	'success',	'text'	=>	'Ticket details modified successfully.']);
	}
	
	public function tasks($tkt){
		return view('tkt.tasks');
	}
	
	public function reopen($tkt){
		return view('tkt.reopen');
	}
	
	public function doreopen($tkt, Request $request){
		$this->CreateTicketStatus($tkt,'REOPENED', $request->status_text, true);
		$this->MailReopenTicketInfo($tkt);
		return redirect()->route('tkt.index')->with(['info'	=>	true, 'type'	=>	'success',	'text'	=>	'Ticket Reopened successfully.']);
	}
	
	public function close($tkt){
		return view('tkt.close');
	}
	
	public function doclose($tkt){
		$this->CreateTicketStatus($tkt,'CLOSED', 'SYSTEM: Reopened ticket closed by customer.', true);
		$this->MailReopenClosedTicketInfo($tkt);
		return redirect()->route('tkt.index')->with(['info'	=>	true, 'type'	=>	'success',	'text'	=>	'Ticked closed successfully.']);
	}
	
	public function reassign($tkt){
		return view('tkt.reassign');
	}
	
	public function doreassign($tkt, Request $request){
		if($this->tkt->customer != $request->customer) return redirect()->back()->with(['info'	=>	true, 'type'	=>	'warning',	'text'	=>	'Error in changing support team, Customer mismatch found.']);
		if($this->tkt->Team->team == $request->team) return redirect()->back()->with(['info'	=>	true, 'type'	=>	'info',	'text'	=>	'No changes done, as old and new Support Teams are same.']);
		$this->tkt->Team->update(['team' => $request->team, 'assigned_by' => $this->getAuthUser()->partner]);
		return redirect()->route('tkt.index')->with(['info'	=>	true, 'type'	=>	'success',	'text'	=>	'New Support Team assigned successfully.']);
	}
	
	public function feedback($tkt){
		return view('tkt.feedback');
	}
	
	public function submitfeedback($tkt, Request $request){
		if($this->tkt->customer != $request->customer) return redirect()->back()->with(['info'	=>	true, 'type'	=>	'warning',	'text'	=>	'Error in changing support team, Customer mismatch found.']);
		$this->TicketFeedback($tkt, $request->customer, $request->points, $request->feedback);
		return redirect()->route('tkt.index')->with(['info'	=>	true, 'type'	=>	'success',	'text'	=>	'Feedback submitted successfully.']);
	}
	
	public function complete($tkt){
		return view('tkt.complete');
	}
	
	public function force_complete($tkt){
		session()->flash('no_feedback', true);
		return view('tkt.complete');
		return redirect()->route('tkt.complete',$tkt);
	}
	
	public function docomplete($tkt, Request $request){
		if($request->fb == "YES") {
			if($this->tkt->customer != $request->customer) return redirect()->back()->with(['info'	=>	true, 'type'	=>	'warning',	'text'	=>	'Error in changing support team, Customer mismatch found.']);
			if(!is_null($request->feedback) || $request->points !="0") $this->TicketFeedback($tkt, $request->customer, $request->points, $request->feedback);
		}
		$this->CreateTicketStatus($tkt,'COMPLETED', '', true);
		$this->MailCompletedTicketInfo($tkt);
		return redirect()->route('tkt.index')->with(['info'	=>	true, 'type'	=>	'success',	'text'	=>	'Ticked, '.$tkt.', marked as completed.']);
	}
	
	public function closure($tkt){
		return view('tkt.closure');
	}
	
	public function doclosure($tkt, Request $request){
		$UoCArray = ['solution' => $request->solution, 'reference_ticket' => $request->reference_ticket, 'user' => $this->getAuthUser()->partner];
		if($request->dcd == "YES") { $UoCArray['support_doc'] = null; $this->DeleteSupportDoc($tkt); }
		if($request->support_doc) $UoCArray['support_doc'] = $this->StoreSupportDoc($request->support_doc);
		\App\Models\TicketClosure::updateOrCreate(['ticket' => $tkt],$UoCArray);
		return redirect()->back()->with(['info'	=>	true, 'type'	=>	'success',	'text'	=>	'Closure activites done successfully.']);
	}
	
	public function get_closuredoc($tkt){
		$JS = $this->GetSupportDoc($tkt); if(!$JS) return;
		return Response()->download(storage_path('app/'.$JS['file']),$JS['name']);
	}
	
	public function download_support_document($id){
		$file = json_decode(\App\Models\TicketClosure::find($id)->support_doc,true);
		return response()->download(storage_path("app/".$file['file']),$file['name']);
	}
	
	public function recreate($tkt){
		return view('tkt.recreate');
	}
	
	public function dorecreate($tkt, Request $request){
		$Validate = $this->getTicketValidation($request);
		if($Validate->fails()) return redirect()->back()->withInput()->withErrors($Validate);
		$customer = ($request->customer)?:$this->getAuthUser()->partner;
		$Ticket = $this->CreateTicketBasic($customer,$request->only('title','description','category','product'));
		$this->SubmitTicketToSupportTeam($Ticket->code, $Ticket->customer, $Ticket->product, $Ticket->edition);
		$TST = $this->OpenTicketStatus($Ticket->code); $TST->update(['status_text' => 'SYSTEM: RECREATED ticket of '.$tkt]);
		$this->CreateTicketStatus($tkt,'RECREATED', $Ticket->code, true);
		return redirect()->route('tkt.index')->with(['info'	=>	true, 'type'	=>	'success',	'text'	=>	'Ticket recreated successfully.']);
	}
	
	public function communicate($tkt){
		return view('tkt.communicate');
	}
	
	public function enquire($tkt){
		return view('tkt.enquire');
	}
	
	public function req_complete($tkt){
		return view('tkt.req_complete');
	}
	
	public function req_complete_mail($tkt){
		$Ticket = $this->tkt->load(['Team.Team' => function($Q){ $Q->with('Details','Logins'); },'Customer' => function($Q){ $Q->with('Logins'); }]);
		$this->SendMail('TKTRequestForComplete',$Ticket);
		//\Mail::queue(new \App\Mail\TKTRequestForComplete($Ticket));
		return redirect()->route('tkt.index')->with(['info' => true, 'type' => 'success', 'text' => 'Mail to customer have been successfully queued.']);
	}
	
	public function transcript($tkt){
		$Data = Ticket::whereCode($tkt)->with('Conversations')->first();
		return view('tkt.transcript',compact('Data'));
	}
	
	public function dismiss($tkt){
		return view('tkt.dismiss');
	}
	
	public function dodismiss($tkt, Request $request){
		if(trim($request->status_text) == "") return redirect()->back()->with(['info'	=>	true, 'type'	=>	'danger',	'text'	=>	'Ticket cannot be dismissed without a reason.']);
		$this->CreateTicketStatus($tkt,'DISMISSED', $request->status_text, true);
		$Ticket = Ticket::whereCode($tkt)->with(['Customer.Logins','CreatedBy','Cstatus','Product','Edition','Category','Team.Team' => function($Q){ $Q->with('Details','Logins'); }])->first();
		$this->SendMail('TKTDismissedInfo',$Ticket,$Ticket->Customer,$Ticket->Team->Team);
		return redirect()->route('tkt.index')->with(['info'	=>	true, 'type'	=>	'success',	'text'	=>	'Ticked dismissed successfully.']);
	}

	
	
	
	
	
	
	
	private function getAuthUser(){
		return (Auth()->user())?:(Auth()->guard("api")->user());
	}
	
	public function get_my_products(){
		$user = $this->getAuthUser();
		if($user->rolename != 'customer') return [];
		return $this->get_products_of_customer($user->partner);
	}
	
	public function get_products_of_customer($customer){
		return \App\Models\CustomerRegistration::whereCustomer($customer)->with(['Product'=>function($Q){ $Q->select('code','name'); },'Edition'=>function($Q){ $Q->select('code','name'); }])->get()->mapWithKeys(function($item){
		    $Product = implode(' ',[$item->Product->name,$item->Edition->name,'Edition']);
		    if($item->remarks) $Product .= ' ('. $item->remarks .')';
			return [$item->seqno	=>	$Product];
		});
	}

	public function get_customer_products(Request $request){
		return $this->get_products_of_customer($request->customer);
	}
	
	public function get_distributor_customers(Request $request){
		$distributor = $request->distributor;
		$Customers = $this->get_all_customer_codes_of_distributor($distributor);
		return \App\Models\Customer::whereIn('code',$Customers)->pluck('name','code')->toArray();
	}
	
	private function get_all_customer_codes_of_distributor($distributor){
		return (new \App\Http\Controllers\DistributorController())->get_all_customer_codes_of_distributor($distributor);
	}
	
	public function get_sub_categories(Request $request){
		$category = $request->category;
		return \App\Http\Controllers\TicketCategoryController::get_sub_category($category);
	}
	
	public function get_support_types(){
		return \App\Models\SupportType::whereStatus('ACTIVE')->pluck('name','code')->toArray();
	}
	
	public function get_tasks_for_handle_after(Request $request){
		$tkt = $request->tkt;
		return \App\Models\TicketTask::whereTicket($tkt)->pluck('seqno','id')->toArray();
	}
	
	public function get_ticket_assignable_users(Request $request){
		$tkt = $request->tkt;
		$TST = \App\Models\TicketSupportTeam::with('Team')->whereTicket($tkt)->first();
		$ST = $TST->Team->code;
		$SAAry = \App\Models\TechnicalSupportAgent::withoutGlobalScope('own')->with('Team')->whereHas('Team',function($Q)use($ST){
			$Q->where('parent',$ST);
		})->pluck('name','code')->toArray();
		return array_merge([$ST=>$TST->Team->name],$SAAry);
	}
	
	public function create_ticket_task($tkt, Request $request){
		$Ticket = Ticket::with(['Cstatus','Tasks'])->whereCode($tkt)->first(); $TasksObj = $Ticket->Tasks();
		$CreateArray = $this->get_task_create_array($request,$TasksObj->get());
		$NewTask = $TasksObj->create($CreateArray);
		if($CreateArray['handle_after']) $NewTask->update(['status'	=>	'INACTIVE']);
		$this->OpenTaskStatus($tkt,$NewTask->id);
		if($Ticket->Cstatus->status != "OPENED") $this->CreateTicketStatus($tkt,'OPENED');
		$this->AddTaskResponder($tkt,$NewTask->id,$request->responder);
		return $NewTask;
	}
	
	static function RearrangeTaskSequences($TicketTasks){
		if($TicketTasks->isNotEmpty()){
			$TicketTasks->each(function($Item, $key){
				if($Item->seqno != ($key+1)) $Item->update(['seqno'	=>	($key+1)]);
			});
		}
		return $TicketTasks->count();
	}
	
	private function get_task_create_array($request, $tasks){
		$Ary = $request->only('title','description','support_type');
		$Ary['created_by'] = $this->getAuthUser()->partner;
		$Ary['seqno'] = $this->CorrectAndGetNextTaskSeq($tasks);
		$Ary['handle_after'] = $this->GetHandleAfterTasks($request);
		return $Ary;
	}
	
	private function CorrectAndGetNextTaskSeq($Task){
		$CurrentTaskCount = $this->RearrangeTaskSequences($Task);
		return $CurrentTaskCount+1;
	}
	
	private function GetHandleAfterTasks($request){
		if(!$request->handle_after) return null;
		$after_tasks = $request->{$request->handle_after};
		if(empty($after_tasks)) return null;
		return \App\Http\Controllers\CommonController::ItemIDsJoinForDB($after_tasks);
	}
	
	public function update_weightages($tkt, Request $request){
		$Tasks = Ticket::find($tkt)->Tasks;
		$WHT = $request->weightage;
		foreach($WHT as $ID => $Weightage)
			if($ID && trim($ID)!="" && $Tasks->where('id',$ID)->isNotEmpty())
				$Tasks->where('id',$ID)->first()->update(['weightage'	=>	$Weightage]);
		return $Tasks->pluck('weightage','id');
	}
	
	public function UpdateTicketStatusTaskRelated($tkt){
		$tasks = $tkt->Tasks;
		if($tasks->isEmpty()) return $this->CreateTicketStatus($tkt->code, 'NEW', 'SYSTEM: Tasks empty.');
		$Status = "OPENED";
		foreach($tasks as $tsk) if($Status == "OPENED" && in_array($tsk->Cstatus->status,[/*'OPENED',*/'WORKING','HOLD'])) $Status = "INPROGRESS";
		if($tkt->Cstatus->status != $Status) return $this->CreateTicketStatus($tkt->code, $Status, 'SYSTEM: Checked for Tasks in WORKING or HOLD and created this status');
	}
	
	public function get_ticket_details($tkt){
		return Ticket::find($tkt);
	}
	
	static function isTicketClosable($tkt){
		$Tasks = \App\Models\TicketTask::whereTicket($tkt)->with('Cstatus')->get();
		$Closable = true;
		$Tasks->each(function($item)use(&$Closable){
			if($Closable && $item->Cstatus->status != "CLOSED")
				$Closable = false;
		});
		return $Closable;
	}
	
	public function get_ticket_progress(Request $request){
		$max = 0; $val = 0;
		foreach(Ticket::find($request->tkt)->tasks as $tsk){
			$max += intval($tsk->weightage);
			if($tsk->Cstatus->status == 'CLOSED') $val += intval($tsk->weightage);
		}
		return ['tkt' => $request->tkt, 'val' => $val, 'max' => $max];
	}
	
	public function get_additional_ticket_category(Request $request){
		$seq = $request->seq; $cus = ($request->cus)?:$this->getAuthUser()->partner; $TC = new \App\Models\TicketCategory();
		$AddCats =  $TC->where('available','<>','ALWAYS')->whereStatus('ACTIVE')->get()->toArray(); $RetCat = [];
		foreach($AddCats as $Cat){ if($TC->{$Cat['available']}($cus, $seq, $Cat['code'])) $RetCat[$Cat['code']] = $Cat['name']; }
		return $RetCat;
	}
	
	public function search_customer(){
		$Like = '%' . Request()->cus . '%';
		return \App\Models\Customer::where('name','like',$Like)->orWhere('name','like',$Like)->get();
	}
	
	public function listtickets($team, $status, Request $request){
		$period = ($request->period)?:false; $Title = ($period) ? 'From: '.date('d/M/y', is_numeric($period) ? $period : strtotime('-'.$period,strtotime(date('Y-m-d 00:00:00'))) ) : strtoupper($status);
		$ORM = \App\Models\Ticket::with(['Team','Customer','Product','Edition','CreatedBy'])->whereHas('Team',function($Q)use($team){ $Q->where('team',$team); });
		if($period) $ORM = $ORM->where('created_at', '>=', date('Y-m-d 00:00:00',( is_numeric($period) ? $period : strtotime('-'.$period,strtotime(date('Y-m-d 00:00:00'))) )));
		if($request->till && $till = $request->till) $ORM = $ORM->where('created_at', '<', date('Y-m-d 23:59:59',( is_numeric($till) ? $till : strtotime('-'.$till,strtotime(date('Y-m-d 23:59:59'))) )));
		$user = $request->user;
		if(($status = strtoupper($status)) != 'TOTAL'){
			if($period) $status = ($status == 'CLOSED') ? ['CLOSED','COMPLETED','RECREATED'] : (($status == 'NEW') ? ['NEW','OPENED'] : $status);
			if($status == 'CURRENT') $status = ['OPENED','INPROGRESS','HOLD'];
			if(!is_array($status)) $status = [$status];
			$ORM = ($request->today)
                ? $ORM->whereHas('Status',function($Q)use($status,$user){ $Q->whereIn('status',$status)->whereBetween('updated_at',[date('Y-m-d 00:00:00'),date('Y-m-d 23:59:59')]); if($user) $Q->where('user',$user); })
                : $ORM->whereHas('Cstatus',function($Q)use($status,$user){ if($user) $Q->whereIn('status',$status)->where('user',$user); else $Q->whereIn('status',$status); });
		}
		//\DB::enableQueryLog();
		$Data = $ORM->paginate(100);
		//return \DB::getQueryLog();
		return view('tkt.list1',compact('Title','Data'));
	}
	
	
	
	
	
	
	
	
	
	
	private function CreateTicketBasic($Customer, $DetailsArray){
		if(!array_key_exists('customer',$DetailsArray)) { $DetailsArray['customer'] = $Customer; }
		$FieldArray = $this->CreateTicketBasic_AddMissing($DetailsArray);
		return Ticket::create($FieldArray);
	}
	
	private function CreateTicketBasic_AddMissing($DetailsArray){
		if(!array_key_exists('priority',$DetailsArray) && array_key_exists('category',$DetailsArray) && $DetailsArray['category']) $DetailsArray['priority'] = $this->GetPriorityOfCategory($DetailsArray['category']);
		if(!array_key_exists('code',$DetailsArray)) $DetailsArray['code'] = null;
		if(!array_key_exists('created_by',$DetailsArray)) $DetailsArray['created_by'] = $this->getAuthUser()->partner;
		if(!array_key_exists('seqno',$DetailsArray)) { 
			$CRegs = $this->GetProductEditionFromCustomerSequence($DetailsArray['customer'],$DetailsArray['product']);
			$DetailsArray['product'] = $CRegs[0]; $DetailsArray['edition'] = $CRegs[1];
		}
		return $DetailsArray;
	}
	
	private function AddAttachments($Ticket, $request){
		if(!$request->has('attachment')) return null; $CreateArray = [];
		foreach($request->attachment as $attachment){
			if($attachment['file']) $CreateArray[] = ['name' => $attachment['name'], 'file' => $this->StoreTicketAttachmentFile($attachment['file'],$Ticket->code), 'user' => $this->getAuthUser()->partner];
		}
		if(!empty($CreateArray)) return $Ticket->Attachments()->createMany($CreateArray);
		return null;
	}
	
	private function StoreTicketAttachmentFile($file,$tkt){
		$Path = $this->GetTicketAttachmentPath($tkt);
		if($file->extension()) return $file->store($Path);
		$ext = mb_strrchr($file->getClientOriginalName(),'.');
		$filename = $file->hashName(); if(mb_substr($filename,-1) == ".") $filename = mb_substr($filename,0,-1);
		return $file->storeAs($Path,$filename.$ext);
	}
	
	private function GetTicketAttachmentPath($tkt){
		return $this->ticket_attachment_path . "/" . $tkt;
	}

	private function GetProductEditionFromCustomerSequence($Customer, $Seqno){
		$CRegs = \App\Models\CustomerRegistration::where(['customer'	=>	$Customer, 'seqno'	=>	$Seqno])->first();
		return is_null($CRegs)?null:[$CRegs->product,$CRegs->edition];
	}
	
	private function GetPriorityOfCategory($Category){
		return ($Category) ? \App\Models\TicketCategoryMaster::withoutGlobalScope('own')->find($Category)->priority : 'NORMAL';
	}
	
	private function SubmitTicketToSupportTeam($Ticket, $Customer, $Product, $Edition){
		$ORM = \App\Models\Customer::find($Customer)->Supportteam()->where(['product'	=>	$Product, 'edition'	=>	$Edition, 'status'	=>	'ACTIVE']);
		if($ORM->count()) $ST = $ORM->first()->supportteam;
		else $ST = $this->getSupportTeamOfCustomer($Customer);
		$Array = ['ticket'	=>	$Ticket, 'customer'	=>	$Customer, 'team'	=>	$ST, 'assigned_by'	=>	$this->getAuthUser()->partner];
		return \App\Models\TicketSupportTeam::create($Array);
	}
	
	private function getSupportTeamOfCustomer($Customer){
		$CustObj = \App\Models\Customer::whereCode($Customer)->with('Parent1.Roles')->first();
		if($CustObj->Parent1->Roles->contains('rolename','distributor')) $ST = $this->getSupportTeamOfDistributor($CustObj->Parent1->parent);
		else $ST = $this->getSupportTeamOfDistributor($CustObj->Parent1->ParentDetails->Parent->parent);
		return ($ST)?:$this->getDefaultSupportTeam();
	}
	
	private function getSupportTeamOfDistributor($distributor){
		$ORM = \App\Models\DistributorSupportTeam::whereDistributor($distributor);
		if($ORM->count()) return $ORM->first()->supportteam;
		return null;
	}
	
	private function getDefaultSupportTeam(){
		return \App\Models\DefaultSupportTeam::first()->supportteam;
	}
	
	private function OpenTicketStatus($Ticket){
		return $this->TicketNewStatus($Ticket, 'NEW', $this->getAuthUser()->partner);
		//return \App\Models\TicketStatusTrans::create(['ticket'	=>	$Ticket, 'start_time'	=>	time(), 'user'	=>	$this->getAuthUser()->partner]);
	}
	
	private function AddTaskResponder($tkt, $tsk, $rsp){
		if(!$rsp) return null;
		$NewTask = \App\Models\TaskResponder::create(['ticket'	=>	$tkt, 'task'	=>	$tsk, 'responder'	=>	$rsp, 'assigned_by'	=>	$this->getAuthUser()->partner]);
		$this->CreateTaskStatus($tsk, 'ASSIGNED');
		return $NewTask;
	}
	
	private function OpenTaskStatus($tkt, $tsk){
		$this->CreateTaskStatus($tsk, 'CREATED');
		//return \App\Models\TaskStatusTrans::create(['ticket'	=>	$tkt, 'task'	=>	$tsk, 'start_time'	=>	time(), 'user'	=>	$this->getAuthUser()->partner]);
	}

	private function CreateTicketStatus($tkt, $status = null, $status_text = null, $end_previous = false){
		return $this->TicketNewStatus($tkt, $status, $this->getAuthUser()->partner, $status_text, $end_previous);
	}
	
	static function TicketNewStatus($tkt, $status, $user, $status_text = null, $end_previous = false){
		$Ticket = Ticket::with(['Status','Cstatus'])->whereCode($tkt)->first();
		if($end_previous) {
			$EndTime = intval(time()); 
			$UpdateArray = ['end_time' => $EndTime, 'total'	=>	$EndTime-intval($Ticket->Cstatus->start_time), 'user'	=>	$user];
			$Ticket->Status()->latest()->first()->update($UpdateArray);
			//$Ticket->Cstatus->updateOrCreate(['ticket' => $tkt],$UpdateArray);
		}
		if($status) {
			$CreateArray = ['status'	=>	$status, 'start_time'	=>	time(), 'user'	=>	$user];
			if($status_text) $CreateArray['status_text'] = $status_text; else $CreateArray['status_text'] = null;
			\App\Models\TicketCurrentStatus::updateOrCreate(['ticket' => $tkt],$CreateArray);
			//$Ticket->Cstatus->updateOrCreate(['ticket' => $tkt],$CreateArray);
			return $Ticket->Status()->create($CreateArray);
		}
	}
	
	private function CreateTaskStatus($tsk, $status = null, $status_text = null, $end_previous = false){
		$TTC = new \App\Http\Controllers\TicketTaskController();
		return $TTC->CreateStatus($tsk, $status, $status_text, $end_previous);
	}
	
	private function TicketFeedback($tkt, $customer, $points, $feedback){
		return \App\Models\TicketFeedback::updateOrCreate(['ticket' => $tkt, 'customer' => $customer],['points' => $points, 'feedback' => $feedback]);
	}
	
	private function StoreSupportDoc($file){
		$path = $this->ticket_supportdoc_path;
		$FileArray['file'] = $file->store($path); $StoreProcedure = ['name'=>'getClientOriginalName','ext'=>'extension','mime'=>'getMimeType','size'=>'getSize'];
		foreach($StoreProcedure as $Field => $FnName) $FileArray[$Field] = $file->$FnName();
		return json_encode($FileArray);
	}
	
	private function GetSupportDoc($tkt){
		$TC = \App\Models\TicketClosure::whereTicket($tkt)->first();
		if(!$TC || !$TC->support_doc) return null;
		return json_decode($TC->support_doc,true);
	}
	
	private function DeleteSupportDoc($tkt){
		$file = json_decode(\App\Models\TicketClosure::whereTicket($tkt)->first()->support_doc,true);
		\Storage::delete($file['file']);
	}
	
	private function DisableOnDemandCategory($Customer, $Seq, $Category){
		if(!$Category) return;
		return (new \App\Models\TicketCategoryMaster)->ondemand_delete($Customer, $Seq, $Category);
		//if(!$Category || !($TC = \App\Models\TicketCategory::where(['code' => $Category, 'available' => 'isSupportteamPermitted'])->first())) return;
		//$TC->del_cus_cat_perm($Customer, $Seq, $Category);
	}
	
	private function ApplyToServiceRequest($Ticket,$SRQ){
		if(is_null($Ticket) || is_null($SRQ) || is_null($srq = \App\Models\ServiceRequest::find($SRQ))) return;
		$srq->add_ticket_response($Ticket,$this->getAuthUser());
	}
	
	private function MailNewTicketInfo($Ticket){
		$CreatorRoles = $Ticket->CreatedBy->Roles;
		if($CreatorRoles->contains('name','supportteam')) $this->SendMail('TKTCreatedInfoToCustomer',$Ticket,$Ticket->Customer,$Ticket->CreatedBy);
		elseif($CreatorRoles->contains('name','customer')) $this->SendMail('TKTCreatedInfoToSupportTeam',$Ticket,$Ticket->Team->Team,$Ticket->CreatedBy);
		else $this->SendMail('TKTCreatedInfoToAll',$Ticket,[$Ticket->Customer,$Ticket->Team->Team],$Ticket->CreatedBy);		
	}
	
	private function SmsNewTicketInfo($Ticket){
		$CreatorRoles = $Ticket->CreatedBy->Roles;
		if($CreatorRoles->contains('name','distributor')) $this->SendSms('TKTNewInfoToDistributor',$Ticket,$Ticket->Customer->get_distributor());
		$this->SendSms('TKTNewToCustomer',$Ticket,$Ticket->Customer);
	}
	
	private function MailCompletedTicketInfo($tkt){
		$Ticket = Ticket::whereCode($tkt)->with(['Customer' => function($Q){ $Q->with('Details','Logins'); },'Category','Product','Edition','Team.Team' => function($Q){ $Q->with('Details','Logins'); },'CreatedBy' => function($Q){ $Q->with('Roles','Logins'); },'Feedback'])->first();
		$this->SendMail('TKTCompletedInfo',$Ticket,$Ticket->Team->Team);
	}
	
	private function MailReopenTicketInfo($tkt){
		$Ticket = Ticket::whereCode($tkt)->with(['Customer' => function($Q){ $Q->with('Details','Logins'); },'Category','Product','Edition','Team.Team' => function($Q){ $Q->with('Details','Logins'); },'CreatedBy' => function($Q){ $Q->with('Roles','Logins'); }])->first();
		$this->SendMail('TKTReopenedInfo',$Ticket,$Ticket->Team->Team,$Ticket->Customer);
	}
	
	private function MailReopenClosedTicketInfo($tkt){
		$Ticket = Ticket::whereCode($tkt)->with(['Customer' => function($Q){ $Q->with('Details','Logins'); },'Category','Product','Edition','Team.Team' => function($Q){ $Q->with('Details','Logins'); },'CreatedBy' => function($Q){ $Q->with('Roles','Logins'); }])->first();
		$this->SendMail('TKTReopenedClosedInfo',$Ticket,$Ticket->Team->Team,$Ticket->Customer);
	}
	
	private function SendMail($Mail,$Object,$To = null,$Cc = null){
		$Class = '\\App\\Mail\\' . $Mail; if(!$To) $To = $Object->Customer;
		$this->SendMailTo(new $Class($Object),$To,$Cc);
	}
	
	private function SendSms($Sms,$Object,$To = null){
		$Class = '\\App\\Sms\\' . $Sms;
		SMS::init(new $Class($Object))->send($To);
	}
	
	private function SendMailTo($Mailable, $To = null, $Cc = null){
		if(!$To || !$Mailable) return; $Mail = \App\Libraries\Mail::init()->queue($Mailable);
		if(is_array($To)) foreach($To as $to) $Mail = $Mail->to($to); else $Mail = $Mail->to($To);
		if(!is_null($Cc)){ if(is_array($Cc)) foreach($Cc as $cc) $Mail = $Mail->cc($cc); else $Mail = $Mail->cc($Cc); }
		$Mail->send();
	}
	
	private function ORMAppendSearch($ORM, $Text){
		if(trim($Text) == "") return $ORM;
		$Like = "%".$Text."%";
		$ORM->where(function($Q)use($Like){
			$Q->where('code','like',$Like)->orWhere('title','like',$Like)->orWhere('priority','like',$Like)
			->orWhereHas('Category',function($Q)use($Like){ $Q->where('name','like',$Like); })
			->orWhereHas('Customer',function($Q)use($Like){ $Q->where('name','like',$Like)->orWhere('code','like',$Like); })
			->orWhereHas('Product',function($Q)use($Like){ $Q->where('name','like',$Like)->orWhere('code','like',$Like); })
			->orWhereHas('Edition',function($Q)use($Like){ $Q->where('name','like',$Like)->orWhere('code','like',$Like); })
			->orWhereHas('Team.Team',function($Q)use($Like){ $Q->where('name','like',$Like)->orWhere('code','like',$Like); })
			->orWhereHas('Cstatus',function($Q)use($Like){ $Q->where('status','like',$Like); })
			->orWhereHas('Customer.Details',function($Q)use($Like){ $Q->where('phone','like',$Like); })
			->orWhereHas('Customer.Logins',function($Q)use($Like){ $Q->where('email','like',$Like); });
		});
		return $ORM;
	}
	
	private function ORMCurrentStatus($ORM, $Status){
		if(empty($Status)) return $ORM;
		$ORM->whereHas('Cstatus',function($Q) use($Status) {
			if(is_string($Status)) $Q->where('status',$Status);
			else $Q->whereIn('status',$Status);
		});
		return $ORM;
	}
	
	private function ViewTickets($Status,$View){
		$ORM = $this->GetBasicTKTORM();
		$ORM = $this->ORMCurrentStatus($ORM,$Status);
		if(Request()->search_text) $ORM = $this->ORMAppendSearch($ORM,Request()->search_text);
		return $this->PaginateAndView($ORM,$View);
	}
	private function GetBasicTKTORM(){
		return Ticket::with('Category','Customer','Product','Edition','Team.Team','Cstatus','Customer.Details','Customer.Logins')->latest();
	}
	
	private function PaginateAndView($ORM, $View){
		$Data = $ORM->paginate($this->view_paginate_length);
		$Links = $Data->appends(['search_text' => Request()->search_text])->links();
		return view('tkt.'.$View,compact('Data','Links'));
	}

	private function GetTicketCustomerDistributor($Ticket){
		$Customer = $Ticket->customer;
		return $this->GetDistributorOfPartner($Customer);
	}
	
	private function GetDistributorOfPartner($Partner){
		$PartnerObj = \App\Models\Partner::whereCode($Partner)->with('ParentDetails')->first();
		if($PartnerObj->ParentDetails[0]->Roles->contains('name','distributor')) return $PartnerObj->ParentDetails[0]->code;
		return $this->GetDistributorOfPartner($PartnerObj->ParentDetails[0]->code);
	}
	
	private function DeleteTicketAttachments($tkt){
		$tkt = $this->GetTktObj($tkt)->load('Attachments');
		if($tkt->Attachments->isEmpty()) return;
		$tkt->Attachments->each(function($item){ $filename = $item->file; $this->DeleteStorageFile($filename); });
		$tkt->Attachments()->delete();
	}
	
	private function DeleteStorageFile($path, $disk = 'local'){
		\Storage::disk($disk)->delete($path);
	}
	
	private function GetTktObj($tkt){
		return is_object($tkt) ? $tkt : Ticket::find(tkt);
	}
	
	private function UpdateTicketAttachments($tkt,$request){
		$tkt = $this->GetTktObj($tkt)->load('Attachments'); $Attachments = $tkt->Attachments;
		if(!$request->has('attachment') && $tkt->Attachments->isEmpty()) return null;
		if(!$request->has('attachment') && $tkt->Attachments->isNotEmpty()) return $this->DeleteTicketAttachments($tkt);
		if($request->has('attachment') && $tkt->Attachments->isEmpty()) return $this->AddAttachments($tkt,$request);
		$this->DeleteTicketAttachmentNotInRequest($Attachments,$request->attachment);
		$this->UpdateTicketAttachmentsInRequest($Attachments,$request->attachment);
		$this->AddTicketAttachmentsInRequest($tkt,$request->attachment);		
	}
	
	private function DeleteTicketAttachmentNotInRequest($DBAttachs,$RQAttachs){
		foreach($DBAttachs as $DBAttach){
			if(!array_key_exists($DBAttach->id,$RQAttachs)){
				$this->DeleteStorageFile($DBAttach->file);
				$DBAttach->delete();
			}
		}
	}
	
	private function UpdateTicketAttachmentsInRequest($DBAttachs,$RQAttachs){
		foreach($DBAttachs as $DBAttach)
			if(array_key_exists($DBAttach->id,$RQAttachs) && $DBAttach->name != $RQAttachs[$DBAttach->id]['name']){
				$DBAttach->name = $RQAttachs[$DBAttach->id]['name']; $DBAttach->save();
			}
	}
	
	private function AddTicketAttachmentsInRequest($Ticket,$RQAttachs){
		foreach($RQAttachs as $key => $RQAttach){
			if(intval($key) < 0 && $RQAttach['file']){
				$file = $this->StoreTicketAttachmentFile($RQAttach['file'],$Ticket->code);
				$name = $RQAttach['name'];
				$user = $this->getAuthUser()->partner;
				$Ticket->Attachments()->create(compact('name','file','user'));
			}
		}
	}
	
	private function TicketCategoryUpdate($Ticket,$Request){
		$Category = $Request->category; if(!$Category) return;
		$Specs = $Request->$Category;
		(new \App\Models\TicketCategory)->create_new((gettype($Ticket) == "object")?$Ticket->code:$Ticket,$Category,$Specs);
	}
	
}