@extends("customer.page")
@include('BladeFunctions')
@section("content")
@php $Products = \App\Models\Product::with(['Editions' => function($Q){ $Q->select('code','name'); }])->select('code','name')->get(); @endphp

<div class="content customer_change_edition"><form method="post">{{ csrf_field() }}
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Change Editions</strong>{!! PanelHeadBackButton(Route('customer.index')) !!}</div>
		<div class="panel-body">
			<div class="table-responsive customer_lists">
				<table class="table table-striped">
					<tbody>
						<tr><th width="15%" nowrap>Customer</th><td width="20%">{{ $Customer->Customer->name }}</td><td>&nbsp;</td></tr>
						<tr><th>Product</th><td><select name="product" class="form-control" onChange="ProductChanged(this.value)">@foreach($Products as $PRD)
							<option value="{{ $PRD->code }}"@if($Customer->product == $PRD->code) selected @endif>{{ $PRD->name }}</option>
						@endforeach</select></td><td>&nbsp;</td></tr>
						<tr><th>Edition</th><td><select name="edition" class="form-control">@foreach($Products as $PRD)
							@continue($PRD->code != $Customer->product)
							@foreach($PRD->Editions as $EDN)
							<option value="{{ $EDN->code }}"@if($Customer->edition == $EDN->code) selected @endif>{{ $EDN->name }}</option>
							@endforeach
						@endforeach</select></td><td>&nbsp;</td></tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="panel-footer clearfix">
			<input type="submit" name="submit" value="Change Edition" class="btn btn-primary pull-right">
		</div>
	</div></form>
	<div class="jumbotron" style="display: none">
		<h1 class="text-center">No Records found</h1>
		<p class="text-center"><a href="dealer/create" class="btn btn-primary text-center">Create New</a></p>
	</div>
</div>


@endsection
@push('js')
<script type="text/javascript">
var _Editions = {!! $Products->mapWithKeys(function($item){ return [ $item->code => $item->Editions->pluck('name','code') ]; })->toJson() !!}
function ProductChanged(PRD){
	$('[name="edition"]').html($.map(_Editions[PRD],function(name,code){ return $('<option>').attr({value:code}).text(name); }))
}
</script>
@endpush