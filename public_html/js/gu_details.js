// JavaScript Document
var _modalHeading = {
	'pubSendForm' : 'Send Form link',
	'pubSendFile' : 'Send Form file',
	'cusSendForm' : 'Send Customer\'s Form link',
	'cusSendFile' : 'Send Customer\'s Form file',
};
var _modalBodyTblClsNRows = {
	'pubSendForm' : ['striped pubSendForm',['Enter email']],
	'pubSendFile' : ['striped pubSendFile',['Enter email']],
	'cusSendForm' : ['striped cusSendForm',['To','Enter Email']],
	'cusSendFile' : ['striped cusSendFile',['To','Enter Email']],
};
var _modalFooterButtons = {
	'pubSendForm' : ['Close',['SendFormLink','Send Form Link']],
	'pubSendFile' : ['Close',['SendFormFile','Send Form File']],
	'cusSendForm' : ['Close',['SendCustForm','Send Customer\'s Form']],
	'cusSendFile' : ['Close',['SendCustFile','Send Customer\'s Form file']],
};

function ConfirmDelete(url){ if(confirm('Are you sure, you want to delete this form. This action is not reversible.')) location.href = url; }
function ConfirmDropFile(url){ if(confirm('Are you sure, you want to delete the file in this form. This action is not reversible.')) location.href = url; }




function SendForm(form,customer){
	if($.trim(customer) == "") return PublicSendForm(form);
	CustomerSendForm(form,customer)
}
function PublicSendForm(form){
	modal = getModal('pubSendForm').modal('show').attr('data-code',form);
	ValObj = {
		'Enter email':$('<input name="pubSendForm" class="form-control">')
	}
	FillModalTable(modal,ValObj);
}
function SendFormLink(){
	form = getModal('pubSendForm').attr('data-code');
	email = $('[name="pubSendForm"]').val();
	if(!validateEmail(email)) return alert('Enter valid email!');
	SendFormLinkToMail(form, email);
}
function CustomerSendForm(form,customer){
	modal = getModal('cusSendForm').modal('show').attr({'data-form':form,'data-customer':customer});
	ValObj = {
		'To':$('<select name="cusSendForm" class="form-control" onChange="CSFChanged(this.value)">').html(GetParOpts()),
		'Enter email':$('<input name="cusSendFormEmail" class="form-control">')
	}
	FillModalTable(modal,ValObj);
}
function SendCustForm(){
	form = getModal('cusSendForm').attr('data-form');
	email = $('[name="cusSendForm"]').val(); if($.trim(email) == "") email = $('[name="cusSendFormEmail"]').val();
	if(!validateEmail(email)) return alert('Enter valid email!');
	SendFormLinkToMail(form, email);
	getModal('cusSendForm').modal('hide')
}
function SendFormLinkToMail(form,email){
	FireAPI('api/v1/gu/action/sfl',function(){},{form:form,email:email})
}







function SendFile(form,customer){
	if($.trim(customer) == "") return PublicSendFile(form);
	return CustomerSendFile(form,customer)
}
function PublicSendFile(form){
	modal = getModal('pubSendFile').modal('show').attr('data-code',form);
	ValObj = {
		'Enter email':$('<input name="pubSendFile" class="form-control">')
	}
	FillModalTable(modal,ValObj);
}
function SendFormFile(){
	form = getModal('pubSendFile').attr('data-code');
	email = $('[name="pubSendFile"]').val();
	if(!validateEmail(email)) return alert('Enter valid email!');
	SendFormFileToMail(form,email);
}
function CustomerSendFile(form,customer){
	modal = getModal('cusSendFile').modal('show').attr({'data-form':form,'data-customer':customer});
	ValObj = {
		'To':$('<select name="cusSendFile" class="form-control" onChange="CSFChanged(this.value)">').html(GetParOpts()),
		'Enter email':$('<input name="cusSendFileEmail" class="form-control">')
	}
	FillModalTable(modal,ValObj);
}
function SendCustFile(){
	form = getModal('cusSendFile').attr('data-form');
	email = $('[name="cusSendFile"]').val(); if($.trim(email) == "") email = $('[name="cusSendFileEmail"]').val();
	if(!validateEmail(email)) return alert('Enter valid email!');
	SendFormFileToMail(form, email);
	getModal('cusSendFile').modal('hide');
}
function SendFormFileToMail(form,email){
	FireAPI('api/v1/gu/action/sff',function(){},{form:form,email:email})
}





function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}
function GetParOpts(){
	OptObj = {'':'Enter email manually'};
	OptObj[_Customer[2]] = 'Customer - ' + _Customer[1];
	OptObj[_Distributor[2]] = 'Distributor - ' + _Distributor[1];
	if(!$.isEmptyObject(_Dealer)) OptObj[_Dealer[2]] = 'Dealer - ' + _Dealer[1];
	return GetSimpleOptions(OptObj)
}
function GetSimpleOptions(Obj){
	return $.map(Obj,function(text,value){
		return SOption(text, value)
	})
}
function SOption(text, value, attr){
	attr = attr || {}; attr = $.extend(null,attr,{value:value})
	return $('<option>').attr(attr).text(text);
}
function CSFChanged(v){
	CSFShowEmail($.trim(v) == "")
}
function CSFShowEmail(s){
	display = (s)?'table-row':'none';
	$('table.cusSendForm tr.enter_email,table.cusSendFile tr.enter_email').css('display',display);
}
















