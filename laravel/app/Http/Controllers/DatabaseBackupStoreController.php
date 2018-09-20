<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

class DatabaseBackupStoreController extends Controller
{
    public function Index(Request $request)
    {
        return view("dbStore.DBBackupStore");
    }
    public function Store(Request $request){
        $this->validate($request,[
            'filename'=>'required',
            'filename.*'   =>'mimes:zip,rar'
        ]);

        if($request->hasFile('filename')) {
            foreach($request->file('filename') as $file)
            {
                //$FileName = $file->getClientOriginalName();
               // $file->storeAs('public/Upload', $FileName);
                Storage::putFile(storage_path('public/Upload'),new File('file'));

            }
        }
    }

}
