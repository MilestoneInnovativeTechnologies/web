@extends("tst.page")
@include('BladeFunctions')
@section("content")
@php
$ORM = \App\Models\Partner::with('Details.City.State.Country','Logins','Roles')->latest();
if(Request()->search_text){ $st = '%'.Request()->search_text.'%'; $ORM = $ORM->where(function($Q)use($st){ $Q->where('name','like',$st)->orWhere('code','like',$st)->orWhereHas('Details',function($Q)use($st){ $Q->where('phone','like',$st); })->orWhereHas('Logins',function($Q)use($st){ $Q->where('email','like',$st); }); }); }
$Data = $ORM->paginate(10);
@endphp

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Partners</strong>{!! PanelHeadAddButton(Route('partner.create'),'Add New Partner') !!}</div>
		<div class="panel-body">
			<div class="clearfix pagination">
				<div class="pull-left col-xs-4 p0"><div class="input-group"><form><input type="text" name="search_text" class="form-control" placeholder="Search" value="{{ Request()->search_text }}" autofocus></form><a href="javascript:SearchText()" class="input-group-addon"><span class="glyphicon glyphicon-search"></span></a></div></div>
				<div class="pull-right">{{ $Data->appends(['search_text' => Request()->search_text])->links() }}</div>
			</div>@if($Data->isNotEmpty())
			<div class="table-responsive">
				<table class="table table-bordered">
					<thead><tr><th>No</th><th>Code</th><th>Name</th><th>Role</th><th>Email</th><th>Phone</th><th>Action</th></tr></thead>
					<tbody>@foreach($Data as $P)
						<tr><td>{{ $loop->iteration }}</td><td>{{ $P->code }}</td><td>{{ $P->name }}</td><td>{{ implode(", ",$P->Roles->pluck('displayname')->toArray()) }}</td><td>{{ implode(", ",$P->Logins->pluck('email')->toArray()) }}</td><td>+{{ $P->Details->phonecode . "-" . $P->Details->phone }}</td><td nowrap>
							{!! glyLink(Route('partner.show',['code'	=>	$P->code]),'View '.$P->name,'list-alt',['class'=>'btn']) !!}
							{!! glyLink('javascript:LoginSetup(\''.$P->code.'\',\''.$P->name.'\',\''.$P->Logins->first()->email.'\')','Send Login Setup Link to '.$P->name,'log-in',['class'=>'btn']) !!}
						</td></tr>
					@endforeach</tbody>
				</table>
			</div>@else
			<div class="jumbotron">
				<h2 class="text-center">No Records found</h2>
			</div>@endif
		</div>
	</div>
</div>

@endsection
@push('js')
<script type="text/javascript">
function LoginSetup(C,N,E){
	modal = getModal()
	setUpLSLModal(modal,['Code','Name','Email'],[C,N,E]).attr('data-code',C).modal('show');
	modal.find('.modal-footer').html(modalLSLConfirmButton());
}
function getModal(){
	ID = 'modalLoginSetup';
	if($('#'+ID).length) return $('#'+ID);
	return GetBSModal('Send Login Setup mail').attr('id',ID).appendTo('body');
}
function setUpLSLModal(modal, Fields, Values){
	tbd = modal.find('.modal-body').html(GetBSTable('striped LSL')).find('tbody');
	tbd.html($.map(Fields,function(Field,i){
		cls = Field.toLowerCase().replace(/\s/g,"_");
		return $('<tr>').addClass(cls).html([
			$('<th>').addClass('head').text(Field),
			$('<td>').addClass('body').text(Values[i])
		])
	}))
	return modal;
}
function modalLSLConfirmButton(){
	return btn('Send Login Setup link now','javascript:SendLSL()','share-alt').addClass('btn-info').append('  Send Login Setup link now');
}
function SendLSL(){
	code = getModal().attr('data-code');
	FireAPI('api/v1/mit/action/slsl/'+code,ConfirmSLL);
	modal.modal('hide');
}

function ConfirmSLL(R){
	alert('Login Setup link have successfully mailed to '+R[1]+', at, '+R[2]);
}

</script>
@endpush