// JavaScript Document

function BrowseUrl($action, $url, $code){
	if($.inArray($action,['et_mail','es_mail','je_mail','ex_mail']) > -1) return SendMail($action,$code)
	window.location.href = $url;
}

function SendMail($action,$code){
	DisableButton($action,$code);
	FireAPI('api/v1/mc/action/sm/'+$action+'/'+$code,function(RJ){
		alert('Mail sent successfully.');
	})
}

function DisableButton($action, $code){
	$('.ib_'+$action,$('tr.c_'+$code)).attr('disabled',true);
	$('.vb_'+$action).attr('disabled',true);
}