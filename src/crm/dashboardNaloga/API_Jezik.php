<?php
include($_SERVER["DOCUMENT_ROOT"] . "/includes/connect.php");

if (!isset($_REQUEST["page"])) {
    http_response_code(500);
    die("Missing query parameter!");
}

$page = $_REQUEST['page'];
switch ($page) {
    case 'allCompanies':
        $sql = "
                SELECT company_id, company_name, 
                    SUM(IF(kj_slusanje IS NULL, qty, 0)) as nema_potvrdjen_jezik,
                    SUM(IF(kj_slusanje = 'Bez znanja', qty, 0)) as BZ,
                    SUM(IF(kj_slusanje = 'A1' AND podnivo = 1, qty, 0)) as A1_1,
                    SUM(IF(kj_slusanje = 'A1' AND podnivo = 2, qty, 0)) as A1_2,
                    SUM(IF(kj_slusanje = 'A2' AND podnivo = 1, qty, 0)) as A2_1,
                    SUM(IF(kj_slusanje = 'A2' AND podnivo = 2, qty, 0)) as A2_2,
                    SUM(IF(kj_slusanje != 'Bez znanja' AND  kj_slusanje != 'A1' AND  kj_slusanje != 'A2' AND  kj_slusanje IS NOT NULL, qty, 0)) as B
                FROM ( SELECT
                    company_id,
                    company_name,
                    kj_slusanje,
                    IF(cvl_status=3, 1, 2) as podnivo,
                    COUNT(*) as qty
                FROM
                        (
                        SELECT
                            company_id,
                            company_name,
                            nalog_id
                        FROM
                            `idk_companies`
                        INNER JOIN `idk_nalozi` ON `idk_companies`.`company_id` = `idk_nalozi`.`kompanija_id`
                        WHERE
                            `idk_nalozi`.`pristup_poslodavcima` = 1
                    ) AS companies
                    INNER JOIN idk_kandidati ON kandidat_nalog_id = companies.nalog_id
                    LEFT JOIN 
                    (SELECT * FROM idk_kandidat_jezici INNER JOIN idk_candidate_verified_languages ON idk_kandidat_jezici.kj_id = idk_candidate_verified_languages.cvl_id AND cvl_active = 1) as aktivni_jezici ON aktivni_jezici.kj_kandidatid = idk_kandidati.kandidat_id
                    WHERE kandidat_status_prijave = 9 
                    GROUP BY company_id, company_name, kj_slusanje, podnivo ) as res GROUP BY company_id
            ";
        $res = $db->query($sql)->fetchAll(PDO::FETCH_CLASS);
        echo json_encode($res);
        //echo json_encode([]);

        break;
    case 'company':
        if (!isset($_REQUEST["companyId"])) {
            http_response_code(500);
            die("Missing query parameter!");
        }
        $id = $_REQUEST["companyId"];


        $sql = "
                SELECT nalog_id, nalog_naziv, 
                    SUM(IF(kj_slusanje IS NULL, qty, 0)) as nema_potvrdjen_jezik,
                    SUM(IF(kj_slusanje = 'Bez znanja', qty, 0)) as BZ,
                    SUM(IF(kj_slusanje = 'A1' AND podnivo = 1, qty, 0)) as A1_1,
                    SUM(IF(kj_slusanje = 'A1' AND podnivo = 2, qty, 0)) as A1_2,
                    SUM(IF(kj_slusanje = 'A2' AND podnivo = 1, qty, 0)) as A2_1,
                    SUM(IF(kj_slusanje = 'A2' AND podnivo = 2, qty, 0)) as A2_2,
                    SUM(IF(kj_slusanje != 'Bez znanja' AND  kj_slusanje != 'A1' AND  kj_slusanje != 'A2' AND  kj_slusanje IS NOT NULL, qty, 0)) as B
                FROM ( SELECT
                    nalog_id,
                    nalog_naziv,
                    kj_slusanje,
                    IF(cvl_status=3, 1, 2) as podnivo,
                    COUNT(*) as qty
                FROM
                    (SELECT * FROM `idk_nalozi`
                        WHERE
                            `idk_nalozi`.`pristup_poslodavcima` = 1
                        AND `idk_nalozi`.`kompanija_id` = :id
                    ) AS nalozi
                    INNER JOIN idk_kandidati ON kandidat_nalog_id = nalozi.nalog_id
                    LEFT JOIN 
                    (SELECT * FROM idk_kandidat_jezici INNER JOIN idk_candidate_verified_languages ON idk_kandidat_jezici.kj_id = idk_candidate_verified_languages.cvl_id AND cvl_active = 1) as aktivni_jezici ON aktivni_jezici.kj_kandidatid = idk_kandidati.kandidat_id
                    WHERE kandidat_status_prijave = 9 
                    GROUP BY nalog_id, nalog_naziv, kj_slusanje, podnivo ) as res GROUP BY nalog_id
        ";
        $query = $db->prepare($sql);
        $query->execute([":id" => $id]);
        echo json_encode($query->fetchAll(PDO::FETCH_CLASS));

        break;
    case "nalogSvi":
        /*  BENJO:
                Ovdje ti prosljeđujem ili company_id ili nalog_id (jedno od to dvoje)
                i mozda status_id.
                Trebaš mi vratiti sve kandidate iz te kompanije/tog naloga koji su na
                proslijeđenom statusu jezika.

                Može se desiti da ti proslijedim samo nalog_id, bez id-a statusa.
                Tada mi vraćaš sve kandidate za taj nalog i za svakog kandidata obavezno njegov
                status jezika.
        */

        $nalogUslov = " AND TRUE ";
        $executeParams = [];

        if (isset($_REQUEST["companyId"])) {
            $company_id = $_REQUEST["companyId"];
            $nalogUslov = $nalogUslov . "AND `idk_nalozi`.`kompanija_id` = :company_id ";
            $executeParams[":company_id"] = $company_id;
        }

        if (isset($_REQUEST["nalogId"])) {
            $nalog_id = $_REQUEST["nalogId"];
            $nalogUslov = $nalogUslov . "AND `idk_nalozi`.`nalog_id` = :nalog_id ";
            $executeParams[":nalog_id"] = $nalog_id;
        }

        // Ako se traži određeni status prijave, ovo je njegov WHERE clause za SQL. Po defaultu
        // je true, što će vratiti sve kandidate.
        $jezikUslov = " WHERE status_jezika IS NOT NULL ";
        if (isset($_REQUEST["statusJezika"])) {
            $statusJezika = $_REQUEST["statusJezika"];

            switch($statusJezika)
            {
                case "BZ":
                    $jezikUslov = "$jezikUslov AND status_jezika = 'Bez znanja'";
                    break;
                case "B":
                    $jezikUslov = "$jezikUslov AND status_jezika NOT IN ('Bez znanja', 'A1', 'A2') ";
                    break;
                case "nema_potvrdjen_jezik":
                    $jezikUslov = " WHERE status_jezika IS NULL ";
                    break;
                default:
                    $nivoi = explode("_", $statusJezika);
                    $jezikUslov = "$jezikUslov AND status_jezika = '{$nivoi[0]}' AND podnivo = {$nivoi[1]} ";
                    break;
            }
        }

        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "
                SELECT * FROM (
                    SELECT
                        kandidat_id,
                        company_id,
                        company_name,
                        nalog_id,
                        nalog_naziv,
                        CONCAT(kandidat_ime, ' ', kandidat_prezime) as kandidat_ime_prezime,
                        kj_slusanje as status_jezika,
                        /*IF(cvl_status = 3, 1, 2) AS podnivo,*/
                        CASE
                            WHEN    cvl_status  = 3 AND kj_slusanje IS NOT NULL AND kj_slusanje != 'Bez znanja'     THEN 1
                            WHEN    cvl_status != 3 AND kj_slusanje IS NOT NULL AND kj_slusanje != 'Bez znanja'     THEN 2
                            WHEN    kj_slusanje = NULL                                                              THEN ' '
                        END
                        as podnivo
                    FROM
                        (
                        SELECT
                            company_id,
                            company_name,
                            nalog_id,
                            nalog_naziv
                        FROM
                            `idk_companies`
                        INNER JOIN `idk_nalozi` ON `idk_companies`.`company_id` = `idk_nalozi`.`kompanija_id`
                        WHERE
                            `idk_nalozi`.`pristup_poslodavcima` = 1
                        $nalogUslov
                    ) AS companies
                    INNER JOIN idk_kandidati ON kandidat_nalog_id = companies.nalog_id
                    LEFT JOIN(
                        SELECT
                            *
                        FROM
                            idk_kandidat_jezici
                        INNER JOIN idk_candidate_verified_languages ON idk_kandidat_jezici.kj_id = idk_candidate_verified_languages.cvl_id AND cvl_active = 1
                    ) AS aktivni_jezici
                    ON
                        aktivni_jezici.kj_kandidatid = idk_kandidati.kandidat_id
                    WHERE
                        kandidat_status_prijave = 9  
                ) as res
            $jezikUslov
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
