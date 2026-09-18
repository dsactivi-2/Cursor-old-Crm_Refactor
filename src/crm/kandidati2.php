<?php
	include("includes/functions.php");
	include("includes/common.php");
	$getEmployeeStatus = explode(",", getEmployeeStatus());

	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: kandidati?page=list_ajax");
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

				case "list_ajax":
				
					$starost_od = null;
					$starost_do = null;
					$vozacka_dozvola = null;
					$kj_znanje_njemacki = null;
					$radno_iskustvo = null;
					$filter_grupeImp = null;
					$filter_grupeExp = array();
					$filter_statusImp = null;
					$filter_statusExp = array();
					$filter_drzavljanstvoImp = null;
					$filter_drzavljanstvoExp = array();
					$filter_boravakImp = null;
					$filter_boravakExp = array();
					$filter_skoleImp = null;
					$filter_skoleExp = array();
					$filter_smjerImp = null;
					$filter_smjerExp = array();
					$filter_strukeImp = null;
					$filter_strukeExp = array();
					$filter_izvorImp = null;
					$filter_izvorExp = array();
					$filter_kategorija_vozackeImp = null;
					$filter_kategorija_vozackeExp = array();
				
				if(isset($_COOKIE['archive_status'])){
					$archive_status = 1;
				}else{
					$archive_status = 0;
				}
				
				
					if(isset($_POST['starost_od'])){
						$starost_od = $_POST['starost_od'];
					}
					if(isset($_POST['starost_do'])){
						$starost_do = $_POST['starost_do'];
					}
					
					if(isset($_POST['vozacka_dozvola'])){
						if($_POST['vozacka_dozvola'] == "DA"){
							$vozacka_dozvola = "Da";
						}
					}
					if(isset($_POST['radno_iskustvo'])){
						if($_POST['radno_iskustvo'] == "DA"){
							$radno_iskustvo = "DA";
						}
					}
					if(isset($_POST['kj_znanje_njemacki'])){
						$kj_znanje_njemacki = $_POST['kj_znanje_njemacki'];
					}
					if(isset($_POST['filter_grupe'])){
						$filter_grupe = $_POST['filter_grupe'];
						$filter_grupeImp = implode(",", $filter_grupe);
						if($filter_grupeImp != null){
							$filter_grupeExp = explode(",", $filter_grupeImp);
						} else {
							$filter_statusImp = 0;
							$filter_grupeExp = explode(",", $filter_grupeImp);
						}
					}
					if(isset($_POST['filter_status'])){
						$filter_status = $_POST['filter_status'];
						$filter_statusImp = implode(",", $filter_status);
						if($filter_statusImp != null){
							$filter_statusExp = explode(",", $filter_statusImp);
						} else {
							$filter_statusImp = 0;
							$filter_statusExp = explode(",", $filter_statusImp);
						}
					}
					if(isset($_POST['filter_drzavljanstvo'])){
						$filter_drzavljanstvo = $_POST['filter_drzavljanstvo'];
						$filter_drzavljanstvoImp = implode(",", $filter_drzavljanstvo);
						if($filter_drzavljanstvoImp != null){
							$filter_drzavljanstvoExp = explode(",", $filter_drzavljanstvoImp);
						} else {
							$filter_drzavljanstvoImp = 0;
							$filter_drzavljanstvoExp = explode(",", $filter_drzavljanstvoImp);
						}
					}
					if(isset($_POST['filter_boravak'])){
						$filter_boravak = $_POST['filter_boravak'];
						$filter_boravakImp = implode(",", $filter_boravak);
						if($filter_boravakImp != null){
							$filter_boravakExp = explode(",", $filter_boravakImp);
						} else {
							$filter_boravakImp = 0;
							$filter_boravakExp = explode(",", $filter_boravakImp);
						}
					}
					if(isset($_POST['filter_skole'])){
						$filter_skole = $_POST['filter_skole'];
						$filter_skoleImp = implode(",", $_POST['filter_skole']);
						if($filter_skoleImp != null){
							$filter_skoleExp = explode(",", $filter_skoleImp);
						} else {
							$filter_skoleImp = 0;
							$filter_skoleExp = explode(",", $filter_skoleImp);
						}
					}
					if(isset($_POST['filter_smjer'])){
						$filter_smjer = $_POST['filter_smjer'];
						$filter_smjerImp = implode(",", $filter_smjer);
						if($filter_smjerImp != null){
							$filter_smjerExp = explode(",", $filter_smjerImp);
						} else {
							$filter_smjerImp = 0;
							$filter_smjerExp = explode(",", $filter_smjerImp);
						}
					}
					if(isset($_POST['filter_struke'])){
						$filter_struke = $_POST['filter_struke'];
						$filter_strukeImp = implode(",", $filter_struke);
						if($filter_strukeImp != null){
							$filter_strukeExp = explode(",", $filter_strukeImp);
						} else {
							$filter_strukeImp = 0;
							$filter_strukeExp = explode(",", $filter_strukeImp);
						}
					}
					if(isset($_POST['filter_izvor'])){
						$filter_izvor = $_POST['filter_izvor'];
						$filter_izvorImp = implode(",", $filter_izvor);
						if($filter_izvorImp != null){
							$filter_izvorExp = explode(",", $filter_izvorImp);
						} else {
							$filter_izvorImp = 0;
							$filter_izvorExp = explode(",", $filter_izvorImp);
						}
					}
					if(isset($_POST['filter_kategorija_vozacke'])){
						$filter_kategorija_vozacke = $_POST['filter_kategorija_vozacke'];
						$filter_kategorija_vozackeImp = implode(",", $filter_kategorija_vozacke);
						if($filter_kategorija_vozackeImp != null){
							$filter_kategorija_vozackeExp = explode(",", $filter_kategorija_vozackeImp);
						} else {
							$filter_kategorija_vozackeImp = 0;
							$filter_kategorija_vozackeExp = explode(",", $filter_kategorija_vozackeImp);
						}
					}
					
				//var_dump($filter_skoleImp);
		?>
				<div class="row">
					<div class="col-xs-8">
						<h1><i class="fa fa-user idk_color_green" aria-hidden="true"></i> Kandidati</h1>
					</div>
					<div class="col-xs-4 text-right idk_margin_top10">
						
						<a href="<?php getSiteURL(); ?>registracija/korak1" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus" aria-hidden="true"></i> <span>Registracija</span></a>
					
					<?php if($archive_status == 0){?>
						<a href="<?php getSiteURL(); ?>do.php?form=show_candidate_archive&enable=1">
							<button class="btn material-btn material-btn-icon-success material-btn main-container__column material-btn-icon-responsive"><i class="fa fa-times" aria-hidden="true"></i> ARHIVA</button>
						</a>
					<?php } else { ?>
						<a href="<?php getSiteURL(); ?>do.php?form=show_candidate_archive&enable=0">
							<button class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-check" aria-hidden="true"></i> ARHIVA</button>
						</a>
					<?php } ?>
					</div>
					<div class="col-xs-12 text-right">
					<hr>				
					</div>
				</div>
			<div class="row">
				<div class="col-xs-12">
					<div class="content_box">
							<div class="row">
								<div class="col-xs-12">
									<div class="panel-group material-accordion material-accordion_success" id="accordion1">
										<div class="panel panel-success material-accordion__panel material-accordion__panel">
											<div class="panel-heading material-accordion__heading">
												<h4 class="panel-title">
												<a class="material-accordion__title" style="margin-bottom:0.3rem" data-toggle="collapse" data-parent="#accordion1" href="#filterKandidata"><i class="fa fa-search" aria-hidden="true" style = "margin-right: 10px;"></i>Filter</a>
												</h4>
											</div>
											<div id="filterKandidata" class="panel-collapse collapse material-accordion__collapse">
												<div class="panel-body">
													<form action="<?php getSiteURL(); ?>kandidati2.php?page=list_ajax" method="post"  http-equiv="Content-type" enctype="multipart/form-data"; charset=utf-8" class="form-horizontal">
													<div class="form-group">
														<label for="kki_naziv_web" class="col-sm-4 control-label">Starost:</label>
														<div class="col-sm-8">
															<div class="row">
																<div class="col-sm-4">
																	<div class="materail-input-block materail-input-block_success">
																		<input class="form-control materail-input" type="number" name="starost_od" id="starost_od" placeholder="od" value="<?php if($starost_od != null) echo $starost_od; ?>">
																		<span class="materail-input-block__line"></span>
																	</div>
																</div>
																<div class="col-sm-4">
																	<div class="materail-input-block materail-input-block_success">
																		<input class="form-control materail-input" type="number" name="starost_do" id="starost_do" placeholder="do" value="<?php if($starost_do != null) echo $starost_do; ?>">
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
																<input class="materail-switch__element" type="checkbox" id="switch_input1" name="vozacka_dozvola" value="DA" <?php if($vozacka_dozvola == "Da") echo "checked"; ?>>
																<label class="materail-switch__label" for="switch_input1"></label>
															</div>
														</div>
													</div>
													<div class="form-group">
														<label for="filter_kategorija_vozacke" class="col-sm-4 control-label">Kategorija vozacke:</label>
														<div class="col-sm-5">
															<select class="selectpicker" id="filter_kategorija_vozacke" name="filter_kategorija_vozacke[]" data-actions-box="true" data-live-search="true" multiple>
																<option value="B" <?php if(in_array("B", $filter_kategorija_vozackeExp)) echo "selected";?>>B</option>
																<option value="C1" <?php if(in_array("C1", $filter_kategorija_vozackeExp)) echo "selected";?>>C1</option>
																<option value="C" <?php if(in_array("C", $filter_kategorija_vozackeExp)) echo "selected";?>>C</option>
																<option value="BE" <?php if(in_array("BE", $filter_kategorija_vozackeExp)) echo "selected";?>>BE</option>
																<option value="C1E" <?php if(in_array("C1E", $filter_kategorija_vozackeExp)) echo "selected";?>>C1E</option>
																<option value="CE" <?php if(in_array("CE", $filter_kategorija_vozackeExp)) echo "selected";?>>CE</option>
															</select>
														</div>
													</div>
													<div class="form-group">
														<label for="kki_naziv_web" class="col-sm-4 control-label">Radno iskustvo:</label>
														<div class="col-sm-8">
															<div class="main-container__column materail-switch materail-switch_primary">
																<input class="materail-switch__element" type="checkbox" id="switch_input2" name="radno_iskustvo" value="DA" <?php if($radno_iskustvo == "DA") echo "checked"; ?>>
																<label class="materail-switch__label" for="switch_input2"></label>
															</div>
														</div>
													</div>
													<div class="form-group">
														<label for="kki_naziv_web" class="col-sm-4 control-label">Znanje njemačkog:</label>
														<div class="col-sm-5">
															<select class="selectpicker" id="kj_znanje_njemacki" name="kj_znanje_njemacki">
																<option value="svi" selected>Svi</option>
																<option value="nemainfo" <?php if($kj_znanje_njemacki == "nemainfo") echo "selected"?>>Nema informacije</option>
																<option value="Bezznanja" <?php if($kj_znanje_njemacki == "Bezznanja") echo "selected"?>>Bez znanja</option>
																<option value="A1" <?php if($kj_znanje_njemacki == "A1") echo "selected"?>>A1</option>
																<option value="A2" <?php if($kj_znanje_njemacki == "A2") echo "selected"?>>A2</option>
																<option value="B1" <?php if($kj_znanje_njemacki == "B1") echo "selected"?>>B1</option>
																<option value="B2" <?php if($kj_znanje_njemacki == "B2") echo "selected"?>>B2</option>
																<option value="C1" <?php if($kj_znanje_njemacki == "C1") echo "selected"?>>C1</option>
																<option value="C2" <?php if($kj_znanje_njemacki == "C2") echo "selected"?>>C2</option>
															</select>
														</div>
													</div>
													<div class="form-group">
														<label for="filter_grupe" class="col-sm-4 control-label">Grupe:</label>
														<div class="col-sm-5">
															<select class="selectpicker" id="filter_grupe" name="filter_grupe[]" data-actions-box="true" data-live-search="true" multiple>
																<?php 
																	$query = $db->prepare("
																					SELECT kg_id, kg_title, kg_date
																					FROM idk_kandidati_grupe
																					WHERE kg_status = 0
																					ORDER BY kg_id DESC
																					");

																	$query->execute();

																	while($row = $query->fetch()){

																		$kg_id = $row['kg_id'];
																		$kg_title = $row['kg_title'];
																?>
																		<option value="<?php echo $kg_id;?>" <?php if(in_array($kg_id, $filter_grupeExp)) echo "selected";?>>
																			<?php echo $kg_title; ?>
																		</option>
																<?php
																	}	
																?>
															</select>
														</div>
													</div>			
													<div class="form-group">
														<label for="filter_status" class="col-sm-4 control-label">Status:</label>
														<div class="col-sm-5">
															<select class="selectpicker" id="filter_status" name="filter_status[]" data-actions-box="true" multiple>
																<option value="0" <?php if(in_array(0, $filter_statusExp)) echo "selected";?>>Na provjeri</option>;
																<option value="1" <?php if(in_array(1, $filter_statusExp)) echo "selected";?>>U obradi</option>;
																<option value="5" <?php if(in_array(5, $filter_statusExp)) echo "selected";?>>Dopuna</option>;
																<option value="4" <?php if(in_array(4, $filter_statusExp)) echo "selected";?>>Kontrola</option>;
																<option value="2" <?php if(in_array(2, $filter_statusExp)) echo "selected";?>>Obrađen</option>;
																<option value="7" <?php if(in_array(7, $filter_statusExp)) echo "selected";?>>U obradi više od 3 dana</option>;
																<option value="8" <?php if(in_array(8, $filter_statusExp)) echo "selected";?>>Na dopuni više od 3 dana</option>;
																<option value="6" <?php if(in_array(6, $filter_statusExp)) echo "selected";?>>Odbio messenger</option>;
																<option value="3" <?php if(in_array(3, $filter_statusExp)) echo "selected";?>>Arhiviran</option>;
															</select>
														</div>
													</div>
													<div class="form-group">
														<label for="filter_izvor" class="col-sm-4 control-label">Izvor:</label>
														<div class="col-sm-5">
															<select class="selectpicker" id="filter_izvor" name="filter_izvor[]" data-actions-box="true" data-live-search="true" multiple>
																<option value="1" <?php if(in_array(1, $filter_izvorExp)) echo "selected";?>>DIPL</option>
																<option value="2" <?php if(in_array(2, $filter_izvorExp)) echo "selected";?>>DAK</option>
																<option value="3" <?php if(in_array(3, $filter_izvorExp)) echo "selected";?>>DIPL - Partner</option>
																<option value="4" <?php if(in_array(4, $filter_izvorExp)) echo "selected";?>>DIPL - SZV</option>
																<option value="5" <?php if(in_array(5, $filter_izvorExp)) echo "selected";?>>DIPL - WEB</option>
																<option value="6" <?php if(in_array(6, $filter_izvorExp)) echo "selected";?>>Partner</option>
																<option value="7" <?php if(in_array(7, $filter_izvorExp)) echo "selected";?>>Partner</option>
																<option value="8" <?php if(in_array(8, $filter_izvorExp)) echo "selected";?>>Prijava na oglas</option>
															</select>
														</div>
													</div>
													<div class="form-group">
														<label for="filter_drzavljanstvo" class="col-sm-4 control-label">Državljanstvo:</label>
														<div class="col-sm-5">
															<select class="selectpicker" id="filter_drzavljanstvo" name="filter_drzavljanstvo[]" multiple>
																<option value="EU" <?php if(in_array("EU", $filter_drzavljanstvoExp)) echo "selected";?>>EU</option>
																<option value="NON-EU" <?php if(in_array("NON-EU", $filter_drzavljanstvoExp)) echo "selected";?>>NON-EU</option>
															</select>
														</div>
													</div>
													<div class="form-group">
														<label for="filter_boravak" class="col-sm-4 control-label">Boravak u EU:</label>
														<div class="col-sm-5">
															<select class="selectpicker" id="filter_boravak" name="filter_boravak[]" multiple>
																<?php 
																	$get_countries = $db->prepare("SELECT boravak_eu FROM idk_kandidati WHERE boravak_eu != 'NN' GROUP BY boravak_eu ");
																	$get_countries->execute();
																	while($row_countires = $get_countries->fetch()){
																		?>
																			<option value="<?php echo $row_countires['boravak_eu']; ?>" <?php if(in_array($row_countires['boravak_eu'], $filter_boravakExp)) echo "selected"?>>
																				<?php echo $row_countires['boravak_eu']; ?>
																			</option>
																		<?php
																	}
																?>
															</select>
														</div>
													</div>
													<div class="form-group">
														<label for="filter_struke" class="col-sm-4 control-label">Struke:</label>
														<div class="col-sm-5">
															<select class="selectpicker" id="filter_struke" name="filter_struke[]" data-actions-box="true" data-live-search="true" multiple>
																<option data-struka_id="1" value="1" <?php if(in_array(1, $filter_strukeExp)) echo "selected";?>>Automehaničar</option>
																<option data-struka_id="2" value="2" <?php if(in_array(2, $filter_strukeExp)) echo "selected";?>>Servisni tehničar</option>
																<option data-struka_id="3" value="3" <?php if(in_array(3, $filter_strukeExp)) echo "selected";?>>Tiefbau</option>
															</select>
														</div>
													</div>
													<script>
														$("#filter_struke").on("change", function(){
															var struka_id = $(this).val();
															$("#filter_skole").attr('disabled', 'disabled');
															
															function updateSkolaEnabled(){
																if(strukaSelect()){
																	$('#filter_skole').removeAttr('disabled');
																} else {
																	$('#filter_skole').attr('disabled', 'disabled');
																}
															}
															function strukaSelect(){
																if($("#filter_struke").val() != ''){
																	return true;
																} else {
																	return false;
																}
															}
															$("#filter_struke").change(updateSkolaEnabled);
															
															$.ajax({
																url: 'ajax_data.php?page=smjer_struke',
																type: 'POST',
																data: {'struka_id':struka_id},
																dataType: 'html',
																success: function(data) {
																	$("#filter_smjer").html(data).selectpicker('refresh');
																},
																error: function (xhr, ajaxOptions, thrownError) {
																	alert(xhr.status);
																	alert(thrownError);
																}
															});
														});
														$(document).ready(function(){
															var struka_id = $('#filter_struke').val();
															$.ajax({
																url: 'ajax_data.php?page=smjer_struke',
																type: 'POST',
																data: {'struka_id':struka_id},
																dataType: 'html',
																success: function(data) {
																	$("#filter_smjer").html(data).selectpicker('refresh');
																},
																error: function (xhr, ajaxOptions, thrownError) {
																	alert(xhr.status);
																	alert(thrownError);
																}
															});
															
														});
													</script>
													<div class="form-group">
														<label for="filter_skole" class="col-sm-4 control-label">Škole:</label>
														<div class="col-sm-5">
															<select class="selectpicker" id="filter_skole" name="filter_skole[]" data-actions-box="true" data-live-search="true" multiple>
																<?php 
																	$query_skole = $db->prepare("
																			SELECT * FROM idk_skole ORDER BY skola_naziv
																	");
																	$query_skole->execute();
																	
																	while($row_skola = $query_skole->fetch()){
																		$skola_naziv = $row_skola['skola_naziv'];
																		$skola_tip = $row_skola['skola_tip_obrazovanja'];
																		$skola_id = $row_skola['skola_id'];
																		?>
																		<option value="<?php echo $skola_naziv; ?>" data-skola_id="<?php echo $skola_id; ?>" <?php if(in_array($skola_naziv, $filter_skoleExp)) echo "selected"?>>
																			<?php echo $skola_naziv; ?>
																		</option>
																		<?php
																	}
																?>
															</select>
														</div>
													</div>
													<script>
														$('#filter_skole').on('change', function() {
															var skola_naziv = $(this).val();
															$("#filter_struke").attr('disabled', 'disabled');
															
															function updateStrukaEnabled(){
																if(strukaSelect()){
																	$('#filter_struke').removeAttr('disabled');
																} else {
																	$('#filter_struke').attr('disabled', 'disabled');
																}
															}
															function strukaSelect(){
																if($("#filter_skole").val() != ''){
																	return true;
																} else {
																	return false;
																}
															}
															$("#filter_skole").change(updateStrukaEnabled);
															
															$.ajax({
																url: 'ajax_data.php?page=smjer_skole',
																type: 'POST',
																data: {'skola_naziv':skola_naziv},
																dataType: 'html',
																success: function(data) {
																	$("#filter_smjer").html(data).selectpicker('refresh');
																},
																error: function (xhr, ajaxOptions, thrownError) {
																	alert(xhr.status);
																	alert(thrownError);
																}
															});
															
														});
														$(document).ready(function(){
															var skola_naziv = $("#filter_skole").val();
															$.ajax({
																url: 'ajax_data.php?page=smjer_skole',
																type: 'POST',
																data: {'skola_naziv':skola_naziv},
																dataType: 'html',
																success: function(data) {
																	$("#filter_smjer").html(data).selectpicker('refresh');
																},
																error: function (xhr, ajaxOptions, thrownError) {
																	alert(xhr.status);
																	alert(thrownError);
																}
															});
														});
													</script>
													<div class="form-group">
														<label for="filter_smjer" class="col-sm-4 control-label">Smjerovi:</label>
														<div class="col-sm-5">
															<select class="selectpicker" id="filter_smjer" name="filter_smjer[]" data-actions-box="true" data-live-search="true" multiple>
															
															</select>
														</div>
													</div>
												</div>
												<div class="modal-footer material-modal__footer">
													<a id="archive_link" href=""><button class="btn btn-primary material-btn material-btn_success" type="submit" name="submit">TRAŽI</button></a>
													</form>
												</div>
											</div>
										</div>
									</div>
								</div>
							
								<script type="text/javascript" language="javascript" >
									$(document).ready(function() {

										$('#idk_table').DataTable({
											responsive: true,
											"pageLength": 10,
											"processing": true,
											"serverSide": true,
											"order": [[ 0, "desc" ]],
											"aoColumns": [
													{ "width": "5%"  },
													{ "width": "5%"  },
													{ "width": "2%"  },
													{ "width": "15%" },
													{ "width": "15%" },
													{ "width": "5%" },
													{ "width": "10%" },
													{ "width": "15%" },
													{ "width": "10%" },
													{ "width": "10%" },
													{ "width": "8%" }
												],

											"ajax":{
												url :"serversidedata.php?page=lista_kandidata",
												type: "POST",
												data: {"starost_od": '<?php echo $starost_od; ?>',
													   "starost_do": '<?php echo $starost_do; ?>',
													   "vozacka_dozvola": '<?php echo $vozacka_dozvola; ?>',
													   "radno_iskustvo": '<?php echo $radno_iskustvo; ?>',
													   "znanje_njemacki": '<?php echo $kj_znanje_njemacki; ?>',
													   "filter_grupe": '<?php echo $filter_grupeImp; ?>',
													   "filter_status": '<?php echo $filter_statusImp; ?>',
													   "filter_drzavljanstvo": '<?php echo $filter_drzavljanstvoImp; ?>',
													   "filter_boravak": '<?php echo $filter_boravakImp; ?>',
													   "filter_skole": '<?php echo $filter_skoleImp; ?>',
													   "filter_smjer": '<?php echo $filter_smjerImp; ?>',
													   "filter_izvor": '<?php echo $filter_izvorImp; ?>',
													   "filter_kategorija_vozacke": '<?php echo $filter_kategorija_vozackeImp; ?>'
														},
												error: function(data){
													$(".list-grid-error").html(""); 
													$("#list-grid_processing").css("display","none");
											
												},
											}
										} );
										
									} );
							</script>								
								<table id="idk_table" class="display" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th></th>
											<th></th>
											<th></th>
											<th>Ime i prezime</th>
											<th>Datum ulaska</th>
											<th>Izvor</th>
											<th class="text-center">CV generisan</th>
											<th>Nalog</th>
											<th class="text-center">Grupa</th>
											<th class="text-center">Status</th>
											<th></th>
										</tr>
									</thead>
								</table>
								
								<script>
									$( document ).ajaxComplete(function() {
										$(".archive").click(function () {
											var addressValue = $(this).attr("data");
											document.getElementById("archive_url").href = addressValue;
									
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
										<p>Jeste li sigurni da želite arhivirati kandidata?</p>
									</div>
									<div class="modal-footer material-modal__footer">
										<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
										<a id="archive_url" href=""><button class="btn btn-primary material-btn material-btn_danger">ARHIVIRAJ</button></a>
									</div>
								</div>
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
			
			<style>
				#date_one{
					display: none;
				}
				#date_two{
					display: none;
				}
			</style>
			<!-- CHART DATA -->
			<div class="col-xs-12 col-sm-12 col-md-4 ">
				<br/>
				<hr/>
				<br/>
				<form  enctype="multipart/form-data" method="post" accept-charset="utf-8" role="form" class="form-horizontal" id="form_date1">
					<div class="flatpickr_one calendar_area" style="top: 88px">
						<input type="text" class="form-control" name="date_one" id="date_one" data-input ></input>
						<a class="input-button" title="toggle" data-toggle><i class="fa fa-calendar" style="font-size: 21px;" aria-hidden="true"></i></a>
					</div>
				</form>
				
				<div id="canvas-holder" style="width:100%">
					<canvas id="chart-area"></canvas>
				</div>
			</div>
			<!-- CHART DATA -->
			<div class="col-xs-12 col-sm-12 col-md-4">
				<br/>
				<hr/>
				<br/>
				<form  enctype="multipart/form-data" method="post" accept-charset="utf-8" role="form" class="form-horizontal" id="form_date2">
					<div class="flatpickr_two calendar_area" style="top: 88px">
						<input type="text" class="form-control" name="date_two" id="date_two" data-input ></input>
						<a class="input-button" title="toggle" data-toggle><i class="fa fa-calendar" style="font-size: 21px;" aria-hidden="true"></i></a>
					</div>
				</form>
				
				<div id="canvas-holder" style="width:100%">
					<canvas id="chart-area2"></canvas>
				</div>
			</div>
			<!-- CHART DATA -->
			<div class="col-xs-12 col-sm-12 col-md-4">
				<br/>
				<hr/>
				<br/>
				<div id="canvas-holder" style="width:100%">
					<canvas id="chart-area3"></canvas>
				</div>
			</div>
			<script>
				$(".flatpickr_one").flatpickr({
					mode: "range",
					dateFormat: "d.m.Y",
					disableMobile: "true",
					onClose: function(selectedDates, dateStr, instance) {
						
						var from = (selectedDates[0].getDate()) + "." + (selectedDates[0].getMonth() + 1) + "." + selectedDates[0].getFullYear(); 
						var tooo = (selectedDates[1].getDate()) + "." + (selectedDates[1].getMonth() + 1) + "." + selectedDates[1].getFullYear(); 
						$("#date_one").val(from+"to"+tooo);
						$('#form_date1').submit();
					
					}
				});	
				$(".flatpickr_two").flatpickr({
					mode: "range",
					dateFormat: "d.m.Y",
					disableMobile: "true",
					onClose: function(selectedDates, dateStr, instance) {
						
						var from2 = (selectedDates[0].getDate()) + "." + (selectedDates[0].getMonth() + 1) + "." + selectedDates[0].getFullYear(); 
						var tooo2 = (selectedDates[1].getDate()) + "." + (selectedDates[1].getMonth() + 1) + "." + selectedDates[1].getFullYear(); 
						$("#date_two").val(from2+"to"+tooo2);
						$('#form_date2').submit();
						
					}
				});						
			</script>
			
				<?php
					if(isset($_POST['date_one'])){
						$f_from = $_POST['date_one'];
					
						if (strpos($f_from, 'to') !== false) {
							$split = explode("to",$f_from);
							$datum_od = $split[0];
							$datum_do = $split[1];
						}else{
							$datum_od = $f_from;
							$datum_do = $f_from;
						}
						
						$date1_from = date('Y-m-d', strtotime($datum_od." 00:00:00"));
						$date1_to = date('Y-m-d', strtotime($datum_do." 23:59:59"));	
						
					}else{
						$date1_from = date('Y-m-d', strtotime("1970-01-01 00:00:00"));
						$date1_to = date('Y-m-d H:i:s');
					}
					if(isset($_POST['date_two'])){
						$date2 = $_POST['date_two'];
					
						if (strpos($date2, 'to') !== false) {
							$split = explode("to",$date2);
							$datum_od2 = $split[0];
							$datum_do2 = $split[1];
						}else{
							$datum_od2 = $date2;
							$datum_do2 = $date2;
						}
						
						$date2_from = date('Y-m-d', strtotime($datum_od2." 00:00:00"));
						$date2_to = date('Y-m-d', strtotime($datum_do2." 23:59:59"));	
						
					}else{
						$date2_from = date('Y-m-d', strtotime("1970-01-01 00:00:00"));
						$date2_to = date('Y-m-d H:i:s');
					}
					
					$status_na_provjeri = 0;
					$status_u_obradi = 1;
					$status_obradjen = 2;
					$status_arhiviran = 3;
					$status_kontrola = 4;
					$status_dopuna = 5;
					$status_odbio_msngr = 6;
					$status_u_obradi_3 = 7;
					$status_u_dopuni_3 = 8;
					
					$mes_status_cekanje_instalacije = 1;
					$mes_status_odbio_instalirati = 3;
					
					$br_mes_npr = getCandidatesPerMessengerStatus($mes_status_cekanje_instalacije, $date2_from, $date2_to);
					$br_mes_odb = getCandidatesPerMessengerStatus($mes_status_odbio_instalirati, $date2_from, $date2_to);
					$br_mes_u_o_3 = getNezavrseni($status_u_obradi_3, $date2_from, $date2_to);
					$br_mes_n_d_3 = getNezavrseni($status_u_dopuni_3, $date2_from, $date2_to);
					$br_mes_u_o = getCandidatesPerStatusWithMessenger($status_u_obradi, $date2_from, $date2_to);
					$br_mes_dop = getCandidatesPerStatusWithMessenger($status_dopuna, $date2_from, $date2_to);
					$br_mes_kon = getCandidatesPerStatusWithMessenger($status_kontrola, $date2_from, $date2_to);
					$br_mes_obr = getCandidatesPerStatusWithMessenger($status_obradjen, $date2_from, $date2_to);
					$br_mes_instaliran = $br_mes_u_o_3 + $br_mes_n_d_3 + $br_mes_u_o + $br_mes_dop + $br_mes_kon + $br_mes_obr;
					$br_mes_total = $br_mes_npr + $br_mes_odb + $br_mes_instaliran;
					
					$procent_mes_u_o_3 = number_format((($br_mes_u_o_3 / $br_mes_instaliran)*100), 2, ',', '');
					$procent_mes_n_d_3 = number_format((($br_mes_n_d_3 / $br_mes_instaliran)*100), 2, ',', '');
					$procent_mes_npr = number_format((($br_mes_npr / $br_mes_total)*100), 2, ',', '');
					$procent_mes_odb = number_format((($br_mes_odb / $br_mes_total)*100), 2, ',', '');
					$procent_mes_u_o = number_format((($br_mes_u_o / $br_mes_instaliran)*100), 2, ',', '');
					$procent_mes_dop = number_format((($br_mes_dop / $br_mes_instaliran)*100), 2, ',', '');
					$procent_mes_kon = number_format((($br_mes_kon / $br_mes_instaliran)*100), 2, ',', '');
					$procent_mes_obr = number_format((($br_mes_obr / $br_mes_instaliran)*100), 2, ',', '');
					$procent_mes_instaliran = number_format((($br_mes_instaliran / $br_mes_total)*100), 2, ',', '');
					
				?>
		<script>
			window.onload = function() {
				var windowsize = $(window).width();
				var position_legend = 'left';
				var position_graph = 'right';
				var aspectRatioForDesktop = true;
				
				var ctx2 = document.getElementById('chart-area2').getContext('2d');
				//window.myDoughnut2 = new Chart(ctx2, config2);
				var ctx = document.getElementById('chart-area').getContext('2d');
				//window.myDoughnut = new Chart(ctx, config);
				var ctx3 = document.getElementById('chart-area3').getContext('2d');
				//window.myDoughnut2 = new Chart(ctx2, config2);
				if (windowsize < 768) {
					var position_legend = 'top';
					var position_graph = 'bottom';
					// ctx.height = 520;
					// ctx2.height = 520;
					$('#chart-area').css('height', '520');
					$('#chart-area2').css('height', '520');
					$('#chart-area3').css('height', '520');
				}else{
					// ctx.height = 400;
					// ctx2.height = 400;
					$('#chart-area').css('height', '350');
					$('#chart-area2').css('height', '350');
					$('#chart-area3').css('height', '350');
				}
				window.chartColors = {
					red: 'rgb(255, 0, 0)',
					orange: 'rgb(255, 159, 64)',
					yellow: 'rgb(255, 205, 86)',
					green: 'rgb(102, 213, 102)',
					blue: 'rgb(54, 162, 235)',
					purple: 'rgb(153, 102, 255)',
					grey: 'rgb(201, 203, 207)',
					light_blue: 'rgb(139,218,242)',
					redd: 'rgb(255,68,11)'
				};	
			
				var randomScalingFactor = function() {
					return Math.round(Math.random() * 100);
				};
		
				window.myDoughnut = new Chart(ctx, {
					type: 'doughnut',
					data: {
						datasets: [{
							data: [
								<?php echo getEmployeesPerStatusDate($status_arhiviran, $date1_from, $date1_to); ?>,
								<?php echo getEmployeesPerStatusDate($status_odbio_msngr, $date1_from, $date1_to); ?>,
								<?php echo getEmployeesPerStatusDate($status_na_provjeri, $date1_from, $date1_to); ?>,
								<?php echo (getEmployeesPerStatusDate($status_u_obradi, $date1_from, $date1_to) + getEmployeesPerStatusDate($status_dopuna, $date1_from, $date1_to) + getEmployeesPerStatusDate($status_u_obradi_3, $date1_from, $date1_to) + getEmployeesPerStatusDate($status_u_dopuni_3, $date1_from, $date1_to)); ?>,
								<?php echo getEmployeesPerStatusDate($status_kontrola, $date1_from, $date1_to); ?>,
								<?php echo getEmployeesPerStatusDate($status_obradjen, $date1_from, $date1_to); ?>,
								
							],
							backgroundColor: [
								window.chartColors.red,
								window.chartColors.redd,
								window.chartColors.orange,
								window.chartColors.blue,
								window.chartColors.light_blue,
								window.chartColors.green
								
							],
							label: 'Dataset 1'
						}],
						labels: [
							'Arhiviran (<?php echo getEmployeesPerStatusDate($status_arhiviran, $date1_from, $date1_to); ?>)',
							'Odbio Messenger (<?php echo getEmployeesPerStatusDate($status_odbio_msngr, $date1_from, $date1_to); ?>)',
							'Na provjeri (<?php echo getEmployeesPerStatusDate($status_na_provjeri, $date1_from, $date1_to); ?>)',
							'U obradi (<?php echo (getEmployeesPerStatusDate($status_u_obradi, $date1_from, $date1_to) + getEmployeesPerStatusDate($status_dopuna, $date1_from, $date1_to) + getEmployeesPerStatusDate($status_u_obradi_3, $date1_from, $date1_to) + getEmployeesPerStatusDate($status_u_dopuni_3, $date1_from, $date1_to)) ; ?>)',
							'Kontrola (<?php echo getEmployeesPerStatusDate($status_kontrola, $date1_from, $date1_to); ?>)',
							'Obrađen (<?php echo getEmployeesPerStatusDate($status_obradjen, $date1_from, $date1_to); ?>)'
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
							text: 'Statistika svih kandidata'
						},
						animation: {
							animateScale: true,
							animateRotate: true
						}
					}
				});
				
				
				window.myDoughnut2 = new Chart(ctx2, {
					type: 'doughnut',
					data: {
						datasets: [{
							data: [
								<?php echo $br_mes_instaliran; ?>,
								<?php echo getCandidatesPerMessengerStatus($mes_status_cekanje_instalacije, $date2_from, $date2_to); ?>,
								<?php echo getCandidatesPerMessengerStatus($mes_status_odbio_instalirati, $date2_from, $date2_to); ?>
							],
							backgroundColor: [
								window.chartColors.green,
								window.chartColors.orange,
								window.chartColors.red
							],
							label: 'Dataset 1'
						}],
						labels: [
							'Instaliran(<?php echo $br_mes_instaliran; ?>) - <?php echo $procent_mes_instaliran; ?> %',
							'Na Čekanju(<?php echo getCandidatesPerMessengerStatus($mes_status_cekanje_instalacije, $date2_from, $date2_to);?>) - <?php echo $procent_mes_npr; ?> %',
							'Odbijen(<?php echo getCandidatesPerMessengerStatus($mes_status_odbio_instalirati, $date2_from, $date2_to);?>) - <?php echo $procent_mes_odb; ?> %'
						]
					},
					options: {
						responsive: true,
						maintainAspectRatio: false,
						position: position_graph,
						legend: {
							position: position_legend,
							align: 'start'
						},
						title: {
							display: true,
							text: 'Statistika instalacija za messenger - Total (<?php echo $br_mes_total;?>)'
						},
						animation: {
							animateScale: true,
							animateRotate: true
						}
					}
				});
				window.myDoughnut3 = new Chart(ctx3, {
					type: 'doughnut',
					data: {
						datasets: [{
							data: [
								<?php echo getCandidatesPerStatusWithMessenger($status_obradjen, $date2_from, $date2_to); ?>,
								<?php echo getNezavrseni($status_u_obradi_3, $date2_from, $date2_to); ?>,
								<?php echo getNezavrseni($status_u_dopuni_3, $date2_from, $date2_to); ?>,
								<?php echo getCandidatesPerStatusWithMessenger($status_u_obradi, $date2_from, $date2_to); ?>,
								<?php echo getCandidatesPerStatusWithMessenger($status_dopuna, $date2_from, $date2_to); ?>,
								<?php echo getCandidatesPerStatusWithMessenger($status_kontrola, $date2_from, $date2_to); ?>
							],
							backgroundColor: [
								window.chartColors.green,
								window.chartColors.yellow,
								window.chartColors.orange,
								window.chartColors.blue,
								window.chartColors.purple,
								window.chartColors.light_blue
							],
							label: 'Dataset 1'
						}],
						labels: [
							'Obrađen (<?php echo getCandidatesPerStatusWithMessenger($status_obradjen, $date2_from, $date2_to);?>) - <?php echo $procent_mes_obr; ?>%',
							'U obradi više od 3 dana (<?php echo getNezavrseni($status_u_obradi_3, $date2_from, $date2_to);?>) - <?php echo $procent_mes_u_o_3; ?>%',
							'Na dopuni više od 3 dana (<?php echo getNezavrseni($status_u_dopuni_3, $date2_from, $date2_to);?>) - <?php echo $procent_mes_n_d_3; ?>%',
							'U obradi (<?php echo getCandidatesPerStatusWithMessenger($status_u_obradi, $date2_from, $date2_to);?>) - <?php echo $procent_mes_u_o; ?>%',
							'Dopuna (<?php echo getCandidatesPerStatusWithMessenger($status_dopuna, $date2_from, $date2_to);?>) - <?php echo $procent_mes_dop; ?>%',
							'Kontrola (<?php echo getCandidatesPerStatusWithMessenger($status_kontrola, $date2_from, $date2_to);?>) - <?php echo $procent_mes_kon; ?>%'
						]
					},
					options: {
						responsive: true,
						maintainAspectRatio: false,
						position: position_graph,
						legend: {
							position: position_legend,
						},
						title: {
							display: true,
							text: 'Statistika obrade za messenger - Total (<?php echo $br_mes_instaliran;?>)'
						},
						animation: {
							animateScale: true,
							animateRotate: true
						}
					}
				});
			};
		</script>
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
								<form action="<?php getSiteURL(); ?>do.php?form=add_employees" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
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
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="password" name="employee_password" id="employee_password" placeholder="Lozinka" required>
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
												<input class="form-control materail-input" data-init-set="false" data-large-default="true" data-format="d.m.Y." data-large-mode="true" data-modal="true" type="text" name="employee_dob" id="employee_dob" placeholder="Datum rođenja">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
										<script>
											$( function() {	$( "#employee_dob" ).dateDropper();												} );
										</script>
									</div>
									<div class="form-group">
										<label for="employee_doe" class="col-sm-3 control-label">Datum zaposlenja:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" data-init-set="false" data-large-default="true" data-format="d.m.Y." data-large-mode="true" data-modal="true" type="text" name="employee_doe" id="employee_doe" placeholder="Datum zaposlenja">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
										<script>
											$( function() {	$( "#employee_doe" ).dateDropper();												} );
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
									<div class="form-group">
										<label for="employee_status" class="col-sm-3 control-label"><span class="text-danger">*</span> Status:</label>
										<div class="col-sm-9">
											<select class="selectpicker" id="employee_status" name="employee_status" required>
												<option value=""></option>
												<option value="3">Korisnik</option>
												<option value="2">Suoer korisnik</option>
												<option value="1">Administrator</option>
												<option value="0">Deaktiviran</option>
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

																var f = this.files[0];

																if (f.size > 20388608 || f.fileSize > 20388608){
																	$('#idk_alert_size').removeClass('hidden');
																	this.value = null;
																}else{
																	$('#idk_alert_size').addClass('hidden');
																}

																var ext = $('#employee_image').val().split('.').pop().toLowerCase();

																if($.inArray(ext, ['jpg', 'jpeg', 'png']) == -1) {
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
					if($getEmployeeStatus == 1){

						$employee_id = $_GET['id'];

						$query = $db->prepare("
										SELECT employee_firstname, employee_lastname, employee_jmbg, employee_email, employee_rfid, employee_position, employee_dob, employee_doe, employee_phone, employee_address, employee_city, employee_country, employee_info, employee_status, employee_image
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
							$employee_rfid = $row['employee_rfid'];
							$employee_phone = $row['employee_phone'];
							$employee_address = $row['employee_address'];
							$employee_city = $row['employee_city'];
							$employee_country = $row['employee_country'];
							$employee_info = $row['employee_info'];
							$employee_status = $row['employee_status'];

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
								<form action="<?php getSiteURL(); ?>do.php?form=edit_employees" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
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
										<label for="employee_password" class="col-sm-3 control-label"><span class="text-danger">*</span> Lozinka:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="password" name="employee_password" id="employee_password" placeholder="Lozinka" >
												<span class="materail-input-block__line"></span>
											</div>
											<small>Ukoliko želite promijeniti lozinku, unesite novu.</small>
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
										<label for="employee_phone" class="col-sm-3 control-label">Primarni telefon:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="employee_phone" id="employee_phone" value="<?php echo $employee_phone; ?>" placeholder="Primarni telefon">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
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
									<div class="form-group">
										<label for="employee_status" class="col-sm-3 control-label"><span class="text-danger">*</span> Status:</label>
										<div class="col-sm-9">
											<select class="selectpicker" id="employee_status" name="employee_status" required>
												<option value=""></option>
												<option value="3" <?php if($employee_status == "3"){ echo "selected"; } ?>>Korisnik</option>
												<option value="2" <?php if($employee_status == "2"){ echo "selected"; } ?>>Suoer korisnik</option>
												<option value="1" <?php if($employee_status == "1"){ echo "selected"; } ?>>Administrator</option>
												<option value="0" <?php if($employee_status == "0"){ echo "selected"; } ?>>Deaktiviran</option>
											</select>
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

																var f = this.files[0];

																if (f.size > 20388608 || f.fileSize > 20388608){
																	$('#idk_alert_size').removeClass('hidden');
																	this.value = null;
																}else{
																	$('#idk_alert_size').addClass('hidden');
																}

																var ext = $('#employee_image').val().split('.').pop().toLowerCase();

																if($.inArray(ext, ['jpg', 'jpeg', 'png']) == -1) {
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

					$kandidat_id = $_GET['id'];

					//UPDATE NOTIFICATION AS READ
					if(isset($_GET['update_not'])){
						$update_not = $_GET['update_not'];
						updateNotificationStatus($update_not, $kandidat_id);
					}else{}

					$query = $db->prepare("
									SELECT kandidat_ime, kandidat_prezime, kandidat_spol, kandidat_djevojackoprezime, kandidat_jmbg, kandidat_datumrodjenja, kandidat_mjestorodjenja, kandidat_drzavarodjenja, kandidat_drzavljanstvo, kandidat_bracnostanje, kandidat_adresa, kandidat_grad, kandidat_pbroj, kandidat_drzava, kandidat_email, kandidat_slika, kandidat_datetime,kandidat_check, kandidat_vozacka_dozvola, kandidat_prijava_na, kandidat_visitedurl
									FROM idk_kandidati
									WHERE kandidat_id = :kandidat_id");

					$query->execute(array(
								':kandidat_id' => $kandidat_id));

					$row = $query->fetch();

						$kandidat_ime = $row['kandidat_ime'];
						$kandidat_prezime = $row['kandidat_prezime'];
						$kandidat_spol = $row['kandidat_spol'];
						$kandidat_check = $row['kandidat_check'];
						$kandidat_djevojackoprezime = $row['kandidat_djevojackoprezime'];
						$kandidat_jmbg = $row['kandidat_jmbg'];
						$kandidat_mjestorodjenja = $row['kandidat_mjestorodjenja'];
						$kandidat_drzavarodjenja = $row['kandidat_drzavarodjenja'];
						$kandidat_drzavljanstvo = $row['kandidat_drzavljanstvo'];
						$kandidat_bracnostanje = $row['kandidat_bracnostanje'];
						$kandidat_adresa = $row['kandidat_adresa'];
						$kandidat_prijava_na = $row['kandidat_prijava_na'];
						$kandidat_grad = $row['kandidat_grad'];
						$kandidat_pbroj = $row['kandidat_pbroj'];
						$kandidat_drzava = $row['kandidat_drzava'];
						$kandidat_email = $row['kandidat_email'];
						$kandidat_drzava = $row['kandidat_drzava'];
						$kandidat_visitedurl = $row['kandidat_visitedurl'];
						$kandidat_datumrodjenja = date('d.m.Y.', strtotime($row['kandidat_datumrodjenja']));
						$kandidat_datetime = date('d.m.Y H:i', strtotime($row['kandidat_datetime']));


						if($row['kandidat_slika'] == "none"){
							$kandidat_slika = "none.jpg";
						}else{
							$kandidat_slika = $row['kandidat_slika'];
						}

						if($row['kandidat_status'] == 0){
							$kandidat_status = "Deaktiviran";
						}elseif($row['kandidat_status'] == 1){
							$kandidat_status = "Administrator";
						}elseif($row['kandidat_status'] == 2){
							$kandidat_status = "Super korisnik";
						}elseif($row['kandidat_status'] == 3){
							$kandidat_status = "Korisnik";
						}

						if($row['kandidat_vozacka_dozvola'] == "Da"){
							$kandidat_vozacka_dozvola = '<span class="label label-success material-label material-label_success main-container__column">DA</span>';
						}else{
							$kandidat_vozacka_dozvola = '<span class="label label-danger material-label material-label_danger main-container__column">NE</span>';
						}
		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><a class="fancybox" rel="group" href="<?php getSiteURL(); ?>files/kandidati/<?php echo $kandidat_slika; ?>"><img class="idk_profile_img" src="<?php getSiteURL(); ?>files/kandidati/<?php echo $kandidat_slika; ?>"></a> <?php echo $kandidat_ime; ?> <?php echo $kandidat_prezime; ?></h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>kandidati?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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
										<div class="alert material-alert material-alert_success">Uspješno ste uredili kontakt podatke kandidatu.</div>
									<?php
									}elseif($mess == 7){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste uredili edukacija podatke kandidatu.</div>
									<?php
									}elseif($mess == 8){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste uredili radno iskustvo kandidatu.</div>
									<?php
									}elseif($mess == 9){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste uredili vještine kandidatu.</div>
									<?php
									}elseif($mess == 10){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste uredili jezik kandidatu.</div>
									<?php
									}elseif($mess == 11){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste uredili osobne informacije kandidatu.</div>
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
											<form action="<?php getSiteURL(); ?>do.php?form=add_candidate_note" method="post" role="form" class="form-horizontal">
												<input type="hidden" name="note_dataid" value="<?php echo $kandidat_id; ?>" />
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
											<form action="<?php getSiteURL(); ?>do.php?form=add_kandidat_doc" enctype="multipart/form-data" method="post" role="form" class="form-horizontal">
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
												<input type="hidden" name="document_dataid" value="<?php echo $kandidat_id; ?>" />
												<div class="form-group">
													<div class="col-md-offset-2 col-sm-8">
														<select class="selectpicker" id="document_name" name="document_name">
															<option value=""></option>
															<option value="Slika">Slika</option>
															<option value="Diploma završene škole">Diploma završene škole</option>
															<option value="Uvjerenje o pripravničkom stažu">Uvjerenje o pripravničkom stažu</option>
															<option value="Uvjerenje o položenom stručnom ispitu">Uvjerenje o polozenom strucnom ispitu</option>
															<option value="Certifikati o poznavanju jezika">Certifikati o poznavanju jezika</option>
															<option value="Dodatni certifikati">Dodatni certifikati</option>
															<option value="Ugovori">Ugovori</option>
															<option value="Ostalo">Ostalo</option>
														</select>
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
									<div class="row">
										<div id="getCandidateStatus" data-id="<?php echo $kandidat_id ?>">

										</div>
									</div>
									<div class="row">
										<div class="col-xs-12">
											<h4 class="idk_info_title"><i class="fa fa-angle-down" aria-hidden="true"></i> Osnovne informacije <span class="pull-right btn material-btn material-btn_success main-container__column edit_candidate_data" data-toggle="modal" data-target="#editAjax" data-id="<?php echo $kandidat_id; ?>" data-kandidat="<?php echo $kandidat_id; ?>" data-type="edit_kandidat" aria-hidden="true"><i class="fa fa-refresh" aria-hidden="true"></i> UREDI</span></h4>
										</div>
									</div>
									<div class="row idk_employee_info">
										<div class="col-md-6">
											<div class="row">
												<strong class="col-sm-4 text-right">Datum prijave:</strong>
												<div class="col-sm-8"><span class="label label-success material-label material-label_success main-container__column"><?php echo $kandidat_datetime; ?></span></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Osobni link:</strong>
												<div class="col-sm-8"><a href="http://crm.job-step.com/registracija/korak2/<?php echo $kandidat_id; ?>/<?php echo $kandidat_check ?>" target="_BLANK"><span class="label label-success material-label material-label_success main-container__column">KORAK 2</span></a></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Za poziciju:</strong>
												<div class="col-sm-8"><span class="label label-success material-label material-label_success main-container__column"><?php echo $kandidat_prijava_na; ?></span></div>
											</div>
											<?php
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
											?>
											<div class="row">
												<strong class="col-sm-4 text-right">URL:</strong>
												<div class="col-sm-8"><span class="label label-info material-label material-label_info main-container__column"><?php echo $lg_url; ?></span></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">URL Opis:</strong>
												<div class="col-sm-8"><span class="label label-info material-label material-label_info main-container__column"><?php echo $lg_desc; ?></span></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Ime:</strong>
												<div class="col-sm-8"><?php echo $kandidat_ime; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Prezime:</strong>
												<div class="col-sm-8"><?php echo $kandidat_prezime; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Email:</strong>
												<div class="col-sm-8"><a href="mailto:<?php echo $kandidat_email; ?>"><?php echo $kandidat_email; ?></a></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Datum rođenja:</strong>
												<div class="col-sm-8"><?php echo $kandidat_datumrodjenja; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Spol:</strong>
												<div class="col-sm-8"><?php echo $kandidat_spol; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Mjesto rođenja:</strong>
												<div class="col-sm-8"><?php echo $kandidat_mjestorodjenja; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Država rođenja:</strong>
												<div class="col-sm-8"><?php echo $kandidat_drzavarodjenja; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Državljanstvo:</strong>
												<div class="col-sm-8"><?php echo $kandidat_drzavljanstvo; ?></div>
											</div>
										</div>
										<div class="col-md-6">
											<div class="row">
												<strong class="col-sm-4 text-right">Adresa:</strong>
												<div class="col-sm-8"><?php echo $kandidat_adresa; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Grad:</strong>
												<div class="col-sm-8"><?php echo $kandidat_grad; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Poštanski broj:</strong>
												<div class="col-sm-8"><?php echo $kandidat_pbroj; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Država:</strong>
												<div class="col-sm-8"><?php echo $kandidat_drzava; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Vozačka dozvola:</strong>
												<div class="col-sm-8"><?php echo $kandidat_vozacka_dozvola; ?></div>
											</div>
										</div>
									</div>
									<br/>
									<br/>
									<br/>
									<br/>

									<!-- Modal contact info -->
									<div class="modal material-modal material-modal_success fade text-left" id="contactAdd">
										<div class="modal-dialog ">
											<div class="modal-content material-modal__content">
												<div class="modal-header material-modal__header">
													<button class="close material-modal__close" data-dismiss="modal">&times;</button>
													<h4 class="modal-title material-modal__title">Dodaj novi kontakt kandidatu</h4>
												</div>
												<div class="modal-body material-modal__body">
												<form action="<?php getSiteURL(); ?>kandidati.php?page=add_kandidat_kontakt" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
													<input type="hidden" name="kki_kandidat_id" value="<?php echo $kandidat_id; ?>">
													<div class="form-group">
														<label for="kki_grupa" class="col-sm-3 control-label"><span class="text-danger">*</span> Vrsta kontakta:</label>
														<div class="col-sm-9">
															<select class="selectpicker" id="kki_grupa" name="kki_grupa">
																<option value=""></option>
																<option value="1">Telefon</option>
																<option value="2">E-mail</option>
																<option value="3">WEB</option>
																<option value="4">Messangeri</option>
															</select>
														</div>
														<script>
															$(document).ready(function() {
																	$('#telefon').hide();
																	$('#email').hide();
																	$('#web').hide();
																	$('#messangeri').hide();
																$('#kki_grupa').on('change', function (e) {
																	if(($('#kki_grupa').selectpicker('val') == "1"))
																	{
																		$('#telefon').slideDown();
																		$('#email').slideUp();
																		$('#web').slideUp();
																		$('#messangeri').slideUp();
																	}
																	if(($('#kki_grupa').selectpicker('val') == "2"))
																	{
																		$('#telefon').slideUp();
																		$('#email').slideDown();
																		$('#web').slideUp();
																		$('#messangeri').slideUp();
																	}
																	if(($('#kki_grupa').selectpicker('val') == "3"))
																	{
																		$('#telefon').slideUp();
																		$('#email').slideUp();
																		$('#web').slideDown();
																		$('#messangeri').slideUp();
																	}
																	if(($('#kki_grupa').selectpicker('val') == "4"))
																	{
																		$('#telefon').slideUp();
																		$('#email').slideUp();
																		$('#web').slideUp();
																		$('#messangeri').slideDown();
																	}
																});
															});
														</script>
													</div>
													<div id="telefon">
													<div class="form-group">
														<label for="kki_naziv" class="col-sm-3 control-label"><span class="text-danger">*</span> Tip telefona:</label>
														<div class="col-sm-9">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" type="text" name="kki_naziv" id="kki_naziv" placeholder="mobilini\fiksni">
																<span class="materail-input-block__line"></span>
															</div>
														</div>
													</div>
													<div class="form-group">
														<label for="kki_podatak" class="col-sm-3 control-label"><span class="text-danger">*</span> Broj telefona:</label>
														<div class="col-sm-9">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" type="text" name="kki_podatak" id="kki_podatak" placeholder="+38765 555 333">
																<span class="materail-input-block__line"></span>
															</div>
														</div>
													</div>
													</div>
													<div id="email">
														<div class="form-group">
															<label for="kki_naziv_email" class="col-sm-3 control-label"><span class="text-danger">*</span> E-mail:</label>
															<div class="col-sm-9">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input" type="email" name="kki_naziv_email" id="kki_naziv_email" placeholder="E-mail">
																	<span class="materail-input-block__line"></span>
																</div>
															</div>
														</div>
													</div>
													<div id="web">
														<div class="form-group">
															<label for="kki_naziv_web" class="col-sm-3 control-label"><span class="text-danger">*</span> Web:</label>
															<div class="col-sm-9">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input" type="text" name="kki_naziv_web" id="kki_naziv_web" placeholder="Naziv" >
																	<span class="materail-input-block__line"></span>
																</div>
															</div>
														</div>
													</div>
													<div id="messangeri">
														<div class="form-group">
															<label for="kki_naziv_messangeri" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv:</label>
															<div class="col-sm-9">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input" type="text" name="kki_naziv_messangeri" id="kki_naziv_messangeri" placeholder="Naziv" >
																	<span class="materail-input-block__line"></span>
																</div>
															</div>
														</div>
														<div class="form-group">
															<label for="kki_podatak_messangeri" class="col-sm-3 control-label"><span class="text-danger">*</span> Kontakt:</label>
															<div class="col-sm-9">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input" type="text" name="kki_podatak_messangeri" id="kki_podatak_messangeri" placeholder="Kontakt" >
																	<span class="materail-input-block__line"></span>
																</div>
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
									<div class="row">
										<div class="col-xs-12">
											<h4 class="idk_info_title"><i class="fa fa-angle-down" aria-hidden="true"></i> Kontakt informacije <span class="pull-right btn material-btn material-btn_success main-container__column" data-toggle="modal" data-target="#contactAdd"><i class="fa fa-plus" aria-hidden="true"></i> DODAJ</span></h4>
										</div>
									</div>
									<div class="row">
										<div class="col-md-12">
											<table class="table table-hover">
												<thead>
													<tr>

														<th class="text-center">Vrsta</th>
														<th class="text-center">Naziv</th>
														<th class="text-center">Kontakt</th>
														<th class="text-right">Izbriši / Uredi</th>
													</tr>
												</thead>
												<tbody>
													<?php
														$select_query = $db->prepare("
																			SELECT kki_id, kki_grupa, kki_naziv, kki_podatak
																			FROM idk_kandidat_kontakt_info
																			WHERE kki_kandidat_id = :kki_kandidat_id");

														$select_query->execute(array(
																		':kki_kandidat_id' => $kandidat_id));

														while($select_row = $select_query->fetch()) {

															$kki_id = $select_row['kki_id'];
															$kki_grupa = $select_row['kki_grupa'];
															if($kki_grupa == 1){
																$kki_grupa = "Telefon";
															}elseif($kki_grupa == 2){
																$kki_grupa = "E-mail";
															}elseif($kki_grupa == 3){
																$kki_grupa = "Web";
															}elseif($kki_grupa == 4){
																$kki_grupa = "Messangeri";
															}else{};
															$kki_naziv = $select_row['kki_naziv'];
															$kki_podatak = $select_row['kki_podatak'];
													?>
													<tr>
														<td class="text-center"><?php echo $kki_grupa; ?></td>
														<td class="text-center"><?php echo $kki_naziv; ?></td>
														<td class="text-center"><?php echo $kki_podatak; ?></td>
														<td class="text-right">
														<a href="<?php getSiteURL(); ?>kandidati?page=delete_kkinfo&id=<?php echo $kki_id; ?>&check=<?php echo $kandidat_check; ?>&kandidatid=<?php echo $kandidat_id;?>" class="material-dropdown-menu__link"><i class="fa fa-times" aria-hidden="true"></i></a>

														<i class="fa fa-outdent idk_edit_candidate_do edit_candidate_data" data-toggle="modal" data-target="#editAjax" data-id="<?php echo $kki_id; ?>" data-kandidat="<?php echo $kandidat_id; ?>" data-type="edit_kandidat_kontakt" aria-hidden="true"></i>

														</td>
													</tr>
													<?php } ?>
												</tbody>
											</table>
										</div>
									</div>
									<br/>
									<br/>
									<br/>
									<br/>


									<!-- EDIT MODAL -->
									<div class="modal material-modal material-modal_success fade text-left" id="editAjax">
										<div class="modal-dialog ">
											<div class="modal-content material-modal__content">
												<div class="modal-header material-modal__header">
													<button class="close material-modal__close" data-dismiss="modal">&times;</button>
													<h4 class="modal-title material-modal__title">Uredi</h4>
												</div>
												<div class="modal-body material-modal__body">
													<div id="loader" style="display: none; text-align: center;">
													<!-- ajax loader -->
													<img src="ajax-loader.gif" width="100">
													</div>
													<div id="loaderContent"></div>
												</div>
												<div class="modal-footer material-modal__footer">
												</div>
											</div>
										</div>
									</div>



									<!-- Modal education add -->
									<div class="modal material-modal material-modal_success fade text-left" id="educationAdd">
										<div class="modal-dialog ">
											<div class="modal-content material-modal__content">
												<div class="modal-header material-modal__header">
													<button class="close material-modal__close" data-dismiss="modal">&times;</button>
													<h4 class="modal-title material-modal__title">Dodaj edukaciju/školovanje</h4>
												</div>
												<div class="modal-body material-modal__body">
													<form action="<?php getSiteURL(); ?>kandidati.php?page=add_kandidat_edukacija" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
														<input type="hidden" name="ke_kandidat_id" value="<?php echo $kandidat_id; ?>">
														<input type="hidden" name="ke_kandidat_check" value="<?php echo $kandidat_check; ?>">
														<div class="form-group">
															<label for="ke_datumod" class="col-sm-4 control-label"><span class="text-danger">*</span> Datum od:</label>
															<div class="col-sm-8">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input" type="text" name="ke_datumod" id="ke_datumod" class="monthPicker" placeholder="Datum od" required>
																	<span class="materail-input-block__line"></span>
																</div>
															</div>
															<style>
															.ui-datepicker-calendar {
																display: none;
															}
															</style>
															<script>
															$('.ui-datepicker-calendar').hide();
															$(document).ready(function(){
																$("#ke_datumod").datepicker({
																	dateFormat: 'mm.yy',
																	changeMonth: true,
																	changeYear: true,
																	yearRange: '1940:<?php echo date('Y'); ?>',
																	showButtonPanel: true,
																	onClose: function(dateText, inst) {
																		var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
																		var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
																		$(this).val($.datepicker.formatDate('mm-yy', new Date(year, month, 1)));
																	}
																});
																$(".monthPicker").focus(function () {
																	$(".ui-datepicker-calendar").addClass('display_none');
																	$(".ui-datepicker-calendar").hide();
																	$("#ui-datepicker-div").position({
																		my: "center top",
																		at: "center bottom",
																		of: $(this)
																	});
																});

															});

															</script>
														</div>
														<div class="form-group">
															<label for="ke_datumdo" class="col-sm-4 control-label"><span class="text-danger">*</span> Datum do:</label>
															<div class="col-sm-5">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input monthPicker" type="text" name="ke_datumdo" id="ke_datumdo" placeholder="Datum do" required>
																	<span class="materail-input-block__line"></span>
																</div>
															</div>
															<div class="col-sm-3">
																<div class="main-container__column material-checkbox-group material-checkbox-group_primary idk_margin_top10">
																	<input type="checkbox" id="ke_datumdo_aktuelno" name="ke_datumdo_aktuelno" class="material-checkbox">
																	<label class="material-checkbox-group__label" for="ke_datumdo_aktuelno">Aktuelno</label>
																</div>
															</div>
															<script>
															$( document ).ready(function() {
																$('#ke_datumdo_aktuelno').click(function()
																{
																	//If checkbox is checked then disable or enable input
																	if ($(this).is(':checked'))
																	{
																		$("#ke_datumdo").removeAttr("disabled");
																		$("#ke_datumdo").attr("disabled","disabled");
																		$("#ke_datumdo").val('00-00-0000');
																	}
																	//If checkbox is unchecked then disable or enable input
																	else
																	{
																		$("#ke_datumdo").removeAttr("disabled");
																		$("#ke_datumdo").val('');
																	}
																});
															});
															</script>
															<script>
															$('.ui-datepicker-calendar').hide();
															$(document).ready(function(){
																$("#ke_datumdo").datepicker({
																	dateFormat: 'mm.yy',
																	changeMonth: true,
																	changeYear: true,
																	yearRange: '1940:<?php echo date('Y'); ?>',
																	showButtonPanel: true,
																	onClose: function(dateText, inst) {
																		var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
																		var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
																		$(this).val($.datepicker.formatDate('mm-yy', new Date(year, month, 1)));
																	}
																});
																$(".monthPicker").focus(function () {
																	$(".ui-datepicker-calendar").addClass('display_none');
																	$(".ui-datepicker-calendar").hide();
																	$("#ui-datepicker-div").position({
																		my: "center top",
																		at: "center bottom",
																		of: $(this)
																	});
																});

															});

															</script>
														</div>
														<div class="form-group">
															<label for="ke_naziv_kvalifikacije" class="col-sm-4 control-label"><span class="text-danger">*</span> Naziv kvalifikacije: <i class="fa fa-info-circle fa-lg idk_margin_left10" data-toggle="tooltip" data-placement="right" title="etc. Medicinar ..." aria-hidden="true"></i></label>
															<div class="col-sm-8">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input" type="text" name="ke_naziv_kvalifikacije" id="ke_naziv_kvalifikacije" placeholder="Naziv kvalifikacije" >
																	<span class="materail-input-block__line"></span>
																</div>
															</div>
														</div>
														<div class="form-group">
															<label for="ke_naziv" class="col-sm-4 control-label"><span class="text-danger">*</span> Naziv ustanove: </label>
															<div class="col-sm-8">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input" type="text" name="ke_naziv" id="ke_naziv" placeholder="Naziv " >
																	<span class="materail-input-block__line"></span>
																</div>
															</div>
														</div>
														<div class="form-group">
															<label for="ke_grad" class="col-sm-4 control-label"><span class="text-danger">*</span> Grad:</label>
															<div class="col-sm-8">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input" type="text" name="ke_grad" id="ke_grad" placeholder="Grad" >
																	<span class="materail-input-block__line"></span>
																</div>
															</div>
														</div>
														<div class="form-group">
															<label for="ke_drzava" class="col-sm-4 control-label"><span class="text-danger">*</span> Država:</label>
															<div class="col-sm-8">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input" type="text" name="ke_drzava" id="ke_drzava" placeholder="Država" >
																	<span class="materail-input-block__line"></span>
																</div>
															</div>
														</div>
														<div class="form-group">
															<label for="ke_opis" class="col-sm-4 control-label"><span class="text-danger"></span> Opis:</label>
															<div class="col-sm-8">
																<div class="materail-input-block materail-input-block_success">
																	<textarea class="form-control materail-input material-textarea" name="ke_opis" id="ke_opis"></textarea>
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
									<div class="row">
										<div class="col-xs-12">
											<h4 class="idk_info_title"><i class="fa fa-angle-down" aria-hidden="true"></i> Školovanje/edukacija <span class="pull-right btn material-btn material-btn_success main-container__column" data-toggle="modal" data-target="#educationAdd"><i class="fa fa-plus" aria-hidden="true"></i> DODAJ</span></h4>
										</div>
									</div>
									<div class="row">
										<div class="col-md-12">
											<table class="table table-hover">
												<thead>
													<tr>

														<th class="text-center">Naziv kvalifikacije</th>
														<th class="text-center">Naziv</th>
														<th class="text-center">Od</th>
														<th class="text-center">Do</th>
														<th class="text-center">Grad</th>
														<th class="text-center">Opis</th>
														<th class="text-right">Izbriši / Uredi</th>
													</tr>
												</thead>
												<tbody>
													<?php
														$select_query = $db->prepare("
																			SELECT ke_id, ke_datumod, ke_datumdo, ke_naziv_kvalifikacije, ke_naziv, ke_grad, ke_opis
																			FROM idk_kandidat_edukacija
																			WHERE ke_kandidat_id = :ke_kandidat_id");

														$select_query->execute(array(
																		':ke_kandidat_id' => $kandidat_id));

														while($select_row = $select_query->fetch()) {

															$ke_id = $select_row['ke_id'];
															$ke_datumod = $select_row['ke_datumod'];
															$ke_datumod_f = date('m.Y', strtotime($ke_datumod));
															$ke_datumdo = $select_row['ke_datumdo'];
															$ke_naziv_kvalifikacije = $select_row['ke_naziv_kvalifikacije'];
															$ke_naziv = $select_row['ke_naziv'];
															$ke_grad = $select_row['ke_grad'];
															$ke_opis = $select_row['ke_opis'];

															if($ke_datumdo != '0000-00-00'){
																$ke_datumdo_f = date('m.Y', strtotime($ke_datumdo));
															}else{
																$ke_datumdo_f = "Aktuelno";
															}
													?>
													<tr>
														<td class="text-center"><?php echo $ke_naziv_kvalifikacije; ?></td>
														<td class="text-center"><?php echo $ke_naziv; ?></td>
														<td class="text-center"><?php echo $ke_datumod_f; ?></td>
														<td class="text-center"><?php echo $ke_datumdo_f; ?></td>
														<td class="text-center"><?php echo $ke_grad; ?></td>
														<td class="text-center"><?php echo $ke_opis; ?></td>
														<td class="text-right">
														<a href="<?php getSiteURL(); ?>kandidati?page=delete_kandidat_edukacija&id=<?php echo $ke_id; ?>&check=<?php echo $kandidat_check; ?>&kandidatid=<?php echo $kandidat_id;?>" class="material-dropdown-menu__link"><i class="fa fa-times" aria-hidden="true"></i></a>

														<i class="fa fa-outdent idk_edit_candidate_do edit_candidate_data" data-toggle="modal" data-target="#editAjax" data-id="<?php echo $ke_id; ?>" data-kandidat="<?php echo $kandidat_id; ?>" data-type="edit_kandidat_edukacija" aria-hidden="true"></i>

														</td>
													</tr>
													<?php } ?>
												</tbody>
											</table>
										</div>
									</div>
									<br/>
									<br/>
									<br/>
									<br/>



									<!-- Modal work experience add -->
									<div class="modal material-modal material-modal_success fade text-left" id="workAdd">
										<div class="modal-dialog ">
											<div class="modal-content material-modal__content">
												<div class="modal-header material-modal__header">
													<button class="close material-modal__close" data-dismiss="modal">&times;</button>
													<h4 class="modal-title material-modal__title">Dodaj radno iskustvo</h4>
												</div>
												<div class="modal-body material-modal__body">
													<form action="<?php getSiteURL(); ?>kandidati.php?page=add_kandidat_iskustvo" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
														<input type="hidden" name="kri_kandidat_id" value="<?php echo $kandidat_id; ?>">
														<input type="hidden" name="kri_kandidat_check" value="<?php echo $kandidat_check; ?>">
														<div class="form-group">
															<label for="kri_darum_od" class="col-sm-3 control-label"><span class="text-danger">*</span> Datum od:</label>
															<div class="col-sm-9">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input monthPicker" type="text" name="kri_darum_od" id="kri_darum_od" placeholder="Datum od" required>
																	<span class="materail-input-block__line"></span>
																</div>
															</div>
															<style>
															.ui-datepicker-calendar {
																display: none;
															}
															</style>
															<script>
															$('.ui-datepicker-calendar').hide();
															$(document).ready(function(){
																$("#kri_darum_od").datepicker({
																	dateFormat: 'mm.yy',
																	changeMonth: true,
																	changeYear: true,
																	yearRange: '1940:<?php echo date('Y'); ?>',
																	showButtonPanel: true,
																	onClose: function(dateText, inst) {
																		var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
																		var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
																		$(this).val($.datepicker.formatDate('mm-yy', new Date(year, month, 1)));
																	}
																});
																$(".monthPicker").focus(function () {
																	$(".ui-datepicker-calendar").addClass('display_none');
																	$(".ui-datepicker-calendar").hide();
																	$("#ui-datepicker-div").position({
																		my: "center top",
																		at: "center bottom",
																		of: $(this)
																	});
																});

															});

															</script>
														</div>
															<script>
															$('.ui-datepicker-calendar').hide();
															$(document).ready(function(){
																$("#kri_datum_do").datepicker({
																	dateFormat: 'mm.yy',
																	changeMonth: true,
																	changeYear: true,
																	yearRange: '1940:<?php echo date('Y'); ?>',
																	showButtonPanel: true,
																	onClose: function(dateText, inst) {
																		var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
																		var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
																		$(this).val($.datepicker.formatDate('mm-yy', new Date(year, month, 1)));
																	}
																});
																$(".monthPicker").focus(function () {
																	$(".ui-datepicker-calendar").addClass('display_none');
																	$(".ui-datepicker-calendar").hide();
																	$("#ui-datepicker-div").position({
																		my: "center top",
																		at: "center bottom",
																		of: $(this)
																	});
																});

															});

															</script>
														<div class="form-group">
															<label for="kri_datum_do" class="col-sm-3 control-label"><span class="text-danger">*</span> Datum do:</label>
															<div class="col-sm-6">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input monthPicker" type="text" name="kri_datum_do" id="kri_datum_do" placeholder="Datum do" required>
																	<span class="materail-input-block__line"></span>
																</div>
															</div>
															<div class="col-sm-3">
																<div class="main-container__column material-checkbox-group material-checkbox-group_primary idk_margin_top10">
																	<input type="checkbox" id="kri_datum_do_aktuelno" name="kri_datum_do_aktuelno" class="material-checkbox">
																	<label class="material-checkbox-group__label" for="kri_datum_do_aktuelno">Aktuelno</label>
																</div>
															</div>
															<script>
															$( document ).ready(function() {
																$('#kri_datum_do_aktuelno').click(function()
																{
																	//If checkbox is checked then disable or enable input
																	if ($(this).is(':checked'))
																	{
																		$("#kri_datum_do").removeAttr("disabled");
																		$("#kri_datum_do").attr("disabled","disabled");
																		$("#kri_datum_do").val('0000-00-00');
																	}
																	//If checkbox is unchecked then disable or enable input
																	else
																	{
																		$("#kri_datum_do").removeAttr("disabled");
																		$("#kri_datum_do").val('');
																	}
																});
															});
															</script>

														</div>
														<div class="form-group">
															<label for="kri_pozicija" class="col-sm-3 control-label"><span class="text-danger">*</span> Pozicija: <i class="fa fa-info-circle fa-lg idk_margin_left10" data-toggle="tooltip" data-placement="right" title="etc. Medicinar ..." aria-hidden="true"></i></label>
															<div class="col-sm-9">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input" type="text" name="kri_pozicija" id="kri_pozicija" placeholder="Pozicija" required>
																	<span class="materail-input-block__line"></span>
																</div>
															</div>
														</div>
														<div class="form-group">
															<label for="kri_naziv" class="col-sm-3 control-label"><span class="text-danger"></span> Naziv poslodavca:</label>
															<div class="col-sm-9">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input" type="text" name="kri_naziv" id="kri_naziv" placeholder="Naziv poslodavca" >
																	<span class="materail-input-block__line"></span>
																</div>
															</div>
														</div>

														<div class="form-group">
															<label for="kri_grad" class="col-sm-3 control-label"><span class="text-danger"></span> Grad:</label>
															<div class="col-sm-9">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input" type="text" name="kri_grad" id="kri_grad" placeholder="Grad" >
																	<span class="materail-input-block__line"></span>
																</div>
															</div>
														</div>
														<!--
														<div class="form-group">
															<label for="kri_drzava" class="col-sm-3 control-label"><span class="text-danger"></span> Država:</label>
															<div class="col-sm-9">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input" type="text" name="kri_drzava" id="kri_drzava" placeholder="Država" >
																	<span class="materail-input-block__line"></span>
																</div>
															</div>
														</div>
														<div class="form-group">
															<label for="kri_adresa" class="col-sm-3 control-label"><span class="text-danger"></span> Adresa:</label>
															<div class="col-sm-9">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input" type="text" name="kri_adresa" id="kri_adresa" placeholder="Adresa" >
																	<span class="materail-input-block__line"></span>
																</div>
															</div>
														</div>
														<div class="form-group">
															<label for="kri_telefon" class="col-sm-3 control-label"><span class="text-danger"></span> Telefon:</label>
															<div class="col-sm-9">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input" type="text" name="kri_telefon" id="kri_telefon" placeholder="Telefon" >
																	<span class="materail-input-block__line"></span>
																</div>
															</div>
														</div>
														<div class="form-group">
															<label for="kri_email" class="col-sm-3 control-label"><span class="text-danger"></span> E-mail:</label>
															<div class="col-sm-9">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input" type="text" name="kri_email" id="kri_email" placeholder="E-mail" >
																	<span class="materail-input-block__line"></span>
																</div>
															</div>
														</div>
														<div class="form-group">
															<label for="kri_web" class="col-sm-3 control-label"><span class="text-danger"></span> WEB:</label>
															<div class="col-sm-9">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input" type="text" name="kri_web" id="kri_web" placeholder="WEB" >
																	<span class="materail-input-block__line"></span>
																</div>
															</div>
														</div>
														-->
														<div class="form-group">
															<label for="kri_opis" class="col-sm-3 control-label"><span class="text-danger"></span> Opis:</label>
															<div class="col-sm-9">
																<div class="materail-input-block materail-input-block_success">
																	<textarea class="form-control materail-input material-textarea" name="kri_opis" id="kri_opis"></textarea>
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
									<div class="row">
										<div class="col-xs-12">
											<h4 class="idk_info_title"><i class="fa fa-angle-down" aria-hidden="true"></i> Radno iskustvo <span class="pull-right btn material-btn material-btn_success main-container__column" data-toggle="modal" data-target="#workAdd"><i class="fa fa-plus" aria-hidden="true"></i> DODAJ</span></h4>
										</div>
									</div>
									<div class="row">
										<div class="col-md-12">
											<table class="table table-hover">
												<thead>
													<tr>

														<th class="text-center">Pozicija</th>
														<th class="text-center">Poslodavac</th>
														<th class="text-center">Grad</th>
														<th class="text-center">Od</th>
														<th class="text-center">Do</th>
														<th class="text-center">Opis</th>
														<th class="text-right">Izbriši / Uredi</th>
													</tr>
												</thead>
												<tbody>
													<?php
														$select_query = $db->prepare("
																			SELECT kri_id, kri_darum_od, kri_datum_do, kri_pozicija, kri_naziv, kri_grad, kri_opis
																			FROM idk_kandidat_radno_iskustvo
																			WHERE kri_kandidat_id = :kri_kandidat_id");

														$select_query->execute(array(
																		':kri_kandidat_id' => $kandidat_id));

														while($select_row = $select_query->fetch()) {

															$kri_id = $select_row['kri_id'];
															$kri_darum_od = $select_row['kri_darum_od'];
															$kri_darum_od_f = date('m.Y', strtotime($kri_darum_od));
															$kri_datum_do = $select_row['kri_datum_do'];
															$kri_pozicija = $select_row['kri_pozicija'];
															$kri_naziv = $select_row['kri_naziv'];
															$kri_grad = $select_row['kri_grad'];
															$kri_opis = $select_row['kri_opis'];

															if($kri_datum_do != '0000-00-00'){
																$kri_datum_do_f = date('m.Y', strtotime($kri_datum_do));
															}else{
																$kri_datum_do_f = "Aktuelno";
															}

													?>
													<tr>
														<td class="text-center"><?php echo $kri_pozicija; ?></td>
														<td class="text-center"><?php echo $kri_naziv; ?></td>
														<td class="text-center"><?php echo $kri_grad; ?></td>
														<td class="text-center"><?php echo $kri_darum_od_f; ?></td>
														<td class="text-center"><?php echo $kri_datum_do_f; ?></td>
														<td class="text-center"><?php echo $kri_opis; ?></td>
														<td class="text-right">
														<a href="<?php getSiteURL(); ?>kandidati?page=delete_kriskustvo&id=<?php echo $kri_id; ?>&check=<?php echo $kandidat_check; ?>&kandidatid=<?php echo $kandidat_id;?>" class="material-dropdown-menu__link"><i class="fa fa-times" aria-hidden="true"></i></a>


														<i class="fa fa-outdent idk_edit_candidate_do edit_candidate_data" data-toggle="modal" data-target="#editAjax" data-id="<?php echo $kri_id; ?>" data-kandidat="<?php echo $kandidat_id; ?>" data-type="edit_kandidat_iskustvo" aria-hidden="true"></i>

														</td>
													</tr>
													<?php } ?>
												</tbody>
											</table>
										</div>
									</div>
									<br/>
									<br/>



									<!-- Modal skills add -->
									<div class="modal material-modal material-modal_success fade text-left" id="skillsAdd">
										<div class="modal-dialog ">
											<div class="modal-content material-modal__content">
												<div class="modal-header material-modal__header">
													<button class="close material-modal__close" data-dismiss="modal">&times;</button>
													<h4 class="modal-title material-modal__title">Dodaj vještine</h4>
												</div>
												<div class="modal-body material-modal__body">
												<form action="<?php getSiteURL(); ?>kandidati.php?page=add_kandidat_vjestine" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
													<input type="hidden" name="kv_kandidat_id" value="<?php echo $kandidat_id; ?>">
													<input type="hidden" name="kv_kandidat_check" value="<?php echo $kandidat_check; ?>">
													<div class="form-group">
														<label for="kv_grupa" class="col-sm-3 control-label"><span class="text-danger">*</span> Tip vještine:</label>
														<div class="col-sm-9">
															<select class="selectpicker" id="kv_grupa" name="kv_grupa">
																<option value=""></option>
																<option value="1">Osnovne vještine</option>
																<option value="2">Digitalne kompetencije</option>
																<option value="3">Dodatne informacije</option>
															</select>
														</div>
													</div>
													<div class="form-group">
														<label for="kv_naziv" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv: <i class="fa fa-info-circle fa-lg idk_margin_left10" data-toggle="tooltip" data-placement="right" title="etc. rad na microsoft alatima, poznavanje rada na računaru ..." aria-hidden="true"></i></label>
														<div class="col-sm-9">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" type="text" name="kv_naziv" id="kv_naziv" placeholder="Naziv " >
																<span class="materail-input-block__line"></span>
															</div>
														</div>
													</div>
													<div class="form-group">
														<label for="kv_opis" class="col-sm-3 control-label"><span class="text-danger"></span> Opis:</label>
														<div class="col-sm-9">
															<div class="materail-input-block materail-input-block_success">
																<textarea class="form-control materail-input material-textarea" name="kv_opis" id="kv_opis"></textarea>
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

									<div class="row">
										<div class="col-xs-12">
											<h4 class="idk_info_title"><i class="fa fa-angle-down" aria-hidden="true"></i> Vještine <span class="pull-right btn material-btn material-btn_success main-container__column" data-toggle="modal" data-target="#skillsAdd"><i class="fa fa-plus" aria-hidden="true"></i> DODAJ</span></h4>
										</div>
									</div>
									<div class="row">
										<div class="col-md-12">
											<table class="table table-hover">
												<thead>
													<tr>

														<th class="text-center">Tip vještine</th>
														<th class="text-center">Naziv</th>
														<th class="text-center">Opis</th>
														<th class="text-right">Izbriši / Uredi</th>
													</tr>
												</thead>
												<tbody>
													<?php
														$select_query = $db->prepare("
																			SELECT kv_id, kv_naziv, kv_grupa, kv_opis
																			FROM idk_kandidat_vjestine
																			WHERE kv_kandidat_id = :kv_kandidat_id");

														$select_query->execute(array(
																		':kv_kandidat_id' => $kandidat_id));

														while($select_row = $select_query->fetch()) {

															$kv_id = $select_row['kv_id'];
															$kv_naziv = $select_row['kv_naziv'];
															$kv_grupa = $select_row['kv_grupa'];
															if($kv_grupa == 1){
																$kv_grupa = "Osnovne vještine";
															}elseif($kv_grupa == 2){
																$kv_grupa = "Digitalne kompetencije";
															}elseif($kv_grupa == 3){
																$kv_grupa = "Dodatne informacije";
															}else{};
															$kv_opis = $select_row['kv_opis'];
													?>
													<tr>

														<td class="text-center"><?php echo $kv_grupa; ?></td>
														<td class="text-center"><?php echo $kv_naziv; ?></td>
														<td class="text-center"><?php echo $kv_opis; ?></td>
														<td class="text-right">
														<a href="<?php getSiteURL(); ?>kandidati?page=delete_kandidat_vjestina&id=<?php echo $kv_id; ?>&kandidatid=<?php echo $kandidat_id;?>" class="material-dropdown-menu__link"><i class="fa fa-times" aria-hidden="true"></i></a>


														<i class="fa fa-outdent idk_edit_candidate_do edit_candidate_data" data-toggle="modal" data-target="#editAjax" data-id="<?php echo $kv_id; ?>" data-kandidat="<?php echo $kandidat_id; ?>" data-type="edit_kandidat_vjestine" aria-hidden="true"></i>

														</td>
													</tr>
													<?php } ?>
												</tbody>
											</table>
										</div>
									</div>
									<br/>
									<br/>



									<!-- Modal languages add -->
									<div class="modal material-modal material-modal_success fade text-left" id="langAdd">
										<div class="modal-dialog ">
											<div class="modal-content material-modal__content">
												<div class="modal-header material-modal__header">
													<button class="close material-modal__close" data-dismiss="modal">&times;</button>
													<h4 class="modal-title material-modal__title">Dodaj vještine</h4>
												</div>
												<div class="modal-body material-modal__body">
													<form action="<?php getSiteURL(); ?>kandidati.php?page=add_kandidat_jezik" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
														<input type="hidden" name="kj_kandidatid" value="<?php echo $kandidat_id; ?>">
														<input type="hidden" name="kj_kandidat_check" value="<?php echo $kandidat_check; ?>">
														<div class="form-group">
															<label for="kj_naziv" class="col-sm-3 control-label"><span class="text-danger">*</span> Jezik:</label>
															<div class="col-sm-9">
																<select class="selectpicker" id="kj_naziv" name="kj_naziv">
																	<option value=""></option>
																	<option value="Engleski">Engleski</option>
																	<option value="Njemački">Njemački</option>
																	<option value="Talijanski">Talijanski</option>
																	<option value="Francuski">Francuski</option>
																	<option value="Španjolski">Španjolski</option>
																	<option value="Arapski">Arapski</option>
																	<option value="Danski">Danski</option>
																</select>
															</div>
														</div>
														<div class="form-group">
															<label for="kj_slusanje" class="col-sm-3 control-label"><span class="text-danger">*</span> Slušanje:</label>
															<div class="col-sm-9">
																<select class="selectpicker" id="kj_slusanje" name="kj_slusanje">
																	<option value=""></option>
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
															<label for="kj_citanje" class="col-sm-3 control-label"><span class="text-danger">*</span> Čitanje:</label>
															<div class="col-sm-9">
																<select class="selectpicker" id="kj_citanje" name="kj_citanje">
																	<option value=""></option>
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
															<label for="kj_govorna_interakcija" class="col-sm-3 control-label"><span class="text-danger">*</span> Govorna interakcija:</label>
															<div class="col-sm-9">
																<select class="selectpicker" id="kj_govorna_interakcija" name="kj_govorna_interakcija">
																	<option value=""></option>
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
															<label for="kj_govorna_produkcija" class="col-sm-3 control-label"><span class="text-danger">*</span> Govorna produkcija:</label>
															<div class="col-sm-9">
																<select class="selectpicker" id="kj_govorna_produkcija" name="kj_govorna_produkcija">
																	<option value=""></option>
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
															<label for="kj_pisanje" class="col-sm-3 control-label"><span class="text-danger">*</span> Pisanje:</label>
															<div class="col-sm-9">
																<select class="selectpicker" id="kj_pisanje" name="kj_pisanje">
																	<option value=""></option>
																	<option value="A1">A1</option>
																	<option value="A2">A2</option>
																	<option value="B1">B1</option>
																	<option value="B2">B2</option>
																	<option value="C1">C1</option>
																	<option value="C2">C2</option>
																</select>
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
									<div class="row">
										<div class="col-xs-12">
											<h4 class="idk_info_title"><i class="fa fa-angle-down" aria-hidden="true"></i> Jezici <span class="pull-right btn material-btn material-btn_success main-container__column" data-toggle="modal" data-target="#langAdd"><i class="fa fa-plus" aria-hidden="true"></i> DODAJ</span></h4>
										</div>
									</div>
									<div class="row">
										<div class="col-md-12">
											<table class="table table-hover">
												<thead>
													<tr>

														<th class="text-center">Jezik</th>
														<th class="text-center">Slušanje</th>
														<th class="text-center">Čitanje</th>
														<th class="text-center">Govorna interakcija</th>
														<th class="text-center">Govorna produkcija</th>
														<th class="text-center">Pisanje</th>
														<th class="text-right">Izbriši / Uredi</th>
													</tr>
												</thead>
												<tbody>
													<?php
														$select_query = $db->prepare("
																			SELECT kj_id, kj_naziv, kj_slusanje, kj_citanje, kj_govorna_interakcija, kj_govorna_produkcija, kj_pisanje
																			FROM idk_kandidat_jezici
																			WHERE kj_kandidatid = :kj_kandidatid");

														$select_query->execute(array(
																		':kj_kandidatid' => $kandidat_id));

														while($select_row = $select_query->fetch()) {

															$kj_id = $select_row['kj_id'];
															$kj_naziv = $select_row['kj_naziv'];
															$kj_slusanje = $select_row['kj_slusanje'];
															$kj_citanje = $select_row['kj_citanje'];
															$kj_govorna_interakcija = $select_row['kj_govorna_interakcija'];
															$kj_govorna_produkcija = $select_row['kj_govorna_produkcija'];
															$kj_pisanje = $select_row['kj_pisanje'];
													?>
													<tr>

														<td class="text-center"><?php echo $kj_naziv; ?></td>
														<td class="text-center"><?php echo $kj_slusanje; ?></td>
														<td class="text-center"><?php echo $kj_citanje; ?></td>
														<td class="text-center"><?php echo $kj_govorna_interakcija; ?></td>
														<td class="text-center"><?php echo $kj_govorna_produkcija; ?></td>
														<td class="text-center"><?php echo $kj_pisanje; ?></td>
														<td class="text-right">
														<a href="<?php getSiteURL(); ?>kandidati?page=delete_kandidat_jezik&id=<?php echo $kj_id; ?>&check=<?php echo $kandidat_check; ?>&kandidatid=<?php echo $kandidat_id;?>" class="material-dropdown-menu__link"><i class="fa fa-times" aria-hidden="true"></i></a>

														<i class="fa fa-outdent idk_edit_candidate_do edit_candidate_data" data-toggle="modal" data-target="#editAjax" data-id="<?php echo $kj_id; ?>" data-kandidat="<?php echo $kandidat_id; ?>" data-type="edit_kandidat_jezik" aria-hidden="true"></i>

														</td>
													</tr>
													<?php } ?>
												</tbody>
											</table>
										</div>
									</div>
								</div>
								<div class="tab-pane fade" id="notes">
									<div class="panel-group material-accordion material-accordion_primary" id="accordion1">
										<?php

											$year_query = $db->prepare("
																SELECT YEAR (note_datetime) AS note_datetime_year
																FROM idk_notes
																WHERE note_dataid = :note_dataid AND note_group = :note_group
																GROUP BY YEAR (note_datetime)
																ORDER BY YEAR (note_datetime) DESC");

											$year_query->execute(array(
															':note_dataid' => $kandidat_id,
															':note_group' => 2
															));

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
																		':note_dataid' => $kandidat_id,
																		':note_group' => 2
																		));

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
															<!--
															<a href="#" data="<?php getSiteURL(); ?>kandidati?page=del_note&id=<?php echo $note_id; ?>" data-toggle="modal" data-target="#deleteNoteModal" class="delete_note btn material-btn material-btn_danger main-container__column"><i class="fa fa-trash-o fa-lg" aria-hidden="true"></i></a>
															-->
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
															':document_group' => 2,
															':document_dataid' => $kandidat_id));

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
												<td class="text-center"><a href="<?php getSiteURL(); ?>files/kandidati_doc/<?php echo $document_file; ?>" class="btn material-btn material-btn_success main-container__column" target="_BLANK"><?php echo $document_icon; ?></a></td>
												<td class="text-center"><a href="#" data="<?php getSiteURL(); ?>kandidati?page=del_doc&id=<?php echo $document_id; ?>" data-toggle="modal" data-target="#deleteDocModal" class="delete_doc btn material-btn material-btn_danger main-container__column"><i class="fa fa-trash-o fa-lg" aria-hidden="true"></i></a></td>
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

							unlink("files/kandidati_doc/" . $document_file);

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

						header("Location: " . getSiteURLr() . "kandidati?page=open&id=$document_dataid&mess=3");

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

						//Delete document from db
						$doc_del_query = $db->prepare("
													DELETE FROM idk_notes
													WHERE note_id = :note_id");

						$doc_del_query->execute(array(
											':note_id' => $note_id));

						header("Location: " . getSiteURLr() . "kandidati?page=open&id=$note_dataid&mess=4");

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
					if($getEmployeeStatus == 1){

						$kandidat_id = $_GET['id'];

						//Save
						$query = $db->prepare("
										UPDATE idk_kandidati
										SET kandidat_status = :kandidat_status
										WHERE kandidat_id = :kandidat_id");

						$query->execute(array(
									':kandidat_status' => 3,
									':kandidat_id' => $kandidat_id));

						//Add to LOGS
						$getCandidateFullnameR = getCandidateFullnameR($kandidat_id);
						$log_desc = "Arhivirao kandidata: " . $getCandidateFullnameR . "";
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


						header("Location: " . getSiteURLr() . "kandidati?page=list&mess=4");

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


				case "delete_kkinfo":

						$kki_id = $_GET['id'];
						$kandidat_check = $_GET['check'];
						$kandidat_id = $_GET['kandidatid'];

						//Get note_txt and note_dataid
						$note_open_query = $db->prepare("
													SELECT kki_naziv, kki_kandidat_id, kki_podatak
													FROM idk_kandidat_kontakt_info
													WHERE kki_id = :kki_id");

						$note_open_query->execute(array(
												':kki_id' => $kki_id));

						$note_open = $note_open_query->fetch();

							$kki_naziv = $note_open['kki_naziv'];
							$kki_kandidat_id = $note_open['kki_kandidat_id'];
							$kki_podatak = $note_open['kki_podatak'];


						//Delete  from db
						$doc_del_query = $db->prepare("
													DELETE FROM idk_kandidat_kontakt_info
													WHERE kki_id = :kki_id");

						$doc_del_query->execute(array(
											':kki_id' => $kki_id));


						//Add to LOGS
						$getCandidateFullnameR = getCandidateFullnameR($kandidat_id);
						$log_desc = "Izbrisao (kontakt informaciju) ".$kki_podatak." kandidatu: " . $getCandidateFullnameR . "";
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

						header("Location: kandidati?page=open&id=$kandidat_id");

				break;
				case "delete_kriskustvo":

						$kri_id = $_GET['id'];
						$check = $_GET['check'];
						$kandidat_id = $_GET['kandidatid'];

						//Get note_txt and note_dataid
						$note_open_query = $db->prepare("
													SELECT kri_kandidat_id, kri_pozicija
													FROM idk_kandidat_radno_iskustvo
													WHERE kri_id = :kri_id");

						$note_open_query->execute(array(
												':kri_id' => $kri_id));

						$note_open = $note_open_query->fetch();

							$kri_kandidat_id = $note_open['kri_kandidat_id'];
							$kri_pozicija = $note_open['kri_pozicija'];


						//Delete  from db
						$doc_del_query = $db->prepare("
													DELETE FROM idk_kandidat_radno_iskustvo
													WHERE kri_id = :kri_id");

						$doc_del_query->execute(array(
											':kri_id' => $kri_id));

						//Add to LOGS
						$getCandidateFullnameR = getCandidateFullnameR($kandidat_id);
						$log_desc = "Izbrisao (radno iskustvo) ".$kri_pozicija." kandidatu: " . $getCandidateFullnameR . "";
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

						header("Location: kandidati?page=open&id=$kandidat_id");

				break;
				case "delete_kandidat_edukacija":

						$ke_id = $_GET['id'];
						$kandidat_check = $_GET['check'];
						$kandidat_id = $_GET['kandidatid'];

						//Get note_txt and note_dataid
						$note_open_query = $db->prepare("
													SELECT ke_kandidat_id, ke_naziv_kvalifikacije
													FROM idk_kandidat_edukacija
													WHERE ke_id = :ke_id");

						$note_open_query->execute(array(
												':ke_id' => $ke_id));

						$note_open = $note_open_query->fetch();

							$ke_kandidat_id = $note_open['ke_kandidat_id'];
							$ke_naziv_kvalifikacije = $note_open['ke_naziv_kvalifikacije'];


						//Delete  from db
						$doc_del_query = $db->prepare("
													DELETE FROM idk_kandidat_edukacija
													WHERE ke_id = :ke_id");

						$doc_del_query->execute(array(
											':ke_id' => $ke_id));

						//Add to LOGS
						$getCandidateFullnameR = getCandidateFullnameR($kandidat_id);
						$log_desc = "Izbrisao ".$ke_naziv_kvalifikacije." edukaciju kandidatu: " . $getCandidateFullnameR . "";
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

						header("Location: kandidati?page=open&id=$kandidat_id");

				break;
				case "delete_kandidat_vjestina":

						$kv_id = $_GET['id'];
						$kandidat_id = $_GET['kandidatid'];

						//Get note_txt and note_dataid
						$note_open_query = $db->prepare("
													SELECT kv_kandidat_id, kv_naziv
													FROM idk_kandidat_vjestine
													WHERE kv_id = :kv_id");

						$note_open_query->execute(array(
												':kv_id' => $kv_id));

						$note_open = $note_open_query->fetch();

							$kv_kandidat_id = $note_open['kv_kandidat_id'];
							$kv_naziv = $note_open['kv_naziv'];


						//Delete  from db
						$doc_del_query = $db->prepare("
													DELETE FROM idk_kandidat_vjestine
													WHERE kv_id = :kv_id");

						$doc_del_query->execute(array(
											':kv_id' => $kv_id));

						//Add to LOGS
						$getCandidateFullnameR = getCandidateFullnameR($kandidat_id);
						$log_desc = "Izbrisao ".$kv_naziv." vještinu kandidatu: " . $getCandidateFullnameR . "";
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

						header("Location: kandidati?page=open&id=$kandidat_id");

				break;
				case "delete_kandidat_jezik":

						$kj_id = $_GET['id'];
						$kandidat_check = $_GET['check'];
						$kandidat_id = $_GET['kandidatid'];

						//Get note_txt and note_dataid
						$open_query = $db->prepare("
												SELECT kj_kandidatid, kj_naziv
												FROM idk_kandidat_jezici
												WHERE kj_id = :kj_id");

						$open_query->execute(array(
											':kj_id' => $kj_id));

						$open = $open_query->fetch();

							$kj_kandidatid = $open['kj_kandidatid'];
							$kj_naziv = $open['kj_naziv'];


						//Delete  from db
						$del_query = $db->prepare("
											DELETE FROM idk_kandidat_jezici
											WHERE kj_id = :kj_id");

						$del_query->execute(array(
										':kj_id' => $kj_id));


						//Add to LOGS
						$getCandidateFullnameR = getCandidateFullnameR($kandidat_id);
						$log_desc = "Izbrisao ".$kj_naziv." jezik kandidatu: " . $getCandidateFullnameR . "";
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

						header("Location: kandidati?page=open&id=$kandidat_id");

				break;


				case "add_kandidat_kontakt":

					$kki_kandidat_id = $_POST['kki_kandidat_id'];
					$kki_kandidat_check = $_POST['kki_kandidat_check'];
					$kki_grupa = $_POST['kki_grupa'];

					if($kki_grupa == 1){
						$kki_naziv = $_POST['kki_naziv'];
						$kki_podatak = $_POST['kki_podatak'];
					}elseif ($kki_grupa == 2){
						$kki_naziv = "E-mail";
						$kki_podatak = $_POST['kki_naziv_email'];
					}elseif ($kki_grupa == 3){
						$kki_naziv = "Web";
						$kki_podatak = $_POST['kki_naziv_web'];
					}elseif ($kki_grupa == 4){
						$kki_naziv = $_POST['kki_naziv_messangeri'];
						$kki_podatak = $_POST['kki_podatak_messangeri'];
					}else{
						header("Location: registracija/korak2/$kki_kandidat_id/$kki_kandidat_check");
					};


							//Add kontakt info to db
						$query = $db->prepare("
										INSERT INTO idk_kandidat_kontakt_info
											(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id)
										VALUES
											(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id)");

						$query->execute(array(
									':kki_grupa' => $kki_grupa,
									':kki_naziv' => $kki_naziv,
									':kki_podatak' => $kki_podatak,
									':kki_kandidat_id' => $kki_kandidat_id));

						header("Location: kandidati?page=open&id=$kki_kandidat_id");
				break;


				case "add_kandidat_edukacija":

						$ke_datumod	 = date("Y-m-d", strtotime("01-".$_POST['ke_datumod']));

						if(isset($_POST['ke_datumdo_aktuelno'])){
							$ke_datumdo = "";
						}else{
							$ke_datumdo = date("Y-m-d", strtotime("01-".$_POST['ke_datumdo']));
						}

						$ke_naziv_kvalifikacije = $_POST['ke_naziv_kvalifikacije'];
						$ke_naziv = $_POST['ke_naziv'];
						$ke_grad = $_POST['ke_grad'];
						$ke_drzava = $_POST['ke_drzava'];
						$ke_opis = $_POST['ke_opis'];
						$ke_kandidat_id = $_POST['ke_kandidat_id'];
						$ke_kandidat_check = $_POST['ke_kandidat_check'];


						//Add user to db
						$query = $db->prepare("
										INSERT INTO idk_kandidat_edukacija
											(ke_datumod	, ke_datumdo, ke_naziv_kvalifikacije, ke_naziv, ke_grad, ke_drzava, ke_opis, ke_kandidat_id)
										VALUES
											(:ke_datumod, :ke_datumdo, :ke_naziv_kvalifikacije, :ke_naziv, :ke_grad, :ke_drzava, :ke_opis, :ke_kandidat_id)");

						$query->execute(array(
									':ke_datumod' => $ke_datumod	,
									':ke_datumdo' => $ke_datumdo,
									':ke_naziv_kvalifikacije' => $ke_naziv_kvalifikacije,
									':ke_naziv' => $ke_naziv,
									':ke_grad' => $ke_grad,
									':ke_drzava' => $ke_drzava,
									':ke_opis' => $ke_opis,
									':ke_kandidat_id' => $ke_kandidat_id));

									header("Location: kandidati?page=open&id=$ke_kandidat_id");

				break;

				case "add_kandidat_iskustvo":

						$kri_darum_od = date("Y-m-d", strtotime("01-".$_POST['kri_darum_od']));
						if(isset($_POST['kri_datum_do_aktuelno'])){
							$kri_datum_do = "";
						}else{
							$kri_datum_do = date("Y-m-d", strtotime("01-".$_POST['kri_datum_do']));
						}

						$kri_pozicija = $_POST['kri_pozicija'];
						$kri_naziv = $_POST['kri_naziv'];

						$kri_grad = $_POST['kri_grad'];
						$kri_drzava = $_POST['kri_drzava'];
						$kri_adresa = $_POST['kri_adresa'];
						$kri_telefon = $_POST['kri_telefon'];
						$kri_email = $_POST['kri_email'];
						$kri_web = $_POST['kri_web'];

						$kri_opis = $_POST['kri_opis'];
						$kri_kandidat_id = $_POST['kri_kandidat_id'];
						$kri_kandidat_check = $_POST['kri_kandidat_check'];


						//Add user to db
						$query = $db->prepare("
										INSERT INTO idk_kandidat_radno_iskustvo
											(kri_darum_od, kri_datum_do, kri_pozicija, kri_naziv, kri_grad, kri_drzava, kri_adresa, kri_telefon, kri_email, kri_web, kri_opis, kri_kandidat_id)
										VALUES
											(:kri_darum_od, :kri_datum_do, :kri_pozicija, :kri_naziv, :kri_grad, :kri_drzava, :kri_adresa, :kri_telefon, :kri_email, :kri_web, :kri_opis, :kri_kandidat_id)");

						$query->execute(array(
									':kri_darum_od' => $kri_darum_od,
									':kri_datum_do' => $kri_datum_do,
									':kri_pozicija' => $kri_pozicija,
									':kri_naziv' => $kri_naziv,
									':kri_grad' => $kri_grad,
									':kri_drzava' => $kri_drzava,
									':kri_adresa' => $kri_adresa,
									':kri_telefon' => $kri_telefon,
									':kri_email' => $kri_email,
									':kri_web' => $kri_web,
									':kri_opis' => $kri_opis,
									':kri_kandidat_id' => $kri_kandidat_id));

									header("Location: kandidati?page=open&id=$kri_kandidat_id");

				break;

				case "add_kandidat_vjestine":

						$kv_naziv = $_POST['kv_naziv'];
						$kv_grupa = $_POST['kv_grupa'];
						$kv_opis = $_POST['kv_opis'];
						$kv_kandidat_id = $_POST['kv_kandidat_id'];
						$kv_kandidat_check = $_POST['kv_kandidat_check'];


						//Add user to db
						$query = $db->prepare("
										INSERT INTO idk_kandidat_vjestine
											(kv_naziv, kv_grupa, kv_opis, kv_kandidat_id)
										VALUES
											(:kv_naziv, :kv_grupa, :kv_opis, :kv_kandidat_id)");

						$query->execute(array(
									':kv_naziv' => $kv_naziv,
									':kv_grupa' => $kv_grupa,
									':kv_opis' => $kv_opis,
									':kv_kandidat_id' => $kv_kandidat_id));

						header("Location: kandidati?page=open&id=$kv_kandidat_id");

				break;

				case "add_kandidat_jezik":

						$kj_naziv = $_POST['kj_naziv'];
						$kj_slusanje = $_POST['kj_slusanje'];
						$kj_citanje = $_POST['kj_citanje'];
						$kj_govorna_interakcija = $_POST['kj_govorna_interakcija'];
						$kj_govorna_produkcija = $_POST['kj_govorna_produkcija'];
						$kj_pisanje = $_POST['kj_pisanje'];
						$kj_kandidatid = $_POST['kj_kandidatid'];
						$kj_kandidat_check = $_POST['kj_kandidat_check'];


						//Add user to db
						$query = $db->prepare("
										INSERT INTO idk_kandidat_jezici
											(kj_naziv, kj_slusanje, kj_citanje, kj_govorna_interakcija, kj_govorna_produkcija, kj_pisanje, kj_kandidatid)
										VALUES
											(:kj_naziv, :kj_slusanje, :kj_citanje, :kj_govorna_interakcija, :kj_govorna_produkcija, :kj_pisanje, :kj_kandidatid)");

						$query->execute(array(
									':kj_naziv' => $kj_naziv,
									':kj_slusanje' => $kj_slusanje,
									':kj_citanje' => $kj_citanje,
									':kj_govorna_interakcija' => $kj_govorna_interakcija,
									':kj_govorna_produkcija' => $kj_govorna_produkcija,
									':kj_pisanje' => $kj_pisanje,
									':kj_kandidatid' => $kj_kandidatid));

						header("Location: kandidati?page=open&id=$kj_kandidatid");

				break;

			}
		?>
			<footer><?php getCopyright(); ?></footer>
		</div>
	</div>
</body>
</html>
