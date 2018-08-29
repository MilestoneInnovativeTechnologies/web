@extends("customer.page")
@include('BladeFunctions')
@section("content")
@php $isDist = (session()->get('_rolename') == 'distributor'); @endphp

<div class="content customer_change_distributor"><form method="post">{{ csrf_field() }}
	<div class="col-md-6 col-md-offset-3"><div class="panel panel-default">
		<div class="panel-heading"><strong>Change @if($isDist) Parent @else Distributor @endif</strong>{!! PanelHeadBackButton(Route('customer.index')) !!}</div>
		<div class="panel-body"><div class="table-responsive"><table class="table table-striped">
			<tbody>
				<tr><th>Customer</th><th>:</th><td>{{ $Customer->name }}</td></tr>
				<tr><th>Current @if($isDist) Parent @else Distributor @endif</th><th>:</th><td>@if($isDist) {{ $Customer->ParentDetails[0]->name }} @else {{ GetDistributor($Customer->ParentDetails[0])->name }} @endif</td></tr>
				<tr><th>New @if($isDist) Parent @else Distributor @endif</th><th>:</th><td><select name="parent" class="form-control">@if($isDist)<option value="{{ Auth()->user()->Partner->code }}">{{ Auth()->user()->Partner->name }}</option>@endif @foreach($Parents as $Parent) <option value="{{ $Parent->code }}">{{ $Parent->name }}</option> @endforeach</select></td></tr>
				
			</tbody>
		</table></div></div>
		<div class="panel-footer clearfix">
			<input type="submit" name="submit" value="Change @if($isDist) Parent @else Distributor @endif" class="btn btn-primary pull-right">
		</div>
	</div></div></form>
</div>


@endsection
@php
function GetDistributor($P){
	if($P->Roles->contains('name','distributor')) return $P;
	return GetDistributor($P->ParentDetails[0]);
}
@endphp
@push('js')
<script type="text/javascript">
$(function(){
	CP = $.trim('@if($isDist) {{ $Customer->ParentDetails[0]->code }} @else {{ GetDistributor($Customer->ParentDetails[0])->code }} @endif');
	$('[name="parent"]').val(CP);
})
</script>
@endpush