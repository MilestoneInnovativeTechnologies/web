@extends('emails.layout')
@section("content")

			<div><p>The registration request of your customer, <strong>{{ $Name }}</strong> for the product, <strong>{{ $Software }}</strong>, has been approved.</p></div>
			<div><p>Here follows the registration details</p></div>
			<div style="width: 90%; margin: auto; padding: 20px; border: 1px solid #E2E2E2; background-color: #FFF4E4; text-align: center;">
				<p>Serial Number</p>
				<div style="width: 75%; margin: auto; padding: 20px; border: 2px solid #FFE2BA; background-color: #FFF; text-align: center; border-radius: 10px;">
					<font size="+2">{{ $Serial }}</font>
				</div>
				<p>Registration Key</p>
				<div style="width: 75%; margin: auto; padding: 20px; border: 2px solid #FFE2BA; background-color: #FFF; text-align: center; border-radius: 10px;">
					<font size="+2">{{ $Key }}</font>
				</div>
				<p><font size="-1">Note: This is valid only for the company {{ $Name }} and for product {{ $Product }} of {{ $Edition }} edition</font></p>
			</div>
			<div><p>All the details mentioned here are also available at customer's control panel at <a href="{{ Route('login') }}">{{ $Company["name"] }}</a></p></div>
			<div><p>Thanks for being a part of {{ $Company["name"] }}</p></div>
			<div><p>You can reach {{ $Company["name"] }} by email at {{ $Company["email"] }} or by phone at {{ $Company["phone"] }}</p></div>

@endsection
