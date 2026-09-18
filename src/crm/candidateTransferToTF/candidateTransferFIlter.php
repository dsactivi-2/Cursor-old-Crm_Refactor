<?php
	include("../includes/functions.php");
	include("../includes/common.php");

	$getEmployeeStatus = explode( ',' , getEmployeeStatus());

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Transfer kandidata | <?php getTitle(); ?></title>
    <style>
        body {
            overflow-x: hidden; 
        }
        .scrollBarHorizontalTF::-webkit-scrollbar {
            height:10px;
            margin-top: 10px;
        }
        .scrollBarHorizontalTF::-webkit-scrollbar-thumb {
            background: #1D84C0;
            border-radius: 5px;
        }
        .scrollBarHorizontalTF::-webkit-scrollbar-track {
            background-color: #e9ecef;
            border-radius: 5px;
        }
        .scrollBarHorizontalTF::-webkit-scrollbar-button{
            /*display:none;*/
        }

		.lds-hourglass {
			position: absolute;
			width: 150px;
			height: 150px;
			top: 50%;
			left: 50%;
			margin-top: -75px; 
			margin-left: -75px;			
			background: radial-gradient(#5cb85c, white);
			border-radius: 100px;
		}
		.lds-hourglass:after {
			top: 50%;
			left: 50%;
			content: " ";
			display: block;
			border-radius: 50%;
			width: 0;
			height: 0;
			margin: 8px;
			box-sizing: border-box;
			border: 66px solid #BAEC84;
			border-color: #4cae4c transparent #4cae4c transparent;
			animation: lds-hourglass 2.0s infinite;
		}
		.lds-hourglass_min {
			position: relative;
			width: 36px;
			height: 36px;
			/* top: 50%;*/
			left: 50%;
			/* margin-top: -10px; */
			margin-left: -10px;			
			background: radial-gradient(#5cb85c, white);
			border-radius: 100px;
		}
		.lds-hourglass_min:after {
			/* top: 50%;*/
			left: 50%;
			content: " ";
			display: block;
			border-radius: 50%;
			width: 0;
			height: 0;
			margin: 1 px;
			box-sizing: border-box;
			border: 18px solid #BAEC84;
			border-color: #4cae4c transparent #4cae4c transparent;
			animation: lds-hourglass 2.0s infinite;
		}
		@keyframes lds-hourglass {
		  0% {
			transform: rotate(0);
			animation-timing-function: cubic-bezier(0.55, 0.055, 0.675, 0.19);
		  }
		  50% {
			transform: rotate(900deg);
			animation-timing-function: cubic-bezier(0.215, 0.61, 0.355, 1);
		  }
		  100% {
			transform: rotate(1800deg);
		  }
		}
	</style>
	<?php include('../includes/head.php');?>
    <script>

        $(document).ready(function() {

            $('#filter_vocation_groups').on('change', function(){
                $('#to_append_to_vocations').empty();
                var filter_vocation_groups = $('#filter_vocation_groups').val();
            
                $.ajax({
                    url: 'getVocationsByVocationGroup.php',
                    type: 'POST',
                    dataType: 'html',
                    data: {
                        'filter_vocation_groups' : filter_vocation_groups
                    },
                    success: function(data) {
                        $('#to_append_to_vocations').append(data);
                        $('#filter_vocations').selectpicker('refresh');
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(xhr.status);
                        alert(thrownError);
                    }
                });
            });

            $('#filter_age_to').on('change',function(){
                var age_to = parseInt($('#filter_age_to').val());
                var age_from = parseInt($('#filter_age_from').val());
                // alert(age_to);
                // alert(age_from);

                if(age_to < age_from){
                    age_to = age_from;
                }
                else if(age_to > 100){
                    age_to = 100;
                }
                else if(age_to < 0){
                    age_to = 0;
                }
                $('#filter_age_to').val(age_to);
            });

            $('#filter_age_from').on('change',function(){
                
                var age_to = parseInt($('#filter_age_to').val());
                var age_from = parseInt($('#filter_age_from').val());
                // alert(age_to);
                // alert(age_from);

                if(age_from > age_to){
                    age_from = age_to;
                }
                else if(age_from > 100){
                    age_from = 100;
                }
                else if(age_from < 0){
                    age_from = 0;
                }
                $('#filter_age_from').val(age_from);
            });


            $('#search').on('click', function(){
                $('#transfer').prop('disabled', true); 

                var filter_for_order = $('#filter_for_order').val();
                var filter_ignore_orders = $('#filter_ignore_orders').val();
                var filter_vocations = $('#filter_vocations').val();
                var filter_age_from = $('#filter_age_from').val();
                var filter_age_to = $('#filter_age_to').val();
                var filter_unknown_age = $('#filter_unknown_age').is(":checked");
                var filter_select_country = $('#filter_select_country').val();
                var filter_select_residence = $('#filter_select_residence').val();
                var filter_language_de = $('#filter_language_de').val();
                var filter_language_en = $('#filter_language_en').val();
                var filter_drivers_licence = $('#filter_drivers_licence').val();
                var filter_visa = $('#filter_visa').is(":checked");
                var filter_work_experience = $('#filter_work_experience').is(":checked");
                var filter_dipl_status = $('#filter_dipl_status').val();
                var filter_vrsta_nostrifikacije = $('#filter_vrsta_nostrifikacije').val();
                
                var filter_only_order_vocations = $('#filter_only_order_vocations').is(":checked");
                
                if(filter_vocations === undefined && !filter_only_order_vocations){
                    $('#lable_vocations').effect('highlight', 300);
                    $('#lable_vocations').effect('pulsate', 200);
                    $('#lable_vocations').effect('highlight', 300);
                }
                else{
                    $('#to_append_to_table').fadeOut(600, function(){
                        $.ajax({
                            url: 'getCandidateTransferFilter.php',
                            type: 'POST',
                            dataType: 'json',
                            // dataType: 'html',
                            data: {
                                'filter_for_order' : filter_for_order,
                                'filter_ignore_orders' : filter_ignore_orders,
                                'filter_vocations' : filter_vocations,
                                'filter_age_from' : filter_age_from,
                                'filter_age_to' : filter_age_to,
                                'filter_unknown_age' : filter_unknown_age,
                                'filter_select_country' : filter_select_country,
                                'filter_select_residence' : filter_select_residence,
                                'filter_language_de' : filter_language_de,
                                'filter_language_en' : filter_language_en,
                                'filter_visa' : filter_visa,
                                'filter_drivers_licence' : filter_drivers_licence,
                                'filter_work_experience' : filter_work_experience,
                                'filter_only_order_vocations' : filter_only_order_vocations,
                                'filter_dipl_status' : filter_dipl_status,
                                'filter_vrsta_nostrifikacije' : filter_vrsta_nostrifikacije

                            },
                            success: function(result) {
                                if(result[0]['table'] != 'error'){

                                    $('#transfer').prop('disabled', false); 
                                    $('#transfer').attr('candidates', result[0]['result_candidates']);
                                    $("#to_append_to_table").empty().append(result[0]['table']).fadeIn(800, function(){
                                        var table = $('#table_marketing').DataTable({
                                            data: result,
                                            "lengthChange": false,
                                            columns: [
                                                {	
                                                    data: 'candidate_id',
                                                    render: function (data) {
                                                        return data;
                                                    },
                    
                                                }, 
                                                {	
                                                    data: 'candidate_name',
                                                    render: function (data) {
                                                        return data;
                                                    },
                    
                                                },
                                                {	
                                                    data: 'vocation',
                                                    render: function (data) {
                                                        return data;
                                                    },
                                                },
                                                {	
                                                    data: 'dob',
                                                    render: function (data) {
                                                        return data;
                                                    },
                                                },
                                                {	
                                                    data: 'is_eu',
                                                    render: function (data) {
                                                        return data;
                                                    },
                                                },
                                                {	
                                                    data: 'has_visa',
                                                    render: function (data) {
                                                        return data;
                                                    },
                                                },
                                                {	
                                                    data: 'language_level_de',
                                                    render: function (data) {
                                                        return data;
                                                    },
                                                },
                                                {	
                                                    data: 'language_level_en',
                                                    render: function (data) {
                                                        return data;
                                                    },
                                                },
                                                {	
                                                    data: 'drivers_licence',
                                                    render: function (data) {
                                                        return data;
                                                    },
                                                },
                                                {	
                                                    data: 'work_experience',
                                                    render: function (data) {
                                                        return data;
                                                    },
                                                },
                                                {	
                                                    data: 'dipl_status',
                                                    render: function (data) {
                                                        return data;
                                                    },
                                                    className: "text-center",
                                                },
                                                {	
                                                    data: 'vrsta_nostrifikacije',
                                                    render: function (data) {
                                                        return data;
                                                    },
                                                    className: "text-center",
                                                }
                                            ]                                    
                                        });
                                    });
                                }
                                else{
                                    $('#transfer').prop('disabled', true); 
                                    $('#transfer').attr('candidates', "");
                                    $("#to_append_to_table").empty().append(result[0]['error_text']).fadeIn(800);
                                }
                            },
                            error: function (xhr, ajaxOptions, thrownError) {
                                alert(xhr.status);
                                alert(thrownError);
                            }
                        });
                        $('#to_append_to_table').empty();
                        $('#to_append_to_table').append('<div class="lds-hourglass"></div>').fadeIn(600);
                    });                    
                }
            });

            $('#transfer').on('click', function(){
                $('#confirm_transfer').prop('disabled', false);

                var candidates_to_transfer = $(this).attr('candidates');
                var candidates_to_transfer = candidates_to_transfer.split(",");
                var filter_for_order = $('#filter_for_order').val();
                
                $('#append_confirmation_text').empty().append('Jeste li sigurni da želite prebaciti '+candidates_to_transfer.length+' kandidata u projekat '+filter_for_order+'?');
                $("#confirmationModal").modal('show');
                
            });

            $('#confirm_transfer').on('click', function(){
                var candidates_to_transfer = $('#transfer').attr('candidates');
                $('#append_confirmation_text').empty().append('Prebacivanje u toku, sačekajte');
                $('#transfer').attr('candidates', '');
                $('#transfer').prop('disabled',true);
                var filter_for_order = $('#filter_for_order').val();
                $(this).prop('disabled',true);
               
                $.ajax({
                    url: 'transferCandidates.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        'candidates_to_transfer' : candidates_to_transfer,
                        'filter_for_order' : filter_for_order
                    },
                    success: function(data) {
                        $('#append_confirmation_text').empty().append(data[0]['response']);
                        if(data[0]['update'] == "1"){
                            $.ajax({
                                url: 'updateSP.php',
                                type: 'POST',
                                dataType: 'html',
                                data: {
                                    'candidates_to_transfer' : candidates_to_transfer,
                                    'filter_for_order' : filter_for_order,
                                },
                                success: function(data) {
                                    $.ajax({
                                        url: 'insertLSP.php',
                                        type: 'POST',
                                        dataType: 'html',
                                        data: {
                                            'candidates_to_transfer' : candidates_to_transfer,
                                            'filter_for_order' : filter_for_order,
                                        },
                                        success: function(data) {
                                            console.log(data);
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
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(xhr.status);
                        alert(thrownError);
                    }
                });
            });
        });

    </script>
</head>
<body>
<div class="modal fade" id="confirmationModal" aria-hidden="true" aria-labelledby="confirmationModalLabel" tabindex="-1" style = "background-color: rgb(255 255 255 / 50%);">
				<div class="modal-dialog modal-dialog-centered">
					<div class="modal-content border-0">
						<div class="modal-body">
							<div class = "row pt-1 my-3">
								<div class = "col-12 text-center">
									<p id = "append_confirmation_text" style = "color: #8E97A3; font-weight: 600; font-size:16px;" class = "mb-0">
										Jeste li sigurni da želite prebaciti x kandidata u projekat y?
									</p>
								</div>
							</div>
						</div>
						<div class="modal-footer justify-content-center border-top-0">
							<button class="btn btn-success" id = "confirm_transfer">DA</button>
						</div>
					</div>
				</div>
			</div>
	<header>
		<?php include('../header.php'); ?>
	</header>
	<div id="sidebar">
		<?php include('../menu.php'); ?>
	</div>
	<div id="content">
		<div class="container-fluid">
            <div class="row">
                <div class="col-xs-8">
                    <h1><i class="fa fa-file idk_color_green" aria-hidden="true"></i> Transfer kandidata</h1>
                </div>
            </div>
            <hr>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="content_box">
                    <div class="row">
                    <div class = "col-lg-4">
                        <br><label for="filter_for_order">Prebaci u nalog:</label><br>
                            <select id="filter_for_order" class="selectpicker" data-live-search="true">
                                <?php
                                    $query_get_orders = $db -> prepare('
                                        SELECT nalog_id, nalog_naziv, nalog_broj
                                        FROM idk_nalozi
                                        WHERE nalog_status NOT IN (8,12)
                                        ORDER BY nalog_id DESC
                                    ');
                                    $query_get_orders -> execute();
    
                                    while($row_get_orders = $query_get_orders -> fetch()){
                                        echo '<option value = "'.$row_get_orders["nalog_id"].'">'.$row_get_orders["nalog_broj"].' | '.$row_get_orders["nalog_naziv"].'</option>';
                                    }
                                ?>
                            </select>   

                            <hr>
                            <label for="filter_ignore_orders">Ignoriši naloge:</label><br>
                            <select id="filter_ignore_orders" class="selectpicker" multiple data-live-search="true">
                                <?php
                                    $query_get_orders = $db -> prepare('
                                        SELECT nalog_id, nalog_naziv, nalog_broj
                                        FROM idk_nalozi
                                        WHERE nalog_status NOT IN (8,12)
                                        ORDER BY nalog_id DESC
                                    ');
                                    $query_get_orders -> execute();
    
                                    while($row_get_orders = $query_get_orders -> fetch()){
                                        echo '<option value = "'.$row_get_orders["nalog_id"].'">'.$row_get_orders["nalog_broj"].' | '.$row_get_orders["nalog_naziv"].'</option>';
                                    }
                                ?>
                            </select>

                            <hr>
                            <input type = "checkbox" id = "filter_only_order_vocations"> Samo smjerovi odabranog naloga
                            <hr>
                            <label for="filter_vocation_groups">Struke:</label><br>
                            <select id="filter_vocation_groups" class="selectpicker" multiple data-live-search="true" data-actions-box="true">
                                <?php
                                    $query_get_vocation_groups = $db -> prepare('
                                        SELECT ss.id_struke, ss.naziv_struke
                                        FROM idk_struke ss
                                    ');
                                    $query_get_vocation_groups -> execute();
    
                                    while($row_get_vocation_groups = $query_get_vocation_groups -> fetch()){
                                        echo '<option value = "'.$row_get_vocation_groups["id_struke"].'">'.$row_get_vocation_groups["naziv_struke"].'</option>';
                                    }
                                    ?>
                                <option value = "0">Bez struke</option>
                            </select> 
                            <br><br>
                            <label id="lable_vocations" for="filter_vocations">Smjerovi:</label><br>
                            <div id="to_append_to_vocations"></div>
                            <br>
                            
                            <input type = "checkbox" id = "filter_work_experience"> Kandidat iskustvo u struci
                            <hr>    
                            <label for="filter_language_de">Poznavanje njemačkog jezika:</label> <br>
                            <select id="filter_language_de" class="selectpicker" multiple>
                                <option value="-1">Nije poznato</option>
                                <option value="0">Bez znanja</option>
                                <option value="1">A1</option>
                                <option value="2">A2</option>
                                <option value="3">B1</option>
                                <option value="4">B2</option>
                                <option value="5">C1</option>
                                <option value="6">C2</option>
                            </select>
                            
                            <hr>
                            <label for="filter_language_en">Poznavanje engleskog jezika:</label> <br>
                            <select id="filter_language_en" class="selectpicker" multiple>
                                <option value="-1">Nije poznato</option>
                                <option value="0">Bez znanja</option>
                                <option value="1">A1</option>
                                <option value="2">A2</option>
                                <option value="3">B1</option>
                                <option value="4">B2</option>
                                <option value="5">C1</option>
                                <option value="6">C2</option>
                            </select>
                            <hr>
                            <div class="col-lg-12" style="margin-bottom:15px;">
                                <label for="filter_age_from" class="col-sm-5">Godine od:</label>
                                <div class="col-sm-7">
                                    <input type="number" style="width:100%;" id = "filter_age_from" value = "1"></input>
                                </div>
                            </div>
                            <div class="col-lg-12" style="margin-bottom:15px;">
                                <label for="filter_age_to" class="col-sm-5"> Godine do:</label>
                                <div class="col-sm-7">
                                    <input type="number" style="width:100%;" id = "filter_age_to" value = "100"></input>
                                </div>
                            </div>
                            <br>
                            <br>
                            <input type = "checkbox" id = "filter_unknown_age"> Nepoznate godine
                            <hr>

                            <label for="filter_select_country">Država (telefon):</label> <br>
                            <select id="filter_select_country" class="selectpicker" multiple>
                                <option value="B">Bosna i Hercegovina</option>
                                <option value="S">Srbija</option>
                                <option value="D">Njemačka</option>
                                <option value="R">Ostali</option>
                            </select>
                            <hr>
                        
                            <label for="filter_select_residence">EU Državljanin:</label> <br>
                            <select id="filter_select_residence" class="selectpicker" multiple>
                                <option value="NON-EU državljanin">NON-EU državljanin</option>
                                <option value="EU državljanin">EU državljanin</option>
                                <option value="Unknown">Nepoznato</option>
                            </select>
                            
                            <hr>
                            <input type = "checkbox" id = "filter_visa"> Kandidat ima vizu
                            
                            <hr>
                            <label for="filter_drivers_licence">Vozačka kategorija:</label> <br>
                            <select id="filter_drivers_licence" class="selectpicker" multiple>
                                <option value="0">Nepoznato</option>
                                <option value="Nema">Nema</option>
                                <option value="B">B</option>
                                <option value="C1">C1</option>
                                <option value="C">C</option>
                                <option value="BE">BE</option>
                                <option value="C1E">C1E</option>
                                <option value="CE">CE</option>
                            </select>

                            <hr>
                            <label for="filter_dipl_status">DIPL status:</label> <br>
                            <select id="filter_dipl_status" class="selectpicker" multiple data-live-search="true" data-actions-box="true">
                                <option value = "0" >Nije u Diplu</option>
                                <option value = "1" >Lead</option>
                                <option value = "2" >Prikupljanje dokumentacije</option>
                                <option value = "3" >Poslana pošta</option>
                                <option value = "4" >U obradi</option>
                                <option value = "5" >Plaćena taksa / Poslana dopuna</option>
                                <option value = "6" >Završen</option>
                                <option value = "7" >Arhiviran</option>
                                <!-- <option value = "1|6" >Neuspješan Kontakt 1</option>
                                <option value = "1|2" >Neuspješan Kontakt 3</option>
                                <option value = "1|3" >Zainteresiran Lead</option>
                                <option value = "1|4" >Nezainteresiran Lead</option>
                                <option value = "1|5" >U obradi Lead</option>
                                <option value = "1|7" >Neuspješan Lead 1</option>
                                <option value = "1|8" >Neuspješan Lead 2</option>
                                <option value = "1|9" >Termin Zainteresiran</option>
                                <option value = "1|10" >Termin Ostali</option>
                                <option value = "1|11" >Lead NL</option>
                                <option value = "1|12" >Lead NZ</option>
                                <option value = "2|2" >Nepotpuna dokumentacija</option>
                                <option value = "2|3" >Na prevodu</option>
                                <option value = "2|4" >Dokumentacija kompletirana</option>
                                <option value = "2|5" >Prevod završen</option>
                                <option value = "2|6" >Poslan zahtjev</option>
                                <option value = "2|7" >Potpisan zahtjev</option>
                                <option value = "3|2" >Zaprimili dokumentaciju</option>
                                <option value = "4|2" >Stigla taksa</option> -->
                            </select>

                            <hr>
                            <label for="filter_vrsta_nostrifikacije">Vrsta nostrifikacije:</label> <br>
                            <select id="filter_vrsta_nostrifikacije" class="selectpicker" multiple data-live-search="true" data-actions-box="true">
                                <option value = "3" >Nepoznato</option>
                                <option value = "2" >Evaluacija</option>
                                <option value = "1" >Potpuno priznata</option>
                                <option value = "0" >Djelimično priznata</option>
                            </select>
                        </div>
                        <div class = "col-lg-8">
                            <div class="col-lg-2 idk_margin_top20">
                                <button id="search" style="width:100%" class="btn btn-success">Traži</button>
                            </div>
                            <div class="col-lg-2 idk_margin_top20">
                                <button disabled = "true" id="transfer" style="width:100%" class="btn btn-success">Prebaci</button>
                            </div>
                            <br>
                            <br>
                            <br>
                            <div id = "to_append_to_table" class = "col-lg-12 scrollBarHorizontalTF" style="overflow-y: auto !important; padding-bottom: 15px; min-height: 200px;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		<footer><?php getCopyright(); ?></footer>
	</div>
</body>
</html>

