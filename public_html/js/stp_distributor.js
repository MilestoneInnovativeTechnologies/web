// JavaScript Document

var _modalHeading = {
	'modalProductUpdate':"Send Product's latest update details and download links.",
	'modalProductInformation':"Send Product's Information and download links.",
	'modalResetDistributorLogin':"Send login reset link",
}
var _modalBodyTblClsNRows = {
	'modalProductUpdate':['striped product_update',['Product','Edition','Package','Version Details','Version','Build Date','Change Log','Send to','Select Customer']],
	'modalProductInformation':['striped product_information',['Product','Edition','Package','Send to','Guest email','Select Customer']],
	'modalResetDistributorLogin':['striped reset_distributor_login',['Code','Name','Email']],
}
var _modalFooterButtons = {
	'modalProductUpdate':['Close',['SendProductUpdateMail','Send Update details and Download Links']],
	'modalProductInformation':['Close',['SendProductInformationMail','Send Product details and Download Links']],
	'modalResetDistributorLogin':['Close',['SendLoginResetLink','Send Login Reset Link by Mail']],
}
		
function SearchDistributors(){
	location.search = '?page=1&search_text=' + $('[name="search_text"]').val()
}






function SendProductUpdates(DCode,DName,DMail){
	ID = 'modalProductUpdate';
	modal = getModal(ID).modal('show').attr({'data-dist':DCode,'data-name':DName,'data-email':DMail});
	$('tr.guest_email,tr.select_customer',modal).css('display','none');
	if($_Relations[DCode]) return SetDistProds2(DCode,ID);
	FireAPI('api/v1/tst/get/dstprd',InitModalPU,{D:DCode});
}
function InitModalPU(DPJson){
	DCode = Object.keys(DPJson)[0];
	StoreGlobals(DPJson[DCode],DCode)
	SetDistProds2(DCode,'modalProductUpdate');
}
function SetDistProds2(DCode,ID){
	PDST = GetSelectTag('product').attr({'onchange':'ProductChanged("'+DCode+'",this.value)'}).html(GetProductOptions(DCode));
	EDST = GetSelectTag('edition').attr({'onchange':'EditionChanged2("'+DCode+'",this.value)'});
	PKST = GetSelectTag('package').attr({'onchange':'PackageChanged2("'+DCode+'",this.value)'});
	STST = GetSelectTag('send_to').attr({'onchange':'SendToChanged2("'+DCode+'",this.value,"'+ID+'")'});
	DCST = GetSelectTag('dpe_customers');
	modal = getModal(ID);
	SendToValues = {}; SendToValues[modal.attr('data-email')] = modal.attr('data-name') + ' - ' + modal.attr('data-email'); SendToValues['Customer'] = 'Customer';
	STST.html(GetOptions(Object.keys(SendToValues),SendToValues));
	FillModalTable(modal,{Product:PDST,Edition:EDST,Package:PKST,'Send to':STST,'Select Customer':DCST});
	PDST.trigger('change');
}
function SendProductUpdateMail(){
	ID = 'modalProductUpdate'; modal = getModal(ID);
	DIST = GetValue('dist',ID); PRD = GetValue('product',ID); EDN = GetValue('edition',ID); PKG = GetValue('package',ID); SND = $('[name="send_to"]',modal).val(); DPE = $('[name="dpe_customers"]').val(); VER = $('tr.version td.tbd',modal).text();
	FireAPI('api/v1/tst/action/spum',DoneSPUM,{DIST:DIST,PRD:PRD,EDN:EDN,PKG:PKG,SND:SND,DPE:DPE,VER:VER})
	getModal(ID).modal('hide');
}
function DoneSPUM(R){
	if($.isArray(R)) alert(R[0]);
	else alert('Product update details have sent successfully.');
	getModal('modalProductUpdate').remove();
}






