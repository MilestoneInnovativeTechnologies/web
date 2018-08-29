$(function(){
	if($('.content.dashboard').length){
		
	} else if($('.content.change_address').length){
		if($('[name="country"]').length) LoadCountries();
	}
});

function LoadCountries(){
	FireAPI('api/countries',function(R){
		$("[name='country']").html(CreateNodes(R,"option","1",{"value":"0","data-currency":"2","data-phonecode":"3"}));
		SetPreVal('country');
	});
}

function DisplayDetails(Resp){
	R = Resp.User;
	$("div.mydetails p.address").text([R.address1,R.address2,R.city,R.state,R.country].join(", "));
	$("div.mydetails p.contacts").html(["+"+R.phonecode+" "+R.phone,R.email].join("<br/>"));
	P = Resp.Parent;
	$("div.myparent p.name").html($("<strong>").text(P.name));
	$("div.myparent p.address").text([P.address1,P.address2,P.city,P.state,P.country].join(", "));
	$("div.myparent p.contacts").html(["+"+P.phonecode+" "+P.phone,P.email].join("<br/>"));
}

function StateChanged(){
	if($("[name='state']").val() == "") return;
	FireAPI("api/"+$("[name='state']").val()+"/cities",function(R){
		$("[name='city']").html(CreateNodes(R,"option","1",{"value":"0"}))
		SetPreVal('city');
	});
}

function CountryChanged(){
	if($("[name='country']").val() == "") return;
	FireAPI("api/"+$("[name='country']").val()+"/states",function(R){
		$("[name='state']").html(CreateNodes(R,"option","1",{"value":"0"}));
		SetPreVal('state');
	});
}

function RegInfoButton(S){
	return btn("View registration info","javascript:GetRegInfos('"+(S)+"')","remove").text("Registration Info").addClass("btn-info btn-xs")
}

function GetRegInfos(S){
	FireAPI("api/v1/customer/reginfo/"+S,function(D){
		$("#package_modal").find("span.title").text("Registration Details")
		.end()
		.find(".modal-body").html($("<p>").html($("<strong>").text("Registered on")).append("<br>").append(ReadableDate(D.registered_on)))
			.append($("<p>").html($("<strong>").text("Serial No")).append("<br>").append(D.serialno))
			.append($("<p>").html($("<strong>").text("Key")).append("<br>").append(D.key))
		.end()
		.modal("show")
	});
}

var _Version = {};
function DisplayMyProducts(JObj){
	I = 0;
	$.each(JObj, function(PID, PObj){
		NewTR = $("<tr>");
		$("<td>").text(++I).appendTo(NewTR).attr({"rowspan":PObj.length,"style":"vertical-align:middle"});
		$("<td>").text(PObj[0].product.name).appendTo(NewTR).attr({"rowspan":PObj.length,"style":"vertical-align:middle"});
		$.each(PObj, function(J, Obj){
			if(J>0) NewTR = $("<tr>");
			$("<td>").text(Obj.edition.name).appendTo(NewTR);
			$("<td>").text(ReadableDate(Obj.created_at)).appendTo(NewTR);
			$("<td>").html(gly_icon((Obj.registered_on) ? ("ok") : ((Obj.requisition) ? "exclamation-sign" : "remove"))).attr("title",( (Obj.registered_on) ? ("Registered") : ((Obj.requisition) ? "Registration request submitted" : "Not registered") )).appendTo(NewTR);
			RB = $("<a>").attr({class:"btn btn-xs btn-info",href:"customer/"+(Obj.seqno)+"/register"}).text((Obj.requisition)?"Do register request again":"Register");
			$("<td>").html((Obj.registered_on)?(RegInfoButton(Obj.seqno)):RB).appendTo(NewTR);
			NewTR.appendTo("table.myproducts tbody");
			if(!_Version[PID]) _Version[PID] = {};
			_Version[PID][Obj.edition.code] = Obj.version;
		})
	})
	PrepareUpdates(JObj);
}

function PrepareUpdates(JObj){
	$_PRD = {};
	$.each(JObj, function(PID, PObj){
		$_PRD[PID] = [];
		$.each(PObj, function(J, Obj){
			$_PRD[PID].push(Obj.edition.code)
		})
	})
	FireAPI('api/v1/customer/packages',DisplayUpdates,{P:$_PRD})
}

var _Updates = {};
function DisplayUpdates(JU){
	J = 0; _Updates = JU;
	$.each(JU, function(PID, Obj){
		NTR = $('<tr>'); EDNS = Object.keys(Obj[1]).length;
		$("<td>").text(++J).appendTo(NTR).attr({"rowspan":EDNS,"style":"vertical-align:middle"});
		$("<td>").text(Obj[0]).appendTo(NTR).attr({"rowspan":EDNS,"style":"vertical-align:middle"});
		K = 0;
		$.each(Obj[1],function(EID, EAry){
			if(K++ > 0) NTR = $('<tr>');
			$("<td>").text(EAry[0]).appendTo(NTR);
			$("<td>").text(_Version[PID][EID]).appendTo(NTR);
			$("<td>").text(EAry[1]).appendTo(NTR);
			$("<td>").html(GetUpdatesAction(PID, EID, EAry, Obj[0])).appendTo(NTR);
			NTR.appendTo("table.myupdates tbody");
		})
	})
}

function GetUpdatesAction(P, E, A, N){
	return [
		btn('Download Latest update of '+N,A[5],'save').attr({'target':'_blank'}),
		btn('View Change Log for this update.','javascript:ViewChangeLog("'+P+'","'+E+'")','transfer')
	]
}

function ViewChangeLog(P, E){
	ChangeLogs = (_Updates[P][1][E][2])?(_Updates[P][1][E][2]):"";
	$("#package_modal").find("span.title").text("Change log details")
		.end()
		.find(".modal-body").html($("<p>").html($("<strong>").text("Change Log of "+_Updates[P][0]+" to version "+_Updates[P][1][E][1])))
			.append($("<ol>").html(ChangeLogs.split("\n").map(function(line){
				return $('<li>').text(line);
			})))
		.end()
		.modal("show")
}

function SetPreVal(Name){
	Obj = $('[name="'+Name+'"]'); PV = Obj.attr('data-pre-value');
	if(PV == "") return;
	Obj.val(PV).trigger('change'); Obj.removeAttr('data-pre-value');
}
