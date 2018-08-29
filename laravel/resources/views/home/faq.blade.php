@php
	$Faqs = \App\Models\FAQ::public()->active()->search()->paginate(5);
//dd($Faqs->toArray())
@endphp<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Milestone :: FAQ</title>
	<meta property="og:title" content="Milestone - Frequently Asked Questions">
	<meta property="og:description" content="A few frequently asked questions from Milestone Knowledge Base.">
	<base href="/">
	@include('inc/favicon')
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-116616304-1"></script>
	<script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'UA-116616304-1');
	</script>
	<style type="text/css">
		.panel {
			margin-bottom: 3px !important;
			border: none !important;
		}
		.panel-heading { cursor: pointer; }
		.panel .panel-body {
			display: none;
		}
		.panel.view .panel-body {
			display: block;
		}
		i.tags {
			font-size: 10px;
			padding: 3px 6px;
			background-color: #FFF;
			margin: 0px 2px;
			-webkit-border-radius: 2px;
			-moz-border-radius: 2px;
			border-radius: 2px;
		}
		.helped {
			margin-top: 100px;
			font-size: 12px;
		}
	</style>
	<script>
        function FAQShow(cls){
            if($('.panel.view.'+cls).length) return $('.panel.view.'+cls+' .panel-body').slideUp(100,function () {$(this).parent().removeClass('view')});
            $('.panel.view').removeClass('view').find(".panel-body").removeAttr('style');
            $('.panel.'+cls+' .panel-body').slideDown(200,function () {$(this).parent().addClass('view')});
            $.post('/api/ifv',{q:cls});
		}
        function FAQBenefits(cls){
            $.post('/api/ifb',{q:cls},function(cls){ $('.panel.'+cls+' .helped').text('Thanks for your feedback...').delay(2000).fadeOut() });
		}
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
	<div class="page faqs products" style="background-color: #EBEBEB">
		<div class="container">
			<div style="height: 90px;">&nbsp;</div>
			<div class="page_heading">Frequently Asked Questions</div>
			<div class="clearfix pagination">
				<div class="pull-left col-xs-7 p0" style="margin-top: 20px;">
					<div class="input-group">
						<form name="search_form">
							<input type="text" name="search_text" class="form-control" placeholder="Search" value="{{ Request()->search_text }}" autofocus>
						</form>
						<a href="javascript:document.forms.search_form.submit()" class="input-group-addon"><span class="glyphicon glyphicon-search"></span></a>
					</div>
				</div>
				<div class="pull-right">{{ $Faqs->appends(['search_text' => Request()->search_text])->links() }}</div>
			</div>
			@forelse($Faqs as $Faq)
				<div class="panel-default panel Q{{ $Faq->id }}">
					<div class="panel-heading" onclick="FAQShow('Q{{ $Faq->id }}')"><strong style="margin-right: 20px;">{{ $loop->iteration }}.</strong> {{ nl2br($Faq->question) }}
						<span class="tags pull-right"><i class="tags">{!! implode('</i><i class="tags">',$Faq->tags) !!}</i></span>
					</div>
					<div class="panel-body"><p>{!! nl2br($Faq->answer) !!}</p>
						<div class="helped">Was this help full to you <button class="btn btn-default btn-sm" onclick="FAQBenefits('Q{{ $Faq->id }}')"><i class="glyphicon glyphicon-thumbs-up"></i> Yes</button></div>
					</div>
				</div>
			@empty
				<div class="jumbotron text-center"><h3>No Data</h3></div>
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
