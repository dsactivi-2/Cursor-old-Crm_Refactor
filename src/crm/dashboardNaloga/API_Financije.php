<?php
include($_SERVER["DOCUMENT_ROOT"] . "/includes/connect.php");
include($_SERVER["DOCUMENT_ROOT"] . '/jobstep_pp/includes/classes/candidatesProjection.php');

if (!isset($_REQUEST["page"])) {
    http_response_code(500);
    die("Missing page parameter!");
}

$page = $_REQUEST['page'];
switch ($page) {
    case 'allCompanies':
        header("Content-Type: application/json");

        $query = '
            SELECT
                all_months.company_id,
                all_months.company_name,
                SUM(all_months.total_mjesec) AS total_mjesec,
                all_months.formatted_month
            FROM(
                SELECT
                    company_id,
                    company_name,

                    CASE 
                        WHEN date_to_look_at < DATE_FORMAT(CURDATE(), "%Y-%m-01") THEN CONCAT(MONTH(CURDATE()),"/",RIGHT(YEAR(CURDATE()),2)) 
                        ELSE formatted_month
                    END AS formatted_month,

                    ROUND(SUM(total_mjesec),2) AS total_mjesec,
                    date_to_look_at
                FROM(
                        (
                        SELECT
                            company_id,
                            idk_companies.company_name,

                            CASE 
                                WHEN nf_placeno IN(0, 1) 
                                THEN CONCAT(MONTH(DATE_ADD(nf_datum_aktiviranja,INTERVAL idk_nalozi.nalog_dospijece DAY)),"/",RIGHT(YEAR(DATE_ADD(nf_datum_aktiviranja,INTERVAL idk_nalozi.nalog_dospijece DAY)),2)) 
                                
                                WHEN nf_placeno = 2 
                                THEN CONCAT(MONTH(DATE_ADD(nf_datum_fakturisanja,INTERVAL idk_nalozi.nalog_dospijece DAY)),"/",RIGHT(YEAR(DATE_ADD(nf_datum_fakturisanja,INTERVAL idk_nalozi.nalog_dospijece DAY)),2)
                            )
                            END AS formatted_month,

                            CASE 
                                WHEN nf_placeno IN(0, 1) 
                                THEN DATE_ADD(nf_datum_aktiviranja,INTERVAL idk_nalozi.nalog_dospijece DAY) 
                                
                                WHEN nf_placeno = 2 
                                THEN DATE_ADD(nf_datum_fakturisanja,INTERVAL idk_nalozi.nalog_dospijece DAY)

                            END AS date_to_look_at,

                            CASE WHEN idk_nalozi.nalog_firma_fakturisanja = 1 THEN SUM(nf_iznos * 1.19)
                                    WHEN idk_nalozi.nalog_firma_fakturisanja = 2 THEN SUM(nf_iznos)
                                    ELSE nf_iznos
                            END AS total_mjesec

                            FROM idk_nalog_financije
                            INNER JOIN idk_nalozi ON idk_nalog_financije.nf_nalog_id = idk_nalozi.nalog_id
                            INNER JOIN idk_companies ON idk_nalozi.kompanija_id = idk_companies.company_id
                            WHERE
                                nf_placeno IN(0, 1, 2)
                            GROUP BY
                                idk_companies.company_id,
                                formatted_month
                            )
                        UNION
                            (
                            SELECT
                                company_id,
                                idk_companies.company_name,
                                CASE 
                                    WHEN kf_placeno IN(0, 1) 
                                    THEN CONCAT(MONTH(DATE_ADD(kf_datum_stvarni,INTERVAL idk_nalozi.nalog_dospijece DAY)),"/",RIGHT(YEAR(DATE_ADD(kf_datum_stvarni,INTERVAL idk_nalozi.nalog_dospijece DAY)),2)) 
                                    
                                    WHEN kf_placeno = 2 
                                    THEN CONCAT(MONTH(DATE_ADD(kf_datum_fakturisanja,INTERVAL idk_nalozi.nalog_dospijece DAY)),"/",RIGHT(YEAR(DATE_ADD(kf_datum_fakturisanja,INTERVAL idk_nalozi.nalog_dospijece DAY)),2))
                                END AS formatted_month,
                        
                                CASE 
                                    WHEN kf_placeno IN(0, 1) 
                                    THEN DATE_ADD(kf_datum_stvarni, INTERVAL idk_nalozi.nalog_dospijece DAY) 
                                    
                                    WHEN kf_placeno = 2 
                                    THEN DATE_ADD(kf_datum_fakturisanja,INTERVAL idk_nalozi.nalog_dospijece DAY)
                                END AS date_to_look_at,

                                CASE WHEN idk_nalozi.nalog_firma_fakturisanja = 1 THEN SUM(kf_iznos * 1.19)
                                    WHEN idk_nalozi.nalog_firma_fakturisanja = 2 THEN SUM(kf_iznos)
                                    ELSE kf_iznos
                                END AS total_mjesec
                                
                            FROM idk_kandidat_financije
                            INNER JOIN idk_nalozi ON idk_kandidat_financije.nalog_id = idk_nalozi.nalog_id
                            INNER JOIN idk_companies ON idk_nalozi.kompanija_id = idk_companies.company_id
                            WHERE
                                kf_placeno IN(0, 1, 2) AND idk_kandidat_financije.kf_status NOT IN(0, 3)
                            GROUP BY
                                idk_companies.company_id,
                                formatted_month
                        )
                    ) AS subquery
                GROUP BY
                    company_id,
                    formatted_month
                ORDER BY
                    `subquery`.`formatted_month`
                DESC
            ) all_months
                GROUP BY company_id, formatted_month
        ';
        $rows = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);

        $output = [];
        foreach ($rows as $row) {
            $companyId = $row["company_id"];
            $formattedMonth = $row["formatted_month"];
            $totalMjesec = $row["total_mjesec"];

            $existingCompany = array_filter($output, function ($company) use ($companyId) {
                return $company["company_id"] === $companyId;
            });

            if (count($existingCompany) > 0) {
                $existingCompanyIndex = array_keys($existingCompany)[0];
                $output[$existingCompanyIndex][$formattedMonth] = $totalMjesec;
            } else {
                $company = [
                    "company_name" => $row["company_name"],
                    "company_id" => $companyId,
                    $formattedMonth => $totalMjesec
                ];
            $output[] = $company;
            }
        }

        echo json_encode($output);

        break;
    default:
        http_response_code(500);
        die("Invalid page specified!");
        break;
}
