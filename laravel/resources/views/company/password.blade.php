@extends("company.page")
@include("BladeFunctions")
@section("content")

<div class="content"><?php
	$Panel = BSPanel('<strong>Change Password</strong>' . PanelHeadBackButton('dashboard'),'|PANELCONTENT|',PanelFooterButton('Change Password'));
	$Form = BSForm(Route('company.password'),'post');
	$Rows = BSGrid([2,8,2]);
	
	$Page = stickContent($Form,stickContent('',stickContent('',$Rows,'|ROW1COL3|'),'|ROW1COL1|'),'|ROW1COL2|');
	$Page = stickContent($Panel,$Page,'|FORMCONTENT|');
	
	
	
	
	$FormElement = formGroup(2,'old_password','password','Old Password');
	$FormElement .= formGroup(2,'password','password','New Password');
	$FormElement .= formGroup(2,'password_confirmation','password','Confirm Password');
	
	$Page = stickContent($FormElement,$Page,'|PANELCONTENT|');
	echo $Page;
?></div>

@endsection