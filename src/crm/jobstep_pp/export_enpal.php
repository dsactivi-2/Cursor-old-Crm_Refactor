<?php

include('includes/function.php');
include('includes/connect.php');

if(isset($_REQUEST["nalog_id"]))
    $nalog_id = $_REQUEST["nalog_id"];
if(isset($_REQUEST["bar_id"]))
    $bar_id 			= $_REQUEST["bar_id"];

Global $db;

$query_get_questions = $db->prepare(
    "SELECT pqu_id, pqu_question, pqu_has_text, pqu_has_rating, pqu_has_dropdown FROM idk_pp_questions WHERE pqu_nalog_id = $nalog_id AND 
    pqu_export_order IS NOT NULL AND
    pqu_id BETWEEN 541 AND 565
    ORDER BY pqu_export_order"
);
$query_get_questions->execute();
$row_questions = $query_get_questions->fetchAll();

$thead_question = "";
foreach($row_questions as $question){
    if($question['pqu_has_rating'] == 1){
        $thead_question .= '<th>'.$question['pqu_question'].'</th>';
    }elseif($question['pqu_has_dropdown'] == 1){
        $thead_question .= '<th>'.$question['pqu_question'].'</th>';
    }
    if($question['pqu_has_text'] == 1){
        $thead_question .= '<th>'.$question['pqu_question'].' Notes</th>';
    }
}
// var_dump($row_questions);

$broj_pitanja = count($row_questions); //Ono sto query prebroji
$sql_select = array();
for($i = 1; $i <= $broj_pitanja; $i++){
    if($row_questions[$i-1]['pqu_has_rating'] == 1){
        array_push($sql_select, 
            'MAX(
                    CASE WHEN queryRowsCandidate.pqu_id = '.$row_questions[$i-1]['pqu_id'].'  AND queryRowsCandidate.pqu_has_rating = 1
                        THEN queryRowsCandidate.pra_rating ELSE null END
                ) AS "rating_'.$row_questions[$i-1]['pqu_question'].'"'
            );
    }
    if($row_questions[$i-1]['pqu_has_dropdown'] == 1){
        array_push($sql_select, 
        'MAX(
                CASE WHEN queryRowsCandidate.pqu_id = '.$row_questions[$i-1]['pqu_id'].'  AND queryRowsCandidate.pqu_has_dropdown = 1
                    THEN queryRowsCandidate.pqo_value_text ELSE null END
            ) AS "dropdown_'.$row_questions[$i-1]['pqu_question'].'"'
        );
    }
    if($row_questions[$i-1]['pqu_has_text'] == 1){
        array_push($sql_select, 
        'MAX(
                CASE WHEN queryRowsCandidate.pqu_id = '.$row_questions[$i-1]['pqu_id'].'  AND queryRowsCandidate.pqu_has_text = 1
                    THEN queryRowsCandidate.pra_comment ELSE null END
            ) AS "comment_'.$row_questions[$i-1]['pqu_question'].'"'
        );
    }
    //if u odnosu na pitanja pa onda text drop itd
    //MAX(CASE WHEN queryRowsCandidate.pqu_id = 235 THEN queryRowsCandidate.pra_comment ELSE null END) AS "HeightComment",
}
$sql_select_implode = implode(",", $sql_select); //Idem preko niza jer se ne zelim muciti zarezom


