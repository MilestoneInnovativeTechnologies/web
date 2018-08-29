@extends('emails.layout')
@section("content")

			<p>A Support ticket, <strong>{{ $Ticket->title }}</strong> have been created for customer, <strong>{{ $Ticket->Customer->name }}</strong>, on <u>{{ date('D d/M/y h:i A',strtotime($Ticket->created_at)) }}</u></p>
			<p>Ticket number generated is <strong>{{ $Ticket->code }}</strong></p>
			<p>Ticket details follow here,</p>
			@component('emails.specialbox')
				<table cellpadding="3" cellspacing="3"><tbody style="font-family: Segoe UI, Arial; font-size: 13px;">
					<tr><td>Ticket</td><td>:</td><td>{{ $Ticket->code }}</td></tr>
					<tr><td valign="top">Title</td><td valign="top">:</td><td valign="top">{{ $Ticket->title }}</td></tr>
					<tr><td valign="top">Description</td><td valign="top">:</td><td valign="top">{!! nl2br($Ticket->description) !!}</td></tr>
					<tr><td>Product</td><td>:</td><td>{{ $Ticket->Product->name }} {{ $Ticket->Edition->name }} Edition</td></tr>
					<tr><td>Category</td><td>:</td><td>{!! $CategoryBreadCrumb !!}</td></tr>
				</tbody></table>
			@endcomponent
			<p>Details of the customer follows here,</p>
			@component('emails.specialbox')
				<table cellpadding="3" cellspacing="3"><tbody style="font-family: Segoe UI, Arial; font-size: 13px;">@php $C = $Ticket->Customer @endphp
					<tr><th colspan="3" align="left">{{ $C->name }}</th></tr>
					<tr><td colspan="3">{{ $CAddress }}</td></tr>
					<tr><td>Email</td><td>:</td><td>{{ $C->Logins->implode('email',', ') }}</td></tr>
					<tr><td>Phone</td><td>:</td><td>+{{ $C->Details->phonecode }}-{{ $C->Details->phone }}</td></tr>
				</tbody></table>
			@endcomponent
			
@endsection