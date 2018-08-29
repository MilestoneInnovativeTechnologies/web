$(function(){

	$("[name='displayname']").on("keyup",UpdateBasename);
	$(".create_form").on("submit",ValidateForm)
	$(".update_form").on("submit",ValidateForm)
	
});

function UpdateBasename(){
	var str = ($("[name='displayname']").val()+"").replace(/^(.)|\s+(.)/g, function ($1) { return $1.toUpperCase(); });
	str = str.replace(/\s/g,"").toLowerCase();
	$("[name='name']").val(str);
}

function ValidateForm(){
	if(/\s/g.test($("[name='name']").val())){
		alert("Spaces are not allowed in Base Name.");
		return false;
	}
	return true;
}

