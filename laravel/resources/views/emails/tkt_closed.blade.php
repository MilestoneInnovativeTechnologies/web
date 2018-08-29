@extends('emails.layout')
@include('emails.functions')
@section("content")

			<p>Hi {{ $Ticket->Customer->name }},</p>
			<p>The support ticket with title, <strong>{{ $Ticket->title }}</strong>, has been <span style="color: #004400;"><strong><u>CLOSED</u></strong></span> by <u>{{ $Ticket->Team->Team->name }}</u> on <u>{{ date('D d/M/y h:i A',$Ticket->Cstatus->start_time) }}</u></p>
			<p>Ticket details follow here,</p>
			@component('emails.specialbox')
				<table cellpadding="3" cellspacing="3"><tbody style="font-family: Segoe UI, Arial; font-size: 13px;">
					<tr><td>Ticket</td><td>:</td><td><strong>{{ $Ticket->code }}</strong></td></tr>
					<tr><td valign="top">Title</td><td valign="top">:</td><td valign="top">{{ $Ticket->title }}</td></tr>
					<tr><td valign="top">Description</td><td valign="top">:</td><td valign="top">{!! nl2br($Ticket->description) !!}</td></tr>
					<tr><td>Product</td><td>:</td><td>{{ $Ticket->Product->name }} {{ $Ticket->Edition->name }} Edition</td></tr>
					<tr><td>Category</td><td>:</td><td>{!! ($Ticket->category)?GetCategoryBreadCrumb($Ticket->Category):'none' !!}</td></tr>
					<tr><td>Opened on</td><td>:</td><td>{{ date('D d/M/y h:i A',strtotime($Ticket->created_at)) }}</td></tr>
					<tr><td>Closed on</td><td>:</td><td>{{ date('D d/M/y h:i A',$Ticket->Cstatus->start_time) }}</td></tr>
				</tbody></table>
			@endcomponent
			<p>You need to do a final process named as 'COMPLETE' on this ticket at you panel. Before COMPLETING your ticket, you are welcomed to provide a feedback, which will help us to serve you better next time.</p>
			<p>If it seems that your Ticket closed without solving the issue, you can REOPEN the ticket. FEEDBACK, REOPEN, COMPLETE all actions you can do at your login panel at <a href="{{ Route('login') }}">Milestone website</a></p>
			<p>If you have already logged in, follow the buttons below to navigate to appropriate page.</p>
			<p>{!! Button('FEEDBACK',Route('tkt.feedback',$Ticket->code)) !!} {!! Button('COMPLETE',Route('tkt.complete',$Ticket->code)) !!} {!! Button('REOPEN',Route('tkt.reopen',$Ticket->code)) !!}</p>
			<p>For further queries contact your support team at,</p>
			@component('emails.specialbox')
				<table cellpadding="3" cellspacing="3"><tbody style="font-family: Segoe UI, Arial; font-size: 13px;">@php $ST = $Ticket->Team->Team @endphp
					<tr><th colspan="3" align="left">{{ $ST->name }}</th></tr>
					<tr><td colspan="3">{{ D2A($ST->Details) }}</td></tr>
					<tr><td>Email</td><td>:</td><td>{{ $ST->Logins->implode('email',', ') }}</td></tr>
					<tr><td>Phone</td><td>:</td><td>+{{ $ST->Details->phonecode }}-{{ $ST->Details->phone }}</td></tr>
				</tbody></table>
			@endcomponent
			
@endsection
@php
function D2A($D){
	$AdrParts = [];
	if($D->address1) $AdrParts[] = $D->address1;
	if($D->address2) $AdrParts[] = $D->address2;
	if($D->city) {
		$AdrParts[] = $D->City->name;
		$AdrParts[] = $D->City->State->name;
		$AdrParts[] = $D->City->State->Country->name;
	}
	return implode(', ', $AdrParts);
}
function GetCategoryBreadCrumb($Obj){
	$BCAry = [];
	if($Obj) $BCAry[] = $Obj->name;
	if($Obj->parent) array_unshift($BCAry,GetCategoryBreadCrumb($Obj->Parent));
	return implode(" &raquo; ", $BCAry);
}

@endphp