@extends("mc.page")
@include('BladeFunctions')
@section("content")

<div class="content">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Search for Customer</strong>{!! PanelHeadBackButton(url()->previous()) !!}</div>
		<div class="panel-body">
			<div class="row">
				<div class="col col-md-4"><form onSubmit="return false">
					<div class="form-group form-horizontal clearfix">
						<label class="control-label col-xs-4">Search hint</label>
						<div class="col-xs-8"><input type="text" name="search_text" class="form-control" placeholder="code/name/email/phone/address"></div>
					</div>
					<div class="col-xs-offset-4 col-xs-8">
						<button type="submit" class="btn btn-info btn-sm pull-left" onClick="SearchCustomer()">Search</button>
					</div>
				</form></div>
				<div class="col col-md-8">
					<div class="panel panel-default">
						<div class="panel-heading"><div class="panel-title">Search Results</div></div>
						<div class="panel-body">
							<div class="table table-responsive yes_results"><table class="table table-striped"><thead><tr><th>No</th><th>Customer</th><th>Create Contract for</th></tr></thead><tbody>
								
							</tbody></table></div>
							<div class="jumbotron no_results" style="display: none">No results found</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
@push('js')
<script type="text/javascript">
var _NCUrl = '{{ Route('mc.new') }}';
function SearchCustomer(){
	_st = $('[name="search_text"]').val();
	if($.trim(_st) == "") return alert('Please enter any text');
	FireAPI('api/v1/mc/get/sc',function(cj){
		if(cj.length) return DSR(cj);
		return $('.yes_results').slideUp().next().slideDown();
	},{st:_st})
}
function DSR(cj){
	container = $('.no_results').slideUp().prev().slideDown().find('tbody').empty();
	$.each(cj,function(i,Obj){
		Data = []; Data[0] = Obj.code; Data[1] = Obj.name;
		Data[2] = DSR_Address(Obj.details); Data[3] = DSR_Email(Obj.logins); Data[4] = DSR_Phone(Obj.details);
		Products = DSR_Products(Obj.registration);
		AddCSData(container, Data, Products, _NCUrl)
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
function DSR_Products(R){
    var PRD = new Object({});
    if(R.length>1)
        $.each(R,function(i,P){ rem = P.remarks ? '('+P.remarks+')' : null; PRD[P.seqno] = [P.product.name,P.edition.name,'Edition',rem].join(' '); });
    else
        $.each(R,function(i,P){ PRD[P.seqno] = [P.product.name,P.edition.name,'Edition'].join(' '); });
    return PRD;
}
function AddCSData(C,D,P,U){
	DC = ['code','name','address','email','phone'];
	divs = $.map(D,function(Q,i){ return $('<div>').addClass(DC[i]).text(Q) });
	prds = $.map(P,function(N,seq){ return $('<a>').css('margin','0px 2px 2px 0px').attr({'href':U+'?u='+D[0]+'&s='+seq}).addClass('btn btn-info btn-sm').text(N); });
	PutTR(C,divs,prds);
}
function PutTR(C,D,P){
	TR1 = $('<tr>').html($('<th class="seq_no" style="vertical-align:middle">').attr('rowspan',1).text(C.find('tr').length+1));
	TR1.append($('<td class="details">').html(D));
	TR1.append($('<td class="products" style="vertical-align:middle">').html(P));
	//TR2 = $('<tr>').html($('<td class="products">').html($('<strong>').text('Create contract for: ')).append(P));
	C.append(TR1);
}
</script>
@endpush