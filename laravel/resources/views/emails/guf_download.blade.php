@extends('emails.layout')
@section("content")

			<p>Browse the below link to download uploaded file</p>
			@component('emails.specialbox')
			<a href="{{ $gu->download }}" style="word-wrap:break-word;">{{ $gu->download }}</a>
			@endcomponent
			<p>Details of the General Form.</p>
			@component('emails.specialbox')
				<table cellpadding="3" cellspacing="3"><tbody style="font-family: Segoe UI, Arial; font-size: 13px;">
					<tr><td>Name</td><td>:</td><td>{{ $gu->name }}</td></tr>
					<tr><td>Description</td><td>:</td><td>{!! nl2br($gu->description) !!}</td></tr>
					<tr><td>File Uploaded on</td><td>:</td><td>{{ date('D d/M/y, h:i a',$gu->time) }}</td></tr>
				</tbody></table>
			@endcomponent

@endsection