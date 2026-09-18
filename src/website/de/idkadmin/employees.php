<?php
	include("includes/functions.php");
	include("includes/common.php");

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
	<title><?php getTitle(); ?></title>

	<?php include('includes/head.php'); ?>

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
					<h1><i class="fa fa-users idk_color_green" aria-hidden="true"></i> Administratori</h1>
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
										echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novog administratora.</div>';
									}elseif($mess == 2){
										echo '<div class="alert material-alert material-alert_danger">Greška: Korisnik kojeg pokuštavate dodati već postoji u bazi podataka.</div>';
									}elseif($mess == 3){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste uredili profil administratora.</div>';
									}elseif($mess == 4){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste arhivirali profil administratora.</div>';
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
													{ "width": "55%" },
													{ "width": "30%" },
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
											<th>E-mail</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										<?php
											$query = $db->prepare("
															SELECT user_id, user_fullname, user_email, user_image
															FROM idk_users
															WHERE user_status != :user_status");

											$query->execute(array(
												':user_status' => 0));

											while($row = $query->fetch()){

												$user_id = $row['user_id'];
												$user_fullname = $row['user_fullname'];
												$user_email = $row['user_email'];

												if($row['user_image'] == NULL){
													$user_image = "none.jpg";
												}else{
													$user_image = $row['user_image'];
												}
										?>
										<tr>
											<td class="text-center"><img class="idk_profile_img" src="<?php getSiteURL(); ?>files/users/<?php echo $user_image; ?>"></td>
											<td><?php echo $user_fullname; ?></td>
											<td><a href="mailto:<?php echo $user_email; ?>"><?php echo $user_email; ?></a></td>
											<td class="text-center">
												<div class="btn-group material-btn-group">
													<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
													<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
														<li><a href="<?php getSiteURL(); ?>employees?page=edit&id=<?php echo $user_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Uredi</a></li>
														<li class="idk_dropdown_danger"><a href="#" data="<?php getSiteURL(); ?>employees?page=archive&id=<?php echo $user_id; ?>" data-toggle="modal" data-target="#archiveModal" class="archive material-dropdown-menu__link"><i class="fa fa-trash-o" aria-hidden="true"></i> Arhiviraj</a></li>
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
														<p>Jeste li sigurni da želite arhivirati administratora?</p>
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
		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-users idk_color_green" aria-hidden="true"></i> Dodaj novog administratora</h1>
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
										<label for="user_fullname" class="col-sm-3 control-label"><span class="text-danger">*</span> Ime i prezime:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="user_fullname" id="user_fullname" placeholder="Ime i prezime" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="user_email" class="col-sm-3 control-label"><span class="text-danger">*</span> Email:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="email" name="user_email" id="user_email" placeholder="Email" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="user_pass" class="col-sm-3 control-label"><span class="text-danger">*</span> Lozinka:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="password" name="user_pass" id="user_pass" placeholder="Lozinka" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="user_color" class="col-sm-3 control-label">Boja:</label>
										<div class="col-sm-1">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="color" name="user_color" id="user_color" placeholder="Boja">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="user_image" class="col-sm-3 control-label">Fotografija:</label>
										<div class="col-sm-9">
											<div class="fileinput fileinput-new" data-provides="fileinput">
												<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;"></div>
												<div>
													<span class="btn btn-default btn-file"><span class="fileinput-new">Izaberi fotografiju</span><span class="fileinput-exists">Promijeni</span><input type="file" name="user_image" id="user_image"></span>
													<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Ukloni</a>
													<script>
														$(function (){
															$('#user_image').change(function (){

																var ext = $('#user_image').val().split('.').pop().toLowerCase();

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
				break;

				case "edit":

						$user_id = $_GET['id'];

						$query = $db->prepare("
										SELECT user_fullname, user_email, user_color, user_image
										FROM idk_users
										WHERE user_id = :user_id");

						$query->execute(array(
									':user_id' => $user_id));

						$row = $query->fetch();

							$user_fullname = $row['user_fullname'];
							$user_email = $row['user_email'];
							$user_color = $row['user_color'];

							if($row['user_image'] == NULL){
								$user_image = "none.jpg";
							}else{
								$user_image = $row['user_image'];
							}

		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-users idk_color_green" aria-hidden="true"></i> Uredi profil adminstratora</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="employees?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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
									<input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
									<div class="form-group">
										<label for="user_fullname" class="col-sm-3 control-label"><span class="text-danger">*</span> Ime i prezime:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="user_fullname" id="user_fullname" value="<?php echo $user_fullname; ?>" placeholder="Ime i prezime" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="user_email" class="col-sm-3 control-label"><span class="text-danger">*</span> Email:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="email" name="user_email" id="user_email" value="<?php echo $user_email; ?>" placeholder="Email" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="user_pass" class="col-sm-3 control-label"><span class="text-danger">*</span> Lozinka:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="password" name="user_pass" id="user_pass" placeholder="Lozinka">
												<span class="materail-input-block__line"></span>
											</div>
											<small>Ukoliko želite promijeniti lozinku, unesite novu.</small>
										</div>
									</div>
									<div class="form-group">
										<label for="user_color" class="col-sm-3 control-label">Boja:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="color" name="user_color" id="user_color" value="<?php echo $user_color; ?>" />
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="user_image" class="col-sm-3 control-label">Fotografija:</label>
										<div class="col-sm-9">
											<div class="fileinput fileinput-new" data-provides="fileinput">
												<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;">
													<img src="<?php getSiteURL(); ?>files/users/<?php echo $user_image; ?>">
												</div>
												<input type="hidden" name="user_image_url" value="<?php echo $user_image; ?>" />
												<div>
													<span class="btn btn-default btn-file"><span class="fileinput-new">Izaberi fotografiju</span><span class="fileinput-exists">Promijeni</span><input type="file" name="user_image" id="user_image"></span>
													<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Ukloni</a>
													<script>
														$(function (){
															$('#user_image').change(function (){

																var ext = $('#user_image').val().split('.').pop().toLowerCase();

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

				case "edit_profile":

						$query = $db->prepare("
										SELECT user_fullname, user_email, user_color, user_image
										FROM idk_users
										WHERE user_id = :user_id");

						$query->execute(array(
									':user_id' => $logged_user_id));

						$row = $query->fetch();

							$user_fullname = $row['user_fullname'];
							$user_email = $row['user_email'];
							$user_color = $row['user_color'];

							if($row['user_image'] == "none" OR $row['user_image'] == NULL){
								$user_image = "none.jpg";
							}else{
								$user_image = $row['user_image'];
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
									<input type="hidden" name="user_id" value="<?php echo $logged_user_id; ?>" />
									<div class="form-group">
										<label for="user_fullname" class="col-sm-3 control-label"><span class="text-danger">*</span> Ime i prezime:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="user_fullname" id="user_fullname" value="<?php echo $user_fullname; ?>" placeholder="Ime i prezime" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="user_email" class="col-sm-3 control-label"><span class="text-danger">*</span> Email:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="email" name="user_email" id="user_email" value="<?php echo $user_email; ?>" placeholder="Email" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="user_color" class="col-sm-3 control-label">Boja:</label>
										<div class="col-sm-1">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="color" name="user_color" id="user_color" value="<?php echo $user_color; ?>" placeholder="Boja">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="user_pass" class="col-sm-3 control-label">Lozinka:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="password" name="user_pass" id="user_pass" placeholder="Lozinka">
												<span class="materail-input-block__line"></span>
											</div>
											<small>Ukoliko želite promijeniti lozinku, unesite novu.</small>
										</div>
									</div>
									<div class="form-group">
										<label for="user_image" class="col-sm-3 control-label">Fotografija:</label>
										<div class="col-sm-9">
											<div class="fileinput fileinput-new" data-provides="fileinput">
												<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;">
													<img src="<?php getSiteURL(); ?>files/users/<?php echo $user_image; ?>">
												</div>
												<input type="hidden" name="user_image_url" value="<?php echo $user_image; ?>" />
												<div>
													<span class="btn btn-default btn-file"><span class="fileinput-new">Izaberi fotografiju</span><span class="fileinput-exists">Promijeni</span><input type="file" name="user_image" id="user_image"></span>
													<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Ukloni</a>
													<script>
														$(function (){
															$('#user_image').change(function (){

																var ext = $('#user_image').val().split('.').pop().toLowerCase();

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

				case "archive":

						$user_id = $_GET['id'];

						//Get
						$query_select = $db->prepare("
												SELECT user_fullname
												FROM idk_users
												WHERE user_id = :user_id");

						$query_select->execute(array(
											':user_id' => $user_id));

						$row_select = $query_select->fetch();

						$user_fullname = $row_select['user_fullname'];

						//Save
						$query = $db->prepare("
										UPDATE idk_users
										SET user_status = :user_status
										WHERE user_id = :user_id");

						$query->execute(array(
									':user_status' => 0,
									':user_id' => $user_id));

						//Add to LOGS
						$log_desc = "Arhivirao administratora: " . $user_fullname . " ";
						$log_date = date('Y-m-d H:i:s');

						$log_query = $db->prepare("
										INSERT INTO idk_logs
											(log_employeeid, log_desc, log_date)
										VALUES
											(:log_employeeid, :log_desc, :log_date)");

						$log_query->execute(array(
										':log_employeeid' => $logged_user_id,
										':log_desc' => $log_desc,
										':log_date' => $log_date));


						header("Location: " . getSiteURLr() . "employees?page=list&mess=4");

				break;

			}
		?>
			<footer><?php getCopyright(); ?></footer>
		</div>
	</div>
</body>
</html>
