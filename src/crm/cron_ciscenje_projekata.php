<?php

exit();
//CRON SE PRIVREMENO UKIDA, ADILU POTREBNI KANDIDATI IZ CILJANOG NALOGA
include("includes/functions.php");


//UZIMA DATUM KOJI JE BIO PRIJE 3 DANA ISKLJUCUJUCI VIKENDE
$date = date("Y-m-d", strtotime("-3 weekday"));

//UZMI NALOG ID IZ GRUPE DATUMA 
$query_get_group = $db->prepare("SELECT papq_nalog_id, ppaq_id FROM idk_pp_appointment_groups WHERE ppaq_end_date <= :date AND ppaq_casting_cron_executed = 0");
$query_get_group->execute(array(
    ":date" => $date
));
while($result_group = $query_get_group->fetch()){
    $candidate_ids 	= array();
    $group_nalog 	= $result_group["papq_nalog_id"];
    $group_id 		= $result_group["ppaq_id"];
    //POKUPI SVE KANDIDATE IZ KASTINGA ZA TAJ NALOG
    $query_get_candidates = $db->prepare("
                                        SELECT
                                            pk_kandidatid,
                                            pk_projectid
                                        FROM
                                            idk_project_kandidati
                                        JOIN
                                            idk_projects
                                        ON
                                            idk_project_kandidati.pk_projectid = idk_projects.project_id
                                        JOIN
                                            idk_nalozi
                                        ON
                                            idk_projects.project_nalogid = idk_nalozi.nalog_id
                                        JOIN 
                                            idk_kandidati 
                                        ON
                                            kandidat_id = pk_kandidatid
                                        WHERE
                                            nalog_id = :group_nalog
                                        AND
                                            project_name LIKE '% - Casting%'
                                        AND kandidat_status_prijave NOT IN (4,7,8,9,10,12,15,18,21,24,27)
                                    ");
    $query_get_candidates->execute(array(
        ":group_nalog" => $group_nalog
    ));
    //UZMI ID PROJEKTA PRIJAVE ZA TAJ NALOG
    $query_get_prijave = $db->prepare("SELECT project_id FROM idk_projects WHERE project_name LIKE '% - Prijave%' AND project_nalogid = :group_nalog");
    $query_get_prijave->execute(array(
        ":group_nalog" => $group_nalog
    ));
    $result_prijave = $query_get_prijave->fetch();
    $prijave_id 	= $result_prijave['project_id'];
    
    while($result_candidate = $query_get_candidates->fetch()){
        $candidate_id 		= $result_candidate['pk_kandidatid'];
        $casting_id			= $result_candidate['pk_projectid'];
        $candidate_ids[] 	= $candidate_id;
        
       // echo $candidate_id . "- prebacuje se u projekt Prijave(".$prijave_id.") za nalog: " . $group_nalog . "<br>";
        
        $delete_from_project = $db->prepare("DELETE FROM idk_project_kandidati WHERE pk_kandidatid = :kandidat_id AND pk_projectid = :project_id");
        $delete_from_project->execute(array(
            ":kandidat_id" 	=> $candidate_id,
            ":project_id" 	=> $casting_id
        ));
        
        $insert_into_prijave = $db->prepare("INSERT INTO idk_project_kandidati (pk_projectid, pk_kandidatid) VALUES (:project_id, :kandidat_id)");
        $insert_into_prijave->execute(array(
            ":project_id" 	=> $prijave_id,
            ":kandidat_id" 	=> $candidate_id
        ));
        // echo $candidate_id . "- postavlja se status prijave U Projektu NZ  <br>";
        
        $set_status_prijave = $db->prepare("UPDATE idk_kandidati SET kandidat_status_prijave = :status_prijave WHERE kandidat_id = :kandidat_id");
        $set_status_prijave->execute(array(
            ":status_prijave" 	=> 2,
            ":kandidat_id"		=> $candidate_id
        ));
        
        // echo "addToLogsStatusPrijave() <br><br><br><br>";
        addToLogsStatusPrijave($casting_id, $prijave_id, 2, $candidate_id, 5);
    }
    $candidate_ids_imp = implode(",", $candidate_ids);
    $log_desc = "Cron je prebacio kandidate: (".$candidate_ids_imp.") u projekt prijave (" . $prijave_id . ") za nalog : " . $group_nalog;
    $log_type = 0;
    addToLogs($log_desc, $log_type);
    $set_cron_executed = $db->prepare("UPDATE idk_pp_appointment_groups SET ppaq_casting_cron_executed = :executed WHERE ppaq_id = :ppaq_id");
    $set_cron_executed->execute(array(
        ":ppaq_id" 	=> $group_id,
        ":executed" => 1
    ));
}
//UZMI NALOG ID IZ GRUPE DATUMA 
$query_get_group = $db->prepare("SELECT 
                                    papq_nalog_id,
                                    ppaq_id
                                FROM 
                                    idk_pp_appointment_groups 
                                WHERE 
                                    ppaq_end_date <= :date 
                                AND 
                                    ppaq_interview_cron_executed = 0
                                ");
$query_get_group->execute(array(
    ":date" => $date
));
while($result_group = $query_get_group->fetch()){
    $candidate_ids 	= array();
    $nalog_id 	= $result_group["papq_nalog_id"];
    $group_id	= $result_group["ppaq_id"];
    
    //POKUPI SVE KANDIDATE IZ INTERVJUA ZA TAJ NALOG
    /*
        *************************************************************************
        *                           NOVI QUERY ADIS                             *
        *************************************************************************
    */
    $query_get_candidates = $db->prepare("
        SELECT 
            kan.kandidat_id AS kandidatId, 
            kan1.project_id AS projectId
        FROM 
            idk_kandidati kan 
        INNER JOIN 
            (
                SELECT 
                    kanX.kandidat_id,
                    proX.project_id
                FROM 
                    idk_kandidati kanX
                INNER JOIN 
                    idk_project_kandidati pkX
                ON 
                    pkX.pk_kandidatid = kanX.kandidat_id
                    AND 
                    kanX.kandidat_nalog_id = :nalogId
                    AND 
                    kanX.kandidat_status_prijave = 3 
                INNER JOIN 
                    (
                        SELECT 
                            proXX.project_id
                        FROM 
                            idk_projects proXX
                        WHERE 
                            proXX.project_name LIKE ('%Intervju%')
                            AND 
                            proXX.project_nalogid = :nalogId
                    )
                    AS 
                    proX
                ON 
                    pkX.pk_projectid = proX.project_id
            )
            AS 
            kan1
        ON 
            kan.kandidat_id = kan1.kandidat_id
            AND 
            kan.kandidat_id NOT IN (
                SELECT 
                    praY.pra_kandidat_id
                FROM 
                    idk_pp_ratings praY
                INNER JOIN 
                    idk_pp_appointments_questions papqY
                ON 
                    praY.pra_appointment_question_id = papqY.papq_id
                    AND 
                    praY.pra_kandidat_id IN (
                        SELECT 
                            kanYY.kandidat_id
                        FROM 
                            idk_kandidati kanYY
                        INNER JOIN 
                            idk_project_kandidati pkYY
                        ON 
                            pkYY.pk_kandidatid = kanYY.kandidat_id
                            AND 
                            kanYY.kandidat_nalog_id = :nalogId
                            AND 
                            kanYY.kandidat_status_prijave = 3 
                        INNER JOIN 
                            (
                                SELECT 
                                    proYYY.project_id AS project_id
                                FROM 
                                    idk_projects proYYY
                                WHERE 
                                    proYYY.project_name LIKE ('%Intervju%')
                                    AND 
                                    proYYY.project_nalogid = :nalogId
                            )
                            AS 
                            proYY
                        ON 
                            pkYY.pk_projectid = proYY.project_id
                    )
                INNER JOIN 
                    idk_pp_appointments papY
                ON 
                    papqY.papq_appointment_id = papY.pap_id
                    AND 
                    papY.pap_nalog_id = :nalogId 
                    AND 
                    papY.pap_group_id = :groupId
                GROUP BY praY.pra_kandidat_id
            )
        INNER JOIN 
            idk_pp_cand_appts pca
        ON 
            kan.kandidat_id = pca.pca_kandidat_id
            AND 
            pca.pca_avg_rating is null
        INNER JOIN 
            idk_pp_appointments pap
        ON
            pca.pca_appointment_id = pap.pap_id
            AND 
            pap.pap_nalog_id = :nalogId 
            AND 
            pap.pap_group_id = :groupId
    ");

    /*
        *************************************************************************
        *                           NOVI QUERY ADIS                             *
        *************************************************************************
    */


    /*

        *************************************************************************
        *                       STARI QUERY OD HARUNA                           *
        *************************************************************************

    $query_get_candidates = $db->prepare("
                                        SELECT
                                            pk_kandidatid,
                                            pk_projectid
                                        FROM
                                            idk_project_kandidati
                                        JOIN
                                            idk_projects
                                        ON
                                            idk_project_kandidati.pk_projectid = idk_projects.project_id
                                        JOIN
                                            idk_nalozi
                                        ON
                                            idk_projects.project_nalogid = idk_nalozi.nalog_id
                                        WHERE
                                            nalog_id = :group_nalog
                                        AND
                                            project_name LIKE '% - Intervju%'
                                        AND
                                            pk_kandidatid NOT IN (
                                                                SELECT kan.kandidat_id
                                                                FROM (
                                                                    SELECT sqkan.kandidat_id
                                                                    FROM idk_kandidati sqkan
                                                                    JOIN idk_project_kandidati pk
                                                                    ON pk.pk_kandidatid = sqkan.kandidat_id
                                                                    JOIN idk_projects
                                                                    ON pk.pk_projectid = idk_projects.project_id
                                                                    WHERE sqkan.kandidat_nalog_id = :group_nalog
                                                                    AND idk_projects.project_name LIKE '% - Intervju%'
                                                                ) kan
                                                                WHERE kan.kandidat_id NOT IN (
                                                                    SELECT bla.pca_kandidat_id
                                                                    FROM (
                                                                        SELECT sqppa.pap_id
                                                                        FROM idk_pp_appointments sqppa
                                                                        WHERE sqppa.pap_nalog_id = :group_nalog
                                                                    )sqppa
                                                                    JOIN(
                                                                        SELECT sqca.pca_appointment_id,  sqca.pca_kandidat_id
                                                                        FROM idk_pp_cand_appts sqca
                                                                        WHERE sqca.pca_kandidat_id NOT IN(
                                                                            SELECT ppr.pra_kandidat_id
                                                                            FROM idk_pp_ratings ppr
                                                                            JOIN idk_pp_appointments_questions ppaq
                                                                            ON ppaq.papq_id = ppr.pra_appointment_question_id
                                                                            JOIN (
                                                                                SELECT sq2ppa.pap_nalog_id, sq2ppa.pap_id
                                                                                FROM idk_pp_appointments sq2ppa
                                                                                WHERE sq2ppa.pap_nalog_id = :group_nalog
                                                                            )2ppa
                                                                            ON 2ppa.pap_id = ppaq.papq_appointment_id
                                                                        )
                                                                    )bla
                                                                    ON bla.pca_appointment_id = sqppa.pap_id
                                                                )
                                                            )
                                    ");
        *************************************************************************
        *                       STARI QUERY OD HARUNA                           *
        *************************************************************************
    */
    $query_get_candidates->execute(array(
        ":nalogId" => $nalog_id, 
        ":groupId" => $group_id
    ));
    //UZMI ID PROJEKTA NIJE DOSAO NA RAZGOVOR ZA TAJ NALOG
    $query_get_nije_dosao = $db->prepare("SELECT project_id FROM idk_projects WHERE project_name LIKE '% - Nije došao na razgovor%' AND project_nalogid = :group_nalog");
    $query_get_nije_dosao->execute(array(
        ":group_nalog" => $nalog_id
    ));
    $result_nije_dosao = $query_get_nije_dosao->fetch();
    $nije_dosao_id 	= $result_nije_dosao['project_id'];
    
    while($result_candidate = $query_get_candidates->fetch()){
        $candidate_id = $result_candidate["kandidatId"];
        $interview_id = $result_candidate["projectId"];
        $candidate_ids[] 	= $candidate_id;					
        
        // echo $candidate_id . " - " . $nalog_id . " OVAJ SE BRISE <br>";
        //PREBACI KANDIDATA IZ PROJEKTA INTERVJU U PROJEKT NIJE DOSAO
            $delete_from_project = $db->prepare("DELETE FROM idk_project_kandidati WHERE pk_kandidatid = :kandidat_id AND pk_projectid = :project_id");
            $delete_from_project->execute(array(
                ":kandidat_id" 	=> $candidate_id,
                ":project_id" 	=> $interview_id
            ));
            $insert_into_nije_dosao = $db->prepare("INSERT INTO idk_project_kandidati (pk_projectid, pk_kandidatid) VALUES (:project_id, :kandidat_id)");
            $insert_into_nije_dosao->execute(array(
                ":project_id" 	=> $nije_dosao_id,
                ":kandidat_id" 	=> $candidate_id
            ));
        // POSTAVI STATUS PRIJAVE NA U Projektu NZ
            $set_status_prijave = $db->prepare("UPDATE idk_kandidati SET kandidat_status_prijave = :status_prijave WHERE kandidat_id = :kandidat_id");
            $set_status_prijave->execute(array(
                ":status_prijave" 	=> 2,
                ":kandidat_id"		=> $candidate_id
            ));
        // POSTAVI STATUS idk_pp_cand_appts NA ARHIVIRAN
            $archive_pca = $db->prepare("UPDATE idk_pp_cand_appts SET pca_status = :new_status WHERE pca_kandidat_id = :kandidat_id AND pca_status = :old_status");
            $archive_pca->execute(array(
                ":new_status" => 0,
                ":old_status" => 1,
                ":kandidat_id" => $candidate_id
            ));
            addToLogsStatusPrijave($interview_id, $nije_dosao_id, 2, $candidate_id, 5);
        
    }
    $candidate_ids_imp = implode(",", $candidate_ids);
    $log_desc = "Cron je prebacio kandidate: (".$candidate_ids_imp.") u projekt nije dosao (" . $nije_dosao_id . ") za nalog : " . $nalog_id;
    $log_type = 0;
    addToLogs($log_desc, $log_type);
    $set_cron_executed = $db->prepare("UPDATE idk_pp_appointment_groups SET ppaq_interview_cron_executed = :executed WHERE ppaq_id = :ppaq_id");
    $set_cron_executed->execute(array(
        ":ppaq_id" 	=> $group_id,
        ":executed" => 1
    ));
}