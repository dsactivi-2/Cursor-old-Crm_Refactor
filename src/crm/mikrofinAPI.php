<?php 
header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Methods: GET, POST');

header("Access-Control-Allow-Headers: X-Requested-With");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include("includes/functions.php");
date_default_timezone_set('Europe/Sarajevo');

/***************************************************
			PRISTUPNE TAČKE
***************************************************/
$htmlSadrzaj = '<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>Predračun DIPL</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta content="Jobstep International d.o.o.  | Agencija za posredovanje pri zapošljavanju" property="og:site_name">
    
		<meta content="Jobstep International d.o.o.  | Agencija za posredovanje pri zapošljavanju" property="og:site_name">
		<meta content="" property="og:image">
		<meta content="Jobstep" property="og:title">
		<meta content="Agencija Jobstep osnovana je 2010. godine u Bihaću, kao samostalna agencija za zapošljavanje svih državljana sa prostora bivše Jugoslavije na području Njemačke." property="og:description">

		<!-- IDK Optimizacija | Jobstep International d.o.o. -->

		<meta property="og:title" content="Jobstep International d.o.o. | Agencija za posredovanje pri zapošljavanju" />
		<meta property="og:description" content="Agencija Jobstep osnovana je 2010. godine u Bihaću, kao samostalna agencija za zapošljavanje svih državljana sa prostora bivše Jugoslavije na području Njemačke." />
		<meta property="og:image" content="images/jobstep/header_img_seo.jpg" />
		<meta property="og:type" content="website">
		<meta property="og:site_name" content="Jobstep International d.o.o. | Agencija za posredovanje pri zapošljavanju">
		<meta property="og:url" content="<?php getSiteUrl(); ?>">
		<link href="css/bootstrap.min.css" rel="stylesheet">
		<link href="css/jasny-bootstrap.min.css" rel="stylesheet">
		<link rel="icon" href="images/Jobstep-logo_news.png">
	</head>
	<body>
		<style>
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
			@media screen and (max-width: 991px) {
				.img-responsive{
					max-width: 100px !important;
					margin-top: 10px !important;
					margin-bottom: 10px !important;
				}
			}
			body{
				overflow-y: hidden; overflow-x: hidden;
			}
			.img-responsive{
				max-width:200px; margin-top: 20px; margin-bottom: 20px;
				}
			.alertx{
				background-color: #ffffff !important; color: #6097a0 !important; text-align: center !important; margin-top: 25px !important;
				}
			#footer{
				position: absolute; left: 0; bottom: 0; width: 100%; border-top: 1px solid #ccc; font-size: 12px; text-align: center; margin-top: 0px;
				}
			@media screen and (max-width: 450px) {
				.alertx h3{font-size: 18px;}
				.img-responsive{max-width:200px; margin-top: 20px; margin-bottom: 20px;}
			}
			@media screen and (max-width: 350px) {
				.alertx h3{font-size: 14px;}
				.img-responsive{max-width:100px; margin-top: 10px; margin-bottom: 10px;}
			}
		</style>';
		
		
if(isset($_GET['action'])){
	$akcija = $_GET['action'];
	
	switch($akcija){
		case 'deleteAccess':
			$dir = new DirectoryIterator($_SERVER["DOCUMENT_ROOT"]."/files/public_temp_files/predracuni_dipl");
				foreach ($dir as $fileinfo){
								$naziv_fajla = $fileinfo->getFilename();
								$pos_splitera					 =	 strpos(	$naziv_fajla	,	"_"); // SPLITER
								$numericka_vrijednost_datuma	 =	 substr(	$naziv_fajla	,	$pos_splitera+1,	 12); // VRIJEDNOST U SESIJI SADRŽI ID KORISNIKA_&DATUMKREACIJE&NASUMIČANBROJ
								$datum 							 = 	 date  (	"Y-m-d H:i:s"		,	strtotime(	$numericka_vrijednost_datuma	)	); 
								if(  date  (	'Y-m-d H:i:s'	,    strtotime($datum. '+2 days'))	 <   date  (	'Y-m-d H:i:s'	)){
										if(	strlen ( $naziv_fajla ) > 10){
											unlink($_SERVER["DOCUMENT_ROOT"]."/files/public_temp_files/predracuni_dipl/".$fileinfo->getFilename());
										}
								}
								
				}
		break;
		case 'procureDownload':
			if(isset($_GET['id'])){
				$kandidat_id = $_GET['id'];
				if(createTempFile($kandidat_id)){
					
					$link = createTempFile($kandidat_id);
					header("Content-Type: application/pdf");
					header('Content-Disposition: attachment; filename="jobstep_nostrf_predracun.pdf"');
					readfile("$link");
					ob_end_flush();
					
					
					echo $htmlSadrzaj;
					echo '<div class="container-fluid">
								<div class="content_box">
									<div class="row">
										<div class="col-sm-12 text-center">
											<img class="img-responsive img-fluid" style="display:inline-block" src="images/Jobstep_logo_new.png" alt="">
										</div>
									</div>
									<div class="row">
										<div class="col-md-12">
											<div class="row">
												<div class = "col-md-8 col-md-offset-2">
													<div class="row">
														<div class = "col-sm-12 text-center">
															<div class="alert material-alert material-alert_success alertx">
																<h3>Ako preuzimanje dokumenta ne započne uskoro, kliknite <a href ="'.$link.'">OVDJE</a>
																</h3>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<footer  id="footer">
										<p>
										©2020 Sva prava pridržana - Jobstep IT Solutions
										</p>
									</footer>
								</div>
							</div>
						</body>
					</html>';
				}else
					header("Location: https://crm.job-step.com/error404");
			}
		break;
			
	
	}
}
?>