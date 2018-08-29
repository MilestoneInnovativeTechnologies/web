@extends('emails.layout')
@section("content")
@php $Company = \App\Models\Company::first() @endphp

			<p>Hi {{ $Name }},</p>
			<p>Your Maintenance contract <strong>{{ $Code }}</strong>, for the product <strong>{{ $Product }}</strong>, is expired on <strong>{{ $EndDate }}</strong></p>
			<p>Please renew immediately to enjoy our seamless support.</p>
			<p>Click here to <a href="{{ Route('mc.renew_req') }}" target="_blank">raise a renewal request</a></p>
			<p>For more information on renewal, contact at</p>
			@component('emails.specialbox')
				<p><strong>{{ $Company->name }}</strong></p>
				<p>{!! implode('<br>',[implode(', ',array_slice($Company->address,0,2)),implode(', ',array_slice($Company->address,2,2)),$Company->address[4]]) !!}<br>
				<strong>Email: </strong> {{ $Company->email }}<br>
				<strong>Phone: </strong> {{ $Company->phone }}
				</p>
			@endcomponent
			<p>Your current Contract details follows here,</p>
			@component('emails.specialbox')
				<p><strong>Contract: </strong>{{ $Code }}</p>
				<p><strong>Start Date: </strong>{{ $StartDate }}</p>
				<p><strong>End Date: </strong>{{ $EndDate }}</p>
				<p>For more contract details, visit <a href="{{ Route('mc.details') }}" target="_blank">{{ Route('mc.details') }}</a></p>
			@endcomponent
			
@endsection