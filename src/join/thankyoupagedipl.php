<?php
	include("includes/functions.php");
	if(isset($_COOKIE['unique'])){
		header("Location: https://job-step.com");
	}
	$value = bin2hex(openssl_random_pseudo_bytes(12));
	setcookie("unique", $value, time()+3600); 


?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>Nostrifikacija diplome</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<?php /* include('includes/favicon.php');*/ ?>
		<!--- GTM TAG LILIUM -->
		<!-- Google Tag Manager -->
			<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
			new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
			j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
			'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
			})(window,document,'script','dataLayer','GTM-5KQ9L6S');</script>
			<!-- End Google Tag Manager -->
			
			
		<!--- GTM TAG FORFIVE -->
		<!-- Google Tag Manager -->
			<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
			new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
			j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
			'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
			})(window,document,'script','dataLayer','GTM-TVGL3L5');</script>
		<!-- End Google Tag Manager -->

		<!-- Style -->
		
		
 
		<!--<script src="https://www.google.com/recaptcha/api.js?render=6LfZkPkUAAAAAEKGm2CfsV82vtHULmHfaS4hPpa_"></script>-->
		<link href="<?php getCRMUrl(); ?>css/bootstrap.min.css" rel="stylesheet">
		<link href="<?php getCRMUrl(); ?>css/jasny-bootstrap.min.css" rel="stylesheet">
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		<link rel="stylesheet" type="text/css" href="<?php getCRMUrl(); ?>css/calendar.css" />
		<link rel="stylesheet" type="text/css" href="<?php getCRMUrl(); ?>css/bootstrap-select.css" />
		<link rel="stylesheet" type="text/css" href="<?php getCRMUrl(); ?>css/jobstep_datedropper.css" />
		<link rel="stylesheet" type="text/css" href="<?php getCRMUrl(); ?>css/timedropper.min.css" rel="stylesheet">
		<link href="<?php getCRMUrl(); ?>css/jquery.dataTables.min.css" rel="stylesheet">
		<link href="<?php getCRMUrl(); ?>css/responsive.dataTables.min.css" rel="stylesheet">
		<link href="<?php getCRMUrl(); ?>css/responsive.bootstrap.min.css" rel="stylesheet">
		<link href="<?php getCRMUrl(); ?>css/jquery.fancybox.css" rel="stylesheet">
		<link href="<?php getCRMUrl(); ?>js/ui/trumbowyg.min.css" rel="stylesheet">
		<link href="<?php getCRMUrl(); ?>css/select2.min.css" rel="stylesheet">
		<link type="text/css" href="https://gyrocode.github.io/jquery-datatables-checkboxes/1.2.11/css/dataTables.checkboxes.css" rel="stylesheet" />
		<link href="https://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
		<link rel="icon" href="../images/Jobstep-logo_news.png">

		<!-- Fonts -->
		<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700&amp;subset=latin-ext" rel="stylesheet">
		<script src="https://kit.fontawesome.com/259cc6db56.js" crossorigin="anonymous"></script>
		<!-- FLATPICK -->
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
		<link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/airbnb.css">
		<!-- TELEFON -->
		<link rel="stylesheet" type="text/css" href="<?php getCRMUrl(); ?>css/intlTelInput.css">
		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->

		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<script src="https://cdn.datedropper.com/get/kqs82kw3qehj1ghchbtnnv6c6f7gzvlz"></script>
		<script src="<?php getCRMUrl(); ?>js/bootstrap.min.js"></script>
		<script src="<?php getCRMUrl(); ?>js/chart.min.js"></script>
		<script src="<?php getCRMUrl(); ?>js/modernizr.custom.63321.js"></script>
		<script type="text/javascript" src="<?php getCRMUrl(); ?>js/jquery.calendario.js"></script>
		<script src="<?php getCRMUrl(); ?>js/jquery.slimscroll.min.js"></script>
		<script src="<?php getCRMUrl(); ?>js/jquery.matchHeight-min.js"></script>
		<script src="<?php getCRMUrl(); ?>js/jasny-bootstrap.min.js"></script>
		<script src="<?php getCRMUrl(); ?>js/bootstrap-select.min.js"></script>
		<script src="<?php getCRMUrl(); ?>js/scripts.js"></script>
		<script src="<?php getCRMUrl(); ?>js/jquery.dataTables.min.js"></script>

		<script src="<?php getCRMUrl(); ?>js/dataTables.responsive.min.js"></script>
		<script src="<?php getCRMUrl(); ?>js/responsive.bootstrap.min.js"></script>
		<script src="<?php getCRMUrl(); ?>js/timedropper.min.js"></script>
		<script src="<?php getCRMUrl(); ?>js/jquery.fancybox.js"></script>
		<script src="<?php getCRMUrl(); ?>js/jquery.mask.min.js"></script>
		<script src="<?php getCRMUrl(); ?>js/trumbowyg.min.js"></script>
		<script src="<?php getCRMUrl(); ?>js/timeago.js"></script>
		<script src="<?php getCRMUrl(); ?>js/select2.min.js"></script>
		<script src="<?php getCRMUrl(); ?>js/jquery.table2excel.js"></script>
		<script src="<?php getCRMUrl(); ?>js/jquery.password-generator-plugin.min.js"></script>
		<script type="text/javascript" src="<?php getCRMUrl(); ?>js/langs/hr.min.js"></script>
		<script type="text/javascript" src="https://gyrocode.github.io/jquery-datatables-checkboxes/1.2.11/js/dataTables.checkboxes.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
		<!-- FLATPICK -->
		<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>




				
		<!-- Style CSS -->
		<link rel="stylesheet" href="https://crm.job-step.com/css/style.css">

		<!-- Style2 CSS -->
		<link rel="stylesheet" href="https://crm.job-step.com/css/style2.css">



		<!-- TELEFON -->
		<script src="<?php getCRMUrl(); ?>js/intlTelInput.js"></script>
		<script src="<?php getCRMUrl(); ?>js/intlTelInput-jquery.min.js"></script>

		<!--- GTM TAG LILIUM -->
		<!-- Google Tag Manager (noscript) -->
		<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5KQ9L6S"
		height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
		<!-- End Google Tag Manager (noscript) -->
		
		
			<!--- GTM TAG FORFIVE -->
		<!-- Google Tag Manager (noscript) -->
		<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TVGL3L5"
		height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
		<!-- End Google Tag Manager (noscript) -->


	
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>
        @import url("https://fonts.googleapis.com/css?family=Open+Sans:300,400,700&amp;subset=latin-ext");

        body {
            font-family: "Open Sans", sans-serif;
            padding-top: 30px;
        }

        .full-screen {
            padding: 6rem 0;
        }

        .small-text {
            color: #5b5b5b;
            font-size: 14px;
            font-weight: bold;
            padding: 8px 16px;
            border-radius: 50px;
            letter-spacing: 0.2px;
        }

        ul {
            margin: 0;
            padding: 0;
        }

            ul li {
                list-style: none;
            }

        a {
            font-weight: normal;
            text-decoration: none !important;
            transition: all 0.4s ease;
        }

            a:hover {
                color: #6097A0 !important;
            }

        .navbar-brand .uil {
            font-size: 40px;
        }

        p {
            font-size: 18px;
            font-weight: 300;
            line-height: 1.5;
            color: #5b5b5b;
        }

        h1, h2, h3, h4, h5, h6 {
            font-weight: bold;
            letter-spacing: -1px;
        }

        h1 {
            color: #212121;
            font-size: 2.8em;
            margin: 24px 0;
        }

        h2 {
            color: #353535;
            font-size: 2.4em;
            font-weight: bold;
        }

        h3 {
            color: #484848;
        }

        h3,
        b, strong {
            font-weight: bold;
        }
    </style>


