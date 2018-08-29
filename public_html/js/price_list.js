$(function(){
	if($("form.pricelist_create").length || $("form.pricelist_update").length) {
		if(typeof(PreDefinedValues) == "undefined") return AddOneMoreLine();
		$.each(PreDefinedValues,function(i,Ary){ AddOneMoreLineWithValue(Ary[0],Ary[1],Ary[2],Ary[3],Ary[4],Ary[5]); })
	}
});

var _PrdOpts = false;
var _EdtOpts = {};

function AddOneMoreLine(){
	NLN = ($("tr.detail_line").length)? (parseInt($("tr.detail_line:last").attr("data-line"))+1) : 1;
	NewTR = $("<tr>").attr({"data-line":NLN,class:"detail_line"});
	PrdOpts = (_PrdOpts) ? _PrdOpts : _PrdOpts = $("<p>").html(CreateNodes(_Products,"option","name",{"value":"code","data-private":"private"})).html();
	NewTR.append($("<td>").html(PrdSel = $("<select>").html(PrdOpts).attr({"name":'product[]',"class":"form-control product","onChange":"ProductChanged('"+NLN+"')"})))
	NewTR.append($("<td>").html($("<select>").attr({"name":'edition[]',"class":"form-control edition"})));
	NewTR.append($("<td>").html($("<input>").attr({"name":'mop[]',"class":"form-control mop","type":"text"})));
	NewTR.append($("<td>").html($("<input>").attr({"name":'price[]',"class":"form-control price","type":"text"})));
	NewTR.append($("<td>").html($("<input>").attr({"name":'mrp[]',"class":"form-control mrp","type":"text"})));
	NewTR.append($("<td>").html($("<input>").attr({"name":'currency[]',"class":"form-control currency","type":"text","value":"INR"})));
	NewTR.append($("<td>").html($("<a>").attr({"class":"btn btn-default","href":'javascript:RemoveDetailLine("'+NLN+'")'}).html($("<span class='glyphicon glyphicon-remove'>"))));
	NewTR.appendTo("table.pl_details tbody"); $("option[data-private='YES']",PrdSel).each(function(i,Opt){ $(Opt).append(' (Private)'); })
	PrdSel.trigger("change");
	return NLN;
}

function AddOneMoreLineWithValue(P,E,C,O,M,Y){
	NLN = AddOneMoreLine();
	TR = $("tr[data-line='"+NLN+"']");
	$("select.product",TR).val(P).trigger("change");
	$("input.mop",TR).val(C); $("input.price",TR).val(O);
	$("input.mrp",TR).val(M); $("input.currency",TR).val(Y);
	$("select.edition",TR).val(E);
	return true;
}

function RemoveFirstLine(){
	if($("tr.detail_line").length) return $("tr.detail_line").remove();
	setTimeout(RemoveFirstLine,500);
}

function FireAPI(Url,Callback){
	$.get(Url,Callback);
}

function CreateNodes(array,thing,textIndex,criteria){
	$Return = new Array();
	$.each(array,function(I,Data){
		$Thing = $("<"+thing+">");
		AttributeObject = new Object();
		$.each(criteria,function(name,index){
			AttributeObject[name] = Data[index]
		});
		$Return.push($("<"+thing+">").attr(AttributeObject).text(Data[textIndex]));
	});
	return $Return;
}

function ProductChanged(LNo){
	TR = $("tr[data-line='"+LNo+"']");
	PRD = $("select.product",TR).val();
	EDTs = (_EdtOpts[PRD]) ? _EdtOpts[PRD] : _EdtOpts[PRD] = $("<p>").html(CreateNodes(Object.keys(_Products[PRD]["editions"]).map(function(K){ return[K,_Editions[K],_Products[PRD]["editions"][K]] }),"option","1",{"value":"0","data-private":"2"})).html();
	$("select.edition",TR).html(EDTs).find("option[data-private='YES']").each(function(i,Opt){ $(Opt).append(' (Private)'); })
}

function RemoveDetailLine(LNo){
	$("tr[data-line='"+LNo+"']").remove();
}
