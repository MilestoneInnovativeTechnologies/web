// JavaScript Document

var _SupportTypes = null;
var _AssignUsers = null;

function CreateTask(tkt){
	if($('tr.new').length) return alert('Please save or delete the newly added row, and try again.');
	AddTaskButton(0);
	tsks = TSK_GetTasksCount()
	TR = TSK_NewRow(tkt,tsks+1); TSK_AddTaskNoColumn(TR,tsks+1);
	TSK_AddDetailColumn(TR);
	TSK_AddRespAndHandleColumn(TR);
	TSK_AddWeightageColumn(TR); TSK_AddActionColumn(TR,tsks+1);
	if(!tsks) SetTaskDefaults()
}

function TSK_NewRow(tkt,tsks){
	return $('<tr>').appendTo($('.tasks tbody')).addClass('new task_row ticket_'+tkt+' task_'+tsks);
}

function TSK_GetTasksCount(){
	return $('tr',$('.tasks tbody')).length;
}

function TSK_AddTaskNoColumn(TR,N){
	LSN = TSK_GetLastTaskNo(); TN = (isNaN(LSN)) ? N+1 : LSN+1;
	NTG('th',{class:'task_no text-center',style:'vertical-align:middle'},N).appendTo(TR);
}

function TSK_GetLastTaskNo(){
	return parseInt($('tr:last',$('.tasks tbody')).find('td.task_no').text())
}

function TSK_AddDetailColumn(TR){
	HTML = NTG('div',{class:'form-inline'},[TSK_TitleField(), TSK_TypeField(), TSK_DescField()])
	NTD(TR,HTML,{class:'task_details'});
	TSK_AddOptionsToSupportType();
}

function TSK_TitleField(){
	return $('<input>').attr({type:'text', name:'title', class:'form-control', placeholder:'Enter Title for the Task'}).css({width:'48%','margin-bottom':'5px'});
}

function TSK_DescField(){
	return $('<textarea>').attr({name:'description', class:'form-control', placeholder:'Enter Technical Description for the Task'}).css({height:'130px', width:'100%'});
}

function TSK_TypeField(){
	return $('<select>').attr({name:'support_type', class:'form-control'}).css({width:'48%','margin-left':'4%','margin-bottom':'5px'});
}

function TSK_AddOptionsToSupportType(){
	if(_SupportTypes) return $('[name="support_type"]').html(SimpleOptions(_SupportTypes));
	FireAPI('api/v1/tkt/get/sp',function(STJ){
		_SupportTypes = $.extend(null,{'':'Select Support Type'},STJ);
		TSK_AddOptionsToSupportType();
	});
}

function TSK_AddRespAndHandleColumn(TR){
	HTML = [
		TSK_AssignField(),
		TSK_HandleFields(),
		TSK_Handlables(),
	]
	NTD(TR,HTML,{class:'resp_handle'})
	TSK_AddAssignUsers();
	TSK_AddHandlableItem();
}

function TSK_HandleFields(){
	return $('<div>').addClass('clearfix text-left handle').html([
		NTG('div',{class:'clearfix'},NTG('h4',{style:'margin-bottom:4px; margin-top:15px'},'Handle Method')),
		$('<label>').addClass('radio-inline').html([NTG('input',{type:'radio', name:'handle_after', value:'', 'onclick':'HandleChanged()', 'checked':true}),'Immediate']),
		$('<label>').addClass('radio-inline').html([NTG('input',{type:'radio', name:'handle_after', value:'after_task', 'onclick':'HandleChanged()'}),'After Tasks']),
	])
}

function TSK_Handlables(){
	return $('<div>').addClass('clearfix text-left handlables').css('display','none');
}

function TSK_AddHandlableItem(){
	FireAPI('api/v1/tkt/get/hat',function(HATJ){
		HTML = $('<div>').addClass('clearfix text-left handlable_items').html(
			$.map(HATJ,function(seq,id){
				//return NTG('div',{class:'checkbox'},NTG('label',{},[NTG('input',{type:'checkbox', name:'after_task[]', value:id}),'TASK #'+seq]));
				return NTG('label',{class:'checkbox-inline', style:'margin-left:0px !important; margin-right:20px !important'},[NTG('input',{type:'checkbox', name:'after_task[]', value:id}),'TASK #'+seq]);
			})
		);
		$('.handlables').html(HTML);
	},{tkt:_TICKET})
}

function TSK_AssignField(){
	return NTG('select',{class:'form-control', name:'responder'},'')
}

