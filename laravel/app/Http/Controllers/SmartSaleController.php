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
    static public $Tables = ['setup','fiscalyearmaster','userprofile','usermaster','userdetails','accountmaster','accountdetails','analysismaster','billsummary','areamaster','areaaccount','invstoremaster','branchstore','branchmaster','companymaster','companydetails','ssitemgroupmaster','ssitemmaster','ssitemunit','pricelistheader','sspricelist','shdata','sddata','setupshift','functiondetails','functioninvdetails','function','functionprint','taxlist','pihdata','piidata','hdata','idata','ddata','importtransactions','sscustomers'];
    static public $Table_Fields = ['type','delay','condition','sync','record'];
    static public $DELAY = 60;
    static public $Fields = ['code','customer','seq','name','brief','date_start','date_end','print_line1','print_line2','print_line3','url_web','url_api','url_interact'];
    static public $Storage = 'ssi';
    static public $Table_DELAY = ['setup' => ['up',0], 'fiscalyearmaster' => ['up',86400], 'functiondetails' => ['up',86400], 'functioninvdetails' => ['up',86400], 'function' => ['up',259200], 'functionprint' => ['up',259200], 'userprofile' => ['up',64800], 'usermaster' => ['up',64800], 'userdetails' => ['up',64800], 'accountmaster' => ['up',300], 'accountdetails' => ['up',300], 'analysismaster' => ['up',86400], 'billsummary' => ['up',300], 'areamaster' => ['up',64800], 'areaaccount' => ['up',64800], 'invstoremaster' => ['up',86400], 'branchstore' => ['up',1296000], 'branchmaster' => ['up',2592000], 'companymaster' => ['up',2592000], 'companydetails' => ['up',2592000], 'ssitemgroupmaster' => ['up',172800], 'ssitemmaster' => ['up',86400], 'ssitemunit' => ['up',259200], 'pricelistheader' => ['up',259200], 'sspricelist' => ['up',86400],'shdata' => ['both',60],'sddata' => ['both',30],'setupshift' => ['up',86400], 'taxlist' => ['up',86400], 'pihdata' => ['both',15], 'piidata' => ['both',15], 'hdata' => ['both',15], 'idata' => ['both',15], 'ddata' => ['up',15], 'importtransactions' => ['down',15], 'sscustomers' => ['down',60]];
    static public $Table_CONDITION = [
        'usermaster' => '{ "ISGROUP":"Y" }',
        'accountmaster' => '[{ "PCODE":"1202%","operand":"LIKE" },{ "LEVEL":"5" }]',
        'accountdetails' => '{ "CODE":"1202%","operand":"LIKE" }',
        'functiondetails' => '["BR1","BR2","CR1","MT1","MT2","MT3","MT4","SL1","SL2","SL3","SL4","SL5","SO1","SO2","SR1","SR2","SR3"]',
        'functioninvdetails' => '["BR1","BR2","CR1","MT1","MT2","MT3","MT4","SL1","SL2","SL3","SL4","SL5","SO1","SO2","SR1","SR2","SR3"]',
        'function' => '["BR1","BR2","CR1","MT1","MT2","MT3","MT4","SL1","SL2","SL3","SL4","SL5","SO1","SO2","SR1","SR2","SR3"]',
        'functionprint' => '{"CODE":["BR1","BR2","CR1","MT1","MT2","MT3","MT4","SL1","SL2","SL3","SL4","SL5","SO1","SO2","SR1","SR2","SR3"]}',
        'analysismaster' => '{"CATCODE": "SE","ISGROUP": "N"}',
        'hdata' => '{"FNCODE":["BR1","BR2","CR1","MT1","MT2","MT3","MT4","SL1","SL2","SL3","SL4","SL5","SO1","SO2","SR1","SR2","SR3"]}',
        'idata' => '{"FNCODE":["BR1","BR2","CR1","MT1","MT2","MT3","MT4","SL1","SL2","SL3","SL4","SL5","SO1","SO2","SR1","SR2","SR3"]}',
        'ddata' => '[{"FNCODE":["BR1","BR2","CR1"]},{"TYPE":"System","operand":"<>"}]',
        'shdata' => '[{"STATUS":"Completed","operand":"<>"}]',
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
        $args = array_filter($request->only($this->device_args));
        SmartSaleDevice::where(Arr::except($args,'name'))->delete();
        $ssd = new SmartSaleDevice();
        foreach ($args as $key => $val) $ssd->$key = $val;
        $id->Devices()->save($ssd);
        return back()->with(['info' => true, 'type' => 'success', 'text' => 'Device Added Successfully']);
    }

    public function delete(SmartSaleDevice $id){
        $id->delete();
        return back()->with(['info' => true, 'type' => 'warning', 'text' => 'Device Removed Successfully']);
    }

    public function clear($id){
        SmartSaleTable::where('smart_sale',$id)->update(['sync' => null,'record' => null]);
        return back()->with(['info' => true, 'type' => 'warning', 'text' => 'Sync Times Cleared Successfully']);
    }
}
