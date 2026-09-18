<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Task Force Naslovnica</title>
        <link rel="stylesheet" href="dashboardNaloga/style.css?<?php echo time();?>" />

        <?php 
        include("includes/functions.php");
        include("includes/common.php");
        $getEmployeeStatus = explode( ',' , getEmployeeStatus());

        include('includes/head.php');
        
        ?>
        <style>
            .cancel_reservation{
                background-color: #C95C48;
                color: white;
                font-size: 15px;
                padding-bottom: 2px;
                padding-top: 2px;
                padding-left: 6px;
                padding-right: 6px;
                border: 1px solid;
                border-radius: 7px;
            }
        </style>
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
                    <div class="col-xs-12">
                        <div class="content_box" style="margin-bottom: 10px; min-height: 0px;">
                            <div class="row" style="margin-bottom: -45px;">
                                <div id="tf_alert_nema_kandidata">
                                    
                                </div>

                                <div class="col-xs-8">
                                    <h1><i class="fa fa-phone idk_color_green" aria-hidden="true"></i> Task Force</h1>
                                </div>

                                <div class="col-xs-4">
                                    <button id="tf_button_next" class="pull-right btn material-btn material-btn_success main-container__column" aria-hidden="true">
                                        <i class="fa fa-arrow-right" aria-hidden="true"></i> TRAŽI DALJE
                                    </button>
                                </div>

                                <br>
                                <br>
                                <br>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="content_box">
                                            <div id="buttons" style="margin-bottom: 40px !important;">
                                                <button onclick="changeConfig(this, 1)" disabled> Posredovanje</button>
                                                <button onclick="changeConfig(this, 2)"> Obrada</button>
                                                <button onclick="changeConfig(this, 3)"> Casting</button>
                                            </div>
                                            <div id="omotac">
                                                <table id="idk_table" class="display" cellspacing="0" width="100%">
                                                    <thead>
                                                        <tr>
                                                            <th>Ime i prezime</th>
                                                            <th>Nalog</th>
                                                            <th>Vrsta taska</th>
                                                            <th>Task Force Status</th>
                                                            <th>Projekat</th>
                                                            <th>Termin za zvati</th>
                                                            <th>Zadnji agent</th>
                                                            <th>Rezervisani agent</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <script>
                                    let ajaxUrl = "serversidedata.php?page=tf_naslovnica_posredovanje";
                                    function changeConfig(button, configIndex) {
                                        $("#buttons").children().removeAttr("disabled");
			                            button.setAttribute("disabled", "");
                                        if(configIndex == 1){
                                            ajaxUrl = "serversidedata.php?page=tf_naslovnica_posredovanje";
                                        }else if(configIndex == 2){
                                            ajaxUrl = "serversidedata.php?page=tf_naslovnica_obrada";
                                        }else if(configIndex == 3){
                                            ajaxUrl = "serversidedata.php?page=tf_naslovnica_casting";   
                                        }
                                        else{
                                            ajaxUrl = "serversidedata.php?page=tf_naslovnica_posredovanje";
                                        }
                                        const dataTable = $('#idk_table').DataTable();

                                        dataTable.ajax.url(ajaxUrl).load();
                                    }
                                    const employee_status = <?php echo json_encode($getEmployeeStatus); ?>;

                                    function reloadTable(){
                                        destroyTable();
                                        $('#idk_table').DataTable({
                                            columnDefs: [
                                                {
                                                    className: 'dt-center', targets: '_all'
                                                },
                                                {
                                                    target: [ 8,9 ],
                                                    visible: false,
                                                    searchable: false,
                                                },
                                                {
                                                    targets: 5,
                                                    "createdCell": function (td, cellData, rowData, row, col) {
                                                        candidate = rowData[8];
                                                        is_reserved = rowData[9];
                                                        task_id = rowData[10];
                                                        candidate_appointment = cellData;
                                                        
                                                        if(candidate_appointment != null && employee_status.includes('1')){
                                                            load_time = Math.floor(Date.now()/1000/60);
                                                            candidate_appointment = Math.floor(new Date(candidate_appointment).getTime()/1000/60);

                                                            const minute_difference = load_time - candidate_appointment;
                                                            
                                                            if(minute_difference >= 30 && is_reserved){
                                                                $(td).css('color', 'red');
                                                                $(td).append(' <button class = "cancel_reservation" candidate_id = "'+candidate+'" task_id = "'+task_id+'" onclick="cancelReservation(this)"><i class="fa fa-ban" aria-hidden="true"></i></button>');
                                                            }
                                                            if(is_reserved){
                                                                $(td).append(' <button class = "change_reservation" candidate_id = "'+candidate+'" task_id = "'+task_id+'" onclick="changeReservation(this)"><i class="fa fa-exchange" aria-hidden="true"></i></button>');
                                                            }
                                                        }
                                                    }
                                                }
                                            ],

                                            responsive: true,

                                            "pageLength": 10,

                                            "processing": true,

                                            "serverSide": true,

                                            "order": [],

                                            "ajax": {
                                                url: ajaxUrl,
                                                type: "POST"
                                            }
                                        });
                                    }

                                    function destroyTable() {
                                        if ($.fn.DataTable.isDataTable('#idk_table')) {
                                            $("#idk_table").DataTable().clear().destroy();
                                            $("#idk_table").empty();
                                        }
                                    }

                                    let currentChangeData = {
                                        candidate_id: null,
                                        task_id: null,
                                        element: null
                                    };

                                    function cancelReservation(element){
                                        candidate_id = $(element).attr('candidate_id');
                                        task_id = $(element).attr('task_id');
                                        $(element).prop('disabled', true);
                                        
                                        $.ajax({
                                            url: 'ajax_data.php?page=check_current_call',
                                            type: 'POST',
                                            dataType: 'html',
                                            data:{
                                                'candidate_id' : candidate_id
                                            },
                                            success : function (response){
                                                console.log(parseInt(response));
                                                if(parseInt(response) == 0){

                                                    $.ajax({
                                                        url: 'ajax_data.php?page=check_task_finished',
                                                        type: 'POST',
                                                        dataType: 'html',
                                                        data:{
                                                            'task_id' : task_id
                                                        },
                                                        success : function (response){
                                                            console.log(parseInt(response));
                                                            if(parseInt(response) == 1){

                                                                $.ajax({
                                                                    url: 'ajax_data.php?page=cancel_reservation',
                                                                    type: 'POST',
                                                                    dataType: 'html',
                                                                    data:{
                                                                        'candidate_id' : candidate_id
                                                                    },
                                                                    success : function (response){
                                                                        $(element).parent().css('color', 'green');
                                                                        $(element).hide('fade');
                                                                    },
                                                                    error: function (xhr, ajaxOptions, thrownError) {
                                                                        alert(xhr.status);
                                                                        alert(thrownError);
                                                                    }
                                                                });
                                                            }else{
                                                                alert('Agent je završio sa ovim taskom! Osvježite stranicu.')
                                                            }
                                                        },
                                                        error: function (xhr, ajaxOptions, thrownError) {
                                                            alert(xhr.status);
                                                            alert(thrownError);
                                                        }
                                                    });
                                                }else{
                                                    alert('Agent je trenutno u pozivu sa kandidatom! Osvježite stranicu.')
                                                }
                                            },
                                            error: function (xhr, ajaxOptions, thrownError) {
                                                alert(xhr.status);
                                                alert(thrownError);
                                            }
                                           
                                        });
                                    }

                                    function changeReservation(element){
                                        // Save element and data
                                        currentChangeData.element = element;
                                        currentChangeData.candidate_id = $(element).attr('candidate_id');
                                        currentChangeData.task_id = $(element).attr('task_id');

                                        const candidateId = $(element).attr('candidate_id');
                                        $('#modal_candidate_id').val(candidateId);

                                        $.ajax({
                                            url: 'ajax_data.php?page=get_reserved_agent_for_candidate',
                                            type: 'POST',
                                            dataType: 'html',
                                            data: {'candidate_id': candidateId},
                                            success: function(response){
                                                $('#modal_candidate_name').html('Trenutni agent: <b>' + response + '</b>');
                                            },
                                            error: function() {
                                                $('#modal_candidate_name').html('Greška pri učitavanju informacija.');
                                            }
                                        });

                                        // Reset and show modal
                                        $('#agentSelect').val('');
                                        $('#changeModal').show();
                                    }
                                    function closeChangeModal() {
                                        $('#changeModal').hide();
                                        currentChangeData = { candidate_id: null, task_id: null, element: null };
                                    }

                                    function submitAgentChange() {
                                        const agent_id = $('#agentSelect').val();

                                        if(!agent_id) {
                                            alert("Odaberite agenta!");
                                            return;
                                        }

                                        const { candidate_id, task_id, element } = currentChangeData;
                                        $(element).prop('disabled', true);

                                        $.ajax({
                                            url: 'ajax_data.php?page=check_current_call',
                                            type: 'POST',
                                            dataType: 'html',
                                            data: { 'candidate_id': candidate_id },
                                            success: function(response){
                                                if (parseInt(response) === 0){
                                                    $.ajax({
                                                        url: 'ajax_data.php?page=check_task_finished',
                                                        type: 'POST',
                                                        dataType: 'html',
                                                        data: { 'task_id': task_id },
                                                        success: function(response){
                                                            if(parseInt(response) === 1){
                                                                $.ajax({
                                                                    url: 'ajax_data.php?page=change_reservation',
                                                                    type: 'POST',
                                                                    dataType: 'html',
                                                                    data: {
                                                                        'candidate_id': candidate_id,
                                                                        'new_agent_id': agent_id
                                                                    },
                                                                    success: function(response) {
                                                                        $(element).parent().css('color', 'blue');
                                                                        $(element).hide('fade');
                                                                        $(element).closest('tr').find('td').eq(7).text(response);
                                                                        $(element).closest('tr').find('td').eq(7).css('color', 'blue');
                                                                        closeChangeModal();
                                                                    },
                                                                    error: function(xhr, ajaxOptions, thrownError){
                                                                        alert(xhr.status);
                                                                        alert(thrownError);
                                                                    }
                                                                });
                                                            }else{
                                                                alert('Agent je završio sa ovim taskom! Osvježite stranicu.');
                                                            }
                                                        }
                                                    });
                                                }else{
                                                    alert('Agent je trenutno u pozivu sa kandidatom! Osvježite stranicu.');
                                                }
                                            },
                                            error: function (xhr, ajaxOptions, thrownError) {
                                                alert(xhr.status);
                                                alert(thrownError);
                                            }
                                        });
                                    }

                                    $(document).ready(function () {

                                        
                                        $('#tf_button_next').on('click', () => {
                                            $.ajax({
                                                type: "POST",
                                                url: "/task_force_queue.php",
                                        
                                                statusCode: {
                                                    
                                                    200: (data) => {
                                                        if(data.category_id == 3){ // 3 - Dipl
                                                            window.location.replace('<?php getSiteURL(); ?>nostrifikacija_diploma?page=otvori_ND_kandidata&id=' + data.dipl_id + '&vrsta_id=' + data.vrsta_id);
                                                        }else{
                                                            window.location.replace('<?php getSiteURL(); ?>kandidati?page=open&id=' + data.kandidat_id + '&projekt_id=' +  data.projekt_id + '&nalog_id=' + data.nalog_id + '&vrsta_id=' + data.vrsta_id);
                                                        }
                                                    }

                                                },

                                                complete: (xhr) => {
                                                    
                                                    if (xhr.status == 200) {
                                                        if(data.category_id == 3){
                                                            window.location.replace('<?php getSiteURL(); ?>nostrifikacija_diploma?page=otvori_ND_kandidata&id=' + data.dipl_id + '&vrsta_id=' + data.vrsta_id);
                                                        }else{
                                                            window.location.replace('<?php getSiteURL(); ?>kandidati?page=open&id=' + data.kandidat_id + '&projekt_id=' +  data.projekt_id + '&nalog_id=' + data.nalog_id + '&vrsta_id=' + data.vrsta_id);
                                                        }
                                                    } else {
                                                        document.querySelector("#tf_alert_nema_kandidata").innerHTML = '<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>Nema kandidata.</strong></div>';
                                                    }

                                                }
                                            });

                                        });   

                                        reloadTable();
                                    });
                                </script>

                                <br>
                                <br>
                               <!-- Change Agent Modal -->
                                <div id="changeModal" class="modal" style="display:none; position: fixed; z-index: 1000; top: 0; left: 0; width:100%; height:100%; background: rgba(0,0,0,0.6);">
                                    <div style="background: white; padding: 20px; max-width: 400px; margin: 100px auto; border-radius: 5px;">
                                        <h4>Promijeni agenta</h4>
                                        <input type="hidden" id="modal_candidate_id" value="">
                                        <div id="modal_candidate_name"></div>
                                        <label for="agentSelect">Odaberi novog agenta:</label>
                                        <?php 
                                        $query = $db -> prepare('
                                            SELECT employee_id, CONCAT(employee_firstname," ",employee_lastname) as employee_fullname
                                            FROM idk_employees
                                            
                                            WHERE (employee_status != 0 AND FIND_IN_SET("18", employee_status)) 
                                            GROUP BY idk_employees.employee_id');
                    
                                        $query -> execute();
                                        ?>
                                        <select id="agentSelect" style="width: 100%; padding: 5px; margin: 10px 0;">
                                            <option value="">-- Odaberi --</option>
                                            <?php 
                                            while($row = $query->fetch()){
                                                echo '<option value="'.$row['employee_id'].'">'.$row['employee_fullname'].'</option>';
                                            }
                                            ?>
                                        </select>

                                        <div style="text-align: right;">
                                            <button onclick="submitAgentChange()" class="btn btn-primary">Spremi</button>
                                            <button onclick="closeChangeModal()" class="btn btn-secondary">Odustani</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>