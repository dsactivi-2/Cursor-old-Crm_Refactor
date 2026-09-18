<?php
ini_set('display_errors', 1);
// ini_set('error_log', 'error_log');
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include("includes/functions.php");
include_once($_SERVER["DOCUMENT_ROOT"] . '/jobstep_pp/includes/classes/candidatesProjection.php');
Global $db;

$log_cron_executed = $db->prepare("
    INSERT INTO idk_logs
        (log_employeeid, log_desc, log_date, log_type)
    VALUES
        (0, 'Pokrenut cron_projekcije.php', now(), 0)
");

$log_cron_executed -> execute();
function getAllCurrentProjections(){
    Global $db;
    
    $query_get_all_current_projections = $db -> prepare('SELECT * FROM idk_kandidat_projekcije');
    $query_get_all_current_projections -> execute();

    return $query_get_all_current_projections -> fetchAll();

}

function findProjectionByCandidateId($candidate_id, $search_projection){
    foreach($search_projection as $projection){
        if($projection['kandidat_id'] == $candidate_id){
            return $projection;
        }
    }
    return NULL;
}

function markCandidateForUpdate($old_projection, $new_projection){
    $date_diff = 0;
    $projected_at = 0;
    $candidate_id = 0;
    if(is_null($old_projection)){
        $candidate_id = $new_projection['candidate_id'];
        $date_diff = 0;
        $projected_at = $new_projection['candidate_assessment'];

        return array(
            'candidate_id' => $candidate_id,
            'date_diff' => $date_diff,
            'projected_at' => $projected_at
        );
    }
    else if($old_projection['proracunati_pocetak_rada'] != $new_projection['candidate_assessment']){
        $candidate_id = $new_projection['candidate_id'];
        $date_diff = floor((strtotime($new_projection['candidate_assessment']) - strtotime($old_projection['proracunati_pocetak_rada']))/(60*60*24));
        $projected_at = $new_projection['candidate_assessment'];
        return array(
            
            'candidate_id' => $candidate_id,
            'date_diff' => $date_diff,
            'projected_at' => $projected_at
        );
    }
    return NULL;
}

$candidate_projections_to_update = array();
$current_projections = getAllCurrentProjections();
$durationPerStatus = new durationPerStatus();

$candidatesProjection   = new candidatesProjection($durationPerStatus);
$new_candidate_projections  = $candidatesProjection -> getCandidateProjectionsForFinances();

$marked_candidates = array();

$insert_statement_values = array();
$insert_statements = array();
$i = 0;

foreach($new_candidate_projections as $new_projection){
    
    array_push($insert_statement_values, "(".$new_projection['candidate_id'].", '".$new_projection['candidate_assessment']."') "); 
    if($i == 200){
        array_push($insert_statements, 'INSERT INTO idk_kandidat_projekcije (kandidat_id, proracunati_pocetak_rada) VALUES '.implode(',', $insert_statement_values));
        $i = 0;
        $insert_statement_values = array();
    }
    else{
        $i ++;
    }
    $old_projection = findProjectionByCandidateId($new_projection['candidate_id'], $current_projections);
    $marked_candidate = markCandidateForUpdate($old_projection, $new_projection);
    if(!is_null($marked_candidate)){
        array_push($marked_candidates, $marked_candidate);
    }
    
}
if($i != 0){
    array_push($insert_statements, 'INSERT INTO idk_kandidat_projekcije (kandidat_id, proracunati_pocetak_rada) VALUES '.implode(',', $insert_statement_values));
}

$delete_all = $db -> prepare('DELETE FROM idk_kandidat_projekcije');
$delete_all -> execute();


if($marked_candidates){
    $update_installments_query = updateMultipleCandidatesInstallments($marked_candidates);
}


$insert_statements = implode(';', $insert_statements);
$insert_query = $db -> prepare($insert_statements.";".$update_installments_query);
$insert_query -> execute();


function updateMultipleCandidatesInstallments($marked_candidates){
    Global $db;
   
    $candidates = array();
    $date_statements = array();
    $real_date_statements = array();
    $temporary_log = array();

    foreach($marked_candidates as $candidate){
        array_push($temporary_log, $candidate['candidate_id']."::(".$candidate['date_diff'].")");

        $candidate_id = $candidate['candidate_id'];
        $shift_dates  = $candidate['date_diff'];

        array_push($date_statements, "WHEN kandidat_id = $candidate_id THEN DATE_ADD(kf_datum, INTERVAL $shift_dates DAY)");
        array_push($real_date_statements, "WHEN kandidat_id = $candidate_id THEN DATE_ADD(kf_datum_stvarni, INTERVAL $shift_dates DAY)");
        array_push($candidates, $candidate_id);
    }
    
    $temporary_log = "'Cron projekcije markirao kandidate:" . json_encode(implode(',', $temporary_log))."'";

	$log_query = $db->prepare("
        INSERT INTO idk_logs
            (log_employeeid, log_desc, log_date, log_type)
        VALUES
            (0, $temporary_log, now(), 0)
    ");

    $log_query->execute();

    $update_query = "
        UPDATE idk_kandidat_financije
            SET 
            kf_datum = (
                CASE
                    ".implode(' ', $date_statements)."
                END
            ),
            kf_datum_stvarni = (
                CASE
                    ".implode(' ', $real_date_statements)."
                END
            )
            WHERE kf_placeno = 0
            AND kf_type NOT IN (0,5)
            AND kandidat_id IN (".implode(',', $candidates).");

    ";
    return $update_query;
}


?>