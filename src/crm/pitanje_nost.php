
<?php
//ID kampanja
$nalog = $_GET['pid'];
if(isset($_GET['lang']))
	$lang = $_GET['lang'];
else
	$lang = "hr";
if($lang == "bs"){
	$novi_tekst = "Da li imate nostrifikovanu diplomu";
	if($nalog == 1){
		$link_crm = "https://crm.job-step.com/registracija/489/korak1";
		$link_dipl = "";
	}else if($nalog == 2){
		$link_crm = "https://crm.job-step.com/registracija/490/korak1";
		$link_dipl = "";
	}
}elseif($lang == "rs"){
	$novi_tekst = "Da li imate nostrifikovanu diplomu";
	
}

?>
<?php
	//Site URL
	function getSiteUrl() {
		echo "https://crm.job-step.com/";
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
		<script src="https://www.google.com/recaptcha/api.js?render=6LfSU_cUAAAAAHEcaDJVGIJMDW2JEZVM2TDVtq7O"></script>
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

		<!-- TELEFON -->
		<script src="https://crm.job-step.com/js/intlTelInput.js"></script>
		<script src="https://crm.job-step.com/js/intlTelInput-jquery.min.js"></script>
		
		
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
			.tipka_da{
				border: none;
				background-color: #6097a0;
				border-radius: 18px;
				padding: 8px;
				line-height: normal;
				color: #ffffff;
				font-weight: bold;
				
			}
			.tipka_ne{
				border: none;
				background-color: #c31d1dc7;
				border-radius: 18px;
				padding: 8px;
				line-height: normal;
				color: #ffffff;
				font-weight: bold;
			}
			a {
			  color: inherit; /* blue colors for links too */
			  text-decoration: inherit; /* no underline */
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
		
		<div id="content_public">
			
			<script>
				grecaptcha.ready(function () {
					grecaptcha.execute('6LfSU_cUAAAAAHEcaDJVGIJMDW2JEZVM2TDVtq7O', { action: 'nostrifikacija_Kampanje' }).then(function (token) {
						var recaptchaResponse = document.getElementById('recaptchaResponse');
						recaptchaResponse.value = token;
					});
				});
			</script>
			<input type="hidden" id="countryCode" name="countryCode" value="<?php echo $country_code; ?>">
			<input type="hidden" name="recaptcha_response" id="recaptchaResponse">
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
													PITANJE
												</div>
											</div>
											<div class = "row" id = "step_1">
												<div class = "col-xs-12 opis_usluge">
													
													<p>
														<?php echo $novi_tekst; ?>
														<br>
													</p>
													
												</div>
											</div>
											<div class="row" style="margin-bottom: 50px;">
												<div class = "col-xs-12" style="text-align: center;">
													<div class="col-xs-6" style="padding: 0;"><button type="submit" name="dipl_da" class="tipka_da" id="dipl_da" value="da" ><a href="<?php echo $link_crm; ?>">DA</a></button></div>
													<div class="col-xs-6" style="padding: 0;"><button type="submit" name="dipl_ne" class="tipka_ne" id="dipl_ne" value="ne" ><a href="#">NE</a></button></div>
												</div>
											</div>
											
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