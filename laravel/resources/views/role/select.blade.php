@extends("role.page")
@section("content")

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><h2>Select a Role to continue</h2></div>
		<div class="panel-body">
			@foreach($Roles as $k => $RoleObj)
			{!! ($k)?'<hr>':'' !!}
			<div class="row">
				<div class="col col-md-4"></div>
				<div class="col col-md-4">
					<a href="{{ Route('roleselect',['role' => $RoleObj["code"]]) }}"><div class="jumbotron text-center" style="margin-bottom: 0px">
						<h4>{{ $RoleObj["displayname"] }}</h4>
						<small>{{ $RoleObj["description"] }}</small>
					</div></a>
				</div>
				<div class="col col-md-4"></div>
			</div>
			@endforeach
		</div>
	</div>
</div>
	
@endsection
{{--

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
@stack("meta")
<base href="/inside/public/">
<link rel="stylesheet" href="css/bootstrap.css" type="text/css">
<link rel="stylesheet" href="css/theme.css" type="text/css">
<link rel="stylesheet" href="css/main.css" type="text/css">
@stack("css")
<script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="js/bootstrap.js"></script>
@stack("js")
<title>@yield("title")</title>
</head>

<body>
	<nav class="navbar navbar-inverse navbar-fixed-top">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="#">MILESTONE</a>
			</div>
		</div>
	</nav>
	<div class="top_spacer"></div>
	<div class="container">
		<div class="panel panel-default">
			<div class="panel-heading"><h2>Select a Role to continue</h2></div>
			<div class="panel-body">
				@foreach($Roles as $k => $RoleObj)
				{!! ($k)?'<hr>':'' !!}
				<div class="row">
					<div class="col col-md-4"></div>
					<div class="col col-md-4">
						<a href="{{ Route('roleselect',['role' => $RoleObj["code"]]) }}"><div class="jumbotron text-center" style="margin-bottom: 0px">
							<h4>{{ $RoleObj["displayname"] }}</h4>
							<small>{{ $RoleObj["description"] }}</small>
						</div></a>
					</div>
					<div class="col col-md-4"></div>
				</div>
				@endforeach
			</div>
		</div>
	</div>
	<footer>
		@include("layouts/footer")		
	</footer>
</body>
</html>
--}}