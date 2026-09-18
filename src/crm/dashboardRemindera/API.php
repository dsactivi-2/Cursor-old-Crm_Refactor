<?php
include($_SERVER["DOCUMENT_ROOT"] . "/includes/connect.php");

if (!isset($_REQUEST["page"])) {
    http_response_code(500);
    die("Missing query parameter!");
}

$page = $_REQUEST['page'];

$whereTypes1 = "";
$whereTypes2 = "";
if(isset($_REQUEST["types"]))
{
    $whereTypes1 = " 
        AND 
            prs.prs_reminder_type_id IN (" . implode(",",$_REQUEST["types"]) . ")
    ";
    $whereTypes2 = "
        AND 
            prs1.prs_reminder_type_id IN (" . implode(",",$_REQUEST["types"]) . ")
    ";
}

$reminderVisibility1 = "";
$reminderVisibility2 = "";
$visibility = 0;
if(isset($_REQUEST["visibility"]))
{
    $visibility = $_REQUEST["visibility"];
    if($_REQUEST["visibility"] == 1){
        $reminderVisibility1 = " 
            AND 
                prt.prt_user_type = 3 
            AND 
                FIND_IN_SET('".$_REQUEST["employee_id"]."', prs.prs_pua_ids) > 0
        ";
        $reminderVisibility2 = " 
            AND 
                prt1.prt_user_type = 3 
            AND 
                FIND_IN_SET('".$_REQUEST["employee_id"]."', prs1.prs_pua_ids) > 0
        ";
    }else if($_REQUEST["visibility"] == 2){
        $reminderVisibility1 = "
            AND 
                prt.prt_user_type IN (1,2)
        ";
        $reminderVisibility2 = "
            AND 
                prt1.prt_user_type IN (1,2)
        ";
    }else if($_REQUEST["visibility"] == 3){
        $reminderVisibility1 = "
            AND 
                prt.prt_user_type = 3
        ";
        $reminderVisibility2 = "
            AND 
                prt1.prt_user_type = 3
        ";
    }
}

$reminderStatus1 = "";
$reminderStatus2 = "";

if (isset($_REQUEST["status"])){

    $statusExp = array();
    $statusImp = "";

    if(in_array("0",$_REQUEST["status"])){ 
        array_push($statusExp,1,2);
    } 
    if (in_array("1",$_REQUEST["status"])) {
        array_push($statusExp,3);
    } 
    if (in_array("2",$_REQUEST["status"])){
        array_push($statusExp,5);
    }

    if (count($statusExp) > 0) {
        $statusImp = implode(",", $statusExp);
        if ($statusImp != "") {
            $reminderStatus1 = "AND pr.pr_status IN (".$statusImp.")";
            $reminderStatus2 = "AND pr1.pr_status IN (".$statusImp.")";
        }
    }

    unset($statusExp);

}

