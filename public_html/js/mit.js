$(function(){
	if($(".content.company_dashboard").length){
		ArrangeCustomers(_Customers); ArrangeDealers(_Dealers); ArrangeDistributors(_Distributors); ArrangeCompanies(_Companies); ArrangeProducts(_PE);
		LoadCustomerProducts(); PageRecordView(); Distribute_CWP(5); Distribute_IWC(5); GetList("distributor");
	} else if($(".content.distributor_panel").length || $(".content.dealer_panel").length){
		LoadPartnerProducts();
		if($(".content.distributor_panel").length) { LoadTransactions(); }
	} else {
		
	}
	
	
});

var $_Partners = {}, $_Country = {}, $_Industry = {}, $_Parent = {}, $_Role = {"customer":[],"dealer":[],"distributor":[],"company":[]}, $_Childs = {}, $_Presale = {}, $_Products = {}, $_Editions = {}, $_PE = {}, $_Registration = {}, $_CPE = {}, $_RCPE = {}, $_ACPE = {};

function ArrangeCustomers(R){
	$.each(R,function(i,CAry){
		CC = ArrangePartnersBasic(CAry,CAry[7]);
		if(CAry[4]){ PartnerRelations(CC,CAry[4]); ArrangePartnersBasic([CAry[4],CAry[5]],CAry[6]);  }
		if(CAry[8]){ if(!$_Industry[CAry[8]]) $_Industry[CAry[8]] = {"name":CAry[9],"customers":[]}; if($.inArray(CC,$_Industry[CAry[8]].customers)<0) $_Industry[CAry[8]].customers.push(CC); }
		if(CAry[10]){ if(!$_Presale[CC]) $_Presale[CC] = [CAry[11],CAry[12]]; }
	});
}

function ArrangeDealers(R){
	$.each(R,function(i,DAry){
		DC = ArrangePartnersBasic(DAry,DAry[7]);
		if(DAry[4]){ PartnerRelations(DC,DAry[4]); ArrangePartnersBasic([DAry[4],DAry[5]],DAry[6]); }
	});
}

function ArrangeDistributors(R){
	$.each(R,function(i,DAry){
		DC = ArrangePartnersBasic(DAry,DAry[7]);
		if(DAry[4]){ PartnerRelations(DC,DAry[4]); }
	});
}

function ArrangeCompanies(R){
	$.each(R,function(i,DAry){
		DC = ArrangePartnersBasic(DAry,DAry[7]);
		if(DAry[4]){ PartnerRelations(DC,DAry[4]); }
	});
}

function ArrangeProducts(PObj){
	TBdy = $(".panel.company_products tbody").empty(); RC = 0;
	$.each(PObj,function(PC,PO){
		if(!$_Products[PC]) $_Products[PC] = PO.name;
		EDTC = 0; TED = Object.keys(PO.editions).length;
		TR = $("<tr>").attr({"data-product":PC}).appendTo(TBdy).html($("<td>").attr({"rowspan":TED,"class":"text-center"}).text(++RC)).append($("<td>").attr("rowspan",TED).text(PO.name));
		$.each(PO.editions,function(EC,EO){
			if(!$_Editions[EC]) $_Editions[EC] = EO.name;
			if(EDTC++ > 0) TR = $("<tr>").attr({"data-product":PC}).appendTo(TBdy);
			TR.attr({"data-edition":EC});
			TR.append($("<td>").text(EO.name)).append($("<td class='text-center tr'>").text("0")).append($("<td class='text-center tn'>").text("0")).append($("<td class='text-center te'>").text("0"))
			if(EDTC == 1) $("<td>").attr({"rowspan":TED,"class":"text-center tp","style":"vertical-align:middle"}).text("0").appendTo(TR)
		})
	})
}

function LoadCustomerProducts(){
	FireAPI("api/v1/mit/customer/products"+paginate(),ArrangeCustomerProducts);
}

function LoadPartnerProducts(){
	FireAPI("api/v1/mit/partner/"+$_PartnerCode+"/products"+paginate(),ArrangePartnerProducts);
}

function LoadDistributorDealers(){
	FireAPI("api/v1/mit/distributor/"+$_PartnerCode+"/dealers"+paginate(),ArrangeDistributorDealers);
}

