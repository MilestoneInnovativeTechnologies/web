@extends("products.page")
@section("content")

<div class="content"><form method="post" action="product/{{ $Product['code'] }}/packages">{{ csrf_field() }}
	<div class="panel panel-default">
		<div class="panel-heading"><strong>{{ $Product['name'] }}'s Packages</strong><a href="products" class="btn btn-default pull-right btn-sm"><span class="glyphicon glyphicon-arrow-left"></span> Back</a></div>
		<div class="panel-body">
			<div class="row clearfix">
			@if(isset($Editions) && !empty($Editions))
			<?php $JSID = array(); ?>
			@foreach($Editions as $count => $Edition)
			@if( ($count%2) === 0 && $count > 0 ) </div><div class="row clearfix"> @endif
				<div class="col col-xs-6">
					<div class="table-responsive">
						<table class="table table-striped">
							<thead><tr><th>{{ $Edition->name }}</th></tr></thead>
							<tbody><tr><td>
								<select name="packages[{{ $Edition->code }}][]" class="form-control" multiple id="ms_{{ $Edition->code }}">
									<?php $JSID[] = "#ms_".$Edition->code; ?>
									@if(!empty($Packages))
									@foreach($Packages as $PKGObj)
									<option value="{{$PKGObj->code}}"{{ (!empty($PKG[$Edition->code]) && is_array($PKG[$Edition->code]) && in_array($PKGObj->code,$PKG[$Edition->code]))?' selected':'' }}>{{$PKGObj->name}} ({{ $PKGObj->type }})</option>
									@endforeach
									@endif
								</select>
							</td></tr></tbody>
						</table>
					</div>
				</div>
			@endforeach
			@endif
			</div>
		</div>
		<div class="panel-footer clearfix"><input type="submit" name="Update" value="Update Packages" class="btn btn-primary pull-right"></div>
	</div></form>
</div>


@endsection
@push("css")
<link rel="stylesheet" href="css/multiselect.css" type="text/css">
@endpush
@push("js")
<script type="text/javascript" src="js/multiselect.js"></script>
<script type="text/javascript">
$(function(){ $('{{ implode(",",$JSID) }}').multiSelect({
	selectableHeader: "<div class='label label-info'>Still Available</div>",
	selectionHeader: "<div class='label label-primary'>Already Selected</div>"
}); })
</script>
@endpush