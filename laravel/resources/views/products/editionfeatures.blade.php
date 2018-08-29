@extends("products.page")
@section("content")
<?php
$PFS = array(); $AF = array();
if(!empty($Features)) {
	foreach($Features as $FID => $FA){
		if(isset($PFs[$FID]) && !empty($PFs[$FID])){
			$PFS[] = '<tr><th>'.(count($PFS)+1).'</th><td><strong>'.$Features[$FID][0].'</strong><br>'.$Features[$FID][1].'</td><td>'.$PFs[$FID].'</td></tr>';
			continue;
		}
	}
}
?>

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Product's Features</strong><a href="product/{{ $Product[0] }}/editions" class="btn btn-default pull-right btn-sm"><span class="glyphicon glyphicon-arrow-left"></span> Back</a></div>
		<div class="panel-body">
			<div class="table-responsive">
				<table class="table table-striped">
					<thead><tr><th width="5%">#</th><th width="65%">Feature</th><th width="30%">Value</th></tr></thead>
					<tbody>
					@if($PFS)
					{!! implode("",$PFS) !!}
					@endif
					</tbody>
				</table>
			</div>
		</div>
	</div><form action="" method="post">{{ csrf_field() }}
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Edition's Features</strong></div>
		<div class="panel-body">
			<div class="table-responsive">
				<table class="table table-striped">
					<thead><tr><th width="5%">#</th><th width="65%">Feature</th><th width="30%">Value</th></tr></thead>
					<tbody>
					@if(!empty($Features))
						@foreach($Features as $FID => $FA)
						<?php if(isset($PFs[$FID]) && !empty($PFs[$FID])) continue; ?>
						<tr>
							<?php $isE = (isset($EFs[$FID]) && !empty($EFs[$FID])); ?>
							<th><input type="checkbox" name="features[]" value="{{ $FID }}"{{ ($isE)?' checked':'' }}></th>
							<td><strong>{{ $FA[0] }}</strong><br>{{ $FA[1] }}</td>
							<td>
								@if(in_array($FA[3],["OPTION","MULTISELECT"]))
									<select name="values[{{ $FID }}]" class="form-control"{{ ($FA[3] == "MULTISELECT")?' multiple':'' }}>
										<?php
											$MyOpts = array();
											if($isE && !is_null($EFs[$FID])){
												if($FA[3] == "OPTION") $MyOpts[] = $EFs[$FID];
												else $MyOpts = explode("-",mb_substr($EFs[$FID],1,-1));
											}
										?>
										@foreach($FA[4] as $Option)
											<option value="{{ $Option }}"{{ (in_array($Option,$MyOpts))?' selected':'' }}>{{ $Option }}</option>
										@endforeach
									</select>
								@else
									@if($FA[3] == "STRING")
										<input type="text" name="values[{{ $FID }}]" class="form-control" value="{{ ($isE && !is_null($EFs[$FID]))?($EFs[$FID]):'' }}">
									@else
										<label class="switch"><input class="form-control" type="checkbox" name="values[{{ $FID }}]" value="YES"{{ (isset($EFs[$FID]) && !empty($EFs[$FID]) && $EFs[$FID] == "YES")?' checked':'' }}><div class="slider"></div></label>
									@endif
								@endif
							</td>
						</tr>
						@endforeach
					@endif
					</tbody>
				</table>
			</div>
		</div>
		<div class="panel-footer clearfix">
			<input type="submit" name="Update" value="Update {{ $Edition[1] }}'s Features" class="btn btn-primary pull-right">
		</div>
	</div></form>
</div>


@endsection
@push("css")
<link rel="stylesheet" href="css/switch.css" type="text/css">
@endpush