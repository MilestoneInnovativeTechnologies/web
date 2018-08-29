<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MaintenanceContract as MC;
use App\Libraries\Mail;

class MaintenanceContractController extends Controller
{
	
	private $MCObj = '';
	public function __construct(){
		$Apply = ['modify','renew'];
		$this->middleware(function($request, $next){
			$segs = $request->segments(); $mc = $segs[1]; $action = $segs[2]; $MC = \App\Models\MaintenanceContract::find($mc);
			if(!$MC) return back()->with(['info'	=>	true, 'type'	=>	'danger', 'text'	=>	'Contract doesn\'t exists.']);
			if(!in_array($action,$MC->available_actions)) return back()->with(['info'	=>	true, 'type'	=>	'danger', 'text'	=>	'The action, '.$action.', is not available for this Contract.']);
			$this->MCObj = $MC;
			return $next($request);
		})->only($Apply);

	}
	
	
	
	
	private $IndexTitles = ['active' => 'Maintenance Contracts', 'expsoon' => 'Expiring Soon Contracts', 'expiring' => 'Expiring Contracts', 'inactive' => 'Expired/Inactive Contracts', 'justexp' => 'Recently expired contracts', 'expired' => 'Expired contracts', 'upcoming' => 'Upcoming Contracts'];
	private $AnnualMaintenancePercentage = 30;
	
	public function index($status = 'active'){
		//return MC::all();
		$ORM = MC::with('Customer','Registration');//->oldest('end_time');
		$search_text = (Request()->search_text)?:null; $status = (Request()->status)?:$status;
		if($search_text){
			$Like = "%".$search_text."%";
			$ORM->where(function($Q)use($Like){
				$Q->where('code','like',$Like)
				->orWhereHas('Customer',function($Q)use($Like){ $Q->where('name','like',$Like)->orWhere('code','like',$Like); })
				->orWhereHas('Registration',function($Q)use($Like){ $Q->whereHas('Product',function($Qu)use($Like){ $Qu->where('code','like',$Like)->orWhere('name','like',$Like); })->orWhereHas('Edition',function($Qu)use($Like){ $Qu->where('code','like',$Like)->orWhere('name','like',$Like); }); });
			});
		}
		if($status) $ORM->{$status}();
		$MCs = $ORM->paginate(15);
		$appends = [];
		if($search_text) $appends['search_text'] = $search_text;
		if($status) $appends['status'] = $status;
		$Links = $MCs->appends($appends)->links();
		$Title = ($status && array_key_exists($status,$this->IndexTitles))?$this->IndexTitles[$status]:'Maintenance Contracts';
		//return compact('MCs','Links','Title');
		return view('mc.index',compact('MCs','Links','Title'));
	}
	
	public function expiring_soon(){
		return $this->index('expsoon');
	}

	public function inactive(){
		return $this->index('inactive');
	}
	
	public function just_expired(){
		return $this->index('justexp');
	}
	
	public function upcoming(){
		return $this->index('upcoming');
	}
	
	public function view($mc){
		$RF = $this->RenewedFrom($mc);
		$MC = MC::whereCode($mc)->with(['Customer' => function($Q){ $Q->with('Details','Logins'); },'Registration','Renewed'])->first();
		return view('mc.view',compact('RF','MC'));
	}
	
	public function search_customer(){
		return view('mc.sc');
	}
	
	public function new_contract(Request $request){
		$CR = $this->GetRegistration($request->u, $request->s); if(!$CR) return redirect()->back()->with(['info' => true, 'type' => 'warning', 'text' => 'Customer registartion details does not exists']);
		$DR = $this->GetDistributorAndPriceDetails($CR->customer, $CR->product, $CR->edition);
		$MP = $this->AnnualMaintenancePercentage;
		$CH = $this->GetCustomerProductContracts($CR->customer, $CR->seqno);
		return view('mc.new',compact('CR','DR','MP','CH'));
	}
	
