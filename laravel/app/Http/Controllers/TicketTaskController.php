<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TicketTask;

class TicketTaskController extends Controller
{

	public function __construct(){
		$Apply = ['edit','update','delete','chngrsp','open','recheck','work','hold','close'];
		$this->middleware(function($request, $next){
			$segs = $request->segments(); $tsk = $segs[1]; $action = $segs[2]; $tskObj = \App\Models\TicketTask::with(['Cstatus'])->whereId($tsk)->first();
			if(!$tskObj) return back()->with(['info'	=>	true, 'type'	=>	'danger', 'text'	=>	'Task requested doesn\'t exists.']);
			if(!in_array($action,$tskObj->available_action)) return back()->with(['info'	=>	true, 'type'	=>	'danger', 'text'	=>	'The action, '.$action.', is not available for this tasks']);
			return $next($request);
		})->only($Apply);

	}
	
	private $view_paginate_length = 30;

	public function index(){
		return $this->ViewTasks(['CREATED','ASSIGNED','RECHECK','REASSIGNED','OPENED','WORKING','HOLD']);
	}
	
	public function tasks_new(){
		return $this->ViewTasks(['CREATED','ASSIGNED','RECHECK','REASSIGNED']);
	}
	
	public function tasks_working(){
		return $this->ViewTasks(['OPENED','WORKING']);
	}
	
	public function tasks_holded(){
		return $this->ViewTasks('HOLD');
	}
	
	public function tasks_closed(){
		return $this->ViewTasks('CLOSED','closed');
	}
	
	public function view($tsk){
		$tsk = TicketTask::with(['Ticket','Stype','Responder','Cstatus','Responder.Assigner'])->whereId($tsk)->first();
		return view('tsk.view',compact('tsk'));
	}
	
	public function edit($tsk){
		$tsk = TicketTask::with(['Ticket.Tasks','Ticket.Customer','Stype','Cstatus'])->whereId($tsk)->first();
		return view('tsk.edit',compact('tsk'));
	}
	
	public function chngrsp($tsk){
		$tsk = TicketTask::with(['Ticket','Stype','Responder','Cstatus','Responder.Assigner'])->whereId($tsk)->first();
		$rsps = $this->get_assignable_users($tsk);
		return view('tsk.chngrsp',compact('tsk','rsps'));
	}
	
	private function get_assignable_users($tsk){
		$tsk = $this->GetTaskObj($tsk);
		$TST = \App\Models\TicketSupportTeam::with('Team')->whereTicket($tsk->ticket)->first()->Team;
		$SAAry = \App\Models\TechnicalSupportAgent::with('Team')->whereHas('Team',function($Q)use($TST){
			$Q->where('parent',$TST->code);
		})->pluck('name','code')->toArray();
		return array_merge([$TST->code=>$TST->name],$SAAry);
	}
	
	public function delete($tsk){
		$this->RemoveTaskFromAfters($tsk);
		$this->DeleteTask($tsk);
		return back()->with(['info'	=>	true, 'type'	=>	'info', 'text'	=>	'Task deleted..']);
	}
	
	public function update($tsk, Request $request){
		$tsk = TicketTask::find($tsk);
		$reqs =  $request->only('title','description','support_type');
		foreach($reqs as $field => $value) if($value != $tsk->$field) $tsk->{$field} = $value;
		$tsk->save(); return $this->UpdateHandleAfterAndReturn($tsk, $request);
	}
	
	public function update_responder($tsk, Request $request){
		$responder = $request->responder; if(!$responder) {
			$this->DeleteResponderOfTask($tsk);
			return back()->with(['info'	=>	true, 'type'	=>	'info', 'text'	=>	'Responder removed from the task.']);
		}
		$users = $this->get_assignable_users($tsk);
		if(!array_key_exists($responder,$users)) return back()->with(['info'	=>	true, 'type'	=>	'danger', 'text'	=>	'Responder assigned is not allowed for this Ticket/Task.']);
		$task = $this->GetTaskObj($tsk); $ticket = $task->ticket;
		$tskResp = \App\Models\TaskResponder::updateOrCreate(['ticket' => $ticket, 'task' => $tsk],['responder' => $responder, 'assigned_by' => $this->getAuthUser()->partner]);
		$this->CreateStatus($tsk,($task->Cstatus->status == 'RECHECK')?'REASSIGNED':'ASSIGNED');
		return back()->with(['info'	=>	true, 'type'	=>	'success', 'text'	=>	'Responder changed successfully..']);
	}
	
