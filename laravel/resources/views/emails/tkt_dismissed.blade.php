@extends('emails.layout')
@section("content")

			<p>The Support ticket, <strong>{{ $Ticket->title }}</strong>, which has been opened on <u>{{ date('D d/M/y h:i A',strtotime($Ticket->created_at)) }}</u> by <u>{{ $Ticket->CreatedBy->name }}</u> have been <strong style="color: #780000">DISMISSED</strong></p>
			<p><strong>Dismissal Reason</strong></p>
			@component('emails.specialbox')
				<p>{!! nl2br($Ticket->Cstatus->status_text) !!}</p>
			@endcomponent
			<p>Ticket details follow here,</p>
			@component('emails.specialbox')
				<table cellpadding="3" cellspacing="3"><tbody style="font-family: Segoe UI, Arial; font-size: 13px;">
					<tr><td>Ticket</td><td>:</td><td>{{ $Ticket->code }}</td></tr>
					<tr><td>Product</td><td>:</td><td>{{ $Ticket->Product->name }} {{ $Ticket->Edition->name }} Edition</td></tr>
					<tr><td valign="top">Title</td><td valign="top">:</td><td valign="top">{{ $Ticket->title }}</td></tr>
					<tr><td valign="top">Description</td><td valign="top">:</td><td valign="top">{!! nl2br($Ticket->description) !!}</td></tr>
					<tr><td>Category</td><td>:</td><td>{!! ($Ticket->category)?GetCategoryBreadCrumb($Ticket->Category):'none' !!}</td></tr>
					<tr><td>Opened on</td><td>:</td><td>{{ date('D d/M/y h:i A',strtotime($Ticket->created_at)) }}</td></tr>
					<tr><td>Dismissed on</td><td>:</td><td>{{ date('D d/M/y h:i A',$Ticket->Cstatus->start_time) }}</td></tr>
				</tbody></table>
			@endcomponent
			<p>Details of Support Team,</p>
			@component('emails.specialbox')
				<table cellpadding="3" cellspacing="3"><tbody style="font-family: Segoe UI, Arial; font-size: 13px;">@php $ST = $Ticket->Team->Team @endphp
					<tr><th colspan="3" align="left">{{ $ST->name }}</th></tr>
					<tr><td colspan="3">{{ D2A($ST->Details) }}</td></tr>
					<tr><td>Email</td><td>:</td><td>{{ $ST->Logins->implode('email',', ') }}</td></tr>
					<tr><td>Phone</td><td>:</td><td>+{{ $ST->Details->phonecode }}-{{ $ST->Details->phone }}</td></tr>
				</tbody></table>
			@endcomponent
			<p>You are requested to contact your support team for any further clarification.</p>
			
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