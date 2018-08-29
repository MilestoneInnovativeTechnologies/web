<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\TicketCoversation;
use App\Libraries\Mail;
use Storage;

class TicketCoversationController extends Controller
{
	
	protected $SDFile = '', $SD = [];	

	protected function UpdateSD(){
		$this->SD = json_decode(Storage::get($this->SDFile),true);
	}
	
	protected function UpdateSDFile(){
		Storage::put($this->SDFile,json_encode($this->SD));
	}
	
	protected function sd($key, $val = null){
		if(is_null($val)) return array_key_exists($key,$this->SD)?$this->SD[$key]:null;
		$this->SD[$key] = $val;
		$this->UpdateSDFile();
	}
	
	public $chat_file_upload_path = "ticket/convfile";
	public $min_activity_time_for_mail_notification = 10 * 60;

	public function __construct(){
		$Apply = ['store_chat_conversation','get_all_chat_conversation','upload_conv_file','send_download_link_chat','store_link_conversation','send_download_link_mail','get_latest_conversation','send_print_object_chat','send_print_object_mail','chat_generalupload_form','mail_generalupload_form','mail_generalupload_file','send_thirdparty_downloadlink'];
		$this->middleware(function($request, $next){
			$segs = $request->segments(); $user = $segs[4]; $tkt = $segs[5];
			$TempStorage = 'tempStorage/tkt_'.$user.'_'.$tkt.'.json';
			if(!Storage::exists($TempStorage)){
				$AUser = (Auth()->user())?:(Auth()->guard("api")->user()); 
				if($AUser->partner == $user && \App\Models\Ticket::find($tkt)){
					$Data['user'] = $user; $Data['ticket'] = $tkt; Storage::put($TempStorage,json_encode($Data));
					event(new \App\Events\ConversationInit($tkt,$user));
				}
				elseif(Auth()->user()) return back()->with(['info'	=>	true, 'type'	=>	'danger', 'text'	=>	'You are not allowed an access here.']);
				else return response("Access not allowed here.", 401);
			}
			$this->SDFile = $TempStorage; $this->UpdateSD();
			event(new \App\Events\ConvUpdateUserActivity($tkt,$user));
			return $next($request);
		})->only($Apply);
		
	}

	
	public function index(){
		return TicketCoversation::all();
	}
	
	public function store_chat_conversation(Request $request){
		if(!$request->type || strtolower($request->type) == 'chat') {
			$this->StoreTextConversation($request->tkt, $request->ctx);
		}
		return $this->SendRemainingChats();
	}

	public function get_all_chat_conversation(){
		return $this->SendAllChats();
	}
	
	public function upload_conv_file(Request $request){
		$tkt = $request->ticket; $user = $request->user; $file = $request->chat_file;
		$Path = $this->StoreChatFile($file, $tkt);
		$this->StoreFileConversation($tkt, $Path, $file);
		return $this->SendRemainingChats();
	}
	
	public function store_link_conversation(Request $request){
		$tkt = $request->tkt; $name = $request->name; $link = $request->link; $desc = $request->desc; 
		$this->StoreLinkConversation($tkt, $name, $link, $desc);
		return $this->SendRemainingChats();
	}
	
	public function send_download_link_chat(Request $request){
		$Link = $this->CreatePackageDownloadLink($this->sd('user'), $request->PRD, $request->EDN, $request->PKG, $request->TYP);
		$Name = 'Software Download Link'; 
		$Details = $this->GetProductDetails($request->PRD, $request->EDN, $request->PKG);
		$Desc = 'Product: ' . $Details->Product->name . ' ' . $Details->Edition->name . ' Edition' . '<br>Package: ' . $Details->Package->name . ', Version: ' . $Details->version_numeric;
		$this->StoreLinkConversation($this->sd('ticket'), $Name, $Link, $Desc);
		return $this->SendRemainingChats();
	}
	
