// JavaScript Document

function ProductChanged(PRD){
	if(PRD == "") { EmptyEditions(); EmptyPackages(); return; }
	DistProductEditions(PRD);
	EmptyMailableCusts();
}

function DistProductEditions(PRD){
	FireAPI("api/v1/mit/get/"+PRD+"/editions",CreateEditions)
}

function CreateEditions(D){
	EmptyEditions().html(GetSelOption("Select Edition","")).append(CreateNodes(D,"option",true)).trigger("change")
}

function EmptyEditions(){
	return EmptyOptions("edition");
}

function EmptyPackages(){
	return EmptyOptions("package");
}

function EmptyOptions(N){
	return $("select[name='"+N+"']").empty();
}

function EditionChanged(EDN){
	if(EDN == "") { EmptyPackages(); return; }
	if((PRD = GetPRDValue()) == "") { return EmptyEditions(); }
	GetPackages(PRD, EDN); GetDistributors(PRD, EDN)
	EmptyMailableCusts();
}

function GetPRDValue(){
	return $("select[name='product']").val();
}

function GetEDNValue(){
	return $("select[name='edition']").val();
}

function GetPackages(PRD, EDN){
	FireAPI("api/v1/mit/get/"+PRD+"/"+EDN+"/packages",CreatePackages)
}

function GetDistributors(PRD, EDN){
	FireAPI("api/v1/mit/get/"+PRD+"/"+EDN+"/distributors",function(DList){
		TBD = $('.distributor_details tbody').empty(); TR = $('<tr>'); TD = $('<td>'); TD2 = $('<td class="text-center">'); CB = $('<input>').attr({type:"checkbox",value:'',name:"CST[]",onChange:"CSTChanged(this.value)",'data-code':'','data-name':'','data-email':'','data-presale':'false'});
		$.each(DList,function(C,EN){
			TR.clone().html([TD.clone().text(EN[1]),TD.clone().text(EN[0]),TD2.clone().html(CB.clone().attr({'value':C,'data-name':EN[1],'data-code':C,'data-email':EN[0]}))]).appendTo(TBD);
		})
	})
}

function CreatePackages(D){
	EmptyPackages().html(GetSelOption("Select Package","")).append(CreateNodes(D,"option",true))
}

function PackageChanged(PKG){
	if(PKG == "") return;
	if((EDN = GetEDNValue()) == "") { return EmptyPackages(); }
	if((PRD = GetPRDValue()) == "") { return EmptyEditions(); }
	GetPackagesDetails(PRD, EDN, PKG);
}

function GetPackagesDetails(PRD, EDN, PKG){
	FireAPI("api/v1/mit/get/"+[PRD,EDN,PKG,"PVD"].join("/"),PVDReceived)
}

function PVDReceived(D){
	if(D.length === 0){
		$(".row.page_content").slideUp();
		EmptyPKGDetailsDTBL();
	} else {
		DistributePKGDetails(D);
		$(".row.page_content").slideDown();
	}
}

function DistributePKGDetails(Obj){
	NFObj = new Object({"Product":"product.name","Edition":"edition.name","Package":"package.name","Version":"version_numeric","Updated On":":ReadableDate:updated_at","File Name":":Concate:version_string,version_numeric"});
	EmptyPKGDetailsDTBL();
	DataTBL = getPKGDetailsDTBL();
	$.each(NFObj,function(N,F){
		$("<tr>").html(PKGDetailsHD(N)).append(PKGDetailsVL(F,GEO(Obj))).appendTo(DataTBL)
	});
}

function EmptyPKGDetailsDTBL(){
	getPKGDetailsDTBL().empty();
}

function getPKGDetailsDTBL(){
	return $(".package_details table tbody");
}

function PKGDetailsHD(H){
	return $("<th>").text(H)
}

function PKGDetailsVL(F,Obj1){
	return $("<td>").html(PKGDetailsVL_VL(F,GEO(Obj1))).attr("data-pkgd",F.toLowerCase().replace(/\s/g,"_"))
}

