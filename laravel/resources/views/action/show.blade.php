@extends("action.page")
@section("content")

	<div class="clearfix">
		<a href="{{ Route('action.index') }}" class="btn btn-info"><span class="glyphicon glyphicon-arrow-left"></span> &nbsp; Back</a>
		<br><br>
	</div>
	
	<div class="panel panel-default">
		<div class="panel-heading"><h3>{{ $Action->displayname }}</h3></div>
		<div class="panel-body"><p>{{ $Action->description }}</p></div>
	<?php
	$BodyArray = ["Base Name"=>"name", "Action id"=>"id"];
	?>
	@foreach($BodyArray as $Head => $Var)
		<div class="panel-body">
			<h4>{{ $Head }}</h4>
			<p>{{ $Action->$Var }}</p>
		</div>
  @endforeach
</div>
@endsection