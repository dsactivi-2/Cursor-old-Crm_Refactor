<?php
	include("includes/functions.php");
	include("includes/common.php");

	$getEmployeeStatus = explode( ',' , getEmployeeStatus());

	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: sales?page=list&type=0&status=0");
	}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Sales | <?php getTitle(); ?></title>

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

				// PROVJERI DA LI JE POSTAVLJEN TIP, AKO JE POSTAVLJEN TIP POSTAVLJEN JE I STATUS
				if(isset($_GET["type"])) {
					// POKUPI PORIJEKLO - POSTAVLJATI CE SE PREKO FILTERA

					// POKUPI TIP 0-FC, 1-SALES
					$type = intval($_GET["type"]);
					if($_GET["type"] == 0){
						// AKO JE TIP NULA ONDA ZA ISPIS IDE FIRST CALL U HEADER SA TOM IKONOM
						$type_text = "First Call";
						$type_icon_class = "fa fa-volume-control-phone";
						// U ZAVISNOSTI STATUSA IDE I SALES_OR_FC
						$sales_or_fc = 0;
						// NA OSNOVU STATUSA IDE QUERY
						//AKO JE NULA ONDA GLEDA IZ GRUPE FC I GLEDA STATUSE LEAD I IN PROGRESS
						if($_GET["status"] == 0){
							$status_get_text = "Novi";
							$status_get = 0;
							$query_status = "client_fc_or_sales IN (0) AND client_fc_status IN (0,1)";
						//AKO JE JEDAN ONDA GLEDA IZ GRUPE SALES I GLEDA STATUS AKTIVAN
						}elseif($_GET["status"] == 1){
							$status_get_text = "Aktivan";
							$status_get = 1;
							$query_status = "client_fc_or_sales IN (1) AND client_sales_status IN (0,1,2,3)";
						//AKO JE DVA ONDA GLEDA IZ GRUPE SALES I GLEDA STATUS ARHIVIRAN
						}elseif($_GET["status"] == 2){
							$status_get_text = "Arhiva";
							$status_get = 2;
							$query_status = "client_fc_or_sales IN (1) AND client_sales_status IN (4)";
						}
					}else{
						// AKO JE TIP JEDAN ONDA ZA ISPIS IDE SALES U HEADER SA TOM IKONOM
						$type_text = "Prodaja";
						$type_icon_class = "fa fa-balance-scale";
						// U ZAVISNOSTI STATUSA IDE I SALES_OR_FC
						$sales_or_fc = 1;
						//AKO JE NULA ONDA GLEDA IZ GRUPE SALES I GLEDA STATUSE LEAD, IN PROGRESS, CEKA UGOVOR
						if($_GET["status"] == 0){
							$status_get_text = "Novi";
							$status_get = 0;
							$query_status = "client_fc_or_sales IN (1) AND client_sales_status IN (0,1,2)";
						//AKO JE JEDINICA ONDA GLEDA IZ GRUPE SALES I GLEDA STATUS AKTIVAN
						}elseif($_GET["status"] == 1){
							$status_get_text = "Aktivan";
							$status_get = 1;
							$query_status = "client_fc_or_sales IN (1) AND client_sales_status IN (3)";
						//AKO JE DVICA ONDA GLEDA IZ GRUPE SALES I GLEDA STATUS ARHIVIRAN
						}elseif($_GET["status"] == 2){
							$status_get_text = "Arhiva";
							$status_get = 2;
							$query_status = "client_fc_or_sales IN (1) AND client_sales_status IN (4)";
						}
					}
				}else{
					header("Location: sales?page=list&type=0&status=0");
				}
		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="<?php echo $type_icon_class;?> idk_color_green" aria-hidden="true"></i> <?php echo "".$type_text." - ".$status_get_text.""; ?></h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<!--<button id="export_clients" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-file-excel-o" aria-hidden="true"></i> <span>Export</span></button>-->
					<?php if($type == 0){?><a href="<?php getSiteURL(); ?>sales?page=add" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj</span></a><?php } ?>
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

									if($mess == 1){
										echo '<div class="alert material-alert material-alert_success fade in">Uspješno ste dodali novog klijenta. <button class="close material-alert__close" data-dismiss="alert">&times;</button></div><br />';
									}elseif($mess == 2){
										echo '<div class="alert material-alert material-alert_success fade in">Klijent već postoji. <button class="close material-alert__close" data-dismiss="alert">&times;</button></div><br />';
									}elseif($mess == 3){
										echo '<div class="alert material-alert material-alert_success fade in">Uspješno ste dodali kontakta. <button class="close material-alert__close" data-dismiss="alert">&times;</button></div><br />';
									}elseif($mess == 4){
										echo '<div class="alert material-alert material-alert_success fade in">Uspješno ste dodali bilješku. <button class="close material-alert__close" data-dismiss="alert">&times;</button></div><br />';
									}elseif($mess == 5){
										echo '<div class="alert material-alert material-alert_success fade in">Uspješno ste obrisali bilješku. <button class="close material-alert__close" data-dismiss="alert">&times;</button></div><br />';
									}elseif($mess == 6){
										echo '<div class="alert material-alert material-alert_success fade in">Uspješno ste dodali dokument. <button class="close material-alert__close" data-dismiss="alert">&times;</button></div><br />';
									}elseif($mess == 7){
										echo '<div class="alert material-alert material-alert_success fade in">Uspješno ste obrisali dokument. <button class="close material-alert__close" data-dismiss="alert">&times;</button></div><br />';
									}elseif($mess == 8){
										echo '<div class="alert material-alert material-alert_success fade in">Uspješno ste uredili klijenta. <button class="close material-alert__close" data-dismiss="alert">&times;</button></div><br />';
									}elseif($mess == 9){
										echo '<div class="alert material-alert material-alert_success fade in">Uspješno ste dodijelili menadžera. <button class="close material-alert__close" data-dismiss="alert">&times;</button></div><br />';
									}elseif($mess == 10){
										echo '<div class="alert material-alert material-alert_success fade in">Uspješno ste preuzeli klijenta. <button class="close material-alert__close" data-dismiss="alert">&times;</button></div><br />';
									}
								}else{
									$mess = 0;
								}
								?>
								<script type="text/javascript">
									$(document).ready(function() {
										$('#idk_table').DataTable({

											responsive: true,

											"order": [[ 0, "desc" ]],

											 "bAutoWidth": false,

											"aoColumns": [
													{ "width": "5%"},
													{ "width": "25%" },
													{ "width": "15%" },
													{ "width": "15%" },
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
											<th>Porijeklo</th>
											<th>Grad</th>
											<th>Telefon</th>
											<th>E-mail</th>
											<th>Status</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										<?php
										//Ako je u pitanju FC - Admin i Sup Projekt Menadzera vide sve - prema permisijama sa Menu.php
										//Ako je u pitanju Prodaja - Admin i Sup Projekt Menadzera i Asistenata vidi sve 
										//Zbog toga uslov prema tipu pa onda permisija
										if($_GET["type"] == 0){
											if((in_array( "2" , $employee_supervizor)) OR (in_array( "1" , $employee_status))){
												$query_manager = "client_id is not null";
											}else{
												$query_manager = "(client_manager = $logged_employee_id OR client_sales_manager = $logged_employee_id OR client_manager IS NULL)";
											}
										}else{
											if((in_array( "2" , $employee_supervizor)) OR (in_array( "3" , $employee_supervizor)) OR (in_array( "1" , $employee_status))){
												$query_manager = "client_id is not null";
											}else{
												$query_manager = "(client_manager = $logged_employee_id OR client_sales_manager = $logged_employee_id OR client_manager IS NULL)";
											}
										}
										$query = $db->prepare("
														SELECT *
														FROM idk_clients
														WHERE ".$query_manager." AND ".$query_status."");

										$query->execute();
										
											while($row = $query->fetch()){

												$client_id = $row['client_id'];
												$client_name = $row['client_name'];
												$client_origin = $row['client_origin'];

												if($client_origin == 0){
													$origin_text = '<span class="label label-success material-label material-label_info main-container__column">RUČNO</span>';
												}elseif($client_origin == 1){						
													$origin_text = '<span class="label label-success material-label material-label_warning main-container__column">DIPL</span>';
												}elseif($client_origin == 4){
													$origin_text = '<span class="label label-primary material-label material-label_primary main-container__column">ANGACOM</span>';
												}elseif($client_origin == 2){
													$origin_text = '<span class="label label-success material-label material-label_success main-container__column">DAK</span>';
												}elseif($client_origin == 5){
													$origin_text = '<span class="label label-success material-label material-label_success main-container__column">Partner</span>';
												}elseif($client_origin == 6){
													$origin_text = '<span class="label label-success material-label material-label_success main-container__column">Join</span>';
												}

												$client_city = $row['client_city'];
												$client_telephone = $row['client_telephone'];
												$client_email = $row['client_email'];
												// AKO NEMA TELEFONA
												if($client_telephone == NULL){
													$query_contact = $db->prepare("
																	SELECT contact_id, contact_firstname, contact_lastname, contact_city, contact_image, contact_type
																	FROM idk_contacts
																	WHERE contact_status != :contact_status AND contact_clientid = :contact_clientid");

													$query_contact->execute(array(
														':contact_clientid' => $client_id,
														':contact_status' => 0));

													while($row_contact = $query_contact->fetch()){
														$contact_id = $row_contact['contact_id'];

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
													}
												}
												// AKO NEMA MAILA
												if($client_email == NULL){
													$query_contact = $db->prepare("
																	SELECT contact_id, contact_firstname, contact_lastname, contact_city, contact_image, contact_type
																	FROM idk_contacts
																	WHERE contact_status != :contact_status AND contact_clientid = :contact_clientid");

													$query_contact->execute(array(
														':contact_clientid' => $client_id,
														':contact_status' => 0));

													while($row_contact = $query_contact->fetch()){
														$client_telephone = $row_phone['ci_data'];

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

														$client_email = $row_phone['ci_data'];
													}
												}
												$client_fc_or_sales = $row['client_fc_or_sales'];

												if($client_fc_or_sales == 0){
													$client_status = $row['client_fc_status'];
														if($row['client_fc_status'] == 0){
															$client_status_text = '<span class="label label-warning material-label material-label_warning main-container__column">LEAD</span>';
														}elseif($row['client_fc_status'] == 1){
															$client_status_text = '<span class="label label-info material-label material-label_info main-container__column">IN PROGRESS</span>';
														}elseif($row['client_fc_status'] == 2){
															$client_status_text = '<span class="label label-success material-label material-label_success main-container__column">Aktivan</span>';
														}else{
															$client_status_text = '<span class="label label-danger material-label material-label_danger main-container__column">Arhiva</span>';
														}
												}else{
													$client_status = $row['client_sales_status'];
													if($row['client_sales_status'] == 0){
														$client_status_text = '<span class="label label-warning material-label material-label_warning main-container__column">LEAD</span>';
													}elseif($row['client_sales_status'] == 1){
														$client_status_text = '<span class="label label-info material-label material-label_info main-container__column">IN PROGRESS</span>';
													}elseif($row['client_sales_status'] == 2){
														$client_status_text = '<span class="label label-info material-label material-label_info main-container__column">ČEKANJE UGOVORA</span>';
													}elseif($row['client_sales_status'] == 3){
														$client_status_text = '<span class="label label-success material-label material-label_success main-container__column">AKTIVAN</span>';
													}else{
														$client_status_text = '<span class="label label-danger material-label material-label_danger main-container__column">Arhiva</span>';
													}
												}
										?>
										<tr>
											<td><?php echo $client_id; ?></a></td>
											<td><a href="<?php getSiteURL(); ?>sales?page=open&id=<?php echo $client_id; ?>"><?php echo $client_name; ?></a></td>
											<td><?php echo $origin_text; ?></td>
											<td><?php echo $client_city; ?></td>
											<td><a href="tel:<?php echo $client_telephone; ?>"><?php echo $client_telephone; ?></a></td>
											<td><a href="mailto:<?php echo $client_email; ?>"><?php echo $client_email; ?></a></td>
											<td><a><?php echo $client_status_text; ?></a></td>
											<td class="text-center">
												<div class="btn-group material-btn-group">
													<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
													<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
														<li><a href="<?php getSiteURL(); ?>sales?page=open&id=<?php echo $client_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori</a></li>
														<li><a href="<?php getSiteURL(); ?>sales?page=edit&id=<?php echo $client_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Uredi</a></li>
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
					if((in_array( "10" , $getEmployeeStatus)) OR (in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "2" , $employee_supervizor)) OR (in_array( "3" , $employee_supervizor)) OR (in_array( "1" , $getEmployeeStatus))){
		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-briefcase idk_color_green" aria-hidden="true"></i> Dodaj novog klijenta</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="javascript: history.go(-1)" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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
								<form id="idk_form" action="<?php getSiteURL(); ?>sales.php?page=add_client" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<div class="form-group">
										<label for="client_name" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="client_name" id="client_name" placeholder="Naziv" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="client_country" class="col-sm-3 control-label"><span class="text-danger">*</span> Država:</label>
										<div class="col-sm-9">
											<select class="selectpicker" id="client_country" name="client_country">
												<option value="DE">Njemačka</option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label for="client_region" class="col-sm-3 control-label"><span class="text-danger">*</span> Regija:</label>
										<div class="col-sm-9">
											<select class="selectpicker" id="client_region" name="client_region">
												<option value = "Baden-Württemberg">Baden-Württemberg</option>
												<option value = "Bayern" >Bayern</option>
												<option value = "Berlin" >Berlin</option>
												<option value = "Brandenburg" >Brandenburg</option>
												<option value = "Bremen" >Bremen</option>
												<option value = "Hamburg" >Hamburg</option>
												<option value = "Hessen" >Hessen</option>
												<option value = "Mecklenburg-Vorpommern" >Mecklenburg-Vorpommern</option>
												<option value = "Niedersachsen" >Niedersachsen</option>
												<option value = "Nordrhein-Westfalen" >Nordrhein-Westfalen</option>
												<option value = "Rheinland-Pfalz" >Rheinland-Pfalz</option>
												<option value = "Saarland" >Saarland</option>
												<option value = "Sachsen" >Sachsen</option>
												<option value = "Sachsen-Anhalt" >Sachsen-Anhalt</option>
												<option value = "Schleswig-Holstein" >Schleswig-Holstein</option>
												<option value = "Thüringen">Thüringen</option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label for="client_city" class="col-sm-3 control-label"><span class="text-danger">*</span> Grad:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="client_city" id="client_city" placeholder="Grad" />
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="client_address" class="col-sm-3 control-label"><span class="text-danger">*</span> Adresa:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="client_address" id="client_address" placeholder="Adresa" />
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="client_pp" class="col-sm-3 control-label"><span class="text-danger">*</span> Poštanski broj: </label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="client_pp" id="client_pp" placeholder="Poštanski broj">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="client_telephone" class="col-sm-3 control-label"><span class="text-danger">*</span>Mobilni telefon: </label>
										<div class="col-sm-9">
											<div class="">
												<input class="form-control materail-input" type="tel" name="client_telephone" id="client_telephone" required>
											</div>
										</div>
									</div>
									<script>
									$( document ).ready(function($) {
										$.each($('input[type=tel]'),function(){
											var telInput = $(this);
											if ($(this).val().startsWith("+") || $(this).val() == '') {
											$(telInput).intlTelInput({
												utilsScript:'https://intl-tel-input.com/node_modules/intl-tel-input/build/js/utils.js',
												autoPlaceholder: "aggressive",
												initialCountry: "ba",
												formatOnDisplay: true,
												preferredCountries: ["de","rs","hr","ba"],
												separateDialCode: true
											});
											}
										});
										//SPAJANJE COUNTRY CODA I TELEFONSKOG BROJA
										$("form").submit(function(event) {
											//event.preventDefault();
											$.each($('input[type=tel]'),function(){
												var telInput = $(this);
												var telType = telInput.data('type');
												telInput.val(telInput.intlTelInput("getNumber"));
											});
										});
									});
									</script>
									<div class="form-group">
										<label for="client_email" class="col-sm-3 control-label">Primarni email:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="email" name="client_email" id="client_email" placeholder="Primarni email" />
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="client_description" class="col-sm-3 control-label">Ostalo: </label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<textarea class="form-control materail-input material-textarea" name="client_description" placeholder="Ostalo" rows="6" required></textarea>
												<span class="materail-input-block__line"></span>
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
					if((in_array( "10" , $getEmployeeStatus)) OR (in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "2" , $employee_supervizor)) OR (in_array( "3" , $employee_supervizor)) OR (in_array( "1" , $getEmployeeStatus))){

						$client_id = $_GET['id'];

						$query = $db->prepare("
										SELECT *
										FROM idk_clients
										WHERE client_id = :client_id");

						$query->execute(array(
									':client_id' => $client_id));

						$row = $query->fetch();

							$client_name = htmlspecialchars($row['client_name']);
							$client_country = $row['client_country'];
							$client_region = $row['client_region'];
							$client_city = $row['client_city'];
							$client_address = $row['client_address'];
							$client_pp = $row['client_pp'];
							$client_telephone = $row['client_telephone'];
							$client_email = $row['client_email'];
							$client_manager = $row['client_manager'];
							$client_description = $row['client_description'];

		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-briefcase idk_color_green" aria-hidden="true"></i> Uredi profile klijenta</h1>
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
								<form id="idk_form" action="<?php getSiteURL(); ?>sales.php?page=edit_client" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<input type="hidden" name="client_id" value="<?php echo $client_id; ?>" />
									<div class="form-group">
										<label for="client_name" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="client_name" id="client_name" placeholder="Naziv" value="<?php echo $client_name; ?>" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="client_country" class="col-sm-3 control-label"><span class="text-danger">*</span> Država:</label>
										<div class="col-sm-9">
											<select class="selectpicker" id="client_country" name="client_country">
												<option>Nije odabrano</option>
												<option value="DE" <?php if($client_country == "DE"){echo "selected";} ?>>Njemačka</option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label for="client_region" class="col-sm-3 control-label"><span class="text-danger">*</span> Regija:</label>
										<div class="col-sm-9">
											<select class="selectpicker" id="client_region" name="client_region">
												<option value = "Baden-Württemberg" <?php if($client_region == "Baden-Württemberg"){echo "selected";} ?>>Baden-Württemberg</option>
												<option value = "Bayern" <?php if($client_region == "Bayern"){echo "selected";} ?>>Bayern</option>
												<option value = "Berlin" <?php if($client_region == "Berlin"){echo "selected";} ?>>Berlin</option>
												<option value = "Brandenburg" <?php if($client_region == "Brandenburg"){echo "selected";} ?>>Brandenburg</option>
												<option value = "Bremen" <?php if($client_region == "Bremen"){echo "selected";} ?>>Bremen</option>
												<option value = "Hamburg" <?php if($client_region == "Hamburg"){echo "selected";} ?>>Hamburg</option>
												<option value = "Hessen" <?php if($client_region == "Hessen"){echo "selected";} ?>>Hessen</option>
												<option value = "Mecklenburg-Vorpommern" <?php if($client_region == "Mecklenburg-Vorpommern"){echo "selected";} ?>>Mecklenburg-Vorpommern</option>
												<option value = "Niedersachsen" <?php if($client_region == "Niedersachsen"){echo "selected";} ?>>Niedersachsen</option>
												<option value = "Nordrhein-Westfalen" <?php if($client_region == "Nordrhein-Westfalen"){echo "selected";} ?> >Nordrhein-Westfalen</option>
												<option value = "Rheinland-Pfalz" <?php if($client_region == "Rheinland-Pfalz"){echo "selected";} ?>>Rheinland-Pfalz</option>
												<option value = "Saarland" <?php if($client_region == "Saarland"){echo "selected";} ?>>Saarland</option>
												<option value = "Sachsen" <?php if($client_region == "Sachsen"){echo "selected";} ?>>Sachsen</option>
												<option value = "Sachsen-Anhalt" <?php if($client_region == "Sachsen-Anhalt"){echo "selected";} ?>>Sachsen-Anhalt</option>
												<option value = "Schleswig-Holstein" <?php if($client_region == "Schleswig-Holstein"){echo "selected";} ?>>Schleswig-Holstein</option>
												<option value = "Thüringen" <?php if($client_region == "Thüringen"){echo "selected";} ?>>Thüringen</option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label for="client_city" class="col-sm-3 control-label">Grad:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="client_city" id="client_city" value="<?php echo $client_city; ?>" />
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="client_address" class="col-sm-3 control-label">Adresa:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="client_address" id="client_address" value="<?php echo $client_address; ?>" />
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
                                    <div class="form-group">
										<label for="client_pp" class="col-sm-3 control-label">Poštanski broj: </label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="client_pp" id="client_pp" value="<?php echo $client_pp; ?>">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<!--
									<div class="form-group">
										<label for="client_manager" class="col-sm-3 control-label">Menadžer:</label>
										<div class="col-sm-9">
											<select class="selectpicker" id="client_manager" name="client_manager" data-live-search="true" required>
												<option value=""></option>
												<?php
													$select_query = $db->prepare("
																		SELECT employee_id, employee_firstname, employee_lastname
																		FROM idk_employees
																		WHERE getEmployeeStatus != :getEmployeeStatus");
													$select_query->execute(array(
																	':getEmployeeStatus' => 0));
													while($select_row = $select_query->fetch()) {
														if($select_row['employee_id'] == $client_manager){
															echo "<option value='" . $select_row['employee_id'] . "' selected>" . $select_row['employee_firstname'] . " " . $select_row['employee_lastname'] . "</option>";
														}else{
															echo "<option value='" . $select_row['employee_id'] . "'>" . $select_row['employee_firstname'] . " " . $select_row['employee_lastname'] . "</option>";
														}
													}
												?>
											</select>
										</div>
									</div>
									-->
									<div class="form-group">
										<label for="client_description" class="col-sm-3 control-label">Ostalo: </label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<textarea class="form-control materail-input material-textarea" name="client_description" placeholder="Ostalo" rows="6" required><?php echo $client_description; ?></textarea>
												<span class="materail-input-block__line"></span>
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

					$client_id = $_GET['id'];

					$query = $db->prepare("
									SELECT *
									FROM idk_clients
									WHERE client_id = :client_id");

					$query->execute(array(
								':client_id' => $client_id));

					$row = $query->fetch();

						$client_name = $row['client_name'];
						$client_country = $row['client_country'];
						if($client_country == "DE"){
							$client_country_text = "Njemačka";
						} else if($client_country != null) {
							$client_country_text = $client_country;
						} else{
							$client_country_text = '<span class="label label-warning material-label material-label_warning main-container__column">Nije unešena informacija</span>';
						}
						$client_region = $row['client_region'];
						$client_city = $row['client_city'];
						$client_address = $row['client_address'];
						$client_pp = $row['client_pp'];
						$client_telephone = $row['client_telephone'];
						$client_email = $row['client_email'];
						// AKO NEMA TELEFONA
						if($client_telephone == NULL){
							$query_contact = $db->prepare("
											SELECT contact_id, contact_firstname, contact_lastname, contact_city, contact_image, contact_type
											FROM idk_contacts
											WHERE contact_status != :contact_status AND contact_clientid = :contact_clientid");

							$query_contact->execute(array(
								':contact_clientid' => $client_id,
								':contact_status' => 0));

							while($row_contact = $query_contact->fetch()){
								$contact_id = $row_contact['contact_id'];

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
							}
						}
						// AKO NEMA MAILA
						if($client_email == NULL){
							$query_contact = $db->prepare("
											SELECT contact_id, contact_firstname, contact_lastname, contact_city, contact_image, contact_type
											FROM idk_contacts
											WHERE contact_status != :contact_status AND contact_clientid = :contact_clientid");

							$query_contact->execute(array(
								':contact_clientid' => $client_id,
								':contact_status' => 0));

							while($row_contact = $query_contact->fetch()){
								$client_telephone = $row_phone['ci_data'];

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

								$client_email = $row_phone['ci_data'];
							}
						}
						$client_origin = $row['client_origin'];
						$client_recommendation = $row['client_recommendation'];
						$client_fc_or_sales = $row['client_fc_or_sales'];
						$client_manager = $row['client_manager'];
						$client_sales_manager = $row['client_sales_manager'];
						$client_contract = $row['client_contract'];
						$client_description = $row['client_description'];
						$datum_zvanja = date("d.m.Y", strtotime($row['client_datum_zvanja']));
						if($client_contract == 0){
							$client_contract_text = '<span class="label label-danger material-label material-label_danger main-container__column">NE</span>';
						}elseif($client_contract == 1){
							$client_contract_text = '<span class="label label-success material-label material-label_success main-container__column">DA</span>';
						}

						if($client_fc_or_sales == 0){
							$fc_or_sales_text = "FIRST CALL";
							$client_status = $row['client_fc_status'];
								if($row['client_fc_status'] == 0){
									$client_status_text = '<span class="label label-info material-label material-label_info main-container__column">LEAD</span>';
								}elseif($row['client_fc_status'] == 1){
									$client_status_text = '<span class="label label-warning material-label material-label_warning main-container__column">IN PROGRESS</span>';
								}elseif($row['client_fc_status'] == 2){
									$client_status_text = '<span class="label label-success material-label material-label_success main-container__column">Aktivan</span>';
								}else{
									$client_status_text = '<span class="label label-danger material-label material-label_danger main-container__column">Arhiva</span>';
								}
						}else{
							$fc_or_sales_text = "SALES";
							$client_status = $row['client_sales_status'];
							if($row['client_sales_status'] == 0){
									$client_status_text = '<span class="label label-info material-label material-label_info main-container__column">LEAD</span>';
								}elseif($row['client_sales_status'] == 1){
									$client_status_text = '<span class="label label-warning material-label material-label_warning main-container__column">IN PROGRESS</span>';
								}elseif($row['client_sales_status'] == 2){
									$client_status_text = '<span class="label label-primary material-label material-label_primary main-container__column">ČEKANJE UGOVORA</span>';
								}elseif($row['client_sales_status'] == 3){
									$client_status_text = '<span class="label label-success material-label material-label_success main-container__column">Aktivan</span>';
								}else{
									$client_status_text = '<span class="label label-danger material-label material-label_danger main-container__column">Arhiva</span>';
								}
						}

						if($client_origin == 0){
							$origin_text = '<span class="label label-success material-label material-label_info main-container__column">RUČNO</span>';
						}elseif($client_origin == 1){
							$origin_text = '<span class="label label-success material-label material-label_warning main-container__column">DIPL</span>';
						}elseif($client_origin == 4){
							$origin_text = '<span class="label label-primary material-label material-label_primary main-container__column">ANGACOM</span>';
						}else if($client_origin == 2){
							$origin_text = '<span class="label label-success material-label material-label_success main-container__column">DAK</span>';
						}else if($client_origin == 5){
							$origin_text = '<span class="label label-success material-label material-label_success main-container__column">Partner</span>';
						}else if($client_origin == 6){
							$origin_text = '<span class="label label-success material-label material-label_success main-container__column">Join</span>';
						}else{
							$origin_text = '<span class="label label-success material-label material-label_success main-container__column">UNKNOWN</span>';
						}
						$buttontxt1 = "";
						$buttontxt2 = "";
						$buttontxt3 = "";
						$buttontxt4 = "";
						$buttontxt5 = "";
						if($client_fc_or_sales == 1){
							if($client_status == 0){
								$buttontxt1 = "material-btn_info";
								$buttontxt2 = "";
								$buttontxt3 = "";
								$buttontxt4 = "";
								$buttontxt5 = "";
								$tabs_class = "material-tabs_info";
							}elseif($client_status == 1){
								$buttontxt1 = "";
								$buttontxt2 = "material-btn_warning";
								$buttontxt3 = "";
								$buttontxt4 = "";
								$buttontxt5 = "";
								$tabs_class = "material-tabs_warning";
							}elseif($client_status == 2){
								$buttontxt1 = "";
								$buttontxt2 = "";
								$buttontxt3 = "material-btn_primary";
								$buttontxt4 = "";
								$buttontxt5 = "";
								$tabs_class = "material-tabs_primary";
							}elseif($client_status == 3){
								$buttontxt1 = "";
								$buttontxt2 = "";
								$buttontxt3 = "";
								$buttontxt4 = "material-btn_success";
								$buttontxt5 = "";
								$tabs_class = "material-tabs_success";
							}else{
								$buttontxt1 = "";
								$buttontxt2 = "";
								$buttontxt3 = "";
								$buttontxt4 = "";
								$buttontxt5 = "material-btn_danger";
								$tabs_class = "material-tabs_danger";
							}
						}else{
							if($client_status == 0){
								$buttontxt1 = "material-btn_info";
								$tabs_class = "material-tabs_info";
							}elseif($client_status == 1){
								$buttontxt2 = "material-btn_warning";
								$tabs_class = "material-tabs_warning";
							}elseif($client_status == 2){
								$buttontxt3 = "material-btn_success";
								$tabs_class = "material-tabs_success";
							}else{
								$buttontxt4 = "material-btn_danger";
								$tabs_class = "material-tabs_danger";
							}
						}

		?>
			<div class="row">
				<?php
					if(isset($_GET['mess'])) {
				?>
				<div class="col-xs-12 text-left idk_margin_top10">
				<?php
						$mess = $_GET['mess'];

					if($mess == 1){
						echo '<div class="alert material-alert material-alert_success fade in">Uspješno ste dodali novog klijenta. <button class="close material-alert__close" data-dismiss="alert">&times;</button></div><br />';
					}elseif($mess == 2){
						echo '<div class="alert material-alert material-alert_success fade in">Klijent već postoji. <button class="close material-alert__close" data-dismiss="alert">&times;</button></div><br />';
					}elseif($mess == 3){
						echo '<div class="alert material-alert material-alert_success fade in">Uspješno ste dodali kontakta. <button class="close material-alert__close" data-dismiss="alert">&times;</button></div><br />';
					}elseif($mess == 4){
						echo '<div class="alert material-alert material-alert_success fade in">Uspješno ste dodali bilješku. <button class="close material-alert__close" data-dismiss="alert">&times;</button></div><br />';
					}elseif($mess == 5){
						echo '<div class="alert material-alert material-alert_success fade in">Uspješno ste obrisali bilješku. <button class="close material-alert__close" data-dismiss="alert">&times;</button></div><br />';
					}elseif($mess == 6){
						echo '<div class="alert material-alert material-alert_success fade in">Uspješno ste dodali dokument. <button class="close material-alert__close" data-dismiss="alert">&times;</button></div><br />';
					}elseif($mess == 7){
						echo '<div class="alert material-alert material-alert_success fade in">Uspješno ste obrisali dokument. <button class="close material-alert__close" data-dismiss="alert">&times;</button></div><br />';
					}elseif($mess == 8){
						echo '<div class="alert material-alert material-alert_success fade in">Uspješno ste uredili klijenta. <button class="close material-alert__close" data-dismiss="alert">&times;</button></div><br />';
					}elseif($mess == 9){
						echo '<div class="alert material-alert material-alert_success fade in">Uspješno ste dodijelili menadžera. <button class="close material-alert__close" data-dismiss="alert">&times;</button></div><br />';
					}elseif($mess == 10){
						echo '<div class="alert material-alert material-alert_success fade in">Uspješno ste preuzeli klijenta. <button class="close material-alert__close" data-dismiss="alert">&times;</button></div><br />';
					}elseif($mess == 11){
						echo '<div class="alert material-alert material-alert_success fade in">Uspješno ste zakazali termin za poziv. <button class="close material-alert__close" data-dismiss="alert">&times;</button></div><br />';
					}elseif($mess == 12){
						echo '<div class="alert material-alert material-alert_success fade in">Uspješno ste izvršili termin za poziv. <button class="close material-alert__close" data-dismiss="alert">&times;</button></div><br />';
					}
				?>
				</div>
				<?php
				}else{
					$mess = 0;
				}
				?>
				<div class="col-xs-6 text-left idk_margin_top10">
					<h1 class="main-container__column"><i class="fa fa-suitcase" aria-hidden="true"></i> <?php echo "".$client_name." (".$fc_or_sales_text.")";?></h1>
				</div>
				<div class="col-xs-6 text-right idk_margin_top10">
					<a href="javascript: history.go(-1)" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
					<a href="sales?page=edit&id=<?php echo $client_id; ?>" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-pencil" aria-hidden="true"></i> <span>Uredi</span></a>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
				<div class="col-md-6">
					<div class="row idk_margin_top10">
						<strong class="col-sm-4 text-left"><i class="fa fa-user" aria-hidden="true"></i> Naziv:</strong>
						<div class="col-sm-8 text-left"><?php echo $client_name; ?></div>
					</div>
					<?php if($client_origin != 0){ ?>
					<div class="row idk_margin_top10">
						<strong class="col-sm-4 text-left"><i class="fa fa-address-book" aria-hidden="true"></i> Preporuka:</strong>
						<div class="col-sm-8 text-left">
							<?php 
								if($client_origin == 1){ 
									echo getDiplCandidateFullnameR($client_recommendation);
								}elseif($client_origin == 2){
									echo getDakCandidateFullnameR($client_recommendation);
								}elseif($client_origin == 5){
									echo (($client_recommendation != null ) ? getInfoPartnerPreporukaR($client_recommendation) : '');
								} 
							?>
						</div>
					</div>
					<?php } ?>
					<div class="row idk_margin_top10">
						<strong class="col-sm-4 text-left"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i> Porijeklo:</strong>
						<div class="col-sm-8"><?php echo $origin_text; ?></div>
					</div>
					<div class="row idk_margin_top10">
						<strong class="col-sm-4 text-left"><i class="fa fa-level-down" aria-hidden="true"></i> Status:</strong>
						<div class="col-sm-8"><?php echo $client_status_text; ?></div>
					</div>
					<div class="row idk_margin_top10">
						<strong class="col-sm-4 text-left"><i class="fa fa-pencil-square" aria-hidden="true"></i> Ugovor:</strong>
						<div class="col-sm-8 text-left"><?php echo $client_contract_text; ?></div>
					</div>
					<?php 
					if($client_manager != NULL){ ?>
						<div class="row idk_margin_top10">
							<strong class="col-sm-4 text-left"><i class="fa fa-address-book" aria-hidden="true"></i> First Call Menadžer:</strong>
							<div class="col-sm-8 text-left"><?php echo getZaposlenikimeR(intval($client_manager));?></div>
						</div>
						<?php 
					}else{
						if($client_fc_or_sales == 0){ ?>
							<div class="row idk_margin_top10">
								<strong class="col-sm-4 text-left"><i class="fa fa-address-book" aria-hidden="true"></i> First Call Menadžer:</strong>
								<div class="col-sm-8 text-left">
									<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal_preuzmi"><i class="fa fa-pencil" aria-hidden="true"></i></button>
								</div>
							</div>
							<?php
						}
					} 
					if($client_manager != NULL){ ?>
						<div class="row idk_margin_top10">
							<strong class="col-sm-4 text-left" style="margin-top:5px;"><i class="fa fa-address-book" aria-hidden="true"></i> Menadžer prodaje:</strong>
							<div class="col-sm-8 text-left"><?php if($client_sales_manager != NULL){ echo getZaposlenikimeR(intval($client_sales_manager));} ?>  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal_menadzer"><i class="fa fa-pencil" aria-hidden="true"></i></button> </div>
						</div>
						<?php 
					}
					if($client_fc_or_sales == 1){ ?>
						<div class="row idk_margin_top10">
							<strong class="col-sm-4 text-left"><i class="fa fa-calendar" aria-hidden="true"></i> Datum zvanja:</strong>
							<div class="col-sm-8 text-left"><?php echo $datum_zvanja;?></div>
						</div>
						<?php 
					} ?>
					<!-- Modal change menadyer-->
					<div class="modal material-modal material-modal_primary fade" id="modal_menadzer">
						<div class="modal-dialog">
							<div class="modal-content material-modal__content">
								<div class="modal-header material-modal__header">
									<button class="close material-modal__close" data-dismiss="modal">&times;</button>
									<h4 class="modal-title material-modal__title">Dodijeli menadžera</h4>
								</div>
								<div class="modal-body material-modal__body">
								<form id="menadzer_form" action="<?php getSiteURL(); ?>sales.php?page=change_manager" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
										<input type = "hidden" name = "client" value="<?php echo $client_id;?>"></input>
										<div class="form-group">
												<label for="new_manager" class="col-sm-3 control-label">Menadzer:</label>
												<div class="col-sm-9">
													<select class="selectpicker" id="new_manager" name="new_manager" required>
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
										<div class="form-group">
											<label for="datum_zvanja" class="col-sm-3 control-label">Datum zvanja:</label>
											<div class="col-sm-9">
												<div class="materail-input-block materail-input-block_success">
													<input class="form-control materail-input" type="text" name="datum_zvanja" id="datum_zvanja" placeholder="Datum zvanja">
													<!--<input class="form-control materail-input" data-init-set="false" data-large-default="true" data-format="d.m.Y." data-large-mode="true" data-modal="true" type="text" name="datum_zvanja" id="datum_zvanja" placeholder="Datum zvanja">-->
													<span class="materail-input-block__line"></span>
												</div>
											</div>
											<script>
												//$( function() {	$( "#datum_zvanja" ).dateDropper(); } );
												$("#datum_zvanja").flatpickr({
													dateFormat: "d.m.Y.",
													disableMobile: "true",
												});
											</script>
										</div>
									</div>
								</form>
								<div class="modal-footer material-modal__footer">
									<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
									<button type="submit" id="change_menadzer_link" form="menadzer_form"  class="btn btn-primary material-btn material-btn_primary">PROMJENI</button>
								</div>
							</div>
						</div>
					</div>
					<!-- Modal preuzmi menadyer-->
					<div class="modal material-modal material-modal_primary fade" id="modal_preuzmi">
						<div class="modal-dialog">
							<div class="modal-content material-modal__content">
								<div class="modal-header material-modal__header">
									<button class="close material-modal__close" data-dismiss="modal">&times;</button>
									<h4 class="modal-title material-modal__title">Preuzimanje klijenta</h4>
								</div>
								<div class="modal-body material-modal__body">
							<form id="preuzmi_form" action="<?php getSiteURL(); ?>sales.php?page=preuzmi" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<input type = "hidden" name = "client" value="<?php echo $client_id;?>"></input>
									Jeste li sigurni da preuzimate ovog klijenta.
								</div>
							</form>
								<div class="modal-footer material-modal__footer">
									<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
									<button type="submit" id="preuzmi_menadzer_link" form="preuzmi_form"  class="btn btn-primary material-btn material-btn_primary">PROMJENI</button>
								</div>
							</div>
						</div>
					</div>
					<?php 
					if($client_description != NULL){ ?>
						<div class="row idk_margin_top10">
							<strong class="col-sm-4 text-left"><i class="fa fa-info-circle" aria-hidden="true"></i> Ostale informacije:</strong>
							<div class="col-sm-8"><?php echo $client_description; ?></div>
						</div>
						<?php 
					}

					//ZAKAZIVANJE TERMINA ZA ZVANJE
					if($client_fc_or_sales == 0 OR $client_fc_or_sales == 1){ 
						$call_array = getCallAppointmentSales($client_id, $client_fc_or_sales);
						$call_field = '';
						if($call_array){
							//Ako postoji Sales reminder onda ce se moci samo izvršiti
							$call_employee = getZaposlenikimeR($call_array["sr_employee_id"]);
							$call_appointment = date("H:i d.m.Y", strtotime($call_array["sr_call_datetime"])) ;
							$call_field = $call_appointment.' (za '.$call_employee.' )
									<span data-toggle="modal" data-target="#modal_izvrsi_termin" class="label material-label material-label_success" title="Kliknikte da označite poziv kao završen!">
										<i class="fa fa-check" aria-hidden="true"></i>
									</span>';
							
						}else{
							if ($client_fc_or_sales == 0) {
								//Ovdje je dopušteno zakazivanje poziva samo za First Call
								$call_field = '
									<span class="label label-primary material-label material-label_primary main-container__column" data-toggle="modal" data-target="#modal_zakazi_termin">
										ZAKAŽI
									</span>'
								;
							}
						}

						if ($call_field != '') {
							?>
								<div class="row idk_margin_top10" style="margin-top:10px;">
									<strong class="col-sm-4 text-left" ><i class="fa fa-phone" aria-hidden="true"></i> Termin za poziv <?php echo (($client_fc_or_sales == 1) ? '(Sales)' : ''); ?> </strong>
								
									<div class="col-sm-8 text-left" >
										<?php echo $call_field; ?>
									</div>
								</div>
							<?php 
						}
					} ?>
					<!-- Modal Zakaži termin-->
					<div class="modal material-modal material-modal_primary fade" id="modal_zakazi_termin">
						<div class="modal-dialog">
							<div class="modal-content material-modal__content">
								<div class="modal-header material-modal__header">
									<button class="close material-modal__close" data-dismiss="modal">&times;</button>
									<h4 class="modal-title material-modal__title">Zakaži termin poziva</h4>
								</div>
								<div class="modal-body material-modal__body">
									<form id="termin_form" action="<?php getSiteURL(); ?>sales.php?page=zakazi_termin_poziva" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
										<input type = "hidden" name = "client_id" value="<?php echo $client_id;?>"></input>
										<div class="form-group">
											<label for="form_termin_poziva" class="col-sm-3 control-label">Datum i vrijeme:</label>
											<div class="col-sm-9">
												<input type="text" class="form-control" name="form_termin_poziva" id="form_termin_poziva" placeholder="Termin" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
										<div class="form-group">
											<label for="employee_termin_poziva" class="col-sm-3 control-label">Za zaposlenika:</label>
											<div class="col-sm-9">
												<select class="selectpicker" id="employee_termin_poziva" name="employee_termin_poziva" required>
													<option value="<?php echo $logged_employee_id?>"><?php getEmployeeFullname($logged_employee_id); ?></option>
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
										
										<div class="modal-footer material-modal__footer">
											<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
											<button type="submit" id="btn_termin_poziva" form="termin_form"  class="btn btn-primary material-btn material-btn_primary">ZAKAŽI</button>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
					<!-- Modal Izvrši termin-->
					<div class="modal material-modal material-modal_primary fade" id="modal_izvrsi_termin">
						<div class="modal-dialog">
							<div class="modal-content material-modal__content">
								<div class="modal-header material-modal__header">
									<button class="close material-modal__close" data-dismiss="modal">&times;</button>
									<h4 class="modal-title material-modal__title">POZIV IZVRŠEN</h4>
								</div>
								<div class="modal-body material-modal__body">
									<form id="termin_end_form" action="<?php getSiteURL(); ?>sales.php?page=izvrsi_poziv" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
										<input type = "hidden" name = "client_id" value="<?php echo $client_id;?>"></input>
										<input type = "hidden" name = "sr_type" value="<?php echo (($client_fc_or_sales == 1) ? 1 : 0); ?>"></input>
										<div class="form-group">
											<div class="col-sm-12">
												Klikom na dugme <b>IZVRŠI</b> označavate da ste izvršili poziv sa klijentom!
											</div>
										</div>
										
										
										<div class="modal-footer material-modal__footer">
											<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
											<button type="submit" id="btn_termin_end" form="termin_end_form"  class="btn btn-primary material-btn material-btn_primary">IZVRŠI</button>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
					<script>
						$("#form_termin_poziva").flatpickr({
							enableTime: true,
							timeFormat: "H:i",
							disableMobile: "true",
							time_24hr: true
						});
						$(document).ready(function() {
							document.getElementById("termin_form").addEventListener("submit", function(event) {
								var selectedDate = document.getElementById("form_termin_poziva").value;
								console.log(selectedDate);
								if (!selectedDate) {
									event.preventDefault(); // Prevent form submission if no date is selected
									alert("Odaberite datum i vrijeme!");
								}
							});
						} );
					</script>
				</div>
				<div class="col-md-6">
					<div class="col-xs-12 text-left">
						<h3 class="main-container__column"><i class="fa fa-clock" aria-hidden="true"></i> Log statusa: </h3>
					</div>
					<?php

					$query_stats = $db->prepare("
									SELECT *
									FROM idk_clients_stats
									WHERE client_id = :client_id AND fc_or_sales = :fc_or_sales");

					$query_stats->execute(array(
								':client_id' => $client_id,
								':fc_or_sales' => $client_fc_or_sales
								));
					while($row_stats = $query_stats->fetch()){

						if($client_fc_or_sales == 0){
							if($row_stats["status"] == 0){
								$stats_status = '<span class="label label-info material-label material-label_info main-container__column">LEAD</span>';
							}elseif($row_stats["status"] == 1){
								$stats_status = '<span class="label label-warning material-label material-label_warning main-container__column">IN PROGRESS</span>';
							}elseif($row_stats["status"] == 2){
								$stats_status = '<span class="label label-success material-label material-label_success main-container__column">AKTIVAN</span>';
							}elseif($row_stats["status"] == 3){
								$stats_status = '<span class="label label-danger material-label material-label_danger main-container__column">ARHIVA</span>';
							}
						}else{
							if($row_stats["status"] == 0){
								$stats_status = '<span class="label label-info material-label material-label_info main-container__column">LEAD</span>';
							}elseif($row_stats["status"] == 1){
								$stats_status = '<span class="label label-warning material-label material-label_warning main-container__column">IN PROGRESS</span>';
							}elseif($row_stats["status"] == 2){
								$stats_status = '<span class="label label-primary material-label material-label_primary main-container__column">ČEKANJE UGOVORA</span>';
							}elseif($row_stats["status"] == 3){
								$stats_status = '<span class="label label-success material-label material-label_success main-container__column">AKTIVAN</span>';
							}elseif($row_stats["status"] == 4){
								$stats_status = '<span class="label label-danger material-label material-label_danger main-container__column">ARHIVA</span>';
							}
						}
					?>

						<div class="row idk_margin_top10">
							<strong class="col-xs-3 text-left"><?php echo $stats_status; ?></strong>
							<strong class="col-xs-5"><i class="fa fa-calendar" aria-hidden="true"></i> <?php echo $row_stats["date"]; ?></strong>
							<strong class="col-xs-4 text-left"><?php echo getZaposlenikimeR($row_stats["zaposlenik_id"]); ?></strong>
						</div>
					<?php
					}
					?>
				</div>
				<div class="row" style="margin-right:10px;">
					<div class="col-xs-12 text-right">
					<?php if($client_fc_or_sales == 0){ ?>
						<div class="btn-group main-container__column" role="group" aria-label="Basic example">
							<a href="#" data="<?php getSiteURL(); ?>sales?page=change_status&id=<?php echo $client_id; ?>&status=0&tip=<?php echo $client_fc_or_sales; ?>" data-toggle="modal" data-target="#change_statusModal" class="change_status material-dropdown-menu__link"><button type="button" class="btn material-btn change_status_candidate <?php echo $buttontxt1; ?>" data-id="<?php echo $client_id ?>" data-status="0"><i class="fa fa-spinner" aria-hidden="true"></i> Lead</button></a>
							<a href="#" data="<?php getSiteURL(); ?>sales?page=change_status&id=<?php echo $client_id; ?>&status=1&tip=<?php echo $client_fc_or_sales; ?>" data-toggle="modal" data-target="#change_statusModal" class="change_status material-dropdown-menu__link"><button type="button" class="btn material-btn change_status_candidate <?php echo $buttontxt2; ?>" data-id="<?php echo $client_id ?>" data-status="1"><i class="fa fa-tasks" aria-hidden="true"></i> In progress</button></a>
							<a href="#" data="<?php getSiteURL(); ?>sales?page=change_status&id=<?php echo $client_id; ?>&status=2&tip=<?php echo $client_fc_or_sales; ?>" data-toggle="modal" <?php if($client_sales_manager == NULL){?>data-placement="top" title="Morate dodijeliti menadžera prodaje."<?php } ?> data-target="#change_statusModal" class="change_status material-dropdown-menu__link"><button <?php if($client_sales_manager == NULL){echo 'disabled';} ?> type="button" class="btn material-btn change_status_candidate <?php echo $buttontxt3; ?>" data-id="<?php echo $client_id ?>" data-status="2"><i class="fa fa-check" aria-hidden="true"></i> Aktivan</button></a>
							<a href="#" data="<?php getSiteURL(); ?>sales?page=change_status&id=<?php echo $client_id; ?>&status=3&tip=<?php echo $client_fc_or_sales; ?>" data-toggle="modal" data-target="#change_statusModal" class="change_status material-dropdown-menu__link"><button type="button" class="btn material-btn change_status_candidate <?php echo $buttontxt4; ?>" data-id="<?php echo $client_id ?>" data-status="4"><i class="fa fa-recycle" aria-hidden="true"></i> Arhiviran</button></a>
						</div>
					<?php }else{ ?>
						<div class="btn-group main-container__column" role="group" aria-label="Basic example">
							<a href="#" data="<?php getSiteURL(); ?>sales?page=change_status&id=<?php echo $client_id; ?>&status=0&tip=<?php echo $client_fc_or_sales; ?>" data-toggle="modal" data-target="#change_statusModal" class="change_status material-dropdown-menu__link"><button type="button" class="btn material-btn change_status_candidate <?php echo $buttontxt1; ?>" data-id="<?php echo $client_id ?>" data-status="0"><i class="fa fa-spinner" aria-hidden="true"></i> Lead</button></a>
							<a href="#" data="<?php getSiteURL(); ?>sales?page=change_status&id=<?php echo $client_id; ?>&status=1&tip=<?php echo $client_fc_or_sales; ?>" data-toggle="modal" data-target="#change_statusModal" class="change_status material-dropdown-menu__link"><button type="button" class="btn material-btn change_status_candidate <?php echo $buttontxt2; ?>" data-id="<?php echo $client_id ?>" data-status="1"><i class="fa fa-tasks" aria-hidden="true"></i> In progress</button></a>
							<a href="#" data="<?php getSiteURL(); ?>sales?page=change_status&id=<?php echo $client_id; ?>&status=2&tip=<?php echo $client_fc_or_sales; ?>" data-toggle="modal" data-target="#change_statusModal" class="change_status material-dropdown-menu__link"><button type="button" class="btn material-btn change_status_candidate <?php echo $buttontxt3; ?>" data-id="<?php echo $client_id ?>" data-status="2"><i class="fa fa-envelope" aria-hidden="true"></i> Čekanje ugovora</button></a>
							<a href="#" data="<?php getSiteURL(); ?>sales?page=change_status&id=<?php echo $client_id; ?>&status=3&tip=<?php echo $client_fc_or_sales; ?>" data-toggle="modal" <?php if($client_contract == 0){?>data-placement="top" title="Morate dodati ugovor."<?php } ?> data-target="#change_statusModal" class="change_status material-dropdown-menu__link"><button <?php if($client_contract == 0){echo 'disabled';} ?> type="button" class="btn material-btn change_status_candidate <?php echo $buttontxt4; ?>" data-id="<?php echo $client_id ?>" data-status="4"><i class="fa fa-check" aria-hidden="true"></i> Aktivan</button></a>
							<a href="#" data="<?php getSiteURL(); ?>sales?page=change_status&id=<?php echo $client_id; ?>&status=4&tip=<?php echo $client_fc_or_sales; ?>" data-toggle="modal" data-target="#change_statusModal" class="change_status material-dropdown-menu__link"><button type="button" class="btn material-btn change_status_candidate <?php echo $buttontxt5; ?>" data-id="<?php echo $client_id ?>" data-status="5"><i class="fa fa-recycle" aria-hidden="true"></i> Arhiviran</button></a>
						</div>
					<?php } ?>
					</div>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<script>
				$(".change_status").click(function () {
					var addressValue = $(this).attr("data");
					document.getElementById("change_status_link").href = addressValue;
				});
			</script>
			<!-- Modal change status-->
			<div class="modal material-modal material-modal_primary fade" id="change_statusModal">
				<div class="modal-dialog">
					<div class="modal-content material-modal__content">
						<div class="modal-header material-modal__header">
							<button class="close material-modal__close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title material-modal__title">Promjeni status</h4>
						</div>
						<div class="modal-body material-modal__body">
							<p>Jeste li sigurni da želite promjeniti status klijenta ?</p>
						</div>
						<div class="modal-footer material-modal__footer">
							<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
							<a id="change_status_link" href=""><button class="btn btn-primary material-btn material-btn_primary">PROMJENI</button></a>
						</div>
					</div>
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
								?>
							</div>
						</div>
						<div id="myTabs" class="panel-group material-tabs-group">
							<ul class="nav nav-tabs material-tabs <?php echo $tabs_class; ?>">
								<li class="active"><a href="#info" class="material-tabs__tab-link" data-toggle="tab">Informacije</a></li>
								<li><a href="#contacts" class="material-tabs__tab-link" data-toggle="tab">Kontakti</a></li>
								<li><a href="#notes" class="material-tabs__tab-link" data-toggle="tab">Bilješke</a></li>
								<li><a href="#documents" class="material-tabs__tab-link" data-toggle="tab">Dokumenti</a></li>
							</ul>
							<div class="tab-content materail-tabs-content">
								<div class="tab-pane fade active in" id="info">
									<div class="row idk_employee_info">
										<div class="col-md-6">
											<div class="row">
												<div class="col-sm-9">
													<h5>Lokacija</h5>
												</div>

											</div>


											<div class="row">
												<strong class="col-sm-4 text-left"><i class="fa fa-home" aria-hidden="true"></i> Država:</strong>
												<div class="col-sm-8"><?php echo $client_country_text; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-left"><i class="fa fa-globe" aria-hidden="true"></i> Regija:</strong>
												<div class="col-sm-8"><?php echo $client_region; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-left"><i class="fa fa-map-signs" aria-hidden="true"></i> Grad:</strong>
												<div class="col-sm-8"><?php echo $client_city; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-left"><i class="fa fa-map" aria-hidden="true"></i> Adresa:</strong>
												<div class="col-sm-8"><?php echo $client_address; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-left"><i class="fa fa-map-marker" aria-hidden="true"></i> Poštanski broj:</strong>
												<div class="col-sm-8"><?php echo $client_pp; ?></div>
											</div>

										</div>
										<div class="col-md-6">
											<div class="row">
												<div class="col-sm-4">
													<h5><i class="fa fa-mobile" aria-hidden="true"></i> Telefon</h5>
												</div>
												<div class="col-sm-8">
													<h4><?php echo $client_telephone;?></h4>
												</div>
												</div>
											<div class="row">
												<div class="col-sm-4">
													<h5><i class="fa fa-envelope-o" aria-hidden="true"></i> E-mail</h5>
												</div>
												<div class="col-sm-8">
													<h4><?php echo $client_email;?></h4>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="tab-pane fade" id="contacts">
									<ul class="list-inline text-right">
										<li><a href="<?php getSiteURL(); ?>sales?page=add_contact&client_id=<?php echo $client_id; ?>" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj kontakt</span></a></li>
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
																WHERE contact_status != :contact_status AND contact_clientid = :contact_clientid");

												$query->execute(array(
													':contact_clientid' => $client_id,
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
														<form action="<?php getSiteURL(); ?>sales.php?page=add_client_note" method="post" role="form" class="form-horizontal">
															<input type="hidden" name="client_id" value="<?php echo $client_id; ?>" />
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
															':note_dataid' => $client_id,
															':note_group' => 8));

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
																		':note_group' => 8,
																		':note_dataid' => $client_id));

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
															<a href="#" data="<?php getSiteURL(); ?>sales?page=del_note&id=<?php echo $note_id; ?>&client_id=<?php echo $client_id; ?>" data-toggle="modal" data-target="#deleteNoteModal" class="delete_note btn material-btn material-btn_danger main-container__column"><i class="fa fa-trash-o fa-lg" aria-hidden="true"></i></a>
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
														<form action="<?php getSiteURL(); ?>sales.php?page=add_company_doc" enctype="multipart/form-data" method="post" role="form" class="form-horizontal">
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
															<input type="hidden" name="document_dataid" value="<?php echo $client_id; ?>" />
															<div class="form-group">
																<div class="col-md-offset-2 col-sm-8">
																	<select class="selectpicker" id="ugovor" name="documetnt_type">
																		<option value="0">Ugovor</option>
																		<option value="1">Ostalo</option>
																	</select>
																</div>
															</div>
															<div class="form-group">
																<div class="col-md-offset-2 col-sm-8">
																	<div class="form-group materail-input-block materail-input-block_success">
																		<input type="text" class="form-control materail-input" name="document_name" id="document_name" placeholder="Naziv dokumenta" value ="Ugovor" required>
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
															':document_group' => 8,
															':document_dataid' => $client_id));

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
												<td class="text-center"><a href="#" data="<?php getSiteURL(); ?>sales?page=del_doc&id=<?php echo $document_id; ?>" data-toggle="modal" data-target="#deleteDocModal" class="delete_doc btn material-btn material-btn_danger main-container__column"><i class="fa fa-trash-o fa-lg" aria-hidden="true"></i></a></td>
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
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php
				break;

				case "add_contact":
					if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "10" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "2" , $employee_supervizor)) OR (in_array( "3" , $employee_supervizor)) OR (in_array( "1" , $getEmployeeStatus))){

						if(isset($_GET['client_id'])) {
							$client_id = $_GET['client_id'];
						}else{
							$client_id = 0;
						}
					?>
						<div class="row">
							<div class="col-xs-8">
								<h1><i class="fa fa-address-card-o idk_color_green" aria-hidden="true"></i> Dodaj novi kontakt</h1>
							</div>
							<div class="col-xs-4 text-right idk_margin_top10">
								<a href="javascript: history.go(-1)" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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
											<form id="idk_form" action="<?php getSiteURL(); ?>sales.php?page=add_contact_logic" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
												<input class="form-control materail-input" type="hidden" name="client_id" value="<?php echo $client_id; ?>">
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
													<label for="contact_phone" class="col-sm-3 control-label"><span class="text-danger">*</span>Mobilni telefon: </label>
													<div class="col-sm-9">
														<div class="">
															<input class="form-control materail-input" type="tel" name="contact_phone" id="contact_phone" required>
														</div>
													</div>
												</div>
												<script>
												$( document ).ready(function($) {
													$.each($('input[type=tel]'),function(){
														var telInput = $(this);
														if ($(this).val().startsWith("+") || $(this).val() == '') {
														$(telInput).intlTelInput({
															utilsScript:'https://intl-tel-input.com/node_modules/intl-tel-input/build/js/utils.js',
															autoPlaceholder: "aggressive",
															initialCountry: "ba",
															formatOnDisplay: true,
															preferredCountries: ["de","rs","hr","ba"],
															separateDialCode: true
														});
														}
													});
													//SPAJANJE COUNTRY CODA I TELEFONSKOG BROJA
													$("form").submit(function(event) {
														//event.preventDefault();
														$.each($('input[type=tel]'),function(){
															var telInput = $(this);
															var telType = telInput.data('type');
															telInput.val(telInput.intlTelInput("getNumber"));
														});
													});
												});
												</script>
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
													<label for="contact_country" class="col-sm-3 control-label"><span class="text-danger">*</span> Država:</label>
													<div class="col-sm-9">
														<select class="selectpicker" id="contact_country" name="contact_country">
															<option value="DE">Njemačka</option>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label for="contact_state" class="col-sm-3 control-label"><span class="text-danger">*</span> Regija:</label>
													<div class="col-sm-9">
														<select class="selectpicker" id="contact_state" name="contact_state">
															<option value = "Baden-Württemberg">Baden-Württemberg</option>
															<option value = "Bayern" >Bayern</option>
															<option value = "Berlin" >Berlin</option>
															<option value = "Brandenburg" >Brandenburg</option>
															<option value = "Bremen" >Bremen</option>
															<option value = "Hamburg" >Hamburg</option>
															<option value = "Hessen" >Hessen</option>
															<option value = "Mecklenburg-Vorpommern" >Mecklenburg-Vorpommern</option>
															<option value = "Niedersachsen" >Niedersachsen</option>
															<option value = "Nordrhein-Westfalen" >Nordrhein-Westfalen</option>
															<option value = "Rheinland-Pfalz" >Rheinland-Pfalz</option>
															<option value = "Saarland" >Saarland</option>
															<option value = "Sachsen" >Sachsen</option>
															<option value = "Sachsen-Anhalt" >Sachsen-Anhalt</option>
															<option value = "Schleswig-Holstein" >Schleswig-Holstein</option>
															<option value = "Thüringen">Thüringen</option>
														</select>
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


				case "add_client":

					$client_name = $_POST['client_name'];
					// PROVJERA DUPLIKATA
					$query_check = $db->prepare("
									SELECT client_id
									FROM idk_clients
									WHERE client_name = :client_name");

					$query_check->execute(array(
						':client_name' => $client_name
					));

					$row_count = $query_check->rowCount();

					if($row_count == 0){
						$client_country = $_POST['client_country'];
						$client_region = $_POST['client_region'];
						$client_city = $_POST['client_city'];
						$client_pp = $_POST['client_pp'];
						$client_address = $_POST['client_address'];
						$company_city = $_POST['company_city'];
						$client_telephone = $_POST['client_telephone'];
						$client_email = $_POST['client_email'];
						$client_description = $_POST['client_description'];
						$status = 0;
						$fc_or_sales = 0;
						$client_origin = 0;
						$client_fc_status = 0;
						$client_sales_status = 0;
						$date = date("Y-m-d H:i:s");
						$zaposlenik_name = getEmployeeFullname($logged_employee_id);
						$stats_desc = "Zaposlenik ". $zaposlenik_name." je dodao klijenta ".$client_name."";
						$stats_desc_fc = "Zaposlenik ". $zaposlenik_name." je dodao klijenta ".$client_name."";

						// ADD USER IN COMPANIES

						$query_company = $db->prepare("
										INSERT INTO idk_companies
											(company_name, company_address, company_zipcode, company_city, company_state, company_country, company_contact_type, company_datetime, company_status, company_origin)
										VALUES
											(:company_name, :company_address, :company_zipcode, :company_city, :company_state, :company_country, :company_contact_type, :company_datetime, :company_status, :company_origin)");

						$query_company->execute(array(
									':company_name' => $client_name,
									':company_address' => $client_address,
									':company_zipcode' => $client_pp,
									':company_city' => $client_city,
									':company_state' => $client_region,
									':company_country' => $client_country,
									':company_contact_type' => "Lead",
									':company_datetime' => date('Y-m-d H:i:s'),
									':company_status' => 1,
									':company_origin' => 3
									));

						$companyid = $db->lastInsertId();

						//Add user to db
						$query = $db->prepare("
										INSERT INTO idk_clients
											(client_name, client_country, client_manager, client_recommendation_company, client_region, client_city, client_address, client_pp, client_telephone, client_email, client_origin, client_fc_or_sales, client_fc_status, client_sales_status)
										VALUES
											(:client_name, :client_country, :client_manager, :client_recommendation_company, :client_region, :client_city, :client_address, :client_pp, :client_telephone, :client_email, :client_origin, :client_fc_or_sales, :client_fc_status, :client_sales_status)");

						$query->execute(array(
									':client_name' => $client_name,
									':client_country' => $client_country,
									':client_manager' => $logged_employee_id,
									':client_recommendation_company' => $companyid,
									':client_region' => $client_region,
									':client_city' => $client_city,
									':client_address' => $client_address,
									':client_pp' => $client_pp,
									':client_telephone' => $client_telephone,
									':client_email' => $client_email,
									':client_origin' => $client_origin,
									':client_fc_or_sales' => $fc_or_sales,
									':client_fc_status' => $client_fc_status,
									':client_sales_status' => $client_sales_status));

						//Get last ID
						$client_id = $db->lastInsertId();

						//Add to LOGS
						insertClientStats($client_id, $client_fc_status, 0, $stats_desc_fc);

						header("Location: sales?page=list&type=0&status=0&mess=1");
					}else{
						header("Location: sales?page=list&type=0&status=0&mess=2");
					}


				break;

				case "edit_client":

					$client_id = $_POST['client_id'];
					$client_name = $_POST['client_name'];
					$client_country = $_POST['client_country'];
					$client_region = $_POST['client_region'];
					$client_city = $_POST['client_city'];
					$client_pp = $_POST['client_pp'];
					// $client_manager = $_POST['client_manager'];
					$client_address = $_POST['client_address'];
					$client_description = $_POST['client_description'];
					$zaposlenik_name = getEmployeeFullname($logged_employee_id);
					$log_desc = "Zaposlenik ". $zaposlenik_name." je uredio klijenta ".$client_name."";

					//Dodavanje ugovora
					$update = $db->prepare("UPDATE idk_clients
											SET client_name = :client_name, client_country = :client_country, client_region = :client_region, client_city = :client_city, client_address = :client_address, client_pp = :client_pp, client_description = :client_description
											WHERE client_id = $client_id
											");
					$update->execute(array(
							':client_name' => $client_name,
							':client_country' => $client_country,
							':client_region' => $client_region,
							':client_city' => $client_city,
							':client_address' => $client_address,
							':client_pp' => $client_pp,
							':client_description' => $client_description
							));

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

					header("Location: sales?page=open&id=$client_id&mess=8");
				break;

				case "change_manager":
				$client_id = $_POST['client'];
				$menadzer = $_POST['new_manager'];
				$datum_zvanja = date("Y-m-d", strtotime($_POST["datum_zvanja"]));

				//SPREMI SAD TO U BAZU
				$query = $db->prepare("
						UPDATE idk_clients
						SET client_sales_manager = :client_sales_manager, client_datum_zvanja = :client_datum_zvanja
						WHERE client_id = :client_id");

				$query->execute(array(
					':client_sales_manager' => $menadzer,
					':client_datum_zvanja' => $datum_zvanja,
					':client_id' => $client_id));

				header("Location: sales?page=open&id=$client_id&mess=9");

				break;

				case "preuzmi":
				$client_id = $_POST['client'];

				//SPREMI SAD TO U BAZU
				$query = $db->prepare("
						UPDATE idk_clients
						SET client_manager = :client_manager
						WHERE client_id = :client_id");

				$query->execute(array(
					':client_manager' => $logged_employee_id,
					':client_id' => $client_id));

				header("Location: sales?page=open&id=$client_id&mess=10");

				break;

				case "change_status":
						// COMPLICATED

						// PREKO MODALA SE SALJU INFORMACIJE O ID KLIJENTA, O STATUSU I TIP 0-FC i 1-SALES
						$client_id = $_GET['id'];
						$status = $_GET['status'];
						$tip = $_GET['tip'];

						//INFORMACIJE O PRETHODNOM STATUSU I IMENU KLIJENTA
						$query_select = $db->prepare("
												SELECT client_name, client_sales_status, client_fc_status, client_sales_manager, company_id
												FROM idk_clients
												JOIN idk_companies ON idk_clients.client_recommendation_company = idk_companies.company_id
												WHERE client_id = :client_id");

						$query_select->execute(array(
											':client_id' => $client_id));

						$row_select = $query_select->fetch();

						// POKUPI INFORMACIJE
						$name = "".$row_select['client_name']."";
						$client_name = $row_select['client_name'];
						$client_sales_status_old = $row_select['client_sales_status'];
						$client_fc_status_old = $row_select['client_fc_status'];
						$menadzer = $row_select['client_sales_manager'];
						$company_id = $row_select['company_id'];

						// AKO JE TIP NULA ONDA SE RADI O FC I ON IMA STATUSE LEAD, IN PROGRESS, AKTIVAN I ARHIVA. KAD JE AKTIVAN PRELAZI I U SALES
						// SALES IMA STATUSE LEAD, IN PROGRESS, CEKANJE UGOVORA, AKTIVAN I ARHIVA
						// KAD DODJE NA AKTIVAN U FC TAD PRELAZI U SALES ODNOSNO CLIENT_FC_OR_SALES POSTAJE 1 ISTO I KAD DODJE U ARHIVIRAN

						// AKO JE TIP 0 TJ. FC ONDA
						if($tip == 0){
							// AKO SU STATUSI LEAD 0 ILI IN PROGRESS 1 ON JE I DALJE U FC, A SALES STATUS SE NE MIJENJA
							if($status == 0 OR $status == 1){
								$client_fc_or_sales = 0;
								$client_fc_status = $status;
								$client_sales_status = $client_sales_status_old;

							// AKO JE STATUS AKTIVAN TJ. 2 ONDA SE PRELAZI U SALES TJ. CLIENT FC OR SALES POSTAJE 1, A STATUS FC STATUS PRELAZI U TAJ STATUS, DOK SALES
							// STATUS OSTAJE STARI TJ. 0
							}elseif($status == 2){
								$client_fc_or_sales = 1;
								$client_fc_status = 2;
								$client_sales_status = 0;
								$mail_referent = getEmployeeEmailR(intval($menadzer));
								$ime_referenta = getZaposlenikimeR(intval($menadzer));
								$subject = "Novi klijent u prodaji";
								$body = 'Zdravo, '.$ime_referenta.'. <br><br>
								Zaduženje za klijenta '.$name.' u modulu prodaja.
								</b><br><br> <i>Sistem automatskog obavještavanja</i>';
								$alt = 'Zaduženje klijenta - SALES';
								sendEmail($mail_referent,$ime_referenta, $subject, $body, $alt);
							// AKO SE ARHIVIRA ONDA JE ON U SALES I STATUSI OBA SU ARHIVA
							}else{
								$client_fc_or_sales = 1;
								$client_fc_status = 3;
								$client_sales_status = 4;
							}
						}else{
							// AKO JE TIP 1 TJ SALES, ONDA FC STATUS SE VISE NE MJENJA ON JE STARI ONAJ, A CLIENT SALES STATUS SE MIJENJA I ON JE ONAJ POSLANI STATUS
							// CLIENT FC OR SALES JE TAD UVIJEK SALES TJ. 1
							$client_fc_status = $client_fc_status_old;
							$client_sales_status = $status;
							$client_fc_or_sales = 1;

						}

						//SPREMI SAD TO U BAZU
						$query = $db->prepare("
								UPDATE idk_clients
								SET client_fc_or_sales = :client_fc_or_sales, client_fc_status = :client_fc_status, client_sales_status = :client_sales_status
								WHERE client_id = :client_id");

						$query->execute(array(
							':client_fc_or_sales' => $client_fc_or_sales,
							':client_fc_status' => $client_fc_status,
							':client_sales_status' => $client_sales_status,
							':client_id' => $client_id));


						$partner_sql = "SELECT js_partner_id, jp_lang, jp_fcmtoken FROM idk_companies JOIN idk_jobstep_partners ON idk_companies.js_partner_id = idk_jobstep_partners.jp_id WHERE company_id = :company_id";
						$partner_query = $db->prepare($partner_sql);
						$partner_query->execute([
							':company_id' => $company_id 
						]);
						$result = $partner_query->fetch();
						$partner_id = $result['js_partner_id'];
						//LOGOVI IDU SADA

						$log_desc = "Promjenio status klijenta" . $name . "";

						// AKO JE TIP FC
						if($tip == 0){
							// AKO SE RADI O STATUSU LEAD ILI IN PROGRESS TREBA SPREMITI SAMO INFOMACIJE ZA FC STATISTIKU
							if($status == 0 OR $status == 1){
								insertClientStats($client_id, $client_fc_status, 0, $log_desc);
							// AKO JE STATUS AKTIVAN ONDA TREBA SPREMITI I ZA FC DA JE PRESAO U AKTIVAN TJ. 2 I ZA SALES DA JE PRESAO U LEAD TJ. 0
							}elseif($status == 2){
								insertClientStats($client_id, 0, 1, $log_desc);
								insertClientStats($client_id, 2, 0, $log_desc);
							}else{
							// AKO SE RADI O ARHIVIRANOM ONDA TREBA SPREMITI DA SE U OBA ARHIVIRALO
								insertClientStats($client_id, 4, 1, $log_desc);
								insertClientStats($client_id, 3, 0, $log_desc);
							}
						// AKO JE TIP SALES
						}else{
							// AKO SE RADI O STATUSIMA LEAD, IN PROGRESS, CEKANJE UGOVORA ILI AKTIVAN TREBA SPREMITI STATS SAMO ZA SALES
							if($status == 0 OR $status == 1 OR $status == 2 OR $status == 3){
								insertClientStats($client_id, $status, 1, $log_desc);
							}else{
							// AKO SE RADI O ARHIVIRANJU ONDA TREBA SPREMITI I ZA FC DA SE ARHIVIRALO I ZA SALES
								insertClientStats($client_id, 4, 1, $log_desc);
								insertClientStats($client_id, 3, 0, $log_desc);
							}

						}

						header("Location: " . getSiteURLr() . "sales?page=open&id=$client_id");

				break;
				case "add_client_note":

					$note_txt = $_POST['note_txt'];
					$note_datetime = date('Y-m-d H:i:s');
					$note_group = 8;
					$client_id = $_POST['client_id'];

					$query = $db->prepare("
									INSERT INTO idk_notes
										(note_txt, note_datetime, note_group, note_dataid, note_employeeid)
									VALUES
										(:note_txt, :note_datetime, :note_group, :note_dataid, :note_employeeid)");

					$query->execute(array(
								':note_txt' => $note_txt,
								':note_datetime' => $note_datetime,
								':note_group' => $note_group,
								':note_dataid' => $client_id,
								':note_employeeid' => $logged_employee_id));

					$query_company = $db->prepare("
									SELECT client_name
									FROM idk_clients
									WHERE client_id = :client_id");

					$query_company->execute(array(
								':client_id' => $client_id));

					$row = $query_company->fetch();

					$client_name = $row['client_name'];

					//Add to LOGS
					$log_desc = "Dodao novu bilješku za klijenta: " .$client_name. "";
					$log_type = "8";
					addToLogs($log_desc, $log_type); //Log za biljeske - $log_type = 8

					header("Location: sales?page=open&id=$client_id&mess=4");


				break;

				case "del_note":
					if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "2" , $employee_supervizor)) OR (in_array( "3" , $employee_supervizor)) OR (in_array( "1" , $getEmployeeStatus))){

						$note_id = $_GET['id'];
						$client_id = $_GET['client_id'];

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
						$log_type = "8";
						addToLogs($log_desc, $log_type); //Log za biljeske - $log_type = 3

						//Delete note from db
						$note_del_query = $db->prepare("
													DELETE FROM idk_notes
													WHERE note_id = :note_id");

						$note_del_query->execute(array(
											':note_id' => $note_id));

						header("Location: " . getSiteURLr() . "sales?page=open&id=$client_id&mess=5");

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

				case "add_contact_logic":

				$contact_firstname = $_POST['contact_firstname'];
				$contact_lastname = $_POST['contact_lastname'];
				$client_id = $_POST['client_id'];
				$contact_nickname = $_POST['contact_nickname'];
				if(empty($_POST['contact_dob'])){ $contact_dob = NULL; }else{ $contact_dob = date("Y-m-d", strtotime($_POST['contact_dob'])); }
				$contact_address = $_POST['contact_address'];
				$contact_zipcode = $_POST['contact_zipcode'];
				$contact_city = $_POST['contact_city'];
				$contact_state = $_POST['contact_state'];
				$contact_country = $_POST['contact_country'];
				$contact_info = $_POST['contact_info'];
				$contact_type = $_POST['contact_type'];
				$contact_datetime = date('Y-m-d H:i:s');
				$contact_status = 1;

				//Add user to db
				$query = $db->prepare("
								INSERT INTO idk_contacts
									(contact_firstname, contact_lastname, contact_nickname, contact_dob, contact_address, contact_zipcode, contact_city, contact_state, contact_country, contact_info, contact_type, contact_clientid, contact_datetime, contact_status)
								VALUES
									(:contact_firstname, :contact_lastname, :contact_nickname, :contact_dob, :contact_address, :contact_zipcode, :contact_city, :contact_state, :contact_country, :contact_info, :contact_type, :contact_clientid, :contact_datetime, :contact_status)");

				$query->execute(array(
							':contact_firstname' => $contact_firstname,
							':contact_lastname' => $contact_lastname,
							':contact_nickname' => $contact_nickname,
							':contact_dob' => $contact_dob,
							':contact_address' => $contact_address,
							':contact_zipcode' => $contact_zipcode,
							':contact_city' => $contact_city,
							':contact_state' => $contact_state,
							':contact_country' => $contact_country,
							':contact_info' => $contact_info,
							':contact_type' => $contact_type,
							':contact_clientid' => $client_id,
							':contact_datetime' => $contact_datetime,
							':contact_status' => $contact_status));

				//Get last ID
				$ci_contactid = $db->lastInsertId();

				//Add primary phone
				if (!empty($_POST['contact_phone'])) {

					$ci_group = 1;
					$ci_title = "Telefon";
					$ci_data = $_POST['contact_phone'];
					$ci_primary = 1;

					$query_phone = $db->prepare("
									INSERT INTO idk_contacts_info
										(ci_group, ci_title, ci_data, ci_primary, ci_contactid)
									VALUES
										(:ci_group, :ci_title, :ci_data, :ci_primary, :ci_contactid)");

					$query_phone->execute(array(
									':ci_group' => $ci_group,
									':ci_title' => $ci_title,
									':ci_data' => $ci_data,
									':ci_primary' => $ci_primary,
									':ci_contactid' => $ci_contactid));
				}

				//Add primary email
				if (!empty($_POST['contact_email'])) {

					$ci_group = 2;
					$ci_title = "E-mail";
					$ci_data = $_POST['contact_email'];
					$ci_primary = 1;

					$query_phone = $db->prepare("
									INSERT INTO idk_contacts_info
										(ci_group, ci_title, ci_data, ci_primary, ci_contactid)
									VALUES
										(:ci_group, :ci_title, :ci_data, :ci_primary, :ci_contactid)");

					$query_phone->execute(array(
									':ci_group' => $ci_group,
									':ci_title' => $ci_title,
									':ci_data' => $ci_data,
									':ci_primary' => $ci_primary,
									':ci_contactid' => $ci_contactid));
				}

				//Add to LOGS
				$log_desc = "Dodao novi kontakt: " . $contact_firstname . " " . $employee_lastname . " ";
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

			header("Location: sales?page=open&id=$client_id&mess=3");

		break;
		case "add_company_doc":

			$document_dataid = $_POST['document_dataid'];

			//Upload document
			$document_file = $_FILES['document_file'];

			//File properties
			$file_name = $document_file['name'];
			$file_tmp = $document_file['tmp_name'];

			//File extension
			$file_ext = explode('.', $file_name);
			$file_ext = strtolower(end($file_ext));

			$allowed = array('jpg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png');

			if(in_array($file_ext, $allowed)) {

				$file_name_new = uniqid() . '.' . $file_ext;
				$file_destination = "files/files/companies/" . $file_name_new;

				if(move_uploaded_file($file_tmp, $file_destination)){}
			}

			$document_name = $_POST['document_name'];
			$document_desc = $_POST['document_desc'];
			$document_datetime = date('Y-m-d H:i:s');
			$document_group = 8;

			$query = $db->prepare("
							INSERT INTO idk_documents
								(document_name, document_desc, document_file, document_icon, document_datetime, document_group, document_dataid, document_employeeid)
							VALUES
								(:document_name, :document_desc, :document_file, :document_icon, :document_datetime, :document_group, :document_dataid, :document_employeeid)");

			$query->execute(array(
							':document_name' => $document_name,
							':document_desc' => $document_desc,
							':document_file' => $file_name_new,
							':document_icon' => $file_ext,
							':document_datetime' => $document_datetime,
							':document_group' => $document_group,
							':document_dataid' => $document_dataid,
							':document_employeeid' => $logged_employee_id));

			//Add to LOGS
			$log_desc = "Dodao novi dokument: " . $document_name . " ";
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

			if($_POST["document_type"]==0){
				//SPREMI SAD TO U BAZU
				$query = $db->prepare("
						UPDATE idk_clients
						SET client_contract = :client_contract
						WHERE client_id = :client_id");

				$query->execute(array(
					':client_contract' => 1,
					':client_id' => $document_dataid));
			}

			header("Location: sales?page=open&id=$document_dataid&mess=6");

		break;

				case "del_doc":
					if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "2" , $employee_supervizor)) OR (in_array( "3" , $employee_supervizor)) OR (in_array( "1" , $getEmployeeStatus))){

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

						header("Location: " . getSiteURLr() . "sales?page=open&id=$document_dataid&mess=7");

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
					if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "2" , $employee_supervizor)) OR (in_array( "3" , $employee_supervizor)) OR (in_array( "1" , $getEmployeeStatus))){

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

				case "zakazi_termin_poziva":
					$client_id = $_POST['client_id'];
					$employee_id = $_POST['employee_termin_poziva'];
					$termin_za_zvanje = $_POST["form_termin_poziva"];

					$query = $db->prepare("
							INSERT INTO idk_sales_reminders
								(sr_employee_id, sr_client_id, sr_call_datetime)
							VALUES
								(:sr_employee_id,:sr_client_id,:sr_call_datetime)
					");
	
					$query->execute(array(
						':sr_employee_id' => $employee_id,
						':sr_client_id' => $client_id,
						':sr_call_datetime' => $termin_za_zvanje
					));

					$query_company = $db->prepare("
									SELECT client_name
									FROM idk_clients
									WHERE client_id = :client_id");

					$query_company->execute(array(
								':client_id' => $client_id));

					$row = $query_company->fetch();

					$client_name = $row['client_name'];

					//Add to LOGS
					$log_desc = "Zakazao termin za poziv sa klijentom: " .$client_name. " [ID = ".$client_id."], za zaposlenika ".getZaposlenikimeR($employee_id)." [ID = ".$employee_id."].";
					$log_type = "0";
					addToLogs($log_desc, $log_type);

	
					header("Location: sales?page=open&id=$client_id&mess=11");
	
				break;

				case "izvrsi_poziv":
					$client_id = $_POST['client_id'];
					$sr_type = $_POST['sr_type']; 
					
					$query = $db->prepare("
							UPDATE idk_sales_reminders
							SET sr_status = :sr_status
							WHERE sr_client_id = :sr_client_id AND sr_type = :sr_type AND sr_status = 1
					");

					$query->execute(array(
						':sr_status' => 2,
						':sr_client_id' => $client_id,
						':sr_type' => $sr_type
					));

					$query_company = $db->prepare("
									SELECT client_name
									FROM idk_clients
									WHERE client_id = :client_id");

					$query_company->execute(array(
								':client_id' => $client_id));

					$row = $query_company->fetch();

					$client_name = $row['client_name'];

					//Add to LOGS
					$log_desc = "Označio termin za poziv kao izvršen sa klijentom: " .$client_name. " [ID = ".$client_id."].";
					$log_type = "0";
					addToLogs($log_desc, $log_type);

					header("Location: sales?page=open&id=$client_id&mess=12");
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
