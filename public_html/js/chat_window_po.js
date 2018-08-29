// JavaScript Document

function AddNewPrintObject(){
	modal = getModal('modalNewPO').modal('show');
	ValObj = {
		'Function Name':$('<select>').attr({class:'form-control',name:'function_name'}),
		'New Function Name':$('<input>').attr({class:'form-control',name:'new_function_name'}),
		'Function Code':$('<input>').attr({class:'form-control',name:'function_code',readonly:true}),
		'Print Name':$('<select>').attr({class:'form-control',name:'print_name'}),
		'New Print Name':$('<input>').attr({class:'form-control',name:'new_print_name'}),
		'File':$('<input>').attr({class:'form-control',name:'po_file',type:'file'}),
		'Preview Image':$('<input>').attr({class:'form-control',name:'po_preview_image', type:'file'}),
	}
	FillModalTable(modal,ValObj);
	ApplyFunctionNameOptions();
	ApplyPrintNameOptions();
	$('tr.new_function_name').css({display:'none'});
	$('tr.new_print_name').css({display:'none'});
}

function AddPO(){
	ValidatePOForm()
}

function ApplyFunctionNameOptions(){
	$('[name="function_name"]').html(GetObjOptions({value:'0', text:'Select Function Name'})).attr('onChange','FunctionNameChanged(this.value)');
	FireAPI(GetGFNUrl(),function(POJ){
		$('[name="function_name"]').append(JsonToFnOptions(POJ));
		$('[name="function_name"]').append(GetObjOptions({value:'-1', text:'Create New'}));
	});
}

function ApplyPrintNameOptions(){
	$('[name="print_name"]').html(GetObjOptions([{value:'', text:'Select Print Name'},{value:'-1', text:'New Print Name'}])).attr('onChange','PrintNameChanged(this.value)');
}

function GetGFNUrl(){
	return ['api/v1/tkt/get',GetCustomerCode(),GetSeqNo(),'gfn'].join('/')
}

function GetGPNUrl(){
	return ['api/v1/tkt/get',GetCustomerCode(),GetSeqNo(),'gpn'].join('/')
}

function JsonToFnOptions(J){
	if($.isEmptyObject(J)) return [];
	return GetObjOptions($.map(J,function(Name, Code){ return new Object({value:Name, text:Name, attr:{'data-code':Code}}); }))
}

function FunctionNameChanged(v){
	$('tr.new_function_name').css({display:'none'});
	if(v == "0") { $('[name="function_code"]').val('').attr({readonly:true}); return; }
	if(v == "-1") { $('[name="function_code"]').val('').removeAttr('readonly'); $('tr.new_function_name').css({display:'table-row'}); return; }
	fc = $('[name="function_name"] option[value="'+v+'"]').attr('data-code');
	$('[name="function_code"]').val(fc).attr('readonly',true)
	LoadPrintNames(fc)
}

function PrintNameChanged(v){
	if(v == "-1") $('tr.new_print_name').css({display:'table-row'});
	else $('tr.new_print_name').css({display:'none'});
}

function LoadPrintNames(fc){
	FireAPI(GetGPNUrl(),function(D){
		fc = Object.keys(D)[0];
		PNS = $('[name="print_name"]');
		$('option:gt(1)',PNS).remove();
		if(D[fc]) $.each(D[fc],function(i,opt){ if(opt) $('<option>').attr('value',opt).text(opt).appendTo(PNS); });
	},{c:GetCustomerCode(),s:GetSeqNo(),f:fc});
}

function ValidatePOForm(){
	if($.trim($('[name="function_code"]').val()) == "") { alert('Function code cannot be empty'); return false; }
	if($.trim($('[name="function_name"]').val()) == "0") { alert('Select proper function name'); return false; }
	if($.trim($('[name="function_name"]').val()) == "-1" && $.trim($('[name="new_function_name"]').val()) == "") { alert('Enter new function name'); return false; }
	CreateAndSubmitPOForm()
}

