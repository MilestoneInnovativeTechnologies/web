<?php

namespace App\Http\Controllers;

use App\Models\PackageVersion;
use Illuminate\Http\Request;

class PackageVersionController extends Controller
{

	public function index(){}

	public function create(){}

	public function store(Request $request){}

	public function show(PackageVersion $packageVersion){}

	public function edit(PackageVersion $packageVersion){}

	public function update(Request $request, PackageVersion $packageVersion){}

	public function destroy(PackageVersion $packageVersion){}
	
	static function get_latest($PRD, $EDN, $PKG){
		return PackageVersion::where(['product' =>	$PRD, 'edition' => $EDN, 'package' => $PKG, 'status' =>	'APPROVED'])->with('Product','Edition','Package')->latest('version_sequence')->first();
	}
	
}
