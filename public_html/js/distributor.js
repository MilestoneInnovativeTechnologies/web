$(function(){
	if($("form.distributor_create").length || $("form.distributor_update").length) {
		FireAPI("api/countries",DistributeCountries);
		FireAPI("api/v1/pricelist/all",DistributePricelist);
		if(typeof(PreDefinedValues) == "undefined") return AddOneMoreLine();
		$.each(PreDefinedValues,function(i,Ary){ AddOneMoreLineWithValue(Ary[0],Ary[1]); })
	} else if($(".content.distributor_show").length) {
		//LoadDistributor(_Distributor);
	} else if($(".content.distributor_lists").length){
		//LoadDistributors(1,40);
	} else if($(".content.distributor_products").length){
		$.each(PreDefinedValues,function(i,Ary){ AddOneMoreLineWithValue(Ary[0],Ary[1]); })
		$(function(){
			OLD_ProductChanged = ProductChanged;
			ProductChanged = function(LNo){
				LNO = OLD_ProductChanged(LNo);
				DOProductChangeFurther(LNO);
			}
		})
		InitialDistributePrice();
	}
});

var _PrdOpts = false, _EdtOpts = {};

function AddOneMoreLine(){
	NLN = ($("tr.detail_line").length)? (parseInt($("tr.detail_line:last").attr("data-line"))+1) : 1;
	NewTR = $("<tr>").attr({"data-line":NLN,class:"detail_line"});
	PrdOpts = (_PrdOpts) ? _PrdOpts : _PrdOpts = $("<p>").html(CreateNodes(_Products,"option","name",{"value":"code","data-private":"private"})).html();
	NewTR.append($("<td>").html(PrdSel = $("<select>").html(PrdOpts).attr({"name":'product[]',"class":"form-control product","onChange":"ProductChanged('"+NLN+"')"})))
	NewTR.append($("<td>").html($("<select>").attr({"name":'edition[]',"class":"form-control edition"})));
	if($(".content.distributor_products").length){
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
	return NLN;
}

function RemoveFirstLine(){
	if($("tr.detail_line").length) return $("tr.detail_line").remove();
	setTimeout(RemoveFirstLine,500);
}

function ProductChanged(LNo){
	TR = $("tr[data-line='"+LNo+"']");
	PRD = $("select.product",TR).val();
	EDTs = (_EdtOpts[PRD]) ? _EdtOpts[PRD] : _EdtOpts[PRD] = $("<p>").html(CreateNodes(Object.keys(_Products[PRD]["editions"]).map(function(K){ return[K,_Editions[K],_Products[PRD]["editions"][K]] }),"option","1",{"value":"0","data-private":"2"})).html();
	$("select.edition",TR).html(EDTs).find("option[data-private='YES']").each(function(i,Opt){ $(Opt).append(' (Private)'); });
	return LNo;
}

function RemoveDetailLine(LNo){
	$("tr[data-line='"+LNo+"']").remove();
}

function DistributeCountries(R){
	Country = $("select[name='country']"); DPV = Country.attr("data-pre-value") || '101';
	Country.html(CreateNodes(R,"option","1",{"value":"0","data-phonecode":"3","data-currency":"2"})).val(DPV);
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

function DistributePricelist(R){
	$("select[name='pricelist']").html(CreateNodes(R,"option","1",{"value":"0"}));
	DPV = $("select[name='pricelist']").attr("data-pre-value");
	if(DPV && DPV != "") $("select[name='pricelist']").val(DPV);
}

function LoadDistributor(Code){
	FireAPI("api/v1/distributor/"+Code,ShowDistributorDetails)
	//console.log(Code);
}

function ShowDistributorDetails(R){
	main = $(".panel.main");
	$(".panel-heading strong",main).text(R.name);
	D = R.details;
	Addr = [D.address1,D.address2,D.city,D.state,R.country].join(", "); $("p.address").html(Addr);
	Cont = ['<label>Email: </label> '+R.email,'<br><label>Phone: </label> +'+D.phonecode+'-'+D.phone].join(", "); $("p.contacts").html(Cont);
	Cntry = R.countries.join("</li><li>"); $("p.countries").html("<ol><li>"+Cntry+"</li><ol>"); $("p.currency").text(R.currency);
	$("p.since").html((R.created_at) ? (ReadableDate(R.created_at)) : null)
	$("p.pricelist").text(R.price_list);
	$("p.status").text(R.status).append((R.status_description)?('<br>'+R.status_description):'');
	$.each(R.products,function(C,PO){
		MyEdts=[];
		$.each(R.product_editions[C],function(i,EC){
			MyEdts.push(R.editions[EC])
		});
		$("<li>").html($("<label>").text(PO[0]).append(((PO[1] == "YES")?' (Private)':''))).append("  - ").append(MyEdts.join(", ")).appendTo($("ol.products"))
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
	$('.modal_distributor_code',modal).text(C); $('.modal_distributor_name',modal).text(N); $('.modal_distributor_email',modal).text(E);
}


function SendLRL(){
	modal = $("#modalLoginReset");
	FireAPI('api/v1/distributor/'+modal.attr('data-code')+'/resetlogin',ConfirmRLL);
	modal.modal('hide');
}

function ConfirmRLL(R){
	alert('Login Reset link have successfully mailed to '+R[1]+', at, '+R[2]);
}


function SupportTeam(C,N){
	modal = getModalSupportTeam().modal('show').attr('data-dt',C).find('tr.name td').text(N);
	FireAPI('api/v1/mit/get/distributor/'+C+'/supportteam',function(D){
		if(D.team) { STC = D.team.code; STN = D.team.name; }
		else { STC = ''; STN = ''; }
		getModalSupportTeam().attr('data-st',STC)
		$('[name="nst"]').val(STC); $('tr.cst').find('td').text(STN);
	})
}

function getModalSupportTeam(){
	ID = 'modalSupportTeam'; M = $('#'+ID);
	if(M.length) return M;
	return CreateModalSupportTeam().find('.modal-body').html(modalSupportTeamSetup()).end().find('.modal-footer').html(modalSupportTeamButtons()).end();
}

function CreateModalSupportTeam(){
	return GetBSModal('Support Team Details').attr({'id':'modalSupportTeam'}).appendTo('body');
}

function modalSupportTeamSetup(){
	return GetBSTable('striped mst').find('tbody').html([
		$('<tr class="name">').append($('<th>').text('Distributor Name')).append($('<th>').text(' : ')).append($('<td>')),
		$('<tr class="cst">').append($('<th>').text('Current Support Team')).append($('<th>').text(' : ')).append($('<td>')),
		$('<tr class="nst">').append($('<th>').text('Select New Support Team')).append($('<th>').text(' : ')).append($('<td>').html(modalSetupSupportTeamSelect()))
	]).end();
}

function modalSetupSupportTeamSelect(){
	FireAPI('api/v1/mit/get/supportteams',function(R){
		$('select[name="nst"]').html($.map(R,function(N,C){ return $('<option>').text(N).attr('value',C); }))
	})
	return $('<select>').attr({'name':'nst','class':'form-control'})
}

function modalSupportTeamButtons(){
	return	[
		$('<button>').attr({'type':'button','class':'btn btn-default','data-dismiss':'modal'}).text('Close'),
		$('<button>').attr({'type':'button','class':'btn btn-primary','onClick':'UpdateST()'}).text('Update Support Team')
	]
}

function UpdateST(){
	modal = getModalSupportTeam().modal('hide')
	DST = modal.attr('data-dt'); OST = modal.attr('data-st'); NST = $('select[name="nst"]').val();
	console.log(OST,NST)
	if(OST == NST) return;
	FireAPI('api/v1/mit/action/udst',function(S){
		return;
	},{D:DST,S:NST})
}