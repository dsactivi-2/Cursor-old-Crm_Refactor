<?php 
    if ($orderId != 0) { 
        /*
            Short form for CD - Casting Dates
        */
        ?>
            <style>
                #castingDatesSettings #allCD .btn {
                    margin-bottom: 5px;
                    margin-top: 5px;
                }
                #castingDatesSettings #allCD .label {
                    font-size: 100%;
                }
                #castingDatesSettings form-field-question .fa-star, #castingDatesSettings form-field-question .fa-star-o {
                    color: #FDD878;
                }
                #castingDatesSettings form-field-question .question-text {
                    color: #1F2E45; 
                    font-style: normal; 
                    font-weight: 600; 
                    font-size: 16px;
                }
                #castingDatesSettings form-field-question textarea {
                    border: 2px solid #eee;
                }

                #castingDatesSettings #addQuestionsCD .questions_AQCD {
                    border: 1px solid #ddd;
                    border-radius: 4px;
                    padding: 15px;
                } 
            </style>
            <!-- 
                Modal ADD START 
                -->
                    <div class="row">
                        <div class="col-sm-12 text-right">
                            <button 
                                data-toggle="modal" data-target="#addCD"
                                class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column"
                            >
                                <i class="fa fa-plus" aria-hidden="true"></i>
                                <span>
                                    Dodaj casting
                                </span>
                            </button>
                        </div>
                    </div>
                    <div class="modal material-modal material-modal_primary fade text-left" id="addCD">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content material-modal__content">
                                <div class="modal-header material-modal__header">
                                    <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title material-modal__title">
                                        <i style = "margin-right: 10px;" class="fa fa-plus" aria-hidden="true"></i>
                                        <span>
                                            Dodaj <strong>Casting</strong>
                                        </span>
                                    </h4>
                                </div> 
                                <div class="modal-body material-modal__body">
                                    <form action="<?php getSiteURL(); ?>jobsoft_settings/backend/do_settings.php?page=add_appointments" enctype="multipart/form-data" method="post" role="form" class="form-horizontal" id="addCDForm">
                                        <input type="hidden" id="order_id" name="order_id" value="<?php echo $orderId; ?>">
                                        <div class="row">
                                            <div class="col-md-offset-2 col-sm-8">

                                                <div class="message_CD form-group hidden">    
                                                        
                                                </div>

                                                <div class="form-group">
                                                    <div class="col-sm-12">
                                                        <label for="name_CD" class="col-sm-4 control-label">
                                                            <span class="text-danger">
                                                                *
                                                            </span>
                                                            Naziv grupe:
                                                        </label>
                                                        <div class="col-sm-8">
                                                            <div class="materail-input-block materail-input-block_primary">
                                                                <input class="form-control materail-input" type="text" name="name_CD" id="name_CD" placeholder="NPR: <?php echo $kompanija . ' - ' . date('F Y'); ?>" required>
                                                                <span class="materail-input-block__line"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <div class="col-sm-12">
                                                        <label for="name_de_CD" class="col-sm-4 control-label">
                                                            <span class="text-danger">
                                                                *
                                                            </span>
                                                            Naziv grupe (DE):
                                                        </label>
                                                        <div class="col-sm-8">
                                                            <div class="materail-input-block materail-input-block_primary">
                                                                <input class="form-control materail-input" type="text" name="name_de_CD" id="name_de_CD" placeholder="NPR: <?php echo $kompanija . ' - ' . date('F Y') . ' (DE)'; ?>" required>
                                                                <span class="materail-input-block__line"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group">    
                                                    <div class="col-sm-12">
                                                        <label for="dates_CD" class="col-sm-4 control-label">
                                                            <span class="text-danger">
                                                                *
                                                            </span>
                                                            Vrijeme termina:
                                                        </label>
                                                        <div class="col-sm-8">
                                                            <div class="">
                                                                <input type="text" class="form-control flatpickr-input active" name="dates_CD" id="dates_CD" placeholder="Odaberite dane" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div id="content_dates_CD" class="hidden"></div>

                                            </div>
                                        </div>
                                        <div class="form-group idk_margin_top20">
                                            <div class="col-md-offset-2 col-sm-8 text-center">
                                                <small>
                                                    <strong>
                                                        Sva polja označena sa 
                                                        <span class="text-danger">
                                                            *
                                                        </span>  
                                                        su obavezna!
                                                    </strong>
                                                </small>
                                            </div>
                                        </div>
                                        <div class="modal-footer material-modal__footer">
                                            <button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
                                            <button type="submit" class="btn btn-primary material-btn material-btn_primary" form="addCDForm"><i class="fa fa-check-square-o" aria-hidden="true"></i> Spremi</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <hr>
                        </div>
                    </div>
                <!-- 
                Modal ADD END 
            -->
            <!-- 
                Script Add START
                -->
                    <script>
                        function setInitialValuesCD() {
                            $('#name_CD', '#addCDForm', '#castingDatesSettings').val(null);
                            $('#name_de_CD', '#addCDForm', '#castingDatesSettings').val(null);
                            $('#dates_CD', '#addCDForm', '#castingDatesSettings').flatpickr({
                                mode: "multiple",
                                dateFormat: "d.m.Y",
                                disableMobile: "true",
                                defaultDate: null
                            });
                        }; 
                        $(document).ready(setInitialValuesCD);
                        function messageCD(type, message) {
                            $('.message_CD', '#addCDForm', '#castingDatesSettings').html(`
                                <div class="col-sm-12">
                                    <div class="alert alert-` + type + ` text-center" role="alert">
                                        ` + message + `
                                    </div>
                                </div>
                            `).removeClass('hidden');
                            
                            setTimeout(function(){
                                $('.message_CD', '#addCDForm', '#castingDatesSettings').html('').addClass('hidden');
                            }, 3000);
                        };
                        function makeContentDateFieldsCD(content_date) {
                            let content_date_without_dots = content_date.replace(/\./g,"");
                            return `
                                <div class="contentDateFieldsCD panel panel-default" data-option="`+content_date+`">
                                    <div class="panel-heading">
                                        <div class="row">
                                            <div class="col-xs-12 text-left">
                                                Datum castinga <strong>`+content_date+`</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="city`+content_date_without_dots+`CD" class="col-sm-4 control-label">
                                                    <span class="text-danger">
                                                        *
                                                    </span>
                                                    Grad:
                                                </label>
                                                <div class="col-sm-8">
                                                    <div class="materail-input-block materail-input-block_primary">
                                                        <input class="form-control materail-input" type="text" name="city`+content_date_without_dots+`CD" id="city`+content_date_without_dots+`CD" placeholder="Unesite grad za `+content_date+`" required>
                                                        <span class="materail-input-block__line"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        };
                        function addContentFieldsCD() {
                            let dates_CD = $('#dates_CD', '#addCDForm', '#castingDatesSettings').val(); 
                            if (dates_CD != '') {
                                dates_CD = dates_CD.split(',').map(date => date.trim());
                            } else {
                                dates_CD = [];
                            }
                            console.log(dates_CD);
                            let content_dates_CD = $('#content_dates_CD', '#addCDForm', '#castingDatesSettings');
                            let existing_content_dates_CD = content_dates_CD.find('.contentDateFieldsCD');
                            if (existing_content_dates_CD.length) { 
                                existing_content_dates_CD.each(function() {
                                    let option = $(this).data('option');
                                    if (!dates_CD.includes(option)) {
                                        $(this).remove();
                                    }
                                });
                            }
                            if (dates_CD.length) {
                                dates_CD.forEach(date => {
                                    if (!existing_content_dates_CD.filter(`[data-option="${date}"]`).length) {
                                        content_dates_CD.append(makeContentDateFieldsCD(date));
                                    }
                                });
                            }
                            if (dates_CD.length) {
                                content_dates_CD.removeClass('hidden');
                            } else {
                                content_dates_CD.addClass('hidden');
                            }
                        };
                        $('#dates_CD', '#addCDForm', '#castingDatesSettings').on('change', addContentFieldsCD);
                        $('#addCDForm', '#castingDatesSettings').on('submit', function(event) {
                            let dates_CD = $('#dates_CD', '#addCDForm', '#castingDatesSettings').val();
                            if (dates_CD === '') {
                                messageCD('warning', 'Polje <strong>Vrijeme termina</strong> je obavezno! Molimo unesite odgovarajuću vrijednost!'); 
                                event.preventDefault();
                            }
                        });
                    </script>
                <!-- 
                Script Add START
            -->
        <?php

        $queryCDS = $db->prepare("
            SELECT 
                pap.pap_id, 
                pap.pap_date,
                pap.pap_city,
                pap.pap_location_name,
                pap.pap_google_maps_location, 
                pap.pap_first_sending_number_days, 
                pap.pap_second_sending_number_days, 
                pap.pap_first_send_enabled,
                pap.pap_second_send_enabled, 
                pap.pap_group_id, 
                pag.ppaq_start_date, 
                pag.ppaq_end_date, 
                pag.papq_name, 
                pag.papq_name_de,
                pah.pah_id, 
                pah.pah_time, 
                pah.pah_doe, 
                cntQuestions.count_questions, 
                cntQuestionRatings.count_question_ratings
            FROM 
                idk_pp_appointments pap 
            JOIN 
                idk_pp_appointment_groups pag
            ON 
                pap.pap_group_id = pag.ppaq_id 
            LEFT JOIN 
                idk_pp_appointment_hours pah
            ON 
                pap.pap_id = pah.pap_id
            INNER JOIN 
                (
                    SELECT 
                        pap1.pap_id,
                        COUNT(papq.papq_id) AS count_questions
                    FROM 
                        idk_pp_appointments pap1
                    LEFT JOIN 
                        idk_pp_appointments_questions papq
                    ON 
                        papq.papq_appointment_id = pap1.pap_id
                    WHERE 
                        pap1.pap_nalog_id = :orderId
                    GROUP BY
                        pap1.pap_id
                ) AS cntQuestions
            ON 
                pap.pap_id = cntQuestions.pap_id
            INNER JOIN 
                (
                    SELECT 
                        pap1.pap_id,
                        COUNT(pra.pra_appointment_question_id) AS count_question_ratings
                    FROM 
                        idk_pp_appointments pap1
                    LEFT JOIN 
                        idk_pp_appointments_questions papq
                    ON 
                        papq.papq_appointment_id = pap1.pap_id
                    LEFT JOIN
                        idk_pp_ratings pra
                    ON 
                        papq.papq_id = pra.pra_appointment_question_id 
                    WHERE 
                        pap1.pap_nalog_id = :orderId
                    GROUP BY
                        pap1.pap_id
                ) AS cntQuestionRatings
            ON 
                pap.pap_id = cntQuestionRatings.pap_id
            WHERE 
                pap.pap_nalog_id = :orderId
                AND 
                pag.papq_nalog_id = :orderId
            ORDER BY 
                pap.pap_id DESC, pah.pah_time ASC
        ");
        $queryCDS->execute(array(
            ':orderId' => $orderId
        ));
        if ($queryCDS->rowCount() > 0) {
            $rowsCDS = $queryCDS->fetchAll(PDO::FETCH_ASSOC);
            $groupedData = array();
            foreach ($rowsCDS as $rowCDS) {
                if (!isset($groupedData[$rowCDS['pap_id']])) {
                    $groupedData[$rowCDS['pap_id']] = array(
                        'pap_id' => $rowCDS['pap_id'],
                        'pap_date' => $rowCDS['pap_date'],
                        'pap_city' => $rowCDS['pap_city'],
                        'pap_location_name' => $rowCDS['pap_location_name'],
                        'pap_google_maps_location' => $rowCDS['pap_google_maps_location'],
                        'pap_first_sending_number_days' => $rowCDS['pap_first_sending_number_days'],
                        'pap_second_sending_number_days' => $rowCDS['pap_second_sending_number_days'],
                        'pap_first_send_enabled' => $rowCDS['pap_first_send_enabled'],
                        'pap_second_send_enabled' => $rowCDS['pap_second_send_enabled'],
                        'pap_group_id' => $rowCDS['pap_group_id'],
                        'ppaq_start_date' => $rowCDS['ppaq_start_date'],
                        'ppaq_end_date' => $rowCDS['ppaq_end_date'],
                        'papq_name' => $rowCDS['papq_name'],
                        'papq_name_de' => $rowCDS['papq_name_de'],
                        'count_questions' => $rowCDS['count_questions'],
                        'count_question_ratings' => $rowCDS['count_question_ratings'],
                        'appointment_hours' => array()
                    );
                }
                if($rowCDS['pah_id'] !== NULL) {
                    $appointmentHours = array( 
                        'pah_id' => $rowCDS['pah_id'],
                        'pah_time' => $rowCDS['pah_time'],
                        'pah_doe' => $rowCDS['pah_doe']
                    );
                    $groupedData[$rowCDS['pap_id']]['appointment_hours'][$rowCDS['pah_id']] = $appointmentHours;
                    unset($appointmentHours);
                }
                unset($rowCDS);
            }
            unset($rowsCDS);
            
            //print("<pre>".print_r($groupedData,true)."</pre>");

            ?>
                <!-- 
                    Message Start
                    -->
                        <?php 
                            if(isset($_GET['messCDS'])) {
                                $enabledMessagesCDS = array(1,2,3,4,5,6,7,8,9,10,11,12,13);
                                $messCDS = $_GET['messCDS'];
                                $resultMessCDS = '';
                                if(in_array($messCDS, $enabledMessagesCDS)) {
                                    if($messCDS == 1){
                                        $resultMessCDS = '<div class="alert alert-success text-center" role="alert"><h4><strong>Uspješno izvršeno</strong></h4><br>Uspješno ste izvršili editovanje termina datuma castinga!</div>';
                                    }elseif($messCDS == 2){
                                        $resultMessCDS = '<div class="alert alert-danger text-center" role="alert"><h4><strong>Greška</strong></h4><br>Akcije nije izvršena!<br>Sistem nije izvršio editovanje termina datuma castinga! Obratite se administratoru sistema!</div>';
                                    }elseif($messCDS == 3){
                                        $resultMessCDS = '<div class="alert alert-success text-center" role="alert"><h4><strong>Uspješno izvršeno</strong></h4><br>Uspješno ste izvršili brisanje termina datuma castinga!</div>';
                                    }elseif($messCDS == 4){
                                        $resultMessCDS = '<div class="alert alert-danger text-center" role="alert"><h4><strong>Greška</strong></h4><br>Akcije nije izvršena!<br>Sistem nije izvršio brisanje termina datuma castinga! Obratite se administratoru sistema!</div>';
                                    }elseif($messCDS == 5){
                                        $resultMessCDS = '<div class="alert alert-success text-center" role="alert"><h4><strong>Uspješno izvršeno</strong></h4><br>Uspješno ste izvršili dodavanje novog termina datuma castinga!</div>';
                                    }elseif($messCDS == 6){
                                        $resultMessCDS = '<div class="alert alert-danger text-center" role="alert"><h4><strong>Greška</strong></h4><br>Akcije nije izvršena!<br>Sistem nije izvršio dodavanje novog termina datuma castinga! Obratite se administratoru sistema!</div>';
                                    }elseif($messCDS == 7){
                                        $resultMessCDS = '<div class="alert alert-success text-center" role="alert"><h4><strong>Uspješno izvršeno</strong></h4><br>Uspješno ste izvršili dodavanje novog/ih datuma castinga!</div>';
                                    }elseif($messCDS == 8){
                                        $resultMessCDS = '<div class="alert alert-danger text-center" role="alert"><h4><strong>Greška</strong></h4><br>Akcije nije izvršena!<br>Sistem nije izvršio dodavanje novog/ih datuma castinga! Obratite se administratoru sistema!</div>';
                                    }elseif($messCDS == 9){
                                        $resultMessCDS = '<div class="alert alert-danger text-center" role="alert"><h4><strong>Greška</strong></h4><br>Akcije nije izvršena!<br>Sistem nije izvršio dodavanje novog/ih datuma castinga! Obratite se administratoru sistema!</div>';
                                    }elseif($messCDS == 10){
                                        $resultMessCDS = '<div class="alert alert-success text-center" role="alert"><h4><strong>Uspješno izvršeno</strong></h4><br>Uspješno ste izvršili uređivanje detalja datuma castinga!</div>';
                                    }elseif($messCDS == 11){
                                        $resultMessCDS = '<div class="alert alert-danger text-center" role="alert"><h4><strong>Greška</strong></h4><br>Akcije nije izvršena!<br>Sistem nije izvršio uređivanje detalja datuma castinga! Obratite se administratoru sistema!</div>';
                                    }elseif($messCDS == 12){
                                        $resultMessCDS = '<div class="alert alert-success text-center" role="alert"><h4><strong>Uspješno izvršeno</strong></h4><br>Uspješno ste izvršili dodavanje/editovanje pitanja datuma castinga!</div>';
                                    }elseif($messCDS == 13){
                                        $resultMessCDS = '<div class="alert alert-danger text-center" role="alert"><h4><strong>Greška</strong></h4><br>Akcije nije izvršena!<br>Sistem nije izvršio dodavanje/editovanje pitanja datuma castinga! Obratite se administratoru sistema!</div>';
                                    }
                                } else {
                                    $resultMessCDS = '<div class="alert alert-danger text-center" role="alert"><h4><strong>Greška</strong></h4><br>Nepredviđena poruka odgovora! Kontaktirajte administratora sistema!.</div>';
                                }
                                
                                ?>
                                    <div class="row">
                                        <div class="col-xs-offset-2 col-xs-8">
                                            <?php 
                                                echo $resultMessCDS;
                                            ?>
                                        </div>
                                    </div>
                                <?php 
                                unset($enabledMessagesCDS);
                            }
                        ?>
                    <!-- 
                    Message End
                -->
                <!-- 
                    Content START 
                    -->
                        <div class="row">
                            <div class="col-xs-12">
                                <script>
                                    $(document).ready(function(){
                                        var allCD = $('#allCD').DataTable({
                                            responsive: true,
                                            "order": [[ 0, "desc" ]],
                                            "bAutoWidth": false,
                                            "aoColumns": [
                                                { "width": "5%"},
                                                { "width": "10%" },
                                                { "width": "8%" },
                                                { "width": "16%" },
                                                { "width": "8%", "bSortable": false  },
                                                { "width": "23%", "bSortable": false },
                                                { "width": "8%", "bSortable": false },
                                                { "width": "8%", "bSortable": false },
                                                { "width": "9%", "bSortable": false },
                                                { "width": "5%", "bSortable": false }
                                            ],
                                        });
                                    });
                                </script>
                                <table id="allCD" class="display" cellspacing="0" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="text-center">IDs</th>
                                            <th class="text-center">Grad</th>
                                            <th class="text-center">Datum</th>
                                            <th class="text-center">Lokacija</th>
                                            <th class="text-center">Google lokacija</th>
                                            <th class="text-center">Termini</th>
                                            <th class="text-center">Prvo slanje</th>
                                            <th class="text-center">Drugo slanje</th>
                                            <th class="text-center">Pitanja dodana</th>
                                            <th class="text-center">Akcija</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                            foreach ($groupedData AS $castingData) {

                                                $appointmentHoursExp = array();
                                                $appointmentHoursImp = '';
                                                if (count($castingData['appointment_hours']) > 0) {
                                                    foreach ($castingData['appointment_hours'] AS $appointmentHours) {
                                                        array_push($appointmentHoursExp, '<button class="btn btn-primary material-btn material-btn_primary" onclick="changeAppointmentHoursCD(this)" data-pah_id='.$appointmentHours['pah_id'].' data-pah_time='.date('H:i', strtotime($appointmentHours['pah_time'])).' data-pap_city='.$castingData['pap_city'].' data-pap_date='.date('d.m.Y', strtotime($castingData['pap_date'])).'>'.date('H:i', strtotime($appointmentHours['pah_time'])).'</button>');
                                                    }
                                                }
                                                array_push($appointmentHoursExp, '<button class="btn btn-success material-btn material-btn_success" onclick="addAppointmentHoursCD(this)" data-pap_id='.$castingData['pap_id'].' data-pap_city='.$castingData['pap_city'].' data-pap_date='.date('d.m.Y', strtotime($castingData['pap_date'])).'>Dodaj</button>');
                                                $appointmentHoursImp = implode(' ', $appointmentHoursExp);

                                                $questionsEditFlag = 0; 
                                                $questionsEditInfoSpan = ''; 
                                                if ($castingData['count_questions'] > 0) {
                                                    if ($castingData['count_question_ratings'] > 0) {
                                                        $questionsEditFlag = 2;
                                                        $questionsEditInfoSpan = '<span class="label label-success" title="Pitanja su dodana ali nije moguće editovati ista jer postoje kandidati koji su ocijenjeni prema postavljenim pitanjima! Dodano pitanja: '.$castingData['count_questions'].'. Za dodana pitanja postoji kandidata sa ocjenom: '.$castingData['count_question_ratings'].' "><i class="fa fa-lock me-3" aria-hidden="true"></i> DA</span>';
                                                    } else {
                                                        $questionsEditFlag = 1;
                                                        $questionsEditInfoSpan = '<span class="label label-primary" title="Pitanja su dodana i editovanje je moguće jer nema kandidata koji su ocijenjeni prema postavljenim pitanjima! Dodano pitanja: '.$castingData['count_questions'].'. Za dodana pitanja postoji kandidata sa ocjenom: '.$castingData['count_question_ratings'].' "><i class="fa fa-unlock me-3" aria-hidden="true"></i> DA</span>';
                                                    }
                                                } else {
                                                    $questionsEditInfoSpan = '<span class="label label-danger"><i class="fa fa-plus me-3" aria-hidden="true"></i> NE</span>';
                                                }

                                                ?>
                                                    <tr>
                                                        <td class="text-center">
                                                            <?php echo $castingData['pap_id']; ?>
                                                        </td>
                                                        <td>
                                                            <?php echo '<strong>'.$castingData['pap_city'].'</strong>'; ?>
                                                        </td>
                                                        <td data-order="<?php echo $castingData['pap_date']; ?>">
                                                            <?php echo '<span class="label label-info"><strong>'.date('d.m.Y', strtotime($castingData['pap_date'])).'</strong></span>'; ?>
                                                        </td>
                                                        <td>
                                                            <?php echo (($castingData['pap_location_name'] != null) ? $castingData['pap_location_name'] : '<span class="label label-warning">Nije ažurirano</span>'); ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <?php echo (($castingData['pap_google_maps_location'] != null) ? '<button class="btn btn-primary material-btn material-btn_primary" data-location_link_cd='.$castingData['pap_google_maps_location'].' onclick="copyGoogleLocationCD(this)">Copy</button>' : '<span class="label label-warning">Nije ažurirano</span>'); ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <?php echo $appointmentHoursImp; ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <?php echo (($castingData['pap_first_send_enabled'] == 1) ? '<span class="label label-info">'.$castingData['pap_first_sending_number_days'].' dan/a prije</span>' : '<span class="label label-danger">Isključeno</span>'); ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <?php echo (($castingData['pap_second_send_enabled'] == 1) ? '<span class="label label-info">'.$castingData['pap_second_sending_number_days'].' dan/a prije</span>' : '<span class="label label-danger">Isključeno</span>'); ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <?php echo $questionsEditInfoSpan; ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="btn-group material-btn-group">
                                                                <button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i><span class="caret material-btn__caret"></span></button>
                                                                <ul style = "top:33px; left:-60px; min-width:185px !important;" class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
                                                                    <?php 
                                                                        if ($questionsEditFlag == 1 OR $questionsEditFlag == 2) {
                                                                            ?>
                                                                                <li>
                                                                                    <a 
                                                                                        class="material-dropdown-menu__link"
                                                                                        onclick="addAppointmentQuestionsCD(this)" 
                                                                                        data-pap_id="<?php echo $castingData['pap_id']; ?>" 
                                                                                        data-pap_city="<?php echo $castingData['pap_city']; ?>" 
                                                                                        data-pap_date="<?php echo date('d.m.Y', strtotime($castingData['pap_date'])); ?>"
                                                                                        data-edit_questions_flag="<?php echo 2; ?>"
                                                                                        data-order_name="<?php echo $nalog_naziv; ?>"
                                                                                        data-company_name="<?php echo $kompanija; ?>" 
                                                                                    >
                                                                                        <i class="fa fa-eye me-4" aria-hidden="true"></i> 
                                                                                        Pregled pitanja
                                                                                    </a>
                                                                                </li>
                                                                    <?php 
                                                                        }
                                                                        if ($questionsEditFlag == 0 OR $questionsEditFlag == 1) {
                                                                            ?>
                                                                                <li>
                                                                                    <a 
                                                                                        class="material-dropdown-menu__link" 
                                                                                        onclick="addAppointmentQuestionsCD(this)" 
                                                                                        data-pap_id="<?php echo $castingData['pap_id']; ?>" 
                                                                                        data-pap_city="<?php echo $castingData['pap_city']; ?>" 
                                                                                        data-pap_date="<?php echo date('d.m.Y', strtotime($castingData['pap_date'])); ?>"
                                                                                        data-edit_questions_flag="<?php echo $questionsEditFlag; ?>"
                                                                                        data-order_name="<?php echo $nalog_naziv; ?>"
                                                                                        data-company_name="<?php echo $kompanija; ?>"
                                                                                    >
                                                                                        <?php 
                                                                                            echo (($questionsEditFlag == 0) ? '<i class="fa fa-plus me-4" aria-hidden="true"></i> Dodaj pitanja' : '<i class="fa fa-pencil-square-o me-4" aria-hidden="true"></i> Uredi pitanja');
                                                                                        ?>
                                                                                    </a>
                                                                                </li>
                                                                            <?php 
                                                                        }
                                                                    ?>
                                                                    <li>
                                                                        <a 
                                                                            class="material-dropdown-menu__link" 
                                                                            onclick="addAppointmentDetailsCD(this)" 
                                                                            data-pap_id="<?php echo $castingData['pap_id']; ?>" 
                                                                            data-pap_city="<?php echo $castingData['pap_city']; ?>" 
                                                                            data-pap_date="<?php echo date('d.m.Y', strtotime($castingData['pap_date'])); ?>"
                                                                            data-pap_location_name ="<?php echo $castingData['pap_location_name']; ?>"
                                                                            data-pap_google_maps_location ="<?php echo $castingData['pap_google_maps_location']; ?>"
                                                                            data-pap_first_send_enabled = "<?php echo $castingData['pap_first_send_enabled']; ?>"
                                                                            data-pap_first_sending_number_days = "<?php echo $castingData['pap_first_sending_number_days']; ?>"
                                                                            data-pap_second_send_enabled = "<?php echo $castingData['pap_second_send_enabled']; ?>"
                                                                            data-pap_second_sending_number_days = "<?php echo $castingData['pap_second_sending_number_days']; ?>"

                                                                        >
                                                                            <i class="fa fa-info-circle me-4" aria-hidden="true"></i> 
                                                                            Detalji castinga
                                                                        </a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php 
                                                unset($castingData);
                                                unset($appointmentHoursExp);
                                                unset($appointmentHoursImp);
                                                unset($questionsEditInfoSpan);
                                                unset($questionsEditFlag);
                                            }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <!-- 
                    Content END 
                -->
                                            
                <!-- 
                    Modals START
                    -->
                        <div class="modal material-modal material-modal_primary fade text-left" id="changeAppointmentHoursCD">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content material-modal__content">
                                    <div class="modal-header material-modal__header">
                                        <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title material-modal__title"><i style = "margin-right: 10px;" class="fa fa-pencil-square" aria-hidden="true"></i><span id="modal-title-text"></span></h4>
                                    </div> 
                                    <div class="modal-body material-modal__body">
                                        <form action="<?php getSiteURL(); ?>jobsoft_settings/backend/do_settings.php?page=change_appointment_hours" enctype="multipart/form-data" method="post" role="form" class="form-horizontal" id="changeAppointmentHoursCDForm">
                                            <input type="hidden" id="pah_id" name="pah_id">
                                            <input type="hidden" id="order_id" name="order_id" value="<?php echo $orderId; ?>">
                                            <div class="row">
                                                <div class="col-md-offset-2 col-sm-8">
                                                    <div class="messageCAHCD form-group hidden">    
                                                        
                                                    </div>
                                                    <div class="form-group">    
                                                        <div class="col-sm-12">
                                                            <label for="pah_time" class="col-sm-4 control-label">
                                                                <span class="text-danger">
                                                                    *
                                                                </span>
                                                                Vrijeme termina:
                                                            </label>
                                                            <div class="col-sm-8">
                                                                <div class="">
                                                                    <input type="text" class="form-control flatpickr-input active" name="pah_time" id="pah_time" placeholder="Termin" readonly="readonly" required>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer material-modal__footer">
                                                <button class="btn btn-danger material-btn material-btn_danger" onclick="deleteAppointmentHoursCD(event)"><i class="fa fa-trash-o" aria-hidden="true"></i> Obriši termin</button>
                                                <button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
                                                <button type="submit" class="btn btn-primary material-btn material-btn_primary" form="changeAppointmentHoursCDForm"><i class="fa fa-check-square-o" aria-hidden="true"></i> Spremi</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal material-modal material-modal_primary fade text-left" id="addAppointmentHoursCD">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content material-modal__content">
                                    <div class="modal-header material-modal__header">
                                        <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title material-modal__title"><i style = "margin-right: 10px;" class="fa fa-pencil-square" aria-hidden="true"></i><span id="modal-title-text"></span></h4>
                                    </div> 
                                    <div class="modal-body material-modal__body">
                                        <form action="<?php getSiteURL(); ?>jobsoft_settings/backend/do_settings.php?page=add_appointment_hours" enctype="multipart/form-data" method="post" role="form" class="form-horizontal" id="addAppointmentHoursCDForm">
                                            <input type="hidden" id="pap_id" name="pap_id">
                                            <input type="hidden" id="order_id" name="order_id" value="<?php echo $orderId; ?>">
                                            <div class="row">
                                                <div class="col-md-offset-2 col-sm-8">
                                                    <div class="messageAAHCD form-group hidden">    
                                                        
                                                    </div>
                                                    <div class="form-group">    
                                                        <div class="col-sm-12">
                                                            <label for="pah_time" class="col-sm-4 control-label">
                                                                <span class="text-danger">
                                                                    *
                                                                </span>
                                                                Vrijeme termina:
                                                            </label>
                                                            <div class="col-sm-8">
                                                                <div class="">
                                                                    <input type="text" class="form-control flatpickr-input active" name="pah_time" id="pah_time" placeholder="Termin" readonly="readonly" required>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer material-modal__footer">
                                                <button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
                                                <button type="submit" class="btn btn-primary material-btn material-btn_primary" form="addAppointmentHoursCDForm"><i class="fa fa-check-square-o" aria-hidden="true"></i> Spremi</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal material-modal material-modal_primary fade text-left" id="addDetailsCD">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content material-modal__content">
                                    <div class="modal-header material-modal__header">
                                        <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title material-modal__title"><i style = "margin-right: 10px;" class="fa fa-pencil-square" aria-hidden="true"></i><span id="modal-title-text"></span></h4>
                                    </div> 
                                    <div class="modal-body material-modal__body">
                                        <form action="<?php getSiteURL(); ?>jobsoft_settings/backend/do_settings.php?page=add_appointment_details" enctype="multipart/form-data" method="post" role="form" class="form-horizontal" id="addDetailsCDForm">
                                            <input type="hidden" id="pap_id" name="pap_id">
                                            <input type="hidden" id="order_id" name="order_id" value="<?php echo $orderId; ?>">
                                            <div class="row">
                                                <div class="col-md-offset-1 col-sm-10">

                                                    <div class="message_ADCD form-group hidden">    
                                                            
                                                    </div>

                                                    <div class="form-group">    
                                                        <div class="col-sm-12">
                                                            <label for="dates_CD" class="col-sm-4 control-label">
                                                                <span class="text-danger">
                                                                    *
                                                                </span>
                                                                Datum termina:
                                                            </label>
                                                            <div class="col-sm-8">
                                                                <div class="materail-input-block materail-input-block_primary">
                                                                    <input type="text" class="form-control flatpickr-input materail-input active" name="dates_CD" id="dates_CD" placeholder="Odaberite dane" required>
                                                                    <span class="materail-input-block__line"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <div class="col-sm-12">
                                                            <label for="city_CD" class="col-sm-4 control-label">
                                                                <span class="text-danger">
                                                                    *
                                                                </span>
                                                                Grad:
                                                            </label>
                                                            <div class="col-sm-8">
                                                                <div class="materail-input-block materail-input-block_primary">
                                                                    <input class="form-control materail-input" type="text" name="city_CD" id="city_CD" placeholder="Unesite grad" required>
                                                                    <span class="materail-input-block__line"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <div class="col-sm-12">
                                                            <label for="address_CD" class="col-sm-4 control-label">
                                                                Naziv / adresa lokacija:
                                                            </label>
                                                            <div class="col-sm-7">
                                                                <div class="materail-input-block materail-input-block_primary">
                                                                    <input class="form-control materail-input" type="text" name="address_CD" id="address_CD" placeholder="Lokacija - adresa">
                                                                    <span class="materail-input-block__line"></span>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-1">
                                                                <span class="btn btn-danger material-btn material-btn_danger question_address_CD">
                                                                    <i class="fa fa-question-circle" aria-hidden="true"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <div class="col-sm-12">
                                                            <label for="address_link_CD" class="col-sm-4 control-label">
                                                                Link lokacije:
                                                            </label>
                                                            <div class="col-sm-7">
                                                                <div class="materail-input-block materail-input-block_primary">
                                                                    <input class="form-control materail-input" type="url" name="address_link_CD" id="address_link_CD" placeholder="https://maps.app.goo.gl/tB2cpZLHU9ZvLZpx6">
                                                                    <span class="materail-input-block__line"></span>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-1">
                                                                <span class="btn btn-danger material-btn material-btn_danger question_address_link_CD">
                                                                    <i class="fa fa-question-circle" aria-hidden="true"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <div class="col-sm-12">
                                                            <label for="first_send_enabled_CD" class="col-sm-4 control-label">
                                                                Uključi slanje prve poruke:
                                                            </label>
                                                            <div class="col-sm-8">
                                                                <div class="main-container__column materail-switch materail-switch_primary">
                                                                    <input class="materail-switch__element" type="checkbox" id="first_send_enabled_CD" name="first_send_enabled_CD" value="1">
                                                                    <label class="materail-switch__label" for="first_send_enabled_CD"></label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group hidden">
                                                        <div class="col-sm-12">
                                                            <label for="first_send_days_CD" class="col-sm-4 control-label">
                                                                <span class="text-danger">
                                                                    *
                                                                </span>
                                                                Broj dana prve poruke:
                                                            </label>
                                                            <div class="col-sm-8">
                                                                <div class="materail-input-block materail-input-block_primary">
                                                                    <input class="form-control materail-input" type="number" name="first_send_days_CD" id="first_send_days_CD" placeholder="Broj dana" step="1">
                                                                    <span class="materail-input-block__line"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <div class="col-sm-12">
                                                            <label for="second_send_enabled_CD" class="col-sm-4 control-label">
                                                                Uključi slanje druge poruke:
                                                            </label>
                                                            <div class="col-sm-8">
                                                                <div class="main-container__column materail-switch materail-switch_primary">
                                                                    <input class="materail-switch__element" type="checkbox" id="second_send_enabled_CD" name="second_send_enabled_CD" value="1">
                                                                    <label class="materail-switch__label" for="second_send_enabled_CD"></label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group hidden">
                                                        <div class="col-sm-12">
                                                            <label for="second_send_days_CD" class="col-sm-4 control-label">
                                                                <span class="text-danger">
                                                                    *
                                                                </span>
                                                                Broj dana druge poruke:
                                                            </label>
                                                            <div class="col-sm-8">
                                                                <div class="materail-input-block materail-input-block_primary">
                                                                    <input class="form-control materail-input" type="number" name="second_send_days_CD" id="second_send_days_CD" placeholder="Broj dana" step="1">
                                                                    <span class="materail-input-block__line"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="form-group idk_margin_top20">
                                                <div class="col-md-offset-2 col-sm-8 text-center">
                                                    <small>
                                                        <strong>
                                                            Sva polja označena sa 
                                                            <span class="text-danger">
                                                                *
                                                            </span>  
                                                            su obavezna!
                                                        </strong>
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="modal-footer material-modal__footer">
                                                <button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
                                                <button type="submit" class="btn btn-primary material-btn material-btn_primary" form="addDetailsCDForm"><i class="fa fa-check-square-o" aria-hidden="true"></i> Spremi</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal material-modal material-modal_primary fade text-left" id="addQuestionsCD">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content material-modal__content">
                                    <div class="modal-header material-modal__header">
                                        <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title material-modal__title"><i class="" aria-hidden="true"></i><span id="modal-title-text"></span></h4>
                                    </div> 
                                    <div class="modal-body material-modal__body">
                                        <form action="<?php getSiteURL(); ?>jobsoft_settings/backend/do_settings.php?page=add_appointment_questions" enctype="multipart/form-data" method="post" role="form" class="form-horizontal" id="addQuestionsCDForm">
                                            <input type="hidden" id="pap_id" name="pap_id">
                                            <input type="hidden" id="order_id" name="order_id" value="<?php echo $orderId; ?>">
                                            <input type="hidden" id="edit_questions_flag" name="edit_questions_flag">
                                            <input type="hidden" id="panel_ids" name="panel_ids">
                                            <input type="hidden" id="pqu_ids" name="pqu_ids">
                                            
                                            <div class="message_AQCD form-group hidden">    
                                                
                                            </div>

                                            <div class="form-group">    
                                                <div class="col-sm-12">
                                                    <label for="setting_mode" class="col-sm-3 control-label">
                                                        <span class="text-danger">*</span> Način postavljanja:
                                                    </label>
                                                    <div class="col-sm-9">
                                                        <div class="">
                                                            <select class="selectpicker" id="setting_mode" name="setting_mode" title = "Odaberite opciju" required>
                                                                <option value="past">Povuci pitanja od proslih castinga</option>
                                                                <option value="new">Kreiraj novi set pitanja</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group hidden">    
                                                <div class="col-sm-12">
                                                    <label for="appointment_dates" class="col-sm-3 control-label">
                                                        <span class="text-danger">*</span> Datum castinga:
                                                    </label>
                                                    <div class="col-sm-9">
                                                        <div class="">
                                                            <select class="selectpicker" id="appointment_dates" name="appointment_dates" title = "Odaberite datum">
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="questions_AQCD hidden">

                                            </div>

                                            <div class="form-group mt-3">
                                                <div class="col-sm-12">
                                                    <label for="new_questions_add" class="col-sm-10 control-label">
                                                        Dodaj pitanja:
                                                    </label>
                                                    <div class="col-sm-2 text-right">
                                                        <span id="new_questions_add" onclick="getNewQuestionsAQCD()" class="btn material-btn btn-primary"><i class="fa fa-plus" aria-hidden="true"></i></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">    
                                                <div class="col-sm-12">
                                                    <label for="new_questions" class="col-sm-3 control-label">
                                                        <span class="text-danger">*</span> Odaberite pitanja:
                                                    </label>
                                                    <div class="col-sm-9">
                                                        <div class="">
                                                            <select class="selectpicker" data-live-search="true" id="new_questions" name="new_questions" title = "Pitanja" multiple>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group new_questions_decision">
                                                <div class="col-sm-12">
                                                    <div class="col-sm-12 text-right">
                                                        <span id="new_questions_yes" onclick="decisionYesNewQuestions()" class="mx-3 btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive">
                                                            <i class="fa fa-check" aria-hidden="true"></i>
                                                            Potvrdi odabir
                                                        </span>
                                                        <span id="new_questions_no" onclick="decisionNoNewQuestions()" class="ms-3 btn material-btn material-btn-icon-danger material-btn_danger main-container__column material-btn-icon-responsive">
                                                            <i class="fa fa-times" aria-hidden="true"></i>
                                                            Odustani
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group idk_margin_top20 remark_form">
                                                <div class="col-md-offset-2 col-sm-8 text-center">
                                                    <small>
                                                        <strong>
                                                            Sva polja označena sa 
                                                            <span class="text-danger">
                                                                *
                                                            </span>  
                                                            su obavezna!
                                                        </strong>
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="modal-footer material-modal__footer">
                                                <button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
                                                <button type="submit" class="btn btn-primary material-btn material-btn_primary" form="addQuestionsCDForm"><i class="fa fa-check-square-o" aria-hidden="true"></i> Spremi</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <!-- 
                    Modals END
                -->

                <!-- 
                    Script START 
                    -->
                        <script>
                            /*
                                Copy Button START
                                */
                                    function copyGoogleLocationCD(thisRow) {
                                        $(thisRow).text('Copied').removeClass('btn-primary').removeClass('material-btn_primary').addClass('btn-success').addClass('material-btn_success');
                                        let location_link_cd = $(thisRow).data('location_link_cd');
                                        var $temp = $("<input>");
                                        $("body").append($temp);
                                        $temp.val(location_link_cd).select();
                                        document.execCommand("copy");
                                        $temp.remove();
                                        setTimeout(function(){
                                            $(thisRow).text('Copy').removeClass('btn-success').removeClass('material-btn_success').addClass('btn-primary').addClass('material-btn_primary');
                                        }, 2000);
                                    }; 
                                /*
                                Copy Button END
                            */
                            
                            /*
                                Edit Delete Appointment Hours START
                                */
                                    function changeAppointmentHoursCD(thisRow) {
                                        let pap_city = $(thisRow).data('pap_city');
                                        let pap_date = $(thisRow).data('pap_date');
                                        let pah_id = $(thisRow).data('pah_id'); 
                                        let pah_time = $(thisRow).data('pah_time');

                                        setModalTitleForChangeAppointmentHoursCD(pap_city, pap_date);
                                        setInitialValueForChangeAppointmentHoursCD(pah_id, pah_time);

                                        $('#changeAppointmentHoursCD', '#castingDatesSettings').modal('show');
                                    };
                                    function setModalTitleForChangeAppointmentHoursCD(pap_city, pap_date) {
                                        if (pap_city != '' && pap_date != '') {
                                            $('#modal-title-text', '#changeAppointmentHoursCD', '#castingDatesSettings').text(pap_city + ' - ' + pap_date);
                                        } else {
                                            $('#modal-title-text', '#changeAppointmentHoursCD', '#castingDatesSettings').text('');
                                        }
                                    }; 
                                    function setInitialValueForChangeAppointmentHoursCD(pah_id, pah_time) {
                                        if (pah_id != '' && pah_time != '') {
                                            $('#pah_id', '#changeAppointmentHoursCDForm', '#castingDatesSettings').val(pah_id);
                                            $('#pah_time', '#changeAppointmentHoursCDForm', '#castingDatesSettings').flatpickr({
                                                enableTime: true,
                                                timeFormat: "H:i:s",
                                                disableMobile: "true",
                                                time_24hr: true,
                                                noCalendar: true,
                                                defaultDate: pah_time
                                            });
                                        } else {
                                            $('#pah_id', '#changeAppointmentHoursCDForm', '#castingDatesSettings').val(null);
                                            if ($('#pah_time', '#changeAppointmentHoursCDForm', '#castingDatesSettings').data('flatpickr') === undefined) {
                                                $('#pah_time', '#changeAppointmentHoursCDForm', '#castingDatesSettings').flatpickr({
                                                    enableTime: true,
                                                    timeFormat: "H:i:s",
                                                    disableMobile: "true",
                                                    time_24hr: true,
                                                    noCalendar: true,
                                                    defaultDate: null
                                                }).destroy();
                                            }
                                        }
                                    };
                                    $('#changeAppointmentHoursCD', '#castingDatesSettings').on('hidden.bs.modal', function() {
                                        setModalTitleForChangeAppointmentHoursCD('', '');
                                        setInitialValueForChangeAppointmentHoursCD('');
                                    });
                                    function messageCAHCD(type, shown, message) {
                                        if (shown === true) {
                                            $('.messageCAHCD','#changeAppointmentHoursCDForm', '#castingDatesSettings').html(`
                                                <div class="col-sm-12">
                                                    <div class="alert alert-` + type + ` text-center" role="alert">
                                                        ` + message + `
                                                    </div>
                                                </div>
                                            `).removeClass('hidden');
                                            
                                            setTimeout(function(){
                                                $('.messageCAHCD','#changeAppointmentHoursCDForm', '#castingDatesSettings').html('').addClass('hidden');
                                            }, 3000);
                                        }
                                    };
                                    $('#changeAppointmentHoursCDForm', '#castingDatesSettings').on('submit', function(event) {
                                        event.preventDefault();
                                        let pah_id = $('#pah_id', '#changeAppointmentHoursCDForm', '#castingDatesSettings').val();
                                        $.ajax({
                                            url: 'ajax_data.php?page=check_candidates_for_appointment_time',
                                            type: 'POST',
                                            dataType: 'html',
                                            data:{
                                                'termin_pah_id': pah_id,
                                            },
                                            success : function (response){
                                                if (response == 0) {
                                                    $('#changeAppointmentHoursCDForm', '#castingDatesSettings').unbind('submit').submit();
                                                } else {
                                                    messageCAHCD('warning', true, 'Nije moguće izvršiti promjenu termina jer za isti ima vezanih kandidata!');
                                                }
                                            },
                                            error: function (xhr, ajaxOptions, thrownError) {
                                                messageCAHCD('danger', true, thrownError);
                                            }
                                        });
                                    });
                                    function deleteAppointmentHoursCD(event) {
                                        event.preventDefault();
                                        let pah_id = $('#pah_id', '#changeAppointmentHoursCDForm', '#castingDatesSettings').val();
                                        $.ajax({
                                            url: 'ajax_data.php?page=check_candidates_for_appointment_time',
                                            type: 'POST',
                                            dataType: 'html',
                                            data:{
                                                'termin_pah_id': pah_id,
                                            },
                                            success : function (response){
                                                if (response == 0) {
                                                    $.ajax({
                                                        url: 'jobsoft_settings/backend/do_settings.php?page=delete_appointment_hours',
                                                        type: 'POST',
                                                        dataType: 'html',
                                                        data:{
                                                            'pah_id': pah_id,
                                                        },
                                                        success : function (response){
                                                            let currentUrl = window.location.href;
                                                            currentUrl = currentUrl.replace(/&?messCDS=\d*/g, '');
                                                            if (response == 3) {
                                                                currentUrl += '&messCDS=3';
                                                                window.location.href = currentUrl;
                                                            } else if (response == 4) {
                                                                currentUrl += '&messCDS=4';
                                                                window.location.href = currentUrl;
                                                            } else {
                                                                messageCAHCD('warning', true, 'Nepotpuni ili neispravni podaci. Molimo vas da provjerite i ponovno pošaljete sve potrebne informacije!');
                                                            }
                                                        },
                                                        error: function (xhr, ajaxOptions, thrownError) {
                                                            messageCAHCD('danger', true, thrownError);
                                                        }
                                                    });
                                                } else {
                                                    messageCAHCD('warning', true, 'Nije moguće izvršiti brisanje termina jer za isti ima vezanih kandidata!');
                                                }
                                            },
                                            error: function (xhr, ajaxOptions, thrownError) {
                                                messageCAHCD('danger', true, thrownError);
                                            }
                                        });
                                    };
                                /*
                                Edit Delete Appointment Hours START
                            */

                            /*
                                Add Appointment Hours START
                                */
                                    function addAppointmentHoursCD(thisRow) {
                                        let pap_city = $(thisRow).data('pap_city');
                                        let pap_date = $(thisRow).data('pap_date');
                                        let pap_id = $(thisRow).data('pap_id');

                                        setModalTitleForAddAppointmentHoursCD(pap_city, pap_date);
                                        setInitialValueForAddAppointmentHoursCD(pap_id);

                                        $('#addAppointmentHoursCD', '#castingDatesSettings').modal('show');
                                    }; 
                                    function setModalTitleForAddAppointmentHoursCD(pap_city, pap_date) {
                                        if (pap_city != '' && pap_date != '') {
                                            $('#modal-title-text', '#addAppointmentHoursCD', '#castingDatesSettings').text(pap_city + ' - ' + pap_date);
                                        } else {
                                            $('#modal-title-text', '#addAppointmentHoursCD', '#castingDatesSettings').text('');
                                        }
                                    };
                                    function setInitialValueForAddAppointmentHoursCD(pap_id) {
                                        if (pap_id != '') {
                                            $('#pap_id', '#addAppointmentHoursCD', '#castingDatesSettings').val(pap_id);
                                            $('#pah_time', '#addAppointmentHoursCD', '#castingDatesSettings').flatpickr({
                                                enableTime: true,
                                                timeFormat: "H:i:s",
                                                disableMobile: "true",
                                                time_24hr: true,
                                                noCalendar: true,
                                                defaultDate: null
                                            });
                                        } else {
                                            $('#pap_id', '#addAppointmentHoursCD', '#castingDatesSettings').val(null);
                                            if ($('#pah_time', '#addAppointmentHoursCD', '#castingDatesSettings').data('flatpickr') === undefined) {
                                                $('#pah_time', '#addAppointmentHoursCD', '#castingDatesSettings').flatpickr({
                                                    enableTime: true,
                                                    timeFormat: "H:i:s",
                                                    disableMobile: "true",
                                                    time_24hr: true,
                                                    noCalendar: true,
                                                    defaultDate: null
                                                }).destroy();
                                            }
                                        }
                                    };
                                    $('#addAppointmentHoursCD', '#castingDatesSettings').on('hidden.bs.modal', function() {
                                        setModalTitleForAddAppointmentHoursCD('', '');
                                        setInitialValueForAddAppointmentHoursCD('');
                                    });
                               /*
                                Add Appointment Hours END
                            */
                            
                            /*
                                Add Appointment Details START 
                                */
                                    function messageADCD(type, message) {
                                        $('.message_ADCD','#addDetailsCD', '#castingDatesSettings').append(`
                                            <div class="col-sm-12">
                                                <div class="alert alert-` + type + ` text-center" role="alert">
                                                    ` + message + `
                                                </div>
                                            </div>
                                        `).removeClass('hidden');
                                        
                                        setTimeout(function(){
                                            $('.message_ADCD','#addDetailsCD', '#castingDatesSettings').html('').addClass('hidden');
                                        }, 10000);
                                    };
                                    function addAppointmentDetailsCD(thisRow) {
                                        let pap_city = $(thisRow).data('pap_city');
                                        let pap_date = $(thisRow).data('pap_date');
                                        let pap_id = $(thisRow).data('pap_id');
                                        let pap_location_name = $(thisRow).data('pap_location_name');
                                        let pap_google_maps_location = $(thisRow).data('pap_google_maps_location');
                                        let pap_first_send_enabled = $(thisRow).data('pap_first_send_enabled');
                                        let pap_first_sending_number_days = $(thisRow).data('pap_first_sending_number_days');
                                        let pap_second_send_enabled = $(thisRow).data('pap_second_send_enabled');
                                        let pap_second_sending_number_days = $(thisRow).data('pap_second_sending_number_days');

                                        setModalTitleForAddAppointmentDetailsCD(pap_city, pap_date);
                                        setInitialValueForAddAppointmentDetailsCD(pap_id, pap_city, pap_date, pap_location_name, pap_google_maps_location, pap_first_send_enabled, pap_first_sending_number_days, pap_second_send_enabled, pap_second_sending_number_days);

                                        $('#addDetailsCD', '#castingDatesSettings').modal('show');
                                    };
                                    function checkingSentMessages(pap_id, pap_city, pap_date) {
                                        $.ajax({
                                            url: 'jobsoft_settings/ajax/ajax_data.php?page=checking_sent_messages',
                                            type: 'POST',
                                            cache: false,
                                            data:{'pap_id': pap_id},
                                            success : function (result){
                                                var result_decode = JSON.parse(result);
                                                let dates_CD = $('#dates_CD', '#addDetailsCD', '#castingDatesSettings');
                                                let city_CD = $('#city_CD', '#addDetailsCD', '#castingDatesSettings');
                                                let first_send_enabled_CD = $('#first_send_enabled_CD', '#addDetailsCD', '#castingDatesSettings'); 
                                                let first_send_days_CD = $('#first_send_days_CD', '#addDetailsCD', '#castingDatesSettings'); 
                                                let second_send_enabled_CD = $('#second_send_enabled_CD', '#addDetailsCD', '#castingDatesSettings');
                                                let second_send_days_CD = $('#second_send_days_CD', '#addDetailsCD', '#castingDatesSettings'); 
                                                if (result_decode.status === 1) {

                                                    if (result_decode.first_message_counter > 0 || result_decode.second_message_counter > 0 || result_decode.past_date == 1) {
                                                        if (dates_CD.data('flatpickr') === undefined) {
                                                            dates_CD.flatpickr().destroy();
                                                        }
                                                        dates_CD.prop('disabled', true).val(pap_date);
                                                        city_CD.prop('disabled', true);
                                                    }

                                                    if (result_decode.first_message_counter > 0 || result_decode.past_date == 1) {
                                                        first_send_enabled_CD.prop('disabled', true);
                                                        first_send_days_CD.prop('disabled', true);
                                                    }

                                                    if (result_decode.second_message_counter > 0 || result_decode.past_date == 1) {
                                                        second_send_enabled_CD.prop('disabled', true);
                                                        second_send_days_CD.prop('disabled', true);
                                                    }

                                                } else {
                                                    messageADCD('warning', result_decode.message);
                                                }
                                            },
                                            error: function (xhr, ajaxOptions, thrownError) {
                                                if (xhr.status == 400) {
                                                    messageADCD('danger', xhr.responseText); 
                                                } else {
                                                    messageADCD('danger', thrownError);
                                                }
                                            }
                                        });
                                    };
                                    function setModalTitleForAddAppointmentDetailsCD(pap_city, pap_date) {
                                        if (pap_city != '' && pap_date != '') {
                                            $('#modal-title-text', '#addDetailsCD', '#castingDatesSettings').text('Detalji za ' + pap_city + ' - ' + pap_date);
                                        } else {
                                            $('#modal-title-text', '#addDetailsCD', '#castingDatesSettings').text('');
                                        }
                                    };
                                    function setInitialValueForAddAppointmentDetailsCD(pap_id, pap_city, pap_date, pap_location_name, pap_google_maps_location, pap_first_send_enabled, pap_first_sending_number_days, pap_second_send_enabled, pap_second_sending_number_days) {
                                        if (pap_id != '') {
                                            $('#pap_id', '#addDetailsCD', '#castingDatesSettings').val(pap_id);
                                            $('#city_CD', '#addDetailsCD', '#castingDatesSettings').removeAttr('readonly').val(pap_city);
                                            $('#dates_CD', '#addDetailsCD', '#castingDatesSettings').removeAttr('readonly').flatpickr({
                                                dateFormat: "d.m.Y",
                                                disableMobile: true,
                                                defaultDate: pap_date
                                            });
                                            $('#address_CD', '#addDetailsCD', '#castingDatesSettings').val(pap_location_name);
                                            $('#address_link_CD', '#addDetailsCD', '#castingDatesSettings').val(pap_google_maps_location);
                                            $('#first_send_enabled_CD', '#addDetailsCD', '#castingDatesSettings').prop('checked', ((pap_first_send_enabled == 1) ? true : false));
                                            if (pap_first_send_enabled == 1) {
                                                $('#first_send_days_CD', '#addDetailsCD', '#castingDatesSettings').val(pap_first_sending_number_days).prop('required', true).closest('.form-group').removeClass('hidden');
                                            } else {
                                                $('#first_send_days_CD', '#addDetailsCD', '#castingDatesSettings').val(7).prop('required', false).closest('.form-group').addClass('hidden');
                                            }
                                            $('#second_send_enabled_CD', '#addDetailsCD', '#castingDatesSettings').prop('checked', ((pap_second_send_enabled == 1) ? true : false));
                                            if (pap_second_send_enabled == 1) {
                                                $('#second_send_days_CD', '#addDetailsCD', '#castingDatesSettings').val(pap_second_sending_number_days).prop('required', true).closest('.form-group').removeClass('hidden');
                                            } else {
                                                $('#second_send_days_CD', '#addDetailsCD', '#castingDatesSettings').val(2).prop('required', false).closest('.form-group').addClass('hidden');
                                            }
                                            if (pap_first_send_enabled == 1 || pap_second_send_enabled == 1) {
                                                minMaxSendDatesADCD();
                                            }

                                            checkingSentMessages(pap_id, pap_city, pap_date);
                                        } else {
                                            $('#pap_id', '#addDetailsCD', '#castingDatesSettings').val(null);
                                            $('#city_CD', '#addDetailsCD', '#castingDatesSettings').val(null).prop('disabled', false);
                                            $('#dates_CD', '#addDetailsCD', '#castingDatesSettings').val(null).prop('disabled', false);
                                            if ($('#dates_CD', '#addDetailsCD', '#castingDatesSettings').data('flatpickr') === undefined) {
                                                $('#dates_CD', '#addDetailsCD', '#castingDatesSettings').flatpickr().destroy();
                                            }
                                            $('#address_CD', '#addDetailsCD', '#castingDatesSettings').val(null);
                                            $('#address_link_CD', '#addDetailsCD', '#castingDatesSettings').val(null);
                                            $('#first_send_enabled_CD', '#addDetailsCD', '#castingDatesSettings').prop('checked', false).prop('disabled', false);
                                            $('#second_send_enabled_CD', '#addDetailsCD', '#castingDatesSettings').prop('checked', false).prop('disabled', false);
                                            $('#first_send_days_CD', '#addDetailsCD', '#castingDatesSettings').val(7).prop('required', false).prop('disabled', false).closest('.form-group').addClass('hidden').removeAttr("min").removeAttr("max");
                                            $('#second_send_days_CD', '#addDetailsCD', '#castingDatesSettings').val(2).prop('required', false).prop('disabled', false).closest('.form-group').addClass('hidden').removeAttr("min").removeAttr("max");
                                        }
                                    };
                                    function showHideSendDatesADCD(field_send_enabled_CD, field_send_days_CD) {
                                        let send_enabled_CD = $('#'+field_send_enabled_CD+'', '#addDetailsCD', '#castingDatesSettings'); 
                                        let send_days_CD = $('#'+field_send_days_CD+'', '#addDetailsCD', '#castingDatesSettings').val();
                                        if (send_enabled_CD.is(':checked')) {
                                            $('#'+field_send_days_CD+'', '#addDetailsCD', '#castingDatesSettings').val(send_days_CD).prop('required', true).closest('.form-group').removeClass('hidden');
                                        } else {
                                            $('#'+field_send_days_CD+'', '#addDetailsCD', '#castingDatesSettings').val(send_days_CD).prop('required', false).closest('.form-group').addClass('hidden');
                                        }
                                        minMaxSendDatesADCD();
                                    };
                                    function minMaxSendDatesADCD(changed_field) {
                                        let first_send_days_CD = parseInt($('#first_send_days_CD', '#addDetailsCD', '#castingDatesSettings').val());
                                        let second_send_days_CD = parseInt($('#second_send_days_CD', '#addDetailsCD', '#castingDatesSettings').val());
                                        /*
                                            Zbog direktnih unosa preko tastature START
                                            */
                                                if (changed_field == 'first_send_days_CD' && first_send_days_CD < (second_send_days_CD + 2)) {
                                                    messageADCD('warning', '<h4><span class="text-danger">*</span>Broj dana prve poruke</h4>Razmak između broja dana mora biti najmanje 2 dana!');
                                                    first_send_days_CD = (second_send_days_CD + 2);
                                                    $('#first_send_days_CD', '#addDetailsCD', '#castingDatesSettings').val(first_send_days_CD);
                                                }

                                                if (changed_field == 'second_send_days_CD' && second_send_days_CD > (first_send_days_CD - 2)) {
                                                    messageADCD('warning', '<h4><span class="text-danger">*</span>Broj dana druge poruke</h4>Razmak između broja dana mora biti najmanje 2 dana!');
                                                    second_send_days_CD = (first_send_days_CD - 2);
                                                    $('#second_send_days_CD', '#addDetailsCD', '#castingDatesSettings').val(second_send_days_CD);
                                                }

                                                if (changed_field == 'second_send_days_CD' && second_send_days_CD < 1) {
                                                    messageADCD('warning', '<h4><span class="text-danger">*</span>Broj dana druge poruke</h4>Razmak između broja dana mora biti najmanje 2 dana!');
                                                    second_send_days_CD = 1;
                                                    $('#second_send_days_CD', '#addDetailsCD', '#castingDatesSettings').val(1);
                                                }
                                            /*
                                            Zbog direktnih unosa preko tastature END
                                        */

                                        $('#first_send_days_CD', '#addDetailsCD', '#castingDatesSettings').attr({
                                            "min" : second_send_days_CD + 2
                                        });
                                        $('#second_send_days_CD', '#addDetailsCD', '#castingDatesSettings').attr({
                                            "max" : first_send_days_CD - 2,        
                                            "min" : 1
                                        });
                                    };
                                    $('#addDetailsCD', '#castingDatesSettings').on('hidden.bs.modal', function() {
                                        setModalTitleForAddAppointmentDetailsCD('', '');
                                        setInitialValueForAddAppointmentDetailsCD('','','','','','','','','');
                                    });
                                    $('#first_send_enabled_CD', '#addDetailsCD', '#castingDatesSettings').on('click', function(){
                                        showHideSendDatesADCD('first_send_enabled_CD', 'first_send_days_CD');
                                    });
                                    $('#second_send_enabled_CD', '#addDetailsCD', '#castingDatesSettings').on('click', function(){
                                        showHideSendDatesADCD('second_send_enabled_CD', 'second_send_days_CD');
                                    });
                                    $('#first_send_days_CD', '#addDetailsCD', '#castingDatesSettings').on('change', function() {
                                        minMaxSendDatesADCD('first_send_days_CD');
                                    });
                                    $('#second_send_days_CD', '#addDetailsCD', '#castingDatesSettings').on('change', function() {
                                        minMaxSendDatesADCD('second_send_days_CD');
                                    });
                                    $('.question_address_CD', '#addDetailsCD', '#castingDatesSettings').on('click', function(){
                                        messageADCD('danger', '<h4>Naziv / adresa lokacije</h4>U slučaju da polje <strong>Naziv / adresa lokacije</strong> nije poznato - isto ostavite prazno!<br><small>U slučaju da se unese neka neispravna vrijednost u polje, kandidatima će ista biti ispisana na formi kojoj pristupaju putem Viber/SMS linka!</small>');
                                    });
                                    $('.question_address_link_CD', '#addDetailsCD', '#castingDatesSettings').on('click', function(){
                                        messageADCD('danger', '<h4>Link lokacije</h4>U slučaju da polje <strong>Link lokacije</strong> nije poznato ili nije moguće generisati link jer nije poznata lokacija - isto ostavite prazno!<br><small>U slučaju da se unese neki neispravan link u polje, kandidatima će biti dostupno dugme koje vodi na neispravan link na formi kojoj pristupaju putem Viber/SMS linka!</small>');
                                    });
                                    $('#addDetailsCDForm', '#addDetailsCD', '#castingDatesSettings').on('submit', function(event) {
                                        event.preventDefault();
                                        let dates_CD = $('#dates_CD', '#addDetailsCD', '#castingDatesSettings');
                                        let city_CD = $('#city_CD', '#addDetailsCD', '#castingDatesSettings');
                                        let first_send_enabled_CD = $('#first_send_enabled_CD', '#addDetailsCD', '#castingDatesSettings'); 
                                        let first_send_days_CD = $('#first_send_days_CD', '#addDetailsCD', '#castingDatesSettings'); 
                                        let second_send_enabled_CD = $('#second_send_enabled_CD', '#addDetailsCD', '#castingDatesSettings');
                                        let second_send_days_CD = $('#second_send_days_CD', '#addDetailsCD', '#castingDatesSettings');
                                        
                                        dates_CD.prop('disabled', false);
                                        city_CD.prop('disabled', false);
                                        first_send_enabled_CD.prop('disabled', false);
                                        first_send_days_CD.prop('disabled', false);
                                        second_send_enabled_CD.prop('disabled', false);
                                        second_send_days_CD.prop('disabled', false);

                                        $('#addDetailsCDForm', '#addDetailsCD', '#castingDatesSettings').unbind('submit').submit();
                                    });
                               /*
                                Add Appointment Details END
                            */
                        </script>
                        <!-- 
                            Add Appointment Questions START
                            -->
                                <script src="jobsoft_settings/components/FormFieldQuestion.js?time=<?php echo time(); ?>" type="module"></script>
                                <script src="/jobsoft_settings/js/functions_for/add-appointment-questions.js?time=<?php echo time(); ?>"></script>
                            <!--
                            Add Appointment Questions END
                        -->
                    <!-- 
                    Script END 
                -->
            <?php 
            
            unset($groupedData);

        } else {
            ?> 
                <div class="row">
                    <div class="col-xs-12">
                        <div class="alert alert-warning text-center" role="alert">
                            <h4><strong>Upozorenje</strong></h4>
                            <br>
                            Nema dodanih datuma castinga za ovaj nalog!
                        </div>
                    </div>
                </div>
            <?php
        }
    }