	public function open($tsk){
		$this->CreateStatus($tsk,'OPENED');
		$Task = TicketTask::with(['Ticket'	=>	function($Q){
			$Q->with('Tasks','Customer','Product','Edition','Createdby');
		},'Createdby','Responder','Status.User'])->whereId($tsk)->first();
		return view('tsk.open',compact('Task'));
	}
	
	public function recheck($tsk){
		$Task = TicketTask::with(['Ticket'	=>	function($Q){
			$Q->with('Tasks','Customer','Product','Edition','Createdby');
		},'Createdby','Responder','Status.User'])->whereId($tsk)->first();
		return view('tsk.recheck',compact('Task'));
	}
	
	public function dorecheck($tsk){
		$this->CreateStatus($tsk,'RECHECK',request()->status_text);
		return redirect()->route('tsk.index')->with(['info'	=>	true, 'type'	=>	'info', 'text'	=>	'Task submitted for rechecking.']);
	}
	
	public function work($tsk){
		$this->WorkTaskTicket($tsk);
		$Task = TicketTask::with(['Ticket'	=>	function($Q){
			$Q->with(['Tasks','Product','Edition','Createdby','Cookies','Connections.Createdby','Pobjects','Customer'	=>	function($Q){
				$Q->with(['Details','Logins','Uploads','Register']);
			},'Attachments']);
		},'Stype','Createdby','Responder','Status'])->whereId($tsk)->first();
		$Packages = $this->GetLatestPackageDetails($Task->Ticket->product, $Task->Ticket->edition);
		//return $Task;
		return view('tsk.work',compact('Task','Packages'));
	}
	
	public function hold($tsk){
		$Task = TicketTask::with(['Createdby','Responder','Status.User'])->whereId($tsk)->first();
		return view('tsk.hold',compact('Task'));
	}
	
	public function dohold($tsk, Request $request){
		$ticket_status_text = ($request->hold_ticket) ? (($request->ticket_status == 'custom_status_text') ? $request->ticket_status_text : $request->ticket_status) : false;
		$this->HoldTaskTicket($tsk, $request->status_text, $ticket_status_text);
		return redirect()->route('tsk.index')->with(['info'	=>	true, 'type'	=>	'info', 'text'	=>	'Task has been Holded.']);
	}
	
	public function close($tsk){
		if(request()->confirm != "yes"){
			$Task = TicketTask::with(['Createdby','Responder','Status.User'])->whereId($tsk)->first();
			return view('tsk.close',compact('Task'));
		}
		$this->CloseTask($tsk);
		return redirect()->route('tsk.index')->with(['info'	=>	true, 'type'	=>	'success', 'text'	=>	'Task has been Closed.']);
	}
	
