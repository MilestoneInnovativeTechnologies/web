// JavaScript Document

function ProductChanged(PRD,N){
	$('select.edition',$('tr.product_line[data-line="'+N+'"]')).html(EditionsOptions(PRD));
}

function RemoveProductLine(N){
	$('tr.product_line[data-line="'+N+'"]').remove();
}

function AddProductLine(P,E){
	tbd = $('table.products tbody');
	N = NextProductLineNo();
	TR = CreateProductLine(N);
	TR.appendTo(tbd);
	if(P && E) SelectProductEdition(TR,P,E);
}
function NextProductLineNo(){
	C = parseInt($('table.products tbody .product_line:last').attr('data-line'));
	return (C) ? C+1 : 1;
}
function CreateProductLine(N){
	TR = $('<tr>').attr({'data-line':N,class:'product_line'});
	TR.append([ProductTD(N),EditionTD(),ControlTD(N)]);
	return TR;
}
function ProductTD(N){
	return $('<td>').html($('<select>').attr({name:'product[]',class:'form-control product',onchange:'ProductChanged(this.value,'+N+')'}).html(ProductOptions()))
}
function EditionTD(){
	return $('<td>').html($('<select>').attr({name:'edition[]',class:'form-control edition'}).html(EditionsOptions(Object.keys(_Products)[0])))
}
function ControlTD(N){
	return $('<td>').html($('<a>').attr({class:'btn btn-default',href:'javascript:RemoveProductLine("'+N+'")'}).html($('<span>').addClass('glyphicon glyphicon-remove')))
}
function ProductOptions(){
	return $.map(_Products,function(Ary,Code){ return $('<option>').attr({value:Code}).text(Ary[0]); })
}
function EditionsOptions(P){
	return $.map(_Products[P][1],function(Name,Code){ return $('<option>').attr({value:Code}).text(Name); })
}
function SelectProductEdition(TR,P,E){
	$('select.product',TR).val(P); $('select.edition',TR).val(E);
}


function RemoveLinkLine(N){
	$('tr.link_line[data-line="'+N+'"]').remove();
}

function AddLinkLine(L,N,F,T){
	tbd = $('table.links tbody');
	C = NextLinkLineNo();
	TR = CreateLinkLine(C);
	TR.appendTo(tbd);
	if(L || N || F || T) SetLinkValues(TR,[L,N,F,T]);
}
function NextLinkLineNo(){
	C = parseInt($('table.links tbody .link_line:last').attr('data-line'));
	return (C) ? C+1 : 1;
}
function CreateLinkLine(N){
	TR = $('<tr>').attr({'data-line':N,class:'link_line'});
	TR.append([LinkTD(N),NameTD(N),FaTD(N),TargetTD(N),LinkControlTD(N)]);
	return TR;
}
function LinkTD(N){
	return $('<td>').html($('<input>').attr({type:'text',name:'link['+N+']',class:'form-control link'}).val('#'))
}
function NameTD(N){
	return $('<td>').html($('<input>').attr({type:'text',name:'lname['+N+']',class:'form-control name'}))
}
function FaTD(N){
	return $('<td>').html($('<input>').attr({type:'text',name:'fa['+N+']',class:'form-control fa'}))
}
function TargetTD(N){
	return $('<td>').html($('<select>').attr({name:'target['+N+']',class:'form-control target'}).html(TargetOptions()))
}
function LinkControlTD(N){
	return $('<td>').html($('<a>').attr({class:'btn btn-default',href:'javascript:RemoveLinkLine("'+N+'")'}).html($('<span>').addClass('glyphicon glyphicon-remove')))
}
function TargetOptions(){
	return $.map({'_blank':'New Window','_self':'Self Window'},function(Name,Code){ return $('<option>').attr({value:Code}).text(Name); })
}

function SetLinkValues(TR,Ary){
	$.each(['link','name','fa','target'],function(i,a){ $('.'+a,TR).val(Ary[i]) });
}









function color_scheme_change(){
	R = parseInt($('[name="cs[r]"]').val());
	G = parseInt($('[name="cs[g]"]').val());
	B = parseInt($('[name="cs[b]"]').val());
	if(R>255 || G>255 || B>255) return alert('Any of the color scheme values should not be more than 255');
	$('.color_scheme').css('background-color','rgba('+R+', '+G+', '+B+', 1)').html($('<small>').text([R,G,B].join(',')));
}

function AddProducts(Ary){
	$.each(Ary,function(i,ary){
		AddProductLine(ary[0],ary[1]);
	})
}

function AddLinks(Ary){
	$.each(Ary,function(i,ary){
		AddLinkLine(ary[0],ary[1],ary[2],ary[3]);
	})
}
