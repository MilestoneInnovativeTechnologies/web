<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
@stack("meta")
<base href="/">
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-116616304-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-116616304-1');
</script>
<link rel="stylesheet" href="css/bootstrap.css" type="text/css">
<link rel="stylesheet" href="css/jquery-ui.min.css" type="text/css">
<link rel="stylesheet" href="css/theme.css" type="text/css">
<link rel="stylesheet" href="css/main.css?_v=20180712" type="text/css">
@stack("css")
<script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="js/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/bootstrap.js"></script>
<script type="text/javascript" src="js/common.js"></script>
@stack("js")
<title>@yield("title")</title>
</head>

<body>
	<nav class="navbar navbar-inverse navbar-fixed-top">
		@include("layouts/nav")
	</nav>
	<div class="top_spacer"></div>
	<div class="container">
		@if(isset($info) && $info)
		<div class="alert alert-{{ $type }}">
			{!! $text !!}
		</div>
		@endif

		@if(session()->has("info"))
		<div class="alert alert-{{ session('type') }}">
			{!! session('text') !!}
		</div>
		@endif

		@if(count($errors))
		<div class="alert alert-danger">
			<ul>
				@foreach($errors->all() as $error)
				<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
		@endif
		
		@yield('content')

		</div>
	<footer>
		@include("layouts/footer")		
	</footer>
</body>
</html>