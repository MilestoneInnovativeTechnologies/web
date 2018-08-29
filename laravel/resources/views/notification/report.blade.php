@extends("notification.page")
@include('BladeFunctions')
@section("content")
@php $report = collect($Data['Read']) @endphp

<div class="content">
	<div class="col-md-8 col-md-offset-2">
		<div class="panel panel-default">
			<div class="panel-heading"><span class="panel-title">{{ $Model->title }} - Report</span>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('notification.index'):url()->previous()) !!}</div>
			<div class="panel-body">
				<ol>
					@forelse($report as $Ary)
					<li><pre style="padding: 0px 0px 0px 15px; border: none; background-color: transparent;	">{{ $Ary['name'] }}			{{ date('D d/M/y h:i A', $Ary['time']) }}</pre></li>
					@empty
					<li><pre>No reads yet</pre></li>
					@endforelse
				</ol>
			</div>
		</div>
	</div>
</div>

@endsection