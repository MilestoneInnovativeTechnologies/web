var _Partners = {}, _Customers = [], _Dealers = [], _Products = {}, _Editions = {}, _PPE = {}, _PEP = {}, _Children = {}, _Register = {}, _Created = {}, _Parent = {}, _Add = {"l":[]}, _Reg = {"l":[]}, _Del = {"l":[]};
var _RDP = 45;


$(function(){
	if($('.change_address.content').length){
		
	} else if($('.change_password.content').length) {
		
	} else {
		LoadDashboardContents(); ArrangeLocations(); DistributeDetails();
	}
});

function LoadDashboardContents(){
	FireAPI("api/v1/dd/content",OrderContents);
}

function ViewDashboard(){
	CreateTable_RAC();
	CreateTable_PRD();
	CreateTable_RRC();
	CreateDROptions()
}

function OrderContents(R){
	$.each(R,function(PID,PArray){
		if(!_Partners[PID]) _Partners[PID] = PArray[0]['name'];
		if(!_Created[PID]) _Created[PID] = (PArray[0]["created_at"])?ReadableTime(PArray[0]["created_at"]):null;
		if(!$.isEmptyObject(PArray[0]['products']) && $.isEmptyObject(PArray[0]['customer_products'])) { _Dealers.push(PID); $Products = PArray[0]['products']; StoreChilds(PID,PArray[0]["children"]); StoreDelCreated(PID,PArray[0]["created_at"]); }
		if($.isEmptyObject(PArray[0]['products']) && !$.isEmptyObject(PArray[0]['customer_products'])) { _Customers.push(PID); $Products = PArray[0]['customer_products']; StoreRegister($Products) }
		$.each($Products,function(m,PrdObj){
			if(!_Products[PrdObj.code]) _Products[PrdObj.code] = PrdObj.name;
			if(!_PPE[PID]) _PPE[PID] = {};
			if(!_PPE[PID][PrdObj.code]) _PPE[PID][PrdObj.code] = [];
			if($.inArray(PrdObj.pivot.edition,_PPE[PID][PrdObj.code])<0) _PPE[PID][PrdObj.code].push(PrdObj.pivot.edition);
			StoreEditions(PrdObj.editions);
		})
	});
	ViewDashboard();
}

function StoreEditions(editions){
	$.each(editions,function(n,EObj){
		if(!_Editions[EObj.code]) _Editions[EObj.code] = EObj.name;
	})
}

function StoreChilds(PID,CArray){
	if(!_Children[PID]) _Children[PID] = [];
	$.each(CArray,function(i,Obj){
		if($.inArray(Obj.partner,_Children[PID])<0) _Children[PID].push(Obj.partner);
		if(!_Parent[Obj.partner]) _Parent[Obj.partner] = PID;
	});
}

function StoreRegister(Prds){
	$.each(Prds,function(n,PO){
		D = PO.pivot;
		if(!_Register[D.customer]) _Register[D.customer] = {};
		if(!_Register[D.customer][D.product]) _Register[D.customer][D.product] = {};
		if(!_Register[D.customer][D.product][D.edition]) _Register[D.customer][D.product][D.edition] = [];
		_Register[D.customer][D.product][D.edition].push((D.registered_on)?ReadableTime(D.registered_on):null);
		if(D.registered_on) { DD = DateDiff(D.registered_on); if(!_Reg[DD]) _Reg[DD] = []; _Reg[DD].push([D.customer,D.product,D.edition].join(":")); } else { _Reg.l.push([D.customer,D.product,D.edition].join(":")) }
		_Register[D.customer][D.product][D.edition].push((D.created_at)?ReadableTime(D.created_at):null);
		if(D.created_at) { DD = DateDiff(D.created_at); if(!_Add[DD]) _Add[DD] = []; _Add[DD].push([D.customer,D.product,D.edition].join(":")); } else { _Add.l.push([D.customer,D.product,D.edition].join(":")) }
		
		if(!_PEP[D.product]) _PEP[D.product] = {};
		if(!_PEP[D.product][D.edition]) _PEP[D.product][D.edition] = {"RC":[],"NRC":[]};
		if(D.registered_on) _PEP[D.product][D.edition]["RC"].push(D.customer); else _PEP[D.product][D.edition]["NRC"].push(D.customer);
	})
}

function StoreDelCreated(P,D){
	if(!D) return _Del.l.push(P);
	D = DateDiff(D); if(!_Del[D]) _Del[D] = [];
	_Del[D].push(P);
}

