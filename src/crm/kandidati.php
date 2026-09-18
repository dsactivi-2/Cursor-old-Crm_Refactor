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
	}else{
		header("Location: kandidati?page=list");
	}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<script src="/components/CandidateProjectionCalendar.js" type="module"></script>
	<script src="/components/Kandidati/RadnoIskustvoUStruci.js?time=<?php echo time(); ?>" type="module"></script>
	<script src="/components/Kandidati/Pokrajina.js?time=<?php echo time(); ?>" type="module"></script>
	<script src="/components/Kandidati/atuPaket.js?time=<?php echo time(); ?>" type="module"></script>
	<script src="/components/Kandidati/BracnoStanje.js" type="module"></script>
	<script src="/components/Kandidati/NacinOdlaska.js?time=<?php echo time(); ?>" type="module"></script>
<?php
    if(isset($_GET["id"]))
    {
        echo "<title>" . getImePrezimeKandidata($_GET["id"]) . " | Kandidati | ";
        getTitle();
        echo "</title>";
    }
    else
    {
        echo "<title>Kandidati | "; 
        getTitle();
        echo "</title>";
    }
?>

	<?php include('includes/head.php'); 
	if (in_array($getUserIp, $getIpWhiteList)){
	?>

	<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
	<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
	<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
	<script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
	<script src="/js/jquery.table2excel.js"></script>
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

				if(isset($_COOKIE['archive_status'])){
					$archive_status = 1;
				}else{
					$archive_status = 0;
				}

		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-user idk_color_green" aria-hidden="true"></i> Kandidati</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
				<?php if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "4" , $employee_status)) OR (in_array( "1" , $employee_status))){ ?>
					<a href="<?php getSiteURL(); ?>registracija/korak1" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus" aria-hidden="true"></i> <span>Registracija</span></a>
				<?php } ?>
				</div>
				<div class="col-xs-12 text-right">
					<hr />
					<button id="exportKandidatesSelected" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-file-excel-o" aria-hidden="true"></i> <span>Export selektovanog</span></button>
					
					<button data-toggle="modal" data-target="#filterKandidati" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-search" aria-hidden="true"></i> <span>FILTER</span></button>
					<?php if($archive_status == 0){ ?>
					<?php if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "4" , $employee_status)) OR (in_array( "1" , $employee_status))){ ?>


					<a href="<?php getSiteURL(); ?>do.php?form=show_candidate_archive&enable=1">
						<button class="btn material-btn material-btn-icon-success material-btn main-container__column material-btn-icon-responsive"><i class="fa fa-times" aria-hidden="true"></i> ARHIVA</button>
					</a>
					<?php } ?>
					<?php }else{ ?>
					<?php if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "4" , $employee_status)) OR (in_array( "1" , $employee_status))){ ?>

					<a href="<?php getSiteURL(); ?>do.php?form=show_candidate_archive&enable=0">
						<button class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-check" aria-hidden="true"></i> ARHIVA</button>
					</a>
					<?php } ?>
					<?php } ?>

					

					<hr />
					<a href="#" class="btn btn-primary material-btn material-btn_success" style="float: right !important;" id="addToProjectBtn"><i class="fa fa-plus" aria-hidden="true"></i> Dodaj u projekat</a>
					<div class="form-group">
						<div class="col-xs-4" style="float: right !important;">
							<select class="selectpicker" id="lg_project" data-live-search="true" name="lg_project">
								<option value="0">Odaberi projekat</option>
								<?php getProjectList(); ?>
							</select>
						</div>
					</div>					
					
					<br/>
					<hr/>
				</div>
				
				<!-- Modal filter -->
				<div class="modal material-modal material-modal_success fade" id="filterKandidati">
					<div class="modal-dialog">
						<div class="modal-content material-modal__content">
							<div class="modal-header material-modal__header">
								<button class="close material-modal__close" data-dismiss="modal">&times;</button>
								<h4 class="modal-title material-modal__title">Filter kandidata</h4>
							</div>
							<div class="modal-body material-modal__body">
								<form action="<?php getSiteURL(); ?>kandidati.php?page=list" method="get" target="_blank" http-equiv="Content-type" enctype="multipart/form-data"; charset=utf-8" class="form-horizontal">
								<input type="hidden" name="page" value="=list" >
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
                                        <select class="selectpicker" data-live-search="true" id="filter_grupe" name="filter_grupe[]" multiple>
											<option value="0">Select / deselect all</option>
    										<?php getGroupList(); ?>
    									</select>
									</div>
								</div>
								<script type="text/javascript">
								function toggleSelectAll(control) {
									var allOptionIsSelected = (control.val() || []).indexOf("0") > -1;
									function valuesOf(elements) {
										return $.map(elements, function(element) {
											return element.value;
										});
									}

									if (control.data('allOptionIsSelected') != allOptionIsSelected) {
										// User clicked 'All' option
										if (allOptionIsSelected) {
											// Can't use .selectpicker('selectAll') because multiple "change" events will be triggered
											control.selectpicker('val', valuesOf(control.find('option')));
										} else {
											control.selectpicker('val', []);
										}
									} else {
										// User clicked other option
										if (allOptionIsSelected && control.val().length != control.find('option').length) {
											// All options were selected, user deselected one option
											// => unselect 'All' option
											control.selectpicker('val', valuesOf(control.find('option:selected[value!=0]')));
											allOptionIsSelected = false;
										} else if (!allOptionIsSelected && control.val().length == control.find('option').length - 1) {
											// Not all options were selected, user selected all options except 'All' option
											// => select 'All' option too
											control.selectpicker('val', valuesOf(control.find('option')));
											allOptionIsSelected = true;
										}
									}
									control.data('allOptionIsSelected', allOptionIsSelected);
								}

								$('#filter_grupe').selectpicker().change(function(){toggleSelectAll($(this));}).trigger('change');
								</script>
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
										echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novog zaposlenika.</div>';
									}elseif($mess == 2){
										echo '<div class="alert material-alert material-alert_danger">Greška: Korisnik kojeg pokuštavate dodati već postoji u bazi podataka.</div>';
									}elseif($mess == 3){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste uredili profil zaposlenika.</div>';
									}elseif($mess == 4){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste arhivirali kandidata.</div>';
									}
								?>
								<script type="text/javascript">
									$(document).ready(function() {
										
										
										
									var table =	$('#idk_table').DataTable({
											responsive: true,
											'columnDefs': [
												{
												'targets': 0,
												'checkboxes': {
													'selectRow': true
												}
												}
											],
											'select': {
												'style': 'multi'
											},												
											"order": [[ 0, "desc" ]],
											"bAutoWidth": false,
											"aoColumns": [
													{ "width": "5%", "bSortable": false },
													{ "width": "5%" },
													{ "width": "5%", "bSortable": false },
													{ "width": "20%" },
													{ "width": "15%" },
													{ "width": "20%" },
													{ "width": "10%" },
													{ "width": "10%" },
													{ "width": "10%", "bSortable": false }
												]
										});
										
										// Handle form submission event 
										$('#exportKandidatesSelected').on('click', function(e){
											
											var form = $("#frm-addToProj");
											
											var rows_selected = table.column(0).checkboxes.selected();
										
											// Iterate over all selected checkboxes
											$.each(rows_selected, function(index, rowId){
												// Create a hidden element 
												$(form).append(
													$('<input>')
													.attr('type', 'hidden')
													.attr('name', 'id[]')
													.val(rowId)
												);
											});
										
											// FOR DEMONSTRATION ONLY
											// The code below is not needed in production
											
											// Output form data to a console     
											$('#selected_rows_kandidates').val(rows_selected.join(","));
											var selectedIds = $('#selected_rows_kandidates').val();
											
											$('#exportajax').load('<?php getSiteUrl(); ?>export_excel.php?prozor=export_kandidata_u_filter&selectedis='+selectedIds+'');
							
											return false;
											
											// Output form data to a console     
											//$('#example-console-form').text($(form).serialize());
											
											// Remove added elements
											$('input[name="id\[\]"]', form).remove();
											
											// Prevent actual form submission
											e.preventDefault();
										}); 
										
										
										
										
									$( "#addToProjectBtn" ).click(function() {
										
											var form = this;
											
											var rows_selected = table.column(0).checkboxes.selected();
										
											// Iterate over all selected checkboxes
											$.each(rows_selected, function(index, rowId){
												// Create a hidden element 
												$(form).append(
													$('<input>')
													.attr('type', 'hidden')
													.attr('name', 'id[]')
													.val(rowId)
												);
											});
										
											// FOR DEMONSTRATION ONLY
											// The code below is not needed in production
											
											// Output form data to a console     
											$('#selected_rows_kandidates').val(rows_selected.join(","));
											var selectedIds = $('#selected_rows_kandidates').val();
											
											//$('#exportajax').load('<?php getSiteUrl(); ?>export_excel.php?prozor=export_kandidata_u_filter&selectedis='+selectedIds+'');
							
											//return false;
											
											// Output form data to a console     
											//$('#example-console-form').text($(form).serialize());
											
											// Remove added elements
											$('input[name="id\[\]"]', form).remove();
											
											// Prevent actual form submission
											//e.preventDefault();										
										
										var projID = $("#lg_project").val();
										$("#proj_id").val(projID);
										$("#selectedrows").val(selectedIds);
										$("#frm-addToProj").submit();
										
									});
										
										
										
										
										
									});


									
									
									
									
									
						

                            
									//	$("#select_all").change(function(){  //"select all" change 
									//	var status = this.checked; // "select all" checked status
									//	$('.checkbox').each(function(){ //iterate all listed checkbox items
									//		this.checked = status; //change ".checkbox" checked status
									//	});
								
                            
								//	$('.checkbox').change(function(){ //".checkbox" change 
								//		//uncheck "select all", if one of the listed checkbox item is unchecked
								//		if(this.checked == false){ //if this item is unchecked
								//			$("#select_all")[0].checked = false; //change "select all" checked status to false
								//		}
								//		
								//		//check "select all" if all checkbox items are checked
								//		if ($('.checkbox:checked').length == $('.checkbox').length ){ 
								//			$("#select_all")[0].checked = true; //change "select all" checked status to true
								//		}
								//	});
									
									
									
								</script>
								<div id="exportajax"></div>	
								<input type="hidden" id="selected_rows_kandidates"></input>
								<form action="<?php getSiteURL(); ?>do.php?form=addToProject" name="frm-addToProj" id="frm-addToProj" method="POST">
									<input id="selectedrows" type="hidden" name="selectedrows"/>
									<input id="proj_id" type="hidden" name="proj_id" value=""/>
									<table id="idk_table" class="display" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th><input type="checkbox" id="select_all" name="select_all"></th>
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
											if($archive_status == 0){
												$upit = "WHERE kandidat_status !=3";
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
												
												//DATUM TERMINA ZA VIZU OPSEG
												$datum_terminaod = "01-".$_GET['datum_terminaod']."";
												$datum_terminado = "30-".$_GET['datum_terminado']."";
												
												if($datum_terminaod !="01-"){
													$datum_termina_od = date("Y-m-d", strtotime($datum_terminaod));
													$datum_termina_do = date("Y-m-d", strtotime($datum_terminado));
													$uslov_pretrage_termin = 'AND datum_termina BETWEEN "'.$datum_termina_od.'" AND "'.$datum_termina_do.'"';
													
												}else{
													$datum_termina_od = "";
													$datum_termina_do = "";
													$uslov_pretrage_termin = '';
												
												}
												
					
												//var_dump($datum_termina_od);
												//echo "<br/>";
												//var_dump($datum_termina_do);												
												if(isset($_GET['filter_grupe'])){
												$filter_grupe = $_GET['filter_grupe'];
													$filter_grupe_f = implode(',', $filter_grupe);
													$uslov_pretrage_grupe = 'AND kandidat_group IN('.$filter_grupe_f.')';
												}else{
													$uslov_pretrage_grupe = '';
												}

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
													$drzavljanstvo = "'EU državljanin','NON-EU državljanin'";
												}else if($filter_drzavljanstvo_f == "NON-EU"){
													$drzavljanstvo = "'NON-EU državljanin'";
												}else if($filter_drzavljanstvo_f == "EU"){
													$drzavljanstvo = "'EU državljanin'";
												}
													$uslov_pretrage_drzavljanstvo = 'AND kandidat_drzavljanstvo_vrsta IN('.$drzavljanstvo.')';
												}else{
													$uslov_pretrage_drzavljanstvo = '';
												}
												
												$filter_boravak = $_GET['filter_boravak'];
												$filter_boravak_f = implode("','", $filter_boravak);
												if(isset($_GET['filter_boravak'])){
													$uslov_pretrage_boravak = "AND boravak_eu IN('".$filter_boravak_f."')";
												}else{
													$uslov_pretrage_boravak = '';
												}
												
												if(isset($_GET['filter_skole'])){
													
													$skole_join = 'JOIN idk_kandidat_edukacija edu ON kandidat_id = edu.ke_kandidat_id';
													$filter_skole = $_GET['filter_skole'];
													$filter_skole_f = implode("','", $filter_skole);
													if(isset($_GET['filter_smjer'])){
														$filter_smjer = $_GET['filter_smjer'];
														$filter_smjer_f = implode("','", $filter_smjer);
														$uslov_pretrage_skola = '';
														$uslov_pretrage_smjer = "AND edu.ke_naziv_kvalifikacije IN ('".$filter_smjer_f."')";
													}else{
														$uslov_pretrage_skola = "AND edu.ke_naziv IN ('".$filter_skole_f."')";
														$uslov_pretrage_smjer = '';
													}
													
												}else{
													$uslov_pretrage_skola = '';
													$uslov_pretrage_smjer = '';
													$skole_join = '';
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

												if($upit !="" OR $uslov_saradnik !=""){$and = "AND";}else{$and = "WHERE";}

												if($vozacka_dozvola == "Da"){
													$uslov_pretrage_vozacka = ''.$and.' kandidat_vozacka_dozvola = "'.$vozacka_dozvola.'"';
												}else{
													$uslov_pretrage_vozacka = ''.$and.' (kandidat_vozacka_dozvola = "Da" OR kandidat_vozacka_dozvola = "Ne")';
												}

												$uslov_pretrage_godine = 'AND (YEAR(NOW()) - YEAR(`kandidat_datumrodjenja`)) BETWEEN '.$starost_od.' AND '.$starost_do.'';
												$uslov_pretrage_status = 'AND kandidat_status IN('.$filter_status_f.')';

											}else{
												$uslov_pretrage_vozacka = "";
												$uslov_pretrage_godine = "";
												$uslov_pretrage_grupe = "";
												$uslov_pretrage_status = "";
												$uslov_pretrage_drzavljanstvo = "";
												$uslov_pretrage_boravak = "";
											}
											/*var_dump("SELECT kandidat_id, kandidat_ime, kandidat_prezime, kandidat_spol, kandidat_jmbg, kandidat_status, kandidat_slika, kandidat_email, kandidat_datetime, kandidat_visitedurl, kandidat_prijava_na, kandidat_group
																FROM idk_kandidati $skole_join
																$upit $uslov_saradnik $uslov_pretrage_vozacka $uslov_pretrage_godine  $uslov_pretrage_grupe $uslov_pretrage_status $uslov_pretrage_drzavljanstvo $uslov_pretrage_termin $uslov_pretrage_boravak $uslov_pretrage_skola $uslov_pretrage_smjer
																ORDER BY kandidat_id ASC");
											exit():*/
											//echo "$upit $uslov_saradnik $uslov_pretrage_vozacka $uslov_pretrage_godine  $uslov_pretrage_statusi $uslov_pretrage_status $uslov_pretrage_drzavljanstvo $uslov_pretrage_termin $uslov_pretrage_boravak";
											
												$query = $db->prepare("
																SELECT kandidat_id, kandidat_ime, kandidat_prezime, kandidat_spol, kandidat_jmbg, kandidat_status, kandidat_slika, kandidat_email, kandidat_datetime, kandidat_visitedurl, kandidat_prijava_na, kandidat_group
																FROM idk_kandidati $skole_join
																$upit $uslov_saradnik $uslov_pretrage_vozacka $uslov_pretrage_godine  $uslov_pretrage_grupe $uslov_pretrage_status $uslov_pretrage_drzavljanstvo $uslov_pretrage_termin $uslov_pretrage_boravak $uslov_pretrage_skola $uslov_pretrage_smjer
																ORDER BY kandidat_id ASC
																");

												$query->execute();
												// var_dump($query->errorInfo());
												// exit();

												$i = 1;
												while($row = $query->fetch()){

													$kandidat_id = $row['kandidat_id'];
													$kandidat_ime = $row['kandidat_ime'];
													$kandidat_prezime = $row['kandidat_prezime'];
													$kandidat_spol = $row['kandidat_spol'];
													$kandidat_jmbg = $row['kandidat_jmbg'];
													$kandidat_group = $row['kandidat_group'];
													$kandidat_email = $row['kandidat_email'];
													$kandidat_datetime = $row['kandidat_datetime'];
													$kandidat_visitedurl = $row['kandidat_visitedurl'];
													$kandidat_prijava_na = $row['kandidat_prijava_na'];


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


													if($row['kandidat_slika'] == "none"){
														$kandidat_slika = "none.jpg";
													}else{
														$kandidat_slika = $row['kandidat_slika'];
													}

													if($row['kandidat_status'] == 0){
														$kandidat_status = '<span class="label label-warning material-label material-label_warning material-label_xs main-container__column">Na provjeri</span>';
													}elseif($row['kandidat_status'] == 1){
														$kandidat_status = '<span class="label label-primary material-label material-label_primary material-label_xs main-container__column">U obradi</span>';
													}elseif($row['kandidat_status'] == 2){
														$kandidat_status = '<span class="label label-success material-label material-label_success material-label_xs main-container__column">Obrađen</span>';
													}elseif($row['kandidat_status'] == 3){
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
														
													$query_iskustvo = $db->prepare("
																	SELECT kri_pozicija
																	FROM idk_kandidat_radno_iskustvo
																	WHERE kri_kandidat_id = :kri_kandidat_id
																	");

													$query_iskustvo->execute(array(
														":kri_kandidat_id" => $kandidat_id
													));
													$iskustvo = "";
													while($row_iskustvo = $query_iskustvo->fetch()){
														$iskustvo .= $row_iskustvo['kri_pozicija'].", ";
													}


											?>
											<?php if(!isset($_GET['search'])){ ?>
											<tr>
												<td class="text-center">
													<?php echo $kandidat_id; ?>
												</td>
												<td class="text-center"><?php echo $kandidat_id; ?><span style="display: none;"><?php echo $iskustvo;?></span></td>
												<td class="text-center"><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>"><img class="idk_profile_img" src="<?php getSiteURL(); ?>files/kandidati/<?php echo $kandidat_slika; ?>"></a></td>
												<td><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>"><?php echo $kandidat_ime; ?> <?php if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "4" , $employee_status)) OR (in_array( "1" , $employee_status))){ ?><?php echo $kandidat_prezime; ?><?php } ?></a></td>
												<td><?php if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "4" , $employee_status)) OR (in_array( "1" , $employee_status))){ ?><?php echo $lg_url; ?><?php } ?></td>
												<td><?php echo $kandidat_prijava_na; ?></td>
												<td class="text-center"><?php if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "4" , $employee_status)) OR (in_array( "1" , $employee_status))){ ?><?php echo '<span class="label label-success material-label material-label_success material-label_xs main-container__column">'.$kg_title.'</span>'; ?><?php } ?></td>
												<td class="text-center"><?php echo $kandidat_status; ?></td>
												<td class="text-center">
													<div class="btn-group material-btn-group">
														<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
														<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
															<li><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori</a></li>
															<?php if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){	?>
															<li><a href="#" class="material-dropdown-menu__link cvshow" data="<?php getSiteURL(); ?>cv?page=open&id=<?php echo $kandidat_id; ?>" data-id="<?php getSiteURL(); ?>cv2?page=open&id=<?php echo $kandidat_id; ?>" data-toggle="modal" data-target="#cvModal"><i class="fa fa-folder-open-o" aria-hidden="true"></i> CV</a></li>
															<?php }else{} ?>

															<li><a href="#" class="material-dropdown-menu__link profilshow" data="<?php getSiteURL(); ?>profil?page=open&id=<?php echo $kandidat_id; ?>" data-id="<?php getSiteURL(); ?>profil2?page=open&id=<?php echo $kandidat_id; ?>" data-toggle="modal" data-target="#profilModal"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Profil</a></li>

															<?php if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){	?>
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
												<td class="text-center">
												<?php echo $kandidat_id; ?>
												</td>
												<td class="text-center"><?php echo $kandidat_id; ?><span style="display: none;"><?php echo $iskustvo;?></span></td>
												<td class="text-center"><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>"><img class="idk_profile_img" src="<?php getSiteURL(); ?>files/kandidati/<?php echo $kandidat_slika; ?>"></a></td>
												<td><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>"><?php echo $kandidat_ime; ?> <?php if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "4" , $employee_status)) OR (in_array( "1" , $employee_status))){ ?><?php echo $kandidat_prezime; ?><?php } ?></a></td>
												<td><?php if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "4" , $employee_status)) OR (in_array( "1" , $employee_status))){ ?><?php echo $lg_url; ?><?php } ?></td>
												<td><?php echo $kandidat_prijava_na; ?></td>
												<td class="text-center"><?php if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "4" , $employee_status)) OR (in_array( "1" , $employee_status))){ ?><?php echo '<span class="label label-success material-label material-label_success material-label_xs main-container__column">'.$kg_title.'</span>'; ?><?php } ?></td>
												<td class="text-center"><?php echo $kandidat_status; ?></td>
												<td class="text-center">
													<div class="btn-group material-btn-group">
														<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
														<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
															<li><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori</a></li>
															<?php if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){	?>
															<li><a href="#" class="material-dropdown-menu__link cvshow" data="<?php getSiteURL(); ?>cv?page=open&id=<?php echo $kandidat_id; ?>" data-id="<?php getSiteURL(); ?>cv2?page=open&id=<?php echo $kandidat_id; ?>" data-toggle="modal" data-target="#cvModal"><i class="fa fa-folder-open-o" aria-hidden="true"></i> CV</a></li>
															<?php }else{} ?>

															<li><a href="#" class="material-dropdown-menu__link profilshow" data="<?php getSiteURL(); ?>profil?page=open&id=<?php echo $kandidat_id; ?>" data-id="<?php getSiteURL(); ?>profil2?page=open&id=<?php echo $kandidat_id; ?>" data-toggle="modal" data-target="#profilModal"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Profil</a></li>

															<?php if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){	?>
															<li class="idk_dropdown_danger"><a href="#" data="<?php getSiteURL(); ?>kandidati?page=archive&id=<?php echo $kandidat_id; ?>" data-toggle="modal" data-target="#archiveModal" class="archive material-dropdown-menu__link"><i class="fa fa-trash-o" aria-hidden="true"></i> Arhiviraj</a></li>
															<?php }else{} ?>

														</ul>
													</div>
												</td>
											</tr>
											<?php }else if($check_german > 0 AND $radno_iskustvo == "DA") { ?>
											<?php if($check_work > 0){ ?>
											<tr>
												<td class="text-center">
												<?php echo $kandidat_id; ?>
												</td>
												<td class="text-center"><?php echo $kandidat_id; ?><span style="display: none;"><?php echo $iskustvo;?></span></td>
												<td class="text-center"><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>"><img class="idk_profile_img" src="<?php getSiteURL(); ?>files/kandidati/<?php echo $kandidat_slika; ?>"></a></td>
												<td><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>"><?php echo $kandidat_ime; ?> <?php if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "4" , $employee_status)) OR (in_array( "1" , $employee_status))){ ?><?php echo $kandidat_prezime; ?><?php } ?></a></td>
												<td><?php if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "4" , $employee_status)) OR (in_array( "1" , $employee_status))){ ?><?php echo $lg_url; ?><?php } ?></td>
												<td><?php echo $kandidat_prijava_na; ?></td>
												<td class="text-center"><?php if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "4" , $employee_status)) OR (in_array( "1" , $employee_status))){ ?><?php echo '<span class="label label-success material-label material-label_success material-label_xs main-container__column">'.$kg_title.'</span>'; ?><?php } ?></td>
												<td class="text-center"><?php echo $kandidat_status; ?></td>
												<td class="text-center">
													<div class="btn-group material-btn-group">
														<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
														<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
															<li><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori</a></li>
															<?php if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){	?>
															<li><a href="#" class="material-dropdown-menu__link cvshow" data="<?php getSiteURL(); ?>cv?page=open&id=<?php echo $kandidat_id; ?>" data-id="<?php getSiteURL(); ?>cv2?page=open&id=<?php echo $kandidat_id; ?>" data-toggle="modal" data-target="#cvModal"><i class="fa fa-folder-open-o" aria-hidden="true"></i> CV</a></li>
															<?php }else{} ?>

															<li><a href="#" class="material-dropdown-menu__link profilshow" data="<?php getSiteURL(); ?>profil?page=open&id=<?php echo $kandidat_id; ?>" data-id="<?php getSiteURL(); ?>profil2?page=open&id=<?php echo $kandidat_id; ?>" data-toggle="modal" data-target="#profilModal"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Profil</a></li>

															<?php if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){	?>
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
												<td class="text-center">
												<?php echo $kandidat_id; ?>
												</td>
												<td class="text-center"><?php echo $kandidat_id; ?><span style="display: none;"><?php echo $iskustvo;?></span></td>
												<td class="text-center"><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>"><img class="idk_profile_img" src="<?php getSiteURL(); ?>files/kandidati/<?php echo $kandidat_slika; ?>"></a></td>
												<td><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>"><?php echo $kandidat_ime; ?> <?php if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "4" , $employee_status)) OR (in_array( "1" , $employee_status))){ ?><?php echo $kandidat_prezime; ?><?php } ?></a></td>
												<td><?php if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "4" , $employee_status)) OR (in_array( "1" , $employee_status))){ ?><?php echo $lg_url; ?><?php } ?></td>
												<td><?php echo $kandidat_prijava_na; ?></td>
												<td class="text-center"><?php if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "4" , $employee_status)) OR (in_array( "1" , $employee_status))){ ?><?php echo '<span class="label label-success material-label material-label_success material-label_xs main-container__column">'.$kg_title.'</span>'; ?><?php } ?></td>
												<td class="text-center"><?php echo $kandidat_status; ?></td>
												<td class="text-center">
													<div class="btn-group material-btn-group">
														<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
														<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
															<li><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori</a></li>
															<?php if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){	?>
															<li><a href="#" class="material-dropdown-menu__link cvshow" data="<?php getSiteURL(); ?>cv?page=open&id=<?php echo $kandidat_id; ?>" data-id="<?php getSiteURL(); ?>cv2?page=open&id=<?php echo $kandidat_id; ?>" data-toggle="modal" data-target="#cvModal"><i class="fa fa-folder-open-o" aria-hidden="true"></i> CV</a></li>
															<?php }else{} ?>

															<li><a href="#" class="material-dropdown-menu__link profilshow" data="<?php getSiteURL(); ?>profil?page=open&id=<?php echo $kandidat_id; ?>" data-id="<?php getSiteURL(); ?>profil2?page=open&id=<?php echo $kandidat_id; ?>" data-toggle="modal" data-target="#profilModal"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Profil</a></li>

															<?php if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){	?>
															<li class="idk_dropdown_danger"><a href="#" data="<?php getSiteURL(); ?>kandidati?page=archive&id=<?php echo $kandidat_id; ?>" data-toggle="modal" data-target="#archiveModal" class="archive material-dropdown-menu__link"><i class="fa fa-trash-o" aria-hidden="true"></i> Arhiviraj</a></li>
															<?php }else{} ?>

														</ul>
													</div>
												</td>
											</tr>
											<?php }else if($radno_iskustvo == "DA") { ?>
											<?php if($check_work > 0){ ?>
											<tr>
												<td class="text-center">
												<?php echo $kandidat_id; ?>
												</td>
												<td class="text-center"><?php echo $kandidat_id; ?><span style="display: none;"><?php echo $iskustvo;?></span></td>
												<td class="text-center"><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>"><img class="idk_profile_img" src="<?php getSiteURL(); ?>files/kandidati/<?php echo $kandidat_slika; ?>"></a></td>
												<td><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>"><?php echo $kandidat_ime; ?> <?php if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "4" , $employee_status)) OR (in_array( "1" , $employee_status))){ ?><?php echo $kandidat_prezime; ?><?php } ?></a></td>
												<td><?php if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "4" , $employee_status)) OR (in_array( "1" , $employee_status))){ ?><?php echo $lg_url; ?><?php } ?></td>
												<td><?php echo $kandidat_prijava_na; ?></td>
												<td class="text-center"><?php if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "4" , $employee_status)) OR (in_array( "1" , $employee_status))){ ?><?php echo '<span class="label label-success material-label material-label_success material-label_xs main-container__column">'.$kg_title.'</span>'; ?><?php } ?></td>
												<td class="text-center"><?php echo $kandidat_status; ?></td>
												<td class="text-center">
													<div class="btn-group material-btn-group">
														<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
														<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
															<li><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori</a></li>
															<?php if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){	?>
															<li><a href="#" class="material-dropdown-menu__link cvshow" data="<?php getSiteURL(); ?>cv?page=open&id=<?php echo $kandidat_id; ?>" data-id="<?php getSiteURL(); ?>cv2?page=open&id=<?php echo $kandidat_id; ?>" data-toggle="modal" data-target="#cvModal"><i class="fa fa-folder-open-o" aria-hidden="true"></i> CV</a></li>
															<?php }else{} ?>

															<li><a href="#" class="material-dropdown-menu__link profilshow" data="<?php getSiteURL(); ?>profil?page=open&id=<?php echo $kandidat_id; ?>" data-id="<?php getSiteURL(); ?>profil2?page=open&id=<?php echo $kandidat_id; ?>" data-toggle="modal" data-target="#profilModal"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Profil</a></li>

															<?php if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){	?>
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
											<script>
												$(".archive").click(function () {
													var addressValue = $(this).attr("data");
													document.getElementById("archive_url").href = addressValue;
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
												$(".cvshow").click(function () {
													var addressValueBs = $(this).attr("data");
													document.getElementById("cv_bosanski").href = addressValueBs;

													var addressValueDe = $(this).data("id");
													document.getElementById("cv_njemacki").href = addressValueDe;
												});
												$(".profilshow").click(function () {
													var addressValueBs = $(this).attr("data");
													document.getElementById("profil_bosanski").href = addressValueBs;

													var addressValueDe = $(this).data("id");
													document.getElementById("profil_njemacki").href = addressValueDe;
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
										</tbody>
									</table>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php
				break;
				
				case "list_ajax":

				
					$starost_od = null;
					$starost_do = null;
					$vozacka_dozvola = null;
					$kj_znanje_njemacki = null;
					$kj_znanje_engleski = null; 
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
					$filter_dipl_statusImp = null;
					$filter_dipl_statusExp = array();
					$filter_vrsta_nostrifikacijeImp = null;
					$filter_vrsta_nostrifikacijeExp = array();

					if(isset($_POST['filter_dipl_status'])){
						$filter_dipl_status = $_POST['filter_dipl_status'];
						$filter_dipl_statusImp = implode(",", $filter_dipl_status);
						
						if($filter_dipl_statusImp != null){
							$filter_dipl_statusExp = explode(",", $filter_dipl_statusImp);
							
						} else {
							$filter_dipl_statusImp = 0;
							$filter_dipl_statusExp = explode(",", $filter_dipl_statusImp);
							
						}
					}

					if(isset($_POST['filter_vrsta_nostrifikacije'])){
						$filter_vrsta_nostrifikacije = $_POST['filter_vrsta_nostrifikacije'];
						$filter_vrsta_nostrifikacijeImp = implode(",", $filter_vrsta_nostrifikacije);

						if($filter_vrsta_nostrifikacijeImp != null){
							$filter_vrsta_nostrifikacijeExp = explode(",", $filter_vrsta_nostrifikacijeImp);
							
						} else {
							$filter_vrsta_nostrifikacijeImp = 0;
							$filter_vrsta_nostrifikacijeExp = explode(",", $filter_vrsta_nostrifikacijeImp);
							
						}
					}
				
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
					if(isset($_POST['kj_znanje_engleski'])){
						$kj_znanje_engleski = $_POST['kj_znanje_engleski']; 
					}
					if(isset($_POST['filter_grupe'])){
						$filter_grupe = $_POST['filter_grupe'];
						$filter_grupeImp = implode(",", $filter_grupe);
						if($filter_grupeImp != null){
							$filter_grupeExp = explode(",", $filter_grupeImp);
						} else {
							$filter_grupeImp = 0;
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
					if(isset($_POST['filter_status_prijave'])){
						$filter_status_prijave = $_POST['filter_status_prijave'];
						$filter_status_prijaveImp = implode(",", $filter_status_prijave);
						if($filter_status_prijaveImp != null){
							$filter_status_prijaveExp = explode(",", $filter_status_prijaveImp);
						} else {
							$filter_status_prijaveImp = 0;
							$filter_status_prijaveExp = explode(",", $filter_status_prijaveImp);
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
						<?php 
						if(getEmployeeStatus() != 20){
							?>
							<a href="<?php getSiteURL(); ?>registracija/korak1" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus" aria-hidden="true"></i> <span>Registracija</span></a>
							<?php 
							if($archive_status == 0){
								?>
								<a href="<?php getSiteURL(); ?>do.php?form=show_candidate_archive&enable=1">
									<button class="btn material-btn material-btn-icon-success material-btn main-container__column material-btn-icon-responsive"><i class="fa fa-times" aria-hidden="true"></i> ARHIVA</button>
								</a><?php
							}else{ ?>
								<a href="<?php getSiteURL(); ?>do.php?form=show_candidate_archive&enable=0">
									<button class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-check" aria-hidden="true"></i> ARHIVA</button>
								</a><?php
							} 
						} ?>
					</div>

					<div class="col-xs-12 text-right">
					<hr>				
					</div>
				</div>
			<div class="row">
				<div class="col-xs-12">
					<div class="content_box">
							<div class="row">
								
								<!-- FILTER KANDIDATA - START -->
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
													<form action="<?php getSiteURL(); ?>kandidati.php?page=list_ajax" method="post"  http-equiv="Content-type" enctype="multipart/form-data"; charset=utf-8" class="form-horizontal">
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
														<label for="kj_znanje_njemacki" class="col-sm-4 control-label">Znanje njemačkog:</label>
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
														<label for="kj_znanje_engleski" class="col-sm-4 control-label">Znanje engleskog:</label>
														<div class="col-sm-5">
															<select class="selectpicker" id="kj_znanje_engleski" name="kj_znanje_engleski">
																<option value="svi" selected>Svi</option>
																<option value="nemainfo" <?php if($kj_znanje_engleski == "nemainfo") echo "selected"?>>Nema informacije</option>
																<option value="Bezznanja" <?php if($kj_znanje_engleski == "Bezznanja") echo "selected"?>>Bez znanja</option>
																<option value="A1" <?php if($kj_znanje_engleski == "A1") echo "selected"?>>A1</option>
																<option value="A2" <?php if($kj_znanje_engleski == "A2") echo "selected"?>>A2</option>
																<option value="B1" <?php if($kj_znanje_engleski == "B1") echo "selected"?>>B1</option>
																<option value="B2" <?php if($kj_znanje_engleski == "B2") echo "selected"?>>B2</option>
																<option value="C1" <?php if($kj_znanje_engleski == "C1") echo "selected"?>>C1</option>
																<option value="C2" <?php if($kj_znanje_engleski == "C2") echo "selected"?>>C2</option>
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
														<label for="filter_status" class="col-sm-4 control-label">Status prijave:</label>
														<div class="col-sm-5">
															<select class="selectpicker" id="filter_status_prijave" name="filter_status_prijave[]" data-actions-box="true" multiple>
																<?php
																	$query = $db->prepare("
																					SELECT status_id, status_naziv
																					FROM idk_kandidat_status_prijave
																					");
																	
																	$query->execute();

																	while($row = $query->fetch()){ ?>
																		<option value="<?php echo $row['status_id'];?>" <?php if(in_array($row['status_id'], $filter_status_prijaveExp)) echo "selected";?>><?php echo $row['status_naziv']; ?></option>;
																<?php }
																?>
																		<option value="0" <?php if(in_array(0, $filter_status_prijaveExp)) echo "selected";?>>Nedefinisan</option>;
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
																<!-- Sedmica je zauzeta na backendu --> 
																<option value="8" <?php if(in_array(8, $filter_izvorExp)) echo "selected";?>>Novi Partner APP</option>
																<option value="0" <?php if(in_array(0, $filter_izvorExp)) echo "selected";?>>Prijava na oglas</option>
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
													<?php 
														$struke_ispis_fil = $db->prepare("
																					SELECT *
																					FROM idk_struke
																				");
														$struke_ispis_fil->execute();
													?>
													<div class="form-group">
														<label for="filter_struke" class="col-sm-4 control-label">Struke:</label>
														<div class="col-sm-5">
															<select class="selectpicker fil_struke" id="filter_struke" name="filter_struke[]" data-actions-box="true" data-live-search="true" multiple>
																<?php
																	while($row_struke_ispis_fil = $struke_ispis_fil->fetch()){
																		$struka_id = $row_struke_ispis_fil['id_struke'];
																		$struka_naziv = $row_struke_ispis_fil['naziv_struke'];

																		if(in_array($struka_id,$filter_strukeExp))
																			echo '<option selected data-struka_id="'.$struka_id.'" value = "'.$struka_id.'">'.$struka_naziv.'</option>';
																		else
																			echo '<option data-struka_id="'.$struka_id.'" value = "'.$struka_id.'">'.$struka_naziv.'</option>';
																	}
																?>
															</select>
														</div>
													</div>
													<script>
														$("#filter_struke").on("change", function(){
															var struka_id = $(this).val();
															$("#filter_skole").attr('disabled', 'disabled');
															
															if(struka_id == null){
																$('#filter_skole').removeAttr('disabled');
															}
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
														<?php
															if(isset($_POST['filter_struke'])){
														?>
																<script>
																	$(document).ready(function(){
																		var struka_id = $('#filter_struke').val();
																		var smjerovi_izabrani=<?php echo json_encode($_POST['filter_smjer']); ?>;
																		$.ajax({
																			url: 'ajax_data.php?page=smjer_struke',
																			type: 'POST',
																			data: {'struka_id':struka_id,
																				'smjer_naziv':smjerovi_izabrani},
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
														<?php
															}
														?>
													<div class="form-group">
														<label for="filter_skole" class="col-sm-4 control-label">Škole:</label>
														<div class="col-sm-5">
															<select class="selectpicker fil_skole" id="filter_skole" name="filter_skole[]" data-actions-box="true" data-live-search="true" multiple>
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
															if(skola_naziv == null ){
																$('#filter_struke').removeAttr('disabled');
															}	
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
														<?php 
															if(isset($_POST['filter_skole'])){
														?>
																<script>
																	$(document).ready(function(){
																		var skola_naziv = <?php echo json_encode($_POST['filter_skole']); ?>;
																		var struke_izabrane = <?php echo json_encode($_POST['filter_smjer']); ?>;
																		$.ajax({
																			url: 'ajax_data.php?page=smjer_skole',
																			type: 'POST',
																			data: {'skola_naziv':skola_naziv,
																				'smjer_naziv':struke_izabrane},
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
														<?php 
															}
														?>
														
													<div class="form-group">
														<label for="filter_smjer" class="col-sm-4 control-label">Smjerovi:</label>
														<div class="col-sm-5">
															<select class="selectpicker" id="filter_smjer" name="filter_smjer[]" data-actions-box="true" data-live-search="true" multiple>
															
															</select>
														</div>
													</div>
													<div class="form-group">
														<label for="filter_dipl_status" class="col-sm-4 control-label">DIPL status:</label>
														<div class="col-sm-5">
															<select id="filter_dipl_status" name="filter_dipl_status[]" class="selectpicker" multiple data-live-search="true" data-actions-box="true">
																<option value = "0" <?php if(in_array('0', $filter_dipl_statusExp)) echo "selected";?> >Nije u Diplu</option>
																<option value = "1" <?php if(in_array('1', $filter_dipl_statusExp)) echo "selected";?> >Lead</option>
																<option value = "2" <?php if(in_array('2', $filter_dipl_statusExp)) echo "selected";?> >Prikupljanje dokumentacije</option>
																<option value = "3" <?php if(in_array('3', $filter_dipl_statusExp)) echo "selected";?> >Poslana pošta</option>
																<option value = "4" <?php if(in_array('4', $filter_dipl_statusExp)) echo "selected";?> >U obradi</option>
																<option value = "5" <?php if(in_array('5', $filter_dipl_statusExp)) echo "selected";?> >Plaćena taksa / Poslana dopuna</option>
																<option value = "6" <?php if(in_array('6', $filter_dipl_statusExp)) echo "selected";?> >Završen</option>
																<option value = "7" <?php if(in_array('7', $filter_dipl_statusExp)) echo "selected";?> >Arhiviran</option>
																<!-- <option value = "1|6" >Neuspješan Kontakt 1</option>
																<option value = "1|2" >Neuspješan Kontakt 3</option>
																<option value = "1|3" >Zainteresiran Lead</option>
																<option value = "1|4" >Nezainteresiran Lead</option>
																<option value = "1|5" >U obradi Lead</option>
																<option value = "1|7" >Neuspješan Lead 1</option>
																<option value = "1|8" >Neuspješan Lead 2</option>
																<option value = "1|9" >Termin Zainteresiran</option>
																<option value = "1|10" >Termin Ostali</option>
																<option value = "1|11" >Lead NL</option>
																<option value = "1|12" >Lead NZ</option>
																<option value = "2|2" >Nepotpuna dokumentacija</option>
																<option value = "2|3" >Na prevodu</option>
																<option value = "2|4" >Dokumentacija kompletirana</option>
																<option value = "2|5" >Prevod završen</option>
																<option value = "2|6" >Poslan zahtjev</option>
																<option value = "2|7" >Potpisan zahtjev</option>
																<option value = "3|2" >Zaprimili dokumentaciju</option>
																<option value = "4|2" >Stigla taksa</option> -->
															</select>
														</div>
													</div>
													<div class="form-group">
														<label for="filter_vrsta_nostrifikacije" class="col-sm-4 control-label">Vrsta nostrifikacije:</label>
														<div class="col-sm-5">
															<select id="filter_vrsta_nostrifikacije" name="filter_vrsta_nostrifikacije[]" class="selectpicker" multiple data-live-search="true" data-actions-box="true">
																<option value = "3" <?php if(in_array('3', $filter_vrsta_nostrifikacijeExp)) echo "selected";?> >Nepoznato</option>
																<option value = "2" <?php if(in_array('2', $filter_vrsta_nostrifikacijeExp)) echo "selected";?> >Evaluacija</option>
																<option value = "1" <?php if(in_array('1', $filter_vrsta_nostrifikacijeExp)) echo "selected";?> >Potpuno priznata</option>
																<option value = "0" <?php if(in_array('0', $filter_vrsta_nostrifikacijeExp)) echo "selected";?> >Djelimično priznata</option>
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
								<!-- FILTER KANDIDATA - END -->
							
								<script type="text/javascript" language="javascript" >
									$(document).ready(function() {
									<?php
									// Show export buttons conditionally
									// (notice the missing 'B' in the 'else' branch)
									if($logged_employee_id == 173 OR $logged_employee_id == 190)
										echo 'let dom = "lBfrtip";';
									else
										echo 'let dom = "lfrtip";';
									?>	
										$('#idk_table').DataTable({
											responsive: true,
											"pageLength": 10,
											"processing": true,
											"serverSide": true,
											"order": [[ 0, "desc" ]],
											"aoColumns": [
													{ "width": "5%" , title: ""},
													{ "width": "7%" , title: ""},
													// { "width": "2%" , title: ""},
													{ "width": "15%", title: "Ime i prezime"},
													{ "width": "15%", title: "Datum ulaska"},
													{ "width": "5%" , title: "Izvor"},
													{ "width": "10%", title: "CV Generisan"},
													{ "width": "15%", title: "Nalog"},
													{ "width": "10%", title: "Grupa"},
													{ "width": "10%", title: "Status"},
													{ "width": "8%" , title: ""},
													{ "width": "0%" , title: "Mobitel", visible: false}
											],               
											lengthMenu: [
												[10, 25, 50, 100, -1],
												[10, 25, 50, 100, 'Sve'],
											],
											"ajax":{
												url :"serversidedata.php?page=lista_kandidata",
												type: "POST",
												data: {"starost_od": '<?php echo $starost_od; ?>',
													   "starost_do": '<?php echo $starost_do; ?>',
													   "vozacka_dozvola": '<?php echo $vozacka_dozvola; ?>',
													   "radno_iskustvo": '<?php echo $radno_iskustvo; ?>',
													   "znanje_njemacki": '<?php echo $kj_znanje_njemacki; ?>',
													   "znanje_engleski": '<?php echo $kj_znanje_engleski; ?>',
													   "filter_grupe": '<?php echo $filter_grupeImp; ?>',
													   "filter_status": '<?php echo $filter_statusImp; ?>',
													   "filter_status_prijave": '<?php echo $filter_status_prijaveImp; ?>',
													   "filter_drzavljanstvo": '<?php echo $filter_drzavljanstvoImp; ?>',
													   "filter_boravak": '<?php echo $filter_boravakImp; ?>',
													   "filter_skole": '<?php echo $filter_skoleImp; ?>',
													   "filter_smjer": '<?php echo $filter_smjerImp; ?>',
													   "filter_izvor": '<?php echo $filter_izvorImp; ?>',
													   "filter_kategorija_vozacke": '<?php echo $filter_kategorija_vozackeImp; ?>',
													   "filter_struke": '<?php echo $filter_strukeImp; ?>',
													   "filter_dipl_status": '<?php echo $filter_dipl_statusImp; ?>',
													   "filter_vrsta_nostrifikacije": '<?php echo $filter_vrsta_nostrifikacijeImp; ?>'
														},
												error: function(data){
													$(".list-grid-error").html(""); 
													$("#list-grid_processing").css("display","none");
											
												},
											},
											buttons: [{
												extend: 'excel',
												className: 'btn material-btn material-btn_primary',
												style: 'margin: 0 10px 0 0;',
												text: "Preuzmi Excel",
												titleAttr: "Preuzmi Excel tabelu",
												exportOptions: {
													orthogonal: "exportxls",
													columns: [0, 3, 11],
												},
												customize: function( xlsx ) {
													var sheet = xlsx.xl.worksheets['sheet1.xml'];

													// Loop over all cells in sheet
													$('row c', sheet).each( function () {
														if($('is t', this).text().includes("ID_FOR_LINK")) {
															const id = $('is t', this).text().split(":")[0].split("=")[1];
															const name = $('is t', this).text().split(":")[1];
															const url = `https://<?php getSiteUrl(); ?>kandidati.php?page=open&amp;id=`;

															// Change cell type to formula
															$(this).attr('t', 'str');

															// Insert hyperlink formula
															$(this).html(`<f>HYPERLINK("${url}${id}","${name}")</f>`);

															// Underline
															$(this).attr( 's', '4' );
														}
													});
												}
											}],
											dom: dom,
											columnDefs: [{
												"targets": "_all",
												"render": function ( data, type, row, meta ) {
													if (type === "exportxls") {
														if(meta.col == 3 /* Kolona 3 je Ime i prezime */){
															// Get ID from row[0], remove html
															const id = row[0].replace(/(<([^>]+)>)/ig,"");
															// This is used above in the customize function to change the cell type to hyperlink
															return `ID_FOR_LINK=${id}: ${data}`;
														}
														return data;
													} else {
														// If not Excel export, display data as it is
														return data;
													}
												}
											}],
										});
										
										// Disable float: left
										$("#idk_table_length").css("float", "none");
									} );
							</script>								
								<table id="idk_table" class="display" cellspacing="0" width="100%">
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

				case "list_ajax_2":

				if(isset($_COOKIE['archive_status'])){
					$archive_status = 1;
				}else{
					$archive_status = 0;
				}
				
				if($archive_status == 0){
					$upit = "WHERE kandidat_status !=3";
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

					if($upit !="" OR $uslov_saradnik !=""){$and = "AND";}else{$and = "WHERE";}

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
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-user idk_color_green" aria-hidden="true"></i> Kandidati</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
				<?php if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "4" , $employee_status)) OR (in_array( "1" , $employee_status))){ ?>
					<a href="<?php getSiteURL(); ?>registracija/korak1" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus" aria-hidden="true"></i> <span>Registracija</span></a>
				<?php } ?>
				</div>
				<div class="col-xs-12 text-right">
					<hr />
					<button data-toggle="modal" data-target="#filterKandidati" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-search" aria-hidden="true"></i> <span>FILTER</span></button>
					<?php if($archive_status == 0){ ?>
					<?php if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "4" , $employee_status)) OR (in_array( "1" , $employee_status))){ ?>


					<a href="<?php getSiteURL(); ?>do.php?form=show_candidate_archive&enable=1">
						<button class="btn material-btn material-btn-icon-success material-btn main-container__column material-btn-icon-responsive"><i class="fa fa-times" aria-hidden="true"></i> ARHIVA</button>
					</a>
					<?php } ?>
					<?php }else{ ?>
					<?php if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "4" , $employee_status)) OR (in_array( "1" , $employee_status))){ ?>

					<a href="<?php getSiteURL(); ?>do.php?form=show_candidate_archive&enable=0">
						<button class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-check" aria-hidden="true"></i> ARHIVA</button>
					</a>
					<?php } ?>
					<?php } ?>

					<hr />
				</div>
				<!-- Modal filter -->
				<div class="modal material-modal material-modal_success fade" id="filterKandidati">
					<div class="modal-dialog">
						<div class="modal-content material-modal__content">
							<div class="modal-header material-modal__header">
								<button class="close material-modal__close" data-dismiss="modal">&times;</button>
								<h4 class="modal-title material-modal__title">Filter kandidata </h4>
							</div>
							<div class="modal-body material-modal__body">
								<form action="<?php getSiteURL(); ?>kandidati.php?page=list" method="get" target="_blank" http-equiv="Content-type" enctype="multipart/form-data"; charset=utf-8" class="form-horizontal">
								<input type="hidden" name="page" value="=list" >
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
										echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novog zaposlenika.</div>';
									}elseif($mess == 2){
										echo '<div class="alert material-alert material-alert_danger">Greška: Korisnik kojeg pokuštavate dodati već postoji u bazi podataka.</div>';
									}elseif($mess == 3){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste uredili profil zaposlenika.</div>';
									}elseif($mess == 4){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste arhivirali kandidata.</div>';
									}
								?>
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
													{ "width": "25%" },
													{ "width": "15%"},
													{ "width": "20%" },
													{ "width": "10%" },
													{ "width": "10%" },
													{ "width": "10%" }
												],

											"ajax":{
												url :"serversidedata2.php",
												type: "POST",
												data: {"uslovi": '<?php echo $uslov_glavni; ?>'},
												error: function(data){
													$(".list-grid-error").html(""); 
													$("#idk_table").append('<tbody class="list-grid-error"><tr><th colspan="3">Ne postoje podaci!</th></tr></tbody>');
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
											<th>Ime i prezime</th>
											<th>URL</th>
											<th>Prijava za</th>
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
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php
				break;	
				
				case "add":
					if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "4" , $employee_status)) OR (in_array( "1" , $employee_status))){
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
					if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "4" , $employee_status)) OR (in_array( "1" , $employee_status))){

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
					if((in_array( "2" , $getEmployeeStatus )) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "5" , $getEmployeeStatus)) OR (in_array( "6" , $getEmployeeStatus)) OR (in_array( "7" , $getEmployeeStatus)) OR (in_array( "9" , $getEmployeeStatus)) OR (in_array( "10" , $getEmployeeStatus)) OR (in_array( "18" , $getEmployeeStatus )) OR (in_array( "1" , $getEmployeeStatus)) OR (in_array( "20" , $getEmployeeStatus))){

					$kandidat_id = $_GET['id'];
					
					Global $logged_employee_id;
					$logged_employee_id = $employee['employee_id'];

					if(isset($_GET['update_not'])){
						$not_id = $_GET['ntf_id'];
						$update_not = $_GET['update_not'];
						updateNotificationStatus($update_not, $not_id);
					}else{}

					$query = $db->prepare("
									SELECT kandidat_ime, kandidat_prezime, kandidat_mobitel, kandidat_spol, kandidat_djevojackoprezime, kandidat_jmbg, kandidat_datumrodjenja, kandidat_mjestorodjenja, kandidat_drzavarodjenja, kandidat_drzavljanstvo, kandidat_bracnostanje, kandidat_adresa, kandidat_grad, kandidat_pbroj, kandidat_drzava, kandidat_email, kandidat_slika, kandidat_datetime,kandidat_check, kandidat_vozacka_dozvola, kandidat_vozacka_kategorija, kandidat_prijava_na, kandidat_visitedurl, kandidat_drzavljanstvo_vrsta, datum_termina, datum_aplikacije, kandidat_viza, kandidat_viza_vrijedi_do, cv_de, profile_de, profile_ba, cv_ba, kandidat_procjenatermina, kandidat_datum_pocetakrada, kandidat_datum_ugovora, kandidat_status, kandidat_zaposlen_kod, kandidat_dipl_id, boravak_eu, kandidat_partner_status, kandidat_partnerid, kandidat_ima_nostrifikaciju, kandidat_nalog_id, kandidat_iskustvo_u_struci, kandidat_bracno_stanje, kandidat_termin_za_vizu, kandidat_broj_pasosa, kandidat_iskustvo_u_struci_trajanje, kandidat_zeljena_regija ,kandidat_zeljeni_grad, kandidat_atu_paket
									FROM idk_kandidati
									WHERE kandidat_id = :kandidat_id");

					$query->execute(array(
								':kandidat_id' => $kandidat_id));

					$row = $query->fetch();

						$kandidat_ime = trim($row['kandidat_ime']);
						$kandidat_prezime = trim($row['kandidat_prezime']);
						$kandidat_mobitel = $row['kandidat_mobitel'];
						$kandidat_spol = $row['kandidat_spol'];
						$kandidat_check = $row['kandidat_check'];
						$kandidat_djevojackoprezime = $row['kandidat_djevojackoprezime'];
						$kandidat_jmbg = $row['kandidat_jmbg'];
						$kandidat_mjestorodjenja = $row['kandidat_mjestorodjenja'];
						$kandidat_drzavarodjenja = $row['kandidat_drzavarodjenja'];
						$kandidat_drzavljanstvo = $row['kandidat_drzavljanstvo'];
						$kandidat_drzavljanstvo_vrsta_ispis = $row['kandidat_drzavljanstvo_vrsta'];
						$kandidat_bracnostanje = $row['kandidat_bracnostanje'];
						$kandidat_adresa = $row['kandidat_adresa'];
						$kandidat_prijava_na = $row['kandidat_prijava_na'];
						$kandidat_grad = $row['kandidat_grad'];
						$kandidat_pbroj = $row['kandidat_pbroj'];
						$kandidat_drzava = $row['kandidat_drzava'];
						$kandidat_email = $row['kandidat_email'];
						$kandidat_drzava = $row['kandidat_drzava'];
						$kandidat_visitedurl = $row['kandidat_visitedurl'];
						$kandidat_vozacka_kategorija = $row['kandidat_vozacka_kategorija'];
						$kandidat_datumrodjenja = date('d.m.Y.', strtotime($row['kandidat_datumrodjenja']));
						$kandidat_datetime = date('d.m.Y H:i', strtotime($row['kandidat_datetime']));
						$kandidat_datum_terminacheck = $row['datum_termina'];
						$kandidat_termin_za_vizu = $row['kandidat_termin_za_vizu'];
						$kandidat_viza = $row['kandidat_viza'];
						$kandidat_viza_vrijedi_do = $row['kandidat_viza_vrijedi_do'];
						$kandidat_datum_aplikacijecheck = $row['datum_aplikacije'];
						$kandidat_procjenatermina = $row['kandidat_procjenatermina'];
						$kandidat_datum_termina = date('d.m.Y', strtotime($row['datum_termina']));
						$kandidat_status = $row['kandidat_status'];
						$kandidat_zaposlen_kod = $row['kandidat_zaposlen_kod'];
						$kandidat_dipl_id = $row['kandidat_dipl_id'];
						$boravak_eu = $row['boravak_eu'];
						$kandidat_partnerid = $row['kandidat_partnerid'];
						$kandidat_partner_status = $row['kandidat_partner_status'];
						$kandidat_nostrifikacija = $row['kandidat_ima_nostrifikaciju'];
						$kandidat_iskustvo_u_struci = $row['kandidat_iskustvo_u_struci'];
						$kandidat_bracno_stanje = $row['kandidat_bracno_stanje'];
						$kandidat_broj_pasosa = $row['kandidat_broj_pasosa'];
						$kandidat_iskustvo_u_struci_trajanje = $row['kandidat_iskustvo_u_struci_trajanje'];
						$kandidat_zeljena_regija = $row['kandidat_zeljena_regija'];
						$kandidat_zeljeni_grad = $row['kandidat_zeljeni_grad'];
						$kandidat_atu_paket = $row['kandidat_atu_paket'];
						

						// Datum potpisa ugovora
						if($row['kandidat_datum_ugovora'] != NULL){
							$kandidat_datum_ugovora = date('d.m.Y', strtotime($row['kandidat_datum_ugovora']));						
						}else{
							$kandidat_datum_ugovora = NULL;						
						}
						
						// Datum pocetka rada
						if($row['kandidat_datum_pocetakrada'] != NULL){
							$kandidat_datum_pocetakrada = date('d.m.Y', strtotime($row['kandidat_datum_pocetakrada']));						
						}else{
							$kandidat_datum_pocetakrada = NULL;						
						}						
						
						
						if($kandidat_procjenatermina == 0){
							$kandidat_datum_termina = '<span class="label label-success material-label material-label_success main-container__column">'.$kandidat_datum_termina.'</span>';
						}else{
							$kandidat_datum_termina = '<span class="label label-danger material-label material-label_danger main-container__column">'.$kandidat_datum_termina.'</span>';
						}
				
						$kandidat_datum_aplikacije = date('d.m.Y', strtotime($row['datum_aplikacije']));
						$cv_de = $row['cv_de'];
						$cv_ba = $row['cv_ba'];
						$profil_de = $row['profile_de'];
						$profil_ba = $row['profile_ba'];
						
						// SKINI NJEMACKI CV
						if($cv_de == 0){
							$downloadCvButton = "";
						}else{
							$downloadCvButton = "";
							$cv_filepath_de = 'files/cv/de/'.$kandidat_id.'-'.$kandidat_ime.'_'.$kandidat_prezime.'.pdf';
						}
									
						// SKINI BOSANSKI CV
						if($cv_ba == 0){
							$downloadCvButtonBa = "";
						}else{
							$downloadCvButtonBa = "";
							$cv_filepath_ba = 'files/cv/ba/'.$kandidat_id.'-'.$kandidat_ime.'_'.$kandidat_prezime.'.pdf';
						}
						
						// SKINI BOSANSKI PROFIL
						if($profil_ba == 0){
							$downloadProfilButtonBa = "";
						}else{
							$downloadProfilButtonBa = "";
							$profil_filepath_ba = 'files/profile/ba/'.$kandidat_id.'-'.$kandidat_ime.'.pdf';
						}
						
						// SKINI NJEMACKI PROFIL
						if($profil_de == 0){
							$downloadProfilButtonDe = "";
						}else{
							$downloadProfilButtonDe = "";
							$profil_filepath_de = 'files/profile/de/'.$kandidat_id.'-'.$kandidat_ime.'.pdf';
						}

						if($row['kandidat_slika'] == "none"){
							$kandidat_slika = "nonekandidati.jpg";
						}else{
							$kandidat_slika = $row['kandidat_slika'];
						}
						/*
						if($row['kandidat_status'] == 0){
							$kandidat_status = "Deaktiviran";
						}elseif($row['kandidat_status'] == 1){
							$kandidat_status = "Administrator";
						}elseif($row['kandidat_status'] == 2){
							$kandidat_status = "Super korisnik";
						}elseif($row['kandidat_status'] == 3){
							$kandidat_status = "Korisnik";
						}
						*/
						if($kandidat_vozacka_kategorija == null)
							$kandidat_vozacka_kategorija_f = "B";
						else
							$kandidat_vozacka_kategorija_f = "".$kandidat_vozacka_kategorija."";
						if($row['kandidat_vozacka_dozvola'] == "Da"){
							$kandidat_vozacka_dozvola = '<span class="label label-success material-label material-label_success main-container__column">'.$kandidat_vozacka_kategorija_f.'</span>';
						}elseif($row['kandidat_vozacka_dozvola'] == "Ne"){
							$kandidat_vozacka_dozvola = '<span class="label label-danger material-label material-label_danger main-container__column">Nema</span>';
						}else{
							$kandidat_vozacka_dozvola = '<span class="label label-danger material-label material-label_danger main-container__column">Nepoznato</span>';
						}
						
						if($row['kandidat_viza'] == "1"){
							$kandidat_viza = '<span class="label label-success material-label material-label_success main-container__column">DA</span>';
							if($kandidat_viza_vrijedi_do !== null){
								$kandidat_viza_vrijedi_do_f = date('d.m.Y', strtotime($kandidat_viza_vrijedi_do));
								$kandidat_viza = '<span class="label label-success material-label material-label_success main-container__column">DA</span> - <span class="label label-success material-label material-label_success main-container__column">'.$kandidat_viza_vrijedi_do_f.'</span>';
							}else{}
							$unesena_viza = 1;
							$unesen_termin = 0;
							$unesen_apl = 0;
						}else{
							$kandidat_viza = '<span class="label label-danger material-label material-label_danger main-container__column">NE</span>';
							$unesena_viza = 0;
							if($row['datum_termina'] !== null && $kandidat_procjenatermina != 1){
								$unesen_termin = 1;
								$unesen_apl = 0;
							}else{
								$unesen_termin = 0;
								if($row['datum_aplikacije'] !== null)
									$unesen_apl = 1;
								else
									$unesen_apl = 0;
							}
						}
						
						$query_bot_username = $db->prepare("
									SELECT email
									FROM users
									WHERE kandidat_id = :kandidat_id");

						$query_bot_username->execute(array(
									':kandidat_id' => $kandidat_id));

						$row_bot_username = $query_bot_username->fetch();

						$kandidat_bot_username = $row_bot_username['email'];
						
						//GET VALIDACIJE
						$val_viza = getValidacijaViza($kandidat_id, "blok_viza");
						$val_termin = getValidacijaViza($kandidat_id, "blok_termin");
						$val_apl = getValidacijaViza($kandidat_id, "blok_apl");
						$val_ime = getValidacijaViza($kandidat_id, "blok_ime");
						$val_pre = getValidacijaViza($kandidat_id, "blok_pre");
						$val_dtR = getValidacijaViza($kandidat_id, "blok_dtR");
						$val_mjR = getValidacijaViza($kandidat_id, "blok_mjR");
						$val_drR = getValidacijaViza($kandidat_id, "blok_drR");
						$val_adr = getValidacijaViza($kandidat_id, "blok_adr");
						$val_gra = getValidacijaViza($kandidat_id, "blok_gra");
						$val_pbr = getValidacijaViza($kandidat_id, "blok_pbr");
						$val_drz = getValidacijaViza($kandidat_id, "blok_drz");
						$val_osnovne = getValidacijaOsnovneInformacije($kandidat_id);
						
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
					var queryString = window.location.search;
					const urlParams = new URLSearchParams(queryString);
					const product = urlParams.get('infoNost');
					if (product == 28 ){
						$("#nostrifikovana_dipl").modal('show');
						$("#modal_title").text("Provjera DIPL statusa");
						$("#info_dipl_arhiva").css("display", "block");
					}
					//console.log(product);
				});
			</script>
			<div class="row">
				<div class="col-xs-8">
					<h1>
						<a class="fancybox" rel="group" href="<?php getSiteURL(); ?>files/kandidati/<?php echo $kandidat_slika; ?>">
							<img class="idk_profile_img" src="<?php getSiteURL(); ?>files/kandidati/<?php echo $kandidat_slika; ?>?v=<?php echo $_COOKIE['image_ctr'] ?? ''; ?>">
						</a>
						<?php 
						echo $kandidat_ime; 
						if(
							(in_array("1" , $employee_status)) OR
							(in_array("2" , $employee_status)) OR
							(in_array("3" , $employee_status)) OR
							(in_array("4" , $employee_status)) OR 
							(in_array("18", $employee_status)) OR
							(in_array("20", $employee_status)) 
						){
							echo " ".$kandidat_prezime;
						} 
						if(isCandidateDVAG($kandidat_id)){
							echo ' (DVAG kandidat)';
						}

						if(hasCandidateWrongNumber($kandidat_id)){
							echo ' (Pogrešan broj)  <button id="correct_number_confirmation" class="btn material-btn material-btn_success main-container__column" aria-hidden="true"><i class="fa fa-check" aria-hidden="true"></i> Potvrdi da je broj tačan</button>';
						}

						if(checkCandidateCooling($kandidat_id)){
							echo ' (Kandidat na hlađenju)';
						}

						if(checkCandidateFreezed($kandidat_id)){
							echo ' (Zaleđen) <button id="unfreeze" class="btn material-btn material-btn_warning main-container__column" aria-hidden="true"><i class="fa fa-sun-o" aria-hidden="true"></i> Odledi</button>';
						}
						?>
						<script>
							$('#unfreeze').on('click', () => {
								$.ajax({
									url: 'ajax_data.php?page=unfreeze_candidate',
									type: 'POST',
									data: {
										"kandidat_id" : kandidat_id
									},
									dataType: 'html',
									success: function(data) {
										window.location.reload();
									},
									error: function (xhr, ajaxOptions, thrownError) {
										alert(xhr.status);
										alert(thrownError);
									}
								});
							});
							
							$('#correct_number_confirmation').on('click', () => {
								$.ajax({
									url: 'ajax_data.php?page=confirm_candidate_number',
									type: 'POST',
									data: {
										"kandidat_id" : kandidat_id
									},
									dataType: 'html',
									success: function(data) {
										window.location.reload();
									},
									error: function (xhr, ajaxOptions, thrownError) {
										alert(xhr.status);
										alert(thrownError);
									}
								});
							});
						</script>
						<a href="#" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" id="otherProfilesButton" data-toggle="modal" data-target="#otherProfilesModal" style="display: none;"><i class="fa fa-user-circle-o" aria-hidden="true"></i> <span>Duplikati profila kandidata</span></a>
					</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">	
					<a href="<?php getSiteURL(); ?>kandidati?page=list_ajax" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
				</div>
				<div class="col-xs-12 idk_margin_top10">
					<?php 
					if (getEmployeeStatus() != 20){
						?>
						<a href="<?php getSiteUrl(); ?>do.php?form=rotate_profile_image&kand_id=<?php echo $kandidat_id; ?>&image=<?php echo $kandidat_slika; ?>&rotate=1" class="btn material-btn material-btn_success main-container__column"><i class="fa fa-undo" aria-hidden="true"></i></a>
						<a href="<?php getSiteUrl(); ?>do.php?form=rotate_profile_image&kand_id=<?php echo $kandidat_id; ?>&image=<?php echo $kandidat_slika; ?>&rotate=2" class="btn material-btn material-btn_success main-container__column"><i class="fa fa-repeat" aria-hidden="true"></i></a>
						<?php 
					} ?>
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
									}elseif($mess == 12){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste uredili opis dokumentu.</div>
										<script>$(function() { $('[href="#documents"]').tab('show'); });</script>
									<?php
									}elseif($mess == 13){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste rotirali fotografiju.</div>
										<script>$(function() { $('[href="#documents"]').tab('show'); });</script>
									<?php
									}elseif($mess == 14){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste izbacili kandidata iz projekta.</div>
										<script>$(function() { $('[href="#projekti"]').tab('show'); });</script>
									<?php
									}elseif($mess == 15){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste dodali kandidata u projekt.</div>
										<script>$(function() { $('[href="#projekti"]').tab('show'); });</script>
									<?php
									}elseif($mess == 16){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste rotirali sliku kandidata.</div>
									<?php
									}elseif($mess == 17){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste generirali CV na bosanskom jeziku.</div>
									<?php
									}elseif($mess == 18){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste ažurirali CV.</div>
									<?php
									}elseif($mess == 19){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste generirali CV na njemačkom jeziku.</div>
									<?php
									}elseif($mess == 20){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste generirali profil na bosanskom jeziku.</div>
									<?php
									}elseif($mess == 21){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste ažurirali profil.</div>
									<?php
									}elseif($mess == 22){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste generirali profil na njemačkom jeziku.</div>
									<?php
									}elseif($mess == 23){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste ažurirali profil.</div>
									<?php
									}elseif($mess == 24){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste generirali CV i profil na njemačkom jeziku.</div>
									<?php
									}elseif($mess == 25){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste generirali CV i ažurirali profil na njemačkom jeziku.</div>
									<?php
									}elseif($mess == 26){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste generirali CV i profil.</div>
									<?php
									}elseif($mess == 27){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste generirali CV i ažurirali profil.</div>
									<?php
									}
									
								?>
							</div>
						</div>
						<?php 
						if(getEmployeeStatus() != 20){
							?>
							<ul class="list-inline text-right">
								<!-- Modal CV -->
								<div class="modal material-modal material-modal_success fade" id="cvModal">
									<div class="modal-dialog">
										<div class="modal-content material-modal__content">
											<div class="modal-header material-modal__header">
												<button class="close material-modal__close" data-dismiss="modal">&times;</button>
											</div>
											<div class="modal-body material-modal__body text-center">
												<a href="<?php getSiteURL(); ?>cv_ba?page=open&id=<?php echo $kandidat_id; ?>" target="_BLANK"><button class="btn btn-primary material-btn material-btn_success"><img src="<?php getSiteUrl(); ?>images/BosniaHerzegowina.png" width="50">&nbsp &nbsp BOSANSKI</button></a>
												<a href="<?php getSiteURL(); ?>cv_de?page=open&id=<?php echo $kandidat_id; ?>" target="_BLANK"><button class="btn btn-primary material-btn material-btn_success"><img src="<?php getSiteUrl(); ?>images/Germany.png" width="50">&nbsp &nbsp NJEMAČKI</button></a>
											</div>
											<div class="modal-footer material-modal__footer">
												<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
											</div>
										</div>
									</div>
								</div>
								<?php 
								if($cv_ba == 1){?>
								<li><a class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column downloadDeCv" href="<?php getSiteUrl(); ?><?php echo $cv_filepath_ba; ?>" download="<?php echo $cv_filepath_ba; ?>"><i class="fa fa-download" aria-hidden="true"></i> <span>Skini Bosanski CV</span></a></li>
								<?php } ?>
								
								<?php
								if($cv_de == 1){?>
								<li><a class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column" href="<?php getSiteUrl(); ?><?php echo $cv_filepath_de; ?>" download="<?php echo $cv_filepath_de; ?>"><i class="fa fa-download" aria-hidden="true"></i> <span>Skini Njemački CV</span></a></li>
								<script>
									//$('.downloadDeCv').click(function(e) {
									//	var cvUrl = $(this).data('urlcva');
									//
									//	e.preventDefault();  //stop the browser from following
									//	window.location.href = cvUrl;
									//});							
								</script>
								<li><a href="<?php getSiteURL(); ?>cv_de?page=edit&id=<?php echo $kandidat_id; ?>" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-edit" aria-hidden="true"></i> <span>Uredi CV</span></a></li>
								<?php }
								if($profil_ba == 1){?>
								<li><a class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column" href="<?php getSiteUrl(); ?><?php echo $profil_filepath_ba; ?>" download="<?php echo $profil_filepath_ba; ?>"><i class="fa fa-download" aria-hidden="true"></i> <span>Skini Bosanski profil</span></a></li>
								<li><a href="<?php getSiteURL(); ?>profil_ba?page=edit&id=<?php echo $kandidat_id; ?>" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-edit" aria-hidden="true"></i> <span>Uredi profil</span></a></li>
								<?php }
								if($profil_de == 1){?>
								<li><a class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column" href="<?php getSiteUrl(); ?><?php echo $profil_filepath_de; ?>" download="<?php echo $profil_filepath_de; ?>"><i class="fa fa-download" aria-hidden="true"></i> <span>Skini Njemački profil</span></a></li>
								<li><a href="<?php getSiteURL(); ?>profil_de?page=edit&id=<?php echo $kandidat_id; ?>" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-edit" aria-hidden="true"></i> <span>Uredi profil</span></a></li>
								<?php } ?>
								<li><a href="#" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" data-toggle="modal" data-target="#cvModal"><i class="fa fa-sticky-note-o" aria-hidden="true"></i> <span>Generiši CV</span></a></li>
							
								
								<li><a href="#" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" data-toggle="modal" data-target="#noteModal"><i class="fa fa-sticky-note-o" aria-hidden="true"></i> <span>Dodaj bilješku</span></a></li>

								<!-- Modal add note -->
								<div class="modal material-modal material-modal_primary fade text-left" id="noteModal">
									<div class="modal-dialog modal-lg">
										<div class="modal-content material-modal__content">
											<div class="modal-header material-modal__header">
												<button class="close material-modal__close" data-dismiss="modal">&times;</button>
												<h4 class="modal-title material-modal__title">Dodaj bilješku</h4>
											</div>
											<div class="modal-body material-modal__body">
												<form action="<?php getSiteURL(); ?>do.php?form=add_candidate_note" enctype="multipart/form-data" method="post" role="form" class="form-horizontal">
													<input type="hidden" name="note_dataid" value="<?php echo $kandidat_id; ?>" />
													<div class="form-group">
														<div class="col-md-offset-2 col-sm-8">
															<div class="">
																<select class="selectpicker" id="note_attachment" name="note_attachment" title = "Da li bilješka ima prilog?" required>
																	<option value="1">Da</option>
																	<option value="0">Ne</option>
																</select>
															</div>
														</div>
													</div>
													
													<script>
														$(document).ready(function() {
															$(".note_attachment_file_field").hide();
															$('#note_attachment_file').val(null);
															$('#note_attachment_file').removeAttr('required');

															$('#note_attachment').change(function() {

																var note_attachment_value = parseInt($("#note_attachment").val());

																$('#note_attachment_file').val(null);
																$('#files-name').text("");
																$('#files-selected').text("");
																$(".fileinput").fileinput("clear");

																if ( note_attachment_value === 1 ) {

																	$(".note_attachment_file_field").show();
																	$('#note_attachment_file').attr('required', 'true');

																} else {
																	
																	$(".note_attachment_file_field").hide();
																	$('#note_attachment_file').removeAttr('required');

																}
															});
														});
													</script>
													<div id="file_alert_size" class="form-group hidden">
														<div class="col-md-offset-2 col-sm-8">
															<div class="alert material-alert material-alert_danger">Greška: Dokument koji pokuštavate dodati je veći od dozvoljene veličine.</div>
														</div>
													</div>
													<div id="file_alert_ext" class="form-group hidden">
														<div class="col-md-offset-2 col-sm-8">
															<div class="alert material-alert material-alert_danger">Greška: Format dokumenta kojeg pokušavate dodati nije dozvoljen.</div>
														</div>
													</div>
													<div id="file_alert_len" class="form-group hidden">
														<div class="col-md-offset-2 col-sm-8">
															<div class="alert material-alert material-alert_danger">Greška: Dozvoljeno je dodati maximalno 10 dokumenata.</div>
														</div>
													</div>
													<div class="form-group note_attachment_file_field">
														<div class="col-md-offset-2 col-sm-8 text-center">
															<div class="fileinput fileinput-new" data-provides="fileinput">
																<span class="btn btn-default btn-file">
																	<span class="fileinput-new"> 
																		Izaberi dokument
																	</span>
																	<span class="fileinput-exists">
																		Promijeni
																	</span>
																	<input type="file" name="note_attachment_file[]" id="note_attachment_file" multiple="">
																</span>
																<i 
																	style="padding-top: 5px; padding-left: 10px; padding-right: 10px;" 
																	class="fa fa-question-circle fa-lg" 
																	data-toggle="tooltip" 
																	data-placement="right" 
																	title="Napomena: Moguće dodati najviše 10 dokumenata! Dokument ne smije biti veći od 5MB! - Dozvoljeni formati: jpg, jpeg, pdf, doc, docx, xls, xlsx, txt, ppt, pptx, png, csv" 
																	aria-hidden="true"
																>
																</i>
																<br>
																<span style="padding-top: 5px; padding-left: 10px; padding-right: 10px; word-break: break-all;" id = "files-selected">
																</span>
																<br>
																<span style="padding-top: 5px; padding-left: 10px; padding-right: 10px; word-break: break-all;" id = "files-name">
																</span>
																
																<script>
																	$(function (){
																		$('#note_attachment_file').change(function (){
																			//Script za provjeru više dokumenata
																			if($('#note_attachment_file').val() !== ""){
																				var files = [];
																				var filesName = [];
																				files.push(this.files);
																				if(parseInt(this.files.length) < 11){
																					$('#files-selected').text(this.files.length + " file selected.");
																					iterateFiles(files);
																					function iterateFiles(filesArray)
																					{
																						var zastavicaPrilog = 0;
																						for(var i=0; i<filesArray.length; i++){
																							//console.log("I"+ i);
																							for(var j=0; j<filesArray[i].length; j++){
																								//console.log("J"+ j);
																								console.log(filesArray[i][j].name + " " + filesArray[i][j].size);
																								filesName.push(filesArray[i][j].name);
																								// alternatively: console.log(filesArray[i].item(j).name);
																								var ext = filesArray[i][j].name.split('.').pop().toLowerCase();
																								if($.inArray(ext, ['jpg', 'jpeg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png']) == -1) {
																									$('#file_alert_ext').removeClass('hidden');
																									setTimeout(function(){
																										$('#file_alert_ext').addClass('hidden');
																									}, 5000);
																									$('#note_attachment_file').val(null);
																									zastavicaPrilog = 1;
																									$('#files-selected').text("");
																									break;
																								}else{
																									$('#file_alert_ext').addClass('hidden');
																								}
																								
																								var f = filesArray[i][j];
																								if (f.size > 5242880){
																									$('#file_alert_size').removeClass('hidden');
																									setTimeout(function(){
																										$('#file_alert_size').addClass('hidden');
																									}, 5000);
																									$('#note_attachment_file').val(null);
																									zastavicaPrilog = 1;
																									$('#files-selected').text("");
																									break;
																								}else{
																									$('#file_alert_size').addClass('hidden');
																								}
																							}
																							if(zastavicaPrilog === 1){
																								break;
																							}
																						}
																						if(zastavicaPrilog === 0){
																							$('#files-name').text("Files: "+ filesName.join(" , ") + "");
																						}else{
																							$('#files-name').text("");
																						}
																					}
																				}else{
																					$('#note_attachment_file').val(null);
																					$('#files-name').text("");
																					$('#files-selected').text("");
																					$('#file_alert_len').removeClass('hidden');
																					setTimeout(function(){
																						$('#file_alert_len').addClass('hidden');
																					}, 5000);
																				}
																			}
																		})
																	});
																</script>
															</div>
														</div>
													</div>
													<style>
														.note_txt_style {
															border: 1px solid #e3e3e3;
															padding: 15px;
														}
													</style>
													<div class="form-group">
														<div class="col-md-offset-2 col-sm-8">
															<div class="materail-input-block materail-input-block_success">
																<textarea class="form-control materail-input material-textarea note_txt_style" name="note_txt" placeholder="Bilješka" rows="6" required></textarea>
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
								<!-- Modal other profiles start -->
									<div class="modal material-modal material-modal_primary fade text-left" id="otherProfilesModal">
										<div class="modal-dialog ">
											<div class="modal-content material-modal__content">
												<div class="modal-header material-modal__header">
													<button class="close material-modal__close" data-dismiss="modal">&times;</button>
													<h4 class="modal-title material-modal__title">Ovdje su prikazani svi duplikati profila ako postoje</h4>
												</div>
												<div class="modal-body material-modal__body">
													<table id="otherProfilesList">
														<thead>
															<td>Id</td>	
															<td>Ime i prezime</td>	
															<td>Broj mobitela</td>	
															<td></td>
														</thead>
														<tbody>
															<?php
															
																$datumrodjena_kandidat = date('Y-m-d', strtotime($kandidat_datumrodjenja));
																$duplikatiProfilaKandidata = $db->prepare("
																	SELECT kandidat_id,kandidat_ime,kandidat_prezime,kandidat_mobitel,kandidat_status, kandidat_datumrodjenja 
																	FROM idk_kandidati 

																	WHERE (kandidat_ime LIKE '%$kandidat_ime%' AND kandidat_prezime LIKE '%$kandidat_prezime%' 
																		OR kandidat_prezime LIKE '%$kandidat_ime%' AND kandidat_ime LIKE '%$kandidat_prezime%') 
																	AND kandidat_id!=$kandidat_id;
																");

																$duplikatiProfilaKandidata->execute();
																$listaProfila = $duplikatiProfilaKandidata->fetchAll();

																foreach($listaProfila as $profil){
																	// == ne gleda + i 0 ispred brojeva (+3876213 je isto kao i 003876213)
																	if($profil["kandidat_mobitel"] == $kandidat_mobitel OR $profil["kandidat_datumrodjenja"] == $datumrodjena_kandidat){
																		?>
																		<script>
																			$("#otherProfilesButton").css("display", "inline-block");
																		</script>
																		<tr>
																			<td>
																				<?php echo $profil["kandidat_id"]; ?>
																			</td>

																			<td>
																				<a href="/kandidati?page=open&id=<?php echo $profil['kandidat_id']; ?>" target="_balnk">
																					<?php echo $profil["kandidat_ime"]; ?> 
																					<?php echo $profil["kandidat_prezime"]; ?>
																				</a>
																			</td>
																			<td><?php echo $profil["kandidat_mobitel"]; ?></td>
																			<td><?php if($profil["kandidat_status"]==3){ ?> <span style="background-color: red; color: white; padding: 5px 10px;">Arhiviran</span> <?php } ?></td>
																		</tr>
																		<?php
																	}
																}

															?>
														</tbody>
													</table>
												</div>
												<div class="modal-footer material-modal__footer">
												
												</div>
											</div>
										</div>
									</div>
									<script>
										$(document).ready(function(){
											$('#otherProfilesList').DataTable({
												responsive: true,
												searching: false,
												paging: false,
												"bAutoWidth": false,
											});
										});
									</script>
								<!-- Modal other profiles end -->
								<?php if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus)) OR (in_array( "18" , $getEmployeeStatus )) ){	?>
								<li><a href="#" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" data-toggle="modal" data-target="#docModal"><i class="fa fa-file-text-o" aria-hidden="true"></i> <span>Dodaj dokument</span></a></li>
								<?php
									getNostrifikacijaBtn($kandidat_id); 
									}
									//COPY CRM IN DIPL START
									//URADIO ADIS TOROMANOVIC
									if(getZaposlenikDiplR($logged_employee_id) == 1 OR getZaposlenikDiplR($logged_employee_id) == 0 OR $logged_employee_id == 67){
									//Samo zaposlenici kojima je omogucena nostrifikacija diplome, mogu da prebace kandidata iz Kan u Dipl
											echo '<li><a href="#" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column" data-toggle="modal" data-target="#searchDIPL"><i class="fa fa-search" aria-hidden="true"></i> <span>DIPL</span></a></li>';
									}
									//COPY CRM IN DIPL END
								?>
								<!-- MODAL TRAZENJE U DIPL -->
								<div class="modal material-modal material-modal_primary fade text-left" id="searchDIPL">
									<div class="modal-dialog ">
										<div class="modal-content material-modal__content">
											<div class="modal-header material-modal__header">
												<button class="close material-modal__close" data-dismiss="modal">&times;</button>
												<h4 class="modal-title material-modal__title">DIPL</h4>
											</div>
											<div class="modal-body material-modal__body">
												<div class="form-group">
												</div>
												<div class="form-group">
													<div class="col-lg-offset-1 col-lg-10" id="list_wrapper">
														<script>
															$(document).ready(function(){
																let kandidat_ime = "<?php echo $kandidat_ime; ?>";
																let kandidat_prezime = "<?php echo $kandidat_prezime; ?>";
																let kandidat_mobitel = "<?php echo $kandidat_mobitel; ?>";
																let kandidat_id = "<?php echo $kandidat_id; ?>";
																$.ajax({
																	url: 'ajax_data.php?page=list_dipl_candidates',
																	type: 'POST',
																	data: {
																		"kandidat_ime" : kandidat_ime,
																		"kandidat_prezime" : kandidat_prezime,
																		"kandidat_mobitel" : kandidat_mobitel,
																		"kandidat_id" : kandidat_id
																	},
																	dataType: 'html',
																	success: function(data) {
																		$("#list_wrapper").html(data);
																		$('#dipl_table').DataTable({
																			responsive: true,
																			searching: false,
																			paging: false,
																			"order": [[ 0, "desc" ]],
																			"bAutoWidth": false,
																		});
																		$(".povezi_btn").click(function(){
																			let dipl_id 	= $(this).data("dipl_id");
																			let povezivanje = $(this).data("povezivanje");
																			$("#dipl_id").val(dipl_id);
																			$("#povezivanje").val(povezivanje);
																			$("#searchDIPL").modal("toggle");
																		});
																		$("#copy_dipl").click(function(){
																			$("#searchDIPL").modal("toggle");
																		});
																		$(".povezi_btn").click(function(){
																			let povezivanje = $("#povezivanje").val();
																			if( povezivanje == 1){
																				$("#modal_title").text("Povezivanje kandidata sa DIPL-om");
																			} else {
																				$("#modal_title").text("Označavanje da kandidat ima nostrifikovanu diplomu");
																			}
																		});
																	},
																	error: function (xhr, ajaxOptions, thrownError) {
																		alert(xhr.status);
																		alert(thrownError);
																	}
																});
															});
														</script>
													</div>
												</div>
												<div class="modal-footer material-modal__footer">
													<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
												</div>
											</div>
										</div>
									</div>
								</div>
								<!-- Modal oznacavanje nostrifikovane diplome -->
								<div class="modal material-modal material-modal_primary fade text-left" id="nostrifikovana_dipl">
									<div class="modal-dialog ">
										<div class="modal-content material-modal__content">
											<div class="modal-header material-modal__header">
												<button class="close material-modal__close" data-dismiss="modal">&times;</button>
												<h4 class="modal-title material-modal__title" id="modal_title"></h4>
											</div>
											<div class="modal-body material-modal__body">
												<form action="/do.php?form=add_nostrifikacija" method="POST">
													<input type="hidden" id="kandidat_id" name="kandidat_id" value="<?php echo $kandidat_id; ?>">
													<input type="hidden" id="dipl_id" name="dipl_id" value="<?php echo $kandidat_dipl_id; ?>">
													<input type="hidden" id="povezivanje" name="povezivanje">
													<div class="form-group" id="info_dipl_arhiva" style="display: none;">
														<div class="col-md-offset-2 col-sm-8">
															<div class="alert alert-warning text-center" role="alert">
																<i class="fa fa-exclamation-triangle fa-3x" aria-hidden="true"></i>
																<br>
																<strong>
																	Profil ovog kandidata na DIPL-u je u arhivi. Ako kandidat već ima nostrifikovanu diplomu onda to označite ispod!
																</strong>
															</div>
														</div>
													</div>
													<div class="form-group">
														<div class="col-sm-7" style="padding-top:5px; text-align:right;">
															<span class="text-danger">*</span>Da li kandidat ima nostrifikovanu diplomu:
														</div>
														<div class="col-sm-5" style="margin-bottom: 20px;">
															<div class="materail-input-block materail-input-block_success idk_radio_buttons">
																<label class="main-container__column material-radio-group material-radio-group_success" for="ima_nostrifikaciju" style="padding-right: 5px;">
																	<input type="radio" name="nostrifikacija" id="ima_nostrifikaciju" class="material-radiobox" value="1" />
																	<span class="material-radio-group__element material-radio-group__check-radio"></span>
																	<span class="material-radio-group__element material-radio-group__caption">Da </span>
																</label>													
																
																<label class="main-container__column material-radio-group material-radio-group_danger" for="nema_nostrifikaciju">
																	<input type="radio" name="nostrifikacija" id="nema_nostrifikaciju" class="material-radiobox" value="0" />
																	<span class="material-radio-group__element material-radio-group__check-radio"></span>
																	<span class="material-radio-group__element material-radio-group__caption">Ne </span>
																</label>
															</div>
														</div>
													</div>
													<div class="form-group" id="warning" style="display: none;">
														<div class="col-md-offset-2 col-sm-8">
															<div class="alert alert-danger text-center" role="alert">
																<i class="fa fa-exclamation-triangle fa-3x" aria-hidden="true"></i>
																<br>
																<strong>
																	Ovom akcijom prebacujete kandidata u DIPL-u na arhivu pod razlogom: "Ima nostrifikovanu diplomu".
																</strong>
															</div>
														</div>
													</div>
													<script>
														$("#ima_nostrifikaciju").click(function(){
															$("#warning").css("display", "block");
														});
														$("#nema_nostrifikaciju").click(function(){
															$("#warning").css("display", "none");
														});
														$("#nostrifikacija_btn").click(function(){
															$("#modal_title").text("Označavanje da kandidat ima nostrifikovanu diplomu");
														});
													</script>
													<div class="modal-footer material-modal__footer" >
														<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
														<button type="submit" class="btn btn-primary material-btn material-btn_success"><i class="fa fa-check-square-o" aria-hidden="true"></i> Zavrsi</button>
													</div>
												</form>
											</div>
										</div>
									</div>
								</div>
								<!-- Modal add DIPL -->
								<div class="modal material-modal material-modal_primary fade text-left" id="diplModal">
									<div class="modal-dialog ">
										<div class="modal-content material-modal__content">
											<div class="modal-header material-modal__header">
												<button class="close material-modal__close" data-dismiss="modal">&times;</button>
												<h4 class="modal-title material-modal__title">Kopiraj u DIPL</h4>
											</div>
											<div class="modal-body material-modal__body">
												<form action="<?php getSiteURL(); ?>nostrifikacija_diploma.php?page=copyCrmKaninDipl&id=<?php echo $kandidat_id; ?>" enctype="multipart/form-data" method="post" role="form" class="form-horizontal" id = "form_copyCrmKaninDipl">
													<div class="form-group">
														<div class="col-md-offset-2 col-sm-8">
															<div class="col-sm-12">
																<div class="alert alert-warning" role="alert">Nastavkom prebacujete kandidata u Dipl modul!</div>
															</div>
														</div>
													</div>
													<div class="form-group">
														<div class="col-sm-7" style="padding-top:5px; text-align:right;">
															<span class="text-danger">*</span>Da li kandidat ima nostrifikovanu diplomu:
														</div>
														<div class="col-sm-5" >
															<div class="materail-input-block materail-input-block_success idk_radio_buttons">
																<label class="main-container__column material-radio-group material-radio-group_success" for="ima_nostrifikaciju_cpy" style="padding-right: 5px;">
																	<input type="radio" name="nostrifikacija_cpy" id="ima_nostrifikaciju_cpy" class="material-radiobox" value="1" />
																	<span class="material-radio-group__element material-radio-group__check-radio"></span>
																	<span class="material-radio-group__element material-radio-group__caption">Da </span>
																</label>													
																
																<label class="main-container__column material-radio-group material-radio-group_danger" for="nema_nostrifikaciju_cpy">
																	<input type="radio" name="nostrifikacija_cpy" id="nema_nostrifikaciju_cpy" class="material-radiobox" value="0" />
																	<span class="material-radio-group__element material-radio-group__check-radio"></span>
																	<span class="material-radio-group__element material-radio-group__caption">Ne </span>
																</label>
															</div>
														</div>
													</div>
													<div class="modal-footer material-modal__footer">
														<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
														<button type="submit" class="btn btn-primary material-btn material-btn_success" form="form_copyCrmKaninDipl"><i class="fa fa-check-square-o" aria-hidden="true"></i> Kopiraj</button>
													</div>
												</form>
											</div>
										</div>
									</div>
								</div>
								<!-- Modal add DIPL end -->
								<!-- Modal add document -->
								<div class="modal material-modal material-modal_primary fade text-left" id="docModal">
									<div class="modal-dialog ">
										<div class="modal-content material-modal__content">
											<div class="modal-header material-modal__header">
												<button class="close material-modal__close" data-dismiss="modal">&times;</button>
												<h4 class="modal-title material-modal__title">Dodaj dokument</h4>
											</div>
											<div class="modal-body material-modal__body">
												<form action="<?php getSiteURL(); ?>do.php?form=add_kandidat_doc" enctype="multipart/form-data" method="post" role="form" class="form-horizontal" id="form_doc">
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
																<option value="Dokaz o završenoj srednjoj školi">Dokaz o završenoj srednjoj školi</option>
																<option value="Dokazivo radno iskustvo">Dokazivo radno iskustvo</option>
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
																<span class="btn btn-default btn-file"><span class="fileinput-new">Izaberi dokument</span><span class="fileinput-exists">Promijeni</span>
																<input type="file" name="document_file" id="document_file" required></span> <i class="fa fa-info-circle fa-lg idk_margin_left10" data-toggle="tooltip" data-placement="right" title="Napomena: Dokument ne smije biti veći od 20MB! - Dozvoljeni formati: jpg, jpeg, pdf, doc, docx, xls, xlsx, txt, ppt, pptx, png" aria-hidden="true"></i>
																<span class="fileinput-filename"></span>
																<a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none">×</a>
																<script>
																	$(function (){
																		$('#document_file').change(function (){

																			var f = this.files[0];

																			if (f.size > 25388608 || f.fileSize > 25388608){
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
												</form>
											</div>
											<div class="modal-footer material-modal__footer">
													<button type="submit" class="btn btn-primary material-btn material-btn_primary" form="form_doc">Dodaj</button>
												
											</div>
										</div>
									</div>
								</div>
								<!-- Modal add document end -->

								<!--
									Odgovarajući poslovi za kandidata START
								 	-->
										<!-- JFC - Jobs for Candidate -->
										<!-- CTJFC - Complete Transfer Jobs for Candidate -->
										<style>
											#jobsForCandidate .loaderJFC {
												border: 8px solid #f3f3f3; 
												border-top: 8px solid #f2a12e;
												border-radius: 50%;
												width: 50px;
												height: 50px;
												animation: spinJFC 1s linear infinite;
												margin: auto;
												margin-bottom: 100px;
												margin-top: 100px;
											}
											@keyframes spinJFC {
												0% { transform: rotate(0deg); }
												100% { transform: rotate(360deg); }
											}
											#jobsForCandidate .contentJFC {
												width:100%;
											}
											#jobsForCandidate .contentJFC #tableJFC .list-group {
												margin-bottom: 0;
											}
											#jobsForCandidate .contentJFC #tableJFC .list-group .list-group-item {
												padding: 6px 6px;
											}
											#jobsForCandidate .messageJFC {
												width:100%;
											}
										</style>
										<li>
											<button 
												class="btn material-btn material-btn-icon-warning material-btn_warning main-container__column" 
												data-toggle="modal" 
												data-target="#jobsForCandidate"
											>
												<i class="fa fa-info-circle" aria-hidden="true"></i> 
												<span>Odgovarajući poslovi</span>
											</button>
										</li>
										<div class="modal material-modal material-modal_warning fade text-left" id="jobsForCandidate">
											<div class="modal-dialog modal-lg">
												<div class="modal-content material-modal__content">
													<div class="modal-header material-modal__header">
														<button class="close material-modal__close" data-dismiss="modal">&times;</button>
														<h4 class="modal-title material-modal__title">
															<i style = "margin-right: 10px;" class="fa fa-info-circle" aria-hidden="true"></i>
															<strong>
																Odgovarajući poslovi za kandidata
															</strong>
														</h4>
													</div> 
													<div class="modal-body material-modal__body">
														<div class="messageJFC hidden"></div>
														<div class="loaderJFC hidden"></div>
														<div class="contentJFC hidden"></div>
													</div>
													<div class="modal-footer material-modal__footer">
														<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
													</div>
												</div>
											</div>
										</div>
										<div class="modal material-modal material-modal_success fade text-left" id="completeTransferJFC">
											<div class="modal-dialog modal-lg">
												<div class="modal-content material-modal__content">
													<div class="modal-header material-modal__header">
														<button class="close material-modal__close" data-dismiss="modal">&times;</button>
														<h4 class="modal-title material-modal__title">
															<i style = "margin-right: 10px;" class="fa fa-share-square-o" aria-hidden="true"></i>
															<strong>
																Dovrši prebacivanje kandidata
															</strong>
														</h4>
													</div> 
													<div class="modal-body material-modal__body">
														<div class="messageCTJFC hidden"></div>
														<div class="contentCTJFC">
															<form action="<?php getSiteURL(); ?>do.php?form=transfer_to_new_nalog" enctype="multipart/form-data" method="post" role="form" class="form-horizontal" id="completeTransferJFCForm">
																<input type="hidden" id="nalogCRJFC" name="nalogCRJFC"/>
																<input type="hidden" id="candidateCRJFC" name="candidateCRJFC"/>
																<div class="row">
																	<div class="col-md-offset-1 col-sm-10">

																		<div class="form-group">
																			<div class="col-sm-12 text-center">
																				<small>
																					<span class="text-danger">
																						Zatvaranjem modala - gube se informacije!
																					</span>  
																				</small>
																			</div>
																		</div>
																		
																		<div class="form-group">    
																			<div class="col-sm-12">
																				<label for="statusCRJFC" class="col-sm-4 control-label">
																					<span class="text-danger">
																						*
																					</span>
																					Status:
																				</label>
																				<div class="col-sm-8">
																					<div class="">
																						<select class="selectpicker" id="statusCRJFC" name="statusCRJFC" title = "Odaberite opciju" required>
																							
																						</select>
																					</div>
																				</div>
																			</div>
																		</div>

																		<div class="form-group">
																			<div class="col-sm-12">
																				<label for="notesCRJFC" class="col-sm-4 control-label">
																					Bilješka:
																				</label>
																				<div class="col-sm-8">
																					<div class="materail-input-block materail-input-block_success materail-input_slide-line">
																						<textarea class="form-control materail-input material-textarea" name="notesCRJFC" id="notesCRJFC" placeholder="Unesite bilješku" rows="4"></textarea>
																						<span class="materail-input-block__line"></span>
																					</div>
																				</div>
																			</div>
																		</div>

																		<div class="form-group hidden">    
																			<div class="col-sm-12">
																				<label for="castingDateCRJFC" class="col-sm-4 control-label">
																					<span class="text-danger">
																						*
																					</span>
																					Datum castinga:
																				</label>
																				<div class="col-sm-8">
																					<div class="">
																						<select class="selectpicker" id="castingDateCRJFC" name="castingDateCRJFC" title = "Odaberite opciju">
																							
																						</select>
																					</div>
																				</div>
																			</div>
																		</div>

																		<div class="form-group hidden">    
																			<div class="col-sm-12">
																				<label for="interviewTimeCRJFC" class="col-sm-4 control-label">
																					<span class="text-danger">
																						*
																					</span>
																					Satnica intervjua:
																				</label>
																				<div class="col-sm-8">
																					<div class="">
																						<select class="selectpicker" id="interviewTimeCRJFC" name="interviewTimeCRJFC" title = "Odaberite opciju">
																							
																						</select>
																					</div>
																				</div>
																			</div>
																		</div>

																		<div class="form-group">    
																			<div class="col-sm-12">
																				<label for="timeCRJFC" class="col-sm-4 control-label">
																					<span class="text-danger">
																						*
																					</span>
																					Termin poziva:
																				</label>
																				<div class="col-sm-8">
																					<div class="">
																						<input type="text" class="form-control flatpickr-input active" name="timeCRJFC" id="timeCRJFC" placeholder="Termin" readonly="readonly" required>
																					</div>
																				</div>
																			</div>
																		</div>

																		<div class="form-group idk_margin_top20">
																			<div class="col-md-offset-2 col-sm-8 text-center">
																				<small>
																					<strong>
																						Sva polja označena sa 
																						<span class="text-danger">
																							*
																						</span>  
																						su obavezna!
																					</strong>
																				</small>
																			</div>
																		</div>
																	</div>
																</div>
																<div class="modal-footer material-modal__footer">
																	<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
																	<button type="submit" class="btn btn-success material-btn material-btn_success" form="completeTransferJFCForm">
																		<i class="fa fa-check-square-o" aria-hidden="true"></i> Spremi
																	</button>
																</div>
															</form>
														</div>
													</div>
												</div>
											</div>
										</div>
										<script>
											function setDataTableJFC() {
												if ($('.contentJFC', '#jobsForCandidate').find('table#tableJFC').length) {
													if($.fn.DataTable.isDataTable('#tableJFC', '#jobsForCandidate') == false){
														$('#tableJFC', '#jobsForCandidate').DataTable({
															responsive: true,
															"order": [[ 0, "desc" ]],
															"bAutoWidth": false,
															"aoColumns": [
																{"width":"5%"},
																{"width":"15%"},
																{"width":"15%"},
																{"width":"55%","bSortable":false},
																{"width":"10%","bSortable":false},
															]
														});
													}
												}
											};
											function destroyDataTableJFC() {
												if ($('.contentJFC', '#jobsForCandidate').find('table#tableJFC').length) {
													if($.fn.DataTable.isDataTable('#tableJFC', '#jobsForCandidate') == true){
														$('#tableJFC', '#jobsForCandidate').DataTable().clear().destroy();
														$('#tableJFC', '#jobsForCandidate').empty();
													}
												}
											};
											function getJFC() {
												let candidateIdJSC = `<?php echo $kandidat_id; ?>`;
												$('.loaderJFC', '#jobsForCandidate').removeClass('hidden');
												setTimeout(function() {
													$.ajax({
														url: 'ajax_data.php?page=jobs_for_candidate',
														type: 'POST',
														dataType: 'html',
														data:{
															'candidateIdJSC': candidateIdJSC,
														},
														success : function (response){
															if (response != '') {
																$('.loaderJFC', '#jobsForCandidate').addClass('hidden');
																$('.contentJFC', '#jobsForCandidate').removeClass('hidden').html(response).promise().then(function() {
																	setDataTableJFC();
																});
															}
														},
														error: function (xhr, ajaxOptions, thrownError) {
															errorInfoJFC(xhr, ajaxOptions, thrownError);
														}
													});
												}, 1000);
											};
											function errorInfoJFC(xhr, ajaxOptions, thrownError) {
												$('.loaderJFC', '#jobsForCandidate').addClass('hidden');
												$('.contentJFC', '#jobsForCandidate').html(`
													<div class="alert alert-danger text-center" role="alert">
														<i class="fa fa-info-circle fa-3x" aria-hidden="true"></i>
														<br>
														<br>
														<h4>
															<strong>
															`+((xhr.status == 500) ? xhr.responseText : thrownError)+`
															</strong>
														</h4>
													</div>
												`).removeClass('hidden'); 
											}; 
											function resetInfoJFC() {
												$('.contentJFC', '#jobsForCandidate').addClass('hidden');
												destroyDataTableJFC();
												$('.contentJFC', '#jobsForCandidate').html(''); 
												$('.loaderJFC', '#jobsForCandidate').addClass('hidden');
												responseMessageJFC('', false, '');
											};
											function responseMessageJFC(type, shown, message) {
												if (shown === true) {
													$('.messageJFC','#jobsForCandidate').html(`
														<div class="alert alert-` + type + ` text-center" role="alert">
															` + message + `
														</div>
													`).removeClass('hidden');
													$('#jobsForCandidate').animate({
														scrollTop: $('#jobsForCandidate').offset().top
													}, 1000);
													$('.messageJFC', '#jobsForCandidate').focus();
												} else {
													$('.messageJFC','#jobsForCandidate').html('').addClass('hidden');
												}
											};
											$('#jobsForCandidate').on('shown.bs.modal', getJFC);
                                    		$('#jobsForCandidate').on('hidden.bs.modal', resetInfoJFC);

											function getAgentInCallWithCandidateJFC(thisRow) {
												let candidate_id = $(thisRow).data("candidate_id");
												let nalog_id = $(thisRow).data("nalog_id");
												$.ajax({
													url: 'ajax_data.php?page=get_agent_incall_with_candidate',
													type: 'POST',
													dataType: 'JSON',
													data:{
														'kandidat_id': candidate_id,
													},
													success : function (response){
														if (response.status === 1) {
															$('#jobsForCandidate').modal('hide');
															resetInfoJFC();
															setTimeout(function() {
																$('#completeTransferJFC').modal('show');
																getCTJFC(nalog_id, candidate_id);
															}, 300);
														} else if (response.status === 0) {
															responseMessageJFC('warning', true, response.message);
														}
													},
													error: function (xhr, ajaxOptions, thrownError) {
														responseMessageJFC('danger', true, thrownError);
													}
												});
											};
											function responseMessageCTJFC(type, shown, message) {
												if (shown === true) {
													$('.messageCTJFC','#completeTransferJFC').html(`
														<div class="alert alert-` + type + ` text-center" role="alert">
															` + message + `
														</div>
													`).removeClass('hidden');
													$('#completeTransferJFC').animate({
														scrollTop: $('#completeTransferJFC').offset().top
													}, 1000);
													$('.messageCTJFC', '#completeTransferJFC').focus();
												} else {
													$('.messageCTJFC','#completeTransferJFC').html('').addClass('hidden');
												}
											};
											function getCTJFC(nalog_id, candidate_id) {
												$.ajax({
													url: 'ajax_data.php?page=get_appointment_details_for_nalog',
													type: 'POST',
													dataType: 'JSON',
													data:{
														'nalog_id': nalog_id,
													},
													success : function (response){
														setFormCTJFC(response, nalog_id, candidate_id);
													},
													error: function (xhr, ajaxOptions, thrownError) {
														responseMessageCTJFC('danger', true, thrownError);
													}
												});
											}; 
											function setDefaultFormCTJFC() {
												$('#nalogCRJFC','#completeTransferJFC').val(null);

												$('#candidateCRJFC','#completeTransferJFC').val(null);

												if ($('#statusCRJFC','#completeTransferJFC').children().length > 0) {
													$('#statusCRJFC','#completeTransferJFC').empty();
												}
												$('#statusCRJFC','#completeTransferJFC').val(null).selectpicker('refresh');

												$('#notesCRJFC','#completeTransferJFC').val(null); 

												if ($('#timeCRJFC','#completeTransferJFC').data('flatpickr') === undefined) {
													$('#timeCRJFC','#completeTransferJFC').flatpickr({
														enableTime: true,
														timeFormat: "H:i",
														disableMobile: "true",
														time_24hr: true, 
														defaultDate: null
													});
												}

												if ($('#castingDateCRJFC', '#completeTransferJFC').children().length > 0) {
													$('#castingDateCRJFC', '#completeTransferJFC').empty();
												}
												$('#castingDateCRJFC', '#completeTransferJFC').val(null).selectpicker('refresh').prop('required', false).closest('.form-group').addClass('hidden'); 

												if ($('#interviewTimeCRJFC', '#completeTransferJFC').children().length > 0) {
													$('#interviewTimeCRJFC', '#completeTransferJFC').empty();
												}
												$('#interviewTimeCRJFC', '#completeTransferJFC').val(null).selectpicker('refresh').prop('required', false).closest('.form-group').addClass('hidden');

												responseMessageCTJFC('', false, '');
											}; 
											function setFormCTJFC(response, nalog_id, candidate_id) {
												
												setDefaultFormCTJFC();

												$('#nalogCRJFC','#completeTransferJFC').val(nalog_id);
												$('#candidateCRJFC','#completeTransferJFC').val(candidate_id);
												
												if (response.tf_status === 14) {
													$('#statusCRJFC', '#completeTransferJFC').append($('<option>', {
														value: response.tf_status,
														text: response.tf_status_naziv
													})).val(response.tf_status).selectpicker('refresh');
												} else if (response.tf_status === 16) {
													$('#statusCRJFC', '#completeTransferJFC').append($('<option>', {
														value: response.tf_status,
														text: response.tf_status_naziv
													})).val(response.tf_status).selectpicker('refresh');

													if (response.casting_appointments) {
														response.casting_appointments.forEach(function(casting_appointments_info) {
															$('#castingDateCRJFC', '#completeTransferJFC').append($('<option>', {
																value: casting_appointments_info.pap_id,
																text: casting_appointments_info.pap_date,
																"data-subtext": casting_appointments_info.pap_city,
																"data-nalog_id": nalog_id,
																"data-candidate_id": candidate_id,
																"data-custom": casting_appointments_info.terminPozivaEndpoint
															}));
														});
														$('#castingDateCRJFC', '#completeTransferJFC').selectpicker('refresh').prop('required', true).closest('.form-group').removeClass('hidden');
													}
												}
											};
											$('#castingDateCRJFC', '#completeTransferJFC').on('change', function() {
												var customValue = $(this).find('option:selected').data('custom');
												var nalogIdValue = $(this).find('option:selected').data('nalog_id');
												var candidateIdValue = $(this).find('option:selected').data('candidate_id');
												var papIdValue = $(this).val();
												$.ajax({
													url: 'ajax_data.php?page=check_appointment_time_for_appointment',
													type: 'POST',
													dataType: 'JSON',
													data:{
														'pap_id' : papIdValue
													},
													success : function (response){
														if ($('#interviewTimeCRJFC', '#completeTransferJFC').children().length > 0) {
															$('#interviewTimeCRJFC', '#completeTransferJFC').empty();
														}
														response.forEach(function(item) {
															$('#interviewTimeCRJFC', '#completeTransferJFC').append($('<option>', {
																value: item.pah_id,
																text: item.pah_time
															}));
														});
														$('#interviewTimeCRJFC', '#completeTransferJFC').selectpicker('refresh').prop('required', true).closest('.form-group').removeClass('hidden');

														$.ajax({
															url: 'ajax_data.php?page='+customValue+'',
															type: 'POST',
															data:{
																'tf_nalog_id': nalogIdValue,
																'tf_casting_id': papIdValue
															},
															success : function (response){
																$('#timeCRJFC','#completeTransferJFC').flatpickr({
																	enableTime: true,
																	timeFormat: "H:i",
																	disableMobile: "true",
																	time_24hr: true,
																	defaultDate: response, 
																	defaultTime: '09:00'
																});
															},
															error: function (xhr, ajaxOptions, thrownError) {
																responseMessageCTJFC('danger', true, thrownError);
															}
														});
													},
													error: function (xhr, ajaxOptions, thrownError) {
														responseMessageCTJFC('danger', true, thrownError);
													}
												});
											});
											$('#completeTransferJFC').on('hidden.bs.modal', setDefaultFormCTJFC);
											function getAgentInCallWithCandidateCTJFC() {
												let nalog_id = $('#nalogCRJFC', '#completeTransferJFC').val();
												let candidate_id = $('#candidateCRJFC', '#completeTransferJFC').val();
												$.ajax({
													url: 'ajax_data.php?page=get_agent_incall_with_candidate',
													type: 'POST',
													dataType: 'JSON',
													data:{
														'kandidat_id': candidate_id,
													},
													success : function (response){
														if (response.status === 1) {
															responseMessageCTJFC('', false, '');
															$('#completeTransferJFCForm', '#completeTransferJFC').unbind('submit').submit();
														} else if (response.status === 0) {
															responseMessageCTJFC('warning', true, response.message);
														}
													},
													error: function (xhr, ajaxOptions, thrownError) {
														responseMessageCTJFC('danger', true, thrownError);
													}
												});
											};
											$('#completeTransferJFCForm', '#completeTransferJFC').on('submit', function(event) {
												event.preventDefault();
												getAgentInCallWithCandidateCTJFC();
											});
										</script>
									<!--
									Odgovarajući poslovi za kandidata END
								-->

							</ul>
							<?php 
						} ?>
						<hr />
						<?php
									
									$status_prijave_query = $db->prepare("
														SELECT status_id,kandidat_drzavljanstvo_vrsta FROM idk_kandidati 
														join idk_kandidat_status_prijave on kandidat_status_prijave=status_id
														WHERE kandidat_id = :kandidat_id");

									$status_prijave_query->execute(array(':kandidat_id' => $kandidat_id));
									$status_prijave_result = $status_prijave_query->fetch();
									$status_prijave_id 				= $status_prijave_result['status_id'];
									$kandidat_drzavljanstvo_vrsta 	= $status_prijave_result['kandidat_drzavljanstvo_vrsta']; 
									
									
						?>
						<div id="myTabs" class="panel-group material-tabs-group">
							<ul class="nav nav-tabs material-tabs material-tabs_primary">
								<li class="active"><a href="#info" class="material-tabs__tab-link" data-toggle="tab">Informacije</a></li>
								<?php if(getEmployeeStatus() != 20){
									?><li><a href="#notes" class="material-tabs__tab-link" data-toggle="tab">Bilješke</a></li>
									<?php 
									if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "18" , $getEmployeeStatus )) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){	?><li><a href="#documents" class="material-tabs__tab-link" data-toggle="tab">Dokumenti</a></li><?php } ?>
									<!-- <li><a href="#important" class="material-tabs__tab-link" data-toggle="tab">Važno</a></li> -->
									<li><a href="#projekti" class="material-tabs__tab-link" data-toggle="tab">Projekti</a></li>
									<?php 
								} ?>
								<li><a href="#logovi" class="material-tabs__tab-link" data-toggle="tab">Logovi</a></li>
								<?php if(getEmployeeStatus() != 20){
									?>
									<li><a href="#projekcija" class="material-tabs__tab-link" data-toggle="tab">Projekcija</a></li>
									<li><a href="#proces_odlaska" class="material-tabs__tab-link" data-toggle="tab" style="<?php if($kandidat_drzavljanstvo_vrsta=="EU državljanin" AND $status_prijave_id>=9){ echo "display: block;";} else if($status_prijave_id<12 AND $status_prijave_id!=10 AND $status_prijave_id!=4){ echo "display: none;"; } ?>">Proces odlaska</a></li>
									<?php 
									if((in_array("1",$employee_status)) OR (in_array("18",$employee_status)) OR (in_array("18",$employee_supervizor))){
										?>
										<!-- 
											TASK FORCE NOTES -> TABS START
										-->
										<li><a href="#task_force_notes" class="material-tabs__tab-link" data-toggle="tab">Task Force Bilješke</a></li>
										<!-- 
											TASK FORCE NOTES -> TABS END
										-->
										<?php 
									}
								}
								?>
							</ul>
							<div class="tab-content materail-tabs-content">
								<div class="tab-pane fade active in" id="info">
									<?php 
									if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "4" , $employee_status)) OR (in_array("18",$employee_status)) OR (in_array( "1" , $employee_status)) OR (in_array( "20" , $employee_status))){ ?>
										<div class="row">
											<div class="col-sm-4">
												<div id="getCandidateGroup" data-id="<?php echo $kandidat_id ?>">

												</div>
											</div>
											<div class="col-sm-8">
												<div id="getCandidateStatus" data-id="<?php echo $kandidat_id ?>">

												</div>
											</div>
											
											<div class="col-sm-12">
												<div class="row">
													<div class="col-sm-6">
														<?php if($kandidat_nostrifikacija == 1){?>
															<style>
																.linear {
																	animation: blink 2s linear infinite;
																	margin-left: 2rem;
																	font-size: 2rem;
																	border-radius: 10px;
																	box-shadow: 2px 2px #c5d4c9;
																}
																@keyframes blink{
																	0% {
																		opacity: .1;
																	}
																	50% {
																		opacity: .5;
																	}
																	100% {
																		opacity: 1;
																	}   
																}
															</style>
															<p class="label label-success material-label material-label_success main-container__column linear">
																Kandidat već ima nostrifikovanu diplomu!
															</p>
														<?php }?>
													</div>
													<style>
														#kandidat_projekat{
															border-radius: 0!important;
															border: 2px solid #ccc!important;
														}
													</style>
													<script>
														$( document ).ready(function() {
															
															var status_prijave_select = $(document).find('select[name="kandidat_status_prijave').val();
															if(status_prijave_select == 4 || status_prijave_select == 10)
																$(".zaposlen_kod").show();
															$( "#kandidat_status_prijave" ).change(function() {
																var value = $(this).val();
																
																$.ajax({
																	url: 'ajax.php?page=edit_projekat_status_p',
																	type: 'POST',
																	data: {'kandidat_id': "<?php echo $kandidat_id; ?>", "status_id": value},
																	dataType: 'html',
																})
																.done(function(data){
																	
																})
																
																if(value == 4 || value == 10)
																	$(".zaposlen_kod").show();
																else
																	$(".zaposlen_kod").hide();
															});
															
															$( "#kandidat_spisak_klijenata" ).change(function() {
																var value_kompanija = $(this).val();
																
																$.ajax({
																	url: 'ajax.php?page=edit_zaposlen_kod',
																	type: 'POST',
																	data: {'kandidat_id': "<?php echo $kandidat_id; ?>", "kompanija_id": value_kompanija},
																	dataType: 'html',
																})
																.done(function(data){
																	
																})
																
															});
														});
													</script>
													<div class="col-sm-3 zaposlen_kod" style="display: none;">
														<div class="form-group">
															<label for="kandidat_zaposlen_kod"><b>Zaposlen kod</b></label>
															<select class="form-control selectpicker" id="kandidat_spisak_klijenata" data-live-search="true"  >
																<option value="" selected disabled>Odaberi kompaniju</option>
																<?php															
																	
																	$query_projekt_nk = $db->prepare("
																		SELECT pk_projectid FROM idk_project_kandidati WHERE pk_kandidatid = $kandidat_id ORDER BY pk_projectid DESC
																	");
																	$query_projekt_nk->execute();
																	$projekt_nk = $query_projekt_nk->fetch();
																	$projectid_nk = $projekt_nk['pk_projectid'];
																	
																	$query_p_nalog_k = $db->prepare("
																		SELECT project_nalogid FROM idk_projects WHERE project_id = $projectid_nk
																	");
																	$query_p_nalog_k->execute();
																	$p_nalog_k = $query_p_nalog_k->fetch();
																	$p_nalogid_k = $p_nalog_k['project_nalogid'];
																	// var_dump($p_nalogid_k);
																	// exit();
																	
																	$query_pn_kompanija = $db->prepare("
																		SELECT kompanija_id FROM idk_nalozi WHERE nalog_id = $p_nalogid_k
																	");
																	$query_pn_kompanija->execute();
																	$pn_kompanija = $query_pn_kompanija->fetch();
																	$pn_kompanijaid = $pn_kompanija['kompanija_id'];
																	
																	$query_kompanije1 = $db->prepare("
																		SELECT company_name, company_id FROM idk_companies WHERE company_status = 1 and company_id = $pn_kompanijaid
																	");
																	$query_kompanije1->execute();
																	$kompanije1 = $query_kompanije1->fetch();
																	if(!empty($kompanije1)){
																		$kompanija_id_1 = $kompanije1['company_id'];
																	?>
																		<option value="<?php echo $kompanija_id_1; ?>" <?php if($kompanija_id_1 == $kandidat_zaposlen_kod){echo "selected";} ?> ><?php echo $kompanije1['company_name']; ?></option>
																	<?php
																	}else
																		$kompanija_id_1 = 0;
																	
																	$query_kompanije = $db->prepare("
																		SELECT company_name, company_id FROM idk_companies WHERE company_status = 1 AND company_id != $kompanija_id_1
																	");
																	
																	$query_kompanije->execute();
																	while($kompanije = $query_kompanije->fetch()){
																		$company_id = $kompanije['company_id'];
																		$company_name = $kompanije['company_name'];
																		
																		?>
																		<option value="<?php echo $company_id; ?>" <?php if($company_id == $kandidat_zaposlen_kod){echo "selected";} ?> ><?php echo substr($company_name, 0, 50); ?></option>
																		<?php
																	}
																?>
															</select>
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-sm-3" >
														<div class="form-group">
															<label for="kandidat_status_prijave"><b>Status prijave</b></label>
															<select class="form-control" id="kandidat_status_prijave" name="kandidat_status_prijave" disabled>
																<option value="nedefinisan">Nedefinisan</option>
																<?php 
																	$query_projekti = $db->prepare("
																					SELECT status_id, status_naziv
																					FROM idk_kandidat_status_prijave
																					WHERE status_aktivan = 1
																					");
						
																	$query_projekti->execute();
																	while($projekti = $query_projekti->fetch()){
						
																		$status_id = $projekti['status_id'];
																		$status_name = $projekti['status_naziv'];
																		
																	// KOJEM PROJEKTU PRIPADA	
																	$query_projekti_kandidat = $db->prepare("
																					SELECT kandidat_status_prijave
																					FROM idk_kandidati
																					WHERE kandidat_id = $kandidat_id
																					");
						
																	$query_projekti_kandidat->execute();
																	$rowProject = $query_projekti_kandidat->fetch();
						
																	$kand_status = $rowProject['kandidat_status_prijave'];															
																?>
																<option value="<?php echo $status_id; ?>" <?php if($status_id == $kand_status){echo "selected";}else{} ?>><?php echo $status_name; ?></option>
																
																<?php } ?>
															</select>
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-sm-3" >
														<div class="form-group">
															<label for="kandidat_status_prijave"><b>Status DIPL</b></label>
															<?php 
																if($kandidat_dipl_id != 0){ 
																	?>
																		<p>
																			<?php echo getStatusDIPLKandidatR(getStatusValueDIPLKandidatR($kandidat_dipl_id)["status"], getStatusValueDIPLKandidatR($kandidat_dipl_id)["podstatus"]); ?>
																		
																		<?php 
																			if (getStatusValueDIPLKandidatR($kandidat_dipl_id)["status"] == 6){
																				$resultFullRecognition = getCandidateFullRecognitionArrayR($kandidat_dipl_id,2);
																				
																				if ($resultFullRecognition["count"] != 0) {
																					echo ( ( ($resultFullRecognition["full_recognition"] == "") ? '<span class="label label-danger material-label material-label_danger main-container__column text-center">Nije određeno</span>' : ( ($resultFullRecognition["full_recognition"] == 0) ? '<span class="label label-primary material-label material-label_primary main-container__column text-center">Djelimično priznata</span>' : ( ($resultFullRecognition["full_recognition"] == 1) ? '<span class="label label-success material-label material-label_success main-container__column text-center">Potpuno priznata</span>' : ( ($resultFullRecognition["full_recognition"] == 2) ? '<span class="label label-info material-label material-label_info main-container__column text-center">Evaluacija</span>' : '<span class="label label-danger material-label material-label_danger main-container__column text-center">Nije određeno</span>' ) ) ) ) );
																				} else {
																					echo '<span class="label label-danger material-label material-label_danger main-container__column text-center">Nije pronađena informacija</span>';
																				}
																			}
																		?>
																		</p>
																	<?php 
																}else{ 
																	?>
																		<p>
																			<span style = "background-color: rgb(131, 144, 152); color: white;" class="label label-default material-label material-label_default main-container__column text-center">
																				Nema DIPL profil
																			</span>
																		</p>
																	<?php 
																} 
															?>
														</div>
													</div>
													<?php
														/*
															Na zahtjev Adila uklonjeno
															$nalogIDPoslodavacTraziJezik = intval(getNalogIdByCandidateId($kandidat_id));
															if($nalogIDPoslodavacTraziJezik != 0){
																$nk_poslodavac_trazi_jezik = getNalogPoslodavacTraziJezik($nalogIDPoslodavacTraziJezik); 
																if (in_array($nalog_poslodavac_trazi_jezik, array(0,1))) {
																	?>
																		<div class="col-sm-3" >
																			<div class="form-group">
																				<label>
																					<b>Poslodavac traži jezik </b><i class="fa fa-info-circle" aria-hidden="true" title="Parametar koji određuje da li poslodavac za koji je kandidat trenutno vezan, traži jezik bez obzira na ishod nostrifikacije potpuna/evaluacija."></i>
																				</label>
																				<div>
																					<span 
																						class="label label-<?php echo (($nk_poslodavac_trazi_jezik == 0) ? 'success' : 'warning');?> material-label material-label_<?php echo (($nk_poslodavac_trazi_jezik == 0) ? 'success' : 'warning');?> main-container__column text-center"
																					>
																						<?php echo (($nk_poslodavac_trazi_jezik == 1) ? 'DA ' : 'NE ');?>

																					</span>
																				</div>
																			</div>
																		</div>
																	<?php
																}
															}
														*/
													?>
												</div>
												<hr/>
											</div>
										</div><?php 
									} ?>
									<?php 
									if(in_array( "18" , $employee_status) OR in_array( "18" , $employee_supervizor))  { ?>
											<div class="row">
												<div class="col-xs-12">
													<div class="col-xs-12" id="tf_alert_nema_kandidata">
								
													</div>
													<div class="col-xs-6">
														<h4 class="idk_info_title"><i class="fa fa-angle-down" aria-hidden="true"></i> Task Force</h4>
													</div>
													<div class="col-xs-6">
														<button id="tf_button_next" <?php echo (checkAgentReservation($logged_employee_id)) ? "disabled" :  ""; ?> class="pull-right btn material-btn material-btn_success main-container__column" aria-hidden="true"><i class="fa fa-arrow-right" aria-hidden="true"></i> TRAŽI DALJE</button>
														<?php if(checkAgentReservationForCandidate($logged_employee_id, $kandidat_id)){ ?>
															<!-- <button id="unreserveAgent" style="margin-right: 10px;" class="pull-right btn material-btn material-btn_danger main-container__column" aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i> ODREZERVIŠI SE</button> -->
														<?php } ?>
														<?php if(isset($_SESSION['IN_CALL'])){ ?>
															<button
																data-toggle="modal" 
																data-target="#potvrda_prekida_modal" 
																id ="anchor_prekid" 
																href=""
																id="task_force_hangup" 
																style="margin-right: 10px;display: none;"  
																class="pull-right btn material-btn material-btn_danger main-container__column" 
																aria-hidden="true">
																<i class="fa fa-phone" aria-hidden="true"></i> PREKINI
															</button>
														<?php }else{?>
															<button
																id="anchor_poziv" 
																data-toggle="modal" 
																data-target="#potvrda_poziva_modal" 
																href=""
																id="task_force_call" 
																style="margin-right: 10px; margin-left: 10px;" 
																display: none;<?php echo (checkCandidateReservation($kandidat_id)) ? "disabled" :  ""; ?> 
																class="pull-right btn material-btn material-btn_success main-container__column" 
																aria-hidden="true">
																<i class="fa fa-phone" aria-hidden="true"></i> POZOVI
															</button>
														<?php } ?>
														<button
														data-toggle="modal" 
															data-target="#potvrda_prekida_modal" 
															id ="anchor_prekid" 
															href=""
															id="task_force_hangup" 
															style="margin-right: 10px;display: none;"  
															class="pull-right btn material-btn material-btn_danger main-container__column"
															aria-hidden="true">
															<i class="fa fa-phone" aria-hidden="true"></i> PREKINI
														</button>
													</div>
												</div>
											</div>
											<div class="row idk_employee_info" style="margin-top: 5px;">
												<div class="col-md-6">
												<div class="row">
														<strong class="col-sm-4 text-right">Vrsta taska:</strong>
														<div class="col-sm-8">
															<span class="label label-success material-label material-label_success main-container__column">
																<?php 
																	if(isset($_GET["vrsta_id"])){
																		$tf_vrsta_id = $_GET["vrsta_id"];
																		echo getVrstaName($tf_vrsta_id);
																	}else{
																		if(checkAgentReservationForCandidate($logged_employee_id, $kandidat_id)){
																			$query_vrsta_tf = $db -> prepare("
																				SELECT tf_vrsta_id
																				FROM idk_tf_reservations
																				WHERE tf_kandidat_id  = :tf_kandidat_id AND tf_agent_id = :tf_agent_id;
																			");

																			$query_vrsta_tf -> execute(array(':tf_kandidat_id' => $kandidat_id, ':tf_agent_id' => $logged_employee_id));

																			$row = $query_vrsta_tf -> fetch();

																			$tf_vrsta_id = $row["tf_vrsta_id"];
																			echo getVrstaName($tf_vrsta_id);
																		}else{
																			if(getLastTFVrsta($kandidat_id)){
																				$tf_vrsta_id = getLastTFVrsta($kandidat_id);
																				echo getVrstaName($tf_vrsta_id);
																			}else{
																				$tf_vrsta_id = null;
																				echo "Unknown";
																			}
																		}
																	} 
																?>
															</span>
														</div>
													</div>
													<div class="row">
														<strong class="col-sm-4 text-right">Nalog:</strong>
														<div class="col-sm-8">
															<span class="label label-success material-label material-label_success main-container__column">
																<?php 
																	if(isset($_GET["nalog_id"])){
																		$tf_nalog_id = $_GET["nalog_id"];
																		echo getNalogNameById($tf_nalog_id);
																	}else{
																		if(checkAgentReservationForCandidate($logged_employee_id, $kandidat_id)){
																			$query_nalog_tf = $db -> prepare("
																				SELECT tf_nalog_id
																				FROM idk_tf_reservations
																				WHERE tf_kandidat_id  = :tf_kandidat_id AND tf_agent_id = :tf_agent_id;
																			");

																			$query_nalog_tf -> execute(array(':tf_kandidat_id' => $kandidat_id, ':tf_agent_id' => $logged_employee_id));

																			$row = $query_nalog_tf -> fetch();

																			$tf_nalog_id = $row["tf_nalog_id"];
																			echo getNalogNameById($tf_nalog_id);
																		}else{
																			if(getLastTFNalog($kandidat_id, $tf_vrsta_id)){
																				$tf_nalog_id = getLastTFNalog($kandidat_id, $tf_vrsta_id);
																				echo getNalogNameById($tf_nalog_id);
																			}else{
																				$tf_nalog_id = null;
																				echo "Unknown";
																			}
																		}
																	} 
																?>
															</span>
														</div>
													</div>
													
													<div class="row">
														<strong class="col-sm-4 text-right">Projekt:</strong>
														<div class="col-sm-8">
															<span class="label label-success material-label material-label_success main-container__column">
																<?php 
																	if(isset($_GET["projekt_id"])){
																		$tf_projekt_id = $_GET["projekt_id"];
																		echo getProjectFullName($tf_projekt_id);
																	}else{
																		if(checkAgentReservationForCandidate($logged_employee_id, $kandidat_id)){
																			$query_projekt_tf = $db -> prepare("
																				SELECT tf_projekt_id
																				FROM idk_tf_reservations
																				WHERE tf_kandidat_id  = :tf_kandidat_id AND tf_agent_id = :tf_agent_id;
																			");

																			$query_projekt_tf -> execute(array(':tf_kandidat_id' => $kandidat_id, ':tf_agent_id' => $logged_employee_id));

																			$row = $query_projekt_tf -> fetch();

																			$tf_projekt_id = $row["tf_projekt_id"];
																			echo getProjectFullName($tf_projekt_id);
																		}else{
																			if(getLastTFProjekt($kandidat_id, $tf_vrsta_id)){
																				$tf_projekt_id = getLastTFProjekt($kandidat_id, $tf_vrsta_id);
																				echo getProjectFullName($tf_projekt_id); 
																			}else{
																				$tf_projekt_id = null;
																				echo "Unknown";
																			}
																		}
																	}
																?>
															</span>
														</div>
													</div>
													<div class="row">
														<strong class="col-sm-4 text-right">Zadnji Task Force status:</strong>
														<div class="col-sm-8">
															<span class="label label-info material-label material-label_info main-container__column"  id="zadnji_task_force_status"><?php if(getLastTFStatus($kandidat_id, $tf_nalog_id, $tf_vrsta_id) != NULL){ echo getTaskForceStatusNameById(getLastTFStatus($kandidat_id, $tf_nalog_id, $tf_vrsta_id));}else{ echo "Unknown";} ?></span>  
															<?php if(hasInboundActiveCall($kandidat_id, $tf_nalog_id, $tf_vrsta_id)){?>
																<span class="label label-danger material-danger material-label_dangero main-container__column">INBOUND</span>
															<?php } ?>
														</div>
													</div>
													<div class="row">
														<strong class="col-sm-4 text-right">Bilješka:</strong>
														<div class="col-sm-8"><p id="tf_biljeska"><?php if(getTFNote(getLastTFId($kandidat_id, $tf_nalog_id, $tf_vrsta_id)) != NULL){ echo getTFNote(getLastTFId($kandidat_id, $tf_nalog_id, $tf_vrsta_id));}else{echo "Nema bilješke.";} ?></p></div>
													</div>
													<?php if(hasInboundActiveCall($kandidat_id, $tf_nalog_id, $tf_vrsta_id)){?>
													<div class="row">
														<strong class="col-sm-4 text-right">Inbound bilješka :</strong>
														<div class="col-sm-8"><p id="tf_biljeska"><?php if(getTFNote(getLastTFIdInbound($kandidat_id, $tf_nalog_id, $tf_vrsta_id)) != NULL){ echo getTFNote(getLastTFIdInbound($kandidat_id, $tf_nalog_id, $tf_vrsta_id));}else{echo "Nema bilješke.";} ?></p></div>
													</div>
													<?php } ?>
													<div class="row">
														<strong class="col-sm-4 text-right">Broj telefona:</strong>
														<div class="col-sm-8"><p id="tf_biljeska"><span style="padding: 5px; background: red; color:white; font-weight: bold;"><?php echo $kandidat_mobitel; ?></p></span></div>
													</div>
												</div>
												<div class="col-md-6">
													<?php if ($tf_nalog_id != NULL AND $tf_projekt_id != NULL AND $tf_vrsta_id != NULL){ ?>
														<div id="taskforce_form_message"></div>
														<form  id="taskforce_form" action="#" enctype="multipart/form-data" method="post" role="form" class="form-horizontal" style="padding: 20px; background: #f9f9f9;">
															<div class="form-group">
																<div class="">
																	<label for="taskforce_form_status" class="col-sm-3 control-label">
																		<b>Status:</b>
																	</label>
																	<div class="col-sm-9">
																		<div class="">
																			<select class="selectpicker" title = "Status" data-actions-box="true" name="taskforce_form_status" id="taskforce_form_status" required>
																				<?php
																				// Ako je casting ostaje isto jer je specificno
																				if($tf_vrsta_id == 1){
																					$lista = getTaskForceStatusesForCandidate($kandidat_id, $tf_vrsta_id);
																				} else{
																					$lista = getTaskForceStatusesForCandidatePosredovanjeObrada($tf_vrsta_id);
																				}

														
																				foreach($lista as $list){
																					echo '<option value="'.$list['tfs_id'].'" data-hasappointment="'.$list['tfs_appointment'].'" data-hasinterview = "'.$list['tfs_has_interview'].'" data-tfs_status_prijave = "'.$list['tfs_status_prijave'].'">'.$list['tfs_name'].'</option>';
																				}
																				?>
																			</select>
																		</div>
																	</div>
																</div>
															</div>
																<div class="form-group">
																	<div class="">
																		<label for="taskforce_form_biljeska" class="col-sm-3 control-label">
																			<b>Bilješka:</b>
																		</label>
																		<div class="col-sm-9">
																			<div class="regular_note">
																				<textarea class="form-control" name="taskforce_form_biljeska" id="taskforce_form_biljeska"></textarea>
																			</div>
																			<?php
																			$brojac_neuspjesnih_komunikacija = brojacNeuspjelaKomunikacija(getLastTFId($kandidat_id, $tf_nalog_id, 1));
																			?>
																			<div class="note_for_neuspjesna" style="display:none">
																				<select class="selectpicker" name="taskforce_form_biljeska" id="neuspjesna_biljeska" title = "Odaberi bilješku.">
																					<option value="OB + VIBER + WA: Kandidat pozvan na obični broj, Viber i WhatsApp — nije se javio na pozive. <?php echo $brojac_neuspjesnih_komunikacija + 1; ?>x">OB + VIBER + WA. <?php echo $brojac_neuspjesnih_komunikacija + 1; ?>x</option>
																					<option value="OB + VIBER: Kandidat pozvan na obični broj i Viber — nije se javio na pozive; nema WhatsApp. <?php echo $brojac_neuspjesnih_komunikacija + 1; ?>x">OB + VIBER (nema WA). <?php echo $brojac_neuspjesnih_komunikacija + 1; ?>x</option>
																					<option value="OB + WA: Kandidat pozvan na obični broj i WhatsApp — nije se javio na pozive; nema Viber. <?php echo $brojac_neuspjesnih_komunikacija + 1; ?>x">OB + WA (nema Viber). <?php echo $brojac_neuspjesnih_komunikacija + 1; ?>x</option>
																					<option value="VIBER + WA: Kandidat pozvan na Viber i WhatsApp — nije se javio na pozive. <?php echo $brojac_neuspjesnih_komunikacija + 1; ?>x">VIBER + WA (OB neaktivan). <?php echo $brojac_neuspjesnih_komunikacija + 1; ?>x</option>
																					<option value="VIBER (OB neaktivan / nema WA): Kandidat pozvan na Viber — nije se javio; obični broj neaktivan ili bez WhatsAppa. <?php echo $brojac_neuspjesnih_komunikacija + 1; ?>x">VIBER (OB neaktivan / nema WA). <?php echo $brojac_neuspjesnih_komunikacija + 1; ?>x</option>
																					<option value="WA (OB neaktivan / nema VIBER): Kandidat pozvan na WhatsApp — nije se javio; obični broj neaktivan ili bez Vibera. <?php echo $brojac_neuspjesnih_komunikacija + 1; ?>x">WA (OB neaktivan / nema VIBER). <?php echo $brojac_neuspjesnih_komunikacija + 1; ?>x</option>
																					<option value="Samo OB: Kandidat pozvan na obični broj — nije se javio; nema Viber ni WhatsApp. <?php echo $brojac_neuspjesnih_komunikacija + 1; ?>x">Samo OB. <?php echo $brojac_neuspjesnih_komunikacija + 1; ?>x</option>
																					<option value="Poslana poruka. <?php echo $brojac_neuspjesnih_komunikacija + 1; ?>x">Poslana poruka. <?php echo $brojac_neuspjesnih_komunikacija + 1; ?>x</option>
																				</select>
																			</div>
																		</div>
																	</div>
																</div>
																<div class="form-group" id="tf_razlog_odustajanja_form" style="display:none;">
																	<div class="">
																		<label for="tf_razlog_odustajanja" class="col-sm-3 control-label">
																			<b>Razlog odustajanja:</b>
																		</label>
																		<div class="col-sm-9">
																			<select class="selectpicker" id="tf_razlog_odustajanja" name="tf_razlog_odustajanja" data-live-search="true" title="Odaberi...">
																			<?php
																				$query_get_reasons = $db->prepare("SELECT rr_id, rr_name_bs FROM idk_reject_reasons WHERE rr_id != 1 AND rr_rejected_by = 1");
																				$query_get_reasons->execute();
																				while($result = $query_get_reasons->fetch()){
																					$rr_id 		= $result['rr_id'];
																					$rr_name_bs = $result['rr_name_bs'];
																			?>
																					<option value="<?php echo $rr_id; ?>"><?php echo $rr_name_bs; ?></option>
																			<?php
																				}
																			?>
																			</select>
																		</div>
																	</div>
																</div>
																<div class="form-group">
																	<div class="">
																		<div class="col-sm-3">
																		</div>
																		<div class="col-sm-9">
																			<select class="selectpicker" id="tf_note_attachment" name="tf_note_attachment" title = "Da li bilješka ima prilog?" required>
																				<option value="1">Da</option>
																				<option value="0">Ne</option>
																			</select>
																		</div>
																	</div>
																</div>
																<script>
																	$(document).ready(function() {
																		$(".tf_note_attachment_file_field").hide();
																		$('#tf_note_attachment_file').val(null);
																		$('#tf_note_attachment_file').removeAttr('required');

																		$('#tf_note_attachment').change(function() {

																			var tf_note_attachment_value = parseInt($("#tf_note_attachment").val());

																			$('#tf_note_attachment_file').val(null);
																			$('#tf_files-name').text("");
																			$('#tf_files-selected').text("");
																			$(".fileinput").fileinput("clear");

																			if ( tf_note_attachment_value === 1 ) {

																				$(".tf_note_attachment_file_field").show();
																				$('#tf_note_attachment_file').attr('required', 'true');

																			} else {
																				
																				$(".tf_note_attachment_file_field").hide();
																				$('#tf_note_attachment_file').removeAttr('required');

																			}
																		});
																	});
																</script>
																<div id="tf_file_alert_size" class="form-group hidden">
																	<div class="col-md-offset-2 col-sm-8">
																		<div class="alert material-alert material-alert_danger">Greška: Dokument koji pokuštavate dodati je veći od dozvoljene veličine.</div>
																	</div>
																</div>
																<div id="tf_file_alert_ext" class="form-group hidden">
																	<div class="col-md-offset-2 col-sm-8">
																		<div class="alert material-alert material-alert_danger">Greška: Format dokumenta kojeg pokušavate dodati nije dozvoljen.</div>
																	</div>
																</div>
																<div id="tf_file_alert_len" class="form-group hidden">
																	<div class="col-md-offset-2 col-sm-8">
																		<div class="alert material-alert material-alert_danger">Greška: Dozvoljeno je dodati maximalno 10 dokumenata.</div>
																	</div>
																</div>
																<div class="form-group tf_note_attachment_file_field">
																	<div class="col-md-offset-2 col-sm-8 text-center">
																		<div class="fileinput fileinput-new" data-provides="fileinput">
																			<span class="btn btn-default btn-file">
																				<span class="fileinput-new"> 
																					Izaberi dokument
																				</span>
																				<span class="fileinput-exists">
																					Promijeni
																				</span>
																				<input type="file" name="tf_note_attachment_file[]" id="tf_note_attachment_file" multiple="">
																			</span>
																			<i 
																				style="padding-top: 5px; padding-left: 10px; padding-right: 10px;" 
																				class="fa fa-question-circle fa-lg" 
																				data-toggle="tooltip" 
																				data-placement="right" 
																				title="Napomena: Moguće dodati najviše 10 dokumenata! Dokument ne smije biti veći od 5MB! - Dozvoljeni formati: jpg, jpeg, pdf, doc, docx, xls, xlsx, txt, ppt, pptx, png, csv" 
																				aria-hidden="true"
																			>
																			</i>
																			<br>
																			<span style="padding-top: 5px; padding-left: 10px; padding-right: 10px; word-break: break-all;" id = "tf_files-selected">
																			</span>
																			<br>
																			<span style="padding-top: 5px; padding-left: 10px; padding-right: 10px; word-break: break-all;" id = "tf_files-name">
																			</span>
																		</div>
																	</div>
																</div>
																<script>
																	$(function (){
																		$('#tf_note_attachment_file').change(function (){
																			//Script za provjeru više dokumenata
																			if($('#tf_note_attachment_file').val() !== ""){
																				var files = [];
																				var filesName = [];
																				files.push(this.files);
																				if(parseInt(this.files.length) < 11){
																					$('#tf_files-selected').text(this.files.length + " file selected.");
																					iterateFiles(files);
																					function iterateFiles(filesArray)
																					{
																						var zastavicaPrilog = 0;
																						for(var i=0; i<filesArray.length; i++){
																							//console.log("I"+ i);
																							for(var j=0; j<filesArray[i].length; j++){
																								//console.log("J"+ j);
																								//console.log(filesArray[i][j].name + " " + filesArray[i][j].size);
																								filesName.push(filesArray[i][j].name);
																								// alternatively: console.log(filesArray[i].item(j).name);
																								var ext = filesArray[i][j].name.split('.').pop().toLowerCase();
																								if($.inArray(ext, ['jpg', 'jpeg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png']) == -1) {
																									$('#tf_file_alert_ext').removeClass('hidden');
																									setTimeout(function(){
																										$('#tf_file_alert_ext').addClass('hidden');
																									}, 5000);
																									$('#tf_note_attachment_file').val(null);
																									zastavicaPrilog = 1;
																									$('#tf_files-selected').text("");
																									break;
																								}else{
																									$('#tf_file_alert_ext').addClass('hidden');
																								}
																								
																								var f = filesArray[i][j];
																								if (f.size > 5242880){
																									$('#tf_file_alert_size').removeClass('hidden');
																									setTimeout(function(){
																										$('#tf_file_alert_size').addClass('hidden');
																									}, 5000);
																									$('#tf_note_attachment_file').val(null);
																									zastavicaPrilog = 1;
																									$('#tf_files-selected').text("");
																									break;
																								}else{
																									$('#tf_file_alert_size').addClass('hidden');
																								}
																							}
																							if(zastavicaPrilog === 1){
																								break;
																							}
																						}
																						if(zastavicaPrilog === 0){
																							$('#tf_files-name').text("Files: "+ filesName.join(" , ") + "");
																						}else{
																							$('#tf_files-name').text("");
																						}
																					}
																				}else{
																					$('#tf_note_attachment_file').val(null);
																					$('#tf_files-name').text("");
																					$('#tf_files-selected').text("");
																					$('#tf_file_alert_len').removeClass('hidden');
																					setTimeout(function(){
																						$('#tf_file_alert_len').addClass('hidden');
																					}, 5000);
																				}
																			}
																		})
																	});
																</script>
																<div class="form-group">
																	<div class="">
																		<label for="taskforce_form_vazna_biljeska" class="col-sm-3 control-label">
																		</label>
																		<div class="col-sm-9">
																			<div class="form-check">
																				<input class="form-check-input" name="taskforce_form_vazna_biljeska" id="taskforce_form_vazna_biljeska" type="checkbox">
																				<label class="form-check-label" for="taskforce_form_vazna_biljeska">
																					Važna bilješka
																				</label>
																			</div>
																		</div>
																	</div>
																</div>

																<div id="taskforce_form_termin_intervju_div" style="display:none;">
																	<div class="form-group" >
																		<label for="termin_group" class="col-sm-3 control-label">
																			<b>Datum castinga:</b>
																		</label>
																		<div class="col-sm-9">
																				<select class="selectpicker" id="termin_group" name="termin_group">
																					<option disabled selected value=""> Odaberi </option>
																				<?php
																					$casting_id = getCastingIdForNalog($tf_nalog_id);
																					$formatted_casting_ids = implode(', ', $casting_id);

																					if(getLastTFStatus($kandidat_id, $tf_nalog_id, $tf_vrsta_id) == 16){ 
																						$query_get_appointment_candidate = $db->prepare("
																															SELECT
																																pca_pah_id,
																																pca_time,
																																pca_appointment_id
																															FROM
																																idk_pp_cand_appts
																															WHERE
																																pca_kandidat_id = :pca_kandidat_id AND pca_status = 1
																															");
																						$query_get_appointment_candidate->execute(array(
																							":pca_kandidat_id" => $kandidat_id
																						));

																						$result_get_appointment_candidate = $query_get_appointment_candidate->fetch();

																						$pca_pah_id 				= $result_get_appointment_candidate['pca_pah_id'];
																						$pca_appointment_id_cand 	= $result_get_appointment_candidate['pca_appointment_id'];
																						$pca_time_cand 				= $result_get_appointment_candidate['pca_time'];
																						
																						$get_appt_name = $db->prepare("
																														SELECT
																																pap_date,
																																pap_city,
																																date_sub(pap_date, INTERVAL pap_first_sending_number_days day) as ending_date
																															FROM
																																idk_pp_appointments
																															WHERE
																																pap_id  = :pap_id
																						");

																						$get_appt_name->execute(array(
																							":pap_id" => $pca_appointment_id_cand
																						));

																						$get_appt_name_row = $get_appt_name->fetch();

																						$pap_date_f_cand 			= date("d.m.Y", strtotime($get_appt_name_row['pap_date']));
																						$pap_city_f_cand 			= $get_appt_name_row['pap_city'];
																						$ending_date 				= $get_appt_name_row['ending_date'];
																					?>
																						
																						<option value="<?php echo $pca_appointment_id_cand;?>" ending_date="<?php echo $ending_date." 09:00:00"; ?>"><?php echo $pap_date_f_cand . " - " . $pap_city_f_cand; ?> (Kandidat je već vezan za ovaj termin)</option>
																					<?php 
																					} 
																					$pca_appointment_id_cand = $pca_appointment_id_cand ?? 0;

																					$query_get_appointment = $db->prepare("
																														SELECT
																															pap_date,
																															pap_id,
																															pap_city,
																															date_sub(pap_date, INTERVAL pap_first_sending_number_days day) as ending_date
																														FROM
																															idk_pp_appointments
																														LEFT JOIN idk_pp_cand_appts ON idk_pp_appointments.pap_id = idk_pp_cand_appts.pca_appointment_id 
																														WHERE
																															pap_group_id IN ($formatted_casting_ids) AND pap_date > NOW() - INTERVAL 1 DAY AND pap_id NOT IN ($pca_appointment_id_cand)
																														GROUP BY pap_id
																														");
																					$query_get_appointment->execute();
																					
																					$appointment_counter = $query_get_appointment->rowCount();
																					if($appointment_counter == 0){ ?>
																						<!-- <option disabled value="0">Nema Castinga</option> -->
																					<?php } ?>
																					
																					
																					<?php
																					while($result_get_appointment = $query_get_appointment->fetch()){
																						$pap_id 	= $result_get_appointment['pap_id'];
																						$pap_date 	= $result_get_appointment['pap_date'];
																						$pap_city	= $result_get_appointment['pap_city'];
																						$ending_date	= $result_get_appointment['ending_date'];
																						$pap_date_f = date("d.m.Y", strtotime($pap_date));
																				?>
																					<option value="<?php echo $pap_id; ?>" ending_date="<?php echo $ending_date." 09:00:00"; ?>" ><?php echo $pap_date_f . " - " . $pap_city; ?></option>
																				<?php
																					}
																				?>
																				</select>
																		</div>
																	</div>
																	<div class="form-group">
																		<label for="taskforce_form_termin_intervju" class="col-sm-3 control-label">
																			<b>Satnica intervjua:</b>
																		</label>
																		<div class="col-sm-9">
																			<div class="">
																				<select class="selectpicker"  name="taskforce_form_termin_intervju" id="taskforce_form_termin_intervju" placeholder="Termin">
																					<option disabled selected value=""> Odaberi </option>
																					<?php if(getLastTFStatus($kandidat_id, $tf_nalog_id, $tf_vrsta_id) == 16){ ?>
																						<option selected value="<?php if($pca_pah_id != NULL){echo $pca_pah_id;}else{echo $pca_time_cand;} ?>"> <?php echo $pca_time_cand; ?> (Kandidat već ima vezanu satnicu) </option>
																					<?php } ?>
																				</select>
																				<!-- <input type="text" class="form-control" name="taskforce_form_termin_intervju" id="taskforce_form_termin_intervju" placeholder="Termin" required> -->
																				<span class="materail-input-block__line"></span>
																			</div>
																		</div>
																	</div>
																</div>
																<!-- <script>
																	$("#taskforce_form_termin_intervju").flatpickr({
																		enableTime: true,
																		timeFormat: "H:i",
																		<?php if(getLastTFStatus($kandidat_id, $tf_nalog_id, $tf_vrsta_id) == 16){ ?>
																		defaultDate: "<?php echo date('H:i', strtotime($pca_time_cand)); ?>",
																		<?php } ?>
																		noCalendar: true,
																		disableMobile: "true",
																		time_24hr: true
																	});
																</script> -->
																<div class="form-group" id="taskforce_form_termin_poziva_div" style="display:none;">
																	<div class="">
																		<label for="taskforce_form_termin_poziva" class="col-sm-3 control-label">
																			<b>Termin poziva:</b>
																		</label>
																		<div class="col-sm-9">
																			<div class="">
																				<input type="text" class="form-control" name="taskforce_form_termin_poziva" id="taskforce_form_termin_poziva" placeholder="Termin" required>
																				<span class="materail-input-block__line"></span>
																			</div>
																		</div>
																	</div>
																</div>
															
																<script>
																	$("#taskforce_form_termin_poziva").flatpickr({
																		enableTime: true,
																		timeFormat: "H:i",
																		disableMobile: "true",
																		time_24hr: true
																	});
																</script>
																
																<!-- LANGUAGE BLOCKS START -->
																<!-- Unos nivoa jezika -->
																<div class="form-group" id="taskforce_language_level_block" style="display: none;">
																	<div class="form-group">
																		<label for="prelazak_na_veci_nivo" class="col-sm-3 control-label">
																			<b>Škola:</b>
																		</label>
																		<div class="col-sm-9">
																			<select class="selectpicker" id="language_ustanova" name="language_ustanova">
																				<option value="0">Samostalno</option>
																				<option value="1">Glosa</option>
																				<option value="2">Lingoda</option>
																				<option value="3">CPE</option>
																				<option value="4">OSD</option>
																			</select>
																		</div>
																	</div>
																	<div class="form-group">
																		<label for="prelazak_na_veci_nivo" class="col-sm-3 control-label">
																			<b>Nivo jezika:</b>
																		</label>
																		<div class="col-sm-9">
																			<select class="selectpicker" id="prelazak_na_veci_nivo" name="prelazak_na_veci_nivo">
																			<option value=""></option>
																			<?php
																				$languageLevels = ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'];

																				$activeLevel = getActiveLanguage($kandidat_id);
																				foreach ($languageLevels as $level) {
																					$disabled = ($level < $activeLevel) ? 'disabled' : '';
																					echo "<option value=\"$level\" $disabled>$level</option>";
																				}
																			?>
																			</select>
																		</div>
																	</div>

																	<div class="form-group">
																		<label for="prelazak_na_veci_nivo_podnivo" class="col-sm-3 control-label">
																			<b>Podnivo jezika:</b>
																		</label>
																		<div class="col-sm-9">
																			<select class="selectpicker" id="prelazak_na_veci_nivo_podnivo" name="prelazak_na_veci_nivo_podnivo">
																			<option value=""></option>
																			<option value="3">Podnivo 1</option>
																			<option value="4">Podnivo 2</option>
																			</select>
																		</div>
																	</div>

																	<div class="form-group" id="prelazak_na_veci_nivo_datum_pocetka_div">
																		<div class="">
																			<label for="prelazak_na_veci_nivo_datum_pocetka" class="col-sm-3 control-label">
																				<b>Datum početka:</b>
																			</label>
																			<div class="col-sm-9">
																				<div class="">
																					<input type="text" class="form-control" name="prelazak_na_veci_nivo_datum_pocetka" id="prelazak_na_veci_nivo_datum_pocetka" placeholder="Datum početka">
																					<span class="materail-input-block__line"></span>
																				</div>
																			</div>
																		</div>
																	</div>
																	<script>
																		$("#prelazak_na_veci_nivo_datum_pocetka").flatpickr({
																			disableMobile: "true",
																			minDate: "2000-01-01"
																		});
																	</script>

																	<div class="form-group">
																		<div class="">
																			<label for="prelazak_na_veci_nivo_datum_kraja" class="col-sm-3 control-label">
																				<b>Datum kraja:</b>
																			</label>
																			<div class="col-sm-9">
																				<div class="">
																					<input type="text" class="form-control" name="prelazak_na_veci_nivo_datum_kraja" id="prelazak_na_veci_nivo_datum_kraja" placeholder="Datum kraja">
																					<span class="materail-input-block__line"></span>
																				</div>
																			</div>
																		</div>
																	</div>
																	<script>
																		$("#prelazak_na_veci_nivo_datum_kraja").flatpickr({
																			disableMobile: "true",
																			minDate: "2000-01-01"
																		});
																	</script>
																</div>

																<!-- Unos termina polaganja -->
																<div class="form-group" id="taskforce_language_exam_block" style="display: none;">
																	<div class="">
																		<label for="taskforce_language_exam_block_date" class="col-sm-3 control-label">
																			<b>Termin polaganja:</b>
																		</label>
																		<div class="col-sm-9">
																			<div class="">
																				<input type="text" class="form-control" name="taskforce_language_exam_block_date" id="taskforce_language_exam_block_date" placeholder="Datum polaganja" required>
																				<span class="materail-input-block__line"></span>
																			</div>
																		</div>
																	</div>
																</div>
																<script>
																	$("#taskforce_language_exam_block_date").flatpickr({
																		disableMobile: "true",
																		minDate: "2000-01-01"
																	});
																</script>

																<!-- Unos certifikata -->
																<div class="form-group" id="taskforce_language_certificate_block" style="display: none;">
																	<div id="tf_certificate_alert_size" class="form-group hidden">
																		<div class="col-md-offset-2 col-sm-8">
																			<div class="alert material-alert material-alert_danger">Greška: Dokument koji pokuštavate dodati je veći od dozvoljene veličine.</div>
																		</div>
																	</div>
																	<div id="tf_certificate_alert_ext" class="form-group hidden">
																		<div class="col-md-offset-2 col-sm-8">
																			<div class="alert material-alert material-alert_danger">Greška: Format dokumenta kojeg pokušavate dodati nije dozvoljen.</div>
																		</div>
																	</div>
																	<div id="tf_certificate_alert_len" class="form-group hidden">
																		<div class="col-md-offset-2 col-sm-8">
																			<div class="alert material-alert material-alert_danger">Greška: Dozvoljeno je dodati maximalno 10 dokumenata.</div>
																		</div>
																	</div>
																	<div class="form-group tf_certificate_attachment_file_field">
																		<div class="col-md-offset-2 col-sm-8 text-center">
																			<div class="fileinputcertificate fileinputcertificate-new" data-provides="fileinputcertificate">
																				<span class="btn btn-default btn-file">
																					<span class="fileinputcertificate-new"> 
																						Certifikat
																					</span>
																					<!-- <span class="fileinputcertificate-exists">
																						Promijeni
																					</span> -->
																					<input type="file" name="tf_certificate_attachment_file" id="tf_certificate_attachment_file">
																				</span>
																				<i 
																					style="padding-top: 5px; padding-left: 10px; padding-right: 10px;" 
																					class="fa fa-question-circle fa-lg" 
																					data-toggle="tooltip" 
																					data-placement="right" 
																					title="Napomena: Moguće dodati najviše 10 dokumenata! Dokument ne smije biti veći od 5MB! - Dozvoljeni formati: jpg, jpeg, pdf, doc, docx, xls, xlsx, txt, ppt, pptx, png, csv" 
																					aria-hidden="true"
																				>
																				</i>
																				<br>
																				<span style="padding-top: 5px; padding-left: 10px; padding-right: 10px; word-break: break-all;" id = "tf_certificates-selected">
																				</span>
																				<br>
																				<span style="padding-top: 5px; padding-left: 10px; padding-right: 10px; word-break: break-all;" id = "tf_certificate-name">
																				</span>
																			</div>
																		</div>
																	</div>
																	<div class="form-group">
																		<label for="taskforce_language_certificate_creation" class="col-sm-3 control-label">
																			<b>Datum kreiranja certifikata:</b>
																		</label>
																		<div class="col-sm-9">
																			<div class="">
																				<input type="text" class="form-control" name="taskforce_language_certificate_creation" id="taskforce_language_certificate_creation" placeholder="Datum kreiranja certifikata">
																				<span class="materail-input-block__line"></span>
																			</div>
																		</div>
																	</div>
																	<script>
																	$("#taskforce_language_certificate_creation").flatpickr({
																			disableMobile: "true",
																			minDate: "2000-01-01"
																		});
																	</script>
																	<div class="form-group">
																		<label for="taskforce_language_certificate_expiration" class="col-sm-3 control-label">
																			<b>Datum važenja certifikata:</b>
																		</label>
																		<div class="col-sm-9">
																			<div class="">
																				<input type="text" class="form-control" name="taskforce_language_certificate_expiration" id="taskforce_language_certificate_expiration" placeholder="Datum važenja certifikata">
																				<span class="materail-input-block__line"></span>
																			</div>
																		</div>
																	</div>
																	<script>
																	$("#taskforce_language_certificate_expiration").flatpickr({
																			disableMobile: "true",
																			minDate: "2000-01-01"
																		});
																	</script>
																</div>
																<script>
																	$(function (){
																		$('#tf_certificate_attachment_file').change(function (){
																			//Script za provjeru više dokumenata
																			if($('#tf_certificate_attachment_file').val() !== ""){
																				var files = [];
																				var filesName = [];
																				files.push(this.files);
																				if(parseInt(this.files.length) < 11){
																					$('#tf_certificate-selected').text(this.files.length + " file selected.");
																					iterateFiles(files);
																					function iterateFiles(filesArray)
																					{
																						var zastavicaPrilog = 0;
																						for(var i=0; i<filesArray.length; i++){
																							//console.log("I"+ i);
																							for(var j=0; j<filesArray[i].length; j++){
																								//console.log("J"+ j);
																								//console.log(filesArray[i][j].name + " " + filesArray[i][j].size);
																								filesName.push(filesArray[i][j].name);
																								// alternatively: console.log(filesArray[i].item(j).name);
																								var ext = filesArray[i][j].name.split('.').pop().toLowerCase();
																								if($.inArray(ext, ['jpg', 'jpeg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png']) == -1) {
																									$('#tf_certificate_alert_ext').removeClass('hidden');
																									setTimeout(function(){
																										$('#tf_file_alert_ext').addClass('hidden');
																									}, 5000);
																									$('#tf_certificate_attachment_file').val(null);
																									zastavicaPrilog = 1;
																									$('#tf_certificate-selected').text("");
																									break;
																								}else{
																									$('#tf_certificate_alert_ext').addClass('hidden');
																								}
																								
																								var f = filesArray[i][j];
																								if (f.size > 5242880){
																									$('#tf_certificate_alert_size').removeClass('hidden');
																									setTimeout(function(){
																										$('#tf_certificate_alert_size').addClass('hidden');
																									}, 5000);
																									$('#tf_certificate_attachment_file').val(null);
																									zastavicaPrilog = 1;
																									$('#tf_certificate-selected').text("");
																									break;
																								}else{
																									$('#tf_certificate_alert_size').addClass('hidden');
																								}
																							}
																							if(zastavicaPrilog === 1){
																								break;
																							}
																						}
																						if(zastavicaPrilog === 0){
																							$('#tf_certificate-name').text("Files: "+ filesName.join(" , ") + "");
																						}else{
																							$('#tf_certificate-name').text("");
																						}
																					}
																				}else{
																					$('#tf_certificate_attachment_file').val(null);
																					$('#tf_certificate-name').text("");
																					$('#tf_certificate-selected').text("");
																					$('#tf_certificate_alert_len').removeClass('hidden');
																					setTimeout(function(){
																						$('#tf_certificate_alert_len').addClass('hidden');
																					}, 5000);
																				}
																			}
																		})
																	});
																</script>
																<!-- LANGUAGE BLOCKS END -->

																<div class="text-right">
																	<!-- <span onclick="closeTaskForceForm()" class="btn material-btn material-btn">Odustani</span> -->
																	<button type="submit" class="btn btn-primary material-btn material-btn_success" form="taskforce_form"><i class="fa fa-save" aria-hidden="true"></i> Završi</button>
																</div>
															</form>
													<?php } ?>
												</div>
											</div>
											<hr/>
											<!--- MODAL POTVRDI ZVANJE -->
											<div class="modal material-modal material-modal_danger fade" id="potvrda_poziva_modal">
												<div class="modal-dialog">
													<div class="modal-content material-modal__content">
														<div class="modal-header material-modal__header">
															<button class="close material-modal__close" data-dismiss="modal">&times;</button>
															<h4 class="modal-title material-modal__title">Potvrda</h4>
														</div>
														<div class="modal-body material-modal__body">
															<h5>Prije poziva, provjerite da li ste ulogovani u VICIDIAL</h5>
														</div>
														<div class="modal-footer material-modal__footer">
															<button class="btn material-btn material-btn" data-dismiss="modal">NE</button>
															<button data-zaposlenik_id = "<?php echo $logged_employee_id; ?>" data-kandidat_mob = "<?php echo $kandidat_mobitel; ?>" id="potvrda_poziva_button" class="btn btn-primary material-btn material-btn_danger">DA</button>
														</div>
													</div>
												</div>
											</div>
											<!--- MODAL POTVRDI ZVANJE END -->

											<!--- MODAL POTVRDI PREKID POZIVA -->
											<div class="modal material-modal material-modal_danger fade" id="potvrda_prekida_modal">
												<div class="modal-dialog">
													<div class="modal-content material-modal__content">
														<div class="modal-header material-modal__header">
															<button class="close material-modal__close" data-dismiss="modal">&times;</button>
															<h4 class="modal-title material-modal__title">Potvrda</h4>
														</div>
														<div class="modal-body material-modal__body">
															<h5>POTVRDITE PREKID POZIVA</h5>
														</div>
														<div class="modal-footer material-modal__footer">
															<button class="btn material-btn material-btn" data-dismiss="modal">NE PREKIDAJ</button>
															<button data-zaposlenik_id = "<?php echo $logged_employee_id; ?>" data-kandidat_mob = "<?php echo $kandidat_mobitel; ?>" id="potvrda_prekida_poziva_button" class="btn btn-primary material-btn material-btn_danger">PREKID</button>
														</div>
													</div>
												</div>
											</div>
											<!--- MODAL POTVRDI PREKID POZIVA END -->
											<script>

												// $("#unreserveAgent").on('click', () => {

												// 	$.ajax({
												// 		type: "POST",
												// 		url: "/ajax_data.php?page=unreserve_agent",
												
												// 		statusCode: {
												// 			200: (data) => {
												// 				$("#tf_button_next").removeAttr('disabled');
												// 			}
												// 		}
												// 	});

												// });

												$('#tf_button_next').on('click', () => {
													$.ajax({
														type: "POST",
														url: "/task_force_queue.php",
												
														statusCode: {
														
															200: (data) => {
																if(data.category_id == 3){
																	window.location.replace('<?php getSiteURL(); ?>nostrifikacija_diploma?page=otvori_ND_kandidata&id=' + data.dipl_id + '&vrsta_id=' + data.vrsta_id);
																}else{
																	window.location.replace('<?php getSiteURL(); ?>kandidati?page=open&id=' + data.kandidat_id + '&projekt_id=' +  data.projekt_id + '&nalog_id=' + data.nalog_id + '&vrsta_id=' + data.vrsta_id);
																}															}
														},
														complete: (xhr) => {
															
															if (xhr.status == 200) {
																if(data.category_id == 3){
																	window.location.replace('<?php getSiteURL(); ?>nostrifikacija_diploma?page=otvori_ND_kandidata&id=' + data.dipl_id + '&vrsta_id=' + data.vrsta_id);
																}else{
																	window.location.replace('<?php getSiteURL(); ?>kandidati?page=open&id=' + data.kandidat_id + '&projekt_id=' +  data.projekt_id + '&nalog_id=' + data.nalog_id + '&vrsta_id=' + data.vrsta_id);
																}															} else {
																document.querySelector("#tf_alert_nema_kandidata").innerHTML = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>Nema kandidata.</strong></div>';
															}

														}
													});

												});

												$("#taskforce_form_status").change(function() {
													let taskforce_form_termin_poziva_div = document.getElementById('taskforce_form_termin_poziva_div');
													let tf_razlog_odustajanja_form = document.getElementById('tf_razlog_odustajanja_form');
													let taskforce_form_termin_poziva = document.getElementById('taskforce_form_termin_poziva');
													let statusAppointment = $(this).find(':selected').data('hasappointment');
													let statusInterview = $(this).find(':selected').data('hasinterview');
													let status_id = document.getElementById("taskforce_form_status").value;
													// Language block
													let language_new_level_block = document.getElementById("taskforce_language_level_block");
													let taskforce_language_exam_block = document.getElementById("taskforce_language_exam_block");
													let taskforce_language_certificate_block = document.getElementById("taskforce_language_certificate_block");
													let prelazak_na_veci_nivo = document.getElementById('prelazak_na_veci_nivo');
													let prelazak_na_veci_nivo_podnivo = document.getElementById('prelazak_na_veci_nivo_podnivo');
													let language_ustanova = document.getElementById('language_ustanova');
													// console.log(status_id);

													
													taskforce_form_termin_poziva.value = "";
													taskforce_form_termin_poziva.disabled = false;
													
													function formatDateTime(date) {
														let year = date.getFullYear();
														let month = (date.getMonth() + 1).toString().padStart(2, '0');
														let day = date.getDate().toString().padStart(2, '0');
														let hours = date.getHours().toString().padStart(2, '0');
														let minutes = date.getMinutes().toString().padStart(2, '0');
														return `${year}-${month}-${day} ${hours}:${minutes}`;
													}

													function addMinutesToTime(minutesToAdd) {
														let currentDate = new Date();
														// let currentDate = new Date("2024-04-15T16:30:00"); // za simulacija testiranja pred kraj radnog vremena
														let hoursToAdd = Math.floor((currentDate.getMinutes() + minutesToAdd) / 60);
														let remainingMinutes = (currentDate.getMinutes() + minutesToAdd) % 60;

														currentDate.setHours(currentDate.getHours() + hoursToAdd);
														currentDate.setMinutes(remainingMinutes);
														if (currentDate.getHours() >= 17) {
															let minutesAfter17 = currentDate.getMinutes() + (currentDate.getHours() - 17) * 60;
															let nextDay = new Date(currentDate);
															nextDay.setDate(currentDate.getDate() + 1);
															nextDay.setHours(9);
															nextDay.setMinutes(minutesAfter17);
															return formatDateTime(nextDay);
														}

														return formatDateTime(currentDate);
													}

													// console.log(addMinutesToTime(150));

													if(statusAppointment){
														taskforce_form_termin_poziva_div.style.display = "block";
														taskforce_form_termin_poziva.required = true;  
													}else{
														taskforce_form_termin_poziva_div.style.display = "none";
														taskforce_form_termin_poziva.required = false;
													}

													if(status_id == 2 || status_id == 8){
														taskforce_form_termin_poziva.value = addMinutesToTime(120);
														taskforce_form_termin_poziva.disabled = true;
													}

													if(status_id == 2){
														$('.regular_note').hide();
														$('.note_for_neuspjesna').show();
														$('#neuspjesna_biljeska').attr('required', 'true');
													}else{
														$('.regular_note').show();
														$('.note_for_neuspjesna').hide();
														$('#neuspjesna_biljeska').removeAttr('required');
													}

													if(statusInterview){
														taskforce_form_termin_intervju_div.style.display = "block";
													}else{
														taskforce_form_termin_intervju_div.style.display = "none";
													}
													
													if(status_id == 30 || status_id == 34 || status_id == 38 || status_id == 42){
														tf_razlog_odustajanja_form.style.display = "block";
													}else{
														tf_razlog_odustajanja_form.style.display = "none";
													}

													if(status_id == 108 || status_id == 113 || status_id == 118){
														language_new_level_block.style.display = "block";
														prelazak_na_veci_nivo.required = true;
														prelazak_na_veci_nivo_podnivo.required = true;
														language_ustanova.required = true;
													}else{
														language_new_level_block.style.display = "none";
														prelazak_na_veci_nivo.required = false;  
														prelazak_na_veci_nivo_podnivo.required = false;  
														language_ustanova.required = false;  
													}

													if(status_id == 124 || status_id == 130 || status_id == 135){
														taskforce_language_exam_block.style.display = "block";
													}else{
														taskforce_language_exam_block.style.display = "none";
													}

													if(status_id == 132){
														taskforce_language_certificate_block.style.display = "block";
														$('#tf_certificate_attachment_file').attr('required', 'true');
													}else{
														taskforce_language_certificate_block.style.display = "none";
														$('#tf_certificate_attachment_file').removeAttr('required');
													}
																										
												});

												let loginForm = document.getElementById("taskforce_form");
												loginForm.addEventListener('submit', function(e){
													e.preventDefault();

													let taskforce_form_status = document.getElementById("taskforce_form_status").value;
													let taskforce_form_biljeska = '';
													if(taskforce_form_status == 2){
														taskforce_form_biljeska = document.getElementById("neuspjesna_biljeska").value;
													}else{
														taskforce_form_biljeska = document.getElementById("taskforce_form_biljeska").value;
													}
													let taskforce_form_vazna_biljeska = document.getElementById("taskforce_form_vazna_biljeska").checked;
													let taskforce_form_termin_poziva = document.getElementById("taskforce_form_termin_poziva").value;
													let taskforce_form_termin_intervju = document.getElementById("taskforce_form_termin_intervju").value;
													let termin_group = document.getElementById("termin_group").value;
													let taskforce_form_status_select = document.getElementById("taskforce_form_status");
													let tf_note_attachment = document.getElementById("tf_note_attachment").value;
													let tf_razlog_odustajanja = document.getElementById("tf_razlog_odustajanja").value;
													let taskforce_form_status_text = taskforce_form_status_select.options[taskforce_form_status_select.selectedIndex].text;

													var formData = new FormData();
													formData.append('tf_candidate_id', <?php echo $kandidat_id;?>);
													formData.append('tf_nalog_id', <?php echo $tf_nalog_id;?>);
													formData.append('tf_projekt_id', <?php echo $tf_projekt_id;?>);
													formData.append('tf_vrsta_id', <?php echo $tf_vrsta_id;?>);
													formData.append('tf_status_id', taskforce_form_status);
													formData.append('tf_note', taskforce_form_biljeska);
													formData.append('tf_important_note', taskforce_form_vazna_biljeska);
													formData.append('tf_call_appointment', taskforce_form_termin_poziva);
													formData.append('intervju', taskforce_form_termin_intervju);
													formData.append('termin_group', termin_group);
													formData.append('tf_note_attachment', tf_note_attachment);
													formData.append('tf_razlog_odustajanja', tf_razlog_odustajanja);
													var tf_nr_of_files = 0;
													jQuery.each(jQuery('#tf_note_attachment_file')[0].files, function(i, file) {
														formData.append('file-'+i, file);
														tf_nr_of_files++;
													});
													formData.append('tf_nr_of_files', tf_nr_of_files);
													
													// let ide_u_glosu = <?php //echo intVal(getGlosaInfoForNalog($tf_nalog_id));?>;
													let ide_u_glosu = false; //ovo ne diraj ko god da si
													let vec_u_glosi = <?php echo checkKandidatGlossa($kandidat_id);?>;
													
													let statusAppointment = $(this).find(':selected').data('hasappointment');
													let statusInterview = $(this).find(':selected').data('hasinterview');
													let task_force_status_prijave = $(this).find(':selected').data('tfs_status_prijave');
													if(statusAppointment){
														if(!taskforce_form_termin_poziva){
															$("#taskforce_form_message").html('<p style="padding: 10px; background: #f3413c; color: white; font-weight: bold;">Morate unijeti termin poziva.</p>');
															return;
														}
													}

													if(statusInterview){
														if(!taskforce_form_termin_intervju){
															$("#taskforce_form_message").html('<p style="padding: 10px; background: #f3413c; color: white; font-weight: bold;">Morate unijeti termin intervjua.</p>');
															return;
														}
														if(!termin_group){
															$("#taskforce_form_message").html('<p style="padding: 10px; background: #f3413c; color: white; font-weight: bold;">Morate unijeti casting. Provjerite da li je definiran.</p>');
															return;
														}
													}

													// Jezik
													if(taskforce_form_status == 108 || taskforce_form_status == 113 || taskforce_form_status == 118){
														let prelazak_na_veci_nivo_datum_pocetka = document.getElementById('prelazak_na_veci_nivo_datum_pocetka').value;
														let prelazak_na_veci_nivo_datum_kraja = document.getElementById('prelazak_na_veci_nivo_datum_kraja').value;
														
														if(!prelazak_na_veci_nivo_datum_pocetka){
															$("#taskforce_form_message").html('<p style="padding: 10px; background: #f3413c; color: white; font-weight: bold;">Morate unijeti datum početka.</p>');
															return;
														}
														if(!prelazak_na_veci_nivo_datum_kraja){
															$("#taskforce_form_message").html('<p style="padding: 10px; background: #f3413c; color: white; font-weight: bold;">Morate unijeti datum kraja.</p>');
															return;
														}else{
															if(prelazak_na_veci_nivo_datum_pocetka >= prelazak_na_veci_nivo_datum_kraja){
																$("#taskforce_form_message").html('<p style="padding: 10px; background: #f3413c; color: white; font-weight: bold;">Datum kraja mora biti veći od početka.</p>');
																return;
															}
														}
														// Jezik
														let prelazak_na_veci_nivo = document.getElementById('prelazak_na_veci_nivo').value;
														let prelazak_na_veci_nivo_podnivo = document.getElementById('prelazak_na_veci_nivo_podnivo').value;
														let language_ustanova = document.getElementById('language_ustanova').value;
														formData.append('language_ustanova', language_ustanova);
														formData.append('prelazak_na_veci_nivo', prelazak_na_veci_nivo);
														formData.append('prelazak_na_veci_nivo_podnivo', prelazak_na_veci_nivo_podnivo);
														formData.append('prelazak_na_veci_nivo_datum_kraja', prelazak_na_veci_nivo_datum_kraja);
														formData.append('prelazak_na_veci_nivo_datum_pocetka', prelazak_na_veci_nivo_datum_pocetka);
													}

													if(taskforce_form_status == 124 || taskforce_form_status == 130 || taskforce_form_status == 135){
														let taskforce_language_exam_block_date = document.getElementById("taskforce_language_exam_block_date").value;
														if(!taskforce_language_exam_block_date){
															$("#taskforce_form_message").html('<p style="padding: 10px; background: #f3413c; color: white; font-weight: bold;">Morate unijeti termin polaganja.</p>');
															return;
														}
														formData.append('taskforce_language_exam_date', taskforce_language_exam_block_date);
													}

													if(taskforce_form_status == 132){
														let taskforce_language_certificate_creation = document.getElementById("taskforce_language_certificate_creation").value;
														let taskforce_language_certificate_expiration = document.getElementById("taskforce_language_certificate_expiration").value;
														let tf_certificate_attachment_file = document.getElementById("tf_certificate_attachment_file").value;
														if(!taskforce_language_certificate_creation && !taskforce_language_certificate_expiration){
															$("#taskforce_form_message").html('<p style="padding: 10px; background: #f3413c; color: white; font-weight: bold;">Morate unijeti datume kreiranja i važenja certifikata.</p>');
															return;
														}else{
															if(taskforce_language_certificate_creation > taskforce_language_certificate_expiration){
																$("#taskforce_form_message").html('<p style="padding: 10px; background: #f3413c; color: white; font-weight: bold;">Datum kreiranja mora biti veći od datuma važenja certifikata.</p>');
															}
														}
														formData.append('taskforce_language_certificate_creation', taskforce_language_certificate_creation);
														formData.append('taskforce_language_certificate_expiration', taskforce_language_certificate_expiration);
														formData.append('tf_certificate_attachment_file', tf_certificate_attachment_file);
														var tf_nr_of_certificates = 0;
														jQuery.each(jQuery('#tf_certificate_attachment_file')[0].files, function(i, file) {
															formData.append('file_certificate-'+i, file);
															tf_nr_of_certificates++;
														});
														formData.append('tf_nr_of_certificates', tf_nr_of_certificates);
													}

													$.ajax({
														url: 'ajax_data.php?page=insert_task',
														type: 'POST',
														processData: false,
														contentType: false,    
														data: formData,
														dataType: 'html',
														success: function(data) {
															// console.log(data);
															loginForm.reset();
															if(ide_u_glosu && task_force_status_prijave == 3 && vec_u_glosi == 0){
																fetch(`ajax_data.php?page=glossa_api_info&id=${kandidat_id}`)
																.then((res) => res.json())
																.then((data_glosa_info) => {
																		fname = data_glosa_info.fname;
																		lname = data_glosa_info.lname;
																		email = data_glosa_info.email;
																		phone = data_glosa_info.phone;
																		crm_id = data_glosa_info.crm_id;
																		
																		if(phone === null)
																		{
																			phone = "1111111111";
																		}

																		var myHeaders = new Headers();
																		myHeaders.append("Authorization", "Bearer 8d81150a-8cd6-4a8a-9279-d4a4a45ccdea");
																		myHeaders.append("Content-Type", "application/json");
																		myHeaders.append("Cookie", "PH_HPXY_CHECK=s1");

																		var raw = JSON.stringify({
																			"name": fname,
																			"lastname": lname,
																			"email": email,
																			"phone": phone,
																			"crm_id": crm_id
																		});

																		var requestOptions = {
																		method: 'POST',
																		headers: myHeaders,
																		body: raw,
																		redirect: 'follow',
																		};

																		fetch("https://glossa-crm.com/api/Person/Create", requestOptions)
																		.then(response => response.json())
																		.then((result) => {
																			let response_msg = result.message;
																			let response_status = result.code;
																			fetch("do.php?form=insert_glossa_lead", {
																				method: "POST",
																				headers: {
																					"Content-type": "application/x-www-form-urlencoded"
																				},
																				body: new URLSearchParams({
																					"kandidat_id": <?php echo $kandidat_id;?>,
																					"response_msg": response_msg,
																					"response_code": response_status,
																					"glossa_kurs": 1
																				})
																			});
																			$("#taskforce_form_status").selectpicker("refresh");
																			$("#taskforce_form_message").html('<p style="padding: 10px; background: #68c368; color: white; font-weight: bold;">Uspješno ste dodali status.</p>');
																			$("#zadnji_task_force_status").html(taskforce_form_status_text);
																			$("#tf_biljeska").html(taskforce_form_biljeska);
																			window.location.reload();
																		})
																		.catch(error => console.log('error', error));
																}); // then
															}else{
																$("#taskforce_form_status").selectpicker("refresh");
																$("#taskforce_form_message").html('<p style="padding: 10px; background: #68c368; color: white; font-weight: bold;">Uspješno ste dodali status.</p>');
																$("#zadnji_task_force_status").html(taskforce_form_status_text);
																$("#tf_biljeska").html(taskforce_form_biljeska);
																window.location.reload();
															}
															
														} // success
													}); // ajax
												}); // funkcija forme

												$("#potvrda_poziva_button").on( "click", function() {

													var zaposlenik_id = $(this).data("zaposlenik_id");
													var kandidat_tel = $(this).data("kandidat_mob");
													var dugme_poziva = $('#anchor_poziv');
													var dugme_prekida = $('#anchor_prekid');

													$.ajax({
														url: 'ajax_data.php?page=check_candidate_reservation',
														type: 'POST',
														data: {'kandidat_id':<?php echo $kandidat_id;?>},
														dataType: 'json',
														success: function(reserved_response) {
															if(reserved_response == 1){
																alert("Kandidat je već u pozivu.");
															}else{
																$.ajax({
																	url: 'vicidial.php?akcija=call_candidate',
																	type: 'POST',
																	data: {'zaposlenik_id':zaposlenik_id, 'kandidat_tel':kandidat_tel},
																	dataType: 'html',
																	success: function(data) {
																		alert(data);
																		dugme_poziva.css({"display": "none"});
																		dugme_prekida.css({"display": "inline-block"});
																		$('#potvrda_poziva_modal').modal('hide');
																	}
																});
															}
														}
													});
												});

												$("#potvrda_prekida_poziva_button").on( "click", function() {
													
													var zaposlenik_id = $(this).data("zaposlenik_id");
													var kandidat_tel = $(this).data("kandidat_mob");
													var dugme_poziva = $('#anchor_poziv');
													var dugme_prekida = $('#anchor_prekid');
													let taskforce_form = document.getElementById('taskforce_form');
													
													$("#taskforce_form_message").html("");	

													$.ajax({
														url: 'vicidial.php?akcija=abort_call',
														type: 'POST',
														data: {'zaposlenik_id':zaposlenik_id, 'kandidat_tel':kandidat_tel},
														dataType: 'html',
														success: function(data) {
															alert(data);
															dugme_prekida.css({"display": "none"});
															dugme_poziva.css({"display": "inline-block"});
															$('#potvrda_prekida_modal').modal('hide');
															taskforce_form.reset();
														}
													});
												});

												$("#termin_group").on("change", function(){
													let zadnji_tf_status = <?php echo intVal(getLastTFStatus($kandidat_id, $tf_nalog_id, $tf_vrsta_id)); ?>;
													let taskforce_form_termin_poziva = document.getElementById("taskforce_form_termin_poziva");
													let termin_group = document.getElementById("termin_group").value;
													let ending_date = new Date($('option:selected', this).attr("ending_date"));
													let dt = new Date();

													let taskforce_form_termin_intervju = document.getElementById("taskforce_form_termin_intervju");

													if(zadnji_tf_status == 16 || dt > ending_date ){
														$.ajax({
															url: 'ajax_data.php?page=getDateSecondMessage',
															type: 'POST',    
															data: {
																'tf_nalog_id' : <?php echo $tf_nalog_id;?>,
																'tf_casting_id' : termin_group,
																'tf_call_appointment' : taskforce_form_termin_poziva.value,
															},
															dataType: 'html',
															success: function(response) {
																taskforce_form_termin_poziva.value = response;
																// taskforce_form_termin_poziva.setAttribute('disabled', '');
															}
														});
													}else{
														$.ajax({
															url: 'ajax_data.php?page=getDateFirstMessage',
															type: 'POST',    
															data: {
																'tf_nalog_id' : <?php echo $tf_nalog_id;?>,
																'tf_casting_id' : termin_group,
																'tf_call_appointment' : taskforce_form_termin_poziva.value,
															},
															dataType: 'html',
															success: function(response) {
																taskforce_form_termin_poziva.value = response;
																// taskforce_form_termin_poziva.setAttribute('disabled', '');
															}
														});
													}

													$.ajax({
														url: 'ajax_data.php?page=check_appointment_time_for_appointment',
														type: 'POST',    
														data: {
															'pap_id' : termin_group
														},
														dataType: 'json',
														success: function(data) {
															var select = $('#taskforce_form_termin_intervju');

															select.empty();

															data.forEach(function (item) {
															var option = $('<option>', {
																value: item.pah_id,
																text: item.pah_time
															});
															select.append(option);
															});

															select.selectpicker('refresh');
														}
													});
												});
											</script><?php 
										}
									?>
									<!-- EDIT MODAL -->
									<div class="modal material-modal material-modal_success fade text-left" id="editAjax">
										<div class="modal-dialog modal-lg">
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
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-xs-12">
											<h4 class="idk_info_title"><i class="fa fa-angle-down" aria-hidden="true"></i> Osnovne informacije
											<?php if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "18" , $getEmployeeStatus )) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){	?>
											<span class="pull-right btn material-btn material-btn_success main-container__column edit_candidate_data" data-toggle="modal" data-target="#editAjax" data-id="<?php echo $kandidat_id; ?>" data-kandidat="<?php echo $kandidat_id; ?>" data-type="edit_kandidat" aria-hidden="true"><i class="fa fa-refresh" aria-hidden="true"></i> UREDI</span>
											<?php } ?>
											</h4>
										</div>
									</div>
									<?php 
										if($kandidat_status == 4){
											if($val_osnovne != 9){
									?>
									<div class="row">
										<div class="text-center">
											<b>Validacija svih osnovnih informacija: </b><br/>
											<a href="" data-toggle="modal" data-target="#modalValYESsveodjednom" data-kandidat_id="<?php echo $kandidat_id; ?>" data-employee_id="<?php echo $logged_employee_id; ?>" class="validate_all_sveodjednom label material-label material-label_success main-container__column" title = "Validacija"><i class="fa fa-plus-circle" aria-hidden="true"></i></a>
										</div>
									</div>
										<?php }}
									?>
									<div class="row idk_employee_info" style="margin-top: 5px;">
										<div class="col-md-6">
										<?php if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "18" , $getEmployeeStatus )) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){	?>
											<div class="row">
												<strong class="col-sm-4 text-right">Datum prijave:</strong>
												<div class="col-sm-8"><span class="label label-success material-label material-label_success main-container__column"><?php echo $kandidat_datetime; ?></span></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Osobni link:</strong>
												<div class="col-sm-8"><a href="<?php getSiteUrl(); ?>registracija/korak2/<?php echo $kandidat_id; ?>/<?php echo $kandidat_check ?>" target="_BLANK"><span class="label label-success material-label material-label_success main-container__column">KORAK 2</span></a></div>
											</div>
										<?php } ?>
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
											<?php if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "18" , $getEmployeeStatus )) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){	?>
											<div class="row">
												<strong class="col-sm-4 text-right">URL:</strong>
												<div class="col-sm-8"><a href="<?php getSiteUrl(); ?>/link_generator.php?page=show_list&id=<?php echo $kandidat_visitedurl; ?>" target="_BLANK"><span class="label label-info material-label material-label_info main-container__column"><?php echo $lg_url; ?></span></a></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">URL Opis:</strong>
												<div class="col-sm-8"><span class="label label-info material-label material-label_info main-container__column"><?php echo $lg_desc; ?></span></div>
											</div>
											<?php } ?>
											<div class="row">
												<strong class="col-sm-4 text-right">Ime:</strong>
												<div class="col-sm-4"><?php echo $kandidat_ime; ?></div>
												<?php 
													if($kandidat_status == 4 or $kandidat_status == 5){
														if($val_ime == null or $val_ime == 3){
												?>
												<div class="text-center col-sm-4">
													<a href="" data-toggle="modal" data-target="#modalValYES" data-kandidat_id="<?php echo $kandidat_id; ?>" data-employee_id="<?php echo $logged_employee_id; ?>" data-vrsta_podatka="blok_ime" class="validate_all label material-label material-label_success main-container__column" title = "Validacija imena"><i class="fa fa-plus-circle" aria-hidden="true"></i></a>
													<a href="" data-toggle="modal" data-target="#modalValNO" data-kandidat_id="<?php echo $kandidat_id; ?>" data-employee_id="<?php echo $logged_employee_id; ?>" data-vrsta_podatka="blok_ime" class="validate_all_X label material-label material-label_danger main-container__column" title = "Validacija imena"><i class="fa fa-minus-circle" aria-hidden="true"></i></a>
												</div>
												<?php 
														}
													}
												?>
											</div>
											<?php if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "18" , $getEmployeeStatus )) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){	?>
											<div class="row">
												<strong class="col-sm-4 text-right">Prezime:</strong>
												<div class="col-sm-4"><?php echo $kandidat_prezime; ?></div>
												<?php 
													if($kandidat_status == 4 or $kandidat_status == 5){
														if($val_pre == null or $val_pre == 3){
												?>
												<div class="text-center col-sm-4">
													<a href="" data-toggle="modal" data-target="#modalValYES" data-kandidat_id="<?php echo $kandidat_id; ?>" data-employee_id="<?php echo $logged_employee_id; ?>" data-vrsta_podatka="blok_pre" class="validate_all label material-label material-label_success main-container__column" title = "Validacija prezimena"><i class="fa fa-plus-circle" aria-hidden="true"></i></a>
													<a href="" data-toggle="modal" data-target="#modalValNO" data-kandidat_id="<?php echo $kandidat_id; ?>" data-employee_id="<?php echo $logged_employee_id; ?>" data-vrsta_podatka="blok_pre" class="validate_all_X label material-label material-label_danger main-container__column" title = "Validacija prezimena"><i class="fa fa-minus-circle" aria-hidden="true"></i></a>
												</div>
												<?php 
														}
													}
												?>
											</div>

											<div class="row">
												<strong class="col-sm-4 text-right">Email:</strong>
												<div class="col-sm-8"><a href="mailto:<?php echo $kandidat_email; ?>"><?php echo $kandidat_email; ?></a></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Bot username:</strong>
												<div class="col-sm-8"><?php echo $kandidat_bot_username; ?></div>
											</div>
											<?php } ?>
											<div class="row">
												<strong class="col-sm-4 text-right">Datum rođenja:</strong>
												<div class="col-sm-4"><?php echo $kandidat_datumrodjenja; ?></div>
												<?php 
													if($kandidat_status == 4 or $kandidat_status == 5){
														if($val_dtR == null or $val_dtR == 3){
												?>
												<div class="text-center col-sm-4">
													<a href="" data-toggle="modal" data-target="#modalValYES" data-kandidat_id="<?php echo $kandidat_id; ?>" data-employee_id="<?php echo $logged_employee_id; ?>" data-vrsta_podatka="blok_dtR" class="validate_all label material-label material-label_success main-container__column" title = "Validacija"><i class="fa fa-plus-circle" aria-hidden="true"></i></a>
													<a href="" data-toggle="modal" data-target="#modalValNO" data-kandidat_id="<?php echo $kandidat_id; ?>" data-employee_id="<?php echo $logged_employee_id; ?>" data-vrsta_podatka="blok_dtR" class="validate_all_X label material-label material-label_danger main-container__column" title = "Validacija"><i class="fa fa-minus-circle" aria-hidden="true"></i></a>
												</div>
												<?php 
														}
													}
												?>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Broj pasoša:</strong>
												<div class="col-sm-8"><?php  echo $kandidat_broj_pasosa; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Spol:</strong>
												<div class="col-sm-8"><?php  /*echo getBrojUnesenihZaValidaciju($kandidat_id)."-".getBrojNaValidaciji($kandidat_id)*/; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Mjesto rođenja:</strong>
												<div class="col-sm-4"><?php echo $kandidat_mjestorodjenja; ?></div>
												<?php 
													if($kandidat_status == 4 or $kandidat_status == 5){
														if($val_mjR == null or $val_mjR == 3){
												?>
												<div class="text-center col-sm-4">
													<a href="" data-toggle="modal" data-target="#modalValYES" data-kandidat_id="<?php echo $kandidat_id; ?>" data-employee_id="<?php echo $logged_employee_id; ?>" data-vrsta_podatka="blok_mjR" class="validate_all label material-label material-label_success main-container__column" title = "Validacija"><i class="fa fa-plus-circle" aria-hidden="true"></i></a>
													<a href="" data-toggle="modal" data-target="#modalValNO" data-kandidat_id="<?php echo $kandidat_id; ?>" data-employee_id="<?php echo $logged_employee_id; ?>" data-vrsta_podatka="blok_mjR" class="validate_all_X label material-label material-label_danger main-container__column" title = "Validacija"><i class="fa fa-minus-circle" aria-hidden="true"></i></a>
												</div>
												<?php 
														}
													}
												?>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Država rođenja:</strong>
												<div class="col-sm-4"><?php echo $kandidat_drzavarodjenja; ?></div>
												<?php 
													if($kandidat_status == 4 or $kandidat_status == 5){
														if($val_drR == null or $val_drR == 3){
												?>
												<div class="text-center col-sm-4">
													<a href="" data-toggle="modal" data-target="#modalValYES" data-kandidat_id="<?php echo $kandidat_id; ?>" data-employee_id="<?php echo $logged_employee_id; ?>" data-vrsta_podatka="blok_drR" class="validate_all label material-label material-label_success main-container__column" title = "Validacija"><i class="fa fa-plus-circle" aria-hidden="true"></i></a>
													<a href="" data-toggle="modal" data-target="#modalValNO" data-kandidat_id="<?php echo $kandidat_id; ?>" data-employee_id="<?php echo $logged_employee_id; ?>" data-vrsta_podatka="blok_drR" class="validate_all_X label material-label material-label_danger main-container__column" title = "Validacija"><i class="fa fa-minus-circle" aria-hidden="true"></i></a>
												</div>
												<?php 
														}
													}
												?>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Državljanstvo vrsta:</strong>
												<div class="col-sm-8"><?php echo $kandidat_drzavljanstvo_vrsta_ispis; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Državljanstvo:</strong>
												<div class="col-sm-8"><?php echo $kandidat_drzavljanstvo; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">EU boravak:</strong>
												<div class="col-sm-8"><?php echo $boravak_eu; ?></div>
											</div>

											<div class="row" style = "padding-bottom: 20px;">
												<strong class="col-sm-4 text-right">ATU paket:</strong>
												<div class="col-sm-8">
													<kandidat-atupaket 
														kandidatId="<?php echo $kandidat_id; ?>" 
														paket="<?php echo intval($kandidat_atu_paket); ?>"
														paketold="<?php echo intval($kandidat_atu_paket); ?>"
													>	
													</kandidat-atupaket>
												</div>
											</div>
											
											<kandidat-pokrajina kandidatId="<?php echo $kandidat_id; ?>" regija="<?php echo $kandidat_zeljena_regija; ?>" grad="<?php echo $kandidat_zeljeni_grad; ?>"></kandidat-pokrajina>

											<radno-iskustvo kandidatId="<?php echo $kandidat_id; ?>" radnoIskustvo="<?php echo $kandidat_iskustvo_u_struci; ?>" radnoIskustvoGodine="<?php echo $kandidat_iskustvo_u_struci_trajanje; ?>"></radno-iskustvo>
											
											<div class="row" style = "padding-bottom: 20px;">
												<strong class="col-sm-4 text-right">Bračno stanje:</strong>
												<div class="col-sm-8">
													<bracno-stanje 
														kandidatId="<?php echo $kandidat_id; ?>" 
														bracnoStanje="<?php echo intval($kandidat_bracno_stanje); ?>"
														bracnoStanjeOld="<?php echo intval($kandidat_bracno_stanje); ?>"
													>	
													</bracno-stanje>
												</div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Kandidat poslan na glossu:</strong>
												<div class="col-sm-8" id="glossa_info">
													<script>
														$(document).ready(function(){
															var kandidat_id = <?php echo $kandidat_id; ?>;
															$.ajax({
																url:'ajax_data.php?page=glossa_info',
																method:'POST',
																data:{ kandidat_id:kandidat_id },
																success:function(data){
																	console.log(data);
																	if(data == 1 || data == 2){
																		$('#glossa_info').html('<span class="label label-success material-label material-label_success main-container__column">DA</span>');
																	}else{
																		$('#glossa_info').html('<span class="label label-danger material-label material-label_danger main-container__column">NE</span>');
																	}
																}
															});
														});
													</script>
												</div>
											</div>
											<?php 

												/* Nacin odlaska  START */

													if ((in_array( "2" , $employee_status)) OR (in_array( "2" , $employee_supervizor)) OR (in_array( "1" , $employee_status))) {
														$departureType = getCandidateDepartureType($kandidat_id);
														$departureStatusPrijave = getCandidateStatusPrijave($kandidat_id);
														$parallelAppliesWestBalkan = getCandidateParallelAppliesWestBalkan($kandidat_id);
														if(in_array($departureStatusPrijave, array(1,2,3,5,6,7,8,9,12))){
															?>
																<nacin-odlaska
																	candidateId = "<?php echo $kandidat_id; ?>"
																	departureType = "<?php echo $departureType[0]; ?>"
																	departureTypeOld = "<?php echo $departureType[0]; ?>"
																	parallelAppliesWestBalkan = "<?php echo $parallelAppliesWestBalkan;?>"
																	parallelAppliesWestBalkanOld = "<?php echo $parallelAppliesWestBalkan;?>"
																	enableEditing = "<?php echo 1; ?>"
																	departureStatusPrijave = "<?php echo $departureStatusPrijave;?>"
																>
																</nacin-odlaska>
															<?php
														}else{
															?>
																<nacin-odlaska
																	candidateId = "<?php echo $kandidat_id; ?>"
																	departureType = "<?php echo $departureType[0]; ?>"
																	departureTypeOld = "<?php echo $departureType[0]; ?>"
																	parallelAppliesWestBalkan = "<?php echo $parallelAppliesWestBalkan;?>"
																	parallelAppliesWestBalkanOld = "<?php echo $parallelAppliesWestBalkan;?>"
																	enableEditing = "<?php echo 0; ?>"
																	departureStatusPrijave = "<?php echo $departureStatusPrijave;?>"
																>
																</nacin-odlaska>
															<?php 
														}
													}

												/* Nacin odlaska  END */
											?>
											
										</div>
										<div class="col-md-6">
										<?php if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "18" , $getEmployeeStatus )) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){	?>
											<div class="row">
												<strong class="col-sm-4 text-right">Adresa:</strong>
												<div class="col-sm-4"><?php echo $kandidat_adresa; ?></div>
												<?php 
													if($kandidat_status == 4 or $kandidat_status == 5){
														if($val_adr == null or $val_adr == 3){
												?>
												<div class="text-center col-sm-4">
													<a href="" data-toggle="modal" data-target="#modalValYES" data-kandidat_id="<?php echo $kandidat_id; ?>" data-employee_id="<?php echo $logged_employee_id; ?>" data-vrsta_podatka="blok_adr" class="validate_all label material-label material-label_success main-container__column" title = "Validacija"><i class="fa fa-plus-circle" aria-hidden="true"></i></a>
													<a href="" data-toggle="modal" data-target="#modalValNO" data-kandidat_id="<?php echo $kandidat_id; ?>" data-employee_id="<?php echo $logged_employee_id; ?>" data-vrsta_podatka="blok_adr" class="validate_all_X label material-label material-label_danger main-container__column" title = "Validacija"><i class="fa fa-minus-circle" aria-hidden="true"></i></a>
												</div>
												<?php 
														}
													}
												?>
											</div>
										<?php } ?>
											<div class="row">
												<strong class="col-sm-4 text-right">Grad:</strong>
												<div class="col-sm-4"><?php echo $kandidat_grad; ?></div>
												<?php 
													if($kandidat_status == 4 or $kandidat_status == 5){
														if($val_gra == null or $val_gra == 3){
												?>
												<div class="text-center col-sm-4">
													<a href="" data-toggle="modal" data-target="#modalValYES" data-kandidat_id="<?php echo $kandidat_id; ?>" data-employee_id="<?php echo $logged_employee_id; ?>" data-vrsta_podatka="blok_gra" class="validate_all label material-label material-label_success main-container__column" title = "Validacija"><i class="fa fa-plus-circle" aria-hidden="true"></i></a>
													<a href="" data-toggle="modal" data-target="#modalValNO" data-kandidat_id="<?php echo $kandidat_id; ?>" data-employee_id="<?php echo $logged_employee_id; ?>" data-vrsta_podatka="blok_gra" class="validate_all_X label material-label material-label_danger main-container__column" title = "Validacija"><i class="fa fa-minus-circle" aria-hidden="true"></i></a>
												</div>
												<?php 
														}
													}
												?>
											</div>
										<?php if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "18" , $getEmployeeStatus )) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){	?>
											<div class="row">
												<strong class="col-sm-4 text-right">Poštanski broj:</strong>
												<div class="col-sm-4"><?php echo $kandidat_pbroj; ?></div>
												<?php 
													if($kandidat_status == 4 or $kandidat_status == 5){
														if($val_pbr == null or $val_pbr == 3){
												?>
												<div class="text-center col-sm-4">
													<a href="" data-toggle="modal" data-target="#modalValYES" data-kandidat_id="<?php echo $kandidat_id; ?>" data-employee_id="<?php echo $logged_employee_id; ?>" data-vrsta_podatka="blok_pbr" class="validate_all label material-label material-label_success main-container__column" title = "Validacija"><i class="fa fa-plus-circle" aria-hidden="true"></i></a>
													<a href="" data-toggle="modal" data-target="#modalValNO" data-kandidat_id="<?php echo $kandidat_id; ?>" data-employee_id="<?php echo $logged_employee_id; ?>" data-vrsta_podatka="blok_pbr" class="validate_all_X label material-label material-label_danger main-container__column" title = "Validacija"><i class="fa fa-minus-circle" aria-hidden="true"></i></a>
												</div>
												<?php 
														}
													}
												?>
											</div>
										<?php } ?>
											<div class="row">
												<strong class="col-sm-4 text-right">Država:</strong>
												<div class="col-sm-4"><?php echo $kandidat_drzava; ?></div>
												<?php 
													if($kandidat_status == 4 or $kandidat_status == 5){
														if($val_drz == null or $val_drz == 3){
												?>
												<div class="text-center col-sm-4">
													<a href="" data-toggle="modal" data-target="#modalValYES" data-kandidat_id="<?php echo $kandidat_id; ?>" data-employee_id="<?php echo $logged_employee_id; ?>" data-vrsta_podatka="blok_drz" class="validate_all label material-label material-label_success main-container__column" title = "Validacija"><i class="fa fa-plus-circle" aria-hidden="true"></i></a>
													<a href="" data-toggle="modal" data-target="#modalValNO" data-kandidat_id="<?php echo $kandidat_id; ?>" data-employee_id="<?php echo $logged_employee_id; ?>" data-vrsta_podatka="blok_drz" class="validate_all_X label material-label material-label_danger main-container__column" title = "Validacija"><i class="fa fa-minus-circle" aria-hidden="true"></i></a>
												</div>
												<?php 
														}
													}
												?>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Vozačka dozvola:</strong>
												<div class="col-sm-8"><?php echo $kandidat_vozacka_dozvola; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Viza:</strong>
												<div class="col-sm-4"><?php echo $kandidat_viza; ?></div>
												<?php 
													if($kandidat_status == 4 or $kandidat_status == 5){
														if($unesena_viza == 1){
															if($val_viza == null or $val_viza == 3){
												?>
												<div class="text-center col-sm-4 display_viza_val">
													<a href="" data-toggle="modal" data-target="#modalValVizaYES" data-kandidat_id="<?php echo $kandidat_id; ?>" data-employee_id="<?php echo $logged_employee_id; ?>" class="validate_viza label material-label material-label_success main-container__column" title = "Validacija vize"><i class="fa fa-plus-circle" aria-hidden="true"></i></a>
													<a href="" data-toggle="modal" data-target="#modalValVizaNO" data-kandidat_id="<?php echo $kandidat_id; ?>" data-employee_id="<?php echo $logged_employee_id; ?>" class="validate_viza label material-label material-label_danger main-container__column" title = "Validacija vize"><i class="fa fa-minus-circle" aria-hidden="true"></i></a>
												</div>
												<?php 
															}
														}
													}
												?>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Datum aplikacije za vizu:</strong>
												<div class="col-sm-4"><?php if($kandidat_datum_aplikacijecheck == NULL){?> <span class="label label-info material-label material-label_info main-container__column">Nema informacije</span><?php  } else{ echo $kandidat_datum_aplikacije; }  ?></div>
												<?php 
													if($kandidat_status == 4 or $kandidat_status == 5){
														if($unesen_apl == 1){
															if($val_apl == null or $val_apl == 3){
												?>
												<div class="text-center col-sm-4 display_apl_val">
													<a href="" data-toggle="modal" data-target="#modalValAplYES" data-kandidat_id="<?php echo $kandidat_id; ?>" data-employee_id="<?php echo $logged_employee_id; ?>" class="validate_apl label material-label material-label_success main-container__column" title = "Validacija datuma apliciranja"><i class="fa fa-plus-circle" aria-hidden="true"></i></a>
													<a href="" data-toggle="modal" data-target="#modalValAplNO" data-kandidat_id="<?php echo $kandidat_id; ?>" data-employee_id="<?php echo $logged_employee_id; ?>" class="validate_apl label material-label material-label_danger main-container__column" title = "Validacija datuma apliciranja"><i class="fa fa-minus-circle" aria-hidden="true"></i></a>
												</div>
												<?php 
															}
														}
													}
												?>
											</div>
											
											<div class="row">
												<strong class="col-sm-4 text-right">Datum termina:</strong>
												<div class="col-sm-4"><?php if($kandidat_datum_terminacheck == NULL){?> <span class="label label-info material-label material-label_info main-container__column">Nema informacije</span><?php  } else{ echo $kandidat_datum_termina; }  ?></div>
												<?php 
													if($kandidat_status == 4 or $kandidat_status == 5){
														if($unesen_termin == 1){
															if($val_termin == null or $val_termin == 3){
												?>
												<div class="text-center col-sm-4 display_ter_val">
													<a href="" data-toggle="modal" data-target="#modalValTerminYES" data-kandidat_id="<?php echo $kandidat_id; ?>" data-employee_id="<?php echo $logged_employee_id; ?>" class="validate_termin label material-label material-label_success main-container__column" title = "Validacija termina. "><i class="fa fa-plus-circle" aria-hidden="true"></i></a>
													<a href="" data-toggle="modal" data-target="#modalValTerminNO" data-kandidat_id="<?php echo $kandidat_id; ?>" data-employee_id="<?php echo $logged_employee_id; ?>" class="validate_termin label material-label material-label_danger main-container__column" title = "Validacija termina "><i class="fa fa-minus-circle" aria-hidden="true"></i></a>
												</div>
												<?php 
															}
														}
													}
												?>
											</div>
											<hr>
											<div class="row">
												<strong class="col-sm-4 text-right">Datum potpisa ugovora: </strong>
												<div class="col-sm-8"><?php if($kandidat_datum_ugovora == NULL){?> <span class="label label-info material-label material-label_info main-container__column">Nema informacije</span><?php  } else{ echo $kandidat_datum_ugovora; }  ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Datum početka rada:</strong>
												<div class="col-sm-8"><?php if($kandidat_datum_pocetakrada == NULL){?> <span class="label label-info material-label material-label_info main-container__column">Nema informacije</span><?php  } else{ echo $kandidat_datum_pocetakrada; }  ?></div>
											</div>
											<?php if(!empty($kandidat_partner_status)){
													$partner_query = $db->prepare("SELECT jp_imeprezime FROM idk_jobstep_partners WHERE jp_id = $kandidat_partnerid");
													$partner_query->execute();
													$row_partner = $partner_query->fetch();
													$partner_ime = $row_partner['jp_imeprezime'];
												?>
												<hr/>
												<div class="row">
													<strong class="col-sm-4 text-right text-danger">Preporučio/la:</strong>
													<div class="col-sm-8"><a href="<?php getSiteUrl(); ?>partners/<?php echo $kandidat_partnerid; ?>"> <?php echo $partner_ime;   ?></a></div>
												</div>
												<?php
											}
											?>
											<hr>
											<div class="row">
												<strong class="col-sm-4 text-right" style="margin-top: 7px;">Termin za casting:</strong>
												<div class="col-sm-4" style="margin-top: 5px;" id="appts_container">
													
												</div>
												<div class="col-sm-4">
													<?php 
													if(getEmployeeStatus() != 20){
														?>
														<a href="#" data-toggle="modal" data-target="#modalTermin" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive" style="margin-top: 3px;"><i class="fa fa-pencil" aria-hidden="true"></i><span>Zakaži termin</span></a>
														<?php		
													} ?>
													</div>
												<div class="col-sm-4"><button id="refresh_casting" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive" style="margin-top: 3px;"><i class="fa fa-refresh" aria-hidden="true"></i><span>Osvježi</span></button></div>

											</div>
											<?php
												//if($kandidat_id == 65776){
											?>
												<hr>
												<div class="row" style="height: 40px;">
													<strong class="col-sm-4 text-right" style="margin-top: 7px;">Potencijalni početak rada:</strong>
													<div class="col-sm-4" style="margin-top: 7px;" id="greska_appts_container">
													<!-- id diva iznad se greskom zvao kao i div iznad njega koji se puni ajaxom -->
													<?php
														$get_potencijalni_pocetak_rada = $db->prepare("SELECT kandidat_potencijalni_pocetak_rada FROM idk_kandidati WHERE kandidat_id = :kandidat_id");
														$get_potencijalni_pocetak_rada->execute(array(
															":kandidat_id" => $kandidat_id
														));
														$result_potencijalni_pocetak_rada = $get_potencijalni_pocetak_rada->fetch();
														$potencijalni_pocetak_rada = $result_potencijalni_pocetak_rada['kandidat_potencijalni_pocetak_rada'];
														$potencijalni_pocetak_rada_f = str_replace('to', '-', $potencijalni_pocetak_rada);
														if($potencijalni_pocetak_rada != null){
															echo '<span class="label label-info material-label material-label_info main-container__column">'.$potencijalni_pocetak_rada_f.'</span>';
														} else {
															echo '<span class="label label-danger material-label material-label_danger main-container__column">Nema informacije</span>';
														}
													?>
													</div>
													<div class="col-sm-4">
													<?php
														if(getEmployeeStatus() != 20){
															if($potencijalni_pocetak_rada != null){
																echo '<a href="#" data-toggle="modal" data-target="#modalPotencijalniPocetakRada" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive" style="/*margin-left: 150px; margin-top: 3px;*/"><i class="fa fa-pencil" aria-hidden="true"></i><span>Izmjeni</span></a>';
															} else {
																echo '<a href="#" data-toggle="modal" data-target="#modalPotencijalniPocetakRada" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive" style="/*margin-left: 150px; margin-top: 3px;*/"><i class="fa fa-plus" aria-hidden="true"></i><span>Dodaj</span></a>';
															}
														}
													?>
													</div>
												</div>
												<div class="row" id="assessment">
														<script>
															let kandidat_id = <?php echo $kandidat_id; ?>;
															$.ajax({
																url: 'ajax_data.php?page=candidate_assessment',
																type: 'POST',    
																data: {
																	'candidate_id':kandidat_id
																},
																dataType: 'html',
																success: function(data) {
																	$("#assessment").html(data);
																}
															});
														</script>
												</div>
											<?php
												//}
											?>
										</div>
									</div>
									<script>
										$(".validate_viza").click(function() {
											var kandidat_id = $(this).data("kandidat_id");
											var employee_id = $(this).data("employee_id");
											// console.log(kandidat_id);
											document.getElementById('viza_kandidat_id').value = kandidat_id;
											document.getElementById('viza_kandidat_id2').value = kandidat_id;
											document.getElementById('viza_employee_id').value = employee_id;
											document.getElementById('viza_employee_id2').value = employee_id;
										});
										$(".validate_apl").click(function() {
											var kandidat_id = $(this).data("kandidat_id");
											var employee_id = $(this).data("employee_id");
											// console.log(kandidat_id);
											document.getElementById('viza_kandidat_id_a').value = kandidat_id;
											document.getElementById('viza_kandidat_id_a2').value = kandidat_id;
											document.getElementById('viza_employee_id_a').value = employee_id;
											document.getElementById('viza_employee_id_a2').value = employee_id;
										});
										$(".validate_termin").click(function() {
											var kandidat_id = $(this).data("kandidat_id");
											var employee_id = $(this).data("employee_id");
											// console.log(kandidat_id);
											document.getElementById('viza_kandidat_id_t').value = kandidat_id;
											document.getElementById('viza_kandidat_id_t2').value = kandidat_id;
											document.getElementById('viza_employee_id_t').value = employee_id;
											document.getElementById('viza_employee_id_t2').value = employee_id;
										});
										$(".validate_all_X").click(function() {
											var kandidat_id = $(this).data("kandidat_id");
											var employee_id = $(this).data("employee_id");
											var vrsta_podatka = $(this).data("vrsta_podatka");
											document.getElementById('val_kandidat_idX').value = kandidat_id;
											document.getElementById('val_employee_idX').value = employee_id;
											$.ajax({
												url: 'ajax.php?page=select_blok',
												type: 'POST',    
												data: {'vrsta_podatka': vrsta_podatka},
												dataType: 'html',
												success: function(data) {
													console.log(data);
													$("#select_block").html(data);
													$('.selectpicker').selectpicker('refresh');
												}
												
											});
											
										});
										$(".validate_all").click(function() {
											var kandidat_id = $(this).data("kandidat_id");
											var employee_id = $(this).data("employee_id");
											var vrsta_podatka = $(this).data("vrsta_podatka");
											document.getElementById('val_kandidat_id').value = kandidat_id;
											document.getElementById('val_employee_id').value = employee_id;
											document.getElementById('val_vrsta_podatka').value = vrsta_podatka;
										});
										$(".validate_all_sveodjednom").click(function() {
											var kandidat_id = $(this).data("kandidat_id");
											var employee_id = $(this).data("employee_id");
											document.getElementById('val_kandidat_id_sveodjednom').value = kandidat_id;
											document.getElementById('val_employee_id_sveodjednom').value = employee_id;
										});
									</script>
							<?php
								
									$query_get_nalog = $db->prepare("
																		SELECT
																			nalog_id,
																			nalog_naziv
																		FROM
																			idk_nalozi
																		JOIN
																			idk_projects
																		ON
																			idk_projects.project_nalogid = idk_nalozi.nalog_id
																		JOIN
																			idk_project_kandidati
																		ON
																			idk_project_kandidati.pk_projectid = idk_projects.project_id
																		WHERE
																			pk_kandidatid = :kandidat_id
																		AND
																			(project_name LIKE '%Casting%' OR project_name LIKE '%Intervju%')
																		ORDER BY nalog_id DESC
																	");
									$query_get_nalog->execute(array(
										":kandidat_id" => $kandidat_id
									));
									$result_get_nalog = $query_get_nalog->fetch();
									
									$nalog_id 		= $result_get_nalog['nalog_id'];
									$naziv_naloga 	= $result_get_nalog['nalog_naziv'];
									
									// ZA NALOGE TZ,BL,SA,BG -- SJEDINJENI SU U JEDAN NALOG(222)
									if($nalog_id == 217 or $nalog_id == 218 or $nalog_id == 219 or $nalog_id == 220){
										$nalog_id = 222;
									}
							?>
									
									<script>
										$(document).ready(function(){
											var nalog_id 	= '<?php echo $nalog_id; ?>';
											var kandidat_id = '<?php echo $kandidat_id; ?>';
											$.ajax({
												url: 'ajax_data.php?page=list_appointments',
												type: 'POST',    
												data: {
													'nalog_id'		:nalog_id,
													'kandidat_id'	:kandidat_id
												},
												dataType: 'html',
												success: function(data) {
													$("#appts_container").html(data);
													$(".delete_appt").click(function(){
														var pca_id 	= $(this).data("pca_id");
														var appt_id = $(this).data("appt_id");
														var time 	= $(this).data("pca_time");
														$("#pca_id").val(pca_id);
														$("#appt_id").val(appt_id);
														$("#time").val(time);
													});
												}
											});
											$("#refresh_casting").click(function(){
												$("#appts_container").empty();
												$.ajax({
													url: 'ajax_data.php?page=list_appointments',
													type: 'POST',    
													data: {
														'nalog_id'		:nalog_id,
														'kandidat_id'	:kandidat_id
													},
													dataType: 'html',
													success: function(data) {
														$("#appts_container").html(data);
														$(".delete_appt").click(function(){
															var pca_id 	= $(this).data("pca_id");
															var appt_id = $(this).data("appt_id");
															var time 	= $(this).data("pca_time");
															$("#pca_id").val(pca_id);
															$("#appt_id").val(appt_id);
															$("#time").val(time);
														});
													}
												});
											});
										});
									</script>
									<!-- MODAL BRISANJE TERMINA -->
									<div class="modal material-modal material-modal_success fade" id="modalDeleteAppt">
										<div class="modal-dialog">
											<div class="modal-content material-modal__content">
												<div class="modal-header material-modal__header">
													<button class="close material-modal__close" data-dismiss="modal">&times;</button>
													<h4 class="modal-title material-modal__title">Brisanje termina</h4>
												</div>
												<div class="modal-body material-modal__body">
												<form action="<?php getSiteURL(); ?>kandidati?page=delete_kandidat_appt" method="POST">
														<div class="form-group">
															<div class="col-md-offset-2 col-sm-8">
																Potvrdom brišete odabrani termin.
															</div>
														</div>
													</div>
													<input type="hidden" name="appt_id" id="appt_id">
													<input type="hidden" name="time" id="time">
													<input type="hidden" name="pca_id" id="pca_id">
													<input type="hidden" name="kandidat_id" id="kandidat_id" value="<?php echo $kandidat_id; ?>">
													<div class="modal-footer material-modal__footer">
														<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
														<button type="submit" class="btn btn-primary material-btn material-btn_success val_isk_da">Potvrdi</button>
													</div>
												</form>
											</div>
										</div>
									</div>
									<!-- MODAL ZAKAZIVANJA TERMINA -->
									<div class="modal material-modal material-modal_success fade" id="modalTermin">
										<div class="modal-dialog">
											<div class="modal-content material-modal__content">
												<div class="modal-header material-modal__header">
													<button class="close material-modal__close" data-dismiss="modal">&times;</button>
													<h4 class="modal-title material-modal__title">Zakazivanje termina za nalog <?php echo $naziv_naloga; ?></h4>
												</div>
												<div class="modal-body material-modal__body">
													<form action="#" enctype="multipart/form-data" method="post" role="form" class="form-horizontal" id="form_potencijalni_pocetak_rada">
														<div class="form-group">
															<label for="termin" class="col-sm-5 control-label">
																<span class="text-danger">
																	*
																</span>
																Izaberite termin:
															</label>
															<div class="col-sm-7">
																	<select class="selectpicker" id="termin" name="termin" required>
																		<option disabled selected value=""> Odaberi </option>
																		<?php
																	
																		$query_get_appointment = $db->prepare("
																											SELECT
																												pap_date,
																												pap_id,
																												pap_city
																											FROM
																												idk_pp_appointments
																											WHERE
																												pap_nalog_id = :nalog_id
																											");
																		$query_get_appointment->execute(array(
																			":nalog_id" => $nalog_id
																		));
																		
																		while($result_get_appointment = $query_get_appointment->fetch()){
																			$pap_id 	= $result_get_appointment['pap_id'];
																			$pap_date 	= $result_get_appointment['pap_date'];
																			$pap_city	= $result_get_appointment['pap_city'];
																			$pap_date_f = date("d.m.Y", strtotime($pap_date));
																		?>
																		<option value="<?php echo $pap_id; ?>"><?php echo $pap_date_f . " - " . $pap_city; ?></option>
																	<?php
																		}
																	?>
																	</select>
															</div>
														</div>
															<div class="form-group">
																<label for="taskforce_form_termin_intervju_manual" class="col-sm-5 control-label">
																	<span class="text-danger">
																		*
																	</span>
																	Satnica termina:
																</label>
																<div class="col-sm-7">
																	<div class="">
																		<select class="selectpicker"  name="taskforce_form_termin_intervju_manual" id="taskforce_form_termin_intervju_manual" placeholder="Termin" required>
																			<option disabled selected value=""> Odaberi </option>
																			<?php if(getLastTFStatus($kandidat_id, $tf_nalog_id, $tf_vrsta_id) == 16){ ?>
																				<option selected value="<?php if($pca_pah_id != NULL){echo $pca_pah_id;}else{echo $pca_time_cand;} ?>"> <?php echo $pca_time_cand; ?> (Kandidat već ima vezanu satnicu) </option>
																			<?php } ?>
																		</select>
																		<span class="materail-input-block__line"></span>
																	</div>
																</div>
															</div>
														<!-- <div class="form-group">
															<label for="vrijeme" class="col-sm-5 control-label">
																<span class="text-danger">
																	*
																</span>
																Izaberite vrijeme:
															</label>
															<div class="col-sm-3">
																<select class="selectpicker" id="sati" name="sati" required>
																	<option selected disabled>hrs</option>
																<?php
																	for($i = 0; $i <= 24; $i++){
																		if($i >= 10){
																?>
																			<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
																<?php
																		} else {
																?>
																			<option value="<?php echo "0" . $i; ?>"><?php echo "0" . $i; ?></option>
																<?php
																		}
																	}
																?>
																</select>
															</div>
															<div class="col-sm-3">
																<select class="selectpicker" id="minute" name="minute" required>
																	<option selected disabled>min</option>
																<?php
																	for($i = 0; $i <= 60; $i++){
																		if($i >= 10){
																?>
																			<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
																<?php
																		} else {
																?>
																			<option value="<?php echo "0" . $i; ?>"><?php echo "0" . $i; ?></option>
																<?php
																		}
																	}
																?>
																</select>
															</div>
														</div> -->
														<script>
														$("#termin").on("change", function(){
															let termin_group = document.getElementById("termin").value;
															let taskforce_form_termin_intervju = document.getElementById("taskforce_form_termin_intervju_manual");

															$.ajax({
																url: 'ajax_data.php?page=check_appointment_time_for_appointment',
																type: 'POST',    
																data: {
																	'pap_id' : termin_group
																},
																dataType: 'json',
																success: function(data) {
																	var select = $('#taskforce_form_termin_intervju_manual');

																	select.empty();

																	data.forEach(function (item) {
																	var option = $('<option>', {
																		value: item.pah_id,
																		text: item.pah_time
																	});
																	select.append(option);
																	});

																	select.selectpicker('refresh');
																}
															});
														});
														</script>
														<input type="hidden" name="kandidat_id" id="kandidat_id" value="<?php echo $kandidat_id; ?>">
														<input type="hidden" name="nalog_id" id="nalog_id" value="<?php echo $nalog_id; ?>">
													</form>
												</div>
												<div class="modal-footer material-modal__footer">
													<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
													<button type="submit" class="btn btn-primary material-btn material-btn_success" id="submit_termin" data-dismiss="modal">Potvrdi</button>
												</div>
												<script>
													$("#submit_termin").click(function(){
														// var hours 		= $("#sati").val();
														// var minutes 	= $("#minute").val();
														var date 		= $("#termin").val();
														var time_id 		= $("#taskforce_form_termin_intervju_manual").val();
														var kandidat_id = "<?php echo $kandidat_id; ?>";
														var nalog_id 	= "<?php echo $nalog_id; ?>";
														// alert(taskforce_form_termin_intervju_manual);
														$.ajax({
															url: 'ajax_data.php?page=add_appointment',
															type: 'POST',    
															data: {
																'nalog_id'		:nalog_id,
																'kandidat_id'	:kandidat_id,
																'time_id'		:time_id,
																'date'			:date
															},
															dataType: 'html',
															success: function(data) {
																$("#appts_container").empty();
																var nalog_id 	= '<?php echo $nalog_id; ?>';
																var kandidat_id = '<?php echo $kandidat_id; ?>';
																$.ajax({
																	url: 'ajax_data.php?page=list_appointments',
																	type: 'POST',    
																	data: {
																		'nalog_id'		:nalog_id,
																		'kandidat_id'	:kandidat_id
																	},
																	dataType: 'html',
																	success: function(data) {
																		$("#appts_container").html(data);
																	}
																});
															}
														});
													});
												</script>
											</div>
										</div>
									</div>
									<!-- MODAL POTENCIJALNI POCETAK RADA -->
									<div class="modal material-modal material-modal_success fade" id="modalPotencijalniPocetakRada">
										<div class="modal-dialog">
											<div class="modal-content material-modal__content">
												<div class="modal-header material-modal__header">
													<button class="close material-modal__close" data-dismiss="modal">&times;</button>
													<h4 class="modal-title material-modal__title">Potencijalni period početka rada</h4>
												</div>
												<div class="modal-body material-modal__body">
													<form action="<?php getSiteURL(); ?>kandidati?page=add_kandidat_potencijalni_pocetak_rada" enctype="multipart/form-data" method="post" role="form" class="form-horizontal" id="form_zakazivanje_termina">
														<div class="form-group">
															<label for="termin" class="col-sm-5 control-label">
																<span class="text-danger">
																	*
																</span>
																Izaberite datum od - do:
															</label>
															<div class="col-sm-7">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input" type="text" name="kandidat_potencijalni_pocetak_rada" id="kandidat_potencijalni_pocetak_rada" placeholder="Datum od - do" autocomplete="off" required>
																	<span class="materail-input-block__line"></span>
																</div>
															</div>
														</div>
														<script>
															$("#kandidat_potencijalni_pocetak_rada").flatpickr({
																dateFormat: "m.Y",
																mode: "range",
																minDate: "today"
															});
														</script>
														<input type="hidden" name="kandidat_id" id="kandidat_id" value="<?php echo $kandidat_id; ?>">
												</div>
												<div class="modal-footer material-modal__footer">
													<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
													<button type="submit" class="btn btn-primary material-btn material-btn_success">Potvrdi</button>
													</form>
												</div>
											</div>
										</div>
									</div>
									
									<!-- MODAL VALIDACIJA SVIH OSNOVNIH INFORMACIJA YES -->
									<div class="modal material-modal material-modal_success fade" id="modalValYESsveodjednom">
										<div class="modal-dialog">
											<div class="modal-content material-modal__content">
												<div class="modal-header material-modal__header">
													<button class="close material-modal__close" data-dismiss="modal">&times;</button>
													<h4 class="modal-title material-modal__title">Validacija</h4>
												</div>
												<div class="modal-body material-modal__body">
													<form action="<?php getSiteURL(); ?>do.php?form=validacija_all_sveodjednom" enctype="multipart/form-data" method="post" role="form" class="form-horizontal" id="form_val_sveodjednom">
														<div class="form-group">
															<div class="col-md-offset-2 col-sm-8">
																Potvrdom označavate da su sve preostale osnovne informacije uredu.
															</div>
														</div>
														<input type="hidden" name="val_kandidat_id_sveodjednom" id="val_kandidat_id_sveodjednom">
														<input type="hidden" name="val_employee_id_sveodjednom" id="val_employee_id_sveodjednom">
													</form>
												</div>
												<div class="modal-footer material-modal__footer">
													<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
													<button type="submit" class="btn btn-primary material-btn material-btn_success" form="form_val_sveodjednom">Potvrdi</button>
												</div>
											</div>
										</div>
									</div>
									
									<!-- MODAL VALIDACIJA PODATAKA  X -->
									<div class="modal material-modal material-modal_danger fade" id="modalValNO">
										<div class="modal-dialog">
											<div class="modal-content material-modal__content">
												<div class="modal-header material-modal__header">
													<button class="close material-modal__close" data-dismiss="modal">&times;</button>
													<h4 class="modal-title material-modal__title">Validacija</h4>
												</div>
												<div class="modal-body material-modal__body">
													<form action="<?php getSiteURL(); ?>do.php?form=validacija_all_X" enctype="multipart/form-data" method="post" role="form" class="form-horizontal" id="form_valX">

														<input type="hidden" name="val_kandidat_id" id="val_kandidat_idX" />
														<input type="hidden" name="val_employee_id" id="val_employee_idX" />
														<div class="form-group">
															<div class="col-md-offset-2 col-sm-8">
																<p style = "margin: 20px 0px 20px 0px;">Odaberite razlog zašto podatak nije uredu</p>
															</div>
														</div>
														<div class="form-group">
															<div class="col-md-offset-2 col-sm-8">
																<div class="col-xs-12"  id="select_block">
																	
																</div>
															</div>
														</div>
													</form>
												</div>
												<div class="modal-footer material-modal__footer">
													<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
													<button type="submit" class="btn btn-primary material-btn material-btn_primary" form="form_valX" >Potvrdi</button>
												</div>
											</div>
										</div>
									</div>
									<!-- MODAL VALIDACIJA PODATAKA -->
									<div class="modal material-modal material-modal_success fade" id="modalValYES">
										<div class="modal-dialog">
											<div class="modal-content material-modal__content">
												<div class="modal-header material-modal__header">
													<button class="close material-modal__close" data-dismiss="modal">&times;</button>
													<h4 class="modal-title material-modal__title">Validacija</h4>
												</div>
												<div class="modal-body material-modal__body">
													<form action="<?php getSiteURL(); ?>do.php?form=validacija_all" enctype="multipart/form-data" method="post" role="form" class="form-horizontal" id="form_val">
														<div class="form-group">
															<div class="col-md-offset-2 col-sm-8">
																Potvrdom označavate da su podaci uredu.
															</div>
														</div>
														<input type="hidden" name="val_kandidat_id" id="val_kandidat_id">
														<input type="hidden" name="val_employee_id" id="val_employee_id">
														<input type="hidden" name="val_vrsta_podatka" id="val_vrsta_podatka">
													</form>
												</div>
												<div class="modal-footer material-modal__footer">
													<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
													<button type="submit" class="btn btn-primary material-btn material-btn_success" form="form_val">Potvrdi</button>
												</div>
											</div>
										</div>
									</div>
									<!-- MODAL VALIDACIJA VIZA DA -->
									<div class="modal material-modal material-modal_success fade" id="modalValVizaYES">
										<div class="modal-dialog">
											<div class="modal-content material-modal__content">
												<div class="modal-header material-modal__header">
													<button class="close material-modal__close" data-dismiss="modal">&times;</button>
													<h4 class="modal-title material-modal__title">Validacija vize</h4>
												</div>
												<div class="modal-body material-modal__body">
													<div class="form-group">
														<div class="col-md-offset-2 col-sm-8">
															Potvrdom označavate da su podaci uredu.
														</div>
													</div>
												</div>
												<input type="hidden" name="viza_kandidat_id" id="viza_kandidat_id">
												<input type="hidden" name="viza_employee_id" id="viza_employee_id">
												<input type="hidden" name="viza_status" id="viza_status" value="1">
												<div class="modal-footer material-modal__footer">
													<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
													<button type="submit" class="btn btn-primary material-btn material-btn_success val_viza_da">Potvrdi</button>
												</div>
											</div>
										</div>
									</div>
									
									<!-- MODAL VALIDACIJA VIZA NE-->
									<div class="modal material-modal material-modal_danger fade" id="modalValVizaNO">
										<div class="modal-dialog">
											<div class="modal-content material-modal__content">
												<div class="modal-header material-modal__header">
													<button class="close material-modal__close" data-dismiss="modal">&times;</button>
													<h4 class="modal-title material-modal__title">Validacija vize</h4>
												</div>
												<div class="modal-body material-modal__body">
													<div class="form-group">
														<div class="col-md-offset-2 col-sm-8">
															<p style = "margin: 20px 0px 20px 0px;">Odaberite razlog zašto viza nije uredu</p>
														</div>
													</div>
													<div class="form-group">
														<div class="col-md-offset-2 col-sm-8">
															<div class = "" style = "margin-bottom: 80px;">
																<select class="selectpicker" id="ro_viza" name="ro_viza" required>
																	<?php
																		$query_razlozi = $db->prepare("
																						SELECT ro_id, ro_naziv, ro_dio_bloka
																						FROM idk_razlozi_odbijanja
																						WHERE ro_blok = 'blok_viza'
																						");
				
																		$query_razlozi->execute();
				
																		while($rowR = $query_razlozi->fetch()){
																			$ro_id = $rowR['ro_id'];
																			$ro_naziv = $rowR['ro_naziv'];
																	?>
																	<option value="<?php echo $ro_id; ?>"><?php echo $ro_naziv; ?></option>
																	<?php 
																		}
																	?>
																</select>
															</div>
														</div>
													</div>
												</div>
												<input type="hidden" name="viza_kandidat_id" id="viza_kandidat_id2">
												<input type="hidden" name="viza_employee_id" id="viza_employee_id2">
												<input type="hidden" name="viza_status" id="viza_status2" value="2">
												<div class="modal-footer material-modal__footer">
													<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
													<button type="submit" class="btn btn-primary material-btn material-btn_danger val_viza_ne">Potvrdi</button>
												</div>
											</div>
										</div>
									</div>
									
									<!-- MODAL VALIDACIJA APLICIRANJA DA -->
									<div class="modal material-modal material-modal_success fade" id="modalValAplYES">
										<div class="modal-dialog">
											<div class="modal-content material-modal__content">
												<div class="modal-header material-modal__header">
													<button class="close material-modal__close" data-dismiss="modal">&times;</button>
													<h4 class="modal-title material-modal__title">Validacija termina</h4>
												</div>
												<div class="modal-body material-modal__body">
													<div class="form-group">
														<div class="col-md-offset-2 col-sm-8">
															Potvrdom označavate da su podaci uredu.
														</div>
													</div>
												</div>
												<input type="hidden" name="viza_kandidat_id" id="viza_kandidat_id_a">
												<input type="hidden" name="viza_employee_id" id="viza_employee_id_a">
												<input type="hidden" name="viza_status" id="viza_status_a" value="1">
												<div class="modal-footer material-modal__footer">
													<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
													<button type="submit" class="btn btn-primary material-btn material-btn_success val_apl_da">Potvrdi</button>
												</div>
											</div>
										</div>
									</div>
									
									<!-- MODAL VALIDACIJA APLICIRANJA NE-->
									<div class="modal material-modal material-modal_danger fade" id="modalValAplNO">
										<div class="modal-dialog">
											<div class="modal-content material-modal__content">
												<div class="modal-header material-modal__header">
													<button class="close material-modal__close" data-dismiss="modal">&times;</button>
													<h4 class="modal-title material-modal__title">Validacija termina</h4>
												</div>
												<div class="modal-body material-modal__body">
													<div class="form-group">
														<div class="col-md-offset-2 col-sm-8">
															<p style = "margin: 20px 0px 20px 0px;">Odaberite razlog zašto apliciranje vize nije uredu</p>
														</div>
													</div>
													<div class="form-group">
														<div class="col-md-offset-2 col-sm-8">
															<div class = "" style = "margin-bottom: 80px;">
																<select class="selectpicker" id="ro_apl" name="ro_apl" required>
																	<?php
																		$query_razlozi = $db->prepare("
																						SELECT ro_id, ro_naziv, ro_dio_bloka
																						FROM idk_razlozi_odbijanja
																						WHERE ro_blok = 'blok_apl'
																						");
				
																		$query_razlozi->execute();
				
																		while($rowR = $query_razlozi->fetch()){
																			$ro_id = $rowR['ro_id'];
																			$ro_naziv = $rowR['ro_naziv'];
																	?>
																	<option value="<?php echo $ro_id; ?>"><?php echo $ro_naziv; ?></option>
																	<?php 
																		}
																	?>
																</select>
															</div>
														</div>
													</div>
												</div>
												<input type="hidden" name="viza_kandidat_id" id="viza_kandidat_id_a2">
												<input type="hidden" name="viza_employee_id" id="viza_employee_id_a2">
												<input type="hidden" name="viza_status" id="viza_status_a2" value="2">
												<div class="modal-footer material-modal__footer">
													<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
													<button type="submit" class="btn btn-primary material-btn material-btn_danger val_apl_ne">Potvrdi</button>
												</div>
											</div>
										</div>
									</div>
									
									<!-- MODAL VALIDACIJA TERMINA DA -->
									<div class="modal material-modal material-modal_success fade" id="modalValTerminYES">
										<div class="modal-dialog">
											<div class="modal-content material-modal__content">
												<div class="modal-header material-modal__header">
													<button class="close material-modal__close" data-dismiss="modal">&times;</button>
													<h4 class="modal-title material-modal__title">Validacija termina</h4>
												</div>
												<div class="modal-body material-modal__body">
													<div class="form-group">
														<div class="col-md-offset-2 col-sm-8">
															Potvrdom označavate da su podaci uredu.
														</div>
													</div>
												</div>
												<input type="hidden" name="viza_kandidat_id" id="viza_kandidat_id_t">
												<input type="hidden" name="viza_employee_id" id="viza_employee_id_t">
												<input type="hidden" name="viza_status" id="viza_status_t" value="1">
												<div class="modal-footer material-modal__footer">
													<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
													<button type="submit" class="btn btn-primary material-btn material-btn_success val_termin_da">Potvrdi</button>
												</div>
											</div>
										</div>
									</div>
									
									<!-- MODAL VALIDACIJA TERMINA NE-->
									<div class="modal material-modal material-modal_danger fade" id="modalValTerminNO">
										<div class="modal-dialog">
											<div class="modal-content material-modal__content">
												<div class="modal-header material-modal__header">
													<button class="close material-modal__close" data-dismiss="modal">&times;</button>
													<h4 class="modal-title material-modal__title">Validacija termina</h4>
												</div>
												<div class="modal-body material-modal__body">
													<div class="form-group">
														<div class="col-md-offset-2 col-sm-8">
															<p style = "margin: 20px 0px 20px 0px;">Odaberite razlog zašto termin za vizu nije uredu</p>
														</div>
													</div>
													<div class="form-group">
														<div class="col-md-offset-2 col-sm-8">
															<div class = "" style = "margin-bottom: 80px;">
																<select class="selectpicker" id="ro_termin" name="ro_termin" required>
																	
																	<?php
																		$query_razlozi = $db->prepare("
																						SELECT ro_id, ro_naziv, ro_dio_bloka
																						FROM idk_razlozi_odbijanja
																						WHERE ro_blok = 'blok_termin'
																						");
				
																		$query_razlozi->execute();
				
																		while($rowR = $query_razlozi->fetch()){
																			$ro_id = $rowR['ro_id'];
																			$ro_naziv = $rowR['ro_naziv'];
																	?>
																	<option value="<?php echo $ro_id; ?>"><?php echo $ro_naziv; ?></option>
																	<?php 
																		}
																	?>
																</select>
															</div>
														</div>
													</div>
												</div>
												<input type="hidden" name="viza_kandidat_id" id="viza_kandidat_id_t2">
												<input type="hidden" name="viza_employee_id" id="viza_employee_id_t2">
												<input type="hidden" name="viza_status" id="viza_status_t2" value="2">
												<div class="modal-footer material-modal__footer">
													<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
													<button type="submit" class="btn btn-primary material-btn material-btn_danger val_termin_ne">Potvrdi</button>
												</div>
											</div>
										</div>
									</div>
									
									<script>
										$(".val_viza_ne").click(function() {
											var kandidat_id = document.getElementById("viza_kandidat_id2").value;
											var employee_id = document.getElementById("viza_employee_id2").value;
											var status = document.getElementById("viza_status2").value;
											var ro_id = document.getElementById("ro_viza").value;
											var dio_bloka = "blok_viza";
											$.ajax({
												url: 'ajax.php?page=validacija_viza',
												type: 'POST',    
												data: {'kandidat_id':kandidat_id, 'employee_id':employee_id, 'status':status, 'ro_id':ro_id, 'dio_bloka':dio_bloka},
												dataType: 'html',
												success: function(data) {
													$('#modalValVizaNO').modal('toggle');
												}
											});
										});
										$(".val_viza_da").click(function() {
											var kandidat_id = document.getElementById("viza_kandidat_id").value;
											var employee_id = document.getElementById("viza_employee_id").value;
											var status = document.getElementById("viza_status").value;
											var dio_bloka = "blok_viza";
											$.ajax({
												url: 'ajax.php?page=validacija_viza_da',
												type: 'POST',    
												data: {'kandidat_id':kandidat_id, 'employee_id':employee_id, 'status':status, 'dio_bloka':dio_bloka},
												dataType: 'html',
												success: function(data) {
													$('#modalValVizaYES').modal('toggle');
													$('.display_viza_val').hide();
												}
											});
										});
										$(".val_apl_ne").click(function() {
											var kandidat_id = document.getElementById("viza_kandidat_id_a2").value;
											var employee_id = document.getElementById("viza_employee_id_a2").value;
											var status = document.getElementById("viza_status_a2").value;
											var ro_id = document.getElementById("ro_apl").value;
											var dio_bloka = "blok_apl";
											$.ajax({
												url: 'ajax.php?page=validacija_viza',
												type: 'POST',    
												data: {'kandidat_id':kandidat_id, 'employee_id':employee_id, 'status':status, 'ro_id':ro_id, 'dio_bloka':dio_bloka},
												dataType: 'html',
												success: function(data) {
													$('#modalValAplNO').modal('toggle');
												}
											});
										});
										$(".val_apl_da").click(function() {
											var kandidat_id = document.getElementById("viza_kandidat_id_a").value;
											var employee_id = document.getElementById("viza_employee_id_a").value;
											var status = document.getElementById("viza_status_a").value;
											var dio_bloka = "blok_apl";
											$.ajax({
												url: 'ajax.php?page=validacija_viza_da',
												type: 'POST',    
												data: {'kandidat_id':kandidat_id, 'employee_id':employee_id, 'status':status, 'dio_bloka':dio_bloka},
												dataType: 'html',
												success: function(data) {
													$('#modalValAplYES').modal('toggle');
													$('.display_apl_val').hide();
												}
											});
										});
										$(".val_termin_ne").click(function() {
											var kandidat_id = document.getElementById("viza_kandidat_id_t2").value;
											var employee_id = document.getElementById("viza_employee_id_t2").value;
											var status = document.getElementById("viza_status_t2").value;
											var ro_id = document.getElementById("ro_termin").value;
											var dio_bloka = "blok_termin";
											$.ajax({
												url: 'ajax.php?page=validacija_viza',
												type: 'POST',    
												data: {'kandidat_id':kandidat_id, 'employee_id':employee_id, 'status':status, 'ro_id':ro_id, 'dio_bloka':dio_bloka},
												dataType: 'html',
												success: function(data) {
													$('#modalValTerminNO').modal('toggle');
												}
											});
										});
										$(".val_termin_da").click(function() {
											var kandidat_id = document.getElementById("viza_kandidat_id_t").value;
											var employee_id = document.getElementById("viza_employee_id_t").value;
											var status = document.getElementById("viza_status_t").value;
											var dio_bloka = "blok_termin";
											$.ajax({
												url: 'ajax.php?page=validacija_viza_da',
												type: 'POST',    
												data: {'kandidat_id':kandidat_id, 'employee_id':employee_id, 'status':status, 'dio_bloka':dio_bloka},
												dataType: 'html',
												success: function(data) {
													$('#modalValTerminYES').modal('toggle');
													$('.display_ter_val').hide();
												}
											});
										});
									</script>
									<br/>
									<br/>
									<br/>
									<br/>

									<hr>

									<!-- Jezici START -->						
										<!-- Modal languages add START-->
										<div class="modal material-modal material-modal_success fade text-left" id="langAdd">
												<div class="modal-dialog ">
													<div class="modal-content material-modal__content">
														<div class="modal-header material-modal__header">
															<button class="close material-modal__close" data-dismiss="modal">&times;</button>
															<h4 class="modal-title material-modal__title">Dodaj jezik</h4>
														</div>
														<div class="modal-body material-modal__body">
															<form action="<?php getSiteURL(); ?>kandidati.php?page=add_kandidat_jezik"  method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
																<input type="hidden" name="kj_kandidat_check" value="<?php echo $kandidat_check; ?>">
																<input type="hidden" name="kj_kandidatid" value="<?php echo $kandidat_id; ?>">
																<div class="form-group">
																	<label for="kj_naziv" class="col-sm-3 control-label"><span class="text-danger">*</span> Jezik:</label>
																	<div class="col-sm-7">
																		<select class="selectpicker" id="kj_naziv" name="kj_naziv" required>
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
																<script>
																	$('#kj_naziv').on('change', function() {
																		if($('#kj_naziv').val()=="Njemački"){
																			$('.izbor_ustanove').css('display', 'block');
																		}else if($('#kj_naziv').val()!="Njemački"){
																			$('.izbor_ustanove').css('display', 'none');
																			$('#kj_ustanova option[value="0"]').prop("selected", "selected").change();;
																		}
																	})
																</script>
																<div class="form-group izbor_ustanove" style="display: none;">
																	<label for="izbor_ustanove" class="col-sm-3 control-label">Odaberi način učenja:</label>
																	<div class="col-sm-7">
																		<select class="selectpicker" id="kj_ustanova" name="kj_ustanova">
																			<option selected value="0">Samostalno</option>
																			<option value="1">Glosa</option>
																			<option value="2">Lingoda</option>
																			<option value="3">CPE</option>
																			<option value="4">OSD</option>
																		</select>
																	</div>
																</div>
																<div class="form-group">
																	<label for="kj_slusanjeat_jezik" class="col-sm-3 control-label"><span class="text-danger">*</span> Nivo jezika: <!-- Slušanje --> </label>
																	<div class="col-sm-7">
																		<select class="selectpicker" id="kj_slusanje" name="kj_slusanje" required>
																			<option value=""></option>
																			<option value="Bez znanja">Bez znanja</option>
																			<option value="A1">A1</option>
																			<option value="A2">A2</option>
																			<option value="B1">B1</option>
																			<option value="B2">B2</option>
																			<option value="C1">C1</option>
																			<option value="C2">C2</option>
																		</select>
																	</div>
																</div>
																<p class="text-center"><small>Sva polja označena sa <span class="text-danger">*</span> su obavezna!</small></p>
																<!-- Stari nacin unosa jezika, ovdje je ako bude trebao nekad u buducnosti
																	<div class="form-group">
																		<label for="kj_citanje" class="col-sm-3 control-label"><span class="text-danger">*</span> Čitanje:</label>
																		<div class="col-sm-7">
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
																		<div class="col-sm-7">
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
																		<div class="col-sm-7">
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
																		<div class="col-sm-7">
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
																-->
																<div class="modal-footer material-modal__footer">
																	<button type="submit" class="btn btn-primary material-btn material-btn_primary">Dodaj</button>
																</div>
															</form>
														</div>	
													</div>
												</div>
											</div>
										<!-- Modal languages add END-->	
										<!-- Button add languages START -->	
											<div class="row">
												<div class="col-xs-12">
													<h4 class="idk_info_title"><i class="fa fa-angle-down" aria-hidden="true"></i> Jezici
													<?php 
														if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "18" , $getEmployeeStatus )) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){	?>
															<span class="pull-right btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive" data-toggle="modal" data-target="#langAdd">
																<i class="fa fa-plus" aria-hidden="true"></i> DODAJ
															</span>
													<?php
														 } 
													?>
													</h4>
												</div>
											</div>
										<!-- Button add languages END -->
										<!-- Tabela jezika START -->	
											<div class="row">
												<div class="col-md-12">
													<table class="table table-hover">
														<thead>
															<tr>
																<th class="text-center">Jezik</th>
																<th class="text-center">Znanje</th>
																<th class="text-center">Aktivan</th>
																<th class="text-center">Status</th>
																<th class="text-center">Cerfitikat</th>
																<th class="text-right"><?php if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "18" , $getEmployeeStatus )) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){	?>Izbriši / Provjeri<?php } ?></th>
															</tr>
														</thead>
														<tbody>
															<?php
																if(getActiveLanguage($kandidat_id)){
																	$lang_query_active = " AND cvl_active=1";
																}else{
																	$lang_query_active = "";
																}

																$select_language_verification= $db->prepare("
																								SELECT kj_id,kj_naziv,kj_slusanje,cvl_course1_started,cvl_course2_started, cvl_motive_start, kj_ustanova,
																									
																									case WHEN cvl_status is null then 'Nije provjeren'
																									else cvl_status
																									end as status,
																									
																									case 
																									when cvl_certificate_expiration_date is null then 'Nema'
																									WHEN cvl_certificate_expiration_date< CURRENT_DATE() then 'Istekao'
																									else cvl_certificate_expiration_date
																									end as certifikat,

																									case when cvl_course1_ended<now() then 'Završio kurs podnivo 1'
																									end as cvl_course1_end,
																									
																									case when cvl_course2_ended<now() then 'Završio kurs podnivo 2'
																									end as cvl_course2_end,

																									CASE WHEN cvl_motive_end < NOW() THEN 'Završio kurs motive' END AS cvl_motive_end,

																									case when cvl_exam_date is null then 'Nije unešen'
																									else cvl_exam_date
																									end as cvl_exam_date,
																									
																									case when cvl_active=1 then 'Aktivan'
																									else 'Nije aktivan'
																									end as cvl_active

																								FROM idk_kandidat_jezici left join idk_candidate_verified_languages  on kj_id=cvl_id  
																								WHERE kj_kandidatid = :kj_kandidatid AND (
																									(kj_naziv LIKE '%Njemacki%' $lang_query_active)
																									OR kj_naziv NOT LIKE '%Njemacki%'
																									)
																								");
																$select_language_verification->execute(array(':kj_kandidatid' => $kandidat_id));
																while($select_row = $select_language_verification->fetch()) {
																	$kj_id = $select_row['kj_id'];
																	$kj_naziv = $select_row['kj_naziv'];
																	$kj_zananje = $select_row['kj_slusanje'];
																	$status = $select_row['status'];
																	$certifikat = $select_row['certifikat'];
																	$cvl_exam_date = date("d.m.Y", strtotime($select_row['cvl_exam_date']));
																	$cvl_active=$select_row['cvl_active'];
																	$cvl_course1_ended = $select_row['cvl_course1_end'];
																	$cvl_motive_end = $select_row['cvl_motive_end'];
																	if(is_null($cvl_course1_ended)){
																		$cvl_course1_started = date("d.m.Y", strtotime($select_row['cvl_course1_started']));
																		$ispis_podnivo_1="Pohađa kurs ".$kj_zananje.".1(".$cvl_course1_started.")";
																	}else{
																		$ispis_podnivo_1 = $cvl_course1_ended;
																	}
																	$cvl_course2_ended = $select_row['cvl_course2_end'];
																	if(is_null($cvl_course2_ended)){
																		$cvl_course2_started = date("d.m.Y", strtotime($select_row['cvl_course2_started']));
																		$ispis_podnivo_2 = "Pohađa kurs ".$kj_zananje.".2(".$cvl_course2_started.")";
																	}else{
																		$ispis_podnivo_2 = $cvl_course2_ended;
																	}
																	$kj_ustanova=$select_row['kj_ustanova'];
																	if($kj_ustanova==1){
																		$uci_preko_ime="(Glosa)";
																	}else if($kj_ustanova==2){
																		$uci_preko_ime="(Lingoda)";
																	}else if($kj_ustanova==3){
																		$uci_preko_ime="(CPE)";
																	}else if($kj_ustanova==4){
																		$uci_preko_ime="(OSD)";
																	}else{
																		$uci_preko_ime="";
																	}

																	if(is_null($cvl_motive_end)){
																		$cvl_motive_start = date("d.m.Y", strtotime($select_row['cvl_motive_start']));
																		$ispis_motive = "Pohađa kurs motive(".$cvl_motive_start.")";
																	}else{
																		$ispis_motive = $cvl_motive_end;
																	}

																	//samo kj_ustanova zamijeniti sa varijablom iz baze
															?>
																	<tr>
																		<td class="text-center"><?php echo $kj_naziv; ?></td>
																		<td class="text-center"><?php echo $kj_zananje; ?></td>
																		<td class="text-center"> <span class="main-container__column material-label label	
																		<?php 
																			if($cvl_active=="Aktivan"){echo 'label-success material-label_success">Aktivan'.$uci_preko_ime ;} 
																			else if($kj_naziv!="Njemački"){echo 'label-">';}
																			else if($cvl_active=="Nije aktivan") {echo 'label-danger material-label_danger">Nije aktivan'.$uci_preko_ime;}
																		?></span></td>
																		<td class="text-center"> <span class="main-container__column material-label label  
																		<?php 
																			if($status==1 or $kj_naziv!="Njemački"){
																				if($kj_ustanova==0){
																					echo 'label-info material-label_info"> Samoprocjena';
																				}else if($kj_ustanova==1){
																					echo 'label-info material-label_info"> Procjena Glose';
																				}else if($kj_ustanova==2){
																					echo 'label-info material-label_info"> Procjena Lingode';
																				}else if($kj_ustanova==3){
																					echo 'label-info material-label_info"> Procjena CPE';
																				}else if($kj_ustanova==4){
																					echo 'label-info material-label_info"> Procjena OSD';
																				}
																			}
																			else if($status==2) {echo 'label-warning material-label_warning"> Samostalno uči';}
																			else if($status==3) {echo 'label-warning material-label_warning"> '.$ispis_podnivo_1;}
																			else if($status==4) {echo 'label-warning material-label_warning"> '.$ispis_podnivo_2;}
																			else if($status==5) {echo 'label-warning material-label_warning">Čeka datum polaganja';}
																			else if($status==6) {echo 'label-warning material-label_warning">Čeka polaganje('.$cvl_exam_date.')';}
																			else if($status==7) {echo 'label-warning material-label_warning">Čeka se rezultat';}
																			else if($status==8) {echo 'label-success material-label_success">Ima certfikat';}
																			else if($status==9) {echo 'label-danger material-label_danger">Certifikat istekao';}
																			else if($status==10) {echo 'label-info material-label_info">Napreduje na veći nivo';}
																			else if($status==11) {echo 'label-danger material-label_danger">Nije položio';}
																			else if($status==12) {echo 'label-danger material-label_danger">Odustao';}
																			else if($status==13) {echo 'label-warning material-label_warning"> Arhiva';}
																			else if($status==14) {echo 'label-warning material-label_warning"> '.$ispis_motive;}
																			else {echo 'label-danger  material-label_danger ">'.$status;}
																		?></span></td>
																		<td class="text-center"><?php  echo $certifikat; ?></td>
																		<td class="text-right">
																			<?php 
																				if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "18" , $getEmployeeStatus )) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){	?>
																					<span style="padding: 0; border: none;" class="dalete_candidate_language material-dropdown-menu__link idk_candidate_action_button idk_candidate_action_button_red" style="cursor: pointer;" data="<?php getSiteURL(); ?>kandidati?page=delete_kandidat_jezik&id=<?php if($status==0){ echo $kj_id;  } ?>&check=<?php echo $kandidat_check; ?>&kandidatid=<?php echo $kandidat_id; ?>" ><?php if($status==0){echo '<i class="fa fa-times" aria-hidden="true" data-toggle="modal" data-target="#provjeriBrisanje"  ></i>';}else{ echo '<i class="fa fa-times" aria-hidden="true" style="cursor: not-allowed;"></i>'; } ?> </span>
																					<script>
																						$(".dalete_candidate_language").click(function () {
																							var addressValue = $(this).attr("data");
																							document.getElementById("confirm_delete_language").href = addressValue;
																						});
																					</script>
																					<!-- MODAL DA LI STE SIGURNI START-->
																						<div class="modal material-modal material-modal_success fade text-center"  id="provjeriBrisanje">
																							<div class="modal-dialog">
																								<div class="modal-content material-modal__content">
																									<div class="modal-header material-modal__header">
																										<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																										<h4 class="modal-title material-modal__title">Da li ste sigurni da želite izbrisati jezik</h4>
																									</div>
																									<div class="modal-body material-modal__body" style="text-align: center;">
																									<a href="" style="" class="btn btn-success" id="confirm_delete_language">Da</a>
																									<button class="btn btn-danger" data-dismiss="modal">Ne</button>			
																									</div>
																									<div class="modal-footer material-modal__footer">
																									</div>
																								</div>
																							</div>
																						</div>
																					<!-- MODAL DA LI STE SIGURNI END-->

																					<!-- 
																						<span class="idk_candidate_action_button idk_candidate_action_button_green"><i class="fa fa-outdent idk_edit_candidate_do edit_candidate_data" data-toggle="modal" data-target="#provjeriAjax" data-id="<?php echo $kj_id; ?>" data-kandidat="<?php echo $kandidat_id; ?>" data-type="edit_kandidat_jezik" aria-hidden="true"></i></span>
																					-->
																					<span class="idk_candidate_action_button idk_candidate_action_button_green"><i class="fa fa-outdent idk_edit_candidate_do edit_candidate_data" data-toggle="modal" data-target="<?php if($kj_naziv=="Njemački"){ echo  '#editAjax';}?>" <?php if($kj_naziv!="Njemački"){echo 'style="cursor: not-allowed;"';} ?> data-id="<?php echo $kj_id; ?>" data-kandidat="<?php echo $kandidat_id; ?>" data-type="provjeri_jezik_kandidata" aria-hidden="true"></i></span>
																			<?php 
																				} 
																			?>
																		</td>
																	</tr>
																<?php
																} 
																?>
														</tbody>
													</table>
												</div>
											</div>
										<!-- Tabela jezika END -->	
									<!-- Jezici END -->

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
															$(document).ready(function () {

																$('#telefon').hide();
																$('#email').hide();
																$('#web').hide();
																$('#messangeri').hide();

																$('#kki_grupa').on('change', function (e) {

																	if (($('#kki_grupa').selectpicker('val') == "1")) {

																		$('#telefon').slideDown();
																		$('#email').slideUp();
																		$('#web').slideUp();
																		$('#messangeri').slideUp();

																	}

																	if (($('#kki_grupa').selectpicker('val') == "2")) {

																		$('#telefon').slideUp();
																		$('#email').slideDown();
																		$('#web').slideUp();
																		$('#messangeri').slideUp();
																	}

																	if (($('#kki_grupa').selectpicker('val') == "3")) {

																		$('#telefon').slideUp();
																		$('#email').slideUp();
																		$('#web').slideDown();
																		$('#messangeri').slideUp();

																	}

																	if (($('#kki_grupa').selectpicker('val') == "4")) {

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
																<select class="selectpicker" id="kki_naziv" name="kki_naziv">
																	<option value="Fiksni">Fiksni</option>
																	<option value="Mobilni">Mobilni</option>
																</select>
															</div>
														</div>

														<div class="form-group">
															<label for="kki_podatak" class="col-sm-3 control-label"><span class="text-danger">*</span> Broj telefona:</label>
															
															<div class="col-sm-9">
																<input class="form-control materail-input" type="tel" name="kki_podatak" id="kki_podatak" placeholder="+38765 555 333">

																<div class="materail-input-block materail-input-block_success">
																	<span class="materail-input-block__line"></span>
																</div>
															</div>
														</div>

														<div id="primary_phone" style="display: none;">
															<div class="form-group">
																<div class="form-check-inline" style="text-align: center; margin-top: 20px; margin-bottom: -20px;">
																	<label class="form-check-label" style="margin: 2px;">
																		<input type="radio" class="form-check-input" name="kki_primary" value="1"> Primarni
																	</label>

																	<label class="form-check-label" style="margin: 2px;">
																		<input type="radio" class="form-check-input" name="kki_primary" value="0"> Sekundarni
																	</label>
																</div>
															</div>

															<script>
																$(document).ready(function () {

																	// Sakrivanje primary radio buttona za dodavanje kontakt informacije
																	if 		(document.querySelector("#kki_naziv").value == "Mobilni") document.querySelector("#primary_phone").style.display = "block";
																	else if (document.querySelector("#kki_naziv").value == "Fiksni")  document.querySelector("#primary_phone").style.display = "none";

																	$('#kki_naziv').on('change', function (e) {

																		if 		(document.querySelector("#kki_naziv").value == "Mobilni") document.querySelector("#primary_phone").style.display = "block";
																		else if (document.querySelector("#kki_naziv").value == "Fiksni")  document.querySelector("#primary_phone").style.display = "none";

																	});

																});
																var telInput = document.getElementById("kki_podatak");
																
																if (telInput.value.startsWith("+") || telInput.value == '') {
																	console.log("TU");
																	console.log('<?php getSiteUrl(); ?>buildTelInput/js/utils.js');
																	iti = window.intlTelInput(telInput, {
																		utilsScript:'<?php getSiteUrl(); ?>buildTelInput/js/utils.js',
																		autoPlaceholder: "aggressive",
																		initialCountry: "ba",
																		formatOnDisplay: true,
																		preferredCountries: ["ba","rs","hr","de"],
																		separateDialCode: true
																	});

																	$("form").submit(function(event) {
																											
																		$("#kki_podatak").val(iti.getNumber()); 

																	});

																	document.querySelector("#telefon > div:nth-child(2) > div > div.iti.iti--allow-dropdown.iti--separate-dial-code").style.width = "100%";
																	
																}
															</script>
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

														
														<div id="primary_email">
															<div class="form-group">
																<div class="form-check-inline" style="text-align: center; margin-top: 20px; margin-bottom: -20px;">
																	<label class="form-check-label" style="margin: 2px;">
																		<input type="radio" class="form-check-input" name="kki_primary" value="1"> Primarni
																	</label>

																	<label class="form-check-label" style="margin: 2px;">
																		<input type="radio" class="form-check-input" name="kki_primary" value="0"> Sekundarni
																	</label>
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

												<div class="modal-footer" style="padding-top: 15px; margin-top: 20px; margin-bottom: -15px;">
													<div class="form-group" style="margin-bottom: 0;">
														<div class="col-sm-offset-2 col-sm-10" style="padding-right: 0;">
																<button type="submit" class="btn btn-success material-btn material-btn_success">Dodaj</button>
															</form>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<?php if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "18" , $getEmployeeStatus )) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){	?>
									<div class="row">
										<div class="col-xs-12">
											<h4 class="idk_info_title"><i class="fa fa-angle-down" aria-hidden="true"></i> Kontakt informacije
											<?php if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "18" , $getEmployeeStatus )) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){	?>
											<span class="pull-right btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive" data-toggle="modal" data-target="#contactAdd"><i class="fa fa-plus" aria-hidden="true"></i> DODAJ</span>
											
											<a id="kontaktinforearenge" href="javascript:void(0);" style="margin-right:10px;" class="pull-right btn outlined mleft_no reorder_link btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive" id="save_reorder"><i class="fa fa-refresh" aria-hidden="true"></i> Izmjeni raspored</a>						
											<?php } ?>
											</h4>
										</div>
									</div>
									<br/>
									
									<script>
									$(document).ready(function(){
										$('#kontaktinforearenge.reorder_link').on('click',function(){
											$("#kontakt_informacije .reorder-process-list").sortable({ tolerance: 'pointer' });
											$('#kontaktinforearenge.reorder_link').html('<i class="fa fa-floppy-o" aria-hidden="true"></i> Sacuvaj');
											$('#kontaktinforearenge.reorder_link').attr("id","save_reorder");
											$('#reorder-helper').slideDown('slow');
											$('#kontakt_informacije .proc_link').attr("href","javascript:void(0);");
											$('#kontakt_informacije .proc_link').css("cursor","move");
											$("#save_reorder").click(function( e ){
													//$(this).html('').prepend('<img src="images/refresh-animated.gif"/>');
													$("#kontakt_informacije .reorder-process-list").sortable('destroy');
													$("#reorder-helper").html( "Izmjenjujem ordere. Molimo Vas ne izlazite sa stranice dok se ne zavrsi" ).removeClass('light_box').addClass('notice notice_error');
										
													var h = [];
													$("#kontakt_informacije .list-group-items").each(function() {  h.push($(this).attr('id').substr(9));  });
													//alert(h);
													
													$.ajax({
														type: "POST",
														url: "<?php getSiteURL(); ?>kandidati?page=reorderKKInfo",
														data: {ids: " " + h + "", type: "process"},
														success: function(data){
															window.location.reload();
														}
													}); 
													return false;
												
												e.preventDefault();     
											});
										});
									});			
									</script>
									
									<?php } ?>
									<?php if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "18" , $getEmployeeStatus )) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){	?>
									<div class="row">
										<div class="col-md-12">
											<script type="text/javascript">
												$(document).ready(function() {
													$('#kontakt_informacije').DataTable({

														responsive: true,

														"order": [[ 0, "desc" ]],

														"bAutoWidth": false,

														"aoColumns": [
																{ "width": "2%" },
																{ "width": "20%" },
																{ "width": "20%" },
																{ "width": "28%" },
																{ "width": "10%", "bSortable": false },
																{ "width": "10%", "bSortable": false, "className": "text-right"}
															]
													});
												} );
											</script>		
																			
											<table id="kontakt_informacije" class="display" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th class="text-center">ID</th>
														<th class="text-center">Vrsta</th>
														<th class="text-center">Naziv</th>
														<th class="text-center">Kontakt</th>
														<th class="text-center">Primarni kontakt</th>
														<th class="text-right"><?php if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "18" , $getEmployeeStatus )) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){	?>Izbriši / Uredi<?php } ?></th>
													</tr>
												</thead>

												<tbody class="reorder-process-list">
													<?php
													$select_query = $db->prepare("
														SELECT kki_id, kki_grupa, kki_naziv, kki_podatak, kki_orderid, kki_primary
														FROM idk_kandidat_kontakt_info
														WHERE kki_kandidat_id = :kki_kandidat_id
														ORDER BY kki_orderid DESC
													");

													$select_query->execute(array(
														':kki_kandidat_id' => $kandidat_id
													));

													$kkcount = 1;

													while ($select_row = $select_query->fetch()) {

														$kki_id      = $select_row['kki_id'];
														$kki_orderid = $select_row['kki_orderid'];
														$kki_grupa   = $select_row['kki_grupa'];

														$check_contact_info_count = $db->prepare("
															SELECT kki_id
															FROM idk_kandidat_kontakt_info
															WHERE kki_grupa = :kki_grupa
															AND kki_kandidat_id = :kki_kandidat_id
														");

														$check_contact_info_count->execute(array(
															':kki_kandidat_id' => $kandidat_id,
															':kki_grupa' => $kki_grupa
														));

														$check_contact_info_count = count($check_contact_info_count->fetchAll());

														if      ($kki_grupa == 1) $kki_grupa = "Telefon";
														else if ($kki_grupa == 2) $kki_grupa = "E-mail";
														else if ($kki_grupa == 3) $kki_grupa = "Web";
														else if ($kki_grupa == 4) $kki_grupa = "Messangeri";
														else {};

														$kki_naziv   = $select_row['kki_naziv'];
														$kki_podatak = $select_row['kki_podatak'];

														$kki_primary = "";

														if ($select_row['kki_primary'] == 1) {
															
															$kki_primary = '
																<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16" style="color: #68c368; transform: translateY(15%);">
																	<path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
																</svg>
															';
													
														}
														?>
														
														<tr id="konta_li_<?php echo $kki_id; ?>" class="list-group-items">
															<div style="float:none;" class="proc_link">
																<td class="text-center"><?php echo $kkcount++; ?></td>
																<td class="text-center"><?php echo $kki_grupa; ?></td>
																<td class="text-center"><?php echo $kki_naziv; ?></td>
																<td class="text-center"><?php echo $kki_podatak; ?></td>
																<td class="text-center"><?php echo $kki_primary; ?></td>
																<td class="text-right">
																	<?php 
																	if ((in_array( "2" , $getEmployeeStatus)) OR (in_array( "18" , $getEmployeeStatus )) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))) {	

																		if (($check_contact_info_count > 1 AND $select_row['kki_primary'] == 0) OR ($check_contact_info_count == 1 AND $select_row['kki_primary'] == 1)) {

																			?>
																			<a class="material-dropdown-menu__link idk_candidate_action_button idk_candidate_action_button_red" data-toggle="modal" data-target="#contactInfoDeleteConfirm<?php echo $kki_id; ?>">
																				<i class="fa fa-times" aria-hidden="true"></i>
																			</a>
																			<?php

																		} else {
																			
																			?>
																			<a class="material-dropdown-menu__link idk_candidate_action_button idk_candidate_action_button_red" data-toggle="modal" data-target="#contactInfoDeleteWarning">
																				<i class="fa fa-times" aria-hidden="true"></i>
																			</a>
																			<?php

																		}
																		
																		?>
																		<span class="idk_candidate_action_button idk_candidate_action_button_green">
																			<i class="fa fa-outdent idk_edit_candidate_do edit_candidate_data" data-toggle="modal" data-target="#editAjax" data-id="<?php echo $kki_id; ?>" data-kandidat="<?php echo $kandidat_id; ?>" data-type="edit_kandidat_kontakt" aria-hidden="true"></i>
																		</span>
																		<?php 

																	} 
																	?>
																</td>
															</div>

															<div class="modal material-modal material-modal_danger fade text-left in" id="contactInfoDeleteConfirm<?php echo $kki_id; ?>">
																<div class="modal-dialog ">
																	<div class="modal-content material-modal__content">
																		<div class="modal-header material-modal__header">
																			<button class="close material-modal__close" data-dismiss="modal">×</button>

																			<h4 class="modal-title material-modal__title">Obriši kontakt informaciju</h4>
																		</div>

																		<br>

																		<div class="modal-body material-modal__body" style="text-align: center;">
																			Jeste li sigurni da želite obrisati ovu kontakt informaciju?
																		</div>

																		<div class="modal-footer" style="padding-top: 15px; margin-top: 20px; margin-bottom: -15px;">
																			<div class="form-group" style="margin-bottom: 0;">
																				<div class="col-sm-offset-2 col-sm-10" style="padding-right: 0;">
																					<a href="<?php getSiteURL(); ?>kandidati?page=delete_kkinfo&id=<?php echo $kki_id; ?>&check=<?php echo $kandidat_check; ?>&kandidatid=<?php echo $kandidat_id;?>">
																						<button type="submit" class="btn material-btn material-btn-icon-danger material-btn_danger main-container__column" style="margin-bottom: 0;"><i class="fa fa-trash" aria-hidden="true" style="border-radius: 2px;"></i> 
																							<span>Obriši</span>
																						</button>
																					</a>
																				</div>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
														</tr>
													<?php 
													}	
												 	?>

													<div class="modal material-modal material-modal_danger fade text-left in" id="contactInfoDeleteWarning">
														<div class="modal-dialog ">
															<div class="modal-content material-modal__content">
																<div class="modal-header material-modal__header">
																	<button class="close material-modal__close" data-dismiss="modal">×</button>

																	<h4 class="modal-title material-modal__title">Obriši kontakt informaciju</h4>
																</div>

																<br>

																<div class="modal-body material-modal__body" style="text-align: center;">
																	<div class="alert alert-danger">
																		Ukoliko želite obrisati primarni kontakt kandidata, potrebno je obrisati ostale kontakt informacije označene kao sekundarne.
																	</div>
																</div>

																<div class="modal-footer" style="padding-top: 15px; margin-bottom: -15px;">
																	<div class="form-group" style="margin-bottom: 0;">
																		<div class="col-sm-offset-2 col-sm-10" style="padding-right: 0;">
																			<button type="submit" class="btn btn-primary material-btn material-btn_danger" data-dismiss="modal">Zatvori</button>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</tbody>
											</table>
										</div>
									</div>
									<?php } ?>
									<br/>
									<br/>
									<br/>
									<br/>


									

		
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
														<input type="hidden" name="ke_naziv" id="ke_naziv">
														<input type="hidden" name="ke_skola_id" id="ke_skola_id">
														<input type="hidden" name="ke_naziv_kvalifikacije" id="ke_naziv_kvalifikacije">
														<div class="form-group">
															<label for="ke_datumod" class="col-sm-4 control-label"><span class="text-danger">*</span> Datum od:</label>
															<div class="col-sm-8">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input" type="text" name="ke_datumod" id="ke_datumod" class="monthPicker" placeholder="Datum od" autocomplete="off" required>
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
																	<input class="form-control materail-input monthPicker" type="text" name="ke_datumdo" id="ke_datumdo" placeholder="Datum do" autocomplete="off" required>
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
																		$("#ke_datumdo").val('');
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
															<label for="ke_naziv_kvalifikacije" class="col-sm-4 control-label"><span class="text-danger">*</span> Naziv kvalifikacije:</label>
															<div class="col-sm-8">
																
																	<select class="selectpicker" name="ke_smjer_id" id="ke_smjer_id" data-actions-box="true" data-live-search="true" title="Odaberi..." required>
																	<?php
																		$query_get_smjerovi = $db->prepare("
																											SELECT
																												ss_id,
																												ss_naziv,
																												ss_skola_id,
																												skola_naziv,
																												skola_tip_obrazovanja
																											FROM
																												idk_skole_smjerovi
																											JOIN
																												idk_skole
																											ON
																												idk_skole.skola_id = idk_skole_smjerovi.ss_skola_id
																										");
																		$query_get_smjerovi->execute();
																		while($result_smjerovi = $query_get_smjerovi->fetch()){
																			$ss_id 			= $result_smjerovi['ss_id'];
																			$ss_naziv 		= $result_smjerovi['ss_naziv'];
																			$ss_skola_id 	= $result_smjerovi['ss_skola_id'];
																			$skola_naziv 	= $result_smjerovi['skola_naziv'];
																			$skola_tip_obrazovanja = $result_smjerovi['skola_tip_obrazovanja'];
																			
																			if($skola_tip_obrazovanja == "srednje"){
																				$tip_obrazovanja = "SSS";
																			}
																			else if($skola_tip_obrazovanja == "visoko"){
																				$tip_obrazovanja = "VSS";
																			}
																	?>
																		<option value="<?php echo $ss_id; ?>" data-ss_naziv="<?php echo $ss_naziv; ?>" data-skola_id="<?php echo $ss_skola_id; ?>" data-skola_naziv="<?php echo $skola_naziv; ?>" data-subtext="<?php echo $skola_naziv ." - ". $tip_obrazovanja; ?>"><?php echo $ss_naziv; ?></option>
																	<?php
																		}
																	?>
																	</select>
															
															</div>
														</div>
														<script>
															$("#ke_smjer_id").change(function(){
																var ss_naziv 	= $("#ke_smjer_id option:selected").data("ss_naziv");
																var skola_id 	= $("#ke_smjer_id option:selected").data("skola_id");
																var skola_naziv = $("#ke_smjer_id option:selected").data("skola_naziv");
																console.log(ss_naziv);
																$("#ke_naziv").val(skola_naziv);
																$("#ke_skola_id").val(skola_id);
																$("#ke_naziv_kvalifikacije").val(ss_naziv);
																
															});
														</script>
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
																<select class="selectpicker" id="ke_drzava" name="ke_drzava">
																	<!-- OVDJE DODATI NOVU DRZAVU insert -->
																	<option value="Bosna i Hercegovina">Bosna i Hercegovina</option>
																	<option value="Hrvatska">Hrvatska</option>
																	<option value="Njemačka">Njemačka</option>
																	<option value="Srbija">Srbija</option>
																	<option value="Albanija">Albanija</option>
																	<option value="Austrija">Austrija</option>
																	<option value="Bugarska">Bugarska</option>
																	<option value="Crna Gora">Crna Gora</option>
																	<option value="Danska">Danska</option>
																	<option value="Italija">Italija</option>
																	<option value="Kosovo">Kosovo</option>
																	<option value="Mađarska">Mađarska</option>
																	<option value="Makedonija">Makedonija</option>
																	<option value="Slovenija">Slovenija</option>
																	<option value="Švicarska">Švicarska</option>
																</select>
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
											<h4 class="idk_info_title"><i class="fa fa-angle-down" aria-hidden="true"></i> Školovanje/edukacija
											<?php if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "18" , $getEmployeeStatus )) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){	?>
											<span class="pull-right btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive" data-toggle="modal" data-target="#educationAdd" id="dodaj"><i class="fa fa-plus" aria-hidden="true"></i> DODAJ</span>
											<a id="skolovanjeedukacija" href="javascript:void(0);" style="margin-right:10px;" class="pull-right btn outlined mleft_no reorder_link btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive" id="save_reorder"><i class="fa fa-refresh" aria-hidden="true"></i> Izmjeni raspored</a>						
								
											<?php } ?>
											</h4>
										</div>
									</div>
									<br/>
									
									<script>
									$(document).ready(function(){
										$('#skolovanjeedukacija.reorder_link').on('click',function(){
											$("#skolovanje_i_edukacija .reorder-process-list").sortable({ tolerance: 'pointer' });
											$('#skolovanjeedukacija.reorder_link').html('<i class="fa fa-floppy-o" aria-hidden="true"></i> Sacuvaj');
											$('#skolovanjeedukacija.reorder_link').attr("id","save_reorder");
											$('#reorder-helper').slideDown('slow');
											$('#skolovanje_i_edukacija .proc_link').attr("href","javascript:void(0);");
											$('#skolovanje_i_edukacija .proc_link').css("cursor","move");
											$("#save_reorder").click(function( e ){
													//$(this).html('').prepend('<img src="images/refresh-animated.gif"/>');
													$("#skolovanje_i_edukacija .reorder-process-list").sortable('destroy');
													$("#reorder-helper").html( "Izmjenjujem ordere. Molimo Vas ne izlazite sa stranice dok se ne zavrsi" ).removeClass('light_box').addClass('notice notice_error');
										
													var h = [];
													$("#skolovanje_i_edukacija .list-group-items").each(function() {  h.push($(this).attr('id').substr(9));  });
													//alert(h);
													
													$.ajax({
														type: "POST",
														url: "<?php getSiteURL(); ?>kandidati?page=reorderEdukacijaInfo",
														data: {ids: " " + h + "", type: "process"},
														success: function(data){
															window.location.reload();
														}
													}); 
													return false;
												
												e.preventDefault();     
											});
										});
									});			
									</script>									
									
									<div class="row">
										<div class="col-md-12">
											<script type="text/javascript">
												$(document).ready(function() {
													$('#skolovanje_i_edukacija').DataTable({

														responsive: true,

														//"order": [[ 0, "desc" ]],

														"bAutoWidth": false,

														
													});
												} );
											</script>										
											<table id="skolovanje_i_edukacija" class="display" cellspacing="0" width="100%">
												<thead>
													<tr>

														<th class="text-center">ID</th>
														<th class="text-center">Naziv kvalifikacije</th>
														<th class="text-center">Naziv kvalifikacije DE</th>
														<th class="text-center">Naziv kvalifikacije EN</th>
														<th class="text-center">Naziv</th>
														<th class="text-center">Od</th>
														<th class="text-center">Do</th>
														<th class="text-center">Grad</th>
														<th class="text-center">Država</th>
														<th class="text-center">Vrsta</th>
														<th class="text-center">Prikaz PP</th>
														<th class="text-right"><?php if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "18" , $getEmployeeStatus )) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){	?>Izbriši / Uredi<?php } ?></th>
													</tr>
												</thead>
												<tbody class="reorder-process-list">
													<?php
														$select_query = $db->prepare("
																			SELECT ke_id, ke_datumod, ke_datumdo, ke_naziv_kvalifikacije, ke_smjer_id, ke_naziv_kvalifikacije_de, ke_skola_id, ke_naziv, ke_grad, ke_drzava, ke_opis, ke_orderid, ke_vrsta_obrazovanja, ke_prikaz_pp, ke_aktuelno
																			FROM idk_kandidat_edukacija
																			WHERE ke_kandidat_id = :ke_kandidat_id
																			ORDER BY ke_datumod DESC
																			");

														$select_query->execute(array(
																		':ke_kandidat_id' => $kandidat_id));
														
														$skolCOunt = 1;
														while($select_row = $select_query->fetch()) {

															$ke_id = $select_row['ke_id'];
															$ke_datumod = $select_row['ke_datumod'];
															if($ke_datumod == null)
																$ke_datumod_f = "-";
															else
															$ke_datumod_f = date('m.Y', strtotime($ke_datumod));
															$ke_datumdo = $select_row['ke_datumdo'];
															$ke_naziv_kvalifikacije = $select_row['ke_naziv_kvalifikacije'];
															$ke_naziv_kvalifikacije_de = $select_row['ke_naziv_kvalifikacije_de'];
															$ke_naziv = $select_row['ke_naziv'];
															$ke_grad = $select_row['ke_grad'];
															$ke_drzava = $select_row['ke_drzava'];
															$ke_opis = $select_row['ke_opis'];
															$ke_vrsta_obrazovanja = $select_row['ke_vrsta_obrazovanja'];
															$ke_prikaz_pp = $select_row['ke_prikaz_pp'];
															$ke_smjer_id = $select_row['ke_smjer_id'];
															$ke_skola_id = $select_row['ke_skola_id'];
															$ke_aktuelno = $select_row['ke_aktuelno'];

															if($ke_smjer_id != null){
																$get_smjer_de = $db->prepare("SELECT ss_naziv_de, ss_naziv_en FROM idk_skole_smjerovi WHERE ss_id = $ke_smjer_id");
																$get_smjer_de->execute();
																$row_smjer_de = $get_smjer_de->fetch();
																$smjer_naziv_de = $row_smjer_de['ss_naziv_de'];
																$ss_naziv_en = $row_smjer_de['ss_naziv_en'];
															}else{
																$smjer_naziv_de = $ke_naziv_kvalifikacije_de;
																$ss_naziv_en = "";
															}

															if($ke_aktuelno != 1){
																if($ke_datumdo == null)
																	$ke_datumdo_f = "-";
																else
																	$ke_datumdo_f = date('m.Y', strtotime($ke_datumdo));
															}else{
																$ke_datumdo_f = "Aktuelno";
															}
															if($ke_vrsta_obrazovanja == null)
																$ke_vrsta_obrazovanja = "-";

															//OVDJE DODATI NOVU DRZAVU kandidati.php
															if(
																in_array(
																	trim(strtolower($ke_drzava)), 
																	array(
																		'bih', 'bosna i hercegovina', 'bosna', 'bosna i herzegovina', 'republika srpska', 'b i h', 'bosnien und herzegowina',
																		'hrvatska', 'kroatien', 'republika hrvatska',
																		'njemačka', 'deutschland', 'nemačka',
																		'srbija', 'republika srbija', 'serbia', 'r srbija', 'serbien',
																		'crna gora', 'montenegro',
																		'makedonija', 'macedonia', 'sjeverna makedonija',
																		'albanija', 'austrija', 'bugarska', 'danska', 'italija', 'kosovo', 'mađarska', 'slovenija', 'švicarska'
																	)
																)
															){
																$drzava_style = "";
															}else{
																$drzava_style = "style='color: red;'";
															}
													?>
													<tr id="eduka_li_<?php echo $ke_id; ?>" class="list-group-items">
														<div style="float:none;" class="proc_link">
															<td class="text-center"><?php echo $skolCOunt++; ?></td>
															<td class="text-center">
															<?php 
																if($ke_smjer_id != null){
																	echo $ke_naziv_kvalifikacije;
																} else {
																	echo '<span class="label label-danger material-label material-label_danger material-label_xs main-container__column">'.$ke_naziv_kvalifikacije.'</span>';
																}
															?>
															</td>
															<td class="text-center">
																<?php  
																if($ke_smjer_id != null){
																	if($smjer_naziv_de != null){
																		?>
																		<a href="<?php getSiteURL(); ?>skole?page=edit_school_direction&ids=<?php echo $ke_skola_id; ?>&idss=<?php echo $ke_smjer_id; ?>" target="_BLANK"><?php echo $smjer_naziv_de; ?></a>
																		<?php
																	} else {
																		?>
																		<a href="<?php getSiteURL(); ?>skole?page=edit_school_direction&ids=<?php echo $ke_skola_id; ?>&idss=<?php echo $ke_smjer_id; ?>" target="_BLANK">Dodaj njemački naziv</a>
																		<?php
																	}
																} else {
																	echo $smjer_naziv_de;
																}
																?>
															</td>
															<td class="text-center">
																<?php  
																if($ke_smjer_id != null){
																	if($ss_naziv_en != null){
																		?>
																		<a href="<?php getSiteURL(); ?>skole?page=edit_school_direction&ids=<?php echo $ke_skola_id; ?>&idss=<?php echo $ke_smjer_id; ?>" target="_BLANK"><?php echo $ss_naziv_en; ?></a>
																		<?php
																	} else {
																		?>
																		<a style="color: red;" href="<?php getSiteURL(); ?>skole?page=edit_school_direction&ids=<?php echo $ke_skola_id; ?>&idss=<?php echo $ke_smjer_id; ?>" target="_BLANK">Dodaj engleski naziv</a>
																		<?php
																	}
																} else {
																	echo $ss_naziv_en;
																}
																?>
															</td>
															<td class="text-center"><?php echo $ke_naziv; ?></td>
															<td class="text-center"><?php echo $ke_datumod_f; ?></td>
															<td class="text-center"><?php echo $ke_datumdo_f; ?></td>
															<td class="text-center"><?php echo $ke_grad; ?></td>
															<td class="text-center" <?php echo $drzava_style; ?>><?php echo $ke_drzava; ?></td>
															<td class="text-center"><?php echo $ke_vrsta_obrazovanja; ?></td>
															<td class="text-center">
																<?php
																	if($ke_prikaz_pp == 1){
																		echo '<span class="glyphicon glyphicon-ok-circle" aria-hidden="true" style="color: green;"></span>';
																	} else {
																		echo '<span class="glyphicon glyphicon-remove-circle" aria-hidden="true" style="color: red;"></span>';
																	}
																?>
															</td>
															<!-- <td class="text-center">
																<div class="display_obr_val_<?php /*echo $ke_id;*/ ?>"> -->
															<?php
																/*if($kandidat_status == 4 or $kandidat_status == 5){
																	$val_obr = getValidacijaObrIsk($kandidat_id, "obrazovanje", $ke_id);
																	if($val_obr != 1){*/
															?>
																<!-- <a href="" data-toggle="modal" data-target="#modalValObrYES" data-kandidat_id="<?php /*echo $kandidat_id;*/ ?>" data-employee_id="<?php /*echo $logged_employee_id;*/ ?>" data-podatak_id="<?php /*echo $ke_id;*/ ?>" class="validate_obr btn material-btn material-btn_success main-container__column" title = "Validacija obrazovanja"><i class="fa fa-plus-circle" aria-hidden="true"></i></a>
																<a href="" data-toggle="modal" data-target="#modalValObrNO" data-kandidat_id="<?php /*echo $kandidat_id;*/ ?>" data-employee_id="<?php /*echo $logged_employee_id;*/ ?>" data-podatak_id="<?php /*echo $ke_id;*/ ?>" class="validate_obr btn material-btn material-btn_danger main-container__column" title = "Validacija obrazovanja"><i class="fa fa-minus-circle" aria-hidden="true"></i></a> -->
															<?php 
																	/*}
																}*/
															?>
																<!-- </div>
															</td> -->
															<td class="text-right">
															<?php if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "18" , $getEmployeeStatus )) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){	?>
															<a href="" data-toggle="modal" data-target="#modalDeleteEdu" data-ke_id="<?php echo $ke_id; ?>" data-kandidat_check="<?php echo $kandidat_check; ?>" data-kandidat_id="<?php echo $kandidat_id; ?>" class="material-dropdown-menu__link idk_candidate_action_button idk_candidate_action_button_red delete_edu" id="delete_edu"><i class="fa fa-times" aria-hidden="true"></i></a>
	
															<span class="idk_candidate_action_button idk_candidate_action_button_green"><i class="fa fa-outdent idk_edit_candidate_do edit_candidate_data" data-toggle="modal" data-target="#editAjax" data-id="<?php echo $ke_id; ?>" data-kandidat="<?php echo $kandidat_id; ?>" data-type="edit_kandidat_edukacija" aria-hidden="true"></i></span>
															<?php } ?>
															</td>
														</div>
													</tr>
													<?php } ?>
												</tbody>
											</table>
											<script>
												$(".validate_obr").click(function() {
													var kandidat_id = $(this).data("kandidat_id");
													var employee_id = $(this).data("employee_id");
													var podatak_id = $(this).data("podatak_id");
													// console.log(kandidat_id);
													document.getElementById('obr_kandidat_id').value = kandidat_id;
													document.getElementById('obr_kandidat_id2').value = kandidat_id;
													document.getElementById('obr_employee_id').value = employee_id;
													document.getElementById('obr_employee_id2').value = employee_id;
													document.getElementById('obr_podatak_id').value = podatak_id;
													document.getElementById('obr_podatak_id2').value = podatak_id;
												});
												$(".delete_edu").click(function(){
													var ke_id 			= $(this).data("ke_id");
													var kandidat_check 	= $(this).data("kandidat_check");
													$("#ke_id").val(ke_id);
													$("#kandidat_check").val(kandidat_check);
												});
											</script>
											<!-- MODAL BRISANJE EDUKACIJE -->
											<div class="modal material-modal material-modal_success fade" id="modalDeleteEdu">
												<div class="modal-dialog">
													<div class="modal-content material-modal__content">
														<div class="modal-header material-modal__header">
															<button class="close material-modal__close" data-dismiss="modal">&times;</button>
															<h4 class="modal-title material-modal__title">Brisanje edukacije</h4>
														</div>
														<div class="modal-body material-modal__body">
														<form action="<?php getSiteURL(); ?>kandidati?page=delete_kandidat_edukacija" method="POST">
																<div class="form-group">
																	<div class="col-md-offset-2 col-sm-8">
																		Potvrdom brišete odabranu edukaciju.
																	</div>
																</div>
															</div>
															<input type="hidden" name="ke_id" id="ke_id">
															<input type="hidden" name="kandidat_check" id="kandidat_check">
															<input type="hidden" name="kandidat_id" id="kandidat_id" value="<?php echo $kandidat_id; ?>">
															<div class="modal-footer material-modal__footer">
																<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
																<button type="submit" class="btn btn-primary material-btn material-btn_success val_isk_da">Potvrdi</button>
															</div>
														</form>
													</div>
												</div>
											</div>
											
											<!-- MODAL VALIDACIJA OBRAZOVANJE DA -->
											<div class="modal material-modal material-modal_success fade" id="modalValObrYES">
												<div class="modal-dialog">
													<div class="modal-content material-modal__content">
														<div class="modal-header material-modal__header">
															<button class="close material-modal__close" data-dismiss="modal">&times;</button>
															<h4 class="modal-title material-modal__title">Validacija obrazovanja</h4>
														</div>
														<div class="modal-body material-modal__body">
															<div class="form-group">
																<div class="col-md-offset-2 col-sm-8">
																	Potvrdom označavate da su podaci uredu.
																</div>
															</div>
														</div>
														<input type="hidden" name="obr_kandidat_id" id="obr_kandidat_id">
														<input type="hidden" name="obr_employee_id" id="obr_employee_id">
														<input type="hidden" name="obr_podatak_id" id="obr_podatak_id">
														<input type="hidden" name="obr_status" id="obr_status" value="1">
														<div class="modal-footer material-modal__footer">
															<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
															<button type="submit" class="btn btn-primary material-btn material-btn_success val_obr_da">Potvrdi</button>
														</div>
													</div>
												</div>
											</div>
											<!-- MODAL VALIDACIJA OBRAZOVANJE NE-->
											<div class="modal material-modal material-modal_danger fade" id="modalValObrNO">
												<div class="modal-dialog">
													<div class="modal-content material-modal__content">
														<div class="modal-header material-modal__header">
															<button class="close material-modal__close" data-dismiss="modal">&times;</button>
															<h4 class="modal-title material-modal__title">Validacija obrazovanja</h4>
														</div>
														<div class="modal-body material-modal__body">
															<div class="form-group">
																<div class="col-md-offset-2 col-sm-8">
																	<p style = "margin: 20px 0px 20px 0px;">Odaberite razlog zašto podaci nisu uredu</p>
																</div>
															</div>
															<div class="form-group">
																<div class="col-md-offset-2 col-sm-8">
																	<div class = "" style = "margin-bottom: 80px;">
																		<select class="selectpicker" id="ro_obr" name="ro_obr" required>
																			<?php
																				$query_razlozi = $db->prepare("
																								SELECT ro_id, ro_naziv, ro_dio_bloka
																								FROM idk_razlozi_odbijanja
																								WHERE ro_blok = 'blok_obr'
																								");
						
																				$query_razlozi->execute();
						
																				while($rowR = $query_razlozi->fetch()){
																					$ro_id = $rowR['ro_id'];
																					$ro_naziv = $rowR['ro_naziv'];
																					$ro_dio_bloka = $rowR['ro_dio_bloka'];
																			?>
																			<option value="<?php echo $ro_id; ?>"><?php echo $ro_naziv; ?></option>
																			<?php 
																				}
																			?>
																		</select>
																	</div>
																</div>
															</div>
														</div>
														<input type="hidden" name="obr_kandidat_id" id="obr_kandidat_id2">
														<input type="hidden" name="obr_employee_id" id="obr_employee_id2">
														<input type="hidden" name="obr_podatak_id" id="obr_podatak_id2">
														<input type="hidden" name="obr_status" id="obr_status2" value="2">
														<input type="hidden" name="ro_dio_bloka" id="ro_dio_bloka" value="<?php echo $ro_dio_bloka; ?>">
														<div class="modal-footer material-modal__footer">
															<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
															<button type="submit" class="btn btn-primary material-btn material-btn_danger val_obr_ne">Potvrdi</button>
														</div>
													</div>
												</div>
											</div>
											<script>
												$(".val_obr_ne").click(function() {
													var kandidat_id = document.getElementById("obr_kandidat_id2").value;
													var employee_id = document.getElementById("obr_employee_id2").value;
													var status = document.getElementById("obr_status2").value;
													var ro_id = document.getElementById("ro_obr").value;
													var podatak_id = document.getElementById("obr_podatak_id2").value;
													var ro_dio_bloka = document.getElementById("ro_dio_bloka").value;
													$.ajax({
														url: 'ajax.php?page=validacija_sve',
														type: 'POST',    
														data: {'kandidat_id':kandidat_id, 'employee_id':employee_id, 'status':status, 'ro_id':ro_id, 'podatak_id':podatak_id, 'vrsta_podatka':'obrazovanje'},
														dataType: 'html',
														success: function(data) {
															$('#modalValObrNO').modal('toggle');
														}
													});
												});
												$(".val_obr_da").click(function() {
													var kandidat_id = document.getElementById("obr_kandidat_id").value;
													var employee_id = document.getElementById("obr_employee_id").value;
													var status = document.getElementById("obr_status").value;
													var podatak_id = document.getElementById("obr_podatak_id").value;
													$.ajax({
														url: 'ajax.php?page=validacija_sve_da',
														type: 'POST',    
														data: {'kandidat_id':kandidat_id, 'employee_id':employee_id, 'status':status, 'podatak_id':podatak_id, 'vrsta_podatka':'obrazovanje'},
														dataType: 'html',
														success: function(data) {
															$('#modalValObrYES').modal('toggle');
															$('.display_obr_val_'+podatak_id+'').hide();
														}
													});
												});
											</script>
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
																	<input class="form-control materail-input monthPicker" type="text" name="kri_darum_od" id="kri_darum_od" placeholder="Datum od" autocomplete="off" required>
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
																	<input class="form-control materail-input monthPicker" type="text" name="kri_datum_do" id="kri_datum_do" placeholder="Datum do" autocomplete="off" required>
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
																		$("#kri_datum_do").val('');
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
														<div class="form-group">
															<label for="kri_opis" class="col-sm-3 control-label"><span class="text-danger"></span> Opis (Njemački):</label>
															<div class="col-sm-9">
																<div class="materail-input-block materail-input-block_success">
																	<textarea class="form-control materail-input material-textarea" name="kri_opis_de" id="kri_opis_de"></textarea>
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
											<h4 class="idk_info_title"><i class="fa fa-angle-down" aria-hidden="true"></i> Radno iskustvo
											<?php if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "18" , $getEmployeeStatus )) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){	?>
											<span class="pull-right btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive" data-toggle="modal" data-target="#workAdd"><i class="fa fa-plus" aria-hidden="true"></i> DODAJ</span>
											<!-- <a id="radnoiskustvro" href="javascript:void(0);" style="margin-right:10px;" class="pull-right btn outlined mleft_no reorder_link btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive" id="save_reorder"><i class="fa fa-refresh" aria-hidden="true"></i> Izmjeni raspored</a>						 -->
											
											<?php } ?>
											</h4>
										</div>
									</div>
									<script>
									$(document).ready(function(){
										$('#radnoiskustvro.reorder_link').on('click',function(){
											$("#radno_iskustvo .reorder-process-list").sortable({ tolerance: 'pointer' });
											$('#radnoiskustvro.reorder_link').html('<i class="fa fa-floppy-o" aria-hidden="true"></i> Sacuvaj');
											$('#radnoiskustvro.reorder_link').attr("id","save_reorder");
											$('#reorder-helper').slideDown('slow');
											$('#radno_iskustvo .proc_link').attr("href","javascript:void(0);");
											$('#radno_iskustvo .proc_link').css("cursor","move");
											$("#save_reorder").click(function( e ){
													//$(this).html('').prepend('<img src="images/refresh-animated.gif"/>');
													$("#radno_iskustvo .reorder-process-list").sortable('destroy');
													$("#reorder-helper").html( "Izmjenjujem ordere. Molimo Vas ne izlazite sa stranice dok se ne zavrsi" ).removeClass('light_box').addClass('notice notice_error');
										
													var h = [];
													$("#radno_iskustvo .list-group-items").each(function() {  h.push($(this).attr('id').substr(9));  });
													//alert(h);
													
													$.ajax({
														type: "POST",
														url: "<?php getSiteURL(); ?>kandidati?page=reorderRadnoIskustvoInfo",
														data: {ids: " " + h + "", type: "process"},
														success: function(data){
															window.location.reload();
														}
													}); 
													return false;
												
												e.preventDefault();     
											});
										});
									});			
									</script>	
									<script type="text/javascript">
										$(document).ready(function() {
											$('#radno_iskustvo').DataTable({

												responsive: true,

												//"order": [[ 0, "desc" ]],

												"bAutoWidth": false,

												"aoColumns": [
														{ "width": "2%" },
														{ "width": "15%" },
														{ "width": "15%" },
														{ "width": "15%" },
														{ "width": "13%" },
														{ "width": "8%" },
														{ "width": "8%" },
														{ "width": "8%" },
														{ "width": "5%" },
														{ "width": "5%" },
														{ "width": "6%", "bSortable": false }
													]
											});
										} );
									</script>										
									<div class="row">
										<div class="col-md-12">
											<table id="radno_iskustvo" class="display" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th class="text-center"></th>
														<th class="text-center">Pozicija</th>
														<th class="text-center">Pozicija DE</th>
														<th class="text-center">Pozicija EN</th>
														<th class="text-center">Poslodavac</th>
														<th class="text-center">Grad</th>
														<th class="text-center">Od</th>
														<th class="text-center">Do</th>
														<th class="text-center">Prikaz PP</th>
														<th class="text-center">Validacija</th>
														<th class="text-right"><?php if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "18" , $getEmployeeStatus )) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){	?>Izbriši / Uredi<?php } ?></th>
													</tr>
												</thead>
												<tbody class="reorder-process-list">
													<?php
														$select_query = $db->prepare("
																			SELECT kri_id, kri_darum_od, kri_datum_do, kri_pozicija, kri_pozicija_de, kri_pozicija_en, kri_naziv, kri_grad, kri_opis, kri_prikaz_pp, kri_aktuelno
																			FROM idk_kandidat_radno_iskustvo
																			WHERE kri_kandidat_id = :kri_kandidat_id
																			ORDER BY kri_darum_od DESC
																			");

														$select_query->execute(array(
																		':kri_kandidat_id' => $kandidat_id));
	
														$sumir = 1;
														while($select_row = $select_query->fetch()) {

															$kri_id = $select_row['kri_id'];
															$kri_darum_od = $select_row['kri_darum_od'];
															$kri_darum_od_f = date('m.Y', strtotime($kri_darum_od));
															$kri_datum_do = $select_row['kri_datum_do'];
															$kri_pozicija = $select_row['kri_pozicija'];
															$kri_pozicija_de = $select_row['kri_pozicija_de'];
															$kri_pozicija_en = $select_row['kri_pozicija_en'];
															$kri_naziv = $select_row['kri_naziv'];
															$kri_grad = $select_row['kri_grad'];
															$kri_opis = $select_row['kri_opis'];
															$kri_prikaz_pp = $select_row['kri_prikaz_pp'];
															$kri_aktuelno = $select_row['kri_aktuelno'];

															if($kri_aktuelno == 1 or $kri_datum_do == null){
																$kri_datum_do_f = "Aktuelno";
															}else{
																$kri_datum_do_f = date('m.Y', strtotime($kri_datum_do));
															}

													?>
													<tr id="radno_li_<?php echo $kri_id; ?>" class="list-group-items">
														<div style="float:none;" class="proc_link">
															<td class="text-center"><?php echo $sumir++; ?></td>
															<td class="text-center"><?php echo $kri_pozicija; ?></td>
															<td class="text-center"><?php echo $kri_pozicija_de; ?></td>
															<td class="text-center"><?php echo $kri_pozicija_en; ?></td>
															<td class="text-center"><?php echo $kri_naziv; ?></td>
															<td class="text-center"><?php echo $kri_grad; ?></td>
															<td class="text-center"><?php echo $kri_darum_od_f; ?></td>
															<td class="text-center"><?php echo $kri_datum_do_f; ?></td>
															<td class="text-center">
																<?php
																	if($kri_prikaz_pp == 1){
																		echo '<span class="glyphicon glyphicon-ok-circle" aria-hidden="true" style="color: green;"></span>';
																	} else {
																		echo '<span class="glyphicon glyphicon-remove-circle" aria-hidden="true" style="color: red;"></span>';
																	}
																?>
															</td>
															<td class="text-center">
																<div class="display_isk_val_<?php echo $kri_id; ?>">
																<?php
																	if($kandidat_status == 4){
																		$val_isk = getValidacijaObrIsk($kandidat_id, "iskustvo", $kri_id);
																		if($val_isk != 1){
																?>
																	<a href="" data-toggle="modal" data-target="#modalValIskYES" data-kandidat_id="<?php echo $kandidat_id; ?>" data-employee_id="<?php echo $logged_employee_id; ?>" data-podatak_id="<?php echo $kri_id; ?>" class="validate_isk btn material-btn material-btn_success main-container__column" title = "Validacija iskustva"><i class="fa fa-plus-circle" aria-hidden="true"></i></a>
																	<a href="" data-toggle="modal" data-target="#modalValIskNO" data-kandidat_id="<?php echo $kandidat_id; ?>" data-employee_id="<?php echo $logged_employee_id; ?>" data-podatak_id="<?php echo $kri_id; ?>" class="validate_isk btn material-btn material-btn_danger main-container__column" title = "Validacija iskustva"><i class="fa fa-minus-circle" aria-hidden="true"></i></a>
																<?php 
																		}
																	}
																?>
																</div>
															</td>
															<td class="text-right">
															<?php if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "18" , $getEmployeeStatus )) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){	?>
															<a href="" data-toggle="modal" data-target="#modalDeleteIskustvo" data-kri_id="<?php echo $kri_id; ?>" data-kandidat_check="<?php echo $kandidat_check; ?>" class="material-dropdown-menu__link idk_candidate_action_button idk_candidate_action_button_red delete_iskustvo"><i class="fa fa-times" aria-hidden="true"></i></a>
	
	
															<span class="idk_candidate_action_button idk_candidate_action_button_green"><i class="fa fa-outdent idk_edit_candidate_do edit_candidate_data" data-toggle="modal" data-target="#editAjax" data-id="<?php echo $kri_id; ?>" data-kandidat="<?php echo $kandidat_id; ?>" data-type="edit_kandidat_iskustvo" aria-hidden="true"></i></span>
															<?php } ?>
															</td>
														</div>
													</tr>
													<?php } ?>
												</tbody>
											</table>
											<script>
												$(".validate_isk").click(function() {
													var kandidat_id = $(this).data("kandidat_id");
													var employee_id = $(this).data("employee_id");
													var podatak_id = $(this).data("podatak_id");
													console.log(kandidat_id);
													console.log(employee_id);
													console.log(podatak_id);
													document.getElementById('isk_kandidat_id').value = kandidat_id;
													document.getElementById('isk_kandidat_id2').value = kandidat_id;
													document.getElementById('isk_employee_id').value = employee_id;
													document.getElementById('isk_employee_id2').value = employee_id;
													document.getElementById('isk_podatak_id').value = podatak_id;
													document.getElementById('isk_podatak_id2').value = podatak_id;
												});
												$(".delete_iskustvo").click(function(){
													var kri_id 			= $(this).data("kri_id");
													$("#kri_id").val(kri_id);
												});
											</script>
											<!-- MODAL BRISANJE ISKUSTVA -->
											<div class="modal material-modal material-modal_success fade" id="modalDeleteIskustvo">
												<div class="modal-dialog">
													<div class="modal-content material-modal__content">
														<div class="modal-header material-modal__header">
															<button class="close material-modal__close" data-dismiss="modal">&times;</button>
															<h4 class="modal-title material-modal__title">Brisanje iskustva</h4>
														</div>
														<div class="modal-body material-modal__body">
														<form action="<?php getSiteURL(); ?>kandidati?page=delete_kriskustvo" method="POST">
																<div class="form-group">
																	<div class="col-md-offset-2 col-sm-8">
																		Potvrdom brišete odabrano radno iskustvo.
																	</div>
																</div>
															</div>
															<input type="hidden" name="kri_id" id="kri_id">
															<input type="hidden" name="kandidat_check" id="kandidat_check" value="<?php echo $kandidat_check; ?>">
															<input type="hidden" name="kandidat_id" id="kandidat_id" value="<?php echo $kandidat_id; ?>">
															<div class="modal-footer material-modal__footer">
																<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
																<button type="submit" class="btn btn-primary material-btn material-btn_success val_isk_da">Potvrdi</button>
															</div>
														</form>
													</div>
												</div>
											</div>
											<!-- MODAL VALIDACIJA OBRAZOVANJE DA -->
											<div class="modal material-modal material-modal_success fade" id="modalValIskYES">
												<div class="modal-dialog">
													<div class="modal-content material-modal__content">
														<div class="modal-header material-modal__header">
															<button class="close material-modal__close" data-dismiss="modal">&times;</button>
															<h4 class="modal-title material-modal__title">Validacija iskustva</h4>
														</div>
														<div class="modal-body material-modal__body">
															<div class="form-group">
																<div class="col-md-offset-2 col-sm-8">
																	Potvrdom označavate da su podaci uredu.
																</div>
															</div>
														</div>
														<input type="hidden" name="isk_kandidat_id" id="isk_kandidat_id">
														<input type="hidden" name="isk_employee_id" id="isk_employee_id">
														<input type="hidden" name="isk_podatak_id" id="isk_podatak_id">
														<input type="hidden" name="isk_status" id="isk_status" value="1">
														<div class="modal-footer material-modal__footer">
															<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
															<button type="submit" class="btn btn-primary material-btn material-btn_success val_isk_da">Potvrdi</button>
														</div>
													</div>
												</div>
											</div>
											<!-- MODAL VALIDACIJA OBRAZOVANJE NE-->
											<div class="modal material-modal material-modal_danger fade" id="modalValIskNO">
												<div class="modal-dialog">
													<div class="modal-content material-modal__content">
														<div class="modal-header material-modal__header">
															<button class="close material-modal__close" data-dismiss="modal">&times;</button>
															<h4 class="modal-title material-modal__title">Validacija iskustva</h4>
														</div>
														<div class="modal-body material-modal__body">
															<div class="form-group">
																<div class="col-md-offset-2 col-sm-8">
																	<p style = "margin: 20px 0px 20px 0px;">Odaberite razlog zašto podaci nisu uredu</p>
																</div>
															</div>
															<div class="form-group">
																<div class="col-md-offset-2 col-sm-8">
																	<div class = "" style = "margin-bottom: 80px;">
																		<select class="selectpicker" id="ro_isk" name="ro_isk" required>
																			<?php
																				$query_razlozi = $db->prepare("
																								SELECT ro_id, ro_naziv, ro_dio_bloka
																								FROM idk_razlozi_odbijanja
																								WHERE ro_blok = 'blok_isk'
																								");
						
																				$query_razlozi->execute();
						
																				while($rowR = $query_razlozi->fetch()){
																					$ro_id = $rowR['ro_id'];
																					$ro_naziv = $rowR['ro_naziv'];
																			?>
																			<option value="<?php echo $ro_id; ?>"><?php echo $ro_naziv; ?></option>
																			<?php 
																				}
																			?>
																		</select>
																	</div>
																</div>
															</div>
														</div>
														<input type="hidden" name="isk_kandidat_id" id="isk_kandidat_id2">
														<input type="hidden" name="isk_employee_id" id="isk_employee_id2">
														<input type="hidden" name="isk_podatak_id" id="isk_podatak_id2">
														<input type="hidden" name="isk_status" id="isk_status2" value="2">
														<div class="modal-footer material-modal__footer">
															<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
															<button type="submit" class="btn btn-primary material-btn material-btn_danger val_isk_ne">Potvrdi</button>
														</div>
													</div>
												</div>
											</div>
											<script>
												$(".val_isk_ne").click(function() {
													var kandidat_id = document.getElementById("isk_kandidat_id2").value;
													var employee_id = document.getElementById("isk_employee_id2").value;
													var status = document.getElementById("isk_status2").value;
													var ro_id = $('select[name="ro_isk"]').val();
													console.log(kandidat_id);
													console.log(ro_id);
													var podatak_id = document.getElementById("isk_podatak_id2").value;
													$.ajax({
														url: 'ajax.php?page=validacija_sve',
														type: 'POST',    
														data: {'kandidat_id':kandidat_id, 'employee_id':employee_id, 'status':status, 'ro_id':ro_id, 'podatak_id':podatak_id, 'vrsta_podatka':'iskustvo'},
														dataType: 'html',
														success: function(data) {
															$('#modalValIskNO').modal('toggle');
														}
													});
												});
												$(".val_isk_da").click(function() {
													var kandidat_id = document.getElementById("isk_kandidat_id").value;
													var employee_id = document.getElementById("isk_employee_id").value;
													var status = document.getElementById("isk_status").value;
													var podatak_id = document.getElementById("isk_podatak_id").value;
													$.ajax({
														url: 'ajax.php?page=validacija_sve_da',
														type: 'POST',    
														data: {'kandidat_id':kandidat_id, 'employee_id':employee_id, 'status':status, 'podatak_id':podatak_id, 'vrsta_podatka':'iskustvo'},
														dataType: 'html',
														success: function(data) {
															$('#modalValIskYES').modal('toggle');
															$('.display_isk_val_'+podatak_id+'').hide();
														}
													});
												});
											</script>
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
											<h4 class="idk_info_title"><i class="fa fa-angle-down" aria-hidden="true"></i> Vještine
											<?php if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "18" , $getEmployeeStatus )) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){	?>
											<span class="pull-right btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive" data-toggle="modal" data-target="#skillsAdd"><i class="fa fa-plus" aria-hidden="true"></i> DODAJ</span>
											<?php } ?>
											</h4>
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
														<th class="text-right"><?php if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "18" , $getEmployeeStatus )) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){	?>Izbriši / Uredi<?php } ?></th>
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
														<?php if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "18" , $getEmployeeStatus )) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){	?>
														<a href="<?php getSiteURL(); ?>kandidati?page=delete_kandidat_vjestina&id=<?php echo $kv_id; ?>&kandidatid=<?php echo $kandidat_id;?>" class="material-dropdown-menu__link idk_candidate_action_button idk_candidate_action_button_red" disabled><i class="fa fa-times" aria-hidden="true"></i></a>


														<span class="idk_candidate_action_button idk_candidate_action_button_green"><i class="fa fa-outdent idk_edit_candidate_do edit_candidate_data" data-toggle="modal" data-target="#editAjax" data-id="<?php echo $kv_id; ?>" data-kandidat="<?php echo $kandidat_id; ?>" data-type="edit_kandidat_vjestine" aria-hidden="true"></i></span>
														<?php } ?>
														</td>
													</tr>
													<?php } ?>
												</tbody>
											</table>
										</div>
									</div>
									<br/>
									<br/>
								</div>

								<div class="tab-pane fade" id="notes">
									<div class="panel-group material-accordion material-accordion_primary" id="accordion1">
										<?php

											$year_query = $db->prepare("
																SELECT note_datetime_year
																FROM
																(	
																	SELECT YEAR (note_datetime) AS note_datetime_year
																	FROM idk_notes
																	WHERE note_dataid = :note_dataid AND note_group = :note_group
																	GROUP BY YEAR (note_datetime)
																
																UNION ALL 
																
																	SELECT YEAR (krr_date) as note_datetime_year
																	FROM idk_kandidati_reject_reasons 
																	JOIN idk_reject_reasons rr on krr_reason_id = rr.rr_id
																	WHERE krr_candidate_id = :note_dataid AND krr_source = 1
																	GROUP BY krr_date
																)
																AS combined_years
																GROUP BY combined_years.note_datetime_year
																ORDER BY combined_years.note_datetime_year DESC");

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

													if((in_array( "2" , $getEmployeeStatus )) OR (in_array( "18" , $getEmployeeStatus )) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){
														$uslov_saradnik = "AND note_employeeid = ".$logged_employee_id."";
													}else{
														$uslov_saradnik = "";
													}
														$notes_query = $db->prepare("
																		(
																			SELECT note_id, note_datetime, note_txt, employee_firstname, employee_lastname 
																			FROM idk_notes 
																			INNER JOIN idk_employees ON idk_notes.note_employeeid = idk_employees.employee_id 
																			WHERE YEAR(note_datetime) = :note_datetime_year AND note_dataid = :note_dataid AND note_group = :note_group
																		) 
																		UNION ALL 
																		(
																			SELECT krr_id as note_id, krr_date as note_datetime, CONCAT('ODUSTAO. <br>Razlog: ',QUOTE(rr.rr_name_bs),'. <br>Opis: ', CASE WHEN krr_description IS NULL THEN '' ELSE krr_description END) as note_txt, employee_firstname, employee_lastname 
																			FROM idk_kandidati_reject_reasons 
																			INNER JOIN idk_employees ON krr_done_by = idk_employees.employee_id 
																			JOIN idk_reject_reasons rr on krr_reason_id = rr.rr_id
																			WHERE krr_candidate_id = :note_dataid AND krr_source = 1 AND YEAR(krr_date) = :note_datetime_year
																		)
																		ORDER BY note_datetime DESC
																		
																		
																		");

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
															$note_files_imp = getFileForCandidateNoteR($note_id);

															if($employee_firstname == "Skladiste"){
																$employee_txt = "Automatska bilješka pri prijavi";
															}else{
																$employee_txt = $employee_firstname . " " . $employee_lastname;
															}

													?>
													<div class="row">
														<div class="col-sm-3">
															<p><i class="fa fa-calendar text-primary" aria-hidden="true"></i> <?php echo $note_date; ?> | <i class="fa fa-clock-o text-primary" aria-hidden="true"></i> <?php echo $note_time; ?></p>
															<p><i class="fa fa-user text-primary" aria-hidden="true"></i> <?php echo $employee_txt; ?> </p>
														</div>
														<div class="col-sm-9">
															<p><?php echo $note_txt; ?></p>
															<?php 
																/*
																	NOTE FILES - START 
																	*/
																		if ( $note_files_imp != null ) {

																			$note_files_exp = explode(",", $note_files_imp);

																			?>
																				<hr>
																				<div class = "row">
																					<?php 
																						foreach($note_files_exp AS $note_file_value){ 

																							$note_file_icon = "";

																							if (strpos($note_file_value, '.jpg') !== false OR strpos($note_file_value, '.png') !== false){
																								$note_file_icon = '<i class="fa fa-file-image-o fa-2x" aria-hidden="true"></i>';
																							}else if(strpos($note_file_value, '.pdf') !== false){
																								$note_file_icon = '<i class="fa fa-file-pdf-o fa-2x" aria-hidden="true"></i>';
																							}else if(strpos($note_file_value, '.doc') !== false OR strpos($note_file_value, '.docx') !== false){
																								$note_file_icon = '<i class="fa fa-file-word-o fa-2x" aria-hidden="true"></i>';
																							}else if(strpos($note_file_value, '.xls') !== false OR strpos($note_file_value, '.xlsx') !== false OR strpos($note_file_value, '.csv') !== false){
																								$note_file_icon = '<i class="fa fa-file-excel-o fa-2x" aria-hidden="true"></i>';
																							}else if(strpos($note_file_value, '.txt') !== false ){
																								$note_file_icon = '<i class="fa fa-file-text-o fa-2x" aria-hidden="true"></i>';
																							}else{
																								$note_file_icon = '<i class="fa fa-file-powerpoint-o fa-2x" aria-hidden="true"></i>';
																							}

																							?>
																								<div class = "col-sm-1">
																									<a href="<?php getSiteURL(); ?>files/prilozi_kandidati/<?php echo $note_file_value; ?>" class="btn material-btn material-btn_success main-container__column" style = "padding: 5px;" target="_BLANK"><?php echo $note_file_icon; ?></a>
																								</div>
																							<?php 
																						}
																					?>
																				</div>
																			<?php 
																		}
																	/*
																	NOTE FILES - END 
																*/
															?>
														</div>
														<?php 
															if (1 == 0){
																//Jer je uklonjeno dugme
														?>
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
														<?php 
															}
														?>
													</div>
													<hr />
													<?php } ?>
												</div>
											</div>
										</div>
										<?php } ?>
									</div>
								</div>
								
								<div class="tab-pane fade" id="projekti">
								<div class="row">
										<div class="col-xs-12 text-right idk_margin_top10">
											<?php if(getCandidateStatusPrijaveRedoslijed($kandidat_id) < 5){
												if(!checkCandidateFreezed($kandidat_id)){
													if(!checkCandidateCooling($kandidat_id)){
												?>
													<a href="#" data-toggle="modal" data-target="#dodajProjModal" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj u projekat</span></a>
												<?php
													}
												} 
											} 
											?>
											<!-- Modal dodaj u projekat -->
											<div class="modal material-modal material-modal_primary fade text-left" id="dodajProjModal">
												<div class="modal-dialog ">
													<div class="modal-content material-modal__content">
														<div class="modal-header material-modal__header">
															<button class="close material-modal__close" data-dismiss="modal">&times;</button>
															<h4 class="modal-title material-modal__title">Dodaj kandidata u projekat</h4>
														</div>
														<div class="modal-body material-modal__body">
															<form action="do.php?form=add_kandidat_projekat" method="post" role="form" class="form-horizontal" id="prebaci_u_project_forma">
																<input type="hidden" name="kandidat_id" value="<?php echo $kandidat_id;  ?>" />
																<?php echo getNalogListByCandidateId($kandidat_id); ?>
																<script>
																	function promjena_pozicije(){
																		var nalog = document.getElementById("lg_nalog").value;
																		var before_data = '<label for="lg_project" class="col-sm-3 control-label"><span class="text-danger">*</span> Projekat:</label><div class="col-sm-9" id="divura"><select  class="selectpicker" id="lg_project" data-live-search="true" name="lg_project">';
																		var after_data = '</select></div>';
																		$.ajax({
																			url: 'ajax_data.php?page=get_Project_Nalog',
																			type: 'POST',    
																			data: {'nalog':nalog},
																			dataType: 'html',
																			success: function(data) {
																				$("#project_pick").fadeOut(200, function(){
																					$("#project_pick").empty().append(before_data + data + after_data).fadeIn(300);
																					$("#lg_project").selectpicker();
																					$("#lg_project").change( function() {
																						let project = $("#lg_project option:selected").text();
																						let status_prijave = parseInt('<?php echo getCandidateStatusPrijaveByCandidateId($kandidat_id); ?>'); 
																						if(project.includes("- Odustao") && [7,8,9,12,15,18,21,24,27,10,4].includes(status_prijave))
																						{
																							$("#dodaj_kandidata_u_projekat").prop( "disabled", true );
																							$("#alertOdustao").css("display", "block");
																						}
																						else
																						{
																							$("#dodaj_kandidata_u_projekat").prop( "disabled", false );
																							$("#alertOdustao").css("display", "none");
																						}
																					});
																				})
																			}
																		});
																	}
																	$("#lg_project").change( function() {
																		let project = $("#lg_project option:selected").text();
																		let status_prijave = parseInt('<?php echo getCandidateStatusPrijaveByCandidateId($kandidat_id); ?>'); 
																		if(project.includes("- Odustao") && [7,8,9,12,15,18,21,24,27,10,4].includes(status_prijave))
																		{
																			$("#dodaj_kandidata_u_projekat").prop( "disabled", true );
																			$("#alertOdustao").css("display", "block");
																		}
																		else
																		{
																			$("#dodaj_kandidata_u_projekat").prop( "disabled", false );
																			$("#alertOdustao").css("display", "none");
																		}
																	});
																</script>
															<div class="form-group" id="alertOdustao" style="display: none;">
																<div class="col-sm-12">
																	<div style = "margin-top: 10px; margin-bottom: 10px; text-align: center;" class="alert alert-danger">
																		Nije moguće kandidata prebaciti u projekat odustao!
																	</div>	
																</div>
															</div>
														</div>
														<div class="modal-footer material-modal__footer">
																<button id = "dodaj_kandidata_u_projekat" type="submit" class="btn btn-primary material-btn material-btn_primary">Dodaj</button>
															</form>
														</div>
														<script>
															// Sve zakomentarisano i prebaceno na klasicni request forme
															// $('#dodaj_kandidata_u_projekat').on('click', function(e){

															// 	let kandidat_id = <?php echo $kandidat_id; ?>;
															// 	let lg_nalog = $("#lg_nalog").val();
															// 	let lg_project = $("#lg_project").val();
															// 	let glossa = $("#glossa_kurs").val();
															// 	let fname = "";
															// 	let lname = "";
															// 	let email = "";
															// 	let phone = "";
															// 	let crm_id = "";

															// 	fetch(`ajax_data.php?page=glossa_api_info&id=${kandidat_id}`)
															// 	.then((res) => res.json())
															// 	.then((data) => {
															// 		if($("#glossa_kurs").is(":checked"))
															// 		{
															// 			fname = data.fname;
															// 			lname = data.lname;
															// 			email = data.email;
															// 			phone = data.phone;
															// 			crm_id = data.crm_id;
																		
															// 			if(phone === null)
															// 			{
															// 				phone = "1111111111";
															// 			}
															// 			fetch("do.php?form=add_kandidat_projekat", {
															// 				method: "POST",
															// 				headers: {
															// 					"Content-type": "application/x-www-form-urlencoded"
															// 				},
															// 				body: new URLSearchParams({
															// 					"kandidat_id": kandidat_id,
															// 					"lg_nalog": lg_nalog,
															// 					"lg_project": lg_project,
															// 					"glossa_kurs": glossa
															// 				})
															// 			});
															// 			var myHeaders = new Headers();
															// 			myHeaders.append("Authorization", "Bearer 8d81150a-8cd6-4a8a-9279-d4a4a45ccdea");
															// 			myHeaders.append("Content-Type", "application/json");
															// 			myHeaders.append("Cookie", "PH_HPXY_CHECK=s1");

															// 			var raw = JSON.stringify({
															// 				"name": fname,
															// 				"lastname": lname,
															// 				"email": email,
															// 				"phone": phone,
															// 				"crm_id": crm_id
															// 			});

															// 			var requestOptions = {
															// 			method: 'POST',
															// 			headers: myHeaders,
															// 			body: raw,
															// 			redirect: 'follow',
															// 			};

															// 			fetch("https://glossa-crm.com/api/Person/Create", requestOptions)
															// 			.then(response => response.json())
															// 			.then((result) => {
															// 				let response_msg = result.message;
															// 				let response_status = result.code;
															// 				fetch("do.php?form=insert_glossa_lead", {
															// 					method: "POST",
															// 					headers: {
															// 						"Content-type": "application/x-www-form-urlencoded"
															// 					},
															// 					body: new URLSearchParams({
															// 						"kandidat_id": kandidat_id,
															// 						"response_msg": response_msg,
															// 						"response_code": response_status,
															// 						"glossa_kurs": glossa
															// 					})
															// 				});
															// 			})
															// 			.catch(error => console.log('error', error));
															// 		}
															// 		else 
															// 		{
															// 			fetch("do.php?form=add_kandidat_projekat", {
															// 				method: "POST",
															// 				headers: {
															// 					"Content-type": "application/x-www-form-urlencoded"
															// 				},
															// 				body: new URLSearchParams({
															// 					"kandidat_id": kandidat_id,
															// 					"lg_nalog": lg_nalog,
															// 					"lg_project": lg_project,
															// 					"glossa_kurs": glossa
															// 				})
															// 			});
															// 		}
															// 	});
															// });
														</script>
													</div>
												</div>
											</div>
										
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
																			{ "width": "20%" },
																			{ "width": "10%", "bSortable": false }
																		]
																});
															} );
														</script>
														<table id="idk_table" class="display" cellspacing="0" width="100%">
															<thead>
																<tr>
																	<th>Naziv</th>
																	<th>Status projekta</th>
																	<th>Nalog</th>
																	<th class="text-center">Kreirano</th>
																	<th></th>
																</tr>
															</thead>
															<tbody>
																<?php
																	$query = $db->prepare("
																					SELECT pk_projectid, pk_kandidatid, proj.project_name, proj.project_datetime, proj.project_status, proj.project_nalogid, k.kandidat_nalog_id
																					FROM idk_project_kandidati
																					INNER JOIN idk_projects proj ON pk_projectid = proj.project_id
																					JOIN idk_kandidati k
																					ON k.kandidat_id = pk_kandidatid
																					WHERE pk_kandidatid = :pk_kandidatid");

																	$query->execute(array(
																		'pk_kandidatid' => $kandidat_id));

																	while($row = $query->fetch()){

																		$project_id = $row['pk_projectid'];
																		$project_name = $row['project_name'];
																		$project_datetime = date('d.m.Y. - H:i', strtotime($row['project_datetime']));
																		$project_status = $row['project_status'];
																		$nalog = $row['project_nalogid'];
																		$kandidat_nalog_id = $row['kandidat_nalog_id'];
																		$flag_enable_delete = false;
																		if($project_status == 1){
																			$project_status_txt = '<span class="label label-success">Aktivan</span>';
																		}elseif($project_status == 0){
																			$project_status_txt = '<span class="label label-danger">Arhiviran</span>';
																		}
																		else $project_status_txt = "nedefinsan";
																		
																		$query_kandidat_nalog_info = $db->prepare("
																				SELECT nalog_naziv
																				FROM idk_nalozi
																				WHERE nalog_id = :nalog_id");
																				
																		$query_kandidat_nalog_info->execute(array(
																						':nalog_id' => $nalog
																					));
																		$row_nalog = $query_kandidat_nalog_info->fetch();
																		$naziv_naloga = $row_nalog['nalog_naziv'];
																		
																		?>
																		<tr>
																			<td><a href="<?php getSiteURL(); ?>projects?page=open&id=<?php echo $project_id; ?>"><?php echo $project_name; ?></a></td>
																			<td class="text-center"><?php echo $project_status_txt; ?></td>
																			<td class="text-center"><a href="<?php getSiteURL(); ?>nalozi?page=open&id=<?php echo $nalog; ?>"><?php echo $naziv_naloga; ?></a></td>
																			<td class="text-center"><?php echo $project_datetime; ?></td>
																			<?php
																			if(   (isReservedByProject($project_name) AND  $nalog != 44 AND is_null($kandidat_nalog_id))
																				 OR  ($kandidat_nalog_id == $nalog AND $nalog != 44) 
																				//AND $kandidat_nalog_id != null AND $kandidat_nalog_id == $nalog
																			){
																				echo '<td></td>';
																			}
																			else{
																				?>
																				<td class="text-center"><a href="<?php getSiteURL(); ?>do.php?form=remove_from_proj&project=<?php echo $project_id; ?>&id=<?php echo $kandidat_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-trash-o" aria-hidden="true"></i></a></td>																				
																				<?php
																			}
																			?>
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
														{ "width": "25%" },
														{ "width": "25%" },
														{ "width": "10%", "bSortable": false },
														{ "width": "10%", "bSortable": false },
														{ "width": "10%", "bSortable": false },
														{ "width": "10%", "bSortable": false }
													]
											});
										} );
									</script>
									<div class="row" style="margin-bottom: 50px;">
										<div class="col-12">
											<table id="idk_table_documents" class="display" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th>Datum</th>
														<th>Naziv</th>
														<th>Opis</th>
														<th class="text-center">Dokument</th>
														<th class="text-center">Rotacija</th>
														<th class="text-center">Validacija</th>
														<th class="text-center">Akcije</th>
													</tr>
												</thead>
												<tbody>
													<?php
														$query_doc = $db->prepare
														("	SELECT document_id, document_name, document_desc, document_file, document_icon, document_datetime, 1 as document_origin
															FROM idk_documents
															WHERE document_group = :document_group AND document_dataid = :document_dataid
															UNION
															SELECT kj_id, CONCAT('Certifikat za jezik ', kj_slusanje) as document_name, 'Certifikat za njemački jezik' as document_desc, cvl_certificate_path as document_file, SUBSTRING_INDEX(cvl_certificate_path, '.', -1) as document_icon, cvl_certificate_upload_date as document_datetime, 2 as document_origin
															FROM idk_kandidat_jezici
															JOIN idk_candidate_verified_languages ON cvl_id = kj_id
															WHERE kj_kandidatid = :document_dataid AND kj_naziv = 'Njemački' AND cvl_certificate_path is not null
														");

														$query_doc->execute(array(
																	':document_group' => 2,
																	':document_dataid' => $kandidat_id));

														while($row_doc = $query_doc->fetch()){

															$document_id = $row_doc['document_id'];
															$document_name = $row_doc['document_name'];
															$document_desc = $row_doc['document_desc'];
															$document_file = $row_doc['document_file'];
															$document_origin = $row_doc['document_origin'];
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
															if($document_origin == 1){
																$document_path = "files/kandidati_doc/";
															}elseif($document_origin == 2){
																$document_path = "";
															}
													?>
													<tr>
														<td class="text-center"><?php echo $document_datetime; ?></td>
														<td style = "word-break: break-all;"><?php echo $document_name; ?></td>
														<td style = "word-break: break-all;"><?php echo $document_desc; ?></td>
														<td class="text-center">
															<a href="<?php echo getSiteURL().$document_path.$document_file; ?>" class="btn material-btn material-btn_success main-container__column" target="_BLANK"><?php echo $document_icon; ?></a>
														</td>
														<td class="text-center">
															<?php if($row_doc['document_icon'] == "jpg"){ ?>
															<a href="<?php getSiteUrl(); ?>do.php?form=rotate_image&kand_id=<?php echo $kandidat_id; ?>&image=<?php echo $document_file; ?>&rotate=1" class="btn material-btn material-btn_success main-container__column"><i class="fa fa-undo" aria-hidden="true"></i></a>
															<a href="<?php getSiteUrl(); ?>do.php?form=rotate_image&kand_id=<?php echo $kandidat_id; ?>&image=<?php echo $document_file; ?>&rotate=2" class="btn material-btn material-btn_success main-container__column"><i class="fa fa-repeat" aria-hidden="true"></i></a>
															<?php }else{ ?>
															<a disabled class="btn material-btn material-btn_success main-container__column"><i class="fa fa-undo" aria-hidden="true"></i></a>
															<a disabled class="btn material-btn material-btn_success main-container__column"><i class="fa fa-repeat" aria-hidden="true"></i></a>
															<?php } ?>
														</td>
														<td class="text-center">
															<div class="display_doc_val_<?php echo $document_id; ?>">
														<?php
															if($kandidat_status == 4 or $kandidat_status == 5){
																$val_docs = getValidacijaObrIsk($kandidat_id, "dokument", $document_id);
																if($val_docs != 1){
																	?>
																<a href="" data-toggle="modal" data-target="#modalValDocYES" data-kandidat_id="<?php echo $kandidat_id; ?>" data-employee_id="<?php echo $logged_employee_id; ?>" data-podatak_id="<?php echo $document_id; ?>" class="validate_docs btn material-btn material-btn_success main-container__column" title = "Validan dokument. "><i class="fa fa-plus-circle" aria-hidden="true"></i></a>
																<a href="" data-toggle="modal" data-target="#modalValiDocNO" data-kandidat_id="<?php echo $kandidat_id; ?>" data-employee_id="<?php echo $logged_employee_id; ?>" data-podatak_id="<?php echo $document_id; ?>" class="validate_docs btn material-btn material-btn_danger main-container__column" title = "Dokument nije validan. "><i class="fa fa-minus-circle" aria-hidden="true"></i></a>
																	<?php 
																}
															}
														?>
															</div>
														</td>
														<td class="text-center">
															<!-- Opcije unutar liste START-->
																<?php if($document_origin == 1){ 
																	//Ne dozvoljamo da se rade akcije na certifikatu jer njegovo porijeklo nije iz iste tabele kao i ostali dokumenti
																	?>
																	<div class="btn-group material-btn-group">
																		<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_success idk_btn_table" data-toggle="dropdown"><i class="fa fa-wrench" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
																		<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_success idk_dropdown_table" role="menu" 
																			style = "    top: 32px;
																			min-width: 70px !important;
																			left: -2px;"
																		>
																			<li>
																				<a href="#" data-doc_id="<?php echo $document_id; ?>" data-doc_desc="<?php echo $document_desc; ?>" data-toggle="modal" data-target="#editDescDoc" class="editDescDoc material-dropdown-menu__link" title = "Uredi opis dokumenta.">
																					<i class="fa fa-list" aria-hidden="true">
																					</i>
																				</a>
																			</li>
																			<?php if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "18" , $getEmployeeStatus )) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){	?>
																			<li>
																				<a href="#" data="<?php getSiteURL(); ?>kandidati?page=del_doc&id=<?php echo $document_id; ?>" data-toggle="modal" data-target="#deleteDocModal" class="delete_doc material-dropdown-menu__link" title = "Obriši dokument.">
																					<i class="fa fa-trash-o fa-lg" aria-hidden="true">
																					</i>
																				</a>
																			</li>
																			<?php } ?>
																		</ul>
																	</div>
																<?php } ?>
															<!-- Opcije unutar liste END-->
														</td>
													</tr>
													<?php } ?>
													<script>
														$(".validate_docs").click(function() {
															var kandidat_id = $(this).data("kandidat_id");
															var employee_id = $(this).data("employee_id");
															var podatak_id = $(this).data("podatak_id");
															// console.log(kandidat_id);
															document.getElementById('doc_kandidat_id').value = kandidat_id;
															document.getElementById('doc_kandidat_id2').value = kandidat_id;
															document.getElementById('doc_employee_id').value = employee_id;
															document.getElementById('doc_employee_id2').value = employee_id;
															document.getElementById('doc_podatak_id').value = podatak_id;
															document.getElementById('doc_podatak_id2').value = podatak_id;
														});
													</script>
													<!-- MODAL VALIDACIJA YES START -->
														<div class="modal material-modal material-modal_success fade" id="modalValDocYES">
															<div class="modal-dialog">
																<div class="modal-content material-modal__content">
																	<div class="modal-header material-modal__header">
																		<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																		<h4 class="modal-title material-modal__title">Validan dokument</h4>
																	</div>
																	<div class="modal-body material-modal__body">
																		<div class="form-group">
																			<div class="col-md-offset-2 col-sm-8">
																				Potvrdom označavate ovaj dokument validnim.
																			</div>
																		</div>
																	</div>
																	<input type="hidden" name="doc_kandidat_id" id="doc_kandidat_id">
																	<input type="hidden" name="doc_employee_id" id="doc_employee_id">
																	<input type="hidden" name="doc_podatak_id" id="doc_podatak_id">
																	<input type="hidden" name="doc_status" id="doc_status" value="1">
																	<div class="modal-footer material-modal__footer">
																		<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
																		<button type="submit" class="btn btn-primary material-btn material-btn_success val_doc_da">Potvrdi</button>
																	</div>
																</div>
															</div>
														</div>
													<!-- MODAL VALIDACIJA YES END -->
													
													<!-- MODAL VALIDACIJA NO START -->
														<div class="modal material-modal material-modal_danger fade" id="modalValiDocNO">
															<div class="modal-dialog">
																<div class="modal-content material-modal__content">
																	<div class="modal-header material-modal__header">
																		<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																		<h4 class="modal-title material-modal__title">Dokument nije validan.</h4>
																	</div>
																	<div class="modal-body material-modal__body">
																		<div class="form-group">
																			<div class="col-md-offset-2 col-sm-8">
																				<p style = "margin: 20px 0px 20px 0px;">Odaberite razlog zbog kojeg smatrate da dokument nije validan.</p>
																			</div>
																		</div>
																		<div class="form-group">
																			<div class="col-md-offset-2 col-sm-8">
																				<div class = "" style = "margin-bottom: 80px;">
																					<select class="selectpicker" id="ro_docs" name="ro_docs" required>
																						<?php
																						$query_razlozi = $db->prepare("
																											SELECT ro_id, ro_naziv, ro_dio_bloka
																											FROM idk_razlozi_odbijanja
																											WHERE ro_blok = 'blok_docs'
																											");
									
																							$query_razlozi->execute();
									
																							while($rowR = $query_razlozi->fetch()){
																								$ro_id = $rowR['ro_id'];
																								$ro_naziv = $rowR['ro_naziv'];
																						?>
																						<option value="<?php echo $ro_id; ?>"><?php echo $ro_naziv; ?></option>
																						<?php 
																							}
																						?>
																					</select>
																				</div>
																			</div>
																		</div>
																	</div>
																	<input type="hidden" name="doc_kandidat_id" id="doc_kandidat_id2">
																	<input type="hidden" name="doc_employee_id" id="doc_employee_id2">
																	<input type="hidden" name="doc_podatak_id" id="doc_podatak_id2">
																	<input type="hidden" name="doc_status" id="doc_status2" value="2">
																	<div class="modal-footer material-modal__footer">
																		<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
																		<button type="submit" class="btn btn-primary material-btn material-btn_danger val_doc_ne">Potvrdi</button>
																	</div>
																</div>
															</div>
														</div>
													<!-- MODAL VALIDACIJA NO END -->
													<script>
														$(".val_doc_ne").click(function() {
															var kandidat_id = document.getElementById("doc_kandidat_id2").value;
															var employee_id = document.getElementById("doc_employee_id2").value;
															var status = document.getElementById("doc_status2").value;
															var ro_id = document.getElementById("ro_docs").value;
															var podatak_id = document.getElementById("doc_podatak_id2").value;
															$.ajax({
																url: 'ajax.php?page=validacija_sve',
																type: 'POST',    
																data: {'kandidat_id':kandidat_id, 'employee_id':employee_id, 'status':status, 'ro_id':ro_id, 'podatak_id':podatak_id, 'vrsta_podatka':'dokument'},
																dataType: 'html',
																success: function(data) {
																	$('#modalValiDocNO').modal('toggle');
																}
															});
															
														});
														$(".val_doc_da").click(function() {
															var kandidat_id = document.getElementById("doc_kandidat_id").value;
															var employee_id = document.getElementById("doc_employee_id").value;
															var status = document.getElementById("doc_status").value;
															var podatak_id = document.getElementById("doc_podatak_id").value;
															$.ajax({
																url: 'ajax.php?page=validacija_sve_da',
																type: 'POST',    
																data: {'kandidat_id':kandidat_id, 'employee_id':employee_id, 'status':status, 'podatak_id':podatak_id, 'vrsta_podatka':'dokument'},
																dataType: 'html',
																success: function(data) {
																	$('#modalValDocYES').modal('toggle');
																	$('.display_doc_val_'+podatak_id+'').hide();
																}
															});
														});
													</script>
													<script>
														$(".editDescDoc").click(function () {
															var doc_id = $(this).data("doc_id");
															var doc_desc = $(this).data("doc_desc");
															console.log(doc_id);
															document.getElementById("edit_doc_id").value = doc_id;
															document.getElementById("document_desc_edit").value = doc_desc;
														});
													</script>
													<!-- Modal -->
													<div class="modal material-modal material-modal_danger fade" id="editDescDoc">
														<div class="modal-dialog">
															<div class="modal-content material-modal__content">
																<div class="modal-header material-modal__header">
																	<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																	<h4 class="modal-title material-modal__title">Uredi opis dokumenta</h4>
																</div>
																<div class="modal-body material-modal__body">
																	<form action="<?php getSiteURL(); ?>do.php?form=edit_kandidat_doc" enctype="multipart/form-data" method="post" role="form" class="form-horizontal">

																	<input type="hidden" name="kandidat_id" value="<?php echo $kandidat_id; ?>" />
																	<input type="hidden" name="document_id" id="edit_doc_id" />
																	<div class="form-group">
																		<div class="col-md-offset-2 col-sm-8">
																			<div class="form-group materail-input-block materail-input-block_success">
																				<input type="text" class="form-control materail-input" name="document_desc" id="document_desc_edit" >
																				<span class="materail-input-block__line"></span>
																			</div>
																		</div>
																	</div>
																</div>
																<div class="modal-footer material-modal__footer">
																	<button type="submit" class="btn btn-primary material-btn material-btn_primary">UREDI</button>
																</form>
																</div>
															</div>
														</div>
													</div>
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
									<hr />
									<hr />
									<script>
										$(document).ready(function() {
											$('#idk_table_contracts').DataTable({

												responsive: true,

												"order": [[ 0, "asc" ]],

												 "bAutoWidth": false,
											});
										} );
									</script>
									<?php
										$get_contract = $db->prepare("
																SELECT
																	kc_file_name,
																	kc_upload_time,
																	kc_signed
																FROM
																	idk_kandidati_contracts
																WHERE
																	kc_candidate_id = :candidate_id
																AND
																	kc_visibility_status = 2 
															");
										$get_contract->execute(array(
											":candidate_id" => $kandidat_id
										));
									?>
									<div style="display: flex; justify-content: center;">
										<h3>Ugovori:</h3>
									</div>
									<div class="row" style="margin-top: 50px;">
										<div class="col-12">
											<table id="idk_table_contracts">
												<thead>
													<tr>
														<th class="text-center">Naziv</th>
														<th class="text-center">Vrijeme uploada</th>
														<th class="text-center">Opis</th>
														<th class="text-center">Dokument</th>
													</tr>
												</thead>
												<tbody>
													<?php
														while($contract_result = $get_contract->fetch()){
															$file_name 		= $contract_result["kc_file_name"];
															$upload_time 	= date("d.m.Y", strtotime($contract_result["kc_upload_time"]));

															if($contract_result["kc_signed"] == 0){
																$contract_desc = "Ugovor nije potpisan od strane kandidata";
															}
															elseif($contract_result["kc_signed"] == 1){
																$contract_desc = "Ugovor potpisan od strane kandidata";
															}
													?>
													<tr>
														<td class="text-center"><?php echo $file_name; ?></td>
														<td class="text-center"><?php echo $upload_time; ?></td>
														<td class="text-center"><?php echo $contract_desc; ?></td>
														<td class="text-center">
															<a href="<?php getSiteURL(); ?>jobstep_pp/files/candidate_contracts/<?php echo $file_name; ?>" class="btn material-btn material-btn_success main-container__column" target="_BLANK"><i class="fa fa-file-pdf-o fa-lg" aria-hidden="true"></i></a>
														</td>
													</tr>
													<?php
														}
													?>
												</tbody>
											</table>
										</div>
									</div>
								</div>
								<div class="tab-pane fade" id="logovi">
									<div class="row">
										<hr>
										<!-- LOG STATUSA START -->
										<div class = "row">
											<!-- LOG STATUSA OBRADE -->
											<div class = "col-xs-6 text-center">
												<div class="row">
													<div class="col-xs-12">
														<h5 style = "font-weight: bold;"><i class="fa fa-pencil-square-o" style = "margin-right: 10px;" aria-hidden="true"></i>Log statusa obrade</h5>
													</div>
												</div>
												<div class="table-responsive">
													<table class="table table-striped">
														<thead>
															<tr>
																<th class="text-center">Status</th>
																<th class="text-center">Prijava na</th>
																<th class="text-center">Datum</th>
																<th class="text-center">Zaposlenik</th>
															</tr>
														</thead>
														<tbody>
															<?php
																$status_logovi_ispis = $db->prepare("
																				SELECT lks_status_obrade, lks_datetime, lks_employee_id, lg.lg_url, lks_link_id, nal.nalog_naziv, nal.nalog_broj
																				FROM idk_log_kandidat_statusi
																				LEFT JOIN idk_link_generator lg ON lks_link_id = lg.lg_id
																				LEFT JOIN idk_nalozi nal ON lg.lg_nalogid = nal.nalog_id
																				WHERE lks_kandidat_id = :lks_kandidat_id
																				ORDER BY lks_datetime ASC");

																$status_logovi_ispis->execute(array(
																							':lks_kandidat_id' => $kandidat_id
																							));
																while($status_logovi_ispis_row = $status_logovi_ispis->fetch()){
																	
																	$status_kandidata_ispis_logova = $status_logovi_ispis_row['lks_status_obrade'];
																	$lg_url = $status_logovi_ispis_row['lg_url'];
																	$nalog_naziv = $status_logovi_ispis_row['nalog_naziv'];
																	$nalog_broj = $status_logovi_ispis_row['nalog_broj'];
																	$prijava_na = "";
																	if($status_kandidata_ispis_logova == 0){
																		$status_kandidata_ispis_logova1 = '<span class="label label-warning material-label material-label_warning main-container__column text-left">Na provjeri</span>';
																		$prijava_na = $nalog_broj." - ".$nalog_naziv;
																	}
																	else if($status_kandidata_ispis_logova == 1){
																		$status_kandidata_ispis_logova1 = '<span class="label label-primary material-label material-label_primary main-container__column text-left">U obradi</span>';
																	}
																	else if($status_kandidata_ispis_logova == 5){ 
																		$status_kandidata_ispis_logova1 = '<span class="label label-primary material-label material-label_primary main-container__column text-left">Dopuna</span>';
																	}
																	else if($status_kandidata_ispis_logova == 6){
																		$status_kandidata_ispis_logova1 = '<span class="label label-danger material-label material-label_danger main-container__column text-left">Odbio messenger</span>';
																	}
																	else if($status_kandidata_ispis_logova == 4){
																		$status_kandidata_ispis_logova1 = '<span class="label label-info material-label material-label_info main-container__column text-left">Kontrola</span>';
																	}
																	else if($status_kandidata_ispis_logova == 2){
																		$status_kandidata_ispis_logova1 = '<span class="label label-success material-label material-label_success main-container__column text-left">Obrađen</span>';
																	}
																	else if($status_kandidata_ispis_logova == 3){
																		$status_kandidata_ispis_logova1 = '<span class="label label-danger material-label material-label_danger main-container__column text-left">Arhiviran</span>';
																	}
																	else if($status_kandidata_ispis_logova == 7){
																		$status_kandidata_ispis_logova1 = '<span class="label label-danger material-label material-label_danger main-container__column text-left">U obradi više od 3 dana</span>';
																	}
																	else if($status_kandidata_ispis_logova == 8){
																		$status_kandidata_ispis_logova1 = '<span class="label label-danger material-label material-label_danger main-container__column text-left">Na dopuni više od 3 dana</span>';
																	}
																	else if($status_kandidata_ispis_logova == 9){
																		$status_kandidata_ispis_logova1 = '<span class="label label-light material-label material-label_light main-container__column text-left">Ponovna prijava</span>';
																		$prijava_na = $nalog_broj." - ".$nalog_naziv;
																	}
																	
																	$vrijeme_promjene_kandidata_ispis_logova =  date('d.m.Y H:i', strtotime($status_logovi_ispis_row['lks_datetime']));
																	
																	$zaposlenik_ili_messenger_provjera  = $status_logovi_ispis_row['lks_employee_id'];
																	if($zaposlenik_ili_messenger_provjera == NULL){
																		if($status_kandidata_ispis_logova == 9)
																			$zaposlenik_nd_kandidata_ispis_logova = '<i class="label label-light material-label material-label_light main-container__column text-left">-</i>';
																		else
																			$zaposlenik_nd_kandidata_ispis_logova = '<i class="fa fa-commenting" aria-hidden="true" title="Messenger obrada!"></i>';
																	}
																	else{
																		$zaposlenik_nd_kandidata_ispis_logova = getZaposlenikimeR($status_logovi_ispis_row['lks_employee_id']);
																	}
																	
															?>
																	<tr>
																		<td class="text-center"> <?php echo $status_kandidata_ispis_logova1;  ?> </td>
																		<td class="text-center"> <?php echo $prijava_na;  ?> </td>
																		<td class="text-center"> <?php echo $vrijeme_promjene_kandidata_ispis_logova; ?> </td>
																		<td class="text-center"> <?php echo $zaposlenik_nd_kandidata_ispis_logova; ?> </td>
																	</tr>
															<?php 
																}
															?>
														</tbody>
													</table>
												</div>
											</div>
											<!-- LOG STATUSA PRIJAVE -->
											<div class = "col-xs-6 text-center">
												<div class="row">
													<div class="col-xs-12">
														<h5 style = "font-weight: bold;"><i class="fa fa-pencil-square-o" style = "margin-right: 10px;" aria-hidden="true"></i>Log statusa prijave</h5>
													</div>
												</div>
												<div class="table-responsive">
													<table class="table table-striped">
														<thead>
															<tr>
																<th class="text-center">Status</th>
																<th class="text-center">Datum</th>
																<th class="text-center">Zaposlenik</th>
																<th class="text-center">Projekti</th>
															</tr>
														</thead>
														<tbody>
															<?php
																$status_prijave_logovi_ispis = $db->prepare("
																				SELECT lsp_status_prijave_id, lsp_datetime, lsp_employee_id, lsp_izvor, lsp_projekt_id
																				FROM idk_log_statusi_prijave
																				WHERE lsp_kandidat_id = :lsp_kandidat_id
																				ORDER BY lsp_datetime ASC");

																$status_prijave_logovi_ispis->execute(array(
																							':lsp_kandidat_id' => $kandidat_id
																							));
																while($status_prijave_logovi_ispis_row = $status_prijave_logovi_ispis->fetch()){
																	$status_prijave_izvor = $status_prijave_logovi_ispis_row['lsp_izvor'];
																	$lsp_employee_id = $status_prijave_logovi_ispis_row['lsp_employee_id'];
																	$status_prijave_kandidata_ispis_logova = $status_prijave_logovi_ispis_row['lsp_status_prijave_id'];
																	$lsp_projekt_id = $status_prijave_logovi_ispis_row['lsp_projekt_id'];
																	
																	if($status_prijave_kandidata_ispis_logova == 1){
																		$status_prijave_kandidata_ispis_logova1 = '<span class="label label-default material-label material-label_default main-container__column text-left">Slobodan</span>';
																	}
																	else if($status_prijave_kandidata_ispis_logova == 2){
																		$status_prijave_kandidata_ispis_logova1 = '<span class="label label-warning material-label material-label_warning main-container__column text-left">Projekt NR</span>';
																	}
																	else if($status_prijave_kandidata_ispis_logova == 3){ 
																		$status_prijave_kandidata_ispis_logova1 = '<span class="label label-info material-label material-label_info main-container__column text-left">Casting</span>';
																	}
																	else if($status_prijave_kandidata_ispis_logova == 4){
																		$status_prijave_kandidata_ispis_logova1 = '<span class="label label-success material-label material-label_success main-container__column text-left">Završen</span>';
																	}
																	else if($status_prijave_kandidata_ispis_logova == 5){
																		$status_prijave_kandidata_ispis_logova1 = '<span class="label label-danger material-label material-label_danger main-container__column text-left">Odbijen</span>';
																	}
																	else if($status_prijave_kandidata_ispis_logova == 6){
																		$status_prijave_kandidata_ispis_logova1 = '<span class="label label-default material-label material-label_default main-container__column text-left" style="background-color: #B60606; color: white;">U projektu RZ</span>';
																	}
																	else if($status_prijave_kandidata_ispis_logova == 7){
																		$status_prijave_kandidata_ispis_logova1 = '<span class="label label-default material-label material-label_default main-container__column text-left" style="background-color: #E2E241; color: white;">Čeka ugovor</span>';
																	}
																	else if($status_prijave_kandidata_ispis_logova == 8){
																		$status_prijave_kandidata_ispis_logova1 = '<span class="label label-primary material-label material-label_primary main-container__column text-left">Poslan ugovor</span>';
																	}
																	else if($status_prijave_kandidata_ispis_logova == 9){
																		$status_prijave_kandidata_ispis_logova1 = '<span class="label label-danger material-label main-container__column text-left" style="background-color: #66FFB2; color: black;">Potpisan ugovor</span>';
																	}
																	else if($status_prijave_kandidata_ispis_logova == 10){
																		$status_prijave_kandidata_ispis_logova1 = '<span class="label label-light material-label material-label_light main-container__column text-left" style="background-color: #33FF33; color: white;">Početak rada</span>';
																	}
																	else if($status_prijave_kandidata_ispis_logova == 12){
																		$status_prijave_kandidata_ispis_logova1 = '<span class="label label-light material-label main-container__column text-left" style="background-color: #E9EFC0; color: black;">Prikupljanje dokumentacije</span>';
																	}
																	else if($status_prijave_kandidata_ispis_logova == 15){
																		$status_prijave_kandidata_ispis_logova1 = '<span class="label label-light material-label main-container__column text-left" style="background-color: #B4E197; color: black;">Čeka termin</span>';
																	}
																	else if($status_prijave_kandidata_ispis_logova == 18){
																		$status_prijave_kandidata_ispis_logova1 = '<span class="label label-light material-label main-container__column text-left" style="background-color: #83BD75; color: black;">Čeka vizu</span>';
																	}
																	else if($status_prijave_kandidata_ispis_logova == 27){
																		$status_prijave_kandidata_ispis_logova1 = '<span class="label label-light material-label main-container__column text-left" style="background-color: #4E944F; color: black;">Dobio vizu</span>';
																	}
																	else if($status_prijave_kandidata_ispis_logova == 21){
																		$status_prijave_kandidata_ispis_logova1 = '<span class="label label-light material-label main-container__column text-left" style="background-color: #FFC3C3; color: black;">Dopuna dokumenata</span>';
																	}
																	else if($status_prijave_kandidata_ispis_logova == 24){
																		$status_prijave_kandidata_ispis_logova1 = '<span class="label label-light material-label main-container__column text-left" style="background-color: #FF8C8C; color: black;">Odbijena viza</span>';
																	}
																	
																	$vrijeme_promjene_kandidata_ispis_logova_prijava =  date('d.m.Y H:i', strtotime($status_prijave_logovi_ispis_row['lsp_datetime']));
																	
																	if($status_prijave_izvor == 2){
																		$get_user_pp = $db->prepare("
																									SELECT pu_fname, pu_lname
																									FROM idk_pp_users
																									WHERE pu_id = :pu_id
																									");
																		$get_user_pp->execute(array(
																			":pu_id" => $lsp_employee_id
																		));
																		$user_pp_result = $get_user_pp->fetch();
																		$user_fname = $user_pp_result['pu_fname'];
																		$user_lname = $user_pp_result['pu_lname'];
																		$zaposlenik_kandidata_ispis_logova_prijava = $user_fname . " " . $user_lname;
																	}
																	elseif($status_prijave_izvor == 5){
																		$zaposlenik_kandidata_ispis_logova_prijava = "Cron";
																	}else{
																		$zaposlenik_kandidata_ispis_logova_prijava = getZaposlenikimeR($status_prijave_logovi_ispis_row['lsp_employee_id']);
																	}
																	
																	$get_project = $db->prepare("
																								SELECT project_name
																								FROM idk_projects
																								WHERE project_id = :project_id
																								");
																	$get_project->execute(array(
																		":project_id" => $lsp_projekt_id
																	));
																	$project_result = $get_project->fetch();
																	$project_name = $project_result['project_name'];
															?>
																	<tr>
																		<td class="text-center"> <?php echo $status_prijave_kandidata_ispis_logova1;  ?> </td>
																		<td class="text-center"> <?php echo $vrijeme_promjene_kandidata_ispis_logova_prijava; ?> </td>
																		<td class="text-center"> <?php echo $zaposlenik_kandidata_ispis_logova_prijava; ?> </td>
																		<td class="text-center"> <?php echo $project_name; ?> </td>
																	</tr>
															<?php 
																}
															?>
														</tbody>
													</table>
												</div>
											</div>
										</div>
										<hr>
										<!-- LOG STATUSA END -->
									</div> 
								</div>
								<div class="tab-pane fade" id="projekcija">
									<hr>
									<candidate-projection kandidat_id="<?php echo $kandidat_id ?>"></candidate-projection>
									<script>
										/*
										*	Kako se komponenta nalazi unutar tab-a, prije nego što se taj tab otvori, ona je nevidljiva. 
										*	Kada postane vidljiva, potrebno je pozvati par funkcija da se updateaju podaci o veličini tabele.
										*/
										$(document).ready(function(){
											var targetNode = document.getElementById('projekcija');
											var observer = new MutationObserver(function(){
												if(targetNode.style.display != 'none' && document.querySelector("candidate-projection").data != undefined){
													document.querySelector("candidate-projection").boost.updateMetrics();
													document.querySelector("candidate-projection").setScrollToThisMonth();
												}
											});
											observer.observe(targetNode, { attributes: true, childList: true });
										});
									</script>
								</div>
								<!-- PRIPREMA ZA PROCES ODLASKA - START -->
									<?php
										/* kupe se informacije potrebne za panel proces odlaska */
										$get_datum_termina = $db->prepare("SELECT datum_termina, kandidat_bio_na_terminu, kandidat_nalog_id, kandidat_status_prijave FROM idk_kandidati WHERE kandidat_id = :kandidat_id");
										$get_datum_termina->execute([":kandidat_id" => $kandidat_id]);
										$result = $get_datum_termina->fetch();
										$datum_termina = date("d.m.Y", strtotime($result["datum_termina"]));
										$nalog_id = $result["kandidat_nalog_id"];
										$kandidat_status_prijave=$result['kandidat_status_prijave'];

										if($result["datum_termina"] != null){
											$termin_btn = "Edit termina";
										} else {
											$termin_btn = "Unos termina";
										}
										if($result["kandidat_bio_na_terminu"] == "1"){
											$termin_class = "label label-success material-label material-label_success man-container__column";
											$check_btn_style = "pointer-events: none;";
											$show_confirmation_btn = false;
											if($kandidat_status_prijave!=18){
												$disabled="disabled";	
											}
											$ishod_btn =' 
															<div class="row">
																<div class="col-sm-5"></div>
																<div class="col-sm-3"><a href="#" class="'.$disabled.' btn material-btn material-btn-icon-success material-btn_success main-container__column" data-toggle="modal" data-target="#ishod_termina_m"><i class="fa fa-plus" aria-hidden="true"></i> <span>Ishod termina</span></a></div>
															</div>
														';
										} 
										elseif($result["kandidat_bio_na_terminu"] === "0"){
											$termin_class = "label label-danger material-label material-label_danger man-container__column";
											$check_btn_style = "pointer-events: none;";
											$show_confirmation_btn = false;
											$new_termin_btn =' 
																<div class="row">
																	<div class="col-sm-5"></div>
																	<div class="col-sm-3"><a href="#" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" data-toggle="modal" data-target="#kandidat_unos_termina_m" ><i class="fa fa-plus" aria-hidden="true"></i> <span>Novi termin</span></a></div>
																</div>
															';
										}
										elseif($result["kandidat_bio_na_terminu"] == null) {
											$termin_class = "label label-info material-label material-label_info man-container__column";
											$show_confirmation_btn = true;
										}
									?>
								<!-- PRIPREMA ZA PROCES ODLASKA - END -->
								<!-- PROCES ODLASKA PANEL - START -->
								<div class="tab-pane fade" id="proces_odlaska">
									<?php
										$provjeri_detalje_kandidata_query = $db->prepare("	SELECT 
																								kandidat_drzavljanstvo_vrsta,kandidat_status_prijave,kandidat_dogovoreni_pocetak_rada,kandidat_potvrden_pocetak_rada	 
																							FROM 
																								idk_kandidati WHERE kandidat_id=:kandidat_id");
										$provjeri_detalje_kandidata_query->execute(array(
											':kandidat_id'	=> $kandidat_id
										));
										$provjeri_detalje_kandidata 		= $provjeri_detalje_kandidata_query->fetch();
										$kandidat_drzavljanstvo_vrsta 		= $provjeri_detalje_kandidata["kandidat_drzavljanstvo_vrsta"];
										$status_prijave_id 					= $provjeri_detalje_kandidata["kandidat_status_prijave"];
										$kandidat_dogovoreni_pocetak_rada	= $provjeri_detalje_kandidata["kandidat_dogovoreni_pocetak_rada"];
										$kandidat_potvrden_pocetak_rada		= $provjeri_detalje_kandidata["kandidat_potvrden_pocetak_rada"];


										if($kandidat_drzavljanstvo_vrsta == "EU državljanin" AND $kandidat_status_prijave>=9){
											
									?>
											 <!-- MODAL POČETAK RADA START -->
											 <div class="modal material-modal material-modal_primary fade text-left" id="početak_rada_m">
                                                        <div class="modal-dialog ">
                                                            <div class="modal-content material-modal__content">
                                                                <div class="modal-header material-modal__header">
                                                                    <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                                                    <h4 class="modal-title material-modal__title">Unos početka rada</h4>
                                                                </div>
                                                                <div class="modal-body material-modal__body">
                                                                    <form action="<?php getSiteURL();?>do.php?form=unesi_datum_pocetak_rada" method="post" role="form" class="form-horizontal">
                                                                        <div class="form-group col-sm-12 text-right">
                                                                            <input type="hidden" name="kandidat_id" value="<?php echo $kandidat_id; ?>">
                                                                            <label for="datum_pocetak_rada" class="col-sm-5 control-label">Unesi dogovoreni datum početka rada:</label>
                                                                            <div class="col-sm-6">
                                                                                <div class="materail-input-block materail-input-block_success">
                                                                                    
                                                                                    <input class="form-control materail-input" type="text" name="datum_pocetak_rada" autocomplete="off" id="datum_pocetak_rada"  >
                                                                                    <span class="materail-input-block__line"></span>
                                                                                </div>
                                                                            </div>
                                                                            <script>
                                                                                $(function() {
                                                                                    initDateSelectNew();
                                                                                });

                                                                                function initDateSelectNew() {
                                                                                    $("#datum_pocetak_rada").flatpickr({
                                                                                        minDate: "2000-01-01"
                                                                                    });
                                                                                }
                                                                            </script>
                                                                        </div>
                                                                       
                                                                </div>
                                                                <div class="modal-footer material-modal__footer">
                                                                        <button type="submit" class="btn btn-primary material-btn material-btn_success"><i class="fa fa-edit" aria-hidden="true"></i> Spremi</button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <!-- MODAL POČETAK RADA END -->
                                                
                                                <!-- MODAL POTVRDI POČETAK RADA START-->
                                                    <div class="modal material-modal material-modal_primary fade text-left" id="potvrdi_pocetak_rada">
                                                        <div class="modal-dialog ">
                                                            <div class="modal-content material-modal__content">
                                                                <div class="modal-header material-modal__header">
                                                                    <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                                                    <h4 class="modal-title material-modal__title">Unos početka rada</h4>
                                                                </div>
                                                                <div class="modal-body material-modal__body">
                                                                    <form action="<?php getSiteURL();?>do.php?form=uredi_pocetak_rada" method="post" role="form" class="form-horizontal">
                                                                    <input type="hidden" name="kandidat_id" value="<?php echo $kandidat_id; ?>">
                                                                    <div class="form-group">
                                                                        <div class="col-sm-5" style="padding-top:5px; text-align:right;">Da li je kandidat počeo sa radom:
                                                                        </div>
                                                                        <div class="col-sm-7">
                                                                            <div class="materail-input-block materail-input-block_success idk_radio_buttons">
                                                                                <label class="main-container__column material-radio-group material-radio-group_success" for="potvrdi_pocetak_rada_da" style="padding-right: 5px;">
                                                                                    <input type="radio" name="potvrdi_pocetak_rada" id="potvrdi_pocetak_rada_da" class="material-radiobox" value="1" />
                                                                                    <span class="material-radio-group__element material-radio-group__check-radio"></span>
                                                                                    <span class="material-radio-group__element material-radio-group__caption">Da </span>
                                                                                </label>													
                                                                                
                                                                                <label class="main-container__column material-radio-group material-radio-group_danger" for="potvrdi_pocetak_rada_ne">
                                                                                    <input type="radio" name="potvrdi_pocetak_rada" id="potvrdi_pocetak_rada_ne" <?php if(isset($kandidat_potvrden_pocetak_rada) AND $kandidat_potvrden_pocetak_rada == 0){echo "checked";} ?> class="material-radiobox" value="0" />
                                                                                    <span class="material-radio-group__element material-radio-group__check-radio"></span>
                                                                                    <span class="material-radio-group__element material-radio-group__caption">Ne </span>
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>	
                                                                    <script>
                                                                        $(document).ready(function () {
                                                                            if( $('#potvrdi_pocetak_rada_ne').is(':checked') ){
                                                                                const novi_datum_pocetak_rada = $("#novi_datum_pocetak_rada").flatpickr();
                                                                                $('.novi_datum_pr').css('display', 'block');
                                                                                $("#spremi_detalje_pr").prop("disabled", true);
                                                                                
                                                                                $("#provjera_novog_datuma_pocetka_rada_da").click(function() {
                                                                                    

                                                                                    $(".novi_datum_pocetak_rada").css('display', 'block');
                                                                                    $("#spremi_detalje_pr").prop("disabled", true);
                                                                                    $("#novi_datum_pocetak_rada").change(function(){
                                                                                        if(!$.trim(this.value).length){
                                                                                            $("#spremi_detalje_pr").prop("disabled", true);
                                                                                        } 
                                                                                        else if($.trim(this.value).length) {
                                                                                            $("#spremi_detalje_pr").prop("disabled", false);
                                                                                        }
                                                                                    });
                                                                                })
                                                                                
                                                                                $("#provjera_novog_datuma_pocetka_rada_ne").click(function() {
                                                                                    $(".novi_datum_pocetak_rada").css('display', 'none');
                                                                                    $("#spremi_detalje_pr").prop("disabled", false);
                                                                                    
                                                                                    novi_datum_pocetak_rada.clear();
                                                                                })
                                                                                    }
                                                                            
                                                                        });
                                                                        $('#potvrdi_pocetak_rada_ne').click(function(){

                                                                            const novi_datum_pocetak_rada = $("#novi_datum_pocetak_rada").flatpickr();
                                                                            $("#provjera_novog_datuma_pocetka_rada_ne").prop("checked", false);
                                                                            $("#provjera_novog_datuma_pocetka_rada_da").prop("checked", false);
                                                                            novi_datum_pocetak_rada.clear();
                                                                            $('.novi_datum_pr').css('display', 'block');
                                                                            $("#spremi_detalje_pr").prop("disabled", true);
                                                                            if( !$("#provjera_novog_datuma_pocetka_rada_da").is(":checked") && !$("#provjera_novog_datuma_pocetka_rada_ne").is(":checked")){
                                                                                $("#spremi_detalje_pr").prop("disabled", true);
                                                                            }
                                                                            $("#provjera_novog_datuma_pocetka_rada_da").click(function() {
                                                                               

                                                                                $(".novi_datum_pocetak_rada").css('display', 'block');
                                                                                $("#spremi_detalje_pr").prop("disabled", true);
                                                                                $("#novi_datum_pocetak_rada").change(function(){
                                                                                    if(!$.trim(this.value).length){
                                                                                        $("#spremi_detalje_pr").prop("disabled", true);
                                                                                    } 
                                                                                    else if($.trim(this.value).length) {
                                                                                        $("#spremi_detalje_pr").prop("disabled", false);
                                                                                    }
                                                                                });
                                                                            })
                                                                            
                                                                            $("#provjera_novog_datuma_pocetka_rada_ne").click(function() {
                                                                                $(".novi_datum_pocetak_rada").css('display', 'none');
                                                                                $("#spremi_detalje_pr").prop("disabled", false);
                                                                                
                                                                                novi_datum_pocetak_rada.clear();
                                                                            })
                                                                            
                                                                        });
                                                                        $('#potvrdi_pocetak_rada_da').click(function(){

                                                                            $('.novi_datum_pr').css('display', 'none');
                                                                            $('.novi_datum_pocetak_rada').css('display', 'none');
                                                                            $("#spremi_detalje_pr").prop("disabled", false);

                                                                        });
                                                                    </script>	
                                                                    <div class="form-group novi_datum_pr" style="display: none;">
                                                                        <div class="col-sm-5" style="padding-top:5px; text-align:right;">
                                                                            Da li kandidat ima novi datum početka rada:
                                                                        </div>
                                                                        <div class="col-sm-7">
                                                                            <div class="materail-input-block materail-input-block_success idk_radio_buttons">
                                                                                <label class="main-container__column material-radio-group material-radio-group_success" for="provjera_novog_datuma_pocetka_rada_da" style="padding-right: 5px;">
                                                                                    <input type="radio" name="provjera_novog_datuma_pocetka_rada" id="provjera_novog_datuma_pocetka_rada_da" class="material-radiobox" value="1" />
                                                                                    <span class="material-radio-group__element material-radio-group__check-radio"></span>
                                                                                    <span class="material-radio-group__element material-radio-group__caption">Da </span>
                                                                                </label>													
                                                                                
                                                                                <label class="main-container__column material-radio-group material-radio-group_danger" for="provjera_novog_datuma_pocetka_rada_ne">
                                                                                    <input type="radio" name="provjera_novog_datuma_pocetka_rada" id="provjera_novog_datuma_pocetka_rada_ne" class="material-radiobox" value="0" />
                                                                                    <span class="material-radio-group__element material-radio-group__check-radio"></span>
                                                                                    <span class="material-radio-group__element material-radio-group__caption">Ne </span>
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group col-sm-12 text-right novi_datum_pocetak_rada" style="display: none;">
                                                                        <label for="novi_datum_pocetak_rada" class="col-sm-5 control-label">Unesi novi datum početka rada:</label>
                                                                        <div class="col-sm-6">
                                                                            <div class="materail-input-block materail-input-block_success">
                                                                                
                                                                                <input class="form-control materail-input" type="text" name="novi_datum_pocetak_rada" autocomplete="off" id="novi_datum_pocetak_rada"  >
                                                                                <span class="materail-input-block__line"></span>
                                                                            </div>
                                                                        </div>
                                                                        <script>
                                                                            $(function() {
                                                                                initDateSelectNPR();
                                                                            });

                                                                            function initDateSelectNPR() {
                                                                                $("#novi_datum_pocetak_rada").flatpickr({
                                                                                    minDate: "2000-01-01"
                                                                                });
                                                                            }
                                                                        </script>
                                                                    </div>	
                                                                </div>
                                                                <div class="modal-footer material-modal__footer">
                                                                        <button type="submit" class="btn btn-primary material-btn material-btn_success" disabled id="spremi_detalje_pr"><i class="fa fa-edit" aria-hidden="true"></i> Spremi</button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <!-- MODAL POTVRDI POČETAK RADA END -->
                                                <div class="row">
                                                    <div class="col-sm-12 text-center">
                                                        <?php if(!isset($kandidat_dogovoreni_pocetak_rada)){  
                                                            ?> 
                                                            <a href="#" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" data-toggle="modal" data-target="#početak_rada_m">
                                                                <i class="fa fa-plus" aria-hidden="true"></i>
                                                                <span>Unesi dogovoreni datum početka rada</span>
                                                            </a>
                                                        <?php }else if (isset($kandidat_dogovoreni_pocetak_rada) AND ($status_prijave_id == 9 OR $status_prijave_id == 12)){ ?>    

                                                            <div class="col-sm-3"></div>
                                                            <div class="col-sm-6 text-right">
                                                                <table id="detalji_pocetka_rada_table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th class="text-center">Datum početka rada</th>
                                                                            <th class="text-center">Potvrdi početak rada</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td class="text-center"><?php if(isset($kandidat_dogovoreni_pocetak_rada)){echo $kandidat_dogovoreni_pocetak_rada;}else{ echo "Kandidat nema unešen datum početka rada!";} ?></td>
                                                                            <td class="text-center"><a href="" class="btn material-btn material-btn_success main-container__column" data-toggle="modal" <?php if($status_prijave_id==9  OR $status_prijave_id == 12){ echo ' data-target="#potvrdi_pocetak_rada"';}else{ echo 'disabled';} ?> ><i class="fa fa-check" aria-hidden="true"></i></a></td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                                <script>
                                                                    $(document).ready(function() {
                                                                        $("#detalji_pocetka_rada_table").DataTable({
                                                                            responsive: true,
                                                                            searching: false,
                                                                            paging: false,
                                                                            "ordering": false,
                                                                            "info":     false,
                                                                            "bAutoWidth": false,
                                                                            "aoColumns": [
                                                                                { "width": "50%" },
                                                                                { "width": "50%" }
                                                                            ]
                                                                        });
                                                                    });
                                                                </script>
                                                            </div>
                                                        <?php 
															}else{	
														?> 
																<div class="row">
																	<div class="col-sm-3"></div>
																	<div class="col-sm-6 text-right">
																		<table id="datum_pocetka_rada_table">
																			<thead>
																				<tr>
																					<th class="text-center">Datum početka rada:</th>
																				</tr>
																			</thead>
																			<tbody>
																				<tr>
																					<td class="text-center"><?php if(isset($kandidat_dogovoreni_pocetak_rada)){echo $kandidat_dogovoreni_pocetak_rada;}else{ echo "Kandidat nema unešen datum početka rada!";} ?></td>
																				</tr>
																			</tbody>
																		</table>
																		<script>
																			$(document).ready(function() {
																				$("#datum_pocetka_rada_table").DataTable({
																					responsive: true,
																					searching: false,
																					paging: false,
																					"ordering": false,
																					"info":     false,
																					"bAutoWidth": false
																				});
																			});
																		</script>
																	</div>
																</div>
														<?php	
															} 
														?>
                                                    </div>
                                                </div>
									<?php
										}else{
											include("proces_odlaska.php");
										}
									?>
								</div>
								<!-- PROCES ODLASKA PANEL - END -->
								<?php 
									if((in_array("1",$employee_status)) OR (in_array("18",$employee_status))  OR (in_array("18",$employee_supervizor))){
								?>
								<!-- 
									TASK FORCE NOTES PANEL START 
								-->
									<style>
										.tf_panel{
											box-shadow: 0 2px 5px 0 rgb(0 0 0 / 30%);
											padding: 20px;
											border: 1px solid lightgrey;
											border-radius: 7px;
											margin-bottom: 20px;
										}
										.tf_panel_top{
											border: 1px solid lightgrey;
											border-radius: 7px;
											padding: 10px;
											margin-bottom: 20px;
										}
										.tf_panel_bottom{
											border: 1px solid lightgrey;
											border-radius: 7px;
											padding: 10px;
										}
										.tf_hr{
											border-top: 1px solid #ccc;
											margin-top: 5px;
											margin-bottom: 5px;
										}
									</style>
									<div class="tab-pane fade" id="task_force_notes">
										<div class="row">
											<div class="col-lg-12">
												<div class = "content_box">
													<div class = "row">
														<div class = "col-lg-offset-2 col-lg-8">
															<div class = "row">
																<?php 
																	$tf_query = $db->prepare("
																		SELECT 
																			tf.tf_agent_id, tf.tf_nalog_id, tf.tf_project_id, tf.tf_casting_id, tf.tf_status_id, tf.tf_call_appointment, tf.tf_note, tf.tf_important_note, tf.tf_doe, tf.tf_files, tf.tf_vrsta_id,
																			tfs.tfs_id, tfs.tfs_name,
																			n.nalog_id, n.nalog_naziv,
																			emp.employee_id, emp.employee_firstname, emp.employee_lastname,
																			p.project_id, p.project_name, 
																			pag.ppaq_id, pag.papq_name
																		FROM 
																			idk_task_force tf
																		INNER JOIN 
																			idk_tf_statusi tfs 
																		ON 
																			tf.tf_status_id = tfs.tfs_id
																		INNER JOIN 
																			idk_nalozi n
																		ON 
																			tf.tf_nalog_id = n.nalog_id
																		LEFT JOIN 
																			idk_employees emp
																		ON 
																			tf.tf_agent_id = emp.employee_id
																		LEFT JOIN 
																			idk_projects p 
																		ON 
																			tf.tf_project_id = p.project_id
																		LEFT JOIN 
																			idk_pp_appointment_groups pag 
																		ON 
																			tf.tf_casting_id = pag.ppaq_id
																		WHERE 
																			tf.tf_candidate_id = :candidateId
																		ORDER BY tf.tf_id DESC
																	");
																	$tf_query->execute(array(
																		':candidateId' => $kandidat_id
																	));
																	while($tf_row = $tf_query->fetch()){ 
																?> 
																<!-- 
																	TF NOTE START 
																-->
																<div class = "col-xs-12">
																	<div class="tf_panel">
																		<div class="row">
																			<div class="col-lg-12">
																				<div class="tf_panel_top">
																					<div class="row">
																						<div class="col-lg-6">
																							<div class="row">
																								<div class="col-xs-1 text-center">
																									<i class="fa fa-user text-primary" aria-hidden="true"></i>
																								</div>
																								<div class="col-xs-2 text-primary">
																									Zaposlenik
																								</div>
																								<div class="col-xs-9">
																									<?php 
																										if($tf_row["employee_id"] != NULL){
																											echo $tf_row["employee_firstname"]." ".$tf_row["employee_lastname"];
																										}else{
																											echo "Automatska biljeska!";
																										}
																									?>
																								</div>
																							</div>
																							<div class="row">
																								<div class="col-xs-1 text-center">
																									<i class="fa fa-calendar text-primary" aria-hidden="true"></i>
																								</div>
																								<div class="col-xs-2 text-primary">
																									Datum
																								</div>
																								<div class="col-xs-9">
																									<?php echo $tf_row["tf_doe"]; ?>
																								</div>
																							</div>
																							<div class="row">
																								<div class="col-xs-1 text-center">
																									<?php 
																										if($tf_row["tf_call_appointment"] != NULL){
																									?>
																									<i class="fa fa-calendar-check-o text-primary" aria-hidden="true"></i>
																									<?php 
																										}else{
																									?>
																									<i class="fa fa-calendar-times-o text-primary" aria-hidden="true"></i>
																									<?php 
																										}
																									?>
																									
																								</div>
																								<div class="col-xs-2 text-primary">
																									Termin
																								</div>
																								<div class="col-xs-9">
																									<?php 
																										if($tf_row["tf_call_appointment"] != NULL){
																											echo date("d.m.Y H:i", strtotime($tf_row["tf_call_appointment"])); 
																										}else{ 
																											echo "Nije postavljen";
																										}
																									?>
																								</div>
																							</div>
																						</div>
																						<div class="col-lg-6">
																							<div class="row">
																								<div class="col-xs-1 text-center">
																									<i class="fa fa-cogs text-primary" aria-hidden="true"></i>
																								</div>
																								<div class="col-xs-2 text-primary">
																									Status
																								</div>
																								<div class="col-xs-9">
																									<?php echo $tf_row["tfs_name"]; ?>
																								</div>
																							</div>
																							<div class="row">
																								<div class="col-xs-1 text-center">
																									<i class="fa fa-folder text-primary" aria-hidden="true"></i>
																								</div>
																								<div class="col-xs-2 text-primary">
																									Vrsta
																								</div>
																								<div class="col-xs-9">
																									<?php echo getVrstaName($tf_row["tf_vrsta_id"]); ?>
																								</div>
																							</div>
																							<div class="row">
																								<div class="col-xs-1 text-center">
																									<i class="fa fa-folder text-primary" aria-hidden="true"></i>
																								</div>
																								<div class="col-xs-2 text-primary">
																									Nalog
																								</div>
																								<div class="col-xs-9">
																									<?php echo $tf_row["nalog_naziv"]; ?>
																								</div>
																							</div>
																							<div class="row">
																								<div class="col-xs-1 text-center">
																									<i class="fa fa-folder-open text-primary" aria-hidden="true"></i>
																								</div>
																								<div class="col-xs-2 text-primary">
																									Projekt
																								</div>
																								<div class="col-xs-9">
																									<?php echo $tf_row["project_name"]; ?>
																								</div>
																							</div>
																							<?php 
																								if($tf_row["tf_casting_id"] != NULL){
																							?>
																							<div class="row">
																								<div class="col-xs-1 text-center">
																									<i class="fa fa-calendar-o text-primary" aria-hidden="true"></i>
																								</div>
																								<div class="col-xs-2 text-primary">
																									Casting
																								</div>
																								<div class="col-xs-9">
																									<?php echo $tf_row["papq_name"]; ?>
																								</div>
																							</div>
																							<?php 
																								}
																							?>
																						</div>
																					</div>
																				</div>
																			</div>
																			<div class="col-lg-12">
																				<div class="tf_panel_bottom">
																					<div class="row">
																						<div class="col-lg-12"> 
																							<div class="row">
																								<div class="col-xs-1 text-center">
																									<i class="fa fa-comment text-primary" aria-hidden="true"></i>
																								</div>
																								<div class="col-xs-9">
																									Bilješka
																								</div>
																								<div class="col-xs-2 text-right">
																									<?php 
																										if($tf_row["tf_important_note"] == 1){
																									?>
																									<i class="fa fa-exclamation-triangle text-danger" aria-hidden="true" title="Pri unosu bilješka je označena kao važna bilješka!"></i>
																									<?php 
																										}
																									?>
																								</div>
																							</div>
																							<div class="tf_hr"></div>
																							<div class="row">
																								<div class="col-xs-12">
																									<p style = "text-align: justify; word-break: break-all;"><?php echo ($tf_row["tf_note"] != NULL ? $tf_row["tf_note"] : "Nije unešena bilješka ") ; ?></p>
																								</div>
																							</div>
																							
																							<?php 
																							if($tf_row['tf_files'] != null){
																								$tf_note_files_exp = explode(",", $tf_row['tf_files']);
																								?>
																								
																								<hr>
																								<div class="row">
																									<div class = "col-xs-12">
																										<?php 
																											foreach($tf_note_files_exp AS $tf_note_file_value){ 

																												$tf_note_file_icon = "";

																												if (strpos($tf_note_file_value, '.jpg') !== false OR strpos($tf_note_file_value, '.png') !== false){
																													$tf_note_file_icon = '<i class="fa fa-file-image-o fa-2x" aria-hidden="true"></i>';
																												}else if(strpos($tf_note_file_value, '.pdf') !== false){
																													$tf_note_file_icon = '<i class="fa fa-file-pdf-o fa-2x" aria-hidden="true"></i>';
																												}else if(strpos($tf_note_file_value, '.doc') !== false OR strpos($tf_note_file_value, '.docx') !== false){
																													$tf_note_file_icon = '<i class="fa fa-file-word-o fa-2x" aria-hidden="true"></i>';
																												}else if(strpos($tf_note_file_value, '.xls') !== false OR strpos($tf_note_file_value, '.xlsx') !== false OR strpos($tf_note_file_value, '.csv') !== false){
																													$tf_note_file_icon = '<i class="fa fa-file-excel-o fa-2x" aria-hidden="true"></i>';
																												}else if(strpos($tf_note_file_value, '.txt') !== false ){
																													$tf_note_file_icon = '<i class="fa fa-file-text-o fa-2x" aria-hidden="true"></i>';
																												}else{
																													$tf_note_file_icon = '<i class="fa fa-file-powerpoint-o fa-2x" aria-hidden="true"></i>';
																												}

																												?>
																													<div class = "col-sm-1">
																														<a href="<?php getSiteURL(); ?>files/prilozi_kandidati/<?php echo $tf_note_file_value; ?>" class="btn material-btn material-btn_success main-container__column" style = "padding: 5px;" target="_BLANK"><?php echo $tf_note_file_icon; ?></a>
																													</div>
																												<?php 
																											}
																										?>
																									</div>
																								</div>
																								<?php
																							} ?>
																						</div>
																					</div>
																				</div>
																			</div>
																		</div>
																	</div>
																</div>
																<!-- 
																	TF NOTE END 
																-->
																<?php 
																	}
																?>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								<!-- 
									TASK FORCE NOTES PANEL END  
								-->
								<?php 
									}
								?>
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

				case "del_doc":
					if((in_array( "2" , $getEmployeeStatus )) OR (in_array( "18" , $getEmployeeStatus )) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){ 

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
					if((in_array( "2" , $getEmployeeStatus )) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){

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
					if((in_array( "2" , $getEmployeeStatus )) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){

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


						header("Location: " . getSiteURLr() . "kandidati?page=list_ajax&mess=4");

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

					if ((in_array( "2" , $getEmployeeStatus )) OR (in_array( "18" , $getEmployeeStatus )) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))) {
						
						$kki_id 		= $_GET['id'];
						$kandidat_check = $_GET['check'];
						$kandidat_id    = $_GET['kandidatid'];

						//Get note_txt and note_dataid
						$note_open_query = $db->prepare("
							SELECT kki_naziv, kki_kandidat_id, kki_podatak, kki_primary
							FROM idk_kandidat_kontakt_info
							WHERE kki_id = :kki_id
						");

						$note_open_query->execute(array(
							':kki_id' => $kki_id
						));

						$note_open = $note_open_query->fetch();

						$kki_naziv 		 = $note_open['kki_naziv'];
						$kki_kandidat_id = $note_open['kki_kandidat_id'];
						$kki_podatak 	 = $note_open['kki_podatak'];

						// Provjera count-a za telefon
						$check_kontakt_info_count = $db->prepare("
							SELECT kki_id, kki_primary, kki_grupa
							FROM idk_kandidat_kontakt_info
							WHERE kki_kandidat_id = :kandidat_id
							AND kki_grupa = 1
						");

						$check_kontakt_info_count->execute(array(
							':kandidat_id' => $kandidat_id
						));

						$count_phone = count($check_kontakt_info_count->fetchAll());
						
						// Provjera count-a za email
						$check_kontakt_info_count = $db->prepare("
							SELECT kki_id, kki_primary, kki_grupa
							FROM idk_kandidat_kontakt_info
							WHERE kki_kandidat_id = :kandidat_id
							AND kki_grupa = 2
						");

						$check_kontakt_info_count->execute(array(
							':kandidat_id' => $kandidat_id
						));

						$count_email = count($check_kontakt_info_count->fetchAll());

						if ($count_phone > 1 || $count_email > 1) {

							// Delete from db
							$doc_del_query = $db->prepare("
								DELETE FROM idk_kandidat_kontakt_info
								WHERE kki_id = :kki_id
								AND kki_primary = 0
							");

							$doc_del_query->execute(array(
								':kki_id' => $kki_id
							));

						} else if ($count_phone == 1 || $count_email == 1) {

							// Provjeri da li je u pitanju mobitel ili email
							$check_kontakt_info_grupa = $db->prepare("
								SELECT kki_grupa
								FROM idk_kandidat_kontakt_info
								WHERE kki_id = :kki_id
							");

							$check_kontakt_info_grupa->execute(array(
								':kki_id' => $kki_id
							));

							// Delete from db
							$delete_primary_kontakt_info = $db->prepare("
								DELETE FROM idk_kandidat_kontakt_info
								WHERE kki_id = :kki_id
								AND kki_primary = 1
							");

							$delete_primary_kontakt_info->execute(array(
								':kki_id' => $kki_id
							));

							$grupa_row = $check_kontakt_info_grupa->fetch();

							if ($grupa_row['kki_grupa'] == 1) { 		// Ako je mobitel
								
								// Update idk_kandidati
								$update_idk_kandidati = $db->prepare("
									UPDATE idk_kandidati
									SET kandidat_mobitel = NULL
									WHERE kandidat_id = :kandidat_id
								");

								$update_idk_kandidati->execute(array(
									':kandidat_id' => $kandidat_id
								));

							} else if ($grupa_row['kki_grupa'] == 2) { // Ako je email
								
								// Update idk_kandidati
								$update_idk_kandidati = $db->prepare("
									UPDATE idk_kandidati
									SET kandidat_email = NULL
									WHERE kandidat_id = :kandidat_id
								");

								$update_idk_kandidati->execute(array(
									':kandidat_id' => $kandidat_id
								));

							}

						}

						//Add to LOGS
						$getCandidateFullnameR = getCandidateFullnameR($kandidat_id);

						$log_desc = "Izbrisao (kontakt informaciju) ".$kki_podatak." kandidatu: " . $getCandidateFullnameR . "";
						$log_date = date('Y-m-d H:i:s');

						$log_query = $db->prepare("
							INSERT INTO idk_logs
								(log_employeeid, log_desc, log_date)
							VALUES
								(:log_employeeid, :log_desc, :log_date)
						");

						$log_query->execute(array(
							':log_employeeid' => $logged_employee_id,
							':log_desc' 	  => $log_desc,
							':log_date'       => $log_date
						));

						header("Location: kandidati?page=open&id=$kandidat_id");

					} else {

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
				
				case "delete_kriskustvo":
					if((in_array( "2" , $getEmployeeStatus )) OR (in_array( "18" , $getEmployeeStatus )) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){
						$kri_id = $_POST['kri_id'];
						$check = $_POST['kandidat_check'];
						$kandidat_id = $_POST['kandidat_id'];

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
											
						//Delete from validnosti
						$del_from_validnosti = $db->prepare("
													DELETE FROM idk_validnosti_inputa
													WHERE vi_kandidat_id = :vi_kandidat_id AND vi_podatak_id = :vi_podatak_id AND vi_vrsta_podatka = 'iskustvo' ");

						$del_from_validnosti->execute(array(
											':vi_kandidat_id' => $kandidat_id,
											':vi_podatak_id' => $kri_id
											));

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
				case "delete_kandidat_edukacija":
					if((in_array( "2" , $getEmployeeStatus )) OR (in_array( "18" , $getEmployeeStatus )) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){
						$ke_id = $_POST['ke_id'];
						$kandidat_check = $_POST['kandidat_check'];
						$kandidat_id = $_POST['kandidat_id'];

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
											
						//Delete from validnosti
						$del_from_validnosti = $db->prepare("
													DELETE FROM idk_validnosti_inputa
													WHERE vi_kandidat_id = :vi_kandidat_id AND vi_podatak_id = :vi_podatak_id AND vi_vrsta_podatka = 'obrazovanje' ");

						$del_from_validnosti->execute(array(
											':vi_kandidat_id' => $kandidat_id,
											':vi_podatak_id' => $ke_id
											));

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
				case "delete_kandidat_vjestina":
					if((in_array( "2" , $getEmployeeStatus )) OR (in_array( "18" , $getEmployeeStatus )) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){
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
				case "delete_kandidat_jezik":
					if((in_array( "2" , $getEmployeeStatus )) OR (in_array( "18" , $getEmployeeStatus )) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){
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
				
				case "delete_kandidat_appt":
					$kandidat_id 	= $_POST['kandidat_id'];
					$pca_id 		= $_POST['pca_id'];
					$time			= $_POST['time'];
					$appt_id		= $_POST['appt_id'];
					
					$query_delete_kandidat_appt = $db->prepare("
																DELETE FROM 
																	idk_pp_cand_appts
																WHERE
																	pca_id = :pca_id
															");
					$query_delete_kandidat_appt->execute(array(
						":pca_id" => $pca_id
					));
					
					//Add to LOGS
					$log_desc = "Obrisan termin kod kandidata " .$kandidat_id. "(termin id = ".$appt_id." i vrijeme = ".$time.")" ;
					$log_type = "0";
					addToLogs($log_desc, $log_type); //Log za biljeske - $log_type = 3
					
					header("Location: kandidati?page=open&id=$kandidat_id");
				break;

				case "add_kandidat_kontakt":

					$kki_kandidat_id 	= $_POST['kki_kandidat_id'];
					$kki_kandidat_check = $_POST['kki_kandidat_check'];
					$kki_grupa 			= $_POST['kki_grupa'];

					if ($kki_grupa == 1) {

						$kki_naziv   = $_POST['kki_naziv'];
						$kki_podatak = $_POST['kki_podatak'];
						// var_dump();
						// exit();

						if ($_POST['kki_primary'] == '1' AND $_POST['kki_naziv'] == 'Mobilni') {

							$query = $db->prepare("
								SELECT kki_id FROM idk_kandidat_kontakt_info 
								WHERE kki_primary = 1 
								AND kki_grupa = 1
								AND kki_kandidat_id = :kki_kandidat_id
							");
	
							$query->execute(array(
								':kki_kandidat_id' => $kki_kandidat_id
							));
							
							$rows = $query->fetchAll();
	
							if (count($rows) > 0) {
								foreach ($rows as $row) {
	
									// Update postojeceg primary-a u idk_kandidat_kontakt_info na 0
									$query = $db->prepare("
										UPDATE idk_kandidat_kontakt_info
										SET kki_primary = 0
										WHERE kki_id = :kki_id
									");
	
									$query->execute(array(
										':kki_id' => $row['kki_id']
									));
	
								}
							}
	
							// Add kontakt info to db
							$query = $db->prepare("
								INSERT INTO idk_kandidat_kontakt_info
									(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id, kki_primary)
								VALUES
									(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id, 1)
							");
	
							$query->execute(array(
								':kki_grupa'       => $kki_grupa,
								':kki_naziv'       => $kki_naziv,
								':kki_podatak' 	   => $kki_podatak,
								':kki_kandidat_id' => $kki_kandidat_id
							));
	
							// Update postojece kontakt informacije u idk_kandidati na novi primary
							$query = $db->prepare("
								UPDATE idk_kandidati
								SET kandidat_mobitel = :kki_podatak
								WHERE kandidat_id = :kki_kandidat_id
							");
	
							$query->execute(array(
								':kki_podatak'     => $kki_podatak,
								':kki_kandidat_id' => $kki_kandidat_id
							));
	
						} else if ($_POST['kki_primary'] == '0') {
	
							// Add kontakt info to db
							$query = $db->prepare("
								INSERT INTO idk_kandidat_kontakt_info
									(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id, kki_primary)
								VALUES
									(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id, 0)
							");
	
							$query->execute(array(
								':kki_grupa' => $kki_grupa,
								':kki_naziv' => $kki_naziv,
								':kki_podatak' => $kki_podatak,
								':kki_kandidat_id' => $kki_kandidat_id
							));
						
						}

					} else if ($kki_grupa == 2) {

						$kki_naziv   = "E-mail";
						$kki_podatak = $_POST['kki_naziv_email'];

						if ($_POST['kki_primary'] == '1') {

							$query = $db->prepare("
								SELECT kki_id FROM idk_kandidat_kontakt_info
								WHERE kki_primary = 1
								AND kki_grupa = 2
								AND kki_kandidat_id = :kki_kandidat_id
							");
	
							$query->execute(array(
								':kki_kandidat_id' => $kki_kandidat_id
							));
							
							$rows = $query->fetchAll();
	
							if (count($rows) > 0) {
								foreach($rows as $row) {
	
									// Update postojeceg primary-a na 0
									$query = $db->prepare("
										UPDATE idk_kandidat_kontakt_info
										SET kki_primary = 0
										WHERE kki_id = :kki_id
									");
	
									$query->execute(array(
										':kki_id' => $row['kki_id']
									));
	
								}
							}
	
							// Add kontakt info to db
							$query = $db->prepare("
								INSERT INTO idk_kandidat_kontakt_info
									(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id, kki_primary)
								VALUES
									(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id, 1)
							");
	
							$query->execute(array(
								':kki_grupa'       => $kki_grupa,
								':kki_naziv' 	   => $kki_naziv,
								':kki_podatak' 	   => $kki_podatak,
								':kki_kandidat_id' => $kki_kandidat_id
							));
	
							// Update postojece kontakt informacije u idk_kandidati na novi primary
							$query = $db->prepare("
								UPDATE idk_kandidati
								SET kandidat_email = :kki_podatak
								WHERE kandidat_id = :kki_kandidat_id
							");
	
							$query->execute(array(
								':kki_podatak'     => $kki_podatak,
								':kki_kandidat_id' => $kki_kandidat_id
							));
	
						} else if ($_POST['kki_primary'] == '0') {
	
							// Add kontakt info to db
							$query = $db->prepare("
								INSERT INTO idk_kandidat_kontakt_info
									(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id, kki_primary)
								VALUES
									(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id, 0)
							");

							$query->execute(array(
								':kki_grupa' 	   => $kki_grupa,
								':kki_naziv' 	   => $kki_naziv,
								':kki_podatak'     => $kki_podatak,
								':kki_kandidat_id' => $kki_kandidat_id
							));
	
						}

					} else if ($kki_grupa == 3) {

						$kki_naziv   = "Web";
						$kki_podatak = $_POST['kki_naziv_web'];

						$query = $db->prepare("
							INSERT INTO idk_kandidat_kontakt_info
								(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id, kki_primary)
							VALUES
								(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id, 0)
						");

						$query->execute(array(
							':kki_grupa'       => $kki_grupa,
							':kki_naziv'       => $kki_naziv,
							':kki_podatak'     => $kki_podatak,
							':kki_kandidat_id' => $kki_kandidat_id
						));

					} else if ($kki_grupa == 4) {

						$kki_naziv   = $_POST['kki_naziv_messangeri'];
						$kki_podatak = $_POST['kki_podatak_messangeri'];

						$query = $db->prepare("
							INSERT INTO idk_kandidat_kontakt_info
								(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id, kki_primary)
							VALUES
								(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id, 0)
						");

						$query->execute(array(
							':kki_grupa' 	   => $kki_grupa,
							':kki_naziv'	   => $kki_naziv,
							':kki_podatak' 	   => $kki_podatak,
							':kki_kandidat_id' => $kki_kandidat_id
						));

					} else header("Location: registracija/korak2/$kki_kandidat_id/$kki_kandidat_check");

					header("Location: kandidati?page=open&id=$kki_kandidat_id");

				break;

				case "add_kandidat_edukacija":

						$ke_datumod	 = date("Y-m-d", strtotime("01-".$_POST['ke_datumod']));

						if(isset($_POST['ke_datumdo_aktuelno'])){
							$ke_datumdo = NULL;
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
						$ke_skola_id = $_POST['ke_skola_id'];
						$ke_smjer_id = $_POST['ke_smjer_id'];
						$ke_aktuelno = (isset($_POST['ke_datumdo_aktuelno'])) ? 1 : 0;
						
						//Add user to db
						$query = $db->prepare("
										INSERT INTO idk_kandidat_edukacija
											(ke_datumod	, ke_datumdo, ke_naziv_kvalifikacije, ke_naziv, ke_grad, ke_drzava, ke_opis, ke_kandidat_id, ke_skola_id, ke_smjer_id, ke_aktuelno)
										VALUES
											(:ke_datumod, :ke_datumdo, :ke_naziv_kvalifikacije, :ke_naziv, :ke_grad, :ke_drzava, :ke_opis, :ke_kandidat_id, :ke_skola_id, :ke_smjer_id, :ke_aktuelno)");

						$query->execute(array(
									':ke_datumod' => $ke_datumod	,
									':ke_datumdo' => $ke_datumdo,
									':ke_naziv_kvalifikacije' => $ke_naziv_kvalifikacije,
									':ke_naziv' => $ke_naziv,
									':ke_grad' => $ke_grad,
									':ke_drzava' => $ke_drzava,
									':ke_opis' => $ke_opis,
									':ke_kandidat_id' => $ke_kandidat_id,
									':ke_skola_id' => $ke_skola_id,
									':ke_smjer_id' => $ke_smjer_id,
									':ke_aktuelno' => $ke_aktuelno,
									));

									header("Location: kandidati?page=open&id=$ke_kandidat_id");

				break;

				case "add_kandidat_iskustvo":

						$kri_darum_od = date("Y-m-d", strtotime("01-".$_POST['kri_darum_od']));
						
						if(isset($_POST['kri_datum_do_aktuelno'])){
							$kri_datum_do = null;
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

						$kri_opis_de = $_POST['kri_opis_de'];
						$kri_kandidat_id = $_POST['kri_kandidat_id'];
						$kri_kandidat_check = $_POST['kri_kandidat_check'];
						$kri_aktuelno = (isset($_POST['kri_datum_do_aktuelno'])) ? 1 : 0;

						//Add user to db
						$query = $db->prepare("
										INSERT INTO idk_kandidat_radno_iskustvo
											(kri_darum_od, kri_datum_do, kri_pozicija, kri_naziv, kri_grad, kri_drzava, kri_adresa, kri_telefon, kri_email, kri_web, kri_opis_de, kri_kandidat_id, kri_aktuelno)
										VALUES
											(:kri_darum_od, :kri_datum_do, :kri_pozicija, :kri_naziv, :kri_grad, :kri_drzava, :kri_adresa, :kri_telefon, :kri_email, :kri_web, :kri_opis_de, :kri_kandidat_id, :kri_aktuelno)");

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
									':kri_opis_de' => $kri_opis_de,
									':kri_kandidat_id' => $kri_kandidat_id,
									':kri_aktuelno' => $kri_aktuelno));

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
						$kj_ustanova = $_POST['kj_ustanova'];
						/* 
						$kj_citanje = $_POST['kj_citanje'];
						$kj_govorna_interakcija = $_POST['kj_govorna_interakcija'];
						$kj_govorna_produkcija = $_POST['kj_govorna_produkcija'];
						$kj_pisanje = $_POST['kj_pisanje'];
						*/

						$kj_kandidatid = $_POST['kj_kandidatid'];
						$kj_kandidat_check = $_POST['kj_kandidat_check'];


						//Add user to db
						$query = $db->prepare("
										INSERT INTO idk_kandidat_jezici
											(kj_naziv, kj_slusanje, kj_citanje, kj_govorna_interakcija, kj_govorna_produkcija, kj_pisanje, kj_kandidatid,kj_ustanova)
										VALUES
											(:kj_naziv, :kj_slusanje, :kj_citanje, :kj_govorna_interakcija, :kj_govorna_produkcija, :kj_pisanje, :kj_kandidatid, :kj_ustanova)");

						$query->execute(array(
									':kj_naziv' => $kj_naziv,
									':kj_slusanje' => $kj_slusanje,
									':kj_citanje' => $kj_slusanje,
									':kj_govorna_interakcija' => $kj_slusanje,
									':kj_govorna_produkcija' => $kj_slusanje,
									':kj_pisanje' => $kj_slusanje,
									':kj_kandidatid' => $kj_kandidatid,
									':kj_ustanova' => $kj_ustanova));
						
						$query_deactivate_active_cvl= $db->prepare("
									UPDATE idk_candidate_verified_languages join idk_kandidat_jezici on idk_candidate_verified_languages.cvl_id=idk_kandidat_jezici.kj_id 
									SET idk_candidate_verified_languages.cvl_active=0 
									WHERE idk_kandidat_jezici.kj_kandidatid=:kj_kandidatid AND idk_candidate_verified_languages.cvl_active=1 AND kj_naziv LIKE '%Njemacki%'");
				
						$query_deactivate_active_cvl->execute(array(
									':kj_kandidatid'=>$kj_kandidatid
						));

						header("Location: kandidati?page=open&id=$kj_kandidatid");

				break;
				
				case "add_kandidat_potencijalni_pocetak_rada":
					$kandidat_id = $_POST['kandidat_id'];
					$potencijalni_pocetak_rada = $_POST['kandidat_potencijalni_pocetak_rada'];
					
					$query_update_potencijalni_pocetak_rada = $db->prepare("
																			UPDATE
																				idk_kandidati
																			SET
																				kandidat_potencijalni_pocetak_rada = :potencijalni_pocetak_rada
																			WHERE
																				kandidat_id = :kandidat_id
																			");
					$query_update_potencijalni_pocetak_rada->execute(array(
						":potencijalni_pocetak_rada" 	=> $potencijalni_pocetak_rada,
						":kandidat_id" 					=> $kandidat_id
					));
					
					//Add to LOGS
					$log_desc = "Postavio potencijalni pocetak rada ".$potencijalni_pocetak_rada." za kandidata " .$kandidat_id. "";
					$log_type = "0";
					addToLogs($log_desc, $log_type); //Log za biljeske - $log_type = 3
					header("Location: kandidati?page=open&id=$kandidat_id");
				break;
				case "reorderKKInfo":
				
					$project_ids = $_POST["ids"];
					
					$idArray = explode(",",$project_ids);
					
					$count = 1;
					foreach ($idArray as $id){
							
						$query = $db->prepare("
										UPDATE idk_kandidat_kontakt_info
										SET	kki_orderid = :kki_orderid
										WHERE kki_id = :kki_id");
							
						$query->execute(array(
								':kki_orderid' => $count,
								':kki_id' => $id
								));			
							
					
					$count ++;    
					
					}
					return TRUE;
					
				break;
				
				case "reorderEdukacijaInfo":
				
					$project_ids = $_POST["ids"];
					
					$idArray = explode(",",$project_ids);
					
					$count = 1;
					foreach ($idArray as $id){
							
						$query = $db->prepare("
										UPDATE idk_kandidat_edukacija
										SET	ke_orderid = :ke_orderid
										WHERE ke_id = :ke_id");
							
						$query->execute(array(
								':ke_orderid' => $count,
								':ke_id' => $id
								));			
							
					
					$count ++;    
					
					}
					return TRUE;
					
				break;	

				case "reorderRadnoIskustvoInfo":
				
					$project_ids = $_POST["ids"];
					
					$idArray = explode(",",$project_ids);
					
					$count = 1;
					foreach ($idArray as $id){
							
						$query = $db->prepare("
										UPDATE idk_kandidat_radno_iskustvo
										SET	kri_order = :kri_order
										WHERE kri_id = :kri_id");
							
						$query->execute(array(
								':kri_order' => $count,
								':kri_id' => $id
								));			
							
					
					$count ++;    
					
					}
					return TRUE;
					
				break;					


				case "idk_kandidat_jezici_lista":
				
					$query = $db->prepare("
									SELECT * FROM `idk_kandidat_jezici` GROUP BY `kj_kandidatid`
									");

					$query->execute();

					$i = 1;
					while($row = $query->fetch()){

						$kj_kandidatid = $row['kj_kandidatid'];
						
					echo "".$i++."     ".$kj_kandidatid."";
						echo "<br/>";
						
						
					$query2 = $db->prepare("
									SELECT * FROM idk_kandidat_jezici
									WHERE kj_kandidatid = $kj_kandidatid
									");

					$query2->execute();

				
					while($row2 = $query2->fetch()){

						$kj_naziv = $row2['kj_naziv'];
						
						echo "------------- ".$kj_naziv."";
						echo "<br/>";
					}	
						
						echo "<br/>";echo "<br/>";
						
					}
				
				
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
