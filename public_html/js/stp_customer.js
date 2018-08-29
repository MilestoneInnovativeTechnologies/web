// JavaScript Document

var _modalHeading = {
	'modalCustomerProductUpdates':"Send Product's latest update details and download links.",
	'modalCustomerProductInformation':"Send Product's Information and download links.",
	'modalResetCustomerLogin':"Send login reset link",
	'modalCustomerPresale':"Change Presale dates of customer",
	'modalTktCategoryPermit':"Allow/Disallow ticket category for the customer",
}
var _modalBodyTblClsNRows = {
	'modalCustomerProductUpdates':['striped product_update',['Customer Mail','Product','Edition','Package','Version','Build Date','Change Log']],
	'modalCustomerProductInformation':['striped product_information',['Product','Edition','Package','Email']],
	'modalResetCustomerLogin':['striped reset_distributor_login',['Code','Name','Email']],
	'modalCustomerPresale':['striped customer_presale',['Name','Product','Presale Start Date','Presale End Date','Presale Extend To']],
	'modalTktCategoryPermit':['striped tkt_category',['Customer','Product','Category','Current Status','Allow or Disallow']],
}
var _modalFooterButtons = {
	'modalCustomerProductUpdates':['Close',['SendProductUpdateMail','Send Update details and Download Links']],
	'modalCustomerProductInformation':['Close',['SendProductInformationMail','Send Product details and Download Links']],
	'modalResetCustomerLogin':['Close',['SendLoginResetLink','Send Login Reset Link by Mail']],
	'modalCustomerPresale':['Close',['ChangeCustomerPresale','Change Presale Dates']],
	'modalTktCategoryPermit':['Close',['ChangeTktcatPerm','Update ticket category permission']],
}








function SearchCustomers(){
	location.search = '?page=1&search_text=' + $('[name="search_text"]').val()
}








function ResetCustomerLogin(Code, Name, Mail){
	modal = getModal('modalResetCustomerLogin').modal('show').attr('data-code',Code);
	FillModalTable(modal,{Code:Code,Name:Name,Email:Mail});
}
function SendLoginResetLink(){
	ID = 'modalResetCustomerLogin';
	Code = $('#'+ID).modal('hide').attr('data-code');
	FireAPI('api/v1/tst/action/sclrl',DoneSCLRL,{C:Code});
}
function DoneSCLRL(SDLRLJSON){
	alert(SDLRLJSON[1] + ', Login Reset link have mailed to '+SDLRLJSON[2]);
}






$_ProductInformations = {}; $_PRDOptions = {}; $_EDNOptions = {}; $_PKGOptions = {}; $_PKGTypes = {"Onetime":[], "Update":[]};
function SendProductInformation(Code, Name, Mail){
	modal = getModal('modalCustomerProductInformation').modal('show').attr({'data-code':Code,'data-name':Name,'data-email':Mail});
	FillModalTable(modal,{Email:Mail});
	if(!$.isEmptyObject($_ProductInformations)) return SetModalPI($_ProductInformations);
	FireAPI('api/v1/tst/get/pi',InitModalPI);
}
function InitModalPI(PIJ){
	$_ProductInformations = PIJ;
	StoreOptions(PIJ)
	SetModalPI(PIJ);
}
function SetModalPI(J){
	ID = 'modalCustomerProductInformation'; modal = getModal(ID);
	PDST = GetSelectTag('product').attr({'onchange':'ProductChanged(this.value,"'+ID+'",true)'}).html(GetStoredOptions('$_PRDOptions',Object.keys(J)));
	EDST = GetSelectTag('edition').attr({'onchange':'EditionChanged(this.value,"Onetime","'+ID+'",true)'});
	PKST = GetSelectTag('package').attr({'onchange':'PackageChanged(this.value,"'+ID+'")'});
	FillModalTable(modal,{Product:PDST,Edition:EDST,Package:PKST}); PDST.trigger('change');
}
function ProductChanged(PRD,ID,SelAll){
	$Editions = Object.keys($_ProductInformations[PRD]); SetValue('product',PRD,ID);
	Sel = $('[name="edition"]').html(GetStoredOptions('$_EDNOptions',$Editions));
	if(SelAll) Sel.prepend(GetSimpleOption('*','All Editions')).val("*");
	Sel.trigger('change');
}
function EditionChanged(EDN,PKGTYPE,ID,SelAll){
	SetValue('edition',EDN,ID); PRD = GetValue('product',ID);
	Sel = $('[name="package"]').empty();
	if(EDN != "*"){
		$Packages = $.map($_ProductInformations[PRD][EDN][PKGTYPE],function(Obj){ return Obj.package.code; });
		Sel.html(GetStoredOptions('$_PKGOptions',$Packages));
	}
	if(SelAll) Sel.prepend(GetSimpleOption('*','All Packages')).val("*");
	Sel.trigger('change');
}
function PackageChanged(PKG,ID){
	SetValue('package',PKG,ID);
}
function SendProductInformationMail(){
	ID = 'modalCustomerProductInformation';
	PRD = GetValue('product',ID); EDN = GetValue('edition',ID); PKG = GetValue('package',ID); SND = GetValue('email',ID);
	FireAPI('api/v1/tst/action/spi',DoneSPI,{PRD:PRD,EDN:EDN,PKG:PKG,SND:SND});
	getModal(ID).modal('hide');
}
function DoneSPI(R){
	if($.isArray(R)) alert(R[0]);
	else alert('Product information have sent successfully.');
}






