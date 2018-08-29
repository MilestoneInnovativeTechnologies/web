@extends("company.page")
@include("BladeFunctions")
@section("content")

<div class="content"><?php
$Fields = ['name','address1','address2','country','state','city','email','phone','website','phonecode','currency'];
$Values = []; $Update = isset($Update);
foreach($Fields as $Field) $Values[$Field] = old($Field,(isset($Data)?((is_array($Data) && array_key_exists($Field,$Data))?$Data[$Field]:(is_object($Data)?(($Data->$Field === NULL)?(($Data[$Field] === NULL)?'':$Data[$Field]):$Data->$Field):'')):''));

	$FormElements = divClass('col-xs-12',formGroup(1,'name','text','Company Name',$Values['name']));
	$FormElements .= divClass('col-xs-6',formGroup(1,'address1','text','Address Line 1',$Values['address1']));
	$FormElements .= divClass('col-xs-6',formGroup(1,'address2','text','Address Line 2',$Values['address2']));
	$FormElements .= divClass('col-xs-4',formGroup(1,'country','select','Country',$Values['country'],['attr'	=>	'onchange="CountryChanged()"', 'selectOptions'	=>	 \App\Models\Country::get()->map(function($item, $key){ return ['text'	=>	$item->name, 'value'	=>	$item->id, 'attr'	=>	'data-phonecode="'.$item->phonecode.'" data-currency="'.$item->currency.'"']; })->toArray() ]));
	$FormElements .= divClass('col-xs-4',formGroup(1,'state','select','State',$Values['state'],['attr'	=>	'onchange="StateChanged()"',	'selectOptions'	=> $States]));
	$FormElements .= divClass('col-xs-4',formGroup(1,'city','select','City',$Values['city'],['selectOptions'	=>	$Cities]));
	$FormElements .= divClass('col-xs-4',formGroup(1,'email','text','Email',$Values['email']));
	$FormElements .= divClass('col-xs-4',formElement('hidden','phonecode',$Values['phonecode']).formElement('hidden','currency',$Values['currency']).formGroup(1,'phone','text','Phone',$Values['phone'],['inputGroup'	=>	'phonecode']));
	$FormElements .= divClass('col-xs-4',formGroup(1,'website','text','Website',$Values['website']));
	
	$Panel = BSPanel('<strong>Change Details</strong>'.PanelHeadBackButton(Route('company.dashboard')),$FormElements,PanelFooterButton('Update Details'));
	$Form = BSForm(Route('company.address'),'post',false,$Panel);
	$Rows = BSGrid([2,8,2]);
	$Page = stickContent($Form,stickContent('',stickContent('',$Rows,'|ROW1COL3|'),'|ROW1COL1|'),'|ROW1COL2|');
	
	echo $Page;
	
?></div>

@endsection
@push("js")
<script type="text/javascript">
	$(function(){
		SOpt = $('[name="country"] option:selected');
		$('[name="phonecode"]').val(SOpt.attr('data-phonecode')); $('[name="currency"]').val(SOpt.attr('data-currency')); $('.phonecode.input-group-addon').text(SOpt.attr('data-phonecode'));
	})
</script>
@endpush