// JavaScript Document

var slides = ['banner01.jpg','banner02.jpg','banner03.jpg','banner04.jpg','banner05.jpg','banner06.jpg','banner07.jpg'];
var slides_path = 'images/slider/';

$(function(){
	//init_slider();
	//init_sliderDimensions();
	//init_animations();
	set_pageContentSizes();
})

window.onresize = function(){
	//init_sliderDimensions();
	//positionContents();
	set_pageContentSizes()
}

function set_pageContentSizes(){
	set_pagesizes();
	set_homecontentsize();
	set_productscontentsize();
	set_featurecontentsize();
	set_downloadcontentsize();
	set_contactcontentsize();
}

function getWindowWidth(){
	return $(window).width();
}

function getWindowHeight(){
	return $(window).height();
}

function isWindowPotrait(){
	return (getWindowHeight() > getWindowWidth());
}

function fitBannerToWindow(){
	$('.main_slide_banner').css({height:getWindowHeight(),width:getWindowWidth()});
}

function set_pagesizes(){
	$('.page_contents .page').css({"minHeight":getWindowHeight()});
}

function set_homecontentsize(){
	$('.home_content').css('margin-top',((getWindowHeight()-$('.home_content').height())/2))
}

function set_productscontentsize(){
	$('.hl_holder').height($('.hl_holder').width());
	mt = (get_navheight()+getWindowHeight()-get_productscontentheight())/2;
	mt = (mt<get_navheight()) ? get_navheight() : mt;
	$('.products .row.top').css('margin-top',mt);
	set_proddetailsheight();
}

function set_proddetailsheight(){
	$('.prod_details').height($('.products_contents').height());
}

function get_productscontentheight(){
	t = $('.products .row.top'); b = $('.products .row.bottom');
	return t.height()+b.height()+parseInt(b.css('margin-top'))
}

function get_navheight(){
	return $('nav.navbar').height();
}

var FTHH = 0;
function set_featurecontentsize(){
	avs = getWindowHeight() - get_navheight();
	hh = $('.page.features .heading').height()+parseInt($('.page.features .heading').css('padding-bottom'));
	thh = $('.page.features .thead').height(); FTHH = thh;
	tpl = avs > 500 ? 100 : (avs/5);
	ch = avs-hh-thh-tpl;
	$('.feature_wrapper').height(ch).animate({scrollTop:FTHH},1);
	$('.page.features .heading').css('margin-top',(tpl/2)+get_navheight())
}

function set_downloadcontentsize(){
	dp = $('.page.download .page_content');
	dph = dp.height();
	mt = (getWindowHeight()-dph)/2; mt = (mt<get_navheight())?get_navheight():mt;
	dp.css('margin-top',mt);
}

function set_contactcontentsize(){
	cbh = get_contactbarheight();
	avh = getWindowHeight() - get_navheight();
	sh = avh * 7/100;
	$('.page.contact .strip').height(sh);
	mph = avh-sh-cbh;
	$('.page.contact .map').height(mph).width('100%').css('margin-top',get_navheight()).find('iframe').height(mph).width(mph-50).css({'margin':'auto',height:mph,width:('100%'),'border':'none'});
}

function get_contactbarheight(){
	d = $('.page.contact .contact');
	return d.height()+parseInt(d.css('margin-bottom'))+parseInt(d.css('margin-top'))+parseInt(d.css('padding-bottom'))+parseInt(d.css('padding-top'))
}

function topage(page){
	scrToPage(page)
}

var scrollPos = 0;
var scrollDir = false;
$(function(){/*
	$(window).bind('mousewheel', function(event) {
		scrollDir = event.originalEvent.wheelDelta;
		if(scrollDir < 0 && scrollPos < 60) scrTo(getWindowHeight());
	});
	$(window).scroll(function (event) {
    scrollPos = $(window).scrollTop();
		if(Math.ceil(scrollPos) >= getWindowHeight()) $('nav.navbar').addClass('navbar-fixed-top');
		else $('nav.navbar').removeClass('navbar-fixed-top');
	});*/
	$('.feature_wrapper').scroll(function(event){
		if($('.feature_wrapper').scrollTop()<FTHH) $('.feature_wrapper').animate({scrollTop:FTHH},1);
	})
});

function scrToPage(page){
	scrTo($('.page.'+page).offset().top)
}

function scrTo(n){
	$('html, body').animate({
   scrollTop: n
	}, 500);
}

function goHome(){
	scrollDir = 1;
	scrTo(0);
	$(window).trigger('scroll');
}

