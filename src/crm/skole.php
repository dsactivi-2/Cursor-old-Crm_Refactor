<?php
	include("includes/functions.php");
	include("includes/common.php");

	$getEmployeeStatus = explode( ',' , getEmployeeStatus());

	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: skole?page=pregled");
	}
?>
<!DOCTYPE html>
<!DOCTYPE html>
<html>
	<head>

		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>Škole | 
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
				<!--Switch START-->
				<?php
					switch($page)
					{
						case 'pregled':
				?>
						<div class = "row">
							<div class = "col-xs-8">
								<h1>
									<i class="fa fa-graduation-cap idk_color_green" aria-hidden="true" style = "margin-right: 10px;">
									</i>
									Škole
								</h1>
							</div>
							<div class = "col-xs-4 text-right idk_margin_top10">
								<a href="" data-toggle="modal" data-target="#modal_add_new_school" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive">
									<i class="fa fa-plus" aria-hidden="true">
									</i>
									<span>
										Dodaj školu
									</span>
								</a>
							</div>
						</div>
						
						<!-- Modal ADD NEW SCHOOL START -->
						
						<div class="modal material-modal material-modal_success fade" id="modal_add_new_school">
							<div class="modal-dialog modal-lg">
								<div class="modal-content material-modal__content">
									<div class="modal-header material-modal__header">
										<button class="close material-modal__close" data-dismiss="modal">&times;</button>
										<h4 class="modal-title material-modal__title">
											<i class="fa fa-plus" aria-hidden="true" style = "margin-right: 10px;">
											</i>
											Dodaj školu
										</h4>
									</div>
									<div class="modal-body material-modal__body">
										<div class = "row">
											<div class="col-md-8 col-md-offset-2">
												<form action="<?php getSiteURL(); ?>skole?page=add_new_school" method="post" http-equiv="Content-type" enctype="multipart/form-data"; charset = "utf-8" class="form-horizontal" id="form_add_new_school">
													<div class="form-group">
														<label for="name_school" class="col-sm-5 control-label">
															<span class="text-danger">
																*
															</span>
															Naziv škole:
														</label>
														<div class="col-sm-7">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" type="text" name="name_school" id="name_school" autocomplete="off" placeholder="Unesite naziv škole" required>
																<span class="materail-input-block__line">
																</span>
															</div>
														</div>
													</div>
													<div class="form-group">
														<label for="de_naziv_skole" class="col-sm-5 control-label">
															
															Njemački naziv škole:
														</label>
														<div class="col-sm-7">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" type="text" name="de_naziv_skole" id="de_naziv_skole" autocomplete="off" placeholder="Unesite njemački naziv škole" required>
																<span class="materail-input-block__line">
																</span>
															</div>
														</div>
													</div>
													<div class="form-group">
														<label for="type_school" class="col-sm-5 control-label">
															<span class="text-danger">
																*
															</span>
															Tip obrazovanja:
														</label>
														<div class="col-sm-7">
															<div class="">
																<select class="selectpicker" name="type_school" id = "type_school" title = "Odaberite tip obrazovanja" required>
																	<option value="srednje">Srednje</option>
																	<option value="visoko">Visoko</option>
																</select>
															</div>
														</div>
													</div>
													<div class="modal-footer material-modal__footer" style = "text-align: center;">
														<button class="btn material-btn material-btn" data-dismiss="modal">
															Odustani
														</button>
														<button type="submit" class="btn btn-primary material-btn material-btn_success" form="form_add_new_school">
															<i class="fa fa-check-square-o" aria-hidden="true" style = "margin-right: 10px;">
															</i>
															Završi
														</button>
													</div>
												</form>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<!-- Modal ADD NEW SCHOOL END -->
						
						<!-- LIST SCHOOL START -->
						
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
											<?php
												// if(isset($_GET['mess'])) {
													// $mess = $_GET['mess'];
												// }else{
													// $mess = 0;
												// }

												// if($mess == 1){
													// echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novog ND kandidata.</div>';
												// }elseif($mess == 2){
													// echo '<div class="alert material-alert material-alert_success">ND kandidat kojeg pokušavate dodati već se nalazi u bazi.</div>';
												// }elseif($mess == 3){
													// echo '<div class="alert material-alert material-alert_success">Uspješno ste stornirali ND kandidata.</div>';
												// }
											?>
											
											<script type="text/javascript">
												$(document).ready(function() {
													$('#skole_table').DataTable({

														responsive: true,

														"order": [[ 0, "desc" ]],

														 "bAutoWidth": false,

														"aoColumns": [
																{ "width": "5%" },
																{ "width": "30%" },
																{ "width": "35%" },
																{ "width": "10%" },
																{ "width": "10%", "bSortable": false },
																{ "width": "10%", "bSortable": false }
															]
													});
												} );
											</script>
											<table id="skole_table" class="display" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th class="text-center">#ID</th>
														<th>Naziv škole</th>
														<th>Njemački naziv škole</th>
														<th class="text-center">Tip obrazovanja</th>
														<th class="text-center">Pregled smjerova</th>
														<th class="text-center">Akcije</th>
													</tr>
												</thead>
												<tbody>
													<?php 
														$ispis_skola = $db->prepare("
																				SELECT *
																				FROM idk_skole
																				");
														$ispis_skola->execute();
														
														while($ispis_skola_row = $ispis_skola->fetch()){
															$id_skole = $ispis_skola_row['skola_id'];
															$naziv_skole = $ispis_skola_row['skola_naziv'];
															$naziv_skole_de = $ispis_skola_row['skola_naziv_de'];
															$tip_skole = $ispis_skola_row['skola_tip_obrazovanja'];
															
															if($tip_skole == 'visoko'){
																$tip_skole1 = '<span class="label label-primary material-label material-label_primary main-container__column text-left">VISOKO</span>';
															}
															else if($tip_skole == 'srednje'){
																$tip_skole1 = '<span class="label label-info material-label material-label_info main-container__column text-left">SREDNJE</span>';
															}
													?>
													<tr>
														<td class="text-center"><?php echo $id_skole; ?></td>
														<td style = "word-break: break-all;"><?php echo $naziv_skole; ?></td>
														<td style = "word-break: break-all;"><?php echo $naziv_skole_de; ?></td>
														<td class="text-center"><?php echo $tip_skole1; ?></td>
														<td class="text-center" style = "padding-top: 13px;">
															<a href="<?php getSiteURL(); ?>skole?page=smjerovi&id=<?php echo $id_skole; ?>" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive">
																<i class="fa fa-folder-open" aria-hidden="true"></i>
																<span>
																	Otvori
																</span>
															</a>
														</td>
														<td class="text-center">
															<div class="btn-group material-btn-group">
																<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_success idk_btn_table" data-toggle="dropdown">
																	<i class="fa fa-cogs fa-lg" aria-hidden="true">
																	</i> 
																	<span class="caret material-btn__caret">
																	</span>
																</button>
																<ul style = "top:32px; left: -40px; min-width: 150px !important;" class="dropdown-menu material-dropdown-menu material-dropdown-menu_success idk_dropdown_table" role="menu">
																	<li>
																		<a href="<?php getSiteURL(); ?>skole?page=edit_school&id=<?php echo $id_skole; ?>" class="material-dropdown-menu__link">
																			<i class="fa fa-pencil-square" style = "margin-right: 10px;" aria-hidden="true"></i>
																			Edit
																		</a>
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
						
						<!-- LIST SCHOOL END -->
						
						
				<?php
						break;
						
						case 'skola_nalog':
						
						$id_naloga = $_GET['id'];
						$get_nalog_naziv = $db->prepare("
													SELECT nalog_naziv
													FROM idk_nalozi
													WHERE nalog_id = :nalog_id
													");
													
						$get_nalog_naziv->execute(array(
													':nalog_id' => $id_naloga));
						$row_nalog = $get_nalog_naziv->fetch();
						$nalog_naziv = $row_nalog['nalog_naziv'];
						
						$get_projects = $db->prepare("SELECT project_id FROM idk_projects WHERE project_nalogid = $id_naloga");
						$get_projects->execute();
						$project_ids = array();
						while($row_projects = $get_projects->fetch()){
							array_push($project_ids, $row_projects['project_id']);
						}
						$project_ids = implode(",", $project_ids);
						$stmt = $db->prepare("SELECT pk_kandidatid FROM idk_project_kandidati WHERE pk_projectid IN ($project_ids)");
						$stmt->execute();
						$kandidat_id_project = array();
						while($result = $stmt->fetch()){
							$kandidat_id = $result['pk_kandidatid'];
							$kandidat_id_project[] = $kandidat_id;
						}
						$kandidat_id_project_unique = array_unique($kandidat_id_project);
						$kandidati_string = implode(",", $kandidat_id_project_unique);
						
						function getBrojPoSkoli($skola_id){
							Global $db;
							Global $kandidati_string;
							$query_bps = $db->prepare("SELECT COUNT(ke_id) as broj FROM idk_kandidat_edukacija WHERE ke_skola_id = $skola_id AND ke_kandidat_id IN ($kandidati_string)");
							$query_bps->execute();
							$row_bps = $query_bps->fetch();
							return $row_bps['broj'];
						}
						// var_dump($kandidat_id_project_unique);
						//echo $nalog_naziv;
				?>
						<div class = "row">
							<div class = "col-xs-8">
								<h1>
									<i class="fa fa-graduation-cap idk_color_green" aria-hidden="true" style = "margin-right: 10px;">
									</i>
									Škole
								</h1>
							</div>
						</div>
						
						<!-- LIST SCHOOL START -->
						
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
											<?php
												// if(isset($_GET['mess'])) {
													// $mess = $_GET['mess'];
												// }else{
													// $mess = 0;
												// }

												// if($mess == 1){
													// echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novog ND kandidata.</div>';
												// }elseif($mess == 2){
													// echo '<div class="alert material-alert material-alert_success">ND kandidat kojeg pokušavate dodati već se nalazi u bazi.</div>';
												// }elseif($mess == 3){
													// echo '<div class="alert material-alert material-alert_success">Uspješno ste stornirali ND kandidata.</div>';
												// }
											?>
											
											<script type="text/javascript">
												$(document).ready(function() {
													$('#skole_table').DataTable({

														responsive: true,

														"order": [[ 0, "desc" ]],

														 "bAutoWidth": false,

														"aoColumns": [
																{ "width": "5%" },
																{ "width": "30%" },
																{ "width": "30%" },
																{ "width": "5%" },
																{ "width": "10%" },
																{ "width": "10%", "bSortable": false },
																{ "width": "10%", "bSortable": false }
															]
													});
												} );
											</script>
											<table id="skole_table" class="display" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th class="text-center">#ID</th>
														<th>Naziv škole</th>
														<th>Njemački naziv škole</th>
														<th class="text-center">Broj</th>
														<th class="text-center">Tip obrazovanja</th>
														<th class="text-center">Pregled smjerova</th>
														<th class="text-center">Akcije</th>
													</tr>
												</thead>
												<tbody>
													<?php 
														$ispis_skola = $db->prepare("
																				SELECT *
																				FROM idk_skole
																				");
														$ispis_skola->execute();
														
														while($ispis_skola_row = $ispis_skola->fetch()){
															$id_skole = $ispis_skola_row['skola_id'];
															$naziv_skole = $ispis_skola_row['skola_naziv'];
															$naziv_skole_de = $ispis_skola_row['skola_naziv_de'];
															$tip_skole = $ispis_skola_row['skola_tip_obrazovanja'];
															
															if($tip_skole == 'visoko'){
																$tip_skole1 = '<span class="label label-primary material-label material-label_primary main-container__column text-left">VISOKO</span>';
															}
															else if($tip_skole == 'srednje'){
																$tip_skole1 = '<span class="label label-info material-label material-label_info main-container__column text-left">SREDNJE</span>';
															}
													?>
													<tr>
														<td class="text-center"><?php echo $id_skole; ?></td>
														<td style = "word-break: break-all;"><?php echo $naziv_skole; ?></td>
														<td style = "word-break: break-all;"><?php echo $naziv_skole_de; ?></td>
														<td class="text-center"><?php echo getBrojPoSkoli($id_skole); ?></td>
														<td class="text-center"><?php echo $tip_skole1; ?></td>
														<td class="text-center" style = "padding-top: 13px;">
															<a href="<?php getSiteURL(); ?>skole?page=smjer_nalog&id=<?php echo $id_skole; ?>&nalog_id=<?php echo $id_naloga; ?>" target="_blank" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive">
																<i class="fa fa-folder-open" aria-hidden="true"></i>
																<span>
																	Otvori
																</span>
															</a>
														</td>
														<td class="text-center">
															<div class="btn-group material-btn-group">
																<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_success idk_btn_table" data-toggle="dropdown">
																	<i class="fa fa-cogs fa-lg" aria-hidden="true">
																	</i> 
																	<span class="caret material-btn__caret">
																	</span>
																</button>
																<ul style = "top:32px; left: -40px; min-width: 150px !important;" class="dropdown-menu material-dropdown-menu material-dropdown-menu_success idk_dropdown_table" role="menu">
																	<li>
																		<a href="<?php getSiteURL(); ?>skole?page=edit_school&id=<?php echo $id_skole; ?>" target="_blank" class="material-dropdown-menu__link">
																			<i class="fa fa-pencil-square" style = "margin-right: 10px;" aria-hidden="true"></i>
																			Edit
																		</a>
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
						
						<!-- LIST SCHOOL END -->
						
						
				<?php
						break;
						
						case 'edit_school':
						if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "1" , $employee_status))){
							
						$id_skole_editt = $_GET['id'];
						$uzmi_naziv_skole_edit = $db->prepare("
													SELECT skola_naziv
													FROM idk_skole
													WHERE skola_id = :skola_id
													");
													
						$uzmi_naziv_skole_edit->execute(array(
													':skola_id' => $id_skole_editt));
														
						$uzmi_naziv_skole_edit_row = $uzmi_naziv_skole_edit->fetch();
						$naziv_skole_naslov_edit = $uzmi_naziv_skole_edit_row['skola_naziv'];
				?>
						<div class = "row">
							<div class = "col-xs-8">
								<h1>
									<i class="fa fa-pencil-square idk_color_green" aria-hidden="true" style = "margin-right: 10px;">
									</i>
									Uredi: <?php echo $naziv_skole_naslov_edit; ?>
								</h1>
							</div>
							<div class = "col-xs-4 text-right idk_margin_top10">
								<a href="<?php getSiteURL(); ?>skole?page=pregled" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive">
									<i class="fa fa-chevron-left" aria-hidden="true"></i> 
									<span>
										Povratak
									</span>
								</a>
							</div>
						</div>
						<?php
							$ispis_skola_edit = $db->prepare("
													SELECT *
													FROM idk_skole
													WHERE skola_id = :skola_id
													");
							$ispis_skola_edit->execute(array(
													':skola_id' => $id_skole_editt));
							
							$ispis_skola_edit_row = $ispis_skola_edit->fetch();
							$id_skole_edit = $ispis_skola_edit_row['skola_id'];
							$naziv_skole = $ispis_skola_edit_row['skola_naziv'];
							$naziv_skole_de = $ispis_skola_edit_row['skola_naziv_de'];
							$tip_skole = $ispis_skola_edit_row['skola_tip_obrazovanja'];
						?>
						<div class="row" style = "margin-top: 20px;">
							<div class="col-md-12">
								<div class="content_box">
									<div class="row">
										<div class="col-xs-12">
											<hr>
										</div>
									</div>
									<div class="row">
										<div class="col-xs-8 col-xs-offset-2 text-center">
											<form action="<?php getSiteURL(); ?>skole?page=edit_school_add" method="post" http-equiv="Content-type" enctype="multipart/form-data"; charset = "utf-8" class="form-horizontal">
												<input type = "hidden" name = "id_school_edit1" value = "<?php echo $id_skole_edit; ?>"/>
												<div class="form-group">
													<label for="name_school_edit" class="col-sm-5 control-label">
														<span class="text-danger">
															*
														</span>
														Naziv škole:
													</label>
													<div class="col-sm-7">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" value = "<?php echo $naziv_skole;?>" name="name_school_edit" id="name_school_edit" autocomplete="off" placeholder="Unesite naziv škole" required>
															<span class="materail-input-block__line">
															</span>
														</div>
													</div>
												</div>
												<div class="form-group">
													<label for="name_school_de" class="col-sm-5 control-label">
														Njemački naziv škole:
													</label>
													<div class="col-sm-7">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" value = "<?php echo $naziv_skole_de;?>" name="name_school_de" id="name_school_de" autocomplete="off" placeholder="Unesite naziv škole" >
															<span class="materail-input-block__line">
															</span>
														</div>
													</div>
												</div>
												<div class="form-group">
													<label for="type_school_edit" class="col-sm-5 control-label">
														<span class="text-danger">
															*
														</span>
														Tip obrazovanja:
													</label>
													<div class="col-sm-7">
														<div class="">
															<select class="selectpicker" name="type_school_edit" id = "type_school_edit" title = "Odaberite tip obrazovanja" required>
																<option <?php if($tip_skole == 'srednje') echo 'selected';?> value="srednje">Srednje</option>
																<option <?php if($tip_skole == 'visoko') echo 'selected';?> value="visoko">Visoko</option>
															</select>
														</div>
													</div>
												</div>
												<div class="form-group" style = "margin-top: 193px; ">
													<div class="col-sm-4 col-sm-offset-4 text-center">
														<ul class="list-inline">
															<li><button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column">
																	<i class="fa fa-save" aria-hidden="true"></i> 
																	<span>
																		Spremi promjene 
																	</span>
																</button>
															</li>
														</ul>
														<small>Sva polja označena sa <span class="text-danger">*</span> su obavezna!</small>
													</div>
												</div>
											</form>
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
						
						case 'add_new_school':
						
							$naziv_skole_add = $_POST['name_school'];
							$de_naziv_skole = $_POST['de_naziv_skole'];
							$tip_skole_add = $_POST['type_school'];
							
							// var_dump('Naziv: '.$naziv_skole_add.' Tip obrazovanja: '.$tip_skole_add.' ');
							// exit();
							
							//Save
							$query = $db->prepare("
											INSERT INTO idk_skole
												(skola_naziv, skola_naziv_de, skola_tip_obrazovanja)
											VALUES
												(:skola_naziv, :skola_naziv_de, :skola_tip_obrazovanja)");

							$query->execute(array(
											':skola_naziv' => $naziv_skole_add,
											':skola_naziv_de' => $de_naziv_skole,
											':skola_tip_obrazovanja' => $tip_skole_add
											));
							$skola_id = $db->lastInsertId();		
							$log_desc = "SKOLE - Zaposlenik je dodao skolu ID = [".$skola_id."], Naziv = [".$naziv_skole_add."] i Tip = [".$tip_skole_add."].";
							$log_type = "0";
							addToLogs($log_desc, $log_type);
							
							header("Location: " . getSiteURLr() . "skole?page=pregled");
							
						break;
						
						case 'edit_school_add':
							$id_skole_edit_add = $_POST['id_school_edit1'];
							$naziv_skole_edit_add = $_POST['name_school_edit'];
							$naziv_skole_de = $_POST['name_school_de'];
							if($naziv_skole_de == "")
								$naziv_skole_de = null;
							$tip_skole_edit_add = $_POST['type_school_edit'];
							// var_dump('ID: '.$id_skole_edit_add .' Naziv: '.$naziv_skole_de.' Tip obrazovanja: '.$tip_skole_edit_add.' ');
							// var_dump($naziv_skole_de);
							// exit();
							//Save
							$query = $db->prepare("
											UPDATE idk_skole
											SET skola_naziv = :skola_naziv, skola_naziv_de = :skola_naziv_de, skola_tip_obrazovanja = :skola_tip_obrazovanja
											WHERE skola_id = :skola_id");

							$query->execute(array(
										':skola_id' => $id_skole_edit_add,
										':skola_naziv' => $naziv_skole_edit_add,
										':skola_naziv_de' => $naziv_skole_de,
										':skola_tip_obrazovanja' => $tip_skole_edit_add 
										));
							
							$log_desc = "SKOLE - Uredio skolu sa ID = [".$id_skole_edit_add."].";
							$log_type = "0";
							addToLogs($log_desc, $log_type);
							header("Location: " . getSiteURLr() . "skole?page=pregled");
						break;
						
						case 'smjerovi':
						$id_otvorenog_smjer = $_GET['id'];
						$uzmi_naziv_skole = $db->prepare("
													SELECT skola_naziv
													FROM idk_skole
													WHERE skola_id = :skola_id
													");
													
						$uzmi_naziv_skole->execute(array(
													':skola_id' => $id_otvorenog_smjer));
														
						$uzmi_naziv_skole_row = $uzmi_naziv_skole->fetch();
						$naziv_skole_naslov = $uzmi_naziv_skole_row['skola_naziv'];
				?>
						<div class = "row">
							<div class = "col-xs-8">
								<h1>
									<i class="fa fa-graduation-cap idk_color_green" aria-hidden="true" style = "margin-right: 10px;">
									</i>
									Smjerovi: <?php echo $naziv_skole_naslov; ?>
								</h1>
							</div>
							<div class = "col-xs-4 text-right idk_margin_top10">
								<a href="" data-toggle="modal" data-target="#modal_add_new_school_direction" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive">
									<i class="fa fa-plus" aria-hidden="true">
									</i>
									<span>
										Dodaj smjer
									</span>
								</a>
								<a href="<?php getSiteURL(); ?>skole?page=pregled" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive">
									<i class="fa fa-chevron-left" aria-hidden="true"></i> 
									<span>
										Povratak
									</span>
								</a>
							</div>
						</div>
						
						<!-- Modal ADD NEW SCHOOL DIRECTION START -->
						
						<div class="modal material-modal material-modal_success fade" id="modal_add_new_school_direction">
							<div class="modal-dialog modal-lg">
								<div class="modal-content material-modal__content">
									<div class="modal-header material-modal__header">
										<button class="close material-modal__close" data-dismiss="modal">&times;</button>
										<h4 class="modal-title material-modal__title">
											<i class="fa fa-plus" aria-hidden="true" style = "margin-right: 10px;">
											</i>
											Dodaj smjer: <?php echo $naziv_skole_naslov; ?>
										</h4>
									</div>
									<div class="modal-body material-modal__body">
										<div class = "row">
											<div class="col-md-8 col-md-offset-2">
												<form action="<?php getSiteURL(); ?>skole?page=add_new_school_direction" method="post" http-equiv="Content-type" enctype="multipart/form-data"; charset = "utf-8" class="form-horizontal" id="form_add_new_school_direction">
													<input type = "hidden" name = "id_school" value = "<?php echo $id_otvorenog_smjer; ?>"/>
													<div class="form-group">
														<label for="name_school_direction" class="col-sm-5 control-label">
															<span class="text-danger">
																*
															</span>
															Naziv smjera:
														</label>
														<div class="col-sm-7">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" type="text" name="name_school_direction" id="name_school_direction" autocomplete="off" placeholder="Unesite naziv smjera" required>
																<span class="materail-input-block__line">
																</span>
															</div>
														</div>
													</div>
													<div class="form-group">
														<label for="add_smjer_de" class="col-sm-5 control-label">
															<span class="text-danger">
																*
															</span>
															Njemački naziv smjera:
														</label>
														<div class="col-sm-7">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" type="text" name="add_smjer_de" id="add_smjer_de" autocomplete="off" placeholder="Unesite njemački naziv smjera" required>
																<span class="materail-input-block__line">
																</span>
															</div>
														</div>
													</div>
													<div class="form-group">
														<label for="time_school_direction" class="col-sm-5 control-label">
															<span class="text-danger">
																*
															</span>
															Period trajanja:
														</label>
														<div class="col-sm-7">
															<div class="">
																<select class="selectpicker" name="time_school_direction" id = "time_school_direction" title = "Odaberite period trajanja obrazovanja" required>
																	<option value="3">3 godine</option>
																	<option value="4">4 godine</option>
																	<option value="5">5 godina</option>
																	<option value="6">6 godina</option>
																</select>
															</div>
														</div>
													</div>
													<div class="modal-footer material-modal__footer" style = "text-align: center;">
														<button class="btn material-btn material-btn" data-dismiss="modal">
															Odustani
														</button>
														<button type="submit" class="btn btn-primary material-btn material-btn_success" form="form_add_new_school_direction">
															<i class="fa fa-check-square-o" aria-hidden="true" style = "margin-right: 10px;">
															</i>
															Završi
														</button>
													</div>
												</form>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<!-- Modal ADD NEW SCHOOL DIRECTION END -->
						
						<!-- LIST SCHOOL DIRECTION START -->
						
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
											<?php
												// if(isset($_GET['mess'])) {
													// $mess = $_GET['mess'];
												// }else{
													// $mess = 0;
												// }

												// if($mess == 1){
													// echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novog ND kandidata.</div>';
												// }elseif($mess == 2){
													// echo '<div class="alert material-alert material-alert_success">ND kandidat kojeg pokušavate dodati već se nalazi u bazi.</div>';
												// }elseif($mess == 3){
													// echo '<div class="alert material-alert material-alert_success">Uspješno ste stornirali ND kandidata.</div>';
												// }
											?>
											
											<script type="text/javascript">
												$(document).ready(function() {
													$('#skole_smjer_table').DataTable({

														responsive: true,

														"order": [[ 0, "desc" ]],

														 "bAutoWidth": false,

														"aoColumns": [
																{ "width": "5%" },
																{ "width": "20%" },
																{ "width": "25%" },
																{ "width": "20%" },
																{ "width": "20%" },
																{ "width": "10%", "bSortable": false }
															]
													});
												} );
											</script>
											<table id="skole_smjer_table" class="display" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th class="text-center">#ID</th>
														<th>Naziv smjera</th>
														<th>Njemački naziv smjera</th>
														<th>Struka</th>
														<th class="text-center">Trajanje obrazovanja</th>
														<th class="text-center">Akcije</th>
													</tr>
												</thead>
												<tbody>
													<?php
													$redni_broj = 1;
														$ispis_smjerova = $db->prepare("
																				SELECT *
																				FROM idk_skole_smjerovi
																				WHERE ss_skola_id=:ss_skola_id
																				");
														$ispis_smjerova->execute(array(
																				':ss_skola_id' => $id_otvorenog_smjer
														));
														
														while($ispis_smjerova_row = $ispis_smjerova->fetch()){
															
															$id_struke_smjera = $ispis_smjerova_row['ss_struka_id'];
															$id_smjera = $ispis_smjerova_row['ss_id'];
															$naziv_smjera = $ispis_smjerova_row['ss_naziv'];
															$naziv_smjera_de = $ispis_smjerova_row['ss_naziv_de'];
															$trajanje_smjera = $ispis_smjerova_row['ss_trajanje_skole'];
															if($trajanje_smjera < 5){
																$trajanje_smjera1 = '<span class="label label-info material-label material-label_info main-container__column text-left">'.$trajanje_smjera.' godine</span>';
															}
															else if($trajanje_smjera > 4){
																$trajanje_smjera1 = '<span class="label label-info material-label material-label_info main-container__column text-left">'.$trajanje_smjera.' godina</span>';
															}
															
														$ispis_struke = $db->prepare("
																					SELECT naziv_struke
																					FROM idk_struke
																					WHERE id_struke = :id_struke
																					");
														$ispis_struke->execute(array(
																				':id_struke' => $id_struke_smjera
																				));
														$ispis_struke_row = $ispis_struke->fetch();
														$naziv_struke_smjera = $ispis_struke_row['naziv_struke'];
															
													?>
													<tr>
														<td class="text-center"><?php echo $redni_broj++; ?></td>
														<td style = "word-break: break-all;"><?php echo $naziv_smjera; ?></td>
														<td style = "word-break: break-all;"><?php echo $naziv_smjera_de; ?></td>
														<td style = "word-break: break-all;"><?php echo $naziv_struke_smjera; ?></td>
														<td class="text-center"><?php echo $trajanje_smjera1; ?></td>
														<td class="text-center">
															<div class="btn-group material-btn-group">
																<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_success idk_btn_table" data-toggle="dropdown">
																	<i class="fa fa-cogs fa-lg" aria-hidden="true">
																	</i> 
																	<span class="caret material-btn__caret">
																	</span>
																</button>
																<ul style = "top:32px; left: -40px; min-width: 150px !important;" class="dropdown-menu material-dropdown-menu material-dropdown-menu_success idk_dropdown_table" role="menu">
																	<li>
																		<a href="<?php getSiteURL(); ?>skole?page=edit_school_direction&ids=<?php echo $id_otvorenog_smjer;?>&idss=<?php echo $id_smjera; ?>" class="material-dropdown-menu__link">
																			<i class="fa fa-pencil-square" style = "margin-right: 10px;" aria-hidden="true"></i>
																			Edit
																		</a>
																	</li>
															<?php
															if($logged_employee_id == 67 or $logged_employee_id == 222 or $logged_employee_id == 43){
																if($id_struke_smjera == null){
															?>
																	<li>
																		<a href="" data-toggle="modal" data-id="<?php echo $id_smjera; ?>" data-name = "<?php echo $naziv_smjera; ?>" data-target="#modal_add_new_profession_direction" class="material-dropdown-menu__link">
																			<i class="fa fa-pencil-square" style = "margin-right: 10px;" aria-hidden="true"></i>
																			Veži za struku
																		</a>
																	</li>
																</ul>
															<?php 
																}
															}
															?>
															</div>
														</td>
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
						
						<!-- LIST SCHOOL DIRECTION END -->
						
						<script>
						$(document).on("click", ".material-dropdown-menu__link", function () {
							var smjerId = $(this).data('id');
							var nazivSmjera = $(this).data('name');
							var title = "Veži struku za: " + nazivSmjera;
							$(".modal-body #id_smjera").val( smjerId );
							$(".modal-header #title").html( title );
						});
						</script>
						
						<!-- Modal ADD PROFESSION DIRECTION START -->
						
						<div class="modal material-modal material-modal_success fade" id="modal_add_new_profession_direction">
							<div class="modal-dialog modal-lg">
								<div class="modal-content material-modal__content">
									<div class="modal-header material-modal__header">
										<button class="close material-modal__close" data-dismiss="modal">&times;</button>
										<h4 class="modal-title material-modal__title" id = "title">
											<i class="fa fa-plus" aria-hidden="true" style = "margin-right: 10px;">
											</i>
											
										</h4>
									</div>
									<div class="modal-body material-modal__body">
										<div class = "row">
											<div class="col-md-8 col-md-offset-2">
												<form action="<?php getSiteURL(); ?>skole?page=vezi_smjer" method="post" http-equiv="Content-type" enctype="multipart/form-data"; charset = "utf-8" class="form-horizontal" id="form_vezi_smjer">
													<input type = "hidden" name = "id_smjera" id = "id_smjera" value = ""/>
													<input type = "hidden" name = "id_skole" value = "<?php echo $id_otvorenog_smjer; ?>"/>
													<div class="form-group">
														<label for="struka" class="col-sm-5 control-label">
															<span class="text-danger">
																*
															</span>
															Struka:
														</label>
														<div class="col-sm-7">
															<div class="">
																<select class="selectpicker" name="struka" id = "struka" title = "Odaberite struku" required>
																	<?php
																	$struke = $db->prepare("
																				SELECT id_struke, naziv_struke
																				FROM idk_struke
																				");
																	$struke->execute();
																	while($ispis_struke_row = $struke->fetch()){
																	
																	$id_struke = $ispis_struke_row['id_struke'];
																	$naziv_struke = $ispis_struke_row['naziv_struke'];
																	
															?>
																	<option value="<?php echo $id_struke; ?>"><?php echo $naziv_struke; ?></option>
															<?php
																	}
															?>
																</select>
															</div>
														</div>
													</div>
													<div class="modal-footer material-modal__footer" style = "text-align: center;">
														<button class="btn material-btn material-btn" data-dismiss="modal">
															Odustani
														</button>
														<button type="submit" class="btn btn-primary material-btn material-btn_success" form="form_vezi_smjer">
															<i class="fa fa-check-square-o" aria-hidden="true" style = "margin-right: 10px;">
															</i>
															Završi
														</button>
													</div>
												</form>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<!-- Modal ADD PROFESSION DIRECTION END -->
						
				<?php
						break;
						
						case 'smjer_nalog':
						$id_otvorenog_smjer = $_GET['id'];
						$uzmi_naziv_skole = $db->prepare("
													SELECT skola_naziv
													FROM idk_skole
													WHERE skola_id = :skola_id
													");
													
						$uzmi_naziv_skole->execute(array(
													':skola_id' => $id_otvorenog_smjer));
														
						$uzmi_naziv_skole_row = $uzmi_naziv_skole->fetch();
						$naziv_skole_naslov = $uzmi_naziv_skole_row['skola_naziv'];
						
						$id_naloga = $_GET['nalog_id'];
						
						$get_projects = $db->prepare("SELECT project_id FROM idk_projects WHERE project_nalogid = $id_naloga");
						$get_projects->execute();
						$project_ids = array();
						while($row_projects = $get_projects->fetch()){
							array_push($project_ids, $row_projects['project_id']);
						}
						$project_ids = implode(",", $project_ids);
						$stmt = $db->prepare("SELECT pk_kandidatid FROM idk_project_kandidati WHERE pk_projectid IN ($project_ids)");
						$stmt->execute();
						$kandidat_id_project = array();
						while($result = $stmt->fetch()){
							$kandidat_id = $result['pk_kandidatid'];
							$kandidat_id_project[] = $kandidat_id;
						}
						$kandidat_id_project_unique = array_unique($kandidat_id_project);
						$kandidati_string = implode(",", $kandidat_id_project_unique);
						
						function getBrojPoSmjeru($smjer_id){
							Global $db;
							Global $kandidati_string;
							$query_bps = $db->prepare("SELECT COUNT(ke_id) as broj FROM idk_kandidat_edukacija WHERE ke_smjer_id = $smjer_id AND ke_kandidat_id IN ($kandidati_string)");
							$query_bps->execute();
							$row_bps = $query_bps->fetch();
							return $row_bps['broj'];
						}
				?>
						<div class = "row">
							<div class = "col-xs-8">
								<h1>
									<i class="fa fa-graduation-cap idk_color_green" aria-hidden="true" style = "margin-right: 10px;">
									</i>
									Smjerovi: <?php echo $naziv_skole_naslov; ?>
								</h1>
							</div>
						</div>
						
						<!-- LIST SCHOOL DIRECTION START -->
						
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
												$(document).ready(function() {
													$('#skole_smjer_table').DataTable({

														responsive: true,

														"order": [[ 0, "desc" ]],

														 "bAutoWidth": false,

														"aoColumns": [
																{ "width": "5%" },
																{ "width": "20%" },
																{ "width": "25%" },
																{ "width": "20%" },
																{ "width": "5%" },
																{ "width": "15%" },
																{ "width": "10%", "bSortable": false }
															]
													});
												} );
											</script>
											<table id="skole_smjer_table" class="display" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th class="text-center">#ID</th>
														<th>Naziv smjera</th>
														<th>Njemački naziv smjera</th>
														<th>Struka</th>
														<th class="text-center">Broj</th>
														<th class="text-center">Trajanje obrazovanja</th>
														<th class="text-center">Akcije</th>
													</tr>
												</thead>
												<tbody>
													<?php
													$redni_broj = 1;
														$ispis_smjerova = $db->prepare("
																				SELECT *
																				FROM idk_skole_smjerovi
																				WHERE ss_skola_id=:ss_skola_id
																				");
														$ispis_smjerova->execute(array(
																				':ss_skola_id' => $id_otvorenog_smjer
														));
														
														while($ispis_smjerova_row = $ispis_smjerova->fetch()){
															
															$id_struke_smjera = $ispis_smjerova_row['ss_struka_id'];
															$id_smjera = $ispis_smjerova_row['ss_id'];
															$naziv_smjera = $ispis_smjerova_row['ss_naziv'];
															$naziv_smjera_de = $ispis_smjerova_row['ss_naziv_de'];
															$trajanje_smjera = $ispis_smjerova_row['ss_trajanje_skole'];
															if($trajanje_smjera < 5){
																$trajanje_smjera1 = '<span class="label label-info material-label material-label_info main-container__column text-left">'.$trajanje_smjera.' godine</span>';
															}
															else if($trajanje_smjera > 4){
																$trajanje_smjera1 = '<span class="label label-info material-label material-label_info main-container__column text-left">'.$trajanje_smjera.' godina</span>';
															}
															
														$ispis_struke = $db->prepare("
																					SELECT naziv_struke
																					FROM idk_struke
																					WHERE id_struke = :id_struke
																					");
														$ispis_struke->execute(array(
																				':id_struke' => $id_struke_smjera
																				));
														$ispis_struke_row = $ispis_struke->fetch();
														$naziv_struke_smjera = $ispis_struke_row['naziv_struke'];
															
													?>
													<tr>
														<td class="text-center"><?php echo $id_smjera; ?></td>
														<td style = "word-break: break-all;"><?php echo $naziv_smjera; ?></td>
														<td style = "word-break: break-all;"><?php echo $naziv_smjera_de; ?></td>
														<td style = "word-break: break-all;"><?php echo $naziv_struke_smjera; ?></td>
														<td class="text-center"><?php echo getBrojPoSmjeru($id_smjera); ?></td>
														<td class="text-center"><?php echo $trajanje_smjera1; ?></td>
														<td class="text-center">
															<div class="btn-group material-btn-group">
																<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_success idk_btn_table" data-toggle="dropdown">
																	<i class="fa fa-cogs fa-lg" aria-hidden="true">
																	</i> 
																	<span class="caret material-btn__caret">
																	</span>
																</button>
																<ul style = "top:32px; left: -40px; min-width: 150px !important;" class="dropdown-menu material-dropdown-menu material-dropdown-menu_success idk_dropdown_table" role="menu">
																	<li>
																		<a href="<?php getSiteURL(); ?>skole?page=edit_school_direction&ids=<?php echo $id_otvorenog_smjer;?>&idss=<?php echo $id_smjera; ?>" target="_blank" class="material-dropdown-menu__link">
																			<i class="fa fa-pencil-square" style = "margin-right: 10px;" aria-hidden="true"></i>
																			Edit
																		</a>
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
						
						<!-- LIST SCHOOL DIRECTION END -->
						
				<?php
						break;
						
						case 'vezi_smjer':
						
							$id_smjera = $_POST['id_smjera'];
							$id_struke = $_POST['struka'];
							$id_skole = $_POST['id_skole'];
					
							
							//Save
							$query = $db->prepare("
											UPDATE idk_skole_smjerovi
											SET	ss_struka_id = :ss_struka_id
											WHERE ss_id = :ss_id");

							$query->execute(array(
											':ss_struka_id' => $id_struke,
											':ss_id' => $id_smjera
											));
								
							$log_desc = "SMJEROVI - Zaposlenik je vezao smjer ID = [".$id_smjera."], za struku ID = [".$id_struke."] .";
							$log_type = "0";
							addToLogs($log_desc, $log_type);
							
							header("Location: " . getSiteURLr() . "skole?page=smjerovi&id=" . $id_skole);
							
						break;
						
						case 'edit_school_direction':
						if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "1" , $employee_status))){
							
						$id_skolee_editt = $_GET['ids'];
						$id_skole_smjer_editt = $_GET['idss'];
						$uzmi_naziv_skole_smjer_edit = $db->prepare("
													SELECT *
													FROM  idk_skole_smjerovi
													WHERE ss_id = :ss_id
													");
													
						$uzmi_naziv_skole_smjer_edit->execute(array(
													':ss_id' => $id_skole_smjer_editt
													));
														
						$smjer_row = $uzmi_naziv_skole_smjer_edit->fetch();
						$naziv_smjera = $smjer_row['ss_naziv'];
						$naziv_smjera_de = $smjer_row['ss_naziv_de'];
						$naziv_smjera_en = $smjer_row['ss_naziv_en'];
						$id_smjera = $smjer_row['ss_id'];
						$skola_id = $smjer_row['ss_skola_id'];
						$trajanje = $smjer_row['ss_trajanje_skole'];
				?>
						<div class = "row">
							<div class = "col-xs-8">
								<h1> 
									<i class="fa fa-pencil-square idk_color_green" aria-hidden="true" style = "margin-right: 10px;">
									</i>
									Uredi: <?php echo $naziv_smjera; ?>
								</h1>
							</div>
							<div class = "col-xs-4 text-right idk_margin_top10">
								<a href="<?php getSiteURL(); ?>skole?page=smjerovi&id=<?php echo $id_skolee_editt; ?>" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive">
									<i class="fa fa-chevron-left" aria-hidden="true"></i> 
									<span>
										Povratak
									</span> 
								</a>
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
										<div class="col-xs-8 col-xs-offset-2 text-center">
											<form action="<?php getSiteURL(); ?>skole?page=edit_school_direction_add" method="post" http-equiv="Content-type" enctype="multipart/form-data"; charset = "utf-8" class="form-horizontal">
												<input type = "hidden" name = "id_school_direction_edit1" value = "<?php echo $id_smjera; ?>"/>
												<input type = "hidden" name = "id_schooll_edit1" value = "<?php echo $skola_id; ?>"/>
												<div class="form-group">
													<label for="name_school_direction_edit" class="col-sm-5 control-label">
														<span class="text-danger">
															*
														</span>
														Naziv smjera:
													</label>
													<div class="col-sm-7">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" value = "<?php echo $naziv_smjera;?>" name="name_school_direction_edit" id="name_school_direction_edit" autocomplete="off" placeholder="Unesite naziv smjera" required>
															<span class="materail-input-block__line">
															</span>
														</div>
													</div>
												</div>
												<div class="form-group">
													<label for="smjer_de" class="col-sm-5 control-label">
														<span class="text-danger">
															*
														</span>
														Njemački naziv smjera:
													</label>
													<div class="col-sm-7">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" value = "<?php echo $naziv_smjera_de;?>" name="smjer_de" id="smjer_de" autocomplete="off" placeholder="Unesite njemački naziv smjera" >
															<span class="materail-input-block__line">
															</span>
														</div>
													</div>
												</div>
												<div class="form-group">
													<label for="smjer_en" class="col-sm-5 control-label">
														<span class="text-danger">
															*
														</span>
														Engleski naziv smjera:
													</label>
													<div class="col-sm-7">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" value = "<?php echo $naziv_smjera_en;?>" name="smjer_en" id="smjer_en" autocomplete="off" placeholder="Unesite engleski naziv smjera" >
															<span class="materail-input-block__line">
															</span>
														</div>
													</div>
												</div>
												<div class="form-group">
													<label for="period_school_direction_edit" class="col-sm-5 control-label">
														<span class="text-danger">
															*
														</span>
														Period trajanja:
													</label>
													<div class="col-sm-7">
														<div class="">
															<select class="selectpicker" name="period_school_direction_edit" id = "period_school_direction_edit" title = "Odaberite period trajanja obrazovanja" required>
																<option <?php if($trajanje == 3) echo 'selected';?> value="3">3 godine</option>
																<option <?php if($trajanje == 4) echo 'selected';?> value="4">4 godine</option>
																<option <?php if($trajanje == 5) echo 'selected';?> value="5">5 godina</option>
																<option <?php if($trajanje == 6) echo 'selected';?> value="6">6 godina</option>
															</select>
														</div>
													</div>
												</div>
												<div class="form-group" style = "margin-top: 193px; ">
													<div class="col-sm-4 col-sm-offset-4 text-center">
														<ul class="list-inline">
															<li><button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column">
																	<i class="fa fa-save" aria-hidden="true"></i> 
																	<span>
																		Spremi promjene 
																	</span>
																</button>
															</li>
														</ul>
														<small>Sva polja označena sa <span class="text-danger">*</span> su obavezna!</small>
													</div>
												</div>
											</form>
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
						
						case 'add_new_school_direction':
							$id_skole_add = $_POST['id_school'];
							$naziv_smjera_add = $_POST['name_school_direction'];
							$naziv_smjera_de = $_POST['add_smjer_de'];
							$trajanje_smjera_add = $_POST['time_school_direction'];
							
							// var_dump('ID SKOLE: '.$id_skole_add.' Naziv: '.$naziv_smjera_add.' Trajanje: '.$trajanje_smjera_add.' ');
							// exit();
							
							//Save
							$query = $db->prepare("
											INSERT INTO idk_skole_smjerovi
												(ss_naziv, ss_naziv_de, ss_skola_id, ss_trajanje_skole)
											VALUES
												(:ss_naziv, :ss_naziv_de, :ss_skola_id, :ss_trajanje_skole)");

							$query->execute(array(
											':ss_naziv' => $naziv_smjera_add,
											':ss_naziv_de' => $naziv_smjera_de,
											':ss_skola_id' => $id_skole_add,
											':ss_trajanje_skole' => $trajanje_smjera_add
											));
							
							$skola_smjer_id = $db->lastInsertId();		
							$log_desc = "SKOLE - Zaposlenik je dodao smjer skole ID = [".$id_skole_add."], sa ID = [".$skola_smjer_id."] Naziv = [".$naziv_smjera_add."] i Trajanje = [".$trajanje_smjera_add."].";
							$log_type = "0";
							addToLogs($log_desc, $log_type);
							
							header("Location: " . getSiteURLr() . "skole?page=smjerovi&id=".$id_skole_add);
						break;
						
						case 'edit_school_direction_add':
							$id_skole = $_POST['id_schooll_edit1'];
							$id_smjer = $_POST['id_school_direction_edit1'];
							$naziv_smjera = $_POST['name_school_direction_edit'];
							$naziv_smjera_de = $_POST['smjer_de'];
							$naziv_smjera_en = $_POST['smjer_en'];
							$trajanje = $_POST['period_school_direction_edit'];
							if($naziv_smjera_de == "")
								$naziv_smjera_de = null;
							if($naziv_smjera_en == "")
								$naziv_smjera_en = null;
							
							// var_dump('ID SKOLE: '.$id_skole_edit_add1.' ID SMJERA: '.$id_skole_smjer_edit_add1.' NAZIV SMJERA: '.$naziv_skole_smjer_edit_add1.'PERIOD: '.$period_skole_smjer_edit_add1);
							// exit();
							$query = $db->prepare("
											UPDATE idk_skole_smjerovi
											SET ss_naziv = :ss_naziv, ss_naziv_de = :ss_naziv_de, ss_naziv_en = :ss_naziv_en, ss_trajanje_skole = :ss_trajanje_skole
											WHERE ss_id = :ss_id AND ss_skola_id = :ss_skola_id");

							$query->execute(array(
										':ss_id' => $id_smjer,
										':ss_skola_id' => $id_skole,
										':ss_naziv' => $naziv_smjera,
										':ss_naziv_de' => $naziv_smjera_de,
										':ss_naziv_en' => $naziv_smjera_en,
										':ss_trajanje_skole' => $trajanje  
										));
							$log_desc = "SKOLE - Zaposlenik je uredio smjer skole ID = [".$id_smjer."]";
							$log_type = "0";
							addToLogs($log_desc, $log_type);
							header("Location: " . getSiteURLr() . "skole?page=smjerovi&id=".$id_skole);
							
						break;
						
									case 'pregled_struke' :
					?>
						<div class = "row">
							<div class = "col-xs-8">
								<h1>
									<i class="fa fa-graduation-cap idk_color_green" aria-hidden="true" style = "margin-right: 10px;">
									</i>
									Struke
								</h1>
							</div>
							<div class = "col-xs-4 text-right idk_margin_top10">
								<a href="" data-toggle="modal" data-target="#modal_add_new_profession" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive">
									<i class="fa fa-plus" aria-hidden="true">
									</i>
									<span>
										Dodaj struku
									</span>
								</a>
							</div>
						</div>
						
						<!-- Modal ADD NEW PROFESSION START -->
						
						<div class="modal material-modal material-modal_success fade" id="modal_add_new_profession">
							<div class="modal-dialog modal-lg">
								<div class="modal-content material-modal__content">
									<div class="modal-header material-modal__header">
										<button class="close material-modal__close" data-dismiss="modal">&times;</button>
										<h4 class="modal-title material-modal__title">
											<i class="fa fa-plus" aria-hidden="true" style = "margin-right: 10px;">
											</i>
											Dodaj struku
										</h4>
									</div>
									<div class="modal-body material-modal__body">
										<div class = "row">
											<div class="col-md-8 col-md-offset-2">
												<form action="<?php getSiteURL(); ?>skole?page=add_new_profession" method="post" http-equiv="Content-type" enctype="multipart/form-data"; charset = "utf-8" class="form-horizontal" id="form_add_new_profession">
													<div class="form-group">
														<label for="name_school" class="col-sm-5 control-label">
															<span class="text-danger">
																*
															</span>
															Naziv struke:
														</label>
														<div class="col-sm-7">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" type="text" name="name_profession" id="name_profession" autocomplete="off" placeholder="Unesite naziv struke" required>
																<span class="materail-input-block__line">
																</span>
															</div>
														</div>
													</div>
													<div class="form-group">
														<label for="de_naziv_struke" class="col-sm-5 control-label">
															
															Njemački naziv struke:
														</label>
														<div class="col-sm-7">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" type="text" name="de_naziv_struke" id="de_naziv_struke" autocomplete="off" placeholder="Unesite njemački naziv struke" >
																<span class="materail-input-block__line">
																</span>
															</div>
														</div>
													</div>
													<div class="modal-footer material-modal__footer" style = "text-align: center;">
														<button class="btn material-btn material-btn" data-dismiss="modal">
															Odustani
														</button>
														<button type="submit" class="btn btn-primary material-btn material-btn_success" form="form_add_new_profession">
															<i class="fa fa-check-square-o" aria-hidden="true" style = "margin-right: 10px;">
															</i>
															Završi
														</button>
													</div>
												</form>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<!-- Modal ADD NEW PROFESSION END -->
						
						<!-- LIST PROFESSION START -->
						
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
												$(document).ready(function() {
													$('#struke_table').DataTable({

														responsive: true,

														"order": [[ 0, "desc" ]],

														 "bAutoWidth": false,

														"aoColumns": [
																{ "width": "5%" },
																{ "width": "35%" },
																{ "width": "35%" },
																{ "width": "10%", "bSortable": false },
																{ "width": "10%", "bSortable": false }
															]
													});
												} );
											</script>
											<table id="struke_table" class="display" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th class="text-center">#ID</th>
														<th>Naziv struke</th>
														<th>Njemački naziv struke</th>
														<th class="text-center">Pregled smjerova</th>
														<th class="text-center">Akcije</th>
													</tr>
												</thead>
												<tbody>
													<?php 
														$ispis_struka = $db->prepare("
																				SELECT *
																				FROM idk_struke
																				");
														$ispis_struka->execute();
														
														while($ispis_struka_row = $ispis_struka->fetch()){
															$id_struke = $ispis_struka_row['id_struke'];
															$naziv_struke = $ispis_struka_row['naziv_struke'];
															$naziv_struke_de = $ispis_struka_row['naziv_struke_de'];
															

													?>
													<tr>
														<td class="text-center"><?php echo $id_struke; ?></td>
														<td style = "word-break: break-all;"><?php echo $naziv_struke; ?></td>
														<td style = "word-break: break-all;"><?php echo $naziv_struke_de; ?></td>
														<td class="text-center" style = "padding-top: 13px;">
															<a href="<?php getSiteURL(); ?>skole?page=smjerovi_struke&id=<?php echo $id_struke; ?>" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive">
																<i class="fa fa-folder-open" aria-hidden="true"></i>
																<span>
																	Otvori
																</span>
															</a>
														</td>
														<td class="text-center">
															<div class="btn-group material-btn-group">
																<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_success idk_btn_table" data-toggle="dropdown">
																	<i class="fa fa-cogs fa-lg" aria-hidden="true">
																	</i> 
																	<span class="caret material-btn__caret">
																	</span>
																</button>
																<ul style = "top:32px; left: -40px; min-width: 150px !important;" class="dropdown-menu material-dropdown-menu material-dropdown-menu_success idk_dropdown_table" role="menu">
																	<li>
																		<a href="<?php getSiteURL(); ?>skole?page=edit_profession&id=<?php echo $id_struke; ?>" class="material-dropdown-menu__link">
																			<i class="fa fa-pencil-square" style = "margin-right: 10px;" aria-hidden="true"></i>
																			Edit
																		</a>
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
						
						break;
						
						case 'add_new_profession':
						
							$naziv_struke_add = $_POST['name_profession'];
							$de_naziv_struke = $_POST['de_naziv_struke'];
					
							
							//Save
							$query = $db->prepare("
											INSERT INTO idk_struke
												(naziv_struke, naziv_struke_de)
											VALUES
												(:naziv_struke, :naziv_struke_de)");

							$query->execute(array(
											':naziv_struke' => $naziv_struke_add,
											':naziv_struke_de' => $de_naziv_struke
											));
							$struka_id = $db->lastInsertId();		
							$log_desc = "STRUKE - Zaposlenik je dodao struku ID = [".$struka_id."], Naziv = [".$naziv_struke_add."] .";
							$log_type = "0";
							addToLogs($log_desc, $log_type);
							
							header("Location: " . getSiteURLr() . "skole?page=pregled_struke");
							
						break;
						
						case 'edit_profession':
						
						if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "1" , $employee_status))){
							
						$id_struke_editt = $_GET['id'];
						$uzmi_naziv_struke_edit = $db->prepare("
													SELECT naziv_struke
													FROM idk_struke
													WHERE id_struke = :id_struka
													");
													
						$uzmi_naziv_struke_edit->execute(array(
													':id_struka' => $id_struke_editt));
														
						$uzmi_naziv_struke_edit_row = $uzmi_naziv_struke_edit->fetch();
						$naziv_struke_naslov_edit = $uzmi_naziv_struke_edit_row['naziv_struke'];
				?>
						<div class = "row">
							<div class = "col-xs-8">
								<h1>
									<i class="fa fa-pencil-square idk_color_green" aria-hidden="true" style = "margin-right: 10px;">
									</i>
									Uredi: <?php echo $naziv_struke_naslov_edit; ?>
								</h1>
							</div>
							<div class = "col-xs-4 text-right idk_margin_top10">
								<a href="<?php getSiteURL(); ?>skole?page=pregled_struke" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive">
									<i class="fa fa-chevron-left" aria-hidden="true"></i> 
									<span>
										Povratak
									</span>
								</a>
							</div>
						</div>
					<?php
							$ispis_struka_edit = $db->prepare("
													SELECT *
													FROM idk_struke
													WHERE id_struke = :id_struke
													");
							$ispis_struka_edit->execute(array(
													':id_struke' => $id_struke_editt));
							
							$ispis_struka_edit_row = $ispis_struka_edit->fetch();
							$id_struke_edit = $ispis_struka_edit_row['id_struke'];
							$naziv_struke = $ispis_struka_edit_row['naziv_struke'];
							$naziv_struke_de = $ispis_struka_edit_row['naziv_struke_de'];
					?>
						<div class="row" style = "margin-top: 20px;">
							<div class="col-md-12">
								<div class="content_box">
									<div class="row">
										<div class="col-xs-12">
											<hr>
										</div>
									</div>
									<div class="row">
										<div class="col-xs-8 col-xs-offset-2 text-center">
											<form action="<?php getSiteURL(); ?>skole?page=edit_profession_add" method="post" http-equiv="Content-type" enctype="multipart/form-data"; charset = "utf-8" class="form-horizontal">
												<input type = "hidden" name = "id_profession_edit" value = "<?php echo $id_struke_edit; ?>"/>
												<div class="form-group">
													<label for="name_profession_edit" class="col-sm-5 control-label">
														<span class="text-danger">
															*
														</span>
														Naziv struke:
													</label>
													<div class="col-sm-7">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" value = "<?php echo $naziv_struke;?>" name="name_profession_edit" id="name_profession_edit" autocomplete="off" placeholder="Unesite naziv struke" required>
															<span class="materail-input-block__line">
															</span>
														</div>
													</div>
												</div>
												<div class="form-group">
													<label for="name_profession_de" class="col-sm-5 control-label">
														Njemački naziv struke:
													</label>
													<div class="col-sm-7">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" value = "<?php echo $naziv_struke_de;?>" name="name_profession_de" id="name_profession_de" autocomplete="off" placeholder="Unesite naziv struke" >
															<span class="materail-input-block__line">
															</span>
														</div>
													</div>
												</div>
												<div class="form-group" style = "margin-top: 193px; ">
													<div class="col-sm-4 col-sm-offset-4 text-center">
														<ul class="list-inline">
															<li><button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column">
																	<i class="fa fa-save" aria-hidden="true"></i> 
																	<span>
																		Spremi promjene 
																	</span>
																</button>
															</li>
														</ul>
														<small>Sva polja označena sa <span class="text-danger">*</span> su obavezna!</small>
													</div>
												</div>
											</form>
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
						
						case 'edit_profession_add':
						
							$id_struke_edit_add = $_POST['id_profession_edit'];
							$naziv_struke_edit_add = $_POST['name_profession_edit'];
							$naziv_struke_de = $_POST['name_profession_de'];
							if($naziv_skole_de == "")
								$naziv_skole_de = null;
							$query = $db->prepare("
											UPDATE idk_struke
											SET naziv_struke = :naziv_struke, naziv_struke_de = :naziv_struke_de
											WHERE id_struke = :id_struke");

							$query->execute(array(
										':id_struke' => $id_struke_edit_add,
										':naziv_struke' => $naziv_struke_edit_add,
										':naziv_struke_de' => $naziv_struke_de
										));
							
							$log_desc = "STRUKE - Uredio struku sa ID = [".$id_struke_edit_add."].";
							$log_type = "0";
							addToLogs($log_desc, $log_type);
							header("Location: " . getSiteURLr() . "skole?page=pregled_struke");
							
						break;
						
							case 'smjerovi_struke':
						$id_otvorene_struke = $_GET['id'];
						$uzmi_naziv_struke = $db->prepare("
													SELECT naziv_struke
													FROM idk_struke
													WHERE id_struke = :id_struke
													");
													
						$uzmi_naziv_struke->execute(array(
													':id_struke' => $id_otvorene_struke));
														
						$uzmi_naziv_struke_row = $uzmi_naziv_struke->fetch();
						$naziv_struke_naslov = $uzmi_naziv_struke_row['naziv_struke'];
				?>
						<div class = "row">
							<div class = "col-xs-8">
								<h1>
									<i class="fa fa-graduation-cap idk_color_green" aria-hidden="true" style = "margin-right: 10px;">
									</i>
									Smjerovi: <?php echo $naziv_struke_naslov; ?>
								</h1>
							</div>
							<div class = "col-xs-4 text-right idk_margin_top10">
								<a href="" data-toggle="modal" data-target="#modal_add_new_profession_direction" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive">
									<i class="fa fa-plus" aria-hidden="true">
									</i>
									<span>
										Dodaj smjer
									</span>
								</a>
								<a href="<?php getSiteURL(); ?>skole?page=pregled_struke" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive">
									<i class="fa fa-chevron-left" aria-hidden="true"></i> 
									<span>
										Povratak
									</span>
								</a>
							</div>
						</div>
						
						<!-- Modal ADD NEW PROFESSION DIRECTION START -->
						
						<div class="modal material-modal material-modal_success fade" id="modal_add_new_profession_direction">
							<div class="modal-dialog modal-lg">
								<div class="modal-content material-modal__content">
									<div class="modal-header material-modal__header">
										<button class="close material-modal__close" data-dismiss="modal">&times;</button>
										<h4 class="modal-title material-modal__title">
											<i class="fa fa-plus" aria-hidden="true" style = "margin-right: 10px;">
											</i>
											Dodaj smjer: <?php echo $naziv_struke_naslov; ?>
										</h4>
									</div>
									<div class="modal-body material-modal__body">
										<div class = "row">
											<div class="col-md-8 col-md-offset-2">
												<form action="<?php getSiteURL(); ?>skole?page=add_new_profession_direction" method="post" http-equiv="Content-type" enctype="multipart/form-data"; charset = "utf-8" class="form-horizontal" id="form_add_new_profession_direction">
													<input type = "hidden" name = "id_struke" value = "<?php echo $id_otvorene_struke; ?>"/>
													<div class="form-group">
														<label for="smjerovi" class="col-sm-5 control-label">
															<span class="text-danger">
																*
															</span>
															Smjerovi:
														</label>
														<div class="col-sm-7">
															<div class="">
																<select class="selectpicker" name="smjerovi[]" data-actions-box="true" data-live-search = "true" id = "smjerovi" title = "Odaberite smjer" data-selected-text-format = "count > 2" multiple required>
															<?php
																	$smjerovi = $db->prepare("
																				SELECT ss_id, ss_naziv, ss_struka_id
																				FROM idk_skole_smjerovi
																				");
																	$smjerovi->execute();
																	while($ispis_smjerova_row = $smjerovi->fetch()){
																	
																	$id_struke_smjeraa = $ispis_smjerova_row['ss_struka_id'];
																	$id_smjera = $ispis_smjerova_row['ss_id'];
																	$naziv_smjera = $ispis_smjerova_row['ss_naziv'];
																	
																	if($id_struke_smjeraa == null){
															?>
																	<option value="<?php echo $id_smjera; ?>"><?php echo $naziv_smjera; ?></option>
															<?php
																		}
																	}
															?>
																</select>
															</div>
														</div>
													</div>
													<div class="modal-footer material-modal__footer" style = "text-align: center;">
														<button class="btn material-btn material-btn" data-dismiss="modal">
															Odustani
														</button>
														<button type="submit" class="btn btn-primary material-btn material-btn_success" form="form_add_new_profession_direction">
															<i class="fa fa-check-square-o" aria-hidden="true" style = "margin-right: 10px;">
															</i>
															Završi
														</button>
													</div>
												</form>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<!-- Modal ADD NEW PROFESSION DIRECTION END -->
						
						<!-- LIST PROFESSION DIRECTION START -->
						
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
												$(document).ready(function() {
													$('#struke_smjer_table').DataTable({

														responsive: true,

														"order": [[ 0, "desc" ]],

														 "bAutoWidth": false,

														"aoColumns": [
																{ "width": "5%" },
																{ "width": "30%" },
																{ "width": "35%" },
																{ "width": "20%" },
																{ "width": "10%", "bSortable": false }
															]
													});
												} );
											</script>
											<table id="struke_smjer_table" class="display" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th class="text-center">#ID</th>
														<th>Naziv smjera</th>
														<th>Njemački naziv smjera</th>
														<th>Škola smjera</th>
														<th class="text-center">Akcije</th>
													</tr>
												</thead>
												<tbody>
													<?php
													$redni_broj = 1;
														$ispis_smjerova = $db->prepare("
																				SELECT *
																				FROM idk_skole_smjerovi
																				WHERE ss_struka_id=:ss_struka_id
																				");
														$ispis_smjerova->execute(array(
																				':ss_struka_id' => $id_otvorene_struke
														));
														
														while($ispis_smjerova_row = $ispis_smjerova->fetch()){
															
															$id_skole_smjera = $ispis_smjerova_row['ss_skola_id'];
															$id_smjera = $ispis_smjerova_row['ss_id'];
															$id_struke_smjera = $ispis_smjerova_row['ss_sturka_id'];
															$naziv_smjera = $ispis_smjerova_row['ss_naziv'];
															$naziv_smjera_de = $ispis_smjerova_row['ss_naziv_de'];
													
														$ispis_skole = $db->prepare("
																					SELECT skola_naziv
																					FROM idk_skole
																					WHERE skola_id = :skola_id
																					");
														$ispis_skole->execute(array(
																			':skola_id' => $id_skole_smjera
																			));
														$ispis_skole_row = $ispis_skole->fetch();
														$naziv_skole_smjera = $ispis_skole_row['skola_naziv'];
													?>
													<tr>
														<td class="text-center"><?php echo $redni_broj++; ?></td>
														<td style = "word-break: break-all;"><?php echo $naziv_smjera; ?></td>
														<td style = "word-break: break-all;"><?php echo $naziv_smjera_de; ?></td>
														<td style = "word-break: break-all;"><?php echo $naziv_skole_smjera; ?></td>
														<td class="text-center">
															<div class="btn-group material-btn-group">
																<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_success idk_btn_table" data-toggle="dropdown">
																	<i class="fa fa-cogs fa-lg" aria-hidden="true">
																	</i> 
																	<span class="caret material-btn__caret">
																	</span>
																</button>
																<ul style = "top:32px; left: -40px; min-width: 150px !important;" class="dropdown-menu material-dropdown-menu material-dropdown-menu_success idk_dropdown_table" role="menu">
																	<li>
																		<a href="<?php getSiteURL(); ?>skole?page=edit_smjer_struke&idss=<?php echo $id_smjera;?>&ids=<?php echo $id_otvorene_struke; ?>" class="material-dropdown-menu__link">
																			<i class="fa fa-pencil-square" style = "margin-right: 10px;" aria-hidden="true"></i>
																			Edit
																		</a>
																	</li>
																	<li>
																		<a href="<?php getSiteURL(); ?>skole?page=ukloni_smjer_struke&idss=<?php echo $id_smjera;?>&ids=<?php echo $id_otvorene_struke; ?>" class="material-dropdown-menu__link">
																			<i class="fa fa-pencil-square" style = "margin-right: 10px;" aria-hidden="true"></i>
																			Ukloni smjer
																		</a>
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
						
						<!-- LIST PROFESSION DIRECTION END -->
						
				<?php
						break;
						
						case 'ukloni_smjer_struke':
						
							$id_smjera = $_GET['idss'];
							$id_otvorene_struke = $_GET['ids'];
							
							$query = $db->prepare("
											UPDATE idk_skole_smjerovi
											SET ss_struka_id = null
											WHERE ss_id = :ss_id");

							$query->execute(array(
										':ss_id' => $id_smjera
										));
							
							$log_desc = "SMJEROVI - Uklonio sturku ID = [".$id_otvorene_struke."] sa smjera ID = [".$id_smjera."].";
							$log_type = "0";
							addToLogs($log_desc, $log_type);
							header("Location: " . getSiteURLr() . "skole?page=smjerovi_struke&id=" . $id_otvorene_struke );
							
						break;
						
							case 'edit_smjer_struke':
						
						if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "1" , $employee_status))){
							
						
						$id_smjera_editt = $_GET['idss'];
						$id_struke_smjera = $_GET['ids'];
					
						$uzmi_naziv_smjera_edit = $db->prepare("
													SELECT ss_naziv
													FROM idk_skole_smjerovi
													WHERE ss_id = :ss_id
													");
													
						$uzmi_naziv_smjera_edit->execute(array(
													':ss_id' => $id_smjera_editt));
														
						$uzmi_naziv_smjera_edit_row = $uzmi_naziv_smjera_edit->fetch();
						$naziv_smjera_naslov_edit = $uzmi_naziv_smjera_edit_row['ss_naziv'];
						

						
				?>
						<div class = "row">
							<div class = "col-xs-8">
								<h1>
									<i class="fa fa-pencil-square idk_color_green" aria-hidden="true" style = "margin-right: 10px;">
									</i>
									Uredi: <?php echo $naziv_smjera_naslov_edit; ?>
								</h1>
							</div>
							<div class = "col-xs-4 text-right idk_margin_top10">
								<a href="<?php getSiteURL(); ?>skole?page=pregled_struke" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive">
									<i class="fa fa-chevron-left" aria-hidden="true"></i> 
									<span>
										Povratak
									</span>
								</a>
							</div>
						</div>
					<?php
							$ispis_smjerova_edit = $db->prepare("
													SELECT *
													FROM idk_skole_smjerovi
													WHERE ss_id = :ss_id
													");
							$ispis_smjerova_edit->execute(array(
													':ss_id' => $id_smjera_editt));
							
							$ispis_smjerova_edit_row = $ispis_smjerova_edit->fetch();
							$id_smjera_edit = $ispis_smjerova_edit_row['ss_id'];
							$naziv_smjera = $ispis_smjerova_edit_row['ss_naziv'];
							$naziv_smjera_de = $ispis_smjerova_edit_row['ss_naziv_de'];
					?>
						<div class="row" style = "margin-top: 20px;">
							<div class="col-md-12">
								<div class="content_box">
									<div class="row">
										<div class="col-xs-12">
											<hr>
										</div>
									</div>
									<div class="row">
										<div class="col-xs-8 col-xs-offset-2 text-center">
											<form action="<?php getSiteURL(); ?>skole?page=edit_smjer_struke_add" method="post" http-equiv="Content-type" enctype="multipart/form-data"; charset = "utf-8" class="form-horizontal">
												<input type = "hidden" name = "id_smjera_edit" value = "<?php echo $id_smjera_edit; ?>"/>
												<input type = "hidden" name = "id_struke_smjera" value = "<?php echo $id_struke_smjera; ?>"/>
												<div class="form-group">
													<label for="naziv_smjera_struke" class="col-sm-5 control-label">
														<span class="text-danger">
															*
														</span>
														Naziv smjera:
													</label>
													<div class="col-sm-7">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" value = "<?php echo $naziv_smjera;?>" name="naziv_smjera_struke" id="naziv_smjera_struke" autocomplete="off" placeholder="Unesite naziv smjera" required>
															<span class="materail-input-block__line">
															</span>
														</div>
													</div>
												</div>
												<div class="form-group">
													<label for="naziv_smjera_struke_de" class="col-sm-5 control-label">
														Njemački naziv smjera:
													</label>
													<div class="col-sm-7">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" value = "<?php echo $naziv_smjera_de;?>" name="naziv_smjera_struke_de" id="naziv_smjera_struke_de" autocomplete="off" placeholder="Unesite naziv smjera" >
															<span class="materail-input-block__line">
															</span>
														</div>
													</div>
												</div>
												<div class="form-group" style = "margin-top: 193px; ">
													<div class="col-sm-4 col-sm-offset-4 text-center">
														<ul class="list-inline">
															<li><button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column">
																	<i class="fa fa-save" aria-hidden="true"></i> 
																	<span>
																		Spremi promjene 
																	</span>
																</button>
															</li>
														</ul>
														<small>Sva polja označena sa <span class="text-danger">*</span> su obavezna!</small>
													</div>
												</div>
											</form>
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
						
						case 'edit_smjer_struke_add':
						
							$id_struke_smjera_edit = $_POST['id_struke_smjera'];
							$id_smjera_edit_add = $_POST['id_smjera_edit'];
							$naziv_smjera_edit_add = $_POST['naziv_smjera_struke'];
							$naziv_smjera_de = $_POST['naziv_smjera_struke_de'];
							if($naziv_skole_de == "")
								$naziv_skole_de = null;
							$query = $db->prepare("
											UPDATE idk_skole_smjerovi
											SET ss_naziv = :ss_naziv, ss_naziv_de = :ss_naziv_de
											WHERE ss_id = :ss_id");

							$query->execute(array(
										':ss_id' => $id_smjera_edit_add,
										':ss_naziv' => $naziv_smjera_edit_add,
										':ss_naziv_de' => $naziv_smjera_de
										));
							
							$log_desc = "SMJEROVI - Uredio smjer sa ID = [".$id_smjera_edit_add."].";
							$log_type = "0";
							addToLogs($log_desc, $log_type);
							header("Location: " . getSiteURLr() . "skole?page=smjerovi_struke&id=" . $id_struke_smjera_edit );
							
						break;
						
						case 'add_new_profession_direction':
						
							$id_smjera_add = $_POST['smjerovi'];
							$id_struke = $_POST['id_struke'];
							
							
							//Save
							$query = $db->prepare("
											UPDATE idk_skole_smjerovi
											SET ss_struka_id = :ss_struka_id
											WHERE ss_id = :ss_id");
					
						for($i = 0; $i < count($id_smjera_add); $i++){
							$query->execute(array(
											':ss_struka_id' => $id_struke,
											':ss_id' => $id_smjera_add[$i]
											));
						
									
							$log_desc = "STRUKE - Zaposlenik je dodao smjer ID = [".$id_smjera_add[$i]."], na struku ID = [".$id_struke."] .";
							$log_type = "0";
							addToLogs($log_desc, $log_type);
						}
							header("Location: " . getSiteURLr() . "skole?page=smjerovi_struke&id=" . $id_struke);
							
						break;
					}
				?>
			</div>
		</div>
		
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