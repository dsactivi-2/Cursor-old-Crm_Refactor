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
	<title>Nalozi | <?php getTitle(); ?></title>

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
					<h1> <i class="fa fa-money idk_color_green" aria-hidden="true"></i> Nalozi NP:</h1>
						<hr>
						<div class="row">
							<div class="col-md-12">
								<div class="content_box">	
									<div class="col-12 row">
										<div class="row">
											<div class="col-lg-4 col-md-4">
												<label for="filter_select_kompanija">Kompanija:</label>
												<select id="filter_select_kompanija" class="selectpicker" name="filter_select_kompanija" data-live-search="true"  data-actions-box="true" multiple>
													<?php 
														$select_query = $db->prepare("
															SELECT company_id, company_name
															FROM idk_companies
															JOIN idk_nalozi ON idk_companies.company_id = idk_nalozi.kompanija_id
															WHERE company_status = 1 AND nalog_status NOT IN (8,12)
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
												$('#filter_select_kompanija').on('change',function(){
													var selectedValues = $('#filter_select_kompanija').val();

													$.ajax({
														url: 'ajax_data.php?page=get_company_orders',
														type: 'POST',
														dataType: 'html',
														data: {
																'companies': selectedValues
															},
														success: function(data) {
															$("#filter_select_nalog").html(data).selectpicker("refresh");  
															var selectedValues = $('#filter_select_nalog').val();
															$.ajax({
																url: 'ajax_data.php?page=get_order_castings',
																type: 'POST',
																dataType: 'html',
																data: {
																		'orders': selectedValues
																	},
																success: function(data) {
																	$("#filter_select_casting").html(data).selectpicker("refresh");  
																},
																error: function (xhr, ajaxOptions, thrownError) {
																	alert(xhr.status);
																	alert(thrownError);
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
											<div class="col-lg-4 col-md-4">
												<label for="filter_select_nalog">Nalog:</label>
												<select id="filter_select_nalog" class="selectpicker" name="filter_select_nalog" data-live-search="true" data-actions-box="true"  multiple>
													<?php 
														
														$select_query = $db->prepare("
															SELECT nalog_id, nalog_naziv, company_id, company_name, nalog_broj
															FROM idk_companies
															JOIN idk_nalozi ON idk_companies.company_id = idk_nalozi.kompanija_id
															WHERE company_status = 1 AND nalog_status NOT IN (8,12)
															ORDER BY nalog_broj DESC");

														$select_query->execute();

														$orders = $select_query->fetchAll();

														foreach($orders as $order){
															echo '<option selected value="'.$order["nalog_id"].'">'.$order["nalog_broj"]." - ".$order["nalog_naziv"].' ('.$order["company_name"].')</option>';
														}
													?>
												</select>
											</div>
											<script>
												$('#filter_select_nalog').on('change',function(){
													var selectedValues = $('#filter_select_nalog').val();

													$.ajax({
														url: 'ajax_data.php?page=get_order_castings',
														type: 'POST',
														dataType: 'html',
														data: {
																'orders': selectedValues
															},
														success: function(data) {
															$("#filter_select_casting").html(data).selectpicker("refresh");  
														},
														error: function (xhr, ajaxOptions, thrownError) {
															alert(xhr.status);
															alert(thrownError);
														}
													});
												});
											</script>
                                            <div class="col-lg-4 col-md-4">
												<label for="filter_select_casting">Casting:</label>
												<select id="filter_select_casting" class="selectpicker" name="filter_select_casting" data-live-search="true" data-actions-box="true"  multiple>
													<?php 
														$select_query = $db->prepare("
															SELECT pap_id, pap_date, pap_city,nalog_naziv
															FROM idk_pp_appointments
															JOIN idk_nalozi ON idk_pp_appointments.pap_nalog_id = idk_nalozi.nalog_id
															JOIN idk_companies ON idk_companies.company_id = idk_nalozi.kompanija_id
															WHERE company_status = 1 AND nalog_status NOT IN (8,12)
															ORDER BY pap_date DESC");

														$select_query->execute();

														$castings = $select_query->fetchAll();

														foreach($castings as $casting){
															echo '<option selected value="'.$casting["pap_id"].'">'.$casting["pap_date"]." - ".$casting["pap_city"].' ('.$casting["nalog_naziv"].')</option>';
														}
													?>
												</select>
											</div>
										</div>
										<div class="row idk_margin_top20" style="display:flex; align-items: flex-end;">
											<div class="col-lg-3 col-md-3">
												<label for="filter_select_status_prijave">Status prijave:</label>
												<select id="filter_select_status_prijave" class="selectpicker" name="filter_select_status_prijave" data-live-search="true" data-actions-box="true" multiple>
													<?php 
														$status_prijave_query = $db->prepare("
															SELECT status_id, status_naziv, redoslijed_statusa
															FROM idk_kandidat_status_prijave
															ORDER BY redoslijed_statusa DESC");

														$status_prijave_query->execute();

														$statusi_prijave = $status_prijave_query->fetchAll();

														foreach($statusi_prijave as $status_prijave){
															echo '<option selected value="'.$status_prijave["status_id"].'">'.$status_prijave["status_naziv"].'</option>';
														}
													?>
												</select>
											</div>
                                            <div class="col-lg-3 col-md-3">
												<label for="filter_select_dipl_status">Dipl status:</label>
												<select id="filter_select_dipl_status" class="selectpicker" name="filter_select_dipl_status" data-live-search="true" data-actions-box="true" multiple>
													<option value = "0" selected>Nije u Diplu</option>
													<option value = "1|1" selected>Lead</option>
													<option value = "1|6" selected>Neuspješan Kontakt 1</option>
													<option value = "1|2" selected>Neuspješan Kontakt 3</option>
													<option value = "1|3" selected>Zainteresiran Lead</option>
													<option value = "1|4" selected>Nezainteresiran Lead</option>
													<option value = "1|5" selected>U obradi Lead</option>
													<option value = "1|7" selected>Neuspješan Lead 1</option>
													<option value = "1|8" selected>Neuspješan Lead 2</option>
													<option value = "1|9" selected>Termin Zainteresiran</option>
													<option value = "1|10" selected>Termin Ostali</option>
													<option value = "1|11" selected>Lead NL</option>
													<option value = "1|12" selected>Lead NZ</option>
													<option value = "2" selected>Prikupljanje dokumentacije</option>
													<option value = "3" selected>Poslana pošta</option>
													<option value = "4" selected>U obradi</option>
													<option value = "5" selected>Dopuna dokumentacije</option>
													<option value = "6" selected>Završen</option>
													<option value = "7" selected>Arhiviran</option>
												</select>
											</div>
                                            <div class="col-lg-3 col-md-3">
												<label for="filter_select_znanje_jezika">Znanje jezika:</label>
												<select id="filter_select_znanje_jezika" class="selectpicker" name="filter_select_znanje_jezika" data-live-search="true" data-actions-box="true" multiple>
													<option selected value="Bez znanja">Bez znanja</option>
													<option selected value="A1">A1</option>
													<option selected value="A2">A2</option>
													<option selected value="B1">B1</option>
													<option selected value="B2">B2</option>
													<option selected value="C1">C1</option>
													<option selected value="C2">C2</option>
												</select>
											</div>
											<div class="col-lg-2 col-md-3 d-flex align-items-center">
												<button id="filter_button_trazi" style="width:100%" class="btn btn-success">Traži</button>
											</div>
											<div class="col-lg-2 col-md-3 d-flex align-items-center">
												<button id="export_button" title="Prvo odaberite da izlista 'Sve' u tabeli." style="width:100%" class="btn btn-success">Export</button>
											</div>
											<script>
												function table_export () {

													var uri  = 'data:application/vnd.ms-excel;base64,';
													template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--><meta http-equiv="content-type" content="text/plain; charset=UTF-8"/></head><body><table>{table}</table></body></html>';

													base64 = function(s) { 
														return window.btoa(unescape(encodeURIComponent(s)));
													}

													format = function(s, c) { 
														return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) 
													}

													table_head_rows = document.querySelectorAll("#idk_table tr")[0];

													table_body_rows = document.querySelectorAll("#idk_table tbody tr");

													let table = document.querySelector("#idk_table");

													var ctx = {worksheet: new Date().toString().replace(" ", "") || 'Worksheet', table: table.innerHTML}
													window.location.href = uri + base64(format(template, ctx));

												}
												
												$('#export_button').on('click',function(){
													table_export();
												});
											</script>
										</div>
										
										<div id = "" width = "100" style = "margin-top: 100px;" >
										<table id="idk_table" class="display" cellspacing="0" width="100%">
								</table>
										</div>
										<!-- <div id = "to_append_to_export" style="display:none;"></div> -->
									</div>
								</div>
							</div>
						</div>							
						<script>
							function getTableInfo(){
								var filter_select_kompanija 	 = $('#filter_select_kompanija').val();
								var filter_select_nalog 		 = $('#filter_select_nalog').val();
								var filter_select_casting 		 = $('#filter_select_casting').val();
								var filter_select_status_prijave = $('#filter_select_status_prijave').val();
								var filter_select_dipl_status	 = $('#filter_select_dipl_status').val();
								var filter_select_znanje_jezika	= $('#filter_select_znanje_jezika').val();

								$('#idk_table').DataTable({
									responsive: true,
									"pageLength": 10,
									"processing": true,
									"serverSide": true,
									"order": [[ 0, "desc" ]],
									'columnDefs': [ {
										'targets': [],
										'orderable': false, 
									}],
									"aoColumns": [
											{ "width": "2%" , title: "ID"},
											{ "width": "18%" , title: "Ime i prezime"},
											{ "width": "10%" , title: "Status prijave"},
											{ "width": "5%", title: "t(Status prijave)"},
											{ "width": "10%", title: "Dipl status"},
											{ "width": "5%" , title: "t(Dipl status)"},
											{ "width": "5%", title: "Znanje jezika"},
											{ "width": "10%", title: "Status jezika"},
											{ "width": "5%", title: "Datum prelaska na status"},
											{ "width": "5%", title: "Datum početka kursa"},
											{ "width": "5%" , title: "Datum kraja kursa"},
											{ "width": "5%" , title: "Potencijalni početak rada"},
											{ "width": "5%" , title: "Dogovoreni početak rada"},
											{ "width": "5%" , title: "Proračunati početak rada"}
									],               
									lengthMenu: [
										[10, 25, 50, 100, -1],
										[10, 25, 50, 100, 'Sve'],
									],
									"ajax":{
										url :"serversidedata.php?page=nalozi_np",
										type: "POST",
										data: 
										{
											'filter_select_kompanija' 	    : filter_select_kompanija,
											'filter_select_nalog'           : filter_select_nalog,
											'filter_select_casting'         : filter_select_casting,
											'filter_select_status_prijave' 	: filter_select_status_prijave,
											'filter_select_dipl_status' 	: filter_select_dipl_status,
											'filter_select_znanje_jezika' 	: filter_select_znanje_jezika
										},
										error: function(data){
											$(".list-grid-error").html(""); 
											$("#list-grid_processing").css("display","none");
									
										},
									}
								});
							}

							$(document).ready(function() {
							    getTableInfo();
							});

							$('#filter_button_trazi').on('click',function(){
								var myDataTable = $('#idk_table').DataTable();
								// Destroy the existing DataTable instance
								if (typeof myDataTable !== 'undefined') {
									myDataTable.destroy();
								}
								getTableInfo();
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