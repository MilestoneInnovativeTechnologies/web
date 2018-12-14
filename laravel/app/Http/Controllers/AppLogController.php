<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\AppInitController;
use Storage;

class AppLogController extends Controller
{
	
	public $AIC;
  
	public function unknownuser(){
		return view('log.unknownuser');
	}
	
	public function getunknownuserdata(Request $request){
		$Page = $request->page?:0;
		$this->AIC = $AIC = new AppInitController();
		$GFile = $AIC->getGuestFilePath($Page);
		return $this->getSegregatedData($GFile);
	}
	
	private function getSegregatedData($GFile){
		if(!Storage::exists($GFile)) Storage::put($GFile,"");
		$Path = storage_path('app/'.$GFile);
		$Array = array_reverse(array_map(function($Line){
			return explode("\t",$Line);
		},file($Path,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)));
		$Data = [];
		//$this->fillDataWithArray($Array,$Pattern);
		//$Pattern = [4=>[5=>[8,9,[6=>[3,[7=>[1=>2]]]]]]];
		foreach($Array as $Set){
			$cmp = $Set[4];
			if(!array_key_exists($cmp,$Data)) $Data[$cmp] = [];
			$brc = $Set[5];
			if(!array_key_exists($brc,$Data[$cmp])) $Data[$cmp][$brc] = [];
			if(empty($Data[$cmp][$brc])) $Data[$cmp][$brc] = [$Set[8],$Set[9],[]];
			$app = $Set[6];
			if(!array_key_exists($app,$Data[$cmp][$brc][2])) $Data[$cmp][$brc][2][$app] = [$Set[3],[]];
			$ver = $Set[7];
			if(!array_key_exists($ver,$Data[$cmp][$brc][2][$app][1])) $Data[$cmp][$brc][2][$app][1][$ver] = [];
			$date = $Set[1];
			if(!array_key_exists($date,$Data[$cmp][$brc][2][$app][1][$ver])) $Data[$cmp][$brc][2][$app][1][$ver][$date] = [];
			$Data[$cmp][$brc][2][$app][1][$ver][$date][] = $Set[2];
		}
		return $Data;
	}
	
	public function searchcustomer(Request $request){
		list($S,$B,$T) = array_values($request->only('S','B','T'));
		$Products = $this->getSimiliarProducts($S);
		$Editions = $this->getSimiliarEditions($S);
		$ORM = $this->getSearchORM($Products,$Editions);
		$WHArray = $this->getWhereHasArray($B,$T);
		if($WHArray) $Result = $ORM->whereHas($WHArray[0],function($Q) use($WHArray){ $Q->where($WHArray[1],'LIKE',$WHArray[2]); })->get();
		else $Result = $this->filterByDistributor($ORM,$T);
        $Result = $this->flatDealerToDistributor($Result);
        return $this->flatternResult($Result);
	}

	private function getSimiliarProducts($S){
		$P = \App\Models\Product::where('name','LIKE','%'.$S.'%')->orWhere('basename','LIKE','%'.$S.'%');
		$SParts = explode(" ",$S);
		foreach($SParts as $W) $P->orWhere('name','LIKE','%'.$W.'%')->orWhere('basename','LIKE','%'.$W.'%');
		return $P->pluck('code');
	}

	private function getSimiliarEditions($S){
		$P = \App\Models\Edition::where('name','LIKE','%'.$S.'%');
		$SParts = explode(" ",$S);
		foreach($SParts as $W) $P->orWhere('name','LIKE','%'.$W.'%');
		return $P->pluck('code');
	}
	
