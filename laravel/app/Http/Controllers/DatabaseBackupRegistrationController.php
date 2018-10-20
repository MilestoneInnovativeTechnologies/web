<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DatabaseBackupRegistrationController extends Controller
{

    public function Index(Request $request)
    {
        return view("dbStore.DBBackupRegistration");
    }

    /**
     * @param  Request $request
     * @return  Response
     *
     */
    public function Validation(Request $request)
    {
      $this->validate($request,[
         'DBUserName'=>'required|min:3',

          ]);



    }

}
