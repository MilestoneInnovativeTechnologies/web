@extends('emails.layout')
@section("content")

			<p>Hi {{ $Email }}</p>
			<p>A request received to reset your Milestone control panel Logins associated with this account {{ $Email }}.</p>
			<p>Please follow the below link to reset your logins</p>
			@component('emails.specialbox')
				<a href="{{ Route('initlogin',['code'=>$Code]) }}" style="word-wrap: break-word">{{ Route('initlogin',['code'=>$Code]) }}</a>
			@endcomponent
			<p style="font-style: italic"><strong>Note:-</strong> If you don't require login reset, please ignore this email.</p>

@endsection