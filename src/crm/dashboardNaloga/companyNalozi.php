<?php
include($_SERVER["DOCUMENT_ROOT"] . "/includes/functions.php");
include($_SERVER["DOCUMENT_ROOT"] . "/includes/common.php");
include("utils.php");

$getEmployeeStatus = explode(',', getEmployeeStatus());
if (!isset($_REQUEST["id"])) {
	header("Location: /dashboardNaloga/companies.php");
}
$id = $_REQUEST["id"];
?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Nalozi - Dashboard naloga | <?php getTitle(); ?></title>

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
                <a style="font-size: 36px; position: absolute; top: 30px; left:30px" id="backButton" href="/dashboardNaloga/companies.php">←</a>
				<div style="text-align:center; margin-bottom: 30px">
					<h1>
						<?php
						echo getCompanyNameById($_REQUEST["id"]);
						?>
					</h1>
				</div>
				<div id="buttons">
					<button onclick="changeConfig(this, 0)"> Recruiting </button>
					<button onclick="changeConfig(this, 1)"> DIPL </button>
					<button onclick="changeConfig(this, 2)"> Jezik </button>
					<button onclick="changeConfig(this, 3)"> Viziranje </button>
					<button onclick="changeConfig(this, 4)"> Projekcija odlaska </button>
					<button onclick="changeConfig(this, 5)"> Početak rada </button>
				</div>
                <div id="omotac">
                    <table id="idk_table" class="stripe" cellspacing="0" width="100%">
                        <tbody>
                        </tbody>
                    </table>
                <div>
			</div>
		</div>
	</div>
	<footer><?php getCopyright(); ?></footer>
	<script>
		let selectedConfig;
        let companyId;
		$(function() {
            setCompanyId();
            document.querySelector("#backButton").setAttribute("href", `/dashboardNaloga/companies?config=${getConfigIndex()}`);
            selectedConfig = configs[getConfigIndex()] || recruitingConfig;
            $("#buttons").children()[getConfigIndex() || 0].setAttribute("disabled","");
			reloadTable();
			//fillNaloziTable();
		});

        function setCompanyId()
        {
            let params = new URLSearchParams(document.location.search);
            companyId = params.get("id");
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

		function changeConfig(button, configIndex) {
			fadeOut();
			$("#buttons").children().removeAttr("disabled");
			button.setAttribute("disabled", "");
            document.querySelector("#backButton").setAttribute("href", `/dashboardNaloga/companies?config=${configIndex}`);
			selectedConfig = configs[configIndex];
            setConfigIndex(configIndex);
			reloadTable();
		}
		function fadeOut(){
			$("#omotac").addClass("animated");
		}
		function fadeIn(){
			$("#omotac").removeClass("animated");
		}
        function setConfigIndex(configIndex)
        {
            let params = new URLSearchParams(document.location.search);
            params.set("config", configIndex);
            window.history.replaceState({}, '', `${document.location.pathname}?${params}`);
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
			if(dates.length != 0)
			{
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
			}
			else{
				for(numOfMonths=0; numOfMonths<12; numOfMonths++){
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
			}

            let newProjekcijaConfig = {
                columnDefs: [
                    {
                        targets: [...Array.from({length: numOfMonths+1}, (x,i) => i+1)],
                        className: 'dt-center',
                    },
                    {
                        targets: "_all",
                        orderable: false,
                        "fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
                            if(sData == 0){
                                $(nTd).html(`<span style="color: lightgray; user-select: none">${sData}</span>`);
                            }
                            else{
                                if(iCol == 0){
                                    $(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus.php?companyId=${companyId}&nalogId=${oData.nalog_id}&config=4>${sData}</a>`);
                                }
                                else{
                                    $(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus.php?companyId=${companyId}&mjesec=${encodeURIComponent(dateColumns[iCol-1].title)}&nalogId=${oData.nalog_id}&config=4><b>${sData}</b></a>`);
                                }
                            }
                        },
                    }
                ],
                columns: [
                    {
                        "data": "nalog_name",
                        "title": "Nalog",
                        width: "25%"
                    },
                    ...dateColumns
                ],
                apiUrl: `/dashboardNaloga/API_Projekcija.php?page=company&companyId=${companyId}`
            }
			/*
				Ako se globalnom objektu `projekcijaConfig` dodijeli nova vrijednost direktno, npr.
				`projekcijaConfig = newProjekcijaConfig`, to neće updateati varijablu `selectedConfig` 
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
		function setPocetakRadaColumns(data){
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
			if(dates.length != 0)
			{
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
			}
			else{
				for(numOfMonths=0; numOfMonths<12; numOfMonths++){
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
			}

            let newPocetakRadaConfig = {
                columnDefs: [
                    {
                        targets: [...Array.from({length: numOfMonths+1}, (x,i) => i+1)],
                        className: 'dt-center',
                    },
                    {
                        targets: "_all",
                        orderable: false,
                        "fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
                            if(sData == 0){
                                $(nTd).html(`<span style="color: lightgray; user-select: none">${sData}</span>`);
                            }
                            else{
                                if(iCol == 0){
                                    $(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus.php?companyId=${companyId}&nalogId=${oData.nalog_id}&config=5>${sData}</a>`);
                                }
                                else{
                                    $(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus.php?companyId=${companyId}&mjesec=${encodeURIComponent(dateColumns[iCol-1].title)}&nalogId=${oData.nalog_id}&config=5><b>${sData}</b></a>`);
                                }
                            }
                        },
                    }
                ],
                columns: [
                    {
                        "data": "nalog_name",
                        "title": "Nalog",
                        width: "25%"
                    },
                    ...dateColumns
                ],
                apiUrl: `/dashboardNaloga/API_Projekcija.php?page=companyPocetakRada&companyId=${companyId}`
            }
			/*
				Ako se globalnom objektu `projekcijaConfig` dodijeli nova vrijednost direktno, npr.
				`projekcijaConfig = newProjekcijaConfig`, to neće updateati varijablu `selectedConfig` 
				niti vrijednost configa u nizu `configs` jer će to samo stvoriti novi objekt.
				No međutim, ako objektu `projekcijaConfig` dodijelimo nove properties, onda će i `selectedConfig`
				i `configs[4]` dobiti te nove vrijednosti.
			*/
			Object.keys(pocetakRadaConfig).forEach(function(key) {
				delete pocetakRadaConfig[key];
			});

			Object.keys(newPocetakRadaConfig).forEach(function(key) {
				pocetakRadaConfig[key] = newPocetakRadaConfig[key];
			});
        }
		function reloadTable() {
            fetch(`${selectedConfig.apiUrl}&companyId=${companyId}`).then(res => res.json()).then(data => {
				destroyTable();
				fillNaloziTable(data);
				fadeIn();
			});
		}

		function destroyTable() {
			if ($.fn.DataTable.isDataTable('#idk_table')) {
				$("#idk_table").DataTable().clear().destroy();
				$("#idk_table").empty();
			}
		}

		function fillNaloziTable(podaci) {
            if(getConfigIndex() == 4){
                setProjectionColumns(podaci);
            }
			if(getConfigIndex() == 5){
                setPocetakRadaColumns(podaci);
            }
			$('#idk_table').DataTable({
				"data": podaci,
				"columns": selectedConfig.columns,
				columnDefs: selectedConfig.columnDefs || [],
				scrollX: true,
				fixedColumns: true,
				dom: "t",
/*
				footerCallback: function(row, data, start, end, display) {
					var api = this.api();


					let total = 0;
					for (let col = 1; col < 6; col++) {

						let columnTotal = api
							.column(col)
							.data()
							.reduce(function(a, b) {
								return parseInt(a) + parseInt(b);
							}, 0);

						total += columnTotal;

						$(api.column(col).footer()).html(columnTotal);
					}

					$(api.column(6).footer()).html(total);
				},
*/
			});
		}
		let recruitingConfig = {
            columnDefs: [
                {
                    targets: [1,2,3,4,5],
                    className: 'dt-center',
                    orderable: false,
                    width: `${75.0 / 5.0}%`
                },
            ],
			columns: [{
					"data": "nalog_naziv",
					"title": "Nalog",
					"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
						$(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus?nalogId=${oData.nalog_id}&companyId=${companyId}&config=0>${oData.nalog_naziv}</a>`);
					},
                    width: "25%"
				},
				{
					"data": "casting",
					"title": "Casting",
					"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
						$(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus.php?nalogId=${oData.nalog_id}&companyId=${companyId}&statusPrijave=3&config=0>${oData.casting}</a>`);
					},
				},
				{
					"data": "ceka_ugovor",
					"title": "Čeka ugovor",
					"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
						$(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus.php?nalogId=${oData.nalog_id}&companyId=${companyId}&statusPrijave=7&config=0>${oData.ceka_ugovor}</a>`);
					},
				},
				{
					"data": "poslan_ugovor",
					"title": "Poslan ugovor",
					"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
						$(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus.php?nalogId=${oData.nalog_id}&companyId=${companyId}&statusPrijave=8&config=0>${oData.poslan_ugovor}</a>`);
					},
				},
				{
					"data": "potpisan_ugovor",
					"title": "Potpisan ugovor",
					"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
                        $(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus.php?nalogId=${oData.nalog_id}&companyId=${companyId}&statusPrijave=9&config=0>${
                                 parseInt(oData.potpisan_ugovor) 
                               + parseInt(oData.prikupljanje_dokumentacije)
                               + parseInt(oData.ceka_termin)
                               + parseInt(oData.ceka_vizu)
                               + parseInt(oData.dopuna_dokumenata)
                               + parseInt(oData.odbijena_viza)
                               + parseInt(oData.dobio_vizu)
                        }</a>`);
					},
				},
				/*
				{
					"data": "pocetak_rada",
					"title": "Početak rada",
					"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
						$(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus.php?nalogId=${oData.nalog_id}&companyId=${companyId}&statusPrijave=10&config=0>${oData.pocetak_rada}</a>`);
					}
				},
				*/
				{
					"data": "zaposlen",
					"title": "Nađeno / traženo",
					"render": function(data, type, row, meta) {
                        return "<b>" + (
                                          parseInt(row.ceka_ugovor) 
                                        + parseInt(row.poslan_ugovor) 
                                        + parseInt(row.potpisan_ugovor) 
                                        + parseInt(row.prikupljanje_dokumentacije)
                                        + parseInt(row.ceka_termin)
                                        + parseInt(row.ceka_vizu)
                                        + parseInt(row.dopuna_dokumenata)
                                        + parseInt(row.odbijena_viza)
                                        + parseInt(row.dobio_vizu)
                                        + parseInt(row.pocetak_rada)
                                        + parseInt(row.zaposlen)
                                    ) 
                             + ` / ${row.nalog_potrebno_kandidata}`
                             + "</b>";
					},
                    "className": "sumCells",
				}
				/* 					
				{
					"data": "zaposlen",
					"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
						$(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus.php?companyId=${oData.company_id}&statusPrijave=4>${oData.zaposlen}</a>`);
					}
				}, 
				*/
			],
			apiUrl: "/dashboardNaloga/API_Recruiting.php?page=company"
		}
		let DIPLConfig = {
            columnDefs: [
                {
                    targets: [1,2,3,4,5,6,7,8],
                    className: 'dt-center',
                    orderable: false,
                    width: `${75.0 / 8.0}%`
                }
            ],
			columns: [{
					"data": "nalog_naziv",
					"title": "Nalog",
					"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
						$(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus?nalogId=${oData.nalog_id}&companyId=${companyId}&config=1>${oData.nalog_naziv}</a>`);
					},
                    width: "25%"
				},
				{
					"data": "nije_u_diplu",
					"title": "Nije u DIPL",
					"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
						$(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus.php?nalogId=${oData.nalog_id}&companyId=${companyId}&status=0&config=1>${oData.nije_u_diplu}</a>`);
					}
				},
				{
					"data": "u_obradi_lead",
					"title": "U obradi lead",
					"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
						$(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus.php?nalogId=${oData.nalog_id}&companyId=${companyId}&status=1&config=1>${oData.u_obradi_lead}</a>`);
					}
				},
				{
					"data": "prikupljanje_dokumentacije",
					"title": "Prikupljanje dokumentacije",
					"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
						$(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus.php?nalogId=${oData.nalog_id}&companyId=${companyId}&status=2&config=1>${oData.prikupljanje_dokumentacije}</a>`);
					}
				},
				{
					"data": "poslana_posta",
					"title": "Poslana pošta",
					"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
						$(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus.php?nalogId=${oData.nalog_id}&companyId=${companyId}&status=3&config=1>${oData.poslana_posta}</a>`);
					}
				},
				{
					"data": "u_obradi",
					"title": "U obradi",
					"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
						$(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus.php?nalogId=${oData.nalog_id}&companyId=${companyId}&status=4&config=1>${oData.u_obradi}</a>`);
					}
				},
				{
					"data": "dopuna_dokumentacije",
					"title": "Dopuna dokumentacije",
					"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
						$(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus.php?nalogId=${oData.nalog_id}&companyId=${companyId}&status=5&config=1>${oData.dopuna_dokumentacije}</a>`);
					}
				},
				{
					"data": "zavrsen",
					"title": "Završen",
					"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
						$(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus.php?nalogId=${oData.nalog_id}&companyId=${companyId}&status=6&config=1>${oData.zavrsen}</a>`);
					}
				},
				{
					"data": "arhiv",
					"title": "Arhiv",
					"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
						$(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus.php?nalogId=${oData.nalog_id}&companyId=${companyId}&status=7&config=1>${oData.arhiv}</a>`);
					}
				},
			],
			apiUrl: "/dashboardNaloga/API_DIPL.php?page=company"
		}
		let jezikConfig = {
            columnDefs: [
                {
                    targets: [1,2,3,4,5,6,7],
                    className: 'dt-center',
                    orderable: false,
                    width: `${75.0 / 7.0}%`
                }
            ],
			columns: [{
					"data": "nalog_naziv",
					"title": "Nalog",
					"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
						$(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus?nalogId=${oData.nalog_id}&companyId=${companyId}&config=2>${oData.nalog_naziv}</a>`);
					},
                    width: "25%"
				},
                {
					"data": "nema_potvrdjen_jezik",
					"title": "Nema potvrđen jezik",
					"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
						$(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus.php?nalogId=${oData.nalog_id}&companyId=${companyId}&statusJezika=nema_potvrdjen_jezik&config=2>${oData.nema_potvrdjen_jezik}</a>`);
					}
				},
				{
					"data": "BZ",
					"title": "Bez znanja",
					"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
						$(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus.php?nalogId=${oData.nalog_id}&companyId=${companyId}&statusJezika=BZ&config=2>${oData.BZ}</a>`);
					}
				},
				{
					"data": "A1_1",
					"title": "A1.1",
					"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
						$(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus.php?nalogId=${oData.nalog_id}&companyId=${companyId}&statusJezika=A1_1&config=2>${oData.A1_1}</a>`);
					}
				},
				{
					"data": "A1_2",
					"title": "A1.2",
					"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
						$(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus.php?nalogId=${oData.nalog_id}&companyId=${companyId}&statusJezika=A1_2&config=2>${oData.A1_2}</a>`);
					}
				},
				{
					"data": "A2_1",
					"title": "A2.1",
					"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
						$(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus.php?nalogId=${oData.nalog_id}&companyId=${companyId}&statusJezika=A2_1&config=2>${oData.A2_1}</a>`);
					}
				},
				{
					"data": "A2_2",
					"title": "A2.2",
					"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
						$(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus.php?nalogId=${oData.nalog_id}&companyId=${companyId}&statusJezika=A2_2&config=2>${oData.A2_2}</a>`);
					}
				},
				{
					"data": "B",
					"title": "B1+",
					"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
						$(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus.php?nalogId=${oData.nalog_id}&companyId=${companyId}&statusJezika=B&config=2>${oData.B}</a>`);
					}
				},
			],
			apiUrl: "/dashboardNaloga/API_Jezik.php?page=company"
		}
		let viziranjeConfig = {
            columnDefs: [
                {
                    targets: [1,2,3,4,5,6],
                    className: 'dt-center',
                    orderable: false,
                    width: `${75.0 / 6.0}%`
                }
            ],
			columns: [{
					"data": "nalog_naziv",
					"title": "Nalog",
					"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
						$(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus.php?companyId=${companyId}&nalogId=${oData.nalog_id}&config=3>${oData.nalog_naziv}</a>`);
					}
					
				},
				{
					"data": "prikupljanje_dokumentacije",
					"title": "Prikupljanje dokumentacije",
					"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
						
						$(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus.php?companyId=${companyId}&nalogId=${oData.nalog_id}&statusPrijave=12&config=3>${oData.prikupljanje_dokumentacije}</a>`);
					}
				},
				{
					"data": "ceka_termin",
					"title": "Čeka termin",
					"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
						$(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus.php?companyId=${companyId}&nalogId=${oData.nalog_id}&statusPrijave=15&config=3>${oData.ceka_termin}</a>`);
					}
				},
				{
					"data": "ceka_vizu",
					"title": "Čeka vizu",
					"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
						$(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus.php?companyId=${companyId}&nalogId=${oData.nalog_id}&statusPrijave=18&config=3>${oData.ceka_vizu}</a>`);
					}
				},
				{
					"data": "dopuna_odbijenica",
					"title": "Dopuna / Odbijenica",
					"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
						$(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus.php?companyId=${companyId}&nalogId=${oData.nalog_id}&statusPrijave=21&config=3>${oData.dopuna_odbijenica}</a>`);
					}
				},
				{
					"data": "dobio_vizu",
					"title": "Dobio vizu",
					"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
						$(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus.php?companyId=${companyId}&nalogId=${oData.nalog_id}&statusPrijave=27&config=3>${oData.dobio_vizu}</a>`);
					}
				},
				{
					"data": "pocetak_rada",
					"title": "Početak rada",
					"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
						$(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus.php?companyId=${companyId}&nalogId=${oData.nalog_id}&statusPrijave=10&config=3>${oData.pocetak_rada}</a>`);
					}
				},
			],
			apiUrl: "/dashboardNaloga/API_Viziranje.php?page=company"
		}

		let projekcijaConfig = {
			apiUrl: `/dashboardNaloga/API_Projekcija.php?page=company`
		}
		let pocetakRadaConfig = {
			apiUrl: `/dashboardNaloga/API_Projekcija.php?page=companyPocetakRada`
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
