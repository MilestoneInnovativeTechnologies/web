<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DistributorBrandingController extends Controller
{
	
	private $branding_icon_path = 'icons', $storage_disk = 'branding';
	
	public function form(){
		return view('db.form');
	}
	
	public function submit(Request $request){
		$this->validate($request, [
        'domain' => 'required|unique:distributor_brandings,domain',
        'product' => 'required|min:1',
        'edition' => 'required|min:1',
    ]);
		//if(!$request->domain) return redirect()->back()->with(['info' => true, 'type' => 'danger', 'text' => 'Domain should not be empty'])->withInput();
		//if(!$request->product || !$request->edition) return redirect()->back()->with(['info' => true, 'type' => 'danger', 'text' => 'Should select atleast one product and edition.'])->withInput();
		$BrandCreateArray = $this->RequestToBrandingCreateArray($request); $Brand = $this->CreateBranding($BrandCreateArray);
		$BrandProduct = $this->RequestToBrandProducts($request); $Brand->Products()->createMany($BrandProduct);
		$BrandMain = $this->RequestToBrandMain($request); $Brand->Main()->create($BrandMain);
		$this->AddBrandLinks($Brand, $request);
		return redirect()->back()->with(['info' => true, 'type' => 'success', 'text' => 'Branding details added successfully.']);
	}
	
	public function index(){
		return view('db.index');
	}
	
	public function view($brand){
		$Data = \App\Models\DistributorBranding::with('Branding')->whereId($brand)->first();
		return view('db.view',compact('Data'));
	}
	
	public function delete($brand){
		$Data = \App\Models\DistributorBranding::with('Branding')->whereId($brand)->first();
		return view('db.delete',compact('Data'));
	}
	
	public function destroy($brand){
		$DB = \App\Models\DistributorBranding::find($brand);
		$B = \App\Models\Branding::whereId($DB->branding)->with('Main')->first();
		if($B->Main->count() > 1) $DB->delete();
		else $B->whereId($DB->branding)->delete();
		return redirect()->route('db.index')->with(['info' => true, 'type' => 'success', 'text' => 'Branding details deleted successfully.']);
	}
	
	public function add_domain($brand){
		$Data = \App\Models\DistributorBranding::with('Branding')->whereId($brand)->first();
		return view('db.add_domain',compact('Data'));
	}
	
	public function domain_add($brand,Request $request){
		$this->validate($request, [
        'domain' => 'required|unique:distributor_brandings,domain',
    ]);
		$DB = \App\Models\DistributorBranding::find($brand);
		$DB::create(['distributor' => $DB->distributor, 'domain' => $request->domain, 'branding' => $DB->branding]);
		return redirect()->route('db.index')->with(['info' => true, 'type' => 'success', 'text' => 'Domain added successfully.']);
	}
	
	public function edit($brand){
		$Data = \App\Models\DistributorBranding::with('Branding')->whereId($brand)->first();
		return view('db.edit',compact('Data'));
	}
	
	public function update($brand, Request $request){
		$Data = \App\Models\DistributorBranding::with('Branding')->whereId($brand)->first();
		if($Data->domain != $request->domain) $this->validate($request, [
        'domain' => 'required|unique:distributor_brandings,domain',
        'product' => 'required|min:1',
        'edition' => 'required|min:1',
    ]); else $this->validate($request, [
        'product' => 'required|min:1',
        'edition' => 'required|min:1',
    ]);
		$this->UpdateDB($Data,$request->only('domain','type'));
		$this->UpdateBrandings($Data->Branding,$request->only('name','heading','caption','about','address','email','number','current_icon','icon','cs'));
		$Data->Branding->Products()->delete(); $Data->Branding->Products()->createMany($this->RequestToBrandProducts($request));
		$Data->Branding->Links()->delete(); $this->AddBrandLinks($Data->Branding, $request);
		return redirect()->back()->with(['info' => true, 'type' => 'success', 'text' => 'Updated Successfully.']);			 
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	private function RequestToBrandingCreateArray($request){
		$CreateArray = $request->only('name','heading','caption','about','address','email','number');
		$CreateArray['color_scheme'] = $this->GetColorSchemeText($request->cs);
		$CreateArray['icon'] = $this->UploadAndGetPath($request->icon);
		return $CreateArray;
	}
	
	private function CreateBranding($Array){
		return \App\Models\Branding::create($Array);
	}
	
	private function GetColorSchemeText($rgb){
		return implode(',',[$rgb['r']?:0,$rgb['g']?:0,$rgb['b']?:0]);
	}
	
	private function UploadAndGetPath($file){
		if(is_null($file)) return null;
		$Path = $this->branding_icon_path; $Disk = $this->storage_disk;
		if($file->extension()) return $file->store($Path,$Disk);
		$ext = mb_strrchr($file->getClientOriginalName(),'.');
		$filename = $file->hashName(); if(mb_substr($filename,-1) == ".") $filename = mb_substr($filename,0,-1);
		return $file->storeAs($Path,$filename.$ext,$Disk);
	}
	
	private function RequestToBrandProducts($request){
		$Array = []; $Product = $request->product; $Edition = $request->edition; 
		foreach($Product as $key => $prd) array_push($Array,['product' => $prd, 'edition' => $Edition[$key]]);
		return $Array;
	}
	
	private function RequestToBrandMain($request){
		return ['domain' => $request->domain, 'distributor' => $request->distributor, 'type' => $request->type];
	}
	
	private function AddBrandLinks($Brand, $request){
		$CreateArray = $this->RequestToBrandLinkArray($request); if(!$CreateArray) return $Brand;
		$Brand->Links()->createMany($CreateArray);
		return $Brand;
	}
	
	private function RequestToBrandLinkArray($request){
		$Array = [];
		$Links = $request->link; $Names = $request->lname; $FAs = $request->fa; $Targets = $request->target;
		if(is_null($Links) && is_null($Names) && is_null($FAs)) return null;
		foreach($Links as $index => $Link){
			if(is_null($Names[$index]) && is_null($FAs[$index])) continue;
			array_push($Array,['link' => $Link, 'name' => $Names[$index], 'fa' => $FAs[$index], 'target' => $Targets[$index]]);
		}
		return (empty($Array)) ? null : $Array;
	}
	
	private function UpdateDB($DB,$Data){
		return $this->UpdateItemWithData($DB,$Data);
	}
	
	private function UpdateBrandings($Brnd, $Data){
		if(!is_null($Data['icon'])) { $Data['icon'] = $this->UploadAndGetPath($Data['icon']); unset($Data['current_icon']); }
		elseif($Data['current_icon'] == 'unchanged') { unset($Data['current_icon'],$Data['icon']); }
		else { $Data['icon'] = null; \Storage::disk($this->storage_disk)->delete($Brnd->icon); unset($Data['current_icon']); }
		
		$color_scheme = $this->GetColorSchemeText($Data['cs']);
		if($Brnd->color_scheme != $color_scheme) $Data['color_scheme'] = $color_scheme;
		unset($Data['cs']);
		
		return $this->UpdateItemWithData($Brnd,$Data);
	}
	
	private function UpdateItemWithData($Item,$Data){
		foreach($Data as $Field => $Value) $Item->$Field = $Value;
		$Item->save();
		return $Item;
	}
	
}
