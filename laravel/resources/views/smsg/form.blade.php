@extends("smsg.page")
@include('BladeFunctions')
@section("content")
@php
$code = Request()->code;
$Data = ($code) ? \App\Models\SMSGateway::find($code) : null;
@endphp


<div class="content">
	<div class="col col-md-12"><form method="post">{{ csrf_field() }}
		<div class="panel panel-default">
			<div class="panel-heading"><strong>@if(isset($Data)) Update @else Create New @endif SMS Gateway</strong>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('smsg.index'):url()->previous()) !!}</div>
			<div class="panel-body">
				<div class="clearfix">
					<div class="col-xs-6">
						{!! formGroup(2,'code','text','Code',old('code',($Data)?$Data->code:''),['labelClass' => 'taleft']) !!}
						{!! formGroup(2,'name','text','Name',old('name',($Data)?$Data->name:''),['labelClass' => 'taleft']) !!}
						{!! formGroup(2,'description','textarea','Description',old('description',($Data)?$Data->description:''),['labelClass' => 'taleft']) !!}
					</div>
				</div>
				<div class="clearfix">&nbsp;<br>&nbsp;</div>
				<div class="clearfix">
					<div class="col-xs-4">
						{!! formGroup(2,'class','text','Class Name',old('class',($Data)?$Data->class:''),['labelWidth' => 4]) !!}
					</div>
					<div class="col-xs-8">
						{!! formGroup(2,'url','text','URL',old('url',($Data)?$Data->url:''),['labelWidth' => 2, 'labelClass' => 'taleft']) !!}
					</div>
				</div>
				<div class="clearfix">
				@for($i=1; 10>$i; $i++)
				<div class="col-xs-4">
					{!! formGroup(2,'arg'.$i,'text','Argument '.$i,old('arg'.$i,($Data)?$Data->{'arg'.$i}:''),['labelWidth' => 4]) !!}
				</div>
					@if($i%3 == 0 && $i != 9) </div><div class="clearfix"> @endif
				@endfor
				</div>
			</div>
			<div class="panel-footer clearfix">
				<input type="submit" name="submit" value="@if(isset($Data))Update @else Create @endif Gateway" class="btn btn-primary pull-right">
			</div>
		</div>
	</form></div>
</div>

@endsection
@push('css')
<style type="text/css">
	.taleft { text-align: left !important }
</style>
@endpush