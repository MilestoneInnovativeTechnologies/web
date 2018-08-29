@extends('emails.layout')
@section("content")

			<p>Hi {{ $Name }},</p>
			<p>Your ticket <strong>{{ $Ticket->code }}</strong>, regarding <em><u>{{ $Ticket->title }}</u></em> has been closed on <span style="color:#880202">{{ date('d/M/y',$Ticket->Cstatus->start_time) }}</span> <small>({{ $Ago }})</small></p>
			<p>We request you to please make the status of the ticket to <em>COMPLETED</em> from your control panel at <a href="{{ Route('login') }}">{{ Route('login') }}</a>. You are also welcomed to provide any feedback about the support while changing the status</p>
			<p>Details of your ticket follows here</a></p>
			@component('emails.specialbox')
				<table cellpadding="3" cellspacing="3"><tbody style="font-family: Segoe UI, Arial; font-size: 13px;">
					<tr><td>Ticket</td><td>:</td><td>{{ $Ticket->code }}</td></tr>
					<tr><td valign="top">Title</td><td valign="top">:</td><td valign="top">{{ $Ticket->title }}</td></tr>
					<tr><td valign="top">Description</td><td valign="top">:</td><td valign="top">{{ $Ticket->description }}</td></tr>
					<tr><td>Created On</td><td>:</td><td>{{ date('d/M/y',strtotime($Ticket->created_at)) }}</td></tr>
					<tr><td>Closed on</td><td>:</td><td>{{ date('d/M/y',$Ticket->Cstatus->start_time) }}</td></tr>
				</tbody></table>
			@endcomponent
			<p>Please contact your support team for any further clarification.</p>
			<p>Support team details follows here</p>@php $Team = $Ticket->Team->Team @endphp
			@component('emails.specialbox')
				<p><strong>{{ $Team->name }}</strong></p>
				<strong>Email: </strong> {{ $Team->Logins[0]->email }}<br>
				<strong>Phone: </strong> +{{ $Team->Details->phonecode }}-{{ $Team->Details->phone }}
				</p>
			@endcomponent
			
@endsection