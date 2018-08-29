@extends("pi.page")
@include('BladeFunctions')
@section("content")
@php $Data = \App\Models\PartnerProduct::wherePartner(Auth()->user()->partner)->with('Products.Editions','Editions')->get(); @endphp

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Products</strong>{!! PanelHeadBackButton(Route('dashboard')) !!}</div>
		<div class="panel-body">@if($Data->count())
			<div class="table-responsive">
				<table class="table table-bordered">
					<thead><tr><th>No</th><th>Product</th><th>Edition</th><th>Action</th></tr></thead>
					<tbody>@foreach($Data as $D)
						<tr>
							<td>{{ $loop->iteration }}</td>
							<td>{{ $D->Products->name }}</td>
							<td>{{ $D->Editions->name }}</td>
							<td nowrap>
								{!! glyLink('javascript:ProductDetails(\''.$D->product.'\')','View Product Details','info-sign',['class'=>'btn']) !!}
								{!! glyLink('javascript:DownloadLink(\''.$D->product.'\',\''.$D->edition.'\')','Download/Generate Link','cloud-download',['class'=>'btn']) !!}
								{!! glyLink('javascript:SendProductInformation(\''.$D->product.'\')','Send product information mail','export',['class'=>'btn']) !!}
								{!! glyLink('javascript:ViewUpdates(\''.$D->product.'\',\''.$D->edition.'\')','View latest update details','sort-by-attributes',['class'=>'btn']) !!}
							</td>
						</tr>
					@endforeach</tbody>
				</table>
			</div>@else
			<div class="jumbotron">
				<h2 class="text-center">No Records found</h2>
			</div>@endif
		</div>
	</div>
	{!! divClass('product_details',BSPanel('<strong class="product_name"></strong>','')) !!}
	@php
		$Grid = BSGrid([5,7]);
		$Grid = stickContent(BSTable(['striped','version_details'],'',''),$Grid,'|ROW1COL1|');
		$Grid = stickContent(divClass('customer_list',BSTable(['striped','customer_list'],'<tr><th colspan="2">Customer List</th></tr>','')),$Grid,'|ROW1COL2|'); 
	@endphp
	{!! divClass('product_updates',BSPanel('<strong class="product_name"></strong>',$Grid)) !!}
</div>
<div id="downloadModal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Create Download Link</h4>
			</div>
			<div class="modal-body clearfix">
				<div class="form-group form-horizontal clearfix">
					<label class="control-label col-xs-4">Select Package</label>
					<div class="col-xs-8">
						<select name="package" onChange="ValidityChanged()" data-product="" data-edition="" class="form-control"></select>
					</div>
				</div>
				<div class="form-group form-horizontal clearfix">
					<label class="control-label col-xs-4">Validity</label>
					<div class="col-xs-8">
						<select name="validity" onChange="ValidityChanged()" class="form-control">{!! implode('',array_map(function($O){ return '<option value="'.$O.'"'.(($O == '12 Hours')?' selected':'').'>'.$O.'</option>'; },['1 Hour','2 Hours','6 Hours','12 Hours','1 Day','2 Days','3 Days'])) !!}</select>
					</div>
				</div>
				<div class="form-group form-horizontal clearfix col-xs-12 generatebutton">
					<a href="javascript:GenerateDownloadLink()" class="btn btn-primary pull-right">Generate Download Link</a>
				</div>
				<div class="form-group form-horizontal clearfix col-xs-12" style="display: none">
					<textarea name="generatedlink" class="form-control" style="height: 100px;"></textarea>
					<a href="" target="_blank">Click here to download now</a>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</form></div>
	</div>
</div>
<div id="mailModal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Send Product Information and Download Links</h4>
			</div>
			<div class="modal-body clearfix">
				<div class="form-group form-horizontal clearfix">
					<label class="control-label col-xs-4">Select Edition</label>
					<div class="col-xs-8">
						<select name="edition" data-product="" data-edition="" class="form-control" onChange="EditionChaged()"><option value="*">All Editions</option></select>
					</div>
				</div>
				<div class="form-group form-horizontal clearfix">
					<label class="control-label col-xs-4">Select Package</label>
					<div class="col-xs-8">
						<select name="package" data-product="" data-edition="" class="form-control"><option value="*">All Packages</option></select>
					</div>
				</div>
				<div class="form-group form-horizontal clearfix">
					<label class="control-label col-xs-4">Email</label>
					<div class="col-xs-8">
						<select name="customer" id="emailSelectCustomer" class="form-control" onChange="CustomerSelected(this.value)"></select>
						<div class="guest_customer_email" style="display: none">
							<input type="text" class="form-control" style="float: left; width: calc(100% - 20px)" id="guest_customer_email" name="guest_customer_email" placeholder="Guest's Email" value="">
							<a href="javascript:NoGuestEmail()" title="Remove"><span class="glyphicon glyphicon-remove pull-right" style="top: 9px;"></span></a>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" onClick="SendIDMail()">Send Mail</button>
			</div>
		</form></div>
	</div>
</div>

@endsection
@push("js")
<script type="text/javascript">
	var _Products = {!! $Data->groupBy('product')->map(function($item, $key){ return [$item->first()->Products->name,$item->first()->Products->description_public,$item->map(function($item, $key){ $Edition = $item->edition; return [$Edition => $item->Products->Editions->map(function($item) use($Edition){ return ($item->code == $Edition)?[$item->name,$item->pivot->description]:NULL; })->filter()->values()]; })]; }) !!};
</script>
<script type="text/javascript" src="js/productinfo.js"></script>
@endpush