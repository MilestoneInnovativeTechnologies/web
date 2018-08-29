<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Storage;

class WebMailLogController extends Controller
{
	
	private $disk = 'local';
	private $path = 'customlog\WebMail';
	
	public function webmail($code, $subject = null){
		if(!$this->is_mail_exists($code)) $this->create_log_file($code, $subject);
		return $this->get_mail_data($code);
	}
	
	public function create_log_file($code,$subject = null){
		$file = $this->get_webmailfile_path($code);
		$data = $this->get_init_data($subject);
		$this->put_log_data($file,$data);
	}
	
	public function is_mail_exists($code){
		$file = $this->get_webmailfile_path($code);
		return $this->is_file_exists($file);
	}
	
	public function get_webmailfile_path($code){
		return implode("\\",[$this->path,$code]) . ".json";
	}
	
	public function is_file_exists($file){
		return Storage::disk($this->disk)->exists($file);
	}
	
	public function get_init_data($subject = null){
		$data = []; $data['subject'] = $subject;
		$data['sent'] = []; $data['receivers'] = [];
		$data['receipt'] = []; $data['sender'] = [];
		return $data;
	}
	
	public function put_log_data($file,$data){
		Storage::disk($this->disk)->put($file,json_encode($data));
	}
	
	public function get_log_data($file){
		return json_decode(Storage::disk($this->disk)->get($file),true);
	}
	
	public function get_mail_data($code){
		$file = $this->get_webmailfile_path($code);
		return $this->get_log_data($file);
	}
	
	public function new_sent($code,$receiver,$subject = null){
		$data = $this->webmail($code,$subject);
		$data = $this->add_sent($data,$receiver);
		$this->update_data($code,$data);
	}
	
	public function add_sent($data,$receiver){
		$time = time();
		if(!in_array($time,$data['sent'])) { $data['sent'][] = $time; $data['sender'][$time] = $this->auth_user()->Partner->name; }
		if(!array_key_exists($time,$data['receivers'])) $data['receivers'][$time] = [];
		$data['receivers'][$time][] = $receiver;
		return $data;
	}
	
	public function add_receipt($data,$receiver){
		$time = time();
		if(!array_key_exists($receiver,$data['receipt'])) $data['receipt'][$receiver] = [];
		$data['receipt'][$receiver][] = $time;
		return $data;
	}
	
	public function update_data($code, $data){
		$file = $this->get_webmailfile_path($code);
		$this->put_log_data($file,$data);
	}
	
	public function new_receipt($code, $receiver){
		$data = $this->webmail($code);
		$data = $this->add_receipt($data,$receiver);
		$this->update_data($code,$data);
	}
	
	private function auth_user(){
		return (Auth()->user())?:(Auth()->guard("api")->user()); 
	}
	
	
}
