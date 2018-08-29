@extends('emails.layout')
@section("content")

			<p><strong>Hi, Test Mail</strong></p>
			<p>Hi, Test Mail from MailTest Mailable.</p>
			@component('emails.specialbox')
				<table cellpadding="3" cellspacing="3"><tbody style="font-family: Segoe UI, Arial; font-size: 12px;">
				<tr><td nowrap valign="top">Name</td><th valign="top">:</th><td valign="top">Value</td></tr>
				</tbody></table>
			@endcomponent

@endsection