@extends('emails.layout')
@section("content")

			<p>Hi,</p>
			<p>A request received to send a download link of software, {{ $Model->name }}</p>
			<p><strong>Details</strong></p>
			@component('emails.specialbox')
				<table cellpadding="3" cellspacing="3"><tbody style="font-family: Segoe UI, Arial; font-size: 13px;">
					<tr><td>Name</td><td>:</td><td>{{ $Model->name }}</td></tr>
					<tr><td>Description</td><td>:</td><td>{!! nl2br($Model->description) !!}</td></tr>
					<tr><td>Version</td><td>:</td><td>{{ $Model->version }}</td></tr>
					<tr><td>Link</td><td>:</td><td style="word-wrap:break-word;"><a href="{{ $Url }}" style="word-wrap:break-word;">Download</a></td></tr>
				</tbody></table>
			@endcomponent

@endsection