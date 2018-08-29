@php
	$Vacancies = \App\Models\Vacancy::where('live','1')->get();
    $JVCount = $Vacancies->count();
@endphp<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Milestone Innovative Technologies</title>
	<meta property="og:title" content="Milestone Job Vacancies">
	<meta property="og:description" content="Milestone have {{ $JVCount ?: 'No' }} job vacancies right now. Apply now and earn a techy rich future.">
	<base href="/">
	@include('inc/favicon')
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-116616304-1"></script>
	<script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'UA-116616304-1');
	</script>
	<script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.js"></script>
	<script type="text/javascript" src="js/home.js"></script>
	<link type="text/css" rel="stylesheet" href="css/bootstrap.css">
	<link type="text/css" rel="stylesheet" href="css/common.css">
</head>

<body>
<nav class="navbar navbar-fixed-top">
	<div class="container">
		<div class="navbar-header">
			<a class="navbar-brand" href="javascript:goHome()"><div class="logo visible-lg-block"></div><div class="logo visible-md-block"></div><div class="logo visible-sm-block"></div><div class="logo visible-xs-block"></div></a>
			<button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbarCollapse">
				<span class="sr-only"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
		</div>
		<div class="navbar-collapse collapse" id="navbarCollapse">
			<ul class="nav navbar-nav navbar-right">
				<li><a href="/home">Home</a></li>
				<li><a href="/home">Products</a></li>
				<li><a href="/home">Features</a></li>
				<li><a href="/home">Contact</a></li>@if(Auth::check())
					<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dashboard <span class="caret"></span></a>
						<ul class="dropdown-menu nav">
							<li class="nav-link"><a href="{{ Route('dashboard') }}">Dashboard</a></li>@if(session("_rolename") != "company")
								<li class="nav-link"><a href="{{ Route('changepassword') }}">Change Password</a></li>
								<li class="nav-link"><a href="{{ Route('changeaddress') }}">Change Address</a></li>
								{!! (session("_roles")>1)?'<li class="nav-link"><a href="roleselect">Change Role</a></li>':'' !!}
							@endif<li class="nav-link"><a href="logout">Logout</a></li>
						</ul>
					</li>
				@else
					<li><a href="javascript:login()">Login</a></li>
				@endif
			</ul>
		</div>
	</div>
</nav>


<div class="page_contents">
	<div class="page vacancies products" style="background-color: #EBEBEB">
		<div class="container">
			<div style="height: 90px;">&nbsp;</div>
			<div class="page_heading">Vacancies @if(Auth::check() && session("_rolename") === "company") - <a class="btn btn-info btn-sm" href="{{ route('vacancy.manage') }}">Manage</a> @endif </div>
			@forelse($Vacancies as $Vacancy)
				<div class="panel-default panel">
					<div class="panel-heading">{{ $Vacancy->title }}<strong class="pull-right">{{ date("d/M/Y",strtotime($Vacancy->date)) }}</strong></div>
					<div class="panel-body"><p>{!! nl2br($Vacancy->description) !!}</p></div>
					<div class="panel-footer clearfix"><a class="btn btn-sm btn-primary pull-right" href="{{ route('vacancy.apply',$Vacancy->code) }}">View/Apply</a></div>
				</div>
			@empty
				<div class="panel panel-default">
					<div class="panel-body">
						<p>Right now we doesn't have any vacancies. Please visit again later</p>
						<h3>OR</h3>
						<p>Enter your email address to get informed earliest, if any job vacancies are available.</p>
						@if(session()->has("email"))
							<p style="color: #38a700">{{ session("email") }}</p>
						@else
							<form method="post">
								{{ csrf_field() }}
								<div class="col-xs-3"><input type="email" placeholder="Email" name="jv_notify_email" class="form-control"></div>
								<div class="col-xs-3"><input type="submit" value="Submit" class="btn btn-info"></div>
							</form>
						@endif
					</div>
				</div>
			@endforelse
		</div>
	</div>
	@include("home.section_contact_wo_map")
</div>




<div id="loginModal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content"><form method="post" action="{{ Route('login') }}" class="form-horizontal">{{ csrf_field() }}
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">Login</h4>
				</div>
				<div class="modal-body">
					<div class="form-group clearfix">
						<div class="col-xs-12">
							<label class="control-label col-xs-4">Email Address:</label>
							<div class="col col-xs-8">
								<input type="text" name="email" value="" class="form-control">
							</div>
						</div>
					</div>
					<div class="form-group clearfix">
						<div class="col-xs-12">
							<label class="control-label col-xs-4">Password:</label>
							<div class="col-xs-8">
								<input type="password" name="password" value="" class="form-control">
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<input type="submit" class="btn btn-primary" value="Login">
				</div>
			</form></div>
	</div>
</div>

</html>
