@extends('emails.layout')
@include('emails.functions')
@section("content")

			<p>Hi {{ $Name }},</p>
			<p>While dealing with Support Ticket <u>{{ $Ticket }}</u>, {{ $Team }} have sent you the download links and details of {{ $FullProduct }}'s {{ $Package }} package.</p>
			@component('emails.specialbox')
				<p><strong>Version: </strong>{{ $Version }}</p>
				<p>{!! Button('Download '.$FullProduct.' '. $Package, $Link) !!}</p>
				<p><small><a href="{{ $Link }}" style="word-wrap: break-word">{{ $Link }}</a></small></p>
			@endcomponent
			<p><strong><u>About {{ $Product }}</u></strong></p>
			<p>{{ $ProductDetails }}</p>
			<p><strong><u>About {{ $Product }} {{ $Edition }} Edition</u></strong></p>
			<p>{{ $EditionDetails }}</p>
			@component('emails.specialbox')
				<p><strong>Version: </strong>{{ $Version }}</p>
				<p>{!! Button('Download '.$FullProduct, $Link) !!}</p>
				<p><small><a href="{{ $Link }}" style="word-wrap: break-word">{{ $Link }}</a></small></p>
			@endcomponent

@endsection