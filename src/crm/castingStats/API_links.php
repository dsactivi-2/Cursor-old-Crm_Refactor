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

        $sql = '
            SELECT 
                stats_by_link.lg_id as id_urla, 
                stats_by_link.lg_url as link_naziv, 
                stats_by_link.tsr_interview_id,
                SUM( IF( stats_by_link.tsr_status = 1,  stats_by_link.counter, 0)) AS dosao,
                SUM( IF( stats_by_link.tsr_status = 2,  stats_by_link.counter, 0)) AS nije_dosao, 
                SUM( IF( stats_by_link.tsr_status = 3,  stats_by_link.counter, 0)) AS odustao,
                SUM(stats_by_link.counter) AS ukupno,
                FORMAT(100 * SUM( IF( stats_by_link.tsr_status = 1,  stats_by_link.counter, 0))/(SUM( IF( stats_by_link.tsr_status = 1,  stats_by_link.counter, 0)) + SUM( IF( stats_by_link.tsr_status = 2,  stats_by_link.counter, 0))), 2) as dosao_pristao,
                FORMAT(100 * SUM( IF( stats_by_link.tsr_status = 1,  stats_by_link.counter, 0))/(SUM( IF( stats_by_link.tsr_status = 1,  stats_by_link.counter, 0)) + SUM( IF( stats_by_link.tsr_status = 2,  stats_by_link.counter, 0)) + SUM( IF( stats_by_link.tsr_status = 3,  stats_by_link.counter, 0))), 2) as dosao_ukupno
            FROM  
                (
                    SELECT 
                        link_stats.lg_id, link_stats.lg_url, link_stats.tsr_status, COUNT(*) as counter, link_stats.tsr_interview_id
                    FROM 
                    (
                        SELECT 
                            lg.lg_id , lg.lg_url, lg.lg_nalogid, lg.idk_urlimg_prijave, lg.lg_datetime, tsr.tsr_status, tsr.tsr_link_id, tsr.tsr_interview_id, tsr.tsr_nalog_id
                        FROM 
                            idk_link_generator lg
                        JOIN 
                            idk_tf_stats_reservations tsr 
                        ON 
                            tsr.tsr_link_id = lg.lg_id
                        '.$query_param.'    
                    ) AS link_stats
                    GROUP BY 
                        link_stats.lg_id, link_stats.tsr_status
                )
                AS 
                stats_by_link
            GROUP BY stats_by_link.lg_id
        ';

        $res = $db->query($sql)->fetchAll(PDO::FETCH_CLASS);

        echo json_encode($res);

    break;

    default:
        http_response_code(500);
        die("No page specified!");
    break; 
}