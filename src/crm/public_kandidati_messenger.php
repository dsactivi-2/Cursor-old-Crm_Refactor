<?php

//Error log enabled
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");
include("includes/functions.php");
require 'mail/PHPMailerAutoload.php';
include("lang/bs.php");

if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
	$ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
	$ip = $_SERVER['REMOTE_ADDR'];
}
$ipdat = @json_decode(file_get_contents( 
	"http://www.geoplugin.net/json.gp?ip=" . $ip)); 
   
$countryCode = strtolower($ipdat->geoplugin_countryCode);

if($countryCode == "de")
	include("lang/de.php");
else
	include("lang/bs.php");

if(isset($_REQUEST["page"])) {
	$page = $_REQUEST["page"];
}else{
	
}

if(isset($_GET['id'])) {
	$kandidat_id = $_GET['id'];
}else{
}

	/*$query = $db->prepare("
			SELECT *
			FROM idk_kandidati
			WHERE kandidat_id = :kandidat_id");

	$query->execute(array(
			':kandidat_id' => $kandidat_id));

	$row = $query->fetch();
	$rowcount = $query->rowCount();

	$kandidat_ime = $row['kandidat_ime'];
	$kandidat_prezime = $row['kandidat_prezime'];*/
	 
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php echo $txt_prijava ; ?> | Jobstep</title>
	<?php include('includes/head.php'); ?>
	<link rel="icon" href="https://crm.job-step.com/images/Jobstep-logo_news.png">
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
			padding: 30px;
			background-color: #6097a0;
			border-radius: 3.25rem 3.25rem 0.25rem 0.25rem;
			color: white;
		}
		#dizajn_vanjski_dio > .row:nth-child(2){
			padding: 30px;
			padding-left: 15px;
			padding-right: 15px;
		}
		
		.material-radio-group_success .material-radio-group__check-radio {
			border-color: #6097a0;
		}
		#content_public{
			height: 100vh;
		}
		body{
			height: 100vh;
		}		
		label {
			display: flex;
		}
		.materail-input-block {
			margin-bottom: 40px;
			padding-left: 2px;
		}
	</style>
	<?php
		switch ($page){
			case "info1":
			
				$db_update_query = $db->prepare("
								UPDATE idk_kandidati
								SET kandidat_pogledao_link_prijave = 1 WHERE kandidat_id = $kandidat_id
								");

				$db_update_query->execute();
	?>
	<div id="content_public">
		<div class="container-fluid">
			<div class="row">
				<div class="col-xs-12 text-center">
                    <img class="img-responsive" src="<?php getSiteURL(); ?>images/Jobstep_logo_new.png" alt="" style="max-width:200px; margin-top: 20px; margin-bottom: 20px;">
                </div>
			</div>
			<div class = "row">
				<div class="col-xs-12">
					<div class="" style = "min-height: 200px;">
						<div class = "row">
							<div class="col-xs-12">
								<div id = "dizajn_vanjski_dio">
									<div class="row">
										
									</div>
									<div class="row">
										<div class="form-group">
										<form action="<?php getSiteURL(); ?>public_kandidati_messenger?page=update_info" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
											<input type="hidden" name="kandidat_id" id="kandidat_id" value="<?php echo $kandidat_id; ?>"/>
											<input type="hidden" name="nalog_id" id="nalog_2" required class="material-radiobox" value="105"/>
											<h4>Brzo i lako do novog posla u Njemačkoj</h4>
											
											<div class="col-xs-12">
											<div class="col-xs-6">
												<h3>Servisni tehničar / Internet montažer</h3>
											</div>
											<div class="col-xs-6" style="padding-top: 40px;">
											<button id="button_nastavi1" type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column text-center"><i class="fa fa-arrow-right" aria-hidden="true"></i> <span><?php echo "Prijavi se" ; ?></span></button>
											</div>
											</div>
										
											<div class="col-xs-12">
												<hr>
												<p>
												Za vodeću međunarodnu kompaniju u Njemačkoj sa preko 5000 uposlenih tražimo više radnika na poziciji <b>SERVISNI TEHNIČAR</b>
												</p>
												<p><b>Potrebne kvalifikacije:</b></p>
												<ul>
												<li>III stepen stručne spreme (poželjno tehničkog smjera)</li>
												<li>poznavanje njemačkog jezika na A2 nivou</li>
												<li>vozačka dozvola za B kategoriju</li>
												</ul>
												<p><b>Opis posla:</b></p>
												<ul>
												<li>spajanje korisnika na telekomunikacijske sisteme, kablovska TV, internet priključak</li>
												<li>provjera opreme i puštanje u rad iste</li>
												<li>izvođenje sklopnih radova u mreži</li>
												<li>izrada koaksijalnih mreža</li>
												<li>postavljanje ormarića</li>
												</ul>
												<p><b>Nudimo:</b></p>
												<ul>
												<li>Bruto platu od min 2.200 EUR (početna plata) plus bonusi</li>
												<li>Osiguran smještaj</li>
												<li> Plaćena obuka u trajanju od 6 sedmica za svakog novog uposlenika</li>
												<li>Savjetovanje i pomoć pri procesu odlaska</li>
												<li>Mogućnost stručnog usavršavanja</li>
												<li>Pomoć pri administrativnim postupcima</li>
												</ul>
												<p><b>Razgovor sa poslodavcem će se održati u Sarajevu i Banja Luci od 16.03. do 24.03.2020.godine.</b></p>
												<hr>
												<p><b>KONTAKT: +387 33 821 305 ili +387 61 038 441</p></b>
												<hr>
												
											</div>
											<div class="col-xs-12 text-right">
												<button id="button_nastavi" type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-arrow-right" aria-hidden="true"></i> <span><?php echo "Prijavi se" ; ?></span></button>
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
			<div class="row">
				<footer  id="footer"><?php getCopyright(); ?></footer>
			</div>
		</div>
	</div>
	<?php
		break;
		case "update_info":
			$kandidat_id = $_POST['kandidat_id'];
			$nalog_id = $_POST['nalog_id'];
			
			$query = $db->prepare("
							SELECT kandidat_id, kandidat_ime, kandidat_prezime, kandidat_email, kandidat_zaposlen_kod, kandidat_mobitel, kandidat_status_prijave
							FROM idk_kandidati
							WHERE kandidat_id = $kandidat_id
							");  

			$query->execute();
			$row = $query->fetch();

			$kandidat_id = $row['kandidat_id'];
			$kandidat_ime = $row['kandidat_ime'];
			$kandidat_prezime = $row['kandidat_prezime'];
			$kandidat_full_name = $kandidat_ime." ".$kandidat_prezime;
			$kandidat_email = $row['kandidat_email'];
			$kandidat_mobitel = $row['kandidat_mobitel'];
			$kandidat_zaposlen_kod = $row['kandidat_zaposlen_kod'];  
			$kandidat_status_prijave = $row['kandidat_status_prijave'];
			$kandidat_prijava_na = "ndf";
			$random_string = generateRandomString();
			$options = [
				'cost' => 10,
			];
			$random_password = password_hash($random_string, PASSWORD_BCRYPT, $options);
			
			$query_users = $db->prepare("
							SELECT kandidat_id, ponovno_slanje
							FROM users
							WHERE kandidat_id = $kandidat_id
							");  

			$query_users->execute();
			$check_user = $query_users->rowCount();
			$row_qu = $query_users->fetch();
			$ponovno_slanje = $row_qu["ponovno_slanje"];
			if($ponovno_slanje != 1){
				if($check_user == 0){
					
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
								':phone' => $kandidat_mobitel,
								':name' => $kandidat_full_name,
								':nalog_id' => $nalog_id,
								':email' => $bot_koriscnicko_ime,
								':password' => $random_password,
								':kandidat_id' => $kandidat_id));
					
				}else{
					
					$query_user_update = $db->prepare("
								UPDATE users
								SET password = :password, ponovno_slanje = :ponovno_slanje
								WHERE kandidat_id = :kandidat_id");

					$query_user_update->execute(array(
								':password' => $random_password,
								':ponovno_slanje' => 1,
								':kandidat_id' => $kandidat_id));
				}
				$log_desc = "Kandidat: " . $kandidat_ime . " " . $kandidat_prezime . "(".$kandidat_id.").(".$random_string.")";
				$log_type = "6";
				addToLogs($log_desc, $log_type);
				
				 
				//za infobip formatira se bez nula i bez++
				$phone_f = str_replace("+", '00', $kandidat_mobitel);
				
				//INFOBIP
				sendSmsToCandidateInfobip1($random_string, $kandidat_mobitel, $kandidat_prijava_na);
				sleep(1);  // Seconds
				sendSmsToCandidateInfobip2($random_string, $kandidat_mobitel, $kandidat_prijava_na);
				sleep(1);  // Seconds
				sendSmsToCandidateInfobip3($random_string, $kandidat_mobitel, $bot_koriscnicko_ime);
				sleep(1);  // Seconds
				sendSmsToCandidateInfobip4($random_string, $kandidat_mobitel, $bot_koriscnicko_ime);
			}
			header("Location: prijava/completed");
		break;
		
		case "completed":
		?>
		<style>
			.col-xs-12 > .img-responsive{
				max-width: 90px !important;
				margin-top: 10px !important;
			}
			.alert > h3{
				margin-top: 0px !important;
				font-size: 19px !important;
			}
			.alert > h5{
				font-size: 15px !important;
				border-left: none !important; 
				margin-bottom: 0px !important;
			}
			.col-md-4 > iframe{
				margin-bottom: 10px;
			}
			
		</style>
		<div id="content_public">
			<div class="container">
				<div class="row">
					<div class="col-xs-12 text-center">
						<img class="img-responsive" src="<?php getSiteURL(); ?>images/Jobstep_logo_new.png" alt="" style="max-width:200px;">
					</div>
					<div class="col-xs-12">
						<br />
					</div>
				</div>
				<div class="row">
					<div class="col-md-6 col-md-offset-3 idk_margin_top10">
						<div style = "background-color: #ffffff; color: #6097a0; text-align: center;" class="alert material-alert material-alert_success">
							<h3><?php echo $txt_reg_zahvala; ?>
							</h3>
							<h5><?php echo "Pogledajte video ispod, preuzmite našu aplikaciju, finalizirajte prijavu i pronađite pravi posao za Vas."; ?>
							</h5>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4 col-md-offset-4">
						<iframe width="100%" height="200" src="https://www.youtube.com/embed/rttgZz8ohEI" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6 col-md-offset-3 text-center">
						<div class="col-xs-8 col-xs-offset-2">
							<a href = "https://play.google.com/store/apps/details?id=net.job_step.jobstepmessenger">
								<img style = "width: 100%; " src = "<?php getSiteURL(); ?>files/kandidati/google-play-store-icon-android.png">
							</a>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6 col-md-offset-3 idk_margin_top10 text-center">
						<div class="col-xs-8 col-xs-offset-2">
							<a href = "#">
								<img  style = "width: 100%; " src = "<?php getSiteURL(); ?>files/kandidati/google-play-store-icon-ios.png">
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
		break;
		case "download_app":  
		/*
			//Detect special conditions devices
			$iPod    = stripos($_SERVER['HTTP_USER_AGENT'],"iPod");
			$iPhone  = stripos($_SERVER['HTTP_USER_AGENT'],"iPhone");
			$iPad    = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");
			$Android = stripos($_SERVER['HTTP_USER_AGENT'],"Android");
			$webOS   = stripos($_SERVER['HTTP_USER_AGENT'],"webOS");
			
			//do something with this information
			if( $iPod || $iPhone ){
				$type = "ios";
			}else if($iPad){
				$type = "ios";
			}else if($Android){
				$type = "android";
			}else{
				$type = "none";
			}
		*/
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		//echo $ip;
		
		$ipdat = @json_decode(file_get_contents( 
			"http://www.geoplugin.net/json.gp?ip=" . $ip)); 
		   
		$countryCode = strtolower($ipdat->geoplugin_countryCode);
		
		if($countryCode == "de")
			include("lang/de.php");
		else
			include("lang/bs.php");
		?>
			<div class="container-fluid">
				<div class="row">
					<div class="col-xs-12 text-center">
						<img class="img-responsive" src="<?php getSiteURL(); ?>images/Jobstep_logo_new.png" alt="" style="max-width:200px; margin-top: 20px; margin-bottom: 20px;">
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12"> 
					</div>
				</div>
				<div class="row">
					<div class="col-md-offset-2 col-sm-8 idk_margin_top10 text-center">
						<a href = "https://play.google.com/store/apps/details?id=net.job_step.jobstepmessenger">
							<div  class="col-xs-5 text-center alert material-alert material-alert_success " style="background-color: #ffffff; color: #6097a0; border-radius: 1.25rem; padding:20px 5px 20px 5px">
								<h6>Android</h6>
								<img style = "width: 100%; " src = "<?php getSiteURL(); ?>files/kandidati/google-play-store-icon-android.png">
							</div>
						</a>
						<div class="col-xs-2">
						</div>
						<a href = "https://apps.apple.com/hr/app/jobstep-messenger/id1486805317"> 
							<div class="col-xs-5 text-center alert material-alert material-alert_success " style="background-color: #ffffff; color: #6097a0; border-radius: 1.25rem; padding:20px 5px 20px 5px">
								<h6>iOS</h6>
								<img style = "width: 100%; " src = "<?php getSiteURL(); ?>files/kandidati/google-play-store-icon-ios.png">
							</div>
						</a>
					</div>
				</div>
				<div class="row">
					<div class="col-md-offset-2 col-sm-8 idk_margin_top10">
						<div style = "background-color: #ffffff; color: #6097a0; text-align: center; border-radius: 1.25rem" class="alert material-alert material-alert_success">
							
							<h5 style="border-left: none;" ><?php echo $txt_preuzmi_app ; ?>
							</h5> 
						</div>
					</div>
				</div>
				<div class="row">
					<footer  id="footer"><?php getCopyright(); ?></footer>
				</div>
			</div>
		<?php
		break;
		case "sms_tutorijal":
		?>
			<div class="container-fluid">
				<div class="row">
					<div class="col-xs-12 text-center">
						<img class="img-responsive" src="<?php getSiteURL(); ?>images/Jobstep_logo_new.png" alt="" style="max-width:100px; margin-top: 20px; margin-bottom: 20px;">
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12"> 
					</div>
				</div> 
				<div class="row">
					<div class="col-md-offset-2 col-sm-8 idk_margin_top10 text-center">
						<iframe id="frame1" width="100%" height="200"  src="https://www.youtube.com/embed/rttgZz8ohEI" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
					</div>
				</div>
				<div class="row">
					<footer  id="footer"><?php getCopyright(); ?></footer>
				</div>
			</div>
		<?php
		break;
		
		case "dipl_info":
			
			$check_query = $db->prepare("
							SELECT povezan_na_dipl FROM idk_kandidati WHERE kandidat_id = $kandidat_id
							");
			$check_query->execute();
			
			$check_row = $check_query->fetch();
			$povezan_na_dipl = $check_row['povezan_na_dipl'];
			// var_dump($povezan_na_dipl);
			// exit();
			if($povezan_na_dipl == 1){
				header("Location: ".getSiteURLR()."dipl/completed/$kandidat_id");
			}else{
				$db_update_query = $db->prepare("
								UPDATE idk_kandidati
								SET kandidat_poslan_dipl = 61 WHERE kandidat_id = $kandidat_id
								");

				$db_update_query->execute();
				?>
				<style>
					@font-face { font-family: Bank-Gothic-Medium; src: url('/css/Bank-Gothic-Medium.ttf'); }
					.naslov_jobstep{
						font-family: Bank-Gothic-Medium;
						font-size: 4.25rem;
						padding: 5px 0px 0px 0px !important;
					}
					.select_izgled{
						max-width: 100%;
						border: 3px solid;
						border-color: #6097a0;
						background-color: white;
						border-radius: 1.25rem;
						margin-bottom: 10px;
						width: inherit;
						padding: 2px;
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
					.unosi{
						max-width: 100%;
						border: 3px solid;
						border-color: #6097a0;
						background-color: white;
						border-radius: 1.25rem;
						margin-bottom: 10px;
						width: inherit;
						padding: 2px;
					}
				</style>
				<div id="content_public">
					<div class="container-fluid">
						<div class="row">
							<div class="col-xs-12 text-center">
							   <!-- <img class="img-responsive" src="<?php getSiteURL(); ?>images/Jobstep_logo_new.png" alt="" style="max-width:200px; margin-top: 20px; margin-bottom: 20px;"> -->
							</div>
						</div>
						<div class = "row">
							<div class="col-xs-12">
								<div class="" style = "min-height: 200px; margin-top: 20px;">
									<div class = "row">
										<div class="col-xs-12">
											<div id = "dizajn_vanjski_dio">
												<div class="row text-center naslov_jobstep">JOBSTEP</div>
												<div class="row">
													<div class="form-group">
													<form action="<?php getSiteURL(); ?>public_kandidati_messenger?page=update_dipl" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
														<input type="hidden" name="kandidat_id" id="kandidat_id" value="<?php echo $kandidat_id; ?>"/>
														<div class="col-xs-12">
															<p>
															<b>Važna obavijest:</b> Došlo je do promjene u novom zakonu! <br/><br/>Od sada možete u Njemačku i bez poznavanje njemačkog jezika. Kako? <br/>Ukoliko nostrificirate Vašu diplomu ili već imate nostrifikaciju, termin za vizu dobijate za 10 dana! <br/><br/>Ukoliko Vas zanima više detalja, odgovorite nam na slijedeće pitanje i naš tim će Vas kontaktirati u kratkom vremenskom roku. 
															</p>
															
															<hr>
															<div class="dipl_pitanje">
																<p>
																Da li ste Vi nostrificirali Vašu diplomu u Njemačkoj?
																</p>
																<div class="row" style="text-align: center;">
																	<div class="col-xs-6" style="padding: 0;"><button type="submit" name="dipl_da" class="tipka_da" id="dipl_da" value="da" >DA</button></div>
																	<div class="col-xs-6" style="padding: 0;"><button type="submit" name="dipl_ne" class="tipka_ne" id="dipl_ne" value="ne" >NE</button></div>
																</div>
																<hr>
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
						<div class="row">
							<footer  id="footer"><?php getCopyright(); ?></footer>
						</div>
					</div>
				</div>
				<?php
			}
		break;
		
		case "update_dipl":
			
			$kandidat_id = $_POST['kandidat_id'];
			/*$srednja = $_POST['srednja'];
			if($srednja == "ostalo"){
				$srednja = $_POST['unos_skola'];
				$smjerovi = $_POST['unos_smjer'];
			}else{
				$smjerovi = $_POST['smjerovi'];
			}*/
			if(!empty($_POST["dipl_da"])){
				$dipl_da = $_POST["dipl_da"];
				//var_dump($dipl_da);
				$ima_dipl = 1;
			}else{
				$ima_dipl = 0;
			}
			//var_dump($ima_dipl);
			
			//INSERT SKOLE
			/*		
			$insert_skole = $db->prepare("
							INSERT INTO idk_kandidat_edukacija
								(ke_naziv_kvalifikacije, ke_naziv, ke_vrsta_obrazovanja, ke_kandidat_id)
							VALUES
								(:ke_naziv_kvalifikacije, :ke_naziv, :ke_vrsta_obrazovanja, :ke_kandidat_id)");
	
			$insert_skole->execute(array(
						':ke_naziv_kvalifikacije' => $smjerovi,
						':ke_naziv' => $srednja,
						':ke_vrsta_obrazovanja' => "srednje",
						':ke_kandidat_id' => $kandidat_id));
			*/			
			$check_query = $db->prepare("
							SELECT povezan_na_dipl FROM idk_kandidati WHERE kandidat_id = $kandidat_id
							");
			$check_query->execute();
			
			$check_row = $check_query->fetch();
			$povezan_na_dipl = $check_row['povezan_na_dipl'];
		
			if($povezan_na_dipl == 0){
				if($ima_dipl == 0){
					$povijest = 1;
					$sms_bot_crm = 4;
					//var_dump($ima_dipl);
					
					//UPDATE povezan_na_dipl u tabeli idk_kandidati
					$update_query = $db->prepare("
								UPDATE idk_kandidati
								SET povezan_na_dipl = :povezan_na_dipl, kandidat_poslan_dipl = :kandidat_poslan_dipl
								WHERE kandidat_id = :kandidat_id
					");
					
					$update_query->execute(array(
								'kandidat_id' => $kandidat_id,
								'kandidat_poslan_dipl' => 63,
								'povezan_na_dipl' => 1
								));
					
					CopyKandidatinDipl($kandidat_id, $povijest, $sms_bot_crm);
				
				}else {
					$db_upd_22 = $db->prepare("
									UPDATE idk_kandidati
									SET kandidat_poslan_dipl = 62 WHERE kandidat_id = $kandidat_id
									");

					$db_upd_22->execute();
					$link = "https://crm.job-step.com/kandidati?page=open&id=".$kandidat_id;
					$mail = new PHPMailer;
					$mail->isSMTP();

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
				}
			}
			
			header("Location: dipl/completed/$kandidat_id");
		break;
		
		case "dipl_completed":
		$kandidat_id = $_GET['id'];
		?>
		<style>
			.col-xs-12 > .img-responsive{
				max-width: 90px !important;
				margin-top: 10px !important;
			}
			.alert > h3{
				margin-top: 0px !important;
				font-size: 19px !important;
			}
			.alert > h5{
				font-size: 15px !important;
				border-left: none !important; 
				margin-bottom: 0px !important;
			}
			.col-md-4 > iframe{
				margin-bottom: 10px;
			}
			
		</style>
		<div id="content_public">
			<div class="container">
				<div class="row">
					<div class="col-xs-12 text-center">
						<img class="img-responsive" src="<?php getSiteURL(); ?>images/Jobstep_logo_new.png" alt="" style="max-width:200px;">
					</div>
					<div class="col-xs-12">
						<br />
					</div>
				</div>
				<div class="row">
					<div class="col-md-6 col-md-offset-3 idk_margin_top10">
						<div style = "background-color: #ffffff; color: #6097a0; text-align: center;" class="alert material-alert material-alert_success">
							<h3><?php echo "Hvala na izdvojenom vremenu, naš tim će Vas kontaktirati za više detalja."; ?>
							</h3>
							<hr>
							<h5><?php echo "Preuzmite našu besplatnu aplikaciju i pratite sve naše aktuelnosti i poslovne ponude."; ?>
							</h5>
						</div>
					</div>
				</div>
				<!--<div class="row">
					<div class="col-md-4 col-md-offset-4">
						<iframe width="100%" height="200" src="https://www.youtube.com/embed/rttgZz8ohEI" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
					</div>
				</div>-->
				<div class="row">
					<div class="col-md-6 col-md-offset-3 text-center">
						<div class="col-xs-8 col-xs-offset-2">
							<a class="opt_mess" href = "https://play.google.com/store/apps/details?id=net.job_step.jobstepmessenger"><!-- https://play.google.com/store/apps/details?id=net.job_step.jobstepmessenger -->
								<img style = "width: 100%; " src = "<?php getSiteURL(); ?>files/kandidati/google-play-store-icon-android.png">
							</a>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6 col-md-offset-3 idk_margin_top10 text-center">
						<div class="col-xs-8 col-xs-offset-2">
							<a class="opt_mess" href = "https://apps.apple.com/hr/app/jobstep-messenger/id1486805317">
								<img  style = "width: 100%; " src = "<?php getSiteURL(); ?>files/kandidati/google-play-store-icon-ios.png">
							</a>
						</div>
					</div>
				</div>
				<input type="hidden" name="kan_id" id="kan_id" value="<?php echo $kandidat_id; ?>">
				<script>
					$(document).on('click', '.opt_mess', function () {
						var kandidat_id = $(this).parent().parent().parent().parent().find('input[name="kan_id"]').val();
						//alert(kandidat_id);
						$.ajax({
							url: '/public_kandidati_messenger.php?page=sms_za_messenger',
							type: 'POST',
							data: {'kandidat_id': kandidat_id},
							dataType: 'html',
							success: function(data) {
								
							}
						});
					});
				</script>
			</div>
		</div>
		<?php
		break;
		
		case "dipl_info_tip2":
			
			$check_query = $db->prepare("
							SELECT povezan_na_dipl, kandidat_poslan_dipl FROM idk_kandidati WHERE kandidat_id = $kandidat_id
							");
			$check_query->execute();
			
			$check_row = $check_query->fetch();
			$povezan_na_dipl = $check_row['povezan_na_dipl'];
			$kandidat_poslan_dipl = $check_row['kandidat_poslan_dipl'];
			// var_dump($povezan_na_dipl);
			// exit();
			//if($povezan_na_dipl == 1 or $kandidat_poslan_dipl == 22 or $kandidat_poslan_dipl == 23){
			if($povezan_na_dipl == 1 or $kandidat_poslan_dipl == 22 or $kandidat_poslan_dipl == 23){
				header("Location: ".getSiteURLR()."dipl/complete/$kandidat_id");
			}else{
				$db_update_query = $db->prepare("
								UPDATE idk_kandidati
								SET kandidat_poslan_dipl = 21 WHERE kandidat_id = $kandidat_id
								");

				$db_update_query->execute();
				?>
				<style>
					.select_izgled{
						max-width: 100%;
						border: 3px solid;
						border-color: #6097a0;
						background-color: white;
						border-radius: 1.25rem;
						margin-bottom: 10px;
						width: 100%;
						padding: 2px;
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
					.unosi{
						max-width: 100%;
						border: 3px solid;
						border-color: #6097a0;
						background-color: white;
						border-radius: 1.25rem;
						margin-bottom: 10px;
						width: 100%;
						padding: 2px;
					}
				</style>
				<div id="content_public">
					<div class="container-fluid">
						<div class="row">
							<div class="col-xs-12 text-center">
							   <!-- <img class="img-responsive" src="<?php getSiteURL(); ?>images/Jobstep_logo_new.png" alt="" style="max-width:200px; margin-top: 20px; margin-bottom: 20px;"> -->
							</div>
						</div>
						<div class = "row">
							<div class="col-xs-12">
								<div class="" style = "min-height: 200px; margin-top: 20px;">
									<div class = "row">
										<div class="col-xs-12">
											<div id = "dizajn_vanjski_dio">
												<div class="row">
													
												</div>
												<div class="row">
													<div class="form-group">
													<form action="<?php getSiteURL(); ?>dipl/complete/<?php echo $kandidat_id?>" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
														<input type="hidden" name="kandidat_id" id="kandidat_id" value="<?php echo $kandidat_id; ?>"/>
														<div class="col-xs-12">
															<p>
															Da biste za 10 dana dobili termin za vizu u Njemačkoj ambasadi,na osnovu novog zakona od 01.03.2020. godine, potrebno je da  nostrificirate Vašu diplomu u Njemačkoj.
															</p>
															<hr>
															<div class="dipl_pitanje">
																<p>
																Da li ste Vi nostrificirali Vašu diplomu u Njemačkoj?
																</p>
																<div class="row" style="text-align: center;">
																	<div class="col-xs-6" style="padding: 0;">
																		<button type="button" name="dipl_da_tip2" class="tipka_da ajax_ima_dipl" id="dipl_da_tip2" value="da" >DA</button>
																	</div>
																	<div class="col-xs-6" style="padding: 0;">
																		<button type="button" name="dipl_ne_tip2" class="tipka_ne ajax_nema_dipl" id="dipl_ne_tip2" value="ne" >NE</button>
																	</div>
																</div>
																<script>
																	$(document).on('click', '.ajax_ima_dipl', function () {
																		$('.skola_pitanje').removeClass('hidden');
																		$("html, body").animate({ scrollTop: $(document).height() }, 1000);
																		$(document).find("#dipl_da_tip2").prop('disabled', true);
																		$(document).find("#dipl_ne_tip2").prop('disabled', true);
																		var kandidat_id = $(this).parent().parent().parent().parent().parent().find('input[name="kandidat_id"]').val();
																		$.ajax({
																			url: '/public_kandidati_messenger.php?page=update_dipl_tip2',
																			type: 'POST',
																			data: {'kandidat_id': kandidat_id, 'ima_dipl': 1},
																			dataType: 'html',
																			success: function(data) {
																				
																			}
																		});
																	});
																	$(document).on('click', '.ajax_nema_dipl', function () {
																		$('.skola_pitanje').removeClass('hidden');
																		$("html, body").animate({ scrollTop: $(document).height() }, 1000);
																		$(document).find("#dipl_da_tip2").prop('disabled', true);
																		$(document).find("#dipl_ne_tip2").prop('disabled', true);
																		var kandidat_id = $(this).parent().parent().parent().parent().parent().find('input[name="kandidat_id"]').val();
																		$.ajax({
																			url: '/public_kandidati_messenger.php?page=update_dipl_tip2',
																			type: 'POST',
																			data: {'kandidat_id': kandidat_id, 'ima_dipl': 0},
																			dataType: 'html',
																			success: function(data) {
																				
																			}
																		});
																	});
																</script>
															</div>
															<hr>
															<div class="skola_pitanje hidden" style="max-width: 100%;">
																<p>Molimo vas da odaberete koju ste srednju školu završili, zatim smjer:</p>
																
																<select class="select_izgled" name="srednja" id="srednja" data-live-search="true" required>
																	<option value="" selected disabled><?php echo "Odaberi školu"; ?></option>
																	<?php 
																		$query_skole = $db->prepare("
																				SELECT * FROM idk_skole WHERE skola_tip_obrazovanja = 'srednje' ORDER BY skola_naziv
																		");
																		$query_skole->execute();
																		
																		while($row_skola = $query_skole->fetch()){
																			$skola_naziv = $row_skola['skola_naziv'];
																			$skola_tip = $row_skola['skola_tip_obrazovanja'];
																			$skola_id = $row_skola['skola_id'];
																			?>
																			<option value="<?php echo $skola_naziv; ?>" class="" data-skola_id="<?php echo $skola_id; ?>"><?php echo $skola_naziv; ?></option>
																			<?php
																		}
																	?>
																			<option value="ostalo" class="" data-skola_id="ostalo"><?php echo "Ostalo"; ?></option>
																	<span class="materail-input-block__line"></span>
																</select>
																
																<script>
																	$('#srednja').on('change', function() {
																		$('.smjerovi_all').addClass('hidden');
																		$('#smjerovi').prop('selectedIndex',0);
																		var skola_id = $(this).find(':selected').data('skola_id');
																		if(skola_id != "ostalo"){
																			$('.opt_'+skola_id+'').removeClass('hidden');
																			$('#smjerovi').removeClass('hidden');
																			$('.unos_smjer').addClass('hidden');
																			$('.unos_skola').addClass('hidden');
																			$('#smjerovi').prop('required', true);
																			$('.unos_smjer').removeAttr('required');
																			$('.unos_skola').removeAttr('required');
																		}else{
																			$('#smjerovi').removeAttr('required');
																			$('#smjerovi').addClass('hidden');
																			$('.unos_smjer').removeClass('hidden');
																			$('.unos_skola').removeClass('hidden');
																			$('.unos_smjer').prop('required', true);
																			$('.unos_skola').prop('required', true);
																		}
																	});
																</script>
																<input type="text" class="unosi unos_skola hidden" name="unos_skola" placeholder="Unesi školu">
																
																<select class="select_izgled" name="smjerovi" id="smjerovi" data-live-search="true" required>
																	<option value="" selected disabled><?php echo "Odaberi smjer"; ?></option>
																	<?php 
																		$query_skole_smjer = $db->prepare("
																				SELECT * FROM idk_skole_smjerovi ORDER BY ss_naziv
																		");
																		$query_skole_smjer->execute();
																		
																		while($row_skola_smjer = $query_skole_smjer->fetch()){
																			$smjer_naziv = $row_skola_smjer['ss_naziv'];
																			$smjer_skola_id = $row_skola_smjer['ss_skola_id'];
																			?>
																			<option value="<?php echo $smjer_naziv; ?>" class="hidden smjerovi_all opt_<?php echo $smjer_skola_id; ?>"><?php echo $smjer_naziv; ?></option>
																			<?php
																		}
																	?>
																</select>
																<input type="text" class="unosi unos_smjer hidden" name="unos_smjer" placeholder="Unesi smjer">
																<div style="margin: auto; text-align: center;">
																	<button type="submit" class="tipka_da kraj_skole" id="srednja_da_kraj" >POTVRDI</button>
																</div>
																
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
						<div class="row">
							<footer  id="footer"><?php getCopyright(); ?></footer>
						</div>
					</div>
				</div>
				<?php
			}
		break;
		
		case "update_dipl_tip2":
			
			$kandidat_id = $_POST['kandidat_id'];
			$ima_dipl = $_POST['ima_dipl'];
			
			// var_dump($ima_dipl);
			// exit();
			$check_query = $db->prepare("
								SELECT povezan_na_dipl FROM idk_kandidati WHERE kandidat_id = $kandidat_id
								");
			$check_query->execute();
			
			$check_row = $check_query->fetch();
			$povezan_na_dipl = $check_row['povezan_na_dipl'];
		
			if($povezan_na_dipl == 0){
				if($ima_dipl == 0){
					$povijest = 1;
					$sms_bot_crm = 1;
					$viber_povijest = 4;
					//var_dump($ima_dipl);
					
					
					//UPDATE povezan_na_dipl u tabeli idk_kandidati
					$update_query = $db->prepare("
								UPDATE idk_kandidati
								SET povezan_na_dipl = :povezan_na_dipl, kandidat_poslan_dipl = :kandidat_poslan_dipl
								WHERE kandidat_id = :kandidat_id
					");
					
					$update_query->execute(array(
								'kandidat_id' => $kandidat_id,
								'kandidat_poslan_dipl' => 23,
								'povezan_na_dipl' => 1
								));
					
					CopyKandidatinDipl($kandidat_id, $povijest, $viber_povijest);
				
				}else {
					$db_upd_22 = $db->prepare("
									UPDATE idk_kandidati
									SET kandidat_poslan_dipl = 22 WHERE kandidat_id = $kandidat_id
									");

					$db_upd_22->execute();
					$link = "https://crm.job-step.com/kandidati?page=open&id=".$kandidat_id;
					$mail = new PHPMailer;
					$mail->isSMTP();
					
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
				}
			}
			
		break;
		
		case "dipl_completed_tip2":
		// $kandidat_id = $_POST['id'];
		// var_dump($_POST['id']);
		// var_dump($kandidat_id);
		if(isset($_POST['srednja']))
		{
			$srednja = $_POST['srednja'];
			if($srednja == "ostalo"){
				$srednja = $_POST['unos_skola'];
				$smjerovi = $_POST['unos_smjer'];
			}else{
				$smjerovi = $_POST['smjerovi'];
			}
			
			//INSERT SKOLE
					
			$insert_skole = $db->prepare("
							INSERT INTO idk_kandidat_edukacija
								(ke_naziv_kvalifikacije, ke_naziv, ke_vrsta_obrazovanja, ke_kandidat_id)
							VALUES
								(:ke_naziv_kvalifikacije, :ke_naziv, :ke_vrsta_obrazovanja, :ke_kandidat_id)");
	
			$insert_skole->execute(array(
						':ke_naziv_kvalifikacije' => $smjerovi,
						':ke_naziv' => $srednja,
						':ke_vrsta_obrazovanja' => "srednje",
						':ke_kandidat_id' => $kandidat_id));
		}
						
		?>
		
		<style>
			.col-xs-12 > .img-responsive{
				max-width: 90px !important;
				margin-top: 10px !important;
			}
			.alert > h3{
				margin-top: 0px !important;
				font-size: 19px !important;
			}
			.alert > h5{
				font-size: 15px !important;
				border-left: none !important; 
				margin-bottom: 0px !important;
			}
			.col-md-4 > iframe{
				margin-bottom: 10px;
			}
			
		</style>
		<div id="content_public">
			<div class="container">
				<div class="row">
					<div class="col-xs-12 text-center">
						<img class="img-responsive" src="<?php getSiteURL(); ?>images/Jobstep_logo_new.png" alt="" style="max-width:200px;">
					</div>
					<div class="col-xs-12">
						<br />
					</div>
				</div>
				<div class="row">
					<div class="col-md-6 col-md-offset-3 idk_margin_top10">
						<div style = "background-color: #ffffff; color: #6097a0; text-align: center;" class="alert material-alert material-alert_success">
							<h3><?php echo "Hvala na izdvojenom vremenu, naš tim će Vas kontaktirati za više detalja."; ?>
							</h3>
							<hr>
							<h5><?php echo "Preuzmite našu besplatnu aplikaciju i pratite sve naše aktuelnosti i poslovne ponude."; ?>
							</h5>
						</div>
					</div>
				</div>
				<!--<div class="row">
					<div class="col-md-4 col-md-offset-4">
						<iframe width="100%" height="200" src="https://www.youtube.com/embed/rttgZz8ohEI" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
					</div>
				</div>-->
				<div class="row">
					<div class="col-md-6 col-md-offset-3 text-center">
						<div class="col-xs-8 col-xs-offset-2">
							<a class="opt_mess" href = "https://play.google.com/store/apps/details?id=net.job_step.jobstepmessenger"><!-- https://play.google.com/store/apps/details?id=net.job_step.jobstepmessenger -->
								<img style = "width: 100%; " src = "<?php getSiteURL(); ?>files/kandidati/google-play-store-icon-android.png">
							</a>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6 col-md-offset-3 idk_margin_top10 text-center">
						<div class="col-xs-8 col-xs-offset-2">
							<a class="opt_mess" href = "https://apps.apple.com/hr/app/jobstep-messenger/id1486805317">
								<img  style = "width: 100%; " src = "<?php getSiteURL(); ?>files/kandidati/google-play-store-icon-ios.png">
							</a>
						</div>
					</div>
				</div>
				<input type="hidden" name="kan_id" id="kan_id" value="<?php echo $kandidat_id; ?>">
				<script>
					$(document).on('click', '.opt_mess', function () {
						var kandidat_id = $(this).parent().parent().parent().parent().find('input[name="kan_id"]').val();
						//alert(kandidat_id);
						$.ajax({
							url: '/public_kandidati_messenger.php?page=sms_za_messenger',
							type: 'POST',
							data: {'kandidat_id': kandidat_id},
							dataType: 'html',
							success: function(data) {
								
							}
						});
					});
				</script>
			</div>
		</div>
		<?php
		break;
		
		case "dipl_info_resend":
			
			$check_query = $db->prepare("
							SELECT povezan_na_dipl, kandidat_poslan_dipl FROM idk_kandidati WHERE kandidat_id = $kandidat_id
							");
			$check_query->execute();
			
			$check_row = $check_query->fetch();
			$povezan_na_dipl = $check_row['povezan_na_dipl'];
			$kandidat_poslan_dipl = $check_row['kandidat_poslan_dipl'];
			// var_dump($povezan_na_dipl);
			// exit();
			//if($povezan_na_dipl == 1 or $kandidat_poslan_dipl == 22 or $kandidat_poslan_dipl == 23){
			if($povezan_na_dipl == 1 or $kandidat_poslan_dipl == 52 or $kandidat_poslan_dipl == 53){
				header("Location: ".getSiteURLR()."dipl/complete/$kandidat_id");
			}else{
				$db_update_query = $db->prepare("
								UPDATE idk_kandidati
								SET kandidat_poslan_dipl = 51 WHERE kandidat_id = $kandidat_id
								");

				$db_update_query->execute();
				?>
				<style>
					.select_izgled{
						max-width: 100%;
						border: 3px solid;
						border-color: #6097a0;
						background-color: white;
						border-radius: 1.25rem;
						margin-bottom: 10px;
						width: 100%;
						padding: 2px;
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
					.unosi{
						max-width: 100%;
						border: 3px solid;
						border-color: #6097a0;
						background-color: white;
						border-radius: 1.25rem;
						margin-bottom: 10px;
						width: 100%;
						padding: 2px;
					}
				</style>
				<div id="content_public">
					<div class="container-fluid">
						<div class="row">
							<div class="col-xs-12 text-center">
							   <!-- <img class="img-responsive" src="<?php getSiteURL(); ?>images/Jobstep_logo_new.png" alt="" style="max-width:200px; margin-top: 20px; margin-bottom: 20px;"> -->
							</div>
						</div>
						<div class = "row">
							<div class="col-xs-12">
								<div class="" style = "min-height: 200px; margin-top: 20px;">
									<div class = "row">
										<div class="col-xs-12">
											<div id = "dizajn_vanjski_dio">
												<div class="row">
													
												</div>
												<div class="row">
													<div class="form-group">
													<form action="<?php getSiteURL(); ?>dipl/complete/<?php echo $kandidat_id?>" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
														<input type="hidden" name="kandidat_id" id="kandidat_id" value="<?php echo $kandidat_id; ?>"/>
														<div class="col-xs-12">
															<p>
															Da biste za 10 dana dobili termin za vizu u Njemačkoj ambasadi,na osnovu novog zakona od 01.03.2020. godine, potrebno je da  nostrificirate Vašu diplomu u Njemačkoj.
															</p>
															<hr>
															<div class="dipl_pitanje">
																<p>
																Da li ste Vi nostrificirali Vašu diplomu u Njemačkoj?
																</p>
																<div class="row" style="text-align: center;">
																	<div class="col-xs-6" style="padding: 0;">
																		<button type="button" name="dipl_da_tip2" class="tipka_da ajax_ima_dipl" id="dipl_da_tip2" value="da" >DA</button>
																	</div>
																	<div class="col-xs-6" style="padding: 0;">
																		<button type="button" name="dipl_ne_tip2" class="tipka_ne ajax_nema_dipl" id="dipl_ne_tip2" value="ne" >NE</button>
																	</div>
																</div>
																<script>
																	$(document).on('click', '.ajax_ima_dipl', function () {
																		$('.skola_pitanje').removeClass('hidden');
																		$("html, body").animate({ scrollTop: $(document).height() }, 1000);
																		$(document).find("#dipl_da_tip2").prop('disabled', true);
																		$(document).find("#dipl_ne_tip2").prop('disabled', true);
																		var kandidat_id = $(this).parent().parent().parent().parent().parent().find('input[name="kandidat_id"]').val();
																		$.ajax({
																			url: '/public_kandidati_messenger.php?page=update_dipl_resend',
																			type: 'POST',
																			data: {'kandidat_id': kandidat_id, 'ima_dipl': 1},
																			dataType: 'html',
																			success: function(data) {
																				
																			}
																		});
																	});
																	$(document).on('click', '.ajax_nema_dipl', function () {
																		$('.skola_pitanje').removeClass('hidden');
																		$("html, body").animate({ scrollTop: $(document).height() }, 1000);
																		$(document).find("#dipl_da_tip2").prop('disabled', true);
																		$(document).find("#dipl_ne_tip2").prop('disabled', true);
																		var kandidat_id = $(this).parent().parent().parent().parent().parent().find('input[name="kandidat_id"]').val();
																		$.ajax({
																			url: '/public_kandidati_messenger.php?page=update_dipl_resend',
																			type: 'POST',
																			data: {'kandidat_id': kandidat_id, 'ima_dipl': 0},
																			dataType: 'html',
																			success: function(data) {
																				
																			}
																		});
																	});
																</script>
															</div>
															<hr>
															<div class="skola_pitanje hidden" style="max-width: 100%;">
																<p>Molimo vas da odaberete koju ste srednju školu završili, zatim smjer:</p>
																
																<select class="select_izgled" name="srednja" id="srednja" data-live-search="true" required>
																	<option value="" selected disabled><?php echo "Odaberi školu"; ?></option>
																	<?php 
																		$query_skole = $db->prepare("
																				SELECT * FROM idk_skole WHERE skola_tip_obrazovanja = 'srednje' ORDER BY skola_naziv
																		");
																		$query_skole->execute();
																		
																		while($row_skola = $query_skole->fetch()){
																			$skola_naziv = $row_skola['skola_naziv'];
																			$skola_tip = $row_skola['skola_tip_obrazovanja'];
																			$skola_id = $row_skola['skola_id'];
																			?>
																			<option value="<?php echo $skola_naziv; ?>" class="" data-skola_id="<?php echo $skola_id; ?>"><?php echo $skola_naziv; ?></option>
																			<?php
																		}
																	?>
																			<option value="ostalo" class="" data-skola_id="ostalo"><?php echo "Ostalo"; ?></option>
																	<span class="materail-input-block__line"></span>
																</select>
																
																<script>
																	$('#srednja').on('change', function() {
																		$('.smjerovi_all').addClass('hidden');
																		$('#smjerovi').prop('selectedIndex',0);
																		var skola_id = $(this).find(':selected').data('skola_id');
																		if(skola_id != "ostalo"){
																			$('.opt_'+skola_id+'').removeClass('hidden');
																			$('#smjerovi').removeClass('hidden');
																			$('.unos_smjer').addClass('hidden');
																			$('.unos_skola').addClass('hidden');
																			$('#smjerovi').prop('required', true);
																			$('.unos_smjer').removeAttr('required');
																			$('.unos_skola').removeAttr('required');
																		}else{
																			$('#smjerovi').removeAttr('required');
																			$('#smjerovi').addClass('hidden');
																			$('.unos_smjer').removeClass('hidden');
																			$('.unos_skola').removeClass('hidden');
																			$('.unos_smjer').prop('required', true);
																			$('.unos_skola').prop('required', true);
																		}
																	});
																</script>
																<input type="text" class="unosi unos_skola hidden" name="unos_skola" placeholder="Unesi školu">
																
																<select class="select_izgled" name="smjerovi" id="smjerovi" data-live-search="true" required>
																	<option value="" selected disabled><?php echo "Odaberi smjer"; ?></option>
																	<?php 
																		$query_skole_smjer = $db->prepare("
																				SELECT * FROM idk_skole_smjerovi ORDER BY ss_naziv
																		");
																		$query_skole_smjer->execute();
																		
																		while($row_skola_smjer = $query_skole_smjer->fetch()){
																			$smjer_naziv = $row_skola_smjer['ss_naziv'];
																			$smjer_skola_id = $row_skola_smjer['ss_skola_id'];
																			?>
																			<option value="<?php echo $smjer_naziv; ?>" class="hidden smjerovi_all opt_<?php echo $smjer_skola_id; ?>"><?php echo $smjer_naziv; ?></option>
																			<?php
																		}
																	?>
																</select>
																<input type="text" class="unosi unos_smjer hidden" name="unos_smjer" placeholder="Unesi smjer">
																<div style="margin: auto; text-align: center;">
																	<button type="submit" class="tipka_da kraj_skole" id="srednja_da_kraj" >POTVRDI</button>
																</div>
																
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
						<div class="row">
							<footer  id="footer"><?php getCopyright(); ?></footer>
						</div>
					</div>
				</div>
				<?php
			}
		break;
		
		case "update_dipl_resend":
			
			$kandidat_id = $_POST['kandidat_id'];
			$ima_dipl = $_POST['ima_dipl'];
			
			// var_dump($ima_dipl);
			// exit();
			$check_query = $db->prepare("
								SELECT povezan_na_dipl FROM idk_kandidati WHERE kandidat_id = $kandidat_id
								");
			$check_query->execute();
			
			$check_row = $check_query->fetch();
			$povezan_na_dipl = $check_row['povezan_na_dipl'];
		
			if($povezan_na_dipl == 0){
				if($ima_dipl == 0){
					$povijest = 1;
					$sms_bot_crm = 1;
					$viber_povijest = 4;
					//var_dump($ima_dipl);
					
					
					//UPDATE povezan_na_dipl u tabeli idk_kandidati
					$update_query = $db->prepare("
								UPDATE idk_kandidati
								SET povezan_na_dipl = :povezan_na_dipl, kandidat_poslan_dipl = :kandidat_poslan_dipl
								WHERE kandidat_id = :kandidat_id
					");
					
					$update_query->execute(array(
								'kandidat_id' => $kandidat_id,
								'kandidat_poslan_dipl' => 53,
								'povezan_na_dipl' => 1
								));
					
					CopyKandidatinDipl($kandidat_id, $povijest, $viber_povijest);
				
				}else {
					$db_upd_22 = $db->prepare("
									UPDATE idk_kandidati
									SET kandidat_poslan_dipl = 52 WHERE kandidat_id = $kandidat_id
									");

					$db_upd_22->execute();
					$link = "https://crm.job-step.com/kandidati?page=open&id=".$kandidat_id;
					$mail = new PHPMailer;
					$mail->isSMTP();

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
				}
			}
			
		break;
		
		case "sms_za_messenger":
			$kandidat_id = $_POST['kandidat_id'];
			$nalog_id = 44;
			$random_string = generateRandomString();
			$options = [
				'cost' => 10,
			];
			$random_password = password_hash($random_string, PASSWORD_BCRYPT, $options);
			
			$query_users = $db->prepare("
							SELECT kandidat_id
							FROM users
							WHERE kandidat_id = $kandidat_id
							");  

			$query_users->execute();
			$check_user = $query_users->rowCount();
			$row_qu = $query_users->fetch();
			//var_dump($ponovno_slanje);
			
			if($check_user == 0){
				
				$kan_info = $db->prepare("
								SELECT kandidat_id, kandidat_ime, kandidat_prezime, kandidat_mobitel
								FROM idk_kandidati
								WHERE kandidat_id = $kandidat_id
								");  
				
				$kan_info->execute();
				$row_kan_info = $kan_info->fetch();
				$kandidat_ime = $row_kan_info['kandidat_ime'];
				$kandidat_prezime = $row_kan_info['kandidat_prezime'];
				$kandidat_full_name = $kandidat_ime." ".$kandidat_prezime;
				$kandidat_mobitel = $row_kan_info['kandidat_mobitel'];
				
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
							':phone' => $kandidat_mobitel,
							':name' => $kandidat_full_name,
							':nalog_id' => $nalog_id,
							':email' => $bot_koriscnicko_ime,
							':password' => $random_password,
							':kandidat_id' => $kandidat_id));
				
				$log_desc = "Kandidat: " . $kandidat_ime . " " . $kandidat_prezime . "(".$kandidat_id.").(".$random_string.")";
				$log_type = "6";
				addToLogs($log_desc, $log_type);
				
				 
				//za infobip formatira se bez nula i bez++
				$phone_f = str_replace("+", '00', $kandidat_mobitel);
				
				//INFOBIP
				sendSmsToCandidateInfobip3($random_string, $kandidat_mobitel, $bot_koriscnicko_ime);
			}
			
		break;
		case "nost_bs":
			
			$check_query = $db->prepare("
							SELECT povezan_na_dipl FROM idk_kandidati WHERE kandidat_id = $kandidat_id
							");
			$check_query->execute();
			
			$check_row = $check_query->fetch();
			$povezan_na_dipl = $check_row['povezan_na_dipl'];
			// var_dump($povezan_na_dipl);
			// exit();
			if($povezan_na_dipl == 1){
				header("Location: ".getSiteURLR()."dipl/completed/$kandidat_id");
			}else{
				$db_update_query = $db->prepare("
								UPDATE idk_kandidati
								SET kandidat_poslan_dipl = 51 WHERE kandidat_id = $kandidat_id
								");

				$db_update_query->execute();
				?>
				<style>
					@font-face { font-family: Bank-Gothic-Medium; src: url('/css/Bank-Gothic-Medium.ttf'); }
					.naslov_jobstep{
						font-family: Bank-Gothic-Medium;
						font-size: 4.25rem;
						padding: 5px 0px 0px 0px !important;
					}
					.select_izgled{
						max-width: 100%;
						border: 3px solid;
						border-color: #6097a0;
						background-color: white;
						border-radius: 1.25rem;
						margin-bottom: 10px;
						width: inherit;
						padding: 2px;
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
					.unosi{
						max-width: 100%;
						border: 3px solid;
						border-color: #6097a0;
						background-color: white;
						border-radius: 1.25rem;
						margin-bottom: 10px;
						width: inherit;
						padding: 2px;
					}
				</style>
				<div id="content_public">
					<div class="container-fluid">
						<div class="row">
							<div class="col-xs-12 text-center">
							   <!-- <img class="img-responsive" src="<?php getSiteURL(); ?>images/Jobstep_logo_new.png" alt="" style="max-width:200px; margin-top: 20px; margin-bottom: 20px;"> -->
							</div>
						</div>
						<div class = "row">
							<div class="col-xs-12">
								<div class="" style = "min-height: 200px; margin-top: 20px;">
									<div class = "row">
										<div class="col-xs-12">
											<div id = "dizajn_vanjski_dio">
												<div class="row text-center naslov_jobstep">JOBSTEP</div>
												<div class="row">
													<div class="form-group">
													<form action="<?php getSiteURL(); ?>public_kandidati_messenger?page=update_nost" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
														<input type="hidden" name="kandidat_id" id="kandidat_id" value="<?php echo $kandidat_id; ?>"/>
														<div class="col-xs-12">
															<p>
															Po novom zakonu na termin čekate samo 10 dana.
															</p>
															
															<hr>
															<div class="dipl_pitanje">
																<p>
																Želite li saznati kako možete najbrže nostrificirati diplomu i steći uslove za apliciranje termina?
																</p>
																<div class="row" style="text-align: center;">
																	<div class="col-xs-6" style="padding: 0;"><button type="submit" name="dipl_da" class="tipka_da" id="dipl_da" value="da" >DA</button></div>
																	<div class="col-xs-6" style="padding: 0;"><button type="submit" name="dipl_ne" class="tipka_ne" id="dipl_ne" value="ne" >NE</button></div>
																</div>
																<hr>
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
						<div class="row">
							<footer  id="footer"><?php getCopyright(); ?></footer>
						</div>
					</div>
				</div>
				<?php
			}
		break;
		case "nost_rs":
			
			$check_query = $db->prepare("
							SELECT povezan_na_dipl FROM idk_kandidati WHERE kandidat_id = $kandidat_id
							");
			$check_query->execute();
			
			$check_row = $check_query->fetch();
			$povezan_na_dipl = $check_row['povezan_na_dipl'];
			// var_dump($povezan_na_dipl);
			// exit();
			if($povezan_na_dipl == 1){
				header("Location: ".getSiteURLR()."dipl/completed/$kandidat_id");
			}else{
				$db_update_query = $db->prepare("
								UPDATE idk_kandidati
								SET kandidat_poslan_dipl = 91 WHERE kandidat_id = $kandidat_id
								");

				$db_update_query->execute();
				?>
				<style>
					@font-face { font-family: Bank-Gothic-Medium; src: url('/css/Bank-Gothic-Medium.ttf'); }
					.naslov_jobstep{
						font-family: Bank-Gothic-Medium;
						font-size: 4.25rem;
						padding: 5px 0px 0px 0px !important;
					}
					.select_izgled{
						max-width: 100%;
						border: 3px solid;
						border-color: #6097a0;
						background-color: white;
						border-radius: 1.25rem;
						margin-bottom: 10px;
						width: inherit;
						padding: 2px;
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
					.unosi{
						max-width: 100%;
						border: 3px solid;
						border-color: #6097a0;
						background-color: white;
						border-radius: 1.25rem;
						margin-bottom: 10px;
						width: inherit;
						padding: 2px;
					}
				</style>
				<div id="content_public">
					<div class="container-fluid">
						<div class="row">
							<div class="col-xs-12 text-center">
							   <!-- <img class="img-responsive" src="<?php getSiteURL(); ?>images/Jobstep_logo_new.png" alt="" style="max-width:200px; margin-top: 20px; margin-bottom: 20px;"> -->
							</div>
						</div>
						<div class = "row">
							<div class="col-xs-12">
								<div class="" style = "min-height: 200px; margin-top: 20px;">
									<div class = "row">
										<div class="col-xs-12">
											<div id = "dizajn_vanjski_dio">
												<div class="row text-center naslov_jobstep">JOBSTEP</div>
												<div class="row">
													<div class="form-group">
													<form action="<?php getSiteURL(); ?>public_kandidati_messenger?page=update_nost" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
														<input type="hidden" name="kandidat_id" id="kandidat_id" value="<?php echo $kandidat_id; ?>"/>
														<div class="col-xs-12">
															<p>
															Storniran Vam je termin? Ne brinite. <br/><br/>Po novom zakonu možete otići u roku od par meseci. <br/>
															</p>
															
															<hr>
															<div class="dipl_pitanje">
																<p>
																Želite li saznati kako brzo bez neizvesnog čekanja termina po novom zakonu mozete otici u Nemačku?
																</p>
																<div class="row" style="text-align: center;">
																	<div class="col-xs-6" style="padding: 0;"><button type="submit" name="dipl_da" class="tipka_da" id="dipl_da" value="da" >DA</button></div>
																	<div class="col-xs-6" style="padding: 0;"><button type="submit" name="dipl_ne" class="tipka_ne" id="dipl_ne" value="ne" >NE</button></div>
																</div>
																<hr>
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
						<div class="row">
							<footer  id="footer"><?php getCopyright(); ?></footer>
						</div>
					</div>
				</div>
				<?php
			}
		break;
		case "update_nost":
			
			$kandidat_id = $_POST['kandidat_id'];
			
			if(!empty($_POST["dipl_da"])){
				$dipl_da = $_POST["dipl_da"];
				//var_dump($dipl_da);
				$ima_dipl = 0;
			}else{
				$ima_dipl = 1;
			}
					
			$check_query = $db->prepare("
							SELECT povezan_na_dipl FROM idk_kandidati WHERE kandidat_id = $kandidat_id
							");
			$check_query->execute();
			
			$check_row = $check_query->fetch();
			$povezan_na_dipl = $check_row['povezan_na_dipl'];
		
			if($povezan_na_dipl == 0){
				if($ima_dipl == 0){
					$povijest = 1;
					$sms_bot_crm = 5;
					//var_dump($ima_dipl);
					
					//UPDATE povezan_na_dipl u tabeli idk_kandidati
					$update_query = $db->prepare("
								UPDATE idk_kandidati
								SET povezan_na_dipl = :povezan_na_dipl, kandidat_poslan_dipl = :kandidat_poslan_dipl
								WHERE kandidat_id = :kandidat_id
					");
					
					$update_query->execute(array(
								'kandidat_id' => $kandidat_id,
								'kandidat_poslan_dipl' => 53,
								'povezan_na_dipl' => 1
								));
					
					CopyKandidatinDipl($kandidat_id, $povijest, $sms_bot_crm);
				
				}else {
					$db_upd_22 = $db->prepare("
									UPDATE idk_kandidati
									SET kandidat_poslan_dipl = 52 WHERE kandidat_id = $kandidat_id
									");

					$db_upd_22->execute();
					$link = "https://crm.job-step.com/kandidati?page=open&id=".$kandidat_id;
					$mail = new PHPMailer;
					$mail->isSMTP();

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
					$mail->Body    = "Slijedeci kandidat je oznacio da ne zeli saznati vise: ".$link;
					$mail->AltBody = "";

					if(!$mail->send()) {
						echo 'Message could not be sent.';
						echo 'Mailer Error: ' . $mail->ErrorInfo;
					}else{
						echo "Mail sent";
					}
				}
			}
			
			header("Location: dipl/completed/$kandidat_id");
		break;
		
		
		}
	?>
	
</body>
</html>
