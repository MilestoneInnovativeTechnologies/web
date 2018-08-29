@extends("distributor.page")
@include("BladeFunctions")
@section("content")
@php $Categories = \App\Models\TicketCategoryMaster::withoutGlobalScopes()->get() @endphp
@php $Excluded = \App\Models\DistributorExcludeCategory::whereDistributor(Request()->distributor)->get() @endphp
@php //dd($Categories->toArray(),$Excluded->toArray()) @endphp


<div class="content">
	<form method="post">{{ csrf_field() }}
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Exclude Support Categories</strong>{!! PanelHeadBackButton((url()->previous() == url()->current())?Route('distributor.index'):url()->previous()) !!}</div>
			<div class="panel-body">@if($Categories->isNotEmpty())
				<select name="category[]" multiple class="form-control" id="categories">
					@foreach($Categories as $Category)
					<option value="{{ $Category->code }}"{{ ($Excluded->contains('category',$Category->code)?' selected':'' )}}>{{ $Category->name }}</option>
					@endforeach
				</select>
			@else
			<p>No categories found!</p>
			@endif</div>
			<div class="panel-footer clearfix">
				<input type="submit" name="submit" value="Exclude Selected Categories" class="btn btn-primary pull-right">
			</div>
		</div>
	</form>
</div>

@endsection
@push("js")
<script type="text/javascript" src="js/multiselect.js"></script>
<script type="text/javascript" src="js/quicksearch.js"></script>
<script type="text/javascript">
$(function(){ $('#categories').multiSelect({
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