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
}