@extends("customer.page")
@include('BladeFunctions')
@section("content")
@php $Products = \App\Models\Product::with(['Editions' => function($Q){ $Q->select('code','name'); }])->select('code','name')->get(); @endphp

<div class="content customer_add_product"><form method="post">{{ csrf_field() }}
	<div class="col-md-8 col-md-offset-2 col-sm-12"><div class="panel panel-default">
		<div class="panel-heading"><strong>Add Product</strong>{!! PanelHeadBackButton(Route('customer.index')) !!}</div>
		<div class="panel-body"><div class="table-responsive"><table class="table table-striped">
			<tbody>
				<tr><th>Customer</th><th>:</th><td>{{ $Customer->name }}</td></tr>
				<tr><th colspan="3">Current Products</th></tr>
				@forelse($Customer->registration as $reg)
					<tr><td colspan="3">{{ $loop->iteration }}. {{ $reg->Product->name }} {{ $reg->Edition->name }} Edition @if($reg->remarks) ({{ $reg->remarks }}) @endif</td></tr>
				@empty
				<tr><td colspan="3">No any products</td></tr>
				@endforelse
				<tr><th colspan="3">New Product</th></tr>
				<tr><td>

						<select name="product" class="form-control" onChange="ProductChanged(this.value)">@foreach($Products as $PRD)
								<option value="{{ $PRD->code }}">{{ $PRD->name }}</option>
							@endforeach</select>

					</td><td></td><td>

						<select name="edition" class="form-control">@foreach($Products as $PRD)
								@foreach($PRD->Editions as $EDN)
									<option value="{{ $EDN->code }}">{{ $EDN->name }}</option>
								@endforeach
							@endforeach</select>

					</td></tr>
			<tr><th>Remarks</th><th>:</th><td><input type="text" name="remarks" class="form-control" id="remarks" value=""></td></tr>
			</tbody>
		</table></div></div>
		<div class="panel-footer clearfix">
			<input type="submit" name="submit" value="Add Product" class="btn btn-primary pull-right">
		</div>
	</div></div></form>
</div>


@endsection
@push('js')
<script type="text/javascript">
	var _Editions = {!! $Products->mapWithKeys(function($item){ return [ $item->code => $item->Editions->pluck('name','code') ]; })->toJson() !!}
			function ProductChanged(PRD){
		$('[name="edition"]').html($.map(_Editions[PRD],function(name,code){ return $('<option>').attr({value:code}).text(name); }))
	}
	$(function(){ ProductChanged(document.getElementsByName('product')[0].value) })
</script>
@endpush
