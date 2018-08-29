@extends("products.page")
@section("content")
<?php
if(!empty($Products->editions)){
	$PEs = array(); $EDs = array();
	foreach($Products->editions as $PEArray){
		$PEs[] = $PEArray -> code;
		$EDs[$PEArray -> code] = array( $PEArray -> pivot -> level, $PEArray -> pivot -> description);
	}
}
?>
<div class="content"><form method="post">{{ csrf_field() }}
	<div class="panel panel-default">
		<div class="panel-heading"><strong>{{ $Products['name'] }}'s Editions</strong><a href="products" class="btn btn-default pull-right btn-sm"><span class="glyphicon glyphicon-arrow-left"></span> Back</a></div>
		<div class="panel-body">
			<div class="table-responsive">
				<table class="table table-striped">
					<thead><tr><th width="5%">#</th><th width="17%">Feature Level</th><th width="33%">Editions</th><th width="40%">Description</th><th width="5%">Features</th></tr></thead>
					<tbody>
					@if(!empty($Editions))
						@foreach($Editions as $EditionArray)
						<tr>
							<th><input type="checkbox" name="editions[]" value="{{ $EditionArray -> code }}"{{ (in_array($EditionArray -> code,$PEs))?' checked':'' }}></th>
							<td><input type="text" name="level[{{ $EditionArray->code }}]" value="{{ (in_array($EditionArray -> code,$PEs))?($EDs[$EditionArray -> code][0]):'' }}" class="form-control"></td>
							<td><strong>{{ $EditionArray -> name }}</strong><br><small>{{ $EditionArray -> description_public }}</small></td>
							<td><textarea name="description[{{ $EditionArray->code }}]" class="form-control" style="height: 180px">{{ (in_array($EditionArray -> code,$PEs))?($EDs[$EditionArray -> code][1]):($EditionArray -> description_public) }}</textarea></td>
							<th><a href="product/{{ $Products['code'] }}/edition/{{ $EditionArray -> code }}/features" class="btn" data-toggle="tooltip" title="View/Edit Features of {{ $Products['name'] }}'s {{ $EditionArray -> name }} Edition"><span class="glyphicon glyphicon-tags"></span></a></th>
						</tr>
						@endforeach
					@endif
					</tbody>
				</table>
			</div>
		</div>
		<div class="panel-footer clearfix"><input type="submit" name="Update" value="Update Editions" class="btn btn-primary pull-right"></div>
	</div>
</form></div>

@endsection