function SendProductUpdates(Code, Name, Mail){
	ID = 'modalCustomerProductUpdates'; modal = getModal(ID).modal('show').attr({'data-code':Code,'data-name':Name,'data-email':Mail});
	if(!$.isEmptyObject($_ProductInformations)) return SetModalPU($_ProductInformations);
	FireAPI('api/v1/tst/get/pi',InitModalPU);
}
function InitModalPU(PIJ){
	$_ProductInformations = PIJ;
	StoreOptions(PIJ)
	SetModalPU(PIJ);
}
function SetModalPU(PIJ){
	ID = 'modalCustomerProductUpdates'; modal = getModal(ID); Code = GetValue('code',ID);
	PDST = GetSelectTag('product').attr({'onchange':'LoadCustomerProductEditions(this.value,"'+ID+'","'+Code+'")'}).html(GetStoredOptions('$_PRDOptions',Object.keys($_CustomerProducts[Code])));
	EDST = GetSelectTag('edition').attr({'onchange':'LoadCustomerProductEditionPackages(this.value,"Update","'+ID+'","'+Code+'")'});
	PKST = GetSelectTag('package').attr({'onchange':'GetUpdateDetails(this.value,"'+ID+'")'});
	FillModalTable(modal,{'Customer Mail':GetValue('email',ID),Product:PDST,Edition:EDST,Package:PKST});
	PDST.trigger('change');
}
function LoadCustomerProductEditions(PRD,ID,CUS){
	modal = getModal(ID); SetValue('product',PRD,ID);
	$('[name="edition"]',modal).html(GetStoredOptions('$_EDNOptions',$_CustomerProducts[CUS][PRD])).trigger('change');
}
function LoadCustomerProductEditionPackages(EDN,TYP,ID,CUS){
	modal = getModal(ID); PRD = GetValue('product',ID); SetValue('edition',EDN,ID);
	$Packages = $.map($_ProductInformations[PRD][EDN][TYP],function(Obj){ return Obj.package.code; });
	$('[name="package"]',modal).html(GetStoredOptions('$_PKGOptions',$Packages)).trigger('change');
}
function GetUpdateDetails(PKG,ID){
	PRD = GetValue('product',ID); EDN = GetValue('edition',ID);
	FireAPI('api/v1/tst/get/pvd',function(PVD){
		ID = 'modalCustomerProductUpdates'; modal = getModal(ID);
		if($.isEmptyObject(PVD)){ V = D = C = ""; } else {
			V = PVD.version_numeric;
			D = PVD.build_date; if(D) D = ReadableDate(D);
			C = PVD.change_log; if(C) C = C.replace(/(?:\r\n|\r|\n)/g, '<br>');
		}
		SetValue('version',V,ID);
		FillModalTable(modal,{'Version':V,'Build Date':D,'Change Log':C});
	},{PRD:PRD,EDN:EDN,PKG:PKG})
}
function SendProductUpdateMail(){
	ID = 'modalCustomerProductUpdates';
	PRD = GetValue('product',ID); EDN = GetValue('edition',ID); PKG = GetValue('package',ID); CUS = GetValue('email',ID); VER = GetValue('version',ID);
	FireAPI('api/v1/tst/action/spum',DoneSPUM,{PRD:PRD,EDN:EDN,PKG:PKG,SND:'Customer',DPE:CUS,VER:VER})
	getModal(ID).modal('hide');
}
function DoneSPUM(R){
	if($.isArray(R)) alert(R[0]);
	else alert('Product update details have sent successfully.');
}