switch ($page) {
    case 'allCompanies':
        $sql = "
            SELECT 
                allRemindersInfo.company_id,
                allRemindersInfo.company_name,
                ".$visibility." AS visibility,
                SUM(
                    CASE
                        WHEN allRemindersInfo.prLevel = 1 THEN 1 ELSE 0
                    END
                ) AS level1,
                SUM(
                    CASE
                        WHEN allRemindersInfo.prLevel = 2 THEN 1 ELSE 0
                    END
                ) AS level2,
                SUM(
                    CASE
                        WHEN allRemindersInfo.prLevel = 3 THEN 1 ELSE 0
                    END
                ) AS level3,
                SUM(
                    CASE
                        WHEN allRemindersInfo.prLevel = 4 THEN 1 ELSE 0
                    END
                ) AS level4,
                SUM(
                    CASE
                        WHEN allRemindersInfo.prLevel >= 5 THEN 1 ELSE 0
                    END
                ) AS level5plus
            FROM 
                (
                    SELECT 
                        c.company_id,
                        c.company_name,
                        n.nalog_id,
                        n.nalog_naziv,
                        allReminders.*
                    FROM 
                        idk_nalozi n 
                    JOIN 
                        idk_companies c
                    ON 
                        c.company_id = n.kompanija_id
                    JOIN 
                        (
                            SELECT 
                                pr.pr_id AS prId,
                                pr.pr_reminder_setting_id AS prSettingId,
                                pr.pr_candidate_id AS prCandidateId, 
                                pr.pr_level AS prLevel, 
                                pr.pr_date_sent AS prDateSent, 
                                pr.pr_users_sent AS prUsersSent, 
                                pr.pr_user_assigned AS prUserAssigned, 
                                pr.pr_done_by_crm_employee AS prDoneCrmEmployee, 
                                pr.pr_date_assigned AS prDateAssigned, 
                                pr.pr_date_completed AS prDateCompleted,
                                pr.pr_status AS prStatus,
            
                                prs.prs_id AS prsId,
                                prs.prs_nalog_id AS prsNalogId, 
                                prs.prs_partner_id AS prsPartnerId,
                                prs.prs_reminder_type_id AS prsTypeId,
                                prs.prs_active AS prsActive, 
                                prs.prs_first_trigger_date AS prsFirstTriggerDate, 
                                prs.prs_create_after AS prsCreateAfter, 
                                
                                prt.prt_id AS prtId,
                                prt.prt_user_type AS prtUserType, 
                                prt.prt_day_freq AS prtDayFreq, 
                                prt.prt_has_documents AS prtHasDocuments,
                                
                                NULL AS prdId,
                                NULL AS prdSettingId,
                                NULL AS prdNrdId, 
                                NULL AS prdCrdId,
                                NULL AS prdDayFreq, 
                                NULL AS prdCreateAfter, 
                                NULL AS prdActive
                                
                            FROM 
                                idk_pp_reminders pr 
                            JOIN 
                                idk_pp_reminder_settings prs
                            ON 
                                prs.prs_id = pr.pr_reminder_setting_id
                                AND 
                                prs.prs_active = 1
                            JOIN 
                                idk_pp_reminder_types prt 
                            ON 
                                prt.prt_id = prs.prs_reminder_type_id 
                                AND 
                                prt.prt_has_documents = 0
                            WHERE 
                                pr.pr_status != 4
                                ".$reminderVisibility1."
                                ".$whereTypes1."
                                ".$reminderStatus1."
                            UNION 
                            
                            SELECT 
                            
                                pr1.pr_id AS prId,
                                pr1.pr_reminder_document_id  AS prSettingId,
                                pr1.pr_candidate_id AS prCandidateId, 
                                pr1.pr_level AS prLevel, 
                                pr1.pr_date_sent AS prDateSent, 
                                pr1.pr_users_sent AS prUsersSent, 
                                pr1.pr_user_assigned AS prUserAssigned, 
                                pr1.pr_done_by_crm_employee AS prDoneCrmEmployee, 
                                pr1.pr_date_assigned AS prDateAssigned, 
                                pr1.pr_date_completed AS prDateCompleted,
                                pr1.pr_status AS prStatus,
            
                                prs1.prs_id AS prsId,
                                prs1.prs_nalog_id AS prsNalogId, 
                                prs1.prs_partner_id AS prsPartnerId,
                                prs1.prs_reminder_type_id AS prsTypeId,
                                prs1.prs_active AS prsActive, 
                                prs1.prs_first_trigger_date AS prsFirstTriggerDate, 
                                prs1.prs_create_after AS prsCreateAfter, 
                                
                                prt1.prt_id AS prtId,
                                prt1.prt_user_type AS prtUserType, 
                                prt1.prt_day_freq AS prtDayFreq, 
                                prt1.prt_has_documents AS prtHasDocuments,
                                
                                prd1.prd_id AS prdId,
                                prd1.prd_prs_id AS prdSettingId,
                                prd1.prd_nrd_id AS prdNrdId, 
                                prd1.prd_crd_id AS prdCrdId,
                                prd1.prd_day_freq AS prdDayFreq, 
                                prd1.prd_create_after AS prdCreateAfter, 
                                prd1.prd_active AS prdActive
                                
                            FROM 
                                idk_pp_reminders pr1 
                            JOIN 
                                idk_pp_reminder_documents prd1
                            ON 
                                prd1.prd_id = pr1.pr_reminder_document_id
                            JOIN 
                                idk_pp_reminder_settings prs1
                            ON 
                                prs1.prs_id = prd1.prd_prs_id
                                AND 
                                prs1.prs_active = 1
                                AND 
                                prd1.prd_active = 1
                            JOIN 
                                idk_pp_reminder_types prt1 
                            ON 
                                prt1.prt_id = prs1.prs_reminder_type_id 
                                AND 
                                prt1.prt_has_documents = 1
                            WHERE 
                                pr1.pr_status != 4
                                ".$reminderVisibility2."
                                ".$whereTypes2."
                                ".$reminderStatus2."
                        )
                        AS 
                        allReminders
                    ON 
                        n.nalog_id = allReminders.prsNalogId
                )
                AS 
                allRemindersInfo
            GROUP BY 
                allRemindersInfo.company_id
        ";
        
        /*$sql = " SELECT
                    company_id,
                    company_name,
                    ".$visibility." AS visibility,
                    SUM(
                        IF(poslanPuta = 1, levelCount, 0)
                    ) AS level1,
                    SUM(
                        IF(poslanPuta = 2, levelCount, 0)
                    ) AS level2,
                    SUM(
                        IF(poslanPuta = 3, levelCount, 0)
                    ) AS level3,
                    SUM(
                        IF(poslanPuta = 4, levelCount, 0)
                    ) AS level4,
                    SUM(
                        IF(poslanPuta >= 5, levelCount, 0)
                    ) AS level5plus
                FROM
                    (
                    SELECT
                        company_id,
                        company_name,
                        poslanPuta,
                        COUNT(*) AS levelCount
                    FROM
                        (
                        SELECT
                            *,
                            COUNT(*) AS poslanPuta
                        FROM
                            (
                            SELECT
                                activeReminders.pr_reminder_setting_id,
                                activeReminders.pr_candidate_id,
                                activeReminders.company_name,
                                activeReminders.company_id
                            FROM
                                (
                                SELECT
                                    *
                                FROM
                                    (
                                    SELECT
                                        *
                                    FROM
                                        (
                                        SELECT
                                            *
                                        FROM
                                            (
                                            SELECT
                                                *
                                            FROM
                                                `idk_pp_reminders`
                                        ) AS reminders
                                    JOIN `idk_pp_reminder_settings` ON reminders.pr_reminder_setting_id = `idk_pp_reminder_settings`.`prs_id` $whereTypes
                                    ) AS remindersWithSettings
                                INNER JOIN `idk_nalozi` ON remindersWithSettings.prs_nalog_id = `idk_nalozi`.`nalog_id`
                                INNER JOIN `idk_companies` ON `idk_companies`.`company_id` = `idk_nalozi`.`kompanija_id`
                                JOIN `idk_pp_reminder_types` ON `idk_pp_reminder_types`.`prt_id` = remindersWithSettings.prs_reminder_type_id
                                ) AS remindersWithCompany
                            WHERE
                                pr_status NOT IN(3, 4) $reminderVisibility
                            ) AS activeReminders
                        INNER JOIN `idk_pp_reminders` ON `idk_pp_reminders`.`pr_reminder_setting_id` = activeReminders.`pr_reminder_setting_id` AND `idk_pp_reminders`.`pr_candidate_id` = activeReminders.`pr_candidate_id`
                        ) AS joinedReminders
                    GROUP BY
                        pr_reminder_setting_id,
                        pr_candidate_id
                    ) AS remindersSentTimes
                GROUP BY
                    poslanPuta,
                    company_name
                ) AS levelsCounted
                GROUP BY
                    company_name
        ";*/
        $res = $db->query($sql)->fetchAll(PDO::FETCH_CLASS);
        echo json_encode($res);

        break;
    case 'company':
        if (!isset($_REQUEST["companyId"])) {
            http_response_code(500);
            die("Missing query parameter!");
        }
        $id = $_REQUEST["companyId"];
        $sql = "
            SELECT 
                allRemindersInfo.nalog_id,
                allRemindersInfo.nalog_naziv,
                ".$visibility." AS visibility,
                SUM(
                    CASE
                        WHEN allRemindersInfo.prLevel = 1 THEN 1 ELSE 0
                    END
                ) AS level1,
                SUM(
                    CASE
                        WHEN allRemindersInfo.prLevel = 2 THEN 1 ELSE 0
                    END
                ) AS level2,
                SUM(
                    CASE
                        WHEN allRemindersInfo.prLevel = 3 THEN 1 ELSE 0
                    END
                ) AS level3,
                SUM(
                    CASE
                        WHEN allRemindersInfo.prLevel = 4 THEN 1 ELSE 0
                    END
                ) AS level4,
                SUM(
                    CASE
                        WHEN allRemindersInfo.prLevel >= 5 THEN 1 ELSE 0
                    END
                ) AS level5plus
            FROM 
                (
                    SELECT 
                        c.company_id,
                        c.company_name,
                        n.nalog_id,
                        n.nalog_naziv,
                        allReminders.*
                    FROM 
                        idk_nalozi n 
                    JOIN 
                        idk_companies c
                    ON 
                        c.company_id = n.kompanija_id
                    JOIN 
                        (
                            SELECT 
                                pr.pr_id AS prId,
                                pr.pr_reminder_setting_id AS prSettingId,
                                pr.pr_candidate_id AS prCandidateId, 
                                pr.pr_level AS prLevel, 
                                pr.pr_date_sent AS prDateSent, 
                                pr.pr_users_sent AS prUsersSent, 
                                pr.pr_user_assigned AS prUserAssigned, 
                                pr.pr_done_by_crm_employee AS prDoneCrmEmployee, 
                                pr.pr_date_assigned AS prDateAssigned, 
                                pr.pr_date_completed AS prDateCompleted,
                                pr.pr_status AS prStatus,
            
                                prs.prs_id AS prsId,
                                prs.prs_nalog_id AS prsNalogId, 
                                prs.prs_partner_id AS prsPartnerId,
                                prs.prs_reminder_type_id AS prsTypeId,
                                prs.prs_active AS prsActive, 
                                prs.prs_first_trigger_date AS prsFirstTriggerDate, 
                                prs.prs_create_after AS prsCreateAfter, 
                                
                                prt.prt_id AS prtId,
                                prt.prt_user_type AS prtUserType, 
                                prt.prt_day_freq AS prtDayFreq, 
                                prt.prt_has_documents AS prtHasDocuments,
                                
                                NULL AS prdId,
                                NULL AS prdSettingId,
                                NULL AS prdNrdId, 
                                NULL AS prdCrdId,
                                NULL AS prdDayFreq, 
                                NULL AS prdCreateAfter, 
                                NULL AS prdActive
                                
                            FROM 
                                idk_pp_reminders pr 
                            JOIN 
                                idk_pp_reminder_settings prs
                            ON 
                                prs.prs_id = pr.pr_reminder_setting_id
                                AND 
                                prs.prs_active = 1
                            JOIN 
                                idk_pp_reminder_types prt 
                            ON 
                                prt.prt_id = prs.prs_reminder_type_id 
                                AND 
                                prt.prt_has_documents = 0
                            WHERE 
                                pr.pr_status != 4
                                ".$reminderVisibility1."
                                ".$whereTypes1."
                                ".$reminderStatus1."

                            UNION 
                            
                            SELECT 
                            
                                pr1.pr_id AS prId,
                                pr1.pr_reminder_document_id AS prSettingId,
                                pr1.pr_candidate_id AS prCandidateId, 
                                pr1.pr_level AS prLevel, 
                                pr1.pr_date_sent AS prDateSent, 
                                pr1.pr_users_sent AS prUsersSent, 
                                pr1.pr_user_assigned AS prUserAssigned, 
                                pr1.pr_done_by_crm_employee AS prDoneCrmEmployee, 
                                pr1.pr_date_assigned AS prDateAssigned, 
                                pr1.pr_date_completed AS prDateCompleted,
                                pr1.pr_status AS prStatus,
            
                                prs1.prs_id AS prsId,
                                prs1.prs_nalog_id AS prsNalogId, 
                                prs1.prs_partner_id AS prsPartnerId,
                                prs1.prs_reminder_type_id AS prsTypeId,
                                prs1.prs_active AS prsActive, 
                                prs1.prs_first_trigger_date AS prsFirstTriggerDate, 
                                prs1.prs_create_after AS prsCreateAfter, 
                                
                                prt1.prt_id AS prtId,
                                prt1.prt_user_type AS prtUserType, 
                                prt1.prt_day_freq AS prtDayFreq, 
                                prt1.prt_has_documents AS prtHasDocuments,
                                
                                prd1.prd_id AS prdId,
                                prd1.prd_prs_id AS prdSettingId,
                                prd1.prd_nrd_id AS prdNrdId, 
                                prd1.prd_crd_id AS prdCrdId,
                                prd1.prd_day_freq AS prdDayFreq, 
                                prd1.prd_create_after AS prdCreateAfter, 
                                prd1.prd_active AS prdActive
                                
                            FROM 
                                idk_pp_reminders pr1 
                            JOIN 
                                idk_pp_reminder_documents prd1
                            ON 
                                prd1.prd_id = pr1.pr_reminder_document_id
                            JOIN 
                                idk_pp_reminder_settings prs1
                            ON 
                                prs1.prs_id = prd1.prd_prs_id
                                AND 
                                prs1.prs_active = 1
                                AND 
                                prd1.prd_active = 1
                            JOIN 
                                idk_pp_reminder_types prt1 
                            ON 
                                prt1.prt_id = prs1.prs_reminder_type_id 
                                AND 
                                prt1.prt_has_documents = 1
                            WHERE 
                                pr1.pr_status != 4
                                ".$reminderVisibility2."
                                ".$whereTypes2."
                                ".$reminderStatus2."
                        )
                        AS 
                        allReminders
                    ON 
                        n.nalog_id = allReminders.prsNalogId
                    WHERE 
                        c.company_id = :id
                )
                AS 
                allRemindersInfo
            GROUP BY 
                allRemindersInfo.nalog_id
        ";
        /*$sql = "SELECT
                nalog_id,
                nalog_naziv,
                ".$visibility." AS visibility,
                SUM(
                    IF(poslanPuta = 1, levelCount, 0)
                ) AS level1,
                SUM(
                    IF(poslanPuta = 2, levelCount, 0)
                ) AS level2,
                SUM(
                    IF(poslanPuta = 3, levelCount, 0)
                ) AS level3,
                SUM(
                    IF(poslanPuta = 4, levelCount, 0)
                ) AS level4,
                SUM(
                    IF(poslanPuta > 5, levelCount, 0)
                ) AS level5plus
            FROM
            (
                SELECT
                    company_name,
                    nalog_naziv,
                    nalog_id,
                    poslanPuta,
                    COUNT(*) AS levelCount
                FROM
            (
                SELECT
                    *,
                    COUNT(*) AS poslanPuta
                FROM
            (
                SELECT
                    activeReminders.pr_reminder_setting_id,
                    activeReminders.pr_candidate_id,
                    activeReminders.company_name,
                    activeReminders.nalog_naziv,
                    activeReminders.nalog_id
                FROM
            (
                SELECT
                    *
                FROM
            (
                SELECT
                    *
                FROM
            (
                SELECT
                    *
                FROM
            (
            SELECT
                *
            FROM
                `idk_pp_reminders`
            ) as reminders 
                JOIN `idk_pp_reminder_settings` ON reminders.pr_reminder_setting_id = `idk_pp_reminder_settings`.`prs_id`
                JOIN `idk_pp_reminder_types` ON `idk_pp_reminder_types`.`prt_id` = idk_pp_reminder_settings.prs_reminder_type_id
                $whereTypes
            )as remindersWithSettings
                INNER JOIN `idk_nalozi` ON remindersWithSettings.prs_nalog_id = `idk_nalozi`.`nalog_id`
                INNER JOIN `idk_companies` ON `idk_companies`.`company_id` = `idk_nalozi`.`kompanija_id`
                WHERE
                    `idk_companies`.`company_id` = :id
            )as remindersWithCompany 
                WHERE
                    pr_status NOT IN(3, 4) $reminderVisibility
            )as activeReminders
                INNER JOIN `idk_pp_reminders` ON `idk_pp_reminders`.`pr_reminder_setting_id` = activeReminders.`pr_reminder_setting_id` AND `idk_pp_reminders`.`pr_candidate_id` = activeReminders.`pr_candidate_id`
            )as joinedReminders 
                GROUP BY
                    pr_reminder_setting_id,
                    pr_candidate_id
            )as remindersSentTimes
                GROUP BY
                    poslanPuta,
                    company_name,
                    nalog_id
            )as levelsCounted 
            GROUP BY
                nalog_naziv
        ";*/
        $query = $db->prepare($sql);
        $query->execute([":id" => $id]);
        echo json_encode($query->fetchAll(PDO::FETCH_CLASS));

        break;
    case "kandidati":

        $nalogUslov = "TRUE ";
        $executeParams = [];

        if (isset($_REQUEST["companyId"]))
        {
            $company_id = $_REQUEST["companyId"];
            $nalogUslov = $nalogUslov . "AND c.company_id = :company_id ";
            $executeParams[":company_id"] = $company_id;
        }

        if (isset($_REQUEST["nalogId"]))
        {
            $nalog_id = $_REQUEST["nalogId"];
            $nalogUslov = $nalogUslov . "AND n.nalog_id = :nalog_id ";
            $executeParams[":nalog_id"] = $nalog_id;
        }

        if (isset($_REQUEST["poslanPuta"]))
        {
            if($_REQUEST["poslanPuta"] == 5)
            {
                $poslan_puta = $_REQUEST["poslanPuta"];
                $nalogUslov = $nalogUslov . "AND allReminders.prLevel >= :poslan_puta ";
                $executeParams[":poslan_puta"] = $poslan_puta;
            }
            else
            {
                $poslan_puta = $_REQUEST["poslanPuta"];
                $nalogUslov = $nalogUslov . "AND allReminders.prLevel = :poslan_puta ";
                $executeParams[":poslan_puta"] = $poslan_puta;
            }
        }       

        $sql = "
            SELECT 
                kan.kandidat_id AS kandidat_id,
                CONCAT(kan.kandidat_ime,' ',kan.kandidat_prezime) AS kandidat_ime_prezime,
                (
                	CASE
                        WHEN allReminders.prtHasDocuments = 1 AND allReminders.nrdName is not null AND allReminders.crdName is null THEN CONCAT(allReminders.prtName, ' - ' , allReminders.nrdName)
                        WHEN allReminders.prtHasDocuments = 1 AND allReminders.nrdName is null AND allReminders.crdName is not null THEN CONCAT(allReminders.prtName, ' - ' , allReminders.crdName)
                        ELSE allReminders.prtName
                    END
                ) AS prt_name,
                n.nalog_naziv,
                allReminders.prSettingId AS pr_reminder_setting_id,
                allReminders.prtHasDocuments,
                allReminders.prCandidateId AS pr_candidate_id, 
                (
                    CASE 
                        WHEN allReminders.prStatus = 1 THEN 'Poslan'
                        WHEN allReminders.prStatus = 2 THEN 'Prihvaćen'
                        WHEN allReminders.prStatus = 3 THEN 'Završen (Normalno)'
                        WHEN allReminders.prStatus = 4 THEN 'Neizvršen'
                        WHEN allReminders.prStatus = 5 THEN 'Završen (Odustankom)'
                        ELSE 'Nedefinisan'
                    END
                ) AS reminderStatus,
                allReminders.prLevel, 
                allReminders.prId,
                (
                    CASE 
                        WHEN allReminders.prLevel = 1 THEN '1'
                        WHEN allReminders.prLevel = 2 THEN '2'
                        WHEN allReminders.prLevel = 3 THEN '3'
                        WHEN allReminders.prLevel = 4 THEN '4'
                        ELSE '5+'
                    END
                ) AS reminderLevel
            FROM 
                idk_nalozi n 
            JOIN 
                idk_companies c
            ON 
                c.company_id = n.kompanija_id
            JOIN 
                (
                    SELECT 
                        pr.pr_id AS prId,
                        pr.pr_reminder_setting_id AS prSettingId,
                        pr.pr_candidate_id AS prCandidateId, 
                        pr.pr_level AS prLevel, 
                        pr.pr_date_sent AS prDateSent, 
                        pr.pr_users_sent AS prUsersSent, 
                        pr.pr_user_assigned AS prUserAssigned, 
                        pr.pr_done_by_crm_employee AS prDoneCrmEmployee, 
                        pr.pr_date_assigned AS prDateAssigned, 
                        pr.pr_date_completed AS prDateCompleted,
                        pr.pr_status AS prStatus,
            
                        prs.prs_id AS prsId,
                        prs.prs_nalog_id AS prsNalogId, 
                        prs.prs_partner_id AS prsPartnerId,
                        prs.prs_reminder_type_id AS prsTypeId,
                        prs.prs_active AS prsActive, 
                        prs.prs_first_trigger_date AS prsFirstTriggerDate, 
                        prs.prs_create_after AS prsCreateAfter, 
                        
                        prt.prt_id AS prtId,
                        prt.prt_name AS prtName,
                        prt.prt_user_type AS prtUserType, 
                        prt.prt_day_freq AS prtDayFreq, 
                        prt.prt_has_documents AS prtHasDocuments,
                        
                        NULL AS prdId,
                        NULL AS prdSettingId,
                        NULL AS prdNrdId, 
                        NULL AS prdCrdId,
                        NULL AS prdDayFreq, 
                        NULL AS prdCreateAfter, 
                        NULL AS prdActive,

                        NULL AS nrdName,
                        NULL AS crdName
                        
                    FROM 
                        idk_pp_reminders pr 
                    JOIN 
                        idk_pp_reminder_settings prs
                    ON 
                        prs.prs_id = pr.pr_reminder_setting_id
                        AND 
                        prs.prs_active = 1
                    JOIN 
                        idk_pp_reminder_types prt 
                    ON 
                        prt.prt_id = prs.prs_reminder_type_id 
                        AND 
                        prt.prt_has_documents = 0
                    WHERE 
                        pr.pr_status != 4
                        ".$reminderVisibility1."
                        ".$whereTypes1."
                        ".$reminderStatus1."
                    
                    UNION 
                    
                    SELECT 
                    
                        pr1.pr_id AS prId,
                        pr1.pr_reminder_document_id AS prSettingId,
                        pr1.pr_candidate_id AS prCandidateId, 
                        pr1.pr_level AS prLevel, 
                        pr1.pr_date_sent AS prDateSent, 
                        pr1.pr_users_sent AS prUsersSent, 
                        pr1.pr_user_assigned AS prUserAssigned, 
                        pr1.pr_done_by_crm_employee AS prDoneCrmEmployee, 
                        pr1.pr_date_assigned AS prDateAssigned, 
                        pr1.pr_date_completed AS prDateCompleted,
                        pr1.pr_status AS prStatus,
            
                        prs1.prs_id AS prsId,
                        prs1.prs_nalog_id AS prsNalogId, 
                        prs1.prs_partner_id AS prsPartnerId,
                        prs1.prs_reminder_type_id AS prsTypeId,
                        prs1.prs_active AS prsActive, 
                        prs1.prs_first_trigger_date AS prsFirstTriggerDate, 
                        prs1.prs_create_after AS prsCreateAfter, 
                        
                        prt1.prt_id AS prtId,
                        prt1.prt_name AS prtName,
                        prt1.prt_user_type AS prtUserType, 
                        prt1.prt_day_freq AS prtDayFreq, 
                        prt1.prt_has_documents AS prtHasDocuments,
                        
                        prd1.prd_id AS prdId,
                        prd1.prd_prs_id AS prdSettingId,
                        prd1.prd_nrd_id AS prdNrdId, 
                        prd1.prd_crd_id AS prdCrdId,
                        prd1.prd_day_freq AS prdDayFreq, 
                        prd1.prd_create_after AS prdCreateAfter, 
                        prd1.prd_active AS prdActive,

                        pdt1.doc_type_name AS nrdName,
                        pdt2.doc_type_name AS crdName
                        
                    FROM 
                        idk_pp_reminders pr1 
                    JOIN 
                        idk_pp_reminder_documents prd1
                    ON 
                        prd1.prd_id = pr1.pr_reminder_document_id
                    JOIN 
                        idk_pp_reminder_settings prs1
                    ON 
                        prs1.prs_id = prd1.prd_prs_id
                        AND 
                        prs1.prs_active = 1
                        AND 
                        prd1.prd_active = 1
                    JOIN 
                        idk_pp_reminder_types prt1 
                    ON 
                        prt1.prt_id = prs1.prs_reminder_type_id 
                        AND 
                        prt1.prt_has_documents = 1
                    LEFT JOIN 
                        idk_pp_nalog_required_documents nrd1
                    ON 
                        nrd1.nrd_id = prd1.prd_nrd_id
                    LEFT JOIN 
                        idk_pp_document_types pdt1
                    ON 
                        pdt1.doc_type_id = nrd1.nrd_type_id
                    LEFT JOIN 
                        idk_pp_cand_required_documents crd1
                    ON 
                        crd1.crd_id = prd1.prd_crd_id
                    LEFT JOIN 
                        idk_pp_document_types pdt2
                    ON 
                        pdt2.doc_type_id = crd1.crd_type_id
                    WHERE 
                        pr1.pr_status != 4
                        ".$reminderVisibility2."
                        ".$whereTypes2."
                        ".$reminderStatus2."
                )
                AS 
                allReminders
            ON 
                n.nalog_id = allReminders.prsNalogId
            JOIN 
                idk_kandidati kan 
            ON 
                kan.kandidat_id = allReminders.prCandidateId
            WHERE 
                ".$nalogUslov."
        ";
        //echo $sql;

        /*$sql = "SELECT
                    *
                    FROM
                        (
                        SELECT
                            pr_candidate_id,
                            company_id,
                            nalog_id,
                            nalog_naziv,
                            kandidat_id,
                            CONCAT(
                                kandidat_ime,
                                ' ',
                                kandidat_prezime
                            ) AS kandidat_ime_prezime,
                            poslanPuta,
                            prt_name,
                            pr_reminder_setting_id
                        FROM
                            (
                            SELECT
                                *,
                                COUNT(*) AS poslanPuta
                            FROM
                                (
                                SELECT
                                    activeReminders.pr_reminder_setting_id,
                                    activeReminders.pr_candidate_id,
                                    activeReminders.company_id,
                                    activeReminders.nalog_id,
                                    activeReminders.nalog_naziv,
                                    activeReminders.prt_name
                                FROM
                                    (
                                    SELECT
                                        *
                                    FROM
                                        (
                                        SELECT
                                            *
                                        FROM
                                            (
                                            SELECT
                                                *
                                            FROM
                                                (
                                                SELECT
                                                    *
                                                FROM
                                                    (
                                                    SELECT
                                                        *
                                                    FROM
                                                        `idk_pp_reminders`
                                                ) AS reminders
                                            JOIN `idk_pp_reminder_settings` ON reminders.pr_reminder_setting_id = `idk_pp_reminder_settings`.`prs_id` $whereTypes
                                            ) AS remindersWithSettings
                                        JOIN `idk_pp_reminder_types` ON `idk_pp_reminder_types`.`prt_id` = remindersWithSettings.prs_reminder_type_id
                                        ) AS remindersWithType
                                    INNER JOIN `idk_nalozi` ON remindersWithType.prs_nalog_id = `idk_nalozi`.`nalog_id`
                                    INNER JOIN `idk_companies` ON `idk_companies`.`company_id` = `idk_nalozi`.`kompanija_id`
                                    ) AS remindersWithCompany
                                WHERE
                                    pr_status NOT IN(3, 4) $reminderVisibility
                                ) AS activeReminders
                            INNER JOIN `idk_pp_reminders` ON `idk_pp_reminders`.`pr_reminder_setting_id` = activeReminders.`pr_reminder_setting_id` AND `idk_pp_reminders`.`pr_candidate_id` = activeReminders.`pr_candidate_id`
                            ) AS joinedReminders
                        GROUP BY
                            pr_reminder_setting_id,
                            pr_candidate_id
                        ) AS remindersSentTimes
                    INNER JOIN `idk_kandidati` ON `idk_kandidati`.`kandidat_id` = remindersSentTimes.pr_candidate_id
                    WHERE
                        $nalogUslov
                    ) AS remindersWithCandidates
                ";*/
        $query = $db->prepare($sql);
        $query->execute($executeParams);

        echo json_encode($query->fetchAll(PDO::FETCH_CLASS));
        break;
    case "reminderTypes":
            $query = $db->query("SELECT prt_id, prt_name FROM idk_pp_reminder_types");
            echo json_encode($query->fetchAll(PDO::FETCH_CLASS));
        break;
    case "reminders":
        $setting_id = "";
        $candidate_id = "";
        $has_documents = "";
        $reminder_id = "";
        $reminder_level = "";
        if (isset($_REQUEST["candidate_id"]) && isset($_REQUEST["setting_id"]) && isset($_REQUEST["has_documents"]) && isset($_REQUEST["reminder_id"]) && isset($_REQUEST["reminder_level"]))
        {
            $setting_id = $_REQUEST["setting_id"];
            $candidate_id = $_REQUEST["candidate_id"];
            $has_documents = $_REQUEST["has_documents"];
            $reminder_id = $_REQUEST["reminder_id"];
            $reminder_level = $_REQUEST["reminder_level"];
        }
        else
        {
            http_response_code(500);
            die("Bad parameters!");
        }

        if ($has_documents == 1) {
            $sql = "
                SELECT 
                    pr.pr_id, pr.pr_users_sent, pr.pr_date_sent, prt.prt_user_type, pr.pr_level,
                    (
                        CASE 
                            WHEN pr.pr_level = 1 THEN '1'
                            WHEN pr.pr_level = 2 THEN '2'
                            WHEN pr.pr_level = 3 THEN '3'
                            WHEN pr.pr_level = 4 THEN '4'
                            ELSE '5+'
                        END
                    ) AS reminder_level,
                    (
                        CASE
                            WHEN prt.prt_user_type = 3 AND pr.pr_user_assigned is not null AND pr.pr_done_by_crm_employee is null THEN CONCAT(e1.employee_firstname, ' ', e1.employee_lastname)
                            WHEN prt.prt_user_type != 3 AND pr.pr_user_assigned is not null AND pr.pr_done_by_crm_employee is null THEN CONCAT(pu.pu_fname, ' ', pu.pu_lname)
                            WHEN pr.pr_user_assigned is null AND pr.pr_done_by_crm_employee is not null THEN CONCAT(e.employee_firstname, ' ', e.employee_lastname)
                            ELSE null
                        END
                    ) AS reminder_user_assigned,
                    (
                        CASE 
                            WHEN pr.pr_status = 1 THEN null
                            WHEN pr.pr_status = 2 THEN pr.pr_date_assigned
                            WHEN pr.pr_status = 3 THEN pr.pr_date_completed
                            WHEN pr.pr_status = 4 THEN null
                            WHEN pr.pr_status = 5 THEN pr.pr_date_completed
                            ELSE null
                        END
                    ) AS reminder_status_datetime,
                    (
                        CASE 
                            WHEN pr.pr_status = 1 THEN 'Poslan'
                            WHEN pr.pr_status = 2 THEN 'Prihvaćen'
                            WHEN pr.pr_status = 3 THEN 'Završen (Normalno)'
                            WHEN pr.pr_status = 4 THEN 'Neizvršen'
                            WHEN pr.pr_status = 5 THEN 'Završen (Odustankom)'
                            ELSE 'Nedefinisan'
                        END
                    ) AS reminder_status
                FROM idk_pp_reminders pr
                JOIN idk_pp_reminder_documents prd ON pr.pr_reminder_document_id = prd.prd_id 
                JOIN idk_pp_reminder_settings prs ON prd.prd_prs_id = prs.prs_id
                JOIN idk_pp_reminder_types prt ON prs.prs_reminder_type_id = prt.prt_id
                LEFT JOIN idk_pp_user_access pua ON pua.pua_id = pr.pr_user_assigned
                LEFT JOIN idk_pp_users pu ON pu.pu_id = pua.pua_user_id 
                LEFT JOIN idk_employees e ON e.employee_id = pr.pr_done_by_crm_employee
                LEFT JOIN idk_employees e1 ON e1.employee_id = pr.pr_user_assigned
                WHERE prd.prd_id = :setting_id AND pr.pr_candidate_id = :candidate_id AND pr.pr_id <= :reminder_id
                ORDER BY pr.pr_id DESC
                LIMIT ".$reminder_level."
            ";
        } else {
            $sql = "
                SELECT 
                    pr.pr_id, pr.pr_users_sent, pr.pr_date_sent, prt.prt_user_type, pr.pr_level,
                    (
                        CASE 
                            WHEN pr.pr_level = 1 THEN '1'
                            WHEN pr.pr_level = 2 THEN '2'
                            WHEN pr.pr_level = 3 THEN '3'
                            WHEN pr.pr_level = 4 THEN '4'
                            ELSE '5+'
                        END
                    ) AS reminder_level,
                    (
                        CASE
                            WHEN prt.prt_user_type = 3 AND pr.pr_user_assigned is not null AND pr.pr_done_by_crm_employee is null THEN CONCAT(e1.employee_firstname, ' ', e1.employee_lastname)
                            WHEN prt.prt_user_type != 3 AND pr.pr_user_assigned is not null AND pr.pr_done_by_crm_employee is null THEN CONCAT(pu.pu_fname, ' ', pu.pu_lname)
                            WHEN pr.pr_user_assigned is null AND pr.pr_done_by_crm_employee is not null THEN CONCAT(e.employee_firstname, ' ', e.employee_lastname)
                            ELSE null
                        END
                    ) AS reminder_user_assigned,
                    (
                        CASE 
                            WHEN pr.pr_status = 1 THEN null
                            WHEN pr.pr_status = 2 THEN pr.pr_date_assigned
                            WHEN pr.pr_status = 3 THEN pr.pr_date_completed
                            WHEN pr.pr_status = 4 THEN null
                            WHEN pr.pr_status = 5 THEN pr.pr_date_completed
                            ELSE null
                        END
                    ) AS reminder_status_datetime,
                    (
                        CASE 
                            WHEN pr.pr_status = 1 THEN 'Poslan'
                            WHEN pr.pr_status = 2 THEN 'Prihvaćen'
                            WHEN pr.pr_status = 3 THEN 'Završen (Normalno)'
                            WHEN pr.pr_status = 4 THEN 'Neizvršen'
                            WHEN pr.pr_status = 5 THEN 'Završen (Odustankom)'
                            ELSE 'Nedefinisan'
                        END
                    ) AS reminder_status
                FROM idk_pp_reminders pr
                JOIN idk_pp_reminder_settings prs ON pr.pr_reminder_setting_id = prs.prs_id
                JOIN idk_pp_reminder_types prt ON prs.prs_reminder_type_id = prt.prt_id
                LEFT JOIN idk_pp_user_access pua ON pua.pua_id = pr.pr_user_assigned
                LEFT JOIN idk_pp_users pu ON pu.pu_id = pua.pua_user_id 
                LEFT JOIN idk_employees e ON e.employee_id = pr.pr_done_by_crm_employee
                LEFT JOIN idk_employees e1 ON e1.employee_id = pr.pr_user_assigned
                WHERE pr.pr_reminder_setting_id = :setting_id AND pr.pr_candidate_id = :candidate_id AND pr.pr_id <= :reminder_id
                ORDER BY pr.pr_id DESC
                LIMIT ".$reminder_level."
            ";
        }

        $query = $db->prepare($sql);
        $query->execute(["setting_id" => $setting_id, "candidate_id" => $candidate_id, "reminder_id" => $reminder_id]);

        echo json_encode($query->fetchAll(PDO::FETCH_CLASS));
        break;
    case "usersPUA":
        $pua_ids="";
        $prt_user_type="";
        if (isset($_REQUEST["pua_ids"]))
        {
            $pua_ids = $_REQUEST["pua_ids"];
            $prt_user_type = $_REQUEST["user_type"];
        }
        else
        {
            http_response_code(500);
            die("Bad parameters!");
        }
        if($prt_user_type == 3){
            $sql = "SELECT employee_id, CONCAT(employee_firstname, ' ', employee_lastname) AS korisnik_ime_prezime FROM idk_employees WHERE employee_id IN( $pua_ids )";
        }else{
            $sql = "SELECT pu_id, CONCAT(pu_fname, ' ', pu_lname) as korisnik_ime_prezime FROM (SELECT * FROM idk_pp_user_access WHERE idk_pp_user_access.pua_id IN ( $pua_ids )) as permissions INNER JOIN idk_pp_users ON idk_pp_users.pu_id = permissions.pua_user_id";
        }

        $query = $db->query($sql);

        echo json_encode($query->fetchAll(PDO::FETCH_CLASS));

        break;
    default:
        http_response_code(500);
        die("No page specified!");
        break;
}
