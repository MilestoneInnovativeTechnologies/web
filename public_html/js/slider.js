// JavaScript Document

var slides = ['banner01.jpg','banner03.jpg','banner04.jpg','banner05.jpg','banner06.jpg','banner07.jpg'];
var slides_path = 'images/slider/';
var slides_current;
var slides_previous;
var slides_animation_delay = 10000;
var slides_timeout = 0;

$(function(){
	
})

function init_slider(){
	createSliderDivs();
}

function init_sliderDimensions(){
	$('.slide_container .slides').height(getWindowHeight()).find('.slide').height(isWindowPotrait()?getSliderPortaitHeight():getWindowHeight()).width(getWindowWidth());
	setControllersHeight();
	setLogoContainerHeight();
}

function init_animations(){
	showSlide(0);
	slides_timeout = setTimeout(slidesAnimate,slides_animation_delay,slideNextNum(),0);
}

function createSliderDivs(){
	parent_div = $('.slide_container .slides').empty();
	$.each(slides,function(i,image){
		$('<div>').addClass('slide slide_'+i).appendTo(parent_div).css({backgroundImage:'url("'+(slides_path+slides[i])+'")',height:isWindowPotrait()?getSliderPortaitHeight():getWindowHeight(),width:getWindowWidth()});
	})
}

function showSlide(slide_num){
	$('.slides .slide_'+slide_num).fadeIn();
	slides_current = slide_num;
	showContents();
}
function hideSlide(slide_num){
	$('.slides .slide_'+slide_num).fadeOut();
	slides_previous = slide_num;
	hideContents();
}

function slideNextNum(){
	current = slides_current;
	next = current+1;
	return (slides.length > next) ? next : 0;
}

function slidePrevNum(){
	current = slides_current;
	prev = current-1;
	return (prev < 0) ? slides.length-1 : prev;
}

function slidesAnimate(show_number, hide_number){
	hideSlide(hide_number);
	showSlide(show_number);
	slides_timeout = setTimeout(function(){
		slidesAnimate(slideNextNum(),slides_current);
	},slides_animation_delay);
	showContents();
}

function hideContents(){
	$('.slide_content').fadeOut();
}
function showContents(){
	HP = (slides_content_heading.length > slides_current) ? slides_current : 0;
	$('.slide_content').fadeIn().find('.heading').text(slides_content_heading[HP]).end().find('.content').text(slides_content_content[HP].substring(0,500));
	positionContents()
}

function positionContents(){
	sw = slideContentWidth(); sh = slideContentHeight();
	ww = sliderWidth(); wh = sliderHeight();
	left = ww-sw;//(ww)-(sw);//-parseInt(Math.random()*100);
	top1 = wh-sh;//(wh)-(sh);//-parseInt(Math.random()*100);
	off = [75,60]
	setContentPos(top1-off[0],left-off[1]);
}

function slideContentWidth(){
	return $('.slide_content').width()+parseInt($('.slide_content').css('padding-left'))+parseInt($('.slide_content').css('padding-right'));
}

function slideContentHeight(){
	return $('.slide_content').height()+parseInt($('.slide_content').css('padding-top'))+parseInt($('.slide_content').css('padding-bottom'));
}

function setContentPos(top,left){
	$('.slide_content').css({top:top,left:left});
}

function sliderWidth(){
	return $('.slide:first').width();
}

function sliderHeight(){
	return $('.slide:first').height();
}

function getSliderPortaitHeight(){
	ratio = parseInt(1900/1194);
	return getWindowWidth()/ratio;
}

function setControllersHeight(){
	$('.slide_container .controllers div').height(sliderHeight());
}

function setLogoContainerHeight(){
	$('.logo_container').height(sliderHeight());
}

function slidesPrevious(){
	clearTimeout(slides_timeout);
	slidesAnimate(slidePrevNum(),slides_current)
}

function slidesNext(){
	clearTimeout(slides_timeout);
	slidesAnimate(slideNextNum(),slides_current)
}


