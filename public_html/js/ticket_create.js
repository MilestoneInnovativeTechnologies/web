// JavaScript Document

$(function(){
	if($('input[name="customer"]').length){
		$('[name="customer"]').autocomplete({
			minLength: 1,
			source: '/api/v1/tkt/get/src',
			select: function(event, ui){ $('[name="customer"]').val(ui.item.code); SetCustomer(ui.item.code); return false; },
			focus: function(event, ui){ $('[name="customer"]').val(ui.item.code); SetCustomer(ui.item.code); return false; }
		}).autocomplete( "instance" )._renderItem = function(ul, item) {
				return $( "<li>" ).appendTo( ul ).append( "<div>" + item.name + "<small class='pull-right'><em>("+DistName(item)+")</em></small></div>" );
			};
		if($('input[name="customer"]').val() != "") { LoadCustomerProducts($('input[name="customer"]').val()); }
	} else {
		LoadMyProducts(); LoadMyCategories();
	}
})

function SetCustomer(C){
	__CUSTOMER = C;
}

function LoadMyProducts(){
	$('[name="product"]').empty();
	FireAPI('api/v1/tkt/get/mp',function(PJ){
		$('[name="product"]').empty().html(getSimpleOptions(PJ)).trigger('change')
	})
}

function LoadCustomerProducts(customer){
	$('[name="product"]').empty()
	FireAPI('api/v1/tkt/get/cp',function(CJ){
		$('[name="product"]').empty().html(getSimpleOptions(CJ)).trigger('change')
	},{customer:customer})
}

function ProductChanged(){
}

function DistName(Obj){
	P = Obj;
	while(true){
		P = P.parent_details[0];
		if(arrayHasKeyVal(P.roles,'name','distributor')) return P.name;
	}
}

function arrayHasKeyVal(Ary,Key,Val){
	has = false;
	$.each(Ary,function(i,Obj){ if(Obj[Key] == Val) has = true; })
	return has;
}

function AddAttachment(){
	N = GetNextAttachmentNum();
	TR = NewAttachmentTR(N);
	$('table.attachments tbody').append(TR);
	return N;
}

function NewAttachmentTR(N){
	return $('<tr>').attr({'data-row':N}).html([
		$('<td>').html($('<input>').attr({type:'text', class:'form-control', name:'attachment['+N+'][name]', placeholder:'Name'})),
		$('<td>').html($('<input>').attr({type:'file', class:'form-control', name:'attachment['+N+'][file]'})),
		$('<td>').html($('<a>').attr({href:'javascript:RemoveAttachment("'+N+'")', class:'btn btn-default'}).html($('<span>').attr({class:'glyphicon glyphicon-minus'}))),
	])
}

function GetNextAttachmentNum(){
	TBD = $('table.attachments tbody');
	if(!$('tr',TBD).length) return 0;
	return 0-(Math.abs($('tr:last',TBD).attr('data-row'))+1);
}

function RemoveAttachment(N){
	$('table.attachments tr[data-row="'+N+'"]').remove();
}

function AddAttachmentData(N,Name,File){
	TR = $('table.attachments tr[data-row="'+N+'"]');
	$('td:first input',TR).val(Name);
	$('td:eq(1) input',TR).attr({type:'text', readonly:true, value:File});
}

function CreateAndAddAttachmentData(Name,File,Id){
	N = AddAttachment();
	if(Id) N = ChangeAttachmentTRNum(N,Id);
	AddAttachmentData(N,Name,File);
}

function ChangeAttachmentTRNum(F,T){
	TR = $('table.attachments tr[data-row="'+F+'"]').attr('data-row',T);
	$.each(['name','file'],function(i,N){ $('[name="attachment['+F+']['+N+']"]',TR).attr('name','attachment['+T+']['+N+']') });
	$('a',TR).attr('href','javascript:RemoveAttachment("'+T+'")');
	return T;
}

function kol(){
	$('[name="category"]:first').find('option:not([orgin])').remove();
}





//--------------CATEGORIES--------------------------//
$.getScript('/js/category_management.js')