<?php

if (isset($_SERVER['HTTP_USER_AGENT']))
{
    $http_user_agent = $_SERVER['HTTP_USER_AGENT']; 
    if (preg_match('/Word|Excel|PowerPoint|ms-office/i', $http_user_agent)) 
    {
        // Prevent MS office products detecting the upcoming re-direct .. forces them to launch the browser to this link
		echo "sdfsdfdf";
		exit();	
    }
	

}

include("includes/functions.php");
include("includes/common.php");

$getEmployeeStatus = explode( ',' , getEmployeeStatus());
$employee_supervizor = explode( ',' , getEmployeeSupervizor());
$team_id = getLoggedEmployeeTeam();

if(isset($_REQUEST["page"])) {
	$page = $_REQUEST["page"];
}else{
	header("Location: finances?page=info");
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Financije | <?php getTitle(); ?></title>

	<?php include('includes/head.php'); ?>
	<style>
		.lds-hourglass {
			position: absolute;
			width: 150px;
			height: 150px;
			top: 50%;
			left: 50%;
			margin-top: -75px; 
			margin-left: -75px;			
			background: radial-gradient(#5cb85c, white);
			border-radius: 100px;
		}
		.lds-hourglass:after {
			top: 50%;
			left: 50%;
			content: " ";
			display: block;
			border-radius: 50%;
			width: 0;
			height: 0;
			margin: 8px;
			box-sizing: border-box;
			border: 66px solid #BAEC84;
			border-color: #4cae4c transparent #4cae4c transparent;
			animation: lds-hourglass 2.0s infinite;
		}
		.lds-hourglass_min {
			position: relative;
			width: 36px;
			height: 36px;
			// top: 50%;
			left: 50%;
			// margin-top: -10px; 
			margin-left: -10px;			
			background: radial-gradient(#5cb85c, white);
			border-radius: 100px;
		}
		.lds-hourglass_min:after {
			// top: 50%;
			left: 50%;
			content: " ";
			display: block;
			border-radius: 50%;
			width: 0;
			height: 0;
			margin: 1 px;
			box-sizing: border-box;
			border: 18px solid #BAEC84;
			border-color: #4cae4c transparent #4cae4c transparent;
			animation: lds-hourglass 2.0s infinite;
		}
		@keyframes lds-hourglass {
		  0% {
			transform: rotate(0);
			animation-timing-function: cubic-bezier(0.55, 0.055, 0.675, 0.19);
		  }
		  50% {
			transform: rotate(900deg);
			animation-timing-function: cubic-bezier(0.215, 0.61, 0.355, 1);
		  }
		  100% {
			transform: rotate(1800deg);
		  }
		}
		td,th{
			text-align: center!important;
		}
	</style>

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

				case "info":
				if((in_array( "9" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){
					
					$f_from = $_GET['f_from'];
					
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
					
					
					$start = $month = strtotime($f_from_f);
					$end = strtotime($f_to_f);
					while($month < $end){
						//	echo date('F Y', $month), PHP_EOL; echo "<br/>";
						$month = strtotime("+1 month", $month);
					}
		?>
			<div class="row">
				<div class="col-xs-12">
					<h1><i class="fa fa-money idk_color_green" aria-hidden="true"></i> Financije</h1>
				</div>
				<div class="col-xs-12">
					<hr/>
				</div>
			</div>
			
			<!-- EXPORT FINACIJE -->
			<div id="exportajax"></div>
			<script>
				$(document).ready(function() {
					$("#alertLoader").hide();
					
				});				
			</script>			
			<div id="myTabs2" class="panel-group material-tabs-group">
				<ul class="nav nav-tabs material-tabs material-tabs_primary">
					<li class="active"><a href="#info" class="material-tabs__tab-link" data-toggle="tab">Informacije</a></li>
					<li><a href="#detailed_info" class="material-tabs__tab-link" data-toggle="tab">Detaljne informacije</a></li>
				</ul>
				<div class="tab-content materail-tabs-content" style="padding: 0">
					<!-- INFORMACIJE -->
					<div class="tab-pane fade active in" id="info">
						<div class="row">
							<div class="col-md-12">
								<div class="content_box">
									<div class="row">
										<div id="alertLoader" class="alert material-alert material-alert_danger"><img src="images/smallLoader.svg" width=27 style="margin-right: 20px;"> Financijski izvještaj se generiše. Molimo pričekajte.</div>
										<form action="<?php getSiteUrl(); ?>finances" enctype="multipart/form-data" method="get" accept-charset="utf-8" role="form" class="form-horizontal">
										<input type="hidden" name="page" value="info">
											<div class="col-sm-3">
												<div class="form-group materail-input-block materail-input-block_primary materail-input_slide-line">
													<input type="text" class="form-control" name="f_from" id="f_from" placeholder="Datum" style="padding:17px;border-radius:0;" required>
													<span class="materail-input-block__line"></span>
												</div>
											</div>
											<div class="col-sm-3">
												<button class="btn btn-success financijeSearchBUtton">Traži</button>
											</div>
											<div class="col-sm-6">
												<?php if(isset($_GET['f_from'])){ ?>
												<button class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive ajaxexport pull-right" style="margin-left: 15px;" data-exportokvirni="0"><i class="fa fa-file-excel-o" aria-hidden="true"></i> <span>Export okvirni</span></button> 
												<button class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive ajaxexport pull-right" data-exportokvirni="1"><i class="fa fa-file-excel-o" aria-hidden="true"></i> <span>Export detaljni</span></button>

												<?php }else{} ?>
											</div>
										</form>
										<script>
											$("#f_from").flatpickr({
												mode: "range",
												dateFormat: "d.m.Y",
												disableMobile: "true"
											});							
										</script>	
									</div>
									
									<?php if(isset($_GET['f_from'])){ ?>
									<br/><hr><br/>
									<input type="hidden" id="selected_rows_kandidates"></input>
									
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
													var table = $('#idk_table').DataTable({
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
														"order": [[ 1, "desc" ]],
														"bAutoWidth": false,
														"aoColumns": [
																{ "width": "5%", "bSortable": false },
																{ "width": "10%" },
																{ "width": "20%" },
																{ "width": "25%" },
																{ "width": "15%" },
																{ "width": "15%" },
																{ "width": "10%", "bSortable": false }
															]
													});
													
													// SELECT IDS AND EXPORT SELECTED
													$('.ajaxexport').on('click', function(e){
														var exportokvirni = $(this).data('exportokvirni');
														
														var form = $("#frm-addToProj");
														
														var rows_selected = table.column(0).checkboxes.selected();
													
														// Iterate over all selected checkboxes
														$.each(rows_selected, function(index, rowId){
															$(form).append(
																$('<input>')
																.attr('type', 'hidden')
																.attr('name', 'id[]')
																.val(rowId)
															);
														});
														
														// Output form data to a console     
														$('#selected_rows_kandidates').val(rows_selected.join(","));
														var selectedIds = $('#selected_rows_kandidates').val();
													
														$("#alertLoader").show("fast");
														$('#exportajax').load('export_excel.php?prozor=export_financije_excel&datumod=<?php echo $f_from_f; ?>&datumdo=<?php echo $f_to_f; ?>&exportokvirni='+exportokvirni+'&nalogids='+selectedIds+'', function() {
															$("#alertLoader").hide("slow");
														});
														return false;
				
														e.preventDefault();
													}); 													
				
													$('#table-filter').on('change', function(){
														table.search(this.value).draw();   
													});
												} );
											</script>
											<table id="idk_table" class="display" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th class="text-center"></th>
														<th class="text-center">Broj</th>
														<th class="text-center">Naziv</th>
														<th class="text-center">Kompanija</th>
														<th class="text-center">Status</th>
														<th class="text-center">Kreirano</th>
														<th></th>
													</tr>
												</thead>
												<tbody>
													<?php
														$query = $db->prepare("
																SELECT nalog_id, kompanija_id, nalog_broj, nalog_naziv, nalog_opis, nalog_kreirano, nalog_status, comp.company_name
																FROM idk_nalozi
																INNER JOIN idk_companies comp ON kompanija_id = comp.company_id
																WHERE nalog_status != :nalog_status AND nalog_financije = :nalog_financije");
				
														$query->execute(array(
																':nalog_status' => 8,
																':nalog_financije' => 1
																));
				
														while($row = $query->fetch()){
				
															$nalog_id = $row['nalog_id'];
															$nalog_broj = $row['nalog_broj'];
															$kompanija_id = $row['kompanija_id'];
															$nalog_naziv = $row['nalog_naziv'];
															$kompanija = $row['company_name'];
															$nalog_status = $row['nalog_status'];
															$nalog_kreirano = date('d.m.Y.', strtotime($row['nalog_kreirano']));
				
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
															}
															
													?>
													<tr>
														<td class="text-center"><?php echo $nalog_id; ?></td>
														<td class="text-center"><?php echo $nalog_broj; ?></td>
														<td class="text-center"><?php echo $nalog_naziv; ?></td>
														<td class="text-center"><a href="<?php getSiteURL(); ?>companies?page=open&id=<?php echo $kompanija_id; ?>"><?php echo $kompanija; ?></a></td>
														<td class="text-center"><?php echo $nalog_status_txt; ?></td>
														<td class="text-center"><?php echo $nalog_kreirano; ?></td>
														<td class="text-center"><a href="<?php getSiteURL(); ?>nalozi?page=open&id=<?php echo $nalog_id; ?>" class="btn material-btn material-btn_success main-container__column"><i class="fa fa-info-circle" aria-hidden="true"></i></a></td>
													</tr>
													<?php } ?>
												</tbody>
											</table>
										</div>										
									</div>
									<?php } ?>
								</div>
							</div>
						</div>
					</div>
					<!-- Export -->
					<div class="tab-pane fade" id="export">
						<div class="row">
							<div class="col-md-12">
								<div class="content_box">
									<div class="row">
										<div class="col-sm-12">
											<h5>Export financijskih informacija</h5>
										</div>
									</div>
									<form id="idk_form" action="<?php getSiteURL(); ?>do.php?form=export_finances" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<div class="row">
														<label class="col-sm-3 control-label"><span class="text-danger">*</span>Odaberite vrstu naloga za export:</label>
														<select class="col-sm-6 materail-input materail-input-custom" name="nalog_ugovor" id="nalog_ugovor" required>
														<option value="okvirni"> Okvirni</option>
														<option value="nalozi"> Nalozi</option>
														<option value="svi"> Svi</option>
													</div>
												</div>
												<div class="form-group">
													<div class="row">
														<label class="col-sm-3 control-label"><span class="text-danger">*</span>Od:</label>
														<input class="col-sm-6 form-control materail-input" type="text" name="" id="export_od" value="22">
													</div>
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
					<div class="tab-pane fade" id="detailed_info">
						<?php 
						$nalog_id = $_GET['nalogid'];
						$mjesec = $_GET['mjesec'];
						$godina = $_GET['godina'];
						if($nalog_id != ""){
							$postojanje_odmah = false;
							$postojanje_ugovor = false;
							$postojanje_pocetak = false;
							$postojanje_mn_prva = false;
							$postojanje_mn_druga = false;
							$query = $db->prepare("
											SELECT nalog_broj, nalog_naziv, nalog_provizija, nalog_potrebno_kandidata, nalog_datum_potpisa_naloga, nalog_placena_prva_rata
											FROM idk_nalozi
											WHERE nalog_id = :nalog_id AND nalog_financije = :nalog_financije");
											
							$query->execute(array(
											':nalog_id' => $nalog_id,
											':nalog_financije' => 1
											));
		
							$row = $query->fetch();
		
							$nalog_broj = $row['nalog_broj'];
							$nalog_naziv = $row['nalog_naziv'];
							$provizija = $row['nalog_provizija'];
							$nalog_potrebno_kandidata = $row['nalog_potrebno_kandidata'];
							$nalog_datum_potpisa_naloga = $row['nalog_datum_potpisa_naloga'];
							$nalog_placena_prva_rata = $row['nalog_placena_prva_rata'];
								
							$url = getSiteUrlr();
							$url_nalog = "".$url."nalozi?page=open&id=".$nalog_id."";
							
							$zadnja_rata = "";
							// PROCENAT ZA ODMAH UGOVOR
							$query_nalog_ugovor = $db->prepare("
											SELECT nr_procenat, nr_datum, nr_vrijeme_placanja
											FROM idk_nalozi_rate
											WHERE nr_nalog = :nr_nalog AND nr_vrijeme_placanja = :nr_vrijeme_placanja
											");
							
							$query_nalog_ugovor->execute(array(
								'nr_nalog' => $nalog_id,
								'nr_vrijeme_placanja' => "odmah"
								));
								
							$number_of_rows_odmah = $query_nalog_ugovor->rowCount();
							if($number_of_rows_odmah > 0)
								$postojanje_odmah = true;
							
							$query_ugovor = $query_nalog_ugovor->fetch();
							$odmah_procenat = $query_ugovor['nr_procenat'];					
							$ugovor_nr_datum = $query_ugovor['nr_datum'];
							$ugovor_nr_datum_f = date("mY", strtotime( $query_ugovor['nr_datum']));
							$ugovor_nr_datum_mjesec = date("m", strtotime( $query_ugovor['nr_datum']));
							$cijena_odmah = 0;
							if($ugovor_nr_datum_mjesec == $mjesec)
								$cijena_odmah = ($nalog_potrebno_kandidata * $provizija / 100 * $odmah_procenat);
							
							//NAZIV ZADNJE RATE RADI STATUSA ZAVRSEN(SAMO NA ZADNJOJ RATI)
							if($query_ugovor['nr_vrijeme_placanja'] !== null)
								$zadnja_rata = $query_ugovor['nr_vrijeme_placanja'];
							
							// PROCENAT ZA POTPIS UGOVORA
							$query_nalog_rate = $db->prepare("
											SELECT nr_procenat, nr_vrijeme_placanja
											FROM idk_nalozi_rate
											WHERE nr_nalog = :nr_nalog AND nr_vrijeme_placanja = :nr_vrijeme_placanja
											");
			
							$query_nalog_rate->execute(array(
								'nr_nalog' => $nalog_id,
								'nr_vrijeme_placanja' => "ugovor"
								));
							
							$number_of_rows_ugovor = $query_nalog_rate->rowCount();
							if($number_of_rows_ugovor > 0)
								$postojanje_ugovor = true;
							
							$ugovor_p = $query_nalog_rate->fetch();
								$ugovor_procenat = $ugovor_p['nr_procenat'];
								
							//NAZIV ZADNJE RATE RADI STATUSA ZAVRSEN(SAMO NA ZADNJOJ RATI)
							if($ugovor_p['nr_vrijeme_placanja'] !== null)
								$zadnja_rata = $ugovor_p['nr_vrijeme_placanja'];
								
							// PROCENAT ZA POCETAK RADA
							$query_nalog_pocetakrada = $db->prepare("
											SELECT nr_procenat, nr_vrijeme_placanja
											FROM idk_nalozi_rate
											WHERE nr_nalog = :nr_nalog AND nr_vrijeme_placanja = :nr_vrijeme_placanja
											");
			
							$query_nalog_pocetakrada->execute(array(
								'nr_nalog' => $nalog_id,
								'nr_vrijeme_placanja' => "pocetak rada"
								));
							
							$number_of_rows_pocetak = $query_nalog_pocetakrada->rowCount();
							if($number_of_rows_pocetak > 0)
								$postojanje_pocetak = true;
							
							$pocetakrada_p = $query_nalog_pocetakrada->fetch();
								$pocetakrada_procenat = $pocetakrada_p['nr_procenat'];	
								
							//NAZIV ZADNJE RATE RADI STATUSA ZAVRSEN(SAMO NA ZADNJOJ RATI)
							if($pocetakrada_p['nr_vrijeme_placanja'] !== null)
								$zadnja_rata = $pocetakrada_p['nr_vrijeme_placanja'];
								
							// PROCENAT ZA MJESECI NAKON
							$query_nalog_mjeseci_nakon = $db->prepare("
											SELECT nr_procenat, nr_mjeseci_nakon, nr_vrijeme_placanja
											FROM idk_nalozi_rate
											WHERE nr_nalog = :nr_nalog AND nr_vrijeme_placanja = :nr_vrijeme_placanja
											");
			
							$query_nalog_mjeseci_nakon->execute(array(
								'nr_nalog' => $nalog_id,
								'nr_vrijeme_placanja' => "mjeseci nakon"
								));
							
							$number_of_rows_nm_prva = $query_nalog_mjeseci_nakon->rowCount();
							if($number_of_rows_nm_prva > 0)
								$postojanje_mn_prva = true;
							
							$row_mjeseci_nakon = $query_nalog_mjeseci_nakon->fetch();
								$mjeseci_nakon_procenat = $row_mjeseci_nakon['nr_procenat'];		
								$nr_mjeseci_nakon = $row_mjeseci_nakon['nr_mjeseci_nakon'];
								if($nr_mjeseci_nakon == 1)
									$mjesec_sufix = "mjesec";
								else if($nr_mjeseci_nakon == 2 or $nr_mjeseci_nakon == 3 or $nr_mjeseci_nakon == 4)
									$mjesec_sufix = "mjeseca";
								else
									$mjesec_sufix = "mjeseci";
								
							//NAZIV ZADNJE RATE RADI STATUSA ZAVRSEN(SAMO NA ZADNJOJ RATI)
							if($row_mjeseci_nakon['nr_vrijeme_placanja'] !== null)
								$zadnja_rata = $row_mjeseci_nakon['nr_vrijeme_placanja'];

							// BROJ RATA NAKON POCETKA RADA
							$query_nalog_broj_rata_npr = $db->prepare("
											SELECT COUNT(nr_id) as broj_rata_npr
											FROM idk_nalozi_rate
											WHERE nr_nalog = :nr_nalog AND nr_vrijeme_placanja = :nr_vrijeme_placanja
											");
							$query_nalog_broj_rata_npr->execute(array(
								'nr_nalog' => $nalog_id,
								'nr_vrijeme_placanja' => "mjeseci nakon"
								));
							$row_brnpr = $query_nalog_broj_rata_npr->fetch();
							$broj_rata_npr = $row_brnpr['broj_rata_npr'];
							
							if($broj_rata_npr == 2){
								$query_nalog_mjeseci_nakon2 = $db->prepare("
												SELECT nr_procenat, nr_mjeseci_nakon, nr_vrijeme_placanja
												FROM idk_nalozi_rate
												WHERE nr_nalog = :nr_nalog AND nr_vrijeme_placanja = :nr_vrijeme_placanja
												ORDER BY nr_mjeseci_nakon DESC
												");
				
								$query_nalog_mjeseci_nakon2->execute(array(
									'nr_nalog' => $nalog_id,
									'nr_vrijeme_placanja' => "mjeseci nakon"
									));
									
								$number_of_rows_nm_druga = $query_nalog_mjeseci_nakon2->rowCount();
								if($number_of_rows_nm_prva > 0)
									$postojanje_mn_druga = true;
								
								$row_mjeseci_nakon2 = $query_nalog_mjeseci_nakon2->fetch();
									$mjeseci_nakon_procenat2 = $row_mjeseci_nakon2['nr_procenat'];		
									$nr_mjeseci_nakon2 = $row_mjeseci_nakon2['nr_mjeseci_nakon'];
									
								if($nr_mjeseci_nakon2 == 1)
									$mjesec_sufix = "mjesec";
								else if($nr_mjeseci_nakon2 == 2 or $nr_mjeseci_nakon2 == 3 or $nr_mjeseci_nakon2 == 4)
									$mjesec_sufix2 = "mjeseca";
								else
									$mjesec_sufix2 = "mjeseci";
								
								//NAZIV ZADNJE RATE RADI STATUSA ZAVRSEN(SAMO NA ZADNJOJ RATI)
								if($row_mjeseci_nakon2['nr_vrijeme_placanja'] !== null)
									$zadnja_rata = $row_mjeseci_nakon2['nr_vrijeme_placanja'].$nr_mjeseci_nakon2;
							}
							
							$cijena_ugovor = ($nalog_potrebno_kandidata * $provizija / 100 * $ugovor_procenat);
						?>
						<script>$(function() { $('[href="#detailed_info"]').tab('show'); });</script>
						<input type="hidden" id="nalog_id" value="<?php echo $nalog_id; ?>">
						<div class="row">
							<div class="col-md-12">
								<div class="content_box">
									<div class="row">
										<div class="col-sm-10">
											<div class="row">
												<div class="col-sm-12">
													<h5>Detaljne informacije o nalogu za mjesec: <?php echo $mjesec; echo "/".$godina; ?></h5>
												</div>
											</div>
											
											<div class="row">
												<strong class="col-sm-3 text-right">Broj naloga:</strong>
												<div class="col-sm-8"><a href="<?php echo $url_nalog; ?>"><?php echo $nalog_broj; ?></a></div>
											</div>
											<div class="row">
												<strong class="col-sm-3 text-right">Naziv naloga:</strong>
												<div class="col-sm-8"><a href="<?php echo $url_nalog; ?>"><?php echo $nalog_naziv; ?></a></div>
											</div>
											<div class="row">
												<strong class="col-sm-3 text-right">Broj potrebnih kandidata:</strong>
												<div class="col-sm-8"><?php echo $nalog_potrebno_kandidata; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-3 text-right">Provizija:</strong>
												<div class="col-sm-8"><?php echo $provizija; ?></div>
											</div>
											</br>
											<?php if($postojanje_odmah){ ?>
											<div class="row">
												<div class="col-sm-12">
													<h5>Rata: Odmah  <span style="font-weight: bold">(<?php echo $odmah_procenat?>%)</span></h5>
												</div>
											</div>
											<div class="row">
												<strong class="col-sm-3 text-right">Rata odmah:</strong>
												<?php if($cijena_odmah != 0){
													?> <div class="col-sm-1"> <?php echo $cijena_odmah; 
													?> 
													</div>
													<div class="col-sm-2">
														<span class="label <?php if($nalog_placena_prva_rata == 1){echo "label-success"; }else{ echo "label-default";} ?>" id="placena_rata_odmah" onclick="promjena_naplate_da_rata_odmah(this)" data-kf-id="<?php echo $nalog_id; ?>">PLAĆENO</span>
														<span style="<?php if($nalog_placena_prva_rata == 0 or $nalog_placena_prva_rata == 2) { ?> display: none; <?php } ?>" class="label label-success" id="datum_placanja_rata_odmah" ><?php echo date("d.m.Y", strtotime($nalog_datum_potpisa_naloga));?></span>
													</div>
													<div class="col-sm-2" >
														<span class="label  <?php if($nalog_placena_prva_rata == 0){echo "label-danger";}else{ echo "label-default";} ?>" id="nije_placena_rata_odmah" onclick="promjena_naplate_ne_rata_odmah(this)" data-kf-id="<?php echo $nalog_id; ?>">NIJE PLAĆENO</span>
													</div>
													<?php 
												}
												else{ ?> <div class="col-sm-2"> <?php echo "Nije u odabranom mjesecu"; ?> </div>
												<?php } ?>
												
												<script>
												
													function promjena_naplate_da_rata_odmah(t){
														var nalog_id = t.getAttribute("data-kf-id");
														if(t.classList.contains("label-default")){
															var placeno = 1;
															t.classList.remove("label-default");
															t.classList.add("label-success");
															var nije_placeno = document.getElementById("nije_placena_rata_odmah");
															nije_placeno.classList.remove("label-danger");
															nije_placeno.classList.add("label-default");
															$.ajax({
																url: 'ajax_data.php?page=rata_odmah_placeno_da',
																type: 'POST',    
																data: {'nalog_id':nalog_id, 'placeno':placeno},
																dataType: 'html',
																success: function(data) {
																	$('#datum_placanja_rata_odmah').show();
																}
															});
															//provjera_placeno();
														}
													}
													
													function promjena_naplate_ne_rata_odmah(t){
														var nalog_id = t.getAttribute("data-kf-id");
														if(t.classList.contains("label-default")){
															var placeno = 0;
															t.classList.remove("label-default");
															t.classList.add("label-danger");
															var nije_placeno = document.getElementById("placena_rata_odmah");
															nije_placeno.classList.remove("label-success");
															nije_placeno.classList.add("label-default");
															$.ajax({
																url: 'ajax_data.php?page=rata_odmah_placeno_ne',
																type: 'POST',    
																data: {'nalog_id':nalog_id, 'placeno':placeno},
																dataType: 'html',
																success: function(data) {
																	$('#datum_placanja_rata_odmah').hide();
																}
															});
															//provjera_placeno();
														}
													}
												
												</script>
												
											</div>
											</br>
											<?php } if($postojanje_ugovor){ ?>
											<div class="row">
												<div class="col-sm-12">
													<h5>Kandidati sa potpisanim ugovorom u odabranom mjesecu  <span style="font-weight: bold">(<?php echo $ugovor_procenat?>% - <?php echo $cijena_ugovor;?> EUR)</span></h5>
												</div>
											</div>
											<?php
											}
											$suma_cijena = 0;
											$array_kandidati_ugovor = array();
											$array_kandidati_pocetak_rada = array();
											$array_kandidati_mjeseci_nakon = array();
											$array_kandidati_mjeseci_nakon2 = array();
											$kf_placeno = array();
											$kandidat_id_ugovor = array();
											$kandidat_id_zamijenjeni = array();
											$zamijenjeni_placeno = array();
											$novi_kandidat = array();
											$kf_dp_ugovor = array();
											$kf_dp_pocetak = array();
											$kf_dp_mn = array();
											$kf_dp_mn2 = array();
											
											// KANDIDATI POTPISAN UGOVOR
											$query_zamjena = $db->prepare("
															SELECT kandidat_id, kf_type, kf_placeno, kf_zamjena_id
															FROM idk_kandidat_financije
															WHERE (nalog_id = :nalog_id) AND (kf_type = 1 AND kf_status = 2)");
															
											$query_zamjena->execute(array(
															':nalog_id' => $nalog_id
															));
															
											while($kandidat_zamjena = $query_zamjena->fetch()){
												$kandidat_id_zamijenjeni[] = $kandidat_zamjena['kandidat_id'];
												$zamijenjeni_placeno[] = $kandidat_zamjena['kf_placeno'];
												$novi_kandidat[] = $kandidat_zamjena['kf_zamjena_id'];
											}

											
											$broj_zamijenjenih_kandidata = count($novi_kandidat);

											$query_kandidati_financije = $db->prepare("
															SELECT kf_id, kandidat_id, kf_datum, kf_datum_stvarni, kf_type, kf_placeno, kf_status, kf_datum_placanja
															FROM idk_kandidat_financije
															WHERE (nalog_id = :nalog_id) AND (kf_type = 1 AND (kf_status = 1 OR kf_status = 3)) AND (MONTH(kf_datum) = :ugovormonth AND YEAR(kf_datum) = :ugovorgodina)");
											
											$query_kandidati_financije->execute(array(
															':nalog_id' => $nalog_id,
															':ugovormonth' => $mjesec,
															':ugovorgodina' => $godina
															));
											$j = 0;
											while($kandidat_financije = $query_kandidati_financije->fetch()){
												$kandidat_id_ugovor[] = $kandidat_financije['kandidat_id'];
												$kf_status[] = $kandidat_financije['kf_status'];
												$kf_id[] = $kandidat_financije['kf_id'];
												$kf_dp_ugovor[] = $kandidat_financije['kf_datum_placanja'];
												
												$query_kandidati = $db->prepare("
																SELECT kandidat_ime, kandidat_prezime
																FROM idk_kandidati
																WHERE kandidat_id = :kandidat_id");
											
												$query_kandidati->execute(array(
																':kandidat_id' => $kandidat_id_ugovor[$j] 
																));
												
												$kandidat = $query_kandidati->fetch();
												$kandidat_ime_ugovor = $kandidat['kandidat_ime'];
												$kandidat_prezime_ugovor = $kandidat['kandidat_prezime'];
												
												if($broj_zamijenjenih_kandidata != 0){
													for($k=0; $k<$broj_zamijenjenih_kandidata; $k++){
														if($kandidat_id_ugovor[$j] == $novi_kandidat[$k]){
															$kandidat_ugovor = "<a href='".$url."kandidati?page=open&id=".$kandidat_id_ugovor[$j]."' id='kandidat".$kandidat_id_ugovor[$j]."' class='kandidat_link'>".$kandidat_ime_ugovor." ".$kandidat_prezime_ugovor."</a><a href='".$url."kandidati?page=open&id=".$kandidat_id_zamijenjeni[$k]."'><span class='label label-primary' style='margin-left: 5px;'> <i class='fa fa-exchange' aria-hidden='true'></i></span></a>";
															if($zamijenjeni_placeno[$k] == "1" and $kandidat_financije['kf_placeno'] == 2){
																$kf_placeno[$j] = 1;
															}else{
																$kf_placeno[$j] = $kandidat_financije['kf_placeno'];
															}
															break;
														}elseif($kandidat_id_ugovor[$j] != $novi_kandidat[$k] and $k == ($broj_zamijenjenih_kandidata-1)){
															$kandidat_ugovor = "<a href='".$url."kandidati?page=open&id=".$kandidat_id_ugovor[$j]."' id='kandidat".$kandidat_id_ugovor[$j]."' class='kandidat_link'>".$kandidat_ime_ugovor." ".$kandidat_prezime_ugovor."</a>";
															$kf_placeno[$j] = $kandidat_financije['kf_placeno'];
														}else{}
													}
												}else{
													$kandidat_ugovor = "<a href='".$url."kandidati?page=open&id=".$kandidat_id_ugovor[$j]."' id='kandidat".$kandidat_id_ugovor[$j]."' class='kandidat_link'>".$kandidat_ime_ugovor." ".$kandidat_prezime_ugovor."</a>";
													$kf_placeno[$j] = $kandidat_financije['kf_placeno'];
												}
												$array_kandidati_ugovor[] = $kandidat_ugovor;
												$j++;
											}
											
											// KANDIDATI POČETAK RADA
											$query_zamjena_pocetak_rada = $db->prepare("
															SELECT kandidat_id, kf_type, kf_placeno, kf_zamjena_id
															FROM idk_kandidat_financije
															WHERE (nalog_id = :nalog_id) AND (kf_type = 2 AND kf_status = 2)");
															
											$query_zamjena_pocetak_rada->execute(array(
															':nalog_id' => $nalog_id
															));
															
											while($kandidat_zamjena_pocetak_rada = $query_zamjena_pocetak_rada->fetch()){
												$kandidat_id_zamijenjeni_pocetak_rada[] = $kandidat_zamjena_pocetak_rada['kandidat_id'];
												$zamijenjeni_placeno_pocetak_rada[] = $kandidat_zamjena_pocetak_rada['kf_placeno'];
												$novi_kandidat_pocetak_rada[] = $kandidat_zamjena_pocetak_rada['kf_zamjena_id'];
												echo $novi_kandidat_pocetak_rada[0];
											}

											
											$broj_zamijenjenih_kandidata_pocetak_rada = count($novi_kandidat_pocetak_rada);
											
											$query_kandidati_financije_pocetak_rada = $db->prepare("
															SELECT kf_id, kandidat_id, kf_datum, kf_datum_stvarni, kf_type, kf_placeno, kf_status, kf_datum_placanja
															FROM idk_kandidat_financije
															WHERE (nalog_id = :nalog_id) AND (kf_type = 2 AND (kf_status = 1 OR kf_status = 3)) AND (MONTH(kf_datum) = :ugovormonth AND YEAR(kf_datum) = :ugovorgodina)");
											
											$query_kandidati_financije_pocetak_rada->execute(array(
															':nalog_id' => $nalog_id,
															':ugovormonth' => $mjesec,
															':ugovorgodina' => $godina
															));
											$j = 0;
											while($kandidat_financije_pocetak_rada = $query_kandidati_financije_pocetak_rada->fetch()){
												$kandidat_id_pocetak_rada[] = $kandidat_financije_pocetak_rada['kandidat_id'];
												$kf_status_pocetak_rada[] = $kandidat_financije_pocetak_rada['kf_status'];
												$kf_id_pocetak_rada[] = $kandidat_financije_pocetak_rada['kf_id'];
												$kf_dp_pocetak[] = $kandidat_financije_pocetak_rada['kf_datum_placanja'];
												
												$query_kandidati_pocetak_rada = $db->prepare("
																SELECT kandidat_ime, kandidat_prezime
																FROM idk_kandidati
																WHERE kandidat_id = :kandidat_id");
											
												$query_kandidati_pocetak_rada->execute(array(
																':kandidat_id' => $kandidat_id_pocetak_rada[$j] 
																));
												
												$kandidat_pocetak_rada = $query_kandidati_pocetak_rada->fetch();
												$kandidat_ime_pocetak_rada = $kandidat_pocetak_rada['kandidat_ime'];
												$kandidat_prezime_pocetak_rada = $kandidat_pocetak_rada['kandidat_prezime'];
												if($broj_zamijenjenih_kandidata_pocetak_rada != 0){
													for($k=0; $k<$broj_zamijenjenih_kandidata_pocetak_rada; $k++){
														if($kandidat_id_pocetak_rada[$j] == $novi_kandidat_pocetak_rada[$k]){
															$kandidat_pocetak_rada = "<a href='".$url."kandidati?page=open&id=".$kandidat_id_pocetak_rada[$j]."' id='kandidat".$kandidat_id_pocetak_rada[$j]."' class='kandidat_link'>".$kandidat_ime_pocetak_rada." ".$kandidat_prezime_pocetak_rada."</a><a href='".$url."kandidati?page=open&id=".$kandidat_id_zamijenjeni_pocetak_rada[$k]."'><span class='label label-primary' style='margin-left: 5px;'> <i class='fa fa-exchange' aria-hidden='true'></i></span></a>";
															if($zamijenjeni_placeno_pocetak_rada[$k] == "1" and $kandidat_financije_pocetak_rada['kf_placeno'] == 2){
																$kf_placeno_pocetak_rada[$j] = 1;
															}elseif($zamijenjeni_placeno_pocetak_rada[$k] == "2" and $kandidat_financije_pocetak_rada['kf_placeno'] == 2){
																$kf_placeno_pocetak_rada[$j] = $kandidat_financije_pocetak_rada['kf_placeno'];
															}else{
																$kf_placeno_pocetak_rada[$j] = $kandidat_financije_pocetak_rada['kf_placeno'];
															}
															break;
														}elseif($kandidat_id_pocetak_rada[$j] != $novi_kandidat_pocetak_rada[$k] and $k == ($broj_zamijenjenih_kandidata_pocetak_rada-1)){
															$kandidat_pocetak_rada = "<a href='".$url."kandidati?page=open&id=".$kandidat_id_pocetak_rada[$j]."' id='kandidat".$kandidat_id_pocetak_rada[$j]."' class='kandidat_link'>".$kandidat_ime_pocetak_rada." ".$kandidat_prezime_pocetak_rada."</a>";
															$kf_placeno_pocetak_rada[$j] = $kandidat_financije_pocetak_rada['kf_placeno'];
														}else{}
													}
												}else{
													$kandidat_pocetak_rada = "<a href='".$url."kandidati?page=open&id=".$kandidat_id_pocetak_rada[$j]."' id='kandidat".$kandidat_id_pocetak_rada[$j]."' class='kandidat_link'>".$kandidat_ime_pocetak_rada." ".$kandidat_prezime_pocetak_rada."</a>";
													$kf_placeno_pocetak_rada[$j] = $kandidat_financije_pocetak_rada['kf_placeno'];
												}
												$array_kandidati_pocetak_rada[] = $kandidat_pocetak_rada;
												$j++;
											}
											
											// KANDIDATI POCETAK RADA 
											/* $query_kandidati_pr = $db->prepare("
															SELECT kandidat_id, kandidat_ime, kandidat_prezime, kandidat_spol, kandidat_jmbg, kandidat_status, kandidat_slika, kandidat_email, kandidat_datetime, kandidat_visitedurl, kandidat_prijava_na, kandidat_group, cv_ba, cv_de
															FROM idk_kandidati
															INNER JOIN idk_project_kandidati ON idk_kandidati.kandidat_id = idk_project_kandidati.pk_kandidatid
															WHERE (pk_projectid = :pk_projectid AND kandidat_status !=3) AND (MONTH(kandidat_datum_pocetakrada_mjesec) = :ugovormonth AND YEAR(kandidat_datum_pocetakrada_mjesec) = :ugovorgodina)");
											
											$query_kandidati_pr->execute(array(
											':pk_projectid' => $project_id,
											':ugovormonth' => $mjesec,
											':ugovorgodina' => $godina
											));
											
											while($kandidat_pr = $query_kandidati_pr->fetch()){
												$kandidat_id_pocetak = $kandidat_pr['kandidat_id'];
												$kandidat_ime_pocetak = $kandidat_pr['kandidat_ime'];
												$kandidat_prezime_pocetak = $kandidat_pr['kandidat_prezime'];
												$kandidat_pocetak = "<a href='".$url."kandidati?page=open&id=".$kandidat_id_pocetak."'>".$kandidat_ime_pocetak." ".$kandidat_prezime_pocetak."</a>";
												$array_kandidati_pocetak_rada[] = $kandidat_pocetak;
											} */
											
											// KANDIDATI POČETAK RADA NAKON # MJESECI
											$query_zamjena_mn = $db->prepare("
															SELECT kandidat_id, kf_type, kf_placeno, kf_zamjena_id
															FROM idk_kandidat_financije
															WHERE (nalog_id = :nalog_id) AND (kf_type = 3 AND kf_status = 2)");
															
											$query_zamjena_mn->execute(array(
															':nalog_id' => $nalog_id
															));
															
											while($kandidat_zamjena_mn = $query_zamjena_mn->fetch()){
												$kandidat_id_zamijenjeni_mn[] = $kandidat_zamjena_mn['kandidat_id'];
												$zamijenjeni_placeno_mn[] = $kandidat_zamjena_mn['kf_placeno'];
												$novi_kandidat_mn[] = $kandidat_zamjena_mn['kf_zamjena_id'];
											}

											
											$broj_zamijenjenih_kandidata_mn = count($novi_kandidat_mn);
													
											
											$query_kandidati_financije_mn = $db->prepare("
															SELECT kf_id, kandidat_id, kf_datum, kf_datum_stvarni, kf_type, kf_placeno, kf_status, kf_datum_placanja
															FROM idk_kandidat_financije
															WHERE (nalog_id = :nalog_id) AND (kf_type = 3 AND (kf_status = 1 OR kf_status = 3)) AND (MONTH(kf_datum) = :ugovormonth AND YEAR(kf_datum) = :ugovorgodina)");
											
											$query_kandidati_financije_mn->execute(array(
															':nalog_id' => $nalog_id,
															':ugovormonth' => $mjesec,
															':ugovorgodina' => $godina
															));
											$j = 0;
											while($kandidat_financije_mn = $query_kandidati_financije_mn->fetch()){
												$kandidat_id_mn[] = $kandidat_financije_mn['kandidat_id'];
												$kf_status_mn[] = $kandidat_financije_mn['kf_status'];
												$kf_id_mn[] = $kandidat_financije_mn['kf_id'];
												$kf_dp_mn[] = $kandidat_financije_mn['kf_datum_placanja'];
												
												$query_kandidati_mn = $db->prepare("
																SELECT kandidat_ime, kandidat_prezime
																FROM idk_kandidati
																WHERE kandidat_id = :kandidat_id");
											
												$query_kandidati_mn->execute(array(
																':kandidat_id' => $kandidat_id_mn[$j] 
																));
												
												$kandidat_mn = $query_kandidati_mn->fetch();
												$kandidat_ime_mn = $kandidat_mn['kandidat_ime'];
												$kandidat_prezime_mn = $kandidat_mn['kandidat_prezime'];
												
												if($broj_zamijenjenih_kandidata_mn != 0){
													for($k=0; $k<$broj_zamijenjenih_kandidata_mn; $k++){
														if($kandidat_id_mn[$j] == $novi_kandidat_mn[$k]){
															$kandidat_mn_link = "<a href='".$url."kandidati?page=open&id=".$kandidat_id_mn[$j]."' id='kandidat".$kandidat_id_mn[$j]."' class='kandidat_link'>".$kandidat_ime_mn." ".$kandidat_prezime_mn."</a><a href='".$url."kandidati?page=open&id=".$kandidat_id_zamijenjeni_mn[$k]."'><span class='label label-primary' style='margin-left: 5px;'> <i class='fa fa-exchange' aria-hidden='true'></i></span></a>";
															if($zamijenjeni_placeno_mn[$k] == "1" and $kandidat_financije_mn['kf_placeno'] == 2){
																$kf_placeno_mn[$j] = 1;
															}elseif($zamijenjeni_placeno_mn[$k] == "2" and $kandidat_financije_mn['kf_placeno'] == 2){
																$kf_placeno_mn[$j] = $kandidat_financije_mn['kf_placeno'];
															}else{
																$kf_placeno_mn[$j] = $kandidat_financije_mn['kf_placeno'];
															}
															break;
														}elseif($kandidat_id_mn[$j] != $novi_kandidat_mn[$k] and $k == ($broj_zamijenjenih_kandidata_mn-1)){
															$kandidat_mn_link = "<a href='".$url."kandidati?page=open&id=".$kandidat_id_mn[$j]."' id='kandidat".$kandidat_id_mn[$j]."' class='kandidat_link'>".$kandidat_ime_mn." ".$kandidat_prezime_mn."</a>";
															$kf_placeno_mn[$j] = $kandidat_financije_mn['kf_placeno'];
														}else{}
													}
												}
												else{
													$kandidat_mn_link = "<a href='".$url."kandidati?page=open&id=".$kandidat_id_mn[$j]."' id='kandidat".$kandidat_id_mn[$j]."' class='kandidat_link'>".$kandidat_ime_mn." ".$kandidat_prezime_mn."</a>";
													$kf_placeno_mn[$j] = $kandidat_financije_mn['kf_placeno'];
												}
												$array_kandidati_mjeseci_nakon[] = $kandidat_mn_link;
												$j++;
											}
											
											// KANDIDATI POCETAK RADA NAKON # MJESECI
											/* $query_kandidati_mn = $db->prepare("
															SELECT kandidat_id, kandidat_ime, kandidat_prezime, kandidat_spol, kandidat_jmbg, kandidat_status, kandidat_slika, kandidat_email, kandidat_datetime, kandidat_visitedurl, kandidat_prijava_na, kandidat_group, cv_ba, cv_de
															FROM idk_kandidati
															INNER JOIN idk_project_kandidati ON idk_kandidati.kandidat_id = idk_project_kandidati.pk_kandidatid
															WHERE (pk_projectid = :pk_projectid AND kandidat_status !=3) AND (MONTH(kandidat_datum_pocetakrada_mjesec + INTERVAL $nr_mjeseci_nakon MONTH) = :ugovormonth AND YEAR(kandidat_datum_pocetakrada_mjesec) = :ugovorgodina)");
											
											$query_kandidati_mn->execute(array(
											':pk_projectid' => $project_id,
											':ugovormonth' => $mjesec,
											':ugovorgodina' => $godina
											));
											
											while($kandidat_mn = $query_kandidati_mn->fetch()){
												$kandidat_id_nm = $kandidat_mn['kandidat_id'];
												$kandidat_ime_nm = $kandidat_mn['kandidat_ime'];
												$kandidat_prezime_nm = $kandidat_mn['kandidat_prezime'];
												$kandidat_nm = "<a href='".$url."kandidati?page=open&id=".$kandidat_id_nm."'>".$kandidat_ime_nm." ".$kandidat_prezime_nm."</a>";
												$array_kandidati_mjeseci_nakon[] = $kandidat_nm;
											} */
											
											// KANDIDATI POČETAK RADA NAKON # MJESECI (druga rata)
											if($broj_rata_npr == 2){
												$query_zamjena_mn2 = $db->prepare("
																SELECT kandidat_id, kf_type, kf_placeno, kf_zamjena_id
																FROM idk_kandidat_financije
																WHERE (nalog_id = :nalog_id) AND (kf_type = 4 AND kf_status = 2)");
																
												$query_zamjena_mn2->execute(array(
																':nalog_id' => $nalog_id
																));
																
												while($kandidat_zamjena_mn2 = $query_zamjena_mn2->fetch()){
													$kandidat_id_zamijenjeni_mn2[] = $kandidat_zamjena_mn2['kandidat_id'];
													$zamijenjeni_placeno_mn2[] = $kandidat_zamjena_mn2['kf_placeno'];
													$novi_kandidat_mn2[] = $kandidat_zamjena_mn2['kf_zamjena_id'];
												}
												
												$broj_zamijenjenih_kandidata_mn2 = count($novi_kandidat_mn2);
												
												$query_kandidati_financije_mn2 = $db->prepare("
																SELECT kf_id, kandidat_id, kf_datum, kf_datum_stvarni, kf_type, kf_placeno, kf_status, kf_datum_placanja
																FROM idk_kandidat_financije
																WHERE (nalog_id = :nalog_id) AND (kf_type = 4 AND (kf_status = 1 OR kf_status = 3)) AND (MONTH(kf_datum) = :ugovormonth AND YEAR(kf_datum) = :ugovorgodina)");
												
												$query_kandidati_financije_mn2->execute(array(
																':nalog_id' => $nalog_id,
																':ugovormonth' => $mjesec,
																':ugovorgodina' => $godina
																));
												$j = 0;
												while($kandidat_financije_mn2 = $query_kandidati_financije_mn2->fetch()){
													$kandidat_id_mn2[] = $kandidat_financije_mn2['kandidat_id'];
													$kf_status_mn2[] = $kandidat_financije_mn2['kf_status'];
													$kf_id_mn2[] = $kandidat_financije_mn2['kf_id'];
													$kf_dp_mn2[] = $kandidat_financije_mn2['kf_datum_placanja'];
													
													$query_kandidati_mn2 = $db->prepare("
																	SELECT kandidat_ime, kandidat_prezime
																	FROM idk_kandidati
																	WHERE kandidat_id = :kandidat_id");
												
													$query_kandidati_mn2->execute(array(
																	':kandidat_id' => $kandidat_id_mn2[$j] 
																	));
													
													$kandidat_mn2 = $query_kandidati_mn2->fetch();
													$kandidat_ime_mn2 = $kandidat_mn2['kandidat_ime'];
													$kandidat_prezime_mn2 = $kandidat_mn2['kandidat_prezime'];
													
													if($broj_zamijenjenih_kandidata_mn2 != 0){
														for($k=0; $k<$broj_zamijenjenih_kandidata_mn2; $k++){
															if($kandidat_id_mn2[$j] == $novi_kandidat_mn2[$k]){
																$kandidat_mn_link2 = "<a href='".$url."kandidati?page=open&id=".$kandidat_id_mn2[$j]."' id='kandidat".$kandidat_id_mn2[$j]."' class='kandidat_link'>".$kandidat_ime_mn2." ".$kandidat_prezime_mn2."</a><a href='".$url."kandidati?page=open&id=".$kandidat_id_zamijenjeni_mn2[$k]."'><span class='label label-primary' style='margin-left: 5px;'> <i class='fa fa-exchange' aria-hidden='true'></i></span></a>";
																if($zamijenjeni_placeno_mn2[$k] == "1" and $kandidat_financije_mn2['kf_placeno'] == 2){
																	$kf_placeno_mn2[$j] = 1;
																}elseif($zamijenjeni_placeno_mn2[$k] == "2" and $kandidat_financije_mn2['kf_placeno'] == 2){
																	$kf_placeno_mn2[$j] = $kandidat_financije_mn2['kf_placeno'];
																}else{
																	$kf_placeno_mn2[$j] = $kandidat_financije_mn2['kf_placeno'];
																}
																break;
															}elseif($kandidat_id_mn2[$j] != $novi_kandidat_mn2[$k] and $k == ($broj_zamijenjenih_kandidata_mn2-1)){
																$kandidat_mn_link2 = "<a href='".$url."kandidati?page=open&id=".$kandidat_id_mn2[$j]."' id='kandidat".$kandidat_id_mn2[$j]."' class='kandidat_link'>".$kandidat_ime_mn2." ".$kandidat_prezime_mn2."</a>";
																$kf_placeno_mn2[$j] = $kandidat_financije_mn2['kf_placeno'];
															}else{}
														}
													}
													else{
														$kandidat_mn_link2 = "<a href='".$url."kandidati?page=open&id=".$kandidat_id_mn2[$j]."' id='kandidat".$kandidat_id_mn2[$j]."' class='kandidat_link'>".$kandidat_ime_mn2." ".$kandidat_prezime_mn2."</a>";
														$kf_placeno_mn2[$j] = $kandidat_financije_mn2['kf_placeno'];
													}
													$array_kandidati_mjeseci_nakon2[] = $kandidat_mn_link2;
													$j++;
												}
											}
											// 
											
											$broj_kandidata_ugovor = count($array_kandidati_ugovor);
											$cijena_ugovor_kandidat = $provizija / 100 * $ugovor_procenat;
											
											$broj_kandidata_pocetak = count($array_kandidati_pocetak_rada);
											$cijena_pocetak_kandidat = $provizija / 100 * $pocetakrada_procenat;
											$cijena_pocetak = ($nalog_potrebno_kandidata * $provizija / 100 * $pocetakrada_procenat);
											
											$broj_kandidata_mn = count($array_kandidati_mjeseci_nakon);
											$cijena_mn = $provizija / 100 * $mjeseci_nakon_procenat;
											$cijena_mjeseci_nakon = ($nalog_potrebno_kandidata * $provizija / 100 * $mjeseci_nakon_procenat);
											if($broj_rata_npr == 2){
												$broj_kandidata_mn2 = count($array_kandidati_mjeseci_nakon2);
												$cijena_mn2 = $provizija / 100 * $mjeseci_nakon_procenat2;
												$cijena_mjeseci_nakon2 = ($nalog_potrebno_kandidata * $provizija / 100 * $mjeseci_nakon_procenat2);
											}
											if($postojanje_ugovor) {
											if(!$broj_kandidata_ugovor){
											?>
											<div class="alert material-alert material-primary">Nema kandidata u odabranom mjesecu</div>
											<?php }else{
												$ugovor_suma = 0;
												$ugovor_suma_naplaceno = 0;
												for($i=0; $i<$broj_kandidata_ugovor; $i++){
													$ugovor_suma += $cijena_ugovor_kandidat;
											?>
											<div class="row ugovori_klasa" style="margin-bottom: 4px; padding-top: auto; padding-bottom: auto;">
												<div class="col-sm-3 text-right"><?php echo $array_kandidati_ugovor[$i];?></div>
												<div class="col-sm-1"><span class="label label-success cijena_ugovor_kandidat"><?php echo $cijena_ugovor_kandidat;?></span></div>
												<div class="col-sm-2">
													<span class="label <?php if($kf_placeno[$i] == 1){echo "label-success"; $ugovor_suma_naplaceno += $cijena_ugovor_kandidat; }else{ echo "label-default";} ?>" id="placeno<?php echo $kf_id[$i];?>" data-toggle="modal" data-target="#datumModal" onclick="proslijedi_modal_datum(this)" data-kf-id="<?php echo $kf_id[$i]; ?>">PLAĆENO</span>
													<span style="<?php if($kf_placeno[$i] == 0 or $kf_placeno[$i] == 2) { ?> display: none; <?php } ?>" class="label label-success" id="datum_placanja<?php echo $kf_id[$i];?>" data-kf-id="<?php echo $kf_dp_ugovor[$i]; ?>"><?php echo date("d.m.Y", strtotime($kf_dp_ugovor[$i]));?></span>
												</div>
												<div class="col-sm-2" >
													<span class="label  <?php if($kf_placeno[$i] == 0){echo "label-danger";}else{ echo "label-default";} ?>" id="nije_placeno<?php echo $kf_id[$i];?>" onclick="promjena_naplate_ne(this)" data-kf-id="<?php echo $kf_id[$i]; ?>">NIJE PLAĆENO</span>
												</div>
												<?php if($zadnja_rata == "ugovor"){?>
												<div class="col-sm-2" >
													<span class="label  <?php if($kf_status[$i] == 3){echo "label-primary";}else{ echo "label-default";} ?>" id="zavrsen<?php echo $kf_id[$i];?>" data-toggle="modal" data-target="#zavrsiModal" data-kf-id="<?php echo $kf_id[$i]; ?>" onclick="proslijedi_modal(this)">ZAVRŠEN</span>
												</div>
												<?php } ?>
											</div>
											<?php	if($ugovor_suma > 0 and $i == $broj_kandidata_ugovor -1){
											?>
											</br>
											<div class="row">
												<strong class="col-sm-3 text-right">UKUPNO:</strong>
												<strong class="col-sm-3"><?php echo $ugovor_suma;?></strong>
											</div>
											<div class="row">
												<strong class="col-sm-3 text-right">UKUPNO NAPLAĆENO:</strong>
												<strong class="col-sm-3" id="naplaceno_ugovora"></strong>
											</div>
											<?php }	}	}?>
											</br>
											<?php } if($postojanje_pocetak) { ?>
											<div class="row">
												<div class="col-sm-12">
													<h5>Kandidati koji počinju rad u odabranom mjesecu  <span style="font-weight: bold">(<?php echo $pocetakrada_procenat?>% - <?php echo $cijena_pocetak;?> EUR)</span></h5>
												</div>
											</div>
											<?php
											if(!$broj_kandidata_pocetak){
											?>
											<div class="alert material-alert material-primary">Nema kandidata u odabranom mjesecu</div>
											<?php }else{
												$pocetak_rada_suma = 0;
												for($i=0; $i<$broj_kandidata_pocetak; $i++){
													$pocetak_rada_suma += $cijena_pocetak_kandidat;
											?>
											<div class="row pocetak_klasa">
												<div class="col-sm-3 text-right"><?php echo $array_kandidati_pocetak_rada[$i];?></div>
												<div class="col-sm-1"><span class="label label-success cijena_pocetak_kandidat"><?php echo $cijena_pocetak_kandidat;?></span></div>
												<div class="col-sm-2">
													<span class="label <?php if($kf_placeno_pocetak_rada[$i] == 1){echo "label-success";}else{ echo "label-default";} ?>" id="placeno<?php echo $kf_id_pocetak_rada[$i];?>" data-toggle="modal" data-target="#datumModal" onclick="proslijedi_modal_datum(this)" data-kf-id="<?php echo $kf_id_pocetak_rada[$i]; ?>">PLAĆENO</span>
													<span style="<?php if($kf_placeno_pocetak_rada[$i] == 0 or $kf_placeno_pocetak_rada[$i] == 2) { ?> display: none; <?php } ?>" class="label label-success" id="datum_placanja<?php echo $kf_id_pocetak_rada[$i];?>" data-kf-id="<?php echo $kf_dp_pocetak[$i]; ?>"><?php echo date("d.m.Y", strtotime($kf_dp_pocetak[$i]));?></span>
												</div>
												<div class="col-sm-2">
													<span class="label  <?php if($kf_placeno_pocetak_rada[$i] == 0){echo "label-danger";}else{ echo "label-default";} ?>" id="nije_placeno<?php echo $kf_id_pocetak_rada[$i];?>" onclick="promjena_naplate_ne(this)" data-kf-id="<?php echo $kf_id_pocetak_rada[$i]; ?>">NIJE PLAĆENO</span>
												</div>
												<?php if($zadnja_rata == "pocetak rada"){?>
												<div class="col-sm-2">
													<span class="label  <?php if($kf_status_pocetak_rada[$i] == 3){echo "label-primary";}else{ echo "label-default";} ?>" id="zavrsen<?php echo $kf_id[$i];?>" data-toggle="modal" data-target="#zavrsiModal" data-kf-id="<?php echo $kf_id_pocetak_rada[$i]; ?>" onclick="proslijedi_modal(this)">ZAVRŠEN</span>
												</div>
												<?php } ?>
											</div>
											<?php	if($pocetak_rada_suma > 0 and $i == $broj_kandidata_pocetak -1){
											?>
											</br>
											<div class="row">
												<strong class="col-sm-3 text-right">UKUPNO:</strong>
												<strong class="col-sm-3"><?php echo $pocetak_rada_suma;?></strong>
											</div>
											<div class="row">
												<strong class="col-sm-3 text-right">UKUPNO NAPLAĆENO:</strong>
												<strong class="col-sm-3" id="naplaceno_pocetak"></strong>
											</div>
											<?php }	}	}?>
											</br>
											<?php } if($postojanje_mn_prva) { ?>
											<div class="row">
												<div class="col-sm-12">
													<h5><?php echo $nr_mjeseci_nakon." ".$mjesec_sufix ;?> nakon početka rada kandidata <span style="font-weight: bold">(<?php echo $mjeseci_nakon_procenat?>% - <?php echo $cijena_mjeseci_nakon;?> EUR)</span></h5>
												</div>
											</div>
											<?php
											if(!$broj_kandidata_mn){
											?>
											<div class="alert material-alert material-primary">Nema kandidata u odabranom mjesecu</div>
											<?php }else{
												$mjeseci_nakon_suma = 0;
												for($i=0; $i<$broj_kandidata_mn; $i++){
													$mjeseci_nakon_suma += $cijena_mn;
											?>
											<div class="row mn_klasa">
												<div class="col-sm-3 text-right"><?php echo $array_kandidati_mjeseci_nakon[$i];?></div>
												<div class="col-sm-1"><span class="label label-success cijena_mn_kandidat"><?php echo $cijena_mn;?></span></div>
												<div class="col-sm-2 text-right" >
													<span class="label <?php if($kf_placeno_mn[$i] == 1){echo "label-success";}else{ echo "label-default";} ?>" id="placeno<?php echo $kf_id_mn[$i];?>" data-toggle="modal" data-target="#datumModal" onclick="proslijedi_modal_datum(this)" data-kf-id="<?php echo $kf_id_mn[$i]; ?>">PLAĆENO</span>
													<span style="<?php if($kf_placeno_mn[$i] == 0 or $kf_placeno_mn[$i] == 2) { ?> display: none; <?php } ?>" class="label label-success" id="datum_placanja<?php echo $kf_id_mn[$i];?>" data-kf-id="<?php echo $kf_dp_mn[$i]; ?>"><?php echo date("d.m.Y", strtotime($kf_dp_mn[$i]));?></span>
												</div>
												<div class="col-sm-2">
													<span class="label  <?php if($kf_placeno_mn[$i] == 0){echo "label-danger";}else{ echo "label-default";} ?>" id="nije_placeno<?php echo $kf_id_mn[$i];?>" onclick="promjena_naplate_ne(this)" data-kf-id="<?php echo $kf_id_mn[$i]; ?>">NIJE PLAĆENO</span>
												</div>
												<?php if($zadnja_rata == "mjeseci nakon"){?>
												<div class="col-sm-2">
													<span class="label  <?php if($kf_status_mn[$i] == 3){echo "label-primary";}else{ echo "label-default";} ?>" id="zavrsen<?php echo $kf_id[$i];?>" data-toggle="modal" data-target="#zavrsiModal" data-kf-id="<?php echo $kf_id_mn[$i]; ?>" onclick="proslijedi_modal(this)">ZAVRŠEN</span>
												</div>
												<?php } ?>
											</div>
											<?php	if($mjeseci_nakon_suma > 0 and $i == $broj_kandidata_mn -1){
											?>
											</br>
											<div class="row">
												<strong class="col-sm-3 text-right">UKUPNO:</strong>
												<strong class="col-sm-3"><?php echo $mjeseci_nakon_suma;?></strong>
											</div>
											<div class="row">
												<strong class="col-sm-3 text-right">UKUPNO NAPLAĆENO:</strong>
												<strong class="col-sm-3" id="naplaceno_mn"></strong>
											</div>
											<?php }	}	}  } if($broj_rata_npr == 2){?>
											
											</br>
											<div class="row">
												<div class="col-sm-12">
													<h5><?php echo $nr_mjeseci_nakon2." ".$mjesec_sufix2 ;?> nakon početka rada kandidata <span style="font-weight: bold">(<?php echo $mjeseci_nakon_procenat2?>% - <?php echo $cijena_mjeseci_nakon2;?> EUR)</span></h5>
												</div>
											</div>
											<?php
											if(!$broj_kandidata_mn2){
											?>
											<div class="alert material-alert material-primary">Nema kandidata u odabranom mjesecu</div>
											<?php }else{
												$mjeseci_nakon_suma2 = 0;
												for($i=0; $i<$broj_kandidata_mn2; $i++){
													$mjeseci_nakon_suma2 += $cijena_mn2;
											?>
											<div class="row mn2_klasa">
												<div class="col-sm-3 text-right"><?php echo $array_kandidati_mjeseci_nakon2[$i];?></div>
												<div class="col-sm-1"><span class="label label-success cijena_mn2_kandidat"><?php echo $cijena_mn2;?></span></div>
												<div class="col-sm-2">
													<span class="label <?php if($kf_placeno_mn2[$i] == 1){echo "label-success";}else{ echo "label-default";} ?>" id="placeno<?php echo $kf_id_mn2[$i];?>" data-toggle="modal" data-target="#datumModal" onclick="proslijedi_modal_datum(this)" data-kf-id="<?php echo $kf_id_mn2[$i]; ?>">PLAĆENO</span>
													<span style="<?php if($kf_placeno_mn2[$i] == 0 or $kf_placeno_mn2[$i] == 2) { ?> display: none; <?php } ?>" class="label label-success" id="datum_placanja<?php echo $kf_id_mn2[$i];?>" data-kf-id="<?php echo $kf_dp_mn2[$i]; ?>"><?php echo date("d.m.Y", strtotime($kf_dp_mn2[$i]));?></span>
												</div>
												<div class="col-sm-2">
													<span class="label  <?php if($kf_placeno_mn2[$i] == 0){echo "label-danger";}else{ echo "label-default";} ?>" id="nije_placeno<?php echo $kf_id_mn2[$i];?>" onclick="promjena_naplate_ne(this)" data-kf-id="<?php echo $kf_id_mn2[$i]; ?>">NIJE PLAĆENO</span>
												</div>
												<?php if($zadnja_rata == "mjeseci nakon".$nr_mjeseci_nakon2){?>
												<div class="col-sm-2">
													<span class="label  <?php if($kf_status_mn2[$i] == 3){echo "label-primary";}else{ echo "label-default";} ?>" id="zavrsen<?php echo $kf_id[$i];?>" data-toggle="modal" data-target="#zavrsiModal" data-kf-id="<?php echo $kf_id_mn2[$i]; ?>" onclick="proslijedi_modal(this)">ZAVRŠEN</span>
												</div>
												<?php } ?>
											</div>
											<?php	if($mjeseci_nakon_suma2 > 0 and $i == $broj_kandidata_mn2 -1){
											?>
											</br>
											<div class="row">
												<strong class="col-sm-3 text-right">UKUPNO:</strong>
												<strong class="col-sm-3"><?php echo $mjeseci_nakon_suma2;?></strong>
											</div>
											<div class="row">
												<strong class="col-sm-3 text-right">UKUPNO NAPLAĆENO:</strong>
												<strong class="col-sm-3" id="naplaceno_mn2"></strong>
											</div>
											<?php }	}	} ?>
											
											<?php } ?>
										</div>
										
										<!-- 	MODAL	-->
										
										<div class="modal material-modal material-modal_primary fade" id="zavrsiModal">
											<div class="modal-dialog">
												<div class="modal-content material-modal__content">
													<input type="hidden" id="modal_kf_id" value=""></input>
													<div class="modal-header material-modal__header">
														<button class="close material-modal__close" data-dismiss="modal">&times;</button>
														<h4 class="modal-title material-modal__title">Financije kandidata</h4>
													</div>
													<div class="modal-body material-modal__body">
														<p>Jeste li sigurni da je kandidat <span id="nazivstavke"></span> završen?</p>
														<p style="color:red; font-size:13px;">(Ukoliko kliknete "završi" ova opcija se više ne može mijenjati)</p>
													</div>
													<div class="modal-footer material-modal__footer">
														<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
														<button class="btn btn-primary material-btn material-btn_primary" onclick="zavrsi_kandidata()">ZAVRŠEN</button>
													</div>
												</div>
											</div>
										</div>
										
										<!-- 	MODAL ZA DATUM PLACANJA	-->
										
										<div class="modal material-modal material-modal_primary fade" id="datumModal">
											<div class="modal-dialog modal-sm">
												<div class="modal-content material-modal__content">
													<input type="hidden" id="modal_dp_kf_id" value=""></input>
													<div class="modal-header material-modal__header">
														<button class="close material-modal__close" data-dismiss="modal">&times;</button>
														<h4 class="modal-title material-modal__title">Datum plaćanja:</h4>
													</div>
													<div class="modal-body material-modal__body">
														<p>Odaberite datum plaćanja: </p>
														<input class="form-control materail-input" type="text" name="datum_placanja" autocomplete="off" id="datum_placanja" required>
													</div>
													<div class="modal-footer material-modal__footer">
														<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
														<button class="btn btn-primary material-btn material-btn_primary" onclick="promjena_naplate_da(this)">UREDU</button>
													</div>
												</div>
											</div>
										</div>
											
										<script>
										
											$( function() {
												$( "#datum_placanja" ).datepicker({
												changeMonth: true,
												changeYear: true,
													dateFormat: 'dd.mm.yy',
													yearRange: '2018:2022'
												});
											});
											$( document ).ready(function() {
												provjera_placeno();
												var zavrseni = document.getElementsByClassName("label-primary");
												for(var i=0; i<zavrseni.length; i++){
													zavrseni[i].removeAttribute("data-kf-id");
													zavrseni[i].removeAttribute("onclick");
													zavrseni[i].removeAttribute("data-toggle");
													zavrseni[i].removeAttribute("data-target");
													console.log("yea");
												}
											});
											
											function provjera_placeno(){
												var cijena_class = document.getElementsByClassName("cijena_ugovor_kandidat")[0];
												if(cijena_class == undefined){
													broj_labela = 0;
												}else{
													var cijena = cijena_class.textContent;
													var broj_labela = $(".ugovori_klasa").find(".label-success").length;
													var suma = 0;
													
													for(var i=0; i<broj_labela; i++){
														var id_labela = document.getElementsByClassName("label-success")[i].id.indexOf("placeno");
														if(id_labela == 0){
															suma = suma + parseInt(cijena);
														}
													}
													document.getElementById("naplaceno_ugovora").textContent = suma;
												}
												
												var cijena_pocetak_class = document.getElementsByClassName("cijena_pocetak_kandidat")[0];
												if(cijena_pocetak_class == undefined){
													broj_labela_pocetak = 0;
												}else{
													var cijena_pocetak = cijena_pocetak_class.textContent;
													var broj_labela_pocetak = $(".pocetak_klasa").find(".label-success").length;
													var suma_pocetak = 0;
													
													for(var i=broj_labela; i<broj_labela_pocetak+broj_labela; i++){
														var id_labela_pocetak = document.getElementsByClassName("label-success")[i].id.indexOf("placeno");
														if(id_labela_pocetak == 0){
															suma_pocetak = suma_pocetak + parseInt(cijena_pocetak);
														}
													}
													document.getElementById("naplaceno_pocetak").textContent = suma_pocetak;
												}
												
												var cijena_mn_class = document.getElementsByClassName("cijena_mn_kandidat")[0];
												if(cijena_mn_class == undefined){
													broj_labela_mn = 0;
												}else{
													var cijena_mn = cijena_mn_class.textContent;
													var broj_labela_mn = $(".mn_klasa").find(".label-success").length;
													var suma_mn = 0;
													
													for(var i=broj_labela_pocetak+broj_labela; i<broj_labela_mn+broj_labela_pocetak+broj_labela; i++){
														var id_labela_mn = document.getElementsByClassName("label-success")[i].id.indexOf("placeno");
														if(id_labela_mn == 0){
															suma_mn = suma_mn + parseInt(cijena_mn);
														}
													}
													document.getElementById("naplaceno_mn").textContent = suma_mn;
												}
												
												var cijena_mn2_class = document.getElementsByClassName("cijena_mn2_kandidat")[0];
												if(cijena_mn2_class == undefined){
													broj_labela_mn2 = 0;
												}else{
													var cijena_mn2 = cijena_mn2_class.textContent;
													var broj_labela_mn2 = $(".mn2_klasa").find(".label-success").length;
													var suma_mn2 = 0;
													
													for(var i=broj_labela_mn+broj_labela_pocetak+broj_labela; i<broj_labela_mn2+broj_labela_mn+broj_labela_pocetak+broj_labela; i++){
														var id_labela_mn2 = document.getElementsByClassName("label-success")[i].id.indexOf("placeno");
														if(id_labela_mn2 == 0){
															suma_mn2 = suma_mn2 + parseInt(cijena_mn2);
														}
													}
													document.getElementById("naplaceno_mn2").textContent = suma_mn2;
												}
											}
											
											function proslijedi_modal_datum(t){
												var kf_id = t.getAttribute("data-kf-id");
												$("#modal_dp_kf_id").val(kf_id);
											}
											
											function promjena_naplate_da(t){
												var kf_id = document.getElementById("modal_dp_kf_id").value;
												var datum_placanja = document.getElementById("datum_placanja").value;
												var placeno = 1;
												$.ajax({
													url: 'ajax_data.php?page=kandidat_placeno_da',
													type: 'POST',    
													data: {'kf_id':kf_id, 'placeno':placeno, 'datum_placanja':datum_placanja},
													dataType: 'html',
													success: function(data) {
														$('#datumModal').modal('toggle');
														document.getElementById("placeno"+kf_id).classList.remove("label-default");
														document.getElementById("placeno"+kf_id).classList.add("label-success");
														document.getElementById("nije_placeno"+kf_id).classList.remove("label-danger");
														document.getElementById("nije_placeno"+kf_id).classList.add("label-default");
														$('#datum_placanja'+kf_id+'').show();
														$('#datum_placanja'+kf_id+'').text(datum_placanja);
														provjera_placeno();
													}
												});
												//$('#placeno'+kf_id+'').off('click');
												
												
											}
											
											function promjena_naplate_ne(t){
												var kf_id = t.getAttribute("data-kf-id");
												if(t.classList.contains("label-default")){
													var placeno = 0;
													t.classList.remove("label-default");
													t.classList.add("label-danger");
													var nije_placeno = document.getElementById("placeno"+kf_id);
													nije_placeno.classList.remove("label-success");
													nije_placeno.classList.add("label-default");
													$.ajax({
														url: 'ajax_data.php?page=kandidat_placeno_ne',
														type: 'POST',    
														data: {'kf_id':kf_id, 'placeno':placeno},
														dataType: 'html',
														success: function(data) {
															$('#datum_placanja'+kf_id+'').hide();
														}
													});
													provjera_placeno();
												}
											}
											
											function proslijedi_modal(t){
												var kf_id = t.getAttribute("data-kf-id");
												$("#modal_kf_id").val(kf_id);
											}
											
											function zavrsi_kandidata(t){
												var kf_id = document.getElementById("modal_kf_id").value;
												$.ajax({
													url: 'ajax_data.php?page=kandidat_zavrsen',
													type: 'POST',    
													data: {'kf_id':kf_id},
													dataType: 'html',
													success: function(data){
														$('#zavrsiModal').modal('toggle');
														document.getElementById("zavrsen"+kf_id).classList.remove("label-default");
														document.getElementById("zavrsen"+kf_id).classList.add("label-primary");
													}
												});
											}
										</script>
									</div>
								</div>
							</div>
						</div>
						<?php }else{ ?>
						<div class="row">
							<div class="col-md-12">
								<div class="content_box">
									<div class="row">
										<div class="col-sm-6">
											<div class="row">
												<div class="col-sm-12">
													<div class="alert material-alert material-alert_warning">Kako bi ste vidjeli detaljne financijske informacije naloga iz exporta otvorite link za željeni nalog</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<?php } ?>
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
				case "dipl_odabir_drzave":
				?>
				<style>
				.dugme{
					align-items: center;
					background: linear-gradient(-45deg, rgba(0,0,0,0.22), rgba(255,255,255,0.25));
					box-shadow: 12px 12px 16px 0 rgba(0, 0, 0, 0.25),
					-8px -8px 12px 0 rgba(255, 255, 255, 0.3);
					border-radius: 50px;
					justify-content: center
				}
				footer{
					display:none !important;
				}
				h2{
					border-bottom: 1px solid black;
				}
				</style>
				<div class="container">
					<div class="col-sm-12 text-center">
						<h2>Predračuni</h2>
					</div>
					<div class="col-sm-4 text-center idk_margin_top20">
						<a href="<?php getSiteURL(); ?>finances?page=dipl_finances_predracuni&country=1" target="_BLANK"><button class="btn btn material-btn material dugme"><img src="<?php getSiteUrl(); ?>images/BosniaHerzegowina.png" width="200"></button></a>
					</div>
					<div class="col-sm-4 text-center idk_margin_top20">	
						<a href="<?php getSiteURL(); ?>finances?page=dipl_finances_predracuni&country=2" target="_BLANK"><button class="btn btn material-btn material dugme"><img src="<?php getSiteUrl(); ?>images/Serbian.png" width="200"></button></a>
					</div>
					<div class="col-sm-4 text-center idk_margin_top20">	
						<a href="<?php getSiteURL(); ?>finances?page=ch_predracuni" target="_BLANK"><button class="btn btn material-btn material dugme"><img src="<?php getSiteUrl(); ?>images/Switzerland.png" width="200"></button></a>
					</div>
					<?php if($team_id == 1){ ?>
					<div class="col-sm-12 text-center idk_margin_top20">
						<h2>Računi</h2>
					</div>
					<div class="col-sm-4 text-center idk_margin_top20">
						<a href="<?php getSiteURL(); ?>finances?page=dipl_finances_racuni&country=1" target="_BLANK"><button class="btn btn material-btn material dugme"><img src="<?php getSiteUrl(); ?>images/BosniaHerzegowina.png" width="200"></button></a>
					</div>
					<div class="col-sm-4 text-center idk_margin_top20">	
						<a href="<?php getSiteURL(); ?>finances?page=dipl_finances_racuni&country=2" target="_BLANK"><button class="btn btn material-btn material dugme"><img src="<?php getSiteUrl(); ?>images/Serbian.png" width="200"></button></a>
					</div>
					<div class="col-sm-4 text-center idk_margin_top20">	
						<a href="<?php getSiteURL(); ?>finances?page=ch_racuni" target="_BLANK"><button class="btn btn material-btn material dugme"><img src="<?php getSiteUrl(); ?>images/Switzerland.png" width="200"></button></a>
					</div>
					<?php } ?>					
				</div>
				<?php
				break;
				
				case "dipl_finances_predracuni":
				if((in_array( "9" , $employee_status)) OR (in_array( "1" , $employee_status)) OR (in_array( "15" , $employee_supervizor))){
					
					if(isset($_GET['f_from'])){
						$f_from = $_GET['f_from'];
					}else{
						$f_from = '';
					}
					
					if(isset($_GET['f_from_du'])){
						$f_from_du = $_GET['f_from_du'];
					}else{
						$f_from_du = '';
					}
					
					/******* za datum kreiranja ********/
					if (strpos($f_from, 'to') !== false) {
						$split = explode(" to ",$f_from);
						$datum_od = $split[0];
						$datum_do = $split[1];
					}else{
						$datum_od = "2022-01-01 00:00:00";
						$datum_do = date("Y-m-d H:i:s");
					}
					
					$f_from_f = date('Y-m-d', strtotime($datum_od));
					$f_to_f = date('Y-m-d', strtotime($datum_do));	
					
					// FOR FLATPICKR
					$f_from_flat = date('d.m.Y', strtotime($datum_od));
					$f_to_flat = date('d.m.Y', strtotime($datum_do));	
					
					$start = $month = strtotime($f_from_f);
					$end = strtotime($f_to_f);
					
					$stat_od_f_query = date('Y-m-d 00:00:00', strtotime($datum_od));
					$stat_do_f_query = date('Y-m-d 23:00:00', strtotime($datum_do));
					/*------------------------*/
					
					/******* za datum uplate ********/
					if (strpos($f_from_du, 'to') !== false) {
						$split_du = explode(" to ",$f_from_du);
						$datum_od_du = $split_du[0];
						$datum_do_du = $split_du[1];
						
						$stat_od_f_query_du = date('Y-m-d 00:00:00', strtotime($datum_od_du));
						$stat_do_f_query_du = date('Y-m-d 23:00:00', strtotime($datum_do_du));
						$query_du = "pr_datum_uplate BETWEEN '$stat_od_f_query_du' AND '$stat_do_f_query_du' AND";
						
						$f_from_f_du = date('Y-m-d', strtotime($datum_od_du));
						$f_to_f_du = date('Y-m-d', strtotime($datum_do_du));	
						
						// FOR FLATPICKR
						$f_from_flat_du = date('d.m.Y', strtotime($datum_od_du));
						$f_to_flat_du = date('d.m.Y', strtotime($datum_do_du));	
					}else{
						$datum_od_du = "2021-01-01 00:00:00";
						$datum_do_du = date("Y-m-d H:i:s");
						
						$stat_od_f_query_du = date('Y-m-d 00:00:00', strtotime($datum_od_du));
						$stat_do_f_query_du = date('Y-m-d 23:00:00', strtotime($datum_do_du));
						$query_du = "";
						$f_from_flat_du = "";
						$f_to_flat_du = "";	
						$f_from_f_du = "";
						$f_to_f_du = "";
					}
					
					
					
					$start_du = $month_du = strtotime($f_from_f_du);
					$end_du = strtotime($f_to_f_du);
					
					
					/*------------------------*/
					
					
					if(isset($_GET['country'])) {
						$country = $_GET['country'];
						if($country == '1'){
							$country = 'BiH';
							$pr_domaca_valuta = 'BAM';
						}elseif($country == '2'){
							$country = 'SRB';
							$pr_domaca_valuta = 'RSD';
						}
					}else{
						$country = 'BiH'; 
						$pr_domaca_valuta = 'BAM';
					}
					
					if(isset($_GET["uplaceno_ili_ne"])){
						$uplaceno_ili_ne = $_GET["uplaceno_ili_ne"];
						$tip_uplate = implode(',', $uplaceno_ili_ne);
					}else{
						$uplaceno_ili_ne = array("0", "1");
						$tip_uplate = '0,1';
					}
					
					if(isset($_GET["rata_filter"])){
						$rata_filter = $_GET["rata_filter"];
						$rate = implode(',', $rata_filter);
					}else{
						$rata_filter = array("1", "2", "3", "4", "5");
						$rate = '1,2,3,4,5';
					}
					
					if(isset($_GET["status_filter"])){
						$status_filter = $_GET["status_filter"];
						$status_query = implode(',', $status_filter);
					}else{
						$status_filter = array("0", "1", "2", "3", "4", "5");
						$status_query = '0,1,2,3,4,5'; 
					}
					
					$month = date('m');
					
					//echo $f_from;
		?>
			<div class="row">
				<div class="col-xs-12">
					<h1><i class="fa fa-money idk_color_green" aria-hidden="true"></i> Financije DIPL - <?php echo $country;?></h1>
				</div>
				<div class="col-xs-12">
					<hr/>
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<div class="row">
							<form action="<?php getSiteUrl(); ?>finances" enctype="multipart/form-data" method="get" accept-charset="utf-8" role="form" class="form-horizontal">
							<input type="hidden" name="page" value="dipl_finances_predracuni">
							<input type="hidden" name="country" value="<?php echo $_GET["country"]; ?>">
								<div class="col-md-10 row">
									<div class="col-lg-3 col-md-3">
										<div class="form-group materail-input-block materail-input-block_primary materail-input_slide-line" style="margin: 0;">
											<label for="f_from">Datum kreiranja predračuna:</label>
											<input type="text" class="form-control" name="f_from" id="f_from" placeholder="Datum" style="padding:17px;border-radius:0;" required>
											<span class="materail-input-block__line"></span>
										</div>
									</div>
									<div class="col-lg-3 col-md-3">
										<div class="form-group materail-input-block materail-input-block_primary materail-input_slide-line" style="margin: 0;">
											<label for="f_from_du">Datum uplate predračuna:</label>
											<input type="text" class="form-control" name="f_from_du" id="f_from_du" placeholder="Datum" style="padding:17px;border-radius:0;" required>
											<span class="materail-input-block__line"></span>
										</div>
									</div>
									<div class="col-lg-2 col-md-2">
										<label for="uplaceno_ili_ne">Status:</label>
										<select id="uplaceno_ili_ne" class="selectpicker" name="uplaceno_ili_ne[]" multiple>
											<option value="1" <?php if(in_array( "1" , $uplaceno_ili_ne)){echo 'selected';}?>>Uplaćeno</option>
											<option value="0" <?php if(in_array( "0" , $uplaceno_ili_ne)){echo 'selected';}?>>Neuplaćeno</option>
										</select>
									</div>
									<div class="col-lg-2 col-md-2">
										<label for="rata_filter">Rata:</label>
										<select id="rata_filter" class="selectpicker" name="rata_filter[]" multiple>
											<option value="1" <?php if(in_array( "1" , $rata_filter)){echo 'selected';}?>>Prva</option>
											<option value="2" <?php if(in_array( "2" , $rata_filter)){echo 'selected';}?>>Druga</option>
											<option value="3" <?php if(in_array( "3" , $rata_filter)){echo 'selected';}?>>Treća</option>
											<option value="4" <?php if(in_array( "4" , $rata_filter)){echo 'selected';}?>>Četvrta</option>
											<option value="5" <?php if(in_array( "5" , $rata_filter)){echo 'selected';}?>>Peta</option>
										</select>
									</div>
									<div class="col-lg-2 col-md-2">
										<label for="status">Status:</label> 
										<select id="status_filter" class="selectpicker" name="status_filter[]" multiple>
											<option value="0" <?php if(in_array( "0" , $status_filter)){echo 'selected';}?>>Arhiva</option>
											<option value="1" <?php if(in_array( "1" , $status_filter)){echo 'selected';}?>>Poslan</option>
											<option value="2" <?php if(in_array( "2" , $status_filter)){echo 'selected';}?>>Uplaćen</option>
											<option value="3" <?php if(in_array( "3" , $status_filter)){echo 'selected';}?>>IN CASO 1</option>
											<option value="4" <?php if(in_array( "4" , $status_filter)){echo 'selected';}?>>IN CASO 2</option>
											<option value="5" <?php if(in_array( "5" , $status_filter)){echo 'selected';}?>>IN CASO 3</option>
										</select>
									</div>
								</div>
								
								<div class="col-md-2 idk_margin_top20">
									<button style="width:100%" class="btn btn-success financijeSearchBUtton">Traži</button>
								</div>
							</form>
						</div>
						<div class="row">
							<div class="col-lg-1 col-md-3 idk_margin_top20">
								<button id="obicni_export" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-file-excel-o" aria-hidden="true"></i> <span>Export</span></button>
							</div>
							<!--<div class="col-md-3 idk_margin_top20">
								<a href="#" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-sticky-note-o" aria-hidden="true"></i> <span>Odustali</span></a>
							</div> -->
							<script>
								$("#f_from").flatpickr({
									mode: "range",
									dateFormat: "d.m.Y",
									disableMobile: "true",
									defaultDate: ["<?php echo $f_from_flat;?>", "<?php echo $f_to_flat;?>"]
								});
								$("#f_from_du").flatpickr({
									mode: "range",
									dateFormat: "d.m.Y",
									disableMobile: "true",
									defaultDate: ["<?php echo $f_from_flat_du;?>", "<?php echo $f_to_flat_du;?>"]
								});
								$('#obicni_export').click(function() {
									$('#export_dak_div').load('export_excel.php?prozor=export_predracuna&datum_range_start=<?php echo $f_from_flat;?>&datum_range_end=<?php echo $f_to_flat;?>&du_start=<?php echo $f_from_flat_du; ?>&du_end=<?php echo $f_to_flat_du ; ?>&domaca_valuta=<?php echo $pr_domaca_valuta;?>&tip_uplate=<?php echo $tip_uplate;?>&rate=<?php echo $rate; ?>&status_query=<?php echo $status_query; ?>'); 
									return false;
								});
							</script>
							<div id="export_dak_div" style="display:none;"></div>
						</div>
						<div class="modal material-modal material-modal_success fade" id="zadnjaRataModal"> 
							<div class="modal-dialog ">
								<div class="modal-content material-modal__content">
									<div class="modal-header material-modal__header">
										<button class="close material-modal__close" data-dismiss="modal">&times;</button>
										<h4 class="modal-title material-modal__title">Kreiraj zadnju ratu</h4>
									</div>
									<div class="modal-body material-modal__body" id="body_modala_rucnog_gutschrifta">
										<form action="do.php?form=createZadnjiPredracunDipl" method="post" role="form" class="form-horizontal">
											<div class="col-sm-12 form-group">
											<label for="nd_kandidati">Kandidat:</label>
											
													<select id="nd_kandidati" class="selectpicker"  data-live-search="true" name="pr_kandidat_id" data-live-search="true" required>
													 
													</select>
											</div>
									</div>
									<div class="modal-footer material-modal__footer">
										<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
										<button type="submit" class="btn btn-success material-btn material-btn_success">Spremi</button>
										</form> 
									</div>
								</div>
							</div>
						</div>
						<script>
							$( "#zadnjaRataButton" ).on('click', function(){
								$( "#nd_kandidati" ).empty();
								$.ajax({
									url: 'ajax_data.php?page=sviKandidatiZaZadnjuRatu',
									type: 'POST',
									dataType: 'json',
									success: function(data) {
										$("#nd_kandidati").append('<option  value="NULL">Odaberi</option>');
										for (var i = 0; i < data.length; i++) {
											$("#nd_kandidati").append('<option  value="' + data[i].id_broj_nd_kandidata + '">' + data[i].ime_nd_kandidata + ' ' + data[i].prezime_nd_kandidata +' - ID: ' + data[i].id_broj_nd_kandidata +'</option>');
											$('#nd_kandidati').selectpicker('refresh');
										}
									}
									});
							});
						</script>
						<div class="row idk_margin_top20">
							<div class="col-xs-12"> 
								<?php
									if(isset($_GET['mess'])) {
										$mess = $_GET['mess'];
									}else{
										$mess = 0;
									}
								?>
								<script type="text/javascript">
									$(document).ready(function() {
										$('#idk_table').DataTable({

											responsive: true,

											 "bAutoWidth": false,

											"aoColumns": [
													{ "width": "5%" },
													{ "width": "20%" },
													{ "width": "10%" },
													{ "width": "10%" },
													{ "width": "10%" },
													{ "width": "10%" },
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
											<th class="text-center">#</th>
											<th class="text-center">Naziv</th>
											<th class="text-center">Broj</th>
											<th class="text-center">Datum kreiranja</th>
											<th class="text-center">Rata</th>
											<th class="text-center">Vrijednost</th>
											<th class="text-center">Status</th>
											<th class="text-center">Uplaćeno</th>
											<th class="text-center">Akcija</th>
										</tr>
									</thead>
									<tbody>
										<?php
										if($team_id == 1){
											$query = $db->prepare("
															SELECT *
															FROM idk_predracuni
															WHERE pr_domaca_valuta = :pr_domaca_valuta AND pr_datum_kreiranja BETWEEN '$stat_od_f_query' AND '$stat_do_f_query' AND $query_du pr_uplaceno IN ($tip_uplate) AND pr_rata IN ($rate) AND pr_status IN ($status_query) AND pr_naplata_preko IN (0,2)
															ORDER BY pr_datum_kreiranja ASC");

											$query->execute(array(
															':pr_domaca_valuta' => $pr_domaca_valuta)); 
										}else{
											$query = $db->prepare("
															SELECT *
															FROM idk_predracuni
															INNER JOIN idk_employees ON idk_predracuni.pr_zaposlenik = idk_employees.employee_id  
															WHERE employee_team = $team_id AND pr_domaca_valuta = :pr_domaca_valuta AND pr_datum_kreiranja BETWEEN '$stat_od_f_query' AND '$stat_do_f_query' AND $query_du pr_uplaceno IN ($tip_uplate) AND pr_rata IN ($rate) AND pr_status IN ($status_query) AND pr_naplata_preko IN (0,2) ");

											$query->execute(array(
															':pr_domaca_valuta' => $pr_domaca_valuta));
										}
										// var_dump($query);
										// exit();
											$brojac = 0;
											$i = 0;
											$uplaceno = 0;
											$neuplaceno = 0;
											$total = 0;
											$incaso1 = 0;
											$incaso2 = 0;
											$incaso3 = 0;
											$incaso1_f = 0;
											$incaso2_f = 0;
											while($row = $query->fetch()){
												$brojac = $brojac + 1;
												$pr_id = $row['pr_id'];
												$pr_kandidat_id = $row['pr_kandidat_id'];
												if($row['pr_kandidat_id'] != NULL){
													$kandidat_name = getImePrezimeNDKanR($row['pr_kandidat_id']);
													$zadnja_rata_broj = getBrojRataNDR($row['pr_kandidat_id']);
													$uplacenost_svih_rata_prije_ove = checkUplacenostSvihPrethodnihRata($row['pr_kandidat_id'], $row["pr_rata"]);
												}
												else{
													$kandidat_name = '<span class="label label-success material-label material-label_success main-container__column">Unknown</span>';
												}
												
												// if(checkOdustao($pr_kandidat_id, $pr_id))
													// $checkOd = " - jeste";
												// else
													// $checkOd = "";
												
												$brojac = $brojac++;
												$pr_broj_predracuna = $row['pr_broj_predracuna'];
												$datum_krei_sort = $row['pr_datum_kreiranja'];
												$pr_datum_kreiranja = date("d.m.Y", strtotime($row['pr_datum_kreiranja']));
												$pr_vrsta_predracuna = $row['pr_vrsta_predracuna'];
												$pr_rata = $row['pr_rata'];
												$rata_text = "Rata ".$pr_rata."";
												$pr_status = $row['pr_status'];
												$pr_domaca_valuta = $row['pr_domaca_valuta'];
												$pr_vrijednost_EUR = $row['pr_vrijednost_EUR'];
												$pr_file = $row['pr_file'];
												$pr_uplaceno = $row['pr_uplaceno'];
												$pr_datum_uplate = date("d.m.Y", strtotime($row['pr_datum_uplate']));
												$pr_opis = $row['pr_opis'];
												$pr_stornirano = $row['pr_stornirano'];
												$pr_datum_storniranja = $row['pr_datum_storniranja'];
												$pr_vrsta_placanja = $row['pr_vrsta_placanja'];
												
												if($pr_domaca_valuta == 'BAM'){
													$vrijednost = $row['pr_vrijednost_BAM'];
													$vrijednost_valuta = 'KM';
												}elseif($pr_domaca_valuta == 'RSD'){
													$vrijednost = $row['pr_vrijednost_RSD'];
													$vrijednost_valuta = 'RSD';
												}
												
												if($pr_stornirano == 0){
													$boja = 'success';
													$plusminus = '';
												}elseif($pr_stornirano == 1){
													$boja = 'danger';
													$plusminus = '-';
												}
												
												$total = $total + $vrijednost;
												$total_f = number_format((float)$total, 2, ',', '.');	 	
												
												if($pr_status == 0){
													$status_show = '<span class="label label-warning material-label material-label_warning main-container__column">Arhiviran</span>';
												}else if($pr_status == 1){
													$status_show = '<span class="label label-info material-label material-label_info main-container__column">Poslan</span>';
												}else if($pr_status == 2){
													$status_show = '<span class="label label-success material-label material-label_success main-container__column">Uplaćen</span>';
												}else if($pr_status == 3){
													$status_show = '<span class="label label-danger material-label material-label_danger main-container__column">In Caso 1</span>';
													$incaso1 = $incaso1 + $vrijednost;
													$incaso1_f = number_format((float)$incaso1, 2, '.', ',');
												}else if($pr_status == 4){
													$status_show = '<span class="label label-danger material-label material-label_danger main-container__column">In Caso 2</span>';
													$incaso2 = $incaso2 + $vrijednost;
													$incaso2_f = number_format((float)$incaso2, 2, '.', ',');
												}else if($pr_status == 5){
													$status_show = '<span class="label label-danger material-label material-label_danger main-container__column">In Caso 3</span>';
													$incaso3 = $incaso3 + $vrijednost;
													$incaso3_f = number_format((float)$incaso3, 2, '.', ',');
												}
												
												if($pr_uplaceno == 0){
													$neuplaceno = $neuplaceno + $vrijednost;
													$neuplaceno_f = number_format((float)$neuplaceno, 2, '.', ',');
													if(($month == '11' OR $month == '12' OR ($zadnja_rata_broj == $pr_rata)) AND $uplacenost_svih_rata_prije_ove == 1){
														$pr_uplaceno_text = '<span class="label label-warning material-label material-label_warning main-container__column">NE</span>';
														if($pr_domaca_valuta == 'BAM'){
															$akcija_text = '
																<div class="akcija">
																	<input type="text" class="form-control bf_input" name="bf_input" id="bf_input'.$brojac.'" placeholder="BF" style="padding:17px;border-radius:0;width:80px;" required>
																	<input type="text" class="form-control datum_input" name="f_from" id="f_from_input'.$brojac.'" placeholder="Datum" style="padding:17px;border-radius:0;float:left;width:90%; width:100px;" required>
																	<i class="fa fa-check oznaci_kao_isplaceno" data-pr_id="'.$pr_id.'" data-pr_kandidat_id="'.$pr_kandidat_id.'" data-pr_domaca_valuta="'.$pr_domaca_valuta.'" style="color:white;background:#5cb85c;font-size:24px;display:flex;float:left;padding:6px;cursor:pointer;" aria-hidden="true"></i>
																</div>
															';
														}else{
															$akcija_text = '
																<div class="akcija">
																	<input type="hidden" class="form-control bf_input" value="nemabf" name="bf_input" id="bf_input'.$brojac.'" placeholder="BF" style="padding:17px;border-radius:0;width:80px;" required>
																	<input type="text" class="form-control datum_input" name="f_from" id="f_from_input'.$brojac.'" placeholder="Datum" style="padding:17px;border-radius:0;float:left;width:90%; width:100px;" required><i class="fa fa-check oznaci_kao_isplaceno" data-pr_id="'.$pr_id.'" data-pr_kandidat_id="'.$pr_kandidat_id.'" data-pr_domaca_valuta="'.$pr_domaca_valuta.'" style="color:white;background:#5cb85c;font-size:24px;display:flex;float:left;padding:6px;cursor:pointer;" aria-hidden="true"></i>
																</div>
															';
														}
													}else{
														$pr_uplaceno_text = '<span class="label label-warning material-label material-label_warning main-container__column">NE</span>';
														$akcija_text = '
															<div class="akcija">
																<input type="hidden" class="form-control bf_input" value="nemabf" name="bf_input" id="bf_input'.$brojac.'" placeholder="BF" style="padding:17px;border-radius:0;width:80px;" required>
																<input type="text" class="form-control datum_input" name="f_from" id="f_from_input'.$brojac.'" placeholder="Datum" style="padding:17px;border-radius:0;float:left;width:90%; width:100px;" required>
																<i class="fa fa-check oznaci_kao_isplaceno" data-pr_id="'.$pr_id.'" data-pr_kandidat_id="'.$pr_kandidat_id.'" data-pr_domaca_valuta="'.$pr_domaca_valuta.'" style="color:white;background:#5cb85c;font-size:24px;display:flex;float:left;padding:6px;cursor:pointer;" aria-hidden="true"></i>
															</div>
														';
													} 
												}else{
													$pr_uplaceno_text = '<span class="label label-success material-label material-label_success main-container__column">DA</span>';
													$akcija_text = '<i class="fa fa-ban" style="font-size:24px;color:green;cursor: not-allowed;" aria-hidden="true" disabled></i>';
													$uplaceno = $uplaceno + $vrijednost;
													$uplaceno_f = number_format((float)$uplaceno, 2, '.', ',');
												}
												
										?>
										
										<tr>
											<td class="text-center"><?php echo ++$i; ?></td>
											<td class="text-center"><a href="nostrifikacija_diploma?page=otvori_ND_kandidata&id=<?php echo $pr_kandidat_id;?>"><?php echo getImePrezimeNDKanR($pr_kandidat_id);?></a></td>
											<td class="text-center"><?php echo $pr_broj_predracuna; ?></td>
											<td class="text-center" data-order="<?php echo $datum_krei_sort; ?>"><?php echo $pr_datum_kreiranja; ?></td>
											<td class="text-center"><?php echo $rata_text; ?></td>
											<td class="text-center"><a href="<?php getSiteUrl(); ?>files/predracuni_dipl/<?php echo $pr_file; ?>" target="_BLANK" class="label label-<?php echo $boja; ?> material-label material-label_<?php echo $boja; ?> main-container__column"><?php echo $plusminus; ?> <?php echo $vrijednost; ?> <?php echo $pr_domaca_valuta; ?></a></td>
											<td class="text-center"><?php echo $status_show;?></td> 
											<td class="text-center"><?php echo $pr_uplaceno_text;?></td>
											<td class="text-center akcija"><?php if($pr_status != 0){ if($team_id == 1){echo $akcija_text;}else{echo '';}}else{echo '';}?></td>
										</tr>
										<script>
											$("#f_from_input"+<?php echo $brojac;?>+"").flatpickr({
												dateFormat: "d.m.Y",
												disableMobile: "true"
											});							
										</script>
										<?php } ?>
									</tbody>
								</table>
								<div class="row">
									<div class="card" data-toggle="tooltip" data-placement="top" title="Vrijednost predračuna koji su uplaćeni">
										<div class="rectangle income"></div>
										<div>
										  <div class="naslov">Uplaćeno</div>
										  <div class="balance">Vrijednost</div>
										  <div class="kurs"><?php echo $vrijednost_valuta;?></div>
										  <div class="amount"><?php echo $uplaceno_f;?></div>
										  <div class="siluet-1"></div>
										  <div class="siluet-2"></div>
										  <!-- <img src="./Siluet.svg" alt=""> -->
										</div>
									</div>
									<div class="card"  data-toggle="tooltip" data-placement="top" title="Vrijednost predračuna koji još nisu uplaćeni">
										<div class="rectangle expense"></div>
										<div>
										  <div class="naslov">Neuplaćeno</div>
										  <div class="balance">Vrijednost</div>
										  <div class="kurs"><?php echo $vrijednost_valuta;?></div>
										  <div class="amount"><?php echo $neuplaceno_f;?></div>
										  <div class="siluet-1"></div>
										  <div class="siluet-2"></div>
										  <!-- <img src="./Siluet.svg" alt=""> -->
										</div>
									</div>
									<div class="card" data-toggle="tooltip" data-placement="top" title="Ukupna vrijednost predračuna">
										<div class="rectangle info"></div>
										<div>
										  <div class="naslov">Total</div>
										  <div class="balance">Vrijednost</div>
										  <div class="kurs"><?php echo $vrijednost_valuta;?></div>
										  <div class="amount"><?php echo $total_f;?></div>
										  <div class="siluet-1"></div>
										  <div class="siluet-2"></div>
										  <!-- <img src="./Siluet.svg" alt=""> -->
										</div>
									</div>
								</div>
								<div class="row">
									<div class="card" data-toggle="tooltip" data-placement="top" title="10 dana nije uplaćeno">
										<div class="rectangle expense"></div>
										<div>
										  <div class="naslov">In Caso 1</div>
										  <div class="balance">Vrijednost</div>
										  <div class="kurs"><?php echo $vrijednost_valuta;?></div>
										  <div class="amount"><?php echo $incaso1_f;?></div>
										  <div class="siluet-1"></div>
										  <div class="siluet-2"></div>
										  <!-- <img src="./Siluet.svg" alt=""> -->
										</div>
									</div>
									<div class="card" data-toggle="tooltip" data-placement="top" title="40 dana nije uplaćeno">
										<div class="rectangle expense"></div>
										<div>
										  <div class="naslov">In Caso 2</div>
										  <div class="balance">Vrijednost</div>
										  <div class="kurs"><?php echo $vrijednost_valuta;?></div>
										  <div class="amount"><?php echo $incaso2_f;?></div>
										  <div class="siluet-1"></div>
										  <div class="siluet-2"></div>
										  <!-- <img src="./Siluet.svg" alt=""> -->
										</div>
									</div>
									<div class="card" data-toggle="tooltip" data-placement="top" title="90 dana nije uplaćeno">
										<div class="rectangle expense"></div>
										<div>
										  <div class="naslov">In Caso 3</div>
										  <div class="balance">Vrijednost</div>
										  <div class="kurs"><?php echo $vrijednost_valuta;?></div>
										  <div class="amount"><?php echo $incaso3_f;?></div>
										  <div class="siluet-1"></div>
										  <div class="siluet-2"></div>
										  <!-- <img src="./Siluet.svg" alt=""> -->
										</div>
									</div>
								</div>
								<script>
								$(".oznaci_kao_isplaceno").on( "click", function() {
									$(this).css('pointer-events','none');
									var pr_id = $(this).data("pr_id");
									var pr_kandidat_id = $(this).data("pr_kandidat_id");
									var pr_domaca_valuta = $(this).data("pr_domaca_valuta");
									var selektor = $(this);
									var date = $(this).prev().val();
									var bf = $(this).prev().prev().val();
									if(date){
										if(pr_domaca_valuta == 'BAM'){
											if(bf){
												if ($(this).hasClass('oznaci_kao_isplaceno')) {
													$.ajax({
														url: 'do.php?form=uplati_predracun', 
														type: 'POST',
														data: {'pr_id':pr_id, 'pr_kandidat_id': pr_kandidat_id, 'pr_datum_uplate': date,'bf': bf},
														dataType: 'html',
														success: function(data) {
															alert(data);
															selektor.prev().remove();
															selektor.css({"color": "green", "cursor": "not-allowed"});
															selektor.removeClass("oznaci_kao_isplaceno");

														}
													});
												};
											}else{
											 	alert('Niste unijeli BF!');
											 	$(this).css('pointer-events','auto');
											}
										}else{
											if ($(this).hasClass('oznaci_kao_isplaceno')) {
												$.ajax({
													url: 'do.php?form=uplati_predracun', 
													type: 'POST',
													data: {'pr_id':pr_id, 'pr_kandidat_id': pr_kandidat_id, 'pr_datum_uplate': date,'bf': bf},
													dataType: 'html',
													success: function(data) {
														alert(data);
														selektor.prev().remove();
														selektor.css({"color": "green", "cursor": "not-allowed"});
														selektor.removeClass("oznaci_kao_isplaceno");

													}
												});
											};
										}
									}else{
										alert('Niste unijeli datum!');
										$(this).css('pointer-events','auto');
									}
								});
								</script>
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
				case "dipl_finances_racuni":
				if((in_array( "9" , $employee_status)) OR (in_array( "1" , $employee_status))){
					
					$f_from = $_GET['f_from'];
					
					if (strpos($f_from, 'to') !== false) {
						$split = explode(" to ",$f_from);
						$datum_od = $split[0];
						$datum_do = $split[1];
					}else{
						$datum_od = "2020-10-10 00:00:00";
						$datum_do = date("Y-m-d H:i:s");
					}
					
					$f_from_f = date('Y-m-d', strtotime($datum_od));
					$f_to_f = date('Y-m-d', strtotime($datum_do));	
					
					
					$start = $month = strtotime($f_from_f);
					$end = strtotime($f_to_f);
					
					$stat_od_f_query = date('Y-m-d 00:00:00', strtotime($datum_od));
					$stat_do_f_query = date('Y-m-d 23:00:00', strtotime($datum_do));
					
					if(isset($_GET['country'])) {
						$country = $_GET['country'];
						if($country == '1'){
							$country = 'BiH';
							$racun_domaca_valuta = 'BAM';
						}elseif($country == '2'){
							$country = 'SRB';
							$racun_domaca_valuta = 'RSD';
						}
					}else{
						$country = 'BiH'; 
						$racun_domaca_valuta = 'BAM';
					}
					
					if(isset($_GET["status_filter"])){
						$status_filter = $_GET["status_filter"];
						$status_query = implode(',', $status_filter);
					}else{
						$status_query = '1,2'; 
					}
					
					
					
		?>
			<div class="row">
				<div class="col-xs-12">
					<h1><i class="fa fa-money idk_color_green" aria-hidden="true"></i> Financije DIPL - <?php echo $country;?></h1>
				</div>
				<div class="col-xs-12">
					<hr/>
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<div class="row">
							<form action="<?php getSiteUrl(); ?>finances" enctype="multipart/form-data" method="get" accept-charset="utf-8" role="form" class="form-horizontal">
							<input type="hidden" name="page" value="dipl_finances_racuni">
							<input type="hidden" name="country" value="<?php echo $_GET["country"]; ?>">
								<div class="col-sm-4">
									<div class="form-group materail-input-block materail-input-block_primary materail-input_slide-line">
										<label for="uplaceno_ili_ne">Period:</label>
										<input type="text" class="form-control" name="f_from" id="f_from" placeholder="Datum" style="padding:17px;border-radius:0;" required>
										<span class="materail-input-block__line"></span>
									</div>
								</div>
								<div class="col-sm-5">
									<label for="status">Status:</label> 
									<select id="status_filter" class="selectpicker" name="status_filter[]" multiple>
										<option value="1" selected>Aktivan</option>
										<option value="2" selected>Storniran</option> 
									</select>
								</div>
								<div class="col-sm-3 idk_margin_top20">
									<button style="width:100%" class="btn btn-success financijeSearchBUtton">Traži</button>
								</div>
							</form>
							<script>
								$("#f_from").flatpickr({
									mode: "range",
									dateFormat: "d.m.Y",
									disableMobile: "true"
								});							
							</script>	
						</div>
						<div class="row idk_margin_top20">
							<div class="col-xs-12">
								<script type="text/javascript">
									$(document).ready(function() {
										$('#idk_table').DataTable({

											responsive: true,

											"order": [[ 1, "asc" ]],

											 "bAutoWidth": false,

											"aoColumns": [
													{ "width": "5%" },
													{ "width": "20%" },
													{ "width": "15%" },
													{ "width": "15%" },
													{ "width": "10%" },
													{ "width": "10%" },
												]
										});
									} );
								</script>
								<table id="idk_table" class="display" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th class="text-center">#</th>
											<th class="text-center">Naziv</th>
											<th class="text-center">Broj</th>
											<th class="text-center">Datum kreiranja</th>
											<th class="text-center">Vrijednost</th>
											<th class="text-center">Status</th>
										</tr>
									</thead>
									<tbody>
										<?php
											$query = $db->prepare("
															SELECT *
															FROM idk_racuni
															WHERE racun_domaca_valuta = :racun_domaca_valuta AND racun_datum_kreiranja BETWEEN '$stat_od_f_query' AND '$stat_do_f_query' AND racun_status IN ($status_query) AND (racun_broj LIKE ('%DIPLRB%') OR racun_broj LIKE ('%DIPLRS%')) ");

											$query->execute(array(
															':racun_domaca_valuta' => $racun_domaca_valuta)); 
										
											while($row = $query->fetch()){

												$racun_id = $row['racun_id'];
												$racun_broj = $row['racun_broj'];
												
												$racun_kandidat_id = $row['racun_kandidat_id'];
												$racun_datum_kreiranja = date("d.m.Y", strtotime($row['racun_datum_kreiranja']));
												$racun_status = $row['racun_status'];
												$racun_domaca_valuta = $row['racun_domaca_valuta'];
												$racun_vrijednost_EUR = $row['racun_vrijednost_EUR'];
												$racun_file = $row['racun_file'];
												$racun_opis = $row['racun_opis'];
												$racun_stornirano = $row['racun_stornirano'];
												$racun_datum_storniranja = $row['racun_datum_storniranja'];
												
												if($racun_domaca_valuta == 'BAM'){
													$vrijednost = $row['racun_vrijednost_BAM'];
												}elseif($racun_domaca_valuta == 'RSD'){
													$vrijednost = $row['racun_vrijednost_RSD'];
												}
												
												if($racun_stornirano == 0){
													$boja = 'success';
													$plusminus = '';
												}elseif($racun_stornirano == 1){
													$boja = 'danger';
													$plusminus = '-';
												}
												
												
												if($racun_status == 1){
													$status_show = '<span class="label label-info material-label material-label_info main-container__column">Aktivan</span>';
												}else if($racun_status == 2){
													$status_show = '<span class="label label-success material-label material-label_success main-container__column">Storniran</span>';
												}
										?>
										<tr>
											<td class="text-center"><?php echo ++$i; ?></td>
											<td class="text-center"><a href="nostrifikacija_diploma?page=otvori_ND_kandidata&id=<?php echo $racun_kandidat_id;?>"><?php echo getImePrezimeNDKanR($racun_kandidat_id);?></a></td>
											<td class="text-center"><?php echo $racun_broj; ?></td>
											<td class="text-center"><?php echo $racun_datum_kreiranja; ?></td> 
											<td class="text-center"><a href="<?php getSiteUrl(); ?>files/racuni_dipl/<?php echo $racun_file; ?>" target="_BLANK" class="label label-<?php echo $boja; ?> material-label material-label_<?php echo $boja; ?> main-container__column"><?php echo $plusminus; ?> <?php echo $vrijednost; ?> <?php echo $racun_domaca_valuta; ?></a></td>
											<td class="text-center"><?php echo $status_show;?></td>
										</tr>
										<?php } ?>
									</tbody>
								</table>
								<script>
								$(".oznaci_kao_isplaceno").on( "click", function() {
									var pr_id = $(this).data("pr_id");
									var pr_kandidat_id = $(this).data("pr_kandidat_id");
									var selektor = $(this);
									

									if ($(this).hasClass('oznaci_kao_isplaceno')) {
										$.ajax({
											url: 'do.php?form=uplati_predracun', 
											type: 'POST',
											data: {'pr_id':pr_id, 'pr_kandidat_id': pr_kandidat_id},
											dataType: 'html',
											success: function(data) {
												alert(data);
												selektor.css({"color": "green", "cursor": "not-allowed"});
												selektor.removeClass("oznaci_kao_isplaceno");

											}
										});
									};
								});
								</script>
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
				case "dipl_inkasso_pregled":
				
				
				
				
				/***************************************************************************/
				/**************** 	PREGLED INKASSO KLIJENATA -> AGENTI     ****************/
				/***************************************************************************/
				if((in_array( "1" , $employee_status)) OR (in_array( "1" , $employee_status)) OR (in_array( "14" , $employee_status))){
					
					
					
		?>
			<div class="row">
				<div class="col-xs-12">
					<h1><i class="fa fa-money idk_color_green" aria-hidden="true"></i> INKASSO PREGLED - DIPL</h1>
				</div>
				<div class="col-xs-12">
					<hr/>
				</div>
			</div>
				<style>
				.akcija{
					display:flex !important;
				}
				.card{
					margin-top: 40px;
					width: 327px;
					height: 208px;
					display: inline-block;
					margin: 10px;
					/* Gradient */

					background: linear-gradient(112.03deg, #68c368 0%, #002e0c 100%);
					border-radius: 16px;
				  }

				.amount{
				  /* 338.34 */


				position: absolute;
				width: 100px;
				height: 40px; 
				padding: 144px 180px 24px 47px;
				font-style: normal;
				font-weight: normal;
				font-size: 32px;
				line-height: 40px;
				letter-spacing: 0.01em;

				color: #FFFFFF;
				}
				.naslov{
				  
				position: absolute;
				width: 200px;
				height: 40px;
				padding: 10px 10px 10px 10px;
				font-style: normal;
				font-weight: normal;
				font-size: 32px;
				line-height: 40px;
				letter-spacing: 0.01em;

				color: #FFFFFF;
				}
				.kurs{
				  /* ¥ */

				position: absolute;
				width: 11px;
				height: 23px;
				padding: 151px 284px 34px 8px;
				font-style: normal;
				font-weight: normal;
				font-size: 14px;
				line-height: 23px;
				margin: -3px 0;
				/* identical to box height */

				letter-spacing: 0.01em;

				color: #FFFFFF;
				}

				.balance{
				  /* Balance */


				position: absolute;
				width: 51px;
				height: 18px;
				padding: 126px 244px 64px 32px;
				font-style: normal;
				font-weight: normal;
				font-size: 14px;
				line-height: 18px;
				/* identical to box height */

				letter-spacing: 0.01em;

				color: #ffffff;

				}

				.card-name{
				position: absolute;
				padding: 32px 234px 160px 32px;
				width: 61px;
				height: 16px;
				}

				.siluet-1{
				  /* Ellipse 4 */
				position: absolute;
				width: 425px;
				height: 171px;
				border-radius: 600px / 200px;
				margin: 98px -20px -61px -78px;
				background: rgba(255, 255, 255, 0.04);
				}

				.siluet-2{
				  position: absolute;
				width: 335px;
				height: 259px;
				border-radius: 50%;
				margin: 64px 89px -115px -97px;
				background: rgba(255, 255, 255, 0.04);
				}
				.rectangle{
				  position: absolute;
				width: 327px;
				height: 208px;

				
				border-radius: 16px;
				}
				.info{
					background: linear-gradient(112.03deg, #3ec3d5 0%, #002e0c 100%);
				}
				.income{
					background: linear-gradient(112.03deg, #68c368 0%, #002e0c 100%);
				}
				.expense{
					background: linear-gradient(112.03deg, #ff5460 0%, #002e0c 100%);
				}
				</style>
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<div class="row idk_margin_top20">
							<div class="col-xs-12">
								<div id="myTabs" class="panel-group material-tabs-group">
									<ul class="nav nav-tabs material-tabs material-tabs_success">
										<li style = "margin-left: 25px;" class="active" ><a href="#jp_inkasso_bih" class="material-tabs__tab-link" data-toggle="tab">INKASSO - BIH</a></li>
										<li><a href="#jp_inkasso_srb" class="material-tabs__tab-link" data-toggle="tab">INKASSO - SRB</a></li>
									</ul>
									<div class="tab-content materail-tabs-content">
									
									<!-- TAB PREGLED KLIJENTA IZ BOSNE -->
									
										<div class="tab-pane fade active in" id="jp_inkasso_bih">					
											<div class="row">
												<div class="col-xs-12">
														<script type="text/javascript">
														$(document).ready(function() {
															$('#idk_table_bih').DataTable({

																responsive: true,

																 "bAutoWidth": false,

																"aoColumns": [
																		{ "width": "5%", "bSortable": false },
																		{ "width": "20%" },
																		{ "width": "10%" },
																		{ "width": "10%" },
																		{ "width": "10%" },
																		{ "width": "10%" },
																		{ "width": "10%" },
																		{ "width": "5%" },
																		{ "width": "10%", "bSortable": false }
																	]
															}); 
														} ); 
													</script>
													<table id="idk_table_bih" class="display" cellspacing="0" width="100%">
														<thead>
															<tr>
																<th class="text-center">#</th>
																<th class="text-center">Naziv</th>
																<th class="text-center">Broj</th>
																<th class="text-center">Datum kreiranja</th>
																<th class="text-center">Rata</th>
																<th class="text-center">Vrijednost</th>
																<th class="text-center">Status</th>
																<th class="text-center">Uplaćeno</th>
																<th class="text-center">Akcija</th>
															</tr>
														</thead>
														<tbody>
															<?php
																$month = date('m');
																$query = $db->prepare("
																				SELECT *
																				FROM idk_predracuni
																				WHERE pr_domaca_valuta = :pr_domaca_valuta
																				AND pr_rata IN ('1','2','3','4','5') AND pr_status IN ('3','4','5')
																				ORDER BY pr_datum_kreiranja ASC");

																$query->execute(array(
																				':pr_domaca_valuta' => 'BAM')); 
																$brojac = 0;
																$uplaceno = 0;
																$neuplaceno = 0;
																$total = 0;
																$incaso1 = 0;
																$incaso2 = 0;
																$incaso3 = 0;
																while($row = $query->fetch()){
																	$brojac = $brojac + 1;
																	$pr_id = $row['pr_id'];
																	$pr_kandidat_id = $row['pr_kandidat_id'];
																	if($row['pr_kandidat_id'] != NULL){
																		$kandidat_name = getImePrezimeNDKanR($row['pr_kandidat_id']);
																		$zadnja_rata_broj = getBrojRataNDR($row['pr_kandidat_id']);
																		$uplacenost_svih_rata_prije_ove = checkUplacenostSvihPrethodnihRata($row['pr_kandidat_id'], $row["pr_rata"]);
																	}
																	else{
																		$kandidat_name = '<span class="label label-success material-label material-label_success main-container__column">Unknown</span>';
																	}
																	
																	$brojac = $brojac++;
																	$pr_broj_predracuna = $row['pr_broj_predracuna'];
																	$pr_datum_kreiranja = date("d.m.Y", strtotime($row['pr_datum_kreiranja']));
																	$pr_vrsta_predracuna = $row['pr_vrsta_predracuna'];
																	$pr_rata = $row['pr_rata'];
																	$rata_text = "Rata ".$pr_rata."";
																	$pr_status = $row['pr_status'];
																	$pr_domaca_valuta = $row['pr_domaca_valuta'];
																	$pr_vrijednost_EUR = $row['pr_vrijednost_EUR'];
																	$pr_file = $row['pr_file'];
																	$pr_uplaceno = $row['pr_uplaceno'];
																	$pr_datum_uplate = date("d.m.Y", strtotime($row['pr_datum_uplate']));
																	$pr_opis = $row['pr_opis'];
																	$pr_stornirano = $row['pr_stornirano'];
																	$pr_datum_storniranja = $row['pr_datum_storniranja'];
																	$pr_vrsta_placanja = $row['pr_vrsta_placanja'];
																	
																	if($pr_domaca_valuta == 'BAM'){
																		$vrijednost = $row['pr_vrijednost_BAM'];
																		$vrijednost_valuta = 'KM';
																	}elseif($pr_domaca_valuta == 'RSD'){
																		$vrijednost = $row['pr_vrijednost_RSD'];
																		$vrijednost_valuta = 'RSD';
																	}
																	
																	if($pr_stornirano == 0){
																		$boja = 'success';
																		$plusminus = '';
																	}elseif($pr_stornirano == 1){
																		$boja = 'danger';
																		$plusminus = '-';
																	}
																	
																	$total = $total + $vrijednost;
																	$total_f = number_format((float)$total, 2, ',', '.');	 	
																	
																	if($pr_status == 0){
																		$status_show = '<span class="label label-warning material-label material-label_warning main-container__column">Arhiviran</span>';
																	}else if($pr_status == 1){
																		$status_show = '<span class="label label-info material-label material-label_info main-container__column">Poslan</span>';
																	}else if($pr_status == 2){
																		$status_show = '<span class="label label-success material-label material-label_success main-container__column">Uplaćen</span>';
																	}else if($pr_status == 3){
																		$status_show = '<span class="label label-danger material-label material-label_danger main-container__column">Inkasso 1</span>';
																		$incaso1 = $incaso1 + $vrijednost;
																		$incaso1_f = number_format((float)$incaso1, 2, '.', ',');
																	}else if($pr_status == 4){
																		$status_show = '<span class="label label-danger material-label material-label_danger main-container__column">Inkasso 2</span>';
																		$incaso2 = $incaso2 + $vrijednost;
																		$incaso2_f = number_format((float)$incaso2 + $vrijednost, 2, '.', ',');
																	}else if($pr_status == 5){
																		$status_show = '<span class="label label-danger material-label material-label_danger main-container__column">Inkasso 3</span>';
																		$incaso3 = $incaso3 + $vrijednost;
																		$incaso3_f = number_format((float)$incaso3, 2, '.', '');
																	}
																	
																	if($pr_uplaceno == 0){
																		$neuplaceno = $neuplaceno + $vrijednost;
																		$neuplaceno_f = number_format((float)$neuplaceno, 2, '.', ',');
																		if(($month == '12' OR ($zadnja_rata_broj == $pr_rata)) AND $uplacenost_svih_rata_prije_ove == 1){
																			$pr_uplaceno_text = '<span class="label label-warning material-label material-label_warning main-container__column">NE</span>';
																			
																		}else{
																			$pr_uplaceno_text = '<span class="label label-warning material-label material-label_warning main-container__column">NE</span>';	} 
																	}else{
																		$pr_uplaceno_text = '<span class="label label-success material-label material-label_success main-container__column">DA</span>';
																		$uplaceno = $uplaceno + $vrijednost;
																		$uplaceno_f = number_format((float)$uplaceno, 2, '.', ',');
																	}
																	
																		$akcija_text = '<a href="nostrifikacija_diploma?page=otvori_ND_kandidata&id='.$pr_kandidat_id.'"><button class="btn material-btn material-btn_success main-container__column material-btn-icon-responsive">Otvori</button>';
														
															?>
															
															<tr>
																<td class="text-center"><?php echo ++$i; ?></td>
																<td class="text-center"><a href="nostrifikacija_diploma?page=otvori_ND_kandidata&id=<?php echo $pr_kandidat_id;?>"><?php echo getImePrezimeNDKanR($pr_kandidat_id);?></a></td>
																<td class="text-center"><?php echo $pr_broj_predracuna; ?></td>
																<td class="text-center"><?php echo $pr_datum_kreiranja; ?></td>
																<td class="text-center"><?php echo $rata_text; ?></td>
																<td class="text-center"><a href="<?php getSiteUrl(); ?>files/predracuni_dipl/<?php echo $pr_file; ?>" target="_BLANK" class="label label-<?php echo $boja; ?> material-label material-label_<?php echo $boja; ?> main-container__column"><?php echo $plusminus; ?> <?php echo $vrijednost; ?> <?php echo $pr_domaca_valuta; ?></a></td>
																<td class="text-center"><?php echo $status_show;?></td> 
																<td class="text-center"><?php echo $pr_uplaceno_text;?></td>
																<td class="text-center"><?php echo $akcija_text;?></td>
															</tr>
															<?php } ?>
														</tbody>
													</table>
												</div>
											</div>
										</div>
										
										<!-- TAB PREGLED KLIJENTA IZ SRB -->
									
										<div class="tab-pane fade" id="jp_inkasso_srb">					
											<div class="row">
												<div class="col-xs-12">
														<script type="text/javascript">
														$(document).ready(function() {
															$('#idk_table_srb').DataTable({

																responsive: true,

																 "bAutoWidth": false,

																"aoColumns": [
																		{ "width": "5%", "bSortable": false },
																		{ "width": "20%" },
																		{ "width": "10%" },
																		{ "width": "10%" },
																		{ "width": "10%" },
																		{ "width": "10%" },
																		{ "width": "10%" },
																		{ "width": "5%" },
																		{ "width": "10%", "bSortable": false }
																	]
															});
														} );
													</script>
													<table id="idk_table_srb" class="display" cellspacing="0" width="100%">
														<thead>
															<tr>
																<th class="text-center">#</th>
																<th class="text-center">Naziv</th>
																<th class="text-center">Broj</th>
																<th class="text-center">Datum kreiranja</th>
																<th class="text-center">Rata</th>
																<th class="text-center">Vrijednost</th>
																<th class="text-center">Status</th>
																<th class="text-center">Uplaćeno</th>
																<th class="text-center">Akcija</th>
															</tr>
														</thead>
														<tbody>
															<?php
																$month = date('m');
																$query = $db->prepare("
																				SELECT *
																				FROM idk_predracuni
																				WHERE pr_domaca_valuta = :pr_domaca_valuta
																				AND pr_rata IN ('1','2','3','4','5') AND pr_status IN ('3','4','5')
																				ORDER BY pr_datum_kreiranja ASC");

																$query->execute(array(
																				':pr_domaca_valuta' => 'RSD')); 
																$brojac = 0;
																$uplaceno = 0;
																$neuplaceno = 0;
																$total = 0;
																$incaso1 = 0;
																$incaso2 = 0;
																$incaso3 = 0;
																while($row = $query->fetch()){
																	$brojac = $brojac + 1;
																	$pr_id = $row['pr_id'];
																	$pr_kandidat_id = $row['pr_kandidat_id'];
																	if($row['pr_kandidat_id'] != NULL){
																		$kandidat_name = getImePrezimeNDKanR($row['pr_kandidat_id']);
																		$zadnja_rata_broj = getBrojRataNDR($row['pr_kandidat_id']);
																		$uplacenost_svih_rata_prije_ove = checkUplacenostSvihPrethodnihRata($row['pr_kandidat_id'], $row["pr_rata"]);
																	}
																	else{
																		$kandidat_name = '<span class="label label-success material-label material-label_success main-container__column">Unknown</span>';
																	}
																	
																	$brojac = $brojac++;
																	$pr_broj_predracuna = $row['pr_broj_predracuna'];
																	$pr_datum_kreiranja = date("d.m.Y", strtotime($row['pr_datum_kreiranja']));
																	$pr_vrsta_predracuna = $row['pr_vrsta_predracuna'];
																	$pr_rata = $row['pr_rata'];
																	$rata_text = "Rata ".$pr_rata."";
																	$pr_status = $row['pr_status'];
																	$pr_domaca_valuta = $row['pr_domaca_valuta'];
																	$pr_vrijednost_EUR = $row['pr_vrijednost_EUR'];
																	$pr_file = $row['pr_file'];
																	$pr_uplaceno = $row['pr_uplaceno'];
																	$pr_datum_uplate = date("d.m.Y", strtotime($row['pr_datum_uplate']));
																	$pr_opis = $row['pr_opis'];
																	$pr_stornirano = $row['pr_stornirano'];
																	$pr_datum_storniranja = $row['pr_datum_storniranja'];
																	$pr_vrsta_placanja = $row['pr_vrsta_placanja'];
																	
																	if($pr_domaca_valuta == 'BAM'){
																		$vrijednost = $row['pr_vrijednost_BAM'];
																		$vrijednost_valuta = 'KM';
																	}elseif($pr_domaca_valuta == 'RSD'){
																		$vrijednost = $row['pr_vrijednost_RSD'];
																		$vrijednost_valuta = 'RSD';
																	}
																	
																	if($pr_stornirano == 0){
																		$boja = 'success';
																		$plusminus = '';
																	}elseif($pr_stornirano == 1){
																		$boja = 'danger';
																		$plusminus = '-';
																	}
																	
																	$total = $total + $vrijednost;
																	$total_f = number_format((float)$total, 2, ',', '.');	 	
																	
																	if($pr_status == 0){
																		$status_show = '<span class="label label-warning material-label material-label_warning main-container__column">Arhiviran</span>';
																	}else if($pr_status == 1){
																		$status_show = '<span class="label label-info material-label material-label_info main-container__column">Poslan</span>';
																	}else if($pr_status == 2){
																		$status_show = '<span class="label label-success material-label material-label_success main-container__column">Uplaćen</span>';
																	}else if($pr_status == 3){
																		$status_show = '<span class="label label-danger material-label material-label_danger main-container__column">In Caso 1</span>';
																		$incaso1 = $incaso1 + $vrijednost;
																		$incaso1_f = number_format((float)$incaso1, 2, '.', ',');
																	}else if($pr_status == 4){
																		$status_show = '<span class="label label-danger material-label material-label_danger main-container__column">In Caso 2</span>';
																		$incaso2 = $incaso2 + $vrijednost;
																		$incaso2_f = number_format((float)$incaso2 + $vrijednost, 2, '.', ',');
																	}else if($pr_status == 5){
																		$status_show = '<span class="label label-danger material-label material-label_danger main-container__column">In Caso 3</span>';
																		$incaso3 = $incaso3 + $vrijednost;
																		$incaso3_f = number_format((float)$incaso3, 2, '.', '');
																	}
																	
																	if($pr_uplaceno == 0){
																		$neuplaceno = $neuplaceno + $vrijednost;
																		$neuplaceno_f = number_format((float)$neuplaceno, 2, '.', ',');
																		if(($month == '12' OR ($zadnja_rata_broj == $pr_rata)) AND $uplacenost_svih_rata_prije_ove == 1){
																			$pr_uplaceno_text = '<span class="label label-warning material-label material-label_warning main-container__column">NE</span>';
																			
																		}else{
																			$pr_uplaceno_text = '<span class="label label-warning material-label material-label_warning main-container__column">NE</span>';	} 
																	}else{
																		$pr_uplaceno_text = '<span class="label label-success material-label material-label_success main-container__column">DA</span>';
																		$uplaceno = $uplaceno + $vrijednost;
																		$uplaceno_f = number_format((float)$uplaceno, 2, '.', ',');
																	}
																	
																	$akcija_text = '<a href="nostrifikacija_diploma?page=otvori_ND_kandidata&id='.$pr_kandidat_id.'"><button class="btn material-btn material-btn_success main-container__column material-btn-icon-responsive">Otvori</button>';
															?>
															
															<tr>
																<td class="text-center"><?php echo ++$i; ?></td>
																<td class="text-center"><a href="nostrifikacija_diploma?page=otvori_ND_kandidata&id=<?php echo $pr_kandidat_id;?>"><?php echo getImePrezimeNDKanR($pr_kandidat_id);?></a></td>
																<td class="text-center"><?php echo $pr_broj_predracuna; ?></td>
																<td class="text-center"><?php echo $pr_datum_kreiranja; ?></td>
																<td class="text-center"><?php echo $rata_text; ?></td>
																<td class="text-center"><a href="<?php getSiteUrl(); ?>files/predracuni_dipl/<?php echo $pr_file; ?>" target="_BLANK" class="label label-<?php echo $boja; ?> material-label material-label_<?php echo $boja; ?> main-container__column"><?php echo $plusminus; ?> <?php echo $vrijednost; ?> <?php echo $pr_domaca_valuta; ?></a></td>
																<td class="text-center"><?php echo $status_show;?></td> 
																<td class="text-center"><?php echo $pr_uplaceno_text;?></td>
																<td class="text-center"><?php echo $akcija_text;?></td>
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
					
						
				/***************************************************************************/
				/**************** 	PREGLED INKASSO KLIJENATA -> AGENTI     ****************/
				/****************			END								****************/
				/***************************************************************************/
					break;
					
					case "dugovanjaPoStaromSistemu":
						if((in_array( "1" , $employee_status))){
						$drzave = array("BAM", "RSD");
						$trenutniDatum = date("Y-m-d"); //trenutno vrijeme za potrebe kôda ispod
						
						//----------------------vrijednosti kolone "vrsta_ugovora_nd_kandidata" u tabeli "idk_nd_kandidata" START
						$r1 = array(9,21,10,41,51,61,71,81);
						$r2 = array(1,22,2,42,52,62,72,82);
						$r3 = array(5,23,6,43,53,63,73,83);
						$r4 = array(7,24,8,44,54,64,74,84);
						$r5 = array(3,25,4,45,55,65,75,85);
						$r6 = array(26);
						$r12 = array(27);
						$rMikro = array(11,12);
						//----------------------vrijednosti kolone "vrsta_ugovora_nd_kandidata" u tabeli "idk_nd_kandidata" END
						//----------------------prethodne vrijednosti implode za query START
						$rata1 = implode(',',$r1);
						$rata2 = implode(',',$r2);
						$rata3 = implode(',',$r3);
						$rata4 = implode(',',$r4);
						$rata5 = implode(',',$r5);
						$rata6 = implode(',',$r6);
						$rata12 = implode(',',$r12);
						$rataMikrofin = implode(',',$rMikro);
						//----------------------prethodne vrijednosti implode za query END
						//funkcija za određivanje inkaso statusa START
						// function getInkasoStatusDIPLKRR($predvidjenoVrijeme){
							// $result = 1; 
							// $trenutnoVrijeme = date("Y-m-d");
							
							// $inCaso1 = date("Y-m-d", strtotime($predvidjenoVrijeme."+21 days"));
							// $inCaso2 = date("Y-m-d", strtotime($predvidjenoVrijeme."+40 days"));
							// $inCaso3 = date("Y-m-d", strtotime($predvidjenoVrijeme."+90 days")); 
							
							// if($inCaso1 < $trenutnoVrijeme){
								// $result = 3; 
							// }
							// if($inCaso2 < $trenutnoVrijeme){
								// $result = 4; 
							// }
							// if($inCaso3 < $trenutnoVrijeme){
								// $result = 5; 
							// }
							
							// return $result;
						// }
						// //funkcija za određivanje inkaso statusa END
						
						// function getStatusDIPLKandidatRR($status, $podstatus){
							// $statusPrikaz = "";
							// if($status == 1 AND $podstatus == 1){
								// $statusPrikaz = 'Lead';
							// }else if($status == 1 AND $podstatus == 6){
								// $statusPrikaz = 'Neuspješan kontakt 1';
							// }else if($status == 1 AND $podstatus == 2){
								// $statusPrikaz = 'Neuspješan kontakt 3';
							// }else if($status == 1 AND $podstatus == 3){
								// $statusPrikaz = 'Zainteresiran Lead';
							// }else if($status == 1 AND $podstatus == 4){
								// $statusPrikaz = 'Nezainteresiran Lead';
							// }else if($status == 1 AND $podstatus == 5){
								// $statusPrikaz = 'U obradi Lead';
							// }else if($status == 1 AND $podstatus == 7){
								// $statusPrikaz = 'Neuspješan Lead 1';
							// }else if($status == 1 AND $podstatus == 8){
								// $statusPrikaz = 'Neuspješan Lead 2';
							// }else if($status == 1 AND $podstatus == 9){
								// $statusPrikaz = 'Termin Zainteresiran';
							// }else if($status == 1 AND $podstatus == 10){
								// $statusPrikaz = 'Termin Ostali';
							// }else if($status == 1 AND $podstatus == 11){
								// $statusPrikaz = 'Lead NL';
							// }else if($status == 1 AND $podstatus == 12){
								// $statusPrikaz = 'Lead NZ';
							// }else if($status == 2){
								// $statusPrikaz = 'Prikupljanje dokumentacije';
							// }else if($status == 3){
								// $statusPrikaz = 'Poslana pošta';
							// }else if($status == 4){
								// $statusPrikaz = 'U obradi';
							// }else if($status == 5){
								// $statusPrikaz = 'Dopuna dokumentacije';
							// }else if($status == 6){
								// $statusPrikaz = 'Završen';
							// }else if($status == 7){
								// $statusPrikaz = 'Arhiv';
							// }else{
								// $statusPrikaz = 'Nije definisano';
							// }
							
							// return $statusPrikaz;
						// }
		?>
						
						<div class="row">
							<div class="col-xs-12">
								<h1><i class="fa fa-money idk_color_green" aria-hidden="true"></i> Dugovanja po starom sistemu naplate</h1>
							</div>
						</div>
						<div class="row idk_margin_top20">
							<div class="col-md-12">
								<div class="content_box">
									<div class="row idk_margin_top20">
										<div class="col-xs-12">
											<script type="text/javascript">
												$(document).ready(function() {
													$('#customers').DataTable({

														responsive: true,

														"order": [[ 0, "asc" ]],

														 "bAutoWidth": false,

														"aoColumns": [
																{ "width": "5%" },
																{ "width": "5%" },
																{ "width": "10%" },
																{ "width": "10%" },
																{ "width": "10%", "bSortable": false },
																{ "width": "5%" },
																{ "width": "5%"},
																{ "width": "10%" },
																{ "width": "5%" },
																{ "width": "5%" },
																{ "width": "10%" }
															]
													});
												} );
											</script>
											<table id="customers" class="display" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th>RBP</th>
														<th>ID kandidata</th>
														<th>Ime Prezime</th>
														<th>Kontakt</th>
														<th>Status</th>
														<th>Ugovor rata</th>
														<th>Zadnja uplaćena</th>
														<th>Datum uplate</th>
														<th>Preostalo za uplatiti</th>
														<th>Duguje</th>
														<th>Duguje iznos</th>
													</tr>
												</thead>
												<tbody>
												<?php 
													$brojac = 0;
													$sumIznos = 0;
													$query1 = $db->prepare("
														SELECT
															k.id_broj_nd_kandidata, 
															k.ime_nd_kandidata, 
															k.prezime_nd_kandidata, 
															k.mobilni_nd_kandidata, 
															k.status_nd_kandidata, 
															k.pstatus_nd_kandidata,
															r.br_r, 
															r.vr_u_r, 
															k.vrsta_ugovora_nd_kandidata, 
															
															k.zaduzen_zaposlenik_nd_kandidata
														FROM 
															idk_nd_kandidata k
														INNER JOIN 
															idk_nd_rate r 
														ON 
															k.id_broj_nd_kandidata = r.id_nd_kan 
															AND 
															r.id_r = (
																SELECT 
																	max(ra.id_r) AS idRate
																FROM 
																	idk_nd_rate ra 
																WHERE 
																	ra.id_nd_kan = k.id_broj_nd_kandidata
															) 
															AND 
															(
																SELECT 
																	count(p.pr_id) AS brojPredracuna
																FROM 
																	idk_predracuni p
																WHERE 
																	p.pr_kandidat_id = k.id_broj_nd_kandidata
																	AND 
																	p.pr_status IN (1,2,3,4,5)
																	AND 
																	p.pr_vrsta_predracuna = 1
															) = 0
														WHERE 
															k.vrsta_ugovora_nd_kandidata NOT IN (".$rata1.",".$rataMikrofin.")
														ORDER BY
															r.vr_u_r 
														ASC
													");
													$query1->execute();
													while($row1 = $query1->fetch()){
														$broj_rata = 0;
														$preostaloZaUplatiti = 0;
														$idKandidat = intval($row1["id_broj_nd_kandidata"]);
														$imeKandidat = $row1["ime_nd_kandidata"];
														$prezimeKandidat = $row1["prezime_nd_kandidata"];
														$mobitelKandidat = $row1["mobilni_nd_kandidata"];
														$statusKandidat = intval($row1["status_nd_kandidata"]);
														$podstatusKandidat = intval($row1["pstatus_nd_kandidata"]);
														$ugovorKandidat = intval($row1["vrsta_ugovora_nd_kandidata"]);
														$vrijemeUplateRate = date("Y-m-d", strtotime($row1["vr_u_r"]));
														$brojRata = intval($row1["br_r"]);
														
														if(in_array($ugovorKandidat, $r1)){
															$broj_rata = 1;
														}else if(in_array($ugovorKandidat, $r2)){
															$broj_rata = 2;
														}else if(in_array($ugovorKandidat, $r3)){ 
															$broj_rata = 3;
														}else if(in_array($ugovorKandidat, $r4)){
															$broj_rata = 4;
														}else if(in_array($ugovorKandidat, $r5)){
															$broj_rata = 5;
														}else if(in_array($ugovorKandidat, $r6)){
															$broj_rata = 6;
														}else if(in_array($ugovorKandidat, $r12)){
															$broj_rata = 12;
														}else if(in_array($ugovorKandidat, $rMikro)){
															$broj_rata = 1;
														}else{
															$broj_rata = 0;
														}
														
														if($brojRata < $broj_rata){
															
															$preostaloZaUplatiti = $broj_rata - $brojRata;
															$dugujeZaUplatiti = 0;
															if($broj_rata != 2){
																$queryUplataPrve = $db->prepare("
																	SELECT 
																		vr_u_r
																	FROM 
																		idk_nd_rate
																	WHERE 
																		id_nd_kan = :id_nd_kan
																");
																$queryUplataPrve->execute(array(
																	':id_nd_kan' => $idKandidat
																));
																$rowUplataPrve = $queryUplataPrve->fetch();
																$datumZplataPrve = date("Y-m-d",strtotime($rowUplataPrve["vr_u_r"]));
																$j = 0;
																$sumaIznosDugovanja = 0;
																for($i = $brojRata+1; $i <= $broj_rata; $i++){
																	$iznosDugovanja = 0;
																	$j = $i - 1;
																	$predvidjeniZaUplatuRate = date("Y-m-d",strtotime($datumZplataPrve."+".$j." months"));
																	if($predvidjeniZaUplatuRate <= $trenutniDatum){
																		$dugujeZaUplatiti++; 
																		$iznosDugovanja = getIznosRate($ugovorKandidat, "BiH", "rata".$i);
																		$sumaIznosDugovanja = $sumaIznosDugovanja + $iznosDugovanja; 
																	}
																}
																if($dugujeZaUplatiti != 0){
																	$brojac++;
																	echo '
																		<tr>
																			<td>'.$brojac.'</td>
																			<td>'.$idKandidat.'</td>
																			<td><a href="'.getSiteUrlr().'nostrifikacija_diploma?page=otvori_ND_kandidata&id='.$idKandidat.'">'.$imeKandidat.' '.$prezimeKandidat.'</td>
																			<td>'.$mobitelKandidat.'</td>
																			<td>'.getStatusDIPLKandidatR($statusKandidat, $podstatusKandidat).'</td>
																			<td>'.$broj_rata.'</td>
																			<td>'.$brojRata.'</td>
																			<td data-order="'. $vrijemeUplateRate .'">'.$vrijemeUplateRate.'</td>
																			<td>'.$preostaloZaUplatiti.'</td>
																			<td>'.$dugujeZaUplatiti.'</td>
																			<td>'.number_format($sumaIznosDugovanja, 2, '.', ',').'</td>
																		</tr>
																	';
																}
															}else{
																if($statusKandidat == 6){
																	$queryZavrsen = $db->prepare("
																		SELECT 
																			MAX(vrijeme_promjene_statusa_nd_kandidata) AS datumZavrsen
																		FROM 
																			idk_nd_kandidata_status_log
																		WHERE 
																			idd_broj_nd_kandidata = ".$idKandidat."
																			AND 
																			status_nd_kandidata = 6
																			AND 
																			broj_dana_statusa_nd_kandidata is null
																	");
																	$queryZavrsen->execute();
																	if($queryZavrsen->rowCount() != 0){
																		$rowZavrsen = $queryZavrsen->fetch();
																		$predvidjeniZaUplatuRate = date("Y-m-d", strtotime($rowZavrsen["datumZavrsen"]));
																	}else{
																		$predvidjeniZaUplatuRate = $trenutniDatum;
																	}
																	$sumaIznosDugovanja = 0;
																	$iznosDugovanja = 0;
																	if($predvidjeniZaUplatuRate <= $trenutniDatum){
																		$dugujeZaUplatiti++; 
																		$iznosDugovanja = getIznosRate($ugovorKandidat, "BiH", "rata2");
																		$sumaIznosDugovanja = $sumaIznosDugovanja + $iznosDugovanja; 
																	}
																	if($dugujeZaUplatiti != 0){
																		$brojac++;
																		echo '
																			<tr>
																				<td>'.$brojac.'</td>
																				<td>'.$idKandidat.'</td>
																				<td><a href="'.getSiteUrlr().'nostrifikacija_diploma?page=otvori_ND_kandidata&id='.$idKandidat.'">'.$imeKandidat.' '.$prezimeKandidat.'</td>
																				<td>'.$mobitelKandidat.'</td>
																				<td>'.getStatusDIPLKandidatR($statusKandidat, $podstatusKandidat).'</td>
																				<td>'.$broj_rata.'</td>
																				<td>'.$brojRata.'</td>
																				<td data-order="'. $vrijemeUplateRate .'">'.$vrijemeUplateRate.'</td>
																				<td>'.$preostaloZaUplatiti.'</td>
																				<td>'.$dugujeZaUplatiti.'</td>
																				<td>'.number_format($sumaIznosDugovanja, 2, '.', ',').'</td>
																			</tr>
																		';
																	}
																}
															}
															$sumIznos = $sumIznos + $sumaIznosDugovanja;
														}
														
													}
												?>
												</tbody>
												<tfoot>
													<tr>
														<th colspan = "10" style = "text-align: right;">Suma</th>
														<th><?php echo number_format($sumIznos, 2, '.', ',');?></th>
													</tr>
												</tfoot>
											</table>
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
					case "ch_predracuni":
					?>
					<style>
						.akcija{
							display:flex !important;
						}
						.card{
							margin-top: 40px;
							width: 327px;
							height: 208px;
							display: inline-block;
							margin: 10px;
							/* Gradient */

							background: linear-gradient(112.03deg, #68c368 0%, #002e0c 100%);
							border-radius: 16px;
						  }

						.amount{
						  /* 338.34 */


						position: absolute;
						width: 100px;
						height: 40px; 
						padding: 144px 180px 24px 47px;
						font-style: normal;
						font-weight: normal;
						font-size: 32px;
						line-height: 40px;
						letter-spacing: 0.01em;

						color: #FFFFFF;
						}
						.naslov{
						  
						position: absolute;
						width: 200px;
						height: 40px;
						padding: 10px 10px 10px 10px;
						font-style: normal;
						font-weight: normal;
						font-size: 32px;
						line-height: 40px;
						letter-spacing: 0.01em;

						color: #FFFFFF;
						}
						.kurs{
						  /* ¥ */

						position: absolute;
						width: 11px;
						height: 23px;
						padding: 151px 284px 34px 8px;
						font-style: normal;
						font-weight: normal;
						font-size: 14px;
						line-height: 23px;
						margin: -3px 0;
						/* identical to box height */

						letter-spacing: 0.01em;

						color: #FFFFFF;
						}

						.balance{
						  /* Balance */


						position: absolute;
						width: 51px;
						height: 18px;
						padding: 126px 244px 64px 32px;
						font-style: normal;
						font-weight: normal;
						font-size: 14px;
						line-height: 18px;
						/* identical to box height */

						letter-spacing: 0.01em;

						color: #ffffff;

						}

						.card-name{
						position: absolute;
						padding: 32px 234px 160px 32px;
						width: 61px;
						height: 16px;
						}

						.siluet-1{
						  /* Ellipse 4 */
						position: absolute;
						width: 425px;
						height: 171px;
						border-radius: 600px / 200px;
						margin: 98px -20px -61px -78px;
						background: rgba(255, 255, 255, 0.04);
						}

						.siluet-2{
						  position: absolute;
						width: 335px;
						height: 259px;
						border-radius: 50%;
						margin: 64px 89px -115px -97px;
						background: rgba(255, 255, 255, 0.04);
						}
						.rectangle{
						  position: absolute;
						width: 327px;
						height: 208px;

						
						border-radius: 16px;
						}
						.info{
							background: linear-gradient(112.03deg, #3ec3d5 0%, #002e0c 100%);
						}
						.income{
							background: linear-gradient(112.03deg, #68c368 0%, #002e0c 100%);
						}
						.expense{
							background: linear-gradient(112.03deg, #ff5460 0%, #002e0c 100%);
						}
					</style>
					<h1> <i class="fa fa-money idk_color_green" aria-hidden="true"></i> Financije DIPL - CH Predračuni:</h1>
						<hr>
						<div class="row">
							<div class="col-md-12">
								<div class="content_box">	
									<div class="col-12 row">
										<div class="col-lg-3 col-md-3">
											<label for="filter_select_is_uplaceno">Status uplate:</label>
											<select id="filter_select_is_uplaceno" class="selectpicker" name="filter_select_is_uplaceno" multiple>
												<option value="1">Uplaćeno</option>
												<option selected value="0">Neuplaćeno</option>
											</select>
										</div>

										<div class="col-lg-3 col-md-3">
											<label for="filter_select_rate">Rata:</label>
											<select id="filter_select_rate" class="selectpicker" name="filter_select_rate" multiple>
												<option selected value="1" >1. Rata</option>
												<option selected value="2" >2. Rata</option>
												<option selected value="3" >3. Rata</option>
												<option selected value="4" >4. Rata</option>
												<option selected value="5" >5. Rata</option>
												<option selected value="6" >6. Rata</option>
												<option selected value="7" >7. Rata</option>
												<option selected value="8" >8. Rata</option>
												<option selected value="9" >9. Rata</option>
												<option selected value="10">10. Rata</option>
												<option selected value="11">11. Rata</option>
												<option selected value="12">12. Rata</option>
											</select>
										</div>
										<div class="col-lg-3 col-md-3">
											<label for="filter_select_status_predracuna">Status predracuna:</label>
											<select id="filter_select_status_predracuna" class="selectpicker" name="filter_select_status_predracuna" multiple>
												<option value="0">Arhiva</option>
												<option selected value="1">Poslan</option>
												<option value="2">Uplaćeno</option>
												<option selected value="3">IN CASO 1</option>
												<option selected value="4">IN CASO 2</option>
												<option selected value="5">IN CASE 3</option>
											</select>
										</div>
										<div class="col-lg-3 col-md-3">
											<label for="filter_select_drzavu">Država:</label>
											<select id="filter_select_drzavu" class="selectpicker" name="filter_select_drzavu" multiple>
												<option selected value="'BAM'">Bosna i Hercegovina</option>
												<option selected value="'RSD'">Srbija</option>
												<option selected value="'EUR'">Ostale</option>
											</select>
										</div>
										<br><br>
										<br><br>
										<div class="col-lg-2 col-md-2">
											<div class="form-group materail-input-block materail-input-block_primary materail-input_slide-line" style="margin: 0;">
												<label for="filter_kreiran_predracun_period">Datum kreiranja predračuna:</label>
												<input type="text" class="form-control" name="filter_kreiran_predracun_period" id="filter_kreiran_predracun_period" placeholder="Datum" style="padding:17px;border-radius:0;">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
										<div class="col-lg-2 col-md-2">
											<div class="form-group materail-input-block materail-input-block_primary materail-input_slide-line" style="margin: 0;">
												<label for="filter_uplacen_predracun_period">Datum uplate predračuna:</label>
												<input type="text" class="form-control" name="filter_uplacen_predracun_period" id="filter_uplacen_predracun_period" placeholder="Datum" style="padding:17px;border-radius:0;">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
										<div class="col-lg-2 col-md-2 offset-3 idk_margin_top20">
											<button id="filter_button_ch_predracuni" style="width:100%" class="btn btn-success">Traži</button>
										</div>
										<div class="col-lg-2 col-md-2 offset-3 idk_margin_top20">
											<button id="export_button_ch_predracuni" style="width:100%" class="btn btn-success">Export</button>
										</div>
										<div class="col-lg-2 col-md-2 idk_margin_top20">
											<button id="download_button_ch_predracuni" style="width:100%" class="btn btn-success">Download</button>
										</div>
										<?php if($logged_employee_id == 67 OR $logged_employee_id == 75 OR $logged_employee_id == 20 OR $logged_employee_id == 412 OR $logged_employee_id == 202) { ?>
										<div class="col-lg-2 col-md-2 idk_margin_top20">
											<button style="width:100%" class="btn btn-success" id="filter_button_export_muster_predracuni">Export CH računovodstvo</button>
										</div>
										<?php } ?>
										
										<div id = "to_append_to_ch_predracuni" width = "100" style = "margin-top: 100px; min-height: 500px;" ></div>
										<div id = "to_append_to_export" style="display:none;"></div>
										<div class="col-xs-12" id="to_append_to_ch_muster_export_predracuni" style="display:none;"></div>
									</div>
								</div>
							</div>
						</div>
					<script>
						function getTablePredracuni(){
							var filter_kreiran_predracun_period = $('#filter_kreiran_predracun_period').val();
							var filter_uplacen_predracun_period = $('#filter_uplacen_predracun_period').val();
							var filter_select_is_uplaceno 		= $('#filter_select_is_uplaceno').val();
							var filter_select_rate 				= $('#filter_select_rate').val();
							var filter_select_status_predracuna = $('#filter_select_status_predracuna').val();
							var filter_select_drzavu 			= $('#filter_select_drzavu').val();
							
							$('#to_append_to_ch_predracuni').fadeOut(600, function(){
								$.ajax({
									url: 'ajax_data.php?page=get_financije_ch_predracuni',
									type: 'POST',
									dataType: 'html',
									data: {
											'filter_kreiran_predracun_period' 	: filter_kreiran_predracun_period,
											'filter_uplacen_predracun_period' 	: filter_uplacen_predracun_period,
											'filter_select_is_uplaceno' 		: filter_select_is_uplaceno,
											'filter_select_rate' 				: filter_select_rate,
											'filter_select_status_predracuna' 	: filter_select_status_predracuna,
											'filter_select_drzavu' 				: filter_select_drzavu
										},
									success: function(data) {
										$("#to_append_to_ch_predracuni").fadeOut(600, function(){
											$("#to_append_to_ch_predracuni").empty().append(data).fadeIn(800);
											var table = $('#table_predracuni_ch').DataTable({
												responsive: true,
												 "bAutoWidth": false
											});
										});
									},
									error: function (xhr, ajaxOptions, thrownError) {
										alert(xhr.status);
										alert(thrownError);
									}
								});
								$('#to_append_to_ch_predracuni').empty();
								$('#to_append_to_ch_predracuni').append('<div class="lds-hourglass"></div>').fadeIn(600);
							});
						}
						$(document).ready(function() {
						   getTablePredracuni();
						});
						$("#filter_kreiran_predracun_period").flatpickr({
							mode: "range",
							dateFormat: "d.m.Y",
							disableMobile: "true"
						});
						$("#filter_uplacen_predracun_period").flatpickr({
							mode: "range",
							dateFormat: "d.m.Y",
							disableMobile: "true"
						});
						$('#filter_button_ch_predracuni').on('click',function(){
							getTablePredracuni();
						});
						
						$("#filter_button_export_muster_predracuni").on('click', function(){
							var filter_period 			= $('#filter_kreiran_predracun_period').val();
							var filter_select_drzavu 	= $('#filter_select_drzavu').val();
							
							$.ajax({
								url: 'export_excel.php?prozor=export_ch_muster_predracuni',
								type: 'POST',
								dataType: 'html',
								data: {
										'filter_period' 		: filter_period,
										'filter_select_drzavu' 	: filter_select_drzavu
								},
								success: function(data) {
									$('#to_append_to_ch_muster_export_predracuni').append(data);
									$("#table_export_muster_ch_predracuni").table2excel({
										exclude: ".noExl",
										name: "Jobstep Int GmbH - Vorlage Buchungsbeleg Rechnung",
										filename: "Vorlage_Buchungsbeleg_Rechnung",
										fileext: ".xls"
									}); 	  
								},
								error: function (xhr, ajaxOptions, thrownError) {
									alert(xhr.status);
									alert(thrownError);
								}
							});
						});
						
						$('#export_button_ch_predracuni').on('click',function(){
							
							var filter_kreiran_predracun_period = $('#filter_kreiran_predracun_period').val();
							var filter_uplacen_predracun_period = $('#filter_uplacen_predracun_period').val();
							var filter_select_is_uplaceno 		= $('#filter_select_is_uplaceno').val();
							var filter_select_rate 				= $('#filter_select_rate').val();
							var filter_select_status_predracuna = $('#filter_select_status_predracuna').val();
							var filter_select_drzavu 			= $('#filter_select_drzavu').val();

							$.ajax({
								url: 'export_excel.php?prozor=export_predracuni_ch',
								type: 'POST',
								dataType: 'html',
								data: {
										'filter_kreiran_predracun_period' 	: filter_kreiran_predracun_period,
										'filter_uplacen_predracun_period' 	: filter_uplacen_predracun_period,
										'filter_select_is_uplaceno' 		: filter_select_is_uplaceno,
										'filter_select_rate' 				: filter_select_rate,
										'filter_select_status_predracuna' 	: filter_select_status_predracuna,
										'filter_select_drzavu' 				: filter_select_drzavu
									},
								success: function(data) {
									// alert(data);
									$('#to_append_to_export').html(data);
									$("#table_export_predracuni_ch").table2excel({
										exclude: ".noExl",
										name: "Predracuni_CH",
										filename: "Predracuni_CH",
										fileext: ".xls"
									}); 	  
								},
								error: function (xhr, ajaxOptions, thrownError) {
									alert(xhr.status);
									alert(thrownError);
								}
							});
						});
						$('#download_button_ch_predracuni').on('click',function(){
							var filter_kreiran_predracun_period = $('#filter_kreiran_predracun_period').val();
							var filter_uplacen_predracun_period = $('#filter_uplacen_predracun_period').val();
							var filter_select_is_uplaceno 		= $('#filter_select_is_uplaceno').val();
							var filter_select_rate 				= $('#filter_select_rate').val();
							var filter_select_status_predracuna = $('#filter_select_status_predracuna').val();
							var filter_select_drzavu 			= $('#filter_select_drzavu').val();
							
							$.ajax({
								url: 'ajax_data.php?page=download_predracuni_ch',
								type: 'POST',
								dataType: 'html',
								data: {
										'filter_kreiran_predracun_period' 	: filter_kreiran_predracun_period,
										'filter_uplacen_predracun_period' 	: filter_uplacen_predracun_period,
										'filter_select_is_uplaceno' 		: filter_select_is_uplaceno,
										'filter_select_rate' 				: filter_select_rate,
										'filter_select_status_predracuna' 	: filter_select_status_predracuna,
										'filter_select_drzavu' 				: filter_select_drzavu
									},
								success: function(data) {
									window.location.href = data;
								},
								error: function (xhr, ajaxOptions, thrownError) {
									alert(xhr.status);
									alert(thrownError);
								}
							});
						});
					</script>
					<?php
					
				break;
				case "ch_racuni":
					?>
					<div class="row">
						<div class="col-xs-12">
							<h1><i class="fa fa-money idk_color_green" aria-hidden="true"></i> Financije DIPL - CH Računi:</h1>
						</div>
						<div class="col-xs-12">
							<hr/>
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-12">
							<div class="content_box">
								<div class="row">
									<div class="col-lg-3">
										<div class="form-group materail-input-block materail-input-block_primary materail-input_slide-line">
											<label for="filter_period">Period:</label>
											<input type="text" class="form-control" name="filter_period" id="filter_period" placeholder="Datum" style="padding:17px;border-radius:0;" required>
											<span class="materail-input-block__line"></span>
										</div>
									</div>
									<div class="col-lg-3">
										<label for="filter_status">Status:</label> 
										<select id="filter_status" class="selectpicker" multiple>
											<option value="1" selected>Aktivan</option>
											<option value="2" selected>Storniran</option>
										</select>
									</div>
									<div class="col-lg-3">
										<label for="filter_select_drzavu">Država:</label> 
										<select id="filter_select_drzavu" class="selectpicker" name="filter_select_drzavu" multiple>
											<option selected value="'BAM'">Bosna i Hercegovina</option>
											<option selected value="'RSD'">Srbija</option>
											<option selected value="'EUR'">Ostale</option>
										</select>
									</div>
								</div>
								<div class="row">
									<div class="col-lg-3 idk_margin_top20">
										<button style="width:100%" class="btn btn-success" id="filter_button_submit">Traži</button>
									</div>
									<div class="col-lg-3 idk_margin_top20">
										<button style="width:100%" class="btn btn-success" id="filter_button_export">Export</button>
									</div>
									<div class="col-lg-3 idk_margin_top20">
										<button style="width:100%" class="btn btn-success" id="download_button_ch_racuni">Download</button>
									</div>
									<?php if($logged_employee_id == 67 OR $logged_employee_id == 63 OR $logged_employee_id == 75 OR $logged_employee_id == 20 OR $logged_employee_id == 412 OR $logged_employee_id == 202 OR $logged_employee_id == 373) { ?>
									<div class="col-lg-3 idk_margin_top20">
										<button style="width:100%" class="btn btn-success" id="filter_button_export_muster">Export CH računovodstvo</button>
									</div>
									<?php } ?>
								</div>
								<div class="row idk_margin_top20">
									<div class="col-xs-12" id="to_append_to_ch_racuni" width = "100" style = "min-height: 500px;display:none;">
									</div>
									<div class="col-xs-12" id="to_append_to_ch_racuni_export" style="display:none;"></div>
									<div class="col-xs-12" id="to_append_to_ch_muster_export" style="display:none;"></div>
								</div>
							</div>
						</div>
					</div>
					<script>
						function getTableRacuni(){
							
							var filter_period 			= $('#filter_period').val();
							var filter_status 			= $('#filter_status').val();
							var filter_select_drzavu 	= $('#filter_select_drzavu').val();
							
							$('#to_append_to_ch_racuni').fadeOut(600, function(){
								$.ajax({
									url: 'ajax_data.php?page=get_financije_ch_racuni',
									type: 'POST',
									dataType: 'html',
									data: {
											'filter_period' 		: filter_period,
											'filter_status' 		: filter_status,
											'filter_select_drzavu' 	: filter_select_drzavu
									},
									success: function(data) {
										// console.log(data);
										$("#to_append_to_ch_racuni").fadeOut(600, function(){
											$("#to_append_to_ch_racuni").empty().append(data).fadeIn(800);
											var table = $('#table_racuni_ch').DataTable({
												responsive: true,
												 "bAutoWidth": false
											});
										});
									},
									error: function (xhr, ajaxOptions, thrownError) {
										alert(xhr.status);
										alert(thrownError);
									}
								});
								$('#to_append_to_ch_racuni').empty();
								$('#to_append_to_ch_racuni').append('<div class="lds-hourglass"></div>').fadeIn(600);
							});
						}
						$("#filter_period").flatpickr({
							mode: "range",
							dateFormat: "d.m.Y",
							disableMobile: "true"
						});
						$(document).ready(function() {
						   getTableRacuni();
						});
						$("#filter_button_submit").on('click', function(){
							getTableRacuni();
						});
						$("#filter_button_export").on('click', function(){
							var filter_period 			= $('#filter_period').val();
							var filter_status 			= $('#filter_status').val();
							var filter_select_drzavu 	= $('#filter_select_drzavu').val();
							
							$.ajax({
								url: 'export_excel.php?prozor=export_racuni_ch',
								type: 'POST',
								dataType: 'html',
								data: {
										'filter_period' 		: filter_period,
										'filter_status' 		: filter_status,
										'filter_select_drzavu' 	: filter_select_drzavu
								},
								success: function(data) {
									$('#to_append_to_ch_racuni_export').html(data);
									$("#table_export_racuni_ch").table2excel({
										exclude: ".noExl",
										name: "Racuni_CH" + filter_period,
										filename: "Racuni_CH" + filter_period,
										fileext: ".xls"
									}); 	  
								},
								error: function (xhr, ajaxOptions, thrownError) {
									alert(xhr.status);
									alert(thrownError);
								}
							});
							$('#to_append_to_ch_racuni_export').empty();
						});
						$("#filter_button_export_muster").on('click', function(){
							var filter_period 			= $('#filter_period').val();
							var filter_status 			= $('#filter_status').val();
							var filter_select_drzavu 	= $('#filter_select_drzavu').val();
							
							$.ajax({
								url: 'export_excel.php?prozor=export_ch_muster',
								type: 'POST',
								dataType: 'html',
								data: {
										'filter_period' 		: filter_period,
										'filter_status' 		: filter_status,
										'filter_select_drzavu' 	: filter_select_drzavu
								},
								success: function(data) {
									$('#to_append_to_ch_muster_export').append(data);
									$("#table_export_muster_ch").table2excel({
										exclude: ".noExl",
										name: "Jobstep Int GmbH - Vorlage Buchungsbeleg",
										filename: "Vorlage_Buchungsbeleg",
										fileext: ".xls"
									}); 	  
								},
								error: function (xhr, ajaxOptions, thrownError) {
									alert(xhr.status);
									alert(thrownError);
								}
							});
						});
						$('#download_button_ch_racuni').on('click',function(){
							
							var filter_period 			= $('#filter_period').val();
							var filter_status 			= $('#filter_status').val();
							var filter_select_drzavu 	= $('#filter_select_drzavu').val();
							console.log(filter_period);
							$.ajax({
								url: 'ajax_data.php?page=download_racuni_ch',
								type: 'POST',
								dataType: 'html',
								data: {
										'filter_period' 		: filter_period,
										'filter_status' 		: filter_status,
										'filter_select_drzavu' 	: filter_select_drzavu
									},
								success: function(data) {
									window.location.href = data;
									// alert(data);
								},
								error: function (xhr, ajaxOptions, thrownError) {
									alert(xhr.status);
									alert(thrownError);
								}
							});
						});
					</script>
					<?php
				break;
				case "list":
					?>
					<style>
						.akcija{
							display:flex !important;
						}
						.card{
							margin-top: 40px;
							width: 327px;
							height: 208px;
							display: inline-block;
							margin: 10px;
							/* Gradient */

							background: linear-gradient(112.03deg, #68c368 0%, #002e0c 100%);
							border-radius: 16px;
						  }

						.amount{
						  /* 338.34 */


						position: absolute;
						width: 100px;
						height: 40px; 
						padding: 144px 180px 24px 47px;
						font-style: normal;
						font-weight: normal;
						font-size: 32px;
						line-height: 40px;
						letter-spacing: 0.01em;

						color: #FFFFFF;
						}
						.naslov{
						  
						position: absolute;
						width: 200px;
						height: 40px;
						padding: 10px 10px 10px 10px;
						font-style: normal;
						font-weight: normal;
						font-size: 32px;
						line-height: 40px;
						letter-spacing: 0.01em;

						color: #FFFFFF;
						}
						.kurs{
						  /* ¥ */

						position: absolute;
						width: 11px;
						height: 23px;
						padding: 151px 284px 34px 8px;
						font-style: normal;
						font-weight: normal;
						font-size: 14px;
						line-height: 23px;
						margin: -3px 0;
						/* identical to box height */

						letter-spacing: 0.01em;

						color: #FFFFFF;
						}

						.balance{
						  /* Balance */


						position: absolute;
						width: 51px;
						height: 18px;
						padding: 126px 244px 64px 32px;
						font-style: normal;
						font-weight: normal;
						font-size: 14px;
						line-height: 18px;
						/* identical to box height */

						letter-spacing: 0.01em;

						color: #ffffff;

						}

						.card-name{
						position: absolute;
						padding: 32px 234px 160px 32px;
						width: 61px;
						height: 16px;
						}

						.siluet-1{
						  /* Ellipse 4 */
						position: absolute;
						width: 425px;
						height: 171px;
						border-radius: 600px / 200px;
						margin: 98px -20px -61px -78px;
						background: rgba(255, 255, 255, 0.04);
						}

						.siluet-2{
						  position: absolute;
						width: 335px;
						height: 259px;
						border-radius: 50%;
						margin: 64px 89px -115px -97px;
						background: rgba(255, 255, 255, 0.04);
						}
						.rectangle{
						  position: absolute;
						width: 327px;
						height: 208px;

						
						border-radius: 16px;
						}
						.info{
							background: linear-gradient(112.03deg, #3ec3d5 0%, #002e0c 100%);
						}
						.income{
							background: linear-gradient(112.03deg, #68c368 0%, #002e0c 100%);
						}
						.expense{
							background: linear-gradient(112.03deg, #ff5460 0%, #002e0c 100%);
						}
					</style>
					<h1> <i class="fa fa-money idk_color_green" aria-hidden="true"></i> Financije kandidati:</h1>
						<hr>
						<div class="row">
							<div class="col-md-12">
								<div class="content_box">	
									<div class="col-12 row">
										<div class="row">
											<div class="col-lg-4 col-md-4">
												<label for="filter_select_kompanija_faktura">Kompanija:</label>
												<select id="filter_select_kompanija_faktura" class="selectpicker" name="filter_select_kompanija_faktura" data-live-search="true"  data-actions-box="true" multiple>
													<?php 
														$select_query = $db->prepare("
															SELECT company_id, company_name
															FROM idk_companies
															JOIN idk_nalozi ON idk_companies.company_id = idk_nalozi.kompanija_id
															WHERE company_status = 1 AND idk_nalozi.nalog_financije != 0
															GROUP BY company_id ORDER BY company_name");

														$select_query->execute();

														$companies = $select_query->fetchAll();

														foreach($companies as $company){
															echo '<option selected value="'.$company["company_id"].'">'.$company["company_name"].'</option>';
														}
													?>
												</select>
											</div>
											<script>
												$('#filter_select_kompanija_faktura').on('change',function(){
													var selectedValues = $('#filter_select_kompanija_faktura').val();

													$.ajax({
														url: 'ajax_data.php?page=get_company_orders',
														type: 'POST',
														dataType: 'html',
														data: {
																'companies': selectedValues
															},
														success: function(data) {
															$("#filter_select_nalog_faktura").html(data).selectpicker("refresh");  
														},
														error: function (xhr, ajaxOptions, thrownError) {
															alert(xhr.status);
															alert(thrownError);
														}
													});
												});
											</script>
											<div class="col-lg-4 col-md-4">
												<label for="filter_select_nalog_faktura">Nalog:</label>
												<select id="filter_select_nalog_faktura" class="selectpicker" name="filter_select_nalog_faktura" data-live-search="true" data-actions-box="true"  multiple>
													<?php 
														$select_query = $db->prepare("
															SELECT nalog_id, nalog_naziv, company_id, company_name, nalog_broj
															FROM idk_companies
															JOIN idk_nalozi ON idk_companies.company_id = idk_nalozi.kompanija_id
															WHERE company_status = 1 AND idk_nalozi.nalog_financije != 0
															ORDER BY nalog_broj DESC");

														$select_query->execute();

														$companies = $select_query->fetchAll();

														foreach($companies as $company){
															echo '<option selected value="'.$company["nalog_id"].'">'.$company["nalog_broj"]." - ".$company["nalog_naziv"].' ('.$company["company_name"].')</option>';
														}
													?>
												</select>
											</div>

											<div class="col-lg-4 col-md-4">
												<label for="filter_select_status_rate_faktura">Status rate:</label>
												<select id="filter_select_status_rate_faktura" class="selectpicker" name="filter_select_status_rate_faktura" multiple>
													<!-- <option selected value="0">NDF</option> -->
													<option selected value="1">Treba fakturisati</option>
													<option selected value="2">Fakturisano</option>
													<option selected value="3">Plaćeno</option>
												</select>
											</div>
										</div>
										<div class="row idk_margin_top20" style="display:flex; align-items: flex-end;">
											<div class="col-lg-2 col-md-2">
												<div class="form-group materail-input-block materail-input-block_primary materail-input_slide-line" style="margin: 0;">
													<label for="filter_kreirana_faktura_period">Datum za fakturisanje:</label>
													<input type="text" class="form-control" name="filter_kreirana_faktura_period" id="filter_kreirana_faktura_period" placeholder="Datum" style="padding:17px;border-radius:0;">
													<span class="materail-input-block__line"></span>
												</div>
											</div>
											<div class="col-lg-2 col-md-2">
												<div class="form-group materail-input-block materail-input-block_primary materail-input_slide-line" style="margin: 0;">
													<label for="filter_fakturisana_faktura_period">Datum fakturisanja:</label>
													<input type="text" class="form-control" name="filter_fakturisana_faktura_period" id="filter_fakturisana_faktura_period" placeholder="Datum" style="padding:17px;border-radius:0;">
													<span class="materail-input-block__line"></span>
												</div>
											</div>
											<div class="col-lg-2 col-md-2">
												<div class="form-group materail-input-block materail-input-block_primary materail-input_slide-line" style="margin: 0;">
													<label for="filter_uplacena_faktura_period">Datum uplate fakture:</label>
													<input type="text" class="form-control" name="filter_uplacena_faktura_period" id="filter_uplacena_faktura_period" placeholder="Datum" style="padding:17px;border-radius:0;">
													<span class="materail-input-block__line"></span>
												</div>
											</div>
											<div class="col-lg-2 col-md-2">
												<label for="filter_select_firma_faktura">Firma fakturisanja:</label>
												<select id="filter_select_firma_faktura" class="selectpicker" name="filter_select_firma_faktura" multiple>
													<option selected value="1">DE - Jobstep Gmbh</option>
													<option selected value="2">CH - Jobstep Int Gmbh</option>
												</select>
											</div>
											<div class="col-lg-2 col-md-3 d-flex align-items-center">
												<button id="filter_button_trazi_faktura" style="width:100%" class="btn btn-success">Traži</button>
											</div>
											<div class="col-lg-2 col-md-3 d-flex align-items-center">
												<button id="export_button_faktura" style="width:100%" class="btn btn-success">Export</button>
											</div>
										</div>
										<div id = "to_append_to_faktura" width = "100" style = "margin-top: 100px; min-height: 500px;" ></div>
										<div id = "to_append_to_export" style="display:none;"></div>
									</div>
								</div>
							</div>
						</div>
					<script>
						function getTablePredracuni(){
							var filter_kreirana_faktura_period 		= $('#filter_kreirana_faktura_period').val();
							var filter_fakturisana_faktura_period 	= $('#filter_fakturisana_faktura_period').val();
							var filter_uplacena_faktura_period 		= $('#filter_uplacena_faktura_period').val();
							var filter_select_kompanija_faktura 	= $('#filter_select_kompanija_faktura').val();
							var filter_select_nalog_faktura 		= $('#filter_select_nalog_faktura').val();
							var filter_select_status_rate_faktura	= $('#filter_select_status_rate_faktura').val();
							var filter_select_firma_faktura			= $('#filter_select_firma_faktura').val();
							
							$('#to_append_to_faktura').fadeOut(600, function(){
								$.ajax({
									url: 'ajax_data.php?page=get_financije_fakture',
									type: 'POST',
									dataType: 'html',
									data: {
											'filter_kreirana_faktura_period' 	: filter_kreirana_faktura_period,
											'filter_fakturisana_faktura_period' : filter_fakturisana_faktura_period,
											'filter_uplacena_faktura_period' 	: filter_uplacena_faktura_period,
											'filter_select_kompanija_faktura' 	: filter_select_kompanija_faktura,
											'filter_select_nalog_faktura' 		: filter_select_nalog_faktura,
											'filter_select_status_rate_faktura' : filter_select_status_rate_faktura,
											'filter_select_firma_faktura' 		: filter_select_firma_faktura
										},
									success: function(data) {
										$("#to_append_to_faktura").fadeOut(600, function(){
											$("#to_append_to_faktura").empty().append(data).fadeIn(800);
											var table = $('#table_fakture').DataTable({
												responsive: true,
												"bAutoWidth": false,
												lengthMenu: [
													[10, 25, 50, -1],
													[10, 25, 50, 'All'],
												],
											});
										});
									},
									error: function (xhr, ajaxOptions, thrownError) {
										alert(xhr.status);
										alert(thrownError);
									}
								});
								$('#to_append_to_faktura').empty();
								$('#to_append_to_faktura').append('<div class="lds-hourglass"></div>').fadeIn(600);
							});
						}
						$(document).ready(function() {
						   getTablePredracuni();
						});
						$("#filter_kreirana_faktura_period").flatpickr({
							mode: "range",
							dateFormat: "d.m.Y",
							disableMobile: "true"
						});
						$("#filter_fakturisana_faktura_period").flatpickr({
							mode: "range",
							dateFormat: "d.m.Y",
							disableMobile: "true"
						});
						$("#filter_uplacena_faktura_period").flatpickr({
							mode: "range",
							dateFormat: "d.m.Y",
							disableMobile: "true"
						});
						$('#filter_button_trazi_faktura').on('click',function(){
							getTablePredracuni();
						});

						function table_export () {

							var uri  = 'data:application/vnd.ms-excel;base64,';
							template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--><meta http-equiv="content-type" content="text/plain; charset=UTF-8"/></head><body><table>{table}</table></body></html>';

							base64 = function(s) { 
								return window.btoa(unescape(encodeURIComponent(s)));
							}

							format = function(s, c) { 
								return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) 
							}

							table_head_rows = document.querySelectorAll("#table_fakture tr")[0];

							table_body_rows = document.querySelectorAll("#table_fakture tbody tr");

							let table = document.querySelector("#table_fakture");

							// var links = table.querySelectorAll("a");
							// links.forEach(function (link) {
							// 	var text = link.innerText;
							// 	link.parentNode.replaceChild(document.createTextNode(text), link);
							// });

							var akcijaElements = document.querySelectorAll(".akcija");
								akcijaElements.forEach(function(element) {
								while (element.firstChild) {
									element.removeChild(element.firstChild);
								}
							});

							var ctx = {worksheet: new Date().toString().replace(" ", "") || 'Worksheet', table: table.innerHTML}
							window.location.href = uri + base64(format(template, ctx));

						}
						
						$('#export_button_faktura').on('click',function(){
							table_export();
						});
					</script>
					<?php
					
				break;
				case "list_nalozi":
					?>
					<style>
						.akcija{
							display:flex !important;
						}
						.card{
							margin-top: 40px;
							width: 327px;
							height: 208px;
							display: inline-block;
							margin: 10px;
							/* Gradient */

							background: linear-gradient(112.03deg, #68c368 0%, #002e0c 100%);
							border-radius: 16px;
						  }

						.amount{
						  /* 338.34 */


						position: absolute;
						width: 100px;
						height: 40px; 
						padding: 144px 180px 24px 47px;
						font-style: normal;
						font-weight: normal;
						font-size: 32px;
						line-height: 40px;
						letter-spacing: 0.01em;

						color: #FFFFFF;
						}
						.naslov{
						  
						position: absolute;
						width: 200px;
						height: 40px;
						padding: 10px 10px 10px 10px;
						font-style: normal;
						font-weight: normal;
						font-size: 32px;
						line-height: 40px;
						letter-spacing: 0.01em;

						color: #FFFFFF;
						}
						.kurs{
						  /* ¥ */

						position: absolute;
						width: 11px;
						height: 23px;
						padding: 151px 284px 34px 8px;
						font-style: normal;
						font-weight: normal;
						font-size: 14px;
						line-height: 23px;
						margin: -3px 0;
						/* identical to box height */

						letter-spacing: 0.01em;

						color: #FFFFFF;
						}

						.balance{
						  /* Balance */


						position: absolute;
						width: 51px;
						height: 18px;
						padding: 126px 244px 64px 32px;
						font-style: normal;
						font-weight: normal;
						font-size: 14px;
						line-height: 18px;
						/* identical to box height */

						letter-spacing: 0.01em;

						color: #ffffff;

						}

						.card-name{
						position: absolute;
						padding: 32px 234px 160px 32px;
						width: 61px;
						height: 16px;
						}

						.siluet-1{
						  /* Ellipse 4 */
						position: absolute;
						width: 425px;
						height: 171px;
						border-radius: 600px / 200px;
						margin: 98px -20px -61px -78px;
						background: rgba(255, 255, 255, 0.04);
						}

						.siluet-2{
						  position: absolute;
						width: 335px;
						height: 259px;
						border-radius: 50%;
						margin: 64px 89px -115px -97px;
						background: rgba(255, 255, 255, 0.04);
						}
						.rectangle{
						  position: absolute;
						width: 327px;
						height: 208px;

						
						border-radius: 16px;
						}
						.info{
							background: linear-gradient(112.03deg, #3ec3d5 0%, #002e0c 100%);
						}
						.income{
							background: linear-gradient(112.03deg, #68c368 0%, #002e0c 100%);
						}
						.expense{
							background: linear-gradient(112.03deg, #ff5460 0%, #002e0c 100%);
						}
					</style>
					<h1> <i class="fa fa-money idk_color_green" aria-hidden="true"></i> Financije nalozi:</h1>
						<hr>
						<div class="row">
							<div class="col-md-12">
								<div class="content_box">	
									<div class="col-12 row">
										<div class="row">
											<div class="col-lg-4 col-md-4">
												<label for="filter_select_kompanija_faktura">Kompanija:</label>
												<select id="filter_select_kompanija_faktura" class="selectpicker" name="filter_select_kompanija_faktura" data-live-search="true"  data-actions-box="true" multiple>
													<?php 
														$select_query = $db->prepare("
															SELECT company_id, company_name
															FROM idk_companies
															JOIN idk_nalozi ON idk_companies.company_id = idk_nalozi.kompanija_id
															WHERE company_status = 1 AND idk_nalozi.nalog_financije != 0
															GROUP BY company_id ORDER by company_name");

														$select_query->execute();

														$companies = $select_query->fetchAll();

														foreach($companies as $company){
															echo '<option selected value="'.$company["company_id"].'">'.$company["company_name"].'</option>';
														}
													?>
												</select>
											</div>
											<script>
												$('#filter_select_kompanija_faktura').on('change',function(){
													var selectedValues = $('#filter_select_kompanija_faktura').val();

													$.ajax({
														url: 'ajax_data.php?page=get_company_orders',
														type: 'POST',
														dataType: 'html',
														data: {
																'companies': selectedValues
															},
														success: function(data) {
															$("#filter_select_nalog_faktura").html(data).selectpicker("refresh");  
														},
														error: function (xhr, ajaxOptions, thrownError) {
															alert(xhr.status);
															alert(thrownError);
														}
													});
												});
											</script>
											<div class="col-lg-4 col-md-4">
												<label for="filter_select_nalog_faktura">Nalog:</label>
												<select id="filter_select_nalog_faktura" class="selectpicker" name="filter_select_nalog_faktura" data-live-search="true" data-actions-box="true"  multiple>
													<?php 
														$select_query = $db->prepare("
															SELECT nalog_id, nalog_naziv, company_id, company_name, nalog_broj
															FROM idk_companies
															JOIN idk_nalozi ON idk_companies.company_id = idk_nalozi.kompanija_id
															WHERE company_status = 1 AND idk_nalozi.nalog_financije != 0
															ORDER BY nalog_broj DESC");

														$select_query->execute();

														$companies = $select_query->fetchAll();

														foreach($companies as $company){
															echo '<option selected value="'.$company["nalog_id"].'">'.$company["nalog_broj"]." - ".$company["nalog_naziv"].' ('.$company["company_name"].')</option>';
														}
													?>
												</select>
											</div>

											<div class="col-lg-4 col-md-4">
												<label for="filter_select_status_rate_faktura">Status rate:</label>
												<select id="filter_select_status_rate_faktura" class="selectpicker" name="filter_select_status_rate_faktura" multiple>
													<!-- <option selected value="0">NDF</option> -->
													<option selected value="1">Treba fakturisati</option>
													<option selected value="2">Fakturisano</option>
													<option selected value="3">Plaćeno</option>
												</select>
											</div>
										</div>
										<div class="row idk_margin_top20" style="display:flex; align-items: flex-end;">
											<div class="col-lg-2 col-md-2">
												<div class="form-group materail-input-block materail-input-block_primary materail-input_slide-line" style="margin: 0;">
													<label for="filter_kreirana_faktura_period">Datum za fakturisanje:</label>
													<input type="text" class="form-control" name="filter_kreirana_faktura_period" id="filter_kreirana_faktura_period" placeholder="Datum" style="padding:17px;border-radius:0;">
													<span class="materail-input-block__line"></span>
												</div>
											</div>
											<div class="col-lg-2 col-md-2">
												<div class="form-group materail-input-block materail-input-block_primary materail-input_slide-line" style="margin: 0;">
													<label for="filter_fakturisana_faktura_period">Datum fakturisanja:</label>
													<input type="text" class="form-control" name="filter_fakturisana_faktura_period" id="filter_fakturisana_faktura_period" placeholder="Datum" style="padding:17px;border-radius:0;">
													<span class="materail-input-block__line"></span>
												</div>
											</div>
											<div class="col-lg-2 col-md-2">
												<div class="form-group materail-input-block materail-input-block_primary materail-input_slide-line" style="margin: 0;">
													<label for="filter_uplacena_faktura_period">Datum uplate fakture:</label>
													<input type="text" class="form-control" name="filter_uplacena_faktura_period" id="filter_uplacena_faktura_period" placeholder="Datum" style="padding:17px;border-radius:0;">
													<span class="materail-input-block__line"></span>
												</div>
											</div>
											<div class="col-lg-2 col-md-2">
												<label for="filter_select_firma_faktura">Firma fakturisanja:</label>
												<select id="filter_select_firma_faktura" class="selectpicker" name="filter_select_firma_faktura" multiple>
													<option selected value="1">DE - Jobstep Gmbh</option>
													<option selected value="2">CH - Jobstep Int Gmbh</option>
												</select>
											</div>
											<div class="col-lg-2 col-md-3 d-flex align-items-center">
												<button id="filter_button_trazi_faktura" style="width:100%" class="btn btn-success">Traži</button>
											</div>
											<div class="col-lg-2 col-md-3 d-flex align-items-center">
												<button id="export_button_faktura" style="width:100%" class="btn btn-success">Export</button>
											</div>
										</div>
										<div id = "to_append_to_faktura" width = "100" style = "margin-top: 100px; min-height: 500px;" ></div>
										<div id = "to_append_to_export" style="display:none;"></div>
									</div>
								</div>
							</div>
						</div>
					<script>
						function getTablePredracuni(){
							var filter_kreirana_faktura_period 		= $('#filter_kreirana_faktura_period').val();
							var filter_fakturisana_faktura_period 	= $('#filter_fakturisana_faktura_period').val();
							var filter_uplacena_faktura_period 		= $('#filter_uplacena_faktura_period').val();
							var filter_select_kompanija_faktura 	= $('#filter_select_kompanija_faktura').val();
							var filter_select_nalog_faktura 		= $('#filter_select_nalog_faktura').val();
							var filter_select_status_rate_faktura	= $('#filter_select_status_rate_faktura').val();
							var filter_select_firma_faktura			= $('#filter_select_firma_faktura').val();
							
							$('#to_append_to_faktura').fadeOut(600, function(){
								$.ajax({
									url: 'ajax_data.php?page=get_financije_fakture_naloga',
									type: 'POST',
									dataType: 'html',
									data: {
											'filter_kreirana_faktura_period' 	: filter_kreirana_faktura_period,
											'filter_fakturisana_faktura_period' : filter_fakturisana_faktura_period,
											'filter_uplacena_faktura_period' 	: filter_uplacena_faktura_period,
											'filter_select_kompanija_faktura' 	: filter_select_kompanija_faktura,
											'filter_select_nalog_faktura' 		: filter_select_nalog_faktura,
											'filter_select_status_rate_faktura' : filter_select_status_rate_faktura,
											'filter_select_firma_faktura' 		: filter_select_firma_faktura
										},
									success: function(data) {
										$("#to_append_to_faktura").fadeOut(600, function(){
											$("#to_append_to_faktura").empty().append(data).fadeIn(800);
											var table = $('#table_fakture').DataTable({
												responsive: true,
												"bAutoWidth": false,
												lengthMenu: [
													[10, 25, 50, -1],
													[10, 25, 50, 'All'],
												],
											});
										});
									},
									error: function (xhr, ajaxOptions, thrownError) {
										alert(xhr.status);
										alert(thrownError);
									}
								});
								$('#to_append_to_faktura').empty();
								$('#to_append_to_faktura').append('<div class="lds-hourglass"></div>').fadeIn(600);
							});
						}
						$(document).ready(function() {
						   getTablePredracuni();
						});
						$("#filter_kreirana_faktura_period").flatpickr({
							mode: "range",
							dateFormat: "d.m.Y",
							disableMobile: "true"
						});
						$("#filter_fakturisana_faktura_period").flatpickr({
							mode: "range",
							dateFormat: "d.m.Y",
							disableMobile: "true"
						});
						$("#filter_uplacena_faktura_period").flatpickr({
							mode: "range",
							dateFormat: "d.m.Y",
							disableMobile: "true"
						});
						$('#filter_button_trazi_faktura').on('click',function(){
							getTablePredracuni();
						});

						function table_export () {

							var uri  = 'data:application/vnd.ms-excel;base64,';
							template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--><meta http-equiv="content-type" content="text/plain; charset=UTF-8"/></head><body><table>{table}</table></body></html>';

							base64 = function(s) { 
								return window.btoa(unescape(encodeURIComponent(s)));
							}

							format = function(s, c) { 
								return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) 
							}

							table_head_rows = document.querySelectorAll("#table_fakture tr")[0];

							table_body_rows = document.querySelectorAll("#table_fakture tbody tr");

							let table = document.querySelector("#table_fakture");

							// var links = table.querySelectorAll("a");
							// links.forEach(function (link) {
							// 	var text = link.innerText;
							// 	link.parentNode.replaceChild(document.createTextNode(text), link);
							// });

							var akcijaElements = document.querySelectorAll(".akcija");
								akcijaElements.forEach(function(element) {
								while (element.firstChild) {
									element.removeChild(element.firstChild);
								}
							});

							var ctx = {worksheet: new Date().toString().replace(" ", "") || 'Worksheet', table: table.innerHTML}
							window.location.href = uri + base64(format(template, ctx));

						}
						
						$('#export_button_faktura').on('click',function(){
							table_export();
						});
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