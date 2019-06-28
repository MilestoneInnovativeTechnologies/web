<?php

namespace App\Http\Controllers;

use App\Models\CustomerRegistration;
use App\Models\SmartSale;
use App\Models\SmartSaleDevice;
use App\Models\SmartSaleTable;
use Illuminate\Http\Request;
use App\Http\Requests\SmartSaleFormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class SmartSaleController extends Controller
{
    static public $Tables = ['setup','fiscalyearmaster','functiondetails','functioninvdetails','userprofile','usermaster','userdetails','accountdetails','analysismaster','areamaster','areaaccount','invstoremaster','branchstore','branchmaster','companymaster','taxruleheader','taxruledetails','itemgroup','itemgroupmaster','itemmaster','itemunit','pricelist','pricelistheader','hdata','idata','pihdata','piidata','billdata','chequedetails','ddata'];
    static public $Table_Fields = ['sync_to_ttl','sync_from_ttl','last_created','last_updated'];
    static public $TTL_UP = 60;
    static public $TTL_DOWN = 60;
    static public $Fields = ['code','customer','seq','name','brief','date_start','date_end','url_web','url_api','url_interact'];
    static public $Storage = 'ssi';
    static public $Table_TTL = ['setup' => [0,0], 'fiscalyearmaster' => [0,0], 'functiondetails' => [600,0], 'functioninvdetails' => [600,0], 'userprofile' => [600,0], 'usermaster' => [600,0], 'userdetails' => [600,0], 'accountdetails' => [600,0], 'analysismaster' => [600,0], 'areamaster' => [300,0], 'areaaccount' => [300,0], 'invstoremaster' => [300,0], 'branchstore' => [300,0], 'branchmaster' => [0,0], 'companymaster' => [0,0], 'taxruleheader' => [0,0], 'taxruledetails' => [0,0], 'itemgroup' => [600,0], 'itemgroupmaster' => [600,0], 'itemmaster' => [600,0], 'itemunit' => [300,0], 'pricelist' => [300,0], 'pricelistheader' => [600,0], 'hdata' => [15,15], 'idata' => [15,15], 'pihdata' => [15,15], 'piidata' => [15,15], 'billdata' => [15,15], 'chequedetails' => [15,15], 'ddata' => [15,15]];
    private $device_args = ['name','uuid','imei','serial','code1','code2','code3'];

    public function store(SmartSaleFormRequest $request){
        $request->merge(['code' => null]);
        $ss = $request->store($request->only(self::$Fields));
        if($request->hasFile('image')) { $ss->image = $request->file('image')->store('',self::$Storage); $ss->save(); }
        $this->addTables($ss,$request);
        return back()->with(['info' => true, 'type' => 'success', 'text' => 'Data submitted successfully']);
    }

    public function update($id,SmartSaleFormRequest $request){
        $ss = SmartSale::find($id); $ss->update($request->only(self::$Fields));
        if($request->hasFile('image')) { $ss->image = $request->file('image')->store('',self::$Storage); $ss->save(); }
        $this->addTables($ss,$request);
        return back()->with(['info' => true, 'type' => 'success', 'text' => 'Data submitted successfully']);
    }

    public function addTables($ss,$request){
        $ss->Tables()->delete();
        foreach(self::$Tables as $Table){
            $sst = new SmartSaleTable();
            $sst->table = $Table;
            foreach(self::$Table_Fields as $Field){
                $sst->$Field = $request->$Table[$Field];
            }
            $ss->Tables()->save($sst);
        }
    }

    public function config($id){
        $content = SmartSale::find($id)->load('Tables');
        $CR = CustomerRegistration::where('customer',$content->customer)->where('seqno',$content->seq)->first()->load(['Product','Edition']);
        $name = implode("",['SS',$content->id]);
        $ss = $content->toArray();
        $ss['product'] = $CR->Product->name; $ss['edition'] = $CR->Edition->name; $ss['application'] = implode(" ",[$ss['product'],$ss['edition'],'Edition']);
        $ss['database'] = $CR->database; $ss['computer'] = $CR->computer;
        Storage::put($name,json_encode($ss));
        return response()->download(storage_path("app/{$name}"),"{$name}.json")->deleteFileAfterSend(true);
    }

    public function apiTableInfo(Request $request){
        return Arr::get(SmartSale::where($request->only(['customer','seq','id','code']))->with('Tables')->first(),'Tables',collect([]))->map(function($item){
            return Arr::only($item->toArray(),['id','table','sync_to_ttl','sync_from_ttl','last_created','last_updated']);
        });
    }

    public function apiSSGetForDevice(Request $request){
        $args = array_filter($request->only($this->device_args)); if(empty($args)) return [];
        return Arr::get(SmartSaleDevice::where($args)->with('SmartSale')->first(),'SmartSale');
    }

    public function apiTableSet($id,Request $request){
        $SST = SmartSaleTable::find($id);
        if($request->has('update')) $SST->last_updated = date('Y-m-d H:i:s',strtotime($request->get('update')));
        if($request->has('create')) $SST->last_created = date('Y-m-d H:i:s',strtotime($request->get('create')));
        $SST->save(); return $SST;
    }

    public function device(SmartSale $id, Request $request){
        $args = $request->only($this->device_args);
        $ssd = new SmartSaleDevice();
        foreach ($args as $key => $val) $ssd->$key = $val;
        $id->Devices()->save($ssd);
        return back()->with(['info' => true, 'type' => 'success', 'text' => 'Device Added Successfully']);
    }

    public function delete(SmartSaleDevice $id){
        $id->delete();
        return back()->with(['info' => true, 'type' => 'warning', 'text' => 'Device Removed Successfully']);
    }
}
