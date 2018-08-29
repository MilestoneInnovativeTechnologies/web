@extends("pra.page")
@include('BladeFunctions')
@section("content")
@php
$Data = (Request()->code) ? \App\Models\ThirdPartyApplication::find(Request()->code) : null;
eval(str_replace('enum','$_PublicSelectOptions = array',DB::select(DB::raw('SHOW COLUMNS FROM third_party_applications WHERE Field = "public"'))[0]->Type.';'));
@endphp

<div class="content"><form method="post" enctype="multipart/form-data">{{ csrf_field() }}
	<div class="col-md-8 col-md-offset-2">
		<div class="panel panel-default">
			<div class="panel-heading"><strong class="panel-title">Third Party Application OR Milestone Tools</strong>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('tpa.index'):url()->previous()) !!}</div>
			<div class="panel-body">
				<div class="clearfix">
					<div class="col-xs-12">{!! formGroup(2,'code','text','Code',old('code',($Data)?$Data->code:(new \App\Models\ThirdPartyApplication)->NewCode()),['labelStyle' => 'text-align:left']) !!}</div>
				</div>
				<div class="clearfix">
					<div class="col-xs-12">{!! formGroup(2,'name','text','Name',old('name',($Data)?$Data->name:''),['labelStyle' => 'text-align:left']) !!}</div>
				</div>
				<div class="clearfix">
					<div class="col-xs-12" style="padding: 0px 30px">{!! formGroup(1,'description','textarea','Description',old('description',($Data)?$Data->description:''),['labelStyle' => 'text-align:left']) !!}</div>
				</div>
				<div class="clearfix">
					<div class="col-xs-12">{!! formGroup(2,'version','text','Version',old('version',($Data)?$Data->version:''),['labelStyle' => 'text-align:left']) !!}</div>
				</div>
				<div class="clearfix">
					<div class="col-xs-12">{!! formGroup(2,'public','select','Public',old('public',($Data)?$Data->public:''),['selectOptions' => $_PublicSelectOptions, 'labelStyle' => 'text-align:left']) !!}</div>
				</div>
				<div class="clearfix">
					<div class="col-xs-12">{!! formGroup(2,'vendor_url','text','Homepage',old('vendor_url',($Data)?$Data->vendor_url:''),['labelStyle' => 'text-align:left']) !!}</div>
				</div>@unless($Data)
				<div class="clearfix">
					<div class="col-xs-12">{!! formGroup(2,'file','file','File',['labelStyle' => 'text-align:left']) !!}</div>
				</div>@endunless
			</div>
			<div class="panel-footer clearfix">
				<input type="submit" name="submit" value="Submit" class="btn btn-primary pull-right">
			</div>
		</div>
	</div>
</form></div>

@endsection