@extends('emails.layout')
@section("content")

			<p>Hi {{ $Ticket->Customer->name }},</p>
			<p>A Support ticket have been created for you by <strong>{{ $Ticket->CreatedBy->name }}</strong> <small>(Support Team)</small>, on <u>{{ date('D d/M/y h:i A',strtotime($Ticket->created_at)) }}</u></p>
			<p>Ticket title is <strong>{{ $Ticket->title }}</strong></p>
			<p>Ticket details follow here,</p>
			@component('emails.specialbox')
				<table cellpadding="3" cellspacing="3"><tbody style="font-family: Segoe UI, Arial; font-size: 13px;">
					<tr><td>Ticket</td><td>:</td><td><strong>{{ $Ticket->code }}</strong></td></tr>
					<tr><td valign="top">Title</td><td valign="top">:</td><td valign="top">{{ $Ticket->title }}</td></tr>
					<tr><td valign="top">Description</td><td valign="top">:</td><td valign="top">{!! nl2br($Ticket->description) !!}</td></tr>
					<tr><td>Product</td><td>:</td><td>{{ $Ticket->Product->name }} {{ $Ticket->Edition->name }} Edition</td></tr>
					<tr><td>Category</td><td>:</td><td>{!! $CategoryBreadCrumb !!}</td></tr>
				</tbody></table>
			@endcomponent
			<p>You can track the status or contact your support team via web chat on <em>Support Tickets</em> tab at your panel. Please <a href="{{ Route('login') }}">login</a> to access your panel.</p>
			<p>Details of your Support Team follows here,</p>
			@component('emails.specialbox')
				<table cellpadding="3" cellspacing="3"><tbody style="font-family: Segoe UI, Arial; font-size: 13px;">@php $ST = $Ticket->Team->Team @endphp
					<tr><th colspan="3" align="left">{{ $ST->name }}</th></tr>
					<tr><td colspan="3">{{ $STAddress }}</td></tr>
					<tr><td>Email</td><td>:</td><td>{{ $ST->Logins->implode('email',', ') }}</td></tr>
					<tr><td>Phone</td><td>:</td><td>+{{ $ST->Details->phonecode }}-{{ $ST->Details->phone }}</td></tr>
				</tbody></table>
			@endcomponent
			
@endsection