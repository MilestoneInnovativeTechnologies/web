@extends('emails.layout')
@section("content")
@php $Company = \App\Models\Company::first() @endphp

			<p>Hi {{ $Name }},</p>
			<p>You doesn't have any active Maintenance Contracts for your product <strong>{{ $Product }}</strong></p>
			<p>For getting seamless support from company, you are requested to sign contract with {{ $Company->name }}</p>
			<p>Click here to raise a request for <a href="{{ Route('mc.contract_req') }}" target="_blank">contract</a>, or contact company for details.</p>
			@component('emails.specialbox')
				<p><strong>{{ $Company->name }}</strong></p>
				<p>{!! implode('<br>',[implode(', ',array_slice($Company->address,0,2)),implode(', ',array_slice($Company->address,2,2)),$Company->address[4]]) !!}<br>
				<strong>Email: </strong> {{ $Company->email }}<br>
				<strong>Phone: </strong> {{ $Company->phone }}
				</p>
			@endcomponent
			
@endsection