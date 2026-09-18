<?php
	include("includes/functions.php");
	include("includes/common.php");

	$getEmployeeStatus = getEmployeeStatus();

	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: prilivi?page=pregledDipl");
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>
		<?php 
			getTitle(); 
		?>
		</title>

		<?php 
			include('includes/head.php'); 
			if (in_array($getUserIp, $getIpWhiteList))
			{
		?>
	</head>
	<body>
		<header>
		<?php
			include('header.php');
		?>
		</header>
		<div id="sidebar">
		<?php
			include('menu.php');
		?>
		</div>
		<!-- Content START -->
		<div id="content">
			<!--Container-fluid START -->
			<div class="container-fluid">
				<?php 
					switch($page)
					{
						case "pregledDipl":
							$time_start = microtime(true); //Vrijeme početak
							$sredinaTrenutniMjesec = date("Y-m-15");
							//-------------------------------------------------------------------------------------
							// OPĆENITO START 
							//-------------------------------------------------------------------------------------
							
								//Funkcija za određivanje pripadnosti određenom mjesecu START
									function odrediPripadnostKoloni($vrijeme){
										$sredinaMjeseca = date("Y-m-15");
										$vrijemeUlaz = date("Y-m-d H:i:s", strtotime($vrijeme));
										$rezultat = "";
										//------------------------------------------------------
										$colMinus5Min = date("Y-m-01 00:00:00", strtotime($sredinaMjeseca."-5 months"));
										$colMinus5Max = date("Y-m-t 23:59:59", strtotime($sredinaMjeseca."-5 months"));
										$colMinus4Min = date("Y-m-01 00:00:00", strtotime($sredinaMjeseca."-4 months"));
										$colMinus4Max = date("Y-m-t 23:59:59", strtotime($sredinaMjeseca."-4 months"));
										$colMinus3Min = date("Y-m-01 00:00:00", strtotime($sredinaMjeseca."-3 months"));
										$colMinus3Max = date("Y-m-t 23:59:59", strtotime($sredinaMjeseca."-3 months"));
										$colMinus2Min = date("Y-m-01 00:00:00", strtotime($sredinaMjeseca."-2 months"));
										$colMinus2Max = date("Y-m-t 23:59:59", strtotime($sredinaMjeseca."-2 months"));
										$colMinus1Min = date("Y-m-01 00:00:00", strtotime($sredinaMjeseca."-1 months"));
										$colMinus1Max = date("Y-m-t 23:59:59", strtotime($sredinaMjeseca."-1 months"));
										$colMin = date("Y-m-01 00:00:00");
										$colMax = date("Y-m-t 23:59:59");
										$colPlus1Min = date("Y-m-01 00:00:00", strtotime($sredinaMjeseca."+1 months"));
										$colPlus1Max = date("Y-m-t 23:59:59", strtotime($sredinaMjeseca."+1 months"));
										$colPlus2Min = date("Y-m-01 00:00:00", strtotime($sredinaMjeseca."+2 months"));
										$colPlus2Max = date("Y-m-t 23:59:59", strtotime($sredinaMjeseca."+2 months"));
										$colPlus3Min = date("Y-m-01 00:00:00", strtotime($sredinaMjeseca."+3 months"));
										$colPlus3Max = date("Y-m-t 23:59:59", strtotime($sredinaMjeseca."+3 months"));
										$colPlus4Min = date("Y-m-01 00:00:00", strtotime($sredinaMjeseca."+4 months"));
										$colPlus4Max = date("Y-m-t 23:59:59", strtotime($sredinaMjeseca."+4 months"));
										$colPlus5Min = date("Y-m-01 00:00:00", strtotime($sredinaMjeseca."+5 months"));
										$colPlus5Max = date("Y-m-t 23:59:59", strtotime($sredinaMjeseca."+5 months"));
										
										if($colMinus5Min <= $vrijemeUlaz AND $vrijemeUlaz <= $colMinus5Max){
											$rezultat = "colMin5";
										}else if($colMinus4Min <= $vrijemeUlaz AND $vrijemeUlaz <= $colMinus4Max){
											$rezultat = "colMin4";
										}else if($colMinus3Min <= $vrijemeUlaz AND $vrijemeUlaz <= $colMinus3Max){
											$rezultat = "colMin3";
										}else if($colMinus2Min <= $vrijemeUlaz AND $vrijemeUlaz <= $colMinus2Max){
											$rezultat = "colMin2";
										}else if($colMinus1Min <= $vrijemeUlaz AND $vrijemeUlaz <= $colMinus1Max){
											$rezultat = "colMin1";
										}else if($colMin <= $vrijemeUlaz AND $vrijemeUlaz <= $colMax){
											$rezultat = "col";
										}else if($colPlus1Min <= $vrijemeUlaz AND $vrijemeUlaz <= $colPlus1Max){
											$rezultat = "colPlus1";
										}else if($colPlus2Min <= $vrijemeUlaz AND $vrijemeUlaz <= $colPlus2Max){
											$rezultat = "colPlus2";
										}else if($colPlus3Min <= $vrijemeUlaz AND $vrijemeUlaz <= $colPlus3Max){
											$rezultat = "colPlus3";
										}else if($colPlus4Min <= $vrijemeUlaz AND $vrijemeUlaz <= $colPlus4Max){
											$rezultat = "colPlus4";
										}else if($colPlus5Min <= $vrijemeUlaz AND $vrijemeUlaz <= $colPlus5Max){
											$rezultat = "colPlus5";
										}else{
											$rezultat = "notIn";
										}
										
										return $rezultat;
									}
								//Funkcija za određivanje pripadnosti određenom mjesecu END 
								
								//inicijalizacija nizova za podatke START
									$prilivNiz = array(
										"colMin5" => array(
											"rata1" => array(),
											"rata2" => array(),
											"rata3" => array(),
											"rata4" => array(),
											"rata5" => array()
										),
										"colMin4" => array(
											"rata1" => array(),
											"rata2" => array(),
											"rata3" => array(),
											"rata4" => array(),
											"rata5" => array()
										),
										"colMin3" => array(
											"rata1" => array(),
											"rata2" => array(),
											"rata3" => array(),
											"rata4" => array(),
											"rata5" => array()
										),
										"colMin2" => array(
											"rata1" => array(),
											"rata2" => array(),
											"rata3" => array(),
											"rata4" => array(),
											"rata5" => array()
										),
										"colMin1" => array(
											"rata1" => array(),
											"rata2" => array(),
											"rata3" => array(),
											"rata4" => array(),
											"rata5" => array()
										),
										"col" => array(
											"rata1" => array(),
											"rata2" => array(),
											"rata3" => array(),
											"rata4" => array(),
											"rata5" => array()
										),
										"colPlus1" => array(
											"rata1" => array(),
											"rata2" => array(),
											"rata3" => array(),
											"rata4" => array(),
											"rata5" => array()
										),
										"colPlus2" => array(
											"rata1" => array(),
											"rata2" => array(),
											"rata3" => array(),
											"rata4" => array(),
											"rata5" => array()
										),
										"colPlus3" => array(
											"rata1" => array(),
											"rata2" => array(),
											"rata3" => array(),
											"rata4" => array(),
											"rata5" => array()
										),
										"colPlus4" => array(
											"rata1" => array(),
											"rata2" => array(),
											"rata3" => array(),
											"rata4" => array(),
											"rata5" => array()
										),
										"colPlus5" => array(
											"rata1" => array(),
											"rata2" => array(),
											"rata3" => array(),
											"rata4" => array(),
											"rata5" => array()
										)
									);
								
									$uplacenoNiz = array(
										"colMin5" => array(),
										"colMin4" => array(),
										"colMin3" => array(),
										"colMin2" => array(),
										"colMin1" => array(),
										"col" => array(),
										"colPlus1" => array(),
										"colPlus2" => array(),
										"colPlus3" => array(),
										"colPlus4" => array(),
										"colPlus5" => array()
									);
									
									$ocekivanoNiz = array(
										"colMin5" => array(),
										"colMin4" => array(),
										"colMin3" => array(),
										"colMin2" => array(),
										"colMin1" => array(),
										"col" => array(),
										"colPlus1" => array(),
										"colPlus2" => array(),
										"colPlus3" => array(),
										"colPlus4" => array(),
										"colPlus5" => array()
									);
								//inicijalizacija nizova za podatke END
								
							//-------------------------------------------------------------------------------------
							// OPĆENITO END 
							//-------------------------------------------------------------------------------------
							
							//-------------------------------------------------------------------------------------
							// DIPL MODUL START 
							//-------------------------------------------------------------------------------------
								//Provjera vrijednosti iz Filtera START 
									if(isset($_POST["drzavaFilter"])){
										$drzavaFilterImp = implode(",", $_POST["drzavaFilter"]);
										$drzavaFilterExp = explode(",", $drzavaFilterImp);
									}else{
										$drzavaFilterImp = "BAM,RSD";
										$drzavaFilterExp = explode(",", $drzavaFilterImp);
									}
									if(isset($_POST["valutaFilter"])){
										$valutaFilterImp = implode(",", $_POST["valutaFilter"]);
										$valutaFilterExp = explode(",", $valutaFilterImp);
									}else{
										$valutaFilterImp = "euro";
										$valutaFilterExp = explode(",", $valutaFilterImp);
									}
								//Provjera vrijednosti iz Filtera END 
								
								//vrste ugovora u bazi START
									$r1 = array(9,21,10,41,51,61,71,81);
									$r2 = array(1,22,2,42,52,62,72,82);
									$r3 = array(5,23,6,43,53,63,73,83);
									$r4 = array(7,24,8,44,54,64,74,84);
									$r5 = array(3,25,4,45,55,65,75,85);
									$r6 = array(26);
									$r12 = array(27);
									$rMikro = array(11,12);
								//vrste ugovora u bazi END
								
								//Uslovi za query prema postavljenim vrijednostima u filteru START
									//Uslov za valutu START
										$uslovValutaQuery = "";
										$uslovDrzavaIznos = "";
										if(in_array("rsd",$valutaFilterExp)){
											$uslovValutaQuery = "RSD";
											$uslovDrzavaIznos = "Srbija";
										}else if(in_array("bam",$valutaFilterExp)){
											$uslovValutaQuery = "BAM";
											$uslovDrzavaIznos = "BiH";
										}else{
											$uslovValutaQuery = "EUR";
											$uslovDrzavaIznos = "EU";
										}
									//Uslov za valutu END 
									
									//Uslov za drzavu START
										$uslovDrzavaQuery = "";
										$puslovDrzavaBAM = "";
										$puslovDrzavaRSD = "";
										if(in_array("BAM",$drzavaFilterExp)){
											$puslovDrzavaBAM = "'BAM'";
										}else{
											$puslovDrzavaBAM = "'BAMX'";
										}
										if(in_array("RSD",$drzavaFilterExp)){
											$puslovDrzavaRSD = "'RSD'";
										}else{
											$puslovDrzavaRSD = "'RSDX'";
										}
										$uslovDrzavaQuery = "(pred.pr_domaca_valuta IN (".$puslovDrzavaBAM.",".$puslovDrzavaRSD."))";
									//Uslov za drzavu END
								//Uslovi za query prema postavljenim vrijednostima u filteru END
								
								
								$query1 = $db->prepare("
									SELECT
										kan.id_broj_nd_kandidata AS kanID, 
										kan.status_nd_kandidata AS kanStatus, 
										kan.pstatus_nd_kandidata AS kanPstatus, 
										kan.vrsta_ugovora_nd_kandidata AS kanVrstaUgovora, 
										pred.pr_datum_kreiranja AS predDatumKreiranja, 
										pred.pr_datum_uplate AS predDatumUplate, 
										pred.pr_domaca_valuta AS predDomacaValuta, 
										pred.pr_vrijednost_".$uslovValutaQuery." AS predVrijednostU, 
										pred.pr_rata AS predRata
									FROM 
										idk_nd_kandidata kan
									INNER JOIN 
										idk_predracuni pred
									ON 
										kan.id_broj_nd_kandidata = pred.pr_kandidat_id
									WHERE  
										pred.pr_vrsta_predracuna = 1
										AND 
										pred.pr_rata = 1
										AND 
										pred.pr_status = 2
										AND 
										pred.pr_uplaceno = 1
										AND 
										".$uslovDrzavaQuery." 
									ORDER BY 
										pred.pr_datum_kreiranja ASC
								");
								$query1->execute();
								while($row1 = $query1->fetch()){
									$kanID = intval($row1["kanID"]);
									$kanStatus = intval($row1["kanStatus"]);
									$kanPstatus = intval($row1["kanPstatus"]);
									$kanVrstaUgovora = intval($row1["kanVrstaUgovora"]);
									$predDatumKreiranja = $row1["predDatumKreiranja"];
									$predDatumUplate = $row1["predDatumUplate"];
									$predDomacaValuta = $row1["predDomacaValuta"];
									$predVrijednostU = $row1["predVrijednostU"];
									$predRata = intval($row1["predRata"]);
									$rezRata = "rata".$predRata; 
									$datumUplateZadnjeUplacene = $predDatumUplate;
									$zadnjaUplacenaBr = $predRata;
									//Na osnovu vrste ugovora određuje se broj rata START 
									$brojRata = 0;
									if(in_array($kanVrstaUgovora, $r1)){
										$brojRata = 1;
									}else if(in_array($kanVrstaUgovora, $r2)){
										$brojRata = 2;
									}else if(in_array($kanVrstaUgovora, $r3)){ 
										$brojRata = 3;
									}else if(in_array($kanVrstaUgovora, $r4)){
										$brojRata = 4;
									}else if(in_array($kanVrstaUgovora, $r5)){
										$brojRata = 5;
									}else if(in_array($kanVrstaUgovora, $r6)){
										$brojRata = 6;
									}else if(in_array($kanVrstaUgovora, $r12)){
										$brojRata = 12;
									}else if(in_array($kanVrstaUgovora, $rMikro)){
										$brojRata = 1;
									}else{
										$brojRata = 0;
									}
									//Na osnovu vrste ugovora određuje se broj rata END 
									
									//Radi dalje ako je broj rata različit od 0 START
									if($brojRata != 0){
										$rezPripadnost = odrediPripadnostKoloni($predDatumUplate); 
										if($rezPripadnost != "notIn"){
											array_push($prilivNiz[$rezPripadnost][$rezRata], $predVrijednostU);
											array_push($uplacenoNiz[$rezPripadnost], $predVrijednostU);
											array_push($ocekivanoNiz[$rezPripadnost], $predVrijednostU);
										}
										
										if($brojRata != 1){
											if($brojRata == 2){
												//Za broj rata "2" provjeravam samo da li postoji generisana druga rata
												$query2 = $db->prepare("
													SELECT 
														pr_datum_kreiranja, 
														pr_rata, 
														pr_status,
														pr_vrijednost_".$uslovValutaQuery." AS predVr, 
														pr_uplaceno, 
														pr_datum_uplate
													FROM 
														idk_predracuni
													WHERE 
														pr_kandidat_id = ".$kanID."
														AND 
														pr_rata = 2
														AND 
														pr_status != 0 
												");
												$query2->execute();
												if(intval($query2->rowCount()) != 0){
													$row2 = $query2->fetch();
													$pr_uplaceno = intval($row2["pr_uplaceno"]);
													if($pr_uplaceno == 0){
														$pr_status = $row2["pr_status"];
														$pr_datum_kreiranja = $row2["pr_datum_kreiranja"];
														$predVr = $row2["predVr"];
														// if($pr_status == 1){
															// $pr_datum_kreiranja = date("Y-m-d H:i:s", strtotime($pr_datum_kreiranja."+21 days"));
														// }else if($pr_status == 3){
															// $pr_datum_kreiranja = date("Y-m-d H:i:s", strtotime($pr_datum_kreiranja."+40 days"));
														// }else if($pr_status == 4){
															// $pr_datum_kreiranja = date("Y-m-d H:i:s", strtotime($pr_datum_kreiranja."+90 days"));
														// }else{
															// $pr_datum_kreiranja = date("Y-m-d H:i:s", strtotime($pr_datum_kreiranja."+120 days"));
														// }
														$rezPripadnost = odrediPripadnostKoloni($pr_datum_kreiranja); 
														if($rezPripadnost != "notIn"){
															array_push($prilivNiz[$rezPripadnost]["rata2"], $predVr);
															//array_push($uplacenoNiz[$rezPripadnost], $predVr);
															array_push($ocekivanoNiz[$rezPripadnost], $predVr);
														}
													}else{
														$pr_datum_uplate = $row2["pr_datum_uplate"];
														$predVr = $row2["predVr"];
														$rezPripadnost = odrediPripadnostKoloni($pr_datum_uplate); 
														if($rezPripadnost != "notIn"){
															array_push($prilivNiz[$rezPripadnost]["rata2"], $predVr);
															array_push($uplacenoNiz[$rezPripadnost], $predVr);
															array_push($ocekivanoNiz[$rezPripadnost], $predVr);
														}
													}
												}else{
													$pr_datum_kreiranja = date("Y-m-d H:i:s", strtotime($datumUplateZadnjeUplacene."+4 months"));
													if($uslovDrzavaIznos != "EU"){
														$predVr = getIznosRate($kanVrstaUgovora, $uslovDrzavaIznos, "rata2");
													}else{
														$predVr = getIznosRate($kanVrstaUgovora, "BiH", "rata2");
														$predVr = $predVr/1.95583;
														$predVr = number_format((float)$predVr, 2, '.', '');
													}
													
													$rezPripadnost = odrediPripadnostKoloni($pr_datum_kreiranja); 
													if($rezPripadnost != "notIn"){
														array_push($prilivNiz[$rezPripadnost]["rata2"], $predVr);
														//array_push($uplacenoNiz[$rezPripadnost], $predVr);
														array_push($ocekivanoNiz[$rezPripadnost], $predVr);
													}
												}
											}else{
												//Za broj rata "3,4,5" provjeravam svaku ratu posebno prolazeci kroz petlju
												$j = 0;
												for($i = 2; $i <= $brojRata; $i++){
													
													$j = $i - $zadnjaUplacenaBr; 
													$query2 = $db->prepare("
														SELECT 
															pr_datum_kreiranja, 
															pr_rata, 
															pr_status,
															pr_vrijednost_".$uslovValutaQuery." AS predVr, 
															pr_uplaceno, 
															pr_datum_uplate
														FROM 
															idk_predracuni
														WHERE 
															pr_kandidat_id = ".$kanID."
															AND 
															pr_rata = ".$i."
															AND 
															pr_status != 0 
													");
													$query2->execute();
													if(intval($query2->rowCount()) != 0){
														$row2 = $query2->fetch();
														$pr_uplaceno = intval($row2["pr_uplaceno"]);
														if($pr_uplaceno == 0){
															$pr_status = $row2["pr_status"];
															$pr_datum_kreiranja = $row2["pr_datum_kreiranja"];
															$predVr = $row2["predVr"];
															// if($pr_status == 1){
																// $pr_datum_kreiranja = date("Y-m-d H:i:s", strtotime($pr_datum_kreiranja."+21 days"));
															// }else if($pr_status == 3){
																// $pr_datum_kreiranja = date("Y-m-d H:i:s", strtotime($pr_datum_kreiranja."+40 days"));
															// }else if($pr_status == 4){
																// $pr_datum_kreiranja = date("Y-m-d H:i:s", strtotime($pr_datum_kreiranja."+90 days"));
															// }else{
																// $pr_datum_kreiranja = date("Y-m-d H:i:s", strtotime($pr_datum_kreiranja."+120 days"));
															// }
															$rezPripadnost = odrediPripadnostKoloni($pr_datum_kreiranja); 
															if($rezPripadnost != "notIn"){
																array_push($prilivNiz[$rezPripadnost]["rata".$i], $predVr);
																//array_push($uplacenoNiz[$rezPripadnost], $predVr);
																array_push($ocekivanoNiz[$rezPripadnost], $predVr);
															}
														}else{
															$pr_datum_uplate = $row2["pr_datum_uplate"];
															$datumUplateZadnjeUplacene = $pr_datum_uplate;
															$zadnjaUplacenaBr = $i;
															$predVr = $row2["predVr"];
															$rezPripadnost = odrediPripadnostKoloni($datumUplateZadnjeUplacene); 
															if($rezPripadnost != "notIn"){
																array_push($prilivNiz[$rezPripadnost]["rata".$i], $predVr);
																array_push($uplacenoNiz[$rezPripadnost], $predVr);
																array_push($ocekivanoNiz[$rezPripadnost], $predVr);
															}
														}
													}else{
														$pr_datum_kreiranja = date("Y-m-d H:i:s", strtotime($datumUplateZadnjeUplacene."+".$j." months"));
														//$pr_datum_kreiranja = date("Y-m-d H:i:s", strtotime($pr_datum_kreiranja."+21 days"));
														if($uslovDrzavaIznos != "EU"){
															$predVr = getIznosRate($kanVrstaUgovora, $uslovDrzavaIznos, "rata".$i);
														}else{
															$predVr = getIznosRate($kanVrstaUgovora, "BiH", "rata".$i);
															$predVr = $predVr/1.95583;
															$predVr = number_format((float)$predVr, 2, '.', '');
														}
														
														$rezPripadnost = odrediPripadnostKoloni($pr_datum_kreiranja); 
														if($rezPripadnost != "notIn"){
															array_push($prilivNiz[$rezPripadnost]["rata".$i], $predVr);
															//array_push($uplacenoNiz[$rezPripadnost], $predVr);
															array_push($ocekivanoNiz[$rezPripadnost], $predVr);
														}
													}
												}
											}
										}
									}
									//Radi dalje ako je broj rata različit od 0 END 
								}
							//-------------------------------------------------------------------------------------
							// DIPL MODUL END  
							//-------------------------------------------------------------------------------------
							$time_end = microtime(true); //Vrijeme kraj
							$execution_time = $time_end - $time_start; //Rezultat izvršavanja između Vrijeme početak i Vrijeme kraj
							$execution_time = number_format((float)$execution_time, 4, '.', ''); //Rezultat izvršavanja između Vrijeme početak i Vrijeme kraj
							
				?>
							<div class = "row">
								<div class = "col-xs-9 idk_color_green">
									<h1>
										<i class="fa fa-money" aria-hidden="true" style = "margin-right: 10px;"></i>
										Prilivi DIPL 
									</h1>
								</div>
								<div class = "col-xs-3 text-right">
									<!-- prazno mjesto -->
								</div>
							</div>
							<div class="row" style = "margin-top: 20px;">
								<div class="col-md-12">
									<div class="content_box">
										<div class="row">
											<div class="col-xs-12">
												<div class="panel-group material-accordion material-accordion_success" id="accordion1">
													<div class="panel panel-default material-accordion__panel material-accordion__panel">
														<div class="panel-heading material-accordion__heading">
															<h4 class="panel-title">
															<a class="material-accordion__title" style="margin-bottom:0.3rem" data-toggle="collapse" data-parent="#accordion1" href="#filterPrilivi"><i class="fa fa-search" aria-hidden="true" style = "margin-right: 10px;"></i>Filter</a>
															</h4>
														</div>
														<div id="filterPrilivi" class="panel-collapse collapse material-accordion__collapse">
															<div class="panel-body">
																<form action="<?php getSiteURL(); ?>prilivi?page=pregledDipl" enctype="multipart/form-data" method="post" role="form" class="form-horizontal" id = "formaFilter">
																	<div class="form-group">
																		<div class="col-md-offset-2 col-sm-8 text-center">
																			<label for="drzavaFilter" class="col-sm-4 control-label">
																				Država:
																			</label>
																			<div class="col-sm-8">
																				<div class="">
																					<select class="selectpicker" title = "Odaberite državu kandidata" data-actions-box="true" data-live-search = "true" id="drzavaFilter" name="drzavaFilter[]" data-selected-text-format = "count > 2" multiple required>
																						<option <?php if(in_array("BAM", $drzavaFilterExp)) echo "selected"; ?> value = "BAM">BiH</option>
																						<option <?php if(in_array("RSD", $drzavaFilterExp)) echo "selected"; ?> value = "RSD">SRB</option>
																						<!--<option <?php //if(in_array("49", $drzavaFilterExp)) echo "selected"; ?> value = "49">DE</option>
																						<option <?php //if(in_array("ostalo", $drzavaFilterExp)) echo "selected"; ?> value = "ostalo">Ostalo</option>-->
																					</select>
																				</div>
																			</div>
																		</div>
																	</div>
																	<div class="form-group">
																		<div class="col-md-offset-2 col-sm-8 text-center">
																			<label for="valutaFilter" class="col-sm-4 control-label">
																				Prikaži u valuti:
																			</label>
																			<div class="col-sm-8">
																				<div class="">
																					<select class="selectpicker" title = "Odaberite valutu za prikaz" data-actions-box="true" data-live-search = "true" id="valutaFilter" name="valutaFilter[]" required>
																						<option <?php if(in_array("bam", $valutaFilterExp)) echo "selected"; ?> value = "bam">BAM</option>
																						<option <?php if(in_array("rsd", $valutaFilterExp)) echo "selected"; ?> value = "rsd">RSD</option>
																						<option <?php if(in_array("euro", $valutaFilterExp)) echo "selected"; ?> value = "euro">EURO</option>
																					</select>
																				</div>
																			</div>
																		</div>
																	</div>
																	<div class="form-group">
																		<div class="col-md-offset-2 col-sm-8 text-center">
																			<button type="submit" class="btn btn-primary material-btn material-btn_success" form="formaFilter"><i class="fa fa-search" aria-hidden="true"></i> Traži</button>
																		</div>
																	</div>
																</form>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="row" style = "margin-top: 20px;">
											<div class="col-xs-12 text-center">
												<?php 
													$drzavaValExp = array();
													$drzavaValImp = "";
													foreach($drzavaFilterExp AS $drzavaValImg){
														if($drzavaValImg == "BAM"){
															array_push($drzavaValExp, '<img src="'.getSiteUrlr().'images/bs3d.png" width=40>');
														}else if($drzavaValImg == "RSD"){
															array_push($drzavaValExp, '<img src="'.getSiteUrlr().'images/sr3d.png" width=40>');
														}else if($drzavaValImg == "EUR"){
															array_push($drzavaValExp, '<img src="'.getSiteUrlr().'images/de3d.png" width=40>');
														}else{
															array_push($drzavaValExp, '<img src="'.getSiteUrlr().'images/globe3d.png" width=40>');
														}
													}
													$drzavaValImp = implode('<span style = "padding-left:15px; padding-right:15px;"> </span>', $drzavaValExp);
													echo $drzavaValImp; 
												?>
											</div>
										</div>
										<div class="row" style = "margin-top: 20px;">
											<div class="col-xs-12 text-center">
												<div class = "row">
													<div class = "col-md-offset-1 col-md-10 text-center">
														<canvas id="myChart" height="350"></canvas>
														<script>
															var ctx = document.getElementById('myChart').getContext('2d');
															var chart = new Chart(ctx, {
																// The type of chart we want to create
																type: 'line',
																
																// The data for our dataset
																data: {
																	labels: [
																		'<?php echo date("M Y", strtotime($sredinaTrenutniMjesec."-5 months"));?>', 
																		'<?php echo date("M Y", strtotime($sredinaTrenutniMjesec."-4 months"));?>', 
																		'<?php echo date("M Y", strtotime($sredinaTrenutniMjesec."-3 months"));?>', 
																		'<?php echo date("M Y", strtotime($sredinaTrenutniMjesec."-2 months"));?>', 
																		'<?php echo date("M Y", strtotime($sredinaTrenutniMjesec."-1 months"));?>', 
																		'<?php echo date("M Y", strtotime($sredinaTrenutniMjesec));?>', 
																		'<?php echo date("M Y", strtotime($sredinaTrenutniMjesec."+1 months"));?>',
																		'<?php echo date("M Y", strtotime($sredinaTrenutniMjesec."+2 months"));?>',
																		'<?php echo date("M Y", strtotime($sredinaTrenutniMjesec."+3 months"));?>',
																		'<?php echo date("M Y", strtotime($sredinaTrenutniMjesec."+4 months"));?>',
																		'<?php echo date("M Y", strtotime($sredinaTrenutniMjesec."+5 months"));?>'
																	],
																	datasets: [
																		{
																			label: 'Uplaćeno',
																			data: [
																				<?php echo array_sum($uplacenoNiz["colMin5"]); ?>, 
																				<?php echo array_sum($uplacenoNiz["colMin4"]); ?>, 
																				<?php echo array_sum($uplacenoNiz["colMin3"]); ?>, 
																				<?php echo array_sum($uplacenoNiz["colMin2"]); ?>, 
																				<?php echo array_sum($uplacenoNiz["colMin1"]); ?>, 
																				<?php echo array_sum($uplacenoNiz["col"]); ?>, 
																				<?php echo array_sum($uplacenoNiz["colPlus1"]); ?>, 
																				<?php echo array_sum($uplacenoNiz["colPlus2"]); ?>, 
																				<?php echo array_sum($uplacenoNiz["colPlus3"]); ?>, 
																				<?php echo array_sum($uplacenoNiz["colPlus4"]); ?>, 
																				<?php echo array_sum($uplacenoNiz["colPlus5"]); ?>
																			],
																			backgroundColor: "rgba(69,165,255,0.7)"
																		},
																		{
																			label: 'Očekivano',
																			data: [
																				<?php echo array_sum($ocekivanoNiz["colMin5"]); ?>, 
																				<?php echo array_sum($ocekivanoNiz["colMin4"]); ?>, 
																				<?php echo array_sum($ocekivanoNiz["colMin3"]); ?>, 
																				<?php echo array_sum($ocekivanoNiz["colMin2"]); ?>, 
																				<?php echo array_sum($ocekivanoNiz["colMin1"]); ?>, 
																				<?php echo array_sum($ocekivanoNiz["col"]); ?>, 
																				<?php echo array_sum($ocekivanoNiz["colPlus1"]); ?>, 
																				<?php echo array_sum($ocekivanoNiz["colPlus2"]); ?>, 
																				<?php echo array_sum($ocekivanoNiz["colPlus3"]); ?>, 
																				<?php echo array_sum($ocekivanoNiz["colPlus4"]); ?>, 
																				<?php echo array_sum($ocekivanoNiz["colPlus5"]); ?>
																			],
																			backgroundColor: "rgba(255,20,8,0.7)"
																		}
																		
																	]
																},
																// Configuration options go here
																options: {
																	responsive: true,
																	maintainAspectRatio: false,
																	scales: {
																		yAxes: [{
																			ticks: {
																				// Include a dollar sign in the ticks
																				callback: function(value, index, values) {
																					const currencyFractionDigits = new Intl.NumberFormat('de-DE', {
																						style: 'currency',
																						currency: 'EUR',
																					}).resolvedOptions().maximumFractionDigits;

																					return (value).toLocaleString('de-DE', { maximumFractionDigits: currencyFractionDigits }) + ' <?php echo $uslovValutaQuery; ?>';
																				}
																			}
																		}]
																	}
																}
															});
														</script>
													</div>
												</div>
											</div>
										</div>
										<div class="row" style = "margin-top: 20px;">
											<div class="col-xs-12" style = "overflow-y: auto !important;">
												<script type="text/javascript">
													$(document).ready(function() {
														$('#kandidatiTable').DataTable({

															responsive: false,

															"order": [[ 0, "asc" ]],

															"bAutoWidth": false,
															"bInfo": false,
															"bPaginate": false,
															"bLengthChange": false,
															"aoColumns": [
																	{ "width": "12%" },
																	{ "width": "8%", "bSortable": false },
																	{ "width": "8%", "bSortable": false },
																	{ "width": "8%", "bSortable": false },
																	{ "width": "8%", "bSortable": false },
																	{ "width": "8%", "bSortable": false },
																	{ "width": "8%", "bSortable": false },
																	{ "width": "8%", "bSortable": false },
																	{ "width": "8%", "bSortable": false },
																	{ "width": "8%", "bSortable": false },
																	{ "width": "8%", "bSortable": false },
																	{ "width": "8%", "bSortable": false }
																]
														});
													});
												</script>
												<table id="kandidatiTable" class="display" cellspacing="0" width="100%">
													<thead>
														<tr>
															<th class="text-center">Rata/Mjesec</th>
															<th class="text-center"><?php echo date("M Y", strtotime($sredinaTrenutniMjesec."-5 months"));?></th>
															<th class="text-center"><?php echo date("M Y", strtotime($sredinaTrenutniMjesec."-4 months"));?></th>
															<th class="text-center"><?php echo date("M Y", strtotime($sredinaTrenutniMjesec."-3 months"));?></th>
															<th class="text-center"><?php echo date("M Y", strtotime($sredinaTrenutniMjesec."-2 months"));?></th>
															<th class="text-center"><?php echo date("M Y", strtotime($sredinaTrenutniMjesec."-1 months"));?></th>
															<th class="text-center"><?php echo date("M Y");?></th>
															<th class="text-center"><?php echo date("M Y", strtotime($sredinaTrenutniMjesec."+1 months"));?></th>
															<th class="text-center"><?php echo date("M Y", strtotime($sredinaTrenutniMjesec."+2 months"));?></th>
															<th class="text-center"><?php echo date("M Y", strtotime($sredinaTrenutniMjesec."+3 months"));?></th>
															<th class="text-center"><?php echo date("M Y", strtotime($sredinaTrenutniMjesec."+4 months"));?></th>
															<th class="text-center"><?php echo date("M Y", strtotime($sredinaTrenutniMjesec."+5 months"));?></th>
														</tr>
													</thead>
													<tbody>
														<tr>
															<td class="text-center" style ="font-weight: bold;">Rata 1</td>
															<td class="text-center" style = "background-color: #424242; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colMin5"]["rata1"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "background-color: #616161; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colMin4"]["rata1"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "background-color: #757575; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colMin3"]["rata1"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "background-color: #9E9E9E; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colMin2"]["rata1"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "background-color: #BDBDBD; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colMin1"]["rata1"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "border-left: 1px solid black; border-right: 1px solid black;"><?php echo number_format((float)array_sum($prilivNiz["col"]["rata1"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "background-color: #78909C; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colPlus1"]["rata1"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "background-color: #607D8B; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colPlus2"]["rata1"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "background-color: #546E7A; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colPlus3"]["rata1"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "background-color: #455A64; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colPlus4"]["rata1"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "background-color: #37474F; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colPlus5"]["rata1"]), 2, ',', '.'); ?></td>
														</tr>
														<tr>
															<td class="text-center" style ="font-weight: bold;">Rata 2</td>
															<td class="text-center" style = "background-color: #424242; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colMin5"]["rata2"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "background-color: #616161; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colMin4"]["rata2"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "background-color: #757575; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colMin3"]["rata2"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "background-color: #9E9E9E; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colMin2"]["rata2"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "background-color: #BDBDBD; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colMin1"]["rata2"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "border-left: 1px solid black; border-right: 1px solid black;"><?php echo number_format((float)array_sum($prilivNiz["col"]["rata2"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "background-color: #78909C; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colPlus1"]["rata2"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "background-color: #607D8B; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colPlus2"]["rata2"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "background-color: #546E7A; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colPlus3"]["rata2"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "background-color: #455A64; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colPlus4"]["rata2"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "background-color: #37474F; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colPlus5"]["rata2"]), 2, ',', '.'); ?></td>
														</tr>
														<tr>
															<td class="text-center" style ="font-weight: bold;">Rata 3</td>
															<td class="text-center" style = "background-color: #424242; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colMin5"]["rata3"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "background-color: #616161; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colMin4"]["rata3"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "background-color: #757575; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colMin3"]["rata3"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "background-color: #9E9E9E; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colMin2"]["rata3"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "background-color: #BDBDBD; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colMin1"]["rata3"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "border-left: 1px solid black; border-right: 1px solid black;"><?php echo number_format((float)array_sum($prilivNiz["col"]["rata3"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "background-color: #78909C; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colPlus1"]["rata3"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "background-color: #607D8B; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colPlus2"]["rata3"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "background-color: #546E7A; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colPlus3"]["rata3"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "background-color: #455A64; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colPlus4"]["rata3"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "background-color: #37474F; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colPlus5"]["rata3"]), 2, ',', '.'); ?></td>
														</tr>
														<tr>
															<td class="text-center" style ="font-weight: bold;">Rata 4</td>
															<td class="text-center" style = "background-color: #424242; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colMin5"]["rata4"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "background-color: #616161; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colMin4"]["rata4"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "background-color: #757575; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colMin3"]["rata4"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "background-color: #9E9E9E; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colMin2"]["rata4"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "background-color: #BDBDBD; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colMin1"]["rata4"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "border-left: 1px solid black; border-right: 1px solid black;"><?php echo number_format((float)array_sum($prilivNiz["col"]["rata4"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "background-color: #78909C; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colPlus1"]["rata4"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "background-color: #607D8B; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colPlus2"]["rata4"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "background-color: #546E7A; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colPlus3"]["rata4"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "background-color: #455A64; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colPlus4"]["rata4"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "background-color: #37474F; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colPlus5"]["rata4"]), 2, ',', '.'); ?></td>
														</tr>
														<tr>
															<td class="text-center" style ="font-weight: bold;">Rata 5</td>
															<td class="text-center" style = "background-color: #424242; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colMin5"]["rata5"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "background-color: #616161; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colMin4"]["rata5"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "background-color: #757575; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colMin3"]["rata5"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "background-color: #9E9E9E; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colMin2"]["rata5"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "background-color: #BDBDBD; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colMin1"]["rata5"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "border-left: 1px solid black; border-right: 1px solid black;"><?php echo number_format((float)array_sum($prilivNiz["col"]["rata5"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "background-color: #78909C; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colPlus1"]["rata5"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "background-color: #607D8B; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colPlus2"]["rata5"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "background-color: #546E7A; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colPlus3"]["rata5"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "background-color: #455A64; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colPlus4"]["rata5"]), 2, ',', '.'); ?></td>
															<td class="text-center" style = "background-color: #37474F; color:white;"><?php echo number_format((float)array_sum($prilivNiz["colPlus5"]["rata5"]), 2, ',', '.'); ?></td>
														</tr>
													</tbody>
													<tfoot>
														<tr>
															<th class="text-center">Očekivano</th>
															<th class="text-center"><?php echo number_format((float)array_sum($ocekivanoNiz["colMin5"]), 2, ',', '.'); ?></th>
															<th class="text-center"><?php echo number_format((float)array_sum($ocekivanoNiz["colMin4"]), 2, ',', '.'); ?></th>
															<th class="text-center"><?php echo number_format((float)array_sum($ocekivanoNiz["colMin3"]), 2, ',', '.'); ?></th>
															<th class="text-center"><?php echo number_format((float)array_sum($ocekivanoNiz["colMin2"]), 2, ',', '.'); ?></th>
															<th class="text-center"><?php echo number_format((float)array_sum($ocekivanoNiz["colMin1"]), 2, ',', '.'); ?></th>
															<th class="text-center" style = "border-left: 1px solid black; border-right: 1px solid black;"><?php echo number_format((float)array_sum($ocekivanoNiz["col"]), 2, ',', '.'); ?></th>
															<th class="text-center"><?php echo number_format((float)array_sum($ocekivanoNiz["colPlus1"]), 2, ',', '.'); ?></th>
															<th class="text-center"><?php echo number_format((float)array_sum($ocekivanoNiz["colPlus2"]), 2, ',', '.'); ?></th>
															<th class="text-center"><?php echo number_format((float)array_sum($ocekivanoNiz["colPlus3"]), 2, ',', '.'); ?></th>
															<th class="text-center"><?php echo number_format((float)array_sum($ocekivanoNiz["colPlus4"]), 2, ',', '.'); ?></th>
															<th class="text-center"><?php echo number_format((float)array_sum($ocekivanoNiz["colPlus5"]), 2, ',', '.'); ?></th>
														</tr>
														<tr>
															<th class="text-center">Uplaćeno</th>
															<th class="text-center"><?php echo number_format((float)array_sum($uplacenoNiz["colMin5"]), 2, ',', '.'); ?></th>
															<th class="text-center"><?php echo number_format((float)array_sum($uplacenoNiz["colMin4"]), 2, ',', '.'); ?></th>
															<th class="text-center"><?php echo number_format((float)array_sum($uplacenoNiz["colMin3"]), 2, ',', '.'); ?></th>
															<th class="text-center"><?php echo number_format((float)array_sum($uplacenoNiz["colMin2"]), 2, ',', '.'); ?></th>
															<th class="text-center"><?php echo number_format((float)array_sum($uplacenoNiz["colMin1"]), 2, ',', '.'); ?></th>
															<th class="text-center" style = "border-left: 1px solid black; border-right: 1px solid black;"><?php echo number_format((float)array_sum($uplacenoNiz["col"]), 2, ',', '.'); ?></th>
															<th class="text-center"><?php echo number_format((float)array_sum($uplacenoNiz["colPlus1"]), 2, ',', '.'); ?></th>
															<th class="text-center"><?php echo number_format((float)array_sum($uplacenoNiz["colPlus2"]), 2, ',', '.'); ?></th>
															<th class="text-center"><?php echo number_format((float)array_sum($uplacenoNiz["colPlus3"]), 2, ',', '.'); ?></th>
															<th class="text-center"><?php echo number_format((float)array_sum($uplacenoNiz["colPlus4"]), 2, ',', '.'); ?></th>
															<th class="text-center"><?php echo number_format((float)array_sum($uplacenoNiz["colPlus5"]), 2, ',', '.'); ?></th>
														</tr>
													</tfoot>
												</table>
											</div>
										</div>
										<div class="row" style = "margin-top: 20px;">
											<div class="col-xs-12">
												<p>Vrijeme obrade: <?php echo $execution_time; ?> sekundi</p><br>
											</div>
										</div>
									</div>
								</div>
							</div>
				<?php 
							//Unset prethodno definisanih nizove START 
								unset($prilivNiz);
								unset($uplacenoNiz);
								unset($ocekivanoNiz);
								unset($drzavaFilterExp);
								unset($valutaFilterExp);
								unset($r1);
								unset($r2);
								unset($r3);
								unset($r4);
								unset($r5);
								unset($r6);
								unset($r12);
								unset($rMikro);
							//Unset prethodno definisanih nizove END 
						break;
					}
				?>
			<footer><?php getCopyright(); ?></footer>
			</div>
			<!--Container-fluid END -->
		</div>
		<!-- Content END -->
	</body>
</html>
<?php 
			}
			else
			{			
				echo 
					'
						<br/>
						<div class="alert material-alert material-alert_danger">
							<h4>
								NEMATE PRIVILEGIJE!
							</h4>
							<p>
								Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.
							</p>
							<br />
						</div>
					';	
			} 
?>