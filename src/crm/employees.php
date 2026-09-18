<?php
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
	<title>Zaposlenici | <?php getTitle(); ?></title>

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
					<h1><i class="fa fa-users idk_color_green" aria-hidden="true"></i> Zaposlenici</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>employees?page=add" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj</span></a>
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
										echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novog zaposlenika.</div>';
									}elseif($mess == 2){
										echo '<div class="alert material-alert material-alert_danger">Greška: Korisnik kojeg pokuštavate dodati već postoji u bazi podataka.</div>';
									}elseif($mess == 3){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste uredili profil zaposlenika.</div>';
									}elseif($mess == 4){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste arhivirali profil zaposlenika.</div>';
									}
								?>
								<script type="text/javascript">
									$(document).ready(function() {
										$('#idk_table').DataTable({

											responsive: true,

											"order": [[ 1, "asc" ]],

											 "bAutoWidth": false,

											"aoColumns": [
													{ "width": "5%", "bSortable": false },
													{ "width": "10%" },
													{ "width": "5%" },
													{ "width": "10%" },
													{ "width": "7%" },
													{ "width": "10%" },
													{ "width": "3%" },
													{ "width": "15%" },
													{ "width": "10%" },
													{ "width": "10%" },
													{ "width": "8%" },
													{ "width": "7%", "bSortable": false }
												]
										});
									} );
								</script>

									<table id="idk_table" class="display" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th></th>
												<th>Ime i prezime</th>
												<th>Tim</th>
												<th>Poslovnica</th>
												<th>Odjel</th>
												<th>Telefon</th>
												<th>E-mail</th>
												<th>DVAG</th>
												<th>Status</th>
												<th>Promjena šifre</th>
												<th>Tražena šifra</th>
												<th></th>
											</tr>
										</thead>
										<tbody>
											<?php

												if((in_array( "9" , $employee_status)) OR (in_array( "1" , $employee_status)) OR (!empty($employee_supervizor[0]))){
													$filter_query =  "";
												}else{
													$employee_odjelid = getZaposlenikDepartmentR($logged_employee_id);
													$filter_query =  " AND employee_odjel = "  . $employee_odjelid ."";
												}
												$query = $db->prepare("
																SELECT employee_id, employee_firstname, employee_lastname, employee_email, employee_status, employee_odjel, employee_image, employee_poslovnica, employee_last_password_change, employee_reset_password, employee_dvag
																FROM idk_employees
																WHERE employee_status != 0 " . $filter_query ."");

												$query->execute();

												while($row = $query->fetch()){

													$employee_id = $row['employee_id'];
													$employee_firstname = $row['employee_firstname'];
													$employee_lastname = $row['employee_lastname'];
													$employee_email = $row['employee_email'];
													$employee_poslovnica = $row['employee_poslovnica'];
													$employee_dvag = $row['employee_dvag'];
													$poslovnica = getBranchNameR($employee_poslovnica);
													$employee_status = explode( ',' , $row['employee_status']); // LIVE

													$threeMonthsAgo = date("Y-m-d H:i:s", strtotime('-3 months'));

													if($row['employee_last_password_change'] < $threeMonthsAgo){
														$employee_last_password_change_text = '<span class="text-danger">'.$row['employee_last_password_change'].'</span>';
													}else{
														$employee_last_password_change_text = $row['employee_last_password_change'];
													}

													if($row['employee_reset_password'] == 1){
														$employee_reset_password =  '<span class="btn btn-success" style="pointer-events: none;">Traženo</span>';
													}else{
														$employee_reset_password = '<span class="btn btn-warning password_change_request" data-employeeid="'.$employee_id.'">Promjeni</span>';
													}

													if($row['employee_image'] == "none"){
														$employee_image = "none.jpg";
													}else{
														$employee_image = $row['employee_image'];
													}

													$employee_status_all = "";
													if(in_array( "1" , $employee_status)){
														$employee_status_all .= "  Administrator <br />";
													}

													if(in_array( "2" , $employee_status)){
														$employee_status_all .= "  Projekt Menadžer <br />";
													}

													if(in_array( "3" , $employee_status)){
														$employee_status_all .= "  Projekt Asistent <br />";
													}
													if(in_array( "4" , $employee_status)){
														$employee_status_all .= "   Obrada <br />";
													}
													if(in_array( "5" , $employee_status)){
														$employee_status_all .= "  Front Office <br />";
													}
													if(in_array( "6" , $employee_status)){
														$employee_status_all .= "  Tehnika<br />";
													}
													if(in_array( "7" , $employee_status)){
														$employee_status_all .= "   Marketing <br />";
													}
													if(in_array( "8" , $employee_status)){
														$employee_status_all .= "   Vanjski Saradnik <br />";
													}
													if(in_array( "9" , $employee_status)){
														$employee_status_all .= "   Financije <br />";
													}
													if(in_array( "10" , $employee_status)){
														$employee_status_all .= "  First Call Agent <br />";
													}
													if(in_array( "11" , $employee_status)){
														$employee_status_all .= "  Saradnik - DAK <br />";
													}
													if(in_array( "12" , $employee_status)){
														$employee_status_all .= "  Saradnik - Prevodioc <br />";
													}
													if(in_array( "13" , $employee_status)){
														$employee_status_all .= "  Prevod <br />";
													}
													if(in_array( "14" , $employee_status)){
														$employee_status_all .= "  Inkaso agent <br />";
													}
													if(in_array( "15" , $employee_status)){
														$employee_status_all .= "  Dipl Agent <br />";
													}
													if(in_array( "16" , $employee_status)){
														$employee_status_all .= "  Obrada Dipl <br />";
													}
													if(in_array( "17" , $employee_status)){
														$employee_status_all .= "  Lilium marketing <br />";
													}
													if(in_array( "18" , $employee_status)){
														$employee_status_all .= "  TF Agent <br />";
													}
													if(in_array( "19" , $employee_status)){
														$employee_status_all .= "  Dysordian Dev <br />";
													}
													if(in_array( "20" , $employee_status)){
														$employee_status_all .= "  Ama-Int Saradnik <br />";
													}
													if(in_array( "0" , $employee_status)){
														$employee_status_all .= "  Deaktiviran <br />";
													}

													if($row['employee_odjel'] == 1){
														$employee_odjel = "Uprava";
													}elseif($row['employee_odjel'] == 2){
														$employee_odjel = "Financije";
													}elseif($row['employee_odjel'] == 3){
														$employee_odjel = "Prodaja";
													}elseif($row['employee_odjel'] == 4){
														$employee_odjel = "Obrada";
													}elseif($row['employee_odjel'] == 5){
														$employee_odjel = "Sve za vizu";
													}elseif($row['employee_odjel'] == 6){
														$employee_odjel = "Marketing";
													}elseif($row['employee_odjel'] == 7){
														$employee_odjel = "Tehnika";
													}elseif($row['employee_odjel'] == 8){
														$employee_odjel = "Development";
													}elseif($row['employee_odjel'] == 9){
														$employee_odjel = "Ostalo";
													}else{
														$employee_odjel = "-";
													}

													if($employee_dvag == 0){
														$employee_dvag_text = '<span class="btn btn-success dvag_change_request" data-employeeid="'.$employee_id.'" data-dvagstatus="1">Aktiviraj</span>';
													}else{
														$employee_dvag_text = '<span class="btn btn-warning dvag_change_request" data-employeeid="'.$employee_id.'" data-dvagstatus="0">Deaktiviraj</span>';
													}

													//Get primary phone
													$query_phone = $db->prepare("
																		SELECT ei_data
																		FROM idk_employees_info
																		WHERE ei_group = :ei_group AND ei_primary = :ei_primary AND ei_employeeid = :ei_employeeid");

													$query_phone->execute(array(
														':ei_group' => 1,
														':ei_primary' => 1,
														':ei_employeeid' => $employee_id));

													$row_phone = $query_phone->fetch();

													$employee_phone = $row_phone['ei_data'];
											?>
											<tr>
												<td class="text-center"><a href="<?php getSiteURL(); ?>employees?page=open&id=<?php echo $employee_id; ?>"><img class="idk_profile_img" src="<?php getSiteURL(); ?>files/employees/<?php echo $employee_image; ?>"></a></td>
												<td><a href="<?php getSiteURL(); ?>employees?page=open&id=<?php echo $employee_id; ?>"><?php echo $employee_firstname; ?> <?php echo $employee_lastname; ?></a></td>
												<td class="text-center"><?php echo getIconTeam($employee_id); ?></td>
												<td><?php echo $poslovnica; ?></td>
												<td><?php echo $employee_odjel; ?></td>
												<td><a href="tel:<?php echo $employee_phone; ?>"><?php echo $employee_phone; ?></a></td>
												<td><a href="mailto:<?php echo $employee_email; ?>"><?php echo $employee_email; ?></a></td>
												<td><?php echo $employee_dvag_text; ?></td>
												<td><?php echo $employee_status_all; ?></td>
												<td><?php echo $employee_last_password_change_text; ?></td>
												<td class="text-center"><?php echo $employee_reset_password; ?></td>
												<td class="text-center">
													<div class="btn-group material-btn-group">
														<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
														<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
															<li><a href="<?php getSiteURL(); ?>employees?page=open&id=<?php echo $employee_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori</a></li>
															<li><a href="<?php getSiteURL(); ?>employees?page=edit&id=<?php echo $employee_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Uredi</a></li>
															<li class="idk_dropdown_danger"><a href="#" data="<?php getSiteURL(); ?>employees?page=archive&id=<?php echo $employee_id; ?>" data-toggle="modal" data-target="#archiveModal" class="archive material-dropdown-menu__link"><i class="fa fa-trash-o" aria-hidden="true"></i> Arhiviraj</a></li>
														</ul>
													</div>
												</td>
											</tr>
											<?php } ?>

											<script>
												$(".archive").click(function () {
													var addressValue = $(this).attr("data");
													document.getElementById("archive_link").href = addressValue;
												});

												$('.password_change_request').on('click', function(e){
																
													var employee_id = $(this).data('employeeid');
													$.ajax({
														type: "POST",
														url: "<?php getSiteURL(); ?>do.php?form=reset_password_request",
														data: {employee_id: employee_id},
														success: function(data){
															$('.password_change_request[data-employeeid="' + employee_id + '"]').removeClass('btn-warning').addClass('btn-success').html('Traženo').css('pointer-events', 'none');
														}
													});
												});
												
												$('.dvag_change_request').on('click', function(e){
																
													var employee_id = $(this).data('employeeid');
													var dvag_status = $(this).data('dvagstatus');
													$.ajax({
														type: "POST",
														url: "<?php getSiteURL(); ?>do.php?form=change_dvag_status",
														data: {employee_id: employee_id, dvag_status: dvag_status},
														success: function(data){
															if(dvag_status == 0){
																$('.dvag_change_request[data-employeeid="' + employee_id + '"]').removeClass('btn-warning ').addClass('btn-success').data('dvagstatus', '1').html('Aktiviraj');
															}else{
																$('.dvag_change_request[data-employeeid="' + employee_id + '"]').removeClass('btn-success').addClass('btn-warning').data('dvagstatus', '0').html('Deaktiviraj');
															}
														}
													});
												}); 
											</script>
											<!-- Modal -->
											<div class="modal material-modal material-modal_danger fade" id="archiveModal">
												<div class="modal-dialog">
													<div class="modal-content material-modal__content">
														<div class="modal-header material-modal__header">
															<button class="close material-modal__close" data-dismiss="modal">&times;</button>
															<h4 class="modal-title material-modal__title">Arhiviranje</h4>
														</div>
														<div class="modal-body material-modal__body">
															<p>Jeste li sigurni da želite arhivirati zaposlenika?</p>
														</div>
														<div class="modal-footer material-modal__footer">
															<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
															<a id="archive_link" href=""><button class="btn btn-primary material-btn material-btn_danger">ARHIVIRAJ</button></a>
														</div>
													</div>
												</div>
											</div>
										</tbody>
									</table>

							</div>
						</div>
					</div>
				</div>
			</div>
		<?php
				break;

				case "list_arhiva":

		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-users idk_color_green" aria-hidden="true"></i> Arhiva zaposlenika</h1>
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
										echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novog zaposlenika.</div>';
									}elseif($mess == 2){
										echo '<div class="alert material-alert material-alert_danger">Greška: Korisnik kojeg pokuštavate dodati već postoji u bazi podataka.</div>';
									}elseif($mess == 3){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste uredili profil zaposlenika.</div>';
									}elseif($mess == 4){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste arhivirali profil zaposlenika.</div>';
									}
								?>
								<script type="text/javascript">
									$(document).ready(function() {
										$('#idk_table').DataTable({

											responsive: true,

											"order": [[ 1, "asc" ]],

											 "bAutoWidth": false,

											"aoColumns": [
													{ "width": "5%", "bSortable": false },
													{ "width": "30%" },
													{ "width": "15%" },
													{ "width": "25%" },
													{ "width": "15%" },
													{ "width": "10%", "bSortable": false }
												]
										});
									} );
								</script>
								<table id="idk_table" class="display" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th></th>
											<th>Ime i prezime</th>
											<th>Telefon</th>
											<th>E-mail</th>
											<th>Status</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										<?php
											$query = $db->prepare("
															SELECT employee_id, employee_firstname, employee_lastname, employee_email, employee_status, employee_image
															FROM idk_employees
															WHERE employee_status = 0");

											$query->execute();

											while($row = $query->fetch()){

												$employee_id = $row['employee_id'];
												$employee_firstname = $row['employee_firstname'];
												$employee_lastname = $row['employee_lastname'];
												$employee_email = $row['employee_email'];

												if($row['employee_image'] == "none"){
													$employee_image = "none.jpg";
												}else{
													$employee_image = $row['employee_image'];
												}

												if($row['employee_status'] == 0){
													$employee_status = "Arhiviran";
												}elseif($row['employee_status'] == 1){
													$employee_status = "Administrator";
												}elseif($row['employee_status'] == 2){
													$employee_status = "Super korisnik";
												}elseif($row['employee_status'] == 3){
													$employee_status = "Korisnik";
												}elseif($row['employee_status'] == 4){
													$employee_status = "Saradnik";
												}

												//Get primary phone
												$query_phone = $db->prepare("
																	SELECT ei_data
																	FROM idk_employees_info
																	WHERE ei_group = :ei_group AND ei_primary = :ei_primary AND ei_employeeid = :ei_employeeid");

												$query_phone->execute(array(
													':ei_group' => 1,
													':ei_primary' => 1,
													':ei_employeeid' => $employee_id));

												$row_phone = $query_phone->fetch();

												$employee_phone = $row_phone['ei_data'];
										?>
										<tr>
											<td class="text-center"><a href="<?php getSiteURL(); ?>employees?page=open&id=<?php echo $employee_id; ?>"><img class="idk_profile_img" src="<?php getSiteURL(); ?>files/employees/<?php echo $employee_image; ?>"></a></td>
											<td><a href="<?php getSiteURL(); ?>employees?page=open&id=<?php echo $employee_id; ?>"><?php echo $employee_firstname; ?> <?php echo $employee_lastname; ?></a></td>
											<td><a href="tel:<?php echo $employee_phone; ?>"><?php echo $employee_phone; ?></a></td>
											<td><a href="mailto:<?php echo $employee_email; ?>"><?php echo $employee_email; ?></a></td>
											<td><?php echo $employee_status; ?></td>
											<td class="text-center">
												<div class="btn-group material-btn-group">
													<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
													<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
														<li><a href="<?php getSiteURL(); ?>employees?page=open&id=<?php echo $employee_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori</a></li>
														<li><a href="<?php getSiteURL(); ?>employees?page=edit&id=<?php echo $employee_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Uredi</a></li>
														<li class="idk_dropdown_danger"><a href="#" data="<?php getSiteURL(); ?>employees?page=archive&id=<?php echo $employee_id; ?>" data-toggle="modal" data-target="#archiveModal" class="archive material-dropdown-menu__link"><i class="fa fa-trash-o" aria-hidden="true"></i> Arhiviraj</a></li>
													</ul>
												</div>
											</td>
										</tr>
										<?php } ?>
										<script>
											$(".archive").click(function () {
												var addressValue = $(this).attr("data");
												document.getElementById("archive_link").href = addressValue;
											});
										</script>
										<!-- Modal -->
										<div class="modal material-modal material-modal_danger fade" id="archiveModal">
											<div class="modal-dialog">
												<div class="modal-content material-modal__content">
													<div class="modal-header material-modal__header">
														<button class="close material-modal__close" data-dismiss="modal">&times;</button>
														<h4 class="modal-title material-modal__title">Arhiviranje</h4>
													</div>
													<div class="modal-body material-modal__body">
														<p>Jeste li sigurni da želite arhivirati zaposlenika?</p>
													</div>
													<div class="modal-footer material-modal__footer">
														<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
														<a id="archive_link" href=""><button class="btn btn-primary material-btn material-btn_danger">ARHIVIRAJ</button></a>
													</div>
												</div>
											</div>
										</div>
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
					if((in_array("1", $getEmployeeStatus)) OR (in_array( "2" , $getEmployeeStatus))){ // REPLACE
		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-users idk_color_green" aria-hidden="true"></i> Dodaj novog zaposlenika</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>employees?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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
								<form id="idk_form" action="<?php getSiteURL(); ?>do.php?form=add_employees" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<div class="form-group">
										<label for="employee_firstname" class="col-sm-3 control-label"><span class="text-danger">*</span> Ime:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="employee_firstname" id="employee_firstname" placeholder="Ime" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_lastname" class="col-sm-3 control-label"><span class="text-danger">*</span> Prezime:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="employee_lastname" id="employee_lastname" placeholder="Prezime" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_jmbg" class="col-sm-3 control-label">JMBG:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="number" name="employee_jmbg" id="employee_jmbg" placeholder="JMBG">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_email" class="col-sm-3 control-label"><span class="text-danger">*</span> Primarni email:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="email" name="employee_email" id="employee_email" placeholder="Primarni email" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_password" class="col-sm-3 control-label"><span class="text-danger">*</span> Lozinka:</label>
										<div class="col-sm-7">
											<div class="materail-input-block materail-input-block_success">
											<input class="form-control materail-input" type="text" name="employee_password_new" id="employee_password_new" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{12,}" placeholder="Lozinka" disabled>
												<input class="form-control materail-input" type="hidden" name="employee_password" id="employee_password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{12,}" placeholder="Lozinka">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
										<div class="col-sm-1">
											<a id="generate_new_password" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive" style="padding-right: 0px!important;"><i class="fa fa-refresh" style="margin: 0;"></i> </a>
										</div>
										<div class="col-sm-1">
											<a onClick='copy_password()' class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive" style="padding-right: 0px!important;"><i class="fa fa-clone" style="margin: 0;"></i> </a>
										</div>
									</div>
									<script>
										$('#generate_new_password').click(function() {

											function generatePassword() {
											var length = 12; // Minimum password length
											var characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_+=<>?';
											var password = '';

											while (password.length < length || !isPasswordValid(password)) {
												password = '';
												while (password.length < length) {
													var char = characters[Math.floor(Math.random() * characters.length)];
													password += char;
												}
											}

											return password;
											}

											function isPasswordValid(password) {
												var regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()-_+=<>?]).{8,}$/;
												var uniqueCharacters = new Set(password.split('')).size >= 4;

												return regex.test(password) && uniqueCharacters;
											}

											var password = generatePassword();

											$('#employee_password_new').val(password);
											$('#employee_password').val(password);
											// $('#employee_password').val($.passGen({'length' : 15, 'numeric' : true, 'lowercase' : true, 'uppercase' : true, 'special' : false}));
											
											// password.select();
											// document.execCommand("copy");
											copyToClipboard(password);
											alert("Lozinka kopirana.");
										});

										function copyToClipboard(text) {
											navigator.clipboard.writeText(text);
										}


										function copy_password(){
											var copyText = document.getElementById("employee_password");
											copyText.select();
											document.execCommand("copy");
											alert("Uspješno kopirana lozinka");
										}
									</script>
									<div class="form-group">
										<label for="employee_color" class="col-sm-3 control-label">Boja:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="color" name="employee_color" id="employee_color" value="#4092d9" />
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_rfid" class="col-sm-3 control-label">RFID:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="employee_rfid" id="employee_rfid" placeholder="RFID">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_position" class="col-sm-3 control-label">Pozicija:</label>
										<div class="col-sm-9">
											<select class="selectpicker" id="employee_position" name="employee_position">
												<option value=""></option>
												<?php
													$select_query = $db->prepare("
																		SELECT otherdata_data
																		FROM idk_otherdata
																		WHERE otherdata_group = :otherdata_group");

													$select_query->execute(array(
																	':otherdata_group' => 1));

													while($select_row = $select_query->fetch()) {
														echo "<option value='" . $select_row['otherdata_data'] . "'>" . $select_row['otherdata_data'] . "</option>";
													}
												?>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_dob" class="col-sm-3 control-label">Datum rođenja:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="employee_dob" id="employee_dob" placeholder="Datum rođenja">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
										<script>
											$("#employee_dob").flatpickr({
												dateFormat: "d.m.Y.",
												disableMobile: "true"
											});
										</script>
									</div>
									<div class="form-group">
										<label for="employee_doe" class="col-sm-3 control-label"><span class="text-danger">*</span> Datum zaposlenja:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="employee_doe" id="employee_doe" placeholder="Datum zaposlenja" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
										<script>
											$("#employee_doe").flatpickr({
												dateFormat: "d.m.Y.",
												disableMobile: "true"
											});
										</script>
									</div>
									<div class="form-group">
										<label for="employee_phone" class="col-sm-3 control-label">Primarni telefon:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="employee_phone" id="employee_phone" placeholder="Primarni telefon">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_address" class="col-sm-3 control-label">Adresa: </label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="employee_address" id="employee_address" placeholder="Adresa">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_city" class="col-sm-3 control-label">Grad:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="employee_city" id="employee_city" placeholder="Grad">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_country" class="col-sm-3 control-label">Država:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="employee_country" id="employee_country" placeholder="Država">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_info" class="col-sm-3 control-label">Ostale informacije:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="employee_info" id="employee_info" placeholder="Ostale informacije">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group" id = "employee_status_sh">
										<div class="col-sm-3">
										</div>
										<div class="col-sm-9">
											<div id = "employee_status_alert" class="alert alert-danger" style = "margin-bottom: 5px;" role="alert">
												
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_status" class="col-sm-3 control-label"><span class="text-danger">*</span> Status:</label>
										<div class="col-sm-9">
											<select class="selectpicker" id="employee_status" name="employee_status[]" data-live-search="true" multiple data-actions-box="true" required>
												<option value="1">Administrator</option>
												<option value="2">Projekt Menadžer</option>
												<option value="3" >Projekt Asistent</option>
												<option value="4" >Obrada</option>
												<option value="5" >Front Office</option>
												<option value="6" >Tehnika</option>
												<option value="7" >Marketing</option>
												<option value="8" >Vanjski Saradnik</option>
												<option value="9" >Financije</option>
												<option value="10">First Call Agent</option>
												<option value="11">Saradnik - DAK</option>
												<option value="12">Saradnik - Prevodioc</option>
												<option value="13">Prevod</option>
												<option value="14">Inkaso Agent</option>
												<option value="15">Dipl Agent</option>
												<option value="16">Obrada Dipl</option>
												<option value="18">TF Agent</option>
												<option value="19">Dysordian Dev</option>
												<option value="20">Ama-Int Saradnik</option>
												<option value="0">Deaktiviran</option>
											</select>
										</div>
									</div>
									<script>
										$(document).ready(function(){
											$('#employee_status_sh').hide();
										});
										$("#employee_status").change(function(){
											if (jQuery.inArray('1', $('#employee_status').val()) != -1){
												$('#employee_status').val(1);
												$('#employee_status').selectpicker('refresh');
												$("#employee_status_alert").html("U slučaju izbora statusa Administrator, nije moguće odabrati ni jednu drugu opciju.");
												$('#employee_status_sh').show();
												setTimeout(function(){
													$('#employee_status_sh').hide();
												}, 5000);
											}
											if (jQuery.inArray('15', $('#employee_status').val()) != -1){
												$('#employee_status').val(15);
												$('#employee_status').selectpicker('refresh');
												$("#employee_status_alert").html("U slučaju izbora statusa Obrada Dipl, nije moguće odabrati ni jednu drugu opciju.");
												$('#employee_status_sh').show();
												setTimeout(function(){
													$('#employee_status_sh').hide();
												}, 5000);
											}
											if (jQuery.inArray('0', $('#employee_status').val()) != -1){
												$('#employee_status').val(0);
												$('#employee_status').selectpicker('refresh');
												$('#employee_supervizor').val(null);
												$('#employee_supervizor').selectpicker('refresh');
												$("#employee_status_alert").html("U slučaju izbora statusa Deaktiviran, nije moguće odabrati ni jednu drugu opciju.");
												$('#employee_status_sh').show();
												setTimeout(function(){
													$('#employee_status_sh').hide();
												}, 5000);
											}
										});
									</script>
									<div class="form-group">
										<label for="employee_status" class="col-sm-3 control-label"> Supervizor:</label>
										<div class="col-sm-9">
											<select class="selectpicker" id="employee_supervizor" name="employee_supervizor[]" data-live-search="true" multiple data-actions-box="true">
												<!--<option value=""></option>-->
												<option value="2" >Projekt Menadžer</option>
												<option value="3" >Projekt Asistent</option>
												<option value="4" >Obrada</option>
												<option value="5">Front Office</option>
												<option value="6" >Tehnika</option>
												<option value="7" >Marketing</option>
												<option value="8">Vanjski Saradnik</option>
												<option value="9" >Financije</option>
												<option value="10">First Call Agent</option>
												<option value="15">Dipl Agent Vođa</option>
												<option value="16">Obrada Dipl</option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_odjel" class="col-sm-3 control-label"><span class="text-danger">*</span> Odjel:</label>
										<div class="col-sm-9">
											<select class="selectpicker" id="employee_odjel" name="employee_odjel" required>
												<option value=""></option>
												<option value="1">Uprava</option>
												<option value="2">Financije</option>
												<option value="3">Prodaja</option>
												<option value="4">Obrada</option>
												<option value="5">Sve za vizu</option>
												<option value="6">Marketing</option>
												<option value="7">Tehnika</option>
												<option value="8">Development</option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label for="employe_vicidial_user" class="col-sm-3 control-label">Vicidial podaci - Korisničko ime:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="employe_vicidial_user" id="employe_vicidial_user"  placeholder="Vicidial pristupni username">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="employe_vicidial_pass" class="col-sm-3 control-label">Vicidial podaci - Lozinka:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="employe_vicidial_pass" id="employe_vicidial_pass" placeholder="Vicidial pristupni password">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_poslovnica" class="col-sm-3 control-label">Poslovnica:</label>
										<div class="col-sm-9">
											<select class="selectpicker" id="employee_poslovnica" name="employee_poslovnica" title ="Odaberite poslovnicu" data-live-search = "true">
												<?php
													$q_pos = $db->prepare("SELECT * FROM idk_poslovnice");
													$q_pos->execute();
													while($q_pos_row = $q_pos->fetch()){
														$b_id = $q_pos_row['branch_id'];
														$b_name = $q_pos_row['branch_name'];
														$b_state = $q_pos_row['branch_state'];
														$b_city = $q_pos_row['branch_city'];
														echo '<option value = "'.$b_id.'" data-subtext="'.$b_city.', '.$b_state.'">'.$b_name.'</option>';
													}
												?>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_team" class="col-sm-3 control-label"><span class="text-danger">*</span> Tim:</label>
										<div class="col-sm-9">
											<select class="selectpicker" id="employee_team" name="employee_team" title ="Odaberite tim" data-live-search = "true" required>
												<?php
													$q_team = $db->prepare("SELECT * FROM idk_timovi WHERE status_t = 1");
													$q_team->execute();
													while($q_team_row = $q_team->fetch()){
														echo '<option value = "'.$q_team_row["id_t"].'">'.$q_team_row["naziv_t"].'</option>';
													}
												?>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_image" class="col-sm-3 control-label">Fotografija:</label>
										<div class="col-sm-9">
											<div class="fileinput fileinput-new" data-provides="fileinput">
												<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;"></div>
												<div>
													<span class="btn btn-default btn-file"><span class="fileinput-new">Izaberi fotografiju</span><span class="fileinput-exists">Promijeni</span><input type="file" name="employee_image" id="employee_image"></span>
													<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Ukloni</a>
													<script>
														$(function (){
															$('#employee_image').change(function (){

																var ext = $('#employee_image').val().split('.').pop().toLowerCase();

																if($.inArray(ext, ['jpg', 'jpeg', 'png']) == -1) {
																	$('#idk_alert_ext').removeClass('hidden');
																	this.value = null;
																}else{
																	$('#idk_alert_ext').addClass('hidden');
																}

																var f = this.files[0];

																if (f.size > 20388608 || f.fileSize > 20388608){
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

									<div class="form-group">
										<label class="col-sm-3"></label>
										<div class="col-sm-9">
											<div id="idk_alert_size" class="hidden">
												<div class="alert material-alert material-alert_danger">Greška: Fotografiju koju pokuštavate dodati je veća od dozvoljene veličine.</div>
											</div>
											<div id="idk_alert_ext" class="hidden">
												<div class="alert material-alert material-alert_danger">Greška: Format fotografije koju pokušavate dodati nije dozvoljen.</div>
											</div>
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
					if((in_array("1", $getEmployeeStatus)) OR (in_array( "2" , $getEmployeeStatus))){

						$employee_id = $_GET['id'];

						$query = $db->prepare("
										SELECT employee_firstname, employee_lastname, employee_jmbg, employee_email, employee_color, employee_rfid, employee_position, employee_dob, employee_doe, employee_address, employee_city, employee_country, employee_info, employee_status, employee_supervizor, employee_odjel, employee_image, employee_warehouse, employee_poslovnica, employee_team,employee_viciuser,employee_vicipass
										FROM idk_employees
										WHERE employee_id = :employee_id");

						$query->execute(array(
									':employee_id' => $employee_id));

						$row = $query->fetch();

							$employee_firstname = $row['employee_firstname'];
							$employee_lastname = $row['employee_lastname'];
							$employee_jmbg = $row['employee_jmbg'];
							$employee_position = $row['employee_position'];
							$employee_dob = date('d.m.Y.', strtotime($row['employee_dob']));
							$employee_doe = date('d.m.Y.', strtotime($row['employee_doe']));
							$employee_email = $row['employee_email'];
							$employee_color = $row['employee_color'];
							$employee_rfid = $row['employee_rfid'];
							$employee_address = $row['employee_address'];
							$employee_city = $row['employee_city'];
							$employee_country = $row['employee_country'];
							$employee_info = $row['employee_info'];
							$employee_status = explode( ',' , $row['employee_status']); // LIVE
							$employee_supervizor = explode( ',' , $row['employee_supervizor']); // POVUCI IZ QUERYA GORE
							$employee_odjel = $row['employee_odjel'];
							$employee_poslovnica = $row['employee_poslovnica'];
							$employee_warehouse = $row['employee_warehouse'];
							$employee_team = $row['employee_team'];
							
							$employe_viciUsername = $row['employee_viciuser'];
							$employe_viciPassword = $row['employee_vicipass'];
							

							if($row['employee_image'] == "none"){
								$employee_image = "none.jpg";
							}else{
								$employee_image = $row['employee_image'];
							}

		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-users idk_color_green" aria-hidden="true"></i> Uredi profile zaposlenika</h1>
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
								<form id="idk_form" action="<?php getSiteURL(); ?>do.php?form=edit_employees" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<input type="hidden" name="employee_id" value="<?php echo $employee_id; ?>" />
									<div class="form-group">
										<label for="employee_firstname" class="col-sm-3 control-label"><span class="text-danger">*</span> Ime:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="employee_firstname" id="employee_firstname" value="<?php echo $employee_firstname; ?>" placeholder="Ime" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_lastname" class="col-sm-3 control-label"><span class="text-danger">*</span> Prezime:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="employee_lastname" id="employee_lastname" value="<?php echo $employee_lastname; ?>" placeholder="Prezime" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_jmbg" class="col-sm-3 control-label">JMBG:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="number" name="employee_jmbg" id="employee_jmbg" value="<?php echo $employee_jmbg; ?>" placeholder="JMBG">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_email" class="col-sm-3 control-label"><span class="text-danger">*</span> Primarni email:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="email" name="employee_email" id="employee_email" value="<?php echo $employee_email; ?>" placeholder="Primarni email" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_password" class="col-sm-3 control-label"> Lozinka:</label>
										<div class="col-sm-7">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="employee_password_new" id="employee_password_new" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{12,}" placeholder="Lozinka" disabled>
												<input class="form-control materail-input" type="hidden" name="employee_password" id="employee_password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{12,}" placeholder="Lozinka">
												<span class="materail-input-block__line"></span>
											</div>
											<small>Ukoliko želite promijeniti lozinku, generišite novu.</small>
										</div>
										<div class="col-sm-1">
											<a id="generate_new_password" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive" style="padding-right: 0px!important;"><i class="fa fa-refresh" style="margin: 0;"></i> </a>
										</div>
										<div class="col-sm-1">
											<a onClick='copy_password_edit()' class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive" style="padding-right: 0px!important;"><i class="fa fa-clone" style="margin: 0;"></i> </a>
										</div>
									</div>
									<script>
										$('#generate_new_password').click(function() {

											function generatePassword() {
											var length = 12; // Minimum password length
											var characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_+=<>?';
											var password = '';

											while (password.length < length || !isPasswordValid(password)) {
												password = '';
												while (password.length < length) {
													var char = characters[Math.floor(Math.random() * characters.length)];
													password += char;
												}
											}

											return password;
											}

											function isPasswordValid(password) {
												var regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()-_+=<>?]).{8,}$/;
												var uniqueCharacters = new Set(password.split('')).size >= 4;

												return regex.test(password) && uniqueCharacters;
											}

											var password = generatePassword();

											$('#employee_password_new').val(password);
											$('#employee_password').val(password);
											// $('#employee_password').val($.passGen({'length' : 15, 'numeric' : true, 'lowercase' : true, 'uppercase' : true, 'special' : false}));
											
											// password.select();
											// document.execCommand("copy");
											copyToClipboard(password);
											alert("Lozinka kopirana.");
										});

										function copyToClipboard(text) {
											navigator.clipboard.writeText(text);
										}


										function copy_password_edit(){
											var copyText = document.getElementById("employee_password");
											copyText.select();
											document.execCommand("copy");
											alert("Uspješno kopirana lozinka");
										}
									</script>
									<div class="form-group">
										<label for="employee_color" class="col-sm-3 control-label">Boja:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="color" name="employee_color" id="employee_color" value="<?php echo $employee_color; ?>" />
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_rfid" class="col-sm-3 control-label">RFID:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="employee_rfid" id="employee_rfid" value="<?php echo $employee_rfid; ?>" placeholder="RFID">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_position" class="col-sm-3 control-label">Pozicija:</label>
										<div class="col-sm-9">
											<select class="selectpicker" id="employee_position" name="employee_position">
												<option value=""></option>
												<?php
													$select_query = $db->prepare("
																		SELECT otherdata_data
																		FROM idk_otherdata
																		WHERE otherdata_group = :otherdata_group");

													$select_query->execute(array(
																	':otherdata_group' => 1));

													while($select_row = $select_query->fetch()) {

														if($employee_position == $select_row['otherdata_data']){ $selected = "selected"; }else{ $selected = ""; }

														echo "<option value='" . $select_row['otherdata_data'] . "' " . $selected . ">" . $select_row['otherdata_data'] . "</option>";
													}
												?>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_dob" class="col-sm-3 control-label">Datum rođenja:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="employee_dob" id="employee_dob" placeholder="Datum rođenja">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
										<script>
											$("#employee_dob").flatpickr({
												dateFormat: "d.m.Y.",
												disableMobile: "true",
												defaultDate: ["<?php echo $employee_dob; ?>"]
											});
										</script>
									</div>
									<div class="form-group">
										<label for="employee_doe" class="col-sm-3 control-label"><span class="text-danger">*</span> Datum zaposlenja:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="employee_doe" id="employee_doe" placeholder="Datum zaposlenja" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
										<script>
											$("#employee_doe").flatpickr({
												dateFormat: "d.m.Y.",
												disableMobile: "true",
												defaultDate: ["<?php echo $employee_doe; ?>"]
											});
										</script>
									</div>
									<div class="form-group">
										<label for="employee_address" class="col-sm-3 control-label">Adresa: </label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="employee_address" id="employee_address" value="<?php echo $employee_address; ?>" placeholder="Adresa">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_city" class="col-sm-3 control-label">Grad:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="employee_city" id="employee_city" value="<?php echo $employee_city; ?>" placeholder="Grad">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_country" class="col-sm-3 control-label">Država:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="employee_country" id="employee_country" value="<?php echo $employee_country; ?>" placeholder="Država">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_info" class="col-sm-3 control-label">Ostale informacije:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="employee_info" id="employee_info" value="<?php echo $employee_info; ?>" placeholder="Ostale informacije">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group" id = "employee_status_sh">
										<div class="col-sm-3">
										</div>
										<div class="col-sm-9">
											<div id = "employee_status_alert" class="alert alert-danger" style = "margin-bottom: 5px;" role="alert">
												
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_status" class="col-sm-3 control-label"><span class="text-danger">*</span> Status:</label>
										<div class="col-sm-9">
											<select class="selectpicker" id="employee_status" name="employee_status[]" data-live-search="true" multiple data-actions-box="true" required>
												<option value="1" <?php if(in_array( "1" , $employee_status)){ echo "selected"; } ?>>Administrator</option>
												<option value="2" <?php if(in_array( "2" , $employee_status)){ echo "selected"; } ?>>Projekt Menadžer</option>
												<option value="3" <?php if(in_array( "3" , $employee_status)){ echo "selected"; } ?>>Projekt Asistent</option>
												<option value="4" <?php if(in_array( "4" , $employee_status)){ echo "selected"; } ?>>Obrada</option>
												<option value="5" <?php if(in_array( "5" , $employee_status)){ echo "selected"; } ?>>Front Office</option>
												<option value="6" <?php if(in_array( "6" , $employee_status)){ echo "selected"; } ?>>Tehnika</option>
												<option value="7" <?php if(in_array( "7" , $employee_status)){ echo "selected"; } ?>>Marketing</option>
												<option value="8" <?php if(in_array( "8" , $employee_status)){ echo "selected"; } ?>>Vanjski Saradnik</option>
												<option value="9" <?php if(in_array( "9" , $employee_status)){ echo "selected"; } ?>>Financije</option>
												<option value="10" <?php if(in_array( "10" , $employee_status)){ echo "selected"; } ?>>First Call Agent</option>
												<option value="11" <?php if(in_array( "11" , $employee_status)){ echo "selected"; } ?>>Saradnik - DAK</option>
												<option value="12" <?php if(in_array( "12" , $employee_status)){ echo "selected"; } ?>>Saradnik - Prevodioc</option>
												<option value="13" <?php if(in_array( "13" , $employee_status)){ echo "selected"; } ?>>Prevod</option>
												<option value="14" <?php if(in_array( "14" , $employee_status)){ echo "selected"; } ?>>Inkaso Agent</option>
												<option value="15" <?php if(in_array( "15" , $employee_status)){ echo "selected"; } ?>>Dipl Agent</option>
												<option value="16" <?php if(in_array( "16" , $employee_status)){ echo "selected"; } ?>>Obrada Dipl</option>
												<option value="17" <?php if(in_array( "17" , $employee_status)){ echo "selected"; } ?>>Lilium marketing</option>
												<option value="18" <?php if(in_array( "18" , $employee_status)){ echo "selected"; } ?>>TF Agent</option>
												<option value="19" <?php if(in_array( "19" , $employee_status)){ echo "selected"; } ?>>Dysordian Dev</option>
												<option value="20" <?php if(in_array( "20" , $employee_status)){ echo "selected"; } ?>>Ama-Int Saradnik</option>
												<option value="69" <?php if(in_array( "69" , $employee_status)){ echo "selected"; } ?>>MARKUS</option>
												<option value="0" <?php if(in_array( "0" , $employee_status)){ echo "selected"; } ?>>Deaktiviran</option>
											</select>
										</div>
									</div>
									<script>
										$(document).ready(function(){
											$('#employee_status_sh').hide();
											if (jQuery.inArray('0', $('#employee_status').val()) != -1){
												$('#employee_status').val(0);
												$('#employee_status').selectpicker('refresh');
												$('#employee_supervizor').val(null);
												$('#employee_supervizor').selectpicker('refresh');
												$("#employee_status_alert").html("U slučaju izbora statusa Deaktiviran, nije moguće odabrati ni jednu drugu opciju.");
												$('#employee_status_sh').show();
												setTimeout(function(){
													$('#employee_status_sh').hide();
												}, 5000);
											}
											if (jQuery.inArray('1', $('#employee_status').val()) != -1){
												$('#employee_status').val(1);
												$('#employee_status').selectpicker('refresh');
												$("#employee_status_alert").html("U slučaju izbora statusa Administrator, nije moguće odabrati ni jednu drugu opciju.");
												$('#employee_status_sh').show();
												setTimeout(function(){
													$('#employee_status_sh').hide();
												}, 5000);
											}
											
											if (jQuery.inArray('15', $('#employee_status').val()) != -1){
												$('#employee_status').val(15);
												$('#employee_status').selectpicker('refresh');
												$("#employee_status_alert").html("U slučaju izbora statusa Dipl Agent, nije moguće odabrati ni jednu drugu opciju.");
												$('#employee_status_sh').show();
												setTimeout(function(){
													$('#employee_status_sh').hide();
												}, 5000);
											}
										});
										$("#employee_status").change(function(){
											if (jQuery.inArray('1', $('#employee_status').val()) != -1){
												$('#employee_status').val(1);
												$('#employee_status').selectpicker('refresh');
												$("#employee_status_alert").html("U slučaju izbora statusa Administrator, nije moguće odabrati ni jednu drugu opciju.");
												$('#employee_status_sh').show();
												setTimeout(function(){
													$('#employee_status_sh').hide();
												}, 5000);
											}
											if (jQuery.inArray('15', $('#employee_status').val()) != -1){
												$('#employee_status').val(15);
												$('#employee_status').selectpicker('refresh');
												$("#employee_status_alert").html("U slučaju izbora statusa Dipl Agent, nije moguće odabrati ni jednu drugu opciju.");
												$('#employee_status_sh').show();
												setTimeout(function(){
													$('#employee_status_sh').hide();
												}, 5000);
											}
											if (jQuery.inArray('0', $('#employee_status').val()) != -1){
												$('#employee_status').val(0);
												$('#employee_status').selectpicker('refresh');
												$('#employee_supervizor').val(null);
												$('#employee_supervizor').selectpicker('refresh');
												$("#employee_status_alert").html("U slučaju izbora statusa Deaktiviran, nije moguće odabrati ni jednu drugu opciju.");
												$('#employee_status_sh').show();
												setTimeout(function(){
													$('#employee_status_sh').hide();
												}, 5000);
											}
										});
									</script>
									<div class="form-group">
										<label for="employee_status" class="col-sm-3 control-label"> Supervizor:</label>
										<div class="col-sm-9">
											<select class="selectpicker" id="employee_supervizor" name="employee_supervizor[]" data-live-search="true" multiple data-actions-box="true">
												<!--<option value=""></option>-->
												<option value="2" <?php if(in_array( "2" , $employee_supervizor)){ echo "selected"; } ?>>Projekt Menadžer</option>
												<option value="3" <?php if(in_array( "3" , $employee_supervizor)){ echo "selected"; } ?>>Projekt Asistent</option>
												<option value="4" <?php if(in_array( "4" , $employee_supervizor)){ echo "selected"; } ?>>Obrada</option>
												<option value="5" <?php if(in_array( "5" , $employee_supervizor)){ echo "selected"; } ?>>Front Office</option>
												<option value="6" <?php if(in_array( "6" , $employee_supervizor)){ echo "selected"; } ?>>Tehnika</option>
												<option value="7" <?php if(in_array( "7" , $employee_supervizor)){ echo "selected"; } ?>>Marketing</option>
												<option value="8" <?php if(in_array( "8" , $employee_supervizor)){ echo "selected"; } ?>>Vanjski Saradnik</option>
												<option value="9" <?php if(in_array( "9" , $employee_supervizor)){ echo "selected"; } ?>>Financije</option>
												<option value="10" <?php if(in_array( "10" , $employee_supervizor)){ echo "selected"; } ?>>First Call Agent</option>
												<option value="15" <?php if(in_array( "15" , $employee_supervizor)){ echo "selected"; } ?>>Dipl Agent Vođa</option>
												<option value="16" <?php if(in_array( "16" , $employee_supervizor)){ echo "selected"; } ?>>Obrada Dipl</option>
												<option value="18" <?php if(in_array( "18" , $employee_supervizor)){ echo "selected"; } ?>>TF Supervizor</option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_odjel" class="col-sm-3 control-label"><span class="text-danger">*</span> Odjel:</label>
										<div class="col-sm-9">
											<select class="selectpicker" id="employee_odjel" name="employee_odjel" required>
												<option value=""></option>
												<option value="1" <?php if($employee_odjel == "1"){ echo "selected"; } ?>>Uprava</option>
												<option value="2" <?php if($employee_odjel == "2"){ echo "selected"; } ?>>Financije</option>
												<option value="3" <?php if($employee_odjel == "3"){ echo "selected"; } ?>>Prodaja</option>
												<option value="4" <?php if($employee_odjel == "4"){ echo "selected"; } ?>>Obrada</option>
												<option value="5" <?php if($employee_odjel == "5"){ echo "selected"; } ?>>Sve za vizu</option>
												<option value="6" <?php if($employee_odjel == "6"){ echo "selected"; } ?>>Marketing</option>
												<option value="7" <?php if($employee_odjel == "7"){ echo "selected"; } ?>>Tehnika</option>
												<option value="8" <?php if($employee_odjel == "8"){ echo "selected"; } ?>>Development</option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_poslovnica" class="col-sm-3 control-label"><span class="text-danger">*</span> Poslovnica:</label>
										<div class="col-sm-9">
											<select class="selectpicker" id="employee_poslovnica" name="employee_poslovnica" title ="Odaberite poslovnicu" data-live-search = "true" >
												<?php
													$q_pos = $db->prepare("SELECT * FROM idk_poslovnice");
													$q_pos->execute();
													while($q_pos_row = $q_pos->fetch()){
														$b_id = $q_pos_row['branch_id'];
														$b_name = $q_pos_row['branch_name'];
														$b_state = $q_pos_row['branch_state'];
														$b_city = $q_pos_row['branch_city'];
														if($b_id == $employee_poslovnica){
															$b_sel = 'selected';
														}
														else{
															$b_sel = ' ';
														}
														echo '<option value = "'.$b_id.'" data-subtext="'.$b_city.', '.$b_state.'" '.$b_sel.'>'.$b_name.'</option>';
													}
												?>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_team" class="col-sm-3 control-label"><span class="text-danger">*</span> Tim:</label>
										<div class="col-sm-9"> 
												<select class="selectpicker" id="employee_team" name="employee_team" title ="Odaberite tim" data-live-search = "true" required>
												<?php
													$q_team = $db->prepare("SELECT * FROM idk_timovi WHERE status_t = 1");
													$q_team->execute();
													while($q_team_row = $q_team->fetch()){
														$id_t = $q_team_row['id_t'];
														$naziv_t = $q_team_row['naziv_t'];
														if($id_t == $employee_team){
															$t_sel = 'selected';
														}
														else{
															$t_sel = ' ';
														}
														echo '<option value = "'.$id_t.'"'.$t_sel.'>'.$naziv_t.'</option>';
													}
												?>
											</select>
										</div>
									</div>
									<?php if($logged_employee_id == 20 or $logged_employee_id == 412 or $logged_employee_id == 67 or $logged_employee_id == 70){ ?>
									<div class="form-group">
										<label for="employee_warehouse" class="col-sm-3 control-label"><span class="text-danger">*</span> Skladištar:</label>
										<div class="col-sm-9">
											<div class="main-container__column materail-switch materail-switch_primary">
												<input class="materail-switch__element" type="checkbox" id="employee_warehouse" name="employee_warehouse" <?php if($employee_warehouse == "1"){ echo "checked"; } ?>>
												<label class="materail-switch__label" for="employee_warehouse"></label>
											</div>
										</div>
									</div>
									<?php } ?>
									<div class="form-group">
										<label for="employee_image" class="col-sm-3 control-label">Fotografija:</label>
										<div class="col-sm-9">
											<div class="fileinput fileinput-new" data-provides="fileinput">
												<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;">
													<img src="<?php getSiteURL(); ?>files/employees/<?php echo $employee_image; ?>">
												</div>
												<input type="hidden" name="employee_image_url" value="<?php echo $employee_image; ?>" />
												<div>
													<span class="btn btn-default btn-file"><span class="fileinput-new">Izaberi fotografiju</span><span class="fileinput-exists">Promijeni</span><input type="file" name="employee_image" id="employee_image"></span>
													<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Ukloni</a>
													<script>
														$(function (){
															$('#employee_image').change(function (){

																var ext = $('#employee_image').val().split('.').pop().toLowerCase();

																if($.inArray(ext, ['jpg', 'jpeg', 'png']) == -1) {
																	$('#idk_alert_ext').removeClass('hidden');
																	this.value = null;
																}else{
																	$('#idk_alert_ext').addClass('hidden');
																}

																var f = this.files[0];

																if (f.size > 20388608 || f.fileSize > 20388608){
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
									<div class="form-group">
										<label for="employe_vicidial_user" class="col-sm-3 control-label">Vicidial podaci - Korisničko ime:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="employe_vicidial_user"  id="employe_vicidial_user" value="<?php echo $employe_viciUsername; ?>" placeholder="Vicidial pristupni username">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="employe_vicidial_pass" class="col-sm-3 control-label">Vicidial podaci - Lozinka:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="employe_vicidial_pass" id="employe_vicidial_pass" value="<?php echo $employe_viciPassword; ?>" placeholder="Vicidial pristupni password">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-3"></label>
										<div class="col-sm-9">
											<div id="idk_alert_size" class="hidden">
												<div class="alert material-alert material-alert_danger">Greška: Fotografiju koju pokuštavate dodati je veća od dozvoljene veličine.</div>
											</div>
											<div id="idk_alert_ext" class="hidden">
												<div class="alert material-alert material-alert_danger">Greška: Format fotografije koju pokušavate dodati nije dozvoljen.</div>
											</div>
										</div>
									</div>
									<br />
									<div class="form-group">
										<div class="col-sm-offset-2 col-sm-10 text-right">
											<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-save" aria-hidden="true"></i> <span>Snimi</span></button>
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

				case "edit_profile":

						$query = $db->prepare("
										SELECT employee_firstname, employee_lastname, employee_jmbg, employee_color, employee_email, employee_dob, employee_doe, employee_address, employee_city, employee_country, employee_info, employee_image,employee_viciuser,employee_vicipass
										FROM idk_employees
										WHERE employee_id = :employee_id");

						$query->execute(array(
									':employee_id' => $logged_employee_id));

						$row = $query->fetch();

							$employee_firstname = $row['employee_firstname'];
							$employee_lastname = $row['employee_lastname'];
							$employee_jmbg = $row['employee_jmbg'];
							$employee_color = $row['employee_color'];
							$employee_dob = date('d.m.Y.', strtotime($row['employee_dob']));
							$employee_doe = date('d.m.Y.', strtotime($row['employee_doe']));
							$employee_email = $row['employee_email'];
							$employee_address = $row['employee_address'];
							$employee_city = $row['employee_city'];
							$employee_country = $row['employee_country'];
							$employee_info = $row['employee_info'];
							$employe_viciUsername = $row['employee_viciuser'];
							$employe_viciPassword = $row['employee_vicipass'];

							if($row['employee_image'] == "none"){
								$employee_image = "none.jpg";
							}else{
								$employee_image = $row['employee_image'];
							}

		?>
			<div class="row">
				<div class="col-xs-12">
					<h1><i class="fa fa-user idk_color_green" aria-hidden="true"></i> Uredi osobni profile</h1>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<div class="row">
							<?php
								if(isset($_GET['mess'])) {
									$mess = $_GET['mess'];
								}else{
									$mess = 0;
								}

								if($mess == 1){
									echo '<div class="alert material-alert material-alert_success">Uspješno ste uredili vaš profil.</div>';
								}
							?>
							<div class="col-md-offset-1 col-md-8">
								<form id="idk_form" action="<?php getSiteURL(); ?>do.php?form=edit_profile" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<input type="hidden" name="employee_id" value="<?php echo $logged_employee_id; ?>" />
									<div class="form-group">
										<label for="employee_firstname" class="col-sm-3 control-label"><span class="text-danger">*</span> Ime:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="employee_firstname" id="employee_firstname" value="<?php echo $employee_firstname; ?>" placeholder="Ime" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_lastname" class="col-sm-3 control-label"><span class="text-danger">*</span> Prezime:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="employee_lastname" id="employee_lastname" value="<?php echo $employee_lastname; ?>" placeholder="Prezime" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_jmbg" class="col-sm-3 control-label">JMBG:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="number" name="employee_jmbg" id="employee_jmbg" value="<?php echo $employee_jmbg; ?>" placeholder="JMBG">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_color" class="col-sm-3 control-label">Boja:</label>
										<div class="col-sm-1">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="color" name="employee_color" id="employee_color" value="<?php echo $employee_color; ?>" placeholder="Boja">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_email" class="col-sm-3 control-label"><span class="text-danger">*</span> Primarni email:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="email" name="employee_email" id="employee_email" value="<?php echo $employee_email; ?>" placeholder="Primarni email" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_password" class="col-sm-3 control-label"><span class="text-danger">*</span> Lozinka:</label>
										<div class="col-sm-7">
											<div class="materail-input-block materail-input-block_success">
											<input class="form-control materail-input" type="text" name="employee_password_new" id="employee_password_new" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{12,}" placeholder="Lozinka" disabled>
												<input class="form-control materail-input" type="hidden" name="employee_password" id="employee_password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{12,}" placeholder="Lozinka">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
										<div class="col-sm-1">
											<a id="generate_new_password" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive" style="padding-right: 0px!important;"><i class="fa fa-refresh" style="margin: 0;"></i> </a>
										</div>
										<div class="col-sm-1">
											<a onClick='copy_password()' class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive" style="padding-right: 0px!important;"><i class="fa fa-clone" style="margin: 0;"></i> </a>
										</div>
									</div>
									<script>
										$('#generate_new_password').click(function() {

											function generatePassword() {
											var length = 12; // Minimum password length
											var characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_+=<>?';
											var password = '';

											while (password.length < length || !isPasswordValid(password)) {
												password = '';
												while (password.length < length) {
													var char = characters[Math.floor(Math.random() * characters.length)];
													password += char;
												}
											}

											return password;
											}

											function isPasswordValid(password) {
												var regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()-_+=<>?]).{8,}$/;
												var uniqueCharacters = new Set(password.split('')).size >= 4;

												return regex.test(password) && uniqueCharacters;
											}

											var password = generatePassword();

											$('#employee_password_new').val(password);
											$('#employee_password').val(password);
											// $('#employee_password').val($.passGen({'length' : 15, 'numeric' : true, 'lowercase' : true, 'uppercase' : true, 'special' : false}));
											
											// password.select();
											// document.execCommand("copy");
											copyToClipboard(password);
											alert("Lozinka kopirana.");
										});

										function copyToClipboard(text) {
											navigator.clipboard.writeText(text);
										}


										function copy_password(){
											var copyText = document.getElementById("employee_password");
											copyText.select();
											document.execCommand("copy");
											alert("Uspješno kopirana lozinka");
										}
									</script>
									<div class="form-group">
										<label for="employee_dob" class="col-sm-3 control-label">Datum rođenja:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" data-init-set="false" data-large-default="true" data-format="d.m.Y." data-large-mode="true" data-modal="true" type="text" name="employee_dob" id="employee_dob" value="<?php echo $employee_dob; ?>" placeholder="Datum rođenja">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
										<script>
											$( function() {	$( "#employee_dob" ).dateDropper();	} );
										</script>
									</div>
									<div class="form-group">
										<label for="employee_doe" class="col-sm-3 control-label">Datum zaposlenja:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" data-init-set="false" data-large-default="true" data-format="d.m.Y." data-large-mode="true" data-modal="true" type="text" name="employee_doe" id="employee_doe" value="<?php echo $employee_doe; ?>" placeholder="Datum zaposlenja">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
										<script>
											$( function() {	$( "#employee_doe" ).dateDropper();	} );
										</script>
									</div>
									<div class="form-group">
										<label for="employee_address" class="col-sm-3 control-label">Adresa:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="employee_address" id="employee_address" value="<?php echo $employee_address; ?>" placeholder="Adresa">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_city" class="col-sm-3 control-label">Grad:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="employee_city" id="employee_city" value="<?php echo $employee_city; ?>" placeholder="Grad">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_country" class="col-sm-3 control-label">Država:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="employee_country" id="employee_country" value="<?php echo $employee_country; ?>" placeholder="Država">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_info" class="col-sm-3 control-label">Ostale informacije:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="employee_info" id="employee_info" value="<?php echo $employee_info; ?>" placeholder="Ostale informacije">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									
									<div class="form-group">
										<label for="employe_vicidial_user" class="col-sm-3 control-label">Vicidial podaci - Korisničko ime:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="employe_vicidial_user" id="employe_vicidial_user" value="<?php echo $employe_viciUsername; ?>" placeholder="Vicidial pristupni username">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="employe_vicidial_pass" class="col-sm-3 control-label">Vicidial podaci - Lozinka:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="employe_vicidial_pass" id="employe_vicidial_pass" value="<?php echo $employe_viciPassword; ?>" placeholder="Vicidial pristupni password">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_image" class="col-sm-3 control-label">Fotografija:</label>
										<div class="col-sm-9">
											<div class="fileinput fileinput-new" data-provides="fileinput">
												<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;">
													<img src="<?php getSiteURL(); ?>files/employees/<?php echo $employee_image; ?>">
												</div>
												<input type="hidden" name="employee_image_url" value="<?php echo $employee_image; ?>" />
												<div>
													<span class="btn btn-default btn-file"><span class="fileinput-new">Izaberi fotografiju</span><span class="fileinput-exists">Promijeni</span><input type="file" name="employee_image" id="employee_image"></span>
													<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Ukloni</a>
													<script>
														$(function (){
															$('#employee_image').change(function (){

																var ext = $('#employee_image').val().split('.').pop().toLowerCase();

																if($.inArray(ext, ['jpg', 'jpeg', 'png']) == -1) {
																	$('#idk_alert_ext').removeClass('hidden');
																	this.value = null;
																}else{
																	$('#idk_alert_ext').addClass('hidden');
																}

																var f = this.files[0];

																if (f.size > 20388608 || f.fileSize > 20388608){
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

									<div class="form-group">
										<label class="col-sm-3"></label>
										<div class="col-sm-9">
											<div id="idk_alert_size" class="hidden">
												<div class="alert material-alert material-alert_danger">Greška: Fotografiju koju pokuštavate dodati je veća od dozvoljene veličine.</div>
											</div>
											<div id="idk_alert_ext" class="hidden">
												<div class="alert material-alert material-alert_danger">Greška: Format fotografije koju pokušavate dodati nije dozvoljen.</div>
											</div>
										</div>
									</div>
									<br />
									<div class="form-group">
										<div class="col-sm-offset-2 col-sm-10 text-right">
											<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-save" aria-hidden="true"></i> <span>Snimi</span></button>
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

				break;

				case "open":

					$employee_id = $_GET['id'];

					$query = $db->prepare("
									SELECT employee_firstname, employee_lastname, employee_jmbg, employee_email, employee_position, employee_dob, employee_doe, employee_address, employee_city, employee_country, employee_info, employee_status, employee_image, employee_inote, employee_odjel, employee_poslovnica, employee_team
									FROM idk_employees
									WHERE employee_id = :employee_id");

					$query->execute(array(
								':employee_id' => $employee_id));

					$row = $query->fetch();

						$employee_firstname = $row['employee_firstname'];
						$employee_lastname = $row['employee_lastname'];
						$employee_jmbg = $row['employee_jmbg'];
						$employee_position = $row['employee_position'];
						$employee_dob = date('d.m.Y.', strtotime($row['employee_dob']));
						$employee_doe = date('d.m.Y.', strtotime($row['employee_doe']));
						$employee_email = $row['employee_email'];
						$employee_address = $row['employee_address'];
						$employee_city = $row['employee_city'];
						$employee_country = $row['employee_country'];
						$employee_info = $row['employee_info'];
						$employee_inote = $row['employee_inote'];
						$employee_team = getTeamName($row['employee_team']);
						$employee_status = explode( ',' , $row['employee_status']); // LIVE

						if($row['employee_image'] == "none"){
							$employee_image = "none.jpg";
						}else{
							$employee_image = $row['employee_image'];
						}


						$employee_status_all = "";
						if(in_array( "1" , $employee_status)){
							$employee_status_all .= "  Administrator <br />";
						}

						if(in_array( "2" , $employee_status)){
							$employee_status_all .= "  Projekt Menadžer <br />";
						}

						if(in_array( "3" , $employee_status)){
							$employee_status_all .= "  Projekt Asistent <br />";
						}
						if(in_array( "4" , $employee_status)){
							$employee_status_all .= "   Obrada <br />";
						}
						if(in_array( "5" , $employee_status)){
							$employee_status_all .= "  Front Office <br />";
						}
						if(in_array( "6" , $employee_status)){
							$employee_status_all .= "  Tehnika<br />";
						}
						if(in_array( "7" , $employee_status)){
							$employee_status_all .= "   Marketing <br />";
						}
						if(in_array( "8" , $employee_status)){
							$employee_status_all .= "   Vanjski Saradnik <br />";
						}
						if(in_array( "9" , $employee_status)){
							$employee_status_all .= "   Financije <br />";
						}
						if(in_array( "10" , $employee_status)){
							$employee_status_all .= "  First Call Agent <br />";
						}
						if(in_array( "11" , $employee_status)){
							$employee_status_all .= "  Saradnik - DAK <br />";
						}
						if(in_array( "12" , $employee_status)){
							$employee_status_all .= "  Saradnik - Prevodioc <br />";
						}
						if(in_array( "13" , $employee_status)){
							$employee_status_all .= "  Prevod <br />";
						}
						if(in_array( "14" , $employee_status)){
							$employee_status_all .= "  Inkaso Agent <br />";
						}
						if(in_array( "15" , $employee_status)){
							$employee_status_all .= "  Dipl Agent <br />";
						}
						if(in_array( "16" , $employee_status)){
							$employee_status_all .= "  Obrada Dipl <br />";
						}
						if(in_array( "17" , $employee_status)){
							$employee_status_all .= "  Lilium marketing <br />";
						}
						if(in_array( "18" , $employee_status)){
							$employee_status_all .= "  TF Agent <br />";
						}
						if(in_array( "19" , $employee_status)){
							$employee_status_all .= "  Dysordian Dev <br />";
						}
						if(in_array( "20" , $employee_status)){
							$employee_status_all .= "  Ama-Int Saradnik <br />";
						}

						if($row['employee_odjel'] == 1){
							$employee_odjel = "Uprava";
						}elseif($row['employee_odjel'] == 2){
							$employee_odjel = "Financije";
						}elseif($row['employee_odjel'] == 3){
							$employee_odjel = "Prodaja";
						}elseif($row['employee_odjel'] == 4){
							$employee_odjel = "Obrada";
						}elseif($row['employee_odjel'] == 5){
							$employee_odjel = "Sve za vizu";
						}elseif($row['employee_odjel'] == 6){
							$employee_odjel = "Marketing";
						}elseif($row['employee_odjel'] == 7){
							$employee_odjel = "Tehnika";
						}elseif($row['employee_odjel'] == 8){
							$employee_odjel = "Development";
						}else{
							$employee_odjel = "NDF";
						}
						
						//Ispis informacija o poslovnici zaposlenika
						$employee_poslovnica = $row['employee_poslovnica'];
						if($employee_poslovnica != NULL){
							$employee_p_naziv = getBranchNameR($employee_poslovnica);
						}
						else{
							$employee_p_naziv = '<span class="material-label material-label_warning main-container__column text-left">Nije odabrana poslovnica!</span>';
						}
		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><a class="fancybox" rel="group" href="<?php getSiteURL(); ?>files/employees/<?php echo $employee_image; ?>"><img class="idk_profile_img" src="<?php getSiteURL(); ?>files/employees/<?php echo $employee_image; ?>"></a> <?php echo $employee_firstname; ?> <?php echo $employee_lastname; ?></h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>employees?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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

									if($mess == 1){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste dodali novi bilješku.</div>
										<script>$(function() { $('[href="#notes"]').tab('show'); });</script>
									<?php
									}elseif($mess == 2){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste dodali novi dokument.</div>
										<script>$(function() { $('[href="#documents"]').tab('show'); });</script>
									<?php
									}elseif($mess == 3){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste obrisali dokument.</div>
										<script>$(function() { $('[href="#documents"]').tab('show'); });</script>
									<?php
									}elseif($mess == 4){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste obrisali bilješku.</div>
										<script>$(function() { $('[href="#notes"]').tab('show'); });</script>
									<?php
									}elseif($mess == 5){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste snimili važne napomene.</div>
										<script>$(function() { $('[href="#important"]').tab('show'); });</script>
									<?php
									}elseif($mess == 8){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste dodali novi zdatak.</div>
										<script>$(function() { $('[href="#tasks"]').tab('show'); });</script>
									<?php
									}elseif($mess == 9){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste završili zadatak.</div>
										<script>$(function() { $('[href="#tasks"]').tab('show'); });</script>
									<?php
									}elseif($mess == 10){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste odgodili zadatak.</div>
										<script>$(function() { $('[href="#tasks"]').tab('show'); });</script>
									<?php
									}elseif($mess == 11){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste obrisali zadatak.</div>
										<script>$(function() { $('[href="#tasks"]').tab('show'); });</script>
									<?php
									}elseif($mess == 12){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste definisali e-mail napomenu.</div>
										<script>$(function() { $('[href="#tasks"]').tab('show'); });</script>
									<?php
									}elseif($mess == 13){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste definisali ponavljanje napomene.</div>
										<script>$(function() { $('[href="#tasks"]').tab('show'); });</script>
									<?php
									}elseif($mess == 14){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste dodali novi telefonski broj.</div>
										<script>$(function() { $('[href="#info"]').tab('show'); });</script>
									<?php
									}elseif($mess == 15){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste postavili novi primarni telefonski broj.</div>
										<script>$(function() { $('[href="#info"]').tab('show'); });</script>
									<?php
									}elseif($mess == 16){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste obrisali kontakt informaciju.</div>
										<script>$(function() { $('[href="#info"]').tab('show'); });</script>
									<?php
									}elseif($mess == 17){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste dodali novu email adresu.</div>
										<script>$(function() { $('[href="#info"]').tab('show'); });</script>
									<?php
									}elseif($mess == 18){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste postavili novu primarnu email adresu.</div>
										<script>$(function() { $('[href="#info"]').tab('show'); });</script>
									<?php
									}elseif($mess == 19){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste dodali novu kontakt informaciju.</div>
										<script>$(function() { $('[href="#info"]').tab('show'); });</script>
									<?php
									}elseif($mess == 20){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste postavili novu primarnu kontakt informaciju.</div>
										<script>$(function() { $('[href="#info"]').tab('show'); });</script>
									<?php
									}elseif($mess == 21){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste uredili kontakt profil.</div>
										<script>$(function() { $('[href="#info"]').tab('show'); });</script>
									<?php
									}
								?>
							</div>
						</div>
						<div id="myTabs" class="panel-group material-tabs-group">
							<ul class="nav nav-tabs material-tabs material-tabs_primary">
								<li class="active"><a href="#info" class="material-tabs__tab-link" data-toggle="tab">Informacije</a></li>
								<li><a href="#tasks" class="material-tabs__tab-link" data-toggle="tab">Zadaci</a></li>
                                <?php if ((in_array( "1" , $getEmployeeStatus)) OR $logged_employee_id == 32 OR $logged_employee_id == 33 OR $logged_employee_id == 11){ ?> <li><a href="#notes" class="material-tabs__tab-link" data-toggle="tab">Bilješke</a></li> <?php } ?>
								<?php if($employee_id == $logged_employee_id) {?><li><a href="#documents" class="material-tabs__tab-link" data-toggle="tab">Dokumenti</a></li><?php } ?>
								<li><a href="#skladiste" class="material-tabs__tab-link" data-toggle="tab">Zaduženo</a></li>
								<li><a href="#important" class="material-tabs__tab-link" data-toggle="tab">Važno</a></li>
							</ul>
							<div class="tab-content materail-tabs-content">
								<div class="tab-pane fade active in" id="info">
									<div class="row idk_employee_info">
										<div class="col-md-6">
											<div class="row">
												<div class="col-sm-9">
													<h5>Osnovne informacije</h5>
												</div>
												<div class="col-sm-3 text-right">
													<a href="employees?page=edit&id=<?php echo $employee_id; ?>" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-pencil" aria-hidden="true"></i> <span></span></a>
												</div>
											</div>

											<div class="row">
												<strong class="col-sm-4 text-right">Ime:</strong>
												<div class="col-sm-8"><?php echo $employee_firstname; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Prezime:</strong>
												<div class="col-sm-8"><?php echo $employee_lastname; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Pozicija:</strong>
												<div class="col-sm-8"><?php echo $employee_position; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Login Email:</strong>
												<div class="col-sm-8"><?php echo $employee_email; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">JMBG:</strong>
												<div class="col-sm-8"><?php echo $employee_jmbg; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Datum rođenja:</strong>
												<div class="col-sm-8"><?php if(!is_null($row['employee_dob'])){ echo $employee_dob; } ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Datum zaposlenja:</strong>
												<div class="col-sm-8"><?php if(!is_null($row['employee_doe'])){ echo $employee_doe; } ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Adresa:</strong>
												<div class="col-sm-8"><?php echo $employee_address; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Grad:</strong>
												<div class="col-sm-8"><?php echo $employee_city; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Država:</strong>
												<div class="col-sm-8"><?php echo $employee_country; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Status:</strong>
												<div class="col-sm-8"><?php echo $employee_status_all; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Odjel:</strong>
												<div class="col-sm-8"><?php echo $employee_odjel; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Poslovnica:</strong>
												<div class="col-sm-8"><?php echo $employee_p_naziv; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Tim:</strong>
												<div class="col-sm-8"><?php echo $employee_team; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Ostale informacije:</strong>
												<div class="col-sm-8"><?php echo $employee_info; ?></div>
											</div>
										</div>
										<div class="col-md-6">
											<div class="row">
												<div class="col-sm-9">
													<h5>Telefon</h5>
												</div>
												<div class="col-sm-3 text-right">
													<a href="#" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" data-toggle="modal" data-target="#phoneModal"><i class="fa fa-plus" aria-hidden="true"></i> <span></span></a>
													<!-- Modal add note -->
													<div class="modal material-modal material-modal_primary fade text-left" id="phoneModal">
														<div class="modal-dialog ">
															<div class="modal-content material-modal__content">
																<div class="modal-header material-modal__header">
																	<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																	<h4 class="modal-title material-modal__title">Dodaj telefonski broj</h4>
																</div>
																<div class="modal-body material-modal__body">
																	<form action="<?php getSiteURL(); ?>do.php?form=add_employee_phone" method="post" role="form" class="form-horizontal">
																		<input type="hidden" name="ei_employeeid" value="<?php echo $employee_id; ?>" />
																		<div class="form-group">
																			<label for="ei_title" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv:</label>
																			<div class="col-sm-9">
																				<div class="materail-input-block materail-input-block_success">
																					<input class="form-control materail-input" type="text" name="ei_title" id="ei_title" placeholder="Mobilni" required>
																					<span class="materail-input-block__line"></span>
																				</div>
																			</div>
																		</div>
																		<div class="form-group">
																			<label for="ei_data" class="col-sm-3 control-label"><span class="text-danger">*</span> Broj telefona:</label>
																			<div class="col-sm-9">
																				<div class="materail-input-block materail-input-block_success">
																					<input class="form-control materail-input" type="text" name="ei_data" id="ei_data" placeholder="003876XXXXXXXX" required>
																					<span class="materail-input-block__line"></span>
																				</div>
																			</div>
																		</div>
																</div>
																<div class="modal-footer material-modal__footer">
																	<ul class="list-inline">
																		<li class="hidden"><i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i></li>
																		<li><button type="submit" class="btn btn-primary material-btn material-btn_primary">Dodaj</button></li>
																	</ul>
																	</form>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="table-responsive">
												<table class="table table-striped">
													<tbody>
														<?php
															$query_phones = $db->prepare("
																				SELECT ei_id, ei_title, ei_data, ei_primary
																				FROM idk_employees_info
																				WHERE ei_group = :ei_group AND ei_employeeid = :ei_employeeid
																				ORDER BY ei_primary DESC");

															$query_phones->execute(array(
																	':ei_group' => 1,
																	':ei_employeeid' => $employee_id));

															while($row_phones = $query_phones->fetch()){

																$ei_id = $row_phones['ei_id'];
																$ei_title = $row_phones['ei_title'];
																$ei_data = $row_phones['ei_data'];
																$ei_primary = $row_phones['ei_primary'];
														?>
														<tr>
															<td><?php echo $ei_title; ?>:</td>
															<td><a href="tel:<?php echo $ei_data; ?>"><?php echo $ei_data; ?></a></td>
															<td class="text-right">
																<ul class="list-inline">
																	<?php if($ei_primary == 1){ echo '<li data-toggle="tooltip" data-placement="top" title="Primarni"><i class="fa fa-star fa-lg text-success" aria-hidden="true"></i></li>'; }else{ echo '<li data-toggle="tooltip" data-placement="top" title="Postavi kao primarni"><a href="' . getSiteURLr() . 'do.php?form=set_primary_phone_employee&ei_id=' . $ei_id . '&employee_id=' . $employee_id  . '"><i class="fa fa-star-o fa-lg text-success" aria-hidden="true"></i></a></li>'; } ?>
																	<li data-toggle="tooltip" data-placement="top" title="Obriši"><a href="#" data="<?php getSiteURL(); ?>employees?page=del_employee_info&id=<?php echo $ei_id; ?>" data-toggle="modal" data-target="#delPhoneModal" class="delPhone"><i class="fa fa-trash-o fa-lg text-danger" aria-hidden="true"></i></a></li>
																</ul>
															</td>
															<script>
																$(".delPhone").click(function () {
																	var addressValue = $(this).attr("data");
																	document.getElementById("delPhone_link").href = addressValue;
																});
															</script>
															<!-- DelPhone Modal -->
															<div class="modal material-modal material-modal_danger fade" id="delPhoneModal">
																<div class="modal-dialog">
																	<div class="modal-content material-modal__content">
																		<div class="modal-header material-modal__header">
																			<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																			<h4 class="modal-title material-modal__title">Brisanje</h4>
																		</div>
																		<div class="modal-body material-modal__body">
																			<p>Jeste li sigurni da želite obrisati broj telefona?</p>
																		</div>
																		<div class="modal-footer material-modal__footer">
																			<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
																			<a id="delPhone_link" href=""><button class="btn btn-primary material-btn material-btn_danger">OBRIŠI</button></a>
																		</div>
																	</div>
																</div>
															</div>
														</tr>
														<?php } ?>
													</tbody>
												</table>
											</div>
											<div class="row">
												<div class="col-sm-9">
													<h5>E-mail</h5>
												</div>
												<div class="col-sm-3 text-right">
													<a href="#" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" data-toggle="modal" data-target="#emailModal"><i class="fa fa-plus" aria-hidden="true"></i> <span></span></a>
													<!-- Modal add note -->
													<div class="modal material-modal material-modal_primary fade text-left" id="emailModal">
														<div class="modal-dialog ">
															<div class="modal-content material-modal__content">
																<div class="modal-header material-modal__header">
																	<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																	<h4 class="modal-title material-modal__title">Dodaj email adresu</h4>
																</div>
																<div class="modal-body material-modal__body">
																	<form action="<?php getSiteURL(); ?>do.php?form=add_employee_email" method="post" role="form" class="form-horizontal">
																		<input type="hidden" name="ei_employeeid" value="<?php echo $employee_id; ?>" />
																		<div class="form-group">
																			<label for="ei_title" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv:</label>
																			<div class="col-sm-9">
																				<div class="materail-input-block materail-input-block_success">
																					<input class="form-control materail-input" type="text" name="ei_title" id="ei_title" placeholder="Privatni" required>
																					<span class="materail-input-block__line"></span>
																				</div>
																			</div>
																		</div>
																		<div class="form-group">
																			<label for="ei_data" class="col-sm-3 control-label"><span class="text-danger">*</span> Email adresa:</label>
																			<div class="col-sm-9">
																				<div class="materail-input-block materail-input-block_success">
																					<input class="form-control materail-input" type="email" name="ei_data" id="ei_data" placeholder="info@primjer.com" required>
																					<span class="materail-input-block__line"></span>
																				</div>
																			</div>
																		</div>
																</div>
																<div class="modal-footer material-modal__footer">
																	<ul class="list-inline">
																		<li class="hidden"><i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i></li>
																		<li><button type="submit" class="btn btn-primary material-btn material-btn_primary">Dodaj</button></li>
																	</ul>
																	</form>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="table-responsive">
												<table class="table table-striped">
													<tbody>
														<?php
															$query_phones = $db->prepare("
																				SELECT ei_id, ei_title, ei_data, ei_primary
																				FROM idk_employees_info
																				WHERE ei_group = :ei_group AND ei_employeeid = :ei_employeeid
																				ORDER BY ei_primary DESC");

															$query_phones->execute(array(
																	':ei_group' => 2,
																	':ei_employeeid' => $employee_id));

															while($row_phones = $query_phones->fetch()){

																$ei_id = $row_phones['ei_id'];
																$ei_title = $row_phones['ei_title'];
																$ei_data = $row_phones['ei_data'];
																$ei_primary = $row_phones['ei_primary'];
														?>
														<tr>
															<td><?php echo $ei_title; ?>:</td>
															<td><a href="mailto:<?php echo $ei_data; ?>"><?php echo $ei_data; ?></a></td>
															<td class="text-right">
																<ul class="list-inline">
																	<?php if($ei_primary == 1){ echo '<li data-toggle="tooltip" data-placement="top" title="Primarni"><i class="fa fa-star fa-lg text-success" aria-hidden="true"></i></li>'; }else{ echo '<li data-toggle="tooltip" data-placement="top" title="Postavi kao primarni"><a href="' . getSiteURLr() . 'do.php?form=set_primary_email_employee&ei_id=' . $ei_id . '&employee_id=' . $employee_id  . '"><i class="fa fa-star-o fa-lg text-success" aria-hidden="true"></i></a></li>'; } ?>
																	<li data-toggle="tooltip" data-placement="top" title="Obriši"><a href="#" data="<?php getSiteURL(); ?>employees?page=del_employee_info&id=<?php echo $ei_id; ?>" data-toggle="modal" data-target="#delEmailModal" class="delEmail"><i class="fa fa-trash-o fa-lg text-danger" aria-hidden="true"></i></a></li>
																</ul>
															</td>
															<script>
																$(".delEmail").click(function () {
																	var addressValue = $(this).attr("data");
																	document.getElementById("delEmail_link").href = addressValue;
																});
															</script>
															<!-- delEmail Modal -->
															<div class="modal material-modal material-modal_danger fade" id="delEmailModal">
																<div class="modal-dialog">
																	<div class="modal-content material-modal__content">
																		<div class="modal-header material-modal__header">
																			<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																			<h4 class="modal-title material-modal__title">Brisanje</h4>
																		</div>
																		<div class="modal-body material-modal__body">
																			<p>Jeste li sigurni da želite obrisati email adresu?</p>
																		</div>
																		<div class="modal-footer material-modal__footer">
																			<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
																			<a id="delEmail_link" href=""><button class="btn btn-primary material-btn material-btn_danger">OBRIŠI</button></a>
																		</div>
																	</div>
																</div>
															</div>
														</tr>
														<?php } ?>
													</tbody>
												</table>
											</div>
											<div class="row">
												<div class="col-sm-9">
													<h5>Ostale kontakt informacije</h5>
												</div>
												<div class="col-sm-3 text-right">
													<a href="#" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" data-toggle="modal" data-target="#otherModal"><i class="fa fa-plus" aria-hidden="true"></i> <span></span></a>
													<!-- Modal add note -->
													<div class="modal material-modal material-modal_primary fade text-left" id="otherModal">
														<div class="modal-dialog ">
															<div class="modal-content material-modal__content">
																<div class="modal-header material-modal__header">
																	<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																	<h4 class="modal-title material-modal__title">Dodaj kontakt informaciju</h4>
																</div>
																<div class="modal-body material-modal__body">
																	<form action="<?php getSiteURL(); ?>do.php?form=add_employee_other" method="post" role="form" class="form-horizontal">
																		<input type="hidden" name="ei_employeeid" value="<?php echo $employee_id; ?>" />
																		<div class="form-group">
																			<label for="ei_title" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv:</label>
																			<div class="col-sm-9">
																				<div class="materail-input-block materail-input-block_success">
																					<input class="form-control materail-input" type="text" name="ei_title" id="ei_title" placeholder="Facebook" required>
																					<span class="materail-input-block__line"></span>
																				</div>
																			</div>
																		</div>
																		<div class="form-group">
																			<label for="ei_data" class="col-sm-3 control-label"><span class="text-danger">*</span> Adresa:</label>
																			<div class="col-sm-9">
																				<div class="materail-input-block materail-input-block_success">
																					<input class="form-control materail-input" type="text" name="ei_data" id="ei_data" placeholder="www.facebook.com/profil" required>
																					<span class="materail-input-block__line"></span>
																				</div>
																			</div>
																		</div>
																</div>
																<div class="modal-footer material-modal__footer">
																	<ul class="list-inline">
																		<li class="hidden"><i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i></li>
																		<li><button type="submit" class="btn btn-primary material-btn material-btn_primary">Dodaj</button></li>
																	</ul>
																	</form>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="table-responsive">
												<table class="table table-striped">
													<tbody>
														<?php
															$query_phones = $db->prepare("
																				SELECT ei_id, ei_title, ei_data, ei_primary
																				FROM idk_employees_info
																				WHERE ei_group = :ei_group AND ei_employeeid = :ei_employeeid
																				ORDER BY ei_primary DESC");

															$query_phones->execute(array(
																	':ei_group' => 3,
																	':ei_employeeid' => $employee_id));

															while($row_phones = $query_phones->fetch()){

																$ei_id = $row_phones['ei_id'];
																$ei_title = $row_phones['ei_title'];
																$ei_data = $row_phones['ei_data'];
																$ei_primary = $row_phones['ei_primary'];
														?>
														<tr>
															<td><?php echo $ei_title; ?>:</td>
															<td><?php echo $ei_data; ?></td>
															<td class="text-right">
																<ul class="list-inline">
																	<?php if($ei_primary == 1){ echo '<li data-toggle="tooltip" data-placement="top" title="Primarni"><i class="fa fa-star fa-lg text-success" aria-hidden="true"></i></li>'; }else{ echo '<li data-toggle="tooltip" data-placement="top" title="Postavi kao primarni"><a href="' . getSiteURLr() . 'do.php?form=set_primary_other_employee&ei_id=' . $ei_id . '&employee_id=' . $employee_id  . '"><i class="fa fa-star-o fa-lg text-success" aria-hidden="true"></i></a></li>'; } ?>
																	<li data-toggle="tooltip" data-placement="top" title="Obriši"><a href="#" data="<?php getSiteURL(); ?>employees?page=del_employee_info&id=<?php echo $ei_id; ?>" data-toggle="modal" data-target="#delOtherModal" class="delOther"><i class="fa fa-trash-o fa-lg text-danger" aria-hidden="true"></i></a></li>
																</ul>
															</td>
															<script>
																$(".delOther").click(function () {
																	var addressValue = $(this).attr("data");
																	document.getElementById("delOther_link").href = addressValue;
																});
															</script>
															<!-- DelPhone Modal -->
															<div class="modal material-modal material-modal_danger fade" id="delOtherModal">
																<div class="modal-dialog">
																	<div class="modal-content material-modal__content">
																		<div class="modal-header material-modal__header">
																			<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																			<h4 class="modal-title material-modal__title">Brisanje</h4>
																		</div>
																		<div class="modal-body material-modal__body">
																			<p>Jeste li sigurni da želite obrisati kontakt informaciju?</p>
																		</div>
																		<div class="modal-footer material-modal__footer">
																			<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
																			<a id="delOther_link" href=""><button class="btn btn-primary material-btn material-btn_danger">OBRIŠI</button></a>
																		</div>
																	</div>
																</div>
															</div>
														</tr>
														<?php } ?>
													</tbody>
												</table>
											</div>
										</div>
									</div>
								</div>
								<div class="tab-pane fade" id="tasks">
									<form id="idk_form" action="<?php getSiteURL(); ?>do.php?form=add_task_employee" method="post" class="form-horizontal" role="form">
										<input type="hidden" name="task_dataid" value="<?php echo $employee_id; ?>">
										<input type="hidden" name="task_assignedid" value="<?php echo $employee_id; ?>">
										<div class="row">
											<div class="col-md-3 idk_margin_top20">
												<div class="col-xs-12">
													<div class="form-group">
													    <label for="task_duedate"><strong>Datum i vrijeme dospijeća:</strong></label>
														<div class="row">
															<div class="col-sm-6">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input" data-init-set="false" data-large-default="true" data-format="d.m.Y." data-max-year="2050" data-large-mode="true" data-modal="true" type="text" name="task_duedate" id="task_duedate" value="<?php echo date('d.m.Y.', time()); ?>">
																	<span class="materail-input-block__line"></span>
																</div>
																<script>
																	$( function() {	$( "#task_duedate" ).dateDropper({format: 'd.m.Y', theme: 'jobstep_datedropper'});});
																</script>
															</div>
															<div class="col-sm-6">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input" type="text" name="task_duetime" id="task_duetime">
																	<span class="materail-input-block__line"></span>
																</div>
																<script>
																	$('#task_duetime').timeDropper({ format:'H:mm', setCurrentTime:false });
																</script>
															</div>
														</div>
													 </div>
												</div>
												<div class="col-xs-12">
													<div class="form-group">
													    <label for="task_emailnotifi"><strong>E-mail napomena:</strong></label>
														<select class="selectpicker" id="task_emailnotifi" name="task_emailnotifi" required>
															<option value="0">Isključeno</option>
															<option value="1">Na dan dospijeća</option>
															<option value="2">Dan prije dospijeća</option>
															<option value="3">Dva dana prije dospijeća</option>
															<option value="4">Sedmicu dana prije dospijeća</option>
															<option value="5">Mjesec dana prije dospijeća</option>
														</select>
													 </div>
												</div>
												<div class="col-xs-12">
													<div class="form-group">
													    <label for="task_repeatnotifi"><strong>Ponavljaj zadatak:</strong></label>
														<select class="selectpicker" id="task_repeatnotifi" name="task_repeatnotifi" required>
															<option value="0">Isključeno</option>
															<option value="1">Dnevno</option>
															<option value="2">Sedmično</option>
															<option value="3">Mjesečno</option>
															<option value="4">Godišnje</option>
														</select>
													 </div>
												</div>
											</div>
											<div class="col-md-9 idk_custom_textare">
												<div class="col-xs-12">
													<textarea id="task_txt" class="form-control materail-input material-textarea" name="task_txt" placeholder="Opiši zadatak ..." rows="8" required></textarea>
												</div>
												<script>
													$('#task_txt').trumbowyg({
														lang: 'hr',
													    btns: [
													        ['strong', 'em', 'del'],
													        ['link'],
													        ['unorderedList', 'orderedList']
													    ]
													});
												</script>
											</div>
										</div>
										<div class="row">
											<div class="col-md-12 text-right">
												<ul class="list-inline">
													<li class="hidden"><i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i></li>
													<li><button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj</span></button></li>
												</ul>

											</div>
										</div>
									</form>
									<hr>
									<div class="row">
										<div class="col-md-12">
											<ul class="cbp_tmtimeline">
												<?php
													$query_tasks = $db->prepare("
																			SELECT task_id, task_txt, task_replytxt, task_duedatetime, task_assignedid, task_emailnotifi, task_repeatnotifi, task_employeeid, task_datetime, task_status, assigned.employee_firstname AS assigned_firstname, assigned.employee_lastname AS assigned_lastname, employee.employee_firstname AS added_firstname, employee.employee_lastname AS added_lastname
																			FROM idk_tasks
																			INNER JOIN idk_employees assigned ON idk_tasks.task_assignedid = assigned.employee_id
																			INNER JOIN idk_employees employee ON idk_tasks.task_employeeid = employee.employee_id
																			WHERE task_dataid = :task_dataid AND task_group = :task_group
																			ORDER BY task_status ASC, task_duedatetime ASC, task_id DESC");

													$query_tasks->execute(array(
																	':task_dataid' => $employee_id,
																	':task_group' => 2));

													while($row_tasks = $query_tasks->fetch()){

														$task_id = $row_tasks['task_id'];
														$task_txt = $row_tasks['task_txt'];
														$task_replytxt = $row_tasks['task_replytxt'];
														$task_emailnotifi = $row_tasks['task_emailnotifi'];
														$task_repeatnotifi = $row_tasks['task_repeatnotifi'];
														$assigned_firstname = $row_tasks['assigned_firstname'];
														$assigned_lastname = $row_tasks['assigned_lastname'];
														$added_firstname = $row_tasks['added_firstname'];
														$added_lastname = $row_tasks['added_lastname'];
														$task_duedatetime_format = date('d.m.Y. - H:i', strtotime($row_tasks['task_duedatetime']));
														$task_duedatetime = date('Y-m-d H:i:s', strtotime($row_tasks['task_duedatetime']));

														if($row_tasks['task_status'] == 1 AND (new DateTime() >= new DateTime($task_duedatetime))){
															$task_status = 'style="background-color: #f2a12e; color: #fff;"';
															$task_icon = '<i class="fa fa-clock-o" aria-hidden="true"></i>';
														}elseif($row_tasks['task_status'] == 1){
															$task_status = 'style="background-color: #ddd; color: #fff;"';
															$task_icon = '<i class="fa fa-check" aria-hidden="true"></i>';
														}elseif($row_tasks['task_status'] == 2){
															$task_status = 'style="color: #fff;"';
															$task_icon = '<i class="fa fa-check" aria-hidden="true"></i>';
														}elseif($row_tasks['task_status'] == 3){
															$task_status = 'style="background-color: #f3413c; color: #fff;"';
															$task_icon = '<i class="fa fa-times" aria-hidden="true"></i>';
														}
												?>
												<li>
													<time class="cbp_tmtime"><span data-toggle="tooltip" data-placement="top" title="Zadatak dodao: <?php echo $added_firstname; ?> <?php echo $added_lastname; ?>"><?php echo $assigned_firstname; ?> <?php echo $assigned_lastname; ?></span> <span class="timeago" datetime="<?php echo $task_duedatetime; ?>" data-toggle="tooltip" data-placement="bottom" title="<?php echo $task_duedatetime_format; ?>"></span></time>
													<div class="cbp_tmicon" <?php echo $task_status; ?> data-toggle="tooltip" data-placement="right" title="<?php echo $task_replytxt; ?>"><?php echo $task_icon; ?></div>
													<div class="cbp_tmlabel">
														<div class="row">
															<div class="col-lg-9 col-md-8 col-sm-7">
																<?php echo $task_txt; ?>
															</div>
															<div class="col-lg-3 col-md-4 col-sm-5 text-right">
																<ul class="list-inline">
																	<?php if($row_tasks['task_status'] == 1){ ?>
																	<li>
																		<a href="#" data="<?php echo $task_id; ?>" data-toggle="modal" data-target="#taskDoneModal" class="task_done btn material-btn material-btn_success main-container__column"><i class="fa fa-check" aria-hidden="true"></i></a>
																		<script>
																			$(".task_done").click(function () {
																				var addressValue = $(this).attr("data");
																				document.getElementById("task_id_done").value = addressValue;
																			});
																		</script>
																		<div class="modal material-modal material-modal_primary fade text-left" id="taskDoneModal">
																			<div class="modal-dialog ">
																				<div class="modal-content material-modal__content">
																					<div class="modal-header material-modal__header">
																						<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																						<h4 class="modal-title material-modal__title">Završi zadatak</h4>
																					</div>
																					<div class="modal-body material-modal__body">
																						<form action="<?php getSiteURL(); ?>do.php?form=add_task_done_employee" method="post" role="form" class="form-horizontal">
																							<input type="hidden" name="task_id" id="task_id_done" value="" />
																							<div class="form-group">
																								<div class="col-md-offset-2 col-sm-8">
																									<div class="form-group materail-input-block materail-input-block_success">
																										<textarea class="form-control materail-input material-textarea" name="task_replytxt" placeholder="Odgovor na zadatak" rows="6" required></textarea>
																										<span class="materail-input-block__line"></span>
																									</div>
																								</div>
																							</div>
																					</div>
																					<div class="modal-footer material-modal__footer">
																						<ul class="list-inline">
																							<li class="hidden"><i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i></li>
																							<li><button type="submit" class="btn btn-primary material-btn material-btn_primary">Završi</button></li>
																						</ul>
																						</form>
																					</div>
																				</div>
																			</div>
																		</div>
																	</li>
																	<li>
																		<a href="#" data="<?php echo $task_id; ?>" data-toggle="modal" data-target="#taskNotDoneModal" class="task_not_done btn material-btn material-btn_danger main-container__column"><i class="fa fa-times" aria-hidden="true"></i></a>
																		<script>
																			$(".task_not_done").click(function () {
																				var addressValue = $(this).attr("data");
																				document.getElementById("task_id_notdone").value = addressValue;
																			});
																		</script>
																		<div class="modal material-modal material-modal_primary fade text-left" id="taskNotDoneModal">
																			<div class="modal-dialog ">
																				<div class="modal-content material-modal__content">
																					<div class="modal-header material-modal__header">
																						<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																						<h4 class="modal-title material-modal__title">Poništi zadatak</h4>
																					</div>
																					<div class="modal-body material-modal__body">
																						<form action="<?php getSiteURL(); ?>do.php?form=add_task_notdone_employee" method="post" role="form" class="form-horizontal">
																							<input type="hidden" name="task_id" id="task_id_notdone" value="" />
																							<div class="form-group">
																								<div class="col-md-offset-2 col-sm-8">
																									<div class="form-group materail-input-block materail-input-block_success">
																										<textarea class="form-control materail-input material-textarea" name="task_replytxt" placeholder="Odgovor na zadatak" rows="6" required></textarea>
																										<span class="materail-input-block__line"></span>
																									</div>
																								</div>
																							</div>
																					</div>
																					<div class="modal-footer material-modal__footer">
																						<ul class="list-inline">
																							<li class="hidden"><i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i></li>
																							<li><button type="submit" class="btn btn-primary material-btn material-btn_primary">Poništi</button></li>
																						</ul>
																						</form>
																					</div>
																				</div>
																			</div>
																		</div>
																	</li>
																	<?php } ?>
																	<li>
																		<a href="#" data="<?php getSiteURL(); ?>employees?page=del_task&id=<?php echo $task_id; ?>" data-toggle="modal" data-target="#deleteTaskModal" class="delete_task btn material-btn material-btn_danger main-container__column"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
																		<script>
																			$(".delete_task").click(function () {
																				var addressValue = $(this).attr("data");
																				document.getElementById("delete_task_link").href = addressValue;
																			});
																		</script>
																		<!-- Modal -->
																		<div class="modal material-modal material-modal_danger fade text-left" id="deleteTaskModal">
																			<div class="modal-dialog">
																				<div class="modal-content material-modal__content">
																					<div class="modal-header material-modal__header">
																						<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																						<h4 class="modal-title material-modal__title">Brisanje</h4>
																					</div>
																					<div class="modal-body material-modal__body">
																						<p>Jeste li sigurni da želite obrisati zadatak?</p>
																					</div>
																					<div class="modal-footer material-modal__footer">
																						<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
																						<a id="delete_task_link" href=""><button class="btn btn-primary material-btn material-btn_danger">OBRIŠI</button></a>
																					</div>
																				</div>
																			</div>
																		</div>
																	</li>
																</ul>
																<hr>
																<ul class="list-inline">
																	<?php if($row_tasks['task_status'] == 1){ ?>
																	<li>
																		<?php
																			if($task_emailnotifi == 0){
																				echo '<span data-toggle="modal" data-target="#taskEmailModal"><a data="' . $task_id . '" data-toggle="tooltip" data-placement="top" title="Isključeno" class="task_emailnotifi1 btn material-btn material-btn_disabled main-container__column"><i class="fa fa-envelope-o" aria-hidden="true"></i></a></span>';
																			}elseif($task_emailnotifi == 1){
																				echo '<span data-toggle="modal" data-target="#taskEmailModal"><a data="' . $task_id . '" data-toggle="tooltip" data-placement="top" title="Na dan dospijeća" class="task_emailnotifi1 btn material-btn material-btn_success main-container__column"><i class="fa fa-envelope-o" aria-hidden="true"></i></a></span>';
																			}elseif($task_emailnotifi == 2){
																				echo '<span data-toggle="modal" data-target="#taskEmailModal"><a data="' . $task_id . '" data-toggle="tooltip" data-placement="top" title="Dan prije dospijeća" class="task_emailnotifi1 btn material-btn material-btn_success main-container__column"><i class="fa fa-envelope-o" aria-hidden="true"></i></a></span>';
																			}elseif($task_emailnotifi == 3){
																				echo '<span data-toggle="modal" data-target="#taskEmailModal"><a data="' . $task_id . '" data-toggle="tooltip" data-placement="top" title="Dva dana prije dospijeća" class="task_emailnotifi1 btn material-btn material-btn_success main-container__column"><i class="fa fa-envelope-o" aria-hidden="true"></i></a></span>';
																			}elseif($task_emailnotifi == 4){
																				echo '<span data-toggle="modal" data-target="#taskEmailModal"><a data="' . $task_id . '" data-toggle="tooltip" data-placement="top" title="Sedmicu dana prije dospijeća" class="task_emailnotifi1 btn material-btn material-btn_success main-container__column"><i class="fa fa-envelope-o" aria-hidden="true"></i></a></span>';
																			}elseif($task_emailnotifi == 5){
																				echo '<span data-toggle="modal" data-target="#taskEmailModal"><a data="' . $task_id . '" data-toggle="tooltip" data-placement="top" title="Mjesec dana prije dospijeća" class="task_emailnotifi1 btn material-btn material-btn_success main-container__column"><i class="fa fa-envelope-o" aria-hidden="true"></i></a></span>';
																			}
																		?>
																		<script>
																			$(".task_emailnotifi1").click(function () {
																				var addressValue = $(this).attr("data");
																				document.getElementById("task_id_emailnotifi").value = addressValue;
																			});
																		</script>
																		<div class="modal material-modal material-modal_primary fade text-left" id="taskEmailModal">
																			<div class="modal-dialog ">
																				<div class="modal-content material-modal__content">
																					<div class="modal-header material-modal__header">
																						<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																						<h4 class="modal-title material-modal__title">E-mail napomena</h4>
																					</div>
																					<div class="modal-body material-modal__body">
																						<form action="<?php getSiteURL(); ?>do.php?form=add_task_emailnotifi_employee" method="post" role="form" class="form-horizontal">
																							<input type="hidden" name="task_id" id="task_id_emailnotifi" value="" />
																							<input type="hidden" name="task_dataid" value="<?php echo $employee_id; ?>" />
																							<div class="form-group">
																								<div class="col-md-offset-2 col-sm-8">
																									<select class="selectpicker" id="task_emailnotifi" name="task_emailnotifi" required>
																										<option value="0">Isključeno</option>
																										<option value="1">Na dan dospijeća</option>
																										<option value="2">Dan prije dospijeća</option>
																										<option value="3">Dva dana prije dospijeća</option>
																										<option value="4">Sedmicu dana prije dospijeća</option>
																										<option value="5">Mjesec dana prije dospijeća</option>
																									</select>
																								</div>
																							</div>
																					</div>
																					<div class="modal-footer material-modal__footer">
																						<ul class="list-inline">
																							<li class="hidden"><i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i></li>
																							<li><button type="submit" class="btn btn-primary material-btn material-btn_primary">Snimi</button></li>
																						</ul>
																						</form>
																					</div>
																				</div>
																			</div>
																		</div>
																	</li>
																	<li>
																		<?php
																			if($task_repeatnotifi == 0){
																				echo '<span data-toggle="modal" data-target="#taskRepeatModal"><a data="' . $task_id . '" data-toggle="tooltip" data-placement="top" title="Isključeno" class="task_repeatnotifi1 btn material-btn material-btn_disabled main-container__column"><i class="fa fa-repeat" aria-hidden="true"></i></a></span>';
																			}elseif($task_repeatnotifi == 1){
																				echo '<span data-toggle="modal" data-target="#taskRepeatModal"><a data="' . $task_id . '" data-toggle="tooltip" data-placement="top" title="Dnevno" class="task_repeatnotifi1 btn material-btn material-btn_success main-container__column"><i class="fa fa-repeat" aria-hidden="true"></i></a></span>';
																			}elseif($task_repeatnotifi == 2){
																				echo '<span data-toggle="modal" data-target="#taskRepeatModal"><a data="' . $task_id . '" data-toggle="tooltip" data-placement="top" title="Sedmično" class="task_repeatnotifi1 btn material-btn material-btn_success main-container__column"><i class="fa fa-repeat" aria-hidden="true"></i></a></span>';
																			}elseif($task_repeatnotifi == 3){
																				echo '<span data-toggle="modal" data-target="#taskRepeatModal"><a data="' . $task_id . '" data-toggle="tooltip" data-placement="top" title="Mjesečno" class="task_repeatnotifi1 btn material-btn material-btn_success main-container__column"><i class="fa fa-repeat" aria-hidden="true"></i></a></span>';
																			}elseif($task_repeatnotifi == 4){
																				echo '<span data-toggle="modal" data-target="#taskRepeatModal"><a data="' . $task_id . '" data-toggle="tooltip" data-placement="top" title="Godišnje" class="task_repeatnotifi1 btn material-btn material-btn_success main-container__column"><i class="fa fa-repeat" aria-hidden="true"></i></a></span>';
																			}
																		?>
																		<script>
																			$(".task_repeatnotifi1").click(function () {
																				var addressValue = $(this).attr("data");
																				document.getElementById("task_id_repeat").value = addressValue;
																			});
																		</script>
																		<div class="modal material-modal material-modal_primary fade text-left" id="taskRepeatModal">
																			<div class="modal-dialog ">
																				<div class="modal-content material-modal__content">
																					<div class="modal-header material-modal__header">
																						<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																						<h4 class="modal-title material-modal__title">Ponavljaj zadatak</h4>
																					</div>
																					<div class="modal-body material-modal__body">
																						<form action="<?php getSiteURL(); ?>do.php?form=add_task_repeat_employee" method="post" role="form" class="form-horizontal">
																							<input type="hidden" name="task_id" id="task_id_repeat" value="" />
																							<input type="hidden" name="task_dataid" value="<?php echo $employee_id; ?>" />
																							<div class="form-group">
																								<div class="col-md-offset-2 col-sm-8">
																									<select class="selectpicker" id="task_repeatnotifi" name="task_repeatnotifi" required>
																										<option value="0">Isključeno</option>
																										<option value="1">Dnevno</option>
																										<option value="2">Sedmično</option>
																										<option value="3">Mjesečno</option>
																										<option value="4">Godišnje</option>
																									</select>
																								</div>
																							</div>
																					</div>
																					<div class="modal-footer material-modal__footer">
																						<ul class="list-inline">
																							<li class="hidden"><i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i></li>
																							<li><button type="submit" class="btn btn-primary material-btn material-btn_primary">Snimi</button></li>
																						</ul>
																						</form>
																					</div>
																				</div>
																			</div>
																		</div>
																	</li>
																	<?php } ?>
																</ul>
															</div>
														</div>
													</div>
												</li>
												<?php } ?>
											</ul>
										</div>
									</div>
								</div>
								<div class="tab-pane fade" id="notes">
									<?php if ((in_array( "1" , $getEmployeeStatus)) OR $logged_employee_id == 32 OR $logged_employee_id == 33 OR $logged_employee_id == 11){ ?>
                                    <ul class="list-inline text-right">
										<li><a href="#" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" data-toggle="modal" data-target="#noteModal"><i class="fa fa-sticky-note-o" aria-hidden="true"></i> <span>Dodaj bilješku</span></a></li>
										<!-- Modal add note -->
										<div class="modal material-modal material-modal_primary fade text-left" id="noteModal">
											<div class="modal-dialog ">
												<div class="modal-content material-modal__content">
													<div class="modal-header material-modal__header">
														<button class="close material-modal__close" data-dismiss="modal">&times;</button>
														<h4 class="modal-title material-modal__title">Dodaj bilješku</h4>
													</div>
													<div class="modal-body material-modal__body">
														<form action="<?php getSiteURL(); ?>do.php?form=add_employee_note" method="post" role="form" class="form-horizontal">
															<input type="hidden" name="note_dataid" value="<?php echo $employee_id; ?>" />
															<div class="form-group">
																<div class="col-md-offset-2 col-sm-8">
																	<div class="form-group materail-input-block materail-input-block_success">
																		<textarea class="form-control materail-input material-textarea" name="note_txt" placeholder="Bilješka" rows="6" required></textarea>
																		<span class="materail-input-block__line"></span>
																	</div>
																</div>
															</div>
													</div>
													<div class="modal-footer material-modal__footer">
														<ul class="list-inline">
															<li class="hidden"><i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i></li>
															<li><button type="submit" class="btn btn-primary material-btn material-btn_primary">Dodaj</button></li>
														</ul>
														</form>
													</div>
												</div>
											</div>
										</div>
										<!-- Modal add note end -->
									</ul>
									<hr>
									<div class="panel-group material-accordion material-accordion_primary" id="accordion1">
										<?php

											$year_query = $db->prepare("
																SELECT YEAR (note_datetime) AS note_datetime_year
																FROM idk_notes
																WHERE note_dataid = :note_dataid AND note_group = :note_group
																GROUP BY YEAR (note_datetime)
																ORDER BY YEAR (note_datetime) DESC");

											$year_query->execute(array(
															':note_dataid' => $employee_id,
															':note_group' => 1));

											while($year_row = $year_query->fetch()){

												$note_datetime_year = $year_row['note_datetime_year'];

												if($note_datetime_year == date('Y')){
													$idk_notes_in = "in";
												}else{
													$idk_notes_in = "";
												}

										?>
										<div class="panel panel-default material-accordion__panel material-accordion__panel">
											<div class="panel-heading material-accordion__heading">
												<h4 class="panel-title">
													<a class="material-accordion__title" data-toggle="collapse" data-parent="#accordion1" href="#<?php echo $note_datetime_year; ?>"><?php echo $note_datetime_year; ?></a>
												</h4>
											</div>
											<div id="<?php echo $note_datetime_year; ?>" class="panel-collapse <?php echo $idk_notes_in; ?> collapse material-accordion__collapse">
												<div class="panel-body">
													<?php
														$notes_query = $db->prepare("
																		SELECT note_id, note_datetime, note_txt, employee_firstname, employee_lastname
																		FROM idk_notes
																		INNER JOIN idk_employees ON idk_notes.note_employeeid = idk_employees.employee_id
																		WHERE YEAR (note_datetime) = :note_datetime_year AND note_dataid = :note_dataid AND note_group = :note_group
																		ORDER BY note_datetime DESC");

														$notes_query->execute(array(
																		':note_datetime_year' => $note_datetime_year,
																		':note_group' => 1,
																		':note_dataid' => $employee_id));

														while($notes_row = $notes_query->fetch()){

															$note_date = date('d.m.Y.', strtotime($notes_row['note_datetime']));
															$note_time = date('H:i', strtotime($notes_row['note_datetime']));
															$note_id = $notes_row['note_id'];
															$note_txt = $notes_row['note_txt'];
															$employee_firstname = $notes_row['employee_firstname'];
															$employee_lastname = $notes_row['employee_lastname'];

													?>
													<div class="row">
														<div class="col-sm-3">
															<p><i class="fa fa-calendar text-primary" aria-hidden="true"></i> <?php echo $note_date; ?> | <i class="fa fa-clock-o text-primary" aria-hidden="true"></i> <?php echo $note_time; ?></p>
															<p><i class="fa fa-user text-primary" aria-hidden="true"></i> <?php echo $employee_firstname; ?> <?php echo $employee_lastname; ?></p>
														</div>
														<div class="col-sm-7">
															<p><?php echo $note_txt; ?></p>
														</div>
														<div class="col-sm-2 text-right">
															<a href="#" data="<?php getSiteURL(); ?>employees?page=del_note&id=<?php echo $note_id; ?>" data-toggle="modal" data-target="#deleteNoteModal" class="delete_note btn material-btn material-btn_danger main-container__column"><i class="fa fa-trash-o fa-lg" aria-hidden="true"></i></a>
															<script>
																$(".delete_note").click(function () {
																	var addressValue = $(this).attr("data");
																	document.getElementById("delete_note_link").href = addressValue;
																});
															</script>
															<!-- Modal -->
															<div class="modal material-modal material-modal_danger fade text-left" id="deleteNoteModal">
																<div class="modal-dialog">
																	<div class="modal-content material-modal__content">
																		<div class="modal-header material-modal__header">
																			<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																			<h4 class="modal-title material-modal__title">Brisanje</h4>
																		</div>
																		<div class="modal-body material-modal__body">
																			<p>Jeste li sigurni da želite obrisati bilješku?</p>
																		</div>
																		<div class="modal-footer material-modal__footer">
																			<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
																			<a id="delete_note_link" href=""><button class="btn btn-primary material-btn material-btn_danger">OBRIŠI</button></a>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
													<hr />
													<?php } ?>
												</div>
											</div>
										</div>
										<?php } ?>
									</div>
                                    <?php } ?>
								</div>
								<div class="tab-pane fade" id="documents">
									<ul class="list-inline text-right">
										<li><a href="#" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" data-toggle="modal" data-target="#docModal"><i class="fa fa-file-text-o" aria-hidden="true"></i> <span>Dodaj dokument</span></a></li>
										<!-- Modal add document -->
										<div class="modal material-modal material-modal_primary fade text-left" id="docModal">
											<div class="modal-dialog ">
												<div class="modal-content material-modal__content">
													<div class="modal-header material-modal__header">
														<button class="close material-modal__close" data-dismiss="modal">&times;</button>
														<h4 class="modal-title material-modal__title">Dodaj dokument</h4>
													</div>
													<div class="modal-body material-modal__body">
														<form action="<?php getSiteURL(); ?>do.php?form=add_employee_doc" enctype="multipart/form-data" method="post" role="form" class="form-horizontal">
															<div id="idk_alert_size" class="row hidden">
																<div class="col-sm-12">
																	<div class="alert material-alert material-alert_danger">Greška: Dokument koji pokuštavate dodati je veći od dozvoljene veličine.</div>
																</div>
															</div>
															<div id="idk_alert_ext" class="row hidden">
																<div class="col-sm-12">
																	<div class="alert material-alert material-alert_danger">Greška: Format dokumenta kojeg pokušavate dodati nije dozvoljen.</div>
																</div>
															</div>
															<input type="hidden" name="document_dataid" value="<?php echo $employee_id; ?>" />
															<div class="form-group">
																<div class="col-md-offset-2 col-sm-8">
																	<div class="form-group materail-input-block materail-input-block_success">
																		<input type="text" class="form-control materail-input" name="document_name" id="document_name" placeholder="Naziv dokumenta" required>
																		<span class="materail-input-block__line"></span>
																	</div>
																</div>
															</div>
															<div class="form-group">
																<div class="col-md-offset-2 col-sm-8">
																	<div class="form-group materail-input-block materail-input-block_success">
																		<input type="text" class="form-control materail-input" name="document_desc" id="document_desc" placeholder="Opis dokumenta">
																		<span class="materail-input-block__line"></span>
																	</div>
																</div>
															</div>
															<div class="form-group">
																<div class="col-md-offset-2 col-sm-8">
																	<div class="fileinput fileinput-new" data-provides="fileinput">
																		<span class="btn btn-default btn-file"><span class="fileinput-new">Izaberi dokument</span><span class="fileinput-exists">Promijeni</span><input type="file" name="document_file" id="document_file" required required></span> <i class="fa fa-info-circle fa-lg idk_margin_left10" data-toggle="tooltip" data-placement="right" title="Napomena: Dokument ne smije biti veći od 20MB! - Dozvoljeni formati: jpg, jpeg, pdf, doc, docx, xls, xlsx, txt, ppt, pptx, png" aria-hidden="true"></i>
																		<span class="fileinput-filename"></span>
																		<a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none">×</a>
																		<script>
																			$(function (){
																				$('#document_file').change(function (){

																					var ext = $('#document_file').val().split('.').pop().toLowerCase();

																					if($.inArray(ext, ['jpg', 'jpeg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png']) == -1) {
																						$('#idk_alert_ext').removeClass('hidden');
																						this.value = null;
																					}else{
																						$('#idk_alert_ext').addClass('hidden');
																					}

																					var f = this.files[0];

																					if (f.size > 20388608 || f.fileSize > 20388608){
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
													<div class="modal-footer material-modal__footer">
															<ul class="list-inline">
																<li class="hidden"><i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i></li>
																<li><button type="submit" class="btn btn-primary material-btn material-btn_primary">Dodaj</button></li>
															</ul>
														</form>
													</div>
												</div>
											</div>
										</div>
										<!-- Modal add document end -->
									</ul>
									<hr>
									<script type="text/javascript">
										$(document).ready(function() {
											$('#idk_table_documents').DataTable({

												"order": [[ 0, "desc" ]],

												 "bAutoWidth": false,

												"aoColumns": [
														{ "width": "10%" },
														{ "width": "40%" },
														{ "width": "40%" },
														{ "width": "5%", "bSortable": false },
														{ "width": "5%", "bSortable": false }
													]
											});
										} );
									</script>
									<table id="idk_table_documents" class="display" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th>Datum</th>
												<th>Naziv</th>
												<th>Opis</th>
												<th>Preuzimanje</th>
												<th></th>
											</tr>
										</thead>
										<tbody>
											<?php
												$query_doc = $db->prepare("
																SELECT document_id, document_name, document_desc, document_file, document_icon, document_datetime
																FROM idk_documents
																WHERE document_group = :document_group AND document_dataid = :document_dataid");

												$query_doc->execute(array(
															':document_group' => 1,
															':document_dataid' => $employee_id));

												while($row_doc = $query_doc->fetch()){

													$document_id = $row_doc['document_id'];
													$document_name = $row_doc['document_name'];
													$document_desc = $row_doc['document_desc'];
													$document_file = $row_doc['document_file'];
													$document_datetime = date('d.m.Y.', strtotime($row_doc['document_datetime']));

													if($row_doc['document_icon'] == "jpg" OR $row_doc['document_icon'] == "png"){
														$document_icon = '<i class="fa fa-file-image-o fa-lg" aria-hidden="true"></i>';
													}elseif($row_doc['document_icon'] == "pdf"){
														$document_icon = '<i class="fa fa-file-pdf-o fa-lg" aria-hidden="true"></i>';
													}elseif($row_doc['document_icon'] == "doc" OR $row_doc['document_icon'] == "docx"){
														$document_icon = '<i class="fa fa-file-word-o fa-lg" aria-hidden="true"></i>';
													}elseif($row_doc['document_icon'] == "xls" OR $row_doc['document_icon'] == "xlsx"){
														$document_icon = '<i class="fa fa-file-excel-o fa-lg" aria-hidden="true"></i>';
													}elseif($row_doc['document_icon'] == "txt"){
														$document_icon = '<i class="fa fa-file-text-o fa-lg" aria-hidden="true"></i>';
													}elseif($row_doc['document_icon'] == "ppt" OR $row_doc['document_icon'] == "pptx"){
														$document_icon = '<i class="fa fa-file-powerpoint-o fa-lg" aria-hidden="true"></i>';
													}else{
														$document_icon = '<i class="fa fa-file-o fa-lg" aria-hidden="true"></i>';
													}
											?>
											<tr>
												<td class="text-center"><?php echo $document_datetime; ?></td>
												<td><?php echo $document_name; ?></td>
												<td><?php echo $document_desc; ?></td>
												<td class="text-center"><a href="download?folder=employees&id=<?php echo $document_id; ?>" class="btn material-btn material-btn_success main-container__column" target="_BLANK"><?php echo $document_icon; ?></a></td>
												<td class="text-center"><a href="#" data="<?php getSiteURL(); ?>employees?page=del_doc&id=<?php echo $document_id; ?>" data-toggle="modal" data-target="#deleteDocModal" class="delete_doc btn material-btn material-btn_danger main-container__column"><i class="fa fa-trash-o fa-lg" aria-hidden="true"></i></a></td>
											</tr>
											<?php } ?>
											<script>
												$(".delete_doc").click(function () {
													var addressValue = $(this).attr("data");
													document.getElementById("delete_doc_link").href = addressValue;
												});
											</script>
											<!-- Modal -->
											<div class="modal material-modal material-modal_danger fade" id="deleteDocModal">
												<div class="modal-dialog">
													<div class="modal-content material-modal__content">
														<div class="modal-header material-modal__header">
															<button class="close material-modal__close" data-dismiss="modal">&times;</button>
															<h4 class="modal-title material-modal__title">Brisanje</h4>
														</div>
														<div class="modal-body material-modal__body">
															<p>Jeste li sigurni da želite obrisati dokument?</p>
														</div>
														<div class="modal-footer material-modal__footer">
															<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
															<a id="delete_doc_link" href=""><button class="btn btn-primary material-btn material-btn_danger">OBRIŠI</button></a>
														</div>
													</div>
												</div>
											</div>
										</tbody>
									</table>
								</div>
								<div class="tab-pane fade" id="skladiste">
									<script type="text/javascript">
										$(document).ready(function() {
											$('#idk_table').DataTable({

												responsive: true,

												"order": [[ 1, "asc" ]],

												 "bAutoWidth": false,

												"aoColumns": [
														{ "width": "5%", "bSortable": false },
														{ "width": "20%" },
														{ "width": "25%" },
														{ "width": "20%" },
														{ "width": "10%" },
														{ "width": "10%" },
														{ "width": "10%", "bSortable": false }
													]
											});
										} );
									</script>
									<table id="idk_table" class="display" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th></th>
												<th>Naziv</th>
												<th>Opis</th>
												<th>Informacije</th>
												<th class="text-center">Poslovnica</th>
												<th class="text-center">Status</th>
												<th class="text-center">Prihvati/odbij</th>
											</tr>
										</thead>
										<tbody>
											<?php
												$query_skladistar = $db->prepare("
																SELECT employee_id, employee_warehouse
																FROM idk_employees
																WHERE employee_id = :employee_id
																");
												$query_skladistar->execute(array(
															':employee_id' => $logged_employee_id));

												$rowSkladistar = $query_skladistar->fetch();
													$employee_warehouse = $rowSkladistar['employee_warehouse'];


												$query_zaduzeno_list = $db->prepare("
																SELECT sz_quantity, sz_opis, isz_sklid, isz_id, skl_title, skl_desc, skl_branch, isz_status, skl_image
																FROM idk_skladiste_zaposlenici
																INNER JOIN idk_skladiste ON idk_skladiste_zaposlenici.isz_sklid = idk_skladiste.skl_id
																WHERE sz_employeeid = :sz_employeeid AND (isz_status = 1 OR isz_status = 2) AND skl_archive = 0");

												$query_zaduzeno_list->execute(array(
															':sz_employeeid' => $employee_id));

												$sumCount = 1;
												while($rowlist = $query_zaduzeno_list->fetch()){

													//$isz_id = $rowlist['isz_id'];
													// - $isz_sklid = $rowlist['isz_sklid'];
													// - $sz_quantity = $rowlist['sz_quantity'];
													//$sz_opis = $rowlist['sz_opis'];
													//$skl_title = $rowlist['skl_title'];
													//$skl_desc = $rowlist['skl_desc'];
													// - $employee_id = $rowlist['employee_id'];
													// - $employe_fullname = $rowlist['employee_firstname']." ".$rowlist['employee_lastname'];
													// - $employee_firstname = $rowlist['employee_firstname'];
													// - $employee_lastname = $rowlist['employee_lastname'];

													$sz_id = $rowlist['isz_id'];
													$skl_title = $rowlist['skl_title'];
													$skl_desc = $rowlist['skl_desc'];
													$skl_image = $rowlist['skl_image'];
													$skl_branch = $rowlist['skl_branch'];
													$sz_status = $rowlist['isz_status'];
													$sz_opis = $rowlist['sz_opis'];

													if($sz_status == 1){
														$ispis = '<span class="label label-success material-label material-label_success main-container__column">ZADUŽENO</span>';
														$prihvati_odbij = '';
													}
													else if($sz_status == 2){
														$ispis = '<span class="label label-primary material-label material-label_primary main-container__column">NA ČEKANJU</span>';
														$prihvati_odbij = '<a href="'.getSiteUrlR().'do.php?form=skladiste_primi_stavku&sz_id='.$sz_id.'&skl_title='.$skl_title.' " class="text-success" style="font-size: 24px;cursor:pointer;"><i class="fa fa-check" aria-hidden="true"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;
																		   <a class="text-danger odbijanje" style="font-size: 24px;cursor:pointer; " href="#" data-productname="'.$skl_title.'" data="'.getSiteUrlR().'do.php?form=skladiste_odbij_stavku&sz_id='.$sz_id.'&skl_title='.$skl_title.'" data-toggle="modal" data-target="#odbij_stavkuModal"><i class="fa fa-times" aria-hidden="true"></i></a>';
													}else if($sz_status == 0){
														$ispis = '';
														$prihvati_odbij = '';
													}

													if($skl_image != "none"){
														$skl_image_txt = $skl_image;
													}else{
														$skl_image_txt = "box.png";
													}

													// GET BRANCH INFO
													$query_branches = $db->prepare("
																	SELECT branch_id, branch_name, branch_state, branch_city
																	FROM idk_poslovnice
																	WHERE branch_id = :branch_id
																	");

													$query_branches->execute(array(
														":branch_id" => $skl_branch
													));

													$rowb = $query_branches->fetch();

													$branch_name = $rowb['branch_name'];
											?>
												<tr>
													<td class="text-center"><img class="idk_profile_img" src="<?php getSiteURL(); ?>images/<?php echo $skl_image_txt; ?>"></td>
													<td><?php echo $skl_title; ?></td>
													<td><?php echo $skl_desc; ?></td>
													<td><?php echo $sz_opis; ?></td>
													<td class="text-center"><span class="label label-success"><?php echo $branch_name; ?></span></td>
													<td class="text-center"><?php echo $ispis; ?></td>
													<td class="text-center"><?php if($logged_employee_id == $employee_id) { echo $prihvati_odbij; } ?></td>
												</tr>
											<?php } ?>
											<script>
												$(".odbijanje").click(function () {
													var addressValue = $(this).attr("data");
													var productname = $(this).data("productname");
													$('#nazivstavke').html(productname);
													document.getElementById("archive_link").href = addressValue;
												});

											</script>
											<!-- Modal odbijanje stavke -->
											<div class="modal material-modal material-modal_danger fade" id="odbij_stavkuModal">
												<div class="modal-dialog">
													<div class="modal-content material-modal__content">
														<div class="modal-header material-modal__header">
															<button class="close material-modal__close" data-dismiss="modal">&times;</button>
															<h4 class="modal-title material-modal__title">Odbij stavku!</h4>
														</div>
														<div class="modal-body material-modal__body">
															<p>Jeste li sigurni da želite odbiti stavku: <span id="nazivstavke"></span>?</p>
														</div>

														<div class="form-group">
															<div class="col-sm-12">
																<div class="form-group materail-input-block materail-input-block_primary materail-input_slide-line">
																	<textarea class="form-control materail-input material-textarea" name="razlog_odbijanja" id="razlog_odbijanja" placeholder="Unesite razlog odbijanja" rows="6" required></textarea>
																	<span class="materail-input-block__line"></span>
																</div>
															</div>
														</div>

														<div class="modal-footer material-modal__footer">
															<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
															<a id="archive_link" href=""><button class="btn btn-primary material-btn material-btn_danger">ODBIJ</button></a>
														</div>
													</div>
												</div>
											</div>
											<script>

												$("#archive_link").click(function (event) {
													var razlog_odbijanja = $("#razlog_odbijanja").val();
													if (razlog_odbijanja == ""){
														alert('Morate unijeti razlog!');
														event.preventDefault();
													}else{
														var addressValue = $("#archive_link").attr('href');
														var full_url = ""+addressValue+"&razlog="+razlog_odbijanja+"";
														event.preventDefault();
														window.location.replace(full_url);
													}
												});
											</script>
										</tbody>
									</table>
								</div>
								<div class="tab-pane fade" id="important">
									<form action="<?php getSiteURL(); ?>do.php?form=save_employee_inote" method="post" role="form" class="form-horizontal">
										<input type="hidden" name="employee_id" value="<?php echo $employee_id; ?>" />
										<div class="form-group">
											<div class="col-md-offset-1 col-sm-10">
												<div class="form-group materail-input-block materail-input-block_success">
													<textarea id="inote" class="form-control materail-input material-textarea" name="employee_inote" placeholder="Važne bilješke" rows="8"><?php echo base64_decode($employee_inote); ?></textarea>
													<span class="materail-input-block__line"></span>
												</div>
												<ul class="list-inline pull-right">
													<li class="hidden"><i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i></li>
													<li><button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-save" aria-hidden="true"></i> <span>Snimi</span></button></li>
												</ul>
											</div>
										</div>
									</form>
									<script>
										$('#inote').trumbowyg({
											lang: 'hr',
										    btns: [
										        ['undo', 'redo'],
										        ['formatting'],
										        ['strong', 'em', 'del'],
										        ['link'],
										        ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
										        ['unorderedList', 'orderedList'],
										        ['horizontalRule'],
										        ['fullscreen']
										    ]
										});
									</script>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php
				break;

				case "del_doc":
					if(in_array("1", $getEmployeeStatus)){

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
					if(in_array("1", $getEmployeeStatus)){

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
						$log_desc = "Obrisao bilješku: " .$note_txt. "";
						$log_type = "3";
						addToLogs($log_desc, $log_type); //Log za biljeske - $log_type = 3

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
					if(in_array("1", $getEmployeeStatus)){

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
					if(in_array("1", $getEmployeeStatus)){

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
					if(in_array("1", $getEmployeeStatus)){

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
