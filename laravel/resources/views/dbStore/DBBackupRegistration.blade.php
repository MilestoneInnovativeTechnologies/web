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
                    <label for="DBName" class="col-sm-4 control-label" style="padding-left: 200px">Database Name</label>
                    <div class="col-sm-5">
                        <input type="email" class="form-control" id="inputEmail3" placeholder="Database Name">
                    </div>
                </div>
                <br><br>
                 <div class="form-group">
                    <label for="DBPassword" class="col-sm-4 control-label" style="padding-left: 200px">Database Password</label>
                     <div class="col-sm-5">
                        <input type="email" class="form-control" id="inputEmail3" placeholder="Database Password">
                    </div>
                 </div> <br><br>
                <div class="form-group">
                    <label for="BackupType" class="col-sm-4 control-label" style="padding-left: 200px">Backup Types</label>
                    <div class="col-sm-5">
                        <div class="dropdown">

                                <select class="form-control"  >
                                    <option>--Select--</option>
                                    <option>  7 Days </option>
                                    <option> 30 Days </option>
                                    <option>1 Year </option>
                                </select>

                        </div>
                    </div>
                    </div>
                </div>
            <div class="panel-footer clearfix">
                <input type="submit" name="Register" value="Register" class="btn btn-primary pull-right" >
            </div>
        </div>
    </form>
@endsection