function ArrangeCustomerProducts(R){
	$.each(R,function(i,Ary){
		if(!$_Products[Ary[2]]) $_Products[Ary[2]] = Ary[3]; if(!$_Editions[Ary[4]]) $_Editions[Ary[4]] = Ary[5]; ArrangePartnersBasic([Ary[0],Ary[1]],'customer');
		key = [Ary[2],Ary[4],(Ary[7])?"R":"N"].join(":");
		if(!$_Registration[key]) $_Registration[key] = [];
		$_Registration[key].push(Ary[0]);
		CPE(Ary[0],Ary[2],Ary[4],Ary[6],Ary[7])
	});
	Distribute_CP();
	DistributeTableData("rac",$_ACPE,5);
	DistributeTableData("rrc",$_RCPE,5);
}

function ArrangePartnerProducts(R){
	$_PartnerData = R;
	Distribute_PD(30);
	if($(".content.distributor_panel").length) LoadDistributorDealers();
}

function ArrangeDistributorDealers(R){
	$_DDData = R;
	Distribute_DDC(30)
}

function CPE(C,P,E,A,R){
	if(!$_CPE[C]) $_CPE[C] = {}; if(!$_CPE[C][P]) $_CPE[C][P] = {}; if(!$_CPE[C][P][E]) $_CPE[C][P][E] = [];
	AD = DateDiff(A); RD = DateDiff(R); I = ($_CPE[C][P][E].push([A,A.split(" ")[0],AD,R,RD]))-1;
	key = [C,P,E,I].join(":");
	if(!$_ACPE[AD]) $_ACPE[AD] = []; $_ACPE[AD].push(key);
	if(R){ if(!$_RCPE[RD]) $_RCPE[RD] = []; $_RCPE[RD].push(key); }
}

function Distribute_PD(R){
	TBD = $(".panel.customers tbody").empty();
	$.each($_PartnerData,function(i,CA){
		if(R <= i) return false;
		CountInc(CA[2],CA[4],(CA[7])?'reg':'unreg',1); CountInc(CA[2],CA[4],'total',1);
		$("<tr>").html($("<td>").text(i+1)).append($("<td>").text(CA[1])).append($("<td class='text-center'>").text("-")).append($("<td>").text(CA[3])).append($("<td>").text(CA[5])).append($("<td>").text(ReadableDate(CA[6]))).append($("<td>").html((CA[7])?ReadableDate(CA[7]):(icon('remove'))).attr("class",(CA[7])?'':'text-center')).appendTo(TBD);
	});
	$("td:eq(2)",$(".content.dealer_panel .panel.customers tbody tr")).remove();
}

function Distribute_DDC(R){
	TBD = $(".panel.customers tbody"); CC = $("tr",TBD).length;
	TBD2 = $(".panel.dealers tbody");
	$.each($_DDData,function(i,CA){
		if(R <= i+CC) return false;
		CountInc(CA[2],CA[4],(CA[7])?'reg':'unreg',1); CountInc(CA[2],CA[4],'total',1);
		$("<tr>").html($("<td>").text(i+CC+1)).append($("<td>").text(CA[1])).append($("<td>").text(CA[9])).append($("<td>").text(CA[3])).append($("<td>").text(CA[5])).append($("<td>").text(ReadableDate(CA[6]))).append($("<td>").html((CA[7])?ReadableDate(CA[7]):(icon('remove'))).attr("class",(CA[7])?'':'text-center')).appendTo(TBD);
		DTR = $("tr[data-dealer='"+CA[8]+"']",TBD2);
		if(DTR.length == 0) DTR = $("<tr data-dealer='"+CA[8]+"'>").html($("<td>").text(i+1)).append($("<td>").text(CA[9])).append($("<td class='dc reg text-center'>").text(0)).append($("<td class='dc unreg text-center'>").text(0)).append($("<td class='dc total text-center'>").text(0)).appendTo(TBD2);
		AddCount($("td."+((CA[7])?'reg':'unreg'),DTR),1); AddCount($("td.total",DTR),1);
	});
}

function RCDS(C){
	$('[data-product][data-edition]').text("0");
	$('tr[data-dealer] .dc').text("0");
	Distribute_PD(C);
	Distribute_DDC(C);
}

function Distribute_CP(){
	$.each($_Registration,function(key,CA){
		PET = key.split(":");
		IncCount(PET[0],PET[1],PET[2],CA.length);
	})
}

