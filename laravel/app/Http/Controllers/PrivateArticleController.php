<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PrivateArticleController extends Controller
{
	
	
	protected $Model;
	
	public $disk = 'local';
	public $path = 'customlog/PrivateArticlesRead';

	public function __construct(){
		$Apply = ['store','update','serve','audience','report'];
		$this->middleware(function($request, $next){
			$segs = $request->segments(); switch (count($segs)){ case 2: list($prefix,$action) = $segs; break; case 3: list($prefix,$code,$action) = $segs; break; default: list($prefix) = $segs; break; }
			$this->Model = $Model = (isset($code)) ? \App\Models\PrivateArticle::find($code) : new \App\Models\PrivateArticle;
			return (in_array($action,$Model->available_actions)) ? $next($request) : redirect()->back()->with(['info'	=>	true, 'type'	=>	'warning', 'text'	=>	'The requested action is not available right now.']);
		})->only($Apply);
	}
	
	public function store(Request $request){
		$val_rules = $this->Model->_validation();
		$validator = \Validator::make($request->all(),$val_rules['rules'],$val_rules['messages']);
		if($validator->fails()) return redirect()->back()->withErrors($validator)->withInput();
		call_user_func_array([$this->Model,'create_new'],$request->only('code','name','title','view'));
		return redirect()->route('pra.index')->with(['info' => true, 'type' => 'success', 'text' => 'Article created successfully.']);
	}
	
	public function update(Request $request){
		$val_rules = $this->Model->_validation();
		if($this->Model->code == $request->code) unset($val_rules['rules']['code']);
		$validator = \Validator::make($request->all(),$val_rules['rules'],$val_rules['messages']);
		if($validator->fails()) return redirect()->back()->withErrors($validator)->withInput();
		foreach($request->only('code','name','title','view') as $field => $req_value){
			if($this->Model->$field != $req_value)
				$this->Model->$field = $req_value;
		}
		$this->Model->save();
		return redirect()->route('pra.edit',$this->Model->code)->with(['info' => true, 'type' => 'success', 'text' => 'Article updated successfully.']);
	}
	
	public function serve($code){
		$AuthUser = $this->Model->_GETAUTHUSER();
		if(!$AuthUser) return redirect()->back()->with(['info' => true, 'type' => 'warning', 'text' => 'The page is not available for you.']);
		$this->AddArticleRead($this->Model->code, $this->Model->name, $AuthUser->Partner->name);
		return view('pra.articles.'.$this->Model->view,['Title' => $this->Model->title]);
	}
	
	public function audience(Request $request){
		$this->Model->update($request->only('target','target_type'));
		$this->Model->audience()->sync($request->r);
		return redirect()->back()->with(['info'	=>	true, 'type'	=>	'success', 'text'	=>	'Audience list updated successfully..']);
	}
	
	public function report(){
		return view('pra.report',['Data' => $this->GetArticleData($this->ArticleFile($this->Model->code),$this->Model->name), 'Model' => $this->Model]);
	}
	
	
	public function AddArticleRead($Code, $Title, $Partner){
		$file = $this->ArticleFile($Code);
		if(!\Storage::disk($this->disk)->exists($file)) $this->CreateArticleLog($file, $Title);
		$this->AddRead($file, $Partner);
	}
	
	private function ArticleFile($code){
		return $this->path . '/' . $code . '.json';
	}
	
	private function CreateArticleLog($file, $title){
		$Data = ['Title' => $title, 'Read' => []];
		$this->SetArticleData($file,$Data);
	}
	
	private function AddRead($file, $partner){
		$Data = $this->GetArticleData($file);
		$Data['Read'][] = ['name' => $partner, 'time' => time()];
		$this->SetArticleData($file, $Data);
	}
	
	private function GetArticleData($file, $title = null){
		if(!\Storage::disk($this->disk)->exists($file)) $this->CreateArticleLog($file, $title);
		return json_decode(\Storage::disk($this->disk)->get($file),true);
	}
	
	private function SetArticleData($file, $data){
		\Storage::disk($this->disk)->put($file,json_encode($data));
	}


}
