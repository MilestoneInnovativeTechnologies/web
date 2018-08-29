@extends("company.page")
@section("content")

<div class="row">
	<div class="col col-md-6 col-sm-6 col-xs-12 col-lg-6">
		@component('layouts.comps.BSDBPanel',['name' => 'registration_requests', 'class' => 'registration_requests', 'type' => ['bordered','condensed'], 'title' => 'Registration Requests', 'js' => 'js/registration_requests.js', 'data' => 'api/v1/capi/get/00grr' , 'heads' => ['No','Name/Distributor','Product','Actions'], 'limits' => [5,10,25]]) @endcomponent
		@component('layouts.comps.BSDBPanel',['name' => 'recent_registrations', 'class' => 'recent_registrations', 'type' => ['bordered','condensed'], 'title' => 'Recently Registered Customers', 'js' => 'js/recent_registrations.js', 'data' => 'api/v1/capi/get/00rrc' , 'heads' => ['No','Name/Distributor','Product','Registered On','Added On'], 'limits' => [5,10,25]]) @endcomponent
		
	</div><div class="col col-md-6 col-sm-6 col-xs-12 col-lg-6" style="padding-left: 0px;">
		<div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Search</div></div><div class="panel-body">
			<div class="table-responsive"><table class="table table-striped"><tbody>
				<tr><th style="padding-top:14px;">Partner</th><th style="padding-top: 14px">:</th><td><input type="text" name="search_partner" class="form-control" placeholder="search with name, email, phone"></td><td><a href="" name="btn_search_partner" target="_blank" class="btn btn-default">Navigate</a></td></tr>
				<tr><th style="padding-top:14px;">Ticket</th><th style="padding-top: 14px">:</th><td><input type="text" name="search_ticket" class="form-control" placeholder="search for tickets"></td><td><a href="" name="btn_search_ticket" target="_blank" class="btn btn-default">Navigate</a></td></tr>
			</tbody></table></div>
		</div></div>
		@component('layouts.comps.BSDBPanel',['name' => 'amc_requests', 'class' => 'amc_requests', 'type' => ['bordered','condensed'], 'title' => 'AMC Requests', 'heads' => ['No','Name/Distributor','Product','Actions'], 'limits' => [5,10,25]]) @endcomponent
		@component('layouts.comps.BSDBPanel',['name' => 'unregistered_recent_customers', 'class' => 'unregistered_recent_customers', 'type' => ['bordered','condensed'], 'title' => 'Recent Customers - Unregistered', 'js' => 'js/unregistered_recent_customers.js', 'data' => 'api/v1/capi/get/gurrc' , 'heads' => ['No','Name/Distributor','Product','Added On'], 'limits' => [5,10,25]]) @endcomponent
		@component('layouts.comps.BSDBPanel',['name' => 'demo_expiring_customers', 'class' => 'demo_expiring_customers', 'type' => ['bordered','condensed'], 'title' => 'Expiring Customers', 'js' => 'js/demo_expiring_customers.js', 'data' => 'api/v1/capi/get/00dec' , 'heads' => ['No','Name/Distributor','Product','Expire date'], 'limits' => [5,10,25]]) @endcomponent
	</div>
</div>
<div class="row">
	<div class="col col-md-12 col-lg-12 col-sm-12 col-xs-12">@component('layouts.comps.BSDBPanel',['name' => 'product_registration_summary', 'class' => 'product_registration_summary', 'type' => ['bordered'], 'title' => 'Registration Summary - Product', 'js' => 'js/product_registration_summary.js?_0.0967554', 'data' => 'api/v1/capi/get/00prs' , 'heads' => ['Product','Edition','Registered','Unregistered','Total'], 'data_filter' => 'prs_period_options']) @endcomponent</div>
</div>
<div class="row">
	<div class="col col-md-7 col-lg-7 col-sm-12 col-xs-12">@component('layouts.comps.BSDBPanel',['name' => 'tickets_summary', 'class' => 'tickets_summary', 'type' => 'bordered', 'title' => 'Tickets Summary', 'js' => 'js/tickets_summary.js', 'data' => 'api/v1/capi/get/000ts', 'heads' => ['Team','Total','Closed','New/Opened','Inprogress','Holded','Reopened'], 'data_filter' => 'ts_period_options']) @endcomponent</div>
	<div class="col col-md-5 col-lg-5 col-sm-12 col-xs-12" style="padding-left: 0px">@component('layouts.comps.BSDBPanel',['name' => 'current_active_tickets', 'class' => 'current_active_tickets', 'type' => 'bordered', 'title' => 'Current Status of Tickets', 'js' => 'js/current_active_tickets.js', 'data' => 'api/v1/capi/get/00cat', 'heads' => ['Team','New','Opened','In Progress','Holded'], 'limits' => [5,10,25]]) @endcomponent</div>
</div>


@endsection
@push('js')
<script type="text/javascript">
var _TKTNavLink = "{{ Route('ticket.panel','--code--') }}";
var _PartnerNavLink = {!! json_encode(collect(["mit.customer.panel","mit.dealer.panel","mit.distributor.panel","mit.supportagent.panel","mit.supportteam.panel"])->mapWithKeys(function($item){ return [mb_substr($item,4,-6) => Route($item,['--code--'])]; })); !!};
</script>
<script type="text/javascript" src="js/mit_dashboard_search.js"></script>
@endpush