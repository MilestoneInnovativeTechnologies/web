@extends("resource.page")
@section("content")
<?php
function floattostr( $val ){
	preg_match( "#^([\+\-]|)([0-9]*)(\.([0-9]*?)|)(0*)$#", trim($val), $o );
	return $o[1].sprintf('%d',$o[2]).($o[3]!='.'?$o[3]:'');			
}
function ACT2Arr($act){
	return array_keys(str_split(str_replace(".","",floattostr ( $act ))),"1");
}
$RRD = [];
if(!empty($ResourceRoles)){
	foreach($ResourceRoles as $RObj){
		$RRD[$RObj['code']] = [$RObj['name'],$RObj['displayname'],ACT2Arr($RObj['pivot']['action'])];
	}
}
$ResActions = ACT2Arr($Resource['action']);
$ActName = array_column($Actions,"displayname"); array_unshift($ActName,"0");

function TDActions($RoleObj,$ResActions,$Values = []){
	$TD = "";
	foreach($ResActions as $ActID){
		$TD .= '<th><label class="switch"><input class="form-control" type="checkbox" name="actions['.$RoleObj['code'].'][]" value="'.$ActID.'"'.(in_array($ActID,$Values)?' checked':'').'><div class="slider"></div></label></th>';
	}
	return $TD;
}
?>




<div class="content">
	<form method="post" action="{{ Route('resource.role',['resource'=>$Resource['code']]) }}">
		{{ csrf_field() }}
		<div class="clearfix">
			<a href="{{ Route('resource.index') }}" class="btn btn-info"><span class="glyphicon glyphicon-arrow-left"></span> &nbsp; Back</a>
			<br><br>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading"><h3>Select Roles for <em>{{ $Resource->displayname }}</em></h3></div>
			<div class="panel-body">
				<div class="form-group">
					<select name="roles[]" class="form-control" multiple id="RolesTable" onChange="RolesValueChanged()">
						@foreach($Roles as $RoleObj)
						<option value="{{ $RoleObj['code'] }}"{{ array_key_exists($RoleObj['code'],$RRD)?' selected':'' }}>{{ $RoleObj['displayname'] }}</option>
						@endforeach
					</select>
				</div>
			</div>
			
			<div class="panel-body">
				<h3>Update actions for selected Roles</h3>
				<div class="table-responsive">
					<table class="table table-bordered table-stripped">
						<tr><th width="20%" rowspan="2">Role</th><th width="80%" colspan="{{ count($ResActions) }}">Actions</th></tr>
						<tr>
						@foreach($ResActions as $ActionId)
							<th>{{ $ActName[$ActionId] }}</th>
						@endforeach
						</tr>
						@foreach($Roles as $K => $RoleObj)
						<tr class="role_{{ $RoleObj['code'] }}"><td><strong>{{ $RoleObj['displayname'] }}</strong></td>{!! TDActions($RoleObj,$ResActions,(array_key_exists($RoleObj['code'],$RRD)?($RRD[$RoleObj['code']][2]):([]))) !!}</tr>
						@endforeach
					</table>
				</div>
			</div>

			<div class="panel-footer clearfix">
				<div class="pull-right"><input type="submit" class="btn btn-info" name="submit" value="Update"> &nbsp; &nbsp; &nbsp; <a href="{{ Route('resource.index') }}" class="btn btn-info" >Cancel</a></div>
			</div>
		</div>
	</form>
</div>

@endsection
@push("js")
<script type="text/javascript">
var TotalActions = {!! '["' . implode('","',array_column($Actions,"displayname")) . '"]' !!};
</script>
@endpush
@push("css")
<link rel="stylesheet" href="css/multiselect.css" type="text/css">
<style type="text/css">
	th { text-align: center !important; }
</style>
@endpush
@push("js")
<script type="text/javascript" src="js/multiselect.js"></script>
<script type="text/javascript">
$(function(){ $('#RolesTable').multiSelect({
	selectableHeader: "<div class='label label-info'>Available Roles</div>",
	selectionHeader: "<div class='label label-primary'>Selected Roles</div>"
}); })
</script>
@endpush