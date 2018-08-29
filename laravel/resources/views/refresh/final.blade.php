<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Refresh</title>
<base href="/">
@include('inc.favicon')
@include('BladeFunctions')
<script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="js/bootstrap.js"></script>
<link type="text/css" rel="stylesheet" href="css/bootstrap.css">

</head>

<body><div class="container" style="margin-top: 60px;">
	<div class="jumbotron text-center">
		<h1>You are all set.</h1>
		<p>If you have any Triggers or Functions to be there on Database, Create it now manually, then follow the below link to set new password for your account and start using :)</p>
		{!! eleClass('a','btn btn-success',Route('initlogin',['code'	=>	$Key]),'href="'.Route('initlogin',['code'	=>	$Key]).'"') !!}
	</div>
</div></body>
</html>