function FireAPI(Url,Callback){
	$.get(Url,Callback);
}

function ReadableTime(T){
	TArray = (new Date((T).split(" ")[0])).toDateString().split(" ");
	return [TArray[2],TArray[1],TArray[3]].join("/");
}

function DateDiff(D){
	return Math.floor((((new Date()).getTime()) - ((new Date(D)).getTime()))/86400000);
}

function ArrangeLocations(){
	L = $.extend(_Location,null); _Location = new Object(); $.each(L,function(i,LO){
		if(!_Location[LO.db]) _Location[LO.db] = {};
		if(!_Location[LO.db][LO.id]) _Location[LO.db][LO.id] = LO.name;
	})
}

function DistributeDetails(){
	D = _Distributor; DD = D.details; P = D.parent.parent_details; PD = P.details;
	Div = $(".mydetails");
	Address = [D.name,"<br>"+DD.address1,DD.address2,"<br>"+_Location["CT"][DD.city],_Location["ST"][DD.state],"<br>"+_Location["CN"][D.country[0]]].join(", "); $("p.address",Div).html(Address).append("<br><label>Distributor since: </label>"+ReadableTime(D.created_at));
	Contact = ["<label>Phone: </label>"+"+"+DD.phonecode+"-"+DD.phone,"<label>Email: </label>"+D.emails].join("<br>");  $("p.contacts",Div).html(Contact);
	$("p.countries",Div).text(D.country.map(function(C){ return _Location["CN"][C]; }).join(","));
	Div = $(".myparent");
	$("p.name",Div).text(P.name);
	Address = [PD.address1,PD.address2,"<br>"+_Location["CT"][PD.city],_Location["ST"][PD.state],"<br>"+_Location["CN"][P.country[0]]].join(", "); $("p.address",Div).html(Address);
	Contact = ["<label>Phone: </label>"+"+"+PD.phonecode+"-"+PD.phone,"<label>Email: </label>"+P.emails].join("<br>");  $("p.contacts",Div).html(Contact);
}

function CreateTable_RAC(){
	Q = Object.keys(_Add).map(function(a){ return isNaN(parseInt(a))?a:parseInt(a) });//.sort();
	TBD1 = $("table.rac tbody").empty(); Rows = 0;
	$.each(Q,function(i,DP){
		if(DP == "l" || parseInt(DP) > _RDP) return (i == 0) ? TBD1.html($("<tr>").html($("<td colspan='7' align='center'>").html($("<b>").text("No customers added within " + _RDP + " days.")))) : true;
		$.each(_Add[DP],function(j,T){
			TR = $("<tr>").appendTo(TBD1);
			TA = T.split(":");
			TR.html($("<td>").text(++Rows)).append($("<td>").text(MV(0,TA[0]))).append($("<td>").text((MV(3,TA[0]))?(MV(0,MV(3,TA[0]))):("-"))).append($("<td>").text(MV(1,TA[1]))).append($("<td>").text(MV(2,TA[2])));
			RC = MV(4,TA[0])[TA[1]][TA[2]];
			TR.append($("<td>").text(RC[1] + " ("+DateDiff(RC[1])+")"));
			$("<td>").html((RC[0])?(RC[0] + " ("+DateDiff(RC[0])+")"):icon("remove")).appendTo(TR)
		})
		
	});
}

function CreateTable_RRC(){
	Q = Object.keys(_Reg).map(function(a){ return isNaN(parseInt(a))?a:parseInt(a) });//.sort();
	TBD3 = $("table.rrc tbody").empty(); Rows2 = 0;
	$.each(Q,function(i,DP){
		if(DP == "l" || parseInt(DP) > _RDP) return (i == 0) ? TBD3.html($("<tr>").html($("<td colspan='7' align='center'>").html($("<b>").text("No customers added within " + _RDP + " days.")))) : true;
		$.each(_Reg[DP],function(j,T){
			TR = $("<tr>").appendTo(TBD3);
			TA = T.split(":");
			TR.html($("<td>").text(++Rows2)).append($("<td>").text(MV(0,TA[0]))).append($("<td>").text((MV(3,TA[0]))?(MV(0,MV(3,TA[0]))):("-"))).append($("<td>").text(MV(1,TA[1]))).append($("<td>").text(MV(2,TA[2])));
			RC = MV(4,TA[0])[TA[1]][TA[2]];
			TR.append($("<td>").text(RC[0] + " ("+DateDiff(RC[0])+")"));
			$("<td>").html((RC[1])?(RC[1] + " ("+DateDiff(RC[1])+")"):icon("remove")).appendTo(TR)
		})
		
	});
}

