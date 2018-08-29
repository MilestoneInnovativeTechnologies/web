@extends("pricelist.page")
@section("content")
@php $Update = isset($Update); @endphp
<div class="content">
	<form class="form pricelist_{{ ($Update)?'update':'create' }}" method="post" action="{{ ($Update)? Route('pricelist.update',["pricelist"	=>	$Code]) : Route('pricelist.store') }}">
		{!! ($Update) ? (method_field('PUT') . csrf_field()) : csrf_field() !!}
		<div class="panel panel-default">
			<div class="panel-heading"><strong>{{ ($Update) ? 'Edit' : 'New' }} Price List</strong><a href="{{ Route('pricelist.index') }}" class="btn btn-default pull-right btn-sm"><span class="glyphicon glyphicon-arrow-left"></span> Back</a></div>
			<div class="panel-body">
				<div class="row">
					<div class="col col-md-3"></div>
					<div class="col col-md-6">
						<div class="form-group clearfix">
							<label for="code" class="control-label col-xs-2">Code</label>
							<div class="col-xs-10">
								<input type="text" name="code" class="form-control" id="code" value="{{ old('code')?:$Code }}">
							</div>
						</div>
						<div class="form-group clearfix">
							<label for="name" class="control-label col-xs-2">Name</label>
							<div class="col-xs-10">
								<input type="text" name="name" class="form-control" id="name" value="{{ old('name')?:((isset($Details['name']))?$Details['name']:'') }}">
							</div>
						</div>
						<div class="form-group clearfix">
							<label for="description" class="control-label col-xs-2">Description</label>
							<div class="col-xs-10">
								<textarea name="description" class="form-control" id="description">{{ old('description')?:((isset($Details['description']))?$Details['description']:'') }}</textarea>
							</div>
						</div>
					</div>
					<div class="col col-md-3"></div>
				</div>
				<hr>
				<div class="row">
					<div class="col col-md-1"></div>
					<div class="col col-md-10">
						<div class="table-responsive">
							<table class="table table-striped pl_details">
								<thead><tr><th width="18%">Product</th><th width="18%">Edition</th><th width="15%">MOP</th><th width="15%">Price</th><th width="15%">MRP</th><th width="15%">Currency</th><th>&nbsp;</th></tr></thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr><td colspan="2"><a href="javascript:AddOneMoreLine()" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-plus"></span> &nbsp; New Line</a> </td><td colspan="5">&nbsp;</td></tr>
								</tfoot>
							</table>
						</div>
					</div>
					<div class="col col-md-1"></div>
				</div>
			</div>
			<div class="panel-footer clearfix">
				<div class="pull-right">
					<input type="submit" name="submit" value="{{ ($Update)?'Update':'Create'}} Price List" class="btn btn-info">
				</div>
			</div>
		</div>
	</form>
</div>

@endsection
@push("js")
<script type="text/javascript">
	var _Products = {!! json_encode($Products) !!}, _Editions = {!! json_encode($Editions) !!};
	@if(null !== old("product"))
		@php
		$Ps = old("product"); $Es = old("edition"); $Vs = old("price"); $Cs = old("currency");
		echo 'var PreDefinedValues = [';
		foreach($Ps as $k => $P){ echo '["'.$Ps[$k].'","'.$Es[$k].'","'.$Vs[$k].'","'.$Cs[$k].'"],'; }
		echo '];';
		@endphp
	@elseif(isset($Update) && $Update === true)
	var PreDefinedValues = [@foreach($Details["items"] as $Obj){!! "['" . implode("', '", array_values(array_replace(array_fill_keys(["product","edition","mop","price","mrp","currency"],"-"),$Obj))) . "']," !!}@endforeach];
	@endif
</script>
@endpush