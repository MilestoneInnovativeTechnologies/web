@extends("layouts.app")
@section('title', 'MILESTONE INNOVATIVE TECHNOLOGIES :: Customer Registration')
@section('container')

	@if(isset($info) && $info)
	<div class="alert alert-{{ $type }}">
		{{ $text }}
	</div>
	@endif

	@if(session()->has("info"))
	<div class="alert alert-{{ session('type') }}">
		{{ session('text') }}
	</div>
	@endif

	@if(count($errors))
	<div class="alert alert-danger">
		<ul>
			@foreach($errors->all() as $error)
			<li>{{ $error }}</li>
			@endforeach
		</ul>
	</div>
	@endif

	@yield('content')

@endsection
@push("js")
<script type="text/javascript" src="js/customer_page.js"></script>
@endpush