<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
include('includes/functions.php');
include("includes/connect.php");
include('html_pdf_generator.php');
include('pdf_generator.php');


function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}



$action = $_GET["action"];

switch ($action){
	
	case "poziv":
		$token = $_GET['token'];
		$jezik = $_GET['jezik'];
		
		$query_get_id = $db -> prepare('
			SELECT ug_kandidat_id, ug_datum_otvaranja_linka, ug_status
			FROM idk_nd_ugovori
			WHERE ug_token = :token
		');
		
		$query_get_id -> execute(array(
			':token' => $token
		));
		
		$row_get_id = $query_get_id -> fetch();
		
		$kandidat_id 				= $row_get_id['ug_kandidat_id'];
		$ug_datum_otvaranja_linka 	= $row_get_id['ug_datum_otvaranja_linka'];
		$ug_status 					= $row_get_id['ug_status'];

		if(is_null($kandidat_id)){
			echo "Link nije validan"; 
		}
		
		else{
			if(is_null($ug_datum_otvaranja_linka) && $ug_status != 0){
				$query_set_otvorio_link = $db -> prepare('
				UPDATE idk_nd_ugovori
				SET ug_status = 4, ug_datum_otvaranja_linka = now()
				WHERE ug_token = :token
				');
				
				$query_set_otvorio_link -> execute(array(':token' => $token));
			}
			generisiUgovorNew($kandidat_id, $jezik);
		}
	
	break;

	case "prihvacen":
		Global $db;
		
		$token = $_GET['token'];
		$jezik = $_GET['jezik'];

		$query_check_status = $db->prepare('
			SELECT ug_status, ug_kandidat_id, ug_jezik
			FROM idk_nd_ugovori
			WHERE ug_token = :token
		');
		$query_check_status -> execute(array(':token' => $token));
		$row_check_status = $query_check_status -> fetch();
		
		$ug_status	 = $row_check_status['ug_status'];
		$kandidat_id = $row_check_status['ug_kandidat_id'];
		$ug_jezik	 = $row_check_status['ug_jezik'];
		
		
		if($ug_status == 1 || $ug_status == 4){
			$datum_prihvatanja = date("Y-m-d H:i:s", time());
			
			$query_update_ugovor_kandidata = $db -> prepare("
				UPDATE idk_nd_ugovori 
				SET ug_status = 2, ug_datum_prihvatanja = :datum_prihvatanja
				WHERE ug_token = :token
			");

			$query_update_ugovor_kandidata -> execute(array(
				':datum_prihvatanja' => $datum_prihvatanja,
				':token' => $token
			));
			
			$ugovor_link = getSiteUrlr()."ugovorNew/".$token."/".$ug_jezik;
			if($ug_jezik == "sr"){
				$poruka_text_mail	= "Uspešno ste potpisali ugovor! Možete ga preuzeti zajedno sa predračunom i uplatnicom klikom na: <a href='".$ugovor_link."'>LINK</a>";
				$poruka_text_viber	= "Uspešno ste potpisali ugovor! Možete ga preuzeti zajedno sa predračunom i uplatnicom klikom na:";
				$poruka_text_sms	= "Uspesno ste potpisali ugovor! Mozete ga preuzeti zajedno sa predracunom i uplatnicom klikom na: ".$ugovor_link;											
				$poruka_button 		= "Otvori";
				$subject_ugovor		= "Ugovor i ";
			}
			else if($ug_jezik == "bs"){
				$poruka_text_mail	= "Uspješno ste potpisali ugovor! Možete ga preuzeti zajedno sa predračunom i uplatnicom klikom na: <a href='".$ugovor_link."'>LINK</a>";
				$poruka_text_viber	= "Uspješno ste potpisali ugovor! Možete ga preuzeti zajedno sa predračunom i uplatnicom klikom na: ";
				$poruka_text_sms	= "Uspjesno ste potpisali ugovor! Mozete ga preuzeti zajedno sa predracunom i uplatnicom klikom na: ".$ugovor_link;
				$poruka_button 		= "Otvori";
				$subject_ugovor		= "Ugovor i ";
				
			}
			else{				
				$poruka_text_mail	= "Sie haben den Vertrag unterschrieben und können ihn gemeinsam mit der Pro-forma-Rechnung und den Einzahlungsschein herunterladen, indem Sie auf die Schaltfläche unten klicken: <a href='".$ugovor_link."'>LINK</a>";
				$poruka_text_viber	= "Sie haben den Vertrag unterschrieben und können ihn gemeinsam mit der Pro-forma-Rechnung und den Einzahlungsschein herunterladen, indem Sie auf die Schaltfläche unten klicken:";
				$poruka_text_sms	= "Sie haben den Vertrag unterschrieben und können ihn gemeinsam mit der Pro-forma-Rechnung und den Einzahlungsschein herunterladen, indem Sie auf die Schaltfläche unten klicken: ".$ugovor_link;													
				$poruka_button 		= "Öffnen";
				$subject_ugovor		= "Vertrag und";
			}
			
			sendViberUgovorLink($kandidat_id, $poruka_text_viber, $poruka_text_sms, $ugovor_link, $poruka_button);
			sendMailUgovorPredracun($kandidat_id, $poruka_text_mail, $ugovor_link, $subject_ugovor, 2);
			generisiUgovorNew($kandidat_id, $jezik);
		}
		else{
			echo 'Već ste donijeli odluku o prihvatanju ugovora';
		}

	break;

	case "generisi_ugovor":
		$token = $_GET['token'];
		$jezik = $_GET['jezik'];
		$ug_file = $_GET['fileName'];
		$ug_ip_adresa = $_GET['ug_ip_adresa'];
		$ug_timestamp = $_GET['ug_timestamp'];



		$query_update_ugovor_kandidata = $db -> prepare("
			UPDATE idk_nd_ugovori 
			SET ug_file = :ug_file, ug_ip_adresa = :ug_ip_adresa, ug_timestamp = :ug_timestamp
			WHERE ug_token = :token
		");
		$query_update_ugovor_kandidata -> execute(array(
			':ug_file' => $ug_file,
			':ug_timestamp' => $ug_timestamp,
			':ug_ip_adresa' => $ug_ip_adresa,
			':token' => $token
		));
		$query_get_kandidat_id = $db -> prepare("
			SELECT ug_kandidat_id FROM idk_nd_ugovori 
			WHERE ug_token = :token
		");

		$query_get_kandidat_id -> execute(array(
			':token' => $token
		));
		$row_get_kandidat = $query_get_kandidat_id->fetch();
		

		generisiUgovorNew($row_get_kandidat['ug_kandidat_id'], "sr");
	break;

	case 'generisi_predracun':
		$token = $_GET['token'];
		$jezik = $_GET['jezik'];
		$predracun_id = $_GET['pr_id'];
		
		
		$query_get_kandidat_id = $db -> prepare("
			SELECT ug_kandidat_id, ug_file FROM idk_nd_ugovori 
			WHERE ug_token = :token
		");

		$query_get_kandidat_id -> execute(array(
			':token' => $token
		));
			
		$row_get_kandidat_id = $query_get_kandidat_id->fetch();
		
		$ug_kandidat_id = $row_get_kandidat_id['ug_kandidat_id'];

		$get_predracun = $db->prepare("
						SELECT pr_broj_predracuna
						FROM idk_predracuni
						WHERE pr_id = :pr_id
						");

		$get_predracun->execute(array(':pr_id' => $predracun_id));
		$predracun_row = $get_predracun->fetch();
		
		$naziv_predracuna = $predracun_row["pr_broj_predracuna"];
		$tmp2 = explode('-', $naziv_predracuna);
		$brojac_predracuna = $tmp2[1];
		$file_datum = date('YmdHis'); 
		$predracun_putanja = $file_datum."DIPLS".$brojac_predracuna.".pdf";

		$update_predracun = $db->prepare("
			UPDATE idk_predracuni
			SET pr_file = :pr_file, pr_datum_kreiranja = now(), pr_stornirano = 0
			WHERE pr_id = :pr_id
		");
		$update_predracun->execute(array(
			':pr_file' => $predracun_putanja,
			':pr_id' => $predracun_id
		));
		
		createPredracunSRB($predracun_id);
		generisiUgovorNew($ug_kandidat_id, $jezik);
	break;

	case "typage":
		Global $db;
		
		$token = $_GET['token'];
		$jezik = $_GET['jezik'];
		$ug_file = $_GET['fileName'];
		
		$query_get_kandidat_id = $db -> prepare("
			SELECT ug_kandidat_id, ug_file, ug_datum_prihvatanja FROM idk_nd_ugovori 
			WHERE ug_token = :token
		");

		$query_get_kandidat_id -> execute(array(
			':token' => $token
		));
			
		$row_get_kandidat_id = $query_get_kandidat_id->fetch();
		
		$ug_kandidat_id 		= $row_get_kandidat_id['ug_kandidat_id'];
		$ug_datum_prihvatanja 	= date("Y-m-d", strtotime($row_get_kandidat_id['ug_datum_prihvatanja']));
		
		$array_pr_file 			= array();
		$array_pr_uplaceno 		= array();
		$array_uplatnica_file 	= array();
		
		$query_get_predracun = $db -> prepare('
			SELECT pr_file, pr_rata, pr_uplaceno
			FROM idk_predracuni
			WHERE pr_kandidat_id = :ug_kandidat_id
			AND pr_status != 0
            ORDER BY pr_rata ASC
		');
		
		$query_get_predracun -> execute(array(':ug_kandidat_id' => $ug_kandidat_id));
		
		while($row_get_predracun = $query_get_predracun -> fetch()){			
			array_push($array_pr_file, $row_get_predracun['pr_file']);
			array_push($array_pr_uplaceno, $row_get_predracun['pr_uplaceno']);
		}
		$cnt_pr = count($array_pr_file)-1;
		
		$query_get_uplatnica = $db -> prepare('
			SELECT id_dokument_nd, naziv_dokument_nd 
			FROM idk_nd_kandidata_dokumenti 
			WHERE tip_dokumenta = 2 
			AND id_kandidata_dokument_nd = :ug_kandidat_id
			AND tip_dokumenta_status = 1 
			ORDER BY id_dokument_nd ASC 

		');
		$query_get_uplatnica -> execute(array(':ug_kandidat_id' => $ug_kandidat_id));
		while($row_get_uplatnica = $query_get_uplatnica -> fetch()){
			array_push($array_uplatnica_file, $row_get_uplatnica['naziv_dokument_nd']);
		}
		$cnt_upl = count($array_uplatnica_file)-1;
		$style_button_bg = "";
		if($array_pr_uplaceno[$cnt_pr] == '1'){
			$style_button_bg = 'background-color:#97a7ac!important;';
		}
		
		
		$jezik = get_string_between($ug_file,"UGCH","-");
		
		$flag_danas = false;
		
		if($ug_datum_prihvatanja == date("Y-m-d")){
			$flag_danas = true;
		}
		if($jezik == "B"){
			$text 				= "Potpisan ugovor, predračun i uplatnicu mozete preuzeti klikom na na dugme ispod.";
			if($flag_danas){
				$text 			= "Vaš ugovor je potpisan. ".$text;
			}
			$text_ugovor 		= "Ugovor";
			$text_predracun 	= "Predračun";
			$text_uplatnica 	= "Uplatnica";				
			$text_partner_app 	= "PREUZMI PARTNER APP APLIKACIJU I ZARADI NOVAC!";
		} 
		
		else if($jezik == "D"){
			$text 				= "Sie können den unterschriebenen Vertrag, die Pro-forma-Rechnung und den Einzahlungsschein herunterladen, indem Sie auf die Schaltfläche unten klicken.";
			if($flag_danas){
				$text 			= "Ihr Vertrag wurde unterzeichnet. ".$text;
			}
			$text_ugovor 		= "Vertrag";
			$text_predracun 	= "Vorschussrechnung";
			$text_uplatnica 	= "Einzahlungsschein";
			$text_partner_app 	= "LADEN SIE DIE APP FÜR UNSERE PARTNER HERUNTER UND VERDIENEN SIE GELD";

		}
		
		else if($jezik == "S"){
			$text 				= "Potpisan ugovor, predračun i uplatnicu možete preuzeti klikom na na dugme ispod.";
			if($flag_danas){
				$text 			= "Vaš ugovor je potpisan. ".$text;
			}
			$text_ugovor 		= "Ugovor";
			$text_predracun 	= "Predračun";
			$text_uplatnica 	= "Uplatnica";
			$text_partner_app 	= "PREUZMI PARTNER APP APLIKACIJU I ZARADI NOVAC!";
		}
		include('includes/head.php');
		?>
		
		<html>
			<head>
				<meta charset="utf-8">
				<meta http-equiv="X-UA-Compatible" content="IE=edge">
				<title>
				<?php 
					getTitle(); 
				?>
				</title>
				<style>
					.container{
						justify-content: center !important;
					}
					.d-flex {
						display: -ms-flexbox!important;
						display: flex!important;
					}
					.h-100 {
						height: 100%!important;
					}
					@media only screen and (min-height: 1300px) {
						.align-items-center {
							display: flex;
						}
					}
					.align-items-center {
						-ms-flex-align: center!important;
						align-items: center!important;
						
					}
					.w-100 {
						width: 100%!important;
					}
					.panel {
						margin-bottom: 0px !important;
					}
					.fax{
						font-size: xxx-large !important;
					}
					.btn{
						width: 100%;
					}
				</style>
			</head>
			<body>
				<div id="content_public">
					<div class="container d-flex h-100">
						<div class="row align-items-center w-100">
							<div class="col-md-12">
								<div class="content_box">
									<div class="row">
										<div class="col-md-offset-2 col-md-8">
											<div class="panel panel-default">
												<div class="panel-heading">
													<h3 class="panel-title text-center">
														<img class="img-responsive" src="<?php getSiteUrl(); ?>images/Jobstep_logo_new.png" alt="" style="max-width:100px;">
													</h3>
												</div>
												<div class="panel-body">
													<div class="well well-lg text-center" style="overflow: auto!important;">
														<h4> 
															<?php echo $text; ?>
														</h4>
														<div class="row">
															<div class="col-md-4 idk_margin_top10">
																<a href="<?php echo getSiteUrlr().'files/predracuni_dipl/'.$array_pr_file[$cnt_pr]; ?>" download>
																	<button class="btn btn-info btn-lg" tabindex="-1" style="font-size:11px; <?php echo $style_button_bg;?>">
																		<i class="fa fa-file-text-o" aria-hidden="true" style = "margin-right: 10px; font-size:12px;">
																		</i> 
																		<?php echo $text_predracun." ".($cnt_pr+1).". Rate"; ?>
																	</button>
																</a>
															</div>
															<div class="col-md-4 idk_margin_top10">
																<a href="<?php echo getSiteUrlr().'files/ugovori_uplatnice_dipl/'.$array_uplatnica_file[$cnt_upl]; ?>" download>
																	<button class="btn btn-info btn-lg" tabindex="-1" style="font-size:11px; <?php echo $style_button_bg;?>">
																		<i class="fa fa-file-o" aria-hidden="true" style = "margin-right: 10px; font-size:12px;">
																		</i> 
																		<?php echo $text_uplatnica." ".($cnt_pr+1).". Rate"; ?>
																	</button>
																</a>
															</div>
															<div class="col-md-4 idk_margin_top10">
																<a href="<?php echo getSiteUrlr().'tcpdf-main/ugovori/'.$ug_file; ?>" download>
																	<button class="btn btn-info btn-lg" tabindex="-1" style="font-size:11px;">
																		<i class="fa fa-file-text" aria-hidden="true" style = "margin-right: 10px; font-size:12px;">
																		</i> 
																		<?php echo $text_ugovor;?>
																	</button>
																</a>
															</div>
														</div>
														<?php
														$cnt_pr 	= $cnt_pr  -1;
														$cnt_upl 	= $cnt_upl -1;
														while(($cnt_pr > -1 && $cnt_upl > -1) && (!is_null($array_pr_file[$cnt_pr]) || !is_null($array_uplatnica_file[$cnt_upl]))){
															$style_button_bg = "";
															if($array_pr_uplaceno[$cnt_pr] == '1'){
																$style_button_bg = 'background-color:#97a7ac;';
															}
															?>
															<div class = "row">
																<div class="col-md-4 idk_margin_top10">
																	<?php
																	if(!is_null($array_pr_file[$cnt_pr])){
																	?>
																		<a href="<?php echo getSiteUrlr().'files/predracuni_dipl/'.$array_pr_file[$cnt_pr]; ?>" download>
																			<button class="btn btn-info btn-lg"  tabindex="-1" style="font-size:9px; <?php echo $style_button_bg;?>">
																				<i class="fa fa-file-text-o" aria-hidden="true" style = "margin-right: 10px; font-size:9px;">
																				</i> 
																				 <?php echo $text_predracun." ".($cnt_pr+1).". Rate"; ?>
																			</button>
																		</a>
																	<?php
																	}
																	?>
																</div>
																<div class="col-md-4 idk_margin_top10">
																	<?php
																	if(!is_null($array_uplatnica_file[$cnt_upl])){
																	?>
																		<a href="<?php echo getSiteUrlr().'files/ugovori_uplatnice_dipl/'.$array_uplatnica_file[$cnt_upl]; ?>" download>
																			<button class="btn btn-info btn-lg" tabindex="-1" style="font-size:9px; <?php echo $style_button_bg;?>">
																				<i class="fa fa-file-o" aria-hidden="true" style = "margin-right: 10px; font-size:9px;">
																				</i> 
																				<?php echo $text_uplatnica." ".($cnt_upl+1).". Rate"; ?>
																			</button>
																		</a>
																	<?php
																	}
																	?>
																</div>
																<div class="col-md-4 idk_margin_top10">
																</div>
															</div>
																<?php
																$cnt_pr 	= $cnt_pr  -1;
																$cnt_upl 	= $cnt_upl -1;
															}
															?>
													</div>
													<div class="well well-lg text-center">
														<h4> 
															<?php echo $text_partner_app; ?>
														</h4>
														<div class="row ">
															<div class="col-md-6 idk_margin_top10">
																<a href="https://play.google.com/store/apps/details?id=com.partnerjobstep" target="_blank" tabindex="-1">
																	<button class="btn btn-default btn-lg" tabindex="-1">
																		<i class="fa fax fa-android" aria-hidden="true">
																		</i> 
																		<br><br>
																		Google Play
																	</button>
																</a>
															</div>
															<div class="col-md-6 idk_margin_top10">
																<a href="https://apps.apple.com/tt/app/jobstep-partner-app/id1498761708?ign-mpt=uo%3D2" target="_blank" tabindex="-1">
																	<button class="btn btn-default btn-lg" tabindex="-1">
																		<i class="fa fax fa-apple" aria-hidden="true">
																		</i> 
																		<br><br>
																		Apple IOs
																	</button>
																</a>
															</div>
														</div>
													</div>
													<div class="embed-responsive embed-responsive-16by9">
														<iframe class="embed-responsive-item" src="https://www.youtube.com/embed/hVd4UGPpOCg" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen=""></iframe>
														
													</div>
												</div>
												<div class="panel-footer text-center">
													<?php getCopyright(); ?>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</body>
		</html>
		
		


		<?php
	break;

	case "odbijen":

		$token = $_GET['token'];
		$jezik = $_GET['jezik'];

		$query_check_status = $db->prepare('
			SELECT ug_status, ug_razlog_odbijanja, ug_datum_odbijanja, ug_kandidat_id
			FROM idk_nd_ugovori
			WHERE ug_token = :token
		');
		$query_check_status -> execute(array(':token' => $token));
		$row_check_status = $query_check_status -> fetch();
		
		$ug_status 				= $row_check_status['ug_status'];
		$ug_razlog_odbijanja 	= $row_check_status['ug_razlog_odbijanja'];
		$ug_datum_odbijanja 	= $row_check_status['ug_datum_odbijanja'];
		$ug_kandidat_id 		= $row_check_status['ug_kandidat_id'];
		
		if($ug_status == 1 || $ug_status == 4 || ($ug_status == 3 && is_null($ug_razlog_odbijanja))){

			$datum_odbijanja = date("Y-m-d H:i:s", time());
			
			if(is_null($ug_datum_odbijanja)){
				$query_update = ", ug_datum_odbijanja = '".$datum_odbijanja."'";
			}
			else{
				$query_update = "";
			}
			$query_set_status = $db -> prepare('
				UPDATE idk_nd_ugovori 
				SET ug_status = 3'.$query_update.'
				WHERE ug_token = :token
			');
			
			$query_set_status -> execute(array(
				':token' => $token
			));
			
			if($jezik == "bs"){
				$text 				= "Molimo Vas da navedete razlog odbijanja ugovora:";
				$razlog 			=  "naziv_ro_bs";
				$text_ostalo 		= "Ostalo.";
				$text_placeholer 	= "Molimo Vas da navedete razlog odbijanja";
			} 
			
			else if($jezik == "de"){
				$text 				= "Wir bitten Sie einen Grund für die Ablehnung des Vertrags zu nennen:";
				$razlog 			= "naziv_ro_de";
				$text_ostalo 		= "Andere.";
				$text_placeholer 	= "Wir bitten Sie einen Grund für die Ablehnung des Vertrags zu nennen";
			}
			
			else if($jezik == "sr"){
				$text 				= "Molimo Vas da navedete razlog odbijanja ugovora:";
				$razlog 			= "naziv_ro_sr";
				$text_ostalo 		= "Ostalo.";
				$text_placeholer 	= "Molimo Vas da navedete razlog odbijanja";
			}
			$query_check_reminder = $db->prepare('
				SELECT reminder_id
				FROM idk_reminders
				WHERE reminder_candidate_id = :ug_kandidat_id
                AND reminder_status = 1
			');
			$query_check_reminder -> execute(array(':ug_kandidat_id' => $ug_kandidat_id));

			if($query_check_reminder -> rowCount() == 0){
				insertReminderOdbioUgovor($token);				
			}
			
			include('includes/head.php');
			
			?>
			<html>
				<head>
					<meta charset="utf-8">
					<meta http-equiv="X-UA-Compatible" content="IE=edge">
					<title>
					<?php 
						getTitle(); 
					?>
					</title>
					<style>
						.container{
							justify-content: center !important;
						}
						.d-flex {
							display: -ms-flexbox!important;
							display: flex!important;
						}
						.h-100 {
							height: 100%!important;
						}
						@media only screen and (min-height: 800px) {
							.align-items-center {
								display: flex;
							}
						}
						.align-items-center {
							-ms-flex-align: center!important;
							align-items: center!important;
						}
						.w-100 {
							width: 100%!important;
						}
						.panel {
							margin-bottom: 0px !important;
						}
						.material-checkbox-group__label {
							height: auto !important;
						}
					</style>
				</head>
				<body>
					<div id="content_public">
						<div class="container d-flex h-100">
							<div class="row align-items-center w-100">
								<div class="col-md-12">
									<div class="content_box">
										<div class="row">
											<div class="col-md-offset-2 col-md-8">
												<div class="panel panel-default">
													<div class="panel-heading">
														<h3 class="panel-title text-center">
															<img class="img-responsive" src="<?php getSiteUrl(); ?>images/Jobstep_logo_new.png" alt="" style="max-width:100px;">
														</h3>
													</div>
													<?php 
													// echo getSiteUrlr().'do_dipl.php?form=spremi_razlog';
													// exit();
													?>
													<div class="panel-body">
														<form action="<?php  echo getSiteUrlr().'ugovor_handler_new.php?action=spremi_razlog';?>" id="form_spremi_razlog" method="post" enctype="multipart/form-data"  class="form-horizontal" role="form">
															<input type="hidden" value="0" id="razlog" name="razlog">
															<div class = "form-group">
																<div class="col-sm-12">
																	<h3><?php echo $text; ?></h3>
																</div>
															</div>
															<div class = "form-group">
																<div class="col-sm-12">
																	<div class="main-container__column material-checkbox-group material-checkbox-group_default idk_margin_top10">
																	<?php 
																		$query_get_razloge = $db->prepare("
																			SELECT id_ro, ".$razlog." as razlog_tekst FROM idk_ro_usluge
																			WHERE tip_ro = 3 AND status_ro = 1 AND id_ro != 383
																		");
																		$query_get_razloge->execute();
																		while($row_get_razloge = $query_get_razloge->fetch()){
																			$razlog_value = $row_get_razloge['id_ro'];
																			$razlog_text = $row_get_razloge['razlog_tekst'];
																			echo '
																				<input type="checkbox" value="'.$razlog_value.'" name="razlog_check_group" id="razlog_check'.$razlog_value.'" class="material-checkbox razlog_check_group">
																				<label class="material-checkbox-group__label" for="razlog_check'.$razlog_value.'">'.$razlog_text.'</label>
																			';
																		}
																	?>
																		<input type="checkbox" value="383" name="razlog_check<?php echo 383;?>" id="razlog_check<?php echo 383;?>" class="material-checkbox razlog_check_group">
																		<label class="material-checkbox-group__label" for="razlog_check<?php echo 383;?>"><?php echo $text_ostalo; ?></label>
																	</div>
																</div>
																
															</div>
																<?php
																// <select class="selectpicker col-xs-12 col-lg-8" id="razlog" name="razlog" >
																// <option value="" selected disabled hidden>Odaberi</option>
																// $query_get_razloge = $db->prepare('
																	// SELECT id_ro, '.$razlog.' as razlog_tekst FROM idk_ro_usluge
																	// WHERE tip_ro = 3 AND status_ro = 1 AND id_ro != 13
																// ');
																	
																// $query_get_razloge->execute();
																
																// while($row_get_razloge = $query_get_razloge->fetch()){
																	// $razlog_value = $row_get_razloge['id_ro'];
																	// $razlog_text = $row_get_razloge['razlog_tekst'];
																	// echo '<label><input type="checkbox" value="'.$razlog_value.'" name="check_group"> '.$razlog_text.'</label><br>';
																	// //echo '<option value="'.$razlog_value.'">'.$razlog_text.'</option>';
																// }
																// //<option value="13">Ostalo</option>
																?>
																<!--<label><input type="checkbox" value="13" name="check_group"> Ostalo</label>-->
															<input type="hidden" id="token" name="token" value="<?php echo $token; ?>">
															<input type="hidden" id="jezik" name="jezik" value="<?php echo $jezik; ?>">
															<input type="hidden" id="checked_razlog" name="checked_razlog" value="">
															<div class = "form-group">
																<div class="col-sm-12">
																	<div class="materail-input-block materail-input-block_default">
																		<input class="form-control materail-input" type="text" name="komentar" id="komentar" placeholder = "<?php echo $text_placeholer; ?>" style="display:none;margin-top:15px;">
																		<span class="materail-input-block__line"></span>
																	</div>
																</div>
															</div>
															<div class="form-group">
																<div class="col-sm-12 text-center">
																	<button type="submit" id="submit_razlog" name="submit_razlog" class="btn material-btn material-btn-icon-danger material-btn_danger main-container__column" style="display:none;margin-top:15px;" form="form_spremi_razlog">
																		<i class="fa fa-plus" aria-hidden="true"></i> <span>Odbij ugovor</span>
																	</button>
																</div>
															</div>
														</form>
													</div>
													<div class="panel-footer text-center">
														<?php getCopyright(); ?>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<script>
						$('.razlog_check_group').on('click', function(){
							var checked 		= $(this).is(':checked');
							var razlog_check_id = $(this).val();
							$('#razlog').val(razlog_check_id);
							$('.razlog_check_group').prop('checked', false);
							if(checked == true){
								$(this).prop('checked', true);
								$('#checked_razlog').val(razlog_check_id);
								if(razlog_check_id == 383){
									$('#komentar').val("");
									$('#submit_razlog').fadeOut(400, function(){
										$('#komentar').fadeIn(400);
										$('#submit_razlog').attr('disabled', true);							
									});
								}
								else{
									$('#komentar').fadeOut(400,function(){							
										$('#submit_razlog').attr('disabled', false);
										$('#submit_razlog').fadeIn(400);
									});
								}
							}
							else{
								$('#submit_razlog').attr('disabled', true);
								$('#submit_razlog').fadeOut(400);
								$('#komentar').fadeOut(400);
							}
						});
						
						$('#komentar').on('keyup',function(){
							if($(this).val() == ""){
								$('#submit_razlog').attr('disabled', true);
								$('#submit_razlog').fadeOut(400);
							}
							else{
								$('#submit_razlog').attr('disabled', false);
								$('#submit_razlog').fadeIn(400);
							}
						});
					
						$('#submit_razlog').on('click', function(){
							$('#submit_razlog').fadeOut(0);
						});
					
					
					</script>
				</body>
			</html>
			
			
		<?php
		}
		else{
			echo 'Već ste donijeli odluku o prihvatanju ugovora';
		}
		
		
	break;

	case "spremi_razlog":
		$token = $_POST['token'];
		$jezik = $_POST['jezik'];
		$razlog = $_POST['razlog'];
		$komentar = $_POST['komentar'];
		if ($komentar == ""){
			$komentar = NULL;
		}
		$query_set_razlog = $db -> prepare('
			UPDATE idk_nd_ugovori 
			SET ug_razlog_odbijanja = :razlog, ug_komentar = :komentar
			WHERE ug_token = :token
		');
		$query_set_razlog -> execute(array(
			':razlog' => $razlog,
			':token' => $token,
			':komentar' => $komentar
		));
		$url = getSiteUrlr();																														
		$url = $url.'ugovor_handler_new.php?action=odbijen_razlogom&token='.$token.'&jezik='.$jezik;	
		header("Location:".$url);

	break;

	case "odbijen_razlogom":
		$token = $_GET['token'];
		$jezik = $_GET['jezik'];

		$query_get_razlog = $db->prepare('
			SELECT ug_razlog_odbijanja 
			FROM idk_nd_ugovori
			WHERE ug_token = :token
		');
		
		$query_get_razlog -> execute(array(':token' => $token));
		
		$row_get_razlog = $query_get_razlog -> fetch();
		$razlog_id = $row_get_razlog['ug_razlog_odbijanja'];
		

		
		if($jezik == "bs"){
			if($razlog_id != 387){
				$text = "Hvala za povratnu informaciju!";				
			}
			else if($razlog_id == 387){
				$text = "Hvala za povratnu informaciju! Naš tim će Vas kontaktirati u što kraćem vremenskom periodu.";
			}
			$text_partner_app = "PREUZMI PARTNER APP APLIKACIJU I ZARADI NOVAC!";
		} 
		else if($jezik == "de"){
			if($razlog_id != 387){
				$text = "Danke für Ihr Feedback.";				
			}
			else if($razlog_id == 387){
				$text = "Danke für Ihr Feedback. Unser Team wird sich so schnell wie möglich mit Ihnen in Verbindung setzen.";
			}
			$text_partner_app = "LADEN SIE DIE APP FÜR UNSERE PARTNER HERUNTER UND VERDIENEN SIE GELD";

		}
		else if($jezik == "sr"){
			if($razlog_id != 387){
				$text = "Hvala za povratnu informaciju!";				
			}
			else if($razlog_id == 387){
				$text = "Hvala za povratnu informaciju! Naš tim će Vas kontaktirati u što kraćem vremenskom periodu.";
			}
			$text_partner_app = "PREUZMI PARTNER APP APLIKACIJU I ZARADI NOVAC!";
		}
		include('includes/head.php');
		?>
		
		<html>
			<head>
				<meta charset="utf-8">
				<meta http-equiv="X-UA-Compatible" content="IE=edge">
				<title>
				<?php 
					getTitle(); 
				?>
				</title>
				<style>
					.container{
						justify-content: center !important;
					}
					.d-flex {
						display: -ms-flexbox!important;
						display: flex!important;
					}
					.h-100 {
						height: 100%!important;
					}
					@media only screen and (min-height: 1000px) {
						.align-items-center {
							display: flex;
						}
					}
					.align-items-center {
						-ms-flex-align: center!important;
						align-items: center!important;
					}
					.w-100 {
						width: 100%!important;
					}
					.panel {
						margin-bottom: 0px !important;
					}
					.fa{
						font-size: xxx-large !important;
					}
					.btn{
						width: 100%;
					}
				</style>
			</head>
			<body>
				<div id="content_public">
					<div class="container d-flex h-100">
						<div class="row align-items-center w-100">
							<div class="col-md-12">
								<div class="content_box">
									<div class="row">
										<div class="col-md-offset-2 col-md-8">
											<div class="panel panel-default">
												<div class="panel-heading">
													<h3 class="panel-title text-center">
														<img class="img-responsive" src="<?php getSiteUrl(); ?>images/Jobstep_logo_new.png" alt="" style="max-width:100px;">
													</h3>
												</div>
												<div class="panel-body">
													<div class="well well-lg text-center">
														<h4> 
															<?php echo $text; ?> 
														</h4>
													</div>
													<div class="well well-lg text-center">
														<h4> 
															<?php echo $text_partner_app; ?>
														</h4>
														<div class="row ">
															<div class="col-md-6 idk_margin_top10">
																<a href="https://play.google.com/store/apps/details?id=com.partnerjobstep" target="_blank" tabindex="-1">
																	<button class="btn btn-default btn-lg" tabindex="-1">
																		<i class="fa fa-android" aria-hidden="true">
																		</i> 
																		<br><br>
																		Google Play
																	</button>
																</a>
															</div>
															<div class="col-md-6 idk_margin_top10">
																<a href="https://apps.apple.com/tt/app/jobstep-partner-app/id1498761708?ign-mpt=uo%3D2" target="_blank" tabindex="-1">
																	<button class="btn btn-default btn-lg" tabindex="-1">
																		<i class="fa fa-apple" aria-hidden="true">
																		</i> 
																		<br><br>
																		Apple IOs
																	</button>
																</a>
															</div>
														</div>
													</div>
													<div class="embed-responsive embed-responsive-16by9">
														<iframe class="embed-responsive-item" src="https://www.youtube.com/embed/hVd4UGPpOCg" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen=""></iframe>
														
													</div>
												</div>
												<div class="panel-footer text-center">
													<?php getCopyright(); ?>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</body>
		</html>
		


		<?php
	break;

	case "arhiviran":
		$jezik = $_GET['jezik'];
		
		$text = "";
		if($jezik == "bs"){
			$text = "Vaš link više nije validan";
		} 
		
		else if($jezik == "de"){
			$text = "Ihr Link ist nicht mehr gültig.";
		}
		
		else if($jezik == "sr"){
			$text = "Vaš link više nije validan";
		}
		include('includes/head.php');
		?>
		<html>
			<head>
				<meta charset="utf-8">
				<meta http-equiv="X-UA-Compatible" content="IE=edge">
				<title>
				<?php 
					getTitle(); 
				?>
				</title>
				<style>
					.col-xs-12 > .img-responsive{
						max-width: 90px !important;
						margin-top: 10px !important;
					}
					.alert > h2{
						margin-top: 0px !important;
					}
				</style>
			</head>
			<body>				
				<div class="row"> 
					<div class="col-sm-8 col-sm-offset-2 idk_margin_top10 text-center">
						<div style="box-shadow: 0 4px 10px #00000080; margin-top: 20px;" class="alert material-alert material-alert_secondary">
							<h4> <?php echo $text; ?> </h4>
						</div>
					</div>
				</div>
			</body>
		</html>
		


		<?php
		
	break;


}

?>