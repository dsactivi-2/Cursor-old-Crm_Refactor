<?php
	include("includes/functions.php");
	include("includes/common.php");

	$getEmployeeStatus = explode( ',' , getEmployeeStatus());
	if(in_array("11",$getEmployeeStatus)) $putanja_jezik = "lang/de.php";
	else $putanja_jezik = "lang/bs.php";
	include($putanja_jezik);
	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: dak?page=list?type=0");
	}
	// KUPLJENJE STATUSA ZA QUERY
	if(isset($_GET["type"])){
		$status = intval($_GET["type"]);
		if($status == 0){
			$head_text = "Novi";
		}elseif($status == 1){
			$head_text = "U obradi";
		}elseif($status == 2){
			if(in_array("11",$getEmployeeStatus)){
				$head_text = "Anfrage";
			}
			else $head_text = "Poslan";
		}elseif($status == 3){
			$head_text = "Aktivan";
		}elseif($status == 4){
			$head_text = "Završen";
		}elseif($status == 5){
			$head_text = "Storniran nakon aktivacije";
		}elseif($status == 6){
			$head_text = "Storniran";
		}elseif($status == 7){
			$head_text = "Arhiviran";
		}elseif($status == 8){
			$head_text = "Na čekanju";
		}else{
			$head_text = "Statistika";
		}

	}
	if(isset($_GET["types"])){
		$status_arr = array_map('intval', $_GET["types"]);
		$head_text = "Verzeichnes";
	}

	// KUPLJENJE DATUMA ZA FILTER
	$f_from = "";
	if(isset($_POST["datum_range"])){
		$f_from = $_POST['datum_range'];
		$datum_export = $f_from;
		if (strpos($f_from, 'to') !== false) {
			$split = explode(" to ",$f_from);
			$datum_od = $split[0];
			$datum_do = $split[1];
		}else{
			$datum_od = $f_from;
			$datum_do = $f_from;
		}

		$f_from_f = date('Y-m-d', strtotime($datum_od));
		$f_to_f = date('Y-m-d', strtotime($datum_do));

		$datum_range_query = "AND datumunosa_dak_kandidat between '$f_from_f' AND '$f_to_f'";
	}else{
		$datum_export = "NULL";
		$datum_range_query = "";
	}
	// KUPLJENJE RATI ZA FILTER
	if(isset($_POST["rata1_kan_fil"])){
		$rata1 = $_POST['rata1_kan_fil'];
		if($rata1 != 2){
			$rata1_query = "AND rata1_dak_kandidat = '$rata1'";
		}else{
			$rata1_query = "AND (rata1_dak_kandidat = 1 OR rata1_dak_kandidat = 0)";
		}
	}else{
		$rata1_query = "AND (rata1_dak_kandidat = 1 OR rata1_dak_kandidat = 0)";
	}
	if(isset($_POST["rata2_kan_fil"])){
		$rata2 = $_POST['rata2_kan_fil'];
		if($rata2 != 2){
			$rata2_query = "AND rata2_dak_kandidat = '$rata2'";
		}else{
			$rata2_query = "AND (rata2_dak_kandidat = 1 OR rata2_dak_kandidat = 0)";
		}
	}else{
		$rata2_query = "AND (rata2_dak_kandidat = 1 OR rata2_dak_kandidat = 0)";
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>DAK | <?php getTitle(); ?></title>

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
					<h1><i class="fa fa-hospital-o idk_color_green" aria-hidden="true"></i> DAK - <?php echo $head_text;?></h1>
				</div>
				<?php if(getEmployeeStatus() != 11){ ?>
				<div class="col-xs-4 text-right idk_margin_top10">
					<?php if($status == 0){ ?>
						<a href="" data-toggle="modal" data-target="#rucna_reg_DAK_kandidata" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-user-plus" aria-hidden="true"></i> <span>Registracija</span></a>
					<?php } ?>
					<button id="export_dak" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-file-excel-o" aria-hidden="true"></i> <span>Export</span></button>
					<!--<a href="<?php getSiteURL(); ?>dak?page=add" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj</span></a>-->
				</div>
				<?php } ?>
					<script>
					$(document).ready(function() {
						$('#export_dak').click(function() {
							$('#export_dak_div').load('export_excel.php?prozor=export_dak&datum_range=<?php echo $f_from;?>&type=<?php echo $status;?>');
							return false;
						});
					});
					</script>
				<div id="export_dak_div" style="display:none;"></div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<?php if(getEmployeeStatus() != 11){ ?>
			<div class = "row">
				<div class = "col-xs-12 text-right">
					<a href="" data-toggle="modal" data-target="#filter_kand_pregled" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive">
						<i class="fa fa-search" aria-hidden="true"></i>
						<span>
							Filter
						</span>
					</a>
					<div class="modal material-modal material-modal_success fade text-left" id="filter_kand_pregled">
						<div class="modal-dialog modal-lg">
							<div class="modal-content material-modal__content">
								<div class="modal-header material-modal__header">
									<button class="close material-modal__close" data-dismiss="modal">&times;</button>
									<h4 class="modal-title material-modal__title"><i style = "margin-right: 10px;" class="fa fa-search" aria-hidden="true"></i>Filter kandidata</h4>
								</div>
								<div class="modal-body material-modal__body">
									<form action="<?php getSiteURL(); ?>dak?page=list&type=<?php echo $status; ?>" enctype="multipart/form-data" method="post" role="form" class="form-horizontal" id = "filterRata">

										<div class="form-group">
											<div class="col-md-offset-2 col-sm-8 text-center">
												<label for="datum_kan_fil" class="col-sm-3 control-label">
													Datum:
												</label>
												<div class="">
													<div class="form-group materail-input-block materail-input-block_primary materail-input_slide-line">
														<input type="text" class="form-control" name="datum_range" id="f_from" placeholder="Datum" style="padding:17px;border-radius:0;" required>
														<span class="materail-input-block__line"></span>
													</div>
												</div>
											</div>
										</div>
										<script>
											$("#f_from").flatpickr({
												mode: "range",
												dateFormat: "d.m.Y",
												disableMobile: "true"
											});
										</script>
										<div class="form-group">
											<div class="col-md-offset-2 col-sm-8 text-center">
												<label for="rata1_kan_fil" class="col-sm-3 control-label">
													Rata 1:
												</label>
												<div class="col-sm-9">
													<div class="">
														<select class="selectpicker" title = "Rata 1" data-actions-box="true" name="rata1_kan_fil">
															<option value = "2">Ništa</option>
															<option value = "1">DA</option>
															<option value = "0">NE</option>
														</select>
													</div>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="col-md-offset-2 col-sm-8 text-center">
												<label for="rata2_kan_fil" class="col-sm-3 control-label">
													Rata 2:
												</label>
												<div class="col-sm-9">
													<div class="">
														<select class="selectpicker" title = "Rata 2" data-actions-box="true" name="rata2_kan_fil">
															<option value = "2">Ništa</option>
															<option value = "1">DA</option>
															<option value = "0">NE</option>
														</select>
													</div>
												</div>
											</div>
										</div>

										<div class="modal-footer material-modal__footer">
											<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
											<button type="submit" class="btn btn-primary material-btn material-btn_success" form="filterRata"><i class="fa fa-search" aria-hidden="true"></i> Završi</button>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<?php } ?>
			<!-- Ručna registracija DAK kandidata - MODAL START-->
			<div class="modal material-modal material-modal_success fade" id="rucna_reg_DAK_kandidata">
				<div class="modal-dialog modal-lg">
					<div class="modal-content material-modal__content">
						<div class="modal-header material-modal__header">
							<button class="close material-modal__close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title material-modal__title">Ručna registaracija DAK kandidata</h4>
						</div>
						<div class="modal-body material-modal__body">
							<div class = "row">
								<div class="col-md-8 col-md-offset-2">
									<form action="<?php getSiteURL(); ?>dak?page=add_rucno_dak_kandidata" method="post" http-equiv="Content-type" enctype="multipart/form-data"; charset=utf-8" class="form-horizontal" id="forma_add_rucno_dak_kandidata">
										<div class="form-group">
											<label for="dak_kandidat_rucno_ime" class="col-sm-3 control-label"><span class="text-danger">*</span>Ime: </label>
											<div class="col-sm-9">
												<div class="materail-input-block materail-input-block_success">
													<input class="form-control materail-input" type="text" name="dak_kandidat_rucno_ime" id="dak_kandidat_rucno_ime" placeholder="Ime" required>
													<span class="materail-input-block__line"></span>
												</div>
											</div>
										</div>
										<div class="form-group">
											<label for="dak_kandidat_rucno_prezime" class="col-sm-3 control-label"><span class="text-danger">*</span>Prezime: </label>
											<div class="col-sm-9">
												<div class="materail-input-block materail-input-block_success">
													<input class="form-control materail-input" type="text" name="dak_kandidat_rucno_prezime" id="dak_kandidat_rucno_prezime" placeholder="Prezime" required>
													<span class="materail-input-block__line"></span>
												</div>
											</div>
										</div>
										<div class="form-group">
											<label for="dak_kandidat_rucno_telefon" class="col-sm-3 control-label"><span class="text-danger">*</span>Mobilni telefon: </label>
											<div class="col-sm-9">
												<div class="">
													<input class="form-control materail-input" type="tel" name="dak_kandidat_rucno_telefon" id="dak_kandidat_rucno_telefon" required>
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
													preferredCountries: ["ba","rs","hr","de"],
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
											<label for="dak_kandidat_rucno_mail" class="col-sm-3 control-label"><span class="text-danger">*</span>Email:</label>
											<div class="col-sm-9">
												<div class="materail-input-block materail-input-block_success">
													<input class="form-control materail-input" type="email" name="dak_kandidat_rucno_mail" id="dak_kandidat_rucno_mail" placeholder="E-mail" required>
													<span class="materail-input-block__line"></span>
												</div>
											</div>
										</div>
										<div class="form-group">
											<label for="pocetak_rada" class="col-sm-3 control-label"><span class="text-danger">*</span> Datum početka rada:</label>
											<div class="col-sm-9">
												<div class="materail-input-block materail-input-block_success">
													<input class="form-control materail-input" type="text" name="pocetak_rada" id="pocetak_rada" class="monthPicker" placeholder="Potencijalni datum početka rada" autocomplete="off" required>
													<span class="materail-input-block__line"></span>
												</div>
											</div>
										</div>
										<script>
											$( function() {
												$( "#pocetak_rada" ).datepicker({
												changeMonth: true,
												changeYear: true,
													dateFormat: 'dd.mm.yy',
													yearRange: '2017:2026'
												});
											});
										</script>
										<!--<div class="form-group">
											<label for="dak_kandidat_rucno_poslodavac" class="col-sm-3 control-label"><span class="text-danger">*</span>Poslodavac:</label>
											<div class="col-sm-9">
												<div class="">
													<select class="selectpicker" id="dak_kandidat_rucno_poslodavac" name="dak_kandidat_rucno_poslodavac" required>
														<option  selected="true" disabled="disabled">Poslodavac</option>
														<option  value ="NULL">Nepoznato</option>
														<?php
															$company_query = $db->prepare("
																			SELECT *
																			FROM idk_companies
																			WHERE company_status = 1
																			");

															$company_query->execute();

															while($row = $company_query->fetch()){

															$company_id = $row['company_id'];
															$company_name = $row['company_name'];
														?>

														<option value="<?php echo $company_id; ?>"><?php echo $company_name; ?></option>
														<?php } ?>
													</select>
												</div>
											</div>
										</div>-->
										<div class="form-group" id="poslodavac_1">
											<label for="poslodavac_new_ND_cand" class="col-sm-3 control-label"><span class="text-danger">*</span>Poslodavac u Njemačkoj:</label>
											<div class="col-sm-3">
												<label class="main-container__column material-radio-group material-radio-group_success" for="poslodavac_new_ND_cand_DA">
													<input type="radio" name="poslodavac_new_ND_cand" id="poslodavac_new_ND_cand_DA" class="material-radiobox" value="1">
													<span class="material-radio-group__element material-radio-group__check-radio"></span>
													<span class="material-radio-group__element material-radio-group__caption">DA</span>
												</label>
											</div>
											<div class="col-sm-3">
												<label class="main-container__column material-radio-group material-radio-group_danger" for="poslodavac_new_ND_cand_NE">
													<input type="radio" name="poslodavac_new_ND_cand" id="poslodavac_new_ND_cand_NE" class="material-radiobox" value="0">
													<span class="material-radio-group__element material-radio-group__check-radio"></span>
													<span class="material-radio-group__element material-radio-group__caption">NE</span>
												</label>
											</div>
										</div>

										<div class="form-group" id="poslodavac_nas_1">
											<label for="poslodavac_nas_new_ND_cand" class="col-sm-3 control-label"><span class="text-danger">*</span>Naš klijent:</label>
											<div class="col-sm-3">
												<label class="main-container__column material-radio-group material-radio-group_success" for="poslodavac_nas_new_ND_cand_DA">
													<input type="radio" name="poslodavac_nas_new_ND_cand" id="poslodavac_nas_new_ND_cand_DA" class="material-radiobox" value="1">
													<span class="material-radio-group__element material-radio-group__check-radio"></span>
													<span class="material-radio-group__element material-radio-group__caption">DA</span>
												</label>
											</div>
											<div class="col-sm-3">
												<label class="main-container__column material-radio-group material-radio-group_danger" for="poslodavac_nas_new_ND_cand_NE">
													<input type="radio" name="poslodavac_nas_new_ND_cand" id="poslodavac_nas_new_ND_cand_NE" class="material-radiobox" value="0">
													<span class="material-radio-group__element material-radio-group__check-radio"></span>
													<span class="material-radio-group__element material-radio-group__caption">NE</span>
												</label>
											</div>
										</div>

										<?php
											$pomocna_varijabla1 = "srednje";
											$nasi_aktivni_klijenti_ispis = $db->prepare("
																				SELECT company_id, company_name, company_contact_type
																				FROM idk_companies
																				WHERE company_contact_type = 'Klijent' OR company_contact_type = 'Lead' OR company_contact_type = 'Bivši klijent' OR company_contact_type = 'Potencijalni klijent'
																			");
											$nasi_aktivni_klijenti_ispis->execute();
										?>
										<div id="poslodavac_2" title = "Odaberite našeg poslodavca" style = "display: block; border: 1px solid #d0d0d0; border-radius: 1.25rem; margin: 5px 0px;">
											<div class = "col-sm-12 text-center" style = "background-color: #d0d0d0; padding: 5px; border-radius: 1.25rem 1.25rem 0rem 0rem; font-size: 18px; font-weight: 500; margin-bottom: 10px;">
												Unos našeg poslodavca
											</div>
											<div class="form-group" style = "margin-left: 0px; margin-right: 0px;">
												<label for="poslodavac_naziv_nas_new_ND_cand" class="col-sm-5 control-label">
													<span class="text-danger">*</span>
													Naziv našeg klijenta:
												</label>
												<div class="col-sm-7">
													<div class="">
														<select class="selectpicker" id="poslodavac_naziv_nas_new_ND_cand" name="poslodavac_naziv_nas_new_ND_cand" title = "Odaberite našeg klijenta" data-live-search="true">

															<?php
																while($row_nasi_aktivni_klijenti_ispis = $nasi_aktivni_klijenti_ispis->fetch()){
																	$id_naseg_klijenta_ND = $row_nasi_aktivni_klijenti_ispis['company_id'];
																	$naziv_naseg_klijenta_ND = $row_nasi_aktivni_klijenti_ispis['company_name'];
																	$vrsta_naseg_klijenta_ND = $row_nasi_aktivni_klijenti_ispis['company_contact_type'];

																	echo '<option value = "'.$id_naseg_klijenta_ND.'" data-subtext = "'.$vrsta_naseg_klijenta_ND.'">'.$naziv_naseg_klijenta_ND.'</option>';
																}
															?>
														</select>
													</div>
												</div>
											</div>
										</div>
										<div id="poslodavac_3" title = "Ispunite sve stavke iz polja informacije o poslodavcu" style = "display: block; border: 1px solid #d0d0d0; border-radius: 1.25rem; margin: 5px 0px;">
											<div class = "col-sm-12 text-center" style = "background-color: #d0d0d0; padding: 5px; border-radius: 1.25rem 1.25rem 0rem 0rem; font-size: 18px; font-weight: 500; margin-bottom: 10px;">
												Unos informacija o poslodavcu
											</div>
											<div class="form-group" style = "margin-left: 0px; margin-right: 0px;" >
												<label for="poslodavac_naziv_new_ND_cand" class="col-sm-5 control-label">
													<span class="text-danger">*</span>
													Naziv poslodavca:
												</label>
												<div class="col-sm-7">
													<div class="materail-input-block materail-input-block_success">
														<input class="form-control materail-input" type="text" name="poslodavac_naziv_new_ND_cand" id="poslodavac_naziv_new_ND_cand" autocomplete="off" placeholder="Unesite naziv poslodavca">
														<span class="materail-input-block__line">
														</span>
													</div>
												</div>
											</div>
											<div class="form-group" style = "margin-left: 0px; margin-right: 0px;">
												<label for="poslodavac_ulica_new_ND_cand" class="col-sm-5 control-label">
													<span class="text-danger">*</span>
													Ulica poslodavca:
												</label>
												<div class="col-sm-7">
													<div class="materail-input-block materail-input-block_success">
														<input class="form-control materail-input" type="text" name="poslodavac_ulica_new_ND_cand" id="poslodavac_ulica_new_ND_cand" autocomplete="off" placeholder="Unesite ulicu">
														<span class="materail-input-block__line">
														</span>
													</div>
												</div>
											</div>
											<div class="form-group" style = "margin-left: 0px; margin-right: 0px;">
												<label for="poslodavac_postanski_broj_new_ND_cand" class="col-sm-5 control-label">
													<span class="text-danger">*</span>
													Poštanski broj poslodavca:
												</label>
												<div class="col-sm-7">
													<div class="materail-input-block materail-input-block_success">
														<input class="form-control materail-input" type="number" name="poslodavac_postanski_broj_new_ND_cand" id="poslodavac_postanski_broj_new_ND_cand" placeholder="Unesite poštanski broj">
														<span class="materail-input-block__line">
														</span>
													</div>
												</div>
											</div>
											<div class="form-group" style = "margin-left: 0px; margin-right: 0px;">
												<label for="poslodavac_grad_new_ND_cand" class="col-sm-5 control-label">
													<span class="text-danger">*</span>
													Grad poslodavca:
												</label>
												<div class="col-sm-7">
													<div class="materail-input-block materail-input-block_success">
														<input class="form-control materail-input" type="text" name="poslodavac_grad_new_ND_cand" id="poslodavac_grad_new_ND_cand" autocomplete="off" placeholder="Unesite grad">
														<span class="materail-input-block__line">
														</span>
													</div>
												</div>
											</div>
											<div class="form-group" style = "margin-left: 0px; margin-right: 0px;">
												<label for="poslodavac_regija_new_ND_cand" class="col-sm-5 control-label"><span class="text-danger">*</span>Regija poslodavca:</label>
												<div class="col-sm-7">
													<div class="">
														<select class="selectpicker" id="poslodavac_regija_new_ND_cand" name="poslodavac_regija_new_ND_cand" data-live-search = "true" title = "Odaberite regiju">
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
											</div>
											<div class="form-group" style = "margin-left: 0px; margin-right: 0px;">
												<label for="poslodavac_drzava_new_ND_cand" class="col-sm-5 control-label">
													<span class="text-danger">*</span>
													Država poslodavca:
												</label>
												<div class="col-sm-7">
													<div class="materail-input-block materail-input-block_success">
														<input class="form-control materail-input" type="text" name="poslodavac_drzava_new_ND_cand" id="poslodavac_drzava_new_ND_cand" autocomplete="off" placeholder="Unesite državu">
														<span class="materail-input-block__line">
														</span>
													</div>
												</div>
											</div>
											<div class="form-group" style = "margin-left: 0px; margin-right: 0px;">
												<label for="poslodavac_ime_new_ND_cand" class="col-sm-5 control-label">
													<span class="text-danger">*</span>
													Ime kontakta:
												</label>
												<div class="col-sm-7">
													<div class="materail-input-block materail-input-block_success">
														<input class="form-control materail-input" type="text" name="poslodavac_ime_new_ND_cand" id="poslodavac_ime_new_ND_cand" autocomplete="off" placeholder="Unesite ime kontakta">
														<span class="materail-input-block__line">
													</div>
												</div>
											</div>
											<div class="form-group" style = "margin-left: 0px; margin-right: 0px;">
												<label for="poslodavac_prezime_new_ND_cand" class="col-sm-5 control-label">
													<span class="text-danger">*</span>
													Prezime kontakta:
												</label>
												<div class="col-sm-7">
													<div class="materail-input-block materail-input-block_success">
														<input class="form-control materail-input" type="text" name="poslodavac_prezime_new_ND_cand" id="poslodavac_prezime_new_ND_cand" autocomplete="off" placeholder="Unesite prezime kontakta">
														<span class="materail-input-block__line">
													</div>
												</div>
											</div>
											<div class="form-group" style = "margin-left: 0px; margin-right: 0px;">
												<label for="poslodavac_mail_new_ND_cand" class="col-sm-5 control-label">
													<span class="text-danger">*</span>
													E-mail kontakta:
												</label>
												<div class="col-sm-7">
													<div class="materail-input-block materail-input-block_success">
														<input class="form-control materail-input" type="text" name="poslodavac_mail_new_ND_cand" id="poslodavac_mail_new_ND_cand" autocomplete="off" placeholder="Unesite mail kontakta">
														<span class="materail-input-block__line">
													</div>
												</div>
											</div>
											<div class="form-group" style = "margin-left: 0px; margin-right: 0px;">
												<label for="poslodavac_kontakt_new_ND_cand" class="col-sm-5 control-label">
													<span class="text-danger">*</span>
													Telefon kontakta:
												</label>
												<div class="col-sm-7">
													<div class="">
														<input class="form-control materail-input" type="tel" name="poslodavac_kontakt_new_ND_cand" id="poslodavac_kontakt_new_ND_cand" placeholder="Unesite kontakt broj">
													</div>
												</div>
											</div>
											<script>
												$( document ).ready(function($) {
													$.each($('#poslodavac_kontakt_new_ND_cand'),function(){
														var telInput = $(this);
														if ($(this).val().startsWith("+") || $(this).val() == '') {
														$(telInput).intlTelInput({
															utilsScript:'https://intl-tel-input.com/node_modules/intl-tel-input/build/js/utils.js',
															autoPlaceholder: "aggressive",
															initialCountry: "de",
															formatOnDisplay: true,
															preferredCountries: ["ba","rs","hr","de"],
															separateDialCode: true
														});
														}
													});
													//SPAJANJE COUNTRY CODA I TELEFONSKOG BROJA
													$("form").submit(function(event) {
														//event.preventDefault();
														$.each($('#poslodavac_kontakt_new_ND_cand'),function(){
															var telInput = $(this);
															var telType = telInput.data('type');
															telInput.val(telInput.intlTelInput("getNumber"));
														});
													});
												});
											</script>
										</div>
										<script>
											$(document).ready(function() {
												document.getElementById("poslodavac_new_ND_cand_NE").required = true;
												$('#poslodavac_nas_1').hide();
												$('#poslodavac_2').hide();
												$('#poslodavac_3').hide();
											});

											$('#poslodavac_new_ND_cand_DA').click(function() {
												if($('#poslodavac_new_ND_cand_DA').is(':checked')) {
													$('#poslodavac_nas_1').show();
													document.getElementById("poslodavac_nas_new_ND_cand_NE").required = true;
												}
											});
											$('#poslodavac_new_ND_cand_NE').click(function() {
												if($('#poslodavac_new_ND_cand_NE').is(':checked')) {
													$('#poslodavac_nas_1').hide();
													$('#poslodavac_2').hide();
													$('#poslodavac_3').hide();
													$("#poslodavac_nas_new_ND_cand_DA").prop( "checked", false );
													$("#poslodavac_nas_new_ND_cand_NE").prop( "checked", false );
													document.getElementById("poslodavac_nas_new_ND_cand_NE").required = false;
													document.getElementById("poslodavac_naziv_nas_new_ND_cand").required = false;
													document.getElementById("poslodavac_naziv_new_ND_cand").required = false;
													document.getElementById("poslodavac_ulica_new_ND_cand").required = false;
													document.getElementById("poslodavac_postanski_broj_new_ND_cand").required = false;
													document.getElementById("poslodavac_grad_new_ND_cand").required = false;
													document.getElementById("poslodavac_regija_new_ND_cand").required = false;
													document.getElementById("poslodavac_drzava_new_ND_cand").required = false;
													document.getElementById("poslodavac_ime_new_ND_cand").required = false;
													document.getElementById("poslodavac_prezime_new_ND_cand").required = false;
													document.getElementById("poslodavac_mail_new_ND_cand").required = false;
													document.getElementById("poslodavac_kontakt_new_ND_cand").required = false;
												}
											});
											$('#poslodavac_nas_new_ND_cand_DA').click(function() {
												if($('#poslodavac_nas_new_ND_cand_DA').is(':checked')) {
													$('#poslodavac_2').show();
													$('#poslodavac_3').hide();
													document.getElementById("poslodavac_naziv_nas_new_ND_cand").required = true;
													document.getElementById("poslodavac_naziv_new_ND_cand").required = false;
													document.getElementById("poslodavac_ulica_new_ND_cand").required = false;
													document.getElementById("poslodavac_postanski_broj_new_ND_cand").required = false;
													document.getElementById("poslodavac_grad_new_ND_cand").required = false;
													document.getElementById("poslodavac_regija_new_ND_cand").required = false;
													document.getElementById("poslodavac_drzava_new_ND_cand").required = false;
													document.getElementById("poslodavac_ime_new_ND_cand").required = false;
													document.getElementById("poslodavac_prezime_new_ND_cand").required = false;
													document.getElementById("poslodavac_mail_new_ND_cand").required = false;
													document.getElementById("poslodavac_kontakt_new_ND_cand").required = false;
												}
											});
											$('#poslodavac_nas_new_ND_cand_NE').click(function() {
												if($('#poslodavac_nas_new_ND_cand_NE').is(':checked')) {
													$('#poslodavac_3').show();
													$('#poslodavac_2').hide();
													document.getElementById("poslodavac_naziv_nas_new_ND_cand").required = false;
													document.getElementById("poslodavac_naziv_new_ND_cand").required = true;
													document.getElementById("poslodavac_ulica_new_ND_cand").required = true;
													document.getElementById("poslodavac_postanski_broj_new_ND_cand").required = true;
													document.getElementById("poslodavac_grad_new_ND_cand").required = true;
													document.getElementById("poslodavac_regija_new_ND_cand").required = true;
													document.getElementById("poslodavac_drzava_new_ND_cand").required = true;
													document.getElementById("poslodavac_ime_new_ND_cand").required = true;
													document.getElementById("poslodavac_prezime_new_ND_cand").required = true;
													document.getElementById("poslodavac_mail_new_ND_cand").required = true;
													document.getElementById("poslodavac_kontakt_new_ND_cand").required = true;
												}
											});
										</script>
										<div class="form-group">
											<label for="dak_kandidat_rucno_poslovnica" class="col-sm-3 control-label"><span class="text-danger">*</span>Poslovnica:</label>
											<div class="col-sm-9">
												<div class="">
													<select class="selectpicker" id="dak_kandidat_rucno_poslovnica" name="dak_kandidat_rucno_poslovnica" required>
														<option  selected="true" disabled="disabled">Odaberi</option>
														<option value="1">Poslovnica Bihać</option>
														<option value="2">Poslovnica Sarajevo</option>
														<option value="3">SVZ Beograd</option>
														<option value="4">SVZ Sarajevo</option>
													</select>
												</div>
											</div>
										</div>
										<div class="form-group">
											<label for="new_manager" class="col-sm-3 control-label"><span class="text-danger">*</span>Menadžer:</label>
											<div class="col-sm-9">
												<div class="">
													<select class="selectpicker" id="new_manager" name="new_manager" data-live-search="true" required>
														<option value=""></option>
														<?php
															$select_query = $db->prepare("
																				SELECT employee_id, employee_firstname, employee_lastname
																				FROM idk_employees
																				WHERE employee_status != :employee_status");
															$select_query->execute(array(
																			':employee_status' => 0));
																			print_r($select_query->errorInfo());
															while($select_row = $select_query->fetch()) {
																echo "<option value='" . $select_row['employee_id'] . "'>" . $select_row['employee_firstname'] . " " . $select_row['employee_lastname'] . "</option>";

															}
														?>
													</select>
												</div>
											</div>
										</div>

										<div class="form-group">
											<label for="dak_kandidat_rucno_komentar" class="col-sm-3 control-label">Komentar:</label>
											<div class="col-sm-9">
												<div class="materail-input-block materail-input-block_primary materail-input_slide-line">
													<textarea class="form-control materail-input material-textarea" name="dak_kandidat_rucno_komentar" id="dak_kandidat_rucno_komentar" placeholder="Dodajte komentar..." rows="4"></textarea>
													<span class="materail-input-block__line"></span>
												</div>
											</div>
										</div>
										<div class="modal-footer material-modal__footer">
											<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
											<button type="submit" class="btn btn-primary material-btn material-btn_success" form="forma_add_rucno_dak_kandidata"><i class="fa fa-check-square-o" aria-hidden="true"></i> Završi</button>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- Ručna registracija DAK kandidata - MODAL END-->
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
										echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novog DAK kandidata.</div>';
									}elseif($mess == 2){
										echo '<div class="alert material-alert material-alert_success">DAK kandidat kojeg pokušavate dodati već se nalazi u bazi.</div>';
									}elseif($mess == 3){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste stornirali DAK kandidata.</div>';
									}
								?>
								<script type="text/javascript">
									$(document).ready(function() {
										$('#idk_table').DataTable({

											responsive: true,

											 "bAutoWidth": false,

											"aoColumns": [
													{ "width": "5%" },
													{ "width": "25%" },
													{ "width": "15%" },
													{ "width": "15%" },
													{ "width": "10%" },
													{ "width": "5%" },
													{ "width": "5%" },
													{ "width": "10%" },
													{ "width": "10%", "bSortable": false }
												]
										});
									} );
								</script>
								<?php if(getEmployeeStatus() != 11){ ?>
								<table id="idk_table" class="display" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th class="text-center">#ID</th>
											<th>Naziv</th>
											<th>Broj</th>
											<th>Email</th>
											<th class="text-center">Status</th>
											<th class="text-center">Rata 1</th>
											<th class="text-center">Rata 2</th>
											<th class="text-center">Manager</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										<?php
											$query = $db->prepare("
															SELECT *
															FROM idk_dak_kandidati
															WHERE status_dak_kandidat = :status_dak_kandidat ".$datum_range_query." ".$rata1_query." ".$rata2_query." ORDER BY id_dak_kandidat DESC");

											$query->execute(array(
															':status_dak_kandidat' => $status));
											$kasnjenje = 0;
											while($row = $query->fetch()){

												$id_dak_kandidat = $row['id_dak_kandidat'];
												if($row['manager_dak_kandidat'] != NULL){
													$manager_dak_kandidat = getZaposlenikimeR($row['manager_dak_kandidat']);
												}
												else{
													$manager_dak_kandidat = '<span class="label label-success material-label material-label_success main-container__column">Nema</span>';
												}
												$tel_dak_kandidat = $row['tel_dak_kandidat'];
												$telconfirm_dak_kandidat = $row['telconfirm_dak_kandidat'];
												$name_dak_kandidat = $row['name_dak_kandidat'];
												$lastname_dak_kandidat = $row['lastname_dak_kandidat'];
												$email_dak_kandidat = $row['email_dak_kandidat'];
												$daypart_dak_kandidat = $row['daypart_dak_kandidat'];
												$comment_dak_kandidat = $row['comment_dak_kandidat'];
												$passport_dak_kandidat = $row['passport_dak_kandidat'];
												$contract_dak_kandidat = $row['contract_dak_kandidat'];
												$status_dak_kandidat = $row['status_dak_kandidat'];
												$pocetak_rada = $row['pocetakrada_dak_kandidat'];
												$rata1 = $row['rata1_dak_kandidat'];
												$rata2 = $row['rata2_dak_kandidat'];
												if($pocetak_rada != NULL){
													$datum_za_obavijest = date('Y-m-d', strtotime("+15 days", strtotime($pocetak_rada)));
													$today = date('Y-m-d');
													if($datum_za_obavijest <= $today AND $status_dak_kandidat < 2){
														$kasnjenje = 1;
													}else{
														$kasnjenje = 0;
													}
													$predikcija_isplate_rate2 = date('Y-m-d', strtotime("+4 months", strtotime($pocetak_rada)));
												}else{
													$predikcija_isplate_rate2 = "";
													$kasnjenje = 0;
												}
												if($telconfirm_dak_kandidat == 1){
													$tel_dak_kandidat = '<span class="label label-success material-label material-label_success main-container__column">'.$tel_dak_kandidat.'</span>';
												}else{
													$tel_dak_kandidat = '<span class="label label-warning material-label material-label_warning main-container__column">'.$tel_dak_kandidat.'</span>';
												}

												if($status_dak_kandidat == 0){
													$status_show = '<span class="label label-warning material-label material-label_warning main-container__column">Novi</span>';
												}else if($status_dak_kandidat == 1){
													$status_show = '<span class="label label-info material-label material-label_info main-container__column">U obradi</span>';
												}else if($status_dak_kandidat == 2){
													$status_show = '<span class="label label-primary material-label material-label_primary main-container__column">Poslan</span>';
												}else if($status_dak_kandidat == 3){
													$status_show = '<span class="label label-success material-label material-label_success main-container__column">Aktivan</span>';
												}else if($status_dak_kandidat == 4){
													$status_show = '<span class="label label-success material-label material-label_success main-container__column">Završen</span>';
												}else if($status_dak_kandidat == 5){
													$status_show = '<span class="label label-secondary material-label material-label_secondary main-container__column">Storniran nakon aktivacije</span>';
												}else if($status_dak_kandidat == 6){
													$status_show = '<span class="label label-danger material-label material-label_danger main-container__column">Storniran</span>';
												}
												else if($status_dak_kandidat == 7){
													$status_show = '<span class="label label-danger material-label material-label_danger main-container__column">Arhiviran</span>';
												}
												else if($status_dak_kandidat == 8){
													$status_show = '<span class="label label-warning material-label material-label_warning main-container__column">Na čekanju</span>';
												}

												if($contract_dak_kandidat != NULL){
													$contract_postojanje = '<span class="label label-success material-label material-label_success main-container__column">DA</span>';
												}else{
													$contract_postojanje = '<span class="label label-warning material-label material-label_warning main-container__column">NE</span>';
												}

												if($passport_dak_kandidat != NULL){
													$passport_postojanje = '<span class="label label-success material-label material-label_success main-container__column">DA</span>';
												}else{
													$passport_postojanje = '<span class="label label-warning material-label material-label_warning main-container__column">NE</span>';
												}

												// RATE

												if($rata1 == 0){
													$rata1_text = '<span class="label label-warning material-label material-label_warning main-container__column">NE</span>';
												}else{
													$rata1_text = '<span class="label label-success material-label material-label_success main-container__column">DA</span>';
												}

												if($rata2 == 0){
													$rata2_text = '<span class="label label-warning material-label material-label_warning main-container__column">NE '.$predikcija_isplate_rate2.'</span>';
												}else{
													$rata2_text = '<span class="label label-success material-label material-label_success main-container__column">DA '.$predikcija_isplate_rate2.'</span>';
												}

										?>
										<tr>
											<td class="text-center"><?php echo $id_dak_kandidat; ?></td>
											<td><?php echo "".$name_dak_kandidat." ". $lastname_dak_kandidat."";?> <?php if($kasnjenje == 1){?> <i style="color:red;" class="fa fa-exclamation-triangle" aria-hidden="true"></i><?php }else{ }?></td>
											<td><?php echo $tel_dak_kandidat; ?></td>
											<td><?php echo $email_dak_kandidat; ?></td>
											<td class="text-center"><?php echo $status_show;?></td>
											<?php if($rata1 == 0){ ?>
												<td class="text-center"><a href="#" data="<?php getSiteURL(); ?>dak?page=plati_ratu&id=<?php echo $id_dak_kandidat; ?>&tip=<?php echo $status; ?>&rata=1" data-toggle="modal" data-target="#payRata" class="payRata material-dropdown-menu__link"><?php echo $rata1_text;?></a></td>
											<?php }else{ ?>
												<td class="text-center"><?php echo $rata1_text;?></td>
											<?php } ?>
											<?php if($rata2 == 0){ ?>
												<td class="text-center"><a href="#" data="<?php getSiteURL(); ?>dak?page=plati_ratu&id=<?php echo $id_dak_kandidat; ?>&tip=<?php echo $status; ?>&rata=2" data-toggle="modal" data-target="#payRata" class="payRata material-dropdown-menu__link"><?php echo $rata2_text;?></a></td>
											<?php }else{ ?>
												<td class="text-center"><?php echo $rata2_text;?></td>
											<?php } ?>

											<td class="text-center"><?php echo $manager_dak_kandidat;?></td>
											<td class="text-center">
												<div class="btn-group material-btn-group">
													<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
													<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
														<li><a href="<?php getSiteURL(); ?>dak?page=open&id=<?php echo $id_dak_kandidat; ?>" class="material-dropdown-menu__link"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori</a></li>
														<?php if($status_dak_kandidat !=7){ ?>
															<li><a href="<?php getSiteURL(); ?>dak?page=edit&id=<?php echo $id_dak_kandidat; ?>" class="material-dropdown-menu__link"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Uredi</a></li>
														<?php } ?>
														<!-- KOMENTIRANA PROMJENA STATUSA KROZ DATATABLE JER IMA PREVISE NOVIH STATUSA -->
														<!--
														<?php if($status_dak_kandidat == 0 && $status_dak_kandidat !=7){ /* ?>
															<li><a href="#" data="<?php getSiteURL(); ?>dak?page=change_status&id=<?php echo $id_dak_kandidat; ?>&status=1&tip=<?php echo $tip_unosa_status; ?>" data-toggle="modal" data-target="#change_statusModal" class="change_status material-dropdown-menu__link"><i class="fa fa-spinner" aria-hidden="true"></i> Obrada</a></li>
														<?php */ } ?>
														<?php if($status_dak_kandidat == 1 && $status_dak_kandidat !=7){ /* ?>
															<li><a href="#" data="<?php getSiteURL(); ?>dak?page=change_status&id=<?php echo $id_dak_kandidat; ?>&status=3&tip=<?php echo $tip_unosa_status; ?>" data-toggle="modal" data-target="#change_statusModal" class="change_status material-dropdown-menu__link"><i class="fa fa-check" aria-hidden="true"></i> Aktiviraj</a></li>
														<?php */ } ?>
														-->
														<?php if($status_dak_kandidat !=7){ ?>
															<li class="material-btn_warning"><a href="#" data="<?php getSiteURL(); ?>dak?page=archive&id=<?php echo $id_dak_kandidat; ?>&tip=<?php echo $status; ?>" data-toggle="modal" data-target="#archiveModal" class="archive material-dropdown-menu__link"><i class="fa fa-trash-o" aria-hidden="true"></i> Storniraj</a></li>
														<?php } ?>
														<?php if($status_dak_kandidat !=7){ ?>
															<li class="idk_dropdown_danger"><a href="#" data="<?php getSiteURL(); ?>dak?page=arhiviraj_kandidata&id=<?php echo $id_dak_kandidat; ?>&tip=<?php echo $status; ?>" data-toggle="modal" data-target="#arhivirajModal" class="arhiviraj material-dropdown-menu__link"><i class="fa fa-trash-o" aria-hidden="true"></i> Arhiviraj</a></li>
														<?php } ?>
														<?php if($status_dak_kandidat !=7){ ?>
															<li class="idk_dropdown_danger"><a href="#" data="<?php getSiteURL(); ?>dak?page=naCekanje&id=<?php echo $id_dak_kandidat; ?>&tip=<?php echo $status; ?>" data-toggle="modal" data-target="#naCekanjeModal" class="naCekanje material-dropdown-menu__link"><i class="fa fa-trash-o" aria-hidden="true"></i> Na čekanje</a></li>
														<?php } ?>
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
											$(".change_status").click(function () {
												var addressValue = $(this).attr("data");
												document.getElementById("change_status_link").href = addressValue;
											});
											$(".payRata").click(function () {
												var addressValue = $(this).attr("data");
												document.getElementById("platiRatu").href = addressValue;
											});
											$(".arhiviraj").click(function () {
												var addressValue = $(this).attr("data");
												document.getElementById("arhiviraj_link").href = addressValue;
											});
											$(".naCekanje").click(function () {
												var addressValue = $(this).attr("data");
												document.getElementById("naCekanje_link").href = addressValue;
											});
										</script>
										<!-- Modal NA CEKANJE-->
										<div class="modal material-modal material-modal_danger fade" id="naCekanjeModal">
											<div class="modal-dialog">
												<div class="modal-content material-modal__content">
													<div class="modal-header material-modal__header">
														<button class="close material-modal__close" data-dismiss="modal">&times;</button>
														<h4 class="modal-title material-modal__title">Pošalji kandidata na <i>NA ČEKANJE</i></h4>
													</div>
													<div class="modal-body material-modal__body">
														<p>Status kandidata je "Na čekanju"!</p>
													</div>
													<div class="modal-footer material-modal__footer">
														<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
														<a id="naCekanje_link" href=""><button class="btn btn-primary material-btn material-btn_danger">POTVRDI</button></a>
													</div>
												</div>
											</div>
										</div>
										<!-- Modal STORNACIJA-->
										<div class="modal material-modal material-modal_danger fade" id="archiveModal">
											<div class="modal-dialog">
												<div class="modal-content material-modal__content">
													<div class="modal-header material-modal__header">
														<button class="close material-modal__close" data-dismiss="modal">&times;</button>
														<h4 class="modal-title material-modal__title">Storniranje</h4>
													</div>
													<div class="modal-body material-modal__body">
														<p>Jeste li sigurni da želite stornirati DAK kandidata?</p>
													</div>
													<div class="modal-footer material-modal__footer">
														<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
														<a id="archive_link" href=""><button class="btn btn-primary material-btn material-btn_danger">STORNIRAJ</button></a>
													</div>
												</div>
											</div>
										</div>
										<!-- Modal arhivacije -->
										<div class="modal material-modal material-modal_danger fade" id="arhivirajModal">
											<div class="modal-dialog">
												<div class="modal-content material-modal__content">
													<div class="modal-header material-modal__header">
														<button class="close material-modal__close" data-dismiss="modal">&times;</button>
														<h4 class="modal-title material-modal__title">Arhiviranje</h4>
													</div>
													<div class="modal-body material-modal__body">
														<p>Jeste li sigurni da želite arhivirati DAK kandidata?</p>
													</div>
													<div class="modal-footer material-modal__footer">
														<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
														<a id="arhiviraj_link" href=""><button class="btn btn-primary material-btn material-btn_danger">ARHIVIRAJ</button></a>
													</div>
												</div>
											</div>
										</div>

										<!-- Modal change status-->
										<div class="modal material-modal material-modal_primary fade" id="change_statusModal">
											<div class="modal-dialog">
												<div class="modal-content material-modal__content">
													<div class="modal-header material-modal__header">
														<button class="close material-modal__close" data-dismiss="modal">&times;</button>
														<h4 class="modal-title material-modal__title">Promjeni status</h4>
													</div>
													<div class="modal-body material-modal__body">
														<p>Jeste li sigurni da želite promjeniti status DAK kandidata?</p>
													</div>
													<div class="modal-footer material-modal__footer">
														<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
														<a id="change_status_link" href=""><button class="btn btn-primary material-btn material-btn_primary">PROMJENI</button></a>
													</div>
												</div>
											</div>
										</div>
											<!-- Modal plati ratu-->
										<div class="modal material-modal material-modal_primary fade" id="payRata">
											<div class="modal-dialog">
												<div class="modal-content material-modal__content">
													<div class="modal-header material-modal__header">
														<button class="close material-modal__close" data-dismiss="modal">&times;</button>
														<h4 class="modal-title material-modal__title">Plati ratu</h4>
													</div>
													<div class="modal-body material-modal__body">
														<p>Jeste li sigurni da želite platiti ratu DAK kandidata?</p>
													</div>
													<div class="modal-footer material-modal__footer">
														<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
														<a id="platiRatu" href=""><button class="btn btn-primary material-btn material-btn_primary">PLATI</button></a>
													</div>
												</div>
											</div>
										</div>
									</tbody>
								</table>
								<!--
								 /** TABELA ZA BINGOA, BONGA, BOHUMA, BRONHITISA **/
								 *
								 *
								 *	BEGIN
								 *
								 **/
								<?php }else{

									if(!empty($status)){ /* case : anfrage */?>
									<!--
									 /** case : anfrage **/
									 *
									 *
									 *	BEGIN
									 *
									 **/
									 -->
									 <script type="text/javascript">
										$(document).ready(function() {
											$('#idk_table_vanjskiSaradnik').DataTable({

												responsive: true,

												"order": [[ 1, "asc" ]],

												 "bAutoWidth": false,

												"aoColumns": [
														{ "width": "5%" },
														{ "width": "25%" },
														{ "width": "12.5%" },
														{ "width": "12-5%" },
														{ "width": "15%" },
														{ "width": "15%" },
														{ "width": "15%", "bSortable": false }
													]
											});
										} );
									</script>
									<table id="idk_table_vanjskiSaradnik" class="display" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th class="text-center">#ID</th>
												<th>NAME</th>
												<th>ZEIT DER ANFRAGE</th>
												<th>GESCHAETZTE BESCHAFTIGUNGSZEIT</th>
												<th class="text-center">AKTIVIEREN</th>
												<th class="text-center">STORNIREN</th>
												<th class="text-center">AUSSTEHEND</th>
											</tr>
										</thead>
										<tbody>
											<?php
											/** ANFRAGE TRAŽI KANDIDATA ČIJI JE STATUS POSLAN **
											*
											*
											*
											*
											**/
												$query = $db->prepare("
																SELECT *
																FROM idk_dak_kandidati
																WHERE status_dak_kandidat = :status_dak_kandidat");
												$query->execute(array(
																':status_dak_kandidat' => $status));

												$kasnjenje = 0;
												while($row = $query->fetch()){

													$id_dak_kandidat = $row['id_dak_kandidat'];
													$name_dak_kandidat = $row['name_dak_kandidat'];
													$lastname_dak_kandidat = $row['lastname_dak_kandidat'];
													$pocetak_rada = $row['pocetakrada_dak_kandidat'];
													$datumslanja_dak_kandidat = date('d.m.Y', strtotime($row['datumslanja_dak_kandidat']));

											?>
											<tr>
												<td class="text-center"><?php echo $id_dak_kandidat; ?></td>
												<td><?php echo "".$name_dak_kandidat." ". $lastname_dak_kandidat."";?> <?php if($kasnjenje == 1){?> <i style="color:red;" class="fa fa-exclamation-triangle" aria-hidden="true"></i><?php }else{ }?></td>
												<td><?php echo $datumslanja_dak_kandidat; ?></td>
												<td><?php echo $pocetak_rada; ?></td>
												<?php
												 /**
												 * SLIJEDI DUGME AKTIVACIJE  / TOOGLE MODAL #AKTIVIRAJ *
												 **/
												?>
												<td align="center"><a href="#" data="<?php getSiteURL();?>dak?page=change_status&id=<?php echo $id_dak_kandidat; ?>&bingo=true" data-toggle="modal" data-target="#change_statusModal"  type="button" class="change_status_aktiviraj btn btn-primary">Aktivieren</a></td>
												 <?php
												 /**
												 * SLIJEDI DUGME STORNACIJE  / TOOGLE MODAL #archiveModal *
												 **/
												?>
												<td align="center"><a href="#" data="<?php getSiteURL();?>dak?page=archive&id=<?php echo $id_dak_kandidat; ?>" data-toggle="modal" data-target="#archiveModal"  type="button" class="btn btn-danger archive">Stornieren</a></td>
												 <?php
												 /**
												 * SLIJEDI DUGME ČEKANJA  / TOOGLE MODAL #NA ČEKANJU *
												 **/
												?>
												<td align="center"><a href="#" data="<?php getSiteURL();?>dak?page=change_status&id=<?php echo $id_dak_kandidat; ?>&bingo=true" data-toggle="modal" data-target="#notify_modal"  type="button" class="change_status_cekaj btn btn-info">Ausstehend</a></td>

											</tr>
											<?php } ?>
											<script>
												$(".archive").click(function () {
													var addressValue = $(this).attr("data");
													document.getElementById("dak_storn").action = addressValue;
												});
												$(".change_status_aktiviraj").click(function () {
													var addressValue = $(this).attr("data");
													document.getElementById("change_status_link_aktiv").href = addressValue;
												});
												$(".change_status_cekaj").click(function () {
													var addressValue = $(this).attr("data");
													document.getElementById("change_status_link_aus").href = addressValue;
												});
											</script>
											<!-- Modal -->
											<div class="modal material-modal material-modal_danger fade" id="archiveModal">
												<div class="modal-dialog">
													<div class="modal-content material-modal__content">
														<div class="modal-header material-modal__header">
															<button class="close material-modal__close" data-dismiss="modal">&times;</button>
															<h4 class="modal-title material-modal__title">Stornierung</h4>
														</div>
														<div class="modal-body material-modal__body">
															<form id="dak_storn" action="" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
															<label for="razlog_dak_storn" class="control-label"><span class="text-danger">*</span><b>Grund der Stornierung:</b></label>
															<select form="dak_storn" class="selectpicker" title="Wähle einen Grund" id="razlog_dak_storn_select" onchange="showTextArea()" name="razlog_dak_storn_select" data-live-search="true" required>
																					<?php
																						$query_razlog = $db->prepare("
																								SELECT id_ro,naziv_ro_de
																								FROM idk_ro_usluge
																								WHERE tip_ro = 2");
																						$query_razlog->execute(array());

																						while($red = $query_razlog->fetch()){
																							$id_ro = $red['id_ro'];
																							$naziv_ro_de = $red['naziv_ro_de'];
																					?>

																						<option value="<?php echo $id_ro; ?>"> <?php echo $naziv_ro_de; ?></option>
																				<?php } ?>
																				<option value="NULL" >Neuer Grund</option>
																	</select>
																	<script>
																			function showTextArea(){
																				var select_id = document.getElementById('razlog_dak_storn_select').value;
																				var razlog = document.getElementById('razlog_textarea');
																				if (select_id == 'NULL') {
																					razlog.style.display = "block";
																					document.getElementById('razlog_dak_storn').required = true;
																				}
																				else {
																					 razlog.style.display = "none";
																					document.getElementById('razlog_dak_storn').required = false;
																				}
																			}
																		</script>
															<div class="form-group">
																	<div   id = "razlog_textarea" style="display: none; padding-top:2rem;">
																		<div class="materail-input-block materail-input-block_success" style="margin-bottom: 1rem">
																			<textarea style="border-top: 2px solid #eee"; class="form-control materail-input" type="text" name="razlog_dak_storn" id="razlog_dak_storn" rows="8" cols="40" placeholder="Grund der Stornierung"></textarea>
																		</div>
																	</div>
																</div>
															</form>
														</div>
														<div class="modal-footer material-modal__footer">
															<button class="btn material-btn material-btn" data-dismiss="modal">Abschlussen</button>
															<button href="#" type="submit" form="dak_storn" class="btn btn-primary material-btn material-btn_danger">Archiviren</button>
														</div>
													</div>
												</div>
											</div>

											<!-- Modal change status-->
											<div class="modal material-modal material-modal_primary fade" id="change_statusModal">
												<div class="modal-dialog">
													<div class="modal-content material-modal__content">
														<div class="modal-header material-modal__header">
															<button class="close material-modal__close" data-dismiss="modal">&times;</button>
															<h4 class="modal-title material-modal__title">Aktiviren</h4>
														</div>
														<div class="modal-body material-modal__body text-center">
															<h4>Sind Sie sicher, dass Sie diesen Kandidat aktiviren wollen?</h4>
														</div>
														<div class="modal-footer material-modal__footer">
															<button class="btn material-btn material-btn" data-dismiss="modal">Abschlussen</button>
															<a id="change_status_link_aktiv" href=""><button class="btn btn-primary material-btn material-btn_primary">Vertig</button></a>
														</div>
													</div>
												</div>
											</div>
												<!-- Modal plati ratu-->
											<div class="modal material-modal material-modal_primary fade" id="notify_modal">
												<div class="modal-dialog">
													<div class="modal-content material-modal__content">
														<div class="modal-header material-modal__header">
															<button class="close material-modal__close" data-dismiss="modal">&times;</button>
															<h4 class="modal-title material-modal__title">Ausstehend bleiben</h4>
														</div>
														<div class="modal-body material-modal__body text-center">
															<h4>Mit diesen Aktion, werden Sie Jobstep Kundenrservice meleden.</h4>
														</div>
														<div class="modal-footer material-modal__footer">
															<button class="btn material-btn material-btn" data-dismiss="modal">Abschlussen</button>
															<a id="change_status_link_aus" href=""><button class="btn btn-primary material-btn material-btn_primary">Vertig</button></a>
														</div>
													</div>
												</div>
											</div>
										</tbody>
									</table>
									<?php } else { ?>
									<!--
									 /** CASE ANFRAG **/
									 *
									 *
									 *	KRAJ
									 *
									 **/
									 -->
									<!--
									********************************************************
									 /** CASE VERZEICHNES **/
									 *
									 *
									 *	BEGIN
									 *
									 **/
									 -->
									 <script type="text/javascript">
										$(document).ready(function() {


											$('#idk_table_vanjskiSaradnik').DataTable({

												responsive: true,

												"order": [[ 1, "asc" ]],

												 "bAutoWidth": false,

												"aoColumns": [
														{ "width": "5%" },
														{ "width": "25%" },
														{ "width": "23%" },
														{ "width": "23%" },
														{ "width": "24%" }
													]
											});

										} );


									</script>
									<table id="idk_table_vanjskiSaradnik" class="display" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th class="text-center">#ID</th>
												<th>NAME</th>
												<th>ZEIT DER ANFRAGE</th>
												<th>GESCHAETZTE BESCHAFTIGUNGSZEIT</th>
												<th class="text-center">MÖGLICHKEITEN</th>
											</tr>
										</thead>
										<tbody>
											<?php
											/** VERZEICHNES TRAŽI KANDIDATA ČIJI JE STATUS AKTIVAN/STORNIRAN **
											*
											*
											*
											*
											**/	$query = $db->prepare("
																SELECT *
																FROM idk_dak_kandidati
																WHERE status_dak_kandidat IN (3,5,6)");
												$query->execute(array());

												$kasnjenje = 0;
												while($row = $query->fetch()){

													$id_dak_kandidat = $row['id_dak_kandidat'];
													$name_dak_kandidat = $row['name_dak_kandidat'];
													$lastname_dak_kandidat = $row['lastname_dak_kandidat'];
													$pocetak_rada = $row['pocetakrada_dak_kandidat'];
													$datumslanja_dak_kandidat = date('d.m.Y', strtotime($row['datumslanja_dak_kandidat']));
													$status_dak_kandidat = $row['status_dak_kandidat'];

											?>
											<tr>
												<td class="text-center"><?php echo $id_dak_kandidat; ?></td>
												<td><?php echo "".$name_dak_kandidat." ". $lastname_dak_kandidat."";?> <?php if($kasnjenje == 1){?> <i style="color:red;" class="fa fa-exclamation-triangle" aria-hidden="true"></i><?php }else{ }?></td>
												<td><?php echo $datumslanja_dak_kandidat; ?></td>
												<td><?php echo $pocetak_rada; ?></td>
												 <?php
												 /**
												 * SLIJEDI DUGME STORNACIJE  / TOOGLE MODAL #archiveModal *
												 **/

												if($status_dak_kandidat == 3){
												?>
												<td align="center"><a href="#" data="<?php getSiteURL();?>dak?page=archive&id=<?php echo $id_dak_kandidat; ?>" data-toggle="modal" data-target="#archiveModal"  type="button" class="btn btn-danger archive">Stornieren</a></td>
												<?php }else { ?>
												<td class="text-center">Der Kandidat wurde bereits storniert</td>
												<?php } ?>
											</tr>
											<?php } ?>
											<script>
												$(".archive").click(function () {
													var addressValue = $(this).attr("data");
													document.getElementById("dak_storn").action = addressValue;
												});
											</script>
											<!-- Modal stornacije  | CASE VERZEICHNES-->
											<div class="modal material-modal material-modal_danger fade" id="archiveModal">
												<div class="modal-dialog">
													<div class="modal-content material-modal__content">
														<div class="modal-header material-modal__header">
															<button class="close material-modal__close" data-dismiss="modal">&times;</button>
															<h4 class="modal-title material-modal__title">Stornierung</h4>
														</div>
														<div class="modal-body material-modal__body">
															<form id="dak_storn" action="" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
																<label for="razlog_dak_storn" class="control-label"><span class="text-danger">*</span><b>Grund der Stornierung:</b></label>
																<select form="dak_storn" class="selectpicker" title="Wähle einen Grund" id="razlog_dak_storn_select" onchange="showTextArea()" name="razlog_dak_storn_select" data-live-search="true" required>
																					<?php
																						$query_razlog = $db->prepare("
																								SELECT id_ro,naziv_ro_de
																								FROM idk_ro_usluge
																								WHERE tip_ro = 2");
																						$query_razlog->execute(array());

																						while($red = $query_razlog->fetch()){
																							$id_ro = $red['id_ro'];
																							$naziv_ro_de = $red['naziv_ro_de'];
																					?>

																						<option value="<?php echo $id_ro; ?>"> <?php echo $naziv_ro_de; ?></option>
																				<?php } ?>
																				<option value="NULL" >Neuer Grund</option>
																	</select>
																	<script>
																			function showTextArea(){
																				var select_id = document.getElementById('razlog_dak_storn_select').value;
																				var razlog = document.getElementById('razlog_textarea');
																				if (select_id == 'NULL') {
																					razlog.style.display = "block";
																					document.getElementById('razlog_dak_storn').required = true;
																				}
																				else {
																					 razlog.style.display = "none";
																					document.getElementById('razlog_dak_storn').required = false;
																				}
																			}
																		</script>
																<div class="form-group">
																	<div   id = "razlog_textarea" style="display: none; padding-top:2rem;">
																		<div class="materail-input-block materail-input-block_success" style="margin-bottom: 1rem">
																			<textarea style="border-top: 2px solid #eee"; class="form-control materail-input" type="text" name="razlog_dak_storn" id="razlog_dak_storn" rows="8" cols="40" placeholder="Grund der Stornierung"></textarea>
																		</div>
																	</div>
																</div>
															</form>
														</div>
														<div class="modal-footer material-modal__footer">
															<button class="btn material-btn material-btn" data-dismiss="modal">Abschlussen</button>
															<button href="#" type="submit" form="dak_storn" class="btn btn-primary material-btn material-btn_danger">Archiviren</button>
														</div>
													</div>
												</div>
											</div>
											<!-- Modal stornacije  | CASE VERZEICHNES    END !!-->
										</tbody>
									</table>
									<!--
									 /** CASE VERZEICHNES **/
									 *
									 *
									 *	KRAJ
									 *
									 **/
									 -->
									 <!--
									 /** TABELA ZA BINGOA, BONGA, BOHUMA, BRONHITISA **/
									 *
									 *
									 *	KRAJ
									 *
									 **/
									 -->
								<?php } } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php
				break;

				case "edit":
					if((in_array( "3" , $getEmployeeStatus)) OR (in_array( "5" , $getEmployeeStatus)) OR (in_array( "2" , $employee_supervizor)) OR (in_array( "1" , $getEmployeeStatus))){

						$kandidat_id = $_GET['id'];

						$query = $db->prepare("
										SELECT *
										FROM idk_dak_kandidati
										WHERE id_dak_kandidat = :id_dak_kandidat");

						$query->execute(array(
									':id_dak_kandidat' => $kandidat_id));

						$row = $query->fetch();

							$name_dak_kandidat = $row['name_dak_kandidat'];
							$lastname_dak_kandidat = $row['lastname_dak_kandidat'];
							$email_dak_kandidat = $row['email_dak_kandidat'];
							$daypart_dak_kandidat = $row['daypart_dak_kandidat'];
							$comment_dak_kandidat = $row['comment_dak_kandidat'];
							$company_dak_kandidat = $row['company_dak_kandidat'];
							$poslovnica_dak_kandidat = $row['poslovnica_dak_kandidat'];
							$tipunosa_dak_kandidat = $row['tipunosa_dak_kandidat'];
							$pocetak_rada = $row['pocetakrada_dak_kandidat'];

		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-briefcase idk_color_green" aria-hidden="true"></i> Uredi DAK kandidata</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<!-- <a href="<?php echo $_SERVER['HTTP_REFERER']; ?>" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>-->
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
								<form id="idk_form" action="<?php getSiteURL(); ?>do.php?form=edit_dak_kandidat" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<input type="hidden" name="id_dak_kandidat" value="<?php echo $kandidat_id; ?>" />
									<div class="form-group">
										<label for="name_dak_kandidat" class="col-sm-3 control-label"><span class="text-danger">*</span> Ime:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="name_dak_kandidat" id="name_dak_kandidat" placeholder="Naziv" value="<?php echo $name_dak_kandidat; ?>" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="lastname_dak_kandidat" class="col-sm-3 control-label"><span class="text-danger">*</span> Prezime:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="lastname_dak_kandidat" id="lastname_dak_kandidat" value="<?php echo $lastname_dak_kandidat; ?>" />
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="email_dak_kandidat" class="col-sm-3 control-label"><span class="text-danger">*</span> E-mail:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="email" name="email_dak_kandidat" id="email_dak_kandidat" value="<?php echo $email_dak_kandidat; ?>" />
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="dak_kandidat_rucno_poslodavac" class="col-sm-3 control-label"><span class="text-danger">*</span>Poslodavac:</label>
										<div class="col-sm-9">
											<div class="">
												<select class="selectpicker" id="dak_kandidat_rucno_poslodavac" name="dak_kandidat_rucno_poslodavac" required>
													<option  value ="NULL">Nepoznato</option>
													<?php
														$company_query = $db->prepare("
																		SELECT *
																		FROM idk_companies
																		WHERE company_status = 1
																		");

														$company_query->execute();

														while($row = $company_query->fetch()){

														$company_id = $row['company_id'];
														$company_name = $row['company_name'];
													?>

														<option value="<?php echo $company_id; ?>" <?php if($company_id == $company_dak_kandidat){echo "selected";} ?>><?php echo $company_name; ?></option>
													<?php } ?>
												</select>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="pocetak_rada" class="col-sm-3 control-label"><span class="text-danger">*</span> Datum početka rada:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="pocetak_rada" id="pocetak_rada" class="monthPicker" placeholder="Potencijalni datum početka rada" autocomplete="off" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<script>
										$( function() {
											$( "#pocetak_rada" ).datepicker({
											changeMonth: true,
											changeYear: true,
												dateFormat: 'dd.mm.yy',
												yearRange: '2017:2026'
											});
										});
									</script>
									<div class="form-group">
										<label for="dak_kandidat_poslovnica" class="col-sm-3 control-label"><span class="text-danger">*</span>Poslovnica:</label>
										<div class="col-sm-9">
											<div class="">
												<select class="selectpicker" id="dak_kandidat_poslovnica" name="dak_kandidat_poslovnica" required>
													<option  selected="true" disabled="disabled">Odaberi</option>
													<option value="1" <?php if($poslovnica_dak_kandidat == 1){echo "selected";} ?>>Poslovnica Bihać</option>
													<option value="2" <?php if($poslovnica_dak_kandidat == 2){echo "selected";} ?>>Poslovnica Sarajevo</option>
													<option value="3" <?php if($poslovnica_dak_kandidat == 3){echo "selected";} ?>>SVZ Beograd</option>
													<option value="3" <?php if($poslovnica_dak_kandidat == 4){echo "selected";} ?>>SVZ Sarajevo</option>
												</select>
											</div>
										</div>
									</div>
									<?php if($tipunosa_dak_kandidat == 0){ ?>
									<div class="form-group">
										<label for="daypart_dak_kandidat" class="col-sm-3 control-label">Period dana za zvanje:</label>
										<div class="col-sm-9">
											<select class="selectpicker" id="daypart_dak_kandidat" name="daypart_dak_kandidat">
												<option <?php if($daypart_dak_kandidat == 0) echo 'selected';?> value="0">Jutro</option>
												<option <?php if($daypart_dak_kandidat == 1) echo 'selected';?> value="1">Podne</option>
												<option <?php if($daypart_dak_kandidat == 2) echo 'selected';?> value="2">Prednoć</option>
												<option <?php if($daypart_dak_kandidat == 3) echo 'selected';?> value="3">Nebitno</option>
											</select>
										</div>
									</div>
									<?php } ?>
									<div class="form-group">
										<label for="comment_dak_kandidat" class="col-sm-3 control-label">Ostale informacije:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="comment_dak_kandidat" id="comment_dak_kandidat" value="<?php echo $comment_dak_kandidat; ?>">
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

					$id_dak_kandidat = $_GET['id'];

					$query = $db->prepare("
									SELECT *
									FROM idk_dak_kandidati
									WHERE id_dak_kandidat = :id_dak_kandidat");

					$query->execute(array(
								':id_dak_kandidat' => $id_dak_kandidat));

					$row = $query->fetch();

						if($row['origin_dak_kandidat'] != NULL){
							$origin_dak_kandidat = '<span class="label label-success material-label material-label_success main-container__column">Ručno</span>';
						}
						else{
							$origin_dak_kandidat = '<span class="label label-success material-label material-label_success main-container__column">Aplikacija</span>';
						}
						if($row['manager_dak_kandidat'] != NULL){
							$manager_dak_kandidat = getZaposlenikimeR($row['manager_dak_kandidat']);
						}
						else{
							$manager_dak_kandidat = '<span class="label label-success material-label material-label_success main-container__column">Aplikacija</span>';
						}
						$tel_dak_kandidat = $row['tel_dak_kandidat'];
						$telconfirm_dak_kandidat = $row['telconfirm_dak_kandidat'];
						$name_dak_kandidat = $row['name_dak_kandidat'];
						$lastname_dak_kandidat = $row['lastname_dak_kandidat'];
						$email_dak_kandidat = $row['email_dak_kandidat'];
						$company_dak_kandidat = $row['company_dak_kandidat'];
						$poslovnica_dak_kandidat = $row['poslovnica_dak_kandidat'];
						$daypart_dak_kandidat = $row['daypart_dak_kandidat'];
						$comment_dak_kandidat = $row['comment_dak_kandidat'];
						$passport_dak_kandidat = $row['passport_dak_kandidat'];
						$timepassport_dak_kandidat = $row['passporttime_dak_kandidat'];
						$contract_dak_kandidat = $row['contract_dak_kandidat'];
						$timecontract_dak_kandidat = $row['contracttime_dak_kandidat'];
						$status_dak_kandidat = $row['status_dak_kandidat'];
						$tipunosa_dak_kandidat = $row['tipunosa_dak_kandidat'];
						$datumunosa_dak_kandidat = $row['datumunosa_dak_kandidat'];
						$datumunosa_dak_kandidat_f = date('d.m.Y H:i', strtotime($row['datumunosa_dak_kandidat']));
						$datumaktivacije_dak_kandidat = $row['datumaktivacije_dak_kandidat'];
						$datumaktivacije_dak_kandidat_f = date('d.m.Y H:i', strtotime($row['datumaktivacije_dak_kandidat']));
						$datumslanja_dak_kandidat = $row['datumslanja_dak_kandidat'];
						$datumslanja_dak_kandidat_f = date('d.m.Y H:i', strtotime($row['datumslanja_dak_kandidat']));

						if($row['pocetakrada_dak_kandidat'] != NULL){
							$pocetakrada_dak_kandidat = date('d.m.Y', strtotime($row['pocetakrada_dak_kandidat']));
						}else{
							$pocetakrada_dak_kandidat = "Nije unešeno";
						}
						$datumstornacije_dak_kandidat = $row['datumstornacije_dak_kandidat'];
						$datumstornacije_dak_kandidat_f = date('d.m.Y H:i', strtotime($row['datumstornacije_dak_kandidat']));
						$datumobrade_dak_kandidat = $row['datumobrade_dak_kandidat'];
						$datumobrade_dak_kandidat_f = date('d.m.Y H:i', strtotime($row['datumobrade_dak_kandidat']));
						$datumzavrsetka_dak_kandidat = $row['datumzavrsetka_dak_kandidat'];
						$datumzavrsetka_dak_kandidat_f = date('d.m.Y H:i', strtotime($row['datumzavrsetka_dak_kandidat']));
						$datumstornoaktivacije_dak_kandidat = $row['datumstornoaktivacije_dak_kandidat'];
						$datumstornoaktivacije_dak_kandidat_f = date('d.m.Y H:i', strtotime($row['datumstornoaktivacije_dak_kandidat']));

						if($status_dak_kandidat == 0){
							$buttontxt1 = "material-btn_warning";
							$tabs_class = "material-tabs_warning";
						}elseif($status_dak_kandidat == 1){
							$buttontxt2 = "material-btn_primary";
							$tabs_class = "material-tabs_primary";
						}elseif($status_dak_kandidat == 2){
							$buttontxt3 = "material-btn_primary";
							$tabs_class = "material-tabs_primary";
						}elseif($status_dak_kandidat == 3){
							$buttontxt4 = "material-btn_success";
							$tabs_class = "material-tabs_success";
						}elseif($status_dak_kandidat == 4){
							$buttontxt5 = "material-btn_success";
							$tabs_class = "material-tabs_success";
						}elseif($status_dak_kandidat == 5){
							$buttontxt6 = "material-btn_danger";
							$tabs_class = "material-tabs_danger";
						}elseif($status_dak_kandidat == 6){
							$buttontxt7 = "material-btn_danger";
							$tabs_class = "material-tabs_danger";
						}

						if($timepassport_dak_kandidat == NULL){
							$passporttime_dak_kandidat = '<span class="label label-primary material-label material-label_warning main-container__column text-left">Nije definisano.</span>';
						}
						else{
							$passporttime_dak_kandidat = date('d.m.Y H:i', strtotime($row['passporttime_dak_kandidat']));
						}
						if($timecontract_dak_kandidat == NULL){
							$contracttime_dak_kandidat = '<span class="label label-primary material-label material-label_warning main-container__column text-left">Nije definisano.</span>';
						}
						else{
							$contracttime_dak_kandidat = date('d.m.Y H:i', strtotime($row['contracttime_dak_kandidat']));
						}

						if($telconfirm_dak_kandidat == 1){
							$tel_dak_kandidat = '<span class="label label-success material-label material-label_success main-container__column">'.$tel_dak_kandidat.'</span>';
						}else{
							$tel_dak_kandidat = '<span class="label label-warning material-label material-label_warning main-container__column">'.$tel_dak_kandidat.'</span>';
						}

						if($daypart_dak_kandidat == 0){
							$daypart = '<span class="label label-success material-label material-label_success main-container__column">Jutro</span>';
						}else if($daypart_dak_kandidat == 1){
							$daypart = '<span class="label label-warning material-label material-label_warning main-container__column">Podne</span>';
						}else if($daypart_dak_kandidat == 2){
							$daypart = '<span class="label label-warning material-label material-label_warning main-container__column">Prednoć</span>';
						}else{
							$daypart = '<span class="label label-warning material-label material-label_warning main-container__column">Nebitno</span>';
						}

						//POSLOVNICA
						if($poslovnica_dak_kandidat == 1){
							$poslovnica = '<span class="label label-info material-label material-label_info main-container__column">Poslovnica Bihać</span>';
						}else if($poslovnica_dak_kandidat == 2){
							$poslovnica = '<span class="label label-info material-label material-label_info main-container__column">Poslovnica Sarajevo</span>';
						}else if($poslovnica_dak_kandidat == 3){
							$poslovnica = '<span class="label label-info material-label material-label_info main-container__column">SVZ Sarajevo</span>';
						}else if($poslovnica_dak_kandidat == 4){
							$poslovnica = '<span class="label label-info material-label material-label_info main-container__column">SVZ Beograd</span>';
						}

						if($contract_dak_kandidat != NULL){
							$contract_postojanje = '<span class="label label-success material-label material-label_success main-container__column">DA</span>';
						}else{
							$contract_postojanje = '<span class="label label-warning material-label material-label_warning main-container__column">NE</span>';
						}

						if($passport_dak_kandidat != NULL){
							$passport_postojanje = '<span class="label label-success material-label material-label_success main-container__column">DA</span>';
						}else{
							$passport_postojanje = '<span class="label label-warning material-label material-label_warning main-container__column">NE</span>';
						}

		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><?php echo "".$name_dak_kandidat." ".$lastname_dak_kandidat .""; ?></h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>dak?page=list&type=<?php echo $tipunosa_dak_kandidat; ?>" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 text-right">
					<div class="btn-group main-container__column" role="group" aria-label="Basic example">
						<a href="#" data="<?php getSiteURL(); ?>dak?page=change_status&id=<?php echo $id_dak_kandidat; ?>&status=0&tip=<?php echo $tipunosa_dak_kandidat; ?>" data-toggle="modal" data-target="#change_statusModal" class="change_status material-dropdown-menu__link"><button type="button" class="btn material-btn change_status_candidate <?php echo $buttontxt1; ?>" data-id="<?php echo $kandidat_id ?>" data-status="0"><i class="fa fa-spinner" aria-hidden="true"></i> Novi</button></a>
						<a href="#" data="<?php getSiteURL(); ?>dak?page=change_status&id=<?php echo $id_dak_kandidat; ?>&status=1&tip=<?php echo $tipunosa_dak_kandidat; ?>" data-toggle="modal" data-target="#change_statusModal" class="change_status material-dropdown-menu__link"><button type="button" class="btn material-btn change_status_candidate <?php echo $buttontxt2; ?>" data-id="<?php echo $kandidat_id ?>" data-status="1"><i class="fa fa-tasks" aria-hidden="true"></i> U obradi</button></a>
						<a href="#" data="<?php getSiteURL(); ?>dak?page=change_status&id=<?php echo $id_dak_kandidat; ?>&status=2&tip=<?php echo $tipunosa_dak_kandidat; ?>" data-toggle="modal" data-target="#change_statusModal" class="change_status material-dropdown-menu__link"><button type="button" class="btn material-btn change_status_candidate <?php echo $buttontxt3; ?>" data-id="<?php echo $kandidat_id ?>" data-status="2"><i class="fa fa-envelope" aria-hidden="true"></i> Poslan</button></a>
						<a href="#" data="<?php getSiteURL(); ?>dak?page=change_status&id=<?php echo $id_dak_kandidat; ?>&status=3&tip=<?php echo $tipunosa_dak_kandidat; ?>" data-toggle="modal" data-target="#change_statusModal" class="change_status material-dropdown-menu__link"><button type="button" class="btn material-btn change_status_candidate <?php echo $buttontxt4; ?>" data-id="<?php echo $kandidat_id ?>" data-status="3"><i class="fa fa-envelope" aria-hidden="true"></i> Aktiviran</button></a>
						<a href="#" data="<?php getSiteURL(); ?>dak?page=change_status&id=<?php echo $id_dak_kandidat; ?>&status=4&tip=<?php echo $tipunosa_dak_kandidat; ?>" data-toggle="modal" data-target="#change_statusModal" class="change_status material-dropdown-menu__link"><button type="button" class="btn material-btn change_status_candidate <?php echo $buttontxt5; ?>" data-id="<?php echo $kandidat_id ?>" data-status="4"><i class="fa fa-check" aria-hidden="true"></i> Završen</button></a>
						<a href="#" data="<?php getSiteURL(); ?>dak?page=change_status&id=<?php echo $id_dak_kandidat; ?>&status=5&tip=<?php echo $tipunosa_dak_kandidat; ?>" data-toggle="modal" data-target="#change_statusModal" class="change_status material-dropdown-menu__link"><button type="button" class="btn material-btn change_status_candidate <?php echo $buttontxt6; ?>" data-id="<?php echo $kandidat_id ?>" data-status="5"><i class="fa fa-recycle" aria-hidden="true"></i> Storniran nakon aktivacije</button></a>
						<a href="#" data="<?php getSiteURL(); ?>dak?page=archive&id=<?php echo $id_dak_kandidat; ?>&tip=<?php echo $tipunosa_dak_kandidat; ?>" data-toggle="modal" data-target="#archiveModal" class="archive material-dropdown-menu__link"><button type="button" class="btn material-btn change_status_candidate <?php echo $buttontxt7; ?>" data-id="<?php echo $kandidat_id ?>" data-status="6" ><i class="fa fa-trash-o" aria-hidden="true"></i> Storniran</button></a>
						<a href="#" data="<?php getSiteURL(); ?>dak?page=arhiviraj_kandidata&id=<?php echo $id_dak_kandidat; ?>&tip=<?php echo $tipunosa_dak_kandidat; ?>" data-toggle="modal" data-target="#arhivirajModal" class="arhiviraj material-dropdown-menu__link"><button type="button" class="btn material-btn change_status_candidate <?php echo $buttontxt7; ?>" data-id="<?php echo $kandidat_id ?>" data-status="7" ><i class="fa fa-trash-o" aria-hidden="true"></i> Arhiviran</button></a>
						<a href="#" data="<?php getSiteURL(); ?>dak?page=naCekanje&id=<?php echo $id_dak_kandidat; ?>&tip=<?php echo $tipunosa_dak_kandidat; ?>" data-toggle="modal" data-target="#naCekanjeModal" class="naCekanje material-dropdown-menu__link"><button type="button" class="btn material-btn change_status_candidate <?php echo $buttontxt7; ?>" data-id="<?php echo $kandidat_id ?>" data-status="8" ><i class="fa fa-tasks" aria-hidden="true"></i> Na čekanje</button></a>
					</div>
				</div>
			</div>
			<script>
				$(".archive").click(function () {
					var addressValue = $(this).attr("data");
					document.getElementById("archive_link").href = addressValue;
				});
				$(".change_status").click(function () {
					var addressValue = $(this).attr("data");
					document.getElementById("change_status_link").href = addressValue;
				});
				$(".arhiviraj").click(function () {
						var addressValue = $(this).attr("data");
							document.getElementById("arhiviraj_link").href = addressValue;
						});
				$(".naCekanje").click(function () {
					var addressValue = $(this).attr("data");
					document.getElementById("naCekanje_link").href = addressValue;
				});
			</script>
			<!-- Modal arhivacije -->
										<div class="modal material-modal material-modal_danger fade" id="arhivirajModal">
											<div class="modal-dialog">
												<div class="modal-content material-modal__content">
													<div class="modal-header material-modal__header">
														<button class="close material-modal__close" data-dismiss="modal">&times;</button>
														<h4 class="modal-title material-modal__title">Arhiviranje</h4>
													</div>
													<div class="modal-body material-modal__body">
														<p>Jeste li sigurni da želite arhivirati DAK kandidata?</p>
													</div>
													<div class="modal-footer material-modal__footer">
														<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
														<a id="arhiviraj_link" href=""><button class="btn btn-primary material-btn material-btn_danger">ARHIVIRAJ</button></a>
													</div>
												</div>
											</div>
										</div>
										<!-- Modal NA CEKANJE-->
										<div class="modal material-modal material-modal_danger fade" id="naCekanjeModal">
											<div class="modal-dialog">
												<div class="modal-content material-modal__content">
													<div class="modal-header material-modal__header">
														<button class="close material-modal__close" data-dismiss="modal">&times;</button>
														<h4 class="modal-title material-modal__title">Pošalji kandidata na <i>NA ČEKANJE</i></h4>
													</div>
													<div class="modal-body material-modal__body">
														<p>Status kandidata je "Na čekanju"!</p>
													</div>
													<div class="modal-footer material-modal__footer">
														<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
														<a id="naCekanje_link" href=""><button class="btn btn-primary material-btn material-btn_danger">POTVRDI</button></a>
													</div>
												</div>
											</div>
										</div>
			<!-- Modal -->
			<div class="modal material-modal material-modal_danger fade" id="archiveModal">
				<div class="modal-dialog">
					<div class="modal-content material-modal__content">
						<div class="modal-header material-modal__header">
							<button class="close material-modal__close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title material-modal__title">Storniranje</h4>
						</div>
						<div class="modal-body material-modal__body">
							<p>Jeste li sigurni da želite stornirati DAK kandidata?</p>
						</div>
						<div class="modal-footer material-modal__footer">
							<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
							<a id="archive_link" href=""><button class="btn btn-primary material-btn material-btn_danger">Storniraj</button></a>
						</div>
					</div>
				</div>
			</div>
			<!-- Modal change status-->
			<div class="modal material-modal material-modal_primary fade" id="change_statusModal">
				<div class="modal-dialog">
					<div class="modal-content material-modal__content">
						<div class="modal-header material-modal__header">
							<button class="close material-modal__close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title material-modal__title">Promjeni status</h4>
						</div>
						<div class="modal-body material-modal__body">
							<p>Jeste li sigurni da želite promjeniti status DAK kandidata?</p>
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
								<li><a href="#documents" class="material-tabs__tab-link" data-toggle="tab">Dokumenti</a></li>
								<li><a href="#notes" class="material-tabs__tab-link" data-toggle="tab">Bilješke</a></li>
								<li><a href="#poslodavci" class="material-tabs__tab-link" data-toggle="tab">Poslodavci</a></li>
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
													<a href="dak?page=edit&id=<?php echo $id_dak_kandidat; ?>" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-pencil" aria-hidden="true"></i> <span>Edit</span></a>
												</div>
											</div>

											<div class="row">
												<strong class="col-sm-4 text-left"><i class="fa fa-user-o" aria-hidden="true"></i> Naziv:</strong>
												<div class="col-sm-8"><?php echo $name_dak_kandidat; ?> <?php echo $lastname_dak_kandidat; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-left"><i class="fa fa-home" aria-hidden="true"></i> Poslovnica:</strong>
												<div class="col-sm-8"><?php echo $poslovnica; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-left"><i class="fa fa-home" aria-hidden="true"></i> Porijeklo:</strong>
												<div class="col-sm-8"><?php echo $origin_dak_kandidat; ?></div>
											</div>
											<?php if($tipunosa_dak_kandidat == 0){ ?>
											<div class="row">
												<strong class="col-sm-4 text-left"><i class="fa fa-moon-o" aria-hidden="true"></i> Period za poziv:</strong>
												<div class="col-sm-8"><?php echo $daypart; ?></div>
											</div>
											<?php } ?>
											<div class="row">
												<strong class="col-sm-4 text-left"><i class="fa fa-calendar-plus-o" aria-hidden="true"></i> Datum početka rada:</strong>
												<div class="col-sm-8"><?php echo $pocetakrada_dak_kandidat; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-left"><i class="fa fa-question-circle-o" aria-hidden="true"></i> Ostale informacije:</strong>
												<div class="col-sm-8"><?php echo $comment_dak_kandidat; ?></div>
											</div>
										</div>
										<div class="col-md-6">
											<div class="row">
												<div class="col-sm-4">
													<h5><i class="fa fa-mobile" aria-hidden="true"></i> Telefon</h5>
												</div>
												<div class="col-sm-8">
													<h4><?php echo $tel_dak_kandidat;?></h4>
												</div>
											</div>
											<div class="row">
												<div class="col-sm-4">
													<h5><i class="fa fa-envelope-o" aria-hidden="true"></i> E-mail</h5>
												</div>
												<div class="col-sm-8">
													<h4><?php echo $email_dak_kandidat;?></h4>
												</div>
											</div>
											<div class="row">
												<div class="col-sm-4">
													<h5><i class="fa fa-user" aria-hidden="true"></i> Menadžer</h5>
												</div>
												<div class="col-sm-4">
													<h4><?php echo $manager_dak_kandidat;?></h4>
												</div>
												<div class="col-sm-4">
													<a href="#" data="" data-toggle="modal" data-target="#managerModal" class="change_manager material-dropdown-menu__link"><button type="button" class="btn material-btn  material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-pencil" aria-hidden="true"></i></button></a>
												</div>
												<!-- Modal change status-->
												<div class="modal material-modal material-modal_primary fade" id="managerModal">
													<div class="modal-dialog">
														<div class="modal-content material-modal__content">
															<div class="modal-header material-modal__header">
																<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																<h4 class="modal-title material-modal__title">Promjeni menadžera</h4>
															</div>
															<div class="modal-body material-modal__body">
																<form action="<?php getSiteURL(); ?>dak.php?page=change_manager" enctype="multipart/form-data" method="post" role="form" class="form-horizontal" id = "forma_manager">
																	<input type="hidden" name="manager_id_dak" value="<?php echo $id_dak_kandidat; ?>" />
																	<div class="form-group">
																		<div class="col-md-offset-2 col-sm-8">
																			<div class="">
																				<select class="selectpicker" id="new_manager" name="new_manager" data-live-search="true" required>
																					<option value=""></option>
																					<?php
																						$select_query = $db->prepare("
																											SELECT employee_id, employee_firstname, employee_lastname
																											FROM idk_employees
																											WHERE employee_status != :employee_status");
																						$select_query->execute(array(
																										':employee_status' => 0));
																										print_r($select_query->errorInfo());
																						while($select_row = $select_query->fetch()) {
																							echo "<option value='" . $select_row['employee_id'] . "'>" . $select_row['employee_firstname'] . " " . $select_row['employee_lastname'] . "</option>";

																						}
																					?>
																				</select>
																			</div>
																		</div>
																	</div>
																</form>
															</div>
															<div class="modal-footer material-modal__footer">
																<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
																<button type="submit" form="forma_manager" class="btn btn-primary material-btn material-btn_primary">PROMJENI</button>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									</hr>
									<div class="row idk_employee_info">
										<div class="col-md-6">
											<div class="row">
												<div class="col-sm-9">
													<h5>Log statusa</h5>
												</div>
											</div>
											<div class="row">
												<strong class="col-sm-5 text-left"><i class="fa fa-calendar" aria-hidden="true"></i> Datum unosa:</strong>
												<div class="col-sm-7"><?php echo $datumunosa_dak_kandidat; ?></div>
											</div>
											<?php if($datumobrade_dak_kandidat != NULL){ ?>
											<div class="row">
												<strong class="col-sm-5 text-left"><i class="fa fa-calendar-o" aria-hidden="true"></i> Datum ulaska u obradu:</strong>
												<div class="col-sm-7"><?php echo $datumobrade_dak_kandidat_f; ?></div>
											</div>
											<?php } ?>
											<?php if($datumslanje_dak_kandidat != NULL){ ?>
											<div class="row">
												<strong class="col-sm-5 text-left"><i class="fa fa-calendar-o" aria-hidden="true"></i> Datum slanja kandidata:</strong>
												<div class="col-sm-7"><?php echo $datumslanja_dak_kandidat_f; ?></div>
											</div>
											<?php } ?>
											<?php if($datumaktivacije_dak_kandidat != NULL){ ?>
											<div class="row">
												<strong class="col-sm-5 text-left"><i class="fa fa-calendar-plus-o" aria-hidden="true"></i> Datum aktivacije:</strong>
												<div class="col-sm-7"><?php echo $datumaktivacije_dak_kandidat_f; ?></div>
											</div>
											<?php } ?>
											<?php if($datumzavrsetka_dak_kandidat != NULL){ ?>
											<div class="row">
												<strong class="col-sm-5 text-left"><i class="fa fa-calendar-check-o" aria-hidden="true"></i> Datum završetka:</strong>
												<div class="col-sm-7"><?php echo $datumzavrsetka_dak_kandidat_f; ?></div>
											</div>
											<?php } ?>
											<?php if($datumstornoaktivacije_dak_kandidat != NULL){ ?>
											<div class="row">
												<strong class="col-sm-5 text-left"><i class="fa fa-calendar-minus-o" aria-hidden="true"></i> Datum stornacije nakon aktivacije:</strong>
												<div class="col-sm-7"><?php echo $datumstornoaktivacije_dak_kandidat_f; ?></div>
											</div>
											<?php } ?>
											<?php if($datumstornacije_dak_kandidat != NULL){ ?>
											<div class="row">
												<strong class="col-sm-5 text-left"><i class="fa fa-calendar-times-o" aria-hidden="true"></i> Datum stornacije:</strong>
												<div class="col-sm-7"><?php echo $datumstornacije_dak_kandidat_f; ?></div>
											</div>
											<?php } ?>
										</div>
									</div>
								</div>
								<!-- Document Start-->
								<div class="tab-pane fade" id="documents">
									<ul class="list-inline text-right">
										<li><a href="#" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" data-toggle="modal" data-target="#dodaj_document_DAK_kandidat"><i class="fa fa-file-text-o" aria-hidden="true"></i> <span>Dodaj dokument</span></a></li>
										<!-- Modal add document -->
										<div class="modal material-modal material-modal_success fade text-left" id="dodaj_document_DAK_kandidat">
											<div class="modal-dialog modal-lg">
												<div class="modal-content material-modal__content">
													<div class="modal-header material-modal__header">
														<button class="close material-modal__close" data-dismiss="modal">&times;</button>
														<h4 class="modal-title material-modal__title"><i style = "margin-right: 10px;" class="fa fa-file" aria-hidden="true"></i> Dodaj dokument</h4>
													</div>
													<div class="modal-body material-modal__body">
														<form action="<?php getSiteURL(); ?>dak.php?page=dodaj_document_DAK_kandidat_new" enctype="multipart/form-data" method="post" role="form" class="form-horizontal" id = "forma_dodaj_document_DAK_kandidat">
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
															<input type="hidden" name="document_DAK_kandidat_id_open" value="<?php echo $id_dak_kandidat; ?>" />
															<div class="form-group">
																<div class="col-md-offset-2 col-sm-8">
																	<div class="">
																		<select class="selectpicker" id="document_DAK_kandidat_vrsta" name="document_DAK_kandidat_vrsta" required>
																			<option  selected="true" disabled="disabled">Odaberite vrstu dokumenta</option>
																			<option value="1">Pasoš</option>
																			<option value="2">Ugovor</option>
																			<option value="3">Ostalo</option>
																		</select>
																	</div>
																</div>
															</div>
															<div class="ostatak_doc_info" style = "display:none;">
																<div class="form-group">
																	<div class="col-md-offset-2 col-sm-8">
																		<div class="form-group materail-input-block materail-input-block_success">
																			<input type="text" class="form-control materail-input" name="document_desc" id="document_desc" placeholder="Opis dokumenta">
																			<span class="materail-input-block__line"></span>
																		</div>
																	</div>
																</div>
															</div>
															<div class="form-group">
																<div class="col-md-offset-2 col-sm-8">
																	<div class="fileinput fileinput-new" data-provides="fileinput">
																		<span class="btn btn-default btn-file">
																			<span class="fileinput-new">
																				Izaberi dokument
																			</span>
																			<span class="fileinput-exists">
																				Promijeni
																			</span>
																			<input type="file" name="document_DAK_kandidat_file" id="document_DAK_kandidat_file">
																		</span>
																		<i style="padding-top: 5px; padding-left: 10px; padding-right: 10px;" class="fa fa-question-circle fa-lg" data-toggle="tooltip" data-placement="right" title="Napomena: Dokument ne smije biti veći od 20MB! - Dozvoljeni formati: jpg, jpeg, pdf, doc, docx, xls, xlsx, txt, ppt, pptx, png, csv" aria-hidden="true">
																		</i>
																		<span style="padding-top: 5px; padding-left: 10px; padding-right: 10px; word-break: break-all;" class="fileinput-filename">
																		</span>
																		<a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="padding-top: 5px; padding-left: 10px; opacity: 0.8; color: red;">
																			<i class="fa fa-times-circle" aria-hidden="true">
																			</i>
																		</a>
																		<script>
																			$( document ).ready(function() {
																				$('#document_DAK_kandidat_vrsta').on('change', function() {
																				  if($("#document_DAK_kandidat_vrsta").val() == '3'){
																					$(".ostatak_doc_info").css("display", "block");
																				  }else{
																					$(".ostatak_doc_info").css("display", "none");
																				  }
																				})
																			});
																			$(function (){
																				$('#document_DAK_kandidat_file').change(function (){
																					if($('#document_DAK_kandidat_file').val() !== ""){

																						var ext = $('#document_DAK_kandidat_file').val().split('.').pop().toLowerCase();

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
																					}
																				})
																			});
																		</script>
																	</div>
																</div>
															</div>
															<div class="modal-footer material-modal__footer">
																<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
																<button type="submit" class="btn btn-primary material-btn material-btn_success" form="forma_dodaj_document_DAK_kandidat"><i class="fa fa-check-square-o" aria-hidden="true"></i> Dodaj</button>
															</div>
														</form>
													</div>
												</div>
											</div>
										</div>
										<!-- Modal add document end -->
									</ul>
									<hr>

									<style>
										.kartica_dokumenti{
											text-align: center;
											color: floralwhite;;
											padding: 1.25rem;
											border-radius: 1.25rem;
											margin: 0 1.25rem;
											box-shadow: 0 2px 10px 0px black;
											-webkit-box-shadow: 0 2px 10px 0px black;
											-moz-box-shadow: 0 2px 10px 0px black;
											background: -webkit-linear-gradient(-45deg, rgb(104, 195, 104, 1) 1%,rgb(0, 58, 16, 1) 100%);
											background: linear-gradient(135deg, rgb(104, 195, 104, 1) 1%,rgb(0, 58, 16, 1) 100%);
											filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#0098d8', endColorstr='#0053b0',GradientType=1 );
											cursor: pointer;
											position: relative;
											top: 0px;
											transition: all 0.3s ease-in-out;

										}
										.kartica_dokumenti_header{
											font-size: x-large;
											font-weight: bold;
											/*border-bottom: 1px solid white;*/
										}
										.kartica_dokumenti_body p{
											margin: 0px 0px 0px 0px;
											padding: 10px 0px 10px 0px;
											word-break: break-all;
											font-size: large;
											/*text-shadow: 0 0 14px black;*/
										}
										.kartica_dokumenti_body hr{
											border-top: 1px solid #fff;
											margin-top: 5px;
											margin-bottom: 5px;
										}
										.angry-animate{
										-webkit-animation:bounce-in 2s ease-in-out 0s 1 normal;
										-moz-animation:bounce-in 2s ease-in-out 0s 1 normal;
										-ms-animation:bounce-in 2s ease-in-out 0s 1 normal;
										animation:bounce-in 2s ease-in-out 0s 1 normal;
										}

										@-webkit-keyframes bounce-in {
										0%{ opacity: 0; -webkit-transform: scale(.3); transform: scale(.3); }
										50%{ opacity: 1; -webkit-transform: scale(1.0); transform: scale(1.0); }
										70%{ -webkit-transform: scale(0.9); transform: scale(0.9); }
										100%{ -webkit-transform: scale(1); transform: scale(1); }
										}

										@keyframes bounce-in {
										0%{ opacity: 0; transform: scale(.3); }
										50%{ opacity: 1; transform: scale(1.0); }
										70%{ transform: scale(0.9); }
										100%{ transform: scale(1); }
										}
										.obrisi_margin{
											margin-top: 20px;
										}
									</style>
									<?php
										if($passport_dak_kandidat != NULL){

											if (strpos($passport_dak_kandidat, '.jpg') != false OR strpos($passport_dak_kandidat, '.png') != false){
												$passport_dokument_icon = '<i class="fa fa-file-image-o fa-3x" aria-hidden="true"></i>';
											}else{
												$passport_dokument_icon = '<i class="fa fa-file-pdf-o fa-3x" aria-hidden="true"></i>';
											}
											//$passport_dokument_download = '<a href="'.getSiteUrlr().'files/dokumenti_tiketi/'.$passport_dak_kandidat.'" class="btn material-btn material-btn_success main-container__column" style = "padding: 5px;" target="_BLANK">'.$passport_dokument_icon.'</a>';
											$passport_dokument_download = '<a href="online-viza/files/'.$passport_dak_kandidat.'" class="btn material-btn material-btn_success main-container__column" style = "padding: 5px;" target="_BLANK">'.$passport_dokument_icon.'</a>';

										}
										else{
											$passport_dokument_download = '<span style = "margin-bottom: 27px;" class="label label-primary material-label material-label_warning main-container__column text-left">Nije priložen pasoš.</span>';;
										}
										if($contract_dak_kandidat != NULL){

											if (strpos($contract_dak_kandidat, '.jpg') != false OR strpos($contract_dak_kandidat, '.png') != false){
												$contract_dokument_icon = '<i class="fa fa-file-image-o fa-3x" aria-hidden="true"></i>';
											}else{
												$contract_dokument_icon = '<i class="fa fa-file-pdf-o fa-3x" aria-hidden="true"></i>';
											}
											//$passport_dokument_download = '<a href="'.getSiteUrlr().'files/dokumenti_tiketi/'.$passport_dak_kandidat.'" class="btn material-btn material-btn_success main-container__column" style = "padding: 5px;" target="_BLANK">'.$contract_dokument_icon.'</a>';
											$contract_dokument_download = '<a href="online-viza/files/'.$contract_dak_kandidat.'" class="btn material-btn material-btn_success main-container__column" style = "padding: 5px;" target="_BLANK">'.$contract_dokument_icon.'</a>';

										}
										else{
											$contract_dokument_download = '<span style = "margin-bottom: 27px;" class="label label-primary material-label material-label_warning main-container__column text-left">Nije priložen ugovor.</span>';
										}
									?>
									<div class="row">
										<div class="col-md-12"  style = "margin-top: 10px;">
											<div class="content_box" style = "min-height: 1px;">
												<div class="row">
													<div class="col-md-6 text-center">
														<div class="content_box" style = "min-height: 1px;">
															<div class = "kartica_dokumenti text-center angry-animate">
																<img src="https://wwtcm.com/images/2.svg" style="
																	position: absolute;
																	bottom: 17px;
																	right: -4px;
																	height: 100%;
																	opacity: 0.06;
																">
																<div class = "kartica_dokumenti_header">
																	<p>
																		Pasoš
																	</p>
																</div>
																<div class = "kartica_dokumenti_body">
																	<hr>
																	<div class = "row">
																		<div class = "col-xs-6 text-center">
																			<p>File
																			</p>
																		</div>
																		<div class = "col-xs-6 text-center">
																			<p>Datum postavljanja
																			</p>
																		</div>
																	</div>
																	<div class = "row">
																		<div class = "col-xs-6 text-center">
																			<p><?php echo $passport_dokument_download; ?>
																			</p>
																		</div>
																		<div class = "col-xs-6 text-center">
																			<p><?php echo $passporttime_dak_kandidat; ?>
																			</p>
																		</div>
																	</div>
																	<hr>
																	<?php if($passport_dak_kandidat != NULL){?>
																	<div class = "row obrisi_margin">
																		<div class = "col-xs-12 text-center">
																			<a href="" data-toggle="modal" data-target="#modal_obrisi_pasos_DAK_kandidat" class="btn material-btn material-btn-icon-danger material-btn_danger main-container__column material-btn-icon-responsive">
																				<i style = "padding-right: 10px;" class="fa fa-trash" aria-hidden="true">
																				</i>
																				Obriši pasoš
																			</a>
																		</div>
																	</div>
																	<?php } ?>
																</div>
															</div>
														</div>
													</div>
													<div class="col-md-6 text-center">
														<div class="content_box" style = "min-height: 1px;">
															<div class = "kartica_dokumenti text-center angry-animate">
																<img src="https://wwtcm.com/images/2.svg" style="
																	position: absolute;
																	bottom: 17px;
																	right: -4px;
																	height: 100%;
																	opacity: 0.06;
																">
																<div class = "kartica_dokumenti_header">
																	<p>
																		Ugovor
																	</p>
																</div>
																<div class = "kartica_dokumenti_body">
																	<hr>
																	<div class = "row">
																		<div class = "col-xs-6 text-center">
																			<p>File
																			</p>
																		</div>
																		<div class = "col-xs-6 text-center">
																			<p>Datum postavljanja
																			</p>
																		</div>
																	</div>
																	<div class = "row">
																		<div class = "col-xs-6 text-center">
																			<p><?php echo $contract_dokument_download; ?>
																			</p>
																		</div>
																		<div class = "col-xs-6 text-center">
																			<p><?php echo $contracttime_dak_kandidat; ?>
																			</p>
																		</div>
																	</div>
																	<hr>
																	<?php if($contract_dak_kandidat != NULL){?>
																	<div class = "row obrisi_margin">
																		<div class = "col-xs-12 text-center">
																			<a href="" data-toggle="modal" data-target="#modal_obrisi_ugovor_DAK_kandidat" class="btn material-btn material-btn-icon-danger material-btn_danger main-container__column material-btn-icon-responsive">
																				<i style = "padding-right: 10px;" class="fa fa-trash" aria-hidden="true">
																				</i>
																				Obriši ugovor
																			</a>
																		</div>
																	</div>
																	<?php } ?>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-12"  style = "margin-top: 10px;">
											<div class="content_box" style = "min-height: 1px;">
												<h1 class="text-center"> OSTALO </h1>
												<hr>
												<div class="row">
												<?php
													$query_docs = $db->prepare("
																	SELECT *
																	FROM idk_documents
																	WHERE document_group = :document_group AND document_dataid = :document_dataid");

													$query_docs->execute(array(
																':document_dataid' => $id_dak_kandidat,
																':document_group' => 7
																));

													while($row_docs = $query_docs->fetch()){

													if($row_docs["document_file"] != NULL){

														if (strpos($row_docs["document_file"], '.jpg') != false OR strpos($row_docs["document_file"], '.png') != false){
															$dokument_icon = '<i class="fa fa-file-image-o fa-3x" aria-hidden="true"></i>';
														}else{
															$dokument_icon = '<i class="fa fa-file-pdf-o fa-3x" aria-hidden="true"></i>';
														}
														//$passport_dokument_download = '<a href="'.getSiteUrlr().'files/dokumenti_tiketi/'.$passport_dak_kandidat.'" class="btn material-btn material-btn_success main-container__column" style = "padding: 5px;" target="_BLANK">'.$contract_dokument_icon.'</a>';
														$dokument_download = '<a href="/files/dak_doc/'.$row_docs["document_file"].'" class="btn material-btn material-btn_success main-container__column" style = "padding: 5px;" target="_BLANK">'.$dokument_icon.'</a>';

													}
													else{
														$dokument_download = '<span style = "margin-bottom: 27px;" class="label label-primary material-label material-label_warning main-container__column text-left">Nije priložen ugovor.</span>';
													}

												?>
													<div class="col-md-12 text-center">
														<div class="content_box" style = "min-height: 1px;">
															<div class = "kartica_dokumenti text-center angry-animate">
																<img src="https://wwtcm.com/images/2.svg" style="
																	position: absolute;
																	bottom: 17px;
																	right: -4px;
																	height: 100%;
																	opacity: 0.06;
																">
																<div class = "kartica_dokumenti_header">
																	<p>
																		<?php echo $row_docs["document_desc"]; ?>
																	</p>
																</div>
																<div class = "kartica_dokumenti_body">
																	<hr>
																	<div class = "row">
																		<div class = "col-xs-6 text-center">
																			<p>File
																			</p>
																		</div>
																		<div class = "col-xs-6 text-center">
																			<p>Datum postavljanja
																			</p>
																		</div>
																	</div>
																	<div class = "row">
																		<div class = "col-xs-6 text-center">
																			<p><?php echo $dokument_download; ?>
																			</p>
																		</div>
																		<div class = "col-xs-6 text-center">
																			<p><?php echo $row_docs["document_datetime"];; ?>
																			</p>
																		</div>
																	</div>
																	<hr>
																	<?php if($row_docs["document_file"] != NULL){?>
																	<div class = "row obrisi_margin">
																		<div class = "col-xs-12 text-center">
																			<a href="<?php getSiteURL(); ?>dak.php?page=obrisi_docs&id=<?php echo $row_docs["document_id"]; ?>" class="btn material-btn material-btn-icon-danger material-btn_danger main-container__column material-btn-icon-responsive">
																				<i style = "padding-right: 10px;" class="fa fa-trash" aria-hidden="true">
																				</i>
																				Obriši file
																			</a>
																		</div>
																	</div>
																	<?php } ?>
																</div>
															</div>
														</div>
													</div>
													<?php
													}
													?>
												</div>
											</div>
										</div>
									</div>
									<!-- Modal obrisi pasos START -->
										<div class="modal material-modal material-modal_danger fade" id="modal_obrisi_pasos_DAK_kandidat">
											<div class="modal-dialog">
												<div class="modal-content material-modal__content">
													<div class="modal-header material-modal__header">
														<button class="close material-modal__close" data-dismiss="modal">&times;</button>
														<h4 class="modal-title material-modal__title">Brisanje pasoša</h4>
													</div>
													<div class="modal-body material-modal__body">
														<div class = "row">
															<div class="col-md-8 col-md-offset-2">
																<form action="<?php getSiteURL(); ?>dak.php?page=obrisi_pasos_DAK_kandidat" method="post" role="form" class="form-horizontal" id = "forma_obrisi_pasos_DAK_kandidat">
																	<input type="hidden" name="document_DAK_pass_kandidat_id_open" value="<?php echo $id_dak_kandidat; ?>" />
																	<div class = "form-group">
																		Jeste li sigurni da želite obrisati pasoš?
																	</div>
																	<div class="modal-footer material-modal__footer">
																		<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
																		<button type="submit" class="btn btn-primary material-btn material-btn_danger" form="forma_obrisi_pasos_DAK_kandidat">
																			<i style = "padding-right: 10px;" class="fa fa-trash" aria-hidden="true">
																			</i>
																			Završi
																		</button>
																	</div>
																</form>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									<!-- Modal obrisi pasos END -->

									<!-- Modal obrisi ugovor START -->
										<div class="modal material-modal material-modal_danger fade" id="modal_obrisi_ugovor_DAK_kandidat">
											<div class="modal-dialog">
												<div class="modal-content material-modal__content">
													<div class="modal-header material-modal__header">
														<button class="close material-modal__close" data-dismiss="modal">&times;</button>
														<h4 class="modal-title material-modal__title">Brisanje ugovora</h4>
													</div>
													<div class="modal-body material-modal__body">
														<div class = "row">
															<div class="col-md-8 col-md-offset-2">
																<form action="<?php getSiteURL(); ?>dak.php?page=obrisi_ugovor_DAK_kandidat" method="post" role="form" class="form-horizontal" id = "forma_obrisi_ugovor_DAK_kandidat">
																	<input type="hidden" name="document_DAK_cont_kandidat_id_open" value="<?php echo $id_dak_kandidat; ?>" />
																	<div class = "form-group">
																		Jeste li sigurni da želite obrisati ugovor?
																	</div>
																	<div class="modal-footer material-modal__footer">
																		<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
																		<button type="submit" class="btn btn-primary material-btn material-btn_danger" form="forma_obrisi_ugovor_DAK_kandidat">
																			<i style = "padding-right: 10px;" class="fa fa-trash" aria-hidden="true">
																			</i>
																			Završi
																		</button>
																	</div>
																</form>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									<!-- Modal obrisi ugovor END -->
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
														<form action="<?php getSiteURL(); ?>do.php?form=add_dak_note" method="post" role="form" class="form-horizontal">
															<input type="hidden" name="note_dataid" value="<?php echo $id_dak_kandidat; ?>" />
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
															':note_dataid' => $id_dak_kandidat,
															':note_group' => 7));

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
																		':note_group' => 7,
																		':note_dataid' => $id_dak_kandidat));

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
															<a href="#" data="<?php getSiteURL(); ?>dak?page=del_note&id=<?php echo $note_id; ?>" data-toggle="modal" data-target="#deleteNoteModal" class="delete_note btn material-btn material-btn_danger main-container__column"><i class="fa fa-trash-o fa-lg" aria-hidden="true"></i></a>
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
								<!-- Document END -->
								<!-- POSLODAVCI START -->
								<div class="tab-pane fade" id="poslodavci">
									<div class="panel-group material-accordion material-accordion_primary" id="accordion1">
										<?php
											$company_query = $db->prepare("
															SELECT company_name, company_address, company_address, company_zipcode, company_city, company_country, company_contact_type
															FROM idk_companies
															WHERE company_id = $company_dak_kandidat");

											$company_query->execute();

												$row = $company_query->fetch();
												$company_name = $row['company_name'];
												$company_address = $row['company_address'];
												$company_zipcode = $row['company_zipcode'];
												$company_city = $row['company_city'];
												$company_country = $row['company_country'];
												$company_contact_type = $row['company_contact_type'];

										?>
										<div class="row idk_employee_info">
											<div class="col-md-6">
												<div class="row">
													<div class="col-sm-9">
														<h5>Poslodavci</h5>
													</div>
												</div>

												<div class="row">
													<strong class="col-sm-4 text-left"><i class="fa fa-building-o" aria-hidden="true"></i> Naziv kompanije:</strong>
													<div class="col-sm-8"><?php echo $company_name; ?></div>
												</div>
												<div class="row">
													<strong class="col-sm-4 text-left"><i class="fa fa-address-card-o" aria-hidden="true"></i> Adresa kompanije:</strong>
													<div class="col-sm-8"><?php echo "".$company_address." ".$company_zipcode." ".$company_city.", ".$company_country.""; ?></div>
												</div>
												<div class="row">
													<strong class="col-sm-4 text-left"><i class="fa fa-question-circle-o" aria-hidden="true"></i> Status kompanije:</strong>
													<div class="col-sm-8"><?php echo $company_contact_type; ?></div>
												</div>
											</div>
											<?php
												$company_info_query = $db->prepare("
																SELECT *
																FROM idk_companies_info
																WHERE comi_companyid = $company_dak_kandidat");

												$company_info_query->execute();

													while($info_row = $company_info_query->fetch()){

														$comi_group = $info_row['comi_group'];
														if($comi_group == 1){
															$company_phone = $info_row['comi_data'];
														}elseif($comi_group == 2){
															$company_email = $info_row['comi_data'];
														}elseif($comi_group == 3){
															$company_web = $info_row['comi_data'];
														}else{

														}
													}
											?>
											<div class="col-md-6">
											<?php if($company_phone != NULL){ ?>
												<div class="row">
													<div class="col-sm-4">
														<h5><i class="fa fa-mobile" aria-hidden="true"></i> Telefon</h5>
													</div>
													<div class="col-sm-8">
														<h4><?php echo $company_phone;?></h4>
													</div>
												</div>
											<?php } ?>
											<?php if($company_email != NULL){ ?>
												<div class="row">
													<div class="col-sm-4">
														<h5><i class="fa fa-envelope-o" aria-hidden="true"></i> E-mail</h5>
													</div>
													<div class="col-sm-8">
														<h4><?php echo $company_email;?></h4>
													</div>
												</div>
											<?php } ?>
											<?php if($company_web != NULL){ ?>
												<div class="row">
													<div class="col-sm-4">
														<h5><i class="fa fa-user" aria-hidden="true"></i> Web</h5>
													</div>
													<div class="col-sm-8">
														<h4><?php echo $company_web;?></h4>
													</div>
												</div>
											<?php } ?>
											</div>
										</div>
									</div>
								</div>
								<!-- POSLODAVCI END -->
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php
				break;
				case "arhiviraj_kandidata":
					if((in_array( "3" , $getEmployeeStatus)) OR (in_array( "5" , $getEmployeeStatus)) OR (in_array( "2" , $employee_supervizor)) OR (in_array( "1" , $getEmployeeStatus))){
						$kandidat_id = $_GET['id'];
						$tip = $_GET['tip'];
						
						
						$query = $db->prepare("
										UPDATE idk_dak_kandidati
										SET status_dak_kandidat = :status_dak_kandidat
										WHERE id_dak_kandidat = :id_dak_kandidat");
						
						$query->execute(array(
									':status_dak_kandidat' => 7,
									':id_dak_kandidat' => $kandidat_id));
					

					
						$query_select = $db->prepare("
												SELECT name_dak_kandidat, lastname_dak_kandidat
												FROM idk_dak_kandidati
												WHERE id_dak_kandidat = :id_dak_kandidat");

						$query_select->execute(array(
											':id_dak_kandidat' => $kandidat_id));
					
						$row_select = $query_select->fetch();

						$name_dak_kandidat = "".$row_select['name_dak_kandidat']." ".$row_select['lastname_dak_kandidat']."";
					
					
					
						
						$log_desc = "Arhivirao dak kandidata: " . $name_dak_kandidat . "";
						$log_date = date('Y-m-d H:i:s');

						$log_query = $db->prepare("
										INSERT INTO idk_logs
											(log_employeeid, log_desc, log_date, log_type)
										VALUES
											(:log_employeeid, :log_desc, :log_date, :log_type)");

						$log_query->execute(array(
										':log_employeeid' => $logged_employee_id,
										':log_desc' => $log_desc,
										':log_type' => 7,
										':log_date' => $log_date));
						
						
						header("Location: " . getSiteURLr() . "dak?page=list&type=$tip");
					}
					
				break;
				case "del_doc":
					if((in_array( "3" , $getEmployeeStatus)) OR (in_array( "5" , $getEmployeeStatus)) OR (in_array( "2" , $employee_supervizor)) OR (in_array( "1" , $getEmployeeStatus))){

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
											(log_employeeid, log_desc, log_date, log_type)
										VALUES
											(:log_employeeid, :log_desc, :log_date, :log_type)");

						$log_query->execute(array(
										':log_employeeid' => $logged_employee_id,
										':log_desc' => $log_desc,
										':log_type' => 7,
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

				case "archive":
					if((in_array( "3" , $getEmployeeStatus)) OR (in_array( "5" , $getEmployeeStatus)) OR (in_array( "2" , $employee_supervizor)) OR (in_array( "1" , $getEmployeeStatus)) OR (in_array( "11" , $getEmployeeStatus))){

						$kandidat_id = $_GET['id'];
						$tip = $_GET['tip'];

						//Get
						$query_select = $db->prepare("
												SELECT name_dak_kandidat, lastname_dak_kandidat,status_dak_kandidat
												FROM idk_dak_kandidati
												WHERE id_dak_kandidat = :id_dak_kandidat");

						$query_select->execute(array(
											':id_dak_kandidat' => $kandidat_id));
					
						$row_select = $query_select->fetch();

						$name_dak_kandidat = "".$row_select['name_dak_kandidat']." ".$row_select['lastname_dak_kandidat']."";
						$status_dak_baza = $row_select['status_dak_kandidat'];
						
						if(in_array( "11" , $getEmployeeStatus)){
							if($_POST['razlog_dak_storn_select'] != 'NULL'){
								
								// NULL JER SELECT JE SADRŽAN U ISTOJ FORMI KAO I DRUGI POST
								$razlog_id = $_POST['razlog_dak_storn_select'];
								$razlog = getRazlogOdbijanja($razlog_id);
								
							}
							else{
								$razlog = $_POST['razlog_dak_storn'];
								$log_query = $db->prepare("
												INSERT INTO idk_ro_usluge
													(naziv_ro_de, tip_ro, status_ro)
												VALUES
													(:naziv_ro_de, :tip_ro, :status_ro)");

								$log_query->execute(array(
												':naziv_ro_de' => $razlog,
												':tip_ro' => 2,
												':status_ro' => 1));
							}

						}
						//Save
						$query = $db->prepare("
										UPDATE idk_dak_kandidati
										SET status_dak_kandidat = :status_dak_kandidat
										WHERE id_dak_kandidat = :id_dak_kandidat");
						if($status_dak_baza != 3){
						$query->execute(array(
									':status_dak_kandidat' => 6,
									':id_dak_kandidat' => $kandidat_id));
						}
						else {
							$query->execute(array(
									':status_dak_kandidat' => 5,
									':id_dak_kandidat' => $kandidat_id));
						}

						//Add to LOGS
						$log_desc = "Arhivirao dak kandidata: " . $name_dak_kandidat . "";
						$log_date = date('Y-m-d H:i:s');

						$log_query = $db->prepare("
										INSERT INTO idk_logs
											(log_employeeid, log_desc, log_date, log_type)
										VALUES
											(:log_employeeid, :log_desc, :log_date, :log_type)");

						$log_query->execute(array(
										':log_employeeid' => $logged_employee_id,
										':log_desc' => $log_desc,
										':log_type' => 7,
										':log_date' => $log_date));


						header("Location: " . getSiteURLr() . "dak?page=list&type=$tip");

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
				case "naCekanje":
				if((in_array( "3" , $getEmployeeStatus)) OR (in_array( "5" , $getEmployeeStatus)) OR (in_array( "2" , $employee_supervizor)) OR (in_array( "1" , $getEmployeeStatus))){
						$kandidat_id = $_GET['id'];
						$tip = $_GET['tip'];
						
						
						$query = $db->prepare("
										UPDATE idk_dak_kandidati
										SET status_dak_kandidat = :status_dak_kandidat
										WHERE id_dak_kandidat = :id_dak_kandidat");
						
						$query->execute(array(
									':status_dak_kandidat' => 8,
									':id_dak_kandidat' => $kandidat_id));
					

					
						$query_select = $db->prepare("
												SELECT name_dak_kandidat, lastname_dak_kandidat
												FROM idk_dak_kandidati
												WHERE id_dak_kandidat = :id_dak_kandidat");

						$query_select->execute(array(
											':id_dak_kandidat' => $kandidat_id));
					
						$row_select = $query_select->fetch();

						$name_dak_kandidat = "".$row_select['name_dak_kandidat']." ".$row_select['lastname_dak_kandidat']."";
					
					
					
						
						$log_desc = "Arhivirao dak kandidata: " . $name_dak_kandidat . "";
						$log_date = date('Y-m-d H:i:s');

						$log_query = $db->prepare("
										INSERT INTO idk_logs
											(log_employeeid, log_desc, log_date, log_type)
										VALUES
											(:log_employeeid, :log_desc, :log_date, :log_type)");

						$log_query->execute(array(
										':log_employeeid' => $logged_employee_id,
										':log_desc' => $log_desc,
										':log_type' => 7,
										':log_date' => $log_date));
						
						
						header("Location: " . getSiteURLr() . "dak?page=list&type=$tip");
					}
					
				break;
				case "change_status":
					if((in_array( "3" , $getEmployeeStatus)) OR (in_array( "5" , $getEmployeeStatus)) OR (in_array( "2" , $employee_supervizor)) OR (in_array( "1" , $getEmployeeStatus)) OR (in_array( "11" , $getEmployeeStatus))){

						$kandidat_id = $_GET['id'];
						$status = $_GET['status'];
						$tip = $_GET['tip'];
						// AKO JE VANJSKI SARADNIK DAK
						$isBingo = $_GET['bingo'];

						if($status == 0){
							$status_text = 'Novi';
							$datumaktivacije_dak_kandidat = NULL;
						}if($status == 1){
							$status_text = 'U obradi';
							$datumobrade_dak_kandidat = date('Y-m-d H:i:s');
						}elseif($status == 2){
							$status_text = 'Poslan';
							$datumslanja_dak_kandidat = date('Y-m-d H:i:s');
						}elseif($status == 3){
							$status_text = 'Aktivan';
							$datumaktivacije_dak_kandidat = date('Y-m-d H:i:s');
						}elseif($status == 4){
							$status_text = 'Zavrsen';
							$datumzavrsetka_dak_kandidat = date('Y-m-d H:i:s');
						}elseif($status == 5){
							$status_text = 'Storniran poslije aktivacije';
							$datumstornoaktivacije_dak_kandidat = date('Y-m-d H:i:s');
						}elseif($status == 6){
							$status_text = 'Storniran';
							$datumstorno_dak_kandidat = date('Y-m-d H:i:s');
						}elseif($status == 7){
							$status_text = 'Arhiviran';
							$datumarhive_dak_kandidat = date('Y-m-d H:i:s');
						}elseif($status == 8){
							$status_text = 'Na čekanju';
							$datumcekanje_dak_kandidat = date('Y-m-d H:i:s');
						}

						//Get
						$query_select = $db->prepare("
												SELECT name_dak_kandidat, lastname_dak_kandidat
												FROM idk_dak_kandidati
												WHERE id_dak_kandidat = :id_dak_kandidat");

						$query_select->execute(array(
											':id_dak_kandidat' => $kandidat_id));

						$row_select = $query_select->fetch();

						$name_dak_kandidat = "".$row_select['name_dak_kandidat']." ".$row_select['lastname_dak_kandidat']."";


						/** UKOLIKO JE VANJSKI SARADNIK **
						*
						*	AKTIVIRAO ILI POSTAVIO KANDIDATA NA ČEKANJE
						*   OBAVJEŠTAVA SE REFERENT ZA DAK
						*	21.11.2023 obrisano slanje maila jer se nije nikad koristilo
						*********** MAILER ***************
						**/
						

						//Save
						if($status == 1){
							$query = $db->prepare("
										UPDATE idk_dak_kandidati
										SET status_dak_kandidat = :status_dak_kandidat, datumobrade_dak_kandidat = :datumobrade_dak_kandidat
										WHERE id_dak_kandidat = :id_dak_kandidat");

								$query->execute(array(
									':status_dak_kandidat' => $status,
									':datumobrade_dak_kandidat' => $datumobrade_dak_kandidat,
									':id_dak_kandidat' => $kandidat_id));
						}
						elseif($status == 2){
								$query = $db->prepare("
										UPDATE idk_dak_kandidati
										SET status_dak_kandidat = :status_dak_kandidat, datumslanja_dak_kandidat = :datumslanja_dak_kandidat
										WHERE id_dak_kandidat = :id_dak_kandidat");

								$query->execute(array(
									':status_dak_kandidat' => $status,
									':datumslanja_dak_kandidat' => $datumslanja_dak_kandidat,
									':id_dak_kandidat' => $kandidat_id));
						}elseif($status == 3){
								$query = $db->prepare("
										UPDATE idk_dak_kandidati
										SET status_dak_kandidat = :status_dak_kandidat, datumaktivacije_dak_kandidat = :datumaktivacije_dak_kandidat
										WHERE id_dak_kandidat = :id_dak_kandidat");

								$query->execute(array(
									':status_dak_kandidat' => $status,
									':datumaktivacije_dak_kandidat' => $datumaktivacije_dak_kandidat,
									':id_dak_kandidat' => $kandidat_id));
						}elseif($status == 4){
								$query = $db->prepare("
										UPDATE idk_dak_kandidati
										SET status_dak_kandidat = :status_dak_kandidat, datumzavrsetka_dak_kandidat = :datumzavrsetka_dak_kandidat
										WHERE id_dak_kandidat = :id_dak_kandidat");

								$query->execute(array(
									':status_dak_kandidat' => $status,
									':datumzavrsetka_dak_kandidat' => $datumzavrsetka_dak_kandidat,
									':id_dak_kandidat' => $kandidat_id));
						}elseif($status == 5){
								$query = $db->prepare("
										UPDATE idk_dak_kandidati
										SET status_dak_kandidat = :status_dak_kandidat, datumstornoaktivacije_dak_kandidat = :datumstornoaktivacije_dak_kandidat
										WHERE id_dak_kandidat = :id_dak_kandidat");

								$query->execute(array(
									':status_dak_kandidat' => $status,
									':datumstornoaktivacije_dak_kandidat' => $datumstornoaktivacije_dak_kandidat,
									':id_dak_kandidat' => $kandidat_id));
						}elseif($status == 6){
								$query = $db->prepare("
										UPDATE idk_dak_kandidati
										SET status_dak_kandidat = :status_dak_kandidat, datumstornoaktivacije_dak_kandidat = :datumstornoaktivacije_dak_kandidat
										WHERE id_dak_kandidat = :id_dak_kandidat");

								$query->execute(array(
									':status_dak_kandidat' => $status,
									':datumstornoaktivacije_dak_kandidat' => $datumstorno_dak_kandidat,
									':id_dak_kandidat' => $kandidat_id));
						}elseif($status == 7){
								$query = $db->prepare("
										UPDATE idk_dak_kandidati
										SET status_dak_kandidat = :status_dak_kandidat, datumstornoaktivacije_dak_kandidat = :datumstornoaktivacije_dak_kandidat
										WHERE id_dak_kandidat = :id_dak_kandidat");

								$query->execute(array(
									':status_dak_kandidat' => $status,
									':datumstornoaktivacije_dak_kandidat' => $datumarhive_dak_kandidat,
									':id_dak_kandidat' => $kandidat_id));
						}elseif($status == 8){
								$query = $db->prepare("
										UPDATE idk_dak_kandidati
										SET status_dak_kandidat = :status_dak_kandidat, datumstornoaktivacije_dak_kandidat = :datumstornoaktivacije_dak_kandidat
										WHERE id_dak_kandidat = :id_dak_kandidat");

								$query->execute(array(
									':status_dak_kandidat' => $status,
									':datumstornoaktivacije_dak_kandidat' => $datumcekanje_dak_kandidat,
									':id_dak_kandidat' => $kandidat_id));
						}

						//Add to LOGS
						$log_desc = "Promjenio status dak kandidata na: " . $status_text . "" . $name_dak_kandidat . "";
						$log_date = date('Y-m-d H:i:s');

						$log_query = $db->prepare("
										INSERT INTO idk_logs
											(log_employeeid, log_desc, log_date, log_type)
										VALUES
											(:log_employeeid, :log_desc, :log_date, :log_type)");

						$log_query->execute(array(
										':log_employeeid' => $logged_employee_id,
										':log_desc' => $log_desc,
										':log_type' => 7,
										':log_date' => $log_date));


						header("Location: " . getSiteURLr() . "dak?page=open&id=$kandidat_id");

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

				case "change_manager":

				$new_manager = $_POST["new_manager"];
				$kandidat_id = $_POST["manager_id_dak"];

				//Get
				$query_select = $db->prepare("
										SELECT name_dak_kandidat, lastname_dak_kandidat
										FROM idk_dak_kandidati
										WHERE id_dak_kandidat = :id_dak_kandidat");

				$query_select->execute(array(
									':id_dak_kandidat' => $kandidat_id));

				$row_select = $query_select->fetch();

				$query = $db->prepare("
										UPDATE idk_dak_kandidati
										SET manager_dak_kandidat = :manager_dak_kandidat
										WHERE id_dak_kandidat = :id_dak_kandidat");

								$query->execute(array(
									':manager_dak_kandidat' => $new_manager,
									':id_dak_kandidat' => $kandidat_id));

				//Add to LOGS
				$log_desc = "Promjenio menadžera dak kandidata: " . $row["name_dak_kandidat"] . "";
				$log_date = date('Y-m-d H:i:s');

				$log_query = $db->prepare("
								INSERT INTO idk_logs
									(log_employeeid, log_desc, log_date, log_type)
								VALUES
									(:log_employeeid, :log_desc, :log_date, :log_type)");

				$log_query->execute(array(
								':log_employeeid' => $logged_employee_id,
								':log_desc' => $log_desc,
								':log_type' => 7,
								':log_date' => $log_date));


				header("Location: " . getSiteURLr() . "dak?page=open&id=$kandidat_id");

				break;

				case "plati_ratu":
					if((in_array( "3" , $getEmployeeStatus)) OR (in_array( "5" , $getEmployeeStatus)) OR (in_array( "2" , $employee_supervizor)) OR (in_array( "1" , $getEmployeeStatus))){

						$kandidat_id = $_GET['id'];
						$rata = $_GET['rata'];
						$tip = $_GET['tip'];
						$date = date("Y-m-d H:i:s");

						//Get
						$query_select = $db->prepare("
												SELECT name_dak_kandidat, lastname_dak_kandidat
												FROM idk_dak_kandidati
												WHERE id_dak_kandidat = :id_dak_kandidat");

						$query_select->execute(array(
											':id_dak_kandidat' => $kandidat_id));

						$row_select = $query_select->fetch();

						$name_dak_kandidat = "".$row_select['name_dak_kandidat']." ".$row_select['lastname_dak_kandidat']."";

						//SPREMI PLACENU RATU
						if($rata == 1){
							$query = $db->prepare("
											UPDATE idk_dak_kandidati
											SET rata1_dak_kandidat = :rata1_dak_kandidat, rata1_datum_dak_kandidat = :rata1_datum_dak_kandidat
											WHERE id_dak_kandidat = :id_dak_kandidat");

							$query->execute(array(
										':rata1_dak_kandidat' => 1,
										':rata1_datum_dak_kandidat' => $date,
										':id_dak_kandidat' => $kandidat_id));
						}else{
							$query = $db->prepare("
											UPDATE idk_dak_kandidati
											SET datumzavrsetka_dak_kandidat = :datumzavrsetka_dak_kandidat, status_dak_kandidat = :status_dak_kandidat, rata2_dak_kandidat = :rata2_dak_kandidat, rata2_datum_dak_kandidat = :rata2_datum_dak_kandidat
											WHERE id_dak_kandidat = :id_dak_kandidat");

							$query->execute(array(
										':datumzavrsetka_dak_kandidat' => $date, 
										':status_dak_kandidat' => 4, 
										':rata2_dak_kandidat' => 1,
										':rata2_datum_dak_kandidat' => $date,
										':id_dak_kandidat' => $kandidat_id));
						//Add to LOGS
						$log_desc = "Prešao u status završen: " . $name_dak_kandidat . "";

						$log_query = $db->prepare("
										INSERT INTO idk_logs
											(log_employeeid, log_desc, log_date, log_type)
										VALUES
											(:log_employeeid, :log_desc, :log_date, :log_type)");

						$log_query->execute(array(
										':log_employeeid' => $logged_employee_id,
										':log_desc' => $log_desc,
										':log_type' => 7,
										':log_date' => $date));
						}

						//Add to LOGS
						$log_desc = "Naplatio ratu ".$rata." dak kandidata: " . $name_dak_kandidat . "";
						$log_date = date('Y-m-d H:i:s');

						$log_query = $db->prepare("
										INSERT INTO idk_logs
											(log_employeeid, log_desc, log_date, log_type)
										VALUES
											(:log_employeeid, :log_desc, :log_date, :log_type)");

						$log_query->execute(array(
										':log_employeeid' => $logged_employee_id,
										':log_desc' => $log_desc,
										':log_type' => 7,
										':log_date' => $log_date));


						header("Location: " . getSiteURLr() . "dak?page=list&type=$tip");

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

				case "add_rucno_dak_kandidata":
					$dak_kandidat_rucno_ime_new = $_POST["dak_kandidat_rucno_ime"];
					$dak_kandidat_rucno_prezime_new = $_POST["dak_kandidat_rucno_prezime"];
					$dak_kandidat_rucno_telefon_new = $_POST["dak_kandidat_rucno_telefon"];
					$dak_kandidat_rucno_poslodavac = $_POST["dak_kandidat_rucno_poslodavac"];
					$dak_kandidat_rucno_poslovnica = $_POST["dak_kandidat_rucno_poslovnica"];
					$new_manager = $_POST["new_manager"];
					$pocetak_rada = date("Y-m-d", strtotime($_POST["pocetak_rada"]));
					$options = ['cost' => 10,];
					$dak_kandidat_rucno_telefon_hashed_new = password_hash($dak_kandidat_rucno_telefon_new, PASSWORD_BCRYPT, $options);
					$dak_kandidat_rucno_mail_new = $_POST["dak_kandidat_rucno_mail"];
					// $dak_kandidat_rucno_period_zvanja_new = $_POST["dak_kandidat_rucno_period_zvanja"];
					$dak_kandidat_rucno_komentar_new = $_POST["dak_kandidat_rucno_komentar"];
					$dak_kandidat_rucno_status_new = 0;
					$dak_kandidat_rucno_tip_unosa_new = 1;
					$dak_kandidat_rucno_vrijeme_unosa_new = date("Y-m-d H:i:s");

					$poslodavac_new_ND_cand1 = $_POST['poslodavac_new_ND_cand'];
					if($poslodavac_new_ND_cand1 == 0){
						$poslodavac_nas_new_ND_cand1 = 0;
						$poslodavac_naziv_nas_new_ND_cand1 = NULL;
						$poslodavac_naziv_new_ND_cand1 = NULL;
						$poslodavac_ulica_new_ND_cand1 = NULL;
						$poslodavac_postanski_broj_new_ND_cand1 = NULL;
						$poslodavac_grad_new_ND_cand1 = NULL;
						$poslodavac_regija_new_ND_cand1 = NULL;
						$poslodavac_drzava_new_ND_cand1 = NULL;
						$poslodavac_ime_new_ND_cand1 = NULL;
						$poslodavac_prezime_new_ND_cand1 = NULL;
						$poslodavac_mail_new_ND_cand1 = NULL;
						$poslodavac_kontakt_broj_new_ND_cand1 = NULL;
					}
					else{
						$poslodavac_nas_new_ND_cand1 = $_POST['poslodavac_nas_new_ND_cand'];
						if($poslodavac_nas_new_ND_cand1 == 0){
							$poslodavac_naziv_nas_new_ND_cand1 = NULL;
							$poslodavac_naziv_new_ND_cand1 = $_POST['poslodavac_naziv_new_ND_cand'];
							$poslodavac_ulica_new_ND_cand1 = $_POST['poslodavac_ulica_new_ND_cand'];
							$poslodavac_postanski_broj_new_ND_cand1 = $_POST['poslodavac_postanski_broj_new_ND_cand'];
							$poslodavac_grad_new_ND_cand1 = $_POST['poslodavac_grad_new_ND_cand'];
							$poslodavac_regija_new_ND_cand1 = $_POST['poslodavac_regija_new_ND_cand'];
							$poslodavac_drzava_new_ND_cand1 = $_POST['poslodavac_drzava_new_ND_cand'];
							$poslodavac_ime_new_ND_cand1 = $_POST['poslodavac_ime_new_ND_cand'];
							$poslodavac_prezime_new_ND_cand1 = $_POST['poslodavac_prezime_new_ND_cand'];
							$poslodavac_mail_new_ND_cand1 = $_POST['poslodavac_mail_new_ND_cand'];
							$poslodavac_kontakt_broj_new_ND_cand1 = $_POST['poslodavac_kontakt_new_ND_cand'];
						}
						else{
							$poslodavac_naziv_nas_new_ND_cand1 = $_POST['poslodavac_naziv_nas_new_ND_cand'];
							$poslodavac_naziv_new_ND_cand1 = NULL;
							$poslodavac_ulica_new_ND_cand1 = NULL;
							$poslodavac_postanski_broj_new_ND_cand1 = NULL;
							$poslodavac_grad_new_ND_cand1 = NULL;
							$poslodavac_regija_new_ND_cand1 = NULL;
							$poslodavac_drzava_new_ND_cand1 = NULL;
							$poslodavac_ime_new_ND_cand1 = NULL;
							$poslodavac_prezime_new_ND_cand1 = NULL;
							$poslodavac_mail_new_ND_cand1 = NULL;
							$poslodavac_kontakt_broj_new_ND_cand1 = NULL;
						}
					}

					// var_dump($dak_kandidat_rucno_ime_new." ".$dak_kandidat_rucno_prezime_new." ".$dak_kandidat_rucno_telefon_new." ".$dak_kandidat_rucno_mail_new." ".$dak_kandidat_rucno_telefon_hashed_new." ".$dak_kandidat_rucno_tip_unosa_new." ".$dak_kandidat_rucno_vrijeme_unosa_new);
					// exit();

					//Provjera kandidata da li postoji u bazi
					$provjera_kandidata_DAK = $db->prepare("
														SELECT name_dak_kandidat, lastname_dak_kandidat, tel_dak_kandidat, email_dak_kandidat
														FROM idk_dak_kandidati
														WHERE name_dak_kandidat = :name_dak_kandidat AND lastname_dak_kandidat = :lastname_dak_kandidat AND tel_dak_kandidat = :tel_dak_kandidat AND email_dak_kandidat = :email_dak_kandidat");
					$provjera_kandidata_DAK->execute(array(
														':name_dak_kandidat' => $dak_kandidat_rucno_ime_new,
														':lastname_dak_kandidat' => $dak_kandidat_rucno_prezime_new,
														':tel_dak_kandidat' => $dak_kandidat_rucno_telefon_new,
														':email_dak_kandidat' => $dak_kandidat_rucno_mail_new));
					$number_of_rows_provjera_kandidata_DAK = $provjera_kandidata_DAK->rowCount();

					if($number_of_rows_provjera_kandidata_DAK == 0){
						//Add user to db idk_dak_kandidati
						$add_DAK_kandidat_new = $db->prepare("
														INSERT INTO idk_dak_kandidati
															(origin_dak_kandidat, manager_dak_kandidat, tel_dak_kandidat, hashedtel_idk_dak_kandidat, telconfirm_dak_kandidat, name_dak_kandidat, lastname_dak_kandidat, pocetakrada_dak_kandidat,  email_dak_kandidat, poslovnica_dak_kandidat, comment_dak_kandidat, status_dak_kandidat, tipunosa_dak_kandidat, datumunosa_dak_kandidat, company_dak_kandidat, nas_klijent_dak_kandidat)
														VALUES
															(:origin_dak_kandidat, :manager_dak_kandidat, :tel_dak_kandidat, :hashedtel_idk_dak_kandidat, :name_dak_kandidat, :lastname_dak_kandidat, :pocetakrada_dak_kandidat, :email_dak_kandidat, :poslovnica_dak_kandidat, :comment_dak_kandidat, :status_dak_kandidat, :tipunosa_dak_kandidat, :datumunosa_dak_kandidat, :company_dak_kandidat, :nas_klijent_dak_kandidat)");
						$add_DAK_kandidat_new->execute(array(
														':origin_dak_kandidat' => 1,
														':manager_dak_kandidat' => $new_manager,
														':tel_dak_kandidat' => $dak_kandidat_rucno_telefon_new,
														':hashedtel_idk_dak_kandidat' => $dak_kandidat_rucno_telefon_hashed_new,
														':telconfirm_dak_kandidat' => 1,
														':name_dak_kandidat' => $dak_kandidat_rucno_ime_new,
														':lastname_dak_kandidat' => $dak_kandidat_rucno_prezime_new,
														':pocetakrada_dak_kandidat' => $pocetak_rada,
														':comment_dak_kandidat' => $dak_kandidat_rucno_komentar_new,
														':email_dak_kandidat' => $dak_kandidat_rucno_mail_new,
														':tipunosa_dak_kandidat' => $dak_kandidat_rucno_tip_unosa_new,
														':status_dak_kandidat' => $dak_kandidat_rucno_status_new,
														':company_dak_kandidat' => $poslodavac_naziv_nas_new_ND_cand1,
														':nas_klijent_dak_kandidat' => $poslodavac_nas_new_ND_cand1,
														':poslovnica_dak_kandidat' => $dak_kandidat_rucno_poslovnica,
														':datumunosa_dak_kandidat' => $dak_kandidat_rucno_vrijeme_unosa_new));

						//Get last ID
						$kandidat_id_nd = $db->lastInsertId();

						//Dodavanje u tabelu IDK_KANDIDATI START
						$kandidat_check = md5(uniqid(rand(), true));

						//Add user to db
						$query_add_user = $db->prepare("
										INSERT INTO idk_kandidati
											(
												kandidat_check,
												kandidat_ime,
												kandidat_prezime,
												kandidat_email,
												kandidat_mobitel,
												kandidat_slika,
												kandidat_status,
												kandidat_status_messenger,
												kandidat_datetime,
												kandidat_visitedurl,
												kandidat_prijava_na,
												kandidat_group,
												kandidat_porijeklo
											)
										VALUES
											(
												:kandidat_check,
												:kandidat_ime,
												:kandidat_prezime,
												:kandidat_email,
												:kandidat_mobitel,
												:kandidat_slika,
												:kandidat_status,
												:kandidat_status_messenger,
												:kandidat_datetime,
												:kandidat_visitedurl,
												:kandidat_prijava_na,
												:kandidat_group,
												:kandidat_porijeklo
											)");

						$query_add_user->execute(array(
									':kandidat_check' => $kandidat_check,
									':kandidat_ime' => $dak_kandidat_rucno_ime_new,
									':kandidat_prezime' => $dak_kandidat_rucno_prezime_new,
									':kandidat_email' => $dak_kandidat_rucno_mail_new,
									':kandidat_mobitel' => $dak_kandidat_rucno_telefon_new,
									':kandidat_slika' => "none",
									':kandidat_status' => 0,
									':kandidat_status_messenger' => 1,
									':kandidat_datetime' => date('Y-m-d H:i:s'),
									':kandidat_visitedurl' => 1,
									':kandidat_prijava_na' => "Ostalo",
									':kandidat_group' => 7,
									':kandidat_porijeklo' => 2
									));
						$kandidat_id = $db->lastInsertId();

						$query_log_status = $db->prepare("
								INSERT INTO idk_log_kandidat_statusi
									(lks_kandidat_id, lks_status_obrade, lks_status_messenger, lks_datetime)
								VALUES
									(:lks_kandidat_id, :lks_status_obrade, :lks_status_messenger, :lks_datetime)
						");

						$query_log_status->execute(array(
								':lks_kandidat_id' => $kandidat_id,
								':lks_status_obrade' => 0,
								':lks_status_messenger' => 1,
								':lks_datetime' => date('Y-m-d H:i:s')
						));

						$nalog_query = $db->prepare("
												SELECT project_id
												FROM idk_projects
												WHERE (project_name LIKE '%Kandidati sa DAK%') ");

						$nalog_query->execute();

						$nalogrow = $nalog_query->fetch();

						$project_id = $nalogrow['project_id'];
						$query_project = $db->prepare("
										INSERT INTO idk_project_kandidati
											(pk_projectid, pk_kandidatid)
										VALUES
											(:pk_projectid, :pk_kandidatid)");

						$query_project->execute(array(
										':pk_projectid' => $project_id,
										':pk_kandidatid' => $kandidat_id));

						$kki_grupa = 1;
						$kki_naziv = "Mobilni";

						//Add mobilni to db
						$query_mob1 = $db->prepare("
										INSERT INTO idk_kandidat_kontakt_info
											(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id)
										VALUES
											(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id)");

						$query_mob1->execute(array(
									':kki_grupa' => $kki_grupa,
									':kki_naziv' => $kki_naziv,
									':kki_podatak' => $dak_kandidat_rucno_telefon_new,
									':kki_kandidat_id' => $kandidat_id));

						$kki_grupa_e = 2;
						$kki_naziv_e = "E-mail";

						//Add kontakt info to db
						$query_email1 = $db->prepare("
										INSERT INTO idk_kandidat_kontakt_info
											(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id)
										VALUES
											(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id)");

						$query_email1->execute(array(
									':kki_grupa' => $kki_grupa_e,
									':kki_naziv' => $kki_naziv_e,
									':kki_podatak' => $dak_kandidat_rucno_mail_new,
									':kki_kandidat_id' => $kandidat_id));

						//Add to table users (chatbot)
						$random_string = generateRandomString();
						$options1 = [
							'cost' => 10,
						];
						$random_password = password_hash($random_string, PASSWORD_BCRYPT, $options1);

						$log_desc2 = "Kandidat: " . $dak_kandidat_rucno_ime_new . " " . $dak_kandidat_rucno_prezime_new . "(".$kandidat_id."). ; (".$random_string.")";
						$log_type2 = "5";
						addToLogs($log_desc2, $log_type2);

						$characters = '0123456789';
						$charactersLength = strlen($characters);
						$randomString = '';
						for ($i = 0; $i < 5; $i++) {
							$randomString .= $characters[rand(0, $charactersLength - 1)];
						}
						$bot_koriscnicko_ime = $dak_kandidat_rucno_ime_new.$randomString;
						$kandidat_full_name = $dak_kandidat_rucno_ime_new." ".$dak_kandidat_rucno_prezime_new;
						$query_user = $db->prepare("
									INSERT INTO users
										(phone, name, nalog_id, email, password, kandidat_id)
									VALUES
										(:phone, :name, :nalog_id, :email, :password, :kandidat_id)");

						$query_user->execute(array(
									':phone' => $dak_kandidat_rucno_telefon_new,
									':name' => $kandidat_full_name,
									':nalog_id' => 44,
									':email' => $bot_koriscnicko_ime,
									':password' => $random_password,
									':kandidat_id' => $kandidat_id));

						//INFOBIP
						// sendSmsToCandidateInfobip1($random_string, $dak_kandidat_rucno_telefon_new, "NN");
						// sleep(1);  // Seconds
						// sendSmsToCandidateInfobip2($random_string, $dak_kandidat_rucno_telefon_new, "NN");
						// sleep(1);  // Seconds
						// sendSmsToCandidateInfobip3($random_string, $dak_kandidat_rucno_telefon_new, $bot_koriscnicko_ime);
						// sleep(1);  // Seconds
						// sendSmsToCandidateInfobip4($random_string, $dak_kandidat_rucno_telefon_new, $bot_koriscnicko_ime);

						sendCandidateMessengerMail($random_string, $dak_kandidat_rucno_mail_new, $bot_koriscnicko_ime);

						//Dodavanje u tabelu IDK_KANDIDATI END

						//Update kompanije i prodaje START
							if($poslodavac_new_ND_cand1 == 1 AND $poslodavac_nas_new_ND_cand1 == 0){
								$tip_poslodavca_new_ND_cand1 = "Lead";
								//Prebacivanje u kompanije START

									$query_company = $db->prepare("
													INSERT INTO idk_companies
														(company_name, company_address, company_zipcode, company_city, company_state, company_country, company_contact_type, company_datetime, company_status, company_origin, company_reccomendation)
													VALUES
														(:company_name, :company_address, :company_zipcode, :company_city, :company_state, :company_country, :company_contact_type, :company_datetime, :company_status, :company_origin, :company_reccomendation)");

									$query_company->execute(array(
												':company_name' => $poslodavac_naziv_new_ND_cand1,
												':company_address' => $poslodavac_ulica_new_ND_cand1,
												':company_zipcode' => $poslodavac_postanski_broj_new_ND_cand1,
												':company_city' => $poslodavac_grad_new_ND_cand1,
												':company_state' => $poslodavac_regija_new_ND_cand1,
												':company_country' => $poslodavac_drzava_new_ND_cand1,
												':company_contact_type' => $tip_poslodavca_new_ND_cand1,
												':company_datetime' => date('Y-m-d H:i:s'),
												':company_status' => 1,
												':company_origin' => 2,
												':company_reccomendation' => $kandidat_id_nd
												));

									$companyid = $db->lastInsertId();

									//Ubacivanje u prodajni modul START
									$query_clients = $db->prepare("
																INSERT INTO idk_clients
																	(
																		client_name,
																		client_country,
																		client_region,
																		client_city,
																		client_address,
																		client_pp,
																		client_telephone,
																		client_email,
																		client_origin,
																		client_recommendation,
																		client_recommendation_company,
																		client_fc_or_sales,
																		client_fc_status,
																		client_sales_status
																	)
																	VALUES
																	(
																		:client_name,
																		:client_country,
																		:client_region,
																		:client_city,
																		:client_address,
																		:client_pp,
																		:client_telephone,
																		:client_email,
																		:client_origin,
																		:client_recommendation,
																		:client_recommendation_company,
																		:client_fc_or_sales,
																		:client_fc_status,
																		:client_sales_status
																	)
																	");
										$query_clients->execute(array(
														':client_name' => $poslodavac_naziv_new_ND_cand1,
														':client_country' => "DE",
														':client_region' => $poslodavac_regija_new_ND_cand1,
														':client_city' => $poslodavac_grad_new_ND_cand1,
														':client_address' => $poslodavac_ulica_new_ND_cand1,
														':client_pp' => $poslodavac_postanski_broj_new_ND_cand1,
														':client_telephone' => NULL,
														':client_email' => NULL,
														':client_origin' => 2,
														':client_recommendation' => $kandidat_id_nd,
														':client_recommendation_company' => $companyid,
														':client_fc_or_sales' => 0,
														':client_fc_status' => 0,
														':client_sales_status' => 0
														));

										$clientid = $db->lastInsertId();
										$stats_desc1 = 'Zaposlenik '.getZaposlenikimeR($logged_employee_id).' je dodao klijenta '.$poslodavac_naziv_new_ND_cand1.'. ';
										$stats_desc2 = 'Zaposlenik '.getZaposlenikimeR($logged_employee_id).' je dodao klijenta preko DAK modula '.$poslodavac_naziv_new_ND_cand1.'. ';
										
										insertClientStats($clientid, 0, 0, $stats_desc2);
									//Ubacivanje u prodajni modul END

									$query_contact = $db->prepare("
													INSERT INTO idk_contacts
														(contact_firstname, contact_lastname, contact_companyid, contact_clientid, contact_datetime, contact_status)
													VALUES
														(:contact_firstname, :contact_lastname, :contact_companyid, :contact_clientid, :contact_datetime, :contact_status)");

									$query_contact->execute(array(
												':contact_firstname' => $poslodavac_ime_new_ND_cand1,
												':contact_lastname' => $poslodavac_prezime_new_ND_cand1,
												':contact_companyid' => $companyid,
												':contact_clientid' => $clientid,
												':contact_datetime' => date('Y-m-d H:i:s'),
												':contact_status' => 1
												));

									$contactid = $db->lastInsertId();

									 //Add primary phone
									if (!empty($poslodavac_kontakt_broj_new_ND_cand1)) {

										$ci_group_t = 1;
										$ci_title_t = "Telefon";
										$ci_data_t = $poslodavac_kontakt_broj_new_ND_cand1;
										$ci_primary_t = 1;

										$query_phone = $db->prepare("
														INSERT INTO idk_contacts_info
															(ci_group, ci_title, ci_data, ci_primary, ci_contactid)
														VALUES
															(:ci_group, :ci_title, :ci_data, :ci_primary, :ci_contactid)");

										$query_phone->execute(array(
														':ci_group' => $ci_group_t,
														':ci_title' => $ci_title_t,
														':ci_data' => $ci_data_t,
														':ci_primary' => $ci_primary_t,
														':ci_contactid' => $contactid
														));
										$contact_telefon_id = $db->lastInsertId();
									}

									//Add primary email
									if (!empty($poslodavac_mail_new_ND_cand1)) {

										$ci_group = 2;
										$ci_title = "E-mail";
										$ci_data = $poslodavac_mail_new_ND_cand1;
										$ci_primary = 1;

										$query_email = $db->prepare("
														INSERT INTO idk_contacts_info
															(ci_group, ci_title, ci_data, ci_primary, ci_contactid)
														VALUES
															(:ci_group, :ci_title, :ci_data, :ci_primary, :ci_contactid)");

										$query_email->execute(array(
														':ci_group' => $ci_group,
														':ci_title' => $ci_title,
														':ci_data' => $ci_data,
														':ci_primary' => $ci_primary,
														':ci_contactid' => $contactid
														));
										$contact_email_id = $db->lastInsertId();
									}

									$kontakt_broj_email_company = " [".$contact_telefon_id.",".$contact_email_id."] ";

									//ADD TO LOGS START
									$log_date_com = date('Y-m-d H:i:s');
									$log_desc_com = "DIPL -> Dodana nova kompanija sa ID = [".$companyid."] i novi klijent sa ID = [".$clientid."] sa kontaktom ID = [".$contactid."] i kontakt podacima ID = ".$kontakt_broj_email_company." ";
									$log_query_com = $db->prepare("
													INSERT INTO idk_logs
														(log_employeeid, log_desc, log_date, log_type)
													VALUES
														(:log_employeeid, :log_desc, :log_date, :log_type)");

									$log_query_com->execute(array(
													':log_employeeid' => $logged_employee_id,
													':log_desc' => $log_desc_com,
													':log_type' => 8,
													':log_date' => $log_date_com));
									//ADD TO LOGS END
								//Prebacivanje u kompanije END
							}
							//Update kompanije i prodaje END

						header("Location: " . getSiteURLr() . "dak?page=list&type=0&mess=1");
					}
					else{
						header("Location: " . getSiteURLr() . "dak?page=list&type=1&mess=2");
					}
				break;

				case "dodaj_document_DAK_kandidat_new":
					$document_DAK_kandidat_id_open_new = $_POST['document_DAK_kandidat_id_open'];
					$document_DAK_kandidat_vrsta_new = intval($_POST['document_DAK_kandidat_vrsta']);
					$document_DAK_kandidat_file_new = $_FILES['document_DAK_kandidat_file'];

					//Generisanje imena datoteke i spremanje START
						//File properties
						$file_name = $document_DAK_kandidat_file_new['name'];
						$file_tmp = $document_DAK_kandidat_file_new['tmp_name'];

						//File extension
						$file_ext = explode('.', $file_name);
						$file_ext = strtolower(end($file_ext));
						$allowed = array('jpg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png', 'csv');

						if(in_array($file_ext, $allowed)) {

							$file_name_new = uniqid() . '.' . $file_ext;
							if($document_DAK_kandidat_vrsta_new == 3){
								$file_destination = "files/dak_doc/" . $file_name_new;
							}else{
								$file_destination = "online-viza/files/" . $file_name_new;
							}
							if(move_uploaded_file($file_tmp, $file_destination)){}
						}
					//Generisanje imena datoteke i spremanje END


					if($document_DAK_kandidat_vrsta_new == 1){
						//Dodavanje pasoša
						$add_doc_DAK_kandidat_new = $db->prepare("
													UPDATE idk_dak_kandidati
													SET passport_dak_kandidat = :passport_dak_kandidat, passporttime_dak_kandidat = :passporttime_dak_kandidat
													WHERE id_dak_kandidat = $document_DAK_kandidat_id_open_new
													");
						$add_doc_DAK_kandidat_new->execute(array(
														':passport_dak_kandidat' => $file_name_new,
														':passporttime_dak_kandidat' => date("Y-m-d H:i:s")
														));
					}else if($document_DAK_kandidat_vrsta_new == 2){
						//Dodavanje ugovora
						$add_doc_DAK_kandidat_new = $db->prepare("
													UPDATE idk_dak_kandidati
													SET contract_dak_kandidat = :contract_dak_kandidat, contracttime_dak_kandidat = :contracttime_dak_kandidat
													WHERE id_dak_kandidat = $document_DAK_kandidat_id_open_new
													");
						$add_doc_DAK_kandidat_new->execute(array(
														':contract_dak_kandidat' => $file_name_new,
														':contracttime_dak_kandidat' => date("Y-m-d H:i:s")
														));
					}else{
						$document_name = "Ostalo DAK";
						$document_desc = $_POST['document_desc'];
						$document_datetime = date('Y-m-d H:i:s');
						$document_group = 7;

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
										':document_dataid' => $document_DAK_kandidat_id_open_new,
										':document_employeeid' => $logged_employee_id));
					}

					header("Location: " . getSiteURLr() . "dak?page=open&id=".$document_DAK_kandidat_id_open_new);
				break;

				case "obrisi_pasos_DAK_kandidat":
					$obrisi_pasos_DAK_kandidat_edit = $_POST['document_DAK_pass_kandidat_id_open'];
					//Brisanje pasosa
					$del_pass_doc_DAK_kandidat_edit = $db->prepare("
														UPDATE idk_dak_kandidati
														SET passport_dak_kandidat = :passport_dak_kandidat, passporttime_dak_kandidat = :passporttime_dak_kandidat
														WHERE id_dak_kandidat = $obrisi_pasos_DAK_kandidat_edit
														");
					$del_pass_doc_DAK_kandidat_edit->execute(array(
														':passport_dak_kandidat' => NULL,
														':passporttime_dak_kandidat' => NULL
														));
					header("Location: " . getSiteURLr() . "dak?page=open&id=".$obrisi_pasos_DAK_kandidat_edit);
				break;

				case "obrisi_ugovor_DAK_kandidat":
					$obrisi_ugovor_DAK_kandidat_edit = $_POST['document_DAK_cont_kandidat_id_open'];
					//Brisanje pasosa
					$del_cont_doc_DAK_kandidat_edit = $db->prepare("
														UPDATE idk_dak_kandidati
														SET contract_dak_kandidat = :contract_dak_kandidat, contracttime_dak_kandidat = :contracttime_dak_kandidat
														WHERE id_dak_kandidat = $obrisi_ugovor_DAK_kandidat_edit
														");
					$del_cont_doc_DAK_kandidat_edit->execute(array(
														':contract_dak_kandidat' => NULL,
														':contracttime_dak_kandidat' => NULL
														));
					header("Location: " . getSiteURLr() . "dak?page=open&id=".$obrisi_ugovor_DAK_kandidat_edit);
				break;

				case "del_note":
					if((in_array( "3" , $getEmployeeStatus)) OR (in_array( "5" , $getEmployeeStatus)) OR (in_array( "2" , $employee_supervizor)) OR (in_array( "1" , $getEmployeeStatus))){

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

						header("Location: " . getSiteURLr() . "dak?page=open&id=$note_dataid&mess=4");

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
				case "obrisi_docs":
					if((in_array( "3" , $getEmployeeStatus)) OR (in_array( "5" , $getEmployeeStatus)) OR (in_array( "2" , $employee_supervizor)) OR (in_array( "1" , $getEmployeeStatus))){

						$id = $_GET['id'];

						//Get doc info
						$doc_open_query = $db->prepare("
													SELECT document_name, document_dataid
													FROM idk_documents
													WHERE document_id = :document_id");

						$doc_open_query->execute(array(
												':document_id' => $id));

						$open = $doc_open_query->fetch();

							$doc_txt = $open['document_name'];
							$doc_dataid = $open['document_dataid'];


						//Add to LOGS
						// $log_desc = "Obrisao bilješku: " .$doc_txt. "";
						// $log_type = "7";
						// addToLogs($log_desc, $log_type); //Log za dak - $log_type = 7

						//Delete doc from db
						$del_query = $db->prepare("
													DELETE FROM idk_documents
													WHERE document_id = :document_id");

						$del_query->execute(array(
											':document_id' => $id));

						header("Location: " . getSiteURLr() . "dak?page=open&id=$doc_dataid&mess=4");

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
				case "list_for_dak":
		?>

						<div class="row">
							<div class="col-sm-4">
								<h1><i class="fa fa-angle-double-right idk_color_green" aria-hidden="true"></i> Wilkommen <?php getEmployeeFullname(); ?></h1>
							</div>
							<div class="col-sm-8 text-right idk_margin_top10">

							</div>
							<div class="col-xs-12">
								<?php
									if(isset($_GET['mess'])) {
										$mess = $_GET['mess'];
									}else{
										$mess = 0;
									}

									if($mess == 1){
										echo '<br><div class="alert material-alert material-alert_success">Hvala! Uspješno ste poslali poruku IDK CRM agentima za podršku.</div>';
									}
								?>
								<hr />
							</div>
						</div>
						<div class="container-fluid" style="background-color:#fff; padding:2rem;">
						<div class="row">
							<div class="col-md-offset-1 col-md-4">
								<a href = "<?php getSiteURL(); ?>dak?page=list&type=2" style="color: #333">
									<div class="idk_time_box idk_box_shadow">
										<h2>Anfrage</h2>
									</div>
								</a>
								<a href = "<?php getSiteURL(); ?>dak?page=list&types[]=3&types[]=5&types[]=6" style="color: #333">
									<div class="idk_time_box idk_box_shadow">
										<h2>Verzeichnes</h2>
									</div>
								</a>
							</div>
							<div class="col-md-offset-1 col-md-5">
								<div class="idk_time_box idk_box_shadow" style="min-height:60vh;">
										<h2>Statistiken</h2>
										<div id="canvas-holder" style="width:100%;">
											<canvas id="chart-area"></canvas>
										</div>
									</div>
							</div>
						</div>
						</div>
						<?php

							$status_poslan = 2;
							$status_aktivni = 3;
							$status_storniran = 5;
							$status_storniranNA = 6;  // stornirani nakon aktivacije
							$broj_poslanih_dak = getEmployeesPerStatusDAK($status_poslan);
							$broj_aktivnih_dak = getEmployeesPerStatusDAK($status_aktivni);
							$broj_storniranih_dak = getEmployeesPerStatusDAK($status_storniran);
							$broj_storniranihNA_dak = getEmployeesPerStatusDAK($status_storniranNA); // stornirani nakon aktivacije
							$br_dak_ukupan = $broj_aktivnih_dak + $broj_storniranih_dak + $broj_storniranihNA_dak + $broj_poslanih_dak;

							$procent_zavrseni = number_format(((($broj_aktivnih_dak + $broj_storniranih_dak + $broj_storniranihNA_dak) / $br_dak_ukupan)*100), 2, ',', '');
							$procent_novih = number_format((($broj_poslanih_dak / $br_dak_ukupan)*100), 2, ',', '');

						?>
						<script>
							window.onload = function() {
							var windowsize = $(window).width();
							var position_legend = 'left';
							var position_graph = 'right';
							var aspectRatioForDesktop = true;
							var ctx = document.getElementById('chart-area').getContext('2d');

							if (windowsize < 768) {
								var position_legend = 'top';
								var position_graph = 'bottom';
								// ctx.height = 520;
								$('#chart-area').css('height', '520');
							}else{
								// ctx.height = 400;
								$('#chart-area').css('height', '350');
							}

							window.chartColors = {
								red: 'rgb(243, 65, 60)',
								green: 'rgb(102, 213, 102)',
								blue: 'rgb(54, 162, 235)'
							};

							var randomScalingFactor = function() {
							return Math.round(Math.random() * 100);
							};

							window.myDoughnut = new Chart(ctx, {
							type: 'doughnut',
							data: {
								datasets: [{
									data: [
										<?php echo getEmployeesPerStatusDAK($status_poslan); ?>,
										<?php  echo (getEmployeesPerStatusDAK($status_storniran)+ getEmployeesPerStatusDAK($status_storniranNA)); ?>,
										<?php echo getEmployeesPerStatusDAK($status_aktivni);?>
										],
										backgroundColor: [
										window.chartColors.green,
										window.chartColors.red,
										window.chartColors.blue
										],
										label: 'Dataset'
									}],
								labels: [
									'Neue (<?php echo getEmployeesPerStatusDAK($status_poslan); ?>)',
									'Storniert (<?php (getEmployeesPerStatusDAK($status_storniran)+ getEmployeesPerStatusDAK($status_storniranNA)); ?>)',
									'Active (<?php echo getEmployeesPerStatusDAK($status_aktivni); ?>)'
								]
							},
							options: {
								responsive: true,
								position: position_graph,
								maintainAspectRatio: false,
								legend: {
									position: position_legend,
									align: 'start'
								},
								title: {
									display: true,
									text: ''
								},
								animation: {
									animateScale: true,
									animateRotate: true
								}
							}
						});
							};
							</script>
		<?php
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
