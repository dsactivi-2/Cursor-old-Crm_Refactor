<?php
	include("includes/functions.php");
	include("includes/common.php");

	$getEmployeeStatus = explode( ',' , getEmployeeStatus());
	
	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: positions?page=list");
	}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Pozicije kandidata | <?php getTitle(); ?></title>

	<?php include('includes/head.php'); 
	if (in_array($getUserIp, $getIpWhiteList)){
	?>

</head>
<body>
	<header>
		<?php include('header.php'); ?>
	</header>
	<div id="sidebar">
		<?php include('menu.php'); ?>
	</div>
	<div id="content">
		<div class="container-fluid">
		<?php
			switch ($page){

				case "list":

		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-id-badge idk_color_green" aria-hidden="true"></i> Pozicije</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>positions?page=add" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj</span></a>
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
								<?php
									if(isset($_GET['mess'])) {
										$mess = $_GET['mess'];
									}else{
										$mess = 0;
									}

									if($mess == 1){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novu poziciju.</div>';
									}elseif($mess == 2){
										echo '<div class="alert material-alert material-alert_danger">Greška: Pozicija koju pokušavate dodati već postoji u bazi podataka.</div>';
									}elseif($mess == 3){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste uredili poziciju.</div>';
									}
								?>
								<script type="text/javascript">
									$(document).ready(function() {
										$('#idk_table').DataTable({

											responsive: true,

											"order": [[ 1, "asc" ]],

											 "bAutoWidth": false,

											"aoColumns": [
													{ "width": "10%"},
													{ "width": "10%" },
													{ "width": "10%" },
													{ "width": "10%" },
													{ "width": "50%", "bSortable": false  },
													{ "width": "10%", "bSortable": false }
												]
										});
									} );
								</script>
								
									<table id="idk_table" class="display" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th>Pozicija <img src="<?php getSiteUrl(); ?>images/BosniaHerzegowina.png" width="25"></th>
												<th>Pozicija <img src="<?php getSiteUrl(); ?>images/Germany.png" width="25"></th>
												<th>Pozicija <img src="<?php getSiteUrl(); ?>images/Britania.png" width="25"></th>
												<th>Pozicija <img src="<?php getSiteUrl(); ?>images/Serbian.png" width="25"></th>
												<th>Opis</th>
												<th></th>
											</tr>
										</thead>
										<tbody>
											<?php
												$query = $db->prepare("
																SELECT kp_id, kp_ime, kp_ime_de,kp_ime_en,kp_ime_rs,kp_opis
																FROM idk_kandidat_pozicija WHERE kp_active = 1"
																);

												$query->execute();

												while($row = $query->fetch()){

													$kp_id = $row['kp_id'];
													$kp_ime = $row['kp_ime'] ?? 'Nema unosa';
													$kp_ime_de = $row['kp_ime_de'] ?? 'Nema unosa';
													$kp_ime_en = $row['kp_ime_en'] ?? 'Nema unosa';
													$kp_ime_rs = $row['kp_ime_rs'] ?? 'Nema unosa';
													$kp_opis = $row['kp_opis'];
											?>
											<tr>
												<td><a href="<?php getSiteURL(); ?>positions?page=open&id=<?php echo $kp_id; ?>"><?php echo $kp_ime; ?></a></td>
												<td><a href="<?php getSiteURL(); ?>positions?page=open&id=<?php echo $kp_id; ?>"><?php echo $kp_ime_de; ?></a></td>
												<td><a href="<?php getSiteURL(); ?>positions?page=open&id=<?php echo $kp_id; ?>"><?php echo $kp_ime_en; ?></a></td>
												<td><a href="<?php getSiteURL(); ?>positions?page=open&id=<?php echo $kp_id; ?>"><?php echo $kp_ime_rs; ?></a></td>
												<td><?php echo $kp_opis; ?></td>
												<td class="text-center">
													<div class="btn-group material-btn-group">
														<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
														<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
															<li><a href="<?php getSiteURL(); ?>positions?page=open&id=<?php echo $kp_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori</a></li>
															<li><a href="<?php getSiteURL(); ?>positions?page=edit&id=<?php echo $kp_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Uredi</a></li>
															<li style="background-color: red;" ><a href="<?php getSiteURL(); ?>do?form=archive_position&kp_id=<?php echo $kp_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-trash-o " aria-hidden="true"></i> Arhiviraj</a></li>
														</ul>
													</div>
												</td>
											</tr>
											<?php } ?>
										</tbody>
									</table>
							
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php
				break;
				

				case "add":
					if(in_array( "1" , $getEmployeeStatus)){
		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-id-badge idk_color_green" aria-hidden="true"></i> Dodaj novu poziciju</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>positions?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<div class="row">
							<div class="col-md-offset-1 col-md-8">
								<form id="idk_form" action="<?php getSiteURL(); ?>do.php?form=add_positions" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<div class="form-group">
										<label for="kp_ime" class="col-sm-3 control-label"><span class="text-danger"></span> Ime pozicije(Bosasnki):</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kp_ime" id="kp_ime" placeholder="Ime pozicije">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="kp_ime_de" class="col-sm-3 control-label"><span class="text-danger">*</span> Ime pozicije (Njemački):</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kp_ime_de" id="kp_ime_de" placeholder="Ime pozicije" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="kp_ime_en" class="col-sm-3 control-label"><span class="text-danger">*</span> Ime pozicije (Engleski):</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kp_ime_en" id="kp_ime_en" placeholder="Ime pozicije" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="kp_ime_rs" class="col-sm-3 control-label"><span class="text-danger"></span> Ime pozicije (Srpski):</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kp_ime_rs" id="kp_ime_rs" placeholder="Ime pozicije">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="kp_opis" class="col-sm-3 control-label"><span class="text-danger"></span> Opis pozicije:</label>
										<div class="col-sm-9">
											<textarea class="form-control materail-input materail-input-custom" type="text" rows="6" name="kp_opis" id="kp_opis" placeholder="Opis pozicije"></textarea>
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

				case "edit":
					if(in_array( "1" , $getEmployeeStatus)){

						$kp_id = $_GET['id'];
						
						$query = $db->prepare("
											SELECT kp_ime, kp_ime_de,kp_ime_en,kp_ime_rs,kp_opis
											FROM idk_kandidat_pozicija
											WHERE kp_id = :kp_id");
										

						$query->execute(array(
									':kp_id' => $kp_id));

						$row = $query->fetch();

							$kp_ime = $row['kp_ime'];
							$kp_ime_de = $row['kp_ime_de'];
							$kp_ime_en = $row['kp_ime_en'];
							$kp_ime_rs = $row['kp_ime_rs'];
							$kp_opis = $row['kp_opis'];
		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-users idk_color_green" aria-hidden="true"></i> Uredi poziciju</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php echo $_SERVER['HTTP_REFERER']; ?>" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<div class="row">
							<div class="col-md-offset-1 col-md-8">
								<form id="idk_form" action="<?php getSiteURL(); ?>do.php?form=edit_positions" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<input type="hidden" name="kp_id" value="<?php echo $kp_id; ?>" />
									<div class="form-group">
										<label for="kp_ime" class="col-sm-3 control-label"><span class="text-danger"></span> Ime pozicije:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kp_ime" id="kp_ime" value="<?php echo $kp_ime ?>">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="kp_ime_de" class="col-sm-3 control-label"><span class="text-danger">*</span> Ime pozicije <img src="<?php getSiteUrl(); ?>images/Germany.png" width="25"> :</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kp_ime_de" id="kp_ime_de" value="<?php echo $kp_ime_de ?>" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="kp_ime_en" class="col-sm-3 control-label"><span class="text-danger">*</span> Ime pozicije <img src="<?php getSiteUrl(); ?>images/Britania.png" width="25"> :</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kp_ime_en" id="kp_ime_en" value="<?php echo $kp_ime_en ?>" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									
									<div class="form-group">
										<label for="kp_ime_rs" class="col-sm-3 control-label"><span class="text-danger"></span> Ime pozicije <img src="<?php getSiteUrl(); ?>images/Serbian.png" width="25"> :</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kp_ime_rs" id="kp_ime_rs" value="<?php echo $kp_ime_rs ?>">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>

									<div class="form-group">
										<label for="kp_opis" class="col-sm-3 control-label"><span class="text-danger"></span> Opis pozicije:</label>
										<div class="col-sm-9">
											<textarea class="form-control materail-input materail-input-custom" type="text" rows="6" name="kp_opis" id="kp_opis"><?php echo $kp_opis ?></textarea>
										</div>
									</div>
									<br />
									<div class="form-group">
										<div class="col-sm-offset-2 col-sm-10 text-right">
											<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-plus" aria-hidden="true"></i> <span>Uredi</span></button>
											<br /><small>Sva polja označena sa <span class="text-danger">*</span> su obavezna!</small>
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

				
				case "open":

					$kp_id = $_GET['id'];

					$query = $db->prepare("
									SELECT kp_ime, kp_ime_de,kp_ime_en,kp_ime_rs, kp_opis,kp_source,kp_created_by
									FROM idk_kandidat_pozicija
									WHERE kp_id = :kp_id");

					$query->execute(array(
								':kp_id' => $kp_id));

					$row = $query->fetch();

					$kp_source = $row['kp_source'];
					$kp_created_by = $row['kp_created_by'];
					$source="";
					$created_by="";
					if(is_null($kp_created_by) OR $kp_created_by==0){
						$source="CRM";
						$created_by="Stari unosi";
					}else{
						if($kp_source==1){
						$get_employee_query = $db->prepare("SELECT employee_firstname,employee_lastname FROM idk_employees WHERE employee_id = :employee_id");
						$get_employee_query->execute(array(':employee_id'=>$kp_created_by));
						$get_employee=$get_employee_query->fetch();
						$source="CRM";
						$created_by = $get_employee['employee_firstname']." ".$get_employee['employee_lastname'];
						}else{
							$get_partner_query = $db->prepare("SELECT jp_imeprezime  FROM idk_jobstep_partners WHERE jp_id = :jp_id");
							$get_partner_query->execute(array(':jp_id'=>$kp_created_by));
							$get_partner=$get_partner_query->fetch();
							$source="Partner APP";
							$created_by = $get_partner['jp_imeprezime'];
						}
					}
					

					$kp_ime = $row['kp_ime'] ?? 'Nema unosa';
					$kp_ime_de = $row['kp_ime_de'] ?? 'Nema unosa';
					$kp_ime_en = $row['kp_ime_en'] ?? 'Nema unosa';
					$kp_ime_rs = $row['kp_ime_rs'] ?? 'Nema unosa';	
					$kp_opis = $row['kp_opis'];

					$nalsov = $row['kp_ime_de'] ?? $row['kp_ime_en'];
		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-id-badge idk_color_green" aria-hidden="true"></i> <?php echo $nalsov ?></h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>positions?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<div id="myTabs" class="panel-group material-tabs-group">
							<ul class="nav nav-tabs material-tabs material-tabs_primary">
								<li class="active"><a href="#info" class="material-tabs__tab-link" data-toggle="tab">Informacije</a></li>
							</ul>
							<div class="tab-content materail-tabs-content">
								<div class="tab-pane fade active in" id="info">
									<div class="row idk_employee_info">
										<div class="col-md-6">
											<div class="row">
												<div class="col-sm-9">
												</div>
												<div class="col-sm-3 text-right">
													<a href="positions?page=edit&id=<?php echo $kp_id; ?>" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-pencil" aria-hidden="true"></i> <span></span></a>
												</div>
											</div>

											<div class="row">
												<strong class="col-sm-4 text-right">Naziv pozicije <img src="<?php getSiteUrl(); ?>images/BosniaHerzegowina.png" width="25"> :</strong>
												<div class="col-sm-8"><?php echo $kp_ime; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Naziv pozicije <img src="<?php getSiteUrl(); ?>images/Germany.png" width="25"> :</strong>
												<div class="col-sm-8"><?php echo $kp_ime_de; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Naziv pozicije <img src="<?php getSiteUrl(); ?>images/Britania.png" width="25"> :</strong>
												<div class="col-sm-8"><?php echo $kp_ime_en; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Naziv pozicije <img src="<?php getSiteUrl(); ?>images/Serbian.png" width="25"> :</strong>
												<div class="col-sm-8"><?php echo $kp_ime_rs ; ?></div>
											</div>
										</div>
										<div class="col-md-6">
											<div class="row">
												<div class="col-sm-9">
													<h5>Opis pozicije</h5>
												</div>
											</div>
											<div class="row">
												<div class="col-sm-12" style="padding-left: 33px!important;"><?php echo $kp_opis; ?></div>
											</div>
											<div class="row">
												<div class="col-sm-9">
													<h5>Profesiju kreirao</h5>
												</div>
											</div>
											<div class="row">
												<div class="col-sm-12" style="padding-left: 33px!important;"><?php echo $created_by; ?></div>
											</div>
											<div class="row">
												<div class="col-sm-12" style="padding-left: 33px!important;">Izvor: <?php echo $source; ?></div>
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

				case "del_doc":
					if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){

						$document_id = $_GET['id'];

						//Get document name, dataid and delete document
						$doc_open_query = $db->prepare("
													SELECT document_name, document_file, document_dataid
													FROM idk_documents
													WHERE document_id = :document_id");

						$doc_open_query->execute(array(
												':document_id' => $document_id));

						$doc_open = $doc_open_query->fetch();

							$document_name = $doc_open['document_name'];
							$document_file = $doc_open['document_file'];
							$document_dataid = $doc_open['document_dataid'];

							unlink("files/files/employees/" . $document_file);

						//Add to LOGS
						$log_desc = "Obrisao dokument: " . $document_name . " ";
						$log_date = date('Y-m-d H:i:s');

						$log_query = $db->prepare("
										INSERT INTO idk_logs
											(log_employeeid, log_desc, log_date)
										VALUES
											(:log_employeeid, :log_desc, :log_date)");

						$log_query->execute(array(
										':log_employeeid' => $logged_employee_id,
										':log_desc' => $log_desc,
										':log_date' => $log_date));

						//Delete document from db
						$doc_del_query = $db->prepare("
													DELETE FROM idk_documents
													WHERE document_id = :document_id");

						$doc_del_query->execute(array(
											':document_id' => $document_id));

						header("Location: " . getSiteURLr() . "employees?page=open&id=$document_dataid&mess=3");

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

				case "del_note":
					if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){

						$note_id = $_GET['id'];

						//Get note_txt and note_dataid
						$note_open_query = $db->prepare("
													SELECT note_txt, note_dataid
													FROM idk_notes
													WHERE note_id = :note_id");

						$note_open_query->execute(array(
												':note_id' => $note_id));

						$note_open = $note_open_query->fetch();

							$note_txt = $note_open['note_txt'];
							$note_dataid = $note_open['note_dataid'];

						//Add to LOGS
						$log_desc = "Obrisao bilješku: " . $note_txt . " ";
						$log_date = date('Y-m-d H:i:s');

						$log_query = $db->prepare("
										INSERT INTO idk_logs
											(log_employeeid, log_desc, log_date)
										VALUES
											(:log_employeeid, :log_desc, :log_date)");

						$log_query->execute(array(
										':log_employeeid' => $logged_employee_id,
										':log_desc' => $log_desc,
										':log_date' => $log_date));

						//Delete document from db
						$doc_del_query = $db->prepare("
													DELETE FROM idk_notes
													WHERE note_id = :note_id");

						$doc_del_query->execute(array(
											':note_id' => $note_id));

						header("Location: " . getSiteURLr() . "employees?page=open&id=$note_dataid&mess=4");

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

				case "archive":
					if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){

						$employee_id = $_GET['id'];

						//Get
						$query_select = $db->prepare("
												SELECT employee_firstname, employee_lastname
												FROM idk_employees
												WHERE employee_id = :employee_id");

						$query_select->execute(array(
											':employee_id' => $employee_id));

						$row_select = $query_select->fetch();

						$employee_firstname = $row_select['employee_firstname'];
						$employee_lastname = $row_select['employee_lastname'];

						//Save
						$query = $db->prepare("
										UPDATE idk_employees
										SET employee_status = :employee_status
										WHERE employee_id = :employee_id");

						$query->execute(array(
									':employee_status' => 0,
									':employee_id' => $employee_id));

						//Add to LOGS
						$log_desc = "Arhivirao zaposlenika: " . $employee_firstname . " " . $employee_lastname . " ";
						$log_date = date('Y-m-d H:i:s');

						$log_query = $db->prepare("
										INSERT INTO idk_logs
											(log_employeeid, log_desc, log_date)
										VALUES
											(:log_employeeid, :log_desc, :log_date)");

						$log_query->execute(array(
										':log_employeeid' => $logged_employee_id,
										':log_desc' => $log_desc,
										':log_date' => $log_date));


						header("Location: " . getSiteURLr() . "employees?page=list&mess=4");

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

				case "del_task":
					if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){

						$task_id = $_GET['id'];

						//Get task_txt and task_dataid
						$task_open_query = $db->prepare("
													SELECT task_txt, task_dataid, task_emailnotifi, task_status
													FROM idk_tasks
													WHERE task_id = :task_id");

						$task_open_query->execute(array(
												':task_id' => $task_id));

						$task_open = $task_open_query->fetch();

							$task_txt = strip_tags($task_open['task_txt']);
							$task_dataid = $task_open['task_dataid'];
							$task_emailnotifi = $task_open['task_emailnotifi'];
							$task_status = $task_open['task_status'];

						//Add to LOGS
						$log_desc = "Obrisao zadatak: " . $task_txt . " ";
						$log_date = date('Y-m-d H:i:s');

						$log_query = $db->prepare("
										INSERT INTO idk_logs
											(log_employeeid, log_desc, log_date)
										VALUES
											(:log_employeeid, :log_desc, :log_date)");

						$log_query->execute(array(
										':log_employeeid' => $logged_employee_id,
										':log_desc' => $log_desc,
										':log_date' => $log_date));

						//Delete task from db
						$task_del_query = $db->prepare("
													DELETE FROM idk_tasks
													WHERE task_id = :task_id");

						$task_del_query->execute(array(
											':task_id' => $task_id));

						//Remove Trigger
						if($task_emailnotifi != 0 && $task_status == 1){
							$url_tag = "email_task_" . $task_id . "";
							removeTrigger($url_tag);
						}

						header("Location: " . getSiteURLr() . "employees?page=open&id=$task_dataid&mess=11");

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

				case "del_employee_info":
					if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){

						$ei_id = $_GET['id'];

						//Get ei_title, ei_data and ei_employeeid
						$phone_open_query = $db->prepare("
													SELECT ei_title, ei_data, ei_employeeid
													FROM idk_employees_info
													WHERE ei_id = :ei_id");

						$phone_open_query->execute(array(
												':ei_id' => $ei_id));

						$phone_open = $phone_open_query->fetch();

							$ei_title = $phone_open['ei_title'];
							$ei_data = $phone_open['ei_data'];
							$ei_employeeid = $phone_open['ei_employeeid'];

						//Add to LOGS
						$log_desc = "Obrisao telefon: " . $ei_title . " - " . $ei_data . "";
						$log_date = date('Y-m-d H:i:s');

						$log_query = $db->prepare("
										INSERT INTO idk_logs
											(log_employeeid, log_desc, log_date)
										VALUES
											(:log_employeeid, :log_desc, :log_date)");

						$log_query->execute(array(
										':log_employeeid' => $logged_employee_id,
										':log_desc' => $log_desc,
										':log_date' => $log_date));

						//Delete phone from db
						$phone_del_query = $db->prepare("
													DELETE FROM idk_employees_info
													WHERE ei_id = :ei_id");

						$phone_del_query->execute(array(
											':ei_id' => $ei_id));

						header("Location: " . getSiteURLr() . "employees?page=open&id=$ei_employeeid&mess=16");

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