<?php
include($_SERVER["DOCUMENT_ROOT"] . "/includes/connect.php");

if (!isset($_REQUEST["page"])) {
    http_response_code(500);
    die("Missing query parameter!");
}

$page = $_REQUEST['page'];
switch ($page) {
    case 'allCompanies':
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT
                    company_id,
                    company_name,
                    SUM(IF((kandidat_ima_nostrifikaciju = 0 OR kandidat_ima_nostrifikaciju IS NULL) AND kandidat_dipl_id = 0,        qty, 0)) AS nije_u_diplu,
                    SUM(IF((kandidat_ima_nostrifikaciju = 0 OR kandidat_ima_nostrifikaciju IS NULL) AND status = 1,                  qty, 0)) AS u_obradi_lead,
                    SUM(IF((kandidat_ima_nostrifikaciju = 0 OR kandidat_ima_nostrifikaciju IS NULL) AND status = 2,                  qty, 0)) AS prikupljanje_dokumentacije,
                    SUM(IF((kandidat_ima_nostrifikaciju = 0 OR kandidat_ima_nostrifikaciju IS NULL) AND status = 3,                  qty, 0)) AS poslana_posta,
                    SUM(IF((kandidat_ima_nostrifikaciju = 0 OR kandidat_ima_nostrifikaciju IS NULL) AND status = 4,                  qty, 0)) AS u_obradi,
                    SUM(IF((kandidat_ima_nostrifikaciju = 0 OR kandidat_ima_nostrifikaciju IS NULL) AND status = 5,                  qty, 0)) AS dopuna_dokumentacije,
                    SUM(IF( kandidat_ima_nostrifikaciju = 1 OR                                          status = 6,                  qty, 0)) AS zavrsen,
                    SUM(IF((kandidat_ima_nostrifikaciju = 0 OR kandidat_ima_nostrifikaciju IS NULL) AND status = 7,                  qty, 0)) AS arhiv
                FROM
                    (SELECT
                        company_id,
                            company_name,
                            kandidat_dipl_id,
                            status_nd_kandidata as status,
                            pstatus_nd_kandidata as pstatus,
                            kandidat_ima_nostrifikaciju,
                            COUNT(*) AS qty
                    FROM
                        (SELECT
                        company_id, company_name, nalog_id
                    FROM
                        `idk_companies`
                    INNER JOIN `idk_nalozi` ON `idk_companies`.`company_id` = `idk_nalozi`.`kompanija_id`
                    WHERE
                        `idk_nalozi`.`pristup_poslodavcima` = 1) AS companies
                    INNER JOIN idk_kandidati ON kandidat_nalog_id = companies.nalog_id
                    LEFT JOIN idk_nd_kandidata ON idk_kandidati.kandidat_dipl_id = idk_nd_kandidata.id_broj_nd_kandidata
                    WHERE idk_kandidati.kandidat_status_prijave = 9
                    GROUP BY company_id , status_nd_kandidata, pstatus_nd_kandidata, kandidat_ima_nostrifikaciju) AS res
                GROUP BY company_name";
        $res = $db->query($sql)->fetchAll(PDO::FETCH_CLASS);
        echo json_encode($res);

        break;
    case 'company':
        if (!isset($_REQUEST["companyId"])) {
            http_response_code(500);
            die("Missing query parameter!");
        }
        $id = $_REQUEST["companyId"];

        $sql = "SELECT
                    nalog_id,
                    nalog_naziv,
                    SUM(IF((kandidat_ima_nostrifikaciju = 0 OR kandidat_ima_nostrifikaciju IS NULL) AND kandidat_dipl_id = 0, qty, 0)) AS nije_u_diplu,
                    SUM(IF((kandidat_ima_nostrifikaciju = 0 OR kandidat_ima_nostrifikaciju IS NULL) AND status = 1,           qty, 0)) AS u_obradi_lead,
                    SUM(IF((kandidat_ima_nostrifikaciju = 0 OR kandidat_ima_nostrifikaciju IS NULL) AND status = 2,           qty, 0)) AS prikupljanje_dokumentacije,
                    SUM(IF((kandidat_ima_nostrifikaciju = 0 OR kandidat_ima_nostrifikaciju IS NULL) AND status = 3,           qty, 0)) AS poslana_posta,
                    SUM(IF((kandidat_ima_nostrifikaciju = 0 OR kandidat_ima_nostrifikaciju IS NULL) AND status = 4,           qty, 0)) AS u_obradi,
                    SUM(IF((kandidat_ima_nostrifikaciju = 0 OR kandidat_ima_nostrifikaciju IS NULL) AND status = 5,           qty, 0)) AS dopuna_dokumentacije,
                    SUM(IF( kandidat_ima_nostrifikaciju = 1 OR                                          status = 6,           qty, 0)) AS zavrsen,
                    SUM(IF((kandidat_ima_nostrifikaciju = 0 OR kandidat_ima_nostrifikaciju IS NULL) AND status = 7,           qty, 0)) AS arhiv
                FROM
                    (SELECT 
                            nalog_id,
                            nalog_naziv,
                            kandidat_dipl_id,
                            status_nd_kandidata as status,
                            pstatus_nd_kandidata as pstatus,
                            COUNT(*) AS qty,
                            kandidat_ima_nostrifikaciju
                         FROM (
                        SELECT * FROM
                        `idk_nalozi` WHERE `idk_nalozi`.`kompanija_id` = :id AND `idk_nalozi`.`pristup_poslodavcima` = 1
                    ) as nalozi
                    INNER JOIN idk_kandidati ON kandidat_nalog_id = nalozi.nalog_id
                    LEFT JOIN idk_nd_kandidata ON idk_kandidati.kandidat_dipl_id = idk_nd_kandidata.id_broj_nd_kandidata
                    WHERE idk_kandidati.kandidat_status_prijave = 9
                    GROUP BY nalog_id , status_nd_kandidata, pstatus_nd_kandidata, kandidat_ima_nostrifikaciju) AS res
                GROUP BY nalog_id
        ";
        $query = $db->prepare($sql);
        $query->execute([":id" => $id]);
        echo json_encode($query->fetchAll(PDO::FETCH_CLASS));

        break;
    case "nalogSvi":

        $nalogUslov     = " ";
        $companyUslov   = " ";
        $statusUslov    = " ";
        $pStatusUslov    = " ";
        $executeParams  = [];

        if (isset($_REQUEST["companyId"])) {
            $company_id = $_REQUEST["companyId"];
            $companyUslov = " AND company_id = :company_id ";
            $executeParams[":company_id"] = $company_id;
        }

        if (isset($_REQUEST["nalogId"])) {
            $nalog_id = $_REQUEST["nalogId"];
            $nalogUslov = "AND nalog_id = :nalog_id ";
            $executeParams[":nalog_id"] = $nalog_id;
        }

        if (isset($_REQUEST["status"])) {
            $status = $_REQUEST["status"];
            $statusUslov = " AND status_nd_kandidata = :status ";

            if($status == 0) {
                $statusUslov = " AND status_nd_kandidata IS NULL";
            }
            else if ($status == 6){
                $statusUslov = " AND (status_nd_kandidata = :status OR kandidat_ima_nostrifikaciju = 1) ";
                $executeParams[":status"] = $status;
            }
            else {
                $statusUslov = " AND (status_nd_kandidata = :status AND (kandidat_ima_nostrifikaciju IS NULL OR kandidat_ima_nostrifikaciju = 0)) ";
                $executeParams[":status"] = $status;
            }
        }

        if (isset($_REQUEST["pstatus"])) {
            $pstatus = $_REQUEST["pstatus"];
            $pStatusUslov = " AND pstatus_nd_kandidata = :pstatus ";
            $executeParams[":pstatus"] = $pstatus;
        }

        $sql = "
                SELECT
                    kandidat_id,
                    company_id,
                    company_name,
                    nalog_id,
                    nalog_naziv,
                    CONCAT(kandidat_ime, ' ', kandidat_prezime) as kandidat_ime_prezime,
                    status_nd_kandidata as status,
                    pstatus_nd_kandidata as pstatus,
                    CASE
                        WHEN (kandidat_ima_nostrifikaciju IS NULL OR kandidat_ima_nostrifikaciju = 0) AND kandidat_dipl_id = 0                                    THEN 'Nije u diplu'
                        WHEN (kandidat_ima_nostrifikaciju IS NULL OR kandidat_ima_nostrifikaciju = 0) AND status_nd_kandidata = 1  AND pstatus_nd_kandidata = 1   THEN 'Lead'
                        WHEN (kandidat_ima_nostrifikaciju IS NULL OR kandidat_ima_nostrifikaciju = 0) AND status_nd_kandidata = 1  AND pstatus_nd_kandidata = 2   THEN 'Neuspješan kontakt 3'
                        WHEN (kandidat_ima_nostrifikaciju IS NULL OR kandidat_ima_nostrifikaciju = 0) AND status_nd_kandidata = 1  AND pstatus_nd_kandidata = 3   THEN 'Zainteresiran Lead'
                        WHEN (kandidat_ima_nostrifikaciju IS NULL OR kandidat_ima_nostrifikaciju = 0) AND status_nd_kandidata = 1  AND pstatus_nd_kandidata = 4   THEN 'Nezainteresiran Lead'
                        WHEN (kandidat_ima_nostrifikaciju IS NULL OR kandidat_ima_nostrifikaciju = 0) AND status_nd_kandidata = 1  AND pstatus_nd_kandidata = 5   THEN 'U obradi Lead'
                        WHEN (kandidat_ima_nostrifikaciju IS NULL OR kandidat_ima_nostrifikaciju = 0) AND status_nd_kandidata = 1  AND pstatus_nd_kandidata = 6   THEN 'Neuspješan kontakt 1'
                        WHEN (kandidat_ima_nostrifikaciju IS NULL OR kandidat_ima_nostrifikaciju = 0) AND status_nd_kandidata = 1  AND pstatus_nd_kandidata = 7   THEN 'Neuspješan Lead 1'
                        WHEN (kandidat_ima_nostrifikaciju IS NULL OR kandidat_ima_nostrifikaciju = 0) AND status_nd_kandidata = 1  AND pstatus_nd_kandidata = 8   THEN 'Neuspješan Lead 2'
                        WHEN (kandidat_ima_nostrifikaciju IS NULL OR kandidat_ima_nostrifikaciju = 0) AND status_nd_kandidata = 1  AND pstatus_nd_kandidata = 9   THEN 'Termin zainteresirani'
                        WHEN (kandidat_ima_nostrifikaciju IS NULL OR kandidat_ima_nostrifikaciju = 0) AND status_nd_kandidata = 1  AND pstatus_nd_kandidata = 10  THEN 'Termin ostali'
                        WHEN (kandidat_ima_nostrifikaciju IS NULL OR kandidat_ima_nostrifikaciju = 0) AND status_nd_kandidata = 1  AND pstatus_nd_kandidata = 11  THEN 'Lead NL'
                        WHEN (kandidat_ima_nostrifikaciju IS NULL OR kandidat_ima_nostrifikaciju = 0) AND status_nd_kandidata = 1  AND pstatus_nd_kandidata = 12  THEN 'Lead NZ'
                        WHEN (kandidat_ima_nostrifikaciju IS NULL OR kandidat_ima_nostrifikaciju = 0) AND status_nd_kandidata = 2                                 THEN 'Prikupljanje dokumentacije'
                        WHEN (kandidat_ima_nostrifikaciju IS NULL OR kandidat_ima_nostrifikaciju = 0) AND status_nd_kandidata = 3                                 THEN 'Poslana pošta'
                        WHEN (kandidat_ima_nostrifikaciju IS NULL OR kandidat_ima_nostrifikaciju = 0) AND status_nd_kandidata = 4                                 THEN 'U obradi'
                        WHEN (kandidat_ima_nostrifikaciju IS NULL OR kandidat_ima_nostrifikaciju = 0) AND status_nd_kandidata = 5                                 THEN 'Dopuna dokumentacije'
                        WHEN (kandidat_ima_nostrifikaciju IS NULL OR kandidat_ima_nostrifikaciju = 0) AND status_nd_kandidata = 6                                 THEN 'Završen'
                        WHEN (kandidat_ima_nostrifikaciju IS NULL OR kandidat_ima_nostrifikaciju = 0) AND status_nd_kandidata = 7                                 THEN 'Arhiv'
                        WHEN kandidat_ima_nostrifikaciju = 1                                                                                                      THEN 'Kandidat već ima nostrifikaciju'
                    END as dipl_status
            FROM
                (
                    SELECT
                         company_id, company_name, nalog_id, nalog_naziv
                         FROM
                         `idk_companies`
                         INNER JOIN `idk_nalozi` ON `idk_companies`.`company_id` = `idk_nalozi`.`kompanija_id`
                         WHERE
                         `idk_nalozi`.`pristup_poslodavcima` = 1
                ) AS companies
            INNER JOIN idk_kandidati ON kandidat_nalog_id = companies.nalog_id
            LEFT JOIN idk_nd_kandidata ON idk_kandidati.kandidat_dipl_id = idk_nd_kandidata.id_broj_nd_kandidata
            WHERE 
                idk_kandidati.kandidat_status_prijave = 9
            $companyUslov
            $nalogUslov
            $statusUslov
            $pStatusUslov
        ";
        $query = $db->prepare($sql);
        $query->execute($executeParams);

        echo json_encode($query->fetchAll(PDO::FETCH_CLASS));
        break;
    default:
        http_response_code(500);
        die("No page specified!");
        break;
}
