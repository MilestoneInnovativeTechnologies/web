@extends("layouts.app")
@section('title', 'TEST PAGE')
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

<div class="content">
	<?php print_r(session()->all()); ?>
</div>


@endsection
@push("css")
@endpush
@push("js")
<script type="text/javascript" src="js/test.js"></script>
@endpush