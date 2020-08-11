<?php

function formGroup($form, $name, $type, $label, $value = '', $extra = []){
	//1 = normal, 2 = horizontal
	if(empty($extra) && is_array($value)) { $extra = $value; $value = ''; }
	$extra = GetFGExtra($extra); $Data = '';
	if($extra['inputGroup']) $Data = eleClass('span',('input-group-addon '.$extra['inputGroup']),'');
	$Data .= formElement($type, $name, $value, $extra);
	if($extra['inputGroup']) $Data = divClass('input-group',$Data);
	if($type == 'checkbox') return divClass('checkbox clearfix',GetHTMLElement('label','',$Data . ' ' . $label));
	if($type == 'radio') return divClass('radio clearfix',GetHTMLElement('label','',$Data . ' ' . $label));
	$Data = ($form == 2) ? GetHTMLElement('div','class="col-xs-'.(12-$extra['labelWidth']).'"',$Data) : $Data;
	$Data = (GetHTMLElement('label','class="control-label'.(($form == 2)?(' col-xs-'.$extra['labelWidth']):'').(' '.$extra['labelClass']).'"'.(' style="'.$extra['labelStyle'].'"'),$label)) . $Data;
	return $Data = GetHTMLElement('div','class="form-group clearfix'.(($form == 2)?' form-horizontal':'').'"',$Data);
}

function formElement($type = 'text', $name = 'name', $value = '', $extra = []){
	$Data = ''; $A = ' ';
	$A .= (array_key_exists('attr',$extra) && $extra['attr'] != '') ? ($extra['attr']) : ('');
	$A .= ' class="'.((array_key_exists('class',$extra) && $extra['class'] != '')?($extra['class']):(($type == 'checkbox' || $type == 'radio')?'':'form-control')).'"';
	$A .= (array_key_exists('style',$extra) && $extra['style'] != '') ? (' style="'.$extra['style'].'"') : ('');
	$A .= ' name="'.$name.'"';
	switch($type){
		case 'textarea':
			$Data = GetHTMLElement('textarea',$A,($value)?:'');
			break;
		case 'checkbox':
			$A .= ' type="checkbox"';
			if($extra['checkValue'] != '') $A .= ' value="'.$extra['checkValue'].'"';
			if($extra['checkValue'] == $value) $A .= ' checked';
			$Data = GetHTMLElement('input',$A);
			break;
		case 'radio':
			$A .= ' type="radio"';
			if($extra['radioValue'] != '') $A .= ' value="'.$extra['radioValue'].'"';
			if($extra['radioValue'] == $value) $A .= ' checked';
			$Data = GetHTMLElement('input',$A);
			break;
		case 'select':
			$Options = selectOptions($extra['selectOptions'], $value);
			if($value) $A .= ' data-pre-value="'.$value.'"';
			$Data = GetHTMLElement('select',$A,$Options);
			break;
		case 'text': case 'input':
			$Data = GetHTMLElement('input',('type="'.$type.'" value="'.$value.'"'.$A),NULL);
			break;
		case 'static':
			$Data = GetHTMLElement('p',str_ireplace('form-control','form-control-static',$A),$value);
			break;
		default:
			$Data = GetHTMLElement('input',('type="'.$type.'" value="'.$value.'"'.$A),NULL);
			break;
	}
	return $Data;
}

function glyLink($href, $title, $icon, $extra = []){
	$Attr = 'href="'.$href.'" title="'.$title.'"';
	$Attr .= (isset($extra['class'])) ? ' class="'.$extra['class'].'"' : '';
	$Attr .= (isset($extra['attr'])) ? (' ' . $extra['attr']) : '';
	$Text = (isset($extra['text'])) ? $extra['text'] : '';
	return GetHTMLElement('a',$Attr,(GetHTMLElement('span','class="glyphicon glyphicon-'.$icon.'"','').$Text));
	//return '<a href="'.$href.'" class="'.$Class.'" title="'.$title.'"'.$Attr.'><span class="glyphicon glyphicon-'.$icon.'"></span>'.($Text).'</a>';
}

function selectOptions($optAry,$default = NULL){
	if($optAry == '') return '';
	$Options = []; $A = ''; $T = '';
	foreach($optAry as $Key => $VAry){
	    if(is_null($VAry)) continue;
		if(is_string($VAry)) {
			$T = $VAry;
			$V = is_int($Key)?$T:$Key;
			$A = 'value="'.$V.'"' . (($V == $default)?' selected="selected"':'');

		} elseif(array_key_exists('text',$VAry) && array_key_exists('value',$VAry)){
			$T = array_key_exists('text',$VAry) ? $VAry['text'] : '';
			$A = array_key_exists('attr',$VAry) ? $VAry['attr'] : '';
			if(array_key_exists('value',$VAry)) $A .= ' value="'.$VAry['value'].'"';
			if(array_key_exists('class',$VAry)) $A .= ' class="'.$VAry['class'].'"';
			if($VAry['value'] == $default) $A .= ' selected="selected"';
		}
		$Options[] = GetHTMLElement('option',$A,$T);
	}
	return implode("",$Options);
}

