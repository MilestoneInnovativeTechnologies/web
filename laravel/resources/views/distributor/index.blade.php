@extends("distributor.page")
@include('BladeFunctions')
@section("content")
@php
$ORM = new \App\Models\Distributor;
if(Request()->search_text){ $st = '%'.Request()->search_text.'%'; $ORM = $ORM->where(function($Q)use($st){ $Q->where('name','like',$st)->orWhere('code','like',$st)->orWhereHas('Details',function($Q)use($st){ $Q->where('phone','like',$st); })->orWhereHas('Logins',function($Q)use($st){ $Q->where('email','like',$st); }); }); }
$Distributors = $ORM->paginate(10);
@endphp

<div class="content">
	
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Distributors</strong>@if(in_array('create',$Distributors->first()->available_actions)) <a href="{{ Route('distributor.create')}}" class="btn btn-info pull-right btn-sm"><span class="glyphicon glyphicon-plus"></span> Create New Distributor</a> @endif</div>
		<div class="panel-body">
			<div class="clearfix pagination">
				<div class="pull-left col-xs-4 p0"><div class="input-group"><form><input type="text" name="search_text" class="form-control" placeholder="Search for distributors" value="{{ Request()->search_text }}" autofocus></form><a href="javascript:SearchText()" class="input-group-addon"><span class="glyphicon glyphicon-search"></span></a></div></div>
				<div class="pull-right">{{ $Distributors->appends(['search_text' => Request()->search_text])->links() }}</div>
			</div>
			<div class="table-responsive">
				<table class="table table-bordered">
					<thead><tr><th>No</th><th>Name</th><th>Address</th><th>Contact</th><th>Actions</th></tr></thead>
					<tbody>@forelse($Distributors as $Dist)
					<tr>
						<td>{{ $loop->iteration }}</td>
						<td><small>{{ $Dist->code }}</small><br><strong>{{ $Dist->name }}</strong></td>
						<td>{!! PartnerAddress($Dist->Details) !!}</td>
						<td>{!! PartnerContact($Dist->Logins, $Dist->Details) !!}</td>
						<td>{!! ActionsToListIcons($Dist) !!}</td>
					</tr>
					@empty
					<tr><td colspan="6" class="text-center"> No records exists</td></tr>
					@endforelse
					</tbody>
				</table>
			</div>
		</div>
	</div>

	
</div>

@endsection
@php
function PartnerAddress($Details){
	$Adr = []; $Loc = []; $AdrLines = [];
	if($Details->address1) $Adr[] = $Details->address1; if($Details->address2) $Adr[] = $Details->address2;
	$AdrLines[] = (empty($Adr)) ? "" : implode(", ",$Adr);
	if($Details->city){ $Loc[] = $Details->City->name; $Loc[] = $Details->City->State->name; }
	$AdrLines[] = (empty($Loc)) ? "" : implode(", ",$Loc);
	if($Details->city) $AdrLines[] = $Details->City->State->Country->name;
	return implode('<br>',$AdrLines);
}
function PartnerContact($L,$D){
	$Lines = [];
	if($D->phone) $Lines[] = ('Phone: ').(($D->phonecode)?("+".$D->phonecode."-"):("")).($D->phone);
	if($L && $L->isNotEmpty()){
		$email = $L->implode('email',', ');
		$Lines[] = ('Email: ').($email);
	}
	return implode('<br>',$Lines);
}
function ActionsToListIcons($Obj,$Prop = 'available_actions',$Pref = 'distributor',$PK = 'code',$Title = 'action_title',$Icon = 'action_icon',$Modal = 'modal_actions'){
	$LI = [];
	foreach($Obj->$Prop as $act){
		if(in_array($act,$Obj->$Modal)) continue;
		$LI[] = glyLink(Route($Pref.'.'.$act,[$Obj->$PK]),$Obj->$Title[$act],$Obj->$Icon[$act],['class' => 'btn', 'attr' => 'style="padding:6px 6px;"']);
	}
	return implode('',$LI);
}
@endphp