</head>
<body>
    <!--You Tube tutorijal i Jobstep Messenger-->
    <section class="justify-content-center align-items-center">
        <div class="container" style="margin-top:0px;">
            <div class="row">
                <div class="col-lg-12" align="center" style="padding-bottom:20px;">
                    <img style="padding-top:10px;" width="40" height="50" src="<?php echo getSiteUrl(); ?>images/uspjesno.png"  />
                    <p>Hvala Vam! Vaši podaci su uspješno poslani.</p>
                </div>

            </div>
            <div class="row">

                <div class="col-lg-8 col-md-12 col-12 d-flex align-items-center">
                    <div class="about-text">
                        <h4 align="center"> Finalizirajte Vašu prijavu!</h4>
                        <p align="center">Pogledajte video ispod, preuzmite našu aplikaciju slijedeći instrukcije na Viberu, koje će Vam pomoći da izradite svoj idealan profil i pronađete pravi posao za Vas.</p>
                        <div style=" position: relative; overflow: hidden; width: 100%; padding-top: 56.25%;">
                            <iframe style="position: absolute; top: 0; left: 0; bottom: 0; right: 0; width: 100%; height: 100%;" src="https://www.youtube.com/embed/rttgZz8ohEI"></iframe>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4" align="center" style="padding-top:30px;">
                    <img src="<?php echo getSiteUrl(); ?>images/jobstep_messenger.png" />
                </div>

            </div>
            <div class="row">
                <div class="col-lg-12" align="center">
                    <div class="custom-btn-group mt-4" style="margin-top:0px;">
                        <a href="https://play.google.com/store/apps/details?id=net.job_step.jobstepmessenger"><img src="<?php echo getSiteUrl(); ?>images/google_play.png" style="margin-bottom:10px;" /></a>
                        <a href="https://apps.apple.com/tt/app/jobstep-messenger/id1486805317"><img src="<?php echo getSiteUrl(); ?>images/app_store.png" style="margin-bottom:10px;" /></a>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!--Jobstep Partner App-->
    <section class="justify-content-center align-items-center">
        <div class="container" style="margin-top:40px;">
            <div class="row">

                <div class="col-lg-7" align="center">
                    <img width="300" height="550" src="<?php echo getSiteUrl(); ?>images/jobstep_partner_app.png" />
                </div>

                <div class="col-lg-5" align="center" style="padding-top:30px;">
                    <div class="custom-btn-group mt-4 " style="padding-top:20px;">
                        <h3>Jobstep Partner App</h3>
                        <p>Jobstep Partner App je aplikacija za vanjske saradnike Jobstepa. Svako može postati naš partner prijavom na ovu aplikaciju. Preporuči Jobstep i zaradi!</p>
                        <a href="https://play.google.com/store/apps/details?id=com.partnerjobstep"><img width="220" height="65" src="<?php echo getSiteUrl(); ?>images/google_play_crna.png" style="margin-bottom:10px;" alt="Android - Jobstep Messenger" /></a>
                        <a href="https://apps.apple.com/tt/app/jobstep-partner-app/id1498761708?ign-mpt=uo%3D2"><img width="220" height="65" src="<?php echo getSiteUrl(); ?>images/app_store_crna.png" style="margin-bottom:10px;" alt="iOS - Jobstep Messenger" /></a>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!--Jobstep Sve za vizu-->
    <section class="justify-content-center align-items-center">
        <div class="container" style="margin-top:0px;">
            <div class="row">
                <div class="col-lg-5" align="center" style="padding-top:30px;">
                    <div class="custom-btn-group mt-4" style="padding-top:20px;">
                        <h3>Sve za vizu</h3>
                        <p>Bliži Vam se termin za vizu? Prikupili ste dokumentaciju ali imate osjećaj da Vam još uvijek nešto nedostaje? Dosadilo Vam je dugo čekanje u redu ili uklapanje u slobodne termine agencija koje se bave popunjavanjem zahtjeva za vizu? Jobstep nudi jednostavno rješenje za Vas. Preuzmite besplatnu aplikaciju za popunjavanje zahtjeva za vizu, ispunite osnove podatke i preuzmite popunjen zahtjev za vizu u nekoj od naših poslovnica. </p>
                        <a href="https://play.google.com/store/apps/details?id=com.jobstep.onlineviza"><img width="220" height="65" src="<?php echo getSiteUrl(); ?>images/google_play_crna.png" style="margin-bottom:10px;" /></a>
                        <a href="#!"><img width="220" height="65" src="<?php echo getSiteUrl(); ?>images/app_store_crna.png" style="margin-bottom:10px;" /></a>
                    </div>
                </div>

                <div class="col-lg-7" align="center">
                    <img width="300" height="550" src="<?php echo getSiteUrl(); ?>images/sve za vizu.png" />
                </div>

            </div>

        </div>
    </section>


    <!--Footer - Društvene mreže-->

    <footer class="text-center text-white" style="background-color: #f1f1f1; margin-top:30px;">

        <div class="container pt-4">
            <p>Zapratite nas na:</p>
            <section class="mb-4">
                <a href="https://www.facebook.com/JobStepInternational" alt="Jobstep Facebook"> <img width="35" height="35" src="<?php echo getSiteUrl(); ?>images/facebook_logo.png" /></a>
                <a href="https://www.instagram.com/jobstepinternational/" alt="Jobstep Instagram"> <img width="35" height="35" src="<?php echo getSiteUrl(); ?>images/instagram_logo.png" /> </a>
                <a href="https://www.linkedin.com/in/jobstepint/" alt="Jobstep LinkedIn" role="button" data-mdb-ripple-color="dark"> <img width="35" height="35" src="<?php echo getSiteUrl(); ?>images/linked_in_logo.png" /></a>
                <a href=" https://www.youtube.com/channel/UChL-bdalb8qxtYckAVZkXZw" alt="Jobstep Youtube" role="button" data-mdb-ripple-color="dark"> <img width="35" height="35" src="<?php echo getSiteUrl(); ?>images/youtube_logo.png" /></a>
            </section>

        </div>

        <div class="text-center text-dark p-3" style="background-color: rgba(0, 0, 0, 0.2);">
            © 2021 Copyright: Jobstep International d.o.o.<br />
        </div>
    </footer>



</body>
</html>