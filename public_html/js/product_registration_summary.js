// JavaScript Document

var _PRS_DetailURL = '/mit/list/registration/details'

function product_registration_summary_arrange_data(Data){
	
	if($.isEmptyObject(Data)) return NoPanelRecordExists($('.panel'+_Panels['product_registration_summary']),5);
	return PRS_ArrangeData(Data);
	
}

function PRS_ArrangeData(ArData){
	Reg = PRS_GetRegData(ArData[0])
	UnReg = PRS_GetUnRegData(ArData[1])
	Data = PRS_MergeRegUnreg(Reg, UnReg);
	return Data2Grid(Data);
}

function PRS_GetRegData(Data){
	ReturnObject = {};
	$.each(Data,function(i,Obj){
		P = Obj.product.code; E = Obj.edition.code; R = parseInt(Obj.reg_count);
		if(typeof ReturnObject[P] == 'undefined') ReturnObject[P] = { name:Obj.product.name };
		if(typeof ReturnObject[P][E] == 'undefined') ReturnObject[P][E] = { name:Obj.edition.name, reg:R }
	});
	return ReturnObject;
}

function PRS_GetUnRegData(Data){
	ReturnObject = {};
	$.each(Data,function(i,Obj){
		P = Obj.product.code; E = Obj.edition.code; R = parseInt(Obj.unreg_count);
		if(typeof ReturnObject[P] == 'undefined') ReturnObject[P] = { name:Obj.product.name };
		if(typeof ReturnObject[P][E] == 'undefined') ReturnObject[P][E] = { name:Obj.edition.name, unreg:R }
	});
	return ReturnObject;
}

function PRS_MergeRegUnreg(R,U){
	return $.extend(true,R,U);
}

function Data2Grid(Data){
	TR = []; GReg = 0; GUReg = 0;
	$.each(Data,function(PC,Obj){
		PReg = 0; PUReg = 0;
		$.each(Obj,function(EC,RU){ if(EC != 'name'){
			TD = [Obj.name]; TD.push(RU.name); TTD = 0;
			if(typeof RU.reg == 'undefined') { TD.push('0'); } else { C = parseInt(RU.reg); TD.push(PRS_ListHyperlink(C,PC,EC,'reg')); GReg += C; TTD += C; PReg += C; }
			if(typeof RU.unreg == 'undefined') { TD.push('0'); } else { C = parseInt(RU.unreg); TD.push(PRS_ListHyperlink(C,PC,EC,'unreg')); GUReg += C; TTD += C; PUReg += C; }
			TD.push(PRS_ListHyperlink(TTD,PC,EC)); TR.push(TD);
		} });
		TR.push([' ','Total',PRS_ListHyperlink(PReg,PC,'','reg'),PRS_ListHyperlink(PUReg,PC,'','unreg'),PRS_ListHyperlink(PReg+PUReg,PC)]);
	});
	TR.push(['Total',' ',PRS_ListHyperlink(GReg,'','','reg'),PRS_ListHyperlink(GUReg,'','','unreg'),PRS_ListHyperlink(GReg+GUReg)]);
	return TR;
}

function product_registration_summary_post_fill(Data, Json, Text, Ajax){
	tbody = $('.panel'+_Panels['product_registration_summary']+' tbody');
	PRS_ProductSpan(tbody);
	PRS_CentreAlignCols(tbody);
	PRS_BoldTotalRows(tbody);
	//PRS_AddHyperlinks()
}

function PRS_ProductSpan(tbody){
	prev = { 'ftd_data':null, 'tr':null };
	$('tr',tbody).each(function(i,tr){
		TR = $(tr); FTD = $('td:first',TR); ftd_data = FTD.text();
		if(ftd_data != prev['ftd_data'] && $.trim(ftd_data) != ""){ prev['ftd_data'] = ftd_data; prev['tr'] = TR; }
		else { PRS_IncTDRowSpan(prev['tr']); FTD.remove(); }
	})
}

function PRS_IncTDRowSpan(TR){
	TD = $('td:first',TR);
	CRS = TD.attr('rowspan') || 1; NRS = parseInt(CRS)+1;
	TD.attr('rowspan',NRS);
}

function PRS_CentreAlignCols(tbody){
	$('td[data-col="3"],td[data-col="4"],td[data-col="5"]',tbody).css({textAlign:'center'});
	$('td[data-col="1"]',tbody).css('vertical-align','middle')
}

function PRS_BoldTotalRows(tbody){
	$('td[data-col="1"]',tbody).css({fontWeight:'bold'});
	$('tr',tbody).each(function(i,tr){
		if($('td[data-col="2"]',$(tr)).text() == "Total") return $(tr).css({fontWeight:'bold'})
		if($('td[data-col="1"]',$(tr)).text() == "Total") return $(tr).css({fontWeight:'900'})
	})
}

function prs_period_options(){
	options = [{ text:'Today',value:prs_get_today() }, { text:'This Month', value:prs_get_current(1) }, { text:'Last Month', value:prs_get_previous(1) }, { text:'This Quarter',value:prs_get_current(3) }, { text:'Last Quarter',value:prs_get_previous(3) }, { text:'This Half Year',value:prs_get_current(6) }, { text:'Last Half Year',value:prs_get_previous(6) }, { text:'This Year',value:prs_get_current(12) }, { text:'Last Year',value:prs_get_previous(12) }, { text:'Total',value:'0' }];
	return { 'period':CreateNodes(options,'option','text',{value:'value'}) };
}

function PRS_AddHyperlinks(){
	//console.info('BOY');
}

function PRS_ListHyperlink(C,P,E,T){
	href = PRS_GetDetailsURL(P,E,T)
	return ($('<a>').text(C).attr({target:'_blank','href':href}).css({color:'inherit',textDecoration:'none'}))[0];
}

function PRS_GetDetailsURL(P,E,T){
	Args = [];
	if(typeof P != 'undefined' && P != '') Args.push('product='+P);
	if(typeof E != 'undefined' && E != '') Args.push('edition='+E);
	if(typeof T != 'undefined' && T != '') Args.push('type='+T);
	period = $('.panel.product_registration_summary select[name="period"]').val();
	if(period != ''){
		if(period.indexOf("&")>-1) { Args.push('till='+period.split('&')[1]); period = period.split('&')[0]; }
		Args.push('period='+period);
	}
	return _PRS_DetailURL+'?'+Args.join('&');
}

function prs_get_today(){
	CD = new Date(); return new Date(CD.getFullYear(),CD.getMonth(),CD.getDate()).getTime()/1000;
}

function prs_get_current(p){ p = parseInt(p);
	CD = new Date(); return new Date(CD.getFullYear(),parseInt(CD.getMonth()/p)*p,1).getTime()/1000;
}

function prs_get_previous(p){ p = parseInt(p);
	CD = new Date(); return (new Date(CD.getFullYear(),CD.getMonth()-(parseInt(CD.getMonth()%p)+p),1).getTime()/1000)+'&'+prs_get_current(p);
}