function ChangePresaleDates(Code, Name){
	ID = 'modalCustomerPresale'; modal = getModal(ID).modal('show').attr({'data-code':Code,'data-name':Name});
	PDST = GetSelectTag('product').attr({'onchange':'PresaleProductChanged("'+Code+'",this.value,"'+ID+'")'}).html($.map($_CustProdSeqs[Code]['Product'],function(Ary,Val){ return GetSimpleOption(Val,Ary[4]) }));
	SDIT = GetInputTag('start_date'); EDIT = GetInputTag('end_date'); EXIT = GetInputTag('extend_date');
	FillModalTable(modal,{'Name':Name,'Product':PDST,'Presale Start Date':SDIT,'Presale End Date':EDIT,'Presale Extend To':EXIT});
	$("[name='start_date'],[name='end_date'],[name='extend_date']",modal).datepicker({format:'yyyy-mm-dd',autoclose:true});
	PDST.trigger('change');
}
function PresaleProductChanged(Cus,Seq,ID){
	PRD = $_CustProdSeqs[Cus]['Product'][Seq][0]; EDN = $_CustProdSeqs[Cus]['Product'][Seq][2];
	SetValue('seqno',Seq,ID); SetValue('product',PRD,ID); SetValue('edition',EDN,ID);
	FireAPI('api/v1/tst/get/psd',function(PSD){
		if(!$.isEmptyObject(PSD)){
			modal = getModal(ID); SD = PSD.registered_on; ED = PSD.presale_enddate; EX = PSD.presale_extended_to;
			if(SD) { SD = ReadableDate(SD); FillModalTable(modal,{'Presale Start Date':SD}); }
			if(ED) { ED = ReadableDate(ED); FillModalTable(modal,{'Presale End Date':ED}); }
			if(EX) {
				if(ED) $('[name="extend_date"]').val(EX).datepicker("setDate", new Date(EX) );
				else $('[name="extend_date"]').prop('disabled',true);
			} else {
				if(!ED) $('[name="extend_date"]').prop('disabled',true);
				else $('[name="extend_date"]').prop('disabled',false);
			}
		}
	},{CUS:Cus,SEQ:Seq});
}
function ChangeCustomerPresale(){
	ID = 'modalCustomerPresale';
	CUS = GetValue('code',ID); SEQ = GetValue('seqno',ID); SD = $('[name="start_date"]').val(); ED = $('[name="end_date"]').val(); EX = $('[name="extend_date"]').val();
	FireAPI('api/v1/tst/action/upd',function(R){ },{CUS:CUS,SEQ:SEQ,SD:SD,ED:ED,EX:EX});
	getModal('modalCustomerPresale').modal('hide');
}




























function StoreOptions(PIJ){
	$.each(PIJ,function(PRD,EDObj){
		$.each(EDObj,function(EDN,PTObj){
			$.each(PTObj,function(PKT,PKAry){
				$.each(PKAry,function(x,Obj){
					if(!$_PRDOptions[PRD]) $_PRDOptions[PRD] = GetSimpleOption(PRD,Obj.product.name);
					if(!$_EDNOptions[EDN]) $_EDNOptions[EDN] = GetSimpleOption(EDN,Obj.edition.name);
					PKGCode = Obj.package.code; PKGName = Obj.package.name;
					if(!$_PKGOptions[PKGCode]) $_PKGOptions[PKGCode] = GetSimpleOption(PKGCode,PKGName);
					$_PKGTypes[PKT].push(PKGCode);
				})
			})
		})
	})
}

function GetStoredOptions(VAR,ARY){
	Options = [];
	$.each(ARY,function(j,C){
		Options.push(eval(VAR+'["'+C+'"]'));
	})
	return Options;
}
function GetSimpleOption(V,T){
	return $('<option>').attr({value:V}).text(T);
}
function GetSimpleOptions(Ary){
	return $.map(Ary,function(T,V){
		return GetSimpleOption(V,T);
	});
	return $('<option>').attr({value:V}).text(T);
}
function GetSelectTag(name){
	return $('<select>').attr({'class':'form-control','name':name});
}
function GetInputTag(name){
	return $('<input>').attr({'type':'text','class':'form-control','name':name});
}
function SetValue(N,V,ID){
	if(ID){ jSel = $('#'+ID);	}
	else {
		if($('.modal.fade.in').length){
			jSel = $('.modal.fade.in');
		} else {
			return setTimeout(SetValue,500,N,V);
		}
	}
	jSel.attr('data-'+N,V);
}
function GetValue(N,ID){
	jSel = (ID) ? $('#'+ID) : $('.modal.fade.in');
	return jSel.attr('data-'+N);
}







