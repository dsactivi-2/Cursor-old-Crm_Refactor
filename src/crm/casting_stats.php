<?php
include("includes/functions.php");
include("includes/common.php");
// Turn off all error reporting
error_reporting(0);
$getEmployeeStatus = getEmployeeStatus();

// if(isset($_REQUEST["page"])) {
//     $page = $_REQUEST["page"];
// }else{
//     header("Location: projects?page=list");
// }
    
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Casting statistika</title>

    <?php include('includes/head.php'); 
if (in_array($getUserIp, $getIpWhiteList)){ ?>

    <script src="<?php getSiteURL(); ?>js/sortable.min.js"></script>
    <link rel="stylesheet" href="dashboardNaloga/style.css?<?php echo time();?>" />
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
            <div class="row">
                <div class="col-xs-8">
                    <h1><i class="fa fa-tty idk_color_green" aria-hidden="true"></i> Statistika poziva na casting:  </h1>
                </div>
                <div class="col-xs-12">
                    <hr />
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="content_box">
                        <div style="margin-bottom:30px; text-align:center">
                            <h1>
                                Statistike
                            </h1>
                        </div>
                        <div id="buttons" style="margin-bottom: 40px !important;">
                            <button onclick="changeConfig(this, 0)"> Agenata - Završeni</button>
                            <button onclick="changeConfig(this, 1)"> Linkova - Završeni</button>
                            <button onclick="changeConfig(this, 2)"> Agenata - Tekući</button>
                            <button onclick="changeConfig(this, 3)"> Linkova - Tekući</button>
                        </div>
                        <div class="row" style="width:30%; margin:auto; border: 3px solid green;">
                            <select class="selectpicker" data-live-search="true" data-actions-box="true" id="select_casting" multiple>
                                <option value = "" selected disabled hidden><b>Odaberite Casting</b></option>
                                <?php 
                                    $query_get_castings = $db->prepare("
                                        SELECT ppaq_id, CONCAT(DATE_FORMAT(ppaq_start_date, '%d.%m.'),' - ', DATE_FORMAT(ppaq_end_date, '%d.%m'), DATE_FORMAT(ppaq_end_date, '.%Y'),'  ',cmp.company_name) as naziv_castinga FROM idk_pp_appointment_groups 
                                        JOIN idk_nalozi ON idk_nalozi.nalog_id = papq_nalog_id
                                        JOIN idk_companies cmp ON cmp.company_id = idk_nalozi.kompanija_id
                                        WHERE papq_name IS NOT NULL AND papq_name != 'TEST' ORDER BY ppaq_id DESC;
                                    ");
                                    $query_get_castings->execute();
                                    while($row_casting = $query_get_castings->fetch()){
                                        ?>
                                            <option value="<?php echo $row_casting['ppaq_id']; ?>"><?php echo $row_casting['naziv_castinga']; ?></option> 
                                        <?php
                                    }
                                ?>
                            </select>
                        </div>
                        <div id="omotac">
                            <table id="idk_table" class="display" cellspacing="0" width="100%">
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>

        $('#select_casting').change(function(){
            reloadTable();
        })

        let selectedConfig;
		$(function() {
            selectedConfig = configs[getConfigIndex()];
            $("#buttons").children()[getConfigIndex() || 0].setAttribute("disabled","");
			reloadTable();
		});

        function getConfigIndex(){
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
        
        function setConfigIndex(configIndex){
            let params = new URLSearchParams(document.location.search);
            params.set("config", configIndex);
            window.history.replaceState({}, '', `${document.location.pathname}?${params}`);
        }
        function fadeOut(){
			$("#omotac").addClass("animated");
		}
		function fadeIn(){
			$("#omotac").removeClass("animated");
		}

		function reloadTable() {
			selected_casting = $('#select_casting').val().toString();
            if(selected_casting != ''){
                apiUrlFull = selectedConfig.apiUrl + '&castingId=' + selected_casting;
            }else{
                apiUrlFull = selectedConfig.apiUrl;
            }
            // console.log(selectedConfig.footer_tds);
            fetch(apiUrlFull).then(res => res.json()).then(data => {
				destroyTable();
                //if(selectedConfig == )
                $("#idk_table").append(selectedConfig.footer_tds);
				// console.log(data);
                fillAgentsTable(data);
				fadeIn();
			});
		}
        function destroyTable() {
			if ($.fn.DataTable.isDataTable('#idk_table')) {
				$("#idk_table").DataTable().clear().destroy();
				$("#idk_table").empty();
			}
		}

        function fillAgentsTable(podaci) {
            
            $('#idk_table').DataTable({
				"data": podaci,
				"columns": selectedConfig.columns,

                "footerCallback": selectedConfig.footer,
                order: selectedConfig.order,
				scrollX: true,
				columnDefs: selectedConfig.columnDefs || [],
				fixedColumns: true,
				dom: selectedConfig.dom || "t",
                
			});

		}

        function changeConfig(button, configIndex) {
			fadeOut();
			$("#buttons").children().removeAttr("disabled");
			button.setAttribute("disabled", "");
			selectedConfig = configs[configIndex];
			reloadTable();
            setConfigIndex(configIndex);
            //localStorage.setItem("dashboardNalogaConfigIndex", configIndex);
		}

        let agentsConfig = {
            
            dom: "Blfrtip",
            order: [[0, 'asc']],
            columnDefs: [
                {
                    targets: [1,2,3,4,5,6],
                    className: 'dt-center',
                    width: `${85.0 / 6.0}%`
                },
                {
                    targets: "_all",
                    orderable: true,
                }
            ],
			columns: [
				
                ...[
					{ data: "agent_name",       title: "Agent",                 status: "" },
					{ data: "odustao",          title: "Odustao",               status: 3  },
					{ data: "dosao",            title: "Došao",                 status: 1  },
					{ data: "nije_dosao",       title: "Nije došao",            status: 2 },
					{ data: "ukupno",           title: "Ukupno",                status: 4 },
					{ data: "dosao_pristao",    title: "Došao (%) Pristao",     status: 10 },
					{ data: "dosao_ukupno",     title: "Došao (%) Ukupno",      status: 10 },
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
                                    $(nTd).html(`<a href=employees?page=open&id=${oData.employee_id}>${sData}</a>`);
                                    // $(nTd).html(`<a href=/dashboardNaloga/companyNalozi?id=${oData.company_id}&config=0>${sData}</a>`);
                                }else if(iCol > 0 && iCol < 5){
                                    selected_casting = $('#select_casting').val().toString();
                                    if(selected_casting != ''){
                                        castingUrl = '&castingId=' + selected_casting;
                                    }else{
                                        castingUrl = '';
                                    }
                                    $(nTd).html(`<a href=casting_candidates_lists?page=agentFinished&id=${oData.employee_id}&status=${el.status}${castingUrl} target="_BLANK">${sData}</a>`);
                                
                                }else{
									// $(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus.php?companyId=${oData.company_id}&statusPrijave=${el.status}>${sData}</a>`);
									// $(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus.php?companyId=${oData.company_id}&statusPrijave=${el.status}>${sData}</a>`);
								}
							}
						}

					}
				})
            ],
            
            footer_tds: "<tfoot><tr><th></th><th></th><th></th><th></th><th></th><th></th><th></th></tr></tfoot>",

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
                    'Ukupno po statusu'
                );
                
                var total_odustao = 0;
                var total_dosao = 0;
                var total_nije_dosao = 0;
                var total_ukupno = 0;
                for (var j = 1; j < 5; j++) {
                    // Total over all pages
                    total = api
                        .column( j )
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        } );

                    // Update footer
                    $( api.column( j ).footer() ).html(
                        total
                    );
                    if(j==1){
                        total_odustao = total;
                    }else if(j==2){
                        total_dosao = total;
                    }else if(j==3){
                        total_nije_dosao = total;
                    }else if(j==4){
                        total_ukupno = total;
                    }
                }
                var pct_pristao = 0;
                var pct_ukupno = 0;
                var total_pristao = total_dosao + total_nije_dosao;

                pct_pristao = (100 * total_dosao / total_pristao).toFixed(2);
                // console.log(pct_pristao);

                pct_ukupno = (100 * total_dosao / total_ukupno).toFixed(2);
                $( api.column( 5 ).footer() ).html(
                    pct_pristao
                );
                $( api.column( 6 ).footer() ).html(
                    pct_ukupno
                );

            }),
            apiUrl: "/castingStats/API_agents.php?page=finishedCastings",
            
        }
        let linksConfig = {
            dom: "Blfrtip",
            order: [[4, 'desc']],
            columnDefs: [
                {
                    targets: [1,2,3,4,5,6],
                    className: 'dt-center',
                    width: `${85.0 / 7.0}%`
                },
                {
                    targets: "_all",
                    orderable: true,
                    order: [[4, 'desc']],
                }
            ],
            columns: [
				
                ...[
					{ data: "link_naziv",       title: "Link",                 status: "" },
					{ data: "odustao",          title: "Odustao",               status: 3  },
					{ data: "dosao",            title: "Došao",                 status: 1  },
					{ data: "nije_dosao",       title: "Nije došao",            status: 2 },
					{ data: "ukupno",           title: "Ukupno",                status: 4 },
					{ data: "dosao_pristao",    title: "Došao (%) Pristao",     status: 10 },
					{ data: "dosao_ukupno",     title: "Došao (%) Ukupno",      status: 10 },
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
                                    $(nTd).html(`<a href=link_generator.php?page=show_list&id=${oData.id_urla}>${sData}</a>`);
                                    // $(nTd).html(`<a href=/dashboardNaloga/companyNalozi?id=${oData.company_id}&config=0>${sData}</a>`);
                                }else if(iCol > 0 && iCol < 5){
                                    selected_casting = $('#select_casting').val().toString();
                                    if(selected_casting != ''){
                                        castingUrl = '&castingId=' + selected_casting;
                                    }else{
                                        castingUrl = '';
                                    }
                                    $(nTd).html(`<a href=casting_candidates_lists?page=linkFinished&id=${oData.id_urla}&status=${el.status}${castingUrl} target="_BLANK">${sData}</a>`);
                                
                                }
                                else{
									// $(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus.php?companyId=${oData.company_id}&statusPrijave=${el.status}>${sData}</a>`);
									// $(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus.php?companyId=${oData.company_id}&statusPrijave=${el.status}>${sData}</a>`);
								}
							}
						}

					}
				})
            ],
            
            footer_tds: "<tfoot><tr><th></th><th></th><th></th><th></th><th></th><th></th><th></th></tr></tfoot>",

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
                    'Ukupno po statusu'
                );
                
                var total_odustao = 0;
                var total_dosao = 0;
                var total_nije_dosao = 0;
                var total_ukupno = 0;
                for (var j = 1; j < 5; j++) {
                    // Total over all pages
                    total = api
                        .column( j )
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        } );

                    // Update footer
                    $( api.column( j ).footer() ).html(
                        total
                    );
                    if(j==1){
                        total_odustao = total;
                    }else if(j==2){
                        total_dosao = total;
                    }else if(j==3){
                        total_nije_dosao = total;
                    }else if(j==4){
                        total_ukupno = total;
                    }
                }
                var pct_pristao = 0;
                var pct_ukupno = 0;
                var total_pristao = total_dosao + total_nije_dosao;

                pct_pristao = (100 * total_dosao / total_pristao).toFixed(2);
                // console.log(pct_pristao);

                pct_ukupno = (100 * total_dosao / total_ukupno).toFixed(2);
                $( api.column( 5 ).footer() ).html(
                    pct_pristao
                );
                $( api.column( 6 ).footer() ).html(
                    pct_ukupno
                );
            }),
            apiUrl: "/castingStats/API_links.php?page=finishedCastings",
        }
        let agentsCurrentConfig = {
            dom: "Blfrtip",
            order: [[0, 'asc']],
            columnDefs: [
                {
                    targets: [1,2,3,4,5,6,7,8],
                    className: 'dt-center',
                    width: `${85.0 / 9.0}%`
                },
                {
                    targets: "_all",
                    orderable: true,
                }
            ],
			columns: [
				
                ...[
					{ data: "agent_name",       title: "Agent",                 status: ""},
                    { data: "pristao",          title: "Pristao",               status: 1 },
					{ data: "dolazi",           title: "Dolazi",                status: 2 },
					{ data: "odustao",          title: "Odustao",               status: 3 },
					{ data: "dosao",            title: "Došao",                 status: 4 },
					{ data: "nije_dosao",       title: "Nije došao",            status: 5 },
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
                                    $(nTd).html(`<a href=employees?page=open&id=${oData.employee_id}>${sData}</a>`);
                                    // $(nTd).html(`<a href=/dashboardNaloga/companyNalozi?id=${oData.company_id}&config=0>${sData}</a>`);
                                }else if(iCol > 0 && iCol < 7){
                                    selected_casting = $('#select_casting').val().toString();
                                    if(selected_casting != ''){
                                        castingUrl = '&castingId=' + selected_casting;
                                    }else{
                                        castingUrl = '';
                                    }
                                    $(nTd).html(`<a href=casting_candidates_lists?page=agentCurrent&id=${oData.employee_id}&status=${el.status}${castingUrl} target="_BLANK">${sData}</a>`);
                                }else{
									// $(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus.php?companyId=${oData.company_id}&statusPrijave=${el.status}>${sData}</a>`);
									// $(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus.php?companyId=${oData.company_id}&statusPrijave=${el.status}>${sData}</a>`);
								}
							}
						}

					}
				}),
                {
					"data": "agent_id",
					"title": "Ukupno",
					"status": 6,
					"render": function(data, type, row, meta) {
                        selected_casting = $('#select_casting').val().toString();
                        if(selected_casting != ''){
                            castingUrl = '&castingId=' + selected_casting;
                        }else{
                            castingUrl = '';
                        }
                        return "<b><a href=casting_candidates_lists?page=agentCurrent&id="+data+"&status=6"+castingUrl+" target='_BLANK'>" + (
                                          parseInt(row.pristao) 
                                        + parseInt(row.dolazi) 
                                        + parseInt(row.odustao)
                                        + parseInt(row.dosao)
                                        + parseInt(row.nije_dosao)
                                    ) 
                             + "</a></b>";
					},
					"className": "sumCells"
				},
                {
					"data": "agent_id",
					"title": "Došao (%) Pristao",
					"render": function(data, type, row, meta) {
                        return "<b>" + 
                        (100 * parseInt(row.dosao) / (parseInt(row.dosao) + parseInt(row.nije_dosao))).toFixed(2) 
                             + "</b>";
					},
					"className": "sumCells"
				},
                {
					"data": "agent_id",
					"title": "Došao (%) Ukupno",
					"render": function(data, type, row, meta) {
                        return "<b>" + 
                        (100 * parseInt(row.dosao) / (parseInt(row.dosao) + parseInt(row.nije_dosao) + parseInt(row.odustao))).toFixed(2) 
                             + "</b>";
					},
					"className": "sumCells"
				}
            ],
            
            footer_tds: "<tfoot><tr><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th></tr></tfoot>",

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
                    'Ukupno po statusu'
                );
                var ukupno_total = 0;
                var total_odustao = 0;
                var total_dosao = 0;
                var total_nije_dosao = 0;
                for (var j = 1; j < 6; j++) {

                    // Total over all pages
                    total = api
                        .column( j )
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        } );

                    // Update footer
                    $( api.column( j ).footer() ).html(
                        total
                    );
                    ukupno_total += total;
                    if(j==3){
                        total_odustao = total;
                    }else if(j==4){
                        total_dosao = total;
                    }else if(j==5){
                        total_nije_dosao = total;
                    }
                };

                var pct_pristao = 0;
                var pct_ukupno = 0;
                var total_pristao = total_dosao + total_nije_dosao;
                pct_pristao = (100 * total_dosao / total_pristao).toFixed(2);
                pct_ukupno = (100 * total_dosao / ukupno_total).toFixed(2);
                
                $( api.column( 6 ).footer() ).html(
                    ukupno_total
                );
                $( api.column( 7 ).footer() ).html(
                    pct_pristao
                );
                $( api.column( 8 ).footer() ).html(
                    pct_ukupno
                );
            }),
            apiUrl: "/castingStats/API_agents_current.php?page=currentCastings",
        }
        let linksCurrentConfig = {
            dom: "Blfrtip",
            order: [[0, 'asc']],
            columnDefs: [
                {
                    targets: [1,2,3,4,5,6,7,8],
                    className: 'dt-center',
                    width: `${85.0 / 9.0}%`
                },
                {
                    targets: "_all",
                    orderable: true,
                }
            ],
			columns: [
				
                ...[
					{ data: "lg_url",           title: "Link",                 status: "" },
                    { data: "pristao",          title: "Pristao",               status: 1  },
					{ data: "dolazi",           title: "Dolazi",                status: 2  },
					{ data: "odustao",          title: "Odustao",               status: 3 },
					{ data: "dosao",            title: "Došao",                 status: 4 },
					{ data: "nije_dosao",       title: "Nije došao",            status: 5 },
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
                                    $(nTd).html(`<a href=link_generator.php?page=show_list&id=${oData.id_urla}>${sData}</a>`);
                                    // $(nTd).html(`<a href=/dashboardNaloga/companyNalozi?id=${oData.company_id}&config=0>${sData}</a>`);
                                }else if(iCol > 0 && iCol < 7){
                                    selected_casting = $('#select_casting').val().toString();
                                    if(selected_casting != ''){
                                        castingUrl = '&castingId=' + selected_casting;
                                    }else{
                                        castingUrl = '';
                                    }
                                    $(nTd).html(`<a href=casting_candidates_lists?page=linkCurrent&id=${oData.id_urla}&status=${el.status}${castingUrl} target="_BLANK">${sData}</a>`);
                                }
                                else{
									// $(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus.php?companyId=${oData.company_id}&statusPrijave=${el.status}>${sData}</a>`);
									// $(nTd).html(`<a href=/dashboardNaloga/kandidatiStatus.php?companyId=${oData.company_id}&statusPrijave=${el.status}>${sData}</a>`);
								}
							}
						}

					}
				}),
                {
					"data": "id_urla",
                    "status": 6,
					"title": "Ukupno",
					"render": function(data, type, row, meta) {
                        selected_casting = $('#select_casting').val().toString();
                        if(selected_casting != ''){
                            castingUrl = '&castingId=' + selected_casting;
                        }else{
                            castingUrl = '';
                        }
                        return "<b><a href=casting_candidates_lists?page=linkCurrent&id="+data+"&status=6"+castingUrl+" target='_BLANK'>" + (
                                          parseInt(row.pristao) 
                                        + parseInt(row.dolazi) 
                                        + parseInt(row.odustao)
                                        + parseInt(row.dosao)
                                        + parseInt(row.nije_dosao)
                                    ) 
                             + "</a></b>";
					},
					"className": "sumCells"
				},
                {
					"data": "id_urla",
					"title": "Došao (%) Pristao",
					"render": function(data, type, row, meta) {
                        return "<b>" + 
                        (100 * parseInt(row.dosao) / (parseInt(row.dosao) + parseInt(row.nije_dosao))).toFixed(2) 
                             + "</b>";
					},
					"className": "sumCells"
				},
                {
					"data": "id_urla",
					"title": "Došao (%) Ukupno",
					"render": function(data, type, row, meta) {
                        return "<b>" + 
                        (100 * parseInt(row.dosao) / (parseInt(row.dosao) + parseInt(row.nije_dosao) + parseInt(row.odustao))).toFixed(2) 
                             + "</b>";
					},
					"className": "sumCells"
				}
            ],
            
            footer_tds: "<tfoot><tr><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th></tr></tfoot>",

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
                    'Ukupno po statusu'
                );
                var ukupno_total = 0;
                var total_odustao = 0;
                var total_dosao = 0;
                var total_nije_dosao = 0;
                for (var j = 1; j < 6; j++) {

                    // Total over all pages
                    total = api
                        .column( j )
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        } );

                    // Update footer
                    $( api.column( j ).footer() ).html(
                        total
                    );
                    ukupno_total += total;
                    if(j==3){
                        total_odustao = total;
                    }else if(j==4){
                        total_dosao = total;
                    }else if(j==5){
                        total_nije_dosao = total;
                    }
                };

                var pct_pristao = 0;
                var pct_ukupno = 0;
                var total_pristao = total_dosao + total_nije_dosao;
                pct_pristao = (100 * total_dosao / total_pristao).toFixed(2);
                pct_ukupno = (100 * total_dosao / ukupno_total).toFixed(2);

                $( api.column( 6 ).footer() ).html(
                    ukupno_total
                );

                $( api.column( 7 ).footer() ).html(
                    pct_pristao
                );
                $( api.column( 8 ).footer() ).html(
                    pct_ukupno
                );
            }),
            apiUrl: "/castingStats/API_link_current.php?page=currentCastings",
        }
        const configs = [agentsConfig, linksConfig, agentsCurrentConfig, linksCurrentConfig ];
    </script>
</html>
<?php 
}else{			
    echo '
        <br/>
        <div class="alert material-alert material-alert_danger">
            <h4>NEMATE PRIVILEGIJE!</h4>
            <p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
            <br />
        </div>
';} 
/*
 <script type="text/javascript">
                                $(document).ready(function() {
                                    var table = $('#table_stats').DataTable({
                                        
                                        responsive: true,

                                        "order": [[ 0, "desc" ]],

                                        "bAutoWidth": false,

                                        "aoColumns": [
                                                { "width": "5%" },
                                                { "width": "5%", "bSortable": false },
                                                { "width": "90%" },
                                            ]
                                    });
                                } );
                            </script>

*/


?>
