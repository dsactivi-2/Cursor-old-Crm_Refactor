<?php 
	$origin = 4; //ANGACOM - u backend-u za novi broj naloga dodati novi if za varijablu $originDesc
	if(isset($_POST["jezikForme"])){
		$jezikForme = intval($_POST["jezikForme"]);
	}else{
		$jezikForme = 2;
	}
	
	$textForme = array(
		"Digitalni nalog" => array("Digitalni nalog", "Registration form", "Anmeldeformular"),
		"Popunite obrazac ispod" => array("Popunite obrazac ispod", "Fill out the form below", "Füllen Sie das folgende Formular aus"),
		"Osnovne informacije" => array("Osnovne informacije", "Personal information", "Allgemeine Informationen"),
		"Molimo vas unesite sve neophodne podatke" => array("Molimo vas unesite sve neophodne podatke", "Please enter all necessary information", "Bitte tragen Sie alle wichtigen Informationen ein"),
		"Ime" => array("Ime", "Name", "Vorname"),
		"Prezime" => array("Prezime", "Surname", "Nachname"),
		"E-mail adresa" => array("E-mail adresa", "E-mail address", "E-Mail Adresse"),
		"Vaša mail adresa" => array("Vaša mail adresa", "Your e-mail address", "Ihre E-mail Adresse"),
		"Kontakt broj" => array("Kontakt broj", "Contact number", "Kontaktnummer"),
		"Informacije o kompaniji" => array("Informacije o kompaniji", "Company information", "Informationen zum Unternehmen"),
		"Ime kompanije" => array("Ime kompanije", "Company name", "Name"),
		"Adresa" => array("Adresa", "Address", "Adresse"),
		"Poštanski broj" => array("Poštanski broj", "Postcode", "PLZ"),
		"Mjesto" => array("Mjesto", "Location", "Ort"),
		"Broj zaposlenih" => array("Broj zaposlenih", "Number of employees", "Mitarbeiteranzahl"),
		"Potrebno radnika" => array("Potrebno radnika", "Personalbedarf	Staffing requirements", "Personalbedarf"),
		"Za koje pozicije su vam potrebni zaposleni?" => array("Za koje pozicije su vam potrebni zaposleni?", "For which positions do you need employees?", "Für welche Positionen benötigen Sie Mitarbeiter?"),
		"Servisni tehničari" => array("Servisni tehničari", "Service engineer", "Servicetechniker"),
		"Kvalifikovani građevinski radnici" => array("Kvalifikovani građevinski radnici", "Skilled workers in civil engineering", "Facharbeiter im Tiefbau"),
		"Ostalo" => array("Ostalo", "Other", "Sonstige"),
		"Ostale pozicije zaposlenih" => array("Ostale pozicije zaposlenih", "Other employee positions", "Andere Mitarbeiterpositionen"),
		"Odaberite" => array("Odaberite", "Choose", "Wählen"),
		"Vaša prijava je uspješno ispunjena" => array("Vaša prijava je uspješno ispunjena", "Your application has been successfully completed", "Ihre Anmeldung war erfolgreich"),
		"Bosanski" => array("Bosanski", "Bosnian", "Bosnisch"),
		"Engleski" => array("Engleski", "English", "Englisch"),
		"Njemački" => array("Njemački", "German", "Deutsch"),
		"Dalje" => array("Dalje", "Next", "Weiter"),
		"Unesite ovdje" => array("Unesite ovdje", "Enter here", "Hier eintragen"),
		"Pogrešan format e-mail adrese. Primjer: example@example.com" => array("Pogrešan format e-mail adrese. Primjer: example@example.com", "Invalid email format. Example: example@example.com", "Ungültiges Email-Format. Beispiel: beispiel@beispiel.com"),
		"Sva prava pridržana - Jobstep IT Solutions" => array("Sva prava pridržana - Jobstep IT Solutions", "All rights reserved - Jobstep IT Solutions", "Alle Rechte vorbehalten - Jobstep IT Solutions"),
		"Nastavkom prihvatate uslove o zaštiti privatnosti." => array("Nastavkom prihvatate uslove o zaštiti privatnosti.", "By continuing, you accept the privacy policy.", "Bei Fortsetzung akzeptieren Sie die Datenschutz-Bedingungen (DSGVO)."),
		"Pogledaj izjavu o zaštiti privatnosti" => array("Pogledaj izjavu o zaštiti privatnosti", "See the privacy statement", "Klicken Sie hier, um die Einverständniserklärung zu sehen"),
		"Izjava o zaštiti privatnosti" => array("Izjava o zaštiti privatnosti", "Privacy Statement", "Datenschutzerklärung"),
		"Slanjem ovog obrasca dajete svoju saglasnost da Vaši podaci budu registrovani u našoj bazi podataka. Jobstep je obavezan čuvati privatnost svojih klijenata i osigurati visok stepen sigurnosti i povjerljivosti ličnih podataka prikupljenih od potencijalnih klijenata u skladu sa Zakonom o zaštiti ličnih podataka i podzakonskim aktima. Lični i svi drugi podaci iz ovog obrasca koje klijent unese u obrascu ili u bilo kojoj drugoj formi, biće korišteni isključivo od strane ovlaštenih lica u procesu potrebnih administrativnih radnji i posredovanju prilikom zapošljavanja." => array("Slanjem ovog obrasca dajete svoju saglasnost da Vaši podaci budu registrovani u našoj bazi podataka. Jobstep je obavezan čuvati privatnost svojih klijenata i osigurati visok stepen sigurnosti i povjerljivosti ličnih podataka prikupljenih od potencijalnih klijenata u skladu sa Zakonom o zaštiti ličnih podataka i podzakonskim aktima. Lični i svi drugi podaci iz ovog obrasca koje klijent unese u obrascu ili u bilo kojoj drugoj formi, biće korišteni isključivo od strane ovlaštenih lica u procesu potrebnih administrativnih radnji i posredovanju prilikom zapošljavanja.", "By submitting this form, you give your consent for your data to be registered in our database. Jobstep is obliged to protect the privacy of its clients and ensure a high degree of security and confidentiality of personal data collected from potential clients in accordance with the Personal Data Protection Act and bylaws. Personal and all other information from this form will be used exclusively by authorized personnel in the process of necessary administrative actions and mediation in employment.", "Mit dem Absenden dieses Formulars stimmen Sie der Registrierung Ihrer Daten in unserer Datenbank zu. Jobstep ist verpflichtet, die Privatsphäre seiner Kunden zu schützen und ein hohes Maß an Sicherheit und Vertraulichkeit der von potenziellen Kunden erhobenen personenbezogenen Daten gemäß dem Gesetz zum Schutz personenbezogener Daten und seiner Satzung zu gewährleisten. Persönliche und alle anderen Daten aus diesem Formular, die der Kunde in das Formular oder in sonstiger Form eingibt, werden ausschließlich von autorisierten Personen im Rahmen der erforderlichen Verwaltungshandlungen und der Vermittlung in Beschäftigung verwendet."),
	);
