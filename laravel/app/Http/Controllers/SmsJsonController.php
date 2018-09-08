<?php

namespace App\Http\Controllers;

use Faker\Provider\File;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;


class SmsJsonController extends Controller
{


   public function Index(Request $request)
    {
      return view("Test.FileUpload");
    }

    public function Store(Request $request){

        if($request->hasFile('file')) {
            $FileName = $request->file->getClientOriginalName();
           $File=$request->file->storeAs('public/Upload', $FileName);
             $json=Storage::get($File,'contents');
            $array=json_decode($json);
            dd($array);
            dd($array);
        }
    }

    public function ReadFile(Request $request)
    {
        $json =Storage::get('public/Upload/outstanding.json','contents');
        $array=json_decode($json);
        dd($array);
   }

}
