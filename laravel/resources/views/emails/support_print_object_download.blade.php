@extends('emails.layout')
@include('emails.functions')
@section("content")

			<p>Hi {{ $Name }},</p>
			<p>As per the request to download Print Object for your Product, {{ $Product }}, here is the details and download links.</p>
			@component('emails.specialbox')
				<p><strong>Function Name: </strong>{{ $FName }}</p>
				<p><strong>Function Code: </strong>{{ $FCode }}</p>
				<p><strong>Approved Time: </strong>{{ $Time }}</p>
				<p><strong>Approved User: </strong>{{ $User }}</p>
				<p>{!! Button('Download', $Link) !!}</p>
				<p><small><a href="{{ $Link }}" style="word-wrap: break-word">{{ $Link }}</a></small></p>
			@endcomponent

@endsection