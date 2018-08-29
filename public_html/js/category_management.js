// JavaScript Document

var __PRODUCT, __CATEGORY;

$(function(){
	if(__CUSTOMER) GetCustomerCategories(__CUSTOMER);
	else $('[name="customer"]').on('change',function(){
		GetCustomerCategories(this.value);
	});
	$('[name="product"]').on('change',function(){
		__PRODUCT = this.value; LoadProductCategories(__CUSTOMER,__PRODUCT)
	})
	$('[name="category"]').on('change',function(){
		__CATEGORY = this.value; LoadCategorySpecs(__CATEGORY);
	})
})

function GetCustomerCategories(C){
	FireAPI('api/v1/tkt/get/ccat',function(CJ){
		PopulateCategories(CJ,'customer');
	},{cus:C})
}

function LoadMyCategories(){
	GetCustomerCategories(__CUSTOMER);
}

function PopulateCategories(J,A){ console.log(J,A)
	$Category = $('[name="category"]'); $('option[data-'+A+']',$Category).remove();
	$.each(getSimpleOptions(J),function(i,$Opt){ $Category.prepend($Opt.attr('data-'+A,1)); });
	SetPreValue($Category);
	Val = $Category.attr('data-pre-value');
	if(!Val) return $Category.trigger('change');
	ForceLoadCategory(Val)
}

function LoadProductCategories(C,R){
	FireAPI('api/v1/tkt/get/atc',function(CJ){
		PopulateCategories(CJ,'product');
	},{seq:R,cus:C});
}

function LoadCategorySpecs(C){
	if(!C) return ClearCategorySpecs();
	FireAPI('api/v1/tkt/get/cspec',function(SJ){
		if(typeof SJ != "object") return ClearCategorySpecs();
		PopulateCategorySpecs(__CATEGORY,SJ)
	},{cat:C})
}

function ClearCategorySpecs(){
	_md = $('.left_section');
	$('._csp',_md).remove();
	return _md;
}

function GetFormGroup(){
	D1 = $('<div>').addClass('form-group clearfix form-horizontal _fg _csp');
	D2 = $('<div>').addClass('col-xs-8 _fc');
	L1 = $('<label>').addClass('control-label col-xs-4 _fl');
	return D1.html([L1,D2]);
}

function GetFormControl(Cat,Spc,Vals){
	_FN = Cat + "[" + Spc + "]";
	if($.isEmptyObject(Vals)){
		INP = $('<input>').attr({type:'text', class:'form-control', name:_FN});
		if(typeof __CSPEC != 'undefined' && typeof __CSPEC[Spc] != 'undefined') INP.attr('value',__CSPEC[Spc]);
		return INP;
	} else {
		S1 = $('<select>').attr({name:_FN, class:'form-control'});
		Os = CreateNodes(Vals,'option','name',{value:'code'});
		if(typeof __CSPEC != 'undefined' && typeof __CSPEC[Spc] != 'undefined') Os = AddAttrSelected(Os,__CSPEC[Spc]);
		return S1.html(Os)
	}
}

function PopulateCategorySpecs(C,S){
	_md = ClearCategorySpecs();
	$.each(S,function(i,SObj){
		_csp = GetFormGroup();
		_fc = GetFormControl(C,SObj.code,SObj.spec_values);
		_csp.find('._fl').text(SObj.name).end().find('._fc').html(_fc);
		_md.append(_csp);
	})
}

function SetPreValue(Sel){
	Sel = $(Sel); Val = Sel.attr('data-pre-value'); if(!Val || !$('option[value="'+Val+'"]',Sel).length) return;
	$('option[value="'+Val+'"]',Sel).attr('selected',true); Sel.removeAttr('data-pre-value');
}	

function AddAttrSelected(Ary,Val){
	return $.map(Ary,function(Obj){
		if(Obj.attr('value') == Val) Obj.attr('selected',true);
		return Obj;
	})
}

_ForceLoadCategoryRan = 0;
function ForceLoadCategory(C){
	if(_ForceLoadCategoryRan++) return;
	if(!C) return;
	FireAPI('api/v1/tkt/get/fgcat',function(FC){
		if(typeof FC == "object") return PopulateCategories(FC,'force');
	},{cat:C})
}


