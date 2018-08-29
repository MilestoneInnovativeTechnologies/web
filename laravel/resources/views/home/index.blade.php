<!doctype html>
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
<link type="text/css" rel="stylesheet" href="css/bootstrap.css">
<link type="text/css" rel="stylesheet" href="css/common.css">
<script type="text/javascript" src="js/home.js"></script>
@php
	$ProdOrm = \App\Models\Product::withoutGlobalScope('own')->where(['private'	=>	'NO']);
	$CompOrm = \App\Models\Company::first();
@endphp<script type="text/javascript">
@if($ProdOrm->count()>0)
$(function(){
	LoadFeatures('{{ $ProdOrm->pluck("code")[0] }}','{{ $ProdOrm->pluck("name")[0] }}');
})
@endif
</script>
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
					<li><a href="javascript:topage('home')">Home</a></li>
					<li><a href="javascript:topage('products')">Products</a></li>
					<li><a href="javascript:topage('features')">Features</a></li>
					<li><a href="javascript:topage('contact')">Contact</a></li>@if(Auth::check())
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
		<div class="page home">
			<div class="container">
				<div class="home_content">
					<div class="mit_caption text-uppercase">i do it my own way</div>
					<div class="mit_escription">Milestone ePlus is highly secured, fully featured, Accounting and inventory management software designed to increase profitability and to give your business a competitive advantage in the industry. </div>
				</div>
			</div>
		</div>
		<div class="page products">
			<div class="container">
				<div class="row top">
					<div class="page_heading">Products</div>
					<div class="col col-lg-6 col-md-6 col-sm-12 col-xs-12 prod_details">
						@if($ProdOrm->count() > 1)
						<a href="javascript:PreviousProduct();" title="Show Previous Product" class="pcb"><div class="products_previous_product"></div></a>
						<a href="javascript:NextProduct();" title="Show Next Product" class="pcb"><div class="products_next_product"></div></a>
						@endif
						<div class="products_contents">
							@if($ProdOrm->count() > 0)
							@foreach($ProdOrm->get() as $Product)
								<div class="products_content">
									<div class="products_heading">{{ $Product->name }}</div>
									<div class="products_text text-justify">{{ mb_substr($Product->description_public,0,500) }}</div>
									<div class="products_download text-right">
										<a href="javascript:DownloadApp('{{ $Product->code }}');" class="btn btn-primary btn-lg">Download {{ $Product->name }}</a>
									</div>
								</div>
							@endforeach
							@else
								<div class="products_content"><div class="products_heading"></div></div>
							@endif
						</div>
					</div>
					<div class="col col-lg-6 col-md-6 hidden-xs hidden-sm">
						<div class="products_newsfeed">
							<div class="heading">NEWS FEED</div>
							<div class="text"><ul>
								<li><i>Date</i><i>Content</i></li>
								<li><i>Date</i><i>Content</i></li>
							</ul></div>
						</div>
					</div>
				</div>
				<div class="row bottom hidden-xs hidden-sm" style="margin-top: 65px;">
					<div class="col col-xs-12 col-sm-12 col-md-4 col-lg-4 text-center">
						<div class="hl_holder"><div class="image uf"></div></div>
						<div class="im_caption">USER FRIENDLY</div>
					</div>
					<div class="col col-xs-12 col-sm-12 col-md-4 col-lg-4 text-center">
						<div class="hl_holder"><div class="image ec"></div></div>
						<div class="im_caption">EASY CONFIGURATION</div>
					</div>
					<div class="col col-xs-12 col-sm-12 col-md-4 col-lg-4 text-center">
						<div class="hl_holder"><div class="image fr"></div></div>
						<div class="im_caption">FLEXIBLE REPORTS</div>
					</div>
				</div>
			</div>
		</div>
		<div class="page features">
			<div class="container">
				<div class="heading">
					<div style="text-align: center; margin: auto;">
						@if($ProdOrm->count() > 1)
						<ul class="nav text-center feature_dropdown_ul">
							<li class="dropdown feature_dropdown_li">
								<a href="#" style="color: #FFFFFF;" class="dropdown-toggle feature_dropdown_li_a" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"></a>
								<ul class="dropdown-menu nav feature_dropdown_li_a_ul">
									@foreach($ProdOrm->pluck('name','code') as $code => $name)
									<li class="nav-link"><a href="javascript:LoadFeatures('{{ $code }}','{{ $name }}')">{{ $name }}</a></li>
									@endforeach
								</ul>
							</li>
						</ul>
						@elseif($ProdOrm->count() > 0)
						{{ $ProdOrm->pluck('name')[0] }}
						@else
						-
						@endif
					</div>
				</div>
				<div class="table table-responsive thead">
					<table class="table table-striped features">
						<thead class="features_thead">
							<tr><th rowspan="2" style="text-align: left; font-size: 20px;" width="40%">FEATURE</th><th colspan="4" style="font-size: 20px;" class="features_edition_th">EDITIONS</th></tr>
							<tr class="features_editions"></tr>
						</thead>
					</table>
				</div>
				<div class="feature_wrapper">
					<div class="feature_content">
						<div class="table table-responsive tbody">
							<table class="table table-striped features">
								<thead style="visibility: hidden" class="features_thead">
									<tr><th rowspan="2" style="text-align: left; font-size: 20px;" width="40%">FEATURE</th><th colspan="4" style="font-size: 20px;" class="features_edition_th">EDITIONS</th></tr>
									<tr class="features_editions"></tr>
								</thead>
								<tbody style="margin-top: -2px;" class="features_tbody"></tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="page contact">
			<div class="container">
				<div class="map">
					<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3921.8753317138617!2d76.0229384148903!3d10.588919092448918!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ba7946fa99fad03%3A0x5bb2c044f3cb6a65!2sMilestone+Innovative+Technologies!5e0!3m2!1sen!2sin!4v1497595975986" allowfullscreen></iframe>
				</div>
			</div>
			<div class="strip"></div>
			<div class="contact">
				<div class="container">
					<div class="row">
						<div class="col col-lg-3 col-md-3 col-sm-4 col-xs-12">
							<div class="item_title">Get To Know Us</div>
							<div class="item_content"><ul>
								<li><a href="javascript:topage('home')">About Us</a></li>
								<li><a href="javascript:topage('products')">Products</a></li>
								<li><a href="javascript:topage('features')">Features</a></li>
								<li><a href="javascript:topage('download')">Download</a></li>
							</ul></div>
						</div>
						<div class="col col-lg-3 col-md-3 col-sm-4 col-xs-12">
							<div class="item_title">Connect With Us</div>
							<div class="item_content"><ul>
								<li><a href="">Facebook</a></li>
								<li><a href="">G+ Plus</a></li>
								<li><a href="">Twitter</a></li>
								<li><a href="">Linkedin</a></li>
							</ul></div>
						</div>
						<div class="col col-lg-3 col-md-3 hidden-xs hidden-sm"></div>
						<div class="col col-lg-3 col-md-3 col-sm-4 col-xs-12">
							<div class="item_content">
								<div class="address"><p>{{ $CompOrm->name }}, {{ $CompOrm->Details->address1 }}, {{ $CompOrm->Details->address2 }}</p></div>
								<div class="website"><p>{{ $CompOrm->Details->website }}</p></div>
								<div class="email"><p>{{ $CompOrm->Logins[0]->email }}</p></div>
								<div class="phone"><p>+{{ $CompOrm->Details->phonecode }} {{ $CompOrm->Details->phone }}<br>+91 7902455500</p></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
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

