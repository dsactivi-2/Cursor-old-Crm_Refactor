<html>
    <!DOCTYPE html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>Jobstep</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<!-- Style -->
		<link href="https://crm.job-step.com/css/bootstrap.min.css" rel="stylesheet">
		<link href="https://crm.job-step.com/css/jasny-bootstrap.min.css" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="https://crm.job-step.com/css/bootstrap-select.css">
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		<link rel="stylesheet" type="text/css" href="https://crm.job-step.com/css/calendar.css" />
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
		<link rel="icon" href="https://crm.job-step.com/images/Jobstep-logo_news.png">

		<!-- Fonts -->
		<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700&amp;subset=latin-ext" rel="stylesheet">
		<script src="https://use.fontawesome.com/758aa0fdaa.js"></script>
		<!-- FLATPICK -->
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
		<link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/airbnb.css">
		<!-- TELEFON -->
		<link rel="stylesheet" href="https://crm.job-step.com/buildTelInput/css/intlTelInput.css">
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
		<!-- TELEFON -->
		<script src="https://crm.job-step.com/buildTelInput/js/intlTelInput.js"></script>
		<!-- <script src="https://crm.job-step.com/js/intlTelInput-jquery.min.js"></script> -->
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

		<?php 
		$lg_language = "bs";
		if(isset($_GET['urlid'])){
			$urlid = $_GET['urlid'];
		}else{
			$urlid = 0;
		}
		
		?>

	</head>
    <?php
   
	include("includes/functions.php");
	?>
	<script>
		$(document).ready(function() {

			$('.selectpicker').selectpicker({});
            var urlid = <?php echo $urlid; ?>;
			$.ajax({
				url: '<?php getCRMUrl(); ?>public_kandidati.php?page=updBrojPregledaLinka',
				type: 'POST',
				data: {"link_id": urlid},
				dataType: 'html',
				success: function(data) {
					
				}
			});

			$.ajax({
				url: '<?php getCRMUrl(); ?>public_kandidati_import.php?page=getLanguageForLink',
				type: 'POST',
				data: {"link_id": urlid},
				dataType: 'html',
				success: function(data) {
                    // console.log(data);
					if(data == "sr"){
						// console.log("srpski");
						$("#njemackog").text("nemačkog");
						$("#lg_smjer1").html("Smer");
						$("#lg_smjer2").html("Smer");
						$("#lg_smjer3").html("smer");
						$("#posljednjih1").html("poslednjih");
						$("#posljednjih2").html("Nemam iskustva u poslednjih 5 godina");
						$("#text_na_dnu").html("<b>JOBSTEP by DATA PROCESS d.o.o.</b>");
					}else{
						// console.log("bosanski");
						$("#njemackog").text("njemačkog");
						$("#lg_smjer1").html("Smjer");
						$("#lg_smjer2").html("Smjer");
						$("#lg_smjer3").html("smjer");
						$("#posljednjih1").html("posljednjih");
						$("#posljednjih2").html("Nemam iskustva u posljednjih 5 godina");
						$("#text_na_dnu").html("<b>JOBSTEP</b>");
					}
				}
			});

			function toggleFormGroup(selector, show) {
				if (show) {
					$(selector).show();
					$(selector).find('input, select, textarea').prop('required', true);
				} else {
					$(selector).hide();
					$(selector).find('input, select, textarea').prop('required', false);
					// Also clear values to avoid accidentally submitting hidden values
					$(selector).find('input[type="text"], input[type="tel"], textarea').val('');
					$(selector).find('select').prop('selectedIndex', 0);
					$(selector).find('input[type="checkbox"], input[type="radio"]').prop('checked', false);
				}
			}

			var urlid = <?php echo (int)$urlid; ?>;
			var putanja = '<?php getCRMUrl(); ?>public_kandidati_import.php?page=getQuestionsForForm';

			$.ajax({
				url: putanja,
				type: 'POST',
				data: {"link_id": urlid},
				dataType: 'json',
				success: function(resp) {
					
					if (!resp) {
						console.warn('getQuestionsForForm: invalid response', resp);
						return;
					}
					// console.log(resp);
					var q = resp.questions; // map: short_name => 0/1
					var nalogMode = resp.nalog_mode == 1;
					var naslov = resp.naslov || '';
					var tekst  = resp.tekst || '';

					if (naslov) {
						$('#form_naslov').text(naslov).show();
					}

					if (tekst) {
						$('#form_tekst').html(tekst).show();
					}

					// JEZIK (short name: 'njemacki')
					toggleFormGroup('#div_za_jezik', !!q['njemacki']);

					// VOZAČKA (short name: 'vozacka')
					toggleFormGroup('#kandidat_kategorija_vozacke_group', !!q['vozacka']);
					
					// ŠKOLE / SMJER (short name: 'obrazovanje')
					toggleFormGroup('#obrazovanje_group', !!q['obrazovanje']);

					// RADNO ISKUSTVO (short names: 'iskustvo' and 'trajanje_isk')
					var isk = !!q['iskustvo'];
					var traj = !!q['trajanje_isk'];
					if (isk && traj) {
						// show full experience w/ duration
						toggleFormGroup('#iskustvo_normal_group', true);
						toggleFormGroup('#iskustvo_only_group', false);
					} else if (isk && !traj) {
						// only yes/no experience (no duration)
						toggleFormGroup('#iskustvo_normal_group', false);
						toggleFormGroup('#iskustvo_only_group', true);
					} else {
						toggleFormGroup('#iskustvo_normal_group', false);
						toggleFormGroup('#iskustvo_only_group', false);
					}

					// DRŽAVLJANSTVO (if you want to use 'drzavljanstvo' short_name — not present in your table)
					// default true unless explicitly provided as 0
					var showDrz = (typeof q['drzavljanstvo'] !== 'undefined') ? !!q['drzavljanstvo'] : true;
					toggleFormGroup('#kandidat_drzavljanstvo_vrsta_group', showDrz);


					// VRIJEME POZIVA (short name: 'vrijeme_poziva')
					toggleFormGroup('#vrijeme_poziva_group', !!q['vrijeme_poziva']);

					if (q['obrazovanje']) {
						if (nalogMode) {
							$('.pick_nalog_schools').show();
							$('.pick_all_schools').hide();
							$("#skola_naziv").prop("required", false).prop("disabled", true);
							$("#smjer_naziv").prop("required", false).prop("disabled", true);

							$("#nalog_smjer_naziv").prop("required", true).prop("disabled", false);
						} else {
							$('.pick_all_schools').show();
							$('.pick_nalog_schools').hide();
							$("#nalog_smjer_naziv").prop("required", false).prop("disabled", true);

							$("#skola_naziv").prop("required", true).prop("disabled", false);
							$("#smjer_naziv").prop("required", true).prop("disabled", false);
						}
					}

					// --- DODATNA PITANJA RENDER ---
					var dp = resp.dodatna_pitanja || [];
					var $wrap = $('#dodatna_pitanja_wrap');
					var $list = $('#dodatna_pitanja_list');

					$list.empty();

					if (dp.length > 0) {
					$wrap.show();

					dp.forEach(function(item) {
						var dpId = parseInt(item.dp_id, 10);
						var tekst = item.dp_tekst || '';

						// escape text
						var safeText = $('<div>').text(tekst).html();

						var idDa = 'dp_' + dpId + '_da';
						var idNe = 'dp_' + dpId + '_ne';

						var rowHtml = `
						<div class="form-group" style="display:flex; align-items:center;">
							<label class="col-sm-5 control-label"><strong>${safeText}</strong>:</label>

							<div class="col-sm-3">
							<label class="main-container__column material-radio-group material-radio-group_success" for="${idDa}">
								<input type="radio" name="userdp[${dpId}]" id="${idDa}" class="material-radiobox" value="1"/>
								<span class="material-radio-group__element material-radio-group__check-radio"></span>
								<span class="material-radio-group__element material-radio-group__caption">Da</span>
							</label>
							</div>

							<div class="col-sm-4">
							<label class="main-container__column material-radio-group material-radio-group_success" for="${idNe}">
								<input type="radio" name="userdp[${dpId}]" id="${idNe}" class="material-radiobox" value="0" checked />
								<span class="material-radio-group__element material-radio-group__check-radio"></span>
								<span class="material-radio-group__element material-radio-group__caption">Ne</span>
							</label>
							</div>
						</div>
						`;

						$list.append(rowHtml);
					});

					} else {
						$wrap.hide();
					}

					
				},
				error: function(xhr, status, err) {
					console.error('getQuestionsForForm AJAX error', status, err);
					// optional: keep defaults if AJAX fails
				}
			});

		});

	</script>
	
	<?php
	
	//DEFAULTNA FORMA
	$show_jezik 			= true;
	$show_vozacka			= true;
	$show_skole				= true;
	$show_iskustvo_trajanje	= true;
	$show_drzavljanstvo		= true;
	$show_vrijeme_poziva	= true;
	$show_only_iskustvo		= true;
	
	$language_group_form = 
		'<select class="select-posao" id="kandidat_jezik" name="nivo_jezika"  data-live-search="true" required>
			<option value selected disabled>Odaberi...</option>
			<option value="Bez znanja">Bez znanja</option>
			<option value="A1">A1</option>
			<option value="A2">A2</option>
			<option value="B1">B1</option>
			<option value="B2">B2</option>
			<option value="C1">C1</option>
			<option value="C2">C2</option>
		</select>'
	;
	$div_za_jezik = '
		<div class="form-group" id="div_za_jezik">
			<label for="kandidat_jezik" class="col-sm-5 control-label"><strong>Nivo poznavanja <span id="njemackog"></span> jezika</strong><span class="zvjezdica">*</span><strong>:</strong></label>
			<div class="col-sm-6">
				'.$language_group_form.'
			</div>
		</div>';
		
	if(isset($_SERVER['HTTP_REFERER'])) {
		$kandidat_visitedurl = $_SERVER["HTTP_REFERER"];
	}else{
		$kandidat_visitedurl = "";
	}
	// GET PARTNER TOKEN
	if(isset($_GET['token'])){
		$token_c = $_GET['token'];
		$token = base64_decode($token_c);
		$token_zaurl = "/".$token_c;
	}else{
		$token = NULL;
		$token_zaurl = "";
	}
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
		$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	//echo $ip;
	
	// $ipdat = @json_decode(file_get_contents( 
	// 	"http://www.geoplugin.net/json.gp?ip=" . $ip)); 
	   
	// $countryCode = strtolower($ipdat->geoplugin_countryCode);
	?>
	<style>
		.korak1 {
			text-align: left;
			color: #6097A0;
            border-bottom: 1px solid #0000002e;
            padding: 0;
        }
        .korak1 h4 {
            font-weight: bold;
        }
		.korak2 {
			text-align: right;
			color: #0000002e;
            border-bottom: 1px solid #0000002e;
            padding: 0;
		}
        .korak2 h4 {
            font-weight: bold;
        }
        @media (max-width: 991px) {
            .input, .select-posao {
                width: 100% !important;
            }
        }
		.zvjezdica {
			color: #6097A0;
		}
		.select-posao {
			width: 100%;
			height: 42px;
			border: 1px solid rgb(0 0 0 / 74%);
			background-color: #FFFFFF;
            padding: 5px;
		}
		.input {
			width: 100%;
			height: 42px;
            padding: 5px;
			border: 1px solid rgb(0 0 0 / 74%);
		}
		.dan-select {
			height: 42px;
			border: 1px solid rgb(0 0 0 / 74%);
			background-color: #FFFFFF;
            padding: 5px;
		}
		.mjesec-select {
			height: 42px;
			border: 1px solid rgb(0 0 0 / 74%);
			background-color: #FFFFFF;
            padding: 5px;
		}
		.godina-select {
			height: 42px;
			border: 1px solid rgb(0 0 0 / 74%);
			background-color: #FFFFFF;
            padding: 5px;
		}
		.material-radio-group_success .material-radio-group__check-radio {
			border-color: #6097A0 !important;
		}
		.material-radio-group_success .material-radio-group__check-radio:after {
			background-color: #3A4053 !important;
		}
		.nastavi {
			width: 202px;
			height: 42px;
			background: #6097A0;
			color: #FFFFFF;
			font-weight: bold;
			margin-top: 10px;
            border: 0;
		}
		.icon {
			background: url('/images/Vector.svg');
			width: 24px;
			height: 24px;
		}
		.bootstrap-select {
			width: 100% !important;
			height: 42px !important;
			border: 1px solid rgba(17, 17, 19, 0.2) !important;
			border-radius: 4px;
			background-color: #FFFFFF;
		}
		.bootstrap-select > .dropdown-toggle {
			width: 100%;
			padding-right: 25px;
			z-index: 1;
			height: 100%;
		}
        #prijavi_se_anchor {
            right: -100px;
        }
		@media (max-width: 991px) {
			.select-posao {
				width: 85vw;
				height: 50px;
			}
			.bootstrap-select {
				width: 85vw !important;
				height: 50px !important;
			}
			.input {
				width: 85vw;
				height: 50px;
			}
			.dan-select {
				width: 25vw;
				height: 50px;
			}
			.mjesec-select{
				width: 25vw;
				height: 50px;
			}
			.godina-select{
				width: 30vw;
				height: 50px;
			}

            #prijavi_se_anchor {
                height: 40px;
                display: block;
                background: #f4d82f;
                color: black;
                position: fixed;
                bottom: 100px;
                font-weight: bold;
                padding: 10px;
                right: 0px;
                line-height: 20px;
                z-index: 9999;
                cursor: pointer;
                text-decoration: none;
                transition: right .2s ease-out;
            }
		}
								
	</style>
	<body>
		<div id="content_public">
			<div class="container">
				<!-- LOGO -->
				<div class="row">
					<div class="col-xs-12 text-center">
						<img id="jobstep_logo" class="img-responsive" src="https://crm.job-step.com/images/Jobstep_logo_new.png" alt="" style="max-width:200px;">
					</div>
					<div class="col-xs-12">
						<br />
					</div>
				</div>
                <br>

				<!-- HEAD TEXT -->
				<div class="row" id="anchor_prijavna_forma">
					<div class="col-sm-7">
						
						<h3><strong id="form_naslov">Prijava za informacije</strong></h3>
						<p id="form_tekst">Ostavite podatke i mi ćemo Vas kontaktirati.</p>
						<?php	/* 
						<h3><strong>Prijava za posao</strong></h3>
						<p>Da bi ste se prijavili za posao, popunite navedenu formu</p>
						<?php */?>
					</div>
				</div>
				<hr><br>
					
				<div class="row" style="margin-top: 15px;">
					<div class="col-md-offset-2 col-md-8">
						<form action="<?php getCRMUrl(); ?>public_kandidati?page=add_kandidat_new" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
							<input type="hidden" name="kandidat_visitedurl" value="<?php echo $kandidat_visitedurl; ?>">
							<input type="hidden" name="urlid" value="<?php echo $urlid; ?>">
							<input type="hidden" name="token" value="<?php echo $token; ?>">
							<input type="hidden" name="lg_language" value="<?php echo $lg_language; ?>">
							
                            <div class="form-group" style="display: none;">
                            	<!-- Kandidat_prijava_na ili grupa će uvijek biti sakrivena, api-em će se kupiti ono sto je naznačeno u linku i ako
                                postoji više grupa biće selektovana prva. -->
								<label for="kandidat_prijava_na" class="col-sm-5 control-label"><strong>RADNO MJESTO NA KOJE SE PRIJAVLJUJETE</strong><span class="zvjezdica">*</span><strong>:</strong></label>
								<div class="col-sm-6">
									<select class="select-posao" id="kandidat_prijava_na" name="kandidat_prijava_na" >
										<?php 
											if($urlid != 0){
												$putanja_return = "return_groups_select";
											}else{
												$putanja_return = "return_all_groups_select";
											}
										
										?>
										<script>
											$(document).ready(function() {
												var urlid = <?php echo $urlid; ?>;
												var putanja = '<?php getCRMUrl(); ?>' + 'public_kandidati_import.php?page=' + '<?php echo $putanja_return; ?>';
												$.ajax({
													url: putanja,
													type: 'POST',
													data: {"link_id": urlid},
													dataType: 'html',
													success: function(data) {
														$('#kandidat_prijava_na').append(data);
													}
												});
											});
												
										</script>
									</select>
								</div>
							</div>

							<!-- IME -->
							<div class="form-group">
								<label for="kandidat_ime" class="col-sm-5 control-label"><strong>Ime</strong><span class="zvjezdica">*</span><strong>:</strong></label>
								<div class="col-sm-6">
									<input class="input" type="text" name="kandidat_ime" id="kandidat_ime" placeholder="Ime" required>
								</div>
							</div>

							<!-- PREZIME -->
							<div class="form-group">
								<label for="kandidat_prezime" class="col-sm-5 control-label"><strong>Prezime</strong><span class="zvjezdica">*</span><strong>:</strong></label>
								<div class="col-sm-6">
									<input class="input" type="text" name="kandidat_prezime" id="kandidat_prezime" placeholder="Prezime" required>
								</div>
							</div>

							<!-- DATUM RODJENJA -->
							<div class="form-group">
								<label for="datum_dan" class="col-sm-5 control-label"><strong>Datum rođenja</strong><span class="zvjezdica">*</span><strong>:</strong></label>
								<div class="col-sm-6">
                                    <select class="dan-select" name="datum_dan" id="datum_dan" required>
                                        <option value="" selected disabled>DD</option>
                                        <?php 
                                        for ($day=1; $day<=31; $day++){ ?>
                                            <option value="<?php echo $day; ?>"><?php echo $day; ?></option>
                                        <?php } ?>
                                    </select>
                                    
                                    <select class="mjesec-select" name="datum_mjesec" id="datum_mjesec" required>
                                        <option value="" selected disabled>MM</option>
                                        <?php 
                                        for ($month=1; $month<=12; $month++){ ?>
                                            <option value="<?php echo $month; ?>"><?php echo $month; ?></option>
                                        <?php } ?>
                                    </select>

                                    <select class="godina-select" name="datum_godina" id="datum_godina" required>
                                        <option value="" selected disabled>GGGG</option>
                                        <?php 
                                        for ($year=2006; $year>1940; $year--){ ?>
                                            <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                                        <?php } ?>
                                    </select>
								</div>
							</div>
							
							<!-- TELEFON -->
							<div class="form-group">
								<label for="kki_phone" class="col-sm-5 control-label"><strong>Mobilni telefon</strong><span class="zvjezdica">*</span><strong>:</strong></label>
								<div class="col-sm-6">
									<div class="">
										<input class="input" type="tel" name="kki_phone" id="kki_phone" placeholder="61 123 456" required>
									</div>
								</div>
							</div>
							<script>
								$( document ).ready(function($) {
									
									var telInput = document.querySelector("#kki_phone");

									iti = window.intlTelInput(telInput, {
										utilsScript: "https://crm.job-step.com/buildTelInput/js/utils.js",
										autoPlaceholder: "aggressive",
										preferredCountries: ["ba","hr","de","it","rs"],
										formatOnDisplay: true,
										separateDialCode: true
									});
									var fullNumber = iti.getNumber();
									$('input[type=tel]').on('change', function() {
                                        console.log(iti.getNumber());
                                    });
									$("form").submit(function(event) {
										
										$("#kki_phone").val(iti.getNumber()); 
									});
									
								});
							</script>
                            
                            <!-- NJEMACKI JEZIK -->
							<?php if($show_jezik){
								echo $div_za_jezik; 
							} 
							
                            // <!-- KATEGORIJA VOZAČKE -->
							if($show_vozacka){
								?>
								<div class="form-group" style="display: block; " id="kandidat_kategorija_vozacke_group">
									<label for="kandidat_kategorija_vozacke" class="col-sm-5 control-label"><strong>Da li imate vozačku dozvolu:</strong><span class="zvjezdica">*</span><strong>:</strong></label>
									<div class="col-sm-6">
										<select class="select-posao" id="kandidat_kategorija_vozacke" name="kandidat_kategorija_vozacke" data-live-search="true" required >
											<option value selected disabled>Odaberi...</option>
											<option value="Ne">Ne</option>
											<option value="B">B</option>
											<option value="C1">C1</option>
											<option value="C">C</option>
											<option value="BE">BE</option>
											<option value="C1E">C1E</option>
											<option value="CE" >CE</option>
										</select>
									</div>
								</div>
								<?php
							}
                               
							// <!-- SKOLE -->
                            if($show_skole){
                                ?>
								<div id="obrazovanje_group">
                                <div class="pick_all_schools" style="display:none;">
                                    <div class="form-group" id="skola_select">
                                        <label for="skola_naziv" class="col-sm-5 control-label"><strong>Završeno obrazovanje</strong><span class="zvjezdica">*</span><strong>:</strong></label>
                                        <div class="col-sm-6">
                                            <select class="select-posao" name="skola_naziv" id="skola_naziv" data-live-search="true">
                                                <option value="" selected disabled>Odaberi...</option>
                                                <script>
                                                    $(document).ready(function() {
                                                        $.ajax({
                                                            url: '<?php getCRMUrl(); ?>' + 'public_kandidati_import.php?page=return_skole',
                                                            type: 'POST',
                                                            data: {},
                                                            dataType: 'html',
                                                            success: function(data) {
                                                                $('#skola_naziv').append(data);
                                                            }
                                                        });
                                                    });
                                                        
                                                </script>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group" id="smjer_select">
                                        <label for="smjer_naziv" class="col-sm-5 control-label"><strong>Zvanje/<span id="lg_smjer2"></span></strong><span class="zvjezdica">*</span><strong>:</strong></label>
                                        <div class="col-sm-6">
                                            <select class="select-posao" name="smjer_naziv" id="smjer_naziv" data-live-search="true" >
                                                <option value="" selected disabled>Odaberi</option>
                                                <script>
                                                    $(document).ready(function() {
                                                        $.ajax({
                                                            url: '<?php getCRMUrl(); ?>' + 'public_kandidati_import.php?page=return_smjerove',
                                                            type: 'POST',
                                                            data: {},
                                                            dataType: 'html',
                                                            success: function(data) {
                                                                $('#smjer_naziv').append(data);
                                                            }
                                                        });
                                                    });
                                                        
                                                </script>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="pick_nalog_schools" style="display:none;">
                                    <div class="form-group" id="smjer_select">
                                        <label for="smjer_naziv" class="col-sm-5 control-label"><strong>Završeno obrazovanje/<span id="lg_smjer3"></span></strong><span class="zvjezdica">*</span><strong>:</strong></label>
                                        <div class="col-sm-6">
                                            <select class="select-posao" name="smjer_naziv" id="nalog_smjer_naziv" data-live-search="true" >
                                                <option value="" selected disabled>Odaberi</option>
                                                <?php 
                                                    $ch = curl_init();
                                                    $data = array("urlid" => $urlid);
                                                    $json = json_encode($data);
													$urlForCurl = getCRMUrlr() . 'liliumAPI/API.php/OrderSchools';
                                                    curl_setopt($ch,CURLOPT_URL, $urlForCurl);
                                                    curl_setopt($ch,CURLOPT_POSTFIELDS, $json );
                                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                                        "accept: application/json",
                                                        "authorization: Bearer 4d861ddc-7ba4-11ed-a1eb-0242ac120002",
                                                        "content-type: application/json"
                                                    ));
                    
                                                    $result = curl_exec($ch);

                                                    //close connection
                                                    curl_close($ch);
                                                    $nalog_smjerovi = json_decode($result, TRUE);
                                                    foreach($nalog_smjerovi as $nalog_smjer){
                                                        echo '<option value="'.$nalog_smjer['id'].'">'.$nalog_smjer['name'].'</option>';
                                                    }
                                                    echo '<option value="ostalo">Ostalo</option>';
                                                ?>
                                            </select> 
                                        </div>
                                    </div>
                                </div>
								<!-- Ručni unos škole i smjera -->
									<div class="form-group" id="unos_skole" style="display: none;">
										<label for="skola_naziv_ru" class="col-sm-5 control-label"><strong>Naziv škole:</strong></label>
										<div class="col-sm-6">
											<input class="input" type="text" name="skola_naziv_ru" id="skola_naziv_ru" placeholder="Naziv škole" >
										</div>
									</div>
									<div class="form-group" id="unos_smjera" style="display: none;">
										<label for="smjer_naziv_ru" class="col-sm-5 control-label"><strong>Unesite smjer:</strong></label>
										<div class="col-sm-6">
											<input class="input" type="text" name="smjer_naziv_ru" id="smjer_naziv_ru" placeholder="Unesite smjer" >
										</div>
									</div>
								<!-- Ručni unos škole i smjera -->
								</div>
							    <?php
                            }
                            
							// <!-- RADNO ISKUSTVO U STRUCI I TRAJANJE -->
							if($show_iskustvo_trajanje){
								?>
								<div id="iskustvo_normal_group">
								<!-- RADNO ISKUSTVO U STRUCI -->
								<div class="form-group" id="iskustvo_u_struci_group" style="display: block">
									<label for="kandidat_iskustvo_u_struci" class="col-sm-5 control-label"><strong>Da li imate radnog iskustva u struci?</strong><span class="zvjezdica">*</span></label>
									<div class="col-sm-3">
										<label class="main-container__column material-radio-group material-radio-group_success" for="iskustvo_u_struci_da">
											<input type="radio" name="kandidat_iskustvo_u_struci" id="iskustvo_u_struci_da" class="material-radiobox" value="1"/>
											<span class="material-radio-group__element material-radio-group__check-radio"></span>
											<span class="material-radio-group__element material-radio-group__caption">Da</span>
										</label>
									</div>
									<div class="col-sm-4">
										<label class="main-container__column material-radio-group material-radio-group_success" for="iskustvo_u_struci_ne">
											<input type="radio" name="kandidat_iskustvo_u_struci" id="iskustvo_u_struci_ne" class="material-radiobox" value="0" checked />
											<span class="material-radio-group__element material-radio-group__check-radio"></span>
											<span class="material-radio-group__element material-radio-group__caption">Ne</span>
										</label>
									</div>
								</div>

								<div class="form-group" id="iskustvo_u_struci_trajanje_group" style="display: none;">
									<label for="iskustvo_u_struci_trajanje" class="col-sm-5 control-label"><strong>Radno iskustvo u struci u <span id="posljednjih1"></span> 5 godina?</strong><span class="zvjezdica">*</span></label>
									<div class="col-sm-6">
										<select class="select-posao" id="iskustvo_u_struci_trajanje" name="iskustvo_u_struci_trajanje">
											<option value="" selected disabled>Odaberi...</option>
											<option id="posljednjih2" value="0">Nemam iskustva u <span ></span> 5 godina</option>
											<option value="1">Manje od 1 godine</option>
											<option value="2">1 godinu</option>
											<option value="3">2 godine</option>
											<option value="4">3 godine</option>
											<option value="5">4 godine</option>
											<option value="6">5 godina</option>
										</select>
									</div>
								</div>

								<script>
									$('#iskustvo_u_struci_da').click(function() {
										if($('#iskustvo_u_struci_da').is(':checked')) { 
											$('#iskustvo_u_struci_trajanje_group').show();
											$("#iskustvo_u_struci_trajanje").prop('required',true);
										}
									});
									$('#iskustvo_u_struci_ne').click(function() {
										if($('#iskustvo_u_struci_ne').is(':checked')) { 
											$('#iskustvo_u_struci_trajanje_group').hide();
											$("#iskustvo_u_struci_trajanje").prop('required',false);
										}
									});
								</script>
								</div>
								<?php 
							}

							// <!-- RADNO ISKUSTVO U STRUCI SAMO DA/NE BEZ TRAJANJA -->
							if($show_only_iskustvo){ ?>
								<div id="iskustvo_only_group">
								<!-- <input type="hidden" name="radnoIskustvoBiljeska" value="1"> -->
								<div class="form-group" id="iskustvo_u_struci_group" style="display: block">
									<label for="kandidat_iskustvo_u_struci" class="col-sm-5 control-label"><strong>Da li imate (svježeg) radnog iskustva u <?php echo $iskustvo_u_cemu; ?>?</strong><span class="zvjezdica">*</span></label>
									<div class="col-sm-3">
										<label class="main-container__column material-radio-group material-radio-group_success" for="iskustvo_u_struci_da_only">
											<input type="radio" name="kandidat_iskustvo_u_struci" id="iskustvo_u_struci_da_only" class="material-radiobox" value="1"/>
											<span class="material-radio-group__element material-radio-group__check-radio"></span>
											<span class="material-radio-group__element material-radio-group__caption">Da</span>
										</label>
									</div>
									<div class="col-sm-4">
										<label class="main-container__column material-radio-group material-radio-group_success" for="iskustvo_u_struci_ne_only">
											<input type="radio" name="kandidat_iskustvo_u_struci" id="iskustvo_u_struci_ne_only" class="material-radiobox" value="0" checked />
											<span class="material-radio-group__element material-radio-group__check-radio"></span>
											<span class="material-radio-group__element material-radio-group__caption">Ne</span>
										</label>
									</div>
								</div>
								</div>
								<?php 
							}

                            // <!-- DRZAVLJANSTVO -->
							if($show_drzavljanstvo){
								?>
								<div class="form-group" id="kandidat_drzavljanstvo_vrsta_group">
									<label for="kandidat_drzavljanstvo_vrsta" class="col-sm-5 control-label"><strong>Državljanstvo</strong><span class="zvjezdica">*</span><strong>:</strong></label>
									<div class="col-sm-3">
										<div class="materail-input-block materail-input-block_success idk_radio_buttons">
											<label class="main-container__column material-radio-group material-radio-group_success" for="kandidat_drzavljanstvo_vrsta_eu">
												<input type="radio" name="kandidat_drzavljanstvo_vrsta" id="kandidat_drzavljanstvo_vrsta_eu" class="material-radiobox" value="EU državljanin" />
												<span class="material-radio-group__element material-radio-group__check-radio"></span>
												<span class="material-radio-group__element material-radio-group__caption">EU-državljanin</span>
											</label>
										</div>
									</div>
									<div class="col-sm-4">
										<div class="materail-input-block materail-input-block_success idk_radio_buttons">
											<label class="main-container__column material-radio-group material-radio-group_success" for="kandidat_drzavljanstvo_vrsta_non">
												<input type="radio" name="kandidat_drzavljanstvo_vrsta" id="kandidat_drzavljanstvo_vrsta_non" class="material-radiobox" value="NON-EU državljanin" checked />
												<span class="material-radio-group__element material-radio-group__check-radio"></span>
												<span class="material-radio-group__element material-radio-group__caption">NON-EU državljanin</span>
											</label>
										</div>
									</div>
								</div>
								<?php 
							}
							
							?>

							<script>
								$(document).ready(function() {
									jezik = $(document).find('input[name="lg_language"]').val();
									
								});
								
								$('#skola_naziv').on('change', function() {
									$('.smjerovi_all').addClass('hidden');
									var skola_id = $(this).find(':selected').data('skola_id');
									$('.opt_'+skola_id+'').removeClass('hidden');
									optionSrednja =  $(this).val();
									if(optionSrednja == "ostalo"){
										$('#smjer_step1').hide();
										$('#smjer_select').hide();
										$('#unos_skole').show();
										$('#unos_smjera').show();
										$("#smjer_naziv").prop('required',false);
										$("#skola_naziv_ru").prop('required',true);
										$("#smjer_naziv_ru").prop('required',true);
									}else{
										$('#smjer_step1').show();
										$('#smjer_select').show();
										$('#unos_skole').hide();
										$('#unos_smjera').hide();
										$("#smjer_naziv").prop('required',true);
										$("#skola_naziv_ru").prop('required',false);
										$("#smjer_naziv_ru").prop('required',false);
									}
									
								});

                                $('#nalog_smjer_naziv').on('change', function() {
                                    optionSmjer = $(this).val();
                                    if(optionSmjer == "ostalo"){
                                        $('#unos_smjera').show();
                                        $('#unos_skole').show();
										$("#skola_naziv_ru").prop('required',true);
										$("#smjer_naziv_ru").prop('required',true);
                                    }else{
                                        $('#unos_smjera').hide();
                                        $('#unos_skole').hide();
										$("#skola_naziv_ru").prop('required',false);
										$("#smjer_naziv_ru").prop('required',false);
                                    }
                                });
								
							</script>
							
							<!-- DODATNA PITANJA (dynamic render from AJAX) -->
							<div id="dodatna_pitanja_wrap" style="display:none; margin-top:20px;">
								

								<div id="dodatna_pitanja_list"></div>
							</div>

							<!-- VRIJEME ZA POZIV --> <?php
							if($show_vrijeme_poziva){
								?>
								<div class="form-group" style="display: block;" id="vrijeme_poziva_group">
									<label for="vrijeme_poziva" class="col-sm-5 control-label"><strong>U koje vrijeme Vam odgovara da Vas nazovemo?</strong><span class="zvjezdica">*</span></label>
									<div class="col-sm-6">
										<select class="select-posao" id="vrijeme_poziva" name="vrijeme_poziva" required>
											<option value="" selected disabled>Odaberi...</option>
											<option value="Bilo kad">Bilo kad</option>
											<option value="Iza 10 sati.">Iza 10 sati.</option>
											<option value="Iza 11 sati.">Iza 11 sati.</option>
											<option value="Iza 12 sati.">Iza 12 sati.</option>
											<option value="Iza 13 sati.">Iza 13 sati.</option>
											<option value="Iza 14 sati.">Iza 14 sati.</option>
											<option value="Iza 15 sati.">Iza 15 sati.</option>
											<option value="Iza 16 sati.">Iza 16 sati.</option>
											<option value="Iza 17 sati.">Iza 17 sati.</option>
										</select>
									</div>
								</div>
								<?php
							} ?>

							<br />

							<div class="form-group">
								<div class="col-sm-offset-2 col-sm-10 text-right">
									
										<button id="button_nastavi" type="submit" class="nastavi">POŠALJI</button>							
									
									<br /><small>Sva polja označena sa <span class="zvjezdica">*</span> su obavezna</small>
								</div>
							</div>
							<div class="form-group">
								<label for="asd" class="col-sm-3 control-label"><span class="text-danger"></span></label>
								
								<div class="col-sm-6" style="padding-top: 10px;">
									
									<p class = "text-center"> Nastavkom prihvatate uslove o zaštiti privatnosti.
									<br><a href="" type="button" data-toggle="modal" data-target="#privacyModal">Pogledaj izjavu o zaštiti privatnosti</a>
									</p>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-12">
									<p class = "text-center" id="text_na_dnu"></p>
								</div>
							</div>
							<!-- Modal privacy -->
							<div class="modal fade modal-fullscreen" id="privacyModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
								<div class="modal-dialog" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<h5 class="modal-title" id="exampleModalLabel">Izjava o zaštiti privatnosti</h5>
										</div>
										<div class="modal-body">
											<p>Slanjem ovog obrasca dajete svoju saglasnost da Vaši podaci budu registrovani u našoj bazi podataka, pri čemu će isti biti dostupni pravnom licu doo Jobstep International i njegovim poslovnim saradnicima (partnerima). Jobstep International doo obavezan je čuvati privatnost svojih korisnika i osigurati visok stepen sigurnosti i povjerljivosti ličnih podataka prikupljenih od potencijalnih kandidata, a u skladu sa Zakonom o zaštiti ličnih podataka BiH i drugim relevantnim domaćim i evropskim zakonima i podzakonskim aktima. Lični podaci koji budu predmet obrade predstavljaju službenu tajnu. Lični i svi drugi podaci iz ovog obrasca koje kandidat unese u obrascu ili u bilo kojoj drugoj formi, biće korišteni isključivo od strane ovlaštenih lica u procesu procjene kandidata i posredovanja prilikom zapošljavanja.</p>
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-secondary" data-dismiss="modal">Zatvori</button>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
				<footer>
					<?php
					echo "<p>©" . date('Y') . " Sva prava pridržana - Jobstep IT Solutions</p>";
					?>
				</footer>
			</div>
		</div>
	</body>
</html>
