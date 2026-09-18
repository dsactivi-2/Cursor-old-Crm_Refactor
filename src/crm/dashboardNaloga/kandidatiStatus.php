<?php
include($_SERVER["DOCUMENT_ROOT"] . "/includes/functions.php");
include($_SERVER["DOCUMENT_ROOT"] . "/includes/common.php");
include("utils.php");

$getEmployeeStatus = explode(',', getEmployeeStatus());
?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Kandidati - Dashboard naloga | <?php getTitle(); ?></title>

	<?php
	include($_SERVER["DOCUMENT_ROOT"] . "/includes/head.php");
	if (in_array($getUserIp, $getIpWhiteList)) {
	?>
		<!-- CK Editor ---------------------------------------------------------------------------------------->
		<script src="<?php getSiteURL(); ?>ckeditor/ckeditor.js" async></script>
    <script src="https://cdn.datatables.net/fixedcolumns/4.1.0/js/dataTables.fixedColumns.min.js"></script>
	<link rel="stylesheet" href="style.css" />
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
			<div style="position:relative">
				<h1>
					<i class="fa fa-briefcase idk_color_green" aria-hidden="true"></i>
					Dashboard naloga - kandidati
				</h1>
				<a style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%);" href="/" class="btn btn-success btn-lg"><i class="fa fa-arrow-left"></i> Idi na dashboard DIPL</a>
			</div>
			<hr />
			<div style="position: relative" class="content_box idk_margin_top20">
                <a style="font-size: 36px; position: absolute; top: 30px; left:30px" id="backButton">←</a>
				<button style="position: absolute; top: 30px; right:30px" id="export" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-file-excel-o" aria-hidden="true"></i> <span>Export</span></button>
				<script>
					$( "#export" ).on( "click", function() {
						table_export();
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

						let table = document.querySelector("#idk_table_wrapper .dataTables_scroll");

						var ctx = {worksheet: new Date().toString().replace(" ", "") || 'Worksheet', table: table.innerHTML}
						window.location.href = uri + base64(format(template, ctx));

						}
				</script>
				<div style="text-align:center">
					<h1>
                        <b id="config"></b>
                        <br />
						<?php
						echo getCompanyNameById($_REQUEST["companyId"]);

						if (isset($_REQUEST["nalogId"])) {
							echo "<br />";
							echo getNazivNaloga($_REQUEST["nalogId"]);
						}
						if (isset($_REQUEST["statusPrijave"])) {
							echo "<br />";
							if($_REQUEST["statusPrijave"]!=21)
								echo getNazivStatusa($_REQUEST["statusPrijave"]);
							else
								echo "Dopuna i odbijenica dokumenata";
						}
						?>
					</h1>
				</div>
				<table id="idk_table" class="stripe" cellspacing="0" width="100%">
					<tbody>
					</tbody>
				</table>
			</div>
			<footer><?php getCopyright(); ?></footer>
		</div>
	</div>
	<script>
        let configIndex;
		$(function() {

            configIndex = getConfigIndex();
            document.querySelector("#config").innerHTML = configs[configIndex].name;
			fillNaloziTable();
            setBackButtonUrl();
		});

        function setBackButtonUrl()
        {
            let params = new URLSearchParams(document.location.search);
            let index = params.get("config");
            if(params.has("nalogId"))
            {
                document.querySelector("#backButton").setAttribute("href", `/dashboardNaloga/companyNalozi?id=${params.get("companyId")}&config=${configIndex}`);
            }
            else
            {
                document.querySelector("#backButton").setAttribute("href", `/dashboardNaloga/companies?config=${configIndex}`);
            }
        }

        function getConfigIndex()
        {
            let params = new URLSearchParams(document.location.search);
            let index = params.get("config");
            if(index)
            {
                index = parseInt(index);
                if(0 <= index && index <= configs.length)
                {
                    return index;
                }
            }

            return 0;
        }

		function fillNaloziTable() {
            let config = configs[configIndex];
			
			$('#idk_table').DataTable({
				"autoWidth": true,
				"ajax": {
					url: config.apiUrl(),
					dataSrc: ""
				},
				"columns": config.columns,
				columnDefs: config.columnDefs || [],
				scrollX: true,
				fixedColumns: true,
				lengthMenu: [[10, 25, 50, -1],[10, 25, 50, 'All']],
			});
		}

		let recruitingConfig = {
            name: "Recruiting",
            columns: [{
						"data": "kandidat_id",
						"title": "ID kandidata",
						"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
							$(nTd).html(`<a href=/kandidati?page=open&id=${oData.kandidat_id}>${oData.kandidat_id}</a>`);
						},
						"width": "1%"
					},
					{
						"data": "kandidat_ime_prezime",
						"title": "Ime i prezime",
						"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
							$(nTd).html(`<a href=/kandidati?page=open&id=${oData.kandidat_id}>${oData.kandidat_ime_prezime}</a>`);
						},
					},
					{
						"data": "nalog_naziv",
						"title": "Nalog",
						"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
							$(nTd).html(`<a href=/nalozi?page=open&id=${oData.nalog_id}>${oData.nalog_naziv}</a>`);

						},
					},
					{
						"data": "status_naziv",
						"title": "Status prijave",
					},
					{
						"data": "na_statusu_od",
						"title": "Dana na statusu",
						"render": function(data, type, row, meta) {
							if (data) {
								let datum = new Date(data);
								let danas = new Date();
								return Math.round((danas - datum) / (1000 * 60 * 60 * 24));
							}
							return "";
						},
						"width": "1%"

					},
					{
						"data": "kandidat_potencijalni_pocetak_rada",
						"title": "Potencijalni početak rada",
					},
				],
            apiUrl: ()=> {
                let params = new URLSearchParams(document.location.search);
                let companyId = params.get("companyId");
                let nalogId = params.get("nalogId");
                let statusPrijave = params.get("statusPrijave");
                let ret = `/dashboardNaloga/API_Recruiting.php?page=nalogSvi
                        ${companyId ? `&companyId=${companyId}` : ""}
                        ${nalogId ? `&nalogId=${nalogId}` : ""}
                        ${statusPrijave ? `&statusPrijave=${statusPrijave}` : ""}
                `;
                return ret.replace(/\s/g, "");
            }
		}
		let DIPLConfig = {
            name: "DIPL",
            columns: [{
						"data": "kandidat_id",
						"title": "ID kandidata",
						"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
							$(nTd).html(`<a href=/kandidati?page=open&id=${oData.kandidat_id}>${oData.kandidat_id}</a>`);
						},
						"width": "1%"
					},
					{
						"data": "kandidat_ime_prezime",
						"title": "Ime i prezime",
						"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
							$(nTd).html(`<a href=/kandidati?page=open&id=${oData.kandidat_id}>${oData.kandidat_ime_prezime}</a>`);
						},
					},
					{
						"data": "nalog_naziv",
						"title": "Nalog",
						"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
							$(nTd).html(`<a href=/nalozi?page=open&id=${oData.nalog_id}>${oData.nalog_naziv}</a>`);

						},
					},
					{
						"data": "dipl_status",
						"title": "DIPL status",
					},
