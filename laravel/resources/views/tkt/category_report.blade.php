@extends("tkt.page")
@section("content")
@php
$Ticket = \App\Models\Ticket::with('Product','Edition','Customer.ParentDetails')->get();
$Category = $Ticket->groupBy(function($item){ return ($item->category) ? $item->Category->name : 'Other'; });
//dd($Category->toArray());
@endphp

<div class="content">
	<div class="panel panel-default"><div class="panel-heading"><div class="panel-title">Category Report</div></div><div class="panel-body">
		<div class="col-md-6"><div class="table table-responsive"><table class="table striped"><tbody>
			<tr><th>Customer</th><th>:</th><td><input type="text" name="customer" class="form-control" value="All"></td></tr>
			<tr><th>Product</th><th>:</th><td><select name="product" class="form-control"><option value="All-All">All</option></select></td></tr>
			<tr><th>Distributor</th><th>:</th><td><input type="text" name="distributor" class="form-control" value="All"></td></tr>
		</tbody></table></div></div>
		<div class="col-md-6"><div class="table table-responsive"><table class="table striped"><tbody>
			<tr><th>Status</th><th colspan="2">&nbsp;</th></tr>
			<tr><th colspan="3">@foreach($Ticket->mapWithKeys(function($item){ return [$item->Cstatus->status => $item->Cstatus->status]; })->values() as $status)				
				<label class="checkbox-inline" style="margin:0px; width: 150px"><input type="checkbox" name="status[]" value="{{ $status }}" checked> {{ $status }}</label>
			@endforeach</th></tr>
		</tbody></table></div></div>
		<div class="table table-responsive"><table class="table report"><thead><tr><th style="vertical-align: middle">Category</th><th style="vertical-align: middle">Total</th><th style="vertical-align: middle">Specifications</th><th><select name="period" class="form-control pull-right" style="width:120px"><option value="0">Period</option>{!! GetPeriodOptions() !!}</select></th></tr></thead><tbody class="main">
		</tbody></table></div>
	</div></div>
</div>

