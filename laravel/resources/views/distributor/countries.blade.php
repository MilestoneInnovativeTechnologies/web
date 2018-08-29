@extends("distributor.page")
@section("content")


<div class="content distributor_countries">
	<form action="{{ Route('distributor.countries',['distributor'	=>	$Code]) }}" method="post">
		{{ csrf_field() }}
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Distributor Countries</strong><a href="{{ Route('distributor.index') }}" class="btn btn-default pull-right btn-sm"><span class="glyphicon glyphicon-arrow-left"></span> &nbsp; Back</a></div>
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
<script type="text/javascript" src="js/quicksearch.js"></script>
<script type="text/javascript">
$(function(){ $('#countries').multiSelect({
	selectableHeader: "<input type='text' class='search-input form-control' autocomplete='off'>",
	selectionHeader: "<input type='text' class='search-input form-control' autocomplete='off'>",
	afterInit: function(ms){
			var that = this,
					$selectableSearch = that.$selectableUl.prev(),
					$selectionSearch = that.$selectionUl.prev(),
					selectableSearchString = '#'+that.$container.attr('id')+' .ms-elem-selectable:not(.ms-selected)',
					selectionSearchString = '#'+that.$container.attr('id')+' .ms-elem-selection.ms-selected';

			that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
			.on('keydown', function(e){
				if (e.which === 40){
					that.$selectableUl.focus();
					return false;
				}
			});

			that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
			.on('keydown', function(e){
				if (e.which == 40){
					that.$selectionUl.focus();
					return false;
				}
			});
		},
		afterSelect: function(){
			this.qs1.cache();
			this.qs2.cache();
		},
		afterDeselect: function(){
			this.qs1.cache();
			this.qs2.cache();
		}
}); })
</script>
@endpush
@push("css")
<link rel="stylesheet" href="css/multiselect.css" type="text/css">
@endpush