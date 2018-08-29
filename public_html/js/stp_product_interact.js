// JavaScript Document

$(function(){
	PopulateProducts();
	CreateDownloadModal();
})

function PopulateProducts(){
	TBD = $('table.product tbody'); TBD.empty();
	$.each(_Product,function(PCode, Obj){
		TR = NTR(TBD);
		PTD = NTD(TR,Obj.name+((Obj.private == "YES")?' (Private)':'')); ETDC = 0;
		$.each(Obj.editions,function(ECode, Obj2){
			if(ETDC++ > 0) { TR = NTR(TBD); IRS(PTD); }
			ETD = NTD(TR,Obj2.name+((Obj2.private == "YES")?' (Private)':'')); VTD = NTD(TR,Obj2.version);
			ATD = NTD(TR,ProductActions(PCode,ECode));
		})
	})
}


function NTR(TBD){
	return $('<tr>').appendTo(TBD);
}
function NTD(TR,T){
	return $('<td>').attr('rowspan','1').appendTo(TR).html(T);
}
function IRS(TD){
	if($.isArray(TD)) return $.each(TD,function(x,td){ IRS($(td)); });
	RS = parseInt(TD.attr('rowspan')); NRS = RS+1; TD.attr('rowspan',NRS);
}

function ProductActions(P,E){
	return [
		btn('View Product and Edition Details','javascript:ViewProductEditionDetails("'+P+'","'+E+'")','info-sign'),
		btn('Download/Generate Link','javascript:GenerateLink("'+P+'","'+E+'")','cloud-download'),
	]
}

function ViewProductEditionDetails(P,E){
	$(".panel:gt(0)").remove();
	PRD = _Product[P]; EDN = PRD['editions'][E];
	PBD = GetBSPanel(PRD.name).appendTo('.content').find('.panel-footer').remove().end().find('.panel-body');
	$('<p>').text(PRD.description).appendTo(PBD);
	ul = $('<ul>').appendTo(PBD).html($.map(PRD.editions,function(EO,EC){
		return $('<li>').html($('<dl>').html([
			$('<dt>').html(EO.name+((EO.private == "YES")?' (Private)':'')),
			$('<dd>').html(EO.description),
		]))
	}));
	ScrollBottom();
}

function ScrollBottom(){
	$("html, body").animate({ scrollTop: $(document).height() }, 150);
}

function GenerateLink(PID, EID){
	$('#downloadModal').modal('show'); PKG = $('#downloadModal [name="package"]').empty(); PKG.attr({'data-product':PID,'data-edition':EID});
	ValidityChanged(0);
	$.getJSON('api/v1/tst/get/packages',{PID:PID,EID:EID},function(JA){
		$('#downloadModal [name="package"]').html(CreateNodes($.map(JA,function(Q){ return Q.packages; }),'option','name',{value:'code','data-type':'type'}))
	})
}

function ValidityChanged(i){
	if(i) return $('[name="generatedlink"]').val('please wait, creating link..').parent().slideDown().prev().slideUp();
	return $('[name="generatedlink"]').val('').parent().slideUp().prev().slideDown();
}

function GenerateDownloadLink(){
	PKG = $('#downloadModal [name="package"]'); VAL = $('[name="validity"]'); 
	ValidityChanged(1);
	$.get('api/v1/get_download_link',{PID:PKG.attr('data-product'),EID:PKG.attr('data-edition'),PKG:PKG.val(),VAL:VAL.val(),TYP:PKG.find("option:selected").attr('data-type')},function(JL){
		$('[name="generatedlink"]').val(JL).next().attr('href',JL);
	})
}

function CreateDownloadModal(){
	modal = GetBSModal('Create Download Link').appendTo('body').attr('id','downloadModal');
	modal.find('.modal-footer').html($('<button>').attr({class:'btn btn-default',type:'button','data-dismiss':'modal'}).text('Close'));
	mbdy = modal.find('.modal-body');
	mbdy.html([
		CreateDownloadModal_PackageFormGroup(),
		CreateDownloadModal_ValidityFormGroup(),
		CreateDownloadModal_GenerateButton(),
		CreateDownloadModal_TextArea()
	])
}

function CreateDownloadModal_PackageFormGroup(){
	return $('<div>').addClass('form-group form-horizontal clearfix').html([
		$('<label>').addClass('control-label col-xs-4').text('Select Package'),
		$('<div>').addClass('col-xs-8').html([
			$('<select>').attr({name:'package','data-product':'','data-edition':'',class:'form-control'})
		])
	])
}

function CreateDownloadModal_ValidityFormGroup(){
	return $('<div>').addClass('form-group form-horizontal clearfix').html([
		$('<label>').addClass('control-label col-xs-4').text('Validity'),
		$('<div>').addClass('col-xs-8').html([
			$('<select>').attr({name:'validity','onChange':'ValidityChanged()',class:'form-control'}).html(CreateDownloadModal_ValidityFormGroup_Options())
		])
	])
}

function CreateDownloadModal_ValidityFormGroup_Options(){
	return $.map(['1 Hour','2 Hours','6 Hours','12 Hours','1 Day','2 Days','3 Days'],function(T){
		return $('<option>').attr({value:T}).text(T);
	})
}

function CreateDownloadModal_GenerateButton(){
	return $('<div>').addClass('form-group form-horizontal clearfix col-xs-12 generatebutton').html([
		$('<a>').attr({'href':'javascript:GenerateDownloadLink()',class:'btn btn-primary pull-right'}).text('Generate Download Link')
	])	
}

function CreateDownloadModal_TextArea(){
	return $('<div>').addClass('form-group form-horizontal clearfix col-xs-12').css('display','none').html([
		$('<textarea>').attr({'name':'generatedlink',class:'form-control',style:'height: 100px;'}),
		$('<a>').attr({'href':'',target:'_blank'}).text('Click here to download now')
	])	
}
















