@extends("resource.page")
@section("content")

	<div class="clearfix">
		<a href="{{ Route('resource.index') }}" class="btn btn-info"><span class="glyphicon glyphicon-arrow-left"></span> &nbsp; Back</a>
		<br><br>
	</div>
	
	<div class="panel panel-default">
		<div class="panel-heading"><h3>{{ $Resource->displayname }}</h3></div>
		<div class="panel-body"><p>{{ $Resource->description }}</p></div>
	<?php
	$BodyArray = ["Base Name"=>"name", "Code"=>"code"];
	?>
	@foreach($BodyArray as $Head => $Var)
		<div class="panel-body">
			<h4>{{ $Head }}</h4>
			<p>{{ $Resource->$Var }}</p>
		</div>
  @endforeach
		<div class="panel-body">
			<h4>Resource Actions</h4>
			<p class="raw_actions">{{ $Resource->action }}</p>
		</div>
	</div>
@endsection
@push("js")
<script type="text/javascript">
var TotalActions = {!! '["' . implode('","',array_column($Actions,"displayname")) . '"]' !!};
</script>
@endpush