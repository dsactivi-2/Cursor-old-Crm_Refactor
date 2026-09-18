<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
	<link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/jasny-bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="https://crm.job-step.com/css/intlTelInput.css">

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('js/jasny-bootstrap.min.js') }}" defer></script>
    {{-- <script src="{{ asset('js/bootstrap-select.min.js') }}" defer></script> --}}
    <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
	<script src="https://crm.job-step.com/js/intlTelInput.js"></script>
	<script src="https://crm.job-step.com/js/intlTelInput-jquery.min.js"></script>

</head>
<body>
    <div id="app" >
        <nav class="navbar navbar-expand-md navbar-light">
            <div class="container">
                <a style = "width: 50%;" class="navbar-brand" href="{{ url('/') }}">
					<img style = "width: 55%;" src = "images/jm.png" >
                </a>
                <button class="navbar-toggler" style="" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                   <span class="navbar-toggler-icon"></span>
                </button>
			</div>
				<div class="collapse navbar-collapse pull-right" id="navbarSupportedContent" style="position: absolute; top: 100%; right: 20px; z-index: 1; border-radius: 10px; background-color: rgba(182, 222, 227, 0.5;">

					<!-- Right Side Of Navbar -->
					<ul class="navbar-nav ml-auto" style="width: fit-content; ">

						<li class="language_list"><a href="lang/de" ><img src="{{asset('images/de3d.png')}}" height="24" width="24"> DE</a></li>
						<li class="language_list"><a href="lang/bs" ><img src="{{asset('images/bs3d.png')}}" height="24" width="24"> BS</a></li>
						<li class="language_list"><a href="lang/hr" ><img src="{{asset('images/hr3d.png')}}" height="24" width="24"> HR</a></li>
						<li class="language_list"><a href="lang/sr" ><img src="{{asset('images/sr3d.png')}}" height="24" width="24"> SR</a></li>
					</ul>
				</div>

        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
</html>
