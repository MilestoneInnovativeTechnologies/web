@extends("distributor.page")
@include('BladeFunctions')
@section("content")

<div class="content distributor_show">

	<div class="panel panel-default main">
		<div class="panel-heading"><span class="panel-title">Distributor Tickets</span>{!! PanelHeadBackButton(url()->previous()) !!}</div>
		<div class="panel-body">
		@component('tkt.comp_tickets',compact('Tickets')) @endcomponent
		</div>
	</div>
</div>

@endsection