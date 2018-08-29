// JavaScript Document

function recent_registrations_arrange_data(Data){
	
	if($.isEmptyObject(Data)) return NoPanelRecordExists($('.panel'+_Panels['recent_registrations']),5);
	return RRC_ArrangeData(Data);
	
}

function RRC_ArrangeData(J){
	no = 0;
	return $.map(J,function(Obj,i){
		COL = [++no]; COL.push(RRC_TD1(Obj.customer));
		COL.push(AppNameFromRegData(Obj));
		COL.push(RRC_TD3(Obj)); COL.push(RRC_TD4(Obj));
		return [COL];
	})
}

function RRC_TD1(Obj){
	Dist = DistributorOfCustomer(Obj.parent_details[0])
	return [_PartnerDetailAnchor(Obj.name,'customer',Obj.code),$('<br>'),$('<small>').html(['(',_PartnerDetailAnchor(Dist[1],'distributor',Dist[0]),')'])];
}

function RRC_TD3(Reg){
	return Reg.registered_on;
	D = new Date(time); date = time.split(' ')[0]; diff = parseInt(DateDiff(date));
	mult = (diff > 1 || diff < -1) ? 's' : ''; post_fix = (diff>0) ? ' day'+mult+' ago' : ' day'+mult+' to go';
	return [date,$('<br>'),$('<small>').text('('+((diff)?(Math.abs(diff)+post_fix):'today')+')')];
}

function RRC_TD4(Reg){
	time = Reg.created_at;
	D = new Date(time); date = time.split(' ')[0]; diff = parseInt(DateDiff(date));
	return date;
	mult = (diff > 1 || diff < -1) ? 's' : ''; post_fix = (diff>0) ? ' day'+mult+' ago' : ' day'+mult+' to go';
	return [date,$('<br>'),$('<small>').text('('+((diff)?(Math.abs(diff)+post_fix):'today')+')')];
}

function DistributorOfCustomer(Parent){
	if(ArrayHasKeyValue(Parent.roles,'name','distributor')) return [Parent.code,Parent.name];
	return DistributorOfCustomer(Parent.parent_details[0]);
}

function AppNameFromRegData(Reg){
	return [Reg.product.name,Reg.edition.name,'Edition'].join(' ');
}
