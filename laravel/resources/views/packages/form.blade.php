@extends("packages.page")
@section("content")
<div class="text-right"><a href="package/upload/{{ $Product }}/{{ $Edition }}" class="btn btn-default"> Back </a></div><br>
<h2>Package: {{ $Details['product']['basename'] }}{{ $Details['edition']['name'] }}Edition{{ $Details['package']['base_name'] }}</h2>
<?php
	$D = is_null($Details) ? false : true;
	if($D) list($major_version,$minor_version,$build_version,$revision,$version_sequence) = [$Details['major_version'],$Details['minor_version'],$Details['build_version'],$Details['revision'],($Details['version_sequence']+1)];
	else list($major_version,$minor_version,$build_version,$revision,$version_sequence) = [0,0,1,0,1];
	$Weightage = ($major_version * 1000000000) + ($minor_version * 1000000) + ($build_version * 1000) + $revision
?>
<form method="post" enctype="multipart/form-data" onSubmit="return Validate()">
{{ csrf_field() }}
<div class="row">
	<div class="col-sm-12 col-md-6 upload_div">
		<div class="jumbotron">
			<label for="upload">Upload File</label>
			<input type="file" name="package" class="form-control package_upload" accept=".exe">
			<h3>OR</h3>
			<label for="over_ftp"><input type="checkbox" name="upload_over_ftp" id="over_ftp" value="YES"> Upload over FTP</label>
		</div>
		
	</div>
	<div class="col-sm-12 col-md-6 form_div">
		<div class="form-group">
			<label for="version_sequence">Sequence No: {{ ($D)?($Details['version_sequence']+1):'0' }}</label>
		</div>
		<div class="form-group row">
			<div class="col-xs-3">
				<label for="major_version">Major Version</label>
				<input class="form-control" type="number" id="major_version" name="major_version" placeholder="" value="{{ $major_version }}">
			</div>
			<div class="col-xs-3">
				<label for="minor_version">Minor Version</label>
				<input class="form-control" type="number" id="minor_version" name="minor_version" placeholder="" value="{{ $minor_version }}">
			</div>
			<div class="col-xs-3">
				<label for="build_version">Build Version</label>
				<input class="form-control" type="number" id="build_version" name="build_version" placeholder="" value="{{ $build_version }}">
			</div>
			<div class="col-xs-3">
				<label for="revision">Revision</label>
				<input class="form-control" type="number" id="revision" name="revision" placeholder="" value="{{ $revision }}">
			</div>
		</div>
		<div class="form-group">
			<label for="version_string">Version String</label>
			<input class="form-control" type="text" id="version_string" name="version_string" data-alpha="{{ ($D)?(($Details['product']['basename']).($Details['edition']['name']).('Edition').($Details['package']['base_name'])):'' }}" value="">
		</div>
		<div class="form-group row">
			<div class="col-xs-6">
				<label for="build_date">Build Date</label>
				<div class="input-group">
					<input class="form-control datepicker" type="text" id="build_date" name="build_date" value="{{ date("d-m-Y") }}" data-date-end-date="0d">
					<span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
				</div>
			</div>
			<div class="col-xs-6">
				<label for="deploy_date">Deploy Date</label>
				<div class="input-group">
					<input class="form-control datepicker" type="text" id="deploy_date" name="deploy_date" value="{{ date("d-m-Y") }}" disabled>
					<span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
				</div>
			</div>
		</div>
		<div class="form-group">
			<label for="change_log">Change Logs</label>
			<textarea class="form-control" id="change_log" name="change_log"></textarea>
		</div>
	</div>
	<div class="form-group pull-right"><br>
		<input type="submit" name="submit" value="Upload" disabled class="btn btn-primary btn-lg" style="padding-right: 75px; padding-left: 75px">
	</div>
</div>
</form>
@endsection
@push("css")
<link rel="stylesheet" type="text/css" href="css/datepicker.css">
@endpush
@push("js")
<script type="text/javascript" src="js/datepicker.js"></script>
<script type="text/javascript">
	$(function(){
		$(".datepicker").datepicker({format:'dd-mm-yyyy',autoclose:true,defaultViewDate:'today'})
		window['weightage'] = {{ $Weightage }};
	})
</script>
@endpush
