<?php

include($_SERVER["DOCUMENT_ROOT"] . "/includes/functions.php");
include($_SERVER["DOCUMENT_ROOT"] . "/includes/common.php");

$getEmployeeStatus = explode(',', getEmployeeStatus());


?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Partner App FAQ</title>

	<?php include('../includes/head.php');
	if (in_array($getUserIp, $getIpWhiteList)) {
	?>

</head>

<body>
	<header>
		<?php include('../header.php'); ?>
	</header>
	<div id="sidebar">
		<?php include('../menu.php'); ?>
	</div>
	<div id="content">
		<div class="container-fluid">

			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-file-text-o idk_color_green" aria-hidden="true"></i> Statistike</h1>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
                        <div id="partner_statistics" class="panel-group material-tabs-group">
                            <ul class="nav nav-tabs material-tabs material-tabs_primary">
                                <li><a href="#user_log" class="material-tabs__tab-link" data-toggle="tab">User log</a></li>
                                <li><a href="#work_activity" class="material-tabs__tab-link" data-toggle="tab">Work activity</a></li>
                                <li><a href="#makleri" class="material-tabs__tab-link" data-toggle="tab">Finance</a></li>
                            </ul>
                            <div class="tab-content materail-tabs-content" style = "padding-top: 0px!important;">
                                <div class="tab-pane fade in" id="work_activity">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="content_box" style = "padding:0px;">
                                                <div class="row">
													<div id="work_activity_subtab" class="panel-group material-tabs-group">
														<ul class="nav nav-tabs material-tabs material-tabs_success">
															<li><a href="#work_activity_overview" class="material-tabs__tab-link" data-toggle="tab">Overview</a></li>
															<li><a href="#work_activity_dashboard" class="material-tabs__tab-link" data-toggle="tab">Dashboard</a></li>
														</ul>
														<div class="tab-content materail-tabs-content">
															<div class="tab-pane fade in" id="work_activity_overview">
																<div class="row">
																	<div class="col-md-12">
																		<div class="content_box">
																			<div class="row">
																				<table id = "table_work_activity" style="width:100%">
																					<thead>
																						<th>Ime i prezime</th>
																						<th>Direktiva</th>
																						<th>Makler ID</th>
																						<th>Broj kreiranih kompanija</th>
																						<th>Broj odbijenih</th>
																						<th>Broj ugovora</th>
																						<th>Ukupan broj prijavljenih kandidata</th>
																						<th>Ukupan broj traženih kandidata</th>
																					</thead>
																				</table>
																				<script>
																					$(document).ready(function(){
																						var myTable = $('#table_work_activity').DataTable({
																							ajax: 'data.php?request=table_work_activity',
																							columns: [
																								{data: 'full_name'},
																								{data: 'directive_number'},
																								{data: 'makler_id'},
																								{data: 'total_companies'},
																								{data: 'rejected_companies'},
																								{data: 'contracted_companies'},
																								{data: 'applied_candidates'},
																								{data: 'total_candidates'}
																							]
																							
																						});
																					});

																				</script>
																			</div>
																			<div class="row">
																				<div class="col-xs-4 text-right">
																					<button id="export_user_work_activity_bih" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-file-excel-o" aria-hidden="true"></i> <span>Export BiH</span></button>
																				</div>
																				<div class="col-xs-4 text-right">
																					<button id="export_user_work_activity_de" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-file-excel-o" aria-hidden="true"></i> <span>Export DE</span></button>
																				</div>
																				<div id="export_user_work_activity_div"></div>
																				<script>
																					$(document).ready(function() {
																						$('#export_user_work_activity_bih').click(function() {
																							$('#export_user_work_activity_div').load('export.php?request=export_work_activity&lang=bs');
																							return false;
																						});
																						$('#export_user_work_activity_de').click(function() {
																							$('#export_user_work_activity_div').load('export.php?request=export_work_activity&lang=de');
																							return false;
																						});
																					});				
																				</script>	
																			</div>
																		</div>
																	</div>
																</div>
															</div>
															<div class="tab-pane fade in" id="work_activity_dashboard">
																<div class="row">
																	<div class="col-md-12">
																		<div class="content_box">
																			<div class="row">
																				<div class="col-md-6">
																					<div class="chart-div" style="width:100%; margin-right:10rem" >
																						<canvas id="companyCategorisationChart"></canvas>
																					</div>
																				</div>
																				<div class="col-md-6">
																					<div class="row">
																					<div class="col-md-3"></div>
																					<div class="col-md-9" style="width: 50%">
																						<canvas id="companyStatusChart"></canvas>
																					</div>
																					</div>
																				</div>
																				<script>
																					const ctx1 = document.getElementById('companyCategorisationChart');
																					const ctx2 = document.getElementById('companyStatusChart');
																					const url1 = "data.php?request=bar_company_sizes";
																					const url2 = "data.php?request=pie_company_statuses";
																					const data1 = fetch(url1).then(response => response.json()).then(data => {
																						new Chart(ctx1, {
																							type: 'bar',
																							data: {
																								labels: ['1-50', '51-100', '101-500', '+500'],
																								datasets: [{
																									label: 'Company sizes',
																									data: [data.small, data.medium, data.enterprise, data.vip],
																									borderWidth: 1,
																									backgroundColor: [
																										'rgb(255, 99, 132)',
																										'rgb(54, 162, 235)',
																										'rgb(75, 192, 192)',
																										'rgb(255, 205, 86)'
																									],
																								}]
																							},
																							options: {
																							scales: {
																								y: {
																								beginAtZero: true
																								}
																							},
																							}
																						});
																					});
																					const data2 = fetch(url2).then(response => response.json()).then(data => {
																						new Chart(ctx2, {
																							type: 'pie',
																							data: {
																								labels: ['Archived', 'Active', 'New', 'In progress', 'On hold', 'Rejected', 'Finished'],
																								datasets: [{
																									label: 'Company status',
																									data: [data.Archived, data.Active, data.New, data.In_progress, data.On_hold, data.Rejected, data.Finished],
																									backgroundColor: [
																										'rgb(131, 144, 152)',
																										'rgb(0, 250, 251)',
																										'rgb(0, 251, 83)',
																										'rgb(14, 105, 115)',
																										'rgb(191, 33, 75)',
																										'rgb(199, 156, 255)',
																										'rgb(242, 228, 46)'
																									],
																								}]
																							},
																							options: {
																								responsive: true,
																								plugins: {
																									legend: {
																										position: 'top',
																										align: 'center'
																									},
																									title: {
																										display: true,
																										text: 'Company status'
																									}
																								}
																							}
																						});
																					});
																				</script>
																			</div>
																			<div class="row">
																				<div class="col-md-6">
																					<div class="chart-div" style="width:100%; margin-right:10rem" >
																						<canvas id="usersPerDirective"></canvas>
																					</div>
																				</div>
																				<div class="col-md-6">
																					<div class="row">
																					<div class="col-md-3"></div>
																					<div class="col-md-9" style="width: 50%">
																						<canvas id="professionsChart"></canvas>
																					</div>
																					</div>
																				</div>
																			</div>
																			<script>
																				//array of 20 random rgb colors
																				const colors = [
																					'rgb(255, 99, 132)',
																					'rgb(54, 162, 235)',
																					'rgb(255, 205, 86)',
																					'rgb(75, 192, 192)',
																					'rgb(153, 102, 255)',
																					'rgb(255, 159, 64)',
																					'rgb(255, 99, 132)',
																					'rgb(54, 162, 235)',
																					'rgb(255, 205, 86)',
																					'rgb(75, 192, 192)',
																					'rgb(153, 102, 255)',
																					'rgb(255, 159, 64)',
																					'rgb(255, 99, 132)',
																					'rgb(54, 162, 235)',
																					'rgb(255, 205, 86)',
																					'rgb(75, 192, 192)',
																					'rgb(153, 102, 255)',
																					'rgb(255, 159, 64)',
																					'rgb(255, 99, 132)',
																					'rgb(54, 162, 235)',
																				];
																				const ctx3 = document.getElementById('usersPerDirective');
																				const url3 = "data.php?request=bar_users_per_directive";
																				const data3 = fetch(url3).then(response => response.json()).then(data => {
																					new Chart(ctx3, {
																							type: 'bar',
																							data: {
																								labels: data.data.map(item => item.directive_number),
																								datasets: [{
																									label: 'Users per directive',
																									data: data.data.map(item => item.user_count),
																									backgroundColor: colors,
																									borderWidth: 1,
																								}]
																							},
																							options: {
																							scales: {
																								y: {
																								beginAtZero: true
																								}
																							},
																							}
																						});	
																				});

																				const ctxP = document.getElementById('professionsChart');
																				const urlP = "data.php?request=pie_common_professions";
																				const dataP = fetch(urlP).then(response => response.json()).then(data => {
																					new Chart(ctxP, {
																						type: 'pie',
																						data: {
																							labels: data.map(item => item.profession),
																							datasets: [{
																								label: 'Common professions',
																								data: data.map(item => item.profession_count),
																								backgroundColor: colors,
																							}]
																						},
																						options: {
																							responsive: true,
																							plugins: {
																								legend: {
																									position: 'top',
																									align: 'center'
																								},
																								title: {
																									display: true,
																									text: 'Common professions'
																								}
																							}
																						}
																					});
																				});
																			</script>
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
                                </div>
								<div class="tab-pane fade in" id="user_log">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="content_box" style = "padding:0px;">
                                                <div class="row">
													<div id="work_activity_subtab" class="panel-group material-tabs-group">
														<ul class="nav nav-tabs material-tabs material-tabs_success">
															<li><a href="#user_log_overview" class="material-tabs__tab-link" data-toggle="tab">Overview</a></li>
															<li><a href="#user_log_dashboard" class="material-tabs__tab-link" data-toggle="tab">Dashboard</a></li>
														</ul>
														<div class="tab-content materail-tabs-content">
															<div class="tab-pane fade in" id="user_log_overview">
																<div class="row">
																	<div class="col-md-12">
																		<div class="content_box">
																			<div class="row">
																				<table id = "table_user_log" style="width:100%">
																					<thead>
																						<th>Ime i prezime</th>
																						<th>Direktiva</th>
																						<th>Makler ID</th>
																						<th>Datum Registracije</th>
																						<th>Prvi login</th>
																						<th>Zadnja aktivnost</th>
																						<th>Account status</th>
																					</thead>
																				</table>
																				<script>
																					$(document).ready(function(){
																						var myTable = $('#table_user_log').DataTable({
																							ajax: 'data.php?request=table_user_activity',
																							columns: [
																								{data: 'full_name'},
																								{data: 'directive_number'},
																								{data: 'makler_id'},
																								{data: 'registration_date'},
																								{data: 'first_login_date'},
																								{data: 'latest_activity_date'},
																								{data: 'account_status'},
																							]
																							
																						});
																					});

																				</script>
																			</div>
																		</div>
																	</div>
																</div>
																<div class="row">
																	<div class="col-xs-4 text-right">
																		<button id="export_user_log_bih" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-file-excel-o" aria-hidden="true"></i> <span>Export BiH</span></button>
																	</div>
																	<div class="col-xs-4 text-right">
																		<button id="export_user_log_de" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-file-excel-o" aria-hidden="true"></i> <span>Export DE</span></button>
																	</div>
																	<div id="export_user_log_div"></div>
																	<script>
																		$(document).ready(function() {
																			$('#export_user_log_bih').click(function() {
																				$('#export_user_log_div').load('export.php?request=export_user_activity&lang=bs');
																				return false;
																			});
																			$('#export_user_log_de').click(function() {
																				$('#export_user_log_div').load('export.php?request=export_user_activity&lang=de');
																				return false;
																			});
																		});				
																	</script>	
																</div>
															</div>
															<div class="tab-pane fade in" id="user_log_dashboard">
																<div class="row">
																	<div class="col-md-12">
																		<div class="content_box">
																			<div class="row">
																				<div class="col-md-6">
																					<div class="row">
																						<div class="col-md-9">
																							<canvas id="registeredUsersChart"></canvas>
																						</div>
																						<div class="col-md-3">
																							<select class="selectpicker" id="registeredUsersSelect">
																								<option selected disabled>Filter</option>
																								<option value="1">Zadnjih 7 dana</option>
																								<option value="2">Zadnjih 30 dana</option>
																								<option value="3">Zadnji kvartal</option>
																								<option value="4">Zadnjih 12 mjeseci</option>
																							</select>
																						</div>
																					</div>
																				</div>
																				<div class="col-md-6">
																					<div class="row">
																					<div class="col-md-3"></div>
																					<div class="col-md-9" style="width: 50%">
																						<canvas id="activeUsersChart"></canvas>
																					</div>
																					</div>
																				</div>
																			</div>
																			<div class="row">
																				<div class="col-md-6">
																					<div class="row">
																						<div class="col-md-9">
																							<canvas id="dailyActivityChart"></canvas>
																						</div>
																						<div class="col-md-3">
																							<select class="selectpicker" id="dailyActivitySelect">
																								<option selected disabled>Filter</option>
																								<option value="1">Zadnjih 7 dana</option>
																								<option value="2">Zadnjih 30 dana</option>
																								<option value="3">Zadnji kvartal</option>
																								<option value="4">Zadnjih 12 mjeseci</option>
																							</select>
																						</div>
																					</div>
																				</div>
																			</div>
																			<script>
																				//-- REGISTERED USERS --//
																				const ctx4 = document.getElementById('registeredUsersChart');
																				$("#registeredUsersSelect").change(function(){
																					var filter = $(this).val();
																					const data = fetch("data.php?request=graph_registered_users&filter=" + $(this).val()).then(response => response.json()).then(data => {
																						const result = [];
																						if(filter == 1){
																							const labels = getLast7Days();	
																							const fetchData = data;
																							labels.forEach(label => {
																								const value = fetchData.find(obj => obj[label]);
																								result.push(value ? value[label] : 0);
																							});
																							const chartData = defineData(labels, result);
																							new Chart(ctx4, {
																								type: 'line',
																								data: chartData
																							});	
																						} else if (filter == 2){
																							const labels = getLast30Days();	
																							const fetchData = data;
																							labels.forEach(label => {
																								const value = fetchData.find(obj => obj[label]);
																								result.push(value ? value[label] : 0);
																							});

																							const chartData = defineData(labels, result);
																							new Chart(ctx4, {
																								type: 'line',
																								data: chartData
																							});	
																						} else if (filter == 3){
																							const labels = getLastQuarter();	
																							const fetchData = [data[0].registered_users];
																							const chartData = defineData(labels, fetchData);
																							new Chart(ctx4, {
																								type: 'line',
																								data: chartData
																							});	

																						} else if (filter == 4){
																							const labels = getLast12Months();	
																							const fetchData = data;
																							labels.forEach(label => {
																								const value = fetchData.find(obj => obj[label]);
																								result.push(value ? value[label] : 0);
																							});
																							const chartData = defineData(labels, result);
																							new Chart(ctx4, {
																								type: 'line',
																								data: chartData
																							});	
																						}

																					});
																				});
																				$(document).ready(function() {
																					var last12Months = getLast12Months();
																					const defaultGraph = fetch('data.php?request=graph_registered_users&filter=4').then(response => response.json()).then(data => {
																						const result = [];
																						last12Months.forEach(label => {
																							const value = data.find(obj => obj[label]);
																							result.push(value ? value[label] : 0);
																						});
																						const chartData = defineData(last12Months, result);
																						new Chart(ctx4, {
																							type: 'line',
																							data: chartData
																						});	
																					});
																				});

																				//-- ACTIVE/INACTIVE USERS --//
																				const ctx5 = document.getElementById('activeUsersChart');
																				const activeUsersData = fetch('data.php?request=pie_current_active_users').then(response => response.json()).then(data => {
																					new Chart(ctx5, {
																						type: 'pie',
																						data: {
																							labels: ['Active', 'Inactive'],
																							datasets: [{
																								label: 'Active users',
																								data: [data.active_users, data.inactive_users],
																								backgroundColor: [
																									'rgb(75, 192, 192)',
																									'rgb(255, 99, 132)'
																								],
																							}]
																						},
																						options: {
																							responsive: true,
																							plugins: {
																								legend: {
																									position: 'top',
																									align: 'center'
																								},
																								title: {
																									display: true,
																									text: 'Active users'
																								}
																							}
																						}
																					});
																				});

																				//-- DAILY ACTIVITY --//
																				const ctx6 = document.getElementById('dailyActivityChart');
																				$("#dailyActivitySelect").change(function(){
																					var filter = $(this).val();
																					const data = fetch("data.php?request=graph_active_daily_users&filter=" + $(this).val()).then(response => response.json()).then(data => {
																						const result = [];
																						if(filter == 1){
																							const labels = getLast7Days();	
																							const fetchData = data;
																							labels.forEach(label => {
																								const value = fetchData.find(obj => obj[label]);
																								result.push(value ? value[label] : 0);
																							});
																							const chartData = defineActivityData(labels, result);
																							new Chart(ctx6, {
																								type: 'line',
																								data: chartData
																							});	
																						} else if (filter == 2){
																							const labels = getLast30Days();	
																							const fetchData = data;
																							labels.forEach(label => {
																								const value = fetchData.find(obj => obj[label]);
																								result.push(value ? value[label] : 0);
																							});

																							const chartData = defineActivityData(labels, result);
																							new Chart(ctx6, {
																								type: 'line',
																								data: chartData
																							});	
																						} else if (filter == 3){
																							const labels = getLastQuarter();	
																							const fetchData = [data[0].daily_activity];
																							const chartData = defineActivityData(labels, fetchData);
																							new Chart(ctx6, {
																								type: 'line',
																								data: chartData
																							});	

																						} else if (filter == 4){
																							const labels = getLast12Months();	
																							const fetchData = data;
																							labels.forEach(label => {
																								const value = fetchData.find(obj => obj[label]);
																								result.push(value ? value[label] : 0);
																							});
																							const chartData = defineActivityData(labels, result);
																							new Chart(ctx6, {
																								type: 'line',
																								data: chartData
																							});	
																						}
																					});
																				});

																				$(document).ready(function() {
																					var last12Months = getLast12Months();
																					const defaultGraph = fetch('data.php?request=graph_active_daily_users&filter=4').then(response => response.json()).then(data => {
																						const result = [];
																						last12Months.forEach(label => {
																							const value = data.find(obj => obj[label]);
																							result.push(value ? value[label] : 0);
																						});
																						const chartData = defineActivityData(last12Months, result);
																						new Chart(ctx6, {
																							type: 'line',
																							data: chartData
																						});	
																					});
																				});



																				// -- FUNCTIONS -- //
																				function defineData(lables, data){
																					const chartData = {
																					labels: lables,
																						datasets: [{
																							label: 'Registered users',
																							data: data,
																							fill: false,
																							borderColor: 'rgb(75, 192, 192)',
																							tension: 0.1
																						}]
																					};

																					return chartData;
																				};
																				function defineActivityData(lables, data){
																					const chartData = {
																					labels: lables,
																						datasets: [{
																							label: 'Daily active users',
																							data: data,
																							fill: false,
																							borderColor: 'rgb(75, 192, 192)',
																							tension: 0.1
																						}]
																					};

																					return chartData;
																				};
																				function getLast12Months() {
																					var months = [];
																					var today = new Date();

																					for (var i = 0; i < 12; i++) {
																						var month = today.getMonth() - i + 1;
																						var year = today.getFullYear();

																						if (month <= 0) {
																							month += 12;
																							year--;
																						}

																						var monthString = month < 10 ? "0" + month : "" + month;
																						var yearString = year.toString().slice(-2);
																						months.push(monthString + "/" + yearString);
																					}

																					return months.reverse();
																				}
																				function getLast7Days() {
																					var days = [];
																					var today = new Date();

																					for (var i = 0; i < 7; i++) {
																						var day = new Date(today);
																						day.setDate(today.getDate() - i);
																						var month = day.getMonth() + 1; 
																						var dayOfMonth = day.getDate();
																						var year = day.getFullYear().toString().slice(-2); 
																						var formattedDate = (dayOfMonth < 10 ? '0' : '') + dayOfMonth + '/' + (month < 10 ? '0' : '') + month + '/' + year;
																						days.push(formattedDate);
																					}

																					return days.reverse(); 
																				}

																				function getLast30Days() {
																					var days = [];
																					var today = new Date();

																					for (var i = 0; i < 30; i++) {
																						var day = new Date(today);
																						day.setDate(today.getDate() - i);
																						var month = day.getMonth() + 1; 
																						var dayOfMonth = day.getDate();
																						var year = day.getFullYear().toString().slice(-2);
																						var formattedDate = (dayOfMonth < 10 ? '0' : '') + dayOfMonth + '/' + (month < 10 ? '0' : '') + month + '/' + year;
																						days.push(formattedDate);
																					}

																					return days.reverse();
																				}
																				function getLastQuarter() {
																					var quarters = [];
																					var today = new Date();
																					var currentYear = today.getFullYear();
																					var currentMonth = today.getMonth() + 1; 
																					var currentQuarter = Math.floor((currentMonth - 1) / 3);

																					var previousQuarter = currentQuarter - 1;
																					if (previousQuarter < 0) {
																						previousQuarter = 3; 
																						currentYear--;
																					}

																					var quarterStartMonth = (previousQuarter * 3) + 1;
																					var quarterEndMonth = quarterStartMonth + 2;
																					var quarterLabel = "Q" + (previousQuarter + 1);
																					var quarter = quarterLabel + " " + currentYear;
																					quarters.push(quarter);

																					return quarters;
																				}
																			</script>
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
                                </div>
                            </div>
                        </div>
					</div>
				</div>
			</div>
			<footer><?php getCopyright(); ?></footer>
		</div>
	</div>
</body>

</html>
<?php } else {
		echo '
				<br/>
				<div class="alert material-alert material-alert_danger">
					<h4>NEMATE PRIVILEGIJE!</h4>
					<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
					<br />
				</div>
';
	} ?>