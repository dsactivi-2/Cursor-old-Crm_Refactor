<?php
include($_SERVER["DOCUMENT_ROOT"] . "/includes/connect.php");

if (!isset($_REQUEST["page"])) {
    http_response_code(500);
    die("Missing query parameter!");
}

$page = $_REQUEST['page'];
switch ($page) {
    case 'finishedCastings':

        if (isset($_REQUEST["castingId"])) {
            $casting_id = $_REQUEST["castingId"];
            $query_param = ' WHERE tsr.tsr_interview_id IN ('.$casting_id.') AND tsr.tsr_status IN (1,2,3)';
        }else{
            $query_param = ' WHERE tsr.tsr_status IN (1,2,3)';
        }

        $sql = 'SELECT employee_id, agent_name, tsr_interview_id,
        SUM( IF( tsr_status = 1,  counter, 0)) AS dosao,
        SUM( IF( tsr_status = 2,  counter, 0)) AS nije_dosao, 
        SUM( IF( tsr_status = 3,  counter, 0)) AS odustao,
        SUM(counter) AS ukupno,
        FORMAT(100 * SUM( IF( tsr_status = 1,  counter, 0))/(SUM( IF( tsr_status = 1,  counter, 0)) + SUM( IF( tsr_status = 2,  counter, 0))), 2) as dosao_pristao,
        FORMAT(100 * SUM( IF( tsr_status = 1,  counter, 0))/(SUM( IF( tsr_status = 1,  counter, 0)) + SUM( IF( tsr_status = 2,  counter, 0)) + SUM( IF( tsr_status = 3,  counter, 0))), 2) as dosao_ukupno
        FROM 
            (SELECT employee_id, CONCAT(agents_stats.employee_firstname, " ", agents_stats.employee_lastname) as agent_name, tsr_status, COUNT(*) as counter, tsr_interview_id
                FROM 
                     (SELECT employee_id, employee_firstname, employee_lastname, tsr.tsr_status, tsr.tsr_interview_id
                         FROM idk_employees emp
                         JOIN idk_tf_stats_reservations tsr ON tsr.tsr_agent_id = emp.employee_id '.$query_param.'
                     ) AS agents_stats
                 GROUP BY agents_stats.employee_id, agents_stats.tsr_status
             ) AS stats_by_agent
        GROUP BY employee_id';
        $res = $db->query($sql)->fetchAll(PDO::FETCH_CLASS);
        echo json_encode($res);

    break;

}