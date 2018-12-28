@php
	$ProdOrm = \App\Models\Product::withoutGlobalScope('own')->where(['private'	=>	'NO']);
	$CompOrm = \App\Models\Company::first();
@endphp<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Milestone Innovative Technologies</title>
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
	@if($ProdOrm->count()>0)
		<script type="text/javascript" src="js/home.js"></script>
		<script type="text/javascript">
			$(function(){
				LoadFeatures('{{ $ProdOrm->pluck("code")[0] }}','{{ $ProdOrm->pluck("name")[0] }}');
			})
		</script>
	@endif
	<link type="text/css" rel="stylesheet" href="css/bootstrap.css">
	<link type="text/css" rel="stylesheet" href="css/common.css">
	<style type="text/css" rel="stylesheet">
		.preview { height: 20em; background-repeat: no-repeat; background-size: contain; background-position: center; border: 1px solid #FFF; overflow: hidden; }
		.preview .aholder { height:inherit; padding-top:4em;  background-color: rgba(220,220,220,0.35); transform: scale(0,0); transition: all 0.1s; }
		.preview .aholder a { margin-left: 40%; }
		.preview .aholder ul { margin-top: 1em; background-color: rgba(221,221,221,0.0); list-style: none; font-size: 0.85em; padding: 1em 2em; transition: all 0.3s 0.1s; }
		.preview:hover .aholder ul { background-color: rgba(221,221,221,0.9); }
		.preview:hover .aholder { transform: scale(1,1); }
		small { margin-top: 0.6em; max-width: 50%; }
		.base { max-height: 0px; overflow: hidden; transition: all 0.5s; }
		.item:hover .base { max-height: 100px; }
	</style>
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

@php
	$ORM = new \App\Models\PublicPrintObject; $DOrm = $ORM->query();
	if(Request()->search_text){ $st = '%'.Request()->search_text.'%'; $DOrm = $DOrm->where(function($Q)use($st){ $Q->where('name','like',$st)->orWhere('code','like',$st)->orWhere('description','like',$st)->orWhereHas('Specs',function($Q)use($st){ foreach(range(0,9) as $C){ $N = 'spec'.$C; if($C) $Q->orWhere($N,'like',$st); else $Q->where($N,'like',$st); } }); }); }
	$Data = $DOrm->web()->paginate(9);
@endphp

<div class="page_contents">
	<div class="page print_objects products" style="background-color: #EBEBEB">
		<div class="container">
			<div style="height: 90px;">&nbsp;</div>
			<div class="page_heading">Print Objects</div>
			<div class="clearfix pagination">
				<div class="pull-left col-xs-4 p0">
					<div class="input-group">
						<form name="search_form">
							<input type="text" name="search_text" class="form-control" placeholder="Search" value="{{ Request()->search_text }}" autofocus>
						</form>
						<a href="javascript:document.forms.search_form.submit()" class="input-group-addon"><span class="glyphicon glyphicon-search"></span></a>
					</div>
				</div>
				<div class="pull-right">{{ $Data->appends(['search_text' => Request()->search_text])->links() }}</div>
			</div>
			@if($Data->isNotEmpty())
				@foreach($Data->chunk(3) as $ChunkArray)
					<div class="row">
						@foreach($ChunkArray as $Item)
							<div class="col col-md-4">
								<div class="item">
									<div class="clearfix top">
										<div class="h4 pull-left">{{ $Item->name }}</div>
										<div class="h4 pull-right" title="Downloads">{{ $Item->downloads }}</div>
									</div>
									<div class="preview" style="background-image: url('{{ \Storage::disk($ORM->storage_disk)->url($Item->preview) }}')">
										<div class="aholder">
											@if($Item->preview)<a href="{{ \Storage::disk($ORM->storage_disk)->url($Item->preview) }}" class="btn btn-default" target="_blank">Preview</a>@endif
												<div class="text-center bg-primary small">{!! nl2br($Item->description) !!}</div>
											@if($Item->Specs && $Item->Specs->details) <ul>
												@foreach($Item->Specs->details as $Name => $Value)
													<li title="{{ $Name }}"><span class="glyphicon glyphicon-chevron-right"></span> &nbsp; {{ $Name }} <b>&gt;</b> {{ $Value }}</li>
												@endforeach
											</ul> @endif
										</div>
									</div>
									<div class="clearfix base">
										<!--<small class="pull-left">{!! nl2br($Item->description) !!}</small>-->
										<div class="actions pull-right">
											<a href="{{ route('home.print_object.download',$Item->code) }}" title="Download this print object" class="btn btn-info" style="margin-top:0.6em;">Download</a>
										</div>
									</div>
								</div>
							</div>
						@endforeach
					</div>
				@endforeach
			@else <div class="jumbotron text-center"><h4>No records found!</h4></div> @endif
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