_DistributorProducts = {};
function SendProductInformation(DCode,DName,DMail){
	ID = 'modalProductInformation';
	modal = getModal(ID).modal('show').attr({'data-dist':DCode,'data-name':DName,'data-email':DMail});
	$('tr.guest_email,tr.select_customer',modal).css('display','none')
	if($_Relations[DCode]) return SetDistProds(DCode,ID);
	FireAPI('api/v1/tst/get/dstprd',InitModalPI,{D:DCode});
}
$_Product = {}; $_Edition = {}; $_Package = {};
function InitModalPI(DPJson){
	DCode = Object.keys(DPJson)[0];
	StoreGlobals(DPJson[DCode],DCode)
	SetDistProds(DCode,'modalProductInformation');
}
function SetDistProds(DCode,ID){
	PDST = GetSelectTag('product').attr({'onchange':'ProductChanged("'+DCode+'",this.value,1)'}).html(GetProductOptions(DCode));
	EDST = GetSelectTag('edition').attr({'onchange':'EditionChanged("'+DCode+'",this.value)'});
	PKST = GetSelectTag('package').attr({'onchange':'PackageChanged("'+DCode+'",this.value)'});
	STST = GetSelectTag('send_to').attr({'onchange':'SendToChanged("'+DCode+'",this.value,"'+ID+'")'});
	GEIT = GetInputTag('guest_email'); DCST = GetSelectTag('distributor_customers');
	modal = getModal(ID);
	SendToValues = {}; SendToValues[modal.attr('data-email')] = modal.attr('data-name') + ' - ' + modal.attr('data-email'); SendToValues['Guest'] = 'Guest'; SendToValues['Customer'] = 'Customer';
	STST.html(GetOptions(Object.keys(SendToValues),SendToValues));
	FillModalTable(modal,{Product:PDST,Edition:EDST,Package:PKST,'Send to':STST,'Guest email':GEIT,'Select Customer':DCST});
	PDST.trigger('change');
}
function SendProductInformationMail(){
	ID = 'modalProductInformation';
	DIST = GetValue('dist',ID); PRD = GetValue('product',ID); EDN = GetValue('edition',ID); PKG = GetValue('package',ID); SND = $('[name="send_to"]').val(); GE = $('[name="guest_email"]').val(); DC = $('[name="distributor_customers"]').val();
	FireAPI('api/v1/tst/action/spi',DoneSPI,{DIST:DIST,PRD:PRD,EDN:EDN,PKG:PKG,SND:SND,GE:GE,DC:DC})
	getModal(ID).modal('hide');
}
function DoneSPI(R){
	if($.isArray(R)) alert(R[0]);
	else alert('Product information have sent successfully.');
	getModal('modalProductInformation').remove();
}








function ResetDistributorLogin(DCode, DName, DMail){
	modal = getModal('modalResetDistributorLogin').modal('show').attr('data-code',DCode);
	FillModalTable(modal,{Code:DCode,Name:DName,Email:DMail});
}
function SendLoginResetLink(){
	ID = 'modalResetDistributorLogin';
	DCode = $('#'+ID).modal('hide').attr('data-code');
	FireAPI('api/v1/tst/action/sdlrl',DoneSDLRL,{D:DCode});
}
function DoneSDLRL(SDLRLJSON){
	alert(SDLRLJSON[1] + ', Login Reset link have mailed to '+SDLRLJSON[2]);
}











function StoreGlobals(ARY,DCode){
	StoreItemsGlobal('$_Product','product.code',['product.name'],ARY);
	StoreItemsGlobal('$_Edition','edition.code',['edition.name'],ARY);
	PKGs = StorePackages(ARY); StoreRelations(DCode,ARY);
}
function StoreItemsGlobal(VAR,KEY,DATA,ARY){
	$.each(ARY,function(n,OBJ){
		STR = VAR+'[OBJ.'+KEY+']';
		if(!eval(STR)){
			eval(STR + ' = []');
			$.each(DATA,function(m,N){
				eval(STR + '.push(OBJ.'+N+')');
			})
		}
	})
}
function StorePackages(ARY){
	PKGs = [];
	$.each(ARY,function(x,Obj){
		$.each(Obj.packages,function(y,Obj){
			C = Obj.package.code;
			if(!$_Package[C]) $_Package[C] = Obj.package.name
			PKGs.push(C);
		})
	})
	return PKGs;
}

