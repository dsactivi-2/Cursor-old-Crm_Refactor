<?php
include('includes/functions.php');
exit();
//Pokupi kandidate koji su na tf status 18 i koji danas kasne na razgovor
$sql = "SELECT
            pap_nalog_id,
            candidate.pca_kandidat_id,
            candidate.tf_project_id,
            candidate.tf_casting_id
        FROM
            idk_pp_appointments
        JOIN
            (
                SELECT 
                    pca_appointment_id,
                    pca_kandidat_id,
                    pca_time,
                    tf_project_id,
                    tf_casting_id
                FROM
                    idk_pp_cand_appts
                JOIN
                    idk_task_force
                ON
                    pca_kandidat_id = tf_candidate_id
                AND
                    tf_status_id IN (16,18)
                AND 
                    tf_last_active_task = 1
                WHERE
                    pca_status = 1
            ) as candidate
        ON
            idk_pp_appointments.pap_id = candidate.pca_appointment_id
        WHERE
            idk_pp_appointments.pap_date = DATE(NOW())
        AND
            TIMEDIFF(TIME(NOW()), candidate.pca_time) >= '04:00:00'";

$stmt = $db->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll();

foreach($result  as $candidate)
{

    /*
        Update glavne tabele idk_kandidati sa TF statusom 22 START
    */
        $update_candidate_table = $db->prepare("UPDATE idk_kandidati SET kandidat_tf_status = 22 WHERE kandidat_id = :candId");
        $update_candidate_table->bindParam(':candId', $candidate['pca_kandidat_id']);
        $update_candidate_table->execute();
    /*
        Update glavne tabele idk_kandidati sa TF statusom 22 END
    */

    //Trenutni tf status update last_active na 0 
    $note = "Cron prebacio na status 'Nije dosao na razgovor'";
    $sql = "UPDATE idk_task_force SET tf_last_active_task = 0 WHERE tf_candidate_id = :candidate_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':candidate_id', $candidate['pca_kandidat_id']);
    $stmt->execute();
    
    //Sve kandidate koji su na tf status 18 i koji danas kasne na razgovor postavi na tf status 22 (nije dosao na razgovor)
    $sql = "INSERT INTO 
                idk_task_force 
                (
                    tf_candidate_id, 
                    tf_nalog_id,
                    tf_project_id,
                    tf_casting_id,
                    tf_status_id, 
                    tf_note,
                    tf_important_note,
                    tf_call_appointment,
                    tf_last_active_task
                ) 
            VALUES 
                (
                    :candidate_id, 
                    :nalog_id, 
                    :project_id,
                    :casting_id,
                    22, 
                    :note,
                    1,
                    NOW(),
                    1
                )";

    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':candidate_id' => $candidate['pca_kandidat_id'],
        ':nalog_id' => $candidate['pap_nalog_id'],
        ':project_id' => $candidate['tf_project_id'],
        ':casting_id' => $candidate['tf_casting_id'],
        ':note' => $note
    ]);

	$last_agent_id = getFullReservedAgentFromCandidate($candidate['pca_kandidat_id']);
    if($last_agent_id != null)
        insertTFStat($candidate['pca_kandidat_id'], $last_agent_id, $candidate['tf_casting_id'], $candidate['pap_nalog_id'], 2);
}