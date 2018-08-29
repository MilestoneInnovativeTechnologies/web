// JavaScript Document

$(function(){
	GetAllChats()
})

function PopulateChat(Chat){
	Html = GetConvHtml(Chat)
	GetChatContentDiv().append(Html);
	ClearChatTextarea();
	FocusChatTextarea();
}

function GetConvHtml(Chat){
	if(IsExistsCC(Chat)) return;
	CCDiv = $('<div>').addClass('conv_content')
		.addClass(GetChatType(Chat))
		.addClass('user_'+GetChatAuthor(Chat))
		.addClass(GetChatInitType(Chat))
		.attr('id','cc_'+Chat.id);
	CCUser = $('<div>').addClass('conv_user').text(GetChatAuthorName(Chat));
	CCTime = $('<div>').addClass('conv_time').text(GetChatCreateTime(Chat));
	CCText = $('<div>').addClass('conv_text').html(GetChatContext(Chat));
	return CCDiv.html([CCUser, CCTime, CCText])
}

function GetChatInitType(Chat){
	return (Chat.user.code == GetUserCode()) ? 'self' : 'other';
}

function GetChatType(Chat){
	return Chat.type.toLowerCase();
}

function GetChatAuthor(Chat){
	return Chat.user.code;
}

function GetChatAuthorName(Chat){
	return Chat.user.name;
}

function GetChatCreateTime(Chat){
	DT = Chat.created_at.split(" "); YMD = DT[0].split("-"); HIS = DT[1].split(":");
	if(parseInt(YMD[2]) == (new Date()).getDate()) return PureTime(HIS);
	return DateTime(Chat.created_at)
}

function DateTime(D){
	a = new Date(D);
	return a.getDate()+"/"+(parseInt(a.getMonth())+1) + " - " + PureTime(D.split(" ")[1].split(":"))
}

function PureTime(HIS){
	AP = (parseInt(HIS[0])>11) ? "PM" : "AM";
	H = (parseInt(HIS[0])>12) ? parseInt(HIS[0])-12 : HIS[0];
	M = HIS[1];
	return [H,M].join(":")+" "+AP;
}

function GetChatContext(Chat){
	if(GetChatType(Chat) == 'chat') return Chat.content.replace(/(?:\r\n|\r|\n)/g, '<br />');
	if(GetChatType(Chat) == 'file') return GetFileConvContent(Chat);
	if(GetChatType(Chat) == 'link') return GetLinkConvContent(Chat);
	if(GetChatType(Chat) == 'info') return Chat.content;
}

function GetAllChats(){
	FireAPI(GetAllChatUrl(),PopulateAllChats);
}

function PopulateAllChats(ResponseChats){
	$.each(ResponseChats,function(i,Chat){
		PopulateChat(Chat)
	})
	ScrollChatAreaToDown();
}

function GetFileConvContent(Chat){
	FJ = JSON.parse(Chat.content);
	M = $('<div>').addClass('name').html(GetConvContentMainDivContent(FJ));
	I = $('<div>').addClass('clearfix info').html(GetConvContentInfoDivContent(FJ))
	return [M,I];
}

function GetLinkConvContent(Chat){
	FJ = JSON.parse(Chat.content);
	M = $('<div>').addClass('name').html($('<strong>').text(FJ.name))
	I = $('<div>').addClass('clearfix info').html([
		$('<div>').addClass('description').html(FJ.description.replace(/(?:\r\n|\r|\n)/g, '<br />')),
		$('<div>').addClass('link').html($('<small>').html($('<a>').attr({href:FJ.link,'target':'_blank'}).text('Browse Link'))),
	]);
	return [M,I];
}

function GetExtension(FJ){
	if(FJ.ext) return FJ.ext.toUpperCase();
	return FJ.name.split('.').pop().toUpperCase();
}

function GetReadableSize(size){
	size = parseInt(size); Unit = ['Bytes','KB','MB','GB']; RSize = '0 Bytes';
	for(x in Unit){
		Pow = Math.pow(1024,x);
		if(size >= Pow) RSize = (size/Pow).toFixed(2) + " " + Unit[x];
		else return RSize;
	}
	return RSize;
}

function IsExistsCC(Chat){
	return $('#cc_'+Chat.id).length;
}

function IsMimeOfImage(mime){
	return /image/g.test(mime);
}

function GetConvContentInfoDivContent(FJ){
	return [
		$('<div>').addClass('col-xs-4 type').html($('<span>').html($('<strong>').text('Type: '))).append(GetExtension(FJ)),
		$('<div>').addClass('col-xs-4 size').html($('<span>').html($('<strong>').text('Size: '))).append(GetReadableSize(FJ.size)),
		$('<div>').addClass('col-xs-4 link').html($('<span>').html($('<a>').attr({'href':FJ.link,'class':'','target':'_blank'}).text('Download'))),
	];
}

function GetConvContentMainDivContent(FJ){
	return IsMimeOfImage(FJ.mime) ? $('<div>').addClass('col-xs-12 image').css('background-image','url("'+_ConvImageViewPath.replace('--PATH--',FJ.file)+'")'): [$('<strong>').text('File: '), FJ.name]
}