function CreateTable_PRD(){
	TBD2 = $("table.products tbody").empty();
	$.each(_MyProducts,function(x,PA){
		TR = $("<tr>").appendTo(TBD2).attr({"data-product":PA[0]}); TTR = 0;
		TEs = PA[2].length;
		$("<td>").text(x+1).appendTo(TR).attr({"rowspan":TEs});
		$("<td>").text(PA[1]).appendTo(TR).attr({"rowspan":TEs});
		$.each(PA[2],function(y,EA){
			if(y > 0) TR = $("<tr>").appendTo(TBD2).attr({"data-product":PA[0]});
			$("<td>").text(EA[1]).appendTo(TR.attr({"data-edition":EA[0]}));
			RC = (_PEP[PA[0]] && _PEP[PA[0]][EA[0]] && _PEP[PA[0]][EA[0]]["RC"]) ? _PEP[PA[0]][EA[0]]["RC"].length : 0; NRC = (_PEP[PA[0]] && _PEP[PA[0]][EA[0]] && _PEP[PA[0]][EA[0]]["NRC"]) ? _PEP[PA[0]][EA[0]]["NRC"].length : 0;
			TTR += (TEC = RC+NRC);
			TR.append($("<td class='RC text-center'>").text(RC)).append($("<td class='NRC text-center'>").text(NRC)).append($("<td class='product_edition_total text-center'>").text(TEC));
			if(y == 0) $("<td>").attr({"rowspan":TEs,"class":'product_total text-center'}).text(TTR).appendTo(TR);
		});
		$('[data-product="'+PA[0]+'"] td.product_total',TBD2).text(TTR);
	})
}
//var _Partners = {}, _Customers = [], _Dealers = [], _Products = {}, _Editions = {}, _PPE = {}, _Children = {}, _Register = {}, _Created = {}, _Parent = {}, _Add = {"l":[]}, _Reg = {"l":[]}, _Del = {"l":[]};
function MV(I,V){
	return eval(["_Partners","_Products","_Editions","_Parent","_Register"][I])[V]
}

function icon(item){
	return $("<span>").addClass("glyphicon glyphicon-"+item)
}

function GetDealerReport(){
	DRC = $("[name='dealer_report']").val();
	FireAPI("api/v1/dd/dealer/"+DRC,ViewDealerReport);
}

function CreateDROptions(){
	DRS = $("[name='dealer_report']").empty();
	$.each(_Dealers,function(i,DC){
		$("<option>").attr({"value":DC}).text(MV(0,DC)).appendTo(DRS);
	})
}

var _Product = {}, _Edition = {};

function ViewDealerReport(R){
	if(R == "0") return alert("No dealer exists..");
	_Product = {}; _Edition = {};
	DisplayProducts(R.Products);
	DisplayCustomers(R.Customers);
	DisplayDetails(R.Details);
	Panel("dealer",1);
}



function DisplayProducts(Obj){
	$Table = $("table.dealer_products tbody").empty();
	$.each(Obj,function(I, Ary){
		NewTR = $("<tr>");
		$("<td>").attr("rowspan",Ary[2].length).text(I+1).appendTo(NewTR);
		$("<td>").attr("rowspan",Ary[2].length).text(Ary[1]).appendTo(NewTR.attr("data-product",Ary[0]));
		_Product[Ary[0]] = Ary[1];
		$.each(Ary[2],function(J, EAry){
			if(J != 0) NewTR = $("<tr>").attr({"data-product":Ary[0],"data-edition":EAry[0]});
			else NewTR.attr({"data-edition":EAry[0]});
			$("<td>").text(EAry[1]).appendTo(NewTR);
			_Edition[EAry[0]] = EAry[1];
			$("<td>").addClass("rc text-center").text(0).appendTo(NewTR);
			$("<td>").addClass("nc text-center").text(0).appendTo(NewTR);
			$("<td>").addClass("tc text-center").text(0).appendTo(NewTR);
			NewTR.appendTo($Table);
		})
	})
}


