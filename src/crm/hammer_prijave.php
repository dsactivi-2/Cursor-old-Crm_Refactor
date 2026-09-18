<?php
	include("includes/functions.php");

	$getEmployeeStatus = getEmployeeStatus();

	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: hammer_prijave?page=pregled");
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
		<?php 	include('includes/head.php');  ?>
	</head>
	<body>
		
		<?php
				switch ($page){
					case "prijavna_forma":
		?>
						<style>
							#dizajn_vanjski_dio{
								border: 1px solid #cccccc;
								border-radius: 3.25rem;
								padding-left: 13px;
								padding-right: 13px;
							}
							#dizajn_vanjski_dio > .row:first-child{
								padding: 10px 30px 10px 30px;
								background-color: #6097a0;
								border-radius: 3.25rem 3.25rem 0.25rem 0.25rem;
								color: white;
								font-weight: bold;
								font-size: large;
								margin-top: -3px;
							}
							#dizajn_vanjski_dio > .row:nth-child(2){
								padding: 10px;
								padding-left: 15px;
								padding-right: 15px;
							}
							#content_public{
								/*height: 100vh;*/
							}
							body{
								/*height: 100vh;*/
							}
							@media screen and (max-width: 991px) {
								.img-responsive{
									max-width: 100px !important;
									margin-top: 10px !important;
									margin-bottom: 10px !important;
								}
							}
							.materail-input-block_success .materail-input-block__line {
								background-color: #6097a0;
							}
							.material-btn_success {
								background-color: #6097a0;
								border-color: #6097a0;
								color: #fff;
							}
							.material-btn-icon-success .fa {
								margin-right: 10px;
								background-color: #6097a0;
								padding: 10px 14px;
							}
							.material-btn_success:hover {
								color: #fff;
								background-color: #6097a0;
								border-color: #6097a0;
							}
							.material-btn_success:active {
								background-color: #046373;
								border-color: #046373;
								color: #fff;
							}
							#footer{
								//position: absolute;
								left: 0;
								bottom: 0;
								width: 100%;
								border-top: 1px solid #ccc;
								font-size: 12px;
								text-align: center;
								margin-top: 0px;
							}
						</style>
						<script>
							$(document).ready(function() {
								$("input").focus(function() {
									$('footer').hide();
								});


								$("input").blur(function(){
									$('footer').show();
								});
										
							});
							
							
						</script>
						<?php 
							$kandidat_id = $_GET['id'];
							
							//INSERT u projekt kako bi se pratio broj ljudi koji otvore poruku
							$query_check = $db->prepare("SELECT * FROM idk_project_kandidati
														WHERE pk_projectid IN (1510,1511,1512) AND pk_kandidatid = :pk_kandidatid");
							$query_check->execute(array(
											':pk_kandidatid' => $kandidat_id));
							
							if($query_check->rowCount() == 0){
								$query_project = $db->prepare("
												INSERT INTO idk_project_kandidati
													(pk_projectid, pk_kandidatid)
												VALUES
													(:pk_projectid, :pk_kandidatid)");

								$query_project->execute(array(
												':pk_projectid' => 1511,
												':pk_kandidatid' => $kandidat_id));
							}
							
							$get_jezik = $db->prepare("
											SELECT kj_slusanje FROM idk_kandidat_jezici WHERE kj_naziv = 'Njemački' AND kj_kandidatid = :kj_kandidatid
							");
							$get_jezik->execute(array(
											":kj_kandidatid" => $kandidat_id
							));
							
							$jezik_row= $get_jezik->fetch();
							$stari_jezik = $jezik_row['kj_slusanje'];
							if(in_array($stari_jezik, array('A2','B1','B2','C1','C2')))
								$jezik = 1;
							else 
								$jezik = 0;
							
							$get_skole = $db->prepare("
											SELECT idk_skole.skola_id, ke_naziv FROM idk_kandidat_edukacija 
											JOIN idk_skole ON ke_naziv = idk_skole.skola_naziv
											WHERE idk_skole.skola_id IN (1,5,13,37,45,47,48,52,59,60,77,85,81,79,76,72,70,63,51,25,7,4,2) AND ke_kandidat_id = :ke_kandidat_id
							");
							$get_skole->execute(array(
											":ke_kandidat_id" => $kandidat_id
							));
							
							$skole_row= $get_skole->fetch();
							$stara_skola_id = $skole_row['skola_id'];
							$ke_naziv = $skole_row['ke_naziv'];
							
							if($stara_skola_id == null)
								$skole = 0;
							else
								$skole = 1;
							
						?>
						<div id="content_public">
							<div class="container-fluid">
								<div class="content_box">
									<div class="row">
										<div class="col-xs-12 text-center">
											<img class="img-responsive" src="<?php getSiteURL(); ?>images/Jobstep_logo_new.png" alt="" style="max-width:200px; margin-top: 20px; margin-bottom: 20px;">
										</div>
									</div>
									<div class="row" style="margin-bottom: 50px;">
										<div class="col-md-12">
											<div class="row">
												<div class = "col-md-8 col-md-offset-2">
													<div class="row">
														<div id = "dizajn_vanjski_dio" class = "col-xs-12">
															<div class="row">
																<div class = "col-xs-12 text-center" style="padding: 12px;">
																	
																</div>
															</div>
															<div class="row" style = "padding-top: 25px;">
																<div class = "col-xs-12">
																	<form action="<?php getSiteURL(); ?>hammer_prijave?page=add_prijava" method="post" enctype="multipart/form-data" class="form-horizontal" charset="utf-8" role="form">
																		<input type="hidden" name="kandidat_id" value="<?php echo $kandidat_id; ?>">
																		<div class="form-group">
																			<div class = "col-md-8 col-md-offset-2">
																				<div class="col-md-12">
																					<p style = "text-align: justify;"> 
																						Utjecajni njemački poslodavac će početkom aprila održati veliki razgovor za posao na kome  ćeš baš ti dobiti šansu da svoju karijeru nastavite u Njemačkoj!
																					</p>
																				</div>
																			</div>
																		</div>
																		<?php if($skole == 0){ ?>
																		<input type="hidden" value="1" name="skola_check">
																		<div class="form-group">
																			<div class = "col-md-8 col-md-offset-2">
																				<label for="skola_id" class="col-sm-3 control-label"><span class="text-danger">*</span> Škola: </label>
																				<div class="col-sm-9">
																					<select class="selectpicker" id="skola_id" name="skola_id" placeholder = "Odeberite školu" required>
																						<option value="" selected disabled >Odaberi školu</option>
																					<?php 
																						$query = $db->prepare("
																							SELECT skola_id, skola_naziv
																							FROM idk_skole
																							WHERE skola_id IN (81,79,76,72,70,63,51,37,25,7,4,2,1)
																						");
																						//sve skole po starom slanju: 1,5,13,37,45,47,48,52,59,60,77,85,81,79,76,72,70,63,51,25,7,4,2
																						$query->execute();
																						while($row = $query->fetch()){
																							$id_s = $row["skola_id"];
																							$name_s = $row["skola_naziv"];
																							
																							echo '<option value = "'.$id_s.'">'.$name_s.'</option>';
																						}
																					?>
																						<option value="ostalo" >Ostalo</option>
																					</select>
																				</div>
																			</div>
																		</div>
																		<?php } else{ ?>
																			<input type="hidden" value="0" name="skola_check">
																			<input type="hidden" value="<?php echo $stara_skola_id; ?>" name="skola_id">
																		<?php } ?>
																		<br>
																		<?php if($jezik == 0){ ?>
																		<div class="form-group" id="poznavanje_da">
																			<div class = "col-md-8 col-md-offset-2">
																				<label for="nivo_jezika" class="col-sm-3 control-label"><span class="text-danger">*</span> Poznavanje Njemačkog jezika: </label>
																				<div class="col-sm-9">
																					<select class="selectpicker" id="nivo_jezika" name="nivo_jezika" required placeholder = "Unesite nivo jezika">
																					<option value="" selected disabled >Odaberi</option>
																					<option value="Bez znanja">Bez znanja</option>
																					<option value="A1">A1</option>
																					<option value="A2">A2</option>
																					<option value="B1">B1</option>
																					<option value="B2">B2</option>
																					<option value="C1">C1</option>
																					<option value="C2">C2</option>
																					</select>
																				</div>
																			</div>
																		</div>
																		<?php } else{ ?>
																			<input type="hidden" value="0" name="nivo_jezika">
																		<?php } ?>
																		
																		<div class="form-group" style = "margin-bottom: 0px;">
																			<div class="col-sm-offset-4 col-sm-4 text-center">
																				<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-sign-in" aria-hidden="true"></i> <span>Pošalji prijavu</span></button>
																				<br/><small>Sva polja označena sa <span class="text-danger">*</span> su obavezna!</small>
																			</div>
																		</div>
																	</form>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class = "row">
										<footer id = "footer">
											<p>
												©2020 Sva prava pridržana - Jobstep IT Solutions
											</p>
										</footer>
									</div>
								</div>
							</div>
						</div>
					<?php
		
					break;
					
					case "add_prijava":
						$kandidat_id = $_POST['kandidat_id'];
						$skola_check = $_POST['skola_check'];
						$skola_id = $_POST['skola_id'];
						$nivo_jezika = $_POST['nivo_jezika'];
						
						//Brisanje iz projekta "samo otvorili link"
						$query = $db->prepare("
										DELETE FROM idk_project_kandidati
										WHERE pk_projectid = :projectid
										AND pk_kandidatid = :kandidatid");

						$query->execute(array(
										':projectid' => 1511,
										':kandidatid' => $kandidat_id));
						
						//$nalog_id = 177;
						$nalog_id = 176;
						
						if($skola_id == "ostalo" OR $nivo_jezika == "Bez znanja" OR $nivo_jezika == "A1"){
							//INSERT u projekt za one koji NE ispunjavaju uslove
							$query_check = $db->prepare("SELECT * FROM idk_project_kandidati
														WHERE pk_projectid = :pk_projectid AND pk_kandidatid = :pk_kandidatid");
							$query_check->execute(array(
											':pk_projectid' => 1510,
											':pk_kandidatid' => $kandidat_id));
							
							if($query_check->rowCount() == 0){
								$query_project = $db->prepare("
												INSERT INTO idk_project_kandidati
													(pk_projectid, pk_kandidatid)
												VALUES
													(:pk_projectid, :pk_kandidatid)");

								$query_project->execute(array(
												':pk_projectid' => 1510,
												':pk_kandidatid' => $kandidat_id));
							}
						}else{
							if(in_array($skola_id, array(81,79,76,72,70,63,51,37,25,7,4,2,1))){
								//INSERT u projekt za Servisne tehnicare
								$query_check = $db->prepare("SELECT * FROM idk_project_kandidati
															WHERE pk_projectid = :pk_projectid AND pk_kandidatid = :pk_kandidatid");
								$query_check->execute(array(
												':pk_projectid' => 1512,
												':pk_kandidatid' => $kandidat_id));
								
								if($query_check->rowCount() == 0){
									$query_project = $db->prepare("
													INSERT INTO idk_project_kandidati
														(pk_projectid, pk_kandidatid)
													VALUES
														(:pk_projectid, :pk_kandidatid)");

									$query_project->execute(array(
													':pk_projectid' => 1512,
													':pk_kandidatid' => $kandidat_id));
								}
								$nalog_id = 176;
							}
							/*if(in_array($skola_id, array(1,5,13,37,45,47,48,52,59,60,77,85))){
								//INSERT u projekt za Građevinske radnike
								$query_check = $db->prepare("SELECT * FROM idk_project_kandidati
															WHERE pk_projectid = :pk_projectid AND pk_kandidatid = :pk_kandidatid");
								$query_check->execute(array(
												':pk_projectid' => 1475,
												':pk_kandidatid' => $kandidat_id));
								
								if($query_check->rowCount() == 0){
									$query_project = $db->prepare("
													INSERT INTO idk_project_kandidati
														(pk_projectid, pk_kandidatid)
													VALUES
														(:pk_projectid, :pk_kandidatid)");

									$query_project->execute(array(
													':pk_projectid' => 1475,
													':pk_kandidatid' => $kandidat_id));
								}
							}*/
						}
						if($skola_check == "1" && $skola_id != "ostalo"){
							
							//get naziv skole
							$query_get_skola = $db->prepare("SELECT skola_naziv FROM idk_skole
															WHERE skola_id = :skola_id");
							$query_get_skola->execute(array(
												':skola_id' => $skola_id));
							$row_skole = $query_get_skola->fetch();
							$skola_naziv = $row_skole['skola_naziv'];
							//insert skole
							$query_skola = $db->prepare("
											INSERT INTO idk_kandidat_edukacija
												(ke_naziv, ke_kandidat_id)
											VALUES
												(:ke_naziv, :ke_kandidat_id)");

							$query_skola->execute(array(
											':ke_naziv' => $skola_naziv,
											':ke_kandidat_id' => $kandidat_id));
						}
						if($nivo_jezika != "0" ){
							
							//da li ima unesen jezik
							$query_get_jezik = $db->prepare("SELECT kj_slusanje FROM idk_kandidat_jezici 
															WHERE kj_naziv = 'Njemački' AND kj_kandidatid = :kj_kandidatid");
							$query_get_jezik->execute(array(
												':kj_kandidatid' => $kandidat_id));
							
							$row_jezik = $query_get_jezik->fetch();
							$stari_jezik = $row_jezik['kj_slusanje'];
							//ako nema vec unesen jezika ide insert, ako ima ide update
							if($stari_jezik == null){
								
								$insert_jezik = $db->prepare("
												INSERT INTO idk_kandidat_jezici
													(kj_naziv, kj_slusanje, kj_citanje, kj_govorna_interakcija, kj_govorna_produkcija, kj_pisanje, kj_kandidatid)
												VALUES
													(:kj_naziv, :kj_slusanje, :kj_citanje, :kj_govorna_interakcija, :kj_govorna_produkcija, :kj_pisanje, :kj_kandidatid)");

								$insert_jezik->execute(array(
												':kj_naziv' => 'Njemački',
												':kj_slusanje' => $nivo_jezika,
												':kj_citanje' => $nivo_jezika,
												':kj_govorna_interakcija' => $nivo_jezika,
												':kj_govorna_produkcija' => $nivo_jezika,
												':kj_pisanje' => $nivo_jezika,
												':kj_kandidatid' => $kandidat_id
												));
							}else{
								
								$update_jezik = $db->prepare("
												UPDATE idk_kandidat_jezici
												SET kj_slusanje = :kj_slusanje, kj_citanje = :kj_citanje, kj_govorna_interakcija = :kj_govorna_interakcija, kj_govorna_produkcija = :kj_govorna_produkcija, kj_pisanje = :kj_pisanje
												WHERE kj_kandidatid = :kj_kandidatid AND kj_naziv = :kj_naziv");

								$update_jezik->execute(array(
												':kj_naziv' => 'Njemački',
												':kj_slusanje' => $nivo_jezika,
												':kj_citanje' => $nivo_jezika,
												':kj_govorna_interakcija' => $nivo_jezika,
												':kj_govorna_produkcija' => $nivo_jezika,
												':kj_pisanje' => $nivo_jezika,
												':kj_kandidatid' => $kandidat_id
												));
							}
						}
						
						header("Location: h_prijava/thank_you/$kandidat_id/$nalog_id");
						
					break;
					
					case "thank_you":
						$kandidat_id = $_REQUEST['id'];
						$nalog_id = $_REQUEST['nalog'];
						include("lang/bs.php");
						
						//check da li je kandidat instalirao messenger
						//get naziv skole
						$check_msn = $db->prepare("SELECT kandidat_status, kandidat_status_messenger FROM idk_kandidati WHERE kandidat_id = :kandidat_id");
						$check_msn->execute(array(':kandidat_id' => $kandidat_id));
						
						$row_msn = $check_msn->fetch();
						$kandidat_status = $row_msn['kandidat_status'];
						$kandidat_status_messenger = $row_msn['kandidat_status_messenger'];
						
						if($kandidat_status == 2 OR $kandidat_status == 4 OR $kandidat_status == 5){
							$slanje_msn = 0;
						}else{
							$slanje_msn = 1;
							
							$check_messenger = $db->prepare("
													SELECT id, email, phone
													FROM users
													WHERE kandidat_id = :kandidat_id ");
						
							$check_messenger->execute(array(
											':kandidat_id' => $kandidat_id
											));
						
							$nr_of_rows_mess = $check_messenger->rowCount();
							
							if($nr_of_rows_mess == 0){
								//nema ovakvih
							}else{
								$random_string = generateRandomString();
								$options = [
									'cost' => 10,
								];
								$random_password = password_hash($random_string, PASSWORD_BCRYPT, $options);
							
								$row_messenger = $check_messenger->fetch();
								$user_id = $row_messenger['id'];
								$bot_koriscnicko_ime = $row_messenger['email'];
								$mobile_phone = $row_messenger['phone'];
								
								$update_user = $db->prepare("
											UPDATE users
											SET nalog_id = :nalog_id, password = :password
											WHERE kandidat_id = :kandidat_id
										");

								$update_user->execute(array(
											':nalog_id' => $nalog_id,
											':password' => $random_password,
											':kandidat_id' => $kandidat_id));
								
								$phone_f = str_replace("+", '', $mobile_phone);
								$link_dload = "https://crm.job-step.com/download";
								$link_uputs = "https://bit.ly/3V177tF";
								$sms2 = $txt_sms2.$link_dload;
								$sms3 = $txt_sms3_1.$bot_koriscnicko_ime.$txt_sms3_2.$random_string.$txt_sms3_3;
								$viber3 = $txt_viber3_1.'\n'.$txt_viber3_2.$bot_koriscnicko_ime.'\n'.$txt_viber3_3.$random_string.'\n';
								$sms4 = $txt_sms4.$link_uputs;
								
								//sendSmsToCandidateInfobip1($random_string, $mobile_phone);
								viberPrijava1($phone_f, $txt_sms1, $txt_viber1);
								sleep(1);  // Seconds
								//sendSmsToCandidateInfobip2($random_string, $mobile_phone);
								viberPrijava2($phone_f, $sms2, $txt_viber2, $txt_btn1, $link_dload);
								sleep(1);  // Seconds
								//sendSmsToCandidateInfobip3($random_string, $mobile_phone, $bot_koriscnicko_ime);
								viberPrijava1($phone_f, $sms3, $viber3);
								sleep(1);  // Seconds
								//sendSmsToCandidateInfobip4($random_string, $mobile_phone, $bot_koriscnicko_ime);
								viberPrijava2($phone_f, $sms4, $txt_viber4, $txt_btn2, $link_uputs);
							
							}
						}
						
						?>
							<style>
								.col-xs-12 > .img-responsive{
									max-width: 90px !important;
									margin-top: 10px !important;
								}
								.alert > h3{
									margin-top: 0px !important;
									font-size: 19px !important;
								}
								.alert > h5{
									font-size: 15px !important;
									border-left: none !important; 
									margin-bottom: 0px !important;
								}
								.col-md-4 > iframe{
									margin-bottom: 10px;
								}
								
							</style>
							<div id="content_public">
								<div class="container">
									<div class="row">
										<div class="col-xs-12 text-center">
											<img class="img-responsive" src="<?php getSiteURL(); ?>images/Jobstep_logo_new.png" alt="" style="max-width:200px;">
										</div>
										<div class="col-xs-12">
											<br />
										</div>
									</div>
									<div class="row">
										<div class="col-md-6 col-md-offset-3 idk_margin_top10">
											<div style = "background-color: #ffffff; color: #6097a0; text-align: center;" class="alert material-alert material-alert_success">
												<h3><?php echo $txt_reg_zahvala; ?>
												</h3>
												<?php if($slanje_msn == 0){ ?>
												<h5><?php echo "Jobstep Team će Vas kontaktirati ubrzo za sve detalje."; ?></h5>
												<?php }elseif($slanje_msn == 1){ ?>
												<h5><?php echo $txt_reg_zahvala2; ?>
												</h5>
												<?php } ?>
											</div>
										</div>
									</div>
									<?php if($slanje_msn == 1){ ?>
									<div class="row">
										<div class="col-md-4 col-md-offset-4">
											<iframe width="100%" height="200" src="https://www.youtube.com/embed/rttgZz8ohEI" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6 col-md-offset-3 text-center">
											<div class="col-xs-8 col-xs-offset-2">
												<a href = "https://play.google.com/store/apps/details?id=net.job_step.jobstepmessenger">
													<img style = "width: 100%; " src = "<?php getSiteURL(); ?>files/kandidati/google-play-store-icon-android.png">
												</a>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6 col-md-offset-3 idk_margin_top10 text-center">
											<div class="col-xs-8 col-xs-offset-2">
												<a href = "https://apps.apple.com/hr/app/jobstep-messenger/id1486805317">
													<img  style = "width: 100%; " src = "<?php getSiteURL(); ?>files/kandidati/google-play-store-icon-ios.png">
												</a>
											</div>
										</div>
									</div>
									<?php } ?>
									<div class = "row">
										<footer id = "footer">
											<p>
												©2020 Sva prava pridržana - Jobstep IT Solutions
											</p>
										</footer>
									</div>
								</div>
							</div>
						<?php
						
					break;
				}
		?>
	</body>
</html>