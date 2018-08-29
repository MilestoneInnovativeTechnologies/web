// JavaScript Document


function FrequentChatCheck(){
	FireAPI(GetLCUrl(),function(RJ){
		if(!$.isEmptyObject(RJ)) PopulateAllChats(RJ);
		setTimeout(FrequentChatCheck,5000);
	})
}

function GetLCUrl(){
	return ['api/v1/tkt/get',GetUserCode(),GetTicketCode(),'glc'].join('/')
}



