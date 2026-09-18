<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	include("includes/functions.php");
	include("includes/common.php");

	$getEmployeeStatus  = explode( ',' , getEmployeeStatus());
	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: employees?page=list");
	}


?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Partneri | <?php getTitle(); ?></title>

	<?php include('includes/head.php');
	
if (in_array($getUserIp, $getIpWhiteList)){
	?>

</head>
<body>
	<header>
		<?php include('header.php'); ?>
	</header>
	<div id="sidebar">
		<?php include('menu.php');  ?>
	</div>
	<div id="content">
		<div class="container-fluid">
		<?php
			switch ($page){
				case "list":

		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-users idk_color_green" aria-hidden="true"></i> Partneri</h1>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<style>
			.material-label_xs {
				border-radius: 1px;
			}
			</style>
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<div class="row">
							<div class="col-xs-12">
								<script type="text/javascript">
									$(document).ready(function() {
										$('#idk_table').DataTable({
											
											responsive: true,
											"pageLength": 10,
											"processing": true,
											"serverSide": true,
											"order": [[ 0, "desc" ]],
											"aoColumns": [
													null,
													null,
													null,
													null,
													null,
													null,
													null,
													null,
													null,
													null,
													null,
													// null
												],
											"ajax":{
												url :"<?php echo getSiteUrlr(); ?>serverside_partner.php?page=list",
												type: "POST",
												error: function(data){
													$(".list-grid-error").html(""); 
													$("#list-grid_processing").css("display","none");
											
												},
											}	
										});
									} );
								</script>

									<table id="idk_table" class="display" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th>ID</th>
												<th>Ime i prezime</th>
												<th>Telefon</th>
												<th >Država</th>
												<th>E-mail</th>
												<th>Status</th>
												<th>Registracija</th>
												<th>Pregledi</th>
												<th>PK</th>
												<th>DIPL</th>
												<!-- <th>Install</th>	-->			
												<th></th>
											</tr>
										</thead>
										
									</table>

							</div>
						</div>
					</div>
				</div>
			</div>
		<?php
				break;

				case "list_dvag":
		?>
				<div class="row">
					<div class="col-xs-8">
						<h1><i class="fa fa-users idk_color_green" aria-hidden="true"></i> Partneri</h1>
					</div>
					<div class="col-xs-12">
						<hr />
					</div>
				</div>
				<style>
				.material-label_xs {
					border-radius: 1px;
				}
				</style>
				<div class="row">
					<div class="col-md-12">
						<div class="content_box">
							<div class="row">
								<div class="col-xs-12">
									<script type="text/javascript">
										$(document).ready(function() {
											$('#idk_table').DataTable({
												
												responsive: true,
												"pageLength": 10,
												"processing": true,
												"serverSide": true,
												"order": [[ 0, "desc" ]],
												"aoColumns": [
														null,
														null,
														null,
														null,
														null,
														null,
														null,
														null,
														null,
														null,
														// null
													],
												"ajax":{
													url :"<?php echo getSiteUrlr(); ?>serverside_partner.php?page=list_dvag",
													type: "POST",
													error: function(data){
														$(".list-grid-error").html(""); 
														$("#list-grid_processing").css("display","none");
												
													},
												}	
											});
										} );
									</script>
	
										<table id="idk_table" class="display" cellspacing="0" width="100%">
											<thead>
												<tr>
													<th class="text-center">ID</th>
													<th class="text-center">Ime i prezime</th>
													<th class="text-center">Telefon</th>
													<th class="text-center">E-mail</th>
													<th class="text-center">Registracija</th>
													<th class="text-center">Leadovi</th>
													<th class="text-center">Pregledi</th>
													<th class="text-center">Preporučeni kandidati</th>		
													<th class="text-center">Preporučene kompanije</th>		
													<th></th>
												</tr>
											</thead>
											
										</table>
	
								</div>
							</div>
						</div>
					</div>
				</div>
		<?php
				break;
				
				case "open":

					$jp_id = intval($_GET['id']);
					
					
					
					$query = $db->prepare("
						SELECT *
						FROM  idk_jobstep_partners
						WHERE jp_id = :jp_id");

					$query->execute(array(
								':jp_id' => $jp_id));

					$row = $query->fetch();
						$jp_imeprezime = $row['jp_imeprezime'];
						
						$jp_email = $row['jp_email'];
						$jp_brtelefona = $row['jp_brtelefona'];
						$jp_provider = $row['jp_provider'];
						$jp_drzava = $row['jp_drzava'];
						$jp_grad = $row['jp_grad'];
						$jp_postanskibroj = $row['jp_postanskibroj'];
						$jp_ulica = $row['jp_ulica'];
						$jp_drzava = $row['jp_drzava'];
						$jp_provider = $row['jp_provider'];
						$jp_socialid = $row['jp_socialid'];
						$jp_placanjeimeprezime = $row['jp_placanjeimeprezime'];
						$jp_posta = $row['jp_posta'];
						$jp_racun = $row['jp_racun'];
						$jp_iban = $row['jp_iban'];
						$jp_swift = $row['jp_swift'];
						$jp_position_place =  $row['jp_position'];
						$jp_preporuka_id =  $row['jp_preporuka_id'];
						$jp_register_date = date('H:i:s d.m.Y', strtotime($row['jp_register_date']));
						$jp_register_formated = date('Y-m-d H:i:s', strtotime($row['jp_register_date']));


						if(($row['jp_social_imgurl'] == NULL) OR ($row['jp_social_imgurl'] == '')){
							$jp_social_imgurl = getSiteUrlr()."files/kandidati/nonekandidati.jpg";
						}else{
							$jp_social_imgurl = $row['jp_social_imgurl'];
						}

						if($row['jp_position'] == 1){
							$jp_position = "Junior Partner";
						}elseif($row['jp_position'] == 2){
							$jp_position = "Partner";
						}elseif($row['jp_position'] == 3){
							$jp_position = "Senior Partner";
						}elseif($row['jp_position'] == 4){
							$jp_position = "Agency Partner";
						}elseif($row['jp_position'] == 5){
							$jp_position = "Superearner";
						}
	
	
	
	
						//BROJ DANA
						$date_current = date("Y-m-d H:i:s");
						$date_count = strtotime($date_current) - strtotime($jp_register_formated);
						$date_count_formated = floor($date_count/86400);
						
						//BROJ PREGLEDA
						$query_pregled = $db->prepare("
								SELECT SUM(jpp_pregledi_count) as jpp_pregledi_count
								FROM idk_jobstep_partners_pregledi
								WHERE jpp_partnerid = :jpp_partnerid");
									
						$query_pregled->execute(array(
										':jpp_partnerid' => $jp_id));  
						
						$row = $query_pregled->fetch();

						$jpp_pregledi_count = intval($row['jpp_pregledi_count']);
						
						
					
						$days = $date_count_formated;
						// JUNIOR U PARTNERA
						$junior_days_percentage = ($days/90)*100;
						
						// DANI
						if($junior_days_percentage < 100){
							$junior_days_percentage = ($days/90)*100;
							$junior_missing_days = 90 - $days;
						}else{
							$junior_days_percentage = 100; 
							$junior_missing_days = 0;
						}
						//PARTNERI
						$partner_query = $db->prepare("
							SELECT jp_id
							FROM idk_jobstep_partners
							WHERE jp_preporuka_id = :jp_preporuka_id");
							
						$partner_query->execute(array(
									':jp_preporuka_id' => $jp_id));
						
									
						$partner_counter = $partner_query->rowCount();
						
						if($partner_counter <= 5){
							$junior_partner_percentage = ($partner_counter/5)*100;  
							$junior_missing_partners = 5 - $partner_counter;
						}else{
							$junior_partner_percentage = 100;  
							$junior_missing_partners = 0;
						}
						
						$full_junior_percentage = ($junior_partner_percentage + $junior_days_percentage)/2;
						$full_junior_percentage = round($full_junior_percentage);
						
						// DANI U SENIORA
						$senior_days_percentage = ($days/180)*100;
						if($senior_days_percentage < 100){ 
							$senior_days_percentage = ($days/180)*100; 
							$senior_missing_days = 180 - $days;
						}else{
							$senior_days_percentage = 100; 
							$senior_missing_days = 0;
						}
						
						
						// ZAPOSLENIK U SENIORA
						$partner_query = $db->prepare("
							SELECT *
							FROM idk_kandidati
							WHERE kandidat_partnerid = :kandidat_partnerid AND kandidat_status_prijave = :kandidat_status_prijave");
							
						$partner_query->execute(array(
									':kandidat_partnerid' => $jp_id,
									':kandidat_status_prijave' => 4));
									
						$partner_counter = $partner_query->rowCount();
						if($partner_counter > 0){
							$partner_percentage = 100;
							$senior_missing_zaposlenik = 0;
						}else{
							$partner_percentage = 0;
							$senior_missing_zaposlenik = 1;
						}
						
						$full_senior_percentage = ($partner_percentage + $senior_days_percentage)/2;
						$full_senior_percentage = round($full_senior_percentage);
						
						if($full_senior_percentage > 99){ $completed_senior_percentage = '100'; }else{	 $completed_senior_percentage = '0'; }
						if($jp_position_place == '3'){ $completed_senior_percentage = '100'; }
							
						
						
						// $jp_promote_junior_days = date('Y-m-d', strtotime($row["jp_register_date"] . " +90 days"));
						// $datediff = $trenutno_vrijeme - $jp_promote_junior_days;
						// $vrijeme_do_promocije = round($datediff / (60 * 60 * 24));
						// var_dump($vrijeme_do_promocije);
						// exit();
																
						if($jp_position_place == 1){
							
							$vrijeme_do_promocije = $junior_missing_days;
							$kandidata_do_promocije = $junior_missing_partners;
							
						}else if($jp_position_place == 2){
							
							$vrijeme_do_promocije = $senior_missing_days;
							$kandidata_do_promocije = $senior_missing_zaposlenik;
							
						}
						else $vrijeme_do_promocije = false;
						
						
						
						
						
						
			?>
			<div class="row">
				<div class="col-xs-8">
					<h1><a href="#"><img class="idk_profile_img" src="<?php echo $jp_social_imgurl; ?>" /></a> <?php echo $jp_imeprezime; ?> </h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>partners/list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<div class="row idk_employee_info">
							<!------------------------------------------------------
										OSNOVNE INFORMACIJE O PARTNERU
							------------------------------------------------------>
							<div class="col-md-6">
								<div class="row">
									<div class="col-sm-9">
										<h5>Osnovne informacije</h5>
									</div>
								</div>

								<div class="row">
									<strong class="col-sm-4 text-right">Ime:</strong>
									<div class="col-sm-8"><?php echo $jp_imeprezime; ?></div>
								</div>
								<div class="row">
									<strong class="col-sm-4 text-right">Pozicija:</strong>
									<div class="col-sm-8"><?php echo $jp_position; ?></div>
								</div>
								<div class="row">
									<strong class="col-sm-4 text-right">Br. telefona:</strong>
									<div class="col-sm-8"><?php echo $jp_brtelefona; ?></div>
								</div>
								<div class="row">
									<strong class="col-sm-4 text-right">Email:</strong>
									<div class="col-sm-8"><?php echo $jp_email; ?></div>
								</div>
								<div class="row">
									<strong class="col-sm-4 text-right">Datum registriranja:</strong>
									<div class="col-sm-8"><span class="label label-info material-label material-label_info material-label_xs main-container__column"><?php echo $jp_register_date; ?></span></div>
								</div>
								
								<div class="row">
									<strong class="col-sm-4 text-right">Država:</strong>
									<div class="col-sm-8"><?php echo $jp_drzava; ?></div>
								</div>
								
								<div class="row">
									<strong class="col-sm-4 text-right">Grad:</strong>
									<div class="col-sm-8"><?php echo $jp_grad; ?></div>
								</div>
								
								<div class="row">
									<strong class="col-sm-4 text-right">Ulica:</strong>
									<div class="col-sm-8"><?php echo $jp_ulica; ?> </div>
								</div>
								
								<div class="row">
									<strong class="col-sm-4 text-right">Pbroj:</strong>
									<div class="col-sm-8"> <?php echo $jp_postanskibroj; ?> </div>
								</div>
								<div class="row">
									<strong class="col-sm-4 text-right">Provider:</strong>
									<div class="col-sm-8"><?php echo $jp_provider; ?></div>
								</div>
								<div class="row">
									<strong class="col-sm-4 text-right">Social FB/Gmail ID:</strong>
									<div class="col-sm-8"><?php echo $jp_socialid; ?></div>
								</div>									
								
								<?php if(!empty($jp_preporuka_id)){ ?>
								<div class="row">
									<strong class="col-sm-4 text-right text-danger">Partner preporučen od:</strong>
									<div class="col-sm-8"><a href="<?php getSiteUrl(); ?>partners/<?php echo $jp_preporuka_id; ?>" class="text-danger"><?php getInfoPartnerPreporuka($jp_preporuka_id); ?></a></div>
								</div>		
								<?php } ?>
								
							</div>
							<!------------------------------------------------------
										PLACANJE PARTNERA
							------------------------------------------------------>
							<div class="col-md-6">
								<div class="row">
									<div class="col-sm-9">
										<h5>Plaćanje</h5>
									</div>
								</div>
								<div class="row">
									<strong class="col-sm-4 text-right">Ime i prezime:</strong>
									<div class="col-sm-8"><?php echo $jp_placanjeimeprezime; ?></div>
								</div>
								<div class="row">
									<strong class="col-sm-4 text-right">Pošta:</strong>
									<div class="col-sm-8">
										<?php if($jp_posta == '1'){ ?>	 
											<i class="fa fa-check" aria-hidden="true" style="color:green;font-size: 16px;"></i>		
										<?php }else{ ?>	
											<i class="fa fa-times" aria-hidden="true" style="color:red;font-size: 16px;"></i>
										<?php } ?>												
									</div>
								</div>			
								
								<div class="row">
									<strong class="col-sm-4 text-right">Račun:</strong>
									<div class="col-sm-8">
										<?php if($jp_racun == '1'){ ?>	 
											<i class="fa fa-check" aria-hidden="true" style="color:green;font-size: 16px;"></i>		
										<?php }else{ ?>	
											<i class="fa fa-times" aria-hidden="true" style="color:red;font-size: 16px;"></i>
										<?php } ?>	
									</div>
								</div>							

								
								<div class="row">
									<strong class="col-sm-4 text-right">IBAN:</strong>
									<div class="col-sm-8"><?php echo $jp_iban; ?></div>
								</div>								
								<div class="row">
									<strong class="col-sm-4 text-right">SWIFT:</strong>
									<div class="col-sm-8"><?php echo $jp_swift; ?></div>
								</div>		
							</div>
				 
														
							<!------------------------------------------------------
								Preporučeni kanididati
							------------------------------------------------------>		
							<div class="col-md-12" style="margin-top:50px;">
								<div class="row">
									<div class="col-sm-9">
										<h5>Preporučeni kanididati </h5>
									</div>
								</div>
															
								<div class="row">
									<div class="col-sm-offset-1 col-sm-10">
											<script type="text/javascript">
												$(document).ready(function() {
													$('#idk_partnerss_kandidates').DataTable({

														responsive: true,

														"order": [[ 0, "desc" ]],
														"aoColumns": [
																null,
																null,
																null,
																null,
																null,
																null,
																null,
																null,
																null
															]
													});
												} );
											</script>

											<table id="idk_partnerss_kandidates" class="display" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th>ID</th>
														<th></th>
														<th>Ime i prezime</th>
														<th>Telefon</th>
														<th>E-mail</th>
														<th>Prijava na</th>
														<th>Status</th>
														<th>Registracija</th>
														<th >Provizija</th>
													</tr>
												</thead>
												<tbody>
													<?php
														//SVI KANDIDATI
														$query_kandidat = $db->prepare("
																	SELECT kandidat_id, kandidat_prijava_na, kandidat_partner_status , kandidat_ime, kandidat_prezime , kandidat_slika, kandidat_mobitel, kandidat_email, kandidat_datetime, kandidat_drzava, kandidat_status_prijave
																	FROM idk_kandidati
																	WHERE kandidat_partnerid = :kandidat_partnerid");
																	
														$query_kandidat->execute(array(
																		':kandidat_partnerid' => $jp_id));
																		
														$trenutno_vrijeme = date('Y-m-d H:i:s',time());					
														$partner_counter = $partner_query->rowCount();
														$money_sum = 0;
														$uplacene_provizije = getPartnerIzvrseneUplateR($jp_id);		
														while($query_kandidat_row = $query_kandidat->fetch()){
															
															// // POKUPI KANDIDAT ID 
															$kandidat_id = $query_kandidat_row["kandidat_id"];  
															$kandidat_ime = $query_kandidat_row["kandidat_ime"];  
															$kandidat_prezime = $query_kandidat_row["kandidat_prezime"];  
															$kandidat_prijava_na = $query_kandidat_row["kandidat_prijava_na"]; 
															$kandidat_mobitel = $query_kandidat_row["kandidat_mobitel"]; 
															$kandidat_email = $query_kandidat_row["kandidat_email"]; 
															$kandidat_drzava = $query_kandidat_row["kandidat_drzava"]; 
															$kandidat_status_prijave = $query_kandidat_row["kandidat_status_prijave"]; 
															$kandidat_datetime = date('H:i d.m.Y', strtotime($query_kandidat_row['kandidat_datetime']));
															
															
															// SLIKA
															if($query_kandidat_row['kandidat_slika'] == "none"){
																$kandidat_slika = "nonekandidati.jpg";
															}else{
																$kandidat_slika = $query_kandidat_row['kandidat_slika'];
															}
																
															if($query_kandidat_row['kandidat_partner_status'] == '1'){
																$partner_procentage = 0.8;
															}elseif($query_kandidat_row['kandidat_partner_status'] == '2'){
																$partner_procentage = 0.9;
																// $jp_promote_partner_days = date('Y-m-d', strtotime($row["jp_register_date"] . " +180 days"));	// BROJ DANA 180 - (180 DANA ZA PROMOCIJU PARTNERA IZ PARTNER(90%) U SENIOR(100%))
																// $datediff = $trenutno_vrijeme - $jp_promote_partner_days;
																// $vrijeme_do_promocije = round($datediff / (60 * 60 * 24));
																
																// var_dump($vrijeme_do_promocije);
															}elseif($query_kandidat_row['kandidat_partner_status'] == '3'){
																$partner_procentage = 1;
															}elseif($query_kandidat_row['kandidat_partner_status'] == '4'){
																$partner_procentage = 1.1;
															}elseif($query_kandidat_row['kandidat_partner_status'] == '5'){
																$partner_procentage = 1.25;
															}
															
															
															if($kandidat_status_prijave == ""){
																$status_name = "U Čekanju";
															}else{
																$query_projekti = $db->prepare("
																	SELECT status_id, status_naziv
																	FROM idk_kandidat_status_prijave
																	WHERE status_id = :status_id");
						
																$query_projekti->execute(array('status_id' => $kandidat_status_prijave));
																$projekti = $query_projekti->fetch();
																$status_name = $projekti['status_naziv'];
															}
															
															
															// POKUPI KOJEM PROJEKTU PRIPADA KANDIDATI
															$query_kandidat_project = $db->prepare("
																		SELECT pk_projectid
																		FROM idk_project_kandidati
																		WHERE pk_kandidatid = :pk_kandidatid
																		ORDER BY pk_id DESC");
																		
															$query_kandidat_project->execute(array(
																			':pk_kandidatid' => $kandidat_id
																			));
																			
															$all_rows = $query_kandidat_project->fetchAll(); 
															$nalozi_kandidata = array();
															foreach($all_rows as $row_project){
																
																$projekat = intval($row_project['pk_projectid']);
																if($projekat != 0){
																	// POKUPI KOJEM NALOGU PRIPADA KANDIDAT PREKO PROJEKTA 
																	$query_kandidat_nalog = $db->prepare("
																				SELECT project_nalogid
																				FROM idk_projects
																				WHERE project_id = :project_id"); 
																				
																	$query_kandidat_nalog->execute(array(
																					':project_id' => $projekat
																					));
																					
																	$row_nalog = $query_kandidat_nalog->fetch();
																	
																	$nalog = intval($row_nalog['project_nalogid']);
																	$nalozi_kandidata[] = $nalog;
																	
																}
															}
															$nalozi_kandidata = array_unique($nalozi_kandidata);
															foreach($nalozi_kandidata as $nalog){
																
																	if($nalog != 0){
																
																	// POKUPI INFORMACIJE O NALOGU
																	$query_kandidat_nalog_info = $db->prepare("
																				SELECT nalog_partner_provizija, nalog_naziv
																				FROM idk_nalozi
																				WHERE nalog_id = :nalog_id");
																				
																	$query_kandidat_nalog_info->execute(array(
																					':nalog_id' => $nalog
																					));
																				
																		$row_provizija = $query_kandidat_nalog_info->fetch();
																		
																		$nalog_naziv = $row_provizija['nalog_naziv'];
																		$provizija = intval($row_provizija['nalog_partner_provizija'])*$partner_procentage;
																		$money_sum += $provizija;
																	}
																?>
																	<tr>
																		<td><?php echo $kandidat_id; ?></td>
																		<td class="text-center"><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>"><img class="idk_profile_img" src="<?php getSiteURL(); ?>files/kandidati/<?php echo $kandidat_slika; ?>"></a></td>
																		<td><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>"><?php echo $kandidat_ime; ?> <?php echo $kandidat_prezime; ?> </a></td>
																		<td><a href="tel:<?php echo $kandidat_mobitel; ?>"><?php echo $kandidat_mobitel; ?></a></td>
																		<td><a href="mailto:<?php echo $kandidat_email; ?>"><?php echo $kandidat_email; ?></a></td>
																		<td><a href="<?php getSiteURL(); ?>nalozi?page=open&id=<?php echo $nalog; ?>"><span class="label label-success material-label material-label_success material-label_xs main-container__column"><?php echo $nalog_naziv; ?></span></a></td>														
																		<td><span class="label label-success material-label material-label_success material-label_xs main-container__column"><?php echo $status_name; ?></span></td>
																		<td><span class="label label-info material-label material-label_info material-label_xs main-container__column"><?php echo $kandidat_datetime; ?></span></td>
																		<td class="text-center"><span class="label label-info material-label material-label_success material-label_xs main-container__column"><?php echo $provizija; ?> €</span></td>
																	</tr> 
													<?php
															}
													
													} ?>
												</tbody>
											</table>
										</div>
								</div>	
								
								
							</div>
							
							<!------------------------------------------------------
								POD PARTNERI
							------------------------------------------------------>		
							<div class="col-md-12" style="margin-top:50px;">
								<div class="row">
									<div class="col-sm-9">
										<h5>Podpartneri</h5>
									</div>
								</div>
															
								<div class="row">
									<div class="col-sm-offset-1 col-sm-10">
											<script type="text/javascript">
												$(document).ready(function() {
													$('#idk_partnerss').DataTable({

														responsive: true,

														"order": [[ 0, "desc" ]],
														"aoColumns": [
																null,
																null,
																null,
																null,
																null,
																null,
																null,
																null
															]
													});
												} );
											</script>
											<table id="idk_partnerss" class="display" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th>ID</th>
														<th></th>
														<th>Ime i prezime</th>
														<th>Telefon</th>
														<th >Država</th>
														<th>E-mail</th>
														<th>Status</th>
														<th>Registracija</th>
													</tr>
												</thead>
												<tbody>
													<?php

														$query = $db->prepare("
																		SELECT jp_id, jp_imeprezime, jp_email, jp_brtelefona, jp_social_imgurl, jp_provider, jp_drzava, jp_position, jp_register_date
																		FROM  idk_jobstep_partners
																		WHERE jp_preporuka_id = :jp_preporuka_id AND jp_confirmedaccount = 1
																		ORDER BY jp_id DESC");

														$query->execute(array(':jp_preporuka_id'  => $jp_id));
														

														while($row = $query->fetch()){

															$jp_idd = $row['jp_id'];
															$jp_imeprezime = $row['jp_imeprezime'];
															$jp_email = $row['jp_email'];
															$jp_brtelefona = $row['jp_brtelefona'];
															$jp_provider = $row['jp_provider'];
															$jp_drzava = $row['jp_drzava'];
															$jp_register_date = date('H:i:s d.m.Y.', strtotime($row['jp_register_date']));


															if(($row['jp_social_imgurl'] == NULL) OR ($row['jp_social_imgurl'] == '')){
																$jp_social_imgurl = getSiteUrlr()."files/kandidati/nonekandidati.jpg";
															}else{
																$jp_social_imgurl = $row['jp_social_imgurl'];
															}

															if($row['jp_position'] == 1){
																$jp_position = "Junior Partner";
															}elseif($row['jp_position'] == 2){
																$jp_position = "Partner";
															}elseif($row['jp_position'] == 3){
																$jp_position = "Senior Partner";
															}elseif($row['jp_position'] == 4){
																$jp_position = "Agency Partner";
															}elseif($row['jp_position'] == 5){
																$jp_position = "Superearner";
															}
													?>
													<tr>
														<td><?php echo $jp_idd; ?></td>
														<td class="text-center"><a href="<?php getSiteURL(); ?>partners/<?php echo $jp_idd; ?>"><img class="idk_profile_img" src="<?php echo $jp_social_imgurl; ?>"></a></td>
														<td><a href="<?php getSiteURL(); ?>partners/<?php echo $jp_idd; ?>"><?php echo $jp_imeprezime; ?> </a></td>
														<td><a href="tel:<?php echo $jp_brtelefona; ?>"><?php echo $jp_brtelefona; ?></a></td>
														<td class="text-center" ><?php echo $jp_drzava; ?></td>
														<td><a href="mailto:<?php echo $jp_email; ?>"><?php echo $jp_email; ?></a></td>
														<td><span class="label label-success material-label material-label_success material-label_xs main-container__column"><?php echo $jp_position_subpartner; ?></span></td>
														<td><span class="label label-info material-label material-label_info material-label_xs main-container__column"><?php echo $jp_register_date; ?></span></td>
													</tr>
													<?php } ?>
												</tbody>
											</table>
										</div>
								</div>	
								
								
							</div>			
							
							<!------------------------------------------------------
								DIPL
							------------------------------------------------------>		
							<div class="col-md-12" style="margin-top:50px;">
								<div class="row">
									<div class="col-sm-9">
										<h5>DIPL</h5>
									</div>
								</div>
															
								<div class="row">
									<div class="col-sm-offset-1 col-sm-10">
											<script type="text/javascript">
												$(document).ready(function() {
													$('#idk_partnerss_dipl').DataTable({

														responsive: true,

														"order": [[ 0, "desc" ]],
														"aoColumns": [
																null,
																null,
																null,
																null,
																null,
																null,
																null
															]
													});
												} );
											</script>
											<table id="idk_partnerss_dipl" class="display" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th class="text-center">#ID</th>
														<th>Ime i Prezime</th>
														<th>Telefon</th>
														<th>Email</th>
														<th class="text-center">Status</th>
														<th class="text-center">Vrijeme kreiranja</th>
														<th class="text-center">Menadžer</th>
													</tr>
												</thead>
												<tbody>
													<?php

														//SVI DIPL
														$query_DIPL = $db->prepare("
																	SELECT id_broj_nd_kandidata, ime_nd_kandidata, prezime_nd_kandidata, mobilni_nd_kandidata, email_nd_kandidata, vrijeme_kreiranja_nd_kandidata, dodao_zaposlenik_nd_kandidata, status_nd_kandidata, pstatus_nd_kandidata, povijest_nd_kandidata, povijest_vrsta_nd_kandidata, zaduzen_zaposlenik_nd_kandidata, partner_nd_status
																	FROM idk_nd_kandidata
																	WHERE kandidat_idd = :kandidat_idd AND povijest_nd_kandidata = :povijest_nd_kandidata");
																	
														$query_DIPL->execute(array(
																		':kandidat_idd' => $jp_id,
																		':povijest_nd_kandidata' => 3));
																		
														// MOGUCA ISPLATA
														while($row_dipl = $query_DIPL->fetch()){
															$dipl_price = 25;
															$id_broj_nd_kandidata = $row_dipl["id_broj_nd_kandidata"]; 
															$ime_nd_kandidata_ispis = $row_dipl["ime_nd_kandidata"]; 
															$prezime_nd_kandidata_ispis = $row_dipl["prezime_nd_kandidata"];
															$mobilni_nd_kandidata_ispis = $row_dipl['mobilni_nd_kandidata'];
															$email_nd_kandidata_ispis = $row_dipl['email_nd_kandidata'];
															$vrijeme_kreiranja_nd_kandidata_ispis = date('d.m.Y H:i', strtotime($row_dipl['vrijeme_kreiranja_nd_kandidata']));
															$dodao_zaposlenik_nd_kandidata_ispis = getZaposlenikimeR($row_dipl['dodao_zaposlenik_nd_kandidata']);
															$zaduzen_zaposlenik_nd_kandidata_ispis = getZaposlenikimeR($row_dipl['zaduzen_zaposlenik_nd_kandidata']);
															$status_nd_kandidata_ispis = $row_dipl['status_nd_kandidata'];
															$pstatus_nd_kandidata_ispis = $row_dipl['pstatus_nd_kandidata'];
															$povijest_nd_kandidata_ispis = $row_dipl['povijest_nd_kandidata'];
															$povijest_vrsta_nd_kandidata_ispis = $row_dipl['povijest_vrsta_nd_kandidata'];		
															
															if($status_nd_kandidata_ispis == 1){
																if($pstatus_nd_kandidata_ispis == 1){
																	$pstatus_nd_kandidata_ispis1 = 'Lead';
																	$pstatus_nd_kandidata_style1 = 'background-color: #839098;';
																}
																else if($pstatus_nd_kandidata_ispis == 2){
																	$pstatus_nd_kandidata_ispis1 = 'Neuspješan Kontakt 3';
																	$pstatus_nd_kandidata_style1 = 'background-color: #00fb53;';
																}
																else if($pstatus_nd_kandidata_ispis == 3){
																	$pstatus_nd_kandidata_ispis1 = 'Zainteresiran Lead';
																	$pstatus_nd_kandidata_style1 = 'background-color: #0E6973;';
																}
																else if($pstatus_nd_kandidata_ispis == 4){
																	$pstatus_nd_kandidata_ispis1 = 'Nezainteresiran Lead';
																	$pstatus_nd_kandidata_style1 = 'background-color: #BF214B;';
																}
																else if($pstatus_nd_kandidata_ispis == 5){
																	$pstatus_nd_kandidata_ispis1 = 'U obradi Lead';
																	$pstatus_nd_kandidata_style1 = 'background-color: #c79cff;';
																}
																else if($pstatus_nd_kandidata_ispis == 6){
																	$pstatus_nd_kandidata_ispis1 = 'Neuspješan Kontakt 1';
																	$pstatus_nd_kandidata_style1 = 'background-color: #00fafb;';
																}
																$status_nd_kandidata_ispis1 = '<span style = "'.$pstatus_nd_kandidata_style1.' color: white" class="label label-default material-label material-label_default main-container__column text-left">'.$pstatus_nd_kandidata_ispis1.'</span>';
															}
															else if($status_nd_kandidata_ispis == 2){
																$status_nd_kandidata_ispis1 = '<span  style = "background-color: #f2e42e;" class="label label-default material-label material-label_default main-container__column text-left">Prikupljanje dokumentacije</span>';
															}
															else if($status_nd_kandidata_ispis == 3){ 
																$status_nd_kandidata_ispis1 = '<span class="label label-primary material-label material-label_primary main-container__column text-left">Poslana pošta</span>';
															}
															else if($status_nd_kandidata_ispis == 4){
																$status_nd_kandidata_ispis1 = '<span class="label label-info material-label material-label_info main-container__column text-left">U obradi</span>';
															}
															else if($status_nd_kandidata_ispis == 5){
																$status_nd_kandidata_ispis1 = '<span class="label label-warning material-label material-label_warning main-container__column text-left">Dopuna dokumentacije</span>';
															}
															else if($status_nd_kandidata_ispis == 6){
																$status_nd_kandidata_ispis1 = '<span class="label label-success material-label material-label_success main-container__column text-left">Završen</span>';
															}
															else if($status_nd_kandidata_ispis == 7){
																$status_nd_kandidata_ispis1 = '<span class="label label-danger material-label material-label_danger main-container__column text-left">Arhiviran</span>';
															}	
															$partner_nd_status = $row_dipl["partner_nd_status"];

															if($partner_nd_status == 1){
																$dipl_price = 20;
															}elseif($partner_nd_status == 2){
																$dipl_price = 22.5;
															}elseif($partner_nd_status == 3){
																$dipl_price = 25;
															}elseif($partner_nd_status == 4){
																$dipl_price = 25;
															} elseif($partner_nd_status == 5){
																$dipl_price = 25;
															} 
													?>
													<tr>
														<td class="text-center"><?php echo $id_broj_nd_kandidata; ?></td>
														<td><?php echo $ime_nd_kandidata_ispis." ".$prezime_nd_kandidata_ispis; ?></td>
														<td><?php echo $mobilni_nd_kandidata_ispis; ?></td>
														<td style = "word-break: break-all;"><?php echo $email_nd_kandidata_ispis; ?></td>
														<td class="text-center" style = "word-break: break-all;"><?php echo $status_nd_kandidata_ispis1; ?></td>
														<td class="text-center"><?php echo $vrijeme_kreiranja_nd_kandidata_ispis; ?></td>
														<td class="text-center"><?php echo $zaduzen_zaposlenik_nd_kandidata_ispis; ?></td> 
													</tr>
													<?php } ?>
												</tbody>
											</table>
										</div>
								</div>	
								
								
							</div>
							
							
							<!------------------------------------------------------
								STATISTIKE
							------------------------------------------------------>
							<div class="col-md-12" style="margin-top:50px;">
								<div class="row">
									<div class="col-sm-9">
										<h5>Statistike i preporuke</h5>
									</div>
								</div>
								
								
								<div class="row">
									<strong class="col-sm-3 text-right">Broj pregleda na partneru:</strong>
									<div class="col-sm-9"><?php echo $jpp_pregledi_count; ?></div>
								</div>
								<div class="row">
									<strong class="col-sm-3 text-right">Broj dana na partneru:</strong>
									<div class="col-sm-9"><?php echo $date_count_formated; ?> dan</div>
								</div>
								<div class="row">
									<strong class="col-sm-3 text-right">Status:</strong>
									<div class="col-sm-9"><?php echo $jp_position; ?></div>
								</div>
								<div class="row">
									<strong class="col-sm-3 text-right">Potrebno za sljedeću promociju:</strong>
									<?php if($vrijeme_do_promocije !== false){ ?>
										<div class="col-sm-9"><?php echo $vrijeme_do_promocije; ?> / <?php echo $kandidata_do_promocije; ?> kandidat/a</div>
									<?php } else { ?>
										<div class="col-sm-9">Nema dostupne promocije</div>
									<?php } ?>
									
								</div>
								
								<div class="row">
									<strong class="col-sm-3 text-right">Moguća isplata:</strong>
									<div class="col-sm-9"><?php echo $money_sum; ?>  €</div>
								</div>
								<div class="row">
									<strong class="col-sm-3 text-right">Isplaćeno para:</strong>
									<div class="col-sm-9"><?php echo $uplacene_provizije; ?>  €</div>
								</div>
								
								
								
								<div class="row">
									<?php if($jp_position_place == '4'){?>
										<!-- CHART DATA -->
										<div class="col-xs-12 col-sm-12 col-md-4 col-md-offset-4">
											<br/>
											<hr/>
											<br/>
											<div id="canvas-holder" style="width:100%">
												<canvas id="chart-area4"></canvas>
											</div>									
										</div									
									<?php }else{ ?>
										<!-- CHART DATA -->
										<div class="col-xs-12 col-sm-12 col-md-4 ">
											<br/>
											<hr/>
											<br/>
											<div id="canvas-holder" style="width:100%">
												<canvas id="chart-area"></canvas>
											</div>
										</div>
										<!-- CHART DATA -->
										<div class="col-xs-12 col-sm-12 col-md-4">
											<br/>
											<hr/>
											<br/>
											<div id="canvas-holder" style="width:100%">
												<canvas id="chart-area2"></canvas>
											</div>
										</div>
										<!-- CHART DATA -->
										<div class="col-xs-12 col-sm-12 col-md-4">
											<br/>
											<hr/>
											<br/>
											<div id="canvas-holder" style="width:100%">
												<canvas id="chart-area3"></canvas>
											</div>
										</div>
									<?php }?>
								</div>
								<?php if($jp_position_place == '4'){?>
								<script>
									window.onload = function() {
										var windowsize = $(window).width();
										var position_legend = 'left';
										var position_graph = 'right';
										var aspectRatioForDesktop = true;
																											
										var ctx4 = document.getElementById('chart-area4').getContext('2d');
										//window.myDoughnut2 = new Chart(ctx2, config2);
										
										if (windowsize < 768) {
											var position_legend = 'top';
											var position_graph = 'bottom';
											// ctx.height = 520;
											// ctx2.height = 520;
											$('#chart-area').css('height', '520');
											$('#chart-area2').css('height', '520');
											$('#chart-area3').css('height', '520');
										}else{
											// ctx.height = 400;
											// ctx2.height = 400;
											$('#chart-area').css('height', '350');
											$('#chart-area2').css('height', '350');
											$('#chart-area3').css('height', '350');
										}
										
										window.chartColors = {
											red: 'rgb(243, 65, 60)',
											orange: 'rgb(255, 159, 64)',
											yellow: 'rgb(255, 205, 86)',
											green: 'rgb(102, 213, 102)',
											blue: 'rgb(54, 162, 235)',
											purple: 'rgb(153, 102, 255)',
											grey: 'rgb(201, 203, 207)',
											light_blue: 'rgb(139,218,242)'
										};	
									
										var randomScalingFactor = function() {
											return Math.round(Math.random() * 100);
										};																
										
										window.myDoughnut = new Chart(ctx4, {
											type: 'doughnut',
											data: {
												datasets: [{
													data: [
														100,
													],
													backgroundColor: [
														window.chartColors.green,
														window.chartColors.grey,
													],
													label: 'Dataset 1'
												}],
												labels: [
													'Completed                                      ',
												]
											},
											options: {
												responsive: true,
												position: position_graph,
												maintainAspectRatio: false,
												legend: {
													position: position_legend,
													align: 'start'
												},
												title: {
													display: true,
													text: 'Agency Partner (110% provizije)'
												},
												animation: {
													animateScale: true,
													animateRotate: true
												}
											}
										});
										

										
										
									};
								</script>
								<?php }else{ ?>
								<script>
									window.onload = function() {
										var windowsize = $(window).width();
										var position_legend = 'left';
										var position_graph = 'right';
										var aspectRatioForDesktop = true;
										
										var ctx2 = document.getElementById('chart-area2').getContext('2d');
										//window.myDoughnut2 = new Chart(ctx2, config2);
										var ctx = document.getElementById('chart-area').getContext('2d');
										//window.myDoughnut = new Chart(ctx, config);
										var ctx3 = document.getElementById('chart-area3').getContext('2d');
										//window.myDoughnut2 = new Chart(ctx2, config2);										
										
										if (windowsize < 768) {
											var position_legend = 'top';
											var position_graph = 'bottom';
											// ctx.height = 520;
											// ctx2.height = 520;
											$('#chart-area').css('height', '520');
											$('#chart-area2').css('height', '520');
											$('#chart-area3').css('height', '520');
										}else{
											// ctx.height = 400;
											// ctx2.height = 400;
											$('#chart-area').css('height', '350');
											$('#chart-area2').css('height', '350');
											$('#chart-area3').css('height', '350');
										}
										
										
										
										window.chartColors = {
											red: 'rgb(243, 65, 60)',
											orange: 'rgb(255, 159, 64)',
											yellow: 'rgb(255, 205, 86)',
											green: 'rgb(102, 213, 102)',
											blue: 'rgb(54, 162, 235)',
											purple: 'rgb(153, 102, 255)',
											grey: 'rgb(201, 203, 207)',
											light_blue: 'rgb(139,218,242)'
										};	
									
										var randomScalingFactor = function() {
											return Math.round(Math.random() * 100);
										};
								
										window.myDoughnut = new Chart(ctx, {
											type: 'doughnut',
											data: {
												datasets: [{
													data: [
														<?php echo $full_junior_percentage; ?>,
														<?php echo (100 - $full_junior_percentage); ?>,
													],
													backgroundColor: [
														window.chartColors.green,
														window.chartColors.grey,
													],
													label: 'Dataset 1'
												}],
												labels: [
													'Completed (<?php echo $full_junior_percentage; ?>%)',
													'Missing (Dana: <?php echo $junior_missing_days; ?> , P. Partnera: <?php echo $junior_missing_partners; ?>)',
												]
											},
											options: {
												responsive: true,
												position: position_graph,
												maintainAspectRatio: false,
												legend: {
													position: position_legend,
													align: 'start'
												},
												title: {
													display: true,
													text: 'Junior Partner Progress (80% provizije)'
												},
												animation: {
													animateScale: true,
													animateRotate: true
												}
											}
										});		
										
										window.myDoughnut = new Chart(ctx2, {
											type: 'doughnut',
											data: {
												datasets: [{
													data: [
														<?php echo $full_senior_percentage; ?>,
														<?php echo (100 - $full_senior_percentage); ?>,
													],
													backgroundColor: [
														window.chartColors.green,
														window.chartColors.grey,
													],
													label: 'Dataset 1'
												}],
												labels: [
													'Completed (<?php echo $full_senior_percentage; ?>%)',
													'Missing (Dana: <?php echo $senior_missing_days; ?> , Z. Kandidata: <?php echo $senior_missing_zaposlenik; ?>)',
												]
											},
											options: {
												responsive: true,
												position: position_graph,
												maintainAspectRatio: false,
												legend: {
													position: position_legend,
													align: 'start'
												},
												title: {
													display: true,
													text: 'Partner Progress (90% provizije)'
												},
												animation: {
													animateScale: true,
													animateRotate: true
												}
											}
										});
										
										window.myDoughnut = new Chart(ctx3, {
											type: 'doughnut',
											data: {
												datasets: [{
													data: [
														<?php echo $completed_senior_percentage; ?>,
														<?php echo (100 - $completed_senior_percentage); ?>,
													],
													backgroundColor: [
														window.chartColors.green,
														window.chartColors.grey,
													],
													label: 'Dataset 1'
												}],
												labels: [
													'Completed                                      ',
													'Missing                                        ',
												]
											},
											options: {
												responsive: true,
												position: position_graph,
												maintainAspectRatio: false,
												legend: {
													position: position_legend,
													align: 'start'
												},
												title: {
													display: true,
													text: 'Senior Partner Progress (100% provizije)'
												},
												animation: {
													animateScale: true,
													animateRotate: true
												}
											}
										});																	
									};
								</script>
								<?php }?>
							</div>
							
						</div>
				</div>
			</div>
			</div>
	
	<?php
		break;

		case "open_dvag":

			$jp_id = intval($_GET['id']);

			$query = $db->prepare("
				SELECT *
				FROM  idk_jobstep_partners
				WHERE jp_id = :jp_id");

			$query->execute(array(
						':jp_id' => $jp_id));

			$row = $query->fetch();
			$jp_imeprezime = $row['jp_imeprezime'];
			$jp_makler_id = $row['jp_makler_id'];
			
			$jp_email = $row['jp_email'];
			$jp_brtelefona = $row['jp_brtelefona'];
			$jp_drzava = $row['jp_drzava'];
			$jp_grad = $row['jp_grad'];
			$jp_postanskibroj = $row['jp_postanskibroj'];
			$jp_ulica = $row['jp_ulica'];
			$jp_drzava = $row['jp_drzava'];
			$jp_placanjeimeprezime = $row['jp_placanjeimeprezime'];
			$jp_register_date = date('H:i:s d.m.Y', strtotime($row['jp_register_date']));
			$jp_register_formated = date('Y-m-d H:i:s', strtotime($row['jp_register_date']));


			if(($row['jp_social_imgurl'] == NULL) OR ($row['jp_social_imgurl'] == '')){
				$jp_social_imgurl = getSiteUrlr()."files/kandidati/nonekandidati.jpg";
			}else{
				$jp_social_imgurl = $row['jp_social_imgurl'];
			}
	
			//BROJ DANA
			$date_current = date("Y-m-d H:i:s");
			$date_count = strtotime($date_current) - strtotime($jp_register_formated);
			$date_count_formated = floor($date_count/86400);
			
			//BROJ PREGLEDA
			$query_pregled = $db->prepare("SELECT SUM(jpp_pregledi_count) as jpp_pregledi_count FROM idk_jobstep_partners_pregledi WHERE jpp_partnerid = :jpp_partnerid");

			$query_pregled->execute(array(':jpp_partnerid' => $jp_id));  

			$row = $query_pregled->fetch();
			$jpp_pregledi_count = intval($row['jpp_pregledi_count']);

			// BROJ KANDIDATA
			$query_candidate_counter = $db->prepare('SELECT idk_kandidati.kandidat_id, idk_kandidati.kandidat_ime, idk_kandidati.kandidat_prezime FROM idk_kandidati WHERE idk_kandidati.kandidat_partnerid = :partner_id AND idk_kandidati.kandidat_nalog_id IS NOT NULL AND idk_kandidati.kandidat_status_prijave IS NOT NULL AND idk_kandidati.kandidat_status_prijave NOT IN (0,1)');

            $query_candidate_counter->execute(array(
                ':partner_id' => $jp_id
            ));

            $number_of_candidates_count = $query_candidate_counter->rowCount();
            $list_of_candidates = $query_candidate_counter->fetchAll();


			// BROJ LEADOVA
			$query_lead_counter = $db->prepare('SELECT idk_kandidati.kandidat_id, idk_kandidati.kandidat_ime, idk_kandidati.kandidat_prezime FROM idk_kandidati WHERE idk_kandidati.zaduzeni_makler_id = :partner_id');

            $query_lead_counter->execute(array(
                ':partner_id' => $jp_id
            ));
			
            $number_of_leads_count = $query_lead_counter->rowCount();
            $list_of_leads = $query_lead_counter->fetchAll();
			
			// BROJ KOMPANIJA
			$query_company_counter = $db->prepare("SELECT idk_companies.company_id, idk_companies.company_name FROM idk_companies WHERE idk_companies.js_partner_id = :partner_id");

			$query_company_counter->execute(array(
				':partner_id' => $jp_id
			));

			$number_of_companies_count = $query_company_counter->rowCount();
			$list_of_companies = $query_company_counter->fetchAll();

	?>
			<div class="row">
				<div class="col-xs-8">
					<h1><a href="#"><img class="idk_profile_img" src="<?php echo $jp_social_imgurl; ?>" /></a> <?php echo $jp_imeprezime." - ".$jp_makler_id; ?> </h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>partners/listdvag" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<div class="row idk_employee_info">
							<!------------------------------------------------------
										OSNOVNE INFORMACIJE O PARTNERU
							------------------------------------------------------>
							<div class="col-md-6">
								<div class="row">
									<div class="col-sm-9">
										<h5>Osnovne informacije</h5>
									</div>
								</div>

								<div class="row">
									<strong class="col-sm-4 text-right">Ime:</strong>
									<div class="col-sm-8"><?php echo $jp_imeprezime; ?></div>
								</div>
								<div class="row">
									<strong class="col-sm-4 text-right">Br. telefona:</strong>
									<div class="col-sm-8"><?php echo $jp_brtelefona; ?></div>
								</div>
								<div class="row">
									<strong class="col-sm-4 text-right">Email:</strong>
									<div class="col-sm-8"><?php echo $jp_email; ?></div>
								</div>
								<div class="row">
									<strong class="col-sm-4 text-right">Datum registriranja:</strong>
									<div class="col-sm-8"><span class="label label-info material-label material-label_info material-label_xs main-container__column"><?php echo $jp_register_date; ?></span></div>
								</div>
								
								<div class="row">
									<strong class="col-sm-4 text-right">Država:</strong>
									<div class="col-sm-8"><?php echo $jp_drzava; ?></div>
								</div>
								
								<div class="row">
									<strong class="col-sm-4 text-right">Grad:</strong>
									<div class="col-sm-8"><?php echo $jp_grad; ?></div>
								</div>
								
								<div class="row">
									<strong class="col-sm-4 text-right">Ulica:</strong>
									<div class="col-sm-8"><?php echo $jp_ulica; ?> </div>
								</div>
								
								<div class="row">
									<strong class="col-sm-4 text-right">Pbroj:</strong>
									<div class="col-sm-8"> <?php echo $jp_postanskibroj; ?> </div>
								</div>
								
								<div class="row">
									<strong class="col-sm-4 text-right">Broj pregleda:</strong>
									<div class="col-sm-8"> <?php echo $jpp_pregledi_count; ?> </div>
								</div>
								
								<div class="row">
									<strong class="col-sm-4 text-right">Broj kandidata:</strong>
									<div class="col-sm-8"> <?php echo $number_of_candidates_count; ?> </div>
								</div>

								<div class="row">
									<strong class="col-sm-4 text-right">Broj kompanija:</strong>
									<div class="col-sm-8"> <?php echo $number_of_companies_count; ?> </div>
								</div>
								
							</div>
														
							<!------------------------------------------------------
								Preporučeni kanididati
							------------------------------------------------------>		
							<div class="col-md-12" style="margin-top:50px;">
								<div class="row">
									<div class="col-sm-9">
										<h5>Preporučeni kandidati </h5>
									</div>
								</div>
															
								<div class="row">
									<div class="col-sm-offset-1 col-sm-10">
										<script type="text/javascript">
											$(document).ready(function() {
												$('#idk_partnerss_kandidates').DataTable({

													responsive: true,

													"order": [[ 0, "desc" ]],
													"aoColumns": [
															null,
															null
														]
												});
											} );
										</script>

										<table id="idk_partnerss_kandidates" class="display" cellspacing="0" width="100%">
											<thead>
												<tr>
													<th>ID</th>
													<th>Ime i prezime</th>
												</tr>
											</thead>
											<tbody>
												<?php foreach($list_of_candidates as $candidate_item){ ?>
												<tr>
													<td><?php echo $candidate_item["kandidat_id"];?></td>
													<td><?php echo $candidate_item["kandidat_ime"]." ".$candidate_item["kandidat_prezime"];?></td>
												</tr>
												<?php } ?> 
											</tbody>
										</table>
									</div>
								</div>	
							</div>
							

							<!------------------------------------------------------
								Leadovi
							------------------------------------------------------>		
							<div class="col-md-12" style="margin-top:50px;">
								<div class="row">
									<div class="col-sm-9">
										<h5>Leadovi </h5>
									</div>
								</div>
															
								<div class="row">
									<div class="col-sm-offset-1 col-sm-10">
										<script type="text/javascript">
											$(document).ready(function() {
												$('#idk_partnerss_leads').DataTable({

													responsive: true,

													"order": [[ 0, "desc" ]],
													"aoColumns": [
															null,
															null
														]
												});
											} );
										</script>

										<table id="idk_partnerss_leads" class="display" cellspacing="0" width="100%">
											<thead>
												<tr>
													<th>ID</th>
													<th>Ime i prezime</th>
												</tr>
											</thead>
											<tbody>
												<?php foreach($list_of_leads as $leads_item){ ?>
												<tr>
													<td><?php echo $leads_item["kandidat_id"];?></td>
													<td><?php echo $leads_item["kandidat_ime"]." ".$leads_item["kandidat_prezime"];?></td>
												</tr>
												<?php } ?> 
											</tbody>
										</table>
									</div>
								</div>	
							</div>

							<!------------------------------------------------------
								Preporučene kompanije
							------------------------------------------------------>		
							<div class="col-md-12" style="margin-top:50px;">
								<div class="row">
									<div class="col-sm-9">
										<h5>Preporučene kompanije </h5>
									</div>
								</div>
															
								<div class="row">
									<div class="col-sm-offset-1 col-sm-10">
										<script type="text/javascript">
											$(document).ready(function() {
												$('#idk_partnerss_companies').DataTable({

													responsive: true,

													"order": [[ 0, "desc" ]],
													"aoColumns": [
															null,
															null
														]
												});
											} );
										</script>

										<table id="idk_partnerss_companies" class="display" cellspacing="0" width="100%">
											<thead>
												<tr>
													<th>ID</th>
													<th>Naziv</th>
												</tr>
											</thead>
											<tbody>
												<?php foreach($list_of_companies as $company_item){ ?>
												<tr>
													<td><?php echo $company_item["company_id"];?></td>
													<td><?php echo $company_item["company_name"];?></td>
												</tr>
												<?php } ?> 
											</tbody>
										</table>
									</div>
								</div>	
							</div>
						</div>
					</div>
				</div>
			</div>

	<?php
		break;
		
		case 'finance':
		
		
		?>
		<div class = "row">
			<div class = "col-xs-9 idk_color_green">
				<h1>
					<i class="fa fa-users idk_color_green" aria-hidden="true" style = "margin-right: 10px;"></i>
						PARTNER - FINANCIJE
				</h1>
			</div>
			<div class = "col-xs-3 text-right">
				<a href="<?php getSiteURL(); ?>partners/finance" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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
				<div id="myTabs" class="panel-group material-tabs-group">
					<ul class="nav nav-tabs material-tabs material-tabs_success">
						<li style = "margin-left: 25px;" class="active" ><a href="#jp_pending" class="material-tabs__tab-link" data-toggle="tab">PARTNER - PENDING</a></li>
						<li style="float: right; margin-right: 25px;"><a href="#jp_paid" class="material-tabs__tab-link" data-toggle="tab">PARTNER - UPLAĆENI</a></li>
					</ul>
					<div class="tab-content materail-tabs-content">
					
					<!-- TAB PREGLED PARTNERA KOJIMA SE TREBA POTVRDITI UPLATA -->
					
						<div class="tab-pane fade active in" id="jp_pending">					
							<div class="row">
								<div class="col-xs-12">
								<script type="text/javascript">
									$(document).ready(function() {
										$('#idk_partners_pending').DataTable({

										responsive: true,

										"order": [[ 0, "desc" ]],
										"aoColumns": [
												null,
												null,
												null,
												null,
												null,
												null,
												null
											]
											});
									} );
								</script>
								<!----------------------------------------------------
										NALOZI - PENDING PAYMENT
								----------------------------------------------------->
								<table id="idk_partners_pending" class="display" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th class="text-center">ID</th>
											<th class="text-center">PARTNER</th>
											<th class="text-center">KANDIDAT</th>
											<th class="text-center">NALOG</th>
											<th class="text-center">DATUM POCETKA RADA</th>
											<th class="text-center">PROVIZIJA</th>
											<th class="text-center">AKCIJA</th>
										</tr>
									</thead>
									<tbody>
									<?php 
								
										$read_query = $db->prepare('
											SELECT DISTINCT kandidat_id, kandidat_ime, kandidat_prezime, kandidat_partnerid, kandidat_datum_pocetakrada, kandidat_slika, kandidat_partner_status, kandidat_prijava_na, idk_partner_uplate.jp_uplate_provizija
											FROM idk_kandidati
											INNER JOIN idk_partner_uplate ON idk_kandidati.kandidat_id = idk_partner_uplate.jp_uplate_kandidatid
											WHERE kandidat_status_prijave = :kandidat_status_prijave
											AND kandidat_partnerid IS NOT NULL
											AND idk_partner_uplate.jp_uplate_status != 1
											 AND idk_partner_uplate.jp_uplate_vrsta = 1
											');  
											
										$read_query->execute(array(':kandidat_status_prijave' => 4));   
										
										$ukupno_kandidata =  $read_query->rowCount();
										
										while($row = $read_query->fetch()){
											
											$kandidat_id = $row['kandidat_id'];
											$kandidat_ime = $row['kandidat_ime'];
											$kandidat_prezime = $row['kandidat_prezime'];
											$kandidat_prijava_na = $row['kandidat_prijava_na'];
											
											
											$kandidat_partnerid = $row['kandidat_partnerid'];
											
											$kandidat_datum_pocetakrada = date('d.m.Y', strtotime($row['kandidat_datum_pocetakrada']));

											$nalog_partner_provizija = $row['jp_uplate_provizija'];
											
											
											// SLIKA
											if($row['kandidat_slika'] == "none"){
												$kandidat_slika = "nonekandidati.jpg";
											}else{
												$kandidat_slika = $row['kandidat_slika'];
											}
												
											if($row['kandidat_partner_status'] == '1'){
												$partner_procentage = 0.8;
											}elseif($row['kandidat_partner_status'] == '2'){
												$partner_procentage = 0.9;
											}elseif($row['kandidat_partner_status'] == '3'){
												$partner_procentage = 1;
											}elseif($row['kandidat_partner_status'] == '4'){
												$partner_procentage = 1.1;
											}elseif($row['kandidat_partner_status'] == '5'){
												$partner_procentage = 1.25;
											}
											
											
											$provizija = round($nalog_partner_provizija*$partner_procentage , 2 );
											
											$partner_isplaceno_txt = '<input type="text" class="form-control jp_datum_uplate" placeholder="Datum" style="float:left;width:15rem;" required><i class="fa fa-check oznaci_kao_isplaceno" data-toggle="modal" data-target="#potvrdaUplate_modal" data-vrsta="1" data-provizija="'.$provizija.'" data-kandidatid="'.$kandidat_id.'" data-partnerid="'.$kandidat_partnerid.'" style="font-size:24px;color:#999999;cursor:pointer;" aria-hidden="true"></i>';																
												
										
										?>
										<tr>
											<td><?php echo $kandidat_id; ?></td>
											<td class="text-center"><a href="<?php getSiteURL(); ?>partners/<?php echo $kandidat_partnerid; ?>"><?php getInfoPartnerPreporuka($kandidat_partnerid); ?></a></td>			
											<td class="text-center"><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>"><img class="idk_profile_img" src="<?php getSiteURL(); ?>files/kandidati/<?php echo $kandidat_slika; ?>"> <span style = "display: block;margin-top: 10px;"> <?php echo $kandidat_ime; ?> <?php echo $kandidat_prezime; ?> </span></a></td>
											<td class="text-center"><span class="label label-success material-label material-label_success material-label_xs main-container__column"><?php echo $kandidat_prijava_na; ?></span></td>
											<td class="text-center"><span class="label label-success material-label material-label_success material-label_xs main-container__column"><?php echo $kandidat_datum_pocetakrada; ?></span></td>
											<td class="text-center"><span class="label label-info material-label material-label_success material-label_xs main-container__column"><?php echo $provizija; ?> €</span></td>	
											<td class="text-center"><?php echo $partner_isplaceno_txt; ?></td> 
										</tr>
										<?php  } ?>
									</tbody>
								</table>
							</div>
						</div>
						
						<hr>
						<div class="row">
							<div class="col-sm-9">
								<h5><b>DIPL</b></h5>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12">
									<style>
									td.details-control {
										background: url('https://datatables.net/examples/resources/details_open.png') no-repeat center center;
										cursor: pointer;
									}
									tr.shown .details-control {
										background: url('https://datatables.net/examples/resources/details_close.png') no-repeat center center;
									}
									</style>
									<script type="text/javascript">
										$(document).ready(function() {
											function format (partnerid ) {
												var div = $(
												'<table class="slow-animation" width="100%"><thead><tr><th class="text-center">#ID</th><th class="text-center">KANDIDAT</th><th class="text-center">STATUS</th><th class="text-center">PORIJEKLO</th><th class="text-center">VRIJEME ULASKA</th><th class="text-center">PROVIZIJA</th><th class="text-center">ISPLATI POJEDINAČAN</th></tr></thead></table>');
												
												$.ajax( {
													url: '<?php getSiteUrl(); ?>ajax_data.php?page=getPartnerCandidates_dipl',
													data: {
														partnerid: partnerid
													},
													dataType: 'json',
													success: function ( data ) {
														data.forEach(row => $(div).append('<tr>'+row+'</tr>'));
													},
													error: function (xhr, ajaxOptions, thrownError) {
																	alert(xhr.status);
																	alert(thrownError);
																}
												} );
												return div;
											}
											 var table = $('#idk_partner_dipl_pending').DataTable({

												responsive: true,

												"order": [[ 1, "desc" ]],
												"aoColumns": [ 
													{
														"className":      'details-control',
														"orderable":      false,
														"data":           null,
														"defaultContent": ''
													},
														null,
														null,
														null,
														null,
														null
													]
											});
											
											$('#idk_partner_dipl_pending tbody').on('click', 'td.details-control', function () {
												var partnerid = $(this).data("partnerid");
												var tr = $(this).closest('tr');
												var row = table.row( tr );
											 
												if ( row.child.isShown() ) {
														row.child.hide();
														tr.removeClass('shown');
												}
												else {
													row.child( format(partnerid) ).show();
													tr.addClass('shown');
												}
											} );
										});
									</script>
									<!------------------------------------------------------------------------
														DIPL PREGLED PARTNER KANDIDATA
									-------------------------------------------------------------------------->
									<table id="idk_partner_dipl_pending" class="display" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th class="text-center"></th>
												<th class="text-center">#ID</th>
												<th class="text-center">PARTNER</th>
												<th class="text-center">UKUPNO NEISPLACENIH PREPORUKA</th>
												<th class="text-center">PROVIZIJA</th>
												<th class="text-center">ISPLATI SVE!</th>
											</tr>
										</thead>
										<tbody>
											<?php
																	
												// NAPOMENUTI SMAJU DA ULJEPŠA QUERY NA OVOM FAJLU
												$query_DIPL = $db->prepare("SELECT COUNT(id_broj_nd_kandidata) as num_partner_kandidata, partner_nd_status, idk_partner_uplate.jp_uplate_partnerid
																			FROM idk_nd_kandidata 
																			INNER JOIN idk_partner_uplate ON idk_nd_kandidata.id_broj_nd_kandidata = idk_partner_uplate.jp_uplate_kandidatid
																			WHERE idk_partner_uplate.jp_uplate_status != 1 AND idk_partner_uplate.jp_uplate_vrsta = 2
																			GROUP BY idk_partner_uplate.jp_uplate_partnerid
																		");
																		
												// povijest_vrsta_nd_kandidata se gleda iz razloga što bi mogle biti kasnije promjene u vrsti nostrifikacije te da se ne mijenja svugdje 						
																		
												$query_DIPL->execute();
												// MOGUCA ISPLATA
												$query_result = $query_DIPL->fetchAll();
												foreach($query_result as $row_dipl){
													$num_partner_kandidata = $row_dipl['num_partner_kandidata'];
													$dipl_price = intval($num_partner_kandidata)*25;
													
													$kandidat_partnerid = $row_dipl['jp_uplate_partnerid'];
													
													
													$partner_nd_status = $row_dipl["partner_nd_status"];

													if($partner_nd_status == 1){
														$dipl_price = 20;
													}elseif($partner_nd_status == 2){
														$dipl_price = 22.5;
													}elseif($partner_nd_status == 3){
														$dipl_price = 25;
													}elseif($partner_nd_status == 4){
														$dipl_price = 25;
													}elseif($partner_nd_status == 5){
														$dipl_price = 25;
													}
													$ukupna_dipl_price = $dipl_price*$num_partner_kandidata;
													$partner_isplaceno_txt = '<input type="text" class="form-control jp_datum_uplate" placeholder="Datum" style="float:left;width:15rem;" required><i class="fa fa-check oznaci_kao_isplaceno" data-toggle="modal" data-target="#potvrdaUplate_modal" data-vrsta="2" data-provizija="'.$dipl_price.'" data-kandidatid="'.$id_broj_nd_kandidata.'" data-partnerid="'.$kandidat_partnerid.'" style="font-size:24px;color:#999999;cursor:pointer;" aria-hidden="true"></i>';																
													
													$partner_ukupno_txt = '<input type="text" class="form-control jp_datum_uplate" placeholder="Datum" style="float:left;width:15rem;" required><i class="fa fa-check oznaci_kao_isplaceno" data-toggle="modal" data-target="#potvrdaUplate_modal" data-vrsta="2" data-provizija="'.$ukupna_dipl_price.'" data-kandidatid="all" data-partnerid="'.$kandidat_partnerid.'" style="font-size:24px;color:#999999;cursor:pointer;" aria-hidden="true"></i>';																
													
													
												?>
											
											<tr>
												<td class="text-center" data-partnerid ="<?php echo $kandidat_partnerid; ?>"></td>
												<td class="text-center"><a href="<?php getSiteURL(); ?>partners/<?php echo $kandidat_partnerid; ?>"><?php echo $kandidat_partnerid; ?></a></td> 
												<td class="text-center"><a href="<?php getSiteURL(); ?>partners/<?php echo $kandidat_partnerid; ?>"><?php getInfoPartnerPreporuka($kandidat_partnerid); ?></a></td> 
												<td class="text-center"><a href="#"><?php echo $num_partner_kandidata; ?></a></td>
												<td class = "text-center"><span class="label label-info material-label material-label_success material-label_xs main-container__column"><?php echo $ukupna_dipl_price; ?> €</span></td>	
												<td class="text-center"><?php echo $partner_ukupno_txt; ?></td> 	
											</tr>
											<?php } ?> 
										</tbody>
									</table>
								</div>
							<!-- Modal potvrde uplate -->
							<div class="modal material-modal material-modal_danger fade" id="potvrdaUplate_modal">
								<div class="modal-dialog">
									<div class="modal-content material-modal__content">
										<div class="modal-header material-modal__header">
											<button class="close material-modal__close" data-dismiss="modal">&times;</button>
											<h4 class="modal-title material-modal__title">Potvrda</h4>
										</div>
										<div class="modal-body material-modal__body">
											<p>Nastavi sa potvrdom uplate?</p>
										</div>
										<div class="modal-footer material-modal__footer">
											<button class="btn material-btn material-btn" data-dismiss="modal">NE</button>
											<button class="btn btn-primary material-btn material-btn_danger potvrdaUplate_button">POTVRDI</button>
										</div>
									</div>
								</div>
							</div>
						</div>	<!-- row end -->
						
						<script>
						
							$(".jp_datum_uplate").flatpickr({
								dateFormat: "d.m.Y H:i:s",
								disableMobile: "true",
							});	
						$(".oznaci_kao_isplaceno").on("click", function() {
							var kandidat_id = $(this).data("kandidatid");
							var partner_id = $(this).data("partnerid");
							var provizija = $(this).data("provizija");
							var vrsta = $(this).data("vrsta");
							var selektor = $(this);
							var datum =  $(this).parent().find("input").val();
							$(".potvrdaUplate_button").on( "click", function() {
								if ($(selektor).hasClass('oznaci_kao_isplaceno')) {
									$.ajax({
										url: '<?php getSiteUrl(); ?>do.php?form=pay_partner',
										type: 'POST',
										data: {'kandidat_id':kandidat_id, 'partner_id':partner_id, 'provizija':provizija,'datum':datum,'vrsta':vrsta},
										dataType: 'html',
										success: function(data) {
											selektor.css({"color": "green", "cursor": "not-allowed"});
											selektor.removeClass("oznaci_kao_isplaceno");
											selektor.removeAttr("data-target");
											console.log(data);
											location.reload();
										}
									});
								};
							});
						});
						</script>
						<!------------------------------------------------------------------------
											DIPL PREGLED PARTNER KANDIDATA KRAJ
						-------------------------------------------------------------------------->
					</div>
					
					<!------------------------------------------------------------------------
										JP_PARTNER_PENDING - MATERIAL TAB END
					-------------------------------------------------------------------------->
					
					<!------------------------------------------------------------------------>
					
					<!------------------------------------------------------------------------
										JP_PARTNER_PAID - MATERIAL TAB BEGIN
					-------------------------------------------------------------------------->
					
					<div class="tab-pane fade" id="jp_paid">			
							<div class="row">
								<div class="col-xs-12">
									<script type="text/javascript">
									$(document).ready(function() {
										$('#idk_partners_paid').DataTable({

											responsive: true,

											"order": [[ 0, "desc" ]],
											"aoColumns": [
													null,
													null,
													null,
													null,
													null,
													null,
													null
												]
										});
									} );
									</script>
									<table id="idk_partners_paid" class="display" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th class="text-center">#ID</th>
												<th class="text-center">PARTNER</th>
												<th class="text-center">KANDIDAT</th>
												<th class="text-center">NALOG</th>
												<th class="text-center">DATUM POCETKA RADA</th>
												<th class="text-center">PROVIZIJA</th>
												<th class="text-center">AKCIJA</th>
											</tr>
										</thead>
										<tbody>
										<?php 
								
										$read_query = $db->prepare('
											SELECT DISTINCT kandidat_id, kandidat_ime, kandidat_prezime, kandidat_partnerid, kandidat_datum_pocetakrada, kandidat_slika, kandidat_partner_status, kandidat_prijava_na, idk_partner_uplate.jp_uplate_provizija
											FROM idk_kandidati
											INNER JOIN idk_partner_uplate ON idk_kandidati.kandidat_id = idk_partner_uplate.jp_uplate_kandidatid
											WHERE kandidat_status_prijave = :kandidat_status_prijave
											AND kandidat_partnerid IS NOT NULL
											AND idk_partner_uplate.jp_uplate_status = 1
											 AND idk_partner_uplate.jp_uplate_vrsta = 1
											');  
											
										$read_query->execute(array(':kandidat_status_prijave' => 4));  
										
										$ukupno_kandidata =  $read_query->rowCount();
										
										while($row = $read_query->fetch()){
											$kandidat_id = $row['kandidat_id'];
											$kandidat_ime = $row['kandidat_ime'];
											$kandidat_prezime = $row['kandidat_prezime'];
											$kandidat_prijava_na = $row['kandidat_prijava_na'];
											
											
											$kandidat_partnerid = $row['kandidat_partnerid'];
											
											$kandidat_datum_pocetakrada = date('d.m.Y', strtotime($row['kandidat_datum_pocetakrada']));

											$provizija = $row['jp_uplate_provizija'];
											
											
											// SLIKA
											if($row['kandidat_slika'] == "none"){
												$kandidat_slika = "nonekandidati.jpg";
											}else{
												$kandidat_slika = $row['kandidat_slika'];
											}
											
											$partner_isplaceno_txt = '<i class="fa fa-check" style="font-size:24px;color:green;cursor: not-allowed;" aria-hidden="true" disabled></i>';										
										
										?>
										<tr>
											<td class="text-center"><?php echo $kandidat_id; ?></td>
											<td class="text-center"><a href="<?php getSiteURL(); ?>partners/<?php echo $kandidat_partnerid; ?>"><?php getInfoPartnerPreporuka($kandidat_partnerid); ?></a></td>			
											<td class="text-center"><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>"><img class="idk_profile_img" src="<?php getSiteURL(); ?>files/kandidati/<?php echo $kandidat_slika; ?>"> <span style = "display: block;margin-top: 10px;"> <?php echo $kandidat_ime; ?> <?php echo $kandidat_prezime; ?> </span></a></td>
											<td class="text-center"><span class="label label-success material-label material-label_success material-label_xs main-container__column"><?php echo $kandidat_prijava_na; ?></span></td>
											<td class="text-center"><span class="label label-success material-label material-label_success material-label_xs main-container__column"><?php echo $kandidat_datum_pocetakrada; ?></span></td>
											<td class="text-center"><span class="label label-info material-label material-label_success material-label_xs main-container__column"><?php echo $provizija; ?> €</span></td>	
											<td  class="text-center"><?php echo $partner_isplaceno_txt; ?></td> 
										</tr>
									<?php } ?>
										</tbody>
									</table>
								</div>
							</div>
						<hr>
						<div class="row">
							<div class="col-sm-9">
								<h5><b>DIPL</b></h5>
							</div>
						</div>
							<div class="row">
								<div class="col-xs-12">
									<script type="text/javascript">
									$(document).ready(function() {
										$('#idk_partners_paid_dipl').DataTable({

											responsive: true,

											"order": [[ 0, "desc" ]],
											"aoColumns": [
													null,
													null,
													null,
													null,
													null,
													null,
													null
												]
										});
									} );
									</script>
									<table id="idk_partners_paid_dipl" class="display" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th class="text-center">#ID</th>
												<th class="text-center">PARTNER</th>
												<th class="text-center">KANDIDAT</th>
												<th class="text-center">STATUS</th>
												<th class="text-center">PORIJEKLO</th>
												<th class="text-center">DATUM UPLATE</th>
												<th class="text-center">PROVIZIJA</th>
												<th class="text-center">AKCIJA</th>
											</tr>
										</thead>
										<tbody>
										<?php 
								
											$query_DIPL_pregled = $db->prepare("SELECT id_broj_nd_kandidata, ime_nd_kandidata, prezime_nd_kandidata, status_nd_kandidata, pstatus_nd_kandidata, povijest_nd_kandidata, povijest_vrsta_nd_kandidata, zaduzen_zaposlenik_nd_kandidata,kampanja_id, idk_partner_uplate.jp_uplate_partnerid, idk_partner_uplate.jp_uplate_provizija, idk_partner_uplate.jp_uplate_datum
																				FROM idk_nd_kandidata 
																				INNER JOIN idk_partner_uplate ON idk_nd_kandidata.id_broj_nd_kandidata = idk_partner_uplate.jp_uplate_kandidatid
																				WHERE idk_partner_uplate.jp_uplate_status = 1 AND idk_partner_uplate.jp_uplate_vrsta = 2
																			");
																				
														// povijest_vrsta_nd_kandidata se gleda iz razloga što bi mogle biti kasnije promjene u vrsti nostrifikacije te da se ne mijenja svugdje 						
																				
											$query_DIPL_pregled->execute();
											
											$ukupno_kandidata =  $query_DIPL_pregled->rowCount();
											
											while($row = $query_DIPL_pregled->fetch()){
												$id_broj_nd_kandidata = $row['id_broj_nd_kandidata'];
												$kandidat_ime = $row['ime_nd_kandidata'];
												$kandidat_prezime = $row['prezime_nd_kandidata'];
												$status_nd_kandidata_ispis = $row['status_nd_kandidata'];
												$pstatus_nd_kandidata_ispis = $row['pstatus_nd_kandidata'];
												$povijest = $row['povijest_nd_kandidata'];
												$povijest_vrsta = $row['povijest_vrsta_nd_kandidata'];
												$kampanja = $row['kampanja_id'];
												
												if($status_nd_kandidata_ispis == 1){
													if($pstatus_nd_kandidata_ispis == 1){
														$pstatus_nd_kandidata_ispis1 = 'Lead';
														$pstatus_nd_kandidata_style1 = 'background-color: #839098;';
													}
													else if($pstatus_nd_kandidata_ispis == 2){
														$pstatus_nd_kandidata_ispis1 = 'Neuspješan Kontakt 3';
														$pstatus_nd_kandidata_style1 = 'background-color: #00fb53;';
													}
													else if($pstatus_nd_kandidata_ispis == 3){
														$pstatus_nd_kandidata_ispis1 = 'Zainteresiran Lead';
														$pstatus_nd_kandidata_style1 = 'background-color: #0E6973;';
													}
													else if($pstatus_nd_kandidata_ispis == 4){
														$pstatus_nd_kandidata_ispis1 = 'Nezainteresiran Lead';
														$pstatus_nd_kandidata_style1 = 'background-color: #BF214B;';
													}
													else if($pstatus_nd_kandidata_ispis == 5){
														$pstatus_nd_kandidata_ispis1 = 'U obradi Lead';
														$pstatus_nd_kandidata_style1 = 'background-color: #c79cff;';
													}
													else if($pstatus_nd_kandidata_ispis == 6){
														$pstatus_nd_kandidata_ispis1 = 'Neuspješan Kontakt 1';
														$pstatus_nd_kandidata_style1 = 'background-color: #00fafb;';
													}
													$status_nd_kandidata_ispis1 = '<span style = "'.$pstatus_nd_kandidata_style1.' color: white" class="label label-default material-label material-label_default main-container__column text-left">'.$pstatus_nd_kandidata_ispis1.'</span>';
												}
												else if($status_nd_kandidata_ispis == 2){
													$status_nd_kandidata_ispis1 = '<span  style = "background-color: #f2e42e;" class="label label-default material-label material-label_default main-container__column text-left">Prikupljanje dokumentacije</span>';
												}
												else if($status_nd_kandidata_ispis == 3){ 
													$status_nd_kandidata_ispis1 = '<span class="label label-primary material-label material-label_primary main-container__column text-left">Poslana pošta</span>';
												}
												else if($status_nd_kandidata_ispis == 4){
													$status_nd_kandidata_ispis1 = '<span class="label label-info material-label material-label_info main-container__column text-left">U obradi</span>';
												}
												else if($status_nd_kandidata_ispis == 5){
													$status_nd_kandidata_ispis1 = '<span class="label label-warning material-label material-label_warning main-container__column text-left">Dopuna dokumentacije</span>';
												}
												else if($status_nd_kandidata_ispis == 6){
													$status_nd_kandidata_ispis1 = '<span class="label label-success material-label material-label_success main-container__column text-left">Završen</span>';
												}
												else if($status_nd_kandidata_ispis == 7){
													$status_nd_kandidata_ispis1 = '<span class="label label-danger material-label material-label_danger main-container__column text-left">Arhiviran</span>';
												}
												
												if($povijest == 0){
													$povijest_ispis = 'Ručna registracija';
													continue;
												}
												else if($povijest == 1){
													if($povijest_vrsta == 1){
														$povijest_vrsta_ispisx = 'SMS';
													}
													else if($povijest_vrsta == 2){
														$povijest_vrsta_ispisx = 'JobStep-Messenger';
													}
													else if($povijest_vrsta == 3){
														$povijest_vrsta_ispisx = 'CRM';
													}
													else if($povijest_vrsta == 4){
														$povijest_vrsta_ispisx = 'Viber';
													}
													$povijest_ispis = 'Kandidati-'.$povijest_vrsta_ispisx.'';
													}
												else if($povijest == 2){
													if($povijest_vrsta == 1){
														$povijest_vrsta_ispisx = 'APP';
													}
													else if($povijest_vrsta == 2){
														$povijest_vrsta_ispisx = 'WEB';
													}
													$povijest_ispis = 'Sve za vize-'.$povijest_vrsta_ispisx.'';
												}
												else if($povijest == 3){
													$povijest_ispis = 'JobStep-Partner-APP';
												}
												else if($povijest == 4){
													$povijest_ispis = 'JobStep-Web';
												}
												else if($povijest == 5){
													$povijest_ispis = ''.getKampanjeSkrNazivDIPLR($kampanja).'';
												}
												
												$kandidat_partnerid = $row['jp_uplate_partnerid'];
												
												$datum_uplate = date('d.m.Y', strtotime($row['jp_uplate_datum']));

												$provizija = $row['jp_uplate_provizija'];
												
												
												$partner_isplaceno_txt = '<i class="fa fa-check" style="font-size:24px;color:green;cursor: not-allowed;" aria-hidden="true" disabled></i>';										
											
											?>
											<tr>
												<td class="text-center"><?php echo $id_broj_nd_kandidata; ?></td>
												<td class="text-center"><a href="<?php getSiteURL(); ?>partners/<?php echo $kandidat_partnerid; ?>"><?php getInfoPartnerPreporuka($kandidat_partnerid); ?></a></td> 
												<td class="text-center"><a href="<?php getSiteURL(); ?>nostrifikacija_diploma?page=otvori_ND_kandidata&id=<?php echo $id_broj_nd_kandidata; ?>"><?php echo $kandidat_ime." ".$kandidat_prezime; ?></a></td>
												<td class="text-center" style = "word-break: break-all;"><?php echo $status_nd_kandidata_ispis1; ?></td>
												<td class="text-center" style = "word-break: break-all;"><span class="label label-primary material-label material-label_primary main-container__column text-left"><?php echo $povijest_ispis; ?></span></td>
												<td class="text-center"><?php echo $datum_uplate; ?></td>
												<td class = "text-center"><span class="label label-info material-label material-label_success material-label_xs main-container__column"><?php echo $dipl_price; ?> €</span></td>	
												<td class="text-center"><?php echo $partner_isplaceno_txt; ?></td> 	
											</tr>
										<?php } ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
		
		
		
		<?php
		break;
		
		case "eligible_leads_to_assign":

			$query_get_eligible_leads = $db -> prepare('
				SELECT 
					kan.kandidat_id, kan.kandidat_ime, kan.kandidat_drzavljanstvo_vrsta, kan.kandidat_prezime, kan.kandidat_status_prijave, kan.zaduzeni_makler_id, kan.zaduzen_makleru_datum, kan.kandidat_partner_lead_status, n.nalog_id, n.nalog_naziv, js.jp_imeprezime, kan.kandidat_dogovoreni_pocetak_rada 
				FROM 
					idk_kandidati kan 
				LEFT JOIN 
					idk_jobstep_partners js 
				ON 
					js.jp_id = kan.zaduzeni_makler_id 
				JOIN
					idk_nalozi n
				ON 
					n.nalog_id = kan.kandidat_nalog_id

				WHERE 
					kan.kandidat_dogovoreni_pocetak_rada IS NOT NULL AND kan.kandidat_status_prijave = 27
			');
			$query_get_eligible_leads -> execute();
			$eligible_leads = $query_get_eligible_leads -> fetchAll();

			$query_get_all_sellers = $db -> prepare('
				SELECT jp_id, jp_imeprezime
				FROM idk_jobstep_partners
				WHERE jp_makler_id IS NOT NULL
			');
			$query_get_all_sellers -> execute();
			$eligible_sellers = $query_get_all_sellers -> fetchAll();

			$select_seller = '<option value="" selected disabled hidden>Odaber Maklera</option></div>';
			foreach($eligible_sellers as $seller){
				$select_seller .= '<option value = "'.$seller["jp_id"].'">'.$seller["jp_imeprezime"].'</option>';
			}
			$select_seller .= "</select>";
			?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-users idk_color_green" aria-hidden="true"></i> Dodjeljivanje podobnih leadova maklerima</h1>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<div class="row">
							<div class="col-xs-12">
								<script type="text/javascript">
								$(document).ready(function() {
									var table = $('#candidate_makler_table').DataTable({
										responsive: true,

										"order": [[ 0, "desc" ]],
										"drawCallback": function( settings ) {
											$(".seller_picker").selectpicker("refresh");
											$('.switch_seller').unbind().bind('click', switchSeller);
										},
										"columns": [
											{ "width": "2%" },
											{ "width": "3%" },
											{ "width": "15%" },
											{ "width": "20%" },
											{ "width": "10%" },
											{ "width": "10%" },
											{ "width": "10%" },
											{ "width": "15%" },
											{ "width": "15%" },
										],    
										'columnDefs': [
											{
											'targets': 0,
												'checkboxes': {
													'selectRow': true
												}
											}
										],
										'select': {
											'style': 'multi'
										},
									});
									$('#assign_all_selected').on('click', function(){
										$("#candidate_ids").empty();
										var rows_selected = table.column(0).checkboxes.selected();

										$.each(rows_selected, function(index, rowId){
											$("#candidate_ids").append(
												rowId
											);
										});
										
										var selected_candidates = $('#candidate_ids').find(".selected_candidate").map(function(){return $(this).val(); }).get();

										if(selected_candidates.length){
											$.ajax({
												url: "../ajax_data.php?page=bulk_assign_candidates_to_seller",
												type: "POST",
												dataType: "html",
												data: {
													selected_candidates: selected_candidates,
												},
												success: function () {
													alert('Kandidati uspješno dodijeljeni.');
													location.reload();
												},
												error: function (xhr, ajaxOptions, thrownError) {
													alert(xhr.status);
													alert(thrownError);
												},
											});
										}

									});
									function switchSeller(){
										var candidate_id = $(this).attr('candidate_id');
										var seller_id = $('#seller_picker_'+candidate_id).val();
										if(seller_id == null){
											$('#parent_'+candidate_id).effect('highlight');
											$('#parent_'+candidate_id).effect('shake');
										}
										else{
											$.ajax({
												url: "../ajax_data.php?page=assign_seller_to_candidate_lead",
												type: "POST",
												dataType: "html",
												data: {
													seller_id: seller_id,
													candidate_id: candidate_id,
												},
												success: function (data) {
													$('#seller_name_'+candidate_id).hide('fade', function(){
														$(this).empty().append(data).show('fade');
													});
												},
												error: function (xhr, ajaxOptions, thrownError) {
													alert(xhr.status);
													alert(thrownError);
												},
											});
										}
									}
								});


								</script>
								<div id="candidate_ids" style="display: none;"></div>
								<div class="col-md-4">
								
								</div>
								<div class="col-md-8">
									<div class="row">
										<div class="text-center">
											<a id = "assign_all_selected" style="float: right; margin-right: 8px;" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodjeli sve označene kandidate</span></a>
										</div>
									</div>
								</div>
								<hr>
								<table id="candidate_makler_table" class="display" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th style = "text-align:left;"><input type = "checkbox" id = "select_all_candidates"></th>
											<th>#ID</th>
											<th>Ime i prezime</th>
											<th>Nalog</th>
											<th class="text-center">Status prijave</th>
											<th>Dogovoreni početak rada</th>
											<th>Državljanstvo</th>
											<th>Zaduzeni makler</th>
											<th class="text-center">Dodijeli maklera</th>
										</tr>
									</thead>
									<tbody>
									<?php
									
										foreach($eligible_leads as $lead){
											$seller_name = $lead['jp_imeprezime'];

											if(is_null($seller_name))
												$seller_name = '<b style = "color: red;">Nije dodijeljen</b>';

											echo "<tr>";
												echo '<td><input class = "selected_candidate" type = "checkbox" value = "'.$lead['kandidat_id'].'"></td>';
												echo "<td>".$lead['kandidat_id']."</td>";
												echo "<td>".$lead['kandidat_ime']." ".$lead['kandidat_prezime']."</td>";
												echo "<td>".$lead['nalog_id']." | ".$lead['nalog_naziv']."</td>";
												echo '<td style = "text-align:center">'.getStatusPrijavePrint($lead['kandidat_status_prijave']).'</td>';
												echo '<td data-order = "'.$lead['kandidat_dogovoreni_pocetak_rada'].'">'.date('d.m.Y', strtotime($lead['kandidat_dogovoreni_pocetak_rada'])).'</td>';
												echo "<td>".$lead['kandidat_drzavljanstvo_vrsta']."</td>";
												echo '<td id = "seller_name_'.$lead['kandidat_id'].'">'.$seller_name.'</td>';
												echo '
													<td>
														<i 	
															id = "switch_seller_'.$lead['kandidat_id'].'"
															candidate_id = "'.$lead['kandidat_id'].'"
															class="fa fa-check switch_seller"
															style="color:white; background:#5cb85c; font-size:24px; display:flex; float:left; padding:4px; cursor:pointer;" 
															aria-hidden="true"
														>
														</i>
														<div style="width:75%; display:inline-block" id = "parent_'.$lead['kandidat_id'].'"><select class="selectpicker seller_picker" candidate_id = "'.$lead['kandidat_id'].'" id = "seller_picker_'.$lead['kandidat_id'].'">'.$select_seller.'
													</td>
												';
											echo "</tr>";
										}
									?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
				<?php
		break;
		case "new_account":
			?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-users idk_color_green" aria-hidden="true"></i> Novi partner račun</h1>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div class="modal material-modal material-modal_primary fade" id="edit_makler">
				<div class="modal-dialog">
					<div class="modal-content material-modal__content">
						<div class="modal-header material-modal__header">
							<button class="close material-modal__close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title material-modal__title">Uređivanje maklera</h4>
						</div>
						<form action="<?php getSiteURL(); ?>do.php?form=edit_makler" method="post" http-equiv="Content-type" enctype="multipart/form-data" charset="UTF-8" class="form-horizontal" >
							<div class="modal-body material-modal__body">	
								<div class="form-group">
									<label for="edit_makler_firstname" class="col-sm-3 control-label"><span class="text-danger">*</span> Ime:</label>
									<div class="col-sm-9">
										<div class="materail-input-block materail-input-block_success">
											<input class="form-control materail-input" type="text" name="edit_makler_firstname" id="edit_makler_firstname" placeholder="Ime" required>
											<span class="materail-input-block__line"></span>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label for="edit_makler_lastname" class="col-sm-3 control-label"><span class="text-danger">*</span> Prezime:</label>
									<div class="col-sm-9">
										<div class="materail-input-block materail-input-block_success">
											<input class="form-control materail-input" type="text" name="edit_makler_lastname" id="edit_makler_lastname" placeholder="Prezime" required>
											<span class="materail-input-block__line"></span>
										</div>
									</div>
								</div>
								<input type="hidden" id = "actual_id" name = "actual_id" actual_id = "">
								<div class="form-group">
									<label for="edit_makler_id" class="col-sm-3 control-label"><span class="text-danger">*</span> Makler ID:</label>
									<div class="col-sm-9">
										<div class="materail-input-block materail-input-block_success">
											<input class="form-control materail-input" name="edit_makler_id" id="edit_makler_id" placeholder="-" required>
											<span class="materail-input-block__line"></span>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label for="edit_makler_broj_direkcije" class="col-sm-3 control-label"><span class="text-danger">*</span> Broj Direkcije:</label>
									<div class="col-sm-9">
										<div class="materail-input-block materail-input-block_success">
											<input class="form-control materail-input" name="edit_makler_broj_direkcije" id="edit_makler_broj_direkcije" placeholder="-" required>
											<span class="materail-input-block__line"></span>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label for="edit_makler_language" class="col-sm-3 control-label">Jezik:</label>
									<div class="col-sm-9">
										<select class="selectpicker" id="edit_makler_language" name="edit_makler_language">
											<option value="de">Njemački</option>
											<option value="en">Engleski</option>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label for="edit_makler_representative_employee" class="col-sm-3 control-label">Representative:</label>
									<div class="col-sm-9">
										<select class="selectpicker" id="edit_makler_representative_employee" name="edit_makler_representative_employee">
											<?php
												$get_employees = $db -> prepare('
													SELECT employee_id, employee_firstname, employee_lastname
													FROM idk_employees
													WHERE employee_makler_representative_status IN (1,2)
												');
												$get_employees -> execute();
												while($employees = $get_employees -> fetch()){
													$employee_id = $employees['employee_id'];
													$employee_firstname = $employees['employee_firstname'];
													$employee_lastname = $employees['employee_lastname'];
													echo '<option value = "'.$employee_id.'">'.$employee_firstname.' '.$employee_lastname.'</option>';
												}
											?>
										</select>
									</div>
								</div>
								
							</div>
							<div class="modal-footer material-modal__footer">
								<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
								<button class="btn btn-primary material-btn material-btn_primary submit_btn" type="submit" >SPREMI</button>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<div class="row">
							<div class="col-xs-12">
								<div id="myTabs" class="panel-group material-tabs-group">
									<ul class="nav nav-tabs material-tabs material-tabs_primary">
										<li class="active"><a href="#existing_accounts" class="material-tabs__tab-link" data-toggle="tab">Postojeći računi</a></li>
										<li><a href="#new_account" class="material-tabs__tab-link" data-toggle="tab">Novi račun</a></li>
										<li><a href="#representatives" class="material-tabs__tab-link" data-toggle="tab">Representatives</a></li>
									</ul>
									<div class="tab-content materail-tabs-content">
										<div class="tab-pane fade in active" id="existing_accounts">
											<div class="row">
												<div class="col-md-12">
													<div class="content_box">
														<div class="col-md-4">
															<label class="col-md-3" for="filter_company">Kompanije:</label>
															<div class = "col-md-9">
																<select id="filter_company" class="selectpicker" name="filter_company" multiple>
																	<?php
																	$get_partner_companies = $db -> prepare("
																		SELECT pc_id, pc_name
																		FROM idk_partner_companies
																	");
																	$get_partner_companies -> execute();
																	while($row_partner_company = $get_partner_companies -> fetch()){
																		$selected_text = "selected";
																		if($row_partner_company['pc_id'] == 4){
																			$selected_text = "";
																		}
																		echo '<option '.$selected_text.' value="'.$row_partner_company['pc_id'].'">'.$row_partner_company['pc_name'].'</option>';
																	}
																	?>
															</select>
															</div>
														</div>
														<br><hr>
														<div class="row" id = "append_accounts_table">
															<script>
																$(document).ready(function() {

																	getAccountsTable();
																	$('#filter_company').on('change', getAccountsTable);

																	function resetPassword(){
																		var id = $(this).attr('jp_id');
																		var email = $(this).attr('jp_mail');
																		var fullname = $(this).attr('fullname');

																		$.ajax({
																			url: "../ajax_data.php?page=reset_password",
																			type: "POST",
																			dataType: "html",
																			data: {
																				id: id,
																				email: email,
																				fullname: fullname
																			},
																			success: function (data) {
																				location.reload();
																			},
																			error: function (xhr, ajaxOptions, thrownError) {
																				alert(xhr.status);
																				alert(thrownError);
																			},
																		});

																	}

																	function accountControll(){
																		var id = $(this).attr('jp_id');
																		var is_active = $(this).attr('is_active');
																		
																		$.ajax({
																			url: "../ajax_data.php?page=account_controll",
																			type: "POST",
																			dataType: "html",
																			data: {
																				id: id,
																				is_active: is_active,
																			},
																			success: function (data) {
																				location.reload();
																			},
																			error: function (xhr, ajaxOptions, thrownError) {
																				alert(xhr.status);
																				alert(thrownError);
																			},
																		});
																		
																	}
																	
																	function getAccountsTable(){
																		var selected_companies = $('#filter_company').val();
																		$('#append_accounts_table').hide('fade', function(){
																			$.ajax({
																				url: "../ajax_data.php?page=get_partner_accounts_list",
																				type: "POST",
																				dataType: "html",
																				data: {
																					selected_companies: selected_companies
																				},
																				success: function (data) {
																					$('#append_accounts_table').empty().append(data).show('fade', 1000);
																					var table = $('#accounts_list').DataTable({
																						responsive: true,

																						"order": [[ 0, "desc" ]],
																						"drawCallback": function( settings ) {
																							$('.password_reset').unbind().bind('click', resetPassword);
																							$('.account_controll').unbind().bind('click', accountControll);
																							$('.edit_makler').unbind().bind('click', editMaklerLoad);
																						},
																					});
																				},
																				error: function (xhr, ajaxOptions, thrownError) {
																					alert(xhr.status);
																					alert(thrownError);
																				},
																			});
																		});
																	}

																	function editMaklerLoad(){
																		$("#edit_makler").modal("show");
																		var firstname = $(this).attr('firstname');
																		var lastname = $(this).attr('lastname');
																		var language = $(this).attr('language');
																		var makler_id = $(this).attr('makler_id');
																		var broj_direkcije = $(this).attr('broj_direkcije');
																		var representative_employee = $(this).attr('representative_employee');
																		var actual_id = $(this).attr('actual_id');

																		$('#edit_makler_firstname').val(firstname);
																		$('#edit_makler_lastname').val(lastname);
																		$('#edit_makler_language').selectpicker('val', language);
																		$('#edit_makler_id').val(makler_id);
																		$('#edit_makler_broj_direkcije').val(broj_direkcije);
																		$('#edit_makler_representative_employee[name=edit_makler_representative_employee]').val(representative_employee);
																		$('#edit_makler_representative_employee').selectpicker('refresh');
																		$('#actual_id').val(actual_id);
																	}
																});
															</script>
															
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="tab-pane fade in" id="new_account">
											<div class="row">
												<div class="col-md-12">
													<div class="content_box">
														<div class="row">
															<div class="col-md-offset-1 col-md-8">
																<form id="idk_form" action="<?php getSiteURL(); ?>do.php?form=new_partner_account" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
																	<div class="form-group">
																		<label for="makler_lastname" class="col-sm-3 control-label"><span class="text-danger">*</span> Ime:</label>
																		<div class="col-sm-9">
																			<div class="materail-input-block materail-input-block_success">
																				<input class="form-control materail-input" type="text" name="makler_firstname" id="makler_firstname" placeholder="Ime" required>
																				<span class="materail-input-block__line"></span>
																			</div>
																		</div>
																	</div>
																	<div class="form-group">
																		<label for="makler_lastname" class="col-sm-3 control-label"><span class="text-danger">*</span> Prezime:</label>
																		<div class="col-sm-9">
																			<div class="materail-input-block materail-input-block_success">
																				<input class="form-control materail-input" type="text" name="makler_lastname" id="makler_lastname" placeholder="Prezime" required>
																				<span class="materail-input-block__line"></span>
																			</div>
																		</div>
																	</div>
																	<div class="form-group">
																		<label for="makler_email" class="col-sm-3 control-label"><span class="text-danger">*</span> Email:</label>
																		<div class="col-sm-9">
																			<div class="materail-input-block materail-input-block_success">
																				<input class="form-control materail-input" type="email" name="makler_email" id="makler_email" placeholder="Email" required>
																				<span class="materail-input-block__line"></span>
																			</div>
																		</div>
																	</div>
																	<div class="form-group">
																		<label for="makler_id" class="col-sm-3 control-label"><span class="text-danger">*</span> Makler ID:</label>
																		<div class="col-sm-9">
																			<div class="materail-input-block materail-input-block_success">
																				<input class="form-control materail-input" name="makler_id" id="makler_id" placeholder="-" required>
																				<span class="materail-input-block__line"></span>
																			</div>
																		</div>
																	</div>
																	<div class="form-group">
																		<label for="makler_broj_direkcije" class="col-sm-3 control-label"><span class="text-danger">*</span> Broj Direkcije:</label>
																		<div class="col-sm-9">
																			<div class="materail-input-block materail-input-block_success">
																				<input class="form-control materail-input" name="makler_broj_direkcije" id="makler_broj_direkcije" placeholder="-" required>
																				<span class="materail-input-block__line"></span>
																			</div>
																		</div>
																	</div>
																	<div class="form-group">
																		<label for="makler_company" class="col-sm-3 control-label"><span class="text-danger">*</span> Kompanija:</label>
																		<div class="col-sm-9">
																			<select class="selectpicker" id="makler_company" name="makler_company" required>
																			<option value=""></option>
																			<?php
																				$select_query = $db->prepare("
																					SELECT pc_id, pc_name
																					FROM idk_partner_companies
																				");

																				$select_query->execute();

																				while($select_row = $select_query->fetch()) {
																					echo '<option value="'. $select_row['pc_id'] .'">' . $select_row['pc_name'] . '</option>';
																				}
																			?>
																			</select>
																		</div>
																	</div>
																	<!-- <div class="form-group">
																		<label for="tutorial_file_name" class="col-sm-3 control-label"><span class="text-danger">*</span> Tutorial:</label>
																		<div class="col-sm-9">
																			<select class="selectpicker" id="tutorial_file_name" name="tutorial_file_name" required>
																			<option value=""></option>
																			<?php
																				// $select_query = $db->prepare("
																				// 	SELECT id, name, is_default, file_name
																				// 	FROM idk_partner_tutorials
																				// ");

																				// $select_query->execute();

																				// while($select_row = $select_query->fetch()) {
																				// 	if($select_row['is_default']){
																				// 		echo '<option selected="selected" value="' . $select_row['file_name'] . '">' . $select_row['name'] . '</option>';
																				// 	}
																				// 	else{
																				// 		echo '<option value="' . $select_row['file_name'] . '">' . $select_row['name'] . '</option>';
																				// 	}
																				// }
																			?>
																			</select>
																		</div>
																	</div> -->
																	<div class="form-group">
																		<label for="makler_language" class="col-sm-3 control-label">Jezik:</label>
																		<div class="col-sm-9">
																			<select class="selectpicker" id="makler_language" name="makler_language">
																				<option value="de">Njemački</option>
																				<option value="en">Engleski</option>
																			</select>
																		</div>
																	</div>
																	
																	<br />
																	<div class="form-group">
																		<div class="col-sm-offset-2 col-sm-10 text-right">
																			<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj</span></button>
																			<br /><small>Sva polja označena sa <span class="text-danger">*</span> su obavezna!</small>
																		</div>
																	</div>
																</form>
																<script>
																	$(document).ready(function() {
																		$('#idk_form').on('submit', function(e){
																			e.preventDefault();
																			var email = $('#makler_email').val();
																			var that = $(this);

																			$.ajax({
																				url: "../ajax_data.php?page=is_duplicate_partner_app_email",
																				type: "POST",
																				dataType: "html",
																				data: {
																					email: email,
																				},
																				success: function (data) {
																					if(data != "0"){
																						alert('Ta e-mail adresa već postoji');
																					}
																					else{
																						that.unbind("submit").submit()
																					}
																				},
																				error: function (xhr, ajaxOptions, thrownError) {
																					alert(xhr.status);
																					alert(thrownError);
																				},
																			});
																		});

																	});
																</script>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="tab-pane fade in" id="representatives">
											<div class="row">
												<div class="col-md-12">
													<div class="content_box">
														<div class="row">
															<div class="col-md-offset-1 col-md-8">
																<script>
																	$(document).ready(function() {
																		var table = $('#representatives_list').DataTable({
																			responsive: true,

																			"order": [[ 0, "desc" ]],
																			"drawCallback": function( settings ) {
																				$('.set_default_representative_employee').unbind().bind('click', setDefaultRepresentativeEmployee);
																			},
																		});

																		function setDefaultRepresentativeEmployee(){
																			var id = $(this).attr('representative_id');
																			var that = $(this);
																			$.ajax({
																				url: "../ajax_data.php?page=set_default_representative_employee",
																				type: "POST",
																				dataType: "html",
																				data: {
																					id: id,
																				},
																				success: function (data) {
																					location.reload();
																				},
																				error: function (xhr, ajaxOptions, thrownError) {
																					alert(xhr.status);
																					alert(thrownError);
																				},
																			});
																		}

																	});
																</script>
																<form id="idk_form" action="<?php getSiteURL(); ?>do.php?form=mark_new_representative" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
																	<div class="form-group">
																		<label for="employee_id" class="col-sm-3 control-label"> Dodaj novog uposlenika:</label>
																		<div class="col-sm-9">
																			<select class="selectpicker" id="employee_id" name="employee_id" required data-live-search="true">
																			<option value=""></option>
																			<?php
																				$select_query = $db->prepare("
																					SELECT employee_id, employee_firstname, employee_lastname
																					FROM idk_employees
																					WHERE employee_status != 0
																					AND employee_makler_representative_status = 0
																					ORDER BY employee_firstname, employee_lastname;
																				");

																				$select_query->execute();

																				while($select_row = $select_query->fetch()) {
																					echo '<option value="'. $select_row['employee_id'] .'">'.$select_row['employee_firstname'].' '.$select_row['employee_lastname'].'</option>';
																				}
																			?>
																			</select>
																		</div>
																	</div>
																	<br />
																	<div class="form-group">
																		<div class="col-sm-offset-2 col-sm-10 text-right">
																			<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj</span></button>
																		</div>
																	</div>
																</form>
																<hr>
																<table id="representatives_list" class="display" cellspacing="0" width="100%">
																	<thead>
																		<tr>
																			<th>Ime i prezime</th>
																			<th>Broj maklera</th>
																			<th>Def.</th>
																		</tr>
																	</thead>
																	<tbody>
																		<?php
																			$get_emoloyees = $db -> prepare('
																				SELECT employee_id, employee_firstname, employee_lastname, employee_makler_representative_status, count(jp_representative_employee_id) as makler_count
																				FROM idk_employees
																				LEFT JOIN idk_jobstep_partners 
																				ON jp_representative_employee_id = employee_id
																				WHERE employee_makler_representative_status IN (1,2)
																				GROUP BY employee_id;
																			');
																			
																			$get_emoloyees -> execute();

																			while($employees = $get_emoloyees -> fetch()){

																				$employee_id = $employees['employee_id'];
																				$employee_firstname = $employees['employee_firstname'];
																				$employee_lastname = $employees['employee_lastname'];
																				$employee_makler_representative_status = $employees['employee_makler_representative_status'];
																				$makler_count = $employees['makler_count'];
																				

																				$is_default_output = '<i class="fa fa-star-o set_default_representative_employee"  representative_id = "'.$employee_id.'" style = "cursor:pointer; color: #FFD700; font-size:25px" aria-hidden="true"></i>';
																				if($employee_makler_representative_status == 2){
																					$is_default_output = '<i class="fa fa-star default_employee_representative" representative_id = "'.$employee_id.'" style = "color: #FFD700; font-size:25px" aria-hidden="true"></i>';
																				}


																				echo "<tr>";
																				echo "<td>".$employee_firstname." ".$employee_lastname."</td>";
																				echo "<td>".$makler_count."</td>";
																				echo '<td data-order = "'.$employee_makler_representative_status.'" style = "text-align: center">'.$is_default_output.'</td>';
																				echo "</tr>";
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
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
		break;
			}
		?>
			<footer><?php getCopyright(); ?></footer>
		</div>
	</div>
</body>
</html>
<?php }else{
			echo '
				<br/>
				<div class="alert material-alert material-alert_danger">
					<h4>NEMATE PRIVILEGIJE!</h4>
					<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
					<br />
				</div>
';} ?>