	public function store_contract(Request $request){
		$MC = $this->NewContract($request->customer, $request->registration_seq, strtotime($request->start_time . " 00:00:00"), strtotime($request->end_time . " 23:59:59"), $request->amount_actual, $request->amount_paid, $request->payment_note, $request->comments);
		$this->SendMail('MCNewContract',$MC->load('Customer.Logins','Registration'));
		return redirect()->route('mc.index')->with(['info' => true, 'type' => 'success', 'text' => 'Maintenance Contract added successfully']);
	}
	
	public function modify($mc){
		$MC = $this->MCObj;
		$CR = $this->GetRegistration($MC->customer, $MC->registration_seq); if(!$CR) return redirect()->back()->with(['info' => true, 'type' => 'warning', 'text' => 'Customer registartion details does not exists']);
		$DR = $this->GetDistributorAndPriceDetails($CR->customer, $CR->product, $CR->edition);
		$MP = $this->AnnualMaintenancePercentage;
		$CH = $this->GetCustomerProductContracts($CR->customer, $CR->seqno);
		return view('mc.new',compact('CR','DR','MP','CH','MC'));
	}
	
	public function update_contract(Request $request){
		$this->UpdateContract($request->code, $this->GetStartDateToTime($request->start_time), $this->GetEndDateToTime($request->end_time), $request->amount_actual, $request->amount_paid, $request->payment_note, $request->comments);
		return redirect()->route('mc.index')->with(['info' => true, 'type' => 'success', 'text' => 'Maintenance Contract updated successfully']);
	}
	
	public function renew($mc){
		$MC = $this->MCObj;
		$CR = $this->GetRegistration($MC->customer, $MC->registration_seq); if(!$CR) return redirect()->back()->with(['info' => true, 'type' => 'warning', 'text' => 'Customer registartion details does not exists']);
		$DR = $this->GetDistributorAndPriceDetails($CR->customer, $CR->product, $CR->edition);
		$MP = $this->AnnualMaintenancePercentage;
		$CH = $this->GetCustomerProductContracts($CR->customer, $CR->seqno);
		return view('mc.renew',compact('CR','DR','MP','CH','MC'));
	}
	
	public function dorenew($mc, Request $request){
		$MC = $this->MCObj->load('Customer.Logins','Registration');
		$NMC = $this->NewContract($MC->customer, $MC->registration_seq, strtotime($request->start_time . " 00:00:00"), strtotime($request->end_time . " 23:59:59"), $request->amount_actual, $request->amount_paid, $request->payment_note, $request->comments);
		$this->AddRenewedDetails($MC->code, $NMC->code);
		$this->SendMail('MCContractRenewed',[$NMC,$MC],$MC->Customer);
		return redirect()->route('mc.index')->with(['info' => true, 'type' => 'success', 'text' => 'Maintenance Contract Renewed Successfully']);
	}
	
	
	
	
	
	
	
	
	public function search_for_customer(Request $request){
		$like = '%'.$request->st.'%';
		return \App\Models\Customer::with(['Details.City.State.Country','Registration' => function($Q){ $Q->select('customer','seqno','product','edition')->whereNotNull('serialno')->whereNotNull('key')->whereNotNull('registered_on'); }])
			->whereHas('Registration',function($Q){
				$Q->whereNotNull('serialno')->whereNotNull('key')->whereNotNull('registered_on');
			})->where(function($Q)use($like){
				$Q->where('code','like',$like)->orWhere('name','like',$like)
					->orWhereHas('Details',function($Q)use($like){
						$Q->where('address1','like',$like)->orWhere('address2','like',$like)->orWhere('phone','like',$like);
					})->orWhereHas('Logins',function($Q)use($like){
						$Q->where('email','like',$like);
					});
			})->get();
	}
	
