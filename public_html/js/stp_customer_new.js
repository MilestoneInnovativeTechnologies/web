// JavaScript Document

$(function(){
	ChangeDistributor()
})

_DP = {}; _DC = {}
function ChangeDistributor(){
	D = $('[name="distributor"]').val();
	if(_DP[D]) PopulateProducts(_DP,D);
	else FireAPI('api/v1/tst/get/dstprd',function(J){
		$.extend(_DP,J);
		PopulateProducts(_DP,$('[name="distributor"]').val());
	},{D:D});
	if(_DC[D]) PopulateCountry(_DC[D]);
	else FireAPI('api/v1/tst/get/dstcnt',function(J){
		_DC[$('[name="distributor"]').val()] = J;
		PopulateCountry(J);
	},{D:D});
	if(_DD[D]) PopulateDealers(_DD[D]);
	else FireAPI('api/v1/tst/get/dstdlr',function(J){
		_DD[$('[name="distributor"]').val()] = J;
		PopulateDealers(J);
	},{D:D})
}

_DPE = {}
function PopulateProducts(J,D){
	if(!_DPE[D]) _DPE[D] = {};
	P = $('[name="product"]').empty();
	$.each(J[D],function(x,Obj){
		PO = Obj.product; EO = Obj.edition;
		if(!_DPE[D][PO.code]) _DPE[D][PO.code] = {}; if(!_DPE[D][PO.code][EO.code]) _DPE[D][PO.code][EO.code] = EO.name;
		if(!$('option[value="'+PO.code+'"]',P).length) $('<option>').attr({value:PO.code}).text(PO.name).appendTo(P);
	})
	P.trigger('change');
}

function ChangeProduct(){
	D = $('[name="distributor"]').val(); P = $('[name="product"]').val();
	E = $('[name="edition"]').empty();
	$.each(_DPE[D][P],function(EC,EN){
		$('<option>').attr({value:EC}).text(EN).appendTo(E);
	})
}

function PopulateCountry(Obj){
	C = $('[name="country"]').empty();
	$.each(Obj,function(c,CO){
		$('<option>').attr({value:c,'data-currency':CO.currency,'data-phonecode':CO.phonecode}).text(CO.name).appendTo(C);
	})
	C.trigger('change');
}

function ChangeCountry(){
	CS = $('[name="country"]'); C = CS.val();
	OS = $('option[value="'+C+'"]',CS);
	CR = OS.attr('data-currency'); PC = OS.attr('data-phonecode');
	$('.input-group-addon.phonecode').text(PC);
	$('[name="phonecode"]').val(PC); $('[name="currency"]').val(CR);
}

_DD = {};
function PopulateDealers(Obj){
	$('[name="dealer"]').html($('<option value="">').text('None')).append($.map(Obj,function(N,C){ return $('<option>').text(N).attr('value',C); }));
}