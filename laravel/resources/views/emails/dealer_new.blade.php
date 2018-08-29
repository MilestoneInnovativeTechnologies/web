@extends('emails.layout')
@section("content")

			<div><p>Congratulations, {{ $Data["name"] }}</p></div>
			<div><p>You have been added as <u>Dealer</u> for the Distributor, {{ $Data["parent"]["name"] }}</p></div>
			<div><p>Here is your Details provided by <strong>{{ $Data["parent"]["name"] }}</strong></p></div>
			<div style="width: 90%; margin: auto; padding: 20px; border: 1px solid #E2E2E2; background-color: #FFF4E4;">
				<strong>{{ $Data["name"] }}</strong>,<br>
				{{ $Data["address"] }}<br>
				{{ $Data["location"] }}<br>
				{{ $Data["country"] }}<br>
				Email: {{ $Data["email"] }}<br>
				Phone: {{ $Data["phone"] }}
			</div>
			<div><p>And your Distributor details follows here</p></div>
			<div style="width: 90%; margin: auto; padding: 20px; border: 1px solid #E2E2E2; background-color: #FFF4E4;">
				<strong>{{ $Data["parent"]["name"] }}</strong>,<br>
				{{ $Data["parent"]["address"] }}<br>
				{{ $Data["parent"]["location"] }}<br>
				{{ $Data["parent"]["country"] }}<br>
				Email: {{ $Data["parent"]["email"] }}<br>
				Phone: {{ $Data["parent"]["phone"] }}
			</div>
			<div><p>You have a control panel reserved at Milestone website, where you can modify your details, manage your customers etc</p></div>
			<div><p>Please follow the below link to activate and setting password for your account</p></div>
			<div><a style="word-wrap: break-word;" href="{{ Route('initlogin',['code'	=>	$Data['login_key']]) }}">{{ Route('initlogin',['code'	=>	$Data['login_key']]) }}</a></div>
			<div><p>Your email, <u>{{ $Data["email"] }}</u>, will be used as username for login.</p></div>
			<div><p>You are authorized to sell the following products</p></div>
			<div style="width: 90%; margin: auto; padding: 20px; border: 1px solid #E2E2E2; background-color: #FFF4E4;">
				<table style="width: 100%; font-size: 14px" border="0" cellpadding="5" cellspacing="5">
					<tr><th width="50%" style="text-align: left;">Product</th><th style="text-align: left">Edition</th></tr>
					@foreach($Data["products"] as $Product)
					<tr><td>{{ $Product[0] }}</td><td>{{ $Product[1] }}</td></tr>
					@endforeach
				</table>
			</div>
			<div><p>Once again thanking you for being a part of Milestone Innovative Technologies.</p></div>

@endsection
