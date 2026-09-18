<?php
	include("includes/functions.php");
	include("includes/common.php");
	// Turn off all error reporting
	error_reporting(0);
	$getEmployeeStatus = getEmployeeStatus();

    //Check module status
    if($module_projects == 0){
        header("Location: index");
    }

	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: projects?page=list");
	}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php getTitle(); ?></title>

	<?php include('includes/head.php'); ?>

	<script src="<?php getSiteURL(); ?>js/sortable.min.js"></script>

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
					<h1><i class="fa fa-stack-overflow idk_color_green" aria-hidden="true"></i> Projekti</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>projects?page=add" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj</span></a>
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
										echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novi projekat.</div>';
									}elseif($mess == 3){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste uredili profil projekta.</div>';
									}elseif($mess == 4){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste arhivirali projekat.</div>';
									}
								?>
								<script type="text/javascript">
									$(document).ready(function() {
										$('#idk_table').DataTable({

											responsive: true,

											"order": [[ 0, "asc" ]],

											 "bAutoWidth": false,

											"aoColumns": [
													{ "width": "30%" },
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
											<th>Naziv</th>
											<th>Kompanija</th>
											<th class="text-center">Kreirano</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										<?php
											$query = $db->prepare("
															SELECT project_id, project_name, project_datetime, company_name, company_type, company_id
															FROM idk_projects
                                                            INNER JOIN idk_companies ON idk_projects.project_companyid = idk_companies.company_id
															WHERE project_status != :project_status");

											$query->execute(array(
												'project_status' => 0));

											while($row = $query->fetch()){

												$project_id = $row['project_id'];
												$project_name = $row['project_name'];
												$company_id = $row['company_id'];
												$company_name = $row['company_name'];
												$company_type = $row['company_type'];
                                                $project_datetime = date('d.m.Y. - H:i', strtotime($row['project_datetime']));
										?>
										<tr>
											<td><a href="<?php getSiteURL(); ?>projects?page=open&id=<?php echo $project_id; ?>"><?php echo $project_name; ?></a></td>
											<td><a href="<?php getSiteURL(); ?>companies?page=open&id=<?php echo $company_id; ?>"><?php echo $company_name; ?> <?php echo $company_type; ?></a></td>
											<td class="text-center"><?php echo $project_datetime; ?></td>
											<td class="text-center">
												<div class="btn-group material-btn-group">
													<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
													<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
														<li><a href="<?php getSiteURL(); ?>projects?page=open&id=<?php echo $project_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori</a></li>
														<li><a href="<?php getSiteURL(); ?>projects?page=edit&id=<?php echo $project_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Uredi</a></li>
														<li class="idk_dropdown_danger"><a href="#" data="<?php getSiteURL(); ?>projects?page=archive&id=<?php echo $project_id; ?>" data-toggle="modal" data-target="#archiveModal" class="archive material-dropdown-menu__link"><i class="fa fa-trash-o" aria-hidden="true"></i> Arhiviraj</a></li>
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
					if($getEmployeeStatus == 1){
		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-stack-overflow idk_color_green" aria-hidden="true"></i> Dodaj novi projekat</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>projects?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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
								<form id="idk_form" action="<?php getSiteURL(); ?>do_modules.php?form=add_project" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<div class="form-group">
										<label for="project_name" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv projekta:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="project_name" id="project_name" placeholder="Naziv projekta" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="project_plannedhours" class="col-sm-3 control-label">Planirano sati:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="number" name="project_plannedhours" id="project_plannedhours">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="project_pmanagerid" class="col-sm-3 control-label">Zadužena osoba:</label>
										<div class="col-sm-9">
											<select class="selectpicker" id="project_pmanagerid" name="project_pmanagerid" data-live-search="true" required>
												<option value="0">Bez zadužene osobe</option>
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
											<span class="materail-input-block__line"></span>
										</div>
									</div>
                                    <div class="form-group">
										<label for="project_desc" class="col-sm-3 control-label">Opis projekta:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<textarea id="project_desc" class="form-control materail-input material-textarea" name="project_desc" placeholder="Opis projekta" rows="8"></textarea>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
                                        <script>
    										$('#project_desc').trumbowyg({
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
									<div class="form-group">
										<label for="project_companyid" class="col-sm-3 control-label"><span class="text-danger">*</span> Kompanija:</label>
										<div class="col-sm-9">
											<select class="selectpicker" id="project_companyid" name="project_companyid" data-live-search="true" required>
												<option value=""></option>
												<?php
													$select_query = $db->prepare("
																		SELECT company_id, company_name, company_type
																		FROM idk_companies
																		WHERE company_status != :company_status");

													$select_query->execute(array(
																	':company_status' => 0));

													while($select_row = $select_query->fetch()) {
														echo "<option value='" . $select_row['company_id'] . "'>" . $select_row['company_name'] . " " . $select_row['company_type'] . "</option>";
													}
												?>
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
					if($getEmployeeStatus == 1){

						$project_id = $_GET['id'];

						$query = $db->prepare("
										SELECT project_name, project_desc, project_plannedhours, project_companyid, project_pmanagerid
										FROM idk_projects
										WHERE project_id = :project_id");

						$query->execute(array(
									':project_id' => $project_id));

						$row = $query->fetch();

							$project_name = $row['project_name'];
							$project_desc = $row['project_desc'];
							$project_plannedhours = $row['project_plannedhours'];
							$project_companyid = $row['project_companyid'];
							$project_pmanagerid = $row['project_pmanagerid'];

		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-stack-overflow idk_color_green" aria-hidden="true"></i> Uredi profile projekta</h1>
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
								<form id="idk_form" action="<?php getSiteURL(); ?>do_modules.php?form=edit_project" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<input type="hidden" name="project_id" value="<?php echo $project_id; ?>" />
                                    <div class="form-group">
										<label for="project_name" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv projekta:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="project_name" id="project_name" value="<?php echo $project_name; ?>" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
                                    <div class="form-group">
										<label for="project_plannedhours" class="col-sm-3 control-label">Planirano sati:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="number" name="project_plannedhours" id="project_plannedhours" value="<?php echo $project_plannedhours; ?>">

												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="project_pmanagerid" class="col-sm-3 control-label">Zadužena osoba:</label>
										<div class="col-sm-9">
											<select class="selectpicker" id="project_pmanagerid" name="project_pmanagerid" data-live-search="true" required>
												<option value="0">Bez zadužene osobe</option>
												<?php

													$select_query = $db->prepare("
																		SELECT employee_id, employee_firstname, employee_lastname
																		FROM idk_employees
																		WHERE employee_status != :employee_status");
													$select_query->execute(array(
																	':employee_status' => 0));

													while($select_row = $select_query->fetch()) {

														if($project_pmanagerid == $select_row['employee_id']){ $selected = "selected"; }else{ $selected = ""; }
                                                        echo "<option value='" . $select_row['employee_id'] . "' " . $selected . ">" . $select_row['employee_firstname'] . " " . $select_row['employee_lastname'] . "</option>";

													}
												?>
											</select>
											<span class="materail-input-block__line"></span>
										</div>
									</div>
                                    <div class="form-group">
										<label for="project_desc" class="col-sm-3 control-label">Opis projekta:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<textarea id="project_desc" class="form-control materail-input material-textarea" name="project_desc" rows="8"><?php echo $project_desc; ?></textarea>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
                                        <script>
    										$('#project_desc').trumbowyg({
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
									<div class="form-group">
										<label for="project_companyid" class="col-sm-3 control-label"><span class="text-danger">*</span> Kompanija:</label>
										<div class="col-sm-9">
											<select class="selectpicker" id="project_companyid" name="project_companyid" data-live-search="true" required>
												<option value=""></option>
												<?php
													$select_query = $db->prepare("
																		SELECT company_id, company_name, company_type
																		FROM idk_companies
																		WHERE company_status != :company_status");

													$select_query->execute(array(
																	':company_status' => 0));

													while($select_row = $select_query->fetch()) {

                                                        if($project_companyid == $select_row['company_id']){ $selected = "selected"; }else{ $selected = ""; }
                                                        echo "<option value='" . $select_row['company_id'] . "' " . $selected . ">" . $select_row['company_name'] . " " . $select_row['company_type'] . "</option>";

													}
												?>
											</select>
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

				case "open":

					$project_id = $_GET['id'];

					$query = $db->prepare("
									SELECT project_name, project_datetime, employee_firstname, employee_lastname
									FROM idk_projects
									LEFT JOIN idk_employees ON idk_projects.project_pmanagerid = idk_employees.employee_id
									WHERE project_id = :project_id");

					$query->execute(array(
								':project_id' => $project_id));

					$row = $query->fetch();

					$project_name = $row['project_name'];
					$employee_firstname = $row['employee_firstname'];
					$employee_lastname = $row['employee_lastname'];
					$project_datetime = date('d.m.Y.', strtotime($row['project_datetime']));
				?>
				<div class="row">
					<div class="col-xs-8">
						<h1><i class="fa fa-stack-overflow idk_color_green" aria-hidden="true"></i> Projekat: <?php echo $project_name; ?> | <i class="fa fa-user idk_color_green" aria-hidden="true"></i> <?php echo $employee_firstname; ?> <?php echo $employee_lastname; ?></h1>
					</div>
					<div class="col-xs-4 text-right idk_margin_top10">
						<a href="<?php getSiteURL(); ?>projects?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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
											<div class="alert material-alert material-alert_success">Uspješno ste dodali dokument.</div>
											<script>$(function() { $('[href="#documents"]').tab('show'); });</script>
										<?php
										}
										elseif($mess == 2){ ?>
											<div class="alert material-alert material-alert_success">Uspješno ste obrisali dokument.</div>
											<script>$(function() { $('[href="#documents"]').tab('show'); });</script>
										<?php
										}
										elseif($mess == 3){ ?>
											<div class="alert material-alert material-alert_success">Uspješno ste dodali bilješku.</div>
											<script>$(function() { $('[href="#notes"]').tab('show'); });</script>
										<?php
										}
										elseif($mess == 4){ ?>
											<div class="alert material-alert material-alert_success">Uspješno ste obrisali bilješku.</div>
											<script>$(function() { $('[href="#notes"]').tab('show'); });</script>
										<?php
										}
										elseif($mess == 5){ ?>
											<div class="alert material-alert material-alert_success">Uspješno ste odradili izmjene na nalogu.</div>
											<script>$(function() { $('[href="#info"]').tab('show'); });</script>
										<?php
										}elseif($mess == 6){ ?>
											<div class="alert material-alert material-alert_success">Uspješno ste kreirali projekat za nalog.</div>
											<script>$(function() { $('[href="#projects"]').tab('show'); });</script>
										<?php
										}
									?>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-6">
									<p>Progres projekta:</p>
									<script>
										function callProgress() {
											$.ajax({
												url: "do_modules.php?form=get_progress&id=<?php echo $project_id; ?>",
												success: (function (result) {
													$(".progress .progress-bar").html(result+'%');
													$(".progress .progress-bar").prop('aria-valuenow',result);
													$(".progress .progress-bar").css('width', result+'%');
												})
											});
										};
										callProgress();
									</script>
									<div class="progress">
										<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
											0%
										</div>
									</div>
								</div>
								<?php
									//Planned time
									$query_planned_time = $db->prepare("
																SELECT project_plannedhours
																FROM idk_projects
																WHERE project_id = :project_id");

									$query_planned_time->execute(Array(
															':project_id' => $project_id));

									$row_planned_time = $query_planned_time->fetch();

										$project_plannedhours = $row_planned_time['project_plannedhours'];

									//Spent time
									$query_time = $db->prepare("
													SELECT COUNT(er_id) AS er_id_total, sum(TIMESTAMPDIFF(MINUTE, er_timefrom, er_timeto)) AS 'er_time_total'
													FROM idk_employees_reports
													WHERE er_projectid = :er_projectid");

									$query_time->execute(Array(
												':er_projectid' => $project_id));

									$row_time = $query_time->fetch();

										$er_time_total_f = floor($row_time['er_time_total'] / 60).':'.($row_time['er_time_total'] -   floor($row_time['er_time_total'] / 60) * 60);

										if($project_plannedhours > $er_time_total_f){
											$project_time_class = "idk_project_time_success";
										}else{
											$project_time_class = "idk_project_time_danger";
										}

								?>
								<div class="col-sm-2">
									<div class="idk_project_time">
										<p>PLANIRANO SATI</p>
										<span><?php echo $project_plannedhours; ?></span>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="idk_project_time <?php echo $project_time_class; ?>">
										<p>UTROŠENO SATI</p>
										<span><?php echo $er_time_total_f; ?></span>
									</div>
								</div>
								<div class="col-sm-2">
									<a href="#" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" data-toggle="modal" data-target="#taskModal"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj zadatak</span></a>
									<div class="modal material-modal material-modal_primary fade text-left" id="taskModal">
										<div class="modal-dialog modal-lg">
											<div class="modal-content material-modal__content">
												<div class="modal-header material-modal__header">
													<button class="close material-modal__close" data-dismiss="modal">&times;</button>
													<h4 class="modal-title material-modal__title">Dodaj novi zadatak</h4>
												</div>
												<div class="modal-body material-modal__body">
													<form action="<?php getSiteURL(); ?>do_modules.php?form=add_project_task" method="post" role="form" class="form-horizontal">
														<input type="hidden" name="pt_projectid" value="<?php echo $project_id; ?>" />
														<div class="form-group">
															<div class="col-md-offset-1 col-sm-10">
																<div class="form-group materail-input-block materail-input-block_success">
																	<input class="form-control materail-input material-textarea" name="pt_name" placeholder="Naziv zadatka" required>
																	<span class="materail-input-block__line"></span>
																</div>
															</div>
														</div>
														<div class="form-group">
															<div class="col-md-offset-1 col-sm-10">
																<select class="selectpicker" id="pt_employeeid" name="pt_employeeid" data-live-search="true" required>
																	<option value="0">Bez zadužene osobe</option>
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
																<span class="materail-input-block__line"></span>
															</div>
														</div>
														<br>
														<div class="form-group">
															<div class="col-md-offset-1 col-sm-10">
																<div class="form-group materail-input-block materail-input-block_success">
																	<textarea class="form-control materail-input material-textarea" name="pt_desc" placeholder="Opis zadatka ..." rows="6"></textarea>
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
							<hr>
							<div id="myTabs" class="panel-group material-tabs-group">
								<ul class="nav nav-tabs material-tabs material-tabs_primary">
									<li class="active"><a href="#progres" class="material-tabs__tab-link" data-toggle="tab">Projekt</a></li>
									<li><a href="#kandidati" class="material-tabs__tab-link" data-toggle="tab">Kandidati</a></li>
									<li><a href="#notes" class="material-tabs__tab-link" data-toggle="tab">Bilješke</a></li>
									<li><a href="#documents" class="material-tabs__tab-link" data-toggle="tab">Dokumenti</a></li>
								</ul>
								<div class="tab-content materail-tabs-content">
									<div class="tab-pane fade active in" id="progres">
										<div class="row">
											<div class="col-md-12">
												<div class="content_box">
													<div class="row">
														<div class="col-xs-12">
															<div class="idk_project_box_holder">
															<div class="idk_project_box_inner">
																<?php
																	$query_project_box = $db->prepare("
																					SELECT ps_id, ps_name
																					FROM idk_projects_settings
																					WHERE ps_group = :ps_group");

																	$query_project_box->execute(Array(
																				':ps_group' => 1));

																	while($row_project_box = $query_project_box->fetch()){

																		$ps_id = $row_project_box['ps_id'];
																		$ps_name = $row_project_box['ps_name'];

																?>
																	<ul class="idk_project_box list-unstyled">
																		<h6><?php echo $ps_name; ?></h6>
																		<div class="idk_project_list" id="box<?php echo $ps_id; ?>">
																			<?php
																				$query_projects_task = $db->prepare("
																											SELECT pt_id, pt_name, pt_sort
																											FROM idk_projects_tasks
																											WHERE pt_projectid = :pt_projectid AND pt_box = :pt_box
																											ORDER BY pt_sort ASC");

																				$query_projects_task->execute(Array(
																									':pt_projectid' => $project_id,
																									':pt_box' => $ps_id));

																				while($row_projects_task = $query_projects_task->fetch()){

																					$pt_id = $row_projects_task['pt_id'];
																					$pt_name = $row_projects_task['pt_name'];
																					$pt_sort = $row_projects_task['pt_sort'];
																			?>
																			<li data-toggle="modal" data-target="#view-modal" class="getUser" data-id="<?php echo $pt_id; ?>"><p><?php echo $pt_name; ?></p></li>
																			<?php } ?>
																		</div>
																	</ul>
																<?php } ?>
															</div>
															</div>
															<div class="modal material-modal material-modal_primary fade" id="view-modal">
															<div class="modal-dialog modal-lg">
																<div class="modal-content material-modal__content">
																	<div class="modal-header material-modal__header">
																		<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																		<h4 class="modal-title material-modal__title">Projektni zadatak</h4>
																	</div>
																	<div class="modal-body material-modal__body">
																		<div id="modal-loader" style="display: none; text-align: center;">
																			<img src="<?php getSiteURL(); ?>images/ajax-loader.gif">
																		</div>

																		<div id="dynamic-content">
																			<div class="row">
																				<div class="col-sm-12">
																					<h4 id="pt_name"></h4>
																				</div>
																				<div class="col-sm-9">
																					<div class="row">
																						<div class="col-sm-6">
																								<p id="pt_datetime"></p>
																						</div>
																						<div class="col-sm-6 text-right">
																								<p><span id="employee_firstname"></span> <span id="employee_lastname"></span></p>
																						</div>
																					</div>
																					<p id="pt_desc" class="pt_desc_view"></p>
																					<div id="pt_desc_edit_box" class="hidden">
																						<form id="edit_project_task_desc" action="" method="post" role="form" class="form-horizontal">
																								<input type="hidden" id="pt_id" name="pt_id" value="" />
																								<div class="form-group">
																									<div class="col-sm-12">
																										<textarea class="form-control materail-input material-textarea" id="pt_desc_edit" name="pt_desc" rows="8"></textarea>
																									</div>
																								</div>
																								<ul class="list-inline">
																									<li class="hidden"><i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i></li>
																									<li><button type="submit" class="btn btn-success material-btn material-btn_success">Snimi</button></li>
																									<li><a id="close_project_task_edit" class="btn material-btn"><i class="fa fa-times"></i></a></li>
																								</ul>
																							</form>
																						</div>

																				</div>
																				<div class="col-sm-3">
																					<ul class="list-unstyled idk_project_task_menu">
																						<li><button id="edit_project_task" class="btn btn-primary material-btn material-btn_primary"><i class="fa fa-edit"></i> Uredi opis</button></li>
																						<li><button id="edit_user" class="btn btn-primary material-btn material-btn_primary"><i class="fa fa-user"></i> Zadužena osoba</button></li>
																						<li><button class="btn btn-primary material-btn material-btn_primary" data-dismiss="modal"><i class="fa fa-times"></i> Zatvori</button></li>
																						<li><button id="pt_del_task" data="0" data-toggle="modal" data-target="#deleteModal"  class="delete btn btn-danger material-btn material-btn-icon-danger material-btn_danger"><i class="fa fa-trash"></i> Obriši</button></li>
																					</ul>
																				</div>
																				<script>
																					$("#edit_project_task").click(function(){
																						$("#pt_desc_edit_box").removeClass("hidden");
																						$("#pt_desc").addClass("hidden");
																					});

																					$("#close_project_task_edit").click(function(){
																						$("#pt_desc_edit_box").addClass("hidden");
																						$("#pt_desc").removeClass("hidden");
																					});
																				</script>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
															</div>
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
																			<p>Jeste li sigurni da želite obrisati zadatak?</p>
																		</div>
																		<div class="modal-footer material-modal__footer">
																			<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
																			<a id="delete_link" href=""><button class="btn btn-primary material-btn material-btn_danger">OBRIŠI</button></a>
																		</div>
																	</div>
																</div>
															</div>
															<script>
																$(document).ready(function(){

																	$(document).on('click', '.getUser', function(e){

																		e.preventDefault();

																		var uid = $(this).data('id'); // get id of clicked row

																		$('#dynamic-content').hide(); // hide dive for loader
																		$('#modal-loader').show();  // load ajax loader

																		$.ajax({
																			url: 'get.php?case=getTaskInfo',
																			type: 'POST',
																			data: 'id='+uid,
																			dataType: 'json'
																		})
																		.done(function (data){
																			//console.log(data);
																			$('#dynamic-content').hide(); // hide dynamic div
																			$('#dynamic-content').show(); // show dynamic div
																			$('#pt_del_task').attr('data','projects?page=del_task&id=' + data.pt_id);
																			$('#pt_id').attr('value',data.pt_id);
																			$('#pt_name').html(data.pt_name);
																			$('#pt_desc').html(data.pt_desc);
																			$('#pt_desc_edit').val(data.pt_desc);
																			$('#pt_datetime').html(data.pt_datetime);
																			$('#employee_firstname').html(data.employee_firstname);
																			$('#employee_lastname').html(data.employee_lastname);
																			$('#modal-loader').hide();    // hide ajax loader
																		})
																		.fail(function(){
																			$('.modal-body').html('<i class="glyphicon glyphicon-info-sign"></i> Something went wrong, Please try again...');
																		});

																		$(function () {

																			$('#edit_project_task_desc').on('submit', function (e) {

																			e.preventDefault();

																			$.ajax({
																				type: 'post',
																				url: 'do_modules.php?form=edit_project_task_desc',
																				data: $('#edit_project_task_desc').serialize(),
																				success: function () {
																					$("#pt_desc").html($("#pt_desc_edit").val());
																					$("#pt_desc_edit_box").addClass("hidden");
																					$("#pt_desc").removeClass("hidden");

																				}
																			});

																			});

																		});

																	});
																});

																</script>
															<script>
																	Sortable.create(box1, {
																		group: {
																			name: 'box1',
																			put: ['box1', 'box2', 'box3', 'box4', 'box5']
																		},
																		ghostClass: 'ghost',
																		animation: 400,

																		onAdd: function (evt) {
																			var itemEl = evt.item;
																			var pt_id = $(itemEl).data('id');

																			$.ajax({
																			url: 'do_modules.php?form=edit_project_box_item&pt_box=1&pt_id=' + pt_id
																		});

																		callProgress();

																		},

																		onSort: function (evt) {
																			var order = this.toArray();

																			var new_order = JSON.stringify( order );

																			console.log(new_order);

																		$.ajax({
																			data: {"order" : order},
																			type: 'POST',
																			url: 'do_modules.php?form=sort_project_box_items'
																		});
																		callProgress();
																		},

																	});

																	Sortable.create(box2, {
																		group: {
																			name: 'box2',
																			put: ['box1', 'box2', 'box3', 'box4', 'box5']
																		},
																		ghostClass: 'ghost',
																		animation: 400,

																		onAdd: function (evt) {
																			var itemEl = evt.item;
																			var pt_id = $(itemEl).data('id');

																			$.ajax({
																			url: 'do_modules.php?form=edit_project_box_item&pt_box=2&pt_id=' + pt_id
																		});
																		callProgress();
																		},

																		onSort: function (evt) {
																			var order = this.toArray();

																			var new_order = JSON.stringify( order );

																			console.log(new_order);

																		$.ajax({
																			data: {"order" : order},
																			type: 'POST',
																			url: 'do_modules.php?form=sort_project_box_items'
																		});
																		callProgress();
																		},


																	});

																	Sortable.create(box3, {
																		group: {
																			name: 'box3',
																			put: ['box1', 'box2', 'box3', 'box4', 'box5']
																		},
																		ghostClass: 'ghost',
																		animation: 400,

																		onAdd: function (evt) {
																			var itemEl = evt.item;
																			var pt_id = $(itemEl).data('id');

																			$.ajax({
																			url: 'do_modules.php?form=edit_project_box_item&pt_box=3&pt_id=' + pt_id
																		});
																		callProgress();
																		},

																		onSort: function (evt) {
																			var order = this.toArray();

																			var new_order = JSON.stringify( order );

																			console.log(new_order);

																		$.ajax({
																			data: {"order" : order},
																			type: 'POST',
																			url: 'do_modules.php?form=sort_project_box_items'
																		});
																		callProgress();
																		},


																	});

																	Sortable.create(box4, {
																		group: {
																			name: 'box4',
																			put: ['box1', 'box2', 'box3', 'box4', 'box5']
																		},
																		ghostClass: 'ghost',
																		animation: 400,

																		onAdd: function (evt) {
																			var itemEl = evt.item;
																			var pt_id = $(itemEl).data('id');

																			$.ajax({
																			url: 'do_modules.php?form=edit_project_box_item&pt_box=4&pt_id=' + pt_id
																		});
																		callProgress();
																		},

																		onSort: function (evt) {
																			var order = this.toArray();

																			var new_order = JSON.stringify( order );

																			console.log(new_order);

																		$.ajax({
																			data: {"order" : order},
																			type: 'POST',
																			url: 'do_modules.php?form=sort_project_box_items'
																		});
																		callProgress();
																		},


																	});

																	Sortable.create(box5, {
																		group: {
																			name: 'box5',
																			put: ['box1', 'box2', 'box3', 'box4', 'box5']
																		},
																		ghostClass: 'ghost',
																		animation: 400,

																		onAdd: function (evt) {
																			var itemEl = evt.item;
																			var pt_id = $(itemEl).data('id');

																			$.ajax({
																			url: 'do_modules.php?form=edit_project_box_item&pt_box=5&pt_id=' + pt_id
																		});
																		callProgress();
																		},

																		onSort: function (evt) {
																			var order = this.toArray();

																			var new_order = JSON.stringify( order );

																			console.log(new_order);

																		$.ajax({
																			data: {"order" : order},
																			type: 'POST',
																			url: 'do_modules.php?form=sort_project_box_items'
																		});
																		callProgress();
																		},

																	});

															</script>
														</div>
													</div>
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
															<form action="<?php getSiteURL(); ?>do.php?form=add_project_note" method="post" role="form" class="form-horizontal">
																<input type="hidden" name="note_dataid" value="<?php echo $project_id; ?>" />
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
																':note_dataid' => $project_id,
																':note_group' => 5));

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
																			':note_group' => 5,
																			':note_dataid' => $project_id));

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
																<a href="#" data="<?php getSiteURL(); ?>projects2?page=del_note&id=<?php echo $note_id; ?>" data-toggle="modal" data-target="#deleteNoteModal" class="delete_note btn material-btn material-btn_danger main-container__column"><i class="fa fa-trash-o fa-lg" aria-hidden="true"></i></a>
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
															<form action="<?php getSiteURL(); ?>do.php?form=add_project_doc" enctype="multipart/form-data" method="post" role="form" class="form-horizontal">
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
																<input type="hidden" name="document_dataid" value="<?php echo $project_id; ?>" />
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
																':document_group' => 5,
																':document_dataid' => $project_id));

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
													<td class="text-center"><a href="<?php getSiteURL(); ?>files/files/projects/<?php echo $document_file; ?>" class="btn material-btn material-btn_success main-container__column" target="_BLANK"><?php echo $document_icon; ?></a></td>
													<td class="text-center"><a href="#" data="<?php getSiteURL(); ?>projects2?page=del_doc&id=<?php echo $document_id; ?>" data-toggle="modal" data-target="#deleteDocModal" class="delete_doc btn material-btn material-btn_danger main-container__column"><i class="fa fa-trash-o fa-lg" aria-hidden="true"></i></a></td>
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
									<div class="tab-pane fade" id="kandidati">
										
										<?php
											if(isset($_COOKIE['archive_status'])){
												$archive_status = 1;
											}else{
												$archive_status = 0;
											}
											
											if($archive_status == 0){
												$upit = "AND kandidat_status !=3";
											}else if($archive_status == 1){
												$upit = "";
											}

											if(getEmployeeStatus() == 4){
												$uslov_saradnik = "AND (kandidat_status = 1 OR  kandidat_status = 2)";
											}else{
												$uslov_saradnik = "";
											}


											// USLOVI ZA FILTER PRETRAGU
											if(isset($_GET['search'])){

												$starost_od = $_GET['starost_od'];
												$starost_do = $_GET['starost_do'];

												$filter_grupe = $_GET['filter_grupe'];
												$filter_grupe_f = implode(',', $filter_grupe);

												$filter_status = $_GET['filter_status'];
												$filter_status_f = implode(',', $filter_status);

												if($_GET['vozacka_dozvola'] == "DA"){
													$vozacka_dozvola = "Da";
												}else{
													$vozacka_dozvola = "Ne";
												}

												if($_GET['radno_iskustvo'] == "DA"){
													$radno_iskustvo = "DA";
												}else{
													$radno_iskustvo = "NE";
												}

												$filter_drzavljanstvo = $_GET['filter_drzavljanstvo'];
												$filter_drzavljanstvo_f = implode(',', $filter_drzavljanstvo);

												if(isset($_GET['filter_drzavljanstvo'])){
												if($filter_drzavljanstvo_f == "EU,NON-EU"){
													$drzavljanstvo = '"EU državljanin","NON-EU državljanin"';
												}else if($filter_drzavljanstvo_f == "NON-EU"){
													$drzavljanstvo = '"NON-EU državljanin"';
												}else if($filter_drzavljanstvo_f == "EU"){
													$drzavljanstvo = '"EU državljanin"';
												}
													$uslov_pretrage_drzavljanstvo = "AND kandidat_drzavljanstvo_vrsta IN(".$drzavljanstvo.")";
												}else{
													$uslov_pretrage_drzavljanstvo = '';
												}


												if($_GET['kj_znanje_njemacki'] == "Bezznanja"){
													$kj_znanje_njemacki = "BEZ ZNANJA";
												}else if($_GET['kj_znanje_njemacki'] == "A1"){
													$kj_znanje_njemacki = "'A1','A2','B1','B2','C1','C2'";
												}else if($_GET['kj_znanje_njemacki'] == "A2"){
													$kj_znanje_njemacki = "'A2','B1','B2','C1','C2'";
												}else if($_GET['kj_znanje_njemacki'] == "B1"){
													$kj_znanje_njemacki = "'B1','B2','C1','C2'";
												}else if($_GET['kj_znanje_njemacki'] == "B2"){
													$kj_znanje_njemacki = "'B2','C1','C2'";
												}else if($_GET['kj_znanje_njemacki'] == "C1"){
													$kj_znanje_njemacki = "'C1','C2'";
												}else if($_GET['kj_znanje_njemacki'] == "C2"){
													$kj_znanje_njemacki = "'C2'";
												}

												if($upit !="" OR $uslov_saradnik !=""){$and = "AND";}else{$and = "and";}

												if($vozacka_dozvola == "Da"){
													$uslov_pretrage_vozacka = ''.$and.' kandidat_vozacka_dozvola = "'.$vozacka_dozvola.'"';
												}else{
													$uslov_pretrage_vozacka = ''.$and.' (kandidat_vozacka_dozvola = "Da" OR kandidat_vozacka_dozvola = "Ne")';
												}

												$uslov_pretrage_godine = 'AND (YEAR(NOW()) - YEAR(`kandidat_datumrodjenja`)) BETWEEN '.$starost_od.' AND '.$starost_do.'';
												$uslov_pretrage_statusi = 'AND kandidat_group IN('.$filter_grupe_f.')';
												$uslov_pretrage_status = 'AND kandidat_status IN('.$filter_status_f.')';

											}else{
												$uslov_pretrage_vozacka = "";
												$uslov_pretrage_godine = "";
												$uslov_pretrage_statusi = "";
												$uslov_pretrage_status = "";
												$uslov_pretrage_drzavljanstvo = "";
											}
											
											
											$uslov_glavni = "".$upit." ".$uslov_saradnik." ".$uslov_pretrage_vozacka." ".$uslov_pretrage_godine."  ".$uslov_pretrage_statusi." ".$uslov_pretrage_status." ".$uslov_pretrage_drzavljanstvo."";
																
										?>
										<!-- Modal filter -->
										<div class="modal material-modal material-modal_success fade" id="filterKandidati">
											<div class="modal-dialog">
												<div class="modal-content material-modal__content">
													<div class="modal-header material-modal__header">
														<button class="close material-modal__close" data-dismiss="modal">&times;</button>
														<h4 class="modal-title material-modal__title">Filter kandidata</h4>
													</div>
													<div class="modal-body material-modal__body">
														<form action="<?php getSiteURL(); ?>projects.php?page=open" method="get" target="_blank" http-equiv="Content-type" enctype="multipart/form-data"; charset="utf-8" class="form-horizontal">
														<input type="hidden" name="page" value="open" >
														<input type="hidden" name="id" value="<?php echo $project_id; ?>" >
														<input type="hidden" name="search" value="yes" >
														<div class="form-group">
															<label for="kki_naziv_web" class="col-sm-4 control-label">Starost:</label>
															<div class="col-sm-8">
																<div class="row">
																	<div class="col-sm-4">
																		<div class="materail-input-block materail-input-block_success">
																			<input class="form-control materail-input" type="number" name="starost_od" id="starost_od" placeholder="od" value="16">
																			<span class="materail-input-block__line"></span>
																		</div>
																	</div>
																	<div class="col-sm-4">
																		<div class="materail-input-block materail-input-block_success">
																			<input class="form-control materail-input" type="number" name="starost_do" id="starost_do" placeholder="do" value="80">
																			<span class="materail-input-block__line"></span>
																		</div>
																	</div>
																</div>
															</div>
														</div>

														<div class="form-group">
															<label for="kki_naziv_web" class="col-sm-4 control-label">Vozačka dozvola:</label>
															<div class="col-sm-8">
																<div class="main-container__column materail-switch materail-switch_primary">
																	<input class="materail-switch__element" type="checkbox" id="switch_input1" name="vozacka_dozvola" value="DA">
																	<label class="materail-switch__label" for="switch_input1"></label>
																</div>
															</div>
														</div>
														<div class="form-group">
															<label for="kki_naziv_web" class="col-sm-4 control-label">Radno iskustvo:</label>
															<div class="col-sm-8">
																<div class="main-container__column materail-switch materail-switch_primary">
																	<input class="materail-switch__element" type="checkbox" id="switch_input2" name="radno_iskustvo" value="DA">
																	<label class="materail-switch__label" for="switch_input2"></label>
																</div>
															</div>
														</div>
														<div class="form-group">
															<label for="kki_naziv_web" class="col-sm-4 control-label">Znanje njemačkog:</label>
															<div class="col-sm-5">
																<select class="selectpicker" id="kj_znanje_njemacki" name="kj_znanje_njemacki">
																	<option value="Bezznanja" selected>Bez znanja</option>
																	<option value="A1">A1</option>
																	<option value="A2">A2</option>
																	<option value="B1">B1</option>
																	<option value="B2">B2</option>
																	<option value="C1">C1</option>
																	<option value="C2">C2</option>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label for="filter_grupe" class="col-sm-4 control-label">Grupe:</label>
															<div class="col-sm-5">
																<select class="selectpicker" id="filter_grupe" name="filter_grupe[]" multiple>
																	<?php getGroupList(); ?>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label for="filter_status" class="col-sm-4 control-label">Status:</label>
															<div class="col-sm-5">
																<select class="selectpicker" id="filter_status" name="filter_status[]" multiple>
																	<?php getStatusList(); ?>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label for="filter_drzavljanstvo" class="col-sm-4 control-label">Državljanstvo:</label>
															<div class="col-sm-5">
																<select class="selectpicker" id="filter_drzavljanstvo" name="filter_drzavljanstvo[]" multiple>
																	<option value="EU">EU</option>
																	<option value="NON-EU">NON-EU</option>
																</select>
															</div>
														</div>

													</div>
													<div class="modal-footer material-modal__footer">
														<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
														<a id="archive_link" href=""><button class="btn btn-primary material-btn material-btn_success">TRAŽI</button></a>

														</form>
													</div>
												</div>
											</div>
										</div>			
										<div class="row">
											<div class="col-md-12">
												<div class="content_box">
													<div class="row">
														<div class="col-xs-12 text-right">
															<button data-toggle="modal" data-target="#filterKandidati" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-search" aria-hidden="true"></i> <span>FILTER</span></button>
															<?php if($archive_status == 0){ ?>
															<?php if($getEmployeeStatus == 1){ ?>
										
										
															<a href="<?php getSiteURL(); ?>do.php?form=show_candidate_archive&enable=1">
																<button class="btn material-btn material-btn-icon-success material-btn main-container__column material-btn-icon-responsive"><i class="fa fa-times" aria-hidden="true"></i> ARHIVA</button>
															</a>
															<?php } ?>
															<?php }else{ ?>
															<?php if($getEmployeeStatus == 1){ ?>
										
															<a href="<?php getSiteURL(); ?>do.php?form=show_candidate_archive&enable=0">
																<button class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-check" aria-hidden="true"></i> ARHIVA</button>
															</a>
															<?php } ?>
															<?php } ?>

														</div>
													</div>
													
													
													<hr>
													<script type="text/javascript">
														$(document).ready(function() {
															$('#idk_table_kandidati').DataTable({

																responsive: true,

																"order": [[ 0, "desc" ]],

																"bAutoWidth": false,

																"aoColumns": [
																		{ "width": "5%" },
																		{ "width": "5%", "bSortable": false },
																		{ "width": "25%" },
																		{ "width": "15%" },
																		{ "width": "20%" },
																		{ "width": "10%" },
																		{ "width": "10%" },
																		{ "width": "10%", "bSortable": false }
																	]
															});
														} );
													</script>
													<table id="idk_table_kandidati" class="display" cellspacing="0" width="100%">
																<thead>
																	<tr>
																		<th></th>
																		<th></th>
																		<th>Ime i prezime</th>
																		<th>URL</th>
																		<th>Prijava za</th>
																		<th class="text-center">Grupa</th>
																		<th class="text-center">Status</th>
																		<th></th>
																	</tr>
																</thead>
														<tbody>
															<?php
																$query_get_kandidati = $db->prepare("
																								SELECT kandidat_id, kandidat_ime, kandidat_prezime, kandidat_spol, kandidat_jmbg, kandidat_status, kandidat_slika, kandidat_email, kandidat_datetime, kandidat_visitedurl, kandidat_prijava_na, kandidat_group
																								FROM idk_kandidati
																								INNER JOIN idk_project_kandidati ON idk_kandidati.kandidat_id = idk_project_kandidati.pk_kandidatid
																								WHERE pk_projectid = :pk_projectid $uslov_glavni
																								");

																$query_get_kandidati->execute(array(':pk_projectid' => $project_id));

																while($row_get_kandidati = $query_get_kandidati->fetch()){

																	
																	$kandidat_id = $row_get_kandidati['kandidat_id'];
																	$kandidat_ime = $row_get_kandidati['kandidat_ime'];
																	$kandidat_prezime = $row_get_kandidati['kandidat_prezime'];
																	$kandidat_spol = $row_get_kandidati['kandidat_spol'];
																	$kandidat_jmbg = $row_get_kandidati['kandidat_jmbg'];
																	$kandidat_group = $row_get_kandidati['kandidat_group'];
																	$kandidat_email = $row_get_kandidati['kandidat_email'];
																	$kandidat_datetime = $row_get_kandidati['kandidat_datetime'];
																	$kandidat_visitedurl = $row_get_kandidati['kandidat_visitedurl'];
																	$kandidat_prijava_na = $row_get_kandidati['kandidat_prijava_na'];


																	// PROVJERA DA LI RADNIK IMA RADNO ISKUSTVO
																	$check_work_experience = $db->prepare("
																						SELECT kri_id
																						FROM idk_kandidat_radno_iskustvo
																						WHERE kri_kandidat_id = :kri_kandidat_id");

																	$check_work_experience->execute(array(
																					':kri_kandidat_id' => $kandidat_id));

																	$check_work = $check_work_experience->rowCount();
																	// END PROVJERA DA LI RADNIK IMA RADNO ISKUSTVO


																	// PROVJERA ZNANJA NJEMACKOG JEZIKA
																	$check_german_knowlege = $db->prepare("
																						SELECT kj_id, kj_naziv, kj_slusanje, kj_citanje, kj_govorna_interakcija, kj_govorna_produkcija, kj_pisanje
																						FROM idk_kandidat_jezici
																						WHERE kj_kandidatid = :kj_kandidatid AND kj_naziv = :kj_naziv AND kj_slusanje IN ($kj_znanje_njemacki)");

																	$check_german_knowlege->execute(array(
																					':kj_kandidatid' => $kandidat_id,
																					':kj_naziv' => "Njemački"
																					));

																	$check_german = $check_german_knowlege->rowCount();
																	// PROVJERA ZNANJA NJEMACKOG JEZIKA


																	$query_urls = $db->prepare("
																					SELECT lg_id, lg_url, lg_desc, lg_datetime
																					FROM idk_link_generator
																					WHERE lg_id = $kandidat_visitedurl
																					");

																	$query_urls->execute();
																	$url = $query_urls->fetch();

																		$lg_id = $url['lg_id'];
																		$lg_url = $url['lg_url'];
																		$lg_desc = $url['lg_desc'];


																	if($row_get_kandidati['kandidat_slika'] == "none"){
																		$kandidat_slika = "none.jpg";
																	}else{
																		$kandidat_slika = $row_get_kandidati['kandidat_slika'];
																	}

																	if($row_get_kandidati['kandidat_status'] == 0){
																		$kandidat_status = '<span class="label label-warning material-label material-label_warning material-label_xs main-container__column">Na provjeri</span>';
																	}elseif($row_get_kandidati['kandidat_status'] == 1){
																		$kandidat_status = '<span class="label label-primary material-label material-label_primary material-label_xs main-container__column">U obradi</span>';
																	}elseif($row_get_kandidati['kandidat_status'] == 2){
																		$kandidat_status = '<span class="label label-success material-label material-label_success material-label_xs main-container__column">Obrađen</span>';
																	}elseif($row_get_kandidati['kandidat_status'] == 3){
																		$kandidat_status = '<span class="label label-danger material-label material-label_danger material-label_xs main-container__column">Arhiviran</span>';
																	}

																	$query_group = $db->prepare("
																					SELECT kg_id, kg_title, kg_date
																					FROM idk_kandidati_grupe
																					WHERE kg_id = :kg_id
																					ORDER BY kg_id ASC
																					");

																	$query_group->execute(array(
																		":kg_id" => $kandidat_group
																	));

																	$griup = $query_group->fetch();
																		$kg_title = $griup['kg_title'];										
																	
															?>
																	<?php if(!isset($_GET['search'])){ ?>
																	<tr>
																		<td class="text-center"><?php echo $kandidat_id; ?></td>
																		<td class="text-center"><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>"><img class="idk_profile_img" src="<?php getSiteURL(); ?>files/kandidati/<?php echo $kandidat_slika; ?>"></a></td>
																		<td><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>"><?php echo $kandidat_ime; ?> <?php if($getEmployeeStatus == 1){ ?><?php echo $kandidat_prezime; ?><?php } ?></a></td>
																		<td><?php if($getEmployeeStatus == 1){ ?><?php echo $lg_url; ?><?php } ?></td>
																		<td><?php echo $kandidat_prijava_na; ?></td>
																		<td class="text-center"><?php if($getEmployeeStatus == 1){ ?><?php echo '<span class="label label-success material-label material-label_success material-label_xs main-container__column">'.$kg_title.'</span>'; ?><?php } ?></td>
																		<td class="text-center"><?php echo $kandidat_status; ?></td>
																		<td class="text-center">
																			<div class="btn-group material-btn-group">
																				<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
																				<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
																					<li><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori</a></li>
																					<?php if(getEmployeeStatus() == 1){ ?>
																					<li><a href="#" class="material-dropdown-menu__link cvshow" data="<?php getSiteURL(); ?>cv?page=open&id=<?php echo $kandidat_id; ?>" data-id="<?php getSiteURL(); ?>cv2?page=open&id=<?php echo $kandidat_id; ?>" data-toggle="modal" data-target="#cvModal"><i class="fa fa-folder-open-o" aria-hidden="true"></i> CV</a></li>
																					<?php }else{} ?>

																					<li><a href="#" class="material-dropdown-menu__link profilshow" data="<?php getSiteURL(); ?>profil?page=open&id=<?php echo $kandidat_id; ?>" data-id="<?php getSiteURL(); ?>profil2?page=open&id=<?php echo $kandidat_id; ?>" data-toggle="modal" data-target="#profilModal"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Profil</a></li>

																					<?php if(getEmployeeStatus() == 1){ ?>
																					<li class="idk_dropdown_danger"><a href="#" data="<?php getSiteURL(); ?>kandidati?page=archive&id=<?php echo $kandidat_id; ?>" data-toggle="modal" data-target="#archiveModal" class="archive material-dropdown-menu__link"><i class="fa fa-trash-o" aria-hidden="true"></i> Arhiviraj</a></li>
																					<?php }else{} ?>

																				</ul>
																			</div>
																		</td>
																	</tr>
																	<?php }else{ ?>
																	<?php if($_GET['kj_znanje_njemacki'] != "Bezznanja"){ ?>
																	<?php if($check_german > 0 AND $radno_iskustvo == "NE"){  ?>
																	<tr>
																		<td class="text-center"><?php echo $i++; ?></td>
																		<td class="text-center"><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>"><img class="idk_profile_img" src="<?php getSiteURL(); ?>files/kandidati/<?php echo $kandidat_slika; ?>"></a></td>
																		<td><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>"><?php echo $kandidat_ime; ?> <?php if($getEmployeeStatus == 1){ ?><?php echo $kandidat_prezime; ?><?php } ?></a></td>
																		<td><?php if($getEmployeeStatus == 1){ ?><?php echo $lg_url; ?><?php } ?></td>
																		<td><?php echo $kandidat_prijava_na; ?></td>
																		<td class="text-center"><?php if($getEmployeeStatus == 1){ ?><?php echo '<span class="label label-success material-label material-label_success material-label_xs main-container__column">'.$kg_title.'</span>'; ?><?php } ?></td>
																		<td class="text-center"><?php echo $kandidat_status; ?></td>
																		<td class="text-center">
																			<div class="btn-group material-btn-group">
																				<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
																				<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
																					<li><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori</a></li>
																					<?php if(getEmployeeStatus() == 1){ ?>
																					<li><a href="#" class="material-dropdown-menu__link cvshow" data="<?php getSiteURL(); ?>cv?page=open&id=<?php echo $kandidat_id; ?>" data-id="<?php getSiteURL(); ?>cv2?page=open&id=<?php echo $kandidat_id; ?>" data-toggle="modal" data-target="#cvModal"><i class="fa fa-folder-open-o" aria-hidden="true"></i> CV</a></li>
																					<?php }else{} ?>

																					<li><a href="#" class="material-dropdown-menu__link profilshow" data="<?php getSiteURL(); ?>profil?page=open&id=<?php echo $kandidat_id; ?>" data-id="<?php getSiteURL(); ?>profil2?page=open&id=<?php echo $kandidat_id; ?>" data-toggle="modal" data-target="#profilModal"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Profil</a></li>

																					<?php if(getEmployeeStatus() == 1){ ?>
																					<li class="idk_dropdown_danger"><a href="#" data="<?php getSiteURL(); ?>kandidati?page=archive&id=<?php echo $kandidat_id; ?>" data-toggle="modal" data-target="#archiveModal" class="archive material-dropdown-menu__link"><i class="fa fa-trash-o" aria-hidden="true"></i> Arhiviraj</a></li>
																					<?php }else{} ?>

																				</ul>
																			</div>
																		</td>
																	</tr>
																	<?php }else if($check_german > 0 AND $radno_iskustvo == "DA") { ?>
																	<?php if($check_work > 0){ ?>
																	<tr>
																		<td class="text-center"><?php echo $i++; ?></td>
																		<td class="text-center"><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>"><img class="idk_profile_img" src="<?php getSiteURL(); ?>files/kandidati/<?php echo $kandidat_slika; ?>"></a></td>
																		<td><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>"><?php echo $kandidat_ime; ?> <?php if($getEmployeeStatus == 1){ ?><?php echo $kandidat_prezime; ?><?php } ?></a></td>
																		<td><?php if($getEmployeeStatus == 1){ ?><?php echo $lg_url; ?><?php } ?></td>
																		<td><?php echo $kandidat_prijava_na; ?></td>
																		<td class="text-center"><?php if($getEmployeeStatus == 1){ ?><?php echo '<span class="label label-success material-label material-label_success material-label_xs main-container__column">'.$kg_title.'</span>'; ?><?php } ?></td>
																		<td class="text-center"><?php echo $kandidat_status; ?></td>
																		<td class="text-center">
																			<div class="btn-group material-btn-group">
																				<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
																				<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
																					<li><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori</a></li>
																					<?php if(getEmployeeStatus() == 1){ ?>
																					<li><a href="#" class="material-dropdown-menu__link cvshow" data="<?php getSiteURL(); ?>cv?page=open&id=<?php echo $kandidat_id; ?>" data-id="<?php getSiteURL(); ?>cv2?page=open&id=<?php echo $kandidat_id; ?>" data-toggle="modal" data-target="#cvModal"><i class="fa fa-folder-open-o" aria-hidden="true"></i> CV</a></li>
																					<?php }else{} ?>

																					<li><a href="#" class="material-dropdown-menu__link profilshow" data="<?php getSiteURL(); ?>profil?page=open&id=<?php echo $kandidat_id; ?>" data-id="<?php getSiteURL(); ?>profil2?page=open&id=<?php echo $kandidat_id; ?>" data-toggle="modal" data-target="#profilModal"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Profil</a></li>

																					<?php if(getEmployeeStatus() == 1){ ?>
																					<li class="idk_dropdown_danger"><a href="#" data="<?php getSiteURL(); ?>kandidati?page=archive&id=<?php echo $kandidat_id; ?>" data-toggle="modal" data-target="#archiveModal" class="archive material-dropdown-menu__link"><i class="fa fa-trash-o" aria-hidden="true"></i> Arhiviraj</a></li>
																					<?php }else{} ?>

																				</ul>
																			</div>
																		</td>
																	</tr>
																	<?php }else{} ?>


																	<?php }else{} ?>

																	<?php }else{ ?>
																	<?php if($radno_iskustvo == "NE"){  ?>
																	<tr>
																		<td class="text-center"><?php echo $i++; ?></td>
																		<td class="text-center"><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>"><img class="idk_profile_img" src="<?php getSiteURL(); ?>files/kandidati/<?php echo $kandidat_slika; ?>"></a></td>
																		<td><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>"><?php echo $kandidat_ime; ?> <?php if($getEmployeeStatus == 1){ ?><?php echo $kandidat_prezime; ?><?php } ?></a></td>
																		<td><?php if($getEmployeeStatus == 1){ ?><?php echo $lg_url; ?><?php } ?></td>
																		<td><?php echo $kandidat_prijava_na; ?></td>
																		<td class="text-center"><?php if($getEmployeeStatus == 1){ ?><?php echo '<span class="label label-success material-label material-label_success material-label_xs main-container__column">'.$kg_title.'</span>'; ?><?php } ?></td>
																		<td class="text-center"><?php echo $kandidat_status; ?></td>
																		<td class="text-center">
																			<div class="btn-group material-btn-group">
																				<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
																				<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
																					<li><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori</a></li>
																					<?php if(getEmployeeStatus() == 1){ ?>
																					<li><a href="#" class="material-dropdown-menu__link cvshow" data="<?php getSiteURL(); ?>cv?page=open&id=<?php echo $kandidat_id; ?>" data-id="<?php getSiteURL(); ?>cv2?page=open&id=<?php echo $kandidat_id; ?>" data-toggle="modal" data-target="#cvModal"><i class="fa fa-folder-open-o" aria-hidden="true"></i> CV</a></li>
																					<?php }else{} ?>

																					<li><a href="#" class="material-dropdown-menu__link profilshow" data="<?php getSiteURL(); ?>profil?page=open&id=<?php echo $kandidat_id; ?>" data-id="<?php getSiteURL(); ?>profil2?page=open&id=<?php echo $kandidat_id; ?>" data-toggle="modal" data-target="#profilModal"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Profil</a></li>

																					<?php if(getEmployeeStatus() == 1){ ?>
																					<li class="idk_dropdown_danger"><a href="#" data="<?php getSiteURL(); ?>kandidati?page=archive&id=<?php echo $kandidat_id; ?>" data-toggle="modal" data-target="#archiveModal" class="archive material-dropdown-menu__link"><i class="fa fa-trash-o" aria-hidden="true"></i> Arhiviraj</a></li>
																					<?php }else{} ?>

																				</ul>
																			</div>
																		</td>
																	</tr>
																	<?php }else if($radno_iskustvo == "DA") { ?>
																	<?php if($check_work > 0){ ?>
																	<tr>
																		<td class="text-center"><?php echo $i++; ?></td>
																		<td class="text-center"><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>"><img class="idk_profile_img" src="<?php getSiteURL(); ?>files/kandidati/<?php echo $kandidat_slika; ?>"></a></td>
																		<td><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>"><?php echo $kandidat_ime; ?> <?php if($getEmployeeStatus == 1){ ?><?php echo $kandidat_prezime; ?><?php } ?></a></td>
																		<td><?php if($getEmployeeStatus == 1){ ?><?php echo $lg_url; ?><?php } ?></td>
																		<td><?php echo $kandidat_prijava_na; ?></td>
																		<td class="text-center"><?php if($getEmployeeStatus == 1){ ?><?php echo '<span class="label label-success material-label material-label_success material-label_xs main-container__column">'.$kg_title.'</span>'; ?><?php } ?></td>
																		<td class="text-center"><?php echo $kandidat_status; ?></td>
																		<td class="text-center">
																			<div class="btn-group material-btn-group">
																				<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
																				<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
																					<li><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori</a></li>
																					<?php if(getEmployeeStatus() == 1){ ?>
																					<li><a href="#" class="material-dropdown-menu__link cvshow" data="<?php getSiteURL(); ?>cv?page=open&id=<?php echo $kandidat_id; ?>" data-id="<?php getSiteURL(); ?>cv2?page=open&id=<?php echo $kandidat_id; ?>" data-toggle="modal" data-target="#cvModal"><i class="fa fa-folder-open-o" aria-hidden="true"></i> CV</a></li>
																					<?php }else{} ?>

																					<li><a href="#" class="material-dropdown-menu__link profilshow" data="<?php getSiteURL(); ?>profil?page=open&id=<?php echo $kandidat_id; ?>" data-id="<?php getSiteURL(); ?>profil2?page=open&id=<?php echo $kandidat_id; ?>" data-toggle="modal" data-target="#profilModal"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Profil</a></li>

																					<?php if(getEmployeeStatus() == 1){ ?>
																					<li class="idk_dropdown_danger"><a href="#" data="<?php getSiteURL(); ?>kandidati?page=archive&id=<?php echo $kandidat_id; ?>" data-toggle="modal" data-target="#archiveModal" class="archive material-dropdown-menu__link"><i class="fa fa-trash-o" aria-hidden="true"></i> Arhiviraj</a></li>
																					<?php }else{} ?>

																				</ul>
																			</div>
																		</td>
																	</tr>
																	<?php }else{} ?>


																	<?php }else{} ?>
																	<?php } ?>

																	<?php } ?>
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
				</div>
				
				<?php
				break;

				case "archive":
					if($getEmployeeStatus == 1){

						$project_id = $_GET['id'];

						//Get
						$query_select = $db->prepare("
												SELECT project_name
												FROM idk_projects
												WHERE project_id = :project_id");

						$query_select->execute(array(
											':project_id' => $project_id));

						$row_select = $query_select->fetch();

						$project_name = $row_select['project_name'];

						//Save
						$query = $db->prepare("
										UPDATE idk_projects
										SET project_status = :project_status
										WHERE project_id = :project_id");

						$query->execute(array(
									':project_status' => 0,
									':project_id' => $project_id));

						//Add to LOGS
						$log_desc = "Arhivirao projekat: " . $project_name . "";
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


						header("Location: " . getSiteURLr() . "projects?page=list&mess=4");

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

				case "del_doc":
					if($getEmployeeStatus == 1){

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

							unlink("files/files/projects/" . $document_file);

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

						header("Location: " . getSiteURLr() . "projects2?page=open&id=$document_dataid&mess=2");

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
					if($getEmployeeStatus == 1){

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

						//Delete note from db
						$note_del_query = $db->prepare("
													DELETE FROM idk_notes
													WHERE note_id = :note_id");

						$note_del_query->execute(array(
											':note_id' => $note_id));

						header("Location: " . getSiteURLr() . "projects2?page=open&id=$note_dataid&mess=4");

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
					if($getEmployeeStatus == 1){

						$pt_id = $_GET['id'];

						//Get
						$query_select = $db->prepare("
												SELECT pt_name, pt_projectid
												FROM idk_projects_tasks
												WHERE pt_id = :pt_id");

						$query_select->execute(array(
											':pt_id' => $pt_id));

						$row_select = $query_select->fetch();

						$pt_name = $row_select['pt_name'];
						$pt_projectid = $row_select['pt_projectid'];

						//Add to LOGS
						$log_desc = "Obrisao projektni zadatak: " . $pt_name . "";
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
						$del_query = $db->prepare("
											DELETE FROM idk_projects_tasks
											WHERE pt_id = :pt_id");

						$del_query->execute(array(
											':pt_id' => $pt_id));


						header("Location: " . getSiteURLr() . "projects?page=open&id=$pt_projectid");

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

	<script>
		$( document ).ajaxComplete(function() {
			$(".cvshow").click(function () {
				var addressValueBs = $(this).attr("data");
				document.getElementById("cv_bosanski").href = addressValueBs;
			
				var addressValueDe = $(this).data("id");
				document.getElementById("cv_njemacki").href = addressValueDe;
			});
		});
		$( document ).ajaxComplete(function() {
			$(".profilshow").click(function () {
				var addressValueBs = $(this).attr("data");
				document.getElementById("profil_bosanski").href = addressValueBs;

				var addressValueDe = $(this).data("id");
				document.getElementById("profil_njemacki").href = addressValueDe;
			});
		});
	
	</script>

	<!-- Modal CV -->
	<div class="modal material-modal material-modal_success fade" id="cvModal">
		<div class="modal-dialog">
			<div class="modal-content material-modal__content">
				<div class="modal-header material-modal__header">
					<button class="close material-modal__close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title material-modal__title">Izaberi jezik CV-a</h4>
				</div>
				<div class="modal-body material-modal__body text-center">
					<a id="cv_bosanski" href="" target="_BLANK"><button class="btn btn-primary material-btn material-btn_success"><img src="<?php getSiteUrl(); ?>images/BosniaHerzegowina.png" width="50">&nbsp &nbsp BOSANSKI</button></a>

					<a id="cv_njemacki" href="" target="_BLANK"><button class="btn btn-primary material-btn material-btn_success"><img src="<?php getSiteUrl(); ?>images/Germany.png" width="50">&nbsp &nbsp NJEMAČKI</button></a>
				</div>
				<div class="modal-footer material-modal__footer">
					<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal profil -->
	<div class="modal material-modal material-modal_success fade" id="profilModal">
		<div class="modal-dialog">
			<div class="modal-content material-modal__content">
				<div class="modal-header material-modal__header">
					<button class="close material-modal__close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title material-modal__title">Izaberi jezik Profila</h4>
				</div>
				<div class="modal-body material-modal__body text-center">
					<a id="profil_bosanski" href="" target="_BLANK"><button class="btn btn-primary material-btn material-btn_success"><img src="<?php getSiteUrl(); ?>images/BosniaHerzegowina.png" width="50">&nbsp &nbsp BOSANSKI</button></a>

					<a id="profil_njemacki" href="" target="_BLANK"><button class="btn btn-primary material-btn material-btn_success"><img src="<?php getSiteUrl(); ?>images/Germany.png" width="50">&nbsp &nbsp NJEMAČKI</button></a>
				</div>
				<div class="modal-footer material-modal__footer">
					<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
				</div>
			</div>
		</div>
	</div>	
</body>
</html>
