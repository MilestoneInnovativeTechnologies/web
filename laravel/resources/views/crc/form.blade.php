@extends("crc.page")
@include('BladeFunctions')
@section("content")

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>{{ $Customer->name }}</strong>{!! PanelHeadBackButton(Route('crc.index')) !!}</div>
		<div class="panel-body">
			<div class="row">
				<div class="col col-md-4">
					{!! formGroup(2, 'app', 'text', 'App') !!}
					{!! formGroup(2, 'login', 'text', 'Login') !!}
					{!! formGroup(2, 'secret', 'text', 'Secret') !!}
					{!! formGroup(1, 'remarks', 'textarea', 'Remarks') !!}
					<a href="javascript:AddConnection('{{ $Customer->code }}')" class="btn btn-primary pull-right">Add</a>
				</div>
				<div class="col col-md-8" style="border-left: 1px solid #CECECE">
					<h4>Current Connections</h4>
					<div class="table table-responsive">
						<table class="table table-striped connections">
							<thead>
								<tr><th>App</th><th>Login</th><th>Secret</th><th>Remarks</th><th> </th></tr>
							</thead>
							<tbody>@if($Customer->Connections->isNotEmpty()) @foreach($Customer->Connections as $CObj)
								<tr id="{{ $CObj->id }}"><td>{{ $CObj->appname }}</td><td>{{ $CObj->login }}</td><td>{{ $CObj->secret }}</td><td>{{ $CObj->remarks }}</td><td width="50">{!! glyLink("javascript:RemoveConnection('".$CObj->id."')", 'Remove Connection', 'remove', $extra = ['class'=>'btn btn-default btn-sm']) !!}</td></tr>
							@endforeach @endif</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
@push('js')
<script type="text/javascript">
	function AddConnection(C){
		app = $('[name="app"]').val(); login = $('[name="login"]').val(); secret = $('[name="secret"]').val(); remarks = $('[name="remarks"]').val(); 
		if($.trim(app) == "") return alert('App field cannot be empty.');
		FireAPI('api/v1/crc/action/ac',function(R){
			if(typeof(R) != 'object') return alert(R);
			$('[name="app"]').val(''); $('[name="login"]').val(''); $('[name="secret"]').val(''); $('[name="remarks"]').val('');
			$('table.connections tbody').append($('<tr id="'+R.id+'">').html([
				$('<td>').text(R.appname),
				$('<td>').text(R.login),
				$('<td>').text(R.secret),
				$('<td>').text(R.remarks),
				$('<td width="50">').html(btn('Remove Connection','javascript:RemoveConnection(\''+R.id+'\')','remove').addClass('btn-default btn-sm'))
			]))
		},{app:app,login:login,secret:secret,remarks:remarks,customer:C});
	}
	function RemoveConnection(id){
		FireAPI('api/v1/crc/action/rc',function(R){
			if(typeof(R) == 'object') return $('tr#'+R.id).remove();
			if(R == "0") return alert('Error in deleting data');
			alert(R);
		},{id:id});
	}
</script>
@endpush