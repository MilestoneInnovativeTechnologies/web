<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketCategory as TC;
use Illuminate\Http\Request;

class TicketCategoryController extends Controller
{
	
	private $filter_methods = ['status' => 'FilterTicketStatus', 'from' => 'FilterTicketCreatedFrom', 'to' => 'FilterTicketCreatedTo',
														 'category' => 'FilterTicketCategory', 'spec' => 'FilterTicketCategorySpec',
														 'product'	=>	'FilterTicketProduct', 'edition'	=>	'FilterTicketEdition', 
														 'customer'	=>	'FilterTicketCustomer', 
														];
	
	public function category_list(Request $request){
		$tkt = Ticket::query();
		foreach($this->filter_methods as $req => $method)
			if($request->$req) $tkt = $this->$method($tkt,$request->$req);
		$Tickets = $tkt->get();
		return view('tkt.category_tickets',compact('Tickets'));
		
	}
	
	
	
	
	
	
	
	
	private function FilterTicketStatus($orm, $status){
		$status = is_array($status) ? $status : [$status];
		return $orm->whereHas('Cstatus',function($Q)use($status){ $Q->whereIn('status',$status); });
	}
	
	private function FilterTicketCreatedFrom($orm, $from){
		return $orm->where(function($Q)use($from){ $Q->where('created_at','>=',date('Y-m-d H:i:s',$from)); });
	}
	
	private function FilterTicketCreatedTo($orm, $to){
		return $orm->where(function($Q)use($to){ $Q->where('created_at','<=',date('Y-m-d H:i:s',$to)); });
	}
	
	private function FilterTicketCategory($orm, $category){
		$category = (is_null($category) || in_array(strtolower($category),['other','others','null'])) ? null : $category;
		return $orm->where(function($Q)use($category){ if($category) $Q->where('category',$category); else $Q->whereNull('category'); });
	}
	
	private function FilterTicketSpecVal($orm, $vals){
		$vals = is_array($vals) ? $vals : [$vals];
		return $orm->whereHas('category_specs_values',function($Q)use($vals){ foreach($vals as $val) $Q->where('value',$val); });
	}
	
	private function FilterTicketCategorySpec($orm, $spec){
		return $orm->whereHas('category_specs_values',function($Q)use($spec){ $Q->where('specification',$spec); });
	}
	
	private function FilterTicketProduct($orm, $product){
		return $orm->where(function($Q)use($product){ $Q->where('product',$product); });
	}
	
	private function FilterTicketEdition($orm, $edition){
		return $orm->where(function($Q)use($edition){ $Q->where('edition',$edition); });
	}
	
	private function FilterTicketCustomer($orm, $customer){
		return $orm->where(function($Q)use($customer){ $Q->where('customer',$customer); });
	}
	
	
	
	
}
