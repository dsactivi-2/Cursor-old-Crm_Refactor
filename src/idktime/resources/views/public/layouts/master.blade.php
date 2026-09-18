<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">



    <!-- CSRF Token -->

    <meta name="csrf-token" content="{{ csrf_token() }}">



	<title>{{ env('APP_NAME') }} - Kontrola vremena</title>



	<meta name="robots" content="noindex" />

	<meta name="robots" content="nofollow" />



	<!-- Fonts -->

    <link rel="dns-prefetch" href="//fonts.gstatic.com">

    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">



    <!-- Favicon -->

    <link rel="shortcut icon" href="{{ asset('assets/images/favicon/favicon.ico') }}" type="image/x-icon">

	

	<!-- Styles -->

    <link rel="stylesheet" href="{{ asset('assets/css/public.css?v=3.2.0') }}">



    <!-- Font awesome icons -->

	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

	

	<!-- jQuery and bootstrap notify library -->

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
	
	@yield('styles')
		
	<!-- Responsive -->
	{{-- <style>
	
		@media only screen and (max-height: 580px) {
			#app main .welcome .welcome-top {
				padding: 10px 50px 0;
			}

			#app main .welcome .welcome-middle {
				padding: 0px 50px;
			}
			
			#app main .welcome .welcome-bottom {
				padding: 0px 10px 20px;
			}
			
			#app main .welcome .welcome-top__time p:first-child {
				font-size: 30px;
			}
			
			#app main .welcome .welcome-top__time p:last-child {
				font-size: 20px;
				margin-bottom: 0px;
			}
		}
	</style> --}}
</head>

<body>

	

	<div id="app">

		<main>

			@yield('content')

		</main>

	</div>



	@yield('js')

	

</body>

</html>