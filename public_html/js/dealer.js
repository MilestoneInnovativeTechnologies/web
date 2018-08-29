$(function(){
	if($("form.dealer_create").length || $("form.dealer_update").length) {
		FireAPI("api/v1/countries",DistributeCountries);
		if(typeof(PreDefinedValues) == "undefined") return AddOneMoreLine();
		$.each(PreDefinedValues,function(i,Ary){ AddOneMoreLineWithValue(Ary[0],Ary[1]); })
	} else if($(".content.dealer_show").length) {
		LoadDealer(_Dealer);
	} else if($(".dealers_list").length){
		LoadDealers(1,40);
	} else if($(".content.dealer_products").length){
		$.each(PreDefinedValues,function(i,Ary){ AddOneMoreLineWithValue(Ary[0],Ary[1]); })
		$(function(){
			OLD_ProductChanged = ProductChanged;
			ProductChanged = function(LNo){
				LNO = OLD_ProductChanged(LNo);
				DOProductChangeFurther(LNO);
			}
		})
		InitialDistributePrice();
	} else if($(".content.dealer_countries").length){
		
	}
});

var _PrdOpts = false, _EdtOpts = {};

function AddOneMoreLine(){
	NLN = ($("tr.detail_line").length)? (parseInt($("tr.detail_line:last").attr("data-line"))+1) : 1;
	NewTR = $("<tr>").attr({"data-line":NLN,class:"detail_line"});
	PrdOpts = (_PrdOpts) ? _PrdOpts : _PrdOpts = $("<p>").html(CreateNodes(_Products,"option","name",{"value":"code","data-private":"private"})).html();
	NewTR.append($("<td>").html(PrdSel = $("<select>").html(PrdOpts).attr({"name":'product[]',"class":"form-control product","onChange":"ProductChanged('"+NLN+"')"})))
	NewTR.append($("<td>").html($("<select>").attr({"name":'edition[]',"class":"form-control edition"})));
	if($(".content.dealer_products").length){
		$.each(["currency","cost","price","mrp"],function(n,Class){
			NewTR.append($("<td>").attr({"class":Class}).text("-"));
		})
	}
	NewTR.append($("<td>").html($("<a>").attr({"class":"btn btn-default","href":'javascript:RemoveDetailLine("'+NLN+'")'}).html($("<span class='glyphicon glyphicon-remove'>"))));
	NewTR.appendTo("table.products tbody"); $("option[data-private='YES']",PrdSel).each(function(i,Opt){ $(Opt).append(' (Private)'); })
	PrdSel.trigger("change");
	return NLN;
}

function AddOneMoreLineWithValue(P,E,V,C){
	NLN = AddOneMoreLine();
	TR = $("tr[data-line='"+NLN+"']");
	$("select.product",TR).val(P).trigger("change");
	$("input.price",TR).val(V); $("input.currency",TR).val(C);
	$("select.edition",TR).val(E);
	return true;
}

