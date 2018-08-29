// JavaScript Document


function BrowseFile(){
	$('[name="chat_file"]').trigger('click');
}

function UploadFile(){
	ChangeUploadButtonText('Uploading....');
	MakeUploadDivAsProgress()
	doUploadChatFile();
}

function ChangeUploadButtonText(Txt){
	$('.browse_file').text(Txt)
}

function doUploadChatFile(){
	url = GetConvFileUploadUrl()
	$.ajax({
		url: url,
		type: 'POST',
		data: new FormData($('form')[0]),
		cache: false,
		contentType: false,
		processData: false,
		dataType:'json',
		xhr: function() {
			var xhr = new window.XMLHttpRequest();
    	xhr.upload.addEventListener("progress", function(evt) {
      	if (evt.lengthComputable) {
					UploadChatFileProgres(evt.loaded,evt.total)
      	}
    	}, false);
    	return xhr;
  	},
		success:ChatFileUploadComplete,
	})
}

function UploadChatFileProgres(loaded,max){
	MaxPercent = 90; StartPercent = 200; ProgressStep = (StartPercent-MaxPercent)/100;
	LoadedPercent = (loaded/max)*100; CurrentProgressPos = StartPercent - (ProgressStep*LoadedPercent);
	UpdateUploadProgressPercent(CurrentProgressPos);
}

function UpdateUploadProgressPercent(data){
	$('.progress').css('background-position-x',data+"%");
}

function ChatFileUploadComplete(D){
	ChangeUploadButtonText('Upload Completed, sending chat...');
	PrepareAndSendUploadChat(D)
	RemoveProgressFromUploadDiv();
	ChangeUploadButtonText('Browse File');
}

function MakeUploadDivAsProgress(){
	$('.browse_file').addClass('progress');
}

function RemoveProgressFromUploadDiv(){
	$('.browse_file').removeClass('progress');
}

function PrepareAndSendUploadChat(D){
	PopulateAllChats(D);
}

function GetConvFileUploadUrl(){
	tkt = GetTicketCode(); usr = GetUserCode();
	return ['api/v1/tkt/action',usr,tkt,'ucf'].join("/");
}
