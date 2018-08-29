<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TicketCategoryMaster as TCM;

class TicketCategoryMasterController extends Controller
{
	
	protected $tcm;
	
	public function __construct(){
		$Apply = ['add','edit','delete','specs'];
		$this->middleware(function($request, $next){
			$segs = $request->segments(); switch (count($segs)){ case 2: list($prefix,$action) = $segs; break; case 3: list($prefix,$tcm,$action) = $segs; break; default: list($prefix) = $segs; break; }
			$this->tcm = $tcm = (isset($tcm)) ? TCM::withoutGlobalScopes()->find($tcm) : new TCM;
			return (in_array($action,$tcm->available_actions)) ? $next($request) : redirect()->back()->with(['info'	=>	true, 'type'	=>	'warning', 'text'	=>	'The requested action is not available right now.']);
		})->only($Apply);
	}
	
	public function add(Request $request){
		$ValidationRules = $this->tcm->validation_rules(); $Rules = $ValidationRules['rules']; $Messages = $ValidationRules['messages'];
		$Validation = \Validator::make($request->all(),$Rules,$Messages);
		if($Validation->fails()) return redirect()->back()->withErrors($Validation)->withInput();
		$this->tcm->add_new($request->code, $request->name, $request->priority, $request->available, $request->description);
		return redirect()->back()->with(['info'	=>	true, 'type'	=>	'success', 'text'	=>	'New category has been added successfully.']);
	}
	
	public function edit(Request $request){
		$tcm = $this->tcm; $ValidationRules = $tcm->validation_rules(); $Rules = $ValidationRules['rules']; $Messages = $ValidationRules['messages'];
		$Rules['code'] = ['nullable',\Illuminate\Validation\Rule::unique('ticket_category_masters')->ignore($tcm->code,'code')];
		$Validation = \Validator::make($request->all(),$Rules,$Messages);
		if($Validation->fails()) return redirect()->back()->withErrors($Validation)->withInput();
		if(!$request->code) $request->merge(['code' => $tcm->NewCode()]);
		$tcm->update($request->only('code','name','description','priority','available'));
		return redirect()->route('tcm.edit',$tcm->code)->with(['info'	=>	true, 'type'	=>	'success', 'text'	=>	'Category updated successfully.']);
	}
	
	public function delete(){
		$this->tcm->update(['status' => 'INACTIVE']);
		return redirect()->route('tcm.index')->with(['info'	=>	true, 'type'	=>	'warning', 'text'	=>	'Category deleted successfully.']);
	}
	
	public function specs(Request $request){
		$this->tcm->update(['specifications' => $this->tcm->get_spec($request->spec,'encode')]);
		return redirect()->route('tcm.index')->with(['info'	=>	true, 'type'	=>	'success', 'text'	=>	'Specfications updated successfully.']);
	}
	
	public function get_customer_categories(Request $request){
		$A = []; $tcm = new TCM;
		$A[] = $tcm->ALWAYS(); $A[] = $tcm->ifSupportTeam($this->getAuthUser());
		$C = call_user_func_array('array_merge',$A);
		$E = $this->GetCustomerExcludedCategories($request->cus);
		return array_diff_key($C,array_fill_keys($E,'-'));
	}
	
	public function get_reg_categories(Request $request){
		$A = []; $tcm = new TCM; $CR = \App\Models\CustomerRegistration::where(['customer' => $request->cus, 'seqno' => $request->seq])->first();
		$A[] = $tcm->isPresale($CR); $A[] = $tcm->onDemand($CR);
		$C = call_user_func_array('array_merge',$A);
		$E = $this->GetCustomerExcludedCategories($request->cus);
		return array_diff_key($C,array_fill_keys($E,'-'));
	}
	
	public function get_category_specs(Request $request){
		$Category = $request->cat; if(!$Category) return null;
		return TCM::withoutGlobalScope('own')->find($Category)->Specs;
	}
	
	public function force_get_category(Request $request){
		$Category = $request->cat; if(!$Category) return null;
		return TCM::withoutGlobalScopes()->whereCode($Category)->pluck('name','code')->toArray();
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function getAuthUser(){
		return (Auth()->user())?:(Auth()->guard("api")->user());
	}
	
	private function GetCustomerExcludedCategories($Customer){
		$CObj = \App\Models\Customer::find($Customer); if(!$CObj) return [];
		$distributor = \App\Models\Customer::find($Customer)->get_distributor()->code;
		return (new \App\Models\DistributorExcludeCategory)->get_categories($distributor);
	}
	
	
	
}