	public function send_mail($mail,$mc){
		$MailClass = ['et_mail' => 'MCExpireToday','es_mail' => 'MCExpireSoon','je_mail' => 'MCJustExpired','ex_mail' => 'MCExpired'];
		$MC = MC::find($mc)->load('Customer.Logins','Registration');
		$this->SendMail($MailClass[$mail],$MC,$MC->Customer);
		return $MC;
	}
	
	/*public function send_mail_es_mail($mc){
		$MC = MC::find($mc)->load('Customer.Logins','Registration');
		$this->SendMail('MCExpireSoon',$MC);
		return $MC;
	}
	*/
	public function send_mail_je_mail($mc){
		$MC = MC::find($mc)->load('Customer.Logins','Registration');
		$this->SendMail('MCJustExpired',$MC,$MC->Customer);
		return $MC;
	}
	
	
	
	
	
	private function RenewedFrom($mc){
		return MC::where('renewed_to',$mc)->first();
	}
	
	private function GetCustomerProductContracts($Customer, $Product){
		return MC::where(['customer' => $Customer, 'registration_seq' => $Product])->get();
	}
	
	private function GetDistributorAndPriceDetails($Customer, $Product, $Edition){
		return (new \App\Http\Controllers\CustomerController())->get_distributor_of_partner($Customer)->load(['Pricelist.Details' => function($Q)use($Product, $Edition){
			$Q->where(['product' => $Product, 'edition' => $Edition]);
		}]);
	}
	
	private function GetRegistration($Customer, $Seq){
		return \App\Models\CustomerRegistration::where(['customer' => $Customer, 'seqno' => $Seq])->with(['Customer' => function($Q){ $Q->select('code','name'); },'Product' => function($Q){ $Q->select('code','name'); },'Edition' => function($Q){ $Q->select('code','name'); }])->select('customer','seqno','product','edition')->whereNotNull('registered_on')->whereNotNull('key')->whereNotNull('serialno')->first();
	}
	
	private function isRegistered($Customer, $Seq){
		return $this->GetRegistration($Customer, $Seq)?true:false;
	}
	
	private function GetNewContractSeqNo($Customer, $Seq){
		$ORM = MC::where(['customer' => $Customer, 'registration_seq' => $Seq])->latest('contract_seq')->first();
		return ($ORM)?intval($ORM->contract_seq)+1:1;
	}
	
	private function NewContract($Customer, $Product, $STime, $ETime, $AActual, $APaid, $PNote, $Comment){
		$create_array = ['code' => null, 'customer' => $Customer, 'registration_seq' => $Product, 'contract_seq' => $this->GetNewContractSeqNo($Customer, $Product), 'start_time' => $STime, 'end_time' => $ETime, 'amount_actual' => $AActual, 'amount_paid' => $APaid, 'payment_note' => $PNote, 'comments' => $Comment];
		return MC::create($create_array);
	}
	
	private function UpdateContract($Code, $STime, $ETime, $AActual, $APaid, $PNote, $Comment){
		$update_array = ['start_time' => $STime, 'end_time' => $ETime, 'amount_actual' => $AActual, 'amount_paid' => $APaid, 'payment_note' => $PNote, 'comments' => $Comment];
		$cond_array = ['code' => $Code];
		return MC::where($cond_array)->update($update_array);
	}
	
	private function GetContractDetails($mc){
		return MC::find($mc);
	}
	
	private function GetStartDateToTime($date){
		$full_date = $date . " 00:00:00";
		return strtotime($full_date);
	}
	
	private function GetEndDateToTime($date){
		$full_date = $date . " 23:59:59";
		return strtotime($full_date);
	}
	
	private function AddRenewedDetails($OMC, $NMC){
		return MC::whereCode($OMC)->update(['renewed_to' => $NMC]);
	}
	
	private function SendMail($Mail,$Object,$To = null){
		$Class = '\\App\\Mail\\' . $Mail;
		if(is_null($To)) $To = $Object->Customer;
		Mail::init()->queue(new $Class($Object))->send($To);
	}
	
}
