@extends('emails.layout')
@section("content")

			<div><p>Hi {{ $Name }}</p></div>
			<div><p>There is some modification in the products you are authorized to sell</p></div>
			<div><p>Here follows the current product details</p></div>
			<div style="width: 90%; margin: auto; padding: 20px; border: 1px solid #E2E2E2; background-color: #FFF4E4;">
				<table style="width: 100%; font-size: 14px" border="0" cellpadding="5" cellspacing="5">
					<tr><th width="30%" style="text-align: left;">Product</th><th width="30%" style="text-align: left">Edition</th><th width="15%" style="text-align: left">Price</th></tr>
					@foreach($Products as $Product)
					<tr><td>{{ $Product[0] }}</td><td>{{ $Product[1] }}</td><td>{{ $Product[3] }}</td></tr>
					@endforeach
				</table>
				<p style="font-size: 10px;"><strong>Note: </strong>All the prices listed here are in {{ $Currency }}</p>
			</div>
			<div><p>For more informations, please email {{ $Company['name'] }} at <u>{!! $Company['email'] !!}</u> or call {{ $Company['phone'] }}</p></div>

@endsection