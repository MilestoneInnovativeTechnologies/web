$(function(){
	
	if($("#new_customer_form").length){
		ChangeBinds = new Object({"country":CountryChanged,"email":EmailChanged,"product":ProductChanged,"industry":IndustryChanged,"state":StateChanged});
		$.each(ChangeBinds,function(field,funct){
			$('[name="'+field+'"]').on('change',funct);
		})
		LoadCountries(); LoadIndustries(); LoadProducts();
	}
	
});


/*
function NameChanged(){
	if($("[name='name']").val() == "") return;
	CheckUnique($("[name='name']").val())
}

function CheckUnique(name){
	FireAPI("api/v1/customer/unique/"+name,function(Response){
		if(!Response.unique) return PushError("name","The name, "+Response.name+" already taken. Please choose a unique name.",false);
		if(hasValidation("name")) return PushSuccess("name","The name, "+Response.name+" is unique. You are requested to go ahead.")
	})
}
*/

function LoadCountries(){
	FireAPI("api/v1/countries",fillCountry);
}
function LoadIndustries(){
	FireAPI("api/industries",fillIndustry);
}
function LoadProducts(){
	FireAPI("api/v1/products",segregateProducts);
}
function LoadDealers(){
	FireAPI("api/v1/dealers",fillDealers);
}



function fillCountry(Response){
	Options = CreateNodes(Response,"option","1",{"value":"0","data-currency":"3","data-phonecode":"2"});
	Country = $("select[name='country']");
	Country.empty().html(Options); PopulateDefaultValue(Country);
	Country.trigger("change");
}
function fillIndustry(Response){
	Response.push({"name":"Add New Industry","code":"-1"});
	Options = CreateNodes(Response,"option","name",{"value":"code"});
	Industry = $("select[name='industry']");
	Industry.empty().html($("<option value=''>").text(" ")).append(Options);
	PopulateDefaultValue(Industry);
}
function fillDealers(Response){
	DealerSel = $("select[name='dealer']").html($("<option value=''>").text("Select Dealer if any"));
	$.each(Response,function(C,N){ $("<option value='"+C+"'>").text(N).appendTo(DealerSel); })
	PopulateDefaultValue(DealerSel);
}

function CountryChanged(){
	Country = $("select[name='country']");
	SelectedOption = $("option[value='"+Country.val()+"']");
	//if($("[name='currency']").val() == "")
	$("[name='currency']").val(SelectedOption.attr("data-currency"));
	$(".input-group-addon.phonecode").text(SelectedOption.attr("data-phonecode"));
	$("[name='phonecode']").val(SelectedOption.attr("data-phonecode"));
	FetchStates()
}

function EmailChanged(){
	if($("[name='email']").val() == "") return;
	FireAPI("api/v1/email/unique/"+$("[name='email']").val(),function(Response){
		if(!Response.unique) return PushError("email","This Email is already registered. Please input a new one",false);
		if(hasValidation("email")) return RemError("email");
	});
}

function ProductChanged(){
	FireAPI("api/v1/"+$("select[name='product']").val()+"/editions",function(Response){
		Editions = [];
		$.each(Response,function(ID,EdtArray){
			if(EdtArray[0]["editions"])
				Editions.push([EdtArray[0]["editions"]["code"],EdtArray[0]["editions"]["name"]]);
		});
		Options = CreateNodes(Editions,"option","1",{"value":"0"});
		$("select[name='edition']").empty().html(Options);
	});
}

function segregateProducts(Response){
	Products = [];
	$.each(Response,function(ID,PrdArray){
		if(PrdArray[0]["products"])
			Products.push([PrdArray[0]["products"]["code"],PrdArray[0]["products"]["name"]]);
	})
	Options = CreateNodes(Products,"option","1",{"value":"0"});
	$("select[name='product']").empty().html(Options).trigger("change");
	PopulateDefaultValue($("select[name='product']"));
}

function IndustryChanged(){
	if($("[name='industry']").val() != "-1") return;
	$("[name='industry']").slideUp();
	$(".new_industry_div").slideDown();
}
function NoNewIndustry(){
	$(".new_industry_div").slideUp();
	$("[name='industry']").slideDown().find("option:first").prop("selected",true);
}

function StateChanged(){
	if(!$("[name='state']").val()) return;
	FireAPI("api/"+($("[name='state']").val())+"/cities",function(Response){
		Options = CreateNodes(Response,"option","1",{"value":"0"});
		City = $("[name='city']").empty().html(Options);
		PopulateDefaultValue(City);
	})
}

function FetchStates(){
	if(!$("select[name='country']").val()) return;
	FireAPI("api/"+($("select[name='country']").val())+"/states",function(Response){
		Options = CreateNodes(Response,"option","1",{"value":"0"});
		State = $("[name='state']").empty().html(Options);
		PopulateDefaultValue(State);
		State.trigger("change");
	})
}






















function PopulateDefaultValue($obj){
	PreVal = $obj.attr("data-pre-value");
	if(PreVal === undefined || typeof(PreVal) == "undefined" || PreVal == "") return;
	$obj.val(PreVal).removeAttr("data-pre-value");
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

function FireAPI(Url,Callback){
	$.get(Url,Callback);
}

function PushError(name,msg,hide){
	if(hasValidation(name)) RemSuccess(name)
	hide = (typeof(hide) == "undefined") ? 7500 : hide;
	MsgSpan = $("<span>").addClass("help-block").text(msg);
	$("[name='"+name+"']").after(MsgSpan).parent("div").addClass("has-error");
	if(hide) setTimeout(function(Div){ $(Div).fadeOut(function(){ $(this).remove(); }).parent("div").removeClass("has-error"); }, hide, MsgSpan);
}

function PushSuccess(name,msg,hide){
	if(hasValidation(name)) RemError(name);
	hide = (typeof(hide) == "undefined") ? 7500 : hide;
	MsgSpan = $("<span>").addClass("help-block").text(msg);
	$("[name='"+name+"']").after(MsgSpan).parent("div").addClass("has-success");
	if(hide) setTimeout(function(Div){ $(Div).fadeOut(function(){ $(this).remove(); }).parent("div").removeClass("has-success"); }, hide, MsgSpan);
}

function RemError(name){
	$("[name='"+name+"']").parent("div").removeClass("has-error").find("span.help-block").remove()
}

function RemSuccess(name){
	$("[name='"+name+"']").siblings("span.help-block").remove().parent("div").removeClass("has-success");
}

function hasValidation(name){
	return ($("[name='"+name+"']").parent("div").hasClass("has-success") || $("[name='"+name+"']").parent("div").hasClass("has-error"));
}
