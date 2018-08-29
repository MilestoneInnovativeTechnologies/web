// JavaScript Document

var _CAT_DetailURL = '/mit/list/team/{team}/status/{status}';

function current_active_tickets_arrange_data(Data){
	
	if($.isEmptyObject(Data)) return NoPanelRecordExists($('.panel'+_Panels['current_active_tickets']),4);
	else return CAT_ArrangeData(Data);
	
}

function CAT_ArrangeData(Data){
	Data = CAT_GetTeamData(Data);
	GridData = CAT_GridData(Data);
	LinkData = CAT_LinkData(Data,GridData);
	return LinkData
}

function CAT_GetTeamData(Data){
	Status2ColIndex = { 'NEW':0,'OPENED':1,'INPROGRESS':2,'HOLD':3 };
	Team = {};
	$.each(Data,function(i,Obj){
		if(typeof Team[Obj.team] == 'undefined') Team[Obj.team] = { name:Obj.name,tickets:[0,0,0,0,0] }
		Team[Obj.team].tickets[Status2ColIndex[Obj.status]] =+ parseInt(Obj.tickets);
	});
	return Team;
}

function CAT_GridData(Data){
	return $.map(Data,function(Obj,TST){
		T = Obj.tickets;
		return [[Obj.name,T[0],T[1],T[2],T[3]]];
	})
}

function current_active_tickets_post_fill(){
	tbody = $('.panel'+_Panels['current_active_tickets']+' tbody');
	CAT_Alignment(tbody)
}

function CAT_Alignment(tbody){
	$('td',tbody).not('[data-col="1"]').css('text-align','center')
}

function CAT_LinkData(Data,Grid){
	Keys = Object.keys(Data);
	return $.map(Grid,function(FullArray,key_ind){
		return [$.map(FullArray,function(Col,i){ if(i) return CAT_Href(Keys[key_ind],Col,i); else return _PartnerDetailAnchor(Col,'supportteam',Keys[key_ind]); })];
	});
}

 function CAT_Href(team,count,status_index){
	 Status = ['','new','opened','inprogress','hold'];
	 href = _CAT_DetailURL.replace('{team}',team).replace('{status}',Status[status_index]);
	 return ($('<a>').attr({target:'_blank',href:href}).css({textDecoration:'none', color:'inherit'}).text(count))[0]
 }