function DisplayCustomers(R){
	RecentlyAdded = {}; RecentlyRegistered = {}; AllCustomers = [];
	PEC = new Object();
	$.each(R,function(i,CObj){
		CreatedAt = (CObj.created_at) ? (ReadableTime(CObj.created_at)) : null;
		RegisteredAt = (CObj.registered_on) ? (ReadableTime(CObj.registered_on)) : null;
		Index = (AllCustomers.push([CObj.name,_Product[CObj.product],_Edition[CObj.edition],CObj.created_at,CObj.created_since,CObj.registered_on,CObj.registered_since,((CreatedAt)?(CreatedAt+" ("+(CObj.created_since)+")"):null),((RegisteredAt)?(RegisteredAt+" ("+(CObj.registered_since)+")"):null)]))-1;
		if(parseInt(CObj.created_since) < _RDP){
			if(RecentlyAdded[CObj.created_since] === undefined) RecentlyAdded[CObj.created_since] = [];
			RecentlyAdded[CObj.created_since].push(Index);
		}
		if(parseInt(CObj.registered_since) < _RDP){
			if(RecentlyRegistered[CObj.registered_since] === undefined) RecentlyRegistered[CObj.registered_since] = [];
			RecentlyRegistered[CObj.registered_since].push(Index);
		}
		if(PEC[CObj.product] === undefined) PEC[CObj.product] = new Object();
		if(PEC[CObj.product][CObj.edition] === undefined) PEC[CObj.product][CObj.edition] = [0,0];
		if(CObj.registered_on) PEC[CObj.product][CObj.edition][1]++
		else PEC[CObj.product][CObj.edition][0]++
	});
	DistributeAll(AllCustomers); DistributeRecentRegistered(RecentlyRegistered,AllCustomers); DistributeRecentAdded(RecentlyAdded,AllCustomers); UpdatePEC(PEC);
}


function DistributeAll(CAry){
	$Table = $("table.dealer_customers tbody").empty();
	if($.isEmptyObject(CAry)) return $Table.html($("<tr>").html($("<td colspan='6' align='center'>").html($("<strong>").text("No customers added yet."))));
	$.each(CAry,function(i,C){
		NewTR = $("<tr>");
		$("<td>").text(i+1).appendTo(NewTR);
		$.each([0,1,2,7,8],function(j,n){
			$("<td>").html(((C[n])?(C[n]):(icon("remove")))).appendTo(NewTR);
		})
		NewTR.appendTo($Table);
	})
}

function DistributeRecentRegistered(RR,CAry){
	$Table = $("table.dealer_rrc tbody").empty();
	if($.isEmptyObject(RR)) return $Table.html($("<tr>").html($("<td colspan='6' align='center'>").html($("<strong>").text("No customers were registered recently within "+_RDP+" days"))));
	Ord = Object.keys(RR).sort(); s = 0;
	$.each(Ord,function(i,S){
		$.each(RR[S],function(j,m){
			NewTR = $("<tr>");
			C = CAry[m];
			$("<td>").text(++s).appendTo(NewTR);
			$.each([0,1,2,8,7],function(k,n){
				$("<td>").html(((C[n])?(C[n]):(icon("remove")))).appendTo(NewTR);
			})
			NewTR.appendTo($Table);
		})
	})
}

function DistributeRecentAdded(RA,CAry){
	$Table = $("table.dealer_rac tbody").empty();
	if($.isEmptyObject(RA)) return $Table.html($("<tr>").html($("<td colspan='6' align='center'>").html($("<strong>").text("No customers were added recently within "+_RDP+" days"))));
	Ord = Object.keys(RA).sort(); s = 0;
	$.each(Ord,function(i,S){
		$.each(RA[S],function(j,m){
			NewTR = $("<tr>");
			C = CAry[m];
			$("<td>").text(++s).appendTo(NewTR);
			$.each([0,1,2,7,8],function(k,n){
				$("<td>").html(((C[n])?(C[n]):(icon("remove")))).appendTo(NewTR);
			})
			NewTR.appendTo($Table);
		})
	})
}

function UpdatePEC(PEC){
	$.each(PEC,function(P,O1){
		$.each(O1,function(E,O2){
			tr = $("table.dealer_products tr[data-product='"+P+"'][data-edition='"+E+"']");
			$("td.nc",tr).text(O2[0]);
			$("td.rc",tr).text(O2[1]);
			$("td.tc",tr).text(O2.reduce((o,n)=>(o+n),0));
		})
	})
}