function TSK_AddAssignUsers(){
	if(_AssignUsers) return $('[name="responder"]').html(SimpleOptions(_AssignUsers));
	FireAPI('api/v1/tkt/get/tau',function(TAUJ){
		_AssignUsers = $.extend({'':'Select Responder'},TAUJ);
		TSK_AddAssignUsers();
	},{tkt:_TICKET});
}

function TSK_AddWeightageColumn(TR){
	HTML = TSK_WeightageField()
	NTD(TR,HTML,{class:'weightage'});
	CalculateWeightage(TSK_GetTasksCount())
}

function TSK_WeightageField(){
	return NTG('input',{class:'form-control', name:'weightage[]', onkeyup:'WeightageChanged('+TSK_GetTasksCount()+')', onchange:'WeightageChanged('+TSK_GetTasksCount()+')', 'data-wno':TSK_GetTasksCount()});
}

function TSK_AddActionColumn(TR,tsks){
	HTML = TSK_ActionButtons(tsks);
	NTD(TR,HTML,{class:'action'});
}

function TSK_ActionButtons(tsks){
	return [
		$('<a>').attr({href:'javascript:SaveTask("'+_TICKET+'","'+tsks+'")', class:'btn btn-primary', style:'margin-bottom:3px'}).text('Save'),
		$('<a>').attr({href:'javascript:DeleteRow("'+tsks+'")', class:'btn btn-danger', style:'margin-bottom:3px'}).text('Delete')
		]
}

function CalculateWeightage(No){
	$('[data-wno="'+No+'"]').val(parseInt(100/parseInt(No))).trigger('change');
}

function WeightageChanged(No){
	No = parseInt(No);
	if(No > 1) return setTimeout(UpdateWeightage,50,(No-1));
	if(TSK_GetTasksCount() > 1) InfoSaveWeightage();
}

function UpdateWeightage(No){
	WSA = parseInt(WeightageSumAfter(No)); WSB = parseInt(WeightageSumBefore(No));
	NewIn = parseInt(100-WSA); NewPer = NewIn/WSB;
	WF = $('[data-wno="'+No+'"]'); OldValue = parseInt(WF.val()); NewValue = parseInt(OldValue*NewPer);
	WF.val(NewValue).trigger('change');
}

function WeightageSumAfter(No){
	SUM = 0;
	for(i = parseInt(No)+1; i <= TSK_GetTasksCount(); i++){
		V = parseInt($('[data-wno="'+i+'"]').val());
		SUM += isNaN(V) ? 0 : V;
	}
	return SUM;
}

function WeightageSumBefore(No){
	SUM = 0;
	for(i = 1; i <= parseInt(No); i++){
		V = parseInt($('[data-wno="'+i+'"]').val());
		SUM += isNaN(V) ? 0 : V;
	}
	return SUM;
}

function InfoSaveWeightage(){
	$('.swb').remove();
	btn('Save Weightage','javascript:DoSaveWeightage()','floppy-save').addClass('btn-primary pull-right swb btn-sm').append(" Save Weightage").appendTo('.tasks_panel .panel-heading').css('margin-right','5px');
}

function DoSaveWeightage(){
	SA = $('[name^="weightage"]').serializeArray();
	if($.isEmptyObject(SA)) return;
	FireAPI('api/v1/tkt/action/'+_TICKET+'/uw',function(WJ){
		$.each(WJ,function(k,v){
			$('[name="weightage['+k+']"]').val(v);
		});
		$('.swb').remove();
	},SA);
}






function DeleteRow(tsk){
	$('tr.new').remove();
	$('[name^="weightage"]:last').trigger('change');
	AddTaskButton(1);
	setTimeout(DoSaveWeightage,1000);
}

function SaveTask(tkt, tsk){
	TR = $('tr.new');
	SA = TSK_GetNewTaskEntries(tsk); SA = SA.concat($('[name="handle_after"]').serializeArray(),$('[name="after_task[]"]').serializeArray());
	FireAPI('api/v1/tkt/action/tkt/'+tkt+'/tsk/create',function(R){
		TSK_PostSaveTasks(R)
	},SA);
}

function TSK_GetNewTaskEntries(tsk){
	fields = ['title','description','support_type','responder','weightage[]'];
	serialObj = []; TR = $('tr.new')
	$.each(fields,function(i, Field){
		serialObj.push({name:Field, value:$('[name="'+Field+'"]',TR).val()});
	})
	return serialObj;
}

