<?php

include($_SERVER["DOCUMENT_ROOT"] . "/includes/functions.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/includes/env.php");

$request = "";

if(isset($_REQUEST["request"])) {
	$request = $_REQUEST["request"];

    switch ($request){
        case "table_user_activity":
            $sql = $db -> prepare('
                SELECT jp_ime, jp_prezime, jp_direction_number, jp_makler_id, jp_register_date, jp_first_login, jp_latest_activity, jp_confirmedaccount, jp_datum_deaktivacije
                FROM idk_jobstep_partners
                WHERE jp_user_type = 1
                AND jp_partner_company = 3
            ');

            $sql -> execute();

            $data = array();

            while($row = $sql -> fetch()){
                $name = $row['jp_ime'];
                $last_name = $row['jp_prezime'];
                $directorate_number = $row['jp_direction_number'];
                $makler_id = $row['jp_makler_id'];
                $registration_date = $row['jp_register_date'];
                $first_login_date = $row['jp_first_login'];
                $latest_activity_date = $row['jp_latest_activity'];
                // $activity_count = $row['jp_activity_count'];
                $confirmed_account = $row['jp_confirmedaccount'];
                // $device = $row['jp_device'];
                // $ios_version = $row['jp_ios_version'];
                // $app_version = $row['jp_app_version'];
                $deactivation_date = $row['jp_datum_deaktivacije'];

                $account_status = 'Disabled';
                if($confirmed_account){
                    $account_status = 'Active';
                }
                else if(is_null($deactivation_date)){
                    $account_status = 'Pending';
                }

                // if(is_null($ios_version)){
                //     $ios_version = 'N/A';
                // }
                // if(is_null($app_version)){
                //     $app_version = 'N/A';
                // }
                // if(is_null($device)){
                //     $device = 'N/A';
                // }

                if(is_null($first_login_date)){
                    $first_login_date = 'N/A';
                }
                else {
                    $first_login_date = date('d.m.y H:i', strtotime($first_login_date));
                }
                
                if(is_null($latest_activity_date)){
                    $latest_activity_date = 'N/A';
                }
                else{
                    $latest_activity_date = date('d.m.y H:i', strtotime($latest_activity_date));
                }


                $rowData['full_name'] = $name.' '.$last_name;
                $rowData['directive_number'] = $directorate_number;
                $rowData['makler_id'] = $makler_id;
                $rowData['registration_date'] = date('d.m.Y', strtotime($registration_date));
                $rowData['first_login_date'] = $first_login_date;
                $rowData['latest_activity_date'] = $latest_activity_date;
                // $rowData['activity_count'] = $activity_count;
                $rowData['account_status'] = $account_status;
                // $rowData['ios_version'] = $ios_version;
                // $rowData['app_version'] = $app_version;
                // $rowData['device'] = $device;

                $data[] = $rowData;
            }

            echo json_encode(['data' => $data]);
        break;

        case "graph_registered_users":
            $filter = $_GET["filter"];
            $currentQuarter = ceil(date('n') / 3);

            switch ($currentQuarter) {
                case 1:
                    $prevQuarterStart = date('Y-m-d', strtotime('first day of January last year'));
                    $prevQuarterEnd = date('Y-m-d', strtotime('last day of December last year'));
                    break;
                case 2:
                    $prevQuarterStart = date('Y-m-d', strtotime('first day of January'));
                    $prevQuarterEnd = date('Y-m-d', strtotime('last day of March'));
                    break;
                case 3:
                    $prevQuarterStart = date('Y-m-d', strtotime('first day of April'));
                    $prevQuarterEnd = date('Y-m-d', strtotime('last day of June'));
                    break;
                case 4:
                    $prevQuarterStart = date('Y-m-d', strtotime('first day of July'));
                    $prevQuarterEnd = date('Y-m-d', strtotime('last day of September'));
                    break;
            }

            switch($filter){
                case 1:
                    $sql = "SELECT jp_register_date, count(jp_id) as registered_users FROM idk_jobstep_partners WHERE (jp_register_date BETWEEN DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY) AND CURRENT_DATE) AND jp_user_type = 1 GROUP BY DATE(jp_register_date)";
                break;
                case 2:
                    $sql = "SELECT jp_register_date, count(jp_id) as registered_users FROM idk_jobstep_partners WHERE (jp_register_date BETWEEN DATE_SUB(CURRENT_DATE(), INTERVAL 30 DAY) AND CURRENT_DATE) AND jp_user_type = 1 GROUP BY DATE(jp_register_date)";
                break;
                case 3:
                    $sql = "SELECT count(jp_id) as registered_users FROM idk_jobstep_partners WHERE (jp_register_date BETWEEN '$prevQuarterStart' AND '$prevQuarterEnd') AND jp_user_type = 1";
                break;
                case 4:
                    $sql = "SELECT jp_register_date, count(jp_id) AS registered_users FROM idk_jobstep_partners WHERE (jp_register_date BETWEEN DATE_SUB(CURRENT_DATE(), INTERVAL 12 MONTH) AND CURRENT_DATE) AND jp_user_type = 1 GROUP BY MONTH(jp_register_date)";
                break;
            }

            $stmt = $db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll();
            $data = [];
            if($filter == 4){
                foreach($result as $row){
                    $data[] = [
                        date('m/y', strtotime($row['jp_register_date'])) => $row['registered_users']
                    ];
                }
            } else if ($filter == 3){
                $data[] = [
                    'registered_users' => $result[0]['registered_users']
                ];
            } else {
                foreach($result as $row){
                    $data[] = [
                        date('d/m/y', strtotime($row['jp_register_date'])) => $row['registered_users']
                    ];
                }
            }
            echo json_encode($data);
        break;

        case "graph_active_daily_users":
            $filter = $_GET["filter"];
            $currentQuarter = ceil(date('n') / 3);

            switch ($currentQuarter) {
                case 1:
                    $prevQuarterStart = date('Y-m-d', strtotime('first day of January last year'));
                    $prevQuarterEnd = date('Y-m-d', strtotime('last day of December last year'));
                    break;
                case 2:
                    $prevQuarterStart = date('Y-m-d', strtotime('first day of January'));
                    $prevQuarterEnd = date('Y-m-d', strtotime('last day of March'));
                    break;
                case 3:
                    $prevQuarterStart = date('Y-m-d', strtotime('first day of April'));
                    $prevQuarterEnd = date('Y-m-d', strtotime('last day of June'));
                    break;
                case 4:
                    $prevQuarterStart = date('Y-m-d', strtotime('first day of July'));
                    $prevQuarterEnd = date('Y-m-d', strtotime('last day of September'));
                    break;
            }
            switch($filter){
                case 1:
                    $sql = "SELECT 
                                DATE(created_at) AS activity_date,
                                COUNT(DISTINCT user_id) AS active_users
                            FROM 
                                idk_partner_user_log
                            WHERE 
                                created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                            GROUP BY 
                                activity_date
                            ORDER BY 
                                activity_date";
                break;
                case 2:
                    $sql = "SELECT 
                                DATE(created_at) AS activity_date,
                                COUNT(DISTINCT user_id) AS active_users
                            FROM 
                                idk_partner_user_log
                            WHERE 
                                created_at >= DATE_SUB(CURDATE(), INTERVAL 29 DAY)
                            GROUP BY 
                                activity_date
                            ORDER BY 
                                activity_date";
                break;
                case 3:
                    $sql = "SELECT 
                                COUNT(DISTINCT user_id) AS active_users
                            FROM 
                                idk_partner_user_log
                            WHERE 
                                created_at BETWEEN '$prevQuarterStart' AND '$prevQuarterEnd'";
                break;
                case 4:
                    $sql = "SELECT 
                                DATE(created_at) AS activity_date,
                                COUNT(DISTINCT user_id) AS active_users
                            FROM 
                                idk_partner_user_log
                            WHERE 
                                created_at >= DATE_SUB(CURDATE(), INTERVAL 11 MONTH)
                            GROUP BY 
                                activity_date
                            ORDER BY 
                                activity_date";
                break;
            }

            $stmt = $db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll();
            $data = [];
            if($filter == 4){
                foreach($result as $row){
                    $data[] = [
                        date('m/y', strtotime($row['activity_date'])) => $row['active_users']
                    ];
                }
            } else if ($filter == 3){
                $data[] = [
                    'active_users' => $result[0]['active_users']
                ];
            } else {
                foreach($result as $row){
                    $data[] = [
                        date('d/m/y', strtotime($row['activity_date'])) => $row['active_users']
                    ];
                }
            }

            echo json_encode($data);
        break;

        case "pie_current_active_users":
            $data = [];
            $sql = "SELECT 
                        COUNT(CASE WHEN jp_latest_activity >= NOW() - INTERVAL 1 HOUR THEN jp_id END) AS active_users,
                        COUNT(CASE WHEN jp_latest_activity < NOW() - INTERVAL 1 HOUR OR jp_latest_activity IS NULL THEN jp_id END) AS inactive_users
                    FROM 
                        idk_jobstep_partners
                    WHERE jp_user_type = 1";

            $stmt = $db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch();
            $data = [
                'active_users' => $result['active_users'],
                'inactive_users' => $result['inactive_users']
            ];

            echo json_encode($data);
        break;

        case "pie_used_devices":
        break;

        case "table_work_activity":
            $data = [];
            $sql = "SELECT
                        jp_id,
                        jp_imeprezime,
                        jp_direction_number,
                        jp_makler_id,
                        br_kompanija.ukupan_broj_kompanija,
                        br_odbijenih.broj_odbijenih,
                        br_ugovora.broj_ugovora,
                        br_prijavljenih_kandidata.prijavljeni_kandidati,
                        COALESCE(SUM(CAST(company_total_workers_required AS UNSIGNED)), 0) AS ukupan_broj_trazenih_kandidata
                    FROM
                        idk_jobstep_partners
                    JOIN
                        (
                            SELECT
                                js_partner_id,
                                count(company_id) as ukupan_broj_kompanija
                            FROM
                                idk_companies
                            GROUP BY js_partner_id
                        ) as br_kompanija
                    ON
                        idk_jobstep_partners.jp_id = br_kompanija.js_partner_id
                    LEFT JOIN 
                        (
                            SELECT
                                js_partner_id,
                                count(company_id) as broj_odbijenih
                            FROM
                                idk_companies
                            WHERE
                                company_status = 5
                            GROUP BY js_partner_id
                        ) as br_odbijenih
                    ON
                        idk_jobstep_partners.jp_id = br_odbijenih.js_partner_id
                    LEFT JOIN
                        (
                        SELECT 
                            js_partner_id,
                            count(company_id) as broj_ugovora
                        FROM
                                idk_companies
                        WHERE
                                company_status IN (1,3,6)
                        GROUP BY js_partner_id
                        ) as br_ugovora
                    ON
                        idk_jobstep_partners.jp_id = br_ugovora.js_partner_id
                    JOIN
                        (
                        SELECT 
                            kandidat_partnerid,
                            count(kandidat_id) as prijavljeni_kandidati
                        FROM
                            idk_kandidati
                        GROUP BY kandidat_partnerid
                        ) as br_prijavljenih_kandidata
                    ON
                        idk_jobstep_partners.jp_id = br_prijavljenih_kandidata.kandidat_partnerid
                    LEFT JOIN
                        idk_companies 
                    ON 
                        idk_companies.js_partner_id = idk_jobstep_partners.jp_id
                    WHERE
                        jp_confirmedaccount = 1 AND jp_user_type = 1
                    GROUP BY jp_id";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll();
            foreach($result as $row){
                if($row["jp_direction_number"] == ""){
                    $direction_number = "-";
                } else {
                    $direction_number = $row["jp_direction_number"];
                }

                if($row["ukupan_broj_kompanija"] == null){
                    $ukuapn_broj_kompanija = 0;
                } else {
                    $ukuapn_broj_kompanija = $row["ukupan_broj_kompanija"];
                }

                if($row["broj_odbijenih"] == null){
                    $broj_odbijenih = 0;
                } else {
                    $broj_odbijenih = $row["broj_odbijenih"];
                }

                if($row["broj_ugovora"] == null){
                    $broj_ugovora = 0;
                } else {
                    $broj_ugovora = $row["broj_ugovora"];
                }

                if($row["prijavljeni_kandidati"] == null){
                    $prijavljeni_kandidati = 0;
                } else {
                    $prijavljeni_kandidati = $row["prijavljeni_kandidati"];
                }
                $data[] = [
                    'full_name' => $row['jp_imeprezime'],
                    'directive_number' => $direction_number,
                    'makler_id' => $row['jp_makler_id'],
                    'total_companies' => $ukuapn_broj_kompanija,
                    'rejected_companies' => $broj_odbijenih,
                    'contracted_companies' => $broj_ugovora,
                    'applied_candidates' => $prijavljeni_kandidati,
                    'total_candidates' => $row['ukupan_broj_trazenih_kandidata']
                ];
            }

            echo json_encode(['data' => $data]);
        break;

        case "bar_company_sizes":
            $small = ["1-10", "11-50", "1-25", "20-50", "26-50"];
            $medium = ["bis 100", "0 - 100", "51-100", "0-100"];
            $enterprise = ["251-500", "100-500", "101 - 500", "101-500"];
            $vip = ["1000+", "> 1000", ">1000", "500-1000", "501 - 1000", "501-1000", "501+", ">500"];
            $small_count = 0;
            $medium_count = 0;
            $enterprise_count = 0;
            $vip_count = 0;

            $sql = "SELECT company_size FROM idk_companies WHERE company_origin = 5 AND company_size IS NOT NULL";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll();
            foreach($result as $row){
                if(in_array($row["company_size"], $small)){
                    $small_count++;
                } else if(in_array($row["company_size"], $medium)){
                    $medium_count++;
                } else if(in_array($row["company_size"], $enterprise)){
                    $enterprise_count++;
                } else if(in_array($row["company_size"], $vip)){
                    $vip_count++;
                }
            }

            $data = [
                'small' => $small_count,
                'medium' => $medium_count,
                'enterprise' => $enterprise_count,
                'vip' => $vip_count
            ];

            echo json_encode($data);
        break;

        case "bar_users_per_directive":
            $sql = "SELECT jp_direction_number, count(jp_id) as user_count FROM `idk_jobstep_partners` WHERE jp_user_type = 1 GROUP BY jp_direction_number";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll();
            $data = [];
            foreach($result as $row){
                $data[] = [
                    'directive_number' => $row['jp_direction_number'],
                    'user_count' => $row['user_count']
                ];
            }

            echo json_encode(['data' => $data]);
        break;

        case "pie_company_statuses":
            $data = ["Archived" => 0,
                    "Active" => 0,
                    "New" => 0,
                    "In_progress" => 0,
                    "On_hold" => 0,
                    "Rejected" => 0,
                    "Finished" => 0];
            $sql = "SELECT all_statuses.company_status, COUNT(c.company_status) AS status_count
                    FROM (
                        SELECT 0 AS company_status
                        UNION ALL
                        SELECT 1 AS company_status
                        UNION ALL
                        SELECT 2 AS company_status
                        UNION ALL
                        SELECT 3 AS company_status
                        UNION ALL
                        SELECT 4 AS company_status
                        UNION ALL
                        SELECT 5 AS company_status
                        UNION ALL
                        SELECT 6 AS company_status
                    ) AS all_statuses
                    LEFT JOIN idk_companies AS c ON all_statuses.company_status = c.company_status AND c.company_origin = 5
                    GROUP BY all_statuses.company_status
                    ORDER BY all_statuses.company_status";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll();
            foreach($result as $row){
                switch($row["company_status"]){
                    case "0":
                        $status = "Archived";
                    break;
                    case "1":
                        $status = "Active";
                    break;
                    case "2":
                        $status = "New";
                    break;
                    case "3":
                        $status = "In_progress";
                    break;
                    case "4":
                        $status = "On_hold";
                    break;
                    case "5":
                        $status = "Rejected";
                    break;
                    case "6":
                        $status = "Finished";
                    break;
                    default:
                        $status = "Unknown";
                    break;
                }
                $data[$status] = intval($row["status_count"]) ?: 0;
            }

            echo json_encode($data);
        break;

        case "pie_common_professions":
            $sql = "SELECT profession_id, COUNT(*) as profession_count, kp_ime
                    FROM (
                        SELECT TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(company_professions, ',', numbers.n), ',', -1)) AS profession_id
                        FROM idk_companies
                        INNER JOIN (
                            SELECT 1 as n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
                            UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8
                            UNION ALL SELECT 9 UNION ALL SELECT 10
                        ) numbers ON CHAR_LENGTH(company_professions) - CHAR_LENGTH(REPLACE(company_professions, ',', '')) >= numbers.n - 1
                    ) as ProfessionList
                    JOIN
                        idk_kandidat_pozicija
                    ON 
                        ProfessionList.profession_id = idk_kandidat_pozicija.kp_id
                    WHERE profession_id != ''
                    GROUP BY profession_id
                    ORDER BY profession_count DESC
                    LIMIT 10";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll();
            $data = [];
            foreach($result as $row){
                $data[] = [
                    'profession' => $row['kp_ime'],
                    'profession_count' => $row['profession_count']
                ];
            }

            echo json_encode($data);
        break;

    }
}
?>