// JavaScript Document

$(function(){
	VDoMC_Close();
	LoadUnusrLog(0);
	LoadMappedLog(0)
	$('body').append(GetBSModal('Customer Details').attr('id','modalCustomerDetails'));
	SetupModalTable();
});

var unusr_data = false;
var neg_off = 0;
var current_vdmc = [];
var customer_details = {};
var selected_customer = '';

function LoadUnusrLog(p){
	FireAPI('api/v1/log/unusr?page='+p,DistributeUnusr);
	CreateTableHeads()
}

function LoadMappedLog(p){
	FireAPI('api/v1/log/unusr/mapped',DistributeMapStatus,{page:p});
}

function DistributeUnusr(R){
	TBD = $('tbody.unusr_tbl_bd');
	if(R.length === 0) return alert("No More Data Available");
	if(unusr_data !== false) unusr_data = $.extend(true,R,unusr_data)
	else unusr_data = R;
	row = 0;
	TBD = $('tbody.unusr_tbl_bd').empty();
	$.each(R,function(cmp,cmpObj){
		$.each(cmpObj, function(brc, brcObj){
			eml = brcObj[0]; phn = brcObj[1];
			$.each(brcObj[2],function(app,appObj){
				pid = appObj[0];
				$.each(appObj[1],function(ver,verObj){
					TR = $('<tr>').appendTo(TBD).attr({'data-cmp':cmp,'data-brc':brc,'data-pid':pid});
					$('<td>').text(++row).appendTo(TR);
					$('<td>').text(cmp).appendTo(TR);
					$('<td>').text(brc).appendTo(TR);
					$('<td>').text(app).appendTo(TR);
					$('<td>').text(ver).appendTo(TR);
					$('<td class="map_status">').text('unavailable').appendTo(TR);
					$('<td>').html(btn('View Details or Map Customer','javascript:VDoMC("'+cmp+'","'+brc+'","'+app+'","'+ver+'")','option-vertical')).appendTo(TR);
				})
			})
		})
	})
}

function DistributeMapStatus(M){
	if(Object.keys(M).length == 0) return;
	TBD = $('tbody.unusr_tbl_bd');
	$.each(M,function(pid,pidObj){
		$.each(pidObj,function(brc,DA){
			$('tr[data-pid="'+pid+'"][data-brc="'+brc+'"]').find('.map_status').text('Mapped to '+DA[0]);
		})
	})
}

function CreateTableHeads(){
	THD = $('<tr>').appendTo($('thead.unusr_tbl_hd').empty());
	Heads = ['No','Company','Branch','Software','Version','Current Map Status','Action'];
	THD.html($.map(Heads,function(head){
		cls = head.toLowerCase().replace(/\s/g,"_");
		return $('<th>').addClass(cls).text(head)
	}))
}

function VDoMC(C,B,S,V){
	VDoMC_Panel(1);
	SetVDMCSection();
	TRAry = GetUnusrDetailsTR();
	$('.panel:eq(1) .col:first tbody').html(TRAry);
	FillUnusrDetailsTR(C,B,S,V);
	current_vdmc = [C,B,S,V];
}

function SetVDMCSection(){
	VDMCBody = $('.panel:eq(1) .panel-body').html(getGrid());
	SetDetailsTable(VDMCBody);
	SetMapPanel(VDMCBody);
}

function SetDetailsTable(VDMCBody){
	VDMCBody.find('.col.col-md-5').html(GetBSTable('striped')).css({/*'border-right':'1px solid #DDD',*/'padding-right':'0px'})
}

function getGrid(){
	return $('<div>').addClass('row').html([$('<div>').addClass('col col-md-5'),$('<div>').addClass('col col-md-7')]);
}

function GetUnusrDetailsTR(){
	heads = ['Company Name','Branch Code','Software','Version','Product ID','Email','Phone','Logins'];
	return $.map(heads,function(head){
		cls = head.toLowerCase().replace(/\s/g,"_");
		return $('<tr>').addClass(cls).html([$('<th>').addClass('th_data').text(head),$('<td>').addClass('td_data').text('')])
	})
}

function FillUnusrDetailsTR(C,B,S,V){
	heads = ['Company Name','Branch Code','Software','Version','Product ID','Email','Phone','Logins'];
	$.each(heads,function(i,head){
		cls = head.toLowerCase().replace(/\s/g,"_");
		$('tr.'+cls+' .td_data').html(window['FUDTR'](cls,C,B,S,V))
	})
}

