@extends('emails.layout')
@section("content")

			<div><p>Hi,</p></div>
			<div><p>We are much obliged to inform you that the whole new release of {{ $Product }} {{ $Edition }} Edition has been updated with version number {{ $Version }}</p></div>
			<div><p>You are kindly requested to download the latest update to acquire our new upgrades and provisions included in the software currently. You can directly download updated version of {{ $Product }} {{ $Edition }} Edition from the <a href="{{ Route('software.update.download',['key'	=>	$MailDownloadKey]) }}">download link</a></p></div>
			<div><p>We higly recommended to use the latest version of {{ $Product }} {{ $Edition }} Edition for a much advanced user friendly interface.</p></div>
			<div><p>You are receiving this message from the Companies development team, to provide you with the best software experience is our priority.</p></div>
			<div><p>For login details, current status and further details visit <a href="{{ Route('home') }}">website</a></p></div>

@endsection