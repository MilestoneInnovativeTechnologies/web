// JavaScript Document


function TPTAction(code,media){
	FireAPI(['api/v1/tkt/action',GetUserCode(),GetTicketCode(),'stpadl'].join('/'),function(D){
		console.log(D);
	},{code:code,media:media,downloads:GetValueFromName('tpa_downloads_'+code)})
}
