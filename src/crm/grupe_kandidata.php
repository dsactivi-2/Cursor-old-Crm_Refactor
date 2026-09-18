<?php
	include("includes/functions.php");
	include("includes/common.php");
	$getEmployeeStatus = explode( ',' , getEmployeeStatus());

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
	<title>Grupe kandidata | <?php getTitle(); ?></title>

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
					<h1><i class="fa fa-check-square idk_color_green" aria-hidden="true"></i> Grupe kandidata</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>grupe_kandidata?page=add" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj</span></a>
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
										echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novu grupu.</div>';
									}elseif($mess == 2){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste uredili grupu.</div>';
									}elseif($mess == 3){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste uredili profil zaposlenika.</div>';
									}elseif($mess == 4){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste arhivirali grupu.</div>';
									}
								?>
								<script type="text/javascript">
									$(document).ready(function() {
										$('#idk_table').DataTable({

											responsive: true,

											"order": [[ 0, "desc" ]],

											 "bAutoWidth": false,

											"aoColumns": [
													{ "width": "5%" },
													{ "width": "70%" },
													{ "width": "15%" },
													{ "width": "10%", "bSortable": false }
												]
										});
									} );
								</script>
								<script>
								function copyToClipboard(element) {
									var $temp = $("<input>");
									$("body").append($temp);
									$temp.val($(element).text()).select();
									document.execCommand("copy");
									$temp.remove();
								}
								</script>
								<table id="idk_table" class="display" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th></th>
											<th>Naziv grupe</th>
											<th>Datum</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										<?php
											$query = $db->prepare("
															SELECT kg_id, kg_title, kg_date
															FROM idk_kandidati_grupe
															WHERE kg_status = 0
															ORDER BY kg_id DESC
															");

											$query->execute();
											$i=1;
											while($row = $query->fetch()){

												$kg_id = $row['kg_id'];
												$kg_title = $row['kg_title'];
												$kg_date = $row['kg_date'];
												$kg_date_f = date('d.m.Y H:i', strtotime($kg_date));




										?>
										<tr>
											<td class="text-center"><?php echo $kg_id; ?></td>
											<td><?php echo $kg_title; ?></td>
											<td><span class="label label-success material-label material-label_success main-container__column"><?php echo $kg_date_f; ?></span></td>
											<td class="text-center">
												<div class="btn-group material-btn-group">
													<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
													<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">

														<li><a href="<?php getSiteURL(); ?>grupe_kandidata?page=edit&id=<?php echo $kg_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Uredi</a></li>

														<li class="idk_dropdown_danger"><a href="#" data="<?php getSiteURL(); ?>grupe_kandidata?page=archive&id=<?php echo $kg_id; ?>" data-toggle="modal" data-target="#archiveModal" class="archive material-dropdown-menu__link"><i class="fa fa-trash-o" aria-hidden="true"></i> Arhiviraj</a></li>

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
					if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){
						
		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-retweet idk_color_green" aria-hidden="true"></i> Dodaj novu grupu</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>grupe_kandidata?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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
								<form action="<?php getSiteURL(); ?>do.php?form=add_candidate_groups" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<div class="form-group">
										<label for="kg_title" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kg_title" id="kg_title" placeholder="Npr. Medicinar" required>
												<span class="materail-input-block__line"></span>
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
					if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){

						$kg_id = $_GET['id'];

						$query = $db->prepare("
										SELECT kg_title
										FROM idk_kandidati_grupe
										WHERE kg_id = :kg_id
										");

						$query->execute(array(
							":kg_id" => $kg_id
						));
						$row = $query->fetch();

							$kg_title = $row['kg_title'];



		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-retweet idk_color_green" aria-hidden="true"></i> Uredi grupu</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>grupe_kandidata?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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
								<form action="<?php getSiteURL(); ?>do.php?form=edit_candidate_groups" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
								<input type="hidden" name="kg_id" value="<?php echo $kg_id ?>">
									<div class="form-group">
										<label for="lg_url" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv grupe:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kg_title" id="kg_title" value="<?php echo $kg_title; ?>" required>
												<span class="materail-input-block__line"></span>
											</div>
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

					$employee_id = $_GET['id'];

					$query = $db->prepare("
									SELECT employee_firstname, employee_lastname, employee_jmbg, employee_email, employee_position, employee_dob, employee_doe, employee_phone, employee_address, employee_city, employee_country, employee_info, employee_status, employee_image, employee_inote
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
						$employee_phone = $row['employee_phone'];
						$employee_address = $row['employee_address'];
						$employee_city = $row['employee_city'];
						$employee_country = $row['employee_country'];
						$employee_info = $row['employee_info'];
						$employee_inote = $row['employee_inote'];

						if($row['employee_image'] == "none"){
							$employee_image = "none.jpg";
						}else{
							$employee_image = $row['employee_image'];
						}

						if($row['employee_status'] == 0){
							$employee_status = "Deaktiviran";
						}elseif($row['employee_status'] == 1){
							$employee_status = "Administrator";
						}elseif($row['employee_status'] == 2){
							$employee_status = "Super korisnik";
						}elseif($row['employee_status'] == 3){
							$employee_status = "Korisnik";
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
									}
								?>
							</div>
						</div>


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
												<button type="submit" class="btn btn-primary material-btn material-btn_primary">Dodaj</button>
											</form>
										</div>
									</div>
								</div>
							</div>
							<!-- Modal add note end -->
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

																		var f = this.files[0];

																		if (f.size > 20388608 || f.fileSize > 20388608){
																			$('#idk_alert_size').removeClass('hidden');
																			this.value = null;
																		}else{
																			$('#idk_alert_size').addClass('hidden');
																		}

																		var ext = $('#document_file').val().split('.').pop().toLowerCase();

																		if($.inArray(ext, ['jpg', 'jpeg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png']) == -1) {
																			$('#idk_alert_ext').removeClass('hidden');
																			this.value = null;
																		}else{
																			$('#idk_alert_ext').addClass('hidden');
																		}
																	})
																});
															</script>
														</div>
													</div>
												</div>
										</div>
										<div class="modal-footer material-modal__footer">
												<button type="submit" class="btn btn-primary material-btn material-btn_primary">Dodaj</button>
											</form>
										</div>
									</div>
								</div>
							</div>
							<!-- Modal add document end -->
						</ul>
						<hr />
						<div id="myTabs" class="panel-group material-tabs-group">
							<ul class="nav nav-tabs material-tabs material-tabs_primary">
								<li class="active"><a href="#info" class="material-tabs__tab-link" data-toggle="tab">Informacije</a></li>
								<li><a href="#notes" class="material-tabs__tab-link" data-toggle="tab">Bilješke</a></li>
								<li><a href="#documents" class="material-tabs__tab-link" data-toggle="tab">Dokumenti</a></li>
								<li><a href="#important" class="material-tabs__tab-link" data-toggle="tab">Važno</a></li>
							</ul>
							<div class="tab-content materail-tabs-content">
								<div class="tab-pane fade active in" id="info">
									<div class="row idk_employee_info">
										<div class="col-md-6">
											<div class="row">
												<strong class="col-sm-4 text-right">Ime:</strong>
												<div class="col-sm-8"><?php echo $employee_firstname; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Prezime:</strong>
												<div class="col-sm-8"><?php echo $employee_lastname; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">JMBG:</strong>
												<div class="col-sm-8"><?php echo $employee_jmbg; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Email:</strong>
												<div class="col-sm-8"><a href="mailto:<?php echo $employee_email; ?>"><?php echo $employee_email; ?></a></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Pozicija:</strong>
												<div class="col-sm-8"><?php echo $employee_position; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Datum rođenja:</strong>
												<div class="col-sm-8"><?php echo $employee_dob; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Datum zaposlenja:</strong>
												<div class="col-sm-8"><?php echo $employee_doe; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Telefon:</strong>
												<div class="col-sm-8"><?php echo $employee_phone; ?></div>
											</div>
										</div>
										<div class="col-md-6">
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
												<div class="col-sm-8"><?php echo $employee_status; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Ostale informacije:</strong>
												<div class="col-sm-8"><?php echo $employee_info; ?></div>
											</div>
										</div>
									</div>
								</div>
								<div class="tab-pane fade" id="notes">
									<div class="panel-group material-accordion material-accordion_primary" id="accordion1">
										<?php

											$year_query = $db->prepare("
																SELECT YEAR (note_datetime) AS note_datetime_year
																FROM idk_notes
																WHERE note_dataid = :note_dataid
																GROUP BY YEAR (note_datetime)
																ORDER BY YEAR (note_datetime) DESC");

											$year_query->execute(array(
															':note_dataid' => $employee_id));

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
																		WHERE YEAR (note_datetime) = :note_datetime_year AND note_dataid = :note_dataid
																		ORDER BY note_datetime DESC");

														$notes_query->execute(array(
																		':note_datetime_year' => $note_datetime_year,
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
								</div>
								<div class="tab-pane fade" id="documents">
									<script type="text/javascript">
										$(document).ready(function() {
											$('#idk_table_documents').DataTable({

												responsive: true,

												"order": [[ 0, "asc" ]],

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
												<td class="text-center"><a href="<?php getSiteURL(); ?>files/employees/<?php echo $document_file; ?>" class="btn material-btn material-btn_success main-container__column" target="_BLANK"><?php echo $document_icon; ?></a></td>
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
								<div class="tab-pane fade" id="important">
									<form action="<?php getSiteURL(); ?>do.php?form=save_employee_inote" method="post" role="form" class="form-horizontal">
										<input type="hidden" name="employee_id" value="<?php echo $employee_id; ?>" />
										<div class="form-group text-right">
											<div class="col-md-offset-1 col-sm-10">
												<div class="form-group materail-input-block materail-input-block_success">
													<textarea class="form-control materail-input material-textarea" name="employee_inote" placeholder="Važne bilješke" rows="8" required><?php echo $employee_inote; ?></textarea>
													<span class="materail-input-block__line"></span>
												</div>
												<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-save" aria-hidden="true"></i> <span>Snimi</span></button>
											</div>
										</div>
									</form>
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

							unlink("files/employees/" . $document_file);

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

						$kg_id = $_GET['id'];


						$query_get = $db->prepare("
										SELECT kg_id, kg_title
										FROM idk_kandidati_grupe
										WHERE kg_id = :kg_id
										");

						$query_get->execute(array(
							":kg_id" => $kg_id
						));
						$row_get = $query_get->fetch();

							$kg_id = $row_get['kg_id'];
							$kg_title = $row_get['kg_title'];

						//Save
						$query = $db->prepare("
										UPDATE idk_kandidati_grupe
										SET kg_status = :kg_status
										WHERE kg_id = :kg_id");

						$query->execute(array(
									':kg_status' => 1,
									':kg_id' => $kg_id));

						//Add to LOGS
						$log_desc = "Arhivirao grupu: " . $kg_title . "";
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


						header("Location: grupe_kandidata?page=list&mess=4");

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