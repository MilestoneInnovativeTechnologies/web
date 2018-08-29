@extends("tscc.page")
@include('BladeFunctions')
@section("content")

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>{{ $Customer->name }}</strong>{!! PanelHeadBackButton(Route('tscc.index')) !!}</div>
		<div class="panel-body">
			<div class="row">
				<div class="col col-md-5">
					{!! formGroup(1, 'name', 'text', 'Name') !!}
					{!! formGroup(1, 'value', 'textarea', 'Value') !!}
					<a href="javascript:AddCookie('{{ $Customer->code }}')" class="btn btn-primary pull-right">Add</a>
				</div>
				<div class="col col-md-7" style="border-left: 1px solid #CECECE">
					<h4>Current Cookies</h4>
					<div class="table table-responsive">
						<table class="table table-striped cookies">
							<tbody>@if($Customer->Cookies->isNotEmpty()) @foreach($Customer->Cookies as $CObj)
								<tr id="{{ $CObj->id }}"><th>{{ $CObj->name }}</th><td>{{ $CObj->value }}</td><td width="50">{!! glyLink("javascript:RemoveCookie('".$CObj->id."')", 'Remove Cookie', 'remove', $extra = ['class'=>'btn btn-default btn-sm']) !!}</td></tr>
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
	function AddCookie(C){
		Name = $('[name="name"]').val(); Value = $('[name="value"]').val();
		if($.trim(Name) == "" || $.trim(Value) == "") return alert('Name and Value fields cannot be empty.');
		FireAPI('api/v1/tscc/action/ac',function(R){
			if(typeof(R) != 'object') return alert(R);
			$('[name="name"]').val(''); $('[name="value"]').val('');
			$('table.cookies tbody').append($('<tr id="'+R.id+'">').html([
				$('<th>').text(R.name),
				$('<td>').text(R.value),
				$('<td width="50">').html(btn('Remove Cookie','javascript:RemoveCookie("'+R.id+'")','remove').addClass('btn-default btn-sm'))
			]))
		},{name:Name,value:Value,customer:C});
	}
	function RemoveCookie(id){
		FireAPI('api/v1/tscc/action/rc',function(R){
			if(typeof(R) == 'object') return $('tr#'+R.id).remove();
			if(R == "0") return alert('Error in deleting data');
			alert(R);
		},{id:id});
	}
</script>
@endpush