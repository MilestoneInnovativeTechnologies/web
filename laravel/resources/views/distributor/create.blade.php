@extends("distributor.page")
@section("content")
@php $Update = isset($Update); $Details = (session()->getOldInput())?:(($Update)?$Details:NULL); @endphp

<div class="content">
	<form class="form distributor_{{ ($Update)?'update':'create' }}" method="post" action="{{ ($Update) ? Route('distributor.update',["code"	=>	$Code]) : Route('distributor.store') }}">
		{!! ($Update) ? (method_field('PUT') . csrf_field()) : csrf_field() !!}
		<div class="panel panel-default">
			<div class="panel-heading"><strong>{{ ($Update)?'Edit':'New'}} Distributor</strong><a href="{{ Route("distributor.index") }}" class="btn btn-default pull-right btn-sm">Back</a></div>
			<div class="panel-body">
				<div class="row">
					<div class="clearfix">&nbsp;</div>
					<div class="col col-md-5">
						<div class="form-group clearfix form-horizontal">
							<label class="control-label col-xs-4">Code</label>
							<div class="col-xs-8">
								<input type="text" name="code" class="form-control" value="{{ isset($Details['code'])?$Details['code']:$Code }}">
							</div>
						</div>
						<div class="form-group clearfix form-horizontal">
							<label class="control-label col-xs-4">Name</label>
							<div class="col-xs-8">
								<input type="text" name="name" class="form-control" value="{{ $Details['name'] }}">
							</div>
						</div>
						<div class="form-group clearfix form-horizontal">
							<label class="control-label col-xs-4">Country</label>
							<div class="col-xs-8">
								<select name="country" class="form-control" onChange="CountryChanged()"{!! ($Details['country'])? ' data-pre-value="'.$Details['country'].'"' :'' !!}></select>
							</div>
						</div>
					</div>
					<div class="col col-md-5">
						<div class="form-group clearfix form-horizontal">
							<label class="control-label col-xs-4">Email</label>
							<div class="col-xs-8">
								<input type="text" name="email" class="form-control" value="{{ $Details['email'] }}">
							</div>
						</div>
						<div class="form-group clearfix form-horizontal">
							<label class="control-label col-xs-4">Phone</label>
							<div class="col-xs-8">
								<div class="input-group">
									<span class="input-group-addon phonecode">{{ $Details['phonecode'] }}</span>
									<input type="text" name="phone" class="form-control" value="{{ $Details['phone'] }}" style="border-bottom-right-radius: 4px; border-top-right-radius: 4px;">
									<input type="hidden" name="phonecode" id="phonecode" value="{{ $Details['phonecode'] }}">
									<input type="hidden" name="currency" id="currency" value="{{ $Details['currency'] }}">
								</div>
							</div>
						</div>
						<div class="form-group clearfix form-horizontal">
							<label class="control-label col-xs-4">Price list</label>
							<div class="col-xs-8">
								<select name="pricelist" class="form-control"{!! ($Details['pricelist'])? ' data-pre-value="'.$Details['pricelist'].'"' :'' !!}></select>
							</div>
						</div>
					</div>
					<div class="col col-md-2"></div>
				</div>
				<hr>
				<div class="row">
					<div class="col col-md-1"></div>
					<div class="col col-md-5" style="border-right: 1px solid #CCC; min-height: 225px;">
						<div class="table-responsive">
							<table class="table table-striped products">
								<thead><tr><th width="40%">Product</th><th width="40%">Edition</th><th>&nbsp;</th></tr></thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr><td><a href="javascript:AddOneMoreLine()" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-plus"></span> &nbsp; New Line</a> </td><td colspan="4">&nbsp;</td></tr>
								</tfoot>
							</table>
						</div>
					</div>
					<div class="col col-md-5">
						<div class="form-group col-xs-12">
							<label>Address Line 1</label>
							<input type="text" name="address1" class="form-control" value="{{ $Details['address1'] }}">
						</div>
						<div class="form-group clearfix">
							<div class="col-xs-6">
								<label>Address Line 2</label>
								<input type="text" name="address2" class="form-control" value="{{ $Details['address2'] }}">
							</div>
							<div class="col-xs-6">
								<label>Web Site</label>
								<input type="text" name="website" class="form-control" value="{{ $Details['website'] }}">
							</div>
						</div>
						<div class="form-group clearfix">
							<div class="col-xs-6">
								<label>State</label>
								<select name="state" class="form-control" onChange="StateChanged()"{!! ($Details['state'])? ' data-pre-value="'.$Details['state'].'"' :'' !!}></select>
							</div>
							<div class="col-xs-6">
								<label>City</label>
								<select name="city" class="form-control"{!! ($Details['city'])? ' data-pre-value="'.$Details['city'].'"' :'' !!}></select>
							</div>
						</div>
					</div>
					<div class="col col-md-1"></div>
				</div>
			</div>
			<div class="panel-footer clearfix">
				<div class="pull-right">
					<input type="submit" name="submit" value="{{ ($Update)?'Update':'Create'}} Distributor" class="btn btn-info">
				</div>
			</div>
		</div>
	</form>
</div>

@endsection
@push("js")
<script type="text/javascript">
var _Products = {!! json_encode($Products) !!}, _Editions = {!! json_encode($Editions) !!};
@if($Details["product"])
	var PreDefinedValues = [@foreach($Details["product"] as $k => $P)['{{$P}}','{{$Details["edition"][$k]}}'],@endforeach]
@endif
</script>
@endpush