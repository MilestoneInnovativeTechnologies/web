<?php

namespace App\Http\Controllers;

use App\Models\PDTable;
use App\Http\Requests\NewPD;

class PDController extends Controller
{
    public function store(NewPD $request){
        $model = $request->store();
        $this->set_tables($model,$request);
        return redirect()->route('pd.index')->with(['info' => true, 'type' => 'success', 'text' => 'Product Demonstration details added successfully']);
    }

    public function update(NewPD $request){
        $model = $request->update($request->id);
        $model->Tables()->forceDelete();
        $this->set_tables($model,$request);
        return back()->with(['info' => true, 'type' => 'success', 'text' => 'Product Demonstration details updated successfully']);
    }

    private function set_tables($model,$request){
        $tbArray = ['table','last_created','last_updated'];
        foreach($request->table as $name => $value){
            $pdtbl = new PDTable();
            foreach ($tbArray as $field) $pdtbl->$field = $request->$field[$name];
            $model->Tables()->save($pdtbl);
        }
    }

}
