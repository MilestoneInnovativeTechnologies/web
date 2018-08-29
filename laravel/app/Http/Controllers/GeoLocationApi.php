<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;

class GeoLocationApi extends Controller
{
    public function AllCountries(){
			$Result = $this->ToValArray(DB::table("countries")->select("id","name","currency","phonecode")->get());
			return response($Result[0])->withHeaders([
				"_RSP_COUNT"	=>	count($Result[0]),
				"_RSP_KEYS"	=>	json_encode($Result[1])
			]);
		}
		
    public function Countries(){
			$user = Auth::guard("api")->user();
			$partner = $user->partner()->first();
			if(in_array("company",array_column($user->roles()->get()->toArray(),"name"))){
				$Collection = DB::table("countries")->select("id","name","phonecode","currency")->get();
			} else {
				$Collection = $partner->countries()->get()->unique("id")->toArray();
			}
			$Result = $this->keep($Collection,["id","name","currency","phonecode"]);
			$Result = $this->ToValArray($Result);
			return response($Result[0])->withHeaders([
				"_RSP_COUNT"	=>	count($Result[0]),
				"_RSP_KEYS"	=>	json_encode($Result[1])
			]);
		}
		
		public function States($Country = null){
			$Result = $this->ToValArray(DB::table("states")->select("id","name")->whereCountry($Country)->get());
			return response($Result[0])->withHeaders([
				"_RSP_COUNT"	=>	count($Result[0]),
				"_RSP_KEYS"	=>	json_encode($Result[1])
			]);
		}
		
		public function Cities($State = null){
			$Result = $this->ToValArray(DB::table("cities")->select("id","name")->whereState($State)->get());
			return response($Result[0])->withHeaders([
				"_RSP_COUNT"	=>	count($Result[0]),
				"_RSP_KEYS"	=>	json_encode($Result[1])
			]);
		}
	
		static function ToValArray($Obj){
			if(!is_array($Obj)) $Obj = $Obj->toArray();
			if(empty($Obj)) return [];
			$Names = array_keys( (array) $Obj[0]);
			return [array_map(function($Item) use($Names){
				$MyArray = [];
				foreach($Names as $name) $MyArray[] = is_array($Item) ? ($Item[$name]) : ($Item->$name);
				return $MyArray;
			},$Obj),$Names];
		}
	
		static function keep($Obj,$KArray){
			foreach($Obj as $K => $Obj2)
				foreach($Obj2 as $F => $V)
					if(!in_array($F,$KArray)) unset($Obj[$K][$F]);// $RetArray[$K][$F] = $V;
			return $Obj;
		}
}
