// JavaScript Document

$(function(){
	if($(".content.form").length){
		LoadCountries()
	}
})

function LoadCountries(){
	FireAPI('api/countries',function(D){
		D.unshift(['','Select Country','',''])
		DistributeOptions('country',D,1,{value:0,'data-currency':2,'data-phonecode':3});
		BindOnChange('country','CountryChanged');
		SetDefaultValue('country')
	})
}

function DistributeOptions(FldNme,OptAry,Text,Critertia){
	FLD = $('[name="'+FldNme+'"]');
	Options = CreateNodes(OptAry,'option',Text,Critertia);
	FLD.html(Options);
}

function BindOnChange(FldNme,Fun){
	FLD = $('[name="'+FldNme+'"]');
	FLD.attr('onchange',Fun+'(this.value)');
}

function CountryChanged(Cntry){
	OPT = $('[name="country"] option[value="'+Cntry+'"]');
	CNCY = OPT.attr('data-currency'); PCDE = OPT.attr('data-phonecode');
	LoadStates(Cntry);
	SetCountryAttrs(CNCY,PCDE);
}

function SetCountryAttrs(C,P){
	$('[name="currency"]').val(C);
	$('[name="phonecode"]').val(P);
	$('.input-group-addon.phonecode').text(P);
}

function LoadStates(Cntry){
	FireAPI('api/'+Cntry+'/states',function(SD){
		SD.unshift(['','Select State']);
		DistributeOptions('state',SD,1,{value:0});
		BindOnChange('state','StateChanged');
		SetDefaultValue('state');
	})
}

function StateChanged(Ste){
	FireAPI('api/'+Ste+'/cities',function(CD){
		DistributeOptions('city',CD,1,{value:0});
		SetDefaultValue('city');
	})
}

function SetDefaultValue(FldNme){
	FLD = $('[name="'+FldNme+'"]');
	PV = FLD.attr('data-pre-value');
	if(PV) FLD.val(PV).removeAttr('data-pre-value').trigger('change');
}


























