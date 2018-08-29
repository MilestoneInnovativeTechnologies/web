@extends("cpo.page")
@include('BladeFunctions')
@section("content")
@php $ORM = \App\Models\CustomerPrintObject::with(['Customer' => function($Q){ $Q->select('code','name'); }])->whereNotNull('preview'); @endphp
@php if(Request()->search_text != ""){ $st = '%'.Request()->search_text.'%'; $ORM->where('function_name','like',$st)->orWhere('function_code','like',$st)->orWhereHas('Customer.Logins',function($Q) use($st){ $Q->where('email','like',$st); })->orWhereHas('Customer.Details',function($Q) use($st){ $Q->where('phone','like',$st); }); } @endphp
@php $Data = $ORM->paginate(16); @endphp

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Print Object Previews</strong>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('cpo.index'):url()->previous()) !!}</div>
		<div class="panel-body">
			<div class="clearfix pagination">
				<div class="pull-left col-xs-4 p0"><div class="input-group"><form><input type="text" name="search_text" class="form-control" placeholder="Search" value="{{ Request()->search_text }}" autofocus></form><a href="javascript:SearchText()" class="input-group-addon"><span class="glyphicon glyphicon-search"></span></a></div></div>
				<div class="pull-right">{!! $Data->appends(['search_text' => Request()->search_text])->links() !!}</div>
			</div>@if($Data->count())
			@foreach($Data->chunk(4) as $chunk)
			<div class="row">
				@foreach($chunk as $cpo)
				<div class="col col-md-3 prev_col"@if($loop->last) style="width:calc(25% - 15px);" @endif><a class="link_block" style="background-image: url({{ \Storage::disk('printobject')->url($cpo->preview) }});" href="{{ route('cpo.details',$cpo->code) }}">&nbsp;</a></div>
				@endforeach
			</div>	
			@endforeach
			@else
			<div class="jumbotron">
				<h2 class="text-center">No Records found</h2>
			</div>@endif
		</div>
	</div>
</div>

@endsection
@push('css')
<style type="text/css">
	a.link_block { display: block; width: 100%; height: 200px; background-size: contain !important; background-repeat: no-repeat; background-position: center; text-decoration: none; }
	div.prev_col { border: 1px solid #DDD; padding: 0px; margin-left: 3px; margin-bottom: 3px; }
</style>
@endpush