@extends("role.page")
@section("content")

	<div class="clearfix">
		<a href="{{ Route('role.index') }}" class="btn btn-info"><span class="glyphicon glyphicon-arrow-left"></span> &nbsp; Back</a>
		<br><br>
	</div>
	
	<div class="panel panel-default">
		<div class="panel-heading"><h3>{{ $Role->displayname }}</h3></div>
		<div class="panel-body"><p>{{ $Role->description }}</p></div>
	<?php
	$BodyArray = ["Code"=>"code","Base Name"=>"name","Created at"=>"created_at","Updated at"=>"updated_at"];
	?>
	@foreach($BodyArray as $Head => $Var)
		<div class="panel-body">
			<h4>{{ $Head }}</h4>
			<p>{{ $Role->$Var }}</p>
		</div>
  @endforeach
</div>
@endsection