//Form Group
function GetFGExtra($extra){
	$Default = ['labelWidth'=>3,'class'=>'','attr'=>'','selectOptions'=>'','style'=>'','inputGroup'=>false,'checkValue'=>'','radioValue'=>'','labelClass'=>'','labelStyle'=>''];
	$newExtra = [];
	foreach($Default as $N => $V)
		$newExtra[$N] = array_key_exists($N,$extra) ? $extra[$N] : $V;
	return $newExtra;
}

function GetHTMLElement($Tag,$Attr = NULL,$Text = NULL){
	$Data = '<'.$Tag.(($Attr === NULL)?'':(' '.$Attr)).'>';
	if($Text === NULL) return $Data;
	return $Data .= $Text.'</'.$Tag.'>';
}

function divClass($Classes,$Content = '|DIVCLASSCONTENT|',$Attributes = []){
	$Attributes = getHtmlAttributes($Attributes);
	$Attributes .= ' class="' . $Classes . '"';
	return GetHTMLElement('div',$Attributes,$Content);
}

function eleClass($Ele, $Classes, $Content = '|ELECLASSCONTENT|',$Attributes = []){
	$Attributes = getHtmlAttributes($Attributes);
	$Attributes .= ' class="' . $Classes . '"';
	return GetHTMLElement($Ele,$Attributes,$Content);
}

function getHtmlAttributes($Attributes){
	if(!$Attributes || empty($Attributes)) return '';
	if(is_string($Attributes)) return $Attributes;
	if(is_array($Attributes)){
		$Attrs = '';
		foreach($Attributes as $Key => $Value){
			if(is_int($Key)) $Attrs .= ' ' . $Value;
			else $Attrs .= ' ' . $Key . '="'.$Value.'"';
		}
		return($Attrs);
	}
}

function BSGrid(...$Rows){
	$Rows = is_array($Rows)?$Rows:(array) $Rows;
	$Total = 12; $Types = ['md'];
	$GridContents = [];
	foreach($Rows as $Row => $Columns){
		$Columns = is_array($Columns)?$Columns:array_fill(0,$Columns,intval($Total/$Columns));
		$RowContents = [];
		foreach($Columns as $Column => $Width){
			$Class = 'col ' . implode(' ',array_map(function($Type) use($Width){ return implode('-',['col',$Type,$Width]); },$Types));
			$RowContents[] = divClass($Class,('|ROW'.($Row+1).'COL'.($Column+1).'|'));
		}
		$GridContents[] = divClass('row',(implode('',$RowContents)));
	}
	return implode('',$GridContents);
}

function stickContent($R,$T,$S = '|DIVCLASSCONTENT|'){
	return str_replace($S,$R,$T);
}

function BSPanel($Heading = NULL,$Content = NULL,$Footer = NULL){
	$Head = divClass('panel-heading clearfix',(($Heading === NULL)?GetHTMLElement('strong','','|PANELHEADING|'):$Heading));
	$Body = divClass('panel-body clearfix',(($Content === NULL)?'|PANELBODY|':$Content));
	return divClass('panel panel-default',($Head.$Body.(($Footer === NULL)?'':(divClass('panel-footer clearfix',$Footer)))));
}

function BSTable($TblTypes,$THead = '|THEAD|',$TBody = '|TBODY|',$Extra = ['theadAttr'=>NULL,'tbodyAttr'=>NULL]){
	$TB = GetHTMLElement('tbody',$Extra['tbodyAttr'],$TBody);
	$TH = GetHTMLElement('thead',$Extra['theadAttr'],$THead);
	return divClass('table-responsive',eleClass('table','table ' . implode(' ',array_map(function($Type){ return 'table-'.$Type; }, (array) $TblTypes)),($TH.$TB)));
}

function BSForm($Action = '', $Method = NULL, $Encrypt = NULL, $Content = '|FORMCONTENT|'){
	$Attr[] = 'action="'.($Action).'"';
	$Attr[] = 'method="'.(($Method != 'get')?'post':$Method).'"';
	if($Encrypt) $Attr[] = 'enctype="'.(($Encrypt === true)?'multipart/form-data':$Encrypt).'"';
	$Text = (($Method !== NULL && $Method != 'get' && $Method != 'post')?method_field($Method):'') . ( csrf_field() . $Content);
	return GetHTMLElement('form',implode(' ',$Attr),$Text);
}

function PanelHeadButton($Action,$Label,$Icon='plus',$Type='info',$Size='sm'){
	return glyLink($Action,$Label,$Icon, ['text'=>(' '.$Label), 'class'=>'no-print btn btn-'.$Type.' btn-'.$Size.' pull-right']);
}

function PanelHeadBackButton($Action,$Label = 'Back'){
	return PanelHeadButton($Action,$Label,'arrow-left','default','sm');
}

function PanelHeadAddButton($Action,$Label){
	return PanelHeadButton($Action,$Label,'add','info','sm');
}

function PanelFooterButton($Action,$type = 'info'){
	return formElement('submit','submit',$Action,['class'=>"pull-right btn btn-$type"]);
}












?>
