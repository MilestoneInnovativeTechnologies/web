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

                <div style="padding-top: 50px" class="form-group {{$errors->has('DatabaseUserName')?'has_error':''  }}">
                    <label for="UserName" class="col-sm-4 control-label" style="padding-left: 200px">Database User Name</label>
                    <div class="col-sm-5">
                        <input type="DatabaseUserName" id="DatabaseUserName" class="form-control" placeholder="Enter Database User Name"  value="{{old('DatabaseUserName')}}">
                        <span class="text-danger">{{$errors->first('DatabaseUserName')}}</span>

                    </div>
                </div>
                <br><br>
                 <br><br>

                </div>
            <div class="panel-footer clearfix">
                <input type="submit" name="Register" value="Package>>" class="btn btn-primary pull-right" >
            </div>
        </div>
    </form>
@endsection