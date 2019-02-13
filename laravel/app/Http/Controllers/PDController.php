<?php

namespace App\Http\Controllers;

use App\Models\CustomerRegistration;
use App\Models\PD;
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

    public function interact($code){
        $CR = $this->getCustomerReg($code); if(is_null($CR)) return 0;
        $PD = $this->getCustomerPD($CR['customer'],$CR['seqno']); if(is_null($PD)) return 0;
        return array_merge($CR,$PD);
    }

    private function getCustomerReg($code){
        list($key,$val) = KeyCodeController::Decode($code);
        $where = array_combine($key,$val);
        $cr = CustomerRegistration::with(['Product','Edition'])->where($where)->first();
        return $cr ? $this->getCRArgs($cr) : null;
    }
    private function getCRArgs($cr){
        return collect($cr)->only(['seqno','customer'])->merge(['product' => implode(" ",[$cr->Product->name,$cr->Edition->name,'Edition'])])->toArray();
    }
    private function getCustomerPD($customer,$seq){
        $pd = PD::with(['Customer','Tables'])->where(compact('customer','seq'))->first();
        return $pd ? $this->getPDArgs($pd) : null;
    }
    private function getPDArgs($pd){
        return collect($pd)->only(['url_interact','code','id'])->merge(['customer' => $pd->Customer->name,'tables' => $pd->Tables->map(function($table){ return collect($table)->only(['table','last_created','last_updated']); })])->toArray();
    }

    public function intupd($code){
        $data = $this->getIntUpdateArray($code);
        $pd = PD::with('Tables')->find($data['id']); $md = ['updated','created'];
        foreach($pd->Tables as $tbl){
            foreach($md as $mode){
                $key = join("_",[$tbl->table,$mode]); $fld = join("_",['last',$mode]);
                $tbl->$fld = array_key_exists($key,$data) ? $data[$key] : $tbl->$fld;
            }
        }
        $pd->push();
    }
    private function getIntUpdateArray($code){
        list($key,$val) = KeyCodeController::Decode($code);
        return array_combine($key,$val);
    }

}
