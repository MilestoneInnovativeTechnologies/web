@extends("tsa.page")
@include('BladeFunctions')
@section("content")
@php
$Descs = [
	'view'	=>	'View primary details of a ticket.',
	'edit'	=>	'Agent can create a ticket. This privilage allows the agent to edit ticket title or description if the ticket is created by the same agent.',
	'delete'	=>	'If the ticket is created by the same agent, then the agent can delete the ticket before the tasks get created.',
	'entitle'	=>	'This gives agent the privilage to correct the Category, Set Priority and to mension ticket type',
	'tasks'	=>	'This gives agent the privilage to manage tasks on a newly created ticket.',
	'closure'	=>	'Closure activities include submitting the solution provided, mentioning any referred tickets if any and also attaching any support document to a ticket on a closed ticket.',
	'reopen'	=>	'A closed ticket can be reopened.',
	'enquire'	=>	'This gives the agent to inquire into details with customer, when the customer reopened a closed ticket. After this enquiry, agent can decide whether to recreate a new ticker on same issue.',
	'recreate'	=>	'Once enquiry done and decided to open a new ticket with the same issue, this privilage will allows that.',
	'req_complete'	=>	'Once the ticket is closed and not made it as COMPLETED by customer for more than 1.5 days, agent can initiate a mail to customer requesting to COMPLETE the ticket.',
	'force_complete'	=>	'Once the ticket is closed and not made it as COMPLETED by customer for more than 3 days, agent can Forcibly make the status to COMPLETE.',
	'transcript' => 'View and Mail chat transcript to Customer, Distributor or any Users who relats with this ticket.',
	'dismiss' => 'If the ticket is not in the scope of support, this action allows to dismiss the ticket with valid reason.'
];
$AgentActions = ($Agent->ticket_privilages) ? explode("-",mb_substr($Agent->ticket_privilages,1,-1)) : [];
@endphp

<div class="content">
	<div class="row">
		<div class="col-md-12"><form method="post">{{ csrf_field() }}
			<div class="panel panel-default">
				<div class="panel-heading"><strong>Update {{ $Agent->name }}'s Privilages on Ticket</strong>{!! PanelHeadBackButton(Route('tsa.index')) !!}</div>
				<div class="panel-body">
					<div class="table table-responsive">
						<table class="table table-striped">
							<thead><tr><th>Select</th><th>Action</th><th>Description</th></tr></thead>
							<tbody>@foreach($TeamActions as $action)
								<tr><th style="text-align: center"><input type="checkbox" name="{{ $Agent->code }}[]" value="{{ $action }}"@if(in_array($action,$AgentActions)) checked @endif></th><td>{{ ucwords(str_ireplace("_"," ",$action)) }}</td><td>{{ $Descs[$action] }}</td></tr>
							@endforeach</tbody>
						</table>
					</div>
				</div>
				<div class="panel-footer clearfix">
					<input type="submit" name="submit" value="Update Privilages" class="btn btn-primary pull-right">
				</div>
			</div>
		</form></div>
	</div>
</div>

@endsection