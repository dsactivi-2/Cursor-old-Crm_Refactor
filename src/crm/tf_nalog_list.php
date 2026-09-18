<?php
include("includes/functions.php");
include("includes/common.php");
// Turn off all error reporting
error_reporting(0);
$getEmployeeStatus = getEmployeeStatus();

if(isset($_REQUEST["page"])) {
    $page = $_REQUEST["page"];
}else{
    $page = "list";
    header("Location: tf_nalog_list?page=list");
}
    
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Task Force Nalozi</title>

    <?php include('includes/head.php'); 
if (in_array($getUserIp, $getIpWhiteList)){ ?>

    <script src="<?php getSiteURL(); ?>js/sortable.min.js"></script>
    <link rel="stylesheet" href="dashboardNaloga/style.css" />
</head>
<body>
	<header>
		<?php include('header.php'); ?>
	</header>
	<div id="sidebar">
		<?php include('menu.php'); ?>
	</div>
	<div id="content">
    <?php
    switch ($page){		
        case "list":
    ?>   
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-8">
                    <h1><i class="fa fa-tty idk_color_green" aria-hidden="true"></i> Aktivacija i deaktivacija naloga za Task Force:  </h1>
                </div>
                <div class="col-xs-12">
                    <hr />
                </div>
            </div>
            <div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<div class="row">
							<div class="col-xs-12">
								<?php
									if(isset($_GET['mess'])) {
										$mess = $_GET['mess'];
									}else{
										$mess = 0;
									}

									if($mess == 1){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novu kompaniju.</div>';
									}elseif($mess == 3){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste arhivirali profil kompanije.</div>';
									}
								?>
								<script type="text/javascript">
									$(document).ready(function() {
										var table = $('#idk_table').DataTable({
											responsive: true,
											"order": [[ 3, "asc" ], [0, "desc"]],
											"bAutoWidth": false,
											"aoColumns": [
													{ "width": "5%" },
													{ "width": "25%" },
													{ "width": "10%" },
													{ "width": "10%" },
													{ "width": "10%" },
													{ "width": "5%", "bSortable": false }
												]
										});

										$('#table-filter').on('change', function(){
											table.search(this.value).draw();   
										});
									} );
								</script>
								<table id="idk_table" class="display" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th class="text-center">Broj</th>
											<th class="text-center">Naziv</th>
											<th class="text-center">Status</th>
											<th class="text-center">TF prioritet</th>
											<th class="text-center">Agenti</th>
											<th class="text-center">Akcija</th>
										</tr>
									</thead>
									<tbody>
										<?php
											$query = $db->prepare("
                                                    SELECT nalog_id, kompanija_id, nalog_broj, nalog_naziv, nalog_opis, nalog_kreirano, nalog_status, nalog_ugovor, employee_id, nalog_financije, nalog_prioritet
                                                    FROM idk_nalozi
                                                    WHERE nalog_status NOT IN (8,12) AND pristup_poslodavcima = 1 ORDER BY idk_nalozi.nalog_prioritet DESC");

											$query->execute();

											while($row = $query->fetch()){

                                                $nalog_id = $row['nalog_id'];
												$nalog_broj = $row['nalog_broj'];
												$kompanija_id = $row['kompanija_id'];
												$nalog_naziv = $row['nalog_naziv'];
												$nalog_ugovor = $row['nalog_ugovor'];
                                                $nalog_status = $row['nalog_status'];
                                                $employee_id = $row['employee_id'];
                                                $nalog_financije = $row['nalog_financije'];
                                                $nalog_prioritet = intval($row['nalog_prioritet']);
                                                $nalog_kreirano = date('d.m.Y.', strtotime($row['nalog_kreirano']));

                                                $agent_count = getUniqueAgentCountForNalog($nalog_id);

                                                if($agent_count > 0){
                                                    $agent_info = '<a href="#" class="dodaj_agente" nalog_id="'.$nalog_id.'" agent_count="'.$agent_count.'" data-toggle="modal"><span class="label label-success">'.$agent_count.'<span></a>';
                                                }else{
                                                    $agent_info = '<a href="#" class="dodaj_agente" nalog_id="'.$nalog_id.'" agent_count="'.$agent_count.'" data-toggle="modal"><span class="label label-danger"> Nije podešeno <span></a>';
                                                }

                                                if($nalog_status == 1){
                                                    $nalog_status_txt = '<span id="nalog_status'.$nalog_id.'" class="label label-primary">Potpis</span>';
                                                }elseif($nalog_status == 2){
                                                    $nalog_status_txt = '<span id="nalog_status'.$nalog_id.'" class="label label-warning">Čeka se uplata</span>';
                                                }elseif($nalog_status == 3){
                                                    $nalog_status_txt = '<span id="nalog_status'.$nalog_id.'" class="label label-primary">Marketing</span>';
                                                }elseif($nalog_status == 4){
                                                    $nalog_status_txt = '<span id="nalog_status'.$nalog_id.'" class="label label-warning">Prijave u toku</span>';
                                                }elseif($nalog_status == 5){
                                                    $nalog_status_txt = '<span id="nalog_status'.$nalog_id.'" class="label label-primary">Obrada prijava</span>';
                                                }elseif($nalog_status == 6){
                                                    $nalog_status_txt = '<span id="nalog_status'.$nalog_id.'" class="label label-warning">Nalog kod poslodavca</span>';
                                                }elseif($nalog_status == 7){
                                                    $nalog_status_txt = '<span id="nalog_status'.$nalog_id.'" class="label label-primary">Casting</span>';
                                                }elseif($nalog_status == 8){
                                                    $nalog_status_txt = '<span id="nalog_status'.$nalog_id.'" class="label label-success">Završeno</span>';
                                                }elseif($nalog_status == 9){
                                                    $nalog_status_txt = '<span id="nalog_status'.$nalog_id.'" class="label label-danger">Na čekanju</span>';
                                                }elseif($nalog_status == 10){
                                                    $nalog_status_txt = '<span id="nalog_status'.$nalog_id.'" class="label label-success">Kandidati u odlasku</span>';
                                                }elseif($nalog_status == 11){
                                                    $nalog_status_txt = '<span id="nalog_status'.$nalog_id.'" class="label label-info">Završeno (nenaplaćeno)</span>';
                                                }elseif($nalog_status == 12){
                                                    $nalog_status_txt = '<span id="nalog_status'.$nalog_id.'" class="label label-danger">Arhiviran<span>';
                                                }

                                                if($nalog_prioritet){
                                                    $nalog_prioritet_txt = '<a href="#" class="promjeni_prioritet" nalog_id="'.$nalog_id.'" data-toggle="modal" data-target="#modal_promjeni_prioritet"><span class="label label-success">'.$nalog_prioritet.'<span></a>';
                                                    $dataorder = $nalog_prioritet;
                                                    $dugme_akcije = '<button href="#" title="Ukloni iz TF-a" class="btn material-btn material-btn_danger main-container__column deaktiviraj_nalog_button" id="deaktiviraj_nalog_button" nalog_id="'.$nalog_id.'" data-toggle="modal" data-target="#modal_nalog_potvrdi_deaktiviranje_naloga"><i class="fa fa-trash" aria-hidden="true"></i></button>';
                                                }else{
                                                    $nalog_prioritet_txt = '<a href="#" class="promjeni_prioritet" nalog_id="'.$nalog_id.'" data-toggle="modal" data-target="#modal_promjeni_prioritet"><span class="label label-danger"> Nema <span></a>';
                                                    $dataorder = 99;
                                                    $dugme_akcije = '<button href="#" title="Dodaj u TF" class="btn material-btn material-btn_success main-container__column aktiviraj_nalog_button" id="aktiviraj_nalog_button" nalog_id="'.$nalog_id.'" data-toggle="modal" data-target="#modal_nalog_potvrdi_aktiviranje_naloga"><i class="fa fa-check" aria-hidden="true"></i></button>';
                                                }
                                                
										?>
										<tr>
											<td class="text-center"><?php echo $nalog_broj; ?></td>
											<td class="text-center"><a href="<?php getSiteURL(); ?>nalozi?page=open&id=<?php echo $nalog_id; ?>"><?php echo $nalog_naziv; ?></td></a>
											<td class="text-center" style="padding:10px !important"><?php echo $nalog_status_txt; ?></td>
											<td class="text-center" data-order="<?php echo $dataorder; ?>"><?php echo $nalog_prioritet_txt; ?></td>
											<td class="text-center"><?php echo $agent_info; ?></td>
                                            <td class="text-center"><?php echo $dugme_akcije; ?></td>
										</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
                            <div class="modal material-modal material-modal_info fade text-left" id="modal_promjeni_prioritet">
                                <div class="modal-dialog ">
                                    <div class="modal-content material-modal__content">
                                        <div class="modal-header material-modal__header">
                                            <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title material-modal__title"><b>Unesite novi prioritet i odaberite spremi</b><h4>
                                        </div>
                                        <div class="modal-body material-modal__body text-center">
                                            <input type="hidden" id="clicked_nalog_id_change">

                                            <div class="form-group">
                                                <label for="kki_grupa" class="col-sm-4 control-label"><span class="text-danger">*</span> Novi prioritet:</label>
                                                
                                                <div class="col-sm-8">
                                                    <input class="form-control materail-input" type="number" name="novi_prioritet" id="novi_prioritet" placeholder="Unesite novi prioriret">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer material-modal__footer">
                                            <button class="btn material-btn material-btn" data-dismiss="modal" id="button_promjeni_prioritet_odustani">Odustani</button>
                                            <button type="submit" class="btn btn-primary material-btn material-btn_danger" id="button_promjeni_prioritet_potvrda">Potvrdi</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <script>
                                $('.promjeni_prioritet').on('click', function () {
                                    $('#clicked_nalog_id_change').val($(this).attr('nalog_id'));
                                    
                                });

                                $('#button_promjeni_prioritet_odustani').on('click', function () {
                                    $('#clicked_nalog_id_change').empty();
                                    $('#novi_prioritet').val("");
                                    console.log($("#novi_prioritet"));
                                });

                                $('#button_promjeni_prioritet_potvrda').on('click', function () {
                                    var nalog_id = $('#clicked_nalog_id_change').val();	
                                    var nalog_prioritet = $('#novi_prioritet').val();
                                    if(nalog_prioritet){	
                                        $.ajax({
                                            url: 'ajax_data.php?page=change_tf_prioritet',
                                            type: 'POST',
                                            data: {	
                                                'nalog_id' :nalog_id,
                                                'nalog_prioritet' :nalog_prioritet
                                            },
                                            dataType: 'html',
                                            success: function(text) {
                                                $("#modal_promjeni_prioritet").modal('hide');
                                                window.location.reload();
                                            },
                                            error: function (xhr, ajaxOptions, thrownError) {
                                                alert(xhr.status);
                                                alert(thrownError);
                                            }
                                        });
                                    }else{
                                        alert("Nalog prioritet ne može biti prazan.")
                                    }
                                });
                            </script>
                            
                            <div class="modal material-modal material-modal_danger fade text-left" id="modal_nalog_potvrdi_deaktiviranje_naloga"  data-backdrop="static">
                                <div class="modal-dialog ">
                                    <div class="modal-content material-modal__content">
                                        <div class="modal-header material-modal__header">
                                            <button class="deaktiviraj_nalog_button_cancel close material-modal__close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title material-modal__title"><b>Jeste li sigurni da želite ukloniti ovaj nalog iz Task Force ?</b><h4>
                                        </div>
                                        <div class="modal-body material-modal__body text-center">
                                            <input type="hidden" id="clicked_nalog_id_deactivate">
                                            <div>
                                            <button id="deaktiviraj_nalog_button_confirm" class="btn material-btn material-btn_success" style="margin-right:20px;">
                                                <i class="fa fa-trash" style="font-size: xxx-medium;" aria-hidden="true"></i><br>
                                                <span style="font-size:10px;">DA</span>
                                            </button>
                                            
                                            <button id="deaktiviraj_nalog_button_cancel" class="deaktiviraj_nalog_button_cancel btn material-btn material-btn_danger" style="margin-left:20px;">
                                                <i class="fa fa-times" style="font-size: xxx-medium;" aria-hidden="true"></i><br>
                                                <span style="font-size:10px;">NE</span>
                                            </button>
                                            </div>
                                            <div class="material-modal__body text-center">
                                            <div id="list_of_connected_candidates"></div>
                                            <button id="confirmation" class="btn material-btn material-btn_danger" style="margin-right:20px;display:none;width:100%;">
                                                <i class="fa fa-trash" style="font-size: xxx-medium;" aria-hidden="true"></i><br>
                                                <span style="font-size:10px;">Ipak deaktiviraj</span>
                                            </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <script>
                                $('.deaktiviraj_nalog_button').on('click', function () {
                                    $('#clicked_nalog_id_deactivate').val($(this).attr('nalog_id'));
                                    
                                });
                                
                                $('.deaktiviraj_nalog_button_cancel').on('click', function () {
                                    $("#confirmation").css("display", "none");
                                    $('#clicked_nalog_id_deactivate').empty();
                                    $('#list_of_connected_candidates').empty();
                                    $("#modal_nalog_potvrdi_deaktiviranje_naloga").modal('hide');
                                });
                                $('#deaktiviraj_nalog_button_confirm').on('click', function () {
                                    var nalog_id = $('#clicked_nalog_id_deactivate').val();	
                                    $.ajax({
                                        url: 'ajax_data.php?page=check_tf_nalog_candidates_on_status',
                                        type: 'POST',
                                        data: {	
                                            'nalog_id' :nalog_id
                                        },
                                        dataType: 'html',
                                        success: function(text) {
                                            let imaNaPristaoDolazi = text;
                                                $.ajax({
                                                    url: 'ajax_data.php?page=deactivate_tf_nalog',
                                                    type: 'POST',
                                                    data: {	
                                                        'nalog_id' :nalog_id
                                                    },
                                                    dataType: 'html',
                                                    success: function(text) {
                                                        if(text == -1){
                                                            $("#modal_nalog_potvrdi_deaktiviranje_naloga").modal('hide');
                                                            window.location.reload();
                                                        }else{
                                                            if(imaNaPristaoDolazi == 0){
                                                            $("#confirmation").css("display", "block");
                                                            }
                                                        }
                                                        $('#list_of_connected_candidates').html(text);
                                                    },
                                                    error: function (xhr, ajaxOptions, thrownError) {
                                                        alert(xhr.status);
                                                        alert(thrownError);
                                                    }
                                                });
                                        },
                                        error: function (xhr, ajaxOptions, thrownError) {
                                            alert(xhr.status);
                                            alert(thrownError);
                                        }
                                    });
                                });

                                $('#confirmation').on('click', function () {
                                    var nalog_id = $('#clicked_nalog_id_deactivate').val();	
                                    $.ajax({
                                        url: 'ajax_data.php?page=deactivate_tf_nalog',
                                        type: 'POST',
                                        data: {	
                                            'nalog_id' :nalog_id, 'confirmation' : true
                                        },
                                        dataType: 'html',
                                        success: function(text) {
                                            if(text == -1){
                                                $("#modal_nalog_potvrdi_deaktiviranje_naloga").modal('hide');
                                                window.location.reload();
                                            }else{                                           }
                                                $('#list_of_connected_candidates').html(text);
                                        },
                                        error: function (xhr, ajaxOptions, thrownError) {
                                            alert(xhr.status);
                                            alert(thrownError);
                                        }
                                    });
                                });
                            </script>

                            <div class="modal material-modal material-modal_success fade text-left" id="modal_nalog_potvrdi_aktiviranje_naloga">
                                <div class="modal-dialog ">
                                    <div class="modal-content material-modal__content">
                                        <div class="modal-header material-modal__header">
                                            <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title material-modal__title"><b>Jeste li sigurni da želite dodati ovaj nalog u Task Force ?</b><h4>
                                        </div>
                                        <div class="modal-body material-modal__body text-center">
                                            <input type="hidden" id="clicked_nalog_id_activate">
                                            <button id="aktiviraj_nalog_button_confirm" class="btn material-btn material-btn_success" style="margin-right:20px;">
                                                <i class="fa fa-trash" style="font-size: xxx-medium;" aria-hidden="true"></i><br>
                                                <span style="font-size:10px;">DA</span>
                                            </button>
                                            <button id="aktiviraj_nalog_button_cancel" class="btn material-btn material-btn_danger" style="margin-left:20px;">
                                                <i class="fa fa-times" style="font-size: xxx-medium;" aria-hidden="true"></i><br>
                                                <span style="font-size:10px;">NE</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <script>
                                $('.aktiviraj_nalog_button').on('click', function () {
                                    $('#clicked_nalog_id_activate').val($(this).attr('nalog_id'));
                                    
                                });
                                
                                $('#aktiviraj_nalog_button_cancel').on('click', function () {
                                    $('#clicked_nalog_id_activate').empty();
                                    $("#modal_nalog_potvrdi_aktiviranje_naloga").modal('hide');
                                });
                                $('#aktiviraj_nalog_button_confirm').on('click', function () {
                                    var nalog_id = $('#clicked_nalog_id_activate').val();
                                    $.ajax({
                                        url: 'ajax_data.php?page=activate_tf_nalog',
                                        type: 'POST',
                                        data: {	
                                            'nalog_id' :nalog_id
                                        },
                                        dataType: 'html',
                                        success: function(text) {
                                            $("#modal_nalog_potvrdi_aktiviranje_naloga").modal('hide');
                                            window.location.reload();
                                        },
                                        error: function (xhr, ajaxOptions, thrownError) {
                                            alert(xhr.status);
                                            alert(thrownError);
                                        }
                                    });
                                });
                            </script>
                            
                            <div class="modal material-modal material-modal_success fade text-left" id="modal_dodaj_agente" data-backdrop="static">
                                <div class="modal-dialog ">
                                    <div class="modal-content material-modal__content">
                                        <div class="modal-header material-modal__header">
                                            <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title material-modal__title"><b>Lista agenata</b><h4>
                                        </div>
                                        <div class="modal-body material-modal__body text-center">
                                            <input type="hidden" id="nalog_id_agents">

                                            <div class="form-group agent_list_div">
                                                
                                            </div>
                                            <div class="form-group" style="margin-top:20px; margin-bottom:20px;">
                                            <button class="dodaj_nove_agente btn material-btn material-btn_secondary">
                                                <i class="fa fa-plus" style="font-size: xxx-medium;" aria-hidden="true"></i><br>
                                                <span style="font-size:10px;">Dodaj nove agente</span>
                                            </button>
                                            
                                            <button class="prekini_dodavanje_agenata btn material-btn material-btn_secondary" style="display:none;">
                                                <i class="fa fa-times" style="font-size: xxx-medium;" aria-hidden="true"></i><br>
                                                <span style="font-size:10px;">Prekini dodavanje</span>
                                            </button>
                                            </div>
                                            <div class="form-group add_new_category" style="display:none; margin-top:40px; margin-bottom: 20px;">
                                                <label for="filter_category" class="col-sm-4 control-label">Odaberi kategoriju:</label>
                                                <div class="col-sm-5">
                                                    <select class="selectpicker filter_category" id="filter_category" name="filter_category" data-live-search="true" data-actions-box="true">
                                                        <option disabled>Odaberi</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group add_new_agent" style="display:none; margin-top:40px; margin-bottom: 20px;">
                                                <label for="filter_agents" class="col-sm-4 control-label">Dodaj novog agenta:</label>
                                                <div class="col-sm-5">
                                                    <select class="selectpicker filter_agents" id="filter_agents" name="filter_agents[]" data-live-search="true" data-actions-box="true" multiple>
                                                        
                                                    </select>
                                                </div>
                                                <button class="save_new_agents btn material-btn material-btn_success" style="display:none;">Spremi</button>
                                            </div>
                                        </div>
                                        <div class="modal-footer material-modal__footer" style="margin-top: 20px;">
                                            <button class="btn material-btn material-btn" data-dismiss="modal" id="button_cancel_dodaj_agente">Zatvori</button>
                                            <!-- <button style="display:none;" type="submit" class="btn btn-danger material-btn material-btn_danger button_ukloni_sve_agente">Ukloni sve agente</button> -->
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <script>
                                $('.dodaj_agente').on('click', function () {

                                    nalog_id = $(this).attr('nalog_id');
                                    agent_count = $(this).attr('agent_count');

                                    $('#nalog_id_agents').val(nalog_id);
                                    $("#modal_dodaj_agente").modal('show');
                                    $('.agent_list_div').empty();

                                    if(agent_count > 0){
                                        // $('.button_ukloni_sve_agente').css('display', 'inline-block');

                                        $.ajax({
                                            url: 'ajax_data.php?page=get_agents_for_nalog',
                                            type: 'POST',
                                            data: {	
                                                'nalog_id' :nalog_id
                                            },
                                            dataType: 'html',
                                            success: function(data) {
                                                var agents = JSON.parse(data);
                                                var currentVrstaName = null;

                                                agents.forEach(function(agent) {
                                                    var tfan_id = agent.tfan_id;
                                                    var tfan_nalog = agent.tfan_nalog;
                                                    var tfan_agent = agent.tfan_agent;
                                                    var tfan_vrsta_id = agent.tfan_vrsta_id;
                                                    var tfan_vrsta_name = agent.tf_vrsta_name;
                                                    var employee_fullname = agent.employee_fullname;

                                                    if (tfan_vrsta_name !== currentVrstaName) {
                                                        // Append the vrsta name only if it has changed
                                                        currentVrstaName = tfan_vrsta_name;
                                                        var vrstaNameElement = '<h3>' + tfan_vrsta_name + '</h3>';
                                                        $('.agent_list_div').append(vrstaNameElement);
                                                    }


                                                    // Do something with the agent properties (e.g., append them to a container)
                                                    var agent_info = '<span class="agent-container agent'+tfan_agent+' vrsta_id'+tfan_vrsta_id+'" style="display: inline-block; margin-top: 5px; padding: 5px 10px; border: 1px solid #ccc; border-radius: 5px; margin-right: 10px; position: relative;">' +
                                                                    employee_fullname +
                                                                    '<span class="remove_agent" agent_id="' + tfan_agent + '" nalog_id="' + tfan_nalog + '"  vrsta_id="' + tfan_vrsta_id + '" style="margin-left: 5px; transform: translateY(-50%); cursor: pointer;">X</span></span>';
                                                    $('.agent_list_div').append(agent_info);
                                                });
                                            },
                                            error: function (xhr, ajaxOptions, thrownError) {
                                                alert(xhr.status);
                                                alert(thrownError);
                                            }
                                        });
                                    }else{
                                        // $('.button_ukloni_sve_agente').css('display', 'none');
                                        $('.agent_list_div').append('<span style="display: inline-block; margin-top: 5px; padding: 5px 10px; border: 1px solid #ccc; border-radius: 5px; margin-right: 10px; position: relative;"> Nema agenata </span>');
                                    }
                                    
                                });

                               // Attach click event to the parent element of the dynamically added .remove_agent spans
                                $('.agent_list_div').on('click', '.remove_agent', function() {
                                    var agent_id = $(this).attr('agent_id');
                                    var nalog_id = $(this).attr('nalog_id');
                                    var vrsta_id = $(this).attr('vrsta_id');

                                    $.ajax({
                                        url: 'ajax_data.php?page=remove_agent_from_nalog',
                                        type: 'POST',
                                        data: {	
                                            'nalog_id' :nalog_id,
                                            'agent_id' :agent_id,
                                            'vrsta_id' :vrsta_id
                                        },
                                        dataType: 'html',
                                        success: function(data) {
                                            $('.agent_list_div').find('.agent-container.agent' + agent_id+'.vrsta_id' + vrsta_id).remove();
                                        },
                                        error: function (xhr, ajaxOptions, thrownError) {
                                            alert(xhr.status);
                                            alert(thrownError);
                                        }
                                    });
                                 });
                        
                                
                                 $('#button_cancel_dodaj_agente').on('click', function () {
                                    $('#nalog_id_agents').empty();	
                                    $('.agent_list_div').empty();
                                    $("#modal_dodaj_agente").modal('hide');
                                });

                                // $('.button_ukloni_sve_agente').on('click', function () {
                                //     var nalog_id = $('#nalog_id_agents').val();	
                                    
                                //     $.ajax({
                                //         url: 'ajax_data.php?page=remove_all_agents_from_nalog',
                                //         type: 'POST',
                                //         data: {	
                                //             'nalog_id' :nalog_id
                                //         },
                                //         dataType: 'html',
                                //         success: function(text) {
                                //             $("#modal_dodaj_agente").modal('hide');
                                //             window.location.reload();
                                //         },
                                //         error: function (xhr, ajaxOptions, thrownError) {
                                //             alert(xhr.status);
                                //             alert(thrownError);
                                //         }
                                //     });
                                // });

                                $('.dodaj_nove_agente').on('click', function (){

                                    $(this).css('display', 'none');
                                    $('.prekini_dodavanje_agenata').css('display', 'inline-block');
                                    $('.add_new_category').css('display', 'block');

                                    $.ajax({
                                        url: 'ajax_data.php?page=get_tf_vrsta_option',
                                        type: 'POST',
                                        dataType: 'html',
                                        success: function(data) {
                                            var vrste = JSON.parse(data);
                                            vrste.forEach(function(vrsta) {
                                                var vrsta_id = vrsta.id;
                                                var vrsta_name = vrsta.name;
                                                // Do something with the agent properties (e.g., append them to a container)
                                                var vrsta_option = '<option value="' + vrsta_id + '">' + vrsta_name + '</option>';
                                                $('#filter_category').append(vrsta_option);
                                            });
                                            $('.filter_category').selectpicker('refresh');
                                        },
                                        error: function (xhr, ajaxOptions, thrownError) {
                                            alert(xhr.status);
                                            alert(thrownError);
                                        }
                                    });

                                    

                                });

                                $('#filter_category').on('change', function (){
                                    $('.add_new_agent').css('display', 'block');

                                    var nalog_id = $('#nalog_id_agents').val();	
                                    var vrsta_id = $('#filter_category').val();	

                                    $.ajax({
                                        url: 'ajax_data.php?page=get_free_agents_for_nalog',
                                        type: 'POST',
                                        data: {	
                                            'nalog_id' :nalog_id,
                                            'vrsta_id' :vrsta_id
                                        },
                                        dataType: 'html',
                                        success: function(data) {
                                            var agents = JSON.parse(data);
                                            $('#filter_agents').empty();

                                            agents.forEach(function(agent) {
                                                var employee_id = agent.employee_id;
                                                var employee_fullname = agent.employee_fullname;
                                                // Do something with the agent properties (e.g., append them to a container)
                                                var agent_option = '<option value="' + employee_id + '">' + employee_fullname + '</option>';
                                                $('#filter_agents').append(agent_option);
                                            });
                                            $('.filter_agents').selectpicker('refresh');
                                        },
                                        error: function (xhr, ajaxOptions, thrownError) {
                                            alert(xhr.status);
                                            alert(thrownError);
                                        }
                                    });
                                });

                                $('.prekini_dodavanje_agenata').on('click', function (){
                                    $(this).css('display', 'none');
                                    $('.dodaj_nove_agente').css('display', 'inline-block');
                                    $('.add_new_category').css('display', 'none');
                                    $('.add_new_agent').css('display', 'none');
                                    $('#filter_category').empty();
                                });

                                $('.filter_agents').on('change', function() {

                                    var selectedCount = $(this).find('option:selected').length;
                                    $('.save_new_agents').toggle(selectedCount > 0);
                                });
                                
                                $('.save_new_agents').on('click', function() {
                                    var nalog_id = $('#nalog_id_agents').val();	
                                    var vrsta_id = $('#filter_category').val();	
                                    var selectedValues = $('.filter_agents').selectpicker('val'); // Get the selected values
                                    $.ajax({
                                        url: 'ajax_data.php?page=add_agents_to_nalog',
                                        type: 'POST',
                                        data: {	
                                            'nalog_id' :nalog_id,
                                            'agent_ids' :selectedValues,
                                            'vrsta_id' :vrsta_id
                                        },
                                        dataType: 'html',
                                        success: function(data) {
                                            $("#modal_dodaj_agente").modal('hide');
                                            window.location.reload();
                                        },
                                        error: function (xhr, ajaxOptions, thrownError) {
                                            alert(xhr.status);
                                            alert(thrownError);
                                        }
                                    });   
                                });
                            </script>

						</div>
					</div>
				</div>
			</div>
        </div>
    <?php 
        break;
        case "candidates":
        $tf_nalog_id = $_GET["nalog_id"];
    ?>
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-8">
                    <h1><i class="fa fa-tty idk_color_green" aria-hidden="true"></i> Lista kandidata:  </h1>
                </div>
                <div class="col-xs-12">
                    <hr />
                </div>
            </div>
            <div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<div class="row">
							<div class="col-xs-12">
								<script type="text/javascript">
									$(document).ready(function() {
										var table = $('#idk_table_cand').DataTable({
											responsive: true,
											"aoColumns": [
													{ "width": "5%" },
													{ "width": "45%" },
													{ "width": "45%" },
												]
										});
									} );
								</script>
								<table id="idk_table_cand" class="display" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th class="text-center">ID</th>
											<th class="text-center">Naziv</th>
											<th class="text-center">Status</th>
										</tr>
									</thead>
									<tbody>
										
                                        <?php
                                        $json = getTaskForceInfoForNalog($tf_nalog_id, 1);
                                        $data = json_decode($json, true);
                                        
                                        foreach ($data as $row) {
                                            $candidate_id = $row['tf_candidate_id'];
                                            $status_id = $row['tf_status_id'];
                                            $tf_project_id = $row['tf_project_id']; ?>
                                            <tr>
                                                <td class="text-center"><?php echo $candidate_id; ?></td>
                                                <td class="text-center"><a target="_blank" href="kandidati?page=open&id=<?php echo $candidate_id;?>&projekt_id=<?php echo $tf_project_id?>&nalog_id=<?php echo $tf_nalog_id;?>"><?php echo getCandidateFullnameR($candidate_id); ?></a></td>
                                                <td class="text-center"><?php echo getTaskForceStatusNameById($status_id);?> </td>
                                            </tr>
                                        <?php 
                                        } 
                                        ?>
										
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
        </div>
    <?php    
        break;
        case "obrada_postavke":
            ?>   
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xs-8">
                        <h1><i class="fa fa-tty idk_color_green" aria-hidden="true"></i> Aktivacija agenata za Task Force obradu:  </h1>
                    </div>
                    <div class="col-xs-12">
                        <hr />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="content_box">
                            <div class="row">
                                <div class="col-xs-12">
                                    <script type="text/javascript">
                                        $(document).ready(function() {
                                            var table = $('#idk_table').DataTable({
                                                responsive: true,
                                                "bAutoWidth": false,
                                                "aoColumns": [
                                                        { "width": "5%" },
                                                        { "width": "35%" },
                                                        { "width": "35%" },
                                                        { "width": "5%", "bSortable": false }
                                                    ]
                                            });
    
                                            $('#table-filter').on('change', function(){
                                                table.search(this.value).draw();   
                                            });
                                        } );
                                    </script>
                                    <table id="idk_table" class="display" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th class="text-center">ID</th>
                                                <th class="text-center">Naziv</th>
                                                <th class="text-center">Status</th>
                                                <th class="text-center">Akcija</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $query = $db->prepare("SELECT employee_id, employee_firstname, employee_lastname
                                                                    FROM idk_employees
                                                                    WHERE employee_status != 0 AND FIND_IN_SET('18', employee_status)
                                                                    GROUP BY employee_id");
    
                                                $query->execute();
                                                
                                                while($row = $query->fetch()){
    
                                                    $employee_id = $row['employee_id'];
                                                    $tasks_counter = count(getTFVrsteForDiplAgent($row['employee_id']));
                                                    $employee_firstname = $row['employee_firstname'];
                                                    $employee_lastname = $row['employee_lastname'];
                                                    $employee_fullname = $employee_firstname." ".$employee_lastname;

                                                    if($tasks_counter > 0){
                                                        $obrada_text = '<a href="#" class="dodaj_taskove" employeeid="'.$employee_id.'" tasks_counter="'.$tasks_counter.'" data-toggle="modal"><span class="label label-success">'.$tasks_counter.'<span></a>';
                                                    }else{
                                                        $obrada_text = '<a href="#" class="dodaj_taskove" employeeid="'.$employee_id.'" tasks_counter="'.$tasks_counter.'" data-toggle="modal"><span class="label label-danger"> Nije podešeno <span></a>';
                                                    }

                                                    if($tasks_counter > 0){
														$akcija = '<span class="btn btn-danger deactivate_tf_obrada" data-employeeid="'.$employee_id.'">Deaktiviraj</span>';
                                                    }else{
														$akcija = '';
                                                    }
                                            
                                            ?>
                                            <tr>
                                                <td class="text-center"><?php echo $employee_id; ?></td>
                                                <td class="text-center"><a href="<?php getSiteURL(); ?>employees?page=open&id=<?php echo $employee_id; ?>"><?php echo $employee_fullname; ?></td></a>
                                                <td class="text-center" style="padding:10px !important"><?php echo $obrada_text; ?></td>
                                                <td class="text-center"><?php echo $akcija; ?></td>
                                            </tr>
                                            <?php } ?>
                                            <div class="modal material-modal material-modal_success fade text-left" id="modal_dodaj_taskove" data-backdrop="static">
                                                <div class="modal-dialog ">
                                                    <div class="modal-content material-modal__content">
                                                        <div class="modal-header material-modal__header">
                                                            <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                                            <h4 class="modal-title material-modal__title"><b>Lista taskova</b><h4>
                                                        </div>
                                                        <div class="modal-body material-modal__body text-center">
                                                            <input type="hidden" id="agents_id">

                                                            <div class="form-group task_list_div">
                                                                
                                                            </div>
                                                            <div class="form-group" style="margin-top:20px; margin-bottom:20px;">
                                                            <button class="dodaj_nove_taskove btn material-btn material-btn_secondary">
                                                                <i class="fa fa-plus" style="font-size: xxx-medium;" aria-hidden="true"></i><br>
                                                                <span style="font-size:10px;">Dodaj nove taskove</span>
                                                            </button>
                                                            
                                                            <button class="prekini_dodavanje_taskova btn material-btn material-btn_secondary" style="display:none;">
                                                                <i class="fa fa-times" style="font-size: xxx-medium;" aria-hidden="true"></i><br>
                                                                <span style="font-size:10px;">Prekini dodavanje</span>
                                                            </button>
                                                            </div>
                                                            <div class="form-group add_new_category" style="display:none; margin-top:40px; margin-bottom: 20px;">
                                                                <label for="filter_category" class="col-sm-4 control-label">Odaberi kategoriju:</label>
                                                                <div class="col-sm-5">
                                                                    <select class="selectpicker filter_category" id="filter_category" name="filter_category" data-live-search="true" data-actions-box="true" multiple>
                                                                        <option disabled>Odaberi</option>
                                                                    </select>
                                                                </div>
                                                                <button class="save_new_tasks btn material-btn material-btn_success" style="display:none;">Spremi</button>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer material-modal__footer" style="margin-top: 20px;">
                                                            <button class="btn material-btn material-btn" data-dismiss="modal" id="button_cancel_dodaj_taskove">Zatvori</button>
                                                            <!-- <button style="display:none;" type="submit" class="btn btn-danger material-btn material-btn_danger button_ukloni_sve_agente">Ukloni sve agente</button> -->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <script>
                                            $('.deactivate_tf_obrada').on('click', function(e){	
                                                var employee_id = $(this).data('employeeid');
                                                $.ajax({
                                                    type: "POST",
                                                    url: "<?php getSiteURL(); ?>ajax_data.php?page=deactivate_tf_obrada",
                                                    data: {employee_id: employee_id},
                                                    success: function(data){
                                                        window.location.reload();
                                                    }
                                                });
                                            });

                                            $('.dodaj_taskove').on('click', function () {
                                                employeeid = $(this).attr('employeeid');
                                                tasks_counter = $(this).attr('tasks_counter');

                                                $('#agents_id').val(employeeid);
                                                $("#modal_dodaj_taskove").modal('show');
                                                $('.task_list_div').empty();

                                                if(tasks_counter > 0){

                                                    $.ajax({
                                                        url: 'ajax_data.php?page=get_tasks_for_agent',
                                                        type: 'POST',
                                                        data: {	
                                                            'employeeid' :employeeid
                                                        },
                                                        dataType: 'html',
                                                        success: function(data) {
                                                            var tasks = JSON.parse(data);

                                                            tasks.forEach(function(task) {
                                                                var tfan_id = task.tfan_id;
                                                                var tfan_nalog = task.tfan_nalog;
                                                                var tfan_agent = task.tfan_agent;
                                                                var tfan_vrsta_id = task.tfan_vrsta_id;
                                                                var tfan_vrsta_name = task.tf_vrsta_name;
                                                                var employee_fullname = task.employee_fullname;

                                                                var agent_info = '<span class="agent-container agent'+tfan_agent+' vrsta_id'+tfan_vrsta_id+'" style="display: inline-block; margin-top: 5px; padding: 5px 10px; border: 1px solid #ccc; border-radius: 5px; margin-right: 10px; position: relative;">' +
                                                                                tfan_vrsta_name +
                                                                                '<span class="remove_task" agent_id="' + tfan_agent + '" vrsta_id="' + tfan_vrsta_id + '" style="margin-left: 5px; transform: translateY(-50%); cursor: pointer;">X</span></span>';
                                                                $('.task_list_div').append(agent_info);
                                                            });
                                                        },
                                                        error: function (xhr, ajaxOptions, thrownError) {
                                                            alert(xhr.status);
                                                            alert(thrownError);
                                                        }
                                                    });
                                                }else{
                                                    // $('.button_ukloni_sve_agente').css('display', 'none');
                                                    $('.task_list_div').append('<span style="display: inline-block; margin-top: 5px; padding: 5px 10px; border: 1px solid #ccc; border-radius: 5px; margin-right: 10px; position: relative;"> Nema taskova </span>');
                                                }

                                                });

                                                // Attach click event to the parent element of the dynamically added .remove_tasks spans
                                                $('.task_list_div').on('click', '.remove_task', function() {
                                                    var agent_id = $(this).attr('agent_id');
                                                    var vrsta_id = $(this).attr('vrsta_id');
                                                    $.ajax({
                                                        url: 'ajax_data.php?page=remove_agent_from_obrada_task',
                                                        type: 'POST',
                                                        data: {	
                                                            'agent_id' :agent_id,
                                                            'vrsta_id' :vrsta_id
                                                        },
                                                        dataType: 'html',
                                                        success: function(data) {
                                                            $('.task_list_div').find('.agent-container.agent' + agent_id+'.vrsta_id' + vrsta_id).remove();
                                                        },
                                                        error: function (xhr, ajaxOptions, thrownError) {
                                                            alert(xhr.status);
                                                            alert(thrownError);
                                                        }
                                                    });
                                                });


                                                $('#button_cancel_dodaj_taskove').on('click', function () {
                                                    $('#agents_id').empty();	
                                                    $('.task_list_div').empty();
                                                    $("#modal_dodaj_taskove").modal('hide');
                                                });

                                                $('.dodaj_nove_taskove').on('click', function (){
                                                    let agent_id = $('#agents_id').val();
                                                    $(this).css('display', 'none');
                                                    $('.prekini_dodavanje_taskova').css('display', 'inline-block');
                                                    $('.add_new_category').css('display', 'block');

                                                    $.ajax({
                                                        url: 'ajax_data.php?page=get_tf_vrsta_obrada_option',
                                                        type: 'POST',
                                                        data: {	
                                                            'agent_id' : agent_id,
                                                        },
                                                        dataType: 'html',
                                                        success: function(data) {
                                                            var vrste = JSON.parse(data);
                                                            vrste.forEach(function(vrsta) {
                                                                var vrsta_id = vrsta.id;
                                                                var vrsta_name = vrsta.name;
                                                                // Do something with the agent properties (e.g., append them to a container)
                                                                var vrsta_option = '<option value="' + vrsta_id + '">' + vrsta_name + '</option>';
                                                                $('#filter_category').append(vrsta_option);
                                                            });
                                                            $('.filter_category').selectpicker('refresh');
                                                        },
                                                        error: function (xhr, ajaxOptions, thrownError) {
                                                            alert(xhr.status);
                                                            alert(thrownError);
                                                        }
                                                    });
                                                });

                                                $('.prekini_dodavanje_taskova').on('click', function (){
                                                    $(this).css('display', 'none');
                                                    $('.dodaj_nove_taskove').css('display', 'inline-block');
                                                    $('.add_new_category').css('display', 'none');
                                                    $('#filter_category').empty();
                                                });

                                                $('#filter_category').on('change', function() {
                                                    var selectedCount = $(this).find('option:selected').length;
                                                    $('.save_new_tasks').toggle(selectedCount > 0);
                                                });

                                                $('.save_new_tasks').on('click', function() {
                                                    var agent_id = $('#agents_id').val();	
                                                    var selectedValues = $('#filter_category').selectpicker('val');
                                                    $.ajax({
                                                        url: 'ajax_data.php?page=add_agent_to_obrada_tasks',
                                                        type: 'POST',
                                                        data: {	
                                                            'agent_id' :agent_id,
                                                            'vrsta_ids' :selectedValues
                                                        },
                                                        dataType: 'html',
                                                        success: function(data) {
                                                            $("#modal_dodaj_taskove").modal('hide');
                                                            window.location.reload();
                                                        },
                                                        error: function (xhr, ajaxOptions, thrownError) {
                                                            alert(xhr.status);
                                                            alert(thrownError);
                                                        }
                                                    });   
                                                });
                                        </script>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php 
            break;
    ?>
    <?php 
    }
    ?>
    </div>
</body>
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
?>
