@extends('emails.layout')
@section("content")

			<p>Browse the below link to download Print Object.</p>
			@component('emails.specialbox')
			<a href="{{ Route('printobject.download',$Key) }}" style="word-wrap:break-word;">{{ Route('printobject.download',$Key) }}</a>
			@endcomponent
			<p>Details of print object.</p>
			@component('emails.specialbox')
				<table cellpadding="3" cellspacing="3"><tbody style="font-family: Segoe UI, Arial; font-size: 13px;">
					<tr><td>Product</td><td>:</td><td>{{ implode(" ",$CPO->Product) }} Edition</td></tr>
					<tr><td>Function name</td><td>:</td><td>{{ $CPO->function_name }}</td></tr>
					<tr><td>Print name</td><td>:</td><td>{{ $CPO->print_name }}</td></tr>
					<tr><td>Support User</td><td>:</td><td>{{ $CPO->User->name }}</td></tr>
					<tr><td>Date</td><td>:</td><td>{{ date('D d/M/y, h:i a',$CPO->time) }}</td></tr>
				</tbody></table>
			@endcomponent

@endsection