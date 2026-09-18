<?php
	include("includes/functions.php");
	include("includes/common.php");

	$getEmployeeStatus = explode( ',' , getEmployeeStatus());

	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: warehouse?page=list");
	}
	$query_skladistar = $db->prepare("
					SELECT employee_id, employee_warehouse
					FROM idk_employees
					WHERE employee_id = :employee_id
					");
	$query_skladistar->execute(array(
				':employee_id' => $logged_employee_id));
				
	$rowSkladistar = $query_skladistar->fetch();
		$employee_warehouse = $rowSkladistar['employee_warehouse'];
		
	if($employee_warehouse == 1){
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
					<h1><i class="fa fa-archive idk_color_green" aria-hidden="true"></i> Skladište</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>warehouse?page=add" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj</span></a>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div id="myTabs2" class="panel-group material-tabs-group">
				<ul class="nav nav-tabs material-tabs material-tabs_primary">
					<li class="active"><a href="#info" class="material-tabs__tab-link" data-toggle="tab">Lista</a></li>
					<li><a href="#branches" class="material-tabs__tab-link" data-toggle="tab">Poslovnice</a></li>
					<li><a href="#logs" class="material-tabs__tab-link" data-toggle="tab">Logovi</a></li>
					<li><a href="#categories" class="material-tabs__tab-link" data-toggle="tab">Kategorije</a></li>
				</ul>
				<div class="tab-content materail-tabs-content" style="padding: 0">
					<div class="tab-pane fade active in" id="info">
						<div class="row">
							<div class="col-md-12">
								<div class="content_box">
									<div class="row">
										<div class="col-xs-9">
										
										</div>
										<div class="col-xs-3">
											<form action="<?php getSiteURL(); ?>warehouse?page=list" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
												<div class="form-group">
													<div class="col-sm-8">
														<select class="selectpicker" id="filter_poslovnice" data-live-search="true" name="filter_poslovnice[]" multiple>
															<?php
																if(isset($_POST['filter_poslovnice'])){
																	$filter_poslovnice = $_POST['filter_poslovnice'];
																	$filter_poslovnice_e = implode(",", $filter_poslovnice);
																	$uslov_za_skladiste = "AND skl_branch IN (".$filter_poslovnice_e.")";
																}else{
																	$filter_poslovnice = "";
																	$uslov_za_skladiste = "";
																	$filter_poslovnice_e = [];
																}																
															
																$query_filter = $db->prepare("
																				SELECT branch_id, branch_name, branch_state, branch_city
																				FROM idk_poslovnice
																				");
		
																$query_filter->execute();
		
																while($rowF = $query_filter->fetch()){
		
																	$branch_id = $rowF['branch_id'];
																	$branch_name = $rowF['branch_name'];
																	$branch_state = $rowF['branch_state'];
																	$branch_city = $rowF['branch_city'];													
															?>
															<option value="<?php echo $branch_id; ?>" <?php if (in_array($branch_id, $filter_poslovnice_e)){echo "selected";}else{} ?>><?php echo $branch_name; ?></option>
															<?php } ?>
														</select>
													</div>
													<div class="col-sm-4">
														<button class="btn material-btn  material-btn_success main-container__column material-btn-icon-responsive">FILTER</button>
													</div>	
												</div>	
											</form>
										</div>
									</div>
										<hr/>
									<div class="row">
										<div class="col-xs-12">
											<?php
												if(isset($_GET['mess'])) {
													$mess = $_GET['mess'];
												}else{
													$mess = 0;
												}

												if($mess == 1){
													echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novu stavku u skladište.</div>';
												}elseif($mess == 2){
													echo '<div class="alert material-alert material-alert_danger">Greška: Korisnik kojeg pokuštavate dodati već postoji u bazi podataka.</div>';
												}elseif($mess == 3){
													echo '<div class="alert material-alert material-alert_success">Uspješno ste uredili stavku u skladištu.</div>';
												}elseif($mess == 4){
													echo '<div class="alert material-alert material-alert_success">Uspješno ste arhivirali stavku u skladištu.</div>';
												}elseif($mess == 5){
												?>
												<script>$(function() { $('[href="#branches"]').tab('show'); });</script>
												<?php
													echo '<div class="alert material-alert material-alert_success">Uspješno ste arhivirali stavku u skladištu.</div>';
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
																{ "width": "15%" },
																{ "width": "25%" },
																{ "width": "12%" },
																{ "width": "10%" },
																{ "width": "10%" },
																{ "width": "8%" },
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
														<th>Naziv</th>
														<th>Opis</th>
														<th>Kategorija</th>
														<th>Datum nabavke</th>
														<th class="text-center">Poslovnica</th>
														<th class="text-center">Ukupno</th>
														<th class="text-center">Dostupno</th>
														<th></th>
													</tr>
												</thead>
												<tbody>
													<?php
														$query = $db->prepare("
																		SELECT skl_id, skl_title, skl_desc, skl_available, skl_image, skl_branch, skl_kategorija, skl_datum_nabavke
																		FROM idk_skladiste
																		WHERE skl_archive = 0 $uslov_za_skladiste");

														$query->execute();

														while($row = $query->fetch()){

															$skl_id = $row['skl_id'];
															$skl_title = $row['skl_title'];
															$skl_desc = $row['skl_desc'];
															$skl_available = $row['skl_available'];
															$skl_image = $row['skl_image'];
															$skl_branch = $row['skl_branch'];
															$skl_kategorija = $row['skl_kategorija'];
															$skl_datum_nabavke = $row['skl_datum_nabavke'];
															
														//	if($skl_branch == 1){
														//		$skl_branch_txt = '<span class="label label-success">Jobstep Bihac</span>';
														//	}else if ($skl_branch == 2){
														//		$skl_branch_txt = '<span class="label label-warning">Jobstep Sarajevo</span>';
														//	}else{
														//		
														//	}
															
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
																$branch_id = $rowb['branch_id'];
																$branch_name = $rowb['branch_name'];
																$branch_state = $rowb['branch_state'];
																$branch_city = $rowb['branch_city'];	
															
															// GET CATEGORY INFO
															$query_cat = $db->prepare("
																			SELECT isk_naziv_kategorije
																			FROM idk_skladiste_kategorije
																			WHERE isk_id = :isk_id
																			");

															$query_cat->execute(array(
																":isk_id" => $skl_kategorija
															));
															
															$rowcat = $query_cat->fetch();
															$naziv_kategorije = $rowcat['isk_naziv_kategorije'];
															
															if($skl_image != "none"){
																$skl_image_txt = $skl_image;
															}else{
																$skl_image_txt = "box.png";
															}
															
															if($skl_available > 0){
																$skl_available_txt = '<span class="label label-success">'.$skl_available.'</span>';
															}else{
																$skl_available_txt = '<span class="label label-warning">'.$skl_available.'</span>';
															}
															
															
															$query_zaduzeno = $db->prepare("
																			SELECT SUM(sz_quantity) AS broj_zaduzeno
																			FROM idk_skladiste_zaposlenici
																			WHERE isz_sklid = :isz_sklid AND (isz_status = 1 OR isz_status = 2)");
										
															$query_zaduzeno->execute(array(
																		':isz_sklid' => $skl_id));
										
															$rowz = $query_zaduzeno->fetch();
										
															$broj_zaduzeno = $rowz['broj_zaduzeno'];
															$dostupno = $skl_available - $broj_zaduzeno;													

															if($dostupno > 0){
																$skl_available_txt = '<span class="label label-success">'.$dostupno.'</span>';
															}else{
																$skl_available_txt = '<span class="label label-danger">'.$dostupno.'</span>';
															}
															
															if($skl_datum_nabavke !== null)
																$datum_nabavke = date("Y.m.d", strtotime($skl_datum_nabavke));
															else
																$datum_nabavke = null;


													?>
													<tr>
														<td class="text-center"><a href="<?php getSiteURL(); ?>warehouse?page=open&id=<?php echo $skl_id; ?>"><img class="idk_profile_img" src="<?php getSiteURL(); ?>images/<?php echo $skl_image_txt; ?>"></a></td>
														<td><a href="<?php getSiteURL(); ?>warehouse?page=open&id=<?php echo $skl_id; ?>"><?php echo $skl_title; ?></a></td>
														<td><a href="<?php getSiteURL(); ?>warehouse?page=open&id=<?php echo $skl_id; ?>"><?php echo $skl_desc; ?></a></td>
														<td><?php echo $naziv_kategorije; ?></td>
														<td><?php echo $datum_nabavke; ?></td>
														<td class="text-center"><a href="<?php getSiteUrl(); ?>warehouse?page=edit_branch&id=<?php echo $skl_branch; ?>" target="_BLANK"><span class="label label-success"><?php echo $branch_name; ?></span></a></td>
														<td class="text-center"><a href=""><span class="label label-success"><?php echo $skl_available; ?></span></a></td>
														<td class="text-center"><?php echo $skl_available_txt; ?></td>
														<td class="text-center">
															<div class="btn-group material-btn-group">
																<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
																<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
																	<li><a href="<?php getSiteURL(); ?>warehouse?page=open&id=<?php echo $skl_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori</a></li>
																	<li><a href="<?php getSiteURL(); ?>warehouse?page=edit&id=<?php echo $skl_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Uredi</a></li>
																	<?php if($broj_zaduzeno == 0){ ?><li class="idk_dropdown_danger"><a href="#" data-productname="<?php echo $skl_title; ?>" data="<?php getSiteURL(); ?>warehouse?page=archive&id=<?php echo $skl_id; ?>" data-toggle="modal" data-target="#archiveModal" class="archive material-dropdown-menu__link"><i class="fa fa-trash-o" aria-hidden="true"></i> Arhiviraj</a></li>
																	<?php } else { ?> <li class="idk_dropdown_danger"><a href="#" data-toggle="modal" data-target="#warningModal" class="material-dropdown-menu__link"><i class="fa fa-trash-o" aria-hidden="true"></i> Arhivirajj</a></li>
																	<?php } ?>
																</ul>
															</div>
														</td>
													</tr>
													<?php } ?>
													<script>
														$(".archive").click(function () {
															var addressValue = $(this).attr("data");
															var productname = $(this).data("productname");
															$('#nazivstavke').html(productname);
														
															document.getElementById("archive_link").href = addressValue;
														});
													</script>
												</tbody>
											</table>
											<!-- Modal warning za arhiviranje-->
													<div class="modal material-modal material-modal_danger fade" id="warningModal">
														<div class="modal-dialog">
															<div class="modal-content material-modal__content">
																<div class="modal-header material-modal__header">
																	<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																	<h4 class="modal-title material-modal__title">Upozorenje</h4>
																</div>
																<div class="modal-body material-modal__body">
																	<p>Nije moguće arhivirati stavku dok se ne razduže svi proizvodi! </p>
																</div>
																<div class="modal-footer material-modal__footer">
																	<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
																</div>
															</div>
														</div>
													</div>
											<!-- Modal za arhiviranje-->
											<div class="modal material-modal material-modal_danger fade" id="archiveModal">
												<div class="modal-dialog">
													<div class="modal-content material-modal__content">
														<div class="modal-header material-modal__header">
															<button class="close material-modal__close" data-dismiss="modal">&times;</button>
															<h4 class="modal-title material-modal__title">Arhiviranje</h4>
														</div>
														<div class="modal-body material-modal__body">
															<p>Jeste li sigurni da želite arhivirati stavku: <span id="nazivstavke"></span>?</p>
														</div>
														<div class="modal-footer material-modal__footer">
															<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
															<a id="archive_link" href=""><button class="btn btn-primary material-btn material-btn_danger">ARHIVIRAJ</button></a>
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
					<div class="tab-pane fade" id="branches">
						<div class="row">
							<div class="col-md-12">
								<div class="content_box">
									<div class="row">
										<div class="col-xs-12">
											<a href="<?php getSiteURL(); ?>warehouse?page=add_branch" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive" style="float: right; margin-bottom: 10px;"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj poslovnicu</span></a>
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

												if($mess == 5){
												?>
												<script>$(function() { $('[href="#branches"]').tab('show'); });</script>
												<?php
													echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali poslovnicu.</div>';
												}else if($mess == 6){
												?>
												<script>$(function() { $('[href="#branches"]').tab('show'); });</script>
												<?php
													echo '<div class="alert material-alert material-alert_success">Uspješno ste uredili poslovnicu.</div>';
												}
											?>
											<script type="text/javascript">
												$(document).ready(function() {
													$('#idk_table2').DataTable({

														responsive: true,

														"order": [[ 1, "asc" ]],

														 "bAutoWidth": false,

														"aoColumns": [
																{ "width": "5%" },
																{ "width": "35%"},
																{ "width": "25%" },
																{ "width": "25%" },
																{ "width": "10%" },
															]
													});
												} );
											</script>
											<form action="<?php getSiteURL(); ?>do.php?form=candidates_to_project" name="frm-to-project" id="frm-to-project" method="POST">
												<table id="idk_table2" class="display" cellspacing="0" width="100%">
													<thead>
														<tr>
															<th>#</th>
															<th>Naziv</th>
															<th>Drzava</th>
															<th>Grad</th>
															<th></th>
														</tr>
													</thead>
													<tbody>
														<?php															
															$query = $db->prepare("
																			SELECT branch_id, branch_name, branch_state, branch_city
																			FROM idk_poslovnice
																			");

															$query->execute();

															$sumCOunt = 1;
															while($row = $query->fetch()){

																$branch_id = $row['branch_id'];
																$branch_name = $row['branch_name'];
																$branch_state = $row['branch_state'];
																$branch_city = $row['branch_city'];
														?>
														<tr>
															<td><?php echo $sumCOunt++; ?></td>
															<td><?php echo $branch_name; ?></td>
															<td><?php echo $branch_state; ?></td>
															<td><?php echo $branch_city; ?></td>
															<td class="text-center">
																<div class="btn-group material-btn-group">
																	<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
																	<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
																		<li><a href="<?php getSiteURL(); ?>warehouse?page=edit_branch&id=<?php echo $branch_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Uredi</a></li>
																		<!--<li class="idk_dropdown_danger"><a href="#" data="<?php getSiteURL(); ?>warehouse?page=archive&id=<?php echo $branch_id; ?>" data-toggle="modal" data-target="#archiveModal" class="archive material-dropdown-menu__link"><i class="fa fa-trash-o" aria-hidden="true"></i> Arhiviraj</a></li>-->
																	</ul>
																</div>
															</td>
														</tr>
														<?php } ?>
														<script>
															$(".archive").click(function () {
																var addressValue = $(this).attr("data");
																var productname = $(this).data("productname");
																$('#nazivstavke').html(productname);
															
																document.getElementById("archive_link").href = addressValue;
															});
														</script>
													</tbody>
												</table>
											</form>
											<!-- Modal -->
											<div class="modal material-modal material-modal_danger fade" id="archiveModal">
												<div class="modal-dialog">
													<div class="modal-content material-modal__content">
														<div class="modal-header material-modal__header">
															<button class="close material-modal__close" data-dismiss="modal">&times;</button>
															<h4 class="modal-title material-modal__title">Arhiviranje</h4>
														</div>
														<div class="modal-body material-modal__body">
															<p>Jeste li sigurni da želite arhivirati stavku: <span id="nazivstavke"></span>?</p>
														</div>
														<div class="modal-footer material-modal__footer">
															<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
															<a id="archive_link" href=""><button class="btn btn-primary material-btn material-btn_danger">ARHIVIRAJ</button></a>
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
					<div class="tab-pane fade" id="logs">
						<div class="row">
							<div class="col-md-12">
								<div class="content_box">
									<div class="row">
										<div class="col-xs-12">
											<script type="text/javascript">
												$(document).ready(function() {
													$('#idk_table3').DataTable({

														responsive: true,

														"order": [[ 0, "desc" ]],

														 "bAutoWidth": false,

														"aoColumns": [
																{"width": "0%", "bVisible": false},
																{ "width": "20%" },
																{ "width": "60%" },
																{ "width": "10%" }
															]
													});
												} );
											</script>
											<table id="idk_table3" class="display" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th></th>
														<th class="text-center">Datum i vrijeme</th>
														<th>Log opis</th>
														<th>Zaposlenik</th>
													</tr>
												</thead>
												<tbody>
													<?php
														$query = $db->prepare("
																		SELECT log_id, log_desc, log_date, log_type, employee_firstname, employee_lastname
																		FROM idk_logs
																		INNER JOIN idk_employees ON idk_logs.log_employeeid = idk_employees.employee_id
																		WHERE log_type = 1");

														$query->execute();

														while($row = $query->fetch()){

															$log_id = $row['log_id'];
															$log_date = date('d.m.Y. - H:i', strtotime($row['log_date']));
															$log_desc = $row['log_desc'];
															$ime = $row['employee_firstname'];
															$prezime = $row['employee_lastname'];
													?>
													<tr>
														<td><?php echo $log_id; ?></td>
														<td class="text-center"><?php echo $log_date; ?></td>
														<td><?php echo $log_desc; ?></td>
														<td><?php echo $ime; ?> <?php echo $prezime; ?></td>
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
					<div class="tab-pane fade" id="categories">
						<div class="row">
							<div class="col-md-12">
								<div class="content_box">
									<div class="row">
										<div class="col-xs-12">
											<a href="<?php getSiteURL(); ?>warehouse?page=add_category" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon" style="float: right; margin-bottom: 10px;"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj kategoriju</span></a>
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

												if($mess == 7){
												?>
												<script>$(function() { $('[href="#categories"]').tab('show'); });</script>
												<?php
													echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali kategoriju.</div>';
												}else if($mess == 8){
												?>
												<script>$(function() { $('[href="#categories"]').tab('show'); });</script>
												<?php
													echo '<div class="alert material-alert material-alert_success">Uspješno ste uredili kategoriju.</div>';
												}else if($mess == 9){
												?>
												<script>$(function() { $('[href="#categories"]').tab('show'); });</script>
												<?php
													echo '<div class="alert material-alert material-alert_danger">Neuspješan unos, kategorija sa istim imenom već postoji. </div>';
												}
											?>
											<script type="text/javascript">
												$(document).ready(function() {
													$('#idk_table_cat').DataTable({

														responsive: true,

														"order": [[ 1, "asc" ]],

														 "bAutoWidth": false,

														"aoColumns": [
																{ "width": "5%" },
																{ "width": "35%"},
																{ "width": "25%" },
																{ "width": "25%" },
																{ "width": "10%" },
															]
													});
												} );
											</script>
											<table id="idk_table_cat" class="display" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th>#</th>
														<th>Naziv kategorije</th>
														<th>Ukupno</th>
														<th>Dostupno</th>
														<th>Akcija</th>
													</tr>
												</thead>
												<tbody>
													<?php
														$query_cats_first = $db->prepare("
																		SELECT isk_id, isk_naziv_kategorije
																		FROM idk_skladiste_kategorije
														");
														$query_cats_first->execute();
														while($rowcats_first = $query_cats_first->fetch()){
															
															$naziv_kategorije = $rowcats_first['isk_naziv_kategorije'];
															$skk_id = $rowcats_first['isk_id'];
															
															$query_cats = $db->prepare("
																			SELECT isk_id, SUM(skl_available) as cat_ukupno 
																			FROM idk_skladiste_kategorije 
																			JOIN idk_skladiste 
																			ON idk_skladiste_kategorije.isk_id = idk_skladiste.skl_kategorija 
																			WHERE skl_archive = 0 AND skl_kategorija = :skl_kategorija
																			
																			");

															$query_cats->execute(array(
																		':skl_kategorija' => $skk_id
															));

															$sumCOunt = 1;
															while($rowcats = $query_cats->fetch()){
																
																$cat_ukupno = $rowcats['cat_ukupno'];
																
																$query_zaduzeno = $db->prepare("
																				SELECT sum(sz_quantity) as zaduzeno_cat
																				FROM idk_skladiste_zaposlenici 
																				JOIN idk_skladiste 
																				ON idk_skladiste.skl_id = idk_skladiste_zaposlenici.isz_sklid 
																				WHERE skl_kategorija = :skl_kategorija AND (isz_status = 1 OR isz_status = 2) ");

																$query_zaduzeno->execute(array(
																			':skl_kategorija' => $skk_id));

																$rowz = $query_zaduzeno->fetch();
																if($cat_ukupno !== null){
																	$zaduzeno_cat = $rowz['zaduzeno_cat'];
																	$dostupno_cat = $cat_ukupno - $zaduzeno_cat;
																}else{
																	$zaduzeno_cat = 0;
																	$dostupno_cat = 0;
																	$cat_ukupno = 0;
																}
															}
													?>
													<tr>
														<td><?php echo $sumCOunt++; ?></td>
														<td><?php echo $naziv_kategorije; ?></td>
														<td><?php echo $cat_ukupno; ?></td>
														<td><?php echo $dostupno_cat; ?></td>
														<td class="text-center">
															<div class="btn-group material-btn-group">
																<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
																<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
																	<li><a href="<?php getSiteURL(); ?>warehouse?page=edit_category&id=<?php echo $skk_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Uredi</a></li>
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
					</div>
				</div>
			</div>
		<?php
				break;

				
				case "add":
					if($employee_warehouse == 1){
		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-th-list idk_color_green" aria-hidden="true"></i> Dodaj novu stavku u skladište</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>warehouse?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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
								<form id="idk_form" action="<?php getSiteURL(); ?>do.php?form=add_skladiste_stavka" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<div class="form-group">
										<label for="skl_title" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="skl_title" id="skl_title" placeholder="Naziv" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="skl_desc" class="col-sm-3 control-label"> Opis:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="skl_desc" id="skl_desc" placeholder="Opis">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="skl_available" class="col-sm-3 control-label"> Na stanju:</label>
										<div class="col-sm-3">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="number" name="skl_available" id="skl_available" placeholder="Na stanju">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="skl_datum" class="col-sm-3 control-label"> Datum nabavke:</label>
										<div class="col-sm-3">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="number" name="skl_datum" id="skl_datum" placeholder="Datum">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<script>
										$("#skl_datum").flatpickr({
											
											dateFormat: "d.m.Y",
											disableMobile: "true"
											
										});							
									</script>
									<div class="form-group">
										<label for="skl_branch" class="col-sm-3 control-label"><span class="text-danger">*</span> Poslovnica:</label>
										<div class="col-sm-3">
											<select class="selectpicker" id="skl_branch" name="skl_branch" required>
											<?php
													$query = $db->prepare("
																	SELECT branch_id, branch_name, branch_state, branch_city
																	FROM idk_poslovnice
																	");

													$query->execute();

													$sumCOunt = 1;
													while($row = $query->fetch()){

														$branch_id = $row['branch_id'];
														$branch_name = $row['branch_name'];
														$branch_state = $row['branch_state'];
														$branch_city = $row['branch_city'];											
											?>
												<option value="<?php echo $branch_id; ?>"><?php echo $branch_name; ?></option>
											<?php } ?>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label for="skl_category" class="col-sm-3 control-label"><span class="text-danger">*</span> Kategorija:</label>
										<div class="col-sm-3">
											<select class="selectpicker" id="skl_category" name="skl_category" required>
												<option value="0" selected>Odaberi kategoriju</option>
												<?php
													$query_cat_name = $db->prepare("
																	SELECT isk_naziv_kategorije, isk_id
																	FROM idk_skladiste_kategorije
																	");

													$query_cat_name->execute();

													$sumCOunt = 1;
													
													while($row_cn = $query_cat_name->fetch()){

														$category_id = $row_cn['isk_id'];
														$category_name = $row_cn['isk_naziv_kategorije'];
														
												?>
												<option value="<?php echo $category_id; ?>" ><?php echo $category_name; ?></option>
												<?php } ?>
											</select>
										</div>
									</div>									
									<div class="form-group">
										<label for="skl_image" class="col-sm-3 control-label">Fotografija:</label>
										<div class="col-sm-9">
											<div class="fileinput fileinput-new" data-provides="fileinput">
												<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;"></div>
												<div>
													<span class="btn btn-default btn-file"><span class="fileinput-new">Izaberi fotografiju</span><span class="fileinput-exists">Promijeni</span><input type="file" name="skl_image" id="skl_image"></span>
													<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Ukloni</a>
													<script>
														$(function (){
															$('#skl_image').change(function (){

																var ext = $('#skl_image').val().split('.').pop().toLowerCase();

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
					if($employee_warehouse == 1){

						$skl_id = $_GET['id'];

						$query = $db->prepare("
										SELECT skl_id, skl_title, skl_desc, skl_available, skl_image, skl_branch, skl_kategorija, skl_datum_nabavke
										FROM idk_skladiste
										WHERE skl_id = :skl_id");

						$query->execute(array(
									':skl_id' => $skl_id));

						$row = $query->fetch();

							$skl_title = $row['skl_title'];
							$skl_desc = $row['skl_desc'];
							$skl_available = $row['skl_available'];
							$skl_image = $row['skl_image'];
							$skl_branch = $row['skl_branch'];
							$skl_kategorija = $row['skl_kategorija'];
							$skl_datum_nabavke = $row['skl_datum_nabavke'];
							$datum_nabavke = date("d-m-Y", strtotime($skl_datum_nabavke));
							
							if($skl_image != "none"){
								$skl_image_txt = $skl_image;
							}else{
								$skl_image_txt = "box.png";
							}
							
						// BROJ JEDINICA ZA STAVKU
					
						$query_unit = $db->prepare("
										SELECT COUNT(isp_skl_id) as broj_jedinica
										FROM idk_skladiste_product
										WHERE isp_skl_id = :isp_skl_id AND isp_archive = 0");
						
						$query_unit->execute(array(
									':isp_skl_id' => $skl_id));
									
						$row_unit = $query_unit->fetch();
						$broj_jedinica = $row_unit['broj_jedinica'];

		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-users idk_color_green" aria-hidden="true"></i> Uredi stavku <?php echo $skl_title; ?></h1>
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
								<form id="idk_form" action="<?php getSiteURL(); ?>do.php?form=edit_skladiste_stavku" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<input type="hidden" name="skl_id" value="<?php echo $skl_id; ?>" />
									<div class="form-group">
										<label for="skl_title" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="skl_title" id="skl_title" value="<?php echo $skl_title; ?>" placeholder="Ime" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="skl_desc" class="col-sm-3 control-label"><span class="text-danger">*</span> Opis:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="skl_desc" id="skl_desc" value="<?php echo $skl_desc; ?>" placeholder="Opis" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="skl_available" class="col-sm-3 control-label">Na stanju:</label>
										<div class="col-sm-3">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="number" name="skl_available" id="skl_available" value="<?php echo $skl_available; ?>" placeholder="Na stanju">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<script>
										$("#skl_available").change(function(){
											var broj_jedinica = "<?php echo $broj_jedinica ?>";
											var novi_unos = $(this).val();
											if(novi_unos < broj_jedinica){
												alert("Da bi ste smanjili broj stavki na stanju, prvo morate arhivirati stavke ovog proizvoda!");
												$("#skl_available").val(broj_jedinica);
											}
										});
									</script>
									<div class="form-group">
										<label for="skl_datum" class="col-sm-3 control-label"> Datum nabavke:</label>
										<div class="col-sm-3">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="number" name="skl_datum" id="skl_datum" placeholder="Datum" >
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<script>
										$("#skl_datum").flatpickr({
											
											dateFormat: "d.m.Y",
											disableMobile: "true",
											defaultDate: "<?php echo $datum_nabavke; ?>"
											
										});							
									</script>
									<div class="form-group">
										<label for="skl_branch" class="col-sm-3 control-label"><span class="text-danger">*</span> Poslovnica:</label>
										<div class="col-sm-3">
											<select class="selectpicker" id="skl_branch" name="skl_branch" required>
												<?php
													$query = $db->prepare("
																	SELECT branch_id, branch_name, branch_state, branch_city
																	FROM idk_poslovnice
																	");

													$query->execute();

													$sumCOunt = 1;
													while($row = $query->fetch()){

														$branch_id = $row['branch_id'];
														$branch_name = $row['branch_name'];
														$branch_state = $row['branch_state'];
														$branch_city = $row['branch_city'];	
												?>
												<option value="<?php echo $branch_id; ?>" <?php if($skl_branch == $branch_id){echo "selected";}else{} ?>><?php echo $branch_name; ?></option>
												<?php } ?>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label for="skl_category" class="col-sm-3 control-label"><span class="text-danger">*</span> Kategorija:</label>
										<div class="col-sm-3">
											<select class="selectpicker" id="skl_category" name="skl_category" required>
												<option value="" selected>Odaberi kategoriju</option>
												<?php
													$query_cat_name = $db->prepare("
																	SELECT isk_naziv_kategorije, isk_id
																	FROM idk_skladiste_kategorije
																	");

													$query_cat_name->execute();

													$sumCOunt = 1;
													
													while($row_cn = $query_cat_name->fetch()){

														$category_id = $row_cn['isk_id'];
														$category_name = $row_cn['isk_naziv_kategorije'];
														
												?>
												<option value="<?php echo $category_id; ?>" <?php if($skl_kategorija == $category_id){echo "selected";}else{} ?>><?php echo $category_name; ?></option>
												<?php } ?>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label for="skl_image" class="col-sm-3 control-label">Fotografija:</label>
										<div class="col-sm-9">
											<div class="fileinput fileinput-new" data-provides="fileinput">
												<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;">
													<img src="<?php getSiteURL(); ?>images/<?php echo $skl_image_txt; ?>">
												</div>
												<input type="hidden" name="skl_image_url" value="<?php echo $skl_image; ?>" />
												<div>
													<span class="btn btn-default btn-file"><span class="fileinput-new">Izaberi fotografiju</span><span class="fileinput-exists">Promijeni</span><input type="file" name="skl_image" id="skl_image"></span>
													<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Ukloni</a>
													<script>
														$(function (){
															$('#skl_image').change(function (){

																var ext = $('#skl_image').val().split('.').pop().toLowerCase();

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

					$skl_id = $_GET['id'];

					$query = $db->prepare("
									SELECT skl_id, skl_title, skl_desc, skl_available, skl_image, skl_branch
									FROM idk_skladiste
									WHERE skl_id = :skl_id");

					$query->execute(array(
								':skl_id' => $skl_id));

					$row = $query->fetch();

					$skl_id = $row['skl_id'];
					$skl_title = $row['skl_title'];
					$skl_desc = $row['skl_desc'];
					$skl_available = $row['skl_available'];
					$skl_branch = $row['skl_branch'];

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
						$branch_id = $rowb['branch_id'];
						$branch_name = $rowb['branch_name'];
						$branch_state = $rowb['branch_state'];
						$branch_city = $rowb['branch_city'];						
				
					if($row['skl_image'] == "none"){
						$skl_image = "box.png";
					}else{
						$skl_image = $row['skl_image'];
					}
					
					$query_zaduzeno = $db->prepare("
									SELECT SUM(sz_quantity) AS broj_zaduzeno
									FROM idk_skladiste_zaposlenici
									WHERE isz_sklid = :isz_sklid AND (isz_status = 1 OR isz_status = 2)");

					$query_zaduzeno->execute(array(
								':isz_sklid' => $skl_id));

					$rowz = $query_zaduzeno->fetch();

						$broj_zaduzeno = $rowz['broj_zaduzeno'];
						$dostupno = $skl_available - $broj_zaduzeno;
						
					if($dostupno > 0){
						$availability_txt = '<span class="label label-success"> '.$dostupno.' </span>';
					}else{
						$availability_txt = '<span class="label label-danger"> '.$dostupno.' </span>';
					}
					
					// BROJ JEDINICA ZA STAVKU
					
					$query_unit = $db->prepare("
									SELECT COUNT(isp_skl_id) as broj_jedinica
									FROM idk_skladiste_product
									WHERE isp_skl_id = :isp_skl_id AND isp_archive = 0");
					
					$query_unit->execute(array(
								':isp_skl_id' => $skl_id));
								
					$row_unit = $query_unit->fetch();
					$broj_jedinica = $row_unit['broj_jedinica'];
					if($broj_jedinica < $skl_available)
						$broj_jedinica_txt = '<span class="label label-danger"> '.$broj_jedinica.' </span>';
					else if($broj_jedinica == $skl_available)
						$broj_jedinica_txt = '<span class="label label-success"> '.$broj_jedinica.' </span>';
					
		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><a class="fancybox" rel="group" href="#"><img class="idk_profile_img" src="<?php getSiteURL(); ?>images/<?php echo $skl_image; ?>"></a> <?php echo $skl_title; ?></h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>warehouse?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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
										<div class="alert material-alert material-alert_success">Uspješno ste zadužili stavku zaposleniku.</div>
										<script>$(function() { $('[href="#notes"]').tab('show'); });</script>
									<?php
									}elseif($mess == 2){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste razdužili zapolsenika.</div>
										<script>$(function() { $('[href="#info"]').tab('show'); });</script>
									<?php
									}elseif($mess == 3){ ?>
										<div class="alert material-alert material-alert_danger">Neuspješno zaduživanje, molimo provjerite podatke i pokušajte ponovo.</div>
										<script>$(function() { $('[href="#info"]').tab('show'); });</script>
									<?php
									}elseif($mess == 4){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste dodali novu stavku u skladište.</div>
										<script>$(function() { $('[href="#info"]').tab('show'); });</script>
									<?php
									}elseif($mess == 5){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste uredili stavku u skladištu.</div>
										<script>$(function() { $('[href="#info"]').tab('show'); });</script>
									<?php
									}elseif($mess == 6){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste arhivirali stavku u skladištu.</div>
										<script>$(function() { $('[href="#info"]').tab('show'); });</script>
									<?php
									}
								?>
							</div>
						</div>
						<div id="myTabs" class="panel-group material-tabs-group">
							<ul class="nav nav-tabs material-tabs material-tabs_primary">
								<li class="active"><a href="#info" class="material-tabs__tab-link" data-toggle="tab">Informacije</a></li>
							</ul>
							<div class="tab-content materail-tabs-content">
								<div class="tab-pane fade active in" id="info">
									<div class="row idk_employee_info">
										<div class="col-md-12">
											<div class="row">
												<div class="col-sm-9">
													<h5>Informacije o proizvodu</h5>
												</div>
												<div class="col-sm-3 text-right">
												</div>
											</div>
											<div class="row">
												<div class="col-md-2">
													<img src="<?php getSiteUrl(); ?>images/<?php echo $skl_image; ?>" class="img-thumbnail" alt="Cinque Terre">
												</div>
											
												<div class="col-md-4">
													<div class="row">
														<strong class="col-sm-4 text-right">Naziv:</strong>
														<div class="col-sm-8"><span class="label label-success"><?php echo $skl_title; ?></span></div>
													</div>
													<div class="row">
														<strong class="col-sm-4 text-right">Opis:</strong>
														<div class="col-sm-8"><span class="label label-success"><?php echo $skl_desc; ?></span></div>
													</div>
													<div class="row">
														<strong class="col-sm-4 text-right">Poslovnica:</strong>
														<div class="col-sm-8"><span class="label label-success"><?php echo $branch_name; ?></span></div>
													</div>													
													<div class="row">
														<strong class="col-sm-4 text-right">Ukupno:</strong>
														<div class="col-sm-8"><span class="label label-success"><?php echo $skl_available; ?></span></div>
													</div>
													<div class="row">
														<strong class="col-sm-4 text-right">Na stanju:</strong>
														<div class="col-sm-8"><?php echo $availability_txt; ?></div>
													</div>
													<div class="row">
														<strong class="col-sm-4 text-right">Unešeno artikala:</strong>
														<div class="col-sm-8"><?php echo $broj_jedinica_txt; ?></div>
													</div>
												</div>
												
												<div class="col-md-4">
													<?php
													if($broj_jedinica < $skl_available){
														
														?>
														<div class="row">
															<h4 class="col-sm-12"><i class="fa fa-exclamation-circle fa-lg text-primary" aria-hidden="true"></i> Unesite preostale artikle za ovaj proizvod:</h4>
															
														</div>
														<div class="row">
															<form id="idk_form" action="<?php getSiteURL(); ?>do.php?form=add_skladiste_artikl" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
																<input type="hidden" name="skl_id" value="<?php echo $skl_id; ?>" />
																<div class="form-group">
																	<label for="skp_serijski_broj" class="col-sm-3 control-label"><span class="text-danger">*</span> Serijski broj:</label>
																	<div class="col-sm-9">
																		<div class="materail-input-block materail-input-block_success">
																			<input class="form-control materail-input" type="text" name="skp_serijski_broj" id="skp_serijski_broj" placeholder="Serijski broj" required>
																			<span class="materail-input-block__line"></span>
																		</div>
																	</div>
																</div>
																<div class="form-group">
																	<label for="skp_inventarno" class="col-sm-3 control-label"><span class="text-danger">*</span> Inventarno:</label>
																	<div class="materail-input-block materail-input-block_success idk_radio_buttons inventarno_da" style="padding-left: 15px;">
																		<label class="main-container__column material-radio-group material-radio-group_success" for="skp_inventarno_da">
																			<input type="radio" name="skp_inventarno" id="skp_inventarno_da" class="material-radiobox" value="1" />
																			<span class="material-radio-group__element material-radio-group__check-radio"></span>
																			<span class="material-radio-group__element material-radio-group__caption">DA</span>
																		</label>
																	</div>
																	<div class="materail-input-block materail-input-block_danger idk_radio_buttons vozacka_ne">
																		<label class="main-container__column material-radio-group material-radio-group_danger" for="skp_inventarno_ne">
																			<input type="radio" name="skp_inventarno" id="skp_inventarno_ne" class="material-radiobox" value="0"  checked />
																			<span class="material-radio-group__element material-radio-group__check-radio"></span>
																			<span class="material-radio-group__element material-radio-group__caption">NE</span>
																		</label>
																	</div>
																</div>
																<div class="form-group">
																	<label for="skp_info" class="col-sm-3 control-label"> Info:</label>
																	<div class="col-sm-9">
																		<div class="materail-input-block materail-input-block_success">
																			<input class="form-control materail-input" type="text" name="skp_info" id="skp_info" placeholder="Info">
																			<span class="materail-input-block__line"></span>
																		</div>
																	</div>
																</div>
																<br />
																<div class="form-group">
																	<div class="col-sm-offset-2 col-sm-10 text-right">
																		<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj</span></button>
																	</div>
																</div>
															</form>
														</div>
														
														<?php
													}
													
													?>
												</div>
											</div>
											<div class="row">
												<div class="col-sm-9">
													<h5>Lista proizvoda</h5>
												</div>
												<div class="col-sm-3 text-right">
												</div>
											</div>
											<div class="row">
														
												<div class="col-md-12">
													<div class="row">			
														<div class="col-md-12">
															<table class="table table-striped">
																<thead>
																	<tr>
																		<th>#</th>
																		<th>Serijski broj</th>
																		<th>Info</th>
																		<th class="text-center">Status</th>
																		<th>Ime zaposlenika</th>
																		<th class="text-center">Inventar</th>
																		<th class="text-center">Akcija</th>
																	</tr>
																</thead>
																<tbody>
																<?php
																	$query_units = $db->prepare("
																					SELECT *
																					FROM idk_skladiste_product
																					WHERE isp_skl_id = :isp_skl_id AND isp_archive = :isp_archive");
																	
																	$query_units->execute(array(
																				':isp_skl_id' => $skl_id,
																				':isp_archive' => 0));
																				
																	$sumCount = 1;
																	while($row_units = $query_units->fetch()){
																		
																		$skp_id = $row_units['isp_id'];
																		$skp_serijski_broj = $row_units['isp_serijski_broj'];
																		$skp_dodatni_info = $row_units['isp_dodatni_info'];
																		$skp_razlozi_odbijanja = $row_units['isp_razlozi_odbijanja'];
																		$skp_razlozi_razduzivanja = $row_units['isp_razlozi_razduzivanja'];
																		$skp_inventarno = $row_units['isp_inventar'];
																	
																	
																		$query_zaduzeno_list = $db->prepare("
																						SELECT sz_quantity, employee_id, employee_firstname, employee_lastname, isz_sklid, isz_id, isz_status
																						FROM idk_skladiste_zaposlenici
																						INNER JOIN idk_employees ON idk_skladiste_zaposlenici.sz_employeeid = idk_employees.employee_id
																						WHERE isz_product_id = :isz_product_id ");

																		$query_zaduzeno_list->execute(array(
																					':isz_product_id' => $skp_id));

																		
																		$broj_zaduzenih = $query_zaduzeno_list->rowCount() ;
																		$span_razlozi2 = "<strong>Razlozi razduživanja: </strong><br/>".$skp_razlozi_razduzivanja;
																		$span_razlozi = "<strong>Razlozi odbijanja: </strong><br/>".$skp_razlozi_odbijanja;
																		if($broj_zaduzenih == 0){
																			$span_razduzi = "";
																			$span_zaposlenik = '<strong style="margin-left: 30px;">-</strong>';
																			$ispis = '<span class="label label-primary material-label material-label_primary main-container__column">NEZADUŽENO</span>';
																			$span_zaduzi = '<a href="#" data-toggle="modal" data-productname="'.$skl_title.'" data-sbrojjj="'.$skp_serijski_broj.'" data-productid="'.$skp_id.'" data-target="#zaduziModal" class="zaduzivanje material-dropdown-menu__link"><i class="fa fa-repeat" aria-hidden="true" style="padding: 2px; background-color: white; border-radius: 5px; color: green;"></i> Zaduži</a>';
																		}else{
																			$rowlist = $query_zaduzeno_list->fetch();
																			$isz_id = $rowlist['isz_id'];
																			$isz_sklid = $rowlist['isz_sklid'];
																			$sz_quantity = $rowlist['sz_quantity'];
																			$employee_id = $rowlist['employee_id'];
																			$employee_firstname = $rowlist['employee_firstname'];
																			$employee_lastname = $rowlist['employee_lastname'];
																			$employe_fullname = $employee_firstname." ".$employee_lastname;
																			$sz_status = $rowlist['isz_status'];
																			if($sz_status == 0){
																				$ispis = '<span class="label label-success material-label material-label_success main-container__column">nula jos</span>';

																			}else if($sz_status == 1){
																				$ispis = '<span class="label label-success material-label material-label_success main-container__column">ZADUŽENO</span>';
																				//$span_razlozi = "";

																			}else if($sz_status == 2){
																				$ispis = '<span class="label label-warning material-label material-label_warning main-container__column">NA ČEKANJU</span>';
																				//$span_razlozi = "";
																			}else if($sz_status == 3){
																				$ispis = '<span class="label label-danger material-label material-label_danger main-container__column">ODBIJENO</span>';
																				
																			}
																			$span_zaduzi =	 "";
																			$span_razduzi = '<a href="#" class="material-dropdown-menu__link razduzivanje" data="'.getSiteUrlr().'warehouse?page=razduzi_stavku_kandidatu&id='.$isz_id.'" data-sklzap="'.$isz_id.'" data-productname="'.$skl_title.'" data-employeename="'.$employe_fullname.'" data-product_id="'.$skp_id.'" data-serijski_broj_d="'.$skp_serijski_broj.'" data-toggle="modal" data-target="#archiveModal"><i class="fa fa-repeat"  aria-hidden="true" style="padding: 2px; background-color: white; border-radius: 5px; color: indianred;"></i> Razduži</a>';
																			$span_zaposlenik = '<a href="'.getSiteUrlr().'employees?page=open&id='.$employee_id.'" target="_BLANK">'.$employe_fullname.'</a>';
																		}
																		if($skp_inventarno == "1")
																			$inventar = '<span class="label label-success material-label material-label_success main-container__column">DA</span>';
																		else
																			$inventar = '<span class="label label-danger material-label material-label_danger main-container__column">NE</span>';
																?>
																	<tr>
																		<td><?php echo $sumCount++ ; ?></td>
																		<td><?php echo $skp_serijski_broj ; ?></td>
																		<td><?php echo $skp_dodatni_info ; ?></td>
																		<td class="text-center"><?php echo $ispis; ?></td>
																		<td><?php echo $span_zaposlenik ; ?></td>
																		<td class="text-center"><?php echo $inventar ; ?></td>
																		<td class="text-center">
																			<div class="btn-group material-btn-group">
																				<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
																				<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
																					<li ><?php echo $span_zaduzi; ?></li>
																					<li ><?php echo $span_razduzi; ?></li>
																					<li><a href="#" data-toggle="modal" data-target="#edit_product" data-idproduct="<?php echo $skp_id; ?>" data-serbroj="<?php echo $skp_serijski_broj; ?>" data-inventar="<?php echo $skp_inventarno; ?>" data-dodinfo="<?php echo $skp_dodatni_info; ?>" data-razlozi="<?php echo $span_razlozi; ?>" data-razlozi2="<?php echo $span_razlozi2; ?>" class="material-dropdown-menu__link editovanje"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Uredi</a></li>
																					<?php if($broj_zaduzenih == 0){?><li class="idk_dropdown_danger"><a href="#" data-productname="<?php echo $skl_title; ?>" data="<?php getSiteURL(); ?>warehouse?page=archiveproduct&id=<?php echo $skp_id; ?>&idd=<?php echo $skl_id; ?>" data-serijski="<?php echo $skp_serijski_broj ;?>" data-toggle="modal" data-target="#archiveProduct" class="archiveproduct material-dropdown-menu__link"><i class="fa fa-trash-o" aria-hidden="true"></i> Arhiviraj</a></li>
																					
																					<?php } ?>
																				</ul>
																			</div>
																		</td>
																		
																	</tr>
																<?php } ?>
																
																
																</tbody>
															</table>
														</div>
														<script>
															$(".archiveproduct").click(function () {
																var addressValue = $(this).attr("data");
																var productname = $(this).data("productname");
																var serijski = $(this).data("serijski");
																$('#nazivproducta').html(productname);
																$('#serijski').html(serijski);
																
																document.getElementById("archive_product_link").href = addressValue;
															});
														</script>
														<!-- Modal arhiviranje producta-->
														<div class="modal material-modal material-modal_danger fade" id="archiveProduct">
															<div class="modal-dialog">
																<div class="modal-content material-modal__content">
																	<div class="modal-header material-modal__header">
																		<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																		<h4 class="modal-title material-modal__title">Arhiviranje</h4>
																	</div>
																	<div class="modal-body material-modal__body">
																		<p>Jeste li sigurni da želite arhivirati stavku: <span id="nazivproducta"></span> (serijski broj: <span id="serijski"></span>)?</p>
																	</div>
																	<div class="modal-footer material-modal__footer">
																		<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
																		<a id="archive_product_link" href=""><button class="btn btn-primary material-btn material-btn_danger">ARHIVIRAJ</button></a>
																	</div>
																</div>
															</div>
														</div>
														<script>
															$(".razduzivanje").click(function () {
																var addressValue = $(this).attr("data");
																var productname = $(this).data("productname");
																var employeename = $(this).data("employeename");
																var serijski_broj_d = $(this).data("serijski_broj_d");
																var prod_id = $(this).data("product_id");
																var sklzap = $(this).data("sklzap");
																$('#nazivstavke').html(productname);
																$('#zaposlenikime').html(employeename);
																$('#serijski_broj_d').html(serijski_broj_d);
																document.getElementById('prod_id').value = prod_id;
																document.getElementById('sklzap').value = sklzap;
															});
														</script>
														<!-- Modal razduži -->
														<div class="modal material-modal material-modal_danger fade" id="archiveModal">
															<div class="modal-dialog">
																<div class="modal-content material-modal__content">
																	<div class="modal-header material-modal__header">
																		<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																		<h4 class="modal-title material-modal__title">Razduži stavku</h4>
																	</div>
																	<div class="modal-body material-modal__body">
																		<p>Jeste li sigurni da želite razdužiti <span id="nazivstavke"></span> (serijski broj: <span id="serijski_broj_d"></span>) zaposleniku <span id="zaposlenikime"></span>?</p>
																	
																		<form class="form-inline" action="<?php getSiteURL(); ?>warehouse?page=razduzi_stavku_kandidatu" method="post" id="forma_razduzi">
																			<input type="hidden" name="skl_id" id="skl_id" value="<?php echo $skl_id; ?>">
																			<input type="hidden" name="prod_id" id="prod_id">
																			<input type="hidden" name="sklzap" id="sklzap">
																			<div class="form-group">
																				<div class="col-sm-12">
																					<div class="form-group materail-input-block materail-input-block_primary materail-input_slide-line">
																						<textarea class="form-control materail-input material-textarea" name="razlog_razduzivanja" id="razlog_razduzivanja" placeholder="Unesite razlog razduzivanja" rows="6" required></textarea>
																						<span class="materail-input-block__line"></span>
																					</div>
																				</div>
																			</div>
																		</form>
																	</div>
																	<div class="modal-footer material-modal__footer">
																		<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
																		<button type="submit" class="btn btn-primary material-btn material-btn_success" form="forma_razduzi">RAZDUŽI</button>
																	</div>
																</div>
															</div>
														</div>
														<script>
															$(".zaduzivanje").click(function () {
																//var addressValueZ = $(this).attr("data");
																var productname = $(this).data("productname");
																var serijski_broj_d = $(this).data("sbrojjj");
																var productid = $(this).data("productid");
																$('#nazivstavkeZ').html(productname);
																$('#serijski_broj_dZ').html(serijski_broj_d);
																document.getElementById('productid').value = productid;
																document.getElementById('sbroj').value = serijski_broj_d;

																document.getElementById("zaduzi_link").href = addressValueZ;
															});
														</script>
														<!-- Modal zaduzivanje-->
														<div class="modal material-modal material-modal_primary fade" id="zaduziModal">
															<div class="modal-dialog">
																<div class="modal-content material-modal__content">
																	<div class="modal-header material-modal__header">
																		<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																		<h4 class="modal-title material-modal__title">Zaduži stavku</h4>
																	</div>
																	<div class="modal-body material-modal__body">
																		<p>Zaduži stavku <span id="nazivstavkeZ"></span> (serijski broj: <span id="serijski_broj_dZ"></span>) zaposleniku <span id="zaposlenikime"></span>:</p>
																		<form class="form-inline" action="<?php getSiteURL(); ?>do.php?form=skladiste_zaduzi_stavku" method="post" id="forma_zaduzi">
																			<input type="hidden" name="skl_id" value="<?php echo $skl_id; ?>">
																			<input type="hidden" name="skl_title" value="<?php echo $skl_title; ?>">
																			<input type="hidden" name="productid" id="productid">
																			<input type="hidden" name="sbroj" id="sbroj">
																			<select class="selectpicker" id="sz_employeeid" data-live-search="true" name="sz_employeeid" required>
																				<option value="0">Bez zaposlenika</option>
																				<?php gelEmployeList(); ?>
																			</select>
																			<hr/>
																			<strong>Dodatne informacije o zaduživanju: </strong></br>
																			<input type="textarea" id="sz_opis" name="sz_opis" style="width: 100%;border:1px solid #68c368; padding: 5px 7px;"></br></br>
																		</form>
																	</div>
																	<div class="modal-footer material-modal__footer">
																		<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
																		<button type="submit" class="btn btn-primary material-btn material-btn_success" form="forma_zaduzi">ZADUŽI</button>
																	</div>
																</div>
															</div>
														</div>
														<script>
														$(".editovanje").click(function () {
																//var addressValueZ = $(this).attr("data");
																var idproduct = $(this).data("idproduct");
																var serbroj = $(this).data("serbroj");
																var inventar = $(this).data("inventar");
																var dodinfo = $(this).data("dodinfo");
																var razlozi = $(this).data("razlozi");
																var razlozi2 = $(this).data("razlozi2");
																document.getElementById('idproduct').value = idproduct;
																document.getElementById('serbroj').value = serbroj;
																document.getElementById('dodinfo').value = dodinfo;
																if(inventar == 1)
																	$('#skp_inventarno_da_e').prop("checked", true);
																else
																	$('#skp_inventarno_ne_e').prop("checked", true);
																$('#razlozi').html(razlozi);
																$('#razlozi2').html(razlozi2);
															});
														</script>
														<!-- Modal uredi product-->
															<div class="modal material-modal material-modal_primary fade" id="edit_product">
																<div class="modal-dialog">
																	<div class="modal-content material-modal__content">
																		<div class="modal-header material-modal__header">
																			<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																			<h4 class="modal-title material-modal__title">Uredi stavku</h4>
																		</div>
																		<div class="modal-body material-modal__body">
																			<form class="form-inline" action="<?php getSiteURL(); ?>do.php?form=skladiste_edit_product" method="post" id="forma_edit_product">
																				<input type="hidden" name="idproduct" id="idproduct">
																				<input type="hidden" name="skl_title" value="<?php echo $skl_title; ?>">
																				<input type="hidden" name="skl_id" value="<?php echo $skl_id; ?>">
																				<div class="row">
																					<label for="serbroj" class="col-sm-3 control-label"><span class="text-danger">*</span> Serijski broj:</label>
																					<div class="col-sm-9">
																						<div class="materail-input-block materail-input-block_success">
																							<input class="form-control materail-input" type="text" name="serbroj" id="serbroj" placeholder="Serijski broj" required>
																							<span class="materail-input-block__line"></span>
																						</div>
																					</div>
																				</div>
																				<br/>
																				<div class="row">
																					<label for="skp_inventarno_e" class="col-sm-3 control-label"><span class="text-danger">*</span> Inventarno:</label>
																					<div class="materail-input-block materail-input-block_success idk_radio_buttons inventarno_da" style="padding-left: 15px;">
																						<label class="main-container__column material-radio-group material-radio-group_success" for="skp_inventarno_da_e">
																							<input type="radio" name="skp_inventarno_e" id="skp_inventarno_da_e" class="material-radiobox" value="1" />
																							<span class="material-radio-group__element material-radio-group__check-radio"></span>
																							<span class="material-radio-group__element material-radio-group__caption">DA</span>
																						</label>
																					</div>
																					<div class="materail-input-block materail-input-block_danger idk_radio_buttons vozacka_ne">
																						<label class="main-container__column material-radio-group material-radio-group_danger" for="skp_inventarno_ne_e">
																							<input type="radio" name="skp_inventarno_e" id="skp_inventarno_ne_e" class="material-radiobox" value="0"  checked />
																							<span class="material-radio-group__element material-radio-group__check-radio"></span>
																							<span class="material-radio-group__element material-radio-group__caption">NE</span>
																						</label>
																					</div>
																				</div>
																				<br/>
																				<div class="row">
																					<label for="dodinfo" class="col-sm-3 control-label"><span class="text-danger">*</span> Dodatni info:</label>
																					<div class="col-sm-9">
																						<div class="materail-input-block materail-input-block_success" style="margin-bottom:20px;">
																							<textarea rows="5" cols="40" class="form-control materail-input" type="text" name="dodinfo" id="dodinfo" placeholder="Dodatni info" required></textarea>
																							<span class="materail-input-block__line"></span>
																						</div>
																					</div>
																				</div>
																				
																				<hr/>
																				<span name="razlozi2" id="razlozi2"></span>
																				<hr/>
																				<span name="razlozi" id="razlozi"></span>
																					
																				
																			</form>
																		</div>
																		<div class="modal-footer material-modal__footer">
																			<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
																			<button type="submit" class="btn btn-primary material-btn material-btn_success" form="forma_edit_product">UREDI</button>
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
				</div>
			</div>
		<?php
				break;

				case "razduzi_stavku_kandidatu":
					
					$isz_id = $_POST['sklzap'];
					$isz_sklid = $_POST['skl_id'];
					$skp_id = $_POST['prod_id'];
					$razlog_razduzivanja = $_POST['razlog_razduzivanja'];

					
					$query_zaduzeno_list = $db->prepare("
									SELECT sz_quantity, employee_id, employee_firstname, employee_lastname, isz_sklid, isz_id, skl_title
									FROM idk_skladiste_zaposlenici
									INNER JOIN idk_employees ON idk_skladiste_zaposlenici.sz_employeeid = idk_employees.employee_id
									INNER JOIN idk_skladiste ON idk_skladiste_zaposlenici.isz_sklid = idk_skladiste.skl_id
									WHERE isz_id = :isz_id");
				
					$query_zaduzeno_list->execute(array(
								':isz_id' => $isz_id));
					
					$sumCount = 1;
					$rowlist = $query_zaduzeno_list->fetch();
				
						$isz_id = $rowlist['isz_id'];
						$isz_sklid = $rowlist['isz_sklid'];
						$sz_quantity = $rowlist['sz_quantity'];
						$employee_id = $rowlist['employee_id'];
						$employe_fullname = $rowlist['employee_firstname']." ".$rowlist['employee_lastname'];
						$employee_firstname = $rowlist['employee_firstname'];
						$employee_lastname = $rowlist['employee_lastname'];
						$skl_title = $rowlist['skl_title'];
						
						// Izbrisi id iz baze
						$task_del_query = $db->prepare("
													DELETE FROM idk_skladiste_zaposlenici
													WHERE isz_id = :isz_id");

						$task_del_query->execute(array(
											':isz_id' => $isz_id));						
						
						// STARI RAZLOZI RAZDUZIVANJA
						$get_razloge = $db->prepare("
										SELECT isp_razlozi_razduzivanja
										FROM idk_skladiste_product
										WHERE isp_id = :isp_id");

						$get_razloge->execute(array(
										':isp_id' => $skp_id));
						
						$razlozi_row = $get_razloge->fetch();
						$stari_razlozi = $razlozi_row['isp_razlozi_razduzivanja'];	
						
						$novi_razlozi = $stari_razlozi."<br/>".$razlog_razduzivanja." (Razdužen: ".$employe_fullname.")";
						
						$update_query = $db->prepare("
										UPDATE idk_skladiste_product
										SET isp_razlozi_razduzivanja = :isp_razlozi_razduzivanja
										WHERE isp_id = :isp_id");
						$update_query->execute(array(
										':isp_id' => $skp_id,
										':isp_razlozi_razduzivanja' => $novi_razlozi));
						
						//Add to LOGS
						$log_desc = "Razdužio stavku '".$skl_title."' od zaposlenika " . $employe_fullname . ".";
						$log_type = "1";
						addToLogs($log_desc, $log_type); //Log za skladiste - $log_type = 1 
						
						if(!isset($_GET['employee_id'])){
							header("Location: warehouse?page=open&id=$isz_sklid&mess=2");
						}else{
							header("Location: employees?page=open&id=$employee_id&mess=22");
						}
						
					
				break;
				
				case "archive":
					if($employee_warehouse == 1){

						$skl_id = $_GET['id'];
						//Get
						$query_select = $db->prepare("
												SELECT skl_title
												FROM idk_skladiste
												WHERE skl_id = :skl_id");

						$query_select->execute(array(
											':skl_id' => $skl_id));

						$row_select = $query_select->fetch();

						$skl_title = $row_select['skl_title'];

						//Save
						$query = $db->prepare("
										UPDATE idk_skladiste
										SET skl_archive = :skl_archive
										WHERE skl_id = :skl_id");

						$query->execute(array(
									':skl_archive' => 1,
									':skl_id' => $skl_id));

						//Add to LOGS
						$log_desc = "Arhivirao stavku u skladištu: " . $skl_title . ".";
						$log_type = "1";
						addToLogs($log_desc, $log_type); //Log za skladiste - $log_type = 1 


						header("Location: warehouse?page=list&mess=4");

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
				
				case "archiveproduct":
					
					$skp_id = $_GET['id'];
					$skl_id = $_GET['idd'];
					
					$query_select = $db->prepare("
											SELECT skl_title
											FROM idk_skladiste
											WHERE skl_id = :skl_id");

					$query_select->execute(array(
										':skl_id' => $skl_id));

					$row_select = $query_select->fetch();

					$skl_title = $row_select['skl_title'];
					
					$query = $db->prepare("
									UPDATE idk_skladiste_product
									SET isp_archive = :isp_archive
									WHERE isp_id = :isp_id");

					$query->execute(array(
								':isp_archive' => 1,
								':isp_id' => $skp_id));
								
					$query = $db->prepare("
									UPDATE idk_skladiste
									SET skl_available = skl_available - 1
									WHERE skl_id = :skl_id");

					$query->execute(array(
								
								':skl_id' => $skl_id));
								
					//Add to LOGS
					$log_desc = "Arhivirao stavku u skladištu: " . $skl_title . ".";
					$log_type = "1";
					addToLogs($log_desc, $log_type); //Log za skladiste - $log_type = 1

					header("Location: warehouse?page=open&id=$skl_id&mess=6");
				
				break;
				
				case "add_branch":
					if($employee_warehouse == 1){
		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-globe idk_color_green" aria-hidden="true"></i> Dodaj novu poslovnicu</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>warehouse?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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
								<form id="idk_form" action="<?php getSiteURL(); ?>do.php?form=add_branch" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<div class="form-group">
										<label for="branch_name" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="branch_name" id="branch_name" placeholder="Naziv" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="branch_state" class="col-sm-3 control-label"> Država:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="branch_state" id="branch_state" placeholder="Država">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="branch_city" class="col-sm-3 control-label"> Grad:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="branch_city" id="branch_city" placeholder="Grad">
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
				
				case "edit_branch":
					if($employee_warehouse == 1){
						
					$branch_id = $_GET['id'];
					
					$query = $db->prepare("
									SELECT branch_id, branch_name, branch_state, branch_city
									FROM idk_poslovnice
									WHERE branch_id = :branch_id
									");
	
					$query->execute(array(
						":branch_id" => $branch_id
					));
	
				
					$row = $query->fetch();
	
						$branch_id = $row['branch_id'];
						$branch_name = $row['branch_name'];
						$branch_state = $row['branch_state'];
						$branch_city = $row['branch_city'];
						
		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-globe idk_color_green" aria-hidden="true"></i> Uredi poslovnicu <?php echo $branch_name; ?></h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>warehouse?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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
								<form id="idk_form" action="<?php getSiteURL(); ?>do.php?form=edit_branch" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
								<input type="hidden" name="branch_id" value="<?php echo $branch_id; ?>">
									<div class="form-group">
										<label for="branch_name" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="branch_name" id="branch_name" value="<?php echo $branch_name; ?>" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="branch_state" class="col-sm-3 control-label"> Država:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="branch_state" id="branch_state" value="<?php echo $branch_state; ?>">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="branch_city" class="col-sm-3 control-label"> Grad:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="branch_city" id="branch_city" value="<?php echo $branch_city; ?>">
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
				
				case "add_category":
				?>
					<div class="row">
						<div class="col-xs-8">
							<h1><i class="fa fa-globe text-primary" aria-hidden="true"></i> Dodaj novu kategoriju</h1>
						</div>
						<div class="col-xs-4 text-right idk_margin_top20">
							<a href="<?php getSiteURL(); ?>warehouse?page=list" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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
										<form id="idk_form" action="<?php getSiteURL(); ?>do.php?form=add_category" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
											<div class="form-group">
												<label for="branch_name" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv:</label>
												<div class="col-sm-9">
													<div class="materail-input-block materail-input-block_success">
														<input class="form-control materail-input" type="text" name="category_name" id="category_name" placeholder="Naziv kategorije" required>
														<span class="materail-input-block__line"></span>
													</div>
												</div>
											</div>
											<br />
											<div class="form-group">
												<div class="col-sm-offset-2 col-sm-10 text-right">
													<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj</span></button>
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
				
				case "edit_category":

					case "edit_branch":

					$skk_id = $_GET['id'];

					$query = $db->prepare("
									SELECT isk_id, isk_naziv_kategorije
									FROM idk_skladiste_kategorije
									WHERE isk_id = :isk_id
									");

					$query->execute(array(
						":isk_id" => $skk_id
					));


					$row = $query->fetch();

					$cat_id = $row['isk_id'];
					$cat_name = $row['isk_naziv_kategorije'];

				?>
					<div class="row">
						<div class="col-xs-8">
							<h1><i class="fa fa-globe text-primary" aria-hidden="true"></i> Uredi kategoriju <?php echo $cat_name; ?></h1>
						</div>
						<div class="col-xs-4 text-right idk_margin_top10">
							<a href="<?php getSiteURL(); ?>warehouse?page=list" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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
										<form id="idk_form" action="<?php getSiteURL(); ?>do.php?form=edit_category" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
											<input type="hidden" name="isk_id" value="<?php echo $cat_id; ?>">
											<div class="form-group">
												<label for="cat_name" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv kategorije:</label>
												<div class="col-sm-9">
													<div class="materail-input-block materail-input-block_success">
														<input class="form-control materail-input" type="text" name="cat_name" id="cat_name" value="<?php echo $cat_name; ?>" required>
														<span class="materail-input-block__line"></span>
													</div>
												</div>
											</div>
											<br />
											<div class="form-group">
												<div class="col-sm-offset-2 col-sm-10 text-right">
													<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon"><i class="fa fa-plus" aria-hidden="true"></i> <span>Uredi</span></button>
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
			}
		?>
			<footer><?php getCopyright(); ?></footer>
		</div>
	</div>
</body>
</html>
	<?php } else echo("Nemate pristup ovom sadržaju.")?>