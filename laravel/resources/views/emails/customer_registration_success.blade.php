@extends('emails.layout')
@section("content")

			<div><p>The registration request of product, <strong>{{ $Software }}</strong>, for customer, <strong>{{ $Name }}</strong>, has been approved.</p></div>
			<div><p>Here follows the registration details</p></div>
			<div style="width: 90%; margin: auto; padding: 20px; border: 1px solid #E2E2E2; background-color: #FFF4E4; text-align: center;">
				<p>Serial Number</p>
				<div style="width: 75%; margin: auto; padding: 20px; border: 2px solid #FFE2BA; background-color: #FFF; text-align: center; border-radius: 10px;">
					<font size="+1" style="font-weight: 900">{{ $Serial }}</font>
				</div>
				<p>Registration Key</p>
				<div style="width: 75%; margin: auto; padding: 20px; border: 2px solid #FFE2BA; background-color: #FFF; text-align: center; border-radius: 10px;">
					<font size="+1" style="font-weight: 900">{{ $Key }}</font>
				</div>
				<p><font size="-1">Note: This is valid only for the company <strong>{{ $Name }}</strong> and for product <strong>{{ $Product }} {{ $Edition }} Edition</strong></font></p>
			</div>
			<div><p>All the details mentioned here are also available at your control panel at <a href="{{ Route('login') }}">{{ $Company["name"] }}</a></p></div>
			<div><p>You are requested to contact your distributor for further details.<br>Distributor details follows here.</p></div>
			<div style="width: 90%; margin: auto; padding: 20px; border: 1px solid #E2E2E2; background-color: #FFF4E4;">
				<strong>{{ $Distributor["name"] }}</strong><br>
				{{ $Distributor["address"] }}<br>
				{{ $Distributor["location"] }}<br>
				{{ $Distributor["country"] }}<br><br>
				Email: {{ $Distributor["email"] }}<br>
				Phone: {{ $Distributor["phone"] }}
			</div>
			<div><p>Thanks for being a member of {{ $Company["name"] }}</p></div>
			<div><p>You can reach {{ $Company["name"] }} by email at {{ $Company["email"] }} or by phone at {{ $Company["phone"] }}</p></div>

@endsection
