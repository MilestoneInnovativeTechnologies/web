@extends("distributor.page")
@include('BladeFunctions')
@section("content")
@php
$Distributor = \App\Models\Distributor::find(Request()->distributor)->load('Supportteam');
@endphp


<div class="content">
	<div class="col-md-6 col-md-offset-3"><form method="post">{{ csrf_field() }}
		<div class="panel panel-default">
			<div class="panel-heading"><span class="panel-title">Change Distributor's Support Team</span>{!! PanelHeadBackButton((url()->previous() == url()->current())?Route('distributor.index'):url()->previous()) !!}</div>
			<div class="panel-body">
				<div class="table-responsive"><table class="table table-striped"><tbody>
					<tr><th>Distributor</th><th>:</th><td>{{ $Distributor->name }}</td></tr>
					<tr><th>Support Team</th><th>:</th><td>{{ $Distributor->Supportteam[0]->Team->name }}</td></tr>
					<tr><th>Change To</th><th>:</th><td><select class="form-control" name="supportteam">{!! \App\Models\SupportTeam::all()->map(function($item){ return '<option value="'.$item->code.'">'.$item->name.'</option>'; })->implode('') !!}</select></td></tr>
				</tbody></table></div>
			</div>
			<div class="panel-footer clearfix">
				<input type="submit" name="update" value="Change Support Team" class="btn pull-right btn-primary">
			</div>
		</div>
	</form></div>
</div>

@endsection
@push('js')
<script type="text/javascript">
$(function(){
	$('[name="supportteam"]').val('{{ $Distributor->Supportteam[0]->Team->code }}');
})
</script>
@endpush