	public function send_download_link_mail(Request $request){
		$Package = $this->GetProductDetails($request->PRD, $request->EDN, $request->PKG); $Package->Product->load('Editions');
		$Ticket = \App\Models\Ticket::whereCode($this->sd('ticket'))->with(['Customer.Logins','CreatedBy.Logins'])->first();
		$Team = \App\Models\TicketSupportTeam::whereTicket($this->sd('ticket'))->first()->Team->name;
		$Link = $this->CreatePackageDownloadLink($this->sd('user'), $request->PRD, $request->EDN, $request->PKG, $Package->Package->type);
		$Mail = Mail::init()->queue(new \App\Mail\SupportProductDownload($Package, $Ticket, $Link, $Team, $this->sd('user')))->to($Ticket->Customer);
		if($Ticket->created_by != $this->sd('user') && $Ticket->customer != $Ticket->created_by) $Mail->cc($Ticket->CreatedBy);
		$Mail->send();
		$this->StoreInfoConversation($this->sd('ticket'), $Package->Product->name.' '.$Package->Edition->name.' Edition\'s ' . $Package->Package->name . ' Package, have beed mailed to customer.');
		return $this->SendRemainingChats();
	}
	
	public function get_latest_conversation(){
		return $this->SendRemainingChats();
	}
	
	public function send_print_object_chat($customer, $reg_seq, Request $request){
		$PO = \App\Models\CustomerPrintObject::withOutGlobalScope('active')->find($request->code); $Link = Route('support.printobject.download',$this->getPrintObjectDownloadKey($PO,'CHAT DOWNLOAD'));
		$this->StoreLinkConversation($this->sd('ticket'), 'Download Print Object', $Link, 'Download print object of function name: '.$PO->function_name.', approved by: '.$PO->User->name.', on '.date('D d/M - h:i A'));
		return $this->SendRemainingChats();
	}
	
	public function send_print_object_mail($customer, $reg_seq, Request $request){
		$PO = \App\Models\CustomerPrintObject::withOutGlobalScope('active')->find($request->code)->load('Customer.Logins');
		$MailKey = md5(time()); $Key = $this->getPrintObjectDownloadKey($PO,$MailKey);
		Mail::init()->queue(new \App\Mail\SupportPrintObjectDownload($PO, $Key))->key($MailKey)->send($PO->Customer);
		$this->StoreInfoConversation($this->sd('ticket'), 'Print Object of function name: '.$PO->function_name.', have been mailed to customer.');
		return $this->SendRemainingChats();
	}
	
	public function chat_generalupload_form($user, $tkt, Request $request){
		$gu = \App\Models\GeneralUpload::find($request->code);
		$this->StoreLinkConversation($tkt, 'General upload form link', $gu->form, 'Upload a file using a form');
		return $gu;
	}
	
	public function mail_generalupload_form($user, $tkt, Request $request){
		$gu = \App\Models\GeneralUpload::find($request->code)->load('Customer.Logins');
		Mail::init()->queue(new \App\Mail\GUFLink($gu))->send($gu->Customer);
		$this->StoreInfoConversation($tkt, 'General upload form link have been mailed to customer.');
		return $gu;
	}
	
	public function mail_generalupload_file($user, $tkt, Request $request){
		$gu = \App\Models\GeneralUpload::find($request->code)->load('Customer.Logins');
		Mail::init()->queue(new \App\Mail\GUFDownloadLink($gu))->send($gu->Customer);
		$this->StoreInfoConversation($tkt, 'Download link to uploaded file have been mailed to customer.');
		return $gu;
	}
	
	public function send_thirdparty_downloadlink($user, $tkt, Request $request){
		$tpa = \App\Models\ThirdPartyApplication::find($request->code);
		$downloads = ($tpa->public == "Yes") ? 0 : ($request->downloads)?: 1; $url = $tpa->download_url($request->downloads);
		if($request->media == 'chat') return $this->StoreLinkConversation($this->sd('ticket'), $tpa->name, $url, 'Download link of Software: '.$tpa->name);
		$this->mail_thirdparty_downloadlink($tpa,$url,$this->GetMailers($tkt, $user));
		
	}
	
