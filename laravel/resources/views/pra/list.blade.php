@extends("pra.page")
@include('BladeFunctions')
@section("content")
@php
$Data = \App\Models\PrivateArticle::all();
@endphp

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading">Articles</div>
		<div class="panel-body">
		@forelse($Data as $A)
			<pre style="padding: 0px 0px 0px 15px; border: none; background-color: transparent;	">{{ $loop->iteration }}.			{{ date('D d/M/Y',strtotime($A->created_at)) }}			{{ $A->title}}			<a href="{{ $A->url }}" target="_blank" class="link">View Article >></a></pre>
		@empty
		<div class="jumbotron text-center"><strong>No Articles</strong></div>
		@endforelse
		</div>
</div>

@endsection