@extends("layouts.app")
@section('title', 'MIT :: dbStore')
@include('BladeFunctions')
@section("content")

    <html>
    <head>
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet"></link>
        <script src="https://code.jquery.com/jquery-1.12.4.js">
        </script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js">
        </script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/1000hz-bootstrap-validator/0.11.5/validator.min.js">
        </script>
    </head>


    <form method="post" enctype="multipart/form-data" data-toggle="validation" role="form">
        {{ csrf_field() }}


        <br><br><br>
        <div class="panel panel-default">
            <div class="panel-heading"><strong>Database Backup Registration</strong></div>
            <div class="panel-body">

                <div class="form-group">
                    <label class="col-sm-4 control-label" style="padding-left: 200px" for="inputName">Database User Name</label>
                    <div class="col-sm-5">
                    <input class="form-control" data-error="Please enter the Database User name." id="DBUserName" placeholder="DBUserName"  type="text" required />
                    <div class="help-block with-errors"></div>
                    </div>
                </div>




                </div>
            <div class="panel-footer clearfix">
                <input type="submit" name="Register" value="Package>>" class="btn btn-primary pull-right" >

            </div>
        </div>
    </form>
    </html>
@endsection