function PKGDetailsVL_FN(F,Obj3){
	FN = F.substr(1).split(":")[0];
	PMS = F.substr(1).split(":").slice(1).join(":");
	PM = $.map(PMS.split(","),function(S){
		return PKGDetailsVL_VL(S,Obj3)
	});
	return eval(FN).apply(this,PM)
}

function PKGDetailsVL_NS(F,Obj4){
	NS = F.split("."); TX = Obj4;
	$.each(NS,function(i,N){
		TX = ($.inArray(typeof(TX),["object","array"])>-1) ? TX[N] : TX;
	});
	return TX;
}

function PKGDetailsVL_VL(F,Obj2){
	if(F.substr(0,1) == ":") return PKGDetailsVL_FN(F,GEO(Obj2));
	if(F.indexOf(".")) return PKGDetailsVL_NS(F,GEO(Obj2));
	return Obj2[F];
}

function GEO(OBJ) {
	return $.extend(null,OBJ,{});
}

function Concate(a,b){
	return a.concat(b);
}

function SearchCustomer(){
	SObj = new Object(); FA = ["product","edition","package","name","email"];
	$.each(FA,function(i,F){ SObj[F] = GetFormValue(F); });
	SObj.presale = $("[name='presale']").prop("checked");
	GetCustomers(SObj);
}

function SearchDistributor(){
	TBD = $('div.distributor_details tbody');
	SNM = $.trim($('[name="dist_name"]').val().toLowerCase());
	SEM = $.trim($('[name="dist_email"]').val().toLowerCase());
	TRs = $('tr',TBD).each(function(i,TR){
		TDT1 = $.trim($('td:first',$(TR)).text().toLowerCase());
		TDT2 = $.trim($('td:eq(1)',$(TR)).text().toLowerCase());
		if((TDT1 == "" || TDT1.indexOf(SNM)>-1) && (TDT2 == "" || TDT2.indexOf(SEM)>-1)) $(TR).css('display','table-row')
		else $(TR).css('display','none')
	})
}

function GetFormValue(n){
	return $("[name='"+n+"']").val();
}

function GetCustomers(SObj){
	$.get("api/v1/mit/get/customer/search",SObj,DistCustomers)
}

function DistCustomers(D){
	EmptyCustomerDetailsTBL()
	CDTBL = getCustomerDetailsTBL();
	if(D.length) {
		$.each(D,function(j,CObj){
			$("<tr>").html(GetSRTDs(CObj)).appendTo(CDTBL);
		})
		AppendSelectedCust(CDTBL)
	} else {
		$("<tr>").html(GetEmptyResult());
		PrependSelectedCust(CDTBL)
	}
}

function getCustomerDetailsTBL(){
	return $(".customer_details table tbody");
}

function EmptyCustomerDetailsTBL(){
	getCustomerDetailsTBL().empty();
}

function GetEmptyResult(){
	return $("<td colspan='4' class='text-center'>").text("Query return no results.")
}

function GetSRTDs(Obj11){
	TDs = [];
	TDs.push($("<td>").text(Obj11.customer.name));
	TDs.push($("<td>").text(Obj11.customer.logins[0].email));
	TDs.push($("<td class='text-center'>").html(PresaleIcon(Obj11)));
	TDs.push($("<td class='text-center'>").html(Sel4Mail(Obj11)));
	return TDs;
}

function GetMCTDs(Obj31){
	TDs = [];
	TDs.push($("<td>").text(Obj31.name));
	TDs.push($("<td>").text(Obj31.email));
	TDs.push($("<td class='text-center'>").html(gly_icon((Obj31.presale == "true") ? "ok" : "remove")));
	TDs.push($("<td class='text-center'>").html(Sel4Mail_AC(Obj31)));
	return TDs;
}

function PresaleIcon(Obj12){
	D = PresaleStatus(Obj12)
	return (D) ? gly_icon("ok") : gly_icon("remove")
}

