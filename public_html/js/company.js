$(function(){
	CreatePanelTables();
	InitPanels();
	SetDataFilters();
	LoadPanelsData()
});

var _DetailsURL = { customer:'/mit/customer/{code}', distributor:'/mit/distributor/{code}', dealer:'/mit/dealer/{code}', supportteam:'/mit/supportteam/{code}', supportagent:'/mit/supportagent/{code}' };

function CreatePanelTables(){
	$.each(_PanelTables, function(panel, headAry){
		TR = $('<tr>').html(WrapElementWithTag('th',headAry));
		$('.'+panel+' thead').html(TR);
	})
}

function GetDecentClassName(A){
	if(A) return A.replace(/[^A-Za-z0-9]/g, '_').toLowerCase();
	return '_';
}

function WrapElementWithTag(Tag,Ary){
	if(!$.isArray(Ary)) return $('<'+Tag+'>').html(Ary);
	else if(isAnyElementJQObject(Ary)) return $('<'+Tag+'>').html(Ary);
	return $.map(Ary,function(Ele){
		return WrapElementWithTag(Tag,Ele);
	})
}

function FillDashboardTable(name,Ary){
	Ary = Ary || GetDashboardContent(name); if($.isEmptyObject(Ary)) return;
	TBODY = $('.panel'+_Panels[name]+' tbody').empty();
	LIMIT = $('.panel'+_Panels[name]+' .limit_control').val() || 9999;
	$.each(Ary,function(i,Cols){
		if(LIMIT <= i) return false;
		$('<tr>').html(AddColNoAttr(WrapElementWithTag('td',Cols))).appendTo(TBODY).attr({'data-record':(i+1)});
	})
}

function StoreDashboardContent(name,content){
	$('.panel'+_Panels[name]).data('content',content);
}
function GetDashboardContent(name){
	return $('.panel'+_Panels[name]).data('content');
}


function ArrayHasKeyValue(Ary,Key,Value){
	Has = false;
	$.each(Ary,function(i,Obj){
		if(Has === false && Obj[Key] == Value) Has = true;
	});
	return Has;
}

function isAnyElementJQObject(Ary){
	JQ = false;
	$.each(Ary,function(i,Ar){
		if(JQ === false && Ar instanceof jQuery) JQ = true;
	})
	return JQ;
}

_URLPanelMap = {};
function LoadPanelsData(){
	$.each(_DataURI, function(panel, url){
		LoadPanelData(panel,url)
	})
}

_LoadedData = {};
function LoadPanelData(panel, url){
	Params = GetLoadDataParameters(panel,url) || {};
	_URLPanelMap[url] = panel;
	FireAPI(url,function(d,s,x){
		url = GetUrlWithoutParams(this.url); item = _URLPanelMap[url]; _LoadedData[item] = d;
		fun_name = [item,'arrange_data'].join('_');
		if(typeof window[fun_name] == 'function') Data = window[fun_name].call(window[fun_name], d,s,x); else { Data = GetDefaultDashboardData(d); console.log('Missing function -'+fun_name+'()-, hence executing default data fetching function for data'+"\n",d); }
		StoreDashboardContent(item,Data); FillDashboardTable(item,Data);
		fun_name = [item,'post_fill'].join('_'); if(typeof window[fun_name] == 'function') window[fun_name].call(window[fun_name], Data, d, s, x);
	},Params)
}

function GetDefaultDashboardData(d){
	return $.map(d,function(a){
		RD = [];
		$.each(a,function(i,Data){ if(typeof Data == 'string' || typeof Data == 'number') RD.push(Data); })
		return [RD];
	})
}

function ObjectSortByKeyValue(Obj, Key, Number){
	return $.map(Object.keys(Obj).sort(function(a,b){ return( (parseInt(Obj[a][Key])-parseInt(Obj[b][Key])) ); }),function(k){ return Obj[k]; });
}

function AddColNoAttr(Ary){
	if($.isArray(Ary)) return $.map(Ary,function(Ele, index){
		if(Ele instanceof jQuery) return Ele.attr('data-col',(index+1));
	})
}

function InitPanels(){
	$.each(_Panels,function(n,Class){
		fun_name = [n,'init'].join('_');
		if(typeof window[fun_name] == 'function') Data = window[fun_name].call(window[fun_name], Class);
	})
}

function SetDataFilters(){
	$.each(_DataFilter,function(panel,funname){
		if($.isArray(funname)) $.each(funname,function(j,fun){ SetDataFilter(panel,fun) })
		else SetDataFilter(panel,funname)
	})
}

function SetDataFilter(panel,funname){
	panel_head = $(".panel"+_Panels[panel]+' .panel-heading');
	if(typeof window[funname] != 'function') return console.log('Data filter, options returning function for, '+panel+', is not found, -'+funname+'-');
	OptObj = window[funname].call(window[funname],null);
	$.each(OptObj,function(SelName,OptsArray){
		SelectTag = $('<select>').attr({ name:SelName,class:'form-control pull-right data_filter',onchange:'LoadPanelData("'+panel+'","'+_DataURI[panel]+'")' }).css({ width: "80px", marginTop:"-28px", padding:"0px" }).html(OptsArray);
		panel_head.append(SelectTag);
	})
}

function GetLoadDataParameters(panel, url){
	return GetDataFilterParameters(panel, url);
}

function GetDataFilterParameters(panel, url){
	panel_heading = $($(_Panels[panel]+' .panel-heading'))
	if(!$('.data_filter',panel_heading).length) return {};
	Params = {};
	$('.data_filter',panel_heading).each(function(i,T){ Params[$(T).attr('name')] = $(T).val(); });
	return Params;
}

function GetUrlWithoutParams(url){
	return url.split("?")[0]
}

function NoPanelRecordExists(Panel,Cols,Text){
	Text = Text || "No records exists.";
	$('tbody',Panel).html($('<tr>').html($('<th>').attr({colspan:Cols}).css('text-align','center').html(Text)));
	return [];
}

function _PartnerDetailURL(Partner,Code){
	return _DetailsURL[Partner].replace('{code}',Code);
}

function _PartnerDetailAnchor(Content,Partner,Code){
	return $('<a>').attr({ target:'_blank', href:_PartnerDetailURL(Partner,Code) }).css({textDecoration:'none',color:'inherit'}).text(Content)[0];
}




