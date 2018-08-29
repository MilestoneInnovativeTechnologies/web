// JavaScript Document

function SendChatText(){
	DisableChatTextarea(true);
	chat_text = GetChatText();
	if($.trim(chat_text) == '') return DisableChatTextarea(false);
	PostChatText(chat_text)
}

function DisableChatTextarea(s){
	s = s || false;
	getChatTextObj().prop('disabled',s);
}

function GetChatText(){
	return getChatTextObj().val()
}

function PostChatText(chat_text){
	FireAPI(GetPostChatTextUrl(),DonePostChatText,{ctx:chat_text})
}

function GetPostChatTextUrl(){
	tkt = GetTicketCode(); usr = GetUserCode();
	return ['api/v1/tkt/action',usr,tkt,'scv'].join("/");
}

function GetAllChatUrl(){
	tkt = GetTicketCode(); usr = GetUserCode();
	return ['api/v1/tkt/get',usr,tkt,'gac'].join("/");
}

function DonePostChatText(ResponseChats){
	DisableChatTextarea(false);
	PopulateAllChats(ResponseChats);
}
