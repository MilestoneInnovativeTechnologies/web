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
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="panel-heading">
                <strong>Database Backup</strong><a href="" title="Back" class="btn btn-default btn-sm pull-right"><span class="glyphicon glyphicon-arrow-left"></span> Back</a></div>
            <div class="panel-body">
                <div class="form-group clearfix form-horizontal">
                    <div class="form-inline"> <label class="control-label col-xs-3 " style="padding-left: 20%">File Count</label>
                     <input id="Count" type="text" class="form-control" ></div>
                </div><br>
               <div class="input-group control-group increment" >
                  <input type="file" name="filename[]" class="form-control" ">
                    <div class="input-group-btn">
                        <button class="btn btn-success" type="button"><i class="glyphicon glyphicon-plus"></i>Add</button>
                    </div>
                </div>
                <div class="clone hide">
                    <div class="control-group input-group" style="margin-top:10px">
                        <input type="file" name="filename[]" class="form-control">
                        <div class="input-group-btn">
                            <button class="btn btn-danger" type="button"><i class="glyphicon glyphicon-remove"></i> Remove</button>
                        </div>
                    </div>
                </div>

            </div>
            <div class="panel-footer clearfix">
                <input type="submit" name="submit" value="Upload" class="btn btn-primary pull-right">
                <p></p><l></l>
            </div>
        </div>

    </form>
        <script type="text/javascript">
            // To read the number entered in the text box
            var Limit;
            var i=0;
            var Count=0;
            $(document).ready(function() {
                $( '#Count' )
                    .keyup(function() {

                        Limit = $( this ).val();
                        $("p").text(Limit);
                    })
                    .keyup();
                // Adding controls dynamically

                    Limit = $('#Count').val();


                    $(".btn-success").click(function () {
                        if (Limit <= 1) {
                            $('#Add').removeAttribute('enabled');
                            Limit = $('#Count').val();
                        }
                        var html = $(".clone").html();
                        $(".increment").after(html);
                        Count++;
                        Limit--;
                        $("l").text(Count);

                    });


                // Removes controls using a Remove button
                $("body").on("click",".btn-danger",function(){
                    $(this).parents(".control-group").remove();
                });

            });
        </script>

    </body>
    </html>


@endsection