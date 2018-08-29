$(function(){
	if($(".content.customer_lists").length){
		GetCustomers(1,40);
	} else if($(".content.customer_edit").length){
		LoadCountryStates()
	}
})

function LoadCountryStates(){
	cntry = $("[name='country']").val(); if($.trim(cntry) == "") return;
	FireAPI("api/"+cntry+"/states",function(States){
		State = $("[name='state']").empty().html(CreateNodes(States,"option","1",{"value":0}))
		if(State.attr("data-pre-value")) State.val(State.attr("data-pre-value")).removeAttr("data-pre-value");
		LoadStateCities();
		UpdatePhoneCode($("[name='country']"))
	});
}

function LoadStateCities(){
	state = $("[name='state']").val(); if($.trim(state) == "") return;
	FireAPI("api/"+state+"/cities",function(Cities){
		City = $("[name='city']").empty().html(CreateNodes(Cities,"option","1",{"value":0}))
		if(City.attr("data-pre-value")) City.val(City.attr("data-pre-value")).removeAttr("data-pre-value");
	});
}

function UpdatePhoneCode(C){
	code = $("option[value='"+(C.val())+"']",C).attr("data-phonecode")
	$("span.input-group-addon.phonecode").text(code);
	$("input[name='phonecode']").val(code);
}

function IndustryChanged(){
	if($("[name='industry']").val() != "-1") return;
	$("[name='industry']").slideUp();
	$(".new_industry_div").slideDown();
}
function NoNewIndustry(){
	$(".new_industry_div").slideUp();
	$("[name='industry']").slideDown().find("option:first").prop("selected",true);
}

















function GetCustomers(page,items){
	FireAPI("api/v1/customer/list/"+page+"/"+items,DistrubuteCustomers);
}

function DistrubuteCustomers(R){
	F = ["AI","customer.name","product.name","edition.name","added_on","registered_on","presale","action"];
	if(_DISTRIBUTOR == 1) F.splice(2,0,"dealer");
	DistributeTableData(".content.customer_lists",R,F,"CCL");
}

function CCL_dealer(F,OB,TD,TR,AI){
	N = OB.parent[0].name; conf = false;
	$.each(OB.parent[0].roles,function(x,RO){
		if(RO.name == "dealer") conf = true;
	})
	if(conf) return N;
	TD.text("-").addClass("text-center");
}

function CCL_added_on(F,OB,TD,TR,AI){
	return ReadableDate(OB[F]);
}

function CCL_registered_on(F,OB,TD,TR,AI){
	return (OB[F]) ? RegData(OB) : gly_icon('remove');
}

function RegData(Obj){
	return [ReadableDate(Obj.registered_on),
					$('<br>'),
					$('<small>').text('('+Obj.serialno+')'),
					$('<br>'),
					$('<small>').text('('+Obj.key+')'),
				 ]
}

function CCL_presale(F,OB,TD,TR,AI){
	D = (OB['presale_extended_to']) || (OB['presale_enddate']) || null; if(!D) return "NULL";
	DF = DateDiff(D);
	if(DF > 0) return "INACTIVE - " + ReadableDate(D);
	if(DF < 0) return "ACTIVE - " + ReadableDate(D);
	else return "Expires today";
}

function CCL_action(F,OB,TD,TR,AI){
	CmnAry = [
	 	btn("Edit deails of "+OB.customer.name,_URL.edit.replace("--CODE--",OB.customer.code),"edit"),
	 	btn("Change presale date of "+OB.customer.name,_URL.presale.replace("--CODE--",OB.customer.code),"random"),
		btn("View details of "+OB.customer.name,_URL.show.replace("--CODE--",OB.customer.code),"list-alt"),
		btn("Send login reset link for "+OB.customer.name,"javascript:LoginReset('"+OB.customer.code+"','"+OB.customer.name+"','"+OB.customer.logins[0].email+"')","log-in"),
		btn("Change Distributor",_URL.change_distributor.replace("--CUSTOMER--",OB.customer.code),"home"),
	 ];
	if(!OB.registered_on) CmnAry.push(btn("Change Editions of "+OB.customer.name,_URL.change_edition.replace("--CUSTOMER--",OB.customer.code).replace("--SEQNO--",OB.seqno),"transfer"));
	if(!OB.registered_on) CmnAry.push(btn("Register "+OB.customer.name,_URL.register.replace("--CUSTOMER--",OB.customer.code).replace("--SEQNO--",OB.seqno),"registration-mark"));
	return CmnAry;
}

function LoginReset(C,N,E){
	modal = $("#modalLoginReset").attr('data-code',C).modal('show');
	$('.modal_customer_code',modal).text(C); $('.modal_customer_name',modal).text(N); $('.modal_customer_email',modal).text(E);
}

function SendLRL(){
	modal = $("#modalLoginReset");
	FireAPI('api/v1/'+modal.attr('data-code')+'/resetlogin',ConfirmRLL);
	modal.modal('hide');
}

function ConfirmRLL(R){
	alert('Login Reset link have successfully mailed to '+R[1]+', at, '+R[2]);
}


