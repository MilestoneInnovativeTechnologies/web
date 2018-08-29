@extends('emails.layout')
@section("content")

			<p>The support ticket, <strong>{{ $Ticket->title }}</strong>, has been marked as <em style="color:#0F5E00; font-weight: 900">COMPLETED</em> by {{ $Ticket->Customer->name }} on <u>{{ date('D d/M/y h:i A',strtotime($Ticket->Cstatus->created_at)) }}</u></p>
			<p>Ticket details follow here,</p>
			@component('emails.specialbox')
				<table cellpadding="3" cellspacing="3"><tbody style="font-family: Segoe UI, Arial; font-size: 13px;">
					<tr><td>Ticket</td><td>:</td><td>{{ $Ticket->code }}</td></tr>
					<tr><td>Product</td><td>:</td><td>{{ $Ticket->Product->name }} {{ $Ticket->Edition->name }} Edition</td></tr>
					<tr><td valign="top">Title</td><td valign="top">:</td><td valign="top">{{ $Ticket->title }}</td></tr>
					<tr><td valign="top">Description</td><td valign="top">:</td><td valign="top">{!! nl2br($Ticket->description) !!}</td></tr>
					<tr><td>Category</td><td>:</td><td>{!! ($Ticket->category)?GetCategoryBreadCrumb($Ticket->Category):'none' !!}</td></tr>
					<tr><td>Opened on</td><td>:</td><td>{{ date('D d/M/y h:i A',strtotime($Ticket->created_at)) }}</td></tr>
					<tr><td>Closed on</td><td>:</td><td>{{ date('D d/M/y h:i A',$Ticket->Status->last(function($Item,$Key){ return $Item->status == 'CLOSED'; })->start_time) }}</td></tr>
					<tr><td>Completed on</td><td>:</td><td>{{ date('D d/M/y h:i A',strtotime($Ticket->Cstatus->created_at)) }}</td></tr>
					<tr><td>Feedback Provided</td><td>:</td><td><strong>{{ ($Ticket->Feedback)?'YES':'NO' }}</strong></td></tr>
				</tbody></table>
			@endcomponent
			<p>Customer details follows here,</p>
			@component('emails.specialbox')
				<table cellpadding="3" cellspacing="3"><tbody style="font-family: Segoe UI, Arial; font-size: 13px;">@php $P = $Ticket->Customer @endphp
					<tr><th colspan="3" align="left">{{ $P->name }}</th></tr>
					<tr><td colspan="3">{{ D2A($P->Details) }}</td></tr>
					<tr><td>Email</td><td>:</td><td>{{ $P->Logins->implode('email',', ') }}</td></tr>
					<tr><td>Phone</td><td>:</td><td>+{{ $P->Details->phonecode }}-{{ $P->Details->phone }}</td></tr>
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