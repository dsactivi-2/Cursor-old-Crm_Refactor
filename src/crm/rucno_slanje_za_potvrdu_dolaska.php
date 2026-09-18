<!DOCTYPE html>
<html>
    <head>
    
        <style>
            table {
                font-family: arial, sans-serif;
                border-collapse: collapse;
                width: 100%;
            }

            td, th {
                border: 1px solid #dddddd;
                text-align: left;
                padding: 8px;
            }
        </style>

    </head>

    <body>
<?php 
    include("includes/connect.php");
    /*
        Ne smije se uraditi include "includes/functions.php"
    */

    $time_start = microtime(true);

    /* 
        Functions START
    */
        function getActiveProviderForSendingMessages($psm_group_type) {
            Global $db;
            $psm_group_type = intval($psm_group_type);
            $result = 0;

            $query = $db->prepare("
                SELECT 
                    psm_active_provider
                FROM 
                    idk_provider_for_sending_messages 
                WHERE 
                    psm_group_type = :psm_group_type
            ");
            $query->execute(array(
                ':psm_group_type' => $psm_group_type
            ));
            if($query->rowCount() == 1) {
                $row = $query->fetch();
                $result = intval($row["psm_active_provider"]);
            } 
            
            return $result;
        }

        function checkPhoneNumberForNTH($phone) {
            $phone = preg_replace('/\s+/', '', $phone);
            if (substr($phone, 0, 2) === "00") {
                $phone = substr($phone, 2);
            } else if (substr($phone, 0, 1) === "+") {
                $phone = substr($phone, 1);
            }
            return $phone;
        }

        function sendMessageViaNTH($params) {
            /*
                Format varijable $params prikazat će se u dokumentima na linkovima: 
                https://app.clickup.com/24391024/v/dc/q8bbg-28548
            */
    
            $curl = curl_init();
            curl_setopt_array($curl, 
                array(
                    CURLOPT_URL => 'https://msg.mobile-gw.com:9000/v1/omni-channel/message',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => $params,
                    CURLOPT_HTTPHEADER => array(
                        'Authorization: Basic am9ic3RlcDpSV0ZoIVN0TElNcTw=',
                        'Content-Type: application/json',
                        'Accept: application/json'
                    ),
                )
            );
            $response = curl_exec($curl);
            curl_close($curl);
            return $response;
        }

        function getInviteLinkKeyR($id){
            
            Global $db;

            $id = intval($id);

            if($id != 0){

                $query_info = $db->prepare("
                    SELECT 
                        send_key
                    FROM 
                        idk_appointment_invite_links
                    WHERE 
                        id = :id 
                ");

                $query_info->execute(array(
                    ":id" => $id
                ));

                $row_cnt = $query_info->rowCount();

                if($row_cnt == 1){
                    
                    $row_info = $query_info->fetch();

                    $result_send_key = $row_info["send_key"];

                    return $result_send_key;

                }else{

                    return "SQL query does not return results!";

                }

            }else{

                return "Invalid arguments passed!";

            }
        }

        function insertAppointmentInviteLinkR($candidate_id, $interview_id, $sent_days_before, $counter_sent){

            Global $db; 

            $candidate_id       = intval($candidate_id); 
            $interview_id       = intval($interview_id);
            $sent_days_before   = intval($sent_days_before); 
            $counter_sent       = intval($counter_sent);

            if($candidate_id != 0 AND $interview_id != 0 AND $sent_days_before != 0 AND $counter_sent != 0){

                $current_time = date("Y-m-d H:i:s"); 

                $send_key = md5($counter_sent.". ".$candidate_id." ".$interview_id." ".$current_time);

                $query_insert = $db->prepare("
                    INSERT INTO idk_appointment_invite_links 
                        (   
                            candidate_id, 
                            interview_id, 
                            link_status,
                            date_sent, 
                            send_key,
                            sent_days_before,
                            counter_sent
                        )
                    VALUES 
                        (
                            :candidate_id, 
                            :interview_id, 
                            :link_status, 
                            :date_sent, 
                            :send_key,
                            :sent_days_before,
                            :counter_sent
                        )
                ");
                $query_insert->execute(array(
                    ":candidate_id" => $candidate_id,
                    ":interview_id" => $interview_id, 
                    ":link_status" => 1,
                    ":date_sent" => $current_time, 
                    ":send_key" => $send_key,
                    ":sent_days_before" => $sent_days_before, 
                    ":counter_sent" => $counter_sent
                )); 

                $insertid = $db->lastInsertId();

                if($insertid){

                    return $insertid; 

                }else{

                    return "Record insertion failed!";

                }

            }else{

                return "Invalid arguments passed!";

            }

        }

        function sendInviteLinkViaSMS($phone, $location, $casting_date, $casting_time, $casting_location, $confirmation_token, $sent_days_before, $type){
            $text_sms = "";
    
            if($type == 1){
                if($casting_location != NULL){
                    $text_sms = "Poštovani, \n\nVaš razgovor za posao je za ".$sent_days_before." ".($sent_days_before == 1 ? "dan" : "dana").".\n\nVaš termin je ".date("d.m.Y", strtotime($casting_date))." u ".date("H:i", strtotime($casting_time))." sati, na lokaciji: ".$casting_location." , ".$location." \n\nMolimo Vas da potvrdite Vaš termin klikom na link: https://crm.job-step.com/public_appointment_invite_link.php?page=confirmation&data=".$confirmation_token."\n\nVaš Jobstep.";
                }else{
                    $text_sms = "Poštovani, \n\nVaš razgovor za posao je za ".$sent_days_before." ".($sent_days_before == 1 ? "dan" : "dana").".\n\nVaš termin je ".date("d.m.Y", strtotime($casting_date))." u ".date("H:i", strtotime($casting_time))." sati.\n\nMolimo Vas da potvrdite Vaš termin klikom na link: https://crm.job-step.com/public_appointment_invite_link.php?page=confirmation&data=".$confirmation_token."\n\nVaš Jobstep.";
                }
                
            }else if($type == 2){
                if($casting_location != NULL){
                    $text_sms = "Poštovani, \n\nVaš razgovor za posao je za ".$sent_days_before." ".($sent_days_before == 1 ? "dan" : "dana").".\n\nVaš termin je ".date("d.m.Y", strtotime($casting_date))." u ".date("H:i", strtotime($casting_time))." sati, na lokaciji: ".$casting_location." , ".$location." \n\nMolimo Vas da potvrdite Vaš termin klikom na link: https://crm.job-step.com/public_appointment_invite_link.php?page=confirmation&data=".$confirmation_token."\n\nVaš Jobstep.";
                }else{
                    $text_sms = "Poštovani, \n\nVaš razgovor za posao je za ".$sent_days_before." ".($sent_days_before == 1 ? "dan" : "dana").".\n\nVaš termin je ".date("d.m.Y", strtotime($casting_date))." u ".date("H:i", strtotime($casting_time))." sati.\n\nMolimo Vas da potvrdite Vaš termin klikom na link: https://crm.job-step.com/public_appointment_invite_link.php?page=confirmation&data=".$confirmation_token."\n\nVaš Jobstep.";
                }
            }else{
                return "The sending type is not a valid value";
            }
            
            $active_provider = getActiveProviderForSendingMessages(1);
            if ($active_provider == 1) {
                $params = array(
                    "scenarioKey" => "E351295F36C3677206F28380311E91A2",
                    "destinations" => array(
                        "to" => array(
                            "phoneNumber" => $phone,
                        )
                    ),
                    "sms" => array(
                        "text" =>$text_sms,
                    )
                );
                $data = json_encode($params);
        
                $curl = curl_init();
        
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://ej8w3r.api.infobip.com/omni/1/advanced",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $data,
                    CURLOPT_HTTPHEADER => array(
                        "accept: application/json",
                        "authorization: Basic ZWJlbmRlcjE6RUJlbmRlcjY0MzI=",
                        "content-type: application/json"
                    ),
                ));
                $response = curl_exec($curl);
                $err = curl_error($curl);
        
                curl_close($curl);
        
                if ($err) {
                return "cURL Error #:" . $err;
                } else {
                return $response;
                }
            } else {
                $phoneNumber = checkPhoneNumberForNTH($phone);
                $params = array(
                    "channels" => array(
                        "SMS"
                    ),
                    "destinations" => array(
                        array(
                            "phoneNumber" => $phoneNumber
                        )
                    ),
                    "sms" => array(
                        "sender" => "Jobstep Int",
                        "text" => $text_sms
                    )
                );
                $params_encode = json_encode($params);
                $response = sendMessageViaNTH($params_encode);
                return $response;
            }
        }

        function sendInviteLinkViaViber($phone, $location, $casting_date, $casting_time, $casting_location, $confirmation_token, $sent_days_before, $type){
            $text_viber = "";
            $button_text = "POTVRDI"; 
            $link_url = "https://crm.job-step.com/public_appointment_invite_link.php?page=confirmation&data=".$confirmation_token;
            if($type == 1){
                if($casting_location != NULL){
                    $text_viber = "Poštovani, \n\nVaš razgovor za posao je za ".$sent_days_before." ".($sent_days_before == 1 ? "dan" : "dana").".\n\nVaš termin je ".date("d.m.Y", strtotime($casting_date))." u ".date("H:i", strtotime($casting_time))." sati, na lokaciji: ".$casting_location." , ".$location." \n\nMolimo Vas da potvrdite Vaš termin klikom na link.\n\nVaš Jobstep.";
                }else{
                    $text_viber = "Poštovani, \n\nVaš razgovor za posao je za ".$sent_days_before." ".($sent_days_before == 1 ? "dan" : "dana").".\n\nVaš termin je ".date("d.m.Y", strtotime($casting_date))." u ".date("H:i", strtotime($casting_time))." sati.\n\nMolimo Vas da potvrdite Vaš termin klikom na link.\n\nVaš Jobstep.";
                }
                
            }else if($type == 2){
                if($casting_location != NULL){
                    $text_viber = "Poštovani, \n\nVaš razgovor za posao je za ".$sent_days_before." ".($sent_days_before == 1 ? "dan" : "dana").".\n\nVaš termin je ".date("d.m.Y", strtotime($casting_date))." u ".date("H:i", strtotime($casting_time))." sati, na lokaciji: ".$casting_location." , ".$location." \n\nMolimo Vas da potvrdite Vaš termin klikom na link.\n\nVaš Jobstep.";
                }else{
                    $text_viber = "Poštovani, \n\nVaš razgovor za posao je za ".$sent_days_before." ".($sent_days_before == 1 ? "dan" : "dana").".\n\nVaš termin je ".date("d.m.Y", strtotime($casting_date))." u ".date("H:i", strtotime($casting_time))." sati.\n\nMolimo Vas da potvrdite Vaš termin klikom na link.\n\nVaš Jobstep.";
                }
            }else{
                return "The sending type is not a valid value";
            }
            
            $broj = str_replace("+","",$phone);

            $active_provider = getActiveProviderForSendingMessages(1);
            if ($active_provider == 1) {
                $curl = curl_init();
        
                $params = array(
                    
                    "scenarioKey" => "C5BA6D5354E4296DD0BB9ACAC382C498",
                    "destinations" => array(
                        "to" => array(
                            "phoneNumber" => $broj,
                            )
                    ),
                    "viber" => array(
                        "text" => $text_viber,
                        // "imageURL" => $imageURL,
                        "buttonText" => $button_text,
                        "buttonURL" => $link_url,
                        "isPromotional" => "false"
                    )
                );
                $data = json_encode($params);
        
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://ej8w3r.api.infobip.com/omni/1/advanced",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $data,
                    CURLOPT_HTTPHEADER => array(
                        "accept: application/json",
                        "authorization: Basic ZWJlbmRlcjE6RUJlbmRlcjY0MzI=",
                        "content-type: application/json"
                    ),
                ));

                $response = curl_exec($curl);
                $err = curl_error($curl);
        
                curl_close($curl);
        
                if ($err) {
                return "cURL Error #:" . $err;
                } else {
                return $response;
                }
            } else {
                $phoneNumber = checkPhoneNumberForNTH($broj);
                $params = array(
                    "channels" => array(
                        "VIBER"
                    ),
                    "destinations" => array(
                        array(
                            "phoneNumber" => $phoneNumber
                        )
                    ),
                    "viber" => array(
                        "sender" => "Jobstep Int",
                        "buttonCaption" => $button_text,
                        "buttonAction" => $link_url,
                        "text" => $text_viber,
                        "ttl" => 14440,
                        "label" => "promotion"
                    )
                );
                $params_encode = json_encode($params);
                $response = sendMessageViaNTH($params_encode);
                return $response;
            }
        }

        function getIntervalForCandidateGroupsArrayR($time_hour) {
            $result = array(
                "time_start" => "",
                "time_end" => ""
            );

            switch($time_hour) {
                case "09":
                    $result["time_start"]   = "00:00:00"; 
                    $result["time_end"]     = "08:59:59";
                break; 
                case "10":
                    $result["time_start"]   = "09:00:00"; 
                    $result["time_end"]     = "09:59:59";
                break;
                case "11":
                    $result["time_start"]   = "10:00:00"; 
                    $result["time_end"]     = "10:59:59";
                break; 
                case "12":
                    $result["time_start"]   = "11:00:00"; 
                    $result["time_end"]     = "11:59:59";
                break; 
                case "13":
                    $result["time_start"]   = "12:00:00"; 
                    $result["time_end"]     = "12:59:59";
                break;
                case "14":
                    $result["time_start"]   = "13:00:00"; 
                    $result["time_end"]     = "13:59:59";
                break; 
                case "15":
                    $result["time_start"]   = "14:00:00"; 
                    $result["time_end"]     = "14:59:59";
                break; 
                case "16":
                    $result["time_start"]   = "15:00:00"; 
                    $result["time_end"]     = "15:59:59";
                break;
                case "17":
                    $result["time_start"]   = "16:00:00"; 
                    $result["time_end"]     = "16:59:59";
                break; 
                case "18":
                    $result["time_start"]   = "17:00:00"; 
                    $result["time_end"]     = "17:59:59";
                break; 
                case "19":
                    $result["time_start"]   = "18:00:00"; 
                    $result["time_end"]     = "23:59:59";
                break;
            }

            return $result;
        }
    /* 
        Functions END
    */
    echo "<small> Prilikom uploada na master ukloniti komenar '//type = getenv('type');'</small> <br>";
    echo "<h3>Candidates</h3><hr>";
    /* 
        Ovaj cron file se pokreće u definisano vrijeme i radi naredno: 
            - Vrši pregled tabele idk_pp_appointments i provjerava da li je neki termin za odredjeni broj dana
            - Ako je termin recimo za 10 dana - onda se kupe svi kandidati iz tabele idk_pp_cand_appts i vrši se slanje linka kandidati preko SMS poruke
            - Kandidat na linku vrši potvrđivanje dolaska na termin
        
        Generalno - cron služi za slanje SMS poruka kandidatima koji za 10 dana recimo imaju termin. U SMS poruci se nalazi link na koji pristupaju i potvrdjuju dolazak na termin. To sve je popraćeno u sistemu. 
    */

    //$type               = getenv("type");
    $type               = $_REQUEST["type"];
    $time_hour          = $_REQUEST["time_hour"];
    $appointment_id     = $_REQUEST["appointment_id"];
    $send_days_beffore  = $_REQUEST["send_days_beffore"]; 
    $enabled_types      = array(1,2);
    /* 
        $type 
            1 - prvo slanje prije odredjenog intervjua/termina 
            2 - drugo slanje prije odredjenog intervjua/termina 
        $enabled_types 
            Omogucene vrijednosti koje moraju biti definisane unutar skripte
    */

    if(in_array($type,$enabled_types)){

        $sql_query = "";
        // $time_hour = date("H");

        $interval_candidate_groups = getIntervalForCandidateGroupsArrayR($time_hour);

        switch($type){

            case 1:

                $sql_query = "
                    SELECT
                        kan.kandidat_id,
                        kan.kandidat_ime, 
                        kan.kandidat_prezime, 
                        kan.kandidat_mobitel,
                        kan.kandidat_tf_status, 
                        pca.pca_id, 
                        pca.pca_time, 
                        pap.pap_id, 
                        pap.pap_date, 
                        pap.pap_nalog_id, 
                        pap.pap_city, 
                        pap.pap_location_name, 
                        pap.pap_google_maps_location, 
                        pap.pap_first_sending_number_days, 
                        pap.pap_second_sending_number_days, 
                        pap.pap_first_send_enabled, 
                        pap.pap_second_send_enabled, 
                        pap.pap_group_id, 
                        DATEDIFF(pap.pap_date, CURRENT_DATE()) AS sent_days_before, 
                        ail.id AS generated_invite_link
                    FROM 
                        idk_pp_appointments pap 
                    INNER JOIN 
                        idk_pp_cand_appts pca
                    ON 
                        pap.pap_id = pca.pca_appointment_id
                        AND 
                        pca.pca_status = 1 
                    INNER JOIN 
                        idk_kandidati kan
                    ON 
                        pca.pca_kandidat_id = kan.kandidat_id 
                        AND 
                        kan.kandidat_status_prijave = 3
                        AND 
                        kan.kandidat_tf_status = 16
                    LEFT JOIN 
                        idk_appointment_invite_links ail
                    ON 
                        pca.pca_id = ail.interview_id 
                    WHERE 
                        (
                            CAST((CONCAT(pap.pap_date, ' ',pca.pca_time)) AS DATETIME) >= CAST((CONCAT(pap.pap_date, ' ', '".$interval_candidate_groups["time_start"]."')) AS DATETIME)
                            AND 
                            CAST((CONCAT(pap.pap_date, ' ',pca.pca_time)) AS DATETIME) <= CAST((CONCAT(pap.pap_date, ' ', '".$interval_candidate_groups["time_end"]."')) AS DATETIME)
                        )
                        AND
                        DATEDIFF(pap.pap_date, CURRENT_DATE()) = :send_days_beffore
                        AND 
	                    pap.pap_id = :appointment_id
                        AND 
                        pap.pap_first_send_enabled = 1
                        AND 
                        pap.pap_first_sending_number_days > 0
                        AND 
                        ail.id IS NULL
                    ORDER BY 
                        pap.pap_id ASC
                ";

            break;

            case 2:

                $sql_query = "
                    SELECT
                        kan.kandidat_id,
                        kan.kandidat_ime, 
                        kan.kandidat_prezime, 
                        kan.kandidat_mobitel,
                        kan.kandidat_tf_status,
                        pca.pca_id, 
                        pca.pca_time, 
                        pap.pap_id, 
                        pap.pap_date, 
                        pap.pap_nalog_id, 
                        pap.pap_city, 
                        pap.pap_location_name, 
                        pap.pap_google_maps_location, 
                        pap.pap_first_sending_number_days, 
                        pap.pap_second_sending_number_days, 
                        pap.pap_first_send_enabled, 
                        pap.pap_second_send_enabled, 
                        pap.pap_group_id,
                        DATEDIFF(pap.pap_date, CURRENT_DATE()) AS sent_days_before
                    FROM 
                        idk_pp_appointments pap 
                    INNER JOIN 
                        idk_pp_cand_appts pca
                    ON 
                        pap.pap_id = pca.pca_appointment_id
                        AND 
                        pca.pca_status = 1 
                    INNER JOIN 
                        idk_kandidati kan
                    ON 
                        pca.pca_kandidat_id = kan.kandidat_id 
                        AND 
                        kan.kandidat_status_prijave = 3
                        AND 
                        kan.kandidat_tf_status = 16
                    LEFT JOIN 
                        idk_appointment_invite_links ail
                    ON 
                        pca.pca_id = ail.interview_id 
                    WHERE 
                        (
                            CAST((CONCAT(pap.pap_date, ' ',pca.pca_time)) AS DATETIME) >= CAST((CONCAT(pap.pap_date, ' ', '".$interval_candidate_groups["time_start"]."')) AS DATETIME)
                            AND 
                            CAST((CONCAT(pap.pap_date, ' ',pca.pca_time)) AS DATETIME) <= CAST((CONCAT(pap.pap_date, ' ', '".$interval_candidate_groups["time_end"]."')) AS DATETIME)
                        )
                        AND
                        DATEDIFF(pap.pap_date, CURRENT_DATE()) = :send_days_beffore
                        AND 
	                    pap.pap_id = :appointment_id
                        AND 
                        pap.pap_second_send_enabled = 1
                        AND 
                        pap.pap_second_sending_number_days > 0
                        AND 
                        ail.id IS NULL
                    ORDER BY 
                        pap.pap_id ASC
                ";

            break;

        }

        if($sql_query != ""){
            
                $query_1 = $db->prepare("".$sql_query."");
                $query_1->execute(array(
                    ':appointment_id' => $appointment_id, 
                    ':send_days_beffore' => $send_days_beffore
                ));
                $number_of_rows = $query_1->rowCount();
                
                if($number_of_rows != 0){

                    echo '
                        <table>
                            <tr>
                                <th>#</th>
                                <th>Can ID</th>
                                <th>Kandidat</th>
                                <th>Telefon</th>
                                <th>TF Status</th>
                                <th>Appointment ID</th>
                                <th>Interview ID</th>
                                <th>Datum Vrijeme</th>
                                <th>Grad</th>
                                <th>Lokacija</th>
                                <th>Google Maps</th>
                                <th>Poslano prije</th>
                                <th>Insert ID</th>
                                <th>Key</th>
                            </tr>
                    ';

                    $row_counter = 0;
                    $insert_results_exp = array();  
                    $insert_results_imp = "";

                    while($row_1 = $query_1->fetch()){

                        $row_counter++;

                        $candidate_id                           = $row_1["kandidat_id"]; 
                        $candidate_first_name                   = $row_1["kandidat_ime"]; 
                        $candidate_last_name                    = $row_1["kandidat_prezime"]; 
                        $candidate_phone                        = $row_1["kandidat_mobitel"];
                        $candidate_tf                           = $row_1["kandidat_tf_status"];
                        $pca_id                                 = $row_1["pca_id"]; 
                        $pca_time                               = $row_1["pca_time"]; 
                        $pap_id                                 = $row_1["pap_id"]; 
                        $pap_date                               = $row_1["pap_date"]; 
                        $pap_nalog_id                           = $row_1["pap_nalog_id"]; 
                        $pap_city                               = $row_1["pap_city"]; 
                        $pap_location_name                      = $row_1["pap_location_name"]; 
                        $pap_google_maps_location               = $row_1["pap_google_maps_location"]; 
                        $pap_first_sending_number_days          = $row_1["pap_first_sending_number_days"]; 
                        $pap_second_sending_number_days         = $row_1["pap_second_sending_number_days"]; 
                        $pap_first_send_enabled                 = $row_1["pap_first_send_enabled"]; 
                        $pap_second_send_enabled                = $row_1["pap_second_send_enabled"]; 
                        $pap_group_id                           = $row_1["pap_group_id"]; 
                        $sent_days_before                       = $row_1["sent_days_before"]; 

                        /*
                        echo '
                            <tr>
                                <td>'.$row_counter.'</td>
                                <td>'.$candidate_id.'</td>
                                <td>'.$candidate_first_name.' '.$candidate_last_name.'</td>
                                <td>'.$candidate_phone.'</td>
                                <td>'.$candidate_tf.'</td>
                                <td>'.$pap_id.'</td>
                                <td>'.$pca_id.'</td>
                                <td>'.$pap_date.' '.$pca_time.'</td>
                                <td>'.$pap_city.'</td>
                                <td>'.$pap_location_name.'</td>
                                <td>'.$pap_google_maps_location.'</td>
                                <td>'.$sent_days_before.'</td>
                                <td>NN</td>
                                <td>NN</td>
                            </tr>
                        ';
                        */

                        //continue;

                        /*
                            Insert appointment invite link START             
                        */
                            $insert_result = insertAppointmentInviteLinkR($candidate_id, $pca_id, $sent_days_before, $type); 

                            if($insert_result != "Record insertion failed!" AND $insert_result != "Invalid arguments passed!"){

                                array_push($insert_results_exp, $insert_result);
                                
                                $invite_link_key = getInviteLinkKeyR($insert_result); 

                                if($invite_link_key != "Invalid arguments passed!" AND $invite_link_key != "SQL query does not return results!"){

                                    echo '
                                        <tr>
                                            <td>'.$row_counter.'</td>
                                            <td>'.$candidate_id.'</td>
                                            <td>'.$candidate_first_name.' '.$candidate_last_name.'</td>
                                            <td>'.$candidate_phone.'</td>
                                            <td>'.$candidate_tf.'</td>
                                            <td>'.$pap_id.'</td>
                                            <td>'.$pca_id.'</td>
                                            <td>'.$pap_date.' '.$pca_time.'</td>
                                            <td>'.$pap_city.'</td>
                                            <td>'.$pap_location_name.'</td>
                                            <td>'.$pap_google_maps_location.'</td>
                                            <td>'.$sent_days_before.'</td>
                                            <td>Success</td>
                                            <td>'.$invite_link_key.'</td>
                                        </tr>
                                    ';

                                    /* 
                                        SMS message START
                                        */
                                            sendInviteLinkViaSMS($candidate_phone, $pap_city, $pap_date, $pca_time, $pap_location_name, $invite_link_key, $sent_days_before, $type);
                                        /* 
                                        SMS message END
                                    */

                                    /*
                                        Viber message START
                                        */
                                            sendInviteLinkViaViber($candidate_phone, $pap_city, $pap_date, $pca_time, $pap_location_name, $invite_link_key, $sent_days_before, $type);
                                        /*
                                        Viber message END    
                                    */

                                }else{

                                    echo '
                                        <tr>
                                            <td>'.$row_counter.'</td>
                                            <td>'.$candidate_id.'</td>
                                            <td>'.$candidate_first_name.' '.$candidate_last_name.'</td>
                                            <td>'.$candidate_phone.'</td>
                                            <td>'.$candidate_tf.'</td>
                                            <td>'.$pap_id.'</td>
                                            <td>'.$pca_id.'</td>
                                            <td>'.$pap_date.' '.$pca_time.'</td>
                                            <td>'.$pap_city.'</td>
                                            <td>'.$pap_location_name.'</td>
                                            <td>'.$pap_google_maps_location.'</td>
                                            <td>'.$sent_days_before.'</td>
                                            <td>Key Error</td>
                                            <td>'.$invite_link_key.'</td>
                                        </tr>
                                    ';

                                    $log_desc_3 = "CRON APPOINTMENT INVITE LINKS: Za kandidata ID = [".$candidate_id."] i unos ID = [".$insert_result."] desila se greška prilikom čitanja ključa za slanje. PORUKA: ".$invite_link_key."";

                                    $log_insert_3 = $db->prepare("
                                        INSERT INTO idk_logs
                                            (
                                                log_employeeid, 
                                                log_desc, 
                                                log_date
                                            )
                                        VALUES
                                            (
                                                :log_employeeid, 
                                                :log_desc, 
                                                :log_date
                                            )
                                    ");

                                    $log_insert_3->execute(array(
                                        ":log_employeeid" => 0, 
                                        ":log_desc" => $log_desc_3, 
                                        ":log_date" => date("Y-m-d H:i:s")
                                    ));

                                }

                            }else{

                                echo '
                                    <tr>
                                        <td>'.$row_counter.'</td>
                                        <td>'.$candidate_id.'</td>
                                        <td>'.$candidate_first_name.' '.$candidate_last_name.'</td>
                                        <td>'.$candidate_phone.'</td>
                                        <td>'.$candidate_tf.'</td>
                                        <td>'.$pap_id.'</td>
                                        <td>'.$pca_id.'</td>
                                        <td>'.$pap_date.' '.$pca_time.'</td>
                                        <td>'.$pap_city.'</td>
                                        <td>'.$pap_location_name.'</td>
                                        <td>'.$pap_google_maps_location.'</td>
                                        <td>'.$sent_days_before.'</td>
                                        <td>Insert Error</td>
                                        <td>'.$insert_result.'</td>
                                    </tr>
                                ';

                                $log_desc_2 = "CRON APPOINTMENT INVITE LINKS: Za kandidata ID = [".$candidate_id."] desila se greška kod unosa. PORUKA: ".$insert_result."";

                                $log_insert_2 = $db->prepare("
                                    INSERT INTO idk_logs
                                        (
                                            log_employeeid, 
                                            log_desc, 
                                            log_date
                                        )
                                    VALUES
                                        (
                                            :log_employeeid, 
                                            :log_desc, 
                                            :log_date
                                        )
                                ");

                                $log_insert_2->execute(array(
                                    ":log_employeeid" => 0, 
                                    ":log_desc" => $log_desc_2, 
                                    ":log_date" => date("Y-m-d H:i:s")
                                ));

                            }
                        /*
                            Insert appointment invite link END             
                        */

                    }

                    echo '
                        </table>
                    ';

                    $insert_results_imp = implode(",", $insert_results_exp);
                    $log_desc_1 = "CRON APPOINTMENT INVITE LINKS: Kreirani invite linkovi sa IDs = [".$insert_results_imp."]. Interval slanja = [".$interval_candidate_groups["time_start"]." - ".$interval_candidate_groups["time_end"]."].";

                    $log_insert_1 = $db->prepare("
                        INSERT INTO idk_logs
                            (
                                log_employeeid, 
                                log_desc, 
                                log_date
                            )
                        VALUES
                            (
                                :log_employeeid, 
                                :log_desc, 
                                :log_date
                            )
                    ");

                    $log_insert_1->execute(array(
                        ":log_employeeid" => 0, 
                        ":log_desc" => $log_desc_1, 
                        ":log_date" => date("Y-m-d H:i:s")
                    ));

                }else{

                    echo "SQL query does not return results!";

                }
            
        }else{

            echo "Invalid SQL query!";

        }

    }else{

        echo "Invalid data type!";

    }

    $time_end = microtime(true);
	$time = $time_end - $time_start;

	echo "<hr><h3>Vrijeme izvršavanja skripte: ".$time."</h3>";

    unset($insert_results_exp);
    unset($enabled_types); 
    unset($interval_candidate_groups);
    
?>
    </body>
</html>