<?php 
	$candidate_id = $_REQUEST['id'] ?? null;
	$candidate_check = $_REQUEST['check'] ?? null;
	$candidate_lang = $_REQUEST['lang'] ?? null;
	
	if ($candidate_id == null OR $candidate_check == null OR $candidate_lang == null) {
		exit();
	}
	
	$txt_reg_ty_1 		= "";
	$txt_prava 			= "";
	$txt_text_na_dnu 	= "";
	if($candidate_lang == "de"){
		$txt_reg_ty_1 			= "Vielen Dank! Ihre Daten sind erfolgreich zugesendet.";
		$txt_prava	 			= "Alle Rechte vorbehalten";
		$txt_text_na_dnu 		= "JOBSTEP";
	}else if($candidate_lang == "en"){
		$txt_reg_ty_1 			= "Thank you! Your information has been sent successfully.";
		$txt_prava 				= "All rights reserved";
		$txt_text_na_dnu 		= "JOBSTEP";
	}else if($candidate_lang == "sr"){
		$txt_reg_ty_1 			= "Hvala Vam! Vaši podaci su uspešno poslati.";
		$txt_prava 				= "Sva prava pridržana";
		$txt_text_na_dnu 		= "JOBSTEP";
	}else if($candidate_lang == "hr"){
		$txt_reg_ty_1 			= "Hvala Vam! Vaši podaci su uspješno poslani.";
		$txt_prava 				= "Sva prava pridržana";
		$txt_text_na_dnu 		= "JOBSTEP";
	}else if($candidate_lang == "bs"){
		$txt_reg_ty_1 			= "Hvala Vam! Vaši podaci su uspješno poslani.";
		$txt_prava 				= "Sva prava pridržana";
		$txt_text_na_dnu 		= "JOBSTEP";
	}else{
		$txt_reg_ty_1 			= "Vielen Dank! Ihre Daten sind erfolgreich zugesendet.";
		$txt_prava	 			= "Alle Rechte vorbehalten";
		$txt_text_na_dnu 		= "JOBSTEP";
	}
	
	
?>

<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="description" content="">
		<meta name="author" content="">
		<title>Jobstep Thank You Page</title>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		
		<style>
			@import url("https://fonts.googleapis.com/css?family=Open+Sans:300,400,700&amp;subset=latin-ext");

			body {
				font-family: "Open Sans", sans-serif;
				padding-top: 30px;
			}
			@media screen and (min-height: 700px) {
				.height_row_1 {
					height: 25%;
				}
				.height_row_2 {
					height: 65%;
				}
				.height_row_3 {
					height: 10%;
				}
			}
			@media screen and (max-height: 700px) { 
				.height_row_1 {
					min-height: 175px;
				}
				.height_row_2 {
					min-height: 455px;
				}
				.height_row_3 {
					min-height: 70px;
				}
			}
			.img_width {
				width: 30%;
			}
			@media screen and (max-width: 600px) {
				.img_width {
					width: 175px;
				}
			}
			.color-text {
				color: #5b5b5b !important;
			}
		</style>
	</head>
	<body>
		<div class="container">
			<div class="row height_row_1">
				<div class="col-xs-12 d-flex align-items-center justify-content-center">
					<div>
						<div class="row gy-3">
							<div class="col-xs-12 text-center">
								<img class="img-responsive img_width" src="https://crm.job-step.com/images/Jobstep_logo_new.png" alt="">
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row height_row_2">
				<div class="col-xs-12 d-flex align-items-center justify-content-center">
					<div>
						<div class="row gy-3">
							<div class="col-xs-12 text-center">
								<img class="img-responsive" width="40" height="40" src="https://crm.job-step.com/images/thankyoupage/uspjesno.png" alt="">
							</div>
							<div class="col-xs-12 text-center">
								<p class="h6 color-text"><?php echo $txt_reg_ty_1; ?></p>
							</div>
						</div>
					</div>
				</div>
			</div>
			<footer class="row height_row_3">
				<div class="col-xs-12 d-flex align-items-center justify-content-center">
					<div>
						<div class="row gy-1">
							<hr class="my-0">
							<div class="col-xs-12 text-center">
								<p class="h6 color-text"><b><?php echo $txt_text_na_dnu; ?></b></p>
							</div>
							<div class="col-xs-12 text-center">
								<p class="h6 color-text"><?php echo "© ". date("Y")  . " " .$txt_prava. " - Jobstep IT Solutions"; ?></p>
							</div>
						</div>
					</div>
				</div>
			</footer>
		</div>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
	</body>
</html>
