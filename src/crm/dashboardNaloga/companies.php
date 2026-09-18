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
			<div style="position:relative">
				<h1>
					<i class="fa fa-briefcase idk_color_green" aria-hidden="true"></i>
					Dashboard naloga - kandidati
				</h1>
				<a style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%);" href="/" class="btn btn-success btn-lg"><i class="fa fa-arrow-left"></i> Idi na dashboard DIPL</a>
			</div>
			<hr />
			<div class="content_box idk_margin_top20">
				<div style="margin-bottom:30px; text-align:center">
					<h1>
						Kompanije
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
				<button style="float:right;" id="export"> Export </button>
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
				<div id="omotac">
					<table id="idk_table" class="stripe" cellspacing="0" width="100%">
						<tbody>
						</tbody>
<!-- 						<tfoot class="sumCells">
							<tr style="text-align: center">
								<td style="padding: 0 !important; text-align: left">∑</td>
								<td style="padding: 0 !important"></td>
								<td style="padding: 0 !important"></td>
								<td style="padding: 0 !important"></td>
								<td style="padding: 0 !important"></td>
								<td style="padding: 0 !important"></td>
								<td style="padding: 0 !important"></td>
							</tr>
						</tfoot> -->
					</table>
				</div>
			</div>
			<footer><?php getCopyright(); ?></footer>
		</div>
	</div>
	<script>
		let selectedConfig;
		$(function() {
            selectedConfig = configs[getConfigIndex()];
            $("#buttons").children()[getConfigIndex() || 0].setAttribute("disabled","");
			reloadTable();
			if(getConfigIndex() == 4 || getConfigIndex() == 5){
				reloadTable();
			}
		});

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

        function setConfigIndex(configIndex)
        {
            let params = new URLSearchParams(document.location.search);
            params.set("config", configIndex);
            window.history.replaceState({}, '', `${document.location.pathname}?${params}`);
        }

		function enableButtons(){
			$.each($('#buttons').children(), function (index, item) {
				$(item).removeAttr('disabled', index);
			});
			button.setAttribute("disabled", "");
		}

		function changeConfig(button, configIndex) {
			fadeOut();
			$("#buttons").children().removeAttr("disabled");
			button.setAttribute("disabled", "");
			selectedConfig = configs[configIndex];
			reloadTable();
			if(configIndex == 4 || configIndex == 5){
				$.each($('#buttons').children(), function (index, item) {
					$(item).attr('disabled', index);
				});
				reloadTable();
				//setTimeout(enableButtons(), 3000);
					$.each($('#buttons').children(), function (index, item) {
						$(item).removeAttr('disabled', index);
					});
					button.setAttribute("disabled", "");
			}
            setConfigIndex(configIndex);
            //localStorage.setItem("dashboardNalogaConfigIndex", configIndex);
		}
		function fadeOut(){
			$("#omotac").addClass("animated");
		}
		function fadeIn(){
			$("#omotac").removeClass("animated");
		}

		function reloadTable() {
			fetch(selectedConfig.apiUrl).then(res => res.json()).then(data => {
				destroyTable();
				$("#idk_table").append(selectedConfig.footer_tds);
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
			let columnCount = dateColumns.length;
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
                                $(nTd).html(`<span class="empty" style="color: lightgray; user-select: none">${sData}</span>`);
								$(nTd).addClass("empty");
                            }
                            else{
                                if(iCol == 0){
                                    $(nTd).html(`<a href=/dashboardNaloga/companyNalozi?id=${oData.company_id}&config=4>${sData}</a>`);
                                }
                                else{
                                    $(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus.php?companyId=${oData.company_id}&mjesec=${encodeURIComponent(dateColumns[iCol-1].title)}&config=4><b>${sData}</b></a>`);
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
				footer_tds: `<tfoot><tr>${'<th></th>'.repeat(columnCount + 1)}</tr></tfoot>`,

				footer: ( function ( row, data, start, end, display ) {
					var api = this.api(), data;
					// Remove the formatting to get integer data for summation
					var intVal = function ( i ) {
						return typeof i === 'string' ?
							i.replace(/[\$,]/g, '')*1 :
							typeof i === 'number' ?
								i : 0;
					};
					
					$( api.column( 0 ).footer() ).html(
						'Ukupno'
					);
					
					for (var j = 1; j < columnCount+1; j++) {
						// Total over all pages
						total = api
							.column( j )
							.data()
							.reduce( function (a, b) {
								return intVal(a) + intVal(b);
							} );

						// Update footer
						$( api.column( j ).footer() ).html(
							`<a href=kandidatiStatus.php?mjesec=${encodeURIComponent(dateColumns[j-1].data)}&config=4>${total}</a>`
						);
							
					
					}
				}),

                apiUrl: "/dashboardNaloga/API_Projekcija.php?page=allCompanies"
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
			let columnCount = dateColumns.length;
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
                                $(nTd).html(`<span class="empty" style="color: lightgray; user-select: none">${sData}</span>`);
								$(nTd).addClass("empty");
                            }
                            else{
                                if(iCol == 0){
                                    $(nTd).html(`<a href=/dashboardNaloga/companyNalozi?id=${oData.company_id}&config=5>${sData}</a>`);
                                }
                                else{
                                    $(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus.php?companyId=${oData.company_id}&mjesec=${encodeURIComponent(dateColumns[iCol-1].title)}&config=5><b>${sData}</b></a>`);
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
				footer_tds: `<tfoot><tr>${'<th></th>'.repeat(columnCount + 1)}</tr></tfoot>`,

				footer: ( function ( row, data, start, end, display ) {
					var api = this.api(), data;
					// Remove the formatting to get integer data for summation
					var intVal = function ( i ) {
						return typeof i === 'string' ?
							i.replace(/[\$,]/g, '')*1 :
							typeof i === 'number' ?
								i : 0;
					};
					
					$( api.column( 0 ).footer() ).html(
						'Ukupno'
					);
					
					for (var j = 1; j < columnCount+1; j++) {
						// Total over all pages
						total = api
							.column( j )
							.data()
							.reduce( function (a, b) {
								return intVal(a) + intVal(b);
							} );

						// Update footer
						$( api.column( j ).footer() ).html(
							`<a href=kandidatiStatus.php?mjesec=${encodeURIComponent(dateColumns[j-1].data)}&config=5>${total}</a>`
						);
							
					
					}
				}),

                apiUrl: "/dashboardNaloga/API_Projekcija.php?page=allCompaniesPocetakRada"
            }
			/*
				Ako se globalnom objektu `projekcijaConfig` dodijeli nova vrijednost direktno, npr.
				`projekcijaConfig = newProjekcijaConfig`, to neće updateati objekt `selectedConfig` 
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

		function fillCompaniesTable(podaci) {
            // Special case for projection
            if(getConfigIndex() == 4){
				// reloadTable();
                setProjectionColumns(podaci);
            }

			if(getConfigIndex() == 5){
				// reloadTable();
                setPocetakRadaColumns(podaci);
            }

			$('#idk_table').DataTable({
				"data": podaci,
				"columns": selectedConfig.columns,
				"footerCallback": selectedConfig.footer,
				scrollX: true,
				columnDefs: selectedConfig.columnDefs || [],
				fixedColumns: true,
				"pageLength": 50,
				dom: "t",
			});
			/*if(getConfigIndex() == 4){
				$.each($('#buttons').children(), function (index, item) {
					$(item).removeAttr('disabled', index);
				});
				$('#buttons').children()[4].setAttribute("disabled", "");
			}*/

		}

		function destroyTable() {
			if ($.fn.DataTable.isDataTable('#idk_table')) {
				$("#idk_table").DataTable().clear().destroy();
				$("#idk_table").empty();
			}
		}
		let recruitingConfig = {
            columnDefs: [
                {
                    targets: [1,2,3,4,5,6],
                    className: 'dt-center',
                    width: `${75.0 / 6.0}%`
                },
                {
                    targets: "_all",
                    orderable: false,
                }
            ],
			columns: [
				...[
					{ data: "company_name",    title: "Kompanija",       status: "" },
					{ data: "casting",         title: "Casting",         status: 3, },
					{ data: "ceka_ugovor",     title: "Čeka ugovor",     status: 7, },
					{ data: "poslan_ugovor",   title: "Poslan ugovor",   status: 8  },
					{ data: "potpisan_ugovor", title: "Potpisan ugovor", status: 9  },
					{ data: "pocetak_rada",    title: "Početak rada",    status: 10 },
				].map(el => {
					return {
						"data": el.data,
						"title": el.title,
						"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
                            if(sData == 0){
                                $(nTd).html(`<span class="empty" style="color: lightgray; user-select: none">${sData}</span>`);
								$(nTd).addClass("empty");
                            }
							else{
                                if(iCol == 0){
                                    $(nTd).html(`<a href=/dashboardNaloga/companyNalozi?id=${oData.company_id}&config=0>${sData}</a>`);
                                }
                                else{
									$(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus.php?companyId=${oData.company_id}&statusPrijave=${el.status}>${sData}</a>`);
								}
							}
						}

					}
				}),
				{
					"data": "company_id",
					"title": "∑",
					"render": function(data, type, row, meta) {
                        return "<b>" + (
                                          parseInt(row.casting) 
                                        + parseInt(row.ceka_ugovor) 
                                        + parseInt(row.poslan_ugovor)
                                        + parseInt(row.potpisan_ugovor)
                                        + parseInt(row.pocetak_rada)
                                    ) 
                             + "</b>";
					},
					"className": "sumCells"
				}
			],
			apiUrl: "/dashboardNaloga/API_Recruiting.php?page=allCompanies"
		}
		let DIPLConfig = {
            columnDefs: [
                { targets: [1,2,3,4,5,6,7,8], className: 'dt-center', width: `${75.0 / 8.0}%` },
                { targets: "_all", orderable: false, }
            ],
			columns: 
				[
					{ data: "company_name",               title: "Kompanija",                  status: "" },
					{ data: "nije_u_diplu",               title: "Nije u DIPL",                status: 0  },
					{ data: "u_obradi_lead",              title: "U obradi lead",              status: 1  },
					{ data: "prikupljanje_dokumentacije", title: "Prikupljanje dokumentacije", status: 2  },
					{ data: "poslana_posta",              title: "Poslana pošta",              status: 3  },
					{ data: "u_obradi",                   title: "U obradi",                   status: 4  },
					{ data: "dopuna_dokumentacije",       title: "Dopuna dokumentacije",       status: 5  },
					{ data: "zavrsen",                    title: "Završen",                    status: 6  },
					{ data: "arhiv",                      title: "Arhiv",                      status: 7  },
				].map(el => {
					return {
						"data": el.data,
						"title": el.title,
						"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
                            if(sData == 0){
                                $(nTd).html(`<span class="empty" style="color: lightgray; user-select: none">${sData}</span>`);
								$(nTd).addClass("empty");
                            }
							else{
                                if(iCol == 0){
                                    $(nTd).html(`<a href=/dashboardNaloga/companyNalozi?id=${oData.company_id}&config=1>${sData}</a>`);
                                }
                                else{
									$(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus.php?companyId=${oData.company_id}&status=${el.status}&config=1>${sData}</a>`);
								}
							}
						}
					}
				}),
			apiUrl: "/dashboardNaloga/API_DIPL.php?page=allCompanies"
		}
		let jezikConfig = {
            columnDefs: [
                { targets: [1,2,3,4,5,6,7], className: 'dt-center', width: `${75.0 / 7.0}%` },
                { targets: "_all", orderable: false, }
            ],
			columns: 
				[
					{ data: "company_name",         title: "Kompanija",           status: "",                     },
					{ data: "nema_potvrdjen_jezik", title: "Nema potvrđen jezik", status: "nema_potvrdjen_jezik", },
					{ data: "BZ",                   title: "Bez znanja",          status: "BZ",                   },
					{ data: "A1_1",                 title: "A1.1",                status: "A1_1",                 },
					{ data: "A1_2",                 title: "A1.2",                status: "A1_2",                 },
					{ data: "A2_1",                 title: "A2.1",                status: "A2_1",                 },
					{ data: "A2_2",                 title: "A2.2",                status: "A2_2",                 },
					{ data: "B",                    title: "B1+",                 status: "B",                    }
				].map(el => {
					return {
						"data": el.data,
						"title": el.title,
						"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
                            if(sData == 0){
                                $(nTd).html(`<span class="empty" style="color: lightgray; user-select: none">${sData}</span>`);
								$(nTd).addClass("empty");
                            }
							else{
                                if(iCol == 0){
                                    $(nTd).html(`<a href=/dashboardNaloga/companyNalozi?id=${oData.company_id}&config=2>${sData}</a>`);
                                }
                                else{
									$(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus.php?companyId=${oData.company_id}&statusJezika=${el.status}&config=2>${sData}</a>`);
								}
							}
						}

					}
				}),
			apiUrl: "/dashboardNaloga/API_Jezik.php?page=allCompanies"
		}
		let viziranjeConfig = {
            columnDefs: [
                { targets: [1,2,3,4,5,6], className: 'dt-center', width: `${75.0 / 6.0}%` },
                { targets: "_all", orderable: false, },
            ],
			columns: 
				[
					{ data: "company_name",               title: "Kompanija",                  status: "" },
					{ data: "prikupljanje_dokumentacije", title: "Prikupljanje dokumentacije", status: 12 },
					{ data: "ceka_termin",                title: "Čeka termin",                status: 15 },
					{ data: "ceka_vizu",                  title: "Čeka vizu",                  status: 18 },
					{ data: "dopuna_odbijenica",          title: "Dopuna / Odbijenica",        status: 21 },
					{ data: "dobio_vizu",                 title: "Dobio vizu",                 status: 27 },
					{ data: "pocetak_rada",               title: "Početak rada",               status: 10 },
				].map(el => {
					return {
						"data": el.data,
						"title": el.title,
						"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
                            if(sData == 0){
                                $(nTd).html(`<span class="empty" style="color: lightgray; user-select: none">${sData}</span>`);
								$(nTd).addClass("empty");
                            }
							else{
                                if(iCol == 0){
                                    $(nTd).html(`<a href=/dashboardNaloga/companyNalozi?id=${oData.company_id}&config=3>${sData}</a>`);
                                }
                                else{
									$(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus.php?companyId=${oData.company_id}&statusPrijave=${el.status}&config=3>${sData}</a>`);
								}
							}
						},
					}
				}),
			apiUrl: "/dashboardNaloga/API_Viziranje.php?page=allCompanies"
		}
		let projekcijaConfig = {
			apiUrl: "/dashboardNaloga/API_Projekcija.php?page=allCompanies"
		}
		let pocetakRadaConfig = {
			apiUrl: "/dashboardNaloga/API_Projekcija.php?page=allCompaniesPocetakRada"
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
