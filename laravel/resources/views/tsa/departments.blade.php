@extends("tsa.page")
@include('BladeFunctions')
@section("content")

<div class="content"><form method="post">{{ csrf_field() }}
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Support Agent's Departments</strong>{!! PanelHeadBackButton(url()->previous()) !!}</div>
		<div class="panel-body">
			<div class="table-responsive sad">
				<table class="table table-striped">
					<tbody></tbody>
				</table>
			</div>
			<div class="jumbotron">
				<h2 class="text-center">No Records found</h2>
			</div>
		</div>
		<div class="panel-footer clearfix">
			<input type="submit" name="submit" value="Update Modifications" class="pull-right btn btn-primary">
		</div>
	</div></form>
</div>

@endsection
@push('js')
<script type="text/javascript">
	var TSA = {!! json_encode(\App\Models\TechnicalSupportAgent::all()->groupBy('code')->toArray()) !!};
	var SD = {!! json_encode(\App\Models\SupportDepartment::all()->groupBy('code')->toArray()) !!};
	var SAD = {!! json_encode(\App\Models\SupportAgentDepartment::all()->groupBy('agent')->toArray()) !!};
</script>
<script type="text/javascript" src="js/tsa_departments.js"></script>
@endpush