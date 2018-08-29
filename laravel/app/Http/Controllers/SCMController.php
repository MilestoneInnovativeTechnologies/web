<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SCMController extends Controller
{
	
  public function CustomerSearch(){
		return view('scm.index');
		return \App\Models\PartnerRole::Customer()->select(['id','login','partner','role','rolename'])->with(['Login'	=>	function($Q){
			$Q->select('id','email');
		},'Partner'	=>	function($Q){
			$Q->select('code','name')->with(['Details','Parent'	=>	function($Q){
				$Q->with(['ParentDetails'	=> function($Q){
					$Q->select('code','name')->with(['Parent.ParentDetails'	=>	function($Q){ $Q->select('code','name'); }]);
				}]);
			}]);
		}])->get();
		//	->whereHas('Login',function($Q){ $Q->where('email','like','%ish%'); })
		//	->orWhereHas('Partner',function($Q){ $Q->where('name','like','%she%'); })
		//	->paginate(1)/*->withPath('custom/url')->appends(['sort' => 'votes'])*/->links();
	}
	
	
	
}
