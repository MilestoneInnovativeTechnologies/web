<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerPrintObject as CPO;
use App\Libraries\Mail;

class CustomerPrintObjectController extends Controller
{
	
	private $print_object_store_path = 'PrintObjects';
	private $print_object_preview_store_path = 'previews';
	private $print_object_disk = 'printobject';
	
	public function index(){
		return view('cpo.index');
	}
	
	public function create(){
		if(in_array(session()->get('_rolename'),['customer','dealer','distributor'])) return redirect()->back()->with(['info' => true, 'type' => 'warning', 'text' => 'You are not allowed to do this action.']);
		return view('cpo.create');
	}

	protected $CPO = null;
	public function store(Request $request){
		$funDets = $this->NewPOFunctionDetails($request); if(!is_array($funDets)) return redirect()->back()->with(['info' => true, 'type' => 'danger', 'text' => 'Function code or Function name cannot be empty.']);
		if(is_null($request->customer) || is_null($request->reg_seq)) return redirect()->back()->with(['info' => true, 'type' => 'danger', 'text' => 'Customer and Product seems to be empty. Please corret it.'])->withInput();
		if(is_null($request->file)) return redirect()->back()->with(['info' => true, 'type' => 'danger', 'text' => 'Print Object file cannot be empty.']);
		$file = $this->HandlePOFile($request->file, $request->customer, $request->reg_seq);
		$preview = null; if($request->hasFile('preview')) $preview = $this->HandlePOPFile($request->preview, $request->customer, $request->reg_seq);
		$print_name = $this->NewPrintName($request);
		$PO = $this->AddPrintObject($request->customer,$request->reg_seq,$funDets[0],$funDets[1],$file,$print_name,$preview);
		$this->ActivatePrintObject($PO->code);
		return redirect()->route('cpo.index')->with(['info' => true, 'type' => 'success', 'text' => 'Print Object added successfully.']);
	}
	
	public function details($code){
		return view('cpo.details',compact('code'));
	}
	
	public function download($code){
		$CPO = CPO::withoutGlobalScope('active')->find($code);
		return response()->download(storage_path("app/".$CPO->file));
	}
	
	public function activate($code){
		$this->ActivatePrintObject($code);
		return redirect()->route('cpo.details',$code)->with(['info' => true, 'type' => 'success', 'text' => 'Selected print object marked as Active.']);
	}
	
	public function mail($code){
		$CPO = CPO::withoutGlobalScope('active')->whereCode($code)->with('Customer.Logins')->first();
		Mail::init()->queue(new \App\Mail\PODownload($CPO))->send($CPO->Customer);
		return redirect()->back()->with(['info' => true, 'type' => 'success', 'text' => 'Download link have been mailed to customer email address.']);
	}
	
	public function preview($code, Request $request){
		if($request->hasFile('preview')) $preview = $this->HandlePOPFile($request->preview, $request->customer, $request->reg_seq);
		$cpo = CPO::withoutGlobalScope('active')->find($code); $this->DeletePreviewFile($cpo->preview);
		$cpo->preview = $preview; $cpo->save();
		return redirect()->back()->with(['info' => true, 'type' => 'success', 'text' => 'Print Object Preview updated successfully.']);
	}

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function search_for_customer(Request $request){
		$like = '%'.$request->st.'%';
		return \App\Models\Customer::with(['Details.City.State.Country','Registration' => function($Q){ $Q->select('customer','seqno','product','edition','registered_on'); }])
			->where(function($Q)use($like){
				$Q->where('code','like',$like)->orWhere('name','like',$like)
					->orWhereHas('Details',function($Q)use($like){
						$Q->where('address1','like',$like)->orWhere('address2','like',$like)->orWhere('phone','like',$like);
					})->orWhereHas('Logins',function($Q)use($like){
						$Q->where('email','like',$like);
					});
			})->get();
	}
	
