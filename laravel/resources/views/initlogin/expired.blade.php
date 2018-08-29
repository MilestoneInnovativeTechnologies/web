@extends("layouts.app")
@section('title', 'MIT :: Login')
@include('BladeFunctions')
@section('content')

{!! divClass('jumbotron text-center',GetHTMLElement('h2','','The link has been EXPIRED!!')) !!}


@endsection
@push('css')
<style type="text/css">
	nav { display: none !important; }
</style>
@endpush
@push("js")
<script type="text/javascript">
	window.onload = function(){
		setTimeout(function(){ location.href = '/home'; },3000)
	}
</script>
@endpush