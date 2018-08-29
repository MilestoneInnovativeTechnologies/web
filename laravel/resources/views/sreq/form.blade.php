@extends("sreq.page")
@include('BladeFunctions')
@section("content")
@php
$update = [];
$fields = ['supportteam','message'];
foreach($fields as $field){
	$update[$field] = isset($sr) ? $sr->$field : null;
}
@endphp

<div class="content">
	<div class="col col-md-8 col-md-offset-2"><form method="post">{{ csrf_field() }}
		<div class="panel panel-default">
			<div class="panel-heading"><strong>@if(isset($sr)) Update @else Create New @endif Service Request</strong>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('sreq.index'):url()->previous()) !!}</div>
			<div class="panel-body">
				{!! formGroup(2,'supportteam','select','Select Support Team',old('supportteam',$update['supportteam']),['selectOptions' => \App\Models\SupportTeam::pluck('name','code')->toArray()]) !!}
				{!! formGroup(2,'message','textarea','Message',old('message',$update['message']),['style' => 'height:120px']) !!}
			</div>
			<div class="panel-footer clearfix">
				<input type="submit" name="submit" value="@if(isset($sr)) Update @else Create @endif" class="btn btn-primary pull-right">
			</div>
		</div>
	</form></div>
</div>

@endsection