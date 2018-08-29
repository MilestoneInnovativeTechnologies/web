@extends('emails.layout')
@section("content")

			<p>Hi {{ $Name }}</p>
			<p>As per the request received, for Resetting your Milestone account password associated with this email address ({{ $Email }}), We are sending you the Password Reset Link</p>
			<!--<p>Your distributor, {{ $Distributor }}, have requested for the Login Reset for your account associated with this email address ({{ $Email }}).</p>-->
			<p>Please follow the below link to reset your logins</p>
			@component('emails.specialbox')
				<a href="{{ Route('initlogin',['code'=>$Code]) }}" style="word-wrap: break-word">{{ Route('initlogin',['code'=>$Code]) }}</a>
			@endcomponent
			<p style="font-style: italic"><strong>Note:-</strong> If you don't require login reset, please ignore this email.</p>
			<p>Please contact distributor for any further assistance. Distributor details follow here,</p>
			@component('emails.specialbox')
				<strong>{{ $Distributor }}</strong><br><br>
				<strong>Email : </strong>{{ $DistributorEmail }}<br>
				<strong>Phone : </strong>{{ $DistributorPhone }}<br>
			@endcomponent

@endsection