// JavaScript Document

window.onresize = ResizeChatWindow;

$(function(){
	ResizeChatWindow();
	bindMetaClickMonitor();
	setTimeout(FrequentChatCheck,5000);
	AddClassToAllToggleA();
})

function AddClassToAllToggleA(){
	$('a').filter('[href^="javascript:Toggle"]').addClass('toggle_anchor')
}


function TogglePanelView(panel){
	AR = {'glyphicon-minus':'glyphicon-plus','glyphicon-plus':'glyphicon-minus'}
	$('.panel.'+panel+' .panel-body').slideToggle();
	$.each(AR,function(h,a){ if($('.panel.'+panel+' a.toggle_anchor span').hasClass(h)) { $('.panel.'+panel+' a.toggle_anchor span').removeClass(h).addClass(a); return false; } })
}