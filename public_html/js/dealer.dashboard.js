
var _Product = new Object();
var _Edition = new Object();
var _RecentDays = 45;

$(function(){
	LoadMyProducts(); LoadMyDetails();
});


function LoadMyProducts(){
	FireAPI("api/v1/dealer/myproducts",DisplayProducts);
}

function DisplayProducts(Obj){
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
			NewTR.appendTo("table.products tbody");
		})
	})
	LoadCustomers();
}

function LoadMyDetails(){
	FireAPI("api/v1/dealer/mydetails",DisplayDetails);
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

function LoadCustomers(){
	FireAPI("api/v1/dealer/mycustomers",DisplayCustomers);
}

function DisplayCustomers(R){
	RecentlyAdded = {}; RecentlyRegistered = {}; AllCustomers = [];
	PEC = new Object();
	$.each(R,function(i,CObj){
		CreatedAtArray = (CObj.created_at)?((new Date((CObj.created_at).split(" ")[0])).toDateString().split(" ")):null;
		RegisteredAtArray = (CObj.registered_on)?((new Date(CObj.registered_on)).toDateString().split(" ")):null;
		CreatedAt = (CObj.created_at) ? ([CreatedAtArray[2],CreatedAtArray[1],CreatedAtArray[3]].join("/")) : null;
		RegisteredAt = (CObj.registered_on) ? ([RegisteredAtArray[2],RegisteredAtArray[1],RegisteredAtArray[3]].join("/")) : null;
		Index = (AllCustomers.push([CObj.name,_Product[CObj.product],_Edition[CObj.edition],CObj.created_at,CObj.created_since,CObj.registered_on,CObj.registered_since,((CreatedAt)?(CreatedAt+" ("+(CObj.created_since)+")"):null),((RegisteredAt)?(RegisteredAt+" ("+(CObj.registered_since)+")"):null)]))-1;
		if(parseInt(CObj.created_since) < _RecentDays){
			if(RecentlyAdded[CObj.created_since] === undefined) RecentlyAdded[CObj.created_since] = [];
			RecentlyAdded[CObj.created_since].push(Index);
		}
		if(parseInt(CObj.registered_since) < _RecentDays){
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
	$.each(CAry,function(i,C){
		NewTR = $("<tr>");
		$("<td>").text(i+1).appendTo(NewTR);
		$.each([0,1,2,7,8],function(j,n){
			$("<td>").html(((C[n])?(C[n]):($("<span>").addClass("glyphicon glyphicon-remove")))).appendTo(NewTR);
		})
		NewTR.appendTo("table.customers tbody");
	})
}

function DistributeRecentRegistered(RR,CAry){
	Ord = Object.keys(RR).sort(); s = 0;
	$.each(Ord,function(i,S){
		$.each(RR[S],function(j,m){
			NewTR = $("<tr>");
			C = CAry[m];
			$("<td>").text(++s).appendTo(NewTR);
			$.each([0,1,2,8,7],function(k,n){
				$("<td>").html(((C[n])?(C[n]):($("<span>").addClass("glyphicon glyphicon-remove")))).appendTo(NewTR);
			})
			NewTR.appendTo("table.recent_registered tbody");
		})
	})
}

function DistributeRecentAdded(RA,CAry){
	Ord = Object.keys(RA).sort(); s = 0;
	$.each(Ord,function(i,S){
		$.each(RA[S],function(j,m){
			NewTR = $("<tr>");
			C = CAry[m];
			$("<td>").text(++s).appendTo(NewTR);
			$.each([0,1,2,7,8],function(k,n){
				$("<td>").html(((C[n])?(C[n]):($("<span>").addClass("glyphicon glyphicon-remove")))).appendTo(NewTR);
			})
			NewTR.appendTo("table.recent_added tbody");
		})
	})
}

function UpdatePEC(PEC){
	$.each(PEC,function(P,O1){
		$.each(O1,function(E,O2){
			tr = $("tr[data-product='"+P+"'][data-edition='"+E+"']");
			$("td.nc",tr).text(O2[0]);
			$("td.rc",tr).text(O2[1]);
			$("td.tc",tr).text(O2.reduce((o,n)=>(o+n),0));
		})
	})
}








function StateChanged(){
	if($("[name='state']").val() == "") return;
	FireAPI("api/"+$("[name='state']").val()+"/cities",function(R){
		$("[name='city']").html(CreateNodes(R,"option","1",{"value":"0"}))
	});
}









