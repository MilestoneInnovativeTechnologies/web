@extends("log.page")
@include('BladeFunctions')
@section("content")

<div class="content unusr">
	@php $Table = BSTable('striped','','',['theadAttr'	=>	'class="unusr_tbl_hd"','tbodyAttr'	=>	'class="unusr_tbl_bd"']) @endphp
	{!! BSPanel('<strong>Unknown Users Software usage log</strong>'.PanelHeadButton('javascript:LoadMore()','Load More','level-up','default','sm'),$Table) !!}
	{!! BSPanel('<strong>User Details</strong>'.PanelHeadButton('javascript:VDoMC_Close()',' ','remove','default','sm'),'') !!}
</div>

@endsection
@push("js")
<script type="text/javascript" src="js/unusr.js?_=20181218"></script>
@endpush