<div id="downloadModal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Download Options</h4>
			</div>
			<div class="modal-body">
				<div class="text-center download_options">
					@if(Auth::check())
					<a href="dashboard" class="btn btn-primary">Navigate to Dashboard to download</a><h2>OR</h2>
					@else
					<a href="javascript:UserDownload();" class="btn btn-primary">Login to download</a><h2>OR</h2>
					@endif
					<a href="javascript:GuestDownload();" class="btn btn-primary">Download as Guest</a>
				</div>
				<div class="guest_download_form clearfix" style="display: none">
					<div class="row">
						<div class="col col-md-2 col-lg-2 hidden-sm hidden-xs"></div>
						<div class="col-md-8 col-lg-8 col-xs-12 col-sm-12">
							<div class="form-group">
								<label class="control-label">Email Address</label>
								<input type="text" name="email_link" class="form-control">
								<small>Download links will be sent to the email address providing.</small>
							</div>
							<div class="clearfix text-right">
								<a href="javascript:BackDwnOpts()" class="btn btn-default">Back</a>
								<a href="javascript:SendDownloadLink();" class="btn btn-primary">Send download link</a>
							</div>
						</div>
						<div class="col col-md-2 col-lg-2 hidden-sm hidden-xs"></div>
					</div>
				</div>
				<div class="user_download_form clearfix" style="display: none">
					<div class="row">
						<div class="col col-md-2 col-lg-2 hidden-sm hidden-xs"></div>
						<div class="col-md-8 col-lg-8 col-xs-12 col-sm-12">
							<form action="{{ Route('login') }}" class="form" method="post">{{ csrf_field() }}
								<div class="form-group">
									<label class="control-label">Email Address</label>
									<input type="text" name="email" class="form-control">
								</div>
								<div class="form-group">
									<label class="control-label">Password</label>
									<input type="password" name="password" class="form-control">
								</div>
								<div class="clearfix text-right">
									<a href="javascript:BackDwnOpts()" class="btn btn-default">Back</a>
									<input type="submit" class="btn btn-primary" value="Login">
								</div>
							</form>
						</div>
						<div class="col col-md-2 col-lg-2 hidden-sm hidden-xs"></div>
					</div>
				</div>
				<div class="download_requested clearfix text-center" style="display: none">
					<strong>Download link mailed to the email provided, Please check the mail..</strong>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
			</div>
		</div>
	</div>
</div>






</body>
</html>