function TicketCategoryPermit(Code, Name){
	ID = 'modalTktCategoryPermit'; modal = getModal(ID).modal('show').attr({'data-code':Code,'data-name':Name});
	FireAPI('api/v1/tst/get/pntc',function(DJ){
		modal = getModal('modalTktCategoryPermit');
		modal.data('current',DJ.current)
		FillModalTable(modal,{
			'Customer':modal.attr('data-name'),
			'Product':GetSelectTag('tcp_product').html(GetSimpleOptions(DJ.products)).attr({onchange:'TCP_ItemChanged()'}),
			'Category':GetSelectTag('tcp_category').html(GetSimpleOptions(DJ.categories)).attr({onchange:'TCP_ItemChanged()'}),
			'Allow or Disallow':GetSelectTag('allow_disallow').html(GetSimpleOptions({'allow':'Allow','disallow':'Disallow'})),
		});
		TCP_ItemChanged();
	},{cus:Code})
}
function ChangeTktcatPerm(){
	ID = 'modalTktCategoryPermit'; modal = getModal(ID).modal('hide');
	FireAPI('api/v1/tst/action/uptc',function(DJ){
		
	},{cus:modal.attr('data-code'),seq:$('[name="tcp_product"]').val(),cat:$('[name="tcp_category"]').val(),sta:$('[name="allow_disallow"]').val()})
}

function TCP_ItemChanged(){
	P = $('[name="tcp_product"]').val(); C = $('[name="tcp_category"]').val(); modal = getModal('modalTktCategoryPermit');
	cur = modal.data('current');
	if(!cur[P]) { $('[name="allow_disallow"]').val('allow'); return FillModalTable(modal,{ 'Current Status':'NULL' }); }
	if($.inArray(C,cur[P]) > -1) { $('[name="allow_disallow"]').val('disallow'); return FillModalTable(modal,{ 'Current Status':'Allowed' }); }
	$('[name="allow_disallow"]').val('allow'); FillModalTable(modal,{ 'Current Status':'Disallowed' });
}








// Modal Functions


function getModal(ID){
	if($('#'+ID).length) return $('#'+ID);
	else return CreateModal(ID).appendTo('body').attr('id',ID);
}

function CreateModal(ID){
	return GetBSModal(getModalHeading(ID)).find('.modal-body').html(GetModalBodyHtml(ID)).end().find('.modal-footer').html(GetModalFooterButtons(ID)).end();
}

function getModalHeading(ID){
	return _modalHeading[ID];
}

function GetModalBodyHtml(ID){
	FN = "MBH_"+ID;
	if(window[FN]) return window[FN]();
	return GetBSTable(_modalBodyTblClsNRows[ID][0]).find('tbody').html(TblRows(_modalBodyTblClsNRows[ID][1])).end();
}

function TblRows(RAry){
	return $.map(RAry,function(HD){
		cls = HD.replace(/\s/g,'_').toLowerCase();
		return $('<tr>').addClass(cls).html([$('<th>').addClass('thd').text(HD),$('<td>').addClass('tbd').text('')]);
	})
}

function GetModalFooterButtons(ID){
	Buttons = _modalFooterButtons[ID];
	return $.map(Buttons,function(BN){
		if(typeof(BN) == 'string') return ReadyMadeButton(BN);
		return $('<button>').attr({type:'button',class:'btn btn-info',onClick:'javascript:'+BN[0]+'()'}).html(BN[1]);
	})
}

function ReadyMadeButton(BN){
	if(BN == 'Close') return $('<button>').attr({type:'button',class:'btn btn-default','data-dismiss':'modal'}).text('Close');
}

function FillModalTable(modal,ValObj){
	$.each(ValObj,function(tr,Value){
		cls = tr.replace(/\s/g,'_').toLowerCase();
		$('td.tbd',$('tr.'+cls)).html(Value);
	})
}
