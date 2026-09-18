<?php
	include("includes/functions.php");
	include("includes/common.php");
	// Turn off all error reporting
	error_reporting(0);
	if(isset($_REQUEST["page"])) {
		if($_REQUEST["page"] == "list"){
			$page = "list";
		}else{
			$page = $_REQUEST["page"];
		}
	}else{
		header("Location: kandidati_test?page=list");
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php getTitle(); ?></title>

	<?php 
		include('includes/head.php'); 
	?>
	

</head>
<body>
	<?php 
		if (in_array($getUserIp, $getIpWhiteList)){
	?>
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
					if(isset($_COOKIE["archive_status"])){
						$archive_status = 1;
					}else{
						$archive_status = 0;
					}
					
					
					if(isset($_POST["drzava_kan_fil"])){
						$drzava_kan_fil = implode(",",$_POST["drzava_kan_fil"]);
					}else{
						$drzava_kan_fil = "";
					}
					if(isset($_POST["starost_od_kan_fil"])){
						$starost_od_kan_fil = $_POST["starost_od_kan_fil"];
					}else{
						$starost_od_kan_fil = "";
					}
					if(isset($_POST["starost_do_kan_fil"])){
						$starost_do_kan_fil = $_POST["starost_do_kan_fil"];
					}else{
						$starost_do_kan_fil = "";
					}
					if(isset($_POST["vozacka_dozvola_kan_fil"])){
						$vozacka_dozvola_kan_fil = $_POST["vozacka_dozvola_kan_fil"];
					}else{
						$vozacka_dozvola_kan_fil = "";
					}
					if(isset($_POST["radno_iskustvo_kan_fil"])){
						$radno_iskustvo_kan_fil = $_POST["radno_iskustvo_kan_fil"];
					}else{
						$radno_iskustvo_kan_fil = "";
					}
					if(isset($_POST["znanje_njemacki_kan_fil"])){
						$znanje_njemacki_kan_fil = implode(",",$_POST["znanje_njemacki_kan_fil"]);
					}else{
						$znanje_njemacki_kan_fil = "";
					}
					if(isset($_POST["grupe_kan_fil"])){
						$grupe_kan_fil = implode(",",$_POST["grupe_kan_fil"]);
					}else{
						$grupe_kan_fil = "";
					}
					if(isset($_POST["status_kan_fil"])){
						$status_kan_fil = implode(",",$_POST["status_kan_fil"]);
					}else{
						$status_kan_fil = "";
					}
					if(isset($_POST["drzavljanstvo_kan_fil"])){
						$drzavljanstvo_kan_fil = implode(",",$_POST["drzavljanstvo_kan_fil"]);
					}else{
						$drzavljanstvo_kan_fil = "";
					}
					if(isset($_POST["termina_od_kan_fil"])){
						$termina_od_kan_fil = $_POST["termina_od_kan_fil"];
					}else{
						$termina_od_kan_fil = "";
					}
					if(isset($_POST["termina_do_kan_fil"])){
						$termina_do_kan_fil = $_POST["termina_do_kan_fil"];
					}else{
						$termina_do_kan_fil = "";
					}
					if(isset($_POST["boravak_kan_fil"])){
						$boravak_kan_fil = implode(",",$_POST["boravak_kan_fil"]);
					}else{
						$boravak_kan_fil = "";
					}
					if(isset($_POST["skole_kan_fil"])){
						$skole_kan_fil = implode(",",$_POST["skole_kan_fil"]);
					}else{
						$skole_kan_fil = "";
					}
					if(isset($_POST["smjerovi_kan_fil"])){
						$smjerovi_kan_fil = implode(",",$_POST["smjerovi_kan_fil"]);
					}else{
						$smjerovi_kan_fil = "";
					}
					var_dump("0  ".$archive_status."</br>");
					var_dump("1  ".$drzava_kan_fil."</br>");
					var_dump("2  ".$starost_od_kan_fil."</br>");
					var_dump("3  ".$starost_do_kan_fil."</br>");
					var_dump("4  ".$vozacka_dozvola_kan_fil."</br>");
					var_dump("5  ".$radno_iskustvo_kan_fil."</br>");
					var_dump("6  ".$znanje_njemacki_kan_fil."</br>");
					var_dump("7  ".$grupe_kan_fil."</br>");
					var_dump("8  ".$status_kan_fil."</br>");
					var_dump("9  ".$drzavljanstvo_kan_fil."</br>");
					var_dump("10  ".$termina_od_kan_fil."</br>");
					var_dump("11  ".$termina_do_kan_fil."</br>");
					var_dump("12  ".$boravak_kan_fil."</br>");
					var_dump("13  ".$skole_kan_fil."</br>");
					var_dump("14  ".$smjerovi_kan_fil."</br>");
					
		?>
					<div class = "row">
						<div class="col-xs-8">
							<h1><i class="fa fa-user idk_color_green" aria-hidden="true"></i> Kandidati</h1>
						</div>
						<div class="col-xs-4 text-right idk_margin_top10">
							<?php 
								if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "4" , $employee_status)) OR (in_array( "1" , $employee_status))){ 
							?>
								<a href="<?php getSiteURL(); ?>registracija/korak1" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus" aria-hidden="true"></i> <span>Registracija</span></a>
							<?php 
								} 
							?>
						</div>
					</div>
					<hr />
					<div class = "row idk_margin_top10">
						<div class="col-xs-12 text-right">
							<button data-toggle="modal" data-target="#filterKandidati" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive">
								<i class="fa fa-search" aria-hidden="true"></i> 
								<span>FILTER</span>
							</button>
							<?php 
								if($archive_status == 0){
									if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "4" , $employee_status)) OR (in_array( "1" , $employee_status))){
							?>
								<a href="<?php getSiteURL(); ?>do.php?form=show_candidate_archive&enable=1">
									<button class="btn material-btn material-btn-icon-success material-btn main-container__column material-btn-icon-responsive">
										<i class="fa fa-times" aria-hidden="true"></i> 
										ARHIVA
									</button>
								</a>
							<?php
									}
								}else{
									if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "4" , $employee_status)) OR (in_array( "1" , $employee_status))){
							?>
								<a href="<?php getSiteURL(); ?>do.php?form=show_candidate_archive&enable=0">
									<button class="btn material-btn material-btn-icon-success material-btn main-container__column material-btn-icon-responsive">
										<i class="fa fa-times" aria-hidden="true"></i> 
										ARHIVA
									</button>
								</a>
							<?php
									}
								}
							?>
						</div>
					</div>
					<!-- Modal filter -->
					<div class="modal material-modal material-modal_success fade text-left" id="filterKandidati">
						<div class="modal-dialog modal-lg">
							<div class="modal-content material-modal__content">
								<div class="modal-header material-modal__header">
									<button class="close material-modal__close" data-dismiss="modal">&times;</button>
									<h4 class="modal-title material-modal__title"><i style = "margin-right: 10px;" class="fa fa-search" aria-hidden="true"></i>Filter kandidata</h4>
								</div> 
								<div class="modal-body material-modal__body">
									<form action="<?php getSiteURL(); ?>kandidati_test?page=list" enctype="multipart/form-data" method="post" role="form" class="form-horizontal" id = "form_filter_kan">
										<div class="form-group">
											<div class="col-md-offset-2 col-sm-8 text-center">
												<label for="drzava_kan_fil" class="col-sm-4 control-label">
													Država:
												</label>
												<div class="col-sm-8">
													<div class="">
														<select class="selectpicker" title = "Odaberite državu" data-actions-box="true" data-live-search = "true" id="drzava_kan_fil" name="drzava_kan_fil[]" data-selected-text-format = "count > 2" multiple>
															<option value = "387">BiH</option>
															<option value = "381">SRB</option>
															<option value = "49">DE</option>
															<option value = "ostalo">Ostalo</option>
														</select>
													</div>
												</div>
											</div>
										</div>
										
										<div class="form-group">
											<div class="col-md-offset-2 col-sm-8 text-center">
												<label for="starost_od_kan_fil" class="col-sm-4 control-label">
													Starost:
												</label>
												<div class="col-sm-8">
													<div class="row">
														<div class="col-sm-4">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" type="number" name="starost_od_kan_fil" id="starost_od_kan_fil" placeholder="Od" value="16" min = "16" max = "79">
																<span class="materail-input-block__line"></span>
															</div>
														</div>
														<div class="col-sm-4">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" type="number" name="starost_do_kan_fil" id="starost_do_kan_fil" placeholder="Do" value="80" min = "16" max = "80">
																<span class="materail-input-block__line"></span>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<script>
											$("#starost_od_kan_fil").change(function() {
												var starost_od_kan_fil = $(this).val();
												var starost_do_kan_fil = $('#starost_do_kan_fil').val();
												if(starost_od_kan_fil < 16){
													$(this).val(16);
												}else{
													if(starost_od_kan_fil < starost_do_kan_fil){
														$(this).val(starost_od_kan_fil);
													}
													else if(starost_od_kan_fil == starost_do_kan_fil){
														$(this).val(parseInt(starost_do_kan_fil) - 1);
													}else{
														$(this).val(parseInt(starost_do_kan_fil) - 1);
													}
												}
											});
											$("#starost_do_kan_fil").change(function() {
												var starost_do_kan_fil = $(this).val();
												var starost_od_kan_fil = $('#starost_od_kan_fil').val();
												if(starost_do_kan_fil > 80){
													$(this).val(80);
												}else{
													if(starost_do_kan_fil > starost_od_kan_fil){
														$(this).val(starost_do_kan_fil);
													}else if(starost_do_kan_fil == starost_od_kan_fil){
														$(this).val(parseInt(starost_od_kan_fil) + 1);
													}else{
														$(this).val(parseInt(starost_od_kan_fil) + 1);
													}
												}
											});
										</script>
										<div class="form-group">
											<div class="col-md-offset-2 col-sm-8 text-center">
												<label for="vozacka_dozvola_kan_fil" class="col-sm-4 control-label">
													Vozačka dozvola:
												</label>
												<div class="col-sm-8">
													<div class="main-container__column materail-switch materail-switch_primary">
														<input class="materail-switch__element" type="checkbox" id="vozacka_dozvola_kan_fil" name="vozacka_dozvola_kan_fil" value="DA">
														<label class="materail-switch__label" for="vozacka_dozvola_kan_fil"></label>
													</div>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="col-md-offset-2 col-sm-8 text-center">
												<label for="radno_iskustvo_kan_fil" class="col-sm-4 control-label">
													Radno iskustvo:
												</label>
												<div class="col-sm-8">
													<div class="main-container__column materail-switch materail-switch_primary">
														<input class="materail-switch__element" type="checkbox" id="radno_iskustvo_kan_fil" name="radno_iskustvo_kan_fil" value="DA">
														<label class="materail-switch__label" for="radno_iskustvo_kan_fil"></label>
													</div>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="col-md-offset-2 col-sm-8 text-center">
												<label for="znanje_njemacki_kan_fil" class="col-sm-4 control-label">
													Znanje njemačkog:
												</label>
												<div class="col-sm-8">
													<div class="">
														<select class="selectpicker" title = "Odaberite nivo jezika" data-actions-box="true" data-live-search = "true" id="znanje_njemacki_kan_fil" name="znanje_njemacki_kan_fil[]" data-selected-text-format = "count > 2" multiple>
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
											</div>
										</div>
										<div class="form-group">
											<div class="col-md-offset-2 col-sm-8 text-center">
												<label for="grupe_kan_fil" class="col-sm-4 control-label">
													Grupe:
												</label>
												<div class="col-sm-8">
													<div class="">
														<select class="selectpicker" title = "Odaberite grupe" data-actions-box="true" data-live-search = "true" id="grupe_kan_fil" name="grupe_kan_fil[]" data-selected-text-format = "count > 2" multiple>
															<?php getGroupList(); ?>
														</select>
													</div>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="col-md-offset-2 col-sm-8 text-center">
												<label for="status_kan_fil" class="col-sm-4 control-label">
													Status:
												</label>
												<div class="col-sm-8">
													<div class="">
														<select class="selectpicker" title = "Odaberite grupe" data-actions-box="true" data-live-search = "true" id="status_kan_fil" name="status_kan_fil[]" data-selected-text-format = "count > 2" multiple>
															<?php getStatusList(); ?>
														</select>
													</div>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="col-md-offset-2 col-sm-8 text-center">
												<label for="drzavljanstvo_kan_fil" class="col-sm-4 control-label">
													Državljanstvo:
												</label>
												<div class="col-sm-8">
													<div class="">
														<select class="selectpicker" title = "Odaberite državljanstvo" data-actions-box="true" data-live-search = "true" id="drzavljanstvo_kan_fil" name="drzavljanstvo_kan_fil[]" data-selected-text-format = "count > 2" multiple>
															<option value="EU">EU</option>
															<option value="NON-EU">NON-EU</option>
														</select>
													</div>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="col-md-offset-2 col-sm-8 text-center">
												<label for="termina_od_kan_fil" class="col-sm-4 control-label">
													Termin za vizu:
												</label>
												<div class="col-sm-8">
													<div class="materail-input-block materail-input-block_success">
														<input class="form-control materail-input monthPicker" autocomplete="off" type="text" name="termina_od_kan_fil" id="termina_od_kan_fil" placeholder="Od">
														<span class="materail-input-block__line"></span>
													</div>
													<div class="materail-input-block materail-input-block_success">
														<input class="form-control materail-input monthPicker" autocomplete="off" type="text" name="termina_do_kan_fil" id="termina_do_kan_fil" placeholder="Do">
														<span class="materail-input-block__line"></span>
													</div>	
												</div>
											</div>
										</div>
										<script>
											$('.ui-datepicker-calendar').hide();
												$(document).ready(function(){
													$("#termina_od_kan_fil").datepicker({
														dateFormat: 'mm.yy',
														changeMonth: true,
														changeYear: true,
														closeText : "Potvrdi",
														yearRange: '2016:<?php echo date("Y"); ?>',
														showButtonPanel: true,
														onClose: function(dateText, inst) {
															var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
															var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
															$(this).val($.datepicker.formatDate('mm-yy', new Date(year, month, 1)));
														}
													});
													$("#termina_do_kan_fil").datepicker({
														dateFormat: 'mm.yy',
														changeMonth: true,
														changeYear: true,
														closeText : "Potvrdi",
														yearRange: '2016:<?php echo date("Y",strtotime("+1")); ?>',
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
											<div class="col-md-offset-2 col-sm-8 text-center">
												<label for="boravak_kan_fil" class="col-sm-4 control-label">
													Boravak u EU:
												</label>
												<div class="col-sm-8">
													<div class="">
														<select class="selectpicker" title = "Odaberite državljanstvo" data-actions-box="true" data-live-search = "true" id="boravak_kan_fil" name="boravak_kan_fil[]" data-selected-text-format = "count > 2" multiple>
															<?php 
																$get_countries = $db->prepare("SELECT boravak_eu FROM idk_kandidati WHERE boravak_eu != 'NN' GROUP BY boravak_eu ");
																$get_countries->execute();
																while($row_countires = $get_countries->fetch()){
															?>
															<option value="<?php echo $row_countires['boravak_eu']; ?>"><?php echo $row_countires['boravak_eu']; ?></option>
															<?php
																}
															?>
														</select>
													</div>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="col-md-offset-2 col-sm-8 text-center">
												<label for="skole_kan_fil" class="col-sm-4 control-label">
													Škole:
												</label>
												<div class="col-sm-8">
													<div class="">
														<select class="selectpicker" title = "Odaberite škole" data-actions-box="true" data-live-search = "true" id="skole_kan_fil" name="skole_kan_fil[]" data-selected-text-format = "count > 2" multiple>
															<?php 
																$query_skole = $db->prepare("
																	SELECT * FROM idk_skole ORDER BY skola_naziv
																");
																$query_skole->execute();
																
																while($row_skola = $query_skole->fetch()){
															?>
															<option value="<?php echo $row_skola['skola_id']; ?>"><?php echo $row_skola['skola_naziv']; ?></option>
															<?php
																}
															?>
														</select>
													</div>
												</div>
											</div>
										</div>
										<script>
											$("#skole_kan_fil").change(function() {
												var skola_id_odabrano = $(this).val();
												$.ajax({
													url: 'ajax_data.php?page=posalji_smjer_odabrane_skole_ms',
													type: 'POST',
													data: {'skola_id_odabrano':skola_id_odabrano},
													dataType: 'html',
													success: function(data) {
														$("#smjerovi_kan_fil").html(data).selectpicker('refresh');
													},
													error: function (xhr, ajaxOptions, thrownError) {
														alert(xhr.status);
														alert(thrownError);
													}
												});
											});
										</script>
										<div class="form-group">
											<div class="col-md-offset-2 col-sm-8 text-center">
												<label for="smjerovi_kan_fil" class="col-sm-4 control-label">
													Škole:
												</label>
												<div class="col-sm-8">
													<div class="">
														<select class="selectpicker" title = "Odaberite smjerovi" data-actions-box="true" data-live-search = "true" id="smjerovi_kan_fil" name="smjerovi_kan_fil[]" data-selected-text-format = "count > 2" multiple>
															
														</select>
													</div>
												</div>
											</div>
										</div>
										<div class="modal-footer material-modal__footer"> 
											<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
											<button type="submit" class="btn btn-primary material-btn material-btn_success" form="form_filter_kan"><i class="fa fa-search" aria-hidden="true"></i> Završi</button>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="content_box">
								<div class = "row idk_margin_top10">
									<div class="col-xs-12 text-right">
										<script type="text/javascript">
											$(document).ready(function() {
												var table_s = $('#kandidati_pregled').DataTable({

													responsive: true,
													"pageLength": 10,
													"processing": true,
													"serverSide": true,
													"order": [[ 0, "desc" ]],

													"bAutoWidth": false,

													"aoColumns": [
														{ "width": "5%"  },
														{ "width": "5%"  },
														{ "width": "2%"  },
														{ "width": "30%" },
														{ "width": "5%" },
														{ "width": "10%" },
														{ "width": "15%" },
														{ "width": "10%" },
														{ "width": "10%" },
														{ "width": "8%", "bSortable": false }
													],
													"ajax":{
														url :"serversidetest.php",
														type: "POST",
														data:{
															"archive_status": '<?php echo $archive_status; ?>', 
															"drzava_kan_fil": '<?php echo $drzava_kan_fil; ?>', 
															"starost_od_kan_fil": '<?php echo $starost_od_kan_fil; ?>', 
															"starost_do_kan_fil": '<?php echo $starost_do_kan_fil; ?>', 
															"vozacka_dozvola_kan_fil": '<?php echo $vozacka_dozvola_kan_fil; ?>', 
															"radno_iskustvo_kan_fil": '<?php echo $radno_iskustvo_kan_fil; ?>', 
															"znanje_njemacki_kan_fil": '<?php echo $znanje_njemacki_kan_fil; ?>', 
															"grupe_kan_fil": '<?php echo $grupe_kan_fil; ?>', 
															"status_kan_fil": '<?php echo $status_kan_fil; ?>', 
															"drzavljanstvo_kan_fil": '<?php echo $drzavljanstvo_kan_fil; ?>', 
															"termina_od_kan_fil": '<?php echo $termina_od_kan_fil; ?>', 
															"termina_do_kan_fil": '<?php echo $termina_do_kan_fil; ?>', 
															"boravak_kan_fil": '<?php echo $boravak_kan_fil; ?>', 
															"skole_kan_fil": '<?php echo $skole_kan_fil; ?>', 
															"smjerovi_kan_fil": '<?php echo $smjerovi_kan_fil; ?>'
														},
														error: function(data){
															$(".list-grid-error").html(""); 
															$("#list-grid_processing").css("display","none"); 
															console.log(data); 
														},
													}
												});
											});
										</script>
										<table id="kandidati_pregled" class="display" cellspacing="0" width="100%">
											<thead>
												<tr>
													<th>#</th>
													<th>#</th>
													<th>#</th>
													<th>Ime i prezime</th>
													<th>Izvor</th>
													<th class="text-center">CV generisan</th>
													<th>Nalog</th>
													<th class="text-center">Grupa</th>
													<th class="text-center">Status</th>
													<th></th>
												</tr>
											</thead>
										</table>
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
<?php 
		}else{
			echo '
				<div class="alert material-alert material-alert_danger">
					<h4>NEMATE PRIVILEGIJE!</h4>
					<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
					<br />
				</div>
			';
		} 
?>
