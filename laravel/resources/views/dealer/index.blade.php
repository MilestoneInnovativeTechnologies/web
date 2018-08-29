@extends("dealer.page")
@section("content")

<div class="content">
	
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Dealers</strong><a href="{{ Route('dealer.create')}}" class="btn btn-info btn-sm pull-right"><span class="glyphicon glyphicon-plus"></span> &nbsp; New Dealer</a></div>
		<div class="panel-body">
			<div class="table-responsive dealers_list">
				<table class="table table-bordered">
					<thead><tr><th>No</th><th>Code</th><th>Name</th><th>Country</th><th>Email</th><th>Phone</th><th>Actions</th></tr></thead>
					<tbody>
					</tbody>
				</table>
			</div>
			<div class="jumbotron" style="display: none">
				<h1 class="text-center">No Records found</h1>
				<p class="text-center"><a href="dealer/create" class="btn btn-primary text-center">Create New</a></p>
			</div>
		</div>
	</div>
	
</div>
<div class="modal fade" id="modalLoginReset" data-code="">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">You are about to send Login Reset Link to Dealer</h4>
			</div>
			<div class="modal-body clearfix">
				<div class="table table-responsive">
					<table class="table table-striped">
						<tbody>
							<tr><th>Dealer Code</th><td class="modal_dealer_code"></td></tr>
							<tr><th>Dealer Name</th><td class="modal_dealer_name"></td></tr>
							<tr><th>Dealer Email</th><td class="modal_dealer_email"></td></tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" onClick="SendLRL()">Send Login Reset Link</button>
			</div>
		</form></div>
	</div>
</div>

@endsection
@push("js")
<script type="text/javascript">
var _urls = {"countries":"{!! Route('dealer.countries',['code'	=>	'--CODE--']) !!}","products":"{!! Route('dealer.products',['code'	=>	'--CODE--']) !!}","show":"{!! Route('dealer.show',['code'	=>	'--CODE--']) !!}","edit":"{!! Route('dealer.edit',['code'	=>	'--CODE--']) !!}","delete":"{!! Route('dealer.destroy',['code'	=>	'--CODE--']) !!}/delete"}
</script>
@endpush