@endsection
@push('js')
<script type="text/javascript">
var FilterData = {}
$(function(){
	$('[name="customer"]').autocomplete({
		minLength: 0,
		source: {!! $Ticket->mapWithKeys(function($item){ return [$item->customer => ['label' => $item->Customer->name, 'value' => $item->Customer->code]]; })->values()->prepend(['label' => 'All', 'value' => 'All'])->toJson() !!},
		focus: function(event, ui){ $('[name="customer"]').val(ui.item.label); FilterData["customer"] = [$.trim(ui.item.value)]; return false; },
		select: function(event, ui){ $('[name="customer"]').val(ui.item.label); FilterData["customer"] = [$.trim(ui.item.value)]; PopulateReport(); return false; }
	}).autocomplete( "instance" )._renderItem = function(ul, item) {
		return $( "<li>" ).appendTo( ul ).append( "<div>" + item.label + "</div>" );
	};
	$('[name="distributor"]').autocomplete({
		minLength: 0,
		source: {!! $Ticket->mapWithKeys(function($item){ $Distributor = ($item->Customer->ParentDetails[0]->Roles->contains('name','distributor')) ? $item->Customer->ParentDetails[0] : $item->Customer->ParentDetails[0]->ParentDetails[0]; return [$Distributor->code => ['label' => $Distributor->name, 'value' => $Distributor->code]]; })->values()->prepend(['label' => 'All', 'value' => 'All'])->toJson() !!},
		focus: function(event, ui){ $('[name="distributor"]').val(ui.item.label); FilterData["distributor"] = [$.trim(ui.item.value)]; return false; },
		select: function(event, ui){ $('[name="distributor"]').val(ui.item.label); FilterData["distributor"] = [$.trim(ui.item.value)]; PopulateReport(); return false; }
	}).autocomplete( "instance" )._renderItem = function(ul, item) {
		return $( "<li>" ).appendTo( ul ).append( "<div>" + item.label + "</div>" );
	};
	$('[name="product"]').append(getSimpleOptions({!! $Ticket->mapWithKeys(function($item){ return [$item->product . '-' . $item->edition => $item->Product->name . ' ' . $item->Edition->name . ' Edition']; })->toJson() !!})).on('change',function(){ FilterData['product'] = [this.value.split('-')[0]]; FilterData['edition'] = [this.value.split('-')[1]]; PopulateReport(); });
	$('[name="status[]"]').on('click',function(){ FilterData['status'] = GetFilterStatus(); PopulateReport(); }).filter('[value="COMPLETED"],[value="DISMISSED"],[value="RECREATED"]').removeAttr('checked').end().filter('[value="CLOSED"]').trigger('click');
	$('[name="period"]').on('change',function(){ FilterData['from'] = [parseInt(this.value.split('&')[0])]; FilterData['to'] = [(this.value.split('&')[1])?(parseInt(this.value.split('&')[1])):parseInt('{{ strtotime(date("Y-m-d 23:59:59")) }}')]; PopulateReport(); });
	
	$('[name="period"]').trigger('change');
})
function GetFilterStatus(){ return $('[name="status[]"]:checked').map(function(i,a){ return($(a).val()) }).toArray(); }
function GetDistributorCode(T){
	P = T.customer.parent_details[0];
	if(ArrayHas(P.roles,'name','distributor')) return P.code;
	return P.parent_details[0].code;
}
function ArrayHas(a,f,v){
	for(x in a)
		if(a[x][f] == v)
			return true;
	return false;
}
function ShowTickets(Tickets){
	getReportTable().empty();
	$.each(Tickets,function(i,T){
		Category = GetCategory(T);
		TR = CreateTblRow(Category,(T.category)?T.category.specs:null,(T.category)?T.category.code:null);
		IncrementTotal(TR); IncrementSpecs(TR,T.category_specs); IncrementSeqs(TR,T.category_specs);
	});
	AnchorCategoryTotal(); AnchorSpecTableCount(); AnchorSeqTableCount();
}
function GetCategory(T){
	return (T.category) ? T.category.name : 'Other';
}
function CreateTblRow(C,S,CC){
	tbd = getReportTable(); cls = rawtext(C); if($('tr.'+cls,tbd).length) return $('tr.'+cls,tbd);
	return GetCreateRow(C,S,CC).appendTo(tbd);
}
function getReportTable(){ return $('table.report tbody.main'); }
function rawtext(t){ return t.toLowerCase().replace(/\s/g,'_').replace(/\\/g,'_').replace(/\//g,'_'); }
function GetCreateRow(C,S,CC){
	return $('<tr>').addClass(rawtext(C)).attr('data-cat-code',CC).html([
		$('<td>').addClass('category_name').text(C),
		$('<td>').addClass('category_total').text('0'),
		$('<td>').addClass('category_specs').html(SpecsTable(S)).attr({colspan:2}),
	]);
}
function IncrementTotal(TR){
	TD = $('.category_total',TR); IncrementInnerText(TD);
}
function IncrementInnerText(D){
	D.text(parseInt(D.text())+1);
}
function IncrementSpecs(TR,s){
	if($.isEmptyObject(s)) return;
	$.each(s,function(l,s1){ if(!s1.value) return; D = $('td.'+rawtext(s1.specification.name),TR).find('div.'+rawtext(s1.value.name)).find('span'); IncrementInnerText(D); })
}
function SpecsTable(S){
	if(!S) return '-';
	return [SpecTable(S),SeqTable(S)];
}
function SpecTable(S){
	Tbl = $('<table>').addClass('table table-bordered table-condensed spec_table').css({ fontSize:'12px' }).html([$('<thead>').html($('<tr>')),$('<tbody>').html($('<tr>'))]); thd = $('thead tr',Tbl); tbdy = $('tbody tr',Tbl);
	$.each(S,function(j,S1){ if($.isEmptyObject(S1.spec_values)) return; $('<th>').text(S1.name).appendTo(thd); mtd = $('<td>').addClass(rawtext(S1.name)+" spec_spec_column").attr('data-spec',S1.code).appendTo(tbdy); $.each(S1.spec_values,function(k,S2){ mtd.append($('<div>').css('width','110px').addClass(rawtext(S2.name)).attr('data-spec-value',S2.code).html([S2.name,$('<span>').addClass('pull-right spec_count').text(0)])) }); })
	return Tbl;
}
function SeqTable(S){
	Tbl2 = $('<table>').addClass('table table-bordered table-condensed seq_table').css({ fontSize:'12px' }).html([$('<thead>').html($('<tr>').html($('<th>').attr('colspan',S.length+1).text(ArrayImplode(S,'name',' - ')))),$('<tbody>')]);
	return Tbl2;
}
function ArrayImplode(Ary,Nme,Dmt){
	return ArrayColumn(Ary,Nme).join(Dmt);
}
function ArrayColumn(Ary,Col){
	Cols = []; $.each(Ary,function(i,Ele){ Cols.push(getObjValue(Ele,Col)) });
	return Cols;
}
function ArrayColumn2(Ary,Col1,Col2){
	Cols = []; $.each(Ary,function(i,Ele){ V = getObjValue(Ele,Col1) ? getObjValue(Ele,Col1) : getObjValue(Ele,Col2); Cols.push(V); });
	return Cols;
}
function getObjValue(Obj,Name){
	if(Name.indexOf('.') == -1 || Name.indexOf(".") === false) return Obj[Name];
	V = Obj; $.each(Name.split('.'),function(m,P){ V = (V) ? V[P] : null; });
	return V
}
function IncrementSeqs(TR,s){
	if(!s || $.isEmptyObject(s)) return;
	tbd2 = $('table.seq_table tbody',TR);
	cls1 = getSeqTRClass(s); IncrementInnerText(getOrCreateSeqRow(tbd2,cls1,s).find('.seq_count'));
}
function getSeqTRClass(s){
	return rawtext(ArrayColumn2(s,'value_text','value.name').join('_'));
}
function getOrCreateSeqRow(tbd,cls,s){
	if($('tr.'+cls,tbd).length) return $('tr.'+cls); count_attr = [];
	tr2 = $('<tr>').addClass(cls); $.each(ArrayColumn2(s,'value_text','value.name'),function(n,v){ $('<td>').text(v).appendTo(tr2); count_attr.push(s[n].value.code) });
	return tr2.append($('<th>').addClass('seq_count').attr('data-spec-vals',count_attr.join('-')).text('0')).appendTo(tbd);
}
function PopulateReport(){
	T = {!! $Ticket->toJson() !!}; Filters = ['StatusCheck','CustomerCheck','DistributorCheck','ProductCheck','EditionCheck','CheckFrom','CheckTo']; FT = T;
	$.each(Filters,function(i,Filter){ FT = FT.filter(window[Filter]) });
	ShowTickets(FT);
}
function AnchorCategoryTotal(){
	href = GetAnchorHref();
	DoAnchorCategoryTotal(href);
}
function AnchorSpecTableCount(){
	href = GetAnchorHref();
	DoAnchorSpecTableCount(href);
}
function AnchorSeqTableCount(){
	href = GetAnchorHref();
	DoAnchorSeqTableCount(href);
}
function DoAnchorCategoryTotal(href){
	$('.category_total').each(function(i,a){ cc = $(a).parents('tr').attr('data-cat-code'); cc = (cc?cc:'null'); $a = LinkAnchor($(a).text(),href+"&category="+cc); $(a).html($a); })
}
function DoAnchorSpecTableCount(href){
	$('.spec_count',$('.spec_table')).each(function(i,span){
		$span = $(span); params = [href]; count = $span.text();
		spec_value = $span.parents('[data-spec-value]').attr('data-spec-value'); params.push('values[]='+spec_value);
		spec = $span.parents('[data-spec]').attr('data-spec'); params.push('spec='+spec);
		cat = $span.parents('[data-cat-code]').attr('data-cat-code'); params.push('category='+cat);
		$a = LinkAnchor(count,params.join('&'));
		$span.html($a);
	})
}
function DoAnchorSeqTableCount(href){
	$('.seq_count',$('.seq_table')).each(function(i,th){
		$th = $(th); params = [href]; count = $th.text();
		spec_value = $th.attr('data-spec-vals'); params.push('values[]='+spec_value.replace(/-/g,'&values[]='));
		cat = $th.parents('[data-cat-code]').attr('data-cat-code'); params.push('category='+cat);
		$a = LinkAnchor(count,params.join('&'));
		$th.html($a);
	})
}
function GetAnchorHref(){
	return [CategoryTicketsPage(),Obj2Params(FilterData)].join('?')
}
function CategoryTicketsPage(){
	return '{{ route("category.tickets") }}';
}
function Obj2Params(Obj){
	params = [];
	$.each(Obj,function(k,v){
		if(typeof v == 'string') params.push(k+"="+v);
		if(typeof v == 'object'){
			if(v.length === 1) params.push(k+"="+v[0]);
			else $.each(v,function(i,a){ params.push(k+"[]="+a) })
		}
	});
	return params.join('&');
}
function LinkAnchor(text,href){
	return getHtml('a',{ target:'_blank', href:href },text);
}
function getHtml(tag,attr,html){
	tag = tag || 'div'; html = html || ( (typeof attr == 'object')?'':attr ); attr = (typeof attr == 'object') ? attr : { class:'' };
	return $('<'+tag+'>').html(html).attr(attr);
}
function StatusCheck(Obj){ return (FilterData && FilterData['status'] && $.isArray(FilterData['status']) && FilterData['status'].length && ($.inArray(Obj.cstatus.status,FilterData['status'])>-1)); }
function CustomerCheck(Obj){ return (FilterData && FilterData['customer'] && $.isArray(FilterData['customer']) && FilterData['customer'][0] != "All") ? $.inArray(Obj.customer.code,FilterData['customer'])>-1 : true; }
function DistributorCheck(Obj){ if(FilterData && FilterData['distributor'] && $.isArray(FilterData['distributor']) && FilterData['distributor'][0] != "All"){ return $.inArray(GetDistributorCode(Obj),FilterData['distributor'])>-1 } else return true; }
function ProductCheck(Obj){ return (FilterData && FilterData['product'] && $.isArray(FilterData['product']) && FilterData['product'][0] != "All") ? $.inArray(Obj.product.code,FilterData['product'])>-1 : true; }
function EditionCheck(Obj){ return (FilterData && FilterData['edition'] && $.isArray(FilterData['edition']) && FilterData['edition'][0] != "All") ? $.inArray(Obj.edition.code,FilterData['edition'])>-1 : true; }
function CheckFrom(Obj){ return (FilterData && FilterData['from'] && $.isArray(FilterData['from']) && FilterData['from'][0] != "0") ? (parseInt(Obj.status[0].start_time) >= FilterData['from'][0]) : true; }
function CheckTo(Obj){ return (FilterData && FilterData['to'] && $.isArray(FilterData['to']) && FilterData['to'][0] != "0") ? (parseInt(Obj.status[0].start_time) <= FilterData['to'][0]) : true; }
</script>
@endpush
@php
function GetPeriodOptions(){
	$Data = [strtotime(date('Y-m-d 00:00:00')) => 'Today', strtotime(date('Y-m-d 00:00:00',strtotime('-'.date('w').' days'))) => 'This Week', strtotime(date('Y-m-d 00:00:00',strtotime('-'.(date('w')+7).' days'))) . '&' . strtotime(date('Y-m-d 00:00:00',strtotime('-'.date('w').' days'))) => 'Last Week', strtotime(date('Y-m-01 00:00:00')) => 'This Month', strtotime(date('Y-m-d 00:00:00',strtotime('first day of last month'))) . '&' . strtotime(date('Y-m-01 00:00:00')) => 'Last Month', strtotime(date('Y-'.((intval((date('n')-1)/3)*3)+1).'-1')) => 'This Quarter', strtotime('-'.(((date('n')-1)%3)+3).' month',strtotime(date('Y-m-01'))) . '&' . strtotime(date('Y-'.((intval((date('n')-1)/3)*3)+1).'-1')) => 'Last Quarter', strtotime(date('Y-0'.((intval((date('n')-1)/6)*6)+1).'-01')) => 'This Half Year', strtotime('-'.(((date('n')-1)%6)+6).' month',strtotime(date('Y-m-01'))) . '&' . strtotime(date('Y-0'.((intval((date('n')-1)/6)*6)+1).'-01')) => 'Last Half Year', strtotime(date('Y-01-01')) => 'This Year', strtotime((date('Y')-1).'-01-01') . '&' . strtotime(date('Y-01-01')) => 'Last Year'];
	return implode("",array_map(function($Name, $Value){ return '<option value="'.$Value.'">'.$Name.'</option>'; },$Data,array_keys($Data)));
}
@endphp