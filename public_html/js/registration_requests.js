// JavaScript Document

function registration_requests_arrange_data(RRJ){
	if($.isEmptyObject(RRJ['data']))
		return NoPanelRecordExists($('.panel'+_Panels['registration_requests']),4);
	return RRQ_ArrangeData(RRJ);
}

function NoRegistrationRequests(){
	panel = $(_Panels['registration_requests']);
	panel.remove();
	return [];
}

var _RRQ_GetLicenceURL, _RRQ_DoRegisterURL;

function RRQ_ArrangeData(RRJ){
	TR = []; _RRQ_GetLicenceURL = RRJ['get_licence_url']; _RRQ_DoRegisterURL = RRJ['do_register_url'];
	$.each(RRJ['data'],function(index,Obj){
		TD = []; TD.push(index+1); TD.push(RRQ_TD2(Obj));
		TD.push(RRQ_TD3(Obj)); TD.push(RRQ_Actions(Obj));
		TR.push(TD);
	})
	return TR;
}

function RRQ_TD2(Obj){
	Dist = RRQ_Distributor(Obj.customer)
	return [_PartnerDetailAnchor(Obj.customer.name,'customer',Obj.customer.code), $('<br>'), $('<small>').html(['(',_PartnerDetailAnchor(Dist[1],'distributor',Dist[0]),')'])]
}

function RRQ_TD3(Obj){
	return [Obj.product.name, Obj.edition.name, 'Edition'].join(' ');
}

function RRQ_Distributor(Cus){
	if(ArrayHasKeyValue(Cus.parent_details[0].roles,'name','distributor')) return [Cus.parent_details[0].code,Cus.parent_details[0].name];
	return RRQ_Distributor(Cus.parent_details[0]);
}

function RRQ_Actions(Obj){
	D = btn('Download Licence file',_RRQ_GetLicenceURL.replace('--customer--',Obj.customer.code).replace('--seqno--',Obj.seqno),'download-alt').css('padding','0px 10px 0px 0px');
	R = btn('Enter Registration Key',_RRQ_DoRegisterURL.replace('--customer--',Obj.customer.code).replace('--seqno--',Obj.seqno),'saved').css('padding','0px');
	return [D,R];
}
