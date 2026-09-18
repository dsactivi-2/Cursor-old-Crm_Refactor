<?php 
    include("../includes/functions.php");
    include("../includes/common.php");

    if(isset($_REQUEST["page"])) {
        $page = $_REQUEST["page"];
    }else{
        header("Location: dashboardRemindera/reminders_of_project_manager?page=list&type=1");
    }

    /*
        Functions START
        */
            function getReminderLabelRPM($reminderType, $reminderName) {
                $result = '';
                $color = array(
                    1 => array('background' => '#FFC0CB', 'color' => '#000000'),
                    2 => array('background' => '#ADD8E6', 'color' => '#000000'),
                    3 => array('background' => '#90EE90', 'color' => '#000000'),
                    4 => array('background' => '#FFD700', 'color' => '#000000'),
                    5 => array('background' => '#FFA07A', 'color' => '#000000'),
                    6 => array('background' => '#DA70D6', 'color' => '#000000'),
                    7 => array('background' => '#FF6347', 'color' => '#FFFFFF'),
                    8 => array('background' => '#20B2AA', 'color' => '#000000'),
                    9 => array('background' => '#FA8072', 'color' => '#000000'),
                    10 => array('background' => '#00FF7F', 'color' => '#000000'),
                    11 => array('background' => '#8A2BE2', 'color' => '#FFFFFF'),
                    12 => array('background' => '#8B4513', 'color' => '#FFFFFF'),
                    13 => array('background' => '#6495ED', 'color' => '#000000'),
                    14 => array('background' => '#B0C4DE', 'color' => '#000000'),
                    15 => array('background' => '#87CEEB', 'color' => '#000000'),
                    16 => array('background' => '#00FA9A', 'color' => '#000000'),
                    17 => array('background' => '#FF4500', 'color' => '#000000'),
                    18 => array('background' => '#32CD32', 'color' => '#000000'),
                    19 => array('background' => '#FF69B4', 'color' => '#000000'),
                    20 => array('background' => '#F08080', 'color' => '#000000'),
                    21 => array('background' => '#8B008B', 'color' => '#FFFFFF'),
                    22 => array('background' => '#4B0082', 'color' => '#FFFFFF'),
                    23 => array('background' => '#00CED1', 'color' => '#000000'),
                    24 => array('background' => '#FFD700', 'color' => '#000000'),
                    25 => array('background' => '#FFD700', 'color' => '#000000')
                );

                if (array_key_exists($reminderType, $color)) {
                    $result = '<span class="label" style="background:'.$color[$reminderType]['background'].'; color:'.$color[$reminderType]['color'].'">'.$reminderName.'</span>';
                } else {
                    $result = '<span class="label label-default">Nepoznati tip remindera</span>';
                }
                unset($color);
                return $result;
            }
            function getReminderLevelLabelRPM($reminderLevel) {
                $result = '';
                switch($reminderLevel) {
                    case 'Level 5+':
                        $result = '<span class="label label-danger">Level 5+</span>';
                    break; 
                    case 'Level 4':
                        $result = '<span class="label label-warning">Level 4</span>';
                    break; 
                    case 'Level 3':
                        $result = '<span class="label label-primary">Level 3</span>'; 
                    break; 
                    case 'Level 2': 
                        $result = '<span class="label label-info">Level 2</span>';
                    break; 
                    case 'Level 1':
                        $result = '<span class="label label-success">Level 1</span>';
                    break; 
                    default:
                        $result = '<span class="label label-default">Nepoznati level</span>';
                    break; 
                }
                return $result; 
            }
        /*
        Functions END    
    */
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>Reminders | JobStep</title>
        <?php 
            include('../includes/head.php'); 
        ?>
    </head>
	<body>
        <?php 
            if (in_array($getUserIp, $getIpWhiteList)) {
                ?>
                    <header>
                        <?php
                            include('../header.php');
                        ?>
                    </header>
                    <div id="sidebar">
                        <?php
                            include('../menu.php');
                        ?>
                    </div>
                    <div id="content">
		                <div class="container-fluid">
                            <?php
                                switch($page)
                                {
                                    case "list":
                                        $enabled_types = array(1,2);
                                        $type = $_REQUEST["type"] ?? null;
                                        ?> 
                                            <div class="row">
                                                <div class = "col-xs-12">
                                                    <?php 
                                                        if (in_array($type, $enabled_types)) {
                                                            ?>
                                                                <div class = "row">
                                                                    <div class = "col-xs-9 idk_color_green">
                                                                        <h1>
                                                                            <i class="fa fa-tasks" aria-hidden="true" style = "margin-right: 10px;"></i>
                                                                            <?php 
                                                                                echo (($type == 1) ? 'Moji reminderi' : 'Controlling reminderi');
                                                                            ?>
                                                                        </h1>
                                                                    </div>
                                                                    <div class = "col-xs-3 text-right">
                                                                        <a href="<?php getSiteURL(); ?>" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
                                                                    </div>
                                                                </div>
                                                                <hr>
                                                                <div class="row" style = "margin-top: 20px;">
                                                                    <div class="col-xs-12">
                                                                        <div class="content_box">
                                                                            <?php 
                                                                                $allUsersCRM = $db->prepare("
                                                                                    SELECT 
                                                                                        employee_id AS userId, 
                                                                                        CONCAT(employee_firstname, ' ', employee_lastname) AS userFullName
                                                                                    FROM 
                                                                                        idk_employees
                                                                                "); 
                                                                                $allUsersCRM->execute(); 
                                                                                $allUsersCRMInfo = $allUsersCRM->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
                                                                                $allUsersCRMInfo = array_map('reset', $allUsersCRMInfo);

                                                                                $sql = '
                                                                                    SELECT 
                                                                                        pr.pr_id AS reminderId,
                                                                                        pr.pr_candidate_id AS candidateId,
                                                                                        CONCAT(kan.kandidat_ime, " ", kan.kandidat_prezime) AS candidateInfo, 
                                                                                        prt.prt_id AS reminderTypeId,
                                                                                        prt.prt_name AS reminderName, 
                                                                                        "Undefined" AS reminderDocument,
                                                                                        pr.pr_level AS reminderLevel, 
                                                                                        (
                                                                                            CASE 
                                                                                                WHEN pr.pr_level = 1 THEN "Level 1"
                                                                                                WHEN pr.pr_level = 2 THEN "Level 2"
                                                                                                WHEN pr.pr_level = 3 THEN "Level 3"
                                                                                                WHEN pr.pr_level = 4 THEN "Level 4"
                                                                                                ELSE "Level 5+"
                                                                                            END
                                                                                        ) AS reminderLevelText, 
                                                                                        pr.pr_date_sent AS reminderDateCreate,
                                                                                        nal.nalog_naziv AS orderInfo,
                                                                                        prs.prs_nalog_id AS orderId,
                                                                                        (
                                                                                            CASE 
                                                                                                WHEN prs.prs_partner_id is not null THEN ppa.company_name
                                                                                                WHEN prs.prs_partner_id is null THEN "No partner"
                                                                                                ELSE "Undefined"
                                                                                            END
                                                                                        ) AS partnerInfo,
                                                                                        prs.prs_partner_id AS partnerId,
                                                                                        pr.pr_users_sent AS reminderUsers
                                                                                    FROM
                                                                                        idk_pp_reminders pr
                                                                                    JOIN 
                                                                                        idk_pp_reminder_settings prs 
                                                                                    ON 
                                                                                        pr.pr_reminder_setting_id = prs.prs_id
                                                                                    JOIN 
                                                                                        idk_pp_reminder_types prt
                                                                                    ON 
                                                                                        prs.prs_reminder_type_id = prt.prt_id
                                                                                    JOIN 
                                                                                        idk_kandidati kan
                                                                                    ON 
                                                                                        pr.pr_candidate_id = kan.kandidat_id
                                                                                    JOIN 
                                                                                        idk_nalozi nal 
                                                                                    ON 
                                                                                        prs.prs_nalog_id = nal.nalog_id 
                                                                                    LEFT JOIN 
                                                                                        (
                                                                                            SELECT 
                                                                                                ppa1.ppa_id, 
                                                                                                ppa1.ppa_status,
                                                                                                ppa1.ppa_company_id, 
                                                                                                com1.company_name
                                                                                            FROM 
                                                                                                idk_pp_partners ppa1 
                                                                                            JOIN 
                                                                                                idk_companies com1 
                                                                                            ON 
                                                                                                ppa1.ppa_company_id = com1.company_id
                                                                                        )
                                                                                        AS 
                                                                                        ppa
                                                                                    ON 
                                                                                        prs.prs_partner_id = ppa.ppa_id 
                                                                                    WHERE 
                                                                                        pr.pr_status = 1
                                                                                        AND 
                                                                                        pr.pr_reminder_document_id is null 
                                                                                        AND 
                                                                                        prs.prs_active = 1 
                                                                                        AND 
                                                                                        prt.prt_user_type = 3
                                                                                        AND
                                                                                        prt.prt_has_documents = 0
                                                                                        AND 
                                                                                        '.(($type == 1) ? 'FIND_IN_SET(:userId, prs.prs_pua_ids) > 0' : 'FIND_IN_SET(:userId, prs.prs_controlling_pua_ids) > 0').'
                                                                            
                                                                                    UNION
                                                                            
                                                                                    SELECT 
                                                                                        pr.pr_id AS reminderId,
                                                                                        pr.pr_candidate_id AS candidateId,
                                                                                        CONCAT(kan.kandidat_ime, " ", kan.kandidat_prezime) AS candidateInfo, 
                                                                                        prt.prt_id AS reminderTypeId,
                                                                                        prt.prt_name AS reminderName, 
                                                                                        pdt.doc_type_name AS reminderDocument,
                                                                                        pr.pr_level AS reminderLevel, 
                                                                                        (
                                                                                            CASE 
                                                                                                WHEN pr.pr_level = 1 THEN "Level 1"
                                                                                                WHEN pr.pr_level = 2 THEN "Level 2"
                                                                                                WHEN pr.pr_level = 3 THEN "Level 3"
                                                                                                WHEN pr.pr_level = 4 THEN "Level 4"
                                                                                                ELSE "Level 5+"
                                                                                            END
                                                                                        ) AS reminderLevelText, 
                                                                                        pr.pr_date_sent AS reminderDateCreate,
                                                                                        nal.nalog_naziv AS orderInfo,
                                                                                        prs.prs_nalog_id AS orderId,
                                                                                        (
                                                                                            CASE 
                                                                                                WHEN prs.prs_partner_id is not null THEN ppa.company_name
                                                                                                WHEN prs.prs_partner_id is null THEN "No partner"
                                                                                                ELSE "Undefined"
                                                                                            END
                                                                                        ) AS partnerInfo,
                                                                                        prs.prs_partner_id AS partnerId,
                                                                                        pr.pr_users_sent AS reminderUsers
                                                                                    FROM
                                                                                        idk_pp_reminders pr
                                                                                    JOIN
                                                                                        idk_pp_reminder_documents prd 
                                                                                    ON 
                                                                                        pr.pr_reminder_document_id = prd.prd_id
                                                                                    JOIN 
                                                                                        idk_pp_reminder_settings prs 
                                                                                    ON 
                                                                                        prd.prd_prs_id = prs.prs_id
                                                                                    JOIN 
                                                                                        idk_pp_reminder_types prt
                                                                                    ON 
                                                                                        prs.prs_reminder_type_id = prt.prt_id
                                                                                    JOIN 
                                                                                        idk_kandidati kan
                                                                                    ON 
                                                                                        pr.pr_candidate_id = kan.kandidat_id
                                                                                    JOIN 
                                                                                        idk_nalozi nal 
                                                                                    ON 
                                                                                        prs.prs_nalog_id = nal.nalog_id 
                                                                                    LEFT JOIN 
                                                                                        (
                                                                                            SELECT 
                                                                                                ppa1.ppa_id, 
                                                                                                ppa1.ppa_status,
                                                                                                ppa1.ppa_company_id, 
                                                                                                com1.company_name
                                                                                            FROM 
                                                                                                idk_pp_partners ppa1 
                                                                                            JOIN 
                                                                                                idk_companies com1 
                                                                                            ON 
                                                                                                ppa1.ppa_company_id = com1.company_id
                                                                                        )
                                                                                        AS 
                                                                                        ppa
                                                                                    ON 
                                                                                        prs.prs_partner_id = ppa.ppa_id 
                                                                                    JOIN 
                                                                                        idk_pp_nalog_required_documents nrd 
                                                                                    ON 
                                                                                        prd.prd_nrd_id = nrd.nrd_id 
                                                                                    JOIN 
                                                                                        idk_pp_document_types pdt 
                                                                                    ON 
                                                                                        nrd.nrd_type_id = pdt.doc_type_id
                                                                                    WHERE 
                                                                                        pr.pr_status = 1
                                                                                        AND 
                                                                                        pr.pr_reminder_setting_id is null 
                                                                                        AND
                                                                                        prd.prd_active = 1 
                                                                                        AND 
                                                                                        prs.prs_active = 1 
                                                                                        AND 
                                                                                        prt.prt_user_type = 3
                                                                                        AND
                                                                                        prt.prt_has_documents = 1
                                                                                        AND
                                                                                        '.(($type == 1) ? 'FIND_IN_SET(:userId, prs.prs_pua_ids) > 0' : 'FIND_IN_SET(:userId, prs.prs_controlling_pua_ids) > 0').'
                                                                                '; 
                                                                                
                                                                                $query = $db->prepare($sql);
                                                                                $query->execute(array(
                                                                                    ':userId' => $logged_employee_id
                                                                                ));
                                                                                if ($query->rowCount() > 0) {
                                                                                    $allReminders = $query->fetchAll(PDO::FETCH_ASSOC);
                                                                                    //print("<pre>".print_r($allReminders,true)."</pre>");
                                                                                    ?>
                                                                                        <div class="row">
                                                                                            <div class="col-xs-12">
                                                                                                <script>
                                                                                                    $(document).ready(function(){
                                                                                                        $('#reminders').DataTable({
                                                                                                            responsive: true,
                                                                                                            "order": [[ 0, "asc" ]],
                                                                                                            "bAutoWidth": false,
                                                                                                        });
                                                                                                    });
                                                                                                </script>
                                                                                                <table id="reminders" class="display" cellspacing="0" style="width:100%">
                                                                                                    <thead>
                                                                                                        <tr>
                                                                                                            <th class="text-center">#</th>
                                                                                                            <th class="text-center">Kandidat</th>
                                                                                                            <th class="text-center">Tip remindera</th>
                                                                                                            <th class="text-center">Dokument</th>
                                                                                                            <th class="text-center">Level</th>
                                                                                                            <th class="text-center">Datum kreiranja</th>
                                                                                                            <th class="text-center">Nalog</th>
                                                                                                            <th class="text-center">Partner</th>
                                                                                                            <th class="text-center">Zaduženi korisnici</th>
                                                                                                        </tr>
                                                                                                    </thead>
                                                                                                    <tbody>
                                                                                                        <?php 
                                                                                                            $cnt = 0;
                                                                                                            foreach ($allReminders AS $reminderData) { 
                                                                                                                $cnt = $cnt + 1;
                                                                                                                ?>
                                                                                                                    <tr>
                                                                                                                        <td class="text-center"><?php echo $cnt; ?></td>
                                                                                                                        <td class="text-center">
                                                                                                                            <a target="_blank" href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $reminderData['candidateId']; ?>"><?php echo $reminderData['candidateInfo']; ?></a>
                                                                                                                        </td>
                                                                                                                        <td class="text-center">
                                                                                                                            <?php echo getReminderLabelRPM($reminderData['reminderTypeId'], $reminderData['reminderName']); ?>
                                                                                                                        </td>
                                                                                                                        <td class="text-center">
                                                                                                                            <?php echo (($reminderData['reminderDocument'] != 'Undefined') ? '<span class="label label-success">'.$reminderData['reminderDocument'].'</span>' : '<i class="fa fa-info-circle" aria-hidden="true" title="Ovaj tip remindera nije vezan za dokumente!"></i>'); ?>
                                                                                                                        </td>
                                                                                                                        <td class="text-center">
                                                                                                                            <?php echo getReminderLevelLabelRPM($reminderData['reminderLevelText']); ?>
                                                                                                                        </td>
                                                                                                                        <td class="text-center"><?php echo date('d.m.Y', strtotime($reminderData['reminderDateCreate'])); ?></td>
                                                                                                                        <td class="text-center">
                                                                                                                            <a target="_blank" href="<?php getSiteURL(); ?>nalozi?page=open&id=<?php echo $reminderData['orderId']; ?>"><?php echo $reminderData['orderInfo']; ?></a>
                                                                                                                        </td>
                                                                                                                        <td class="text-center">
                                                                                                                            <?php
                                                                                                                                if ($reminderData['partnerInfo'] != 'No partner' AND $reminderData['partnerInfo'] != 'Undefined') {
                                                                                                                                ?>
                                                                                                                                    <a target="_blank" href="<?php getSiteURL(); ?>companies?page=open&id=<?php echo $reminderData['partnerId']; ?>"><?php echo $reminderData['partnerInfo']; ?></a>
                                                                                                                                <?php 
                                                                                                                                } else {
                                                                                                                                    echo '<i class="fa fa-info-circle" aria-hidden="true" title="Ovaj tip remindera nije vezan za partnere!"></i>';
                                                                                                                                }    
                                                                                                                            ?>
                                                                                                                        </td>
                                                                                                                        <td class="text-center">
                                                                                                                            <?php 
                                                                                                                                $usersInfoImp = '<i class="fa fa-info-circle" aria-hidden="true" title="Ovaj tip remindera nema postavljene korisnike!"></i>';
                                                                                                                                if ($reminderData['reminderUsers'] != null) {
                                                                                                                                    $usersInfoExp = array();
                                                                                                                                    $reminderUsers = explode(',',$reminderData['reminderUsers']);
                                                                                                                                    foreach ($reminderUsers AS $reminderUser) {
                                                                                                                                        array_push($usersInfoExp, $allUsersCRMInfo[$reminderUser]['userFullName']);
                                                                                                                                        unset($reminderUser); 
                                                                                                                                    }
                                                                                                                                    unset($reminderUsers);
                                                                                                                                    $usersInfoImp = implode(', ',$usersInfoExp); 
                                                                                                                                    unset($usersInfoExp);
                                                                                                                                }
                                                                                                                                echo $usersInfoImp;
                                                                                                                            ?>
                                                                                                                        </td>
                                                                                                                    </tr> 
                                                                                                                <?php 
                                                                                                                unset($reminderData); 
                                                                                                            }
                                                                                                            unset($cnt);
                                                                                                        ?>
                                                                                                    </tbody>
                                                                                                    <tfoot>
                                                                                                        <tr>
                                                                                                            <th class="text-center">#</th>
                                                                                                            <th class="text-center">Kandidat</th>
                                                                                                            <th class="text-center">Tip remindera</th>
                                                                                                            <th class="text-center">Dokument</th>
                                                                                                            <th class="text-center">Level</th>
                                                                                                            <th class="text-center">Datum kreiranja</th>
                                                                                                            <th class="text-center">Nalog</th>
                                                                                                            <th class="text-center">Partner</th>
                                                                                                            <th class="text-center">Zaduženi korisnici</th>
                                                                                                        </tr>
                                                                                                    </tfoot>
                                                                                                </table>
                                                                                            </div>
                                                                                        </div>
                                                                                    <?php 
                                                                                } else {
                                                                                    ?> 
                                                                                        <div class="row">
                                                                                            <div class="col-xs-offset-2 col-xs-8">
                                                                                                <div class="alert alert-info text-center" role="alert">
                                                                                                    <h4><strong>Info</strong></h4><br>
                                                                                                    Nema aktivnih remindera!
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    <?php 
                                                                                }
                                                                                unset($allReminders);
                                                                                unset($allUsersCRMInfo); 
                                                                                unset($sql);
                                                                            ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php 
                                                        } else {
                                                            ?>
                                                                <div class="alert material-alert material-alert_danger">
                                                                    <h4>Upozorenje!</h4>
                                                                    <p>Nedefinisani tip pregleda!</p>
                                                                    <br>
                                                                </div>
                                                            <?php
                                                        }
                                                    ?>
                                                </div>
                                            </div>
                                        <?php 
                                        unset($enabled_types);
                                    break;

                                    default:
                                        ?> 
                                            <br>
                                            <div class="alert material-alert material-alert_danger">
                                                <h4>Upozorenje!</h4>
                                                <p>Nedefinisana stranica!</p>
                                                <br>
                                            </div>
                                        <?php 
                                    break;
                                }
                            ?>
                            <footer><?php getCopyright(); ?></footer>
                        </div>
                    </div>
                <?php 
            } else {
                ?>
                    <br>
                    <div class="alert material-alert material-alert_danger">
                        <h4>NEMATE PRIVILEGIJE!</h4>
                        <p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
                        <br>
                    </div>
                <?php
            }
        ?>
    </body>
</html>
