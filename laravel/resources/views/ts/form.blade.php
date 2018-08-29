@extends("ts.page")
@include('BladeFunctions')
@section("content")
<?php
$Fields = ['code','name','description','customer_side_view','similiar_to_status','after','agent_status','customer_status'];
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
		<div class="col col-md-8"><form method="post" action="{{ ($Update) ? Route('ts.update',['ticketStatus'	=>	$Values['code']]) : Route('ts.store') }}">{{ csrf_field() }}{{ ($Update) ? method_field('put') : '' }}
			<div class="panel panel-default">
				<div class="panel-heading"><strong>{{ ($Update) ? 'Edit Ticket Status' : 'Add Ticket Status'}}</strong>{!! glyLink(Route('ts.index'),'Go Back','arrow-left', ['text'=>' Back', 'class'=>'btn btn-default btn-sm pull-right']) !!}</div>
				<div class="panel-body">
					<div class="clearfix">
						<div class="col-xs-6">
							{!! formGroup(1,'code', 'text', 'Ticket Status Code', $Values['code']) !!}
						</div>
						<div class="col-xs-6">
							{!! formGroup(1,'name', 'text', 'Ticket Status Name', $Values['name']) !!}
						</div>
					</div>
					<div class="clearfix">
						<div class="col-xs-12">{!! formGroup(1,'description', 'textarea', 'Description', $Values['description'], ['style'=>'height:100px']) !!}</div>
					</div>
					<div class="clearfix">
						<div class="col-xs-12">
							{!! formGroup(1,'customer_side_view', 'text', 'Customer side display status', $Values['customer_side_view']) !!}
						</div>
						<div class="col-xs-6">
							{!! formGroup(1,'similiar_to_status', 'select', 'Similiar to Status', $Values['similiar_to_status'], ['selectOptions'=>$SS]) !!}
						</div>
						<div class="col-xs-6">
							{!! formGroup(1,'after', 'select', 'This status comes after', $Values['after'], ['selectOptions'=>$PS]) !!}
						</div>
					</div>
					<div class="clearfix">
						<div class="col-xs-6">
							{!! formGroup(1,'agent_status', 'select', 'Agent Status', $Values['agent_status'], ['selectOptions'=>$MS]) !!}
						</div>
						<div class="col-xs-6">
							{!! formGroup(1,'customer_status', 'select', 'Customer Status', $Values['agent_status'], ['selectOptions'=>$MS]) !!}
						</div>
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