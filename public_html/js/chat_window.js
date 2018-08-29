
function ResizeChatWindow(){
	$('.chat_window .panel-body').height($(window).height()-400);
}

function AlterConvType(){
	X = ['chat','file'];
	for(x in X){
		if($('.con_type.active').hasClass(X[x]))
			return ConvTypeActive(X[x]);
	}

}

function ConvTypeActive(type){
	ConvTypeChangeActive(type)
	ConvTypeViewField(type)
	ChatSendButtonActivate(type)
}

function ConvTypeChangeActive(type){
	A = {'chat':'file','file':'chat'}
	$('.conversation_types .con_type').filter('.'+type).removeClass('active').end().filter('.'+A[type]).addClass('active');
}

function ConvTypeViewField(type){
	A = {'file':['chat_textarea','browse_file'],'chat':['browse_file','chat_textarea']}
	$('.'+A[type][0]).slideDown(200); $('.'+A[type][1]).slideUp(200);
}

function ChatSendButtonActivate(type){
	A = {'file':'removeClass','chat':'addClass'}
	$('.chat_send_button')[A[type]]('disabled');
}


function bindMetaClickMonitor(){
	getChatTextObj().keydown(function (e) {
		if((e.keyCode == 13 || e.keyCode == 10)){
			if(e.shiftKey || e.metaKey){
				CurrentVal = getChatTextObj().val();
				NewVal = $.trim(CurrentVal) += "\r\n";
				getChatTextObj().val(NewVal).focus();				
			} else {
				SendChatText();
			}
		}
	});
}


function ScrollChatAreaToDown(){
	CCHolder = GetChatContentDiv();
	HolderHeight = CCHolder.height(); ParentHeight = CCHolder.parent().height();
	ScrollableAmout = (HolderHeight - ParentHeight)
	if(ScrollableAmout > 0) CCHolder.parent().animate({ scrollTop: ScrollableAmout }, 150);
}



function getChatTextObj(){
	return $('[name="chat_text"]');
}

function GetValueFromName(n){
	return $('[name="'+n+'"]').val()
}
function GetTicketCode(){
	return GetValueFromName('ticket');
}
function GetTaskCode(){
	return GetValueFromName('task');
}
function GetUserCode(){
	return GetValueFromName('user');
}
function GetCustomerCode(){
	return GetValueFromName('customer');
}
function GetTeamCode(){
	return GetValueFromName('team');
}
function GetProductCode(){
	return GetValueFromName('product');
}
function GetEditionCode(){
	return GetValueFromName('edition');
}
function GetSeqNo(){
	return GetValueFromName('reg_seq');
}

function GetChatContentDiv(){
	return $('.chat_window .conv_content_holder');
}

function ClearChatTextarea(){
	getChatTextObj().val('')
}
function FocusChatTextarea(){
	getChatTextObj().focus();
}