function PresaleStatus(Obj14){
	D = (Obj14.pex)?(Obj14.pex):((Obj14.ped)?(Obj14.ped):false);
	return (D && DateDiff(D)<1)
}

function Sel4Mail(Obj13){
	return $("<input>").attr({type:"checkbox",value:Obj13.customer.code,name:"CST[]",onChange:"CSTChanged(this.value)","data-code":Obj13.customer.code,"data-name":Obj13.customer.name,"data-email":Obj13.customer.logins[0].email,"data-presale":PresaleStatus(Obj13)})
}

function Sel4Mail_AC(Obj32){
	return $("<input>").attr({type:"checkbox",value:Obj32.code,name:"CST[]",onChange:"CSTChanged(this.value)","data-code":Obj32.code,"data-name":Obj32.name,"data-email":Obj32.email,"data-presale":Obj32.presale}).prop("checked",true);
}

var MailableCustomers = new Object();
function CSTChanged(V){
	C = GetCSTCB(V);
	if(C.prop("checked")) return AddMailableCust(C)
	return DelMailableCust(C)
}

function GetCSTCB(V){
	return $("input[data-code='"+V+"']");
}

function AddMailableCust(C){
	DelMailableCust(C)
	Ary = ["code","name","email","presale"];
	MObj = new Object();
	$.each(Ary,function(m,D){
		MObj[D] = C.attr("data-"+D);
	})
	MailableCustomers[C.attr("value")] = MObj;
	SubmitButtonStatus();
}

function DelMailableCust(C){
	delete MailableCustomers[C.attr("value")];
	SubmitButtonStatus();
}

function PrependSelectedCust(TBL){
	AddAlreadySelCus(TBL,"prependTo")
}

function AppendSelectedCust(TBL){
	AddAlreadySelCus(TBL,"appendTo")
}

function AddAlreadySelCus(TBL2,Method){
	$.each(MailableCustomers,function(Code,Obj21){
		if(isSelCusExist(Code)) return CheckCust(Code);
		$("<tr>").html(GetMCTDs(Obj21))[Method](TBL2);
	})
}

function isSelCusExist(Code){
	return $("[data-code='"+Code+"']").length;
}

function CheckCust(Code2){
	$("[data-code='"+Code2+"']").prop("checked",true);
}

function SendUpdateMail(){
	Data = new Object({customers:Object.keys(MailableCustomers)});
	$.each(["product","edition","package"],function(n,L){
		Data[L] = GetFormValue(L);
	});
	Data.version = GetVersion();
	Data.distributors = $('.distributor_details [name="CST[]"]:checked').length;
	PostUPCustomers(Data)
}

function PostUPCustomers(Data){
	SUMButton(0)
	$.post("api/v1/mit/action/sum",Data,function(K){
		alert("Mail is being sent soon.");
		EmptyEditions(); EmptyCustomerDetailsTBL(); EmptyPKGDetailsDTBL();
		$(".row.page_content").slideUp(); EmptyMailableCusts(); EmptyPackages();
		$("select[name='product'] option:first").prop("selected",true);
	})
}

function InvertCustomerSelection(){
	$("[name='CST[]']").each(function(x,B){
		$(B).prop("checked",!$(B).prop("checked")).trigger("change");
	})
}

function InvertDistributorSelection(){
	$(".distributor_details [name='CST[]']:visible").each(function(x,B){
		$(B).prop("checked",!$(B).prop("checked")).trigger("change");
	})
}

function EmptyMailableCusts(){
	MailableCustomers = new Object({});
	SubmitButtonStatus()
}

function GetVersion(){
	return $("td[data-pkgd='version_numeric']").text();
}

function SubmitButtonStatus(){
	SUMButton(Object.keys(MailableCustomers).length)
}

function SUMButton(S){
	$(".SUM_Button")[(S)?"removeClass":"addClass"]("disabled");
}