function DistributeTableData(Tbl,Data,R){
	Data = $.extend({},Data); ORD = Object.keys(Data);//.sort();
	TBD = $(".panel.company_"+Tbl+" tbody").empty(); RC = 0;
	$.each(ORD,function(i,D){
		if(R <= RC) return false;
		$.each(Data[D],function(j,TT){
			if(++RC > R) return false;
			T = TT.split(":");
			TR = $("<tr>").appendTo(TBD).html($("<td>").text(RC)).append($("<td>").html(LINK("C",$_Partners[T[0]][0],T[0]))).append($("<td>").html(ParentDD(T[0])));
			TR.append($("<td>").text($_Products[T[1]])).append($("<td>").text($_Editions[T[2]]));
			CPE = $_CPE[T[0]][T[1]][T[2]][T[3]];
			TR.append($("<td>").text(CPE[1]+"("+CPE[2]+")"));
			TR.append($("<td>").html(((CPE[3]) ? (CPE[3]+"("+CPE[4]+")") : (icon("remove")))));
		})
	})
}

function CountInc(P,E,T,C){
	TD = $("td."+T+"[data-product='"+P+"'][data-edition='"+E+"']");
	AddCount(TD,C);
}

function IncCount(P,E,T,C){
	TR = $("tr[data-product='"+P+"'][data-edition='"+E+"']");
	TD = $("td.t"+T.toLowerCase(),TR); AddCount(TD,C);
	AddCount($("td.te",TR),C); AddCount($("tr[data-product='"+P+"'] td.tp"),C);
}

function AddCount(TD,C){
	O = parseInt(TD.text());
	N = O+C; TD.text(N);
}

function ParentDD(PC){
	if(!$_Parent[PC]) return "";
	P = $_Parent[PC];
	if($_Parent[P]){
		//if($_Parent[P] == "COMPANY" || $_Parent[P] == "thahir") return LINK("S",$_Partners[P][0],P);
		if($.inArray($_Parent[P],$_Role['company']) > -1) return LINK("S",$_Partners[P][0],P);
		return LINK("S",$_Partners[$_Parent[P]][0],$_Parent[P])[0].outerHTML+"/"+LINK("D",$_Partners[P][0],P)[0].outerHTML;
	}
}

function Distribute_CWP(R){
	Count = {};
	CTs = $.each($_Country,function(C,O){	T = O.customer.length + O.dealer.length + O.distributor.length; if(!Count[T]) Count[T] = []; Count[T].push(C) });
	ORD = Object.keys(Count).reverse(); TBD2 = $(".panel.country_partners tbody").empty(); RC = 0;
	for(x in ORD){
		$.each(Count[ORD[x]],function(i,C){
			if(++RC > R) return false;
			TR = $("<tr>").appendTo(TBD2);
			TR.html($("<td class='text-center'>").text(RC)).append($("<td>").text(C));
			$.each(["distributor","dealer","customer"],function(k,R){ TR.append($("<td class='text-center'>").text(CTs[C][R].length)); });
			TR.append($("<td class='text-center'>").text(ORD[x]))
		})
	}
}

function Distribute_IWC(R){
	Count = {}; IDs = $.each($_Industry,function(IC,IO){ L = IO.customers.length; if(!Count[L]) Count[L] = []; Count[L].push(IC); });
	ORD = Object.keys(Count).reverse(); TBD3 = $(".panel.industry_customers tbody").empty(); RC = 0;
	for(x in ORD){
		$.each(Count[ORD[x]],function(i,IC){
			if(++RC > R) return false;
			TR = $("<tr>").appendTo(TBD3);
			TR.html($("<td class='text-center'>").text(RC)).append($("<td>").text(IDs[IC].name));
			TR.append($("<td class='text-center'>").text(ORD[x]))
		})
	}
}

function ListRecvd(R){
	$.each(R,DistList);
	//DistList(k,R[k]);
}

function DistList(i,O){
	$S = $("select[name='VP_"+i+"']");
	if(i == "dealer") { $("option:gt(0)",$S).remove();  $("option:gt(0)",$("select[name='VP_customer']")).remove(); }
	$.each(O,function(C,N){ if($("option[value='"+C+"']",$S).length == 0) $S.append($("<option value='"+C+"'>").text(N))})
}

function PN(I){
	i = new Object({"C":"customer","D":"dealer","S":"distributor"})[I];
	location.href = _RouteLinks[i].replace("--CODE--",$("select[name='VP_"+i+"']").val());
	//console.log(L)
	//return $('<a>').attr("href",_RouteLinks[new Object({"C":"customer","D":"dealer","S":"distributor"})[T]].replace("--CODE--",C)).text(N)
}



