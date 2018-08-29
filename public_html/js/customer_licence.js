function LicChanged(){
	FD = new FormData();
	if($("[name='licence']").val() == ""){ console.log("NULL"); return; }
	FD.append("licence",$("[name='licence']")[0].files[0]);
	$.ajax({ url: 'api/v1/L2D', type:'post', data:FD, cache:false, dataType: 'json', processData: false, contentType: false, success: function(lic_data){
		Names = ["Address1","Address2","City","State","Country","CompanyName","eMail","SoftwareName"];
		$.each(Names,function(z,Name){
			$("tr."+Name+" td.lf").text(($.inArray(typeof(lic_data[Name]),["string","number"])>-1)?lic_data[Name]:'');
			VerifyDBLCData(Name)
		})
	}}); LicenceFormCheck();
}

function VerifyDBLCData(Name){
	TR = $("tr."+Name);
	DB = $("td.db",TR).text().toLowerCase(); LF = $("td.lf",TR).text().toLowerCase();
	ok = ((DB == LF) || (Name == "Country" && $("td.db",TR).attr("data-sortname").toLowerCase() == LF));
	$("td.ok",TR).html((ok)?gly_icon('ok'):gly_icon('remove')).attr("data-ok",ok) ;
}

function LicenceFormCheck(){
	if($("[data-ok='false']").length) $("input[name='action']").prop("disabled",true);
	else $("input[name='action']").prop("disabled",false);
	setTimeout(LicenceFormCheck,750); 
}