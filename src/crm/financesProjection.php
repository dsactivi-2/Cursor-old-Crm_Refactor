<?php
include($_SERVER["DOCUMENT_ROOT"] . "/includes/functions.php");
include($_SERVER["DOCUMENT_ROOT"] . "/includes/common.php");

$getEmployeeStatus = explode(',', getEmployeeStatus());

if(isset($_REQUEST["page"])) {
	$page = $_REQUEST["page"];
}else{
	header("Location: financesProjection?page=info");
}
?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Dashboard naloga | <?php getTitle(); ?></title>

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
			<?php
			switch ($page){
				case "list": 
				?>
					<div style="position:relative">
						<h1>
							<i class="fa fa-briefcase idk_color_green" aria-hidden="true"></i>
							Financije - projekcija
						</h1>
						<a style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%);" href="/" class="btn btn-success btn-lg"><i class="fa fa-arrow-left"></i> Idi na dashboard DIPL</a>
					</div>
					<hr />
					<div class="content_box idk_margin_top20">
						<div style="margin-bottom:30px; text-align:center">
							<h1>
								Projekcija odlaska - financije
							</h1>
						</div>
						<button style="float:right;" id="export"> Export </button>
						<script>
						$( "#export" ).on( "click", function() {
							table_export();
						} );
						function table_export() {
								var uri = 'data:application/vnd.ms-excel;base64,';
								var template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--><meta http-equiv="content-type" content="text/plain; charset=UTF-8"/></head><body><table>{table}</table></body></html>';

								var base64 = function(s) {
									return window.btoa(unescape(encodeURIComponent(s)));
								};

								var format = function(s, c) {
									return s.replace(/{(\w+)}/g, function(m, p) {
									return c[p];
									});
								};

								var table = document.querySelector("#idk_table_wrapper");

								// Create a temporary table for exporting
								var tempTable = document.createElement('table');

								// Clone the table headers
								var thead = table.querySelector('thead');
								var clonedThead = thead.cloneNode(true);
								tempTable.appendChild(clonedThead);

								// Clone and format the table cells
								var tbody = table.querySelector('tbody');
								var clonedRows = tbody.querySelectorAll('tr');
								clonedRows.forEach(function(row, rowIndex) {
									var clonedRow = row.cloneNode(true);
									var cells = clonedRow.querySelectorAll('td');
									cells.forEach(function(cell, cellIndex) {
									var text = cell.innerText.trim();
									if (!isNaN(parseFloat(text.replace(/,/g, '')))) {
										var numericValue = parseFloat(text.replace(/,/g, '').replace(/ /g, '').replace(/,/g, '.'));
										cell.innerText = numericValue;
										cell.setAttribute('style', 'mso-number-format:"# ##0.00"');
										cell.setAttribute('data-value', text);
									}
									});

									// Add the sum formula for each row
									// var sumFormulaRow = document.createElement('td');
									// sumFormulaRow.setAttribute('style', 'mso-number-format:"#,##0.00"');
									var startRow = rowIndex + 2;
									var endRow = rowIndex + 2 + clonedRows.length - 1;
									// sumFormulaRow.innerText = `=SUM(B${startRow}:G${endRow})`;
									// clonedRow.appendChild(sumFormulaRow);

									tempTable.appendChild(clonedRow);
								});

								// Add the sum formulas for each column
								var sumFormulaRow = document.createElement('tr');
								sumFormulaRow.setAttribute('style', 'mso-number-format:"# ##0.00"');
								for (var i = 0; i < clonedThead.querySelectorAll('th').length; i++) {
									var sumFormulaCell = document.createElement('td');
									var startRow = 2;
									var endRow = 2 + clonedRows.length - 1;
									if(i == 0){
										sumFormulaCell.innerText = ``;
									}else{
										sumFormulaCell.innerText = `=SUM(${String.fromCharCode(65 + i)}${startRow}:${String.fromCharCode(65 + i)}${endRow})`;
									}
									sumFormulaRow.appendChild(sumFormulaCell);
								}
								tempTable.appendChild(sumFormulaRow);
								
								// Adjust column widths manually
								var tempTableCols = clonedThead.querySelectorAll('th');
								tempTableCols.forEach(function(col, colIndex) {
									col.setAttribute('style', `width: 120px; min-width: 120px;`);
								});

								// Export the temporary table
								var ctx = { worksheet: new Date().toString().replace(" ", "") || 'Worksheet', table: tempTable.innerHTML };
								var excelData = format(template, ctx);

								// Convert the Excel data to base64
								var excelBase64 = base64(excelData);

								// Create a download link and trigger the download
								var link = document.createElement("a");
								link.href = uri + excelBase64;
								link.download = "data.xls";
								link.click();
							}	
						</script>
						<div id="omotac">
							<table id="idk_table" class="stripe" cellspacing="0" width="100%">
								<tbody>
								</tbody>
							</table>
						</div>
					</div>
					<script>
						let selectedConfig;
						$(function() {
							selectedConfig = configs[0];
							reloadTable();
						});

						function fadeOut(){
							$("#omotac").addClass("animated");
						}
						function fadeIn(){
							$("#omotac").removeClass("animated");
						}

						function reloadTable() {
							fetch(selectedConfig.apiUrl).then(res => res.json()).then(data => {
								destroyTable();
								fillCompaniesTable(data);
								fadeIn();
							});
						}

						function setProjectionColumns(data){
							// Array of all dates
							const allDates = data.map(company => Object.keys(company).filter(key => key.includes("/"))).flat();
							const today = new Date();
							// Unique, sorted dates
							const dates = [...new Set(allDates)].sort((a,b) => {
								const mjesecA = parseInt(a.split("/")[0]);
								const godinaA = parseInt(a.split("/")[1]);

								const mjesecB = parseInt(b.split("/")[0]);
								const godinaB = parseInt(b.split("/")[1]);
								
								if(godinaA < godinaB){
									return -1;
								} else if(godinaA > godinaB){
									return 1;
								} else {
									if(mjesecA < mjesecB){
										return -1;
									} else if(mjesecA > mjesecB){
										return 1;
									}
									else{
										return 0;
									}
								}
							});

							let dateColumns = [];
							let numOfMonths = 0;
							for(numOfMonths=0; ; numOfMonths++){
								let date = new Date(today.getFullYear(), today.getMonth() + numOfMonths, 1);
								let monthNumber = date.getMonth() + 1;
								let yearNumber = date.getFullYear().toString().substring(2);
								let monthName = date.toLocaleString( 'en-US', {month: 'short'});
								
								dateColumns.push({
									"data":  `${monthNumber}/${yearNumber}`,
									"title":  `${monthNumber}/${yearNumber}`,
									"defaultContent": "0"
								});

								if(`${monthNumber}/${yearNumber}` === dates[dates.length-1])
									break;
							}       

							let newProjekcijaConfig = {
								columnDefs: [
									{
										targets: [...Array.from({length: numOfMonths+1}, (x,i) => i+1)],
										className: 'dt-right',
									},
									{
										targets: "_all",
										orderable: false,
										"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
											if(sData == 0){
												$(nTd).html(`<span class="empty" style="color: lightgray; user-select: none">${sData}</span>`);
												$(nTd).addClass("empty");
											}
											else{
												if(iCol == 0){
													$(nTd).html(`<a target="__blank" href=/companies?page=open&id=${oData.company_id}#nalozi>${sData}</a>`);
												}
												else{
													$(nTd).html(`<a target="__blank" href=/financesProjection.php?page=info&company_id=${oData.company_id}&mjesec=${encodeURIComponent(dateColumns[iCol-1].title)}><b>${parseFloat(sData).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")}</b></a>`);
												}
											}
										},
									}
								],
								columns: [
									{
										"data": "company_name",
										"title": "Kompanija",
										width: "25%"
									},
									...dateColumns
								],
								apiUrl: "/dashboardNaloga/API_Financije.php?page=allCompanies"
							}

							/*
								Ako se globalnom objektu `projekcijaConfig` dodijeli nova vrijednost direktno, npr.
								`projekcijaConfig = newProjekcijaConfig`, to neće updateati objekt `selectedConfig` 
								niti vrijednost configa u nizu `configs` jer će to samo stvoriti novi objekt.
								No međutim, ako objektu `projekcijaConfig` dodijelimo nove properties, onda će i `selectedConfig`
								i `configs[4]` dobiti te nove vrijednosti.
							*/
							Object.keys(projekcijaConfig).forEach(function(key) {
								delete projekcijaConfig[key];
							});

							Object.keys(newProjekcijaConfig).forEach(function(key) {
								projekcijaConfig[key] = newProjekcijaConfig[key];
							});
						}

						function fillCompaniesTable(podaci) {
							// Special case for projection
							setProjectionColumns(podaci);
							// Create the table footer
							let footerHtml = '<tfoot><tr class="tfooter_class"><th>Total</th>';
							for (let i = 0; i < selectedConfig.columns.length - 1; i++) {
								footerHtml += '<th></th>';
							}
							footerHtml += '</tr></tfoot>';
							
							// Append the footer HTML to the table
							$('#idk_table').append(footerHtml);
							
							$('#idk_table').DataTable({
								"data": podaci,
								"columns": selectedConfig.columns,
								scrollX: true,
								columnDefs: selectedConfig.columnDefs || [],
								fixedColumns: true,
								dom: "t",
								"pageLength": 50,
								drawCallback: function () {
									const api = this.api();
									const table = $(this.api().table().node());

									// Calculate and display the sum for each column on the right side
									api.columns().every(function () {
										const column = this;
										if (column.index() > 0) {
										const sum = column.data().reduce((a, b) => {
											const numA = parseFloat(a.toString().replace(",", ".")) || 0;
											const numB = parseFloat(b.toString().replace(",", ".")) || 0;
											return numA + numB;
										}, 0);
										$(column.footer()).html(sum.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
										$(column.footer()).addClass("no-style text-right"); // Add the "no-style" and "text-right" classes
										}
									});

									// Add class to footer cells for right alignment
									$(table).find("tfoot th:not(:last-child)").addClass("text-right"); // Exclude the last column

									// Calculate and display the sum for each row on the right side
									const tableBody = $(table).find("tbody");
									tableBody.find("tr").each(function () {
										const row = $(this);
										let rowSum = 0;
										row.find("td:not(:first-child)").each(function () {
										const cellValue = parseFloat($(this).text().replace(/,/g, "")) || 0;
										rowSum += cellValue;
										});
										row.append(`<td class="no-style text-right">${rowSum.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")}</td>`);
									});
									// Add empty cell after the last column sum in the footer row
									$(".tfooter_class").append('<th></th>');
									const footerTh = $(".tfooter_class th"); // Replace "#idk_table" with your table selector
									footerTh.css("padding", "10px 8px");
									
									// Select the last th element in the table header
									const lastTh = $("thead tr:last-child");

									// // Change the content to "SUM"
									lastTh.append('<th class="no-style text-right">SUM</th>');
									$(".dataTables_scrollFoot").css("display","none");
									$(".dataTables_scrollHead").css("display","none");
								}
							});
							
						}

						function destroyTable() {
							if ($.fn.DataTable.isDataTable('#idk_table')) {
								$("#idk_table").DataTable().clear().destroy();
								$("#idk_table").empty();
							}
						}
						let projekcijaConfig = {
							apiUrl: "/dashboardNaloga/API_Financije.php?page=allCompanies"
						}
						const configs = [projekcijaConfig];
					</script>
				<?php 
				break;

				case "info":
					$company_id = $_GET["company_id"];
					$mjesec 	= $_GET["mjesec"];
					$parts = explode('/', $mjesec);
					$month = $parts[0];
					$year = $parts[1];

					$firstDay = date('Y-m-d', strtotime("$year-$month-01"));
					$lastDay = date('Y-m-t', strtotime("$year-$month-01"));
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
					<h1> <i class="fa fa-money idk_color_green" aria-hidden="true"></i> Financije za <?php echo getCompanyNameById($company_id); ?> za period <?php echo date('Y.m.d', strtotime($firstDay)).'-'.date('Y.m.d', strtotime($lastDay)); ?>:</h1>
						<hr>
						<div class="row">
							<div class="col-md-12">
								<div class="content_box">	
									<div class="col-12 row">
										<div class="row idk_margin_top20" style="display:flex; align-items: flex-end;">
											<div class="col-lg-3 col-md-3">
												<label for="filter_select_status_rate_faktura">Status rate:</label>
												<select id="filter_select_status_rate_faktura" class="selectpicker" name="filter_select_status_rate_faktura" multiple>
													<option selected value="0">Očekivano</option>
													<option selected value="1">Treba fakturisati</option>
													<option selected value="2">Fakturisano</option>
												</select>
											</div>
											<div class="col-lg-3 col-md-3">
												<label for="filter_select_firma_faktura">Firma fakturisanja:</label>
												<select id="filter_select_firma_faktura" class="selectpicker" name="filter_select_firma_faktura" multiple>
													<option selected value="1">DE - Jobstep Gmbh</option>
													<option selected value="2">CH - Jobstep Int Gmbh</option>
												</select>
											</div>
											<div class="col-lg-3 col-md-3 d-flex align-items-center">
												<button id="filter_button_trazi_faktura" style="width:100%" class="btn btn-success">Traži</button>
											</div>
											<div class="col-lg-3 col-md-3 d-flex align-items-center">
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
								var filter_kreirana_faktura_start 		= '<?php echo $firstDay; ?>' ?? '';
								var filter_kreirana_faktura_end 		= '<?php echo $lastDay; ?>' ?? '';
								var filter_select_kompanija_faktura 	= '<?php echo $company_id; ?>' ?? '';
								var filter_select_status_rate_faktura	= $('#filter_select_status_rate_faktura').val();
								var filter_select_firma_faktura			= $('#filter_select_firma_faktura').val();
								
								$('#to_append_to_faktura').fadeOut(600, function(){
									$.ajax({
										url: 'ajax_data.php?page=get_financije_projekcija',
										type: 'POST',
										dataType: 'html',
										data: {
												'filter_kreirana_faktura_start' 	: filter_kreirana_faktura_start,
												'filter_kreirana_faktura_end' 		: filter_kreirana_faktura_end,
												'filter_select_kompanija_faktura' 	: filter_select_kompanija_faktura,
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
								});
							}
							$(document).ready(function() {
								getTablePredracuni();
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
