function btn(title,url,icn){
	return $("<a>").attr({"href":url,"title":title,"class":"btn"}).html(gly_icon(icn));
}

function gly_icon(icn){
	return $("<span>").addClass("glyphicon glyphicon-"+icn)
}

function FireAPI(Url,Callback,ParamObj){
	ParamObj = ParamObj || {};
	$.get(Url,ParamObj,Callback);
}

function ReadableDate(T){
	TArray = (new Date((T).split(" ")[0])).toDateString().split(" ");
	return [TArray[2],TArray[1],TArray[3]].join("/");
}

function SecondsToTimeArray(s){
	s = parseInt(s);
	L = [31104000,2592000,86400,3600,60,0]; D = [s,0,0,0,0,0];
	for(x in L){
		dx = parseInt(D[x]); lx = parseInt(L[x]); x = parseInt(x); nx = x+1;
		if(D[nx] === undefined) return D
		if(dx >= lx) {
			D[x] = parseInt(dx/lx);
			Bal = dx%lx;
			if(D[nx] !== undefined) D[nx] = Bal;
		} else {
			D[x] = 0;
			if(D[nx] !== undefined) D[nx] = dx;
		};
		if(D[nx] === undefined) break;
	}
}

function CreateNodes(array,thing,textIndex,criteria){
	if(textIndex === true) return $.map(array,function(T,V){ return $("<"+thing+">").text(T).attr({value:V}); });
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

function DateDiff(D,f){
	f = f || 'd';
	switch(f.toLowerCase()){
		case 'n': f = 2592000000; break; case 'w': f = 604800000; break; case 'd': f = 86400000; break;
		case 'h': f = 3600000; break; case 'm': f = 60000; break; case 's': f = 1000; break;
	}
	return Math.floor((((new Date()).getTime()) - ((new Date(D)).getTime()))/f);
}

function DistributeTableData(S,D,C,P){
	TBD = (typeof(S) == "string") ? $("tbody",$(S)) : S;
	TBD.empty();
	AI = 0;
	$.each(D,function(m,DO){
		TR = $("<tr>").appendTo(TBD);
		$.each(C,function(n,F){
			TD = $("<td>").appendTo(TR);
			if(F == "AI") { TD.text(++AI); return true; }
			FN = P+"_"+F;
			if(F == "") HTM = "";
			else if(typeof(window[FN]) == "function") HTM = window[FN].call(FN,F,DO,TD,TR,AI,m);
			else HTM = F.split(".").reduce(function(r,e,i,a){ return r[e]; },DO);
			TD.html(HTM);
		})
	})
}

function GetSelOption(T,V){
	return $("<option>").text(T).attr("value",V);
}

function GetBSTable(type){
	type = $.isArray(type)?type:[type];
	cls = $.map(type,function(typ){ return 'table-'+typ; }).join(' ');
	return $('<div>').addClass('table table-responsive').html($('<table>').addClass('table '+cls).html([$('<thead>'),$('<tbody>')]));
}

function GetBSPanel(H){
	H = H || '';
	return $('<div>').addClass('panel panel-default').html([$('<div>').addClass('panel-heading clearfix').html($('<strong>').text(H)),$('<div>').addClass('panel-body clearfix'),$('<div>').addClass('panel-footer clearfix')]);
}

function GetBSModal(H){
	H = H || '';
	D = $('<div>').addClass('modal-dialog')
	$('<div>').addClass('modal-content').html([
	$('<div>').addClass('modal-header').html([$('<button>').addClass('close').attr({type:"button",'data-dismiss':"modal",'aria-hidden':"true"}).html('×'),$('<h4>').addClass('modal-title').html(H)]),
	$('<div>').addClass('modal-body clearfix'),
	$('<div>').addClass('modal-footer clearfix')]).appendTo(D);
	return $('<div>').addClass('modal fade').html(D);
}

function SearchText(){
	location.search = '?search_text='+$('[name="search_text"]').val();
}


// Modal Function


/*
	var _modalHeading = {
		'modalCustomerProductUpdates':"Send Product's latest update details and download links.",
		'modalCustomerProductInformation':"Send Product's Information and download links.",
		'modalResetCustomerLogin':"Send login reset link",
		'modalCustomerPresale':"Change Presale dates of customer",
	}
	var _modalBodyTblClsNRows = {
		'modalCustomerProductUpdates':['striped product_update',['Customer Mail','Product','Edition','Package','Version','Build Date','Change Log']],
		'modalCustomerProductInformation':['striped product_information',['Product','Edition','Package','Email']],
		'modalResetCustomerLogin':['striped reset_distributor_login',['Code','Name','Email']],
		'modalCustomerPresale':['striped customer_presale',['Name','Product','Presale Start Date','Presale End Date','Presale Extend To']],
	}
	var _modalFooterButtons = {
		'modalCustomerProductUpdates':['Close',['SendProductUpdateMail','Send Update details and Download Links']],
		'modalCustomerProductInformation':['Close',['SendProductInformationMail','Send Product details and Download Links']],
		'modalResetCustomerLogin':['Close',['SendLoginResetLink','Send Login Reset Link by Mail']],
		'modalCustomerPresale':['Close',['ChangeCustomerPresale','Change Presale Dates']],
	}

*/

function getModal(ID){
	if($('#'+ID).length) return $('#'+ID);
	else return CreateModal(ID).appendTo('body').attr('id',ID);
}

function CreateModal(ID){
	return GetBSModal(getModalHeading(ID)).find('.modal-body').html(GetModalBodyHtml(ID)).end().find('.modal-footer').html(GetModalFooterButtons(ID)).end();
}

function getModalHeading(ID){
	return _modalHeading[ID];
}

function GetModalBodyHtml(ID){
	FN = "MBH_"+ID;
	if(window[FN]) return window[FN]();
	return GetBSTable(_modalBodyTblClsNRows[ID][0]).find('tbody').html(TblRows(_modalBodyTblClsNRows[ID][1])).end();
}

function TblRows(RAry){
	return $.map(RAry,function(HD){
		cls = HD.replace(/\s/g,'_').toLowerCase();
		return $('<tr>').addClass(cls).html([$('<th>').addClass('thd').text(HD),$('<td>').addClass('tbd').text('')]);
	})
}

function GetModalFooterButtons(ID){
	Buttons = _modalFooterButtons[ID];
	return $.map(Buttons,function(BN){
		if(typeof(BN) == 'string') return ReadyMadeButton(BN);
		return $('<button>').attr({type:'button',class:'btn btn-info',onClick:'javascript:'+BN[0]+'()'}).html(BN[1]);
	})
}

function ReadyMadeButton(BN){
	if(BN == 'Close') return $('<button>').attr({type:'button',class:'btn btn-default','data-dismiss':'modal'}).text('Close');
}

function FillModalTable(modal,ValObj){
	$.each(ValObj,function(tr,Value){
		cls = tr.replace(/\s/g,'_').toLowerCase();
		$('td.tbd',$('tr.'+cls)).html(Value);
	})
}

function getSimpleOptions(Ary){
	return $.map(Ary,function(Name, Code){
		return $('<option>').attr({value:Code}).text(Name);
	})
}
