<?php
include($_SERVER["DOCUMENT_ROOT"] . "/includes/functions.php");
include($_SERVER["DOCUMENT_ROOT"] . "/includes/common.php");

$getEmployeeStatus = explode(',', getEmployeeStatus());

/*
	Zaposlenici koji vide tipove u filteru dodatne stvari
	Ako se ovjde mijenja - potrebno podesiti na file-u također:
		- naloziReminders.php
		- kandidatiReminders.php 

	|	|	|	|	|	|	|	|	|	|	|
	V	V	V	V	V	V	V	V	V	V	V
*/
$flagEmployeeView = 0;
if (getModulePermission(3)) {
	$flagEmployeeView = 1;
}

?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php getTitle(); ?></title>

	<?php
	include($_SERVER["DOCUMENT_ROOT"] . "/includes/head.php");
	if (in_array($getUserIp, $getIpWhiteList)) {
	?>
		<!-- CK Editor ---------------------------------------------------------------------------------------->
		<script src="<?php getSiteURL(); ?>ckeditor/ckeditor.js" async></script>
		<script src="./palette.js"></script>
</head>

<body>
	<header>
		<?php
		include($_SERVER["DOCUMENT_ROOT"] . "/header.php");
		?>
	</header>
	<div id="sidebar">
		<?php
		include($_SERVER["DOCUMENT_ROOT"] . "/menu.php");
		?>
	</div>
	<div id="content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-briefcase idk_color_green" aria-hidden="true"></i> Dashboard remindera</h1>
				</div>
			</div>
			<hr />
			<div class="content_box idk_margin_top20">
				<div>
					<div style="text-align:center">
						<h1>
							Kompanije
						</h1>
					</div>
					<div class="row" style="margin: 30px 0px 30px 0px;">
						<div style="margin: 30px 0px 30px 0px;">
							<div class="col-xs-4">
								<select class="selectpicker" id="reminder_select" multiple>
								</select>
							</div>
						</div>
						<div style="margin: 30px 0px 30px 0px;">
							<div class="col-xs-4">
								<select class="selectpicker" id="reminder_visibility" title="Ništa izabrano">
									<?php 
										if ($flagEmployeeView == 1) {
											?>
												<option value="0" selected>Svi reminderi</option>
												<option value="1">Moji reminderi</option>
											<?php 
										} else {
											?>
												<option value="1" selected>Moji reminderi</option>
											<?php 
										}
									?>
									<option value="2">Jobsoft reminderi</option>
									<?php 
										if ($flagEmployeeView == 1) {
											?>
												<option value="3">Reminderi zaposlenika</option>
											<?php 
										}
									?>
								</select>
							</div>
						</div>
						<div style="margin: 30px 0px 30px 0px;">
							<div class="col-xs-4">
								<select class="selectpicker" id="reminder_status" title="Ništa izabrano" multiple>
									
									<option value="0" selected>Aktivni</option>
									<option value="1">Završeni (Normalno)</option>
									<option value="2">Završeni (Odustankom)</option>
												
								</select>
							</div>
						</div>
					</div>
					<table id="idk_table" class="display" cellspacing="0" width="100%">
						<thead>
							<tr>
								<th>Ime Kompanije</th>
								<th>Level 1</th>
								<th>Level 2</th>
								<th>Level 3</th>
								<th>Level 4</th>
								<th>Level 5+</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
				<hr />
				<canvas id="grafStatusa" height="80px"></canvas>
			</div>
			<footer><?php getCopyright(); ?></footer>
		</div>
	</div>
	<script>
		$(function() {
			selectReminderStatusVal();
			let reminder_visibility = parseInt($("#reminder_visibility").val());
			let reminder_status = $("#reminder_status").val();
			let query_visibility = "visibility=0";
			if(reminder_visibility)
			{
				if(reminder_visibility == 1){
					query_visibility = `visibility=${reminder_visibility}&employee_id=${<?php echo $logged_employee_id;?>}`;
				}else if(reminder_visibility == 2){
					query_visibility = `visibility=${reminder_visibility}`;
				}else if(reminder_visibility == 3){
					query_visibility = `visibility=${reminder_visibility}`;
				}
			}
			let query_status = "";
			if(reminder_status)
			{
				query_status = reminder_status.map(el1 => `status[]=${el1}`).join("&");
			}

			fetch(`/dashboardRemindera/API.php?page=allCompanies&${query_visibility}&${query_status}`).then(res => res.json()).then(data => {
				initChart(data);
				fillCompaniesTable(data,'',query_status);
			});

			initTypeSelector();
			$("#reminder_select").on("change", reloadTable);
			$("#reminder_visibility").on("change", reloadTable);
			$("#reminder_status").on("change", reloadTable);
		});

		function selectReminderStatusVal(){
			let reminder_status = $("#reminder_status").val();
			if(!reminder_status){
				$("#reminder_status").val(0).selectpicker("refresh");
				alert("U polju status morate odabrati barem jednu opciju!");
			}
		}

		function reloadTable(){
			selectReminderStatusVal();
			let reminder_types = $("#reminder_select").val();
			let reminder_visibility = $("#reminder_visibility").val();
			let reminder_status = $("#reminder_status").val();

			let query = "";
			if(reminder_types)
			{
				query = reminder_types.map(el => `types[]=${el}`).join("&");
			}

			let query_visibility = "visibility=0";
			if(reminder_visibility)
			{
				if(reminder_visibility == 1){
					query_visibility = `visibility=${reminder_visibility}&employee_id=${<?php echo $logged_employee_id;?>}`;
				}else if(reminder_visibility == 2){
					query_visibility = `visibility=${reminder_visibility}`;
				}else if(reminder_visibility == 3){
					query_visibility = `visibility=${reminder_visibility}`;
				}
			}

			let query_status = "";
			if(reminder_status)
			{
				query_status = reminder_status.map(el1 => `status[]=${el1}`).join("&");
			}
			
			$.get(`/dashboardRemindera/API.php?page=allCompanies&${query}&${query_visibility}&${query_status}`, (data)=>
				{
					$("#idk_table").DataTable().destroy();
					initChart(JSON.parse(data));
					fillCompaniesTable(JSON.parse(data), query, query_status);
				}
			)
		}

		function initTypeSelector()
		{
			fetch("/dashboardRemindera/API.php?page=reminderTypes").then(res => res.json()).then(data => {
				data.forEach(type => {
					$('#reminder_select').append($('<option>', {
						value: type.prt_id,
						text: type.prt_name
					}));				
				});
				$('#reminder_select').selectpicker('refresh');
			});
		}

		function initChart(podaci) {
			let colors = palette('tol', podaci.length).map((hex) => {
				return '#' + hex;
			});

			const labels = [
				'Level 1',
				'Level 2',
				'Level 3',
				'Level 4',
				'Level 5+'
			];

			const data = {
				labels: labels,
				datasets: podaci.map((podatak, index) => {
					return {
						label: podatak.company_name,
						data: [
							podatak.level1,
							podatak.level2,
							podatak.level3,
							podatak.level4,
							podatak.level5plus
						],
						borderColor: colors[index],
						fill: false
					};
				})
			};

			const config = {
				type: 'line',
				data: data,
				options: {}
			};
			const myChart = new Chart(
				document.getElementById('grafStatusa'),
				config
			);
		}

		function fillCompaniesTable(podaci, reminderTypeFilter, reminderStatusFilter) {
			let reminderTypeFilterData = '';
			if(reminderTypeFilter != ''){
				reminderTypeFilterData = `&${reminderTypeFilter}`
			}
			let reminderStatusFilterData = '';
			if(reminderStatusFilter != ''){
				reminderStatusFilterData = `&${reminderStatusFilter}`
			}
			$('#idk_table').DataTable({
				"data": podaci,
				"columns": [{
						"data": "company_name",
						"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
							$(nTd).html(`<a href=/dashboardRemindera/naloziReminders.php?companyId=${oData.company_id}&visibility=${oData.visibility}${reminderTypeFilterData}${reminderStatusFilterData}>${oData.company_name}</a>`);
						}
					},
					{
						"data": "level1",
						"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
							$(nTd).html(`<a href=/dashboardRemindera/kandidatiReminders.php?companyId=${oData.company_id}&poslanPuta=1&visibility=${oData.visibility}${reminderTypeFilterData}${reminderStatusFilterData}>${oData.level1}</a>`);
						}
					},
					{
						"data": "level2",
						"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
							$(nTd).html(`<a href=/dashboardRemindera/kandidatiReminders.php?companyId=${oData.company_id}&poslanPuta=2&visibility=${oData.visibility}${reminderTypeFilterData}${reminderStatusFilterData}>${oData.level2}</a>`);
						}
					},
					{
						"data": "level3",
						"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
							$(nTd).html(`<a href=/dashboardRemindera/kandidatiReminders.php?companyId=${oData.company_id}&poslanPuta=3&visibility=${oData.visibility}${reminderTypeFilterData}${reminderStatusFilterData}>${oData.level3}</a>`);
						}
					},
					{
						"data": "level4",
						"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
							$(nTd).html(`<a href=/dashboardRemindera/kandidatiReminders.php?companyId=${oData.company_id}&poslanPuta=4&visibility=${oData.visibility}${reminderTypeFilterData}${reminderStatusFilterData}>${oData.level4}</a>`);
						}
					},
					{
						"data": "level5plus",
						"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
							$(nTd).html(`<a href=/dashboardRemindera/kandidatiReminders.php?companyId=${oData.company_id}&poslanPuta=5&visibility=${oData.visibility}${reminderTypeFilterData}${reminderStatusFilterData}>${oData.level5plus}</a>`);
						}
					},
				],
				responsive: true
			});
		}
	</script>
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
