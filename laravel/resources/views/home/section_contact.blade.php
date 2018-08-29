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
