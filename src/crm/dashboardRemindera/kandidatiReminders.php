<?php
include($_SERVER["DOCUMENT_ROOT"] . "/includes/functions.php");
include($_SERVER["DOCUMENT_ROOT"] . "/includes/common.php");
include("utils.php");

$getEmployeeStatus = explode(',', getEmployeeStatus());
/*
	Zaposlenici koji vide tipove u filteru dodatne stvari
	Ako se ovjde mijenja - potrebno podesiti na file-u također:
		- companiesReminders.php
		- naloziReminders.php 

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
				<h1>
					<i class="fa fa-briefcase idk_color_green" aria-hidden="true"></i>
					 Dashboard remindera - kandidati
				</h1>
				</div>
			</div>
			<hr />
			<div class="content_box idk_margin_top20">
			<div style="text-align:center">
			<h1>
				<?php 
					if(isset($_REQUEST["companyId"]))
					{
						echo getCompanyNameById($_REQUEST["companyId"]);
					}

					if(isset($_REQUEST["nalogId"]))
					{
						echo "<br />";
						echo "Nalog: ";
						echo getNazivNaloga($_REQUEST["nalogId"]);
					}

					if(isset($_REQUEST["poslanPuta"]))
					{
						echo "<br />";
						echo "Level: ";
						echo $_REQUEST["poslanPuta"];
					}

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

			fillNaloziTable(query_type, query_visibility, query_status);

			makeRowsExpandable();

			//initTypeSelector();

			$("#reminder_select").on("change", reloadTable);
			$("#reminder_visibility").on("change", reloadTable);
			$("#reminder_status").on("change", reloadTable);
		});

		/***********************************
		 * 
		 * FUNKCIJE
		 * 
		***********************************/
		async function format(child, d) {
			// `d` is the original data object for the row
			const response = await fetch(`/dashboardRemindera/API.php?page=reminders&setting_id=${d.pr_reminder_setting_id}&candidate_id=${d.pr_candidate_id}&has_documents=${d.prtHasDocuments}&reminder_id=${d.prId}&reminder_level=${d.prLevel}`);
			const reminders = await response.json();

			let rows = "";
			for(let i=0; i<reminders.length; i++)
			//for (let i = reminders.length - 1; i >= 0; i--)
			{
				let r = reminders[i];

				const response1 = await fetch(`/dashboardRemindera/API.php?page=usersPUA&pua_ids=${r.pr_users_sent}&user_type=${r.prt_user_type}`);
				const users = await response1.json();
				rows += `
					<tr>
						<td>
							${reminders.length-i}
						</td>
						<td>
							${new Date(r.pr_date_sent).toISOString().split('T')[0].split("-").reverse().join(".")}
						</td>
						<td>
							${users.map(u=>u.korisnik_ime_prezime).join("<br />")}
						</td>
						<td>
							${r.reminder_status}
						</td>
						<td>
							${ 
								(r.reminder_status_datetime != null) ? new Date(r.reminder_status_datetime).toISOString().split('T')[0].split("-").reverse().join(".") : ''
							}
						</td>
						<td>
							${
								(r.reminder_user_assigned != null) ? r.reminder_user_assigned : ''
							}
						</td>
						<td>
							${r.reminder_level}
						</td>
					</tr>`;
			}


			const table = 
				'<div class="slider">' +
				'<div style="display:flex; flex-direction: column; align-items: center">' +
				'<br />' +
				'<div style="background: white; text-align:center; padding:20px; overflow:hidden; border-radius:15px; display: inline-block; box-shadow: 2px 2px 5px rgba(0,0,0,0.2)">' +
				'<h1>' + d.kandidat_ime_prezime + '</h1>' +
				'<br />' +
				'<b>Poslani reminderi</b>'+
				'<table style="min-width: 30vw">' +
				'<thead>' + 
				'<tr>' +
				'<th style="text-align:center">R. br</th>' + 
				'<th style="text-align:center">Poslano</th>' + 
				'<th style="text-align:center">Korisnicima</th>' +
				'<th style="text-align:center">Status</th>' +
				'<th style="text-align:center">Status Vrijeme</th>' +
				'<th style="text-align:center">Korisnik</th>' +
				'<th style="text-align:center">Level</th>' +
				'</tr>' +
				'</thead>' + 
				rows +
				'</table>' +
				'</div>' +
				'<br />' +
				'</div>' +
				'</div>';

				child(table, 'no-padding').show();
				$('div.slider', child()).slideDown();
		}

		function selectReminderStatusVal(){
			let reminder_status = $("#reminder_status").val();
			if(!reminder_status){
				$("#reminder_status").val(0).selectpicker("refresh");
				alert("U polju status morate odabrati barem jednu opciju!");
			}
		}
		
		function makeRowsExpandable(table)
		{
			$('#idk_table tbody').on('click', 'td.dt-control', function () {
				var tr = $(this).closest('tr');
				var row = $('#idk_table').DataTable().row(tr);
		
				if (row.child.isShown()) {
					$('div.slider', row.child()).slideUp( function () {
						row.child.hide();
						tr.removeClass('shown');
					});
					tr.children().last().children().last().css("transform", "rotateZ(0deg)");
				} else {
					//row.child(format(row.data()), 'no-padding').show();

					format(row.child,row.data());
					tr.children().last().children().last().css("transform", "rotateZ(180deg)");
					//$('div.slider', row.child()).slideDown();
				}
			});
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
			fillNaloziTable(query, query_visibility, query_status);
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

		function fillNaloziTable(reminderTypesQuery, query_visibility, reminderStatusFilter) {
			$('#idk_table').DataTable({
				"autoWidth":true,
				"ajax": {
					url: `
						/dashboardRemindera/API.php?page=kandidati&${reminderTypesQuery}&${query_visibility}&${reminderStatusFilter}
						<?php 
						if(isset($_REQUEST['companyId'])) 
							echo '&companyId='.$_REQUEST['companyId']; 
						if(isset($_REQUEST['nalogId'])) 
							echo '&nalogId='.$_REQUEST['nalogId'];
						if(isset($_REQUEST['poslanPuta'])) 
							echo '&poslanPuta='.$_REQUEST['poslanPuta']; ?>
					`,
					dataSrc: ""
				},
				"columns": [
					{
						"data": "kandidat_id",
						"title": "ID kandidata",
						"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
							$(nTd).html(`<a href=/kandidati?page=open&id=${oData.kandidat_id}>${oData.kandidat_id}</a>`);
						},
					},
					{
						"data": "kandidat_ime_prezime",
						"title": "Ime i prezime",
						"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
							$(nTd).html(`<a href=/kandidati?page=open&id=${oData.kandidat_id}>${oData.kandidat_ime_prezime}</a>`);
						},
					},
					<?php
					if(!isset($_REQUEST['nalogId']))
					{
						echo '
							{
								"data": "nalog_naziv",
								"title": "Nalog"
							},
						';
					}
					?>
					{
						"data": "prt_name",
						"title": "Tip remindera"
					},
					{
						"data": "reminderStatus",
						"title": "Status",
						"class": "text-center"
					},
					{
						"data": "reminderLevel",
						"title": "Level",
						"class": "text-center"
					},
					{
						className: 'dt-control',
						orderable: false,
						data: null,
						defaultContent: '<i style="cursor: pointer; transition:all 0.5s;" class="fa fa-arrow-down idk_color_green"></i>',
						width: "1%"
					}
				],
				responsive: true
			});
		}
	</script>
	<style>
		.slider{
			display:none;
		}
		table.dataTable tbody td.no-padding {
			padding: 0;
		}
	</style>
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