function FUDTR(cls,C,B,S,V){
	D = unusr_data[C][B];
	switch (cls) {
		case 'company_name':
			return C;
			break;
		case 'branch_code':
			return B;
			break;
		case 'software':
			return S;
			break;
		case 'version':
			return V;
			break;
		case 'product_id':
			return D[2][S][0];
			break;
		case 'email':
			return D[0];
			break;
		case 'phone':
			return D[1];
			break;
		case 'logins':
			VERs = D[2][S][1][V];
			DL=$('<dl>')
			$.each(VERs,function(Dt,TmAry){
				$('<dt>').text(Dt).appendTo(DL);
				TmAR = TmAry.reverse()
				$('<dd>').text($.map(TmAR,function(Tm){ return ToAmPm(Tm); }).join(", ")).appendTo(DL);
			})
			return DL;
			break;
	}
}

function ToAmPm(Tm){
	TmA = Tm.split(":");
	AP = (parseInt(TmA[0]) < 12) ? "AM" : "PM";
	TmA[0] = (parseInt(TmA[0]) < 13) ? TmA[0] : TmA[0]-12;
	return TmA.join(":") + " " + AP;
}

function VDoMC_Panel(S){
	$('.panel:eq(1)')[(S)?'slideDown':'slideUp'](250);
}

function VDoMC_Close(){
	VDoMC_Panel(0);
}

function LoadMore(){
	LoadUnusrLog(++neg_off)
}

function SetMapPanel(VDMCBody){
	TBL = VDMCBody.find('.col.col-md-7').html(GetBSPanel('Map to Customer')).find('.panel-body').html(GetBSTable('striped map_customer')).find('table');
	VDMCBody.find('.col.col-md-7 .panel-footer').html(btn('Map Customer','javascript:MapSelectedCustomer()','share-alt').append('  Map selected customer to this log').addClass('pull-right btn-info'))
	$('thead',TBL).html(GetSearchTR());
}

function GetSearchTR(){
	SrchFields = ['Select Search By','Company Name','Email','Phone','Distributor'];
	select = $('<select name="search_by" class="form-control" onchange="SearchByChanged(this.value)">').html($.map(SrchFields,function(fld){ cls = fld.toLowerCase().replace(/\s/g,'_'); return GetSelOption(fld,cls).addClass(cls); }));
	input = $('<input name="search_text" class="form-control">');
	search = btn('Search','javascript:SearchCustomer()','search').addClass('btn-info').append(' Search for Customers');
	return [$('<tr>').html([$('<td>').html(select),$('<td>').html(input),$('<td>').html(search)]),$('<tr class="result_head" style="display:none">').html([$('<th>').text('Company'),$('<th>').text('Distributor'),$('<th>').text('View/Select')]),$('<tr class="result_unavailable" style="display:none">').html($('<th colspan="3" class="text-center">').text('No results found!'))];
}

function SearchByChanged(Value){
	$('[name="search_text"]').prop('disabled',Value == 'select_search_by')
	if(Value == 'company_name') return $('[name="search_text"]').val(current_vdmc[0]);
	if(Value == 'email') return $('[name="search_text"]').val(unusr_data[current_vdmc[0]][current_vdmc[1]][0]);
	if(Value == 'phone') return $('[name="search_text"]').val(unusr_data[current_vdmc[0]][current_vdmc[1]][1]);
	if(Value == 'distributor') return $('[name="search_text"]').val('');
}

function SearchCustomer(){
	S = current_vdmc[2];
	B = $('[name="search_by"]').val(); if(B == 'select_search_by') return alert('Select, by in which field to be searched');
	T = $('[name="search_text"]').val();
	FireAPI('api/v1/log/unusr/map/search',SearchResult,{S:S,B:B,T:T})
}

function SearchResult(R){
	if(R.length === 0) return NoSearchResults(); else ResultsAvailable();
	customer_details = R;
	DistributeTableData('table.map_customer',customer_details,['customer.name','customer.distributor','action'],'MP');
}

function NoSearchResults(){
	$('tr.result_unavailable').css('display','table-row');
	$('tr.result_head').css('display','none');
	$('table.map_customer tbody').empty();
}

function ResultsAvailable(){
	$('tr.result_unavailable').css('display','none');
	$('tr.result_head').css('display','table-row');
	$('table.map_customer tbody').empty();
}

function MP_action(h,Obj,td,tr,ai,code){
	tr.attr({'data-code':code.split('-')[0],'class':'cust_'+code,'data-seqno':code.split('-')[1]});
	return [btn('View Details','javascript:ViewCustomerDetails("'+code+'")','user').append(' View').addClass('btn-info btn-sm'),$('<span>').text(" "),btn('Select','javascript:SelectThisCustomer("'+code+'")','play-circle').append(' Select').addClass('btn-info btn-sm')];
}

function ViewCustomerDetails(code){
	$('#modalCustomerDetails').modal('show').attr('data-code',code);
	FillModalCDData(code);
	
}

function SelectThisCustomer(code){
	code = code || $('#modalCustomerDetails').modal('hide').attr('data-code');
	$('tr.selected_customer').removeAttr('style').removeClass('selected_customer');
	$('tr.cust_'+code).addClass('selected_customer').css('background-color','#c5c5c5');
	selected_customer = code;
}

