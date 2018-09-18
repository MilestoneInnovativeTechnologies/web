@extends("layouts.app")
@section('title', 'MIT :: dbStore')
@include('BladeFunctions')
@section("content")
    <html>
    <head></head>
    <body>
        <form method="post" enctype="multipart/form-data" >
        {{ csrf_field() }}


        <br><br><br>
        <div class="panel panel-default">

            <div class="panel-heading">
                <strong>Database Backup</strong><a href="" title="Back" class="btn btn-default btn-sm pull-right"><span class="glyphicon glyphicon-arrow-left"></span> Back</a></div>
            <div class="panel-body">
                <div class="form-group clearfix form-horizontal">
                    <label class="control-label col-xs-3 " style="padding-left: 20%">File Count</label>
                    <div class="form-inline"> <input type="text" style="padding-right: 10%" class="form-control" ></div>
                </div><br>
                <div class="form-group clearfix form-horizontal" id="UploadControl">
                    <label class="control-label col-xs-3 " style="">File</label>
                    <div class="col-xs-9">
                        <input name= "filesToUpload[]" id="filesToUpload" type="file" multiple  class="form-control" />
                    </div></div>

            </div>
            <div class="panel-footer clearfix">
                <input type="submit" name="submit" value="Upload" class="btn btn-primary pull-right">
                <button type="button" name="add" id="add" class="btn btn-primary pull-right">Add More</button>

            </div>
        </div>
    </form>
        <script type="text/javascript">
         //  $(document).ready(function () {
            //   var i=1;
              // var html=" <label class=\"control-label col-xs-3 \" style=\"\">File</label>\n" +
                //   "<div class=\"col-xs-9\">\n" +
                //   "<input name= \"filesToUpload[]\" id=\"filesToUpload\" type=\"file\" multiple=\"true\"  class=\"form-control\" />\n" +
                //   "</div>"
             //  $("#add").click(function () {
           //        $("#UploadControl").append(html);
               //    i++;
            //   })
              // })


        </script>
     </body>
    </html>


@endsection