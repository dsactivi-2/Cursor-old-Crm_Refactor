<?php

include('includes/function.php');
include('includes/connect.php');

if(isset($_REQUEST["nalog_id"]))
    $nalog_id = $_REQUEST["nalog_id"];
// if(isset($_REQUEST["bar_id"]))
//     $bar_id 			= $_REQUEST["bar_id"];
$bar_id = 3;

Global $db;

//GET QUESTION IDS & Questions FOR thead- START
    $get_nalog_questions = $db->prepare("SELECT pqu_id, pqu_question FROM idk_pp_questions WHERE pqu_nalog_id = $nalog_id ORDER BY pqu_export_order");
    $get_nalog_questions->execute();
    $row_question_ids = $get_nalog_questions->fetchAll();
    $array_question_ids = array();
    $array_questions = array();
    $thead_question = "";
    // var_dump($row_question_ids);

    foreach($row_question_ids as $question){
        
        array_push($array_question_ids, $question[0]);
        array_push($array_questions, $question[1]);

        $thead_question .= '<th colspan=2>'.$question['pqu_question'].'</th>';
        // $thead_question .= '<th> Notes - '.$question['pqu_question'].'</th>';
    }
    $imp_questions = implode(",", $array_question_ids);
    
//GET QUESTION IDS - END


$broj_pitanja = count($row_question_ids); //Ono sto query prebroji

$sql_select = array();
for($i = 1; $i <= $broj_pitanja; $i++){
        $brojac = $i-1;
        array_push($sql_select, 
            'MAX(
                    CASE WHEN queryRowsCandidate.pqu_id = '.$row_question_ids[$i-1]['pqu_id'].'  AND queryRowsCandidate.pqu_has_rating = 1
                        THEN queryRowsCandidate.pra_rating ELSE null END
                ) AS rating'.$brojac
            );
   
        array_push($sql_select, 
        'MAX(
                CASE WHEN queryRowsCandidate.pqu_id = '.$row_question_ids[$i-1]['pqu_id'].'  AND queryRowsCandidate.pqu_has_text = 1
                    THEN queryRowsCandidate.pra_comment ELSE null END
            ) AS comment'.$brojac
        );
    
    //if u odnosu na pitanja pa onda text drop itd
    //MAX(CASE WHEN queryRowsCandidate.pqu_id = 235 THEN queryRowsCandidate.pra_comment ELSE null END) AS "HeightComment",
}

$sql_select_implode = implode(",", $sql_select); //Idem preko niza jer se ne zelim muciti zarezom

if($bar_id == 3){
    $search_like = "- Intervju";
    $additional_condition = "";
    $order_query = " ORDER BY pap.pap_date, pca.pca_time";
}
if($bar_id == 4){
    $search_like = "- Ugovor";
    $additional_condition = "OR pro.project_name LIKE ('%Kandidati poceli sa radom')";
    $order_query = " ORDER BY pap.pap_date DESC, pca.pca_time DESC";
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

// $candidate_ids = "133771";

$sql = 
    "SELECT kandidat_id, kandidat_ime, kandidat_prezime, pca.pca_comment, pca.pca_avg_rating
     FROM idk_kandidati
     JOIN(
        SELECT sq_pca.pca_appointment_id, sq_pca.pca_time, sq_pca.pca_kandidat_id, sq_pca.pca_comment, sq_pca.pca_avg_rating
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
            <th>First Name </th>
            <th>Last Name</th>
            <th>General Comment</th>
            <th>Average Rating</th>

            '.$thead_question.'
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
        $pca_comment                    = $row['pca_comment'];
        $pca_avg_rating                 = $row['pca_avg_rating'];
        
        //DIO ZA ODGOVORE
           
        
            $sql_final = "
                SELECT    
                    
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
                            SELECT sq_pca.pca_avg_rating, sq_pca.pca_kandidat_id, sq_pca.pca_recommendation, sq_pca.pca_comment, sq_pca.pca_interviewer, sq_pca.pca_appointment_id
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
                <td></td>
                ';
            }else{
                for($j = 0; $j < count($rows_final[0])/2; $j++){
                    if(($j+1)%2 == 0){
                        $valign = "vertical-align: top;";
                        $cell_width = "";
                    }else{
                        $valign = "";
                        $cell_width = " width: 30px;";
                    }
                    $answers_tds .= '<td style="height: 17px; '.$valign.$cell_width.' ">'.$rows_final[0][$j].'</td>';
                    // var_dump($rows_final[0][$j]);
                }
            }
            // var_dump($answers_tds);
        //DIO ZA ODGOVORE


        echo '
            <tr>
                <td>'.$kandidat_ime.'</td>
                <td>'.$kandidat_prezime.'</td>

                <td style="vertical-align: top;">'.$pca_comment.'</td>
                <td>'.$pca_avg_rating.'</td>
                '.$answers_tds.'

            </tr>
        ';
        $x++;
    }

echo '</table>';