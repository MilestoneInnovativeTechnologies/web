<?php
function Button($Display, $Link, $Extra = []){
	return '<a href="'.$Link.'" '.ButtonStyles($Extra).'>'.$Display.'</a>';
}
function ButtonDefaultStyles(){
	return [
		'background-color'	=>	'#337ab7',
		'border'	=>	'1px solid #2e6da4',
		'color'	=>	'#FFF',
		'padding'	=>	'5px 15px',
		'text-align'	=>	'center',
		'line-height'	=>	'1.33',
		'border-radius'	=>	'3px',
		'display'	=>	'inline-block',
		'cursor'	=>	'pointer',
		'font-weight'	=>	'500',
		'text-decoration'	=>	'none',
		'margin-bottom'	=>	'3px',
		'font-size'	=>	'13px',
	];
}
function ButtonStyles($Extra){
	$Default = ButtonDefaultStyles();
	$Styles = array_merge($Default, $Extra);
	return RawStyle($Styles);
}
function RawStyle($Styles){
	return 'style="' . implode('; ', array_map(function($prop, $value){ return $prop.': '.$value; },array_keys($Styles),array_values($Styles))) . '"';
}
?>