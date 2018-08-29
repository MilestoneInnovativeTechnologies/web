@extends("tcm.page")
@include('BladeFunctions')
@section("content")

<div class="content">
	<div class="col col-md-10 col-md-offset-1"><form method="post">{{ csrf_field() }}
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Select/Deselect Specifications</strong>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('tcm.index'):url()->previous()) !!}</div>
			<div class="panel-body"><div class="table-responsive"><table class="striped table"><thead><tr><th>Select</th><th>Name</th><th>Details</th></tr></thead><tbody>
				@php $SPECS = ($tcm->specs) ? $tcm->specs->pluck('code')->toArray() : [] @endphp
				@forelse(\App\Models\TicketCategorySpecification::whereNull('spec')->get() as $spec)
				<tr><td><input type="checkbox" name="spec[]" value="{{ $spec->code }}"@if(in_array($spec->code,$SPECS)) checked @endif></td><td>{{ $spec->name }}</td><td>{!! nl2br($spec->description) !!}</td></tr>
				@empty
				<tr><th colspan="3">No specifications created yet</th></tr>
				@endforelse
			</tbody></table></div>
			</div>
			<div class="panel-footer clearfix">
				<input type="submit" name="submit" value="Assign Specifications" class="btn btn-primary pull-right">
			</div>
		</div>
	</form></div>
</div>

@endsection