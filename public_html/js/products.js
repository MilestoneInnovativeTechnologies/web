// JavaScript Document

$(function(){
	$("#same_as_public").on("change",function(){
		if($(this).prop("checked")){
			$("#description_internal").val($("#description").val()).attr("readonly","readonly");
		} else {
			$("#description_internal").removeAttr("readonly");
		}
	});
	$("#description").on("change",function(){
		$("#same_as_public").prop("checked",false);
		$("#description_internal").removeAttr("readonly")
	});
	$('#name').on("keyup",UpdateBaseName);
})



function UpdateBaseName(){
	str = ($("#name").val()+"").replace(/^(.)|\s+(.)/g, function ($1) { return $1.toUpperCase(); });
	str = str.replace(/\s/g,"");
	$("#basename").val(str);
}

function ValidateBaseName(){
	bn = $("#basename").val();
	if(/\s/g.test(bn)){
		alert("Spaces are not allowed in Base Name.");
		return false;
	}
	return true;
}