	public function listtasks($agent, $status, Request $request){
		$period = ($request->period)?:false; $Title = ($period) ? 'From: '.date('d/M/y', is_numeric($period) ? $period : strtotime('-'.$period,strtotime(date('Y-m-d 00:00:00'))) ) : strtoupper($status);
		$ORM = \App\Models\TicketTask::with(['Responder.Assigner','Ticket.Customer','CreatedBy'])->whereHas('Responder',function($Q)use($agent){ $Q->where('responder',$agent); });
		if($period) $ORM = $ORM->where('created_at', '>=', date('Y-m-d 00:00:00',$period));
		if($request->till && $till = $request->till) $ORM = $ORM->where('created_at', '<', date('Y-m-d 00:00:00',( is_numeric($till) ? $till : strtotime('-'.$till,strtotime(date('Y-m-d 00:00:00'))) )));
		if(($status = strtoupper($status)) != 'TOTAL'){
			$ActualStatus = ['NEW' => ['CREATED','ASSIGNED','REASSIGNED'], 'WORKING' => ['OPENED','WORKING']]; if(!$period) $ActualStatus['NEW'][] = 'RECHECK';
			$status = (array_key_exists($status,$ActualStatus)) ? $ActualStatus[$status] : [$status];
			$ORM = $ORM->whereHas('Cstatus',function($Q)use($status){ $Q->whereIn('status',$status); });
		}
		$Data = $ORM->paginate(100);
		return view('tsk.list1',compact('Title','Data'));
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	private function UpdateHandleAfterAndReturn($tsk, $request){
		$HandleAfter = null;
		if(is_null($tsk->handle_after) && (is_null($request->handle_after) || empty($request->{$request->handle_after}))) { $HandleAfter = null; }
		if(is_null($tsk->handle_after) && !is_null($request->handle_after) && !empty($request->{$request->handle_after})){
			$HandleThisAfter = array_map('intval',$request->{$request->handle_after});
			$HandleAfter = \App\Http\Controllers\CommonController::ItemIDsJoinForDB($HandleThisAfter);
		}
		if(!is_null($tsk->handle_after) && (is_null($request->handle_after) || empty($request->{$request->handle_after}))){
			$HandleAfter = null;
		}
		if(!is_null($tsk->handle_after) && !is_null($request->handle_after) && !empty($request->{$request->handle_after})){
			$BeforeTasks1 = $tsk->handle_after; $BeforeTasks2 = array_map('intval',$request->{$request->handle_after});
			$Add = []; $Delete = []; $Untouch = [];
			foreach($BeforeTasks1 as $BeforeTasks){ if(in_array($BeforeTasks->id,$BeforeTasks2)) $Untouch[] = $BeforeTasks->id; else $Delete[] = $BeforeTasks->id; }
			foreach($BeforeTasks2 as $TaskId){ if(!$BeforeTasks1->contains('id',$TaskId)) $Add[] = $TaskId; }
			$HandleAfter = \App\Http\Controllers\CommonController::ItemIDsJoinForDB(array_merge($Untouch,$Add));
		}
		$tsk->update(['handle_after'	=>	$HandleAfter]); $this->UpdateTaskInactivity($tsk);
		return back()->with(['info'	=>	true, 'type'	=>	'success', 'text'	=>	'Task updated successfully']);
	}
	
	private function GetFollowingTasks($TaskIds){
		return TicketTask::whereIn('id',(array) $TaskIds)->get();
	}
	
	private function UpdateTaskInactivity($tsk){
		$Task = $this->GetTaskObj($tsk);
		$BeforeTasks = $Task->handle_after;
		if(is_null($BeforeTasks) || empty($BeforeTasks)) return $this->ChangeTaskActivity($Task,'ACTIVE');
		$Status = 'ACTIVE';
		foreach($BeforeTasks as $BeforeTask)
			if($Status == "ACTIVE" && $BeforeTask->Cstatus->status != "CLOSED")
				$Status = 'INACTIVE';
		return $this->ChangeTaskActivity($Task,$Status);
	}
	
	private function UpdateDependentTaskActivity($tsk){
		return $this->GetDependentTasks($tsk)->each(function($item)use($tsk){
			$this->UpdateTaskInactivity($item);
		});
	}
	
	private function ChangeTaskActivity($tsk, $status){
		return (gettype($tsk) == "object") ? $tsk->update(['status'	=>	$status]) : TicketTask::find($tsk)->update(['status'	=>	$status]);
	}
	
	private function GetTaskObj($tsk, $with = null){
		return (gettype($tsk) == "object") ? $tsk : (($with) ? (TicketTask::whereId($tsk)->with($with)->first()) : (TicketTask::find($tsk)));
	}
	
	private function GetTaskId($tsk){
		return (gettype($tsk) == "object") ? $tsk->id : $tsk;
	}
	
	public function CreateStatus($tsk, $status = null, $status_text = null, $end_previous = false){
		$Task = $this->GetTaskObj($tsk,['Status','Cstatus']);
		if($end_previous) {
			$EndTime = intval(time());
			$UpdateArray = ['end_time' => $EndTime, 'total'	=>	$EndTime-intval($Task->Cstatus->start_time), 'user'	=>	$this->getAuthUser()->partner];
			$Task->Status()->latest('id')->first()->update($UpdateArray);
			//$Task->Cstatus->update($UpdateArray);
			//$Task->Cstatus->updateOrCreate(['ticket' => $tkt],$UpdateArray);
		}
		if($status) {
			$CreateArray = ['ticket'	=>	$Task->ticket,	'status'	=>	$status, 'start_time'	=>	time(), 'user'	=>	$this->getAuthUser()->partner];
			if($status_text) $CreateArray['status_text'] = $status_text; else $CreateArray['status_text'] = null;
			\App\Models\TaskCurrentStatus::updateOrCreate(['ticket' => $Task->ticket, 'task' => $Task->id],$CreateArray);
			return $Task->Status()->create($CreateArray);
		}
	}
	
	private function DeleteResponderOfTask($tsk){
		$tsk = $this->GetTaskId($tsk);
		\App\Models\TaskResponder::whereTask($tsk)->delete();
		$this->CreateStatus($tsk,'CREATED','SYSTEM: Responder deleted by '.$this->getAuthUser()->partner);
		return $tsk;
	}
	
	private function RemoveTaskFromAfters($tsk){
		return $this->GetDependentTasks($tsk)->each(function($item)use($tsk){
			$IDs = $item->handle_after->pluck('id')->toArray();
			if(count($IDs) == 1 && $IDs[0] == $tsk) { $item->update(['handle_after'	=>	null]); $this->UpdateTaskInactivity($item); }
			else { $NewIDs = array_diff($IDs,[$tsk]); $item->update(['handle_after'	=>	\App\Http\Controllers\CommonController::ItemIDsJoinForDB($NewIDs)]); $this->UpdateTaskInactivity($item); } 
		});
	}
	
	private function GetDependentTasks($tsk){
		$tsk = $this->GetTaskId($tsk);
		return TicketTask::where('handle_after','like','%-'.$tsk.'-%')->get();
	}
	
	private function DeleteTask($tsk){
		$tsk = $this->GetTaskObj($tsk); $tkt = $tsk->ticket;
		$tsk->delete();
		$tktObj = \App\Models\Ticket::with(['Tasks'])->whereCode($tkt)->first();
		$TicletController = new \App\Http\Controllers\TicketController();
		$CurrentTasksCount = $TicletController->RearrangeTaskSequences($tktObj->Tasks);
		$TicletController->UpdateTicketStatusTaskRelated($tktObj);
	}
	
	private function getAuthUser(){
		return (Auth()->user())?:(Auth()->guard("api")->user());
	}
	
	private function HoldTaskTicket($tsk, $task_text, $ticket_text){
		$tsk = $this->GetTaskId($tsk);
		$Task = TicketTask::with(['Status','Ticket.Status'])->whereId($tsk)->first();
		$this->CreateStatus($tsk,'HOLD',$task_text,true);
		if($ticket_text !== false) $this->TicketNewStatus($Task->ticket, 'HOLD', $this->getAuthUser()->partner, $ticket_text, true);
	}
	
	private function WorkTaskTicket($tsk){
		$tsk = $this->GetTaskId($tsk); $Task = TicketTask::with(['Ticket.Cstatus','Cstatus'])->whereId($tsk)->first();
		if($Task->Cstatus->status != 'WORKING' || $Task->Cstatus->user != $this->getAuthUser()->partner){
			$this->CreateStatus($tsk,'WORKING',null,($Task->Cstatus->status == 'WORKING' || $Task->Cstatus->status == 'HOLD'));
			if($Task->Ticket->Cstatus->status != 'INPROGRESS' || ($Task->Ticket->Cstatus->status == 'INPROGRESS' && $Task->Ticket->Cstatus->status_text == $Task->current_task_closed_ticket_not_closable_status_text))
				$this->TicketNewStatus($Task->ticket, 'INPROGRESS', $this->getAuthUser()->partner, 'SYSTEM: Task status changed to WORKING.',($Task->Ticket->Cstatus->status == 'HOLD' || $Task->Ticket->Cstatus->status == 'INPROGRESS'));				
		}
	}
	
	private function TicketNewStatus($tkt, $status, $user, $text = null, $end_previous = false){
		return \App\Http\Controllers\TicketController::TicketNewStatus($tkt, $status, $user, $text, $end_previous);
	}
	
	private function CloseTask($tsk){
		$this->CreateStatus($tsk,'CLOSED',null,true); $this->UpdateDependentTaskActivity($tsk);
		$Task = $this->GetTaskObj($tsk);
		if(\App\Http\Controllers\TicketController::isTicketClosable($Task->ticket)){
			$this->TicketNewStatus($Task->ticket, 'CLOSED', $this->getAuthUser()->partner, 'SYSTEM: All Task CLOSED.',true);
			$Ticket = \App\Models\Ticket::whereCode($Task->ticket)->with(['Customer' => function($Q){ $Q->with('Details','Logins'); },'Category','Product','Edition','Team.Team' => function($Q){ $Q->with('Details','Logins'); },'CreatedBy' => function($Q){ $Q->with('Roles','Logins'); }])->first();
			$this->SendTicketCloseInfo($Ticket); $this->SmsTicketCloseInfo($Ticket);
		} else {
			$this->TicketNewStatus($Task->ticket, 'INPROGRESS', $this->getAuthUser()->partner, $Task->current_task_closed_ticket_not_closable_status_text,true);
		}
	}
	
	private function GetLatestPackageDetails($PRD, $EDN){
		$Pkgs = \App\Models\PackageVersion::where(['product' => $PRD, 'edition' => $EDN])->select('package')->with('Package')->has('Package')->get()->keyBy('package')->keys();
		return $Pkgs->mapWithKeys(function($PKG)use($PRD, $EDN){
			return [$PKG => \App\Http\Controllers\PackageVersionController::get_latest($PRD, $EDN, $PKG)];
		})->filter();
		//App\Http\Controllers\PackageVersionController::get_latest();
	}
	
	private function SendTicketCloseInfo($Ticket){
		$this->SendMail('TKTCloseInfo',$Ticket);
	}
	
	private function SmsTicketCloseInfo($Ticket){
		if($Ticket->CreatedBy->Roles->contains('name','distributor')) $this->SendSms('TKTClosedToDistributor',$Ticket,$Ticket->CreatedBy);
		$this->SendSms('TKTClosedToCustomer',$Ticket,$Ticket->Customer);
	}
	
	private function SendMail($Mail,$Object){
		$Class = '\\App\\Mail\\' . $Mail;
		\App\Libraries\Mail::init()->queue(new $Class($Object))->to($Object->Customer)->cc($Object->Team->Team)->send();
	}
	
	private function SendSms($SMS,$Object,$To){
		$Class = '\\App\\Sms\\' . $SMS;
		\App\Libraries\SMS::init(new $Class($Object))->send($To);
	}
	
	private function ORMAppendSearch($ORM, $Text){
		if(trim($Text) == "") return $ORM;
		$Like = "%".$Text."%";
		$ORM->where(function($Q)use($Like){
			$Q->where('ticket','like',$Like)->orWhere('description','like',$Like)
			->orWhereHas('Ticket',function($Q)use($Like){ $Q->where('title','like',$Like)->orWhere('description','like',$Like); })
			->orWhereHas('Ticket.Customer',function($Q)use($Like){ $Q->where('name','like',$Like)->orWhere('code','like',$Like); })
			->orWhereHas('Cstatus',function($Q)use($Like){ $Q->where('status','like',$Like); });
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
	
	private function ViewTasks($Status, $View = 'index'){
		$ORM = $this->GetBasicTSKORM();
		$ORM = $this->ORMCurrentStatus($ORM,$Status);
		if(Request()->search_text) $ORM = $this->ORMAppendSearch($ORM,Request()->search_text);
		return $this->PaginateAndView($ORM,$View);
	}

	private function GetBasicTSKORM(){
		return TicketTask::own()->with(['Ticket.Customer','Cstatus','Stype','Status','Responder'])->latest();
	}

	private function PaginateAndView($ORM, $View = 'index'){
		$Data = $ORM->paginate($this->view_paginate_length);
		$Links = $Data->appends(['search_text' => Request()->search_text])->links();
		$Data = $Data->groupBy('ticket');
		return view('tsk.'.$View,compact('Data','Links'));
	}
}

