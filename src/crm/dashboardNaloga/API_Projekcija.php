<?php
include($_SERVER["DOCUMENT_ROOT"] . "/includes/connect.php");
include_once($_SERVER["DOCUMENT_ROOT"] . '/jobstep_pp/includes/classes/candidatesProjection.php');
$durationPerStatus = new durationPerStatus();

if (!isset($_REQUEST["page"])) {
    http_response_code(500);
    die("Missing page parameter!");
}

$page = $_REQUEST['page'];
switch ($page) {
    case "singleCandidate":
        header('Content-Type: application/json; charset=utf-8');

        if(!isset($_GET["kandidat_id"])){
            http_response_code(400);
            die("Missing parameter kandidat_id");
        }

        $kandidat_id = $_GET['kandidat_id'];

        $candidatesProjection   = new candidatesProjection($durationPerStatus, intval($kandidat_id));
        $projectionObject       = $candidatesProjection -> getDetailedAssessment();
        echo json_encode($projectionObject);


        break;
    case 'allCompanies':
        header("Content-Type: application/json");

        $proj = new candidatesProjection($durationPerStatus, NULL, TRUE);
        $candidates = $proj->getCandidateProjectionRows();

        // Group candidates by their companies.
        $companies = [];
        foreach($candidates as $candidate){
            $month = date("n/y", $candidate["candidate_assessment"]);

            $companies[$candidate["company_id"]]["company_name"] = $candidate["company_name"];
            $companies[$candidate["company_id"]]["company_id"]   = $candidate["company_id"];
            $companies[$candidate["company_id"]][$month]         = ($companies[$candidate["company_id"]][$month] ?? 0) + 1;
        }

        // Convert the associative array to an array.
        $companiesArray = [];
        foreach($companies as $company){
            $companiesArray[] = $company;
        }

        echo json_encode($companiesArray);
        break;
    case 'allCompaniesPocetakRada':
            header("Content-Type: application/json");
           
            $query = "SELECT
                    all_months.company_id,
                    all_months.company_name,
                    COUNT(all_months.kandidat_id) AS total_candidates,
                    all_months.formatted_month
                    
                FROM (
                    SELECT
                        idk_companies.company_id,
                        idk_companies.company_name,
                        DATE_FORMAT(idk_kandidati.kandidat_dogovoreni_pocetak_rada, '%c/%y') AS formatted_month,
                        idk_kandidati.kandidat_id
                    FROM idk_kandidati
                    INNER JOIN idk_nalozi ON idk_kandidati.kandidat_nalog_id = idk_nalozi.nalog_id
                    INNER JOIN idk_companies ON idk_nalozi.kompanija_id = idk_companies.company_id
                    WHERE idk_kandidati.kandidat_dogovoreni_pocetak_rada IS NOT NULL
                    AND kandidat_dogovoreni_pocetak_rada >= DATE_FORMAT(NOW(), '%Y-%m-01')
                ) AS all_months
                GROUP BY all_months.company_id, all_months.formatted_month
                ORDER BY all_months.company_id, all_months.formatted_month DESC
            ";
            $rows = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);

            $output = [];
            foreach ($rows as $row) {
                $companyId = $row["company_id"];
                $formattedMonth = $row["formatted_month"];
                $total_candidates = intval($row["total_candidates"]);
    
                $existingCompany = array_filter($output, function ($company) use ($companyId) {
                    return $company["company_id"] === $companyId;
                });
    
                if (count($existingCompany) > 0) {
                    $existingCompanyIndex = array_keys($existingCompany)[0];
                    $output[$existingCompanyIndex][$formattedMonth] = $total_candidates;
                } else {
                    $company = [
                        "company_name" => $row["company_name"],
                        "company_id" => $companyId,
                        $formattedMonth => $total_candidates
                    ];
                $output[] = $company;
                }
            }
            echo json_encode($output);
        break;
    case 'company':
        header("Content-Type: application/json");
        if (!isset($_REQUEST["companyId"])) {
            http_response_code(500);
            die("Missing query parameter!");
        }
        $id = $_REQUEST["companyId"];

        $proj = new candidatesProjection($durationPerStatus, NULL, TRUE);
        $allCandidates = $proj->getCandidateProjectionRows();
        $candidates = array_filter($allCandidates, function($candidate) use ($id) { return $candidate["company_id"] == $id; });

        $nalozi = [];
        foreach($candidates as $candidate){
            $nalozi[$candidate["nalog_id"]]["nalog_name"] = $candidate["nalog_name"];
            $nalozi[$candidate["nalog_id"]]["nalog_id"] = $candidate["nalog_id"];
            $nalozi[$candidate["nalog_id"]][date("n/y", $candidate["candidate_assessment"])] = ($nalozi[$candidate["nalog_id"]][date("n/y", $candidate["candidate_assessment"])] ?? 0) + 1;
        }
        $naloziArray = [];
        foreach($nalozi as $nalog){
            $naloziArray[] = $nalog;
        }

        echo json_encode($naloziArray);
        break;
    case 'companyPocetakRada':
        header("Content-Type: application/json");
        if (!isset($_REQUEST["companyId"])) {
            http_response_code(500);
            die("Missing query parameter!");
        }
        $id = $_REQUEST["companyId"];

        $query = "SELECT
                all_months.nalog_id,
                all_months.nalog_naziv,
                COUNT(all_months.kandidat_id) AS total_candidates,
                all_months.formatted_month
                
            FROM (
                SELECT
                    idk_nalozi.nalog_id,
                    idk_nalozi.nalog_naziv,
                    DATE_FORMAT(idk_kandidati.kandidat_dogovoreni_pocetak_rada, '%c/%y') AS formatted_month,
                    idk_kandidati.kandidat_id
                FROM idk_kandidati
                INNER JOIN idk_nalozi ON idk_kandidati.kandidat_nalog_id = idk_nalozi.nalog_id
                INNER JOIN idk_companies ON idk_nalozi.kompanija_id = idk_companies.company_id
                WHERE idk_kandidati.kandidat_dogovoreni_pocetak_rada IS NOT NULL 
                AND idk_companies.company_id = $id
                AND kandidat_dogovoreni_pocetak_rada >= DATE_FORMAT(NOW(), '%Y-%m-01')
            ) AS all_months
            GROUP BY all_months.nalog_id, all_months.formatted_month
        ";
        $rows = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);


        $output = [];
        foreach ($rows as $row) {
            $companyId = $row["nalog_id"];
            $formattedMonth = $row["formatted_month"];
            $total_candidates = intval($row["total_candidates"]);

            $existingCompany = array_filter($output, function ($company) use ($companyId) {
                return $company["nalog_id"] === $companyId;
            });

            if (count($existingCompany) > 0) {
                $existingCompanyIndex = array_keys($existingCompany)[0];
                $output[$existingCompanyIndex][$formattedMonth] = $total_candidates;
            } else {
                $company = [
                    "nalog_name" => $row["nalog_naziv"],
                    "nalog_id" => $companyId,
                    $formattedMonth => $total_candidates
                ];
            $output[] = $company;
            }
        }
        echo json_encode($output);
        break;
    case "nalogSvi":
        header("Content-Type: application/json");
        $companyId = $_REQUEST["companyId"] ?? null;
        $nalogId = $_REQUEST["nalogId"] ?? null;
        $mjesec = $_REQUEST["mjesec"] ?? null;

        $proj = new candidatesProjection($durationPerStatus, NULL, TRUE);
        $candidates = $proj->getCandidateProjectionRows();
        
        foreach($candidates as &$candidate){
            $candidate["mjesec"] = date("n/y", $candidate["candidate_assessment"]);
        }

        if($companyId){
            $candidates = array_filter($candidates, function($cand) use ($companyId){
                return $cand["company_id"] == $companyId;
            });
        }

        if($nalogId){
            $candidates = array_filter($candidates, function($cand) use ($nalogId){
                return $cand["nalog_id"] == $nalogId;
            });
        }

        if($mjesec){
            $candidates = array_filter($candidates, function($cand) use ($mjesec){
                return $cand["mjesec"] == $mjesec;
            });
        }
        $arr = [];
        foreach($candidates as $cand){
            $arr[] = $cand;
        }
        echo json_encode($arr);

        break;

    case "nalogSviPocetakRada":
        header("Content-Type: application/json");
        $companyId = $_REQUEST["companyId"] ?? null;
        $nalogId = $_REQUEST["nalogId"] ?? null;
        $mjesec = $_REQUEST["mjesec"] ?? null;

        if($mjesec != null){
            $parts = explode('/', $mjesec);
            $month = $parts[0];
            $year = $parts[1];
    
            $firstDayOfMonth = date("$year-$month-01");
            $lastDayOfMonth = date("Y-m-t", strtotime($firstDayOfMonth));

            $month_query = "AND kandidat_dogovoreni_pocetak_rada BETWEEN '$firstDayOfMonth' AND '$lastDayOfMonth'";
        }else{
            $month_query = "";
        }
        
        if($nalogId != null){
            $nalog_query = " AND idk_nalozi.nalog_id = $nalogId";
        }else{
            $nalog_query = "";
        }

        $query = $db -> prepare("SELECT
                all_months.candidate_id,
                all_months.candidate_fullname,
                all_months.partner_name, 
                all_months.nalog_id,
                all_months.nalog_name,
                all_months.candidate_position,
                all_months.candidate_assessment,
                all_months.formatted_month
                
            FROM (
                SELECT
                    idk_kandidati.kandidat_id as candidate_id,
                    CONCAT(idk_kandidati.kandidat_ime , ' ', idk_kandidati.kandidat_prezime) as candidate_fullname,
                    partner.company_name as partner_name,
                    idk_nalozi.nalog_id,
                    idk_nalozi.nalog_naziv as nalog_name,
                    idk_kandidati.kandidat_pp_pozicija as candidate_position,
                    UNIX_TIMESTAMP(idk_kandidati.kandidat_dogovoreni_pocetak_rada) as candidate_assessment,
                    DATE_FORMAT(idk_kandidati.kandidat_dogovoreni_pocetak_rada, '%c/%y') AS formatted_month
                FROM idk_kandidati
                INNER JOIN idk_nalozi ON idk_kandidati.kandidat_nalog_id = idk_nalozi.nalog_id
                INNER JOIN idk_companies ON idk_nalozi.kompanija_id = idk_companies.company_id
                LEFT JOIN (
                    SELECT * 
                    FROM idk_pp_partners sqpp
                    JOIN idk_companies sqcomp
                    ON sqpp.ppa_company_id = sqcomp.company_id
                ) partner
                ON idk_kandidati.kandidat_ppa_partner_id = partner.ppa_id
                WHERE idk_kandidati.kandidat_dogovoreni_pocetak_rada IS NOT NULL 
                AND idk_companies.company_id = $companyId
                AND kandidat_dogovoreni_pocetak_rada >= DATE_FORMAT(NOW(), '%Y-%m-01')
                $month_query
                $nalog_query
            ) AS all_months
        ");
        
        $query->execute();
        $rows = $query->fetchAll(PDO::FETCH_ASSOC);

        $output = [];
        foreach ($rows as $row) {
            $candidate_id = $row["candidate_id"];
            $candidate_fullname = $row["candidate_fullname"];
            $partner_name = $row["partner_name"];
            $nalog_id = $row["nalog_id"];
            $nalog_name = $row["nalog_name"];
            $candidate_position = $row["candidate_position"];
            $candidate_assessment = $row["candidate_assessment"];

            $company = [
                "candidate_id" => $row["candidate_id"],
                "candidate_fullname" => $candidate_fullname,
                "partner_name" => $partner_name,
                "nalog_id" => $nalog_id,
                "nalog_name" => $nalog_name,
                "candidate_position" => $candidate_position,
                "candidate_assessment" => $candidate_assessment
            ];
            $output[] = $company;
        }
        echo json_encode($output);
    
        break;
    default:
        http_response_code(500);
        die("Invalid page specified!");
        break;
}
