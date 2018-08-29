<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
	
	protected $Model;
	
	public $disk = 'local';
	public $path = 'customlog/Notifications';
	
	public function __construct(){
		$Apply = ['store','audience','update','serve','report'];
		$this->middleware(function($request, $next){
			$segs = $request->segments(); switch (count($segs)){ case 2: list($prefix,$action) = $segs; break; case 3: list($prefix,$code,$action) = $segs; break; default: list($prefix) = $segs; break; }
			$this->Model = $Model = (isset($code)) ? \App\Models\Notification::find($code) : new \App\Models\Notification;
			return (in_array($action,$Model->available_actions)) ? $next($request) : redirect()->back()->with(['info'	=>	true, 'type'	=>	'warning', 'text'	=>	'The requested action is not available right now.']);
		})->only($Apply);
	}
	
	
	
	public function store(Request $request){
		$val_rules = $this->Model->_validation();
		$validator = \Validator::make($request->all(),$val_rules['rules'],$val_rules['messages']);
		if($validator->fails()) return redirect()->back()->withErrors($validator)->withInput();
		$create_array = $request->only(['code','title','description','description_short','date','notify_from','notify_to']);
		if(!$create_array['notify_from']) $create_array['notify_from'] = $create_array['date'];
		if(!$create_array['notify_to']) $create_array['notify_to'] = $create_array['notify_from'];
		$create_array['created_by'] = $request->user()->partner;
		$N = $this->Model->create($create_array);
		return redirect()->route('notification.index')->with(['info'	=>	true, 'type'	=>	'success', 'text'	=>	'New notification added successfully. Please select the audience.' . '<a href="'.Route("notification.audience",$N->code).'" class="btn btn-link">Select Audience</a>!']);
	}
	
	public function audience(Request $request){
		$this->Model->update($request->only('target','target_type'));
		$this->Model->audience()->sync($request->r);
		return redirect()->back()->with(['info'	=>	true, 'type'	=>	'success', 'text'	=>	'Audience list updated successfully..']);
	}
	
	public function update(Request $request){
		$val_rules = $this->Model->_validation();
		if($this->Model->code == $request->code) unset($val_rules['rules']['code']);
		elseif(is_null($request->code)) $request->merge(['code' => $this->Model->NewCode()]);
		$validator = \Validator::make($request->all(),$val_rules['rules'],$val_rules['messages']);
		if($validator->fails()) return redirect()->back()->withErrors($validator)->withInput();
		$update_array = $request->only(['code','title','description','description_short','date','notify_from','notify_to']);
		if(!$update_array['notify_from']) $update_array['notify_from'] = $update_array['date'];
		if(!$update_array['notify_to']) $update_array['notify_to'] = $update_array['notify_from'];
		$this->Model->update($update_array);
		return redirect()->route('notification.edit',$this->Model->code)->with(['info'	=>	true, 'type'	=>	'success', 'text'	=>	'Notification updated successfully.']);
	}
	
	public function serve(){
		$AuthUser = $this->Model->_GETAUTHUSER();
		if(!$AuthUser) return redirect()->back()->with(['info' => true, 'type' => 'warning', 'text' => 'The page is not available for you.']);
		$this->AddNotificationRead($this->Model->code, $this->Model->title, $AuthUser->Partner->name);
		return view('notification.preview',['Data' => $this->Model]);
	}
	
	public function compose(\Illuminate\View\View $view){
		$view->with('Data', \App\Models\Notification::all());
	}
	
	public function report(){
		return view('notification.report',['Data' => $this->GetNotificationData($this->NotificationFile($this->Model->code),$this->Model->title), 'Model' => $this->Model]);
	}

	public function AddNotificationRead($Code, $Title, $Partner){
		$file = $this->NotificationFile($Code);
		if(!\Storage::disk($this->disk)->exists($file)) $this->CreateNotificationLog($file, $Title);
		$this->AddRead($file, $Partner);
	}
	
	private function NotificationFile($code){
		return $this->path . '/' . $code . '.json';
	}
	
	private function CreateNotificationLog($file, $title){
		$Data = ['Title' => $title, 'Read' => []];
		$this->SetNotificationData($file,$Data);
	}
	
	private function AddRead($file, $partner){
		$Data = $this->GetNotificationData($file);
		$Data['Read'][] = ['name' => $partner, 'time' => time()];
		$this->SetNotificationData($file, $Data);
	}
	
	private function GetNotificationData($file, $title = null){
		if(!\Storage::disk($this->disk)->exists($file)) $this->CreateNotificationLog($file, $title);
		return json_decode(\Storage::disk($this->disk)->get($file),true);
	}
	
	private function SetNotificationData($file, $data){
		\Storage::disk($this->disk)->put($file,json_encode($data));
	}
	
	
}
