@extends('emails.layout')
@section("content")

			<p>Hi {{ $SR->Supportteam->name }}</p>
			<p>A new service request has been created by {{ $SR->User->name }}, <small>({{ $SR->User->Roles->implode('displayname',', ') }})</small>, at {{ date('d/M/y h:i a',$SR->user_time) }}.</p>
			<p><strong>Message</strong></p>
			@component('emails.specialbox')
			<p>{!! nl2br($SR->message) !!}</p>
			@endcomponent

@endsection