$(function(){
	$("#datepicker").datepicker({autoclose:true,defaultViewDate:'today'});
	CalculateAmount();
})



function AmtChanged(TID){
	PA = getPrice(TID);
	Ex = ModifyAmount(1,PA);
	SetEx(TID,Ex);
	CalculateAmount();
}

function getPrice(TID){
	TR = $("table.transactions tbody tr[data-code='"+TID+"']");
	P = parseFloat($("td.price",TR).text());
	E = $("[name='ex["+TID+"]']").val() || $("td.ex",TR).text()
	A = $("[name='amount["+TID+"]']").val() || $("td.amount",TR).text()
	return [parseFloat(P),parseFloat(E),parseFloat(A),$("td.price small",TR).text().substr(1,1)]
}

function ModifyAmount(P,PA){
	F = ["(-2-/-1-)","(-2-/-0-)","(-0-)*(-1-)"];
	return parseFloat(eval(F[P].replace("-0-",PA[0]).replace("-1-",PA[1]).replace("-2-",PA[2])));
}

function SetEx(TID,Ex){
	$("[name='ex["+TID+"]']").val(parseFloat(Ex))
}

function ExChanged(TID){
	PA = getPrice(TID);
	Amt = ModifyAmount(2,PA)
	SetAmount(TID,Amt);
	CalculateAmount();
}

function SetAmount(TID,Amt){
	$("[name='amount["+TID+"]']").val(parseFloat(Amt))
}

function CalculateAmount(){
	BS = new Object({"-":0,"+":0});
	$("tr[data-code][data-status='active']",$("table.transactions")).each(function(i,tr){
		TID = $(tr).attr("data-code");
		PA = getPrice(TID);
		BS[PA[3]]+=PA[2];
	})
	UpdateSummary(BS);
}

function UpdateSummary(SObj){
	$("div.type-plus").text(SObj["+"]);
	$("div.type-minus").text(SObj["-"]);
	UpdateBalance(SObj)
}

function UpdateBalance(SObj){
	$("div.balance").text(Math.abs(SObj["+"]-SObj["-"])).prev().prev().append($("<small>").text(" - to " + ((SObj["+"]<SObj["-"])?'company':'distributor'))).find("small:last").prev().remove();
}

function StatusChange(TID){
	//$("tr[data-code='"+TID+"']").attr("data-status","inactive").find("td.status").text("INACTIVE");
	FireAPI("api/v1/mit/exec/alterstatus/"+TID,function(Data){
		$("tr[data-code='"+Data.code+"']").attr("data-status",Data.status.toLowerCase()).find("td.status").text(Data.status);
		CalculateAmount();
	});
}

function SubmitChanges(TID){
	PA = getPrice(TID);
	FireAPI("api/v1/mit/exec/updateprice/"+TID+"/"+PA.join("|"),function(Data){
		TR = $("tr[data-code='"+Data.code+"']").attr("data-status",Data.status.toLowerCase());
		$("td.status",TR).text(Data.status);
		$("td.price",TR).text(Data.price).append(" "+Data.currency).append($("<br>")).append($("<small>").text("("+Data.type.substr(0,1)+")")).attr("title","towards "+(Data.type == "-")?"company":"distributor");
		$("[name='ex["+Data.code+"]']").val(Data.exchange_rate);
		$("[name='amount["+Data.code+"]']").val(Data.amount);
		CalculateAmount();
	})
}

function NewTransaction(){
	TXN = GetTransactionCode();
	_NewTrans[TXN] = GetNTData();
	ViewTXN(TXN);
	TransPostActions(true);
}

function GetTransactionCode(){
	RS = (Math.random()+"").substr(2,9).split('').reduce(function(a,c){ return a+String.fromCharCode(65+parseInt(c)) },0).substr(1);
	return ($("tr[data-code='"+RS+"']",$("table.transactions")).length) ? GetTransactionCode() : RS;
}

function GetNTData(){
	Data = {}; Fields = ["date","description","price","exchange_rate","amount","status","type"];
	$.each(Fields,function(n,Field){
		Data[Field] = $("[name='"+Field+"']").val();
	});
	Data.currency = $("[name='type']").parent().next().next().text();
	return Data;
}

function ViewTXN(TXN){
	Data = _NewTrans[TXN];
	TR = $("<tr>").attr({"data-status":Data.status.toLowerCase(),"data-code":TXN});
	$("<td class='no'>").text($("tr[data-code]").length+1).appendTo(TR);
	$("<td class='code'>").text(TXN).appendTo(TR);
	$("<td class='date'>").text(ReadDate(Data.date)).appendTo(TR);
	$("<td class='desc'>").text(Data.description).appendTo(TR);
	$("<td nowrap>").attr({title:"towards "+((Data.type == "-")?"company":"distributor"), class:"price", align:"center"}).text(Data.price+" "+Data.currency).append($("<br>")).append($("<small>").text("("+Data.type+")")).appendTo(TR);
	$("<td nowrap class='ex text-center' style='vertical-align:middle'>").text(Data.exchange_rate).appendTo(TR);
	$("<td nowrap class='amount text-center' style='vertical-align:middle'>").text(parseFloat(Data.price)*parseFloat(Data.exchange_rate)).appendTo(TR);
	$("<td class='status text-center' style='vertical-align:middle'>").text(Data.status).appendTo(TR);
	$("<td nowrap class='action text-center' style='vertical-align:middle'>").html(btn("delete","javascript:DeleteNT('"+TXN+"')","remove")).appendTo(TR);
	TR.insertAfter("tr.primary");
}

function ReadDate(date){
	D = (new Date(date.split("-").reverse())).toString().split(" ");
	return [D[2],D[1],D[3]].join("/")
}

function DeleteNT(TXN){
	$("tr[data-code='"+TXN+"']").remove();
	delete _NewTrans[TXN];
	TransPostActions();
}

function CheckSubmit(){
	$("input.add_new_trans").prop("disabled",!Object.keys(_NewTrans).length);
}

function TransPostActions(added){
	CheckSubmit();
	CalculateAmount();
	if(added) ClearPrimary();
}

function ClearPrimary(){
	$("[name='description']").val('');
	$("[name='price']").val('0');
	$("[name='type']").val('+');
	$("[name='exchange_rate']").val('1'); ExchangeChanged()
	$("[name='status']").val('ACTIVE');
}

function PriceChanged(){
	PA = GetNetPrice();
	SetNT("amount",ModifyAmount(2,PA));
}

function TotalChanged(){
	PA = GetNetPrice();
	SetNT("exchange_rate",ModifyAmount(1,PA));	
}

function ExchangeChanged(){
	PA = GetNetPrice();
	SetNT("amount",ModifyAmount(2,PA));	
}

function GetNetPrice(){
	TR = $("tr.primary");
	return [parseFloat($("[name='price']").val()),parseFloat($("[name='exchange_rate']").val()),parseFloat($("[name='amount']").val()),$("[name='type']").val()]
}

function SetNT(i,a){
	$("input[name='"+i+"']",$("tr.primary")).val(a);
}

function PrepareTransaction(){
	console.log(_NewTrans); DIV = $(".new_trans_data");
	$.each(_NewTrans,function(Identifier,Fields){
		$.each(Fields,function(Field, Value){
			$("<input>").attr({"type":"hidden","name":Identifier+"["+Field+"]","value":Value}).appendTo(DIV);
		})
		$("<input>").attr({"type":"hidden","name":"identifier[]","value":Identifier}).appendTo(DIV);
	})
	return true;
}