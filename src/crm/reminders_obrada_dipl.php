<?php
	include("includes/functions.php");
	include("includes/common.php");

	$getEmployeeStatus = getEmployeeStatus();

	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: reminders_obrada_dipl?page=pregledKandidata");
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
						case "pregledKandidata":
							if(isset($_POST["drzavaFilter"])){
								$drzavaFilterImp = implode(",", $_POST["drzavaFilter"]);
								$drzavaFilterExp = explode(",", $drzavaFilterImp);
							}else{
								$drzavaFilterImp = "387,381,49,ostalo"; //"387,381,49,ostalo";
								$drzavaFilterExp = explode(",", $drzavaFilterImp);
							}
							
							if(isset($_POST["statusFilter"])){
								$statusFilterImp = implode(",", $_POST["statusFilter"]);
								$statusFilterExp = explode(",", $statusFilterImp);
							}else{
								$statusFilterImp = "2"; //"2,3,4,5";
								$statusFilterExp = explode(",", $statusFilterImp);
							}
							
							if(isset($_POST["brojKomunikacijaFilter"]) AND $_POST["brojKomunikacijaFilter"] != ""){
								$brojKomunikacijaFilter = intval($_POST["brojKomunikacijaFilter"]);
							}else{
								$brojKomunikacijaFilter = NULL;
							}
							
							if(isset($_POST["brojStatusFilter"]) AND $_POST["brojStatusFilter"] != ""){
								$brojStatusFilter = intval($_POST["brojStatusFilter"]);
							}else{
								$brojStatusFilter = NULL;
							}
							
				?>
							<div class = "row">
								<div class = "col-xs-9 idk_color_green">
									<h1>
										<i class="fa fa-bell" aria-hidden="true" style = "margin-right: 10px;"></i>
										Reminder Obrada DIPL 
									</h1>
								</div>
								<div class = "col-xs-3 text-right">
									<a href="<?php getSiteURL(); ?>reminders_obrada_dipl?page=pregledKandidata" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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
															<a class="material-accordion__title" style="margin-bottom:0.3rem" data-toggle="collapse" data-parent="#accordion1" href="#filterKandidata"><i class="fa fa-search" aria-hidden="true" style = "margin-right: 10px;"></i>Filter</a>
															</h4>
														</div>
														<div id="filterKandidata" class="panel-collapse collapse material-accordion__collapse">
															<div class="panel-body">
																<form action="<?php getSiteURL(); ?>reminders_obrada_dipl?page=pregledKandidata" enctype="multipart/form-data" method="post" role="form" class="form-horizontal" id = "formaFilter">
																	<div class="form-group">
																		<div class="col-md-offset-2 col-sm-8 text-center">
																			<label for="drzavaFilter" class="col-sm-4 control-label">
																				Država:
																			</label>
																			<div class="col-sm-8">
																				<div class="">
																					<select class="selectpicker" title = "Odaberite državu kandidata" data-actions-box="true" data-live-search = "true" id="drzavaFilter" name="drzavaFilter[]" data-selected-text-format = "count > 2" multiple>
																						<option <?php if(in_array("387", $drzavaFilterExp)) echo "selected"; ?> value = "387">BiH</option>
																						<option <?php if(in_array("381", $drzavaFilterExp)) echo "selected"; ?> value = "381">SRB</option>
																						<option <?php if(in_array("49", $drzavaFilterExp)) echo "selected"; ?> value = "49">DE</option>
																						<option <?php if(in_array("ostalo", $drzavaFilterExp)) echo "selected"; ?> value = "ostalo">Ostalo</option>
																					</select>
																				</div>
																			</div>
																		</div>
																	</div>
																	<div class="form-group">
																		<div class="col-md-offset-2 col-sm-8 text-center">
																			<label for="statusFilter" class="col-sm-4 control-label">
																				Status:
																			</label>
																			<div class="col-sm-8">
																				<div class="">
																					<select class="selectpicker" title = "Odaberite status kandidata" data-actions-box="true" data-live-search = "true" id="statusFilter" name="statusFilter[]" data-selected-text-format = "count > 2">
																						<option <?php if(in_array("2", $statusFilterExp)) echo "selected"; ?> value = "2">Prikupljanje dokumentacije</option>
																						<option <?php if(in_array("3", $statusFilterExp)) echo "selected"; ?> value = "3">Poslana pošta</option>
																						<option <?php if(in_array("4", $statusFilterExp)) echo "selected"; ?> value = "4">U obradi</option>
																						<option <?php if(in_array("5", $statusFilterExp)) echo "selected"; ?> value = "5">Dopuna dokumentacije</option>
																					</select>
																				</div>
																			</div>
																		</div>
																	</div>
																	<div class="form-group" id = "alertBrojKomunikacija">
																		<div class="col-md-offset-2 col-sm-8 text-center">
																			<div id = "messager_type_1" class="alert alert-danger" role="alert">
																				Unešena vrijednost je izvan dozvoljenih granica. <br>
																				Broj se može nalaziti u intervalu izmedju <span style = "font-weight: bold;">1</span> i <span style = "font-weight: bold;">300</span>, uključujući i granice intervala. 
																			</div>
																		</div>
																	</div>
																	<div class="form-group" id = "brKomSH">
																		<div class="col-md-offset-2 col-sm-8 text-center">
																			<label for="brojKomunikacijaFilter" class="col-sm-4 control-label">
																				Broj dana od zadnje komunikacije:
																			</label>
																			<div class="col-sm-8">
																				<div class="materail-input-block materail-input-block_success">
																					<input class="form-control materail-input" type="number" name="brojKomunikacijaFilter" id="brojKomunikacijaFilter" autocomplete="off" min = "1" max = "300" placeholder="NPR: 30 -> Nije dodana komunikacija više od 30 dana." value = "<?php echo $brojKomunikacijaFilter; ?>">
																					<span class="materail-input-block__line">
																					</span>
																				</div>
																			</div>
																		</div>
																	</div>
																	<script>
																		$(document).ready(function(){
																			$("#alertBrojKomunikacija").hide();
																			var brKomProvjera = parseInt($("#brojKomunikacijaFilter").val());
																			var brStatProvjera1 = parseInt($("#brojStatusFilter").val());
																			if(isNaN(brKomProvjera) == true && isNaN(brStatProvjera1) == false){
																				$("#brKomSH").hide();
																			}
																		});
																		$("#brojKomunikacijaFilter").change(function(){
																			$("#brojStatusFilter").val("");
																			$("#brStaSH").hide();
																			var brojKomProvjera = parseInt($("#brojKomunikacijaFilter").val());
																			if(brojKomProvjera < 1 || brojKomProvjera > 300){
																				$('#alertBrojKomunikacija').show();
																				setTimeout(function(){
																					$('#alertBrojKomunikacija').hide();
																					$("#brojKomunikacijaFilter").val(1);
																				}, 5000);
																			}
																			if(isNaN(brojKomProvjera) == true){
																				$("#brStaSH").show();
																			}
																		});
																	</script>
																	<div class="form-group" id = "alertBrojStatus">
																		<div class="col-md-offset-2 col-sm-8 text-center">
																			<div id = "messager_type_1" class="alert alert-danger" role="alert">
																				Unešena vrijednost je izvan dozvoljenih granica. <br>
																				Broj mora biti veći od <span style = "font-weight: bold;">1</span>. 
																			</div>
																		</div>
																	</div>
																	<div class="form-group" id = "brStaSH">
																		<div class="col-md-offset-2 col-sm-8 text-center">
																			<label for="brojStatusFilter" class="col-sm-4 control-label">
																				Broj dana na statusu:
																			</label>
																			<div class="col-sm-8">
																				<div class="materail-input-block materail-input-block_success">
																					<input class="form-control materail-input" type="number" name="brojStatusFilter" id="brojStatusFilter" autocomplete="off" min = "1" placeholder="NPR: 30 -> Kandidat se nalazi više od 30 dana na statusu." value = "<?php echo $brojStatusFilter; ?>">
																					<span class="materail-input-block__line">
																					</span>
																				</div>
																			</div>
																		</div>
																	</div>
																	<script>
																		$(document).ready(function(){
																			$("#alertBrojStatus").hide();
																			var brStatProvjera = parseInt($("#brojStatusFilter").val());
																			var brKomProvjera1 = parseInt($("#brojKomunikacijaFilter").val());
																			if(isNaN(brStatProvjera) == true && isNaN(brKomProvjera1) == false){
																				$("#brStaSH").hide();
																			}
																		});
																		$("#brojStatusFilter").change(function(){
																			$("#brojKomunikacijaFilter").val("");
																			$("#brKomSH").hide();
																			var brojStatProvjera = parseInt($("#brojStatusFilter").val());
																			if(brojStatProvjera < 1){
																				$('#alertBrojStatus').show();
																				setTimeout(function(){
																					$('#alertBrojStatus').hide();
																					$("#brojStatusFilter").val(1);
																				}, 5000);
																			}
																			if(isNaN(brojStatProvjera) == true){
																				$("#brKomSH").show();
																			}
																		});
																	</script>
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
										<div class="row">
											<div class="col-xs-12">
												<?php 
													$trenutnoVrijeme = date("Y-m-d H:i:s");
													//Uslov za drzavu START 
													//----------------------------------------------------------------------
													$uslovDrzava = "";
													if(in_array("387", $drzavaFilterExp)){
														$poduslovDrzavaBih = "(kan.mobilni_nd_kandidata LIKE '+387%')";
													}else{
														$poduslovDrzavaBih = "(kan.mobilni_nd_kandidata = '0000')";
													} 
													if(in_array("381", $drzavaFilterExp)){
														$poduslovDrzavaSrb = "(kan.mobilni_nd_kandidata LIKE '+381%')";
													}else{
														$poduslovDrzavaSrb = "(kan.mobilni_nd_kandidata = '0000')";
													} 
													if(in_array("49", $drzavaFilterExp)){
														$poduslovDrzavaDe = "(kan.mobilni_nd_kandidata LIKE '+49%')";
													}else{
														$poduslovDrzavaDe = "(kan.mobilni_nd_kandidata = '0000')";
													} 
													if(in_array("ostalo", $drzavaFilterExp)){
														$poduslovDrzavaOst = "((kan.mobilni_nd_kandidata NOT LIKE '+387%' AND kan.mobilni_nd_kandidata NOT LIKE '+381%' AND kan.mobilni_nd_kandidata NOT LIKE '+49%') OR kan.mobilni_nd_kandidata IS NULL )";
													}else{
														$poduslovDrzavaOst = "kan.mobilni_nd_kandidata = '0000'";
													}
													$uslovDrzava = "(
														".$poduslovDrzavaBih." OR 
														".$poduslovDrzavaSrb." OR 
														".$poduslovDrzavaDe." OR 
														".$poduslovDrzavaOst."
													)";
													//----------------------------------------------------------------------
													//Uslov za drzavu END 
													
													if($brojStatusFilter != NULL){
														$uslovStatus = "(DATEDIFF('".$trenutnoVrijeme."', log.vrijeme_promjene_statusa_nd_kandidata)) >= ".$brojStatusFilter."";
													}else{
														$uslovStatus = "kan.id_broj_nd_kandidata is not null";
													}
													
													$maxLogCount = 0;
													$maxLogKandidatiExp = array();
													$maxLogKandidatiImp = "";
													
													$queryMaxLogKAndidati = $db->prepare("
														SELECT 
															log.id_log_status_nd_kandidata AS maxIdLog
														FROM 
															idk_nd_kandidata_status_log log
														INNER JOIN 
															idk_nd_kandidata kan
														ON 
															kan.id_broj_nd_kandidata = log.idd_broj_nd_kandidata
														WHERE
															kan.status_nd_kandidata IN (".$statusFilterImp.")
														AND 
															log.broj_dana_statusa_nd_kandidata is null 
														AND 
															".$uslovDrzava."
													");
													$queryMaxLogKAndidati ->execute();
													$maxLogCount = $queryMaxLogKAndidati->rowCount();
													if($maxLogCount != 0){
														while($rowMaxLogKAndidati = $queryMaxLogKAndidati->fetch()){
															$idMaxLog = $rowMaxLogKAndidati["maxIdLog"];
															array_push($maxLogKandidatiExp, $idMaxLog);
														}
														$maxLogKandidatiImp = implode(",", $maxLogKandidatiExp);
														$kandidatiCount = 0;
														$queryKandidati = $db->prepare("
															SELECT
																kan.id_broj_nd_kandidata AS idKandidata, 
																kan.ime_nd_kandidata AS imeKandidata, 
																kan.prezime_nd_kandidata AS prezimeKandidata, 
																kan.mobilni_nd_kandidata AS brojTelefonaKandidata,
																kan.skola_nd_kandidata AS skolaKandidata,
																kan.skola_smjer_nd_kandidata AS smjerSkoleKandidata,
																kan.status_nd_kandidata AS statusKandidata,
																kan.pstatus_nd_kandidata AS pstatusKandidata,
																log.vrijeme_promjene_statusa_nd_kandidata AS vrijemeUlaskaUStatus,
																(DATEDIFF('".$trenutnoVrijeme."', log.vrijeme_promjene_statusa_nd_kandidata)) AS brDanaStatus
															FROM 
																idk_nd_kandidata kan
															INNER JOIN
																idk_nd_kandidata_status_log log
															ON 
																log.idd_broj_nd_kandidata = kan.id_broj_nd_kandidata
															AND 
																log.id_log_status_nd_kandidata IN (".$maxLogKandidatiImp.")
															WHERE 
																kan.status_nd_kandidata IN (".$statusFilterImp.")
															AND 
																".$uslovDrzava." 
															AND 
																".$uslovStatus."
														");
														$queryKandidati->execute();
														$kandidatiCount = $queryKandidati->rowCount();
														if($kandidatiCount != 0){
															
												?>
														<script type="text/javascript">
															$(document).ready(function() {
																$('#kandidatiTable').DataTable({

																	responsive: true,

																	"order": [[ 0, "desc" ]],

																	"bAutoWidth": false,

																	"aoColumns": [
																			{ "width": "5%" },
																			{ "width": "10%" },
																			{ "width": "5%" },
																			{ "width": "10%" },
																			{ "width": "10%" },
																			{ "width": "15%" },
																			{ "width": "15%" },
																			{ "width": "10%" },
																			{ "width": "5%" },
																			{ "width": "10%" },
																			{ "width": "5%" }
																		]
																});
															});
														</script>
														<table id="kandidatiTable" class="display" cellspacing="0" width="100%">
															<thead>
																<tr>
																	<th class="text-center">#ID</th>
																	<th class="text-center">Ime i Prezime</th>
																	<th class="text-center">Država</th>
																	<th class="text-center">Mobitel</th>
																	<th class="text-center">Status</th>
																	<th class="text-center">Škola</th>
																	<th class="text-center">Smjer</th>
																	<th class="text-center">Vrijeme statusa</th>
																	<th class="text-center">Dana na statusu</th>
																	<th class="text-center">ZadnjaBiljeska</th>
																	<th class="text-center">Dana</th>
																</tr>
															</thead>
															<tbody>
															<?php 
																while($rowKandidati = $queryKandidati->fetch()){
																	$idKan = intval($rowKandidati["idKandidata"]);
																	$imePrezimeKan = '<a target="_blank" href="'.getSiteUrlr().'nostrifikacija_diploma?page=otvori_ND_kandidata&id='.$idKan.'">'.$rowKandidati["imeKandidata"]." ".$rowKandidati["prezimeKandidata"];
																	$mobilniKan = $rowKandidati["brojTelefonaKandidata"];
																	if(strpos(substr($mobilniKan, 0, 4), "381")){
																		$drzavaKan = '<img src="'.getSiteUrlr().'images/sr3d.png" width=20>';
																	}else if(strpos(substr($mobilniKan, 0, 4), "387")){
																		$drzavaKan = '<img src="'.getSiteUrlr().'images/bs3d.png" width=20>';
																	}else if(strpos(substr($mobilniKan, 0, 3), "49")){
																		$drzavaKan = '<img src="'.getSiteUrlr().'images/de3d.png" width=20>';
																	}else{
																		$drzavaKan = '<img src="'.getSiteUrlr().'images/globe3d.png" width=20>';
																	}
																	$skolaKan = getSkolaNDKanidataR($rowKandidati["skolaKandidata"]);
																	$skolaSmjerKan = getSkolaSmjerNDKanidataR($rowKandidati["smjerSkoleKandidata"]);
																	$statusKan = getStatusDIPLKandidatR($rowKandidati["statusKandidata"], $rowKandidati["pstatusKandidata"]);
																	$datumStatusKan = date("d.m.Y", strtotime($rowKandidati["vrijemeUlaskaUStatus"]));
																	$danaStatusKan = '<span class="material-label material-label_default main-container__column text-left">'.intval($rowKandidati["brDanaStatus"]).'</span>';
																	if($brojKomunikacijaFilter != NULL){
																		$queryMaxBiljeskaKAndidat = $db->prepare("
																			SELECT 
																				MAX(maxbilj.vrijeme_dodavanja_biljeska_nd) AS maxBiljVrijeme,
																				(DATEDIFF('".$trenutnoVrijeme."', MAX(maxbilj.vrijeme_dodavanja_biljeska_nd))) AS brDanaBiljeska
																			FROM 
																				idk_nd_kandidata_biljeske maxbilj
																			WHERE 
																				maxbilj.id_kandidata_biljeska_nd = :idKanMaxBilj
																			AND 
																				maxbilj.status_biljeska_nd = 4
																		");
																		$queryMaxBiljeskaKAndidat->execute(array(
																			':idKanMaxBilj' => $idKan
																		));
																		$rowMaxBiljeskaKAndidat = $queryMaxBiljeskaKAndidat->fetch();
																		$vrijemeMaxBiljeska = $rowMaxBiljeskaKAndidat["maxBiljVrijeme"];
																		$danaMax = intval($rowMaxBiljeskaKAndidat["brDanaBiljeska"]);
																		if($vrijemeMaxBiljeska == NULL){
																			$vrijemeMaxBiljeska = '<i class="fa fa-question-circle" aria-hidden="true" style = "color: red;" title = "Nije dodana ni jedna komunikacija!"></i>';
																			$danaMax = '<i class="fa fa-question-circle" aria-hidden="true" style = "color: red;" title = "Nije dodana ni jedna komunikacija!"></i>';
																		}else{
																			$vrijemeMaxBiljeska = date("d.m.Y", strtotime($vrijemeMaxBiljeska));
																			$danaMax = $danaMax;
																			if($danaMax < $brojKomunikacijaFilter){
																				continue;
																			}
																		}
																	}else{
																		$vrijemeMaxBiljeska = '<i class="fa fa-question-circle" aria-hidden="true" style = "color: orange;" title = "Nije postavljen kriterij u filteru za broj dana od zadnje bilješke!"></i>';
																		$danaMax = '<i class="fa fa-question-circle" aria-hidden="true" style = "color: orange;" title = "Nije postavljen kriterij u filteru za broj dana od zadnje bilješke!"></i>';
																	}
															?>
																<tr>
																	<td class="text-center"><?php echo $idKan; ?></td>
																	<td class="text-center"><?php echo $imePrezimeKan; ?></td>
																	<td class="text-center"><?php echo $drzavaKan; ?></td>
																	<td class="text-center"><?php echo $mobilniKan; ?></td>
																	<td class="text-center"><?php echo $statusKan; ?></td>
																	<td class="text-center"><?php echo $skolaKan; ?></td>
																	<td class="text-center"><?php echo $skolaSmjerKan; ?></td>
																	<td class="text-center"><?php echo $datumStatusKan; ?></td>
																	<td class="text-center"><?php echo $danaStatusKan; ?></td>
																	<td class="text-center"><?php echo $vrijemeMaxBiljeska; ?></td>
																	<td class="text-center"><?php echo $danaMax; ?></td>
																</tr>
															<?php
																}
															?>
															</tbody>
														</table>
												<?php 
															
														}else{
															echo '
																<div id = "messager_type_1" style = "padding: 15px; font-size: 24px;" class="alert alert-danger" role="alert">
																	U odobranom kriteriju nema kandidata!
																</div>
															';
														}
														
													}else{
														echo '
															<div id = "messager_type_1" style = "padding: 15px; font-size: 24px;" class="alert alert-danger" role="alert">
																U odobranom kriteriju nema kandidata!
															</div>
														';
													}
												?>
											</div>
										</div>
									</div>
								</div>
							</div>
				<?php 
							
						break;
						
						case "croneExport":
							if((in_array( "1" , $employee_status)) OR (in_array( "16" , $employee_status))){
								$id_export = intval($_GET["id"]);
								if($id_export != 0){
									function getStatusDIPLKandidatTextR($status, $podstatus){
										$statusPrikaz = "";
										if($status == 1 AND $podstatus == 1){
											$statusPrikaz = 'Lead';
										}else if($status == 1 AND $podstatus == 6){
											$statusPrikaz = 'Neuspješan kontakt 1';
										}else if($status == 1 AND $podstatus == 2){
											$statusPrikaz = 'Neuspješan kontakt 3';
										}else if($status == 1 AND $podstatus == 3){
											$statusPrikaz = 'Zainteresiran Lead';
										}else if($status == 1 AND $podstatus == 4){
											$statusPrikaz = 'Nezainteresiran Lead';
										}else if($status == 1 AND $podstatus == 5){
											$statusPrikaz = 'U obradi Lead';
										}else if($status == 1 AND $podstatus == 7){
											$statusPrikaz = 'Neuspješan Lead 1';
										}else if($status == 1 AND $podstatus == 8){
											$statusPrikaz = 'Neuspješan Lead 2';
										}else if($status == 1 AND $podstatus == 9){
											$statusPrikaz = 'Termin Zainteresiran';
										}else if($status == 1 AND $podstatus == 10){
											$statusPrikaz = 'Termin Ostali';
										}else if($status == 1 AND $podstatus == 11){
											$statusPrikaz = 'Lead NL';
										}else if($status == 1 AND $podstatus == 12){
											$statusPrikaz = 'Lead NZ';
										}else if($status == 2){
											$statusPrikaz = 'Prikupljanje dokumentacije';
										}else if($status == 3){
											$statusPrikaz = 'Poslana pošta';
										}else if($status == 4){
											$statusPrikaz = 'U obradi';
										}else if($status == 5){
											$statusPrikaz = 'Dopuna dokumentacije';
										}else if($status == 6){
											$statusPrikaz = 'Završen';
										}else if($status == 7){
											$statusPrikaz = 'Arhiv';
										}else{
											$statusPrikaz = 'Nije definisano';
										}
										
										return $statusPrikaz;
									}
				?>
							<div class = "row">
								<div class = "col-xs-9 idk_color_green">
									<h1>
										<i class="fa fa-floppy-o" aria-hidden="true" style = "margin-right: 10px;"></i>
										Reminder Obrada - export Excel
									</h1>
								</div>
								<div class = "col-xs-3 text-right">
								<!-- Prazan prostor -->
								</div>
							</div>
							<div class="row" style = "margin-top: 20px;">
								<div class="col-md-12">
									<div class="content_box">
										<div class="row">
											<div class="col-xs-12">
												<style>
													#customers {
														font-family: Arial, Helvetica, sans-serif;
														border-collapse: collapse;
														width: 100%;
													}

													#customers td, #customers th {
														border: 1px solid #ddd;
														padding: 8px;
													}

													#customers tr:nth-child(even){background-color: #f2f2f2;}

													#customers tr:hover {background-color: #ddd;}

													#customers th {
														padding-top: 12px;
														padding-bottom: 12px;
														text-align: left;
														background-color: #04AA6D;
														color: white;
													}
												</style>
												
												<?php 
													
													$queryPodaci = $db->prepare("
														SELECT 
															result_c, download_employees_c, sent_to_employees_c
														FROM 
															idk_nd_cron_export
														WHERE 
															id_c = :id_c
													");
													$queryPodaci->execute(array(
														':id_c' => $id_export
													));
													$brojPodaci = $queryPodaci->rowCount();
													if($brojPodaci != 0){
														$rowPodaci = $queryPodaci->fetch();
														$result_c = $rowPodaci["result_c"];
														$sent_to_employees_c = explode(",",$rowPodaci["sent_to_employees_c"]);
														if(in_array($logged_employee_id,$sent_to_employees_c) OR in_array( "1" , $employee_status)){
															$download_employees_c = $rowPodaci["download_employees_c"];
															if($download_employees_c != NULL){
																$download_employees_c = explode(",",$download_employees_c);
															}else{
																$download_employees_c = array();
															}
															if(!in_array($logged_employee_id,$download_employees_c)){
																array_push($download_employees_c, $logged_employee_id);
																$download_employees_c = implode(",",$download_employees_c);
																
																$insertPreuzeli = $db->prepare("
																	UPDATE 
																		idk_nd_cron_export 
																	SET 
																		download_employees_c = :download_employees_c 
																	WHERE 
																		id_c = :id_c
																");
																$insertPreuzeli->execute(array(
																	':id_c' => $id_export,
																	':download_employees_c' => $download_employees_c
																));
															}
															
															echo
																'<div class="exportHiden" style = "display: none;">'.$result_c.'</div>';
													?>
															<script>
																$(document).ready(function(){
																	$(".exportSuccessAlert").hide();
																	$("#customers").table2excel({
																		exclude: ".noExl",
																		name: "DIPLReminderObrada<?php echo date('dmYHis');?>",
																		filename: "DIPLReminderObrada<?php echo date('dmYHis');?>",
																		fileext: ".xls"
																	});
																	$(".exportSuccessAlert").show();
																});
															</script>
															<div class="exportSuccessAlert alert alert-success text-center" role="alert">
																Uspješno ste preuzeli file!
															</div>
												<?php 
														}else{
															echo '
																<div class="alert alert-danger text-center" role="alert">
																	Nemate odobrenje za preuzimanje exporta!
																</div>
															';
														}
													}else{
														echo '
															<div class="alert alert-danger text-center" role="alert">
																Export kojeg pokušavate preuzeti je obrisan!
															</div>
														';
													}
												?>
												
											</div>
										</div>
									</div>
								</div>
							</div>
				<?php
								}else{
									echo '
										<div class="alert material-alert material-alert_danger">
											<h4>Problem sa linkom!</h4>
											<p>Kontaktirajte administratora za pomoć.</p>
											<br />
											<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
										</div>
									';
								}
							}else{
								echo '
									<div class="alert material-alert material-alert_danger">
										<h4>Nemate privilegije za ovaj dio stranice!</h4>
										<p>Kontaktirajte administratora za pomoć.</p>
										<br />
										<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
									</div>
								';
							}
						break;
						
						case "croneSettings":
							if((in_array( "1" , $employee_status))){
								
				?>
							<div class = "row">
								<div class = "col-xs-9 idk_color_green">
									<h1>
										<i class="fa fa-wrench" aria-hidden="true" style = "margin-right: 10px;"></i>
										Postavke Remindera Obrade
									</h1>
								</div>
								<div class = "col-xs-3 text-right">
								<!-- Prazan prostor -->
								</div>
							</div>
							<div class="row" style = "margin-top: 20px;">
								<div class="col-md-12">
									<div class="content_box">
										<div class="row">
											<div class="col-xs-12">
												<script type="text/javascript">
													$(document).ready(function() {
														$('#settingsTable').DataTable({

															responsive: true,

															"order": [[ 0, "desc" ]],

															"bAutoWidth": false,

															"aoColumns": [
																	{ "width": "5%" },
																	{ "width": "10%" },
																	{ "width": "12.5%" },
																	{ "width": "7.5%" },
																	{ "width": "7.5%" },
																	{ "width": "12.5%" },
																	{ "width": "7.5%" },
																	{ "width": "7.5%" },
																	{ "width": "10%" },
																	{ "width": "10%" },
																	{ "width": "10%" }
																]
														});
													});
												</script>
												<table id="settingsTable" class="display" cellspacing="0" width="100%">
													<thead>
														<tr>
															<th class="text-center">#ID</th>
															<th class="text-center">Modul</th>
															<th class="text-center">Cron status</th>
															<th class="text-center">Zaposlenici</th>
															<th class="text-center">Kontrola</th>
															<th class="text-center">Status kandidata</th>
															<th class="text-center">Dana status</th>
															<th class="text-center">Dana komunikacija</th>
															<th class="text-center">Početak</th>
															<th class="text-center">Kraj</th>
															<th class="text-center">Pregled exporta</th>
														</tr>
													</thead>
													<tbody>
												<?php 
													$queryListSettings = $db->prepare("
														SELECT 
															*
														FROM 
															idk_nd_cron_settings
													");
													$queryListSettings->execute();
													
													while($rowListSettings = $queryListSettings->fetch()){
														$idS = $rowListSettings["id_s"];
														if(intval($rowListSettings["type_s"]) == 1){
															$typeS = '<span class="label label-default material-label material-label_default main-container__column text-left">Obrada DIPL</span>';
														}else{
															$typeS = '<span class="label label-danger material-label material-label_danger main-container__column text-left">Nepoznato</span>';
														}
														if(intval($rowListSettings["status_s"]) == 1){
															$statusS = '<span class="label label-success material-label material-label_success main-container__column text-left">Aktivan</span>';
														}else{
															$statusS = '<span class="label label-danger material-label material-label_danger main-container__column text-left">Nije aktivan</span>';
														}
														$employees = explode(",",$rowListSettings["employees_s"]);
														$employeesS = array();
														foreach ($employees as $valueEmployees) {
															array_push($employeesS, getZaposlenikimeR($valueEmployees));
														}
														$employeesS = implode(",",$employeesS);
														$control = explode(",",$rowListSettings["control_employees_s"]);
														$controlS = array();
														foreach ($control as $valueControl) {
															array_push($controlS, getZaposlenikimeR($valueControl));
														}
														$controlS = implode(",",$controlS);
														$statusKandidatS = getStatusDIPLKandidatR($rowListSettings["candidate_status_s"], 0);
														if(intval($rowListSettings["status_days_s"]) != NULL){
															$statusDanaS = '<span class="label label-default material-label material-label_default main-container__column text-left">'.$rowListSettings["status_days_s"].'</span>';
														}else{
															$statusDanaS = '<i class="fa fa-question-circle" aria-hidden="true" style = "color: red;" title = "Nije definisan broj dana za status!"></i>';
														}
														if(intval($rowListSettings["communication_days_s"]) != NULL){
															$komunikacijaDanaS = '<span class="label label-default material-label material-label_default main-container__column text-left">'.$rowListSettings["communication_days_s"].'</span>';
														}else{
															$komunikacijaDanaS = '<i class="fa fa-question-circle" aria-hidden="true" style = "color: red;" title = "Nije definisan broj dana za komunikaciju!"></i>';
														}
														$pocetakProvjereS = date("d-m-Y H:i:s", strtotime($rowListSettings["start_day_s"]));
														if($rowListSettings["end_day_s"] != NULL){
															$krajProvjereS = date("d-m-Y H:i:s", strtotime($rowListSettings["end_day_s"]));
														}else{
															$krajProvjereS = '<i class="fa fa-question-circle" aria-hidden="true" style = "color: red;" title = "Nije definisano krajnje vrijeme - aktivan proces!"></i>';
														}
												?>
													<tr>
														<td class="text-center"><?php echo $idS; ?></td>
														<td class="text-center"><?php echo $typeS; ?></td>
														<td class="text-center"><?php echo $statusS; ?></td>
														<td class="text-center"><?php echo '<i class="fa fa-users" aria-hidden="true" style = "color: red;" title = "'.$employeesS.'"></i>'; ?></td>
														<td class="text-center"><?php echo '<i class="fa fa-user-secret" aria-hidden="true" style = "color: red;" title = "'.$controlS.'"></i>'; ?></td>
														<td class="text-center"><?php echo $statusKandidatS; ?></td>
														<td class="text-center"><?php echo $statusDanaS; ?></td>
														<td class="text-center"><?php echo $komunikacijaDanaS; ?></td>
														<td class="text-center"><?php echo $pocetakProvjereS; ?></td>
														<td class="text-center"><?php echo $krajProvjereS; ?></td>
														<td class="text-center">
															<a href = "<?php getSiteURL(); ?>/reminders_obrada_dipl?page=croneList&id=<?php echo $idS; ?>" target="_BLANK" >
																<button class="btn btn-success material-btn material-btn_success">
																	<i class="fa fa-external-link" aria-hidden="true" style = "margin-right: 10px;">
																	</i>
																</button>
															</a>
														</td>
													</tr>
												<?php 
													}
												?>
													</tbody>
												</table>
											</div>
										</div>
									</div>
								</div>
							</div>
				<?php
							}else{
								echo '
									<div class="alert material-alert material-alert_danger">
										<h4>Nemate privilegije za ovaj dio stranice!</h4>
										<p>Kontaktirajte administratora za pomoć.</p>
										<br />
										<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
									</div>
								';
							}
						break;
						
						case "croneList":
							if((in_array( "1" , $employee_status))){
								$settingsID = $_GET["id"];
								
				?>
							<div class = "row">
								<div class = "col-xs-9 idk_color_green">
									<h1>
										<i class="fa fa-fa-list" aria-hidden="true" style = "margin-right: 10px;"></i>
										Export Lista
									</h1>
								</div>
								<div class = "col-xs-3 text-right">
								<!-- Prazan prostor -->
								</div>
							</div>
							<div class="row" style = "margin-top: 20px;">
								<div class="col-md-12">
									<div class="content_box">
										<div class="row">
											<div class="col-xs-12">
												<script type="text/javascript">
													$(document).ready(function() {
														$('#listTable').DataTable({

															responsive: true,

															"order": [[ 0, "desc" ]],

															"bAutoWidth": false,

															"aoColumns": [
																	{ "width": "5%" },
																	{ "width": "10%" },
																	{ "width": "15%" },
																	{ "width": "10%" },
																	{ "width": "30%" },
																	{ "width": "30%" }
																]
														});
													});
												</script>
												<table id="listTable" class="display" cellspacing="0" width="100%">
													<thead>
														<tr>
															<th class="text-center">#ID</th>
															<th class="text-center">Modul</th>
															<th class="text-center">Vrijeme exporta</th>
															<th class="text-center">Poslano</th>
															<th class="text-center">Zaposlenici</th>
															<th class="text-center">Preuzeli zaposlenici</th>
														</tr>
													</thead>
													<tbody>
												<?php 
													$queryListExport = $db->prepare("
														SELECT 
															*
														FROM 
															idk_nd_cron_export
														WHERE 
															settings_id = ".$settingsID."
													");
													$queryListExport->execute();
													
													while($rowListExport = $queryListExport->fetch()){
														$idC = $rowListExport["id_c"];
														if(intval($rowListExport["type_c"]) == 1){
															$typeC = '<span class="label label-default material-label material-label_default main-container__column text-left">Obrada DIPL</span>';
														}else{
															$typeC = '<span class="label label-danger material-label material-label_danger main-container__column text-left">Nepoznato</span>';
														}
														if(intval($rowListExport["sending_type_c"]) == 1){
															$sendC = '<span class="label label-default material-label material-label_default main-container__column text-left">Zaposlenicima</span>';
														}else{
															$sendC = '<span class="label label-default material-label material-label_default main-container__column text-left">Kontrolingu</span>';
														}
														$employees = explode(",",$rowListExport["sent_to_employees_c"]);
														$employeesC = array();
														foreach ($employees as $valueEmployees) {
															array_push($employeesC, getZaposlenikimeR($valueEmployees));
														}
														$employeesC = implode(",",$employeesC);
														if($rowListExport["download_employees_c"] != NULL){
															$download = explode(",",$rowListExport["download_employees_c"]);
														}else{
															$download = array();
														}
														
														if(count($download) != 0){
															$downloadC = array();
															foreach ($download as $valuedownload) {
																array_push($downloadC, getZaposlenikimeR($valuedownload));
															}
															$downloadC = implode(",",$downloadC);
														}else{
															$downloadC = '<i class="fa fa-question-circle" aria-hidden="true" style = "color: red;" title = "Niko od zaposlenika nije preuzeo!"></i>';
														}
														$broj = count($download);
														$vrijemeC = date("d-m-Y H:i:s", strtotime($rowListExport["datetime_c"]));
												?>
													<tr>
														<td class="text-center"><?php echo $idC; ?></td>
														<td class="text-center"><?php echo $typeC; ?></td>
														<td class="text-center"><?php echo $vrijemeC; ?></td>
														<td class="text-center"><?php echo $sendC; ?></td>
														<td class="text-center"><?php echo $employeesC; ?></td>
														<td class="text-center"><?php echo $downloadC; ?></td>
													</tr>
												<?php 
													}
												?>
													</tbody>
												</table>
											</div>
										</div>
									</div>
								</div>
							</div>
				<?php
							}else{
								echo '
									<div class="alert material-alert material-alert_danger">
										<h4>Nemate privilegije za ovaj dio stranice!</h4>
										<p>Kontaktirajte administratora za pomoć.</p>
										<br />
										<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
									</div>
								';
							}
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