@extends("distributor.page")
@section("content")

<div class="content">
	<div class="jumbotron text-center">
		<h2>Something wents wrong!!</h2>
		<div class="text-center">
			<big>Please try again..</big><br><br>
			<div class="clearfix"><a href="{{ Route($item . ".index") }}" class="btn btn-lg btn-info">Back to {{ ucwords($item) }}</a></div>
		</div>
	</div>
</div>

@endsection