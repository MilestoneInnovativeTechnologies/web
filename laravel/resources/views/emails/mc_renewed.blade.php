@extends('emails.layout')
@section("content")
@php $Company = \App\Models\Company::first(); @endphp

			<p>Hi {{ $Name }},</p>
			<p>Your Maintenance contract <strong>{{ $OldCode }}</strong>, for the product <strong>{{ $Product }}</strong>, have been renewed. The expiry date of old contract is <em>{{ $OldExpireDate }}</em></p>
			<p>Details of the new contract follows here,</p>
			@component('emails.specialbox')
				<p><strong>Contract: </strong>{{ $Code }}</p>
				<p><strong>Start Date: </strong>{{ $StartDate }}</p>
				<p><strong>End Date: </strong>{{ $EndDate }}</p>
				<p>For more details, visit <a href="{{ Route('mc.details') }}" target="_blank">{{ Route('mc.details') }}</a></p>
			@endcomponent
			<p>Kindly continue enjoying the seamless support from {{ $Company->name }}</p>
			<p>For further details, visit <a href="{{ Route('home') }}">website</a>, or contact at</p>
			@component('emails.specialbox')
				<p><strong>{{ $Company->name }}</strong></p>
				<p>{!! implode('<br>',[implode(', ',array_slice($Company->address,0,2)),implode(', ',array_slice($Company->address,2,2)),$Company->address[4]]) !!}<br>
				<strong>Email: </strong> {{ $Company->email }}<br>
				<strong>Phone: </strong> {{ $Company->phone }}
				</p>
			@endcomponent
			<p>Thanks for being a part of Milestone family.</p>
			
@endsection