	public function get_print_names(Request $request){
		return [$request->f => CPO::withoutGlobalScopes()->where(['customer' => $request->c, 'reg_seq' => $request->s, 'function_code' => $request->f])->pluck('print_name')->unique()->values()->toArray()];
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	private function NewPOFunctionDetails($request){
		if($request->function_name == "0" || is_null($request->function_code)) return false;
		if($request->function_name == "-1") return (is_null($request->new_function_name) || is_null($request->function_code))?false:[$request->new_function_name, $request->function_code];
		return [$request->function_name, $request->function_code];
	}
	
	private function NewPrintName($request){
		if($request->print_name == "" || is_null($request->print_name)) return null;
		if($request->print_name == "-1") return (is_null($request->new_print_name) || $request->new_print_name == "") ? null : $request->new_print_name;
		return $request->print_name;
	}
	
	private function AddPrintObject($Customer, $Product, $Function, $Code, $File, $Print = null, $Preview = null){
		$this->CPO = new CPO();
		$po_seq = $this->GetNextPOSeq($Customer, $Product);
		$fn_seq = $this->GetNextFNSeq($Customer, $Product, $Function, $Code);
		$pn_seq = $this->GetNextPNSeq($Customer, $Product, $Function, $Code, $Print);
		return $this->StorePO($Customer, $Product, $po_seq, $Code, $Function, $fn_seq, $Print, $pn_seq, $File, $Preview, $this->getAuthUser()->partner, time());
	}
	
	private function GetNextPOSeq($Customer, $Product){
		$Data = $this->CPO->where(['customer' => $Customer, 'reg_seq' => $Product])->latest('po_seq')->first();
		$POSeq =  ($Data) ? $Data->po_seq : 0;
		return $POSeq + 1;
	}
	
	private function GetNextFNSeq($Customer, $Product, $Function, $Code){
		$Data = $this->CPO->where(['customer' => $Customer, 'reg_seq' => $Product, 'function_name' => $Function, 'function_code' => $Code])->latest('function_seq')->first();
		$FNSeq =  ($Data) ? $Data->function_seq : 0;
		return $FNSeq + 1;
	}
	
	private function GetNextPNSeq($Customer, $Product, $Function, $Code, $Print){
		$Data = $this->CPO->where(['customer' => $Customer, 'reg_seq' => $Product, 'function_name' => $Function, 'function_code' => $Code, 'print_name' => $Print])->latest('print_seq')->first();
		$PNSeq =  ($Data) ? $Data->print_seq : 0;
		return $PNSeq + 1;
	}
	
	private function StorePO($customer, $reg_seq, $po_seq, $function_code, $function_name, $function_seq, $print_name, $print_seq, $file, $preview, $user, $time){
		$code = null;
		return CPO::create(compact('code', 'customer', 'reg_seq', 'po_seq', 'function_code', 'function_name', 'function_seq', 'print_name', 'print_seq', 'file', 'preview', 'user', 'time'));
	}
	
	private function GetPOFStorePath($C, $P){
		return implode("/",[$this->print_object_store_path,$C,$P]);
	}
	
	private function GetPOPStorePath($C, $P){
		return implode("/",[$this->print_object_preview_store_path,$C,$P]);
	}
	
	private function getAuthUser(){
		return (Auth()->user())?:(Auth()->guard("api")->user());
	}
	
	private function ActivatePrintObject($po){
		$po = CPO::withoutGlobalScope('active')->find($po); if(is_null($po)) return;
		CPO::withoutGlobalScope('active')->where(['customer' => $po->customer, 'reg_seq' => $po->reg_seq, 'function_code' => $po->function_code, 'print_name' => $po->print_name])->where('code','<>',$po->code)->update(['status' => 'INACTIVE']);
		return CPO::withoutGlobalScope('active')->find($po->code)->update(['status' => 'ACTIVE']);
	}
	
	private function HandlePOFile($POFile, $Customer, $Regreq){
		if($POFile->extension()){ $file = $POFile->store($this->GetPOFStorePath($Customer,$Regreq)); }
		else { $file = $POFile->storeAs($this->GetPOFStorePath($Customer,$Regreq),$POFile->hashName().'rpt'); }
		return $file;
	}
	
	private function HandlePOPFile($POPFile, $Customer, $Regreq){
		return $POPFile->store($this->GetPOPStorePath($Customer, $Regreq),$this->print_object_disk);
	}
	
	private function DeletePreviewFile($file){
		\Storage::disk($this->print_object_disk)->delete($file);
	}
	
	public function CallMethod($Method, $Args = []){
		return call_user_func_array([$this, $Method], $Args);
	}
	
	
}