<?php 	
header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Methods: GET, POST');

header("Access-Control-Allow-Headers: X-Requested-With");
	include("includes/functions.php");
	if(isset($_REQUEST["page"])) 
		$page = $_REQUEST["page"]; 
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
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Jobstep</title>

	<?php include('includes/head.php'); ?>
	

</head>
<body>

			<?php
	if(!$_POST) exit;
	$company_name = $_POST['company_ime'];
	$company_kontakt1 = $_POST['company_vorname'];
	$company_kontakt2 = $_POST['company_nachname'];
	$company_kontakt = $company_kontakt1." ".$company_kontakt2;
	$company_tel = $_POST['company_tel'];
	$company_mail = $_POST['company_mail'];
	$company_textarea = $_POST['company_textarea'];
	
	
	if(trim($company_name) == '') {
		echo '<div class="error_message">Sie müssen Ihres Firmanamen eingeben.</div>';
			header( "refresh:5;url=https://job-step.de/firma-anmelden" );
	} else if(trim($company_kontakt1 ) == '') {
		echo '<div class="error_message">Sie müssen Ihren Vornamen eingeben.</div>';
			header( "refresh:5;url=https://job-step.de/firma-anmelden" );
	}else if(trim($company_kontakt2 ) == '') {
		echo '<div class="error_message">Sie müssen Ihren Nachname eingeben.</div>';
			header( "refresh:5;url=https://job-step.de/firma-anmelden" );
	} else if(!is_numeric($company_tel)) {
		echo '<div class="error_message">Telefonnummer kann nur Nummern enthalten.</div>';
			header( "refresh:5;url=https://job-step.de/firma-anmelden" );
	} else if(trim($company_kontakt) == ''){
		echo '<div class="error_message"> Bitte füllen Sie die Felder mit dem roten Zeichen.</div>';
			header( "refresh:5;url=https://job-step.de/firma-anmelden" );
	}else if(trim($company_mail) == ''){
		echo '<div class="error_message"> Bitte geben Sie ihre E-Mail-Adresse ein.</div>';
			header( "refresh:5;url=https://job-step.de/firma-anmelden" );
	}else if(!strpos($company_mail,"@")){
		echo '<div class="error_message"> Die E-Mail, die Sie eingegeben haben, scheint nicht richtig zu sein.</div>';
		header( "refresh:5;url=https://job-step.de/firma-anmelden" );
	}

	if(get_magic_quotes_gpc()) {
		$company_textarea = stripslashes($company_textarea);
	}
	
	$mail_referentDak = "i.suljic@wwtravel.net";
	$subject = "JOBSTEP - KOMPANIJA ŽELI STUPITI U KONTAKT";
	$body = 'Kompanija : <u>'.$company_name.'</u> je ispunila prijavu. <br><br>
			Kontakt osoba: <b>'.$company_kontakt.'</b><br>
			Poruka: <b>'.$company_textarea.'</b><br>
			Telefon: <b>'.$company_tel.'</b><br>
			Email: <b>'.$company_mail.'</b><br>
			</b><br><br> <i>JOBSTEP - sistem automatskog obavještavanja</i>';
	$alt = 'JOBSTEP - KOMPANIJA ŽELI STUPITI U KONTAKT';
	
	$_POST = array();
	header("Location: https://job-step.de/");
	?>
	</body>
</html>