function TSK_PostSaveTasks(R){
	UpdateNewWeightageName(R.id);
	DoSaveWeightage();
	AddTaskButton(1);
	TSK_ReplaceFieldsWithData();
	$('tr.new').removeClass('new');
}

function TSK_ReplaceFieldsWithData(){
	TR = $('tr.new');
	TSK_ReplaceDetailTD();
	TSK_ReplaceTespHandleTD();
	TD = $('.action',TR); TD.html('');
}

function TSK_ReplaceDetailTD(){
	TD = $('.task_details',$('tr.new'));
	V = [$('[name="title"]',TD).val()," ("+$('[name="support_type"] option:selected',TD).text()+")",$('[name="description"]',TD).val()];
	H = ['strong','small','br'];
	A = [new Object({})]; HTML = TSK_GetHTMLContents([H,V,A],[0,0],[1,1],[2],2);
	TD.html(HTML);
}

function TSK_ReplaceTespHandleTD(){
	TD = $('.resp_handle',$('tr.new')); $('[name="handle_after"]').each(function(i,rb){ if($(rb).prop('checked')) HA = $(rb).parent().text(); })
	H = ['br','strong'];
	V = ['Responder:','Handle Method:',$('[name="responder"] option:selected',TD).text(),HA];
	A = [new Object({})]; HTML = TSK_GetHTMLContents([H,V,A],[1,0],[0],2,[0],[0],[1,1],[0],3,[3]);
	TD.html(HTML);
}

function TSK_GetHTMLContents(D){
	args = arguments; HTML = [];
	$.each(args,function(i,A){
		if(i > 0){
			if($.isArray(A)) HTML.push( NTG(D[0][A[0]], D[2][A[2]] ,D[1][A[1]]) )
			else HTML.push(D[1][A])
		}
	})
	return HTML;
}

function UpdateNewWeightageName(ID){
	return $('tr.new td.weightage input[name^="weightage"]').attr('name','weightage['+ID+']');
}






function SetTaskDefaults(){
	TR = $('tr.new.task_1');
	$('input[name="title"]',TR).val($('th.tkt_title').text());
	$('textarea[name="description"]',TR).val($('td.tkt_desc').text());
	$('input[name="handle_after"]:first',TR).prop('checked',true);
	SetTaskDefaults_RSP(); SetTaskDefaults_ST();
}
function SetTaskDefaults_ST(){ console.log('yoy');
	TR = $('tr.new.task_1'); Opt = $('select[name="support_type"] option:eq(1)',TR);
	if(Opt.length) return Opt.prop('selected',true);
	return setTimeout(SetTaskDefaults_ST,500);
}
function SetTaskDefaults_RSP(){ console.log('boy');
	TR = $('tr.new.task_1'); Opt = $('select[name="responder"] option:eq(2)',TR);
	if(Opt.length) return Opt.prop('selected',true);
	return setTimeout(SetTaskDefaults_RSP,500);
}














function AddTaskButton(status){
	if(status) $('.tasks_panel .panel-heading a').removeClass('disabled')
	else $('.tasks_panel .panel-heading a').addClass('disabled')
}

function HandleChanged(){
	$('[name="handle_after"]').each(function(k,RD){
		if($(RD).prop('checked')){
			if($(RD).val() == 'after_task') Handlables(1);
			else Handlables(0);
		}
	})
}

function Handlables(show){
	$('.handlables')[(show)?'slideDown':'slideUp'](150);
}

function ChangePanelVisibility(p,v){
	p = $('.'+p+' .panel-body');
	if(typeof(v) == 'undefined') v = p.css('display') == 'none';
	p[(v)?'slideDown':'slideUp'](200);
	A = ['glyphicon-minus','glyphicon-plus']; i = v?1:0;
	p.parents('.panel-default').find('.panel-heading a[href^="javascript:ChangePanelVisibility"] span').removeClass(A[i]).addClass(A.reverse()[i]);
}




function NTD(TR,HTML,ATTR){
	return NTG('td',ATTR,HTML).appendTo(TR)
}

function NTG(TG,ATTR,HTML){
	TAG = $('<'+TG+'>')
	if(typeof(ATTR) != "undefined") TAG.attr(ATTR);
	if(typeof(HTML) != "undefined") TAG.html(HTML);
	return TAG;
}

function SimpleOptions(J){
	return $.map(J,function(t,v){
		return $('<option>').attr({value:v}).text(t);
	})
}






