@extends("cpo.page")
@include('BladeFunctions')
@section("content")

<div class="content">@if(Request()->c && Request()->s)<form method="post" enctype="multipart/form-data">{{ csrf_field() }}@endif
	<div class="panel panel-default">
		<div class="panel-heading"><strong>New Print Object</strong>{!! PanelHeadBackButton((url()->previous() == url()->current())?Route('cpo.index'):url()->previous()) !!}</div>
		<div class="panel-body">
			@if(!Request()->c || !Request()->s)
			<div class="row">
				<div class="col col-md-5"><form onSubmit="return false">
					<div class="form-group form-horizontal clearfix">
						<label class="control-label col-xs-6">Search &amp; Select customer</label>
						<div class="col-xs-6"><input type="text" name="search_text" class="form-control" placeholder="code/name/email/phone/address"></div>
					</div>
					<div class="col-xs-offset-6 col-xs-6">
						<button class="btn btn-info btn-sm pull-left" onClick="SearchCustomer()">Search</button>
					</div>
				</form></div>
				<div class="col col-md-7">
					<div class="panel panel-default">
						<div class="panel-heading"><div class="panel-title">Search Results</div></div>
						<div class="panel-body">
							<div class="table table-responsive yes_results"><table class="table table-striped"><thead><tr><th>No</th><th>Customer</th><th style="text-align: right">Presale</th><th>Create Print object for</th></tr></thead><tbody>
								
							</tbody></table></div>
							<div class="jumbotron no_results" style="display: none">No results found</div>
						</div>
					</div>
				</div>
			</div>
			@else
			<div class="clearfix">
				<div class="col-xs-6">
					@php $FS = \App\Models\CustomerPrintObject::where(['customer' => Request()->c,'reg_seq' => Request()->s])->pluck('function_name','function_code')->toArray(); @endphp
					{!! formGroup(2,'customer_name','text','Customer',Request()->n,['attr' => 'disabled', 'labelWidth' => 4]) !!}
					<div class="form-horizontal clearfix form-group">
						<label for="function_name" class="control-label col-xs-4">Function Name</label>
						<div class="col-xs-8">
							<select name="function_name" class="form-control" id="function_name" onChange="FunctionNameChanged(this.value)"><option value="0">Select Function</option>@unless(empty($FS)) @foreach($FS as $FSc => $FSn) <option value="{{ $FSn }}" data-code="{{ $FSc }}">{{ $FSn }}</option> @endforeach @endunless<option value="-1">New Function</option></select>
							<div class="new_function_name_div" style="display: none;">
								<input type="text" class="form-control reduce_width" id="new_function_name" name="new_function_name" placeholder="Enter new function name" value="">
								<a href="javascript:NoNewFunctionName()" title="Remove"><span class="glyphicon glyphicon-remove pull-right top_adjust"></span></a>
								<div class="clear"></div>
							</div>
						</div>
					</div>
					{!! formGroup(2,'function_code','text','Function Code','',['attr' => 'readonly required', 'labelWidth' => 4]) !!}
					<div class="form-horizontal clearfix form-group">
						<label for="print_name" class="control-label col-xs-4">Print Name</label>
						<div class="col-xs-8">
							<select name="print_name" class="form-control" id="print_name" onChange="PrintNameChanged(this.value)"><option value="">NONE</option><option value="-1">New Print Name</option></select>
							<div class="new_print_name_div" style="display: none;">
								<input type="text" class="form-control reduce_width" id="new_print_name" name="new_print_name" placeholder="Enter new print name" value="">
								<a href="javascript:NoNewPrintName()" title="Remove"><span class="glyphicon glyphicon-remove pull-right top_adjust"></span></a>
								<div class="clear"></div>
							</div>
						</div>
					</div>				
				</div>
				<div class="col-xs-6">
					{!! formGroup(2,'product_name','text','Product',Request()->p,['attr' => 'disabled']) !!}
					{!! formGroup(2,'file','file','File') !!}
					{!! formGroup(2,'preview','file','Preview Image') !!}
				</div>
				<input type="hidden" name="customer" value="{{ Request()->c }}"><input type="hidden" name="reg_seq" value="{{ Request()->s }}">
			</div>
			@endif
		</div>@if(Request()->c && Request()->s)
		<div class="panel-footer clearfix">
			<input type="submit" name="submit" value="Add Print Object" class="btn btn-primary pull-right">
		</div>@endif
	</div>
</form></div>