if($bar_id == 3){
    $search_like = "- Intervju";
    $additional_condition = "";
    $order_query = " ORDER BY pap.pap_date, pca.pca_time";
    $theadhs_mail_phone = "";
}
if($bar_id == 4){
    $search_like = "- Ugovor";
    $additional_condition = "OR pro.project_name LIKE ('%Kandidati poceli sa radom') OR pro.project_name LIKE ('%Zavrseni kandidati')";
    $order_query = " ORDER BY pap.pap_date DESC, pca.pca_time DESC";
    $theadhs_mail_phone = "<th>Mail</th><th>Phone</th>";
}
$query_get_candidate_ids = $db -> prepare("
    SELECT kan.kandidat_id 
    FROM idk_kandidati kan
    JOIN idk_project_kandidati pk
    ON pk.pk_kandidatid = kan.kandidat_id
    JOIN idk_projects pro
    ON pro.project_id = pk.pk_projectid
    WHERE (pro.project_name LIKE ('%".$search_like."%') ".$additional_condition.") 
    AND pro.project_nalogid IN (".$nalog_id.")
    AND kan.kandidat_status != 3
");

$query_get_candidate_ids -> execute();

$candidate_ids = array();
while($row_get_candidate_ids = $query_get_candidate_ids->fetch()){
    array_push($candidate_ids, $row_get_candidate_ids['kandidat_id']);
}
$candidate_ids = implode(',',$candidate_ids);
// $candidate_ids = "137761";

$sql = 
    "SELECT kandidat_id, kandidat_ime, kandidat_prezime, CONCAT(DATE_FORMAT(pap.pap_date, '%e.%c.%Y'), ', ', DATE_FORMAT(pca.pca_time, '%H:%i'), ' - ', pap.pap_city) AS termin_grad,
            CASE 
                WHEN kandidat_drzavljanstvo LIKE 'Bosanskohercegovačko' THEN 'Bosnian'
                WHEN kandidat_drzavljanstvo LIKE 'Srpsko' THEN 'Serbian'
                WHEN kandidat_drzavljanstvo LIKE 'Hrvatsko' THEN 'Croatian'
                WHEN kandidat_drzavljanstvo LIKE 'Njemačko' THEN 'German'
                ELSE ''
            END AS drzavljanstvo_drzava,
            CONCAT(kandidat_adresa, ', ', kandidat_pbroj, ' ', kandidat_grad) as full_adresa, 
            kandidat_mobitel, kandidat_email, pca.pca_comment,
            CASE
                WHEN kandidat_drzavljanstvo_vrsta LIKE 'EU%' THEN 'EU'
                WHEN kandidat_drzavljanstvo_vrsta LIKE '%NON-EU%' THEN 'NON-EU'
                ELSE '' 
            END AS drzavljanstvo_vrsta,
            CASE 
                WHEN pca.pca_recommendation = 1 THEN 'Hire' 
                WHEN pca.pca_recommendation = 2 THEN 'Reject' 
                WHEN pca.pca_recommendation = 3 THEN 'Not Sure' 
                ELSE ' '
            END
            AS decision_recommendation,
            pca_reason_recommendation
     FROM idk_kandidati
     JOIN(
        SELECT sq_pca.pca_appointment_id, sq_pca.pca_time, sq_pca.pca_kandidat_id, sq_pca.pca_comment, sq_pca.pca_recommendation, sq_pca.pca_reason_recommendation
        FROM idk_pp_cand_appts sq_pca
        JOIN idk_pp_appointments sq_ppa
        ON sq_ppa.pap_id = sq_pca.pca_appointment_id
        WHERE sq_ppa.pap_nalog_id IN (".$nalog_id.") AND sq_pca.pca_id IN ( SELECT MAX(pca_id) AS max_id FROM idk_pp_cand_appts WHERE pca_kandidat_id IN (".$candidate_ids.") GROUP BY pca_kandidat_id)
        AND sq_pca.pca_kandidat_id IN (".$candidate_ids.")
     ) pca
     ON 
        kandidat_id = pca.pca_kandidat_id
     JOIN 
        idk_pp_appointments pap
     ON 
        pca.pca_appointment_id = pap.pap_id
     WHERE kandidat_id IN (".$candidate_ids.") ".$order_query."
     " 
;

// echo $sql;
$query_get_export = $db -> prepare($sql);
$query_get_export -> execute();
echo '
    <table>
        <tr>
            <th>System ID</th>
            <th>Termin</th>
            <th>First Name </th>
            <th>Last Name</th>
            '.$theadhs_mail_phone.'
            <th>Nationality</th>
            <th>Type of Nationality</th>
            <th>Address</th>
            <th>Interviewer</th>
            '.$thead_question.'
            <th>Interview Notes</th>
            <th>Decision Recommendation</th>
            <th>Main Reason for (Hiring Decision) / Final Verdict</th>
            <th>Final decision</th>
        </tr>
';


    $candidate_full_name = "";
    $candidate_address = "";
    $candidate_dob = "";
    $candidate_vocation = "";
    $candidate_city = "";
    $x = 1;
    
    while($row = $query_get_export -> fetch()){
        $kandidat_id                    = $row['kandidat_id'];
        $kandidat_ime                   = $row['kandidat_ime'];
        $kandidat_prezime               = $row['kandidat_prezime'];

        $drzavljanstvo_drzava           = $row['drzavljanstvo_drzava'];
        $drzavljanstvo_vrsta            = $row['drzavljanstvo_vrsta'];
        $full_adresa                    = $row['full_adresa'];
        $kandidat_mobitel               = $row['kandidat_mobitel'];
        $kandidat_email                 = $row['kandidat_email'];
        $termin_grad                    = $row['termin_grad'];
        $pca_comment                    = $row['pca_comment'];
        $decision_recommendation        = $row['decision_recommendation'];
        $pca_reason_recommendation      = $row['pca_reason_recommendation'];

        if($bar_id == 4){
            $tds_phone_mail = '<td>'.$kandidat_email.'</td><td>'.$kandidat_mobitel.'</td>';
        }else{
            $tds_phone_mail = '';
        }
        
        //DIO ZA ODGOVORE
           
        
            $sql_final = "
                SELECT    
                    queryRowsCandidate.kandidat_id AS candidate_id,
                    CONCAT(queryRowsCandidate.kandidat_ime, ' ', queryRowsCandidate.kandidat_prezime) AS candidate_first_last_name,
                    queryRowsCandidate.pca_avg_rating AS candidate_avg_rating, 
                    queryRowsCandidate.pca_comment AS candidate_general_comment,
                    pca_interviewer,
                    ".$sql_select_implode."
                FROM 
                    (
                        SELECT 
            
                            kan.kandidat_id,
                            kan.kandidat_ime,
                            kan.kandidat_prezime,
                            
                            pca.pca_avg_rating,
                            pca.pca_comment,
                            pca.pca_recommendation, 
                            pca.pca_reason_recommendation, 
                            pca.pca_interviewer, 
                            
                            pr.pra_rating, 
                            pr.pra_comment,
                            pr.pra_options_value,
                            
                            pq.pqu_id,
                            pq.pqu_question,
                        	pq.pqu_has_rating,
                        	pq.pqu_has_text,
                        	pq.pqu_has_dropdown,
                        	pqo.pqo_value_text
                            
                        FROM 
                            idk_kandidati kan
                        JOIN(
                            SELECT sq_pca.pca_avg_rating, sq_pca.pca_kandidat_id, sq_pca.pca_recommendation, sq_pca.pca_comment, sq_pca.pca_interviewer, sq_pca.pca_appointment_id, sq_pca.pca_reason_recommendation
                            FROM idk_pp_cand_appts sq_pca
                            JOIN idk_pp_appointments sq_ppa
                            ON sq_ppa.pap_id = sq_pca.pca_appointment_id
                            WHERE sq_ppa.pap_nalog_id IN (".$nalog_id.") AND sq_pca.pca_id IN ( SELECT MAX(pca_id) AS max_id FROM idk_pp_cand_appts WHERE pca_kandidat_id = ".$kandidat_id." GROUP BY pca_kandidat_id)
                            AND sq_pca.pca_kandidat_id = ".$kandidat_id."
                        ) pca
                        ON 
                            pca.pca_kandidat_id = kandidat_id
                        JOIN 
                            idk_pp_appointments pap
                        ON 
                            pca.pca_appointment_id = pap.pap_id
                        JOIN 
                            idk_pp_appointments_questions paq
                        ON 
                            pap.pap_id = paq.papq_appointment_id
                        JOIN 
                            idk_pp_questions pq 
                        ON 
                            paq.papq_question_id = pq.pqu_id
                        LEFT JOIN 
                            idk_pp_ratings pr
                        ON 
                            paq.papq_id  = pr.pra_appointment_question_id
                        LEFT JOIN 
                            idk_pp_question_options pqo 
                        ON 
                            pqo.pqo_question_id = pq.pqu_id AND pqo.pqo_value = pr.pra_options_value AND pq.pqu_has_dropdown = 1
                        WHERE
                            pr.pra_kandidat_id = $kandidat_id 
                            AND
                            pca.pca_kandidat_id = $kandidat_id 
                    ) 
                    AS 
                    queryRowsCandidate
                GROUP BY 
                queryRowsCandidate.kandidat_id
            ";
            
            $final_answers = $db->prepare($sql_final);
            $final_answers->execute();
            $rows_final = $final_answers->fetchAll();
            // var_dump($rows_final);
            // var_dump($sql_final);
            // exit();
            $answers_tds = "";
            if($rows_final[0] == 0){
                $answers_tds = '
                <td></td><td></td><td></td><td></td>
                <td></td><td></td><td></td><td></td>
                <td></td><td></td><td></td><td></td>
                <td></td><td></td><td></td><td></td>
                ';
            }else{
                for($j = 4; $j < count($rows_final[0])/2; $j++){
                    $answers_tds .= '<td style="height: 17px; vertical-align: top;" >'.$rows_final[0][$j].'</td>';
                    // var_dump($rows_final[0][$j]);
                }
            }
            // var_dump($answers_tds);
        //DIO ZA ODGOVORE



        echo '
            <tr>
                <td>'.$kandidat_id.'</td>
                <td>'.$termin_grad.'</td>
                <td>'.$kandidat_ime.'</td>
                <td>'.$kandidat_prezime.'</td>
                '.$tds_phone_mail.'
                <td>'.$drzavljanstvo_drzava.'</td>
                <td>'.$drzavljanstvo_vrsta.'</td>
                <td>'.$full_adresa.'</td>
                '.$answers_tds.'
                <td style="height: 17px; vertical-align: top;">'.$pca_comment.'</td>
                <td>'.$decision_recommendation.'</td>
                <td>'.$pca_reason_recommendation.'</td>
                <td></td>

            </tr>
        ';
        $x++;
    }

echo '</table>';