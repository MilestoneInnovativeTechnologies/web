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
