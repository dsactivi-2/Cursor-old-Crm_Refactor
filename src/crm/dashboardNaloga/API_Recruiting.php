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
                SELECT
                    company_id,
                    company_name,
                    SUM( IF( kandidat_status_prijave = 1,  qty, 0)) AS slobodan,
                    SUM( IF( kandidat_status_prijave = 2,  qty, 0)) AS u_projektu_nr,
                    SUM( IF( kandidat_status_prijave = 3,  qty, 0)) AS casting,
                    SUM( IF( kandidat_status_prijave = 4,  qty, 0)) AS zaposlen,
                    SUM( IF( kandidat_status_prijave = 5,  qty, 0)) AS odbijen,
                    SUM( IF( kandidat_status_prijave = 6,  qty, 0)) AS u_projektu_rz,
                    SUM( IF( kandidat_status_prijave = 7,  qty, 0)) AS ceka_ugovor,
                    SUM( IF( kandidat_status_prijave = 8,  qty, 0)) AS poslan_ugovor,
                    SUM( IF( kandidat_status_prijave = 9 OR kandidat_status_prijave > 10
                                                        ,  qty, 0)) AS potpisan_ugovor,
                    SUM( IF( kandidat_status_prijave = 10, qty, 0)) AS pocetak_rada
                FROM
                    (SELECT
                        company_id,
                            company_name,
                            kandidat_status_prijave,
                            COUNT(*) AS qty
                    FROM
                        (SELECT
                        company_id, company_name, nalog_id
                    FROM
                        `idk_companies`
                    INNER JOIN `idk_nalozi` ON `idk_companies`.`company_id` = `idk_nalozi`.`kompanija_id`
                    WHERE
                        `idk_nalozi`.`pristup_poslodavcima` = 1
                    AND
                        `idk_nalozi`.`nalog_status` != 12
                    ) AS companies
                    INNER JOIN idk_kandidati ON kandidat_nalog_id = companies.nalog_id
                    WHERE
                        kandidat_status_prijave IS NOT NULL
                    GROUP BY company_id , kandidat_status_prijave) AS res
                GROUP BY company_name
            ";
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
                    nalog_potrebno_kandidata,
                    SUM( IF( kandidat_status_prijave = 1,  qty, 0)) AS slobodan,
                    SUM( IF( kandidat_status_prijave = 2,  qty, 0)) AS u_projektu_nr,
                    SUM( IF( kandidat_status_prijave = 3,  qty, 0)) AS casting,
                    SUM( IF( kandidat_status_prijave = 4,  qty, 0)) AS zaposlen,
                    SUM( IF( kandidat_status_prijave = 5,  qty, 0)) AS odbijen,
                    SUM( IF( kandidat_status_prijave = 6,  qty, 0)) AS u_projektu_rz,
                    SUM( IF( kandidat_status_prijave = 7,  qty, 0)) AS ceka_ugovor,
                    SUM( IF( kandidat_status_prijave = 8,  qty, 0)) AS poslan_ugovor,
                    SUM( IF( kandidat_status_prijave = 9,  qty, 0)) AS potpisan_ugovor,
                    SUM( IF( kandidat_status_prijave = 10, qty, 0)) AS pocetak_rada,
                    SUM( IF( kandidat_status_prijave = 12, qty, 0)) AS prikupljanje_dokumentacije,
                    SUM( IF( kandidat_status_prijave = 15, qty, 0)) AS ceka_termin,
                    SUM( IF( kandidat_status_prijave = 18, qty, 0)) AS ceka_vizu,
                    SUM( IF( kandidat_status_prijave = 21, qty, 0)) AS dopuna_dokumenata,
                    SUM( IF( kandidat_status_prijave = 24, qty, 0)) AS odbijena_viza,
                    SUM( IF( kandidat_status_prijave = 27, qty, 0)) AS dobio_vizu
                FROM
                    (
                    SELECT
                        nalog_id,
                        nalog_naziv,
                        nalog_potrebno_kandidata,
                        kandidat_id,
                        `kandidat_status_prijave`,
                        COUNT(*) AS qty
                    FROM
                        (
                        SELECT
                            `nalog_id`,
                            `nalog_naziv`,
                            `nalog_potrebno_kandidata`
                        FROM
                            `idk_nalozi`
                        WHERE
                            `idk_nalozi`.`kompanija_id` = :id AND `idk_nalozi`.`pristup_poslodavcima` = 1
                    ) AS nalozi
                INNER JOIN `idk_kandidati` ON `idk_kandidati`.`kandidat_nalog_id` = `nalozi`.`nalog_id`
                WHERE
                    `idk_kandidati`.`kandidat_status_prijave` IS NOT NULL
                GROUP BY
                    nalog_id,
                    kandidat_status_prijave
                ) AS res
                GROUP BY
                    nalog_id
        ";
        $query = $db->prepare($sql);
        $query->execute([":id" => $id]);
        echo json_encode($query->fetchAll(PDO::FETCH_CLASS));

        break;
    case "nalogSvi":

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
        $statusPrijaveUslov = " AND TRUE ";
        if (isset($_REQUEST["statusPrijave"])) {
            $statusPrijave = $_REQUEST["statusPrijave"];
            
            $statusPrijaveUslov = $statusPrijaveUslov . " AND (`kandidat_status_prijave` = :status_prijave ";
            if($statusPrijave==21){
               $statusPrijaveUslov=$statusPrijaveUslov." OR kandidat_status_prijave=24 "; 
            }

            if($statusPrijave==9){
               $statusPrijaveUslov=$statusPrijaveUslov." OR kandidat_status_prijave IN (12,15,18,21,24,27) "; 
            }
            $statusPrijaveUslov = $statusPrijaveUslov . ") ";
            $executeParams[":status_prijave"] = $statusPrijave;
        }


        $sql = "
            SELECT
                kandidat_id,
                CONCAT(
                    kandidat_ime,
                    ' ',
                    kandidat_prezime
                ) AS kandidat_ime_prezime,
                kandidat_status_prijave,
                IF(
                    kandidat_potencijalni_pocetak_rada IS NOT NULL,
                    kandidat_potencijalni_pocetak_rada,
                    'Nepoznato'
                ) AS kandidat_potencijalni_pocetak_rada,
                nalog_id,
                nalog_naziv,
                status_naziv,
                MAX(`idk_log_statusi_prijave`.`lsp_datetime`) as na_statusu_od
            FROM
                `idk_nalozi`
            INNER JOIN idk_kandidati ON `idk_kandidati`.`kandidat_nalog_id` = `idk_nalozi`.`nalog_id`
            INNER JOIN `idk_kandidat_status_prijave` ON `idk_kandidati`.`kandidat_status_prijave` = `idk_kandidat_status_prijave`.`status_id`
            LEFT JOIN `idk_log_statusi_prijave` ON `idk_log_statusi_prijave`.`lsp_kandidat_id` = `idk_kandidati`.`kandidat_id` AND `idk_log_statusi_prijave`.`lsp_status_prijave_id` = `idk_kandidati`.`kandidat_status_prijave`
            WHERE
                /*`idk_kandidati`.`kandidat_status_prijave` IN (3,7,8,9,10)
            AND*/
                kandidat_status_prijave IS NOT NULL AND `idk_nalozi`.`pristup_poslodavcima` = 1 "
            . 
                $nalogUslov . $statusPrijaveUslov
            .
                "GROUP BY `idk_kandidati`.`kandidat_id`  
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
