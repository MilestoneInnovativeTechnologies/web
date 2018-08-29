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
		$("#description_internal").removeAttr("readonly");
	});
	$("input.package_upload").on("change",function(){
		FileName = $(this).val().split("\\").pop();
		RE = new RegExp('(\\d+)\.(\\d+)\.(\\d+)\.(\\d+)\.exe');
		match_array = RE.exec(FileName);
		match_array = $.isArray(match_array) ? match_array.slice(1,5) : [0,0,0,0];
		SetVersions(match_array);
		ValidateUploadingFile(FileName)
	});
	sel = '[name="' + ["major_version","minor_version","build_version","revision"].join('"],[name="') + '"]';
	$(sel).on("change",function(){
		UpdateVersionString();
	});
	$('#over_ftp').on("change",OverFTP);
	$('#name').on("keyup",UpdateBaseName);
})

function UpdateVersionString(){
	field_array = ["major_version","minor_version","build_version","revision"];
	field_value = [];
	$.each(field_array,function(I,F){
		field_value.push($("input[name='"+F+"']").val());
	});
	$("[name='version_string']").val($("[name='version_string']").attr("data-alpha") + field_value.join("."));
}
function SetVersions(A){
	field_array = ["major_version","minor_version","build_version","revision"];
	$.each(field_array,function(I,F){
		$("input[name='"+F+"']").val(A[I]).trigger("change");
	});
}
function GetVersionNo(){
	field_array = ["major_version","minor_version","build_version","revision"];
	version_num = [];
	$.each(field_array,function(I,F){
		version_num.push($("input[name='"+F+"']").val());
	});
	return version_num.join(".");
}
function ValidateUploadingFile(FileName){
	version_string = $("[name='version_string']").attr("data-alpha") + GetVersionNo();
	if(FileName == version_string + ".exe"){
		$("[name='version_string']").val(version_string).prop("readonly",true);
		$('[type="submit"]').prop("disabled",false)
	} else {
		alert("File selected doesn't match with the expected one. Please check the file.");
		$('[type="submit"]').prop("disabled",true)
	}
}
function OverFTP(){
	$('input[name="package"]').prop("disabled",$('#over_ftp').prop("checked"));
	if($('#over_ftp').prop("checked")){
		UpdateVersionString();
		$('[type="submit"]').prop("disabled",false);
	} else {
	}
}
function Validate(){
	field_array = ["major_version","minor_version","build_version","revision"];
	multiply = [1000000000,1000000,1000,1];
	MyWeightage = 0;
	$.each(field_array,function(i,n){
		MyWeightage += (parseInt($("[name='"+n+"']").val()) * multiply[i])
	})
	console.log(MyWeightage,weightage);
	if(weightage >= MyWeightage){
		alert("A higher/similiar version of the same paackage is already existing..");
		return false;
	}
	return true;
}

function RejectReason(frm){
	pmt = prompt("Reason for Rejection","   ");
	if(pmt){
		$("#"+frm).find("input[name='reason']").val(pmt).end().find("a").replaceWith($('<input type="submit" name="submit" class="btn btn-primary" value="Reject">'));
		$("#"+frm).find("input[value='Reject']").trigger("click");
	} else {
		return;
	}
}

function UpdateBaseName(){
	str = ($("#name").val()+"").replace(/^(.)|\s+(.)/g, function ($1) { return $1.toUpperCase(); });
	str = str.replace(/\s/g,"").toLowerCase();
	$("#base_name").val(str);
}

function ValidateBaseName(){
	bn = $("#base_name").val();
	if(/\s/g.test(bn)){
		alert("Spaces are not allowed in Base Name.");
		return false;
	}
	return true;
}

function WithDrawReason(frm,PR,ED,PA,VE){
	Modal = $('#withdrawModal').attr('data-form',frm); Modal.modal('show').find('textarea').val('');
	$('tr.product td').text(PR); $('tr.edition td').text(ED); $('tr.package td').text(PA); $('tr.version td').text(VE);
}

function ConfirmWithDraw(){
	frm = (Modal = $('#withdrawModal')).attr('data-form');
	$("#"+frm+' [name="reason"]').val($('textarea[name="modal_reason"]').val());
	Modal.modal('hide');
	$("#"+frm).trigger("submit");
}