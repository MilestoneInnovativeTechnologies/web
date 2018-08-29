@extends("tdt.page")
@include('BladeFunctions')
@section("content")
<?php
$Fields = ['code','name','description'];
$Values = []; $Update = isset($Update);
foreach($Fields as $Field){
	$V = ''; 
	if(old($Field) !== NULL) { $V = old($Field); }
	elseif(isset($Data)) {
		if(is_array($Data) && array_key_exists($Field,$Data)) $V = $Data[$Field];
		elseif(is_object($Data)){
			if($Data->$Field !== NULL) $V = $Data->$Field;
			elseif($Data[$Field] !== NULL) $V = $Data[$Field];
			else $V = "";
		}
	} else {
		$V = '';
	}
	$Values[$Field] = $V;
}
?>
<div class="content">
	<div class="row">
		<div class="col col-md-2"></div>
		<div class="col col-md-8"><form method="post" action="{{ ($Update) ? Route('tdt.update',['supportType'	=>	$Values['code']]) : Route('tdt.store') }}">{{ csrf_field() }}{{ ($Update) ? method_field('put') : '' }}
			<div class="panel panel-default">
				<div class="panel-heading"><strong>{{ ($Update) ? 'Edit Ticket Detail Type' : 'Add Ticket Detail Type'}}</strong>{!! glyLink(Route('tdt.index'),'Go Back','arrow-left', ['text'=>' Back', 'class'=>'btn btn-default btn-sm pull-right']) !!}</div>
				<div class="panel-body">
					<div class="clearfix">
						<div class="col-xs-6">
							{!! formGroup(1,'code', 'text', 'Ticket Detail Type Code', $Values['code']) !!}
						</div>
						<div class="col-xs-6">
							{!! formGroup(1,'name', 'text', 'Ticket Detail Type Name', $Values['name']) !!}
						</div>
					</div>
					<div class="clearfix">
						<div class="col-xs-12">{!! formGroup(1,'description', 'textarea', 'Description', $Values['description'], ['style'=>'height:120px']) !!}</div>
					</div>
				</div>
				<div class="panel-footer clearfix">
					<input type="submit" name="submit" value="{{ ($Update) ? 'Update' : 'Submit' }}" class="btn btn-info pull-right">
				</div>
			</div></form>
		</div>
		<div class="col col-md-2"></div>
	</div>
</div>

@endsection