// JavaScript Document

function unregistered_recent_customers_arrange_data(Data){
	
	if($.isEmptyObject(Data)) return NoPanelRecordExists($('.panel'+_Panels['unregistered_recent_customers']),4);
	return RCU_ArrangeData(Data);
	
}

function RCU_ArrangeData(J){
	no = 0;
	return $.map(J,function(Obj,i){
		COL = [++no]; COL.push(RCU_TD1(Obj.customer));
		COL.push(AppNameFromRegData(Obj));
		COL.push(RCU_TD3(Obj));
		return [COL];
	})
}

function RCU_TD1(Obj){
	Dist = DistributorOfCustomer(Obj.parent_details[0])
	return [_PartnerDetailAnchor(Obj.name,'customer',Obj.code),$('<br>'),$('<small>').html(['(',_PartnerDetailAnchor(Dist[1],'distributor',Dist[0]),')'])];
}

function DistributorOfCustomer(Parent){
	if(ArrayHasKeyValue(Parent.roles,'name','distributor')) return [Parent.code,Parent.name];
	return DistributorOfCustomer(Parent.parent_details[0]);
}

function AppNameFromRegData(Reg){
	return [Reg.product.name,Reg.edition.name,'Edition'].join(' ');
}

function RCU_TD3(Reg){
	time = Reg.created_at;
	D = new Date(time); date = time.split(' ')[0]; diff = DateDiff(date);
	return [date,$('<br>'),$('<small>').text('('+((diff)?diff+' day'+((diff > 1)?'s':'')+' ago':'today')+')')];
}
