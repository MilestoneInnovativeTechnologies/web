@extends("customer.page")
@include('BladeFunctions')
@section("content")

<div class="content distributor_show">

	<div class="panel panel-default main">
		<div class="panel-heading"><span class="panel-title">Customer Tickets</span>{!! PanelHeadBackButton(url()->previous()) !!}</div>
		<div class="panel-body">
		<div class="pagination">{{ $Tickets->links() }}</div>
		@component('tkt.comp_tickets',compact('Tickets')) @endcomponent
		</div>
	</div>
</div>

@endsection