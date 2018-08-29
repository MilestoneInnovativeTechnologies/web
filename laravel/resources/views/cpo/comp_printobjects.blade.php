<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>No</th><th>Product</th><th>Function Name</th><th>Preview</th></tr></thead><tbody>
	@if($PrintObjects && $PrintObjects->isNotEmpty()) @foreach($PrintObjects as $Po)
	<tr><th>{{ $loop->iteration }}</th><td>{{ implode(' ',$Po->Product).' Edition' }}</td><td>{{ $Po->function_name }} <small>({{ $Po->function_code }})</small><br>{{ $Po->print_name }}</td><td>{!! ($Po->preview)?'<a href="'. \Storage::disk('printobject')->url($Po->preview) .'" target="_blank"><img src="' . \Storage::disk('printobject')->url($Po->preview) . '" width="64" height="64"></a>':'no preview available' !!}</td></tr>
	@endforeach @else
	<tr><th colspan="4" style="text-align: center">No records found!</th></tr>
	@endif
</tbody></table></div>