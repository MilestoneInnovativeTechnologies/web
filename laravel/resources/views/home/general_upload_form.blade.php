@php
if(Request()->file){
$file = Request()->file;
$guc = new App\Http\Controllers\GeneralUploadController();
$SPath = $guc->store_file($file,$GUF->code);
$GUF->update(['file' => $SPath, 'size' => Storage::disk($guc->upload_disk)->size($SPath), 'time' => time()]);
$success = true;
}
@endphp
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Milestone Form Upload</title>
<style type="text/css">
	* { margin: auto; padding: 0px; }	
</style>
</head>
<body>
@if(isset($success))
<div style="text-align: center; margin: 90px auto 0px; color: #B5B5B5; font-family: monospace; font-size: 25px;">Your file has been submited..<br>Please close this window</div>
@elseif($GUF->file && $GUF->overwrite == 'N')
<div style="text-align: center; margin: 90px auto 0px; color: #B5B5B5; font-family: monospace; font-size: 25px;">The form requested is already filled with a file and the file is not overwritable.<br>Request to either drop the file or make the form overwritable inorder to upload a file</div>
@else
<div style="text-align: center; margin: 90px auto 0px; color: #B5B5B5; font-family: monospace; font-size: 25px;"><h2>UPLOAD FORM</h2></div>
<div style="width: 440px; border: 2px solid #DDD; min-height: 220px; border-radius: 4px; background-color: #FDFDFD">
	<div style="width: 400px; margin: 10px auto 0px; text-align: center; color: #696969; border: 1px solid #DDD; background-color: #FFF; padding: 10px; font-family: Segoe, Segoe UI, DejaVu Sans, Trebuchet MS, Verdana,' sans-serif'"><h2>{{ $GUF->name }}</h2></div>
	@if($GUF->description)<div style="width: 400px; margin: 10px auto 0px; text-align: center; color: #696969; border: 1px solid #DDD; background-color: #FFF; padding: 10px; font-family: Segoe, Segoe UI, DejaVu Sans, Trebuchet MS, Verdana,' sans-serif'"><h4>{{ $GUF->description }}</h4></div>@endif
	<a href="javascript:BrowseFile()" style="text-decoration: none;"><div id="choose" style="width: 400px; margin: 10px auto 10px; text-align: center; color: #696969; border: 1px solid #DDD; background-color: #FFF; padding: 75px 10px; font-family: Segoe, Segoe UI, DejaVu Sans, Trebuchet MS, Verdana,' sans-serif'">Click here to choose file</div></a>
	<form method="post" enctype="multipart/form-data">{{ csrf_field() }}<input type="file" name="file" style="display: none" onChange="ShowSubmit()"></form>
	<a href="javascript:StartUpload()" style="text-decoration: none;"><div id="submit" style="display: none; width: 400px; margin: 10px auto 10px; text-align: center; color: #696969; font-size: 18px; border: 1px solid #DDD; background-color: #FFF; padding: 10px; font-family: Segoe, Segoe UI, DejaVu Sans, Trebuchet MS, Verdana,' sans-serif'">Submit</div></a>
</div>
<div style="width: 440px; font-size: 12px; text-align: center; margin-top: 5px; color: #757575; font-style: italic" id="log">Choose a file to upload</div>
<script type="text/javascript" src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script type="text/javascript">
	function BrowseFile(){
		$('[name="file"]').trigger('click');
		$('#log').text('select file to upload.')
	}
	function ShowSubmit(){
		$('#log').text('Click on submit to start upload');
		$('#choose').html([$('<h4>').text($('[name="file"]').val().split('\\').pop()),$('<em>').text('Click here to change file.')]);
		$('#submit').slideDown(150)
	}
	function StartUpload(){
		$('#log').text('starting upload. Thanks for your patience!!');
		$('form')[0].submit();
		$('#choose').html([$('<h4>').text('--uploading--'),$('<h3>').text('--')]);
		setInterval(function(){ $('#choose h3').text(({'--':'\\','\\':'|','|':'/','/':'--'})[$('#choose h3').text()]) },100)
	}
</script>
@endif
</body>
</html>