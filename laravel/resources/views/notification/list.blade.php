@extends("notification.page")
@include('BladeFunctions')
@section("content")
@php
$Data = \App\Models\Notification::viewable()->get();
@endphp

<div class="content">
	<div class="row">
	@forelse($Data as $N)
		<div class="col col-xs-4" style="padding-right: 0px"><div class="panel panel-default"><div class="panel-heading">{{ $N->title }}<span class="pull-right">{{ date('d/M/y',strtotime($N->date)) }}</span></div><div class="panel-body" style="height: 90px">{!! mb_substr($N->description_short,0,168) !!}</div><div class="panel-body">{!! $N->serve_button() !!}</div></div></div>
	@empty
	<div class="jumbotron text-center"><strong>No Notifications</strong></div>
	@endforelse
	</div>
</div>

@endsection