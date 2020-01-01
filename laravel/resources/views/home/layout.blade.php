<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield("title")</title>
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
<link type="text/css" rel="stylesheet" href="css/bootstrap.css">
<link type="text/css" rel="stylesheet" href="css/common.css">
@stack("css")
@stack("js")
</head>

<body>
	@include("home.nav")
	@yield('content')
    <script type="text/javascript">
        var $zoho=$zoho || {};$zoho.salesiq = $zoho.salesiq ||
            {widgetcode:"dc5ef62c96d607a9f0f0a9ab33a8c109176eeef3687ddf182efe474fbdda2fbd7d58a2f3813c0ccb343f6b76c3e18078", values:{},ready:function(){}};
        var d=document;s=d.createElement("script");s.type="text/javascript";s.id="zsiqscript";s.defer=true;
        s.src="https://salesiq.zoho.in/widget";t=d.getElementsByTagName("script")[0];t.parentNode.insertBefore(s,t);d.write("<div id='zsiqwidget'></div>");
    </script>
</body>
</html>