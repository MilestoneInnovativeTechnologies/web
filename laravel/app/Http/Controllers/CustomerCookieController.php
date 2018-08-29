<?php

namespace App\Http\Controllers;

use App\Models\CustomerCookie;
use Illuminate\Http\Request;

class CustomerCookieController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
			return view('tscc.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
			$Code = Request()->code;
			$Customer = \App\Models\Customer::whereCode($Code)->with('Cookies')->get();
			$Customer = $Customer->isNotEmpty()?$Customer->first():false;
			return view('tscc.form',compact('Customer'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\App\Models\CustomerCookie  $customerCookie
     * @return \Illuminate\Http\Response
     */
    public function show(CustomerCookie $customerCookie)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\App\Models\CustomerCookie  $customerCookie
     * @return \Illuminate\Http\Response
     */
    public function edit(CustomerCookie $customerCookie)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\App\Models\CustomerCookie  $customerCookie
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CustomerCookie $customerCookie)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\App\Models\CustomerCookie  $customerCookie
     * @return \Illuminate\Http\Response
     */
    public function destroy(CustomerCookie $customerCookie)
    {
        //
    }
	
	public function add_cookie(Request $request){
		$Name = $request->name; $Value = $request->value; $Customer = $request->customer;
		if(!$this->isCustomerAllowed($Customer)) return 'Customer not assigned';
		return $this->AddCookie($Customer,$Name,$Value,$this->getAuthUser()->partner);
		
	}
	
	public function remove_cookie(Request $request){
		$ID = $request->id; $CC = CustomerCookie::whereId($ID)->get();
		if($CC->isEmpty() || !$this->isCustomerAllowed($CC->first()->customer)) return 'Customer not assigned';
		return ($this->RemoveCookie($ID))?["id"=>$ID]:0;
	}
	
	private function isCustomerAllowed($Customer){
		return \App\Models\Customer::whereCode($Customer)->get()->isNotEmpty();
	}
	
	private function getAuthUser(){
		return (Auth()->user())?:(Auth()->guard("api")->user());
	}
	
	static function AddCookie($Customer,$Name,$Value,$CreatedBy){
		return CustomerCookie::create(['customer'	=>	$Customer, 'name'	=>	$Name, 'value'	=>	$Value, 'created_by'	=>	$CreatedBy]);
	}
	
	static function RemoveCookie($ID){
		return CustomerCookie::destroy($ID);
	}
	
}
