@extends('emails.layout')
@section("content")

			<div><p>Hi, {{ $Data["name"] }}</p></div>
			<div><p>You have been added as a Distributor for {{ $Data['parent']['name'] }}</p></div>
			<div><p>Details as per record follows here</p></div>
			<div style="width: 90%; margin: auto; padding: 20px; border: 1px solid #E2E2E2; background-color: #FFF4E4;">
				<strong>{{ $Data["name"] }}</strong>,<br>
				{{ $Data["address"] }}<br>
				{{ $Data["location"] }}<br>
				{{ $Data["country"] }}<br>
				Email: {{ $Data["email"] }}<br>
				Phone: {{ $Data["phone"] }}
			</div>
			<div><p>A control panel have been reserved for you at <a href="{{ Route('login') }}">Milestone Website</a>, where you can modify your details, manage customers, dealers etc</p></div>
			<div><p>Your email, <u>{{ $Data["email"] }}</u>, will be used as login name for your account.</p></div>
			<div><p>To activate and setting up your account, please follow the below link.</p></div>
			<div><a style="word-wrap: break-word;" href="{{ Route('initlogin',['code'	=>	$Data['login_key']]) }}">{{ Route('initlogin',['code'	=>	$Data['login_key']]) }}</a></div>
			<div><p>You are authorized to sell the following products in <u>{{ $Data["country"] }}</u></p></div>
			<div style="width: 90%; margin: auto; padding: 20px; border: 1px solid #E2E2E2; background-color: #FFF4E4;">
				<table style="width: 100%; font-size: 14px" border="0" cellpadding="5" cellspacing="5">
					<tr><th width="50%" style="text-align: left;">Product</th><th style="text-align: left">Edition</th></tr>
					@foreach($Data["products"] as $Product)
					<tr><td>{{ $Product[0] }}</td><td>{{ $Product[1] }}</td></tr>
					@endforeach
				</table>
			</div>
			<div><p>For more informations, please email {{ $Data['parent']['name'] }} at <u>{!! $Data['parent']['email'] !!}</u> or call {{ $Data['parent']['phone'] }}</p></div>
			<div><p>Once again thanking you for being a part of {{ $Data['parent']['name'] }}.</p></div>

@endsection