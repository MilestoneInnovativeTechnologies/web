@extends("layouts.app")
@section('title', 'MIT :: dbStore')
@include('BladeFunctions')
@section("content")

    <html>
    <head>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" />

    </head>

    <style>
     .box{
         width:800px;
         margin:0 auto;
     }
        .active_tab1{
            background-color: #fff;
            color: #333;
            font-weight: 600    ;
        }
        .inactive_tab1{
            background-color: #f5f5f5;
            color:#333;
            cursor: not-allowed;
        }
        .has-error{
            border-color: #cc0000;
            background-color: #ffff99;
        }

    </style>

    <form method="post" enctype="multipart/ form-data" data-toggle="validation" role="form">
        {{ csrf_field() }}


        <br><br><br>
        <div class="panel panel-default">
            <div class="panel-heading"><strong>Database Backup Registration</strong></div>
            <div class="panel-body">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active-tab1" style="border:1px solid #ccc" id="list_DatabaseDetails">Database Details</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link inactive-tab1" style="border:1px solid #ccc" id="list_PackageDetails">Package Details</a>
                    </li>
                </ul>
                <div class="tab-content" style="margin-top:16px;">


                        <div class="tab-pane active" id="Database_details">
                             <div class="panel-body">
                                <div class="form-group">
                                <label class="col-sm-4 control-label" style="padding-left: 200px" for="inputName">Database User Name</label>
                                    <div class="col-sm-5">
                                     <input class="form-control" data-error="Please enter the Database User name." id="DBUserName" placeholder="DBUserName"  type="text" required name="DBUserName"/>
                                     <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                            </div>
                        </div>


                    <div class="tab-pane fade" id="Package_details">
                        <div class="panel-body">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label" style="padding-left: 200px" for="inputName">Package</label>
                                    <div class="col-sm-5">
                                    <input type="text" name="first_name" id="first_name" class="form-control" />
                                    <span id="error_first_name" class="text-danger"></span>
                                    </div>
                                </div>
                         </div>
                    </div>
            <div class="panel-footer clearfix">
                <input type="submit" name="Register" value="Package" id="Package" class="btn btn-primary pull-centre" style="margin-left: 500px">

            </div>
        </div>
            </div></div>
    </form>
    </html>


    <script>
        $(document).ready(function () {
            $('#Package').click(
                function(){

                    if(('#DBUserName').valueOf()=='')
                    {
                        return false;
                    }
                    else {
                        $('#list_DatabaseDetails').removeClass('active active_tab1');
                        $('#list_DatabaseDetails').removeAttr('href data-toggle');
                        $('#Database_details').removeClass('active');
                        $('#list_DatabaseDetails').addClass('inactive_tab1');
                        $('#list_PackageDetails').removeClass('inactive_tab1');
                        $('#list_PackageDetails').addClass('active_tab1 active');
                        $('#list_PackageDetails').attr('href', '#Package_details');
                        $('#list_PackageDetails').attr('data-toggle', 'tab');
                        $('#Package_details').addClass('active in');
                    }
                }
            )

        });
    </script>
@endsection