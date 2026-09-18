<?php
	include("includes/functions.php");
	include("includes/common.php");

	$getEmployeeStatus = explode( ',' , getEmployeeStatus());

	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: contacts?page=list");
	}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php getTitle(); ?></title>

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
					<h1><i class="fa fa-address-card-o idk_color_green" aria-hidden="true"></i> Kontakti</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>contacts?page=add" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj</span></a>
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
										echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novi kontakt.</div>';
									}elseif($mess == 3){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste arhivirali kontakt profil.</div>';
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
													{ "width": "25%" },
													{ "width": "15%" },
													{ "width": "15%" },
													{ "width": "20%" },
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
											<th>Ime i prezime</th>
											<th>Grad</th>
											<th>Telefon</th>
											<th>E-mail</th>
											<th class="text-center">Vrsta</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										<?php
											$query = $db->prepare("
															SELECT contact_id, contact_firstname, contact_lastname, contact_city, contact_image, contact_type
															FROM idk_contacts
															WHERE contact_status != :contact_status");

											$query->execute(array(':contact_status' => 0));

											while($row = $query->fetch()){

												$contact_id = $row['contact_id'];
												$contact_firstname = $row['contact_firstname'];
												$contact_lastname = $row['contact_lastname'];
												$contact_city = $row['contact_city'];
												$contact_type = $row['contact_type'];

												if($row['contact_image'] == "none"){
													$contact_image = "none.jpg";
												}else{
													$contact_image = $row['contact_image'];
												}

												//Get primary phone
												$query_phone = $db->prepare("
																	SELECT ci_data
																	FROM idk_contacts_info
																	WHERE ci_group = :ci_group AND ci_primary = :ci_primary AND ci_contactid = :ci_contactid");

												$query_phone->execute(array(
													':ci_group' => 1,
													':ci_primary' => 1,
													':ci_contactid' => $contact_id));

												$row_phone = $query_phone->fetch();

												$contact_phone = $row_phone['ci_data'];

												//Get primary email
												$query_email = $db->prepare("
																	SELECT ci_data
																	FROM idk_contacts_info
																	WHERE ci_group = :ci_group AND ci_primary = :ci_primary AND ci_contactid = :ci_contactid");

												$query_email->execute(array(
													':ci_group' => 2,
													':ci_primary' => 1,
													':ci_contactid' => $contact_id));

												$row_email = $query_email->fetch();

												$contact_email = $row_email['ci_data'];
										?>
										<tr>
											<td class="text-center"><a href="<?php getSiteURL(); ?>contacts?page=open&id=<?php echo $contact_id; ?>"><img class="idk_profile_img" src="<?php getSiteURL(); ?>files/contacts/<?php echo $contact_image; ?>"></a></td>
											<td><a href="<?php getSiteURL(); ?>contacts?page=open&id=<?php echo $contact_id; ?>"><?php echo $contact_firstname; ?> <?php echo $contact_lastname; ?></a></td>
											<td><?php echo $contact_city; ?></td>
											<td><a href="tel:<?php echo $contact_phone; ?>"><?php echo $contact_phone; ?></a></td>
											<td><a href="mailto:<?php echo $contact_email; ?>"><?php echo $contact_email; ?></a></td>
											<td class="text-center"><?php echo $contact_type; ?></td>
											<td class="text-center">
												<div class="btn-group material-btn-group">
													<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
													<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
														<li><a href="<?php getSiteURL(); ?>contacts?page=open&id=<?php echo $contact_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori</a></li>
														<li><a href="<?php getSiteURL(); ?>contacts?page=edit&id=<?php echo $contact_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Uredi</a></li>
														<li class="idk_dropdown_danger"><a href="#" data="<?php getSiteURL(); ?>contacts?page=archive&id=<?php echo $contact_id; ?>" data-toggle="modal" data-target="#archiveModal" class="archive material-dropdown-menu__link"><i class="fa fa-trash-o" aria-hidden="true"></i> Arhiviraj</a></li>
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
														<p>Jeste li sigurni da želite arhivirati kontakt profil?</p>
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
					if((in_array( "1" , $getEmployeeStatus)) OR (in_array( "2" , $getEmployeeStatus))){
					//if((in_array( "1" , $getEmployeeStatus) OR (in_array( "2" , $getEmployeeStatus)){
						//if($getEmployeeStatus == 1 OR $getEmployeeStatus == 2){

						if(isset($_GET['company'])) {
							$companyid = $_GET['company'];
						}else{
							$companyid = 0;
						}
		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-address-card-o idk_color_green" aria-hidden="true"></i> Dodaj novi kontakt</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>contacts?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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
								<form id="idk_form" action="<?php getSiteURL(); ?>do.php?form=add_contact" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<div class="form-group">
										<label for="contact_firstname" class="col-sm-3 control-label"><span class="text-danger">*</span> Ime:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="contact_firstname" id="contact_firstname" placeholder="Ime" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="contact_lastname" class="col-sm-3 control-label"><span class="text-danger">*</span> Prezime:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="contact_lastname" id="contact_lastname" placeholder="Prezime" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="contact_companyid" class="col-sm-3 control-label">Kompanija:</label>
										<div class="col-sm-9">
											<select class="selectpicker" id="contact_companyid" data-live-search="true" name="contact_companyid">
												<option value="0">Samostalni kontakt</option>
												<?php
													$select_query = $db->prepare("
																		SELECT company_id, company_name, company_type
																		FROM idk_companies
																		WHERE company_status != :company_status");

													$select_query->execute(array(
																	':company_status' => 0));

													while($select_row = $select_query->fetch()) {
														if($companyid == $select_row['company_id']){ $selected = "selected"; }else{ $selected = ""; }
														echo "<option value='" . $select_row['company_id'] . "' " . $selected . ">" . $select_row['company_name'] . " " . $select_row['company_type'] . "</option>";
													}
												?>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label for="contact_phone" class="col-sm-3 control-label">Primarni telefon:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="contact_phone" id="contact_phone" placeholder="Primarni telefon" />
												<span class="materail-input-block__line"></span>
											</div>
										</div>
                                        <script>$('#contact_phone').mask("000000000000000000", {placeholder: "00387XXXXXXXXXX"});</script>
									</div>
									<div class="form-group">
										<label for="contact_email" class="col-sm-3 control-label">Primarni email:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="email" name="contact_email" id="contact_email" placeholder="Primarni email" />
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="contact_nickname" class="col-sm-3 control-label">Nadimak:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="contact_nickname" id="contact_nickname" placeholder="Nadimak" />
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="contact_dob" class="col-sm-3 control-label">Datum rođenja:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" data-init-set="false" data-large-default="true" data-format="d.m.Y." data-large-mode="true" data-modal="true" type="text" name="contact_dob" id="contact_dob" placeholder="Datum rođenja">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
										<script>
											$( function() {	$( "#contact_dob" ).dateDropper(); } );
										</script>
									</div>
									<div class="form-group">
										<label for="contact_address" class="col-sm-3 control-label">Adresa: </label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="contact_address" id="contact_address" placeholder="Adresa">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
                                    <div class="form-group">
										<label for="contact_zipcode" class="col-sm-3 control-label">Poštanski broj: </label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="contact_zipcode" id="contact_zipcode" placeholder="Poštanski broj">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="contact_city" class="col-sm-3 control-label">Grad:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<script>
												  $( function() {
												    var availableTags = [
														<?php

															$select_query = $db->prepare("
																				SELECT contact_city
																				FROM idk_contacts
																				GROUP BY contact_city");

															$select_query->execute();

															while($select_row = $select_query->fetch()) {
																echo '"' . $select_row['contact_city'] . '",';
															}

														 ?>
												    ];
												    $( "#contact_city" ).autocomplete({
												      source: availableTags
												    });
												  } );
												  </script>
												<input class="form-control materail-input" type="text" name="contact_city" id="contact_city" placeholder="Grad">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="contact_state" class="col-sm-3 control-label">Regija:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<script>
												  $( function() {
												    var availableTags = [
														<?php

															$select_query = $db->prepare("
																				SELECT contact_state
																				FROM idk_contacts
																				GROUP BY contact_state");

															$select_query->execute();

															while($select_row = $select_query->fetch()) {
																echo '"' . $select_row['contact_state'] . '",';
															}

														 ?>
												    ];
												    $( "#contact_state" ).autocomplete({
												      source: availableTags
												    });
												  } );
												  </script>
												<input class="form-control materail-input" type="text" name="contact_state" id="contact_state" placeholder="Regija">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="contact_country" class="col-sm-3 control-label">Država:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<script>
												  $( function() {
												    var availableTags = [
														<?php

															$select_query = $db->prepare("
																				SELECT contact_country
																				FROM idk_contacts
																				GROUP BY contact_country");

															$select_query->execute();

															while($select_row = $select_query->fetch()) {
																echo '"' . $select_row['contact_country'] . '",';
															}

														 ?>
												    ];
												    $( "#contact_country" ).autocomplete({
												      source: availableTags
												    });
												  } );
												  </script>
												<input class="form-control materail-input" type="text" name="contact_country" id="contact_country" placeholder="Država">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="contact_info" class="col-sm-3 control-label">Ostale informacije:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="contact_info" id="contact_info" placeholder="Ostale informacije">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="contact_type" class="col-sm-3 control-label">Vrsta kontakta:</label>
										<div class="col-sm-9">
											<select class="selectpicker" id="contact_type" name="contact_type">
												<option value="Ostalo">Ostalo</option>
												<?php
													$select_query = $db->prepare("
																		SELECT otherdata_data
																		FROM idk_otherdata
																		WHERE otherdata_group = :otherdata_group");

													$select_query->execute(array(
																	':otherdata_group' => 2));

													while($select_row = $select_query->fetch()) {
														echo "<option value='" . $select_row['otherdata_data'] . "'>" . $select_row['otherdata_data'] . "</option>";
													}
												?>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label for="contact_image" class="col-sm-3 control-label">Fotografija:</label>
										<div class="col-sm-9">
											<div class="fileinput fileinput-new" data-provides="fileinput">
												<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;"></div>
												<div>
													<span class="btn btn-default btn-file"><span class="fileinput-new">Izaberi fotografiju</span><span class="fileinput-exists">Promijeni</span><input type="file" name="contact_image" id="contact_image"></span>
													<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Ukloni</a>
													<script>
														$(function (){
															$('#contact_image').change(function (){

																var ext = $('#contact_image').val().split('.').pop().toLowerCase();

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
											<ul class="list-inline">
												<li class="hidden"><i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i></li>
												<li>
													<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj</span></button>
												</li>
											</ul>
											<small>Sva polja označena sa <span class="text-danger">*</span> su obavezna!</small>
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
					if($getEmployeeStatus == 1 OR $getEmployeeStatus == 2){

						$contact_id = $_GET['id'];

						$query = $db->prepare("
										SELECT contact_firstname, contact_lastname, contact_companyid, contact_nickname, contact_dob, contact_address, contact_zipcode, contact_city, contact_state, contact_country, contact_info, contact_image, contact_type
										FROM idk_contacts
										WHERE contact_id = :contact_id");

						$query->execute(array(
									':contact_id' => $contact_id));

						$row = $query->fetch();

							$contact_firstname = $row['contact_firstname'];
							$contact_lastname = $row['contact_lastname'];
							$contact_companyid = $row['contact_companyid'];
							$contact_nickname = $row['contact_nickname'];
							$contact_dob = date('d.m.Y.', strtotime($row['contact_dob']));
							$contact_address = $row['contact_address'];
							$contact_zipcode = $row['contact_zipcode'];
							$contact_city = $row['contact_city'];
							$contact_state = $row['contact_state'];
							$contact_country = $row['contact_country'];
							$contact_info = $row['contact_info'];
							$contact_type = $row['contact_type'];

							if($row['contact_image'] == "none"){
								$contact_image = "none.jpg";
							}else{
								$contact_image = $row['contact_image'];
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
								<form id="idk_form" action="<?php getSiteURL(); ?>do.php?form=edit_contact" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<input type="hidden" name="contact_id" value="<?php echo $contact_id; ?>" />
									<div class="form-group">
										<label for="contact_firstname" class="col-sm-3 control-label"><span class="text-danger">*</span> Ime:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="contact_firstname" id="contact_firstname" value="<?php echo $contact_firstname; ?>" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="contact_lastname" class="col-sm-3 control-label"><span class="text-danger">*</span> Prezime:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="contact_lastname" id="contact_lastname" value="<?php echo $contact_lastname; ?>" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="contact_companyid" class="col-sm-3 control-label">Kompanija:</label>
										<div class="col-sm-9">
											<select class="selectpicker" id="contact_companyid" data-live-search="true" name="contact_companyid">
												<option value="0">Samostalni kontakt</option>
												<?php
													$select_query = $db->prepare("
																		SELECT company_id, company_name, company_type
																		FROM idk_companies
																		WHERE company_status != :company_status");

													$select_query->execute(array(
																	':company_status' => 0));

													while($select_row = $select_query->fetch()) {
														if($contact_companyid == $select_row['company_id']){ $selected = "selected"; }else{ $selected = ""; }
														echo "<option value='" . $select_row['company_id'] . "' " . $selected . ">" . $select_row['company_name'] . " " . $select_row['company_type'] . "</option>";
													}
												?>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label for="contact_nickname" class="col-sm-3 control-label">Nadimak:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="contact_nickname" id="contact_nickname" value="<?php echo $contact_nickname; ?>" />
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="contact_dob" class="col-sm-3 control-label">Datum rođenja:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" data-init-set="false" data-large-default="true" data-format="d.m.Y." data-large-mode="true" data-modal="true" type="text" name="contact_dob" id="contact_dob" value="<?php echo $contact_dob; ?>">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
										<script>
											$( function() {	$( "#contact_dob" ).dateDropper(); } );
										</script>
									</div>
									<div class="form-group">
										<label for="contact_address" class="col-sm-3 control-label">Adresa: </label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="contact_address" id="contact_address" value="<?php echo $contact_address; ?>">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
                                    <div class="form-group">
										<label for="contact_zipcode" class="col-sm-3 control-label">Poštanski broj: </label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="contact_zipcode" id="contact_zipcode" value="<?php echo $contact_zipcode; ?>">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="contact_city" class="col-sm-3 control-label">Grad:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="contact_city" id="contact_city" value="<?php echo $contact_city; ?>">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="contact_state" class="col-sm-3 control-label">Regija:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="contact_state" id="contact_state" value="<?php echo $contact_state; ?>">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="contact_country" class="col-sm-3 control-label">Država:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="contact_country" id="contact_country" value="<?php echo $contact_country; ?>">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="contact_info" class="col-sm-3 control-label">Ostale informacije:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="contact_info" id="contact_info" value="<?php echo $contact_info; ?>">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="contact_type" class="col-sm-3 control-label">Vrsta kontakta:</label>
										<div class="col-sm-9">
											<select class="selectpicker" id="contact_type" name="contact_type">
												<option value="Ostalo">Ostalo</option>
												<?php
													$select_query = $db->prepare("
																		SELECT otherdata_data
																		FROM idk_otherdata
																		WHERE otherdata_group = :otherdata_group");

													$select_query->execute(array(
																	':otherdata_group' => 2));

													while($select_row = $select_query->fetch()) {
														if($contact_type == $select_row['otherdata_data']){ $selected = "selected"; }else{ $selected = ""; }
														echo "<option value='" . $select_row['otherdata_data'] . "' " . $selected . ">" . $select_row['otherdata_data'] . "</option>";
													}
												?>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label for="contact_image" class="col-sm-3 control-label">Fotografija:</label>
										<div class="col-sm-9">
											<div class="fileinput fileinput-new" data-provides="fileinput">
												<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;">
													<img src="<?php getSiteURL(); ?>files/contacts/<?php echo $contact_image; ?>">
												</div>
												<input type="hidden" name="contact_image_url" value="<?php echo $contact_image; ?>" />
												<div>
													<span class="btn btn-default btn-file"><span class="fileinput-new">Izaberi fotografiju</span><span class="fileinput-exists">Promijeni</span><input type="file" name="contact_image" id="contact_image"></span>
													<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Ukloni</a>
													<script>
														$(function (){
															$('#contact_image').change(function (){

																var ext = $('#contact_image').val().split('.').pop().toLowerCase();

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
											<ul class="list-inline">
												<li class="hidden"><i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i></li>
												<li><button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-save" aria-hidden="true"></i> <span>Snimi</span></button></li>
											</ul>
											<small>Sva polja označena sa <span class="text-danger">*</span> su obavezna!</small>
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

					$contact_id = $_GET['id'];

					$query = $db->prepare("
									SELECT contact_firstname, contact_lastname, contact_nickname, contact_dob, contact_address, contact_zipcode, contact_city, contact_state, contact_country, contact_info, contact_inote, contact_image, contact_type, contact_status, contact_companyid, company_name, company_type
									FROM idk_contacts
									LEFT JOIN idk_companies ON idk_contacts.contact_companyid = idk_companies.company_id
									WHERE contact_id = :contact_id");

					$query->execute(array(
								':contact_id' => $contact_id));

					$row = $query->fetch();

						$contact_firstname = $row['contact_firstname'];
						$contact_lastname = $row['contact_lastname'];
						$contact_nickname = $row['contact_nickname'];
						$contact_dob = date('d.m.Y.', strtotime($row['contact_dob']));
						$contact_address = $row['contact_address'];
						$contact_zipcode = $row['contact_zipcode'];
						$contact_city = $row['contact_city'];
						$contact_state = $row['contact_state'];
						$contact_country = $row['contact_country'];
						$contact_info = $row['contact_info'];
						$contact_inote = $row['contact_inote'];
						$contact_type = $row['contact_type'];
						$contact_status = $row['contact_status'];
						$contact_companyid = $row['contact_companyid'];
						$company_name = $row['company_name'];
						$company_type = $row['company_type'];

						if($row['contact_image'] == "none"){
							$contact_image = "none.jpg";
						}else{
							$contact_image = $row['contact_image'];
						}

						if($row['contact_status'] == 0){
							$contact_status = "Deaktiviran";
						}elseif($row['contact_status'] == 1){
							$contact_status = "Aktivan";
						}
		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><a class="fancybox" rel="group" href="<?php getSiteURL(); ?>files/contacts/<?php echo $contact_image; ?>"><img class="idk_profile_img" src="<?php getSiteURL(); ?>files/contacts/<?php echo $contact_image; ?>"></a> <?php echo $contact_firstname; ?> <?php echo $contact_lastname; ?></h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>contacts?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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
									}elseif($mess == 6){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste dodali novu aktivnost.</div>
										<script>$(function() { $('[href="#timeline"]').tab('show'); });</script>
									<?php
									}elseif($mess == 7){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste obrisali aktivnost.</div>
										<script>$(function() { $('[href="#timeline"]').tab('show'); });</script>
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
								<li class="active"><a href="#timeline" class="material-tabs__tab-link" data-toggle="tab">Aktivnosti</a></li>
								<li><a href="#tasks" class="material-tabs__tab-link" data-toggle="tab">Zadaci</a></li>
								<li><a href="#info" class="material-tabs__tab-link" data-toggle="tab">Informacije</a></li>
								<li><a href="#notes" class="material-tabs__tab-link" data-toggle="tab">Bilješke</a></li>
								<li><a href="#documents" class="material-tabs__tab-link" data-toggle="tab">Dokumenti</a></li>
								<li><a href="#important" class="material-tabs__tab-link" data-toggle="tab">Važno</a></li>
							</ul>
							<div class="tab-content materail-tabs-content">
								<div class="tab-pane fade active in" id="timeline">
									<form id="idk_form" action="<?php getSiteURL(); ?>do.php?form=add_timeline" method="post" class="form-horizontal" role="form">
										<input type="hidden" name="timeline_dataid" value="<?php echo $contact_id; ?>">
										<div class="row">
											<div class="col-md-3 idk_custom_radio">
												<div class="col-xs-12">
													<div class="form-group">
													    <label for="task_date"><strong>Vrsta aktivnosti:</strong></label>
														<ul class="list-inline">
															<li>
																<label>
																	<input type="radio" name="timeline_type" value="1" />
																	<i class="fa fa-phone fa-2x" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Poziv"></i>
																</label>
															</li>
															<li>
																<label>
																	<input type="radio" name="timeline_type" value="2" />
																	<i class="fa fa-envelope fa-2x" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Email"></i>
																</label>
															</li>
															<li>
																<label>
																	<input type="radio" name="timeline_type" value="3" />
																	<i class="fa fa-users fa-2x" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Sastanak"></i>
																</label>
															</li>
															<li>
																<label>
																	<input type="radio" name="timeline_type" value="4" checked>
																	<i class="fa fa-sticky-note fa-2x" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Ostalo"></i>
																</label>
															</li>
														</ul>
													 </div>
												</div>
												<div class="col-xs-12">
													<div class="form-group">
													    <label for="task_date"><strong>Datum i vrijeme aktivnosti:</strong></label>
														<div class="row">
															<div class="col-sm-6">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input" data-init-set="false" data-large-default="true" data-format="d.m.Y." data-large-mode="true" data-modal="true" type="text" name="timeline_date" id="timeline_date" value="<?php echo date('d.m.Y.', time()); ?>">
																	<span class="materail-input-block__line"></span>
																</div>
																<script>
																	$( function() {	$( "#timeline_date" ).dateDropper(); });
																</script>
															</div>
															<div class="col-sm-6">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input" type="text" name="timeline_time" id="timeline_time">
																	<span class="materail-input-block__line"></span>
																</div>
																<script>
																	$('#timeline_time').timeDropper({ format:'H:mm', setCurrentTime:false });
																</script>
															</div>
														</div>
													 </div>
												</div>
											</div>
											<div class="col-md-9 idk_custom_textare">
												<div class="col-xs-12">
													<textarea id="timeline_txt" class="form-control materail-input material-textarea" name="timeline_txt" placeholder="Opiši aktivnost ..." rows="8" required></textarea>
												</div>
												<script>
													$('#timeline_txt').trumbowyg({
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
													$query_timeline = $db->prepare("
																			SELECT timeline_id, timeline_type, timeline_txt, timeline_datetime, employee_firstname, employee_lastname
																			FROM idk_timeline
																			INNER JOIN idk_employees ON idk_timeline.timeline_employeeid = idk_employees.employee_id
																			WHERE timeline_dataid = :timeline_dataid AND timeline_group = :timeline_group
																			ORDER BY timeline_datetime DESC, timeline_id DESC");

													$query_timeline->execute(array(
																	':timeline_dataid' => $contact_id,
																	':timeline_group' => 1));

													while($row_timeline = $query_timeline->fetch()){

														$timeline_id = $row_timeline['timeline_id'];
														$employee_firstname = $row_timeline['employee_firstname'];
														$employee_lastname = $row_timeline['employee_lastname'];
														$timeline_txt = $row_timeline['timeline_txt'];
														$timeline_datetime_format = date('d.m.Y. - H:i', strtotime($row_timeline['timeline_datetime']));
														$timeline_datetime = date('Y-m-d H:i:s', strtotime($row_timeline['timeline_datetime']));

														if($row_timeline['timeline_type'] == 1){
															$timeline_type = '<i class="fa fa-phone" aria-hidden="true"></i>';
														}elseif($row_timeline['timeline_type'] == 2){
															$timeline_type = '<i class="fa fa-envelope" aria-hidden="true"></i>';
														}elseif($row_timeline['timeline_type'] == 3){
															$timeline_type = '<i class="fa fa-users" aria-hidden="true"></i>';
														}elseif($row_timeline['timeline_type'] == 4){
															$timeline_type = '<i class="fa fa-sticky-note" aria-hidden="true"></i>';
														}
												?>
												<li>
													<time class="cbp_tmtime"><span><?php echo $employee_firstname; ?> <?php echo $employee_lastname; ?></span> <span class="timeago" datetime="<?php echo $timeline_datetime; ?>" data-toggle="tooltip" data-placement="bottom" title="<?php echo $timeline_datetime_format; ?>"></span></time>
													<div class="cbp_tmicon"><?php echo $timeline_type; ?></div>
													<div class="cbp_tmlabel">
														<div class="row">
															<div class="col-sm-11">
																<?php echo $timeline_txt; ?>
															</div>
															<div class="col-sm-1 text-right">
																<a href="#" data="<?php getSiteURL(); ?>contacts?page=del_timeline&id=<?php echo $timeline_id; ?>" data-toggle="modal" data-target="#deleteTimelineModal" class="delete_timeline btn material-btn material-btn_danger main-container__column"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
																<script>
																	$(".delete_timeline").click(function () {
																		var addressValue = $(this).attr("data");
																		document.getElementById("delete_timeline_link").href = addressValue;
																	});
																</script>
																<!-- Modal -->
																<div class="modal material-modal material-modal_danger fade text-left" id="deleteTimelineModal">
																	<div class="modal-dialog">
																		<div class="modal-content material-modal__content">
																			<div class="modal-header material-modal__header">
																				<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																				<h4 class="modal-title material-modal__title">Brisanje</h4>
																			</div>
																			<div class="modal-body material-modal__body">
																				<p>Jeste li sigurni da želite obrisati aktivnost?</p>
																			</div>
																			<div class="modal-footer material-modal__footer">
																				<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
																				<a id="delete_timeline_link" href=""><button class="btn btn-primary material-btn material-btn_danger">OBRIŠI</button></a>
																			</div>
																		</div>
																	</div>
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
								<div class="tab-pane fade" id="tasks">
									<form id="idk_form" action="<?php getSiteURL(); ?>do.php?form=add_task" method="post" class="form-horizontal" role="form">
										<input type="hidden" name="task_dataid" value="<?php echo $contact_id; ?>">
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
																	$( function() {	$( "#task_duedate" ).dateDropper(); });
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
													    <label for="task_assignedid"><strong>Dodijeli zadatak:</strong></label>
														<select class="selectpicker" id="task_assignedid" name="task_assignedid" data-live-search="true" required>
															<option value=""></option>
															<?php
																$select_query = $db->prepare("
																					SELECT employee_id, employee_firstname, employee_lastname
																					FROM idk_employees
																					WHERE employee_status != :employee_status");
																$select_query->execute(array(
																				':employee_status' => 0));
																while($select_row = $select_query->fetch()) {
																	echo "<option value='" . $select_row['employee_id'] . "'>" . $select_row['employee_firstname'] . " " . $select_row['employee_lastname'] . "</option>";
																}
															?>
														</select>
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
																			ORDER BY task_status ASC, task_duedatetime DESC, task_id DESC");

													$query_tasks->execute(array(
																	':task_dataid' => $contact_id,
																	':task_group' => 1));

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
																						<form action="<?php getSiteURL(); ?>do.php?form=add_task_done" method="post" role="form" class="form-horizontal">
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
																						<form action="<?php getSiteURL(); ?>do.php?form=add_task_notdone" method="post" role="form" class="form-horizontal">
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
																		<a href="#" data="<?php getSiteURL(); ?>contacts?page=del_task&id=<?php echo $task_id; ?>" data-toggle="modal" data-target="#deleteTaskModal" class="delete_task btn material-btn material-btn_danger main-container__column"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
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
																						<form action="<?php getSiteURL(); ?>do.php?form=add_task_emailnotifi" method="post" role="form" class="form-horizontal">
																							<input type="hidden" name="task_id" id="task_id_emailnotifi" value="" />
																							<input type="hidden" name="task_dataid" value="<?php echo $contact_id; ?>" />
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
																						<form action="<?php getSiteURL(); ?>do.php?form=add_task_repeat" method="post" role="form" class="form-horizontal">
																							<input type="hidden" name="task_id" id="task_id_repeat" value="" />
																							<input type="hidden" name="task_dataid" value="<?php echo $contact_id; ?>" />
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
								<div class="tab-pane fade" id="info">
									<div class="row idk_employee_info">
										<div class="col-md-6">
											<div class="row">
												<div class="col-sm-9">
													<h5>Osnovne informacije</h5>
												</div>
												<div class="col-sm-3 text-right">
													<a href="contacts?page=edit&id=<?php echo $contact_id; ?>" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-pencil" aria-hidden="true"></i> <span></span></a>
												</div>
											</div>

											<div class="row">
												<strong class="col-sm-4 text-right">Ime:</strong>
												<div class="col-sm-8"><?php echo $contact_firstname; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Prezime:</strong>
												<div class="col-sm-8"><?php echo $contact_lastname; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Kompanija:</strong>
												<div class="col-sm-8"><?php if($contact_companyid != 0){ echo '<a href="' . getSiteUrlr() . 'companies?page=open&id=' . $contact_companyid . '">' . $company_name . ' ' . $company_type . '</a>'; }else{ echo "Samostalni kontakt"; } ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Nadimak:</strong>
												<div class="col-sm-8"><?php echo $contact_nickname; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Datum rođenja:</strong>
												<div class="col-sm-8"><?php if(!is_null($row['contact_dob'])){ echo $contact_dob; } ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Adresa:</strong>
												<div class="col-sm-8"><?php echo $contact_address; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Poštanski broj:</strong>
												<div class="col-sm-8"><?php echo $contact_zipcode; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Grad:</strong>
												<div class="col-sm-8"><?php echo $contact_city; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Regija:</strong>
												<div class="col-sm-8"><?php echo $contact_state; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Država:</strong>
												<div class="col-sm-8"><?php echo $contact_country; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Status:</strong>
												<div class="col-sm-8"><?php echo $contact_status; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Ostale informacije:</strong>
												<div class="col-sm-8"><?php echo $contact_info; ?></div>
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
																	<form action="<?php getSiteURL(); ?>do.php?form=add_contact_phone" method="post" role="form" class="form-horizontal">
																		<input type="hidden" name="ci_contactid" value="<?php echo $contact_id; ?>" />
																		<div class="form-group">
																			<label for="ci_title" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv:</label>
																			<div class="col-sm-9">
																				<div class="materail-input-block materail-input-block_success">
																					<input class="form-control materail-input" type="text" name="ci_title" id="ci_title" placeholder="Mobilni" required>
																					<span class="materail-input-block__line"></span>
																				</div>
																			</div>
																		</div>
																		<div class="form-group">
																			<label for="ci_data" class="col-sm-3 control-label"><span class="text-danger">*</span> Broj telefona:</label>
																			<div class="col-sm-9">
																				<div class="materail-input-block materail-input-block_success">
																					<input class="form-control materail-input" type="text" name="ci_data" id="ci_data" placeholder="003876XXXXXXXX" required>
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
																				SELECT ci_id, ci_title, ci_data, ci_primary
																				FROM idk_contacts_info
																				WHERE ci_group = :ci_group AND ci_contactid = :ci_contactid
																				ORDER BY ci_primary DESC");

															$query_phones->execute(array(
																	':ci_group' => 1,
																	':ci_contactid' => $contact_id));

															while($row_phones = $query_phones->fetch()){

																$ci_id = $row_phones['ci_id'];
																$ci_title = $row_phones['ci_title'];
																$ci_data = $row_phones['ci_data'];
																$ci_primary = $row_phones['ci_primary'];
														?>
														<tr>
															<td><?php echo $ci_title; ?>:</td>
															<td><a href="tel:<?php echo $ci_data; ?>"><?php echo $ci_data; ?></a></td>
															<td class="text-right">
																<ul class="list-inline">
																	<?php if($ci_primary == 1){ echo '<li data-toggle="tooltip" data-placement="top" title="Primarni"><i class="fa fa-star fa-lg text-success" aria-hidden="true"></i></li>'; }else{ echo '<li data-toggle="tooltip" data-placement="top" title="Postavi kao primarni"><a href="' . getSiteURLr() . 'do.php?form=set_primary_phone&ci_id=' . $ci_id . '&contact_id=' . $contact_id  . '"><i class="fa fa-star-o fa-lg text-success" aria-hidden="true"></i></a></li>'; } ?>
																	<li data-toggle="tooltip" data-placement="top" title="Obriši"><a href="#" data="<?php getSiteURL(); ?>contacts?page=del_contact_info&id=<?php echo $ci_id; ?>" data-toggle="modal" data-target="#delPhoneModal" class="delPhone"><i class="fa fa-trash-o fa-lg text-danger" aria-hidden="true"></i></a></li>
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
																	<form action="<?php getSiteURL(); ?>do.php?form=add_contact_email" method="post" role="form" class="form-horizontal">
																		<input type="hidden" name="ci_contactid" value="<?php echo $contact_id; ?>" />
																		<div class="form-group">
																			<label for="ci_title" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv:</label>
																			<div class="col-sm-9">
																				<div class="materail-input-block materail-input-block_success">
																					<input class="form-control materail-input" type="text" name="ci_title" id="ci_title" placeholder="Privatni" required>
																					<span class="materail-input-block__line"></span>
																				</div>
																			</div>
																		</div>
																		<div class="form-group">
																			<label for="ci_data" class="col-sm-3 control-label"><span class="text-danger">*</span> Email adresa:</label>
																			<div class="col-sm-9">
																				<div class="materail-input-block materail-input-block_success">
																					<input class="form-control materail-input" type="email" name="ci_data" id="ci_data" placeholder="info@primjer.com" required>
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
																				SELECT ci_id, ci_title, ci_data, ci_primary
																				FROM idk_contacts_info
																				WHERE ci_group = :ci_group AND ci_contactid = :ci_contactid
																				ORDER BY ci_primary DESC");

															$query_phones->execute(array(
																	':ci_group' => 2,
																	':ci_contactid' => $contact_id));

															while($row_phones = $query_phones->fetch()){

																$ci_id = $row_phones['ci_id'];
																$ci_title = $row_phones['ci_title'];
																$ci_data = $row_phones['ci_data'];
																$ci_primary = $row_phones['ci_primary'];
														?>
														<tr>
															<td><?php echo $ci_title; ?>:</td>
															<td><a href="mailto:<?php echo $ci_data; ?>"><?php echo $ci_data; ?></a></td>
															<td class="text-right">
																<ul class="list-inline">
																	<?php if($ci_primary == 1){ echo '<li data-toggle="tooltip" data-placement="top" title="Primarni"><i class="fa fa-star fa-lg text-success" aria-hidden="true"></i></li>'; }else{ echo '<li data-toggle="tooltip" data-placement="top" title="Postavi kao primarni"><a href="' . getSiteURLr() . 'do.php?form=set_primary_email&ci_id=' . $ci_id . '&contact_id=' . $contact_id  . '"><i class="fa fa-star-o fa-lg text-success" aria-hidden="true"></i></a></li>'; } ?>
																	<li data-toggle="tooltip" data-placement="top" title="Obriši"><a href="#" data="<?php getSiteURL(); ?>contacts?page=del_contact_info&id=<?php echo $ci_id; ?>" data-toggle="modal" data-target="#delEmailModal" class="delEmail"><i class="fa fa-trash-o fa-lg text-danger" aria-hidden="true"></i></a></li>
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
																	<form action="<?php getSiteURL(); ?>do.php?form=add_contact_other" method="post" role="form" class="form-horizontal">
																		<input type="hidden" name="ci_contactid" value="<?php echo $contact_id; ?>" />
																		<div class="form-group">
																			<label for="ci_title" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv:</label>
																			<div class="col-sm-9">
																				<div class="materail-input-block materail-input-block_success">
																					<input class="form-control materail-input" type="text" name="ci_title" id="ci_title" placeholder="Facebook" required>
																					<span class="materail-input-block__line"></span>
																				</div>
																			</div>
																		</div>
																		<div class="form-group">
																			<label for="ci_data" class="col-sm-3 control-label"><span class="text-danger">*</span> Adresa:</label>
																			<div class="col-sm-9">
																				<div class="materail-input-block materail-input-block_success">
																					<input class="form-control materail-input" type="text" name="ci_data" id="ci_data" placeholder="www.facebook.com/profil" required>
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
																				SELECT ci_id, ci_title, ci_data, ci_primary
																				FROM idk_contacts_info
																				WHERE ci_group = :ci_group AND ci_contactid = :ci_contactid
																				ORDER BY ci_primary DESC");

															$query_phones->execute(array(
																	':ci_group' => 3,
																	':ci_contactid' => $contact_id));

															while($row_phones = $query_phones->fetch()){

																$ci_id = $row_phones['ci_id'];
																$ci_title = $row_phones['ci_title'];
																$ci_data = $row_phones['ci_data'];
																$ci_primary = $row_phones['ci_primary'];
														?>
														<tr>
															<td><?php echo $ci_title; ?>:</td>
															<td><?php echo $ci_data; ?></td>
															<td class="text-right">
																<ul class="list-inline">
																	<?php if($ci_primary == 1){ echo '<li data-toggle="tooltip" data-placement="top" title="Primarni"><i class="fa fa-star fa-lg text-success" aria-hidden="true"></i></li>'; }else{ echo '<li data-toggle="tooltip" data-placement="top" title="Postavi kao primarni"><a href="' . getSiteURLr() . 'do.php?form=set_primary_other&ci_id=' . $ci_id . '&contact_id=' . $contact_id  . '"><i class="fa fa-star-o fa-lg text-success" aria-hidden="true"></i></a></li>'; } ?>
																	<li data-toggle="tooltip" data-placement="top" title="Obriši"><a href="#" data="<?php getSiteURL(); ?>contacts?page=del_contact_info&id=<?php echo $ci_id; ?>" data-toggle="modal" data-target="#delOtherModal" class="delOther"><i class="fa fa-trash-o fa-lg text-danger" aria-hidden="true"></i></a></li>
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
								<div class="tab-pane fade" id="notes">
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
														<form action="<?php getSiteURL(); ?>do.php?form=add_contact_note" method="post" role="form" class="form-horizontal">
															<input type="hidden" name="note_dataid" value="<?php echo $contact_id; ?>" />
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
															':note_dataid' => $contact_id,
															':note_group' => 6));

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
																		':note_group' => 6,
																		':note_dataid' => $contact_id));

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
															<a href="#" data="<?php getSiteURL(); ?>contacts?page=del_note&id=<?php echo $note_id; ?>" data-toggle="modal" data-target="#deleteNoteModal" class="delete_note btn material-btn material-btn_danger main-container__column"><i class="fa fa-trash-o fa-lg" aria-hidden="true"></i></a>
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
														<form action="<?php getSiteURL(); ?>do.php?form=add_contact_doc" enctype="multipart/form-data" method="post" role="form" class="form-horizontal">
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
															<input type="hidden" name="document_dataid" value="<?php echo $contact_id; ?>" />
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
															':document_group' => 2,
															':document_dataid' => $contact_id));

												while($row_doc = $query_doc->fetch()){

													$document_id = $row_doc['document_id'];
													$document_name = $row_doc['document_name'];
													$document_desc = $row_doc['document_desc'];
													$document_file = $row_doc['document_file'];
													$document_datetime = date('d.m.Y.', strtotime($row_doc['document_datetime']));

													if($row_doc['document_icon'] == "jpg"){
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
												<td class="text-center"><a href="download?folder=contacts&id=<?php echo $document_id; ?>" class="btn material-btn material-btn_success main-container__column" target="_BLANK"><?php echo $document_icon; ?></a></td>
												<td class="text-center"><a href="#" data="<?php getSiteURL(); ?>contacts?page=del_doc&id=<?php echo $document_id; ?>" data-toggle="modal" data-target="#deleteDocModal" class="delete_doc btn material-btn material-btn_danger main-container__column"><i class="fa fa-trash-o fa-lg" aria-hidden="true"></i></a></td>
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
								<div class="tab-pane fade" id="important">
									<form action="<?php getSiteURL(); ?>do.php?form=save_contact_inote" method="post" role="form" class="form-horizontal">
										<input type="hidden" name="contact_id" value="<?php echo $contact_id; ?>" />
										<div class="form-group">
											<div class="col-md-offset-1 col-sm-10">
												<div class="form-group materail-input-block materail-input-block_success">
													<textarea id="inote" class="form-control materail-input material-textarea" name="contact_inote" placeholder="Važne bilješke" rows="8"><?php echo base64_decode($contact_inote); ?></textarea>
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

							unlink("files/files/contacts/" . $document_file);

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

						header("Location: " . getSiteURLr() . "contacts?page=open&id=$document_dataid&mess=3");

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

						//Delete note from db
						$note_del_query = $db->prepare("
													DELETE FROM idk_notes
													WHERE note_id = :note_id");

						$note_del_query->execute(array(
											':note_id' => $note_id));

						header("Location: " . getSiteURLr() . "contacts?page=open&id=$note_dataid&mess=4");

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

						$contact_id = $_GET['id'];

						//Get
						$query_select = $db->prepare("
												SELECT contact_firstname, contact_lastname
												FROM idk_contacts
												WHERE contact_id = :contact_id");

						$query_select->execute(array(
											':contact_id' => $contact_id));

						$row_select = $query_select->fetch();

						$contact_firstname = $row_select['contact_firstname'];
						$contact_lastname = $row_select['contact_lastname'];

						//Save
						$query = $db->prepare("
										UPDATE idk_contacts
										SET contact_status = :contact_status
										WHERE contact_id = :contact_id");

						$query->execute(array(
									':contact_status' => 0,
									':contact_id' => $contact_id));

						//Add to LOGS
						$log_desc = "Arhivirao kontakt profil: " . $contact_firstname . " " . $contact_lastname . " ";
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


						header("Location: " . getSiteURLr() . "contacts?page=list&mess=3");

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

				case "del_timeline":
					if(in_array("1", $getEmployeeStatus)){

						$timeline_id = $_GET['id'];

						//Get timeline_txt and timeline_dataid
						$timeline_open_query = $db->prepare("
													SELECT timeline_txt, timeline_dataid
													FROM idk_timeline
													WHERE timeline_id = :timeline_id");

						$timeline_open_query->execute(array(
												':timeline_id' => $timeline_id));

						$timeline_open = $timeline_open_query->fetch();

							$timeline_txt = strip_tags($timeline_open['timeline_txt']);
							$timeline_dataid = $timeline_open['timeline_dataid'];

						//Add to LOGS
						$log_desc = "Obrisao aktivnost: " . $timeline_txt . " ";
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

						//Delete timeline from db
						$timeline_del_query = $db->prepare("
													DELETE FROM idk_timeline
													WHERE timeline_id = :timeline_id");

						$timeline_del_query->execute(array(
											':timeline_id' => $timeline_id));

						header("Location: " . getSiteURLr() . "contacts?page=open&id=$timeline_dataid&mess=7");

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

						header("Location: " . getSiteURLr() . "contacts?page=open&id=$task_dataid&mess=11");

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

				case "del_contact_info":
					if(in_array("1", $getEmployeeStatus)){

						$ci_id = $_GET['id'];

						//Get ci_title, ci_data and ci_contactid
						$phone_open_query = $db->prepare("
													SELECT ci_title, ci_data, ci_contactid
													FROM idk_contacts_info
													WHERE ci_id = :ci_id");

						$phone_open_query->execute(array(
												':ci_id' => $ci_id));

						$phone_open = $phone_open_query->fetch();

							$ci_title = $phone_open['ci_title'];
							$ci_data = $phone_open['ci_data'];
							$ci_contactid = $phone_open['ci_contactid'];

						//Add to LOGS
						$log_desc = "Obrisao telefon: " . $ci_title . " - " . $ci_data . "";
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
													DELETE FROM idk_contacts_info
													WHERE ci_id = :ci_id");

						$phone_del_query->execute(array(
											':ci_id' => $ci_id));

						header("Location: " . getSiteURLr() . "contacts?page=open&id=$ci_contactid&mess=16");

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