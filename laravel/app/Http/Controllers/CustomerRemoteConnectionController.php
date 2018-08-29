<?php

namespace App\Http\Controllers;

use App\Models\CustomerRemoteConnection;
use Illuminate\Http\Request;

class CustomerRemoteConnectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
			return view('crc.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
			$Code = Request()->code;
			$Customer = \App\Models\Customer::whereCode($Code)->with('Connections')->get();
			$Customer = $Customer->isNotEmpty()?$Customer->first():false;
			return view('crc.form',compact('Customer'));
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
     * @param  \App\Models\App\Models\CustomerRemoteConnection  $customerRemoteConnection
     * @return \Illuminate\Http\Response
     */
    public function show(CustomerRemoteConnection $customerRemoteConnection)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\App\Models\CustomerRemoteConnection  $customerRemoteConnection
     * @return \Illuminate\Http\Response
     */
    public function edit(CustomerRemoteConnection $customerRemoteConnection)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\App\Models\CustomerRemoteConnection  $customerRemoteConnection
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CustomerRemoteConnection $customerRemoteConnection)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\App\Models\CustomerRemoteConnection  $customerRemoteConnection
     * @return \Illuminate\Http\Response
     */
    public function destroy(CustomerRemoteConnection $customerRemoteConnection)
    {
        //
    }
	
	public function add_connection(Request $request){
		$customer = $request->customer; $app = $request->app; $login = $request->login; $secret = $request->secret; $remarks = $request->remarks; 
		if(!$this->isCustomerAllowed($customer)) return 'Customer not assigned';
		return $this->AddConnection($customer,$app,$login,$secret,$remarks,$this->getAuthUser()->partner);
	}
	
	public function remove_connection(Request $request){
		$ID = $request->id; $CC = CustomerRemoteConnection::whereId($ID)->get();
		if($CC->isEmpty() || !$this->isCustomerAllowed($CC->first()->customer)) return 'Customer not assigned';
		return ($this->RemoveConnection($ID))?["id"=>$ID]:0;
	}
	
	private function isCustomerAllowed($Customer){
		return \App\Models\Customer::whereCode($Customer)->get()->isNotEmpty();
	}
	
	private function getAuthUser(){
		return (Auth()->user())?:(Auth()->guard("api")->user());
	}
	
	static function AddConnection($customer,$app,$login,$secret,$remarks,$user){
		return CustomerRemoteConnection::create(['customer'	=>	$customer, 'appname'	=>	$app, 'login'	=>	$login, 'secret'	=>	$secret, 'remarks'	=>	$remarks, 'created_by'	=>	$user]);
	}
	
	static function RemoveConnection($ID){
		return CustomerRemoteConnection::destroy($ID);
	}

}
