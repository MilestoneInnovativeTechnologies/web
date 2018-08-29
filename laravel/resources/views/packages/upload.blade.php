@extends("packages.page")
@section("content")
<?php
	$Type = (isset($Edition)?(isset($Package)?'Package':'Edition'):'Product');
?>
<div class="text-right"><a href="{{ (($Type == "Product")?url()->previous():( ($Type == "Edition")?'package/upload':('package/upload/'.$Product->code))) }}" class="btn btn-default"> Back </a></div><br>

@if($Type == "Product")
<div class="panel panel-default">
  <div class="panel-heading"><strong>Select a Product</strong></div>
  <div class="panel-body clearfix">
  	@if(!empty($Product))
  	@foreach($Product as $ProdArray)
  	<div class="col-xs-4">
			<a class="link-block" href="package/upload/{{ $ProdArray['code'] }}">
				<div class="jumbotron text-center"><h3>{{ $ProdArray['name'] }}</h3></div>
			</a>
  	</div>
  	@endforeach
  	@endif  	
  </div>
</div>
@endif

@if($Type == "Edition")
<div class="panel panel-default">
  <div class="panel-heading"><strong>Select {{ $Product->name }}'s Edition</strong></div>
  <div class="panel-body">
  	@if(!empty($Edition))
  	@foreach($Edition as $EditionArray)
  	<div class="col-xs-4">
			<a class="link-block" href="package/upload/{{ $Product->code }}/{{ $EditionArray['code'] }}">
				<div class="jumbotron text-center"><h3>{{ $EditionArray['name'] }}</h3></div>
			</a>
  	</div>
  	@endforeach
  	@endif  	
  </div>
</div>
@endif

@if($Type == "Package")
<div class="panel panel-default">
  <div class="panel-heading"><strong>Select Package of {{ $Product->name }}'s {{ $Edition->name }} edition</strong></div>
  <div class="panel-body">
  	@if(!empty($Package))
  	@foreach($Package as $PackageArray)
  	<div class="col-xs-4">
	  	<a class="link-block" href="package/upload/{{ $Product->code }}/{{ $Edition->code }}/{{ $PackageArray['package'] }}">
  			<div class="jumbotron text-center"><h3>{{ $PackageArray['packages']->name }}</h3><br><small>({{ $PackageArray['packages']->type }})</small></div>
  		</a>
		</div>
  	@endforeach
  	@endif  	
  </div>
</div>
@endif
@endsection