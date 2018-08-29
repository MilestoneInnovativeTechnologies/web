@extends('emails.layout')
@section("content")

			<p>Hi {{ $Name }}</p>
			<p>A request received to reset your Milestone control panel Logins associated with the account {{ $Email }}.</p>
			<p>Please follow the below link to reset your logins</p>
			@component('emails.specialbox')
				<a href="{{ Route('initlogin',['code'=>$Code]) }}" style="word-wrap: break-word">{{ Route('initlogin',['code'=>$Code]) }}</a>
			@endcomponent
			<p style="font-style: italic"><strong>Note:-</strong> If you don't require login reset, please ignore this email.</p>
			<p>Please contact company for any further assistance. Company details follow here,</p>
			@component('emails.specialbox')
				<strong>{{ $Company }}</strong><br><br>
				<strong>Email : </strong>{{ $CompanyEmail }}<br>
				<strong>Phone : </strong>{{ $CompanyPhone }}<br>
			@endcomponent

@endsection