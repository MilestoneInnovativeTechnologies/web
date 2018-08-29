<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PublicPrintObjectController extends Controller
{

    protected $Model;

    public $max_download_size = 25;

    public function __construct(){
        $Apply = ['store','download','delete','update','preview','file'];
        $this->middleware(function($request, $next){
            $segs = $request->segments();  switch (count($segs)){ case 2: list($prefix,$action) = $segs; break; case 3: list($prefix,$code,$action) = $segs; break; case 4: list($prefix,$code,$action,$key) = $segs; break; default: list($prefix) = $segs; break; }
            $this->Model = $Model = (isset($code)) ? \App\Models\PublicPrintObject::find($code) : new \App\Models\PublicPrintObject;
            return (in_array($action,$Model->available_actions)) ? $next($request) : redirect()->back()->with(['info'	=>	true, 'type'	=>	'warning', 'text'	=>	'The requested action is not available right now.']);
        })->only($Apply);
    }

    public function store(Request $request){
        $val_rules = $this->Model->_validation();
        $validator = \Validator::make($request->all(),$val_rules['rules'],$val_rules['messages']);
        if($validator->fails()) return redirect()->back()->withErrors($validator)->withInput();
        $this->Model = call_user_func_array([$this->Model,'create_new'],$request->only('name','description','code'));
        $this->add_files($request); $this->add_specs($request);

        return redirect()->route('ppo.index')->with(['info' => true, 'type' => 'success', 'text' => 'New Print Object updated successfully.']);
    }

    private function add_files($request){
        if($request->hasFile('file')){ $this->Model->set_file($request->file('file')); }
        if($request->hasFile('preview')){ $this->Model->set_preview($request->file('preview')); }
    }
    private function add_specs($request){
        foreach(range(0,9) as $C){
            $spec = 'spec'.$C; $val = $request->get($spec);
            if($val) $this->Model->add_spec($spec,$val);
        }
    }

    public function download(){
        $this->Model->increment('downloads');
        $DownloadName = $this->Model->name . (strrchr($this->Model->file,".")?:".rpt");
        return response()->download(\Storage::disk($this->Model->storage_disk)->getDriver()->getAdapter()->applyPathPrefix($this->Model->file),$DownloadName);
    }

    public function delete(){
        $this->Model->update_data(['status' => 'INACTIVE']);
        return redirect()->route('ppo.index')->with(['info' => true, 'type' => 'success', 'text' => $this->Model->name . ' deleted successfully.']);
    }

    public function update(Request $request){
        $this->Model->update_data($request->only(['code','name','description']));
        $this->add_specs($request);
        return redirect()->route('ppo.index')->with(['info' => true, 'type' => 'success', 'text' => $this->Model->name . ' updated successfully.']);
    }

    public function preview(Request $request){
        if($request->hasFile('preview')){ $this->Model->set_preview($request->file('preview')); }
        return back()->with(['info' => true, 'type' => 'success', 'text' => 'Preview added successfully.']);
    }

    public function file(Request $request){
        if($request->hasFile('file')){ $this->Model->set_file($request->file('file')); }
        return back()->with(['info' => true, 'type' => 'success', 'text' => 'Object added successfully.']);
    }

}
