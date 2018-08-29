// JavaScript Document

var _TS_DetailURL = '/mit/list/team/{team}/status/{status}';

function tickets_summary_arrange_data(Data){
	
	if($.isEmptyObject(Data)) return NoPanelRecordExists($('.panel'+_Panels['tickets_summary']),7);
	else return TS_ArrangeData(Data);
	
}

function TS_ArrangeData(Data){
	Data = TS_GetTeamData(Data);
	GridData = TS_GridData(Data);
	LinkData = TS_HrefData(Data,GridData)
	return LinkData;
}

function TS_GetTeamData(Data){
	Status2ColIndex = { 'CLOSED':0,'NEW':1,'OPENED':1,'INPROGRESS':2,'COMPLETED':0,'HOLD':3,'REOPENED':4,'RECREATED':4 };
	Team = {};
	$.each(Data,function(i,Obj){
		if(typeof Team[Obj.team] == 'undefined') Team[Obj.team] = { name:Obj.name,tickets:[0,0,0,0,0] }
		Team[Obj.team].tickets[Status2ColIndex[Obj.status]] += parseInt(Obj.tickets);
	});
	return Team;
}

function TS_GridData(Data){
	return $.map(Data,function(Obj,TST){
		T = Obj.tickets; S = T.reduce((a, b) => a + b, 0);
		return [[Obj.name,S,T[0],T[1],T[2],T[3],T[4]]];
	})
}

function tickets_summary_post_fill(){
	tbody = $('.panel'+_Panels['tickets_summary']+' tbody');
	TS_BoldColumns(tbody);
	TS_AlignColumns(tbody);
}

function TS_BoldColumns(tbody){
	$('[data-col="2"],[data-col="3"]',tbody).css({ fontWeight:'bold' })
}

function TS_AlignColumns(tbody){
	$('[data-col]',tbody).not('[data-col="1"]').css({ textAlign:'center' })
}

function ts_period_options(){
	return TS_GetPeriodOptions()
}

function TS_GetPeriodOptions(){
	options = [{ text:'Today',value:'0 day' }, { text:'From yesterday',value:'1 day' }, { text:'Last 2 days',value:'2 days' }, { text:'Last 3 days',value:'3 days' }, { text:'Last 4 days',value:'4 days' }, { text:'This week',value:TS_get_this_week() }, { text:'This month',value:TS_get_this_month() }, { text:'Last month',value:TS_get_last_month() }, { text:'Last 3 months',value:TS_get_last_months(3) }, { text:'Last 6 months',value:TS_get_last_months(6) }, { text:'This year',value:TS_get_this_year() }]
	return { 'period':CreateNodes(options,'option','text',{value:'value'}) };
}

function TS_HrefData(Data,GridData){
	Keys = Object.keys(Data);
	return $.map(GridData,function(FullArray,key_ind){
		return [$.map(FullArray,function(Col,i){ if(i) return TS_Href(Keys[key_ind],Col,i); else return _PartnerDetailAnchor(Col,'supportteam',Keys[key_ind]); })];
	});
}
function TS_Href(team,count,status_index){
 Status = ['','total','closed','new','inprogress','hold','reopened'];
 href = _TS_DetailURL.replace('{team}',team).replace('{status}',Status[status_index]) + '?period=' + $('.panel.tickets_summary select[name="period"]').val().replace('&','&till=');
 return ($('<a>').attr({target:'_blank',href:href}).css({textDecoration:'none', color:'inherit'}).text(count))[0]
}

function TS_get_this_week(){
	CD = new Date();
	return (new Date(CD.getFullYear(),CD.getMonth(),CD.getDate()-CD.getDay())).getTime()/1000
}
function TS_get_this_month(){
	CD = new Date();
	return (new Date(CD.getFullYear(),CD.getMonth(),1)).getTime()/1000
}
function TS_get_last_month(){
	CD = new Date();
	return [(new Date(CD.getFullYear(),CD.getMonth()-1,1)).getTime()/1000,(new Date(CD.getFullYear(),CD.getMonth(),1)).getTime()/1000].join('&')
}
function TS_get_last_months(d){
	CD = new Date();
	return (new Date(CD.getFullYear(),CD.getMonth()-2,1)).getTime()/1000;
}
function TS_get_this_year(){
	CD = new Date();
	return (new Date(CD.getFullYear(),0,1)).getTime()/1000;
}














