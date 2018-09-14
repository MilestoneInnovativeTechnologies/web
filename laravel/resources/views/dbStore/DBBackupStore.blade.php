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
                    <div class="col-xs-9"><input type="file" value=""  class="form-control" name="file"></div></div>

            </div>
            <div class="panel-footer clearfix">
                <input type="submit" name="submit" value="Upload" class="btn btn-primary pull-right">
                <a href="" id="add">Add</a>

            </div>
        </div>
    </form>
        <script type="text/javascript">
           $(document).ready(function () {
               var html=" <label class=\"control-label col-xs-3 \" style=\"\">File</label>\n" +
                   "                    <div class=\"col-xs-9\"><input type=\"file\" value=\"\"  class=\"form-control\" name=\"file\"></div></div>"
               $("#add").click(function ( {

               }) {
                   $("#UploadControl").append(html);

               })
           })
        </script>
     </body>
    </html>


@endsection