	public function mail_thirdparty_downloadlink($tpa, $url, $mailers){
		$Mail = Mail::init()->queue(new \App\Mail\ThirdPartyAppDownload($tpa,$url));
		if($mailers['cc']) $Mail = $Mail->cc($mailers['cc']); $Mail->send($mailers['to']);
		$this->StoreInfoConversation($this->SD('ticket'), 'Download link of '.$tpa->name.' has been mailed to '.$mailers['to']->name.'.');
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
		
	private function getAuthUser(){
		return (Auth()->user())?:(Auth()->guard("api")->user());
	}
	
	private function StoreTextConversation($tkt, $txt){
		if(trim($txt) == "") return null;
		return $this->StoreConversation($tkt, $this->sd('user'), $txt);
	}
	
	private function StoreFileConversation($tkt, $path, $file){
		if(trim($path) == "") return null;
		$FileArray['file'] = $path; $StoreProcedure = ['name'=>'getClientOriginalName','ext'=>'extension','mime'=>'getMimeType','size'=>'getSize'];
		foreach($StoreProcedure as $Field => $FnName) $FileArray[$Field] = $file->$FnName();
		return $this->StoreConversation($tkt, $this->sd('user'), json_encode($FileArray), 'FILE');
	}
	
	private function StoreLinkConversation($tkt, $name, $link, $desc = null){
		if(trim($tkt) == "" || trim($name) == "" || trim($link) == "") return null;
		$LinkArray = ['name' => $name, 'description' => $desc, 'link' => $link];
		return $this->StoreConversation($tkt, $this->sd('user'), json_encode($LinkArray), 'LINK');
	}
	
	private function StoreInfoConversation($tkt, $txt){
		if(trim($tkt) == "" || trim($txt) == "") return null;
		return $this->StoreConversation($tkt, $this->sd('user'), $txt, 'INFO');
	}
	
	private function SendRemainingChats(){
		$TCs = $this->GetRemainingChats();
		return $this->SendTicketConversations($TCs);
	}
	
	private function SendAllChats(){
		$TCs = $this->GetAllChats();
		return $this->SendTicketConversations($TCs);
	}
	
	private function GetRemainingChats(){
		$UserLastId = $this->GetLastConvSendId();
		$user = $this->sd('user'); $tkt = $this->sd('ticket');
		$LastId = $this->GetLastConv($tkt);
		if($UserLastId < $LastId)	return TicketCoversation::where(['ticket' => $tkt])->where('id','>',$UserLastId)->with(['User'	=>	function($Q){ $Q->select('code','name'); }])->oldest()->get();
		return collect([]);
	}
	
	private function GetLastConvSendId(){
		$id = $this->sd('last_send_conv_id');
		return (is_null($id)) ? 0 : $id;
	}
	
	private function SendTicketConversations($TCs){
		if($TCs->isNotEmpty()) $this->UpdateLastSendConvId($TCs);
		$TCs = $this->UpdateFileTypesWithDownloadLink($TCs);
		return $TCs->toArray();
	}
	
	private function UpdateLastSendConvId($TCs){
		$last = $TCs->last(); $lastId = $last->id;
		$this->sd('last_send_conv_id',$lastId);
	}
	
	private function GetAllChats(){
		$user = $this->sd('user'); $tkt = $this->sd('ticket');
		return TicketCoversation::where(['ticket' => $tkt])->with(['User'	=>	function($Q){ $Q->select('code','name'); }])->oldest()->get();
	}
	
	private function UpdateFileTypesWithDownloadLink($TCs){
		return $TCs->map(function($item, $key){
			if($item->type == 'FILE'){
				$Content = json_decode($item->content,true);
				$Content['link'] = Route("ticket.uploadedfile.download",['tkt' => $item->ticket, 'id' => $item->id]);
				$item->content = json_encode($Content);
			}
			return $item;
		});
	}
	
	private function CreatePackageDownloadLink($USR, $PRD, $EDN, $PKG, $TYP){
		return Route("software.download",['key' => \App\Http\Controllers\KeyCodeController::Encode(['expiry','user','product','edition','package','type'],[strtotime("+18 hours"), $USR, $PRD, $EDN, $PKG, $TYP])]);
	}
	
	private function GetProductDetails($PRD, $EDN, $PKG){
		return \App\Http\Controllers\PackageVersionController::get_latest($PRD, $EDN, $PKG);
	}
	
	private function getPrintObjectDownloadKey($PO,$mail){
		$PAry = ['mail','code','name','link'];
		$VAry = [$mail, $PO->code, $PO->function_name, $PO->file];
		return \App\Http\Controllers\KeyCodeController::Encode($PAry, $VAry);
	}
	
	private function StoreChatFile($file,$tkt){
		$Path = $this->chat_file_upload_path . "/" . $tkt;
		if($file->extension()) return $file->store($Path);
		$ext = mb_strrchr($file->getClientOriginalName(),'.');
		$filename = $file->hashName(); if(mb_substr($filename,-1) == ".") $filename = mb_substr($filename,0,-1);
		return $file->storeAs($Path,$filename.$ext);
	}
	
	
	
	
	
	private function StoreConversation($tkt, $user, $content, $type = "CHAT"){
		$Ticket = TicketCoversation::create(['ticket' => $tkt, 'user' => $user, 'content' => $content, 'type' => $type]);
		$this->UpdateConvSupportFile($tkt, $user, $Ticket->id);
		return $Ticket;
	}
	
	
	
	private function GetMailers($tkt, $user){
		$Tkt = \App\Models\Ticket::with('Customer','CreatedBy.Logins')->whereCode($tkt)->first();
		$To = (in_array($Tkt->CreatedBy->code,[$user,$Tkt->customer])) ? $Tkt->Customer : $Tkt->CreatedBy;
		$Cc = ($To->code != $Tkt->customer) ? $Tkt->Customer : null;
		return ['to' => $To, 'cc' => $Cc];
	}
	
	private function GetTicketUsers($tkt){
		$CSFC = new \App\Http\Controllers\ConversationSupportFileController();
		return $CSFC->get_users($tkt);
	}
	
	private function UpdateConvSupportFile($tkt, $user, $cid){
		$CSFC = new \App\Http\Controllers\ConversationSupportFileController();
		$LSCT = $CSFC->get_last_conv_time($tkt);
		$CSFC->add_user_conv($tkt, $user, $cid);
		if(time()-$LSCT >= $this->min_activity_time_for_mail_notification) $this->NotifyUsersNewConversation($tkt, $user, $CSFC->get_users($tkt));
	}
	
	private function GetLastConv($tkt){
		$CSFC = new \App\Http\Controllers\ConversationSupportFileController();
		return $CSFC->get_last_conv($tkt);
	}
	
	private function NotifyUsersNewConversation($tkt, $initiator, $users){
		$mail_users = array_diff($users,[$initiator]); if(empty($mail_users)) return;
		$Ticket = \App\Models\Ticket::whereCode($tkt)->with(['Customer' => function($Q){ $Q->with('Details','Logins'); },'Team.Team' => function($Q){ $Q->with('Details','Logins'); },'CreatedBy' => function($Q){ $Q->with('Roles','Logins'); },'Conversations','Tasks' => function($Q){ $Q->whereHas('Cstatus',function($R){ $R->where('status','WORKING'); })->with(['Responder.Responder.Logins' => function($Q){ $Q->select('id','partner','email'); }]); }])->first();
		$this->SendMail('TKTConversationNotify',$Ticket,$mail_users);
	}
	
	private function SendMail($Mail,$Object,$To = null){
		$Class = '\\App\\Mail\\' . $Mail;
		if(is_null($To)) $To = $Object->Customer;
		$Mail = Mail::init()->queue(new $Class($Object));
		if(is_array($To)) foreach($To as $to) $Mail = $Mail->to($to); else $Mail = $Mail->to($To);
		$Mail->send();
	}
	
}
