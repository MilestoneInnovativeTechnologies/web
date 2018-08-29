// JavaScript Document

	function InvertSelection(cls){
		tbl = $('table.'+cls);
		$('input[type="checkbox"]',tbl).each(function(i,cbx){
			$(cbx).prop('checked',!$(cbx).prop('checked'))
		})
	}

function SelectUnassigned(cls){
	tbl = $('table.'+cls);
	$('input[type="checkbox"]',tbl).each(function(i,cbx){
		dst = $(cbx).attr('data-st');
		if(!dst) $(cbx).prop('checked',true);
	})
	
}

function FetchDistributorCustomer(){
	Dist = $('[name="distributor"]').val();
	FireAPI('api/v1/tst/get/dc',ParseData,{D:Dist})
}

$_ASSIGNED = {};
function ParseData(CJ){
	$_ASSIGNED = CJ.assigned; Customers = CJ.customers;
	DistributeCustomer(Customers);
}

function DistributeCustomer(CJ){
	if(CJ.length === 0 || Object.keys(CJ).length === 0) return NoCustomers(); else HaveCustomers();
	tbd = $('.panel.customers tbody').empty();
	$.each(CJ,function(Code, CAry){
		$.each(CAry, function(i, CObj){
			TR = $('<tr>').attr({'data-customer':Code,'data-product':CObj.product.code,'data-edition':CObj.edition.code}).appendTo(tbd);
			CB = CHKBX(CObj); NTD().html(CB).addClass('text-center').appendTo(TR);
			NTD().text(CObj.customer.name).appendTo(TR);
			NTD().text((CB.attr('data-st'))?(CB.attr('data-st')):'-').appendTo(TR);
			NTD().text(CObj.product.name).appendTo(TR);
			NTD().text(CObj.edition.name).appendTo(TR);
			NTD().html(CustomerAddress(CObj.customer.details)).appendTo(TR);
			NTD().html(CustomerContacts(CObj.customer.details,CObj.customer.logins)).appendTo(TR);
		})
	});
}

function NoCustomers(){
	$('.panel.customers').slideUp(175);
	$('.jumbotron').slideDown(175);
	$('.asc').addClass('disabled')
	return;
}

function HaveCustomers(){
	$('.panel.customers').slideDown(175);
	$('.jumbotron').slideUp(175);
	$('.asc').removeClass('disabled')
	return;
}

function NTD(){
	return $('<td>');
}

function CHKBX(O){
	C = O.customer.code; P = O.product.code; E = O.edition.code; V = [C,P,E].join('-');
	INP = $('<input>').attr({ type:'checkbox',name:'DST[]',value:V });
	return INPCHECK(INP);
}

function INPCHECK(INP){
	AC = $_ASSIGNED[C]; if(!AC) return INP;
	CPE = INP.attr('value').split('-'); PE = {};
	$.each(AC,function(j,SO){
		PC = SO.product.code; if(!PE[PC]) PE[PC] = {};
		EC = SO.edition.code; if(!PE[PC][EC]) PE[PC][EC] = [SO.supportteam,SO.team.name];
	})
	if(PE[CPE[1]] && PE[CPE[1]][CPE[2]]){
		if(PE[CPE[1]][CPE[2]][0] == $_CODE) { AddOld(CPE); return INP.prop('checked',true).attr('data-st',PE[CPE[1]][CPE[2]][1]); }
		else return INP.attr('data-st',PE[CPE[1]][CPE[2]][1])
	}
		
	return INP;
}

function CustomerAddress(DO){
	L1 = [DO.address1,DO.address2].join(', ');
	L2 = (DO.city)?([DO.city.name,DO.city.state.name].join(', ')):'';
	L3 = (DO.city)?(DO.city.state.country.name):'';
	return [L1,L2,L3].join('<br>');
}

function CustomerContacts(DO,LO){
	L1 = ['+',DO.phonecode,'-',DO.phone].join('');
	L2 = $.map(LO,function(O){ return O.email; }).join('<br>');
	return [L1,L2].join('<br>');
}

function AddOld(CPE){
	$('<input type="hidden" name="OD[]" value="'+CPE.join('-')+'">').appendTo($('.customers.panel tbody'));
}

function UpdateDistributors(){
	FireAPI('api/v1/tst/action/stuc/'+$_CODE,function(){
		alert('Updated Succesfully..');
		location.search = '?_D='+$('[name="distributor"]').val();
	},$('input[name="OD[]"],input[name="DST[]"]').serializeArray());
}
