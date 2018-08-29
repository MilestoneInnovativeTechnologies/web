// JavaScript Document

window.onresize = ResizeChatWindow;

$(function(){
	ResizeChatWindow();
	bindMetaClickMonitor();
	AnimateWell('task_well','start'); AnimateWell('ticket_well','start');
	AddClassToAllToggleA();
	setTimeout(FrequentChatCheck,5000);
})

function AnimateWell(item,content){
	contents = {'text':'start','start':'text'};
	div = $('.'+item);
	data = (content == 'start') ? GetTimeAnimateData(div.attr('data-'+content)) : div.attr('data-'+content);
	div.find("."+content).text(data).end().find('div').slideToggle();
	setTimeout(AnimateWell,5000,item,contents[content]);
}

function GetTimeAnimateData(t){
	secs = DateDiff(t,'s'); time = SecondsToTimeArray(secs); TAC = ['Yrs','Months','Days','Hrs','Mins','Secs'];
	time_array = []; time_unit = '';
	for(x in time){
		if(time[x] || time_array.length) { time_array.push(time[x]); time_unit = (time_unit == "")?TAC[x]:time_unit; }
		if(time_array.length >= 2) return time_array.join(":")+" "+time_unit;
	}
	return time_array.join(":")+" "+time_unit;
}

function AddClassToAllToggleA(){
	$('a').filter('[href^="javascript:Toggle"]').addClass('toggle_anchor')
}

function TogglePanelView(panel){
	AR = {'glyphicon-minus':'glyphicon-plus','glyphicon-plus':'glyphicon-minus'}
	$('.panel.'+panel+' .panel-body').slideToggle();
	$.each(AR,function(h,a){ if($('.panel.'+panel+' a.toggle_anchor span').hasClass(h)) { $('.panel.'+panel+' a.toggle_anchor span').removeClass(h).addClass(a); return false; } })
}
	var _modalHeading = {
		'modalNewCookie':"Create New Cookie for Customer",
		'modalNewConnection':"Add New Remote Connection Details",
		'modalViewConnection':"View Remote connection details",
		'modalCustomerPresale':"Change Presale dates of customer",
		'modalNewPO':"Add New Print Object",
		'modalViewPOHistory':"Earlier Versions of Print Object",
		'modalNewUploadForm':"Create New Upload Form",
		'modalDetailsUploadForm':"Upload Form Details",
	}
	var _modalBodyTblClsNRows = {
		'modalNewCookie':['striped new_cookie',['Cookie Name','Cookie Value']],
		'modalNewConnection':['striped new_connection',['App Name','Login','Secret','Remarks']],
		'modalViewConnection':['striped view_connection',['App Name','Login','Secret','Remarks']],
		'modalCustomerPresale':['striped customer_presale',['Name','Product','Presale Start Date','Presale End Date','Presale Extend To']],
		'modalNewPO':['striped new_po',['Function Name','New Function Name','Function Code','Print Name','New Print Name','File','Preview Image']],
		'modalViewPOHistory':['striped po_versions'],
		'modalNewUploadForm':['striped upload_form',['Name','Description','Overwritable']],
		'modalDetailsUploadForm':['striped upload_form_details',['Code','Name','Description','Customer','Ticket','File','Time','Overwritable','Form Link']],
	}
	var _modalFooterButtons = {
		'modalNewCookie':['Close',['CreateCookie','Create Cookie']],
		'modalNewConnection':['Close',['CreateConnection','Add Connection Details']],
		'modalViewConnection':['Close'],
		'modalCustomerPresale':['Close',['ChangeCustomerPresale','Change Presale Dates']],
		'modalNewPO':['Close',['AddPO','Add Print Object']],
		'modalViewPOHistory':['Close'],
		'modalNewUploadForm':['Close',['CreateUploadForm','Create Form']],
		'modalDetailsUploadForm':['Close',['DeleteUploadForm','Delete form'], ['MailFormLink','Mail Form Link'],['ChatFormLink','Share Form link by Chat']],
	}

function AddNewCookie(){
	modal = getModal('modalNewCookie').modal('show');
	ValObj = {
		'Cookie Name':$('<input>').attr({class:'form-control',name:'cookie_name'}),
		'Cookie Value':$('<input>').attr({class:'form-control',name:'cookie_value'})
	}
	FillModalTable(modal,ValObj);
}
 function CreateCookie(){
	 cknm = $('[name="cookie_name"]').val(); ckvl = $('[name="cookie_value"]').val();
	 if($.trim(cknm) == "" || $.trim(ckvl) == "") return alert('Cookie Name and Value should not be empty');
	 user = GetCustomerCode(); FireAPI('api/v1/tkt/action/cck',function(RJ){
		 $('<tr>').attr('id','customer_cookie_'+RJ.id).html([
			 $('<td>').text(RJ.name),
			 $('<td>').text(RJ.value),
			 $('<td>').html(btn('Delete this cookie','javascript:DeleteCookie("'+RJ.id+'")','remove').removeClass('btn')),
		 ]).appendTo('.customer_cookies.panel tbody');
		 getModal('modalNewCookie').modal('hide')
	 },{customer:user,name:cknm,value:ckvl});
 }
