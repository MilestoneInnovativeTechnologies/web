@extends("tkt.page")
@section("content")
@php
if(request('distributor')){
	$distributor = request('distributor');
	$Tickets = $Tickets->filter(function($item)use($distributor){ return $item->Customer->get_distributor()->code == $distributor; });
}
if(request('values')){
	$values = request('values');
	$Tickets = $Tickets->filter(function($item)use($values){
		$specs = $item->category_specs_values;
		foreach($values as $value){
			if($specs->where('value',$value)->count() == 0)
				return false;
		}
		return true;
	});
}
@endphp

<div class="content">
	@component('tkt.comp_tickets',compact('Tickets')) @endcomponent
</div>

@endsection