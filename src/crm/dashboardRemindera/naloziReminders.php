<?php
include($_SERVER["DOCUMENT_ROOT"] . "/includes/functions.php");
include($_SERVER["DOCUMENT_ROOT"] . "/includes/common.php");
include("utils.php");

$getEmployeeStatus = explode(',', getEmployeeStatus());
if (!isset($_REQUEST["companyId"])) {
	header("Location: /dashboardRemindera/companiesReminders.php");
}
$id = $_REQUEST["companyId"];
/*
	Zaposlenici koji vide tipove u filteru dodatne stvari
	Ako se ovjde mijenja - potrebno podesiti na file-u također:
		- companiesReminders.php
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
				<h1><i class="fa fa-briefcase idk_color_green" aria-hidden="true"></i> Dashboard remindera po nalozima</h1>
				</div>
			</div>
			<hr />
			<div class="content_box idk_margin_top20">
				<div style="text-align:center">
					<h1>
						<?php 
							echo getCompanyNameById($_REQUEST["companyId"]);

							if(isset($_REQUEST["visibility"])){
								$visibility = $_REQUEST["visibility"];
							} else {
								$visibility = (($flagEmployeeView == 1) ? 0 : 1);
							}

							$typesFilter = array();
							if(isset($_REQUEST["types"])){
								$typesFilter = $_REQUEST["types"];
							}

							$statusFilter = array();
							if(isset($_REQUEST["status"])){
								$statusFilter = $_REQUEST["status"];
							}
						?>
					</h1>
				</div>
				<div class="row" style="margin: 30px 0px 30px 0px;">
					<div style="margin: 30px 0px 30px 0px;">
						<div class="col-xs-4">
							<select class="selectpicker" id="reminder_select" multiple>
								<?php 
									$query_reminder_type = $db->prepare("
										SELECT prt_id, prt_name FROM idk_pp_reminder_types
									");
									$query_reminder_type->execute();
									while($row_reminder_type = $query_reminder_type->fetch()){
										echo '<option '.((in_array($row_reminder_type["prt_id"],$typesFilter)) ? "selected": "").' value="'.$row_reminder_type["prt_id"].'">'.$row_reminder_type["prt_name"].'</option>';
									}
								?>
							</select>
						</div>
					</div>
					<div style="margin: 30px 0px 30px 0px;">
						<div class="col-xs-4">
							<select class="selectpicker" id="reminder_visibility">
								<?php 
									if ($flagEmployeeView == 1) {
										?>
											<option <?php echo (($visibility == 0) ? 'selected' : ''); ?> value="0">Svi reminderi</option>
											<option <?php echo (($visibility == 1) ? 'selected' : ''); ?> value="1">Moji reminderi</option>
										<?php 
									} else {
										?>
											<option <?php echo (($visibility == 1) ? 'selected' : ''); ?> value="1">Moji reminderi</option>
										<?php 
									}
								?>
								<option <?php echo (($visibility == 2) ? 'selected' : ''); ?> value="2">Jobsoft reminderi</option>
								<?php 
									if ($flagEmployeeView == 1) {
										?>
											<option <?php echo (($visibility == 3) ? 'selected' : ''); ?> value="3">Reminderi zaposlenika</option>
										<?php 
									}
								?>
							</select>
						</div>
					</div>
					<div style="margin: 30px 0px 30px 0px;">
						<div class="col-xs-4">
							<select class="selectpicker" id="reminder_status" title="Ništa izabrano" multiple>
								
								<option value="0" <?php echo (in_array("0",$statusFilter)) ? "selected": ""; ?>>Aktivni</option>
								<option value="1" <?php echo (in_array("1",$statusFilter)) ? "selected": ""; ?>>Završeni (Normalno)</option>
								<option value="2" <?php echo (in_array("2",$statusFilter)) ? "selected": ""; ?>>Završeni (Odustankom)</option>
											
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
			<footer><?php getCopyright(); ?></footer>
		</div>
	</div>
	<script>
		$(function() {
			selectReminderStatusVal();
			var reminder_types_d = $("#reminder_select").val();
			var query_type = "";
			if(reminder_types_d)
			{
				query_type = reminder_types_d.map(el => `types[]=${el}`).join("&");
			}

			let reminder_visibility = parseInt($("#reminder_visibility").val());
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

			let reminder_status = $("#reminder_status").val();
			let query_status = "";
			if(reminder_status)
			{
				query_status = reminder_status.map(el1 => `status[]=${el1}`).join("&");
			}
			
			fillNaloziTable(query_type, query_visibility,query_status);
			
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

			let reminder_status = $("#reminder_status").val();
			let query_status = "";
			if(reminder_status)
			{
				query_status = reminder_status.map(el1 => `status[]=${el1}`).join("&");
			}

			$("#idk_table").DataTable().destroy();
			fillNaloziTable(query, query_visibility,query_status);
		}

		function initTypeSelector(selected_types)
		{
			fetch("/dashboardRemindera/API.php?page=reminderTypes").then(res => res.json()).then(data => {
				data.forEach(type => {
					/*$('#reminder_select').append($('<option>', {
						value: type.prt_id,
						text: type.prt_name
					}));*/
					$('#reminder_select').append(`<option ${(selected_types.includes(type.prt_id)) ? "selected": ""} value="${type.prt_id}">${type.prt_name}</option>`);
				});
				$('#reminder_select').selectpicker('refresh');
			});
		}

		function fillNaloziTable(reminderTypesQuery, query_visibility,reminderStatusFilter) {
			//console.log("Funkcija:"+reminderTypesQuery);
			let reminderTypeFilterData = '';
			if(reminderTypesQuery != ''){
				reminderTypeFilterData = `&${reminderTypesQuery}`
			}
			let reminderStatusFilterData = '';
			if(reminderStatusFilter != ''){
				reminderStatusFilterData = `&${reminderStatusFilter}`
			}
			$('#idk_table').DataTable({
				"ajax": {
					url: `/dashboardRemindera/API.php?page=company&${reminderTypesQuery}&${query_visibility}&${reminderStatusFilter}&companyId=<?php echo $id; ?>`,
					dataSrc: ""
				},
				"columns": [{
						"data": "nalog_naziv",
						"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
							$(nTd).html(`<a href=/dashboardRemindera/kandidatiReminders?nalogId=${oData.nalog_id}&visibility=${oData.visibility}${reminderTypeFilterData}${reminderStatusFilterData}>${oData.nalog_naziv}</a>`);
						}
					},
					{
						"data": "level1",
						"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
							$(nTd).html(`<a href=/dashboardRemindera/kandidatiReminders.php?nalogId=${oData.nalog_id}&poslanPuta=1&visibility=${oData.visibility}${reminderTypeFilterData}${reminderStatusFilterData}>${oData.level1}</a>`);
						}
					},
					{
						"data": "level2",
						"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
							$(nTd).html(`<a href=/dashboardRemindera/kandidatiReminders.php?nalogId=${oData.nalog_id}&poslanPuta=2&visibility=${oData.visibility}${reminderTypeFilterData}${reminderStatusFilterData}>${oData.level2}</a>`);
						}
					},
					{
						"data": "level3",
						"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
							$(nTd).html(`<a href=/dashboardRemindera/kandidatiReminders.php?nalogId=${oData.nalog_id}&poslanPuta=3&visibility=${oData.visibility}${reminderTypeFilterData}${reminderStatusFilterData}>${oData.level3}</a>`);
						}
					},
					{
						"data": "level4",
						"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
							$(nTd).html(`<a href=/dashboardRemindera/kandidatiReminders.php?nalogId=${oData.nalog_id}&poslanPuta=4&visibility=${oData.visibility}${reminderTypeFilterData}${reminderStatusFilterData}>${oData.level4}</a>`);
						}
					},
					{
						"data": "level5plus",
						"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
							$(nTd).html(`<a href=/dashboardRemindera/kandidatiReminders.php?nalogId=${oData.nalog_id}&poslanPuta=5&visibility=${oData.visibility}${reminderTypeFilterData}${reminderStatusFilterData}>${oData.level5plus}</a>`);
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
