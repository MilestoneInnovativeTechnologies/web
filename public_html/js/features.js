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
	})
	
	$("#type").on("change",function(){
		if($("#type").val() == "OPTION" || $("#type").val() == "MULTISELECT"){
			$(".form-group.options").slideDown();
		} else {
			$(".form-group.options").slideUp();
		}
	}).trigger("change");
	
	$("#category").on("change",CategoryChanged)
	
})

function AddNewOption(){
	MD = $(".form-group.options");
	$(".option-pack",MD).filter(":first").clone().appendTo(MD).find("input").val("").attr("name","option["+($(".option-pack",MD).length-1)+"]").next().attr("href","javascript:DeleteOption("+($(".option-pack",MD).length-1)+")");
}
function DeleteOption(N){
	MD = $(".form-group.options");
	N = parseInt(N); L = $(".option-pack",MD).length; $A = $(".option-pack:gt("+N+")",MD);
	if($A.length > 0){
		$A.each(function(Ind,Ele){
			$(Ele).find("input").attr("name","option["+(N+Ind)+"]").next().attr("href","javascript:DeleteOption("+(N+Ind)+")");
		})
		$(".option-pack:eq("+N+")",MD).remove();
	} else {
		if(N == 0) $(".option-pack:first",MD).find("input").val("");
		else $(".option-pack:eq("+N+")",MD).remove();
	}
	//return N?$(".option-pack:eq("+N+")",MD).remove():$(".option-pack:first",MD).find("input").val("");
	//if(N == 0) $(".option-pack:first",MD).find("input").val("");
	//else $(".option-pack:eq("+N+")",MD).remove(); 
}

function CategoryChanged(){
	if($("#category").val() != "-1") return;
	$("#category").slideUp();
	$(".new_category_div").slideDown();
}
function NoNewCategory(){
	$("#category").slideDown().find("option:first").prop("selected",true);
	$(".new_category_div").slideUp();
}


