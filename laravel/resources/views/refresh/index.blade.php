<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Refresh</title>
<base href="/">
@include('inc.favicon')
@include('BladeFunctions')
<script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="js/bootstrap.js"></script>
<link type="text/css" rel="stylesheet" href="css/bootstrap.css">
<script type="text/javascript">
	$(function(){
		$('[name="country"]').prepend($('<option>').text('Select Country').attr('value','')).val('')
	})
	function CountryChanged(){
		country = $('[name="country"]').val(); if(country == '') return;
		$.getJSON('api/'+country+'/states',function(J){
			st = $('[name="state"]').html($('<option>').text('Select State'));
			$.each(J,function(i,Ary){ st.append($('<option value="'+Ary[0]+'">').text(Ary[1])); })
		});
		C = $('option[value="'+country+'"]',$('[name="country"]'));
		pc = C.attr('data-phonecode'); c = C.attr('data-currency');
		$('[name="phonecode"]').val(pc);
		$('[name="currency"]').val(c);
		$('.input-group-addon.phonecode').text(pc);
	}
	function StateChanged(){
		state = $('[name="state"]').val();
		$.getJSON('api/'+state+'/cities',function(JS){
			ct = $('[name="city"]').html($('<option>').text('Select City'));
			$.each(JS,function(i,Ary){ ct.append($('<option value="'+Ary[0]+'">').text(Ary[1])); })
		});
	}
</script>
</head>

<body><div class="container" style="margin-top: 60px;"><?php

	$Row = BSGrid([2,8, 2]);
	$Row = stickContent(' ',stickContent(' ',$Row,'|ROW1COL3|'),'|ROW1COL1|');
	$FormElements = divClass('col-xs-12',formGroup(1,'name','text','Company Name'));
	$FormElements .= divClass('col-xs-6',formGroup(1,'address1','text','Address Line 1'));
	$FormElements .= divClass('col-xs-6',formGroup(1,'address2','text','Address Line 2'));
	$FormElements .= divClass('col-xs-4',formGroup(1,'country','select','Country',['attr'	=>	'onchange="CountryChanged()"', 'selectOptions'	=>	 \App\Models\Country::get()->map(function($item, $key){ return ['text'	=>	$item->name, 'value'	=>	$item->id, 'attr'	=>	'data-phonecode="'.$item->phonecode.'" data-currency="'.$item->currency.'"']; })->toArray() ]));
	$FormElements .= divClass('col-xs-4',formGroup(1,'state','select','State',['attr'	=>	'onchange="StateChanged()"']));
	$FormElements .= divClass('col-xs-4',formGroup(1,'city','select','City'));
	$FormElements .= divClass('col-xs-4',formGroup(1,'email','text','Email'));
	$FormElements .= divClass('col-xs-4',formElement('hidden','phonecode').formElement('hidden','currency').formGroup(1,'phone','text','Phone',['inputGroup'	=>	'phonecode']));
	$FormElements .= divClass('col-xs-4',formGroup(1,'website','text','Website'));

	$ExtraData = '</div><hr /><div class="panel-body clearfix">'. eleClass('h4','','Database Backups');
	//$ExtraData .= divClass('col-xs-12 text-danger text-center','Functions and Triggers should create manually.<br>You are requested to do that first before submitting this form.');
	$ExtraData .= divClass('col-xs-12',formGroup(2,'structure','file','Structure Sql File'));
	$ExtraData .= divClass('col-xs-12',formGroup(2,'trigger','file','Triggers Sql File'));
	//$ExtraData .= divClass('col-xs-12',formGroup(2,'function','file','Functions Sql File'));
	$ExtraData .= divClass('col-xs-12',formGroup(2,'data','file','Data Sql File'));

	$Page = stickContent(BSForm('refresh','post',true),$Row,'|ROW1COL2|');
	$Panel = BSPanel('<strong>Enter Company Details</strong>','|PANELCONTENT|',PanelFooterButton('Proceed'));
	$Page = stickContent($Panel,$Page,'|FORMCONTENT|');
	$Page = stickContent($FormElements.$ExtraData,$Page,'|PANELCONTENT|');
	echo $Page;
	
?></div>
</body>
</html>