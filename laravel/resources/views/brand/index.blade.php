<!DOCTYPE html>
<html lang="en">@php
$B = $Data->Branding;
//dd($Data->toArray());
$Products = $B->Products->groupBy(function($Item, $Key){ return $Item->Product->code; });
//dd($Products->toArray());
$MyEditions = [];
@endphp
	
  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="{{ $B->about }}">
    <meta name="author" content="{{ $B->name }}">

    <title></title>

    <!-- Bootstrap core CSS -->
    <link href="/css/brand/bootstrap.min.css" rel="stylesheet">

    <!-- Custom fonts for this template -->
    <link href="https://fonts.googleapis.com/css?family=Saira+Extra+Condensed:100,200,300,400,500,600,700,800,900" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i" rel="stylesheet">
    <link href="/css/brand/font-awesome.min.css" rel="stylesheet">
    <link href="/css/brand/devicons.min.css" rel="stylesheet">
    <link href="/css/brand/simple-line-icons.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="/css/brand/brand.min.css" rel="stylesheet">
    
    <style type="text/css">
			{!! str_ireplace('#bd5d38','rgba('.($B->color_scheme).',1)','.list-icons .list-inline-item i:hover{color:#bd5d38}.list-social-icons a:hover{color:#bd5d38}.bg-primary{background-color:#bd5d38!important}.text-primary{color:#bd5d38!important}a{color:#bd5d38}') !!}
		</style>

  </head>

  <body id="page-top">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top" id="sideNav">
      <a class="navbar-brand js-scroll-trigger" href="#page-top">
        <span class="d-block d-lg-none">{{ $B->name }}</span>
        <span class="d-none d-lg-block">
          <img class="img-fluid img-profile rounded-circle mx-auto mb-2" src="{{ \Storage::disk('branding')->url($B->icon) }}" alt="">
        </span>
      </a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link js-scroll-trigger" href="#about">About</a>
          </li>@if($Data->type == 'company')
          @if($Products->isNotEmpty()) @foreach($Products as $PRCode => $PRArray)
					<li class="nav-item">
            <a class="nav-link js-scroll-trigger" href="#{{ $PRCode }}">{{ $PRArray[0]->Product->name }}</a>
          </li>
         	@endforeach @endif
          @elseif($Products->isNotEmpty()) @foreach($Products as $PRCode => $PRArray) @break($loop->index) @foreach($PRArray as $key => $Dets)
					<li class="nav-item">
            <a class="nav-link js-scroll-trigger" href="#{{ $Dets->Edition->code }}">{{ $Dets->Edition->name }}</a>
          </li>
          @endforeach @endforeach
          @endif
          <li class="nav-item">
            <a class="nav-link js-scroll-trigger" href="#contacts">Contacts</a>
          </li>
        </ul>
      </div>
    </nav>

    <div class="container-fluid p-0">

      <section class="brand-section p-3 p-lg-5 d-flex d-column" id="about">
        <div class="my-auto">
          <h1 class="mb-0">{{ $B->heading }}
            <span class="text-primary"></span>
          </h1>
          <div class="subheading mb-5">{{ $B->caption }}
            <a href="mailto:name@email.com"></a>
          </div>
          <p class="mb-5">{{ $B->about }}</p>
        </div>
      </section>

     @if($Data->type == 'company')
    
     @foreach($Products as $PRCode => $PRArray)
     @php $MyEditions = []; @endphp
      <section class="brand-section p-3 p-lg-5 d-flex flex-column" id="{{ $PRCode }}">
        <div class="my-auto">
          <h2 class="mb-2">{{ $PRArray[0]->Product->name }}</h2>
          <p class="mb-5">{!! nl2br($PRArray[0]->Product->description_public) !!}</p>

          @foreach($PRArray as $Dets)
          <div class="brand-item d-flex flex-column flex-md-row mb-5">
            <div class="brand-content mr-auto">
              <h3 class="mb-0">{{ $Dets->Edition->name }}</h3>
              <div class="subheading mb-3"></div>
              @foreach($Dets->Product->Editions as $EDN)
							@continue($EDN->code != $Dets->Edition->code)
							@php $MyEditions[$EDN->code] = [$EDN->name, $EDN->pivot->description]; @endphp
						 	<p>{!! nl2br(explode("\n",$EDN->pivot->description)[0]) !!}</p>
							@endforeach
            </div>
            <div class="brand-date text-md-right">
             	<a href="#{{ $PRCode }}_{{ $Dets->Edition->code }}" class="nav-link js-scroll-trigger">More</a>
            </div>
          </div>
          @endforeach
        </div>
      </section>

      @foreach($MyEditions as $EDNCode => $EDNArray)
      <section class="brand-section p-3 p-lg-5 d-flex flex-column" id="{{ $PRCode }}_{{ $EDNCode }}">
        <div class="my-auto">
          <h2 class="mb-5">{{ $EDNArray[0] }}</h2>
          @foreach(explode("\n",$EDNArray[1]) as $DescPart)
          <p class="mb-1">{{ $DescPart }}</p>
          @endforeach
        </div>
				<div class="brand-date text-md-right">
					<a href="#{{ $PRCode }}" class="nav-link js-scroll-trigger">Back</a>
				</div>
      </section>
      @endforeach
     @endforeach
     
     @elseif($Data->type == 'product')
     
     @if($Products->isNotEmpty()) @foreach($Products as $PRCode => $PRArray) @break($loop->index) @foreach($PRArray as $Dets)
      <section class="brand-section p-3 p-lg-5 d-flex flex-column" id="{{ $Dets->Edition->code }}">
        <div class="my-auto">
          <h2 class="mb-5">{{ $Dets->Edition->name }}</h2>

          
          <div class="brand-item d-flex flex-column flex-md-row mb-5">
            <div class="my-auto">
              <div class="subheading mb-3"></div>
              @foreach($Dets->Product->Editions as $EDN) @continue($EDN->code != $Dets->Edition->code) @php $MyEditions[$EDN->code] = [$EDN->name, $EDN->pivot->description]; @endphp
						 	 <p class="mb-4">{!! implode('</p><p class="mb-4">',(explode("\n",$EDN->pivot->description))) !!}</p>
							@endforeach
            </div>
          </div>
        </div>
      </section>
     @endforeach @endforeach @endif
     
     @endif


      <section class="brand-section p-3 p-lg-5 d-flex flex-column" id="contacts">
        <div class="my-auto">
          <h2 class="mb-5">Contact</h2>

          <div class="brand-item d-flex flex-column flex-md-row mb-5">
            <div class="brand-content mr-auto">
              <h3 class="mb-0">Address</h3>
              <div class="subheading mb-3"></div>
              <div>{!! nl2br($B->address) !!}</div>
            </div>
          </div>

          <div class="brand-item d-flex flex-column flex-md-row mb-5">
            <div class="brand-content mr-auto">
              <h3 class="mb-0">Contact Number</h3>
              <div class="subheading mb-3"></div>
              <ul class="fa-ul mb-0">@foreach(explode("\n",$B->number) as $no)
              	<li><i class="fa-li fa fa-phone"></i>{{ $no }}</li>
              @endforeach</ul>
              
              
            </div>
          </div>

          <div class="brand-item d-flex flex-column flex-md-row mb-5">
            <div class="brand-content mr-auto">
              <h3 class="mb-0">Email Address</h3>
              <div class="subheading mb-3"></div>
              <ul class="fa-ul mb-0">@foreach(explode("\n",$B->email) as $email)
              	<li><i class="fa-li fa fa-envelope"></i>{{ $email }}</li>
              @endforeach</ul>
            </div>
          </div>
          
          @if($B->Links->isNotEmpty())
          <ul class="list-inline list-social-icons mb-0">
           	@foreach($B->Links as $Link)
            <li class="list-inline-item">
              @if($Link->link == '#' || $Link->link == '') <a href="#"> @else <a href="{{ $Link->link }}" target="{{ $Link->target }}"> @endif
                @if($Link->fa != '')<span class="fa-stack fa-lg">
                  <i class="fa fa-circle fa-stack-2x"></i>
                  <i class="fa fa-{{ str_ireplace('fb-','',$Link->fa) }} fa-stack-1x fa-inverse"></i>
                @elseif($Link->name != '')
                	{!! $Link->name !!}
                @else
                	&nbsp;
                @endif
                </span>
              </a>
            </li>
           	@endforeach
          </ul>
          @endif

        </div>
      </section>

    </div>

    <!-- Bootstrap core JavaScript -->
    <script src="/js/brand/jquery.min.js"></script>
    <script src="/js/brand/bootstrap.bundle.min.js"></script>

    <!-- Plugin JavaScript -->
    <script src="/js/brand/jquery.easing.min.js"></script>

    <!-- Custom scripts for this template -->
    <script src="/js/brand/brand.min.js"></script>

  </body>

</html>