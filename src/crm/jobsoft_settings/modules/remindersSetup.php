<?php 

    /*
        Functions for this document START 
        */

            function getReminderLabelRS($reminderType, $reminderName) {
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

            function getDocumentStatusLabelRS($documentStatus, $documentName) {
                $result = '';
                $color = array(
                    'Active' => array('background' => '#90EE90', 'color' => '#000000', 'title' => 'Dokument je aktivan!'), 
                    'Inactive' => array('background' => '#FFC0CB', 'color' => '#000000', 'title' => 'Dokument nije aktivan! Izvršite izmjenu sa istim aktivnim dokumentom ako postoji!')
                );
                if (array_key_exists($documentStatus, $color)) { 
                    $result = '<span class="label" title="'.$color[$documentStatus]['title'].'" style="background:'.$color[$documentStatus]['background'].'; color:'.$color[$documentStatus]['color'].'">'.$documentName.'</span>';
                } else {
                    $result = '<span class="label label-default" title="Nepoznati status dokumenta!">'.$documentName.'</span>';
                }
                unset($color);
                return $result;
            }

        /*
        Functions for this document START 
    */

    $orderId = intval($nalog_id);

    if ($orderId != 0) {
        $queryAllUsersCRM = $db->prepare("
            SELECT 
                employee_id AS userId, 
                CONCAT(employee_firstname, ' ', employee_lastname) AS userFullName
            FROM 
                idk_employees
        "); 
        $queryAllUsersCRM->execute(); 
        $rowAllUsersCRM = $queryAllUsersCRM->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
        $rowAllUsersCRM = array_map('reset', $rowAllUsersCRM);

        $queryAllAccessPP = $db->prepare("
            SELECT 
                pua.pua_id AS userAccessId, 
                pu.pu_id AS userId, 
                CONCAT(pu.pu_fname, ' ', pu.pu_lname) AS userFullName
            FROM 
                idk_pp_user_access pua
            JOIN 
                idk_pp_users pu
            ON 
                pua.pua_user_id = pu.pu_id
            ORDER BY 
                pu.pu_id
        "); 
        $queryAllAccessPP->execute(); 
        $rowAllAccessPP = $queryAllAccessPP->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
        $rowAllAccessPP = array_map('reset', $rowAllAccessPP);

        $queryAllOrderDocuments = $db->prepare("
            SELECT 
                nrd_id AS documentId,
                (
                    CASE 
                        WHEN nrd_status = 1 THEN 'Active'
                        WHEN nrd_status = 0 THEN 'Inactive'
                        ELSE 'Undefined'
                    END
                ) AS documentStatus
            FROM 
                idk_pp_nalog_required_documents
            WHERE 
                nrd_nalog_id = :nrd_nalog_id
            ORDER BY
                nrd_id 
            ASC
        "); 
        $queryAllOrderDocuments->execute(array(
            ':nrd_nalog_id' => $orderId
        ));
        $rowAllOrderDocuments = $queryAllOrderDocuments->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
        $rowAllOrderDocuments = array_map('reset', $rowAllOrderDocuments);
        //print("<pre>".print_r($rowAllOrderDocuments,true)."</pre>");

        $queryAllSettings = $db->prepare("
            SELECT 
                allSettings.*
            FROM 
                (
                    /*All reminder settings*/
                    SELECT 
                        prt1.prt_id 															AS reminderType, 
                        prt1.prt_name 															AS reminderTypeName,
                        'No document name'														AS reminderDocumentName,
                        (
                            CASE
                                WHEN prt1.prt_user_type = 1 THEN 'SuperAdmin'
                                WHEN prt1.prt_user_type = 2 THEN 'Admin'
                                WHEN prt1.prt_user_type = 3 THEN 'ProjectManager'
                                ELSE 'Undefined'
                            END
                        ) 																		AS reminderUserType, 
                        prt1.prt_day_freq 														AS reminderDayFreq,
                        (	
                            CASE
                                WHEN prt1.prt_has_documents = 1 THEN 'has documents'
                                WHEN prt1.prt_has_documents = 0 THEN 'no documents'
                                ELSE 'Undefined'
                            END
                        )																		AS reminderHasDocuments,
                        prs1.prs_id																AS settingId, 
                        'Undefined'																AS settingDocumentId,
                        'Undefined'                                                             AS settingOrderDocumentId,
                        com1.company_name														AS settingCompanyName,
                        nal1.nalog_naziv														AS settingOrderName,
                        prs1.prs_nalog_id                                                       AS settingOrderId, 
                        (
                            CASE 
                                WHEN prs1.prs_partner_id is not null THEN ppa1.company_name
                                WHEN prs1.prs_partner_id is null THEN 'No partner'
                                ELSE 'Undefined'
                            END
                        )																		AS settingPartnerName,
                        (
                            CASE 
                                WHEN prs1.prs_partner_id is not null THEN prs1.prs_partner_id
                                WHEN prs1.prs_partner_id is null THEN 'No partner'
                                ELSE 'Undefined'
                            END
                        )                                                                       AS settingPartnerId, 
                        (
                            CASE 
                                WHEN prs1.prs_pua_ids is null THEN 'Undefined'
                                ELSE prs1.prs_pua_ids 
                            END 
                        )                                                                       AS settingAssignedUsers,
                        (
                            CASE 
                                WHEN prs1.prs_controlling_pua_ids is null THEN 'Undefined'
                                ELSE prs1.prs_controlling_pua_ids 
                            END 
                        )                                                                       AS settingControllingUsers, 
                        (
                            CASE 
                                WHEN prs1.prs_first_trigger_date is null THEN 'Undefined'
                                ELSE prs1.prs_first_trigger_date 
                            END 
                        )                                                                       AS settingFirstTriggerDate,
                        (
                            CASE 
                                WHEN prs1.prs_create_after is null THEN 0
                                ELSE prs1.prs_create_after 
                            END 
                        )                                                                       AS settingCreateAfter
                    FROM 
                        idk_pp_reminder_types prt1 
                    JOIN  
                        idk_pp_reminder_settings prs1 
                    ON 
                        prt1.prt_id = prs1.prs_reminder_type_id
                    JOIN 
                        idk_nalozi nal1
                    ON 
                        prs1.prs_nalog_id = nal1.nalog_id 
                    JOIN 
                        idk_companies com1 
                    ON 
                        nal1.kompanija_id = com1.company_id
                    LEFT JOIN 
                        (
                            SELECT 
                                ppa2.ppa_id, 
                                ppa2.ppa_status,
                                ppa2.ppa_company_id, 
                                com2.company_name
                            FROM 
                                idk_pp_partners ppa2 
                            JOIN 
                                idk_companies com2 
                            ON 
                                ppa2.ppa_company_id = com2.company_id
                            WHERE 
                                ppa2.ppa_nalog_id = :nalogId
                        )
                        AS 
                        ppa1 
                    ON 
                        prs1.prs_partner_id = ppa1.ppa_id
                    WHERE 
                        prs1.prs_nalog_id = :nalogId
                        AND 
                        prs1.prs_active = 1
                        AND 
                        prt1.prt_has_documents = 0
                        
                    UNION 
                    
                    /*All reminder settings*/
                    SELECT 
                        prt1.prt_id 															AS reminderType, 
                        prt1.prt_name 															AS reminderTypeName,
                        nrd1.doc_type_name														AS reminderDocumentName,
                        (
                            CASE
                                WHEN prt1.prt_user_type = 1 THEN 'SuperAdmin'
                                WHEN prt1.prt_user_type = 2 THEN 'Admin'
                                WHEN prt1.prt_user_type = 3 THEN 'ProjectManager'
                                ELSE 'Undefined'
                            END
                        ) 																		AS reminderUserType, 
                        prd1.prd_day_freq 														AS reminderDayFreq,
                        (	
                            CASE
                                WHEN prt1.prt_has_documents = 1 THEN 'has documents'
                                WHEN prt1.prt_has_documents = 0 THEN 'no documents'
                                ELSE 'Undefined'
                            END
                        )																		AS reminderHasDocuments,
                        prs1.prs_id																AS settingId, 
                        prd1.prd_id																AS settingDocumentId,
                        prd1.prd_nrd_id                                                         AS settingOrderDocumentId,
                        com1.company_name														AS settingCompanyName,
                        nal1.nalog_naziv														AS settingOrderName,
                        prs1.prs_nalog_id                                                       AS settingOrderId, 
                        (
                            CASE 
                                WHEN prs1.prs_partner_id is not null THEN ppa1.company_name
                                WHEN prs1.prs_partner_id is null THEN 'No partner'
                                ELSE 'Undefined'
                            END
                        )																		AS settingPartnerName,
                        (
                            CASE 
                                WHEN prs1.prs_partner_id is not null THEN prs1.prs_partner_id
                                WHEN prs1.prs_partner_id is null THEN 'No partner'
                                ELSE 'Undefined'
                            END
                        )                                                                       AS settingPartnerId,
                        (
                            CASE 
                                WHEN prs1.prs_pua_ids is null THEN 'Undefined'
                                ELSE prs1.prs_pua_ids 
                            END 
                        )                                                                       AS settingAssignedUsers, 
                        (
                            CASE 
                                WHEN prs1.prs_controlling_pua_ids is null THEN 'Undefined'
                                ELSE prs1.prs_controlling_pua_ids 
                            END 
                        )                                                                       AS settingControllingUsers, 
                        (
                            CASE 
                                WHEN prs1.prs_first_trigger_date is null THEN 'Undefined'
                                ELSE prs1.prs_first_trigger_date 
                            END 
                        )                                                                       AS settingFirstTriggerDate,
                        (
                            CASE 
                                WHEN prs1.prs_create_after is null THEN 0
                                ELSE prs1.prs_create_after 
                            END 
                        )                                                                       AS settingCreateAfter
                    FROM 
                        idk_pp_reminder_types prt1 
                    JOIN  
                        idk_pp_reminder_settings prs1 
                    ON 
                        prt1.prt_id = prs1.prs_reminder_type_id
                    JOIN 
                        idk_pp_reminder_documents prd1 
                    ON 
                        prs1.prs_id = prd1.prd_prs_id
                    JOIN 
                        idk_nalozi nal1
                    ON 
                        prs1.prs_nalog_id = nal1.nalog_id 
                    JOIN 
                        idk_companies com1 
                    ON 
                        nal1.kompanija_id = com1.company_id
                    LEFT JOIN 
                        (
                            SELECT 
                                ppa2.ppa_id, 
                                ppa2.ppa_status,
                                ppa2.ppa_company_id, 
                                com2.company_name
                            FROM 
                                idk_pp_partners ppa2 
                            JOIN 
                                idk_companies com2 
                            ON 
                                ppa2.ppa_company_id = com2.company_id
                            WHERE 
                                ppa2.ppa_nalog_id = :nalogId
                        )
                        AS 
                        ppa1 
                    ON 
                        prs1.prs_partner_id = ppa1.ppa_id
                    LEFT JOIN
                        (
                            SELECT 
                                nrd2.nrd_id, 
                                dt2.doc_type_name
                            FROM 
                                idk_pp_nalog_required_documents nrd2 
                            JOIN 
                                idk_pp_document_types dt2 
                            ON 
                                nrd2.nrd_type_id = dt2.doc_type_id
                            WHERE 
                                nrd2.nrd_nalog_id = :nalogId
                        ) 
                        AS 
                        nrd1
                    ON
                        prd1.prd_nrd_id = nrd1.nrd_id
                    WHERE 
                        prs1.prs_nalog_id = :nalogId
                        AND 
                        prs1.prs_active = 1
                        AND 
                        prt1.prt_has_documents = 1
                        AND 
                        prd1.prd_active = 1
                        AND 
                        prd1.prd_nrd_id is not null 
                        AND 
                        prd1.prd_crd_id is null 
                )
                AS 
                allSettings
            ORDER BY allSettings.reminderType ASC
        "); 
        $queryAllSettings->execute(array(
            ':nalogId' => $orderId
        ));
        if ($queryAllSettings->rowCount() > 0) {
            $rowsAllSettings = $queryAllSettings->fetchAll(PDO::FETCH_ASSOC);

            $groupedData = array();

            foreach ( $rowsAllSettings AS $rowSettings ) { 

                $settingAssignedUsersInfoImp = 'Undefined';
                if ($rowSettings['settingAssignedUsers'] != 'Undefined') {
                    $settingAssignedUsersInfoExp = array();
                    $settingAssignedUsers = explode(',',$rowSettings['settingAssignedUsers']);
                    foreach ($settingAssignedUsers AS $settingAssignedUser) {
                        if ($rowSettings['reminderUserType'] == 'ProjectManager') {
                            array_push($settingAssignedUsersInfoExp, $rowAllUsersCRM[$settingAssignedUser]['userFullName']); 
                        } else {
                            array_push($settingAssignedUsersInfoExp, $rowAllAccessPP[$settingAssignedUser]['userFullName']);
                        }
                    }
                    unset($settingAssignedUsers);
                    $settingAssignedUsersInfoImp = implode(', ',$settingAssignedUsersInfoExp); 
                    unset($settingAssignedUsersInfoExp);
                }

                $settingControllingUsersInfoImp = 'Undefined';
                if ($rowSettings['settingControllingUsers'] != 'Undefined') {
                    $settingControllingUsersInfoExp = array();
                    $settingControllingUsers = explode(',',$rowSettings['settingControllingUsers']);
                    foreach ($settingControllingUsers AS $settingControllingUser) {
                        if ($rowSettings['reminderUserType'] == 'ProjectManager') {
                            array_push($settingControllingUsersInfoExp, $rowAllUsersCRM[$settingControllingUser]['userFullName']); 
                        } else {
                            array_push($settingControllingUsersInfoExp, $rowAllAccessPP[$settingControllingUser]['userFullName']);
                        }
                    }
                    unset($settingControllingUsers);
                    $settingControllingUsersInfoImp = implode(', ',$settingControllingUsersInfoExp); 
                    unset($settingControllingUsersInfoExp);
                }

                $settingOrderDocumentStatus = 'Undefined'; 
                if ($rowSettings['reminderHasDocuments'] == 'has documents') {
                    if (isset($rowAllOrderDocuments[$rowSettings['settingOrderDocumentId']])) {
                        $settingOrderDocumentStatus = $rowAllOrderDocuments[$rowSettings['settingOrderDocumentId']]['documentStatus']; 
                    }
                }

                if (isset($groupedData[$rowSettings['reminderUserType']])) {
                    $existingSettingId = $rowSettings['settingId'];
                    if (isset($groupedData[$rowSettings['reminderUserType']]['data'][$existingSettingId])) {
                        // Ako postoji settingId, samo dodaj podatke
                        $groupedData[$rowSettings['reminderUserType']]['data'][$existingSettingId]['groupSetting']['data'][] = array(
                            'settingDocumentId' => $rowSettings['settingDocumentId'],
                            'settingOrderDocumentId' => $rowSettings['settingOrderDocumentId'],
                            'reminderDocumentName' => $rowSettings['reminderDocumentName'],
                            'settingOrderDocumentStatus' => $settingOrderDocumentStatus
                        );
                    } else {
                        // Ako ne postoji settingId, dodaj novi settingId sa podacima
                        $groupedData[$rowSettings['reminderUserType']]['data'][$existingSettingId] = array(
                            'reminderType' => $rowSettings['reminderType'], 
                            'reminderTypeName' => $rowSettings['reminderTypeName'], 
                            'reminderUserType' => $rowSettings['reminderUserType'], 
                            'reminderDayFreq' => $rowSettings['reminderDayFreq'], 
                            'reminderHasDocuments' => $rowSettings['reminderHasDocuments'], 
                            'groupSetting' => array(
                                'settingId' => $existingSettingId,
                                'data' => array(
                                    array(
                                        'settingDocumentId' => $rowSettings['settingDocumentId'],
                                        'settingOrderDocumentId' => $rowSettings['settingOrderDocumentId'],
                                        'reminderDocumentName' => $rowSettings['reminderDocumentName'],
                                        'settingOrderDocumentStatus' => $settingOrderDocumentStatus,
                                    )
                                )
                            ),
                            'settingCompanyName' => $rowSettings['settingCompanyName'], 
                            'settingOrderName' => $rowSettings['settingOrderName'],
                            'settingOrderId' => $rowSettings['settingOrderId'], 
                            'settingPartnerName' => $rowSettings['settingPartnerName'],
                            'settingPartnerId' => $rowSettings['settingPartnerId'],
                            'settingAssignedUsers' => $rowSettings['settingAssignedUsers'],
                            'settingAssignedUsersInfo' => $settingAssignedUsersInfoImp,
                            'settingControllingUsers' => $rowSettings['settingControllingUsers'],
                            'settingControllingUsersInfo' => $settingControllingUsersInfoImp,
                            'settingFirstTriggerDate' => $rowSettings['settingFirstTriggerDate'],
                            'settingCreateAfter' => $rowSettings['settingCreateAfter']
                        );
                    }
                } else {
                    // Ako ne postoji reminderUserType, dodaj novi sa podacima
                    $groupedData[$rowSettings['reminderUserType']] = array(
                        'data' => array(
                            $rowSettings['settingId'] => array(
                                'reminderType' => $rowSettings['reminderType'], 
                                'reminderTypeName' => $rowSettings['reminderTypeName'], 
                                'reminderUserType' => $rowSettings['reminderUserType'], 
                                'reminderDayFreq' => $rowSettings['reminderDayFreq'], 
                                'reminderHasDocuments' => $rowSettings['reminderHasDocuments'], 
                                'groupSetting' => array(
                                    'settingId' => $rowSettings['settingId'],
                                    'data' => array(
                                        array(
                                            'settingDocumentId' => $rowSettings['settingDocumentId'],
                                            'settingOrderDocumentId' => $rowSettings['settingOrderDocumentId'],
                                            'reminderDocumentName' => $rowSettings['reminderDocumentName'],
                                            'settingOrderDocumentStatus' => $settingOrderDocumentStatus,
                                        )
                                    )
                                ),
                                'settingCompanyName' => $rowSettings['settingCompanyName'], 
                                'settingOrderName' => $rowSettings['settingOrderName'],
                                'settingOrderId' => $rowSettings['settingOrderId'], 
                                'settingPartnerName' => $rowSettings['settingPartnerName'],
                                'settingPartnerId' => $rowSettings['settingPartnerId'],
                                'settingAssignedUsers' => $rowSettings['settingAssignedUsers'],
                                'settingAssignedUsersInfo' => $settingAssignedUsersInfoImp,
                                'settingControllingUsers' => $rowSettings['settingControllingUsers'],
                                'settingControllingUsersInfo' => $settingControllingUsersInfoImp,
                                'settingFirstTriggerDate' => $rowSettings['settingFirstTriggerDate'],
                                'settingCreateAfter' => $rowSettings['settingCreateAfter']
                            )
                        )
                    );
                }
                unset($settingAssignedUsersInfoImp);
                unset($settingControllingUsersInfoImp);

            }

            unset($rowsAllSettings); 

            /*
                Odkomentarisati liniju koda u slučaju da treba vidjeti izgled podataka (groupedData) START 
                */
                    //print("<pre>".print_r($groupedData,true)."</pre>");
                    //exit();
                /*
                Odkomentarisati liniju koda u slučaju da treba vidjeti izgled podataka (groupedData) START 
            */

            ?>
                <!-- 
                    Message Start
                    -->
                        <?php 
                            if(isset($_GET['messrs'])) {
                                $enabledMessagesRS = array(1,2,3,4);
                                $messRS = $_GET['messrs'];
                                $resultMessRS = '';
                                if(in_array($messRS, $enabledMessagesRS)) {
                                    if($messRS == 1){
                                        $resultMessRS = '<div class="alert alert-danger text-center" role="alert"><h4><strong>Greška</strong></h4><br>Pokušali ste izvršiti update nepredviđenog tipa korisnika! Obratite se administratoru sistema!</div>';
                                    }elseif($messRS == 2){
                                        $resultMessRS = '<div class="alert alert-danger text-center" role="alert"><h4><strong>Upozorenje</strong></h4><br>Akcijom bi se izvršila promjena za više postavki ili sistem nije pronašao ni jednu postavku nad kojom je moguće izvršiti ažuriranje! Obratite se administratoru sistema!</div>';
                                    }elseif($messRS == 3){
                                        $resultMessRS = '<div class="alert alert-danger text-center" role="alert"><h4><strong>Greška</strong></h4><br>Postoji problem sa ažuriranjem postavke! Obratite se administratoru sistema!</div>';
                                    }elseif($messRS == 4){
                                        $resultMessRS = '<div class="alert alert-success text-center" role="alert"><h4><strong>Uspješno izvršeno</strong></h4><br>Uspješno ste izvršili ažuriranje postavke!</div>';
                                    }
                                } else {
                                    $resultMessRS = '<div class="alert alert-danger text-center" role="alert"><h4><strong>Greška</strong></h4><br>Nepredviđena poruka odgovora! Kontaktirajte administratora sistema!.</div>';
                                }
                                
                                ?>
                                    <div class="row">
                                        <div class="col-xs-offset-2 col-xs-8">
                                            <?php 
                                                echo $resultMessRS;
                                            ?>
                                        </div>
                                    </div>
                                <?php 
                                unset($enabledMessagesRS);
                            }
                        ?>
                        
                    <!-- 
                    Message End
                -->
                <!--
                    Header options START 
                    -->
                        <div class="row">
                            <div class="col-xs-12 text-right">
                                <a href="" data-toggle="modal" data-target="#addReminderSetting" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive">
                                    <i class="fa fa-plus" aria-hidden="true">
                                    </i>
                                    <span>
                                        Dodaj postavku
                                    </span>
                                </a>
                                <div class="modal material-modal material-modal_primary fade text-left" id="addReminderSetting">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content material-modal__content">
                                            <div class="modal-header material-modal__header">
                                                <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                                <h4 class="modal-title material-modal__title"><i style = "margin-right: 10px;" class="fa fa-plus" aria-hidden="true"></i>Dodaj postavku remindera</h4>
                                            </div> 
                                            <div class="modal-body material-modal__body">
                                                <div class="form-group">
                                                    <div class="col-md-offset-1 col-sm-10">
                                                        <div class="alert alert-danger text-center" role="alert">
                                                            <h4><strong>Info</strong></h4>
                                                            <br>
                                                            Opcija je u razvoju. Obratite se DEV team-u u slučaju potrebe aktivacije određenog remindera.
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer material-modal__footer">
                                                    <button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
                                                    <!--<button type="submit" class="btn btn-primary material-btn material-btn_success" form="form_id"><i class="fa fa-check-square-o" aria-hidden="true"></i> Spremi</button>-->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <!--
                    Header options END 
                -->
                <!-- 
                    Content START 
                    -->
                        <div class="row">
                            <div class="col-xs-12">
                                <!-- 
                                    SuperAdmin START 
                                    -->
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="row">
                                                    <div class="col-xs-12">
                                                        <h5 style = "font-weight: bold;">SuperAdmin</h5>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-xs-12">
                                                        <?php 
                                                            if (isset($groupedData['SuperAdmin']) AND isset($groupedData['SuperAdmin']['data'])){
                                                                ?>
                                                                    <script>
                                                                        $(document).ready(function(){
                                                                            $('#superAdmin').DataTable({
                                                                                responsive: true,
														                        "order": [[ 0, "asc" ]],
                                                                                "bAutoWidth": false,
                                                                            });
                                                                        });
                                                                    </script>
                                                                    <table id="superAdmin" class="display" cellspacing="0" style="width:100%">
                                                                        <thead>
                                                                            <tr>
                                                                                <th class="text-center">Tip</th>
                                                                                <th class="text-center">Naziv</th>
                                                                                <th class="text-center">Dokument</th>
                                                                                <th class="text-center">Partner Name</th>
                                                                                <th class="text-center">Korisnici</th>
                                                                                <th class="text-center">Controlling korisnici</th>
                                                                                <th class="text-center">Akcija</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <?php 
                                                                                foreach ($groupedData['SuperAdmin']['data'] AS $superAdminData) {
                                                                                    ?>
                                                                                        <tr>
                                                                                            <td class="text-center"><?php echo $superAdminData['reminderType']; ?></td>
                                                                                            <td class="text-center"><?php echo getReminderLabelRS($superAdminData['reminderType'], $superAdminData['reminderTypeName']); ?></td>
                                                                                            <td class="text-center">
                                                                                                <?php 
                                                                                                    if ($superAdminData['reminderHasDocuments'] != 'no documents') {
                                                                                                        $reminderDocumentsName = array();
                                                                                                        foreach ($superAdminData['groupSetting']['data'] AS $superAdminGroupSettingData) {
                                                                                                            array_push($reminderDocumentsName, getDocumentStatusLabelRS($superAdminGroupSettingData['settingOrderDocumentStatus'], $superAdminGroupSettingData['reminderDocumentName']));
                                                                                                        }
                                                                                                        echo implode(' ', $reminderDocumentsName); 
                                                                                                        unset($superAdminGroupSettingData);
                                                                                                        unset($reminderDocumentsName); 
                                                                                                    } else { 
                                                                                                        echo '<i class="fa fa-info-circle" aria-hidden="true" title="Ovaj reminder nije vezan za dokumente!"></i>';  
                                                                                                    }; 
                                                                                                ?>
                                                                                            </td>
                                                                                            <td class="text-center"><?php echo (($superAdminData['settingPartnerName'] != 'No partner') ? $superAdminData['settingPartnerName'] : '<i class="fa fa-info-circle" aria-hidden="true" title="Ovaj reminder nije vezan za partnere!"></i>'); ?></td>
                                                                                            <td class="text-center"><?php echo (($superAdminData['settingAssignedUsersInfo'] != 'Undefined') ? $superAdminData['settingAssignedUsersInfo'] : '<i class="fa fa-info-circle" aria-hidden="true" title="Ovaj reminder nema postavljenih usera!"></i>'); ?></td>
                                                                                            <td class="text-center"><?php echo (($superAdminData['settingControllingUsersInfo'] != 'Undefined') ? $superAdminData['settingControllingUsersInfo'] : '<i class="fa fa-info-circle" aria-hidden="true" title="Ovaj reminder nema postavljenih kontroling usera!"></i>'); ?></td>
                                                                                            <td class="text-center">
                                                                                                <div class="btn-group material-btn-group">
                                                                                                    <button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i><span class="caret material-btn__caret"></span></button>
                                                                                                    <ul style = "top:33px; left:-60px; min-width:185px !important;" class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
                                                                                                        <li>
                                                                                                            <a 
                                                                                                                class="material-dropdown-menu__link" 
                                                                                                                onclick="changeUsersRS(this)"
                                                                                                                data-change_user_type="users"
                                                                                                                data-reminder_type ="<?php echo $superAdminData['reminderType']; ?>"
                                                                                                                data-reminder_user_type ="<?php echo $superAdminData['reminderUserType']; ?>"
                                                                                                                data-setting_id ="<?php echo $superAdminData['groupSetting']['settingId']; ?>"
                                                                                                                data-setting_order_id ="<?php echo $superAdminData['settingOrderId']; ?>"
                                                                                                                data-setting_partner_id ="<?php echo $superAdminData['settingPartnerId']; ?>"
                                                                                                                data-setting_assigned_users ="<?php echo $superAdminData['settingAssignedUsers']; ?>"
                                                                                                                data-setting_controlling_users ="<?php echo $superAdminData['settingControllingUsers']; ?>"
                                                                                                            >
                                                                                                                <i class="fa fa-pencil-square" aria-hidden="true"></i> 
                                                                                                                Korisnici
                                                                                                            </a>
                                                                                                        </li>
                                                                                                        <li>
                                                                                                            <a 
                                                                                                                class="material-dropdown-menu__link" 
                                                                                                                onclick="changeUsersRS(this)"
                                                                                                                data-change_user_type="controllingUsers"
                                                                                                                data-reminder_type ="<?php echo $superAdminData['reminderType']; ?>"
                                                                                                                data-reminder_user_type ="<?php echo $superAdminData['reminderUserType']; ?>"
                                                                                                                data-setting_id ="<?php echo $superAdminData['groupSetting']['settingId']; ?>"
                                                                                                                data-setting_order_id ="<?php echo $superAdminData['settingOrderId']; ?>"
                                                                                                                data-setting_partner_id ="<?php echo $superAdminData['settingPartnerId']; ?>"
                                                                                                                data-setting_assigned_users ="<?php echo $superAdminData['settingAssignedUsers']; ?>"
                                                                                                                data-setting_controlling_users ="<?php echo $superAdminData['settingControllingUsers']; ?>"
                                                                                                            >
                                                                                                                <i class="fa fa-pencil-square" aria-hidden="true"></i> 
                                                                                                                Controlling korisnici
                                                                                                            </a>
                                                                                                        </li>
                                                                                                    </ul>
                                                                                                </div>
                                                                                            </td>
                                                                                        </tr>
                                                                                    <?php 
                                                                                }
                                                                                unset($superAdminData); 
                                                                            ?>
                                                                        </tbody>
                                                                        <tfoot>
                                                                            <tr>
                                                                                <th class="text-center">Tip</th>
                                                                                <th class="text-center">Naziv</th>
                                                                                <th class="text-center">Dokument</th>
                                                                                <th class="text-center">Partner Name</th>
                                                                                <th class="text-center">Korisnici</th>
                                                                                <th class="text-center">Controlling korisnici</th>
                                                                                <th class="text-center">Akcija</th>
                                                                            </tr>
                                                                        </tfoot>
                                                                    </table>
                                                                <?php
                                                            } else {
                                                                ?>
                                                                    <div class="alert alert-warning text-center" role="alert">
                                                                        Nema aktivnih postavki za SuperAdmin usera!
                                                                    </div>
                                                                <?php
                                                            }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <!-- 
                                    SuperAdmin END 
                                -->
                                <hr>
                                <!-- 
                                    Admin START 
                                    -->
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="row">
                                                    <div class="col-xs-12">
                                                        <h5 style = "font-weight: bold;">Admin</h5>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-xs-12">
                                                        <?php 
                                                            if (isset($groupedData['Admin']) AND isset($groupedData['Admin']['data'])){
                                                                ?>
                                                                    <script>
                                                                        $(document).ready(function(){
                                                                            $('#admin').DataTable({
                                                                                responsive: true,
														                        "order": [[ 0, "asc" ]],
                                                                                "bAutoWidth": false,
                                                                            });
                                                                        });
                                                                    </script>
                                                                    <table id="admin" class="display" cellspacing="0" style="width:100%">
                                                                        <thead>
                                                                            <tr>
                                                                                <th class="text-center">Tip</th>
                                                                                <th class="text-center">Naziv</th>
                                                                                <th class="text-center">Dokument</th>
                                                                                <th class="text-center">Partner Name</th>
                                                                                <th class="text-center">Korisnici</th>
                                                                                <th class="text-center">Controlling korisnici</th>
                                                                                <th class="text-center">Akcija</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <?php 
                                                                                foreach ($groupedData['Admin']['data'] AS $adminData) {
                                                                                    ?>
                                                                                        <tr>
                                                                                            <td class="text-center"><?php echo $adminData['reminderType']; ?></td>
                                                                                            <td class="text-center"><?php echo getReminderLabelRS($adminData['reminderType'], $adminData['reminderTypeName']); ?></td>
                                                                                            <td class="text-center">
                                                                                                <?php 
                                                                                                    if ($adminData['reminderHasDocuments'] != 'no documents') {
                                                                                                        $reminderDocumentsName = array();
                                                                                                        foreach ($adminData['groupSetting']['data'] AS $adminGroupSettingData) {
                                                                                                            array_push($reminderDocumentsName, getDocumentStatusLabelRS($adminGroupSettingData['settingOrderDocumentStatus'], $adminGroupSettingData['reminderDocumentName']));
                                                                                                        }
                                                                                                        echo implode(' ', $reminderDocumentsName); 
                                                                                                        unset($adminGroupSettingData);
                                                                                                        unset($reminderDocumentsName); 
                                                                                                    } else { 
                                                                                                        echo '<i class="fa fa-info-circle" aria-hidden="true" title="Ovaj reminder nije vezan za dokumente!"></i>';  
                                                                                                    }; 
                                                                                                ?>
                                                                                            </td>
                                                                                            <td class="text-center"><?php echo (($adminData['settingPartnerName'] != 'No partner') ? $adminData['settingPartnerName'] : '<i class="fa fa-info-circle" aria-hidden="true" title="Ovaj reminder nije vezan za partnere!"></i>'); ?></td>
                                                                                            <td class="text-center"><?php echo (($adminData['settingAssignedUsersInfo'] != 'Undefined') ? $adminData['settingAssignedUsersInfo'] : '<i class="fa fa-info-circle" aria-hidden="true" title="Ovaj reminder nema postavljenih usera!"></i>'); ?></td>
                                                                                            <td class="text-center"><?php echo (($adminData['settingControllingUsersInfo'] != 'Undefined') ? $adminData['settingControllingUsersInfo'] : '<i class="fa fa-info-circle" aria-hidden="true" title="Ovaj reminder nema postavljenih kontroling usera!"></i>'); ?></td>
                                                                                            <td class="text-center">
                                                                                                <div class="btn-group material-btn-group">
                                                                                                    <button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i><span class="caret material-btn__caret"></span></button>
                                                                                                    <ul style = "top:33px; left:-60px; min-width:185px !important;" class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
                                                                                                        <li>
                                                                                                            <a 
                                                                                                                class="material-dropdown-menu__link" 
                                                                                                                onclick="changeUsersRS(this)"
                                                                                                                data-change_user_type="users"
                                                                                                                data-reminder_type ="<?php echo $adminData['reminderType']; ?>"
                                                                                                                data-reminder_user_type ="<?php echo $adminData['reminderUserType']; ?>"
                                                                                                                data-setting_id ="<?php echo $adminData['groupSetting']['settingId']; ?>"
                                                                                                                data-setting_order_id ="<?php echo $adminData['settingOrderId']; ?>"
                                                                                                                data-setting_partner_id ="<?php echo $adminData['settingPartnerId']; ?>"
                                                                                                                data-setting_assigned_users ="<?php echo $adminData['settingAssignedUsers']; ?>"
                                                                                                                data-setting_controlling_users ="<?php echo $adminData['settingControllingUsers']; ?>"
                                                                                                            >
                                                                                                                <i class="fa fa-pencil-square" aria-hidden="true"></i> 
                                                                                                                Korisnici
                                                                                                            </a>
                                                                                                        </li>
                                                                                                        <li>
                                                                                                            <a 
                                                                                                                class="material-dropdown-menu__link" 
                                                                                                                onclick="changeUsersRS(this)"
                                                                                                                data-change_user_type="controllingUsers"
                                                                                                                data-reminder_type ="<?php echo $adminData['reminderType']; ?>"
                                                                                                                data-reminder_user_type ="<?php echo $adminData['reminderUserType']; ?>"
                                                                                                                data-setting_id ="<?php echo $adminData['groupSetting']['settingId']; ?>"
                                                                                                                data-setting_order_id ="<?php echo $adminData['settingOrderId']; ?>"
                                                                                                                data-setting_partner_id ="<?php echo $adminData['settingPartnerId']; ?>"
                                                                                                                data-setting_assigned_users ="<?php echo $adminData['settingAssignedUsers']; ?>"
                                                                                                                data-setting_controlling_users ="<?php echo $adminData['settingControllingUsers']; ?>"
                                                                                                            >
                                                                                                                <i class="fa fa-pencil-square" aria-hidden="true"></i> 
                                                                                                                Controlling korisnici
                                                                                                            </a>
                                                                                                        </li>
                                                                                                    </ul>
                                                                                                </div>
                                                                                            </td>
                                                                                        </tr>
                                                                                    <?php 
                                                                                }
                                                                            ?>
                                                                        </tbody>
                                                                        <tfoot>
                                                                            <tr>
                                                                                <th class="text-center">Tip</th>
                                                                                <th class="text-center">Naziv</th>
                                                                                <th class="text-center">Dokument</th>
                                                                                <th class="text-center">Partner Name</th>
                                                                                <th class="text-center">Korisnici</th>
                                                                                <th class="text-center">Controlling korisnici</th>
                                                                                <th class="text-center">Akcija</th>
                                                                            </tr>
                                                                        </tfoot>
                                                                    </table>
                                                                <?php
                                                            } else {
                                                                ?>
                                                                    <div class="alert alert-warning text-center" role="alert">
                                                                        Nema aktivnih postavki za Admin usera!
                                                                    </div>
                                                                <?php
                                                            }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <!-- 
                                    Admin END 
                                -->
                                <hr>
                                <!-- 
                                    ProjectManager START 
                                    -->
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="row">
                                                    <div class="col-xs-12">
                                                        <h5 style = "font-weight: bold;">Project Manager</h5>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-xs-12">
                                                        <?php 
                                                            if (isset($groupedData['ProjectManager']) AND isset($groupedData['ProjectManager']['data'])){
                                                                ?>
                                                                    <script>
                                                                        $(document).ready(function(){
                                                                            $('#projectManager').DataTable({
                                                                                responsive: true,
														                        "order": [[ 0, "asc" ]],
                                                                                "bAutoWidth": false,
                                                                            });
                                                                        });
                                                                    </script>
                                                                    <table id="projectManager" class="display" cellspacing="0" style="width:100%">
                                                                        <thead>
                                                                            <tr>
                                                                                <th class="text-center">Tip</th>
                                                                                <th class="text-center">Naziv</th>
                                                                                <th class="text-center">Dokument</th>
                                                                                <th class="text-center">Partner Name</th>
                                                                                <th class="text-center">Korisnici</th>
                                                                                <th class="text-center">Controlling korisnici</th>
                                                                                <th class="text-center">Akcija</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <?php 
                                                                                foreach ($groupedData['ProjectManager']['data'] AS $projectManagerData) {
                                                                                    ?>
                                                                                        <tr>
                                                                                            <td class="text-center"><?php echo $projectManagerData['reminderType']; ?></td>
                                                                                            <td class="text-center"><?php echo getReminderLabelRS($projectManagerData['reminderType'], $projectManagerData['reminderTypeName']); ?></td>
                                                                                            <td class="text-center">
                                                                                                <?php 
                                                                                                    if ($projectManagerData['reminderHasDocuments'] != 'no documents') {
                                                                                                        $reminderDocumentsName = array();
                                                                                                        foreach ($projectManagerData['groupSetting']['data'] AS $projectManagerGroupSettingData) {
                                                                                                            array_push($reminderDocumentsName, getDocumentStatusLabelRS($projectManagerGroupSettingData['settingOrderDocumentStatus'], $projectManagerGroupSettingData['reminderDocumentName']));
                                                                                                        }
                                                                                                        echo implode(' ', $reminderDocumentsName); 
                                                                                                        unset($projectManagerGroupSettingData);
                                                                                                        unset($reminderDocumentsName); 
                                                                                                    } else { 
                                                                                                        echo '<i class="fa fa-info-circle" aria-hidden="true" title="Ovaj reminder nije vezan za dokumente!"></i>';  
                                                                                                    }; 
                                                                                                ?>
                                                                                            </td>
                                                                                            <td class="text-center"><?php echo (($projectManagerData['settingPartnerName'] != 'No partner') ? $projectManagerData['settingPartnerName'] : '<i class="fa fa-info-circle" aria-hidden="true" title="Ovaj reminder nije vezan za partnere!"></i>'); ?></td>
                                                                                            <td class="text-center"><?php echo (($projectManagerData['settingAssignedUsersInfo'] != 'Undefined') ? $projectManagerData['settingAssignedUsersInfo'] : '<i class="fa fa-info-circle" aria-hidden="true" title="Ovaj reminder nema postavljenih usera!"></i>'); ?></td>
                                                                                            <td class="text-center"><?php echo (($projectManagerData['settingControllingUsersInfo'] != 'Undefined') ? $projectManagerData['settingControllingUsersInfo'] : '<i class="fa fa-info-circle" aria-hidden="true" title="Ovaj reminder nema postavljenih kontroling usera!"></i>'); ?></td>
                                                                                            <td class="text-center">
                                                                                                <div class="btn-group material-btn-group">
                                                                                                    <button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i><span class="caret material-btn__caret"></span></button>
                                                                                                    <ul style = "top:33px; left:-60px; min-width:185px !important;" class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
                                                                                                        <li>
                                                                                                            <a 
                                                                                                                class="material-dropdown-menu__link" 
                                                                                                                onclick="changeUsersRS(this)"
                                                                                                                data-change_user_type="users"
                                                                                                                data-reminder_type ="<?php echo $projectManagerData['reminderType']; ?>"
                                                                                                                data-reminder_user_type ="<?php echo $projectManagerData['reminderUserType']; ?>"
                                                                                                                data-setting_id ="<?php echo $projectManagerData['groupSetting']['settingId']; ?>"
                                                                                                                data-setting_order_id ="<?php echo $projectManagerData['settingOrderId']; ?>"
                                                                                                                data-setting_partner_id ="<?php echo $projectManagerData['settingPartnerId']; ?>"
                                                                                                                data-setting_assigned_users ="<?php echo $projectManagerData['settingAssignedUsers']; ?>"
                                                                                                                data-setting_controlling_users ="<?php echo $projectManagerData['settingControllingUsers']; ?>"
                                                                                                            >
                                                                                                                <i class="fa fa-pencil-square" aria-hidden="true"></i> 
                                                                                                                Korisnici
                                                                                                            </a>
                                                                                                        </li>
                                                                                                        <li>
                                                                                                            <a 
                                                                                                                class="material-dropdown-menu__link" 
                                                                                                                onclick="changeUsersRS(this)"
                                                                                                                data-change_user_type="controllingUsers"
                                                                                                                data-reminder_type ="<?php echo $projectManagerData['reminderType']; ?>"
                                                                                                                data-reminder_user_type ="<?php echo $projectManagerData['reminderUserType']; ?>"
                                                                                                                data-setting_id ="<?php echo $projectManagerData['groupSetting']['settingId']; ?>"
                                                                                                                data-setting_order_id ="<?php echo $projectManagerData['settingOrderId']; ?>"
                                                                                                                data-setting_partner_id ="<?php echo $projectManagerData['settingPartnerId']; ?>"
                                                                                                                data-setting_assigned_users ="<?php echo $projectManagerData['settingAssignedUsers']; ?>"
                                                                                                                data-setting_controlling_users ="<?php echo $projectManagerData['settingControllingUsers']; ?>"
                                                                                                            >
                                                                                                                <i class="fa fa-pencil-square" aria-hidden="true"></i> 
                                                                                                                Controlling korisnici
                                                                                                            </a>
                                                                                                        </li>
                                                                                                    </ul>
                                                                                                </div>
                                                                                            </td>
                                                                                        </tr>
                                                                                    <?php 
                                                                                }
                                                                            ?>
                                                                        </tbody>
                                                                        <tfoot>
                                                                            <tr>
                                                                                <th class="text-center">Tip</th>
                                                                                <th class="text-center">Naziv</th>
                                                                                <th class="text-center">Dokument</th>
                                                                                <th class="text-center">Partner Name</th>
                                                                                <th class="text-center">Korisnici</th>
                                                                                <th class="text-center">Controlling korisnici</th>
                                                                                <th class="text-center">Akcija</th>
                                                                            </tr>
                                                                        </tfoot>
                                                                    </table>
                                                                <?php
                                                            } else {
                                                                ?>
                                                                    <div class="alert alert-warning text-center" role="alert">
                                                                        Nema aktivnih postavki za Project Manager usera!
                                                                    </div>
                                                                <?php
                                                            }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <!-- 
                                    ProjectManager END 
                                -->
                            </div>
                        </div>
                    <!-- 
                    Content END
                -->
                <!-- 
                    Modal START 
                    -->
                        <div class="modal material-modal material-modal_primary fade text-left" id="changeUsersRS">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content material-modal__content">
                                    <div class="modal-header material-modal__header">
                                        <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title material-modal__title"><i style = "margin-right: 10px;" class="fa fa-pencil-square" aria-hidden="true"></i><span id="modal-title-text"></span></h4>
                                    </div> 
                                    <div class="modal-body material-modal__body">
                                        <form action="<?php getSiteURL(); ?>jobsoft_settings/backend/do_settings.php?page=change_reminder_users" enctype="multipart/form-data" method="post" role="form" class="form-horizontal" id="changeUsersRSForm">
                                            <input type="hidden" id="change_user_type" name="change_user_type">
                                            <input type="hidden" id="reminder_type" name="reminder_type">
                                            <input type="hidden" id="reminder_user_type" name="reminder_user_type">
                                            <input type="hidden" id="setting_id" name="setting_id">
                                            <input type="hidden" id="setting_order_id" name="setting_order_id">
                                            <input type="hidden" id="setting_partner_id" name="setting_partner_id">
                                            <input type="hidden" id="setting_assigned_users" name="setting_assigned_users">
                                            <input type="hidden" id="setting_controlling_users" name="setting_controlling_users">
                                            <div class="form-group">
                                                <div class="col-md-offset-2 col-sm-8" id="required_alert">

                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-md-offset-2 col-sm-8">
                                                    <select class="selectpicker" id="setting_users_new" name="setting_assigned_users_new[]" data-live-search="true" title = "Odaberite korisnike" multiple>
                                                        
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer material-modal__footer">
                                                <button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
                                                <button type="submit" class="btn btn-primary material-btn material-btn_success" form="changeUsersRSForm"><i class="fa fa-check-square-o" aria-hidden="true"></i> Spremi</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <!-- 
                    Modal END
                -->
                <!-- 
                    Script START 
                    -->
                        <script>
                            $('#setting_users_new', '#changeUsersRSForm').on('change',function() {
                                if ($('#setting_users_new', '#changeUsersRSForm').val() === null) {
                                    if($('#setting_users_new', '#changeUsersRSForm').prop('required')) {
                                        $('#required_alert', '#changeUsersRSForm').html('<div class="alert alert-danger text-center" role="alert"><h4><strong>Upozorenje</strong></h4><br>Polje za označavanje korisnika je obavezno! Označite barem jednog korisnika kako bi mogli spremiti!</div>');
                                        $('#required_alert', '#changeUsersRSForm').show();
                                    } else {
                                        $('#required_alert', '#changeUsersRSForm').html('<div class="alert alert-warning text-center" role="alert"><h4><strong>Napomena</strong></h4><br>Kada su u pitanju controlling korisnici, moguće je spremiti prazno polje! Time se postavlja aktivni reminder bez controlling korisnika!</div>');
                                        $('#required_alert', '#changeUsersRSForm').show();
                                    }
                                } else {
                                    if ($('#required_alert', '#changeUsersRSForm').show()) {
                                        $('#required_alert', '#changeUsersRSForm').html('');
                                        $('#required_alert', '#changeUsersRSForm').hide();
                                    }
                                }
                            });

                            function resetFormData() {
                                getModalTitle('');
                                $('#changeUsersRSForm :input:hidden').removeAttr('value');
                                $('#setting_users_new option', '#changeUsersRSForm').remove();
                                $('#setting_users_new', '#changeUsersRSForm').selectpicker('refresh');
                                $('#required_alert', '#changeUsersRSForm').html('');
                                $('#required_alert', '#changeUsersRSForm').hide();
                            }; 

                            function getModalTitle(change_user_type) {
                                var result = '';
                                if (change_user_type === 'users') {
                                    result = 'Uredi korisnike'; 
                                } else if (change_user_type === 'controllingUsers') {
                                    result = 'Uredi controlling korisnike'; 
                                } else {
                                    result = '';
                                }
                                $('#modal-title-text', '#changeUsersRS').text(result);
                            };

                            function setRequiredOnModalSelect(change_user_type) {
                                var result = false;
                                if (change_user_type === 'users') {
                                    var result = true;
                                } else if (change_user_type === 'controllingUsers') {
                                    var result = false; 
                                } else {
                                    var result = false;
                                }
                                $('#setting_users_new', '#changeUsersRSForm').prop('required',result).selectpicker('refresh');
                            }

                            function getUsersForReminder(form_data) {
                                $.ajax({
                                    url: 'jobsoft_settings/backend/do_settings.php?page=get_users_for_reminder',
                                    type: 'POST',
                                    processData: false,
                                    contentType: false,
                                    cache: false,
                                    data:form_data,
                                    success : function (result){
                                        if (result == '') {
                                            resetFormData();
                                            alert("Postoji problem sa otvaranjem modala za Setup korisnika za remindere! Molimo, obratite se administratoru sistema!");
                                        } else if (result == 'No results found for query!') {
                                            resetFormData();
                                            alert("Ne postoji ni jedan user sa aktivnom permisijom koji se može postaviti na ovaj reminder! Molimo, izvršite dodavanje pristupa za usera/e na JobSoft postavke - Postavke pristupa ili se obratite se administratoru sistema!");
                                        } else {
                                            $("#setting_users_new", '#changeUsersRSForm').html(result).selectpicker('refresh');
                                            $('#changeUsersRS').modal('show');
                                        }
                                    },
                                    error: function (xhr, ajaxOptions, thrownError) {
                                        alert(xhr.status);
                                        alert(thrownError);
                                    }
                                });
                            };

                            function changeUsersRS(thisRow) {
                                var change_user_type = $(thisRow).data('change_user_type');
                                var reminder_type = $(thisRow).data('reminder_type');
                                var reminder_user_type = $(thisRow).data('reminder_user_type');
                                var setting_id = $(thisRow).data('setting_id');
                                var setting_order_id = $(thisRow).data('setting_order_id');
                                var setting_partner_id = $(thisRow).data('setting_partner_id');
                                var setting_assigned_users = $(thisRow).data('setting_assigned_users');
                                var setting_controlling_users = $(thisRow).data('setting_controlling_users'); 

                                getModalTitle(change_user_type);
                                setRequiredOnModalSelect(change_user_type); 

                                var form_data = new FormData();

                                form_data.append('change_user_type', change_user_type);
                                form_data.append('reminder_type', reminder_type);
                                form_data.append('reminder_user_type', reminder_user_type);
                                form_data.append('setting_id', setting_id);
                                form_data.append('setting_order_id', setting_order_id);
                                form_data.append('setting_partner_id', setting_partner_id);
                                form_data.append('setting_assigned_users', setting_assigned_users);
                                form_data.append('setting_controlling_users', setting_controlling_users);

                                getUsersForReminder(form_data);

                                form_data.forEach((value, key) => {
                                    $("#" + key, '#changeUsersRSForm').val(value);
                                });
                                
                            };
                            $('#changeUsersRS').on('hidden.bs.modal', function () {
                                resetFormData();
                            });
                        </script>
                    <!-- 
                    Script END  
                -->
            <?php 

            unset($groupedData); 

        } else {
            ?> 
                <div class="row">
                    <div class="col-xs-12">
                        <div class="alert alert-warning text-center" role="alert">
                            <h4><strong>Upozorenje</strong></h4>
                            <br>
                            Nema aktivnih postavki remindera za ovaj nalog!
                        </div>
                    </div>
                </div>
            <?php 
        }
        unset($rowAllUsersCRM);
        unset($rowAllAccessPP); 
        unset($rowAllOrderDocuments); 
    } else {
        ?> 
            <div class="row">
                <div class="col-xs-12">
                    <div class="alert alert-danger text-center" role="alert">
						<h4><strong>Greška</strong></h4>
						<br>
						Problem sa skriptom za setup remindera! Obratite se administratoru sistema!
					</div>
                </div>
            </div>
        <?php 
    }
?>