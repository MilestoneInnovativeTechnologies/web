@extends("role.page")
@section("content")
<?php
$ActName = [];
foreach($Actions as $ActArr){
	$ActName[$ActArr['id']] = $ActArr['displayname'];
}
function floattostr( $val ){
	preg_match( "#^([\+\-]|)([0-9]*)(\.([0-9]*?)|)(0*)$#", trim($val), $o );
	return $o[1].sprintf('%d',$o[2]).($o[3]!='.'?$o[3]:'');			
}
function ACT2Arr($act){
	return array_keys(str_split(str_replace(".","",floattostr ( $act ))),"1");
}
$RS = []; $RSD = []; $JSID = ["#ResourceTable"];
if(!empty($RoleResources)){
	$RS = array_map(function($RSObj) use(&$RSD){
		$RSD[$RSObj["code"]] = [$RSObj["name"],$RSObj["action"],$RSObj["pivot"]["action"]];
		return $RSObj["code"];
	},$RoleResources);
}
?>
<div class="content">
	<div class="clearfix">
		<a href="{{ Route('role.index') }}" class="btn btn-info"><span class="glyphicon glyphicon-arrow-left"></span> &nbsp; Back</a>
		<br><br>
	</div>
	<form method="post" action="{{ Route('role.resource',['role'=>$Role['code']]) }}">
		{{ csrf_field() }}
		<div class="panel panel-default">
			<div class="panel-heading"><h3>Select Resources for {{ $Role['displayname'] }}</h3></div>
			<div class="panel-body"><p>
				<select name="resources[]" class="form-control" multiple id="ResourceTable" onChange="ResourceChange()">
					@foreach($Resources as $RObj)
					<option value="{{ $RObj['code'] }}" data-action="{{ $RObj['action'] }}" data-name="{{ $RObj['name'] }}"{{ (array_key_exists($RObj['code'],$RSD))?' selected':'' }}>{{ $RObj["displayname"] }}</option>
					@endforeach
				</select>
			</p></div>
			@foreach($Resources as $ResObj)
			<div class="panel-body resource {{ $ResObj["name"] }} code_{{ $ResObj["code"] }}">
				<div class="resource_content">
					<h3>Select actions for <ins>{{ $ResObj["displayname"] }}</ins></h3>
					<?php	$ResourceActions = ACT2Arr( $ResObj["action"] );	?>
					<?php	$MyActions = (array_key_exists($ResObj["code"],$RSD)) ? (ACT2Arr( $RSD[$ResObj["code"]][2] )) : [1]; ?>
					<div class="clearfix">
					@foreach($ResourceActions as $k => $ActID)
						{!! ($k % 4 == 0)?'</div><div class="clearfix">':'' !!}
						<div class="col-xs-3"><label class="switch"><input id="id_{{$ResObj["code"]}}_{{$ActID}}" class="form-control" type="checkbox" name="actions[{{ $ResObj["code"] }}][]" value="{{ $ActID }}"{{ (in_array($ActID,$MyActions))?' checked':'' }}><div class="slider"></div></label><label class="switch_label" for="id_{{$ResObj["code"]}}_{{$ActID}}">{{ $ActName[$ActID] }}</label></div>
					@endforeach
					</div>
				</div>
			</div>
			@endforeach
			<div class="panel-footer clearfix">
				<div class="pull-right"><input type="submit" class="btn btn-info" name="submit" value="Update"> &nbsp; &nbsp; &nbsp; <a href="{{ Route('role.index') }}" class="btn btn-info" >Cancel</a></div>
			</div>
		</div>
	</form>
</div>

@endsection
@push("css")
<link rel="stylesheet" href="css/multiselect.css" type="text/css">
<style type="text/css">
	.switch_label { position: absolute; margin-left: 20px; }
	.panel-body.resource h3 { background-color: #F9F9F9; margin-bottom: 30px; padding: 15px; border-radius: 10px; margin-top: 0px !important; border-bottom-left-radius: 0px; border-bottom-right-radius: 0px; }
	.panel-body.resource .resource_content { border: 1px solid #E8E8E8; border-radius: 10px; padding-bottom: 20px; }
	
</style>
@endpush
@push("js")
<script type="text/javascript" src="js/multiselect.js"></script>
<script type="text/javascript">
$(function(){ $('{{ implode(",",$JSID) }}').multiSelect({
	selectableHeader: "<div class='label label-info'>Available Resources</div>",
	selectionHeader: "<div class='label label-primary'>Selected Resources</div>"
}); })
</script>
@endpush