// JavaScript Document

$(function(){
	_modalHeading['modalSendChatScript'] = 'Send Chat Trascript';
	_modalBodyTblClsNRows['modalSendChatScript'] = ['striped send_chat_transcript',['Customer','Distributor','Support Team','Others']];
	_modalFooterButtons['modalSendChatScript'] = ['Close',['SendTranscript','Send Chat Transcript']];
})

function SendChatTrasnscript(tkt){
	modal = getModal('modalSendChatScript').modal('show');
	FireAPI(GetTUrl(),function(Ary){
		if($.isEmptyObject(Ary)) return;
		ShowSendChatDetails(Ary);
	});
}

function StackedCheckbox(name, value, text){
	return $('<div>').addClass('checkbox').html($('<label>').html([$('<input>').attr({type:'checkbox',name:name,value:value}),' ',text]));
}

function GetTUrl(){
	return ['api/v1/tkt/get',GetUserCode(),GetTicketCode(),'tu'].join('/')
}

function ShowSendChatDetails(Ary){
	ValObj = GetValObj(Ary);
	modal = getModal('modalSendChatScript');
	FillModalTable(modal,ValObj);
}

function GetValObj(Ary){
	data = GetArrangedData(Ary);
	key2name = {'customer':'Customer','distributor':'Distributor','createdby':'Others','supportteam':'Support Team','others':'Others'};
	ValObj = {};
	$.each(data,function(n,Html){
		key = key2name[n]; Obj = $(Html);
		if(ValObj.hasOwnProperty(key)) ValObj[key].push(Obj)
		else ValObj[key] = [Obj];
	});
	return ValObj;
}

function GetArrangedData(Ary){
	roles = ['customer','distributor','createdby','supportteam','others'];
	ValObj = {};
	$.each(roles,function(i,role){
		users = Ary[1][role];
		$.each(users,function(i,user){
			dets = Ary[0][user];
			if(ValObj.hasOwnProperty(role)) ValObj[role].push(StackedCheckbox('user[]',dets[1],dets[0])[0])
			else ValObj[role] = [StackedCheckbox('user[]',dets[1],dets[0])[0]];
		})
	});
	return ValObj;
}

function SendTranscript(){
	Emails = GetSendTransCheckedMails();
	if(Emails !== false){
		FireAPI(GetSTUUrl(),function(RR){
			console.log(RR);
		},{emails:Emails.toArray()})
		getModal('modalSendChatScript').modal('hide');
	} else {
		alert('You haven\'t selected any user to send email')
	}
}

function GetSendTransCheckedMails(){
	if(!$('[name="user[]"]').filter(':checked').length) return false;
	return $('[name="user[]"]').filter(':checked').map(function(i,chkbx){ return(chkbx.value) })
}

function GetSTUUrl(){
	return ['api/v1/tkt/action',GetUserCode(),GetTicketCode(),'sct'].join('/')
}











