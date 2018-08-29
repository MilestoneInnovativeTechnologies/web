@extends("dealer.page")
@section("content")


<div class="content dealer_countries">
	<form action="{{ Route('dealer.countries',['dealer'	=>	$Code]) }}" method="post">
		{{ csrf_field() }}
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Dealer Countries</strong><a href="{{ Route('dealer.index') }}" class="btn btn-default pull-right btn-sm"><span class="glyphicon glyphicon-arrow-left"></span> Back</a></div>
			<div class="panel-body">
				<select name="countries[]" multiple class="form-control" id="countries">
					@foreach($All as $ID => $Country)
					<option value="{{ $ID }}"{{ ($My->has($ID)?' selected':'' )}}>{{ $Country }}</option>
					@endforeach
				</select>
			</div>
			<div class="panel-footer clearfix">
				<div class="pull-right">
					<input type="submit" name="submit" value="Update" class="btn btn-info">
				</div>
			</div>
		</div>
	</form>
</div>

@endsection
@push("js")
<script type="text/javascript" src="js/multiselect.js"></script>
<script type="text/javascript">
$(function(){ $('#countries').multiSelect({
	selectableHeader: "<div class='label label-info'>Available Countries</div>",
	selectionHeader: "<div class='label label-primary'>Selected Countries</div>"
}); })
</script>
@endpush
@push("css")
<link rel="stylesheet" href="css/multiselect.css" type="text/css">
@endpush