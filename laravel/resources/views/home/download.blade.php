<!DOCTYPE html>
<html>
<head>
    <title>Download ePlus</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.css" type="text/css">
    <style>
        * { margin: auto; padding: 0px; }
        .flex-container {
            display: flex;
            background-color: #EFEFEF;
            height: 100vh;
        }

        .flex-container > div {
            background-color: #f1f1f1;
            width: 75vw;
        }
    </style>
    <script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript">
        function SendDownloadLink(){
            email = $('[name="email_link"]').val();
            if(!/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,5})+$/.test(email)) return GDEmailError();
            RequestDownloadLink(email);
        }

        function GDEmailError(){
            hint = $('.hint'); eErr = $('.email_error');
            hint.slideUp(100,() => eErr.slideDown(100))
            setTimeout(function(){ hint.slideDown(100,() => eErr.slideUp(100)) },3500);
        }

        function RequestDownloadLink(email){
            $('.form-group.doing').slideUp(() => $('.form-group.done').slideDown(() => $.post('api/sdl',{product:'PRD001',email:email})))
        }

    </script>
</head>
<body>

<div class="flex-container">
    <div>
        <div class="panel panel-default">
            <div class="panel-heading">Download Milestone ePlus</div>
            <div class="panel-body">

                <form class="form-horizontal" method="POST">

                    <div class="form-group">
                        <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                        <div class="col-md-6">
                            <input id="email" type="email" class="form-control" name="email_link" value="" required autofocus>
                            <small class="hint">Download links will be sent to the email address providing.</small>
                            <small class="email_error text-danger" style="display: none">Email Address seems to be invalid.. Please correct and try again</small>
                        </div>
                    </div>

                    <div class="form-group doing">
                        <div class="col-md-8 col-md-offset-4">
                            <a href="{!! route('home') !!}" class="btn btn-default">Cancel</a>
                            <a href="javascript:SendDownloadLink();" class="btn btn-primary">Send download link</a>
                        </div>
                    </div>

                    <div class="form-group done" style="display: none">
                        <div class="col-md-8 col-md-offset-4">
                            <small>The download details are being mailed to the email address provided.. Please check the mail for details!</small><br />
                            <a href="{!! route('home') !!}" class="btn btn-primary">Back to Home Page</a>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>