$_Relations = {};
function StoreRelations(DC, ARY){
	if($_Relations[DC]) return;
	$_Relations[DC] = {};
	$.each(ARY,function(k,Obj){
		PC = Obj.product.code;
		if(!$_Relations[DC][PC]) $_Relations[DC][PC] = {};
		EC = Obj.edition.code;
		if(!$_Relations[DC][PC][EC]){
			$_Relations[DC][PC][EC] = {'Onetime':[],'Update':[]};
			$.each(Obj.packages, function(l,Obj2){
				$_Relations[DC][PC][EC][Obj2.package.type].push(Obj2.package.code);
			});
		}
	})
}

function GetProductOptions(DCode){
	PRDs = Object.keys($_Relations[DCode]);
	return GetOptions(PRDs,$_Product);
}
function ProductChanged(DC,Val,All){
	EDNs = Object.keys($_Relations[DC][Val]); SetValue('product',Val);
	jSel = $('[name="edition"]');
	if(All) jSel.html($('<option value="*">').text('All Editions'));
	else jSel.empty();
	jSel.append(GetOptions(EDNs,$_Edition)).trigger('change');
}
function EditionChanged(DC,Val){
	PD = $('[name="product"]').val(); SetValue('edition',Val);
	PKG = $('[name="package"]').html($('<option value="*">').text('All Packages'));
	if(Val != "*") PKG.append(GetOptions($_Relations[DC][PD][Val]['Onetime'],$_Package));
	PKG.trigger('change');
}
function EditionChanged2(DC,Val){
	PD = $('[name="product"]').val(); SetValue('edition',Val);
	PKG = $('[name="package"]').empty().append(GetOptions($_Relations[DC][PD][Val]['Update'],$_Package));
	PKG.trigger('change');
}
function PackageChanged(DC,Val){
	SetValue('package',Val);
}
$_PEP = [];
function PackageChanged2(DC,Val){
	SetValue('package',Val); PRD = $('[name="product"]').val(); EDN = $('[name="edition"]').val(); PKG = $('[name="package"]').val();
	if($_PEP && $_PEP[PRD] && $_PEP[PRD][EDN] && $_PEP[PRD][EDN][PKG]) return PopulateVersionDetails($_PEP[PRD][EDN][PKG]);
	FireAPI('api/v1/tst/get/pvd',StorePEP,{PRD:PRD,EDN:EDN,PKG:PKG})
	$('[name="send_to"] option:first',getModal('modalProductUpdate')).prop('selected',true).trigger('change');
}

