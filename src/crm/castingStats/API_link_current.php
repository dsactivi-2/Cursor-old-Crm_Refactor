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

        $sql1 = '
                SELECT 
                    lg_url,
                    kan.kandidat_visitedurl as id_urla
                FROM idk_kandidati kan
                JOIN idk_link_generator link
                ON kan.kandidat_visitedurl = link.lg_id and kan.kandidat_tf_status IN (16,18)
                INNER JOIN idk_pp_cand_appts pca ON kan.kandidat_id = pca.pca_kandidat_id
                JOIN idk_pp_appointments pap on pap.pap_id = pca.pca_appointment_id '.$pap_query.'
                GROUP BY kan.kandidat_visitedurl
        ';

        $sql2 = '
                SELECT 
                    lg_url,
                    tsr.tsr_link_id as id_urla, 
                    SUM( IF( tsr.tsr_candidate_id IN (SELECT tsr_candidate_id FROM idk_tf_stats_reservations WHERE tsr_status = 4 AND tsr_link_id = lg_id AND tsr_interview_id = tsr.tsr_interview_id),  1, 0)) AS pristao,
                    SUM( IF( tsr.tsr_candidate_id IN (SELECT tsr_candidate_id FROM idk_tf_stats_reservations WHERE tsr_status = 5 AND tsr_link_id = lg_id AND tsr_interview_id = tsr.tsr_interview_id),  1, 0)) AS dolazi,
                    SUM( IF( tsr.tsr_candidate_id IN (SELECT tsr_candidate_id FROM idk_tf_stats_reservations WHERE tsr_status = 1 AND tsr_link_id = lg_id AND tsr_interview_id = tsr.tsr_interview_id),  1, 0)) AS dosao,
                    SUM( IF( tsr.tsr_candidate_id IN (SELECT tsr_candidate_id FROM idk_tf_stats_reservations WHERE tsr_status = 2 AND tsr_link_id = lg_id AND tsr_interview_id = tsr.tsr_interview_id),  1, 0)) AS nije_dosao,
                    SUM( IF( tsr.tsr_candidate_id IN (SELECT tsr_candidate_id FROM idk_tf_stats_reservations WHERE tsr_status = 3 AND tsr_link_id = lg_id AND tsr_interview_id = tsr.tsr_interview_id),  1, 0)) AS odustao
                FROM idk_tf_stats_reservations tsr
                JOIN idk_link_generator link
                ON tsr.tsr_link_id = link.lg_id
                '.$tsr_query.'
                GROUP BY tsr.tsr_link_id
        ';

        $res1 = $db->query($sql1)->fetchAll(PDO::FETCH_CLASS);
        $res2 = $db->query($sql2)->fetchAll(PDO::FETCH_CLASS);

        // Concatenate arrays
        $results = array_merge($res1, $res2);

        // Group the results by id_urla
        $grouped_results = array();
        foreach ($results as $result) {
            $id_urla = $result->id_urla;
            if (!isset($grouped_results[$id_urla])) {
                $grouped_results[$id_urla] = $result;
            } else {
                // Merge the objects with the same id_urla
                $grouped_results[$id_urla] = (object) array_merge((array) $grouped_results[$id_urla], (array) $result);
            }

            if (!property_exists($grouped_results[$id_urla], 'pristao')) {
                $grouped_results[$id_urla]->pristao = 0;
            }
            if (!property_exists($grouped_results[$id_urla], 'dolazi')) {
                $grouped_results[$id_urla]->dolazi = 0;
            }
            // Check if the dosao, nije_dosao, and odustao attributes exist for this agent, and if not, set them to 0
            if (!property_exists($grouped_results[$id_urla], 'dosao')) {
                $grouped_results[$id_urla]->dosao = 0;
            }
            if (!property_exists($grouped_results[$id_urla], 'nije_dosao')) {
                $grouped_results[$id_urla]->nije_dosao = 0;
            }
            if (!property_exists($grouped_results[$id_urla], 'odustao')) {
                $grouped_results[$id_urla]->odustao = 0;
            }
        }

        // Convert the associative array to a numerical indexed array
        $output = array_values($grouped_results);
        echo json_encode($output);

    break;

    default:
        http_response_code(500);
        die("No page specified!");
    break; 
}