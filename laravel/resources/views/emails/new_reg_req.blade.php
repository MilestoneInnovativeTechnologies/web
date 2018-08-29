@extends('emails.layout')
@section("content")

			<div><p>New Registration Request Received for Product, <strong>{{ $ProductName }}</strong></p></div>
			<div><p>Requisition Code: <strong><u>{{ $Requisition }}</u></strong></p></div>
			<div><p>Details of the customer,</p></div>
			@component('emails.specialbox')
				<strong>{{ $CustomerName }}</strong><br>
				{!! $CustomerAddress !!}<br>
				Industry: {{ $CustomerIndustry }}<br>
				Email: {{ $CustomerEmail }}<br>
				Phone: {{ $CustomerPhone }}
			@endcomponent
			<div><p>This customer belongs to the distributor, <strong>{{ $DistributorName }}</strong></p></div>
			<div><p style="font-size: 10px"><u>Note: Licence File Attached</u></p></div>

@endsection