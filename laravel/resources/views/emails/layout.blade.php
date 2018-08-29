<div style="width: 100%; height: 35px; background-color: #354C79;"></div>
<div style="max-width: 480px; width: 70%; margin: auto; font-family: Segoe UI, Arial; font-size: 13px; text-align: justify">
	<div style="width: 220px; height: 50px; margin: 0px; padding: 10px 0px;"><img src="{{ $message->embed(storage_path('app/emails/MailLogo1.png')) }}"></div>
	<div style="background-color: #F7F7F7; padding: 10px;">
		@yield('content')
	</div>
</div>