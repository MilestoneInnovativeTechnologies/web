		<div class="page contact">
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
							@php $Company = \App\Models\Company::first(); @endphp
								<div class="address"><p>{{ $Company->name }}, {{ $Company->Details->address1 }}, {{ $Company->Details->address2 }}</p></div>
								<div class="website"><p>{{ $Company->Details->website }}</p></div>
								<div class="email"><p>{{ $Company->Logins[0]->email }}</p></div>
								<div class="phone"><p>+{{ $Company->Details->phonecode }} {{ $Company->Details->phone }}<br>+91 7902455500</p></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
