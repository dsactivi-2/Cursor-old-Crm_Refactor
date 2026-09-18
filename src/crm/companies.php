<?php
	include("includes/functions.php");
	include("includes/common.php");
  require_once($_SERVER["DOCUMENT_ROOT"] . "/includes/env.php");

	$getEmployeeStatus = explode( ',' , getEmployeeStatus());

	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: companies?page=list");
	}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Companies | <?php getTitle(); ?></title>

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
				<div class="col-xs-6">
					<h1><i class="fa fa-briefcase idk_color_green" aria-hidden="true"></i> Kompanije</h1>
				</div>
				<div class="col-xs-6 text-right idk_margin_top10">
					<button id="export_kompanija" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-file-excel-o" aria-hidden="true"></i> <span>Export</span></button>				
					<a href="<?php getSiteURL(); ?>companies?page=partner_companies" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-briefcase" aria-hidden="true"></i> <span>Kompanije sa partnera</span></a>
					<a href="<?php getSiteURL(); ?>companies?page=add" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj</span></a>
				</div>
					<script>
					$(document).ready(function() {
						$('#export_kompanija').click(function() {
							$('#export_kompanija_div').load('export_excel.php?prozor=export_kompanija_excel');
						
							return false;
						});
					});				
					</script>	
				<div id="export_kompanija_div"></div>
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
										echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novu kompaniju.</div>';
									}elseif($mess == 3){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste arhivirali profil kompanije.</div>';
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
													{ "width": "15%" },
													{ "width": "10%" },
													{ "width": "5%" },
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
											<th>Vrsta kontakta</th>
											<th>Grad</th>
											<th>Telefon</th>
											<th>E-mail</th>
											<th>Nalozi</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										<?php
											$query = $db->prepare("
															SELECT company_id, company_name, company_contact_type, company_city, company_logo
															FROM idk_companies
															WHERE company_status != :company_status");

											$query->execute(array(':company_status' => 0));

											while($row = $query->fetch()){

												$company_id = $row['company_id'];
												$company_name = $row['company_name'];
												$company_contact_type = $row['company_contact_type'];
												$company_city = $row['company_city'];

												if($row['company_logo'] == "none"){
													$company_logo = "none.jpg";
												}else{
													$company_logo = $row['company_logo'];
												}

												//Get primary phone
												$query_phone = $db->prepare("
																	SELECT comi_data
																	FROM idk_companies_info
																	WHERE comi_group = :comi_group AND comi_primary = :comi_primary AND comi_companyid = :comi_companyid");

												$query_phone->execute(array(
													':comi_group' => 1,
													':comi_primary' => 1,
													':comi_companyid' => $company_id));

												$row_phone = $query_phone->fetch();

												$comapny_phone = $row_phone['comi_data'];

												//Get primary email
												$query_email = $db->prepare("
																	SELECT comi_data
																	FROM idk_companies_info
																	WHERE comi_group = :comi_group AND comi_primary = :comi_primary AND comi_companyid = :comi_companyid");

												$query_email->execute(array(
													':comi_group' => 2,
													':comi_primary' => 1,
													':comi_companyid' => $company_id));

												$row_email = $query_email->fetch();

												$company_email = $row_email['comi_data'];
												
												$get_broj_naloga = $db->prepare("SELECT COUNT(nalog_id) as broj_naloga FROM idk_nalozi WHERE kompanija_id = :kompanija_id");
												$get_broj_naloga->execute(array(':kompanija_id' => $company_id));
												$row_broj_naloga = $get_broj_naloga->fetch();
												$broj_naloga = $row_broj_naloga['broj_naloga'];
										?>
										<tr>
											<td class="text-center"><a href="<?php getSiteURL(); ?>companies?page=open&id=<?php echo $company_id; ?>"><div class="idk_profile_img"><img src="<?php getSiteURL(); ?>files/companies/<?php echo $company_logo; ?>"></a></div></td>
											<td><a href="<?php getSiteURL(); ?>companies?page=open&id=<?php echo $company_id; ?>"><?php echo $company_name; ?></a></td>
											<td><?php echo $company_contact_type; ?></td>
											<td><?php echo $company_city; ?></td>
											<td><a href="tel:<?php echo $comapny_phone; ?>"><?php echo $comapny_phone; ?></a></td>
											<td><a href="mailto:<?php echo $company_email; ?>"><?php echo $company_email; ?></a></td>
											<td><a href="<?php getSiteURL(); ?>companies?page=open&id=<?php echo $company_id; ?>#nalozi"><?php echo $broj_naloga; ?></a></td>
											<td class="text-center">
												<div class="btn-group material-btn-group">
													<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
													<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
														<li><a href="<?php getSiteURL(); ?>companies?page=open&id=<?php echo $company_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori</a></li>
														<li><a href="<?php getSiteURL(); ?>companies?page=edit&id=<?php echo $company_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Uredi</a></li>
														<li class="idk_dropdown_danger"><a href="#" data="<?php getSiteURL(); ?>companies?page=archive&id=<?php echo $company_id; ?>" data-toggle="modal" data-target="#archiveModal" class="archive material-dropdown-menu__link"><i class="fa fa-trash-o" aria-hidden="true"></i> Arhiviraj</a></li>
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
														<p>Jeste li sigurni da želite arhivirati profil kompanije?</p>
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
				case "partner_companies":
					?>
						<div class="row">
							<div class="col-xs-6">
								<h1><i class="fa fa-briefcase idk_color_green" aria-hidden="true"></i> Kompanije sa partnera</h1>
							</div>
							<div class="col-xs-6 text-right idk_margin_top10">
								<!-- <button id="export_kompanija" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-file-excel-o" aria-hidden="true"></i> <span>Export</span></button>				 -->
							</div>
								<script>
								$(document).ready(function() {
									$('#export_kompanija').click(function() {
										$('#export_kompanija_div').load('export_excel.php?prozor=export_kompanija_excel');
									
										return false;
									});
								});				
								</script>	
							<div id="export_kompanija_div"></div>
							<div class="col-xs-12">
								<hr />
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<div class="content_box">
									<div style="padding-bottom: 10px;">	
										<a class="btn <?php echo isset($_GET['archived']) ? 'btn-primary' : 'btn-success disabled'; ?>" href="<?php getSiteURL(); ?>companies?page=partner_companies" type="button">Aktivne</a>
										<a class="btn <?php echo isset($_GET['archived']) ? 'btn-success disabled' : 'btn-primary'; ?>" href="<?php getSiteURL(); ?>companies?page=partner_companies&archived" type="button">Arhivirane</a>
									</div>
									<div class="" style="margin-top: 20px; margin-bottom: 20px; <?php echo isset($_GET['archived']) ? 'display: none;' : ''; ?>">
									<div class="row">
										<div class="col-md-4">
											<label for="status" class="col-form-label">Izaberi status:</label>
											<select id="status" class="selectpicker" onchange="applyFilter(this)">
												<option value="<?php echo getSiteURL(); ?>companies?page=partner_companies" <?php echo isset($_GET['status']) ? '' : 'selected'; ?> >Svi</option>
												<option value="<?php echo getSiteURL(); ?>companies?page=partner_companies&status=2" <?php echo (isset($_GET['status']) && $_GET['status']==2) ? 'selected' : ''; ?>>New</option>
												<option value="<?php echo getSiteURL(); ?>companies?page=partner_companies&status=3" <?php echo (isset($_GET['status']) && $_GET['status']==3) ? 'selected' : ''; ?>>In progress</option>
												<option value="<?php echo getSiteURL(); ?>companies?page=partner_companies&status=4" <?php echo (isset($_GET['status']) && $_GET['status']==4) ? 'selected' : ''; ?>>On hold</option>
												<option value="<?php echo getSiteURL(); ?>companies?page=partner_companies&status=5" <?php echo (isset($_GET['status']) && $_GET['status']==5) ? 'selected' : ''; ?>>Rejected</option>
												<option value="<?php echo getSiteURL(); ?>companies?page=partner_companies&status=1" <?php echo (isset($_GET['status']) && $_GET['status']==1) ? 'selected' : ''; ?>>Active</option>
												<option value="<?php echo getSiteURL(); ?>companies?page=partner_companies&status=6" <?php echo (isset($_GET['status']) && $_GET['status']==6) ? 'selected' : ''; ?>>Finished</option>
											</select>
										</div>
									</div>

									<script>
										function applyFilter(selectElement) {
											var selectedOption = selectElement.options[selectElement.selectedIndex];
											window.location.href = selectedOption.value;
										}
									</script>

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
													echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novu kompaniju.</div>';
												}elseif($mess == 3){
													echo '<div class="alert material-alert material-alert_success">Uspješno ste arhivirali profil kompanije.</div>';
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
																{ "width": "18%" },
																{ "width": "15%" },
																{ "width": "10%" },
																{ "width": "15%" },
																{ "width": "10%" },
																{ "width": "5%" },
																{ "width": "5%" },
																{ "width": "7%" },
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
														<th>Veličina kompanije</th>
														<th>Kontakt</th>
														<th>Telefon</th>
														<th>E-mail</th>
														<th>Nalozi</th>
														<th>Partner</th>
														<th>Status</th>
														<th></th>
													</tr>
												</thead>
												<tbody>
													<?php
														if(isset($_GET['status'])){
															if($_GET['status'] == 1){
																$company_sql = "AND company_status=1";
															}elseif($_GET['status'] == 2){
																$company_sql = "AND company_status=2 OR company_status IS NULL";
															}elseif($_GET['status'] == 3){
																$company_sql = "AND company_status=3";
															}elseif($_GET['status'] == 4){
																$company_sql = "AND company_status=4";
															}elseif($_GET['status'] == 5){
																$company_sql = "AND company_status=5";
															}elseif($_GET['status'] == 6){
																$company_sql = "AND company_status=6";
															}else{
																$company_sql = isset($_GET['archived']) ? 'AND company_status=0' : 'AND (company_status!=0 OR company_status IS NULL)';
															}
														}else{
															$company_sql = isset($_GET['archived']) ? 'AND company_status=0' : 'AND (company_status!=0 OR company_status IS NULL)';
														}


														$query = $db->prepare("
																		SELECT company_id, company_name, company_contact_type, company_city, company_logo, js_partner_id, company_size,contact_firstname, contact_lastname, company_status
																		FROM idk_companies LEFT JOIN idk_contacts ON idk_companies.company_id=idk_contacts.contact_companyid
																		WHERE company_origin = 5 $company_sql GROUP BY company_id");
														$query->execute();
			
														while($row = $query->fetch()){
			
															$company_id = $row['company_id'];
															$company_name = $row['company_name'];
															$company_contact_type = $row['company_contact_type'];
															$company_contact = $row['contact_firstname']." ".$row['contact_lastname'];
															$js_partner_id = $row['js_partner_id'];
															$company_size = $row['company_size'];

															if($row['company_status'] == 0 AND isset($row['company_status'])){
																$company_status = "Archived";
																$company_status_color = "#643939";
															}elseif($row['company_status'] == 1){
																$company_status = "Active";
																$company_status_color = "#6c5723";
															}elseif($row['company_status'] == 2 OR is_null($row['company_status'])){
																$company_status = "New";
																$company_status_color = "#007bff";
															}elseif($row['company_status'] == 3){
																$company_status = "In progress";
																$company_status_color = "#384575";
															}elseif($row['company_status'] == 4){
																$company_status = "On hold";
																$company_status_color = "#65422a";
															}elseif($row['company_status'] == 5){
																$company_status = "Rejected";
																$company_status_color = "#643939";
															}elseif($row['company_status'] == 6){
																$company_status = "Finished";
																$company_status_color = "#28583b";
															}
															if($row['company_logo'] == "none"){
																$company_logo = "none.jpg";
															}else{
																$company_logo = $row['company_logo'];
															}

															switch ($company_size){
																case "1-10": case "11-50": case "1-25": case "20-50": case "26-50": 
																	$company_size_text = "Small";
																break;
																case "bis 100": case "0 - 100": case "51-100": case "0-100":
																	$company_size_text = "Medium";
																break;
																case  "251-500": case "100-500": case "101 - 500": case "101-500": 
																	$company_size_text = "Enterprise";
																break;
																case "1000+": case "> 1000": case ">1000": case "500-1000": case "501 - 1000": case "501-1000": case "501+": case ">501":
																	$company_size_text = "VIP";
																break;
																case null:
																	$company_size_text = "Nije unešeno";
																break;
																default:
																	$company_size_text = "Greška! Problem sa brojem.";

															}
			
															//Get primary phone
															$query_phone = $db->prepare("
																				SELECT comi_data
																				FROM idk_companies_info
																				WHERE comi_group = :comi_group AND comi_primary = :comi_primary AND comi_companyid = :comi_companyid");
			
															$query_phone->execute(array(
																':comi_group' => 1,
																':comi_primary' => 1,
																':comi_companyid' => $company_id));
			
															$row_phone = $query_phone->fetch();
			
															$comapny_phone = $row_phone['comi_data'];
			
															//Get primary email
															$query_email = $db->prepare("
																				SELECT comi_data
																				FROM idk_companies_info
																				WHERE comi_group = :comi_group AND comi_primary = :comi_primary AND comi_companyid = :comi_companyid");
			
															$query_email->execute(array(
																':comi_group' => 2,
																':comi_primary' => 1,
																':comi_companyid' => $company_id));
			
															$row_email = $query_email->fetch();
			
															$company_email = $row_email['comi_data'];
															
															$get_broj_naloga = $db->prepare("SELECT COUNT(nalog_id) as broj_naloga FROM idk_nalozi WHERE kompanija_id = :kompanija_id");
															$get_broj_naloga->execute(array(':kompanija_id' => $company_id));
															$row_broj_naloga = $get_broj_naloga->fetch();
															$broj_naloga = $row_broj_naloga['broj_naloga'];
													?>
													<tr>
														<td class="text-center"><a href="<?php getSiteURL(); ?>companies?page=open&id=<?php echo $company_id; ?>"><div class="idk_profile_img"><img src="<?php getSiteURL(); ?>files/companies/<?php echo $company_logo; ?>"></a></div></td>
														<td><a href="<?php getSiteURL(); ?>companies?page=open&id=<?php echo $company_id; ?>"><?php echo $company_name; ?></a></td>
														<td><?php echo $company_size_text; ?></td>
														<td><?php echo $company_contact; ?></td>
														<td><a href="tel:<?php echo $comapny_phone; ?>"><?php echo $comapny_phone; ?></a></td>
														<td><a href="mailto:<?php echo $company_email; ?>"><?php echo $company_email; ?></a></td>
														<td><a href="<?php getSiteURL(); ?>companies?page=open&id=<?php echo $company_id; ?>#nalozi"><?php echo $broj_naloga; ?></a></td>
														<td><a href="<?php getSiteURL(); ?>partners/<?php echo $js_partner_id; ?>"><?php getInfoPartnerPreporuka($js_partner_id); ?></a></td>
														<td style="background-color: <?php echo $company_status_color ?>; color: white;" ><?php echo $company_status; ?></td>
														<td class="text-center">
															<div class="btn-group material-btn-group">
																<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
																<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
																	<li><a href="<?php getSiteURL(); ?>companies?page=open&id=<?php echo $company_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori</a></li>
																	<li><a href="<?php getSiteURL(); ?>companies?page=edit&id=<?php echo $company_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Uredi</a></li>
																	<li class="idk_dropdown_danger"><a href="#" data="<?php getSiteURL(); ?>companies?page=archive&id=<?php echo $company_id; ?>" data-toggle="modal" data-target="#archiveModal" class="archive material-dropdown-menu__link"><i class="fa fa-trash-o" aria-hidden="true"></i> Arhiviraj</a></li>
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
																	<p>Jeste li sigurni da želite arhivirati profil kompanije?</p>
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
					if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "7" , $getEmployeeStatus)) OR (in_array( "9" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){
		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-briefcase idk_color_green" aria-hidden="true"></i> Dodaj novu kompaniju</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>companies?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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
								<form id="idk_form" action="<?php getSiteURL(); ?>do.php?form=add_company" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<div class="form-group">
										<label for="company_name" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="company_name" id="company_name" placeholder="Naziv" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="company_type" class="col-sm-3 control-label">Vrsta poslovanja:</label>
										<div class="col-sm-9">
											<select class="selectpicker" id="company_type" name="company_type">
												<option value="Ostalo">Ostalo</option>
												<?php
													$select_query = $db->prepare("
																		SELECT otherdata_data
																		FROM idk_otherdata
																		WHERE otherdata_group = :otherdata_group");

													$select_query->execute(array(
																	':otherdata_group' => 3));

													while($select_row = $select_query->fetch()) {
														echo "<option value='" . $select_row['otherdata_data'] . "'>" . $select_row['otherdata_data'] . "</option>";
													}
												?>
											</select>
										</div>
									</div>	
									<div class="form-group">
										<label for="company_idnum" class="col-sm-3 control-label">ID broj:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="company_idnum" id="company_idnum" placeholder="ID broj" />
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="company_taxnum" class="col-sm-3 control-label">PDV broj:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="company_taxnum" id="company_taxnum" placeholder="PDV broj" />
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="company_phone" class="col-sm-3 control-label">Primarni telefon:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="company_phone" id="company_phone" placeholder="Primarni telefon" />
												<span class="materail-input-block__line"></span>
											</div>
										</div>
                                        <script>$('#company_phone').mask("000000000000000", {placeholder: "00387XXXXXXXXXX"});</script>
									</div>
									<div class="form-group">
										<label for="company_email" class="col-sm-3 control-label">Primarni email:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="email" name="company_email" id="company_email" placeholder="Primarni email" />
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="company_address" class="col-sm-3 control-label">Adresa: </label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="company_address" id="company_address" placeholder="Adresa">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
                                    <div class="form-group">
										<label for="company_zipcode" class="col-sm-3 control-label">Poštanski broj: </label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="company_zipcode" id="company_zipcode" placeholder="Poštanski broj">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="company_city" class="col-sm-3 control-label">Grad:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<script>
												  $( function() {
												    var availableTags = [
														<?php

															$select_query = $db->prepare("
																				SELECT company_city
																				FROM idk_companies
																				GROUP BY company_city");

															$select_query->execute();

															while($select_row = $select_query->fetch()) {
																echo '"' . $select_row['company_city'] . '",';
															}

														 ?>
												    ];
												    $( "#company_city" ).autocomplete({
												      source: availableTags
												    });
												  } );
												  </script>
												<input class="form-control materail-input" type="text" name="company_city" id="company_city" placeholder="Grad">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="company_state" class="col-sm-3 control-label">Regija:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<script>
												  $( function() {
												    var availableTags = [
														<?php

															$select_query = $db->prepare("
																				SELECT company_state
																				FROM idk_companies
																				GROUP BY company_state");

															$select_query->execute();

															while($select_row = $select_query->fetch()) {
																echo '"' . $select_row['company_state'] . '",';
															}

														 ?>
												    ];
												    $( "#company_state" ).autocomplete({
												      source: availableTags
												    });
												  } );
												  </script>
												<input class="form-control materail-input" type="text" name="company_state" id="company_state" placeholder="Regija">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="company_country" class="col-sm-3 control-label">Država:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<script>
												  $( function() {
												    var availableTags = [
														<?php

															$select_query = $db->prepare("
																				SELECT company_country
																				FROM idk_companies
																				GROUP BY company_country");

															$select_query->execute();

															while($select_row = $select_query->fetch()) {
																echo '"' . $select_row['company_country'] . '",';
															}

														 ?>
												    ];
												    $( "#company_country" ).autocomplete({
												      source: availableTags
												    });
												  } );
												  </script>
												<input class="form-control materail-input" type="text" name="company_country" id="company_country" placeholder="Država">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="company_info" class="col-sm-3 control-label">Ostale informacije:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="company_info" id="company_info" placeholder="Ostale informacije">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="company_contact_type" class="col-sm-3 control-label">Vrsta kontakta:</label>
										<div class="col-sm-9">
											<select class="selectpicker" id="company_contact_type" name="company_contact_type">
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
										<label for="company_logo" class="col-sm-3 control-label">Logo:</label>
										<div class="col-sm-9">
											<div class="fileinput fileinput-new" data-provides="fileinput">
												<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;"></div>
												<div>
													<span class="btn btn-default btn-file"><span class="fileinput-new">Izaberi logo</span><span class="fileinput-exists">Promijeni</span><input type="file" name="company_logo" id="company_logo"></span>
													<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Ukloni</a>
													<script>
														$(function (){
															$('#company_logo').change(function (){

																var ext = $('#company_logo').val().split('.').pop().toLowerCase();

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
												<div class="alert material-alert material-alert_danger">Greška: Logo koji pokuštavate dodati je veći od dozvoljene veličine.</div>
											</div>
											<div id="idk_alert_ext" class="hidden">
												<div class="alert material-alert material-alert_danger">Greška: Format logotipa koji pokušavate dodati nije dozvoljen.</div>
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
					if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "7" , $getEmployeeStatus)) OR (in_array( "9" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){

						$company_id = $_GET['id'];

						$query = $db->prepare("
										SELECT company_name, company_type, company_idnum, company_taxnum, company_address, company_zipcode, company_city, company_state, company_country, company_info, company_logo, company_contact_type, company_status
										FROM idk_companies
										WHERE company_id = :company_id");

						$query->execute(array(
									':company_id' => $company_id));

						$row = $query->fetch();

							$company_name = htmlspecialchars($row['company_name']);
							$company_type = $row['company_type'];
							$company_idnum = $row['company_idnum'];
							$company_taxnum = $row['company_taxnum'];
							$company_address = $row['company_address'];
							$company_zipcode = $row['company_zipcode'];
							$company_city = $row['company_city'];
							$company_state = $row['company_state'];
							$company_country = $row['company_country'];
							$company_info = $row['company_info'];
							$company_contact_type = $row['company_contact_type'];
							$company_status = $row['company_status'];
							
							if($row['company_logo'] == "none"){
								$company_logo = "none.jpg";
							}else{
								$company_logo = $row['company_logo'];
							}

		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-briefcase idk_color_green" aria-hidden="true"></i> Uredi profile kompanije</h1>
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
								<form id="idk_form" action="<?php getSiteURL(); ?>do.php?form=edit_company" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<input type="hidden" name="company_id" value="<?php echo $company_id; ?>" />
									<div class="form-group">
										<label for="company_name" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="company_name" id="company_name" placeholder="Naziv" value="<?php echo $company_name; ?>" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="company_type" class="col-sm-3 control-label">Vrsta poslovanja:</label>
										<div class="col-sm-9">
											<select class="selectpicker" id="company_type" name="company_type">
												<option value="Ostalo">Ostalo</option>
												<?php
													$select_query = $db->prepare("
																		SELECT otherdata_data
																		FROM idk_otherdata
																		WHERE otherdata_group = :otherdata_group");

													$select_query->execute(array(
																	':otherdata_group' => 3));

													while($select_row = $select_query->fetch()) {
														if($company_type == $select_row['otherdata_data']){ $selected = "selected"; }else{ $selected = ""; }
														echo "<option value='" . $select_row['otherdata_data'] . "' " . $selected . ">" . $select_row['otherdata_data'] . "</option>";
													}
												?>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label for="company_status" class="col-sm-3 control-label">Status kompanije</label>
										<div class="col-sm-9">
											<select class="selectpicker" id="company_status" name="company_status"> 
												<option <?php if($company_status==2 OR is_null($company_status)) echo "selected"; ?> value="2">New</option>
												<option <?php if($company_status==3) echo "selected"; ?> value="3">In progress</option>
												<option <?php if($company_status==4) echo "selected"; ?> value="4">On hold</option>  
												<option <?php if($company_status==5) echo "selected"; ?> value="5">Rejected</option>
												<option <?php if($company_status==1) echo "selected"; ?> value="1">Active</option>
												<option <?php if($company_status==6) echo "selected"; ?> value="6">Finished</option>
												<option <?php if($company_status==0 AND isset($company_status)) echo "selected"; ?> value="0">Archived</option>
												
											</select>
										</div>
									</div>
									<div class="form-group">
										<label for="company_idnum" class="col-sm-3 control-label">ID broj:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="company_idnum" id="company_idnum" value="<?php echo $company_idnum; ?>" />
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="company_taxnum" class="col-sm-3 control-label">PDV broj:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="company_taxnum" id="company_taxnum" value="<?php echo $company_taxnum; ?>" />
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="company_address" class="col-sm-3 control-label">Adresa: </label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="company_address" id="company_address" value="<?php echo $company_address; ?>">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
                                    <div class="form-group">
										<label for="company_zipcode" class="col-sm-3 control-label">Poštanski broj: </label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="company_zipcode" id="company_zipcode" value="<?php echo $company_zipcode; ?>">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="company_city" class="col-sm-3 control-label">Grad:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="company_city" id="company_city" value="<?php echo $company_city; ?>">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="company_state" class="col-sm-3 control-label">Regija:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="company_state" id="company_state" value="<?php echo $company_state; ?>">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="company_country" class="col-sm-3 control-label">Država:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="company_country" id="company_country" value="<?php echo $company_country; ?>">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="company_info" class="col-sm-3 control-label">Ostale informacije:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="company_info" id="company_info" value="<?php echo $company_info; ?>">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="company_contact_type" class="col-sm-3 control-label">Vrsta kontakta:</label>
										<div class="col-sm-9">
											<select class="selectpicker" id="company_contact_type" name="company_contact_type">
												<option value="Ostalo">Ostalo</option>
												<?php
													$select_query = $db->prepare("
																		SELECT otherdata_data
																		FROM idk_otherdata
																		WHERE otherdata_group = :otherdata_group");

													$select_query->execute(array(
																	':otherdata_group' => 2));

													while($select_row = $select_query->fetch()) {
														if($company_contact_type == $select_row['otherdata_data']){ $selected = "selected"; }else{ $selected = ""; }
														echo "<option value='" . $select_row['otherdata_data'] . "' " . $selected . ">" . $select_row['otherdata_data'] . "</option>";
													}
												?>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label for="company_logo" class="col-sm-3 control-label">Logo:</label>
										<div class="col-sm-9">
											<div class="fileinput fileinput-new" data-provides="fileinput">
												<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;">
													<img src="<?php getSiteURL(); ?>files/companies/<?php echo $company_logo; ?>">
												</div>
												<input type="hidden" name="company_logo_url" value="<?php echo $company_logo; ?>" />
												<div>
													<span class="btn btn-default btn-file"><span class="fileinput-new">Izaberi fotografiju</span><span class="fileinput-exists">Promijeni</span><input type="file" name="company_logo" id="company_logo"></span>
													<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Ukloni</a>
													<script>
														$(function (){
															$('#company_logo').change(function (){

																var ext = $('#company_logo').val().split('.').pop().toLowerCase();

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

					$company_id = $_GET['id'];

					$query = $db->prepare("
									SELECT company_name,company_message,company_total_workers_required,company_professions, company_type, company_idnum, company_taxnum, company_address, company_zipcode, company_city, company_state, company_country, company_info, company_inote, company_logo, company_contact_type, company_status, company_origin, company_size, js_partner_id 
									FROM idk_companies
									WHERE company_id = :company_id");

					$query->execute(array(
								':company_id' => $company_id));

					$row = $query->fetch();

						$company_name = $row['company_name'];
						$company_type = $row['company_type'];
						$company_idnum = $row['company_idnum'];
						$company_taxnum = $row['company_taxnum'];
						$company_address = $row['company_address'];
						$company_zipcode = $row['company_zipcode'];
						$company_city = $row['company_city'];
						$company_state = $row['company_state'];
						$company_country = $row['company_country'];
						$company_info = $row['company_info'];
						$company_inote = $row['company_inote'];
						$company_contact_type = $row['company_contact_type'];
						$company_origin = $row['company_origin'];
						$company_size = $row['company_size'];
						$company_message = $row['company_message'];
						$company_total_workers_required = $row['company_total_workers_required'];
						$company_professions = $row['company_professions'];
						$js_partner_id = $row['js_partner_id'];

						if($row['company_logo'] == "none"){
							$company_logo = "none.jpg";
						}else{
							$company_logo = $row['company_logo'];
						}

						switch ($company_size){
							case "1-10": case "11-50": case "1-25": case "20-50": case "26-50": 
								$company_size_text = "Small (1 - 50)";
							break;
							case "bis 100": case "0 - 100": case "51-100": case "0-100":
								$company_size_text = "Medium (51 - 100)";
							break;
							case  "251-500": case "100-500": case "101 - 500": case "101-500": 
								$company_size_text = "Enterprise (101 - 500)";
							break;
							case "1000+": case "> 1000": case ">1000": case "500-1000": case "501 - 1000": case "501-1000": case "501+": case ">501":
								$company_size_text = "VIP (501+)";
							break;
							case null:
								$company_size_text = "Nije unešeno";
							break;
							default:
								$company_size_text = "Greška! Problem sa brojem.";

						}
						
						if($row['company_status'] == 0 AND isset($row['company_status'])){
							$company_status = "Archived";
						}elseif($row['company_status'] == 1){
							$company_status = "Active";
						}elseif($row['company_status'] == 2 OR is_null($row['company_status'])){
							$company_status = "New";
						}elseif($row['company_status'] == 3){
							$company_status = "In progress";
						}elseif($row['company_status'] == 4){
							$company_status = "On hold";
						}elseif($row['company_status'] == 5){
							$company_status = "Rejected";
						}elseif($row['company_status'] == 6){
							$company_status = "Finished";
						}

						/*$check_nalog_num = $db->prepare("
							SELECT nalog_id
							FROM idk_nalozi
							ORDER BY nalog_id DESC LIMIT 1 ");

						$check_nalog_num->execute();
						$row2 = $check_nalog_num->fetch();
						$lastID = $row2['nalog_id'];
						$broj_naloga = $lastID + 3;*/



						$check_nalog_ai = $db->prepare("
							SELECT AUTO_INCREMENT
							FROM information_schema.TABLES
							WHERE TABLE_SCHEMA = '$envConfig->DB_DATABASE'
							AND TABLE_NAME = 'idk_nalozi';
						");
						$check_nalog_ai->execute();
						$row_ai = $check_nalog_ai->fetch();
						$broj_naloga = $row_ai['AUTO_INCREMENT'];
						$lastID = $broj_naloga - 1;

		?>
			<script>
				$(document).ready(function(){
					// get the tab from url
					var hash = window.location.hash;
					// if a hash is present (when you come to this page)
					console.log(hash);
					if (hash !='') {
						// show the tab
						$('.nav-tabs a[href="' + hash + '"]').tab('show');
					}
				});
			</script>
			<div class="row">
				<div class="col-xs-8">
					<a class="fancybox" rel="group" href="<?php getSiteURL(); ?>files/companies/<?php echo $company_logo; ?>"><div class="idk_profile_img"><img src="<?php getSiteURL(); ?>files/companies/<?php echo $company_logo; ?>"></div></a> <h1><?php echo $company_name; ?></h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>companies?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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
										<div class="alert material-alert material-alert_success">Uspješno ste dodali novu bilješku.</div>
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
										<div class="alert material-alert material-alert_success">Uspješno ste uredili profil kompanije.</div>
										<script>$(function() { $('[href="#info"]').tab('show'); });</script>
									<?php
								}elseif($mess == 22){ ?>
									<div class="alert material-alert material-alert_success">Uspješno ste kreirali nalog.</div>
									<script>$(function() { $('[href="#nalozi"]').tab('show'); });</script>
								<?php
							}
								?>
							</div>
						</div>
						<?php 
							if(isset($_GET['tab'])){
								$tab=$_GET['tab'];
							}else{
								$tab="info";
							}
						?>
						<div id="myTabs" class="panel-group material-tabs-group">
							<ul class="nav nav-tabs material-tabs material-tabs_primary">
								<li class="<?php if($tab=="info"){echo "active";} ?>"><a href="#info" class="material-tabs__tab-link" data-toggle="tab">Informacije</a></li>
								<li><a href="#timeline" class="material-tabs__tab-link" data-toggle="tab">Aktivnosti</a></li>
								<li><a href="#tasks" class="material-tabs__tab-link" data-toggle="tab">Zadaci</a></li>
								<li><a href="#contacts" class="material-tabs__tab-link" data-toggle="tab">Kontakti</a></li>
								<li><a href="#notes" class="material-tabs__tab-link" data-toggle="tab">Bilješke</a></li>
								<li><a href="#documents" class="material-tabs__tab-link" data-toggle="tab">Dokumenti</a></li>
								<li><a href="#important" class="material-tabs__tab-link" data-toggle="tab">Važno</a></li>
								<li><a href="#nalozi" class="material-tabs__tab-link" data-toggle="tab">Nalozi</a></li>
								<li class="<?php if($tab=="company_users"){echo "active";} ?>"><a href="#company_users" class="material-tabs__tab-link" data-toggle="tab">JobSoft korisnici</a></li>
							</ul>
							<div class="tab-content materail-tabs-content">
								<div class="tab-pane fade <?php if($tab=="info"){echo "active in";} ?>" id="info">
									<div class="row idk_employee_info">
										<div class="col-md-6">
											<div class="row">
												<div class="col-sm-9">
													<h5>Osnovne informacije</h5>
												</div>
												<div class="col-sm-3 text-right">
													<a href="companies?page=edit&id=<?php echo $company_id; ?>" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-pencil" aria-hidden="true"></i> <span></span></a>
												</div>
											</div>

											<div class="row">
												<strong class="col-sm-4 text-right">Naziv:</strong>
												<div class="col-sm-8"><?php echo $company_name; ?> <?php echo $company_type; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Partner:</strong>
												<div class="col-sm-8"><?php getInfoPartnerPreporuka($js_partner_id); ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Veličina kompanije:</strong>
												<div class="col-sm-8"><?php echo $company_size_text; ?> </div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">ID broj:</strong>
												<div class="col-sm-8"><?php echo $company_idnum; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">PDV broj:</strong>
												<div class="col-sm-8"><?php echo $company_taxnum; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Adresa:</strong>
												<div class="col-sm-8"><?php echo $company_address; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Poštanski broj:</strong>
												<div class="col-sm-8"><?php echo $company_zipcode; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Grad:</strong>
												<div class="col-sm-8"><?php echo $company_city; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Regija:</strong>
												<div class="col-sm-8"><?php echo $company_state; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Država:</strong>
												<div class="col-sm-8"><?php echo $company_country; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Status:</strong>
												<div class="col-sm-8"><?php echo $company_status; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Vrsta kontakta:</strong>
												<div class="col-sm-8"><?php echo $company_contact_type; ?></div>
											</div>
											<?php 
												if($company_origin == 4){
											?>
											<div class="row">
												<strong class="col-sm-4 text-right">Porijeklo:</strong>
												<div class="col-sm-8"><span class="label label-primary material-label material-label_primary main-container__column">ANGACOM</span></div>
											</div>
											<?php 
												}
												if ($company_origin == 5) {
											?> 
											<div class="row">
												<strong class="col-sm-4 text-right">Porijeklo:</strong>
												<div class="col-sm-8"><span class="label label-primary material-label material-label_primary main-container__column">Partner App</span></div>
											</div>
											<?php
												}
												if ($company_origin == 6) {
											?> 
											<div class="row">
												<strong class="col-sm-4 text-right">Porijeklo:</strong>
												<div class="col-sm-8"><span class="label label-primary material-label material-label_primary main-container__column">Join</span></div>
											</div>
											<?php
												}
											?>
											<div class="row">
												<strong class="col-sm-4 text-right">Bilješke iz aplikacije:</strong>
												<div class="col-sm-8"><?php echo $company_message;  ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Ukupni traženi broj zaposlenih:</strong>
												<div class="col-sm-8"><?php echo $company_total_workers_required;  ?></div>
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
																	<form action="<?php getSiteURL(); ?>do.php?form=add_company_phone" method="post" role="form" class="form-horizontal">
																		<input type="hidden" name="comi_companyid" value="<?php echo $company_id; ?>" />
																		<div class="form-group">
																			<label for="comi_title" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv:</label>
																			<div class="col-sm-9">
																				<div class="materail-input-block materail-input-block_success">
																					<input class="form-control materail-input" type="text" name="comi_title" id="comi_title" placeholder="Mobilni" required>
																					<span class="materail-input-block__line"></span>
																				</div>
																			</div>
																		</div>
																		<div class="form-group">
																			<label for="comi_data" class="col-sm-3 control-label"><span class="text-danger">*</span> Broj telefona:</label>
																			<div class="col-sm-9">
																				<div class="materail-input-block materail-input-block_success">
																					<input class="form-control materail-input" type="text" name="comi_data" id="comi_data" placeholder="003876XXXXXXXX" required>
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
																				SELECT comi_id, comi_title, comi_data, comi_primary
																				FROM idk_companies_info
																				WHERE comi_group = :comi_group AND comi_companyid = :comi_companyid
																				ORDER BY comi_primary DESC");

															$query_phones->execute(array(
																	':comi_group' => 1,
																	':comi_companyid' => $company_id));

															while($row_phones = $query_phones->fetch()){

																$comi_id = $row_phones['comi_id'];
																$comi_title = $row_phones['comi_title'];
																$comi_data = $row_phones['comi_data'];
																$comi_primary = $row_phones['comi_primary'];
														?>
														<tr>
															<td><?php echo $comi_title; ?>:</td>
															<td><a href="tel:<?php echo $comi_data; ?>"><?php echo $comi_data; ?></a></td>
															<td class="text-right">
																<ul class="list-inline">
																	<?php if($comi_primary == 1){ echo '<li data-toggle="tooltip" data-placement="top" title="Primarni"><i class="fa fa-star fa-lg text-success" aria-hidden="true"></i></li>'; }else{ echo '<li data-toggle="tooltip" data-placement="top" title="Postavi kao primarni"><a href="' . getSiteURLr() . 'do.php?form=set_primary_phone_company&comi_id=' . $comi_id . '&comi_companyid=' . $company_id  . '"><i class="fa fa-star-o fa-lg text-success" aria-hidden="true"></i></a></li>'; } ?>
																	<li data-toggle="tooltip" data-placement="top" title="Obriši"><a href="#" data="<?php getSiteURL(); ?>companies?page=del_company_info&id=<?php echo $comi_id; ?>" data-toggle="modal" data-target="#delPhoneModal" class="delPhone"><i class="fa fa-trash-o fa-lg text-danger" aria-hidden="true"></i></a></li>
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
																	<form action="<?php getSiteURL(); ?>do.php?form=add_company_email" method="post" role="form" class="form-horizontal">
																		<input type="hidden" name="comi_companyid" value="<?php echo $company_id; ?>" />
																		<div class="form-group">
																			<label for="comi_title" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv:</label>
																			<div class="col-sm-9">
																				<div class="materail-input-block materail-input-block_success">
																					<input class="form-control materail-input" type="text" name="comi_title" id="comi_title" placeholder="Privatni" required>
																					<span class="materail-input-block__line"></span>
																				</div>
																			</div>
																		</div>
																		<div class="form-group">
																			<label for="comi_data" class="col-sm-3 control-label"><span class="text-danger">*</span> Email adresa:</label>
																			<div class="col-sm-9">
																				<div class="materail-input-block materail-input-block_success">
																					<input class="form-control materail-input" type="email" name="comi_data" id="comi_data" placeholder="info@primjer.com" required>
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
																				SELECT comi_id, comi_title, comi_data, comi_primary
																				FROM idk_companies_info
																				WHERE comi_group = :comi_group AND comi_companyid = :comi_companyid
																				ORDER BY comi_primary DESC");

															$query_phones->execute(array(
																	':comi_group' => 2,
																	':comi_companyid' => $company_id));

															while($row_phones = $query_phones->fetch()){

																$comi_id = $row_phones['comi_id'];
																$comi_title = $row_phones['comi_title'];
																$comi_data = $row_phones['comi_data'];
																$comi_primary = $row_phones['comi_primary'];
														?>
														<tr>
															<td><?php echo $comi_title; ?>:</td>
															<td><a href="mailto:<?php echo $comi_data; ?>"><?php echo $comi_data; ?></a></td>
															<td class="text-right">
																<ul class="list-inline">
																	<?php if($comi_primary == 1){ echo '<li data-toggle="tooltip" data-placement="top" title="Primarni"><i class="fa fa-star fa-lg text-success" aria-hidden="true"></i></li>'; }else{ echo '<li data-toggle="tooltip" data-placement="top" title="Postavi kao primarni"><a href="' . getSiteURLr() . 'do.php?form=set_primary_email_company&comi_id=' . $comi_id . '&comi_companyid=' . $company_id . '"><i class="fa fa-star-o fa-lg text-success" aria-hidden="true"></i></a></li>'; } ?>
																	<li data-toggle="tooltip" data-placement="top" title="Obriši"><a href="#" data="<?php getSiteURL(); ?>companies?page=del_company_info&id=<?php echo $comi_id; ?>" data-toggle="modal" data-target="#delEmailModal" class="delEmail"><i class="fa fa-trash-o fa-lg text-danger" aria-hidden="true"></i></a></li>
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
																	<form action="<?php getSiteURL(); ?>do.php?form=add_company_other" method="post" role="form" class="form-horizontal">
																		<input type="hidden" name="comi_companyid" value="<?php echo $company_id; ?>" />
																		<div class="form-group">
																			<label for="comi_title" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv:</label>
																			<div class="col-sm-9">
																				<div class="materail-input-block materail-input-block_success">
																					<input class="form-control materail-input" type="text" name="comi_title" id="comi_title" placeholder="Facebook" required>
																					<span class="materail-input-block__line"></span>
																				</div>
																			</div>
																		</div>
																		<div class="form-group">
																			<label for="comi_data" class="col-sm-3 control-label"><span class="text-danger">*</span> Adresa:</label>
																			<div class="col-sm-9">
																				<div class="materail-input-block materail-input-block_success">
																					<input class="form-control materail-input" type="text" name="comi_data" id="comi_data" placeholder="https://www.facebook.com/profil" required>
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
																				SELECT comi_id, comi_title, comi_data, comi_primary
																				FROM idk_companies_info
																				WHERE comi_group = :comi_group AND comi_companyid = :comi_companyid
																				ORDER BY comi_primary DESC");

															$query_phones->execute(array(
																	':comi_group' => 3,
																	':comi_companyid' => $company_id));

															while($row_phones = $query_phones->fetch()){

																$comi_id = $row_phones['comi_id'];
																$comi_title = $row_phones['comi_title'];
																$comi_data = $row_phones['comi_data'];
																$comi_primary = $row_phones['comi_primary'];
														?>
														<tr>
															<td><?php echo $comi_title; ?>:</td>
															<td><?php echo $comi_data; ?></td>
															<td class="text-right">
																<ul class="list-inline">
																	<?php if($comi_primary == 1){ echo '<li data-toggle="tooltip" data-placement="top" title="Primarni"><i class="fa fa-star fa-lg text-success" aria-hidden="true"></i></li>'; }else{ echo '<li data-toggle="tooltip" data-placement="top" title="Postavi kao primarni"><a href="' . getSiteURLr() . 'do.php?form=set_primary_other_company&comi_id=' . $comi_id . '&comi_companyid=' . $company_id  . '"><i class="fa fa-star-o fa-lg text-success" aria-hidden="true"></i></a></li>'; } ?>
																	<li data-toggle="tooltip" data-placement="top" title="Obriši"><a href="#" data="<?php getSiteURL(); ?>companies?page=del_company_info&id=<?php echo $comi_id; ?>" data-toggle="modal" data-target="#delOtherModal" class="delOther"><i class="fa fa-trash-o fa-lg text-danger" aria-hidden="true"></i></a></li>
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
											<div class="row">
												<div class="col-sm-9">
													<h5>Profesije</h5>
												</div>
											</div>
											<div class="table-responsive">
												<table class="table table-striped">
													<tbody>
														<?php
														if(is_null($company_professions)){
															$company_professions = 0;
														}
														$professions_sql = "SELECT kp_id,kp_ime_de,kp_ime_en FROM idk_kandidat_pozicija WHERE kp_id IN ($company_professions)";
														$professions_stmt = $db->prepare($professions_sql);
														$professions_stmt->execute();
														$professions = $professions_stmt->fetchAll();
															foreach($professions as $profession){
															$profession_name = $profession['kp_ime_de'] ?? $profession['kp_ime_en'];
														?>
														<tr>
															<td style="width: 20%;">Profesija <?php echo $profession['kp_id'].": ";  ?></td>
															<td><a href="positions?page=open&id=<?php echo $profession['kp_id']; ?> "><?php echo 	$profession_name;  ?></a></td>
														</tr>
														<?php } ?>
													</tbody>
												</table>
											</div>
										</div>
									</div>
								</div>
								<div class="tab-pane fade" id="timeline">
									<form id="idk_form" action="<?php getSiteURL(); ?>do.php?form=add_timeline_comapny" method="post" class="form-horizontal" role="form">
										<input type="hidden" name="timeline_dataid" value="<?php echo $company_id; ?>">
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
																	//$( function() {	$( "#timeline_date" ).dateDropper(); });
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
																	':timeline_dataid' => $company_id,
																	':timeline_group' => 2));

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
																<a href="#" data="<?php getSiteURL(); ?>companies?page=del_timeline&id=<?php echo $timeline_id; ?>" data-toggle="modal" data-target="#deleteTimelineModal" class="delete_timeline btn material-btn material-btn_danger main-container__column"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
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
									<form id="idk_form" action="<?php getSiteURL(); ?>do.php?form=add_task_company" method="post" class="form-horizontal" role="form">
										<input type="hidden" name="task_dataid" value="<?php echo $company_id; ?>">
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
																	//$( function() {	$( "#task_duedate" ).dateDropper(); });
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
																	':task_dataid' => $company_id,
																	':task_group' => 3));

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
																						<form action="<?php getSiteURL(); ?>do.php?form=add_task_done_company" method="post" role="form" class="form-horizontal">
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
																						<form action="<?php getSiteURL(); ?>do.php?form=add_task_notdone_company" method="post" role="form" class="form-horizontal">
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
																		<a href="#" data="<?php getSiteURL(); ?>companies?page=del_task&id=<?php echo $task_id; ?>" data-toggle="modal" data-target="#deleteTaskModal" class="delete_task btn material-btn material-btn_danger main-container__column"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
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
																						<form action="<?php getSiteURL(); ?>do.php?form=add_task_emailnotifi_company" method="post" role="form" class="form-horizontal">
																							<input type="hidden" name="task_id" id="task_id_emailnotifi" value="" />
																							<input type="hidden" name="task_dataid" value="<?php echo $company_id; ?>" />
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
																						<form action="<?php getSiteURL(); ?>do.php?form=add_task_repeat_company" method="post" role="form" class="form-horizontal">
																							<input type="hidden" name="task_id" id="task_id_repeat" value="" />
																							<input type="hidden" name="task_dataid" value="<?php echo $company_id; ?>" />
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
								<div class="tab-pane fade" id="contacts">
									<ul class="list-inline text-right">
										<li><a href="<?php getSiteURL(); ?>contacts?page=add&company=<?php echo $company_id; ?>" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj kontakt</span></a></li>
									</ul>
									<hr>
									<script type="text/javascript">
										$(document).ready(function() {
											$('#idk_table_contacts').DataTable({

												responsive: true,

												"order": [[ 1, "asc" ]],

												 "bAutoWidth": false,

												"aoColumns": [
														{ "width": "5%", "bSortable": false },
														{ "width": "45%" },
														{ "width": "25%" },
														{ "width": "25%" }
													]
											});
										} );
									</script>
									<table id="idk_table_contacts" class="display" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th></th>
												<th>Ime i prezime</th>
												<th class="text-center">Telefon</th>
												<th class="text-center">E-mail</th>
											</tr>
										</thead>
										<tbody>
											<?php
												$query = $db->prepare("
																SELECT contact_id, contact_firstname, contact_lastname, contact_city, contact_image, contact_type
																FROM idk_contacts
																WHERE contact_status != :contact_status AND contact_companyid = :contact_companyid");

												$query->execute(array(
													':contact_companyid' => $company_id,
													':contact_status' => 0));

												while($row = $query->fetch()){

													$contact_id = $row['contact_id'];
													$contact_firstname = $row['contact_firstname'];
													$contact_lastname = $row['contact_lastname'];

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
												<td class="text-center"><a href="tel:<?php echo $contact_phone; ?>"><?php echo $contact_phone; ?></a></td>
												<td class="text-center"><a href="mailto:<?php echo $contact_email; ?>"><?php echo $contact_email; ?></a></td>
											</tr>
											<?php } ?>
										</tbody>
									</table>
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
														<form action="<?php getSiteURL(); ?>do.php?form=add_company_note" method="post" role="form" class="form-horizontal">
															<input type="hidden" name="note_dataid" value="<?php echo $company_id; ?>" />
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
															':note_dataid' => $company_id,
															':note_group' => 3));

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
																		':note_group' => 3,
																		':note_dataid' => $company_id));

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
															<a href="#" data="<?php getSiteURL(); ?>companies?page=del_note&id=<?php echo $note_id; ?>" data-toggle="modal" data-target="#deleteNoteModal" class="delete_note btn material-btn material-btn_danger main-container__column"><i class="fa fa-trash-o fa-lg" aria-hidden="true"></i></a>
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
														<form action="<?php getSiteURL(); ?>do.php?form=add_company_doc" enctype="multipart/form-data" method="post" role="form" class="form-horizontal">
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
															<input type="hidden" name="document_dataid" value="<?php echo $company_id; ?>" />
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
															':document_group' => 3,
															':document_dataid' => $company_id));

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
												<td class="text-center"><a href="<?php getSiteURL(); ?>files/files/companies/<?php echo $document_file; ?>" class="btn material-btn material-btn_success main-container__column" target="_BLANK"><?php echo $document_icon; ?></a></td>
												<td class="text-center"><a href="#" data="<?php getSiteURL(); ?>companies?page=del_doc&id=<?php echo $document_id; ?>" data-toggle="modal" data-target="#deleteDocModal" class="delete_doc btn material-btn material-btn_danger main-container__column"><i class="fa fa-trash-o fa-lg" aria-hidden="true"></i></a></td>
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
									<form action="<?php getSiteURL(); ?>do.php?form=save_company_inote" method="post" role="form" class="form-horizontal">
										<input type="hidden" name="company_id" value="<?php echo $company_id; ?>" />
										<div class="form-group">
											<div class="col-md-offset-1 col-sm-10">
												<div class="form-group materail-input-block materail-input-block_success">
													<textarea id="inote" class="form-control materail-input material-textarea" name="company_inote" placeholder="Važne bilješke" rows="8"><?php echo base64_decode($company_inote); ?></textarea>
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
								<div class="tab-pane fade" id="nalozi">
									<ul class="list-inline text-right">
										<li><a href="#" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" data-toggle="modal" data-target="#nalogModal"><i class="fa fa-plus" aria-hidden="true"></i> <span>Kreiraj nalog</span></a></li>
										<!-- Modal add document -->
										<div class="modal material-modal material-modal_primary fade text-left" id="nalogModal">
											<div class="modal-dialog modal-lg">
												<div class="modal-content material-modal__content">
													<div class="modal-header material-modal__header">
														<button class="close material-modal__close" data-dismiss="modal">&times;</button>
														<h4 class="modal-title material-modal__title">Kreiraj nalog</h4>
													</div>
													<div class="modal-body material-modal__body">
														<form action="<?php getSiteURL(); ?>do.php?form=kreiraj_nalog" enctype="multipart/form-data" method="post" role="form" class="form-horizontal">
															<input type="hidden" name="kompanija_id" value="<?php echo $company_id; ?>" />
															<!-- JS Partner Info -->
																<?php 
																	$resultForPartner = getCompanyHasJSPartnerArrayR($company_id);
																	if ($resultForPartner["status"] != 102 AND $resultForPartner["status"] != 101 AND $resultForPartner["status"] != 0){			
																		?>
																			<div class="form-group">
																				<div class="col-md-offset-2 col-sm-8">
																					<div class="row" style="margin-bottom: 10px;">
																						<div class="col-sm-12 text-center">
																							<span class="label label-default material-label material-label_default main-container__column">
																								Kompanija registrovana preko Partnera
																							</span>
																						</div>
																					</div>
																					<div class="row" style="margin-bottom: 10px;">
																						<div class="col-sm-5 text-right">
																							Informacije o partneru:
																						</div>
																						<div class="col-sm-7 text-center">
																							<span class="label label-success material-label material-label_success main-container__column">
																								<?php 
																									echo $resultForPartner["partner_name"];
																								?> 
																							</span>
																						</div>
																					</div>
																					<div class="row">
																						<div class="col-md-offset-2 col-sm-8 text-center">
																							<?php 
																								$resultOrders = getJSPartnerHasNalogArrayR($resultForPartner["parnter_id"], $company_id); 
																								if ($resultOrders["status"] != 102){
																									if ($resultOrders["status"] == 1) {
																										$resultOrderLinks = array();
																										$resultOrderLinksView = "";
																										foreach($resultOrders["count"] AS $countOfResultOrders) { 
																											array_push($resultOrderLinks, '<a href="'.getSiteUrlr().'nalozi?page=open&id='.$resultOrders["nalogIDs"][$countOfResultOrders].'" target="_BLANK"><span class="label label-default">'.$resultOrders["nalogName"][$countOfResultOrders].'</span></a>');
																										}
																										$resultOrderLinksView = implode(" , ", $resultOrderLinks);
																										echo '<div class="alert alert-warning text-center" role="alert">Provjerom utvrđeno da gore navedeni Partner već ima povezan nalog '.$resultOrderLinksView.'!</div>';
																										unset($resultOrderLinks);
																									} else {
																										echo '<div class="alert alert-success text-center" role="alert">Provjerom je utvrđeno da će novokreirani nalog biti vezan za gore navedenog Partnera!</div>';
																									}
																								} else {
																									echo '<div class="alert alert-danger text-center" role="alert">Desio se problem prilikom provjere naloga za koje je vezan Partner!</div>';
																								}
																								unset($resultOrders);
																							?>
																						</div>
																					</div>
																					<hr>
																				</div>
																			</div>
																			
																		<?php 
																	}
																	unset($resultForPartner);
																?>
															<!-- JS Partner Info -->
															<div class="form-group">
																<div class="col-md-offset-2 col-sm-8">
																	<div class="form-group materail-input-block materail-input-block_success">
																		<?php if($lastID < 10){ ?>
																		<input type="text" class="form-control materail-input" name="broj_naloga" id="broj_naloga" value="<?php echo '00'.$broj_naloga.'/'.date("Y"); ?>" required>
																		<?php }elseif($lastID >= 10 && $lastID < 100){ ?>
																		<input type="text" class="form-control materail-input" name="broj_naloga" id="broj_naloga" value="<?php echo '0'.$broj_naloga.'/'.date("Y"); ?>" required>
																		<?php }else{ ?>	
																		<input type="text" class="form-control materail-input" name="broj_naloga" id="broj_naloga" value="<?php echo $broj_naloga.'/'.date("Y"); ?>" required>	
																		<?php } ?>	
																		<span class="materail-input-block__line"></span>
																	</div>
																</div>
															</div>
															<div class="form-group">
																<div class="col-md-offset-2 col-sm-8">
																	<div class="form-group materail-input-block materail-input-block_success">
																		<input type="text" class="form-control materail-input" name="nalog_naziv" id="nalog_naziv" placeholder="Naziv naloga" required>
																		<span class="materail-input-block__line"></span>
																	</div>
																</div>
															</div>
															<div class="form-group">
																<div class="col-md-offset-2 col-sm-8">
																	<div class="form-group materail-input-block materail-input-block_success">
																		<input type="text" class="form-control materail-input" name="nalog_opis" id="nalog_opis" placeholder="Opis naloga">
																		<span class="materail-input-block__line"></span>
																	</div>
																</div>
															</div>
															<div class="form-group">
																<div class="col-md-offset-2 col-sm-8">
																	<div class="form-group materail-input-block materail-input-block_success">
																		<input type="text" class="form-control materail-input" name="nalog_partner_provizija" id="nalog_partner_provizija" placeholder="Provizija naloga za partnere(€)">
																		<span class="materail-input-block__line"></span>
																	</div>
																</div>
															</div>
															<div class="form-group">
																<div class="col-md-offset-2 col-sm-8">
																	<div class="row">
																		<div class="col-sm-5" style="padding-top:5px">Datum potpisa ugovora:</div>
																		<div class="col-sm-7">
																			<div class="materail-input-block materail-input-block_success">
																				<input class="form-control materail-input" type="text" name="datum_potpisa_ugovora" autocomplete="off" id="datum_potpisa_ugovora" required>
																				<span class="materail-input-block__line"></span>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
															<script>
																$( function() {
																	$( "#datum_potpisa_ugovora" ).datepicker({
																	changeMonth: true,
																	changeYear: true,
																		dateFormat: 'dd.mm.yy',
																		yearRange: '2017:2026'
																	});
																});
															</script>
															<div class="form-group">
																<div class="col-md-offset-2 col-sm-8">
																	<div class="row">
																		<div class="col-sm-5" style="padding-top:5px">Vrsta ugovora:</div>
																		<div class="col-sm-7">
																			<select class="selectpicker" name="nalog_ugovor" id="nalog_ugovor" title = "Odaberite vrstu ugovora" required>
																				<option value="Okvirni">Okvirni</option>
																				<option value="Nalog">Nalog</option>
																			</select>
																		</div>
																	</div>
																</div>
															</div>
															<div class="form-group">
																<div class="col-md-offset-2 col-sm-8">
																	<div class="row">
																		<div class="col-sm-5" style="padding-top:5px">Firma fakturisanja:</div>
																		<div class="col-sm-7">
																			<select class="selectpicker" name="nalog_firma_fakturisanja" id="nalog_firma_fakturisanja" title = "Odaberite opciju" required>
																				<option value = "1" data-subtext="Jobstep GmbH">DE</option>
																				<option value = "2" data-subtext="Jobstep Int Gmbh">CH</option>
																			</select>
																		</div>
																	</div>
																</div>
															</div>
															<div class="form-group nalog_dospijece">
																<div class="col-md-offset-2 col-sm-8">
																	<div class="row">
																		<div class="col-sm-5" style="padding-top:5px">Broj dana za dospijeće plaćanja:</div>
																		<div class="col-sm-7">
																			<div class="materail-input-block materail-input-block_success">
																				<input type="number" class="form-control materail-input" name="nalog_dospijece" id="nalog_dospijece" placeholder="Broj dana" min="0" value="0" step="1">
																				<span class="materail-input-block__line"></span>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
															<hr>
															<div class="form-group">
																<div class="col-md-offset-2 col-sm-8">
																	<div class="row">
																		<div class="col-sm-5" style="padding-top:5px">Poslodavac plaća nostrifikaciju:</div>
																		<div class="col-sm-7">
																			<select class="selectpicker" name="nalog_nostrifikacija" id="nalog_nostrifikacija" title = "Odaberite opciju" required>
																				<option value = "0">NE</option>
																				<option value = "1">DA</option>
																			</select>
																		</div>
																	</div>
																</div>
															</div>
															<script>
																$(document).ready(function() {
																	$(".nalog_nostrifikacija_provizija_polje").hide();
																	$(".nalog_nostrifikacija_nacin_polje").hide();
																	$(".nalog_nostrifikacija_broj_rata_polje").hide();
																	$("#nalog_nostrifikacija_provizija").removeAttr('required').val(null);
																	$("#nalog_nostrifikacija_nacin").removeAttr('required').val(null).selectpicker("refresh");
																	$("#nalog_nostrifikacija_broj_rata").removeAttr('required').val(null);

																	$('#nalog_nostrifikacija').change(function(){

																		var nalog_nostrifikacija_value = parseInt($("#nalog_nostrifikacija").val());

																		if ( nalog_nostrifikacija_value === 1) {
																			$(".nalog_nostrifikacija_provizija_polje").show();
																			$(".nalog_nostrifikacija_nacin_polje").show();
																			$(".nalog_nostrifikacija_broj_rata_polje").hide();
																			$("#nalog_nostrifikacija_provizija").attr('required', 'required').val(null);
																			$("#nalog_nostrifikacija_nacin").attr('required', 'required').val(null).selectpicker("refresh");
																			$("#nalog_nostrifikacija_broj_rata").removeAttr('required').val(null);
																		} else {
																			$(".nalog_nostrifikacija_provizija_polje").hide();
																			$(".nalog_nostrifikacija_nacin_polje").hide();
																			$(".nalog_nostrifikacija_broj_rata_polje").hide();
																			$("#nalog_nostrifikacija_provizija").removeAttr('required').val(null);
																			$("#nalog_nostrifikacija_nacin").removeAttr('required').val(null).selectpicker("refresh");
																			$("#nalog_nostrifikacija_broj_rata").removeAttr('required').val(null);
																		}

																	});
																});
															</script>
															<div class="form-group nalog_nostrifikacija_provizija_polje">
																<div class="col-md-offset-2 col-sm-8">
																	<div class="row">
																		<div class="col-sm-5" style="padding-top:5px">Provizija nostrifikacije:</div>
																		<div class="col-sm-7">
																			<div class="materail-input-block materail-input-block_success">
																				<input type="number" class="form-control materail-input" name="nalog_nostrifikacija_provizija" id="nalog_nostrifikacija_provizija" placeholder="Provizija" min="0" value="0" step="0.01">
																				<span class="materail-input-block__line"></span>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
															<div class="form-group nalog_nostrifikacija_nacin_polje">
																<div class="col-md-offset-2 col-sm-8">
																	<div class="row">
																		<div class="col-sm-5" style="padding-top:5px">Način nostrifikacije:</div>
																		<div class="col-sm-7">
																			<select class="selectpicker" name="nalog_nostrifikacija_nacin" id="nalog_nostrifikacija_nacin" title = "Odaberite opciju">
																				<option value = "1">Standardni način</option>
																				<option value = "2">Mjesecne rate</option>
																			</select>
																		</div>
																	</div>
																</div>
															</div>
															<script>
																$(document).ready(function() {
																	
																	$('#nalog_nostrifikacija_nacin').change(function(){

																		var nalog_nostrifikacija_nacin_value = parseInt($("#nalog_nostrifikacija_nacin").val());

																		if ( nalog_nostrifikacija_nacin_value === 2) { 
																			$(".nalog_nostrifikacija_broj_rata_polje").show();
																			$("#nalog_nostrifikacija_broj_rata").attr('required', 'required').val(null);
																		} else {
																			$(".nalog_nostrifikacija_broj_rata_polje").hide();
																			$("#nalog_nostrifikacija_broj_rata").removeAttr('required').val(null);
																		}

																	});
																});
															</script>
															<div class="form-group nalog_nostrifikacija_broj_rata_polje">
																<div class="col-md-offset-2 col-sm-8">
																	<div class="row">
																		<div class="col-sm-5" style="padding-top:5px">Broj rata mjesečno:</div>
																		<div class="col-sm-7">
																			<div class="materail-input-block materail-input-block_success">
																				<input type="number" class="form-control materail-input" name="nalog_nostrifikacija_broj_rata" id="nalog_nostrifikacija_broj_rata" placeholder="Broj potrebnih kandidata" min="1" value="0" step="1">
																				<span class="materail-input-block__line"></span>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
															<hr>
															<div class="form-group">
																<div class="col-md-offset-2 col-sm-8">
																	<div class="row">
																		<div class="col-sm-5" style="padding-top:5px">Način plaćanja:</div>
																		<div class="col-sm-7">
																			<select class="selectpicker" name="nalog_financije" id="nalog_financije" title = "Odaberite način plaćanja" required>
																				<option value = "1">Standarni</option>
																				<option value = "2">Mjesečno</option>
																				<option value = "3">Po plati</option>
																			</select>
																		</div>
																	</div>
																</div>
															</div>
															<script>
																$(document).ready(function() {
																	$(".provizija_po_plati_help_desc").hide();
																	$("#provizija_po_plati_close").hide();

																	$(".avans_help_desc").hide();
																	$("#avans_close").hide();

																	$(".broj_kandidata_polje").hide();
																	$("#nalog_potrebno_kandidata").removeAttr('required').val(null);
																	$(".provizija_polje").hide();
																	$("#nalog_provizija").removeAttr('required').val(null);
																	$(".provizija_po_plati_polje").hide();
																	$("#nalog_provizija_po_plati").removeAttr('required').val(0);
																	$(".avans_pitanje_polje").hide();
																	$("#nalog_ima_avans").removeAttr('required').val(0).selectpicker("refresh");
																	$(".avans_polje").hide();
																	$("#nalog_avans").removeAttr('required').val(0);
																	$(".broj_rata_polje").hide();
																	$("#nalog_broj_rata").removeAttr('required').val(null);
																});

																$('#nalog_financije').change(function(){

																	var nalog_financije_value = parseInt($("#nalog_financije").val());

																	$(".provizija_po_plati_help_desc").hide();
																	$("#provizija_po_plati_help").show();
																	$("#provizija_po_plati_close").hide();

																	$(".avans_help_desc").hide();
																	$("#avans_help").show();
																	$("#avans_close").hide();

																	if ( nalog_financije_value === 1 ) {

																		$(".broj_kandidata_polje").show();
																		$("#nalog_potrebno_kandidata").attr('required', 'required').val(null); 
																		$(".provizija_polje").show();
																		$("#nalog_provizija").attr('required', 'required').val(null); 
																		$(".provizija_po_plati_polje").hide();
																		$("#nalog_provizija_po_plati").removeAttr('required').val(0);
																		$(".avans_pitanje_polje").hide();
																		$("#nalog_ima_avans").removeAttr('required').val(0).selectpicker("refresh");
																		$(".avans_polje").hide();
																		$("#nalog_avans").removeAttr('required').val(0);
																		$(".broj_rata_polje").show();
																		$("#nalog_broj_rata").attr('required', 'required').val(null); 

																	} else if ( nalog_financije_value === 2 ) {

																		$(".broj_kandidata_polje").show();
																		$("#nalog_potrebno_kandidata").attr('required', 'required').val(null); 
																		$(".provizija_polje").show();
																		$("#nalog_provizija").attr('required', 'required').val(null); 
																		$(".provizija_po_plati_polje").hide();
																		$("#nalog_provizija_po_plati").removeAttr('required').val(0);
																		$(".avans_pitanje_polje").show();
																		$("#nalog_ima_avans").attr('required', 'required').val(0).selectpicker("refresh");
																		$(".avans_polje").hide();
																		$("#nalog_avans").removeAttr('required').val(0);
																		$(".broj_rata_polje").show();
																		$("#nalog_broj_rata").attr('required', 'required').val(null); 

																	} else if ( nalog_financije_value === 3 ) {

																		$(".broj_kandidata_polje").show();
																		$("#nalog_potrebno_kandidata").attr('required', 'required').val(null); 
																		$(".provizija_polje").hide();
																		$("#nalog_provizija").removeAttr('required').val(null);
																		$(".provizija_po_plati_polje").show();
																		$("#nalog_provizija_po_plati").attr('required', 'required').val(0);
																		$(".avans_pitanje_polje").hide();
																		$("#nalog_ima_avans").removeAttr('required').val(0).selectpicker("refresh");
																		$(".avans_polje").hide();
																		$("#nalog_avans").removeAttr('required').val(0); 
																		$(".broj_rata_polje").show();
																		$("#nalog_broj_rata").attr('required', 'required').val(null); 

																	} else {

																		$(".broj_kandidata_polje").hide();
																		$("#nalog_potrebno_kandidata").removeAttr('required').val(null);
																		$(".provizija_polje").hide();
																		$("#nalog_provizija").removeAttr('required').val(null);
																		$(".provizija_po_plati_polje").hide();
																		$("#nalog_provizija_po_plati").removeAttr('required').val(0);
																		$(".avans_pitanje_polje").hide();
																		$("#nalog_ima_avans").removeAttr('required').val(0).selectpicker("refresh");
																		$(".avans_polje").hide();
																		$("#nalog_avans").removeAttr('required').val(0);
																		$(".broj_rata_polje").hide();
																		$("#nalog_broj_rata").removeAttr('required').val(null);

																	}
																	
																}); 
															</script>
															<div class="form-group broj_kandidata_polje">
																<div class="col-md-offset-2 col-sm-8">
																	<div class="row">
																		<div class="col-sm-5" style="padding-top:5px">Broj potrebnih kandidata:</div>
																		<div class="col-sm-7">
																			<div class="materail-input-block materail-input-block_success">
																				<input type="number" class="form-control materail-input" name="nalog_potrebno_kandidata" id="nalog_potrebno_kandidata" placeholder="Broj potrebnih kandidata" min="1" value="0" step="1">
																				<span class="materail-input-block__line"></span>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
															<div class="form-group provizija_polje">
																<div class="col-md-offset-2 col-sm-8">
																	<div class="row">
																		<div class="col-sm-5" style="padding-top:5px">Provizija:</div>
																		<div class="col-sm-7">
																			<div class="materail-input-block materail-input-block_success">
																				<input type="number" class="form-control materail-input" name="nalog_provizija" id="nalog_provizija" placeholder="Provizija" min="0" value="0" step="0.01">
																				<span class="materail-input-block__line"></span>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
															<div class="form-group provizija_po_plati_help_desc">
																<div class="col-md-offset-2 col-sm-8 text-center">
																	<div class="alert alert-danger" role="alert">
																		<h3><strong>NAPOMENA</strong></h3>
																		<br><br>
																		Polje <strong>Provizija po plati</strong> označava koeficijent kojim se množi plata određenog kandidata da bi se dobila konačna provizija određenog kandidata.
																		<br><br>
																		<strong>Primjer</strong>
																		<br><br>
																		Ako se u polje <strong>Provizija po plati</strong> postavi vrijednost na <strong>2,50</strong>.<br>
																		Ako konačna plata zaposlenika iznosi 1800 €.
																		Konačna provizija za tog kandidata će iznositi <strong>2,50 x 1800 € = 4500 €</strong>
																	</div>
																</div>
															</div>
															<div class="form-group provizija_po_plati_polje">
																<div class="col-md-offset-2 col-sm-8">
																	<div class="row">
																		<div class="col-sm-5" style="padding-top:5px">Provizija po plati:</div>
																		<div class="col-sm-6">
																			<div class="materail-input-block materail-input-block_success">
																				<input type="number" class="form-control materail-input" name="nalog_provizija_po_plati" id="nalog_provizija_po_plati" placeholder="Provizija po plati" min="0" value="0" step="0.01">
																				<span class="materail-input-block__line"></span>
																			</div>
																		</div>
																		<div class="col-sm-1">
																			<i class="fa fa-question-circle-o fa-2x" id="provizija_po_plati_help" aria-hidden="true"></i>
																			<i class="fa fa-times-circle-o fa-2x"  id="provizija_po_plati_close" aria-hidden="true"></i>
																		</div>
																		<script>
																			$(document).ready(function(){
																				$("#provizija_po_plati_help").click(function(){
																					$(".provizija_po_plati_help_desc").show();
																					$("#provizija_po_plati_help").hide();
																					$("#provizija_po_plati_close").show();
																				});
																				$("#provizija_po_plati_close").click(function(){
																					$(".provizija_po_plati_help_desc").hide();
																					$("#provizija_po_plati_help").show();
																					$("#provizija_po_plati_close").hide();
																				});
																			});
																		</script>
																	</div>
																</div>
															</div>
															<div class="form-group avans_pitanje_polje">
																<div class="col-md-offset-2 col-sm-8">
																	<div class="row">
																		<div class="col-sm-5" style="padding-top:5px">Ima li avans:</div>
																		<div class="col-sm-7">
																			<select class="selectpicker" name="nalog_ima_avans" id="nalog_ima_avans" title = "Odaberite opciju" required>
																				<option value = "0">NE</option>
																				<option value = "1">DA</option>
																			</select>
																		</div>
																	</div>
																</div>
															</div>
															<script>
																$('#nalog_ima_avans').change(function(){

																	var nalog_ima_avans_value = parseInt($("#nalog_ima_avans").val());

																	if ( nalog_ima_avans_value === 1 ) {
																		$(".avans_polje").show();
																		$("#nalog_avans").attr('required', 'required').val(0);
																	} else {
																		$(".avans_polje").hide();
																		$("#nalog_avans").removeAttr('required').val(0);
																	}

																});
															</script>
															<div class="form-group avans_help_desc">
																<div class="col-md-offset-2 col-sm-8 text-center">
																	<div class="alert alert-danger" role="alert">
																		<h3><strong>NAPOMENA</strong></h3>
																		<br><br>
																		Polje <strong>Iznos avansa</strong> označava procenat avansa čija vrijednost može biti u intervalu od <strong>1</strong> do <strong>100</strong>.
																		<br><br>
																		<strong>Primjer</strong>
																		<br><br>
																		Ako se u polje <strong>Iznos avansa</strong> postavi vrijednost na <strong>30</strong>.<br>
																		Konačni avans za taj nalog će iznositi <strong>30 %</strong> ukupne cijene.
																	</div>
																</div>
															</div>
															<div class="form-group avans_polje">
																<div class="col-md-offset-2 col-sm-8">
																	<div class="row">
																		<div class="col-sm-5" style="padding-top:5px">Iznos avansa:</div>
																		<div class="col-sm-6">
																			<div class="materail-input-block materail-input-block_success">
																				<input type="number" class="form-control materail-input" name="nalog_avans" id="nalog_avans" placeholder="Procenat avansa" min="0" max="100" value="0" step="1">
																				<span class="materail-input-block__line"></span>
																			</div>
																		</div>
																		<div class="col-sm-1">
																			<i class="fa fa-question-circle-o fa-2x" id="avans_help" aria-hidden="true"></i>
																			<i class="fa fa-times-circle-o fa-2x"  id="avans_close" aria-hidden="true"></i>
																		</div>
																		<script>
																			$(document).ready(function(){
																				$("#avans_help").click(function(){
																					$(".avans_help_desc").show();
																					$("#avans_help").hide();
																					$("#avans_close").show();
																				});
																				$("#avans_close").click(function(){
																					$(".avans_help_desc").hide();
																					$("#avans_help").show();
																					$("#avans_close").hide();
																				});
																			});
																		</script>
																	</div>
																</div>
															</div>
															<div class="form-group broj_rata_polje">
																<div class="col-md-offset-2 col-sm-8">
																	<div class="row">
																		<div class="col-sm-5" style="padding-top:5px">Broj rata:</div>
																		<div class="col-sm-7">
																			<div class="materail-input-block materail-input-block_success">
																				<input type="text" class="form-control materail-input" name="nalog_broj_rata" id="nalog_broj_rata" placeholder="Broj rata">
																				<span class="materail-input-block__line"></span>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
															<hr>
															<div class="form-group">
																<div class="col-md-offset-2 col-sm-8">
																	<div class="row">
																		<div class="col-sm-1" style="padding-top:5px">
																			<i class="fa fa-info-circle" aria-hidden="true" title="Parametar koji određuje da li poslodavac traži jezik bez obzira na ishod nostrifikacije potpuna/evaluacija. U slučaju da prilikom kreiranja nije poznata informacija, moguće je istu kasnije urediti."></i>
																		</div>
																		<div class="col-sm-4" style="padding-top:5px">
																			Poslodavac traži jezik
																		</div>
																		<div class="col-sm-7">
																			<select class="selectpicker" name="nalog_poslodavac_trazi_jezik" id="nalog_poslodavac_trazi_jezik" title = "Odaberite opciju" required>
																				<option value = "0" selected>NE</option>
																				<option value = "1">DA</option>
																			</select>
																		</div>
																	</div>
																</div>
															</div>
															<div class="form-group">
																<div class="col-md-offset-2 col-sm-8">
																	<div class="row">
																		<div class="col-sm-1" style="padding-top:5px">
																			<i class="fa fa-info-circle" aria-hidden="true" title="Po ugovoru poslodavac se obavezuje na korištenje aplikacije JobSoft. Odnosno, obavezuje se na to da ako ne uradi neku akciju za nekog kandidata na istoj aplikaciji a JobStep ga pri tom obavijesti N puta, slijede ugovorom definisane akcije sa JobStep strane."></i>
																		</div>
																		<div class="col-sm-4" style="padding-top:5px">
																			Poslodavac koristi JobSoft
																		</div>
																		<div class="col-sm-7">
																			<select class="selectpicker" name="nalog_poslodavac_koristi_pp" id="nalog_poslodavac_koristi_pp" title = "Odaberite opciju" required>
																				<option value = "0">NE</option>
																				<option value = "1">DA</option>
																			</select>
																		</div>
																	</div>
																</div>
															</div>
															<hr>
															<div class="form-group">
																<div class="col-md-offset-2 col-sm-8">
																	<label class="main-container__column material-radio-group material-radio-group_success" for="nalog_projekti_da">
																		<input type="radio" name="nalog_projekti" id="nalog_projekti_da" class="material-radiobox" value="Da" checked/>
																		<span class="material-radio-group__element material-radio-group__check-radio"></span>
																		<span class="material-radio-group__element material-radio-group__caption">Želim da sistem kreira default projekte</span>
																	</label>
																</div>
																<br />
																<div class="col-md-offset-2 col-sm-8">
																	<label class="main-container__column material-radio-group material-radio-group_danger" for="nalog_projekti_ne">
																		<input type="radio" name="nalog_projekti" id="nalog_projekti_ne" class="material-radiobox" value="Ne"/>
																		<span class="material-radio-group__element material-radio-group__check-radio"></span>
																		<span class="material-radio-group__element material-radio-group__caption">Ne želim da sistem kreira default projekte</span>
																	</label>
																</div>
															</div>
														</div>
													<div class="modal-footer material-modal__footer">
															<ul class="list-inline">
																<li class="hidden"><i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i></li>
																<li><button type="submit" class="btn btn-primary material-btn material-btn_primary">Kreiraj</button></li>
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
											$('#idk_table_nalozi').DataTable({

												"order": [[ 0, "desc" ]],

												 "bAutoWidth": false,

												"aoColumns": [
														{ "width": "5%" },
														{ "width": "5%" },
														{ "width": "25%" },
														{ "width": "10%" },
														{ "width": "10%" },
														{ "width": "10%" },
														{ "width": "10%" },
														{ "width": "10%" },
														{ "width": "10%" },
														{ "width": "5%", "bSortable": false }
													]
											});
										} );
									</script>
									<table id="idk_table_nalozi" class="display" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th class="text-center">ID</th>
												<th class="text-center">Broj</th>
												<th class="text-center">Naziv</th>
												<th class="text-center">Kreirano</th>
												<th class="text-center">Status</th>
												<th class="text-center">Financije</th>
												<th class="text-center">Ugovor</th>
												<th class="text-center">Završen</th>
												<th class="text-center">Traženo</th>
												<th class="text-center"></th>
											</tr>
										</thead>
										<tbody>
											<?php
												$query_doc = $db->prepare("
																SELECT nalog_id, kompanija_id, nalog_broj, nalog_naziv, nalog_opis, nalog_kreirano, nalog_status, nalog_financije, nalog_potrebno_kandidata
																FROM idk_nalozi
																WHERE kompanija_id = :kompanija_id");

												$query_doc->execute(array(
															':kompanija_id' => $company_id));

												while($row_doc = $query_doc->fetch()){

													$nalog_id = $row_doc['nalog_id'];
													$nalog_broj = $row_doc['nalog_broj'];
													$nalog_naziv = $row_doc['nalog_naziv'];
													$nalog_opis = $row_doc['nalog_opis'];
													$nalog_status = $row_doc['nalog_status'];
													$nalog_financije = $row_doc['nalog_financije'];
													$nalog_potrebno_kandidata = $row_doc['nalog_potrebno_kandidata'];
													$nalog_kreirano = date('d.m.Y.', strtotime($row_doc['nalog_kreirano']));

													if($nalog_status == 1){
														$nalog_status_txt = '<span class="label label-primary">Potpis</span>';
													}elseif($nalog_status == 2){
														$nalog_status_txt = '<span class="label label-warning">Čeka se uplata</span>';
													}elseif($nalog_status == 3){
														$nalog_status_txt = '<span class="label label-primary">Marketing</span>';
													}elseif($nalog_status == 4){
														$nalog_status_txt = '<span class="label label-warning">Prijave u toku</span>';
													}elseif($nalog_status == 5){
														$nalog_status_txt = '<span class="label label-primary">Obrada prijava</span>';
													}elseif($nalog_status == 6){
														$nalog_status_txt = '<span class="label label-warning">Nalog kod poslodavca</span>';
													}elseif($nalog_status == 7){
														$nalog_status_txt = '<span class="label label-primary">Casting</span>';
													}elseif($nalog_status == 8){
														$nalog_status_txt = '<span class="label label-success">Završeno</span>';
													}elseif($nalog_status == 9){
														$nalog_status_txt = '<span class="label label-danger">Na čekanju</span>';
													}elseif($nalog_status == 10){
														$nalog_status_txt = '<span class="label label-success">Kandidati u odlasku</span>';
													}elseif($nalog_status == 11){
														$nalog_status_txt = '<span class="label label-info">Završeno (nenaplaćeno)</span>';
													}
													
													$get_project_ugov = $db->prepare("SELECT project_id FROM idk_projects WHERE project_nalogid = :nalog_id AND project_name LIKE '%- Ugovor%' ");
													$get_project_ugov->execute(array(":nalog_id" => $nalog_id));
													$row_getpu = $get_project_ugov->fetch();
													$project_ugov_id = $row_getpu['project_id'];
													$brojKand_ugovor = getNumberOfCandidatesProject($project_ugov_id);
													
													$get_project_zavr = $db->prepare("SELECT project_id FROM idk_projects WHERE project_nalogid = :nalog_id AND project_name LIKE '%- Završen%' ");
													$get_project_zavr->execute(array(":nalog_id" => $nalog_id));
													$row_getpz = $get_project_zavr->fetch();
													$project_zavr_id = $row_getpz['project_id'];
													$brojKand_zavrsen = getNumberOfCandidatesProject($project_zavr_id);
													
											?>
											<tr>
												<td class="text-center"><?php echo $nalog_id; ?></td>
												<td class="text-center"><?php echo $nalog_broj; ?></td>
												<td class="text-center"><?php echo $nalog_naziv; ?></td>
												<td class="text-center"><?php echo $nalog_kreirano; ?></td>
												<td class="text-center"><?php echo $nalog_status_txt; ?></td>
												<td class="text-center"><?php echo $nalog_financije; ?></td>
												<td class="text-center"><?php echo $brojKand_ugovor; ?></td>
												<td class="text-center"><?php echo $brojKand_zavrsen; ?></td>
												<td class="text-center"><?php echo $nalog_potrebno_kandidata; ?></td>
												<td class="text-center"><a href="<?php getSiteURL(); ?>nalozi?page=open&id=<?php echo $nalog_id; ?>" class="btn material-btn material-btn_success main-container__column" target="_BLANK"><i class="fa fa-info-circle" aria-hidden="true"></i></a></td>
											</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>
								<div class="tab-pane fade <?php if($tab=="company_users"){echo "active in";} ?>" id="company_users">
									<div class="row">
										<div class="col-md-12">
											<?php
												include('jobsoft_settings/company/company_users.php');
											?> 
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
					if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "7" , $getEmployeeStatus)) OR (in_array( "9" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){

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

							unlink("files/files/companies/" . $document_file);

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

						header("Location: " . getSiteURLr() . "companies?page=open&id=$document_dataid&mess=3");

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
					if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "7" , $getEmployeeStatus)) OR (in_array( "9" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){

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

						header("Location: " . getSiteURLr() . "companies?page=open&id=$note_dataid&mess=4");

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
					if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "7" , $getEmployeeStatus)) OR (in_array( "9" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){

						$company_id = $_GET['id'];

						//Get
						$query_select = $db->prepare("
												SELECT company_name
												FROM idk_companies
												WHERE company_id = :company_id");

						$query_select->execute(array(
											':company_id' => $company_id));

						$row_select = $query_select->fetch();

						$company_name = $row_select['company_name'];

						//Save
						$query = $db->prepare("
										UPDATE idk_companies
										SET company_status = :company_status
										WHERE company_id = :company_id");

						$query->execute(array(
									':company_status' => 0,
									':company_id' => $company_id));

						//Add to LOGS
						$log_desc = "Arhivirao profil kompanije: " . $company_name . "";
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


						header("Location: " . getSiteURLr() . "companies?page=list&mess=3");

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
					if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "7" , $getEmployeeStatus)) OR (in_array( "9" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){

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

						header("Location: " . getSiteURLr() . "companies?page=open&id=$timeline_dataid&mess=7");

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
					if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "7" , $getEmployeeStatus)) OR (in_array( "9" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){

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

						header("Location: " . getSiteURLr() . "companies?page=open&id=$task_dataid&mess=11");

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

				case "del_company_info":
					if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "7" , $getEmployeeStatus)) OR (in_array( "9" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){

						$comi_id = $_GET['id'];

						//Get comi_title, comi_data and comi_companyid
						$phone_open_query = $db->prepare("
													SELECT comi_title, comi_data, comi_companyid
													FROM idk_companies_info
													WHERE comi_id = :comi_id");

						$phone_open_query->execute(array(
												':comi_id' => $comi_id));

						$phone_open = $phone_open_query->fetch();

							$comi_title = $phone_open['comi_title'];
							$comi_data = $phone_open['comi_data'];
							$comi_companyid = $phone_open['comi_companyid'];

						//Add to LOGS
						$log_desc = "Obrisao kontakt informaciju: " . $comi_title . " - " . $comi_data . "";
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
													DELETE FROM idk_companies_info
													WHERE comi_id = :comi_id");

						$phone_del_query->execute(array(
											':comi_id' => $comi_id));

						header("Location: " . getSiteURLr() . "companies?page=open&id=$comi_companyid&mess=16");

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