function DisplayDetails(D){
	$(".dealer_panel .dealer_name").text(D.name);
	$Tbl = $("table.dealer_details tbody");
	$("td:first",$Tbl).text(ReadableTime(D.created_at)).next().text(D.email).next().text("+"+D.phonecode+"-"+D.phone).next().html([D.address1,D.address2,"<br>"+D.city,D.state,"<br>"+D.country].join(", "))
}

function Panel(N,A){
	panels = ["distributor","dealer","customer","transaction"]; $S = $('.' + panels.join('_panel,.') + '_panel');
	default_view_panel = "distributor";
	view_panel = (A) ? N : default_view_panel; 
	if($S.filter(":visible").attr("class").replace("_panel","") == view_panel) return;
	return $S.filter(":visible").slideUp().end().filter("."+view_panel+"_panel").slideDown();
}

function ViewOwnCustomers(){
	ORD = Object.keys(_Add).sort(); AC = _Register; Tbl = $("table.all_customers tbody").empty(); seq = 0;
	$.each(ORD,function(i,ind){
		$.each(_Add[ind],function(j,CStr){
			CC = CStr.split(":")[0]; RO = _Register[CC];
			if(MV(3,CC)) return true;
			TR = $("<tr>").appendTo(Tbl); Prds = Object.keys(RO).length;
			td1 = $("<td>").attr("rowspan",Prds).text(++seq).appendTo(TR);
			td2 = $("<td>").attr("rowspan",Prds).text(MV(0,CC)).appendTo(TR);
			PDC = 0;
			$.each(RO,function(PC,PO){
				edts = Object.keys(PO).length;
				if(PDC++ > 0) TR = $("<tr>").appendTo(Tbl); 
				td3 = $("<td>").attr("rowspan",edts).text(MV(1,PC)).appendTo(TR);
				NRSP = parseInt(td1.attr("rowspan")) * edts;
				td1.attr("rowspan",NRSP); td2.attr("rowspan",NRSP);
				EDC = 0; $.each(PO,function(EC,RA){
					if(EDC++ > 0) TR = $("<tr>").appendTo(Tbl);
					td4 = $("<td>").text(MV(2,EC)).appendTo(TR);
					td5 = $("<td>").html((RA[1])?(ReadableTime(RA[1])):(icon("remove"))).appendTo(TR);
					td6 = $("<td>").html((RA[0])?(ReadableTime(RA[0])):(icon("remove"))).appendTo(TR);
				})
			})
		})
	});
	Panel("customer",1);
}

function ViewTransactions(){
	Panel("transaction",1);
	if(_Transactions) PopulateTransactions(_Transactions);
	else FireAPI("api/v1/dd/transactions",function(Data){
		PopulateTransactions(_Transactions = Data);
	})
}

function PopulateTransactions(Data){
	$TBD = $("table.transactions tbody").empty();
	PreBal = 0; C = Data.length;
	$.each(Data,function(i,T){
		TR = $("<tr>").prependTo($TBD);
		$("<td>").text(C-i).appendTo(TR);
		$("<td>").text(ReadableTime(T[0])).appendTo(TR);
		$("<td>").text(T[1]).appendTo(TR);
		$("<td class='text-center'>").text(amount = parseFloat(T[2])).appendTo(TR);
		$("<td class='text-center'>").text(PreBal -= (amount * parseInt(T[3]))).appendTo(TR);
	});
	T = parseInt($("select[name='TransactionRecords']").val());
	$("tr",$TBD).removeAttr("style").filter(":gt("+T+")").css("display","none")
}

function StateChanged(){
	if(!$("[name='state']").val() || $("[name='state']").val() == "") return;
	FireAPI("api/"+$("[name='state']").val()+"/cities",function(R){
		$("[name='city']").html(CreateNodes(R,"option","1",{"value":"0"}))
		SetPreVal('city');
	});
}

function CountryChanged(){
	if(!$("[name='country']").val() || $("[name='country']").val() == "") return;
	FireAPI("api/"+$("[name='country']").val()+"/states",function(R){
		$("[name='state']").html(CreateNodes(R,"option","1",{"value":"0"}));
		SetPreVal('state');
		$("[name='city']").empty();
	});
}

function SetPreVal(Name){
	Obj = $('[name="'+Name+'"]'); PV = Obj.attr('data-pre-value');
	if(PV == "") return;
	Obj.val(PV).trigger('change'); Obj.removeAttr('data-pre-value');
}