function SetValue(N,V){
	if($('.modal.fade.in').length) {
		$('.modal.fade.in').attr('data-'+N,V)
	} else {
		setTimeout(SetValue,500,N,V);
	}
}
function GetValue(N,ID){
	jSel = (ID) ? $('#'+ID) : $('.modal.fade.in');
	return jSel.attr('data-'+N);
}
function SendToChanged(DC,ST,ID){
	if(ST == 'Guest') {
		$('tr.guest_email',getModal(ID)).css('display','table-row');
		$('tr.select_customer',getModal(ID)).css('display','none');
	} else  if(ST == 'Customer') {
		$('tr.guest_email',getModal(ID)).css('display','none');
		$('tr.select_customer',getModal(ID)).css('display','table-row');
		LoadDistributorCustomers(GetValue('dist',ID))
	} else {
		$('tr.guest_email',getModal(ID)).css('display','none');
		$('tr.select_customer',getModal(ID)).css('display','none');
	}
}
function SendToChanged2(DC,ST,ID){
	if(ST == 'Customer') {
		$('tr.select_customer',getModal(ID)).css('display','table-row');
		LoadDistributorProductEditionCustomers(DC,GetValue('product',ID),GetValue('edition',ID));
	} else {
		$('tr.select_customer',getModal(ID)).css('display','none');
	}
}
$_DistributorCustomers = {};
function LoadDistributorCustomers(DCode){
	$('[name="distributor_customers"]').html(GetOptions(['-1'],{'-1':'Loading Customers, please wait'}));
	if($_DistributorCustomers[DCode]) return ListCustomers($_DistributorCustomers[DCode]);
	FireAPI('api/v1/tst/get/dce',StoreDistributorCustomers,{D:DCode})
}
$_DPECustomers = {};
function LoadDistributorProductEditionCustomers(D,P,E){
	$('[name="dpe_customers"]').html(GetOptions(['-1'],{'-1':'Loading Customers, please wait'}));
	if($_DPECustomers[D] && $_DPECustomers[D][P] && $_DPECustomers[D][P][E]) return ListDPECustomers($_DPECustomers[D][P][E]);
	FireAPI('api/v1/tst/get/dpece',StoreDPECustomers,{D:D,P:P,E:E})
}
function StoreDistributorCustomers(DCJ){
	CST = {}; DCode = GetValue('dist');
	
	$_DistributorCustomers[DCode] = CST;
	ListCustomers(CST);
}
function StoreDPECustomers(DPECJ){
	ID = 'modalProductUpdate'; D = GetValue('dist',ID); P = GetValue('product',ID); E = GetValue('edition',ID);
	if(!$_DPECustomers[D]) $_DPECustomers[D] = [];
	if(!$_DPECustomers[D][P]) $_DPECustomers[D][P] = [];
	if(!$_DPECustomers[D][P][E]) $_DPECustomers[D][P][E] = {};
	$.each(DPECJ,function(e,n){ $_DPECustomers[D][P][E][e] = n+' - ('+e+')'; })
	return ListDPECustomers($_DPECustomers[D][P][E]);
}
function ListCustomers(List){
	if($.isEmptyObject(List)) return $('[name="distributor_customers"]').html(GetOptions(['0'],{'0':'No customers available'}))
	$('[name="distributor_customers"]').html(GetOptions(Object.keys(List),List))
}
function ListDPECustomers(List){
	if($.isEmptyObject(List)) return $('[name="dpe_customers"]').html(GetOptions(['0'],{'0':'No customers available'}))
	$('[name="dpe_customers"]').html(GetOptions(Object.keys(List),List))
}
function StorePEP(PEPJ){
	if(!PEPJ) return DisplayVersion(false); else DisplayVersion(true);
	PRD = PEPJ['product']; EDN = PEPJ['edition']; PKG = PEPJ['package'];
	if(!$_PEP[PRD]) $_PEP[PRD] = [];
	if(!$_PEP[PRD][EDN]) $_PEP[PRD][EDN] = [];
	if(!$_PEP[PRD][EDN][PKG]) $_PEP[PRD][EDN][PKG] = PEPJ;
	PopulateVersionDetails(PEPJ)
}
function PopulateVersionDetails(PVD){
	DisplayVersion(true);
	V = PVD.version_numeric; D = PVD.build_date; if(D) D = ReadableDate(D); C = PVD.change_log; if(C) C = C.replace(/(?:\r\n|\r|\n)/g, '<br>');
	FillModalTable(getModal('modalProductUpdate'),{'Version':V,'Build Date':D,'Change Log':C});
}
function DisplayVersion(S){
	modal = getModal('modalProductUpdate')
	FillModalTable(modal,{'Version Details':'','Version':'','Build Date':'','Change Log':''});
	if(S === false) { FillModalTable(modal,{'Version Details':'<i>No package details available</i>'});/* $('tr.version,tr.build_date,tr.change_log',modal).slideUp(150); $('tr.version_details',modal).slideDown(150).css('display','table-row');*/ }
	//else { $('tr.version,tr.build_date,tr.change_log',modal).slideDown(150).css('display','table-row'); $('tr.version_details',modal).slideUp(150); }
	}
function GetSelectTag(name){
	return $('<select>').attr({'class':'form-control','name':name});
}
function GetInputTag(name){
	return $('<input>').attr({'type':'text','class':'form-control','name':name});
}
function GetOptions(Codes,CNObj){
	return $.map(Codes,function(code){
		return $('<option>').attr({value:code}).text(CNObj[code])
	})
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
