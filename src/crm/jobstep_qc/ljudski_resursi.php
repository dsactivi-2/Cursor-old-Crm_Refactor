<?php 
	include("includes/functions.php");
	
	$isLoggedIn = isLoggedIn();
	$page = "";
	if($isLoggedIn == 1){
		if(isset($_REQUEST["page"])) {
			$page = $_REQUEST["page"];
		}else{
			header("Location: /jobstep_qc/ljudski_resursi.php?page=case1");
		}
?>
<!doctype html>
<html lang="en" class="h-100">
	<head>
		<?php
			include("includes/head.php"); 
		?>
	<style>
		.dropdown-menu.show{
			display: contents!important;
		}
		.btn_menu{
			background-color: #57AFAB;
			color: #BAEC84;
			border-radius: 50%;
			padding:0.8rem;
		}
		.btn_menu:hover{
			background-color: #61C7A8;
			color: white;
		}
		.btn_menu_sm{
			background-color: #57AFAB;
			color: #BAEC84;
			border-radius: 50%;
			padding:0.6rem;
			font-size:3px;
			margin-top:15px;
		}
		.btn_menu_sm:hover{
			background-color: #61C7A8;
			color: white;
			font-size:3px;
		}
		.btn_menu_back{
			background-color: #57AFAB;
			color: #BAEC84;
		}
		.btn_menu_back:hover{
			background-color: #61C7A8;
			color: white;
		}
		.btn_positive_rnd{
			background-color: #769859;
			color: #BAEC84;
			border-radius: 50%;
			padding:0.8rem;
		}
		.btn_positive_rnd:hover{
			background-color: #61C7A8;
			color: white;
		}
		.naslov{
			color: #6097A0;
		}
		.card_positive_sm{
			margin-top:5px;
			margin-bottom:5px;
			margin-lef:0px;
			margin-right:0px;
			border-radius:20px;
		}
		.card_positive_sm>.card-header{
			font-size: 10px;
			background-color: #769859;
			color:white;

		}
		.card_positive_sm>.card-body{
			font-size:8px;
		}
		.card_negative_sm{
			margin-top:5px;
			margin-bottom:5px;
			margin-lef:0px;
			margin-right:0px;
			border-radius:20px;
		}
		.card_negative_sm>.card-header{
			font-size: 10px;
			background-color: #BB7C97;
			color:white;

		}
		.card_negative_sm>.card-body{
			font-size:8px;
		}
		.card{
			border-radius:20px;
			margin-top:20px;
		}

		.card_neutral{

		}
		.card_neutral>.card-header{
			background-color: #57AFAB;
			color: white;
			font-size: 20px;
			font-weight: bold;
		}
		.card_neutral>.card_body{
			
		}
		.card_neutral>.card_body>.card_title{
			
		}
		.card_negative{
			border-radius:20px;
		}
		.card_negative>.card-header{
			background-color: #BB7C97;
			color: white;
			font-size: 20px;
			font-weight: bold;
		}
		.card_negative>.card_body{
			
		}
		.card_negative>.card_body>.card_title{
			
		}
		.card_positive{
			border-radius:20px;

		}
		.card_positive>.card-header{
			background-color: #769859;
			color: white;
			font-size: 20px;
			font-weight: bold;
		}
		.card_positive>.card_body{
			
		}
		.card_positive>.card_body>.card_title{
			
		}
		.card_text{
			padding: 0rem;
		}
		.vacation_days_remaning{
			background-color:#BAEC84;
			color:black;
			font-weight:bold;
			border-radius:25px;
			padding:1rem;
		}
		.form_field{
			margin-top:20px;
		}
		.form_field_required{
			margin-top:20px;
			margin-bottom:20px;
			padding-bottom:20px;
			border-color:black;
			border-radius:25px;
			background-color:#BB7C97;
		}
		.form_input{
			border-radius:10px;
			border-color: #6097A0;
		}
	</style>
	</head>
	
	<body class = "d-flex flex-column h-100">
		<?php 
			include("includes/navbar.php");
		?>
		<main class="container my-2">
			<div class = "row">
				<div class = "col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12 text-center">
					<?php 
						
						switch($page){
							case "case1":
								?>
								<script>
								$(document).ready(function() {
									$('.dosje_selected_employee').selectpicker();
									var menu = $('#menu');
									
									$('#menu_btn_aktivni_dokumenti').on('click', function() {
										menu.fadeOut();
										$('#menu_aktivni_dokumenti').delay(500).fadeIn();

									});			
									$('#menu_btn_stari_dokumenti').on('click', function() {
										menu.fadeOut();
										$('#menu_stari_dokumenti').delay(500).fadeIn();

									});	

									$('#menu_btn_predani_zahtjevi').on('click', function() {
										menu.fadeOut();
										$('#menu_predani_zahtjevi').delay(500).fadeIn();

									});
									$('#menu_btn_godisnji_odmor').on('click', function() {
										menu.fadeOut();
										$('#menu_godisnji_odmor').delay(500).fadeIn();

									});

									$('#menu_btn_sluzbeni_put').on('click', function() {
										menu.fadeOut();
										$('#menu_sluzbeni_put').delay(500).fadeIn();

									});

									$('#menu_btn_sluzbeno_vozilo').on('click', function() {
										menu.fadeOut();
										$('#menu_sluzbeno_vozilo').delay(500).fadeIn();

									});
									
									$('#menu_btn_bolovanje').on('click', function() {
										menu.fadeOut();
										$('#menu_bolovanje').delay(500).fadeIn();

									});
									
									$('#menu_btn_izvjestaj_sluzbeni_put').on('click', function() {
										menu.fadeOut();
										$('#menu_izvjestaj_sluzbeni_put').delay(500).fadeIn();

									});
									
									$('#menu_btn_pregled_dosjea_zaposlenika').on('click', function() {
										menu.fadeOut();
										$('#menu_pregled_dosjea_zaposlenika').delay(500).fadeIn();

									});
									
									$('#menu_btn_zahtjevi_na_cekanju').on('click', function() {
										menu.fadeOut();
										$('#menu_zahtjevi_na_cekanju').delay(500).fadeIn();

									});
									
									$('#menu_btn_posebni_datumi').on('click', function() {
										menu.fadeOut();
										$('#menu_posebni_datumi').delay(500).fadeIn();

									});
																		
									$('#menu_btn_seminari').on('click', function() {
										menu.fadeOut();
										$('#menu_seminari').delay(500).fadeIn();

									});
									
									$('#menu_btn_statistika').on('click', function() {
										menu.fadeOut();
										$('#menu_statistika').delay(500).fadeIn();

									});
									
									$('#menu_btn_izmjena_dokumenta').on('click', function() {
										menu.fadeOut();
										$('#menu_izmjena_dokumenta').delay(500).fadeIn();

									});
									
									$('#menu_btn_na_cekanju').on('click', function() {
										if ($('#menu_odobreni').is(':visible'))
											$('#menu_odobreni').fadeOut();
										
										else if ($('#menu_odbijeni').is(':visible')) 
											$('#menu_odbijeni').fadeOut();

										$('#menu_na_cekanju').delay(300).fadeIn();
									});
									
									$('#menu_btn_odobren').on('click', function() {
										if ($('#menu_na_cekanju').is(':visible'))
											$('#menu_na_cekanju').fadeOut();
										
										else if ($('#menu_odbijeni').is(':visible')) 
											$('#menu_odbijeni').fadeOut();

										$('#menu_odobreni').delay(300).fadeIn();
									});
									
									$('#menu_btn_odbijen').on('click', function() {
										if ($('#menu_odobreni').is(':visible'))
											$('#menu_odobreni').fadeOut();
										
										else if ($('#menu_na_cekanju').is(':visible')) 
											$('#menu_na_cekanju').fadeOut();

										$('#menu_odbijeni').delay(300).fadeIn();
									});									
									
									$('.btn_menu_back_onclick').on('click', function() {
										if ($('#menu_aktivni_dokumenti').is(':visible')) {
											$('#menu_aktivni_dokumenti').fadeOut();
											menu.delay(500).fadeIn();
										}
										else if ($('#menu_stari_dokumenti').is(':visible')) {
											$('#menu_stari_dokumenti').fadeOut();
											menu.delay(500).fadeIn();
										}

										else if ($('#menu_predani_zahtjevi').is(':visible')) {
											$('#menu_predani_zahtjevi').fadeOut();
											menu.delay(500).fadeIn();
										}
										
										else if ($('#menu_godisnji_odmor').is(':visible')) {
											$('#menu_godisnji_odmor').fadeOut();
											menu.delay(500).fadeIn();
										}
										else if ($('#menu_sluzbeni_put').is(':visible')) {
											$('#menu_sluzbeni_put').fadeOut();
											menu.delay(500).fadeIn();
										}
										else if ($('#menu_sluzbeno_vozilo').is(':visible')) {
											$('#menu_sluzbeno_vozilo').fadeOut();
											menu.delay(500).fadeIn();
										}
										else if ($('#menu_bolovanje').is(':visible')) {
											$('#menu_bolovanje').fadeOut();
											menu.delay(500).fadeIn();
										}
										else if ($('#menu_izvjestaj_sluzbeni_put').is(':visible')) {
											$('#menu_izvjestaj_sluzbeni_put').fadeOut();
											menu.delay(500).fadeIn();
										}
										else if ($('#menu_pregled_dosjea_zaposlenika').is(':visible')) {
											$('#menu_pregled_dosjea_zaposlenika').fadeOut();
											menu.delay(500).fadeIn();
										}
										else if ($('#menu_zahtjevi_na_cekanju').is(':visible')) {
											$('#menu_zahtjevi_na_cekanju').fadeOut();
											menu.delay(500).fadeIn();
										}
										else if ($('#menu_posebni_datumi').is(':visible')) {
											$('#menu_posebni_datumi').fadeOut();
											menu.delay(500).fadeIn();
										}
										else if ($('#menu_seminari').is(':visible')) {
											$('#menu_seminari').fadeOut();
											menu.delay(500).fadeIn();
										}
										else if ($('#menu_statistika').is(':visible')) {
											$('#menu_statistika').fadeOut();
											menu.delay(500).fadeIn();
										}
										else if ($('#menu_izmjena_dokumenta').is(':visible')) {
											$('#menu_izmjena_dokumenta').fadeOut();
											menu.delay(500).fadeIn();
										}
										else if ($('#menu_uspjesno_podnesen_zahtjev').is(':visible')) {
											$('#menu_uspjesno_podnesen_zahtjev').fadeOut();
											menu.delay(500).fadeIn();
										}
										
									});
																	  								   
								});
								</script>
<!-- Glavni izbornik START -->
								<div id="menu">
									<div class="row">

										<div class="mb-3 naslov">
											<h3><b>Dosije</b></h3>
											<hr>
										</div>
									
										<div class="col-4 d-inline-block text-center ">
											<button id="menu_btn_aktivni_dokumenti" type="button" class="btn btn_menu">
												<span class="material-icons">fact_check</span>
											</button>
											<br>
											<p style="font-size: 11px;">Aktivni dokumenti</p>
										</div>
										
										<div class="col-4 d-inline-block text-center col-xs-4">	
											<button id="menu_btn_stari_dokumenti" type="button" class="btn btn_menu">
												<span class="material-icons">auto_delete</span>
											</button>
											<br>
											<p style="font-size: 11px;">Stari dokumenti</p>
										</div>
										
										<div class="col-4 d-inline-block text-center col-xs-4	">
											<button id="menu_btn_predani_zahtjevi" type="button" class="btn btn_menu">
												<span class="material-icons">mark_email_read</span>
											</button>
											<br>
											<p style="font-size: 11px;">Predani zahtjevi</p>
										</div>
										
										<div class="mb-3 naslov">
											<hr>
											<h3><b>Zahtjevi</b></h3>
											<hr>
										</div>
										
										<div class="col-4 d-inline-block text-center ">
											<button id="menu_btn_godisnji_odmor" type="button" class="btn btn_menu">
												<span class="material-icons">surfing</span>
											</button>
											<br>
											<p style="font-size: 11px;">Godišnji odmor</p>
										</div>
										
										<div class="col-4 d-inline-block text-center ">
											<button id="menu_btn_sluzbeni_put" type="button" class="btn btn_menu">
												<span class="material-icons">card_travel</span>
											</button>
											<br>
											<p style="font-size: 11px;">Službeni put</p>
										</div>
										
										<div class="col-4 d-inline-block text-center ">
											<button id="menu_btn_sluzbeno_vozilo" type="button" class="btn btn_menu">
												<span class="material-icons">drive_eta</span>
											</button>
											<br>
											<p style="font-size: 11px;">Službeno vozilo</p>
										</div>
										
										<div class="col-4 d-inline-block text-center ">
											<button id="menu_btn_bolovanje" type="button" class="btn btn_menu">
												<span class="material-icons">healing</span>
											</button>
											<br>
											<p style="font-size: 11px;">Bolovanje</p>
										</div>
										
										<div class="col-4 d-inline-block text-center ">
											<button id="menu_btn_izmjena_dokumenta" type="button" class="btn btn_menu">
												<span class="material-icons">change_circle</span>
											</button>
											<br>
											<p style="font-size: 11px;">Izmjena dokumenta</p>
										</div>
										
										<div class="col-4 d-inline-block text-center ">
											<button id="menu_btn_izvjestaj_sluzbeni_put" type="button" class="btn btn_menu">
												<span class="material-icons">list_alt</span>
											</button>
											<br>
											<p style="font-size: 11px;">Izvještaj za službeni put</p>
										</div>
										
										<div class="mb-3 naslov">
											<hr>
											<h3><b>Uprava</b></h3>
											<hr>
										</div>

										<div class="col-4 d-inline-block text-center ">
											<button id="menu_btn_pregled_dosjea_zaposlenika" type="button" class="btn btn_menu">
												<span class="material-icons">manage_search</span>
											</button>
											<br>
											<p style="font-size: 11px;">Pregled dosjea zaposlenika</p>
										</div>
										
										<div class="col-4 d-inline-block text-center ">
											<button id="menu_btn_zahtjevi_na_cekanju" type="button" class="btn btn_menu">
												<span class="material-icons">alarm_on</span>
											</button>
											<br>
											<p style="font-size: 11px;">Zahtjevi na čekanju</p>
										</div>
																				
										<div class="col-4 d-inline-block text-center ">
											<button id="menu_btn_posebni_datumi" type="button" class="btn btn_menu">
												<span class="material-icons">date_range</span>
											</button>
											<br>
											<p style="font-size: 11px;">Posebni datumi</p>
										</div>
																														
										<div class="col-4 d-inline-block text-center ">
											<button id="menu_btn_seminari" type="button" class="btn btn_menu">
												<span class="material-icons">groups</span>
											</button>
											<br>
											<p style="font-size: 11px;">Seminari</p>
										</div>
																														
										<div class="col-4 d-inline-block text-center ">
											<button id="menu_btn_statistika" type="button" class="btn btn_menu">
												<span class="material-icons">leaderboard</span>
											</button>
											<br>
											<p style="font-size: 11px;">Statistiike</p>
										</div>
										
									</div>
								</div>
<!-- Glavni izbornik END -->

<!-- Ispis aktivnih dokumenata logovanog zaposlenika START -->

								<div id="menu_aktivni_dokumenti" style="display:none;">
									<div class="mb-3 naslov">
										<h3>
											<b>Aktivni dokumenti</b>
											<button type="button" class="btn btn_menu_back float-end btn_menu_back_onclick">
												<span class="material-icons" style="font-size: 18px;">undo</span>
											</button>
										</h3>
										<hr>
									</div>
									<?php
									$selected_employee = $logged_employee_id;
									$query_get_dosje = $db->prepare('
										SELECT dok.naziv, tip.naziv_dokumenta, dok.datum_unosa
										FROM idk_hr_dosje dok
										JOIN idk_hr_tipovi_dokumenta tip
										ON tip.id_tip_dokumenta = dok.tip_dokumenta
										WHERE dok.id_zaposlenik = :selected_employee
										AND dok.id_dokumenta = (
											SELECT max(doksq.id_dokumenta)
											FROM idk_hr_dosje doksq
											WHERE doksq.tip_dokumenta = dok.tip_dokumenta
											AND doksq.id_zaposlenik = :selected_employee
										)
									');
									
									$query_get_dosje -> execute(array(':selected_employee' => $selected_employee));
													
									while($row_get_dosje = $query_get_dosje->fetch()){
										$naziv = $row_get_dosje["naziv"];
										$tip_dokumenta = $row_get_dosje["naziv_dokumenta"];
										$datum_unosa = date("d.m.Y", strtotime($row_get_dosje["datum_unosa"]));
										
										echo   '<div class="card card_positive_sm col-5 d-inline-block">
													<div class="card-header">
														<b>'.$tip_dokumenta.'</b>
													</div>
													<div class="card-body">
														<p class="card-text">
															Datum unosa: <b>'.$datum_unosa.'</b><br>
															<a href="'.getSiteUrlr().'files/dosje/'.$naziv.'" download>
																<button type="button" class="btn btn_menu_sm" style="border-radius:15px">
																	<span class="material-icons" style="font-size:10px;">file_download</span>
																</button>
															</a>
														</p>
													</div>
												</div>
												';	
									}
								?>
								</div>
<!-- Ispis aktivnih dokumenata logovanog zaposlenika END-->

<!-- Ispis starih dokumenata logovanog zaposlenika START-->

								<div id="menu_stari_dokumenti" style="display:none;">
									<div class="mb-3 naslov">
										<h3>
											<b>Stari dokumenti</b>
											<button type="button" class="btn btn_menu_back float-end btn_menu_back_onclick">
												<span class="material-icons" style="font-size: 18px;">undo</span>
											</button>
										</h3>
										<hr>
									</div>
<?php
									$selected_employee = $logged_employee_id;
									$query_get_dosje = $db->prepare('
										SELECT dok.naziv, tip.naziv_dokumenta, dok.datum_unosa
										FROM idk_hr_dosje dok
										JOIN idk_hr_tipovi_dokumenta tip
										ON tip.id_tip_dokumenta = dok.tip_dokumenta
										WHERE dok.id_zaposlenik = :selected_employee
										AND dok.id_dokumenta != (
											SELECT max(doksq.id_dokumenta)
											FROM idk_hr_dosje doksq
											WHERE doksq.tip_dokumenta = dok.tip_dokumenta
											AND doksq.id_zaposlenik = :selected_employee
										)
									');
									
									$query_get_dosje -> execute(array(':selected_employee' => $selected_employee));
													
									while($row_get_dosje = $query_get_dosje->fetch()){
										$naziv = $row_get_dosje["naziv"];
										$tip_dokumenta = $row_get_dosje["naziv_dokumenta"];
										$datum_unosa = date("d.m.Y", strtotime($row_get_dosje["datum_unosa"]));
										
										echo   '<div class="card card_negative_sm col-5 d-inline-block">
													<div class="card-header">
														<b>'.$tip_dokumenta.'</b>
													</div>
													<div class="card-body">
														<p class="card-text">
															Datum unosa: <b>'.$datum_unosa.'</b><br>
															<a href="'.getSiteUrlr().'files/dosje/'.$naziv.'" download>
																<button type="button" class="btn btn_menu_sm" style="border-radius:15px">
																	<span class="material-icons" style="font-size:10px;">file_download</span>
																</button>
															</a>
														</p>
													</div>
												</div>
												';	
									}
								?>
								</div>

<!-- Ispis starih dokumenata logovanog zaposlenika END-->

<!-- Otvaranje pod menija ispisa zahtjeva logovanoh zaposlenika START-->

								<div id="menu_predani_zahtjevi" style="display:none;">
									<div class="mb-3 naslov">
										<h3>
											<b>Predani zahtjevi</b>
											<button type="button" class="btn btn_menu_back float-end btn_menu_back_onclick">
												<span class="material-icons" style="font-size: 18px;">undo</span>
											</button>
										</h3>
										<hr>
									</div>
									<?php
											$query_get_godisnje_odmore = $db->prepare('
												SELECT god.godisnji_odmor_trajanje, god.datum_podnosenja, god.datum_od, god.datum_do, god.zahtjev_status, god.razlog_odbijanja, doc.naziv
												FROM idk_hr_godisnji_odmor god
												LEFT JOIN idk_hr_dosje doc
												ON doc.id_dokumenta = god.dokument_id 
												WHERE god.zaposlenik_id = :logged_employee_id
												ORDER BY god.datum_podnosenja DESC

											');
											
											$query_get_godisnje_odmore -> execute(array(':logged_employee_id' => $logged_employee_id));
											$godisnji_odmor_na_cekanju = "";
											$godisnji_odmor_odobren = "";
											$godisnji_odmor_odbijen = "";

											while($row_get_godisnje_odmore = $query_get_godisnje_odmore->fetch()){
												
												$godisnji_odmor_trajanje = $row_get_godisnje_odmore['godisnji_odmor_trajanje'];
												$godisnji_odmor_datum_podnosenja = $row_get_godisnje_odmore['datum_podnosenja'];
												$godisnji_odmor_datum_od = $row_get_godisnje_odmore['datum_od'];
												$godisnji_odmor_datum_do = $row_get_godisnje_odmore['datum_do'];
												$godisnji_odmor_zahtjev_status= $row_get_godisnje_odmore['zahtjev_status'];
												$godisnji_odmor_razlog_odbijanja= $row_get_godisnje_odmore['razlog_odbijanja'];
												$godisnji_odmor_naziv_dokumenta= $row_get_godisnje_odmore['naziv'];

												if($godisnji_odmor_zahtjev_status == 1){
													$godisnji_odmor_na_cekanju .='
													<div class="card card_neutral">
														<div class="card-header">
															Zahtjev za godišnji odmor
														</div>
														<div class="card-body">
															<p class="card-text">
																Datum od: <b>'.date("d.m.Y", strtotime($godisnji_odmor_datum_od)).'</b> do <b>'.date("d.m.Y", strtotime($godisnji_odmor_datum_do)).'</b><br>
																Godišnji odmor bi trajao: <b>'.$godisnji_odmor_trajanje.'</b> dana<br>
																Datum podnošenja zahtjeva: <b>'.date("d.m.Y", strtotime($godisnji_odmor_datum_podnosenja)).'</b>
															</p>
														</div>
													</div>
													';
												}
												else if($godisnji_odmor_zahtjev_status == 2){
													$godisnji_odmor_odobren .='
														<div class="card card_positive">
															<div class="card-header">
																Zahtjev za godišnji odmor
															</div>
															<div class="card-body">
																	Datum od: <b>'.date("d.m.Y", strtotime($godisnji_odmor_datum_od)).'</b> do <b>'.date("d.m.Y", strtotime($godisnji_odmor_datum_do)).'</b><br>
																	Godišnji odmor je trajao: <b>'.$godisnji_odmor_trajanje.'</b> dana<br>
																	Datum podnošenja zahtjeva: <b>'.date("d.m.Y", strtotime($godisnji_odmor_datum_podnosenja)).'</b><br>										
																	<a href="'.getSiteUrlr().'files/dosje/'.$godisnji_odmor_naziv_dokumenta.'" download><br>
																		<button type="button" class="btn btn_menu col-8" style="border-radius:15px">
																			<span class="material-icons">file_download</span> Download
																		</button>
																	</a>
															</div>
														</div>
													';
												}
												else if($godisnji_odmor_zahtjev_status == 3){
													$godisnji_odmor_odbijen .= '
														<div class="card card_negative">
															<div class="card-header">
																 Zahtjev za godišnji odmor
															</div>
															<div class="card-body">
																<p class="card-text">
																	Datum od: <b>'.date("d.m.Y", strtotime($godisnji_odmor_datum_od)).'</b> do <b>'.date("d.m.Y", strtotime($godisnji_odmor_datum_do)).'</b><br>
																	Godišnji odmor bi trajao: <b>'.$godisnji_odmor_trajanje.'</b> dana<br>
																	Datum podnošenja zahtjeva: <b>'.date("d.m.Y", strtotime($godisnji_odmor_datum_podnosenja)).'</b><br>
																	Razlog odbijanja: <br><b>'.$godisnji_odmor_razlog_odbijanja.'</b>
																</p>
															</div>
														</div>
													';
												}
											}
										if($godisnji_odmor_na_cekanju == "")
											$godisnji_odmor_na_cekanju = '<p style="text-align: center; color: #BB7C97;"><b><i>Nemate predanih zahtjeva za koje čekate odobrenje</b></i></p>';
										
										if($godisnji_odmor_odobren == "")
											$godisnji_odmor_odobren = '<p style="text-align: center; color: #BB7C97;"><b><i>Nemate odobrenih zahtjeva </p></b></i>';
										
										if($godisnji_odmor_odbijen == "")
											$godisnji_odmor_odbijen = '<p style="text-align: center; color: #BB7C97;"><b><i>Nemate odbijenih zahtjeva </p></b></i>';
										
										
										$sluzbeni_put_na_cekanju = "";
										$sluzbeni_put_odobren = "";
										$sluzbeni_put_odbijen = "";
										
										$query_get_zahtjeve_sluzbeni_put = $db->prepare('
											SELECT sp.datum_od, sp.datum_do, sp.relacija_od, sp.relacija_do, sp.dnevnica_dana, sp.sluzbeno_vozilo_dana, sp.trajanje_rezervacije, 
											sp.tip_troska_smjestaja, dos.naziv, sp.odobren, sp.datum_podnosenja, sp.razlog_odbijanja
											FROM idk_hr_sluzbeni_put sp
											LEFT JOIN idk_hr_dosje dos
											ON dos.id_dokumenta = sp.dokument_id
											WHERE sp.zaposlenik_id = :logged_employee_id
											ORDER BY sp.datum_podnosenja DESC
										');
										
										$query_get_zahtjeve_sluzbeni_put->execute(array(':logged_employee_id' => $logged_employee_id));
										
										while($row_get_zahtjeve_sluzbeni_put = $query_get_zahtjeve_sluzbeni_put->fetch()){
											$zahtjev_sluzbeni_put_datum_od = date("d.m.Y", strtotime($row_get_zahtjeve_sluzbeni_put['datum_od']));
											$zahtjev_sluzbeni_put_datum_do = date("d.m.Y", strtotime($row_get_zahtjeve_sluzbeni_put['datum_do']));
											$zahtjev_sluzbeni_put_relacija_od = $row_get_zahtjeve_sluzbeni_put['relacija_od'];
											$zahtjev_sluzbeni_put_relacija_do = $row_get_zahtjeve_sluzbeni_put['relacija_do'];
											$zahtjev_sluzbeni_put_dnevnica_dana = $row_get_zahtjeve_sluzbeni_put['dnevnica_dana'];
											$zahtjev_sluzbeni_put_sluzbeno_vozilo_dana = $row_get_zahtjeve_sluzbeni_put['sluzbeno_vozilo_dana'];
											$zahtjev_sluzbeni_put_trajanje_rezervacije = $row_get_zahtjeve_sluzbeni_put['trajanje_rezervacije'];
											$zahtjev_sluzbeni_put_tip_troska_smjestaja = $row_get_zahtjeve_sluzbeni_put['tip_troska_smjestaja'];
											$zahtjev_sluzbeni_put_naziv = $row_get_zahtjeve_sluzbeni_put['naziv'];
											$zahtjev_sluzbeni_put_odobren = $row_get_zahtjeve_sluzbeni_put['odobren'];
											$zahtjev_sluzbeni_put_razlog_odbijanja = $row_get_zahtjeve_sluzbeni_put['razlog_odbijanja'];
											$zahtjev_sluzbeni_put_datum_podnosenja = date("d.m.Y", strtotime($row_get_zahtjeve_sluzbeni_put['datum_podnosenja']));
											
											if($zahtjev_sluzbeni_put_tip_troska_smjestaja == 1){
												$zahtjev_sluzbeni_put_text_trosak_smjestaja = "Trošak: <b>Gotovina</b>";
											}
											else if($zahtjev_sluzbeni_put_tip_troska_smjestaja == 2){
												$zahtjev_sluzbeni_put_text_trosak_smjestaja = "Trošak: <b>Na račun</b>";
											}
											else
												$zahtjev_sluzbeni_put_text_trosak_smjestaja = "";
											
											if($zahtjev_sluzbeni_put_odobren == 0){
													$sluzbeni_put_na_cekanju.='
													<div class="card card_neutral">
														<div class="card-header">
															Zahtjev za službeni put
														</div>
														<div class="card-body">
															<p class="card-text">
																Datum podnošenja zahtjeva: <b>'.$zahtjev_sluzbeni_put_datum_podnosenja.'</b><br>
																Datum od: <b>'.$zahtjev_sluzbeni_put_datum_od.'</b> do <b>'.$zahtjev_sluzbeni_put_datum_do.'</b><br>
																Relacija od: <b>'.$zahtjev_sluzbeni_put_relacija_od.'</b> do <b>'.$zahtjev_sluzbeni_put_relacija_do.'</b><br>
																Dnevnica: <b>'.$zahtjev_sluzbeni_put_dnevnica_dana.'</b> dana<br>
																Službeno vozilo: <b>'.$zahtjev_sluzbeni_put_sluzbeno_vozilo_dana.'</b> dana<br>
																Smještaj: <b>'.$zahtjev_sluzbeni_put_trajanje_rezervacije.'</b> dana<br>
																'.$zahtjev_sluzbeni_put_text_trosak_smjestaja.'
															</p>
														</div>
													</div>
													';
												}
												else if($zahtjev_sluzbeni_put_odobren == 1){
													$sluzbeni_put_odobren .='
														<div class="card card_positive">
															<div class="card-header">
															Zahtjev za službeni put
															</div>
															<div class="card-body">
																<p class="card-text">
																	Datum podnošenja zahtjeva: <b>'.$zahtjev_sluzbeni_put_datum_podnosenja.'</b><br>
																	Datum od: <b>'.$zahtjev_sluzbeni_put_datum_od.'</b> do <b>'.$zahtjev_sluzbeni_put_datum_do.'</b><br>
																	Relacija od: <b>'.$zahtjev_sluzbeni_put_relacija_od.'</b> do <b>'.$zahtjev_sluzbeni_put_relacija_do.'</b><br>
																	Dnevnica: <b>'.$zahtjev_sluzbeni_put_dnevnica_dana.'</b> dana<br>
																	Službeno vozilo: <b>'.$zahtjev_sluzbeni_put_sluzbeno_vozilo_dana.'</b> dana<br>
																	Smještaj: <b>'.$zahtjev_sluzbeni_put_trajanje_rezervacije.'</b> dana<br>
																	'.$zahtjev_sluzbeni_put_text_trosak_smjestaja.'
																</p>					
																<a href="'.getSiteUrlr().'files/dosje/'.$godisnji_odmor_naziv_dokumenta.'" download><br>
																	<button type="button" class="btn btn_menu col-8" style="border-radius:15px">
																		<span class="material-icons">file_download</span> Download
																	</button>
																</a>
															</div>
														</div>
													';
												}
												else if($zahtjev_sluzbeni_put_odobren == 2){
													$sluzbeni_put_odbijen .= '
														<div class="card card_negative">
															<div class="card-header">
																Zahtjev za službeni put
															</div>
															<div class="card-body">
																<p class="card-text">
																	Datum podnošenja zahtjeva: <b>'.$zahtjev_sluzbeni_put_datum_podnosenja.'</b><br>
																	Datum od: <b>'.$zahtjev_sluzbeni_put_datum_od.'</b> do <b>'.$zahtjev_sluzbeni_put_datum_do.'</b><br>
																	Relacija od: <b>'.$zahtjev_sluzbeni_put_relacija_od.'</b> do <b>'.$zahtjev_sluzbeni_put_relacija_do.'</b><br>
																	Dnevnica: <b>'.$zahtjev_sluzbeni_put_dnevnica_dana.'</b> dana<br>
																	Službeno vozilo: <b>'.$zahtjev_sluzbeni_put_sluzbeno_vozilo_dana.'</b> dana<br>
																	Smještaj: <b>'.$zahtjev_sluzbeni_put_trajanje_rezervacije.'</b> dana<br>
																	'.$zahtjev_sluzbeni_put_text_trosak_smjestaja.'<br>
																	Razlog odbijanja:<br>'.$zahtjev_sluzbeni_put_razlog_odbijanja.'
																</p>
															</div>
														</div>
													';
												}
										if($sluzbeni_put_na_cekanju == "")
											$sluzbeni_put_na_cekanju = '<p style="text-align: center; color: #BB7C97;"><b><i>Nemate predanih zahtjeva za koje čekate odobrenje</b></i></p>';
										
										if($sluzbeni_put_odobren == "")
											$sluzbeni_put_odobren = '<p style="text-align: center; color: #BB7C97;"><b><i>Nemate odobrenih zahtjeva </p></b></i>';
										
										if($sluzbeni_put_odbijen == "")
											$sluzbeni_put_odbijen = '<p style="text-align: center; color: #BB7C97;"><b><i>Nemate odbijenih zahtjeva </p></b></i>';
										}
										?>
									<div class="row">
										<div class="col-4 d-inline-block text-center ">
											<button id="menu_btn_na_cekanju" type="button" class="btn btn_menu">
												<span class="material-icons">watch</span>
											</button>
											<br>
											<p style="font-size: 11px;">Na čekanju</p>
										</div>
		
										<div class="col-4 d-inline-block text-center ">
											<button id="menu_btn_odobren" type="button" class="btn btn_menu">
												<span class="material-icons">thumb_up</span>
											</button>
											<br>
											<p style="font-size: 11px;">Odobreni</p>
										</div>
										
										<div class="col-4 d-inline-block text-center ">
											<button id="menu_btn_odbijen" type="button" class="btn btn_menu">
												<span class="material-icons">thumb_down</span>
											</button>
											<br>
											<p style="font-size: 11px;">Odbijeni</p>
										</div>
									</div>
	<!-- Ispis zahtjeva na čekanju START-->
									<div id="menu_na_cekanju" style="display:none;">
										<div class="my-3 naslov">
											<hr>
											<h3>
												<b>Zahtjevi za godišnji odmor:</b>
											</h3>
											<hr>
										</div>
										<?php
										echo $godisnji_odmor_na_cekanju;
										?>
										<div class="my-3 naslov">
											<hr>
											<h3>
												<b>Zahtjevi za službeni put:</b>
											</h3>
											<hr>
										</div>
										<?php
										echo $sluzbeni_put_na_cekanju;
										?>
									</div>
	<!-- Ispis zahtjeva na čekanju END-->
	
	<!-- Ispis odobrenih zahtjeva START-->
									<div id="menu_odobreni" style="display:none;">
										<div class="my-3 naslov">
											<hr>
											<h3>
												<b>Zahtjevi za godišnji odmor:</b>
											</h3>
											<hr>
										</div>
										<?php
										echo $godisnji_odmor_odobren;
										?>
										<div class="my-3 naslov">
											<hr>
											<h3>
												<b>Zahtjevi za službeni put:</b>
											</h3>
											<hr>
										</div>
										<?php
										echo $sluzbeni_put_odobren;
										?>
									</div>
	<!-- Ispis odobrenih zahtjeva END-->
	
	<!-- Ispis odbijenih zahtjeva START-->
									<div id="menu_odbijeni" style="display:none;">
										<div class="my-3 naslov">
											<hr>
											<h3>
												<b>Zahtjevi za godišnji odmor:</b>
											</h3>
											<hr>
										</div>
										<?php
										echo $godisnji_odmor_odbijen;
										?>
										<div class="my-3 naslov">
											<hr>
											<h3>
												<b>Zahtjevi za službeni put:</b>
											</h3>
											<hr>
										</div>
										<?php
										echo $sluzbeni_put_odbijen;
										?>
									</div>
								</div>
	<!-- Ispis odbijenih zahtjeva END-->
								
<!-- Otvaranje pod menija ispisa zahtjeva logovanoh zaposlenika END-->

<!-- Interfejs za predavanje zahtjeva za godišnji odmor START-->

								<div id="menu_godisnji_odmor" style="display:none;">
									<div class="mb-3 naslov">
										<h3>
											<b>Zahtjev za godišnji odmor</b>
											<button type="button" class="btn btn_menu_back float-end btn_menu_back_onclick">
												<span class="material-icons" style="font-size: 18px;">undo</span>
											</button>
										</h3>
										<hr>
									</div>
									<div class="card card_neutral">
										<div class="card-header">
											Preostali dani godišnjeg odmora
										</div>
										<div class="card-body">
											<div class="row">
												<div class="col-4 d-inline-block px-1">
													<div class="vacation_days_remaning">
														<div style="font-size:9px">2020:</div>
														10
													</div>
												</div>
												<div class="col-4 d-inline-block px-1">
													<div class="vacation_days_remaning">
														<div style="font-size:9px">2021:</div>
														10
													</div>
												</div>
												<div class="col-4 d-inline-block px-1">
													<div class="vacation_days_remaning">
														<div style="font-size:9px">2022:</div>
														10
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="col-10 offset-1 mt-3">
										<input class="form-control" type="text" name="period_godisnji" id="period_godisnji" placeholder="Unesite datum">
									</div>
									
									<script>
										$("#period_godisnji").flatpickr({
											mode: "range",
											dateFormat: "d.m.Y",
											disableMobile: "true",
											minDate: new Date().fp_incr(10)
										});
									</script>
									
									
									<div class="card card_negative">
										<div class="card-header">
											Featured
										</div>
										<div class="card-body">
											<p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
										</div>
									</div>
									<div class="card card_positive">
										<div class="card-header">
											Featured
										</div>
										<div class="card-body">
											<p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
										</div>
									</div>
									
									
								</div>

<!-- Interfejs za predavanje zahtjeva za godišnji odmor END-->

<!-- Interfejs za predavanje zahtjeva za službeni put START-->

								<div id="menu_sluzbeni_put" style="display:none;">
									<div class="mb-3 naslov">
										<h3>
											<b>Zahtjev za službeni put</b>
											<button type="button" class="btn btn_menu_back float-end btn_menu_back_onclick">
												<span class="material-icons" style="font-size: 18px;">undo</span>
											</button>
										</h3>
										<hr>
									</div>
									<div id="form_period_sluzbeni_put" class="form_field">
										<label for="period_sluzbeni_put"><span class="text-danger">*</span> Period službenog puta:</label>
										<div class="col-10 offset-1">
											<input class="form-control form_input" type="text" name="period_sluzbeni_put" id="period_sluzbeni_put">
											<script>
									
												$("#period_sluzbeni_put").flatpickr({
													mode: "range",
													dateFormat: "Y-m-d",
													disableMobile: "true",
													minDate: "today"
												});
												
												
												$("#period_sluzbeni_put" ).on('change', function() {

													var period = $("#period_sluzbeni_put").val();
													period = period.split(' to ');
													
													if(typeof period[1] !== 'undefined'){
														var nizDatumOd = period[0].split('-');
														var nizDatumDo = period[1].split('-');
														var datumOd = new Date(nizDatumOd[0], parseInt(nizDatumOd[1])-1, parseInt(nizDatumOd[2])-1);
														var datumDo = new Date(nizDatumDo[0], parseInt(nizDatumDo[1])-1, parseInt(nizDatumDo[2])+1);
														
														var cnt = 0;

														while(datumOd.toString(datumOd.setDate(datumOd.getDate()+1)) != datumDo.toString()){																		
															cnt++;

														}

														$("#period_sluzbeni_put_dana").val(cnt);
													}	
												});
												
											</script>
										</div>
									</div>
									
									<div id="form_relacija_od" class="form_field">
										<label for="relacija_od" class="col-8 control-label"><span class="text-danger">*</span> Na relaciji od:</label>
										<div class="col-10 offset-1">
											<input class="form-control col-8 form_input" type="text" name="relacija_od" id="relacija_od">
										</div>
									</div>
									
									<div id="form_relacija_do" class="form_field">
										<label for="relacija_do" class="col-8 control-label"><span class="text-danger">*</span> do:</label>
										<div class="col-10 offset-1">
											<input class="form-control form_input" type="text" name="relacija_do" id="relacija_do">
										</div>
									</div>
									<div class="row">
										<div id="form_check_dnevnica" class="form_field col-6 d-inline-block">
											<label><input class="form-check-input" type="checkbox" name="check_dnevnica" id="check_dnevnica"> Dnevnica</label>
											<br>
											<div id="dnevnica_open" style="display:none;">
												<label>Za <input class="form_control form_input" style="width:20%" type="text" name="dnevnica_dana" id="dnevnica_dana"> dana<label>
											</div>
										</div>
										<script>
											$("#check_dnevnica" ).on('change', function() {
												if ($(this).is(':checked')) {
													$("#dnevnica_open").fadeIn();
												}
												else{
													$("#dnevnica_open").fadeOut();
												}
											});
										</script>
										
										<div id="form_check_sluzbeno_vozilo" class="form_field col-6 d-inline-block">
											<label><input class="form-check-input" type="checkbox" name="check_sluzbeno_vozilo" id="check_sluzbeno_vozilo"> Službeno vozilo</label>
											<br>
											<div id="sluzbeno_vozilo_open" style="display:none;">
												<label>Za <input class="form_control form_input" style="width:20%" type="text" name="sluzbeno_vozilo_dana" id="sluzbeno_vozilo_dana"> dana<label>
											</div>
										</div>
										<script>
											$("#check_sluzbeno_vozilo" ).on('change', function() {
												if ($(this).is(':checked')) {
													$("#sluzbeno_vozilo_open").fadeIn();
												}
												else{
													$("#sluzbeno_vozilo_open").fadeOut();
												}
											});
										</script>
									</div>
									<div class="row">
										<div id="form_check_smjestaj"  class="form_field col-6 d-inline-block">
											<label><input class="form-check-input" type="checkbox" name="check_smjestaj" id="check_smjestaj"> Smještaj</label>
											<br>
											<div id="smjestaj_open" style="display:none;">
												<label>Za <input class="form_control form_input" style="width:20%" name="trajanje_rezervacije" id="trajanje_rezervacije"> dana<label>
											</div>
										</div>
										<script>
											$("#check_smjestaj" ).on('change', function() {
												if ($(this).is(':checked')) {
													$("#smjestaj_open").fadeIn();
												}
												else{
													$("#smjestaj_open").fadeOut();
												}
											});
										</script>
										
										<div id="form_troskovi_nocenje"  class="form_field col-6 d-inline-block">
											<label><input class="form-check-input" type="checkbox" name="check_troskovi_nocenje" id="check_troskovi_nocenje"> Troškovi noćenja</label>
											<br>
											<div id="troskovi_nocenje_open" style="display:none;">
												<label> <input class="form-check-input" type="checkbox" name="check_gotovina" id="check_gotovina"> Gotovina</label>
												<label> <input class="form-check-input" type="checkbox" name="check_racun" id="check_racun"> Na račun</label>
											</div>
											<script>
												$("#check_troskovi_nocenje" ).on('change', function() {
													if ($(this).is(':checked')) {
														$("#troskovi_nocenje_open").fadeIn();
													}
													else{
														$("#troskovi_nocenje_open").fadeOut();
													}
												});
												$("#check_gotovina").on('change', function(){
													$("#check_racun").prop("checked", false);
												});
												$("#check_racun").on('change', function(){
													$( "#check_gotovina" ).prop( "checked", false );
												});
											</script>
										</div>
									</div>
									<div class="card card_negative mb-5" id="msq_required_sluzbeni_put" style="display:none;">
										<div class="card-header">
											Istaknuta polja su obavezna !!!
										</div>
									</div>
									<button id="form_submit_zahtjev_sluzbeni_put" type="button" class="btn btn_menu_back mt-3">
										<span class="material-icons" >file_upload PREDAJ ZAHTJEV file_upload</span>
									</button>
									<input id="period_sluzbeni_put_dana" type="hidden">

									<script>
										
										$("#form_submit_zahtjev_sluzbeni_put").on('click',function(){
											
											var period_sluzbeni_put = $("#period_sluzbeni_put").val();
											var period_sluzbeni_put_dana = $("#period_sluzbeni_put_dana").val();
											var relacija_od = $("#relacija_od").val();
											var relacija_do = $("#relacija_do").val();
											var check_dnevnica = $("#check_dnevnica").is(':checked');
											var dnevnica_dana = $("#dnevnica_dana").val();
											var check_sluzbeno_vozilo = $("#check_sluzbeno_vozilo").is(':checked');
											var sluzbeno_vozilo_dana = $("#sluzbeno_vozilo_dana").val();
											var check_smjestaj = $("#check_smjestaj").is(':checked');
											var trajanje_rezervacije = $("#trajanje_rezervacije").val();
											var check_troskovi_nocenje = $("#check_troskovi_nocenje").is(':checked');
											var check_racun = $("#check_racun").is(':checked');
											var check_gotovina = $("#check_gotovina").is(':checked');
											var flag_greske=0;
											
											if(period_sluzbeni_put == ""){
												$("#form_period_sluzbeni_put").removeClass('form_field');
												$("#form_period_sluzbeni_put").addClass('form_field_required');
												$('#msq_required_sluzbeni_put').fadeIn();
												flag_greske=1;
											}
											else{
												$("#form_period_sluzbeni_put").removeClass('form_field_required');
												$("#form_period_sluzbeni_put").addClass('form_field');
											}
											if(relacija_od == ""){
												$("#form_relacija_od").removeClass('form_field');
												$("#form_relacija_od").addClass('form_field_required');
												$('#msq_required_sluzbeni_put').fadeIn();
												flag_greske=1;
											}
											else{
												$("#form_relacija_od").removeClass('form_field_required');
												$("#form_relacija_od").addClass('form_field');
											}
											if(relacija_do == ""){
												$("#form_relacija_do").removeClass('form_field');
												$("#form_relacija_do").addClass('form_field_required');
												$('#msq_required_sluzbeni_put').fadeIn();
												flag_greske=1;
											}
											else{
												$("#form_relacija_do").removeClass('form_field_required');
												$("#form_relacija_do").addClass('form_field');
											}
											
											if(check_dnevnica){
												if(dnevnica_dana == ""){
													$("#form_check_dnevnica").removeClass('form_field');
													$("#form_check_dnevnica").addClass('form_field_required');
													$('#msq_required_sluzbeni_put').fadeIn();
													flag_greske=1;
												}
												else{
													$("#form_check_dnevnica").removeClass('form_field_required');
													$("#form_check_dnevnica").addClass('form_field');
												}
											}
											else{
												$("#form_check_dnevnica").removeClass('form_field_required');
												$("#form_check_dnevnica").addClass('form_field');
											}
											if(check_sluzbeno_vozilo){
												if(sluzbeno_vozilo_dana == ""){
													$("#form_check_sluzbeno_vozilo").removeClass('form_field');
													$("#form_check_sluzbeno_vozilo").addClass('form_field_required');
													$('#msq_required_sluzbeni_put').fadeIn();
													flag_greske=1;
												}
												else{
													$("#form_check_sluzbeno_vozilo").removeClass('form_field_required');
													$("#form_check_sluzbeno_vozilo").addClass('form_field');
												}
											}
											else{
												$("#form_check_sluzbeno_vozilo").removeClass('form_field_required');
												$("#form_check_sluzbeno_vozilo").addClass('form_field');
											}		
											
											if(check_smjestaj){
												if(trajanje_rezervacije == ""){
													$("#form_check_smjestaj").removeClass('form_field');
													$("#form_check_smjestaj").addClass('form_field_required');
													$('#msq_required_sluzbeni_put').fadeIn();
													flag_greske=1;
												}
												else{
													$("#form_check_smjestaj").removeClass('form_field_required');
													$("#form_check_smjestaj").addClass('form_field');
												}
											}
											else{
												$("#form_check_smjestaj").removeClass('form_field_required');
												$("#form_check_smjestaj").addClass('form_field');
											}
											if(check_troskovi_nocenje){
												if(!check_racun && !check_gotovina){
													$("#form_troskovi_nocenje").removeClass('form_field');
													$("#form_troskovi_nocenje").addClass('form_field_required');
													$('#msq_required_sluzbeni_put').fadeIn();
													flag_greske=1;
												}
												else{
													$("#form_troskovi_nocenje").removeClass('form_field_required');
													$("#form_troskovi_nocenje").addClass('form_field');
												}
											}
											else{
												$("#form_troskovi_nocenje").removeClass('form_field_required');
												$("#form_troskovi_nocenje").addClass('form_field');
											}
											var period_sluzbeni_put = $("#period_sluzbeni_put").val();
											var relacija_od = $("#relacija_od").val();
											var relacija_do = $("#relacija_do").val();
											var check_dnevnica = $("#check_dnevnica").is(':checked');
											var dnevnica_dana = $("#dnevnica_dana").val();
											var check_sluzbeno_vozilo = $("#check_sluzbeno_vozilo").is(':checked');
											var sluzbeno_vozilo_dana = $("#sluzbeno_vozilo_dana").val();
											var check_smjestaj = $("#check_smjestaj").is(':checked');
											var trajanje_rezervacije = $("#trajanje_rezervacije").val();
											var check_troskovi_nocenje = $("#check_troskovi_nocenje").is(':checked');
											var check_racun = $("#check_racun").is(':checked');
											var check_gotovina = $("#check_gotovina").is(':checked');
											if(flag_greske == 0){
												$.ajax({
													url: 'ajax_data.php?form=obrada_zahtjeva_sluzbeni_put',
													type: 'POST',
													data: {	
														'period_sluzbeni_put':period_sluzbeni_put,										
														'relacija_od':relacija_od,										
														'relacija_do':relacija_do,										
														'check_dnevnica':check_dnevnica,										
														'dnevnica_dana':dnevnica_dana,										
														'check_sluzbeno_vozilo':check_sluzbeno_vozilo,										
														'sluzbeno_vozilo_dana':sluzbeno_vozilo_dana,										
														'check_smjestaj':check_smjestaj,										
														'trajanje_rezervacije':trajanje_rezervacije,										
														'check_troskovi_nocenje':check_troskovi_nocenje,										
														'check_racun':check_racun,							
														'check_gotovina':check_gotovina							
													},
													dataType: 'text',
													success: function(data) {
														$('#menu_sluzbeni_put').fadeOut();
														$('#menu_uspjesno_podnesen_zahtjev').delay(500).fadeIn();
														$("#period_sluzbeni_put").val("");
														$("#period_sluzbeni_put_dana").val("");
														$("#relacija_od").val("");
														$("#relacija_do").val("");
														$("#check_dnevnica").prop("checked",false);
														$("#dnevnica_dana").val("");
														$("#check_sluzbeno_vozilo").prop("checked",false);
														$("#sluzbeno_vozilo_dana").val("");
														$("#check_smjestaj").prop("checked",false);
														$("#trajanje_rezervacije").val("");
														$("#check_troskovi_nocenje").prop("checked",false);
														$("#check_racun").prop("checked",false);
														$("#check_gotovina").prop("checked",false);
													},
													error: function (xhr, ajaxOptions, thrownError) {
														alert(xhr.status);
														alert(thrownError);
													}
												});
											}
										});
									</script>
								</div>

<!-- Interfejs za predavanje zahtjeva za službeni put END-->

<!-- Interfejs za predavanje zahtjeva za službeno vozilo START-->

								<div id="menu_sluzbeno_vozilo" style="display:none;">
									<div class="mb-3 naslov">
										<h3>
											<b>Zahtjev za službeno vozilo</b>
											<button type="button" class="btn btn_menu_back float-end btn_menu_back_onclick">
												<span class="material-icons" style="font-size: 18px;">undo</span>
											</button>
										</h3>
										<hr>
									</div>
								</div>
								
<!-- Interfejs za predavanje zahtjeva za službeno vozilo END-->

<!-- Interfejs za predavanje zahtjeva za bolovanje START-->

								<div id="menu_bolovanje" style="display:none;">
									<div class="mb-3 naslov">
										<h3>
											<b>Zahtjev za bolovanje</b>
											<button type="button" class="btn btn_menu_back float-end btn_menu_back_onclick">
												<span class="material-icons" style="font-size: 18px;">undo</span>
											</button>
										</h3>
										<hr>
									</div>
								</div>
								
<!-- Interfejs za predavanje zahtjeva za bolovanje END-->

<!-- Interfejs za predavanje zahtjeva za izmjenu dokumenta START-->

								<div id="menu_izmjena_dokumenta" style="display:none;">
									<div class="mb-3 naslov">
										<h3>
											<b>Izmjena dokumenta</b>
											<button type="button" class="btn btn_menu_back float-end btn_menu_back_onclick">
												<span class="material-icons" style="font-size: 18px;">undo</span>
											</button>
										</h3>
										<hr>
									</div>
								</div>
								
<!-- Interfejs za predavanje zahtjeva za izmjenu dokumenta END-->

<!-- Interfejs za predavanje izvještaja za službeni put START-->

								<div id="menu_izvjestaj_sluzbeni_put" style="display:none;">
									<div class="mb-3 naslov">
										<h3>
											<b>Izvještaj za službeni put</b>
											<button type="button" class="btn btn_menu_back float-end btn_menu_back_onclick">
												<span class="material-icons" style="font-size: 18px;">undo</span>
											</button>
										</h3>
										<hr>
									</div>
								</div>

<!-- Interfejs za predavanje izvještaja za službeni put END-->

<!-- Interfejs za ispis dosjea nekog zaposlenika START-->

								<div id="menu_pregled_dosjea_zaposlenika" style="display:none;">
									<div class="mb-3 naslov">
										<h3>
											<b>Pregled dosjea zaposlenika</b>
											<button type="button" class="btn btn_menu_back float-end btn_menu_back_onclick">
												<span class="material-icons" style="font-size: 18px;">undo</span>
											</button>
										</h3>
										<hr>
									</div>
									<div>
									<select class="selectpicker" id="dosje_selected_employee" name="dosje_selected_employee" data-live-search="true"  data-actions-box="true">
										<option value="Odaberite zaposlenika" selected disabled hidden>Odaberite zaposlenika</option>
										<?php
										$query_get_all_employees = $db -> prepare('
											SELECT employee_id, employee_firstname, employee_lastname
											FROM idk_employees
											WHERE employee_status != 0
										');
										$query_get_all_employees -> execute();
										while($row_get_all_employees = $query_get_all_employees->fetch()){
											$employee_full_name = $row_get_all_employees['employee_firstname']." ".$row_get_all_employees['employee_lastname'];
											$employee_id = $row_get_all_employees['employee_id'];
											?>
											<option value="<?php echo $employee_id; ?>"><?php echo $employee_full_name; ?></option>
											<?php
										}
										?>
									</select>
									</div>
								</div>
								
<!-- Interfejs za ispis dosjea nekog zaposlenika END-->

<!-- Interfejs za ispis predanih zahtjeva koje treba odobriti/odbiti START-->

								<div id="menu_zahtjevi_na_cekanju" style="display:none;">
									<div class="mb-3 naslov">
										<h3>
											<b>Zahtjevi na čekanju</b>
											<button type="button" class="btn btn_menu_back float-end btn_menu_back_onclick">
												<span class="material-icons" style="font-size: 18px;">undo</span>
											</button>
										</h3>
										<hr>
									</div>
								</div>
																
<!-- Interfejs za ispis predanih zahtjeva koje treba odobriti/odbiti END-->

<!-- Interfejs za unos posebnih datuma START-->

								<div id="menu_posebni_datumi" style="display:none;">
									<div class="mb-3 naslov">
										<h3>
											<b>Posebni datumi</b>
											<button type="button" class="btn btn_menu_back float-end btn_menu_back_onclick">
												<span class="material-icons" style="font-size: 18px;">undo</span>
											</button>
										</h3>
										<hr>
									</div>
								</div>
																								
<!-- Interfejs za unos posebnih datuma EMD-->

<!-- Interfejs za kreiranje seminara START-->

								<div id="menu_seminari" style="display:none;">
									<div class="mb-3 naslov">
										<h3>
											<b>Seminari</b>
											<button type="button" class="btn btn_menu_back float-end btn_menu_back_onclick">
												<span class="material-icons" style="font-size: 18px;">undo</span>
											</button>
										</h3>
										<hr>
									</div>
								</div>
								
<!-- Interfejs za kreiranje seminara END-->

<!-- Interfejs za ispis statistika START-->

								<div id="menu_statistika" style="display:none;">
									<div class="mb-3 naslov">
										<h3>
											<b>Statistika</b>
											<button type="button" class="btn btn_menu_back float-end btn_menu_back_onclick">
												<span class="material-icons" style="font-size: 18px;">undo</span>
											</button>
										</h3>
										<hr>
									</div>
								</div>
								
<!-- Interfejs za ispis statistika END-->

<!-- Prozor koji daje do znanja da je zahtjev uspješno podnesen START-->
								<div id="menu_uspjesno_podnesen_zahtjev" style="display:none">
									<div class="card card_positive mt-5">
										<div class="card-header">
											Zahtjev je uspješno podnesen
										</div>
									</div>
									<button type="button" class="btn btn_menu btn_menu_back_onclick col-6 mt-5" style="border-radius:15px">
										<span class="material-icons" style="font-size: 30px;">thumb_up OK</span>
									</button>
								</div>
<!-- Prozor koji daje do znanja da je zahtjev uspješno podnesen END-->
								
								<?php
							break;
						}
					?>
				</div>
			</div>
		</main>
		<?php 
			include("includes/footer.php");
		?>
	</body>
</html>
<?php 
	}else{
		header("Location:/jobstep_qc/landing.php");
	}
?>