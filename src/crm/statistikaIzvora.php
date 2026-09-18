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
	<title>Statistika izvora | <?php getTitle(); ?></title>

	<?php
	include($_SERVER["DOCUMENT_ROOT"] . "/includes/head.php");
	if (in_array($getUserIp, $getIpWhiteList)) {
	?>
		<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
		<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
		<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
		<script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
		<script src="/js/jquery.table2excel.js"></script>
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
					<h1><i class="fa fa-briefcase idk_color_green" aria-hidden="true"></i> Statistika izvora</h1>
				</div>
			</div>
			<hr />
			<div id="statistika_izvora_container" class="content_box idk_margin_top20">



				<div class="row" style="display: flex; justify-content:center">
					<div class="col-md-6">
						<div class="panel panel-default">
							<div class="panel-heading">
								Filter
							</div>
							<div class="panel-body">
								<div style="display:flex; justify-content:center;">
									<div style="display:flex; flex-direction:column; flex: 1; padding:5px">
										<label for="fromTo">Vremenski period</label>
										<input type="text" class="form-control" id="fromTo" name="fromTo" placeholder="Datum" style="padding:17px; border-radius:0;" required>
									</div>
									<div style="display:flex; flex-direction:column; flex:1; padding:5px">
										<label for="drzave_select">Države</label>
										<select name="drzave_select" class="selectpicker" id="drzave_select" multiple>
											<option value="BiH">BiH</option>
											<option value="Srbija">Srbija</option>
											<option value="Njemacka">Njemačka</option>
										</select>
									</div>
									<div style="display:flex; flex-direction:column; flex:1; padding:5px">
										<label for="kampanje_select">Kampanje</label>
										<select style="max-width: 200px !important" name="kampanje_select" class="selectpicker" data-live-search="true" data-actions-box=true id="kampanje_select" multiple>
										</select>
									</div>
								</div>
								<div style="display:flex; justify-content:space-between; margin-top: 20px" id="buttons">
									<button class="btn material-btn material-btn_success" id="applyFilterButton">Primijeni filter</button>
									<button class="btn material-btn material-btn_success" id="procentiToggleButton">Prikaži procente</button>
								</div>
							</div>
						</div>
					</div>
				</div>


				<!--  

						DATATABLE

				-->
				<div>
					<h1 style="text-align:center" id="loading"></h1>
					<table id="idk_table" class="display" cellspacing="0">
						<tbody>
						</tbody>
					</table>
				</div>

				<hr />
			</div>
			<footer><?php getCopyright(); ?></footer>
		</div>
	</div>
	<style>
		.bootstrap-select {
			max-width: 300px !important;
		}

		.text {
			white-space: pre-wrap;
		}

		#buttons>* {
			flex: 1;
			margin-left: 5px;
			margin-right: 5px;
		}
	</style>
	<script>
		/******    GLOBALS    ******/
		let columns = [];

		let kampanje = [];
		let drzave = [];

		const today = new Date();
		const oneMonthAgo = new Date(today.getFullYear(), today.getMonth() - 1, today.getDate());

		let from = oneMonthAgo.toLocaleDateString('en-GB').split("/").reverse().join("-");
		let to = today.toLocaleDateString('en-GB').split("/").reverse().join("-");


		let result;

		let procenti = false;


		/******    ON LOAD    ******/
		$(function() {

			initCountrySelect();

			initDateSelect();

			loadKampanjePicker();

			fetchDataAndLoadTables();

			$("#applyFilterButton").on("click", fetchDataAndLoadTables);

			$("#procentiToggleButton").on("click", togglePercentage);

		});


		function togglePercentage() {
			procenti = !procenti;

			if (procenti) {
				extractColumnsPercentage(result);
				$("#procentiToggleButton").text("Prikaži brojeve");
			} else {
				extractColumns(result);
				$("#procentiToggleButton").text("Prikaži procente");
			}

			destroyTable();

			loadTable(result);

		}



		/******    FUNKCIJE    ******/

		function initDateSelect() {
			$("#fromTo").flatpickr({
				mode: "range",
				dateFormat: "Y-m-d",
				defaultDate: [today, oneMonthAgo],
				onChange: (selectedDates, dateStr, instance) => {
					if (dateStr.includes("to")) {
						let dates = dateStr.split(" to ");
						from = dates[0];
						to = dates[1];

					}
				}
			});
		}

		function initCountrySelect() {
			$('#drzave_select').on("change", (e) => {
				drzave = $('#drzave_select').val();
				loadKampanjePicker();
				console.log(result);
			});
		}

		function fetchDataAndLoadTables() {

			destroyTable();

			$("#applyFilterButton").prop('disabled', true);
			$("#procentiToggleButton").prop("disabled", true);
			$("#loading").text("Loading...");

			$.ajax({
				url: createUrlFromFilter(),
				success: function(data) {
					let podaci = JSON.parse(data);
					result = podaci;
					if (podaci.length > 0) {
						if (procenti) {
							extractColumnsPercentage(podaci);
						} else {
							extractColumns(podaci);
						}

						loadTable(podaci);

						$("#loading").text("");
					} else {
						$("#loading").text("Nema podataka za odabrani filter!");
						$("#applyFilterButton").prop('disabled', false);
					}
				}
			});

		}

		function createUrlFromFilter() {
			let url = `/do_dipl.php?form=statistika_izvor_lead&from=${from}&to=${to}&detaljno`;

			let drzaveQuery = "";
			if (drzave) {
				drzaveQuery = drzave.map(el => `drzave[]="${el}"`).join("&");
				url += `&${drzaveQuery}`;
			}
			let kampanjeQuery = "";
			if (kampanje) {
				kampanjeQuery = kampanje.map(el => `kampanje[]="${el}"`).join("&");
				url += `&${kampanjeQuery}`;
			}

			return url;
		}

		function extractColumns(podaci) {
			columns = [];
			let columnNames = Object.keys(podaci[0]);
			for (var i in columnNames) {

				columns.push({
					data: columnNames[i],
					title: formatColumnName(columnNames[i]),
					visible: (columnNames[i].startsWith("ARH") || columnNames[i].startsWith("NEZ") || columnNames[i].startsWith("termin_")) ? false : true,
					render: function(data, type, row, meta) {
						if (!data)
							return 0;

						if (type === "exportxls") {
							return data;
						}
						if (meta.col === 1) {
							return `<a target="_blank" href="/kampanje?page=open&id=${row.ID}">${data}</a>`;
						}

						return data;
					},

				});

			}
		}

		function extractColumnsPercentage(podaci) {
			columns = [];
			let columnNames = Object.keys(podaci[0]);
			for (var i in columnNames) {

				if (!["ID", "Naziv", "leadovi"].includes(columnNames[i])) {
					columns.push({
						data: columnNames[i],
						title: formatColumnName(columnNames[i]),
						visible: (columnNames[i].startsWith("ARH") || columnNames[i].startsWith("NEZ") || columnNames[i].startsWith("termin_")) ? false : true,
						render: function(data, type, row, meta) {
							if (type === "exportxls") {
								if (!data)
									return 0;

								return data;
							}

							if (meta.col === 1) {
								return `<a target="_blank" href="/kampanje?page=open&id=${row.ID}">${data}</a>`;
							}
							if (!data)
								return "0.00%";
							return ((parseInt(data) / parseInt(row.leadovi)) * 100).toFixed(2) + "%";
						},
					});
				} else {
					columns.push({
						data: columnNames[i],
						title: formatColumnName(columnNames[i]),
						visible: (columnNames[i].startsWith("ARH") || columnNames[i].startsWith("NEZ") || columnNames[i].startsWith("termin_")) ? false : true,
						render: function(data, type, row, meta) {
							if (!data)
								return 0;

							if (type === "exportxls") {
								return data;
							}
							if (meta.col === 1) {
								return `<a target="_blank" href="/kampanje?page=open&id=${row.ID}">${data}</a>`;
							}
							return data;
						},
					});

				}
			}
		}


		function loadKampanjePicker() {
			$('#kampanje_select').empty();

			$('#kampanje_select').on("change", (e) => {
				kampanje = $('#kampanje_select').val();
			});


			$.ajax({
				url: "/do_dipl?form=getKampanje" + (drzave ? "&" + drzave.map(el => `drzave[]="${el}"`).join("&") : ""),
				success: function(data) {
					let podaci = JSON.parse(data);
					podaci.forEach(kampanja => {
						$('#kampanje_select').append($('<option>', {
							value: kampanja.kd_id,
							text: kampanja.kd_naziv
						}));
					});
					$('#kampanje_select').selectpicker("refresh");
					kampanje = [];
				}
			});


		}

		function loadTable(podaci) {
			$('#idk_table').DataTable({
				data: podaci,
				columns: columns,
				buttons: [{
					extend: 'excel',
					className: 'btn material-btn material-btn_primary',
					text: "Preuzmi detaljnu Excel tabelu",
					titleAttr: "Preuzmi detaljnu Excel tabelu koja sadrži sve razloge zbog kojih je lead arhiviran ili nezainteresiran",
					exportOptions: {
						orthogonal: "exportxls"
					}
				}],
				initComplete: () => {
					$("#loading").text("");
					$("#applyFilterButton").prop('disabled', false);
					$("#procentiToggleButton").prop("disabled", false);
					$("#idk_table").DataTable().buttons().containers().appendTo('#buttons');
				}
			});
		}

		function formatColumnName(str) {
			str = str.replaceAll("_", " ");
			str = str.replaceAll("NEZ", " ");
			str = str.replaceAll("ARH", " ");

			const nums = /[0123456789]/ig;
			str = str.replaceAll(nums, " ");

			str = str.charAt(0).toUpperCase() + str.slice(1);
			return str;
		}

		function destroyTable() {
			if ($.fn.DataTable.isDataTable('#idk_table')) {
				$("#idk_table").DataTable().clear().destroy();
				$("#idk_table").empty();
			}
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