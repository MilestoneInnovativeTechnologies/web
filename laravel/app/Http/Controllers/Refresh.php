<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Refresh extends Controller
{

		public function index(){
			return view('refresh.index');
		}
	
		public function post(Request $request){
			$Structure = file_get_contents($request->file('structure'));
			//$Function = file_get_contents($request->file('function'));
			$Trigger = file_get_contents($request->file('trigger'));
			$Data = file_get_contents($request->file('data'));
			
			$this->structureDB($Structure);
			//$this->functionDB($Function);
			$this->triggerDB($Trigger);
			$this->dataDB($Data);
			$PartnerCode = 'COMPANY';
			$Login = $this->addCompany($request,$PartnerCode);
			
			$Key = (new \App\Http\Controllers\KeyCodeController())->KeyEncode(['id','partner','email'],[$Login,$PartnerCode,$request->email]);
			return view('refresh.final',compact('Key'));
			
		}
	
		private function addCompany($request,$PartnerCode){
			$P = \App\Models\Partner::create(['code'=>$PartnerCode, 'name'=>$request->name]);;
			$P->Details()->create(['code'=>$PartnerCode
														 ,'address1'=>$request->address1
														 ,'address2'=>$request->address2
														 ,'phonecode'=>$request->phonecode
														 ,'currency'=>$request->currency
														 ,'city'=>$request->city
														 ,'state'=>$request->state
														 ,'phone'=>$request->phone
														 ,'website'=>$request->website
														]);
			$P->Countries()->attach($request->country);
			$login = $P->Logins()->create(['email'=>$request->email])->id;
			$P->Roles()->attach('COMPANY',['login'	=>	$login, 'rolename'	=>	'COMPANY']);
			return $login;
		}
	
		private function structureDB($Sql){
			\DB::unprepared($Sql);
		}
	
		private function functionDB($Sql){
			\DB::unprepared($Sql);
		}
	
		private function triggerDB($Sql){
			\DB::unprepared($Sql);
		}
	
		private function dataDB($Sql){
			\DB::unprepared($Sql);
		}
}
