// JavaScript Document


function AddNewUploadForm(){
	modal = getModal('modalNewUploadForm').modal('show');
	ValObj = {
		'Name':$('<input>').attr({class:'form-control',name:'uploadform_name'}),
		'Description':$('<textarea>').attr({class:'form-control',name:'uploadform_desc'}),
		'Overwritable':$('<select>').attr({class:'form-control',name:'uploadform_overwrite'}).html(GetObjOptions([{text:'NO',value:'N'},{text:'YES',value:'Y'}])),
	}
	FillModalTable(modal,ValObj);
}

function CreateUploadForm(){
	N = GetValueFromName('uploadform_name'); D = GetValueFromName('uploadform_desc'); O = GetValueFromName('uploadform_overwrite');
	FireAPI(GetGUFUrl(),function(CR){
		$('<tr>').attr({id:"customer_uploads_"+CR.code}).html([
			$('<td>').text(CR.name),
			$('<td>').text((CR.file)?'Yes':'No'),
			$('<td>').html(CustomerUploadActions(CR)),
		]).appendTo('.customer_uploads tbody');
		setTimeout(CheckForFileUpload,10000,CR.code);
	},{name:N,description:D,overwrite:O});
	getModal('modalNewUploadForm').modal('hide');
}

function GetGUFUrl(){
	return ['api/v1/tkt/action',GetCustomerCode(),GetTicketCode(),'cguf'].join('/')
}
function GetCGUFUrl(){
	return ['api/v1/tkt/action',GetCustomerCode(),GetTicketCode(),'cgfu'].join('/')
}

function CheckForFileUpload(code){
	FireAPI(GetCGUFUrl(),function(CRsp){
		if(typeof(CRsp) == 'string') return setTimeout(CheckForFileUpload,10000,CRsp);
		$('tr#customer_uploads_'+CRsp.code).find('td:eq(1)').text('Yes').next().html(CustomerUploadActions(CRsp))
	},{code:code})
}

var _GUForms = [];
function CustomerUploadActions(Obj){
	BTNS = []; _GUForms[Obj.code] = Obj;
	if(Obj.file) BTNS.push(btn('Download',Obj.download,'download').attr({target:'_blank','data-name':'download'}).css({padding:'5px'}).removeClass('btn'));
	if(Obj.file) BTNS.push(btn('Mail file download link','javascript:MailGUFFileDownloadLink("'+Obj.code+'")','share-alt').css({padding:'5px'}).removeClass('btn').attr({'data-name':'details'}));
	BTNS.push(btn('View more details','javascript:GeneralUploadDetails("'+Obj.code+'")','list-alt').css({padding:'5px'}).removeClass('btn').attr({'data-name':'details'}));
	BTNS.push(btn('Form Link by chat','javascript:ChatFormLink("'+Obj.code+'")','transfer').css({padding:'5px'}).removeClass('btn').attr({'data-name':'linkchat'}));
	BTNS.push(btn('Form Link by mail','javascript:MailFormLink("'+Obj.code+'")','envelope').css({padding:'5px'}).removeClass('btn').attr({'data-name':'linkmail'}));
	return BTNS;
}
function PostUploadActions(Ary){
	$.each(Ary,function(i,Obj){
		$('tr#customer_uploads_'+Obj.code).find('td:last').html(CustomerUploadActions(Obj));
	})
}
function GeneralUploadDetails(code){
	modal = getModal('modalDetailsUploadForm').modal('show').attr({'data-code':code}); D = _GUForms[code];
	ValObj = {
		'Code':D.code,
		'Name':D.name,
		'Description':D.description,
		'Customer':(D.customer)?D.customer.name:'',
		'Ticket':D.ticket,
		'File':(D.file)?('<span>Yes</span> <a href="javascript:DropUploadedFile(\''+D.code+'\')" class="btn btn-warning btn-sm">Drop file</a> <a href="javascript:DownloadUploadedFile(\''+D.code+'\')" class="btn btn-info btn-sm">Download</a> <a href="javascript:MailGUFFileDownloadLink(\''+D.code+'\')" class="btn btn-info btn-sm">Mail Download Link</a>'):'No',
		'Overwritable':({Y:'<span>Yes</span>',N:'<span>No</span>'})[D.overwrite] + ' <a href="javascript:ChangeOverwriteOfUploadedFile(\''+D.code+'\')" class="btn btn-warning btn-sm">Change</a>',
		'Time':new Date(D.time*1000),
		'Form Link':[$('<textarea class="form-control">').val(D.form),$('<a href="'+D.form+'" target="_blank" class="pull-right">').text('Browse Link')],
	}
	FillModalTable(modal,ValObj);
}
function DropUploadedFile(code){
	FireAPI(GetDGUFUrl(),function(R){
		spn = getModal('modalDetailsUploadForm').find('tr.file .tbd').text('No');
		$('tr#customer_uploads_'+R.code).find('td:eq(1)').text('No');
		PostUploadActions([R])
	},{code:code})
	
}
function ChangeOverwriteOfUploadedFile(code){
	FireAPI(GetCUFOUrl(),function(R){
		spn = getModal('modalDetailsUploadForm').find('tr.overwritable .tbd span');
		spn.text(({'Y':'Yes','N':'No'})[R.overwrite]);
	},{code:code})
}
function DeleteUploadForm(code){
	FireAPI(GetDGUUrl(),function(){
		code = getModal('modalDetailsUploadForm').modal('hide').attr('data-code');
		$('tr#customer_uploads_'+code).remove();
	},{code:getModal('modalDetailsUploadForm').attr('data-code')})
}
function DownloadUploadedFile(code){
	window.open(_GUForms[code].download,'_blank','height=100;menubar=0;resizable=0;scrollbars=0;status=0;titlebar=0;toolbar=0;width=100');
}
function MailFormLink(code){
	code = code || getModal('modalDetailsUploadForm').attr('data-code');
	FireAPI(GetMFLUrl(),function(){
		code = getModal('modalDetailsUploadForm').modal('hide');
	},{code:code})
}
function ChatFormLink(code){
	code = code || getModal('modalDetailsUploadForm').attr('data-code');
	FireAPI(GetGUFCUrl(),function(){
		code = getModal('modalDetailsUploadForm').modal('hide');
	},{code:code})
}
function MailGUFFileDownloadLink(code){
	code = code || getModal('modalDetailsUploadForm').attr('data-code');
	FireAPI(GetDGUFFrl(),function(){
		code = getModal('modalDetailsUploadForm').modal('hide');
	},{code:code})
}

function GetDGUFUrl(){
	return ['api/v1/tkt/action',GetCustomerCode(),GetTicketCode(),'dguf'].join('/')
}

function GetCUFOUrl(){
	return ['api/v1/tkt/action',GetCustomerCode(),GetTicketCode(),'cufo'].join('/')
}

function GetDGUUrl(){
	return ['api/v1/tkt/action',GetCustomerCode(),GetTicketCode(),'dgf'].join('/')
}

function GetGUFCUrl(){
	return ['api/v1/tkt/action',GetUserCode(),GetTicketCode(),'gufc'].join('/')
}

function GetMFLUrl(){
	return ['api/v1/tkt/action',GetUserCode(),GetTicketCode(),'mfl'].join('/')
}

function GetDGUFFrl(){
	return ['api/v1/tkt/action',GetUserCode(),GetTicketCode(),'dguff'].join('/')
}
