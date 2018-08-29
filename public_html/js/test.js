
$(function(){
	$("[name='file']").on("change",UPLOAD)
})

function UPLOAD(event){
	files = event.target.files;
	fd = new FormData();
	fd.append("file",$("[name='file']")[0].files[0]);
	$.ajax({
		url: 'api/L2D',
		type:'post',
		data:fd,
		cache:false,
		dataType: 'json',
		processData: false,
		contentType: false,
		success:function(d){
			console.log(d);
		}
	});
}
