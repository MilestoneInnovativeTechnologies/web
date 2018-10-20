@extends("layouts.app")
@section('title', 'MIT :: dbStore')
@include('BladeFunctions')
@section("content")

    <form method="post" enctype="multipart/ form-data" data-toggle="validation" role="form"  onSubmit="return ValidateBaseName()">
        {{ csrf_field() }}


        <br><br><br>

        <div class="panel panel-default">
            <div class="panel-heading"><strong>Database Backup Registration</strong></div>
            <div class="panel-body">
                <div class="row">

                    <div style="border-right:1px solid #CECECE" class="col col-md-6 left_section ">
                        <div class="form-group clearfix form-horizontal"><label class="control-label col-xs-3 " style="">DBUser Name</label><div class="col-xs-8"><input type="text" id="DBUsername" value="" required value="{{ old('DBUsername') }}" class="form-control" name="DBUsername"></div></div>
                        <div class="form-group clearfix form-horizontal"><label class="control-label col-xs-3 " style="">Password</label><div class="col-xs-8"><input type="text" value="" required="" class="form-control" name="Password"></div></div>
                        <div class="form-group clearfix form-horizontal"><label class="control-label col-xs-3 " style="">DB Port</label><div class="col-xs-8"><input type="text" value="" required="" class="form-control" name="DBPort"></div></div>

                    </div>
                    <div class="col col-md-6" >
                        <div class="form-group clearfix form-horizontal"><label class="control-label col-xs-2 " >Package</label><div class="col-xs-5">
                                <select   required=""  class="form-control" name="Package" >
                                    <option value="1">Package1</option>
                                    <option value="2">Package2</option>
                                </select>
                            </div></div>
                    </div>
                </div>
            </div>
            <div class="panel-footer clearfix">

                <button type="submit" name="btnRegister" id="btnRegister" class="btn btn-primary pull-right" >Register</button>
            </div>
        </div>

    </form>



@endsection