function ArrangePartnersBasic(Ary,Role){
	PC = Ary[0];
	if(!$_Partners[PC] || (!$_Partners[PC][1] || !$_Partners[PC][2])) $_Partners[PC] = [Ary[1],Ary[2],Ary[3]];
	if($.inArray(PC,$_Role[Role])<0) $_Role[Role].push(PC);
	if(!Ary[2]) return PC;
	$.each(Ary[2].split(", "),function(j,CRY){ if(!$_Country[CRY]) $_Country[CRY] = {"customer":[],"dealer":[],"distributor":[],"company":[]}; if($.inArray(PC,$_Country[CRY][Role])<0) $_Country[CRY][Role].push(PC); });
	return PC;
}

function PartnerRelations(PC,PRC){
	if(!$_Parent[PC]) $_Parent[PC] = PRC; if(!$_Childs[PRC]) $_Childs[PRC] = []; if($.inArray(PC,$_Childs[PRC])<0) $_Childs[PRC].push(PC);
}

function GetList(item,parent){
	FireAPI("api/v1/mit/list/"+item+((parent)?"/"+parent:''),ListRecvd)
}

function LINK(T,N,C){
	return $('<a>').attr("href",_RouteLinks[new Object({"C":"customer","D":"dealer","S":"distributor"})[T]].replace("--CODE--",C)).text(N)
	//link = _RouteLinks[new Object({"C":"customer","D":"dealer","S":"distributor"})[T]].replace("--CODE--",C)
}

function EditPRV(){
	$(".page_record_view").next().slideUp(100).next().slideDown(100)
}
function CancelPRV(){
	$(".page_record_view").next().slideDown(100).next().slideUp(100)
}
function ChangePRV(){
	$("label.prv_lbl").text((new Object({_:"Records",__:"Days"}))[$("[name='prv_req']").val()])
}
function SubmitPRV(){
	window.location.search = "?"+$("[name='prv_req']").val()+"="+$("[name='prv_cnt']").val()
}

function LoadTransactions(){
	if($_Transaction) return PopulateTransactions($_Transaction);
	FireAPI("api/v1/mit/"+$_PartnerCode+"/transactions",function(Data){
		PopulateTransactions($_Transaction = Data);
	})
}

function PopulateTransactions(Data){
	$TBD = $("table.transactions tbody").empty();
	PreBal = 0; C = Data.length;
	$.each(Data,function(i,T){
		TR = $("<tr>").prependTo($TBD);
		$("<td>").text(C-i).appendTo(TR);
		$("<td>").text(ReadableDate(T[0])).appendTo(TR);
		$("<td>").text(T[1]).appendTo(TR);
		$("<td class='text-center'>").text(amount = parseFloat(T[2])).appendTo(TR);
		$("<td class='text-center'>").text(PreBal -= (amount * parseInt(T[3]))).appendTo(TR);
	});
	T = parseInt($("select[name='TransactionRecords']").val());
	$("tr",$TBD).removeAttr("style").filter(":gt("+T+")").css("display","none")
}



function PageRecordView(){
	PRV = $(".page_record_view");
	PRV.html("This page took details of "+((_DaysNo > 0)?('last '+_DaysNo+' Days'):('recent '+_ItemsNo+' records')) );
	RA = window.location.search.substr(1).split("=");
	$("[name='prv_req']").val(RA[0]); $("[name='prv_cnt']").val(RA[1]);
}

function paginate(){
	if(_DaysNo > 0) return "/"+_DaysNo;
	return "/"+_ItemsNo+"/"+1;
}


function btn(title,url,icn){
	return $("<a>").attr({"href":url,"title":title,"class":"btn"}).html(icon(icn));
}

function icon(icn){
	return $("<span>").addClass("glyphicon glyphicon-"+icn)
}

function CountryChanged(){
	CSel = $('[name="country"]');
	$.getJSON('api/'+CSel.val()+'/states',function(STJ){
		$('[name="state"]').html($('<option value="">').text('Select State')).append(CreateNodes(STJ,'option',1,{value:0}));
	})
	CO = $('option[value="'+CSel.val()+'"]');
	$('[name="phonecode"]').val(CO.attr('data-phonecode')); $('.input-group-addon.phonecode').text(CO.attr('data-phonecode'));
	$('[name="currency"]').val(CO.attr('data-currency'));
}

function StateChanged(){
	SSel = $('[name="state"]');
	$.getJSON('api/'+SSel.val()+'/cities',function(CTJ){
		$('[name="city"]').html($('<option value="">').text('Select City')).append(CreateNodes(CTJ,'option',1,{value:0}));
	})
}