function login(){
	$('#loginModal').modal('show');
}

function DownloadApp(App){
	$('#downloadModal').modal('show').attr('data-product',App);
	BackDwnOpts();
}

function UserDownload(){
	$('#downloadModal .user_download_form').slideDown();
	$('#downloadModal .download_options').slideUp();
}

function GuestDownload(){
	$('#downloadModal .guest_download_form').slideDown();
	$('#downloadModal .download_options').slideUp();
}

function BackDwnOpts(){
	$('#downloadModal .download_options').slideDown();
	$('.user_download_form,.guest_download_form,.download_requested',$('#downloadModal')).slideUp();
}

function SendDownloadLink(){
	email = $('[name="email_link"]').val();
	if(!/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,5})+$/.test(email)) return GDEmailError();
	product = $('#downloadModal').attr('data-product');
	RequestDownloadLink(product,email);
}

function GDEmailError(){
	lbl = $('.guest_download_form .control-label');
	$('small',lbl).remove();
	sm = $('<small>').text('     Email seems to be invalid.').css('color','#F00').appendTo(lbl);
	setTimeout(function(sm){ sm.remove(); },3000,sm);
}

function RequestDownloadLink(product,email){
	$.post('api/sdl',{product:product,email:email},function(){
		$('#downloadModal').modal('hide');
	});
	$('#downloadModal .guest_download_form').slideUp();
	$('#downloadModal .download_requested').slideDown();
}

function PreviousProduct(){
	$('.products_contents .products_content:last').prependTo($('.products_contents'));
	set_proddetailsheight();
}

function NextProduct(){
	$('.products_contents .products_content:first').appendTo($('.products_contents'));
	set_proddetailsheight();
}

var _FeatureStorage = {};
function LoadFeatures(PID,PNAME){
	$('.feature_dropdown_li_a').html(PNAME).append("      ").append($('<span>').addClass('caret'));
	if(_FeatureStorage[PID]) return FeatureDistribute(_FeatureStorage[PID]);
	$.getJSON('api/features/'+PID,function(FObj){
		_FeatureStorage[PID] = FObj;
		FeatureDistribute(FObj);
	})
}

function FeatureDistribute(FObj){
	editions = FObj.editions;
	setFeatureThead(editions);
	features = FObj.features;
	setProductFeatures(features);
	setEditionFeatures(editions);
	set_featurecontentsize();
	change_featureYesToCheck();
}

function change_featureYesToCheck(){
	$('.features_tbody td:contains("-")').text('');
	$('.features_tbody td:contains("YES")').html($('<img>').attr({'src':'images/check.png'}));
}

var FeatureEditions = [];
function setFeatureThead(editions){
	THTR = $('tr.features_editions').empty();
	Edtns = $.each(Object.keys(editions),function(i,Edtn){
		THTR.append($('<th>').text(Edtn.toUpperCase()));
	});
	$('.features_edition_th').attr('colspan',Edtns.length);
	FeatureEditions = Edtns;
}

function getReadyMadeFeatureTDs(Edtns){
	TDArray = [];
	$.each(FeatureEditions,function(j,Edtn){
		tdclass = get_text2class(Edtn);
		tdtext = '-';
		TDArray.push($('<td>').addClass(tdclass).text(tdtext));
	})
	return TDArray;
}

function get_text2class(str){
	return str.replace(/\s/g, '_').toLowerCase();
}

function setProductFeatures(features){
	tbody = $('.features_tbody').empty();
	$.each(features,function(k,FAry){
		NewFeatureTR(FAry,false).appendTo(tbody);
	})
}

function setEditionFeatures(editions){
	tbody2 = $('.features_tbody')
	$.each(editions,function(edtn,EAry){
		$.each(EAry,function(l,FAry2){
			A_TR = $('tr#trf_'+FAry2[0],tbody2)
			if(A_TR.length) EditFeatureTR(A_TR,FAry2,get_text2class(edtn))
			else NewFeatureTR(FAry2,get_text2class(edtn)).appendTo(tbody);
		})
	})
}

function NewFeatureTR(NV,W){
	TR = $('<tr>').attr('id','trf_'+NV[0]);
	TDs = getReadyMadeFeatureTDs();
	TR.html(TDs);
	if(W){
		$('td.'+W,TR).text(NV[2])
	} else {
		$('td',TR).text(NV[2])
	}
	return TR.prepend($('<td>').text(NV[1]).css('text-align','left'));
}

function EditFeatureTR(TR,NV,W){
	$('td.'+W,TR).text(NV[2]);
}




