<?php
include($_SERVER["DOCUMENT_ROOT"] . "/includes/connect.php");

if (!isset($_REQUEST["page"])) {
    http_response_code(500);
    die("Missing query parameter!");
}

$page = $_REQUEST['page'];
switch ($page) {
    case 'currentCastings':

        if (isset($_REQUEST["castingId"])) {
            $casting_id = $_REQUEST["castingId"];
            $pap_query = ' WHERE pap.pap_group_id IN ('.$casting_id.')';
            $tsr_query = ' WHERE tsr.tsr_interview_id IN ('.$casting_id.')';
        }else{
            $pap_query = "";
            $tsr_query = "";
        }

        $sql1 = 'SELECT 
                    employee_id,
                    CONCAT(emp.employee_firstname, " ", emp.employee_lastname) as agent_name,
                    kan.tf_reserved_agent as agent_id
                FROM idk_kandidati kan
                JOIN idk_employees emp
                ON kan.tf_reserved_agent = emp.employee_id
                INNER JOIN idk_pp_cand_appts pca ON kan.kandidat_id = pca.pca_kandidat_id  AND pca.pca_status = 1
				JOIN idk_pp_appointments pap on pap.pap_id = pca.pca_appointment_id '.$pap_query.'
                GROUP BY kan.tf_reserved_agent';

        $sql2 = 'SELECT 
                employee_id,
                CONCAT(emp.employee_firstname, " ", emp.employee_lastname) as agent_name,
                tsr.tsr_agent_id as agent_id, 
                SUM( IF( tsr.tsr_candidate_id IN (SELECT tsr_candidate_id FROM idk_tf_stats_reservations WHERE tsr_status = 4 AND tsr_agent_id = employee_id AND tsr_interview_id = tsr.tsr_interview_id),  1, 0)) AS pristao,
                SUM( IF( tsr.tsr_candidate_id IN (SELECT tsr_candidate_id FROM idk_tf_stats_reservations WHERE tsr_status = 5 AND tsr_agent_id = employee_id AND tsr_interview_id = tsr.tsr_interview_id),  1, 0)) AS dolazi,
                SUM( IF( tsr.tsr_candidate_id IN (SELECT tsr_candidate_id FROM idk_tf_stats_reservations WHERE tsr_status = 1 AND tsr_agent_id = employee_id AND tsr_interview_id = tsr.tsr_interview_id),  1, 0)) AS dosao,
                SUM( IF( tsr.tsr_candidate_id IN (SELECT tsr_candidate_id FROM idk_tf_stats_reservations WHERE tsr_status = 2 AND tsr_agent_id = employee_id AND tsr_interview_id = tsr.tsr_interview_id),  1, 0)) AS nije_dosao,
                SUM( IF( tsr.tsr_candidate_id IN (SELECT tsr_candidate_id FROM idk_tf_stats_reservations WHERE tsr_status = 3 AND tsr_agent_id = employee_id AND tsr_interview_id = tsr.tsr_interview_id),  1, 0)) AS odustao
            FROM idk_tf_stats_reservations tsr
            JOIN idk_employees emp
            ON tsr.tsr_agent_id = emp.employee_id
            '.$tsr_query.'
            GROUP BY tsr.tsr_agent_id';

        $res1 = $db->query($sql1)->fetchAll(PDO::FETCH_CLASS);
        $res2 = $db->query($sql2)->fetchAll(PDO::FETCH_CLASS);

        // Concatenate arrays
        $results = array_merge($res1, $res2);
        
        // Group the results by agent_id
        $grouped_results = array();
        foreach ($results as $result) {
            $agent_id = $result->agent_id;
            if (!isset($grouped_results[$agent_id])) {
                $grouped_results[$agent_id] = $result;
            } else {
                // Merge the objects with the same agent_id
                $grouped_results[$agent_id] = (object) array_merge((array) $grouped_results[$agent_id], (array) $result);
            }
            if (!property_exists($grouped_results[$agent_id], 'pristao')) {
                $grouped_results[$agent_id]->pristao = 0;
            }
            if (!property_exists($grouped_results[$agent_id], 'dolazi')) {
                $grouped_results[$agent_id]->dolazi = 0;
            }
            // Check if the dosao, nije_dosao, and odustao attributes exist for this agent, and if not, set them to 0
            if (!property_exists($grouped_results[$agent_id], 'dosao')) {
                $grouped_results[$agent_id]->dosao = 0;
            }
            if (!property_exists($grouped_results[$agent_id], 'nije_dosao')) {
                $grouped_results[$agent_id]->nije_dosao = 0;
            }
            if (!property_exists($grouped_results[$agent_id], 'odustao')) {
                $grouped_results[$agent_id]->odustao = 0;
            }
        }


        // Convert the associative array to a numerical indexed array
        $output = array_values($grouped_results);
        echo json_encode($output);

        break;

}