function DeleteCookie(id){
	FireAPI('api/v1/tkt/action/rck',function(a){
		tr = $('#customer_cookie_'+a).remove();
	},{customer:GetCustomerCode(),cookie:id})
}

function AddNewConnection(){
	modal = getModal('modalNewConnection').modal('show');
	ValObj = {
		'App Name':$('<select>').attr({class:'form-control',name:'rmt_app_name'}).html(GetSimpleOptions(['Teamviewer','Ammy Admin','Any Desk'])),
		'Login':$('<input>').attr({class:'form-control',name:'rmt_app_login'}),
		'Secret':$('<input>').attr({class:'form-control',name:'rmt_app_secret'}),
		'Remarks':$('<textarea>').attr({class:'form-control',name:'rmt_app_remarks'}),
	}
	FillModalTable(modal,ValObj);
}
 function CreateConnection(){
	 app = GetValueFromName('rmt_app_name'); login = GetValueFromName('rmt_app_login'); secret = GetValueFromName('rmt_app_secret'); remarks = GetValueFromName('rmt_app_remarks'); 
	 if($.trim(app) == "") return alert('App Name not be empty');
	 customer = GetCustomerCode(); FireAPI('api/v1/tkt/action/ccrc',function(RJ){
		 //<tr id="customer_connection_{{ $Cn->id }}"><td>{{ $Cn->appname }}</td><td>{{ $Cn->login }}</td><td>{{ $Cn->secret }}</td></tr>
		 $('<tr>').attr('id','customer_connection_'+RJ.id).html([
			 $('<td>').text(RJ.appname),
			 $('<td>').text(RJ.login),
			 $('<td>').text(RJ.secret),
			 $('<td>').html([btn('View in Detail','javascript:ViewConnection(\''+RJ.id+'\',\''+RJ.appname+'\',\''+RJ.login+'\',\''+RJ.secret+'\',\''+RJ.remarks+'\')','list-alt').removeClass('btn'),' &nbsp; ',btn('Delete this connection','javascript:DeleteConnection(\''+RJ.id+'\')','remove').removeClass('btn')]),
		 ]).appendTo('.customer_connections.panel tbody');
		 getModal('modalNewConnection').modal('hide')
	 },{customer:customer,appname:app,login:login,secret:secret,remarks:remarks});
 }
function DeleteConnection(id){
	FireAPI('api/v1/tkt/action/rcrc',function(a){
		tr = $('#customer_connection_'+a).remove();
	},{customer:GetCustomerCode(),connection:id})
}
function ViewConnection(id,app,login,secret,remarks){
	modal = getModal('modalViewConnection').modal('show');
	FillModalTable(modal,{
		'App Name':$('<input>').attr({class:'form-control',value:app}),
		'Login':$('<input>').attr({class:'form-control',value:login}),
		'Secret':$('<input>').attr({class:'form-control',value:secret}),
		'Remarks':$('<textarea>').attr({class:'form-control'}).val(remarks),
	});
}



function SendDownloadLinkByChat(P,E,K,T){
	FireAPI(GetSDLCUrl(),function(RJ){
		PopulateAllChats(RJ);
	},{PRD:P,EDN:E,PKG:K,TYP:T})
}
function GetSDLCUrl(){
	return ['api/v1/tkt/action',GetUserCode(),GetTicketCode(),'sdlc'].join('/')
}

function SendDownloadLinkByMail(P,E,K,C){
	FireAPI(GetSDLMUrl(),function(RJ){
		PopulateAllChats(RJ);
	},{PRD:P,EDN:E,PKG:K,CUS:C})
	
}
function GetSDLMUrl(){
	return ['api/v1/tkt/action',GetUserCode(),GetTicketCode(),'sdlm'].join('/')
}

function GetSimpleOptions(Ary){
	return $.map(Ary,function(a){
		return OptionTag(a,a);
	})
}

function OptionTag(v,t,attr){
	attr = $.extend({'value':v},(attr || {}));
	return $('<option>').text(t).attr(attr);
}

function GetObjOptions(Obj){
	if(!$.isArray(Obj)) return OptionTag(Obj.value, Obj.text, ((Obj.attr)?(Obj.attr):({})));
	return $.map(Obj,function(AObj){
		return GetObjOptions(AObj);
	})
}