function PostForm(url, formdata, success){
	$.ajax({
		url: url,
		type: 'POST',
		data: formdata,
		cache: false,
		contentType: false,
		processData: false,
		dataType:'json',
		success:success,
	})
}

function CreateAndSubmitPOForm(){
	P = GetCustomerCode(); S = GetSeqNo(); F = GetValueFromName('function_name'); C = GetValueFromName('function_code'); if($.inArray($.trim(F),['0','-1'])>-1) F = GetValueFromName('new_function_name'); N = GetValueFromName('print_name'); if(N == "-1") N = GetValueFromName('new_print_name');
	formData = new FormData($('<form>').attr({"enctype":"multipart/form-data"}).html([$('[name="po_preview_image"]').clone(),$('[name="po_file"]').clone(),HiddenInput('customer',P),HiddenInput('reg_seq',S),HiddenInput('function_name',F),HiddenInput('function_code',C),HiddenInput('print_name',N)])[0])
	getModal('modalNewPO').modal('hide');
	PostForm(GetAPOUrl(P,S),formData,AddedPrintObject)
}

function AddedPrintObject(d){
	if(typeof(d) == "string") return alert('Error in Adding new print object. Error text: '+d);
	fncode = d.function_code;
	TR = $('tr[data-fncode="'+fncode+'"]'); if(TR.length) return UpdatePOTR(TR,d);
	return AddPOTR(d).appendTo($('.customer_print_objects.panel tbody'));
}

function UpdatePOTR(tr,d){
	tr.find('td:eq(1)').find('small:first').text(d.user.name)
		.next().next().text(GetTime2Date(d.time))
		.parent().next().find('a:first').attr({href:'javascript:MailPrintObject("'+d.code+'")'})
		.next().attr({href:'javascript:ChatLinkPrintObject("'+d.code+'")'})
		.next().attr({href:'javascript:HistoryPrintObject("'+d.code+'","'+d.function_name+'","'+d.function_code+'")'})
}

function AddPOTR(d){
	//<tr id="customer_printobject_{{ $Pb->code }}" data-fncode="{{ $Pb->function_code }}"><td><small>{{ $Pb->function_code }}</small><br><small>{{ $Pb->function_name }}</small></td>
	//<td><small>{{ $Pb->User->name }}</small><br><small>{{ date('D d/m, h:i A',$Pb->time) }}</small></td><td>
	//<a href="javascript:MailPrintObject('{{ $Pb->code }}')" class="btn" style="padding: 5px" title="Send Print Object download link by Mail"><span class="glyphicon glyphicon-envelope"></span></a>
	//<a href="javascript:ChatLinkPrintObject('{{ $Pb->code }}')" class="btn" style="padding: 5px" title="Send Print Object download link by Chat"><span class="glyphicon glyphicon-transfer"></span></a>
	//<a href="javascript:HistoryPrintObject('{{ $Pb->code }}','{{ $Pb->function_name }}','{{ $Pb->function_code }}')" class="btn" style="padding: 5px" title="View earlier versions"><span class="glyphicon glyphicon-list-alt"></span></a></td></tr>
	TR = $('<tr>').attr({'id':'customer_printobject_'+d.code,'data-fncode':d.function_code});
	TD1 = $('<td>').html([$('<small>').text(d.function_code),$('<br>'),$('<small>').text(d.function_name)]);
	TD2 = $('<td>').html([$('<small>').text(d.user.name),$('<br>'),$('<small>').text(GetTime2Date(d.time))]);
	A1 = $('<a>').attr({href:'javascript:MailPrintObject("'+d.code+'")',class:'btn',style:'padding: 5px',title:'Send Print Object download link by Mail'}).html(gly_icon('envelope'))
	A2 = $('<a>').attr({href:'javascript:ChatLinkPrintObject("'+d.code+'")',class:'btn',style:'padding: 5px',title:'Send Print Object download link by Chat'}).html(gly_icon('transfer'))
	A3 = $('<a>').attr({href:'javascript:HistoryPrintObject("'+d.code+'","'+d.function_name+'","'+d.function_code+'")',class:'btn',style:'padding: 5px',title:'View earlier versions'}).html(gly_icon('list-alt'))
	TD3 = $('<td>').html([A1,A2,A3]); return TR.html([TD1,TD2,TD3]);
}

