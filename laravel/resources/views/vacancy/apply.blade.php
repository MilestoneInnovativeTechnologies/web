@php
$Vacancy = \App\Models\Vacancy::with("spec")->find($code);
$Vacancy->increment('views')
//dd($Vacancy->toArray());
@endphp<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Milestone Innovative Technologies</title>
    <meta property="og:title" content="{{ $Vacancy->title }} vacancy on Milestone IT - Apply Now">
    <meta property="og:description" content="{{ substr($Vacancy->description,0,240) }}">
    <meta property="og:image" content="http://milestoneit.net/images/icon_large.png" />
    <meta property="og:site_name" content="Milestone Innovative Technologies" />
    <meta property="og:url" content="{{ route('vacancy.apply',$Vacancy->code) }}" />
    <base href="/">
    @include('inc/favicon')
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-116616304-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'UA-116616304-1');
    </script>
    <script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <script type="text/javascript" src="js/home.js"></script>
    <link type="text/css" rel="stylesheet" href="css/bootstrap.css">
    <link type="text/css" rel="stylesheet" href="css/common.css">
    <style>
        br { content:"A" !important; display: block !important; margin-bottom: 15px; }
    </style>
</head>

<body>
<nav class="navbar navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <a class="navbar-brand" href="javascript:goHome()"><div class="logo visible-lg-block"></div><div class="logo visible-md-block"></div><div class="logo visible-sm-block"></div><div class="logo visible-xs-block"></div></a>
            <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbarCollapse">
                <span class="sr-only"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>
        <div class="navbar-collapse collapse" id="navbarCollapse">
            <ul class="nav navbar-nav navbar-right">
                <li><a href="/home">Home</a></li>
                <li><a href="/home">Products</a></li>
                <li><a href="/home">Features</a></li>
                <li><a href="/home">Contact</a></li>
                <li><a href="/vacancy">Vacancies</a></li>
            </ul>
        </div>
    </div>
</nav>


<div class="page_contents">
    <div class="page vacancies products" style="background-color: #EBEBEB">
        <div class="container">
            <div style="height: 150px;">&nbsp;</div>
            @if($Vacancy->live  == "0" || $Vacancy->live  === 0)
                <div class="alert alert-danger font-weight-bold">
                    This vacancy is not available right now!
                </div>
            @endif
            @if(session()->has('info'))
                <div class="alert alert-success">
                    Thank you, {{ session()->get('applicant')->name }}. Your application has been registered successfully.
                </div>
            @endif
            <div class="row">
                <div class="col-md-7">
                    <div class="panel panel-default">
                        <div class="panel-heading">{{ $Vacancy->title }}</div>
                        <div class="panel-body">
                            <p>{!! nl2br($Vacancy->description) !!}</p>
                            @if($Vacancy->spec && $Vacancy->spec->isNotEmpty())
                            <div class="table-responsive"><table class="table table-condensed"><tbody>
                                    @foreach($Vacancy->spec as $spec)
                                        <tr><th>{{ $spec->title }}</th><th>:</th><td>{!! nl2br($spec->detail) !!}</td></tr>
                                        @endforeach
                                    </tbody></table></div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-5" style="padding-left: 0px">
                    <form method="post" enctype="multipart/form-data">{{ csrf_field() }}
                        <div class="panel panel-default">
                            <div class="panel-heading">Apply Now</div>
                            <div class="panel-body">
                                <div class="form-group col-xs-6">
                                    <label>Name</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>
                                <div class="form-group col-xs-6">
                                    <label>Phone</label>
                                    <input type="text" name="phone" class="form-control" required>
                                </div>
                                <div class="form-group col-xs-6">
                                    <label>Email</label>
                                    <input type="text" name="email" class="form-control" required>
                                </div>
                                <div class="form-group col-xs-6">
                                    <label>Resume</label>
                                    <input type="file" name="resume" class="form-control" required>
                                </div>
                                <div class="form-group col-xs-12">
                                    <label>Message to HR Department (if any)</label>
                                    <textarea name="message" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="panel-footer clearfix">
                                <input type="submit" value="Apply Now" class="btn btn-primary pull-right">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @include("home.section_contact_wo_map")
</div>




<div id="loginModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content"><form method="post" action="{{ Route('login') }}" class="form-horizontal">{{ csrf_field() }}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Login</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group clearfix">
                        <div class="col-xs-12">
                            <label class="control-label col-xs-4">Email Address:</label>
                            <div class="col col-xs-8">
                                <input type="text" name="email" value="" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="form-group clearfix">
                        <div class="col-xs-12">
                            <label class="control-label col-xs-4">Password:</label>
                            <div class="col-xs-8">
                                <input type="password" name="password" value="" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <input type="submit" class="btn btn-primary" value="Login">
                </div>
            </form></div>
    </div>
</div>

</html>
