@extends('emails.layout')
@section("content")

			<div><p>Hi, {{ $Name }}</p></div>
			<div><p>There is some modification in the countries, that you have assigned earlier.</p></div>
			<div><p>The following are the countries that you have access to sell your products.</p></div>
			<div style="width: 90%; margin: auto; padding: 20px; border: 1px solid #E2E2E2; background-color: #FFF4E4;">
				<ol>
					@foreach($Countries as $Country)
					<li>{{ $Country }}</li>
					@endforeach
				</ol>
			</div>
			<div><p>For more informations, please email {{ $Parent['name'] }} at <u>{!! $Parent['email'] !!}</u> or call {{ $Parent['phone'] }}</p></div>

@endsection