@extends("products.page")
@section("content")

<?php
$MyFeatures = array();
$MyFeatureValues = array();
foreach($PFs['features'] as $FeatureArray){
	$MyFeatures[] = $FeatureArray['id'];
	$Vals = $FeatureArray['pivot']["value"];
	if($FeatureArray['value_type'] == "MULTISELECT") {
		$MyVal = explode("-",mb_substr($Vals,1,-1));
	} else {
		$MyVal = array($Vals);
	}
	$MyFeatureValues[$FeatureArray['id']] = $MyVal;
}
?>

<div class="content"><form method="post">{{ csrf_field() }}
	<div class="panel panel-default">
		<div class="panel-heading"><strong>{{ $PFs['name'] }}'s Features</strong><a href="{{ url()->previous() }}" class="btn btn-default pull-right btn-sm"><span class="glyphicon glyphicon-arrow-left"></span> Back</a></div>
		<div class="panel-body">
			<div class="table-responsive">
				<table class="table table-striped">
					<thead><tr><th width="5%">#</th><th width="65%">Features</th><th width="30%">Value</th></tr></thead>
					<tbody>
					@if(!empty($Features))
						@foreach($Features as $Feature)
						<?php
							$isIN = in_array($Feature['id'],$MyFeatures);
							$ID = $Feature -> id;
							$VALS = (isset($MyFeatureValues[$ID]))?$MyFeatureValues[$ID]:array("");
							$isO = in_array($Feature->value_type,array("OPTION","MULTISELECT"));
							$isM = ($Feature -> value_type == "MULTISELECT");
						?>
						<tr>
							<th><input type="checkbox" name="features[]" value="{{ $ID }}"{{ ($isIN)?' checked':'' }}></th>
							<td><strong>{{ $Feature['name'] }}</strong><br>{{ $Feature['description_public'] }}</td>
							<td>
							@if($isO)
								<select class="form-control" name="values[{{ $ID }}]{!! ($isM)?'[]" multiple':'"' !!}">
								@foreach($Feature->options as $OptArray)
									<option value="{{ $OptArray['option'] }}"{{ (in_array($OptArray['option'],$VALS))?' selected':'' }}>{{ $OptArray['option'] }}</option>
								@endforeach
								</select>
							@else
								@if($Feature -> value_type == "STRING")
									<input class="form-control" type="text" name="values[{{ $ID }}]" value="{{ $VALS[0] }}">
								@else
									<label class="switch"><input class="form-control" type="checkbox" name="values[{{ $ID }}]" value="YES"{{ ($VALS[0] == "YES")?' checked':'' }}><div class="slider"></div></label>
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
		<div class="panel-footer clearfix"><input type="submit" name="Update" value="Update Features" class="btn btn-primary pull-right"></div>
	</div>
</form></div>

@endsection
@push("css")
<link rel="stylesheet" href="css/switch.css" type="text/css">
@endpush
