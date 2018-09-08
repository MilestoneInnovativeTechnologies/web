<?php

namespace App\Http\Controllers;

use Faker\Provider\File;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;



class SmsJsonController extends Controller
{


   public function Index(Request $request)
    {
      return view("Test.FileUpload");
    }

    public function Store(Request $request){


          // $file=$request->only ('file');
           //dd($file);
        if($request->hasFile('file')) {
            $FileName = $request->file->getClientOriginalName();
            $request->file->storeAs('public/Upload', $FileName);

            $json= $request->file( 'public/Upload',  'outstanding.json');

          // $SMS=json_decode($File,true);
           // dd($SMS);
            //$file=fopen($File,'r');
           // dd($File);
           // while(!feof($file)){
              //  $line=fgets($file);
             //   $obj=json_encode($line);
               // echo $obj->sid;     echo"<hr>";
            //}
            $json = '["apple","orange","banana","strawberry"]';
            $a=json_decode($request->file( 'public/Upload',  'outstanding.json'));
            echo $a[0];
        }


    }

    //public function ReadFile(Request $request)
    //{

  //  }

}
