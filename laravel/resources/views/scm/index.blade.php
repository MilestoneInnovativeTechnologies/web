@extends("scm.page")
@include('BladeFunctions')
@section("content")
@php $ORM = \App\Models\PartnerRole::Customer()->select(['id','login','partner','role','rolename'])->with(['Login'	=>	function($Q){
			$Q->select('id','email');
		},'Partner'	=>	function($Q){
			$Q->select('code','name')->with(['Details','Parent'	=>	function($Q){
				$Q->with(['ParentDetails'	=> function($Q){
					$Q->select('code','name')->with(['Parent.ParentDetails'	=>	function($Q){ $Q->select('code','name'); }]);
				}]);
			}]);
		}])->latest(); $Data = $ORM->paginate(15); $Pagination = $Data->links(); @endphp
<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Customers</strong><div class="pull-right col-xs-4"><div class="input-group"><input type="text" placeholder="Search" name="customer_search_text" class="form-control"><span class="input-group-addon"><a href="javascript:SearchCustomer()"><span class="glyphicon glyphicon-search"></span></a></span></div></div></div>
		<div class="panel-body">@if($Data->isNotEmpty())
			<div class="pagination pull-right">{!! $Pagination !!}</div>
			<div class="table table-responsive">
				<table class="table table-striped">
					<thead><tr><th>No</th><th>Name</th><th>Address</th><th>Contact</th><th>Distributor</th><th>Action</th></tr></thead>
					<tbody>@foreach($Data as $Line)
						<tr>
							<td>{{ $loop->iteration }}</td>
							<td>{{ $Line->Partner->name }}</td>
							<td>{{ PartnerAddress($Line->Partner->Details) }}</td>
							<td>{!! PartnerContact($Line->Partner) !!}</td>
							<td>{{ PartnerParent($Line->Partner->Parent) }}</td>
							<td nowrap>{!! PartnerActions($Line->Partner) !!}</td>
						</tr>
					@endforeach</tbody>
				</table>
			</div>@else
			<div class="jumbotron text-center no-record"><h3>No Records found</h3></div>
		@endif</div>
	</div>
</div>

@endsection
@push('js')
<script type="text/javascript" src="js/scm_cs.js"></script>
@endpush
@push('css')
<style type="text/css">
	.pagination { margin: 0px !important; }
</style>
@endpush
@php
function PartnerAddress($D){
	$S = ', ';
	$Adr = [$D->address1,$D->address2];
	if($D->city) array_push($Adr,$D->City->name,$D->City->State->name,$D->City->State->Country->name);
	return implode($S, $Adr);
}
function PartnerContact($P){
	$No = ['+',$P->Details->phonecode,'-',$P->Details->phone];
	$Emails = $P->Logins->pluck('email')->toArray();
	return ' ' . join('',$No) . '<br/>' . ' ' . join('<br/>', $Emails);
}
function PartnerParent($Parent){
	$PName = $Parent->ParentDetails->name;
	if(strtolower($Parent->parent) == 'company') return $PName;
	return $Parent->ParentDetails->Parent->ParentDetails->name . '(' .$PName. ')';
}
function PartnerActions($P){
	return glyLink('javascript:ViewCustomer(\''.$P->code.'\')','View Details of '.$P->name,'list-alt',['class'=>'btn btn-none']) .
				 glyLink('javascript:ChangePresale(\''.$P->code.'\')','View Details of '.$P->name,'calendar',['class'=>'btn btn-none']) .
				 glyLink('javascript:LoginReset(\''.$P->code.'\',\''.$P->name.'\',\''.$P->Logins->first()->email.'\')','Send login reset mail to '.$P->name,'log-in',['class'=>'btn btn-none'])
	;
}
@endphp