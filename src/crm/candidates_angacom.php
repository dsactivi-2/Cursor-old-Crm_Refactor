<?php
	include("includes/functions.php");
	include("includes/common.php");
	
	$getEmployeeStatus = explode( ',' , getEmployeeStatus());
	// Turn off all error reporting
	error_reporting(0);
	if(isset($_REQUEST["page"])) {
		if($_REQUEST["page"] == "=list"){
			$page = "list";
		}else{
			$page = $_REQUEST["page"];
		}
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
	<div id="content" style="margin: auto !important; background: linear-gradient(89.82deg, #57828D 0.17%, rgba(96, 151, 160, 0.65) 99.86%);">
		<div class="container-fluid">
		<?php
			switch ($page){
				
				case "list_ajax":

				
					$starost_od = null;
					$starost_do = null;
					$vozacka_dozvola = null;
					$kj_znanje_njemacki = null;
					$radno_iskustvo = null;
					$filter_skoleImp = null;
					$filter_skoleExp = array();
					$filter_smjerImp = null;
					$filter_smjerExp = array();
					$filter_strukeImp = null;
					$filter_strukeExp = array();
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
						<h1 style="color: white; margin-left: 50px;"><i class="fa fa-user idk_color_green" aria-hidden="true"></i> Kandidaten</h1>
					</div>
					<div class="col-xs-12 text-right">
					<hr>				
					</div>
				</div>
			<div class="row">
				<div class="col-xs-12">
					<div class="content_box" style="border-radius: 15px; margin: 50px;">
							<div class="row">
								<div class="col-xs-12">
									<div class="panel-group material-accordion material-accordion_success" id="accordion1">
										<div class="panel panel-success material-accordion__panel material-accordion__panel" >
											<div class="panel-heading material-accordion__heading">
												<h4 class="panel-title">
												<a class="material-accordion__title" style="margin-bottom:0.3rem; background-color: rgba(96, 151, 160, 0.65) !important;" data-toggle="collapse" data-parent="#accordion1" href="#filterKandidata"><i class="fa fa-search" aria-hidden="true" style = "margin-right: 10px;"></i>Filter</a>
												</h4>
											</div>
											<div id="filterKandidata" class="panel-collapse collapse material-accordion__collapse">
												<div class="panel-body">
													<form action="<?php getSiteURL(); ?>candidates_angacom.php?page=list_ajax" method="post"  http-equiv="Content-type" enctype="multipart/form-data"; charset=utf-8" class="form-horizontal">
													<div class="form-group">
														<label for="kki_naziv_web" class="col-sm-4 control-label">Alter:</label>
														<div class="col-sm-8">
															<div class="row">
																<div class="col-sm-4">
																	<div class="materail-input-block materail-input-block_success">
																		<input class="form-control materail-input" type="number" name="starost_od" id="starost_od" placeholder="aus" value="<?php if($starost_od != null) echo $starost_od; ?>">
																		<span class="materail-input-block__line"></span>
																	</div>
																</div>
																<div class="col-sm-4">
																	<div class="materail-input-block materail-input-block_success">
																		<input class="form-control materail-input" type="number" name="starost_do" id="starost_do" placeholder="zu" value="<?php if($starost_do != null) echo $starost_do; ?>">
																		<span class="materail-input-block__line"></span>
																	</div>
																</div>
															</div>
														</div>
													</div>

													<div class="form-group">
														<label for="kki_naziv_web" class="col-sm-4 control-label">Führerschein:</label>
														<div class="col-sm-8">
															<div class="main-container__column materail-switch materail-switch_primary">
																<input class="materail-switch__element" type="checkbox" id="switch_input1" name="vozacka_dozvola" value="DA" <?php if($vozacka_dozvola == "Da") echo "checked"; ?>>
																<label class="materail-switch__label" for="switch_input1"></label>
															</div>
														</div>
													</div>
													<div class="form-group">
														<label for="filter_kategorija_vozacke" class="col-sm-4 control-label">Fahrkategorien:</label>
														<div class="col-sm-5">
															<select class="selectpicker" id="filter_kategorija_vozacke" name="filter_kategorija_vozacke[]" data-actions-box="true" data-live-search="true" multiple title="Nichts gewählt">
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
														<label for="kki_naziv_web" class="col-sm-4 control-label">Arbeitserfahrung:</label>
														<div class="col-sm-8">
															<div class="main-container__column materail-switch materail-switch_primary">
																<input class="materail-switch__element" type="checkbox" id="switch_input2" name="radno_iskustvo" value="DA" <?php if($radno_iskustvo == "DA") echo "checked"; ?>>
																<label class="materail-switch__label" for="switch_input2"></label>
															</div>
														</div>
													</div>
													<div class="form-group">
														<label for="kki_naziv_web" class="col-sm-4 control-label">Sprache:</label>
														<div class="col-sm-5">
															<select class="selectpicker" id="kj_znanje_njemacki" name="kj_znanje_njemacki">
																<option value="svi" selected>Alles</option>
																<option value="nemainfo" <?php if($kj_znanje_njemacki == "nemainfo") echo "selected"?>>Keine Information</option>
																<option value="Bezznanja" <?php if($kj_znanje_njemacki == "Bezznanja") echo "selected"?>>Ohne Kenntnisse</option>
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
														<label for="filter_struke" class="col-sm-4 control-label">Beruf:</label>
														<div class="col-sm-5">
															<select class="selectpicker" id="filter_struke" name="filter_struke[]" data-actions-box="true" data-live-search="true" multiple title="Nichts gewählt">
																<option data-struka_id="1" value="1" <?php if(in_array(1, $filter_strukeExp)) echo "selected";?>>Kfz-Mechaniker</option>
																<option data-struka_id="2" value="2" <?php if(in_array(2, $filter_strukeExp)) echo "selected";?>>Servicetechniker</option>
																<option data-struka_id="3" value="3" <?php if(in_array(3, $filter_strukeExp)) echo "selected";?>>Facharbeiter im Tiefbau</option>
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
													<div class="form-group" style="display: none;">
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
													<div class="form-group" style="display: none;">
														<label for="filter_smjer" class="col-sm-4 control-label">Smjerovi:</label>
														<div class="col-sm-5">
															<select class="selectpicker" id="filter_smjer" name="filter_smjer[]" data-actions-box="true" data-live-search="true" multiple>
															
															</select>
														</div>
													</div>
												</div>
												<div class="modal-footer material-modal__footer">
													<a id="archive_link" href=""><button class="btn btn-primary material-btn " style="background-color: rgba(96, 151, 160, 0.65); color: white;" type="submit" name="submit">SEARCH</button></a>
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
											"language": {
														url: '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json'
													},
											"aoColumns": [
													{ "width": "10%"  },
													{ "width": "10%"  },
													{ "width": "30%"  },
													{ "width": "10%" },
													{ "width": "10%" },
													{ "width": "30%" }
												],

											"ajax":{
												url :"serversidedata.php?page=lista_kandidata_dn",
												type: "POST",
												data: {"starost_od": '<?php echo $starost_od; ?>',
													   "starost_do": '<?php echo $starost_do; ?>',
													   "vozacka_dozvola": '<?php echo $vozacka_dozvola; ?>',
													   "radno_iskustvo": '<?php echo $radno_iskustvo; ?>',
													   "znanje_njemacki": '<?php echo $kj_znanje_njemacki; ?>',
													   "filter_skole": '<?php echo $filter_skoleImp; ?>',
													   "filter_smjer": '<?php echo $filter_smjerImp; ?>',
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
											<th>Name</th>
											<th>Anmeldedatum</th>
											<th>Führerschein</th>
											<th>Beruf</th>
											<th>Sprache</th> 
										</tr>
									</thead>
								</table>
			
		<?php
				break;			
			}
		?>
			<footer><?php getCopyright(); ?></footer>
		</div>
	</div>
</body>
</html>
<?php }else{						echo '
							<div class="alert material-alert material-alert_danger">
								<h4>NEMATE PRIVILEGIJE!</h4>
								<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
								<br />
							</div>
						';} ?>