/*
					{
						"data": "na_statusu_od",
						"title": "Dana na statusu",
						"render": function(data, type, row, meta) {
							if (data) {
								let datum = new Date(data);
								let danas = new Date();
								return Math.round((danas - datum) / (1000 * 60 * 60 * 24));
							}
							return "";
						},
						"width": "1%"

					},
					{
						"data": "kandidat_potencijalni_pocetak_rada",
						"title": "Potencijalni početak rada",
					},
*/
				],
            apiUrl: ()=> {
                let params = new URLSearchParams(document.location.search);
                let companyId = params.get("companyId");
                let nalogId = params.get("nalogId");
                let status = params.get("status");
                let pstatus = params.get("pstatus");
                let ret = `/dashboardNaloga/API_DIPL.php?page=nalogSvi
                        ${companyId ? `&companyId=${companyId}` : ""}
                        ${nalogId ? `&nalogId=${nalogId}` : ""}
                        ${status ? `&status=${status}` : ""}
                        ${pstatus ? `&pstatus=${pstatus}` : ""}
                `;
                return ret.replace(/\s/g, "");
            }
		}
		let jezikConfig = {
            name: "Jezik",
            columns: [{
						"data": "kandidat_id",
						"title": "ID kandidata",
						"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
							$(nTd).html(`<a href=/kandidati?page=open&id=${oData.kandidat_id}>${oData.kandidat_id}</a>`);
						},
						"width": "1%"
					},
					{
						"data": "kandidat_ime_prezime",
						"title": "Ime i prezime",
						"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
							$(nTd).html(`<a href=/kandidati?page=open&id=${oData.kandidat_id}>${oData.kandidat_ime_prezime}</a>`);
						},
					},
					{
						"data": "nalog_naziv",
						"title": "Nalog",
						"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
							$(nTd).html(`<a href=/nalozi?page=open&id=${oData.nalog_id}>${oData.nalog_naziv}</a>`);

						},
					},
					{
						"data": "status_jezika",
						"title": "Nivo",
					},
					{
						"data": "podnivo",
						"title": "Podnivo",
					},
