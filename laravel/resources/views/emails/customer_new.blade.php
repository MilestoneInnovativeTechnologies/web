@extends('emails.layout')
@section("content")

			<div><p>Dear {{ $Data["name"] }}</p></div>
			<div><p>Thank you for being a part of Milestone Innovative Technologies.<br>You are using our product <u>{{ $Data["product"] }}</u> of edition <u>{{ $Data["edition"] }}</u>.<br>We value your relationship with us, so we are dedicated to serve you all time.</p></div>
			<div><p>Here is your Distributor Details</p></div>
			
			<div>
				<div style="width: 90%; height: 9px; margin: auto"><img src="{{ $message->embed(storage_path('app/emails/ContentTopBorder.jpg')) }}" width="100%" height="9"></div>
				<div style="width: 90%; margin: auto; padding: 20px">
					<strong>{{ $Data["parent"]["name"] }}</strong>,<br>
					{{ $Data["parent"]["address"] }}<br>
					{{ $Data["parent"]["location"] }}<br>
					{{ $Data["parent"]["country"] }}<br>
					Email: {{ $Data["parent"]["email"] }}<br>
					Phone: {{ $Data["parent"]["phone"] }}
				</div>
				<div style="width: 90%; height: 10px; margin: auto"><img src="{{ $message->embed(storage_path('app/emails/ContentBottomBorder.jpg')) }}" width="100%" height="10"></div>
			</div>
			<div style=""><p>You may please keep contact with your distributor for further support</p></div>
			<div><span>You are even welcomed to contact company at <span style="font-family: monospace;">support@milestoneit.net</span> at any time.</span></div>
			<div style="width: 90%; border: 1px solid #E2E2E2; background-color: #FFF; margin-top: 30px; padding: 20px">
				<p style="margin-bottom: 0px;">We also created a web login for you at <a href="{{ Route('login') }}">Milestone Website</a><br>
				where you can manage your
				<ul style="margin: 0px; padding-left: 20px; list-style: circle"><li>Products</li><li>Updates</li><li>Upgrades</li><li>Registrations</li><li>... and many more</li></ul>
				</p>
				<p>
					<div style="font-family: monospace; font-size: 15px; padding: 20px; background-color: #fff4e4">
						<strong style="line-height: 30px;">Your email, <u>{{ $Data["email"] }}</u>, will be used as login name.</strong>
						<p>Please follow the below link to activate your account</p>
						<p><a style="word-wrap: break-word;" href="{{ Route('initlogin',['code'	=>	$Data['login_key']]) }}">{{ Route('initlogin',['code'	=>	$Data['login_key']]) }}</a></p>
						<p style="font-size: 10px;"><strong>Note: </strong>You will be redirected to set a new password for your login name, inorder to activate your account.</p>
					</div>
				</p>
			</div>
			<div><p>Once again thanking you for being a part of Milestone Innovative Technologies.</p></div>

@endsection