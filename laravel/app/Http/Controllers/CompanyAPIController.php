<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CompanyAPIController extends Controller
{
	
	private $recent_time = '1 month', $expire_notify_start_time = '35 days', $expire_notify_end_time = '25 days';
	private $recent_registration_period = '115 days';
	
	public function get_registration_requests(){
		return ['data' => $this->GetRegReqs(), 'get_licence_url' => route('download.customer.licence',['--customer--','--seqno--']), 'do_register_url' => route('customer.registration',['--customer--','--seqno--'])];
	}
	
	public function get_unregistered_recent_customers(){
		$RecentCustomers = $this->GetRecentCustomers();
		return $this->FilterUnregistered($RecentCustomers);
	}
	
	public function get_expiring_customers(){
		$ExpiringUnRegs = $this->GetExpiringUnregs();
		return $this->SetExpiringSortOrder($ExpiringUnRegs);
	}
	
	public function recently_registered_customers(){
		return $RecentlyRegistered = $this->GetRecentlyRegisteredCustomers();
	}
	
//	public function product_registration_summary(Request $request){
//		$ORM = \App\Models\CustomerRegistration::select('product','edition',\DB::raw('count(seqno) as reg_count'))->groupBy(['product','edition'])->whereNotNull('registered_on')->with(['Product' => function($Q){ $Q->select('code','name'); },'Edition' => function($Q){ $Q->select('code','name'); }]);
//		if(mb_strpos($request->period,'&') !== false) $request->merge(['till' => explode("&",$request->period)[1],'period' => explode("&",$request->period)[0]]);
//		if($request->period && $period = $request->period) $ORM = $ORM->where('created_at','>=',date('Y-m-d 00:00:00',( (is_numeric($period)) ? $period : strtotime('-'.$period,strtotime(date('Y-m-d 00:00:00')) ))));
//		if($request->till && $till = $request->till) $ORM = $ORM->where('created_at','<',date('Y-m-d 00:00:00',( (is_numeric($till)) ? $till : strtotime('-'.$till,strtotime(date('Y-m-d 00:00:00')) ))));
//		$Reg = $ORM->get();
//		$ORM = \App\Models\CustomerRegistration::select('product','edition',\DB::raw('count(seqno) as unreg_count'))->groupBy(['product','edition'])->whereNull('registered_on')->with(['Product' => function($Q){ $Q->select('code','name'); },'Edition' => function($Q){ $Q->select('code','name'); }]);
//		if($request->period && $period = $request->period) $ORM = $ORM->where('created_at','>=',date('Y-m-d 00:00:00',( (is_numeric($period)) ? $period : strtotime('-'.$period,strtotime(date('Y-m-d 00:00:00')) ))));
//		if($request->till && $till = $request->till) $ORM = $ORM->where('created_at','<',date('Y-m-d 00:00:00',( (is_numeric($till)) ? $till : strtotime('-'.$till,strtotime(date('Y-m-d 00:00:00')) ))));
//		$Unreg = $ORM->get();
//		return [$Reg, $Unreg];
//	}
	
	public function product_registration_summary(Request $request){
		$ORM = \App\Models\CustomerRegistration::select('product','edition',\DB::raw('count(seqno) as reg_count'))->groupBy(['product','edition'])->whereNotNull('registered_on')->with(['Product' => function($Q){ $Q->select('code','name'); },'Edition' => function($Q){ $Q->select('code','name'); }]);
		if(mb_strpos($request->period,'&') !== false) $request->merge(['till' => explode("&",$request->period)[1],'period' => explode("&",$request->period)[0]]);
		if($request->period && $period = $request->period) $ORM = $ORM->where('registered_on','>=',date('Y-m-d',( (is_numeric($period)) ? $period : strtotime('-'.$period,strtotime(date('Y-m-d 00:00:00')) ))));
		if($request->till && $till = $request->till) $ORM = $ORM->where('registered_on','<',date('Y-m-d',( (is_numeric($till)) ? $till : strtotime('-'.$till,strtotime(date('Y-m-d 00:00:00')) ))));
		$Reg = $ORM->get();
		$ORM = \App\Models\CustomerRegistration::select('product','edition',\DB::raw('count(seqno) as unreg_count'))->groupBy(['product','edition'])->whereNull('registered_on')->with(['Product' => function($Q){ $Q->select('code','name'); },'Edition' => function($Q){ $Q->select('code','name'); }]);
		if($request->period && $period = $request->period) $ORM = $ORM->where('created_at','>=',date('Y-m-d 00:00:00',( (is_numeric($period)) ? $period : strtotime('-'.$period,strtotime(date('Y-m-d 00:00:00')) ))));
		if($request->till && $till = $request->till) $ORM = $ORM->where('created_at','<',date('Y-m-d 00:00:00',( (is_numeric($till)) ? $till : strtotime('-'.$till,strtotime(date('Y-m-d 00:00:00')) ))));
		$Unreg = $ORM->get();
		return [$Reg, $Unreg];
	}
	
	public function get_ticket_summary(Request $request){
		if($request->period){
			if(mb_strpos($request->period,'&') !== false) list($period,$till) = explode("&",$request->period);
			else { $till = strtotime('tomorrow'); $period = $request->period; }
			$from =  (is_numeric($period)) ? intval($period) : strtotime('-'.$period,strtotime(date('Y-m-d 00:00:00')) );
			$to =  (is_numeric($till)) ? intval($till) : strtotime('-'.$till,strtotime(date('Y-m-d 00:00:00')) );
		} else {
			$from = strtotime('today');
			$to = strtotime('tomorrow');
		}
		return $this->GetTicketSummary($from,$to);
	}
	
	public function current_active_tickets_summary(Request $request){
		return $this->GetCurrentActiveTickets();
	}
	
	public function partner_search(Request $request){
		$like = '%'.$request->term.'%';
		return \App\Models\Partner::with('Details','Logins','Roles')->where(function($Q) use($like){
			$Q->orWhere('code','like',$like)
				->orWhere('name','like',$like)
				->orWhereHas('Details',function($Q) use($like){ $Q->where('phone','like',$like); })
				->orWhereHas('Logins',function($Q) use($like){ $Q->where('email','like',$like); })
				;
		})->get();
	}
	
	public function ticket_search(Request $request){
		$Like = '%'.$request->term.'%';
		$ORM = \App\Models\Ticket::with('Category','Customer','Product','Edition','Team.Team','Cstatus','Customer.Details','Customer.Logins','Closure','Status')->latest();
		return $ORM->where(function($Q)use($Like){
			$Q->where('code','like',$Like)->orWhere('title','like',$Like)->orWhere('priority','like',$Like)
			->orWhereHas('Category',function($Q)use($Like){ $Q->where('name','like',$Like); })
			->orWhereHas('Customer',function($Q)use($Like){ $Q->where('name','like',$Like)->orWhere('code','like',$Like); })
			->orWhereHas('Product',function($Q)use($Like){ $Q->where('name','like',$Like)->orWhere('code','like',$Like); })
			->orWhereHas('Edition',function($Q)use($Like){ $Q->where('name','like',$Like)->orWhere('code','like',$Like); })
			->orWhereHas('Team.Team',function($Q)use($Like){ $Q->where('name','like',$Like)->orWhere('code','like',$Like); })
			->orWhereHas('Customer.Details',function($Q)use($Like){ $Q->where('phone','like',$Like); })
			->orWhereHas('Customer.Logins',function($Q)use($Like){ $Q->where('email','like',$Like); });
		})->get();
	}
	
	
	
	
	
	
	
	
	private function GetRegReqs(){
		return \App\Models\CustomerRegistration::whereNull("serialno")->whereNull("key")->whereNotNull("lic_file")
			->with("customer.parentDetails.parentDetails","product","edition")
			->orderBy("updated_at","desc")
			->get();
	}
	
	private function GetRecentCustomers(){
		return \App\Models\CustomerRegistration::with('Customer.ParentDetails.ParentDetails','Product','Edition')->latest()->where('created_at','>=',date('Y-m-d 00:00:00',strtotime('-'.$this->recent_time)))->get();
	}
	
	private function FilterUnregistered($CustomerCollection){
		return $CustomerCollection->filter(function($Item, $Key){
			return (is_null($Item->key) || is_null($Item->registered_on) || is_null($Item->serialno));
		});
	}
	
	private function FilterRegistered($CustomerCollection){
		return $CustomerCollection->filter(function($Item, $Key){
			return (!is_null($Item->key) && !is_null($Item->registered_on) && !is_null($Item->serialno));
		});
	}
	
	private function GetExpiringUnregs(){
		return \App\Models\CustomerRegistration::with('Customer.ParentDetails.ParentDetails','Product','Edition')->where(function($Q){
			$Q->whereNull('key')->orWhereNull('serialno')->orWhereNull('registered_on');
		})->where(function($Q){
			$Q->where('created_at','>=',date('Y-m-d 00:00:00',strtotime('-'.$this->expire_notify_start_time)))->where('created_at','<=',date('Y-m-d 00:00:00',strtotime('-'.$this->expire_notify_end_time)));
		})->get();
	}
	
	private function SetExpiringSortOrder($Collection){
		$Today = strtotime(date('Y-m-d 00:00:00')); $Order = [0,-1,1,-2,2];
		return $Collection->transform(function($Item, $Key)use($Today){
			$ItemDate = strtotime((explode(" ",$Item->created_at)[0])." 00:00:00");
			$diff = 30 - (($Today - $ItemDate)/(60*60*24)); $SO = abs($diff * 2); if($diff<0) $SO--;
			$Item->sort_order = intval($SO);
			$Item->expire_on = date('Y-m-d 23:59:59',$ItemDate + (30*24*60*60));
			return $Item;
		});
	}
	
	private function GetRecentlyRegisteredCustomers(){
		return \App\Models\CustomerRegistration::with('Customer.ParentDetails.ParentDetails','Product','Edition')->where(function($Q){
			$Q->whereNotNull('key')->whereNotNull('serialno')->whereNotNull('registered_on');
		})->where(function($Q){
			$Q->where('registered_on','>=',date('Y-m-d',strtotime('-'.$this->recent_registration_period)));
		})->latest('registered_on')->get();
	}
	
	private function GetTicketSummary($from,$to){
		return \DB::table('ticket_current_status')
			->select('partners.name','ticket_support_teams.team','ticket_current_status.status',\DB::raw('count(`ticket_current_status`.`ticket`) as tickets'))
			->join('ticket_support_teams','ticket_current_status.ticket','=','ticket_support_teams.ticket')
			->join('partners','partners.code','=','ticket_support_teams.team')
			->join('tickets','tickets.code','=','ticket_current_status.ticket')
			->groupBy('partners.name','ticket_support_teams.team','status')
			->where('tickets.created_at','>=',date('Y-m-d 00:00:00',$from))
			->where('tickets.created_at','<',date('Y-m-d 00:00:00',$to))
			->get();
	}
	
	private function GetCurrentActiveTickets(){
		$ActiveStatuses = ['NEW','OPENED','INPROGRESS','HOLD'];
		//return \App\Models\TicketCurrentStatus::whereIn('status',$ActiveStatuses)->get();
		return \DB::table('ticket_current_status')
			->select('partners.name','ticket_support_teams.team','ticket_current_status.status',\DB::raw('count(`ticket_current_status`.`ticket`) as tickets'))
			->join('ticket_support_teams','ticket_current_status.ticket','=','ticket_support_teams.ticket')
			->join('partners','partners.code','=','ticket_support_teams.team')
			->join('tickets','tickets.code','=','ticket_current_status.ticket')
			->groupBy('partners.name','ticket_support_teams.team','status')
			->whereIn('ticket_current_status.status',$ActiveStatuses)
			->get();
	}
	
}