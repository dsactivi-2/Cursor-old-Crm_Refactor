
<?php
include("includes/functions.php");
//ID kampanja
$kamp = $_GET['pid'];
$kampanje_sa_mailom = array(1,567);
if(isset($_GET['lang']))
	$lang = $_GET['lang'];
else
	$lang = "hr";
if($lang == "bs"){
	$country_code = "ba";
	$novi_tekst = "Popunite našu prijavnu formu kako bi Vas naš tim mogao kontaktirati i detaljno upoznati s procedurom za odlazak u Njemačku.";
	$tekst1 = "Nostrifikacija diplome je neophodan proces prilikom odlaska u Njemačku.";
	$tekst2 = "Uz Job Step tim nostrifikaciju možete odraditi profesionalno i u najkraćem mogućem roku!";
	$tekst3 = "Stručan tim, posebno kreiran program te profesionalna korespondencija sa zaduženim institucijama i nadležnim osobama samo su dio naše usluge.";
	$tekst4 = "Popunite obrazac i član našeg tima će Vam se javiti. ";
	$tekst5 = "Tako ćete dobiti jasne upute i učiniti konkretne korake.";
	
}elseif($lang == "bs1"){
	$country_code = "de";
	$novi_tekst = "Popunite našu prijavnu formu kako bi Vas naš tim mogao kontaktirati i detaljno upoznati s procedurom za odlazak u Njemačku.";
	$tekst1 = "Nostrifikacija diplome je neophodan proces prilikom odlaska u Njemačku.";
	$tekst2 = "Uz Job Step tim nostrifikaciju možete odraditi profesionalno i u najkraćem mogućem roku!";
	$tekst3 = "Stručan tim, posebno kreiran program te profesionalna korespondencija sa zaduženim institucijama i nadležnim osobama samo su dio naše usluge.";
	$tekst4 = "Popunite obrazac i član našeg tima će Vam se javiti. ";
	$tekst5 = "Tako ćete dobiti jasne upute i učiniti konkretne korake.";
}else{
	$country_code = "rs";
	$novi_tekst = "Popunite našu prijavnu formu kako bi Vas naš tim mogao kontaktirati i detaljno upoznati s procedurom za odlazak u Nemačku.";
	$tekst1 = "Nostrifikacija diplome je neophodan proces prilikom odlaska u Nemačku.";
	$tekst2 = "Uz Job Step tim nostrifikaciju možete odraditi profesionalno i u najkraćem mogućem roku!";
	$tekst3 = "Stručan tim, posebno kreiran program te profesionalna korespondencija sa zaduženim institucijama i nadležnim osobama samo su dio naše usluge.";
	$tekst4 = "Popunite obrazac i član našeg tima će Vam se javiti. ";
	$tekst5 = "Tako ćete dobiti jasne upute i učiniti konkretne korake.";
}

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>Nostrifikacija diplome</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<!-- Style -->
		<link href="https://crm.job-step.com/css/bootstrap.min.css" rel="stylesheet">
		<link href="https://crm.job-step.com/css/jasny-bootstrap.min.css" rel="stylesheet">
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		<link rel="stylesheet" type="text/css" href="https://crm.job-step.com/css/calendar.css" />
		<link rel="stylesheet" type="text/css" href="https://crm.job-step.com/css/bootstrap-select.css" />
		<!-- <link rel="stylesheet" type="text/css" href="css/jobstep_datedropper.css" /> -->
		<link rel="stylesheet" type="text/css" href="https://crm.job-step.com/css/timedropper.min.css" rel="stylesheet">
		<link href="https://crm.job-step.com/css/jquery.dataTables.min.css" rel="stylesheet">
		<link href="https://crm.job-step.com/css/responsive.dataTables.min.css" rel="stylesheet">
		<link href="https://crm.job-step.com/css/responsive.bootstrap.min.css" rel="stylesheet">
		<link href="https://crm.job-step.com/css/jquery.fancybox.css" rel="stylesheet">
		<link href="https://crm.job-step.com/js/ui/trumbowyg.min.css" rel="stylesheet">
		<link href="https://crm.job-step.com/css/select2.min.css" rel="stylesheet">
		<link href="https://crm.job-step.com/css/style.css" rel="stylesheet">
		<link type="text/css" href="https://gyrocode.github.io/jquery-datatables-checkboxes/1.2.11/css/dataTables.checkboxes.css" rel="stylesheet" />
		<link href="https://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
		<link rel="icon" href="../images/Jobstep-logo_news.png">

		<!-- Fonts -->
		<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700&amp;subset=latin-ext" rel="stylesheet">
		<script src="https://use.fontawesome.com/758aa0fdaa.js"></script>
		<!-- FLATPICK -->
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
		<link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/airbnb.css">
		<!-- TELEFON -->
		<link rel="stylesheet" type="text/css" href="https://crm.job-step.com/css/intlTelInput.css">
		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->

		<!-- RECAPTCHA -->
		
		<script src="https://www.google.com/recaptcha/api.js?render=6LdZmAIaAAAAANQAf0Fhrg8lD5Iq0291FQOvLyW6"></script>
		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<!-- <script src="https://cdn.datedropper.com/get/kqs82kw3qehj1ghchbtnnv6c6f7gzvlz"></script> -->
		<script src="https://crm.job-step.com/js/bootstrap.min.js"></script>
		<script src="https://crm.job-step.com/js/chart.min.js"></script>
		<script src="https://crm.job-step.com/js/modernizr.custom.63321.js"></script>
		<script type="text/javascript" src="https://crm.job-step.com/js/jquery.calendario.js"></script>
		<script src="https://crm.job-step.com/js/jquery.slimscroll.min.js"></script>
		<script src="https://crm.job-step.com/js/jquery.matchHeight-min.js"></script>
		<script src="https://crm.job-step.com/js/jasny-bootstrap.min.js"></script>
		<script src="https://crm.job-step.com/js/bootstrap-select.min.js"></script>
		<!-- TELEFON -->
		<script src="https://crm.job-step.com/js/intlTelInput.js"></script>
		<script src="https://crm.job-step.com/js/intlTelInput-jquery.min.js"></script>
		<!--		<script src="https://intl-tel-input.com/node_modules/intl-tel-input/build/js/utils.js"></script>-->
		<!--	<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.min.js"></script>-->
		
		<script src="https://crm.job-step.com/js/jquery.dataTables.min.js"></script>

		<script src="https://crm.job-step.com/js/dataTables.responsive.min.js"></script>
		<script src="https://crm.job-step.com/js/responsive.bootstrap.min.js"></script>
		<script src="https://crm.job-step.com/js/timedropper.min.js"></script>
		<script src="https://crm.job-step.com/js/jquery.fancybox.js"></script>
		<script src="https://crm.job-step.com/js/jquery.mask.min.js"></script>
		<script src="https://crm.job-step.com/js/trumbowyg.min.js"></script>
		<script src="https://crm.job-step.com/js/timeago.js"></script>
		<script src="https://crm.job-step.com/js/select2.min.js"></script>
		<script src="https://crm.job-step.com/js/jquery.table2excel.js"></script>
		<script src="https://crm.job-step.com/js/jquery.password-generator-plugin.min.js"></script>
		<script type="text/javascript" src="https://crm.job-step.com/js/langs/hr.min.js"></script>
		<script type="text/javascript" src="https://gyrocode.github.io/jquery-datatables-checkboxes/1.2.11/js/dataTables.checkboxes.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
		<!-- FLATPICK -->
		<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
		
		<!-- Facebook Pixel Code -->
	
		<script>
		!function(f,b,e,v,n,t,s)
		{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
		n.callMethod.apply(n,arguments):n.queue.push(arguments)};
		if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
		n.queue=[];t=b.createElement(e);t.async=!0;
		t.src=v;s=b.getElementsByTagName(e)[0];
		s.parentNode.insertBefore(t,s)}(window, document,'script',
		'https://connect.facebook.net/en_US/fbevents.js');
		fbq('init', '692183871429489');
		fbq('track', 'PageView');
		</script>
		<noscript><img height="1" width="1" style="display:none"
		src="https://www.facebook.com/tr?id=692183871429489&ev=PageView&noscript=1"
		/></noscript>
		<!-- DODANO 9.11 - Ismail Suljic -->
		<!-- End Facebook Pixel Code -->
		
	</head>
	<body>
		<style>
			#dizajn_vanjski_dio{
				border: 1px solid #cccccc;
				border-radius: 3.25rem;
				padding-left: 13px;
				padding-right: 13px;
			}
			#dizajn_vanjski_dio > .row:first-child{
				padding: 10px 30px 10px 30px;
				background-color: #6097a0;
				border-radius: 3.25rem 3.25rem 0.25rem 0.25rem;
				color: white;
				font-weight: bold;
				font-size: large;
				margin-top: -3px;
			}
				
			#dizajn_vanjski_dio > .row:nth-child(2){	
				padding-top: 15px;	
				padding-left: 15px;	
				padding-right: 15px;	
				padding-bottom : 0px;
			}	
			#dizajn_vanjski_dio > .row:nth-child(3){	
				padding: 15px;	
				padding-left: 15px;	
				padding-right: 15px;	
			}
			#content_public{
				/*height: 100vh;*/
			}
			body{
				/*height: 100vh;*/
			}
			@media screen and (max-width: 991px) {
				.img-responsive{
					max-width: 50px !important;
					margin-top: 10px !important;
					margin-bottom: 10px !important;
				}
			}
			.materail-input-block_success .materail-input-block__line {
				background-color: #6097a0;
			}
			.material-btn_success {
				background-color: #6097a0;
				border-color: #6097a0;
				color: #fff;
			}
			.material-btn-icon-success .fa {
				margin-right: 10px;
				background-color: #6097a0;
				padding: 10px 14px;
			}
			.material-btn_success:hover {
				color: #fff;
				background-color: #6097a0;
				border-color: #6097a0;
			}
			.material-btn_success:active {
				background-color: #046373;
				border-color: #046373;
				color: #fff;
			}
			#footer{
				position: absolute;
				left: 0;
				bottom: 0;
				width: 100%;
				border-top: 1px solid #ccc;
				font-size: 12px;
				text-align: center;
				margin-top: 0px;
			}
			.opis_usluge > p{
				text-align: center;
			}
			.zoomOut {
				-webkit-animation-name: zoomOut;
				animation-name: zoomOut;
				-webkit-animation-duration: 1s;
				animation-duration: 1s;
				-webkit-animation-fill-mode: both;
				animation-fill-mode: both;
			}
			@-webkit-keyframes zoomOut {
				0% {
					opacity: 1;
				}
		  
				50% {
					opacity: 0;
					-webkit-transform: scale3d(.3, .3, .3);
					transform: scale3d(.3, .3, .3);
				}
				100% {
					opacity: 0;
				}
			}
			@keyframes zoomOut {
				0% {
					opacity: 1;
				}
				50% {
					opacity: 0;
					-webkit-transform: scale3d(.3, .3, .3);
					transform: scale3d(.3, .3, .3);
				}
				100% {
					opacity: 0;
				}
			}
			.zoomIn {
				-webkit-animation-name: zoomIn;
				animation-name: zoomIn;
				-webkit-animation-duration: 1s;
				animation-duration: 1s;
				-webkit-animation-fill-mode: both;
				animation-fill-mode: both;
			}
			@-webkit-keyframes zoomIn {
				0% {
					opacity: 0;
					-webkit-transform: scale3d(.3, .3, .3);
					transform: scale3d(.3, .3, .3);
				}
				50% {
					opacity: 1;
				}
			}
			@keyframes zoomIn {
				0% {
					opacity: 0;
					-webkit-transform: scale3d(.3, .3, .3);
					transform: scale3d(.3, .3, .3);
				}
				50% {
					opacity: 1;
				}
			}
		</style>
		<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-180348284-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-180348284-1');