	private function getSearchORM($Products,$Editions){
		$SelectFields = ['customer','seqno','product','edition','product_id','serialno','registered_on'];
		return \App\Models\CustomerRegistration::select($SelectFields)->whereIn('product',$Products)->whereIn('edition',$Editions)
			->with(['Customer'	=>	function($Q){
				$Q->select('code','name')
					->with(['Details'	=>	function($Q){
						$SelectFields = ['partner','address1','address2','phonecode','phone','city','state'];
						$Q->select($SelectFields)
							->with(['City.State.Country'	=>	function($Q){ $Q->select('id','name'); }]);
					},'Logins'	=>	function($Q){
						$SelectFields = ['partner','email'];
						$Q->select($SelectFields);
					},'Parent1.ParentDetails'	=>	function($Q){
						$Q->select('code','name')
							->with(['Roles'	=>	function($Q){ $Q->select('code','name'); },'Parent.ParentDetails'	=>	function($Q){ $Q->select('code','name')->with(['Roles'	=>	function($Q){ $Q->select('code','name'); }]); }]);
					}]);
			},'Product'	=>	function($Q){
				$Q->select('code','name');
			},'Edition'	=>	function($Q){
				$Q->select('code','name');
			}])
			->has('Customer.Parent1')
            ;
	}
	
	private function getWhereHasArray($B,$T){
		$Like = ['%'.$T.'%'];
		$AHAry = ['company_name'	=>	['Customer','name'], 'email'	=>	['Customer.Logins','email'], 'phone'	=>	['Customer.Details','phone']];
		return (array_key_exists($B,$AHAry))?array_merge($AHAry[$B],$Like):false;
	}
	
	private function filterByDistributor($ORM,$Distributor){
		$Result = $ORM->get();
		if(trim($Distributor) == '') return $Result;
		return $Result->filter(function($Val, $Key) use($Distributor){
			$Roles = $Val->Customer->Parent->ParentDetails->Roles;
			$RolNames = $Roles->pluck('name')->toArray();
			if(in_array('distributor',$RolNames)){
				return (!stripos($Val->Customer->Parent->ParentDetails->name,$Distributor) === false);
			} elseif($Val->Customer->Parent->ParentDetails->Parent){
				$RolNames = $Val->Customer->Parent->ParentDetails->Parent->ParentDetails->Roles->pluck('name')->toArray();
				if(in_array('distributor',$RolNames)){
					return (!stripos($Val->Customer->Parent->ParentDetails->Parent->ParentDetails->name,$Distributor) === false);
				}
			} else {
				return false;
			}
		})->values();
	}
	
	private function flatDealerToDistributor($Result){
		$Result = $Result->toArray();
		foreach($Result as $K => $Array){
			$Roles = $Array['customer']['parent1']['parent_details']['roles'];
			$RolNames = array_column($Roles,'name');
			if(in_array('distributor',$RolNames)){
				$Result[$K]['customer']['distributor'] = $Result[$K]['customer']['parent1']['parent_details']['name'];
				unset($Result[$K]['customer']['parent']);
			} else {
				$Roles = $Array['customer']['parent1']['parent_details']['parent']['parent_details']['roles'];
				$RolNames = array_column($Roles,'name');
				if(in_array('distributor',$RolNames)){
					$Result[$K]['customer']['distributor'] = $Result[$K]['customer']['parent1']['parent_details']['parent']['parent_details']['name'];
					unset($Result[$K]['customer']['parent1']);
				}
			}
		}
		return $Result;
	}
	
	private function flatternResult($Result){
		$Data = [];
		foreach($Result as $K => $Array){
			$CA = array_merge($Array['customer'],$Array['customer']['details']);
			$Result[$K]['customer'] = $CA;
			$Result[$K]['customer']['email'] = implode(", ",array_column($Result[$K]['customer']['logins'],'email'));
			$Result[$K]['customer']['city'] = $Array['customer']['details']['city']['name'];
			$Result[$K]['customer']['state'] = $Array['customer']['details']['city']['state']['name'];
			$Result[$K]['customer']['country'] = $Array['customer']['details']['city']['state']['country']['name'];
			$Result[$K]['product'] = $Array['product']['name']; $Result[$K]['product_id'] = $Array['product']['code'];
			$Result[$K]['edition'] = $Array['edition']['name']; $Result[$K]['edition_id'] = $Array['edition']['code'];
			unset($Result[$K]['customer']['details'],$Result[$K]['customer']['logins'],$Result[$K]['customer']['code'],$Result[$K]['customer']['partner']);
			$Data[$Array['customer']['code']."-".$Array['seqno']] = $Result[$K];
		}
		return $Data;
	}
	
	public function mapped(Request $request){
		$this->AIC = $AIC = new AppInitController();
		return $AIC->getlogMapArray($request->page?:0);
	}
	
}