@endsection
@push('js')
<script type="text/javascript">
var _NPOUrl = '{{ Route("cpo.create") }}';
function SearchCustomer(){
	_st = $('[name="search_text"]').val();
	if($.trim(_st) == "") return alert('Please enter any text');
	FireAPI('api/v1/cpo/get/sc',function(cj){
		if(cj.length) return DSR(cj);
		return $('.yes_results').slideUp().next().slideDown();
	},{st:_st});
}
function DSR(cj){
	container = $('.no_results').slideUp().prev().slideDown().find('tbody').empty();
	$.each(cj,function(i,Obj){
		Data = []; Data[0] = Obj.code; Data[1] = Obj.name;
		Data[2] = DSR_Address(Obj.details); Data[3] = DSR_Email(Obj.logins); Data[4] = DSR_Phone(Obj.details);
		Presales = DSR_Presales(Obj.registration);
		Products = DSR_Products(Obj.registration);
		AddCSData(container, Data, Presales, Products, _NPOUrl)
	})
}
function DSR_Address(Adr){
	AdrData = [Adr.address1,Adr.address2];
	if(Adr.city) AdrData.push(Adr.city.name,Adr.city.state.name,Adr.city.state.country.name);
	return AdrData.join(', ')
}
function DSR_Email(Ls){
	return $.map(Ls,function(Obj){
		return Obj.email
	}).join(', ');
}
function DSR_Phone(D){
	return '+'+D.phonecode+'-'+D.phone;
}
function DSR_Presales(R){
	var PRS = [];	$.each(R,function(i,P){ PRS.push(gly_icon((P.registered_on)?'ok':'remove').css({display:'block',padding:'7px 0px'})); });
	return PRS;
}
function DSR_Products(R){
	var PRD = new Object({});
	if(R.length>1)
        $.each(R,function(i,P){ rem = P.remarks ? '('+P.remarks+')' : null; PRD[P.seqno] = [P.product.name,P.edition.name,'Edition',rem].join(' '); });
	else
		$.each(R,function(i,P){ PRD[P.seqno] = [P.product.name,P.edition.name,'Edition'].join(' '); });
	return PRD;
}
function AddCSData(C,D,S,P,U){
	DC = ['code','name','address','email','phone'];
	divs = $.map(D,function(Q,i){ return $('<div>').addClass(DC[i]).text(Q) });
	prds = $.map(P,function(N,seq){ return $('<a>').css('margin','0px 2px 2px 0px').attr({'href':U+'?c='+D[0]+'&s='+seq+'&n='+D[1]+'&p='+N}).addClass('btn btn-info btn-sm').text(N); });
	PutTR(C,divs,S,prds);
}
function PutTR(C,D,S,P){
	TR1 = $('<tr>').html($('<th class="seq_no" style="vertical-align:middle">').attr('rowspan',1).text(C.find('tr').length+1));
	TR1.append($('<td class="details">').html(D)).append($('<td class="psales" style="vertical-align:middle; text-align:right">').html(S));
	TR1.append($('<td class="products" style="vertical-align:middle">').html(P));
	//TR2 = $('<tr>').html($('<td class="products">').html($('<strong>').text('Create contract for: ')).append(P));
	C.append(TR1);
}
function FunctionNameChanged(val){
	if(val == "0") return;
	if(val == "-1") { $('#function_name').slideUp().next().slideDown(); $('[name="function_code"]').removeAttr('readonly').attr({placeholder:'Enter new function code'}).val(''); return; }
	$fc = $('[name="function_name"] option[value="'+val+'"]').attr('data-code');
	$('[name="function_code"]').val($fc); LoadPrintNames($fc);
}
function PrintNameChanged(val){
	if(val == "") return;
	if(val == "-1") return $('#print_name').slideUp().next().slideDown();
}
function NoNewFunctionName(){
	$('#function_name').slideDown().next().slideUp();
	$('[name="function_code"]').removeAttr('placeholder').attr('readonly',true).val('');
	$('#function_name').val("0");
}
function NoNewPrintName(){
	$('#print_name').slideDown().next().slideUp();
	$('#print_name option:first').attr('selected',true);
}
var _PrintNames = {};
function LoadPrintNames(fc){
	if(_PrintNames[fc]) return PopulatePrintNames(_PrintNames[fc]);
	FireAPI('api/v1/cpo/get/pn',function(D){ fc = Object.keys(D)[0]; _PrintNames[fc] = D[fc]; PopulatePrintNames(D[fc]); },{c:'{{ Request()->c }}',s:'{{ Request()->s }}',f:fc})
}
function PopulatePrintNames(data){
	PNS = $('[name="print_name"]'); $('option:gt(1)',PNS).remove();
	if(data) $.each(data,function(i,opt){ if(opt) $('<option>').attr('value',opt).text(opt).appendTo(PNS); });
}
</script>
@endpush
@push("css")
<link rel="stylesheet" type="text/css" href="css/datepicker.css">
<style type="text/css">
	.reduce_width { width: calc(100% - 20px); float: left !important; }
	.top_adjust { top: 9px; }
</style>
@endpush