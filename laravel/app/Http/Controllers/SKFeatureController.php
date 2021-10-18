<?php

namespace App\Http\Controllers;

use App\Http\Requests\SK\FeatureFormRequest;
use App\Models\SK\Edition;
use App\Models\SK\EditionFeature;
use App\Models\SK\Feature;
use Illuminate\Http\Request;

class SKFeatureController extends Controller
{
    static $db_fields = ['code','name','detail','type','default','parent','level','status'];
    public function add(FeatureFormRequest $request){
        if(!$request->has('code')) $request->merge(['code' => Feature::CODE()]);
        $Feature = $request->store($request->only(self::$db_fields));
        return redirect()->route('sk.feature.editions',['feature' => $Feature->id]);
    }

    public function code(){
        return Feature::CODE();
    }

    public function update(Feature $feature, Request $request){
        if($request->submit === 'Update Feature Details') {
            if ($feature->code !== $request->code && in_array($request->code, Feature::pluck('code')->toArray())) return back()->withInput()->withErrors(['code' => 'Provided code is not unique!!']);
            $feature->update($request->only(self::$db_fields));
        }
        if($request->submit === 'Update Edition Values') {
            self::SyncEditionFeature($feature,'Editions',$request->feature);
        }
        return redirect()->route('sk.features');
    }

    public function add_edition(Request $request){
        if(!$request->has('name')) return back()->withErrors(['name' => 'Name is Mandatory!!']);
        $Edition = Edition::create($request->only(['name','detail']));
        return redirect()->route('sk.edition.features',['edition' => $Edition->id]);
    }

    public function edition_update(Edition $edition, Request $request){
        if($request->submit === 'Update Edition Details') $edition->update($request->only(['name','detail','status']));
        if($request->submit === 'Update Feature Values') self::SyncEditionFeature($edition,'Features',$request->feature);
        return redirect()->route('sk.editions');
    }

    private static function SyncEditionFeature($Model,$Relation,$Records){
        $defaults = Feature::pluck('default','id')->toArray(); $records = [];
        if($Relation === 'Features'){
            foreach($Records as $feature => $value){
                if($value !== $defaults[$feature]) $records[] = new EditionFeature(compact('feature','value'));
            }
        }
        if($Relation === 'Editions'){
            $default = $Model->default;
            foreach($Records as $edition => $value){
                if($value !== $default) $records[] = new EditionFeature(compact('edition','value'));
            }
        }
        $Model->$Relation()->delete();
        if(count($records)) $Model->$Relation()->saveMany($records);
    }
}
