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
	$page = "list_casting";
	header("Location: tf_agent_stats?page=list_casting");
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Statistika | <?php getTitle(); ?></title>

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
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
				case "list": // Stara lista - statistika agenata za broj poziva po vrsti taska: casting, posredovanje, obrada, inkaso
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
					<h1> <i class="fa fa-money idk_color_green" aria-hidden="true"></i> Statistika agenata:</h1>
						<hr>
						<div class="row">
							<div class="col-md-12">
								<div class="content_box">	
									<div class="col-12 row">
										<div class="row" style="display: flex;">
											<div class="col-lg-4 col-md-4">
												<label for="filter_select_vrste">Vrste taskova:</label>
												<select id="filter_select_vrste" class="selectpicker" name="filter_select_vrste" data-live-search="true"  data-actions-box="true" multiple>
													<option selected value="1">Casting</option>;
													<option selected value="2">Posredovanje</option>;
													<option selected value="3">Obrada</option>;
													<option selected value="4">Inkaso</option>;
												</select>
											</div>

                                            <div class="col-lg-2 col-md-2">
												<div class="form-group materail-input-block materail-input-block_primary materail-input_slide-line" style="margin: 0; min-width: 200px;">
													<label for="filter_datum">Datum:</label>
													<input type="text" class="form-control" name="filter_datum" id="filter_datum" placeholder="Datum" style="padding:17px;border-radius:0;">
													<span class="materail-input-block__line"></span>
												</div>
											</div>
                                            <div style="display:flex; justify-content: flex-end; align-items: flex-end; width: 100%;">
                                                <div class="col-lg-2 col-md-3 d-flex align-items-center">
                                                    <button id="filter_button_trazi" style="width:100%" class="btn btn-success">Traži</button>
                                                </div>
                                                <div class="col-lg-2 col-md-3 d-flex align-items-center">
                                                    <button id="export_button" style="width:100%" class="btn btn-success">Export</button>
                                                </div>
                                            </div>
										</div>
										
										<div id = "to_append_to_list" width = "100" style = "margin-top: 100px; min-height: 500px;" ></div>
										<div id = "to_append_to_export" style="display:none;"></div>
									</div>
								</div>
							</div>
						</div>
					<script>
						function getTable(){
							var filter_select_vrste = $('#filter_select_vrste').val();
							var filter_datum 	    = $('#filter_datum').val();
							
							$('#to_append_to_list').fadeOut(600, function(){
								$.ajax({
									url: 'ajax_data.php?page=get_agent_tf_stats',
									type: 'POST',
									dataType: 'html',
									data: {
											'filter_select_vrste' 	: filter_select_vrste,
											'filter_datum'          : filter_datum
										},
									success: function(data) {
										$("#to_append_to_list").fadeOut(600, function(){
											$("#to_append_to_list").empty().append(data).fadeIn(800);
											var table = $('#table').DataTable({
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
								$('#to_append_to_list').empty();
								$('#to_append_to_list').append('<div class="lds-hourglass"></div>').fadeIn(600);
							});
						}
						$(document).ready(function() {
                            getTable();
						});
						$("#filter_datum").flatpickr({
							mode: "range",
							dateFormat: "d.m.Y",
							disableMobile: "true",
							// defaultDate: "today"
						});
						
						$('#filter_button_trazi').on('click',function(){
							getTable();
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

							table_head_rows = document.querySelectorAll("#table tr")[0];

							table_body_rows = document.querySelectorAll("#table tbody tr");

							let table = document.querySelector("#table");

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
						
						$('#export_button').on('click',function(){
							table_export();
						});
					</script>
					<?php
					
				break;

				case "list_casting": // Nova lista za statistiku agenata za broj poziva samo za casting sa pie chart-ovima
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
						.chart-grid {
							display: grid;
							grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
							justify-content: center;
							gap: 20px;
						}

						.chart-container {
							max-width: auto;       
							height: 720px;          
							width: 100%;
							/* height: auto; */
							display: flex;
							flex-direction: column;
							align-items: center;
							justify-content: flex-start;
							background: #fff;
							border: 1px solid #ddd;
							border-radius: 12px;
							padding: 16px;
							box-shadow: 0 2px 5px rgba(0,0,0,0.1);
							text-align: center;
							margin: auto;
						}

						.chart-container canvas {
							width: 100% !important;
							height: auto !important;
							max-width: 300px;           
							max-height: 300px;
							margin: auto;
						}
						.custom-legend {
							margin-top: 10px;
							text-align: left;
							font-size: 14px;
						}

						.legend-item {
							display: flex;
							align-items: center;
							margin-bottom: 4px;
						}

						.legend-color {
							display: inline-block;
							width: 12px;
							height: 12px;
							border-radius: 2px;
							margin-right: 8px;
						}
					</style>
					<h1> <i class="fa fa-money idk_color_green" aria-hidden="true"></i> Statistika agenata:</h1>
						<hr>
						<div class="row">
							<div class="col-md-12">
								<div class="content_box">	
									<div class="col-12 row">
										<div class="row" style="display: flex;">
											<div class="col-lg-4 col-md-4">
												<label for="filter_select_vrste">Vrste komunikacije:</label>
												<select id="filter_select_vrste" class="selectpicker" name="filter_select_vrste" data-live-search="true"  data-actions-box="true" multiple>
													<option selected value="2|Neuspješna komunikacija">Neuspješna komunikacija</option>
													<option selected value="4|Ne ispunjava uslove za nalog">Ne ispunjava uslove za nalog</option>
													<option selected value="6|Nije zainteresiran">Nije zainteresiran</option>
													<option value="8|Ispunjava uslove - ne javlja se">Ispunjava uslove - ne javlja se</option>
													<option value="10|Ne odgovara mu termin za casting">Ne odgovara mu termin za casting</option>
													<option selected value="12|Dopuna">Dopuna</option>
													<option selected value="14|Zainteresiran">Zainteresiran</option>
													<option selected value="16|Pristao">Pristao</option>
													<option value="18|Dolazi">Dolazi</option>
													<option value="20|Došao">Došao</option>
													<option value="22|Nije došao">Nije došao</option>
													<option value="24|Inbound poziv">Inbound poziv</option>
													<option selected value="147|Pogrešan broj">Pogrešan broj</option>
													<option selected value="148|Nedostupan (bez kontakta)">Nedostupan (bez kontakta)</option>
												</select>
											</div>

                                            <div class="col-lg-2 col-md-2">
												<div class="form-group materail-input-block materail-input-block_primary materail-input_slide-line" style="margin: 0; min-width: 200px;">
													<label for="filter_datum">Datum:</label>
													<input type="text" class="form-control" name="filter_datum" id="filter_datum" placeholder="Datum" style="padding:17px;border-radius:0;">
													<span class="materail-input-block__line"></span>
												</div>
											</div>
                                            <div style="display:flex; justify-content: flex-end; align-items: flex-end; width: 100%;">
                                                <div class="col-lg-2 col-md-3 d-flex align-items-center">
                                                    <button id="filter_button_trazi" style="width:100%" class="btn btn-success">Traži</button>
                                                </div>
                                                <div class="col-lg-2 col-md-3 d-flex align-items-center">
                                                    <button id="export_button" style="width:100%" class="btn btn-success">Export</button>
                                                </div>
												<div class="col-lg-2 col-md-3 d-flex align-items-center">
                                                    <button id="link_old_stats" style="width:100%" class="btn btn-success">Stara statistika</button>
                                                </div>
                                            </div>
										</div>
										
										<div id = "to_append_to_list" width = "100" style = "margin-top: 100px; min-height: 500px;" ></div>
										<div id = "to_append_to_export" style="display:none;"></div>
									</div>
								</div>
							</div>
						</div>
					<script>
						document.getElementById('link_old_stats').addEventListener('click', function() {
							window.location.href = 'tf_agent_stats.php?page=list';
						});
						function renderAgentCharts() {
							const colorMap = {
								"Neuspješna komunikacija": "#FF6384",
								"Ne ispunjava uslove za nalog": "#36A2EB",
								"Nije zainteresiran": "#FFCE56",
								"Ispunjava uslove - ne javlja se": "#4BC0C0",
								"Ne odgovara mu termin za casting": "#9966FF",
								"Dopuna": "#FF9F40",
								"Zainteresiran": "#E7E9ED",
								"Pristao": "#8BC34A",
								"Dolazi": "#F44336",
								"Došao": "#00BCD4",
								"Nije došao": "#CDDC39",
								"Inbound poziv": "#9C27B0",
								"Pogrešan broj": "#795548",
								"Nedostupan (bez kontakta)": "#607D8B"
							};

							document.querySelectorAll('.chart-canvas').forEach((canvas) => {
								const labels = JSON.parse(canvas.dataset.labels);
								const data = JSON.parse(canvas.dataset.values);
								const total = data.reduce((sum, val) => sum + val, 0);
								const colors = labels.map(label => colorMap[label] || '#999999');

								const ctx = canvas.getContext('2d');

								
								Chart.register({
									id: 'afterDrawLegend',
									afterDraw(chart) {
										const canvas = chart.canvas;
										const labels = chart.data.labels;
										const rawData = chart.data.datasets[0].data;
										const colors = chart.data.datasets[0].backgroundColor;

										// Exclude 'Overall' from legend
										const filtered = labels
											.map((label, i) => ({ label, value: rawData[i], color: colors[i] }))
											.filter(item => item.label !== 'Overall');

										const total = filtered.reduce((sum, item) => sum + Number(item.value), 0);

										if (canvas.parentElement.querySelector('.custom-legend')) return;

										const legend = document.createElement('div');
										legend.className = 'custom-legend';

										filtered.forEach(({ label, value, color }) => {
											const percent = total > 0 ? ((value / total) * 100).toFixed(2) : "0.00";
											const item = document.createElement('div');
											item.className = 'legend-item';
											item.innerHTML = `
												<span class="legend-color" style="background-color: ${color};"></span>
												<span>${percent}% (${value}) – ${label}</span>
											`;
											legend.appendChild(item);
										});

										canvas.parentElement.appendChild(legend);
									}
								});

								// Remove 'Overall' before creating the chart
								const chartLabels = labels.filter((label, i) => label !== 'Ukupno taskova' && label !== 'AgentID');
								const chartData = data.filter((_, i) => labels[i] !== 'Ukupno taskova' && labels[i] !== 'AgentID');
								const chartColors = colors.filter((_, i) => labels[i] !== 'Ukupno taskova' && labels[i] !== 'AgentID');

								new Chart(ctx, {
									type: 'pie',
									data: {
									labels: chartLabels,
									datasets: [{
										data: chartData,
										backgroundColor: chartColors
									}]
								},
									options: {
										plugins: {
											legend: { display: false },
											tooltip: {
											callbacks: {
												label: function(context) {
													const value = context.parsed;
													const data = context.dataset.data;
													const total = data.reduce((sum, val) => sum + Number(val), 0); 
													const percent = total > 0 ? ((value / total) * 100).toFixed(2) : "0.00";
													return `${percent}% (${value}) – ${context.label}`;
												}
											}
											},
											// Custom plugin to render HTML legend after draw
											afterDrawLegend: {
											id: 'afterDrawLegend',
											afterDraw(chart) {
												const canvas = chart.canvas;
												const labels = chart.data.labels;
												const data = chart.data.datasets[0].data;
												const total = data.reduce((sum, val) => sum + val, 0);
												const colors = chart.data.datasets[0].backgroundColor;

												// Avoid duplication
												if (canvas.parentElement.querySelector('.custom-legend')) return;

												const legend = document.createElement('div');
												legend.className = 'custom-legend';

												labels.forEach((label, i) => {
												const value = data[i];
												const percent = ((value / total) * 100).toFixed(2);
												const color = colors[i];

												const item = document.createElement('div');
												item.className = 'legend-item';
												item.innerHTML = `
													<span class="legend-color" style="background-color: ${color};"></span>
													<span>${percent}% (${value}) – ${label}</span>
												`;
												legend.appendChild(item);
												});

												canvas.parentElement.appendChild(legend);
											}
											}
										}
									}
								});

								

							});

							function generateCustomLegend(canvas, labels, data, colors, total) {
								const legend = document.createElement('div');
								legend.className = 'custom-legend';

								labels.forEach((label, i) => {
									const value = data[i];
									const percent = ((value / total) * 100).toFixed(2);
									const color = colors[i];

									const item = document.createElement('div');
									item.className = 'legend-item';
									item.innerHTML = `
										<span class="legend-color" style="background-color: ${color};"></span>
										<span>${percent}% (${value}) – ${label}</span>
									`;
									legend.appendChild(item);
								});

								canvas.parentElement.appendChild(legend);
							}
						}

						function getTable(){
							var filter_select_vrste = $('#filter_select_vrste').val();
							var filter_datum 	    = $('#filter_datum').val();
							
							$('#to_append_to_list').fadeOut(600, function(){
								$.ajax({
									url: 'ajax_data.php?page=get_agent_tf_stats_casting',
									type: 'POST',
									dataType: 'html',
									data: {
											'filter_select_vrste' 	: filter_select_vrste,
											'filter_datum'          : filter_datum
										},
									success: function(data) {
										$("#to_append_to_list").fadeOut(600, function(){
											$("#to_append_to_list").empty().append(data).fadeIn(800);
											var table = $('#table').DataTable({
												responsive: true,
												"bAutoWidth": false,
												lengthMenu: [
													[10, 25, 50, -1],
													[10, 25, 50, 'All'],
												],
											});
											renderAgentCharts();
										});
									},
									error: function (xhr, ajaxOptions, thrownError) {
										alert(xhr.status);
										alert(thrownError);
									}
								});
								$('#to_append_to_list').empty();
								$('#to_append_to_list').append('<div class="lds-hourglass"></div>').fadeIn(600);
							});
						}
						$(document).ready(function() {
                            getTable();
						});
						$("#filter_datum").flatpickr({
							mode: "range",
							dateFormat: "d.m.Y",
							disableMobile: "true",
							// defaultDate: "today"
						});
						
						$('#filter_button_trazi').on('click',function(){
							getTable();
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

							table_head_rows = document.querySelectorAll("#table tr")[0];

							table_body_rows = document.querySelectorAll("#table tbody tr");

							let table = document.querySelector("#table");

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
						
						$('#export_button').on('click',function(){
							table_export();
						});
					</script>
					<?php
					
				break;

				case "candidates": // Stara lista sa svim taskova za agenta - dodan pie chart za casting
					$type  = $_REQUEST['type'];
					$date  = $_REQUEST['date'];
					$agent = $_REQUEST['agent'];
					?>
					<style>
						.akcija{
							display:flex !important;
						}
						.card{
							margin-top: 40px;
							width: 100%;
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

						.rectangle{
						position: absolute;
						width: 100%;
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
					<h1> <i class="fa fa-money idk_color_green" aria-hidden="true"></i> Lista taskova za agenta: <?php echo getEmployeeFullnameById($agent); ?></h1>
					<h1><?php echo $date; ?></h1>
					<hr>
					<div class="row">
							<div class="col-md-12">
								<div class="content_box">	
									<div class="col-12 row">
										<div id = "to_append_to_list" width = "100" style = "margin-top: 10px; min-height: 500px;" ></div>
										<div id = "to_append_to_export" style="display:none;"></div>
									</div>
								</div>
							</div>
						</div>
					<script>
						function getTable(){
							
							$('#to_append_to_list').fadeOut(600, function(){
								var filter_select_vrste =  '<?php echo $type;?>';
								var filter_datum 	    =  '<?php echo $date;?>';
								var agent 	    		=  '<?php echo $agent;?>';

								$.ajax({
									url: 'ajax_data.php?page=get_agent_tf_stats_candidates',
									type: 'POST',
									dataType: 'html',
									data: {
											'filter_select_vrste' 	: filter_select_vrste,
											'filter_datum'          : filter_datum,
											'agent'          		: agent
										},
									success: function(data) {
										$("#to_append_to_list").fadeOut(600, function(){
											$("#to_append_to_list").empty().append(data).fadeIn(800);
											window.table = $('#table').DataTable({
												responsive: true,
												"bAutoWidth": false,
												lengthMenu: [
													[10, 25, 50, -1],
													[10, 25, 50, 'All'],
												],
												drawCallback: function () {
													var api = this.api();
													var data = api.column(4, { search: 'applied' }).data(); // adjust column index if needed
													var counts = {};

													data.each(function (value) {
														counts[value] = (counts[value] || 0) + 1;
													});

													// Prepare data
													const labels = Object.keys(counts);
													const values = Object.values(counts);
													const colors = ['#FF6384', '#36A2EB', '#FFCE56', '#8AFFC1', '#D66BFF', '#FF9F40',
													'#4BC0C0', '#9966FF', '#C9CBCF', '#FF8C00', '#8B008B', '#228B22',
													'#00CED1', '#FFD700', '#CD5C5C', '#DC143C', '#8A2BE2', '#5F9EA0',
													'#6495ED', '#FFA07A', '#B0C4DE', '#20B2AA', '#9370DB', '#7FFFD4']; // Extend if needed

													// Destroy old chart
													if (window.taskTypeChart instanceof Chart) {
														window.taskTypeChart.destroy();
													}

													// Render Pie Chart
													var ctx = document.getElementById('taskTypeChart').getContext('2d');
													window.taskTypeChart = new Chart(ctx, {
														type: 'pie',
														data: {
														labels: labels,
														datasets: [{
															label: 'Task Type',
															data: values,
															backgroundColor: colors.slice(0, labels.length),
															borderWidth: 1
														}]
														},
														options: {
														responsive: true,
														maintainAspectRatio: false,
														plugins: {
															legend: {
															display: false // Disable built-in legend
															},
															tooltip: {
															callbacks: {
																label: function (context) {
																let label = context.label || '';
																let value = context.parsed || 0;
																let total = context.chart._metasets[0].total || context.dataset.data.reduce((a, b) => a + b, 0);
																let percent = ((value / total) * 100).toFixed(1);
																return `${label}: ${value} (${percent}%)`;
																}
															}
															}
														}
														}
													});

													// Render Custom Legend
													const total = values.reduce((a, b) => a + b, 0);
													const legendHTML = labels.map((label, i) => {
														const count = values[i];
														const percent = total ? ((count / total) * 100).toFixed(1) : 0;
														return `
														<div style="display: flex; align-items: center; margin-bottom: 8px;">
															<div style="width: 14px; height: 14px; background-color: ${colors[i]}; border-radius: 50%; margin-right: 10px;"></div>
															<div style="font-size: 14px; color: #333;">
															<strong>${label}</strong>: ${count} (${percent}%)
															</div>
														</div>
														`;
													}).join('');

													document.getElementById('taskTypeLegend').innerHTML = `<div>${legendHTML}</div>`;
												}

											});
										});
									},
									error: function (xhr, ajaxOptions, thrownError) {
										alert(xhr.status);
										alert(thrownError);
									}
								});
								$('#to_append_to_list').empty();
								$('#to_append_to_list').append('<div class="lds-hourglass"></div>').fadeIn(600);
							});
						}
						$(document).ready(function() {
                            getTable();
						});
						$("#filter_datum").flatpickr({
							mode: "range",
							dateFormat: "d.m.Y",
							disableMobile: "true",
							defaultDate: "today"
						});
						
						$('#filter_button_trazi').on('click',function(){
							getTable();
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

							table_head_rows = document.querySelectorAll("#table tr")[0];

							table_body_rows = document.querySelectorAll("#table tbody tr");

							let table = document.querySelector("#table");

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
						
						$('#export_button').on('click',function(){
							table_export();
						});
					</script>
					<?php
					
				break;

				case "candidates_list_for_agent": // Nova lista sa svim taskova za agenta uz pie chart taskova i sumu kandidata po statusima
					$type  = $_REQUEST['type'];
					$date  = $_REQUEST['date'];
					$agent = $_REQUEST['agent'];
					?>
					<style>
						.akcija{
							display:flex !important;
						}
						.card{
							margin-top: 40px;
							width: 100%;
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
						width: 300px;
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

						.rectangle{
						position: absolute;
						width: 100%;
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
						.stat-grid_new {
							display: grid;
							grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
							gap: 15px;
							/* max-width: 1000px; */
							margin: 0 auto;
							padding: 20px;
						}

						.card_new {
							background: linear-gradient(to bottom right, #5ec16b, #0d4214);
							color: white;
							text-align: center;
							border-radius: 10px;
							padding: 20px;
							font-size: 2rem;
							font-weight: bold;
							box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
						}

						.card_new_other {
							background: linear-gradient(to bottom right, #5ca9dd, #0b3c5d);
							color: white;
							text-align: center;
							border-radius: 10px;
							padding: 20px;
							font-size: 2rem;
							font-weight: bold;
							box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
						}

						.card_new_unres {
							background: linear-gradient(to bottom right, #d3d3d3, #6e6e6e);
							color: white;
							text-align: center;
							border-radius: 10px;
							padding: 20px;
							font-size: 2rem;
							font-weight: bold;
							box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
						}
						.section-title {
							font-size: 2rem;
							font-weight: bold;
							margin: 30px 0 10px;
							color: #003153;
						}
						.card-container {
							display: flex;
							flex-wrap: wrap;
							gap: 20px;
							margin-bottom: 30px;
						}
						.cardf {
							flex: 1 0 180px;
							padding: 12px 10px;
							color: white;
							font-weight: bold;
							border-radius: 10px;
							box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
							text-align: center;
							font-size: 2rem;
							text-decoration: none;
							transition: transform 0.1s ease-in-out;
						}

						.cardf.green {
							background: linear-gradient(to bottom right, #5ec16b, #0d4214);
						}
						.cardf.green:hover{
							transform: scale(1.03);
							cursor: pointer;
						}
						.cardf.blue {
							background: linear-gradient(to bottom right, #5ba4d4, #093c66);
						}
						.cardf.gray {
							background: linear-gradient(to bottom right, #d3d3d3, #6e6e6e);
							color: white;
						}
						.cardf.total {
							background: linear-gradient(to bottom right, #a8d5a2, #6e8f63);
							font-size: 3rem;
							padding: 25px;
							flex: 1;
							text-align: center;
							box-shadow: 0 0 12px rgba(76, 175, 80, 0.5);
						}
						.cardf.secondary {
							background: linear-gradient(to bottom right, #a9cfe7, #5c7080);
							font-size: 3rem;
							padding: 25px;
							flex: 1;
							text-align: center;
							box-shadow: 0 0 12px rgba(0, 0, 0, 0.2);
							}
					</style>
					<h1> <i class="fa fa-money idk_color_green" aria-hidden="true"></i> Lista taskova za agenta: <?php echo getEmployeeFullnameById($agent); ?></h1>
					<h1><?php echo $date; ?></h1>
					<hr>
					<div class="row">
						<div class="col-md-12">
							<div class="content_box">	
								<div class="col-12 row">
									<div id = "to_append_to_list" width = "100" style = "margin-top: 10px; min-height: 500px;" ></div>
									<div id = "to_append_to_export" style="display:none;"></div>
								</div>
							</div>
						</div>
					</div>
					<script>
						function getTable(){
							
							$('#to_append_to_list').fadeOut(600, function(){
								var filter_select_vrste =  '<?php echo $type;?>';
								var filter_datum 	    =  '<?php echo $date;?>';
								var agent 	    		=  '<?php echo $agent;?>';

								$.ajax({
									url: 'ajax_data.php?page=get_agent_casting_tasks_stats',
									type: 'POST',
									dataType: 'html',
									data: {
											'filter_select_vrste' 	: filter_select_vrste,
											'filter_datum'          : filter_datum,
											'agent'          		: agent
										},
									success: function(data) {
										$("#to_append_to_list").fadeOut(600, function(){
											$("#to_append_to_list").empty().append(data).fadeIn(800);
											window.table = $('#table').DataTable({
												responsive: true,
												"bAutoWidth": false,
												lengthMenu: [
													[10, 25, 50, -1],
													[10, 25, 50, 'All'],
												],
												drawCallback: function () {
													var api = this.api();
													var data = api.column(4, { search: 'applied' }).data(); // adjust column index if needed
													var counts = {};

													data.each(function (value) {
														counts[value] = (counts[value] || 0) + 1;
													});

													// Prepare data
													const labels = Object.keys(counts);
													const values = Object.values(counts);
													const colors = ['#FF6384', '#36A2EB', '#FFCE56', '#8AFFC1', '#D66BFF', '#FF9F40',
													'#4BC0C0', '#9966FF', '#C9CBCF', '#FF8C00', '#8B008B', '#228B22',
													'#00CED1', '#FFD700', '#CD5C5C', '#DC143C', '#8A2BE2', '#5F9EA0',
													'#6495ED', '#FFA07A', '#B0C4DE', '#20B2AA', '#9370DB', '#7FFFD4']; // Extend if needed

													// Destroy old chart
													if (window.taskTypeChart instanceof Chart) {
														window.taskTypeChart.destroy();
													}

													// Render Pie Chart
													var ctx = document.getElementById('taskTypeChart').getContext('2d');
													window.taskTypeChart = new Chart(ctx, {
														type: 'pie',
														data: {
														labels: labels,
														datasets: [{
															label: 'Task Type',
															data: values,
															backgroundColor: colors.slice(0, labels.length),
															borderWidth: 1
														}]
														},
														options: {
														responsive: true,
														maintainAspectRatio: false,
														plugins: {
															legend: {
															display: false // Disable built-in legend
															},
															tooltip: {
															callbacks: {
																label: function (context) {
																let label = context.label || '';
																let value = context.parsed || 0;
																let total = context.chart._metasets[0].total || context.dataset.data.reduce((a, b) => a + b, 0);
																let percent = ((value / total) * 100).toFixed(1);
																return `${label}: ${value} (${percent}%)`;
																}
															}
															}
														}
														}
													});

													// Render Custom Legend
													const total = values.reduce((a, b) => a + b, 0);
													const legendHTML = labels.map((label, i) => {
														const count = values[i];
														const percent = total ? ((count / total) * 100).toFixed(1) : 0;
														return `
														<div style="display: flex; align-items: center; margin-bottom: 8px;">
															<div style="width: 14px; height: 14px; background-color: ${colors[i]}; border-radius: 50%; margin-right: 10px;"></div>
															<div style="font-size: 14px; color: #333;">
															<strong>${label}</strong>: ${count} (${percent}%)
															</div>
														</div>
														`;
													}).join('');

													document.getElementById('taskTypeLegend').innerHTML = `<div>${legendHTML}</div>`;
												}

											});
										});
									},
									error: function (xhr, ajaxOptions, thrownError) {
										alert(xhr.status);
										alert(thrownError);
									}
								});
								$('#to_append_to_list').empty();
								$('#to_append_to_list').append('<div class="lds-hourglass"></div>').fadeIn(600);
							});
						}
						$(document).ready(function() {
                            getTable();
						});
						$("#filter_datum").flatpickr({
							mode: "range",
							dateFormat: "d.m.Y",
							disableMobile: "true",
							defaultDate: "today"
						});
						
						$('#filter_button_trazi').on('click',function(){
							getTable();
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

							table_head_rows = document.querySelectorAll("#table tr")[0];

							table_body_rows = document.querySelectorAll("#table tbody tr");

							let table = document.querySelector("#table");

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
						
						$('#export_button').on('click',function(){
							table_export();
						});
					</script>
					<?php
					
				break;

				case "candidate_per_sp_for_agent":
					$agent	= $_REQUEST['agent'];	
					$type	= $_REQUEST['type'];		// type 1 - for that agent; type 2 - for other agents
					$date	= $_REQUEST['date'];
					$sp 	= $_REQUEST['sp']; 			// status prijave

					?>
					<style>
						#table th, 
						#table td {
							text-align: left !important;
						}
					</style>
					<h1> <i class="fa fa-money idk_color_green" aria-hidden="true"></i> Lista kandidata na statusu <b><?php echo strip_tags(getStatusPrijavePrint($sp)); ?></b> za agenta: <b><?php echo getEmployeeFullnameById($agent); ?></b>, koje je prozvao u periodu:</h1>
					<h1><b><?php echo $date; ?></b></h1>
					<hr>
					<div class="row">
						<div class="col-md-12">
							<div class="content_box">	
								<div class="col-12 row">
									<div id = "to_append_to_list" width = "100" style = "margin-top: 10px; min-height: 500px;" ></div>
									<div id = "to_append_to_export" style="display:none;"></div>
								</div>
							</div>
						</div>
					</div>

					<script>
						function getTable(){
							$('#to_append_to_list').fadeOut(600, function(){
								var agent 	= '<?php echo $agent;?>';
								var type 	= '<?php echo $type;?>';
								var date 	= '<?php echo $date;?>';
								var sp 		= '<?php echo $sp;?>';

								$.ajax({
									url: 'ajax_data.php?page=get_candidates_list_by_sp',
									type: 'POST',
									dataType: 'html',
									data: {
										'agent'		: agent,
										'type' 		: type,
										'date' 		: date,
										'sp' 		: sp
									},
									success: function(data) {
										$("#to_append_to_list").fadeOut(600, function(){
											$("#to_append_to_list").empty().append(data).fadeIn(800);
											var table = $('#table').DataTable({
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
								$('#to_append_to_list').empty();
								$('#to_append_to_list').append('<div class="lds-hourglass"></div>').fadeIn(600);
							});
						}
						$(document).ready(function() {
                            getTable();
						});
					</script>
					
					<?php
					var_dump($agent);
					var_dump($type);
					var_dump($date);
					var_dump($sp);


				break;
			}
		?>
			<footer><?php getCopyright(); ?></footer>
		</div>
	</div>
</body>
</html>