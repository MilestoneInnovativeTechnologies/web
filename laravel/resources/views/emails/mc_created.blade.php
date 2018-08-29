@extends('emails.layout')
@section("content")
@php $Company = \App\Models\Company::first(); @endphp

			<p>Hi {{ $Name }},</p>
			<p>A new Maintenance contract has been created for you for the product <b>{{ $Product }}</b></p>
			<p>Contract details follow here,</p>
			@component('emails.specialbox')
				<table cellpadding="5" cellspacing="5"><tbody>
					<tr><td align="left">Contract</td><td>{{ $Code }}</td></tr>
					<tr><td align="left">Start Date</td><td>{{ $StartDate }}</td></tr>
					<tr><td align="left">End Date</td><td>{{ $EndDate }}</td></tr>
				</tbody></table>
				<p>For more details, visit <a href="{{ Route('mc.details') }}" target="_blank">{{ Route('mc.details') }}</a></p>
			@endcomponent
			<p>Kindly start enjoying the seamless support from {{ $Company->name }}</p>
			<p>For further details, visit <a href="{{ Route('home') }}">website</a> or contact at</p>
			@component('emails.specialbox')
				<table cellpadding="5" cellspacing="5"><tbody>
					<tr><th colspan="2" align="left">{{ $Company->name }}</th></tr>
					<tr><td colspan="2" align="left">{!! implode('<br>',[implode(', ',array_slice($Company->address,0,2)),implode(', ',array_slice($Company->address,2,2)),$Company->address[4]]) !!}</td></tr>
					<tr><td align="left">Email</td><td>{{ $Company->email }}</td></tr>
					<tr><td align="left">Phone</td><td>{{ $Company->phone }}</td></tr>
				</tbody></table>
			@endcomponent
			<p>Thanks for being a part of Milestone family.</p>
			
@endsection