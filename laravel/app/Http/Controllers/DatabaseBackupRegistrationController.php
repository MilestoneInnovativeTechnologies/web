<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DatabaseBackupRegistrationController extends Controller
{

    public function Index(Request $request)
    {
        return view("dbStore.DBBackupRegistration");
    }

    /**
     * @param  \Illuminate\Http\Request $request
     * @return  \Illuminate\Http\Response
     *
     */
    public function Store(Request $request)
    {
      $this->validate($request,[
         'DBUserName'=>'required|min:3',
          ]);



    }
    //
}