function GetTime2Date(t){
	d = new Date(parseInt(t)*1000); Days = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat']; Months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
	A1 = Days[d.getDay()];
	B1 = [d.getDate(),Months[d.getMonth()]].join('/');
	A = 'AM';
	h = d.getHours(); if(h > 12){ A = 'PM'; h = h-12; } else if(h = 0) { h = 12; } else if(h < 10) h = "0"+h;
	m = d.getMinutes(); if(m < 10) m = "0"+m;
	C1 = [[h,m].join(':'),A].join(' ');
	return [A1,B1,C1].join(' ');
}

function HiddenInput(n,v){
	return $('<input>').attr({type:'hidden',name:n,value:v});
}

function GetAPOUrl(P,S){
	return ['api/v1/tkt/action',P,S,'apo'].join("/");
}

function MailPrintObject(code){
	FireAPI(GetSPOMUrl(),function(D){
		PopulateAllChats(D)
	},{code:code})
}

function ChatLinkPrintObject(code){
	FireAPI(GetSPOCUrl(),function(D){
		PopulateAllChats(D)
	},{code:code})
}

function GetSPOMUrl(){
	return ['api/v1/tkt/action',GetUserCode(),GetTicketCode(),'spom'].join("/");
}

function GetSPOCUrl(){
	return ['api/v1/tkt/action',GetUserCode(),GetTicketCode(),'spoc'].join("/");
}

function HistoryPrintObject(code,f_name,f_code){
	modal = getModal('modalViewPOHistory').modal('show');
	DeployPOHTableHead(modal, f_name);
	GetPOHistory(code,f_name,f_code)
}

function DeployPOHTableHead(modal, f_name){
	$('thead',modal).html($('<tr>').html([$('<th>').text('No'),$('<th>').text('Approved User'),$('<th>').text('Approved Time'),$('<th>').text('Status'),$('<th>').text('Actions')]));
	$('.modal-title',modal).find('span').remove().end().append($('<span>').text(', '+f_name));
}

function GetPOHistory(code, f_name, f_code){
	FireAPI(GetPOHUrl(),function(Data){
		tbd = $('tbody',getModal('modalViewPOHistory')).empty();
		if($.isEmptyObject(Data)) return;
		$.each(Data,function(i,po){
			$('<tr>').html(POH_Columns(po,i+1)).append($('<td>').html(POHColumnActions(po.code))).appendTo(tbd);
		})
	},{code:code,fname:f_name,fcode:f_code})
}

function GetPOHUrl(){
	return ['api/v1/tkt/get',GetCustomerCode(),GetSeqNo(),'poh'].join('/')
}

function POH_Columns(po,i){
	TD1 = $('<td>').text(i); TD2 = $('<td>').text(po.user.name); d = new Date(parseInt(po.time) * 1000); date = [d.getDate(),d.getMonth()+1,d.getFullYear()].join('/') + " " + d.getHours() + ":" + d.getMinutes();
	TD3 = $('<td>').text(date); TD4 = $('<td>').text(po.status); TD5 = $('<td>').text('-');
	return [TD1, TD2, TD3, TD4];
}

function POHColumnActions(code){
	return [$('<a>').attr({href:'javascript:MailPrintObject("'+code+'")',class:'btn',style:'padding: 5px',title:'Send Print Object download link by Mail'}).html(gly_icon('envelope')),
	$('<a>').attr({href:'javascript:ChatLinkPrintObject("'+code+'")',class:'btn',style:'padding: 5px',title:'Send Print Object download link by Chat'}).html(gly_icon('transfer'))];
}
