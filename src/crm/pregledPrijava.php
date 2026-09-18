<?php
	include("includes/functions.php");
	include("includes/common.php");

	$getEmployeeStatus = explode( ',' , getEmployeeStatus());
	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: pregledPrijava?page=main_list");
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Pregled prijava</title>

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
			left: 50%;
			margin-left: -10px;			
			background: radial-gradient(#5cb85c, white);
			border-radius: 100px;
		}
		.lds-hourglass_min:after {
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
	</style>

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

				case "main_list":
					?>
					<div class="row">
						<div class="col-xs-12">
							<h1><i class="fa fa-file-o idk_color_green" aria-hidden="true"></i> Pregled prijava</h1>
						</div>
						
						<div class="col-xs-12">
							<hr />
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="content_box">
								<div class="col-12">
									<div class="row" style="display:flex; align-items: flex-end;">
										<div class="col-lg-2 col-md-2">
											<div class="form-group materail-input-block materail-input-block_primary materail-input_slide-line" style="margin: 0;">
												<label for="filter_period_prijava">Odaberi period:</label>
												<input type="text" class="form-control" name="filter_period_prijava" id="filter_period_prijava" placeholder="Datum" style="padding:17px;border-radius:0;">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
										<div class="col-lg-2 col-md-3 d-flex align-items-center">
											<button id="filter_button_trazi_prijave" style="width:100%" class="btn btn-success">Traži</button>
										</div>
									</div>
									<?php
										
										
									?>
									<script type="text/javascript">
										// $(document).ready(function() {
										// 	var table = $('#idk_table').DataTable({
										// 		responsive: true,
										// 		bSortable: true,
										// 		"order": [[ 0, "desc" ]],
										// 			"bAutoWidth": false,
										// 		"aoColumns": [
										// 				{ "width": "16%" },
										// 				{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },
										// 				{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },
										// 				{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" },{ "width": "4%" }
										// 			]
										// 	});

										// 	$('#table-filter').on('change', function(){
										// 		table.search(this.value).draw();   
										// 	});
											
										// });

									</script>
								</div>
								<div id = "to_append_pregled_prijava" width = "100" style = "margin-top: 100px; min-height: 500px;" ></div>
							</div>
						</div>
					</div>
					<script>
						function getTablePregledPrijava(){
							var filter_period_prijava 		= $('#filter_period_prijava').val();
							
							$('#to_append_pregled_prijava').fadeOut(600, function(){
								$.ajax({
									url: 'ajax_data.php?page=get_pregled_prijava',
									type: 'POST',
									dataType: 'html',
									data: {
										'filter_period_prijava' 	: filter_period_prijava
									},
									success: function(data) {
										$("#to_append_pregled_prijava").fadeOut(600, function(){
											$("#to_append_pregled_prijava").empty().append(data).fadeIn(800);
											var table = $('#table_prijave').DataTable({
												// responsive: true,
												"bAutoWidth": false,
												order: [[0, 'desc']],
												scrollX: true,
												lengthMenu: [
													[10, 25, 50, -1],
													[10, 25, 50, 'All'],
												],
											});
											setTimeout(function () {
												table.columns.adjust().draw();
											}, 200);
										});
									},
									error: function (xhr, ajaxOptions, thrownError) {
										alert(xhr.status);
										alert(thrownError);
									}
								});
								$('#to_append_pregled_prijava').empty();
								$('#to_append_pregled_prijava').append('<div class="lds-hourglass"></div>').fadeIn(600);
							});
						}
						$(document).ready(function() {
							getTablePregledPrijava();
						});
						$("#filter_period_prijava").flatpickr({
							mode: "range",
							dateFormat: "d.m.Y",
							disableMobile: "true"
						});
						$('#filter_button_trazi_prijave').on('click',function(){
							getTablePregledPrijava();
						});

					</script>
					<?php
				break;

				case "candidates_list":
					
					$nalog_id 	= $_GET["nalog_id"];
					$dan 		= $_GET["dan"];
					$date_from 	= date("Y-m-d 00:00:00", strtotime($dan));
					$date_to 	= date("Y-m-d 23:59:59", strtotime($dan));
					// var_dump($nalog_id);
					// var_dump($dan);
					?>
					<div class="row">
						<div class="col-xs-12">
							<h1><i class="fa fa-file-o idk_color_green" aria-hidden="true"></i> Lista prijavljenih kandidata</h1>
						</div>
						
						<div class="col-xs-12">
							<hr />
						</div>
						<div class="col-xs-12">
							<div class="content_box idk_margin_top20">
								<div style="text-align:center">
									<h1>
										<?php echo "<br />"."<b>".getNalogNameById($nalog_id)."<br />".$dan."</b>";?>
									</h1>
								</div>
								<script type="text/javascript">
									$(document).ready(function() {
										$('#list_candidate_by_day').DataTable({

											responsive: true,
											"bAutoWidth": false,
											order: [[2, 'desc']],
											"aoColumns": [
													{ "width": "10%" },
													{ "width": "20%" },
													{ "width": "20%" },
													{ "width": "15%" },
													{ "width": "5%" },
													{ "width": "30%" }
												]
										});
									} );
								</script>
								<table id="list_candidate_by_day" class="stripe" cellspacing="0" width="100%">
									<thead>
										<th>ID</th>
										<th>Kandidat</th>
										<th class="text-center"	>Vrijeme prijave</th>
										<th class="text-center">Tip Prijave</th>
										<th class="text-center">Link ID</th>
										<th>Link</th>
									</thead>
									<tbody>
										<?php
										$sql = "SELECT
													kandidat_id, kandidat_ime, kandidat_prezime, lks_datetime, lks_status_obrade, lg.lg_url, lg.lg_id
												FROM
													idk_log_kandidat_statusi
												JOIN(
													SELECT
														MAX(lks_id) AS max_log_id
													FROM
														idk_log_kandidat_statusi
													WHERE
														lks_kandidat_id != 0 AND lks_status_obrade IN(0, 9)
													GROUP BY
														lks_kandidat_id
												) max_log
												ON
													lks_id = max_log.max_log_id
												JOIN idk_link_generator lg ON
													lks_link_id = lg.lg_id AND lks_link_id IS NOT NULL AND lks_link_id != 1
												
												JOIN idk_kandidati ON 
													kandidat_id = lks_kandidat_id
												WHERE lg.lg_nalogid = ".$nalog_id."  AND lks_datetime BETWEEN '".$date_from."' AND '".$date_to."'
												ORDER BY `idk_log_kandidat_statusi`.`lks_datetime` DESC";

												// var_dump($sql);
										$list_candidates = $db->prepare($sql);
										$list_candidates->execute();
										// var_dump($list_candidates->errorInfo());

										while($row_candidate = $list_candidates->fetch()){
											$kandidat_id 		= $row_candidate['kandidat_id'];
											$kandidat_ime 		= $row_candidate['kandidat_ime'];
											$kandidat_prezime 	= $row_candidate['kandidat_prezime'];
											$lks_datetime 		= $row_candidate['lks_datetime'];
											$lks_status_obrade 	= $row_candidate['lks_status_obrade'];
											$lg_url 			= $row_candidate['lg_url'];
											$lg_id 				= $row_candidate['lg_id'];

											if($lks_status_obrade == 0){
												$tip_prijave = '<span class="label label-warning material-label material-label_warning main-container__column text-left">Prva prijava</span>';
											}elseif($lks_status_obrade == 9){
												$tip_prijave = '<span class="label label-light material-label material-label_light main-container__column text-left">Ponovna prijava</span>';
											}else{
												$tip_prijave = "GREŠKA";
											}

											echo '
												<tr>
													<td><a href=/kandidati?page=open&id='.$kandidat_id.'>'.$kandidat_id.'</a></td>
													<td><a href=/kandidati?page=open&id='.$kandidat_id.'>'.$kandidat_ime.' '.$kandidat_prezime.'</a></td>
													<td class="text-center">'.date('H:i:s', strtotime($lks_datetime)).'</td>
													<td class="text-center">'.$tip_prijave.'</td>
													<td class="text-center">'.$lg_id.'</td>
													<td>'.$lg_url.'</td>
												</tr>
											';
										}
										?>
									</tbody>
								</table>
							</div>
						</div>
					</div>

					<?php
				break;

				case "candidates_list_total":
					
					$nalog_ids = $_POST["nalog_ids"];
					$nalog_ids_string = implode(",", $nalog_ids);
					$dan 		= $_POST["dan"];
					$date_from 	= date("Y-m-d 00:00:00", strtotime($dan));
					$date_to 	= date("Y-m-d 23:59:59", strtotime($dan));
					?>
					<div class="row">
						<div class="col-xs-12">
							<h1><i class="fa fa-file-o idk_color_green" aria-hidden="true"></i> Lista prijavljenih kandidata</h1>
						</div>
						
						<div class="col-xs-12">
							<hr />
						</div>
						<div class="col-xs-12">
							<div class="content_box idk_margin_top20">
								<div style="text-align:center">
									<h1>
										<?php echo "<br />"."<b>".getNalogNameById($nalog_id)."<br />".$dan."</b>";?>
										<button id="export_button" title="Prvo odaberite da izlista 'Sve' u tabeli." class="btn btn-success">Export</button>

									</h1>
								</div>
								<script type="text/javascript">
									$(document).ready(function() {
										$('#list_candidate_by_day').DataTable({
											responsive: true,
											"bAutoWidth": false,
											order: [[3, 'desc']],
											"aoColumns": [
												{ "width": "10%" },
												{ "width": "20%" },
												{ "width": "20%" },
												{ "width": "20%" },
												{ "width": "30%" }
											],
											lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]]
										});
									} );
									function table_export () {

										var uri  = 'data:application/vnd.ms-excel;base64,';
										template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--><meta http-equiv="content-type" content="text/plain; charset=UTF-8"/></head><body><table>{table}</table></body></html>';

										base64 = function(s) { 
											return window.btoa(unescape(encodeURIComponent(s)));
										}

										format = function(s, c) { 
											return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) 
										}

										table_head_rows = document.querySelectorAll("#list_candidate_by_day tr")[0];

										table_body_rows = document.querySelectorAll("#list_candidate_by_day tbody tr");

										let table = document.querySelector("#list_candidate_by_day");

										var ctx = {worksheet: new Date().toString().replace(" ", "") || 'Worksheet', table: table.innerHTML}
										window.location.href = uri + base64(format(template, ctx));

									}
									
									$('#export_button').on('click',function(){
										table_export();
									});
								</script>
								<table id="list_candidate_by_day" class="stripe" cellspacing="0" width="100%">
									<thead>
										<th>ID</th>
										<th>Ime</th>
										<th>Prezime</th>
										<th class="text-center"	>Vrijeme prijave</th>
										<th class="text-center">Telefon</th>
									</thead>
									<tbody>
										<?php
										$sql = "SELECT
													kandidat_id, kandidat_ime, kandidat_prezime, lks_datetime, lks_status_obrade, lg.lg_url, lg.lg_id, kandidat_mobitel
												FROM
													idk_log_kandidat_statusi
												JOIN(
													SELECT
														MAX(lks_id) AS max_log_id
													FROM
														idk_log_kandidat_statusi
													WHERE
														lks_kandidat_id != 0 AND lks_status_obrade IN(0, 9)
													GROUP BY
														lks_kandidat_id
												) max_log
												ON
													lks_id = max_log.max_log_id
												JOIN idk_link_generator lg ON
													lks_link_id = lg.lg_id AND lks_link_id IS NOT NULL AND lks_link_id != 1
												
												JOIN idk_kandidati ON 
													kandidat_id = lks_kandidat_id
												WHERE lg.lg_nalogid IN (".$nalog_ids_string.")  AND lks_datetime BETWEEN '".$date_from."' AND '".$date_to."'
												ORDER BY `idk_log_kandidat_statusi`.`lks_datetime` DESC";

												// var_dump($sql);
											$list_candidates = $db->prepare($sql);
											$list_candidates->execute();
										// var_dump($list_candidates->errorInfo());

										while($row_candidate = $list_candidates->fetch()){
											$kandidat_id 		= $row_candidate['kandidat_id'];
											$kandidat_ime 		= $row_candidate['kandidat_ime'];
											$kandidat_prezime 	= $row_candidate['kandidat_prezime'];
											$kandidat_mobitel 	= $row_candidate['kandidat_mobitel'];
											$lks_datetime 		= $row_candidate['lks_datetime'];
											$lks_status_obrade 	= $row_candidate['lks_status_obrade'];
											$lg_url 			= $row_candidate['lg_url'];
											$lg_id 				= $row_candidate['lg_id'];

											if($lks_status_obrade == 0){
												$tip_prijave = '<span class="label label-warning material-label material-label_warning main-container__column text-left">Prva prijava</span>';
											}elseif($lks_status_obrade == 9){
												$tip_prijave = '<span class="label label-light material-label material-label_light main-container__column text-left">Ponovna prijava</span>';
											}else{
												$tip_prijave = "GREŠKA";
											}

											echo '
												<tr>
													<td><a href=/kandidati?page=open&id='.$kandidat_id.'>'.$kandidat_id.'</a></td>
													<td><a href=/kandidati?page=open&id='.$kandidat_id.'>'.$kandidat_ime.'</a></td>
													<td><a href=/kandidati?page=open&id='.$kandidat_id.'>'.$kandidat_prezime.'</a></td>
													<td class="text-center">'.date('H:i:s', strtotime($lks_datetime)).'</td>
													<td class="text-center">'.$kandidat_mobitel.'</td>
												</tr>
											';
										}
										?>
									</tbody>
								</table>
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
<?php }else{			
			echo '
				<br/>
				<div class="alert material-alert material-alert_danger">
					<h4>NEMATE PRIVILEGIJE!</h4>
					<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
					<br />
				</div>
';} ?>