/*
					{
						"data": "na_statusu_od",
						"title": "Dana na statusu",
						"render": function(data, type, row, meta) {
							if (data) {
								let datum = new Date(data);
								let danas = new Date();
								return Math.round((danas - datum) / (1000 * 60 * 60 * 24));
							}
							return "";
						},
						"width": "1%"

					},
					{
						"data": "kandidat_potencijalni_pocetak_rada",
						"title": "Potencijalni početak rada",
					},
*/
				],
            apiUrl: ()=> {
                let params = new URLSearchParams(document.location.search);
                let companyId = params.get("companyId");
                let nalogId = params.get("nalogId");
                let status = params.get("statusJezika");
                let ret = `/dashboardNaloga/API_Jezik.php?page=nalogSvi
                        ${companyId ? `&companyId=${companyId}` : ""}
                        ${nalogId ? `&nalogId=${nalogId}` : ""}
                        ${status ? `&statusJezika=${status}` : ""}
                `;
                return ret.replace(/\s/g, "");
            }
		}
		let viziranjeConfig = {
            name: "Viziranje",
            columns: [{
						"data": "kandidat_id",
						"title": "ID kandidata",
						"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
							$(nTd).html(`<a href=/kandidati?page=open&id=${oData.kandidat_id}>${oData.kandidat_id}</a>`);
						},
						"width": "1%"
					},
					{
						"data": "kandidat_ime_prezime",
						"title": "Ime i prezime",
						"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
							$(nTd).html(`<a href=/kandidati?page=open&id=${oData.kandidat_id}>${oData.kandidat_ime_prezime}</a>`);
						},
					},
					{
						"data": "nalog_naziv",
						"title": "Nalog",
						"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
							$(nTd).html(`<a href=/nalozi?page=open&id=${oData.nalog_id}>${oData.nalog_naziv}</a>`);

						},
					},
					{
						"data": "status_naziv",
						"title": "Status prijave",
					},
					{
						"data": "na_statusu_od",
						"title": "Dana na statusu",
						"render": function(data, type, row, meta) {
							if (data) {
								let datum = new Date(data);
								let danas = new Date();
								return Math.round((danas - datum) / (1000 * 60 * 60 * 24));
							}
							return "";
						},
						"width": "1%"

					},
					{
						"data": "kandidat_potencijalni_pocetak_rada",
						"title": "Potencijalni početak rada",
					},
				],
            apiUrl: ()=> {
                let params = new URLSearchParams(document.location.search);
                let companyId = params.get("companyId");
                let nalogId = params.get("nalogId");
                let statusPrijave = params.get("statusPrijave");
                let ret = `/dashboardNaloga/API_Viziranje.php?page=nalogSvi
                        ${companyId ? `&companyId=${companyId}` : ""}
                        ${nalogId ? `&nalogId=${nalogId}` : ""}
                        ${statusPrijave ? `&statusPrijave=${statusPrijave}` : ""}
                `;
                return ret.replace(/\s/g, "");
            }
		}
		let projekcijaConfig = {
			
            name: "Projekcija",
			columnDefs: [
				{
					targets: "_all",
					className: 'dt-center',
				},
			],
			columns:[
				{
					"title": "ID",
					"data": "candidate_id",
					"width": "1%",
					"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
						$(nTd).html(`<a href=/kandidati?page=open&id=${sData}>${sData}</a>`);
					},
				},
				{
					"title": "Kandidat",
					"data": "candidate_fullname",
					"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
						$(nTd).html(`<a href=/kandidati?page=open&id=${oData.candidate_id}>${sData}</a>`);
					},
				},
				{
					"title": "Klijent",
					"data": "partner_name",
				},
				{
					"title": "Nalog naziv",
					"data": "nalog_name",
				},
				{
					"title": "Pozicija",
					"data": "candidate_position",
				},
				{
					"title": "Projekcija početka rada",
					"data": "candidate_assessment",
					"render": function(data, type, row, meta) {
						return new Date(data*1000).toLocaleDateString("sr-Latn-BA", { year: 'numeric', month: 'numeric', day: 'numeric' });
					},
				},
			],
            apiUrl: ()=> {
                let params = new URLSearchParams(document.location.search);
                let companyId = params.get("companyId");
                let nalogId = params.get("nalogId");
                let mjesec = params.get("mjesec");
                let ret = `/dashboardNaloga/API_Projekcija.php?page=nalogSvi
                        ${companyId ? `&companyId=${companyId}` : ""}
                        ${nalogId ? `&nalogId=${nalogId}` : ""}
                        ${mjesec ? `&mjesec=${mjesec}` : ""}
                `;
                return ret.replace(/\s/g, "");
            }
		}
		let pocetakRadaConfig = {
			
            name: "Početak rada",
			columnDefs: [
				{
					targets: "_all",
					className: 'dt-center',
				},
			],
			columns:[
				{
					"title": "ID",
					"data": "candidate_id",
					"width": "1%",
					"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
						$(nTd).html(`<a href=/kandidati?page=open&id=${sData}>${sData}</a>`);
					},
				},
				{
					"title": "Kandidat",
					"data": "candidate_fullname",
					"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
						$(nTd).html(`<a href=/kandidati?page=open&id=${oData.candidate_id}>${sData}</a>`);
					},
				},
				{
					"title": "Klijent",
					"data": "partner_name",
				},
				{
					"title": "Nalog naziv",
					"data": "nalog_name",
				},
				{
					"title": "Pozicija",
					"data": "candidate_position",
				},
				{
					"title": "Dogovoreni početak rada",
					"data": "candidate_assessment",
					"render": function(data, type, row, meta) {
						return new Date(data*1000).toLocaleDateString("sr-Latn-BA", { year: 'numeric', month: 'numeric', day: 'numeric' });
					},
				},
			],
            apiUrl: ()=> {
                let params = new URLSearchParams(document.location.search);
                let companyId = params.get("companyId");
                let nalogId = params.get("nalogId");
                let mjesec = params.get("mjesec");
                let ret = `/dashboardNaloga/API_Projekcija.php?page=nalogSviPocetakRada
                        ${companyId ? `&companyId=${companyId}` : ""}
                        ${nalogId ? `&nalogId=${nalogId}` : ""}
                        ${mjesec ? `&mjesec=${mjesec}` : ""}
                `;
                return ret.replace(/\s/g, "");
            }
		}
        const configs = [recruitingConfig, DIPLConfig, jezikConfig, viziranjeConfig, projekcijaConfig, pocetakRadaConfig];
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
