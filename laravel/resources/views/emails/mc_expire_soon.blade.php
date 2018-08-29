@extends('emails.layout')
@section("content")
@php $Company = \App\Models\Company::first() @endphp

			<p>Hi {{ $Name }},</p>
			<p>Your Maintenance contract <strong>{{ $Code }}</strong>, for the product <strong>{{ $Product }}</strong>, is about to expire on <strong>{{ $EndDate }}</strong></p>
			<p>Please renew the contract before it get expired, to enjoy our uninterrupted services</p>
			<p>Contract details follows here,</p>
			@component('emails.specialbox')
				<p><strong>Contract: </strong>{{ $Code }}</p>
				<p><strong>Start Date: </strong>{{ $StartDate }}</p>
				<p><strong>End Date: </strong>{{ $EndDate }}</p>
				<p>For more details, visit <a href="{{ Route('mc.details') }}" target="_blank">{{ Route('mc.details') }}</a></p>
			@endcomponent
			<p>Please contact {{ $Company->name }}, or click here to <a href="{{ Route('mc.renew_req') }}" target="_blank">raise a renewal request</a></p>
			<p>For further details contact at</p>
			@component('emails.specialbox')
				<p><strong>{{ $Company->name }}</strong></p>
				<p>{!! implode('<br>',[implode(', ',array_slice($Company->address,0,2)),implode(', ',array_slice($Company->address,2,2)),$Company->address[4]]) !!}<br>
				<strong>Email: </strong> {{ $Company->email }}<br>
				<strong>Phone: </strong> {{ $Company->phone }}
				</p>
			@endcomponent
			
@endsection