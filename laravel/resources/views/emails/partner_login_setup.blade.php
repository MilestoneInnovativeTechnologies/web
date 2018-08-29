@extends('emails.layout')
@section("content")

			<p>Hi {{ $Name }}</p>
			<p>You have a control panel existing at Milestone website</p>
			<p>Please follow the below link to set new password and start using your panel</p>
			@component('emails.specialbox')
				<a href="{{ Route('initlogin',['code'=>$Code]) }}" style="word-wrap: break-word">{{ Route('initlogin',['code'=>$Code]) }}</a>
			@endcomponent
			<p>The username for the panel is {{ $Email }}.</p>
			<p style="font-style: italic"><strong>Note:-</strong> If you have already started using this panel, or doesn't requested for such an action, kindly ingore this mail.</p>

@endsection