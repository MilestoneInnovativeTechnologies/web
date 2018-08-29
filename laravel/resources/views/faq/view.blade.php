@extends("faq.page")
@include('BladeFunctions')
@php $Data = App\Models\FAQ::find(request()->id); @endphp
@section("content")
<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>FAQ</strong>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('faq.index'):url()->previous()) !!}</div>
		<div class="panel-body">
			<div class="table-responsive"><table class="table table-striped"><tbody>
					<tr><th colspan="2">{!! nl2br($Data->question) !!}</th></tr>
					<tr><td colspan="2">{!! nl2br($Data->answer) !!}</td></tr>
					<tr><th colspan="2">&nbsp;</th></tr>
					<tr><th>Categories</th><td>{{ implode(', ',$Data->Categories ? $Data->Categories->categories : []) }}</td></tr>
					<tr><th>Software</th><td>{{ implode(', ',$Data->tags) }}</td></tr>
					<tr><th>Scope</th><td>{{ Scopes($Data->Scope) }}</td></tr>
					<tr><th colspan="2">&nbsp;</th></tr>
					<tr><th width="50">Views</th><td>{{ $Data->views }}</td></tr>
					<tr><th>Benefits</th><td>{{ $Data->benefits }}</td></tr>
					<tr><th colspan="2">&nbsp;</th></tr>
					<tr><th width="50">Created By</th><td>{{ $Data->Creator->name }}</td></tr>
					<tr><th>Created On</th><td>{{ date('D d/m/y, h:i:s a',strtotime($Data->created_at)) }}</td></tr>
					</tbody></table></div>
		</div>
	</div>
</div>

@endsection
@php
	function Scopes($Obj){
        $scope_keys = ['public','support','distributor','customer'];
        $scopes = [];
        foreach($scope_keys as $key) if($Obj->$key === 'YES') $scopes[] = ucfirst($key);
        if($Obj->partner) $scopes[] = $Obj->Partner->name;
        return implode(", ", $scopes);
    }
@endphp