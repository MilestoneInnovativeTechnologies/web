$(function(){

	$("[name='displayname']").on("keyup",UpdateBasename);
	$(".create_form").on("submit",ValidateForm)
	$(".update_form").on("submit",ValidateForm)
	if($("#ResourceTable").length) ResourceChange();
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

function ResourceChange(){
	ValArray = $("#ResourceTable").val();
	if(ValArray.length == 0) return $(".panel-body.resource").slideUp();
	$(".panel-body.resource").each(function(Ind,Ele){
		$(Ele)[($.inArray(($(Ele).attr("class").match(/code_(.+)/)[1]),ValArray)<0)?'slideUp':'slideDown']();
	})
}