?>

<!doctype html>
<html class="h-100">
	<head>
		<!-- 
		Required meta tags START ++++++++++++++++++++++++++++++++++++++++++++
		-->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- 
		Required meta tags END ++++++++++++++++++++++++++++++++++++++++++++++ 
		-->
		
		<!-- 
		CSS START +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		-->
			<link href="https://crm.job-step.com/jobstep_pp/css/bootstrap.min.css" rel="stylesheet">
			<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta2/dist/css/bootstrap-select.min.css">
			<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
			<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha256-eZrrJcwDc/3uDhsdt61sL2oOBY362qM3lon1gyExkL0=" crossorigin="anonymous" />
			<link rel="stylesheet" type="text/css" href="https://crm.job-step.com/css/intlTelInput.css">
		<!-- 
		CSS END   +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		-->

		<!-- 
		Titile Jobstep START ++++++++++++++++++++++++++++++++++++++++++++++++
		-->
			<title>Jobstep</title>
			<link rel="icon" href="https://crm.job-step.com/images/Jobstep-logo_news.png">
		<!-- 
		Titile Jobstep END ++++++++++++++++++++++++++++++++++++++++++++++++++
		-->
		
		<!-- 
		Script START ++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		-->
		<script src="https://crm.job-step.com/jobstep_pp/javascript/bootstrap.bundle.js"></script>
		<script src="https://crm.job-step.com/jobstep_pp/javascript/jquery.js"></script>
		<script src="https://crm.job-step.com/jobstep_pp/javascript/jquery-ui.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta2/dist/js/bootstrap-select.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
		<script src="https://crm.job-step.com/js/intlTelInput.js"></script>
		<script src="https://crm.job-step.com/js/intlTelInput-jquery.min.js"></script>
		<!-- 
		Script END ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		-->
		
		<!-- 
		MyCSS Jobstep START +++++++++++++++++++++++++++++++++++++++++++++++++
		-->
		<style>
			/* +++++++++++++++++++++++++++++++++++ LOADER START +++++++++++++++++++++++++++++++++++ */
			.lds-dual-ring_big {
				display: inline-block;
				width: 120px;
				height: 120px;
				position: fixed;
				top: 50%;
				left: 50%;
				margin-top: -60px;
				margin-left: -60px;
			}
			.lds-dual-ring_big:after {
				content: " ";
				display: block;
				width: 120px;
				height: 120px;
				margin: 8px;
				border-radius: 50%;
				border: 12px solid #FFFFFF;
				border-color: #FFFFFF transparent #FFFFFF transparent;
				animation: lds-dual-ring_big 1.2s linear infinite;
			}
			@keyframes lds-dual-ring_big {
				0% {
					transform: rotate(0deg);
				}
				100% {
					transform: rotate(360deg);
				}
			}
			#page-cover {
				display: none;
				position: fixed;
				width: 100%;
				height: 100%;
				/*background-color: #000;*/
				background: #6097A0;
				z-index: 999;
				top: 0;
				left: 0;
			}
			/* ++++++++++++++++++++++++++++++++++++ LOADER END ++++++++++++++++++++++++++++++++++++ */
			/* +++++++++++++++++++++++++++++++++++ OPĆENITO START +++++++++++++++++++++++++++++++++++ */
			@import url('https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;500;600;700&display=swap');
			body{
				/*font-family: 'Inter', sans-serif;*/
				background: linear-gradient(89.82deg, #57828D 0.17%, rgba(96, 151, 160, 0.65) 99.86%);
				overflow-y: hidden;
			}
			.card{
				background: linear-gradient(90deg, rgba(255, 255, 255, 0.11) 1.63%, rgba(255, 255, 255, 0.1) 100%);
				border: 2px solid rgba(255, 255, 255, 0.35);
				box-sizing: border-box;
				backdrop-filter: blur(5px);
				/* Note: backdrop-filter has minimal browser support */
				border-radius: 30px;
			}
			.scrollbar::-webkit-scrollbar-track {
				background-color: darkgrey;
			}
			.scrollbar::-webkit-scrollbar-thumb {
				box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
			}
			.scrollbar::-webkit-scrollbar {
				width: 5px;
			}
			.scrollbar{
				scroll-behavior: smooth;
			}
			p{
				margin-top: unset !important;
				margin-bottom: unset !important;
			}
			form .form-label{
				/*font-family: 'Inter';*/
				font-style: normal;
				font-weight: 400;
				color: #FFFFFF;
			}
			form .styleInput{
				background: linear-gradient(90deg, rgba(255, 255, 255, 0.4) 1.63%, rgba(255, 255, 255, 0.2) 100%);
				border: 1px solid rgba(255, 255, 255, 0.4);
				box-sizing: border-box;
				backdrop-filter: blur(50px);
				border-radius: 5px;
			}
			form .styleInput::placeholder{
				font-style: normal;
				font-weight: 400;
				font-size: 15px;
				color: rgba(0, 0, 0, 0.25);
			}
			.bootstrap-select>.dropdown-toggle.bs-placeholder{
				font-style: normal;
				font-weight: 400;
				font-size: 15px;
				color: rgba(0, 0, 0, 0.25);
			}
			.bootstrap-select>.btn-light {
				background: linear-gradient(90deg, rgba(255, 255, 255, 0.4) 1.63%, rgba(255, 255, 255, 0.2) 100%);
				border: 1px solid rgba(255, 255, 255, 0.4);
				box-sizing: border-box;
				backdrop-filter: blur(50px);
			}
			form .dugme{
				background: #6097A0;
				border-radius: 20px;
				font-style: normal;
				font-weight: 600;
				color: #FFFFFF;
			}
			form .dugme:active{
				color: #FFFFFF;
			}
			form .dugme:hover{
				color: #FFFFFF;
			}
			.iti {
				display: block !important;
			}
			.iti--separate-dial-code .iti__selected-flag {
				background-color: #6097A0 !important;
				border-radius: 5px;
			}
			.iti--allow-dropdown .iti__flag-container:hover .iti__selected-flag {
				background-color: #6097a080 !important;
			}
			.iti--separate-dial-code .iti__selected-dial-code {
				color: #FFFFFF;
			}
			.ellipse1{
				position: absolute;
				width: 15%;
				left: 1%;
				top: -2%;
				transform: rotate(-34.46deg);
			}
			.ellipse2{
				position: absolute;
				width: 15%;
				left: 2%;
				bottom: -3%;
				transform: rotate(-34.46deg);
			}
			.ellipse3{
				position: absolute;
				width: 15%;
				right: 4%;
				top: 1%;
				/*transform: rotate(-34.46deg);*/
			}
			.ellipse4{
				position: absolute;
				right: 19%;
				width:12%;
				top: 5%;
				/*transform: rotate(-34.46deg);*/
			}
			.logoBack{
				position: absolute;
				right: 1%;
				width:15%;
				bottom: 5%;
			}
			form .position-relative .progress{
				background-color: unset;
			}
			form .position-relative .progress .progress-bar{
				background: linear-gradient(90deg, rgba(255, 255, 255, 0.32) 1.63%, rgba(255, 255, 255, 0.16) 100%);
				border: 1px solid rgba(255, 255, 255, 0.4);
				backdrop-filter: blur(50px);
				border-radius: 5px;
				box-sizing: border-box;
			}
			.btn-number-format-active:hover{
				color: #FFFFFF;
			}
			.btn-number-format-deactive:hover{
				color: #73a2aa;
			}
			.btn-number-format-active{
				background: linear-gradient(0deg, rgba(115,162,170,0.4) 0%, rgba(115,162,170,0.8) 30%, rgba(115,162,170,1) 50%, rgba(115,162,170,0.8) 70%, rgba(115,162,170,0.4) 100%);
				border: 1px solid rgba(255, 255, 255, 0.4);
				box-sizing: border-box;
				backdrop-filter: blur(50px);
				/*font-family: 'Inter';*/
				font-style: normal;
				font-weight: 700;
				color: #FFFFFF;
			}
			.btn-number-format-deactive{
				background: linear-gradient(0deg, rgba(255,255,255,0.4) 0%, rgba(255,255,255,0.8) 30%, rgba(255,255,255,1) 50%, rgba(255,255,255,0.8) 70%, rgba(255,255,255,0.4) 100%);
				border: 1px solid rgba(255, 255, 255, 0.4);
				box-sizing: border-box;
				backdrop-filter: blur(50px);
				/*font-family: 'Inter';*/
				font-style: normal;
				font-weight: 700;
				color: #73a2aa;
			}
			.btn.disabled, .btn:disabled, fieldset:disabled .btn {
				pointer-events: none;
				opacity: 1;
			}
			/* Chrome, Safari, Edge, Opera */
			input::-webkit-outer-spin-button,
			input::-webkit-inner-spin-button {
				-webkit-appearance: none;
				margin: 0;
			}

			/* Firefox */
			input[type=number] {
				-moz-appearance: textfield;
			}
			.form-control{
				background-color: #ff000000;
			}
			.btn:focus {
				outline: 0;
				box-shadow: unset;
			}
			.langActive {
				-webkit-box-shadow: 0px 0px 20px 10px #ffffff; 
				box-shadow: 0px 0px 20px 10px #ffffff;
				border-radius: 50%;
			}
			.textAlignMobile1{
				text-align: left;
			}
			.textAlignMobile2{
				text-align: right;
			}
			@media (max-width: 992px) {
				.textAlignMobile1{
					text-align: center!important;
				}
				.textAlignMobile2{
					text-align: center!important;
					margin-bottom: 15px;
					margin-top: 10px;
				}
			}
			/* ++++++++++++++++++++++++++++++++++++ OPĆENITO END ++++++++++++++++++++++++++++++++++++ */
			
			
			/* ++++++++++++++++++++++++++++++++++++ Step 1 START ++++++++++++++++++++++++++++++++++++ */
			form .icon{
				color: #FFFFFF;
			}
			.naslov{
				font-style: normal;
				font-weight: 500;
				color: #FFFFFF;
			}
			.podnaslov{
				font-style: normal;
				font-weight: 400;
				color: #FFFFFF;
			}
			form .form-text{
				font-style: normal;
				font-weight: 400;
				color: #FFFFFF;
				background-color: #a52a2a;
				border-radius: 15px;
				padding: 10px;
			}
			/* +++++++++++++++++++++++++++++++++++++ Step 1 END +++++++++++++++++++++++++++++++++++++ */
		</style>
		<script>
			function getLoaderBig(){
				$("#page-cover").css('z-index', 2000);
				$("#page-cover").css("opacity",1).fadeIn(200, function () {          
					$('.lds-dual-ring_big').css({'z-index':9999}).fadeIn();;
				});
			}
			function removeLoader(){
				$("#page-cover").fadeOut(200, function(){
					$(".lds-dual-ring_big").fadeOut(500);
					$("#page-cover").css("opacity",0.0);
					$("#page-cover").css('z-index', -1);
					
				})
			}
			function validateEmail(email) {
			  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
			  return regex.test(email);
			}
			$(document).ready(function(){
				getLoaderBig();
				setTimeout(function() {
					removeLoader();
				}, 1500);
			});
		</script>
		<!-- 
		MyCSS Jobstep END  ++++++++++++++++++++++++++++++++++++++++++++++++++
		-->
	</head>
	<body class = "d-flex flex-column h-100">
		<img src="images/digitalni_nalog_images/Ellipse1.svg" class="ellipse1" alt=" ">
		<img src="images/digitalni_nalog_images/Ellipse1.svg" class="ellipse2" alt=" ">
		<img src="images/digitalni_nalog_images/Ellipse1.svg" class="ellipse3" alt=" ">
		<img src="images/digitalni_nalog_images/Ellipse2.svg" class="ellipse4" alt=" ">
		<img src="images/digitalni_nalog_images/bijeli_vektorski_logo_Jobstepa_1.png" class="logoBack" alt=" ">
		<!-- Loader START -->
		<div id="page-cover"></div>
		<div class="lds-dual-ring_big" style="display:none;"></div>
		<script>
			// $(document).ready(function(){
				// $('.step2').hide();
				// $('#emailHelp').hide();
				// getLoaderBig();
				// setTimeout(function() {
					// removeLoader();
				// }, 1000);
			// });
		</script>
		<!-- Loader END -->
		<div class="container-fluid justify-content-center d-flex h-100 scrollbar overflow-auto">
			<div class = "row align-items-center w-100">
				<div class = "col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
					<div class="container my-5">
						<!-- 
						**************************************************************************
						Content START 
						**************************************************************************
						-->
						
						<div class="card py-4 px-2">
							<div class="card-body">
								<div class="row">
									<div class="col-lg-10 offset-lg-1">
										<div class="row pb-3 step1">
											<div class="col-lg-6 textAlignMobile1">
												<p class="naslov fs-2"><?php echo $textForme["Digitalni nalog"][$jezikForme]; ?></p>
											</div>
											<div class="col-lg-6 textAlignMobile2">
												<form method="POST" action="">
													<div class="btn-group" role="group" aria-label="Basic example"> 
														<!--<button class="btn" type="submit" value="0" name="jezikForme"><img class="<?php //echo (($jezikForme == 0) ? 'langActive' : ''); ?>" src="https://crm.job-step.com/images/digitalni_nalog_images/bih.png" height="32" width="32"></button>-->
														<button class="btn" type="submit" value="1" name="jezikForme"><img class="<?php echo (($jezikForme == 1) ? 'langActive' : ''); ?>" src="https://crm.job-step.com/images/digitalni_nalog_images/uk.png" height="32" width="32"></button>
														<button class="btn" type="submit" value="2" name="jezikForme"><img class="<?php echo (($jezikForme == 2) ? 'langActive' : ''); ?>" src="https://crm.job-step.com/images/digitalni_nalog_images/de.png" height="32" width="32"></button>
													</div>
												</form>
											</div>
											<div class="col-lg-12">
												<p class="podnaslov fs-6"><?php echo $textForme["Popunite obrazac ispod"][$jezikForme]; ?></p>
											</div>
										</div>
										<div class="row pt-3 pb-3 step2" style = "display:none;">
											<div class="col-lg-12">
												<p class="naslov fs-2"><i class="fa fa-briefcase icon2 me-2" aria-hidden="true"></i><?php echo $textForme["Informacije o kompaniji"][$jezikForme]; ?></p>
											</div>
										</div>
										<form autocomplete="off">
											<div class="row pb-3">
												<div class="col-lg-12">
													<div class="position-relative m-4">
														<div class="progress" style="height: 1px;">
															<div class="progress-bar" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
														</div>
														<button type="button" class="btn1Step position-absolute top-0 start-0 translate-middle btn btn-sm rounded-pill btn-number-format-active fs-4" style="width: 3rem; height:3rem;" disabled>1</button>
														<button type="button" class="btn2Step position-absolute top-0 start-50 translate-middle btn btn-sm rounded-pill btn-number-format-deactive fs-4" style="width: 3rem; height:3rem;" disabled>2</button>
														<button type="button" class="btn3Step position-absolute top-0 start-100 translate-middle btn btn-sm rounded-pill btn-number-format-deactive fs-4" style="width: 3rem; height:3rem;" disabled>3</button>
													</div>
												</div>
											</div>
											
											<div class="row pt-3 pb-3 step1">
												<div class="col-lg-12">
													<p class="naslov fs-2"><i class="fa fa-user-circle-o icon2 me-2" aria-hidden="true"></i><?php echo $textForme["Osnovne informacije"][$jezikForme]; ?></p>
												</div>
												<div class="col-lg-12">
													<p class="podnaslov fs-6"><?php echo $textForme["Molimo vas unesite sve neophodne podatke"][$jezikForme]; ?></p>
												</div>
											</div>
											
											<div class="mb-3 step1">
												<div class="row">
													<div class="col-lg-6">
														<label for="ime" class="form-label"><?php echo $textForme["Ime"][$jezikForme]; ?>*:</label>
														<input type="text" class="form-control styleInput" id="ime" placeholder="<?php echo $textForme["Ime"][$jezikForme]; ?>">
													</div>
													<div class="col-lg-6">
														<label for="prezime" class="form-label"><?php echo $textForme["Prezime"][$jezikForme]; ?>*:</label>
														<input type="text" class="form-control styleInput" id="prezime" placeholder="<?php echo $textForme["Prezime"][$jezikForme]; ?>">
													</div>
												</div>
											</div>
											<div class="mb-3 step1">
												<div class="row">
													<div class="col-lg-12">
														<label for="email" class="form-label"><?php echo $textForme["E-mail adresa"][$jezikForme]; ?>*:</label>
														<input type="email" class="form-control styleInput" id="email" placeholder="<?php echo $textForme["Vaša mail adresa"][$jezikForme]; ?>">
														<div id="emailHelp" class="form-text" style = "display:none;"><?php echo $textForme["Pogrešan format e-mail adrese. Primjer: example@example.com"][$jezikForme]; ?></div>
													</div>
												</div>
											</div>
											<div class="mb-3 step1">
												<div class="row">
													<div class="col-lg-12">
														<label for="telefon" class="form-label"><?php echo $textForme["Kontakt broj"][$jezikForme]; ?>*:</label>
														<input type="tel" class="form-control styleInput" id="telefon">
													</div>
												</div>
											</div>
											<div class="mt-3 mb-3 step1">
												<div class="row">
													<div class="col-lg-12 text-end">
														<button type="button" class="btn btn-lg dugme px-5 btn-step1"><?php echo $textForme["Dalje"][$jezikForme]; ?></button>
													</div>
												</div>
											</div>
											<div class="mb-3 step2" style = "display:none;">
												<div class="row">
													<div class="col-lg-12">
														<label for="ime_kompanije" class="form-label"><?php echo $textForme["Ime kompanije"][$jezikForme]; ?>*:</label>
														<input type="text" class="form-control styleInput" id="ime_kompanije" placeholder="<?php echo $textForme["Ime kompanije"][$jezikForme]; ?>">
													</div>
												</div>
											</div>
											<div class="mb-3 step2" style = "display:none;">
												<div class="row">
													<div class="col-lg-4">
														<label for="adresa_kompanije" class="form-label"><?php echo $textForme["Adresa"][$jezikForme]; ?>*:</label>
														<input type="text" class="form-control styleInput" id="adresa_kompanije" placeholder="<?php echo $textForme["Unesite ovdje"][$jezikForme]; ?>">
													</div>
													<div class="col-lg-4">
														<label for="postanski_broj_kompanije" class="form-label"><?php echo $textForme["Poštanski broj"][$jezikForme]; ?>*:</label>
														<input type="number" class="form-control styleInput" id="postanski_broj_kompanije" placeholder="<?php echo $textForme["Unesite ovdje"][$jezikForme]; ?>">
													</div>
													<div class="col-lg-4">
														<label for="mjesto_kompanije" class="form-label"><?php echo $textForme["Mjesto"][$jezikForme]; ?>*:</label>
														<input type="text" class="form-control styleInput" id="mjesto_kompanije" placeholder="<?php echo $textForme["Unesite ovdje"][$jezikForme]; ?>">
													</div>
												</div>
											</div>
											<div class="mb-3 step2" style = "display:none;">
												<div class="row">
													<div class="col-lg-12">
														<label class="form-label"><?php echo $textForme["Broj zaposlenih"][$jezikForme]; ?>*:</label>
														<div class="brojZaposlenihEffect">
															<select class="selectpicker form-control" id="broj_zaposlenih" title="<?php echo $textForme["Odaberite"][$jezikForme]; ?>">
																<option value="0-20">0-20</option>
																<option value="20-40">20-40</option>
																<option value="40-60">40-60</option>
																<option value="60+">60+</option>
															</select>
														</div>
													</div>
												</div>
											</div>
											<div class="mb-3 step2" style = "display:none;">
												<div class="row">
													<div class="col-lg-12">
														<label class="form-label"><?php echo $textForme["Potrebno radnika"][$jezikForme]; ?>*:</label>
														<div class="potrebnoRadnikaEffect">
															<select class="selectpicker form-control" id="potrebno_radnika" title="<?php echo $textForme["Odaberite"][$jezikForme]; ?>">
																<option value="0-20">0-20</option>
																<option value="20-40">20-40</option>
																<option value="40-60">40-60</option>
																<option value="60+">60+</option>
															</select>
														</div>
													</div>
												</div>
											</div>
											<div class="mb-3 step2" style = "display:none;">
												<div class="row">
													<div class="col-lg-12">
														<label class="form-label"><?php echo $textForme["Za koje pozicije su vam potrebni zaposleni?"][$jezikForme]; ?>*:</label>
														<div class="pozicijeRadnikaEffect">
															<select class="selectpicker form-control" id="pozicije_radnika" title="<?php echo $textForme["Odaberite"][$jezikForme]; ?>" multiple>
																<option value="Servisni tehničari"><?php echo $textForme["Servisni tehničari"][$jezikForme]; ?></option>
																<option value="Kvalifikovani građevinski radnici"><?php echo $textForme["Kvalifikovani građevinski radnici"][$jezikForme]; ?></option>
																<option value="Ostalo"><?php echo $textForme["Ostalo"][$jezikForme]; ?></option>
															</select>
														</div>
													</div>
												</div>
											</div>
											<script>
												$("#pozicije_radnika").on("change", function(){
													var pozicija = $("#pozicije_radnika").val();
													if (jQuery.inArray('Ostalo', $("#pozicije_radnika").val()) != -1){
														//alert("nalazi se u nizu");
														$('#pozicijaSH').show('blind', 500);
													}
													if(jQuery.inArray('Ostalo', $("#pozicije_radnika").val()) == -1){
														//alert("ne nalazi se u nizu");
														$('#pozicijaSH').hide('blind', 500);
														$('#pozicije_radnika_ostalo').val(null);
													}
												});
											</script>
											<div class="mb-3" style = "display:none;" id="pozicijaSH">
												<div class="row">
													<div class="col-lg-12">
														<label for="pozicije_radnika_ostalo" class="form-label"><?php echo $textForme["Ostale pozicije zaposlenih"][$jezikForme]; ?>*:</label>
														<input type="text" class="form-control styleInput" id="pozicije_radnika_ostalo" placeholder="<?php echo $textForme["Unesite ovdje"][$jezikForme]; ?>">
													</div>
												</div>
											</div>
											<div class="mt-3 mb-3 step2" style = "display:none;">
												<div class="row">
													<div class="col-lg-12 text-end">
														<button type="button" class="btn btn-lg dugme px-5 btn-step2"><?php echo $textForme["Dalje"][$jezikForme]; ?></button>
													</div>
												</div>
											</div>
											<div class="row my-5 pt-3 pb-3 step3" style = "display:none;">
												<div class="col-lg-12">
													<p class="naslov fs-1 text-center">
														<?php echo $textForme["Vaša prijava je uspješno ispunjena"][$jezikForme]; ?>
													</p>
												</div>
											</div>
											<div class="row">
												<div class="col-lg-12 text-center">
													<p class="podnaslov fs-6"><?php echo $textForme["Nastavkom prihvatate uslove o zaštiti privatnosti."][$jezikForme]; ?></p>
												</div>
												<div class="col-lg-12 text-center">
													<button type="button" class="btn btn-link" data-bs-toggle="modal" data-bs-target="#izjavaModal">
														<?php echo $textForme["Pogledaj izjavu o zaštiti privatnosti"][$jezikForme]; ?>
													</button>
												</div>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
						
						<!-- 
						**************************************************************************
						Content END 
						**************************************************************************
						-->
						
						<!-- 
						**************************************************************************
						script handle START 
						**************************************************************************
						-->
						<script>
							$(document).ready(function($) {
							//$( window ).on( "load", function($){
								$.each($('#telefon'),function(){
									var telInput = $(this);
									if ($(this).val().startsWith("+") || $(this).val() == '') {
										$(telInput).intlTelInput({
											utilsScript:'https://intl-tel-input.com/node_modules/intl-tel-input/build/js/utils.js',
											autoPlaceholder: "aggressive",
											initialCountry: "de",
											formatOnDisplay: true,
											preferredCountries: ["ba","rs","hr","de"],
											separateDialCode: true
										});
									}
								});
							});
							$(".btn-step1").on("click", function(){
								var imePoslodavac = $("#ime").val();
								var prezimePoslodavac = $("#prezime").val();
								var emailPoslodavac = $("#email").val();
								var telefonPoslodavac = $("#telefon").val();
								
								var flagStep1 = 1; //Zastavica za validaciju forme 1 - ispunjeno sve 0 - nije ispunjeno
								
								if(imePoslodavac == ""){
									flagStep1 = 0;
									$('#ime').effect('bounce', 1000);
								}
								if(prezimePoslodavac == ""){
									flagStep1 = 0;
									$('#prezime').effect('bounce', 1000);
								}
								if(emailPoslodavac == ""){
									flagStep1 = 0;
									$('#email').effect('bounce', 1000);
								}else{
									if(validateEmail(emailPoslodavac) == false){
										flagStep1 = 0;
										$('#email').effect('bounce', 1000, function(){
											$('#emailHelp').show('blind', 500, function(){
												setTimeout(function() {
													$('#emailHelp').hide('blind', 500);
												}, 2000);
											});
										});
									}
								}
								if(telefonPoslodavac == ""){
									flagStep1 = 0;
									$('#telefon').effect('bounce', 1000);
								}
								
								if(flagStep1 == 1){
									//alert("Ime: "+imePoslodavac+" Prezime: "+prezimePoslodavac+" Email: "+emailPoslodavac+" Telefon: "+telefonPoslodavac);
									getLoaderBig();
									$('.step1').hide('fade', 500, function(){
										$( ".btn1Step" ).removeClass( "btn-number-format-active", function(){
											$( ".btn1Step" ).addClass("btn-number-format-deactive", function(){
												$( ".btn2Step" ).removeClass( "btn-number-format-deactive", function(){
													$( ".btn2Step" ).addClass("btn-number-format-active", function(){
														$('.step2').show('fade', 500, function(){
															$('#broj_zaposlenih').selectpicker();
															$('#potrebno_radnika').selectpicker();
															$('#pozicije_radnika').selectpicker();
															removeLoader();
														});
													});
												});
											});
										});
									});
								}
							});
							
							$(".btn-step2").on("click", function(){
								var origin = parseInt('<?php echo $origin; ?>'); 
								var language = parseInt('<?php echo $jezikForme; ?>'); 
								var nazivKompanije = $('#ime_kompanije').val();
								var adresaKompanije = $('#adresa_kompanije').val();
								var postanskiBrojKompanije = $('#postanski_broj_kompanije').val();
								var mjestoKompanije = $('#mjesto_kompanije').val();
								var brojZaposlenih = $('#broj_zaposlenih').val();
								var potrebnoRadnika = $('#potrebno_radnika').val();
								var pazicijeRadnika = $('#pozicije_radnika').val();
								var pozicijeRadnikaOstalo = $('#pozicije_radnika_ostalo').val();
								
								var flagStep12 = 1; //Zastavica za validaciju forme 12 - ispunjeno sve 0 - nije ispunjeno
								
								if(nazivKompanije == ""){
									flagStep12 = 0;
									$('#ime_kompanije').effect('bounce', 1000);
								}
								if(adresaKompanije == ""){
									flagStep12 = 0;
									$('#adresa_kompanije').effect('bounce', 1000);
								}
								if(postanskiBrojKompanije == ""){
									flagStep12 = 0;
									$('#postanski_broj_kompanije').effect('bounce', 1000);
								}
								if(mjestoKompanije == ""){
									flagStep12 = 0;
									$('#mjesto_kompanije').effect('bounce', 1000);
								}
								if(brojZaposlenih == ""){
									flagStep12 = 0;
									$('.brojZaposlenihEffect').effect('bounce', 1000);
								}
								if(potrebnoRadnika == ""){
									flagStep12 = 0;
									$('.potrebnoRadnikaEffect').effect('bounce', 1000);
								}
								if(pazicijeRadnika == "" && pazicijeRadnika.length == 0){
									flagStep12 = 0;
									$('.pozicijeRadnikaEffect').effect('bounce', 1000);
								}
								if(jQuery.inArray('Ostalo', $('#pozicije_radnika').val()) != -1 && pozicijeRadnikaOstalo == ""){
									flagStep12 = 0;
									$('#pozicije_radnika_ostalo').effect('bounce', 1000);
								}
								// var imePoslodavac = $("#ime").val();
								// var prezimePoslodavac = $("#prezime").val();
								// var emailPoslodavac = $("#email").val();
								// var telefonPoslodavac = $("#telefon").val();
								// alert("Ime: "+imePoslodavac+" Prezime: "+prezimePoslodavac+" Email: "+emailPoslodavac+" Telefon: "+telefonPoslodavac);
								// alert("Ime: "+nazivKompanije+" Adresa: "+adresaKompanije+" Postanski broj: "+postanskiBrojKompanije+" Mjesto kompanije: "+mjestoKompanije+" Broj zaposlenih: "+brojZaposlenih+" Potrebno radnika" + potrebnoRadnika+" Pozicije radnika" + pazicijeRadnika + " Ostalo "+pozicijeRadnikaOstalo);
								
								if(flagStep12 == 1){
									//alert("Ime: "+nazivKompanije+" Opis: "+opisPoslaKompanije+" telefon: "+telefonKompanije+" broj zaposlenih: "+brojZaposlenih+" struke: "+zanimajuStruke+" Duzina struke" + zanimajuStruke.length);
									getLoaderBig();
									
									$.each($('#telefon'),function(){
										var telInput = $(this);	
										var telType = telInput.data('type');	
										telInput.val(telInput.intlTelInput("getNumber"));  
									});
									
									var imePoslodavac = $("#ime").val();
									var prezimePoslodavac = $("#prezime").val();
									var emailPoslodavac = $("#email").val();
									var telefonPoslodavac = $("#telefon").val();
									
									if(jQuery.inArray('Ostalo', $('#pozicije_radnika').val()) != -1){
										$('#pozicijaSH').hide();
									}
									
									// alert("Ime: "+imePoslodavac+" Prezime: "+prezimePoslodavac+" Email: "+emailPoslodavac+" Telefon: "+telefonPoslodavac);
									// alert("Ime: "+nazivKompanije+" Opis: "+opisPoslaKompanije+" telefon: "+telefonKompanije+" broj zaposlenih: "+brojZaposlenih+" struke: "+zanimajuStruke+" Duzina struke" + zanimajuStruke.length);
									
									
									$.ajax({
										url: 'digitalni_nalog_backend.php?page=completeDigitalniNalog',
										type: 'POST',
										data: {
											'origin':origin,
											'language':language,
											'imePoslodavac':imePoslodavac,
											'prezimePoslodavac':prezimePoslodavac,
											'emailPoslodavac':emailPoslodavac,
											'telefonPoslodavac':telefonPoslodavac,
											'nazivKompanije':nazivKompanije,
											'adresaKompanije':adresaKompanije,
											'postanskiBrojKompanije':postanskiBrojKompanije,
											'mjestoKompanije':mjestoKompanije,
											'brojZaposlenih':brojZaposlenih,
											'potrebnoRadnika':potrebnoRadnika,
											'pazicijeRadnika':pazicijeRadnika,
											'pozicijeRadnikaOstalo':pozicijeRadnikaOstalo
										},
										dataType: 'html',
										success: function(data) {
											
											if(data == 1){
												$('.step2').hide('fade', 500, function(){
													$( ".btn2Step" ).removeClass( "btn-number-format-active", function(){
														$( ".btn2Step" ).addClass("btn-number-format-deactive", function(){
															$( ".btn3Step" ).removeClass( "btn-number-format-deactive", function(){
																$( ".btn3Step" ).addClass("btn-number-format-active", function(){
																	$('.step3').show('fade', 500, function(){
																		removeLoader(); 
																	});
																});
															});
														});
													});
												});
											}
										},
										error: function (xhr, ajaxOptions, thrownError) {
											alert(xhr.status);
											alert(thrownError);
										}
									});
								}
							});
						</script>
						<!-- 
						**************************************************************************
						script handle END 
						**************************************************************************
						-->
						
						
						<!-- 
						**************************************************************************
						modal START 
						**************************************************************************
						-->
						<div class="modal fade" id="izjavaModal" data-bs-backdrop="false" data-bs-keyboard="false" tabindex="-1" aria-labelledby="izjavaModalLabel" aria-hidden="true" style = "background-color: rgb(149 185 191 / 50%);">
							<div class="modal-dialog modal-dialog-centered modal-lg">
								<div class="modal-content border-0">
									<div class="modal-header border-bottom-0 text-center">
										<h5 class="modal-title w-100" id="izjavaModalLabel"><?php echo $textForme["Izjava o zaštiti privatnosti"][$jezikForme]; ?></h5>
										<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
									</div>
									<div class="modal-body text-center">
										<div class="row pt-3 pb-3">
											<div class="col-lg-12">
												<p class="fs-6">
													<?php echo $textForme["Slanjem ovog obrasca dajete svoju saglasnost da Vaši podaci budu registrovani u našoj bazi podataka. Jobstep je obavezan čuvati privatnost svojih klijenata i osigurati visok stepen sigurnosti i povjerljivosti ličnih podataka prikupljenih od potencijalnih klijenata u skladu sa Zakonom o zaštiti ličnih podataka i podzakonskim aktima. Lični i svi drugi podaci iz ovog obrasca koje klijent unese u obrascu ili u bilo kojoj drugoj formi, biće korišteni isključivo od strane ovlaštenih lica u procesu potrebnih administrativnih radnji i posredovanju prilikom zapošljavanja."][$jezikForme]; ?>
												</p>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- 
						**************************************************************************
						modal END 
						**************************************************************************
						-->
					</div>
				</div>
			</div>
		</div>
		<footer class="footer mt-auto py-2">
			<div class="container text-center">
				<small>
					<span class="text-white">
						©<?php 
							echo date("Y")." ".$textForme["Sva prava pridržana - Jobstep IT Solutions"][$jezikForme];
						?>
					</span>
				</small>
			</div>
		</footer>
	</body>
</html>