function SetupModalTable(){
	MBD = $('#modalCustomerDetails .modal-body');
	MBD.html(GetBSTable('striped modal_cd'));
	heads = ['Company Code','Company Name','Sequence No','Product','Edition','Distributor','Email','Phone','Address','Country','Product Id','Serial No','Registered On']
	TBD = $('table.modal_cd tbody').html($.map(heads,function(head){
		cls = head.toLowerCase().replace(/\s/g,'_');
		return $('<tr>').addClass(cls).html([$('<th>').addClass('modal_cd_th').text(head),$('<td>').addClass('modal_cd_td').text('')]);
	}))
	$('#modalCustomerDetails .modal-footer').html([btn('Close','','').append('Close').attr({'data-dismiss':"modal"}).addClass('btn-default'),btn('Select This Customer','javascript:SelectThisCustomer("")','play-circle').append(' Select This Customer').addClass('btn-info')])
}

function FillModalCDData(code){
	Data = customer_details[code]; C = Data.customer;
	Values = [code.split("-")[0],C.name,Data.seqno,Data.product,Data.edition,C.distributor,C.email,'+'+C.phonecode+'-'+C.phone,[C.address1,C.address2,C.city,C.state].join(', '),C.country,Data.product_id,Data.serialno,Data.registered_on];
	$.each(Values,function(ind,value){
		$('table.modal_cd tr:eq('+ind+') td.modal_cd_td').text(value);
	})
}

function MapSelectedCustomer(){
	SC = $('table.map_customer tr.selected_customer');
	if(SC.length == 0) return alert('Please select a customer');
	cus = SC.attr('data-code'); seq = SC.attr('data-seqno'); pid = unusr_data[current_vdmc[0]][current_vdmc[1]][2][current_vdmc[2]][0]; brc = current_vdmc[1];
	DoMap(pid,brc,cus,seq,0)
}

function DoMap(pid,brc,cus,seq,frc){
	FireAPI('api/v1/log/unusr/map',MapStatus,{pid:pid,brc:brc,cus:cus,seq:seq,frc:frc})
	$('#modalMapExist').modal('hide');
}

function MapStatus(R){
	AS = R[0]; MS = R[1];
	if(AS){
		if(MS) return alert('Map Data added successfully.');
		else return MapDataExists(R);
	} else {
		
	}
}

function MapDataExists(R){
	modal = MapConfirmModal().modal('show');
	FillMapExistModal(R);
	MapExistConfirmButton(R);
}

function MapConfirmModal(){
	if($('#modalMapExist').length > 0) return $('#modalMapExist');
	return CreateMapConfirmModal();
}

function CreateMapConfirmModal(){
	Modal = GetBSModal('Action Required, MAP Data already exists!!').attr('id','modalMapExist');
	$('.modal-body',Modal).html(CreateMapExistTable());
	return Modal.appendTo($('body'));
}

function CreateMapExistTable(){
	Tbl = GetBSTable('striped map_exists')
	Tbl.find('thead').html([
		$('<tr>').append($('<th>').text('Product ID')).append($('<th colspan="2" class="me_pid">')),
		$('<tr>').append($('<th>').text('Branch')).append($('<th colspan="2" class="me_brc">'))
	]).next().html([
		$('<tr>').append($('<th>').text('')).append($('<th>').text('Customer')).append($('<th>').text('Sequence No')),
		$('<tr>').append($('<th>').text('Existing Data')).append($('<td class="ex_cus">')).append($('<td class="ex_seq">')),
		$('<tr>').append($('<th>').text('Applyed Data')).append($('<td class="ap_cus">')).append($('<td class="ap_seq">')),
	]);
	return Tbl;
}	
	
function FillMapExistModal(R){
	TBL = $('table.map_exists');
	TBL.find('.me_pid').text(R[3][0]);
	TBL.find('.me_brc').text(R[3][1]);
	TBL.find('.ex_cus').text(R[2][0]);
	TBL.find('.ex_seq').text(R[2][1]);
	TBL.find('.ap_cus').text(R[3][2]);
	TBL.find('.ap_seq').text(R[3][3]);
}

function MapExistConfirmButton(R){
	M = R[3];
	MapConfirmModal().find('.modal-footer').html([
		btn('Close','','').attr({class:'btn btn-default','data-dismiss':'modal'}).append('Close'),
		btn('Force create MAP Data','javascript:DoMap("'+M[0]+'","'+M[1]+'","'+M[2]+'","'+M[3]+'","1")','retweet').attr({class:'btn btn-default'}).append(' Force create MAP Data.').addClass('btn-info')
	])
}
//btn('Force create MAP Data','javascript:alert("'+M[0]+'","'+M[1]+'","'+M[2]+'","'+M[3]+'","1")','retweet').attr({class:'btn btn-default','data-dismiss':'modal'}).append(' Force create MAP Data.').addClass('btn-info')