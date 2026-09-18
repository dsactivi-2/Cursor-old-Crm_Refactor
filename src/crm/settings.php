<?php
	include("includes/functions.php");
	include("includes/common.php");
	$getEmployeeStatus = explode( ',' , getEmployeeStatus());

	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: postavke");
	}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Settings | <?php getTitle(); ?></title>

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

				case "storage":
					if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "6" , $getEmployeeStatus)) OR (in_array( "7" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){

						//Mark as read
						if(isset($_GET['nid'])) {
							$notification_id = $_GET['nid'];

							$query_update = $db->prepare("
												UPDATE idk_notifications
												SET	notification_status = :notification_status
												WHERE notification_id = :notification_id AND notification_datetime <= 'NOW()'");

							$query_update->execute(array(
											':notification_status' => 2,
											':notification_id' => $notification_id));
						}

		?>
			<div class="row">
				<div class="col-xs-12">
					<h1><i class="fa fa-pie-chart idk_color_green" aria-hidden="true"></i> Prostor na disku</h1>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>

			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<div class="row">
							<div class="col-sm-3">
								<h5>Ukupno prostora</h5>
								<?php
									function folderSize ($dir){

										$bytes_all = 0;

											foreach (glob(rtrim($dir, '/').'/*', GLOB_NOSORT) as $each) {
												$bytes_all += is_file($each) ? filesize($each) : folderSize($each);
											}

										return  $bytes_all;

									}

									$folder_size = folderSize('../../private/' . getSubdomainr() . '_files/');
									$db_size = getDBSize();
									$bytes_used = $folder_size + $db_size;
									$number_users = getNumberUsers();
									$crm_folder_size = getCrmFolderSizer();
									$bytes = $crm_folder_size * 1073741824;
									$bytes_total = $number_users * $bytes;

									if ($bytes_used >= 1073741824){
										$bytes_used_format = number_format($bytes_used / 1073741824, 2) . ' GB';
									}elseif ($bytes_used >= 1048576){
										$bytes_used_format = number_format($bytes_used / 1048576, 2) . ' MB';
									}elseif ($bytes_used >= 1024){
										$bytes_used_format = number_format($bytes_used / 1024, 2) . ' kB';
									}elseif ($bytes_used > 1){
										$bytes_used_format = $bytes_used . ' bytes';
									}elseif ($bytes_used == 1){
										$bytes_used_format = $bytes_used . ' byte';
									}else{
										$bytes_used_format = '0 bytes';
									}

									if ($bytes_total >= 1073741824){
										$bytes_total_format = number_format($bytes_total / 1073741824, 0) . ' GB';
									}elseif ($bytes_total >= 1048576){
										$bytes_total_format = number_format($bytes_total / 1048576, 0) . ' MB';
									}elseif ($bytes_total >= 1024){
										$bytes_total_format = number_format($bytes_total / 1024, 2) . ' kB';
									}elseif ($bytes_total > 1){
										$bytes_total_format = $bytes_total . ' bytes';
									}elseif ($bytes_total == 1){
										$bytes_total_format = $bytes_total . ' byte';
									}else{
										$bytes_total_format = '0 bytes';
									}

									$percentage = ($bytes_used / $bytes_total) * 100;
									$percentage_format = number_format($percentage, 0);

									//Check for limit and send Notification
									if($percentage_format > 90){

										$notification_title = "Prostor na serveru je nizak!";
										$notification_icon = "pie-chart";
										$notification_link = "" . getSiteURLr() . "settings?page=storage";
										$datetime = date('Y-m-d H:i:s', strtotime("+5 minutes"));

										$query_notification = $db->prepare("
																INSERT INTO idk_notifications
																	(notification_datetime, notification_title, notification_icon, notification_link, notification_employeeid, notification_status)
																VALUES
																	(:notification_datetime, :notification_title, :notification_icon, :notification_link, :notification_employeeid, :notification_status)");

										$query_notification->execute(array(
																':notification_datetime' => $datetime,
																':notification_title' => $notification_title,
																':notification_icon' => $notification_icon,
																':notification_link' => $notification_link,
																':notification_employeeid' => 1,
																':notification_status' => 1));
									}

								?>
								<canvas id="myPieChart"></canvas>
								<script>
									var ctx = document.getElementById("myPieChart");
									var myPieChart  = new Chart(ctx, {
										type: 'pie',
										data: {
											datasets: [{
													data: [<?php $percentage_free = 100 - $percentage_format; if ($percentage_free < 0){ echo '0'; }else{ echo $percentage_free; } ?>, <?php echo $percentage_format; ?>],
													backgroundColor: [
														"#68c368",
														"#f3413c"
													],
													hoverBackgroundColor: [
														"#68c368",
														"#f3413c"
													]
												}]
										},
										options: {
											tooltips: {
												enabled: false,
											},
											type:"pie",
												animation:{
													animateScale:true
												}
										}
									});
								</script>
								<br />
								<ul class="list-inline idk_storage_label">
									<li>
										<h6 class="text-danger"><?php echo $bytes_used_format ?></h6>
										<p><?php echo $percentage_format; ?>% se koristi</p>
									</li>
									<li>
										<div></div>
									</li>
									<li>
										<h6><?php echo $bytes_total_format ?></h6>
										<p>Ukupno prostora</p>
									</li>
								</ul>
							</div>
							<div class="col-sm-9">
								<h5>Paketi</h5>
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

				case "contact_type":
					if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "6" , $getEmployeeStatus)) OR (in_array( "7" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){
		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-address-card-o idk_color_green" aria-hidden="true"></i> Vrste kontakta</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">

				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<h5>Dodaj novu vrstu kontakta</h5>
						<div class="row">
							<div class="col-xs-12 text-center">
								<form id="idk_form" action="<?php getSiteURL(); ?>do.php?form=add_contact_type" method="post" class="form-horizontal" role="form">
									<div class="form-group">
										<label for="otherdata_data" class="col-sm-4 control-label"><span class="text-danger">*</span> Naziv vrste kontakta:</label>
										<div class="col-sm-4">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="otherdata_data" id="otherdata_data" placeholder="Naziv ..." required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
										<div class="col-sm-4 text-left">
											<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj</span></button>
										</div>
									</div>
								</form>
							</div>
						</div>
						<hr>
						<div class="row">
							<div class="col-xs-12">
								<h5>Trenutne vrste kontakta</h5>
								<?php
									if(isset($_GET['mess'])) {
										$mess = $_GET['mess'];
									}else{
										$mess = 0;
									}

									if($mess == 1){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novu vrstu kontakta.</div>';
									}elseif($mess == 2){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste obrisali vrstu kontakta.</div>';
									}
								?>
								<script type="text/javascript">
									$(document).ready(function() {
										$('#idk_table').DataTable({

											"order": [[ 0, "asc" ]],

											 "bAutoWidth": false,

											"aoColumns": [
													{ "width": "90%" },
													{ "width": "10%", "bSortable": false }
												]
										});
									} );
								</script>
								<table id="idk_table" class="display" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th>Naziv</th>
											<th>Obriši</th>
										</tr>
									</thead>
									<tbody>
										<?php
											$query = $db->prepare("
															SELECT otherdata_id, otherdata_data
															FROM idk_otherdata WHERE otherdata_group = :otherdata_group");

											$query->execute(array(
														':otherdata_group' => 2));

											while($row = $query->fetch()){

												$otherdata_id = $row['otherdata_id'];
												$otherdata_data = $row['otherdata_data'];

										?>
										<tr>
											<td><?php echo $otherdata_data; ?></td>
											<td class="text-center">
												<a href="#" data="<?php getSiteURL(); ?>do.php?form=delete_contact_type&id=<?php echo $otherdata_id; ?>"  data-toggle="modal" data-target="#deleteModal" class="delete dropdown-toggle material-dropdown-btn material-btn material-btn_danger"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
											</td>
										</tr>
										<?php } ?>
										<script>
											$(".delete").click(function () {
												var addressValue = $(this).attr("data");
												document.getElementById("delete_link").href = addressValue;
											});
										</script>
										<!-- Modal -->
										<div class="modal material-modal material-modal_danger fade" id="deleteModal">
											<div class="modal-dialog">
												<div class="modal-content material-modal__content">
													<div class="modal-header material-modal__header">
														<button class="close material-modal__close" data-dismiss="modal">&times;</button>
														<h4 class="modal-title material-modal__title">Brisanje</h4>
													</div>
													<div class="modal-body material-modal__body">
														<p>Jeste li sigurni da želite obrisati vrstu kontakta?</p>
													</div>
													<div class="modal-footer material-modal__footer">
														<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
														<a id="delete_link" href=""><button class="btn btn-primary material-btn material-btn_danger">OBRIŠI</button></a>
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

				case "company_type":
					if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "6" , $getEmployeeStatus)) OR (in_array( "7" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){
		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-address-card-o idk_color_green" aria-hidden="true"></i> Vrste poslovanja</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">

				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<h5>Dodaj novu vrstu poslovanja</h5>
						<div class="row">
							<div class="col-xs-12 text-center">
								<form id="idk_form" action="<?php getSiteURL(); ?>do.php?form=add_company_type" method="post" class="form-horizontal" role="form">
									<div class="form-group">
										<label for="otherdata_data" class="col-sm-4 control-label"><span class="text-danger">*</span> Naziv vrste poslovanja:</label>
										<div class="col-sm-4">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="otherdata_data" id="otherdata_data" placeholder="Naziv ..." required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
										<div class="col-sm-4 text-left">
											<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj</span></button>
										</div>
									</div>
								</form>
							</div>
						</div>
						<hr>
						<div class="row">
							<div class="col-xs-12">
								<h5>Trenutne vrste poslovanja</h5>
								<?php
									if(isset($_GET['mess'])) {
										$mess = $_GET['mess'];
									}else{
										$mess = 0;
									}

									if($mess == 1){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novu vrstu poslovanja.</div>';
									}elseif($mess == 2){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste obrisali vrstu poslovanja.</div>';
									}
								?>
								<script type="text/javascript">
									$(document).ready(function() {
										$('#idk_table').DataTable({

											"order": [[ 0, "asc" ]],

											 "bAutoWidth": false,

											"aoColumns": [
													{ "width": "90%" },
													{ "width": "10%", "bSortable": false }
												]
										});
									} );
								</script>
								<table id="idk_table" class="display" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th>Naziv</th>
											<th>Obriši</th>
										</tr>
									</thead>
									<tbody>
										<?php
											$query = $db->prepare("
															SELECT otherdata_id, otherdata_data
															FROM idk_otherdata WHERE otherdata_group = :otherdata_group");

											$query->execute(array(
														':otherdata_group' => 3));

											while($row = $query->fetch()){

												$otherdata_id = $row['otherdata_id'];
												$otherdata_data = $row['otherdata_data'];

										?>
										<tr>
											<td><?php echo $otherdata_data; ?></td>
											<td class="text-center">
												<a href="#" data="<?php getSiteURL(); ?>do.php?form=delete_company_type&id=<?php echo $otherdata_id; ?>"  data-toggle="modal" data-target="#deleteModal" class="delete dropdown-toggle material-dropdown-btn material-btn material-btn_danger"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
											</td>
										</tr>
										<?php } ?>
										<script>
											$(".delete").click(function () {
												var addressValue = $(this).attr("data");
												document.getElementById("delete_link").href = addressValue;
											});
										</script>
										<!-- Modal -->
										<div class="modal material-modal material-modal_danger fade" id="deleteModal">
											<div class="modal-dialog">
												<div class="modal-content material-modal__content">
													<div class="modal-header material-modal__header">
														<button class="close material-modal__close" data-dismiss="modal">&times;</button>
														<h4 class="modal-title material-modal__title">Brisanje</h4>
													</div>
													<div class="modal-body material-modal__body">
														<p>Jeste li sigurni da želite obrisati vrstu poslovanja?</p>
													</div>
													<div class="modal-footer material-modal__footer">
														<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
														<a id="delete_link" href=""><button class="btn btn-primary material-btn material-btn_danger">OBRIŠI</button></a>
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

				case "employee_position":
					if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "6" , $getEmployeeStatus)) OR (in_array( "7" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){
		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-users idk_color_green" aria-hidden="true"></i> Pozicije zaposlenika</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">

				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<h5>Dodaj novu poziciju zaposlenika</h5>
						<div class="row">
							<div class="col-xs-12 text-center">
								<form id="idk_form" action="<?php getSiteURL(); ?>do.php?form=add_employee_position" method="post" class="form-horizontal" role="form">
									<div class="form-group">
										<label for="otherdata_data" class="col-sm-4 control-label"><span class="text-danger">*</span> Naziv pozicije zaposlenika:</label>
										<div class="col-sm-4">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="otherdata_data" id="otherdata_data" placeholder="Naziv ..." required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
										<div class="col-sm-4 text-left">
											<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj</span></button>
										</div>
									</div>
								</form>
							</div>
						</div>
						<hr>
						<div class="row">
							<div class="col-xs-12">
								<h5>Trenutne Pozicije zaposlenika</h5>
								<?php
									if(isset($_GET['mess'])) {
										$mess = $_GET['mess'];
									}else{
										$mess = 0;
									}

									if($mess == 1){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novu poziciju zaposlenika.</div>';
									}elseif($mess == 2){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste obrisali poziciju zaposlenika.</div>';
									}
								?>
								<script type="text/javascript">
									$(document).ready(function() {
										$('#idk_table').DataTable({

											"order": [[ 0, "asc" ]],

											 "bAutoWidth": false,

											"aoColumns": [
													{ "width": "90%" },
													{ "width": "10%", "bSortable": false }
												]
										});
									} );
								</script>
								<table id="idk_table" class="display" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th>Naziv</th>
											<th>Obriši</th>
										</tr>
									</thead>
									<tbody>
										<?php
											$query = $db->prepare("
															SELECT otherdata_id, otherdata_data
															FROM idk_otherdata WHERE otherdata_group = :otherdata_group");

											$query->execute(array(
														':otherdata_group' => 1));

											while($row = $query->fetch()){

												$otherdata_id = $row['otherdata_id'];
												$otherdata_data = $row['otherdata_data'];

										?>
										<tr>
											<td><?php echo $otherdata_data; ?></td>
											<td class="text-center">
												<a href="#" data="<?php getSiteURL(); ?>do.php?form=delete_employee_position&id=<?php echo $otherdata_id; ?>"  data-toggle="modal" data-target="#deleteModal" class="delete dropdown-toggle material-dropdown-btn material-btn material-btn_danger"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
											</td>
										</tr>
										<?php } ?>
										<script>
											$(".delete").click(function () {
												var addressValue = $(this).attr("data");
												document.getElementById("delete_link").href = addressValue;
											});
										</script>
										<!-- Modal -->
										<div class="modal material-modal material-modal_danger fade" id="deleteModal">
											<div class="modal-dialog">
												<div class="modal-content material-modal__content">
													<div class="modal-header material-modal__header">
														<button class="close material-modal__close" data-dismiss="modal">&times;</button>
														<h4 class="modal-title material-modal__title">Brisanje</h4>
													</div>
													<div class="modal-body material-modal__body">
														<p>Jeste li sigurni da želite obrisati vrstu kontakta?</p>
													</div>
													<div class="modal-footer material-modal__footer">
														<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
														<a id="delete_link" href=""><button class="btn btn-primary material-btn material-btn_danger">OBRIŠI</button></a>
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
				
				case "email":
					if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "6" , $getEmployeeStatus)) OR (in_array( "7" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){
		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-envelope-o idk_color_green" aria-hidden="true"></i> E-mail predlošci</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>index" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<?php
						if(isset($_GET['mess'])) {
							$mess = $_GET['mess'];
						}else{
							$mess = 0;
						}
	
						if($mess == 1){
							echo '<div class="alert material-alert material-alert_success">Uspješno ste snimili promjene.</div>';
						}elseif($mess == 2){
							echo '<div class="alert material-alert material-alert_danger">Greška: Korisnik kojeg pokuštavate dodati već postoji u bazi podataka.</div>';
						}elseif($mess == 3){
							echo '<div class="alert material-alert material-alert_success">Uspješno ste uredili profil zaposlenika.</div>';
						}elseif($mess == 4){
							echo '<div class="alert material-alert material-alert_success">Uspješno ste arhivirali profil zaposlenika.</div>';
						}
					?>
				</div>
				<div class="col-md-12">
					<div class="content_box">
						<div class="row">
							<div class="col-md-offset-1 col-md-10">
								<?php
									// GET TITLE AND TXT FOR EMAIL REGISTRATION
									$query_email_registration = $db->prepare("
													SELECT email_title, email_txt
													FROM idk_email_text
													WHERE email_value = :email_value");
														
									$query_email_registration->execute(array(
												':email_value' => 'email_registracija'));
									
									$row_email_registration = $query_email_registration->fetch();
									
										$email_title = $row_email_registration['email_title'];
										$email_txt = $row_email_registration['email_txt'];
								?>
							
								<form action="<?php getSiteURL(); ?>do.php?form=edit_email_data" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<div class="form-group">
										<label for="email_title_registracija_title" class="col-sm-2 control-label"><span class="text-danger">*</span> Završena registracija kandidata:</label>
										<div class="col-sm-10">
											<div class="row">
												<div class="col-sm-12">
													<div class="materail-input-block materail-input-block_success">
														<input class="form-control materail-input" type="text" name="email_title_registracija_title" id="email_title_registracija_title" value="<?php echo $email_title; ?>" required>
														<span class="materail-input-block__line"></span>
													</div>
												</div>		
												<hr/>
												<div class="col-sm-12">
													<textarea name="email_title_registracija_txt" id="editor1" class="ckeditor" rows="20"><?php echo $email_txt; ?></textarea>
												<script>
													$('#editor1').trumbowyg({
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
										</div>
									</div>
									<br/>
									<div class="form-group">
										<div class="col-sm-12 text-right">
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