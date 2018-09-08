@extends("layouts.app")
@section('title', 'MIT :: Test')
@include('BladeFunctions')
@section("content")
    <form method="post" enctype="multipart/form-data" >
        {{ csrf_field() }}


        <br><br><br>
        <div class="panel panel-default">
        <div class="panel-heading"><strong>SMS</strong><a href="" title="Back" class="btn btn-default btn-sm pull-right"><span class="glyphicon glyphicon-arrow-left"></span> Back</a></div>
        <div class="panel-body">

            <div class="form-group clearfix form-horizontal"><label class="control-label col-xs-3 " style="">File</label><div class="col-xs-9"><input type="file" value=""  class="form-control" name="file"></div></div>

        </div>
        <div class="panel-footer clearfix">
            <input type="submit" name="submit" value="Upload" class="btn btn-primary pull-right">
        </div>
        </div>
    </form>

@endsection