</script>
		<script>
			$(document).ready(function() {
				$("input").focus(function() { 
					$('footer').hide();
				});


				$("input").blur(function(){
					$('footer').show();
				});
						
			});
			
			
		</script>
		<script>
		$(document).ready(function() {
			var kampanja_id = <?php echo $kamp; ?>;
			console.log(kampanja_id);
			$.ajax({
				url: '<?php getCRMUrl(); ?>public_kandidati.php?page=updBrojPregledaKampanje',
				type: 'POST',
				data: {"kampanja_id": kampanja_id},
				dataType: 'html',
				success: function(data) {
					
				}
			});
		});
		
		</script>
		<script>
		
		function showErrorMessage(){
			$('.error_message').show('slow');
			$('.error_message').css("display","flex");
			$('.error_message').html('Pogrešno unešeni podaci');
			setTimeout(() => {
				$('.error_message').hide('slow');
				}, 3000);
		}
		</script>
		<div id="content_public">
			
			<script>
				grecaptcha.ready(function () {
					grecaptcha.execute('6LdZmAIaAAAAANQAf0Fhrg8lD5Iq0291FQOvLyW6', { action: 'nostrifikacija_Kampanje' }).then(function (token) {
						var recaptchaResponse = document.getElementById('recaptchaResponse');
						recaptchaResponse.value = token;
					});
				});
			</script>
			
			<input type="hidden" id="countryCode" name="countryCode" value="<?php echo $country_code; ?>">
			<div class="container-fluid">
				<div class="content_box">
					<div class="row">
						<div class="col-xs-12 text-center">
							<img class="img-responsive" src="https://crm.job-step.com/images/Jobstep_logo_new.png" alt="" style="max-width:50px; margin-top: 20px; margin-bottom: 20px;">
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="row">
								<div class = "col-md-8 col-md-offset-2">
									<div class="row">
										<div id = "dizajn_vanjski_dio" class = "col-xs-12">
											<div class="row">
												<div class = "col-xs-12 text-center">
													Prijavna forma
												</div>
											</div>
											<div class = "row" id = "step_1">
												<div class = "col-xs-12 opis_usluge">
													
													<p>
														<?php echo $novi_tekst; ?>
														<br>
														<!-- <span id="skraceni_tekst" >Uz Job Step tim ... <a onclick = "go_to_step_2()">VIDI VIŠE</a></span> -->
													</p>
													
													<p id="ostatak_teksta">
														<?php echo $tekst2; ?>
														<br>
														<?php echo $tekst3; ?>
														<br>
														<?php echo $tekst4; ?>
														<br>
														<?php echo $tekst5; ?>
													</p>
												</div>
												<!-- <div class = "col-xs-12 text-center">
													<button onclick = "go_to_step_2()" class="btn material-btn material-btn-icon-success material-btn_success main-container__column">
														<i class="fa fa-sign-in" aria-hidden="true">
														</i>
														<span>
															Popuni obrazac
														</span>
													</button>
												</div>
												-->
											</div>
											<div class="row" id = "step_2" style="margin-bottom: 50px;">
												<div class = "col-xs-12">
													<?php if ($kamp == 1){?>
													<form action="https://crm.job-step.com/public_kandidati.php?page=prijavaDIPLKtest" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
													<?php }else{ ?>
													<form action="<?php getCRMUrl(); ?>public_kandidati.php?page=prijavaDIPLK" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
														<?php } ?>
														<input name="kamp"  id="kamp" type = "hidden" value ="<?php echo $kamp; ?>"> 
														<div class="form-group">
															<label for="kandidat_ime_dipl" class="col-sm-3 control-label"><span class="text-danger">*</span> Ime: </label>
															<div class="col-sm-9">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input" type="text" name="kandidat_ime_dipl" id="kandidat_ime_dipl" autocomplete="off" placeholder="Ime" required>
																	<span class="materail-input-block__line"></span>
																</div>
															</div>
														</div>
														<div class="form-group">
															<label for="kandidat_prezime_dipl" class="col-sm-3 control-label"><span class="text-danger">*</span> Prezime: </label>
															<div class="col-sm-9">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input" type="text" name="kandidat_prezime_dipl" id="kandidat_prezime_dipl" autocomplete="off" placeholder="Prezime" required>
																	<span class="materail-input-block__line"></span>
																</div>
															</div>
														</div>
														<div class="form-group">
															<label for="kandidat_telefon_dipl" class="col-sm-3 control-label"><span class="text-danger">*</span>Telefon:</label>
															<div class="col-sm-9">
																<div class="">
																	<input class="form-control materail-input" type="tel" name="kandidat_telefon_dipl" id="kandidat_telefon_dipl" autocomplete="off" required>
																</div>
															</div>
														</div>
														<div class="form-group text-center">
															<style>
															.error_message{
																transition: 0.5s;
																width: 30%;
																background: #741414;
																height: 3rem;
																display: flex;
																align-items: center;
																justify-content: center;
																color: #fff;
																border-radius: 10px;
																display:none;
															}
															@media screen and (max-width: 450px) {
																.error_message{
																	width:100%;
																}
															}
															@media screen and (max-width: 350px) {
																.error_message{
																	width:100%;
																}
															}
															</style>
															<div class="col-sm-12 text-center" style="text-align: -webkit-center;">
																<div class="error_message">TEXT</div>
															</div>
														</div>
															<?php if ($kamp == 1){?>
														<script>
														$( document ).ready(function($) {
															$.each($('#kandidat_telefon_dipl'),function(){
																var telInput = $(this);
																var countryCode = document.getElementById("countryCode").value 
																if ($(this).val().startsWith("+") || $(this).val() == '') {
																$(telInput).intlTelInput({
																	utilsScript:'js/utils.min.js',
																	autoPlaceholder: "aggressive",
																	initialCountry: ""+countryCode+"",
																	formatOnDisplay: true,
																	preferredCountries: ["ba","rs","hr","de"],
																	separateDialCode: true
																});
																}
															});
															//SPAJANJE COUNTRY CODA I TELEFONSKOG BROJA
															$("form").on('submit', function(event) {
																event.preventDefault();
																$('#subform').attr('disabled','disabled');
															
																$.each($('#kandidat_telefon_dipl'),function(){
																	var telInput = $(this);	
																	var telType = telInput.data('type');	
																	telInput.val(telInput.intlTelInput("getNumber"));  
																	//console.log(telInput);
																});
															
															
																var kandidat_ime_dipl 		= $( '#kandidat_ime_dipl' ).val();
																var kandidat_prezime_dipl 	= $( '#kandidat_prezime_dipl' ).val();
																var kandidat_telefon_dipl 	= $( '#kandidat_telefon_dipl' ).val();
																
																$.ajax({
																	url: '<?php getCRMUrl(); ?>processing.php',
																	type: 'POST',
																	data: {
																		"kandidat_ime_dipl": kandidat_ime_dipl,
																		"kandidat_prezime_dipl": kandidat_prezime_dipl,
																		"kandidat_telefon_dipl": kandidat_telefon_dipl
																		},
																	dataType: 'json',
																	success: function(data) {
																		let parsed_data = data;
																		if( parsed_data == 'kandidat_telefon_dipl')
																		{
																			$('#subform').removeAttr('disabled');
																			showErrorMessage();
																			$('#kandidat_telefon_dipl').val('');
																			$('label[for="kandidat_telefon_dipl"]').css('transition','0.5s ease-in');
																			$('label[for="kandidat_telefon_dipl"]').css('transform','scale(1.05)');
																			setTimeout(() => {
																				$('label[for="kandidat_telefon_dipl"]').css('transform','scale(1)');
																				}, 1000); 
																		}
																		else if( parsed_data == true )
																		{
																			event.currentTarget.submit();
																		}
																		else
																		{
																			$('#subform').removeAttr('disabled');
																			//$('#kandidat_telefon_dipl').val('');
																			$('#kandidat_ime_dipl').val('');
																			$('#kandidat_prezime_dipl').val('');
																			showErrorMessage();
																		}
																	},
																	error: function (xhr, ajaxOptions, thrownError) {
																		alert(xhr.status);
																		alert(thrownError);
																	}
																});
															});
																
															});	
														</script>
															<?php }else{ ?>
														<script>
														$( document ).ready(function($) {
															$.each($('#kandidat_telefon_dipl'),function(){
																var telInput = $(this);
																var countryCode = document.getElementById("countryCode").value 
																if ($(this).val().startsWith("+") || $(this).val() == '') {
																$(telInput).intlTelInput({
																	utilsScript:'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.min.js',
																	autoPlaceholder: "aggressive",
																	initialCountry: ""+countryCode+"",
																	formatOnDisplay: true,
																	preferredCountries: ["ba","rs","hr","de"],
																	separateDialCode: true
																});
																}
															});
															//SPAJANJE COUNTRY CODA I TELEFONSKOG BROJA
															$("form").on('submit', function(event) {
																event.preventDefault();
																$('#subform').attr('disabled','disabled');
															
																$.each($('#kandidat_telefon_dipl'),function(){
																	var telInput = $(this);	
																	var telType = telInput.data('type');	
																	telInput.val(telInput.intlTelInput("getNumber"));  
																	//console.log(telInput);
																});
															
															
																var kandidat_ime_dipl 		= $( '#kandidat_ime_dipl' ).val();
																var kandidat_prezime_dipl 	= $( '#kandidat_prezime_dipl' ).val();
																var kandidat_telefon_dipl 	= $( '#kandidat_telefon_dipl' ).val();
																
																$.ajax({
																	url: '<?php getCRMUrl(); ?>processing.php',
																	type: 'POST',
																	data: {
																		"kandidat_ime_dipl": kandidat_ime_dipl,
																		"kandidat_prezime_dipl": kandidat_prezime_dipl,
																		"kandidat_telefon_dipl": kandidat_telefon_dipl
																		},
																	dataType: 'json',
																	success: function(data) {
																		let parsed_data = data;
																		if( parsed_data == 'kandidat_telefon_dipl')
																		{
																			$('#subform').removeAttr('disabled');
																			showErrorMessage();
																			$('#kandidat_telefon_dipl').val('');
																			$('label[for="kandidat_telefon_dipl"]').css('transition','0.5s ease-in');
																			$('label[for="kandidat_telefon_dipl"]').css('transform','scale(1.05)');
																			setTimeout(() => {
																				$('label[for="kandidat_telefon_dipl"]').css('transform','scale(1)');
																				}, 1000); 
																		}
																		else if( parsed_data == true )
																		{
																			event.currentTarget.submit();
																		}
																		else
																		{
																			$('#subform').removeAttr('disabled');
																			//$('#kandidat_telefon_dipl').val('');
																			$('#kandidat_ime_dipl').val('');
																			$('#kandidat_prezime_dipl').val('');
																			showErrorMessage();
																		}
																	},
																	error: function (xhr, ajaxOptions, thrownError) {
																		alert(xhr.status);
																		alert(thrownError);
																	}
																});
															});
														});
														</script>
															<?php } ?>
														<?php if (in_array( $kamp , $kampanje_sa_mailom)){ ?>
														<div class="form-group">
															<label for="kandidat_email_dipl" class="col-sm-3 control-label"><span class="text-danger">*</span>Email:</label>
															<div class="col-sm-9">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input" type="email" name="kandidat_email_dipl" id="kandidat_email_dipl" autocomplete="off" placeholder="E-mail" required>
																	<span class="materail-input-block__line"></span>
																</div>
															</div>
														</div>
														<?php }else{} ?>
														<input type="hidden" name="recaptcha_response" id="recaptchaResponse">
														<div class="form-group" style = "margin-bottom: 0px;">
															<div class="col-sm-offset-4 col-sm-4 text-center">
																<button type="submit" id = "subform" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-sign-in" aria-hidden="true"></i> <span>Pošalji prijavu</span></button>
																<br/><small>Sva polja označena sa <span class="text-danger">*</span> su obavezna!</small>
															</div>
														</div>
													</form>
												</div>
											</div>
											<script>
												$(document).ready(function() {
													$('#ostatak_teksta').hide();
												});
												function go_to_step_2() {
													$('#skraceni_tekst').addClass('zoomOut');
													setTimeout(function() {
														$('#skraceni_tekst').hide();
														$('#ostatak_teksta').show();
														$('#ostatak_teksta').addClass('zoomIn');
													}, 500);
												}
											</script>
											<div class = "row">
												<footer id = "footer">
													<p>
														©2020 Sva prava pridržana - Jobstep IT Solutions
													</p>
												</footer>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>