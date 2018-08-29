$(function(){

	$("[name='displayname']").on("keyup",UpdateBasename);
	$(".create_form").on("submit",ValidateForm)
	$(".update_form").on("submit",ValidateForm)
	if($('td.resource_actions').length) ActionsToReadableString($('td.resource_actions'));
	if($('p.raw_actions').length) ActionsToReadableString($('p.raw_actions'));
	if($("#RolesTable").length) RolesValueChanged();
	
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

function ActionsToReadableString(Target){
	Target.each(
		function(I,Ele){
			Actions = [];
			$.each($(Ele).text().substr(2).split(""),function(J,D){
				if(D == "1") Actions.push(TotalActions[J])
			});
			$(Ele).text(Actions.join(", "));
		}
	);
}

function RolesValueChanged(){
	ValArray = $("#RolesTable").val();
	if(ValArray.length == 0) return $("tr[class*='role_']").fadeOut();
	$("tr[class*='role_']").each(function(Ind,Ele){
		$(Ele)[($.inArray(($(Ele).attr("class").match(/role_(\d+)/)[1]),ValArray)<0)?'fadeOut':'fadeIn']();
	})
}








