@extends("product_update_mailer.page")
@section("content")

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Send Update Information mail</strong><a href="{{ url()->previous() }}" class="btn btn-default pull-right btn-sm"><span class="glyphicon glyphicon-arrow-left"></span> Back</a></div>
		<div class="panel-body">
		
			<div class="row clearfix">
				<div class="col col-md-4">
					<div class="form-group form-horizontal clearfix">
						<label class="control-label col-xs-4" style="text-align: left">Product :</label>
						<div class="col-xs-8">
							<select class="form-control" name="product" onChange="ProductChanged(this.value)">
								<option value="">Select Product</option>
								@foreach(\App\Models\Product::whereActive(1)->get() as $PO)
									<option value="{{ $PO->code }}">{{ $PO->name }}</option>
								@endforeach
							</select>
						</div>
					</div>
				</div>
				<div class="col col-md-4">
					<div class="form-group form-horizontal clearfix">
						<label class="control-label col-xs-4" style="text-align: left">Edition :</label>
						<div class="col-xs-8">
							<select class="form-control" name="edition" onChange="EditionChanged(this.value)"></select>
						</div>
					</div>
				</div>
				<div class="col col-md-4">
					<div class="form-group form-horizontal clearfix">
						<label class="control-label col-xs-4" style="text-align: left">Package :</label>
						<div class="col-xs-8">
							<select class="form-control" name="package" onChange="PackageChanged(this.value)"></select>
						</div>
					</div>
				</div>
			</div>
			<div class="row page_content clearfix" style="display: none"><hr>
				<div class="col col-md-7" style="border-right: 1px solid #DDD">
					<div class="table-responsive customer_details">
						<table class="table table-striped">
							<thead><tr><th colspan="4">Search for Customers to send email</th></tr>
							<tr><th>Name</th><th>Email</th><th>Presale</th><th class="text-center">Select</th></tr>
							<tr>
								<td><input type="text" class="form-control" name="name"></td>
								<td><input type="text" class="form-control" name="email"></td>
								<td class="text-center"><input type="checkbox" name="presale" value="1"></td>
								<td class="text-center"><button class="btn btn-info" onClick="SearchCustomer()">Search</button><br><a style="font-size: 12px;" href="javascript:InvertCustomerSelection()" class="link">Invert Customer Selection</a></td>
							</tr></thead>
							<tbody></tbody>
						</table>
					</div>
					<div class="table-responsive distributor_details">
						<table class="table table-striped">
							<thead><tr><th colspan="4">Search for Distributors to send email</th></tr>
							<tr><th>Name</th><th>Email</th><th class="text-center">Select</th></tr>
							<tr>
								<td><input type="text" class="form-control" name="dist_name"></td>
								<td><input type="text" class="form-control" name="dist_email"></td>
								<td class="text-center"><button class="btn btn-info" onClick="SearchDistributor()">Search</button><br><a style="font-size: 12px;" href="javascript:InvertDistributorSelection()" class="link">Invert Distributor Selection</a></td>
							</tr></thead>
							<tbody></tbody>
						</table>
					</div>
				</div>
				<div class="col col-md-5">
					<div class="package_details table-responsive">
						<table class="table table-striped">
							<tbody></tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="panel-footer clearfix">
			<a href="javascript:SendUpdateMail()" class="btn btn-primary pull-right disabled SUM_Button">Submit</a>
		</div>
	</div>
</div>

@endsection