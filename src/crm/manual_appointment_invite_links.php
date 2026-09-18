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
            $time_start = microtime(true);
            /* Functions START */
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
                function sendOnlyViber($phone, $text_viber, $button_text, $link_url){
                    $broj = str_replace("+","",$phone);
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
                    echo $err = curl_error($curl);
            
                    curl_close($curl);
            
                    $xmldata = json_decode($response);
                    // var_dump($xmldata);
                }
            
                function sendOnlySMS($phone, $text_sms){
                    $phone = str_replace("+","",$phone);
                    $curl = curl_init();
            
                    $params = array(
                        "scenarioKey" => "E351295F36C3677206F28380311E91A2",
                        "destinations" => array(
                            "to" => array(
                                "phoneNumber" => $phone,
                            )
                        ),
                        "sms" => array(
                            "text" => $text_sms,
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
                    echo $err = curl_error($curl);
            
                    curl_close($curl);
            
                    $xmldata = json_decode($response);
                    // var_dump($xmldata);
                }
            /* Functions END */

            echo "<h3>Candidates</h3><hr>";
            $send = $_REQUEST["send"];
            $enabled_send = array(0,1);
            if(in_array($send,$enabled_send)){
                $sql_query = '
                    SELECT 
                        kan.kandidat_id,
                        kan.kandidat_ime, 
                        kan.kandidat_prezime, 
                        kan.kandidat_mobitel,
                        (+38763021436) AS mobitel_moj, 
                        kan.kandidat_tf_status,
                        pca.pca_id, 
                        pca.pca_time,
                        pap.pap_id, 
                        pap.pap_date,  
                        pap.pap_city, 
                        pap.pap_location_name, 
                        pap.pap_google_maps_location,
                        ail.link_status,
                        (CASE 
                            WHEN ail.link_status = 1 THEN "Poslano" 
                            WHEN ail.link_status = 2 THEN "Otvoreno" 
                            WHEN ail.link_status = 3 THEN "Potvrdjeno" 
                            WHEN ail.link_status = 0 THEN "Arhivirano"
                            ELSE null
                        END) AS link_status_text,
                        ail.send_key, 
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
                        kan.kandidat_tf_status IN (16,18)
                    JOIN 
                        idk_appointment_invite_links ail
                    ON 
                        ail.interview_id = pca.pca_id
                        AND 
                        ail.candidate_id = pca.pca_kandidat_id
                        AND 
                        ail.counter_sent = 2
                    WHERE 
                        pap.pap_id = 250
                        AND 
                        ail.link_status = 1
                ';
                $query = $db->prepare("".$sql_query."");
                $query->execute();
                $number_of_rows = $query->rowCount();
                
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
                                <th>Send days before</th>
                                <th>Link status</th>
                                <th>Link status text</th>
                                <th>Key</th>
                            </tr>
                    ';
                    $row_counter = 0;
                    while($row = $query->fetch()){
                        $row_counter++;
                        $kandidat_id                            = $row["kandidat_id"]; 
                        $kandidat_ime                           = $row["kandidat_ime"]; 
                        $kandidat_prezime                       = $row["kandidat_prezime"]; 
                        $kandidat_mobitel                       = $row["kandidat_mobitel"];
                        $kandidat_tf_status                     = $row["kandidat_tf_status"];
                        $pca_id                                 = $row["pca_id"]; 
                        $pca_time                               = $row["pca_time"]; 
                        $pap_id                                 = $row["pap_id"]; 
                        $pap_date                               = $row["pap_date"]; 
                        $pap_city                               = $row["pap_city"]; 
                        $pap_location_name                      = $row["pap_location_name"]; 
                        $pap_google_maps_location               = $row["pap_google_maps_location"]; 
                        $link_status                            = $row["link_status"]; 
                        $link_status_text                       = $row["link_status_text"]; 
                        $send_key                               = $row["send_key"];
                        $sent_days_before                       = $row["sent_days_before"];

                        $link_url_potvrdjeni = $pap_google_maps_location;
                        $tekst_za_slanje_sms_potvrdjeni = "Poštovani, \n\nradi bolje pristupačnosti, promenili smo lokaciju razgovora za posao. \nNova lokacija je: ".$pap_location_name.".\n\nLINK google maps: ".$pap_google_maps_location." \n\nRadujemo se susretu sa Vama i želimo Vam sreću na razgovoru za posao!\n\nVaš Jobstep";
	                    $tekst_za_slanje_viber_potvrdjeni = "Poštovani, \n\nradi bolje pristupačnosti, promenili smo lokaciju razgovora za posao. \nNova lokacija je: ".$pap_location_name.".\n\nRadujemo se susretu sa Vama i želimo Vam sreću na razgovoru za posao!\n\nVaš Jobstep";
                        $button_text_potvrdjeni = "LINK Google Maps";
                        
                        $link_url_normalna_poruka = "https://crm.job-step.com/public_appointment_invite_link.php?page=confirmation&data=".$send_key;
                        // $link_url_normalna_poruka = "localhost/public_appointment_invite_link.php?page=confirmation&data=".$send_key;
                        $tekst_normalna_poruka_viber = "Poštovani, \n\nVaš razgovor za posao je za ".$sent_days_before." dana.\n\nVaš termin je ".date("d.m.Y", strtotime($pap_date))." u ".date("H:i", strtotime($pca_time))." sati, na lokaciji: ".$pap_location_name.". \n\nMolimo Vas da potvrdite Vaš termin klikom na link.\n\nVaš Jobstep.";
                        $button_normalna_poruka = "POTVRDI";

                        if ($send == 0){
                            echo '
                                <tr>
                                    <td>'.$row_counter.'</td>
                                    <td>'.$kandidat_id.'</td>
                                    <td>'.$kandidat_ime.' '.$kandidat_prezime.'</td>
                                    <td>'.$kandidat_mobitel.'</td>
                                    <td>'.$kandidat_tf_status.'</td>
                                    <td>'.$pap_id.'</td>
                                    <td>'.$pca_id.'</td>
                                    <td>'.$pap_date.' '.$pca_time.'</td>
                                    <td>'.$pap_city.'</td>
                                    <td>'.$pap_location_name.'</td>
                                    <td>'.$pap_google_maps_location.'</td>
                                    <td>'.$sent_days_before.'</td>
                                    <td>'.$link_status.'</td>
                                    <td>'.$link_status_text.'</td>
                                    <td>'.$send_key.'</td>
                                </tr>
                            ';
                        } else {

                            if ($link_status == 1){
                                echo '
                                    <tr>
                                        <td>'.$row_counter.'</td>
                                        <td>'.$kandidat_id.'</td>
                                        <td>'.$kandidat_ime.' '.$kandidat_prezime.'</td>
                                        <td>'.$kandidat_mobitel.'</td>
                                        <td>'.$kandidat_tf_status.'</td>
                                        <td>'.$pap_id.'</td>
                                        <td>'.$pca_id.'</td>
                                        <td>'.$pap_date.' '.$pca_time.'</td>
                                        <td>'.$pap_city.'</td>
                                        <td>'.$pap_location_name.'</td>
                                        <td>'.$pap_google_maps_location.'</td>
                                        <td>'.$sent_days_before.'</td>
                                        <td>'.$link_status.'</td>
                                        <td>'.$link_status_text.'</td>
                                        <td>'.$send_key.'</td>
                                    </tr>
                                ';
                                sendOnlyViber($kandidat_mobitel, $tekst_normalna_poruka_viber, $button_normalna_poruka, $link_url_normalna_poruka);
                            }
                            /*
                            if ($link_status != null){
                                if ($link_status == 3) {
                                    //Potvrdjeni

                                    sendOnlyViber($kandidat_mobitel, $tekst_za_slanje_viber_potvrdjeni, $button_text_potvrdjeni, $link_url_potvrdjeni);
                                    sendOnlySMS($kandidat_mobitel, $tekst_za_slanje_sms_potvrdjeni);

                                } else if (in_array($link_status, array(1,2))) {
                                    //Poslano i otvoreno

                                    sendOnlyViber($kandidat_mobitel, $tekst_normalna_poruka_viber, $button_normalna_poruka, $link_url_normalna_poruka);
                                    sendInviteLinkViaSMS($kandidat_mobitel, $pap_city, $pap_date, $pca_time, $pap_location_name, $send_key, $sent_days_before, 2);
                                } else {
                                    //Do nothing
                                }

                                echo '
                                    <tr>
                                        <td>'.$row_counter.'</td>
                                        <td>'.$kandidat_id.'</td>
                                        <td>'.$kandidat_ime.' '.$kandidat_prezime.'</td>
                                        <td>'.$kandidat_mobitel.'</td>
                                        <td>'.$kandidat_tf_status.'</td>
                                        <td>'.$pap_id.'</td>
                                        <td>'.$pca_id.'</td>
                                        <td>'.$pap_date.' '.$pca_time.'</td>
                                        <td>'.$pap_city.'</td>
                                        <td>'.$pap_location_name.'</td>
                                        <td>'.$pap_google_maps_location.'</td>
                                        <td>'.$sent_days_before.'</td>
                                        <td>'.$link_status.'</td>
                                        <td>'.$link_status_text.'</td>
                                        <td>'.$send_key.'</td>
                                    </tr>
                                ';
                                
                            } else {
                                $insert_result = insertAppointmentInviteLinkR($kandidat_id, $pca_id, $sent_days_before, 2);
                                if($insert_result != "Record insertion failed!" AND $insert_result != "Invalid arguments passed!"){
                                    $invite_link_key = getInviteLinkKeyR($insert_result);
                                    if($invite_link_key != "Invalid arguments passed!" AND $invite_link_key != "SQL query does not return results!"){
                                        echo '
                                            <tr>
                                                <td>'.$row_counter.'</td>
                                                <td>'.$kandidat_id.'</td>
                                                <td>'.$kandidat_ime.' '.$kandidat_prezime.'</td>
                                                <td>'.$kandidat_mobitel.'</td>
                                                <td>'.$kandidat_tf_status.'</td>
                                                <td>'.$pap_id.'</td>
                                                <td>'.$pca_id.'</td>
                                                <td>'.$pap_date.' '.$pca_time.'</td>
                                                <td>'.$pap_city.'</td>
                                                <td>'.$pap_location_name.'</td>
                                                <td>'.$pap_google_maps_location.'</td>
                                                <td>'.$sent_days_before.'</td>
                                                <td>Success</td>
                                                <td>Poslan novi link</td>
                                                <td>'.$invite_link_key.'</td>
                                            </tr>
                                        ';

                                        $link_nekreirani = "http://www.jobstep-app.com/public_appointment_invite_link.php?page=confirmation&data=".$invite_link_key;
                                        sendOnlyViber($kandidat_mobitel, $tekst_normalna_poruka_viber, $button_normalna_poruka, $link_nekreirani);
                                        sendInviteLinkViaSMS($kandidat_mobitel, $pap_city, $pap_date, $pca_time, $pap_location_name, $invite_link_key, $sent_days_before, 2);
                                        //sendInviteLinkViaSMS($kandidat_mobitel, $pap_city, $pap_date, $pca_time, $pap_location_name, $invite_link_key, $sent_days_before, 2);

                                    }else{
                                        echo '
                                            <tr>
                                                <td>'.$row_counter.'</td>
                                                <td>'.$kandidat_id.'</td>
                                                <td>'.$kandidat_ime.' '.$kandidat_prezime.'</td>
                                                <td>'.$kandidat_mobitel.'</td>
                                                <td>'.$kandidat_tf_status.'</td>
                                                <td>'.$pap_id.'</td>
                                                <td>'.$pca_id.'</td>
                                                <td>'.$pap_date.' '.$pca_time.'</td>
                                                <td>'.$pap_city.'</td>
                                                <td>'.$pap_location_name.'</td>
                                                <td>'.$pap_google_maps_location.'</td>
                                                <td>'.$sent_days_before.'</td>
                                                <td>Key Error</td>
                                                <td>'.$invite_link_key.'</td>
                                                <td>'.$send_key.'</td>
                                            </tr>
                                        ';
                                    }
                                }else{
                                    echo '
                                        <tr>
                                            <td>'.$row_counter.'</td>
                                            <td>'.$kandidat_id.'</td>
                                            <td>'.$kandidat_ime.' '.$kandidat_prezime.'</td>
                                            <td>'.$kandidat_mobitel.'</td>
                                            <td>'.$kandidat_tf_status.'</td>
                                            <td>'.$pap_id.'</td>
                                            <td>'.$pca_id.'</td>
                                            <td>'.$pap_date.' '.$pca_time.'</td>
                                            <td>'.$pap_city.'</td>
                                            <td>'.$pap_location_name.'</td>
                                            <td>'.$pap_google_maps_location.'</td>
                                            <td>'.$sent_days_before.'</td>
                                            <td>Insert Error</td>
                                            <td>'.$insert_result.'</td>
                                            <td>'.$send_key.'</td>
                                        </tr>
                                    ';
                                }
                            }
                            */
                        }
                    }
                    echo '
                        </table>
                    ';
                }else{
                    echo "SQL query nije pronašao rezultate!";
                }
            }else{
                echo "Nevažeći parametar 'send' unutar URL-a!";
            }

            $time_end = microtime(true);
            $time = $time_end - $time_start;

            echo "<hr><h3>Vrijeme izvršavanja skripte: ".$time."</h3>";

            unset($enabled_send); 
        ?> 
    </body>
</html>