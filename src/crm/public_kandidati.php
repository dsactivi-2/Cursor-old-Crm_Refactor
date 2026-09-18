<?php
header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Methods: GET, POST');

header("Access-Control-Allow-Headers: X-Requested-With");
?>
<script src="https://www.google.com/recaptcha/api.js?render=6LfSU_cUAAAAAHEcaDJVGIJMDW2JEZVM2TDVtq7O"></script>
<?php
	include("includes/functions.php");
	
	require 'mail/PHPMailerAutoload.php';
	$getEmployeeStatus = getEmployeeStatus();

	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: registracija/korak1");
	}

	//Get project
	if(isset($_GET["urlid"])) {
		$lg_id = $_GET["urlid"];

		$query_project = $db->prepare("
						SELECT lp_linkid, lp_projectid
						FROM idk_link_projects
						WHERE lp_linkid = :lg_id");

		$query_project->execute(array(
						":lg_id" => $lg_id));

		$projects = array();
		
		while($row_project = $query_project->fetch()){
			$projects[] = $row_project['lp_projectid'];
		}
	}
		
	// IF Step 1 get language from URLID, else get language from canndidate check id
	if(isset($_GET["urlid"])){
		$lg_id = $_GET["urlid"];
	}else{
		if(isset($_REQUEST['check'])){
			// GET THE VISITED URL FROM CANDIDATE CHECK ID
			$candidate_check_url = $db->prepare("
							SELECT kandidat_visitedurl
							FROM idk_kandidati
							WHERE kandidat_check = :kandidat_check");
		
			$candidate_check_url->execute(array(
							":kandidat_check" => $_REQUEST['check']));
			
			$cUrlId = $candidate_check_url->fetch();
			$lg_id = $cUrlId['kandidat_visitedurl'];
		}
		else
			$lg_id = 0;
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
	
	$ipdat = @json_decode(file_get_contents( 
		"http://www.geoplugin.net/json.gp?ip=" . $ip)); 
	   
	$countryCode = strtolower($ipdat->geoplugin_countryCode);
	if($countryCode == "")
		$countryCode = "ba";
	
	if(isset($_POST['lang']))
		$choosen_lang  = $_POST['lang'];
	else
		$choosen_lang = "";
	
	// CHECK THE SELECTED LANGUAGE FOR URL
	$query_language = $db->prepare("
					SELECT lg_language, lg_nalogid
					FROM idk_link_generator
					WHERE lg_id = :lg_id");

	$query_language->execute(array(
					":lg_id" => $lg_id));
	
	$language = $query_language->fetch();
	$lg_language = $language['lg_language'];
	$lg_nalogid = $language['lg_nalogid'];
	
	if($countryCode == "de" OR $choosen_lang == "de"){
		include("lang/de.php");
		$lg_nalogid = $language['lg_nalogid'];
		$lg_language = "de";
	}else{
		if(isset($lg_language)){
			include("lang/".$lg_language.".php");
			$lg_nalogid = $language['lg_nalogid'];
		}
		else{
			include("lang/bs.php");
			$lg_nalogid = null;
		}
	}
	
	// POKUPI PITANJA I KORAKE ZA PRIJAVU ZA ODREĐENI Nalog
	$query_pitanja = $db->prepare("
					SELECT *
					FROM idk_nalozi_blokovi_prijave
					WHERE nbp_nalogid = :nbp_nalogid
	");
	$query_pitanja->execute(array(
					":nbp_nalogid" => $lg_nalogid
	));
	
	$nalog_pitanja = $query_pitanja->fetch();
	
	if($query_pitanja->rowCount() > 0){
		$nbp_vozacka = $nalog_pitanja["nbp_vozacka"];
		$nbp_korak4 = $nalog_pitanja["nbp_korak4"];
		$nbp_ostali_jezici = $nalog_pitanja["nbp_ostali_jezici"];
		$nbp_korak7 = $nalog_pitanja["nbp_korak7"];
		$nbp_slika = $nalog_pitanja["nbp_slika"];
		$nbp_diploma = $nalog_pitanja["nbp_diploma"];
		$nbp_pripravnicki = $nalog_pitanja["nbp_pripravnicki"];
		$nbp_strucni = $nalog_pitanja["nbp_strucni"];
		$nbp_jezik_cert = $nalog_pitanja["nbp_jezik_cert"];
	}else{
		$nbp_vozacka = "block";
		$nbp_korak4 = "1";
		$nbp_ostali_jezici = "block";
		$nbp_korak7 = "1";
		$nbp_slika = "1";
		$nbp_diploma = "1";
		$nbp_pripravnicki = "1";
		$nbp_strucni = "1";
		$nbp_jezik_cert = "1";
	}
$test_cc = array('ba','de','rs','hr','me','si','at','xk');
if (in_array($countryCode, $test_cc)){	
	
?>
<!DOCTYPE html>
<html>
<head>
	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-136019842-1"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments);}
	  gtag('js', new Date());

	  gtag('config', 'UA-136019842-1');
	  
	</script>
	<!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-TVGL3L5');</script>
	<!-- End Google Tag Manager -->
	
	<!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-K3553FC');</script>
	<!-- End Google Tag Manager -->
	
	
	
	<meta name="facebook-domain-verification" content="8cerw3utni6tern5oshym5369ii05o" />
	
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php getTitle(); ?></title>
	
			<?php if($_REQUEST['page'] != "campaignStatistics") // QUICKFIX ZBOG STATISTIKE DIPL KAMPANJA, VANJSKIM SARADNICIMA --- ISMAIL 
			include('includes/head.php'); ?>
	
</head>
<body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TVGL3L5"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-K3553FC"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
	<style>
		.grecaptcha-badge{
			display:none !important;
		}
	</style>
	<div id="content_public">
			<!---<div class="row">
                <div class="col-xs-12 text-center" style="background: linear-gradient(265.05deg, #3A4053 4%, #6097A0 87.25%); height: 251px;">
                    <img class="img-responsive" src="<?php getSiteURL(); ?>images/jslogotrans.png" alt="" style="max-width:200px; margin-top: 30px;">
                </div>
                <div class="col-xs-12">
                    <br />
                </div>
            </div>--->
	<script>
				grecaptcha.ready(function () {
					grecaptcha.execute('6LfSU_cUAAAAAHEcaDJVGIJMDW2JEZVM2TDVtq7O', { action: 'public_kandidat_new' }).then(function (token) {
						var recaptchaResponse = document.getElementById('recaptchaResponse');
						recaptchaResponse.value = token;
					});
				});
			</script>
		<div class="container">
			<?php if($_REQUEST['page'] != "campaignStatistics"){ // QUICKFIX ZBOG STATISTIKE DIPL KAMPANJA, VANJSKIM SARADNICIMA --- ISMAIL ?>
            <div class="row">
                <div class="col-xs-12 text-center">
                    <img class="img-responsive" src="<?php getSiteURL(); ?>images/Jobstep_logo_new.png" alt="" style="max-width:200px;">
                </div>
                <div class="col-xs-12">
                    <br />
                </div>
            </div>
			<?php } // QUICKFIX END --- ISMAIL ?>
		<?php
			switch ($page){

				case "step1_stari":
				//15.2.2022 stavljena nova forma
				if(isset($_GET['urlid'])){
					$urlid = $_GET['urlid'];
				}else{
					$urlid = 0;
				}

				if(isset($_SERVER['HTTP_REFERER'])) {
  					$kandidat_visitedurl = $_SERVER["HTTP_REFERER"];
   				}else{
  					$kandidat_visitedurl = "";
				}
				
				//UPDATE BROJ PREGLEDA ZA LINK
				$upd_bp_link = $db->prepare("
							UPDATE idk_link_generator
							SET lg_broj_pregleda = lg_broj_pregleda + 1
							WHERE lg_id = :lg_id");

				$upd_bp_link->execute(array(
						':lg_id' => $urlid));
			
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
				
				$ipdat = @json_decode(file_get_contents( 
					"http://www.geoplugin.net/json.gp?ip=" . $ip)); 
				   
				$countryCode = strtolower($ipdat->geoplugin_countryCode); 
				
				
		?>
			<!-- Facebook Pixel Code -->
			<script>
			!function(f,b,e,v,n,t,s)
			{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
			n.callMethod.apply(n,arguments):n.queue.push(arguments)};
			if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
			n.queue=[];t=b.createElement(e);t.async=!0;
			t.src=v;s=b.getElementsByTagName(e)[0];
			s.parentNode.insertBefore(t,s)}(window,document,'script',
			'https://connect.facebook.net/en_US/fbevents.js');
			 fbq('init', '1231906870601181'); 
			fbq('track', 'PageView');
			</script>
			<noscript>
			 <img height="1" width="1" 
			src="https://www.facebook.com/tr?id=1231906870601181&ev=PageView
			&noscript=1"/>
			</noscript>
			<!-- End Facebook Pixel Code -->
            <div class="row">
                <div class="col-xs-12">
                    <?php
                        if(isset($_GET['mess'])) {
                            $mess = $_GET['mess'];
                        }else{
                            $mess = 0;
                        }

                        if($mess == 1){
                            echo '<div class="alert material-alert material-alert_danger">Greška: Kandidat sa Vašim korisničkim podacima već postoji u bazi podataka!</div>';
                        }
                    ?>
                </div>
            </div>
			<div class="row">
				<div class="col-xs-12">
					<h1><b><i class="fa fa-user-circle-o idk_color_green" aria-hidden="true"></i> <?php echo $txt_reg_korakjedan_osnovneinfo; ?></b></h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<span style="opacity:0.2;"><?php // echo $_SERVER["SERVER_NAME"]; ?></span>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
				<div class="col-xs-12">
					<form method="POST" action="">
						<ul class="list-inline pull-right" style="width: fit-content; list-style:none;">
							<li><b><?php echo $txt_jezik; ?></b></li>
							<li><b>|</b></li>
							<li class="language_list">
								<button name="lang" type="submit" value="de" style="background: white; border: 0px;" ><img src="<?php getSiteURL(); ?>images/de3d.png" height="24" width="24"></button>
							</li>
							<li><b>|</b></li>
							<li class="language_list">
								<button name="lang" type="submit" value="bs" style="background: white; border: 0px;" >
									<img src="<?php getSiteURL(); ?>images/bs3d.png" height="24" width="24"> 
									<img style="margin-left: 5px; margin-right: 5px;" src="<?php getSiteURL(); ?>images/hr3d.png" height="24" width="24"> 
									<img src="<?php getSiteURL(); ?>images/sr3d.png" height="24" width="24">
								</button>
							</li>
						</ul>
					</form>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<div class="row">
							<div class="col-md-offset-1 col-md-8">
								<form action="<?php getSiteURL(); ?>public_kandidati?page=add_kandidat_new" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<input type="hidden" name="kandidat_visitedurl" value="<?php echo $kandidat_visitedurl; ?>">
									<input type="hidden" name="urlid" value="<?php echo $urlid; ?>">
									<input type="hidden" name="token" value="<?php echo $token; ?>">
									<input type="hidden" name="nalogid" value="<?php echo $lg_nalogid; ?>">
									<input type="hidden" name="lg_language" value="<?php echo $lg_language; ?>">
									<input type="hidden" id="countryCode" name="countryCode" value="<?php echo $countryCode; ?>">
								<?php
								
								foreach($projects as $project_id){
									echo '<input type="hidden" name="lg_project[]" value="'.$project_id.'">';
								}

								?>
								<?php
									if(isset($_GET['urlid'])){
										$urlid = $_GET['urlid'];
								?>
									<div class="form-group">
										<label for="kandidat_prijava_na" class="col-sm-3 control-label"><span class="text-danger">*</span> <?php echo $txt_reg_korakjedan_radno_mjesto ; ?>:</label>
										<div class="col-sm-9">
											<select class="selectpicker" id="kandidat_prijava_na" name="kandidat_prijava_na" >
											<?php
												$query_job_type = $db->prepare("
																SELECT lr_groupid, kg_title, kg_title_de, kg_title_it
																FROM idk_link_generator_rel
																INNER JOIN idk_kandidati_grupe ON idk_link_generator_rel.lr_groupid = idk_kandidati_grupe.kg_id
																WHERE lr_lgid = :lr_lgid
																");

												$query_job_type->execute(array(
													":lr_lgid" => $urlid
												));
												$count = $query_job_type->rowCount();
												while($job_type = $query_job_type->fetch()){

													$lr_groupid = $job_type['lr_groupid'];
													if($lg_language == "bs"){
														$kg_title = $job_type['kg_title'];
													}
													else if($lg_language == "de"){
														$kg_title = $job_type['kg_title_de'];
														if($kg_title == ""){
															$kg_title = $job_type['kg_title'];
														}
													}else{
														$kg_title = $job_type['kg_title_it'];
														if($kg_title == ""){
															$kg_title = $job_type['kg_title'];
														}
													}

											?>
                                                <option value="<?php echo $lr_groupid; ?>"><?php echo $kg_title; ?></option>
											<?php } ?>
    										</select>
										</div>
									</div>
								<?php }else{ ?>
									<div class="form-group">
										<label for="kandidat_prijava_na" class="col-sm-3 control-label"><span class="text-danger">*</span> <?php echo $txt_reg_korakjedan_radno_mjesto ; ?>:</label>
										<div class="col-sm-9">
                                            <select class="selectpicker" id="kandidat_prijava_na" name="kandidat_prijava_na" title="<?php echo $txt_reg_korakjedan_nista_izabrano; ?>" data-live-search="true">
											<?php
												$query_job_type = $db->prepare("
																SELECT kg_id, kg_title
																FROM idk_kandidati_grupe
																");

												$query_job_type->execute();
												$count = $query_job_type->rowCount();
												while($job_type = $query_job_type->fetch()){

													$kg_id = $job_type['kg_id'];
													$kg_title = $job_type['kg_title'];

											?> 
                                                <option value="<?php echo $kg_id; ?>"><?php echo $kg_title; ?></option>
											<?php } ?>
    										</select>
										</div>
									</div>
								<?php } ?>
									<input type="hidden" name="recaptcha_response" id="recaptchaResponse">
									<div class="form-group">
										<label for="kandidat_ime" class="col-sm-3 control-label"><span class="text-danger">*</span> <?php echo $txt_reg_korakjedan_ime ; ?>:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kandidat_ime" id="kandidat_ime" placeholder="<?php echo $txt_reg_korakjedan_ime ; ?>" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="kandidat_prezime" class="col-sm-3 control-label"><span class="text-danger">*</span> <?php echo $txt_reg_korakjedan_prezime ; ?>:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kandidat_prezime" id="kandidat_prezime" placeholder="<?php echo $txt_reg_korakjedan_prezime ; ?>" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<style>
										@media (max-width: 400px) {
											.bootstrap-select.btn-group[class*="col-"] .dropdown-toggle {
												
											}
										}
										.bootstrap-select.btn-group{
											padding: 0px;
										}
									</style>
									<div class="form-group">
										<label for="datum_dan" class="col-sm-3 control-label"><span class="text-danger">*</span> <?php echo $txt_reg_korakjedan_datum_rodjenja ; ?>:</label>
										<div class="col-sm-9">
											<select class="selectpicker col-sm-2 col-xs-3" name="datum_dan" id="datum_dan" required>
													<option value="" selected disabled>DD</option>
													<?php 
													for ($day=1; $day<=31; $day++){ ?>
														<option value="<?php echo $day; ?>"><?php echo $day; ?></option>
													<?php } ?>
											</select>
											/
											<select class="selectpicker col-sm-2 col-xs-3" name="datum_mjesec" id="datum_mjesec" required>
													<option value="" selected disabled>MM</option>
													<?php 
													for ($month=1; $month<=12; $month++){ ?>
														<option value="<?php echo $month; ?>"><?php echo $month; ?></option>
													<?php } ?>
											</select>
											/
											<select class="selectpicker col-sm-3 col-xs-4" name="datum_godina" id="datum_godina" required>
													<option value="" selected disabled>YYYY</option>
													<?php 
													for ($year=2004; $year>1940; $year--){ ?>
														<option value="<?php echo $year; ?>"><?php echo $year; ?></option>
													<?php } ?>
											</select>
											<span class="materail-input-block__line"></span>
										</div>
									</div>
									<div class="form-group">
										<label for="kandidat_mjestorodjenja" class="col-sm-3 control-label"><span class="text-danger">*</span> <?php echo $txt_reg_korakjedan_mjesto_rodjenja ; ?>:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kandidat_mjestorodjenja" id="kandidat_mjestorodjenja" placeholder="<?php echo $txt_reg_korakjedan_mjesto_rodjenja ; ?>" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="kandidat_drzavarodjenja" class="col-sm-3 control-label"><span class="text-danger">*</span> <?php echo $txt_reg_korakjedan_drzava_rodjenja ; ?>:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kandidat_drzavarodjenja" id="kandidat_drzavarodjenja" placeholder="<?php echo $txt_reg_korakjedan_drzava_rodjenja ; ?>" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="kki_phone" class="col-sm-3 control-label"><span class="text-danger">*</span><?php echo $txt_reg_korakjedan_mobilni_tel; ?>:</label>
										<div class="col-sm-9">
											<div class="">
												<input class="form-control materail-input" type="tel" name="kki_phone" id="kki_phone" required>
											</div>
										</div>
									</div>
									<script>
										$( document ).ready(function($) {
											$.each($('input[type=tel]'),function(){
												var telInput = $(this);
												//var countryCode = $("#countryCode").val;
												var countryCode = document.getElementById("countryCode").value 
												console.log(countryCode);
												if ($(this).val().startsWith("+") || $(this).val() == '') {
													$(telInput).intlTelInput({
														utilsScript:'https://intl-tel-input.com/node_modules/intl-tel-input/build/js/utils.js',
														autoPlaceholder: "aggressive",
														initialCountry: ""+countryCode+"",
														formatOnDisplay: true,
														preferredCountries: ["ba","hr","de","it","rs"],
														separateDialCode: true
													});
												}
											});
											//SPAJANJE COUNTRY CODA I TELEFONSKOG BROJA
											$("form").submit(function(event) {
												//event.preventDefault();
												$.each($('input[type=tel]'),function(){
													var telInput = $(this);	
													var telType = telInput.data('type');	
													telInput.val(telInput.intlTelInput("getNumber"));  
												});
											});	
										});
									</script>
									<div class="form-group">
										<label for="kandidat_email" class="col-sm-3 control-label"><span class="text-danger">*</span>Email:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="email" name="kandidat_email" id="kandidat_email" placeholder="E-mail" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="kandidat_drzavljanstvo_vrsta" class="col-sm-3 control-label"><span class="text-danger">*</span> <?php echo $txt_reg_korakjedan_drzavljanstvo ; ?>:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success idk_radio_buttons">
												<label class="main-container__column material-radio-group material-radio-group_success" for="kandidat_drzavljanstvo_vrsta_eu">
													<input type="radio" name="kandidat_drzavljanstvo_vrsta" id="kandidat_drzavljanstvo_vrsta_eu" class="material-radiobox" value="EU državljanin" />
													<span class="material-radio-group__element material-radio-group__check-radio"></span>
													<span class="material-radio-group__element material-radio-group__caption"><?php echo $txt_reg_korakjedan_eu_drzavljanin; ?></span>
												</label>
											</div>
											<div class="materail-input-block materail-input-block_success idk_radio_buttons">
												<label class="main-container__column material-radio-group material-radio-group_success" for="kandidat_drzavljanstvo_vrsta_non">
													<input type="radio" name="kandidat_drzavljanstvo_vrsta" id="kandidat_drzavljanstvo_vrsta_non" class="material-radiobox" value="NON-EU državljanin" checked />
													<span class="material-radio-group__element material-radio-group__check-radio"></span>
													<span class="material-radio-group__element material-radio-group__caption"><?php echo $txt_reg_korakjedan_non_eu_drzavljanin; ?></span>
												</label>
											</div>
										</div>
									</div>
									<div class="form-group" id="boravak_eu">
										<label for="kandidat_boravak_eu" class="col-sm-3 control-label"> <?php echo $txt_reg_korakjedan_boravak_eu ; ?>:</label>
										
										<div class="col-sm-1">
											<label class="main-container__column material-radio-group material-radio-group_success" for="kandidat_boravak_eu_da">
												<input type="radio" name="kandidat_boravak_eu" id="kandidat_boravak_eu_da" class="material-radiobox" value="DA" />
												<span class="material-radio-group__element material-radio-group__check-radio"></span>
												<span class="material-radio-group__element material-radio-group__caption"><?php echo $txt_reg_da; ?></span>
											</label>
										</div>
										<div class="col-sm-1">
											<label class="main-container__column material-radio-group material-radio-group_danger" for="kandidat_boravak_eu_ne">
												<input type="radio" name="kandidat_boravak_eu" id="kandidat_boravak_eu_ne" class="material-radiobox" value="NE" checked />
												<span class="material-radio-group__element material-radio-group__check-radio"></span>
												<span class="material-radio-group__element material-radio-group__caption"><?php echo $txt_reg_ne; ?></span>
											</label>
										</div>
									</div>
									<div class="form-group" id="eu_drzava_step1" style="display:none;" >
										<label for="eu_drzava" class="col-sm-3 control-label"> <?php echo $txt_reg_korakjedan_odaberi_zemlju ; ?>:</label>
										<div class="col-sm-9">
											<select class="selectpicker " name="eu_drzava" id="eu_drzava" >
													<option value="" selected disabled><?php echo $txt_reg_koraktri_odaberi ; ?></option>
													<option value="Austrija"><?php echo $txt_Austrija ; ?></option>
													<option value="Hrvatska"><?php echo $txt_Hrvatska ; ?></option>
													<option value="Njemačka"><?php echo $txt_Njemačka ; ?></option>
													<option value="Italija"><?php echo $txt_Italija ; ?></option>
													<option value="Slovenija"><?php echo $txt_Slovenija ; ?></option>
													<option value="Belgija"><?php echo $txt_Belgija ; ?></option>
													<option value="Bugarska"><?php echo $txt_Bugarska ; ?></option>
													<option value="Kipar"><?php echo $txt_Kipar ; ?></option>
													<option value="Češka"><?php echo $txt_Ceska ; ?></option>
													<option value="Danska"><?php echo $txt_Danska ; ?></option>
													<option value="Estonija"><?php echo $txt_Estonija ; ?></option>
													<option value="Finska"><?php echo $txt_Finska ; ?></option>
													<option value="Francuska"><?php echo $txt_Francuska ; ?></option>
													<option value="Grčka"><?php echo $txt_Grcka ; ?></option>
													<option value="Mađarska"><?php echo $txt_Madarska ; ?></option>
													<option value="Irska"><?php echo $txt_Irska ; ?></option>
													<option value="Latvija"><?php echo $txt_Latvija ; ?></option>
													<option value="Litvanija"><?php echo $txt_Litvanija ; ?></option>
													<option value="Luksemburg"><?php echo $txt_Luksemburg ; ?></option>
													<option value="Malta"><?php echo $txt_Malta ; ?></option>
													<option value="Holandija"><?php echo $txt_Holandija ; ?></option>
													<option value="Poljska"><?php echo $txt_Poljska ; ?></option>
													<option value="Portugal"><?php echo $txt_Portugal ; ?></option>
													<option value="Rumunija"><?php echo $txt_Rumunija ; ?></option>
													<option value="Slovačka"><?php echo $txt_Slovacka ; ?></option>
													<option value="Španija"><?php echo $txt_Spanija ; ?></option>
													<option value="Švedska"><?php echo $txt_Svedska ; ?></option>
													
											</select>
											<span class="materail-input-block__line"></span>
										</div>
									</div>
									<div class="form-group" id="skole_step1" style="">
										<label for="skola_naziv" class="col-sm-3 control-label"><span class="text-danger">*</span><?php echo $txt_reg_koraktri_zavrseno_obr; ?>:</label>
										<div class="col-sm-8">
											<select class="selectpicker" name="skola_naziv" id="skola_naziv" data-live-search="true" required>
												<option value="" selected disabled><?php echo $txt_reg_koraktri_odaberi; ?></option>
												<?php 
													$query_skole = $db->prepare("
															SELECT * FROM idk_skole WHERE skola_tip_obrazovanja = 'srednje' ORDER BY skola_naziv
													");
													$query_skole->execute();
													
													while($row_skola = $query_skole->fetch()){
														
														$skola_tip = $row_skola['skola_tip_obrazovanja'];
														$skola_id = $row_skola['skola_id'];
														if($lg_language == "bs"){
															$skola_naziv = $row_skola['skola_naziv'];
														}
														else if($lg_language == "de"){
															$skola_naziv = $row_skola['skola_naziv_de'];
															if($skola_naziv == ""){
																$skola_naziv = $row_skola['skola_naziv'];
															}
														}else{
															$skola_naziv = $row_skola['skola_naziv'];
														}
														?>
														<option value="<?php echo $skola_naziv; ?>" data-skola_id="<?php echo $skola_id; ?>"><?php echo $skola_naziv; ?></option>
														<?php
													}
												?>
												<option value="ostalo"><?php echo $txt_reg_koraktri_ostalo; ?></option>
												<span class="materail-input-block__line"></span>
											</select>
										</div>
									</div>
									<div class="form-group" id="smjer_step1" style="">
										<label for="smjer_naziv" class="col-sm-3 control-label"><span class="text-danger">*</span><?php echo $txt_reg_koraktri_zvanje_smjer; ?>:</label>
										<div class="col-sm-8">
											<select class="selectpicker" name="smjer_naziv" id="smjer_naziv" data-live-search="true" required >
												<option value="" selected disabled><?php echo $txt_reg_koraktri_odaberi; ?></option>
												<?php 
													$query_skole_smjer = $db->prepare("
															SELECT * FROM idk_skole_smjerovi ORDER BY ss_naziv
													");
													$query_skole_smjer->execute();
													
													while($row_skola_smjer = $query_skole_smjer->fetch()){
														$smjer_skola_id = $row_skola_smjer['ss_skola_id'];
														if($lg_language == "bs"){
															$smjer_naziv = $row_skola_smjer['ss_naziv'];
														}
														else if($lg_language == "de"){
															$smjer_naziv = $row_skola_smjer['ss_naziv_de'];
															if($smjer_naziv == ""){
																$smjer_naziv = $row_skola_smjer['ss_naziv'];
															}
														}else{
															$smjer_naziv = $row_skola_smjer['ss_naziv'];
														}
														?>
														<option value="<?php echo $smjer_naziv; ?>" class="hidden smjerovi_all opt_<?php echo $smjer_skola_id; ?>"><?php echo $smjer_naziv; ?></option>
														<?php
													}
												?>
											</select>
										</div>
									</div>
									<div class="form-group" id="unos_skole" style="display: none;">
										<label for="skola_naziv_ru" class="col-sm-3 control-label"> <?php echo $txt_naziv_skole ; ?>:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="skola_naziv_ru" id="skola_naziv_ru" placeholder="<?php echo $txt_naziv_skole ; ?>" >
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group" id="unos_smjera" style="display: none;">
										<label for="smjer_naziv_ru" class="col-sm-3 control-label"> <?php echo $txt_naziv_smjera ; ?>:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="smjer_naziv_ru" id="smjer_naziv_ru" placeholder="<?php echo $txt_naziv_smjera ; ?>" >
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									
									<!-- PITANJA ZA VIZU -->
									<!-- VIZA -->
									<div class="form-group" id="viza_viza">
										<label for="kandidat_viza" class="col-sm-3 control-label"><?php echo $txt_reg_korakjedan_viza ; ?>?</label>
										<div class="col-sm-1">
											<label class="main-container__column material-radio-group material-radio-group_success" for="viza_da">
												<input type="radio" name="kandidat_viza" id="viza_da" class="material-radiobox" value="1"/>
												<span class="material-radio-group__element material-radio-group__check-radio"></span>
												<span class="material-radio-group__element material-radio-group__caption"><?php echo $txt_reg_da ; ?></span>
											</label>
										</div>
										<div class="col-sm-1">
											<label class="main-container__column material-radio-group material-radio-group_danger" for="viza_ne">
												<input type="radio" name="kandidat_viza" id="viza_ne" class="material-radiobox" value="0" />
												<span class="material-radio-group__element material-radio-group__check-radio"></span>
												<span class="material-radio-group__element material-radio-group__caption"><?php echo $txt_reg_ne ; ?></span>
											</label>
										</div>
									</div>
									<div class="form-group" id="viza_datum_vrijedi_do">
										<label for="kandidat_viza_vrijedi_do" class="col-sm-3 control-label"><?php echo $txt_reg_korakjedan_viza_vrijedi_do ; ?>?</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kandidat_viza_vrijedi_do" id="kandidat_viza_vrijedi_do" placeholder="<?php echo $txt_reg_korakjedan_viza_datum_isteka; ?>">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
										<script>
										<?php $year_sixteen = date('Y') - 16; ?>
										$( function() {
											$( "#kandidat_viza_vrijedi_do" ).datepicker({
											changeMonth: true,
											changeYear: true,
												dateFormat: 'dd.mm.yy' ,
												yearRange: '2020:2050'
											});
										} );
										</script>
									</div>
									<!-- TERMIN -->
									<div class="form-group" id="viza_termin">
										<label for="kandidat_email" class="col-sm-3 control-label"><?php echo $txt_reg_korakjedan_termin_pitanje ; ?></label>
										<div class="col-sm-1">
											<label class="main-container__column material-radio-group material-radio-group_success" for="termin_daa">
												<input type="radio" name="kandidat_termin" id="termin_daa" class="material-radiobox" value="1"/>
												<span class="material-radio-group__element material-radio-group__check-radio"></span>
												<span class="material-radio-group__element material-radio-group__caption"><?php echo $txt_reg_da ; ?></span>
											</label>
										</div>
										<div class="col-sm-1">
											<label class="main-container__column material-radio-group material-radio-group_danger" for="termin_nee">
												<input type="radio" name="kandidat_termin" id="termin_nee" class="material-radiobox" value="0"/>
												<span class="material-radio-group__element material-radio-group__check-radio"></span>
												<span class="material-radio-group__element material-radio-group__caption"><?php echo $txt_reg_ne ; ?></span>
											</label>
										</div>
									</div>
									
									<div class="form-group" id="viza_datum_termina">
										<label for="kandidat_termin_date" class="col-sm-3 control-label"><?php echo $txt_reg_korakjedan_datum_termina; ?></label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kandidat_termin_date" id="kandidat_termin_date" placeholder="<?php echo $txt_reg_korakjedan_datum_termina; ?>">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
										<script>
										<?php $year_sixteen = date('Y') - 16; ?>
										$( function() {
											$( "#kandidat_termin_date" ).datepicker({
											changeMonth: true,
											changeYear: true,
												dateFormat: 'dd.mm.yy' ,
												yearRange: '2018:2025'
											});
										} );
										</script>
									</div>
									
									<!-- APLICIRANJE -->
									<div class="form-group" id="viza_apliciranje">
										<label for="kandidat_apliciranje" class="col-sm-3 control-label"><?php echo $txt_reg_korakjedan_aplikacija_za_vizu ; ?>?</label>
										<div class="col-sm-1">
											<label class="main-container__column material-radio-group material-radio-group_success" for="apliciranje_da">
												<input type="radio" name="kandidat_apliciranje" id="apliciranje_da" class="material-radiobox" value="1"/>
												<span class="material-radio-group__element material-radio-group__check-radio"></span>
												<span class="material-radio-group__element material-radio-group__caption"><?php echo $txt_reg_da ; ?></span>
											</label>
										</div>
										<div class="col-sm-1">
											<label class="main-container__column material-radio-group material-radio-group_danger" for="apliciranje_ne">
												<input type="radio" name="kandidat_apliciranje" id="apliciranje_ne" class="material-radiobox" value="0"/>
												<span class="material-radio-group__element material-radio-group__check-radio"></span>
												<span class="material-radio-group__element material-radio-group__caption"><?php echo $txt_reg_ne ; ?></span>
											</label>
										</div>
									</div>
									
									<div class="form-group" id="viza_datum_apliciranja">
										<label for="kandidat_termin_date_app" class="col-sm-3 control-label"><?php echo $txt_reg_korakjedan_kad_aplikacija ; ?></label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kandidat_termin_date_app" id="kandidat_termin_date_app" placeholder="<?php echo $txt_reg_korakjedan_datum_aplikacije; ?>">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
										<script>
										<?php $year_sixteen = date('Y') - 16; ?>
										$( function() {
											$( "#kandidat_termin_date_app" ).datepicker({
											changeMonth: true,
											changeYear: true,
												dateFormat: 'dd.mm.yy' ,
												yearRange: '2017:2019'
											});
										} );
										</script>
									</div>
									<script>
										
										$(document).ready(function() {
											jezik = $(document).find('input[name="lg_language"]').val();
											console.log(jezik)
											if(jezik == 'de' || jezik == 'it')
												$('#viza_viza').hide();
											
											$('#viza_datum_vrijedi_do').hide();
											$('#viza_termin').hide();
											$('#viza_datum_termina').hide();
											$('#viza_apliciranje').hide();
											$('#viza_datum_apliciranja').hide();
											
										});
										
										$('#kandidat_drzavljanstvo_vrsta_eu').click(function() {
											if($('#kandidat_drzavljanstvo_vrsta_eu').is(':checked')) { 
												$('#viza_viza').hide();
												$('#viza_datum_vrijedi_do').hide();
												$('#viza_termin').hide();
												$('#viza_datum_termina').hide();
												$('#viza_apliciranje').hide();
												$('#viza_datum_apliciranja').hide();
												$('#boravak_eu').hide();
												$('#eu_drzava_step1').hide();
												$('#skole_step1').hide();
												$('#smjer_step1').hide();
												$("#viza_da").prop("checked", false);
												$("#viza_ne").prop("checked", false);
												$("#termin_daa").prop("checked", false);
												$("#termin_nee").prop("checked", false);
												$("#apliciranje_da").prop("checked", false);
												$("#apliciranje_ne").prop("checked", false);
												$("#kandidat_boravak_eu_da").prop("checked", false);
												$("#kandidat_boravak_eu_ne").prop("checked", false);
												$('#skola_naziv').selectpicker('val', '');
												$('#smjer_naziv').selectpicker('val', '');
												$('#unos_skole').hide();
												$('#unos_smjera').hide();
												$("#skola_naziv").prop('required',false);
												$("#smjer_naziv").prop('required',false);
											}
										});
										
										$('#kandidat_drzavljanstvo_vrsta_non').click(function() {
											if($('#kandidat_drzavljanstvo_vrsta_non').is(':checked')) { 
												$('#viza_viza').show();
												$('#boravak_eu').show();
												$('#eu_drzava').selectpicker('val', '');
												$('#skole_step1').show();
												$('#smjer_step1').show();
												$("#skola_naziv").prop('required',true);
											}
										});
										
										$('#kandidat_boravak_eu_da').click(function() {
											if($('#kandidat_boravak_eu_da').is(':checked')) { 
												$('#eu_drzava_step1').show();
												$('#skole_step1').hide();
												$('#smjer_step1').hide();
												$('#skola_naziv').selectpicker('val', '');
												$('#smjer_naziv').selectpicker('val', '');
												$('#unos_skole').hide();
												$('#unos_smjera').hide();
												$("#skola_naziv").prop('required',false);
												$("#smjer_naziv").prop('required',false);
											}
										});
										
										$('#kandidat_boravak_eu_ne').click(function() {
											if($('#kandidat_boravak_eu_ne').is(':checked')) { 
												$('#eu_drzava_step1').hide();
												$('#skole_step1').show();
												$('#smjer_step1').show();
												$('#eu_drzava').selectpicker('val', '');
												$("#skola_naziv").prop('required',true);
											}
										});
										
										$('#skola_naziv').on('change', function() {
											$('.smjerovi_all').addClass('hidden');
											var skola_id = $(this).find(':selected').data('skola_id');
											$('.opt_'+skola_id+'').removeClass('hidden');
											$('#smjer_naziv').selectpicker('val', '');
											optionSrednja =  $(this).val();
											if(optionSrednja == "ostalo"){
												$('#smjer_step1').hide();
												$('#unos_skole').show();
												$('#unos_smjera').show();
												$("#smjer_naziv").prop('required',false);
											}else{
												$('#smjer_step1').show();
												$('#unos_skole').hide();
												$('#unos_smjera').hide();
												$("#smjer_naziv").prop('required',true);
											}
											
										});

										$('#viza_da').click(function() {
											if($('#viza_da').is(':checked')) { 
												$('#viza_datum_vrijedi_do').show();
												$('#viza_termin').hide();
												$('#viza_datum_termina').hide();
												$('#viza_apliciranje').hide();
												$('#viza_datum_apliciranja').hide();
												$("#termin_daa").prop("checked", false);
												$("#termin_nee").prop("checked", false);
												$("#apliciranje_da").prop("checked", false);
												$("#apliciranje_ne").prop("checked", false);
											}
										});
										$('#viza_ne').click(function() {
											if($('#viza_ne').is(':checked')) { 
												$('#viza_datum_vrijedi_do').hide();
												$('#viza_termin').show();
											}
										});
										$('#termin_daa').click(function() {
											if($('#termin_daa').is(':checked')) { 
												$('#viza_datum_termina').show();
												$('#viza_apliciranje').hide();
												$('#viza_datum_apliciranja').hide();
												$("#apliciranje_da").prop("checked", false);
												$("#apliciranje_ne").prop("checked", false);
											}
										});
										$('#termin_nee').click(function() {
											if($('#termin_nee').is(':checked')) { 
												$('#viza_datum_termina').hide();
												$('#viza_apliciranje').show();
											}
										});
										
										$('#apliciranje_da').click(function() {
											if($('#apliciranje_da').is(':checked')) { 
												$('#viza_datum_apliciranja').show();
											}
										});
										$('#apliciranje_ne').click(function() {
											if($('#apliciranje_ne').is(':checked')) { 
												$('#viza_datum_apliciranja').hide();
											}
										});
									</script>
									
									<br />
									<div class="form-group">
										<div class="col-sm-offset-2 col-sm-10 text-right">
											<button id="button_nastavi" type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-arrow-right" aria-hidden="true"></i> <span><?php echo $txt_reg_nastavi ; ?></span></button>
											<br /><small><?php echo $txt_reg_korakjedan_obavezna_polja1 ; ?><span class="text-danger">*</span><?php echo $txt_reg_korakjedan_obavezna_polja2 ; ?></small>
										</div>
									</div>
									
									<div class="form-group">
										<label for="asd" class="col-sm-3 control-label"><span class="text-danger"></span></label>
										
										<div class="col-sm-6" style="padding-top: 10px;">
											
											<p class = "text-center"> <?php echo $txt_reg_korakjedan_prihvatanje_privatnosti ; ?>
											<br><a href="" type="button" data-toggle="modal" data-target="#privacyModal"><?php echo $txt_reg_korakjedan_pogledaj_izjavu ; ?></a>
											</p>
										</div>
									</div>
									<!-- Modal privacy -->
									<div class="modal fade modal-fullscreen" id="privacyModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
									  <div class="modal-dialog" role="document">
										<div class="modal-content">
										  <div class="modal-header">
											<h5 class="modal-title" id="exampleModalLabel"><?php echo $txt_reg_korakjedan_izjava_h5 ; ?></h5>
										  </div>
										  <div class="modal-body">
											<p><?php echo $txt_reg_korakjedan_izjava_tekst ; ?></p>
										  </div>
										  <div class="modal-footer">
											<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $txt_reg_zatvori; ?></button>
										  </div>
										</div>
									  </div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php
				break;

				case "step2":

				$kandidat_id = $_REQUEST['id'];
				$kandidat_check = $_REQUEST['check'];

				// CHECK IF USER EXISTS IN DATABASE
				if(checkUserR($kandidat_id, $kandidat_check) == 1){
				?>
					<div class="row">
						<div class="col-xs-12">
							<?php
								if(isset($_GET['mess'])) {
									$mess = $_GET['mess'];
								}else{
									$mess = 0;
								}

								if($mess == 1){
									echo '<div class="alert material-alert material-alert_danger">Greška: Kandidat sa ovom E-mail adresom već postoji u bazi podataka!</div>';
								}
							?>
						</div>
					</div>
					
					<div class="row">
						<div class="col-xs-12">
							<h1><b><i class="fa fa-user-circle-o idk_color_green" aria-hidden="true"></i> <?php echo $txt_reg_korakdva_kontakt_informacije; ?></b></h1>
						</div>
						<div class="col-xs-12 text-right idk_margin_top10">
							<a href="<?php getSiteURL(); ?>registracija/korak3/<?php echo $kandidat_id; ?>/<?php echo $kandidat_check; ?>"><button class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column"><i class="fa fa-arrow-right" aria-hidden="true"></i> <span><?php echo $txt_reg_preskoci ; ?></span></button></a>
						</div>
						<div class="col-xs-12">
							<hr />
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="content_box">
								<div class="row">
									<div class="col-md-offset-1 col-md-8">
										<form action="<?php getSiteURL(); ?>public_kandidati?page=add_kandidat_ostalo" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
											<input type="hidden" name="kandidat_id" value="<?php echo $kandidat_id; ?>">
											<input type="hidden" name="kandidat_check" value="<?php echo $kandidat_check; ?>">
											
											<div class="form-group">
												<label for="kandidat_spol" class="col-sm-3 control-label"><span class="text-danger">*</span> <?php echo $txt_reg_korakdva_spol ; ?>:</label>
												<div class="col-sm-9">
													<select class="selectpicker" id="kandidat_spol" title="<?php echo $txt_reg_korakjedan_nista_izabrano; ?>" name="kandidat_spol">
														<option value=""></option>
														<option value="Muško"><?php echo $txt_reg_korakdva_musko; ?></option>
														<option value="Žensko"><?php echo $txt_reg_korakdva_zensko; ?></option>
													</select>
												</div>
											</div>
											
											<div id="kandidat_djevojackoprezime" class="form-group hidden">
												<label for="kandidat_djevojackoprezime" class="col-sm-3 control-label"><span class="text-danger">*</span> <?php echo $txt_reg_korakdva_djevojacko_prezime ; ?>:</label>
												<div class="col-sm-9">
													<div class="materail-input-block materail-input-block_success">
														<input class="form-control materail-input" type="text" name="kandidat_djevojackoprezime" id="kandidat_djevojackoprezime" placeholder="<?php echo $txt_reg_korakdva_djevojacko_prezime ; ?>" />
														<span class="materail-input-block__line"></span>
													</div>
												</div>
											</div>
											<script>
												$('#kandidat_spol').on('change', function() {
													if (this.value == 'Žensko') $('#kandidat_djevojackoprezime').removeClass('hidden');
													if (this.value == 'Muško') $('#kandidat_djevojackoprezime').addClass('hidden');
													if (this.value == '') $('#kandidat_djevojackoprezime').addClass('hidden');
												});
											</script>
											
											<div class="form-group">
												<label for="kandidat_drzavljanstvo" class="col-sm-3 control-label"><span class="text-danger">*</span> <?php echo $txt_reg_korakdva_drzavljanstvo_naziv ; ?>:</label>
												<div class="col-sm-9">
													<div class="materail-input-block materail-input-block_success">
														<input class="form-control materail-input" type="text" name="kandidat_drzavljanstvo" id="kandidat_drzavljanstvo" placeholder="<?php echo $txt_reg_korakdva_drzavljanstvo_naziv ; ?>" required>
														<span class="materail-input-block__line"></span>
													</div>
												</div>
											</div>
											 <input type="hidden" name="recaptcha_response" id="recaptchaResponse">
											<div class="form-group">
												<label for="kandidat_adresa" class="col-sm-3 control-label"><span class="text-danger">*</span> <?php echo $txt_reg_korakdva_adresa ; ?>:</label>
												<div class="col-sm-9">
													<div class="materail-input-block materail-input-block_success">
														<input class="form-control materail-input" type="text" name="kandidat_adresa" id="kandidat_adresa" placeholder="<?php echo $txt_reg_korakdva_adresa ; ?>" required>
														<span class="materail-input-block__line"></span>
													</div>
												</div>
											</div>
											<div class="form-group">
												<label for="kandidat_grad" class="col-sm-3 control-label"><span class="text-danger">*</span> <?php echo $txt_reg_grad ; ?>:</label>
												<div class="col-sm-9">
													<div class="materail-input-block materail-input-block_success">
														<input class="form-control materail-input" type="text" name="kandidat_grad" id="kandidat_grad" placeholder="<?php echo $txt_reg_grad ; ?>" required>
														<span class="materail-input-block__line"></span>
													</div>
												</div>
											</div>
											<div class="form-group">
												<label for="kandidat_pbroj" class="col-sm-3 control-label"><span class="text-danger">*</span> <?php echo $txt_reg_korakdva_postanski_broj ; ?>:</label>
												<div class="col-sm-9">
													<div class="materail-input-block materail-input-block_success">
														<input class="form-control materail-input" type="number" name="kandidat_pbroj" id="kandidat_pbroj" placeholder="<?php echo $txt_reg_korakdva_postanski_broj ; ?>" required>
														<span class="materail-input-block__line"></span>
													</div>
												</div>
											</div>
											<div class="form-group">
												<label for="kandidat_drzava" class="col-sm-3 control-label"><span class="text-danger">*</span> <?php echo $txt_reg_drzava ; ?>:</label>
												<div class="col-sm-9">
													<div class="materail-input-block materail-input-block_success">
														<input class="form-control materail-input" type="text" name="kandidat_drzava" id="kandidat_drzava" placeholder="<?php echo $txt_reg_drzava ; ?>" required>
														<span class="materail-input-block__line"></span>
													</div>
												</div>
											</div>
											
											<div style="display: <?php echo $nbp_vozacka;?>">
												<div class="form-group">
													<label for="kandidat_vozacka_dozvola" class="col-sm-3 control-label"><?php echo $txt_reg_korakdva_vozacka ; ?>:</label>
													<div class="col-sm-1">
														<label class="main-container__column material-radio-group material-radio-group_success" for="radio3">
															<input type="radio" name="kandidat_vozacka_dozvola" id="radio3" class="material-radiobox" value="Da"/>
															<span class="material-radio-group__element material-radio-group__check-radio"></span>
															<span class="material-radio-group__element material-radio-group__caption"><?php echo $txt_reg_da ; ?></span>
														</label>
													</div>
													<div class="col-sm-1">
														<label class="main-container__column material-radio-group material-radio-group_danger" for="radio5">
															<input type="radio" name="kandidat_vozacka_dozvola" id="radio5" class="material-radiobox" value="Ne"/>
															<span class="material-radio-group__element material-radio-group__check-radio"></span>
															<span class="material-radio-group__element material-radio-group__caption"><?php echo $txt_reg_ne ; ?></span>
														</label>
													</div>
												</div>
											</div>
											
											<div class="form-group">
												<div class="col-sm-offset-2 col-sm-10 text-right">
													<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-plus" aria-hidden="true"></i> <span><?php echo $txt_reg_spremi; ?></span></button>
												</div>
											</div>
										</form>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php
				}else{}
				

				break;
				case "step3":

				$kandidat_id = $_REQUEST['id'];
				$kandidat_check = $_REQUEST['check'];
				
				// CHECK IF USER EXISTS IN DATABASE
				if(checkUserR($kandidat_id, $kandidat_check) == 1){


				// GET URL ID FROM WHERE USER SIGN UP
				$query = $db->prepare("
								SELECT kandidat_visitedurl
								FROM idk_kandidati
								WHERE kandidat_id = :kandidat_id
								");

				$query->execute(array(
					":kandidat_id" => $kandidat_id
				));

				$row = $query->fetch();
					$kandidat_visitedurl = $row['kandidat_visitedurl'];
					
				// SETOVANJE PRAZNOG ARRAYA
				$vrste_edukacija = array();
				$vrste_edukacija_de = array();
				$vrste_edukacija_it = array();
				
				// DA LI JE VEĆ UNEŠENO SREDNJE OBRAZOVANJE
				
				$srednje_obr = false;
				$query_obr1 = $db->prepare("
								SELECT ke_vrsta_obrazovanja
								FROM idk_kandidat_edukacija
								WHERE ke_kandidat_id = :ke_kandidat_id
								");

				$query_obr1->execute(array(
					":ke_kandidat_id" => $kandidat_id
				));
				while($row_obr1 = $query_obr1->fetch()){
					$vrsta_obrazovanja = $row_obr1['ke_vrsta_obrazovanja'];
					
					if($vrsta_obrazovanja == "srednje")
						$srednje_obr = true;
					
				}
				
				$vrste_edukacija += ["srednje" => "Srednjoškolsko obrazovanje"];
				$vrste_edukacija_de += ["srednje" => "Berufsabschluss"];
				$vrste_edukacija_it += ["srednje" => "Educazione Scuola Superiore"];
				
				// DA LI JE TRAŽENO VISOKO I DODATNO OBRAZOVANJE AKO JE PRIJAVA VEZANA ZA NALOG
				if($lg_nalogid !== null){
					$query_obr = $db->prepare("
									SELECT nbp_visoko_obr, nbp_dodatno_obr
									FROM idk_nalozi_blokovi_prijave
									WHERE nbp_nalogid = :nbp_nalogid
									");

					$query_obr->execute(array(
						":nbp_nalogid" => $lg_nalogid
					));
					$row_obr = $query_obr->fetch();
					$nbp_visoko_obr = $row_obr['nbp_visoko_obr'];
					$nbp_dodatno_obr = $row_obr['nbp_dodatno_obr'];
					
					if($nbp_visoko_obr == 1){
						$vrste_edukacija += ["visoko" => "Visoko obrazovanje"];
						$vrste_edukacija_de += ["visoko" => "Hochschulabschluss"];
						$vrste_edukacija_it += ["visoko" => "Educazione Universitaria"];
					}
					if($nbp_dodatno_obr == 1){
						$vrste_edukacija += ["ostalo" => "Dodatna edukacija"];
						$vrste_edukacija_de += ["ostalo" => "Fortbildung"];
						$vrste_edukacija_it += ["ostalo" => "Altra istruzione"];
					}
				}else{
					$vrste_edukacija += ["visoko" => "Visoko obrazovanje"];
					$vrste_edukacija_de += ["visoko" => "Hochschulabschluss"];
					$vrste_edukacija_it += ["visoko" => "Educazione Universitaria"];
					$vrste_edukacija += ["ostalo" => "Dodatna edukacija"];
					$vrste_edukacija_de += ["ostalo" => "Fortbildung"];
					$vrste_edukacija_it += ["ostalo" => "Altra istruzione"];
				}
				
				// $vrste_edukacija = array(
						// "osnovno" => "Osnovno obrazovanje", 
						// "srednje" => "Srednjoškolsko obrazovanje", 
						// "visoko" => "Visoko obrazovanje",
						// "ostalo" => "Dodatna edukacija"
				// );
				//var_dump($vrste_edukacija["osnovno"]);
				// var_dump($nbp_dodatno_obr);
				// exit();

		?>
			
            <div class="row">
                <div class="col-xs-12">
                </div>
            </div>
			<div class="row">
				<div class="col-xs-12">
					<h1><b><i class="fa fa-user-circle-o idk_color_green" aria-hidden="true"></i> <?php echo $txt_reg_koraktri_koraktri; ?></b></h1>
				</div>
				<div class="col-xs-12 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>registracija/korak2/<?php echo $kandidat_id; ?>/<?php echo $kandidat_check; ?>"><button class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column"><i class="fa fa-arrow-left" aria-hidden="true"></i> <span><?php echo $txt_reg_nazad; ?></span></button></a>

					<a href="<?php getSiteURL(); ?>registracija/korak4/<?php echo $kandidat_id; ?>/<?php echo $kandidat_check; ?>"><button class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column"><i class="fa fa-arrow-right" aria-hidden="true"></i> <span><?php echo $txt_reg_preskoci; ?></span></button></a>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<div class="row">
							<div class="col-md-offset-1 col-md-8">
								<form action="<?php getSiteURL(); ?>public_kandidati?page=add_kandidat_edukacija" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<input type="hidden" name="ke_kandidat_id" value="<?php echo $kandidat_id; ?>">
									<input type="hidden" name="ke_kandidat_check" value="<?php echo $kandidat_check; ?>">
									<input type="hidden" name="lg_language" value="<?php echo $lg_language; ?>">
									
									<div class="form-group">
										<label for="vrsta_obrazovanja" class="col-sm-4 control-label"><span class="text-danger">*</span><?php echo $txt_reg_koraktri_nivo; ?>: </label>
										<div class="col-sm-8">
											<select class="selectpicker" name="vrsta_obrazovanja" id="vrsta_obrazovanja" required>
												<option value="" selected disabled><?php echo $txt_reg_koraktri_odaberi; ?></option>
												<?php 
												if($lg_language == "de")
													$vrste_edukacija = $vrste_edukacija_de;
												elseif($lg_language == "it")
													$vrste_edukacija = $vrste_edukacija_it;
												
												foreach($vrste_edukacija as $vrsta_value => $vrsta) {?>
												<option value="<?php echo $vrsta_value;?>"><?php echo $vrsta; ?></option>
												<?php } ?>
											</select>
										</div>
									</div>
									<script>
										$( document ).ready(function($) {
											$('.always_shown').addClass('hidden');
											$('.zvanje').addClass('hidden');
											$('.opis_obr').addClass('hidden');
											$('.zvanje_select').addClass('hidden');
											$('#vrsta_obrazovanja').on('change', function() {
												if (this.value == 'srednje'){
													$('.always_shown').removeClass('hidden');
													$('.zvanje').addClass('hidden');
													$('.opt_srednje').removeClass('hidden');
													$('.opt_visoko').addClass('hidden');
													$('.opt_dodatna').addClass('hidden');
													$('.opis_obr').addClass('hidden');
													$('.zvanje_select').removeClass('hidden');
												}else if (this.value == 'visoko'){
													$('.always_shown').removeClass('hidden');
													$('.zvanje').addClass('hidden');
													$('.opt_visoko').removeClass('hidden');
													$('.opt_srednje').addClass('hidden');
													$('.opt_dodatna').addClass('hidden');
													$('.opis_obr').removeClass('hidden');
													$('.zvanje_select').removeClass('hidden');
												}else if (this.value == 'ostalo'){
													$('.always_shown').removeClass('hidden');
													$('.zvanje').removeClass('hidden');
													$('.opt_dodatna').removeClass('hidden');
													$('.opt_srednje').addClass('hidden');
													$('.opt_visoko').addClass('hidden');
													$('.opis_obr').removeClass('hidden');
													$('.zvanje_select').addClass('hidden');
												}
											   
											});
										});
									</script>
									<div class="form-group">
										<label for="ke_naziv" class="col-sm-4 control-label"><span class="text-danger">*</span> <?php echo $txt_reg_koraktri_zavrseno_obr; ?>:</label>
										<div class="col-sm-8">
											<select class="selectpicker" name="ke_naziv" id="ke_naziv" data-live-search="true" required>
												<option value="" selected disabled><?php echo $txt_reg_koraktri_odaberi; ?></option>
												<?php 
													$query_skole = $db->prepare("
															SELECT * FROM idk_skole ORDER BY skola_naziv
													");
													$query_skole->execute();
													
													while($row_skola = $query_skole->fetch()){
														$skola_naziv = $row_skola['skola_naziv'];
														$skola_tip = $row_skola['skola_tip_obrazovanja'];
														$skola_id = $row_skola['skola_id'];
														?>
														<option value="<?php echo $skola_id; ?>" class="hidden opt_<?php echo $skola_tip; ?>" data-skola_id="<?php echo $skola_id; ?>"><?php echo $skola_naziv; ?></option>
														<?php
													}
												?>
												
												<option value="certifikati" class="opt_dodatna hidden"><?php echo $txt_reg_koraktri_certifikat; ?></option>
												<option value="seminari" class="opt_dodatna hidden"><?php echo $txt_reg_koraktri_seminar; ?></option>
												<option value="kursevi" class="opt_dodatna hidden"><?php echo $txt_reg_koraktri_kurs; ?></option>
												<option value="ostalo"><?php echo $txt_reg_koraktri_ostalo; ?></option>
												
												<span class="materail-input-block__line"></span>
											</select>
										</div>
									</div>
									<script>
									
										$('#ke_naziv').on('change', function() {
											$('.smjerovi_all').addClass('hidden');
											var skola_id = $(this).find(':selected').data('skola_id');
											$('.opt_'+skola_id+'').removeClass('hidden');
										});
									</script>
									<div class="form-group zvanje_select">
										<label for="smjerovi" class="col-sm-4 control-label"><span class="text-danger">*</span> <?php echo $txt_reg_koraktri_zvanje_smjer; ?>:</label>
										<div class="col-sm-8">
											<select class="selectpicker" name="smjerovi" id="smjerovi" data-live-search="true" required>
												<option value="" selected disabled><?php echo $txt_reg_koraktri_odaberi; ?></option>
												<?php 
													$query_skole_smjer = $db->prepare("
															SELECT * FROM idk_skole_smjerovi ORDER BY ss_naziv
													");
													$query_skole_smjer->execute();
													
													while($row_skola_smjer = $query_skole_smjer->fetch()){
														$smjer_naziv = $row_skola_smjer['ss_naziv'];
														$smjer_skola_id = $row_skola_smjer['ss_skola_id'];
														$smjer_id = $row_skola_smjer['ss_id'];
														?>
														<option value="<?php echo $smjer_id; ?>" class="hidden smjerovi_all opt_<?php echo $smjer_skola_id; ?>"><?php echo $smjer_naziv; ?></option>
														<?php
													}
												?>
											</select>
										</div>
									</div>
									<div class="form-group zvanje">
										<label for="ke_naziv_kvalifikacije" class="col-sm-4 control-label"><span class="text-danger">*</span> <?php echo $txt_reg_koraktri_naziv_kvalifikacije; ?>:</label>
										<div class="col-sm-8">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="ke_naziv_kvalifikacije" id="ke_naziv_kvalifikacije" placeholder="<?php echo $txt_reg_koraktri_naziv_kvalifikacije;?>" >
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group opis_obr">
										<label for="ke_opis" class="col-sm-4 control-label"><span class="text-danger"></span> <?php echo $txt_reg_opis; ?>:</label>
										<div class="col-sm-8">
											<div class="materail-input-block materail-input-block_success">
												<textarea class="form-control materail-input material-textarea" name="ke_opis" id="ke_opis"></textarea>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group always_shown">
										<label for="ke_datum" class="col-sm-4 control-label"><span class="text-danger">*</span> <?php echo $txt_reg_koraktri_godina; ?>:</label>
										<div class="col-sm-5">
											<select class="selectpicker col-sm-5" name="ke_datumod" id="ke_datumod" data-live-search="true" required>
												<option value="" selected disabled><?php echo $txt_reg_od; ?></option>
												<?php 
												for ($year=2021; $year>1960; $year--){ ?>
													<option value="<?php echo $year; ?>"><?php echo $year; ?></option>
												<?php } ?>
											</select>
											-
											<select class="selectpicker col-sm-5" name="ke_datumdo" id="ke_datumdo" data-live-search="true" required>
												<option value="" selected disabled><?php echo $txt_reg_do; ?></option>
												<?php 
												for ($year=2021; $year>1960; $year--){ ?>
													<option value="<?php echo $year; ?>"><?php echo $year; ?></option>
												<?php } ?>
											</select>
										</div>
										<div class="col-sm-3">
											<div class="main-container__column material-checkbox-group material-checkbox-group_primary idk_margin_top10">
												<input type="checkbox" id="ke_datumdo_aktuelno" name="ke_datumdo_aktuelno" class="material-checkbox">
												<label class="material-checkbox-group__label" for="ke_datumdo_aktuelno"><?php echo $txt_reg_koraktri_aktuelno; ?></label>
											</div>
										</div>
										<script>
										$( document ).ready(function() {
											$('#ke_datumdo_aktuelno').click(function()
											{
												console.log("asdasd");
												//If checkbox is checked then disable or enable input
												if ($(this).is(':checked'))
												{
													//$("#ke_datumdo").removeAttr("disabled");
													$("#ke_datumdo").attr("disabled","disabled");
													$("#ke_datumdo").val('00-00-0000');
												}
												//If checkbox is unchecked then disable or enable input
												else
												{
													$("#ke_datumdo").removeAttr("disabled");
													$("#ke_datumdo").val('');
												}
											});
										});
										</script>
									</div>
									<div class="form-group always_shown">
										<label for="ke_grad" class="col-sm-4 control-label"><span class="text-danger">*</span> <?php echo $txt_reg_grad; ?>:</label>
										<div class="col-sm-8">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="ke_grad" id="ke_grad" placeholder="<?php echo $txt_reg_grad; ?>" >
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group always_shown">
										<label for="ke_drzava" class="col-sm-4 control-label"><span class="text-danger">*</span> <?php echo $txt_reg_drzava; ?>:</label>
										<div class="col-sm-8">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="ke_drzava" id="ke_drzava" placeholder="<?php echo $txt_reg_drzava; ?>" >
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									 <input type="hidden" name="recaptcha_response" id="recaptchaResponse">
									<div class="form-group always_shown">
										<div class="col-sm-offset-2 col-sm-10 text-right">
											<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-plus" aria-hidden="true"></i> <span><?php echo $txt_reg_spremiV; ?></span></button>
										</div>
									</div>
								</form>
							</div>
								<div class="col-md-12">
									<h4><?php echo $txt_reg_koraktri_pregled_edukacije; ?>:</h4>
									<table class="table table-hover">
										<thead>
											<tr>

												<th class="text-center"><?php echo $txt_reg_koraktri_naziv_kvalifikacije; ?></th>
												<th class="text-center"><?php echo $txt_reg_naziv; ?></th>
												<th class="text-center"><?php echo $txt_reg_od; ?></th>
												<th class="text-center"><?php echo $txt_reg_do; ?></th>
												<th class="text-center"><?php echo $txt_reg_grad; ?></th>
												<th class="text-center"><?php echo $txt_reg_opis; ?></th>
												<th class="text-center"><?php echo $txt_reg_izbrisi; ?></th>
											</tr>
										</thead>
										<tbody>
											<?php
												$select_query = $db->prepare("
																	SELECT ke_id, ke_datumod, ke_datumdo, ke_naziv_kvalifikacije, ke_naziv, ke_grad, ke_opis, ke_aktuelno
																	FROM idk_kandidat_edukacija
																	WHERE ke_kandidat_id = :ke_kandidat_id");

												$select_query->execute(array(
																':ke_kandidat_id' => $kandidat_id));

												while($select_row = $select_query->fetch()) {

													$ke_id = $select_row['ke_id'];
													$ke_datumod = $select_row['ke_datumod'];
													$ke_datumod_f = date('Y', strtotime($ke_datumod));
													$ke_datumdo = $select_row['ke_datumdo'];
													$ke_naziv_kvalifikacije = $select_row['ke_naziv_kvalifikacije'];
													$ke_naziv = $select_row['ke_naziv'];
													$ke_grad = $select_row['ke_grad'];
													$ke_opis = $select_row['ke_opis'];
													$ke_aktuelno = $select_row['ke_aktuelno'];

													if($ke_aktuelno != 1){
														$ke_datumdo_f = date('Y', strtotime($ke_datumdo));
													}else{
														$ke_datumdo_f = $txt_reg_koraktri_aktuelno;
														
													}
											?>
											<tr>
												<td class="text-center"><?php echo $ke_naziv_kvalifikacije; ?></td>
												<td class="text-center"><?php echo $ke_naziv; ?></td>
												<td class="text-center"><?php echo $ke_datumod_f; ?></td>
												<td class="text-center"><?php echo $ke_datumdo_f; ?></td>
												<td class="text-center"><?php echo $ke_grad; ?></td>
												<td class="text-center"><?php echo $ke_opis; ?></td>
												<td class="text-center"><a href="<?php getSiteURL(); ?>public_kandidati?page=delete_kandidat_edukacija&id=<?php echo $ke_id; ?>&check=<?php echo $kandidat_check; ?>" class="material-dropdown-menu__link"><i class="fa fa-times" aria-hidden="true"></i></a></td>
											</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>
								<br />
								<div class="form-group">
									<div class="col-sm-12 text-right">
										<a href="<?php getSiteURL(); ?>registracija/korak4/<?php echo $kandidat_id; ?>/<?php echo $kandidat_check; ?>"><button class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-arrow-right" aria-hidden="true"></i> <span><?php echo $txt_reg_nastavi; ?></span></button></a>
										<br /><small><?php echo $txt_reg_korakjedan_obavezna_polja1; ?><span class="text-danger">*</span><?php echo $txt_reg_korakjedan_obavezna_polja2; ?></small>
									</div>
								</div>
						</div>
					</div>
				</div>
			</div>
		<?php
		}else{}
				
				break;
				case "step4":

				$kandidat_id = $_REQUEST['id'];
				$kandidat_check = $_REQUEST['check'];
				
				if($nbp_korak4 == "1"){

				// CHECK IF USER EXISTS IN DATABASE
				if(checkUserR($kandidat_id, $kandidat_check) == 1){

		?>
			
            <div class="row">
                <div class="col-xs-12">
                </div>
            </div>
			<div class="row">
				<div class="col-xs-12">
					<h1><b><i class="fa fa-user-circle-o idk_color_green" aria-hidden="true"></i> <?php echo $txt_reg_korakcetiri_korak_cetiri; ?></b></h1>
				</div>
				<div class="col-xs-12 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>registracija/korak3/<?php echo $kandidat_id; ?>/<?php echo $kandidat_check; ?>"><button class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column"><i class="fa fa-arrow-left" aria-hidden="true"></i> <span><?php echo $txt_reg_nazad; ?></span></button></a>

					<a href="<?php getSiteURL(); ?>registracija/korak6/<?php echo $kandidat_id; ?>/<?php echo $kandidat_check; ?>"><button class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column"><i class="fa fa-arrow-right" aria-hidden="true"></i> <span><?php echo $txt_reg_preskoci; ?></span></button></a>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<div class="row">
							<div class="col-md-offset-1 col-md-8">
								<form action="<?php getSiteURL(); ?>public_kandidati?page=add_kandidat_iskustvo" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<input type="hidden" name="kri_kandidat_id" value="<?php echo $kandidat_id; ?>">
									<input type="hidden" name="kri_kandidat_check" value="<?php echo $kandidat_check; ?>">
									 <input type="hidden" name="recaptcha_response" id="recaptchaResponse">
									<!-- DATUM OD START - pojedinačno mjesec i godina pomocu selecta-->
									<div class = "form-group">
										<label for = "" class="col-sm-3 control-label">
											<span class="text-danger">
												*
											</span> 
											<?php echo $txt_reg_datum_od; ?>:
											
										</label>
										<div class="col-sm-3">
											<select style = "width: 50%;" class="selectpicker" name="kri_datum_od_mjesec" id="kri_datum_od_mjesec" required>
												<option value="" selected disabled><?php echo $txt_reg_korakcetiri_mjesec; ?></option>
												<?php for($mjesec_od = 1; $mjesec_od <= 12; $mjesec_od++) {?>
													<option value="<?php echo $mjesec_od; ?>"><?php echo $mjesec_od; ?></option>
												<?php }?>
											</select>
										</div>
										<div class="col-sm-3">
											<select style = "width: 50%;" class="selectpicker" name="kri_datum_od_godina" id="kri_datum_od_godina" required>
												<option value="" selected disabled><?php echo $txt_reg_koraktri_godina; ?></option>
												<?php for($godina_od = date("Y"); $godina_od >= 1960; $godina_od--) {?>
													<option value="<?php echo $godina_od; ?>"><?php echo $godina_od; ?></option>
												<?php }?>
											</select>
										</div>
									</div>
									<!-- DATUM OD END - pojedinačno mjesec i godina pomocu selecta-->
									
									<!-- DATUM DO START - pojedinačno mjesec i godina pomocu selecta-->
									<div class = "form-group">
										<label for = "kri_datum_do_mjesec" class="col-sm-3 control-label">
											<span class="text-danger">
												*
											</span> 
											<?php echo $txt_reg_datum_do; ?>:
										</label>
										<div class="col-sm-3">
											<select style = "width: 50%;" class="selectpicker" name="kri_datum_do_mjesec" id="kri_datum_do_mjesec" required>
												<option value="" selected disabled><?php echo $txt_reg_korakcetiri_mjesec; ?></option>
												<?php for($mjesec_do = 1; $mjesec_do <= 12; $mjesec_do++) {?>
													<option value="<?php echo $mjesec_do; ?>"><?php echo $mjesec_do; ?></option>
												<?php }?>
											</select>
										</div>
										<div class="col-sm-3">
											<select style = "width: 50%;" class="selectpicker" name="kri_datum_do_godina" id="kri_datum_do_godina" required>
												<option value="" selected disabled><?php echo $txt_reg_koraktri_godina; ?></option>
												<?php for($godina_do = date("Y"); $godina_do >= 1960; $godina_do--) {?>
													<option value="<?php echo $godina_do; ?>"><?php echo $godina_do; ?></option>
												<?php }?>
											</select>
										</div>
										<div class="col-sm-3">
											<div class="main-container__column material-checkbox-group material-checkbox-group_primary idk_margin_top10">
												<input type="checkbox" id="kri_datum_do_aktuelno" name="kri_datum_do_aktuelno" class="material-checkbox">
												<label class="material-checkbox-group__label" for="kri_datum_do_aktuelno"><?php echo $txt_reg_koraktri_aktuelno; ?></label>
											</div>
										</div>
										<script>
										$( document ).ready(function() {
											$('#kri_datum_do_aktuelno').click(function()
											{
												//If checkbox is checked then disable or enable input
												if ($(this).is(':checked'))
												{
													$("#kri_datum_do_mjesec").removeAttr("disabled");
													$("#kri_datum_do_mjesec").attr("disabled","disabled");
													//$("#kri_datum_do_mjesec").val('0000-00-00');
													
													$("#kri_datum_do_godina").removeAttr("disabled");
													$("#kri_datum_do_godina").attr("disabled","disabled");
													//$("#kri_datum_do_godina").val('0000-00-00');
												}
												//If checkbox is unchecked then disable or enable input
												else
												{
													$("#kri_datum_do_mjesec").removeAttr("disabled");
													//$("#kri_datum_do_mjesec").val('');
													$("#kri_datum_do_godina").removeAttr("disabled");
													//$("#kri_datum_do_godina").val('');
												}
											});
										});
										</script>
									</div>
									<!-- DATUM DO END - pojedinačno mjesec i godina pomocu selecta-->
									<div class="form-group">
										<label for="kri_pozicija" class="col-sm-3 control-label">
											<span class="text-danger">
												*
											</span> 
											<?php echo $txt_reg_korakcetiri_pozicija; ?>: 
											<!--<i class="fa fa-info-circle fa-lg idk_margin_left10" data-toggle="tooltip" data-placement="right" title="<?php //echo $txt_reg_korakpet_etc_info; ?>" aria-hidden="true">
											</i>-->
										</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kri_pozicija" id="kri_pozicija" placeholder="<?php echo $txt_reg_korakcetiri_pozicija; ?>" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="kri_naziv" class="col-sm-3 control-label"><span class="text-danger"></span> <?php echo $txt_reg_korakcetiri_naziv_poslodavca; ?>:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kri_naziv" id="kri_naziv" placeholder="<?php echo $txt_reg_korakcetiri_naziv_poslodavca; ?>" >
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>

									<div class="form-group">
										<label for="kri_grad" class="col-sm-3 control-label"><span class="text-danger"></span> <?php echo $txt_reg_grad; ?>:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kri_grad" id="kri_grad" placeholder="<?php echo $txt_reg_grad; ?>" >
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<!--
									<div class="form-group">
										<label for="kri_drzava" class="col-sm-3 control-label"><span class="text-danger"></span> Država:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kri_drzava" id="kri_drzava" placeholder="Država" >
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="kri_adresa" class="col-sm-3 control-label"><span class="text-danger"></span> Adresa:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kri_adresa" id="kri_adresa" placeholder="Adresa" >
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="kri_telefon" class="col-sm-3 control-label"><span class="text-danger"></span> Telefon:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kri_telefon" id="kri_telefon" placeholder="Telefon" >
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="kri_email" class="col-sm-3 control-label"><span class="text-danger"></span> E-mail:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kri_email" id="kri_email" placeholder="E-mail" >
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="kri_web" class="col-sm-3 control-label"><span class="text-danger"></span> WEB:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kri_web" id="kri_web" placeholder="WEB" >
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									-->
									<div class="form-group">
										<label for="kri_opis" class="col-sm-3 control-label"><span class="text-danger"></span> <?php echo $txt_reg_opis; ?>:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<textarea class="form-control materail-input material-textarea" name="kri_opis" id="kri_opis"></textarea>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<div class="col-sm-offset-2 col-sm-10 text-right">
											<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-plus" aria-hidden="true"></i> <span><?php echo $txt_reg_spremi; ?></span></button>
										</div>
									</div>
								</form>
							</div>
								<div class="col-md-12">
									<h4><?php echo $txt_reg_pregled_radno_iskustvo; ?>:</h4>
									<table class="table table-hover">
										<thead>
											<tr>

												<th class="text-center"><?php echo $txt_reg_korakcetiri_pozicija; ?></th>
												<th class="text-center"><?php echo $txt_reg_poslodavac; ?></th>
												<th class="text-center"><?php echo $txt_reg_datum_od; ?></th>
												<th class="text-center"><?php echo $txt_reg_datum_do; ?></th>
												<th class="text-center"><?php echo $txt_reg_grad; ?></th>
												<th class="text-center"><?php echo $txt_reg_opis; ?></th>
												<th class="text-center"><?php echo $txt_reg_izbrisi; ?></th>
											</tr>
										</thead>
										<tbody>
											<?php
												$select_query = $db->prepare("
																	SELECT kri_id, kri_darum_od, kri_datum_do, kri_pozicija, kri_naziv, kri_grad, kri_opis, kri_aktuelno
																	FROM idk_kandidat_radno_iskustvo
																	WHERE kri_kandidat_id = :kri_kandidat_id");

												$select_query->execute(array(
																':kri_kandidat_id' => $kandidat_id));

												while($select_row = $select_query->fetch()) {

													$kri_id = $select_row['kri_id'];
													$kri_darum_od = $select_row['kri_darum_od'];
													$kri_darum_od_f = date('m.Y', strtotime($kri_darum_od));
													$kri_datum_do = $select_row['kri_datum_do'];
													$kri_pozicija = $select_row['kri_pozicija'];
													$kri_naziv = $select_row['kri_naziv'];
													$kri_grad = $select_row['kri_grad'];
													$kri_opis = $select_row['kri_opis'];
													$kri_aktuelno = $select_row['kri_aktuelno'];


													if($kri_aktuelno != 1){
														$kri_datum_do_f = date('m.Y', strtotime($kri_datum_do));
													}else{
														$kri_datum_do_f = "Aktuelno";
													}

											?>
											<tr>
												<td class="text-center"><?php echo $kri_pozicija; ?></td>
												<td class="text-center"><?php echo $kri_naziv; ?></td>
												<td class="text-center"><?php echo $kri_darum_od_f; ?></td>
												<td class="text-center"><?php echo $kri_datum_do_f; ?></td>
												<td class="text-center"><?php echo $kri_grad; ?></td>
												<td class="text-center"><?php echo $kri_opis; ?></td>
												<td class="text-center"><a href="<?php getSiteURL(); ?>public_kandidati?page=delete_kriskustvo&id=<?php echo $kri_id; ?>&check=<?php echo $kandidat_check; ?>" class="material-dropdown-menu__link"><i class="fa fa-times" aria-hidden="true"></i></a></td>
											</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>
								<br />
								<div class="form-group">
									<div class="col-sm-12 text-right">
										<a href="<?php getSiteURL(); ?>registracija/korak6/<?php echo $kandidat_id; ?>/<?php echo $kandidat_check; ?>"><button class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-arrow-right" aria-hidden="true"></i> <span><?php echo $txt_reg_nastavi; ?></span></button></a>
										<br /><small><?php echo $txt_reg_korakjedan_obavezna_polja1; ?><span class="text-danger">*</span><?php echo $txt_reg_korakjedan_obavezna_polja2; ?></small>
									</div>
								</div>
						</div>
					</div>
				</div>
			</div>
		<?php
			}else{}
				}else{
					header("Location: ".getSiteURLR()."registracija/korak6/".$kandidat_id."/".$kandidat_check);
				}
				break;
				case "step5":

				$kandidat_id = $_REQUEST['id'];
				$kandidat_check = $_REQUEST['check'];
				
				// CHECK IF USER EXISTS IN DATABASE
				if(checkUserR($kandidat_id, $kandidat_check) == 1){
		?>
			
            <div class="row">
                <div class="col-xs-12">
                </div>
            </div>
			<div class="row">
				<div class="col-xs-12">
					<h1><b><i class="fa fa-user-circle-o idk_color_green" aria-hidden="true"></i> <?php echo $txt_reg_korakpet_korak_pet; ?></b></h1>
				</div>
				<div class="col-xs-12 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>registracija/korak4/<?php echo $kandidat_id; ?>/<?php echo $kandidat_check; ?>"><button class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column"><i class="fa fa-arrow-left" aria-hidden="true"></i> <span><?php echo $txt_reg_nazad; ?></span></button></a>

					<a href="<?php getSiteURL(); ?>registracija/korak6/<?php echo $kandidat_id; ?>/<?php echo $kandidat_check; ?>"><button class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column"><i class="fa fa-arrow-right" aria-hidden="true"></i> <span><?php echo $txt_reg_preskoci; ?></span></button></a>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<div class="row">
							<div class="col-md-offset-1 col-md-8">
								<form action="<?php getSiteURL(); ?>public_kandidati?page=add_kandidat_vjestine" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<input type="hidden" name="kv_kandidat_id" value="<?php echo $kandidat_id; ?>">
									<input type="hidden" name="kv_kandidat_check" value="<?php echo $kandidat_check; ?>">
									 <input type="hidden" name="recaptcha_response" id="recaptchaResponse">
									<div class="form-group">
										<label for="kv_grupa" class="col-sm-3 control-label"><span class="text-danger">*</span> <?php echo $txt_reg_korakpet_tip_vjestine; ?>:</label>
										<div class="col-sm-9">
                                            <select class="selectpicker" id="kv_grupa" name="kv_grupa" title="<?php echo $txt_reg_korakjedan_nista_izabrano; ?>">
                                                <option value=""></option>
    											<option value="1"><?php echo $txt_reg_korakpet_osnovne_vjestine; ?></option>
    											<option value="2"><?php echo $txt_reg_korakpet_digitalne_kompetencije; ?></option>
    											<option value="3"><?php echo $txt_reg_korakpet_dodatne_informacije; ?></option>
    										</select>
										</div>
									</div>
									<div class="form-group">
										<label for="kv_naziv" class="col-sm-3 control-label"><span class="text-danger">*</span> <?php echo $txt_reg_naziv; ?>: <i class="fa fa-info-circle fa-lg idk_margin_left10" data-toggle="tooltip" data-placement="right" title="etc. rad na microsoft alatima, poznavanje rada na računaru ..." aria-hidden="true"></i></label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kv_naziv" id="kv_naziv" placeholder="<?php echo $txt_reg_naziv; ?> " >
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="kv_opis" class="col-sm-3 control-label"><span class="text-danger"></span> <?php echo $txt_reg_opis; ?>:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<textarea class="form-control materail-input material-textarea" name="kv_opis" id="<?php echo $txt_reg_opis; ?>"></textarea>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<div class="col-sm-offset-2 col-sm-10 text-right">
											<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-plus" aria-hidden="true"></i> <span><?php echo $txt_reg_spremi; ?></span></button>
										</div>
									</div>
								</form>
							</div>
								<div class="col-md-12">
									<h4><?php echo $txt_reg_pregled_vjestina; ?>:</h4>
									<table class="table table-hover">
										<thead>
											<tr>

												<th class="text-center"><?php echo $txt_reg_korakpet_tip_vjestine; ?></th>
												<th class="text-center"><?php echo $txt_reg_naziv; ?></th>
												<th class="text-center"><?php echo $txt_reg_opis; ?></th>
												<th class="text-center"><?php echo $txt_reg_izbrisi; ?></th>
											</tr>
										</thead>
										<tbody>
											<?php
												$select_query = $db->prepare("
																	SELECT kv_id, kv_naziv, kv_grupa, kv_opis
																	FROM idk_kandidat_vjestine
																	WHERE kv_kandidat_id = :kv_kandidat_id");

												$select_query->execute(array(
																':kv_kandidat_id' => $kandidat_id));

												while($select_row = $select_query->fetch()) {

													$kv_id = $select_row['kv_id'];
													$kv_naziv = $select_row['kv_naziv'];
													$kv_grupa = $select_row['kv_grupa'];
													if($kv_grupa == 1){
														$kv_grupa = "Osnovne vještine";
													}elseif($kv_grupa == 2){
														$kv_grupa = "Digitalne kompetencije";
													}elseif($kv_grupa == 3){
														$kv_grupa = "Dodatne informacije";
													}else{};
													$kv_opis = $select_row['kv_opis'];
											?>
											<tr>
												<?php
													if($lg_language == "de" and $kv_grupa == "Osnovne vještine"){
														$kv_grupa = "Grundfähigkeiten";
													}
													else if($lg_language == "de" and $kv_grupa == "Digitalne kompetencije"){
														$kv_grupa = "Digitale Fähigkeiten";
													}
													else if($lg_language == "de" and $kv_grupa == "Dodatne informacije"){
														$kv_grupa = "Zusätzliche Informationen";
													}
												?>
												<td class="text-center"><?php echo $kv_grupa; ?></td>
												<td class="text-center"><?php echo $kv_naziv; ?></td>
												<td class="text-center"><?php echo $kv_opis; ?></td>
												<td class="text-center"><a href="<?php getSiteURL(); ?>public_kandidati?page=delete_kandidat_vjestina&id=<?php echo $kv_id; ?>&check=<?php echo $kandidat_check; ?>" class="material-dropdown-menu__link"><i class="fa fa-times" aria-hidden="true"></i></a></td>
											</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>
								<br />
								<div class="form-group">
									<div class="col-sm-12 text-right">
										<a href="<?php getSiteURL(); ?>registracija/korak6/<?php echo $kandidat_id; ?>/<?php echo $kandidat_check; ?>"><button class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-arrow-right" aria-hidden="true"></i> <span><?php echo $txt_reg_nastavi; ?></span></button></a>
										<br /><small><?php echo $txt_reg_korakjedan_obavezna_polja1; ?><span class="text-danger">*</span><?php echo $txt_reg_korakjedan_obavezna_polja2; ?></small>
									</div>
								</div>
						</div>
					</div>
				</div>
			</div>
		<?php

		}else{}
			
				break;
				case "step6":

				$kandidat_id = $_REQUEST['id'];
				$kandidat_check = $_REQUEST['check'];


				$check_german_query = $db->prepare("
									SELECT kj_id, kj_naziv, kj_slusanje, kj_citanje, kj_govorna_interakcija, kj_govorna_produkcija, kj_pisanje
									FROM idk_kandidat_jezici
									WHERE kj_kandidatid = :kj_kandidatid AND kj_naziv = :kj_naziv");

				$check_german_query->execute(array(
								':kj_kandidatid' => $kandidat_id,
								':kj_naziv' => "Njemački"
								));

				$check_german = $check_german_query->rowCount();

				// CHECK IF USER EXISTS IN DATABASE
				if(checkUserR($kandidat_id, $kandidat_check) == 1){
		?>
			
            <div class="row">
                <div class="col-xs-12">
                </div>
            </div>
			<div class="row">
				<div class="col-xs-12">
					<h1><b><i class="fa fa-user-circle-o idk_color_green" aria-hidden="true"></i> <?php echo $txt_reg_koraksest_korak_sest; ?></b></h1>
				</div>
				<div class="col-xs-12 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>registracija/korak4/<?php echo $kandidat_id; ?>/<?php echo $kandidat_check; ?>"><button class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column"><i class="fa fa-arrow-left" aria-hidden="true"></i> <span><?php echo $txt_reg_nazad; ?></span></button></a>
					<?php if($check_german === 1){ ?>
					<a href="<?php getSiteURL(); ?>registracija/korak7/<?php echo $kandidat_id; ?>/<?php echo $kandidat_check; ?>"><button class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column"><i class="fa fa-arrow-right" aria-hidden="true"></i> <span><?php echo $txt_reg_preskoci; ?></span></button></a>
					<?php }else{ ?>
					<a href="" disabled><button disabled class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column"><i class="fa fa-arrow-right" aria-hidden="true"></i> <span><?php echo $txt_reg_preskoci; ?></span></button></a>
					<?php } ?>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<div class="row">
							<div class="col-md-offset-1 col-md-8">
								<form action="<?php getSiteURL(); ?>public_kandidati?page=add_kandidat_jezik" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<input type="hidden" name="kj_kandidatid" value="<?php echo $kandidat_id; ?>">
									<input type="hidden" name="kj_kandidat_check" value="<?php echo $kandidat_check; ?>">
									 <input type="hidden" name="recaptcha_response" id="recaptchaResponse">
									<div class="form-group">
										<label for="kj_znanje_njemacki" class="col-sm-3 control-label"><span class="text-danger">*</span> <?php echo $txt_reg_koraksest_znanje_njemackog; ?>:</label>
										<div class="col-sm-9">
                                            <select class="selectpicker" id="kj_znanje_njemacki" name="kj_znanje_njemacki" title="<?php echo $txt_reg_korakjedan_nista_izabrano; ?>">
                                                <option value=""></option>
    											<option value="Bez znanja"><?php echo $txt_reg_bez_znanja ; ?></option>
    											<option value="A1">A1</option>
    											<option value="A2">A2</option>
    											<option value="B1">B1</option>
    											<option value="B2">B2</option>
    											<option value="C1">C1</option>
    											<option value="C2">C2</option>
    										</select>
										</div>
									</div>
									<div style="display: <?php echo $nbp_ostali_jezici;?>">
										<div class="form-group">
											<label for="kj_naziv" class="col-sm-3 control-label"> <?php echo $txt_reg_koraksest_ostali_jezici; ?>:</label>
											<div class="col-sm-9">
												<select class="selectpicker" id="kj_naziv" name="kj_naziv" title="<?php echo $txt_reg_korakjedan_nista_izabrano; ?>">
													<option value=""></option>
													<option value="Engleski"><?php echo $txt_reg_engleski; ?></option>
													<option value="Talijanski"><?php echo $txt_reg_talijanski; ?></option>
													<option value="Francuski"><?php echo $txt_reg_francuski; ?></option>
													<option value="Španjolski"><?php echo $txt_reg_spanjolski; ?></option>
													<option value="Arapski"><?php echo $txt_reg_arapski; ?></option>
													<option value="Danski"><?php echo $txt_reg_danski; ?></option>
													<option value="ostalo"><?php echo $txt_reg_ostaloV; ?></option>
												</select>
											</div>
										</div>
										<script>
										$( document ).ready(function() {
											$('#drugi_jezik').hide();

											$('#kj_naziv').on('change', function (e) {
												if(($('#kj_naziv').selectpicker('val') == "ostalo"))
												{
													$('#drugi_jezik').slideDown();
												}else{
													$('#drugi_jezik').hide();
												}
											});
										});
										</script>
										<div id="drugi_jezik" class="form-group">
											<label for="kj_ostalo" class="col-sm-3 control-label"><span class="text-danger">*</span> <?php echo $txt_reg_naziv_jezika; ?></label>
											<div class="col-sm-9">
												<div class="materail-input-block materail-input-block_success">
													<input class="form-control materail-input" type="text" name="kj_ostalo" id="kj_ostalo" placeholder="<?php echo $txt_reg_naziv_jezika; ?>" >
													<span class="materail-input-block__line"></span>
												</div>
											</div>
										</div>

										<div class="form-group">
											<label for="kj_znanje" class="col-sm-3 control-label"><?php echo $txt_reg_koraksest_znanje; ?>:</label>
											<div class="col-sm-9">
												<select class="selectpicker" id="kj_znanje" name="kj_znanje" title="<?php echo $txt_reg_korakjedan_nista_izabrano; ?>">
													<option value=""></option>
													<option value="A1">A1</option>
													<option value="A2">A2</option>
													<option value="B1">B1</option>
													<option value="B2">B2</option>
													<option value="C1">C1</option>
													<option value="C2">C2</option>
												</select>
											</div>
										</div>
									</div>
									<!--
									<div class="form-group">
										<label for="kj_slusanje" class="col-sm-3 control-label"><span class="text-danger">*</span> Slušanje:</label>
										<div class="col-sm-9">
                                            <select class="selectpicker" id="kj_slusanje" name="kj_slusanje">
                                                <option value=""></option>
    											<option value="A1">A1</option>
    											<option value="A2">A2</option>
    											<option value="B1">B1</option>
    											<option value="B2">B2</option>
    											<option value="C1">C1</option>
    											<option value="C2">C2</option>
    										</select>
										</div>
									</div>
									<div class="form-group">
										<label for="kj_citanje" class="col-sm-3 control-label"><span class="text-danger">*</span> Čitanje:</label>
										<div class="col-sm-9">
                                            <select class="selectpicker" id="kj_citanje" name="kj_citanje">
                                                <option value=""></option>
    											<option value="A1">A1</option>
    											<option value="A2">A2</option>
    											<option value="B1">B1</option>
    											<option value="B2">B2</option>
    											<option value="C1">C1</option>
    											<option value="C2">C2</option>
    										</select>
										</div>
									</div>
									<div class="form-group">
										<label for="kj_govorna_interakcija" class="col-sm-3 control-label"><span class="text-danger">*</span> Govorna interakcija:</label>
										<div class="col-sm-9">
                                            <select class="selectpicker" id="kj_govorna_interakcija" name="kj_govorna_interakcija">
                                                <option value=""></option>
    											<option value="A1">A1</option>
    											<option value="A2">A2</option>
    											<option value="B1">B1</option>
    											<option value="B2">B2</option>
    											<option value="C1">C1</option>
    											<option value="C2">C2</option>
    										</select>
										</div>
									</div>
									<div class="form-group">
										<label for="kj_govorna_produkcija" class="col-sm-3 control-label"><span class="text-danger">*</span> Govorna produkcija:</label>
										<div class="col-sm-9">
                                            <select class="selectpicker" id="kj_govorna_produkcija" name="kj_govorna_produkcija">
                                                <option value=""></option>
    											<option value="A1">A1</option>
    											<option value="A2">A2</option>
    											<option value="B1">B1</option>
    											<option value="B2">B2</option>
    											<option value="C1">C1</option>
    											<option value="C2">C2</option>
    										</select>
										</div>
									</div>
									<div class="form-group">
										<label for="kj_pisanje" class="col-sm-3 control-label"><span class="text-danger">*</span> Pisanje:</label>
										<div class="col-sm-9">
                                            <select class="selectpicker" id="kj_pisanje" name="kj_pisanje">
                                                <option value=""></option>
    											<option value="A1">A1</option>
    											<option value="A2">A2</option>
    											<option value="B1">B1</option>
    											<option value="B2">B2</option>
    											<option value="C1">C1</option>
    											<option value="C2">C2</option>
    										</select>
										</div>
									</div>
									-->
									<div class="form-group">
										<div class="col-sm-offset-2 col-sm-10 text-right">
											<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-plus" aria-hidden="true"></i> <span><?php echo $txt_reg_spremi; ?></span></button>
										</div>
									</div>
								</form>
							</div>
								<div class="col-md-12">
									<h4><?php echo $txt_reg_koraksest_pregled_jezika; ?>:</h4>
									<div class="table-responsive">
									<table class="table table-hover">
										<thead>
											<tr>

												<th class="text-center"><?php echo $txt_reg_jezik; ?></th>
												<th class="text-center"><?php echo $txt_reg_slusanje; ?></th>
												<th class="text-center"><?php echo $txt_reg_citanje; ?></th>
												<th class="text-center"><?php echo $txt_reg_koraksest_govorna_interakcija; ?></th>
												<th class="text-center"><?php echo $txt_reg_koraksest_govorna_produkcija; ?></th>
												<th class="text-center"><?php echo $txt_reg_koraksest_pisanje; ?></th>
												<th class="text-center"><?php echo $txt_reg_izbrisi; ?></th>
											</tr>
										</thead>
										<tbody>
											<?php
												$select_query = $db->prepare("
																	SELECT kj_id, kj_naziv, kj_slusanje, kj_citanje, kj_govorna_interakcija, kj_govorna_produkcija, kj_pisanje
																	FROM idk_kandidat_jezici
																	WHERE kj_kandidatid = :kj_kandidatid");

												$select_query->execute(array(
																':kj_kandidatid' => $kandidat_id));

												while($select_row = $select_query->fetch()) {

													$kj_id = $select_row['kj_id'];
													$kj_naziv = $select_row['kj_naziv'];
													$kj_slusanje = $select_row['kj_slusanje'];
													$kj_citanje = $select_row['kj_citanje'];
													$kj_govorna_interakcija = $select_row['kj_govorna_interakcija'];
													$kj_govorna_produkcija = $select_row['kj_govorna_produkcija'];
													$kj_pisanje = $select_row['kj_pisanje'];
											?>
											<tr>

												<td class="text-center"><?php echo $kj_naziv; ?></td>
												<td class="text-center"><?php echo $kj_slusanje; ?></td>
												<td class="text-center"><?php echo $kj_citanje; ?></td>
												<td class="text-center"><?php echo $kj_govorna_interakcija; ?></td>
												<td class="text-center"><?php echo $kj_govorna_produkcija; ?></td>
												<td class="text-center"><?php echo $kj_pisanje; ?></td>
												<td class="text-center"><a href="<?php getSiteURL(); ?>public_kandidati?page=delete_kandidat_jezik&id=<?php echo $kj_id; ?>&check=<?php echo $kandidat_check; ?>" class="material-dropdown-menu__link"><i class="fa fa-times" aria-hidden="true"></i></a></td>
											</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>
								</div>
								<br />
								<div class="form-group">
									<div class="col-sm-12 text-right">
									<?php if($check_german === 1){ ?>
										<a href="<?php getSiteURL(); ?>registracija/korak7/<?php echo $kandidat_id; ?>/<?php echo $kandidat_check; ?>"><button class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-arrow-right" aria-hidden="true"></i> <span><?php echo $txt_reg_nastavi; ?></span></button></a>
										<br /><small><?php echo $txt_reg_korakjedan_obavezna_polja1; ?><span class="text-danger">*</span><?php echo $txt_reg_korakjedan_obavezna_polja2; ?></small>
									<?php }else{ ?>
										<a href="" disabled><button disabled class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-arrow-right" aria-hidden="true"></i> <span><?php echo $txt_reg_nastavi; ?> <?php echo $txt_reg_koraksest_molimo_unesite; ?></span></button></a>

									<?php } ?>
									</div>
								</div>
						</div>
					</div>
				</div>
			</div>
		<?php
		}else{}
				break;

				case "step7":

				$kandidat_id = $_REQUEST['id'];
				$kandidat_check = $_REQUEST['check'];
				
				if($nbp_korak7 == "1"){

				// CHECK IF USER EXISTS IN DATABASE
				if(checkUserR($kandidat_id, $kandidat_check) == 1){
					
					
					$query_group = $db->prepare("
					SELECT kandidat_group
					FROM idk_kandidati
					WHERE kandidat_id = $kandidat_id
					");

					$query_group->execute();

					$row_group = $query_group->fetch();

					$kandidat_group = $row_group['kandidat_group'];
					
		?>
			
           <div class="row">
                <div class="col-xs-12">
                </div>
            </div>
			<div class="row">
				<div class="col-xs-12">
					<h1><b><i class="fa fa-user-circle-o idk_color_green" aria-hidden="true"></i> <?php echo $txt_reg_koraksedam_korak_sedam; ?></b></h1>
				</div>
				<div class="col-xs-12 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>registracija/korak6/<?php echo $kandidat_id; ?>/<?php echo $kandidat_check; ?>"><button class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column"><i class="fa fa-arrow-left" aria-hidden="true"></i> <span><?php echo $txt_reg_nazad; ?></span></button></a>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<div class="row">
							<div class="col-md-offset-1 col-md-8">
										<form action="<?php getSiteURL(); ?>public_kandidati?page=add_kandidat_doc_form" enctype="multipart/form-data" method="post" role="form" class="form-horizontal">
											 <input type="hidden" name="recaptcha_response" id="recaptchaResponse">
											<div id="idk_alert_size" class="row hidden">
												<div class="col-sm-12">
													<div class="alert material-alert material-alert_danger"><?php echo $txt_reg_greska_dokument_velicina; ?></div>
												</div>
											</div>
											<div id="idk_alert_ext" class="row hidden">
												<div class="col-sm-12">
													<div class="alert material-alert material-alert_danger"><?php echo $txt_reg_greska_dokument_format; ?></div>
												</div>
											</div>
											<input type="hidden" name="document_dataid" value="<?php echo $kandidat_id; ?>" />
											<input type="hidden" name="kandidat_id" value="<?php echo $kandidat_id; ?>" />
											<input type="hidden" name="kandidat_check" value="<?php echo $kandidat_check; ?>" />
											<div class="form-group">
												<div class="col-sm-8">
													<select class="selectpicker" id="document_name" name="document_name" title="<?php echo $txt_reg_korakjedan_nista_izabrano; ?>" required>
														<option value=""></option>
														<?php if($nbp_slika == "1") { ?><option value="Slika"><?php echo $txt_reg_fotografija; ?></option><?php } ?>
														<?php if($nbp_diploma == "1") { ?><option value="Diploma završene škole"><?php echo $txt_reg_koraksedam_diploma; ?></option><?php } ?>
														<?php if($kandidat_group == "2" or $kandidat_group == "6" or $kandidat_group == "23") { ?>
														<?php if($nbp_pripravnicki == "1") { ?><option value="Uvjerenje o pripravničkom stažu"><?php echo $txt_reg_koraksedam_uvjerenje_pripravnicki; ?></option><?php } ?>
														<?php if($nbp_strucni == "1") { ?><option value="Uvjerenje o položenom stručnom ispitu"><?php echo $txt_reg_koraksedam_uvjerenje_strucni; ?></option><?php } ?>
														<?php } ?>
														<?php if($nbp_jezik_cert == "1") { ?><option value="Certifikati o poznavanju jezika"><?php echo $txt_reg_koraksedam_certifikat_jezik; ?></option><?php } ?>
														<option value="Dodatni certifikati"><?php echo $txt_reg_koraksedam_dodatni_certifikati; ?></option>
														<option value="Ugovori"><?php echo $txt_reg_koraksedam_ugovori; ?></option>
														<option value="Ostalo"><?php echo $txt_reg_koraksedam_ostalo; ?></option>
													</select>
												</div>
											</div>

											<div class="form-group">
												<div class="col-sm-8">
													<div class="form-group materail-input-block materail-input-block_success">
														<input type="text" class="form-control materail-input" name="document_desc" id="document_desc" placeholder="<?php echo $txt_reg_koraksedam_opis_dokumenta; ?>">
														<span class="materail-input-block__line"></span>
													</div>
												</div>
											</div>
											<div class="form-group">
												<div class="col-sm-8">
													<div class="fileinput fileinput-new" data-provides="fileinput">
														<span class="btn btn-default btn-file"><span class="fileinput-new"><?php echo $txt_reg_koraksedam_izaberi_dokument; ?></span><span class="fileinput-exists"><?php echo $txt_reg_promijeni; ?></span><input type="file" name="document_file" id="document_file" required required></span> <i class="fa fa-info-circle fa-lg idk_margin_left10" data-toggle="tooltip" data-placement="right" title="<?php echo $txt_reg_koraksedam_napomena; ?>" aria-hidden="true"></i>
														<span class="fileinput-filename"></span>
														<a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none">×</a>
														<script>
															$(function (){
																$('#document_file').change(function (){

																	var f = this.files[0];

																	if (f.size > 20388608 || f.fileSize > 20388608){
																		$('#idk_alert_size').removeClass('hidden');
																		this.value = null;
																	}else{
																		$('#idk_alert_size').addClass('hidden');
																	}

																	var ext = $('#document_file').val().split('.').pop().toLowerCase();

																	if($.inArray(ext, ['jpg', 'jpeg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png']) == -1) {
																		$('#idk_alert_ext').removeClass('hidden');
																		this.value = null;
																	}else{
																		$('#idk_alert_ext').addClass('hidden');
																	}
																})
															});
														</script>
													</div>
												</div>
											</div>
											<div class="form-group">
												<div class="col-sm-8 text-right">
													<button type="submit" class="btn btn-primary material-btn material-btn_primary"><?php echo $txt_reg_spremi; ?></button>
												</div>
											</div>
										</form>
								</div>


							</div>
							<br/>
							<div class="row">
								<div class="col-md-12">
									<h4><?php echo $txt_reg_pregled_dokumenata; ?>:</h4>
									<table class="table table-hover">
										<thead>
											<tr>
												<th>Datum</th>
												<th><?php echo $txt_reg_naziv; ?></th>
												<th><?php echo $txt_reg_opis; ?></th>
												<th><?php echo $txt_reg_koraksedam_preuzimanje; ?></th>
												<th><?php echo $txt_reg_izbrisi; ?></th>
											</tr>
										</thead>
										<tbody>
											<?php
												$query_doc = $db->prepare("
																SELECT document_id, document_name, document_desc, document_file, document_icon, document_datetime
																FROM idk_documents
																WHERE document_group = :document_group AND document_dataid = :document_dataid");

												$query_doc->execute(array(
															':document_group' => 2,
															':document_dataid' => $kandidat_id));

												while($row_doc = $query_doc->fetch()){

													$document_id = $row_doc['document_id'];
													$document_name = $row_doc['document_name'];
													$document_desc = $row_doc['document_desc'];
													$document_file = $row_doc['document_file'];
													$document_datetime = date('d.m.Y.', strtotime($row_doc['document_datetime']));

													if($row_doc['document_icon'] == "jpg"){
														$document_icon = '<i class="fa fa-file-image-o fa-lg" aria-hidden="true"></i>';
													}elseif($row_doc['document_icon'] == "pdf"){
														$document_icon = '<i class="fa fa-file-pdf-o fa-lg" aria-hidden="true"></i>';
													}elseif($row_doc['document_icon'] == "doc" OR $row_doc['document_icon'] == "docx"){
														$document_icon = '<i class="fa fa-file-word-o fa-lg" aria-hidden="true"></i>';
													}elseif($row_doc['document_icon'] == "xls" OR $row_doc['document_icon'] == "xlsx"){
														$document_icon = '<i class="fa fa-file-excel-o fa-lg" aria-hidden="true"></i>';
													}elseif($row_doc['document_icon'] == "txt"){
														$document_icon = '<i class="fa fa-file-text-o fa-lg" aria-hidden="true"></i>';
													}elseif($row_doc['document_icon'] == "ppt" OR $row_doc['document_icon'] == "pptx"){
														$document_icon = '<i class="fa fa-file-powerpoint-o fa-lg" aria-hidden="true"></i>';
													}else{
														$document_icon = '<i class="fa fa-file-o fa-lg" aria-hidden="true"></i>';
													}
											?>
											<tr>
												<td><?php echo $document_datetime; ?></td>
												<td><?php echo $document_name; ?></td>
												<td><?php echo $document_desc; ?></td>
												<td><a href="<?php getSiteURL(); ?>files/kandidati_doc/<?php echo $document_file; ?>" class="btn material-btn material-btn_success main-container__column" target="_BLANK"><?php echo $document_icon; ?></a></td>
												<td><a href="#" data="<?php getSiteURL(); ?>public_kandidati?page=del_doc&docid=<?php echo $document_id; ?>&id=<?php echo $kandidat_id; ?>&check=<?php echo $kandidat_check; ?>" data-toggle="modal" data-target="#deleteDocModal" class="delete_doc btn material-btn material-btn_danger main-container__column"><i class="fa fa-trash-o fa-lg" aria-hidden="true"></i></a></td>
											</tr>
											<?php } ?>
											<script>
												$(".delete_doc").click(function () {
													var addressValue = $(this).attr("data");
													document.getElementById("delete_doc_link").href = addressValue;
												});
											</script>
											<!-- Modal -->
											<div class="modal material-modal material-modal_danger fade" id="deleteDocModal">
												<div class="modal-dialog">
													<div class="modal-content material-modal__content">
														<div class="modal-header material-modal__header">
															<button class="close material-modal__close" data-dismiss="modal">&times;</button>
															<h4 class="modal-title material-modal__title"><?php echo $txt_reg_izbrisi;?></h4>
														</div>
														<div class="modal-body material-modal__body">
															<p><?php echo $txt_reg_koraksedam_sigurno_obrisati; ?></p>
														</div>
														<div class="modal-footer material-modal__footer">
															<button class="btn material-btn material-btn" data-dismiss="modal"><?php echo $txt_reg_zatvori; ?></button>
															<a id="delete_doc_link" href=""><button class="btn btn-primary material-btn material-btn_danger"><?php echo $txt_reg_obrisi; ?></button></a>
														</div>
													</div>
												</div>
											</div>
										</tbody>
									</table>
								</div>
								<br />
								<div class="form-group">
									<div class="col-sm-12 text-right">
										<a href="<?php getSiteURL(); ?>registracija/korak8/<?php echo $kandidat_id; ?>/<?php echo $kandidat_check; ?>"><button class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-arrow-right" aria-hidden="true"></i> <span><?php echo $txt_reg_koraksedam_zavrsi; ?></span></button></a>
										<br /><small><?php echo $txt_reg_korakjedan_obavezna_polja1; ?><span class="text-danger">*</span><?php echo $txt_reg_korakjedan_obavezna_polja2; ?></small>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php
		}else{}
			}else{
				header("Location: ".getSiteURLR()."registracija/korak8/".$kandidat_id."/".$kandidat_check);
			}
				break;

				case "step8":

				$kandidat_id = $_REQUEST['id'];
				$kandidat_check = $_REQUEST['check'];

				// CHECK IF USER EXISTS IN DATABASE
				if(checkUserR($kandidat_id, $kandidat_check) == 1){

				notifForOnlneRegister($kandidat_id, $kandidat_check);
				
		?>
			
            <div class="row">
                <div class="col-xs-12">
                </div>
            </div>
			<div class="row">
				<div class="col-xs-12 idk_margin_top10">
					<div class="alert material-alert material-alert_success"><h2><?php echo $txt_reg_zahvala; ?></h2><h4><?php echo $txt_reg_uspjesna_saradnja; ?></h4><br/></div>
				</div>
			</div>
		<?php
		}else{}
				break;
				
				case "step_prechat":
					
					$kandidat_id = $_REQUEST['id'];
					$kandidat_check = $_REQUEST['check'];
					$kandidat_lang = $_REQUEST['lang'];
					if($kandidat_lang == "de")
						include("lang/de.php");
					else
						include("lang/bs.php");

					// CHECK IF USER EXISTS IN DATABASE
					if(checkUserR($kandidat_id, $kandidat_check) == 1){
						
						// $get_phone = $db->prepare("
													// SELECT kki_podatak
													// FROM idk_kandidat_kontakt_info
													// WHERE kki_kandidat_id = :kki_kandidat_id AND kki_naziv = :kki_naziv ");

						// $get_phone->execute(array(
												// ':kki_kandidat_id' => $kandidat_id,
												// ':kki_naziv' => "Mobilni"
												// ));

						// $phone_row = $get_phone->fetch();

						// $phone = $phone_row['kki_podatak'];
						// $phone_f = str_replace("+", '00', $phone);
						
						//sendSmsToCandidate($random_string, $phone_f);
					?>
						
						<!-- Facebook Pixel Code -->
						<script>
						!function(f,b,e,v,n,t,s)
						{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
						n.callMethod.apply(n,arguments):n.queue.push(arguments)};
						if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
						n.queue=[];t=b.createElement(e);t.async=!0;
						t.src=v;s=b.getElementsByTagName(e)[0];
						s.parentNode.insertBefore(t,s)}(window,document,'script',
						'https://connect.facebook.net/en_US/fbevents.js');
						 fbq('init', '1231906870601181'); 
						fbq('track', 'PageView');
						fbq('track', 'Lead');
						</script>
						<noscript>
						 <img height="1" width="1" 
						src="https://www.facebook.com/tr?id=1231906870601181&ev=PageView
						&noscript=1"/>
						</noscript>
						<!-- End Facebook Pixel Code -->
						
						<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Jobstep Thank You Page</title>

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
        <div style="margin-top:0px;">
            <div class="row">
                <div class="col-lg-12" align="center" style="padding-bottom:20px;">
                    <img style="padding-top:10px;" width="40" height="50" src="<?php getSiteURL(); ?>images/thankyoupage/uspjesno.png"  />
                    <p><?php echo $txt_reg_ty_1; ?></p>
                </div>

            </div>
            <div class="row">

                <div class="col-lg-7 col-md-12 col-12 d-flex align-items-center">
                    <div class="about-text">
                        <h2><?php echo $txt_reg_ty_2; ?></h2>
                        <p><?php echo $txt_reg_ty_3; ?></p>
                        <div style=" position: relative; overflow: hidden; width: 100%; padding-top: 56.25%;">
                            <iframe style="position: absolute; top: 0; left: 0; bottom: 0; right: 0; width: 100%; height: 100%;" src="https://www.youtube.com/embed/rttgZz8ohEI"></iframe>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5" align="center" style="padding-top:30px;">
                    <img src="<?php getSiteURL(); ?>images/thankyoupage/jobstep_messenger.png" />
                </div>

            </div>
            <div class="row">
                <div class="col-lg-12" align="center">
                    <div class="custom-btn-group mt-4" style="margin-top:0px;">
                        <a href="https://play.google.com/store/apps/details?id=net.job_step.jobstepmessenger"><img src="<?php getSiteURL(); ?>images/thankyoupage/google_play.png" style="margin-bottom:10px;" /></a>
                        <a href="https://apps.apple.com/tt/app/jobstep-messenger/id1486805317"><img src="<?php getSiteURL(); ?>images/thankyoupage/app_store.png" style="margin-bottom:10px;" /></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!--Nostrifikacija diploma-->
    <section>
            <div class="row">
                <div class="col-lg-11 text-center mx-auto col-12">
                    <div class="col-lg-12 mx-auto" style="padding-top:60px;">
                        <h2><?php echo $txt_reg_ty_4; ?></h2>
                    </div>

                </div>

            </div>
            <div class="row" style="padding-bottom:60px;">
                <div class="col-lg-12" align="center" style="padding-top:20px;">
                    <p><?php echo $txt_reg_ty_5; ?></p>
                    <a href="https://job-step.net/usluga/priznavanje-diplome/3"><button type="button" class="btn btn-outline-info" style="border-radius: 50px; padding: 10px 50px 10px 50px; font-size: 20px;"><?php echo $txt_prijavi_se; ?></button></a>
                </div>
            </div>
    </section>

    <!--Jobstep Partner App-->
    <section class="justify-content-center align-items-center">
        <div class="container" style="margin-top:0px;">
            <div class="row">

                <div class="col-lg-7" align="center">
                    <img width="300" height="550" src="<?php getSiteURL(); ?>images/thankyoupage/jobstep_partner_app.png" />
                </div>

                <div class="col-lg-5" align="center" style="padding-top:30px;">
                    <div class="custom-btn-group mt-4 " style="padding-top:20px;">
                        <h2>Jobstep Partner App</h2>
                        <p><?php echo $txt_reg_ty_6; ?></p>
                        <a href="https://play.google.com/store/apps/details?id=com.partnerjobstep"><img width="220" height="65" src="<?php getSiteURL(); ?>images/thankyoupage/google_play_crna.png" style="margin-bottom:10px;" alt="Android - Jobstep Messenger" /></a>
                        <a href="https://apps.apple.com/tt/app/jobstep-partner-app/id1498761708?ign-mpt=uo%3D2"><img width="220" height="65" src="<?php getSiteURL(); ?>images/thankyoupage/app_store_crna.png" style="margin-bottom:10px;" alt="iOS - Jobstep Messenger" /></a>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!--Jobstep Sve za vizu-->
    <!-- <section class="justify-content-center align-items-center">
        <div class="container" style="margin-top:0px;">
            <div class="row">
                <div class="col-lg-5" align="center" style="padding-top:30px;">
                    <div class="custom-btn-group mt-4" style="padding-top:20px;">
                        <h2>Sve za vizu</h2>
                        <p><?php /*echo $txt_reg_ty_7; ?></p>
                        <a href="https://play.google.com/store/apps/details?id=com.jobstep.onlineviza"><img width="220" height="65" src="<?php getSiteURL(); ?>images/thankyoupage/google_play_crna.png" style="margin-bottom:10px;" /></a>
                        <a href="#!"><img width="220" height="65" src="<?php getSiteURL(); */ ?>images/thankyoupage/app_store_crna.png" style="margin-bottom:10px;" /></a>
                    </div>
                </div>

                <div class="col-lg-7" align="center">
                    <img width="300" height="550" src="<?php getSiteURL(); ?>images/thankyoupage/sve za vizu.png" />
                </div>

            </div>

        </div>
    </section> -->


    <!--Footer - Društvene mreže-->

    <footer class="text-center text-white" style="background-color: #f1f1f1; margin-top:30px;">

        <div class="container pt-4">
            <p>Zapratite nas na:</p>
            <section class="mb-4">
                <a href="https://www.facebook.com/JobStepInternational" alt="Jobstep Facebook"> <img width="35" height="35" src="<?php getSiteURL(); ?>images/thankyoupage/facebook_logo.png" /></a>
                <a href="https://www.instagram.com/jobstepinternational/" alt="Jobstep Instagram"> <img width="35" height="35" src="<?php getSiteURL(); ?>images/thankyoupage/instagram_logo.png" /> </a>
                <a href="https://www.linkedin.com/in/jobstepint/" alt="Jobstep LinkedIn" role="button" data-mdb-ripple-color="dark"> <img width="35" height="35" src="<?php getSiteURL(); ?>images/thankyoupage/linked_in_logo.png" /></a>
                <a href=" https://www.youtube.com/channel/UChL-bdalb8qxtYckAVZkXZw" alt="Jobstep Youtube" role="button" data-mdb-ripple-color="dark"> <img width="35" height="35" src="<?php getSiteURL(); ?>images/thankyoupage/youtube_logo.png" /></a>
            </section>

        </div>
    </footer>



</body>
</html>
					<?php
					}else{}
					
				break;

				case "delete_kkinfo":

						$kki_id = $_GET['id'];
						$kandidat_check = $_GET['check'];

						//Get note_txt and note_dataid
						$note_open_query = $db->prepare("
													SELECT kki_naziv, kki_kandidat_id
													FROM idk_kandidat_kontakt_info
													WHERE kki_id = :kki_id");

						$note_open_query->execute(array(
												':kki_id' => $kki_id));

						$note_open = $note_open_query->fetch();

							$kki_naziv = $note_open['kki_naziv'];
							$kki_kandidat_id = $note_open['kki_kandidat_id'];


						//Delete  from db
						$doc_del_query = $db->prepare("
													DELETE FROM idk_kandidat_kontakt_info
													WHERE kki_id = :kki_id");

						$doc_del_query->execute(array(
											':kki_id' => $kki_id));

						header("Location: registracija/korak2/$kki_kandidat_id/$kandidat_check");

				break;
				case "delete_kriskustvo":

						$kri_id = $_GET['id'];
						$kandidat_id = $_GET['check'];

						//Get note_txt and note_dataid
						$note_open_query = $db->prepare("
													SELECT kri_kandidat_id
													FROM idk_kandidat_radno_iskustvo
													WHERE kri_id = :kri_id");

						$note_open_query->execute(array(
												':kri_id' => $kri_id));

						$note_open = $note_open_query->fetch();

							$kri_kandidat_id = $note_open['kri_kandidat_id'];


						//Delete  from db
						$doc_del_query = $db->prepare("
													DELETE FROM idk_kandidat_radno_iskustvo
													WHERE kri_id = :kri_id");

						$doc_del_query->execute(array(
											':kri_id' => $kri_id));

						header("Location: registracija/korak4/$kri_kandidat_id/$kandidat_id");

				break;
				case "delete_kandidat_edukacija":

						$ke_id = $_GET['id'];
						$kandidat_check = $_GET['check'];

						//Get note_txt and note_dataid
						$note_open_query = $db->prepare("
													SELECT ke_kandidat_id
													FROM idk_kandidat_edukacija
													WHERE ke_id = :ke_id");

						$note_open_query->execute(array(
												':ke_id' => $ke_id));

						$note_open = $note_open_query->fetch();

							$ke_kandidat_id = $note_open['ke_kandidat_id'];


						//Delete  from db
						$doc_del_query = $db->prepare("
													DELETE FROM idk_kandidat_edukacija
													WHERE ke_id = :ke_id");

						$doc_del_query->execute(array(
											':ke_id' => $ke_id));

						header("Location: registracija/korak3/$ke_kandidat_id/$kandidat_check");

				break;
				case "delete_kandidat_vjestina":

						$kv_id = $_GET['id'];
						$kandidat_check = $_GET['check'];

						//Get note_txt and note_dataid
						$note_open_query = $db->prepare("
													SELECT kv_kandidat_id
													FROM idk_kandidat_vjestine
													WHERE kv_id = :kv_id");

						$note_open_query->execute(array(
												':kv_id' => $kv_id));

						$note_open = $note_open_query->fetch();

							$kv_kandidat_id = $note_open['kv_kandidat_id'];


						//Delete  from db
						$doc_del_query = $db->prepare("
													DELETE FROM idk_kandidat_vjestine
													WHERE kv_id = :kv_id");

						$doc_del_query->execute(array(
											':kv_id' => $kv_id));

						header("Location: registracija/korak5/$kv_kandidat_id/$kandidat_check");

				break;

				case "del_doc":

						$docid = $_GET['docid'];
						$kj_id = $_GET['id'];
						$kandidat_check = $_GET['check'];

						//Get document name, dataid and delete document
						$doc_open_query = $db->prepare("
													SELECT document_name, document_file, document_dataid
													FROM idk_documents
													WHERE document_id = :document_id AND document_dataid = :document_dataid");

						$doc_open_query->execute(array(
												':document_id' => $docid,
												':document_dataid' => $kj_id
												));

						$doc_open = $doc_open_query->fetch();

							$document_name = $doc_open['document_name'];
							$document_file = $doc_open['document_file'];
							$document_dataid = $doc_open['document_dataid'];

							unlink("files/kandidati_doc/" . $document_file);

						//Add to LOGS
						$log_desc = "Obrisao dokument: " . $document_name . " ";
						$log_date = date('Y-m-d H:i:s');

						$log_query = $db->prepare("
										INSERT INTO idk_logs
											(log_employeeid, log_desc, log_date)
										VALUES
											(:log_employeeid, :log_desc, :log_date)");

						$log_query->execute(array(
										':log_employeeid' => $logged_employee_id,
										':log_desc' => $log_desc,
										':log_date' => $log_date));

						//Delete document from db
						$doc_del_query = $db->prepare("
													DELETE FROM idk_documents
													WHERE document_id = :document_id AND document_dataid = :document_dataid");

						$doc_del_query->execute(array(
											':document_id' => $docid,
											':document_dataid' => $kj_id
											));

						header("Location: registracija/korak7/$kj_id/$kandidat_check");

				break;

				case "delete_kandidat_jezik":

						$kj_id = $_GET['id'];
						$kandidat_check = $_GET['check'];

						//Get note_txt and note_dataid
						$open_query = $db->prepare("
												SELECT kj_kandidatid
												FROM idk_kandidat_jezici
												WHERE kj_id = :kj_id");

						$open_query->execute(array(
											':kj_id' => $kj_id));

						$open = $open_query->fetch();

							$kj_kandidatid = $open['kj_kandidatid'];


						//Delete  from db
						$del_query = $db->prepare("
											DELETE FROM idk_kandidat_jezici
											WHERE kj_id = :kj_id");

						$del_query->execute(array(
										':kj_id' => $kj_id));

						header("Location: registracija/korak6/$kj_kandidatid/$kandidat_check");

				break;
				
				
				case "add_kandidat":

					$kandidat_ime = $_POST['kandidat_ime'];
					$kandidat_prezime = $_POST['kandidat_prezime'];
					$urlid = $_POST['urlid'];
					$partner_token = $_POST['token'];
					$kandidat_datumrodjenja = date("Y-m-d", strtotime($_POST['kandidat_datumrodjenja']));
					
					//Check if user exist
					$check_query2 = $db->prepare("
											SELECT lg_id, lg_url, lg_nalogid
											FROM idk_link_generator
											WHERE lg_id = :lg_id");
				
					$check_query2->execute(array(
									':lg_id' => $urlid));
				
					$num = $check_query2->rowCount();
					$rowlg = $check_query2->fetch();
						
						$lg_nalogid = $rowlg['lg_nalogid'];
				
					if($num > 0){
						$url_id = $_POST['urlid'];
					}else{
						$url_id = 1;
					}
					
				
					//napravi username po imenu, prezimenu i godini
					$kandidat_datumrodjenja_username = date("dmY", strtotime($_POST['kandidat_datumrodjenja']));
					$ime_korime = strtolower($kandidat_ime);
					$prezime_korime = strtolower($kandidat_prezime);
					$imeprezime = $ime_korime.''.$prezime_korime;
					$search = array("ć", "č", "ž", "š", "đ");
					$replacement = array("c", "c", "z", "s", "dj");
					$imeprezime_korime = str_replace($search, $replacement, $imeprezime);
					
					// Provjera da li postoji korisnik sa istim imenom i prezimenom u zadnjih sat vremena
					$granicno_vrijeme = date( "Y-m-d H:i:s",  strtotime("- 24 hour" ));
					$check_user = $db->prepare("
											SELECT kandidat_ime
											FROM idk_kandidati
											WHERE kandidat_ime = :kandidat_ime AND kandidat_prezime = :kandidat_prezime AND kandidat_datetime > :kandidat_datetime ");
				
					$check_user->execute(array(
									':kandidat_ime' => $kandidat_ime,
									':kandidat_prezime' => $kandidat_prezime,
									':kandidat_datetime' => $granicno_vrijeme
									));
				
					$number_of_rows_user = $check_user->rowCount();
				
					if($number_of_rows_user == 0){
					
						// nisu ulazile duple prijave zbog korisnickog imena koji je kombinacija imenaprezimenadatumarodjenja, pa sam dodao
						//jos jedan token od 3 broja na to da moze ulaziti
						$random_token = rand(100, 999);
						$kandidat_korisnickoime_uf = $imeprezime_korime.''.$kandidat_datumrodjenja_username.''.$random_token;
						$kandidat_korisnickoime = str_replace(' ', '', strtolower($kandidat_korisnickoime_uf));
						
						
					
						//Check if user exist
						$check_query = $db->prepare("
												SELECT kandidat_email
												FROM idk_kandidati
												WHERE kandidat_korisnickoime = :kandidat_korisnickoime");
					
						$check_query->execute(array(
										':kandidat_korisnickoime' => $kandidat_korisnickoime));
					
						$number_of_rows = $check_query->rowCount();
					
						if($number_of_rows == 0){
					
					
							$kandidat_spol = $_POST['kandidat_spol'];
							$kandidat_djevojackoprezime = $_POST['kandidat_djevojackoprezime'];
							if(!empty($_POST['kandidat_jmbg'])){ $kandidat_jmbg = $_POST['kandidat_jmbg']; }else{ $kandidat_jmbg = 0; }
							$kandidat_brojlk = $_POST['kandidat_brojlk'];
					
							$kandidat_mjestorodjenja = $_POST['kandidat_mjestorodjenja'];
							$kandidat_drzavarodjenja = $_POST['kandidat_drzavarodjenja'];
							$kandidat_drzavljanstvo = $_POST['kandidat_drzavljanstvo'];
							$kandidat_drzavljanstvo_vrsta = $_POST['kandidat_drzavljanstvo_vrsta'];
							$kandidat_adresa = $_POST['kandidat_adresa'];
							$kandidat_grad = $_POST['kandidat_grad'];
							$kandidat_pbroj = $_POST['kandidat_pbroj'];
							$kandidat_drzava = $_POST['kandidat_drzava'];
							$kandidat_visitedurl = $_POST['kandidat_visitedurl'];
							$kandidat_email = $_POST['kandidat_email'];
							$kandidat_datum_termina = $_POST['kandidat_termin_date'];
							$kandidat_datum_aplikacije = $_POST['kandidat_termin_date_app'];
							$kandidat_group = $_POST['kandidat_prijava_na'];
							$datum_aplikacije = date("Y-m-d", strtotime($kandidat_datum_aplikacije));
							
							
							// AKO nije unio termin za vizu a jeste datum aplikacije - Dodaj 18 mjeseci na datum aplikacije
							if($kandidat_datum_aplikacije != NULL){
								$kandidat_datum_aplikacije = date("Y-m-d", strtotime($kandidat_datum_aplikacije));
								
								if($kandidat_datum_termina == NULL){
									// AUTOMATSKI PROCJENJEN TERMIN - Dodano 18mj na datum apliciranja
									$kandidat_procjenatermina = 1;
									$kandidat_datum_termina = date('Y-m-d', strtotime("+20 months", strtotime($kandidat_datum_aplikacije)));
								}else{
									$kandidat_procjenatermina = 0;
									$kandidat_datum_termina = $_POST['kandidat_termin_date'];
								}
							}else{
								$kandidat_datum_aplikacije = NULL;
								$kandidat_datum_termina = NULL;
							}
								$mjesectermina = date("m",strtotime($kandidat_datum_termina));
								$godinatermina = date("Y",strtotime($kandidat_datum_termina));
							
							
						//	echo "Datum aplikacije: ".$kandidat_datum_aplikacije."<br/>";
						//	echo "Termin za vizu: ".$kandidat_datum_termina."<br/>";
						//	echo "Nalog id: ".$lg_nalogid."<br/>";
						//	echo "Mjesec termina: ".$mjesectermina."<br/>";
							
							$group_title_query = $db->prepare("
											SELECT kg_id, kg_title
											FROM idk_kandidati_grupe
											WHERE kg_id = :kg_id
											");
					
							$group_title_query->execute(array(
								":kg_id" => $kandidat_group
							));
					
							$row_kg_title = $group_title_query->fetch();
							$kandidat_prijava_na = $row_kg_title['kg_title'];
					
							if($_POST['kandidat_vozacka_dozvola'] == ""){
								$kandidat_vozacka_dozvola = "Ne";
							}else{
								$kandidat_vozacka_dozvola = $_POST['kandidat_vozacka_dozvola'];
							}
							
							//$kandidat_password = MD5($_POST['kandidat_password']);
							$kandidat_password = MD5($kandidat_korisnickoime);
							$kandidat_status = 0;
							$kandidat_datetime = date('Y-m-d H:i:s');
					
							$kandidat_check = md5(uniqid(rand(), true));
					
							//Upload and save kandidat_slika
							if($_FILES['kandidat_slika']['size'] !== 0) {
								$kandidat_slika = $_FILES['kandidat_slika'];
					
								//File properties
								$file_name = $kandidat_slika['name'];
								$file_tmp = $kandidat_slika['tmp_name'];
								$file_size = $kandidat_slika['size'];
								$file_error = $kandidat_slika['error'];
					
								//File extension
								$file_ext = explode('.', $file_name);
								$file_ext = strtolower(end($file_ext));
					
								$allowed = array('jpg', 'png');
					
								if(in_array($file_ext, $allowed)) {
					
									$kandidat_slika_final = uniqid() . '.' . $file_ext;
									$file_destination = 'files/kandidati/' . $kandidat_slika_final;
					
									if(move_uploaded_file($file_tmp, $file_destination)) {
					
										$path_to_image_directory = "files/kandidati/";
										$final_width_of_image = 660;
					
										if(preg_match('/[.](jpg)$/', $kandidat_slika_final)) {
											$im = imagecreatefromjpeg($path_to_image_directory . $kandidat_slika_final);
										} else if (preg_match('/[.](png)$/', $kandidat_slika_final)) {
											$im = imagecreatefrompng($path_to_image_directory . $kandidat_slika_final);
										}
					
										$ox = imagesx($im);
										$oy = imagesy($im);
										$nx = $final_width_of_image;
										$ny = floor($oy * ($final_width_of_image / $ox));
										$nm = imagecreatetruecolor($nx, $ny);
					
										imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);
										imagejpeg($nm, $path_to_image_directory . $kandidat_slika_final);
					
									}
								}
							}else{
								$kandidat_slika_final = "none";
							}
							
							//ADD PARTNER DATA
							
							if($partner_token != null){
								$partner_query = $db->prepare("
												SELECT jp_id, jp_fcmtoken, jp_position, jp_imeprezime
												FROM idk_jobstep_partners
												WHERE jp_mailconfirmation_token = :jp_mailconfirmation_token
												");
						
									$partner_query->execute(array(
										":jp_mailconfirmation_token" => $partner_token
								));
						
								$row_partner = $partner_query->fetch();
								$partner_id = $row_partner['jp_id'];
								$partner_position = $row_partner['jp_position'];
								$jp_fcmtoken = $row_partner['jp_fcmtoken'];
								$partner_ime = $row_partner['jp_imeprezime'];
								$log_poruka = "Novi kandidat ".$kandidat_ime." je prijavljen preko vašeg linka.";
								// send_notification_partnerapp($jp_fcmtoken, $partner_ime, $log_poruka);
							}else{
								$partner_id = null;
								$partner_position = null;
							}
					
							//Add user to db
							$query = $db->prepare("
											INSERT INTO idk_kandidati
												(kandidat_check, kandidat_ime, kandidat_prezime, kandidat_spol, kandidat_djevojackoprezime, kandidat_jmbg, kandidat_brojlk, kandidat_mjestorodjenja, kandidat_drzavarodjenja, kandidat_drzavljanstvo_vrsta, kandidat_drzavljanstvo, kandidat_adresa, kandidat_grad, kandidat_pbroj, kandidat_drzava, kandidat_email, kandidat_password, kandidat_slika, kandidat_status, kandidat_datetime, kandidat_korisnickoime, kandidat_datumrodjenja, kandidat_visitedurl, kandidat_vozacka_dozvola, kandidat_prijava_na, datum_termina, datum_aplikacije, kandidat_group, kandidat_procjenatermina, kandidat_partnerid, kandidat_partner_status)
											VALUES
												(:kandidat_check, :kandidat_ime, :kandidat_prezime, :kandidat_spol, :kandidat_djevojackoprezime, :kandidat_jmbg, :kandidat_brojlk, :kandidat_mjestorodjenja, :kandidat_drzavarodjenja, :kandidat_drzavljanstvo_vrsta, :kandidat_drzavljanstvo, :kandidat_adresa, :kandidat_grad, :kandidat_pbroj, :kandidat_drzava, :kandidat_email, :kandidat_password, :kandidat_slika, :kandidat_status, :kandidat_datetime, :kandidat_korisnickoime, :kandidat_datumrodjenja, :kandidat_visitedurl, :kandidat_vozacka_dozvola, :kandidat_prijava_na, :datum_termina, :datum_aplikacije, :kandidat_group, :kandidat_procjenatermina, :kandidat_partnerid, :kandidat_partner_status)");
					
							$query->execute(array(
										':kandidat_check' => $kandidat_check,
										':kandidat_ime' => $kandidat_ime,
										':kandidat_prezime' => $kandidat_prezime,
										':kandidat_spol' => $kandidat_spol,
										':kandidat_djevojackoprezime' => $kandidat_djevojackoprezime,
										':kandidat_jmbg' => $kandidat_jmbg,
										':kandidat_brojlk' => $kandidat_brojlk,
										':kandidat_mjestorodjenja' => $kandidat_mjestorodjenja,
										':kandidat_drzavarodjenja' => $kandidat_drzavarodjenja,
										':kandidat_drzavljanstvo_vrsta' => $kandidat_drzavljanstvo_vrsta,
										':kandidat_drzavljanstvo' => $kandidat_drzavljanstvo,
										':kandidat_adresa' => $kandidat_adresa,
										':kandidat_grad' => $kandidat_grad,
										':kandidat_pbroj' => $kandidat_pbroj,
										':kandidat_drzava' => $kandidat_drzava,
										':kandidat_email' => $kandidat_email,
										':kandidat_password' => $kandidat_password,
										':kandidat_slika' => $kandidat_slika_final,
										':kandidat_status' => $kandidat_status,
										':kandidat_datetime' => $kandidat_datetime,
										':kandidat_korisnickoime' => $kandidat_korisnickoime,
										':kandidat_datumrodjenja' => $kandidat_datumrodjenja,
										':kandidat_visitedurl' => $url_id,
										':kandidat_vozacka_dozvola' => $kandidat_vozacka_dozvola,
										':kandidat_prijava_na' => $kandidat_prijava_na,
										':datum_termina' => $kandidat_datum_termina,
										':datum_aplikacije' => $kandidat_datum_aplikacije,
										':kandidat_group' => $kandidat_group,
										':kandidat_procjenatermina' => $kandidat_procjenatermina,
										':kandidat_partnerid' => $partner_id,
										':kandidat_partner_status' => $partner_position
										));
					
										//print_r($query = $db->errorInfo());
										//exit();
										
										$kandidat_id = $db->lastInsertId();
										
										
										
										if($kandidat_drzavljanstvo_vrsta == "EU državljanin"){
											$kandidat_drzavljanstvo_vrsta_txt = "EU";
											
											//IF USER IS EU - Transfer him into EU kandidati project within the order
											$nalog_query = $db->prepare("
																	SELECT project_id
																	FROM idk_projects
																	WHERE project_nalogid = :project_nalogid AND project_eukandidati = 1");
										
											$nalog_query->execute(array(
															':project_nalogid' => $lg_nalogid));
										
											$nalogrow = $nalog_query->fetch();
											
											$project_id = $nalogrow['project_id'];
											
												$query_project = $db->prepare("
												INSERT INTO idk_project_kandidati
													(pk_projectid, pk_kandidatid)
												VALUES
													(:pk_projectid, :pk_kandidatid)");
					
												$query_project->execute(array(
															':pk_projectid' => $project_id,
															':pk_kandidatid' => $kandidat_id));						
											
											
										}else{
											$kandidat_drzavljanstvo_vrsta_txt = "NONEU";
											
											
											//Provjeri da li postoji mjesec termina unutar projekata
											$check_termin_month = $db->prepare("
																	SELECT project_id, project_name
																	FROM idk_projects
																	WHERE project_nalogid = :project_nalogid AND $mjesectermina BETWEEN MONTH(project_datumtermina) AND MONTH(project_datumterminado) AND YEAR(project_datumtermina) = $godinatermina");
										
											$check_termin_month->execute(array(
															':project_nalogid' => $lg_nalogid));
										
											$terminmonthcount = $check_termin_month->rowcount();						
											$terminmonth = $check_termin_month->fetch();	
											$project_id_termin = $terminmonth['project_id'];					
											$project_name = $terminmonth['project_name'];						
											
											if($terminmonthcount == 0){
												
												//IF USER IS NON EU - Transfer him into Prijave project within the order
												$nalog_query = $db->prepare("
																		SELECT project_id
																		FROM idk_projects
																		WHERE project_nalogid = :project_nalogid AND (project_name LIKE '%Prijave%' OR project_name LIKE '%Prijava%') AND project_nalogid != 0 ");
											
												$nalog_query->execute(array(
																':project_nalogid' => $lg_nalogid));
											
												$nalogrow = $nalog_query->fetch();
												
												$project_id = $nalogrow['project_id'];	
												
													$query_project = $db->prepare("
													INSERT INTO idk_project_kandidati
														(pk_projectid, pk_kandidatid)
													VALUES
														(:pk_projectid, :pk_kandidatid)");
						
													$query_project->execute(array(
																':pk_projectid' => $project_id,
																':pk_kandidatid' => $kandidat_id));	
												
											}else{
													$query_project = $db->prepare("
													INSERT INTO idk_project_kandidati
														(pk_projectid, pk_kandidatid)
													VALUES
														(:pk_projectid, :pk_kandidatid)");
						
													$query_project->execute(array(
																':pk_projectid' => $project_id_termin,
																':pk_kandidatid' => $kandidat_id));	
											}
											
											
											
										}	
										
					
										if($lg_nalogid == NULL or $lg_nalogid == 0){
											//Add in Projects
											if(!empty($_POST['lg_project'])){
						
												$projects = $_POST['lg_project'];
						
												foreach($projects as $project_id){
						
													$query_project = $db->prepare("
													INSERT INTO idk_project_kandidati
														(pk_projectid, pk_kandidatid)
													VALUES
														(:pk_projectid, :pk_kandidatid)");
						
													$query_project->execute(array(
																':pk_projectid' => $project_id,
																':pk_kandidatid' => $kandidat_id));
												}
						
											}
										}else{}
					
					
											$kki_grupa = 1;
											$kki_naziv = $_POST['kki_naziv'];
											$kki_podatak = $_POST['kki_podatak'];
					
											//Add kontakt info to db
											$query_mob = $db->prepare("
															INSERT INTO idk_kandidat_kontakt_info
																(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id)
															VALUES
																(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id)");
					
											$query_mob->execute(array(
														':kki_grupa' => $kki_grupa,
														':kki_naziv' => $kki_naziv,
														':kki_podatak' => $kki_podatak,
														':kki_kandidat_id' => $kandidat_id));
					
										if(isset($_POST['kandidat_email'])){
											$kki_grupa = 2;
											$kki_naziv = "E-mail";
											$kki_podatak = $_POST['kandidat_email'];
					
											//Add kontakt info to db
											$query_email = $db->prepare("
															INSERT INTO idk_kandidat_kontakt_info
																(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id)
															VALUES
																(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id)");
					
											$query_email->execute(array(
														':kki_grupa' => $kki_grupa,
														':kki_naziv' => $kki_naziv,
														':kki_podatak' => $kki_podatak,
														':kki_kandidat_id' => $kandidat_id));
										}
					
					
							//Add to Search
					
							$search_text = "Kandidat: " . $kandidat_ime . " " . $kandidat_prezime . " ";
							$search_tags = $kandidat_ime . " " . $kandidat_prezime . " " . $kandidat_grad . " " . $kandidat_drzavarodjenja . " " . $kandidat_spol;
							$search_link = "kandidati?page=open&id=$kandidat_id";
					
							$search_query = $db->prepare("
													INSERT INTO idk_search
														(search_text, search_tags, search_link)
													VALUES
														(:search_text, :search_tags, :search_link)");
					
							$search_query->execute(array(
												':search_text' => $search_text,
												':search_tags' => $search_tags,
												':search_link' => $search_link));
							
							//Add to logs candidate IP
							
							$date_time_ip = date("F j, Y, g:i T");
							if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
								$ip = $_SERVER['HTTP_CLIENT_IP'];
							} elseif (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
								$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
							} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
								$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
							} else {
								$ip = $_SERVER['REMOTE_ADDR'];
							}
							$log_desc = "Kandidat: " . $kandidat_ime . " " . $kandidat_prezime . ". ".$date_time_ip."; IP: ".$ip;
							$log_type = "5";
							addToLogs($log_desc, $log_type);
					
					
							header("Location: registracija/korak2/$kandidat_id/$kandidat_check");
					
						}else{
							header("Location: registracija/korak1/poruka/1");
						}
					}else{
						header("Location: registracija/korak1/poruka2/1");
						
					}
				
				break;	
				
				case "add_kandidat_new":
					/*  // Build POST request:
					$recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
					$recaptcha_secret = '6LfSU_cUAAAAAAZolZBCR5u5zfIZfl2roL3irH8_';
					$recaptcha_response = $_POST['recaptcha_response'];
					// Make and decode POST request:
					$recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
					$recaptcha = json_decode($recaptcha);
					
					// Take action based on the score returned:
					if ($recaptcha->score >= 0.4) { 
						*/

					//OSNOVNE INFORMACIJE
						$kandidat_ime = trim($_POST['kandidat_ime']);
						$kandidat_prezime = trim($_POST['kandidat_prezime']);
						$urlid = $_POST['urlid'];
						$lg_language = $_POST['lg_language'];
						$dan_rodjenja = $_POST["datum_dan"];
						$mjesec_rodjenja = $_POST["datum_mjesec"];
						$godina_rodjenja = $_POST["datum_godina"];
						$mobile_phone = $_POST['kki_phone'];
						$partner_token = $_POST['token'];
						
						if(isset($_REQUEST['kandidat_email'])){
							$kandidat_email = $_REQUEST['kandidat_email'];
						}else{
							$kandidat_email = null;
						}
						
						$kandidat_full_name = $kandidat_ime." ".$kandidat_prezime;
						
						$kandidat_datumrodjenja = date('Y-m-d', strtotime($godina_rodjenja."-".$mjesec_rodjenja."-".$dan_rodjenja));
					//OSNOVNE INFROMACIJE
						
					//ZASTARJELO
						// if($urlid == 1000){
							// $kj_znanje_njemacki = $_POST['nivo_jezika'];
							// var_dump($kj_znanje_njemacki);
							// exit();
						// }
					//ZASTARJELO

					//DRŽAVLJANSTVO VRSTA
					if(isset($_POST['kandidat_drzavljanstvo_vrsta'])){
						$kandidat_drzavljanstvo_vrsta = $_POST['kandidat_drzavljanstvo_vrsta'];
					}else{
						$kandidat_drzavljanstvo_vrsta = null;
					}
					if($kandidat_drzavljanstvo_vrsta == "EU državljanin"){
						$eu_drzava = "NN";
					}else{
						$eu_drzava = "NE";
					}
					//DRŽAVLJANSTVO VRSTA
					
					// ODGOVARAJUĆE VRIJEME POZIVA
						if(isset($_POST['vrijeme_poziva'])){
							$vrijeme_poziva = $_POST['vrijeme_poziva'];
						}else{
							$vrijeme_poziva = null;
						}
					// ODGOVARAJUĆE VRIJEME POZIVA
					
					// ENGLESKI JEZIK MIN B2 DA/NE
						if(isset($_POST['engleski_min_b2'])){
							$engleski_b2_da_ne = $_POST['engleski_min_b2'];
						}else{
							$engleski_b2_da_ne = null;
						}
					// ENGLESKI JEZIK MIN B2 DA/NE
						
					//INCLUDE JEZIK
						if($lg_language == "de")
							include("lang/de.php");
						else
							include("lang/bs.php");
					//INCULUDE JEZIK
						
					//Postavljanje defulatnog urlid = 1 kad nema linka i uzimanje id naloga
						$check_query2 = $db->prepare("
												SELECT lg_id, lg_url, lg_nalogid
												FROM idk_link_generator
												WHERE lg_id = :lg_id");
					
						$check_query2->execute(array(
										':lg_id' => $urlid));
					
						$num = $check_query2->rowCount();
						$rowlg = $check_query2->fetch();
							
						$lg_nalogid = $rowlg['lg_nalogid'];
						
						if($num > 0){
							$url_id = $_POST['urlid'];
						}else{
							$url_id = 1;
						}
					//Postavljanje defulatnog urlid = 1 kad nema linka i uzimanje id naloga
						
					//napravi username po imenu, prezimenu i godini
						$ime_korime = strtolower($kandidat_ime);
						$prezime_korime = strtolower($kandidat_prezime);
						$imeprezime = $ime_korime.''.$prezime_korime;
						$search = array("ć", "č", "ž", "š", "đ");
						$replacement = array("c", "c", "z", "s", "dj");
						$imeprezime_korime = str_replace($search, $replacement, $imeprezime);
					//napravi username po imenu, prezimenu i godini
					
					//ISKUSTVO U STRUCI	
						$iskustvo_u_struci_note = null;
						if(isset($_POST['kandidat_iskustvo_u_struci'])){
							$kandidat_iskustvo_u_struci = $_POST['kandidat_iskustvo_u_struci'];
							if($kandidat_iskustvo_u_struci == 1){
								$kandidat_iskustvo_u_struci_trajanje = $_POST['iskustvo_u_struci_trajanje'] ?? null;
								
							}else{
								$kandidat_iskustvo_u_struci_trajanje = null;
							}
							
							// if(isset($_POST['radnoIskustvoBiljeska'])){
							// 	$iskustvo_u_struci_note = "Radno iskustvo u automehanici: " . ($kandidat_iskustvo_u_struci == 1 ? "DA" : "NE") . ".";
							// }
						}
						else{
							$kandidat_iskustvo_u_struci = null;
							$kandidat_iskustvo_u_struci_trajanje = null;
						}
					//ISKUSTVO U STRUCI

					//VIZA I TERMIN
						if(isset($_POST['kandidat_viza']))
							$kandidat_viza_da_ne = $_POST['kandidat_viza'];
						else
							$kandidat_viza_da_ne = "";
						if(isset($_POST['kandidat_termin']))
							$kandidat_termin_da_ne = $_POST['kandidat_termin'];
						else
							$kandidat_termin_da_ne = "";
						if(isset($_POST['kandidat_apliciranje']))
							$kandidat_apliciranje_da_ne = $_POST['kandidat_apliciranje'];
						else
							$kandidat_apliciranje_da_ne = "";
					
						if($kandidat_viza_da_ne == 1){
							$kandidat_viza = 1;
							$kandidat_viza_vrijedi_do = $_POST['kandidat_viza_vrijedi_do'];
							$kandidat_viza_vrijedi_do = date("Y-m-d", strtotime($kandidat_viza_vrijedi_do));
							$kandidat_datum_termina = null;
							$kandidat_datum_aplikacije = null;
							$kandidat_procjenatermina = 0;
						}else{
							$kandidat_viza = 0;
							$kandidat_viza_vrijedi_do = null;
							if($kandidat_termin_da_ne == 1){
								$kandidat_datum_termina = $_POST['kandidat_termin_date'];
								$kandidat_datum_termina = date("Y-m-d", strtotime($kandidat_datum_termina));
								$kandidat_datum_aplikacije = null;
								$kandidat_procjenatermina = 0;
							}else{
								if($kandidat_apliciranje_da_ne == 1){
									$kandidat_datum_aplikacije =  $_POST['kandidat_termin_date_app'];
									$kandidat_datum_aplikacije = date("Y-m-d", strtotime($kandidat_datum_aplikacije));
									$kandidat_procjenatermina = 1;
									$kandidat_datum_termina = date('Y-m-d', strtotime("+20 months", strtotime($kandidat_datum_aplikacije)));
								}else{
									$kandidat_procjenatermina = 0;
									$kandidat_datum_termina = null;
									$kandidat_datum_aplikacije = null;
								}
							}
						}
					//VIZA I TERMIN
						
					//SPECIFIČNE INFORMACIJE ZA ODREĐENE OGLASE
						if(isset($_POST['termin_carglass'])){
							$termin_za_vizu_carglass = $_POST['termin_carglass'];
						}else{
							$termin_za_vizu_carglass = null;
						}
						if(isset($_POST['grad_za_razgovor'])){
							$grad_za_razgovor = $_POST['grad_za_razgovor'];
						}else{
							$grad_za_razgovor = null;
						}
						if(isset($_POST['atu_radno_iskustvo'])){
							$atu_radno_iskustvo = $_POST['atu_radno_iskustvo'];
						}else{
							$atu_radno_iskustvo = null;
						}

						if(isset($_POST['pokrajina_rada'])){
							$pokrajina_rada = $_POST['pokrajina_rada'];
						}else{
							$pokrajina_rada = null;
						}
					//SPECIFIČNE INFORMACIJE ZA ODREĐENE OGLASE

					//VOZACKA I KATEGORIJE VOZACKE
						if(isset($_POST['kandidat_vozacka_dozvola'])){
							$kandidat_vozacka_dozvola = $_POST['kandidat_vozacka_dozvola'];
							if($kandidat_vozacka_dozvola == "Da"){
								$kandidat_vozacka_kategorija = "B";
							}else{
								$kandidat_vozacka_kategorija = null;
							}
						}else{
							if(isset($_POST['kandidat_kategorija_vozacke'])){
								if($_POST['kandidat_kategorija_vozacke'] == "Ne"){
									$kandidat_vozacka_dozvola = "Ne";
									$kandidat_vozacka_kategorija = null;	
								}else{
									$kandidat_vozacka_dozvola = "Da";
									$kandidat_vozacka_kategorija = $_POST['kandidat_kategorija_vozacke'];
								}
							}else{
								$kandidat_vozacka_dozvola = null;
								$kandidat_vozacka_kategorija = null;
							}
						}
					//VOZACKA I KATEGORIJE VOZACKE

					//SKOLE - preradjeno 20.10.2025

						$skola_id_post = $_POST['skola_naziv'] ?? null;
						$smjer_id_post = $_POST['smjer_naziv'] ?? null;
						$rucni_unos = ($skola_id_post === 'ostalo') || ($smjer_id_post === 'ostalo');

						// Normalize empty/zero to null, and cast real IDs to int
						$skola_id_post = ($skola_id_post === '' || $skola_id_post === '0') ? null : $skola_id_post;
						$smjer_id_post = ($smjer_id_post === '' || $smjer_id_post === '0') ? null : $smjer_id_post;

						if($rucni_unos){
							// Rucni unos
							$smjer_naziv = $_POST["smjer_naziv_ru"] ?? null;
							if($smjer_naziv != null){
								//provjeri da li smjer vec postoji
								$query_smjer_naziv = $db->prepare("SELECT ss_id, skola_id, ss_naziv, skola_naziv FROM idk_skole_smjerovi JOIN idk_skole 
																ON idk_skole_smjerovi.ss_skola_id = idk_skole.skola_id WHERE ss_naziv = :smjer_naziv " 
								);
								$query_smjer_naziv->execute(array(":smjer_naziv" => $smjer_naziv));
								$count = $query_smjer_naziv->rowCount();
								$row_smjer_naziv = $query_smjer_naziv->fetch();
								if($count > 0){
									$smjer_id_post = $row_smjer_naziv['ss_id'];
									$smjer_naziv = $row_smjer_naziv['ss_naziv'];
									$skola_id_post = $row_smjer_naziv['skola_id'];
									$skola_naziv = $row_smjer_naziv['skola_naziv'];
								} else {
									$smjer_id_post = null;
									$smjer_naziv = $_POST["smjer_naziv_ru"];
									$skola_id_post = null;
									$skola_naziv = $_POST["skola_naziv_ru"] ?? null;
								}
							}else{
								$skola_naziv = null;
								$smjer_naziv = null;
							}
						}else{
							// cast real IDs to int
							$skola_id_post = is_null($skola_id_post) ? null : (int)$skola_id_post;
							$smjer_id_post = is_null($smjer_id_post) ? null : (int)$smjer_id_post;
							if($smjer_id_post === null){
								$skola_naziv = null;
								$smjer_naziv = null;
							}else{
								if($skola_id_post === null){
									//SAMO SMJER IMAMO - Input 1
									$query_skola_naziv_id = $db->prepare(" SELECT skola_id, skola_naziv, ss_naziv FROM idk_skole 
											JOIN idk_skole_smjerovi ON ss_skola_id = skola_id WHERE ss_id = :smjer_id_post "
									);
									$query_skola_naziv_id->execute(array(":smjer_id_post" => $smjer_id_post));
									$row_skola_naziv_id = $query_skola_naziv_id->fetch();
									$skola_naziv = $row_skola_naziv_id['skola_naziv'];
									$skola_id_post = $row_skola_naziv_id['skola_id'];
									$smjer_naziv = $row_skola_naziv_id['ss_naziv'];
								}else{
									//IMAMO OBOJE - input 2
									$query_skola_naziv = $db->prepare("SELECT skola_naziv FROM idk_skole WHERE skola_id = :skola_id_post");
									$query_skola_naziv->execute(array(":skola_id_post" => $skola_id_post));
									$row_skola_naziv = $query_skola_naziv->fetch();
									$skola_naziv = $row_skola_naziv['skola_naziv'];
									
									$query_smjer_naziv = $db->prepare("SELECT ss_naziv FROM idk_skole_smjerovi WHERE ss_id = :smjer_id_post");
									$query_smjer_naziv->execute(array(":smjer_id_post" => $smjer_id_post));
									$row_smjer_naziv = $query_smjer_naziv->fetch();
									$smjer_naziv = $row_smjer_naziv['ss_naziv'];


								}
							}
						}
					
					//SKOLE - preradjeno 20.10.2025
					
						
					// Provjera da li postoji korisnik sa istim imenom i prezimenom u zadnjih sat vremena
						$granicno_vrijeme = date( "Y-m-d H:i:s",  strtotime("- 1 hour" ));
						$check_user = $db->prepare("
												SELECT kandidat_ime, kandidat_slika, kandidat_id, kandidat_status_messenger, kandidat_check, kandidat_visitedurl, kandidat_status, kandidat_vozacka_kategorija
												FROM idk_kandidati
												WHERE TRIM(kandidat_ime) = :kandidat_ime AND TRIM(kandidat_prezime) = :kandidat_prezime AND (kandidat_mobitel = :kandidat_mobitel OR kandidat_datumrodjenja = :kandidat_datumrodjenja) AND kandidat_status != 3");
					
						$check_user->execute(array(
										':kandidat_ime' => $kandidat_ime,
										':kandidat_prezime' => $kandidat_prezime,
										':kandidat_mobitel' => $mobile_phone,
										':kandidat_datumrodjenja' => $kandidat_datumrodjenja
										));
					
						$number_of_rows_user = $check_user->rowCount();

						
						$rows_user = $check_user->fetch();
						$check_slika = $rows_user['kandidat_slika'];
						$kandidat_id_c = $rows_user['kandidat_id'];
						$kandidat_status_messenger = $rows_user['kandidat_status_messenger'];
						$kandidat_check_exists = $rows_user['kandidat_check'];
						$stari_visitedurl = $rows_user['kandidat_visitedurl'];
						$kandidat_status = $rows_user['kandidat_status'];
						
						$kandidat_group = $_POST['kandidat_prijava_na'];
						$group_title_query = $db->prepare("
										SELECT kg_id, kg_title
										FROM idk_kandidati_grupe
										WHERE kg_id = :kg_id
										");
				
						$group_title_query->execute(array(
							":kg_id" => $kandidat_group
						));
						$row_kg_title = $group_title_query->fetch();
						$kandidat_prijava_na = $row_kg_title['kg_title'];
						
						$kandidat_datetime = date('Y-m-d H:i:s');
						$kandidat_check = md5(uniqid(rand(), true));
					// Provjera da li postoji korisnik sa istim imenom i prezimenom u zadnjih sat vremena

					//ISKUSTVO U STRUCI	
						$iskustvo_u_struci_note = null;
						if(isset($_POST['kandidat_iskustvo_u_struci'])){
							$kandidat_iskustvo_u_struci = $_POST['kandidat_iskustvo_u_struci'];
							if($kandidat_iskustvo_u_struci == 1){
								$kandidat_iskustvo_u_struci_trajanje = $_POST['iskustvo_u_struci_trajanje'] ?? null;
								
							}else{
								$kandidat_iskustvo_u_struci_trajanje = null;
							}
							
							if(isset($_POST['radnoIskustvoBiljeska'])){
								$iskustvo_u_struci_note = "Radno iskustvo u struci (" . $kandidat_prijava_na . "): " . ($kandidat_iskustvo_u_struci == 1 ? "DA" : "NE") . ".";
							}
						}
						else{
							$kandidat_iskustvo_u_struci = null;
							$kandidat_iskustvo_u_struci_trajanje = null;
						}
					//ISKUSTVO U STRUCI
						
						//NOVA PRIJAVA
						if($number_of_rows_user == 0){
							
							// nisu ulazile duple prijave zbog korisnickog imena koji je kombinacija imenaprezimenadatumarodjenja, pa sam dodao
							//jos jedan token od 3 broja na to da moze ulaziti
							$random_token = rand(100, 999);
							$kandidat_korisnickoime_uf = $imeprezime_korime.''.$random_token;
							$kandidat_korisnickoime = str_replace(' ', '', strtolower($kandidat_korisnickoime_uf));
							$kandidat_password = MD5($kandidat_korisnickoime);
							
							//izbaceno provjeravanje da li ima kandidata sa ovakvim $kandidat_korisnickoime
							
							// $kandidat_mjestorodjenja = $_POST['kandidat_mjestorodjenja'];
							// $kandidat_drzavarodjenja = $_POST['kandidat_drzavarodjenja'];
							$kandidat_visitedurl = $_POST['kandidat_visitedurl'];
							
							/*
							$kandidat_datum_termina = $_POST['kandidat_termin_date'];
							$kandidat_datum_aplikacije = $_POST['kandidat_termin_date_app'];
							$datum_aplikacije = date("Y-m-d", strtotime($kandidat_datum_aplikacije));
							*/
							
							$kandidat_status = 0;
							
							if($lg_language == "bs" or $lg_language == "de" or $lg_language == "rs" or $lg_language == "sr")
								$kandidat_status_messenger = 1;
							else
								$kandidat_status_messenger = 4;
						
							//Upload and save kandidat_slika
							/*
							if($_FILES['kandidat_slika'] !== null){
								if($_FILES['kandidat_slika']['size'] !== 0) {
									$kandidat_slika = $_FILES['kandidat_slika'];
						
									//File properties
									$file_name = $kandidat_slika['name'];
									$file_tmp = $kandidat_slika['tmp_name'];
									$file_size = $kandidat_slika['size'];
									$file_error = $kandidat_slika['error'];
						
									//File extension
									$file_ext = explode('.', $file_name);
									$file_ext = strtolower(end($file_ext));
						
									$allowed = array('jpg', 'png');
						
									if(in_array($file_ext, $allowed)) {
						
										$kandidat_slika_final = uniqid() . '.' . $file_ext;
										$file_destination = 'files/kandidati/' . $kandidat_slika_final;
						
										if(move_uploaded_file($file_tmp, $file_destination)) {
						
											$path_to_image_directory = "files/kandidati/";
											$final_width_of_image = 660;
											ini_set('memory_limit', '-1');
											if(preg_match('/[.](jpg)$/', $kandidat_slika_final)) {
												$im = imagecreatefromjpeg($path_to_image_directory . $kandidat_slika_final);
											} else if (preg_match('/[.](png)$/', $kandidat_slika_final)) {
												$im = imagecreatefrompng($path_to_image_directory . $kandidat_slika_final);
											}
						
											$ox = imagesx($im);
											$oy = imagesy($im);
											$nx = $final_width_of_image;
											$ny = floor($oy * ($final_width_of_image / $ox));
											$nm = imagecreatetruecolor($nx, $ny);
						
											imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);
											imagejpeg($nm, $path_to_image_directory . $kandidat_slika_final);
						
										}
									}else{
										$kandidat_slika_final = "none";
									}
								}else{
									$kandidat_slika_final = "none";
								}
							}else*/
								$kandidat_slika_final = "none";
							
							//ADD PARTNER DATA
								if($partner_token != null){
									$partner_query = $db->prepare("
													SELECT jp_id, jp_fcmtoken, jp_position, jp_imeprezime, jp_user_type
													FROM idk_jobstep_partners
													WHERE jp_mailconfirmation_token = :jp_mailconfirmation_token
													");
							
										$partner_query->execute(array(
											":jp_mailconfirmation_token" => $partner_token
									));
							
									$row_partner = $partner_query->fetch();
									$partner_id = $row_partner['jp_id'];
									$partner_position = $row_partner['jp_position'];
									$jp_fcmtoken = $row_partner['jp_fcmtoken'];
									$partner_ime = $row_partner['jp_imeprezime'];
									$jp_user_type = $row_partner['jp_user_type'];
									$log_poruka = "Novi kandidat ".$kandidat_ime." je prijavljen preko vašeg linka.";
									if ($jp_user_type == 1) {
										$kandidat_porijeklo = 8;
									} else {
										// send_notification_partnerapp($jp_fcmtoken, $partner_ime, $log_poruka);
										$kandidat_porijeklo = 6;
									}
								}else{
									$partner_id = null;
									$partner_position = null;
									$kandidat_porijeklo = 0;
								}
							//ADD PARTNER DATA
							
							//Add user to db
								$query = $db->prepare("
												INSERT INTO idk_kandidati
													( kandidat_check, kandidat_ime, kandidat_prezime, kandidat_drzavljanstvo_vrsta, kandidat_mobitel, kandidat_password, kandidat_slika, kandidat_status, kandidat_status_messenger, kandidat_datetime, kandidat_korisnickoime, kandidat_datumrodjenja, kandidat_visitedurl, kandidat_prijava_na, datum_termina, datum_aplikacije, kandidat_group, kandidat_procjenatermina, kandidat_viza, kandidat_viza_vrijedi_do, kandidat_status_prijave, kandidat_partnerid, kandidat_partner_status, kandidat_porijeklo, kandidat_iskustvo_u_struci, kandidat_iskustvo_u_struci_trajanje, kandidat_termin_za_vizu, kandidat_vozacka_dozvola, kandidat_vozacka_kategorija)
												VALUES
													(:kandidat_check,:kandidat_ime,:kandidat_prezime,:kandidat_drzavljanstvo_vrsta,:kandidat_mobitel, :kandidat_password, :kandidat_slika, :kandidat_status, :kandidat_status_messenger, :kandidat_datetime, :kandidat_korisnickoime, :kandidat_datumrodjenja, :kandidat_visitedurl, :kandidat_prijava_na, :datum_termina, :datum_aplikacije, :kandidat_group, :kandidat_procjenatermina, :kandidat_viza, :kandidat_viza_vrijedi_do, :kandidat_status_prijave, :kandidat_partnerid, :kandidat_partner_status, :kandidat_porijeklo, :kandidat_iskustvo_u_struci, :kandidat_iskustvo_u_struci_trajanje, :kandidat_termin_za_vizu, :kandidat_vozacka_dozvola, :kandidat_vozacka_kategorija)");
						
								$query->execute(array(
											':kandidat_check' => $kandidat_check,
											':kandidat_ime' => $kandidat_ime,
											':kandidat_prezime' => $kandidat_prezime,
											':kandidat_drzavljanstvo_vrsta' => $kandidat_drzavljanstvo_vrsta,
											':kandidat_mobitel' => $mobile_phone,
											':kandidat_password' => $kandidat_password,
											':kandidat_slika' => $kandidat_slika_final,
											':kandidat_status' => $kandidat_status,
											':kandidat_status_messenger' => $kandidat_status_messenger,
											':kandidat_datetime' => $kandidat_datetime,
											':kandidat_korisnickoime' => $kandidat_korisnickoime,
											':kandidat_datumrodjenja' => $kandidat_datumrodjenja,
											':kandidat_visitedurl' => $url_id,
											':kandidat_prijava_na' => $kandidat_prijava_na,
											':datum_termina' => $kandidat_datum_termina,
											':datum_aplikacije' => $kandidat_datum_aplikacije,
											':kandidat_group' => $kandidat_group,
											':kandidat_procjenatermina' => $kandidat_procjenatermina,
											':kandidat_viza' => $kandidat_viza,
											':kandidat_viza_vrijedi_do' => $kandidat_viza_vrijedi_do,
											':kandidat_status_prijave' => 2,
											':kandidat_partnerid' => $partner_id,
											':kandidat_partner_status' => $partner_position,
											':kandidat_porijeklo' => $kandidat_porijeklo,
											':kandidat_iskustvo_u_struci' => $kandidat_iskustvo_u_struci,
											':kandidat_iskustvo_u_struci_trajanje' => $kandidat_iskustvo_u_struci_trajanje,
											':kandidat_termin_za_vizu' => $termin_za_vizu_carglass,
											':kandidat_vozacka_dozvola' => $kandidat_vozacka_dozvola,
											':kandidat_vozacka_kategorija' => $kandidat_vozacka_kategorija
											));
										
								$kandidat_id = $db->lastInsertId();
							//Add user to db
							
							// INSERT LOG STATUSA
								$query_log_status = $db->prepare("
										INSERT INTO idk_log_kandidat_statusi
											(lks_kandidat_id, lks_status_obrade, lks_status_messenger, lks_datetime, lks_link_id)
										VALUES
											(:lks_kandidat_id, :lks_status_obrade, :lks_status_messenger, :lks_datetime, :lks_link_id)
								");
								
								$query_log_status->execute(array(
										':lks_kandidat_id' => $kandidat_id,
										':lks_status_obrade' => $kandidat_status,
										':lks_status_messenger' => $kandidat_status_messenger,
										':lks_datetime' => $kandidat_datetime,
										':lks_link_id' => $url_id
								));
							// INSERT LOG STATUSA
							
							// DODATNE BILJESKE
							$userdp = $_POST['userdp'] ?? [];   // array dp_id => 0/1
							$dp_ids = array_keys($userdp);
							$dp_ids = array_map('intval', $dp_ids);
							$dp_ids = array_values(array_filter($dp_ids));

							if (!empty($dp_ids)) {
								$placeholders = implode(',', array_fill(0, count($dp_ids), '?'));

								$stmt = $db->prepare("
									SELECT dp_id, dp_tekst
									FROM idk_dodatna_pitanja
									WHERE dp_id IN ($placeholders)
								");
								$stmt->execute($dp_ids);

								$dp_texts = [];
								foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
									$dp_texts[(int)$r['dp_id']] = $r['dp_tekst'];
								}

								// 3) Insert notes
								$note_group    = 2;
								$note_dataid   = (int)$kandidat_id;
								$note_datetime = date('Y-m-d H:i:s');
								$note_employeeid = 139;

								$insNote = $db->prepare("
									INSERT INTO idk_notes (note_txt, note_datetime, note_group, note_dataid, note_employeeid)
									VALUES (:note_txt, :note_datetime, :note_group, :note_dataid, :note_employeeid)
								");

								foreach ($userdp as $dp_id => $yesno) {
									$dp_id = (int)$dp_id;
									if (!isset($dp_texts[$dp_id])) continue; // skip unknown ids

									$answer = ((int)$yesno === 1) ? 'DA' : 'NE';
									$note_txt = trim($dp_texts[$dp_id]) . ': ' . $answer;

									$insNote->execute([
										':note_txt'       => $note_txt,
										':note_datetime'  => $note_datetime,
										':note_group'     => $note_group,
										':note_dataid'    => $note_dataid,
										':note_employeeid'=> $note_employeeid
									]);
								}
							}

							// BILJEŠKA ZA RADNO ISKUSTVO - SAMO DA/NE
							if($iskustvo_u_struci_note != null){
								$note_group = 2;
								$note_dataid = $kandidat_id;
								$note_datetime = date('Y-m-d H:i:s');
								
								$query_biljeske = $db->prepare("
												INSERT INTO idk_notes
													(note_txt, note_datetime, note_group, note_dataid, note_employeeid)
												VALUES
													(:note_txt, :note_datetime, :note_group, :note_dataid, :note_employeeid)");
			
								$query_biljeske->execute(array(
											':note_txt' => $iskustvo_u_struci_note,
											':note_datetime' => $note_datetime,
											':note_group' => $note_group,
											':note_dataid' => $note_dataid,
											':note_employeeid' => 139));
							}
							// BILJEŠKA ZA RADNO ISKUSTVO - SAMO DA/NE

							// NOVA BILJEŠKA 17.07.2025 ZA VRIJEME POZIVA
								if($vrijeme_poziva != null){
									$note_group = 2;
									$note_dataid = $kandidat_id;
									$note_datetime = date('Y-m-d H:i:s');
									
									$note_txt_cert = "Željeno vrijeme poziva: ".$vrijeme_poziva;
									
									$query_biljeske = $db->prepare("
													INSERT INTO idk_notes
														(note_txt, note_datetime, note_group, note_dataid, note_employeeid)
													VALUES
														(:note_txt, :note_datetime, :note_group, :note_dataid, :note_employeeid)");
				
									$query_biljeske->execute(array(
												':note_txt' => $note_txt_cert,
												':note_datetime' => $note_datetime,
												':note_group' => $note_group,
												':note_dataid' => $note_dataid,
												':note_employeeid' => 139));
								}
							// NOVA BILJEŠKA 17.07.2025 ZA VRIJEME POZIVA

							// BILJEŠKA 15.12.2025 ZA ENGLESKI JEZIK
							if($engleski_b2_da_ne != null){
								$note_group = 2;
								$note_dataid = $kandidat_id;
								$note_datetime = date('Y-m-d H:i:s');
								
								$note_txt_cert = "Poznajete li engleski jezik minimalno B2: ".$engleski_b2_da_ne .".";
								
								$query_biljeske = $db->prepare("
												INSERT INTO idk_notes
													(note_txt, note_datetime, note_group, note_dataid, note_employeeid)
												VALUES
													(:note_txt, :note_datetime, :note_group, :note_dataid, :note_employeeid)");
			
								$query_biljeske->execute(array(
											':note_txt' => $note_txt_cert,
											':note_datetime' => $note_datetime,
											':note_group' => $note_group,
											':note_dataid' => $note_dataid,
											':note_employeeid' => 139));
							}
							// BILJEŠKA 15.12.2025 ZA ENGLESKI JEZIK

							//SPECIFIČNI INFO ZA ODREĐENE FORME - BILJEŠKE
								if($grad_za_razgovor != null){
									$note_group = 2;
									$note_dataid = $kandidat_id;
									$note_datetime = date('Y-m-d H:i:s');
									
									$note_txt_cert = "Izabrani grad za razgovor: ".$grad_za_razgovor;
									
									$query_biljeske = $db->prepare("
													INSERT INTO idk_notes
														(note_txt, note_datetime, note_group, note_dataid, note_employeeid)
													VALUES
														(:note_txt, :note_datetime, :note_group, :note_dataid, :note_employeeid)");
				
									$query_biljeske->execute(array(
												':note_txt' => $note_txt_cert,
												':note_datetime' => $note_datetime,
												':note_group' => $note_group,
												':note_dataid' => $note_dataid,
												':note_employeeid' => 67));
								}
								if($atu_radno_iskustvo != null OR $pokrajina_rada != null){
									$note_group = 2;
									$note_dataid = $kandidat_id;
									$note_datetime = date('Y-m-d H:i:s');
									$note_txt_cert = "";
									if ($atu_radno_iskustvo != null) {
										if($atu_radno_iskustvo == 1){ 
											$atu_radno_iskustvo_txt = "Da";
										}elseif($atu_radno_iskustvo == 0){
											$atu_radno_iskustvo_txt = "Ne";
										}else{
											$atu_radno_iskustvo_txt = "Undefined";
										}
										$note_txt_cert = $note_txt_cert . " Da li imate radnog iskustva kao automehanicar: ".$atu_radno_iskustvo_txt.";";
									}
									if($pokrajina_rada != null){
										$note_txt_cert = $note_txt_cert . " U kojoj pokrajini zelite raditi: ".$pokrajina_rada.";";
									}
									$query_biljeske = $db->prepare("
													INSERT INTO idk_notes
														(note_txt, note_datetime, note_group, note_dataid, note_employeeid)
													VALUES
														(:note_txt, :note_datetime, :note_group, :note_dataid, :note_employeeid)");
				
									$query_biljeske->execute(array(
												':note_txt' => $note_txt_cert,
												':note_datetime' => $note_datetime,
												':note_group' => $note_group,
												':note_dataid' => $note_dataid,
												':note_employeeid' => 67));
								}
							//SPECIFIČNI INFO ZA ODREĐENE FORME - BILJEŠKE

							//INSERT JEZIKA
								if(isset($_POST['nivo_jezika'])){
									
									$kj_naziv_njemacki = "Njemački";
									$kj_znanje_njemacki = $_POST['nivo_jezika'];
									// Add language knowlege
									$query_njem = $db->prepare("
													INSERT INTO idk_kandidat_jezici
														(kj_naziv, kj_slusanje, kj_citanje, kj_govorna_interakcija, kj_govorna_produkcija, kj_pisanje, kj_kandidatid)
													VALUES
														(:kj_naziv, :kj_slusanje, :kj_citanje, :kj_govorna_interakcija, :kj_govorna_produkcija, :kj_pisanje, :kj_kandidatid)");
							
									$query_njem->execute(array(
												':kj_naziv' => $kj_naziv_njemacki,
												':kj_slusanje' => $kj_znanje_njemacki,
												':kj_citanje' => $kj_znanje_njemacki,
												':kj_govorna_interakcija' => $kj_znanje_njemacki,
												':kj_govorna_produkcija' => $kj_znanje_njemacki,
												':kj_pisanje' => $kj_znanje_njemacki,
												':kj_kandidatid' => $kandidat_id));
								}
								if(isset($_POST['nivo_engleskog_jezika'])){
									
									$kj_naziv_engleski = "Engleski";
									$kj_znanje_engleski = $_POST['nivo_engleskog_jezika'];
									// Add language knowlege
									$query_njem = $db->prepare("
													INSERT INTO idk_kandidat_jezici
														(kj_naziv, kj_slusanje, kj_citanje, kj_govorna_interakcija, kj_govorna_produkcija, kj_pisanje, kj_kandidatid)
													VALUES
														(:kj_naziv, :kj_slusanje, :kj_citanje, :kj_govorna_interakcija, :kj_govorna_produkcija, :kj_pisanje, :kj_kandidatid)");
							
									$query_njem->execute(array(
												':kj_naziv' => $kj_naziv_engleski,
												':kj_slusanje' => $kj_znanje_engleski,
												':kj_citanje' => $kj_znanje_engleski,
												':kj_govorna_interakcija' => $kj_znanje_engleski,
												':kj_govorna_produkcija' => $kj_znanje_engleski,
												':kj_pisanje' => $kj_znanje_engleski,
												':kj_kandidatid' => $kandidat_id));
								}
							//INSERT JEZIKA

							//SPECIFIČNI INSERT PROJEKTA ZA LINK 446
								if($urlid == 446){
									
									$get_project = $db->prepare("
															SELECT project_id
															FROM idk_projects
															WHERE project_nalogid = 117 AND (project_name LIKE '%$kandidat_prijava_na%') ");
								
									$get_project->execute();
								
									$gp_row = $get_project->fetch();
									$projectid = $gp_row['project_id'];
									
									$check_project = $db->prepare("
															SELECT pk_projectid
															FROM idk_project_kandidati
															WHERE pk_projectid = :pk_projectid AND pk_kandidatid = :pk_kandidatid");
								
									$check_project->execute(array(
														':pk_projectid' => $projectid,
														':pk_kandidatid' => $kandidat_id
									));
									if($check_project->rowCount() == 0){
										
										$query_project = $db->prepare("
														INSERT INTO idk_project_kandidati
															(pk_projectid, pk_kandidatid)
														VALUES
															(:pk_projectid, :pk_kandidatid)");
							
										$query_project->execute(array(
														':pk_projectid' => $projectid,
														':pk_kandidatid' => $kandidat_id));
									}
								}
							//SPECIFIČNI INSERT PROJEKTA ZA LINK 446

							//VEZANJE ZA PROJEKT
								if($lg_nalogid != 0){
									if($kandidat_drzavljanstvo_vrsta == "EU državljanin"){
										$kandidat_drzavljanstvo_vrsta_txt = "EU";
										
										//IF USER IS EU - Transfer him into EU kandidati project within the order
										$nalog_query = $db->prepare("
																SELECT project_id
																FROM idk_projects
																WHERE project_nalogid = :project_nalogid AND project_eukandidati = 1");
									
										$nalog_query->execute(array(
														':project_nalogid' => $lg_nalogid));
									
										$nalogrow = $nalog_query->fetch();
										
										$project_id = $nalogrow['project_id'];
										
										$query_project = $db->prepare("
														INSERT INTO idk_project_kandidati
															(pk_projectid, pk_kandidatid)
														VALUES
															(:pk_projectid, :pk_kandidatid)");

										$query_project->execute(array(
														':pk_projectid' => $project_id,
														':pk_kandidatid' => $kandidat_id));						
										
									}else{
										$kandidat_drzavljanstvo_vrsta_txt = "NONEU";
										$mjesectermina = date("m",strtotime($kandidat_datum_termina));
										$godinatermina = date("Y",strtotime($kandidat_datum_termina));
										
										//Provjeri da li postoji mjesec termina unutar projekata
										$check_termin_month = $db->prepare("
																SELECT project_id, project_name
																FROM idk_projects
																WHERE project_nalogid = :project_nalogid AND $mjesectermina BETWEEN MONTH(project_datumtermina) AND MONTH(project_datumterminado) AND YEAR(project_datumtermina) = $godinatermina");
									
										$check_termin_month->execute(array(
														':project_nalogid' => $lg_nalogid));
									
										$terminmonthcount = $check_termin_month->rowcount();						
										$terminmonth = $check_termin_month->fetch();	
										$project_id_termin = $terminmonth['project_id'];					
										$project_name = $terminmonth['project_name'];						
										
										if($terminmonthcount == 0){
											
											//IF USER IS NON EU - Transfer him into Prijave project within the order
											$nalog_query = $db->prepare("
																	SELECT project_id
																	FROM idk_projects
																	WHERE project_nalogid = :project_nalogid AND (project_name LIKE '%Prijave%' OR project_name LIKE '%Prijava%') ");
										
											$nalog_query->execute(array(
															':project_nalogid' => $lg_nalogid));
										
											$nalogrow = $nalog_query->fetch();
											if(!$nalogrow){
												//sta raditi ako nema projekta "PRIJAVA"
												//IDU U NOVE PROJEKTI ZA VIBER NALOG
											}else{
												
												$project_id = $nalogrow['project_id'];	
												
												$query_project = $db->prepare("
																INSERT INTO idk_project_kandidati
																	(pk_projectid, pk_kandidatid)
																VALUES
																	(:pk_projectid, :pk_kandidatid)");
									
												$query_project->execute(array(
																':pk_projectid' => $project_id,
																':pk_kandidatid' => $kandidat_id));	
												
												addToLogsStatusPrijave(NULL, $project_id, 2, $kandidat_id, 3);
											}
											
										}else{
											$query_project = $db->prepare("
															INSERT INTO idk_project_kandidati
																(pk_projectid, pk_kandidatid)
															VALUES
																(:pk_projectid, :pk_kandidatid)");
					
											$query_project->execute(array(
															':pk_projectid' => $project_id_termin,
															':pk_kandidatid' => $kandidat_id));	
										}
									}
								}
							//VEZANJE ZA PROJEKT

							//INSERT SKOLE
								if($skola_naziv != "nema" AND $skola_naziv != null){
									$insert_skole = $db->prepare("
													INSERT INTO idk_kandidat_edukacija
														(ke_naziv_kvalifikacije, ke_naziv, ke_vrsta_obrazovanja, ke_kandidat_id, ke_skola_id, ke_smjer_id)
													VALUES
														(:ke_naziv_kvalifikacije,:ke_naziv,:ke_vrsta_obrazovanja,:ke_kandidat_id,:ke_skola_id,:ke_smjer_id)");
							
									$insert_skole->execute(array(
												':ke_naziv_kvalifikacije' => $smjer_naziv,
												':ke_naziv' => $skola_naziv,
												':ke_vrsta_obrazovanja' => "srednje",
												':ke_skola_id' => $skola_id_post,
												':ke_smjer_id' => $smjer_id_post,
												':ke_kandidat_id' => $kandidat_id));
								}
							//INSERT SKOLE
							
							//INSERT TELEFONA I MAILA
								$kki_grupa = 1;
								$kki_naziv = "Mobilni";

								//Add mobilni to db
								$query_mob = $db->prepare("
												INSERT INTO idk_kandidat_kontakt_info
													(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id)
												VALUES
													(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id)");

								$query_mob->execute(array(
											':kki_grupa' => $kki_grupa,
											':kki_naziv' => $kki_naziv,
											':kki_podatak' => $mobile_phone,
											':kki_kandidat_id' => $kandidat_id));
								
								$kki_grupa_e = 2;
								$kki_naziv_e = "E-mail";
								if($kandidat_email != null){
									$kki_podatak_e = $_POST['kandidat_email'];

									//Add kontakt info to db
									$query_email = $db->prepare("
													INSERT INTO idk_kandidat_kontakt_info
														(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id)
													VALUES
														(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id)");

									$query_email->execute(array(
												':kki_grupa' => $kki_grupa_e,
												':kki_naziv' => $kki_naziv_e,
												':kki_podatak' => $kki_podatak_e,
												':kki_kandidat_id' => $kandidat_id));
								}
							//INSERT TELEFONA I MAILA
							
							//Add to table users (chatbot)
								$random_string = generateRandomString();
								$options = [
									'cost' => 10,
								];
								$random_password = password_hash($random_string, PASSWORD_BCRYPT, $options);
								
								//Add to logs candidate IP	
									$date_time_ip = date("F j, Y, g:i T");
									if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
										$ip = $_SERVER['HTTP_CLIENT_IP'];
									} elseif (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
										$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
									} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
										$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
									} else {
										$ip = $_SERVER['REMOTE_ADDR'];
									}
									$log_desc = "Kandidat: " . $kandidat_ime . " " . $kandidat_prezime . "(".$kandidat_id."). ".$date_time_ip."; IP: ".$ip." (".$random_string.")";
									$log_type = "5";
									addToLogs($log_desc, $log_type);
								//Add to logs candidate IP

								// DE i IT kandidate slati na korak 2
								if($lg_language == "it"){
									header("Location: registracija/korak2/$kandidat_id/$kandidat_check");
								}else{
									
									if($urlid == 0){
										header("Location: kandidati?page=list_ajax");
									}else{
										
										$characters = '0123456789';
										$charactersLength = strlen($characters);
										$randomString = '';
										for ($i = 0; $i < 5; $i++) {
											$randomString .= $characters[rand(0, $charactersLength - 1)];
										}
										$bot_koriscnicko_ime = $kandidat_ime.$randomString;
										
										$query_user = $db->prepare("
													INSERT INTO users
														(phone, name, nalog_id, email, password, kandidat_id)
													VALUES
														(:phone, :name, :nalog_id, :email, :password, :kandidat_id)");

										$query_user->execute(array(
													':phone' => $mobile_phone,
													':name' => $kandidat_full_name,
													':nalog_id' => $lg_nalogid,
													':email' => $bot_koriscnicko_ime,
													':password' => $random_password,
													':kandidat_id' => $kandidat_id));
										
										
										$phone_f = str_replace("+", '', $mobile_phone);
										/*
										sendSmsToCandidate1($random_string, $phone_f, $kandidat_prijava_na);
										sleep(1);  // Seconds
										sendSmsToCandidate2($random_string, $phone_f, $kandidat_prijava_na);
										sleep(1);  // Seconds
										sendSmsToCandidate3($random_string, $phone_f, $kandidat_email);
										sleep(1);  // Seconds
										sendSmsToCandidate4($random_string, $phone_f, $kandidat_email);
										*/
										
										
										$link_dload = "https://crm.job-step.com/download";
										$link_uputs = "https://bit.ly/3V177tF";

										$kandidat_full_name = getCandidateFullnameR($kandidat_id);

										$to_send_viber_poruka_1 = $kandidat_full_name.$text_viber_new_prva_poruka; 
										$to_send_sms_poruka_1 	= $kandidat_full_name.$text_sms_new_prva_poruka; 

										$to_send_viber_poruka_2 = $text_viber_new_druga_poruka;
										$to_send_sms_poruka_2 	= $text_sms_new_druga_poruka." ".$link_dload;

										$to_send_viber_poruka_3 = $text_viber_new_treca_poruka_1.'\n\n'.$text_viber_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_viber_new_treca_poruka_3.$random_string;
										$to_send_sms_poruka_3 	= $text_sms_new_treca_poruka_1.'\n\n'.$text_sms_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_sms_new_treca_poruka_3.$random_string;
										// var_dump($to_send_viber_poruka_3);
										// var_dump($to_send_sms_poruka_3);
										// exit();
										viberPrijava1($phone_f, $to_send_sms_poruka_1, $to_send_viber_poruka_1);
										sleep(1);
										viberPrijava2($phone_f, $to_send_sms_poruka_2, $to_send_viber_poruka_2, $text_btn_new_druga_poruka, $link_dload);
										sleep(1);
										viberPrijava1($phone_f, $to_send_sms_poruka_3, $to_send_viber_poruka_3);
										sleep(1);

										if($lg_language == "bs"){
											$to_send_viber_poruka_4 = $text_viber_new_cetvrta_poruka;
											$to_send_sms_poruka_4 	= $text_sms_new_cetvrta_poruka." ".$link_uputs;
											
											viberPrijava2($phone_f, $to_send_sms_poruka_4, $to_send_viber_poruka_4, $text_btn_new_cetvrta_poruka, $link_uputs);
										}
										if($kandidat_email != null)
											sendCandidateMessengerMail($random_string, $kandidat_email, $bot_koriscnicko_ime);
										checkCandidateInputs($kandidat_id);
										if($urlid == 530){
											header("Location: https://job-step.org/registracija/thank_you/$lg_language" );
										}else{
											header("Location: " . getJoinUrlr() . "/job/thank_you/$kandidat_id/$kandidat_check/$lg_language");
										}
									}
								}
							//Add to table users (chatbot)
							
							notifForOnlneRegister($kandidat_id, $kandidat_check);
							
						}

						//PONOVNA PRIJAVA
						else{
							$kandidat_group = $_POST['kandidat_prijava_na'];
							$group_title_query = $db->prepare("
											SELECT kg_id, kg_title
											FROM idk_kandidati_grupe
											WHERE kg_id = :kg_id
											");
					
							$group_title_query->execute(array(
								":kg_id" => $kandidat_group
							));
							$row_kg_title = $group_title_query->fetch();
							$kandidat_prijava_na = $row_kg_title['kg_title'];
							$kandidat_vozacka_kategorija_c = $rows_user['kandidat_vozacka_kategorija'];
							
							//Ako kandidat vec ima unesenu kategoriju vozacke onda za update ide to sto ima uneseno
							//Ako nema onda kupimo sa prijave, a ako je na prijavi null i ovdje ce upasti null
							//ako je na prijavi ista uneseno onda ce biti uneseno samo B za kategoriju

							if($kandidat_vozacka_kategorija_c != null){
								$kandidat_vozacka_dozvola_for_update = "Da";
								$kandidat_vozacka_kategorija_for_update = $kandidat_vozacka_kategorija_c;
							}else{
								$kandidat_vozacka_dozvola_for_update = $kandidat_vozacka_dozvola;
								$kandidat_vozacka_kategorija_for_update = $kandidat_vozacka_kategorija;
							}

							/*if($check_slika == "none" or $check_slika == "none.jpg"){
								//Upload and save kandidat_slika
								if($_FILES['kandidat_slika'] !== null){
									if($_FILES['kandidat_slika']['size'] !== 0) {
										$kandidat_slika = $_FILES['kandidat_slika'];
							
										//File properties
										$file_name = $kandidat_slika['name'];
										$file_tmp = $kandidat_slika['tmp_name'];
										$file_size = $kandidat_slika['size'];
										$file_error = $kandidat_slika['error'];
							
										//File extension
										$file_ext = explode('.', $file_name);
										$file_ext = strtolower(end($file_ext));
							
										$allowed = array('jpg', 'png');
							
										if(in_array($file_ext, $allowed)) {
							
											$kandidat_slika_final = uniqid() . '.' . $file_ext;
											$file_destination = 'files/kandidati/' . $kandidat_slika_final;
							
											if(move_uploaded_file($file_tmp, $file_destination)) {
							
												$path_to_image_directory = "files/kandidati/";
												$final_width_of_image = 660;
							
												if(preg_match('/[.](jpg)$/', $kandidat_slika_final)) {
													$im = imagecreatefromjpeg($path_to_image_directory . $kandidat_slika_final);
												} else if (preg_match('/[.](png)$/', $kandidat_slika_final)) {
													$im = imagecreatefrompng($path_to_image_directory . $kandidat_slika_final);
												}
							
												$ox = imagesx($im);
												$oy = imagesy($im);
												$nx = $final_width_of_image;
												$ny = floor($oy * ($final_width_of_image / $ox));
												$nm = imagecreatetruecolor($nx, $ny);
							
												imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);
												imagejpeg($nm, $path_to_image_directory . $kandidat_slika_final);
							
											}
										}
									}else{
										$kandidat_slika_final = "none";
									}
								}else
									$kandidat_slika_final = "none";
							}else*/
							$kandidat_slika_final = "none";
							
							$check_messenger = $db->prepare("
													SELECT id, email, phone
													FROM users
													WHERE kandidat_id = :kandidat_id ");
						
							$check_messenger->execute(array(
											':kandidat_id' => $kandidat_id_c
											));
						
							$nr_of_rows_mess = $check_messenger->rowCount();
							if($nr_of_rows_mess == 0){
								$kandidat_status_messenger = 1;
							}else{
								
							}
							
							//Update user
								$update_user = $db->prepare("
												UPDATE idk_kandidati
												SET kandidat_drzavljanstvo_vrsta = :kandidat_drzavljanstvo_vrsta, kandidat_mobitel= :kandidat_mobitel, kandidat_slika = :kandidat_slika, kandidat_status_messenger = :kandidat_status_messenger, kandidat_datumrodjenja = :kandidat_datumrodjenja, datum_termina = :datum_termina, datum_aplikacije = :datum_aplikacije, kandidat_procjenatermina = :kandidat_procjenatermina, kandidat_viza = :kandidat_viza, kandidat_viza_vrijedi_do = :kandidat_viza_vrijedi_do, boravak_eu = :boravak_eu, kandidat_visitedurl = :kandidat_visitedurl, kandidat_prijava_na = :kandidat_prijava_na, kandidat_iskustvo_u_struci = :kandidat_iskustvo_u_struci, kandidat_iskustvo_u_struci_trajanje = :kandidat_iskustvo_u_struci_trajanje, kandidat_termin_za_vizu = :kandidat_termin_za_vizu, kandidat_vozacka_dozvola = :kandidat_vozacka_dozvola, kandidat_vozacka_kategorija = :kandidat_vozacka_kategorija
												WHERE kandidat_id = :kandidat_id
												");
						
								$update_user->execute(array(
											':kandidat_id' => $kandidat_id_c,
											':kandidat_drzavljanstvo_vrsta' => $kandidat_drzavljanstvo_vrsta,
											':kandidat_mobitel' => $mobile_phone,
											':kandidat_slika' => $kandidat_slika_final,
											':kandidat_status_messenger' => $kandidat_status_messenger,
											':kandidat_datumrodjenja' => $kandidat_datumrodjenja,
											':datum_termina' => $kandidat_datum_termina,
											':datum_aplikacije' => $kandidat_datum_aplikacije,
											':kandidat_procjenatermina' => $kandidat_procjenatermina,
											':kandidat_viza' => $kandidat_viza,
											':kandidat_viza_vrijedi_do' => $kandidat_viza_vrijedi_do,
											':boravak_eu' => $eu_drzava,
											':kandidat_visitedurl' => $url_id,
											':kandidat_prijava_na' => $kandidat_prijava_na,
											':kandidat_iskustvo_u_struci' => $kandidat_iskustvo_u_struci,
											':kandidat_iskustvo_u_struci_trajanje' => $kandidat_iskustvo_u_struci_trajanje,
											':kandidat_termin_za_vizu' => $termin_za_vizu_carglass,
											':kandidat_vozacka_dozvola' => $kandidat_vozacka_dozvola_for_update,
											':kandidat_vozacka_kategorija' => $kandidat_vozacka_kategorija_for_update
											));
							//Update user

							
							// BILJEŠKA ZA RADNO ISKUSTVO - SAMO DA/NE
								if($iskustvo_u_struci_note != null){
									$note_group = 2;
									$note_dataid = $kandidat_id_c;
									$note_datetime = date('Y-m-d H:i:s');
									
									$query_biljeske = $db->prepare("
													INSERT INTO idk_notes
														(note_txt, note_datetime, note_group, note_dataid, note_employeeid)
													VALUES
														(:note_txt, :note_datetime, :note_group, :note_dataid, :note_employeeid)");
				
									$query_biljeske->execute(array(
												':note_txt' => $iskustvo_u_struci_note,
												':note_datetime' => $note_datetime,
												':note_group' => $note_group,
												':note_dataid' => $note_dataid,
												':note_employeeid' => 139));
								}
							// BILJEŠKA ZA RADNO ISKUSTVO - SAMO DA/NE

							// NOVA BILJEŠKA 17.07.2025 ZA VRIJEME POZIVA
								if($vrijeme_poziva != null){
									$note_group = 2;
									$note_dataid = $kandidat_id_c;
									$note_datetime = date('Y-m-d H:i:s');
									
									$note_txt_cert = "Željeno vrijeme poziva: ".$vrijeme_poziva;
									
									$query_biljeske = $db->prepare("
													INSERT INTO idk_notes
														(note_txt, note_datetime, note_group, note_dataid, note_employeeid)
													VALUES
														(:note_txt, :note_datetime, :note_group, :note_dataid, :note_employeeid)");
				
									$query_biljeske->execute(array(
												':note_txt' => $note_txt_cert,
												':note_datetime' => $note_datetime,
												':note_group' => $note_group,
												':note_dataid' => $note_dataid,
												':note_employeeid' => 139));
								}
							// NOVA BILJEŠKA 17.07.2025 ZA VRIJEME POZIVA

							// BILJEŠKA 15.12.2025 ZA ENGLESKI JEZIK
							if($engleski_b2_da_ne != null){
								$note_group = 2;
								$note_dataid = $kandidat_id_c;
								$note_datetime = date('Y-m-d H:i:s');
								
								$note_txt_cert = "Poznajete li engleski jezik minimalno B2: ".$engleski_b2_da_ne .".";
								
								$query_biljeske = $db->prepare("
												INSERT INTO idk_notes
													(note_txt, note_datetime, note_group, note_dataid, note_employeeid)
												VALUES
													(:note_txt, :note_datetime, :note_group, :note_dataid, :note_employeeid)");
			
								$query_biljeske->execute(array(
											':note_txt' => $note_txt_cert,
											':note_datetime' => $note_datetime,
											':note_group' => $note_group,
											':note_dataid' => $note_dataid,
											':note_employeeid' => 139));
							}
							// BILJEŠKA 15.12.2025 ZA ENGLESKI JEZIK

							//SPICIFIČNI INFO ZA ODREĐENE FORME - BILJEŠKE
								if($grad_za_razgovor != null){
									$note_group = 2;
									$note_dataid = $kandidat_id_c;
									$note_datetime = date('Y-m-d H:i:s');
									
									$note_txt_cert = "Izabrani grad za razgovor: ".$grad_za_razgovor;
									
									$query_biljeske = $db->prepare("
													INSERT INTO idk_notes
														(note_txt, note_datetime, note_group, note_dataid, note_employeeid)
													VALUES
														(:note_txt, :note_datetime, :note_group, :note_dataid, :note_employeeid)");
				
									$query_biljeske->execute(array(
												':note_txt' => $note_txt_cert,
												':note_datetime' => $note_datetime,
												':note_group' => $note_group,
												':note_dataid' => $note_dataid,
												':note_employeeid' => 67));
								}
								if($atu_radno_iskustvo != null OR $pokrajina_rada != null){
									$note_group = 2;
									$note_dataid = $kandidat_id_c;
									$note_datetime = date('Y-m-d H:i:s');
									$note_txt_cert = "";
									if ($atu_radno_iskustvo != null) {
										if($atu_radno_iskustvo == 1){ 
											$atu_radno_iskustvo_txt = "Da";
										}elseif($atu_radno_iskustvo == 0){
											$atu_radno_iskustvo_txt = "Ne";
										}else{
											$atu_radno_iskustvo_txt = "Undefined";
										}
										$note_txt_cert = $note_txt_cert . " Da li imate radnog iskustva kao automehanicar: ".$atu_radno_iskustvo_txt.";";
									}
									if($pokrajina_rada != null){
										$note_txt_cert = $note_txt_cert . " U kojoj pokrajini zelite raditi: ".$pokrajina_rada.";";
									}
									
									$query_biljeske = $db->prepare("
													INSERT INTO idk_notes
														(note_txt, note_datetime, note_group, note_dataid, note_employeeid)
													VALUES
														(:note_txt, :note_datetime, :note_group, :note_dataid, :note_employeeid)");
				
									$query_biljeske->execute(array(
												':note_txt' => $note_txt_cert,
												':note_datetime' => $note_datetime,
												':note_group' => $note_group,
												':note_dataid' => $note_dataid,
												':note_employeeid' => 67));
								}
							//SPICIFIČNI INFO ZA ODREĐENE FORME - BILJEŠKE

							//INSERT/UPDATE JEZIKA
								if(isset($_POST['nivo_jezika'])){
									$kj_naziv_njemacki = "Njemački";
									$kj_znanje_njemacki = $_POST['nivo_jezika'];
									$query_check = $db->prepare("SELECT
																	kj_id
																FROM
																	idk_kandidat_jezici
																WHERE
																	kj_naziv = :kj_naziv
																AND
																	kj_kandidatid = :kandidat_id
																");
									$query_check->execute(array(
										":kandidat_id" => $kandidat_id_c,
										":kj_naziv" => $kj_naziv_njemacki
									));
									$count_jezik = $query_check->rowCount();
									$result_query_check = $query_check->fetch();
									if($count_jezik > 0){
										$kj_id = $result_query_check['kj_id'];
										//UPDATE
										$query_njem = $db->prepare("
											UPDATE idk_kandidat_jezici
											SET kj_naziv = :kj_naziv, kj_citanje = :kj_citanje, kj_slusanje = :kj_slusanje, kj_citanje = :kj_citanje, kj_govorna_interakcija = :kj_govorna_interakcija, kj_govorna_produkcija = :kj_govorna_produkcija, kj_pisanje = :kj_pisanje
											WHERE kj_id = :kj_id
											");
										$query_njem->execute(array(
													':kj_naziv' => $kj_naziv_njemacki,
													':kj_slusanje' => $kj_znanje_njemacki,
													':kj_citanje' => $kj_znanje_njemacki,
													':kj_govorna_interakcija' => $kj_znanje_njemacki,
													':kj_govorna_produkcija' => $kj_znanje_njemacki,
													':kj_pisanje' => $kj_znanje_njemacki,
													':kj_id' => $kj_id));		
									} else {
											//INSERT
										
										// Add language knowlege
										$query_njem = $db->prepare("
														INSERT INTO idk_kandidat_jezici
															(kj_naziv, kj_slusanje, kj_citanje, kj_govorna_interakcija, kj_govorna_produkcija, kj_pisanje, kj_kandidatid)
														VALUES
															(:kj_naziv, :kj_slusanje, :kj_citanje, :kj_govorna_interakcija, :kj_govorna_produkcija, :kj_pisanje, :kj_kandidatid)");
								
										$query_njem->execute(array(
													':kj_naziv' => $kj_naziv_njemacki,
													':kj_slusanje' => $kj_znanje_njemacki,
													':kj_citanje' => $kj_znanje_njemacki,
													':kj_govorna_interakcija' => $kj_znanje_njemacki,
													':kj_govorna_produkcija' => $kj_znanje_njemacki,
													':kj_pisanje' => $kj_znanje_njemacki,
													':kj_kandidatid' => $kandidat_id_c));
									}
								}

								if(isset($_POST['nivo_engleskog_jezika'])){
									
									$kj_naziv_engleski = "Engleski";
									$kj_znanje_engleski = $_POST['nivo_engleskog_jezika'];

									$query_check = $db->prepare("SELECT
																	kj_id
																FROM
																	idk_kandidat_jezici
																WHERE
																	kj_naziv = :kj_naziv
																AND
																	kj_kandidatid = :kandidat_id
																");
									$query_check->execute(array(
										":kandidat_id" => $kandidat_id_c,
										":kj_naziv" => $kj_naziv_engleski
									));
									$count_jezik = $query_check->rowCount();
									$result_query_check = $query_check->fetch();
									if($count_jezik > 0){
										$kj_id = $result_query_check['kj_id'];
										//UPDATE
										$query_eng = $db->prepare("
											UPDATE idk_kandidat_jezici
											SET kj_naziv = :kj_naziv, kj_citanje = :kj_citanje, kj_slusanje = :kj_slusanje, kj_citanje = :kj_citanje, kj_govorna_interakcija = :kj_govorna_interakcija, kj_govorna_produkcija = :kj_govorna_produkcija, kj_pisanje = :kj_pisanje
											WHERE kj_id = :kj_id
											");
										$query_eng->execute(array(
													':kj_naziv' => $kj_naziv_engleski,
													':kj_slusanje' => $kj_znanje_engleski,
													':kj_citanje' => $kj_znanje_engleski,
													':kj_govorna_interakcija' => $kj_znanje_engleski,
													':kj_govorna_produkcija' => $kj_znanje_engleski,
													':kj_pisanje' => $kj_znanje_engleski,
													':kj_id' => $kj_id));		
									}else{

										// Add language knowlege
										$query_eng = $db->prepare("
														INSERT INTO idk_kandidat_jezici
															(kj_naziv, kj_slusanje, kj_citanje, kj_govorna_interakcija, kj_govorna_produkcija, kj_pisanje, kj_kandidatid)
														VALUES
															(:kj_naziv, :kj_slusanje, :kj_citanje, :kj_govorna_interakcija, :kj_govorna_produkcija, :kj_pisanje, :kj_kandidatid)");
								
										$query_eng->execute(array(
													':kj_naziv' => $kj_naziv_engleski,
													':kj_slusanje' => $kj_znanje_engleski,
													':kj_citanje' => $kj_znanje_engleski,
													':kj_govorna_interakcija' => $kj_znanje_engleski,
													':kj_govorna_produkcija' => $kj_znanje_engleski,
													':kj_pisanje' => $kj_znanje_engleski,
													':kj_kandidatid' => $kandidat_id_c));
									}
								}
							//INSERT/UPDATE JEZIKA

							//INSERT INTO KANDIDAT LOG STATUSI
								$query_log_status = $db->prepare("
										INSERT INTO idk_log_kandidat_statusi
											(lks_kandidat_id, lks_status_obrade, lks_status_messenger, lks_datetime, lks_link_id)
										VALUES
											(:lks_kandidat_id, :lks_status_obrade, :lks_status_messenger, :lks_datetime, :lks_link_id)
								");
								
								$query_log_status->execute(array(
										':lks_kandidat_id' => $kandidat_id_c,
										':lks_status_obrade' => 9,
										':lks_status_messenger' => 0,
										':lks_datetime' => $kandidat_datetime,
										':lks_link_id' => $urlid
								));
							//INSERT INTO KANDIDAT LOG STATUSI

							//SPECIFIČNI INSERT PROJEKTA ZA LINK 446
								if($urlid == 446){
									
									$get_project = $db->prepare("
															SELECT project_id
															FROM idk_projects
															WHERE project_nalogid = 117 AND (project_name LIKE '%$kandidat_prijava_na%') ");
								
									$get_project->execute();
								
									$gp_row = $get_project->fetch();
									$projectid = $gp_row['project_id'];
									
									$check_project = $db->prepare("
															SELECT pk_projectid
															FROM idk_project_kandidati
															WHERE pk_projectid = :pk_projectid AND pk_kandidatid = :pk_kandidatid");
								
									$check_project->execute(array(
														':pk_projectid' => $projectid,
														':pk_kandidatid' => $kandidat_id_c
									));
									if($check_project->rowCount() == 0){
										
										$query_project = $db->prepare("
														INSERT INTO idk_project_kandidati
															(pk_projectid, pk_kandidatid)
														VALUES
															(:pk_projectid, :pk_kandidatid)");
							
										$query_project->execute(array(
														':pk_projectid' => $projectid,
														':pk_kandidatid' => $kandidat_id_c));
									}
								}
							//SPECIFIČNI INSERT PROJEKTA ZA LINK 446

							//GET PARTNER DATA
								if($partner_token != null){
									$partner_query = $db->prepare("
													SELECT jp_id, jp_fcmtoken, jp_position, jp_imeprezime, jp_user_type
													FROM idk_jobstep_partners
													WHERE jp_mailconfirmation_token = :jp_mailconfirmation_token
													");
							
										$partner_query->execute(array(
											":jp_mailconfirmation_token" => $partner_token
									));
							
									$row_partner = $partner_query->fetch();
									$partner_id = $row_partner['jp_id'];
									$partner_position = $row_partner['jp_position'];
									$jp_fcmtoken = $row_partner['jp_fcmtoken'];
									$partner_ime = $row_partner['jp_imeprezime'];
									$jp_user_type = $row_partner['jp_user_type']; 
									$log_poruka = "Novi kandidat ".$kandidat_ime." je prijavljen preko vašeg linka.";
									
									if ($jp_user_type == 1) {
										$kandidat_porijeklo = 8; /*Jer je Emir rekao da ne treba 9 stavljati - postoji flag rezervisan - ako je rezervisan - nece ga prebaciti na 8 */
									} else {
										$kandidat_porijeklo = 7;
										// send_notification_partnerapp($jp_fcmtoken, $partner_ime, $log_poruka);
									}
									
								}else{
									$partner_id = null;
									$partner_position = null;
									$kandidat_porijeklo = 0;
								}
							//GET PARTNER DATA

							//VEZANJE ZA PROJEKAT
								$rezervisanFlag = 0;
								
								if($lg_nalogid != 0){
									$nalog_query = $db->prepare("
															SELECT project_id
															FROM idk_projects
															WHERE project_nalogid = :project_nalogid AND (project_name LIKE '%Prijave%' OR project_name LIKE '%Prijava%') ");
								
									$nalog_query->execute(array(
													':project_nalogid' => $lg_nalogid));
								
									$nalogrow = $nalog_query->fetch();
									if(!$nalogrow){
										//sta raditi ako nema projekta "PRIJAVA"
										//IDU U NOVE PROJEKTI ZA VIBER NALOG
										
									}else{
									
										$project_id = $nalogrow['project_id'];	
										$check_project = $db->prepare("
																SELECT pk_projectid
																FROM idk_project_kandidati
																WHERE pk_projectid = :pk_projectid AND pk_kandidatid = :pk_kandidatid");
									
										$check_project->execute(array(
															':pk_projectid' => $project_id,
															':pk_kandidatid' => $kandidat_id_c
										));
										if($check_project->rowCount() == 0){
											
											//Provjera rezervisanosti START
											
											$queryPrijavaNalog = $db->prepare("
												SELECT 
													kandidat_nalog_id, kandidat_status_prijave
												FROM 
													idk_kandidati
												WHERE 
													kandidat_id = :kandidat_id
											");
											$queryPrijavaNalog->execute(array(
												':kandidat_id' => $kandidat_id_c
											));
											$rowPrijavaNalog = $queryPrijavaNalog->fetch();
											$nalogIdPN = intval($rowPrijavaNalog["kandidat_nalog_id"]);
											$statusPrijavePN = intval($rowPrijavaNalog["kandidat_status_prijave"]);
											
											$queryProjekti = $db->prepare("
												SELECT 
													count(pk.pk_id) AS brojac
												FROM 
													idk_project_kandidati pk
												JOIN 
													idk_projects p
												ON 
													pk.pk_projectid = p.project_id
												WHERE 
													pk.pk_kandidatid = :pk_kandidatid
													AND 
													(
														project_name LIKE '%Intervju%' 
														OR 
														project_name LIKE '%Obrađeno%' 
														OR 
														project_name LIKE '%Završeni kandidati%' 
														OR 
														project_name LIKE '%BOT - ispunjava uslove%' 
														OR 
														project_name LIKE '%Baza - odgovara za nalog%'
														OR 
														project_name LIKE '%Casting%' 
														OR 
														project_name LIKE '%Ugovor%'
													)
													AND p.project_nalogid != 44
											");
											$queryProjekti->execute(array(
												':pk_kandidatid' => $kandidat_id_c
											));
											$rowProjekti = $queryProjekti->fetch();
											$brojacUProjektuP = intval($rowProjekti["brojac"]);
											
											if($nalogIdPN != 0 OR $brojacUProjektuP != 0 OR $statusPrijavePN == 4){
												$rezervisanFlag = 1;
											}
											
											if($rezervisanFlag == 0){
												$query_project = $db->prepare("
																INSERT INTO idk_project_kandidati
																	(pk_projectid, pk_kandidatid)
																VALUES
																	(:pk_projectid, :pk_kandidatid)");
									
												$query_project->execute(array(
																':pk_projectid' => $project_id,
																':pk_kandidatid' => $kandidat_id_c));
												addToLogsStatusPrijave(NULL, $project_id, 2, $kandidat_id_c, 3);

												//AKO Kandidat nije rezervisan onda mu se može promijeniti partner
												//UPDATE PARTNER DATA
													if($partner_token != null){
														
														$upd_partner_kandidat = $db->prepare("
															UPDATE idk_kandidati
															SET kandidat_partnerid = :kandidat_partnerid, kandidat_partner_status = :kandidat_partner_status, kandidat_porijeklo = :kandidat_porijeklo
															WHERE kandidat_id = :kandidat_id
														");
														$upd_partner_kandidat->execute(array(
															':kandidat_partnerid' => $partner_id,
															':kandidat_partner_status' => $partner_position,
															':kandidat_porijeklo' => $kandidat_porijeklo,
															':kandidat_id' => $kandidat_id_c
															));
													}
												//ADD PARTNER DATA

											}else{
												pushToProjectCandidateQueue($kandidat_id_c, $project_id, $partner_id);
											}
											//Provjera rezervisanosti END 
										}
									}
								}
							//VEZANJE ZA PROJEKAT


							//INSERT SKOLE
								if($skola_naziv != "nema" AND $skola_naziv != null){
									$insert_skole = $db->prepare("
													INSERT INTO idk_kandidat_edukacija
														(ke_naziv_kvalifikacije, ke_naziv, ke_vrsta_obrazovanja, ke_kandidat_id, ke_skola_id, ke_smjer_id)
													VALUES
														(:ke_naziv_kvalifikacije, :ke_naziv, :ke_vrsta_obrazovanja, :ke_kandidat_id, :ke_skola_id, :ke_smjer_id)");
							
									$insert_skole->execute(array(
												':ke_naziv_kvalifikacije' => $smjer_naziv,
												':ke_naziv' => $skola_naziv,
												':ke_vrsta_obrazovanja' => "srednje",
												':ke_skola_id' => $skola_id_post,
												':ke_smjer_id' => $smjer_id_post,
												':ke_kandidat_id' => $kandidat_id_c));
								}
							//INSERT SKOLE
							
							//Add to table users (chatbot)
								$random_string = generateRandomString();
								$options = [
									'cost' => 10,
								];
								$random_password = password_hash($random_string, PASSWORD_BCRYPT, $options);
								
								//Add to logs candidate IP	
								$date_time_ip = date("F j, Y, g:i T");
								if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
									$ip = $_SERVER['HTTP_CLIENT_IP'];
								} elseif (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
									$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
								} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
									$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
								} else {
									$ip = $_SERVER['REMOTE_ADDR'];
								}
								$log_desc = "Kandidat se ponovo prijavio: " . $kandidat_ime . " " . $kandidat_prezime . ". ".$date_time_ip."; IP: ".$ip." (".$random_string.").";
								$log_type = "5";
								addToLogs($log_desc, $log_type);
								
								//JEZICI, BOT NALOG=0...SAMO BOT
								
								if($nr_of_rows_mess == 0){
									$characters = '0123456789';
									$charactersLength = strlen($characters);
									$randomString = '';
									for ($i = 0; $i < 5; $i++) {
										$randomString .= $characters[rand(0, $charactersLength - 1)];
									}
									$bot_koriscnicko_ime = $kandidat_ime.$randomString;
									
									$query_user = $db->prepare("
												INSERT INTO users
													(phone, name, nalog_id, email, password, kandidat_id)
												VALUES
													(:phone, :name, :nalog_id, :email, :password, :kandidat_id)");

									$query_user->execute(array(
												':phone' => $mobile_phone,
												':name' => $kandidat_full_name,
												':nalog_id' => $lg_nalogid,
												':email' => $bot_koriscnicko_ime,
												':password' => $random_password,
												':kandidat_id' => $kandidat_id_c));
									$phone_f = str_replace("+", '00', $mobile_phone);
									//INFOBIP
									$link_dload = "https://crm.job-step.com/download";
									$link_uputs = "https://bit.ly/3V177tF";
									$kandidat_full_name = getCandidateFullnameR($kandidat_id_c);

									$to_send_viber_poruka_1 = $kandidat_full_name.$text_viber_new_prva_poruka; 
									$to_send_sms_poruka_1 	= $kandidat_full_name.$text_sms_new_prva_poruka; 

									$to_send_viber_poruka_2 = $text_viber_new_druga_poruka;
									$to_send_sms_poruka_2 	= $text_sms_new_druga_poruka." ".$link_dload;

									$to_send_viber_poruka_3 = $text_viber_new_treca_poruka_1.'\n\n'.$text_viber_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_viber_new_treca_poruka_3.$random_string;
									$to_send_sms_poruka_3 	= $text_sms_new_treca_poruka_1.'\n\n'.$text_sms_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_sms_new_treca_poruka_3.$random_string;


									viberPrijava1($phone_f, $to_send_sms_poruka_1, $to_send_viber_poruka_1);
									sleep(1);
									viberPrijava2($phone_f, $to_send_sms_poruka_2, $to_send_viber_poruka_2, $text_btn_new_druga_poruka, $link_dload);
									sleep(1);
									viberPrijava1($phone_f, $to_send_sms_poruka_3, $to_send_viber_poruka_3);
									sleep(1);

									if($lg_language == "bs"){
										$to_send_viber_poruka_4 = $text_viber_new_cetvrta_poruka;
										$to_send_sms_poruka_4 	= $text_sms_new_cetvrta_poruka." ".$link_uputs;
										
										viberPrijava2($phone_f, $to_send_sms_poruka_4, $to_send_viber_poruka_4, $text_btn_new_cetvrta_poruka, $link_uputs);
									}
									if($rezervisanFlag == 0){
										checkCandidateInputs($kandidat_id_c);
									}
									if($kandidat_email != null)
										sendCandidateMessengerMail($random_string, $kandidat_email, $bot_koriscnicko_ime);
									
									
								}else{
									//AKO JE KANDIDAT VEC NA BOTU PROVJERITI STATUS
									if($kandidat_status == 2 OR $kandidat_status == 4 OR $kandidat_status == 5){
										//AKO JE OBRADJEN, NA KONTROLI ILI NA DOPUNI ONDA VEZATI GA ZA NALOG I STAVITI U ODGOVARAJUCI PROJEKAT
										if($rezervisanFlag == 0){
											$update_user = $db->prepare("
														UPDATE users
														SET nalog_id = :nalog_id
														WHERE kandidat_id = :kandidat_id
													");

											$update_user->execute(array(
														':nalog_id' => $lg_nalogid,
														':kandidat_id' => $kandidat_id_c));
										
											checkCandidateInputs($kandidat_id_c);
										}
									}else{
										//AKO NIJE NISTA RADIO NA BOTU ONDA VEZATI GA ZA NALOG I POSLATI PODATKE PONOVO
										
										
										if($rezervisanFlag == 0){
											
											$characters = '0123456789';
											$charactersLength = strlen($characters);
											
											$randomString = '';
											for ($i = 0; $i < 5; $i++) {
												$randomString .= $characters[rand(0, $charactersLength - 1)];
											}
											$bot_koriscnicko_ime = $kandidat_ime.$randomString;
											
											$row_messenger = $check_messenger->fetch();
											$user_id = $row_messenger['id'];
											$mobile_phone = $row_messenger['phone'];
											
											$update_user = $db->prepare("
														UPDATE users
														SET nalog_id = :nalog_id, password = :password, email = :email
														WHERE kandidat_id = :kandidat_id
													");

											$update_user->execute(array(
														':nalog_id' => $lg_nalogid,
														':password' => $random_password,
														':email' => $bot_koriscnicko_ime,
														':kandidat_id' => $kandidat_id_c));
											
											$phone_f = str_replace("+", '', $mobile_phone);
											
											$link_dload = "https://crm.job-step.com/download";
											$link_uputs = "https://bit.ly/3V177tF";
											$kandidat_full_name = getCandidateFullnameR($kandidat_id_c);

											$to_send_viber_poruka_1 = $kandidat_full_name.$text_viber_new_prva_poruka; 
											$to_send_sms_poruka_1 	= $kandidat_full_name.$text_sms_new_prva_poruka; 

											$to_send_viber_poruka_2 = $text_viber_new_druga_poruka;
											$to_send_sms_poruka_2 	= $text_sms_new_druga_poruka." ".$link_dload;

											$to_send_viber_poruka_3 = $text_viber_new_treca_poruka_1.'\n\n'.$text_viber_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_viber_new_treca_poruka_3.$random_string;
											$to_send_sms_poruka_3 	= $text_sms_new_treca_poruka_1.'\n\n'.$text_sms_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_sms_new_treca_poruka_3.$random_string;
											// var_dump($to_send_viber_poruka_3);
											// var_dump($to_send_sms_poruka_3);
											// exit();
											viberPrijava1($phone_f, $to_send_sms_poruka_1, $to_send_viber_poruka_1);
											sleep(1);
											viberPrijava2($phone_f, $to_send_sms_poruka_2, $to_send_viber_poruka_2, $text_btn_new_druga_poruka, $link_dload);
											sleep(1);
											viberPrijava1($phone_f, $to_send_sms_poruka_3, $to_send_viber_poruka_3);
											sleep(1);

											if($lg_language == "bs"){
												$to_send_viber_poruka_4 = $text_viber_new_cetvrta_poruka;
												$to_send_sms_poruka_4 	= $text_sms_new_cetvrta_poruka." ".$link_uputs;
												
												viberPrijava2($phone_f, $to_send_sms_poruka_4, $to_send_viber_poruka_4, $text_btn_new_cetvrta_poruka, $link_uputs);
											}
											
											checkCandidateInputs($kandidat_id_c);
											
											if($kandidat_email != null)
												sendCandidateMessengerMail($random_string, $kandidat_email, $bot_koriscnicko_ime);
										}
									}
								}
							//Add to table users (chatbot)

							//NOTIFIKACIJE
							notifForPonovnaPrijava($kandidat_id_c, $kandidat_check_exists);
							//header("Location: registracija/thank_you/$kandidat_id_c/$kandidat_check_exists");
							
							if($urlid == 530){
								header("Location: https://job-step.org/registracija/thank_you/$lg_language" );
							}else{
								header("Location: " . getJoinUrlr() . "/job/thank_you/$kandidat_id_c/$kandidat_check_exists/$lg_language");
							}
							
						}
					/*}else{
						echo "Greška! Molimo Vas pokusajte kasnije.";
					}*/
					
					
				break;

				case "add_kandidat_new_from_partner":
					
					//OSNOVNE INFORMACIJE
						$kandidat_ime = trim($_POST['kandidat_ime']);
						$kandidat_prezime = trim($_POST['kandidat_prezime']);
						$url_id = $_POST['urlid'];
						$lg_language = $_POST['lg_language'];
						$dan_rodjenja = $_POST["datum_dan"];
						$mjesec_rodjenja = $_POST["datum_mjesec"];
						$godina_rodjenja = $_POST["datum_godina"];
						$mobile_phone = $_POST['kki_phone'];
						$partner_token = $_POST['token'];
						
						$kandidat_full_name = $kandidat_ime." ".$kandidat_prezime;
						
						$kandidat_datumrodjenja = date('Y-m-d', strtotime($godina_rodjenja."-".$mjesec_rodjenja."-".$dan_rodjenja));
					//OSNOVNE INFROMACIJE
					
					//uzimanje naziva grupe
						$kandidat_group = $_POST['kandidat_prijava_na'];
						
						if($kandidat_group != null){

							$group_title_query = $db->prepare("
											SELECT kg_id, kg_title
											FROM idk_kandidati_grupe
											WHERE kg_id = :kg_id
											");
					
							$group_title_query->execute(array(
								":kg_id" => $kandidat_group
							));
							$row_kg_title = $group_title_query->fetch();
							$kandidat_prijava_na = $row_kg_title['kg_title'];
						}else{
							$kandidat_prijava_na = "Ostalo";
							$kandidat_group = 7;
						}
					//uzimanje naziva grupe

					//uzimanje id naloga
						$check_query2 = $db->prepare("
									SELECT lg_nalogid
									FROM idk_link_generator
									WHERE lg_id = :lg_id");

						$check_query2->execute(array(
							':lg_id' => $url_id));

						$num = $check_query2->rowCount();
						$rowlg = $check_query2->fetch();

						$lg_nalogid = $rowlg['lg_nalogid'];
					//uzimanje id naloga
					//DRŽAVLJANSTVO VRSTA
						if(isset($_POST['kandidat_drzavljanstvo_vrsta'])){
							$kandidat_drzavljanstvo_vrsta = $_POST['kandidat_drzavljanstvo_vrsta'];
						}else{
							$kandidat_drzavljanstvo_vrsta = null;
						}
					//DRŽAVLJANSTVO VRSTA
						
					//INCLUDE JEZIK
						if($lg_language == "de")
							include("lang/de.php");
						else
							include("lang/bs.php");
					//INCULUDE JEZIK
					
					//ISKUSTVO U STRUCI	
						if(isset($_POST['kandidat_iskustvo_u_struci'])){
							$kandidat_iskustvo_u_struci = $_POST['kandidat_iskustvo_u_struci'];
							if($kandidat_iskustvo_u_struci == 1){
								$kandidat_iskustvo_u_struci_trajanje = $_POST['iskustvo_u_struci_trajanje'];
							}else{
								$kandidat_iskustvo_u_struci_trajanje = null;
							}
						}
						else{
							$kandidat_iskustvo_u_struci = null;
							$kandidat_iskustvo_u_struci_trajanje = null;
						}
					//ISKUSTVO U STRUCI

					//VOZACKA I KATEGORIJE VOZACKE
						if(isset($_POST['kandidat_vozacka_dozvola'])){
							$kandidat_vozacka_dozvola = $_POST['kandidat_vozacka_dozvola'];
							if($kandidat_vozacka_dozvola == "Da"){
								$kandidat_vozacka_kategorija = "B";
							}else{
								$kandidat_vozacka_kategorija = null;
							}
						}else{
							if(isset($_POST['kandidat_kategorija_vozacke'])){
								if($_POST['kandidat_kategorija_vozacke'] == "Ne"){
									$kandidat_vozacka_dozvola = "Ne";
									$kandidat_vozacka_kategorija = null;	
								}else{
									$kandidat_vozacka_dozvola = "Da";
									$kandidat_vozacka_kategorija = $_POST['kandidat_kategorija_vozacke'];
								}
							}else{
								$kandidat_vozacka_dozvola = null;
								$kandidat_vozacka_kategorija = null;
							}
						}
					//VOZACKA I KATEGORIJE VOZACKE

					//SMJER/ZANIMANJE
						
						if(isset($_POST['smjer_naziv'])){
							
							$smjer_id_post = $_POST['smjer_naziv'];
							if($smjer_id_post != "ostalo" && $smjer_id_post != "0"){
								$query_skola_naziv_id = $db->prepare("
										SELECT skola_id, skola_naziv, ss_naziv FROM idk_skole 
										JOIN idk_skole_smjerovi ON ss_skola_id = skola_id
										WHERE ss_id = $smjer_id_post
								");
								$query_skola_naziv_id->execute();
								$row_skola_naziv_id = $query_skola_naziv_id->fetch();
								$skola_naziv = $row_skola_naziv_id['skola_naziv'];
								$skola_id_post = $row_skola_naziv_id['skola_id'];
								$smjer_naziv = $row_skola_naziv_id['ss_naziv'];
							}else{
								//unos rucne skole za defaultnu formu
								$smjer_naziv = $_POST["smjer_naziv_ru"];
								//provjeri da li smjer vec postoji
								$query_smjer_naziv = $db->prepare("SELECT 
																	ss_id,
																	skola_id,
																	ss_naziv,
																	skola_naziv
																FROM 
																	idk_skole_smjerovi 
																JOIN 
																	idk_skole 
																ON 
																	idk_skole_smjerovi.ss_skola_id = idk_skole.skola_id 
																WHERE 
																	trim(ss_naziv) = '$smjer_naziv' OR
																	trim(ss_naziv_de) = '$smjer_naziv' OR
																	trim(ss_naziv_en) = '$smjer_naziv'
																");
								$query_smjer_naziv->execute();
								$count = $query_smjer_naziv->rowCount();
								$row_smjer_naziv = $query_smjer_naziv->fetch();
								if($count > 0){
									$smjer_id_post = $row_smjer_naziv['ss_id'];
									$smjer_naziv = $row_smjer_naziv['ss_naziv'];
									$skola_id_post = $row_smjer_naziv['skola_id'];
									$skola_naziv = $row_smjer_naziv['skola_naziv'];
								} else {
									$smjer_id_post = null;
									$smjer_naziv = $_POST["smjer_naziv_ru"];
									$skola_id_post = null;
									$skola_naziv = null;
								}
							}
						}else{
							$skola_naziv = "nema";
						}
						
					//SMJER/ZANIMANJE

					//NIVO OBRAZOVANJA
						if(isset($_POST['nivo_obrazovanja'])){
							$nivo_obrazovanja = $_POST['nivo_obrazovanja'];
						}else{
							$nivo_obrazovanja = null;
						}
					//NIVO OBRAZOVANJA
					
						
					// Provjera da li postoji korisnik sa istim imenom i prezimenom u zadnjih sat vremena
						$granicno_vrijeme = date( "Y-m-d H:i:s",  strtotime("- 1 hour" ));
						$check_user = $db->prepare("
												SELECT kandidat_ime, kandidat_id, kandidat_check, kandidat_status, kandidat_vozacka_kategorija
												FROM idk_kandidati
												WHERE TRIM(kandidat_ime) = :kandidat_ime AND TRIM(kandidat_prezime) = :kandidat_prezime AND (kandidat_mobitel = :kandidat_mobitel OR kandidat_datumrodjenja = :kandidat_datumrodjenja) AND kandidat_status != 3");
					
						$check_user->execute(array(
										':kandidat_ime' => $kandidat_ime,
										':kandidat_prezime' => $kandidat_prezime,
										':kandidat_mobitel' => $mobile_phone,
										':kandidat_datumrodjenja' => $kandidat_datumrodjenja
										));
					
						$number_of_rows_user = $check_user->rowCount();

						
						$rows_user = $check_user->fetch();
						
						$kandidat_id_c = $rows_user['kandidat_id'];
						$kandidat_check_exists = $rows_user['kandidat_check'];
						$kandidat_status = $rows_user['kandidat_status'];
						
						$kandidat_datetime = date('Y-m-d H:i:s');
						$kandidat_check = md5(uniqid(rand(), true));
					// Provjera da li postoji korisnik sa istim imenom i prezimenom u zadnjih sat vremena
						
					//NOVA PRIJAVA
						if($number_of_rows_user == 0){
							
							//ADD PARTNER DATA
								if($partner_token != null){
									$partner_query = $db->prepare("
													SELECT jp_id, jp_fcmtoken, jp_position, jp_imeprezime, jp_user_type
													FROM idk_jobstep_partners
													WHERE jp_mailconfirmation_token = :jp_mailconfirmation_token
													");
							
										$partner_query->execute(array(
											":jp_mailconfirmation_token" => $partner_token
									));
							
									$row_partner = $partner_query->fetch();
									$partner_id = $row_partner['jp_id'];
									$partner_position = $row_partner['jp_position'];
									$jp_fcmtoken = $row_partner['jp_fcmtoken'];
									$partner_ime = $row_partner['jp_imeprezime'];
									$jp_user_type = $row_partner['jp_user_type'];
									$log_poruka = "Novi kandidat ".$kandidat_ime." je prijavljen preko vašeg linka.";
									
									if ($jp_user_type == 1) {
										$kandidat_porijeklo = 8;
									} else {
										$kandidat_porijeklo = 6;
									}
									//dodavanje assigned makler
									$check_nalog = $db->prepare("SELECT js_partner_id 
																FROM idk_companies 
																JOIN idk_nalozi ON idk_companies.company_id = idk_nalozi.kompanija_id 
																WHERE nalog_id = :nalog_id");
									$check_nalog->execute(array(
										":nalog_id" => $lg_nalogid
									));
									$row_check_nalog = $check_nalog->fetch();
									if($row_check_nalog['js_partner_id'] != 0){
										$assigned_makler = $row_check_nalog['js_partner_id'];
									}else{
										$assigned_makler = $partner_id;
									}
								}else{
									$partner_id = null;
									$partner_position = null;
									$kandidat_porijeklo = 0;
								}
							//ADD PARTNER DATA
								
							
							//Add user to db
								$query = $db->prepare("
												INSERT INTO idk_kandidati
													( kandidat_check, kandidat_ime, kandidat_prezime, kandidat_drzavljanstvo_vrsta, kandidat_mobitel, kandidat_slika, kandidat_status, kandidat_status_messenger, kandidat_datetime, kandidat_datumrodjenja, kandidat_visitedurl, kandidat_prijava_na, kandidat_group, kandidat_status_prijave, kandidat_partnerid, kandidat_partner_status, kandidat_porijeklo, kandidat_iskustvo_u_struci, kandidat_iskustvo_u_struci_trajanje, kandidat_vozacka_dozvola, kandidat_vozacka_kategorija, kandidat_nivo_obrazovanja, assigned_to_makler)
												VALUES
													(:kandidat_check,:kandidat_ime,:kandidat_prezime,:kandidat_drzavljanstvo_vrsta,:kandidat_mobitel,:kandidat_slika,:kandidat_status,:kandidat_status_messenger,:kandidat_datetime,:kandidat_datumrodjenja,:kandidat_visitedurl,:kandidat_prijava_na,:kandidat_group,:kandidat_status_prijave,:kandidat_partnerid,:kandidat_partner_status,:kandidat_porijeklo,:kandidat_iskustvo_u_struci,:kandidat_iskustvo_u_struci_trajanje,:kandidat_vozacka_dozvola,:kandidat_vozacka_kategorija,:kandidat_nivo_obrazovanja,:assigned_to_makler)");
						
								$query->execute(array(
											':kandidat_check' => $kandidat_check,
											':kandidat_ime' => $kandidat_ime,
											':kandidat_prezime' => $kandidat_prezime,
											':kandidat_drzavljanstvo_vrsta' => $kandidat_drzavljanstvo_vrsta,
											':kandidat_mobitel' => $mobile_phone,
											':kandidat_slika' => "none",
											':kandidat_status' => 0,
											':kandidat_status_messenger' => 0,
											':kandidat_datetime' => $kandidat_datetime,
											':kandidat_datumrodjenja' => $kandidat_datumrodjenja,
											':kandidat_visitedurl' => $url_id,
											':kandidat_prijava_na' => $kandidat_prijava_na,
											':kandidat_group' => $kandidat_group,
											':kandidat_status_prijave' => 2,
											':kandidat_partnerid' => $partner_id,
											':kandidat_partner_status' => $partner_position,
											':kandidat_porijeklo' => $kandidat_porijeklo,
											':kandidat_iskustvo_u_struci' => $kandidat_iskustvo_u_struci,
											':kandidat_iskustvo_u_struci_trajanje' => $kandidat_iskustvo_u_struci_trajanje,
											':kandidat_vozacka_dozvola' => $kandidat_vozacka_dozvola,
											':kandidat_vozacka_kategorija' => $kandidat_vozacka_kategorija,
											':kandidat_nivo_obrazovanja' => $nivo_obrazovanja,
											':assigned_to_makler' => $assigned_makler
											));
											
										
								$kandidat_id = $db->lastInsertId();
							//Add user to db
							
							// INSERT LOG STATUSA
								$query_log_status = $db->prepare("
										INSERT INTO idk_log_kandidat_statusi
											(lks_kandidat_id, lks_status_obrade, lks_status_messenger, lks_datetime, lks_link_id)
										VALUES
											(:lks_kandidat_id, :lks_status_obrade, :lks_status_messenger, :lks_datetime, :lks_link_id)
								");
								
								$query_log_status->execute(array(
										':lks_kandidat_id' => $kandidat_id,
										':lks_status_obrade' => 0,
										':lks_status_messenger' => 0,
										':lks_datetime' => $kandidat_datetime,
										':lks_link_id' => $url_id
								));
							// INSERT LOG STATUSA

							//INSERT JEZIKA
								if(isset($_POST['nivo_jezika'])){
									
									$kj_naziv_njemacki = "Njemački";
									$kj_znanje_njemacki = $_POST['nivo_jezika'];
									// Add language knowlege
									$query_njem = $db->prepare("
													INSERT INTO idk_kandidat_jezici
														(kj_naziv, kj_slusanje, kj_citanje, kj_govorna_interakcija, kj_govorna_produkcija, kj_pisanje, kj_kandidatid)
													VALUES
														(:kj_naziv, :kj_slusanje, :kj_citanje, :kj_govorna_interakcija, :kj_govorna_produkcija, :kj_pisanje, :kj_kandidatid)");
							
									$query_njem->execute(array(
												':kj_naziv' => $kj_naziv_njemacki,
												':kj_slusanje' => $kj_znanje_njemacki,
												':kj_citanje' => $kj_znanje_njemacki,
												':kj_govorna_interakcija' => $kj_znanje_njemacki,
												':kj_govorna_produkcija' => $kj_znanje_njemacki,
												':kj_pisanje' => $kj_znanje_njemacki,
												':kj_kandidatid' => $kandidat_id));
								}
								if(isset($_POST['lang_counter'])){
									$lang_counter = $_POST['lang_counter'];
									for($i = 1; $i <= $lang_counter; $i++){
										$lang_type = $_POST['tip_jezika_'.$i];
										switch($lang_type){
											case "en":
												$lang_name = "Engleski";
											break;
											case "fr":
												$lang_name = "Francuski";
											break;
											case "it":
												$lang_name = "Italijanski";
											break;
											default:
												$lang_name = "Nepoznato";
										}
										$lang_level = $_POST['nivo_jezika_'.$i];
										// Add language knowlege
										$query_add_lang = $db->prepare("
														INSERT INTO idk_kandidat_jezici
															(kj_naziv, kj_slusanje, kj_citanje, kj_govorna_interakcija, kj_govorna_produkcija, kj_pisanje, kj_kandidatid)
														VALUES
															(:kj_naziv, :kj_slusanje, :kj_citanje, :kj_govorna_interakcija, :kj_govorna_produkcija, :kj_pisanje, :kj_kandidatid)");
								
										$query_add_lang->execute(array(
													':kj_naziv' => $lang_name,
													':kj_slusanje' => $lang_level,
													':kj_citanje' => $lang_level,
													':kj_govorna_interakcija' => $lang_level,
													':kj_govorna_produkcija' => $lang_level,
													':kj_pisanje' => $lang_level,
													':kj_kandidatid' => $kandidat_id));
									}
								}
							//INSERT JEZIKA

							//VEZANJE ZA PROJEKT
								$resultProjektInsert = insertCandidatInProjectForPartnerDVAGArrayR($kandidat_id, 1);
								if($resultProjektInsert['status'] != 1){
									$log_desc = "Kandidat: " . $kandidat_id . " se nije povezao za projekt. Link ID: " . $url_id . ". Status greške: " . $resultProjektInsert['status'] . ". Poruka greške: " . $resultProjektInsert['message'] . "." ;
									$log_type = "5";
									addToLogs($log_desc, $log_type);
								}

							//VEZANJE ZA PROJEKT

							//INSERT SKOLE
								if($skola_naziv != "nema"){
									$insert_skole = $db->prepare("
													INSERT INTO idk_kandidat_edukacija
														(ke_naziv_kvalifikacije, ke_naziv, ke_vrsta_obrazovanja, ke_kandidat_id, ke_skola_id, ke_smjer_id)
													VALUES
														(:ke_naziv_kvalifikacije,:ke_naziv,:ke_vrsta_obrazovanja,:ke_kandidat_id,:ke_skola_id,:ke_smjer_id)");
							
									$insert_skole->execute(array(
												':ke_naziv_kvalifikacije' => $smjer_naziv,
												':ke_naziv' => $skola_naziv,
												':ke_vrsta_obrazovanja' => "srednje",
												':ke_skola_id' => $skola_id_post,
												':ke_smjer_id' => $smjer_id_post,
												':ke_kandidat_id' => $kandidat_id));
								}
							//INSERT SKOLE
							
							//INSERT TELEFONA
								$kki_grupa = 1;
								$kki_naziv = "Mobilni";

								//Add mobilni to db
								$query_mob = $db->prepare("
												INSERT INTO idk_kandidat_kontakt_info
													(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id)
												VALUES
													(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id)");

								$query_mob->execute(array(
											':kki_grupa' => $kki_grupa,
											':kki_naziv' => $kki_naziv,
											':kki_podatak' => $mobile_phone,
											':kki_kandidat_id' => $kandidat_id));
								
								$kki_grupa_e = 2;
								$kki_naziv_e = "E-mail";
								
							//INSERT TELEFONA
							
							//Add to logs candidate IP	
								$date_time_ip = date("F j, Y, g:i T");
								if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
									$ip = $_SERVER['HTTP_CLIENT_IP'];
								} elseif (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
									$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
								} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
									$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
								} else {
									$ip = $_SERVER['REMOTE_ADDR'];
								}
								$log_desc = "Kandidat: " . $kandidat_ime . " " . $kandidat_prezime . "(".$kandidat_id."). ".$date_time_ip."; IP: ".$ip." ";
								$log_type = "5";
								addToLogs($log_desc, $log_type);
							//Add to logs candidate IP

							if($url_id != 2040){
								checkCandidateInputs($kandidat_id);
							}
							
							header("Location: " . getJoinUrlr() . "/job/thankyou/$kandidat_id/$kandidat_check/$lg_language");
						}
					//NOVA PRIJAVA
					
					//PONOVNA PRIJAVA
						else{
							
							$kandidat_vozacka_kategorija_c = $rows_user['kandidat_vozacka_kategorija'];
							
							//Ako kandidat vec ima unesenu kategoriju vozacke onda za update ide to sto ima uneseno
							//Ako nema onda kupimo sa prijave, a ako je na prijavi null i ovdje ce upasti null
							//ako je na prijavi ista uneseno onda ce biti uneseno samo B za kategoriju

							if($kandidat_vozacka_kategorija_c != null){
								$kandidat_vozacka_dozvola_for_update = "Da";
								$kandidat_vozacka_kategorija_for_update = $kandidat_vozacka_kategorija_c;
							}else{
								$kandidat_vozacka_dozvola_for_update = $kandidat_vozacka_dozvola;
								$kandidat_vozacka_kategorija_for_update = $kandidat_vozacka_kategorija;
							}
							
							//Update user
								$update_user = $db->prepare("
												UPDATE idk_kandidati
												SET kandidat_drzavljanstvo_vrsta = :kandidat_drzavljanstvo_vrsta, kandidat_mobitel= :kandidat_mobitel, kandidat_slika = :kandidat_slika, kandidat_datumrodjenja = :kandidat_datumrodjenja, kandidat_visitedurl = :kandidat_visitedurl, kandidat_prijava_na = :kandidat_prijava_na, kandidat_iskustvo_u_struci = :kandidat_iskustvo_u_struci, kandidat_iskustvo_u_struci_trajanje = :kandidat_iskustvo_u_struci_trajanje, kandidat_vozacka_dozvola = :kandidat_vozacka_dozvola, kandidat_vozacka_kategorija = :kandidat_vozacka_kategorija, kandidat_nivo_obrazovanja = :kandidat_nivo_obrazovanja
												WHERE kandidat_id = :kandidat_id
												");
						
								$update_user->execute(array(
											':kandidat_id' => $kandidat_id_c,
											':kandidat_drzavljanstvo_vrsta' => $kandidat_drzavljanstvo_vrsta,
											':kandidat_mobitel' => $mobile_phone,
											':kandidat_slika' => "none",
											':kandidat_datumrodjenja' => $kandidat_datumrodjenja,
											':kandidat_visitedurl' => $url_id,
											':kandidat_prijava_na' => $kandidat_prijava_na,
											':kandidat_iskustvo_u_struci' => $kandidat_iskustvo_u_struci,
											':kandidat_iskustvo_u_struci_trajanje' => $kandidat_iskustvo_u_struci_trajanje,
											':kandidat_vozacka_dozvola' => $kandidat_vozacka_dozvola_for_update,
											':kandidat_vozacka_kategorija' => $kandidat_vozacka_kategorija_for_update,
											':kandidat_nivo_obrazovanja' => $nivo_obrazovanja
											));
							//Update user

							//INSERT/UPDATE JEZIKA
								if(isset($_POST['nivo_jezika'])){
									$kj_naziv_njemacki = "Njemački";
									$kj_znanje_njemacki = $_POST['nivo_jezika'];
									$query_check = $db->prepare("SELECT
																	kj_id
																FROM
																	idk_kandidat_jezici
																WHERE
																	kj_naziv = :kj_naziv
																AND
																	kj_kandidatid = :kandidat_id
																");
									$query_check->execute(array(
										":kandidat_id" => $kandidat_id_c,
										":kj_naziv" => $kj_naziv_njemacki
									));
									$count_jezik = $query_check->rowCount();
									$result_query_check = $query_check->fetch();
									if($count_jezik > 0){
										$kj_id = $result_query_check['kj_id'];
										//UPDATE
										$query_njem = $db->prepare("
											UPDATE idk_kandidat_jezici
											SET kj_naziv = :kj_naziv, kj_citanje = :kj_citanje, kj_slusanje = :kj_slusanje, kj_citanje = :kj_citanje, kj_govorna_interakcija = :kj_govorna_interakcija, kj_govorna_produkcija = :kj_govorna_produkcija, kj_pisanje = :kj_pisanje
											WHERE kj_id = :kj_id
											");
										$query_njem->execute(array(
													':kj_naziv' => $kj_naziv_njemacki,
													':kj_slusanje' => $kj_znanje_njemacki,
													':kj_citanje' => $kj_znanje_njemacki,
													':kj_govorna_interakcija' => $kj_znanje_njemacki,
													':kj_govorna_produkcija' => $kj_znanje_njemacki,
													':kj_pisanje' => $kj_znanje_njemacki,
													':kj_id' => $kj_id));		
									} else {
											//INSERT
										
										// Add language knowlege
										$query_njem = $db->prepare("
														INSERT INTO idk_kandidat_jezici
															(kj_naziv, kj_slusanje, kj_citanje, kj_govorna_interakcija, kj_govorna_produkcija, kj_pisanje, kj_kandidatid)
														VALUES
															(:kj_naziv, :kj_slusanje, :kj_citanje, :kj_govorna_interakcija, :kj_govorna_produkcija, :kj_pisanje, :kj_kandidatid)");
								
										$query_njem->execute(array(
													':kj_naziv' => $kj_naziv_njemacki,
													':kj_slusanje' => $kj_znanje_njemacki,
													':kj_citanje' => $kj_znanje_njemacki,
													':kj_govorna_interakcija' => $kj_znanje_njemacki,
													':kj_govorna_produkcija' => $kj_znanje_njemacki,
													':kj_pisanje' => $kj_znanje_njemacki,
													':kj_kandidatid' => $kandidat_id_c));
									}
								}

								if(isset($_POST['lang_counter'])){
									$lang_counter = $_POST['lang_counter'];
									for($i = 1; $i <= $lang_counter; $i++){
										$lang_type = $_POST['tip_jezika_'.$i];
										switch($lang_type){
											case "en":
												$lang_name = "Engleski";
											break;
											case "fr":
												$lang_name = "Francuski";
											break;
											case "it":
												$lang_name = "Italijanski";
											break;
											default:
												$lang_name = "Nepoznato";
										}
										$lang_level = $_POST['nivo_jezika_'.$i];
										
										$query_check = $db->prepare("SELECT
													kj_id
												FROM
													idk_kandidat_jezici
												WHERE
													kj_naziv = :kj_naziv
												AND
													kj_kandidatid = :kandidat_id
												");
										$query_check->execute(array(
											":kandidat_id" => $kandidat_id_c,
											":kj_naziv" => $lang_name
										));
										$count_jezik = $query_check->rowCount();
										$result_query_check = $query_check->fetch();
										if($count_jezik > 0){
											$kj_id = $result_query_check['kj_id'];
											//UPDATE
											$query_eng = $db->prepare("
												UPDATE idk_kandidat_jezici
												SET kj_naziv = :kj_naziv, kj_citanje = :kj_citanje, kj_slusanje = :kj_slusanje, kj_citanje = :kj_citanje, kj_govorna_interakcija = :kj_govorna_interakcija, kj_govorna_produkcija = :kj_govorna_produkcija, kj_pisanje = :kj_pisanje
												WHERE kj_id = :kj_id
												");
											$query_eng->execute(array(
														':kj_naziv' => $lang_name,
														':kj_slusanje' => $lang_level,
														':kj_citanje' => $lang_level,
														':kj_govorna_interakcija' => $lang_level,
														':kj_govorna_produkcija' => $lang_level,
														':kj_pisanje' => $lang_level,
														':kj_id' => $kj_id));		
										}else{

											// Add language knowlege
											$query_eng = $db->prepare("
															INSERT INTO idk_kandidat_jezici
																(kj_naziv, kj_slusanje, kj_citanje, kj_govorna_interakcija, kj_govorna_produkcija, kj_pisanje, kj_kandidatid)
															VALUES
																(:kj_naziv, :kj_slusanje, :kj_citanje, :kj_govorna_interakcija, :kj_govorna_produkcija, :kj_pisanje, :kj_kandidatid)");
									
											$query_eng->execute(array(
														':kj_naziv' => $lang_name,
														':kj_slusanje' => $lang_level,
														':kj_citanje' => $lang_level,
														':kj_govorna_interakcija' => $lang_level,
														':kj_govorna_produkcija' => $lang_level,
														':kj_pisanje' => $lang_level,
														':kj_kandidatid' => $kandidat_id_c));
										}
									}
								}
								
							//INSERT/UPDATE JEZIKA

							//INSERT INTO KANDIDAT LOG STATUSI
								$query_log_status = $db->prepare("
										INSERT INTO idk_log_kandidat_statusi
											(lks_kandidat_id, lks_status_obrade, lks_status_messenger, lks_datetime, lks_link_id)
										VALUES
											(:lks_kandidat_id, :lks_status_obrade, :lks_status_messenger, :lks_datetime, :lks_link_id)
								");
								
								$query_log_status->execute(array(
										':lks_kandidat_id' => $kandidat_id_c,
										':lks_status_obrade' => 9,
										':lks_status_messenger' => 0,
										':lks_datetime' => $kandidat_datetime,
										':lks_link_id' => $url_id
								));
							//INSERT INTO KANDIDAT LOG STATUSI

							//GET PARTNER DATA
								if($partner_token != null){
									$partner_query = $db->prepare("
													SELECT jp_id, jp_fcmtoken, jp_position, jp_imeprezime, jp_user_type
													FROM idk_jobstep_partners
													WHERE jp_mailconfirmation_token = :jp_mailconfirmation_token
													");
							
										$partner_query->execute(array(
											":jp_mailconfirmation_token" => $partner_token
									));
							
									$row_partner = $partner_query->fetch();
									$partner_id = $row_partner['jp_id'];
									$partner_position = $row_partner['jp_position'];
									$jp_fcmtoken = $row_partner['jp_fcmtoken'];
									$partner_ime = $row_partner['jp_imeprezime'];
									$jp_user_type = $row_partner['jp_user_type']; 
									$log_poruka = "Novi kandidat ".$kandidat_ime." je prijavljen preko vašeg linka.";
									// send_notification_partnerapp($jp_fcmtoken, $partner_ime, $log_poruka);
									if ($jp_user_type == 1) {
										$kandidat_porijeklo = 8; /*Jer je Emir rekao da ne treba 9 stavljati - postoji flag rezervisan - ako je rezervisan - nece ga prebaciti na 8 */
									} else {
										$kandidat_porijeklo = 7;
									}
								}else{
									$partner_id = null;
									$partner_position = null;
									$kandidat_porijeklo = 0;
								}
							//GET PARTNER DATA
							
							//VEZANJE ZA PROJEKAT
							
								//Provjera rezervisanosti START
									$rezervisanFlag = 0;
											
									$queryPrijavaNalog = $db->prepare("
										SELECT 
											kandidat_nalog_id, kandidat_status_prijave
										FROM 
											idk_kandidati
										WHERE 
											kandidat_id = :kandidat_id
									");
									$queryPrijavaNalog->execute(array(
										':kandidat_id' => $kandidat_id_c
									));
									$rowPrijavaNalog = $queryPrijavaNalog->fetch();
									$nalogIdPN = intval($rowPrijavaNalog["kandidat_nalog_id"]);
									$statusPrijavePN = intval($rowPrijavaNalog["kandidat_status_prijave"]);
								
									$queryProjekti = $db->prepare("
										SELECT 
											count(pk.pk_id) AS brojac
										FROM 
											idk_project_kandidati pk
										JOIN 
											idk_projects p
										ON 
											pk.pk_projectid = p.project_id
										WHERE 
											pk.pk_kandidatid = :pk_kandidatid
											AND 
											(
												project_name LIKE '%Intervju%' 
												OR 
												project_name LIKE '%Obrađeno%' 
												OR 
												project_name LIKE '%Završeni kandidati%' 
												OR 
												project_name LIKE '%BOT - ispunjava uslove%'
												OR 
												project_name LIKE '%Baza - odgovara za nalog%' 
												OR 
												project_name LIKE '%Casting%' 
												OR 
												project_name LIKE '%Ugovor%'
											)
											AND p.project_nalogid != 44
									");
									$queryProjekti->execute(array(
										':pk_kandidatid' => $kandidat_id_c
									));
									$rowProjekti = $queryProjekti->fetch();
									$brojacUProjektuP = intval($rowProjekti["brojac"]);
								
									if($nalogIdPN != 0 OR $brojacUProjektuP != 0 OR $statusPrijavePN == 4){
										$rezervisanFlag = 1;
									}
								//Provjera rezervisanosti END
								
								//INSERT U PROJEKT ILI QUEUE - START
									if($rezervisanFlag == 0){
										
										$resultProjektInsert = insertCandidatInProjectForPartnerDVAGArrayR($kandidat_id_c, 1);
										if($resultProjektInsert['status'] != 1){
											$log_desc = "Kandidat: " . $kandidat_id_c . " se nije povezao za projekt. Link ID: " . $url_id . ". Status greške: " . $resultProjektInsert['status'] . ". Poruka greške: " . $resultProjektInsert['message'] . "." ;
											$log_type = "5";
											addToLogs($log_desc, $log_type);
										}
										//AKO Kandidat nije rezervisan onda mu se može promijeniti partner
										//UPDATE PARTNER DATA
											if($partner_token != null){
												
												$upd_partner_kandidat = $db->prepare("
													UPDATE idk_kandidati
													SET kandidat_partnerid = :kandidat_partnerid, kandidat_partner_status = :kandidat_partner_status, kandidat_porijeklo = :kandidat_porijeklo
													WHERE kandidat_id = :kandidat_id
												");
												$upd_partner_kandidat->execute(array(
													':kandidat_partnerid' => $partner_id,
													':kandidat_partner_status' => $partner_position,
													':kandidat_porijeklo' => $kandidat_porijeklo,
													':kandidat_id' => $kandidat_id_c
													));
											}
										//UPDATE PARTNER DATA

									}else{
										
										$resultProjektGet = insertCandidatInProjectForPartnerDVAGArrayR($kandidat_id_c, 2);
										if($resultProjektGet['status'] != 1){
											$log_desc = "Kandidat: " . $kandidat_id_c . " nije pronadjen projekt za spremanje u queue. Link ID: " . $url_id . ". Status greške: " . $resultProjektInsert['status'] . ". Poruka greške: " . $resultProjektInsert['message'] . "." ;
											$log_type = "5";
											addToLogs($log_desc, $log_type);
										}else{
											pushToProjectCandidateQueue($kandidat_id_c, $resultProjektGet['projectId'], $partner_id);
										}
									}
								//INSERT U PROJEKT ILI QUEUE - END

							//VEZANJE ZA PROJEKAT


							//INSERT SKOLE
								if($skola_naziv != "nema"){
									$insert_skole = $db->prepare("
													INSERT INTO idk_kandidat_edukacija
														(ke_naziv_kvalifikacije, ke_naziv, ke_vrsta_obrazovanja, ke_kandidat_id, ke_skola_id, ke_smjer_id)
													VALUES
														(:ke_naziv_kvalifikacije, :ke_naziv, :ke_vrsta_obrazovanja, :ke_kandidat_id, :ke_skola_id, :ke_smjer_id)");
							
									$insert_skole->execute(array(
												':ke_naziv_kvalifikacije' => $smjer_naziv,
												':ke_naziv' => $skola_naziv,
												':ke_vrsta_obrazovanja' => "srednje",
												':ke_skola_id' => $skola_id_post,
												':ke_smjer_id' => $smjer_id_post,
												':ke_kandidat_id' => $kandidat_id_c));
								}
							//INSERT SKOLE
							
							//Add to logs candidate IP	
								$date_time_ip = date("F j, Y, g:i T");
								if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
									$ip = $_SERVER['HTTP_CLIENT_IP'];
								} elseif (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
									$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
								} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
									$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
								} else {
									$ip = $_SERVER['REMOTE_ADDR'];
								}
								$log_desc = "Kandidat se ponovo prijavio: " . $kandidat_ime . " " . $kandidat_prezime . ". ".$date_time_ip."; IP: ".$ip." (".$random_string.").";
								$log_type = "5";
								addToLogs($log_desc, $log_type);
							//Add to logs candidate IP
							
							if($rezervisanFlag == 0 AND $url_id != 2040){
								checkCandidateInputs($kandidat_id_c);
							}
							
							header("Location: " . getJoinUrlr() . "/job/thankyou/$kandidat_id_c/$kandidat_check_exists/$lg_language");
							
						}
					//PONOVNA PRIJAVA

				break;

				case "add_kandidat_new_import_lilium":
					/*  // Build POST request:
					$recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
					$recaptcha_secret = '6LfSU_cUAAAAAAZolZBCR5u5zfIZfl2roL3irH8_';
					$recaptcha_response = $_POST['recaptcha_response'];
					// Make and decode POST request:
					$recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
					$recaptcha = json_decode($recaptcha);
					
					// Take action based on the score returned:
					if ($recaptcha->score >= 0.4) { 
						*/
						$kandidat_ime = $_POST['kandidat_ime'];
						$kandidat_prezime = $_POST['kandidat_prezime'];
						$urlid = $_POST['urlid'];
						// if($urlid == 1000){
							// $kj_znanje_njemacki = $_POST['nivo_jezika'];
							// var_dump($kj_znanje_njemacki);
							// exit();
						// }
						
						$lg_language = $_POST['lg_language'];
						//$kandidat_email = $_POST['kandidat_email'];
						$dan_rodjenja = $_POST["datum_dan"];
						$mjesec_rodjenja = $_POST["datum_mjesec"];
						$godina_rodjenja = $_POST["datum_godina"];
						$mobile_phone = $_POST['kki_phone'];
						$kandidat_drzavljanstvo_vrsta = $_POST['kandidat_drzavljanstvo_vrsta'];
						$partner_token = $_POST['token'];
						
						if(isset($_REQUEST['kandidat_email'])){
							$kandidat_email = $_REQUEST['kandidat_email'];
						}else{
							$kandidat_email = null;
						}
						
						if($lg_language == "de")
							include("lang/de.php");
						else
							include("lang/bs.php");
						
						$kandidat_full_name = $kandidat_ime." ".$kandidat_prezime;
						
						$kandidat_datumrodjenja = date('Y-m-d', strtotime($godina_rodjenja."-".$mjesec_rodjenja."-".$dan_rodjenja));
						
						//Postavljanje defulatnog urlid = 1 kad nema linka i uzimanje id naloga
						$check_query2 = $db->prepare("
												SELECT lg_id, lg_url, lg_nalogid
												FROM idk_link_generator
												WHERE lg_id = :lg_id");
					
						$check_query2->execute(array(
										':lg_id' => $urlid));
					
						$num = $check_query2->rowCount();
						$rowlg = $check_query2->fetch();
							
						$lg_nalogid = $rowlg['lg_nalogid'];
						
						if($num > 0){
							$url_id = $_POST['urlid'];
						}else{
							$url_id = 1;
						}
						
						//napravi username po imenu, prezimenu i godini
						$ime_korime = strtolower($kandidat_ime);
						$prezime_korime = strtolower($kandidat_prezime);
						$imeprezime = $ime_korime.''.$prezime_korime;
						$search = array("ć", "č", "ž", "š", "đ");
						$replacement = array("c", "c", "z", "s", "dj");
						$imeprezime_korime = str_replace($search, $replacement, $imeprezime);
					
						//VIZA I TERMIN
						if(isset($_POST['kandidat_viza']))
							$kandidat_viza_da_ne = $_POST['kandidat_viza'];
						else
							$kandidat_viza_da_ne ="";
						if(isset($_POST['kandidat_termin']))
							$kandidat_termin_da_ne = $_POST['kandidat_termin'];
						else
							$kandidat_termin_da_ne ="";
						if(isset($_POST['kandidat_apliciranje']))
							$kandidat_apliciranje_da_ne = $_POST['kandidat_apliciranje'];
						else
							$kandidat_apliciranje_da_ne ="";
						
						if(isset($_POST['kandidat_iskustvo_u_struci']))
							$kandidat_iskustvo_u_struci = $_POST['kandidat_iskustvo_u_struci'];
						else
							$kandidat_iskustvo_u_struci = null;
						
						if($kandidat_viza_da_ne == 1){
							$kandidat_viza = 1;
							$kandidat_viza_vrijedi_do = $_POST['kandidat_viza_vrijedi_do'];
							$kandidat_viza_vrijedi_do = date("Y-m-d", strtotime($kandidat_viza_vrijedi_do));
							$kandidat_datum_termina = null;
							$kandidat_datum_aplikacije = null;
							$kandidat_procjenatermina = 0;
						}else{
							$kandidat_viza = 0;
							$kandidat_viza_vrijedi_do = null;
							if($kandidat_termin_da_ne == 1){
								$kandidat_datum_termina = $_POST['kandidat_termin_date'];
								$kandidat_datum_termina = date("Y-m-d", strtotime($kandidat_datum_termina));
								$kandidat_datum_aplikacije = null;
								$kandidat_procjenatermina = 0;
							}else{
								if($kandidat_apliciranje_da_ne == 1){
									$kandidat_datum_aplikacije =  $_POST['kandidat_termin_date_app'];
									$kandidat_datum_aplikacije = date("Y-m-d", strtotime($kandidat_datum_aplikacije));
									$kandidat_procjenatermina = 1;
									$kandidat_datum_termina = date('Y-m-d', strtotime("+20 months", strtotime($kandidat_datum_aplikacije)));
								}else{
									$kandidat_procjenatermina = 0;
									$kandidat_datum_termina = null;
									$kandidat_datum_aplikacije = null;
								}
							}
						}
						
						if(isset($_POST['termin_carglass'])){
							$termin_za_vizu_carglass = $_POST['termin_carglass'];
						}else{
							$termin_za_vizu_carglass = null;
						}
						if(isset($_POST['grad_za_razgovor'])){
							$grad_za_razgovor = $_POST['grad_za_razgovor'];
						}else{
							$grad_za_razgovor = null;
						}

						if(isset($_POST['kandidat_vozacka_dozvola'])){
							$kandidat_vozacka_dozvola = $_POST['kandidat_vozacka_dozvola'];
							if($kandidat_vozacka_dozvola == "Da"){
								$kandidat_vozacka_kategorija = "B";
							}else{
								$kandidat_vozacka_kategorija = null;
							}
						}else{
							$kandidat_vozacka_dozvola = null;
							$kandidat_vozacka_kategorija = null;
						}

						//BORAVAK I SKOLE
						if($kandidat_drzavljanstvo_vrsta == "EU državljanin"){
							$eu_drzava = "NN";
						}else{
							$eu_drzava = "NE";
						}
						
						$skola_id_post = $_POST['skola_naziv'];
						if($skola_id_post != "ostalo"){
							$smjer_id_post = $_POST['smjer_naziv'];
							
							if($smjer_id_post == 0){
								$skola_naziv = "nema";
							}else{
							// var_dump($smjer_id_post);
								$query_skola_naziv = $db->prepare("SELECT skola_naziv FROM idk_skole WHERE skola_id = $skola_id_post");
								$query_skola_naziv->execute();
								$row_skola_naziv = $query_skola_naziv->fetch();
								$skola_naziv = $row_skola_naziv['skola_naziv'];
								
								$query_smjer_naziv = $db->prepare("SELECT ss_naziv FROM idk_skole_smjerovi WHERE ss_id = $smjer_id_post");
								$query_smjer_naziv->execute();
								$row_smjer_naziv = $query_smjer_naziv->fetch();
								$smjer_naziv = $row_smjer_naziv['ss_naziv'];
							}

						}else{
							$skola_naziv = $_POST['skola_naziv_ru'];
							$smjer_naziv = $_POST['smjer_naziv_ru'];
							$skola_id_post = null;
							$smjer_id_post = null;
						}

						// exit();
						
						// Provjera da li postoji korisnik sa istim imenom i prezimenom u zadnjih sat vremena
						$granicno_vrijeme = date( "Y-m-d H:i:s",  strtotime("- 1 hour" ));
						$check_user = $db->prepare("
												SELECT kandidat_ime, kandidat_slika, kandidat_id, kandidat_status_messenger, kandidat_check, kandidat_visitedurl, kandidat_status, kandidat_vozacka_kategorija
												FROM idk_kandidati
												WHERE kandidat_ime = :kandidat_ime AND kandidat_prezime = :kandidat_prezime AND kandidat_mobitel = :kandidat_mobitel AND kandidat_status != 3");
					
						$check_user->execute(array(
										':kandidat_ime' => $kandidat_ime,
										':kandidat_prezime' => $kandidat_prezime,
										':kandidat_mobitel' => $mobile_phone
										));
					
						$number_of_rows_user = $check_user->rowCount();
						$rows_user = $check_user->fetch();
						$check_slika = $rows_user['kandidat_slika'];
						$kandidat_id_c = $rows_user['kandidat_id'];
						$kandidat_status_messenger = $rows_user['kandidat_status_messenger'];
						$kandidat_check_exists = $rows_user['kandidat_check'];
						$stari_visitedurl = $rows_user['kandidat_visitedurl'];
						$kandidat_status = $rows_user['kandidat_status'];
						
						$kandidat_group = $_POST['kandidat_prijava_na'];
						$group_title_query = $db->prepare("
										SELECT kg_id, kg_title
										FROM idk_kandidati_grupe
										WHERE kg_id = :kg_id
										");
				
						$group_title_query->execute(array(
							":kg_id" => $kandidat_group
						));
						$row_kg_title = $group_title_query->fetch();
						$kandidat_prijava_na = $row_kg_title['kg_title'];
						
						$kandidat_datetime = date('Y-m-d H:i:s');
						$kandidat_check = md5(uniqid(rand(), true));
						
						if($number_of_rows_user == 0){
							
							// nisu ulazile duple prijave zbog korisnickog imena koji je kombinacija imenaprezimenadatumarodjenja, pa sam dodao
							//jos jedan token od 3 broja na to da moze ulaziti
							$random_token = rand(100, 999);
							$kandidat_korisnickoime_uf = $imeprezime_korime.''.$random_token;
							$kandidat_korisnickoime = str_replace(' ', '', strtolower($kandidat_korisnickoime_uf));
							$kandidat_password = MD5($kandidat_korisnickoime);
							
							//izbaceno provjeravanje da li ima kandidata sa ovakvim $kandidat_korisnickoime
							
							// $kandidat_mjestorodjenja = $_POST['kandidat_mjestorodjenja'];
							// $kandidat_drzavarodjenja = $_POST['kandidat_drzavarodjenja'];
							$kandidat_visitedurl = $_POST['kandidat_visitedurl'];
							
							/*
							$kandidat_datum_termina = $_POST['kandidat_termin_date'];
							$kandidat_datum_aplikacije = $_POST['kandidat_termin_date_app'];
							$datum_aplikacije = date("Y-m-d", strtotime($kandidat_datum_aplikacije));
							*/
							
							$kandidat_status = 0;
							
							if($lg_language == "bs" or $lg_language == "de")
								$kandidat_status_messenger = 1;
							else
								$kandidat_status_messenger = 4;
						
							//Upload and save kandidat_slika
							/*
							if($_FILES['kandidat_slika'] !== null){
								if($_FILES['kandidat_slika']['size'] !== 0) {
									$kandidat_slika = $_FILES['kandidat_slika'];
						
									//File properties
									$file_name = $kandidat_slika['name'];
									$file_tmp = $kandidat_slika['tmp_name'];
									$file_size = $kandidat_slika['size'];
									$file_error = $kandidat_slika['error'];
						
									//File extension
									$file_ext = explode('.', $file_name);
									$file_ext = strtolower(end($file_ext));
						
									$allowed = array('jpg', 'png');
						
									if(in_array($file_ext, $allowed)) {
						
										$kandidat_slika_final = uniqid() . '.' . $file_ext;
										$file_destination = 'files/kandidati/' . $kandidat_slika_final;
						
										if(move_uploaded_file($file_tmp, $file_destination)) {
						
											$path_to_image_directory = "files/kandidati/";
											$final_width_of_image = 660;
											ini_set('memory_limit', '-1');
											if(preg_match('/[.](jpg)$/', $kandidat_slika_final)) {
												$im = imagecreatefromjpeg($path_to_image_directory . $kandidat_slika_final);
											} else if (preg_match('/[.](png)$/', $kandidat_slika_final)) {
												$im = imagecreatefrompng($path_to_image_directory . $kandidat_slika_final);
											}
						
											$ox = imagesx($im);
											$oy = imagesy($im);
											$nx = $final_width_of_image;
											$ny = floor($oy * ($final_width_of_image / $ox));
											$nm = imagecreatetruecolor($nx, $ny);
						
											imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);
											imagejpeg($nm, $path_to_image_directory . $kandidat_slika_final);
						
										}
									}else{
										$kandidat_slika_final = "none";
									}
								}else{
									$kandidat_slika_final = "none";
								}
							}else*/
								$kandidat_slika_final = "none";
							
							//ADD PARTNER DATA
								
							if($partner_token != null){
								$partner_query = $db->prepare("
												SELECT jp_id, jp_fcmtoken, jp_position, jp_imeprezime
												FROM idk_jobstep_partners
												WHERE jp_mailconfirmation_token = :jp_mailconfirmation_token
												");
						
									$partner_query->execute(array(
										":jp_mailconfirmation_token" => $partner_token
								));
						
								$row_partner = $partner_query->fetch();
								$partner_id = $row_partner['jp_id'];
								$partner_position = $row_partner['jp_position'];
								$jp_fcmtoken = $row_partner['jp_fcmtoken'];
								$partner_ime = $row_partner['jp_imeprezime'];
								$log_poruka = "Novi kandidat ".$kandidat_ime." je prijavljen preko vašeg linka.";
								// send_notification_partnerapp($jp_fcmtoken, $partner_ime, $log_poruka);
								$kandidat_porijeklo = 6;
							}else{
								$partner_id = null;
								$partner_position = null;
								$kandidat_porijeklo = 0;
							}
							
							//Add user to db
							$query = $db->prepare("
											INSERT INTO idk_kandidati
												( kandidat_check, kandidat_ime, kandidat_prezime, kandidat_drzavljanstvo_vrsta, kandidat_mobitel, kandidat_password, kandidat_slika, kandidat_status, kandidat_status_messenger, kandidat_datetime, kandidat_korisnickoime, kandidat_datumrodjenja, kandidat_visitedurl, kandidat_prijava_na, datum_termina, datum_aplikacije, kandidat_group, kandidat_procjenatermina, kandidat_viza, kandidat_viza_vrijedi_do, kandidat_status_prijave, kandidat_partnerid, kandidat_partner_status, kandidat_porijeklo, kandidat_iskustvo_u_struci, kandidat_termin_za_vizu, kandidat_vozacka_dozvola, kandidat_vozacka_kategorija)
											VALUES
												(:kandidat_check,:kandidat_ime,:kandidat_prezime,:kandidat_drzavljanstvo_vrsta,:kandidat_mobitel, :kandidat_password, :kandidat_slika, :kandidat_status, :kandidat_status_messenger, :kandidat_datetime, :kandidat_korisnickoime, :kandidat_datumrodjenja, :kandidat_visitedurl, :kandidat_prijava_na, :datum_termina, :datum_aplikacije, :kandidat_group, :kandidat_procjenatermina, :kandidat_viza, :kandidat_viza_vrijedi_do, :kandidat_status_prijave, :kandidat_partnerid, :kandidat_partner_status, :kandidat_porijeklo, :kandidat_iskustvo_u_struci, :kandidat_termin_za_vizu, :kandidat_vozacka_dozvola, :kandidat_vozacka_kategorija)");
					
							$query->execute(array(
										':kandidat_check' => $kandidat_check,
										':kandidat_ime' => $kandidat_ime,
										':kandidat_prezime' => $kandidat_prezime,
										':kandidat_drzavljanstvo_vrsta' => $kandidat_drzavljanstvo_vrsta,
										':kandidat_mobitel' => $mobile_phone,
										':kandidat_password' => $kandidat_password,
										':kandidat_slika' => $kandidat_slika_final,
										':kandidat_status' => $kandidat_status,
										':kandidat_status_messenger' => $kandidat_status_messenger,
										':kandidat_datetime' => $kandidat_datetime,
										':kandidat_korisnickoime' => $kandidat_korisnickoime,
										':kandidat_datumrodjenja' => $kandidat_datumrodjenja,
										':kandidat_visitedurl' => $url_id,
										':kandidat_prijava_na' => $kandidat_prijava_na,
										':datum_termina' => $kandidat_datum_termina,
										':datum_aplikacije' => $kandidat_datum_aplikacije,
										':kandidat_group' => $kandidat_group,
										':kandidat_procjenatermina' => $kandidat_procjenatermina,
										':kandidat_viza' => $kandidat_viza,
										':kandidat_viza_vrijedi_do' => $kandidat_viza_vrijedi_do,
										':kandidat_status_prijave' => 2,
										':kandidat_partnerid' => $partner_id,
										':kandidat_partner_status' => $partner_position,
										':kandidat_porijeklo' => $kandidat_porijeklo,
										':kandidat_iskustvo_u_struci' => $kandidat_iskustvo_u_struci,
										':kandidat_termin_za_vizu' => $termin_za_vizu_carglass,
										':kandidat_vozacka_dozvola' => $kandidat_vozacka_dozvola,
										':kandidat_vozacka_kategorija' => $kandidat_vozacka_kategorija
										));
										
							$kandidat_id = $db->lastInsertId();
							
							$query_log_status = $db->prepare("
									INSERT INTO idk_log_kandidat_statusi
										(lks_kandidat_id, lks_status_obrade, lks_status_messenger, lks_datetime)
									VALUES
										(:lks_kandidat_id, :lks_status_obrade, :lks_status_messenger, :lks_datetime)
							");
							
							$query_log_status->execute(array(
									':lks_kandidat_id' => $kandidat_id,
									':lks_status_obrade' => $kandidat_status,
									':lks_status_messenger' => $kandidat_status_messenger,
									':lks_datetime' => $kandidat_datetime
							));

							if($grad_za_razgovor != null){
								$note_group = 2;
								$note_dataid = $kandidat_id;
								$note_datetime = date('Y-m-d H:i:s');
								
								$note_txt_cert = "Izabrani grad za razgovor: ".$grad_za_razgovor;
								
								$query_biljeske = $db->prepare("
												INSERT INTO idk_notes
													(note_txt, note_datetime, note_group, note_dataid, note_employeeid)
												VALUES
													(:note_txt, :note_datetime, :note_group, :note_dataid, :note_employeeid)");
			
								$query_biljeske->execute(array(
											':note_txt' => $note_txt_cert,
											':note_datetime' => $note_datetime,
											':note_group' => $note_group,
											':note_dataid' => $note_dataid,
											':note_employeeid' => 67));
							}

							if(isset($_POST['nivo_jezika'])){
								
								$kj_naziv_njemacki = "Njemački";
								$kj_znanje_njemacki = $_POST['nivo_jezika'];
								// Add language knowlege
								$query_njem = $db->prepare("
												INSERT INTO idk_kandidat_jezici
													(kj_naziv, kj_slusanje, kj_citanje, kj_govorna_interakcija, kj_govorna_produkcija, kj_pisanje, kj_kandidatid)
												VALUES
													(:kj_naziv, :kj_slusanje, :kj_citanje, :kj_govorna_interakcija, :kj_govorna_produkcija, :kj_pisanje, :kj_kandidatid)");
						
								$query_njem->execute(array(
											':kj_naziv' => $kj_naziv_njemacki,
											':kj_slusanje' => $kj_znanje_njemacki,
											':kj_citanje' => $kj_znanje_njemacki,
											':kj_govorna_interakcija' => $kj_znanje_njemacki,
											':kj_govorna_produkcija' => $kj_znanje_njemacki,
											':kj_pisanje' => $kj_znanje_njemacki,
											':kj_kandidatid' => $kandidat_id));
							}
							
							if($urlid == 446){
								
								$get_project = $db->prepare("
														SELECT project_id
														FROM idk_projects
														WHERE project_nalogid = 117 AND (project_name LIKE '%$kandidat_prijava_na%') ");
							
								$get_project->execute();
							
								$gp_row = $get_project->fetch();
								$projectid = $gp_row['project_id'];
								
								$check_project = $db->prepare("
														SELECT pk_projectid
														FROM idk_project_kandidati
														WHERE pk_projectid = :pk_projectid AND pk_kandidatid = :pk_kandidatid");
							
								$check_project->execute(array(
													':pk_projectid' => $projectid,
													':pk_kandidatid' => $kandidat_id
								));
								if($check_project->rowCount() == 0){
									
									$query_project = $db->prepare("
													INSERT INTO idk_project_kandidati
														(pk_projectid, pk_kandidatid)
													VALUES
														(:pk_projectid, :pk_kandidatid)");
						
									$query_project->execute(array(
													':pk_projectid' => $projectid,
													':pk_kandidatid' => $kandidat_id));
								}
							}
							
							if($kandidat_drzavljanstvo_vrsta == "EU državljanin"){
								$kandidat_drzavljanstvo_vrsta_txt = "EU";
								
								//IF USER IS EU - Transfer him into EU kandidati project within the order
								$nalog_query = $db->prepare("
														SELECT project_id
														FROM idk_projects
														WHERE project_nalogid = :project_nalogid AND project_eukandidati = 1");
							
								$nalog_query->execute(array(
												':project_nalogid' => $lg_nalogid));
							
								$nalogrow = $nalog_query->fetch();
								
								$project_id = $nalogrow['project_id'];
								
								$query_project = $db->prepare("
												INSERT INTO idk_project_kandidati
													(pk_projectid, pk_kandidatid)
												VALUES
													(:pk_projectid, :pk_kandidatid)");

								$query_project->execute(array(
												':pk_projectid' => $project_id,
												':pk_kandidatid' => $kandidat_id));						
								
							}else{
								$kandidat_drzavljanstvo_vrsta_txt = "NONEU";
								$mjesectermina = date("m",strtotime($kandidat_datum_termina));
								$godinatermina = date("Y",strtotime($kandidat_datum_termina));
								
								//Provjeri da li postoji mjesec termina unutar projekata
								$check_termin_month = $db->prepare("
														SELECT project_id, project_name
														FROM idk_projects
														WHERE project_nalogid = :project_nalogid AND $mjesectermina BETWEEN MONTH(project_datumtermina) AND MONTH(project_datumterminado) AND YEAR(project_datumtermina) = $godinatermina");
							
								$check_termin_month->execute(array(
												':project_nalogid' => $lg_nalogid));
							
								$terminmonthcount = $check_termin_month->rowcount();						
								$terminmonth = $check_termin_month->fetch();	
								$project_id_termin = $terminmonth['project_id'];					
								$project_name = $terminmonth['project_name'];						
								
								if($terminmonthcount == 0){
									
									//IF USER IS NON EU - Transfer him into Prijave project within the order
									$nalog_query = $db->prepare("
															SELECT project_id
															FROM idk_projects
															WHERE project_nalogid = :project_nalogid AND (project_name LIKE '%Prijave%' OR project_name LIKE '%Prijava%') ");
								
									$nalog_query->execute(array(
													':project_nalogid' => $lg_nalogid));
								
									$nalogrow = $nalog_query->fetch();
									if(!$nalogrow){
										//sta raditi ako nema projekta "PRIJAVA"
										//IDU U NOVE PROJEKTI ZA VIBER NALOG
									}else{
										
										$project_id = $nalogrow['project_id'];	
										
										$query_project = $db->prepare("
														INSERT INTO idk_project_kandidati
															(pk_projectid, pk_kandidatid)
														VALUES
															(:pk_projectid, :pk_kandidatid)");
							
										$query_project->execute(array(
														':pk_projectid' => $project_id,
														':pk_kandidatid' => $kandidat_id));	
										
										addToLogsStatusPrijave(NULL, $project_id, 2, $kandidat_id, 3);
									}
									
								}else{
									$query_project = $db->prepare("
													INSERT INTO idk_project_kandidati
														(pk_projectid, pk_kandidatid)
													VALUES
														(:pk_projectid, :pk_kandidatid)");
			
									$query_project->execute(array(
													':pk_projectid' => $project_id_termin,
													':pk_kandidatid' => $kandidat_id));	
								}
							}
							
							//INSERT SKOLE
							if($skola_naziv != "nema" AND $skola_naziv != null){
								$insert_skole = $db->prepare("
												INSERT INTO idk_kandidat_edukacija
													(ke_naziv_kvalifikacije, ke_naziv, ke_vrsta_obrazovanja, ke_kandidat_id, ke_skola_id, ke_smjer_id)
												VALUES
													(:ke_naziv_kvalifikacije,:ke_naziv,:ke_vrsta_obrazovanja,:ke_kandidat_id,:ke_skola_id,:ke_smjer_id)");
						
								$insert_skole->execute(array(
											':ke_naziv_kvalifikacije' => $smjer_naziv,
											':ke_naziv' => $skola_naziv,
											':ke_vrsta_obrazovanja' => "srednje",
											':ke_skola_id' => $skola_id_post,
											':ke_smjer_id' => $smjer_id_post,
											':ke_kandidat_id' => $kandidat_id));
							}
							
							$kki_grupa = 1;
							$kki_naziv = "Mobilni";

							//Add mobilni to db
							$query_mob = $db->prepare("
											INSERT INTO idk_kandidat_kontakt_info
												(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id)
											VALUES
												(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id)");

							$query_mob->execute(array(
										':kki_grupa' => $kki_grupa,
										':kki_naziv' => $kki_naziv,
										':kki_podatak' => $mobile_phone,
										':kki_kandidat_id' => $kandidat_id));
							
							$kki_grupa_e = 2;
							$kki_naziv_e = "E-mail";
							if($kandidat_email != null){
								$kki_podatak_e = $_POST['kandidat_email'];

								//Add kontakt info to db
								$query_email = $db->prepare("
												INSERT INTO idk_kandidat_kontakt_info
													(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id)
												VALUES
													(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id)");

								$query_email->execute(array(
											':kki_grupa' => $kki_grupa_e,
											':kki_naziv' => $kki_naziv_e,
											':kki_podatak' => $kki_podatak_e,
											':kki_kandidat_id' => $kandidat_id));
							}
							
							//Add to table users (chatbot)
							$random_string = generateRandomString();
							$options = [
								'cost' => 10,
							];
							$random_password = password_hash($random_string, PASSWORD_BCRYPT, $options);
							
							//Add to logs candidate IP	
							$date_time_ip = date("F j, Y, g:i T");
							if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
								$ip = $_SERVER['HTTP_CLIENT_IP'];
							} elseif (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
								$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
							} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
								$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
							} else {
								$ip = $_SERVER['REMOTE_ADDR'];
							}
							$log_desc = "Kandidat: " . $kandidat_ime . " " . $kandidat_prezime . "(".$kandidat_id."). ".$date_time_ip."; IP: ".$ip." (".$random_string.")";
							$log_type = "5";
							addToLogs($log_desc, $log_type);
							
							// DE i IT kandidate slati na korak 2
							if($lg_language == "it"){
								
							}else{
								
								if($urlid == 0){
									
								}else{
									
									$characters = '0123456789';
									$charactersLength = strlen($characters);
									$randomString = '';
									for ($i = 0; $i < 5; $i++) {
										$randomString .= $characters[rand(0, $charactersLength - 1)];
									}
									$bot_koriscnicko_ime = $kandidat_ime.$randomString;
									
									$query_user = $db->prepare("
												INSERT INTO users
													(phone, name, nalog_id, email, password, kandidat_id)
												VALUES
													(:phone, :name, :nalog_id, :email, :password, :kandidat_id)");

									$query_user->execute(array(
												':phone' => $mobile_phone,
												':name' => $kandidat_full_name,
												':nalog_id' => $lg_nalogid,
												':email' => $bot_koriscnicko_ime,
												':password' => $random_password,
												':kandidat_id' => $kandidat_id));
									
									
									$phone_f = str_replace("+", '', $mobile_phone);
									/*
									sendSmsToCandidate1($random_string, $phone_f, $kandidat_prijava_na);
									sleep(1);  // Seconds
									sendSmsToCandidate2($random_string, $phone_f, $kandidat_prijava_na);
									sleep(1);  // Seconds
									sendSmsToCandidate3($random_string, $phone_f, $kandidat_email);
									sleep(1);  // Seconds
									sendSmsToCandidate4($random_string, $phone_f, $kandidat_email);
									*/
									
									
									$link_dload = "https://crm.job-step.com/download";
									$link_uputs = "https://bit.ly/3V177tF";

									$kandidat_full_name = getCandidateFullnameR($kandidat_id);

									$to_send_viber_poruka_1 = $kandidat_full_name.$text_viber_new_prva_poruka; 
									$to_send_sms_poruka_1 	= $kandidat_full_name.$text_sms_new_prva_poruka; 

									$to_send_viber_poruka_2 = $text_viber_new_druga_poruka;
									$to_send_sms_poruka_2 	= $text_sms_new_druga_poruka." ".$link_dload;

									$to_send_viber_poruka_3 = $text_viber_new_treca_poruka_1.'\n\n'.$text_viber_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_viber_new_treca_poruka_3.$random_string;
									$to_send_sms_poruka_3 	= $text_sms_new_treca_poruka_1.'\n\n'.$text_sms_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_sms_new_treca_poruka_3.$random_string;
									// var_dump($to_send_viber_poruka_3);
									// var_dump($to_send_sms_poruka_3);
									// exit();
									viberPrijava1($phone_f, $to_send_sms_poruka_1, $to_send_viber_poruka_1);
									sleep(1);
									viberPrijava2($phone_f, $to_send_sms_poruka_2, $to_send_viber_poruka_2, $text_btn_new_druga_poruka, $link_dload);
									sleep(1);
									viberPrijava1($phone_f, $to_send_sms_poruka_3, $to_send_viber_poruka_3);
									sleep(1);

									if($lg_language == "bs"){
										$to_send_viber_poruka_4 = $text_viber_new_cetvrta_poruka;
										$to_send_sms_poruka_4 	= $text_sms_new_cetvrta_poruka." ".$link_uputs;
										
										viberPrijava2($phone_f, $to_send_sms_poruka_4, $to_send_viber_poruka_4, $text_btn_new_cetvrta_poruka, $link_uputs);
									}
									if($kandidat_email != null)
										sendCandidateMessengerMail($random_string, $kandidat_email, $bot_koriscnicko_ime);
									checkCandidateInputs($kandidat_id);
									if($urlid == 530){
										
									}else{
										
									}
								}
							}
							notifForOnlneRegister($kandidat_id, $kandidat_check);
							
						}else{
							$kandidat_group = $_POST['kandidat_prijava_na'];
							$group_title_query = $db->prepare("
											SELECT kg_id, kg_title
											FROM idk_kandidati_grupe
											WHERE kg_id = :kg_id
											");
					
							$group_title_query->execute(array(
								":kg_id" => $kandidat_group
							));
							$row_kg_title = $group_title_query->fetch();
							$kandidat_prijava_na = $row_kg_title['kg_title'];
							$kandidat_vozacka_kategorija_c = $rows_user['kandidat_vozacka_kategorija'];
							
							//Ako kandidat vec ima unesenu kategoriju vozacke onda za update ide to sto ima uneseno
							//Ako nema onda kupimo sa prijave, a ako je na prijavi null i ovdje ce upasti null
							//ako je na prijavi ista uneseno onda ce biti uneseno samo B za kategoriju

							if($kandidat_vozacka_kategorija_c != null){
								$kandidat_vozacka_dozvola_for_update = "Da";
								$kandidat_vozacka_kategorija_for_update = $kandidat_vozacka_kategorija_c;
							}else{
								$kandidat_vozacka_dozvola_for_update = $kandidat_vozacka_dozvola;
								$kandidat_vozacka_kategorija_for_update = $kandidat_vozacka_kategorija;
							}
							/*if($check_slika == "none" or $check_slika == "none.jpg"){
								//Upload and save kandidat_slika
								if($_FILES['kandidat_slika'] !== null){
									if($_FILES['kandidat_slika']['size'] !== 0) {
										$kandidat_slika = $_FILES['kandidat_slika'];
							
										//File properties
										$file_name = $kandidat_slika['name'];
										$file_tmp = $kandidat_slika['tmp_name'];
										$file_size = $kandidat_slika['size'];
										$file_error = $kandidat_slika['error'];
							
										//File extension
										$file_ext = explode('.', $file_name);
										$file_ext = strtolower(end($file_ext));
							
										$allowed = array('jpg', 'png');
							
										if(in_array($file_ext, $allowed)) {
							
											$kandidat_slika_final = uniqid() . '.' . $file_ext;
											$file_destination = 'files/kandidati/' . $kandidat_slika_final;
							
											if(move_uploaded_file($file_tmp, $file_destination)) {
							
												$path_to_image_directory = "files/kandidati/";
												$final_width_of_image = 660;
							
												if(preg_match('/[.](jpg)$/', $kandidat_slika_final)) {
													$im = imagecreatefromjpeg($path_to_image_directory . $kandidat_slika_final);
												} else if (preg_match('/[.](png)$/', $kandidat_slika_final)) {
													$im = imagecreatefrompng($path_to_image_directory . $kandidat_slika_final);
												}
							
												$ox = imagesx($im);
												$oy = imagesy($im);
												$nx = $final_width_of_image;
												$ny = floor($oy * ($final_width_of_image / $ox));
												$nm = imagecreatetruecolor($nx, $ny);
							
												imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);
												imagejpeg($nm, $path_to_image_directory . $kandidat_slika_final);
							
											}
										}
									}else{
										$kandidat_slika_final = "none";
									}
								}else
									$kandidat_slika_final = "none";
							}else
							*/
								$kandidat_slika_final = "none";
							
							// var_dump($kandidat_id_c);
							// var_dump($kandidat_group);
							// exit();
							
							$check_messenger = $db->prepare("
													SELECT id, email, phone
													FROM users
													WHERE kandidat_id = :kandidat_id ");
						
							$check_messenger->execute(array(
											':kandidat_id' => $kandidat_id_c
											));
						
							$nr_of_rows_mess = $check_messenger->rowCount();
							if($nr_of_rows_mess == 0){
								$kandidat_status_messenger = 1;
							}else{
								
							}
							
							//ADD PARTNER DATA
								
							if($partner_token != null){
								$partner_query = $db->prepare("
												SELECT jp_id, jp_fcmtoken, jp_position, jp_imeprezime
												FROM idk_jobstep_partners
												WHERE jp_mailconfirmation_token = :jp_mailconfirmation_token
												");
						
									$partner_query->execute(array(
										":jp_mailconfirmation_token" => $partner_token
								));
						
								$row_partner = $partner_query->fetch();
								$partner_id = $row_partner['jp_id'];
								$partner_position = $row_partner['jp_position'];
								$jp_fcmtoken = $row_partner['jp_fcmtoken'];
								$partner_ime = $row_partner['jp_imeprezime'];
								$log_poruka = "Novi kandidat ".$kandidat_ime." je prijavljen preko vašeg linka.";
								// send_notification_partnerapp($jp_fcmtoken, $partner_ime, $log_poruka);
								$kandidat_porijeklo = 7;
							}else{
								$partner_id = null;
								$partner_position = null;
								$kandidat_porijeklo = 0;
							}
							
							//Update user
							$update_user = $db->prepare("
											UPDATE idk_kandidati
											SET kandidat_drzavljanstvo_vrsta = :kandidat_drzavljanstvo_vrsta, kandidat_mobitel= :kandidat_mobitel, kandidat_slika = :kandidat_slika, kandidat_status_messenger = :kandidat_status_messenger, kandidat_datumrodjenja = :kandidat_datumrodjenja, datum_termina = :datum_termina, datum_aplikacije = :datum_aplikacije, kandidat_procjenatermina = :kandidat_procjenatermina, kandidat_viza = :kandidat_viza, kandidat_viza_vrijedi_do = :kandidat_viza_vrijedi_do, boravak_eu = :boravak_eu, kandidat_visitedurl = :kandidat_visitedurl, kandidat_prijava_na = :kandidat_prijava_na, kandidat_partnerid = :kandidat_partnerid, kandidat_partner_status = :kandidat_partner_status, kandidat_porijeklo = :kandidat_porijeklo, kandidat_iskustvo_u_struci = :kandidat_iskustvo_u_struci, kandidat_termin_za_vizu = :kandidat_termin_za_vizu, kandidat_vozacka_dozvola = :kandidat_vozacka_dozvola, kandidat_vozacka_kategorija = :kandidat_vozacka_kategorija
											WHERE kandidat_id = :kandidat_id
											");
					
							$update_user->execute(array(
										':kandidat_id' => $kandidat_id_c,
										':kandidat_drzavljanstvo_vrsta' => $kandidat_drzavljanstvo_vrsta,
										':kandidat_mobitel' => $mobile_phone,
										':kandidat_slika' => $kandidat_slika_final,
										':kandidat_status_messenger' => $kandidat_status_messenger,
										':kandidat_datumrodjenja' => $kandidat_datumrodjenja,
										':datum_termina' => $kandidat_datum_termina,
										':datum_aplikacije' => $kandidat_datum_aplikacije,
										':kandidat_procjenatermina' => $kandidat_procjenatermina,
										':kandidat_viza' => $kandidat_viza,
										':kandidat_viza_vrijedi_do' => $kandidat_viza_vrijedi_do,
										':boravak_eu' => $eu_drzava,
										':kandidat_visitedurl' => $url_id,
										':kandidat_prijava_na' => $kandidat_prijava_na,
										':kandidat_partnerid' => $partner_id,
										':kandidat_partner_status' => $partner_position,
										':kandidat_porijeklo' => $kandidat_porijeklo,
										':kandidat_iskustvo_u_struci' => $kandidat_iskustvo_u_struci,
										':kandidat_termin_za_vizu' => $termin_za_vizu_carglass,
										':kandidat_vozacka_dozvola' => $kandidat_vozacka_dozvola_for_update,
										':kandidat_vozacka_kategorija' => $kandidat_vozacka_kategorija_for_update
										));
							
							if($grad_za_razgovor != null){
								$note_group = 2;
								$note_dataid = $kandidat_id_c;
								$note_datetime = date('Y-m-d H:i:s');
								
								$note_txt_cert = "Izabrani grad za razgovor: ".$grad_za_razgovor;
								
								$query_biljeske = $db->prepare("
												INSERT INTO idk_notes
													(note_txt, note_datetime, note_group, note_dataid, note_employeeid)
												VALUES
													(:note_txt, :note_datetime, :note_group, :note_dataid, :note_employeeid)");
			
								$query_biljeske->execute(array(
											':note_txt' => $note_txt_cert,
											':note_datetime' => $note_datetime,
											':note_group' => $note_group,
											':note_dataid' => $note_dataid,
											':note_employeeid' => 67));
							}
							//INSERT/UPDATE JEZIKA
							if(isset($_POST['nivo_jezika'])){
								$kj_naziv_njemacki = "Njemački";
								$kj_znanje_njemacki = $_POST['nivo_jezika'];
								$query_check = $db->prepare("SELECT
																kj_id
															FROM
																idk_kandidat_jezici
															WHERE
																kj_naziv = :kj_naziv
															AND
																kj_kandidatid = :kandidat_id
															");
								$query_check->execute(array(
									":kandidat_id" => $kandidat_id_c,
									":kj_naziv" => $kj_naziv_njemacki
								));
								$count_jezik = $query_check->rowCount();
								$result_query_check = $query_check->fetch();
								if($count_jezik > 0){
									$kj_id = $result_query_check['kj_id'];
									//UPDATE
									$query_njem = $db->prepare("
										UPDATE idk_kandidat_jezici
										SET kj_naziv = :kj_naziv, kj_citanje = :kj_citanje, kj_slusanje = :kj_slusanje, kj_citanje = :kj_citanje, kj_govorna_interakcija = :kj_govorna_interakcija, kj_govorna_produkcija = :kj_govorna_produkcija, kj_pisanje = :kj_pisanje
										WHERE kj_kandidatid = :kj_kandidatid
										");
									$query_njem->execute(array(
												':kj_naziv' => $kj_naziv_njemacki,
												':kj_slusanje' => $kj_znanje_njemacki,
												':kj_citanje' => $kj_znanje_njemacki,
												':kj_govorna_interakcija' => $kj_znanje_njemacki,
												':kj_govorna_produkcija' => $kj_znanje_njemacki,
												':kj_pisanje' => $kj_znanje_njemacki,
												':kj_kandidatid' => $kandidat_id_c));		
								} else {
										//INSERT
									
									// Add language knowlege
									$query_njem = $db->prepare("
													INSERT INTO idk_kandidat_jezici
														(kj_naziv, kj_slusanje, kj_citanje, kj_govorna_interakcija, kj_govorna_produkcija, kj_pisanje, kj_kandidatid)
													VALUES
														(:kj_naziv, :kj_slusanje, :kj_citanje, :kj_govorna_interakcija, :kj_govorna_produkcija, :kj_pisanje, :kj_kandidatid)");
							
									$query_njem->execute(array(
												':kj_naziv' => $kj_naziv_njemacki,
												':kj_slusanje' => $kj_znanje_njemacki,
												':kj_citanje' => $kj_znanje_njemacki,
												':kj_govorna_interakcija' => $kj_znanje_njemacki,
												':kj_govorna_produkcija' => $kj_znanje_njemacki,
												':kj_pisanje' => $kj_znanje_njemacki,
												':kj_kandidatid' => $kandidat_id_c));
								}
							}
							//INSERT INTO KANDIDAT LOG STATUSI
							
							$query_log_status = $db->prepare("
									INSERT INTO idk_log_kandidat_statusi
										(lks_kandidat_id, lks_status_obrade, lks_status_messenger, lks_datetime)
									VALUES
										(:lks_kandidat_id, :lks_status_obrade, :lks_status_messenger, :lks_datetime)
							");
							
							$query_log_status->execute(array(
									':lks_kandidat_id' => $kandidat_id_c,
									':lks_status_obrade' => 9,
									':lks_status_messenger' => 0,
									':lks_datetime' => $kandidat_datetime
							));
							
							if($urlid == 446){
								
								$get_project = $db->prepare("
														SELECT project_id
														FROM idk_projects
														WHERE project_nalogid = 117 AND (project_name LIKE '%$kandidat_prijava_na%') ");
							
								$get_project->execute();
							
								$gp_row = $get_project->fetch();
								$projectid = $gp_row['project_id'];
								
								$check_project = $db->prepare("
														SELECT pk_projectid
														FROM idk_project_kandidati
														WHERE pk_projectid = :pk_projectid AND pk_kandidatid = :pk_kandidatid");
							
								$check_project->execute(array(
													':pk_projectid' => $projectid,
													':pk_kandidatid' => $kandidat_id_c
								));
								if($check_project->rowCount() == 0){
									
									$query_project = $db->prepare("
													INSERT INTO idk_project_kandidati
														(pk_projectid, pk_kandidatid)
													VALUES
														(:pk_projectid, :pk_kandidatid)");
						
									$query_project->execute(array(
													':pk_projectid' => $projectid,
													':pk_kandidatid' => $kandidat_id_c));
								}
							}
							
							//INSERT INTO PROJEKAT
							
							$rezervisanFlag = 0;
							
							$nalog_query = $db->prepare("
													SELECT project_id
													FROM idk_projects
													WHERE project_nalogid = :project_nalogid AND (project_name LIKE '%Prijave%' OR project_name LIKE '%Prijava%') ");
						
							$nalog_query->execute(array(
											':project_nalogid' => $lg_nalogid));
						
							$nalogrow = $nalog_query->fetch();
							if(!$nalogrow){
								//sta raditi ako nema projekta "PRIJAVA"
								//IDU U NOVE PROJEKTI ZA VIBER NALOG
								
							}else{
							
								$project_id = $nalogrow['project_id'];	
								$check_project = $db->prepare("
														SELECT pk_projectid
														FROM idk_project_kandidati
														WHERE pk_projectid = :pk_projectid AND pk_kandidatid = :pk_kandidatid");
							
								$check_project->execute(array(
													':pk_projectid' => $project_id,
													':pk_kandidatid' => $kandidat_id_c
								));
								if($check_project->rowCount() == 0){
									
									//Provjera rezervisanosti START
									
									$queryPrijavaNalog = $db->prepare("
										SELECT 
											kandidat_nalog_id, kandidat_status_prijave
										FROM 
											idk_kandidati
										WHERE 
											kandidat_id = :kandidat_id
									");
									$queryPrijavaNalog->execute(array(
										':kandidat_id' => $kandidat_id_c
									));
									$rowPrijavaNalog = $queryPrijavaNalog->fetch();
									$nalogIdPN = intval($rowPrijavaNalog["kandidat_nalog_id"]);
									$statusPrijavePN = intval($rowPrijavaNalog["kandidat_status_prijave"]);
									
									$queryProjekti = $db->prepare("
										SELECT 
											count(pk.pk_id) AS brojac
										FROM 
											idk_project_kandidati pk
										JOIN 
											idk_projects p
										ON 
											pk.pk_projectid = p.project_id
										WHERE 
											pk.pk_kandidatid = :pk_kandidatid
											AND 
											(
												project_name LIKE '%Intervju%' 
												OR 
												project_name LIKE '%Obrađeno%' 
												OR 
												project_name LIKE '%Završeni kandidati%' 
												OR 
												project_name LIKE '%BOT - ispunjava uslove%' 
												OR 
												project_name LIKE '%Baza - odgovara za nalog%'
												OR 
												project_name LIKE '%Casting%' 
												OR 
												project_name LIKE '%Ugovor%'
											)
											AND p.project_nalogid != 44
									");
									$queryProjekti->execute(array(
										':pk_kandidatid' => $kandidat_id_c
									));
									$rowProjekti = $queryProjekti->fetch();
									$brojacUProjektuP = intval($rowProjekti["brojac"]);
									
									if($nalogIdPN != 0 OR $brojacUProjektuP != 0 OR $statusPrijavePN == 4){
										$rezervisanFlag = 1;
									}
									
									if($rezervisanFlag == 0){
										$query_project = $db->prepare("
														INSERT INTO idk_project_kandidati
															(pk_projectid, pk_kandidatid)
														VALUES
															(:pk_projectid, :pk_kandidatid)");
							
										$query_project->execute(array(
														':pk_projectid' => $project_id,
														':pk_kandidatid' => $kandidat_id_c));
										addToLogsStatusPrijave(NULL, $project_id, 2, $kandidat_id_c, 3);
									}else{
										pushToProjectCandidateQueue($kandidat_id_c, $project_id);
									}
									//Provjera rezervisanosti END 
								}
							}
							
							//INSERT SKOLE
							if($skola_naziv != "nema" AND $skola_naziv != null){
								$insert_skole = $db->prepare("
												INSERT INTO idk_kandidat_edukacija
													(ke_naziv_kvalifikacije, ke_naziv, ke_vrsta_obrazovanja, ke_kandidat_id, ke_skola_id, ke_smjer_id)
												VALUES
													(:ke_naziv_kvalifikacije, :ke_naziv, :ke_vrsta_obrazovanja, :ke_kandidat_id, :ke_skola_id, :ke_smjer_id)");
						
								$insert_skole->execute(array(
											':ke_naziv_kvalifikacije' => $smjer_naziv,
											':ke_naziv' => $skola_naziv,
											':ke_vrsta_obrazovanja' => "srednje",
											':ke_skola_id' => $skola_id_post,
											':ke_smjer_id' => $smjer_id_post,
											':ke_kandidat_id' => $kandidat_id_c));
							}
							
							//Add to table users (chatbot)
							$random_string = generateRandomString();
							$options = [
								'cost' => 10,
							];
							$random_password = password_hash($random_string, PASSWORD_BCRYPT, $options);
							
							//Add to logs candidate IP	
							$date_time_ip = date("F j, Y, g:i T");
							if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
								$ip = $_SERVER['HTTP_CLIENT_IP'];
							} elseif (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
								$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
							} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
								$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
							} else {
								$ip = $_SERVER['REMOTE_ADDR'];
							}
							$log_desc = "Kandidat se ponovo prijavio: " . $kandidat_ime . " " . $kandidat_prezime . ". ".$date_time_ip."; IP: ".$ip." (".$random_string.").";
							$log_type = "5";
							addToLogs($log_desc, $log_type);
							
							//JEZICI, BOT NALOG=0...SAMO BOT
							
							if($nr_of_rows_mess == 0){
								$characters = '0123456789';
								$charactersLength = strlen($characters);
								$randomString = '';
								for ($i = 0; $i < 5; $i++) {
									$randomString .= $characters[rand(0, $charactersLength - 1)];
								}
								$bot_koriscnicko_ime = $kandidat_ime.$randomString;
								
								$query_user = $db->prepare("
											INSERT INTO users
												(phone, name, nalog_id, email, password, kandidat_id)
											VALUES
												(:phone, :name, :nalog_id, :email, :password, :kandidat_id)");

								$query_user->execute(array(
											':phone' => $mobile_phone,
											':name' => $kandidat_full_name,
											':nalog_id' => $lg_nalogid,
											':email' => $bot_koriscnicko_ime,
											':password' => $random_password,
											':kandidat_id' => $kandidat_id_c));
								$phone_f = str_replace("+", '00', $mobile_phone);
								//INFOBIP
								$link_dload = "https://crm.job-step.com/download";
								$link_uputs = "https://bit.ly/3V177tF";
								$kandidat_full_name = getCandidateFullnameR($kandidat_id_c);

								$to_send_viber_poruka_1 = $kandidat_full_name.$text_viber_new_prva_poruka; 
								$to_send_sms_poruka_1 	= $kandidat_full_name.$text_sms_new_prva_poruka; 

								$to_send_viber_poruka_2 = $text_viber_new_druga_poruka;
								$to_send_sms_poruka_2 	= $text_sms_new_druga_poruka." ".$link_dload;

								$to_send_viber_poruka_3 = $text_viber_new_treca_poruka_1.'\n\n'.$text_viber_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_viber_new_treca_poruka_3.$random_string;
								$to_send_sms_poruka_3 	= $text_sms_new_treca_poruka_1.'\n\n'.$text_sms_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_sms_new_treca_poruka_3.$random_string;


								viberPrijava1($phone_f, $to_send_sms_poruka_1, $to_send_viber_poruka_1);
								sleep(1);
								viberPrijava2($phone_f, $to_send_sms_poruka_2, $to_send_viber_poruka_2, $text_btn_new_druga_poruka, $link_dload);
								sleep(1);
								viberPrijava1($phone_f, $to_send_sms_poruka_3, $to_send_viber_poruka_3);
								sleep(1);

								if($lg_language == "bs"){
									$to_send_viber_poruka_4 = $text_viber_new_cetvrta_poruka;
									$to_send_sms_poruka_4 	= $text_sms_new_cetvrta_poruka." ".$link_uputs;
									
									viberPrijava2($phone_f, $to_send_sms_poruka_4, $to_send_viber_poruka_4, $text_btn_new_cetvrta_poruka, $link_uputs);
								}
								if($rezervisanFlag == 0){
									checkCandidateInputs($kandidat_id_c);
								}
								if($kandidat_email != null)
									sendCandidateMessengerMail($random_string, $kandidat_email, $bot_koriscnicko_ime);
								
								
							}else{
								//AKO JE KANDIDAT VEC NA BOTU PROVJERITI STATUS
								if($kandidat_status == 2 OR $kandidat_status == 4 OR $kandidat_status == 5){
									//AKO JE OBRADJEN, NA KONTROLI ILI NA DOPUNI ONDA VEZATI GA ZA NALOG I STAVITI U ODGOVARAJUCI PROJEKAT
									if($rezervisanFlag == 0){
										$update_user = $db->prepare("
													UPDATE users
													SET nalog_id = :nalog_id
													WHERE kandidat_id = :kandidat_id
												");

										$update_user->execute(array(
													':nalog_id' => $lg_nalogid,
													':kandidat_id' => $kandidat_id_c));
									
										checkCandidateInputs($kandidat_id_c);
									}
								}else{
									//AKO NIJE NISTA RADIO NA BOTU ONDA VEZATI GA ZA NALOG I POSLATI PODATKE PONOVO
									
									
									if($rezervisanFlag == 0){
										
										$characters = '0123456789';
										$charactersLength = strlen($characters);
										
										$randomString = '';
										for ($i = 0; $i < 5; $i++) {
											$randomString .= $characters[rand(0, $charactersLength - 1)];
										}
										$bot_koriscnicko_ime = $kandidat_ime.$randomString;
										
										$row_messenger = $check_messenger->fetch();
										$user_id = $row_messenger['id'];
										$mobile_phone = $row_messenger['phone'];
										
										$update_user = $db->prepare("
													UPDATE users
													SET nalog_id = :nalog_id, password = :password, email = :email
													WHERE kandidat_id = :kandidat_id
												");

										$update_user->execute(array(
													':nalog_id' => $lg_nalogid,
													':password' => $random_password,
													':email' => $bot_koriscnicko_ime,
													':kandidat_id' => $kandidat_id_c));
										
										$phone_f = str_replace("+", '', $mobile_phone);
										
										$link_dload = "https://crm.job-step.com/download";
										$link_uputs = "https://bit.ly/3V177tF";
										$kandidat_full_name = getCandidateFullnameR($kandidat_id_c);

										$to_send_viber_poruka_1 = $kandidat_full_name.$text_viber_new_prva_poruka; 
										$to_send_sms_poruka_1 	= $kandidat_full_name.$text_sms_new_prva_poruka; 

										$to_send_viber_poruka_2 = $text_viber_new_druga_poruka;
										$to_send_sms_poruka_2 	= $text_sms_new_druga_poruka." ".$link_dload;

										$to_send_viber_poruka_3 = $text_viber_new_treca_poruka_1.'\n\n'.$text_viber_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_viber_new_treca_poruka_3.$random_string;
										$to_send_sms_poruka_3 	= $text_sms_new_treca_poruka_1.'\n\n'.$text_sms_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_sms_new_treca_poruka_3.$random_string;
										// var_dump($to_send_viber_poruka_3);
										// var_dump($to_send_sms_poruka_3);
										// exit();
										viberPrijava1($phone_f, $to_send_sms_poruka_1, $to_send_viber_poruka_1);
										sleep(1);
										viberPrijava2($phone_f, $to_send_sms_poruka_2, $to_send_viber_poruka_2, $text_btn_new_druga_poruka, $link_dload);
										sleep(1);
										viberPrijava1($phone_f, $to_send_sms_poruka_3, $to_send_viber_poruka_3);
										sleep(1);

										if($lg_language == "bs"){
											$to_send_viber_poruka_4 = $text_viber_new_cetvrta_poruka;
											$to_send_sms_poruka_4 	= $text_sms_new_cetvrta_poruka." ".$link_uputs;
											
											viberPrijava2($phone_f, $to_send_sms_poruka_4, $to_send_viber_poruka_4, $text_btn_new_cetvrta_poruka, $link_uputs);
										}
										
										checkCandidateInputs($kandidat_id_c);
										
										if($kandidat_email != null)
											sendCandidateMessengerMail($random_string, $kandidat_email, $bot_koriscnicko_ime);
									}
								}
							}
							
							//SKOLE
							
							//NOTIFIKACIJE
							notifForPonovnaPrijava($kandidat_id_c, $kandidat_check_exists);
							//header("Location: registracija/thank_you/$kandidat_id_c/$kandidat_check_exists");
							if($urlid == 530){
								
							}else{
								
							}
							
						}
					/*}else{
						echo "Greška! Molimo Vas pokusajte kasnije.";
					}*/
					
					
				break;
				
				case "add_kandidat_ostalo":
						 // Build POST request:
					$recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
					$recaptcha_secret = '6LfSU_cUAAAAAAZolZBCR5u5zfIZfl2roL3irH8_';
					$recaptcha_response = $_POST['recaptcha_response'];
					// Make and decode POST request:
					$recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
					$recaptcha = json_decode($recaptcha);

					// Take action based on the score returned:
					if ($recaptcha->score >= 0) {
					$kandidat_id = $_POST['kandidat_id'];	
					$kandidat_check = $_POST['kandidat_check'];
					$kandidat_spol = $_POST['kandidat_spol'];
					$kandidat_djevojackoprezime = $_POST['kandidat_djevojackoprezime'];
					$kandidat_drzavljanstvo = $_POST['kandidat_drzavljanstvo'];
					$kandidat_adresa = $_POST['kandidat_adresa'];
					$kandidat_grad = $_POST['kandidat_grad'];
					$kandidat_pbroj = $_POST['kandidat_pbroj'];
					$kandidat_drzava = $_POST['kandidat_drzava'];
					$kandidat_vozacka_dozvola = $_POST['kandidat_vozacka_dozvola'];
					
					$update_query = $db->prepare("
								UPDATE idk_kandidati
								SET kandidat_spol = :kandidat_spol, kandidat_djevojackoprezime = :kandidat_djevojackoprezime, kandidat_drzavljanstvo = :kandidat_drzavljanstvo, kandidat_adresa = :kandidat_adresa, kandidat_grad = :kandidat_grad, kandidat_pbroj = :kandidat_pbroj, kandidat_drzava = :kandidat_drzava, kandidat_vozacka_dozvola = :kandidat_vozacka_dozvola
								WHERE kandidat_id = :kandidat_id
					");
					
					$update_query->execute(array(
								'kandidat_id' => $kandidat_id,
								'kandidat_spol' => $kandidat_spol,
								'kandidat_djevojackoprezime' => $kandidat_djevojackoprezime,
								'kandidat_drzavljanstvo' => $kandidat_drzavljanstvo,
								'kandidat_adresa' => $kandidat_adresa,
								'kandidat_grad' => $kandidat_grad,
								'kandidat_pbroj' => $kandidat_pbroj,
								'kandidat_drzava' => $kandidat_drzava,
								'kandidat_vozacka_dozvola' => $kandidat_vozacka_dozvola
					));
					
					header("Location: registracija/korak3/$kandidat_id/$kandidat_check");
					}
					else {
						echo "Something went wrong, please go back and fill out the form again";
					}
				break;

				case "add_kandidat_kontakt":
						 // Build POST request:
					$recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
					$recaptcha_secret = '6LfSU_cUAAAAAAZolZBCR5u5zfIZfl2roL3irH8_';
					$recaptcha_response = $_POST['recaptcha_response'];
					// Make and decode POST request:
					$recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
					$recaptcha = json_decode($recaptcha);

					// Take action based on the score returned:
					if ($recaptcha->score >= 0) {
					$kki_kandidat_id = $_POST['kki_kandidat_id'];
					$kki_kandidat_check = $_POST['kki_kandidat_check'];
					$kki_grupa = $_POST['kki_grupa'];
				
					if($kki_grupa == 1){
						$kki_naziv = $_POST['kki_naziv'];
						$kki_podatak = $_POST['kki_podatak'];
					}elseif ($kki_grupa == 2){
						$kki_naziv = "E-mail";
						$kki_podatak = $_POST['kki_naziv_email'];
					}elseif ($kki_grupa == 3){
						$kki_naziv = "Web";
						$kki_podatak = $_POST['kki_naziv_web'];
					}elseif ($kki_grupa == 4){
						$kki_naziv = $_POST['kki_naziv_messangeri'];
						$kki_podatak = $_POST['kki_podatak_messangeri'];
					}else{
						header("Location: registracija/korak2/$kki_kandidat_id/$kki_kandidat_check");
					};
				
				
							//Add kontakt info to db
						$query = $db->prepare("
										INSERT INTO idk_kandidat_kontakt_info
											(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id)
										VALUES
											(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id)");
				
						$query->execute(array(
									':kki_grupa' => $kki_grupa,
									':kki_naziv' => $kki_naziv,
									':kki_podatak' => $kki_podatak,
									':kki_kandidat_id' => $kki_kandidat_id));
				
						header("Location: registracija/korak2/$kki_kandidat_id/$kki_kandidat_check");
					}else {
						echo "Something when wrong, please go back and try again";
					}
				break;
				case "import_add_kandidat_new":
					
					$kandidat_ime = $_REQUEST['kandidat_ime'];
					$kandidat_prezime = $_REQUEST['kandidat_prezime'];
					$urlid = $_REQUEST['urlid'];
					$lg_language = $_REQUEST['lg_language'];
					$kandidat_email = $_REQUEST['kandidat_email'];
					$dan_rodjenja = $_REQUEST["datum_dan"];
					$mjesec_rodjenja = $_REQUEST["datum_mjesec"];
					$godina_rodjenja = $_REQUEST["datum_godina"];
					$mobile_phone = $_REQUEST['kki_phone'];
					$partner_token = $_REQUEST['token'];
					$kandidat_viza_da_ne = $_REQUEST['kandidat_viza'];
					$kandidat_group = $_REQUEST['kandidat_prijava_na'];
					// echo $kandidat_ime."tu je";
					// exit();

					if($lg_language == "de")
						include("lang/de.php");
					else
						include("lang/bs.php");
					
					$kandidat_full_name = $kandidat_ime." ".$kandidat_prezime;
					
					$kandidat_datumrodjenja = date('Y-m-d', strtotime($godina_rodjenja."-".$mjesec_rodjenja."-".$dan_rodjenja));

					//Postavljanje defulatnog urlid = 1 kad nema linka i uzimanje id naloga
					$check_query2 = $db->prepare("
											SELECT lg_id, lg_url, lg_nalogid
											FROM idk_link_generator
											WHERE lg_id = :lg_id");
				
					$check_query2->execute(array(
									':lg_id' => $urlid));
				
					$num = $check_query2->rowCount();
					$rowlg = $check_query2->fetch();
						
					$lg_nalogid = $rowlg['lg_nalogid'];
					
					if($num > 0){
						$url_id = $_REQUEST['urlid'];
					}else{
						$url_id = 1;
					}
					
					//napravi username po imenu, prezimenu i godini
					$ime_korime = strtolower($kandidat_ime);
					$prezime_korime = strtolower($kandidat_prezime);
					$imeprezime = $ime_korime.''.$prezime_korime;
					$search = array("ć", "č", "ž", "š", "đ");
					$replacement = array("c", "c", "z", "s", "dj");
					$imeprezime_korime = str_replace($search, $replacement, $imeprezime);
				
					
					if($kandidat_viza_da_ne == "DA"){
						$kandidat_viza = 1;	
					}else{
						$kandidat_viza = 0;
					}
					
					// Provjera da li postoji korisnik sa istim imenom i prezimenom u zadnjih sat vremena
					$granicno_vrijeme = date( "Y-m-d H:i:s",  strtotime("- 1 hour" ));
					$check_user = $db->prepare("
											SELECT kandidat_ime, kandidat_id, kandidat_status_messenger, kandidat_check, kandidat_visitedurl, kandidat_status
											FROM idk_kandidati
											WHERE kandidat_ime = :kandidat_ime AND kandidat_prezime = :kandidat_prezime AND kandidat_mobitel = :kandidat_mobitel AND kandidat_status != 3");
				
					$check_user->execute(array(
									':kandidat_ime' => $kandidat_ime,
									':kandidat_prezime' => $kandidat_prezime,
									':kandidat_mobitel' => $mobile_phone
									));
				
					$number_of_rows_user = $check_user->rowCount();
					
					
					$group_title_query = $db->prepare("
									SELECT kg_id, kg_title
									FROM idk_kandidati_grupe
									WHERE kg_id = :kg_id
									");
			
					$group_title_query->execute(array(
						":kg_id" => $kandidat_group
					));
					$row_kg_title = $group_title_query->fetch();
					$kandidat_prijava_na = $row_kg_title['kg_title'];
					
					$kandidat_datetime = date('Y-m-d H:i:s');
					$kandidat_check = md5(uniqid(rand(), true));
					
					if($number_of_rows_user == 0){
						
						$random_token = rand(100, 999);
						$kandidat_korisnickoime_uf = $imeprezime_korime.''.$random_token;
						$kandidat_korisnickoime = str_replace(' ', '', strtolower($kandidat_korisnickoime_uf));
						$kandidat_password = MD5($kandidat_korisnickoime);
						
						$kandidat_status = 0;
						
						if($lg_language == "bs" or $lg_language == "de")
							$kandidat_status_messenger = 1;
						else
							$kandidat_status_messenger = 4;
						
						$kandidat_slika_final = "none";
						
						//ADD PARTNER DATA
							
						if($partner_token != null){
							$partner_query = $db->prepare("
											SELECT jp_id, jp_fcmtoken, jp_position, jp_imeprezime
											FROM idk_jobstep_partners
											WHERE jp_mailconfirmation_token = :jp_mailconfirmation_token
											");
					
								$partner_query->execute(array(
									":jp_mailconfirmation_token" => $partner_token
							));
					
							$row_partner = $partner_query->fetch();
							$partner_id = $row_partner['jp_id'];
							$partner_position = $row_partner['jp_position'];
							$jp_fcmtoken = $row_partner['jp_fcmtoken'];
							$partner_ime = $row_partner['jp_imeprezime'];
							$log_poruka = "Novi kandidat ".$kandidat_ime." je prijavljen preko vašeg linka.";
							// send_notification_partnerapp($jp_fcmtoken, $partner_ime, $log_poruka);
							$kandidat_porijeklo = 6;
						}else{
							$partner_id = null;
							$partner_position = null;
							$kandidat_porijeklo = 0;
						}
						
						//Add user to db
						$query = $db->prepare("
										INSERT INTO idk_kandidati
											( kandidat_check, kandidat_ime, kandidat_prezime, kandidat_email, kandidat_mobitel, kandidat_password, kandidat_slika, kandidat_status, kandidat_status_messenger, kandidat_datetime, kandidat_korisnickoime, kandidat_datumrodjenja, kandidat_visitedurl, kandidat_prijava_na, kandidat_group, kandidat_viza, kandidat_partnerid, kandidat_partner_status, kandidat_porijeklo)
										VALUES
											(:kandidat_check,:kandidat_ime,:kandidat_prezime,:kandidat_email,:kandidat_mobitel,:kandidat_password,:kandidat_slika,:kandidat_status,:kandidat_status_messenger,:kandidat_datetime,:kandidat_korisnickoime,:kandidat_datumrodjenja,:kandidat_visitedurl,:kandidat_prijava_na,:kandidat_group,:kandidat_viza,:kandidat_partnerid,:kandidat_partner_status,:kandidat_porijeklo)");
				
						$query->execute(array(
									':kandidat_check' => $kandidat_check,
									':kandidat_ime' => $kandidat_ime,
									':kandidat_prezime' => $kandidat_prezime,
									':kandidat_email' => $kandidat_email,
									':kandidat_mobitel' => $mobile_phone,
									':kandidat_password' => $kandidat_password,
									':kandidat_slika' => $kandidat_slika_final,
									':kandidat_status' => $kandidat_status,
									':kandidat_status_messenger' => $kandidat_status_messenger,
									':kandidat_datetime' => $kandidat_datetime,
									':kandidat_korisnickoime' => $kandidat_korisnickoime,
									':kandidat_datumrodjenja' => $kandidat_datumrodjenja,
									':kandidat_visitedurl' => $url_id,
									':kandidat_prijava_na' => $kandidat_prijava_na,
									':kandidat_group' => $kandidat_group,
									':kandidat_viza' => $kandidat_viza,
									':kandidat_partnerid' => $partner_id,
									':kandidat_partner_status' => $partner_position,
									':kandidat_porijeklo' => $kandidat_porijeklo
									));
									
						$kandidat_id = $db->lastInsertId();
						
						$query_log_status = $db->prepare("
								INSERT INTO idk_log_kandidat_statusi
									(lks_kandidat_id, lks_status_obrade, lks_status_messenger, lks_datetime)
								VALUES
									(:lks_kandidat_id, :lks_status_obrade, :lks_status_messenger, :lks_datetime)
						");
						
						$query_log_status->execute(array(
								':lks_kandidat_id' => $kandidat_id,
								':lks_status_obrade' => $kandidat_status,
								':lks_status_messenger' => $kandidat_status_messenger,
								':lks_datetime' => $kandidat_datetime
						));

						$post_nivo_jezika = $_REQUEST['nivo_jezika'];
						
						if($post_nivo_jezika == "Ne poznajem njemački jezik.")
							$kj_znanje_njemacki = "Bez znanja";
						else
							$kj_znanje_njemacki = $post_nivo_jezika;
						
						$kj_naziv_njemacki = "Njemački";
						// Add language knowlege
						$query_njem = $db->prepare("
										INSERT INTO idk_kandidat_jezici
											(kj_naziv, kj_slusanje, kj_citanje, kj_govorna_interakcija, kj_govorna_produkcija, kj_pisanje, kj_kandidatid)
										VALUES
											(:kj_naziv, :kj_slusanje, :kj_citanje, :kj_govorna_interakcija, :kj_govorna_produkcija, :kj_pisanje, :kj_kandidatid)");
				
						$query_njem->execute(array(
									':kj_naziv' => $kj_naziv_njemacki,
									':kj_slusanje' => $kj_znanje_njemacki,
									':kj_citanje' => $kj_znanje_njemacki,
									':kj_govorna_interakcija' => $kj_znanje_njemacki,
									':kj_govorna_produkcija' => $kj_znanje_njemacki,
									':kj_pisanje' => $kj_znanje_njemacki,
									':kj_kandidatid' => $kandidat_id));
										
						
						//VEZANJE KANDIDATA ZA PROJEKT
						if($lg_nalogid != 0){
							$nalog_query = $db->prepare("
													SELECT project_id
													FROM idk_projects
													WHERE project_nalogid = :project_nalogid AND (project_name LIKE '%Prijave%' OR project_name LIKE '%Prijava%') ");
						
							$nalog_query->execute(array(
											':project_nalogid' => $lg_nalogid));
						
							$nalogrow = $nalog_query->fetch();
							if(!$nalogrow){
								//sta raditi ako nema projekta "PRIJAVA"
								//IDU U NOVE PROJEKTI ZA VIBER NALOG
							}else{
								
								$project_id = $nalogrow['project_id'];	
								
								$query_project = $db->prepare("
												INSERT INTO idk_project_kandidati
													(pk_projectid, pk_kandidatid)
												VALUES
													(:pk_projectid, :pk_kandidatid)");
					
								$query_project->execute(array(
												':pk_projectid' => $project_id,
												':pk_kandidatid' => $kandidat_id));	
							}
						}
						
						$kki_grupa = 1;
						$kki_naziv = "Mobilni";

						//Add mobilni to db
						$query_mob = $db->prepare("
										INSERT INTO idk_kandidat_kontakt_info
											(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id)
										VALUES
											(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id)");

						$query_mob->execute(array(
									':kki_grupa' => $kki_grupa,
									':kki_naziv' => $kki_naziv,
									':kki_podatak' => $mobile_phone,
									':kki_kandidat_id' => $kandidat_id));
						
						$kki_grupa_e = 2;
						$kki_naziv_e = "E-mail";
						$kki_podatak_e = $_REQUEST['kandidat_email'];

						//Add kontakt info to db
						$query_email = $db->prepare("
										INSERT INTO idk_kandidat_kontakt_info
											(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id)
										VALUES
											(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id)");

						$query_email->execute(array(
									':kki_grupa' => $kki_grupa_e,
									':kki_naziv' => $kki_naziv_e,
									':kki_podatak' => $kki_podatak_e,
									':kki_kandidat_id' => $kandidat_id));
						
						//Add to table users (chatbot)
						$random_string = generateRandomString();
						$options = [
							'cost' => 10,
						];
						$random_password = password_hash($random_string, PASSWORD_BCRYPT, $options);
						
						//Add to logs candidate IP	
						$date_time_ip = date("F j, Y, g:i T");
						if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
							$ip = $_SERVER['HTTP_CLIENT_IP'];
						} elseif (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
							$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
						} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
							$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
						} else {
							$ip = $_SERVER['REMOTE_ADDR'];
						}
						$log_desc = "Kandidat: " . $kandidat_ime . " " . $kandidat_prezime . "(".$kandidat_id."). ".$date_time_ip."; IP: ".$ip." (".$random_string.")";
						$log_type = "5";
						addToLogs($log_desc, $log_type);
						
						if($urlid == 0){
							header("Location: registracija/korak2/$kandidat_id/$kandidat_check");
						}else{
							
							$characters = '0123456789';
							$charactersLength = strlen($characters);
							$randomString = '';
							for ($i = 0; $i < 5; $i++) {
								$randomString .= $characters[rand(0, $charactersLength - 1)];
							}
							$bot_koriscnicko_ime = $kandidat_ime.$randomString;
							
							$query_user = $db->prepare("
										INSERT INTO users
											(phone, name, nalog_id, email, password, kandidat_id)
										VALUES
											(:phone, :name, :nalog_id, :email, :password, :kandidat_id)");

							$query_user->execute(array(
										':phone' => $mobile_phone,
										':name' => $kandidat_full_name,
										':nalog_id' => $lg_nalogid,
										':email' => $bot_koriscnicko_ime,
										':password' => $random_password,
										':kandidat_id' => $kandidat_id));
							
							
							$phone_f = str_replace("+", '', $mobile_phone);
							
							$link_dload = "https://crm.job-step.com/download";
							$link_uputs = "https://bit.ly/3V177tF";

							$kandidat_full_name = getCandidateFullnameR($kandidat_id);

							$to_send_viber_poruka_1 = $kandidat_full_name.$text_viber_new_prva_poruka; 
							$to_send_sms_poruka_1 	= $kandidat_full_name.$text_sms_new_prva_poruka; 

							$to_send_viber_poruka_2 = $text_viber_new_druga_poruka;
							$to_send_sms_poruka_2 	= $text_sms_new_druga_poruka." ".$link_dload;

							$to_send_viber_poruka_3 = $text_viber_new_treca_poruka_1.'\n\n'.$text_viber_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_viber_new_treca_poruka_3.$random_string;
							$to_send_sms_poruka_3 	= $text_sms_new_treca_poruka_1.'\n\n'.$text_sms_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_sms_new_treca_poruka_3.$random_string;
							// var_dump($to_send_viber_poruka_3);
							// var_dump($to_send_sms_poruka_3);
							// exit();
							viberPrijava1($phone_f, $to_send_sms_poruka_1, $to_send_viber_poruka_1);
							sleep(1);
							viberPrijava2($phone_f, $to_send_sms_poruka_2, $to_send_viber_poruka_2, $text_btn_new_druga_poruka, $link_dload);
							sleep(1);
							viberPrijava1($phone_f, $to_send_sms_poruka_3, $to_send_viber_poruka_3);
							sleep(1);

							if($lg_language == "bs"){
								$to_send_viber_poruka_4 = $text_viber_new_cetvrta_poruka;
								$to_send_sms_poruka_4 	= $text_sms_new_cetvrta_poruka." ".$link_uputs;
								
								viberPrijava2($phone_f, $to_send_sms_poruka_4, $to_send_viber_poruka_4, $text_btn_new_cetvrta_poruka, $link_uputs);
							}
							
							sendCandidateMessengerMail($random_string, $kandidat_email, $bot_koriscnicko_ime);
							
							header("Location: registracija/thank_you/$kandidat_id/$kandidat_check/$lg_language");
						}
					
						notifForOnlneRegister($kandidat_id, $kandidat_check);
						
					}else{
						
						$rows_user = $check_user->fetch();
						$kandidat_id_c = $rows_user['kandidat_id'];
						$kandidat_status_messenger = $rows_user['kandidat_status_messenger'];
						$kandidat_check_exists = $rows_user['kandidat_check'];
						$stari_visitedurl = $rows_user['kandidat_visitedurl'];
						$kandidat_status = $rows_user['kandidat_status'];
						
						$kandidat_slika_final = "none";
						
						$check_messenger = $db->prepare("
												SELECT id, email, phone
												FROM users
												WHERE kandidat_id = :kandidat_id ");
					
						$check_messenger->execute(array(
										':kandidat_id' => $kandidat_id_c
										));
					
						$nr_of_rows_mess = $check_messenger->rowCount();
						if($nr_of_rows_mess == 0){
							$kandidat_status_messenger = 1;
						}else{}
						
						//ADD PARTNER DATA
							
						if($partner_token != null){
							$partner_query = $db->prepare("
											SELECT jp_id, jp_fcmtoken, jp_position, jp_imeprezime
											FROM idk_jobstep_partners
											WHERE jp_mailconfirmation_token = :jp_mailconfirmation_token
											");
					
								$partner_query->execute(array(
									":jp_mailconfirmation_token" => $partner_token
							));
					
							$row_partner = $partner_query->fetch();
							$partner_id = $row_partner['jp_id'];
							$partner_position = $row_partner['jp_position'];
							$jp_fcmtoken = $row_partner['jp_fcmtoken'];
							$partner_ime = $row_partner['jp_imeprezime'];
							$log_poruka = "Novi kandidat ".$kandidat_ime." je prijavljen preko vašeg linka.";
							// send_notification_partnerapp($jp_fcmtoken, $partner_ime, $log_poruka);
							$kandidat_porijeklo = 7;
						}else{
							$partner_id = null;
							$partner_position = null;
							$kandidat_porijeklo = 0;
						}
						
						//Update user
						$update_user = $db->prepare("
										UPDATE idk_kandidati
										SET kandidat_email = :kandidat_email, kandidat_status_messenger = :kandidat_status_messenger, kandidat_datumrodjenja = :kandidat_datumrodjenja, kandidat_viza = :kandidat_viza, kandidat_visitedurl = :kandidat_visitedurl, kandidat_prijava_na = :kandidat_prijava_na, kandidat_partnerid = :kandidat_partnerid, kandidat_partner_status = :kandidat_partner_status, kandidat_porijeklo = :kandidat_porijeklo
										WHERE kandidat_id = :kandidat_id
										");
				
						$update_user->execute(array(
									':kandidat_id' => $kandidat_id_c,
									':kandidat_email' => $kandidat_email,
									':kandidat_status_messenger' => $kandidat_status_messenger,
									':kandidat_datumrodjenja' => $kandidat_datumrodjenja,
									':kandidat_viza' => $kandidat_viza,
									':kandidat_visitedurl' => $url_id,
									':kandidat_prijava_na' => $kandidat_prijava_na,
									':kandidat_partnerid' => $partner_id,
									':kandidat_partner_status' => $partner_position,
									':kandidat_porijeklo' => $kandidat_porijeklo
									));
						
						//INSERT INTO KANDIDAT LOG STATUSI
						
						$query_log_status = $db->prepare("
								INSERT INTO idk_log_kandidat_statusi
									(lks_kandidat_id, lks_status_obrade, lks_status_messenger, lks_datetime)
								VALUES
									(:lks_kandidat_id, :lks_status_obrade, :lks_status_messenger, :lks_datetime)
						");
						
						$query_log_status->execute(array(
								':lks_kandidat_id' => $kandidat_id_c,
								':lks_status_obrade' => 9,
								':lks_status_messenger' => 0,
								':lks_datetime' => $kandidat_datetime
						));
						
						$post_nivo_jezika = $_REQUEST['nivo_jezika'];
						
						if($post_nivo_jezika == "Ne poznajem njemački jezik.")
							$kj_znanje_njemacki = "Bez znanja";
						else
							$kj_znanje_njemacki = $post_nivo_jezika;
						
						$kj_naziv_njemacki = "Njemački";
						// Add language knowlege
						$query_njem = $db->prepare("
										UPDATE idk_kandidat_jezici
										SET kj_naziv = :kj_naziv, kj_citanje = :kj_citanje, kj_slusanje = :kj_slusanje, kj_citanje = :kj_citanje, kj_govorna_interakcija = :kj_govorna_interakcija, kj_govorna_produkcija = :kj_govorna_produkcija, kj_pisanje = :kj_pisanje
										WHERE kj_kandidatid = :kj_kandidatid
										");
				
						$query_njem->execute(array(
									':kj_naziv' => $kj_naziv_njemacki,
									':kj_slusanje' => $kj_znanje_njemacki,
									':kj_citanje' => $kj_znanje_njemacki,
									':kj_govorna_interakcija' => $kj_znanje_njemacki,
									':kj_govorna_produkcija' => $kj_znanje_njemacki,
									':kj_pisanje' => $kj_znanje_njemacki,
									':kj_kandidatid' => $kandidat_id));				
						
						//VEZANJE KANDIDATA ZA PROJEKT
						if($lg_nalogid != 0){
							$nalog_query = $db->prepare("
													SELECT project_id
													FROM idk_projects
													WHERE project_nalogid = :project_nalogid AND (project_name LIKE '%Prijave%' OR project_name LIKE '%Prijava%') ");
						
							$nalog_query->execute(array(
											':project_nalogid' => $lg_nalogid));
						
							$nalogrow = $nalog_query->fetch();
							if(!$nalogrow){
								//sta raditi ako nema projekta "PRIJAVA"
								//IDU U NOVE PROJEKTI ZA VIBER NALOG
							}else{
							
								$project_id = $nalogrow['project_id'];	
								$check_project = $db->prepare("
														SELECT pk_projectid
														FROM idk_project_kandidati
														WHERE pk_projectid = :pk_projectid AND pk_kandidatid = :pk_kandidatid");
							
								$check_project->execute(array(
													':pk_projectid' => $project_id,
													':pk_kandidatid' => $kandidat_id_c
								));
								if($check_project->rowCount() == 0){
									
									$query_project = $db->prepare("
													INSERT INTO idk_project_kandidati
														(pk_projectid, pk_kandidatid)
													VALUES
														(:pk_projectid, :pk_kandidatid)");
						
									$query_project->execute(array(
													':pk_projectid' => $project_id,
													':pk_kandidatid' => $kandidat_id_c));
								}
							}
						}
						
						//Add to table users (chatbot)
						$random_string = generateRandomString();
						$options = [
							'cost' => 10,
						];
						$random_password = password_hash($random_string, PASSWORD_BCRYPT, $options);
						
						//Add to logs candidate IP	
						$date_time_ip = date("F j, Y, g:i T");
						if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
							$ip = $_SERVER['HTTP_CLIENT_IP'];
						} elseif (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
							$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
						} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
							$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
						} else {
							$ip = $_SERVER['REMOTE_ADDR'];
						}
						$log_desc = "Kandidat se ponovo prijavio: " . $kandidat_ime . " " . $kandidat_prezime . ". ".$date_time_ip."; IP: ".$ip." (".$random_string."). Stari visited url: ".$stari_visitedurl."";
						$log_type = "5";
						addToLogs($log_desc, $log_type);
						
						//JEZICI, BOT NALOG=0...SAMO BOT
						
						if($nr_of_rows_mess == 0){
							$characters = '0123456789';
							$charactersLength = strlen($characters);
							$randomString = '';
							for ($i = 0; $i < 5; $i++) {
								$randomString .= $characters[rand(0, $charactersLength - 1)];
							}
							$bot_koriscnicko_ime = $kandidat_ime.$randomString;
							
							$query_user = $db->prepare("
										INSERT INTO users
											(phone, name, nalog_id, email, password, kandidat_id)
										VALUES
											(:phone, :name, :nalog_id, :email, :password, :kandidat_id)");

							$query_user->execute(array(
										':phone' => $mobile_phone,
										':name' => $kandidat_full_name,
										':nalog_id' => $lg_nalogid,
										':email' => $bot_koriscnicko_ime,
										':password' => $random_password,
										':kandidat_id' => $kandidat_id_c));
							$phone_f = str_replace("+", '00', $mobile_phone);
							//INFOBIP
							$link_dload = "https://crm.job-step.com/download";
							$link_uputs = "https://bit.ly/3V177tF";
							$kandidat_full_name = getCandidateFullnameR($kandidat_id_c);

							$to_send_viber_poruka_1 = $kandidat_full_name.$text_viber_new_prva_poruka; 
							$to_send_sms_poruka_1 	= $kandidat_full_name.$text_sms_new_prva_poruka; 

							$to_send_viber_poruka_2 = $text_viber_new_druga_poruka;
							$to_send_sms_poruka_2 	= $text_sms_new_druga_poruka." ".$link_dload;

							$to_send_viber_poruka_3 = $text_viber_new_treca_poruka_1.'\n\n'.$text_viber_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_viber_new_treca_poruka_3.$random_string;
							$to_send_sms_poruka_3 	= $text_sms_new_treca_poruka_1.'\n\n'.$text_sms_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_sms_new_treca_poruka_3.$random_string;


							viberPrijava1($phone_f, $to_send_sms_poruka_1, $to_send_viber_poruka_1);
							sleep(1);
							viberPrijava2($phone_f, $to_send_sms_poruka_2, $to_send_viber_poruka_2, $text_btn_new_druga_poruka, $link_dload);
							sleep(1);
							viberPrijava1($phone_f, $to_send_sms_poruka_3, $to_send_viber_poruka_3);
							sleep(1);

							if($lg_language == "bs"){
								$to_send_viber_poruka_4 = $text_viber_new_cetvrta_poruka;
								$to_send_sms_poruka_4 	= $text_sms_new_cetvrta_poruka." ".$link_uputs;
								
								viberPrijava2($phone_f, $to_send_sms_poruka_4, $to_send_viber_poruka_4, $text_btn_new_cetvrta_poruka, $link_uputs);
							}
							
							sendCandidateMessengerMail($random_string, $kandidat_email, $bot_koriscnicko_ime);
							
							
						}else{
							//AKO JE KANDIDAT VEC NA BOTU PROVJERITI STATUS
							if($kandidat_status == 2 OR $kandidat_status == 4 OR $kandidat_status == 5){
								//AKO JE OBRADJEN, NA KONTROLI ILI NA DOPUNI ONDA VEZATI GA ZA NALOG I STAVITI U ODGOVARAJUCI PROJEKAT
								$update_user = $db->prepare("
											UPDATE users
											SET nalog_id = :nalog_id
											WHERE kandidat_id = :kandidat_id
										");

								$update_user->execute(array(
											':nalog_id' => $lg_nalogid,
											':kandidat_id' => $kandidat_id_c));
											
								checkCandidateInputs($kandidat_id_c);
							}else{
								//AKO NIJE NISTA RADIO NA BOTU ONDA VEZATI GA ZA NALOG I POSLATI PODATKE PONOVO
								$random_string = generateRandomString();
								$options = [
									'cost' => 10,
								];
								$random_password = password_hash($random_string, PASSWORD_BCRYPT, $options);
								
								$characters = '0123456789';
								$charactersLength = strlen($characters);
								
								$randomString = '';
								for ($i = 0; $i < 5; $i++) {
									$randomString .= $characters[rand(0, $charactersLength - 1)];
								}
								$bot_koriscnicko_ime = $kandidat_ime.$randomString;
								
								$row_messenger = $check_messenger->fetch();
								$user_id = $row_messenger['id'];
								$mobile_phone = $row_messenger['phone'];
								
								$update_user = $db->prepare("
											UPDATE users
											SET nalog_id = :nalog_id, password = :password, email = :email
											WHERE kandidat_id = :kandidat_id
										");

								$update_user->execute(array(
											':nalog_id' => $lg_nalogid,
											':password' => $random_password,
											':email' => $bot_koriscnicko_ime,
											':kandidat_id' => $kandidat_id_c));
								
								$phone_f = str_replace("+", '', $mobile_phone);
								
								$link_dload = "https://crm.job-step.com/download";
								$link_uputs = "https://bit.ly/3V177tF";
								$kandidat_full_name = getCandidateFullnameR($kandidat_id_c);

								$to_send_viber_poruka_1 = $kandidat_full_name.$text_viber_new_prva_poruka; 
								$to_send_sms_poruka_1 	= $kandidat_full_name.$text_sms_new_prva_poruka; 

								$to_send_viber_poruka_2 = $text_viber_new_druga_poruka;
								$to_send_sms_poruka_2 	= $text_sms_new_druga_poruka." ".$link_dload;

								$to_send_viber_poruka_3 = $text_viber_new_treca_poruka_1.'\n\n'.$text_viber_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_viber_new_treca_poruka_3.$random_string;
								$to_send_sms_poruka_3 	= $text_sms_new_treca_poruka_1.'\n\n'.$text_sms_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_sms_new_treca_poruka_3.$random_string;
								// var_dump($to_send_viber_poruka_3);
								// var_dump($to_send_sms_poruka_3);
								// exit();
								viberPrijava1($phone_f, $to_send_sms_poruka_1, $to_send_viber_poruka_1);
								sleep(1);
								viberPrijava2($phone_f, $to_send_sms_poruka_2, $to_send_viber_poruka_2, $text_btn_new_druga_poruka, $link_dload);
								sleep(1);
								viberPrijava1($phone_f, $to_send_sms_poruka_3, $to_send_viber_poruka_3);
								sleep(1);

								if($lg_language == "bs"){
									$to_send_viber_poruka_4 = $text_viber_new_cetvrta_poruka;
									$to_send_sms_poruka_4 	= $text_sms_new_cetvrta_poruka." ".$link_uputs;
									
									viberPrijava2($phone_f, $to_send_sms_poruka_4, $to_send_viber_poruka_4, $text_btn_new_cetvrta_poruka, $link_uputs);
								}
								
								sendCandidateMessengerMail($random_string, $kandidat_email, $bot_koriscnicko_ime);
							}
						}
						
						//NOTIFIKACIJE
						notifForPonovnaPrijava($kandidat_id_c, $kandidat_check_exists);
						//header("Location: registracija/thank_you/$kandidat_id_c/$kandidat_check_exists");
						header("Location: registracija/thank_you/$kandidat_id_c/$kandidat_check_exists/$lg_language");
					}
					
				break;
				
				case "add_kandidat_ostalo":
						 // Build POST request:
					$recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
					$recaptcha_secret = '6LfSU_cUAAAAAAZolZBCR5u5zfIZfl2roL3irH8_';
					$recaptcha_response = $_POST['recaptcha_response'];
					// Make and decode POST request:
					$recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
					$recaptcha = json_decode($recaptcha);

					// Take action based on the score returned:
					if ($recaptcha->score >= 0) {
					$kandidat_id = $_POST['kandidat_id'];	
					$kandidat_check = $_POST['kandidat_check'];
					$kandidat_spol = $_POST['kandidat_spol'];
					$kandidat_djevojackoprezime = $_POST['kandidat_djevojackoprezime'];
					$kandidat_drzavljanstvo = $_POST['kandidat_drzavljanstvo'];
					$kandidat_adresa = $_POST['kandidat_adresa'];
					$kandidat_grad = $_POST['kandidat_grad'];
					$kandidat_pbroj = $_POST['kandidat_pbroj'];
					$kandidat_drzava = $_POST['kandidat_drzava'];
					$kandidat_vozacka_dozvola = $_POST['kandidat_vozacka_dozvola'];
					
					$update_query = $db->prepare("
								UPDATE idk_kandidati
								SET kandidat_spol = :kandidat_spol, kandidat_djevojackoprezime = :kandidat_djevojackoprezime, kandidat_drzavljanstvo = :kandidat_drzavljanstvo, kandidat_adresa = :kandidat_adresa, kandidat_grad = :kandidat_grad, kandidat_pbroj = :kandidat_pbroj, kandidat_drzava = :kandidat_drzava, kandidat_vozacka_dozvola = :kandidat_vozacka_dozvola
								WHERE kandidat_id = :kandidat_id
					");
					
					$update_query->execute(array(
								'kandidat_id' => $kandidat_id,
								'kandidat_spol' => $kandidat_spol,
								'kandidat_djevojackoprezime' => $kandidat_djevojackoprezime,
								'kandidat_drzavljanstvo' => $kandidat_drzavljanstvo,
								'kandidat_adresa' => $kandidat_adresa,
								'kandidat_grad' => $kandidat_grad,
								'kandidat_pbroj' => $kandidat_pbroj,
								'kandidat_drzava' => $kandidat_drzava,
								'kandidat_vozacka_dozvola' => $kandidat_vozacka_dozvola
					));
					
					header("Location: registracija/korak3/$kandidat_id/$kandidat_check");
					}
					else {
						echo "Something went wrong, please go back and fill out the form again";
					}
				break;

				case "add_kandidat_kontakt":
						 // Build POST request:
					$recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
					$recaptcha_secret = '6LfSU_cUAAAAAAZolZBCR5u5zfIZfl2roL3irH8_';
					$recaptcha_response = $_POST['recaptcha_response'];
					// Make and decode POST request:
					$recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
					$recaptcha = json_decode($recaptcha);

					// Take action based on the score returned:
					if ($recaptcha->score >= 0) {
					$kki_kandidat_id = $_POST['kki_kandidat_id'];
					$kki_kandidat_check = $_POST['kki_kandidat_check'];
					$kki_grupa = $_POST['kki_grupa'];
				
					if($kki_grupa == 1){
						$kki_naziv = $_POST['kki_naziv'];
						$kki_podatak = $_POST['kki_podatak'];
					}elseif ($kki_grupa == 2){
						$kki_naziv = "E-mail";
						$kki_podatak = $_POST['kki_naziv_email'];
					}elseif ($kki_grupa == 3){
						$kki_naziv = "Web";
						$kki_podatak = $_POST['kki_naziv_web'];
					}elseif ($kki_grupa == 4){
						$kki_naziv = $_POST['kki_naziv_messangeri'];
						$kki_podatak = $_POST['kki_podatak_messangeri'];
					}else{
						header("Location: registracija/korak2/$kki_kandidat_id/$kki_kandidat_check");
					};
				
				
							//Add kontakt info to db
						$query = $db->prepare("
										INSERT INTO idk_kandidat_kontakt_info
											(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id)
										VALUES
											(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id)");
				
						$query->execute(array(
									':kki_grupa' => $kki_grupa,
									':kki_naziv' => $kki_naziv,
									':kki_podatak' => $kki_podatak,
									':kki_kandidat_id' => $kki_kandidat_id));
				
						header("Location: registracija/korak2/$kki_kandidat_id/$kki_kandidat_check");
					}else {
						echo "Something when wrong, please go back and try again";
					}
				break;
				case "add_kandidat_edukacija":
							 // Build POST request:
					$recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
					$recaptcha_secret = '6LfSU_cUAAAAAAZolZBCR5u5zfIZfl2roL3irH8_';
					$recaptcha_response = $_POST['recaptcha_response'];
					// Make and decode POST request:
					$recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
					$recaptcha = json_decode($recaptcha);

					// Take action based on the score returned:
					if ($recaptcha->score >= 0) {
						$ke_datumod	 = date("Y-m-d", strtotime("01-01-".$_POST['ke_datumod']));
				
						if(isset($_POST['ke_datumdo_aktuelno'])){
							$ke_datumdo = "";
						}else{
							$ke_datumdo = date("Y-m-d", strtotime("01-01-".$_POST['ke_datumdo']));
						}
				
				
						if($_POST['ke_naziv_kvalifikacije'] != "OSTALO"){
							$ke_smjer_id = $_POST['smjerovi'];
							
							$query_smjer_naziv = $db->prepare("SELECT ss_naziv FROM idk_skole_smjerovi WHERE ss_id = $ke_smjer_id");
							$query_smjer_naziv->execute();
							$row_smjer_naziv = $query_smjer_naziv->fetch();
							$ke_naziv_kvalifikacije = $row_smjer_naziv['ss_naziv'];
						}else{
							$ke_naziv_kvalifikacije = $_POST['ke_naziv_kvalifikacije'];
							$ke_smjer_id = null;
						}
				
						$ke_skola_id = $_POST['ke_naziv'];
						
						$query_skola_naziv = $db->prepare("SELECT skola_naziv FROM idk_skole WHERE skola_id = $ke_skola_id");
						$query_skola_naziv->execute();
						$row_skola_naziv = $query_skola_naziv->fetch();
						$ke_naziv = $row_skola_naziv['skola_naziv'];
						
						$ke_grad = $_POST['ke_grad'];
						$ke_drzava = $_POST['ke_drzava'];
						$ke_opis = $_POST['ke_opis'];
						$ke_kandidat_id = $_POST['ke_kandidat_id'];
						$ke_kandidat_check = $_POST['ke_kandidat_check'];
						$vrsta_obrazovanja = $_POST['vrsta_obrazovanja'];
				
				
						//Add user to db
						$query = $db->prepare("
										INSERT INTO idk_kandidat_edukacija
											(ke_datumod	, ke_datumdo, ke_naziv_kvalifikacije, ke_naziv, ke_grad, ke_drzava, ke_opis, ke_vrsta_obrazovanja, ke_kandidat_id, ke_skola_id, ke_smjer_id)
										VALUES
											(:ke_datumod, :ke_datumdo, :ke_naziv_kvalifikacije, :ke_naziv, :ke_grad, :ke_drzava, :ke_opis, :ke_vrsta_obrazovanja, :ke_kandidat_id, :ke_skola_id, :ke_smjer_id)");
				
						$query->execute(array(
									':ke_datumod' => $ke_datumod	,
									':ke_datumdo' => $ke_datumdo,
									':ke_naziv_kvalifikacije' => $ke_naziv_kvalifikacije,
									':ke_naziv' => $ke_naziv,
									':ke_grad' => $ke_grad,
									':ke_drzava' => $ke_drzava,
									':ke_opis' => $ke_opis,
									':ke_vrsta_obrazovanja' => $vrsta_obrazovanja,
									':ke_skola_id' => $ke_skola_id,
									':ke_smjer_id' => $ke_smjer_id,
									':ke_kandidat_id' => $ke_kandidat_id));
				
						header("Location: registracija/korak3/$ke_kandidat_id/$ke_kandidat_check");
					} else echo "Something went wrong, please try again";
				break;				
				
				case "add_kandidat_iskustvo":
					 // Build POST request:
					$recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
					$recaptcha_secret = '6LfSU_cUAAAAAAZolZBCR5u5zfIZfl2roL3irH8_';
					$recaptcha_response = $_POST['recaptcha_response'];
					// Make and decode POST request:
					$recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
					$recaptcha = json_decode($recaptcha);

					// Take action based on the score returned:
					if ($recaptcha->score >= 0) {
						//START -> Kupljenje iz select-a - datum od i do te spajanje u odgovarajući format <-
						$kri_datum_od_mjesec_pokupi = $_POST['kri_datum_od_mjesec'];
						$kri_datum_od_godina_pokupi = $_POST['kri_datum_od_godina'];
						$kri_datum_do_mjesec_pokupi = $_POST['kri_datum_do_mjesec'];
						$kri_datum_do_godina_pokupi = $_POST['kri_datum_do_godina'];
						//END
						
						//Format datuma START
						$kri_datum_od_format = $kri_datum_od_mjesec_pokupi .'-'.$kri_datum_od_godina_pokupi;
						$kri_datum_do_format = $kri_datum_do_mjesec_pokupi .'-'.$kri_datum_do_godina_pokupi;
						//Format datuma END
						
						$kri_darum_od = date("Y-m-d", strtotime("01-".$kri_datum_od_format));
						if(isset($_POST['kri_datum_do_aktuelno'])){
							$kri_datum_do = "";
						}else{
							$kri_datum_do = date("Y-m-d", strtotime("01-".$kri_datum_do_format));
						}
				
						$kri_pozicija = $_POST['kri_pozicija'];
						$kri_naziv = $_POST['kri_naziv'];
				
						$kri_grad = $_POST['kri_grad'];
						$kri_drzava = $_POST['kri_drzava'];
						$kri_adresa = $_POST['kri_adresa'];
						$kri_telefon = $_POST['kri_telefon'];
						$kri_email = $_POST['kri_email'];
						$kri_web = $_POST['kri_web'];
				
						$kri_opis = $_POST['kri_opis'];
						$kri_kandidat_id = $_POST['kri_kandidat_id'];
						$kri_kandidat_check = $_POST['kri_kandidat_check'];
				
				
						//Add user to db
						$query = $db->prepare("
										INSERT INTO idk_kandidat_radno_iskustvo
											(kri_darum_od, kri_datum_do, kri_pozicija, kri_naziv, kri_grad, kri_drzava, kri_adresa, kri_telefon, kri_email, kri_web, kri_opis, kri_kandidat_id)
										VALUES
											(:kri_darum_od, :kri_datum_do, :kri_pozicija, :kri_naziv, :kri_grad, :kri_drzava, :kri_adresa, :kri_telefon, :kri_email, :kri_web, :kri_opis, :kri_kandidat_id)");
				
						$query->execute(array(
									':kri_darum_od' => $kri_darum_od,
									':kri_datum_do' => $kri_datum_do,
									':kri_pozicija' => $kri_pozicija,
									':kri_naziv' => $kri_naziv,
									':kri_grad' => $kri_grad,
									':kri_drzava' => $kri_drzava,
									':kri_adresa' => $kri_adresa,
									':kri_telefon' => $kri_telefon,
									':kri_email' => $kri_email,
									':kri_web' => $kri_web,
									':kri_opis' => $kri_opis,
									':kri_kandidat_id' => $kri_kandidat_id));
				
									header("Location: registracija/korak4/$kri_kandidat_id/$kri_kandidat_check");
					}else echo "Something went wrong, please try again";
				break;	

				case "add_kandidat_vjestine":
						 // Build POST request:
					$recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
					$recaptcha_secret = '6LfSU_cUAAAAAAZolZBCR5u5zfIZfl2roL3irH8_';
					$recaptcha_response = $_POST['recaptcha_response'];
					// Make and decode POST request:
					$recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
					$recaptcha = json_decode($recaptcha);

					// Take action based on the score returned:
					if ($recaptcha->score >= 0) {
						$kv_naziv = $_POST['kv_naziv'];
						$kv_grupa = $_POST['kv_grupa'];
						$kv_opis = $_POST['kv_opis'];
						$kv_kandidat_id = $_POST['kv_kandidat_id'];
						$kv_kandidat_check = $_POST['kv_kandidat_check'];
				
				
						//Add user to db
						$query = $db->prepare("
										INSERT INTO idk_kandidat_vjestine
											(kv_naziv, kv_grupa, kv_opis, kv_kandidat_id)
										VALUES
											(:kv_naziv, :kv_grupa, :kv_opis, :kv_kandidat_id)");
				
						$query->execute(array(
									':kv_naziv' => $kv_naziv,
									':kv_grupa' => $kv_grupa,
									':kv_opis' => $kv_opis,
									':kv_kandidat_id' => $kv_kandidat_id));
				
									header("Location: registracija/korak5/$kv_kandidat_id/$kv_kandidat_check");
					}else echo "Something went wrong, please try again";
				break;				
				
				case "add_kandidat_jezik":
							 // Build POST request:
					$recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
					$recaptcha_secret = '6LfSU_cUAAAAAAZolZBCR5u5zfIZfl2roL3irH8_';
					$recaptcha_response = $_POST['recaptcha_response'];
					// Make and decode POST request:
					$recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
					$recaptcha = json_decode($recaptcha);

					// Take action based on the score returned:
					if ($recaptcha->score >= 0) {
						if(!empty($_POST['kj_ostalo'])){
							$kj_naziv = $_POST['kj_ostalo'];
						}else{
							$kj_naziv = $_POST['kj_naziv'];
						}
				
				
						//$kj_slusanje = $_POST['kj_slusanje'];
						//$kj_citanje = $_POST['kj_citanje'];
						//$kj_govorna_interakcija = $_POST['kj_govorna_interakcija'];
						//$kj_govorna_produkcija = $_POST['kj_govorna_produkcija'];
						//$kj_pisanje = $_POST['kj_pisanje'];
						$kj_kandidatid = $_POST['kj_kandidatid'];
						$kj_kandidat_check = $_POST['kj_kandidat_check'];
				
						$kj_slusanje = $_POST['kj_znanje'];
						$kj_citanje = $_POST['kj_znanje'];
						$kj_govorna_interakcija = $_POST['kj_znanje'];
						$kj_govorna_produkcija = $_POST['kj_znanje'];
						$kj_pisanje = $_POST['kj_znanje'];
				
				
						// NJEMACKI JEZIK
				
						$kj_naziv_njemacki = "Njemački";
						$kj_znanje_njemacki = $_POST['kj_znanje_njemacki'];
				
						if($_POST['kj_znanje_njemacki'] != NULL){
						// Add language knowlege
						$query_njem = $db->prepare("
										INSERT INTO idk_kandidat_jezici
											(kj_naziv, kj_slusanje, kj_citanje, kj_govorna_interakcija, kj_govorna_produkcija, kj_pisanje, kj_kandidatid)
										VALUES
											(:kj_naziv, :kj_slusanje, :kj_citanje, :kj_govorna_interakcija, :kj_govorna_produkcija, :kj_pisanje, :kj_kandidatid)");
				
						$query_njem->execute(array(
									':kj_naziv' => $kj_naziv_njemacki,
									':kj_slusanje' => $kj_znanje_njemacki,
									':kj_citanje' => $kj_znanje_njemacki,
									':kj_govorna_interakcija' => $kj_znanje_njemacki,
									':kj_govorna_produkcija' => $kj_znanje_njemacki,
									':kj_pisanje' => $kj_znanje_njemacki,
									':kj_kandidatid' => $kj_kandidatid));
						}
				
						if($_POST['kj_naziv'] != NULL){
						// Add language knowlege
						$query = $db->prepare("
										INSERT INTO idk_kandidat_jezici
											(kj_naziv, kj_slusanje, kj_citanje, kj_govorna_interakcija, kj_govorna_produkcija, kj_pisanje, kj_kandidatid)
										VALUES
											(:kj_naziv, :kj_slusanje, :kj_citanje, :kj_govorna_interakcija, :kj_govorna_produkcija, :kj_pisanje, :kj_kandidatid)");
				
						$query->execute(array(
									':kj_naziv' => $kj_naziv,
									':kj_slusanje' => $kj_slusanje,
									':kj_citanje' => $kj_citanje,
									':kj_govorna_interakcija' => $kj_govorna_interakcija,
									':kj_govorna_produkcija' => $kj_govorna_produkcija,
									':kj_pisanje' => $kj_pisanje,
									':kj_kandidatid' => $kj_kandidatid));
						}
				
						header("Location: registracija/korak6/$kj_kandidatid/$kj_kandidat_check");
					} else echo "Something went wrong, please try again";
				break;
				
				case "checkCandidateInputs":
				
					$kandidat_id = $_POST['kand_id_ajax'];
					checkCandidateInputs($kandidat_id);
				
				break;

				case "getLanguageForLink":
					$link_id = $_POST['link_id'];
					$get_lang = $db->prepare("SELECT lg_language FROM idk_link_generator WHERE lg_id = $link_id");
					$get_lang->execute();
					$row_lang = $get_lang->fetch();
		
					echo $row_lang['lg_language'];
				
				break;
				
				case "add_kandidat_doc_form":
						 // Build POST request:
					/*$recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
					$recaptcha_secret = '6LfSU_cUAAAAAAZolZBCR5u5zfIZfl2roL3irH8_';
					$recaptcha_response = $_POST['recaptcha_response'];
					// Make and decode POST request:
					$recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
					$recaptcha = json_decode($recaptcha);
					*/
					// Take action based on the score returned:
					//if ($recaptcha->score >= 0.3) {
					$document_dataid = $_POST['document_dataid'];
					$kandidat_id = $_POST['kandidat_id'];
					$kandidat_check = $_POST['kandidat_check'];
				
					//Upload document
					$document_file = $_FILES['document_file'];
				
					//File properties
					$file_name = $document_file['name'];
					$file_tmp = $document_file['tmp_name'];
				
					//File extension
					$file_ext = explode('.', $file_name);
					$file_ext = strtolower(end($file_ext));
				
					$allowed = array('jpg', 'jpeg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png');
				
					if(in_array($file_ext, $allowed)) {
				
						$file_name_new = uniqid() . '.' . $file_ext;
						$file_destination = 'files/kandidati_doc/' . $file_name_new;
				
						if(move_uploaded_file($file_tmp, $file_destination)){}
					}
				
					$document_name = $_POST['document_name'];
					$document_desc = $_POST['document_desc'];
					$document_datetime = date('Y-m-d H:i:s');
					$document_group = 2;
				
					$query = $db->prepare("
									INSERT INTO idk_documents
										(document_name, document_desc, document_file, document_icon, document_datetime, document_group, document_dataid, document_employeeid)
									VALUES
										(:document_name, :document_desc, :document_file, :document_icon, :document_datetime, :document_group, :document_dataid, :document_employeeid)");
				
					$query->execute(array(
									':document_name' => $document_name,
									':document_desc' => $document_desc,
									':document_file' => $file_name_new,
									':document_icon' => $file_ext,
									':document_datetime' => $document_datetime,
									':document_group' => $document_group,
									':document_dataid' => $document_dataid,
									':document_employeeid' => 0));
				
					//DA LI JE UNESENA SLIKA NA PRVOM KORAKU, AKO NE DODAJ IZ DOC-a
					
					
					$query_check_photo = $db->prepare("
									SELECT kandidat_slika FROM idk_kandidati WHERE kandidat_id = $document_dataid
					");
					
					$query_check_photo->execute();
					$query_check_photo_row = $query_check_photo->fetch();		
					$slika_check = $query_check_photo_row['kandidat_slika'];
					
					if($document_name == "Slika" && $slika_check == "none"){
						
						//uploadProfilneslike($document_file, $file_name, $file_tmp, $document_dataid);
						
						$file_destination2 = 'files/kandidati/' . $file_name_new;
						
						copy($file_destination, $file_destination2);
						// if(move_uploaded_file($file_tmp, $file_destination2)) {
		
							// $path_to_image_directory = "files/kandidati/";
							// $final_width_of_image = 660;
		
							// if(preg_match('/[.](jpg)$/', $file_name_new)) {
								// $im = imagecreatefromjpeg($path_to_image_directory . $file_name_new);
							// } else if (preg_match('/[.](png)$/', $file_name_new)) {
								// $im = imagecreatefrompng($path_to_image_directory . $file_name_new);
							// }
		
							// $ox = imagesx($im);
							// $oy = imagesy($im);
							// $nx = $final_width_of_image;
							// $ny = floor($oy * ($final_width_of_image / $ox));
							// $nm = imagecreatetruecolor($nx, $ny);
		
							// imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);
							// imagejpeg($nm, $path_to_image_directory . $file_name_new);
		
						// }
						$query_update_photo = $db->prepare("
									UPDATE idk_kandidati
									SET kandidat_slika = :kandidat_slika
									WHERE kandidat_id = :kandidat_id
						");
						$query_update_photo->execute(array(
									'kandidat_slika' => $file_name_new,
									'kandidat_id' => $document_dataid
						));
					}
					
					//Add to Search
					$search_text = "Dokument: " . $document_name . "";
					$search_tags = $document_name . " " . $document_desc;
					$search_link = "files/kandidati_doc/$file_name_new";
					$search_query = $db->prepare("
											INSERT INTO idk_search
												(search_text, search_tags, search_link)
											VALUES
												(:search_text, :search_tags, :search_link)");
					$search_query->execute(array(
										':search_text' => $search_text,
										':search_tags' => $search_tags,
										':search_link' => $search_link));
				
				
				
					if($kandidat_check != "0"){
						header("Location: registracija/korak7/$kandidat_id/$kandidat_check");
					}
					//} else echo "Something went wrong, try again";
				
				break;

				case "dupla_prijava":
				
					if(isset($_GET['poruka'])) {
						$poruka = $_GET['poruka'];
					}else{
						$poruka = 0;
					}
					
					if($poruka == 1){
						echo '<div class="alert material-alert material-alert_danger">Zahvaljujemo na interesovanju, Vaša prijava je zabilježena u našem sistemu. Za dodatna pitanja obratite nam se na telefon +387 33 821306.</div>';
					}
					
				break;
				
				case "slanje_sbota":
						$kandidat_id = $_POST['kandidat_id'];
						$povijest = $_POST['povijest'];
						$sms_bot_crm = $_POST['sms_bot_crm'];
						
						// var_dump($kandidat_id);
						// var_dump($povijest);
						// var_dump($sms_bot_crm);
						
						//UPDATE povezan_na_dipl u tabeli idk_kandidati
						$update_query = $db->prepare("
									UPDATE idk_kandidati
									SET povezan_na_dipl = :povezan_na_dipl
									WHERE kandidat_id = :kandidat_id
						");
						
						$update_query->execute(array(
									'kandidat_id' => $kandidat_id,
									'povezan_na_dipl' => 1
									));
						
						CopyKandidatinDipl($kandidat_id, $povijest, $sms_bot_crm);
				break;
				
				case "mail_dipl_da":
						$kandidat_id = $_POST['kandidat_id'];
						$link = "https://crm.job-step.com/kandidati?page=open&id=".$kandidat_id;
						$mail = new PHPMailer;
						$mail->isSMTP();
						//try {
							$mail->Host = 'smtp.gmail.com';                 // Specify main and backup SMTP servers
							$mail->SMTPAuth = true;                         // Enable SMTP authentication
							$mail->Username = 'support@job-step.com';        // SMTP username
							$mail->Password = 'eooc nnxo aylp lqkh';          // SMTP password
							$mail->SMTPSecure = 'ssl';                      // Enable TLS encryption, `ssl` also accepted
							$mail->Port = 465;                              // TCP port to connect to
							$mail->CharSet = 'UTF-8';

							//Recipients
							$mail->setFrom('support@job-step.com', 'JobStep');
							$mail->addAddress('e.bender@job-step.com', 'support@job-step.com');		// Add a recipient
							
							$mail->Subject ="DIPL";
							$mail->Body    = "Slijedeci kandidat je oznacio da ima nostrificiranu diplomu: ".$link;
							$mail->AltBody = "";

							if(!$mail->send()) {
								echo 'Message could not be sent.';
								echo 'Mailer Error: ' . $mail->ErrorInfo;
							}else{
								echo "Mail sent";
							}
						/*} catch (phpmailerException $e) {
							echo $e->errorMessage(); //Pretty error messages from PHPMailer
						}*/
				break;
				
				case "sendToDiplFromSvezaVizu":
					
					$id_dak = $_POST["id"]; 
					$dipl_da_ne_app = $_POST["dipl"];
					
					//Uzimanje podataka iz idk_dak_kandidati
					$info_idk_dak = $db->prepare("
												SELECT
													name_dak_kandidat,
													lastname_dak_kandidat,
													email_dak_kandidat,
													tel_dak_kandidat
												FROM 
													idk_dak_kandidati
												WHERE 
													id_dak_kandidat = :id_dak_kandidat
												");
					$info_idk_dak->execute(array(
												':id_dak_kandidat' => $id_dak
												));
					$info_dak_row = $info_idk_dak->fetch();
				
					$ime_new_ND_cand1 = $info_dak_row['name_dak_kandidat'];
					$prezime_new_ND_cand1 = $info_dak_row['lastname_dak_kandidat'];
					$email_new_ND_cand1 = $info_dak_row['email_dak_kandidat'];
					$mobilni_new_ND_cand1 = $info_dak_row['tel_dak_kandidat'];
					
					if($dipl_da_ne_app == 1){
						//Provjera da li se nalazi u DIPL VEC
						$provjera_dipl = $db->prepare("
												SELECT id_broj_nd_kandidata, zaduzen_zaposlenik_nd_kandidata
												FROM idk_nd_kandidata
												WHERE mobilni_nd_kandidata = :mobilni_nd_kandidata
												");
						$provjera_dipl->execute(array(
												":mobilni_nd_kandidata" => $mobilni_new_ND_cand1
											));
											
						$dipl_da_ne = $provjera_dipl->rowCount();
						$provjera_dipl_row = $provjera_dipl->fetch();
						$kandidat_id_vec_u_dipl = $provjera_dipl_row['id_broj_nd_kandidata'];
						$zaduzen_id_vec_u_dipl = $provjera_dipl_row['zaduzen_zaposlenik_nd_kandidata'];
						if($dipl_da_ne == 0){
							$kandidat_id_nd = CopyDAKinDipl($id_dak, 2, 1);
							$pov_na_dipl = 1;
						}else{
							//Ako se kandidat nalazi u diplu
							$kandidat_id_nd = $kandidat_id_vec_u_dipl;
							$pov_na_dipl = 1;
							$desila_se_prijava = insertPonovnePrijaveDIPL($kandidat_id_vec_u_dipl, $ime_new_ND_cand1, $prezime_new_ND_cand1, $mobilni_new_ND_cand1, $email_new_ND_cand1, 2, 1, NULL);
							if($desila_se_prijava == 1){
								/*
								//Slanje maila starom menadzeru sa linkom kandidata njemu dodjeljenog
								$user_query = $db->prepare("
														SELECT employee_firstname, employee_lastname, employee_email
														FROM idk_employees
														WHERE employee_id = :employee_id");

								$user_query->execute(array(
												':employee_id' => $zaduzen_id_vec_u_dipl));

								$user = $user_query->fetch();

								$employee_firstname = $user['employee_firstname'];
								$employee_lastname = $user['employee_lastname'];
								$employee_email = $user['employee_email'];

								//Send email to user
								$mail_email = $employee_email;
								$mail_name = $employee_firstname . ' ' . $employee_lastname;
								$mail_subject = "Dipl modul - Stari kandidat";
								$mail_url = "" . getSiteUrlr() . "/nostrifikacija_diploma?page=otvori_ND_kandidata&id=".$kandidat_id_vec_u_dipl." ";
								$mail_body = "
												<p>Vaš stari kandidat u navedenom linku, pokušao se ponovno prijaviti za nostrifikaciju diplome preko aplikacije Sve za vizu.</p>
												<p>Detalje pogledajte na linku: " . $mail_url . "</p>
								";
								$mail_altbody = "
												<p>Vaš stari kandidat u navedenom linku, pokušao se ponovno prijaviti za nostrifikaciju diplome preko aplikacije Sve za vizu.</p>
												<p>Detalje pogledajte na linku: " . $mail_url . "</p>
								";
								
								sendEmail($mail_email, $mail_name, $mail_subject, $mail_body, $mail_altbody);
								*/
							}
						}
					}
					else{
						$kandidat_id_nd = 0;
						$pov_na_dipl = 0;
					}
					
					//Provjera da li se nalazi u kandidatima
					$provjera_kandidati = $db->prepare("
									SELECT kandidat_id
									FROM idk_kandidati
									WHERE kandidat_mobitel = :kandidat_mobitel
									");
					$provjera_kandidati->execute(array(
											":kandidat_mobitel" => $mobilni_new_ND_cand1
										));
					$kandidati_da_ne = $provjera_kandidati->rowCount();
					$provjera_kandidati_row = $provjera_kandidati->fetch();
					$kandidat_id_vec_u_kan = $provjera_kandidati_row['kandidat_id'];
					if($kandidati_da_ne == 0){ 
					//Dodavanje u tabelu IDK_KANDIDATI START
						$kandidat_check = md5(uniqid(rand(), true));
						
						//Add user to db
						$query_add_user = $db->prepare("
										INSERT INTO idk_kandidati
											(
												kandidat_check,
												kandidat_ime,
												kandidat_prezime, 
												kandidat_email, 
												kandidat_mobitel, 
												kandidat_slika, 
												kandidat_status, 
												kandidat_status_messenger, 
												kandidat_datetime, 
												kandidat_visitedurl, 
												kandidat_prijava_na, 
												kandidat_group,
												povezan_na_dipl,
												kandidat_porijeklo,
												kandidat_dipl_id 
											)
										VALUES
											(
												:kandidat_check, 
												:kandidat_ime, 
												:kandidat_prezime, 
												:kandidat_email, 
												:kandidat_mobitel, 
												:kandidat_slika, 
												:kandidat_status, 
												:kandidat_status_messenger, 
												:kandidat_datetime, 
												:kandidat_visitedurl, 
												:kandidat_prijava_na, 
												:kandidat_group,
												:povezan_na_dipl,
												:kandidat_porijeklo,
												:kandidat_dipl_id
											)");
				
						$query_add_user->execute(array(
									':kandidat_check' => $kandidat_check,
									':kandidat_ime' => $ime_new_ND_cand1,
									':kandidat_prezime' => $prezime_new_ND_cand1,
									':kandidat_email' => $email_new_ND_cand1,
									':kandidat_mobitel' => $mobilni_new_ND_cand1,
									':kandidat_slika' => "none",
									':kandidat_status' => 0,
									':kandidat_status_messenger' => 1,
									':kandidat_datetime' => date('Y-m-d H:i:s'),
									':kandidat_visitedurl' => 1,
									':kandidat_prijava_na' => "Ostalo",
									':kandidat_group' => 7,
									':povezan_na_dipl' => $pov_na_dipl,
									':kandidat_porijeklo' => 2,
									':kandidat_dipl_id' => $kandidat_id_nd
									));
						$kandidat_id = $db->lastInsertId();
						
						$query_log_status = $db->prepare("
								INSERT INTO idk_log_kandidat_statusi
									(lks_kandidat_id, lks_status_obrade, lks_status_messenger, lks_datetime)
								VALUES
									(:lks_kandidat_id, :lks_status_obrade, :lks_status_messenger, :lks_datetime)
						");
						
						$query_log_status->execute(array(
								':lks_kandidat_id' => $kandidat_id,
								':lks_status_obrade' => 0,
								':lks_status_messenger' => 1,
								':lks_datetime' => date('Y-m-d H:i:s')
						));
						
						$nalog_query = $db->prepare("
												SELECT project_id
												FROM idk_projects
												WHERE (project_name LIKE '%Kandidati sa DAK%') ");
					
						$nalog_query->execute();
					
						$nalogrow = $nalog_query->fetch();
						
						$project_id = $nalogrow['project_id'];
						$query_project = $db->prepare("
										INSERT INTO idk_project_kandidati
											(pk_projectid, pk_kandidatid)
										VALUES
											(:pk_projectid, :pk_kandidatid)");

						$query_project->execute(array(
										':pk_projectid' => $project_id,
										':pk_kandidatid' => $kandidat_id));
										
						$kki_grupa = 1;
						$kki_naziv = "Mobilni";

						//Add mobilni to db
						$query_mob1 = $db->prepare("
										INSERT INTO idk_kandidat_kontakt_info
											(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id)
										VALUES
											(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id)");

						$query_mob1->execute(array(
									':kki_grupa' => $kki_grupa,
									':kki_naziv' => $kki_naziv,
									':kki_podatak' => $mobilni_new_ND_cand1,
									':kki_kandidat_id' => $kandidat_id));
						
						$kki_grupa_e = 2;
						$kki_naziv_e = "E-mail";
						
						//Add kontakt info to db
						$query_email1 = $db->prepare("
										INSERT INTO idk_kandidat_kontakt_info
											(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id)
										VALUES
											(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id)");

						$query_email1->execute(array(
									':kki_grupa' => $kki_grupa_e,
									':kki_naziv' => $kki_naziv_e,
									':kki_podatak' => $email_new_ND_cand1,
									':kki_kandidat_id' => $kandidat_id));
						
						//Add to table users (chatbot)
						$random_string = generateRandomString();
						$options1 = [
							'cost' => 10,
						];
						$random_password = password_hash($random_string, PASSWORD_BCRYPT, $options1);
						
						$log_desc2 = "Kandidat: " . $ime_new_ND_cand1 . " " . $prezime_new_ND_cand1 . "(".$kandidat_id."). ; (".$random_string.")";
						$log_type2 = "5";
						addToLogs($log_desc2, $log_type2);
						
						$characters = '0123456789';
						$charactersLength = strlen($characters);
						$randomString = '';
						for ($i = 0; $i < 5; $i++) {
							$randomString .= $characters[rand(0, $charactersLength - 1)];
						}
						$bot_koriscnicko_ime = $ime_new_ND_cand1.$randomString;
						$kandidat_full_name = $ime_new_ND_cand1." ".$prezime_new_ND_cand1;
						$query_user = $db->prepare("
									INSERT INTO users
										(phone, name, nalog_id, email, password, kandidat_id)
									VALUES
										(:phone, :name, :nalog_id, :email, :password, :kandidat_id)");

						$query_user->execute(array(
									':phone' => $mobilni_new_ND_cand1,
									':name' => $kandidat_full_name,
									':nalog_id' => 44,
									':email' => $bot_koriscnicko_ime,
									':password' => $random_password,
									':kandidat_id' => $kandidat_id));
									
						$link_dload = "https://crm.job-step.com/download";
						$link_uputs = "https://bit.ly/3V177tF";
						include('lang/bs.php');
						$to_send_viber_poruka_1 = $kandidat_full_name.$text_viber_new_prva_poruka; 
						$to_send_sms_poruka_1 	= $kandidat_full_name.$text_sms_new_prva_poruka; 

						$to_send_viber_poruka_2 = $text_viber_new_druga_poruka;
						$to_send_sms_poruka_2 	= $text_sms_new_druga_poruka." ".$link_dload;

						$to_send_viber_poruka_3 = $text_viber_new_treca_poruka_1.'\n\n'.$text_viber_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_viber_new_treca_poruka_3.$random_string;
						$to_send_sms_poruka_3 	= $text_sms_new_treca_poruka_1.'\n\n'.$text_sms_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_sms_new_treca_poruka_3.$random_string;

						$to_send_viber_poruka_4 = $text_viber_new_cetvrta_poruka;
						$to_send_sms_poruka_4 	= $text_sms_new_cetvrta_poruka." ".$link_uputs;

						viberPrijava1($mobilni_new_ND_cand1, $to_send_sms_poruka_1, $to_send_viber_poruka_1);
						sleep(1);
						viberPrijava2($mobilni_new_ND_cand1, $to_send_sms_poruka_2, $to_send_viber_poruka_2, $text_btn_new_druga_poruka, $link_dload);
						sleep(1);
						viberPrijava1($mobilni_new_ND_cand1, $to_send_sms_poruka_3, $to_send_viber_poruka_3);
						sleep(1);
						viberPrijava2($mobilni_new_ND_cand1, $to_send_sms_poruka_4, $to_send_viber_poruka_4, $text_btn_new_cetvrta_poruka, $link_uputs);

						
						
						
						sendCandidateMessengerMail($random_string, $email_new_ND_cand1, $bot_koriscnicko_ime);
						
					//Dodavanje u tabelu IDK_KANDIDATI END
					}else{
						$update_pov_dipl = $db->prepare("
											UPDATE idk_kandidati
											SET povezan_na_dipl = :povezan_na_dipl, kandidat_dipl_id = :kandidat_dipl_id
											WHERE kandidat_id = :kandidat_id
											");
				
						$update_pov_dipl->execute(array( 
							':kandidat_id' => $kandidat_id_vec_u_kan,
							':povezan_na_dipl' => $pov_na_dipl,
							':kandidat_dipl_id' => $kandidat_id_nd
						));
					}
				break;
				
				case "formJSinDIPL":
					//Dodavanje kandidata sa JS stranice u Dipl modul
					//Prijavna forma
					?>		
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
							padding: 10px;
							padding-left: 15px;
							padding-right: 15px;
						}
						@media screen and (max-width: 991px) {
							.img-responsive{
								max-width: 100px !important;
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
						footer{
							position: absolute;
							left: 0;
							bottom: 0;
							width: 100%;
							border-top: 1px solid #ccc;
							font-size: 12px;
							text-align: center;
							margin-top: 0px;
						} 
					</style>
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
					<div class="content_box">
						<div class="row">
							<div class="col-md-12">
								<div class="row">
									<div class = "col-md-8 col-md-offset-2">
										<div class="row">
											<div id = "dizajn_vanjski_dio" class = "col-xs-12">
												<div class="row">
													<div class = "col-xs-12 text-center">
														Nostrifikacija diplome
													</div>
												</div>
												<div class="row">
													<div class = "col-xs-12">
														<form action="<?php getSiteURL(); ?>public_kandidati.php?page=saveJSinDIPL" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
															 <input type="hidden" name="recaptcha_response" id="recaptchaResponse">
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
																<label for="kandidat_telefon_dipl" class="col-sm-3 control-label"><span class="text-danger">*</span>Telefon</label>
																<div class="col-sm-9">
																	<div class="">
																		<input class="form-control materail-input" type="tel" name="kandidat_telefon_dipl" id="kandidat_telefon_dipl" autocomplete="off" required>
																	</div>
																</div>
															</div>
															<script>
															$( document ).ready(function($) {
																$.each($('#kandidat_telefon_dipl'),function(){
																	var telInput = $(this);
																	if ($(this).val().startsWith("+") || $(this).val() == '') {
																	$(telInput).intlTelInput({
																		utilsScript:'https://intl-tel-input.com/node_modules/intl-tel-input/build/js/utils.js',
																		autoPlaceholder: "aggressive",
																		initialCountry: "ba",
																		formatOnDisplay: true,
																		preferredCountries: ["ba","rs","hr","de"],
																		separateDialCode: true
																	});
																	}
																});
																//SPAJANJE COUNTRY CODA I TELEFONSKOG BROJA
																$("form").submit(function(event) {
																	//event.preventDefault();
																	$.each($('#kandidat_telefon_dipl'),function(){
																		var telInput = $(this);	
																		var telType = telInput.data('type');	
																		telInput.val(telInput.intlTelInput("getNumber"));  
																	});
																});	
															});
															</script>
															<div class="form-group">
																<label for="kandidat_email_dipl" class="col-sm-3 control-label"><span class="text-danger">*</span>Email:</label>
																<div class="col-sm-9">
																	<div class="materail-input-block materail-input-block_success">
																		<input class="form-control materail-input" type="email" name="kandidat_email_dipl" id="kandidat_email_dipl" autocomplete="off" placeholder="E-mail" required>
																		<span class="materail-input-block__line"></span>
																	</div>
																</div>
															</div>
															<div class="form-group">
																<div class="col-sm-offset-4 col-sm-4 text-center">
																	<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-sign-in" aria-hidden="true"></i> <span>Pošalji prijavu</span></button>
																	<br/><br/><small>Sva polja označena sa <span class="text-danger">*</span> su obavezna!</small>
																</div>
															</div>
														</form>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php			
				break;
				
				case "saveJSinDIPL":
					 // Build POST request:
					// $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
					// $recaptcha_secret = '6LfSU_cUAAAAAAZolZBCR5u5zfIZfl2roL3irH8_';
					// $recaptcha_response = $_POST['recaptcha_response'];
					// // Make and decode POST request:
					// $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
					// $recaptcha = json_decode($recaptcha);
					// // Take action based on the score returned:
					// if ($recaptcha->score >= 0) {
						$kandidat_ime = $_POST["kandidat_ime_dipl"];
						$kandidat_prezime = $_POST["kandidat_prezime_dipl"];
						$kandidat_broj_tel = $_POST["kandidat_telefon_dipl"];
						$kandidat_email = $_POST["kandidat_email_dipl"];
						
						//Provjera da li se nalazi u DIPL VEC
						$provjera_dipl = $db->prepare("
												SELECT id_broj_nd_kandidata, zaduzen_zaposlenik_nd_kandidata
												FROM idk_nd_kandidata
												WHERE mobilni_nd_kandidata = :mobilni_nd_kandidata
												");
						$provjera_dipl->execute(array(
												":mobilni_nd_kandidata" => $kandidat_broj_tel
											));
						$dipl_da_ne = $provjera_dipl->rowCount();
						$provjera_dipl_row = $provjera_dipl->fetch();
						$kandidat_id_vec_u_dipl = $provjera_dipl_row['id_broj_nd_kandidata'];
						$zaduzen_id_vec_u_dipl = $provjera_dipl_row['zaduzen_zaposlenik_nd_kandidata'];
						if($dipl_da_ne == 0){
							//Ako se kandidat ne nalazi u diplu
								$menager = getDodjeliAgentuDIPLR($kandidat_broj_tel, 0, "A");
								//REZULTAT ALGORITMA 
								$zadnji_menager = $menager['stari'];
								$novi_menager = $menager['novi'];
								$novi_menager_team = getTeamIdByEmployee($novi_menager);
								
								$ime_new_ND_cand1 = $kandidat_ime;
								$prezime_new_ND_cand1 = $kandidat_prezime;
								$email_new_ND_cand1 = $kandidat_email;
								$mobilni_new_ND_cand1 = $kandidat_broj_tel;
								//UPDATE kandidata u Dipl
								$new_ND_kandidat = $db->prepare("
												INSERT INTO idk_nd_kandidata
													(
														ime_nd_kandidata, 
														prezime_nd_kandidata,
														mobilni_nd_kandidata, 
														email_nd_kandidata, 
														vrijeme_kreiranja_nd_kandidata,
														zaduzen_zaposlenik_nd_kandidata, 
														tim_nd_kandidata, 
														status_nd_kandidata,
														povijest_nd_kandidata,
														povijest_vrsta_nd_kandidata,
														kandidat_idd
													)
												VALUES
													(
														:ime_nd_kandidata, 
														:prezime_nd_kandidata,
														:mobilni_nd_kandidata, 
														:email_nd_kandidata, 
														:vrijeme_kreiranja_nd_kandidata,
														:zaduzen_zaposlenik_nd_kandidata, 
														:tim_nd_kandidata, 
														:status_nd_kandidata,
														:povijest_nd_kandidata,
														:povijest_vrsta_nd_kandidata,
														:kandidat_idd
													)");

								$new_ND_kandidat->execute(array(
												':ime_nd_kandidata' => $ime_new_ND_cand1,
												':prezime_nd_kandidata' => $prezime_new_ND_cand1,
												':mobilni_nd_kandidata' => $mobilni_new_ND_cand1,
												':email_nd_kandidata' => $email_new_ND_cand1,
												':vrijeme_kreiranja_nd_kandidata' => date('Y-m-d H:i:s'),
												':zaduzen_zaposlenik_nd_kandidata' => $novi_menager,
												':tim_nd_kandidata' => $novi_menager_team,
												':status_nd_kandidata' => 1,
												':povijest_nd_kandidata' => 4,
												':povijest_vrsta_nd_kandidata' => 1,
												':kandidat_idd' => NULL
												));
								//Get last ID
								$kandidat_id_nd = $db->lastInsertId();
								
								//Update statistike menadzera START
								// vrsta_aktivnosti == 0 -> oznacava da je sistem automatski dodjelio tom menadzeru kandidata
								$insert_statistike_menagera = $db->prepare("
											INSERT INTO idk_nd_menadzeri_statistike
												(idd_broj_nd_kandidata, zaduzen_zaposlenik_id, vrsta_aktivnosti, vrijeme_aktivnosti)
											VALUES
												(:idd_broj_nd_kandidata, :zaduzen_zaposlenik_id, :vrsta_aktivnosti, :vrijeme_aktivnosti)");

								$insert_statistike_menagera->execute(array(
											':idd_broj_nd_kandidata' => $kandidat_id_nd,
											':zaduzen_zaposlenik_id' => $novi_menager,
											':vrsta_aktivnosti' => 0,
											':vrijeme_aktivnosti' => date('Y-m-d H:i:s')
											));
								
								//Slanje maila novom menadzeru sa linkom kandidata njemu dodjeljenog
								/*$user_query = $db->prepare("
														SELECT employee_firstname, employee_lastname, employee_email
														FROM idk_employees
														WHERE employee_id = :employee_id");

								$user_query->execute(array(
												':employee_id' => $novi_menager));

								$user = $user_query->fetch();

								$employee_firstname = $user['employee_firstname'];
								$employee_lastname = $user['employee_lastname'];
								$employee_email = $user['employee_email'];

								//Send email to user
								$mail_email = $employee_email;
								$mail_name = $employee_firstname . ' ' . $employee_lastname;
								$mail_subject = "Dipl modul - Novi kandidat";
								$mail_url = "" . getSiteUrlr() . "/nostrifikacija_diploma?page=otvori_ND_kandidata&id=".$kandidat_id_nd." ";
								$mail_body = "
												<p>Zaduženi ste za novog kandidata Na DIPL modulu.</p>
												<p>Detalje pogledajte na linku: " . $mail_url . "</p>
								";
								$mail_altbody = "
												<p>Zaduženi ste za novog kandidata Na DIPL modulu.</p>
												<p>Detalje pogledajte na linku: " . $mail_url . "</p>
								";*/
								
								//Update log statusa ND kandidata
								$new_ND_status = $db->prepare("
													INSERT INTO idk_nd_kandidata_status_log
													(
														idd_broj_nd_kandidata,
														status_nd_kandidata,
														vrijeme_promjene_statusa_nd_kandidata
													)
													VALUES
													(
														:idd_broj_nd_kandidata,
														:status_nd_kandidata,
														:vrijeme_promjene_statusa_nd_kandidata
													)
								");
								$new_ND_status->execute(array(
												':idd_broj_nd_kandidata' => $kandidat_id_nd,
												':status_nd_kandidata' => 1,
												':vrijeme_promjene_statusa_nd_kandidata' => date('Y-m-d H:i:s')
												));
								
								//sendEmail($mail_email, $mail_name, $mail_subject, $mail_body, $mail_altbody);		
								//ADD TO LOGS START
									$log_date = date('Y-m-d H:i:s');
									$log_desc = "DIPL -> Dodan novi kandidat sa ID = [".$kandidat_id_nd."] sa stranice JobStep.net - Zadužen zaposlenik: ".$novi_menager." ";
									$log_query = $db->prepare("
													INSERT INTO idk_logs 
														(log_employeeid, log_desc, log_date)
													VALUES
														(:log_employeeid, :log_desc, :log_date)");

									$log_query->execute(array(
													':log_employeeid' => $logged_employee_id,
													':log_desc' => $log_desc,
													':log_date' => $log_date));
								//ADD TO LOGS END
								
								//Provjera da li se nalazi u kandidatima
								$provjera_kandidati = $db->prepare("
												SELECT kandidat_id
												FROM idk_kandidati
												WHERE kandidat_mobitel = :kandidat_mobitel AND kandidat_status != :kandidat_status
												");
								$provjera_kandidati->execute(array(
														":kandidat_mobitel" => $kandidat_broj_tel,
														":kandidat_status" => 3
													));
								$kandidati_da_ne = $provjera_kandidati->rowCount();
								$provjera_kandidati_row = $provjera_kandidati->fetch();
								$kandidat_id_vec_u_kan = $provjera_kandidati_row['kandidat_id'];
								if($kandidati_da_ne == 0){
									//DODAVANJE U TABELU IDK_KANDIDATI
									
									$kandidat_check = md5(uniqid(rand(), true));
									
									//Add user to db
									$query_add_user = $db->prepare("
													INSERT INTO idk_kandidati
														(kandidat_check, kandidat_ime, kandidat_prezime, kandidat_email, kandidat_mobitel, kandidat_slika, kandidat_status, kandidat_status_messenger, kandidat_status_prijave, kandidat_datetime, kandidat_visitedurl, kandidat_prijava_na, kandidat_group, povezan_na_dipl, kandidat_porijeklo, kandidat_dipl_id )
													VALUES
														(:kandidat_check, :kandidat_ime, :kandidat_prezime, :kandidat_email, :kandidat_mobitel, :kandidat_slika, :kandidat_status, :kandidat_status_messenger, :kandidat_status_prijave, :kandidat_datetime, :kandidat_visitedurl, :kandidat_prijava_na, :kandidat_group, :povezan_na_dipl, :kandidat_porijeklo, :kandidat_dipl_id)");
							
									$query_add_user->execute(array(
												':kandidat_check' => $kandidat_check,
												':kandidat_ime' => $ime_new_ND_cand1,
												':kandidat_prezime' => $prezime_new_ND_cand1,
												':kandidat_email' => $email_new_ND_cand1,
												':kandidat_mobitel' => $mobilni_new_ND_cand1,
												':kandidat_slika' => "none",
												':kandidat_status' => 0,
												':kandidat_status_messenger' => 1,
												':kandidat_status_prijave' => 1,
												':kandidat_datetime' => date('Y-m-d H:i:s'),
												':kandidat_visitedurl' => 1,
												':kandidat_prijava_na' => "Ostalo",
												':kandidat_group' => 7,
												':povezan_na_dipl' => 1,
												':kandidat_porijeklo' => 5,
												':kandidat_dipl_id' => $kandidat_id_nd
												));
												
									$kandidat_id = $db->lastInsertId();
									//UPDATE ND_KANDIDATA STAVITI OVAJ ID 
									
									$query_log_status = $db->prepare("
											INSERT INTO idk_log_kandidat_statusi
												(lks_kandidat_id, lks_status_obrade, lks_status_messenger, lks_datetime)
											VALUES
												(:lks_kandidat_id, :lks_status_obrade, :lks_status_messenger, :lks_datetime)
									");
									
									$query_log_status->execute(array(
											':lks_kandidat_id' => $kandidat_id,
											':lks_status_obrade' => 0,
											':lks_status_messenger' => 1,
											':lks_datetime' => date('Y-m-d H:i:s')
									));
									
									$nalog_query = $db->prepare("
															SELECT project_id
															FROM idk_projects
															WHERE (project_name LIKE '%Kandidati sa stranice JobStep%') ");
								
									$nalog_query->execute();
								
									$nalogrow = $nalog_query->fetch();
									
									$project_id = $nalogrow['project_id'];
									$query_project = $db->prepare("
													INSERT INTO idk_project_kandidati
														(pk_projectid, pk_kandidatid)
													VALUES
														(:pk_projectid, :pk_kandidatid)");

									$query_project->execute(array(
													':pk_projectid' => $project_id,
													':pk_kandidatid' => $kandidat_id));
													
									addToLogsStatusPrijave(NULL, $project_id, 1, $kandidat_id, 3);
													
									$kki_grupa = 1;
									$kki_naziv = "Mobilni";

									//Add mobilni to db
									$query_mob = $db->prepare("
													INSERT INTO idk_kandidat_kontakt_info
														(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id)
													VALUES
														(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id)");

									$query_mob->execute(array(
												':kki_grupa' => $kki_grupa,
												':kki_naziv' => $kki_naziv,
												':kki_podatak' => $mobilni_new_ND_cand1,
												':kki_kandidat_id' => $kandidat_id));
									
									$kki_grupa_e = 2;
									$kki_naziv_e = "E-mail";
									
									//Add kontakt info to db
									$query_email = $db->prepare("
													INSERT INTO idk_kandidat_kontakt_info
														(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id)
													VALUES
														(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id)");

									$query_email->execute(array(
												':kki_grupa' => $kki_grupa_e,
												':kki_naziv' => $kki_naziv_e,
												':kki_podatak' => $email_new_ND_cand1,
												':kki_kandidat_id' => $kandidat_id));
									
									//Add to table users (chatbot)
									$random_string = generateRandomString();
									$options = [
										'cost' => 10,
									];
									$random_password = password_hash($random_string, PASSWORD_BCRYPT, $options);
									
									$log_desc2 = "Kandidat: " . $ime_new_ND_cand1 . " " . $prezime_new_ND_cand1 . "(".$kandidat_id."). ; (".$random_string.")";
									$log_type2 = "5";
									addToLogs($log_desc2, $log_type2);
									
									$characters = '0123456789';
									$charactersLength = strlen($characters);
									$randomString = '';
									for ($i = 0; $i < 5; $i++) {
										$randomString .= $characters[rand(0, $charactersLength - 1)];
									}
									$bot_koriscnicko_ime = $ime_new_ND_cand1.$randomString;
									$kandidat_full_name = $ime_new_ND_cand1." ".$prezime_new_ND_cand1;
									$query_user = $db->prepare("
												INSERT INTO users
													(phone, name, nalog_id, email, password, kandidat_id)
												VALUES
													(:phone, :name, :nalog_id, :email, :password, :kandidat_id)");

									$query_user->execute(array(
												':phone' => $mobilni_new_ND_cand1,
												':name' => $kandidat_full_name,
												':nalog_id' => 44,
												':email' => $bot_koriscnicko_ime,
												':password' => $random_password,
												':kandidat_id' => $kandidat_id));
												
									$link_dload = "https://crm.job-step.com/download";
									$link_uputs = "https://bit.ly/3V177tF";
									include('lang/bs.php');
									$to_send_viber_poruka_1 = $kandidat_full_name.$text_viber_new_prva_poruka; 
									$to_send_sms_poruka_1 	= $kandidat_full_name.$text_sms_new_prva_poruka; 

									$to_send_viber_poruka_2 = $text_viber_new_druga_poruka;
									$to_send_sms_poruka_2 	= $text_sms_new_druga_poruka." ".$link_dload;

									$to_send_viber_poruka_3 = $text_viber_new_treca_poruka_1.'\n\n'.$text_viber_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_viber_new_treca_poruka_3.$random_string;
									$to_send_sms_poruka_3 	= $text_sms_new_treca_poruka_1.'\n\n'.$text_sms_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_sms_new_treca_poruka_3.$random_string;

									$to_send_viber_poruka_4 = $text_viber_new_cetvrta_poruka;
									$to_send_sms_poruka_4 	= $text_sms_new_cetvrta_poruka." ".$link_uputs;

									viberPrijava1($mobilni_new_ND_cand1, $to_send_sms_poruka_1, $to_send_viber_poruka_1);
									sleep(1);
									viberPrijava2($mobilni_new_ND_cand1, $to_send_sms_poruka_2, $to_send_viber_poruka_2, $text_btn_new_druga_poruka, $link_dload);
									sleep(1);
									viberPrijava1($mobilni_new_ND_cand1, $to_send_sms_poruka_3, $to_send_viber_poruka_3);
									sleep(1);
									viberPrijava2($mobilni_new_ND_cand1, $to_send_sms_poruka_4, $to_send_viber_poruka_4, $text_btn_new_cetvrta_poruka, $link_uputs);

									//KRAJ DODAVANJA U TABELU KANDIDATI
								}
								else{
									$update_query_pov_dipl = $db->prepare("
											UPDATE idk_kandidati
											SET povezan_na_dipl = :povezan_na_dipl, kandidat_dipl_id = :kandidat_dipl_id
											WHERE kandidat_id = :kandidat_id
									");
									
									$update_query_pov_dipl->execute(array(
												':kandidat_id' => $kandidat_id_vec_u_kan,
												':povezan_na_dipl' => 1,
												':kandidat_dipl_id' => $kandidat_id_nd
												));
								}
						}
						else{
							//Provjera da li se nalazi u kandidatima
								$provjera_kandidati = $db->prepare("
												SELECT kandidat_id
												FROM idk_kandidati
												WHERE kandidat_mobitel = :kandidat_mobitel AND kandidat_status != :kandidat_status
												");
								$provjera_kandidati->execute(array(
														":kandidat_mobitel" => $kandidat_broj_tel,
														":kandidat_status" => 3
													));
								$kandidati_da_ne = $provjera_kandidati->rowCount();
								$provjera_kandidati_row = $provjera_kandidati->fetch();
								$kandidat_id_vec_u_kan = $provjera_kandidati_row['kandidat_id'];
								if($kandidati_da_ne != 0){
									$update_query_pov_dipl = $db->prepare("
											UPDATE idk_kandidati
											SET povezan_na_dipl = :povezan_na_dipl, kandidat_dipl_id = :kandidat_dipl_id
											WHERE kandidat_id = :kandidat_id
									");
									
									$update_query_pov_dipl->execute(array(
												':kandidat_id' => $kandidat_id_vec_u_kan,
												':povezan_na_dipl' => 1,
												':kandidat_dipl_id' => $kandidat_id_vec_u_dipl
												));
								}
							//Ako se kandidat nalazi u diplu
							$desila_se_prijava = insertPonovnePrijaveDIPL($kandidat_id_vec_u_dipl, $kandidat_ime, $kandidat_prezime, $kandidat_broj_tel, $kandidat_email, 4, 1, NULL);
							if($desila_se_prijava == 1){
								/*
								//Slanje maila starom menadzeru sa linkom kandidata njemu dodjeljenog
								$user_query = $db->prepare("
														SELECT employee_firstname, employee_lastname, employee_email
														FROM idk_employees
														WHERE employee_id = :employee_id");

								$user_query->execute(array(
												':employee_id' => $zaduzen_id_vec_u_dipl));

								$user = $user_query->fetch();

								$employee_firstname = $user['employee_firstname'];
								$employee_lastname = $user['employee_lastname'];
								$employee_email = $user['employee_email'];

								//Send email to user
								$mail_email = $employee_email;
								$mail_name = $employee_firstname . ' ' . $employee_lastname;
								$mail_subject = "Dipl modul - Stari kandidat";
								$mail_url = "" . getSiteUrlr() . "/nostrifikacija_diploma?page=otvori_ND_kandidata&id=".$kandidat_id_vec_u_dipl." ";
								$mail_body = "
												<p>Vaš stari kandidat u navedenom linku, pokušao se ponovno prijaviti za nostrifikaciju diplome putem linka na stranici JobStep.</p>
												<p>Detalje pogledajte na linku: " . $mail_url . "</p>
								";
								$mail_altbody = "
												<p>Vaš stari kandidat u navedenom linku, pokušao se ponovno prijaviti za nostrifikaciju diplome putem linka na stranici JobStep..</p>
												<p>Detalje pogledajte na linku: " . $mail_url . "</p>
								";
								
								sendEmail($mail_email, $mail_name, $mail_subject, $mail_body, $mail_altbody);
								*/
							}
						}
						header("Location: " . getSiteURL() . "nostrifikacija/completed");
					// }else echo "Something went wrong, please try again";
				break;
				
				case "prijavaDIPLK":
				 // Build POST request:
				 // if(isset($_POST['recaptcha_response'])){
					// $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
					// $recaptcha_secret = '6LdZmAIaAAAAAMXI2zDBtAnUKtAo8mAJZKDuV84H';
					// $recaptcha_response = $_POST['recaptcha_response'];
					// // Make and decode POST request:
					// $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
					// $recaptcha = json_decode($recaptcha);
					// // Take action based on the score returned:
					// if ($recaptcha->score >= 0) {
						include("lang/bs.php");
						$kamp = $_POST["kamp"];
						$kandidat_ime = $_POST["kandidat_ime_dipl"]; 
						$kandidat_prezime = $_POST["kandidat_prezime_dipl"];
						$kandidat_broj_tel = $_POST["kandidat_telefon_dipl"];
						if(isset($_POST['kandidat_email_dipl']))
							$kandidat_email  = $_POST['kandidat_email_dipl'];
						else
							$kandidat_email = null;
						
						// VALIDACIJA SE VRŠI NA FAJLU processing.php
						
						/*$dan_rodjenja = $_POST["datum_dan"];
						$mjesec_rodjenja = $_POST["datum_mjesec"];
						$godina_rodjenja = $_POST["datum_godina"];
						
						$cijeli_datum = new DateTime($godina_rodjenja."-".$mjesec_rodjenja."-".$dan_rodjenja);
						$kandidat_datumrodjenja = $cijeli_datum->format('Y-m-d');
						*/
						//Provjera da li se nalazi u DIPL VEC
						if($kamp == 95 || $kamp == 108){
							$provjera_dipl = $db->prepare("
													SELECT id_broj_nd_kandidata, zaduzen_zaposlenik_nd_kandidata
													FROM idk_nd_kandidata
													WHERE mobilni_nd_kandidata = :mobilni_nd_kandidata AND mobilni_nd_kandidata != '' AND povijest_nd_kandidata = 5 AND kampanja_id = :kampanja_id
													ORDER BY id_broj_nd_kandidata DESC
													");
							$provjera_dipl->execute(array(
													":mobilni_nd_kandidata" => $kandidat_broj_tel,
													":kampanja_id" => $kamp
												));
							$dipl_da_ne = $provjera_dipl->rowCount();
							$provjera_dipl_row = $provjera_dipl->fetch();
							$kandidat_id_vec_u_dipl = $provjera_dipl_row['id_broj_nd_kandidata'];
							$zaduzen_id_vec_u_dipl = $provjera_dipl_row['zaduzen_zaposlenik_nd_kandidata'];
						}else{
							$provjera_dipl = $db->prepare("
													SELECT id_broj_nd_kandidata, zaduzen_zaposlenik_nd_kandidata
													FROM idk_nd_kandidata
													WHERE mobilni_nd_kandidata = :mobilni_nd_kandidata AND mobilni_nd_kandidata != ''
													");
							$provjera_dipl->execute(array(
													":mobilni_nd_kandidata" => $kandidat_broj_tel
												));
							$dipl_da_ne = $provjera_dipl->rowCount();
							$provjera_dipl_row = $provjera_dipl->fetch();
							$kandidat_id_vec_u_dipl = $provjera_dipl_row['id_broj_nd_kandidata'];
							$zaduzen_id_vec_u_dipl = $provjera_dipl_row['zaduzen_zaposlenik_nd_kandidata'];
						}
						
						if($dipl_da_ne == 0){
							//Ako se kandidat ne nalazi u diplu
							//Update broja prijavljenih preko kampanje
							$query_upd = $db->prepare("
										UPDATE idk_kampanje_dipl
										SET kd_broj_prijavljenih = kd_broj_prijavljenih + 1
										WHERE kd_id = :kd_id");

							$query_upd->execute(array(
									':kd_id' => $kamp));
							
							$menager = getDodjeliAgentuDIPLR($kandidat_broj_tel, $kamp);
							//REZULTAT ALGORITMA 
							$zadnji_menager = $menager['stari'];
							$novi_menager = $menager['novi'];
							$novi_menager_team = getTeamIdByEmployee($novi_menager);
							
							$ime_new_ND_cand1 = $kandidat_ime;
							$prezime_new_ND_cand1 = $kandidat_prezime;
							$email_new_ND_cand1 = $kandidat_email;
							$mobilni_new_ND_cand1 = $kandidat_broj_tel;
							
							//UPDATE kandidata u Dipl
							$new_ND_kandidat = $db->prepare("
											INSERT INTO idk_nd_kandidata
												(
													ime_nd_kandidata, 
													prezime_nd_kandidata,
													mobilni_nd_kandidata, 
													email_nd_kandidata, 
													vrijeme_kreiranja_nd_kandidata,
													zaduzen_zaposlenik_nd_kandidata, 
													tim_nd_kandidata, 
													status_nd_kandidata,
													povijest_nd_kandidata,
													povijest_vrsta_nd_kandidata,
													kampanja_id
												)
											VALUES
												(
													:ime_nd_kandidata, 
													:prezime_nd_kandidata,
													:mobilni_nd_kandidata, 
													:email_nd_kandidata, 
													:vrijeme_kreiranja_nd_kandidata,
													:zaduzen_zaposlenik_nd_kandidata, 
													:tim_nd_kandidata, 
													:status_nd_kandidata,
													:povijest_nd_kandidata,
													:povijest_vrsta_nd_kandidata,
													:kampanja_id
												)");

							$new_ND_kandidat->execute(array(
											':ime_nd_kandidata' => $ime_new_ND_cand1,
											':prezime_nd_kandidata' => $prezime_new_ND_cand1,
											':mobilni_nd_kandidata' => $mobilni_new_ND_cand1,
											':email_nd_kandidata' => $email_new_ND_cand1,
											':vrijeme_kreiranja_nd_kandidata' => date('Y-m-d H:i:s'),
											':zaduzen_zaposlenik_nd_kandidata' => $novi_menager,
											':tim_nd_kandidata' => $novi_menager_team,
											':status_nd_kandidata' => 1,
											':povijest_nd_kandidata' => 5,
											':povijest_vrsta_nd_kandidata' => 1,
											':kampanja_id' => $kamp
											));
							//Get last ID
							
							$kandidat_id_nd = $db->lastInsertId();
							
							//Update statistike menadzera START
							// vrsta_aktivnosti == 0 -> oznacava da je sistem automatski dodjelio tom menadzeru kandidata
							$insert_statistike_menagera = $db->prepare("
										INSERT INTO idk_nd_menadzeri_statistike
											(idd_broj_nd_kandidata, zaduzen_zaposlenik_id, vrsta_aktivnosti, vrijeme_aktivnosti)
										VALUES
											(:idd_broj_nd_kandidata, :zaduzen_zaposlenik_id, :vrsta_aktivnosti, :vrijeme_aktivnosti)");

							$insert_statistike_menagera->execute(array(
										':idd_broj_nd_kandidata' => $kandidat_id_nd,
										':zaduzen_zaposlenik_id' => $novi_menager,
										':vrsta_aktivnosti' => 0,
										':vrijeme_aktivnosti' => date('Y-m-d H:i:s')
										));
			
							//Update log statusa ND kandidata
							$new_ND_status = $db->prepare("
												INSERT INTO idk_nd_kandidata_status_log
												(
													idd_broj_nd_kandidata,
													status_nd_kandidata,
													vrijeme_promjene_statusa_nd_kandidata
												)
												VALUES
												(
													:idd_broj_nd_kandidata,
													:status_nd_kandidata,
													:vrijeme_promjene_statusa_nd_kandidata
												)
							");
							$new_ND_status->execute(array(
											':idd_broj_nd_kandidata' => $kandidat_id_nd,
											':status_nd_kandidata' => 1,
											':vrijeme_promjene_statusa_nd_kandidata' => date('Y-m-d H:i:s')
											));
			
							//ADD TO LOGS START
								$log_date = date('Y-m-d H:i:s');
								$log_desc = "DIPL -> Dodan novi kandidat sa ID = [".$kandidat_id_nd."] sa stranice SveZaVizu.eu - Kampanja ID = [".$kamp."] - Zadužen zaposlenik: ".$novi_menager." ";
								$log_query = $db->prepare("
												INSERT INTO idk_logs 
													(log_employeeid, log_desc, log_date)
												VALUES
													(:log_employeeid, :log_desc, :log_date)");

								$log_query->execute(array(
												':log_employeeid' => $logged_employee_id,
												':log_desc' => $log_desc,
												':log_date' => $log_date));
							//ADD TO LOGS END
			
							//Provjera da li se nalazi u kandidatima
							$provjera_kandidati = $db->prepare("
											SELECT kandidat_id
											FROM idk_kandidati
											WHERE kandidat_mobitel = :kandidat_mobitel AND kandidat_status != :kandidat_status
											");
							$provjera_kandidati->execute(array(
													":kandidat_mobitel" => $kandidat_broj_tel,
													":kandidat_status" => 3
												));
							$kandidati_da_ne = $provjera_kandidati->rowCount();
							$provjera_kandidati_row = $provjera_kandidati->fetch();
							$kandidat_id_vec_u_kan = $provjera_kandidati_row['kandidat_id'];
							if($kandidati_da_ne == 0){
								//DODAVANJE U TABELU IDK_KANDIDATI
								
								$kandidat_check = md5(uniqid(rand(), true));
				
								//Add user to db
								$query_add_user = $db->prepare("
												INSERT INTO idk_kandidati
													(kandidat_check, kandidat_ime, kandidat_prezime, kandidat_email, kandidat_mobitel, kandidat_slika, kandidat_status, kandidat_status_messenger, kandidat_status_prijave, kandidat_datetime, kandidat_visitedurl, kandidat_prijava_na, kandidat_group, povezan_na_dipl, kandidat_porijeklo, kandidat_dipl_id )
												VALUES
													(:kandidat_check, :kandidat_ime, :kandidat_prezime, :kandidat_email, :kandidat_mobitel, :kandidat_slika, :kandidat_status, :kandidat_status_messenger, :kandidat_status_prijave, :kandidat_datetime, :kandidat_visitedurl, :kandidat_prijava_na, :kandidat_group, :povezan_na_dipl, :kandidat_porijeklo, :kandidat_dipl_id)");
						
								$query_add_user->execute(array(
											':kandidat_check' => $kandidat_check,
											':kandidat_ime' => $ime_new_ND_cand1,
											':kandidat_prezime' => $prezime_new_ND_cand1,
											':kandidat_email' => $email_new_ND_cand1,
											':kandidat_mobitel' => $mobilni_new_ND_cand1,
											':kandidat_slika' => "none",
											':kandidat_status' => 0,
											':kandidat_status_messenger' => 1,
											':kandidat_status_prijave' => 1,
											':kandidat_datetime' => date('Y-m-d H:i:s'),
											':kandidat_visitedurl' => 1,
											':kandidat_prijava_na' => "Ostalo",
											':kandidat_group' => 7,
											':povezan_na_dipl' => 1,
											':kandidat_porijeklo' => 4,
											':kandidat_dipl_id' => $kandidat_id_nd
											));
											
								$kandidat_id = $db->lastInsertId();
								//UPDATE ND_KANDIDATA STAVITI OVAJ ID 
					
								$query_log_status = $db->prepare("
										INSERT INTO idk_log_kandidat_statusi
											(lks_kandidat_id, lks_status_obrade, lks_status_messenger, lks_datetime)
										VALUES
											(:lks_kandidat_id, :lks_status_obrade, :lks_status_messenger, :lks_datetime)
								");
								
								$query_log_status->execute(array(
										':lks_kandidat_id' => $kandidat_id,
										':lks_status_obrade' => 0,
										':lks_status_messenger' => 1,
										':lks_datetime' => date('Y-m-d H:i:s')
								));
								
								$nalog_query = $db->prepare("
														SELECT project_id
														FROM idk_projects
														WHERE (project_name LIKE '%Kampanje sa DIPLa%') ");
							
								$nalog_query->execute();
							
								$nalogrow = $nalog_query->fetch();
								
								$project_id = $nalogrow['project_id'];
								$query_project = $db->prepare("
												INSERT INTO idk_project_kandidati
													(pk_projectid, pk_kandidatid)
												VALUES
													(:pk_projectid, :pk_kandidatid)");

								$query_project->execute(array(
												':pk_projectid' => $project_id,
												':pk_kandidatid' => $kandidat_id));
								
								addToLogsStatusPrijave(NULL, $project_id, 1, $kandidat_id, 3);
												
								$kki_grupa = 1;
								$kki_naziv = "Mobilni";

								//Add mobilni to db
								$query_mob = $db->prepare("
												INSERT INTO idk_kandidat_kontakt_info
													(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id)
												VALUES
													(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id)");

								$query_mob->execute(array(
											':kki_grupa' => $kki_grupa,
											':kki_naziv' => $kki_naziv,
											':kki_podatak' => $mobilni_new_ND_cand1,
											':kki_kandidat_id' => $kandidat_id));
								
								$kki_grupa_e = 2;
								$kki_naziv_e = "E-mail";
								
								//Add kontakt info to db
								$query_email = $db->prepare("
												INSERT INTO idk_kandidat_kontakt_info
													(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id)
												VALUES
													(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id)");

								$query_email->execute(array(
											':kki_grupa' => $kki_grupa_e,
											':kki_naziv' => $kki_naziv_e,
											':kki_podatak' => $email_new_ND_cand1,
											':kki_kandidat_id' => $kandidat_id));
								
								//Add to table users (chatbot)
								$random_string = generateRandomString();
								$options = [
									'cost' => 10,
								];
								$random_password = password_hash($random_string, PASSWORD_BCRYPT, $options);
								
								$log_desc2 = "Kandidat: " . $ime_new_ND_cand1 . " " . $prezime_new_ND_cand1 . "(".$kandidat_id."). ; (".$random_string.")";
								$log_type2 = "5";
								addToLogs($log_desc2, $log_type2);
								
								$characters = '0123456789';
								$charactersLength = strlen($characters);
								$randomString = '';
								for ($i = 0; $i < 5; $i++) {
									$randomString .= $characters[rand(0, $charactersLength - 1)];
								}
								$bot_koriscnicko_ime = $ime_new_ND_cand1.$randomString;
								$kandidat_full_name = $ime_new_ND_cand1." ".$prezime_new_ND_cand1;
								$query_user = $db->prepare("
											INSERT INTO users
												(phone, name, nalog_id, email, password, kandidat_id)
											VALUES
												(:phone, :name, :nalog_id, :email, :password, :kandidat_id)");

								$query_user->execute(array(
											':phone' => $mobilni_new_ND_cand1,
											':name' => $kandidat_full_name,
											':nalog_id' => 44,
											':email' => $bot_koriscnicko_ime,
											':password' => $random_password,
											':kandidat_id' => $kandidat_id));
											
								$phone_f = str_replace("+", '', $mobilni_new_ND_cand1);
								
								$link_dload = "https://crm.job-step.com/download";
								$link_uputs = "https://bit.ly/3V177tF";

								$to_send_viber_poruka_1 = $kandidat_full_name.$text_viber_new_prva_poruka; 
								$to_send_sms_poruka_1 	= $kandidat_full_name.$text_sms_new_prva_poruka; 

								$to_send_viber_poruka_2 = $text_viber_new_druga_poruka;
								$to_send_sms_poruka_2 	= $text_sms_new_druga_poruka." ".$link_dload;

								$to_send_viber_poruka_3 = $text_viber_new_treca_poruka_1.'\n\n'.$text_viber_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_viber_new_treca_poruka_3.$random_string;
								$to_send_sms_poruka_3 	= $text_sms_new_treca_poruka_1.'\n\n'.$text_sms_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_sms_new_treca_poruka_3.$random_string;

								$to_send_viber_poruka_4 = $text_viber_new_cetvrta_poruka;
								$to_send_sms_poruka_4 	= $text_sms_new_cetvrta_poruka." ".$link_uputs;

								viberPrijava1($mobilni_new_ND_cand1, $to_send_sms_poruka_1, $to_send_viber_poruka_1);
								sleep(1);
								viberPrijava2($mobilni_new_ND_cand1, $to_send_sms_poruka_2, $to_send_viber_poruka_2, $text_btn_new_druga_poruka, $link_dload);
								sleep(1);
								viberPrijava1($mobilni_new_ND_cand1, $to_send_sms_poruka_3, $to_send_viber_poruka_3);
								sleep(1);
								viberPrijava2($mobilni_new_ND_cand1, $to_send_sms_poruka_4, $to_send_viber_poruka_4, $text_btn_new_cetvrta_poruka, $link_uputs);

								
					
								// sendCandidateMessengerMail($random_string, $email_new_ND_cand1, $bot_koriscnicko_ime);
								
								//KRAJ DODAVANJA U TABELU KANDIDATI
							}else{
								$update_query_pov_dipl = $db->prepare("
									UPDATE idk_kandidati
									SET povezan_na_dipl = :povezan_na_dipl, kandidat_dipl_id = :kandidat_dipl_id
									WHERE kandidat_id = :kandidat_id
								");
								
								$update_query_pov_dipl->execute(array(
									':kandidat_id' => $kandidat_id_vec_u_kan,
									':povezan_na_dipl' => 1,
									':kandidat_dipl_id' => $kandidat_id_nd
								));
							}
						}else{
						//Provjera da li se nalazi u kandidatima
							$provjera_kandidati = $db->prepare("
											SELECT kandidat_id
											FROM idk_kandidati
											WHERE kandidat_mobitel = :kandidat_mobitel AND kandidat_status != :kandidat_status AND kandidat_mobitel != ''
											");
							$provjera_kandidati->execute(array(
													":kandidat_mobitel" => $kandidat_broj_tel,
													":kandidat_status" => 3
												));
							$kandidati_da_ne = $provjera_kandidati->rowCount();
							$provjera_kandidati_row = $provjera_kandidati->fetch();
							$kandidat_id_vec_u_kan = $provjera_kandidati_row['kandidat_id'];
							if($kandidati_da_ne != 0){
								$update_query_pov_dipl = $db->prepare("
									UPDATE idk_kandidati
									SET povezan_na_dipl = :povezan_na_dipl, kandidat_dipl_id = :dipl_id
									WHERE kandidat_id = :kandidat_id
								");
								
								$update_query_pov_dipl->execute(array(
									':kandidat_id' => $kandidat_id_vec_u_kan,
									':povezan_na_dipl' => 1,
									':dipl_id' => $kandidat_id_vec_u_dipl
								));
							}
							//Ako se kandidat nalazi u diplu
							$desila_se_prijava = insertPonovnePrijaveDIPL($kandidat_id_vec_u_dipl, $kandidat_ime, $kandidat_prezime, $kandidat_broj_tel, $kandidat_email, 5, 1, $kamp);
							if($desila_se_prijava == 1){
								/*
								//Slanje maila starom menadzeru sa linkom kandidata njemu dodjeljenog
								$user_query = $db->prepare("
														SELECT employee_firstname, employee_lastname, employee_email
														FROM idk_employees
														WHERE employee_id = :employee_id");

								$user_query->execute(array(
												':employee_id' => $zaduzen_id_vec_u_dipl));

								$user = $user_query->fetch();

								$employee_firstname = $user['employee_firstname'];
								$employee_lastname = $user['employee_lastname'];
								$employee_email = $user['employee_email'];

								//Send email to user
								$mail_email = $employee_email;
								$mail_name = $employee_firstname . ' ' . $employee_lastname;
								$mail_subject = "Dipl modul - Stari kandidat";
								$mail_url = "" . getSiteUrlr() . "/nostrifikacija_diploma?page=otvori_ND_kandidata&id=".$kandidat_id_vec_u_dipl." ";
								$mail_body = "
												<p>Vaš stari kandidat u navedenom linku, pokušao se ponovno prijaviti za nostrifikaciju diplome putem linka za kampanje na stranici Sve za vizu.</p>
												<p>Detalje pogledajte na linku: " . $mail_url . "</p>
								";
								$mail_altbody = "
												<p>Vaš stari kandidat u navedenom linku, pokušao se ponovno prijaviti za nostrifikaciju diplome putem linka za kampanje na stranici Sve za vizu.</p>
												<p>Detalje pogledajte na linku: " . $mail_url . "</p>
								";
								
								sendEmail($mail_email, $mail_name, $mail_subject, $mail_body, $mail_altbody);
								sendEmail("support@job-step.com", "support@job-step.com", $mail_subject, $mail_body, $mail_altbody);
								*/
							}
						}
						
						header("Location: " . getJoinUrlr() . "/nostrification/thank_you");

					// }else echo "Something went wrong, please try again";
				 // }else echo 'Something went wrong - please contact us at info@job-step.net';
				break;
				
				case "prijavaDIPLKtest":
					
					$kandidat_drzavljanstvo_vrsta = $_POST['kandidat_drzavljanstvo_vrsta'];
					if($kandidat_drzavljanstvo_vrsta == "EU državljanin"){
							$eu_drzava = "NN";
							$skola_naziv = "nema";
						}elseif($kandidat_drzavljanstvo_vrsta == "nova_prijava"){
							$skola_naziv = $_POST['skola_naziv'];
						}else{
							$boravak_eu = $_POST['kandidat_boravak_eu'];
							if($boravak_eu == "DA"){
								$eu_drzava = $_POST['eu_drzava'];
								$skola_naziv = "nema";
							}else{
								$eu_drzava = "NE";
								$skola_naziv = $_POST['skola_naziv'];
								if($skola_naziv != "ostalo"){
									$smjer_naziv = $_POST['smjer_naziv'];
								}else{
									$skola_naziv = $_POST['skola_naziv_ru'];
									$smjer_naziv = $_POST['smjer_naziv_ru'];
								}
							}
						}
					
					//$skola_naziv = $_POST['skola_naziv'];
					
					$insert_skole = $db->prepare("
												INSERT INTO idk_kandidat_edukacija
													(ke_naziv_kvalifikacije, ke_naziv, ke_vrsta_obrazovanja, ke_kandidat_id)
												VALUES
													(:ke_naziv_kvalifikacije, :ke_naziv, :ke_vrsta_obrazovanja, :ke_kandidat_id)");
						
								$insert_skole->execute(array(
											':ke_naziv_kvalifikacije' => $smjer_naziv,
											':ke_naziv' => $skola_naziv,
											':ke_vrsta_obrazovanja' => "srednje",
											':ke_kandidat_id' => 31810));
					var_dump($smjer_naziv);
					exit();
					
					
					// Build POST request:
					$recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
					$recaptcha_secret = '6LdZmAIaAAAAAMXI2zDBtAnUKtAo8mAJZKDuV84H';
					$recaptcha_response = $_POST['recaptcha_response'];
					// Make and decode POST request:
					$recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
					$recaptcha = json_decode($recaptcha);
					// Take action based on the score returned:
					if ($recaptcha->score >= 0) {
						include("lang/bs.php");
						$kamp = $_POST["kamp"];
						$kandidat_ime = $_POST["kandidat_ime_dipl"];
						$kandidat_prezime = $_POST["kandidat_prezime_dipl"];
						$kandidat_broj_tel = $_POST["kandidat_telefon_dipl"];
						$kandidat_email = $_POST["kandidat_email_dipl"];
						echo "asdasd";
						var_dump($kandidat_broj_tel);
						exit();
						/*$dan_rodjenja = $_POST["datum_dan"];
						$mjesec_rodjenja = $_POST["datum_mjesec"];
						$godina_rodjenja = $_POST["datum_godina"];
						
						$cijeli_datum = new DateTime($godina_rodjenja."-".$mjesec_rodjenja."-".$dan_rodjenja);
						$kandidat_datumrodjenja = $cijeli_datum->format('Y-m-d');
						*/
						//Provjera da li se nalazi u DIPL VEC
						$provjera_dipl = $db->prepare("
												SELECT id_broj_nd_kandidata, zaduzen_zaposlenik_nd_kandidata
												FROM idk_nd_kandidata
												WHERE mobilni_nd_kandidata = :mobilni_nd_kandidata AND mobilni_nd_kandidata != ''
												");
						$provjera_dipl->execute(array(
												":mobilni_nd_kandidata" => $kandidat_broj_tel
											));
						$dipl_da_ne = $provjera_dipl->rowCount();
						$provjera_dipl_row = $provjera_dipl->fetch();
						$kandidat_id_vec_u_dipl = $provjera_dipl_row['id_broj_nd_kandidata'];
						$zaduzen_id_vec_u_dipl = $provjera_dipl_row['zaduzen_zaposlenik_nd_kandidata'];
						
						if($dipl_da_ne == 0){
							//Ako se kandidat ne nalazi u diplu
							//Update broja prijavljenih preko kampanje
							$query_upd = $db->prepare("
										UPDATE idk_kampanje_dipl
										SET kd_broj_prijavljenih = kd_broj_prijavljenih + 1
										WHERE kd_id = :kd_id");

							$query_upd->execute(array(
									':kd_id' => $kamp));
							
							//Algoritam dodjele kandidata odredjenom zaposleniku
							$uzmi_zadnjeg_zaduzenog_zaposlenika = $db->prepare("
														SELECT 
															employee_id
														FROM 
															idk_employees
														WHERE 
															employee_nostrifikacija_diploma = :employee_nostrifikacija_diploma AND
															employee_nostrifikacija_zadnji_menadzer_app = :employee_nostrifikacija_zadnji_menadzer_app
							");
							$uzmi_zadnjeg_zaduzenog_zaposlenika->execute(array(
														':employee_nostrifikacija_diploma' => 1,
														':employee_nostrifikacija_zadnji_menadzer_app' => 1
														));
														
							if (($uzmi_zadnjeg_zaduzenog_zaposlenika->rowCount()) == 0){
								//Ako prethodni Select ne nadje takvog zaposlenika,
								//trazi se zaposlenik sa najmanjim ID-om kojem je omogućena nostrifikacija
								$uzmi_prvog_zaposlenika_u_redu = $db->prepare("
														SELECT 
															MIN(employee_id) AS prvi_zaposlenik_u_redu
														FROM 
															idk_employees
														WHERE
															employee_nostrifikacija_diploma = :employee_nostrifikacija_diploma
								");
								$uzmi_prvog_zaposlenika_u_redu->execute(array(
															':employee_nostrifikacija_diploma' => 1
															));
								$uzmi_prvog_zaposlenika_u_redu_row = $uzmi_prvog_zaposlenika_u_redu->fetch();
								$zaduzeni_zaposlenik_novi_xx = $uzmi_prvog_zaposlenika_u_redu_row['prvi_zaposlenik_u_redu'];
							}else{
								//Ako prethodni Select nadje zadnjeg zaduzenog zaposlenika, 
								//trazi se naredni u nizu i on se uzima kao novi zaduzeni
								$uzmi_zadnjeg_zaduzenog_zaposlenika_row = $uzmi_zadnjeg_zaduzenog_zaposlenika->fetch();
								$zadnji_zaduzeni_menadzer = intval($uzmi_zadnjeg_zaduzenog_zaposlenika_row['employee_id']);
								
								$uzmi_narednog_zaposlenika_u_redu = $db->prepare("
														SELECT 
															MIN(employee_id) AS naredni_zaposlenik_u_redu
														FROM
															idk_employees
														WHERE 
															employee_nostrifikacija_diploma = :employee_nostrifikacija_diploma AND
															employee_id > :employee_id
								");
								$uzmi_narednog_zaposlenika_u_redu->execute(array(
															':employee_nostrifikacija_diploma' => 1,
															':employee_id' => $zadnji_zaduzeni_menadzer
															));
								$uzmi_narednog_zaposlenika_u_redu_row = $uzmi_narednog_zaposlenika_u_redu->fetch();
								
								if(($uzmi_narednog_zaposlenika_u_redu_row['naredni_zaposlenik_u_redu']) == NULL){
									//Ako prethodni select vrati NULL vrijednost, to znaci da je 
									// $zadnji_zaduzeni_menadzer bio sa najvećim ID-om u redu kojem je omogućena nostrifikacija
									//Odnosno doslo se do kraja reda. U tom slucaju uzima se prvi u redu
									$uzmi_prvog_zaposlenika_u_redu1 = $db->prepare("
														SELECT 
															MIN(employee_id) AS prvi_zaposlenik_u_reduu
														FROM
															idk_employees
														WHERE
															employee_nostrifikacija_diploma = :employee_nostrifikacija_diploma
									");
									$uzmi_prvog_zaposlenika_u_redu1->execute(array(
																':employee_nostrifikacija_diploma' => 1
																));
									$uzmi_prvog_zaposlenika_u_redu1_row = $uzmi_prvog_zaposlenika_u_redu1->fetch();
									$zaduzeni_zaposlenik_novi_xx = $uzmi_prvog_zaposlenika_u_redu1_row['prvi_zaposlenik_u_reduu'];
								}else{
									//Uzima se prvi veci
									$zaduzeni_zaposlenik_novi_xx = $uzmi_narednog_zaposlenika_u_redu_row['naredni_zaposlenik_u_redu'];
								} 
							}
			
							//REZULTAT ALGORITMA 
							$zadnji_menager = $zadnji_zaduzeni_menadzer;
							$novi_menager = $zaduzeni_zaposlenik_novi_xx;
							
							$ime_new_ND_cand1 = $kandidat_ime;
							$prezime_new_ND_cand1 = $kandidat_prezime;
							$email_new_ND_cand1 = $kandidat_email;
							$mobilni_new_ND_cand1 = $kandidat_broj_tel;
							
							//UPDATE kandidata u Dipl
							$new_ND_kandidat = $db->prepare("
											INSERT INTO idk_nd_kandidata
												(
													ime_nd_kandidata, 
													prezime_nd_kandidata,
													mobilni_nd_kandidata, 
													email_nd_kandidata, 
													vrijeme_kreiranja_nd_kandidata,
													zaduzen_zaposlenik_nd_kandidata, 
													status_nd_kandidata,
													povijest_nd_kandidata,
													povijest_vrsta_nd_kandidata,
													kampanja_id
												)
											VALUES
												(
													:ime_nd_kandidata, 
													:prezime_nd_kandidata,
													:mobilni_nd_kandidata, 
													:email_nd_kandidata, 
													:vrijeme_kreiranja_nd_kandidata,
													:zaduzen_zaposlenik_nd_kandidata, 
													:status_nd_kandidata,
													:povijest_nd_kandidata,
													:povijest_vrsta_nd_kandidata,
													:kampanja_id
												)");

							$new_ND_kandidat->execute(array(
											':ime_nd_kandidata' => $ime_new_ND_cand1,
											':prezime_nd_kandidata' => $prezime_new_ND_cand1,
											':mobilni_nd_kandidata' => $mobilni_new_ND_cand1,
											':email_nd_kandidata' => $email_new_ND_cand1,
											':vrijeme_kreiranja_nd_kandidata' => date('Y-m-d H:i:s'),
											':zaduzen_zaposlenik_nd_kandidata' => $novi_menager,
											':status_nd_kandidata' => 1,
											':povijest_nd_kandidata' => 5,
											':povijest_vrsta_nd_kandidata' => 1,
											':kampanja_id' => $kamp
											));
							//Get last ID
							
							$kandidat_id_nd = $db->lastInsertId();
							
							//UPDATE NULL starom zaduzenom i UPDATE 1 novom zaduzenom 
							if($zadnji_menager == NULL){
								//Ako je zadnji menadzer == NULL onda se samo radi UPDATE prvog u redu menadzera na 1 
								$update_novog_menadzera_sa_1 = $db->prepare("
													UPDATE idk_employees
													SET employee_nostrifikacija_zadnji_menadzer_app	 = :employee_nostrifikacija_zadnji_menadzer_app	
													WHERE employee_id = :employee_id");

								$update_novog_menadzera_sa_1->execute(array(
											':employee_id' => $novi_menager,
											':employee_nostrifikacija_zadnji_menadzer_app' => 1
											));
							}else{
								//Update zadnjeg menadzera START
								$update_starog_menadzera_sa_null = $db->prepare("
													UPDATE idk_employees
													SET employee_nostrifikacija_zadnji_menadzer_app	 = :employee_nostrifikacija_zadnji_menadzer_app	
													WHERE employee_id = :employee_id");

								$update_starog_menadzera_sa_null->execute(array(
											':employee_id' => $zadnji_menager,
											':employee_nostrifikacija_zadnji_menadzer_app' => NULL
											));
								
								//Update zadnjeg menadzera START
								$update_novog_menadzera_sa_1 = $db->prepare("
													UPDATE idk_employees
													SET employee_nostrifikacija_zadnji_menadzer_app	 = :employee_nostrifikacija_zadnji_menadzer_app	
													WHERE employee_id = :employee_id");

								$update_novog_menadzera_sa_1->execute(array(
											':employee_id' => $novi_menager,
											':employee_nostrifikacija_zadnji_menadzer_app' => 1
											));
							}
			
							//Update statistike menadzera START
							// vrsta_aktivnosti == 0 -> oznacava da je sistem automatski dodjelio tom menadzeru kandidata
							$insert_statistike_menagera = $db->prepare("
										INSERT INTO idk_nd_menadzeri_statistike
											(idd_broj_nd_kandidata, zaduzen_zaposlenik_id, vrsta_aktivnosti, vrijeme_aktivnosti)
										VALUES
											(:idd_broj_nd_kandidata, :zaduzen_zaposlenik_id, :vrsta_aktivnosti, :vrijeme_aktivnosti)");

							$insert_statistike_menagera->execute(array(
										':idd_broj_nd_kandidata' => $kandidat_id_nd,
										':zaduzen_zaposlenik_id' => $novi_menager,
										':vrsta_aktivnosti' => 0,
										':vrijeme_aktivnosti' => date('Y-m-d H:i:s')
										));
			
							//Update log statusa ND kandidata
							$new_ND_status = $db->prepare("
												INSERT INTO idk_nd_kandidata_status_log
												(
													idd_broj_nd_kandidata,
													status_nd_kandidata,
													vrijeme_promjene_statusa_nd_kandidata
												)
												VALUES
												(
													:idd_broj_nd_kandidata,
													:status_nd_kandidata,
													:vrijeme_promjene_statusa_nd_kandidata
												)
							");
							$new_ND_status->execute(array(
											':idd_broj_nd_kandidata' => $kandidat_id_nd,
											':status_nd_kandidata' => 1,
											':vrijeme_promjene_statusa_nd_kandidata' => date('Y-m-d H:i:s')
											));
			
							//ADD TO LOGS START
								$log_date = date('Y-m-d H:i:s');
								$log_desc = "DIPL -> Dodan novi kandidat sa ID = [".$kandidat_id_nd."] sa stranice SveZaVizu.eu - Kampanja ID = [".$kamp."] - Zadužen zaposlenik: ".$novi_menager." ";
								$log_query = $db->prepare("
												INSERT INTO idk_logs 
													(log_employeeid, log_desc, log_date)
												VALUES
													(:log_employeeid, :log_desc, :log_date)");

								$log_query->execute(array(
												':log_employeeid' => $logged_employee_id,
												':log_desc' => $log_desc,
												':log_date' => $log_date));
							//ADD TO LOGS END
			
							//Provjera da li se nalazi u kandidatima
							$provjera_kandidati = $db->prepare("
											SELECT kandidat_id
											FROM idk_kandidati
											WHERE kandidat_mobitel = :kandidat_mobitel AND kandidat_status != :kandidat_status
											");
							$provjera_kandidati->execute(array(
													":kandidat_mobitel" => $kandidat_broj_tel,
													":kandidat_status" => 3
												));
							$kandidati_da_ne = $provjera_kandidati->rowCount();
							$provjera_kandidati_row = $provjera_kandidati->fetch();
							$kandidat_id_vec_u_kan = $provjera_kandidati_row['kandidat_id'];
							if($kandidati_da_ne == 0){
								//DODAVANJE U TABELU IDK_KANDIDATI
								
								$kandidat_check = md5(uniqid(rand(), true));
				
								//Add user to db
								$query_add_user = $db->prepare("
												INSERT INTO idk_kandidati
													(kandidat_check, kandidat_ime, kandidat_prezime, kandidat_email, kandidat_mobitel, kandidat_slika, kandidat_status, kandidat_status_messenger, kandidat_datetime, kandidat_visitedurl, kandidat_prijava_na, kandidat_group, povezan_na_dipl, kandidat_porijeklo )
												VALUES
													(:kandidat_check, :kandidat_ime, :kandidat_prezime, :kandidat_email, :kandidat_mobitel, :kandidat_slika, :kandidat_status, :kandidat_status_messenger, :kandidat_datetime, :kandidat_visitedurl, :kandidat_prijava_na, :kandidat_group, :povezan_na_dipl, :kandidat_porijeklo)");
						
								$query_add_user->execute(array(
											':kandidat_check' => $kandidat_check,
											':kandidat_ime' => $ime_new_ND_cand1,
											':kandidat_prezime' => $prezime_new_ND_cand1,
											':kandidat_email' => $email_new_ND_cand1,
											':kandidat_mobitel' => $mobilni_new_ND_cand1,
											':kandidat_slika' => "none",
											':kandidat_status' => 0,
											':kandidat_status_messenger' => 1,
											':kandidat_datetime' => date('Y-m-d H:i:s'),
											':kandidat_visitedurl' => 1,
											':kandidat_prijava_na' => "Ostalo",
											':kandidat_group' => 7,
											':povezan_na_dipl' => 1,
											':kandidat_porijeklo' => 4
											));
											
								$kandidat_id = $db->lastInsertId();
								//UPDATE ND_KANDIDATA STAVITI OVAJ ID 
					
								$query_log_status = $db->prepare("
										INSERT INTO idk_log_kandidat_statusi
											(lks_kandidat_id, lks_status_obrade, lks_status_messenger, lks_datetime)
										VALUES
											(:lks_kandidat_id, :lks_status_obrade, :lks_status_messenger, :lks_datetime)
								");
								
								$query_log_status->execute(array(
										':lks_kandidat_id' => $kandidat_id,
										':lks_status_obrade' => 0,
										':lks_status_messenger' => 1,
										':lks_datetime' => date('Y-m-d H:i:s')
								));
								
								$nalog_query = $db->prepare("
														SELECT project_id
														FROM idk_projects
														WHERE (project_name LIKE '%Kampanje sa DIPLa%') ");
							
								$nalog_query->execute();
							
								$nalogrow = $nalog_query->fetch();
								
								$project_id = $nalogrow['project_id'];
								$query_project = $db->prepare("
												INSERT INTO idk_project_kandidati
													(pk_projectid, pk_kandidatid)
												VALUES
													(:pk_projectid, :pk_kandidatid)");

								$query_project->execute(array(
												':pk_projectid' => $project_id,
												':pk_kandidatid' => $kandidat_id));
												
								$kki_grupa = 1;
								$kki_naziv = "Mobilni";

								//Add mobilni to db
								$query_mob = $db->prepare("
												INSERT INTO idk_kandidat_kontakt_info
													(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id)
												VALUES
													(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id)");

								$query_mob->execute(array(
											':kki_grupa' => $kki_grupa,
											':kki_naziv' => $kki_naziv,
											':kki_podatak' => $mobilni_new_ND_cand1,
											':kki_kandidat_id' => $kandidat_id));
								
								$kki_grupa_e = 2;
								$kki_naziv_e = "E-mail";
								
								//Add kontakt info to db
								$query_email = $db->prepare("
												INSERT INTO idk_kandidat_kontakt_info
													(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id)
												VALUES
													(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id)");

								$query_email->execute(array(
											':kki_grupa' => $kki_grupa_e,
											':kki_naziv' => $kki_naziv_e,
											':kki_podatak' => $email_new_ND_cand1,
											':kki_kandidat_id' => $kandidat_id));
								
								//Add to table users (chatbot)
								$random_string = generateRandomString();
								$options = [
									'cost' => 10,
								];
								$random_password = password_hash($random_string, PASSWORD_BCRYPT, $options);
								
								$log_desc2 = "Kandidat: " . $ime_new_ND_cand1 . " " . $prezime_new_ND_cand1 . "(".$kandidat_id."). ; (".$random_string.")";
								$log_type2 = "5";
								addToLogs($log_desc2, $log_type2);
								
								$characters = '0123456789';
								$charactersLength = strlen($characters);
								$randomString = '';
								for ($i = 0; $i < 5; $i++) {
									$randomString .= $characters[rand(0, $charactersLength - 1)];
								}
								$bot_koriscnicko_ime = $ime_new_ND_cand1.$randomString;
								$kandidat_full_name = $ime_new_ND_cand1." ".$prezime_new_ND_cand1;
								$query_user = $db->prepare("
											INSERT INTO users
												(phone, name, nalog_id, email, password, kandidat_id)
											VALUES
												(:phone, :name, :nalog_id, :email, :password, :kandidat_id)");

								$query_user->execute(array(
											':phone' => $mobilni_new_ND_cand1,
											':name' => $kandidat_full_name,
											':nalog_id' => 44,
											':email' => $bot_koriscnicko_ime,
											':password' => $random_password,
											':kandidat_id' => $kandidat_id));
											
								/*//INFOBIP
								sendSmsToCandidateInfobip1($random_string, $mobilni_new_ND_cand1, "NN");
								//sleep(1);  // Seconds
								sendSmsToCandidateInfobip2($random_string, $mobilni_new_ND_cand1, "NN");
								//sleep(1);  // Seconds
								sendSmsToCandidateInfobip3($random_string, $mobilni_new_ND_cand1, $bot_koriscnicko_ime);
								//sleep(1);  // Seconds
								sendSmsToCandidateInfobip4($random_string, $mobilni_new_ND_cand1, $bot_koriscnicko_ime);
								*/
								$phone_f = str_replace("+", '', $mobilni_new_ND_cand1);
								
								$link_dload = "https://crm.job-step.com/download";
								$link_uputs = "https://bit.ly/3V177tF";
								$sms2 = $txt_sms2.$link_dload;
								$sms3 = $txt_sms3_1.$bot_koriscnicko_ime.$txt_sms3_2.$random_string.$txt_sms3_3;
								$viber3 = $txt_viber3_1.'\n'.$txt_viber3_2.$bot_koriscnicko_ime.'\n'.$txt_viber3_3.$random_string.'\n';
								$sms4 = $txt_sms4.$link_uputs;
								//INFOBIP
								//sendSmsToCandidateInfobip1($random_string, $mobile_phone);
								viberPrijava1($phone_f, $txt_sms1, $txt_viber1);
								sleep(1);  // Seconds
								//sendSmsToCandidateInfobip2($random_string, $mobile_phone);
								viberPrijava2($phone_f, $sms2, $txt_viber2, $txt_btn1, $link_dload);
								sleep(1);  // Seconds
								//sendSmsToCandidateInfobip3($random_string, $mobile_phone, $bot_koriscnicko_ime);
								viberPrijava1($phone_f, $sms3, $viber3);
								sleep(1);  // Seconds
								//sendSmsToCandidateInfobip4($random_string, $mobile_phone, $bot_koriscnicko_ime);
								viberPrijava2($phone_f, $sms4, $txt_viber4, $txt_btn2, $link_uputs);
								
					
								// sendCandidateMessengerMail($random_string, $email_new_ND_cand1, $bot_koriscnicko_ime);
								
								//KRAJ DODAVANJA U TABELU KANDIDATI
							}else{
								$update_query_pov_dipl = $db->prepare("
										UPDATE idk_kandidati
										SET povezan_na_dipl = :povezan_na_dipl
										WHERE kandidat_id = :kandidat_id
								");
								
								$update_query_pov_dipl->execute(array(
											'kandidat_id' => $kandidat_id_vec_u_kan,
											'povezan_na_dipl' => 1
											));
							}
						}else{
						//Provjera da li se nalazi u kandidatima
							$provjera_kandidati = $db->prepare("
											SELECT kandidat_id
											FROM idk_kandidati
											WHERE kandidat_mobitel = :kandidat_mobitel AND kandidat_status != :kandidat_status AND kandidat_mobitel != ''
											");
							$provjera_kandidati->execute(array(
													":kandidat_mobitel" => $kandidat_broj_tel,
													":kandidat_status" => 3
												));
							$kandidati_da_ne = $provjera_kandidati->rowCount();
							$provjera_kandidati_row = $provjera_kandidati->fetch();
							$kandidat_id_vec_u_kan = $provjera_kandidati_row['kandidat_id'];
							if($kandidati_da_ne != NULL){
								$update_query_pov_dipl = $db->prepare("
										UPDATE idk_kandidati
										SET povezan_na_dipl = :povezan_na_dipl
										WHERE kandidat_id = :kandidat_id
								");
								
								$update_query_pov_dipl->execute(array(
											'kandidat_id' => $kandidat_id_vec_u_kan,
											'povezan_na_dipl' => 1
											));
							}
							//Ako se kandidat nalazi u diplu
							/*
							//Slanje maila starom menadzeru sa linkom kandidata njemu dodjeljenog
							$user_query = $db->prepare("
													SELECT employee_firstname, employee_lastname, employee_email
													FROM idk_employees
													WHERE employee_id = :employee_id");

							$user_query->execute(array(
											':employee_id' => $zaduzen_id_vec_u_dipl));

							$user = $user_query->fetch();

							$employee_firstname = $user['employee_firstname'];
							$employee_lastname = $user['employee_lastname'];
							$employee_email = $user['employee_email'];

							//Send email to user
							$mail_email = $employee_email;
							$mail_name = $employee_firstname . ' ' . $employee_lastname;
							$mail_subject = "Dipl modul - Stari kandidat";
							$mail_url = "" . getSiteUrlr() . "/nostrifikacija_diploma?page=otvori_ND_kandidata&id=".$kandidat_id_vec_u_dipl." ";
							$mail_body = "
											<p>Vaš stari kandidat u navedenom linku, pokušao se ponovno prijaviti za nostrifikaciju diplome putem linka za kampanje na stranici Sve za vizu.</p>
											<p>Detalje pogledajte na linku: " . $mail_url . "</p>
							";
							$mail_altbody = "
											<p>Vaš stari kandidat u navedenom linku, pokušao se ponovno prijaviti za nostrifikaciju diplome putem linka za kampanje na stranici Sve za vizu.</p>
											<p>Detalje pogledajte na linku: " . $mail_url . "</p>
							";
							
							sendEmail($mail_email, $mail_name, $mail_subject, $mail_body, $mail_altbody);
							sendEmail("support@job-step.com", "support@job-step.com", $mail_subject, $mail_body, $mail_altbody);*/
						}
	
						header("Location: https://job-step.net/registracija/thank_you" );
					}else echo "Something went wrong, please try again";
				break;
				
				case "porukaJSinDIPL":
		?>
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
                    <img style="padding-top:10px;" width="40" height="50" src="<?php getSiteURL(); ?>images/thankyoupageSaveJSInDipl/uspjesno.png"  />
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
                    <img src="<?php getSiteURL(); ?>images/thankyoupageSaveJSInDipl/jobstep_messenger.png" />
                </div>

            </div>
            <div class="row">
                <div class="col-lg-12" align="center">
                    <div class="custom-btn-group mt-4" style="margin-top:0px;">
                        <a href="https://play.google.com/store/apps/details?id=net.job_step.jobstepmessenger"><img src="<?php getSiteURL(); ?>images/thankyoupageSaveJSInDipl/google_play.png" style="margin-bottom:10px;" /></a>
                        <a href="https://apps.apple.com/tt/app/jobstep-messenger/id1486805317"><img src="<?php getSiteURL(); ?>images/thankyoupageSaveJSInDipl/app_store.png" style="margin-bottom:10px;" /></a>
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
                    <img width="300" height="550" src="<?php getSiteURL(); ?>images/thankyoupageSaveJSInDipl/jobstep_partner_app.png" />
                </div>

                <div class="col-lg-5" align="center" style="padding-top:30px;">
                    <div class="custom-btn-group mt-4 " style="padding-top:20px;">
                        <h3>Jobstep Partner App</h3>
                        <p>Jobstep Partner App je aplikacija za vanjske saradnike Jobstepa. Svako može postati naš partner prijavom na ovu aplikaciju. Preporuči Jobstep i zaradi!</p>
                        <a href="https://play.google.com/store/apps/details?id=com.partnerjobstep"><img width="220" height="65" src="<?php getSiteURL(); ?>images/thankyoupageSaveJSInDipl/google_play_crna.png" style="margin-bottom:10px;" alt="Android - Jobstep Messenger" /></a>
                        <a href="https://apps.apple.com/tt/app/jobstep-partner-app/id1498761708?ign-mpt=uo%3D2"><img width="220" height="65" src="<?php getSiteURL(); ?>images/thankyoupageSaveJSInDipl/app_store_crna.png" style="margin-bottom:10px;" alt="iOS - Jobstep Messenger" /></a>
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
                        <a href="https://play.google.com/store/apps/details?id=com.jobstep.onlineviza"><img width="220" height="65" src="<?php getSiteURL(); ?>images/thankyoupageSaveJSInDipl/google_play_crna.png" style="margin-bottom:10px;" /></a>
                        <a href="#!"><img width="220" height="65" src="<?php getSiteURL(); ?>images/thankyoupageSaveJSInDipl/app_store_crna.png" style="margin-bottom:10px;" /></a>
                    </div>
                </div>

                <div class="col-lg-7" align="center">
                    <img width="300" height="550" src="<?php getSiteURL(); ?>images/thankyoupageSaveJSInDipl/sve za vizu.png" />
                </div>

            </div>

        </div>
    </section>


    <!--Footer - Društvene mreže-->

    <footer class="text-center text-white" style="background-color: #f1f1f1; margin-top:30px;">

        <div class="container pt-4">
            <p>Zapratite nas na:</p>
            <section class="mb-4">
                <a href="https://www.facebook.com/JobStepInternational" alt="Jobstep Facebook"> <img width="35" height="35" src="<?php getSiteURL(); ?>images/thankyoupageSaveJSInDipl/facebook_logo.png" /></a>
                <a href="https://www.instagram.com/jobstepinternational/" alt="Jobstep Instagram"> <img width="35" height="35" src="<?php getSiteURL(); ?>images/thankyoupageSaveJSInDipl/instagram_logo.png" /> </a>
                <a href="https://www.linkedin.com/in/jobstepint/" alt="Jobstep LinkedIn" role="button" data-mdb-ripple-color="dark"> <img width="35" height="35" src="<?php getSiteURL(); ?>images/thankyoupageSaveJSInDipl/linked_in_logo.png" /></a>
                <a href=" https://www.youtube.com/channel/UChL-bdalb8qxtYckAVZkXZw" alt="Jobstep Youtube" role="button" data-mdb-ripple-color="dark"> <img width="35" height="35" src="<?php getSiteURL(); ?>images/thankyoupageSaveJSInDipl/youtube_logo.png" /></a>
            </section>

        </div>

        <div class="text-center text-dark p-3" style="background-color: rgba(0, 0, 0, 0.2);">
            © 2021 Copyright: Jobstep International d.o.o.<br />
        </div>
    </footer>



</body>
</html>
		<?php
				break;
				case "updBrojPregledaKampanje":
					$kampanja_id = $_POST['kampanja_id'];
					$query_upd = $db->prepare("
								UPDATE idk_kampanje_dipl
								SET kd_broj_pregleda = kd_broj_pregleda + 1
								WHERE kd_id = :kd_id");

					$query_upd->execute(array(
							':kd_id' => $kampanja_id));	
				break;
				
				case "updBrojPregledaLinka":
					$link_id = $_POST['link_id'];
					$query_upd = $db->prepare("
								UPDATE idk_link_generator
								SET lg_broj_pregleda = lg_broj_pregleda + 1
								WHERE lg_id = :lg_id");

					$query_upd->execute(array(
							':lg_id' => $link_id));	
				break;

				case "getEntriesPerLink":
				// FOR FIVE
					// $kampanje = array('111','92','91','90','89','88','87','86','85','84','83','82','81','80','79','78','77','76','75',
// '68', '67', '66', '65', '64', '62', '60', '59', '58', '57', '56', '55', '54', '53', '52', '51','50','49');
						$kampanje = array('115', '116', '117', '118', '119', '120');

					echo '
							<table id = "main-table" width="50%" class = "table" border="1">
							<thead>
								<th>GENERISANI LINK</th>
								<th>BROJ PRIJAVA</th>
								<th>AKCIJE</th>
							</thead>
							<tbody>';
					foreach($kampanje as $kd_id){	
					if($kd_id == "getEntriesPerLink") continue;
						$read_query = $db->prepare("SELECT kd_jezik_forme,kd_broj_prijavljenih,kd_datum_kreiranja
													FROM idk_kampanje_dipl
													WHERE kd_status = 1
													AND kd_id = :kd_id
													");
													
						$read_query->execute(array(
										":kd_id" => $kd_id
										));
										
						$row = $read_query->fetch();
						
						$kd_jezik_forme = $row['kd_jezik_forme'];
						$kd_broj_prijavljenih = $row['kd_broj_prijavljenih'];
						$span_prijavljenih = '<span class="label label-default material-label material-label_default main-container__column">'.$kd_broj_prijavljenih.'</span>';
						$kd_datum_kreiranja = $row['kd_datum_kreiranja'];
						
						
						if( date(strtotime($kd_datum_kreiranja)) > date(strtotime("09-04-2021")))
							$href_link_prijave = "https://job-step.net/usluga/priznavanje-diplome/3/".$kd_id."/".$kd_jezik_forme;
						else $href_link_prijave = "https://job-step.net/nostrifikacija_prijava/".$kd_id."/".$kd_jezik_forme;
						
						echo '
							<tr>
								<td>
									'.$href_link_prijave.'
								</td>
								<td>
									'.$span_prijavljenih.'
								</td>
								<td>
									<button type="button" data-kampanja_id = '.$kd_id.' style="border-radius:3rem" class="button button-default material-label material-label_success main-container__column doughnut_loader">Prikazi detaljniju statistiku</button>
								</td>
							</tr>';
						
					}
					
					 echo '<tr><td colspan="3" align="center"><button type="button" style="border-radius:3rem" data-kampanja_id = "all-viamedia" class="button button-default material-label material-label_success main-container__column doughnut_loader">Prikazi ukupnu statistiku</button>
								</td></tr></tbody></table>';
					 
					echo '<div id ="statistics"></div>';
					echo '<div id="spinner" style="display:none; position:absolute; left:50%; top:50%; 
							  -webkit-animation: spin 1s linear infinite;
									  animation: spin 1s linear infinite;">
								<svg class="animate-spin" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 4335 4335" width="100" height="100">
									<path fill="#008DD2" d="M3346 1077c41,0 75,34 75,75 0,41 -34,75 -75,75 -41,0 -75,-34 -75,-75 0,-41 34,-75 75,-75zm-1198 -824c193,0 349,156 349,349 0,193 -156,349 -349,349 -193,0 -349,-156 -349,-349 0,-193 156,-349 349,-349zm-1116 546c151,0 274,123 274,274 0,151 -123,274 -274,274 -151,0 -274,-123 -274,-274 0,-151 123,-274 274,-274zm-500 1189c134,0 243,109 243,243 0,134 -109,243 -243,243 -134,0 -243,-109 -243,-243 0,-134 109,-243 243,-243zm500 1223c121,0 218,98 218,218 0,121 -98,218 -218,218 -121,0 -218,-98 -218,-218 0,-121 98,-218 218,-218zm1116 434c110,0 200,89 200,200 0,110 -89,200 -200,200 -110,0 -200,-89 -200,-200 0,-110 89,-200 200,-200zm1145 -434c81,0 147,66 147,147 0,81 -66,147 -147,147 -81,0 -147,-66 -147,-147 0,-81 66,-147 147,-147zm459 -1098c65,0 119,53 119,119 0,65 -53,119 -119,119 -65,0 -119,-53 -119,-119 0,-65 53,-119 119,-119z"
									/>
								</svg>
							</div>
							';

					echo '<script>
					
				
							var $= jQuery.noConflict();
							$(".doughnut_loader").on("click",function(){
								$("#spinner").show();
								var kampanja_id = $(this).data("kampanja_id");

								$.ajax({
										url: "https://crm.job-step.com/public_kandidati.php?page=campaignStatistics&campaign_id="+kampanja_id,
										type: "GET",
										dataType: "html",
										success: function(data) {
												
												$("#spinner").hide();
												$("#statistics").html();
												$("#statistics").html(data);
										},
										error: function (xhr, ajaxOptions, thrownError) {
											alert(xhr.status);
											alert(thrownError);
										}
									});
									

							});
						</script>
						';
					 
				break;
				case "campaignStatistics":
					if(isset($_GET['campaign_id'])){
						$kampanja_id = $_GET['campaign_id'];
						if($kampanja_id == 'all') $query = "kampanja_id IN ('112','107','106','104','70','61')";
						else if($kampanja_id == 'all-viamedia')
							// $query = "kampanja_id IN ('111','92','91','90','89','88','87','86','85','84','83','82','81','80','79','78','77','76','75',
// '68', '67', '66', '65', '64', '62', '60', '59', '58', '57', '56', '55', '54', '53', '52', '51','50','49')";
						$query = "kampanja_id IN ('115', '116', '117', '118', '119', '120')";
						else $query = "kampanja_id = ".$kampanja_id;
					?>
						<div class="row">
							<div class="col-md-12" style="margin-top:2rem">
								<div class="content_box">
									<h3 style="margin-bottom:3rem">Statistike kandidata vezanih za ovu kampanju: <?php echo $kampanja_id; ?></h2>
									<div class="row statistike-kampanje">
									
										<div class="chart-div" style="width:40%; margin-right:10rem" >
											<canvas id="plot"></canvas>
											<?php 

												$q_getcountstatusi=$db->prepare('
													select 
													sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=1 THEN 1 else 0 END) AS statLead,
													sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=6 THEN 1 else 0 END) AS Nk1,
													sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=2 THEN 1 else 0 END) AS Nk3,
													sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=3 THEN 1 else 0 END) AS Zld,
													sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=4 THEN 1 else 0 END) AS Nzld,
													sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=5 THEN 1 else 0 END) AS Uobld,
													sum(case when status_nd_kandidata=2 THEN 1 else 0 END) AS Prdok,
													sum(case when status_nd_kandidata=3 THEN 1 else 0 END) AS Pospos,
													sum(case when status_nd_kandidata=4 THEN 1 else 0 END) AS Obrada,
													sum(case when status_nd_kandidata=5 THEN 1 else 0 END) AS Dopdok,
													sum(case when status_nd_kandidata=6 THEN 1 else 0 END) AS Zavrs,
													sum(case when status_nd_kandidata=7 THEN 1 else 0 END) AS Arhiva
													FROM idk_nd_kandidata 
													WHERE '.$query
													);
												$q_getcountstatusi->execute();
												$row_cntst=$q_getcountstatusi->fetch();

												$uk_br_st11_ispis2=$row_cntst['statLead'];
												$uk_br_st16_ispis2=$row_cntst['Nk1'];
												$uk_br_st12_ispis2=$row_cntst['Nk3'];
												$uk_br_st13_ispis2=$row_cntst['Zld'];
												$uk_br_st14_ispis2=$row_cntst['Nzld'];
												$uk_br_st15_ispis2=$row_cntst['Uobld'];
												$uk_br_st2_ispis2=$row_cntst['Prdok'];
												$uk_br_st3_ispis2=$row_cntst['Pospos'];
												$uk_br_st4_ispis2=$row_cntst['Obrada'];
												$uk_br_st5_ispis2=$row_cntst['Dopdok'];
												$uk_br_st6_ispis2=$row_cntst['Zavrs'];
												$uk_br_st7_ispis2=$row_cntst['Arhiva'];
												$ukupan_br_ispis2=$uk_br_st11_ispis2 + $uk_br_st12_ispis2 + $uk_br_st13_ispis2 + $uk_br_st14_ispis2 + $uk_br_st15_ispis2 + $uk_br_st16_ispis2 + $uk_br_st2_ispis2 + $uk_br_st3_ispis2 + $uk_br_st4_ispis2 + $uk_br_st5_ispis2 + $uk_br_st6_ispis2 + $uk_br_st7_ispis2;

											?>
											<script>
											  $.getScript( "https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.1.1/chart.min.js", function() {
												var ctx = document.getElementById('plot').getContext('2d');
												const data = {
												  labels: [
													'Lead (<?php echo $uk_br_st11_ispis2; ?>) - <?php echo $proc_st11_ispis2; ?>',
													'Neuspješan Kontakt 1 (<?php echo $uk_br_st16_ispis2; ?>) - <?php echo $proc_st16_ispis2; ?>',
													'Neuspješan Kontakt 3 (<?php echo $uk_br_st12_ispis2; ?>) - <?php echo $proc_st12_ispis2; ?>',
													'Zainteresiran Lead (<?php echo $uk_br_st13_ispis2; ?>) - <?php echo $proc_st13_ispis2; ?>',
													'Nezainteresiran Lead (<?php echo $uk_br_st14_ispis2; ?>) - <?php echo $proc_st14_ispis2; ?>',
													'U obradi Lead (<?php echo $uk_br_st15_ispis2; ?>) - <?php echo $proc_st15_ispis2; ?>',
													'Prikupljanje dokumentacije (<?php echo $uk_br_st2_ispis2; ?>) - <?php echo $proc_st2_ispis2; ?>',
													'Poslana pošta (<?php echo $uk_br_st3_ispis2; ?>) - <?php echo $proc_st3_ispis2; ?>',
													'U obradi (<?php echo $uk_br_st4_ispis2; ?>) - <?php echo $proc_st4_ispis2; ?>',
													'Dopuna dokumentacije (<?php echo $uk_br_st5_ispis2; ?>) - <?php echo $proc_st5_ispis2; ?>',
													'Završen (<?php echo $uk_br_st6_ispis2; ?>) - <?php echo $proc_st6_ispis2; ?>',
													'Arhiviran (<?php echo $uk_br_st7_ispis2; ?>) - <?php echo $proc_st7_ispis2; ?>'
												],
												  datasets: [{
													data: [
														<?php echo $uk_br_st11_ispis2; ?>,
														<?php echo $uk_br_st16_ispis2; ?>,
														<?php echo $uk_br_st12_ispis2; ?>,
														<?php echo $uk_br_st13_ispis2; ?>,
														<?php echo $uk_br_st14_ispis2; ?>,
														<?php echo $uk_br_st15_ispis2; ?>,
														<?php echo $uk_br_st2_ispis2; ?>,
														<?php echo $uk_br_st3_ispis2; ?>,
														<?php echo $uk_br_st4_ispis2; ?>,
														<?php echo $uk_br_st5_ispis2; ?>,
														<?php echo $uk_br_st6_ispis2; ?>,
														<?php echo $uk_br_st7_ispis2; ?>
													],
													backgroundColor: [
														'rgb(131, 144, 152)',
														'rgb(0, 250, 251)',
														'rgb(0, 251, 83)',
														'rgb(14, 105, 115)',
														'rgb(191, 33, 75)',
														'rgb(199, 156, 255)',
														'rgb(242, 228, 46)',
														'rgb(64, 146, 217)',
														'rgb(139, 218, 242)',
														'rgb(242, 161, 46)',
														'rgb(104, 195, 104)',
														'rgb(243, 65, 60)'
													],
													hoverOffset: 4
												  }]
												};
												var myChart = new Chart(ctx, {
													type: 'pie',
													data: data,
													options: {
														responsive: true,
														maintainAspectRatio: false,
														plugins: {	
															legend: {
																position: 'right',
																align: 'center'
															},
															title: {
																display: true,
																padding: 5,
																position: 'top',
																align: 'center',
																text: 'Ukupno: (<?php echo $ukupan_br_ispis2; ?>)'
															},
														},
														animation: {
															animateScale: true,
															animateRotate: true
														}
													}
												});
											});
											</script>
										</div>
										<div class="uspjesno-izdanih-predracuna">
											<h1>Uspjesno izdanih predracuna (uplata): </h1>
											<?php 
												$ukupno_uspjesnih = (
													$uk_br_st2_ispis2=$row_cntst['Prdok'] +
													$uk_br_st3_ispis2=$row_cntst['Pospos'] +
													$uk_br_st4_ispis2=$row_cntst['Obrada'] +
													$uk_br_st5_ispis2=$row_cntst['Dopdok'] +
													$uk_br_st6_ispis2=$row_cntst['Zavrs']
													);
													
											?>
											<h1><b><?php echo $ukupno_uspjesnih; ?></b> </h1>
										</div>
										
									<style>
										.statistike-kampanje{
											display: flex;
											width:100%;
										}
										.uspjesno-izdanih-predracuna{
											display:flex;
											align-items: center;
										}
									</style>
									</div>
								</div>
							</div>
						</div>
				<?php 
					}
				break;
				case "getEntriesPerLinkLilium":
					$kampanje = array(
							'112','107','106','104','70','61');

					echo '
							<table id = "main-table" width="50%" class = "table" border="1">
							<thead>
								<th>GENERISANI LINK</th>
								<th>BROJ PRIJAVA</th>
								<th>AKCIJE</th>
							</thead>
							<tbody>';
					foreach($kampanje as $kd_id){	
					if($kd_id == "getEntriesPerLink") continue;
						$read_query = $db->prepare("SELECT kd_jezik_forme,kd_broj_prijavljenih,kd_datum_kreiranja
													FROM idk_kampanje_dipl
													WHERE kd_status = 1
													AND kd_id = :kd_id
													");
													
						$read_query->execute(array(
										":kd_id" => $kd_id
										));
										
						$row = $read_query->fetch();
						
						$kd_jezik_forme = $row['kd_jezik_forme'];
						$kd_broj_prijavljenih = $row['kd_broj_prijavljenih'];
						$span_prijavljenih = '<span class="label label-default material-label material-label_default main-container__column">'.$kd_broj_prijavljenih.'</span>';
						$kd_datum_kreiranja = $row['kd_datum_kreiranja'];
						
						
						if( date(strtotime($kd_datum_kreiranja)) > date(strtotime("09-04-2021")))
							$href_link_prijave = "https://job-step.net/usluga/priznavanje-diplome/3/".$kd_id."/".$kd_jezik_forme;
						else $href_link_prijave = "https://job-step.net/nostrifikacija_prijava/".$kd_id."/".$kd_jezik_forme;
						
						echo '
							<tr>
								<td>
									'.$href_link_prijave.'
								</td>
								<td>
									'.$span_prijavljenih.'
								</td>
								<td>
									<button type="button" data-kampanja_id = '.$kd_id.' style="border-radius:3rem" class="button button-default material-label material-label_success main-container__column doughnut_loader">Prikazi detaljniju statistiku</button>
								</td>
							</tr>';
						
					}
					
					 echo '<tr><td colspan="3" align="center"><button type="button" style="border-radius:3rem" data-kampanja_id = "all" class="button button-default material-label material-label_success main-container__column doughnut_loader">Prikazi ukupnu statistiku</button>
								</td></tr></tbody></table>';
					 
					echo '<div id ="statistics"></div>';
					echo '<div id="spinner" style="display:none; position:absolute; left:50%; top:50%; 
							  -webkit-animation: spin 1s linear infinite;
									  animation: spin 1s linear infinite;">
								<svg class="animate-spin" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 4335 4335" width="100" height="100">
									<path fill="#008DD2" d="M3346 1077c41,0 75,34 75,75 0,41 -34,75 -75,75 -41,0 -75,-34 -75,-75 0,-41 34,-75 75,-75zm-1198 -824c193,0 349,156 349,349 0,193 -156,349 -349,349 -193,0 -349,-156 -349,-349 0,-193 156,-349 349,-349zm-1116 546c151,0 274,123 274,274 0,151 -123,274 -274,274 -151,0 -274,-123 -274,-274 0,-151 123,-274 274,-274zm-500 1189c134,0 243,109 243,243 0,134 -109,243 -243,243 -134,0 -243,-109 -243,-243 0,-134 109,-243 243,-243zm500 1223c121,0 218,98 218,218 0,121 -98,218 -218,218 -121,0 -218,-98 -218,-218 0,-121 98,-218 218,-218zm1116 434c110,0 200,89 200,200 0,110 -89,200 -200,200 -110,0 -200,-89 -200,-200 0,-110 89,-200 200,-200zm1145 -434c81,0 147,66 147,147 0,81 -66,147 -147,147 -81,0 -147,-66 -147,-147 0,-81 66,-147 147,-147zm459 -1098c65,0 119,53 119,119 0,65 -53,119 -119,119 -65,0 -119,-53 -119,-119 0,-65 53,-119 119,-119z"
									/>
								</svg>
							</div>
							';

					echo '<script>
					
				
							var $= jQuery.noConflict();
							$(".doughnut_loader").on("click",function(){
								$("#spinner").show();
								var kampanja_id = $(this).data("kampanja_id");

								$.ajax({
										url: "https://crm.job-step.com/public_kandidati.php?page=campaignStatistics&campaign_id="+kampanja_id,
										type: "GET",
										dataType: "html",
										success: function(data) {
												
												$("#spinner").hide();
												$("#statistics").html();
												$("#statistics").html(data);
										},
										error: function (xhr, ajaxOptions, thrownError) {
											alert(xhr.status);
											alert(thrownError);
										}
									});
									

							});
						</script>
						';
					 
				break;
				
				case "step1":
				
				if(isset($_GET['urlid'])){
					$urlid = $_GET['urlid'];
				}else{
					$urlid = 0;
				}

				if(isset($_SERVER['HTTP_REFERER'])) {
  					$kandidat_visitedurl = $_SERVER["HTTP_REFERER"];
   				}else{
  					$kandidat_visitedurl = "";
				}
				
				//UPDATE BROJ PREGLEDA ZA LINK
				$upd_bp_link = $db->prepare("
							UPDATE idk_link_generator
							SET lg_broj_pregleda = lg_broj_pregleda + 1
							WHERE lg_id = :lg_id");

				$upd_bp_link->execute(array(
						':lg_id' => $urlid));
			
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
				
				$ipdat = @json_decode(file_get_contents( 
					"http://www.geoplugin.net/json.gp?ip=" . $ip)); 
				   
				$countryCode = strtolower($ipdat->geoplugin_countryCode); 
				?>
					<style>
						.korak1 {
							background-color: #6097A0;
							text-align: center;
							color: #FFFFFF;
							border-radius: 3px;
						}
						.korak2 {
							background-color: #C4C4C4;
							text-align: center;
							color: #FFFFFF;
							border-radius: 3px;
						}
						.zvjezdica {
							color: #6097A0;
						}
						.select-posao {
							width: 424px;
							height: 42px;
							border: 1px solid rgba(17, 17, 19, 0.2);
							border-radius: 4px;
							background-color: #FFFFFF;
						}
						.input {
							width: 424px;
							height: 42px;
							border: 1px solid rgba(17, 17, 19, 0.2);
							border-radius: 4px;
						}
						.dan-select {
							width: 115px;
							height: 42px;
							border: 1px solid rgba(17, 17, 19, 0.2);
							border-radius: 4px;
							background-color: #FFFFFF;
						}
						.mjesec-select {
							width: 115px;
							height: 42px;
							border: 1px solid rgba(17, 17, 19, 0.2);
							border-radius: 4px;
							background-color: #FFFFFF;
						}
						.godina-select {
							width: 115px;
							height: 42px;
							border: 1px solid rgba(17, 17, 19, 0.2);
							border-radius: 4px;
							background-color: #FFFFFF;
						}
						.material-radio-group_success .material-radio-group__check-radio {
							border-color: #6097A0 !important;
						}
						.material-radio-group_success .material-radio-group__check-radio:after {
							background-color: #3A4053 !important;
						}
						.nastavi {
							width: 251px;
							height: 42px;
							background: #6097A0;
							box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
							border-radius: 4px;
							color: #FFFFFF;
							font-weight: bold;
							margin-top: 10px;
						}
						.icon {
							background: url('images/Vector.svg');
							width: 24px;
							height: 24px;
						}
						.bootstrap-select {
							width: 424px !important;
							height: 42px !important;
							border: 1px solid rgba(17, 17, 19, 0.2);
							border-radius: 4px;
							background-color: #FFFFFF;
						}
						.bootstrap-select > .dropdown-toggle {
							width: 100%;
							padding-right: 25px;
							z-index: 1;
							height: 100%;
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
						}
												
					</style>
					<noscript>
						<div style="position: fixed; top: 0px; left: 0px; z-index: 30000000; 
									height: 100%; width: 100%; background-color: #FFFFFF">
							<h1 style="margin-left: 25vw;"><strong>Da biste pristupili stranici molimo vas da uključite JavaScript u vašem pretraživaču.</strong></h1>
						</div>
					</noscript>
					<div class="row">
						<div class="col-sm-7">
							<h3><strong><?php echo $txt_reg_korakjedan_prijava_za_posao; ?></strong></h3>
							<p><?php echo $txt_reg_korakjedan_prijava_za_posao_desc; ?></p>
						</div>
						<div class="col-sm-4">
							<form method="POST" action="">
								<ul class="list-inline pull-right" style="width: fit-content; list-style:none;">
									<li><b><?php echo $txt_jezik; ?></b></li>
									<li><b>|</b></li>
									<li class="language_list">
										<button name="lang" type="submit" value="de" style="background: white; border: 0px;" ><img src="<?php getSiteURL(); ?>images/de3d.png" height="24" width="24"></button>
									</li>
									<li><b>|</b></li>
									<li class="language_list">
										<button name="lang" type="submit" value="bs" style="background: white; border: 0px;" >
											<img src="<?php getSiteURL(); ?>images/bs3d.png" height="24" width="24"> 
											<img style="margin-left: 5px; margin-right: 5px;" src="<?php getSiteURL(); ?>images/hr3d.png" height="24" width="24"> 
											<img src="<?php getSiteURL(); ?>images/sr3d.png" height="24" width="24">
										</button>
									</li>
								</ul>
							</form>
						</div>
					</div>
					<div class="row" style="margin-top: 15px;">
						<div class="col-xs-6 korak1">
							<h4><?php echo $txt_reg_korakjedan_korak1; ?></h4>
						</div>
						<div class="col-xs-6 korak2">
							<h4><?php echo $txt_reg_korakjedan_korak2; ?></h4>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12" style="color: #6097A0; text-align: left; margin-top: 20px;">
							<span class="icon"></span>
							<h4><strong><?php echo $txt_reg_korakjedan_osnovneinfo; ?></strong></h4>
						</div>
					</div>
					<div class="row" style="margin-top: 15px;">
						<div class="col-md-offset-1 col-md-8">
							<form action="<?php getSiteURL(); ?>public_kandidati?page=add_kandidat_new" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
								<input type="hidden" id="countryCode" name="countryCode" value="<?php echo $countryCode; ?>">
								<input type="hidden" name="kandidat_visitedurl" value="<?php echo $kandidat_visitedurl; ?>">
								<input type="hidden" name="urlid" value="<?php echo $urlid; ?>">
								<input type="hidden" name="token" value="<?php echo $token; ?>">
								<input type="hidden" name="nalogid" value="<?php echo $lg_nalogid; ?>">
								<input type="hidden" name="lg_language" value="<?php echo $lg_language; ?>">
								<input type="hidden" id="countryCode" name="countryCode" value="<?php echo $countryCode; ?>">
							
								<?php
									if(isset($_GET['urlid'])){
										$urlid = $_GET['urlid'];
								?>
									<div class="form-group">
										<label for="kandidat_prijava_na" class="col-sm-5 control-label"><strong><?php echo $txt_reg_korakjedan_radno_mjesto ; ?></strong><span class="zvjezdica">*</span><strong>:</strong></label>
										<div class="col-sm-6">
											<select class="select-posao" id="kandidat_prijava_na" name="kandidat_prijava_na" >
											<?php
												$query_job_type = $db->prepare("
																SELECT lr_groupid, kg_title, kg_title_de, kg_title_it
																FROM idk_link_generator_rel
																INNER JOIN idk_kandidati_grupe ON idk_link_generator_rel.lr_groupid = idk_kandidati_grupe.kg_id
																WHERE lr_lgid = :lr_lgid
																");

												$query_job_type->execute(array(
													":lr_lgid" => $urlid
												));
												$count = $query_job_type->rowCount();
												while($job_type = $query_job_type->fetch()){

													$lr_groupid = $job_type['lr_groupid'];
													if($lg_language == "bs"){
														$kg_title = $job_type['kg_title'];
													}
													else if($lg_language == "de"){
														$kg_title = $job_type['kg_title_de'];
														if($kg_title == ""){
															$kg_title = $job_type['kg_title'];
														}
													}else{
														$kg_title = $job_type['kg_title_it'];
														if($kg_title == ""){
															$kg_title = $job_type['kg_title'];
														}
													}

											?>
                                                <option value="<?php echo $lr_groupid; ?>"><?php echo $kg_title; ?></option>
											<?php } ?>
    										</select>
										</div>
									</div>
								<?php }else{ ?>
									<div class="form-group">
										<label for="kandidat_prijava_na" class="col-sm-5 control-label"><strong><?php echo $txt_reg_korakjedan_radno_mjesto ; ?></strong><span class="zvjezdica">*</span><strong>:</strong></label>
										<div class="col-sm-6">
                                            <select class="select-posao" id="kandidat_prijava_na" name="kandidat_prijava_na" title="<?php echo $txt_reg_korakjedan_nista_izabrano; ?>">
											<?php
												$query_job_type = $db->prepare("
																SELECT kg_id, kg_title
																FROM idk_kandidati_grupe
																");

												$query_job_type->execute();
												$count = $query_job_type->rowCount();
												while($job_type = $query_job_type->fetch()){

													$kg_id = $job_type['kg_id'];
													$kg_title = $job_type['kg_title'];

											?> 
                                                <option value="<?php echo $kg_id; ?>"><?php echo $kg_title; ?></option>
											<?php } ?>
    										</select>
										</div>
									</div>
								<?php } ?>
									<input type="hidden" name="recaptcha_response" id="recaptchaResponse">
								<div class="form-group">
									<label for="kandidat_ime" class="col-sm-5 control-label"><strong><?php echo $txt_reg_korakjedan_ime ; ?></strong><span class="zvjezdica">*</span><strong>:</strong></label>
									<div class="col-sm-6">
										<input class="input" type="text" name="kandidat_ime" id="kandidat_ime" placeholder="<?php echo $txt_reg_korakjedan_ime ; ?>" required>
									</div>
								</div>
								<div class="form-group">
									<label for="kandidat_prezime" class="col-sm-5 control-label"><strong><?php echo $txt_reg_korakjedan_prezime ; ?></strong><span class="zvjezdica">*</span><strong>:</strong></label>
									<div class="col-sm-6">
										<input class="input" type="text" name="kandidat_prezime" id="kandidat_prezime" placeholder="<?php echo $txt_reg_korakjedan_prezime ; ?>" required>
									</div>
								</div>
								<div class="form-group">
									<label for="datum_dan" class="col-sm-5 control-label"><strong><?php echo $txt_reg_korakjedan_datum_rodjenja ; ?></strong><span class="zvjezdica">*</span><strong>:</strong></label>
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
												for ($year=2004; $year>1940; $year--){ ?>
													<option value="<?php echo $year; ?>"><?php echo $year; ?></option>
												<?php } ?>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label for="kki_phone" class="col-sm-5 control-label"><strong><?php echo $txt_reg_korakjedan_mobilni_tel; ?></strong><span class="zvjezdica">*</span><strong>:</strong></label>
									<div class="col-sm-6">
										<div class="">
											<input class="input" type="tel" name="kki_phone" id="kki_phone" required>
										</div>
									</div>
								</div>
								<script>
									$( document ).ready(function($) {
										$.each($('input[type=tel]'),function(){
											var telInput = $(this);
											//var countryCode = $("#countryCode").val;
											var countryCode = document.getElementById("countryCode").value 
											console.log(countryCode);
											if ($(this).val().startsWith("+") || $(this).val() == '') {
												$(telInput).intlTelInput({
													utilsScript:'https://intl-tel-input.com/node_modules/intl-tel-input/build/js/utils.js',
													autoPlaceholder: "aggressive",
													initialCountry: ""+countryCode+"",
													formatOnDisplay: true,
													preferredCountries: ["ba","hr","de","it","rs"],
													separateDialCode: true
												});
											}
										});
										//SPAJANJE COUNTRY CODA I TELEFONSKOG BROJA
										$("form").submit(function(event) {
											//event.preventDefault();
											$.each($('input[type=tel]'),function(){
												var telInput = $(this);	
												var telType = telInput.data('type');	
												telInput.val(telInput.intlTelInput("getNumber"));  
											});
										});	
									});
								</script>
								<div class="form-group">
									<label for="kandidat_drzavljanstvo_vrsta" class="col-sm-5 control-label"><strong><?php echo $txt_reg_korakjedan_drzavljanstvo ; ?></strong><span class="zvjezdica">*</span><strong>:</strong></label>
									<div class="col-sm-6">
										<div class="materail-input-block materail-input-block_success idk_radio_buttons">
											<label class="main-container__column material-radio-group material-radio-group_success" for="kandidat_drzavljanstvo_vrsta_eu">
												<input type="radio" name="kandidat_drzavljanstvo_vrsta" id="kandidat_drzavljanstvo_vrsta_eu" class="material-radiobox" value="EU državljanin" />
												<span class="material-radio-group__element material-radio-group__check-radio"></span>
												<span class="material-radio-group__element material-radio-group__caption"><?php echo $txt_reg_korakjedan_eu_drzavljanin; ?></span>
											</label>
										</div>
										<div class="materail-input-block materail-input-block_success idk_radio_buttons">
											<label class="main-container__column material-radio-group material-radio-group_success" for="kandidat_drzavljanstvo_vrsta_non">
												<input type="radio" name="kandidat_drzavljanstvo_vrsta" id="kandidat_drzavljanstvo_vrsta_non" class="material-radiobox" value="NON-EU državljanin" checked />
												<span class="material-radio-group__element material-radio-group__check-radio"></span>
												<span class="material-radio-group__element material-radio-group__caption"><?php echo $txt_reg_korakjedan_non_eu_drzavljanin; ?></span>
											</label>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label for="kandidat_jezik" class="col-sm-5 control-label"><strong><?php echo $txt_reg_korakjedan_nivo_njemackog; ?></strong><span class="zvjezdica">*</span><strong>:</strong></label>
									<div class="col-sm-6">
										<select class="select-posao" id="kandidat_jezik" name="nivo_jezika"  data-live-search="true" required>
											<option selected disabled>Odaberi...</option>
											<option value="Bez znanja">Bez znanja</option>
											<option value="A1">A1</option>
											<option value="A2">A2</option>
											<option value="B1">B1</option>
											<option value="B2">B2</option>
											<option value="C1">C1</option>
											<option value="C2">C2</option>
										</select>
									</div>
								</div>
								<div class="form-group" id="skola_select">
									<label for="skola_naziv" class="col-sm-5 control-label"><strong><?php echo $txt_reg_koraktri_zavrseno_obr; ?></strong><span class="zvjezdica">*</span><strong>:</strong></label>
									<div class="col-sm-6">
										<select class="selectpicker" name="skola_naziv" id="skola_naziv" data-live-search="true" required>
											<option value="" selected disabled>Odaberi...</option>
											<?php 
												$query_skole = $db->prepare("
														SELECT * FROM idk_skole WHERE skola_tip_obrazovanja = 'srednje' ORDER BY skola_naziv
												");
												$query_skole->execute();
												while($row_skola = $query_skole->fetch()){
													$skola_tip = $row_skola['skola_tip_obrazovanja'];
													$skola_id = $row_skola['skola_id'];
													if($lg_language == "bs"){
													$skola_naziv = $row_skola['skola_naziv'];
												}
												else if($lg_language == "de"){
													$skola_naziv = $row_skola['skola_naziv_de'];
													if($skola_naziv == ""){
														$skola_naziv = $row_skola['skola_naziv'];
													}
												}else{
													$skola_naziv = $row_skola['skola_naziv'];
												}
													?>
													<option value="<?php echo $skola_id; ?>" data-skola_id="<?php echo $skola_id; ?>"><?php echo $skola_naziv; ?></option>
													<?php
												}
											?>
											<option value="ostalo"><?php echo $txt_reg_koraktri_ostalo; ?></option>
										</select>
									</div>
								</div>
								<div class="form-group" id="smjer_select">
									<label for="smjer_naziv" class="col-sm-5 control-label"><strong><?php echo $txt_reg_koraktri_zvanje_smjer; ?></strong><span class="zvjezdica">*</span><strong>:</strong></label>
									<div class="col-sm-6">
										<select class="selectpicker" name="smjer_naziv" id="smjer_naziv" data-live-search="true" required >
											<option value="" selected disabled><?php echo $txt_reg_koraktri_odaberi; ?></option>
											<?php 
												$query_skole_smjer = $db->prepare("
														SELECT * FROM idk_skole_smjerovi ORDER BY ss_naziv
												");
												$query_skole_smjer->execute();
												
												while($row_skola_smjer = $query_skole_smjer->fetch()){
													$smjer_skola_id = $row_skola_smjer['ss_skola_id'];
													$smjer_id = $row_skola_smjer['ss_id'];
													if($lg_language == "bs"){
														$smjer_naziv = $row_skola_smjer['ss_naziv'];
													}
													else if($lg_language == "de"){
														$smjer_naziv = $row_skola_smjer['ss_naziv_de'];
														if($smjer_naziv == ""){
															$smjer_naziv = $row_skola_smjer['ss_naziv'];
														}
													}else{
														$smjer_naziv = $row_skola_smjer['ss_naziv'];
													}
													?>
													<option value="<?php echo $smjer_id; ?>" class="hidden smjerovi_all opt_<?php echo $smjer_skola_id; ?>"><?php echo $smjer_naziv; ?></option>
													<?php
												}
											?>
										</select>
									</div>
								</div>
								<div class="form-group" id="unos_skole" style="display: none;">
									<label for="skola_naziv_ru" class="col-sm-5 control-label"><strong><?php echo $txt_naziv_skole ; ?>:</strong></label>
									<div class="col-sm-6">
										<input class="input" type="text" name="skola_naziv_ru" id="skola_naziv_ru" placeholder="<?php echo $txt_naziv_skole ; ?>" >
									</div>
								</div>
								<div class="form-group" id="unos_smjera" style="display: none;">
									<label for="smjer_naziv_ru" class="col-sm-5 control-label"><strong><?php echo $txt_naziv_smjera ; ?>:</strong></label>
									<div class="col-sm-6">
										<input class="input" type="text" name="smjer_naziv_ru" id="smjer_naziv_ru" placeholder="<?php echo $txt_naziv_smjera ; ?>" >
									</div>
								</div>
								<div class="form-group" id="viza_viza">
									<label for="kandidat_viza" class="col-sm-5 control-label"><strong>Da li imate vizu u Njemačkoj?</strong><span class="zvjezdica">*</span></label>
									<div class="col-sm-3">
										<label class="main-container__column material-radio-group material-radio-group_success" for="viza_da">
											<input type="radio" name="kandidat_viza" id="viza_da" class="material-radiobox" value="1"/>
											<span class="material-radio-group__element material-radio-group__check-radio"></span>
											<span class="material-radio-group__element material-radio-group__caption">Da</span>
										</label>
									</div>
									<div class="col-sm-3">
										<label class="main-container__column material-radio-group material-radio-group_success" for="viza_ne">
											<input type="radio" name="kandidat_viza" id="viza_ne" class="material-radiobox" value="0" />
											<span class="material-radio-group__element material-radio-group__check-radio"></span>
											<span class="material-radio-group__element material-radio-group__caption">Ne</span>
										</label>
									</div>
								</div>
								<div class="form-group" id="viza_datum_vrijedi_do">
									<label for="kandidat_viza_vrijedi_do" class="col-sm-5 control-label"><strong><?php echo $txt_reg_korakjedan_viza_vrijedi_do ; ?>?</strong></label>
									<div class="col-sm-6">
										<input class="input" type="text" name="kandidat_viza_vrijedi_do" id="kandidat_viza_vrijedi_do" placeholder="<?php echo $txt_reg_korakjedan_viza_datum_isteka; ?>">
									</div>
									<script>
									<?php $year_sixteen = date('Y') - 16; ?>
									$( function() {
										$( "#kandidat_viza_vrijedi_do" ).datepicker({
										changeMonth: true,
										changeYear: true,
											dateFormat: 'dd.mm.yy' ,
											yearRange: '2020:2050'
										});
									} );
									</script>
								</div>
								<!-- TERMIN -->
								<div class="form-group" id="viza_termin">
									<label for="kandidat_email" class="col-sm-5 control-label"><strong><?php echo $txt_reg_korakjedan_termin_pitanje ; ?></strong></label>
									<div class="col-sm-3">
										<label class="main-container__column material-radio-group material-radio-group_success" for="termin_daa">
											<input type="radio" name="kandidat_termin" id="termin_daa" class="material-radiobox" value="1"/>
											<span class="material-radio-group__element material-radio-group__check-radio"></span>
											<span class="material-radio-group__element material-radio-group__caption"><?php echo $txt_reg_da ; ?></span>
										</label>
									</div>
									<div class="col-sm-3">
										<label class="main-container__column material-radio-group material-radio-group_success" for="termin_nee">
											<input type="radio" name="kandidat_termin" id="termin_nee" class="material-radiobox" value="0"/>
											<span class="material-radio-group__element material-radio-group__check-radio"></span>
											<span class="material-radio-group__element material-radio-group__caption"><?php echo $txt_reg_ne ; ?></span>
										</label>
									</div>
								</div>
								<div class="form-group" id="viza_datum_termina">
									<label for="kandidat_termin_date" class="col-sm-5 control-label"><strong><?php echo $txt_reg_korakjedan_datum_termina; ?></strong></label>
									<div class="col-sm-6">
										<input class="input" type="text" name="kandidat_termin_date" id="kandidat_termin_date" placeholder="<?php echo $txt_reg_korakjedan_datum_termina; ?>">
									</div>
									<script>
									<?php $year_sixteen = date('Y') - 16; ?>
									$( function() {
										$( "#kandidat_termin_date" ).datepicker({
										changeMonth: true,
										changeYear: true,
											dateFormat: 'dd.mm.yy' ,
											yearRange: '2018:2025'
										});
									} );
									</script>
								</div>
								<!-- APLICIRANJE -->
								<div class="form-group" id="viza_apliciranje" style="margin-top: 10px !important;">
									<label for="kandidat_apliciranje" class="col-sm-5 control-label"><strong><?php echo $txt_reg_korakjedan_aplikacija_za_vizu ; ?>?</strong></label>
									<div class="col-sm-3">
										<label class="main-container__column material-radio-group material-radio-group_success" for="apliciranje_da">
											<input type="radio" name="kandidat_apliciranje" id="apliciranje_da" class="material-radiobox" value="1"/>
											<span class="material-radio-group__element material-radio-group__check-radio"></span>
											<span class="material-radio-group__element material-radio-group__caption"><?php echo $txt_reg_da ; ?></span>
										</label>
									</div>
									<div class="col-sm-3">
										<label class="main-container__column material-radio-group material-radio-group_success" for="apliciranje_ne">
											<input type="radio" name="kandidat_apliciranje" id="apliciranje_ne" class="material-radiobox" value="0"/>
											<span class="material-radio-group__element material-radio-group__check-radio"></span>
											<span class="material-radio-group__element material-radio-group__caption"><?php echo $txt_reg_ne ; ?></span>
										</label>
									</div>
								</div>
								
								<div class="form-group" id="viza_datum_apliciranja">
									<label for="kandidat_termin_date_app" class="col-sm-5 control-label"><strong><?php echo $txt_reg_korakjedan_kad_aplikacija ; ?></strong></label>
									<div class="col-sm-6">
										<input class="input" type="text" name="kandidat_termin_date_app" id="kandidat_termin_date_app" placeholder="<?php echo $txt_reg_korakjedan_datum_aplikacije; ?>">
									</div>
									<script>
									<?php $year_sixteen = date('Y') - 16; ?>
									$( function() {
										$( "#kandidat_termin_date_app" ).datepicker({
										changeMonth: true,
										changeYear: true,
											dateFormat: 'dd.mm.yy' ,
											yearRange: '2017:2019'
										});
									} );
									</script>
								</div>
								<script>
									
									$(document).ready(function() {
										jezik = $(document).find('input[name="lg_language"]').val();
										console.log(jezik)
										if(jezik == 'de' || jezik == 'it')
											$('#viza_viza').hide();
										
										$('#viza_datum_vrijedi_do').hide();
										$('#viza_termin').hide();
										$('#viza_datum_termina').hide();
										$('#viza_apliciranje').hide();
										$('#viza_datum_apliciranja').hide();
										
									});
									$('#kandidat_drzavljanstvo_vrsta_eu').click(function() {
										if($('#kandidat_drzavljanstvo_vrsta_eu').is(':checked')) { 
											$('#viza_viza').hide();
											$('#viza_datum_vrijedi_do').hide();
											$('#viza_termin').hide();
											$('#viza_datum_termina').hide();
											$('#viza_apliciranje').hide();
											$('#viza_datum_apliciranja').hide();
											$('#boravak_eu').hide();
											$('#eu_drzava_step1').hide();
											$('#skole_step1').hide();
											$('#smjer_step1').hide();
											$("#viza_da").prop("checked", false);
											$("#viza_ne").prop("checked", false);
											$("#termin_daa").prop("checked", false);
											$("#termin_nee").prop("checked", false);
											$("#apliciranje_da").prop("checked", false);
											$("#apliciranje_ne").prop("checked", false);
											$("#kandidat_boravak_eu_da").prop("checked", false);
											$("#kandidat_boravak_eu_ne").prop("checked", false);
											$('#skola_select').hide();
											$('#smjer_select').hide();
											$('#unos_skole').hide();
											$('#unos_smjera').hide();
											$("#skola_naziv").prop('required',false);
											$("#smjer_naziv").prop('required',false);
										}
									});
									
									$('#kandidat_drzavljanstvo_vrsta_non').click(function() {
										if($('#kandidat_drzavljanstvo_vrsta_non').is(':checked')) { 
											$('#viza_viza').show();
											$('#boravak_eu').show();
											$('#eu_drzava').selectpicker('val', '');
											$('#skole_step1').show();
											$('#smjer_step1').show();
											$('#skola_select').show();
											$('#smjer_select').show();
											$("#skola_naziv").prop('required',true);
											$("#smjer_naziv").prop('required', true);
										}
									});
									
									$('#kandidat_boravak_eu_da').click(function() {
										if($('#kandidat_boravak_eu_da').is(':checked')) { 
											$('#eu_drzava_step1').show();
											$('#skole_step1').hide();
											$('#smjer_step1').hide();
											$('#skola_select').hide();
											$('#smjer_select').hide();
											$('#unos_skole').hide();
											$('#unos_smjera').hide();
											$("#skola_naziv").prop('required',false);
											$("#smjer_naziv").prop('required',false);
										}
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
										}else{
											$('#smjer_step1').show();
											$('#smjer_select').show();
											$('#unos_skole').hide();
											$('#unos_smjera').hide();
											$("#smjer_naziv").prop('required',true);
										}
										
									});

									$('#viza_da').click(function() {
										if($('#viza_da').is(':checked')) { 
											$('#viza_datum_vrijedi_do').show();
											$('#viza_termin').hide();
											$('#viza_datum_termina').hide();
											$('#viza_apliciranje').hide();
											$('#viza_datum_apliciranja').hide();
											$("#termin_daa").prop("checked", false);
											$("#termin_nee").prop("checked", false);
											$("#apliciranje_da").prop("checked", false);
											$("#apliciranje_ne").prop("checked", false);
										}
									});
									$('#viza_ne').click(function() {
										if($('#viza_ne').is(':checked')) { 
											$('#viza_datum_vrijedi_do').hide();
											$('#viza_termin').show();
										}
									});
									$('#termin_daa').click(function() {
										if($('#termin_daa').is(':checked')) { 
											$('#viza_datum_termina').show();
											$('#viza_apliciranje').hide();
											$('#viza_datum_apliciranja').hide();
											$("#apliciranje_da").prop("checked", false);
											$("#apliciranje_ne").prop("checked", false);
										}
									});
									$('#termin_nee').click(function() {
										if($('#termin_nee').is(':checked')) { 
											$('#viza_datum_termina').hide();
											$('#viza_apliciranje').show();
										}
									});
									
									$('#apliciranje_da').click(function() {
										if($('#apliciranje_da').is(':checked')) { 
											$('#viza_datum_apliciranja').show();
										}
									});
									$('#apliciranje_ne').click(function() {
										if($('#apliciranje_ne').is(':checked')) { 
											$('#viza_datum_apliciranja').hide();
										}
									});
								</script>
								<br />
								<div class="form-group">
									<div class="col-sm-offset-2 col-sm-10 text-right">
										<button id="button_nastavi" type="submit" class="nastavi"><?php echo $txt_reg_nastavi ; ?> <span><i class="fa fa-arrow-right" aria-hidden="true"></i></span></button>
										<br /><small><?php echo $txt_reg_korakjedan_obavezna_polja1 ; ?><span class="zvjezdica">*</span><?php echo $txt_reg_korakjedan_obavezna_polja2 ; ?></small>
									</div>
								</div>
								
								<div class="form-group">
									<label for="asd" class="col-sm-3 control-label"><span class="text-danger"></span></label>
									
									<div class="col-sm-6" style="padding-top: 10px;">
										
										<p class = "text-center"> <?php echo $txt_reg_korakjedan_prihvatanje_privatnosti ; ?>
										<br><a href="" type="button" data-toggle="modal" data-target="#privacyModal"><?php echo $txt_reg_korakjedan_pogledaj_izjavu ; ?></a>
										</p>
									</div>
								</div>
								<!-- Modal privacy -->
								<div class="modal fade modal-fullscreen" id="privacyModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
								  <div class="modal-dialog" role="document">
									<div class="modal-content">
									  <div class="modal-header">
										<h5 class="modal-title" id="exampleModalLabel"><?php echo $txt_reg_korakjedan_izjava_h5 ; ?></h5>
									  </div>
									  <div class="modal-body">
										<p><?php echo $txt_reg_korakjedan_izjava_tekst ; ?></p>
									  </div>
									  <div class="modal-footer">
										<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $txt_reg_zatvori; ?></button>
									  </div>
									</div>
								  </div>
								</div>
							</form>
						</div>
					</div>
					
				<?php
				break;
				
				case "test_search":
				if(isset($_GET['urlid'])){
					$urlid = $_GET['urlid'];
				}else{
					$urlid = 0;
				}

				if(isset($_SERVER['HTTP_REFERER'])) {
  					$kandidat_visitedurl = $_SERVER["HTTP_REFERER"];
   				}else{
  					$kandidat_visitedurl = "";
				}
				
				//UPDATE BROJ PREGLEDA ZA LINK
				$upd_bp_link = $db->prepare("
							UPDATE idk_link_generator
							SET lg_broj_pregleda = lg_broj_pregleda + 1
							WHERE lg_id = :lg_id");

				$upd_bp_link->execute(array(
						':lg_id' => $urlid));
			
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
				
				$ipdat = @json_decode(file_get_contents( 
					"http://www.geoplugin.net/json.gp?ip=" . $ip)); 
				   
				$countryCode = strtolower($ipdat->geoplugin_countryCode); 
				?>
					<style>
						.korak1 {
							background-color: #6097A0;
							text-align: center;
							color: #FFFFFF;
							border-radius: 3px;
						}
						.korak2 {
							background-color: #C4C4C4;
							text-align: center;
							color: #FFFFFF;
							border-radius: 3px;
						}
						.zvjezdica {
							color: #6097A0;
						}
						.btn-group bootstrap-select {
							width: 424px;
							height: 42px;
							border: 1px solid rgba(17, 17, 19, 0.2);
							border-radius: 4px;
							background-color: #FFFFFF;
						}
						.input {
							width: 424px;
							height: 42px;
							border: 1px solid rgba(17, 17, 19, 0.2);
							border-radius: 4px;
						}
						.dan-select {
							width: 115px;
							height: 42px;
							border: 1px solid rgba(17, 17, 19, 0.2);
							border-radius: 4px;
							background-color: #FFFFFF;
						}
						.mjesec-select {
							width: 115px;
							height: 42px;
							border: 1px solid rgba(17, 17, 19, 0.2);
							border-radius: 4px;
							background-color: #FFFFFF;
						}
						.godina-select {
							width: 115px;
							height: 42px;
							border: 1px solid rgba(17, 17, 19, 0.2);
							border-radius: 4px;
							background-color: #FFFFFF;
						}
						.material-radio-group_success .material-radio-group__check-radio {
							border-color: #6097A0 !important;
						}
						.material-radio-group_success .material-radio-group__check-radio:after {
							background-color: #3A4053 !important;
						}
						.nastavi {
							width: 251px;
							height: 42px;
							background: #6097A0;
							box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
							border-radius: 4px;
							color: #FFFFFF;
							font-weight: bold;
							margin-top: 10px;
						}
						.icon {
							background: url('images/Vector.svg');
							width: 24px;
							height: 24px;
						}
						.bootstrap-select {
							width: 424px !important;
							height: 42px !important;
							border: 1px solid rgba(17, 17, 19, 0.2);
							border-radius: 4px;
							background-color: #FFFFFF;
						}
						.bootstrap-select > .dropdown-toggle {
							width: 100%;
							padding-right: 25px;
							z-index: 1;
							height: 100%;
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
						}
												
					</style>
					
					<div class="row">
						<div class="col-sm-7">
							<h3><strong><?php echo $txt_reg_korakjedan_prijava_za_posao; ?></strong></h3>
							<p><?php echo $txt_reg_korakjedan_prijava_za_posao_desc; ?></p>
						</div>
						<div class="col-sm-4">
							<form method="POST" action="">
								<ul class="list-inline pull-right" style="width: fit-content; list-style:none;">
									<li><b><?php echo $txt_jezik; ?></b></li>
									<li><b>|</b></li>
									<li class="language_list">
										<button name="lang" type="submit" value="de" style="background: white; border: 0px;" ><img src="<?php getSiteURL(); ?>images/de3d.png" height="24" width="24"></button>
									</li>
									<li><b>|</b></li>
									<li class="language_list">
										<button name="lang" type="submit" value="bs" style="background: white; border: 0px;" >
											<img src="<?php getSiteURL(); ?>images/bs3d.png" height="24" width="24"> 
											<img style="margin-left: 5px; margin-right: 5px;" src="<?php getSiteURL(); ?>images/hr3d.png" height="24" width="24"> 
											<img src="<?php getSiteURL(); ?>images/sr3d.png" height="24" width="24">
										</button>
									</li>
								</ul>
							</form>
						</div>
					</div>
					<div class="row" style="margin-top: 15px;">
						<div class="col-xs-6 korak1">
							<h4><?php echo $txt_reg_korakjedan_korak1; ?></h4>
						</div>
						<div class="col-xs-6 korak2">
							<h4><?php echo $txt_reg_korakjedan_korak2; ?></h4>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12" style="color: #6097A0; text-align: left; margin-top: 20px;">
							<span class="icon"></span>
							<h4><strong><?php echo $txt_reg_korakjedan_osnovneinfo; ?></strong></h4>
						</div>
					</div>
					<div class="row" style="margin-top: 15px;">
						<div class="col-md-offset-1 col-md-8">
							<form action="<?php getSiteURL(); ?>public_kandidati?page=add_kandidat_new" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
								<input type="hidden" id="countryCode" name="countryCode" value="<?php echo $countryCode; ?>">
								<input type="hidden" name="kandidat_visitedurl" value="<?php echo $kandidat_visitedurl; ?>">
								<input type="hidden" name="urlid" value="<?php echo $urlid; ?>">
								<input type="hidden" name="token" value="<?php echo $token; ?>">
								<input type="hidden" name="nalogid" value="<?php echo $lg_nalogid; ?>">
								<input type="hidden" name="lg_language" value="<?php echo $lg_language; ?>">
								<input type="hidden" id="countryCode" name="countryCode" value="<?php echo $countryCode; ?>">
							
								<?php
									if(isset($_GET['urlid'])){
										$urlid = $_GET['urlid'];
								?>
									<div class="form-group">
										<label for="kandidat_prijava_na" class="col-sm-5 control-label"><strong><?php echo $txt_reg_korakjedan_radno_mjesto ; ?></strong><span class="zvjezdica">*</span><strong>:</strong></label>
										<div class="col-sm-6">
											<select class="selectpicker" id="kandidat_prijava_na" name="kandidat_prijava_na">
											<?php
												$query_job_type = $db->prepare("
																SELECT lr_groupid, kg_title, kg_title_de, kg_title_it
																FROM idk_link_generator_rel
																INNER JOIN idk_kandidati_grupe ON idk_link_generator_rel.lr_groupid = idk_kandidati_grupe.kg_id
																WHERE lr_lgid = :lr_lgid
																");

												$query_job_type->execute(array(
													":lr_lgid" => $urlid
												));
												$count = $query_job_type->rowCount();
												while($job_type = $query_job_type->fetch()){

													$lr_groupid = $job_type['lr_groupid'];
													if($lg_language == "bs"){
														$kg_title = $job_type['kg_title'];
													}
													else if($lg_language == "de"){
														$kg_title = $job_type['kg_title_de'];
														if($kg_title == ""){
															$kg_title = $job_type['kg_title'];
														}
													}else{
														$kg_title = $job_type['kg_title_it'];
														if($kg_title == ""){
															$kg_title = $job_type['kg_title'];
														}
													}

											?>
                                                <option value="<?php echo $lr_groupid; ?>"><?php echo $kg_title; ?></option>
											<?php } ?>
    										</select>
										</div>
									</div>
								<?php }else{ ?>
									<div class="form-group">
										<label for="kandidat_prijava_na" class="col-sm-5 control-label"><strong><?php echo $txt_reg_korakjedan_radno_mjesto ; ?></strong><span class="zvjezdica">*</span><strong>:</strong></label>
										<div class="col-sm-6">
                                            <select class="selectpicker" id="kandidat_prijava_na" name="kandidat_prijava_na" title="<?php echo $txt_reg_korakjedan_nista_izabrano; ?>">
											<?php
												$query_job_type = $db->prepare("
																SELECT kg_id, kg_title
																FROM idk_kandidati_grupe
																");

												$query_job_type->execute();
												$count = $query_job_type->rowCount();
												while($job_type = $query_job_type->fetch()){

													$kg_id = $job_type['kg_id'];
													$kg_title = $job_type['kg_title'];

											?> 
                                                <option value="<?php echo $kg_id; ?>"><?php echo $kg_title; ?></option>
											<?php } ?>
    										</select>
										</div>
									</div>
								<?php } ?>
									<input type="hidden" name="recaptcha_response" id="recaptchaResponse">
								<div class="form-group">
									<label for="kandidat_ime" class="col-sm-5 control-label"><strong><?php echo $txt_reg_korakjedan_ime ; ?></strong><span class="zvjezdica">*</span><strong>:</strong></label>
									<div class="col-sm-6">
										<input class="input" type="text" name="kandidat_ime" id="kandidat_ime" placeholder="<?php echo $txt_reg_korakjedan_ime ; ?>" required>
									</div>
								</div>
								<div class="form-group">
									<label for="kandidat_prezime" class="col-sm-5 control-label"><strong><?php echo $txt_reg_korakjedan_prezime ; ?></strong><span class="zvjezdica">*</span><strong>:</strong></label>
									<div class="col-sm-6">
										<input class="input" type="text" name="kandidat_prezime" id="kandidat_prezime" placeholder="<?php echo $txt_reg_korakjedan_prezime ; ?>" required>
									</div>
								</div>
								<div class="form-group">
									<label for="datum_dan" class="col-sm-5 control-label"><strong><?php echo $txt_reg_korakjedan_datum_rodjenja ; ?></strong><span class="zvjezdica">*</span><strong>:</strong></label>
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
												for ($year=2004; $year>1940; $year--){ ?>
													<option value="<?php echo $year; ?>"><?php echo $year; ?></option>
												<?php } ?>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label for="kki_phone" class="col-sm-5 control-label"><strong><?php echo $txt_reg_korakjedan_mobilni_tel; ?></strong><span class="zvjezdica">*</span><strong>:</strong></label>
									<div class="col-sm-6">
										<div class="">
											<input class="input" type="tel" name="kki_phone" id="kki_phone" required>
										</div>
									</div>
								</div>
								<script>
									$( document ).ready(function($) {
										$.each($('input[type=tel]'),function(){
											var telInput = $(this);
											//var countryCode = $("#countryCode").val;
											var countryCode = document.getElementById("countryCode").value 
											console.log(countryCode);
											if ($(this).val().startsWith("+") || $(this).val() == '') {
												$(telInput).intlTelInput({
													utilsScript:'https://intl-tel-input.com/node_modules/intl-tel-input/build/js/utils.js',
													autoPlaceholder: "aggressive",
													initialCountry: ""+countryCode+"",
													formatOnDisplay: true,
													preferredCountries: ["ba","hr","de","it","rs"],
													separateDialCode: true
												});
											}
										});
										//SPAJANJE COUNTRY CODA I TELEFONSKOG BROJA
										$("form").submit(function(event) {
											//event.preventDefault();
											$.each($('input[type=tel]'),function(){
												var telInput = $(this);	
												var telType = telInput.data('type');	
												telInput.val(telInput.intlTelInput("getNumber"));  
											});
										});	
									});
								</script>
								<div class="form-group">
									<label for="kandidat_drzavljanstvo_vrsta" class="col-sm-5 control-label"><strong><?php echo $txt_reg_korakjedan_drzavljanstvo ; ?></strong><span class="zvjezdica">*</span><strong>:</strong></label>
									<div class="col-sm-6">
										<div class="materail-input-block materail-input-block_success idk_radio_buttons">
											<label class="main-container__column material-radio-group material-radio-group_success" for="kandidat_drzavljanstvo_vrsta_eu">
												<input type="radio" name="kandidat_drzavljanstvo_vrsta" id="kandidat_drzavljanstvo_vrsta_eu" class="material-radiobox" value="EU državljanin" />
												<span class="material-radio-group__element material-radio-group__check-radio"></span>
												<span class="material-radio-group__element material-radio-group__caption"><?php echo $txt_reg_korakjedan_eu_drzavljanin; ?></span>
											</label>
										</div>
										<div class="materail-input-block materail-input-block_success idk_radio_buttons">
											<label class="main-container__column material-radio-group material-radio-group_success" for="kandidat_drzavljanstvo_vrsta_non">
												<input type="radio" name="kandidat_drzavljanstvo_vrsta" id="kandidat_drzavljanstvo_vrsta_non" class="material-radiobox" value="NON-EU državljanin" checked />
												<span class="material-radio-group__element material-radio-group__check-radio"></span>
												<span class="material-radio-group__element material-radio-group__caption"><?php echo $txt_reg_korakjedan_non_eu_drzavljanin; ?></span>
											</label>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label for="kandidat_jezik" class="col-sm-5 control-label"><strong><?php echo $txt_reg_korakjedan_nivo_njemackog; ?></strong><span class="zvjezdica">*</span><strong>:</strong></label>
									<div class="col-sm-6">
										<select class="select-posao" id="kandidat_jezik" name="nivo_jezika"  data-live-search="true">
											<option selected disabled>Odaberi...</option>
											<option value="Bez znanja">Bez znanja</option>
											<option value="A1">A1</option>
											<option value="A2">A2</option>
											<option value="B1">B1</option>
											<option value="B2">B2</option>
											<option value="C1">C1</option>
											<option value="C2">C2</option>
										</select>
									</div>
								</div>
								<div class="form-group" id="skola_select">
									<label for="skola_naziv" class="col-sm-5 control-label"><strong><?php echo $txt_reg_koraktri_zavrseno_obr; ?></strong><span class="zvjezdica">*</span><strong>:</strong></label>
									<div class="col-sm-6">
										<select class="selectpicker" name="skola_naziv" id="skola_naziv" required data-live-search=true>
											<option value="" selected disabled>Odaberi...</option>
											<?php 
												$query_skole = $db->prepare("
														SELECT * FROM idk_skole WHERE skola_tip_obrazovanja = 'srednje' ORDER BY skola_naziv
												");
												$query_skole->execute();
												while($row_skola = $query_skole->fetch()){
													$skola_tip = $row_skola['skola_tip_obrazovanja'];
													$skola_id = $row_skola['skola_id'];
													if($lg_language == "bs"){
													$skola_naziv = $row_skola['skola_naziv'];
												}
												else if($lg_language == "de"){
													$skola_naziv = $row_skola['skola_naziv_de'];
													if($skola_naziv == ""){
														$skola_naziv = $row_skola['skola_naziv'];
													}
												}else{
													$skola_naziv = $row_skola['skola_naziv'];
												}
													?>
													<option value="<?php echo $skola_id; ?>" data-skola_id="<?php echo $skola_id; ?>"><?php echo $skola_naziv; ?></option>
													<?php
												}
											?>
											<option value="ostalo"><?php echo $txt_reg_koraktri_ostalo; ?></option>
										</select>
									</div>
								</div>
								<div class="form-group" id="smjer_select">
									<label for="smjer_naziv" class="col-sm-5 control-label"><strong><?php echo $txt_reg_koraktri_zvanje_smjer; ?></strong><span class="zvjezdica">*</span><strong>:</strong></label>
									<div class="col-sm-6">
										<select class="selectpicker" name="smjer_naziv" id="smjer_naziv" data-live-search="true" required >
											<option value="" selected disabled><?php echo $txt_reg_koraktri_odaberi; ?></option>
											<?php 
												$query_skole_smjer = $db->prepare("
														SELECT * FROM idk_skole_smjerovi ORDER BY ss_naziv
												");
												$query_skole_smjer->execute();
												
												while($row_skola_smjer = $query_skole_smjer->fetch()){
													$smjer_skola_id = $row_skola_smjer['ss_skola_id'];
													$smjer_id = $row_skola_smjer['ss_id'];
													if($lg_language == "bs"){
														$smjer_naziv = $row_skola_smjer['ss_naziv'];
													}
													else if($lg_language == "de"){
														$smjer_naziv = $row_skola_smjer['ss_naziv_de'];
														if($smjer_naziv == ""){
															$smjer_naziv = $row_skola_smjer['ss_naziv'];
														}
													}else{
														$smjer_naziv = $row_skola_smjer['ss_naziv'];
													}
													?>
													<option value="<?php echo $smjer_id; ?>" class="hidden smjerovi_all opt_<?php echo $smjer_skola_id; ?>"><?php echo $smjer_naziv; ?></option>
													<?php
												}
											?>
										</select>
									</div>
								</div>
								<div class="form-group" id="unos_skole" style="display: none;">
									<label for="skola_naziv_ru" class="col-sm-5 control-label"><strong><?php echo $txt_naziv_skole ; ?>:</strong></label>
									<div class="col-sm-6">
										<input class="input" type="text" name="skola_naziv_ru" id="skola_naziv_ru" placeholder="<?php echo $txt_naziv_skole ; ?>" >
									</div>
								</div>
								<div class="form-group" id="unos_smjera" style="display: none;">
									<label for="smjer_naziv_ru" class="col-sm-5 control-label"><strong><?php echo $txt_naziv_smjera ; ?>:</strong></label>
									<div class="col-sm-6">
										<input class="input" type="text" name="smjer_naziv_ru" id="smjer_naziv_ru" placeholder="<?php echo $txt_naziv_smjera ; ?>" >
									</div>
								</div>
								<div class="form-group" id="viza_viza">
									<label for="kandidat_viza" class="col-sm-5 control-label"><strong>Da li imate vizu u Njemačkoj?</strong><span class="zvjezdica">*</span></label>
									<div class="col-sm-3">
										<label class="main-container__column material-radio-group material-radio-group_success" for="viza_da">
											<input type="radio" name="kandidat_viza" id="viza_da" class="material-radiobox" value="1"/>
											<span class="material-radio-group__element material-radio-group__check-radio"></span>
											<span class="material-radio-group__element material-radio-group__caption">Da</span>
										</label>
									</div>
									<div class="col-sm-3">
										<label class="main-container__column material-radio-group material-radio-group_success" for="viza_ne">
											<input type="radio" name="kandidat_viza" id="viza_ne" class="material-radiobox" value="0" />
											<span class="material-radio-group__element material-radio-group__check-radio"></span>
											<span class="material-radio-group__element material-radio-group__caption">Ne</span>
										</label>
									</div>
								</div>
								<div class="form-group" id="viza_datum_vrijedi_do">
									<label for="kandidat_viza_vrijedi_do" class="col-sm-5 control-label"><strong><?php echo $txt_reg_korakjedan_viza_vrijedi_do ; ?>?</strong></label>
									<div class="col-sm-6">
										<input class="input" type="text" name="kandidat_viza_vrijedi_do" id="kandidat_viza_vrijedi_do" placeholder="<?php echo $txt_reg_korakjedan_viza_datum_isteka; ?>">
									</div>
									<script>
									<?php $year_sixteen = date('Y') - 16; ?>
									$( function() {
										$( "#kandidat_viza_vrijedi_do" ).datepicker({
										changeMonth: true,
										changeYear: true,
											dateFormat: 'dd.mm.yy' ,
											yearRange: '2020:2050'
										});
									} );
									</script>
								</div>
								<!-- TERMIN -->
								<div class="form-group" id="viza_termin">
									<label for="kandidat_email" class="col-sm-5 control-label"><strong><?php echo $txt_reg_korakjedan_termin_pitanje ; ?></strong></label>
									<div class="col-sm-3">
										<label class="main-container__column material-radio-group material-radio-group_success" for="termin_daa">
											<input type="radio" name="kandidat_termin" id="termin_daa" class="material-radiobox" value="1"/>
											<span class="material-radio-group__element material-radio-group__check-radio"></span>
											<span class="material-radio-group__element material-radio-group__caption"><?php echo $txt_reg_da ; ?></span>
										</label>
									</div>
									<div class="col-sm-3">
										<label class="main-container__column material-radio-group material-radio-group_success" for="termin_nee">
											<input type="radio" name="kandidat_termin" id="termin_nee" class="material-radiobox" value="0"/>
											<span class="material-radio-group__element material-radio-group__check-radio"></span>
											<span class="material-radio-group__element material-radio-group__caption"><?php echo $txt_reg_ne ; ?></span>
										</label>
									</div>
								</div>
								<div class="form-group" id="viza_datum_termina">
									<label for="kandidat_termin_date" class="col-sm-5 control-label"><strong><?php echo $txt_reg_korakjedan_datum_termina; ?></strong></label>
									<div class="col-sm-6">
										<input class="input" type="text" name="kandidat_termin_date" id="kandidat_termin_date" placeholder="<?php echo $txt_reg_korakjedan_datum_termina; ?>">
									</div>
									<script>
									<?php $year_sixteen = date('Y') - 16; ?>
									$( function() {
										$( "#kandidat_termin_date" ).datepicker({
										changeMonth: true,
										changeYear: true,
											dateFormat: 'dd.mm.yy' ,
											yearRange: '2018:2025'
										});
									} );
									</script>
								</div>
								<!-- APLICIRANJE -->
								<div class="form-group" id="viza_apliciranje" style="margin-top: 10px !important;">
									<label for="kandidat_apliciranje" class="col-sm-5 control-label"><strong><?php echo $txt_reg_korakjedan_aplikacija_za_vizu ; ?>?</strong></label>
									<div class="col-sm-3">
										<label class="main-container__column material-radio-group material-radio-group_success" for="apliciranje_da">
											<input type="radio" name="kandidat_apliciranje" id="apliciranje_da" class="material-radiobox" value="1"/>
											<span class="material-radio-group__element material-radio-group__check-radio"></span>
											<span class="material-radio-group__element material-radio-group__caption"><?php echo $txt_reg_da ; ?></span>
										</label>
									</div>
									<div class="col-sm-3">
										<label class="main-container__column material-radio-group material-radio-group_success" for="apliciranje_ne">
											<input type="radio" name="kandidat_apliciranje" id="apliciranje_ne" class="material-radiobox" value="0"/>
											<span class="material-radio-group__element material-radio-group__check-radio"></span>
											<span class="material-radio-group__element material-radio-group__caption"><?php echo $txt_reg_ne ; ?></span>
										</label>
									</div>
								</div>
								
								<div class="form-group" id="viza_datum_apliciranja">
									<label for="kandidat_termin_date_app" class="col-sm-5 control-label"><strong><?php echo $txt_reg_korakjedan_kad_aplikacija ; ?></strong></label>
									<div class="col-sm-6">
										<input class="input" type="text" name="kandidat_termin_date_app" id="kandidat_termin_date_app" placeholder="<?php echo $txt_reg_korakjedan_datum_aplikacije; ?>">
									</div>
									<script>
									<?php $year_sixteen = date('Y') - 16; ?>
									$( function() {
										$( "#kandidat_termin_date_app" ).datepicker({
										changeMonth: true,
										changeYear: true,
											dateFormat: 'dd.mm.yy' ,
											yearRange: '2017:2019'
										});
									} );
									</script>
								</div>
								<script>
									
									$(document).ready(function() {
										jezik = $(document).find('input[name="lg_language"]').val();
										console.log(jezik)
										if(jezik == 'de' || jezik == 'it')
											$('#viza_viza').hide();
										
										$('#viza_datum_vrijedi_do').hide();
										$('#viza_termin').hide();
										$('#viza_datum_termina').hide();
										$('#viza_apliciranje').hide();
										$('#viza_datum_apliciranja').hide();
										
									});
									$('#kandidat_drzavljanstvo_vrsta_eu').click(function() {
										if($('#kandidat_drzavljanstvo_vrsta_eu').is(':checked')) { 
											$('#viza_viza').hide();
											$('#viza_datum_vrijedi_do').hide();
											$('#viza_termin').hide();
											$('#viza_datum_termina').hide();
											$('#viza_apliciranje').hide();
											$('#viza_datum_apliciranja').hide();
											$('#boravak_eu').hide();
											$('#eu_drzava_step1').hide();
											$('#skole_step1').hide();
											$('#smjer_step1').hide();
											$("#viza_da").prop("checked", false);
											$("#viza_ne").prop("checked", false);
											$("#termin_daa").prop("checked", false);
											$("#termin_nee").prop("checked", false);
											$("#apliciranje_da").prop("checked", false);
											$("#apliciranje_ne").prop("checked", false);
											$("#kandidat_boravak_eu_da").prop("checked", false);
											$("#kandidat_boravak_eu_ne").prop("checked", false);
											$('#skola_select').hide();
											$('#smjer_select').hide();
											$('#unos_skole').hide();
											$('#unos_smjera').hide();
											$("#skola_naziv").prop('required',false);
											$("#smjer_naziv").prop('required',false);
										}
									});
									
									$('#kandidat_drzavljanstvo_vrsta_non').click(function() {
										if($('#kandidat_drzavljanstvo_vrsta_non').is(':checked')) { 
											$('#viza_viza').show();
											$('#boravak_eu').show();
											$('#eu_drzava').selectpicker('val', '');
											$('#skole_step1').show();
											$('#smjer_step1').show();
											$('#skola_select').show();
											$('#smjer_select').show();
											$("#skola_naziv").prop('required',true);
											$("#smjer_naziv").prop('required', true);
										}
									});
									
									$('#kandidat_boravak_eu_da').click(function() {
										if($('#kandidat_boravak_eu_da').is(':checked')) { 
											$('#eu_drzava_step1').show();
											$('#skole_step1').hide();
											$('#smjer_step1').hide();
											$('#skola_select').hide();
											$('#smjer_select').hide();
											$('#unos_skole').hide();
											$('#unos_smjera').hide();
											$("#skola_naziv").prop('required',false);
											$("#smjer_naziv").prop('required',false);
										}
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
										}else{
											$('#smjer_step1').show();
											$('#smjer_select').show();
											$('#unos_skole').hide();
											$('#unos_smjera').hide();
											$("#smjer_naziv").prop('required',true);
										}
										
									});

									$('#viza_da').click(function() {
										if($('#viza_da').is(':checked')) { 
											$('#viza_datum_vrijedi_do').show();
											$('#viza_termin').hide();
											$('#viza_datum_termina').hide();
											$('#viza_apliciranje').hide();
											$('#viza_datum_apliciranja').hide();
											$("#termin_daa").prop("checked", false);
											$("#termin_nee").prop("checked", false);
											$("#apliciranje_da").prop("checked", false);
											$("#apliciranje_ne").prop("checked", false);
										}
									});
									$('#viza_ne').click(function() {
										if($('#viza_ne').is(':checked')) { 
											$('#viza_datum_vrijedi_do').hide();
											$('#viza_termin').show();
										}
									});
									$('#termin_daa').click(function() {
										if($('#termin_daa').is(':checked')) { 
											$('#viza_datum_termina').show();
											$('#viza_apliciranje').hide();
											$('#viza_datum_apliciranja').hide();
											$("#apliciranje_da").prop("checked", false);
											$("#apliciranje_ne").prop("checked", false);
										}
									});
									$('#termin_nee').click(function() {
										if($('#termin_nee').is(':checked')) { 
											$('#viza_datum_termina').hide();
											$('#viza_apliciranje').show();
										}
									});
									
									$('#apliciranje_da').click(function() {
										if($('#apliciranje_da').is(':checked')) { 
											$('#viza_datum_apliciranja').show();
										}
									});
									$('#apliciranje_ne').click(function() {
										if($('#apliciranje_ne').is(':checked')) { 
											$('#viza_datum_apliciranja').hide();
										}
									});
								</script>
								<br />
								<div class="form-group">
									<div class="col-sm-offset-2 col-sm-10 text-right">
										<button id="button_nastavi" type="submit" class="nastavi"><?php echo $txt_reg_nastavi ; ?> <span><i class="fa fa-arrow-right" aria-hidden="true"></i></span></button>
										<br /><small><?php echo $txt_reg_korakjedan_obavezna_polja1 ; ?><span class="zvjezdica">*</span><?php echo $txt_reg_korakjedan_obavezna_polja2 ; ?></small>
									</div>
								</div>
								
								<div class="form-group">
									<label for="asd" class="col-sm-3 control-label"><span class="text-danger"></span></label>
									
									<div class="col-sm-6" style="padding-top: 10px;">
										
										<p class = "text-center"> <?php echo $txt_reg_korakjedan_prihvatanje_privatnosti ; ?>
										<br><a href="" type="button" data-toggle="modal" data-target="#privacyModal"><?php echo $txt_reg_korakjedan_pogledaj_izjavu ; ?></a>
										</p>
									</div>
								</div>
								<!-- Modal privacy -->
								<div class="modal fade modal-fullscreen" id="privacyModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
								  <div class="modal-dialog" role="document">
									<div class="modal-content">
									  <div class="modal-header">
										<h5 class="modal-title" id="exampleModalLabel"><?php echo $txt_reg_korakjedan_izjava_h5 ; ?></h5>
									  </div>
									  <div class="modal-body">
										<p><?php echo $txt_reg_korakjedan_izjava_tekst ; ?></p>
									  </div>
									  <div class="modal-footer">
										<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $txt_reg_zatvori; ?></button>
									  </div>
									</div>
								  </div>
								</div>
							</form>
						</div>
					</div>
					
				<?php
				break;
				
            }
		?>
			<footer>
				<?php if($_REQUEST['page'] != "campaignStatistics") // QUICKFIX ZBOG STATISTIKE DIPL KAMPANJA, VANJSKIM SARADNICIMA --- ISMAIL 
				getCopyright(); ?>
			</footer>
		</div>
	</div>
</body>
</html>
<?php 
}
?>
