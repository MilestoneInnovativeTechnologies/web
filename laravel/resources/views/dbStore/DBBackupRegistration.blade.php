@extends("layouts.app")
@section('title', 'MIT :: dbStore')
@include('BladeFunctions')
@section("content")





    <form method="post" enctype="multipart/form-data" >
        {{ csrf_field() }}


        <br><br><br>
        <div class="panel panel-default">
            <div class="panel-heading"><strong>Database Backup Registration</strong></div>
            <div class="panel-body">

                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label" style="padding-left: 200px">Name</label>
                    <div class="col-sm-5">
                        <input type="email" class="form-control" id="inputEmail3" placeholder="Name">
                    </div>
                </div>
                <br><br>
                    <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label" style="padding-left: 200px">Name</label>
                    <div class="col-sm-5">
                        <input type="email" class="form-control" id="inputEmail3" placeholder="Name">
                    </div>
                    </div>


            </div>
            <div class="panel-footer clearfix">
                <input type="submit" name="submit" value="Upload" class="btn btn-primary pull-right">
            </div>
        </div>
    </form>
@endsection