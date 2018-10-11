@extends("layouts.app")
@section('title', 'MIT :: dbStore')
@include('BladeFunctions')
@section("content")

    <html>
    <head>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
       <!-- <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" />  -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>


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
    <script>
        function bigDiv(x) {
          x.style.height="100px";
          x.style.width="100px";
        }
        function NormalDiv(x)
        {
            x.style.height="20px";
            x.style.width="20px";
        }
    </script>

    <form method="post" enctype="multipart/ form-data" data-toggle="validation" role="form" action="Store">
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
                                        <span id="error_DatabaseName" class="text-danger"></span>
                                     <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer clearfix">

                                <button type="button" name="Register" id="btnPackage" class="btn btn-primary pull-centre" style="margin-left: 500px">Package</button>
                            </div>
                        </div>



                    <div class="tab-pane fade" id="Package_details">
                        <div class="panel-body">
                            <div class="form-group">

                                <div class="dropdown" style="margin-left: 420px">
                                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Select a Package
                                        <span class="caret"></span></button>
                                    <ul class="dropdown-menu">
                                        <li><a href="#">1 year</a></li>
                                        <li><a href="#">6 Months</a></li>
                                        <li><a href="#">3 Months</a></li>
                                    </ul>
                                </div>
                                <div style="background-color: #32383e; height: 20px;width: 20%;" onmouseover="bigDiv(this)" onmouseout="NormalDiv(this)"></div>

                            </div>


                         </div>
                        <div  class="panel-footer clearfix" style="margin-top: 70px;  " >
                            <table>
                                <tr>
                                    <td><button type="button" name="btnPrevious" id="btnPrevious" class="btn btn-primary pull-centre" style="margin-left: 400px">Previous</button></div></td>
                                    <td><button type="button" name="btnRegister" id="btnRegister" class="btn btn-primary pull-centre" style="margin-left: 30px">Register</button></div></td>
                                </tr>
                            </table>


                    </div>

        </div>
            </div></div>
    </form>
    </html>

    <script>
        $(document).ready(function () {

            $('#btnPackage').click(
                function(){

                    var error_DatabaseName='';

                    if($.trim($('#DBUserName').val()).length == 0)
                   {
                        error_DatabaseName="Database username is required";
                        $('#error_DatabaseName').text(error_DatabaseName);
                        $('#DBUserName').addClass('has_error');
                   }

                   else
                   {
                       error_DatabaseName='';
                       $('#error_DatabaseName').text(error_DatabaseName);
                       $('#DBUserName').removeClass('has_error');

                   }
                   if(error_DatabaseName!='')
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
                } );

            $('#btnPrevious').click(
                function(){
                    $('#list_PackageDetails').removeClass('active active_tab1');
                    $('#list_PackageDetails').removeAttr('href data-toggle');
                    $('#Package_details').removeClass('active in');
                    $('#list_PackageDetails').addClass('inactive_tab1');
                    $('#list_DatabaseDetails').removeClass('inactive_tab1');
                    $('#list_DatabaseDetails').addClass('active_tab1 active');
                    $('#list_DatabaseDetails').attr('href', '#Database_details');
                    $('#list_DatabaseDetails').attr('data-toggle', 'tab');
                    $('#Database_details').addClass('active in');
                })
        });
    </script>
@endsection