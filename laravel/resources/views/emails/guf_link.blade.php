@extends('emails.layout')
@section("content")

			<p>Browse the below link to upload a file using general form</p>
			@component('emails.specialbox')
			<a href="{{ $gu->form }}" style="word-wrap:break-word;">{{ $gu->form }}</a>
			@endcomponent
			<p>Details of the General Form.</p>
			@component('emails.specialbox')
				<table cellpadding="3" cellspacing="3"><tbody style="font-family: Segoe UI, Arial; font-size: 13px;">
					<tr><td>Name</td><td>:</td><td>{{ $gu->name }}</td></tr>
					<tr><td>Description</td><td>:</td><td>{!! nl2br($gu->description) !!}</td></tr>
					<tr><td>Overwritable</td><td>:</td><td>{{ (['Y'=>'Yes','N'=>'No'])[$gu->overwrite] }}</td></tr>
				</tbody></table>
			@endcomponent

@endsection