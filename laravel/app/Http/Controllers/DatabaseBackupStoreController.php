<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DatabaseBackupStoreController extends Controller
{
    public function Index(Request $request)
    {
        return view("dbStore.DBBackupStore");
    }
    public function Store(Request $request){

        if($request->hasFile('file')) {
            foreach($request->file as $file)
            {
                $FileName = $file->getClientOriginalName();
               $request->file->storeAs('public/Upload', $FileName);
               // $json=Storage::get($File,'contents');
              // $array=json_decode($json);
               // dd($array);

            }


return "yes";


        }
    }
    public function addMorePost(Request $request)
    {
        $rules = [];


        foreach ($request->input('name') as $key => $value) {
            $rules["name.{$key}"] = 'required';
        }
        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {
            foreach ($request->input('name') as $key => $value) {
                TagList::create(['name' => $value]);
            }
            return response()->json(['success' => 'done']);
        }
        return response()->json(['error' => $validator->errors()->all()]);
    }
}
