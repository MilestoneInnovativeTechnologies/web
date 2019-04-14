<?php

namespace App\Http\Controllers;

use App\Models\SmartSale;
use App\Models\SmartSaleTable;
use Illuminate\Http\Request;
use App\Http\Requests\SmartSaleFormRequest;
use Illuminate\Support\Facades\Storage;

class SmartSaleController extends Controller
{
    static public $Tables = ['setup','fiscalyearmaster','functiondetails','functioninvdetails','userprofile','usermaster','userdetails','accountdetails','analysismaster','areamaster','areaaccount','invstoremaster','branchstore','taxruleheader','taxruledetails','itemgroup','itemgroupmaster','itemmaster','itemunit','pricelist','pricelistheader','hdata','idata','pihdata','piidata','billdata','chequedetails','ddata'];
    static public $Table_Fields = ['sync_to_ttl','sync_from_ttl','last_created','last_updated'];
    static public $TTL_UP = 60;
    static public $TTL_DOWN = 60;
    static public $Fields = ['code','customer','seq','name','brief','date_start','date_end','url_web','url_api','url_interact'];
    static public $Storage = 'ssi';
    static public $Table_TTL = ['setup' => [0,0], 'fiscalyearmaster' => [0,0], 'functiondetails' => [600,0], 'functioninvdetails' => [600,0], 'userprofile' => [600,0], 'usermaster' => [600,0], 'userdetails' => [600,0], 'accountdetails' => [600,0], 'analysismaster' => [600,0], 'areamaster' => [300,0], 'areaaccount' => [300,0], 'invstoremaster' => [300,0], 'branchstore' => [300,0], 'taxruleheader' => [0,0], 'taxruledetails' => [0,0], 'itemgroup' => [600,0], 'itemgroupmaster' => [600,0], 'itemmaster' => [600,0], 'itemunit' => [300,0], 'pricelist' => [300,0], 'pricelistheader' => [600,0], 'hdata' => [15,15], 'idata' => [15,15], 'pihdata' => [15,15], 'piidata' => [15,15], 'billdata' => [15,15], 'chequedetails' => [15,15], 'ddata' => [15,15]];

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
        $name = implode("",['SS',$content->id]);
        Storage::put($name,json_encode($content));
        return response()->download(storage_path("app/{$name}"),"{$name}.json")->deleteFileAfterSend(true);
    }
}