function RemoveFirstLine(){
	if($("tr.detail_line").length) return $("tr.detail_line").remove();
	setTimeout(RemoveFirstLine,500);
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

function DistributeCountries(R){
	Country = $("select[name='country']"); DPV = Country.attr("data-pre-value") || '101';
	Country.html(CreateNodes(R,"option","1",{"value":"0","data-phonecode":"2","data-currency":"3"})).val(DPV);
	CountryChanged();
}

function CountryChanged(){
	sel = $("select[name='country']");
	FireAPI("api/"+(sel.val())+"/states",DistributeStates)
	PC = $("option[value='"+(sel.val())+"']").attr("data-phonecode");
	$("[name='phonecode']").val(PC); $(".input-group-addon.phonecode").text(PC);
	$("[name='currency']").val($("option[value='"+(sel.val())+"']").attr("data-currency"));
}

function DistributeStates(R){
	State = $("select[name='state']"); DPV = State.attr("data-pre-value");
	State.html(CreateNodes(R,"option","1",{"value":"0"}));
	if(DPV && DPV != "") State.val(DPV);
	StateChanged();
}

function StateChanged(){
	sel = $("select[name='state']");
	FireAPI("api/"+(sel.val())+"/cities",function(R){
		$("select[name='city']").html(CreateNodes(R,"option","1",{"value":"0"}));
		DPV = $("select[name='city']").attr("data-pre-value");
		if(DPV && DPV != "") $("select[name='city']").val(DPV);
	});
}

function LoadDealers(page,items){
	items = items || 40;
	FireAPI("api/v1/dealer/list/"+page+"/"+items,function(R){
		TBody = $(".dealers_list table tbody").empty();
		if($.isEmptyObject(R)) return ($(".table-responsive.dealers_list").fadeOut() && $(".jumbotron").fadeIn())
		$.each(R,function(I,Obj){
			TR = $("<tr>").appendTo(TBody).html($("<td>").text(I+1));
			$.each(["pcode","name","country","email","phone"],function(J,F){
				$("<td>").text(Obj[F]).appendTo(TR);
			});
			TR.append($("<td>").append(btn("View/Edit Products of "+Obj.name,_urls.products.replace("--CODE--",Obj.pcode),"cd")).append(btn("View/Edit Countries authorized to "+Obj.name,_urls.countries.replace("--CODE--",Obj.pcode),"map-marker")).append(btn("View Details of "+Obj.name,_urls.show.replace("--CODE--",Obj.pcode),"list-alt")).append(btn("Edit "+Obj.name,_urls.edit.replace("--CODE--",Obj.pcode),"edit")).append(btn("Send login reset link for "+Obj.name,'javascript:LoginReset("'+Obj.pcode+'","'+Obj.name+'","'+Obj.email+'")',"log-in"))/*.append(btn("Delete "+Obj.name,_urls.delete.replace("--CODE--",Obj.pcode),"remove"))*/);
		});
	});
}

function LoadDealer(Code){
	FireAPI("api/v1/dealer/"+Code,ShowDealerDetails)
	//console.log(Code);
}

function ShowDealerDetails(R){
	main = $(".panel.main");
	$(".panel-heading strong",main).text(R.name);
	D = R.details;
	Addr = [D.address1,D.address2,D.city,D.state,R.country].join(", "); $("p.address").text(Addr); $("p.phone").text('+'+D.phonecode+'-'+D.phone); $("p.email").text(R.email);
	$("p.countries").html("<ol><li>" + R.countries.join("</li><li>") + "</li></ol>");
	$("p.currency").text(R.currency);
	CreatedAtArray =	(R.created_at)?((new Date((R.created_at).split(" ")[0])).toDateString().split(" ")):null;
	CreatedAt = (R.created_at) ? ([CreatedAtArray[2],CreatedAtArray[1],CreatedAtArray[3]].join("/")) : null;
	Others = "<label>Dealer since: </label> "+CreatedAt+"<br><br><label>Current Status: </label> "+R.status+((R.status_description)?('<br>'+R.status_description):'');
	$("p.since").text(ReadableDate(R.created_at));
	$("p.status").html(R.status+((R.status_description)?('<br>'+R.status_description):''));
	$("p.others").html(Others);
	$.each(R.products,function(C,PO){
		MyEdts=[];
		$.each(R.product_editions[C],function(i,EC){
			MyEdts.push(R.editions[EC])
		});
		//$("<li>").text(PO[0]).append(((PO[1] == "YES")?' (Private)':'')).append($("<ol>").html("<li>"+(MyEdts.join("</li><li>"))+"</li>")).appendTo("ul.products");
		$("<li>").html($("<label>").text(PO[0]).append(((PO[1] == "YES")?' (Private)':''))).append("  - ").append(MyEdts.join(", ")).appendTo($("ul.products"))
	})
	
}

function DOProductChangeFurther(LNo){
	TR = $("tr[data-line='"+LNo+"']");
	PRDVal = $("[name='product[]']",TR).val();
	EDT = $("[name='edition[]']",TR); EDTVal = EDT.val();
	PutPriceDetails(LNo,_PriceList[[PRDVal,EDTVal].join(":")]);
	EDT.attr("onChange","PriceListEditionChange('"+LNo+"')")
}

function InitialDistributePrice(){
	$("tr[data-line]").each(function(m,tr){
		TR = $(tr); dl = TR.attr('data-line');
		PRDVal = $("[name='product[]']",TR).val(); EDT = $("[name='edition[]']",TR); EDTVal = EDT.val();
		PutPriceDetails(dl,_PriceList[[PRDVal,EDTVal].join(":")]);
		EDT.attr("onChange","PriceListEditionChange('"+dl+"')")
	})
}

function PutPriceDetails(LNo,PA){
	PA = (typeof(PA) == "undefined") ? [0,0,0,0] : PA.map(function(MP){ return isNaN(parseInt(MP)) ? MP : parseFloat(MP) });
	TR = $("tr[data-line='"+LNo+"']");
	$.each(["cost","price","mrp","currency"],function(n,Class){
		$("td."+Class,TR).text(PA[n])
	})
}

function PriceListEditionChange(LNo){
	TR = $("tr[data-line='"+LNo+"']");
	PRDVal = $("[name='product[]']",TR).val();
	EDTVal = $("[name='edition[]']",TR).val();
	PutPriceDetails(LNo,_PriceList[[PRDVal,EDTVal].join(":")])
}

function LoginReset(C,N,E){
	modal = $("#modalLoginReset").attr('data-code',C).modal('show');
	$('.modal_dealer_code',modal).text(C); $('.modal_dealer_name',modal).text(N); $('.modal_dealer_email',modal).text(E);
}

function SendLRL(){
	modal = $("#modalLoginReset");
	FireAPI('api/v1/'+modal.attr('data-code')+'/resetlogin',ConfirmRLL);
	modal.modal('hide');
}

function ConfirmRLL(R){
	alert('Login Reset link have successfully mailed to '+R[1]+', at, '+R[2]);
}
