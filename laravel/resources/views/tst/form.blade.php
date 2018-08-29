@extends("tst.page")
@include('BladeFunctions')
@section("content")
<?php
$Fields = ['code','name','country','email','phone','address1','address2','website','state','city','phonecode','currency','privilaged','default'];
$Values = []; $Update = isset($Update);
foreach($Fields as $Field) $Values[$Field] = old($Field,(isset($Data)?((is_array($Data) && array_key_exists($Field,$Data))?$Data[$Field]:(is_object($Data)?(($Data->$Field === NULL)?(($Data[$Field] === NULL)?'':$Data[$Field]):$Data->$Field):'')):''));
?>
<div class="content form">
	<?php $Form =  BSForm((($Update)?Route('tst.update',['partner'	=>	$Values['code']]):Route('tst.store')),($Update?'put':'post')) ?>
	<?php $Panel = stickContent(BSPanel('<strong>'.($Update?'Edit':'New').' Support Team</strong>'.PanelHeadBackButton(Route('tst.index')),NULL,PanelFooterButton($Update?'Update':'Submit')),$Form,'|FORMCONTENT|') ?>
	<?php $Grid = stickContent(BSGrid(2),$Panel,'|PANELBODY|') ?>
	<?php
	$R1C1 = divClass('col-xs-12',divClass('col-xs-8',formGroup('2','code','text','Code',$Values['code'],['attr'=>'required','labelWidth'	=>	4])).divClass('col-xs-4',formGroup('2','privilaged','checkbox','Privilaged',$Values['privilaged'],['checkValue'	=>	'YES'])));
	//$R1C1 .= formGroup('2','code','text','Team Code',$Values['code'],['attr'=>'required']);
	$R1C1 .= divClass('col-xs-12',divClass('col-xs-8',formGroup('2','name','text','Name',$Values['name'],['attr'=>'required','labelWidth'	=>	4])).divClass('col-xs-4',formGroup('2','default','checkbox','Default Team',($Values['default'])?'YES':'NO',['checkValue'	=>	'YES'])));
	//$R1C1 .= formGroup('2','name','text','Team Name',$Values['name'],['attr'=>'required']);
	$R1C1 .= formGroup('2','country','select','Country',$Values['country']/*,['attr'=>'required']*/);
	$R1C1 .= formGroup('2','email','text','Email',$Values['email']/*,['attr'=>'required']*/);
	$R1C1 .= formGroup('2','phone','text','Number',$Values['phone'],['inputGroup'=>'phonecode'/*,'attr'=>'required'*/]);
	$Grid = stickContent($R1C1,$Grid,'|ROW1COL1|');
	$R1C2 = formGroup('2','address1','text','Address Line 1',$Values['address1']);
	$R1C2 .= formGroup('2','address2','text','Address Line 2',$Values['address2']);
	//$R1C2 .= divClass('col col-xs-12',divClass('col-xs-6',formGroup('1','state','select','State',$Values['state'])).divClass('col-xs-6',formGroup('1','city','select','City',$Values['city'])));
	$R1C2 .= formGroup('2','state','select','State',$Values['state']);
	$R1C2 .= formGroup('2','city','select','City',$Values['city']);
	$R1C2 .= formGroup('2','website','text','Website',$Values['website']);
	$R1C2 .= formGroup('2','phonecode','hidden',' ',$Values['phonecode']);
	$R1C2 .= formGroup('2','currency','hidden',' ',$Values['currency']);
	$Grid = stickContent($R1C2,$Grid,'|ROW1COL2|');
	?>
	{!! $Grid !!}
</div>

@endsection
@push('js')
<script type="text/javascript" src="js/support_team.js"></script>
@endpush