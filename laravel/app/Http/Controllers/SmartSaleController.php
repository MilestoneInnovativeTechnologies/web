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
    static public $Tables = ['setup','fiscalyearmaster','functiondetails','functioninvdetails','function','userprofile','usermaster','userdetails','accountdetails','analysismaster','billsummary','areamaster','areaaccount','invstoremaster','branchstore','branchmaster','companymaster','companydetails','itemgroupmaster','itemmaster','itemunit','pricelistheader','pricelist','taxlist','pihdata','piidata','hdata','idata','ddata','importtransactions'];
    static public $Table_Fields = ['type','delay','condition','sync','record'];
    static public $DELAY = 60;
    static public $Fields = ['code','customer','seq','name','brief','print_head_line1','print_head_line2','footer_text','date_start','date_end','url_web','url_api','url_interact'];
    static public $Storage = 'ssi';
    static public $Table_DELAY = ['setup' => ['up',0], 'fiscalyearmaster' => ['up',86400], 'functiondetails' => ['up',0], 'functioninvdetails' => ['up',0], 'function' => ['up',259200], 'userprofile' => ['up',64800], 'usermaster' => ['up',64800], 'userdetails' => ['up',64800], 'accountdetails' => ['down',300], 'analysismaster' => ['up',0], 'billsummary' => ['up',300], 'areamaster' => ['up',64800], 'areaaccount' => ['up',64800], 'invstoremaster' => ['up',86400], 'branchstore' => ['up',1296000], 'branchmaster' => ['up',2592000], 'companymaster' => ['up',2592000], 'companydetails' => ['up',2592000], 'itemgroupmaster' => ['up',172800], 'itemmaster' => ['up',86400], 'itemunit' => ['up',259200], 'pricelistheader' => ['up',259200], 'pricelist' => ['up',86400], 'taxlist' => ['up',86400], 'pihdata' => ['both',15], 'piidata' => ['both',15], 'hdata' => ['both',15], 'idata' => ['both',15], 'ddata' => ['up',15], 'importtransactions' => ['down',15]];
    static public $Table_CONDITION = [
        'functiondetails' => '["BR1","BR2","CR1","MT1","MT2","MT3","MT4","SL1","SL2","SL3","SL4","SL5","SO1","SO2","SR1","SR2","SR3"]',
        'functioninvdetails' => '["BR1","BR2","CR1","MT1","MT2","MT3","MT4","SL1","SL2","SL3","SL4","SL5","SO1","SO2","SR1","SR2","SR3"]',
        'function' => '["BR1","BR2","CR1","MT1","MT2","MT3","MT4","SL1","SL2","SL3","SL4","SL5","SO1","SO2","SR1","SR2","SR3"]',
        'analysismaster' => '{"CATCODE": "SE","ISGROUP": "N"}',
        'hdata' => '{"FNCODE":["BR1","BR2","CR1","MT1","MT2","MT3","MT4","SL1","SL2","SL3","SL4","SL5","SO1","SO2","SR1","SR2","SR3"]}',
        'idata' => '{"FNCODE":["BR1","BR2","CR1","MT1","MT2","MT3","MT4","SL1","SL2","SL3","SL4","SL5","SO1","SO2","SR1","SR2","SR3"]}',
        'ddata' => '[{"FNCODE":["BR1","BR2","CR1"]},{"TYPE":"System","operand":"<>"}]',
        ];
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
            return Arr::except($item->toArray(),['smart_sale','created_at','updated_at']);
        });
    }

    public function apiSSGetForDevice(Request $request){
        $args = array_filter($request->only($this->device_args)); if(empty($args)) return [];
        return Arr::get(SmartSaleDevice::where($args)->with('SmartSale')->first(),'SmartSale');
    }

    public function apiTableSet($id,Request $request){
        $SST = SmartSaleTable::find($id);
        if($request->has('record')) $SST->record = date('Y-m-d H:i:s',strtotime($request->get('record')));
        if($request->has('sync')) $SST->sync = date('Y-m-d H:i:s',strtotime($request->get('sync')));
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
