// JavaScript Document

$(function(){
	SetSDOptions()
	ListSupportAgents();
	CheckValues();
})

var _SDOptions = [];
function SetSDOptions(){
	Opts = [];
	$.each(SD,function(SC,Ary){
		Obj = Ary[0];
		Opts.push($('<label>').html([
			$('<input>').attr({'type':'checkbox','name':'[]','value':Obj.code}).css('margin-right','10px'),
			Obj.name
		]).css('font-weight','normal'))
	});
	_SDOptions = Opts;
}

function ListSupportAgents(){
	tbd = $('.sad tbody').empty();
	TSAs = $.each(TSA,function(AC,Ary){
		Obj = Ary[0];
		$('<tr>').html([
			$('<th>').text(Obj.name),
			$('<td>').html(Departments(Obj.code))
		]).appendTo(tbd)
	})
	if(Object.keys(TSAs).length) $('.sad').slideDown(150).next().slideUp(150).parent().next().slideDown(150);
	else $('.sad').slideUp(150).next().slideDown(150).parent().next().slideup(150);
}

function Departments(Code){
	return $.map(_SDOptions,function(Lbl,i){
		NWLbl = Lbl.clone();
		$('input',NWLbl).attr('name',Code+'[]');
		return $('<div>').addClass('col-xs-4').html(NWLbl);
	});
}

function CheckValues(){
	$.each(SAD,function(AC,Ary){
		$.each(Ary,function(j,DObj){
			console.log(AC,DObj.department.code)
			$('[name="'+AC+'[]"][value="'+DObj.department.code+'"]').prop('checked',true);
		})
	})
}
