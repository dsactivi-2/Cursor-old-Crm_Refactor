<?php
	include("includes/functions.php");
	include("includes/common.php");

	$getEmployeeStatus = getEmployeeStatus();

	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: dipl?page=lista_kandidata&type=1");
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>DIPL | 
		<?php 
			getTitle(); 
		?>
		</title>

		<?php 
			include('includes/head.php'); 
			if (in_array($getUserIp, $getIpWhiteList))
			{
		?>
		<style>
			.alert {
				margin-bottom: 0px !important;
			}
			.alertxx{
				border: 1px solid transparent;
				font-size: medium;
				font-weight: bold;
				border-radius: 1.25rem;
			}
			.alert-dangerxx{
				color: #ffffff;
				background-color: #f3413c;
				border-color: #f3413c;
			}
			.alert_br{
				font-size: xx-large;
			}
			.blinking{
				animation:blinkingText 1.2s infinite;
			}
			@keyframes blinkingText{
				0%{
					background-color: #f3413c;
				}
				49%{
					background-color: #f3413c;
				}
				60%{
					background-color: transparent; color: #f3413c; border-color: transparent;
				}
				99%{
					background-color: transparent; color: #f3413c; border-color: transparent;
				}
				100%{
					background-color: #f3413c;
				}
			}
			.click_under:hover {
				text-decoration: none !important;
			}
			
			.box_style{
				border: 1px solid #dddddd;
				border-radius: 2.75rem;
				box-shadow: 0px 0px 20px 0px rgb(0 0 0 / 50%);
				-webkit-box-shadow: 0px 0px 20px 0px rgb(0 0 0 / 50%);
				-moz-box-shadow: 0px 0px 20px 0px rgba(0,0,0,0.5);
				padding: 2.75rem;
				margin-bottom: 5rem;
			}
		</style>
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
				<!--Switch START-->
				<?php
					switch($page)
					{
						case "lista_kandidata":
						
						if((in_array( "2" , $employee_supervizor)) OR (in_array( "7" , $employee_status)) OR (in_array( "3" , $employee_supervizor)) OR (in_array( "15" , $employee_supervizor)) OR (in_array( "1" , $employee_status)) OR (in_array( "9" , $employee_status)) OR (in_array( "15" , $employee_status)) OR (in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "19" , $employee_status))){
						
							$type = $_GET["type"];
							$prior = 0;
							$team = getLoggedEmployeeTeam();
							if($type == 1){
								$naslov = " - Lead";
							}
							else if($type == 2){
								$naslov = " - Neuspješan Kontakt 1";
							}
							else if($type == 3){
								$naslov = " - Neuspješan Kontakt 3";
							}
							else if($type == 4){
								$naslov = " - Zainteresiran Lead";
							}
							else if($type == 5){
								$naslov = " - Nezainteresiran Lead";
							}
							else if($type == 6){
								$naslov = " - U obradi Lead";
							}
							else if($type == 7){
								$naslov = " - Aktivni";
							}
							else if($type == 8){
								$naslov = " - Završeni";
							}
							else if($type == 9){
								$naslov = " - Arhivirani";
							}
							else if($type == 10){
								$naslov = " - Svi kandidati";
							}
							else if($type == 11){
								$prior = $_GET["pr"];
								$naslov = " - Prioriteti ".$prior."";
							}
							else if($type == 12){
								$naslov = " - Svi prioriteti";
							}
							else if($type == 13){
								$prior = $_GET["pr"];
								if($prior == 1){
									$naslov = " - Moji JobStep Partner Prioriteti";
								}else{
									$naslov = " - Svi JobStep Partner Prioriteti";
								}
							}
							else if($type == 14){
								$prior = $_GET["pr"];
								if($prior == 1){
									$naslov = " - Moji Facebook/Instagram - Inbound Prioriteti";
								}else{
									$naslov = " - Svi Facebook/Instagram - Inbound Prioriteti";
								}
							}
							
				?>
						<div class = "row">
							<div class = "col-xs-6 idk_color_green">
								<h1><i class="fa fa-pencil-square-o" aria-hidden="true" style = "margin-right: 10px;"></i> Nostrifikacija Diploma <?php echo $naslov; ?></h1>
							</div>
							<div class = "col-xs-6 text-right idk_margin_top10">
								<!-- Prostor za neke funkcije -->
								<?php if($type != 10 AND (getZaposlenikDiplR($logged_employee_id) == 1 OR getZaposlenikDiplR($logged_employee_id) == 0)){?>
								<a target="_BLANK" href="<?php getSiteURL(); ?>nostrifikacija_diploma?page=menadzer_pregled_kandidata&id_m=<?php echo $logged_employee_id; ?>" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive">
									<i class="fa fa-plus" aria-hidden="true">
									</i>
									<span>
										Statistike 
									</span>
								</a>
								<?php }?>
							</div>
						</div>
						<div class="row" style = "margin-top: 20px;">
							<div class="col-md-12">
								<div class="content_box">
									<?php
										//Informacije o ponovnim prijavama se neće ispisivati u slučajevima Aktivnih, Završenih, Arhiviranih i Svih kandidata
										$uslov_pp = array(4, 5, 6, 7, 8, 9, 10, 11, 13, 14);
										$uslov_partner = array(1,2,3,12);
										if (!(in_array($type, $uslov_pp))){
											if($type == 12){
												$zap_usl = "";
											}else{
												$zap_usl = $logged_employee_id;
											}
									?>
									<div class="row">
										<div class="col-xs-12">
											<div class = "row">
												<div class = "col-md-3">
													<div class="row">
														<div class="box_style col-md-10 col-md-offset-1">
															<?php 
																if(getBrojPrioritetKandidataDIPLR(1, $zap_usl) == 0){
															?>
																<div class="alert alert-success text-center" role="alert">
																	Prioritet 1 <br><span class = "alert_br">0</span>
																</div>
															<?php 
																}else{
																	if($type != 12){
															?>
																	<a class = "click_under" href="<?php getSiteUrl(); ?>dipl?page=lista_kandidata&type=11&pr=1">
															<?php 
																	}
															?>
																	<div class="alert alertxx alert-danger alert-dangerxx text-center blinking" role="alert">
																		Prioritet 1 <br><span class = "alert_br"> <?php echo getBrojPrioritetKandidataDIPLR(1, $zap_usl); ?></span>
																	</div>
															<?php 
																	if($type != 12){
															?>
																	</a>
															<?php
																	}
																}
															?>
														</div>
													</div>
												</div>
												<div class = "col-md-3">
													<div class="row">
														<div class="box_style col-md-10 col-md-offset-1">
															<?php 
																if(getBrojPrioritetKandidataDIPLR(2, $zap_usl) == 0){
															?>
																<div class="alert alert-success text-center" role="alert">
																	Prioritet 2 <br><span class = "alert_br">0</span>
																</div>
															<?php 
																}else{
																	if($type != 12){
															?>
																	<a class = "click_under" href="<?php getSiteUrl(); ?>dipl?page=lista_kandidata&type=11&pr=2">
															<?php 
																	}
															?>
																	<div class="alert alertxx alert-danger alert-dangerxx text-center blinking" role="alert">
																		Prioritet 2 <br><span class = "alert_br"> <?php echo getBrojPrioritetKandidataDIPLR(2, $zap_usl); ?> </span>
																	</div>
															<?php 
																	if($type != 12){
															?>
																	</a>
															<?php
																	}
																}
															?>
														</div>
													</div>
												</div>
												<?php 
													if (in_array($type, $uslov_partner)){
												?>
													<div class = "col-md-3">
														<div class="row">
															<div class="box_style col-md-10 col-md-offset-1">
																<?php 
																	if($type == 12){
																		if(getBrojPrioritetPartnerKandidataDIPLR(1, $logged_employee_id) == 0){
																?>
																			<div class="alert alert-success text-center" role="alert">
																				JobStep Partner prioriteti <br><span class = "alert_br">0</span>
																			</div>
																<?php
																		}else{
																?>
																			<a class = "click_under" href="<?php getSiteUrl(); ?>dipl?page=lista_kandidata&type=13&pr=2">
																				<div class="alert alertxx alert-danger alert-dangerxx text-center blinking" role="alert">
																					JobStep Partner prioriteti <br><span class = "alert_br"> <?php echo getBrojPrioritetPartnerKandidataDIPLR(1, $logged_employee_id); ?> </span>
																				</div>
																			</a>
																<?php
																		}
																	}else{
																		if(getBrojPrioritetPartnerKandidataDIPLR(0, $logged_employee_id) == 0){
																?>
																			<div class="alert alert-success text-center" role="alert">
																				JobStep Partner prioriteti <br><span class = "alert_br">0</span>
																			</div>
																<?php
																		}else{
																?>
																			<a class = "click_under" href="<?php getSiteUrl(); ?>dipl?page=lista_kandidata&type=13&pr=1">
																				<div class="alert alertxx alert-danger alert-dangerxx text-center blinking" role="alert">
																					JobStep Partner prioriteti <br><span class = "alert_br"> <?php echo getBrojPrioritetPartnerKandidataDIPLR(0, $logged_employee_id); ?> </span>
																				</div>
																			</a>
																<?php
																		}
																	}
																?>
															</div>
														</div>
													</div>
													<div class = "col-md-3">
														<div class="row">
															<div class="box_style col-md-10 col-md-offset-1">
																<?php 
																	if($type == 12){
																		if(getBrPrioritetFacebookInstagramDIPLR(1, $logged_employee_id) == 0){
																?>
																			<div class="alert alert-success text-center" role="alert">
																				FB/IG - Inbound Leads <br><span class = "alert_br">0</span>
																			</div>
																<?php
																		}else{
																?>
																			<a class = "click_under" href="<?php getSiteUrl(); ?>dipl?page=lista_kandidata&type=14&pr=2">
																				<div class="alert alertxx alert-danger alert-dangerxx text-center blinking" role="alert">
																					FB/IG - Inbound Leads <br><span class = "alert_br"> <?php echo getBrPrioritetFacebookInstagramDIPLR(1, $logged_employee_id); ?> </span>
																				</div>
																			</a>
																<?php
																		}
																	}else{
																		if(getBrPrioritetFacebookInstagramDIPLR(0, $logged_employee_id) == 0){
																?>
																			<div class="alert alert-success text-center" role="alert">
																				FB/IG - Inbound Leads <br><span class = "alert_br">0</span>
																			</div>
																<?php
																		}else{
																?>
																			<a class = "click_under" href="<?php getSiteUrl(); ?>dipl?page=lista_kandidata&type=14&pr=1">
																				<div class="alert alertxx alert-danger alert-dangerxx text-center blinking" role="alert">
																					FB/IG - Inbound Leads <br><span class = "alert_br"> <?php echo getBrPrioritetFacebookInstagramDIPLR(0, $logged_employee_id); ?> </span>
																				</div>
																			</a>
																<?php
																		}
																	}
																?>
															</div>
														</div>
													</div>
												<?php
													}
												?>
											</div>
										</div>
									</div>
									</hr>
									<?php 
										}
										
										$uslov_pz = array(1,2,3,10);
										if ((in_array($type, $uslov_pz)))
										{
									?>
									<div class="row">
										<div class="col-xs-12">
											<style>
												.table_fs{
													font-size: 1.75rem !important;
													border-radius: 13px;
												}
												.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
													border-top: 0px !important;
												}
												.table>thead>tr>th {
													border-bottom: 0px !important;
												}
											</style>
											<div class = "row">
												<div class="box_style col-md-8 col-md-offset-2">
													<?php 
															if($type == 10){
																$type_func = 1;
															}else{
																$type_func = 2;
															}
															//Neobrađeni
															$neobradeni_prosli = getBrojPozoviKasnijeDIPLR($type_func, 0, 0, $logged_employee_id);
															$neobradeni_danas = getBrojPozoviKasnijeDIPLR($type_func, 0, 2, $logged_employee_id);
															$neobradeni_buduci = getBrojPozoviKasnijeDIPLR($type_func, 0, 1, $logged_employee_id);
															if($neobradeni_prosli == 0){
																$neobradeni_prosli_ispis = '<span class="table_fs label label-success material-label material-label_success main-container__column text-center">'.$neobradeni_prosli.'</span>';
															}else{
																$neobradeni_prosli_ispis = '<a href = " '.getSiteUrlr().'dipl?page=lista_pozovi_kasnije&type='.$type_func.'&status=0&vrijeme=0&id_zap='.$logged_employee_id.'" target="_BLANK"><span class="table_fs label label-danger material-label material-label_danger main-container__column alertxx alert-dangerxx blinking text-center">'.$neobradeni_prosli.'</span></a>';
															}
															if($neobradeni_danas == 0){
																$neobradeni_danas_ispis = '<span class="table_fs label label-success material-label material-label_success main-container__column text-center">'.$neobradeni_danas.'</span>';
															}else{
																$neobradeni_danas_ispis = '<a href = " '.getSiteUrlr().'dipl?page=lista_pozovi_kasnije&type='.$type_func.'&status=0&vrijeme=2&id_zap='.$logged_employee_id.'" target="_BLANK"><span class="table_fs label label-danger material-label material-label_danger main-container__column alertxx alert-dangerxx blinking text-center">'.$neobradeni_danas.'</span></a>';
															}
															if($neobradeni_buduci == 0){
																$neobradeni_buduci_ispis = '<span class="table_fs label label-success material-label material-label_success main-container__column text-center">'.$neobradeni_buduci.'</span>';
															}else{
																$neobradeni_buduci_ispis = '<a href = " '.getSiteUrlr().'dipl?page=lista_pozovi_kasnije&type='.$type_func.'&status=0&vrijeme=1&id_zap='.$logged_employee_id.'" target="_BLANK"><span class="table_fs label label-warning material-label material-label_warning main-container__column text-center">'.$neobradeni_buduci.'</span></a>';
															}
															//Obrađeni 
															$obradeni_prosli = getBrojPozoviKasnijeDIPLR($type_func, 1, 0, $logged_employee_id);
															$obradeni_danas = getBrojPozoviKasnijeDIPLR($type_func, 1, 2, $logged_employee_id);
															$obradeni_buduci = getBrojPozoviKasnijeDIPLR($type_func, 1, 1, $logged_employee_id);
															if($obradeni_prosli == 0){
																$obradeni_prosli_ispis = '<span class="table_fs label label-success material-label material-label_success main-container__column text-center">'.$obradeni_prosli.'</span>';
															}else{
																$obradeni_prosli_ispis = '<a href = " '.getSiteUrlr().'dipl?page=lista_pozovi_kasnije&type='.$type_func.'&status=1&vrijeme=0&id_zap='.$logged_employee_id.'" target="_BLANK"><span class="table_fs label label-success material-label material-label_success main-container__column text-center">'.$obradeni_prosli.'</span></a>';
															}
															if($obradeni_danas == 0){
																$obradeni_danas_ispis = '<span class="table_fs label label-success material-label material-label_success main-container__column text-center">'.$obradeni_danas.'</span>';
															}else{
																$obradeni_danas_ispis = '<a href = " '.getSiteUrlr().'dipl?page=lista_pozovi_kasnije&type='.$type_func.'&status=1&vrijeme=2&id_zap='.$logged_employee_id.'" target="_BLANK"><span class="table_fs label label-success material-label material-label_success main-container__column text-center">'.$obradeni_danas.'</span></a>';
															}
															if($obradeni_buduci == 0){
																$obradeni_buduci_ispis = '<span class="table_fs label label-success material-label material-label_success main-container__column text-center">'.$obradeni_buduci.'</span>';
															}else{
																$obradeni_buduci_ispis = '<a href = " '.getSiteUrlr().'dipl?page=lista_pozovi_kasnije&type='.$type_func.'&status=1&vrijeme=1&id_zap='.$logged_employee_id.'" target="_BLANK"><span class="table_fs label label-success material-label material-label_success main-container__column text-center">'.$obradeni_buduci.'</span></a>';
															}
															
															$ukupno_prosli = $neobradeni_prosli + $obradeni_prosli;
															$ukupno_danas = $neobradeni_danas + $obradeni_danas;
															$ukupno_buduci = $neobradeni_buduci + $obradeni_buduci;
														
													?>
													<div class="table-responsive">
														<table class="table">
															<caption class = "text-center"><span class="table_fs label label-default material-label material-label_default main-container__column text-center">Zakazani pozivi</span> <br> <hr></caption>
															<thead>
																<tr>
																	<th class="text-center"></th>
																	<th class="text-center"><span class="table_fs label label-default material-label material-label_default main-container__column text-center">Prošli</span></th>
																	<th class="text-center"><span class="table_fs label label-default material-label material-label_default main-container__column text-center">Danas</span></th>
																	<th class="text-center"><span class="table_fs label label-default material-label material-label_default main-container__column text-center">Budući</span></th>
																</tr>
															</thead>
															<tbody>
																<tr>
																	<td class="text-center"><span class="table_fs label label-default material-label material-label_default main-container__column text-center">Neprozvani</span></td>
																	<td class="text-center"><?php echo $neobradeni_prosli_ispis; ?></td>
																	<td class="text-center"><?php echo $neobradeni_danas_ispis; ?></td> 
																	<td class="text-center"><?php echo $neobradeni_buduci_ispis; ?></td>
																</tr>
																<tr>
																	<td class="text-center"><span class="table_fs label label-default material-label material-label_default main-container__column text-center">Prozvani</span></td>
																	<td class="text-center"><?php echo $obradeni_prosli_ispis; ?></td>
																	<td class="text-center"><?php echo $obradeni_danas_ispis; ?></td>
																	<td class="text-center"><?php echo $obradeni_buduci_ispis; ?></td>
																</tr>
																<tr>
																	<td class="text-center"><span class="table_fs label label-default material-label material-label_default main-container__column text-center">Ukupno</span></td> 
																	<td class="text-center"><span class="table_fs label label-primary material-label material-label_primary main-container__column text-center"><?php echo $ukupno_prosli; ?></span></td>
																	<td class="text-center"><span class="table_fs label label-primary material-label material-label_primary main-container__column text-center"><?php echo $ukupno_danas; ?></span></td>
																	<td class="text-center"><span class="table_fs label label-primary material-label material-label_primary main-container__column text-center"><?php echo $ukupno_buduci; ?></span></td>
																</tr>
															</tbody>
														</table>
													</div>
												</div>
											</div>
										</div>
									</div>
									<?php
										}
									?>
									</hr>
									<div class="row">
										<div class="col-xs-12">
											<script type="text/javascript">
												$(document).ready(function() {
													var table_s = $('#table_Lead').DataTable({
				
														responsive: true,
														"pageLength": 10,
														"processing": true,
														"serverSide": true,
														
														"order": [[ 0, "desc" ]],

														"bAutoWidth": false,

														"aoColumns": [
																{ "width": "5%"},
																{ "width": "15%" },
																{ "width": "10%" },
																{ "width": "15%" },
																{ "width": "15%" },
																{ "width": "10%" },
																{ "width": "12.5%" },
																{ "width": "10%" },
																{ "width": "7.5%" , "bSortable": false }
															],
														"createdRow": function(row, data, dataIndex) {
															var $dateCell = $(row).find('td:eq(3)'); // get first column
															var dateOrder = $dateCell.text(); // get the ISO date 
															//console.log(dateOrder);
															if(dateOrder == ''){ 
																  $dateCell
																	.addClass('text-center')
																	.data('order', dateOrder)
																	.html('<span class="label label-warning material-label material-label_warning main-container__column text-center">Nema dodano!<span>');
															  }
															  else{
															  $dateCell
																  .addClass('text-center')
																  .data('order', dateOrder) // set it to data-order
																  .html('<span class="label label-default material-label material-label_default main-container__column text-center">'+moment(dateOrder).format('DD.MM.YYYY HH:mm:ss'))+'</span>'; // and set the formatted text
															  }
														  },
														"ajax":{
															url :"ssdata_dipl?page=lista_lead",
															type: "POST",
															data:{
																"type_ispis": '<?php echo $type; ?>', 
																"logirani_zaposlenik": '<?php echo $logged_employee_id; ?>',
																"team_ispis": '<?php echo $team; ?>',
																"prioritet": '<?php echo $prior; ?>',
															},
															error: function(data){
																$(".list-grid-error").html(""); 
																$("#list-grid_processing").css("display","none");
														
															},
														}
													});
												});
											</script>
											<table id="table_Lead" class="display" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th class="text-center">#ID</th>
														<th class="text-center">Ime i prezime</th>
														<th class="text-center">Telefon</th>
														<th class="text-center">Zadnja komunikacija</th>
														<th class="text-center">Status</th>
														<th class="text-center">Vrijeme</th>
														<th class="text-center">Kreirao</th>
														<th class="text-center">Zadužen</th>
														<th class="text-center">Akcija</th>
													</tr>
												</thead>			
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
				<?php
						}
						else{
							echo '
								<div class="alert material-alert material-alert_danger">
									<h4>NEMATE PRIVILEGIJE!</h4>
									<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
									<br />
									<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
								</div>
							';
						}
						break;
						
						case "pregledDIPL":
							//Za sada samo administrator ima uvid u ovaj dio
							if((in_array( "2" , $employee_supervizor)) OR (in_array( "3" , $employee_supervizor)) OR (in_array( "15" , $employee_supervizor)) OR (in_array( "1" , $employee_status)) OR (in_array( "9" , $employee_status)) OR (in_array("7", $employee_status))  OR (in_array("19", $employee_status)) ){
								//Provjerava teama
								$team = getLoggedEmployeeTeam();
								if($team == 1){
									$employess_id_team = implode(", ", getIdOfEmployeeTeam($team));
								}else{
									$employess_id_team = implode(", ", getIdOfEmployeeTeam($team));
								}
								
				?>
							<div class = "row">
								<div class = "col-xs-9 idk_color_green">
									<h1>
										<i class="fa fa-user-secret" aria-hidden="true" style = "margin-right: 10px;">
										</i>
										Nostrifikacija Diploma - Nadzorni panel
									</h1>
								</div>
								<div class = "col-xs-3 text-right">
									<a href="<?php getSiteURL(); ?>dipl?page=pregledDIPL&type=1" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
								</div>
							</div>
							<div class="row" style = "margin-top: 20px;">
								<div class="col-md-12">
									<div class="content_box">
										<div class="row">
											<div class="col-xs-12">
												<hr>
											</div>
										</div>
										<div class="row">
											<div class="col-xs-12">
												<div id="myTabs" class="panel-group material-tabs-group">
													<ul class="nav nav-tabs material-tabs material-tabs_success">
														<li style = "margin-left: 25px;" class="active" ><a href="#kand_pregled" class="material-tabs__tab-link" data-toggle="tab">Kandidati</a></li>
														<li><a href="#zap_pregled" class="material-tabs__tab-link" data-toggle="tab">Zaposlenici</a></li>
														<li><a href="#otvoreni_ugovori" class="material-tabs__tab-link otvoreni_ugovori_load" data-toggle="tab">Statusi ugovora</a></li>
														<?php if($team == 1){?>
														<li style="float: right; margin-right: 25px;"><a href="#razlozi_pregled" class="material-tabs__tab-link" data-toggle="tab">Prodaja razlozi</a></li>
														<li style="float: right;"><a href="#razlozi_ugovora" class="material-tabs__tab-link" data-toggle="tab">Razlozi odbijanja ugovora</a></li>
														<li style="float: right;"><a href="#razloziInkaso" class="material-tabs__tab-link" data-toggle="tab">Inkaso razlozi</a></li>
														<?php }?>
													</ul>
												
													<div class="tab-content materail-tabs-content">
														<div class="tab-pane fade active in" id="kand_pregled">
															<div class = "row">
																<div class = "col-xs-12">
																	<div class = "content_box">
																		<div class = "row">
																			<div class = "col-xs-6 text-left">
																				<a href="" data-toggle="modal" data-target="#filter_kand_pregled" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive">
																					<i class="fa fa-search" aria-hidden="true"></i>
																					<span>
																						Filter
																					</span>
																				</a>
																				<div class="modal material-modal material-modal_success fade text-left" id="filter_kand_pregled">
																					<div class="modal-dialog modal-lg">
																						<div class="modal-content material-modal__content">
																							<div class="modal-header material-modal__header">
																								<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																								<h4 class="modal-title material-modal__title"><i style = "margin-right: 10px;" class="fa fa-search" aria-hidden="true"></i>Filter kandidata</h4>
																							</div> 
																							<div class="modal-body material-modal__body">
																								<form action="<?php getSiteURL(); ?>dipl?page=pregledDIPL&type=2" enctype="multipart/form-data" method="post" role="form" class="form-horizontal" id = "pregledDIPL_form">
																									<input id="selected_team_fil" name="selected_team_fil" type="hidden"></input>
																									<div class="form-group">
																										<div class="col-md-offset-2 col-sm-8 text-center">
																											<label for="drzava_kan_fil" class="col-sm-4 control-label">
																												Država:
																											</label>
																											<div class="col-sm-8">
																												<div class="">
																													<select class="selectpicker validacija_opcija_f" title = "Odaberite status kandidata" data-actions-box="true" data-live-search = "true" id="drzava_kan_fil" name="drzava_kan_fil[]" data-selected-text-format = "count > 2" multiple>
																														<option <?php if( in_array("387" ,$_POST['drzava_kan_fil'] ?? [])){ echo "selected"; } ?>  value = "387">BiH</option>
																														<option <?php if( in_array("381" ,$_POST['drzava_kan_fil'] ?? [])){ echo "selected"; } ?> value = "381">SRB</option>
																														<option <?php if( in_array("49" ,$_POST['drzava_kan_fil'] ?? [])){ echo "selected"; } ?> value = "49">DE</option>
																														<option <?php if( in_array("ostalo" ,$_POST['drzava_kan_fil'] ?? [])){ echo "selected"; } ?> value = "ostalo">Ostalo</option>
																													</select>
																												</div>
																											</div>
																										</div>
																									</div>
																									<div class="form-group">
																										<div class="col-md-offset-2 col-sm-8 text-center">
																											<label for="status_kan_fil" class="col-sm-4 control-label">
																												Status:
																											</label>
																											<div class="col-sm-8">
																												<div class="">
																													<select class="selectpicker validacija_opcija_f" title = "Odaberite status kandidata" data-actions-box="true" data-live-search = "true" id="status_kan_fil" name="status_kan_fil[]" data-selected-text-format = "count > 2" multiple>
																														<option  <?php if( in_array("11" ,$_POST['status_kan_fil'] ?? [])){ echo "selected"; } ?> value = "11">Lead</option>
																														<option  <?php if( in_array("16" ,$_POST['status_kan_fil'] ?? [])){ echo "selected"; } ?> value = "16">Neuspješan Kontakt 1</option>
																														<option  <?php if( in_array("12" ,$_POST['status_kan_fil'] ?? [])){ echo "selected"; } ?> value = "12">Neuspješan Kontakt 3</option>
																														<option  <?php if( in_array("13" ,$_POST['status_kan_fil'] ?? [])){ echo "selected"; } ?> value = "13">Zainteresiran Lead</option>
																														<option <?php if( in_array("14" ,$_POST['status_kan_fil'] ?? [])){ echo "selected"; } ?>  value = "14">Nezainteresiran Lead</option>
																														<option <?php if( in_array("15" ,$_POST['status_kan_fil'] ?? [])){ echo "selected"; } ?>  value = "15">U obradi Lead</option>
																														<!-- //111Adis222 10 START -->
																														<option <?php if( in_array("17" ,$_POST['status_kan_fil'] ?? [])){ echo "selected"; } ?>  value = "17">Neuspješan Lead 1</option>
																														<option <?php if( in_array("18" ,$_POST['status_kan_fil'] ?? [])){ echo "selected"; } ?>  value = "18">Neuspješan Lead 2</option>
																														<option <?php if( in_array("19" ,$_POST['status_kan_fil'] ?? [])){ echo "selected"; } ?>  value = "19">Termin Zainteresiran</option>
																														<option <?php if( in_array("20" ,$_POST['status_kan_fil'] ?? [])){ echo "selected"; } ?>  value = "20">Termin Ostali</option>
																														<option <?php if( in_array("21" ,$_POST['status_kan_fil'] ?? [])){ echo "selected"; } ?>  value = "21">Lead NL</option>
																														<option <?php if( in_array("22" ,$_POST['status_kan_fil'] ?? [])){ echo "selected"; } ?>  value = "22">Lead NZ</option>
																														<!-- //111Adis222 10 END -->
																														<option <?php if( in_array("221" ,$_POST['status_kan_fil'] ?? [])){ echo "selected"; } ?>  value = "221">Prikupljanje dokumentacije</option>
																														<option <?php if( in_array("222" ,$_POST['status_kan_fil'] ?? [])){ echo "selected"; } ?>  value = "222">Nepotpuna dokumentacija</option>
																														<option <?php if( in_array("224" ,$_POST['status_kan_fil'] ?? [])){ echo "selected"; } ?>  value = "224">Dokumentacija kompletirana</option>
																														<option <?php if( in_array("223" ,$_POST['status_kan_fil'] ?? [])){ echo "selected"; } ?>  value = "223">Na prevodu</option>
																														<option <?php if( in_array("225" ,$_POST['status_kan_fil'] ?? [])){ echo "selected"; } ?>  value = "225">Prevod završen</option>
																														<option <?php if( in_array("226" ,$_POST['status_kan_fil'] ?? [])){ echo "selected"; } ?>  value = "226">Poslan zahtjev</option>
																														<option <?php if( in_array("227" ,$_POST['status_kan_fil'] ?? [])){ echo "selected"; } ?>  value = "227">Potpisan zahtjev</option>
																														<option <?php if( in_array("31" ,$_POST['status_kan_fil'] ?? [])){ echo "selected"; } ?>  value = "31">Poslana pošta</option>
																														<option <?php if( in_array("32" ,$_POST['status_kan_fil'] ?? [])){ echo "selected"; } ?>  value = "32">Zaprimili dokumentaciju</option>
																														<option <?php if( in_array("41" ,$_POST['status_kan_fil'] ?? [])){ echo "selected"; } ?>  value = "41">U obradi</option>
																														<option <?php if( in_array("42" ,$_POST['status_kan_fil'] ?? [])){ echo "selected"; } ?>  value = "42">Stigla taxa / dopuna</option>
																														<option <?php if( in_array("5" ,$_POST['status_kan_fil'] ?? [])){ echo "selected"; } ?>  value = "5">Plaćena taxa / Poslana dopuna</option>
																														<option <?php if( in_array("6" ,$_POST['status_kan_fil'] ?? [])){ echo "selected"; } ?>  value = "6">Završen</option>
																														<option <?php if( in_array("7" ,$_POST['status_kan_fil'] ?? [])){ echo "selected"; } ?>  value = "7">Arhiviran</option>
																													</select>
																												</div>
																											</div>
																										</div>
																									</div>
																									<div class="form-group" <?php echo ((getModulePermission(7) == false) ? 'style="display:none;"': ''); ?>>
																										<div class="col-md-offset-2 col-sm-8 text-center">
																											<label for="razlog_odbijanja_kan_fil" class="col-sm-4 control-label">
																												Razlozi odbijanja prodaje:
																											</label>
																											<div class="col-sm-8">
																												<div class="">
																													<select class="selectpicker validacija_opcija_f" title = "Odaberite razlog odbijanja" data-actions-box="true" data-live-search = "true" id="razlog_odbijanja_kan_fil" name="razlog_odbijanja_kan_fil[]" data-selected-text-format = "count > 2" multiple>
																														<?php 
																															$queryRazlogOdbijanja = $db->prepare("
																																SELECT 
																																	id_ro, naziv_ro_bs, ponovno_zvanje_ro, br_dana_ro
																																FROM 
																																	idk_ro_usluge
																																WHERE 
																																	tip_ro = 1 AND status_ro IN (1,2,3) AND ((ponovno_zvanje_ro = 1 AND br_dana_ro is not null) OR (ponovno_zvanje_ro = 0 AND br_dana_ro is null))
																																ORDER BY 
																																	ponovno_zvanje_ro, id_ro 
																																ASC
																															"); 
																															$queryRazlogOdbijanja->execute();
																															$countRazlogOdbijanja = $queryRazlogOdbijanja->rowCount();
																															if($countRazlogOdbijanja > 0) {
																																while($rowRazlogOdbijanja = $queryRazlogOdbijanja->fetch()) {
																																	echo '
																																		<option
																																			value="'.$rowRazlogOdbijanja["id_ro"].'"
																																			data-subtext="'.(($rowRazlogOdbijanja["ponovno_zvanje_ro"] == 1) ? 'Status: Nezainteresiran Lead.' : 'Status: Arhiviran.').'"
																																			'.((in_array($rowRazlogOdbijanja["id_ro"], $_POST['razlog_odbijanja_kan_fil'] ?? [])) ? 'selected' : '').'
																																		>
																																			'.$rowRazlogOdbijanja["naziv_ro_bs"].'
																																		</option>
																																	';
																																}
																															} 
																														?>
																													</select>
																													<script>
																														$('#razlog_odbijanja_kan_fil').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
																															$("#status_kan_fil").val(null).selectpicker("refresh");
																															var razlog_odbijanja_kan_fil = $(this).find('option:selected');
																															var status_kan_fil = $("#status_kan_fil").val() || [];
																															var nezainteresiranFlag = false;
																															var arhiviranFlag = false;  
																															if (razlog_odbijanja_kan_fil.length > 0) {
																																razlog_odbijanja_kan_fil.each(function() {
																																	if($(this).data('subtext') === 'Status: Nezainteresiran Lead.') {
																																		nezainteresiranFlag = true; 
																																		status_kan_fil.push("14");
																																	} 
																																	if($(this).data('subtext') === 'Status: Arhiviran.') {
																																		arhiviranFlag = true; 
																																		status_kan_fil.push("7");
																																	} 
																																});
																																
																																if (nezainteresiranFlag) {
																																	status_kan_fil.push("14");
																																} else {
																																	// Ako nema odabranog razloga 'Nezainteresiran Lead', uklonite vrijednost 14 iz status_kan_fil
																																	status_kan_fil = status_kan_fil.filter(function(value) {
																																		return value !== "14";
																																	});
																																}

																																if (arhiviranFlag) {
																																	status_kan_fil.push("7");
																																} else {
																																	// Ako nema odabranog razloga 'Nezainteresiran Lead', uklonite vrijednost 14 iz status_kan_fil
																																	status_kan_fil = status_kan_fil.filter(function(value) {
																																		return value !== "7";
																																	});
																																}

																																$("#status_kan_fil").val(status_kan_fil).selectpicker("refresh");
																															} else {
																																$("#status_kan_fil").val(null).selectpicker("refresh");
																															}
																														});
																													</script>
																												</div>
																											</div>
																										</div>
																									</div>
																									<div class="form-group" <?php echo ((getModulePermission(7) == false) ? 'style="display:none;"': ''); ?>>
																										<div class="col-md-offset-2 col-sm-8 text-center">
																											<div class="alert alert-warning" role="alert">
																												<small>
																													Polje <strong>Razlozi odbijanja prodaje</strong> je polje koje pri korištenju <strong class="text-danger">USPORAVA</strong> filtriranje kandidata!<br>
																													Također, isto polje utiče na polje <strong>Status</strong> jer se kandidati sa određenim razlogom mogu nalaziti jedino pod statusima <strong>Nezainteresiran Lead</strong> i <strong>Arhiviran</strong>.<br>
																													Dovoljno je odabrati razlog a sistem će sam selektovati određeni Status!
																												</small>
																											</div>
																										</div>
																									</div>
																									<div class="form-group">
																										<div class="col-md-offset-2 col-sm-8 text-center">
																											<label for="vrsta_priznanja_fil" class="col-sm-4 control-label">
																												Vrsta priznanja:
																											</label>
																											<div class="col-sm-8">
																												<div class="">
																													<select class="selectpicker validacija_opcija_f" title = "Odaberite status kandidata" data-actions-box="true" data-live-search = "true" id="vrsta_priznanja_fil" name="vrsta_priznanja_fil[]" data-selected-text-format = "count > 2" multiple>
																														<option  <?php if( in_array("2" ,$_POST['vrsta_priznanja_fil'] ?? [])){ echo "selected"; } ?> value = "2">Evaluacija</option>
																														<option  <?php if( in_array("1" ,$_POST['vrsta_priznanja_fil'] ?? [])){ echo "selected"; } ?> value = "1">Potpuno priznata</option>
																														<option  <?php if( in_array("0" ,$_POST['vrsta_priznanja_fil'] ?? [])){ echo "selected"; } ?> value = "0">Djelimično priznata</option>
																														<option  <?php if( in_array("3" ,$_POST['vrsta_priznanja_fil'] ?? [])){ echo "selected"; } ?> value = "3">Neodređeno</option>
																														<option  <?php if( in_array("4" ,$_POST['vrsta_priznanja_fil'] ?? [])){ echo "selected"; } ?> value = "4">Nije unešena nostrifikacija</option>
																													</select>
																												</div>
																												<script>
																													$("#vrsta_priznanja_fil").on("change",function() {
																														var vrsta_priznanja_fil = $("#vrsta_priznanja_fil").val();
																														//console.log(vrsta_priznanja_fil);
																														if (vrsta_priznanja_fil != null) {
																															if(vrsta_priznanja_fil.includes("4")){
																																$("#vrsta_priznanja_fil").val(4).selectpicker("refresh");
																															} else {
																																$("#vrsta_priznanja_fil").selectpicker("refresh");
																															}
																														}
																													});
																												</script>
																											</div>
																										</div>
																									</div>
																									<!--<div class="form-group" id = "podstatus_novix">
																										<div class="col-md-offset-2 col-sm-8 text-center">
																											<label for="pstatus_kan_fil" class="col-sm-3 control-label">
																												Podstatus:
																											</label>
																											<div class="col-sm-9">
																												<div class="">
																													<select class="selectpicker validacija_opcija_f" title = "Odaberite podstatus" data-actions-box="true" data-live-search = "true" id="pstatus_kan_fil" name="pstatus_kan_fil[]" data-selected-text-format = "count > 2" multiple>
																														<option value = "1">Lead</option>
																														<option value = "6">Neuspješan Kontakt 1</option>
																														<option value = "2">Neuspješan Kontakt 3</option>
																														<option value = "3">Zainteresiran Lead</option>
																														<option value = "4">Nezainteresiran Lead</option>
																														<option value = "5">U obradi Lead</option>
																													</select>
																												</div>
																											</div>
																										</div>
																									</div>-->
																									<div class="form-group">
																										<div class="col-md-offset-2 col-sm-8 text-center">
																											<label for="povijest_kan_fil" class="col-sm-4 control-label">
																												Povijest ulaska:
																											</label>
																											<div class="col-sm-8">
																												<div class="">
																													<select class="selectpicker validacija_opcija_f" title = "Odaberite povijest kandidata" data-actions-box="true" data-live-search = "true" id="povijest_kan_fil" name="povijest_kan_fil[]" data-selected-text-format = "count > 2" multiple>
																														<option <?php if( in_array("0" ,$_POST['povijest_kan_fil'] ?? [])){ echo "selected"; } ?>  value = "0">Ručna registracija</option>
																														<option <?php if( in_array("1" ,$_POST['povijest_kan_fil'] ?? [])){ echo "selected"; } ?> value = "1">Kandidati - SMS</option>
																														<option <?php if( in_array("2" ,$_POST['povijest_kan_fil'] ?? [])){ echo "selected"; } ?>   value = "2">Kandidati - Messenger</option>
																														<option <?php if( in_array("3" ,$_POST['povijest_kan_fil'] ?? [])){ echo "selected"; } ?>   value = "3">Kandidati - CRM</option>
																														<option <?php if( in_array("4" ,$_POST['povijest_kan_fil'] ?? [])){ echo "selected"; } ?>   value = "4">Kandidati - Viber</option>
																														<option <?php if( in_array("9" ,$_POST['povijest_kan_fil'] ?? [])){ echo "selected"; } ?>   value = "9">Kandidati - Viber Stornirani</option>
																														<option <?php if( in_array("5" ,$_POST['povijest_kan_fil'] ?? [])){ echo "selected"; } ?>   value = "5">Sve za vize - App</option>
																														<option <?php if( in_array("6" ,$_POST['povijest_kan_fil'] ?? [])){ echo "selected"; } ?>   value = "6">Sve za vize - WEB</option>
																														<option <?php if( in_array("7" ,$_POST['povijest_kan_fil'] ?? [])){ echo "selected"; } ?>   value = "7">JobStep Partner APP</option> 
																														<option <?php if( in_array("8" ,$_POST['povijest_kan_fil'] ?? [])){ echo "selected"; } ?>   value = "8">JobStep Web</option>
																														<option <?php if( in_array("10" ,$_POST['povijest_kan_fil'] ?? [])){ echo "selected"; } ?>   value = "10">Kampanje</option>
																													</select>
																												</div>
																											</div>
																										</div>
																									</div>
																									<script>
																										$(document).ready(function(){
																											$('#kampanje_f').hide();
																											if (jQuery.inArray('10', $('#povijest_kan_fil').val()) != -1){
																											//if ($('#povijest_kan_fil').val() == '10'){
																												
																												$('#kampanje_f').show();
																												document.getElementById("kampanja_kan_fil").required = true;
																											}else{
																												$('#kampanje_f').hide();
																												document.getElementById("kampanja_kan_fil").required = false;
																											}
																											document.getElementById("kampanja_kan_fil").required = false;
																											$("#povijest_kan_fil").change(function(){
																												//console.log($('#povijest_kan_fil').val());
																												//console.log(jQuery.inArray('10', $('#povijest_kan_fil').val()));
																												
																												if (jQuery.inArray('10', $('#povijest_kan_fil').val()) != -1){
																												//if ($('#povijest_kan_fil').val() == '10'){
																													
																													$('#kampanje_f').show();
																													document.getElementById("kampanja_kan_fil").required = true;
																												}else{
																													$('#kampanje_f').hide();
																													document.getElementById("kampanja_kan_fil").required = false;
																												}
																											});
																										});
																									</script>
																									<div class="form-group" id = "kampanje_f">
																										<div class="col-md-offset-2 col-sm-8 text-center">
																											<?php 
																												$queryKamp = $db->prepare("
																													SELECT kd_naziv, kd_id, kd_skraceni_naziv, kd_drzava
																													FROM idk_kampanje_dipl
																													WHERE kd_status = 1 
																												");
																												$queryKamp->execute();
																											?>
																											<label for="kampanja_kan_fil" class="col-sm-4 control-label">
																												Kampanje:
																											</label>
																											<div class="col-sm-8">
																												<div class="">
																													<select class="selectpicker validacija_opcija_f" title = "Odaberite kampanju" data-actions-box="true" data-live-search = "true" id="kampanja_kan_fil" name="kampanja_kan_fil[]" data-selected-text-format = "count > 2" multiple>
																													<?php 
																														while($rowKamp = $queryKamp->fetch()){
																															if(in_array($rowKamp['kd_id'],$_POST['kampanja_kan_fil'] ?? []))
																															echo '<option selected value = "'.$rowKamp["kd_id"].'" data-subtext = "'.$rowKamp["kd_skraceni_naziv"].'-'.$rowKamp["kd_drzava"].'">'.$rowKamp["kd_naziv"].'</option>';
																															else
																															echo '<option value = "'.$rowKamp["kd_id"].'" data-subtext = "'.$rowKamp["kd_skraceni_naziv"].'-'.$rowKamp["kd_drzava"].'">'.$rowKamp["kd_naziv"].'</option>';
																														}
																													?>
																													</select>
																												</div>
																											</div>
																										</div>
																									</div>
																									<div class="form-group">
																										<div class="col-md-offset-2 col-sm-8 text-center">
																											<label for="" class="col-sm-4 control-label">Vrijeme ulaska:</label>
																											<div class="col-sm-4">
																												<div class="materail-input-block materail-input-block_success">
																													<input class="validacija_opcija_f form-control materail-input" type="text" name="datum_ulaskaod_fil" id="datum_ulaskaod_fil" placeholder="Od">
																													<span class="materail-input-block__line"></span>
																													
																												</div>
																											</div>
																											<script>
																												$("#datum_ulaskaod_fil").flatpickr({
																													dateFormat: "d-m-Y",
																													minDate: "01-04-2020",
																													maxDate: "today",
																													disableMobile: "true",
																													defaultDate: ["<?php if(isset($_POST['datum_ulaskaod_fil'])){ echo $_POST['datum_ulaskaod_fil']; } else{ echo "01-04-2022";} ?>"],
																													disable: ["<?php echo date("d").'-'.date("m").'-'.date("Y"); ?>"]
																												});
																											</script>
																											<div class="col-sm-4">
																												<div class="materail-input-block materail-input-block_success">
																													<input class="validacija_opcija_f form-control materail-input" type="text" name="datum_ulaskado_fil" id="datum_ulaskado_fil" placeholder="Do">
																													<span class="materail-input-block__line"></span>
																												</div>
																											</div>
																											<script>
																												$("#datum_ulaskado_fil").flatpickr({
																													dateFormat: "d-m-Y",
																													minDate: "01-04-2020",
																													maxDate: "today",
																													disableMobile: "true",
																													defaultDate: ["<?php  if(isset($_POST['datum_ulaskado_fil'])){ echo $_POST['datum_ulaskado_fil'];}else{ echo date("d").'-'.date("m").'-'.date("Y");} ?>"],
																													disable: ["01-04-2020"]
																												});
																											</script>
																										</div>
																									</div>
																									<?php
																										$struke_ispis_fil = $db->prepare("
																																	SELECT *
																																	FROM idk_struke
																																");
																										$struke_ispis_fil->execute();
																									?>
																									<div class="form-group">
																										<div class="col-md-offset-2 col-sm-8 text-center">
																											<label for="struka_fil" class="col-sm-4 control-label">Struka:</label>
																											<div class="col-sm-8">
																												<select class="selectpicker validacija_opcija_f" id="struka_fil" name="struka_fil[]" data-actions-box="true" data-live-search = "true" title = "Odaberite struku" data-selected-text-format = "count > 2" multiple>
																													<?php
																														while($row_struke_ispis_fil = $struke_ispis_fil->fetch()){
																															$struka_id = $row_struke_ispis_fil['id_struke'];
																															$struka_naziv = $row_struke_ispis_fil['naziv_struke'];
																															if(in_array($struka_id,$_POST['struka_fil'] ?? []))
																															echo '<option selected value = "'.$struka_id.'">'.$struka_naziv.'</option>';
																															else
																															echo '<option value = "'.$struka_id.'">'.$struka_naziv.'</option>';
																														}
																													?>
																												</select>
																											</div>
																										</div>
																									</div>
																									<?php
																										$skole_ispis_fil = $db->prepare("
																																	SELECT *
																																	FROM idk_skole
																																");
																										$skole_ispis_fil->execute();
																									?>
																									<div class="form-group">
																										<div class="col-md-offset-2 col-sm-8 text-center">
																											<label for="s_kan_fil" class="col-sm-4 control-label">Škola:</label>
																											<div class="col-sm-8">
																												<div class="">
																													<select class="selectpicker validacija_opcija_f" id="s_kan_fil" name="s_kan_fil[]" data-actions-box="true" data-live-search = "true" title = "Odaberite školu" data-selected-text-format = "count > 2" multiple>
																														<?php
																															while($row_skole_ispis_fil = $skole_ispis_fil->fetch()){
																																$skola_id1 = $row_skole_ispis_fil['skola_id'];
																																$skola_naziv1 = $row_skole_ispis_fil['skola_naziv'];
																																if(in_array($skola_id1,$_POST['s_kan_fil'] ?? []))
																																echo '<option selected value = "'.$skola_id1.'">'.$skola_naziv1.'</option>';
																																else
																																echo '<option value = "'.$skola_id1.'">'.$skola_naziv1.'</option>';
																															}
																														?>
																													</select>
																												</div>
																											</div>
																										</div>
																									</div>
																									
																									<script>
																										$(document).ready(function() {
																											var skola_id_odabrano=[];
																											$.each($("#s_kan_fil option:selected"),function(){
																												skola_id_odabrano.push($(this).val());
																											});
																											var struke_izabrane=<?php echo json_encode($_POST['ss_kan_fil'] ?? []); ?>;
																											
																											/*alert(skola_id_odabrano);*/
																											$.ajax({
																												url: 'ajax_data.php?page=posalji_smjer_odabrane_skole_ms',
																												type: 'POST',
																												data: {'skola_id_odabrano':skola_id_odabrano,
																														'struke_izabrane':struke_izabrane
																													},
																												dataType: 'html',
																												success: function(data) {
																													$("#ss_kan_fil").html(data);
																												},
																												error: function (xhr, ajaxOptions, thrownError) {
																													alert(xhr.status);
																													alert(thrownError);
																												}
																											});
																											
																										});
																										$("#s_kan_fil").change(function (){
																										/*$('#s_kan_fil').on('change',function() {*/
																											var skola_id_odabrano = $(this).val();
																						
																											
																											/*alert(skola_id_odabrano);*/
																											$.ajax({
																												url: 'ajax_data.php?page=posalji_smjer_odabrane_skole_ms',
																												type: 'POST',
																												data: {'skola_id_odabrano':skola_id_odabrano},
																												dataType: 'html',
																												success: function(data) {
																													$("#ss_kan_fil").html(data).selectpicker('refresh');
																												},
																												error: function (xhr, ajaxOptions, thrownError) {
																													alert(xhr.status);
																													alert(thrownError);
																												}
																											});
																										});
																									</script>
																									
																									<div class="form-group">
																										<div class="col-md-offset-2 col-sm-8 text-center">
																											<label for="ss_kan_fil" class="col-sm-4 control-label"> Smjer:</label>
																											<div class="col-sm-8">
																												<div class="">
																													<select class="selectpicker validacija_opcija_f" id="ss_kan_fil" data-live-search = "true" title = "Odaberite smjer" name="ss_kan_fil[]" data-actions-box="true" data-selected-text-format = "count > 2" multiple>
																														
																													</select>
																												</div>
																											</div>
																										</div>
																									</div>
																									<?php
																										$flagException = 0; 
																										if($team == 4 AND (in_array( "2" , $employee_supervizor) OR in_array( "3" , $employee_supervizor))){
																											$flagException = 1; 
																										}
																										if($team == 1 OR $flagException == 1){
																											$uslov_team_fill = " id_t is not null";
																										}else{
																											$uslov_team_fill = " id_t = ".$team." ";
																										}
																										$team_fill = $db->prepare("
																																SELECT id_t, naziv_t
																																FROM idk_timovi
																																WHERE status_t = 1 AND ".$uslov_team_fill." 
																																");
																										$team_fill->execute();
																									?>
																									<div class="form-group">
																										<div class="col-md-offset-2 col-sm-8 text-center">
																											<label for="team_fil" class="col-sm-4 control-label"> Team:</label>
																											<div class="col-sm-8">
																												<div class="">
																													<select class="selectpicker validacija_opcija_f" id="team_fil" data-live-search = "true" title = "Odaberite tim" name="team_fil[]" data-actions-box="true" data-selected-text-format = "count > 2" multiple>
																														<?php
																															while($row_team_fill = $team_fill->fetch()){
																																$team_id1 = $row_team_fill['id_t'];
																																$team_naziv1 = $row_team_fill['naziv_t'];
																																if(in_array($team_id1,$_POST['team_fil'] ?? []))
																																echo '<option selected value = "'.$team_id1.'">'.$team_naziv1.'</option>';
																																else
																																echo '<option value = "'.$team_id1.'">'.$team_naziv1.'</option>';
																															}
																														?>
																													</select>
																												</div>
																											</div>
																										</div>
																									</div>
																									<script>
																										$(document).ready(function() {

																											var team_id_odabrano=[];
																											$.each($("#team_fil option:selected"),function(){
																												team_id_odabrano.push($(this).val());
																											});
																											var radnici_izabrani=<?php echo json_encode($_POST['zaposlenik_fil'] ?? []); ?>;
																											//alert(team_id_odabrano);
																											$.ajax({
																												url: 'ajax_data.php?page=posalji_zaposlenike_tima',
																												type: 'POST',
																												data: {'team_id_odabrano':team_id_odabrano,
																														'radnici_izabrani':radnici_izabrani},
																												dataType: 'html',
																												success: function(data) {
																													$("#zaposlenik_fil").html(data);
																												},
																												error: function (xhr, ajaxOptions, thrownError) {
																													alert(xhr.status);
																													alert(thrownError);
																												}
																											});


																											document.getElementById("zaposlenik_fil").required = false;
																											$("#team_fil").change(function() {
																											/*$('#s_kan_fil').on('change',function() {*/
																												var team_id_odabrano = $(this).val();
																												// alert(team_id_odabrano);
																												document.getElementById("selected_team_fil").value = team_id_odabrano;
																												if(team_id_odabrano === null){
																													document.getElementById("zaposlenik_fil").required = false;
																													team_id_odabrano = 0;
																												}else{
																													document.getElementById("zaposlenik_fil").required = true;
																												}
																												/*alert(skola_id_odabrano);*/
																												/*alert(team_id_odabrano); */
																												$.ajax({
																													url: 'ajax_data.php?page=posalji_zaposlenike_tima',
																													type: 'POST',
																													data: {'team_id_odabrano':team_id_odabrano},
																													dataType: 'html',
																													success: function(data) {
																														$("#zaposlenik_fil").html(data).selectpicker('refresh');
																													},
																													error: function (xhr, ajaxOptions, thrownError) {
																														alert(xhr.status);
																														alert(thrownError);
																													}
																												});
																												
																											});
																										});
																									</script>
																									<div class="form-group">
																										<div class="col-md-offset-2 col-sm-8 text-center">
																											<label for="zaposlenik_fil" class="col-sm-4 control-label"> Zadužen zaposlenik:</label>
																											<div class="col-sm-8">
																												<div class="">
																													<select class="selectpicker validacija_opcija_f" id="zaposlenik_fil" data-live-search = "true" title = "Odaberite zaposlenika" name="zaposlenik_fil[]" data-actions-box="true" data-selected-text-format = "count > 2" multiple>
																														
																													</select>
																												</div>
																											</div>
																										</div>
																									</div>
																									<div class="form-group">
																										<div class="col-md-offset-2 col-sm-8 text-center">
																											<label for="vrstaug_fil" class="col-sm-4 control-label">
																												Vrsta ugovora:
																											</label>
																											<div class="col-sm-8">
																												<div class="">
																													<select class="selectpicker validacija_opcija_f" title = "Odaberite vrstu ugovora" data-actions-box="true" data-live-search = "true" id="vrstaug_fil" name="vrstaug_fil[]" data-selected-text-format = "count > 2" multiple>
																														<optgroup label="Ugovori bez popusta">
																															<option <?php if(in_array(9,$_POST['vrstaug_fil'] ?? [])){ echo "selected"; } ?> value = "9" data-subtext="10% popust">
																																1 rata
																															</option>
																															<option  <?php if(in_array(1,$_POST['vrstaug_fil'] ?? [])){ echo "selected"; } ?> value = "1">
																																2 rate
																															</option>
																															<option  <?php if(in_array(5,$_POST['vrstaug_fil'] ?? [])){ echo "selected"; } ?> value = "5">
																																3 rate
																															</option>
																															<option  <?php if(in_array(7,$_POST['vrstaug_fil'] ?? [])){ echo "selected"; } ?> value = "7">
																																4 rate
																															</option>
																															<option  <?php if(in_array(3,$_POST['vrstaug_fil'] ?? [])){ echo "selected"; } ?> value = "3">
																																5 rata
																															</option>
																															<option <?php if(in_array(11,$_POST['vrstaug_fil'] ?? [])){ echo "selected"; } ?> value = "11">
																																Mikrofin
																															</option>
																														</optgroup>
																														<optgroup label="Ugovori sa popustom 30% za naše kandidate">
																															<option <?php if(in_array(10,$_POST['vrstaug_fil'] ?? [])){ echo "selected"; } ?> value = "10">
																																1 rata
																															</option>
																															<option <?php if(in_array(2,$_POST['vrstaug_fil'] ?? [])){ echo "selected"; } ?> value = "2" >
																																2 rate
																															</option>
																															<option <?php if(in_array(6,$_POST['vrstaug_fil'] ?? [])){ echo "selected"; } ?> value = "6">
																																3 rate
																															</option>
																															<option <?php if(in_array(8,$_POST['vrstaug_fil'] ?? [])){ echo "selected"; } ?> value = "8">
																																4 rate
																															</option>
																															<option <?php if(in_array(4,$_POST['vrstaug_fil'] ?? [])){ echo "selected"; } ?> value = "4">
																																5 rata
																															</option>
																															<option <?php if(in_array(12,$_POST['vrstaug_fil'] ?? [])){ echo "selected"; } ?> value = "12">
																																Mikrofin
																															</option>
																														</optgroup>
																														<optgroup label="Ugovor sa popustom od 100%">
																															<option <?php if(in_array(99,$_POST['vrstaug_fil'] ?? [])){ echo "selected"; } ?>  value = "99">
																																1 rata
																															</option>
																														</optgroup>
																														<!--<optgroup label="Ugovori sa popustom od 30% za kandidate sa A1 certifikatom">
																															<option  value = "71">
																																1 rata
																															</option>
																															<option value = "72">
																																2 rate
																															</option>
																															<option  value = "73">
																																3 rate
																															</option>
																															<option  value = "74">
																																4 rate
																															</option>
																															<option  value = "75">
																																5 rata
																															</option>
																														</optgroup>
																														<optgroup label="Ugovori sa popustom od 50% za kandidate sa A2 certifikatom">
																															<option  value = "51">
																																1 rata
																															</option>
																															<option value = "52">
																																2 rate
																															</option>
																															<option  value = "53">
																																3 rate
																															</option>
																															<option  value = "54">
																																4 rate
																															</option>
																															<option  value = "55">
																																5 rata
																															</option>
																														</optgroup>
																														<optgroup label="Ugovori sa popustom od 70% za kandidate sa B1,B2,C1,C2 certifikatom">
																															<option  value = "41">
																																1 rata
																															</option>
																															<option value = "42">
																																2 rate
																															</option>
																															<option  value = "43">
																																3 rate
																															</option>
																															<option  value = "44">
																																4 rate
																															</option>
																															<option  value = "45">
																																5 rata
																															</option>
																														</optgroup>
																														<optgroup label="Ugovori sa popustom od 20% za kandidate bez certifikata">
																															<option  value = "61">
																																1 rata
																															</option>
																															<option value = "62">
																																2 rate
																															</option>
																															<option  value = "63">
																																3 rate
																															</option>
																															<option  value = "64">
																																4 rate
																															</option>
																															<option  value = "65">
																																5 rata
																															</option>
																														</optgroup>
																														<optgroup label="Ugovori sa popustom od 10% za inkaso kandidate">
																															<option value = "82">
																																2 rate
																															</option>
																															<option  value = "83">
																																3 rate
																															</option>
																															<option  value = "84">
																																4 rate
																															</option>
																															<option  value = "85">
																																5 rata
																															</option>
																														</optgroup>-->
																													</select>
																												</div>
																											</div>
																										</div>
																									</div>
																									<div class="form-group">
																										<div class="col-md-offset-2 col-sm-8 text-center">
																											<label for="nivojezika_fil" class="col-sm-4 control-label">
																												Nivo poznavanja jezika:
																											</label>
																											<div class="col-sm-8">
																												<div class="">
																													<select class="selectpicker validacija_opcija_f" title = "Odaberite nivo jezika" id="nivojezika_fil" name="nivojezika_fil[]" data-selected-text-format = "count > 2" data-live-search = "true" data-actions-box="true" multiple>
																														<option <?php if(in_array(0,$_POST['nivojezika_fil'] ?? [])){ echo "selected";} ?> value = "0">Nije označeno</option>
																														<option <?php if(in_array(1,$_POST['nivojezika_fil'] ?? [])){ echo "selected";} ?> value = "1">Bez znanja</option>
																														<option <?php if(in_array(2,$_POST['nivojezika_fil'] ?? [])){ echo "selected";} ?> value = "2">A1</option>
																														<option <?php if(in_array(3,$_POST['nivojezika_fil'] ?? [])){ echo "selected";} ?> value = "3">A2</option>
																														<option <?php if(in_array(4,$_POST['nivojezika_fil'] ?? [])){ echo "selected";} ?> value = "4">B1</option>
																														<option <?php if(in_array(5,$_POST['nivojezika_fil'] ?? [])){ echo "selected";} ?> value = "5">B2</option>
																														<option <?php if(in_array(6,$_POST['nivojezika_fil'] ?? [])){ echo "selected";} ?> value = "6">C1</option>
																														<option <?php if(in_array(7,$_POST['nivojezika_fil'] ?? [])){ echo "selected";} ?> value = "7">C2</option>
																													</select>
																												</div>
																											</div>
																										</div>
																									</div>

																									<?php 
																										$queryStatusiPrijave = $db->prepare("
																														SELECT 
																															status_id,status_naziv,redoslijed_statusa
																														FROM 
																															idk_kandidat_status_prijave  
																														ORDER BY 
																															redoslijed_statusa ASC 
																															
																										");
																										$queryStatusiPrijave->execute();
																									?>
																									<div class="form-group">
																										<div class="col-md-offset-2 col-sm-8 text-center">
																											<label for="statusPrijaveFilter" class="col-sm-4 control-label">
																												Status prijave:
																											</label>
																											<div class="col-sm-8">
																												<div class="">
																													<select class="selectpicker validacija_opcija_f" title = "Odaberite struke" data-actions-box="true" data-live-search = "true" id="statusPrijaveFilter" name="statusPrijaveFilter[]" data-selected-text-format = "count > 2" multiple>
																														<?php 
																															while($rowStatus = $queryStatusiPrijave->fetch()){
																																$status_id = intval($rowStatus["status_id"]);
																																$status_naziv = $rowStatus["status_naziv"];
																																if(in_array($status_id,$_POST['statusPrijaveFilter'] ?? []))
																																echo '<option selected value = "'.$status_id.'">'.$status_naziv.'</option>';
																																else
																																echo '<option value = "'.$status_id.'">'.$status_naziv.'</option>';
																																
																															}
																														?>
																													</select>
																												</div>
																											</div>
																										</div>
																									</div>
																									<div class="modal-footer material-modal__footer"> 
																										<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
																										<button type="submit" id = "pregledDIPL_formx" class="btn btn-primary material-btn material-btn_success" form="pregledDIPL_form"><i class="fa fa-search" aria-hidden="true"></i> Završi</button>
																									</div>
																									<script>
																										$('#podstatus_novix').hide();
																										
																										$(document).ready(function(){
																											
																											document.getElementById("pregledDIPL_formx").disabled = true;
																											document.getElementById("pregledDIPL_formx").title = "Nije moguće filtrirati ako nije izabran bar jedan kriterij filtra!";
																											/*$("#status_kan_fil").change(function(){
																												console.log($('#status_kan_fil').val());
																												if(jQuery.inArray( '7', $('#status_kan_fil').val()) >= 0){
																													
																													console.log("ima arh");
																													//$('#podstatus_novix').hide();
																												}else{
																													console.log("nema arh");
																													//$('#podstatus_novix').show();
																												}
																											});*/
																											$(".validacija_opcija_f").change(function(){
																												
																												/*if(jQuery.inArray( '1', $('#status_kan_fil').val())){
																													$('#podstatus_novix').hide();
																												}else{
																													$('#podstatus_novix').show();
																												}*/
																												if ($('#drzava_kan_fil').val() || $('#status_kan_fil').val() || $('#razlog_odbijanja_kan_fil').val() || $('#vrsta_priznanja_fil').val() || $('#povijest_kan_fil').val() || $('#datum_ulaskaod_fil').val() || $('#datum_ulaskado_fil').val() || $('#s_kan_fil').val() || $('#ss_kan_fil').val() || $('#zaposlenik_fil').val() || $('#vrstaug_fil').val() || $('#nivojezika_fil').val() || $('#team_fil').val() || $('#statusPrijaveFilter').val()){
																													document.getElementById("pregledDIPL_formx").disabled = false;
																													document.getElementById("pregledDIPL_formx").title = "Opcija omogućena!";
																												}else{
																													document.getElementById("pregledDIPL_formx").disabled = true;
																													document.getElementById("pregledDIPL_formx").title = "Nije moguće filtrirati ako nije izabran bar jedan kriterij filtra!";
																												}
																											});
																										});
																									</script>
																								</form>
																							</div>
																						</div>
																					</div>
																				</div>
																			</div>
																			<div class = "col-xs-6 text-right">
																				<button id="export_DIPL" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-file-excel-o" aria-hidden="true"></i> <span>Export</span></button>
																			</div>
																		</div>
																		<div class = "row">
																			<div class = "col-xs-12">
																				<?php 
																					if($_GET['type'] == 1){
																				?>
																					<div id = "ex_hidden" style = "display: none;">
																						<input type = "text" id = "uex" value = "<?php echo $_GET['type'];?>">
																						<input type = "text" id = "teamex" value = "<?php echo $team;?>">
																					</div>
																					<script>
																						
																						$(document).ready(function() {
																							$('#export_DIPL').click(function() {
																								$('#export_DIPL').prop("disabled", true);  
																								var uex = document.getElementById("uex").value;
																								var teamex = document.getElementById("teamex").value;
																								$.ajax({
																									url: 'export_excel.php?prozor=export_DIPL',
																									type: 'POST',
																									data: {'uex':uex, 'teamex':teamex},
																									dataType: 'html',
																									success: function(data) {
																										$("#export_DIPL_div").html(data);
																										$('#export_DIPL').prop("disabled", false);
																									},
																									error: function (xhr, ajaxOptions, thrownError) {
																										alert(xhr.status);
																										alert(thrownError);
																									}
																								});
																							});
																						});
																					</script>
																				<?php
																					}
																					else if($_GET['type'] == 2){
																				?>
																					<div id = "ex_hidden" style = "display: none;">
																						<input type = "text" id = "uex" value = "<?php echo $_GET['type'];?>">
																						<input type = "text" id = "teamex" value = "<?php echo $team;?>">
																						<input type = "text" id = "ue10" value = "<?php if(isset($_POST['drzava_kan_fil'])){ echo implode(',',$_POST['drzava_kan_fil']); } else{ echo "";}?>">
																						<input type = "text" id = "ue1" value = "<?php if(isset($_POST['status_kan_fil'])){ echo implode(',',$_POST['status_kan_fil']); } else{ echo "";}?>">
																						<input type = "text" id = "ue14" value = "<?php if(isset($_POST['vrsta_priznanja_fil'])){ echo implode(',',$_POST['vrsta_priznanja_fil']); } else{ echo "";}?>">
																						<input type = "text" id = "ue2" value = "<?php if(isset($_POST['povijest_kan_fil'])){ echo implode(',',$_POST['povijest_kan_fil']); } else{ echo "";}?>">
																						<input type = "text" id = "ue9" value = "<?php if(isset($_POST['kampanja_kan_fil'])){ echo implode(',',$_POST['kampanja_kan_fil']); } else{ echo "";}?>">
																						<input type = "text" id = "ue3" value = "<?php if(isset($_POST['datum_ulaskaod_fil'])){ echo $_POST['datum_ulaskaod_fil']; } else{ echo "";}?>">
																						<input type = "text" id = "ue4" value = "<?php if(isset($_POST['datum_ulaskado_fil'])){ echo $_POST['datum_ulaskado_fil']; } else{ echo "";}?>">
																						<input type = "text" id = "ue11" value = "<?php if(isset($_POST['struka_fil'])){ echo implode(',',$_POST['struka_fil']); } else{ echo "";}?>">
																						<input type = "text" id = "ue15" value = "<?php if(isset($_POST['razlog_odbijanja_kan_fil'])){ echo implode(',',$_POST['razlog_odbijanja_kan_fil']); } else{ echo "";}?>">
																						<input type = "text" id = "ue5" value = "<?php if(isset($_POST['s_kan_fil'])){ echo implode(',',$_POST['s_kan_fil']); } else{ echo "";}?>">
																						<input type = "text" id = "ue6" value = "<?php if(isset($_POST['ss_kan_fil'])){ echo implode(',',$_POST['ss_kan_fil']); } else{ echo "";}?>">
																						<input type = "text" id = "ue7" value = "<?php if(isset($_POST['zaposlenik_fil'])){ echo implode(',',$_POST['zaposlenik_fil']); } else{ echo "";}?>">
																						<input type = "text" id = "ue8" value = "<?php if(isset($_POST['vrstaug_fil'])){ echo implode(',',$_POST['vrstaug_fil']); } else{ echo "";}?>">
																						<input type = "text" id = "ue12" value = "<?php if(isset($_POST['nivojezika_fil'])){ echo implode(',',$_POST['nivojezika_fil']); } else{ echo "";}?>">
																						<input type = "text" id = "ue13" value = "<?php if(isset($_POST['statusPrijaveFilter'])){ echo implode(',',$_POST['statusPrijaveFilter']); } else{ echo "";}?>">
																					</div>
																					<script>
																						$(document).ready(function() {
																							$('#export_DIPL').click(function() {
																								$('#export_DIPL').prop("disabled", true);
																								var uex = document.getElementById("uex").value;
																								var teamex = document.getElementById("teamex").value;
																								var ue1 = document.getElementById("ue1").value;
																								var ue2 = document.getElementById("ue2").value;
																								var ue9 = document.getElementById("ue9").value;
																								var ue3 = document.getElementById("ue3").value;
																								var ue4 = document.getElementById("ue4").value;
																								var ue11 = document.getElementById("ue11").value;
																								var ue15 = document.getElementById("ue15").value;
																								var ue5 = document.getElementById("ue5").value;
																								var ue6 = document.getElementById("ue6").value;
																								var ue7 = document.getElementById("ue7").value;
																								var ue8 = document.getElementById("ue8").value;
																								var ue10 = document.getElementById("ue10").value;
																								var ue12 = document.getElementById("ue12").value;
																								var ue13 = document.getElementById("ue13").value;
																								/*console.log(' Type:'+uex+' 1:'+ue1+' 2:'+ue2+' 3:'+ue3+' 4:'+ue4+' 5:'+ue5+' 6:'+ue6+' 7:'+ue7+' 8:'+ue8+' ');
																								alert(' Type:'+uex+' 1:'+ue1+' 2:'+ue2+' 3:'+ue3+' 4:'+ue4+' 5:'+ue5+' 6:'+ue6+' 7:'+ue7+' 8:'+ue8+' ');*/
																								$.ajax({
																									url: 'export_excel.php?prozor=export_DIPL',
																									type: 'POST',
																									data: {
																											'uex':uex,
																											'teamex':teamex,
																											'ue1':ue1,
																											'ue2':ue2,
																											'ue9':ue9,
																											'ue3':ue3,
																											'ue4':ue4,
																											'ue11':ue11,
																											'ue15':ue15,
																											'ue5':ue5,
																											'ue6':ue6,
																											'ue7':ue7,
																											'ue8':ue8,
																											'ue10':ue10,
																											'ue12':ue12,
																											'ue13':ue13
																											},
																									dataType: 'html',
																									success: function(data) {
																										$("#export_DIPL_div").html(data);
																										$('#export_DIPL').prop("disabled", false);
																									},
																									error: function (xhr, ajaxOptions, thrownError) {
																										alert(xhr.status);
																										alert(thrownError);
																									}
																								});
																							});
																						});
																					</script>
																				<?php
																					}
																				?>
																				<div id="export_DIPL_div" style = "display:none;">
																				</div>
																			</div>
																		</div>
																		<div class = "row">
																			<div class = "col-xs-12">
																				<div class = "row">
																					<div class = "col-md-offset-2 col-sm-8 text-center">
																						<?php if($_GET['type'] == 1){?>
																							<div id = "messager_type_1" style = "padding: 15px; font-size: 24px;" class="alert alert-danger" role="alert">
																								U listi se nalaze svi kandidati!<br>
																								Za konkretnu pretragu, koristite opciju <i style = "margin: 0px 3px; color: black;" class="fa fa-search" aria-hidden="true"></i><span style = "font-weight: bold; color: black;"> Filter</span>.
																							</div>
																							<script>
																								$(document).ready(function () {
																									setTimeout(function(){
																												$('#messager_type_1').hide();
																											}, 10000);
																								});
																							</script>
																						<?php }else{?>
																							<div id = "messager_type_2" style = "padding: 15px; font-size: 24px;" class="alert alert-danger" role="alert">
																								U listi se nalaze kandidati prema prethodno odabranom kriteriju!
																							</div>
																							<script>
																								$(document).ready(function () {
																									setTimeout(function(){
																												$('#messager_type_2').hide();
																											}, 10000);
																								});
																							</script>
																						<?php }?>
																					</div>
																				</div>
																			</div>
																		</div>
																		<hr>
																		<style>
																			.izgled_card_stat{
																				border: 1px solid #cccccc;
																				border-radius: 1.25rem;
																				margin: 0px;
																				padding: 20px;
																				box-shadow: 0 0 10px #cccccc;
																			}
																			.naslov_card_stat{
																				font-weight: bold;
																				padding: 5px 20px;
																				border-radius: 1.25rem;
																				color: black;
																			}
																			.podnozje_card_stat{
																				font-weight: bold;
																				background-color: #a2a2a2;
																				padding: 5px 20px;
																				border-radius: 1.25rem;
																				color: white;
																			}
																		</style>
																		<div class = "row">
																		<?php 
																		$drzave_niz=array("+387","+381","+49","os");
																		foreach ($drzave_niz as $drzava){
																			?>
																			<div class = "col-md-6 " style = "margin-top: 15px;">
																				<div class = "row izgled_card_stat">
																					<div class = "col-xs-12">
																						<?php
																							if($team == 1){
																								$uslov_stat = " id_broj_nd_kandidata is not null";
																							}else{
																								$uslov_stat = " zaduzen_zaposlenik_nd_kandidata IN (".$employess_id_team.")";
																							}
																							
																							if($drzava == "os"){
																								$uslov_drzava = "(mobilni_nd_kandidata NOT LIKE '+381%' AND mobilni_nd_kandidata NOT LIKE '+387%' AND mobilni_nd_kandidata NOT LIKE '+49%' OR mobilni_nd_kandidata is null) ";
																							}
																							else 
																								$uslov_drzava = "mobilni_nd_kandidata LIKE '".$drzava."%' ";
																							
																							$q_getcountstatusi=$db->prepare('
																								select 
																								sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=1 THEN 1 else 0 END) AS statLead,
																								sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=6 THEN 1 else 0 END) AS Nk1,
																								sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=2 THEN 1 else 0 END) AS Nk3,
																								sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=3 THEN 1 else 0 END) AS Zld,
																								sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=4 THEN 1 else 0 END) AS Nzld,
																								sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=5 THEN 1 else 0 END) AS Uobld,
																								sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=7 THEN 1 else 0 END) AS NL1,
																								sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=8 THEN 1 else 0 END) AS NL2,
																								sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=9 THEN 1 else 0 END) AS TZ,
																								sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=10 THEN 1 else 0 END) AS TOs,
																								sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=11 THEN 1 else 0 END) AS LNL,
																								sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=12 THEN 1 else 0 END) AS LNZ,
																								sum(case when status_nd_kandidata=2 THEN 1 else 0 END) AS Prdok,
																								sum(case when status_nd_kandidata=3 THEN 1 else 0 END) AS Pospos,
																								sum(case when status_nd_kandidata=4 THEN 1 else 0 END) AS Obrada,
																								sum(case when status_nd_kandidata=5 THEN 1 else 0 END) AS Dopdok,
																								sum(case when status_nd_kandidata=6 THEN 1 else 0 END) AS Zavrs,
																								sum(case when status_nd_kandidata=7 THEN 1 else 0 END) AS Arhiva
																								FROM idk_nd_kandidata WHERE '.$uslov_stat.' AND '.$uslov_drzava
																								);
																							$q_getcountstatusi->execute();
																							$row_cntst=$q_getcountstatusi->fetch();

																							$uk_br_st11_ispis2=$row_cntst['statLead'];
																							$uk_br_st16_ispis2=$row_cntst['Nk1'];
																							$uk_br_st12_ispis2=$row_cntst['Nk3'];
																							$uk_br_st13_ispis2=$row_cntst['Zld'];
																							$uk_br_st14_ispis2=$row_cntst['Nzld'];
																							$uk_br_st15_ispis2=$row_cntst['Uobld'];
																							$uk_br_st17_ispis2=$row_cntst['NL1'];
																							$uk_br_st18_ispis2=$row_cntst['NL2'];
																							$uk_br_st19_ispis2=$row_cntst['TZ'];
																							$uk_br_st20_ispis2=$row_cntst['TOs'];
																							$uk_br_st21_ispis2=$row_cntst['LNL'];
																							$uk_br_st22_ispis2=$row_cntst['LNZ'];
																							$uk_br_st2_ispis2=$row_cntst['Prdok'];
																							$uk_br_st3_ispis2=$row_cntst['Pospos'];
																							$uk_br_st4_ispis2=$row_cntst['Obrada'];
																							$uk_br_st5_ispis2=$row_cntst['Dopdok'];
																							$uk_br_st6_ispis2=$row_cntst['Zavrs'];
																							$uk_br_st7_ispis2=$row_cntst['Arhiva'];
																							$ukupan_br_ispis2=$uk_br_st11_ispis2 + $uk_br_st12_ispis2 + $uk_br_st13_ispis2 + $uk_br_st14_ispis2 + $uk_br_st15_ispis2 + $uk_br_st16_ispis2 + $uk_br_st17_ispis2 + $uk_br_st18_ispis2 + $uk_br_st19_ispis2 + $uk_br_st20_ispis2 + $uk_br_st21_ispis2 + $uk_br_st22_ispis2 + $uk_br_st2_ispis2 + $uk_br_st3_ispis2 + $uk_br_st4_ispis2 + $uk_br_st5_ispis2 + $uk_br_st6_ispis2 + $uk_br_st7_ispis2;

																							//Racunanje postotka
																							$proc_st11_ispis2 = (($uk_br_st11_ispis2 != 0 AND $ukupan_br_ispis2) ? number_format((($uk_br_st11_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '') : "0,00");
																							$proc_st12_ispis2 = (($uk_br_st12_ispis2 != 0 AND $ukupan_br_ispis2) ? number_format((($uk_br_st12_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '') : "0,00");
																							$proc_st16_ispis2 = (($uk_br_st16_ispis2 != 0 AND $ukupan_br_ispis2) ? number_format((($uk_br_st16_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '') : "0,00");
																							$proc_st13_ispis2 = (($uk_br_st13_ispis2 != 0 AND $ukupan_br_ispis2) ? number_format((($uk_br_st13_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '') : "0,00");
																							$proc_st14_ispis2 = (($uk_br_st14_ispis2 != 0 AND $ukupan_br_ispis2) ? number_format((($uk_br_st14_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '') : "0,00");
																							$proc_st15_ispis2 = (($uk_br_st15_ispis2 != 0 AND $ukupan_br_ispis2) ? number_format((($uk_br_st15_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '') : "0,00");
																							$proc_st17_ispis2 = (($uk_br_st17_ispis2 != 0 AND $ukupan_br_ispis2) ? number_format((($uk_br_st17_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '') : "0,00");
																							$proc_st18_ispis2 = (($uk_br_st18_ispis2 != 0 AND $ukupan_br_ispis2) ? number_format((($uk_br_st18_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '') : "0,00");
																							$proc_st19_ispis2 = (($uk_br_st19_ispis2 != 0 AND $ukupan_br_ispis2) ? number_format((($uk_br_st19_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '') : "0,00");
																							$proc_st20_ispis2 = (($uk_br_st20_ispis2 != 0 AND $ukupan_br_ispis2) ? number_format((($uk_br_st20_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '') : "0,00");
																							$proc_st21_ispis2 = (($uk_br_st21_ispis2 != 0 AND $ukupan_br_ispis2) ? number_format((($uk_br_st21_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '') : "0,00");
																							$proc_st22_ispis2 = (($uk_br_st22_ispis2 != 0 AND $ukupan_br_ispis2) ? number_format((($uk_br_st22_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '') : "0,00");
																							$proc_st2_ispis2 = (($uk_br_st2_ispis2 != 0 AND $ukupan_br_ispis2) ? number_format((($uk_br_st2_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '') : "0,00");
																							$proc_st3_ispis2 = (($uk_br_st3_ispis2 != 0 AND $ukupan_br_ispis2) ? number_format((($uk_br_st3_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '') : "0,00");
																							$proc_st4_ispis2 = (($uk_br_st4_ispis2 != 0 AND $ukupan_br_ispis2) ? number_format((($uk_br_st4_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '') : "0,00");
																							$proc_st5_ispis2 = (($uk_br_st5_ispis2 != 0 AND $ukupan_br_ispis2) ? number_format((($uk_br_st5_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '') : "0,00");
																							$proc_st6_ispis2 = (($uk_br_st6_ispis2 != 0 AND $ukupan_br_ispis2) ? number_format((($uk_br_st6_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '') : "0,00");
																							$proc_st7_ispis2 = (($uk_br_st7_ispis2 != 0 AND $ukupan_br_ispis2) ? number_format((($uk_br_st7_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '') : "0,00");
																						?>
																						
																						<div class = "row">
																							<div class="col-xs-12" style = "margin-bottom: 15px;">
																								<span class = "naslov_card_stat">
																									<?php
																									if($drzava == "+387")
																										echo '<img src="'.getSiteUrlr().'images/bs3d.png" width=25> Bosna i Hercegovina';
																									else if($drzava =="+381")
																										echo '<img src="'.getSiteUrlr().'images/sr3d.png" width=25> Srbija';
																									else if($drzava =="+49")
																										echo '<img src="'.getSiteUrlr().'images/de3d.png" width=25> Njemačka';
																									else if($drzava =="os")
																										echo '<img src="'.getSiteUrlr().'images/globe3d.png" width=25> Ostali';
																									
																									?>
																								</span>
																							</div>
																						</div>
																						<div class = "row">
																							<div class="col-xs-12">
																								<div id="canvas-holder" style="width:100%">
																									<canvas id="ca1<?php echo $drzava;?>"></canvas>
																								</div>
																							</div>
																						</div>
																						<script>
																							// window.onload = function() {
																								
																								//var windowsize = $(window).width();
																								var position_legend = 'left';
																								var position_graph = 'right';
																								var aspectRatioForDesktop = true;
																								var ctx11 = document.getElementById("ca1"+"<?php echo $drzava;?>").getContext('2d');

																								/*if (windowsize < 768) {
																									var position_legend = 'top';
																									var position_graph = 'bottom';
																									$('#ca1').css('height', '520');
																								}else{
																									$('#ca1').css('height', '350');
																								}
																								window.chartColors = {
																									red: 'rgb(243, 65, 60)',
																									orange: 'rgb(242, 161, 46)',
																									yellow: 'rgb(242, 228, 46)',
																									green: 'rgb(104, 195, 104)',
																									blue: 'rgb(64, 146, 217)',
																									purple: 'rgb(199, 156, 255)',
																									light_blue: 'rgb(139, 218, 242)'
																								};	*/
																							
																								var randomScalingFactor = function() {
																									return Math.round(Math.random() * 100);
																								};
																								
																								window.myDoughnut1 = new Chart(ctx11, {
																									type: 'doughnut',
																									data: {
																										datasets: [{
																											data: [
																												<?php echo $uk_br_st11_ispis2; ?>,
																												<?php echo $uk_br_st16_ispis2; ?>,
																												<?php echo $uk_br_st12_ispis2; ?>,
																												<?php echo $uk_br_st13_ispis2; ?>,
																												<?php echo $uk_br_st14_ispis2; ?>,
																												<?php echo $uk_br_st15_ispis2; ?>,
																												<?php echo $uk_br_st17_ispis2; ?>,
																												<?php echo $uk_br_st18_ispis2; ?>,
																												<?php echo $uk_br_st19_ispis2; ?>,
																												<?php echo $uk_br_st20_ispis2; ?>,
																												<?php echo $uk_br_st21_ispis2; ?>,
																												<?php echo $uk_br_st22_ispis2; ?>,
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
																												'rgb(183, 182, 249)',
																												'rgb(207, 95, 250)',
																												'rgb(117, 34, 99)',
																												'rgb(204, 177, 122)',
																												'rgb(128, 123, 70)',
																												'rgb(214, 76, 10)',
																												'rgb(242, 228, 46)',
																												'rgb(64, 146, 217)',
																												'rgb(139, 218, 242)',
																												'rgb(242, 161, 46)',
																												'rgb(104, 195, 104)',
																												'rgb(243, 65, 60)'
																											],
																											label: 'Dataset 1'
																										}],
																										labels: [
																											'Lead (<?php echo $uk_br_st11_ispis2; ?>) - <?php echo $proc_st11_ispis2; ?>%',
																											'Neuspješan Kontakt 1 (<?php echo $uk_br_st16_ispis2; ?>) - <?php echo $proc_st16_ispis2; ?>%',
																											'Neuspješan Kontakt 3 (<?php echo $uk_br_st12_ispis2; ?>) - <?php echo $proc_st12_ispis2; ?>%',
																											'Zainteresiran Lead (<?php echo $uk_br_st13_ispis2; ?>) - <?php echo $proc_st13_ispis2; ?>%',
																											'Nezainteresiran Lead (<?php echo $uk_br_st14_ispis2; ?>) - <?php echo $proc_st14_ispis2; ?>%',
																											'U obradi Lead (<?php echo $uk_br_st15_ispis2; ?>) - <?php echo $proc_st15_ispis2; ?>%',
																											'Neuspješan Lead 1 (<?php echo $uk_br_st17_ispis2; ?>) - <?php echo $proc_st17_ispis2; ?>%',
																											'Neuspješan Lead 2 (<?php echo $uk_br_st18_ispis2; ?>) - <?php echo $proc_st18_ispis2; ?>%',
																											'Termin Zainteresiran (<?php echo $uk_br_st19_ispis2; ?>) - <?php echo $proc_st19_ispis2; ?>%',
																											'Termin Ostali (<?php echo $uk_br_st20_ispis2; ?>) - <?php echo $proc_st20_ispis2; ?>%',
																											'Lead NL (<?php echo $uk_br_st21_ispis2; ?>) - <?php echo $proc_st21_ispis2; ?>%',
																											'Lead NZ (<?php echo $uk_br_st22_ispis2; ?>) - <?php echo $proc_st22_ispis2; ?>%',
																											'Prikupljanje dokumentacije (<?php echo $uk_br_st2_ispis2; ?>) - <?php echo $proc_st2_ispis2; ?>%',
																											'Poslana pošta (<?php echo $uk_br_st3_ispis2; ?>) - <?php echo $proc_st3_ispis2; ?>%',
																											'U obradi (<?php echo $uk_br_st4_ispis2; ?>) - <?php echo $proc_st4_ispis2; ?>%',
																											'Dopuna dokumentacije (<?php echo $uk_br_st5_ispis2; ?>) - <?php echo $proc_st5_ispis2; ?>%',
																											'Završen (<?php echo $uk_br_st6_ispis2; ?>) - <?php echo $proc_st6_ispis2; ?>%',
																											'Arhiviran (<?php echo $uk_br_st7_ispis2; ?>) - <?php echo $proc_st7_ispis2; ?>%'
																										]
																									},
																									options: {
																										responsive: true,
																										maintainAspectRatio: false,
																										position: position_graph,
																										legend: {
																											position: position_legend,
																											align: 'start'
																										},
																										title: {
																											display: false,
																											text: 'Ukupno: (<?php echo $ukupan_br_ispis2; ?>)'
																										},
																										animation: {
																											animateScale: true,
																											animateRotate: true
																										}
																									}
																								});
																								
																							// };
																							
																							
																						</script>
																						<div class = "row">
																							<div class="col-xs-12 text-left" style = "margin-top: 15px;">
																								<span class = "podnozje_card_stat">
																									Ukupno: <?php echo $ukupan_br_ispis2; ?>
																								</span>
																							</div>
																						</div>
																					</div>
																				</div>
																			</div>
																			<?php
																			}
																			?>
																		</div>
																		<hr>
																		<div class = "row">
																			<div class = "col-xs-12">
																				<script type="text/javascript">
																					$(document).ready(function() {
																						var table_s = $('#kandidati_pregled_ND').DataTable({
													
																							responsive: true,
																							"pageLength": 10,
																							"processing": true,
																							"serverSide": true,
																							"order": [[ 0, "desc" ]],

																							"bAutoWidth": false,

																							"aoColumns": [
																									{ "width": "5%" },
																									{ "width": "15%" },
																									{ "width": "15%" },
																									{ "width": "15%" },
																									{ "width": "10%" },
																									{ "width": "10%" },
																									{ "width": "10%" },
																									{ "width": "10%" },
																									{ "width": "10%", "bSortable": false }
																								],
																							"createdRow": function(row, data, dataIndex) {
																								var $dateCell = $(row).find('td:eq(3)'); // get first column
																								var dateOrder = $dateCell.text(); // get the ISO date 
																								//console.log(dateOrder);
																								if(dateOrder == ''){ 
																									  $dateCell
																										.addClass('text-center')
																										.data('order', dateOrder)
																										.html('<span class="label label-warning material-label material-label_warning main-container__column text-center">Nema dodano!<span>');
																								  }
																								  else{
																								  $dateCell
																									  .addClass('text-center')
																									  .data('order', dateOrder) // set it to data-order
																									  .html('<span class="label label-default material-label material-label_default main-container__column text-center">'+moment(dateOrder).format('DD.MM.YYYY'))+'</span>'; // and set the formatted text
																								  }
																							  },
																							"ajax":{
																								url :"ssdata_dipl.php?page=lista_diplnp",
																								type: "POST",
																								data:{
																									"type_ssd": '<?php echo $_GET["type"]; ?>', 
																									"team_ssd": '<?php echo $team; ?>', 
																									"drzava_ssd": '<?php if(isset($_POST["drzava_kan_fil"])){ echo implode(",",$_POST["drzava_kan_fil"]); } else{ echo "";}?>', 
																									"status_ssd": '<?php if(isset($_POST["status_kan_fil"])){ echo implode(",",$_POST["status_kan_fil"]); } else{ echo "";}?>', 
																									"vrsta_priznanja_ssd": '<?php if(isset($_POST["vrsta_priznanja_fil"])){ echo implode(",",$_POST["vrsta_priznanja_fil"]); } else{ echo "";}?>',
																									"povijest_ssd": '<?php if(isset($_POST["povijest_kan_fil"])){ echo implode(",",$_POST["povijest_kan_fil"]); } else{ echo "";}?>', 
																									"kampanja_ssd": '<?php if(isset($_POST["kampanja_kan_fil"])){ echo implode(",",$_POST["kampanja_kan_fil"]); } else{ echo "";}?>',
																									"datumod_ssd": '<?php if(isset($_POST["datum_ulaskaod_fil"])){ echo $_POST["datum_ulaskaod_fil"]; } else{ echo "";}?>', 
																									"datumdo_ssd": '<?php if(isset($_POST["datum_ulaskado_fil"])){ echo $_POST["datum_ulaskado_fil"]; } else{ echo "";}?>', 
																									"struka_ssd": '<?php if(isset($_POST["struka_fil"])){ echo implode(",",$_POST["struka_fil"]); } else{ echo "";}?>', 
																									"razlozi_odbijanja_ssd": '<?php if(isset($_POST["razlog_odbijanja_kan_fil"])){ echo implode(",",$_POST["razlog_odbijanja_kan_fil"]); } else{ echo "";}?>',
																									"skola_ssd": '<?php if(isset($_POST["s_kan_fil"])){ echo implode(",",$_POST["s_kan_fil"]); } else{ echo "";}?>', 
																									"skola_smijer_ssd": '<?php if(isset($_POST["ss_kan_fil"])){ echo implode(",",$_POST["ss_kan_fil"]); } else{ echo "";}?>', 
																									"zaposlenik_ssd": '<?php if(isset($_POST["zaposlenik_fil"])){ echo implode(",",$_POST["zaposlenik_fil"]); } else{ echo "";}?>', 
																									"vrstaugovora_ssd": '<?php if(isset($_POST["vrstaug_fil"])){ echo implode(",",$_POST["vrstaug_fil"]); } else{ echo "";}?>',
																									"nivojezika_ssd": '<?php if(isset($_POST["nivojezika_fil"])){ echo implode(",",$_POST["nivojezika_fil"]); } else{ echo "";}?>',
																									"statusPrijave_ssd": '<?php if(isset($_POST["statusPrijaveFilter"])){ echo implode(",",$_POST["statusPrijaveFilter"]); } else{ echo "";}?>',
																									"selected_team_ssd": '<?php if(isset($_POST["selected_team_fil"])){echo $_POST["selected_team_fil"];} else {echo "";}?>'
																								},
																								error: function(data){
																									$(".list-grid-error").html(""); 
																									$("#list-grid_processing").css("display","none"); 
																									console.log(data); 
																							
																								},
																							}
																						});
																					});
																				</script>
																				<table id="kandidati_pregled_ND" class="display" cellspacing="0" width="100%">
																					<thead>
																						<tr>
																							<th class="text-center">#ID</th>
																							<th>Ime i Prezime</th>
																							<th>Telefon</th>
																							<th>Zadnja komunikacija</th>
																							<th class="text-center">Status</th>
																							<th class="text-center">Vrijeme kreiranja</th>
																							<th class="text-center">Kreirao</th>
																							<th class="text-center">Menadžer</th>
																							<th class="text-center">Akcije</th>
																						</tr>
																					</thead>
																				</table>
																			</div>
																		</div>
																		<hr>
																	</div>
																</div>
															</div>
														</div>
														<div class="tab-pane fade" id="zap_pregled">
															<div class = "row">
																<div class = "col-xs-12">
																	<div class = "content_box">
																		<div class = "row">
																			<div class = "col-sm-6">
																				<div class = "row">
																					<div class = "col-xs-12">
																						<div class="row"> 
																							<h5 style = "font-weight: bold;"><i class="fa fa-users" style = "margin-right: 10px;" aria-hidden="true"></i>DIPL Menadžeri</h5>
																						</div>
																						<div class="row" style = "margin: 10px 40px;">
																							<script type="text/javascript">
																								$(document).ready(function() {
																									$('#dipl_menadzeri_table').DataTable({

																										responsive: true,

																										"order": [[ 0, "desc" ]],

																										"bAutoWidth": false,

																										"aoColumns": [
																												{ "width": "5%", "bSortable": false },
																												{ "width": "45%" },
																												{ "width": "20%" },
																												{ "width": "30%", "bSortable": false }
																											]
																									});
																								});
																							</script>
																							<table id="dipl_menadzeri_table" class="display" cellspacing="0" width="100%">
																								<thead>
																									<tr>
																										<th class="text-center">#ID</th>
																										<th>Ime i prezime</th>
																										<th class="text-center">Broj kandidata</th>
																										<th class="text-center">Pregled zaposlenika</th>
																									</tr>
																								</thead>
																								<tbody>
																									<?php
																										$broj_zap = 0;
																										if($team == 1){
																											$uslov_zaposleni_ispis = " employee_id is not null";
																										}else{
																											$uslov_zaposleni_ispis = " employee_id IN (".$employess_id_team.")";
																										}
																										$zaposleni_ispis = $db->prepare("
																																SELECT employee_id, employee_firstname, employee_lastname, employee_nostrifikacija_diploma
																																FROM idk_employees
																																WHERE (employee_nostrifikacija_diploma = 1 OR employee_nostrifikacija_diploma = 0) AND ".$uslov_zaposleni_ispis."
																																");
																										$zaposleni_ispis->execute();
																										
																										while($zaposleni_ispis_row = $zaposleni_ispis->fetch()){
																											$broj_zap++;
																											$id_zap_ispis = $zaposleni_ispis_row['employee_id'];
																											$ime_zap_ispis = $zaposleni_ispis_row['employee_firstname'];
																											$prezime_zap_ispis = $zaposleni_ispis_row['employee_lastname'];
																											$employee_nostrifikacija_diploma = $zaposleni_ispis_row['employee_nostrifikacija_diploma'];
																											if($employee_nostrifikacija_diploma == 1){
																												$employee_name_text = '<span class="label label-success material-label material-label_success main-container__column">'.$ime_zap_ispis.' '.$prezime_zap_ispis.'</span>';
																											}else{
																												$employee_name_text = '<span class="label label-danger material-label material-label_danger main-container__column">'.$ime_zap_ispis.' '.$prezime_zap_ispis.'</span>';
																											}
																											
																											$br_zaduzenja = $db->prepare("
																																SELECT COUNT(id_broj_nd_kandidata) as br_zaduz
																																FROM idk_nd_kandidata
																																WHERE zaduzen_zaposlenik_nd_kandidata = :zaduzen_zaposlenik_nd_kandidata
																																");
																											$br_zaduzenja->execute(array(
																																':zaduzen_zaposlenik_nd_kandidata' => $id_zap_ispis
																																));
																											$r_br_zaduzenja = $br_zaduzenja->fetch();
																											$uk_br_zaduzenja = $r_br_zaduzenja['br_zaduz'];
																											$br_zaduzenja_ispis = '<span class="label label-primary material-label material-label_primary main-container__column text-left">'.$uk_br_zaduzenja.'</span>';
																									?>
																									<tr>
																										<td class="text-center"><?php echo $id_zap_ispis;?></td>
																										<td class="text-center"><?php echo $employee_name_text;?></td>
																										<td class="text-center">
																											<a target="_BLANK" href="<?php getSiteURL(); ?>nostrifikacija_diploma?page=menadzer_pregled_kandidata&id_m=<?php echo $id_zap_ispis; ?>">
																												<?php echo $br_zaduzenja_ispis;?>
																											</a>
																										</td>
																										<td class="text-center">
																											<a id = "zaposlenik_detaljno_ajax<?php echo $broj_zap; ?>" data="<?php echo $id_zap_ispis; ?>" class="btn material-btn btn-success"><i class="fa fa-info-circle" aria-hidden="true"></i></a>
																											<script>
																												$("#zaposlenik_detaljno_ajax<?php echo $broj_zap; ?>").click(function () {
																													var id_zaposlenika_pregled = $(this).attr("data");
																													$.ajax({
																														url: 'ajax_data.php?page=posalji_pregled_zaposlenika_DIPL',
																														type: 'POST',
																														data: {'id_zaposlenika_pregled':id_zaposlenika_pregled},
																														dataType: 'html',
																														success: function(data) {
																															$("#detaljno_pregled_zaposlenika").html(data);
																														},
																														error: function (xhr, ajaxOptions, thrownError) {
																															alert(xhr.status);
																															alert(thrownError);
																														}
																													});
																												});	
																											</script>
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
																			<script>
																				$(document).ready(function() {
																					$("#detaljno_pregled_zaposlenika").html('<div class="alert alert-warning" role="alert">Klikom na dugme <i class="fa fa-info-circle" aria-hidden="true" style = "margin: 0px 5px; color: black;"></i> u koloni <span style = "font-weight: bold; color: black;">Pregled zaposlenika</span>, omogućuje vam se prikaz detalja za određenog zaposlenika!</div>');
																				});									
																			</script>
																			<div class = "col-sm-6">
																				<div class = "row">
																					<div class = "col-xs-12">
																						<div class="row"> 
																							<h5 style = "font-weight: bold;"><i class="fa fa-info-circle" style = "margin-right: 10px;" aria-hidden="true"></i>Pregled zaposlenika </h5>
																						</div>
																						<div id = "detaljno_pregled_zaposlenika">
																							
																						</div>
																					</div>
																				</div>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
														</div>
														<div class="tab-pane fade" id="otvoreni_ugovori">
															<div class = "row">
																<div class = "col-xs-12">
																	<div class = "content_box">
																		<div class = "row">
																			<div class = "col-xs-12">
																				<div class="row">
																					<div class="col-xs-8">
																						<h5 style = "font-weight: bold;"><i class="fa fa-times-circle" style = "margin-right: 10px;" aria-hidden="true"></i>Spisak otvorenih ugovora </h5>
																					</div>
																					<div class="col-xs-4 text-right">
																						
																					</div>
																				</div>
																				<?php
																					if(isset($_POST["datum_ugovor"])){
																						if(strpos($_POST["datum_ugovor"], 'to') !== false) {
																							$datum_ugovor = explode(" to ",$_POST["datum_ugovor"]);
																							$datum_ugovor_od = date("d.m.Y",strtotime($datum_ugovor[0]));
																							$datum_ugovor_do = date("d.m.Y",strtotime($datum_ugovor[1]));
																						}else{
																							$datum_ugovor_od = "01.05.2021";
																							$datum_ugovor_do = date("d.m.Y");
																						}
																					}else{
																						$datum_ugovor_od = "01.05.2021";
																						$datum_ugovor_do = date("d.m.Y");
																					}
																					if(isset($_POST["status_ugovor"])){
																						$status_ugovor = $_POST["status_ugovor"];
																					}else{
																						$status_ugovor = array(0,1,2,4);
																					}
																					if(isset($_POST["razlog_ugovor"])){
																						$razlog_ugovor = $_POST["razlog_ugovor"];
																					}else{
																						$razlog_ugovor = array();
																					}
																					
																					if($team == 1){
																						$uslov_statistika = " kan.id_broj_nd_kandidata is not null ";
																					}else{
																						$uslov_statistika = " kan.tim_nd_kandidata = ".$team." ";
																					}
																					
																					$query_statistika = $db->prepare("
																						SELECT 
																							sum( case when ug_status = 0 then 1 else 0 end ) AS broj1,
																							sum( case when ug_status = 1 then 1 else 0 end ) AS broj2,
																							sum( case when ug_status = 2 then 1 else 0 end ) AS broj3,
																							sum( case when ug_status = 3 then 1 else 0 end ) AS broj4,
																							sum( case when ug_status = 4 then 1 else 0 end ) AS broj5
																						FROM 
																							idk_nd_ugovori
																						JOIN 
																							idk_nd_kandidata kan
																						ON 
																							ug_kandidat_id = kan.id_broj_nd_kandidata
																						WHERE ".$uslov_statistika." AND ug_datum_slanja BETWEEN '".date('Y-m-d H:i:s',strtotime($datum_ugovor_od.' 00:00:00'))."' AND '".date('Y-m-d H:i:s',strtotime($datum_ugovor_do.' 23:59:59'))."'
																					");
																					
																					$query_statistika->execute();
																					
																					$row_statistika = $query_statistika->fetch();
																					
																					$arhiva_stat = intval($row_statistika["broj1"]);
																					$poslan_stat = intval($row_statistika["broj2"]);
																					$prihvacen_stat = intval($row_statistika["broj3"]);
																					$odbijen_stat = intval($row_statistika["broj4"]);
																					$otvoren_stat = intval($row_statistika["broj5"]);
																					
																					$ukupno_statusi = $arhiva_stat+$poslan_stat+$prihvacen_stat+$odbijen_stat+$otvoren_stat;
																					
																					//echo "Datum: ".$datum_ugovor." Status: ".$status_ugovor." Razlog: ".$razlog_ugovor."";
																				?>
																				<div class="row" style = "margin-top: 5px; margin-bottom: 20px;">
																					<div class="col-md-offset-4 col-sm-4 text-center">
																						<div id="canvas-holder" style="width:100%">
																							<canvas id="caUG"></canvas>
																						</div>
																						<script>
																							$('.otvoreni_ugovori_load').on('click', function(){
																								var ctx = document.getElementById('caUG').getContext('2d');
																								var myChart = new Chart(ctx, {
																									type: 'doughnut',
																									data: {
																										labels: [
																											'Arhiviran (<?php echo $arhiva_stat; ?>) - <?php echo ($arhiva_stat != 0) ? number_format((($arhiva_stat / $ukupno_statusi)*100), 2, ",", "") : 0; ?>%',
																											'Poslan (<?php echo $poslan_stat; ?>) - <?php echo ($poslan_stat != 0) ? number_format((($poslan_stat / $ukupno_statusi)*100), 2, ",", "") : 0; ?>%',
																											'Otvoren Link (<?php echo $otvoren_stat; ?>) - <?php echo ($otvoren_stat != 0) ? number_format((($otvoren_stat / $ukupno_statusi)*100), 2, ",", "") : 0; ?>%',
																											'Prihvaćen (<?php echo $prihvacen_stat; ?>) - <?php echo ($prihvacen_stat != 0) ? number_format((($prihvacen_stat / $ukupno_statusi)*100), 2, ",", "") : 0; ?>%',
																											'Odbijen (<?php echo $odbijen_stat; ?>) - <?php echo ($odbijen_stat != 0) ? number_format((($odbijen_stat / $ukupno_statusi)*100), 2, ",", "") : 0; ?>%'
																										],
																										datasets: [{
																											label: '# of Votes',
																											data: [
																											<?php echo $arhiva_stat; ?>,
																											<?php echo $poslan_stat; ?>,
																											<?php echo $otvoren_stat; ?>,
																											<?php echo $prihvacen_stat; ?>,
																											<?php echo $odbijen_stat; ?> 
																											],
																											backgroundColor: [
																												'rgb(243, 65, 60)',
																												'rgb(64, 146, 217)',
																												'rgb(139, 218, 242)',
																												'rgb(104, 195, 104)',
																												'rgb(242, 228, 46)'
																											]
																										}]
																									},
																									options: {
																										responsive: true,
																										position: 'right',
																										title: {
																											display: true,
																											text: 'Ukupno: (<?php echo $ukupno_statusi; ?>)'
																										},
																										legend: {
																											position: 'left',
																											align: 'start'
																										}
																									}
																								});
																							});
																						</script>
																					</div>
																				</div>
																				<div class="row" id = "showhide_poruka" style = "margin-bottom: 40px;">
																					<div class="col-md-offset-3 col-sm-6 text-center">
																						<div class="alert alert-danger" role="alert">
																							U slučaju izbora vrijednosti <span style = "font-weight: bold; color: black;">Odbijen</span> u polju <span style = "font-weight: bold; color: black;">Status</span>,
																							nije moguće odabrati ostale vrijednosti polja. 
																						</div>
																					</div>
																				</div>
																				<div class="row">
																					<div class="col-xs-12">
																						<form action="<?php getSiteURL(); ?>dipl?page=pregledDIPL&type=1" enctype="multipart/form-data" method="post" role="form" class="form-horizontal" id = "form_pregled_ugovora">
																						<div class="col-lg-3 col-md-3">
																							<div class="form-group materail-input-block materail-input-block_primary materail-input_slide-line" style="margin: 0;">
																								<label for="datum_ugovor">Datum kreiranja ugovora:</label>
																								<input type="text" class="form-control" id="datum_ugovor" name="datum_ugovor" placeholder="Datum" style="padding:17px; border-radius:0;" required>
																								<span class="materail-input-block__line"></span>
																							</div>
																						</div>
																						<script>
																							$("#datum_ugovor").flatpickr({
																								mode: "range",
																								dateFormat: "d.m.Y",
																								disableMobile: "true",
																								defaultDate: ["<?php echo $datum_ugovor_od;?>", "<?php echo $datum_ugovor_do;?>"]
																							});
																						</script>
																						<div class="col-lg-3 col-md-3">
																							<label for="status_ugovor">Status:</label>
																							<select id="status_ugovor" name="status_ugovor[]" class="selectpicker" multiple>
																								<option value="0" <?php if(in_array(0, $status_ugovor)){echo "selected";}else{echo "";}?>>Arhiviran</option>
																								<option value="1" <?php if(in_array(1, $status_ugovor)){echo "selected";}else{echo "";}?>>Poslan</option>
																								<option value="4" <?php if(in_array(4, $status_ugovor)){echo "selected";}else{echo "";}?>>Otvoren Link</option>
																								<option value="2" <?php if(in_array(2, $status_ugovor)){echo "selected";}else{echo "";}?>>Prihvaćen</option>
																								<option value="3" <?php if(in_array(3, $status_ugovor)){echo "selected";}else{echo "";}?>>Odbijen</option>
																							</select>
																						</div>
																						<div class="col-lg-3 col-md-3" id = "razlog_ugovor_showhide">
																							<label for="razlog_ugovor">Razlog odbijanja:</label>
																							<select id="razlog_ugovor" name="razlog_ugovor[]" class="selectpicker" multiple>
																								<option value="383" <?php if(in_array(383, $razlog_ugovor)){echo "selected";}else{echo "";}?>>Ostalo</option>
																								<?php 
																									$ugRaz = $db->prepare("
																										SELECT ro.id_ro, ro.naziv_ro_bs
																										FROM idk_ro_usluge ro
																										WHERE ro.tip_ro = :tip_ro AND ro.id_ro != 383
																									");
																									$ugRaz->execute(array(
																										':tip_ro' => 3
																									));
																									
																									while($rowUgRaz = $ugRaz->fetch()){
																										if(in_array($rowUgRaz["id_ro"], $razlog_ugovor)){
																											$razlog_ugovor_selected = "selected";
																										}else{
																											$razlog_ugovor_selected = " ";
																										}
																										echo '<option value="'.$rowUgRaz["id_ro"].'" '.$razlog_ugovor_selected.'>'.$rowUgRaz["naziv_ro_bs"].'</option>';
																									}
																								?>
																							</select>
																						</div>
																						<div class="col-md-3 col-lg-3 idk_margin_top20">
																							<button type="submit" class="btn btn-primary material-btn material-btn_success" form="form_pregled_ugovora"><i class="fa fa-search" aria-hidden="true"></i> Traži</button>
																						</div>
																						</form>
																					</div>
																				</div>
																				<hr>
																				<div class = "row">
																					<div class = "col-xs-12">
																						<script type="text/javascript">
																							$("#status_ugovor").change(function(){
																								var stat = $('#status_ugovor').val();
																								if (jQuery.inArray('3', $('#status_ugovor').val()) != -1){
																									$('#status_ugovor').val(3);
																									$('#status_ugovor').selectpicker('refresh');
																									$("#razlog_ugovor_showhide").show();
																									$('#showhide_poruka').show();
																									setTimeout(function(){
																										$('#showhide_poruka').hide();
																									}, 5000);
																								}else{
																									$('#razlog_ugovor').val(null);
																									$('#razlog_ugovor').selectpicker('refresh');
																									$("#razlog_ugovor_showhide").hide();
																									$('#showhide_poruka').hide();
																								}
																							});
																							$(document).ready(function() {
																								if (jQuery.inArray('3', $('#status_ugovor').val()) != -1){
																									$("#razlog_ugovor_showhide").show();
																								}else{
																									$("#razlog_ugovor_showhide").hide();
																								}
																								$('#showhide_poruka').hide();
																								var datum_ugovor = $('#datum_ugovor').val();
																								var status_ugovor = $('#status_ugovor').val();
																								var razlog_ugovor = $('#razlog_ugovor').val();
																								
																								var tableUG = $('#tableUgovori').DataTable({
															
																									responsive: true,
																									"pageLength": 10,
																									"processing": true,
																									"serverSide": true,
																									"order": [[ 0, "desc" ]],

																									"bAutoWidth": false,

																									"aoColumns": [
																										{ "width": "5%" },
																										{ "width": "20%" },
																										{ "width": "10%" },
																										{ "width": "10%" },
																										{ "width": "10%", "bSortable": false },
																										{ "width": "10%", "bSortable": false },
																										{ "width": "15%" },
																										{ "width": "15%" },
																										{ "width": "5%", "bSortable": false }
																									],
																									"ajax":{
																										url :"ssdata_dipl.php?page=lista_ugovora",
																										type: "POST",
																										data:{
																											'datum_ugovor':datum_ugovor, 'status_ugovor':status_ugovor, 'razlog_ugovor':razlog_ugovor 
																										},
																										error: function(data){
																											$(".list-grid-error").html(""); 
																											$("#list-grid_processing").css("display","none"); 
																											console.log(data); 
																									
																										},
																									}
																								});
																							});
																						</script>
																						<table id="tableUgovori" class="display" cellspacing="0" width="100%">
																							<thead>
																								<tr>
																									<th class="text-center">#</th>
																									<th class="text-center">Ime i Prezime</th>
																									<th class="text-center">Status</th>
																									<th class="text-center">Datum</th>
																									<th class="text-center">Kandidat Ugovor</th>
																									<th class="text-center">CH Ugovor</th>
																									<th class="text-center">Razlog</th>
																									<th class="text-center">Komentar</th>
																									<th class="text-center">Akcija</th>
																								</tr>
																							</thead>
																						</table>
																					</div>
																				</div>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
														</div>
														
														
														<?php if($team == 1){?>
														<div class="tab-pane fade" id="razlozi_ugovora">
															<div class = "row">
																<div class = "col-xs-12">
																	<div class = "content_box">
																		<div class = "row">
																			<div class = "col-xs-12">
																				<div class="row">
																					<div class="col-xs-8">
																						<h5 style = "font-weight: bold;"><i class="fa fa-times-circle" style = "margin-right: 10px;" aria-hidden="true"></i>Spisak razloga odbijanja ugovora </h5>
																					</div>
																					<div class="col-xs-4 text-right">
																						<a href="" data-toggle="modal" data-target="#add_new_razlog_ugovori_modal" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive">
																							<i class="fa fa-plus" aria-hidden="true">
																							</i>
																							<span>
																								Dodaj novi razlog
																							</span>
																						</a>
																						<div class="modal material-modal material-modal_success fade text-left" id="add_new_razlog_ugovori_modal">
																							<div class="modal-dialog modal-lg">
																								<div class="modal-content material-modal__content">
																									<div class="modal-header material-modal__header">
																										<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																										<h4 class="modal-title material-modal__title"><i style = "margin-right: 10px;" class="fa fa-file" aria-hidden="true"></i>Dodaj novi razlog odbijanja ugovora</h4>
																									</div> 
																									<div class="modal-body material-modal__body">
																										<form action="<?php getSiteURL(); ?>do_dipl?form=add_new_razlog_ugovori" enctype="multipart/form-data" method="post" role="form" class="form-horizontal" id = "form_add_new_razlog_ugovori_modal">
																											<div class="form-group">
																												<div class="col-md-offset-2 col-sm-8">
																													<div class="alert alert-danger text-center">
																														Sva polja <span style="font-weight: bold;">MORAJU</span> biti ispravno unešena!<br>
																														Razlozi su <span style="font-weight: bold;">VIDLJIVI</span> klijentima/kandidatima!
																													</div>
																												</div>
																											</div>
																											<div class="form-group">
																												<div class="col-md-offset-2 col-sm-8">
																													<label for="naziv_RU" class="col-sm-5 control-label">
																														<span class="text-danger">
																															*
																														</span>
																														Naziv:
																													</label>
																													<div class="col-sm-7">
																														<div class="materail-input-block materail-input-block_success">
																															<input class="form-control materail-input" type="text" name="naziv_RU" id="naziv_RU" placeholder="Nemam novca" autocomplete="off" required>
																															<span class="materail-input-block__line">
																															</span>
																														</div>
																													</div>
																												</div>
																											</div>
																											<div class="form-group">
																												<div class="col-md-offset-2 col-sm-8">
																													<label for="nazivSR_RU" class="col-sm-5 control-label">
																														<span class="text-danger">
																															*
																														</span>
																														Naziv SR:
																													</label>
																													<div class="col-sm-7">
																														<div class="materail-input-block materail-input-block_success">
																															<input class="form-control materail-input" type="text" name="nazivSR_RU" id="nazivSR_RU" placeholder="Nemam novca" autocomplete="off" required>
																															<span class="materail-input-block__line">
																															</span>
																														</div>
																													</div>
																												</div>
																											</div>
																											<div class="form-group">
																												<div class="col-md-offset-2 col-sm-8">
																													<label for="nazivDE_RU" class="col-sm-5 control-label">
																														<span class="text-danger">
																															*
																														</span>
																														Naziv DE:
																													</label>
																													<div class="col-sm-7">
																														<div class="materail-input-block materail-input-block_success">
																															<input class="form-control materail-input" type="text" name="nazivDE_RU" id="nazivDE_RU" placeholder="Ich habe kein Geld" autocomplete="off" required>
																															<span class="materail-input-block__line">
																															</span>
																														</div>
																													</div>
																												</div>
																											</div>
																											<div class="modal-footer material-modal__footer">
																												<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
																												<button type="submit" class="btn btn-primary material-btn material-btn_success" form="form_add_new_razlog_ugovori_modal"><i class="fa fa-check-square-o" aria-hidden="true"></i> Spremi</button>
																											</div>
																										</form>
																									</div>
																								</div>
																							</div>
																						</div>
																					</div>
																				</div>
																				<div class="row" style = "margin: 10px 40px;">
																					<div class = "col-xs-12">
																						<?php 
																							$razloziUgovori = $db->prepare("
																								SELECT ro.id_ro, ro.naziv_ro_bs, ro.naziv_ro_de, ro.naziv_ro_sr, ro.status_ro, emp.employee_firstname, emp.employee_lastname 
																								FROM idk_ro_usluge ro
																								INNER JOIN idk_employees emp
																								ON ro.dodao_ro = emp.employee_id
																								WHERE ro.tip_ro = :tip_ro
																							");
																							$razloziUgovori->execute(array(
																								':tip_ro' => 3
																							));
																						?>
																						<script type="text/javascript">
																							$(document).ready(function() {
																								$('#razloziUgovoriTable').DataTable({

																									responsive: true,

																									"order": [[ 0, "desc" ]],

																									 "bAutoWidth": false,

																									"aoColumns": [
																											{ "width": "5%", "bSortable": false },
																											{ "width": "20%" },
																											{ "width": "20%" },
																											{ "width": "20%" },
																											{ "width": "15%" },
																											{ "width": "10%" },
																											{ "width": "10%", "bSortable": false}
																										]
																								});
																							} );
																						</script>
																						<table id="razloziUgovoriTable" class="display" cellspacing="0" width="100%">
																							<thead>
																								<tr>
																									<th class="text-center">#ID</th>
																									<th class="text-center"><img src="images/bs3d.png" width="25"></th>
																									<th class="text-center"><img src="images/sr3d.png" width="25"></th>
																									<th class="text-center"><img src="images/de3d.png" width="25"></th>
																									<th class="text-center">Status razloga</th>
																									<th class="text-center">Dodao zaposlenik</th>
																									<th class="text-center">Akcije</th>
																								</tr>
																							</thead>
																							<tbody>
																								<?php 
																									while($rowRazloziUgovori = $razloziUgovori->fetch()){
																										$id_ro = $rowRazloziUgovori["id_ro"];
																										$naziv_ro_bs = $rowRazloziUgovori["naziv_ro_bs"];
																										$naziv_ro_de = $rowRazloziUgovori["naziv_ro_de"];
																										$naziv_ro_sr = $rowRazloziUgovori["naziv_ro_sr"];
																										if(intval($rowRazloziUgovori["status_ro"]) == 0){
																											$status_ro = '<span class="label label-danger material-label material-label_danger main-container__column text-left">Isključen</span>';
																										}else{
																											$status_ro = '<span class="label label-success material-label material-label_success main-container__column text-left">Uključen</span>';
																										}
																										$dodao_ro = $rowRazloziUgovori["employee_firstname"]." ".$rowRazloziUgovori["employee_lastname"];
																								?>
																								<tr>
																									<td class="text-center"><?php echo $id_ro;?></td>
																									<td class="text-center"><?php echo $naziv_ro_bs;?></td>
																									<td class="text-center"><?php echo $naziv_ro_sr;?></td>
																									<td class="text-center"><?php echo $naziv_ro_de;?></td>
																									<td class="text-center"><?php echo $status_ro;?></td>
																									<td class="text-center"><?php echo $dodao_ro;?></td>
																									<td class = "text-center">
																										<div class="btn-group material-btn-group">
																											<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_success idk_btn_table" data-toggle="dropdown">
																												<i class="fa fa-cogs fa-lg" aria-hidden="true">
																												</i> 
																												<span class="caret material-btn__caret">
																												</span>
																											</button>
																											<ul style = "top: 32px; left: -40px; min-width: 150px !important;" class="dropdown-menu material-dropdown-menu material-dropdown-menu_success idk_dropdown_table" role="menu">
																												<li>
																													<?php 
																														if(intval($rowRazloziUgovori["status_ro"]) == 0){
																													?>
																													<a id = "action_RU" href="#" class="material-dropdown-menu__link" 
																														data-toggle="modal" 
																														data-target="#open_action_RU"
																														data-address_ru ="<?php getSiteURL(); ?>do_dipl?form=aktiviraj_deaktiviraj_RU&action=1&id_RO=<?php echo $id_ro; ?>"
																														data-status_ru ="<?php echo intval($rowRazloziUgovori["status_ro"]); ?>"
																														>
																														<i class="fa fa-check" aria-hidden="true">
																														</i> 
																														Uključi
																													</a>
																													<?php 
																														}else{
																													?>
																													<a id = "action_RU" href="#" class="material-dropdown-menu__link" 
																														data-toggle="modal" 
																														data-target="#open_action_RU"
																														data-address_ru ="<?php getSiteURL(); ?>do_dipl?form=aktiviraj_deaktiviraj_RU&action=0&id_RO=<?php echo $id_ro; ?>"
																														data-status_ru ="<?php echo intval($rowRazloziUgovori["status_ro"]); ?>"
																														>
																														<i class="fa fa-times" aria-hidden="true">
																														</i> 
																														Isključi
																													</a>	
																													<?php
																														}
																													?>
																												</li>
																											</ul>
																										</div>
																									</td>
																								</tr>
																								<?php 
																									}
																								?>
																							</tbody>
																						</table>
																						<script>
																							$(document).on("click","#action_RU",function() {
																								var address_ru = $(this).data("address_ru");
																								var status_ru = $(this).data("status_ru");
																								document.getElementById("action_RU_submit").href = address_ru;
																								if(status_ru == 0){
																									$("#text_action_RU").html("Jeste li sigurni da želite uključiti razlog odbijanja DIPL ugovora?");
																									$("#header_action_RU").html("Uključenje razloga");
																								}else{
																									$("#text_action_RU").html("Jeste li sigurni da želite isključiti razlog odbijanja DIPL ugovora?");
																									$("#header_action_RU").html("Isključenje razloga");
																								}
																							});
																						</script>
																						<!-- Modal za aktiviranje - deaktiviranje agenta -->
																						<div class="modal material-modal material-modal_primary fade" id="open_action_RU">
																							<div class="modal-dialog">
																								<div class="modal-content material-modal__content">
																									<div class="modal-header material-modal__header">
																										<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																										<h4 class="modal-title material-modal__title">
																											<p id="header_action_RU"></p>
																										</h4>
																									</div>
																									<div class="modal-body material-modal__body">
																										<div class="row">
																											<div class="col-xs-12 text-left">
																												<p id="text_action_RU"></p>
																											</div>
																										</div>
																									</div>
																									<div class="modal-footer material-modal__footer">
																										<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
																										<a id="action_RU_submit" href="#"><button class="btn btn-primary material-btn material-btn_primary">Završi</button></a>
																									</div>
																								</div>
																							</div>
																						</div>
																					</div>
																				</div>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
														</div>
														
														<div class="tab-pane fade" id="razloziInkaso">
															<div class = "row">
																<div class = "col-xs-12">
																	<div class = "content_box">
																		<div class = "row">
																			<div class = "col-xs-12">
																				<div class="row">
																					<div class="col-xs-8">
																						<h5 style = "font-weight: bold;"><i class="fa fa-times-circle" style = "margin-right: 10px;" aria-hidden="true"></i>Inkaso razlozi </h5>
																					</div>
																					<div class="col-xs-4 text-right">
																						<a href="" data-toggle="modal" data-target="#noviInkasoRazlogModal" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive">
																							<i class="fa fa-plus" aria-hidden="true">
																							</i>
																							<span>
																								Dodaj novi razlog
																							</span>
																						</a>
																						<div class="modal material-modal material-modal_success fade text-left" id="noviInkasoRazlogModal">
																							<div class="modal-dialog modal-lg">
																								<div class="modal-content material-modal__content">
																									<div class="modal-header material-modal__header">
																										<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																										<h4 class="modal-title material-modal__title"><i style = "margin-right: 10px;" class="fa fa-file" aria-hidden="true"></i>Dodaj novi razlog</h4>
																									</div> 
																									<div class="modal-body material-modal__body">
																										<form action="<?php getSiteURL(); ?>do_dipl?form=addInkasoRazlog" enctype="multipart/form-data" method="post" role="form" class="form-horizontal" id = "formInkasoRazlog">
																											<div class="form-group">
																												<div class="col-md-offset-2 col-sm-8">
																													<label for="razlogInkaso" class="col-sm-5 control-label">
																														<span class="text-danger">
																															*
																														</span>
																														Razlog:
																													</label>
																													<div class="col-sm-7">
																														<div class="materail-input-block materail-input-block_success">
																															<input class="form-control materail-input" type="text" name="razlogInkaso" id="razlogInkaso" placeholder="Unesite razlog" autocomplete="off" required>
																															<span class="materail-input-block__line">
																															</span>
																														</div>
																													</div>
																												</div>
																											</div>
																											<div class="form-group">
																												<div class="col-md-offset-2 col-sm-8">
																													<label for="zvatiInkaso" class="col-sm-5 control-label">
																														<span class="text-danger">
																															*
																														</span>
																														Potrebno ponovo pozvati:
																													</label>
																													<div class="col-sm-7">
																														<div class="">
																															<select class="selectpicker" id="zvatiInkaso" name="zvatiInkaso" title = "Odaberite opciju" required>
																																<option value = "1">DA</option>
																																<option value = "0">NE</option>
																															</select>
																														</div>
																													</div>
																												</div>
																											</div>
																											<div class="form-group" id = "danaInkasoDiv">
																												<div class="col-md-offset-2 col-sm-8">
																													<label for="danaInkaso" class="col-sm-5 control-label">
																														<span class="text-danger">
																															*
																														</span>
																														Broj dana:
																													</label>
																													<div class="col-sm-7">
																														<div class="materail-input-block materail-input-block_success">
																															<input class="form-control materail-input" type="number" name="danaInkaso" id="danaInkaso" placeholder="Unesite period nakon kojeg se zove" autocomplete="off">
																															<span class="materail-input-block__line">
																															</span>
																														</div>
																													</div>
																												</div>
																											</div>
																											<script>
																												$(document).ready(function(){
																													$('#danaInkasoDiv').hide();
																													$("#zvatiInkaso").change(function(){
																														if (parseInt($('#zvatiInkaso').val()) == 1){
																															$('#danaInkasoDiv').show();
																															document.getElementById("danaInkaso").required = true;
																														}else{
																															$('#danaInkasoDiv').hide();
																															document.getElementById("danaInkaso").required = false;
																														}
																													});
																												});
																											</script>
																											<div class="modal-footer material-modal__footer">
																												<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
																												<button type="submit" class="btn btn-primary material-btn material-btn_success" form="formInkasoRazlog"><i class="fa fa-check-square-o" aria-hidden="true"></i> Spremi</button>
																											</div>
																										</form>
																									</div>
																								</div>
																							</div>
																						</div>
																					</div>
																				</div>
																				<div class="row" style = "margin: 10px 40px;">
																					<script type="text/javascript">
																						$(document).ready(function() {
																							$('#razloziInkasoTable').DataTable({

																								responsive: true,

																								"order": [[ 0, "desc" ]],

																								"bAutoWidth": false,

																								"aoColumns": [
																										{ "width": "5%", "bSortable": false },
																										{ "width": "40%" },
																										{ "width": "10%" },
																										{ "width": "10%", "bSortable": false },
																										{ "width": "10%", "bSortable": false },
																										{ "width": "15%", "bSortable": false },
																										{ "width": "10%", "bSortable": false }
																									]
																							});
																						});
																					</script>
																					<table id="razloziInkasoTable" class="display" cellspacing="0" width="100%">
																						<thead>
																							<tr>
																								<th class="text-center">#ID</th>
																								<th>Naziv</th>
																								<th class="text-center">Status</th>
																								<th class="text-center">Ponovno zvanje</th>
																								<th class="text-center">Broj dana</th>
																								<th class="text-center">Dodao zaposlenik</th>
																								<th class="text-center">Akcija</th>
																							</tr>
																						</thead>
																						<tbody>
																							<?php
																								$inkasoRazloziQuery = $db->prepare("
																									SELECT *
																									FROM idk_ro_usluge
																									WHERE tip_ro = :tip_ro
																								");
																								$inkasoRazloziQuery->execute(array(
																									':tip_ro' => 4
																								));
																								
																								while($inkasoRazloziRow = $inkasoRazloziQuery->fetch()){
																									$razlogId = $inkasoRazloziRow['id_ro'];
																									$razlogNazivBs = $inkasoRazloziRow['naziv_ro_bs'];
																									$razlogStatus = $inkasoRazloziRow['status_ro'];
																									$razlogPz = $inkasoRazloziRow['ponovno_zvanje_ro'];
																									$razlogBd = $inkasoRazloziRow['br_dana_ro'];
																									$razlogOdobrio = getZaposlenikimeR($inkasoRazloziRow['odobrio_ro']);
																									$razlogDodao = getZaposlenikimeR($inkasoRazloziRow['dodao_ro']);
																									if($razlogStatus == 0){
																										$razlogStatusIspis = '<span class="label label-warning material-label material-label_warning main-container__column text-left" title= "Čeka se odobrenje zaposlenika '.$razlogOdobrio.'">Na čekanju</span>';
																									}else if($razlogStatus == 1){
																										$razlogStatusIspis = '<span class="label label-success material-label material-label_success main-container__column text-left" title= "Odobrio zaposlenik '.$razlogOdobrio.'">Odobren</span>';
																									}else if($razlogStatus == 2){
																										$razlogStatusIspis = '<span class="label label-danger material-label material-label_danger main-container__column text-left" title= "Odbio zaposlenik '.$razlogOdobrio.'">Odbijen</span>';
																									}
																									
																									if($razlogPz == 1){
																										$razlogPzIspis = '<span class="label label-success material-label material-label_success main-container__column text-left">DA</span>';
																										$razlogBdIspis = '<span class="label label-success material-label material-label_success main-container__column text-left">'.$razlogBd.'</span>';
																									}else{
																										$razlogPzIspis = '<span class="label label-danger material-label material-label_danger main-container__column text-left">NE</span>';
																										$razlogBdIspis = '<span class="label label-danger material-label material-label_danger main-container__column text-left">0</span>';
																									}
																							?>
																								<tr>
																									<td class="text-center"><?php echo $razlogId; ?></td>
																									<td class="text-center"><?php echo $razlogNazivBs; ?></td>
																									<td class="text-center"><?php echo $razlogStatusIspis; ?></td>
																									<td class="text-center"><?php echo $razlogPzIspis; ?></td>
																									<td class="text-center"><?php echo $razlogBdIspis; ?></td>
																									<td class="text-center"><?php echo $razlogDodao; ?></td>
																									<td class="text-center">
																										<?php if(($logged_employee_id == intval($inkasoRazloziRow['odobrio_ro']) OR (in_array( "1" , $employee_status))) AND $razlogId != 365){ ?>
																										<a target="_BLANK" href="<?php getSiteURL(); ?>nostrifikacija_diploma?page=odobri_razlog&id=<?php echo $razlogId; ?>" class="btn material-btn btn-success">
																											<i style = "padding-right: 15px;" class="fa fa-info-circle" aria-hidden="true"></i>
																											Odobri
																										</a>
																										<?php 
																											}else{
																										?>
																										<a class="btn material-btn btn-success" title = "Nemate privilegije za ovu akciju ili nije moguće urediti razlog!">
																											<i style = "padding-right: 15px;" class="fa fa-info-circle" aria-hidden="true"></i>
																											Odobri
																										</a>
																										<?php
																											}
																										?>
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
															</div>
														</div>
														<div class="tab-pane fade" id="razlozi_pregled">
															<div class = "row">
																<div class = "col-xs-12">
																	<div class = "content_box">
																		<div class = "row">
																			<div class = "col-xs-12">
																				<div class="row">
																					<div class="col-xs-8">
																						<h5 style = "font-weight: bold;"><i class="fa fa-times-circle" style = "margin-right: 10px;" aria-hidden="true"></i>Spisak razloga odbijanja usluge nostrifikacije diploma </h5>
																					</div>
																					<div class="col-xs-4 text-right">
																						<a href="" data-toggle="modal" data-target="#add_new_razlog_modal" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive">
																							<i class="fa fa-plus" aria-hidden="true">
																							</i>
																							<span>
																								Dodaj novi razlog
																							</span>
																						</a>
																						<div class="modal material-modal material-modal_success fade text-left" id="add_new_razlog_modal">
																							<div class="modal-dialog modal-lg">
																								<div class="modal-content material-modal__content">
																									<div class="modal-header material-modal__header">
																										<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																										<h4 class="modal-title material-modal__title"><i style = "margin-right: 10px;" class="fa fa-file" aria-hidden="true"></i>Dodaj novi razlog</h4>
																									</div> 
																									<div class="modal-body material-modal__body">
																										<form action="<?php getSiteURL(); ?>do_dipl?form=add_new_razlog" enctype="multipart/form-data" method="post" role="form" class="form-horizontal" id = "form_add_new_razlog">
																											<div class="form-group">
																												<div class="col-md-offset-2 col-sm-8">
																													<label for="razlog_new" class="col-sm-5 control-label">
																														<span class="text-danger">
																															*
																														</span>
																														Razlog:
																													</label>
																													<div class="col-sm-7">
																														<div class="materail-input-block materail-input-block_success">
																															<input class="form-control materail-input" type="text" name="razlog_new" id="razlog_new" placeholder="Unesite razlog" autocomplete="off" required>
																															<span class="materail-input-block__line">
																															</span>
																														</div>
																													</div>
																												</div>
																											</div>
																											<div class="form-group">
																												<div class="col-md-offset-2 col-sm-8">
																													<label for="zvati_opet_new" class="col-sm-5 control-label">
																														<span class="text-danger">
																															*
																														</span>
																														Potrebno ponovo pozvati:
																													</label>
																													<div class="col-sm-7">
																														<div class="">
																															<select class="selectpicker" id="zvati_opet_new" name="zvati_opet_new" title = "Odaberite određenu opciju" required>
																																<option value = "1">DA</option>
																																<option value = "0">NE</option>
																															</select>
																														</div>
																													</div>
																												</div>
																											</div>
																											<div class="form-group" id = "br_dana_show_hide">
																												<div class="col-md-offset-2 col-sm-8">
																													<label for="br_dana_new" class="col-sm-5 control-label">
																														<span class="text-danger">
																															*
																														</span>
																														Broj dana:
																													</label>
																													<div class="col-sm-7">
																														<div class="materail-input-block materail-input-block_success">
																															<input class="form-control materail-input" type="number" name="br_dana_new" id="br_dana_new" placeholder="Unesite period nakon kojeg se zove" autocomplete="off">
																															<span class="materail-input-block__line">
																															</span>
																														</div>
																													</div>
																												</div>
																											</div>
																											<script>
																												$(document).ready(function(){
																													$('#br_dana_show_hide').hide();
																													$("#zvati_opet_new").change(function(){
																														if ($('#zvati_opet_new').val() == '1'){
																															$('#br_dana_show_hide').show();
																															document.getElementById("br_dana_new").required = true;
																														}else{
																															$('#br_dana_show_hide').hide();
																															document.getElementById("br_dana_new").required = false;
																														}
																													});
																												});
																											</script>
																											<div class="modal-footer material-modal__footer">
																												<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
																												<button type="submit" class="btn btn-primary material-btn material-btn_success" form="form_add_new_razlog"><i class="fa fa-check-square-o" aria-hidden="true"></i> Spremi</button>
																											</div>
																										</form>
																									</div>
																								</div>
																							</div>
																						</div>
																					</div>
																				</div>
																				<div class="row" style = "margin: 10px 40px;">
																					<script type="text/javascript">
																						$(document).ready(function() {
																							$('#razlozi_dipl').DataTable({

																								responsive: true,

																								"order": [[ 0, "desc" ]],

																								"bAutoWidth": false,

																								"aoColumns": [
																										{ "width": "5%", "bSortable": false },
																										{ "width": "50%" },
																										{ "width": "10%" },
																										{ "width": "10%", "bSortable": false },
																										{ "width": "15%", "bSortable": false },
																										{ "width": "10%", "bSortable": false }
																									]
																							});
																						});
																					</script>
																					<table id="razlozi_dipl" class="display" cellspacing="0" width="100%">
																						<thead>
																							<tr>
																								<th class="text-center">#ID</th>
																								<th>Naziv</th>
																								<th class="text-center">Status</th>
																								<th class="text-center">Ponovno zvanje</th>
																								<th class="text-center">Dodao zaposlenik</th>
																								<th class="text-center">Akcija</th>
																							</tr>
																						</thead>
																						<tbody>
																							<?php
																								$razlog_ispis = $db->prepare("
																									SELECT *
																									FROM idk_ro_usluge
																									WHERE tip_ro = 1 AND status_ro != 3
																								");
																								$razlog_ispis->execute();
																								
																								while($razlog_ispis_row = $razlog_ispis->fetch()){
																									$razlog_id = $razlog_ispis_row['id_ro'];
																									$razlog_naziv_bs = $razlog_ispis_row['naziv_ro_bs'];
																									$razlog_status = $razlog_ispis_row['status_ro'];
																									$razlog_pz = $razlog_ispis_row['ponovno_zvanje_ro'];
																									$razlog_bd = $razlog_ispis_row['br_dana_ro'];
																									$razlog_od =  getZaposlenikimeR($razlog_ispis_row['odobrio_ro']);
																									$razlog_dod =  getZaposlenikimeR($razlog_ispis_row['dodao_ro']);
																									if($razlog_status == 0){
																										$razlog_status_ispis = '<span class="label label-warning material-label material-label_warning main-container__column text-left" title= "Čeka se odobrenje zaposlenika '.$razlog_od.'">Na čekanju</span>';
																									}else if($razlog_status == 1){
																										$razlog_status_ispis = '<span class="label label-success material-label material-label_success main-container__column text-left" title= "Odobrio zaposlenik '.$razlog_od.'">Odobren</span>';
																									}else if($razlog_status == 2){
																										$razlog_status_ispis = '<span class="label label-danger material-label material-label_danger main-container__column text-left" title= "Odbio zaposlenik '.$razlog_od.'">Odbijen</span>';
																									}
																									
																									if($razlog_pz == 1){
																										$razlog_pz_ispis = '<span class="label label-success material-label material-label_success main-container__column text-left" title = "Pozvati nakon '.$razlog_bd.' dana.">DA</span>';
																									}else{
																										$razlog_pz_ispis = '<span class="label label-danger material-label material-label_danger main-container__column text-left">NE</span>';
																									}
																							?>
																								<tr>
																									<td class="text-center"><?php echo $razlog_id; ?></td>
																									<td class="text-center"><?php echo $razlog_naziv_bs; ?></td>
																									<td class="text-center"><?php echo $razlog_status_ispis; ?></td>
																									<td class="text-center"><?php echo $razlog_pz_ispis; ?></td>
																									<td class="text-center"><?php echo $razlog_dod; ?></td>
																									<td class="text-center">
																										<?php if($logged_employee_id == $razlog_ispis_row['odobrio_ro'] OR (in_array( "1" , $employee_status))){ ?>
																										<a target="_BLANK" href="<?php getSiteURL(); ?>nostrifikacija_diploma?page=odobri_razlog&id=<?php echo $razlog_id; ?>" class="btn material-btn btn-success">
																											<i style = "padding-right: 15px;" class="fa fa-info-circle" aria-hidden="true"></i>
																											Odobri
																										</a>
																										<?php
																											}else{
																										?>
																										<a class="btn material-btn btn-success" title = "Nemate privilegije za ovu akciju!">
																											<i style = "padding-right: 15px;" class="fa fa-info-circle" aria-hidden="true"></i>
																											Odobri
																										</a>
																										<?php
																											}
																										?>
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
															</div>
														</div>
														<?php }?>
													</div>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-xs-12">
												<hr>
											</div>
										</div>
									</div>
								</div>
							</div>
				<?php
							}
							else{
								echo '
									<div class="alert material-alert material-alert_danger">
										<h4>NEMATE PRIVILEGIJE!</h4>
										<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
										<br />
										<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
									</div>
								';
							}
						break;
						
						case "add_new_kandidat":
							$type = intval($_GET["type"]);
							$naslov_type = "";
							if($type == 1){
								$naslov_type = "Skraćena prijavna";
							}else if($type == 0){
								$naslov_type = "Potpuna prijavna";
							}else{
								echo '
									<div class="alert material-alert material-alert_danger">
										<h4>NEMATE PRIVILEGIJE!</h4>
										<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
										<br />
										<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
									</div>
								';
								exit();
							}
							?>
							<style>
								.form_edit_izgled{
									border: 1px solid #cccccc;
									padding: 20px 50px 20px 50px;
									border-radius: 1.25rem;
								}
							</style>
							<div class="row">
								<div class="col-xs-8">
									<h1><i class="fa fa-plus idk_color_green" aria-hidden="true" style = "margin-right: 15px;"></i> Novi kandidata - <?php echo $naslov_type; ?></h1>
								</div>
								<div class="col-xs-4 text-right idk_margin_top10">
									<a href="<?php echo $_SERVER['HTTP_REFERER']; ?>" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-8">
								</div>
								<div class="col-xs-4 text-right idk_margin_top10">
									<?php 
										if($type == 0){
									?>
									<a href="<?php getSiteURL(); ?>dipl?page=add_new_kandidat&type=1" class="btn material-btn material-btn-icon-danger material-btn_danger main-container__column material-btn-icon-responsive">
										<i style = "padding-right: 20px;" class="fa fa-paper-plane-o" aria-hidden="true"></i>
										Skraćena prijava
									</a>
									<?php 
										}else{
									?>
									<a href="<?php getSiteURL(); ?>dipl?page=add_new_kandidat&type=0" class="btn material-btn material-btn-icon-danger material-btn_danger main-container__column material-btn-icon-responsive">
										<i style = "padding-right: 20px;" class="fa fa-paper-plane" aria-hidden="true"></i>
										Potpuna prijava
									</a>
									<?php		
										}
									?>
								</div>
								<div class="col-xs-12">
									<hr />
								</div>
							</div>
							<div class="row" style = "margin-top: 20px;">
								<div class="col-md-12">
									<div class="content_box">
										
										<!--<div class="row" style = "margin-top: 15px; margin-bottom: 30px;">
											<div class="col-xs-12 text-left">
												<a href="https://job-step.net/nostrifikacija_prijava/104/bs" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus" aria-hidden="true"></i> <span>Ručna registracija Lillium</span></a>
											</div>
										</div>-->
									<?php 
										if((in_array( "1" , $employee_status)) OR $logged_employee_id == 48 OR (in_array( "7" , $employee_status))){
									?>
										<div class="row" style = "margin-top: 15px; margin-bottom: 30px;">
											<div class="col-xs-6 text-left">
												<a href="<?php getSiteURL(); ?>dipl?page=preporuka&t=1" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus" aria-hidden="true"></i> <span>Facebook/Instagram</span></a>
											</div>
											<div class="col-xs-6 text-right">
												<a href="<?php getSiteURL(); ?>dipl?page=preporuka&t=2" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus" aria-hidden="true"></i> <span>Inbound Leads</span></a>
											</div>
										</div>
									<?php 
										}
									?>
										<div class="row idk_margin_top10">
											<div class="col-md-8 col-md-offset-2">
												<?php 
													if($type == 0){
												?>
												<div class="alert alert-danger text-center" role="alert">
													<strong>Potpuna prijava!</strong><br>
													U slučaju <b>potpune prijave</b>, zabranjen je unos pogrešnih podataka.
													Ako nedostaje neki od traženih podataka, birajte opciju <b>Skraćena prijava</b>.
												</div>
												<?php 
													}else{
												?>
												<div class="alert alert-danger text-center" role="alert">
													<strong>Skraćena prijava!</strong><br>
													U slučaju <b>skraćene prijave</b>, dovoljno je unijeti tražene podatke za registraciju kandidata.
												</div>
												<?php
													}
												?>
											</div>
										</div>
										<div class="row idk_margin_top10">
											<div class="col-md-8 col-md-offset-2 form_edit_izgled">
												<form action="<?php getSiteURL(); ?>do_dipl?form=add_new_ND_cand" method="post" http-equiv="Content-type" enctype="multipart/form-data"; charset="UTF-8" class="form-horizontal" id="form_registracija_novog_ND_kandidata">
													
													<input type="hidden" name="tip_forme" value="<?php echo $type; ?>">
													<div class="form-group">
														<label for="ime_new_ND_cand" class="col-sm-5 control-label">
															<span class="text-danger">
																*
															</span>
															Ime:
														</label>
														<div class="col-sm-7">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" type="text" name="ime_new_ND_cand" id="ime_new_ND_cand" placeholder="Unesite ime" autocomplete="off" required>
																<span class="materail-input-block__line">
																</span>
															</div>
														</div>
													</div>
													<div class="form-group">
														<label for="prezime_new_ND_cand" class="col-sm-5 control-label">
															<span class="text-danger">
																*
															</span>
															Prezime:
														</label>
														<div class="col-sm-7">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" type="text" name="prezime_new_ND_cand" id="prezime_new_ND_cand" autocomplete="off" placeholder="Unesite prezime" required>
																<span class="materail-input-block__line">
																</span>
															</div>
														</div>
													</div>
													<?php 
														if($type == 0){
													?>
													<div id="adresa_preb" title = "Unesite adresu prebivališta" style = "display: block; border: 1px solid #d0d0d0; border-radius: 1.25rem; margin: 5px 0px;">
														<div class = "col-sm-12 text-center" style = "background-color: #d0d0d0; padding: 5px; border-radius: 1.25rem 1.25rem 0rem 0rem; font-size: 18px; font-weight: 500; margin-bottom: 10px;">
															Adresa prebivališta
														</div>
														<div class="form-group">
															<label for="ulica_new_ND_cand" class="col-sm-5 control-label">
																<span class="text-danger">
																	*
																</span>
																Ulica:
															</label>
															<div class="col-sm-7">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input" type="text" name="ulica_new_ND_cand" id="ulica_new_ND_cand" autocomplete="off" placeholder="Unesite ulicu" required>
																	<span class="materail-input-block__line">
																	</span>
																</div>
															</div>
														</div>
														<div class="form-group">
															<label for="postanski_broj_new_ND_cand" class="col-sm-5 control-label">
																<span class="text-danger">
																	*
																</span>
																Poštanski broj:
															</label>
															<div class="col-sm-7">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input" type="number" name="postanski_broj_new_ND_cand" id="postanski_broj_new_ND_cand" autocomplete="off" placeholder="Unesite poštanski broj" required>
																	<span class="materail-input-block__line">
																	</span>
																</div>
															</div>
														</div>
														<div class="form-group">
															<label for="grad_new_ND_cand" class="col-sm-5 control-label">
																<span class="text-danger">
																	*
																</span>
																Grad:
															</label>
															<div class="col-sm-7">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input" type="text" name="grad_new_ND_cand" id="grad_new_ND_cand" autocomplete="off" placeholder="Unesite grad" required>
																	<span class="materail-input-block__line">
																	</span>
																</div>
															</div>
														</div>
													</div>
													<?php
														}
													?>
													<div class="form-group">
														<label for="mobilni_new_ND_cand" class="col-sm-5 control-label">
															<span class="text-danger">
																*
															</span>
															Mobilni telefon:
														</label>
														<div class="col-sm-7">
															<div class="">
																<input class="form-control materail-input" type="tel" name="mobilni_new_ND_cand" id="mobilni_new_ND_cand" required>
															</div>
														</div>
													</div>
													<script>
													$( document ).ready(function($) {
														
														var telInput = document.querySelector("#mobilni_new_ND_cand");
														
														iti = window.intlTelInput(telInput, {
															utilsScript: "<?php getSiteUrl(); ?>buildTelInput/js/utils.js",
															initialCountry: "ba",
															autoPlaceholder: "aggressive",
															preferredCountries: ["ba","rs","hr","de"],
															formatOnDisplay: true,
															separateDialCode: true
														});
														var fullNumber = iti.getNumber();
														// $('input[type=tel]').on('change', function() {
														// 	console.log(iti.getNumber());
														// });
														$("form").submit(function(event) {
															
															$("#mobilni_new_ND_cand").val(iti.getNumber()); 
														});
														
														/*$.each($('#mobilni_new_ND_cand'),function(){
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
															$.each($('#mobilni_new_ND_cand'),function(){
																var telInput = $(this);	
																var telType = telInput.data('type');	
																telInput.val(telInput.intlTelInput("getNumber"));  
															});
														});	*/
													});
													</script>
													<div class="form-group">
														<label for="email_new_ND_cand" class="col-sm-5 control-label">
															<span class="text-danger">
																*
															</span>
															Email:
														</label>
														<div class="col-sm-7">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" type="email" name="email_new_ND_cand" autocomplete="off" id="email_new_ND_cand" placeholder="Unesite e-mail" required>
																<span class="materail-input-block__line"></span>
															</div>
														</div>
													</div>
													<?php 
														if($type == 0){
													?>
													<div class="form-group" title = "Odaberite ponuđenu opciju" id="srbija_pos">
														<label for="srbija_new_ND_cand" class="col-sm-5 control-label"><span class="text-danger">*</span> Da li kandidat sklapa ugovor sa JobStep Srbija:</label>
														<div class="col-sm-1">
															<label class="main-container__column material-radio-group material-radio-group_success" for="srbija_new_ND_cand_DA">
																<input type="radio" name="srbija_new_ND_cand" id="srbija_new_ND_cand_DA" class="material-radiobox" value="1">
																<span class="material-radio-group__element material-radio-group__check-radio"></span>
																<span class="material-radio-group__element material-radio-group__caption">DA</span>
															</label>
														</div>
														<div class="col-sm-1">
															<label class="main-container__column material-radio-group material-radio-group_danger" for="srbija_new_ND_cand_NE">
																<input type="radio" name="srbija_new_ND_cand" id="srbija_new_ND_cand_NE" class="material-radiobox" value="0">
																<span class="material-radio-group__element material-radio-group__check-radio"></span>
																<span class="material-radio-group__element material-radio-group__caption">NE</span>
															</label>
														</div>
													</div>
													<div id="adresa_bor" title = "Unesite adresu boravišta" style = "display: block; border: 1px solid #d0d0d0; border-radius: 1.25rem; margin: 5px 0px;">
														<div class = "col-sm-12 text-center" style = "background-color: #d0d0d0; padding: 5px; border-radius: 1.25rem 1.25rem 0rem 0rem; font-size: 18px; font-weight: 500; margin-bottom: 10px;">
															Adresa boravišta
														</div>
														<div class="form-group" style = "margin-left: 0px; margin-right: 0px;">
															<div class="col-sm-12">
																<div class="alert alert-warning text-center" role="alert">
																	U slučaju da je adresa boravišta jednaka adresi prebivališta, unesite iste podatke kao i prethodno!
																</div>
															</div>
														</div>
														<div class="form-group" style = "margin-left: 0px; margin-right: 0px;">
															<label for="ulica_new_ND_cand1" class="col-sm-5 control-label">
																<span class="text-danger">
																	*
																</span>
																Ulica:
															</label>
															<div class="col-sm-7">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input" type="text" name="ulica_new_ND_cand1" id="ulica_new_ND_cand1" autocomplete="off" placeholder="Unesite ulicu">
																	<span class="materail-input-block__line">
																	</span>
																</div>
															</div>
														</div>
														<div class="form-group" style = "margin-left: 0px; margin-right: 0px;">
															<label for="postanski_broj_new_ND_cand1" class="col-sm-5 control-label">
																<span class="text-danger">
																	*
																</span>
																Poštanski broj:
															</label>
															<div class="col-sm-7">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input" type="number" name="postanski_broj_new_ND_cand1" id="postanski_broj_new_ND_cand1" autocomplete="off" placeholder="Unesite poštanski broj">
																	<span class="materail-input-block__line">
																	</span>
																</div>
															</div>
														</div>
														<div class="form-group" style = "margin-left: 0px; margin-right: 0px;">
															<label for="grad_new_ND_cand1" class="col-sm-5 control-label">
																<span class="text-danger">
																	*
																</span>
																Grad:
															</label>
															<div class="col-sm-7">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input" type="text" name="grad_new_ND_cand1" id="grad_new_ND_cand1" autocomplete="off" placeholder="Unesite grad">
																	<span class="materail-input-block__line">
																	</span>
																</div>
															</div>
														</div>
													</div>
													<script>
														$(document).ready(function() {
															document.getElementById("srbija_new_ND_cand_NE").required = true;
															$('#adresa_bor').hide();
															$('#nadlezni_org').hide();
															document.getElementById("ulica_new_ND_cand1").required = false;
															document.getElementById("postanski_broj_new_ND_cand1").required = false;
															document.getElementById("grad_new_ND_cand1").required = false;
															document.getElementById("nadlezni_organ_new_ND_cand").required = false;
														});
														$('#srbija_new_ND_cand_NE').click(function() {
															if($('#srbija_new_ND_cand_NE').is(':checked')) { 
																$('#adresa_bor').hide();
																$('#nadlezni_org').hide();
																document.getElementById("ulica_new_ND_cand1").required = false;
																document.getElementById("postanski_broj_new_ND_cand1").required = false;
																document.getElementById("grad_new_ND_cand1").required = false;
																document.getElementById("nadlezni_organ_new_ND_cand").required = false;
															}
														});
														$('#srbija_new_ND_cand_DA').click(function() {
															if($('#srbija_new_ND_cand_DA').is(':checked')) { 
																$('#adresa_bor').show();
																$('#nadlezni_org').show();
																document.getElementById("ulica_new_ND_cand1").required = true;
																document.getElementById("postanski_broj_new_ND_cand1").required = true;
																document.getElementById("grad_new_ND_cand1").required = true;
																document.getElementById("nadlezni_organ_new_ND_cand").required = true;
															}
														});
													</script>
													<div class="form-group" id = "nadlezni_org">
														<label for="nadlezni_organ_new_ND_cand" class="col-sm-5 control-label">
															<span class="text-danger">*</span> Nadležni organ koji je izdao ličnu kartu:
														</label>
														<div class="col-sm-7">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" type="text" name="nadlezni_organ_new_ND_cand" autocomplete="off" id="nadlezni_organ_new_ND_cand" placeholder="PU, Kruševac">
																<span class="materail-input-block__line"></span>
															</div>
														</div>
													</div>
													<div class="form-group">
														<label for="jmbg_new_ND_cand" class="col-sm-5 control-label">
															<span class="text-danger">*</span>
															JMBG:
														</label>
														<div class="col-sm-7">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" type="text" name="jmbg_new_ND_cand" autocomplete="off" id="jmbg_new_ND_cand" placeholder="Unesite JMBG" required>
																<span class="materail-input-block__line"></span>
															</div>
														</div>
													</div>
													<div class="form-group">
														<label for="broj_licne_karte_new_ND_cand" class="col-sm-5 control-label">
															<span class="text-danger">*</span>
															Broj lične karte:
														</label>
														<div class="col-sm-7">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" type="text" name="broj_licne_karte_new_ND_cand" autocomplete="off" id="broj_licne_karte_new_ND_cand" placeholder="Unesite broj lične karte" required>
																<span class="materail-input-block__line"></span>
															</div>
														</div>
													</div>
													<style>
														.pokrece_icon{
															color: red;
														}
													</style>
													<div class="form-group">
														<label for="vrsta_obrade_new_ND_cand" class="col-sm-5 control-label">
															<span class="text-danger">*</span>
															Pokreće nostrifikaciju za:
														</label>
														<div class="col-sm-6">
															<div class="">
																<select class="selectpicker" id="vrsta_obrade_new_ND_cand" title = "Odaberite opciju" name="vrsta_obrade_new_ND_cand" required>
																	<option value = "1" data-subtext = "">Ausbildung</option>
																	<option value = "2" data-subtext = "Srednja stručna sprema">SSS</option>
																	<option value = "3" data-subtext = "Visoka stručna sprema">VSS</option>
																</select>
															</div>
														</div>
														<div class="col-sm-1 text-right " >
															<i class="fa fa-exclamation-triangle fa-2x pokrece_icon" aria-hidden="true" data-toggle="tooltip" data-placement="right" title="" data-original-title="Informacija mora biti tačna!"></i>
														</div>
													</div>
													<?php
														$skole_ispis_select = $db->prepare("
																					SELECT *
																					FROM idk_skole
																				");
														$skole_ispis_select->execute();
													?>
													<div class="form-group">
														<label for="skola_new_ND_cand" class="col-sm-5 control-label"><span class="text-danger">*</span>Škola:</label>
														<div class="col-sm-7">
															<div class="">
																<select class="selectpicker" id="skola_new_ND_cand" name="skola_new_ND_cand" data-live-search = "true" title = "Odaberite školu" onchange="uzmi_skola_id()" required>
																	<?php
																		while($row_skole_ispis_select = $skole_ispis_select->fetch()){
																			$skola_id1 = $row_skole_ispis_select['skola_id'];
																			$skola_naziv1 = $row_skole_ispis_select['skola_naziv'];
																			echo '<option value = "'.$skola_id1.'">'.$skola_naziv1.'</option>';
																		}
																	?>
																</select>
															</div>
														</div>
													</div>
													
													<script>
														function uzmi_skola_id() {
															var skola_id_odabrano = document.getElementById("skola_new_ND_cand").value;
															$.ajax({
																url: 'ajax_data.php?page=posalji_smjer_odabrane_skole',
																type: 'POST',
																data: {'skola_id_odabrano':skola_id_odabrano},
																dataType: 'html',
																success: function(data) {
																	$("#skola_smjer_new_ND_cand").html(data).selectpicker('refresh');
																},
																error: function (xhr, ajaxOptions, thrownError) {
																	alert(xhr.status);
																	alert(thrownError);
																}
															});
														}
													</script>
													
													<div class="form-group">
														<label for="skola_smjer_new_ND_cand" class="col-sm-5 control-label"><span class="text-danger">*</span>Smjer:</label>
														<div class="col-sm-7">
															<div class="">
																<select class="selectpicker" id="skola_smjer_new_ND_cand" data-live-search = "true" title = "Odaberite smjer" name="skola_smjer_new_ND_cand" required>
																	
																</select>
															</div>
														</div>
													</div>
													<div class="form-group">
														<label for="nivo_jezika_new_ND_cand" class="col-sm-5 control-label">
															<span class="text-danger">*</span>
															Nivo njemačkog jezika:
														</label>
														<div class="col-sm-7">
															<div class="">
																<select class="selectpicker" id="nivo_jezika_new_ND_cand" title = "Odaberite opciju" name="nivo_jezika_new_ND_cand" required>
																	<option value = "1" data-subtext = "">Bez znanja</option>
																	<option value = "2" data-subtext = "">A1</option>
																	<option value = "3" data-subtext = "">A2</option>
																	<option value = "4" data-subtext = "">B1</option>
																	<option value = "5" data-subtext = "">B2</option>
																	<option value = "6" data-subtext = "">C1</option>
																	<option value = "7" data-subtext = "">C2</option>
																</select>
															</div>
														</div>
													</div>
													<div class="form-group" title = "Odaberite ponuđenu opciju" id="poslodavac_1">
														<label for="poslodavac_new_ND_cand" class="col-sm-5 control-label"><span class="text-danger">*</span>Poslodavac u Njemačkoj:</label>
														<div class="col-sm-1">
															<label class="main-container__column material-radio-group material-radio-group_success" for="poslodavac_new_ND_cand_DA">
																<input type="radio" name="poslodavac_new_ND_cand" id="poslodavac_new_ND_cand_DA" class="material-radiobox" value="1">
																<span class="material-radio-group__element material-radio-group__check-radio"></span>
																<span class="material-radio-group__element material-radio-group__caption">DA</span>
															</label>
														</div>
														<div class="col-sm-1">
															<label class="main-container__column material-radio-group material-radio-group_danger" for="poslodavac_new_ND_cand_NE">
																<input type="radio" name="poslodavac_new_ND_cand" id="poslodavac_new_ND_cand_NE" class="material-radiobox" value="0">
																<span class="material-radio-group__element material-radio-group__check-radio"></span>
																<span class="material-radio-group__element material-radio-group__caption">NE</span>
															</label>
														</div>
													</div>
													
													<div class="form-group" title = "Odaberite ponuđenu opciju" id="poslodavac_nas_1">
														<label for="poslodavac_nas_new_ND_cand" class="col-sm-5 control-label"><span class="text-danger">*</span>Naš klijent:</label>
														<div class="col-sm-1">
															<label class="main-container__column material-radio-group material-radio-group_success" for="poslodavac_nas_new_ND_cand_DA">
																<input type="radio" name="poslodavac_nas_new_ND_cand" id="poslodavac_nas_new_ND_cand_DA" class="material-radiobox" value="1">
																<span class="material-radio-group__element material-radio-group__check-radio"></span>
																<span class="material-radio-group__element material-radio-group__caption">DA</span>
															</label>
														</div>
														<div class="col-sm-1">
															<label class="main-container__column material-radio-group material-radio-group_danger" for="poslodavac_nas_new_ND_cand_NE">
																<input type="radio" name="poslodavac_nas_new_ND_cand" id="poslodavac_nas_new_ND_cand_NE" class="material-radiobox" value="0">
																<span class="material-radio-group__element material-radio-group__check-radio"></span>
																<span class="material-radio-group__element material-radio-group__caption">NE</span>
															</label>
														</div>
													</div>
													<?php 
														$pomocna_varijabla1 = "srednje";
														$nasi_aktivni_klijenti_ispis = $db->prepare("
																							SELECT company_id, company_name, company_contact_type
																							FROM idk_companies
																							WHERE company_contact_type = 'Klijent' OR company_contact_type = 'Lead' OR company_contact_type = 'Bivši klijent' OR company_contact_type = 'Potencijalni klijent'
																						");
														$nasi_aktivni_klijenti_ispis->execute();
													?>
													<div id="poslodavac_2" title = "Odaberite našeg poslodavca" style = "display: block; border: 1px solid #d0d0d0; border-radius: 1.25rem; margin: 5px 0px;">
														<div class = "col-sm-12 text-center" style = "background-color: #d0d0d0; padding: 5px; border-radius: 1.25rem 1.25rem 0rem 0rem; font-size: 18px; font-weight: 500; margin-bottom: 10px;">
															Unos našeg poslodavca
														</div>
														<div class="form-group" style = "margin-left: 0px; margin-right: 0px;">
															<label for="poslodavac_naziv_nas_new_ND_cand" class="col-sm-5 control-label">
																<span class="text-danger">
																	*
																</span>
																Naziv našeg klijenta:
															</label>
															<div class="col-sm-7">
																<div class="">
																	<select class="selectpicker" id="poslodavac_naziv_nas_new_ND_cand" name="poslodavac_naziv_nas_new_ND_cand" title = "Odaberite našeg klijenta" data-live-search="true">
																		
																		<?php
																			while($row_nasi_aktivni_klijenti_ispis = $nasi_aktivni_klijenti_ispis->fetch()){
																				$id_naseg_klijenta_ND = $row_nasi_aktivni_klijenti_ispis['company_id'];
																				$naziv_naseg_klijenta_ND = $row_nasi_aktivni_klijenti_ispis['company_name'];
																				$vrsta_naseg_klijenta_ND = $row_nasi_aktivni_klijenti_ispis['company_contact_type'];
																				
																				echo '<option value = "'.$id_naseg_klijenta_ND.'" data-subtext = "'.$vrsta_naseg_klijenta_ND.'">'.$naziv_naseg_klijenta_ND.'</option>';
																			}
																		?>
																	</select>
																</div>
															</div>
														</div>
													</div>
													<div id="poslodavac_3" title = "Ispunite sve stavke iz polja informacije o poslodavcu" style = "display: block; border: 1px solid #d0d0d0; border-radius: 1.25rem; margin: 5px 0px;">
														<div class = "col-sm-12 text-center" style = "background-color: #d0d0d0; padding: 5px; border-radius: 1.25rem 1.25rem 0rem 0rem; font-size: 18px; font-weight: 500; margin-bottom: 10px;">
															Unos informacija o poslodavcu
														</div>
														<div class="form-group" style = "margin-left: 0px; margin-right: 0px;" >
															<label for="poslodavac_naziv_new_ND_cand" class="col-sm-5 control-label">
																<span class="text-danger">
																	*
																</span>
																Naziv poslodavca:
															</label>
															<div class="col-sm-7">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input" type="text" name="poslodavac_naziv_new_ND_cand" id="poslodavac_naziv_new_ND_cand" autocomplete="off" placeholder="Unesite naziv poslodavca">
																	<span class="materail-input-block__line">
																	</span>
																</div>
															</div>
														</div>
														<div class="form-group" style = "margin-left: 0px; margin-right: 0px;">
															<label for="poslodavac_ulica_new_ND_cand" class="col-sm-5 control-label">
																<span class="text-danger">
																	*
																</span>
																Ulica poslodavca:
															</label>
															<div class="col-sm-7">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input" type="text" name="poslodavac_ulica_new_ND_cand" id="poslodavac_ulica_new_ND_cand" autocomplete="off" placeholder="Unesite ulicu">
																	<span class="materail-input-block__line">
																	</span>
																</div>
															</div>
														</div>
														<div class="form-group" style = "margin-left: 0px; margin-right: 0px;">
															<label for="poslodavac_postanski_broj_new_ND_cand" class="col-sm-5 control-label">
																<span class="text-danger">
																	*
																</span>
																Poštanski broj poslodavca:
															</label>
															<div class="col-sm-7">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input" type="number" name="poslodavac_postanski_broj_new_ND_cand" id="poslodavac_postanski_broj_new_ND_cand" autocomplete="off" placeholder="Unesite poštanski broj">
																	<span class="materail-input-block__line">
																	</span>
																</div>
															</div>
														</div>
														<div class="form-group" style = "margin-left: 0px; margin-right: 0px;">
															<label for="poslodavac_grad_new_ND_cand" class="col-sm-5 control-label">
																<span class="text-danger">
																	*
																</span>
																Grad poslodavca:
															</label>
															<div class="col-sm-7">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input" type="text" name="poslodavac_grad_new_ND_cand" id="poslodavac_grad_new_ND_cand" autocomplete="off" placeholder="Unesite grad">
																	<span class="materail-input-block__line">
																	</span>
																</div>
															</div>
														</div>
														<div class="form-group" style = "margin-left: 0px; margin-right: 0px;">
															<label for="poslodavac_regija_new_ND_cand" class="col-sm-5 control-label">
																<span class="text-danger">
																	*
																</span>
																Regija poslodavca:
															</label>
															<div class="col-sm-7">
																<div class="">
																	<select class="selectpicker" id="poslodavac_regija_new_ND_cand" name="poslodavac_regija_new_ND_cand" data-live-search = "true" title = "Odaberite regiju">
																		<option value = "Baden-Württemberg">Baden-Württemberg</option>
																		<option value = "Bayern" >Bayern</option>
																		<option value = "Berlin" >Berlin</option>
																		<option value = "Brandenburg" >Brandenburg</option>
																		<option value = "Bremen" >Bremen</option>
																		<option value = "Hamburg" >Hamburg</option>
																		<option value = "Hessen" >Hessen</option>
																		<option value = "Mecklenburg-Vorpommern" >Mecklenburg-Vorpommern</option>
																		<option value = "Niedersachsen" >Niedersachsen</option>
																		<option value = "Nordrhein-Westfalen" >Nordrhein-Westfalen</option>
																		<option value = "Rheinland-Pfalz" >Rheinland-Pfalz</option>
																		<option value = "Saarland" >Saarland</option>
																		<option value = "Sachsen" >Sachsen</option>
																		<option value = "Sachsen-Anhalt" >Sachsen-Anhalt</option>
																		<option value = "Schleswig-Holstein" >Schleswig-Holstein</option>
																		<option value = "Thüringen">Thüringen</option>
																	</select>
																</div>
															</div>
														</div>
														<div class="form-group" style = "margin-left: 0px; margin-right: 0px;">
															<label for="poslodavac_drzava_new_ND_cand" class="col-sm-5 control-label">
																<span class="text-danger">
																	*
																</span>
																Država poslodavca:
															</label>
															<div class="col-sm-7">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input" type="text" name="poslodavac_drzava_new_ND_cand" id="poslodavac_drzava_new_ND_cand" autocomplete="off" placeholder="Unesite državu">
																	<span class="materail-input-block__line">
																	</span>
																</div>
															</div>
														</div>
														<div class="form-group"  style = "margin-left: 0px; margin-right: 0px;" title = "Odaberite ponuđenu opciju" id="pos_kont">
															<label for="poslodavac_kont_da_ne_ND_cand" class="col-sm-5 control-label"><span class="text-danger">*</span> Poznati podaci kontakt osobe:</label>
															<div class="col-sm-1">
																<label class="main-container__column material-radio-group material-radio-group_success" for="pos_kont_ND_cand_DA">
																	<input type="radio" name="poslodavac_kont_da_ne_ND_cand" id="pos_kont_ND_cand_DA" class="material-radiobox" value="1">
																	<span class="material-radio-group__element material-radio-group__check-radio"></span>
																	<span class="material-radio-group__element material-radio-group__caption">DA</span>
																</label>
															</div>
															<div class="col-sm-1">
																<label class="main-container__column material-radio-group material-radio-group_danger" for="pos_kont_ND_cand_NE">
																	<input type="radio" name="poslodavac_kont_da_ne_ND_cand" id="pos_kont_ND_cand_NE" class="material-radiobox" value="0">
																	<span class="material-radio-group__element material-radio-group__check-radio"></span>
																	<span class="material-radio-group__element material-radio-group__caption">NE</span>
																</label>
															</div>
														</div>
														<div id="kontakt_osoba1" title = "Ispunite sve stavke iz polja informacije o kontakt osobi">
															<div class="form-group" style = "margin-left: 0px; margin-right: 0px;">
																<label for="poslodavac_ime_new_ND_cand" class="col-sm-5 control-label">
																	<span class="text-danger">
																		*
																	</span>
																	Ime kontakta:
																</label>
																<div class="col-sm-7">
																	<div class="materail-input-block materail-input-block_success">
																		<input class="form-control materail-input" type="text" name="poslodavac_ime_new_ND_cand" id="poslodavac_ime_new_ND_cand" autocomplete="off" placeholder="Unesite ime kontakta">
																		<span class="materail-input-block__line">
																		</span>
																	</div>
																</div>
															</div>
															<div class="form-group" style = "margin-left: 0px; margin-right: 0px;">
																<label for="poslodavac_prezime_new_ND_cand" class="col-sm-5 control-label">
																	<span class="text-danger">
																		*
																	</span>
																	Prezime kontakta:
																</label>
																<div class="col-sm-7">
																	<div class="materail-input-block materail-input-block_success">
																		<input class="form-control materail-input" type="text" name="poslodavac_prezime_new_ND_cand" id="poslodavac_prezime_new_ND_cand" autocomplete="off" placeholder="Unesite prezime kontakta">
																		<span class="materail-input-block__line">
																		</span>
																	</div>
																</div>
															</div>
															<div class="form-group" style = "margin-left: 0px; margin-right: 0px;">
																<label for="poslodavac_mail_new_ND_cand" class="col-sm-5 control-label">
																	<span class="text-danger">
																		*
																	</span>
																	Email kontakta:
																</label>
																<div class="col-sm-7">
																	<div class="materail-input-block materail-input-block_success">
																		<input class="form-control materail-input" type="email" name="poslodavac_mail_new_ND_cand" id="poslodavac_mail_new_ND_cand" autocomplete="off" placeholder="Unesite mail kontakta">
																		<span class="materail-input-block__line">
																		</span>
																	</div>
																</div>
															</div>
															<div class="form-group" style = "margin-left: 0px; margin-right: 0px;">
																<label for="poslodavac_kontakt_new_ND_cand" class="col-sm-5 control-label">
																	<span class="text-danger">
																		*
																	</span>
																	Telefon kontakta:
																</label>
																<div class="col-sm-7">
																	<div class="">
																		<input class="form-control materail-input" type="tel" name="poslodavac_kontakt_new_ND_cand" id="poslodavac_kontakt_new_ND_cand" placeholder="Unesite kontakt broj">
																		
																	</div>
																</div>
															</div>
														</div>
														<script>
															$( document ).ready(function($) {
																
																var telInput_posl = document.querySelector("#poslodavac_kontakt_new_ND_cand");
																
																iti_posl = window.intlTelInput(telInput_posl, {
																	utilsScript: "<?php getSiteUrl(); ?>buildTelInput/js/utils.js",
																	initialCountry: "ba",
																	autoPlaceholder: "aggressive",
																	preferredCountries: ["ba","rs","hr","de"],
																	formatOnDisplay: true,
																	separateDialCode: true
																});
																var fullNumber_posl = iti.getNumber();
																$('input[id=poslodavac_kontakt_new_ND_cand]').on('change', function() {
																	console.log(iti_posl.getNumber());
																});
																$("form").submit(function(event) {
																	
																	$("#poslodavac_kontakt_new_ND_cand").val(iti_posl.getNumber()); 
																});
																
															});
														</script>
													</div>
													<script>
														$(document).ready(function() {
															document.getElementById("poslodavac_new_ND_cand_NE").required = true;
															$('#poslodavac_nas_1').hide();
															$('#poslodavac_2').hide();
															$('#poslodavac_3').hide();
															$('#kontakt_osoba1').hide();
														});
													
														$('#poslodavac_new_ND_cand_DA').click(function() {
															if($('#poslodavac_new_ND_cand_DA').is(':checked')) { 
																$('#poslodavac_nas_1').show();
																document.getElementById("poslodavac_nas_new_ND_cand_NE").required = true;
															}
														});
														$('#poslodavac_new_ND_cand_NE').click(function() {
															if($('#poslodavac_new_ND_cand_NE').is(':checked')) { 
																$('#poslodavac_nas_1').hide();
																$('#poslodavac_2').hide();
																$('#poslodavac_3').hide();
																$('#kontakt_osoba1').hide();
																$("#poslodavac_nas_new_ND_cand_DA").prop( "checked", false );
																$("#poslodavac_nas_new_ND_cand_NE").prop( "checked", false );
																$("#pos_kont_ND_cand_DA").prop( "checked", false );
																$("#pos_kont_ND_cand_NE").prop( "checked", false );
																document.getElementById("poslodavac_nas_new_ND_cand_NE").required = false;
																document.getElementById("poslodavac_naziv_nas_new_ND_cand").required = false;
																document.getElementById("poslodavac_naziv_new_ND_cand").required = false;
																document.getElementById("poslodavac_ulica_new_ND_cand").required = false;
																document.getElementById("poslodavac_postanski_broj_new_ND_cand").required = false;
																document.getElementById("poslodavac_grad_new_ND_cand").required = false;
																document.getElementById("poslodavac_regija_new_ND_cand").required = false;
																document.getElementById("poslodavac_drzava_new_ND_cand").required = false;
																document.getElementById("pos_kont_ND_cand_NE").required = false;
																document.getElementById("poslodavac_ime_new_ND_cand").required = false;
																document.getElementById("poslodavac_prezime_new_ND_cand").required = false;
																document.getElementById("poslodavac_mail_new_ND_cand").required = false;
																document.getElementById("poslodavac_kontakt_new_ND_cand").required = false;
															}
														});
														$('#poslodavac_nas_new_ND_cand_DA').click(function() {
															if($('#poslodavac_nas_new_ND_cand_DA').is(':checked')) { 
																$('#poslodavac_2').show();
																$('#poslodavac_3').hide();
																$('#kontakt_osoba1').hide();
																$("#pos_kont_ND_cand_DA").prop( "checked", false );
																$("#pos_kont_ND_cand_NE").prop( "checked", false );
																document.getElementById("poslodavac_naziv_nas_new_ND_cand").required = true;
																document.getElementById("poslodavac_naziv_new_ND_cand").required = false;
																document.getElementById("poslodavac_ulica_new_ND_cand").required = false;
																document.getElementById("poslodavac_postanski_broj_new_ND_cand").required = false;
																document.getElementById("poslodavac_grad_new_ND_cand").required = false;
																document.getElementById("poslodavac_regija_new_ND_cand").required = false;
																document.getElementById("poslodavac_drzava_new_ND_cand").required = false;
																document.getElementById("pos_kont_ND_cand_NE").required = false;
																document.getElementById("poslodavac_ime_new_ND_cand").required = false;
																document.getElementById("poslodavac_prezime_new_ND_cand").required = false;
																document.getElementById("poslodavac_mail_new_ND_cand").required = false;
																document.getElementById("poslodavac_kontakt_new_ND_cand").required = false;
															}
														});
														$('#poslodavac_nas_new_ND_cand_NE').click(function() {
															if($('#poslodavac_nas_new_ND_cand_NE').is(':checked')) { 
																$('#poslodavac_3').show();
																$('#poslodavac_2').hide();
																$('#kontakt_osoba1').hide();
																$("#pos_kont_ND_cand_DA").prop( "checked", false );
																$("#pos_kont_ND_cand_NE").prop( "checked", false );
																document.getElementById("poslodavac_naziv_nas_new_ND_cand").required = false;
																document.getElementById("poslodavac_naziv_new_ND_cand").required = true;
																document.getElementById("poslodavac_ulica_new_ND_cand").required = true;
																document.getElementById("poslodavac_postanski_broj_new_ND_cand").required = true;
																document.getElementById("poslodavac_grad_new_ND_cand").required = true;
																document.getElementById("poslodavac_regija_new_ND_cand").required = true;
																document.getElementById("poslodavac_drzava_new_ND_cand").required = true;
																document.getElementById("pos_kont_ND_cand_NE").required = true;
																/*document.getElementById("poslodavac_ime_new_ND_cand").required = true;
																document.getElementById("poslodavac_prezime_new_ND_cand").required = true;
																document.getElementById("poslodavac_mail_new_ND_cand").required = true;
																document.getElementById("poslodavac_kontakt_new_ND_cand").required = true;*/
															}
														});
														$('#pos_kont_ND_cand_NE').click(function(){
															if($('#pos_kont_ND_cand_NE').is(':checked')) {
																$('#kontakt_osoba1').hide();
																document.getElementById("poslodavac_ime_new_ND_cand").required = false;
																document.getElementById("poslodavac_prezime_new_ND_cand").required = false;
																document.getElementById("poslodavac_mail_new_ND_cand").required = false;
																document.getElementById("poslodavac_kontakt_new_ND_cand").required = false;
															}
														});
														$('#pos_kont_ND_cand_DA').click(function(){
															if($('#pos_kont_ND_cand_DA').is(':checked')) {
																$('#kontakt_osoba1').show();
																document.getElementById("poslodavac_ime_new_ND_cand").required = true;
																document.getElementById("poslodavac_prezime_new_ND_cand").required = true;
																document.getElementById("poslodavac_mail_new_ND_cand").required = true;
																document.getElementById("poslodavac_kontakt_new_ND_cand").required = true;
															}
														});
													</script>
													<?php 
														}
													?>
													<div class="form-group">
														<label for="koment_new_ND_cand" class="col-sm-5 control-label">Komentar:</label>
														<div class="col-sm-7">
															<div class="materail-input-block materail-input-block_primary materail-input_slide-line">
																<textarea class="form-control materail-input material-textarea" name="koment_new_ND_cand" id="koment_new_ND_cand" placeholder="Dodajte komentar..." rows="4"></textarea>
																<span class="materail-input-block__line"></span>
															</div>
														</div>
													</div>
													<div class="modal-footer material-modal__footer" style = "text-align: center;">
														<button class="btn material-btn material-btn" data-dismiss="modal">
															Odustani
														</button>
														<button type="submit" class="btn btn-primary material-btn material-btn_success" form="form_registracija_novog_ND_kandidata">
															<i class="fa fa-check-square-o" aria-hidden="true" style = "margin-right: 10px;">
															</i>
															Završi
														</button>
													</div>
													<div class="row" style = "margin-top: 15px;">
														<div class="col-md-8 col-md-offset-2 text-center">
															<small>
																Sva polja označena sa 
																<span class="text-danger">
																	*
																</span>  
																su obavezna!
															</small>
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
						
						case "prebaciKandidata":
							if(isset($_GET["id"])){
								$id = intval($_GET["id"]);
								$query = $db->prepare("SELECT id_broj_nd_kandidata, ime_nd_kandidata, prezime_nd_kandidata, mobilni_nd_kandidata, email_nd_kandidata, vrijeme_kreiranja_nd_kandidata, zaduzen_zaposlenik_nd_kandidata FROM idk_nd_kandidata WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata");
								$query->execute(array(':id_broj_nd_kandidata' => $id ));
								$row = $query->fetch();
								$ime = $row["ime_nd_kandidata"];
								$prezime = $row["prezime_nd_kandidata"];
								$mobilni = $row["mobilni_nd_kandidata"];
								$email = $row["email_nd_kandidata"];
								$kreiran_datum = date("d.m.Y H:i", strtotime($row["vrijeme_kreiranja_nd_kandidata"]));
								$zaduzen_id = $row["zaduzen_zaposlenik_nd_kandidata"];
								$zaduzen = getZaposlenikimeR($row["zaduzen_zaposlenik_nd_kandidata"]);
				?>
								<div class="row">
									<div class="col-xs-8">
										<h1>
											<i class="fa fa-hashtag idk_color_green" style = "margin-right: 15px;" aria-hidden="true"></i>
												<?php echo " ".$id." "; ?>
											<i class="fa fa-user-circle-o idk_color_green" style = "margin-left: 15px; margin-right: 15px;" aria-hidden="true"></i>
												<?php echo " ".$ime." ".$prezime ." "; ?>
										</h1>
									</div>  
									<div class="col-xs-4 text-right idk_margin_top10">
										<?php 
											if(isset($_SERVER['HTTP_REFERER'])){
										?>
										<a href="<?php echo $_SERVER['HTTP_REFERER']; ?>" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
										<?php 
											}
										?>
									</div>
								</div>
								<div class = "row">
									<div class="col-xs-12">
										<hr/>
									</div>
								</div>
								
								<div class="row" style = "margin-top: 20px;">
									<div class="col-md-12">
										<div class="content_box">
											<div class = "row">
												<div class="col-md-6 col-md-offset-3">
													<div class = "row">
														<div class = "col-xs-12">
															<div class="alert alert-danger text-center" role="alert">
																Da bi se ostvario potpuni pregled profila kandidata - potrebno je istog prebaciti određenom zaposleniku, jer je kandidat po prijavi stavljen u Skladište Kandidata.
															</div>
														</div>
													</div>
													<hr/>
													<div class = "row">
														<div class = "col-xs-12">
															<div class="row"> 
																<div class="col-xs-12">
																	<h5 style = "font-weight: bold;"><i class="fa fa-info-circle" style = "margin-right: 10px;" aria-hidden="true"></i>Osnovne informacije</h5>
																</div>
															</div>
															<div class="row" style = "padding-top: 5px;">
																<div class = "col-xs-1 text-center">
																	<i class="fa fa-user-o" aria-hidden="true"></i>
																</div>
																<div style = "font-weight: bold;" class = "col-xs-3 text-right">
																	Ime i prezime:
																</div>
																<div class = "col-xs-8 text-left">
																	<?php echo " ".$ime." ".$prezime ." ";?>
																</div>
															</div>
															<div class="row" style = "padding-top: 5px;">
																<div class = "col-xs-1 text-center">
																	<i class="fa fa-envelope-o" aria-hidden="true"></i>
																</div>
																<div style = "font-weight: bold;" class = "col-xs-3 text-right">
																	E-mail:
																</div>
																<div class = "col-xs-8 text-left">
																	<?php echo " ".$email." ";?>
																</div>
															</div>
															<div class="row" style = "padding-top: 5px;">
																<div class = "col-xs-1 text-center">
																	<i class="fa fa-mobile" aria-hidden="true"></i>
																</div>
																<div style = "font-weight: bold;" class = "col-xs-3 text-right">
																	Mobitel:
																</div>
																<div class = "col-xs-8 text-left">
																	<?php echo " ".$mobilni." ";?>
																</div>
															</div>
															<div class="row" style = "padding-top: 5px;">
																<div class = "col-xs-1 text-center">
																	<i class="fa fa-calendar" aria-hidden="true"></i>
																</div>
																<div style = "font-weight: bold;" class = "col-xs-3 text-right">
																	Vrijeme kreiranja:
																</div>
																<div class = "col-xs-8 text-left" style = "word-break: break-all;">
																	<?php echo " ".$kreiran_datum." ";?>
																</div>
															</div>
															<div class="row" style = "padding-top: 5px;">
																<div class = "col-xs-1 text-center">
																	<i class="fa fa-cog" aria-hidden="true"></i>
																</div>
																<div style = "font-weight: bold;" class = "col-xs-3 text-right">
																	Zaduženje:
																</div>
																<div class = "col-xs-8 text-left">
																	<?php echo " ".$zaduzen." ";?>
																</div>
															</div>
														</div>
													</div>
													<div class = "row">
														<div class = "col-xs-12 text-center" style = "padding-top: 50px;">
															<?php 
																if($logged_employee_id == 11 OR $logged_employee_id == 32 OR $logged_employee_id == 33 OR (in_array( "1" , $employee_status)) OR (in_array( "2" , $employee_supervizor)) OR (in_array( "3" , $employee_supervizor)) OR (in_array( "15" , $employee_supervizor))){
																	$team = getLoggedEmployeeTeam();
																	if($logged_employee_id == 11 OR $logged_employee_id == 32 OR $logged_employee_id == 33 OR (in_array( "1" , $employee_status))){
																		$query_uslov = " employee_id is not null ";
																	}else{
																		$query_uslov = " employee_id IN (".implode(", ", getIdOfEmployeeTeam($team)).")";
																	}
																	
															?>
																	<a href="" data-toggle="modal" data-target="#prebaci_drugom" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive">
																		<i class="fa fa-plus" aria-hidden="true">
																		</i>
																		<span>
																			Prebaci zaposleniku
																		</span>
																	</a>
																	<div class="modal material-modal material-modal_success fade text-left" id="prebaci_drugom">
																		<div class="modal-dialog modal-lg">
																			<div class="modal-content material-modal__content">
																				<div class="modal-header material-modal__header">
																					<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																					<h4 class="modal-title material-modal__title"><i style = "margin-right: 10px;" class="fa fa-file" aria-hidden="true"></i>Promjena zaduženja</h4>
																				</div> 
																				<div class="modal-body material-modal__body">
																					<form action="<?php getSiteURL(); ?>nostrifikacija_diploma?page=prebaci_drugom_zaposlenom" enctype="multipart/form-data" method="post" role="form" class="form-horizontal" id = "form_prebaci_drugom">
																						
																						<input type="hidden" name="id_kand_menadzer_nd_new" value="<?php echo $id; ?>" />
																						<input type="hidden" name="id_kand_stari_menadzer_nd_new" value="<?php echo $zaduzen_id; ?>" />
																						
																						<div class="form-group">
																							<div class="col-md-offset-2 col-sm-8">
																								<label for="id_zapos_menadzer_nd_new" class="col-sm-3 control-label">
																									<span class="text-danger">
																										*
																									</span>
																									Zaposlenik:
																								</label>
																								<div class="col-sm-9">
																									<div class="">
																										<?php
																											$broj_telefona = substr($mobilni, 1, 3);
																											if(strpos($broj_telefona, "387") !== false){
																												//Srbija
																												$drzava = "bih";
																											}else if(strpos($broj_telefona, "381") !== false){
																												//Bosna
																												$drzava = "srb";
																											}else if(strpos($broj_telefona, "49") !== false){
																												//Njemacka
																												$drzava = "de";
																											}else{
																												//Ostalo
																												$drzava = "ostalo";
																											}
																										
																											$uzmi_podatke = $db->prepare("
																												SELECT emp.employee_id, emp.employee_firstname, emp.employee_lastname
																												FROM idk_employees emp 
																												INNER JOIN idk_nd_limiti lim
																												ON lim.lt_emp_id = emp.employee_id 
																												WHERE emp.employee_id != 139 AND emp.employee_nostrifikacija_diploma IN (0,1) AND ".$query_uslov." AND lim.lt_drzava LIKE '%".$drzava."%' AND emp.employee_status != 0
																											");
																											$uzmi_podatke->execute();
																										?>
																										<select class="selectpicker" title = "Odaberite novog menadžera" data-live-search = "true" id="id_zapos_menadzer_nd_new" name="id_zapos_menadzer_nd_new" required>
																										
																											<?php
																												while($uzmi_podatke_row = $uzmi_podatke->fetch()){
																													$zaposlenik_id = $uzmi_podatke_row['employee_id'];
																													$zaposlenik_ime = $uzmi_podatke_row['employee_firstname'];
																													$zaposlenik_prezime = $uzmi_podatke_row['employee_lastname'];
																													echo '<option value = "'.$zaposlenik_id.'">'.$zaposlenik_ime.' '.$zaposlenik_prezime.'</option>';
																												}
																											?>
																										</select>
																									</div>
																								</div>
																							</div>
																						</div>
																						<div class="modal-footer material-modal__footer">
																							<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
																							<button type="submit" class="btn btn-primary material-btn material-btn_success" form="form_prebaci_drugom"><i class="fa fa-check-square-o" aria-hidden="true"></i> Spremi</button>
																						</div>
																					</form>
																				</div>
																			</div>
																		</div>
																	</div>
															<?php
																}else{
															?>
															<div class="alert alert-danger text-center" role="alert">
																Kontaktirajte Vašeg nadređenog da Vam prebaci kandidata!
															</div>
															<?php 
																}
															?>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
				<?php 
							}else{
								echo 
									'
										<br/>
										<div class="alert material-alert material-alert_danger">
											<h4>
												GREŠKA!
											</h4>
											<p>
												Problem sa linkom. Kontaktirajte administratora za pomoć.
											</p>
											<br />
										</div>
									';	
							}
						break;
						
						case "lista_pozovi_kasnije":
							$type = $_GET["type"]; //type = 1 - pregled svih kandidata type = 2 - pregled kandidata samo određenog zaposlenika
							$status = $_GET["status"]; //$status - 1 obrađeno, 0 - nije obrađeno
							$vrijeme = $_GET["vrijeme"]; //vrijeme - 2 Prosli, 0 - Danas , 1 - Budući
							$team = getLoggedEmployeeTeam();
							$employess_id_team = implode(", ", getIdOfEmployeeTeam($team));
							$employee = "";
							if( isset($_GET["type"]) AND isset($_GET["status"]) AND isset($_GET["vrijeme"]) ){
								$current_date = date("Y-m-d");// current date
								$date_plus30 = date("Y-m-d",strtotime('+30 days',strtotime($current_date))) . " 23:59:59";
								$date_minus30 = date("Y-m-d",strtotime('-30 days',strtotime($current_date))) . " 00:00:00";
								$date_plus1 = date("Y-m-d",strtotime('+1 day',strtotime($current_date))) . " 00:00:00";
								$date_minus1 = date("Y-m-d",strtotime('-1 day',strtotime($current_date))) . " 23:59:59";
								$uslov_obradjeno = "";
								$uslov_vrijeme = "";
								//Uslov za status
								if($status == 1){
									$uslov_obradjeno = " (kan.status_zakaznog_poziva = 1 AND kan.status_zakaznog_poziva is not null) ";
								}else{
									$uslov_obradjeno = " (kan.status_zakaznog_poziva = 0 AND kan.status_zakaznog_poziva is not null) ";
								}
								//Uslov za vrijeme
								if($vrijeme == 1){
									$uslov_vrijeme = " kan.vrijeme_zakaznog_poziva BETWEEN '".$date_plus1."' AND '".$date_plus30."' ";
								}else if($vrijeme == 2){
									$uslov_vrijeme = " kan.vrijeme_zakaznog_poziva BETWEEN '".$current_date." 00:00:00' AND '".$current_date." 23:59:59' ";
								}else{
									$uslov_vrijeme = " kan.vrijeme_zakaznog_poziva BETWEEN '".$date_minus30."' AND '".$date_minus1."' ";
								}
								if($type == 1){
									//pregled svih uslovi
									if($team == 1){
										$uslov_query = " (kan.zaduzen_zaposlenik_nd_kandidata is not null) ";
									}else{
										$uslov_query = " (kan.zaduzen_zaposlenik_nd_kandidata is not null AND kan.zaduzen_zaposlenik_nd_kandidata IN (".$employess_id_team."))";
									}
								}else{
									$employee = $_GET["id_zap"];
									//pregled pregled od zaposlenika
									$uslov_query = " kan.zaduzen_zaposlenik_nd_kandidata = ".$employee." ";
								}
								
								//Prilagodba ispisa 
								if($type == 1){
									if($status == 1){
										$naslov_isp = "prozvani";
									}else{
										$naslov_isp = "neprozvani";
									}
									if($vrijeme == 1){
										$naslov_ispi = "budući";
									}else if($vrijeme == 2){
										$naslov_ispi = "današnji";
									}else{
										$naslov_ispi = "prošli";
									}
									$naslov_ispis = "Zakazani pozivi - Svi ".$naslov_isp." ".$naslov_ispi." kandidati ";
								}else{
									if($status == 1){
										$naslov_isp = "obrađeni";
									}else{
										$naslov_isp = "neobrađeni";
									}
									if($vrijeme == 1){
										$naslov_ispi = "budući";
									}else if($vrijeme == 2){
										$naslov_ispi = "današnji";
									}else{
										$naslov_ispi = "prošli";
									}
									$naslov_ispis = "Zakazani pozivi - Moji ".$naslov_isp." ".$naslov_ispi." kandidati ";
								}
								$glavni_guery = "
									SELECT kan.id_broj_nd_kandidata, kan.ime_nd_kandidata, kan.prezime_nd_kandidata, kan.zaduzen_zaposlenik_nd_kandidata, kan.vrijeme_zakaznog_poziva, kan.status_nd_kandidata, kan.pstatus_nd_kandidata
									FROM idk_nd_kandidata kan 
									WHERE  ".$uslov_query." AND kan.status_nd_kandidata NOT IN (2,3,4,5,6,7)
									AND 
										".$uslov_obradjeno." 
									AND 
										".$uslov_vrijeme." 
								";
				?>
				
								<div class = "row">
									<div class = "col-xs-6 idk_color_green">
										<h1>
											<i class="fa fa-pencil-square-o" aria-hidden="true" style = "margin-right: 10px;">
											</i>
											<?php echo $naslov_ispis; ?>
										</h1>
									</div>
									<div class = "col-xs-6 text-right idk_margin_top10">
									
									</div>
								</div>
								<div class="row" style = "margin-top: 20px;">
									<div class="col-md-12">
										<div class="content_box">
											<div class="row">
												<div class="col-xs-12">
													<hr>
												</div>
											</div>
											<div class="row">
												<div class="col-xs-12">
													<script type="text/javascript">
														jQuery.extend( jQuery.fn.dataTableExt.oSort, {
															"de_datetime-asc": function ( a, b ) {
																var x, y;
																if (jQuery.trim(a) !== '') {
																	var deDatea = jQuery.trim(a).split(' ');
																	var deTimea = deDatea[1].split(':');
																	var deDatea2 = deDatea[0].split('.');
																				if(typeof deTimea[2] != 'undefined') {
																					x = (deDatea2[2] + deDatea2[1] + deDatea2[0] + deTimea[0] + deTimea[1] + deTimea[2]) * 1;
																				} else {
																					x = (deDatea2[2] + deDatea2[1] + deDatea2[0] + deTimea[0] + deTimea[1]) * 1;
																				}
																} else {
																	x = -Infinity; // = l'an 1000 ...
																}
														
																if (jQuery.trim(b) !== '') {
																	var deDateb = jQuery.trim(b).split(' ');
																	var deTimeb = deDateb[1].split(':');
																	deDateb = deDateb[0].split('.');
																				if(typeof deTimeb[2] != 'undefined') {
																					y = (deDateb[2] + deDateb[1] + deDateb[0] + deTimeb[0] + deTimeb[1] + deTimeb[2]) * 1;
																				} else {
																					y = (deDateb[2] + deDateb[1] + deDateb[0] + deTimeb[0] + deTimeb[1]) * 1;
																				}
																} else {
																	y = -Infinity;
																}
																var z = ((x < y) ? -1 : ((x > y) ? 1 : 0));
																return z;
															},
														
															
															"de_date-asc": function ( a, b ) {
																var x, y;
																if (jQuery.trim(a) !== '') {
																	var deDatea = jQuery.trim(a).split('.');
																	x = (deDatea[2] + deDatea[1] + deDatea[0]) * 1;
																} else {
																	x = Infinity; // = l'an 1000 ...
																}
														
																if (jQuery.trim(b) !== '') {
																	var deDateb = jQuery.trim(b).split('.');
																	y = (deDateb[2] + deDateb[1] + deDateb[0]) * 1;
																} else {
																	y = -Infinity;
																}
																var z = ((x < y) ? -1 : ((x > y) ? 1 : 0));
																return z;
															},
														
															"de_date-desc": function ( a, b ) {
																var x, y;
																if (jQuery.trim(a) !== '') {
																	var deDatea = jQuery.trim(a).split('.');
																	x = (deDatea[2] + deDatea[1] + deDatea[0]) * 1;
																} else {
																	x = -Infinity;
																}
														
																if (jQuery.trim(b) !== '') {
																	var deDateb = jQuery.trim(b).split('.');
																	y = (deDateb[2] + deDateb[1] + deDateb[0]) * 1;
																} else {
																	y = Infinity;
																}
																var z = ((x < y) ? 1 : ((x > y) ? -1 : 0));
																return z;
															}
														} );
														$(document).ready(function() {
															$('#kandidati').DataTable({

																responsive: true,

																"order": [[ 3, "asc" ]],

																 "bAutoWidth": false,
																
																"aoColumns": [
																	{ "width": "10%" },
																	{ "width": "30%" },
																	{ "width": "20%" },
																	{ "width": "20%" },
																	{ "width": "20%" }
																],
																columnDefs: [
																{ type: 'de_datetime', targets: [ 3 ] }
																]
															});
														} );
													</script>
													<table id="kandidati" class="display" cellspacing="0" width="100%">
														<thead>
															<tr>
																<th class="text-center">#ID</th>
																<th class="text-center">Ime i Prezime</th>
																<th class="text-center">Status</th>
																<th class="text-center">Menadžer</th>
																<th class="text-center">Vrijeme</th>
															</tr>
														</thead>
														<tbody>
															<?php 
																$query = $db->prepare("".$glavni_guery."");
																$query->execute();
																while($row = $query->fetch()){
																	$id = $row["id_broj_nd_kandidata"];
																	$ime_prezime = $row["ime_nd_kandidata"]." ".$row["prezime_nd_kandidata"];
																	$zaposlenik = getZaposlenikimeR($row["zaduzen_zaposlenik_nd_kandidata"]);
																	$vrijeme = date("d.m.Y H:i:s", strtotime($row["vrijeme_zakaznog_poziva"]));
																	$status_nd_kandidata = $row["status_nd_kandidata"];
																	$pstatus_nd_kandidata = $row["pstatus_nd_kandidata"];
																	$status_f = getStatusDIPLKandidatR($status_nd_kandidata, $pstatus_nd_kandidata);
															?>
															<tr>
																<td class="text-center"><?php echo $id; ?></td>
																<td class="text-center"><a target="_blank" href="<?php getSiteURL(); ?>nostrifikacija_diploma?page=otvori_ND_kandidata&id=<?php echo $id;?>"><?php echo $ime_prezime; ?></a></td>
																<td class="text-center"><?php echo $status_f; ?></td>
																<td class="text-center"><?php echo $zaposlenik; ?></td>
																<td class="text-center" style = "word-break: break-all;"><?php echo $vrijeme; ?></td>
															</tr>
															<?php 
																}
															?>
														</tbody>
													</table>
												</div>
											</div>
											<div class="row">
												<div class="col-xs-12">
													<hr>
												</div>
											</div>
										</div>
									</div>
								</div>
				
				<?php
								
							}
							else{
								echo 
								'
									<br/>
									<div class="alert material-alert material-alert_danger">
										<h4>
											GREŠKA!
										</h4>
										<p>
											Problem sa linkom. Kontaktirajte administratora za pomoć.
										</p>
										<br />
									</div>
								';	
							}
						break;  
						
						case  "inkaso_pocetna":
							if((in_array( "1" , $employee_status)) OR (in_array( "14" , $employee_status))){
								$bezID = array();
								$bezIDispis = "";
								$query0 = $db->prepare("
									SELECT pred.pr_id
									FROM idk_predracuni pred
									JOIN idk_nd_kandidata_biljeske bilj
									ON pred.pr_id = bilj.predracun_id
									WHERE pred.pr_vrsta_predracuna = 1 AND pred.pr_status IN (3,4,5) AND bilj.status_biljeska_nd = 3 AND bilj.tip_biljeska_nd IN (2,3,4,5,6) AND zadnja_inkaso_biljeska = 1
								");
								$query0->execute();
								if($query0->rowCount() != 0){
									while($row0 = $query0->fetch()) {
										//echo $row0["pr_id"] ."<br/>";
										array_push($bezID, $row0["pr_id"]);
									}
									$bezIDispis = implode(",", $bezID);
								}else{
									$bezIDispis = "0";
								}
								$query1 = $db->prepare("
									SELECT
										sum(case when pred.pr_domaca_valuta = 'BAM' AND pred.pr_id is not null then 1 else 0 end) as tip1BAM,
										sum(case when pred.pr_domaca_valuta = 'RSD' AND pred.pr_id is not null then 1 else 0 end) as tip1RSD,
										sum(case when pred.pr_domaca_valuta = 'EUR' AND pred.pr_id is not null then 1 else 0 end) as tip1EUR
									FROM idk_predracuni pred
									WHERE pred.pr_vrsta_predracuna = 1 AND  pred.pr_status IN (3,4,5) AND pr_uplaceno = 0 AND pred.pr_stari_ink_status is null AND pred.pr_id NOT IN (".$bezIDispis.")
								");
								$query1->execute();
								
								$query2 = $db->prepare("
									SELECT
										sum(case when pred.pr_domaca_valuta = 'BAM' AND bilj.tip_biljeska_nd = 2 then 1 else 0 end) as tip2BAM,
										sum(case when pred.pr_domaca_valuta = 'RSD' AND bilj.tip_biljeska_nd = 2 then 1 else 0 end) as tip2RSD,
										sum(case when pred.pr_domaca_valuta = 'EUR' AND bilj.tip_biljeska_nd = 2 then 1 else 0 end) as tip2EUR,
										sum(case when pred.pr_domaca_valuta = 'BAM' AND bilj.tip_biljeska_nd = 3 then 1 else 0 end) as tip3BAM,
										sum(case when pred.pr_domaca_valuta = 'RSD' AND bilj.tip_biljeska_nd = 3 then 1 else 0 end) as tip3RSD,
										sum(case when pred.pr_domaca_valuta = 'EUR' AND bilj.tip_biljeska_nd = 3 then 1 else 0 end) as tip3EUR,
										sum(case when pred.pr_domaca_valuta = 'BAM' AND bilj.tip_biljeska_nd = 5 then 1 else 0 end) as tip4BAM,
										sum(case when pred.pr_domaca_valuta = 'RSD' AND bilj.tip_biljeska_nd = 5 then 1 else 0 end) as tip4RSD,
										sum(case when pred.pr_domaca_valuta = 'EUR' AND bilj.tip_biljeska_nd = 5 then 1 else 0 end) as tip4EUR
									FROM idk_predracuni pred
									JOIN idk_nd_kandidata_biljeske bilj
									ON pred.pr_id = bilj.predracun_id
									WHERE pred.pr_vrsta_predracuna = 1 AND pred.pr_status IN (3,4,5) AND pr_uplaceno = 0 AND  bilj.status_biljeska_nd = 3 AND bilj.zadnja_inkaso_biljeska = 1
								");
								$query2->execute();
								
								$query3 = $db->prepare("
									SELECT
										sum(case when pred.pr_domaca_valuta = 'BAM' AND pr_stari_ink_status is not null then 1 else 0 end) as tip5BAM,
										sum(case when pred.pr_domaca_valuta = 'RSD' AND pr_stari_ink_status is not null then 1 else 0 end) as tip5RSD,
										sum(case when pred.pr_domaca_valuta = 'EUR' AND pr_stari_ink_status is not null then 1 else 0 end) as tip5EUR
									FROM idk_predracuni pred
									WHERE pred.pr_vrsta_predracuna = 1 AND pred.pr_status IN (1,3,4,5) AND pr_uplaceno = 0
								");
								$query3->execute();
								
								$row1 = $query1->fetch();
								$row2 = $query2->fetch();
								$row3 = $query3->fetch();
							
								$tip1BAM = intval($row1["tip1BAM"]);
								$tip1RSD = intval($row1["tip1RSD"]);
								$tip1EUR = intval($row1["tip1EUR"]);
								$tip2BAM = intval($row2["tip2BAM"]);
								$tip2RSD = intval($row2["tip2RSD"]);
								$tip2EUR = intval($row2["tip2EUR"]);
								$tip3BAM = intval($row2["tip3BAM"]);
								$tip3RSD = intval($row2["tip3RSD"]);
								$tip3EUR = intval($row2["tip3EUR"]);
								$tip4BAM = intval($row2["tip4BAM"]);
								$tip4RSD = intval($row2["tip4RSD"]);
								$tip4EUR = intval($row2["tip4EUR"]);
								$tip5BAM = intval($row3["tip5BAM"]);
								$tip5RSD = intval($row3["tip5RSD"]);
								$tip5EUR = intval($row3["tip5EUR"]);
				?>
							<style>
								.dugme{
									align-items: center;
									background: linear-gradient(-45deg, rgba(0,0,0,0.22), rgba(255,255,255,0.25));
									box-shadow: 12px 12px 16px 0 rgba(0, 0, 0, 0.25),
									-8px -8px 12px 0 rgba(255, 255, 255, 0.3);
									border-radius: 50px;
									justify-content: center
								}
								.margin_country{
									margin: 15px 0px;
								}
								.broj_type{
									font-size: large;
									font-weight: bold;
									color: #686868;
								}
							</style>
							<div class = "row">
								<div class = "col-xs-9 idk_color_green">
									<h1>
										<i class="fa fa-credit-card" aria-hidden="true" style = "margin-right: 10px;"></i>
										DIPL Inkaso 
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
												<!-- <hr> -->
											</div>
										</div>
										<div class="row">
											<div class="col-xs-12">
												<div class = "row">
													<div class = "col-md-6 col-md-offset-3">
														<div class="panel panel-danger">
															<div class="panel-heading">
																<h3 class="panel-title text-center"><b>Novi</b></h3>
															</div>
															<div class="panel-body">
																<div class="row">
																	<div class="col-md-4 col-xs-12 text-center margin_country">
																		<a href="<?php getSiteURL(); ?>dipl?page=inkaso_ispis&country=BIH&type=1" target="_BLANK">
																			<button class="btn btn material-btn material dugme">
																				<img src="<?php getSiteUrl(); ?>images/BosniaHerzegowina.png" width="100"><br>
																				<span class="broj_type"><?php echo $tip1BAM; ?></span>
																			</button>
																		</a>
																	</div>
																	<div class="col-md-4 col-xs-12 text-center margin_country">
																		<a href="<?php getSiteURL(); ?>dipl?page=inkaso_ispis&country=SRB&type=1" target="_BLANK">
																			<button class="btn btn material-btn material dugme">
																				<img src="<?php getSiteUrl(); ?>images/Serbian.png" width="100"><br>
																				<span class="broj_type"><?php echo $tip1RSD; ?></span>
																			</button>
																		</a>
																	</div>
																	<div class="col-md-4 col-xs-12 text-center margin_country">
																		<a href="<?php getSiteURL(); ?>dipl?page=inkaso_ispis&country=DE&type=1" target="_BLANK">
																			<button class="btn btn material-btn material dugme">
																				<img src="<?php getSiteUrl(); ?>images/Germany.png" width="100"><br>
																				<span class="broj_type"><?php echo $tip1EUR; ?></span>
																			</button>
																		</a>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
												<div class = "row">
													<div class = "col-md-6 col-xs-12">
														<div class="panel panel-warning">
															<div class="panel-heading">
																<h3 class="panel-title text-center"><b>Ne javlja se</b></h3>
															</div>
															<div class="panel-body">
																<div class="row">
																	<div class="col-md-4 col-xs-12 text-center margin_country">
																		<a href="<?php getSiteURL(); ?>dipl?page=inkaso_ispis&country=BIH&type=2" target="_BLANK">
																			<button class="btn btn material-btn material dugme">
																				<img src="<?php getSiteUrl(); ?>images/BosniaHerzegowina.png" width="100"><br>
																				<span class="broj_type"><?php echo $tip2BAM; ?></span>
																			</button>
																		</a>
																	</div>
																	<div class="col-md-4 col-xs-12 text-center margin_country">
																		<a href="<?php getSiteURL(); ?>dipl?page=inkaso_ispis&country=SRB&type=2" target="_BLANK">
																			<button class="btn btn material-btn material dugme">
																				<img src="<?php getSiteUrl(); ?>images/Serbian.png" width="100"><br>
																				<span class="broj_type"><?php echo $tip2RSD; ?></span>
																			</button>
																		</a>
																	</div>
																	<div class="col-md-4 col-xs-12 text-center margin_country">
																		<a href="<?php getSiteURL(); ?>dipl?page=inkaso_ispis&country=DE&type=2" target="_BLANK">
																			<button class="btn btn material-btn material dugme">
																				<img src="<?php getSiteUrl(); ?>images/Germany.png" width="100"><br>
																				<span class="broj_type"><?php echo $tip2EUR; ?></span>
																			</button>
																		</a>
																	</div>
																</div>
															</div>
														</div>
													</div>
													<div class = "col-md-6 col-xs-12">
														<div class="panel panel-primary">
															<div class="panel-heading">
																<h3 class="panel-title text-center"><b>Pozvati kasnije</b></h3>
															</div>
															<div class="panel-body">
																<div class="row">
																	<div class="col-md-4 col-xs-12 text-center margin_country">
																		<a href="<?php getSiteURL(); ?>dipl?page=inkaso_ispis&country=BIH&type=3" target="_BLANK">
																			<button class="btn btn material-btn material dugme">
																				<img src="<?php getSiteUrl(); ?>images/BosniaHerzegowina.png" width="100"><br>
																				<span class="broj_type"><?php echo $tip3BAM; ?></span>
																			</button>
																		</a>
																	</div>
																	<div class="col-md-4 col-xs-12 text-center margin_country">
																		<a href="<?php getSiteURL(); ?>dipl?page=inkaso_ispis&country=SRB&type=3" target="_BLANK">
																			<button class="btn btn material-btn material dugme">
																				<img src="<?php getSiteUrl(); ?>images/Serbian.png" width="100"><br>
																				<span class="broj_type"><?php echo $tip3RSD; ?></span>
																			</button>
																		</a>
																	</div>
																	<div class="col-md-4 col-xs-12 text-center margin_country">
																		<a href="<?php getSiteURL(); ?>dipl?page=inkaso_ispis&country=DE&type=3" target="_BLANK">
																			<button class="btn btn material-btn material dugme">
																				<img src="<?php getSiteUrl(); ?>images/Germany.png" width="100"><br>
																				<span class="broj_type"><?php echo $tip3EUR; ?></span>
																			</button>
																		</a>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
												<div class = "row">
													<div class = "col-md-6 col-xs-12">
														<div class="panel panel-success">
															<div class="panel-heading">
																<h3 class="panel-title text-center"><b>Uplata na dan</b></h3>
															</div>
															<div class="panel-body">
																<div class="row">
																	<div class="col-md-4 col-xs-12 text-center margin_country">
																		<a href="<?php getSiteURL(); ?>dipl?page=inkaso_ispis&country=BIH&type=4" target="_BLANK">
																			<button class="btn btn material-btn material dugme">
																				<img src="<?php getSiteUrl(); ?>images/BosniaHerzegowina.png" width="100"><br>
																				<span class="broj_type"><?php echo $tip4BAM; ?></span>
																			</button>
																		</a>
																	</div>
																	<div class="col-md-4 col-xs-12 text-center margin_country">
																		<a href="<?php getSiteURL(); ?>dipl?page=inkaso_ispis&country=SRB&type=4" target="_BLANK">
																			<button class="btn btn material-btn material dugme">
																				<img src="<?php getSiteUrl(); ?>images/Serbian.png" width="100"><br>
																				<span class="broj_type"><?php echo $tip4RSD; ?></span>
																			</button>
																		</a>
																	</div>
																	<div class="col-md-4 col-xs-12 text-center margin_country">
																		<a href="<?php getSiteURL(); ?>dipl?page=inkaso_ispis&country=DE&type=4" target="_BLANK">
																			<button class="btn btn material-btn material dugme">
																				<img src="<?php getSiteUrl(); ?>images/Germany.png" width="100"><br>
																				<span class="broj_type"><?php echo $tip4EUR; ?></span>
																			</button>
																		</a>
																	</div>
																</div>
															</div>
														</div>
													</div>
													<div class = "col-md-6 col-xs-12">
														<div class="panel panel-info">
															<div class="panel-heading">
																<h3 class="panel-title text-center"><b>Promjena ugovora</b></h3>
															</div>
															<div class="panel-body">
																<div class="row">
																	<div class="col-md-4 col-xs-12 text-center margin_country">
																		<a href="<?php getSiteURL(); ?>dipl?page=inkaso_ispis&country=BIH&type=5" target="_BLANK">
																			<button class="btn btn material-btn material dugme">
																				<img src="<?php getSiteUrl(); ?>images/BosniaHerzegowina.png" width="100"><br>
																				<span class="broj_type"><?php echo $tip5BAM; ?></span>
																			</button>
																		</a>
																	</div>
																	<div class="col-md-4 col-xs-12 text-center margin_country">
																		<a href="<?php getSiteURL(); ?>dipl?page=inkaso_ispis&country=SRB&type=5" target="_BLANK">
																			<button class="btn btn material-btn material dugme">
																				<img src="<?php getSiteUrl(); ?>images/Serbian.png" width="100"><br>
																				<span class="broj_type"><?php echo $tip5RSD; ?></span>
																			</button>
																		</a>
																	</div>
																	<div class="col-md-4 col-xs-12 text-center margin_country">
																		<a href="<?php getSiteURL(); ?>dipl?page=inkaso_ispis&country=DE&type=5" target="_BLANK">
																			<button class="btn btn material-btn material dugme">
																				<img src="<?php getSiteUrl(); ?>images/Germany.png" width="100"><br>
																				<span class="broj_type"><?php echo $tip5EUR; ?></span>
																			</button>
																		</a>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-xs-12">
												<!-- <hr> -->
											</div>
										</div>
									</div>
								</div>
							</div>
				<?php
								
							}else{
								echo '
									<div class="alert material-alert material-alert_danger">
										<h4>NEMATE PRIVILEGIJE!</h4>
										<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
										<br />
										<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
									</div>
								';
							}
						break;
						
						case "inkaso_ispis":
							if((in_array( "1" , $employee_status)) OR (in_array( "14" , $employee_status))){
								
								$country = $_GET["country"];  
								$country_ispis = "";
								$country_uslov = "";
								if($country == "BIH"){
									$country_ispis = '<img src="'.getSiteUrlr().'images/BosniaHerzegowina.png" width="30">';
									$country_uslov = "BAM";
								}else if($country == "SRB"){
									$country_ispis = '<img src="'.getSiteUrlr().'images/Serbian.png" width="30">';
									$country_uslov = "RSD";
								}else if($country == "DE"){
									$country_ispis = '<img src="'.getSiteUrlr().'images/Germany.png" width="30">';
									$country_uslov = "EUR";
								}
								if(isset($_GET["period"])){
									if($_GET["period"] == 0){
										$naslov_period = 'za prošle';
									}else if($_GET["period"] == 1){
										$naslov_period = 'za danas';
									}else{
										$naslov_period = 'za buduće';
									}
								}else{
									$naslov_period = "";
								}
								$type = $_GET["type"];
								$naslov_ispis = "";
								if($type == 1){
									$naslov_ispis = 'Inkaso predračuni - Novi '.$country_ispis;
								}else if($type == 2){
									$naslov_ispis = 'Inkaso predračuni - Ne javlja se '.$country_ispis;
								}else if($type == 3){
									$naslov_ispis = 'Inkaso predračuni - Pozvati kasnije '.$naslov_period.' '.$country_ispis;
								}else if($type == 4){
									$naslov_ispis = 'Inkaso predračuni - Uplata na dan '.$naslov_period.' '.$country_ispis;
								}else if($type == 5){
									$naslov_ispis = 'Inkaso predračuni - Promjena ugovora '.$country_ispis;
								}
				?>
							<style>
								.broj_type{
									font-size: large;
									font-weight: bold;
									color: #686868;
								}
							</style>
							<div class = "row">
								<div class = "col-xs-9 idk_color_green">
									<h1>
										<i class="fa fa-credit-card" aria-hidden="true" style = "margin-right: 10px;"></i>
										DIPL - <?php echo $naslov_ispis;?>
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
												<?php
													if($type == 3 OR $type == 4){
												?>
												<div class="row">
													<div class="col-xs-6">
													</div>
													<div class="col-xs-6">
														<div class="row">
															<form action="<?php getSiteUrl(); ?>dipl" enctype="multipart/form-data" method="get" accept-charset="utf-8" role="form" class="form-horizontal">
																<input type="hidden" name="page" value="inkaso_ispis">
																<input type="hidden" name="country" value="<?php echo $country; ?>">
																<input type="hidden" name="type" value="<?php echo $type; ?>">
																<?php 
																	if(isset($_GET["period"])){
																?>
																<input type="hidden" name="period" value="<?php echo $_GET["period"]; ?>">
																<?php 
																	}
																?>
																<div class="col-sm-8">
																	<label for="zapID"></label>
																	<?php 
																		$queryZap = $db->prepare("
																			SELECT employee_firstname, employee_lastname, employee_id FROM idk_employees WHERE employee_status LIKE '%14%'
																		");
																		$queryZap->execute();
																	?>
																	<select id="zapID" class="selectpicker" name="zapID" title = "Odaberi zaposlenika" value="0">
																		<option value = "0" selected disabled>Odaberi zaposlenika</option>
																		<?php 
																			while($rowZap = $queryZap->fetch()){
																				$idZap = $rowZap["employee_id"];
																				$flnameZap = $rowZap["employee_firstname"]." ".$rowZap["employee_lastname"];
																				if(isset($_GET["zapID"])){
																					if($_GET["zapID"] == $idZap){
																						echo '<option value = "'.$idZap.'" selected>'.$flnameZap.'</option>';
																					}else{
																						echo '<option value = "'.$idZap.'">'.$flnameZap.'</option>';
																					}
																				}else{
																					echo '<option value = "'.$idZap.'">'.$flnameZap.'</option>';
																				}
																			}
																		?>
																	</select>
																</div>
																
																<div class="col-sm-4 idk_margin_top20">
																	<button style="width:100%" class="btn btn-success">Traži</button>
																</div>
															</form>
														</div>
													</div>
												</div>
												<?php
													}
													if($type == 3 OR $type == 4){
														if($type == 3){
															$uslov1_query2 = "bilj.vrijeme_ponovnog_zvanja";
															$uslov2_query2 = "3";
														}else{
															$uslov1_query2 = "bilj.uplata_na_datum";
															$uslov2_query2 = "5";
														}
														$query2 = $db->prepare("
															SELECT 
															sum(
																case
																	when DATE(".$uslov1_query2.") = CURRENT_DATE() then 1 else 0
																end
																)
															AS brojtype3DANAS,
															sum(
																case
																	when DATE(".$uslov1_query2.") > CURRENT_DATE() then 1 else 0
																end
																)
															AS brojtype3SUTRA,
															sum(
																case
																	when DATE(".$uslov1_query2.") < CURRENT_DATE() then 1 else 0
																end
																)
															AS brojtype3JUCER
															FROM idk_predracuni pred
															JOIN idk_nd_kandidata_biljeske bilj
															ON pred.pr_id = bilj.predracun_id
															WHERE pred.pr_vrsta_predracuna = 1 AND pred.pr_domaca_valuta = '".$country_uslov."' AND pred.pr_status IN (3,4,5) 
															AND pr_uplaceno = 0 AND  bilj.status_biljeska_nd = 3 AND bilj.tip_biljeska_nd = ".$uslov2_query2." AND bilj.zadnja_inkaso_biljeska = 1
														");
														$query2->execute();
														$row2 = $query2->fetch();
														$brojtype3DANAS = intval($row2["brojtype3DANAS"]);
														$brojtype3SUTRA = intval($row2["brojtype3SUTRA"]);
														$brojtype3JUCER = intval($row2["brojtype3JUCER"]);
												?>
												<div class="row idk_margin_top20">
													<div class="col-md-4 col-xs-12">
														<a href="<?php getSiteURL(); ?>dipl?page=inkaso_ispis&country=<?php echo $country;?>&type=<?php echo $type;?>&period=0">
														<div class="panel panel-danger">
															<div class="panel-heading">
																<h3 class="panel-title text-center"><b>Prethodni</b></h3>
															</div>
															<div class="panel-body">
																<div class="row">
																	<div class="col-md-12 col-xs-12 text-center">
																		<span class="broj_type"><?php echo $brojtype3JUCER; ?></span>
																	</div>
																</div>
															</div>
														</div>
														</a>
													</div>
													<div class="col-md-4 col-xs-12">
														<a href="<?php getSiteURL(); ?>dipl?page=inkaso_ispis&country=<?php echo $country;?>&type=<?php echo $type;?>&period=1">
														<div class="panel panel-warning">
															<div class="panel-heading">
																<h3 class="panel-title text-center"><b>Danas</b></h3>
															</div>
															<div class="panel-body">
																<div class="row">
																	<div class="col-md-12 col-xs-12 text-center">
																		<span class="broj_type"><?php echo $brojtype3DANAS; ?></span>
																	</div>
																</div>
															</div>
														</div>
														</a>
													</div>
													<div class="col-md-4 col-xs-12">
														<a href="<?php getSiteURL(); ?>dipl?page=inkaso_ispis&country=<?php echo $country;?>&type=<?php echo $type;?>&period=2">
														<div class="panel panel-success">
															<div class="panel-heading">
																<h3 class="panel-title text-center"><b>Budući</b></h3>
															</div>
															<div class="panel-body">
																<div class="row">
																	<div class="col-md-12 col-xs-12 text-center">
																		<span class="broj_type"><?php echo $brojtype3SUTRA; ?></span>
																	</div>
																</div>
															</div>
														</div>
														</a>
													</div>
												</div>
												<?php
													}
												?>
											</div>
										</div>
										<div class="row">
											<div class="col-xs-12">
												<?php
													$br = 0;
													$naslov_kolona_8 = "";
													if($type == 1){
														$naslov_kolona_8 = "Bilješka";
														$bezID = array();
														$bezIDispis = "";
														$query0 = $db->prepare("
															SELECT pred.pr_id
															FROM idk_predracuni pred
															JOIN idk_nd_kandidata_biljeske bilj
															ON pred.pr_id = bilj.predracun_id
															WHERE pred.pr_vrsta_predracuna = 1 AND pred.pr_status IN (3,4,5) AND pred.pr_domaca_valuta = '".$country_uslov."' AND bilj.status_biljeska_nd = 3 AND bilj.tip_biljeska_nd IN (2,3,4,5,6) AND zadnja_inkaso_biljeska = 1
														");
														$query0->execute();
														if($query0->rowCount() != 0){
															while($row0 = $query0->fetch()) {
																//echo $row0["pr_id"] ."<br/>";
																array_push($bezID, $row0["pr_id"]);
															}
															$bezIDispis = implode(",", $bezID);
														}else{
															$bezIDispis = "0";
														}
														$query1 = $db->prepare("
															SELECT pred.pr_broj_predracuna AS br_predracuna, pred.pr_kandidat_id AS id_kan, pred.pr_datum_kreiranja AS datum_kr, pred.pr_rata AS rata, pred.pr_vrijednost_".$country_uslov." AS vrijednost, pred.pr_status AS status, kan.ime_nd_kandidata AS ime, kan.prezime_nd_kandidata AS prezime, pred.pr_datum_kreiranja AS kolona8
															FROM idk_predracuni pred
															JOIN idk_nd_kandidata kan
															ON pred.pr_kandidat_id = kan.id_broj_nd_kandidata
															WHERE pred.pr_vrsta_predracuna = 1 AND pred.pr_domaca_valuta = '".$country_uslov."' AND pred.pr_status IN (3,4,5) AND pr_uplaceno = 0 AND pr_stari_ink_status is null AND pred.pr_id NOT IN(".$bezIDispis.")
														");
														$query1->execute();
														
													}else if($type == 2){
														$naslov_kolona_8 = "Nije se javio";
														$query1 = $db->prepare("
															SELECT pred.pr_broj_predracuna AS br_predracuna, pred.pr_kandidat_id AS id_kan, pred.pr_datum_kreiranja AS datum_kr, pred.pr_rata AS rata, pred.pr_vrijednost_".$country_uslov." AS vrijednost, pred.pr_status AS status, kan.ime_nd_kandidata AS ime, kan.prezime_nd_kandidata AS prezime, bilj.vrijeme_dodavanja_biljeska_nd AS kolona8
															FROM idk_predracuni pred
															JOIN idk_nd_kandidata kan
															ON pred.pr_kandidat_id = kan.id_broj_nd_kandidata
															JOIN idk_nd_kandidata_biljeske bilj
															ON pred.pr_id = bilj.predracun_id
															WHERE pred.pr_vrsta_predracuna = 1 AND pred.pr_domaca_valuta = '".$country_uslov."' AND pred.pr_status IN (3,4,5) AND pr_uplaceno = 0 AND  bilj.status_biljeska_nd = 3 AND bilj.tip_biljeska_nd = 2 AND bilj.zadnja_inkaso_biljeska = 1
														");
														$query1->execute();
													}else if($type == 3){
														$naslov_kolona_8 = "Pozvati";
														$uslov_period = "";
														if(isset($_GET["period"])){
															$period = $_GET["period"];
															if($period == 0){
																$uslov_period = "AND DATE(".$uslov1_query2.") < CURRENT_DATE()";
															}else if($period == 1){
																$uslov_period = "AND DATE(".$uslov1_query2.") = CURRENT_DATE()";
															}else if($period == 2){
																$uslov_period = "AND DATE(".$uslov1_query2.") > CURRENT_DATE()";
															}
														}else{
															$uslov_period = "";
														}
														$uslov_zap = "";
														if(isset($_GET["zapID"])){
															$zaposs = $_GET["zapID"];
															if($zaposs == 0){
																$uslov_zap = "";
															}else{
																$uslov_zap = "AND bilj.dodao_zaposlenik_biljeska_nd = ".$zaposs."";
															}
														}else{
															$uslov_zap = "";
														}
														$query1 = $db->prepare("
															SELECT pred.pr_broj_predracuna AS br_predracuna, pred.pr_kandidat_id AS id_kan, pred.pr_datum_kreiranja AS datum_kr, pred.pr_rata AS rata, pred.pr_vrijednost_".$country_uslov." AS vrijednost, pred.pr_status AS status, kan.ime_nd_kandidata AS ime, kan.prezime_nd_kandidata AS prezime, bilj.vrijeme_ponovnog_zvanja AS kolona8
															FROM idk_predracuni pred
															JOIN idk_nd_kandidata kan
															ON pred.pr_kandidat_id = kan.id_broj_nd_kandidata
															JOIN idk_nd_kandidata_biljeske bilj
															ON pred.pr_id = bilj.predracun_id
															WHERE pred.pr_vrsta_predracuna = 1 AND pred.pr_domaca_valuta = '".$country_uslov."' AND pred.pr_status IN (3,4,5) AND pr_uplaceno = 0 AND  bilj.status_biljeska_nd = 3 AND bilj.tip_biljeska_nd = 3 AND bilj.zadnja_inkaso_biljeska = 1 ".$uslov_period." ".$uslov_zap."
														");
														$query1->execute();
													}else if($type == 4){
														$naslov_kolona_8 = "Uplata na dan";
														$uslov_period = "";
														if(isset($_GET["period"])){
															$period = $_GET["period"];
															if($period == 0){
																$uslov_period = "AND DATE(".$uslov1_query2.") < CURRENT_DATE()";
															}else if($period == 1){
																$uslov_period = "AND DATE(".$uslov1_query2.") = CURRENT_DATE()";
															}else if($period == 2){
																$uslov_period = "AND DATE(".$uslov1_query2.") > CURRENT_DATE()";
															}
														}else{
															$uslov_period = "";
														}
														$uslov_zap = "";
														if(isset($_GET["zapID"])){
															$zaposs = $_GET["zapID"];
															if($zaposs == 0){
																$uslov_zap = "";
															}else{
																$uslov_zap = "AND bilj.dodao_zaposlenik_biljeska_nd = ".$zaposs."";
															}
														}else{
															$uslov_zap = "";
														}
														$query1 = $db->prepare("
															SELECT pred.pr_broj_predracuna AS br_predracuna, pred.pr_kandidat_id AS id_kan, pred.pr_datum_kreiranja AS datum_kr, pred.pr_rata AS rata, pred.pr_vrijednost_".$country_uslov." AS vrijednost, pred.pr_status AS status, kan.ime_nd_kandidata AS ime, kan.prezime_nd_kandidata AS prezime, bilj.uplata_na_datum AS kolona8
															FROM idk_predracuni pred
															JOIN idk_nd_kandidata kan
															ON pred.pr_kandidat_id = kan.id_broj_nd_kandidata
															JOIN idk_nd_kandidata_biljeske bilj
															ON pred.pr_id = bilj.predracun_id
															WHERE pred.pr_vrsta_predracuna = 1 AND pred.pr_domaca_valuta = '".$country_uslov."' AND pred.pr_status IN (3,4,5) AND pr_uplaceno = 0 AND  bilj.status_biljeska_nd = 3 AND bilj.tip_biljeska_nd = 5 AND bilj.zadnja_inkaso_biljeska = 1 ".$uslov_period." ".$uslov_zap."
														");
														$query1->execute();
													}else if($type == 5){
														$naslov_kolona_8 = "Datum promjene";
														$query1 = $db->prepare("
															SELECT pred.pr_broj_predracuna AS br_predracuna, pred.pr_kandidat_id AS id_kan, pred.pr_datum_kreiranja AS datum_kr, pred.pr_rata AS rata, pred.pr_vrijednost_".$country_uslov." AS vrijednost, pred.pr_status AS status, kan.ime_nd_kandidata AS ime, kan.prezime_nd_kandidata AS prezime, pred.pr_datum_kreiranja AS kolona8
															FROM idk_predracuni pred
															JOIN idk_nd_kandidata kan
															ON pred.pr_kandidat_id = kan.id_broj_nd_kandidata
															WHERE pred.pr_vrsta_predracuna = 1 AND pred.pr_domaca_valuta = '".$country_uslov."' AND pred.pr_status IN (1,3,4,5) AND pr_uplaceno = 0 AND pr_stari_ink_status is not null
														");
														$query1->execute();
													}
												?>
												<script type="text/javascript">
													/*jQuery.extend( jQuery.fn.dataTableExt.oSort, {
														"de_datetime-asc": function ( a, b ) {
															var x, y;
															if (jQuery.trim(a) !== '') {
																var deDatea = jQuery.trim(a).split(' ');
																var deTimea = deDatea[1].split(':');
																var deDatea2 = deDatea[0].split('.');
																			if(typeof deTimea[2] != 'undefined') {
																				x = (deDatea2[2] + deDatea2[1] + deDatea2[0] + deTimea[0] + deTimea[1] + deTimea[2]) * 1;
																			} else {
																				x = (deDatea2[2] + deDatea2[1] + deDatea2[0] + deTimea[0] + deTimea[1]) * 1;
																			}
															} else {
																x = -Infinity; // = l'an 1000 ...
															}
													
															if (jQuery.trim(b) !== '') {
																var deDateb = jQuery.trim(b).split(' ');
																var deTimeb = deDateb[1].split(':');
																deDateb = deDateb[0].split('.');
																			if(typeof deTimeb[2] != 'undefined') {
																				y = (deDateb[2] + deDateb[1] + deDateb[0] + deTimeb[0] + deTimeb[1] + deTimeb[2]) * 1;
																			} else {
																				y = (deDateb[2] + deDateb[1] + deDateb[0] + deTimeb[0] + deTimeb[1]) * 1;
																			}
															} else {
																y = -Infinity;
															}
															var z = ((x < y) ? -1 : ((x > y) ? 1 : 0));
															return z;
														},
													
														
														"de_date-asc": function ( a, b ) {
															var x, y;
															if (jQuery.trim(a) !== '') {
																var deDatea = jQuery.trim(a).split('.');
																x = (deDatea[2] + deDatea[1] + deDatea[0]) * 1;
															} else {
																x = Infinity; // = l'an 1000 ...
															}
													
															if (jQuery.trim(b) !== '') {
																var deDateb = jQuery.trim(b).split('.');
																y = (deDateb[2] + deDateb[1] + deDateb[0]) * 1;
															} else {
																y = -Infinity;
															}
															var z = ((x < y) ? -1 : ((x > y) ? 1 : 0));
															return z;
														},
													
														"de_date-desc": function ( a, b ) {
															var x, y;
															if (jQuery.trim(a) !== '') {
																var deDatea = jQuery.trim(a).split('.');
																x = (deDatea[2] + deDatea[1] + deDatea[0]) * 1;
															} else {
																x = -Infinity;
															}
													
															if (jQuery.trim(b) !== '') {
																var deDateb = jQuery.trim(b).split('.');
																y = (deDateb[2] + deDateb[1] + deDateb[0]) * 1;
															} else {
																y = Infinity;
															}
															var z = ((x < y) ? 1 : ((x > y) ? -1 : 0));
															return z;
														}
													} );*/
													$(document).ready(function() {
														$('#idk_table').DataTable({
															responsive: true,
															"order": [[ 7, "desc" ]],
															"bAutoWidth": false,
															"aoColumns": [
																{ "width": "5%", "bSortable": false },
																{ "width": "20%" },
																{ "width": "10%" },
																{ "width": "12.5%" },
																{ "width": "10%" },
																{ "width": "10%" },
																{ "width": "10%" },
																{ "width": "12.5%" }
															],
															/*columnDefs: [
																{ type: 'de_date', targets: [ 3, 7 ] }
															]*/
														}); 
													});
												</script>
												<table id="idk_table" class="display" cellspacing="0" width="100%">
													<thead>
														<tr>
															<th class="text-center">#</th>
															<th class="text-center">Ime Prezime</th>
															<th class="text-center">Broj predračuna</th>
															<th class="text-center">Datum kreiranja</th>
															<th class="text-center">Rata</th>
															<th class="text-center">Vrijednost <?php echo $country_uslov; ?></th>
															<th class="text-center">Status</th>
															<th class="text-center"><?php echo $naslov_kolona_8; ?></th>
														</tr>
													</thead>
													<tbody>
														<?php 
															while($row = $query1->fetch()){
																$br++;
																$id_kan = $row["id_kan"];
																$kan_podaci = $row["ime"]." ".$row["prezime"];
																$br_predracuna = $row["br_predracuna"];
																$datum_kr = date("d.m.Y H:i", strtotime($row["datum_kr"]));
																$datum_sort_kr = strtotime($row["datum_kr"]);
																$rata = $row["rata"];
																$vrijednost = $row["vrijednost"]." ".$country_uslov;
																//status predracuna ispis start
																$status = '';
																if(intval($row["status"]) == 0){
																	$status = '<span class="label label-warning material-label material-label_warning main-container__column">Arhiviran</span>';
																}else if(intval($row["status"]) == 1){
																	$status = '<span class="label label-info material-label material-label_info main-container__column">Poslan</span>';
																}else if(intval($row["status"]) == 2){
																	$status = '<span class="label label-success material-label material-label_success main-container__column">Uplaćen</span>';
																}else if(intval($row["status"]) == 3){
																	$status = '<span class="label label-danger material-label material-label_danger main-container__column">Inkaso 1</span>';
																}else if(intval($row["status"]) == 4){
																	$status = '<span class="label label-danger material-label material-label_danger main-container__column">Inkaso 2</span>';
																}else if(intval($row["status"]) == 5){
																	$status = '<span class="label label-danger material-label material-label_danger main-container__column">Inkaso 3</span>';
																}
																//status predracuna ispis end
																//kolona 8 prema tipu start
																$kolona8 = date("d.m.Y H:i", strtotime($row["kolona8"]));
																$datum_sort_pozovi = strtotime($row["kolona8"]);
																//kolona 8 prema tipu end
																$row_style = "";
																if($type == 3 OR $type == 4){
																	$diff = strtotime(date("Y-m-d")) - strtotime(date("Y-m-d", strtotime($row["kolona8"])));
																	$day = floor($diff/86400);
																	if($day == 0){
																		$row_style = "background-color: #faebcc;";
																	}else if($day > 0){
																		$row_style = "background-color: #ebccd1;";
																	}else if($day < 0){
																		$row_style = "background-color: #d6e9c6;";
																	}
																}else{
																	$row_style = "";
																}
														?>
														<tr style = "<?php echo $row_style; ?>">
															<td class="text-center"><?php echo $br; ?></td>
															<td class="text-center"><a href="nostrifikacija_diploma?page=otvori_ND_kandidata&id=<?php echo $id_kan;?>"><?php echo $kan_podaci;?></a></td>
															<td class="text-center"><?php echo $br_predracuna; ?></td>
															<td class="text-center" data-order="<?php echo $datum_sort_kr ; ?>" ><?php echo $datum_kr; ?></td>
															<td class="text-center"><?php echo $rata; ?></td>
															<td class="text-center"><?php echo $vrijednost; ?></td>
															<td class="text-center"><?php echo $status;?></td>
															<td class="text-center" data-order="<?php echo $datum_sort_pozovi ; ?>" ><?php echo $kolona8;?></td>
														</tr>
														<?php 
															}
														?>
													</tbody>
												</table>
											</div>
										</div>
										<div class="row">
											<div class="col-xs-12">
												<!-- <hr> -->
											</div>
										</div>
									</div>
								</div>
							</div>
				<?php
								
							}else{
								echo '
									<div class="alert material-alert material-alert_danger">
										<h4>NEMATE PRIVILEGIJE!</h4>
										<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
										<br />
										<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
									</div>
								';
							}
						break;
						
						case "preporuka":
							if((in_array( "1" , $employee_status)) OR $logged_employee_id == 48 OR (in_array( "7" , $employee_status))){
								$t = intval($_GET["t"]);
				?>
								<style>
									.form_edit_izgled{
										border: 1px solid #cccccc;
										padding: 20px 50px 20px 50px;
										border-radius: 1.25rem;
									}
								</style>
								<div class="row">
									<div class="col-xs-8">
										<h1><i class="fa fa-plus idk_color_green" aria-hidden="true" style = "margin-right: 15px;"></i> Novi kandidat 
										<?php 
											if($t == 1){
										?>
											<i class="fa fa-facebook-square" aria-hidden="true"></i> <i class="fa fa-instagram" aria-hidden="true"></i>
										<?php 
											}else{
										?>
											- Inbound Lead
										<?php
											}
										?>	
										</h1>
									</div>
									<div class="col-xs-4 text-right idk_margin_top10">
										<a href="<?php echo $_SERVER['HTTP_REFERER']; ?>" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
									</div>
									<div class="col-xs-12">
										<hr />
									</div>
								</div>
								<div class="row" style = "margin-top: 20px;">
									<div class="col-md-12">
										<div class="content_box">
											<div class="row">
												<div class="col-md-8 col-md-offset-2 form_edit_izgled">
													<form action="<?php getSiteURL(); ?>do_dipl?form=add_preporuka" method="post" http-equiv="Content-type" enctype="multipart/form-data"; charset="UTF-8" class="form-horizontal" id="form_add_preporuka">
														<input type="hidden" name="t" id="t" value="<?php echo $t; ?>">
														<div class="form-group">
															<label for="ime_new" class="col-sm-5 control-label">
																<span class="text-danger">
																	*
																</span>
																Ime:
															</label>
															<div class="col-sm-7">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input" type="text" name="ime_new" id="ime_new" placeholder="Unesite ime" autocomplete="off" required>
																	<span class="materail-input-block__line">
																	</span>
																</div>
															</div>
														</div>
														<div class="form-group">
															<label for="prezime_new" class="col-sm-5 control-label">
																<span class="text-danger">
																	*
																</span>
																Prezime:
															</label>
															<div class="col-sm-7">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input" type="text" name="prezime_new" id="prezime_new" autocomplete="off" placeholder="Unesite prezime" required>
																	<span class="materail-input-block__line">
																	</span>
																</div>
															</div>
														</div>
														<div class="form-group">
															<label for="mobilni_new" class="col-sm-5 control-label">
																<span class="text-danger">
																	*
																</span>
																Mobilni telefon:
															</label>
															<div class="col-sm-7">
																<div class="">
																	<input class="form-control materail-input" type="tel" name="mobilni_new" id="mobilni_new" required>
																</div>
															</div>
														</div>
														<script>
															$( document ).ready(function($) {
																
																var telInput = document.querySelector("#mobilni_new");
																
																iti = window.intlTelInput(telInput, {
																	utilsScript: "<?php getSiteUrl(); ?>buildTelInput/js/utils.js",
																	initialCountry: "ba",
																	autoPlaceholder: "aggressive",
																	preferredCountries: ["ba","rs","hr","de"],
																	formatOnDisplay: true,
																	separateDialCode: true
																});
																var fullNumber = iti.getNumber();
																$("form").submit(function(event) {
																	
																	$("#mobilni_new").val(iti.getNumber()); 
																});
																
															});
														</script>
														<?php 
															if($t == 1){
														?>
														<div class="form-group">
															<label for="kd_" class="col-sm-5 control-label">
																Slike razgovora:
																<span class="text-danger">
																	*
																</span>
															</label>
															<div class="col-sm-7">
																<div class="fileinput fileinput-new" data-provides="fileinput">
																	<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;">
																	</div>
																	<div>
																		<span class="btn btn-default btn-file">
																			<span class="fileinput-new">Izaberi fotografije</span>
																			<span class="fileinput-exists">Promijeni</span>
																			<input type="file" name="chat_image[]" multiple="" required>
																		</span>
																		<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Ukloni</a>
																		<script>
																			$(function (){
																				$('#idk_urlimg_prijave').change(function (){

																					var ext = $('#idk_urlimg_prijave').val().split('.').pop().toLowerCase();

																					if($.inArray(ext, ['jpg', 'jpeg', 'png', '']) == -1) {
																						$('#idk_alert_ext').removeClass('hidden');
																						this.value = null;
																					}else{
																						$('#idk_alert_ext').addClass('hidden');
																					}

																					var f = this.files[0];

																					if (f.size > 20388600 || f.fileSize > 20388600){
																						$('#idk_alert_size').removeClass('hidden');
																						this.value = null;
																					}else{
																						$('#idk_alert_size').addClass('hidden');
																					}
																				})
																			});
																		</script>
																	</div>
																</div>
															</div>
														</div>
														<?php 
															}
														?>
														<div class="modal-footer material-modal__footer" style = "text-align: center;">
															<button class="btn material-btn material-btn" data-dismiss="modal">
																Odustani
															</button>
															<button type="submit" class="btn btn-primary material-btn material-btn_success" form="form_add_preporuka">
																<i class="fa fa-check-square-o" aria-hidden="true" style = "margin-right: 10px;">
																</i>
																Završi
															</button>
														</div>
														<div class="row" style = "margin-top: 15px;">
															<div class="col-md-8 col-md-offset-2 text-center">
																<small>
																	Sva polja označena sa 
																	<span class="text-danger">
																		*
																	</span>  
																	su obavezna!
																</small>
															</div>
														</div>
													</form>
												</div>
											</div>
										</div>
									</div>
								</div>
				<?php
								
							}else{
								echo '
									<div class="alert material-alert material-alert_danger">
										<h4>NEMATE PRIVILEGIJE!</h4>
										<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
										<br />
										<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
									</div>
								';
							}
						break;
						
						case "preporuka_rezultat":
							$id = $_GET["id"];
							$type = $_GET["type"];
							if(isset($id)){
				?>
								<div class="row">
									<div class="col-xs-8">
										<h1><i class="fa fa-plus idk_color_green" aria-hidden="true" style = "margin-right: 15px;"></i> Prijava kandidata <i class="fa fa-facebook-square" aria-hidden="true"></i> <i class="fa fa-instagram" aria-hidden="true"></i></h1>
									</div>
									<div class="col-xs-4 text-right idk_margin_top10">
										<a href="<?php echo $_SERVER['HTTP_REFERER']; ?>" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
									</div>
									<div class="col-xs-12">
										<hr />
									</div>
								</div>
								<div class="row" style = "margin-top: 20px;">
									<div class="col-md-12">
										<div class="content_box">
											<div class="row">
												<div class="col-md-6 col-md-offset-3">
												<?php 
													$query = $db->prepare("
														SELECT ime_nd_kandidata, prezime_nd_kandidata, mobilni_nd_kandidata, vrijeme_kreiranja_nd_kandidata, zaduzen_zaposlenik_nd_kandidata
														FROM idk_nd_kandidata
														WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata
													");
													$query->execute(array(':id_broj_nd_kandidata' => $id));
													$row = $query->fetch();
													$ime = $row["ime_nd_kandidata"];
													$prezime = $row["prezime_nd_kandidata"];
													$mobilni = $row["mobilni_nd_kandidata"];
													$vrijeme = date("d.m.Y H:i", strtotime($row["vrijeme_kreiranja_nd_kandidata"]));
													$zaduzen = getZaposlenikimeR($row["zaduzen_zaposlenik_nd_kandidata"]);
													
													
													if($type == 0){
														$type_ispis = "danger";
														$poruka = "Kandidat se već nalazi u sistemu!";
													}else{
														$type_ispis = "success";
														$poruka = "Uspješno ste registrovali novog kandidata!";
													}
													
													
												?>
													<div class="row" style = "padding-top: 15px;"> 
														<div class = "col-xs-12 text-center">
															<div class="alert alert-<?php echo $type_ispis; ?>" role="alert"><?php echo $poruka; ?>	</div>
														</div>
													</div>
													<div class="row" style = "padding-top: 15px;"> 
														<div class="col-xs-12">
															<h5 style = "font-weight: bold;"><i class="fa fa-info-circle" style = "margin-right: 10px;" aria-hidden="true"></i>Osnovne informacije</h5>
														</div>
													</div>
													<div class="row" style = "padding-top: 5px;">
														<div class = "col-xs-1 text-center">
															<i class="fa fa-user-o" aria-hidden="true"></i>
														</div>
														<div style = "font-weight: bold;" class = "col-xs-3 text-right">
															Ime i prezime:
														</div>
														<div class = "col-xs-8 text-left">
															<a href = "<?php getSiteURL(); ?>nostrifikacija_diploma?page=otvori_ND_kandidata&id=<?php echo $id; ?>" >
															<?php echo " ".$ime." ".$prezime ."";?>
															</a>
														</div>
													</div>
													<div class="row" style = "padding-top: 5px;">
														<div class = "col-xs-1 text-center">
															<i class="fa fa-calendar" aria-hidden="true"></i>
														</div>
														<div style = "font-weight: bold;" class = "col-xs-3 text-right">
															Vrijeme kreiranja:
														</div>
														<div class = "col-xs-8 text-left" style = "word-break: break-all;">
															<?php echo " ".$vrijeme." ";?>
														</div>
													</div>
													<div class="row" style = "padding-top: 15px;">
														<div class="col-xs-12">
															<h5 style = "font-weight: bold;"><i class="fa fa-globe" style = "margin-right: 10px;" aria-hidden="true"></i>Kontakt informacije</h5>
														</div>
													</div>
													<div class="row" style = "padding-top: 5px;">
														<div class = "col-xs-1 text-center">
															<i class="fa fa-mobile" aria-hidden="true"></i>
														</div>
														<div style = "font-weight: bold;" class = "col-xs-3 text-right">
															Mobitel:
														</div>
														<div class = "col-xs-8 text-left">
															<?php echo " ".$mobilni." ";?> 
														</div>
													</div>
													<div class="row" style = "padding-top: 15px;">
														<div class="col-xs-12">
															<h5 style = "font-weight: bold;"><i class="fa fa-user" style = "margin-right: 10px;" aria-hidden="true"></i>Menadžer</h5>
														</div>
													</div>
													<div class="row" style = "padding-top: 5px;">
														<div class = "col-xs-1 text-center">
															<i class="fa fa-user-plus" aria-hidden="true"></i>
														</div>
														<div style = "font-weight: bold;" class = "col-xs-3 text-right">
															Zadužen/a:
														</div>
														<div class = "col-xs-8 text-left">
															<?php echo " ".$zaduzen." ";?>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
				<?php 
							}else{
								echo '
									<div class="alert material-alert material-alert_danger">
										<h4>Problem sa prijavom kandidata po preporuci!</h4>
										<p>Kontaktirajte administratora za pomoć.</p>
										<br />
										<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
									</div>
								';
							}
						break;
						
						case "listaObrada":
							if((in_array( "1" , $employee_status)) OR (in_array( "16" , $employee_status)) OR (in_array( "16" , $employee_supervizor)) OR (in_array( "9" , $employee_status))){
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
									$statusFilterImp = "221,222,224,223,225,226,227,31,32,41,42,5,6";
									$statusFilterExp = explode(",", $statusFilterImp);
								}
								if(isset($_POST["strukaFilter"])){
									$strukaFilterImp = implode(",", $_POST["strukaFilter"]);
									$strukaFilterExp = explode(",", $strukaFilterImp);
								}else{
									$strukaFilterImp = "0";
									$strukaFilterExp = explode(",", $strukaFilterImp);
								}
								if(isset($_POST["zadnjaKomFilter"])){
									$zadnjaKomFilterExp = explode(",", $_POST["zadnjaKomFilter"]);
									$zadnjaKomFilterImp = implode(",", $zadnjaKomFilterExp);
								}else{
									$zadnjaKomFilterImp = "0"; 
									$zadnjaKomFilterExp = explode(",", $zadnjaKomFilterImp);
								}
								if(isset($_POST["brDanaStatusFilter"])){
									$brDanaStatusFilterExp = explode(",", $_POST["brDanaStatusFilter"]);
									$brDanaStatusFilterImp = implode(",", $brDanaStatusFilterExp);
									if(in_array("1",$brDanaStatusFilterExp)){
										$brojStatusFilter = intval($_POST["brojStatusFilter"]);
									}else{
										$brojStatusFilter = NULL;
									}
								}else{
									$brDanaStatusFilterImp = "0"; 
									$brDanaStatusFilterExp = explode(",", $brDanaStatusFilterImp);
									$brojStatusFilter = NULL;
								}
								if(isset($_POST["brDanaKomFilter"])){
									$brDanaKomFilterExp = explode(",", $_POST["brDanaKomFilter"]);
									$brDanaKomFilterImp = implode(",", $brDanaKomFilterExp);
									if(in_array("1",$brDanaKomFilterExp)){
										$brojKomunikacijaFilter = intval($_POST["brojKomunikacijaFilter"]);
									}else{
										$brojKomunikacijaFilter = NULL;
									}
								}else{
									$brDanaKomFilterImp = "0"; 
									$brDanaKomFilterExp = explode(",", $brDanaKomFilterImp);
									$brojKomunikacijaFilter = NULL;
								}
				?>
							<style>
								.dugme{
									align-items: center;
									box-shadow: 12px 12px 16px 0 rgba(0, 0, 0, 0.25),
									-8px -8px 12px 0 rgba(255, 255, 255, 0.3);
									border-radius: 50px;
									justify-content: center;
									padding: 20px;
									width: 50%;
								}
								.podnaslov_type{
									font-size: large;
									font-weight: bold;
									color: #686868;
								}
								.broj_type{
									font-size: xxx-large;
									font-weight: bold;
									color: #686868;
								}
								.icon{
									font-size: large;
									color: #686868;
								}
								.dugmeX{
									background: linear-gradient(-45deg, rgba(255,255,255,0.40), rgba(0,0,0,0.15));
								}
								.prelaz{
									animation:prelazX 1s infinite;
								}
								@keyframes prelazX{
									0%{
										background: linear-gradient(-45deg, rgba(255,25,25,0.40), rgba(0,0,0,0.15));
									}
									10%{
										background: linear-gradient(-45deg, rgba(255,50,50,0.40), rgba(0,0,0,0.15));
									}
									20%{
										background: linear-gradient(-45deg, rgba(255,75,75,0.40), rgba(0,0,0,0.15));
									}
									30%{
										background: linear-gradient(-45deg, rgba(255,100,100,0.40), rgba(0,0,0,0.15));
									}
									40%{
										background: linear-gradient(-45deg, rgba(255,125,125,0.40), rgba(0,0,0,0.15));
									}
									50%{
										background: linear-gradient(-45deg, rgba(255,150,150,0.40), rgba(0,0,0,0.15));
									}
									60%{
										background: linear-gradient(-45deg, rgba(255,175,175,0.40), rgba(0,0,0,0.15));
									}
									70%{
										background: linear-gradient(-45deg, rgba(255,200,200,0.40), rgba(0,0,0,0.15));
									}
									80%{
										background: linear-gradient(-45deg, rgba(255,225,225,0.40), rgba(0,0,0,0.15));
									}
									90%{
										background: linear-gradient(-45deg, rgba(255,250,250,0.40),rgba(0,0,0,0.15));
									}
									100%{
										background: linear-gradient(-45deg, rgba(255,255,255,0.40), rgba(0,0,0,0.15));
									}
								}
							</style>
							<div class = "row">
								<div class = "col-xs-9 idk_color_green">
									<h1>
										<i class="fa fa-spinner" aria-hidden="true" style = "margin-right: 10px;"></i>
										Obrada kandidata
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
												<div class = "row">
												<?php 
												
													$uslovZaposlenikReminder = "";
													if((in_array( "1" , $employee_status)) OR (in_array( "9" , $employee_status)) OR (in_array( "16" , $employee_supervizor))){
														$uslovZaposlenikReminder = " reminder_employee is not null";
													}else{
														$uslovZaposlenikReminder = " reminder_employee = ".$logged_employee_id."";
													}
													$trenutnoVrijeme = date("Y-m-d H:i:s");
													$naredniDan = date("Y-m-d",strtotime('+1 day',strtotime($trenutnoVrijeme))) . " 00:00:00";
													$trenutnoVrijemeKraj = date("Y-m-d")." 23:59:59";
													// echo "
														// SELECT 
															// sum(
																// case 
																	// when reminder_date_time < '".$trenutnoVrijeme."' then 1 else 0
																// end
															// ) AS brojProsliReminder,
															// sum(
																// case 
																	// when reminder_date_time >= '".$trenutnoVrijeme."' AND reminder_date_time <= '".$trenutnoVrijemeKraj."' then 1 else 0
																// end
															// ) AS brojDanasReminder,
															// sum(
																// case 
																	// when reminder_date_time >= '".$naredniDan."' then 1 else 0
																// end
															// ) AS brojBuduciReminder
														// FROM 
															// idk_reminders
														// WHERE 
															// reminder_type = 1
															// AND 
															// reminder_status = 1
															// AND 
															// ".$uslovZaposlenikReminder."
													// ";
													$queryPrebrojReminder = $db->prepare("
														SELECT 
															sum(
																case 
																	when reminder_date_time < '".$trenutnoVrijeme."' then 1 else 0
																end
															) AS brojProsliReminder,
															sum(
																case 
																	when reminder_date_time >= '".$trenutnoVrijeme."' AND reminder_date_time <= '".$trenutnoVrijemeKraj."' then 1 else 0
																end
															) AS brojDanasReminder,
															sum(
																case 
																	when reminder_date_time >= '".$naredniDan."' then 1 else 0
																end
															) AS brojBuduciReminder
														FROM 
															idk_reminders
														WHERE 
															reminder_type = 1
															AND 
															reminder_status = 1
															AND 
															".$uslovZaposlenikReminder."
													");
													$queryPrebrojReminder->execute();
													$rowPrebrojReminder = $queryPrebrojReminder->fetch();
													$brojProsliReminder = intval($rowPrebrojReminder["brojProsliReminder"]);
													$brojDanasReminder = intval($rowPrebrojReminder["brojDanasReminder"]);
													$brojBuduciReminder = intval($rowPrebrojReminder["brojBuduciReminder"]);
													
												?>
													<div class = "col-sm-4 col-md-4 col-xs-12 text-center">
														
														<a <?php echo ($brojProsliReminder != 0) ?  'href = "'.getSiteUrlr().'dipl?page=reminderKandidatiLista&type=1"' : ''; ?> target="_BLANK">
															<button class="btn btn material-btn material dugme <?php echo ($brojProsliReminder != 0) ?  "prelaz" : "dugmeX"; ?>">
																<i class="icon fa fa-calendar-minus-o" aria-hidden="true"></i><br>
																<span class="broj_type"><?php echo $brojProsliReminder;?></span><br>
																<span class="podnaslov_type">Prošli</span>
															</button>
														</a>
													</div>
													<div class = "col-sm-4 col-md-4 col-xs-12 text-center">
														<a <?php echo ($brojDanasReminder != 0) ?  'href = "'.getSiteUrlr().'dipl?page=reminderKandidatiLista&type=2"' : ''; ?> target="_BLANK">
															<button class="btn btn material-btn material dugme <?php echo ($brojDanasReminder != 0) ?  "prelaz" : "dugmeX"; ?>">
																<i class="icon fa fa-calendar-o" aria-hidden="true"></i><br>
																<span class="broj_type"><?php echo $brojDanasReminder;?></span><br>
																<span class="podnaslov_type">Danas</span>
															</button>
														</a>
													</div>
													<div class = "col-sm-4 col-md-4 col-xs-12 text-center">
														<a <?php echo ($brojBuduciReminder != 0) ?  'href = "'.getSiteUrlr().'dipl?page=reminderKandidatiLista&type=3"' : ''; ?> target="_BLANK">
															<button class="btn btn material-btn material dugme dugmeX">
																<i class="icon fa fa-calendar-plus-o" aria-hidden="true"></i><br>
																<span class="broj_type"><?php echo $brojBuduciReminder;?></span><br>
																<span class="podnaslov_type">Budući</span>
															</button>
														</a>
													</div>
												</div>
											</div>
										</div>
										<br>
										<br>
										<hr>
										<div class="row">
											<div class="col-xs-12">
												<div class="panel-group material-accordion material-accordion_success" id="accordion1">
													<div class="panel panel-success material-accordion__panel material-accordion__panel">
														<div class="panel-heading material-accordion__heading">
															<h4 class="panel-title">
															<a class="material-accordion__title" style="margin-bottom:0.3rem" data-toggle="collapse" data-parent="#accordion1" href="#filterKandidata"><i class="fa fa-search" aria-hidden="true" style = "margin-right: 10px;"></i>Filter</a>
															</h4>
														</div>
														<div id="filterKandidata" class="panel-collapse collapse material-accordion__collapse">
															<div class="panel-body">
																<form action="<?php getSiteURL(); ?>dipl?page=listaObrada" enctype="multipart/form-data" method="post" role="form" class="form-horizontal" id = "formaFilter">
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
																					<select class="selectpicker" title = "Odaberite status kandidata" data-actions-box="true" data-live-search = "true" id="statusFilter" name="statusFilter[]" data-selected-text-format = "count > 2" multiple>
																						<option <?php if(in_array("221", $statusFilterExp)) echo "selected"; ?> value = "221">Prikupljanje dokumentacije</option>
																						<option <?php if(in_array("222", $statusFilterExp)) echo "selected"; ?> value = "222">Nepotpuna dokumentacija</option>
																						<option <?php if(in_array("224", $statusFilterExp)) echo "selected"; ?> value = "224">Dokumentacija kompletirana</option>
																						<option <?php if(in_array("223", $statusFilterExp)) echo "selected"; ?> value = "223">Na prevodu</option>
																						<option <?php if(in_array("225", $statusFilterExp)) echo "selected"; ?> value = "225">Prevod završen</option>
																						<option <?php if(in_array("226", $statusFilterExp)) echo "selected"; ?> value = "226">Poslan zahtjev</option>
																						<option <?php if(in_array("227", $statusFilterExp)) echo "selected"; ?> value = "227">Potpisan zahtjev</option>
																						<option <?php if(in_array("31", $statusFilterExp)) echo "selected"; ?> value = "31">Poslana pošta</option>
																						<option <?php if(in_array("32", $statusFilterExp)) echo "selected"; ?> value = "32">Zaprimili dokumentaciju</option>
																						<option <?php if(in_array("41", $statusFilterExp)) echo "selected"; ?> value = "41">U obradi</option>
																						<option <?php if(in_array("41", $statusFilterExp)) echo "selected"; ?> value = "42">Stigla taxa / dopuna</option>
																						<option <?php if(in_array("5", $statusFilterExp)) echo "selected"; ?> value = "5">Plaćena taxa / Poslana dopuna</option>
																						<option <?php if(in_array("6", $statusFilterExp)) echo "selected"; ?> value = "6">Završen</option>
																					</select>
																				</div>
																			</div>
																		</div>
																	</div>
																	<?php 
																		$queryStruke = $db->prepare("
																			SELECT 
																				id_struke, 
																				naziv_struke
																			FROM 
																				idk_struke
																		");
																		$queryStruke->execute();
																	?>
																	<div class="form-group">
																		<div class="col-md-offset-2 col-sm-8 text-center">
																			<label for="strukaFilter" class="col-sm-4 control-label">
																				Struke:
																			</label>
																			<div class="col-sm-8">
																				<div class="">
																					<select class="selectpicker" title = "Odaberite struke" data-actions-box="true" data-live-search = "true" id="strukaFilter" name="strukaFilter[]" data-selected-text-format = "count > 2" multiple>
																						<?php 
																							while($rowStruke = $queryStruke->fetch()){
																								$strukaId = intval($rowStruke["id_struke"]);
																								$strukaNaziv = $rowStruke["naziv_struke"];
																								
																								if(in_array($strukaId, $strukaFilterExp)){
																									echo '<option value = "'.$strukaId.'" selected>'.$strukaNaziv.'</option>';
																								}else{
																									echo '<option value = "'.$strukaId.'">'.$strukaNaziv.'</option>';
																								}
																							}
																						?>
																					</select>
																				</div>
																			</div>
																		</div>
																	</div>
																	<div class="form-group">
																		<div class="col-md-offset-2 col-sm-8 text-center">
																			<label for="zadnjaKomFilter" class="col-sm-4 control-label">
																				Zadnja komunikacija:
																			</label>
																			<div class="col-sm-8">
																				<div class="">
																					<select class="selectpicker" title = "Odaberite DA za prikaz informacija o zadnjoj komunikaciji" data-actions-box="true" data-live-search = "true" id="zadnjaKomFilter" name="zadnjaKomFilter" data-selected-text-format = "count > 2">
																						<option <?php if(in_array("0", $zadnjaKomFilterExp)) echo "selected"; ?> value = "0" data-subtext = "Nema prikaza za zadnju komunikaciju">NE</option>
																						<option <?php if(in_array("1", $zadnjaKomFilterExp)) echo "selected"; ?> value = "1" data-subtext = "Prikazuju se informacije o zadnjoj komunikaciji">DA</option>
																					</select>
																				</div>
																			</div>
																		</div>
																	</div>
																	<div class="form-group">
																		<div class="col-md-offset-2 col-sm-8 text-center">
																			<label for="brDanaStatusFilter" class="col-sm-4 control-label">
																				Broj dana na statusu:
																			</label>
																			<div class="col-sm-8">
																				<div class="">
																					<select class="selectpicker" title = "Odaberite DA za unos broja dana za status" data-actions-box="true" data-live-search = "true" id="brDanaStatusFilter" name="brDanaStatusFilter" data-selected-text-format = "count > 2">
																						<option <?php if(in_array("0", $brDanaStatusFilterExp)) echo "selected"; ?> value = "0" data-subtext = "">NE</option>
																						<option <?php if(in_array("1", $brDanaStatusFilterExp)) echo "selected"; ?> value = "1" data-subtext = "">DA</option>
																					</select>
																				</div>
																			</div>
																		</div>
																	</div>
																	<script>
																		$(document).ready(function(){
																			if(parseInt($("#brDanaStatusFilter").val()) === 1){
																				$("#brStaSH").show();
																			}else{
																				$("#brStaSH").hide();
																			}
																		});
																		$("#brDanaStatusFilter").change(function(){
																			if(parseInt($("#brDanaStatusFilter").val()) === 1){
																				$("#brStaSH").show();
																			}else{
																				$("#brStaSH").hide();
																				$("#brojStatusFilter").val(null);
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
																				Broj:
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
																		});
																		$("#brojStatusFilter").change(function(){
																			var brojStatProvjera = parseInt($("#brojStatusFilter").val());
																			if(brojStatProvjera < 1){
																				$('#alertBrojStatus').show();
																				setTimeout(function(){
																					$('#alertBrojStatus').hide();
																					$("#brojStatusFilter").val(1);
																				}, 5000);
																			}
																		});
																	</script>
																	<div class="form-group">
																		<div class="col-md-offset-2 col-sm-8 text-center">
																			<label for="brDanaKomFilter" class="col-sm-4 control-label">
																				Broj dana od zadnje komunikacije:
																			</label>
																			<div class="col-sm-8">
																				<div class="">
																					<select class="selectpicker" title = "Odaberite DA za unos broja dana za status" data-actions-box="true" data-live-search = "true" id="brDanaKomFilter" name="brDanaKomFilter" data-selected-text-format = "count > 2">
																						<option <?php if(in_array("0", $brDanaKomFilterExp)) echo "selected"; ?> value = "0" data-subtext = "">NE</option>
																						<option <?php if(in_array("1", $brDanaKomFilterExp)) echo "selected"; ?> value = "1" data-subtext = "">DA</option>
																					</select>
																				</div>
																			</div>
																		</div>
																	</div>
																	<script>
																		$(document).ready(function(){
																			if(parseInt($("#brDanaKomFilter").val()) === 1){
																				$("#brKomSH").show();
																			}else{
																				$("#brKomSH").hide();
																			}
																		});
																		$("#brDanaKomFilter").change(function(){
																			if(parseInt($("#brDanaKomFilter").val()) === 1){
																				$("#brKomSH").show();
																				$('#zadnjaKomFilter').val(1);
																				$('#zadnjaKomFilter').selectpicker('refresh');
																			}else{
																				$("#brKomSH").hide();
																				$("#brojKomunikacijaFilter").val(null);
																				$('#zadnjaKomFilter').val(0);
																				$('#zadnjaKomFilter').selectpicker('refresh');
																			}
																		});
																	</script>
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
																				Broj:
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
																		});
																		$("#brojKomunikacijaFilter").change(function(){
																			var brojKomProvjera = parseInt($("#brojKomunikacijaFilter").val());
																			if(brojKomProvjera < 1 || brojKomProvjera > 300){
																				$('#alertBrojKomunikacija').show();
																				setTimeout(function(){
																					$('#alertBrojKomunikacija').hide();
																					$("#brojKomunikacijaFilter").val(1);
																				}, 5000);
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
													$sortiranjeKolone = '';
													if(in_array("0", $zadnjaKomFilterExp)){
														$sortiranjeKolone = '
															"aoColumns": [
																{ "width": "5%" },
																{ "width": "12.5%" },
																{ "width": "5%"},
																{ "width": "10%" },
																{ "width": "20%" },
																{ "width": "7.5%" },
																{ "width": "5%", "bSortable": false },
																{ "width": "7.5%", "bSortable": false },
																{ "width": "5%", "bSortable": false },
																{ "width": "15%" },
																{ "width": "7.5%", "bSortable": false }
															],
														';
													}else{
														$sortiranjeKolone = '
															"aoColumns": [
																{ "width": "5%" },
																{ "width": "12.5%" },
																{ "width": "5%", "bSortable": false },
																{ "width": "10%" },
																{ "width": "20%" },
																{ "width": "7.5%" },
																{ "width": "5%", "bSortable": false },
																{ "width": "7.5%" },
																{ "width": "5%", "bSortable": false },
																{ "width": "15%" },
																{ "width": "7.5%", "bSortable": false }
															],
														';
													}
												?>
												<script type="text/javascript">
													$(document).ready(function() {
														
														var table_s = $('#kandidati_pregled_ND').DataTable({
													
															responsive: true,
															"pageLength": 10,
															"processing": true,
															"serverSide": true,
															"order": [[ 0, "desc" ]],

															"bAutoWidth": false,

															<?php 
																echo $sortiranjeKolone;
															?>
															"ajax":{
																url :"ssdata_dipl.php?page=lista_obrada",
																type: "POST",
																data:{
																	"drzava_ssd": '<?php echo $drzavaFilterImp; ?>',
																	"status_ssd": '<?php echo $statusFilterImp; ?>',
																	"struka_ssd": '<?php echo $strukaFilterImp; ?>',
																	"komunikacija_ssd": '<?php echo $zadnjaKomFilterImp; ?>',
																	"status_da_ne_ssd": '<?php echo $brDanaStatusFilterImp; ?>',
																	"status_broj_ssd": '<?php echo $brojStatusFilter; ?>',
																	"kom_da_ne_ssd": '<?php echo $brDanaKomFilterImp; ?>',
																	"kom_broj_ssd": '<?php echo $brojKomunikacijaFilter; ?>'
																},
																error: function(data){
																	$(".list-grid-error").html(""); 
																	$("#list-grid_processing").css("display","none"); 
																	console.log(data); 
															
																},
															}
														});
														
													});
												</script>
												<table id="kandidati_pregled_ND" class="display" cellspacing="0" width="100%">
													<thead>
														<tr>
															<th class="text-center">#ID</th>
															<th class="text-center">Ime i Prezime</th>
															<th class="text-center">Država</th>
															<th class="text-center">Kontakt</th>
															<th class="text-center">Status</th>
															<th class="text-center" title = "Vrijeme kada je kandidat prebačen na prikazani status!">Status Vrijeme</th>
															<th class="text-center" title = "Koliko dana se kandidat nalazi na prikazanom statusu!">Dana na statusu</th>
															<th class="text-center" title = "Zadnja bilješka unešena od strane obrade!">Zadnja bilješka</th>
															<th class="text-center" title = "Koliko dana je prošlo od zadnje bilješke!">Dana od zadnje bilješke</th>
															<th class="text-center">Kreirao</th>
															<th class="text-center">Akcije</th>
														</tr>
													</thead>
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
						
						case "reminderKandidatiLista":
							
							if((in_array( "1" , $employee_status)) OR (in_array( "16" , $employee_status)) OR (in_array( "16" , $employee_supervizor)) OR (in_array( "9" , $employee_status))){
								$type = intval($_GET["type"]);
								$dozvoljeniTipovi = array(1,2,3);
								
								if(in_array($type, $dozvoljeniTipovi)){
									$trenutnoVrijeme = date("Y-m-d H:i:s");
									$naredniDan = date("Y-m-d",strtotime('+1 day',strtotime($trenutnoVrijeme))) . " 00:00:00";
									$trenutnoVrijemeKraj = date("Y-m-d")." 23:59:59";
									//type: 1-prosli 2-danas 3-buduci
									$naslovIspis = "";
									$iconIspis = "";
									$uslovVrijemeReminder = "";
									if($type == 1){
										$naslovIspis = "Reminder - Prošli";
										$iconIspis = 'fa-calendar-minus-o';
										$uslovVrijemeReminder = "r.reminder_date_time < '".$trenutnoVrijeme."'";
									}else if($type == 2){
										$naslovIspis = "Reminder - Danas";
										$iconIspis = 'fa-calendar-o';
										$uslovVrijemeReminder = "r.reminder_date_time >= '".$trenutnoVrijeme."' AND r.reminder_date_time <= '".$trenutnoVrijemeKraj."'";
									}else{
										$naslovIspis = "Reminder - Budući";
										$iconIspis = 'fa-calendar-plus-o';
										$uslovVrijemeReminder = "r.reminder_date_time >= '".$naredniDan."'";
									}
									
									$uslovZaposlenikReminder = "";
									if((in_array( "1" , $employee_status)) OR (in_array( "9" , $employee_status)) OR (in_array( "16" , $employee_supervizor))){
										$uslovZaposlenikReminder = " r.reminder_employee is not null";
									}else{
										$uslovZaposlenikReminder = " r.reminder_employee = ".$logged_employee_id."";
									}
								
				?>
							<div class = "row">
								<div class = "col-xs-9 idk_color_green">
									<h1>
										<i class="fa <?php echo $iconIspis; ?>" aria-hidden="true" style = "margin-right: 10px;"></i>
										<?php echo $naslovIspis; ?>
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
												<script>
													$(document).ready(function() {
														$('#kandidati').DataTable({

															responsive: true,

															"order": [[ 7, "desc" ]],

															"bAutoWidth": false,
															
															"aoColumns": [
																{ "width": "5%" },
																{ "width": "15%" },
																{ "width": "5%" },
																{ "width": "10%" },
																{ "width": "15%" },
																{ "width": "25%" },
																{ "width": "10%" },
																{ "width": "15%" }
															]
														});
													} );
												</script>
												<table id="kandidati" class="display" cellspacing="0" width="100%">
													<thead>
														<tr>
															<th class="text-center">#RB</th>
															<th class="text-center">Ime i Prezime</th>
															<th class="text-center">Država</th>
															<th class="text-center">Kontakt</th>
															<th class="text-center">Status</th>
															<th class="text-center">Sadržaj bilješke</th>
															<th class="text-center" title = "Zaposlenik koji je postavio reminder.">Dodao</th>
															<th class="text-center">Vrijeme termina</th>
														</tr>
													</thead>
													<tbody>
														<?php 
															$brojKan = 0;
															// echo "
																// SELECT 
																	// kan.id_broj_nd_kandidata, kan.ime_nd_kandidata, kan.prezime_nd_kandidata, kan.mobilni_nd_kandidata, kan.status_nd_kandidata, kan.pstatus_nd_kandidata, bilj.sadrzaj_biljeska_nd, r.reminder_date_time
																// FROM 
																	// idk_reminders r
																// INNER JOIN 
																	// idk_nd_kandidata kan
																// ON 
																	// r.reminder_candidate_id = kan.id_broj_nd_kandidata
																// INNER JOIN 
																	// idk_nd_kandidata_biljeske bilj
																// ON 
																	// r.reminder_foreign_key = bilj.id_biljeska_nd
																// WHERE 
																	// r.reminder_type = 1
																	// AND 
																	// r.reminder_status = 1
																	// AND 
																	// ".$uslovZaposlenikReminder."
																	// AND 
																	// ".$uslovVrijemeReminder."
															// ";
															$query = $db->prepare("
																SELECT 
																	kan.id_broj_nd_kandidata, kan.ime_nd_kandidata, kan.prezime_nd_kandidata, kan.mobilni_nd_kandidata, kan.status_nd_kandidata, kan.pstatus_nd_kandidata, bilj.sadrzaj_biljeska_nd, r.reminder_date_time, r.reminder_employee
																FROM 
																	idk_reminders r
																INNER JOIN 
																	idk_nd_kandidata kan
																ON 
																	r.reminder_candidate_id = kan.id_broj_nd_kandidata
																INNER JOIN 
																	idk_nd_kandidata_biljeske bilj
																ON 
																	r.reminder_foreign_key = bilj.id_biljeska_nd
																WHERE 
																	r.reminder_type = 1
																	AND 
																	r.reminder_status = 1
																	AND 
																	".$uslovZaposlenikReminder."
																	AND 
																	".$uslovVrijemeReminder."
															");
															$query->execute();
															while($row = $query->fetch()){
																$brojKan++;
																$remKanId = intval($row["id_broj_nd_kandidata"]);
																$remKanImePrezime = '<a href="'.getSiteUrlr().'nostrifikacija_diploma?page=otvori_ND_kandidata&id='.$remKanId.'">'.$row["ime_nd_kandidata"].' '.$row["prezime_nd_kandidata"].'</a>';
																$remKanMobitel = $row["mobilni_nd_kandidata"];
																$remKanDrzava = "";
																if(strpos(substr($remKanMobitel, 0, 4), "381")){
																	$remKanDrzava = '<img src="'.getSiteUrlr().'images/sr3d.png" width=20>';
																}else if(strpos(substr($remKanMobitel, 0, 4), "387")){
																	$remKanDrzava = '<img src="'.getSiteUrlr().'images/bs3d.png" width=20>';
																}else if(strpos(substr($remKanMobitel, 0, 3), "49")){
																	$remKanDrzava = '<img src="'.getSiteUrlr().'images/de3d.png" width=20>';
																}else{
																	$remKanDrzava = '<img src="'.getSiteUrlr().'images/globe3d.png" width=20>';
																}
																$remKanStatus = getStatusDIPLKandidatR($row["status_nd_kandidata"],$row["pstatus_nd_kandidata"]);
																$remSadrzajBilj = substr($row["sadrzaj_biljeska_nd"], 0, 40)."...";
																$remVrijeme = date("d.m.Y H:i", strtotime($row["reminder_date_time"]));
																$remVrijemeOrder = strtotime($row["reminder_date_time"]);
																$remZaposlenik = getZaposlenikimeR($row["reminder_employee"]);
																
																echo '
																	<tr>
																		<td class="text-center">'.$brojKan.'</td>
																		<td class="text-center">'.$remKanImePrezime.'</td>
																		<td class="text-center">'.$remKanDrzava.'</td>
																		<td class="text-center">'.$remKanMobitel.'</td>
																		<td class="text-center">'.$remKanStatus.'</td>
																		<td class="text-center">'.$remSadrzajBilj.'</td>
																		<td class="text-center">'.$remZaposlenik.'</td>
																		<td class="text-center" data-order="'.$remVrijemeOrder.'">'.$remVrijeme.'</td>
																	</tr>
																';
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
											<h4>Pogrešan tip pregleda kandidata!</h4>
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

						case "iboundPoziviSkladiste":
							if(in_array($logged_employee_id, array(75,173,158,201))){
				?>
							<div class = "row">
								<div class = "col-xs-9 idk_color_green">
									<h1>
										<i class="fa fa-phone-square" aria-hidden="true" style = "margin-right: 10px;"></i>
										Inbound pozivi za kandidate u Skladištu
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
											<div class="col-xs-12 text-center">
												<div class="alert alert-warning" role="alert">Otvorite profil kandidata i prebaciti određenom zaposleniku!</div>
											</div>
										</div>
										<hr>
										<div class="row">
											<div class="col-xs-12">
												<script>
													$(document).ready(function() {
														$('#inbound').DataTable({

															responsive: true,

															"order": [[ 0, "desc" ]],

															"bAutoWidth": false,
															
															"aoColumns": [
																{ "width": "5%" },
																{ "width": "15%" },
																{ "width": "50%" },
																{ "width": "15%" },
																{ "width": "15%" }
															]
														});
													} );
												</script>
												<table id="inbound" class="display" cellspacing="0" width="100%">
													<thead>
														<tr>
															<th class="text-center">#</th>
															<th class="text-center">Ime i Prezime</th>
															<th class="text-center">Sadrzaj</th>
															<th class="text-center">Dodao</th>
															<th class="text-center">Vrijeme dodavanja</th>
														</tr>
													</thead>
													<tbody>
														<?php 
															$queryInbound = $db->prepare("
																SELECT 
																	kan.id_broj_nd_kandidata, 
																	kan.ime_nd_kandidata, 
																	kan.prezime_nd_kandidata,
																	bilj.sadrzaj_biljeska_nd,
																	bilj.vrijeme_dodavanja_biljeska_nd, 
																	bilj.dodao_zaposlenik_biljeska_nd
																FROM 
																	idk_nd_kandidata kan
																JOIN 
																	idk_nd_kandidata_biljeske bilj
																ON 
																	kan.id_broj_nd_kandidata = bilj.id_kandidata_biljeska_nd
																	AND 
																	bilj.id_biljeska_nd IN (
																		SELECT 
																			MAX(b.id_biljeska_nd)
																		FROM 
																			idk_nd_kandidata_biljeske b
																		JOIN 
																			idk_nd_kandidata k
																		ON 
																			b.id_kandidata_biljeska_nd = k.id_broj_nd_kandidata
																		WHERE 
																			k.inbound_aktivan = 1
																			AND 
																			k.zaduzen_zaposlenik_nd_kandidata = 139
																			AND 
																			b.status_biljeska_nd = 2
																			AND 
																			b.tip_biljeska_nd = 17
																		GROUP BY b.id_kandidata_biljeska_nd 
																	)
																WHERE 
																	kan.inbound_aktivan = 1
																	AND 
																	kan.zaduzen_zaposlenik_nd_kandidata = 139
																ORDER BY bilj.vrijeme_dodavanja_biljeska_nd ASC 
															");

															$queryInbound->execute();

															while($rowInbound = $queryInbound->fetch()){
																$id_broj_nd_kandidata = $rowInbound["id_broj_nd_kandidata"];
																$ime_nd_kandidata = $rowInbound["ime_nd_kandidata"];
																$prezime_nd_kandidata = $rowInbound["prezime_nd_kandidata"];
																$sadrzaj_biljeska_nd = $rowInbound["sadrzaj_biljeska_nd"];
																$vrijeme_dodavanja_biljeska_nd = date("d.m.Y H:i",strtotime($rowInbound["vrijeme_dodavanja_biljeska_nd"]));
																$dodao_zaposlenik_biljeska_nd = getZaposlenikimeR($rowInbound["dodao_zaposlenik_biljeska_nd"]);

																echo '
																	<tr>
																		<td class="text-center">'.$id_broj_nd_kandidata.'</td>
																		<td class="text-center"><a target="_BLANK" href="/nostrifikacija_diploma?page=otvori_ND_kandidata&id='.$id_broj_nd_kandidata.'">'.$ime_nd_kandidata.' '.$prezime_nd_kandidata.'</a></td>
																		<td>'.$sadrzaj_biljeska_nd.'</td>
																		<td class="text-center">'.$dodao_zaposlenik_biljeska_nd.'</td>
																		<td class="text-center" data-order='.strtotime($rowInbound["vrijeme_dodavanja_biljeska_nd"]).'>'.$vrijeme_dodavanja_biljeska_nd.'</td>
																	</tr>
																';
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
				<!--Switch END-->
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
