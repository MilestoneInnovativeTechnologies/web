// JavaScript Document

function demo_expiring_customers_arrange_data(Data){
	
	if($.isEmptyObject(Data)) return NoPanelRecordExists($('.panel'+_Panels['demo_expiring_customers']),4);
	return DEC_ArrangeData(Data);
	
}

function DEC_ArrangeData(J){
	Data = ObjectSortByKeyValue(J,'sort_order');
	no = 0;
	return $.map(Data,function(Obj,i){
		COL = [++no]; COL.push(DEC_TD1(Obj.customer));
		COL.push(DEC_TD2(Obj));
		COL.push(DEC_TD3(Obj));
		return [COL];
	})
}

function DEC_TD1(Obj){
	Dist = DEC_Distributor(Obj.parent_details[0])
	return [_PartnerDetailAnchor(Obj.name,'customer',Obj.code),$('<br>'),$('<small>').html(['(',_PartnerDetailAnchor(Dist[1],'distributor',Dist[0]),')'])];
}

function DEC_Distributor(Parent){
	if(ArrayHasKeyValue(Parent.roles,'name','distributor')) return [Parent.code,Parent.name];
	return DEC_Distributor(Parent.parent_details[0]);
}

function DEC_TD2(Reg){
	return [Reg.product.name,Reg.edition.name,'Edition'].join(' ');
}

function DEC_TD3(Reg){
	time = Reg.expire_on;
	D = new Date(time); date = time.split(' ')[0]; diff = parseInt(DateDiff(date));
	mult = (diff > 1 || diff < -1) ? 's' : ''; post_fix = (diff>0) ? ' day'+mult+' ago' : ' day'+mult+' to go';
	return [date,$('<br>'),$('<small>').text('('+((diff)?(Math.abs(diff)+post_fix):'today')+')')];
}
