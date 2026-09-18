<?php 
    include($_SERVER['DOCUMENT_ROOT'].'/includes/functions.php');
    if ($logged_employee_id != 0) {
        
        $page = $_GET['page']; 
        
        switch($page) {

            case 'activate_order_pp':
                $order_id = $_GET["order_id"] ?? null; 
                $company_id = intval(getCompanyIdByNalogId($order_id));

                if ($order_id != null AND $company_id != 0) {
                    $queryUpdateOrderPP = $db->prepare("
                        UPDATE 
                            idk_nalozi 
                        SET 
                            pristup_poslodavcima = :pristup_poslodavcima
                        WHERE 
                            nalog_id = :nalog_id
                    "); 
                    $queryUpdateOrderPP->execute(array(
                        ':pristup_poslodavcima'     => 1, 
                        ':nalog_id'                 => $order_id
                    ));

                    
                    $query_insert_partner = $db->prepare("
                        INSERT INTO
                            idk_pp_partners
                            ( ppa_nalog_id, ppa_company_id, ppa_status)
                        VALUES
                            (:nalog_id, :company_id, :status)
                    ");
                    $query_insert_partner->execute(array(
                        ":nalog_id" 	=> $order_id,
                        ":company_id" 	=> $company_id,
                        ":status" 		=> 1
                    ));

                    $partner_id = $db->lastInsertId();

                    //Add our employees to Jobsoft Access
                    $user_ids = array(18, 84, 134, 136, 130);
                    
                    foreach($user_ids as $user_id){
                        $query_insert_super_admin = $db->prepare('
                            INSERT INTO idk_pp_user_access
                            (
                                pua_type,
                                pua_nalog_id, 
                                pua_user_id, 
                                pua_permission_id, 
                                pua_status
                            )
                            VALUES
                            (
                                :pua_type,
                                :pua_nalog_id, 
                                :pua_user_id, 
                                :pua_permission_id, 
                                :pua_status
                            )
                        ');
                        $query_insert_super_admin->execute(array(
                            ':pua_type' => 1, 
                            ':pua_nalog_id' => $order_id, 
                            ':pua_user_id' => $user_id,  
                            ':pua_permission_id' => 1, 
                            ':pua_status' => 1
                        ));

                        $query_insert_admin = $db->prepare('
                            INSERT INTO idk_pp_user_access
                            (
                                pua_type,
                                pua_nalog_id, 
                                pua_partner_id,
                                pua_user_id, 
                                pua_permission_id, 
                                pua_status
                            )
                            VALUES
                            (
                                :pua_type,
                                :pua_nalog_id, 
                                :pua_partner_id,
                                :pua_user_id, 
                                :pua_permission_id, 
                                :pua_status
                            )
                        ');
                        $query_insert_admin->execute(array(
                            ':pua_type' => 2, 
                            ':pua_nalog_id' => $order_id, 
                            ':pua_partner_id' => $partner_id,
                            ':pua_user_id' => $user_id,  
                            ':pua_permission_id' => 2, 
                            ':pua_status' => 1
                        ));
                    }
                }

                header('Location: ../../nalozi?page=open&id='.$order_id.'&tab=jobstep_pp_settings');

            break;

            case 'get_user_info': 
                $user_id = $_POST["user_id"] ?? null; 

                $result = array(
                    'status' => 0, 
                    'message' => 'default', 
                    'data' => array()
                );

                if ($user_id != null) {
                    $query = $db->prepare("
                        SELECT 
                            pu.pu_id                                AS userId, 
                            pu.pu_status                            AS userStatusValue,
                            (
                                CASE 
                                    WHEN pu.pu_status = 0 THEN 'Inactive'
                                    WHEN pu.pu_status = 1 THEN 'Active'
                                    ELSE 'Undefined'
                                END 
                            )                                       AS userStatusText,
                            pu.pu_fname                             AS userFirstName,
                            pu.pu_lname                             AS userLastName, 
                            pu.pu_email                             AS userEmail, 
                            pu.pu_language                          AS userLanguageValue, 
                            (
                                CASE 
                                    WHEN pu.pu_language = 0 THEN 'Bosnian/Croatian/Serbian'
                                    WHEN pu.pu_language = 1 THEN 'German'
                                    WHEN pu.pu_language = 2 THEN 'English'
                                    ELSE 'Undefined'
                                END 
                            )                                       AS userLanguageText,
                            pu.pu_gender                            AS userGenderValue, 
                            (
                                CASE 
                                    WHEN pu.pu_gender LIKE 'Male' THEN 'Male'
                                    WHEN pu.pu_gender LIKE 'Female' THEN 'Female'
                                    ELSE 'Undefined'
                                END 
                            )                                       AS userGenderText 
                        FROM 
                            idk_pp_users pu 
                        WHERE 
                            pu.pu_id = :pu_id
                    ");
                    $query->execute(array(
                        ':pu_id' => $user_id
                    ));
                    if ($query->rowCount() == 1) {
                        $row = $query->fetchAll(PDO::FETCH_ASSOC);
                        $result['status'] = 1; 
                        $result['message'] = 'Uspješno učitani podaci.';
                        array_push($result['data'], $row[0]);
                        unset($row);
                    } else {
                        $result['status'] = 101;
                        $result['message'] = 'Postoji problem prilikom učitavanja podataka o korisniku. Upit nije pronašao korisnika i zbog toga nije moguće pokrenuti opciju "Uredi korisnika".';
                    }
                } else {
                    $result['status'] = 100; 
                    $result['message'] = 'Postoji problem prilikom učitavanja podataka o korisniku. Zbog toga nije moguće pokrenuti opciju "Uredi korisnika".';
                }

                echo json_encode($result);
            break;
            
            case 'edit_user_info':
                $company_id = $_POST['company_id'] ?? null;
                $user_id = $_POST['user_id'] ?? null;
                $user_first_name = $_POST['user_first_name'] ?? null;
                $user_last_name = $_POST['user_last_name'] ?? null;
                $user_email = $_POST['user_email'] ?? null;
                $user_language = $_POST['user_language'] ?? null;
                $user_gender = $_POST['user_gender'] ?? null;
                $user_status = $_POST['user_status'] ?? null;

                $result = 0;
                $log_desc = 'Company Users -> Zaposlenik izvršio edit informacija JobSoft korisnika sa ID = ['.$user_id.'] za kompaniju sa ID = ['.$company_id.']. ';
                if ($company_id != null AND $user_id != null AND $user_first_name != null AND $user_last_name != null AND $user_email != null AND $user_language != null AND $user_gender != null AND $user_status != null) {
                    $queryCheckUser = $db->prepare("
                        SELECT 
                            pu.pu_id
                        FROM 
                            idk_pp_users pu 
                        WHERE 
                            pu.pu_id = :pu_id 
                    ");
                    $queryCheckUser->execute(array(
                        ':pu_id' => $user_id
                    ));
                    if ($queryCheckUser->rowCount() == 1) {
                        $queryUpdateUser = $db->prepare("
                            UPDATE 
                                idk_pp_users 
                            SET 
                                pu_fname = :pu_fname,
                                pu_lname = :pu_lname, 
                                pu_email = :pu_email, 
                                pu_language = :pu_language, 
                                pu_gender = :pu_gender,
                                pu_status = :pu_status
                            WHERE 
                                pu_id = :pu_id
                        "); 
                        $queryUpdateUser->execute(array(
                            ':pu_fname'     => $user_first_name, 
                            ':pu_lname'     => $user_last_name, 
                            ':pu_email'     => $user_email,
                            ':pu_language'  => $user_language,
                            ':pu_gender'    => $user_gender,
                            ':pu_status'    => $user_status,
                            ':pu_id'        => $user_id
                        ));
                        $row_affected_count = $queryUpdateUser->rowCount();
                        if($row_affected_count == 1) {
                            $result = 5;
                            $log_desc = $log_desc. 'Detalji: {"pu_fname" => '.$user_first_name.', "pu_lname" => '.$user_last_name.', "pu_email" => '.$user_email.', "pu_language" => '.$user_language.', "pu_gender" => '.$user_gender.', "pu_status" => '.$user_status.'}. Success: Ažuriranje uspješno izvršeno!';
                            addToLogs($log_desc, 0);
                        } else if ($row_affected_count > 1) {
                            /* Slučaj koji se vjerovatno neće nikad desiti zbog provjere sa queryCheckUser - ali je bez obzira pokriven. */
                            $result = 4;
                            $log_desc = $log_desc. 'Detalji: {"pu_fname" => '.$user_first_name.', "pu_lname" => '.$user_last_name.', "pu_email" => '.$user_email.', "pu_language" => '.$user_language.', "pu_gender" => '.$user_gender.', "pu_status" => '.$user_status.'}. Error: Sistem je pronašao više korisnika nad kojima je izvršio ažuriranje! Korisnik je obaviješten da se odmah obrati administratoru sistema!';
                            addToLogs($log_desc, 0);
                        } else {
                            $result = 3; 
                        }
                    } else {
                        $result = 2;
                    }
                } else {
                    $result = 1;
                }

                header('Location: ../../companies?page=open&id='.$company_id.'&tab=company_users&company_users_message='.$result.'');
            break; 

            case 'add_company_user': 
                $company_id         = $_POST['company_id'] ?? null;
                $user_first_name    = $_POST['user_first_name'] ?? null;
                $user_last_name     = $_POST['user_last_name'] ?? null;
                $user_email         = $_POST['user_email'] ?? null;
                $user_password      = $_POST['user_password'] ?? null;
                $user_language      = $_POST['user_language'] ?? null;
                $user_gender        = $_POST['user_gender'] ?? null;
                $user_key           = md5(date("YmdHis").rand().date("siHdmY"));
                $result = 0;
                $log_desc = 'Company Users -> Zaposlenik dodao novog JobSoft korisnika za kompaniju sa ID = ['.$company_id.']. ';
                if ($company_id != null AND $user_first_name != null AND $user_last_name != null AND $user_email != null AND $user_password != null AND $user_language != null AND $user_gender != null AND $user_key != '') {
                    
                    $queryInsertUser = $db->prepare("
                        INSERT INTO
                            idk_pp_users
                            (
                                pu_fname,
                                pu_lname,
                                pu_email,
                                pu_password,
                                pu_key,
                                pu_gender,
                                pu_language,
                                pu_company_id
                            )
                        VALUES
                            (
                                :pu_fname,
                                :pu_lname,
                                :pu_email,
                                :pu_password,
                                :pu_key,
                                :pu_gender,
                                :pu_language,
                                :pu_company_id
                            )
                    ");
                    $queryInsertUser->execute(array(
                        ':pu_fname' => $user_first_name,
                        ':pu_lname' => $user_last_name,
                        ':pu_email' => $user_email,
                        ':pu_password' => md5($user_password),
                        ':pu_key' => $user_key,
                        ':pu_gender' => $user_gender,
                        ':pu_language' => $user_language,
                        ':pu_company_id' => $company_id
                    ));
                    $user_id = $db->lastInsertId();
                    if ($user_id != 0) {
                        $result = 8; 
                        $log_desc = $log_desc. 'Detalji: {"pu_id" => '.$user_id.', "pu_fname" => '.$user_first_name.', "pu_lname" => '.$user_last_name.', "pu_email" => '.$user_email.', "pu_language" => '.$user_language.', "pu_gender" => '.$user_gender.'}. Success: Korisnik uspješno dodan!';
                        addToLogs($log_desc, 0);
                    } else {
                        $result = 7; 
                        $log_desc = $log_desc. 'Detalji: {"pu_fname" => '.$user_first_name.', "pu_lname" => '.$user_last_name.', "pu_email" => '.$user_email.', "pu_language" => '.$user_language.', "pu_gender" => '.$user_gender.'}. Error: Korisnik nije dodan!';
                        addToLogs($log_desc, 0);
                    }
                } else {
                    $result = 6; 
                }
                
                header('Location: ../../companies?page=open&id='.$company_id.'&tab=company_users&company_users_message='.$result.'');
            break; 

            case 'get_user_access':
                $user_id = $_POST['user_id'] ?? null;

                if ($user_id != null) {
                    $queryUserAccess = $db->prepare('
                        SELECT 
                            pua.pua_id AS accessId,   
                            pua.pua_type AS accessTypeValue, 
                            (
                                CASE 
                                    WHEN pua.pua_type = 1 THEN "SuperAdmin"
                                    WHEN pua.pua_type = 2 THEN "Admin"
                                    ELSE "Undefined"
                                END
                            ) AS accessTypeText,
                            pua.pua_status AS accessStatusValue, 
                            (
                                CASE 
                                    WHEN pua.pua_status = 0 THEN "Inactive"
                                    WHEN pua.pua_status = 1 THEN "Active"
                                    ELSE "Undefined"
                                END
                            ) AS accessStatusText, 
                            pua.pua_nalog_id AS accessOrderValue, 
                            nal.nalog_naziv AS accessOrderText,
                            nal.kompanija_id AS accessOrderCompanyValue, 
                            cnal.company_name AS accessOrderCompanyText, 
                            pua.pua_partner_id AS accessPartnerValue, 
                            (
                                CASE 
                                    WHEN pua.pua_partner_id is not null THEN cpp.company_name
                                    ELSE "Undefined"
                                END 
                            ) AS accessPartnerText
                        FROM 
                            idk_pp_user_access pua 
                        JOIN 
                            idk_nalozi nal
                        ON 
                            pua.pua_nalog_id = nal.nalog_id
                        JOIN 
                            idk_companies cnal
                        ON 
                            nal.kompanija_id = cnal.company_id
                        LEFT JOIN 
                            idk_pp_partners pp
                        ON 
                            pua.pua_partner_id = pp.ppa_id
                        LEFT JOIN 
                            idk_companies cpp
                        ON 
                            pp.ppa_company_id = cpp.company_id
                        WHERE 
                            pua.pua_user_id = :pua_user_id
                    ');
                    $queryUserAccess->execute(array(
                        ':pua_user_id' => $user_id
                    ));
                    if ($queryUserAccess->rowCount() > 0) {
                        $rowUserAccess = $queryUserAccess->fetchAll(PDO::FETCH_ASSOC);
                        $groupedData = array();
                        foreach ($rowUserAccess as $row) {
                            $accessOrderValue = $row['accessOrderValue'];

                            if (!isset($groupedData[$accessOrderValue])) {
                                $groupedData[$accessOrderValue] = array(
                                    'accessOrderValue' => $row['accessOrderValue'],
                                    'accessOrderText' => $row['accessOrderText'],
                                    'accessOrderCompanyValue' => $row['accessOrderCompanyValue'],
                                    'accessOrderCompanyText' => $row['accessOrderCompanyText'],
                                    'SuperAdmin' => array('data' => array()),
                                    'Admin' => array('data' => array())
                                );
                            }

                            $accessTypeText = $row['accessTypeText'];

                            $data = array(
                                'accessId' => $row['accessId'],
                                'accessTypeValue' => $row['accessTypeValue'],
                                'accessTypeText' => $row['accessTypeText'],
                                'accessStatusValue' => $row['accessStatusValue'],
                                'accessStatusText' => $row['accessStatusText'],
                                'accessPartnerValue' => $row['accessPartnerValue'],
                                'accessPartnerText' => $row['accessPartnerText'],
                            );

                            $groupedData[$accessOrderValue][$accessTypeText]['data'][] = $data;
                            unset($accessOrderValue);
                            unset($accessTypeText);
                            unset($data);
                        }

                        //print("<pre>" . print_r($groupedData, true) . "</pre>");
                         
                            ?>
                                <style>
                                    #accessTable>tbody>tr>td, #accessTable>thead>tr>th {
                                        vertical-align: middle;
                                        word-break: break-all;
                                    }
                                </style>
                                <div class="table-responsive scrollBarHorizontal">
                                    <table class="table table-bordered table-sm" id = "accessTable">
                                        <thead>
                                            <th class="text-center">Nalog - Kompanija</th>
                                            <th class="text-center">Partner</th>
                                            <th class="text-center">Tip pristupa</th>
                                            <th class="text-center">Status</th>
                                        </thead>
                                        <tbody>
                                        <?php 
                                            foreach ($groupedData as $accessOrder) {
                                                $superAdminOrderInfoFlag = false;
                                                if (count($accessOrder['SuperAdmin']['data']) > 0){
                                                    foreach ($accessOrder['SuperAdmin']['data'] as $data) { 
                                                        ?>
                                                            <tr>
                                                                <?php 
                                                                    if (!$superAdminOrderInfoFlag) {
                                                                        ?>
                                                                            <td class="text-center wordbreak" rowspan="<?php echo (count($accessOrder['SuperAdmin']['data']) + count($accessOrder['Admin']['data'])); ?>">
                                                                                <a href="<?php getSiteURL(); ?>nalozi?page=open&id=<?php echo $accessOrder['accessOrderValue']; ?>&tab=jobstep_pp_settings&tabgs=userAccessSettings" target="_blank">
                                                                                    <?php echo $accessOrder['accessOrderText'] . ' - '. $accessOrder['accessOrderCompanyText']; ?>
                                                                                </a>
                                                                            </td>
                                                                        <?php
                                                                        $superAdminOrderInfoFlag = true; 
                                                                    }    
                                                                ?>
                                                                <td class="text-center"><span class="label label-default"><i class="fa fa-info-circle" title="SuperAdmin pristup nije vezan za partnera!" aria-hidden="true"></i></span></td>
                                                                <td class="text-center"><span class="label label-primary"><?php echo $data['accessTypeText']; ?></span></td>
                                                                <td class="text-center" ><?php echo (($data['accessStatusValue'] == 1) ? '<span class="label label-success">'.$data['accessStatusText'].'</span>' : '<span class="label label-danger">'.$data['accessStatusText'].'</span>'); ?></td>
                                                            </tr>
                                                        <?php 
                                                    }
                                                }
                                                $adminOrderInfoFlag = false;
                                                if (count($accessOrder['Admin']['data']) > 0){
                                                    foreach ($accessOrder['Admin']['data'] as $data) {
                                                        ?>
                                                            <tr>
                                                                <?php 
                                                                    if (count($accessOrder['SuperAdmin']['data']) == 0 AND $superAdminOrderInfoFlag == false AND !$adminOrderInfoFlag) {
                                                                        ?>
                                                                            <td class="text-center wordbreak" rowspan="<?php echo (count($accessOrder['Admin']['data'])); ?>">
                                                                                <a href="<?php getSiteURL(); ?>nalozi?page=open&id=<?php echo $accessOrder['accessOrderValue']; ?>&tab=jobstep_pp_settings&tabgs=userAccessSettings" target="_blank">
                                                                                    <?php echo $accessOrder['accessOrderText'] . ' - '. $accessOrder['accessOrderCompanyText']; ?>
                                                                                </a>
                                                                            </td>
                                                                        <?php 
                                                                        $adminOrderInfoFlag = true; 
                                                                    }    
                                                                ?>
                                                                
                                                                <td class="text-center"><span class="label label-info"><?php echo $data['accessPartnerText'];  ?></span></td>
                                                                <td class="text-center"><span class="label label-warning"><?php echo $data['accessTypeText']; ?></span></td>
                                                                <td class="text-center" ><?php echo (($data['accessStatusValue'] == 1) ? '<span class="label label-success">'.$data['accessStatusText'].'</span>' : '<span class="label label-danger">'.$data['accessStatusText'].'</span>'); ?></td>
                                                            </tr>
                                                        <?php 
                                                    }
                                                }
                                            }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php 

                        unset($groupedData);
                        unset($rowUserAccess);
                    } else {
                        echo '<div class="alert alert-warning text-center" role="alert"><h4><strong>Obavijest</strong></h4><br>Za traženog usera sistem nije pronašao pristupe ni na jednom nalogu!</div>';
                    }
                } else {
                    echo '';
                }
            break; 

            case 'add_new_user_access':
                $order_id = $_POST['order_id'] ?? null;
                $user_id = $_POST['user_id'] ?? null;
                $user_super_admin = $_POST['user_super_admin'] ?? null;
                $user_admin = $_POST['user_admin'] ?? null; 

                $result1 = 0; 
                $result2 = 0;
                $logDesc1 = 'JobSoft Postavke - Postavke pristupa - Dodaj pristup -> Zaposlenik dodao SuperAdmin pristup novog korisnika ID = ['.$user_id.'] za nalog ID = ['.$order_id.']. Detalji: ';
                $logDesc2 = 'JobSoft Postavke - Postavke pristupa - Dodaj pristup -> Zaposlenik dodao Admin pristup novog korisnika ID = ['.$user_id.'] za nalog ID = ['.$order_id.']. Detalji: ';
                if ($user_id != null AND $user_super_admin != null AND $order_id != null) {
                    /*
                        SuperAdmin Insert START
                    */
                        $query_insert_super_admin = $db->prepare('
                            INSERT INTO idk_pp_user_access
                            (
                                pua_type,
                                pua_nalog_id, 
                                pua_user_id, 
                                pua_permission_id, 
                                pua_status
                            )
                            VALUES
                            (
                                :pua_type,
                                :pua_nalog_id, 
                                :pua_user_id, 
                                :pua_permission_id, 
                                :pua_status
                            )
                        ');
                        $query_insert_super_admin->execute(array(
                            ':pua_type' => 1, 
                            ':pua_nalog_id' => $order_id, 
                            ':pua_user_id' => $user_id,  
                            ':pua_permission_id' => 1, 
                            ':pua_status' => $user_super_admin
                        ));
                        $superAdminAccessId = $db->lastInsertId();
                        if ($superAdminAccessId != 0) {
                            $logDesc1 = $logDesc1.'Izvršen insert pristupa na status vrijednost ['.(($user_super_admin) ? 'Active': 'Inactive').'] za pristup ID = ['.$superAdminAccessId.']';
                            addToLogs($logDesc1, 0);
                            $result1 = 7;
                        } else {
                            $result1 = 8;
                        }
                    /*
                        SuperAdmin Insert END
                    */
                    /*
                        Admin Update START 
                    */
                        if ($user_admin != null) {

                            $actionStatus = array();
                            $actionTypes = array();
                            $accessIds = array();
                            $accessStatuses = array();

                            foreach($user_admin as $partner_id) {
                                $query_insert_admin = $db->prepare('
                                    INSERT INTO idk_pp_user_access
                                    (
                                        pua_type,
                                        pua_nalog_id, 
                                        pua_partner_id,
                                        pua_user_id, 
                                        pua_permission_id, 
                                        pua_status
                                    )
                                    VALUES
                                    (
                                        :pua_type,
                                        :pua_nalog_id, 
                                        :pua_partner_id,
                                        :pua_user_id, 
                                        :pua_permission_id, 
                                        :pua_status
                                    )
                                ');
                                $query_insert_admin->execute(array(
                                    ':pua_type' => 2, 
                                    ':pua_nalog_id' => $order_id, 
                                    ':pua_partner_id' => $partner_id,
                                    ':pua_user_id' => $user_id,  
                                    ':pua_permission_id' => 2, 
                                    ':pua_status' => 1
                                ));
                                $adminAccessId = $db->lastInsertId();
                                if ($adminAccessId != 0) {
                                    array_push($actionStatus, 'Success');
                                    array_push($actionTypes, 'Insert new');
                                    array_push($accessIds, ''.$adminAccessId.'');
                                    array_push($accessStatuses, 'Active');
                                } else {
                                    array_push($actionStatus, 'Error');
                                    array_push($actionTypes, 'Insert new');
                                    array_push($accessIds, ''.$adminAccessId.'');
                                    array_push($accessStatuses, 'Active');
                                }
                                unset($adminAccessId); 
                            }

                            $actionStatusImp = '';
                            $actionTypesImp = '';
                            $accessIdsImp = '';
                            $accessStatusesImp = '';

                            if (count($accessIds) > 0) {
                                $actionStatusImp = implode(',',$actionStatus);
                                $actionTypesImp = implode(',',$actionTypes);
                                $accessIdsImp = implode(',',$accessIds);
                                $accessStatusesImp = implode(',',$accessStatuses);

                                if (in_array('Error', $actionStatus)) {
                                    $result2 = 5; 
                                } else {
                                    $result2 = 6;
                                }

                                $logDesc2 = $logDesc2.'{"AccessIds" => ('.$accessIdsImp.'), "ActionStatus" => ('.$actionStatusImp.'), "ActionTypes" => ('.$actionTypesImp.'), "AccessStatuses" => ('.$accessStatusesImp.')}';
                                addToLogs($logDesc2, 0);
                            }

                            unset($actionStatus);
                            unset($actionTypes);
                            unset($accessIds);
                            unset($accessStatuses);

                            unset($actionStatusImp);
                            unset($actionTypesImp);
                            unset($accessIdsImp);
                            unset($accessStatusesImp);
                        }
                    /*
                        Admin Update END 
                    */
                } else {
                    $result1 = 6; 
                    $result2 = 4;
                }

                if ($result1 != 0 AND $result2 != 0) {
                    header('Location: ../../nalozi?page=open&id='.$order_id.'&tab=jobstep_pp_settings&tabgs=userAccessSettings&message1UAS='.$result1.'&message2UAS='.$result2.'');
                } else if ($result1 == 0 AND $result2 != 0) {
                    header('Location: ../../nalozi?page=open&id='.$order_id.'&tab=jobstep_pp_settings&tabgs=userAccessSettings&message2UAS='.$result2.'');
                } else if ($result1 != 0 AND $result2 == 0) {
                    header('Location: ../../nalozi?page=open&id='.$order_id.'&tab=jobstep_pp_settings&tabgs=userAccessSettings&message1UAS='.$result1.'');
                } else {
                    header('Location: ../../nalozi?page=open&id='.$order_id.'&tab=jobstep_pp_settings&tabgs=userAccessSettings');
                }
            break;
            
            case 'get_user_access_value': 
                $user_id = $_POST['user_id'] ?? null;
                $user_company = $_POST['user_company'] ?? null;
                $order_id = $_POST['order_id'] ?? null; 

                $result = array(
                    'status' => 0, 
                    'message' => 'default', 
                    'data' => ''
                );

                if ($user_id != null AND $user_company != null AND $order_id != 0) {
                    $query = $db->prepare('
                        SELECT 
                            pua.pua_type AS accessType, 
                            pua.pua_partner_id AS accessPartnerId, 
                            pua.pua_status AS accessStatus
                        FROM 
                            idk_pp_user_access pua 
                        JOIN 
                            idk_pp_users pu 
                        ON 
                            pua.pua_user_id = pu.pu_id 
                        WHERE 
                            pua.pua_user_id = :userId
                            AND 
                            pua.pua_nalog_id = :orderId
                            AND 
                            pu.pu_company_id = :companyId
                        ORDER BY  
                            pua.pua_partner_id 
                        ASC
                    ');
                    $query->execute(array(
                        ':userId' => $user_id, 
                        ':companyId' => $user_company, 
                        ':orderId' => $order_id
                    )); 
                    if ($query->rowCount() > 0) {
                        $row = $query->fetchAll(PDO::FETCH_ASSOC);
                        $result['status'] = 1; 
                        $result['message'] = 'Uspješno učitani podaci.';
                        $result['data'] = $row;
                        unset($row);
                    } else {
                        $result['status'] = 101;
                        $result['message'] = 'Postoji problem prilikom učitavanja podataka o pristupima korisnika. Upit nije pronašao pristupe korisnika i zbog toga nije moguće pokrenuti opciju "Uredi pristupe".';
                    }

                } else {
                    $result['status'] = 100; 
                    $result['message'] = 'Postoji problem prilikom učitavanja podataka o pristupima korisnika. Zbog toga nije moguće pokrenuti opciju "Uredi pristupe".';
                }

                echo json_encode($result);
            break; 

            case 'edit_user_access_value':
                $user_id = $_POST['user_id'] ?? null;
                $user_company = $_POST['user_company'] ?? null;
                $order_id = $_POST['order_id'] ?? null; 

                $user_super_admin = $_POST['user_super_admin'] ?? null;
                $user_admin = $_POST['user_admin'] ?? null; 

                $result1 = 0;
                $result2 = 0;
                $logDesc1 = 'JobSoft Postavke - Postavke pristupa - Uredi pristup -> Zaposlenik uredio SuperAdmin pristup korisnika ID = ['.$user_id.'] za nalog ID = ['.$order_id.']. Detalji: ';
                $logDesc2 = 'JobSoft Postavke - Postavke pristupa - Uredi pristup -> Zaposlenik uredio Admin pristup korisnika ID = ['.$user_id.'] za nalog ID = ['.$order_id.']. Detalji: ';
                if ($user_id != null AND $user_company != null AND $order_id != null AND $user_super_admin != null) {

                    /*
                        SuperAdmin Update START
                    */
                        $superAdminAccessId = checkJobSoftUserAccess($user_id, $user_company, $order_id, 0, 1);

                        if ($superAdminAccessId['accessId'] != 0) {
                            if ($superAdminAccessId['accessStatus'] != $user_super_admin) {
                                $query_update_super_admin = $db->prepare('
                                    UPDATE 
                                        idk_pp_user_access
                                    SET 
                                        pua_status = '.$user_super_admin.'
                                    WHERE 
                                        pua_id = :superAdminAccessId
                                ');
                                $query_update_super_admin->execute(array(
                                    ':superAdminAccessId' => $superAdminAccessId['accessId']
                                ));
                                if ($query_update_super_admin->rowCount() == 1) {
                                    $logDesc1 = $logDesc1.'Izvršen update statusa na status vrijednost ['.(($user_super_admin) ? 'Active': 'Inactive').'] za pristup ID = ['.$superAdminAccessId['accessId'].']';
                                    addToLogs($logDesc1, 0);
                                    $result1 = 2; 
                                } else {
                                    $result1 = 3;
                                }
                            }
                        } else {
                            $query_insert_super_admin = $db->prepare('
                                INSERT INTO idk_pp_user_access
                                (
                                    pua_type,
                                    pua_nalog_id, 
                                    pua_user_id, 
                                    pua_permission_id, 
                                    pua_status
                                )
                                VALUES
                                (
                                    :pua_type,
                                    :pua_nalog_id, 
                                    :pua_user_id, 
                                    :pua_permission_id, 
                                    :pua_status
                                )
                            ');
                            $query_insert_super_admin->execute(array(
                                ':pua_type' => 1, 
                                ':pua_nalog_id' => $order_id, 
                                ':pua_user_id' => $user_id,  
                                ':pua_permission_id' => 1, 
                                ':pua_status' => $user_super_admin
                            ));
                            $superAdminAccessId = $db->lastInsertId();
                            if ($superAdminAccessId != 0) {
                                $logDesc1 = $logDesc1.'Izvršen insert statusa na status vrijednost ['.(($user_super_admin) ? 'Active': 'Inactive').'] za pristup ID = ['.$superAdminAccessId.']';
                                addToLogs($logDesc1, 0);
                                $result1 = 4;
                            } else {
                                $result1 = 5;
                            }
                        }
                        unset($superAdminAccessId);
                    /*
                        SuperAdmin Update END 
                    */

                    /*
                        Admin Update START 
                    */
                        $actionStatus = array();
                        $actionTypes = array();
                        $accessIds = array();
                        $accessStatuses = array();

                        if ($user_admin != null) {
                            $orderPartnerIds = getActivePartnersForOrderArrayR($order_id);
                            if (count($orderPartnerIds) != 0) {
                                foreach ($orderPartnerIds AS $orderPartnerId) {
                                    if (!in_array($orderPartnerId, $user_admin)) {
                                        $adminAccessId = checkJobSoftUserAccess($user_id, $user_company, $order_id, $orderPartnerId, 2);
                                        if ($adminAccessId['accessId'] != 0 AND $adminAccessId['accessStatus'] == 1) {
                                            $query_update_admin = $db->prepare('
                                                UPDATE 
                                                    idk_pp_user_access
                                                SET 
                                                    pua_status = 0
                                                WHERE 
                                                    pua_id = :adminAccessId
                                            ');
                                            $query_update_admin->execute(array(
                                                ':adminAccessId' => $adminAccessId['accessId']
                                            ));
                                            if ($query_update_admin->rowCount() == 1) {
                                                array_push($actionStatus, 'Success');
                                                array_push($actionTypes, 'Update old');
                                                array_push($accessIds, ''.$adminAccessId['accessId'].'');
                                                array_push($accessStatuses, 'Inactive');
                                            } else {
                                                array_push($actionStatus, 'Error');
                                                array_push($actionTypes, 'Update old');
                                                array_push($accessIds, ''.$adminAccessId['accessId'].'');
                                                array_push($accessStatuses, 'Inactive');
                                            }
                                        }
                                    }
                                }
                            }

                            foreach($user_admin as $partner_id) {
                                $adminAccessId = checkJobSoftUserAccess($user_id, $user_company, $order_id, $partner_id, 2);
                                if ($adminAccessId['accessId'] != 0) {
                                    if ($adminAccessId['accessStatus'] == 0) {
                                        $query_update_admin = $db->prepare('
                                            UPDATE 
                                                idk_pp_user_access
                                            SET 
                                                pua_status = 1
                                            WHERE 
                                                pua_id = :adminAccessId
                                        ');
                                        $query_update_admin->execute(array(
                                            ':adminAccessId' => $adminAccessId['accessId']
                                        ));
                                        if ($query_update_admin->rowCount() == 1) {
                                            array_push($actionStatus, 'Success');
                                            array_push($actionTypes, 'Update new');
                                            array_push($accessIds, ''.$adminAccessId['accessId'].'');
                                            array_push($accessStatuses, 'Active');
                                        } else {
                                            array_push($actionStatus, 'Error');
                                            array_push($actionTypes, 'Update new');
                                            array_push($accessIds, ''.$adminAccessId['accessId'].'');
                                            array_push($accessStatuses, 'Active');
                                        }
                                    }
                                } else {
                                    $query_insert_admin = $db->prepare('
                                        INSERT INTO idk_pp_user_access
                                        (
                                            pua_type,
                                            pua_nalog_id, 
                                            pua_partner_id,
                                            pua_user_id, 
                                            pua_permission_id, 
                                            pua_status
                                        )
                                        VALUES
                                        (
                                            :pua_type,
                                            :pua_nalog_id, 
                                            :pua_partner_id,
                                            :pua_user_id, 
                                            :pua_permission_id, 
                                            :pua_status
                                        )
                                    ');
                                    $query_insert_admin->execute(array(
                                        ':pua_type' => 2, 
                                        ':pua_nalog_id' => $order_id, 
                                        ':pua_partner_id' => $partner_id,
                                        ':pua_user_id' => $user_id,  
                                        ':pua_permission_id' => 2, 
                                        ':pua_status' => 1
                                    ));
                                    $adminAccessId = $db->lastInsertId();
                                    if ($adminAccessId != 0) {
                                        array_push($actionStatus, 'Success');
                                        array_push($actionTypes, 'Insert new');
                                        array_push($accessIds, ''.$adminAccessId.'');
                                        array_push($accessStatuses, 'Active');
                                    } else {
                                        array_push($actionStatus, 'Error');
                                        array_push($actionTypes, 'Insert new');
                                        array_push($accessIds, ''.$adminAccessId.'');
                                        array_push($accessStatuses, 'Active');
                                    }
                                }
                                unset($adminAccessId); 
                            }
                        } else {
                            $orderPartnerIds = getActivePartnersForOrderArrayR($order_id);
                            if (count($orderPartnerIds) != 0) {
                                foreach ($orderPartnerIds AS $orderPartnerId) {
                                    $adminAccessId = checkJobSoftUserAccess($user_id, $user_company, $order_id, $orderPartnerId, 2);
                                    if ($adminAccessId['accessId'] != 0 AND $adminAccessId['accessStatus'] == 1) {
                                        $query_update_admin = $db->prepare('
                                            UPDATE 
                                                idk_pp_user_access
                                            SET 
                                                pua_status = 0
                                            WHERE 
                                                pua_id = :adminAccessId
                                        ');
                                        $query_update_admin->execute(array(
                                            ':adminAccessId' => $adminAccessId['accessId']
                                        ));
                                        if ($query_update_admin->rowCount() == 1) {
                                            array_push($actionStatus, 'Success');
                                            array_push($actionTypes, 'Update old');
                                            array_push($accessIds, ''.$adminAccessId['accessId'].'');
                                            array_push($accessStatuses, 'Inactive');
                                        } else {
                                            array_push($actionStatus, 'Error');
                                            array_push($actionTypes, 'Update old');
                                            array_push($accessIds, ''.$adminAccessId['accessId'].'');
                                            array_push($accessStatuses, 'Inactive');
                                        }
                                    }
                                    unset($adminAccessId); 
                                }
                            }
                        }

                        $actionStatusImp = '';
                        $actionTypesImp = '';
                        $accessIdsImp = '';
                        $accessStatusesImp = '';

                        if (count($accessIds) > 0) {
                            $actionStatusImp = implode(',',$actionStatus);
                            $actionTypesImp = implode(',',$actionTypes);
                            $accessIdsImp = implode(',',$accessIds);
                            $accessStatusesImp = implode(',',$accessStatuses);

                            if (in_array('Error', $actionStatus)) {
                                $result2 = 2; 
                            } else {
                                $result2 = 3;
                            }

                            $logDesc2 = $logDesc2.'{"AccessIds" => ('.$accessIdsImp.'), "ActionStatus" => ('.$actionStatusImp.'), "ActionTypes" => ('.$actionTypesImp.'), "AccessStatuses" => ('.$accessStatusesImp.')}';
                            addToLogs($logDesc2, 0);
                        }

                        unset($actionStatus);
                        unset($actionTypes);
                        unset($accessIds);
                        unset($accessStatuses);

                        unset($actionStatusImp);
                        unset($actionTypesImp);
                        unset($accessIdsImp);
                        unset($accessStatusesImp);
                    /*
                        Admin Update END 
                    */

                } else {
                    $result1 = 1;
                    $result2 = 1;
                }

                if ($result1 != 0 AND $result2 != 0) {
                    header('Location: ../../nalozi?page=open&id='.$order_id.'&tab=jobstep_pp_settings&tabgs=userAccessSettings&message1UAS='.$result1.'&message2UAS='.$result2.'');
                } else if ($result1 == 0 AND $result2 != 0) {
                    header('Location: ../../nalozi?page=open&id='.$order_id.'&tab=jobstep_pp_settings&tabgs=userAccessSettings&message2UAS='.$result2.'');
                } else if ($result1 != 0 AND $result2 == 0) {
                    header('Location: ../../nalozi?page=open&id='.$order_id.'&tab=jobstep_pp_settings&tabgs=userAccessSettings&message1UAS='.$result1.'');
                } else {
                    header('Location: ../../nalozi?page=open&id='.$order_id.'&tab=jobstep_pp_settings&tabgs=userAccessSettings');
                }
            break; 
            
            case "get_users_for_reminder":

                $change_user_type          = $_POST["change_user_type"];
                $reminder_type             = $_POST["reminder_type"];
                $reminder_user_type        = $_POST["reminder_user_type"];
                $setting_id                = $_POST["setting_id"];
                $setting_order_id          = $_POST["setting_order_id"];
                $setting_partner_id        = $_POST["setting_partner_id"];
                $setting_assigned_users    = $_POST["setting_assigned_users"];
                $setting_controlling_users = $_POST["setting_controlling_users"];

                /*
                    data preparation START
                    */
                        $setting_order_id = intval($setting_order_id); 
                        $setting_partner_id = (($setting_partner_id != 'No partner' OR $setting_partner_id != 'Undefined') ? intval($setting_partner_id) : 0);

                        $permission_type = 0; 
                        $search_case = '';
                        $sub_condition_partner = ''; 
                        if ($reminder_user_type == 'SuperAdmin') {
                            $permission_type = 1; 
                            $search_case = 'pp_users'; 
                            $sub_condition_partner = 'pua.pua_partner_id is null'; 
                        } else if ($reminder_user_type == 'Admin') {
                            $permission_type = 2;
                            $search_case = 'pp_users';
                            $sub_condition_partner = 'pua.pua_partner_id = '.$setting_partner_id;
                        } else if ($reminder_user_type == 'ProjectManager') {
                            $permission_type = 2; 
                            $search_case = 'crm_users';
                            $sub_condition_partner = '';
                        } else {
                            $permission_type = 0;
                            $search_case = ''; 
                            $sub_condition_partner = ''; 
                        }
                        
                        if ($change_user_type == 'users') {
                            $selected_user = (($setting_assigned_users != 'Undefined') ? explode(',', $setting_assigned_users) : array());
                        } else if ($change_user_type == 'controllingUsers') {
                            $selected_user = (($setting_controlling_users != 'Undefined') ? explode(',', $setting_controlling_users) : array());
                        } else {
                            $selected_user = array();
                        }
                    /*
                    data preparation END 
                */

                /* 
                    execution START 
                    */
                        $sql = '';
                        $result = ''; 
                        if ($permission_type != 0 AND $search_case != '') {
                            if ($search_case == 'pp_users') {
                                $sql = '
                                    SELECT 
                                        pua.pua_id 													AS user_access_id,
                                        pu.pu_id 													AS user_id,   
                                        CONCAT(pu.pu_fname, " ", pu.pu_lname) 						AS user_full_name,
                                        c.company_name												AS user_company_name
                                    FROM 
                                        idk_pp_user_access pua
                                    JOIN 
                                        idk_pp_users pu
                                    ON 
                                        pua.pua_user_id = pu.pu_id 
                                    JOIN 
                                        idk_companies c 
                                    ON 
                                        pu.pu_company_id = c.company_id 
                                    WHERE 
                                        pua.pua_type = '.$permission_type.'
                                        AND 
                                        pua.pua_nalog_id = '.$setting_order_id.'
                                        AND 
                                        '.$sub_condition_partner.'
                                        AND 
                                        pua.pua_status = 1
                                        AND 
                                        pu.pu_status = 1
                                    ORDER BY 
                                        pu.pu_id, pua.pua_id 
                                    ASC
                                ';
                            } else {
                                $sql = '
                                    SELECT 
                                        emp.employee_id 											AS user_access_id, 
                                        emp.employee_id  											AS user_id, 
                                        CONCAT(emp.employee_firstname, " ", emp.employee_lastname) 	AS user_full_name, 
                                        CONCAT("JobStep Zaposlenik")								AS user_company_name
                                    FROM 
                                        idk_employees emp 
                                    WHERE 
                                        FIND_IN_SET('.$permission_type.', emp.employee_status) > 0
                                        AND 
                                        emp.employee_status != 0
                                    ORDER BY
                                        emp.employee_id
                                    ASC
                                '; 
                            } 

                            if ($sql != '') {
                                $query = $db->prepare($sql);
                                $query->execute();
                                if ($query->rowCount() != 0) {
                                    $rows = $query->fetchAll(PDO::FETCH_ASSOC);
                                    $options = array();
                                    foreach($rows AS $row) {
                                        if (in_array($row["user_access_id"], $selected_user)) {
                                            array_push($options, '<option value="'.$row["user_access_id"].'" data-subtext="'.$row["user_company_name"].'" selected>'.$row["user_full_name"].'</option>'); 
                                        } else {
                                            array_push($options, '<option value="'.$row["user_access_id"].'" data-subtext="'.$row["user_company_name"].'">'.$row["user_full_name"].'</option>'); 
                                        }
                                    }
                                    $result = implode("", $options); 
                                    unset($rows);
                                    unset($options); 
                                } else {
                                    $result = 'No results found for query!';
                                }
                            }
                        }
                        unset($selected_user);
                    /* 
                    execution END 
                */
                
                echo $result;

            break; 

            case "change_reminder_users": 
                $change_user_type          		= $_POST['change_user_type'];
                $reminder_type             		= $_POST['reminder_type'];
                $reminder_user_type        		= $_POST['reminder_user_type'];
                $setting_id                		= $_POST['setting_id'];
                $setting_order_id          		= $_POST['setting_order_id'];
                $setting_partner_id        		= $_POST['setting_partner_id'];
                $setting_assigned_users    		= $_POST['setting_assigned_users'];
                $setting_controlling_users 		= $_POST['setting_controlling_users'];
                $setting_assigned_users_new 	= $_POST['setting_assigned_users_new'] ?? null;

                $target_column_value = (($setting_assigned_users_new != null) ? implode(',', $setting_assigned_users_new) : null);
                
                $enabled_user_change_types = array('users', 'controllingUsers');
                $result = 0;
                $target_column = '';
                $target_column_value_old = '';  
                $log_desc = 'JobSoft Postavke - Reminders setup - Korisnici/Controlling korisnici -> '; 
                if (in_array($change_user_type, $enabled_user_change_types)) {
                    if ($change_user_type == 'users') {
                        $target_column = 'prs_pua_ids';
                        $target_column_value_old = $setting_assigned_users;
                        $log_desc = $log_desc . 'Zaposlenik izvršio postavku korisnika za remindere. ';
                    } else {
                        $target_column = 'prs_controlling_pua_ids';
                        $target_column_value_old = $setting_controlling_users;
                        $log_desc = $log_desc . 'Zaposlenik izvršio postavku controlling korisnika za remindere. ';
                    }
                    $query_check = $db->prepare('
                        SELECT 
                            prs_id
                        FROM 
                            idk_pp_reminder_settings
                        WHERE 
                            prs_id = :prs_id
                            AND
                            prs_nalog_id = :prs_nalog_id
                            AND 
                            prs_partner_id '.( ( $setting_partner_id != 'No partner' ) ? ' = ' . $setting_partner_id . '' : ' is null ').' 
                            AND
                            prs_reminder_type_id = :prs_reminder_type_id
                    ');
                    $query_check->execute(array(
                        ':prs_id' => $setting_id,
                        ':prs_nalog_id' => $setting_order_id,
                        ':prs_reminder_type_id' => $reminder_type
                    ));
                    if ($query_check->rowCount() == 1) { 
                        $query_update = $db->prepare('
                            UPDATE
                                idk_pp_reminder_settings
                            SET
                                '.$target_column.' = :target_column
                            WHERE 
                                prs_id = :prs_id
                                AND
                                prs_nalog_id = :prs_nalog_id
                                AND 
                                prs_partner_id '.( ( $setting_partner_id != 'No partner' ) ? ' = ' . $setting_partner_id . '' : ' is null ').' 
                                AND
                                prs_reminder_type_id = :prs_reminder_type_id
                        ');
                        $query_update->execute(array(
                            ':prs_id' => $setting_id,
                            ':prs_nalog_id' => $setting_order_id,
                            ':prs_reminder_type_id' => $reminder_type, 
                            ':target_column' => $target_column_value
                        ));
                        if ($query_update->rowCount() == 1) {
                            $result = 4;
                            $log_desc = $log_desc . ' Detalji: {"setting_id" => "'.$setting_id.'", "order_id" => "'.$setting_order_id.'", "partner_id" => "'.$setting_partner_id.'", "reminder_type" => "'.$reminder_type.'", "target_column" => "'.$target_column.'", "target_column_value" => "'.$target_column_value.'", "target_column_value_old" => "'.$target_column_value_old.'", "request status" => "success"}';
                            addToLogs($log_desc, 0);
                        } else {
                            $result = 3;
                            $log_desc = $log_desc . ' Detalji: {"setting_id" => "'.$setting_id.'", "order_id" => "'.$setting_order_id.'", "partner_id" => "'.$setting_partner_id.'", "reminder_type" => "'.$reminder_type.'", "target_column" => "'.$target_column.'", "target_column_value" => "'.$target_column_value.'", "target_column_value_old" => "'.$target_column_value_old.'", "request status" => "warning"}';
                            addToLogs($log_desc, 0);
                        }
                    } else {
                        $result = 2;
                    }
                } else {
                    $result = 1;
                }

                unset($enabled_user_change_types); 
                header('Location: ../../nalozi?page=open&id='.$setting_order_id.'&tab=jobstep_pp_settings&tabgs=reminderSettings&messrs='.$result.'');
            break; 

            case "get_question_categories":
                $result = ""; 
                $query = $db->prepare("
                    SELECT 
                        pqc.pqc_id, pqc.pqc_name, pqc.pqc_name_de
                    FROM 
                        idk_pp_question_categories pqc
                ");
                $query->execute();
                if ($query->rowCount() > 0) {
                    $rows = $query->fetchAll(PDO::FETCH_ASSOC);
                    echo json_encode($rows);
                } else {
                    http_response_code(204);
                    die("No data for question categories!");
                }
            break; 

            case "add_new_predefined_question":
                $orderANPQ              = $_POST["orderANPQ"] ?? null; 
                $questionTextANPQ       = $_POST["questionTextANPQ"] ?? null; 
                $categoryANPQ           = $_POST["categoryANPQ"] ?? null;
                $newCategoryANPQ        = $_POST["newCategoryANPQ"] ?? null;
                $newDeCategoryANPQ      = $_POST["newDeCategoryANPQ"] ?? null;
                $hasTextANPQ            = $_POST["hasTextANPQ"] ?? 0; 
                $hasRatingANPQ          = $_POST["hasRatingANPQ"] ?? 0; 
                $hasDropdownANPQ        = $_POST["hasDropdownANPQ"] ?? 0; 
                $numbersOptionsANPQ     = $_POST["numbersOptionsANPQ"] ?? null;

                $optionsValue = array();
                if ($numbersOptionsANPQ) {
                    $numbersOptionsANPQ = json_decode($numbersOptionsANPQ, true);
                    $cnt = 0; 
                    foreach($numbersOptionsANPQ AS $numberOptionANPQ) {
                        $cnt++;
                        $optionsValue[$numberOptionANPQ] = array(
                            'valueANPQ' => $cnt,
                            'textOptionsANPQ' => $_POST['textOptions'.$numberOptionANPQ.'ANPQ'] ?? null,
                            'textdeOptionsANPQ' => $_POST['textdeOptions'.$numberOptionANPQ.'ANPQ'] ?? null,
                            'subtextOptionsANPQ' => $_POST['subtextOptions'.$numberOptionANPQ.'ANPQ'] ?? null,
                            'subtextdeOptionsANPQ' => $_POST['subtextdeOptions'.$numberOptionANPQ.'ANPQ'] ?? null,
                        );
                    }
                    unset($numbersOptionsANPQ);
                }

                if (($orderANPQ) AND ($questionTextANPQ) AND ($categoryANPQ)) {

                    if ($categoryANPQ == 'new') {
                        $query_insert_category = $db->prepare("
                            INSERT INTO idk_pp_question_categories 
                                (
                                    pqc_name, 
                                    pqc_name_de
                                ) 
                            VALUES 
                                (
                                    :pqc_name,
                                    :pqc_name_de
                                )
                        ");
                        $query_insert_category->execute(array(
                            ':pqc_name' => $newCategoryANPQ,
                            ':pqc_name_de' => $newDeCategoryANPQ
                        ));
                        $categoryANPQ = $db->lastInsertId();
                    } else if ($categoryANPQ == 'without') {
                        $categoryANPQ = null;
                    }

                    $query_insert_questions = $db->prepare("
                        INSERT INTO idk_pp_questions 
                            (
                                pqu_question, 
                                pqu_nalog_id, 
                                pqu_user_id, 
                                pqu_category_id, 
                                pqu_has_text, 
                                pqu_has_rating, 
                                pqu_has_dropdown
                            ) 
                        VALUES 
                            (
                                :pqu_question, 
                                :pqu_nalog_id, 
                                :pqu_user_id, 
                                :pqu_category_id, 
                                :pqu_has_text, 
                                :pqu_has_rating, 
                                :pqu_has_dropdown
                            )
                    ");
                    $query_insert_questions->execute(array(
                        ':pqu_question' => $questionTextANPQ,
                        ':pqu_nalog_id' => $orderANPQ,
                        ':pqu_user_id' => $logged_employee_id,
                        ':pqu_category_id' => $categoryANPQ,
                        ':pqu_has_text' => $hasTextANPQ,
                        ':pqu_has_rating' => $hasRatingANPQ,
                        ':pqu_has_dropdown' => $hasDropdownANPQ
                    ));
                    $questionId = $db->lastInsertId();
                    if($questionId) {
                        $result = 3;
                        if(count($optionsValue) > 0) {
                            $sqlValueExplode = array();
                            $sqlValueImplode = '';
                            foreach($optionsValue AS $optionValue){
                                array_push($sqlValueExplode, '('.$questionId.', '.$optionValue['valueANPQ'].', "'.$optionValue['textOptionsANPQ'].'", "'.$optionValue['textdeOptionsANPQ'].'", "'.$optionValue['subtextOptionsANPQ'].'", "'.$optionValue['subtextdeOptionsANPQ'].'")');
                            }
                            $sqlValueImplode = implode(',', $sqlValueExplode);
                            

                            $query_insert_questions_options = $db->prepare("
                                INSERT INTO idk_pp_question_options 
                                    ( 
                                        pqo_question_id, 
                                        pqo_value, 
                                        pqo_value_text, 
                                        pqo_value_text_de, 
                                        pqo_value_subtext, 
                                        pqo_value_subtext_de
                                    ) 
                                VALUES 
                                    ".$sqlValueImplode." 
                            ");
                            $query_insert_questions_options->execute();
                            if ($query_insert_questions_options->rowCount() == count($sqlValueExplode)) {
                                $result = 4;
                            } else {
                                $result = 5;
                            }
                            unset($sqlValueExplode);
                        }
                    } else {
                        $result = 2;
                    }
                } else {
                    $result = 1;
                }
                unset($optionsValue);
                header('Location: ../../nalozi?page=open&id='.$orderANPQ.'&tab=jobstep_pp_settings&tabgs=predefinedQuestionsSettings&messPQS='.$result.'');
            break;

            case "change_appointment_hours": 
                $pah_id = $_POST["pah_id"] ?? null; 
                $pah_time = $_POST["pah_time"] ?? null; 
                $order_id = $_POST["order_id"] ?? null; 

                if ($pah_id == null OR $pah_time == null OR $order_id == null) {
                    http_response_code(400);
                    die("Nepotpuni ili neispravni podaci. Molimo vas da provjerite i ponovno pošaljete sve potrebne informacije.");
                }

                $log_desc = 'JobSoft Postavke - Datumi castinga - Editovanje termina datuma castinga -> '; 
                $query = $db->prepare("
                    UPDATE 
                        idk_pp_appointment_hours 
                    SET 
                        pah_time = :pah_time
                    WHERE 
                        pah_id = :pah_id
                ");
                $query -> execute(array(
                    ':pah_time' => date("H:i:s", strtotime($pah_time)),
                    ':pah_id' => $pah_id
                ));
                if ($query->rowCount() == 1) {
                    $result = 1; 
                    $log_desc = $log_desc. "Uspješno izvršeno editovanje termina datuma castinga za ID = [".$pah_id."] na vrijednost [".date("H:i:s", strtotime($pah_time))."]."; 
                } else {
                    $result = 2;
                    $log_desc = $log_desc. "Nije izvršeno editovanje termina datuma castinga za ID = [".$pah_id."] na vrijednost [".date("H:i:s", strtotime($pah_time))."].";
                }
                addToLogs($log_desc, 0);
                header('Location: ../../nalozi?page=open&id='.$order_id.'&tab=jobstep_pp_settings&tabgs=castingDatesSettings&messCDS='.$result.'');
            break; 

            case "delete_appointment_hours": 
                $pah_id = $_POST['pah_id'] ?? null;

                if ($pah_id == null) {
                    echo 204;
                    exit();
                }
                $result = 0;
                $log_desc = 'JobSoft Postavke - Datumi castinga - Brisanje termina datuma castinga -> '; 
                $query = $db->prepare("
                    DELETE FROM idk_pp_appointment_hours 
                    WHERE pah_id = :pah_id
                ");
                $query->execute(array(
                    ':pah_id' => $pah_id
                ));
                if ($query->rowCount() == 1) {
                    $log_desc = $log_desc. "Uspješno izvršeno brisanje termina datuma castinga za ID = [".$pah_id."].";
                    $result = 3;
                } else {
                    $log_desc = $log_desc. "Nije izvršeno brisanje termina datuma castinga za ID = [".$pah_id."]. Effect of deletion on rows: ".$query->rowCount()."";
                    $result = 4;
                }
                addToLogs($log_desc, 0);
                echo $result;
            break;

            case "add_appointment_hours":
                $pap_id = $_POST['pap_id'] ?? null; 
                $order_id = $_POST["order_id"] ?? null; 
                $pah_time = $_POST["pah_time"] ?? null;

                if ($pap_id == null OR $pah_time == null OR $order_id == null) {
                    http_response_code(400);
                    die("Nepotpuni ili neispravni podaci. Molimo vas da provjerite i ponovno pošaljete sve potrebne informacije.");
                }

                $result = 0;
                $log_desc = 'JobSoft Postavke - Datumi castinga - Dodavanje termina datuma castinga -> ';

                $query = $db->prepare("
                    INSERT INTO idk_pp_appointment_hours
                        (
                            pah_time, 
                            pah_employee_id, 
                            pap_id
                        )
                    VALUES
                        (
                            :pah_time, 
                            :pah_employee_id, 
                            :pap_id
                        )
                ");
                $query->execute(array(
                    ':pah_time' => $pah_time,
                    ':pah_employee_id' => $logged_employee_id,
                    ':pap_id' => $pap_id
                ));
                $ahId = $db->lastInsertId();
                if ($ahId) {
                    $result = 5;
                    $log_desc = $log_desc. "Uspješno izvršeno dodavanje termina datuma castinga sa ID = [".$ahId."].";
                } else {
                    $result = 6;
                    $log_desc = $log_desc. "Postoji problem sa dodavanjem termina datuma castinga. Poslane vrijednosti su: {'pap_id' : ".$pap_id.", 'order_id' : ".$order_id.", 'pah_time' : ".$pah_time."} ";
                }   
                addToLogs($log_desc, 0);
                header('Location: ../../nalozi?page=open&id='.$order_id.'&tab=jobstep_pp_settings&tabgs=castingDatesSettings&messCDS='.$result.'');
            break;

            case "add_appointments":
                $order_id = $_POST['order_id'] ?? null; 
                $name_CD = $_POST['name_CD'] ?? null; 
                $name_de_CD = $_POST['name_de_CD'] ?? null; 
                $dates_CD = $_POST['dates_CD'] ?? null;

                if ($order_id == null OR $name_CD == null OR $name_de_CD == null OR $dates_CD == null) {
                    http_response_code(400);
                    die("Nepotpuni ili neispravni podaci. Molimo vas da provjerite i ponovno pošaljete sve potrebne informacije.");
                }

                $dates_CD = str_replace(' ', '', $dates_CD);

                $dates_CD_exp = explode(',', $dates_CD); 
                $times_CD_exp = array();
                $grouped_city_data_CD = array();

                foreach ($dates_CD_exp AS $date_CD) {
                    array_push($times_CD_exp, strtotime($date_CD));
                    $date_format_CD = date('Y-m-d', strtotime($date_CD));
                    if (!isset($grouped_city_data_CD[$date_format_CD])) {
                        $grouped_city_data_CD[$date_format_CD] = array(
                            'date_CD' => $date_format_CD, 
                            'city_CD' => $_POST['city'.date('dmY', strtotime($date_CD)).'CD'] ?? null
                        );
                        if (!$grouped_city_data_CD[$date_format_CD]['city_CD']) {
                            http_response_code(400);
                            die("Nepotpuni ili neispravni podaci. Molimo vas da provjerite i ponovno pošaljete sve potrebne informacije.");
                        }
                    }
                    unset($date_CD_format);
                    unset($date_CD);
                }
                unset($dates_CD_exp);

                $max_date_CD = date('Y-m-d', max($times_CD_exp));
                $min_date_CD = date('Y-m-d', min($times_CD_exp));

                unset($times_CD_exp);
                
                /*
                    Unos u bazu START
                    */
                        $result = 0;
                        $log_desc = 'JobSoft Postavke - Datumi castinga - Dodavanje datuma castinga -> ';

                        $query_qroup = $db->prepare("
                            INSERT INTO idk_pp_appointment_groups 
                                (
                                    ppaq_start_date,
                                    ppaq_end_date,
                                    papq_name,
                                    papq_name_de,
                                    papq_nalog_id
                                ) 
                            VALUES 
                                (
                                    :ppaq_start_date,
                                    :ppaq_end_date,
                                    :papq_name,
                                    :papq_name_de,
                                    :papq_nalog_id
                                )
                        ");
                        $query_qroup->execute(array(
                            ':ppaq_start_date' => $min_date_CD,
                            ':ppaq_end_date' => $max_date_CD,
                            ':papq_name' => $name_CD,
                            ':papq_name_de' => $name_de_CD,
                            ':papq_nalog_id' => $order_id
                        ));
                        $group_id = $db->lastInsertId();
                        if ($group_id) { 

                            $value_for_insert_exp = array();
                            $value_for_insert_imp = '';
                            foreach($grouped_city_data_CD AS $row_city_data_CD) {
                                array_push($value_for_insert_exp, '("'.$row_city_data_CD['date_CD'].'",'.$order_id.',"'.$row_city_data_CD['city_CD'].'",'.$group_id.')'); 
                                unset($row_city_data_CD);
                            }
                            $value_for_insert_imp = implode(',', $value_for_insert_exp); 
                            unset($value_for_insert_exp);

                            $query_appointment = $db->prepare("
                                INSERT INTO idk_pp_appointments 
                                    (
                                        pap_date,
                                        pap_nalog_id,
                                        pap_city,
                                        pap_group_id
                                    ) 
                                VALUES 
                                    ".$value_for_insert_imp."
                            ");
                            $query_appointment->execute();

                            if ($query_appointment->rowCount() == count($grouped_city_data_CD)) {
                                $result = 7;
                                $log_desc = $log_desc. "Uspješno izvršeno dodavanje datuma castinga sa Group ID = [".$group_id."]. Dodano termina: ".$query_appointment->rowCount()."";
                            } else {
                                $result = 8;
                                $log_desc = $log_desc. "Desio se problem prilikom dodavanja datuma castinga za Group ID = [".$group_id."]. Poslane vrijednosti: ".$value_for_insert_imp."";
                            }

                            addToLogs($log_desc, 0);

                            unset($value_for_insert_imp);
                        } else {
                            $result = 9;
                        }
                    /* 
                    Unos u bazu END
                */

                unset($grouped_city_data_CD);

                header('Location: ../../nalozi?page=open&id='.$order_id.'&tab=jobstep_pp_settings&tabgs=castingDatesSettings&messCDS='.$result.'');
            break;
            
            case "add_appointment_details":
                $pap_id = $_POST['pap_id'] ?? null; 
                $order_id = $_POST['order_id'] ?? null;
                $dates_CD = $_POST['dates_CD'] ?? null; 
                $city_CD = $_POST['city_CD'] ?? null;
                $address_CD = $_POST['address_CD'] ?? null; 
                $address_link_CD = $_POST['address_link_CD'] ?? null; 
                $first_send_enabled_CD = $_POST['first_send_enabled_CD'] ?? 0;
                $first_send_days_CD = $_POST['first_send_days_CD'] ?? 0;
                $second_send_enabled_CD = $_POST['second_send_enabled_CD'] ?? 0;
                $second_send_days_CD = $_POST['second_send_days_CD'] ?? 0;

                if ($pap_id == null OR $order_id == null OR $dates_CD == null OR $city_CD == null) {
                    http_response_code(400);
                    die("Nepotpuni ili neispravni podaci. Molimo vas da provjerite i ponovno pošaljete sve potrebne informacije.");
                }

                $old_details = array();
                $new_details = array();
                $dates_CD = date('Y-m-d', strtotime($dates_CD));

                $query_old_details = $db->prepare("
                    SELECT 
                        pap_date,
                        pap_city,
                        pap_location_name,
                        pap_google_maps_location,
                        pap_first_sending_number_days,
                        pap_second_sending_number_days,
                        pap_first_send_enabled, 
                        pap_second_send_enabled
                    FROM 
                        idk_pp_appointments
                    WHERE 
                        pap_id = :pap_id
                ");
                $query_old_details->execute(array(
                    'pap_id' => $pap_id
                ));
                $row_old_details = $query_old_details->fetch();
                $old_details['pap_date'] = $row_old_details['pap_date']; 
                $old_details['pap_city'] = $row_old_details['pap_city']; 
                $old_details['pap_location_name'] = $row_old_details['pap_location_name']; 
                $old_details['pap_google_maps_location'] = $row_old_details['pap_google_maps_location']; 
                $old_details['pap_first_sending_number_days'] = $row_old_details['pap_first_sending_number_days']; 
                $old_details['pap_second_sending_number_days'] = $row_old_details['pap_second_sending_number_days']; 
                $old_details['pap_first_send_enabled'] = $row_old_details['pap_first_send_enabled']; 
                $old_details['pap_second_send_enabled'] = $row_old_details['pap_second_send_enabled'];

                $new_details['pap_date'] = $dates_CD;
                $new_details['pap_city'] = $city_CD;
                $new_details['pap_location_name'] = $address_CD; 
                $new_details['pap_google_maps_location'] = $address_link_CD; 
                $new_details['pap_first_sending_number_days'] = $first_send_days_CD; 
                $new_details['pap_second_sending_number_days'] = $second_send_days_CD; 
                $new_details['pap_first_send_enabled'] = $first_send_enabled_CD; 
                $new_details['pap_second_send_enabled'] = $second_send_enabled_CD;

                $result = 0;
                $log_desc = 'JobSoft Postavke - Datumi castinga - Dodavanje detalja datuma castinga -> ';

                $query_update_details = $db->prepare("
                    UPDATE 
                        idk_pp_appointments
                    SET 
                        pap_date = :pap_date,
                        pap_city = :pap_city,
                        pap_location_name = :pap_location_name, 
                        pap_google_maps_location = :pap_google_maps_location, 
                        pap_first_sending_number_days = :pap_first_sending_number_days,
                        pap_second_sending_number_days = :pap_second_sending_number_days,
                        pap_first_send_enabled = :pap_first_send_enabled, 
                        pap_second_send_enabled = :pap_second_send_enabled
                    WHERE 
                        pap_id = :pap_id
                ");
                $query_update_details->execute(array(
                    ':pap_id' => $pap_id, 
                    ':pap_date' => $dates_CD,
                    ':pap_city' => $city_CD,
                    ':pap_location_name' => $address_CD, 
                    ':pap_google_maps_location' => $address_link_CD, 
                    ':pap_first_sending_number_days' => $first_send_days_CD, 
                    ':pap_second_sending_number_days' => $second_send_days_CD, 
                    ':pap_first_send_enabled' => $first_send_enabled_CD, 
                    ':pap_second_send_enabled' => $second_send_enabled_CD
                ));

                if ($query_update_details->rowCount() == 1) {
                    $result = 10;
                    $log_desc = $log_desc. "Uspješno izvršeno uređivanje detalja datuma castinga za Appointment ID = [".$pap_id."]. Stare vrijednosti: ".json_encode($old_details, JSON_PRETTY_PRINT).". Nove vrijednosti: ".json_encode($new_details, JSON_PRETTY_PRINT).".";
                } else {
                    $result = 11;
                    $log_desc = $log_desc. "Problem sa uređivanjem detalja datuma castinga za Appointment ID = [".$pap_id."]. Stare vrijednosti: ".json_encode($old_details, JSON_PRETTY_PRINT).". Nove vrijednosti: ".json_encode($new_details, JSON_PRETTY_PRINT).".";
                }
                
                addToLogs($log_desc, 0);

                unset($row_old_details);
                unset($old_details); 
                unset($new_details); 

                header('Location: ../../nalozi?page=open&id='.$order_id.'&tab=jobstep_pp_settings&tabgs=castingDatesSettings&messCDS='.$result.'');
            break; 

            case "edit_predefined_question":

                function reindexOptionsDataEPQ($array) {
                    if (isset($array['optionsData']) && is_array($array['optionsData'])) {
                        $indexedOptions = array();
                        foreach ($array['optionsData'] as $option) {
                            if (isset($option['pqoValue'])) {
                                $indexedOptions[$option['pqoValue']] = $option;
                            }
                        }
                        $array['optionsData'] = $indexedOptions;
                    }
                    return $array;
                }

                $orderEPQ               = $_POST["orderEPQ"] ?? null;
                $questionIdEPQ          = $_POST["questionIdEPQ"] ?? null;
                $questionOldDataEPQ     = $_POST["questionOldDataEPQ"];
                $questionTextEPQ        = $_POST["questionTextEPQ"] ?? null; 
                $categoryEPQ            = $_POST["categoryEPQ"] ?? null;
                $newCategoryEPQ         = $_POST["newCategoryEPQ"] ?? null;
                $newDeCategoryEPQ       = $_POST["newDeCategoryEPQ"] ?? null;
                $hasTextEPQ             = $_POST["hasTextEPQ"] ?? 0; 
                $hasRatingEPQ           = $_POST["hasRatingEPQ"] ?? 0; 
                $hasDropdownEPQ         = $_POST["hasDropdownEPQ"] ?? 0; 
                $numbersOptionsEPQ      = $_POST["numbersOptionsEPQ"] ?? null;

                $arrayQuestionOldDataEPQ = json_decode($questionOldDataEPQ, true);
                $arrayQuestionOldDataEPQ = reindexOptionsDataEPQ($arrayQuestionOldDataEPQ);

                /*echo '<h3>Old values</h3>';
                print("<pre>".print_r($arrayQuestionOldDataEPQ, true)."</pre>");*/

                $newValuesArray = array(
                    'pqId' => $questionIdEPQ, 
                    'pqOrder' => $orderEPQ,
                    'pqQuestion' => $questionTextEPQ, 
                    'pqUser' => $logged_employee_id,
                    'pqcId' => $categoryEPQ,
                    'pqcName' => (($categoryEPQ == 'new') ? $newCategoryEPQ : null),
                    'pqcNameDe' => (($categoryEPQ == 'new') ? $newDeCategoryEPQ : null), 
                    'pqHasText' => $hasTextEPQ, 
                    'hasRating' => $hasRatingEPQ, 
                    'hasDropdown' => $hasDropdownEPQ,
                    'optionsData' => array(),
                ); 

                if ($numbersOptionsEPQ AND $hasDropdownEPQ == 1) {
                    $numbersOptionsEPQ = json_decode($numbersOptionsEPQ, true);
                    foreach($numbersOptionsEPQ AS $numberOptionEPQ) {
                        $newValuesArray['optionsData'][$numberOptionEPQ] = array(
                            'pqoId' => $_POST['optionsId'.$numberOptionEPQ.'EPQ'] ?? null,
                            'pqoValue' => $numberOptionEPQ,
                            'pqoValueText' => $_POST['textOptions'.$numberOptionEPQ.'EPQ'] ?? null,
                            'pqoValueTextDe' => $_POST['textdeOptions'.$numberOptionEPQ.'EPQ'] ?? null,
                            'pqoValueSubtext' => $_POST['subtextOptions'.$numberOptionEPQ.'EPQ'] ?? null,
                            'pqoValueSubtextDe' => $_POST['subtextdeOptions'.$numberOptionEPQ.'EPQ'] ?? null
                        );
                    }
                    unset($numbersOptionsEPQ);
                }

                /*echo '<h3>New values</h3>';
                print("<pre>".print_r($newValuesArray, true)."</pre>");*/

                $deletedOldValues = array_diff_assoc($arrayQuestionOldDataEPQ['optionsData'], $newValuesArray['optionsData']);

                /*echo '<h3>Delete values</h3>';
                print("<pre>".print_r($deletedOldValues, true)."</pre>");*/
                
                $log_desc = 'JobSoft Postavke - Predefinisana pitanja - Edit predefinisanih pitanja -> Izvršeno editovanje pitanja sa ID = ['.$questionIdEPQ.'].';

                if ($newValuesArray['pqId'] AND $newValuesArray['pqOrder'] AND $newValuesArray['pqQuestion']) {
                    if ($newValuesArray['pqcId'] == 'new') {
                        $query_insert_category = $db->prepare("
                            INSERT INTO idk_pp_question_categories 
                                (
                                    pqc_name, 
                                    pqc_name_de
                                ) 
                            VALUES 
                                (
                                    :pqc_name,
                                    :pqc_name_de
                                )
                        ");
                        $query_insert_category->execute(array(
                            ':pqc_name' => $newValuesArray['pqcName'],
                            ':pqc_name_de' => $newValuesArray['pqcNameDe']
                        ));
                        $newValuesArray['pqcId'] = $db->lastInsertId();
                    } else if ($newValuesArray['pqcId'] == 'without') {
                        $newValuesArray['pqcId'] = null;
                    }

                    $query_update_question = $db->prepare("
                        UPDATE 
                            idk_pp_questions
                        SET
                            pqu_question = :pqu_question, 
                            pqu_user_id = :pqu_user_id,
                            pqu_category_id = :pqu_category_id,
                            pqu_has_text = :pqu_has_text,
                            pqu_has_rating = :pqu_has_rating,
                            pqu_has_dropdown = :pqu_has_dropdown
                        WHERE 
                            pqu_id = :pqu_id
                            AND 
                            pqu_nalog_id = :pqu_nalog_id
                    ");
                    $query_update_question->execute(array(
                        ':pqu_question' => $newValuesArray['pqQuestion'],
                        ':pqu_user_id' => $newValuesArray['pqUser'],
                        ':pqu_category_id' => $newValuesArray['pqcId'],
                        ':pqu_has_text' => $newValuesArray['pqHasText'],
                        ':pqu_has_rating' => $newValuesArray['hasRating'],
                        ':pqu_has_dropdown' => $newValuesArray['hasDropdown'], 
                        ':pqu_nalog_id' => $newValuesArray['pqOrder'],
                        ':pqu_id' => $newValuesArray['pqId']
                    ));

                    if (count($newValuesArray['optionsData']) > 0) {
                        $cnt = 1;
                        foreach($newValuesArray['optionsData'] AS $newValueArray) {
                            if ($newValueArray['pqoId'] != 0) {
                                $query_update_question_option = $db->prepare("
                                    UPDATE
                                        idk_pp_question_options
                                    SET 
                                        pqo_value = :pqo_value, 
                                        pqo_value_text = :pqo_value_text, 
                                        pqo_value_text_de = :pqo_value_text_de,
                                        pqo_value_subtext = :pqo_value_subtext, 
                                        pqo_value_subtext_de = :pqo_value_subtext_de
                                    WHERE 
                                        pqo_id = :pqo_id 
                                        AND 
                                        pqo_question_id = :pqo_question_id
                                ");
                                $query_update_question_option->execute(array(
                                    ':pqo_value' => $cnt, 
                                    ':pqo_value_text' => $newValueArray['pqoValueText'], 
                                    ':pqo_value_text_de' => $newValueArray['pqoValueTextDe'], 
                                    ':pqo_value_subtext' => $newValueArray['pqoValueSubtext'], 
                                    ':pqo_value_subtext_de' => $newValueArray['pqoValueSubtextDe'], 
                                    ':pqo_id' => $newValueArray['pqoId'], 
                                    ':pqo_question_id' => $newValuesArray['pqId']
                                ));
                                $newValuesArray['optionsData'][$newValueArray['pqoValue']]['pqoValue'] = $cnt;
                                $temporary_array_variable = $newValuesArray['optionsData'][$newValueArray['pqoValue']]; 
                                unset($newValuesArray['optionsData'][$newValueArray['pqoValue']]);
                                $newValuesArray['optionsData'][$cnt] = $temporary_array_variable;
                                
                            } else {
                                $query_insert_questions_options = $db->prepare("
                                    INSERT INTO idk_pp_question_options 
                                        ( 
                                            pqo_question_id, 
                                            pqo_value, 
                                            pqo_value_text, 
                                            pqo_value_text_de, 
                                            pqo_value_subtext, 
                                            pqo_value_subtext_de
                                        ) 
                                    VALUES 
                                        (
                                            :pqo_question_id, 
                                            :pqo_value, 
                                            :pqo_value_text, 
                                            :pqo_value_text_de, 
                                            :pqo_value_subtext, 
                                            :pqo_value_subtext_de
                                        ) 
                                ");
                                $query_insert_questions_options->execute(array(
                                    ':pqo_question_id' => $newValuesArray['pqId'],
                                    ':pqo_value' => $cnt, 
                                    ':pqo_value_text' => $newValueArray['pqoValueText'], 
                                    ':pqo_value_text_de' => $newValueArray['pqoValueTextDe'], 
                                    ':pqo_value_subtext' => $newValueArray['pqoValueSubtext'], 
                                    ':pqo_value_subtext_de' => $newValueArray['pqoValueSubtextDe']
                                ));
                                $newValuesArray['optionsData'][$newValueArray['pqoValue']]['pqoId'] = $db->lastInsertId();
                                $newValuesArray['optionsData'][$newValueArray['pqoValue']]['pqoValue'] = $cnt;
                                $temporary_array_variable = $newValuesArray['optionsData'][$newValueArray['pqoValue']];
                                unset($newValuesArray['optionsData'][$newValueArray['pqoValue']]);
                                $newValuesArray['optionsData'][$cnt] = $temporary_array_variable; 
                            }
                            $cnt++;
                            unset($temporary_array_variable);
                        }
                    }

                    if (count($deletedOldValues) > 0) {
                        foreach($deletedOldValues AS $deleteOldValue){
                            $query_delete_question_option = $db->prepare("
                                DELETE FROM 
                                    idk_pp_question_options 
                                WHERE 
                                    pqo_id = :pqo_id
                            ");
                            $query_delete_question_option->execute(array(
                                ':pqo_id' => $deleteOldValue['pqoId']
                            ));
                        }
                    }

                } else {
                    http_response_code(400);
                    die("Nepotpuni ili neispravni podaci. Molimo vas da provjerite i ponovno pošaljete sve potrebne informacije.");
                }

                addToLogs($log_desc, 0);

                /*echo '<h3>New values after update</h3>';
                print("<pre>".print_r($newValuesArray, true)."</pre>");*/

                unset($arrayQuestionOldDataEPQ);
                unset($newValuesArray);
                unset($deletedOldValues);

                header('Location: ../../nalozi?page=open&id='.$orderEPQ.'&tab=jobstep_pp_settings&tabgs=predefinedQuestionsSettings&messPQS=6');

            break; 

            case "add_appointment_questions": 
                 
                $pap_id = $_POST['pap_id'] ?? null; 
                $order_id = $_POST['order_id'] ?? null; 
                $edit_questions_flag = $_POST['edit_questions_flag'] ?? null; 
                $panel_ids = $_POST['panel_ids'] ?? null; 
                $pqu_ids = $_POST['pqu_ids'] ?? null; 
                $setting_mode = $_POST['setting_mode'] ?? null; 
                $appointment_dates = $_POST['appointment_dates'] ?? null; 

                if ($pap_id == null OR $order_id == null OR $edit_questions_flag == null OR $panel_ids == null OR $pqu_ids == null OR $setting_mode == null) {
                    http_response_code(400);
                    die("Nepotpuni ili neispravni podaci. Molimo vas da provjerite i ponovno pošaljete sve potrebne informacije ili da kontaktirate administratora sistema za pomoć!");
                }

                /*
                    Formatting of main data START
                    */
                        $pap_id = intval($pap_id); 
                        $order_id = intval($order_id); 
                        $edit_questions_flag = intval($edit_questions_flag); 

                        $panel_ids_clean = str_replace(['[', ']', '"'], '', $panel_ids); 
                        $panel_ids_exp = explode(',', $panel_ids_clean);
                        $panel_ids_imp = ((count($panel_ids_exp) > 0) ? implode(',', $panel_ids_exp) : 0);
                        
                        $pqu_ids_clean = str_replace(['[', ']', '"'], '', $pqu_ids); 
                        $pqu_ids_exp = explode(',', $pqu_ids_clean);
                        $pqu_ids_imp = ((count($pqu_ids_exp) > 0) ? implode(',', $pqu_ids_exp) : 0);

                        $appointment_dates = (($appointment_dates != '') ? $appointment_dates : null);
                    /*
                    Formatting of main data END
                */

                /*
                    Formatting of questions data START
                    */
                        $flag_ok = 1; 
                        $questions = array();
                        foreach ($panel_ids_exp AS $panel_id_exp) {
                            $pqu_id                 = $_POST['pqu_id_'.$panel_id_exp] ?? null; 
                            $pqu_edited             = $_POST['pqu_edited_'.$panel_id_exp] ?? null;
                            $pqu_order              = $_POST['pqu_order_'.$panel_id_exp] ?? null;
                            $pqu_included           = $_POST['pqu_included_'.$panel_id_exp] ?? null;
                            $pqu_text_questions     = $_POST['pqu_text_questions_'.$panel_id_exp] ?? null;

                            if ($pqu_id == null OR $pqu_edited == null OR $pqu_order == null OR $pqu_included == null OR $pqu_text_questions == null) {
                                $flag_ok = 0; 
                                break; 
                            }

                            $questions[$panel_id_exp] = array(
                                'pqu_id' => $pqu_id,
                                'pqu_edited' => $pqu_edited,
                                'pqu_order' => $pqu_order,
                                'pqu_included' => $pqu_included,
                                'pqu_text_questions' => $pqu_text_questions
                            );
                            unset($panel_id_exp); 
                        } 
                    /*
                    Formatting of questions data END
                */

                if ($flag_ok == 0) {
                    http_response_code(400);
                    die("Nepotpuni ili neispravni podaci za pitanja. Molimo vas da provjerite i ponovno pošaljete sve potrebne informacije ili da kontaktirate administratora sistema za pomoć!");
                }

                /*echo '<br> pap_id: '; var_dump($pap_id); 
                echo '<br> order_id: '; var_dump($order_id);
                echo '<br> edit_questions_flag: '; var_dump($edit_questions_flag);
                echo '<br> panel_ids: '; var_dump($panel_ids_imp);
                echo '<br> pqu_ids: '; var_dump($pqu_ids_imp);
                echo '<br> setting_mode: '; var_dump($setting_mode);
                echo '<br> appointment_dates: '; var_dump($appointment_dates);
                print("<pre>".print_r($questions,true)."</pre>");*/

                $log_desc = 'JobSoft Postavke - Datumi castinga - Dodavanje pitanja -> ';
                $flag_insert_ok = 0;
                $old_questions_for_delete = array();
                $cnt_old_papq = -1;

                if ($edit_questions_flag == 1) {
                    $query_get_old_papq = $db->prepare("
                        SELECT 
                            papq_id 
                        FROM 
                            idk_pp_appointments_questions 
                        WHERE 
                            papq_appointment_id = :papq_appointment_id 
                    "); 
                    $query_get_old_papq->execute(array(
                        ':papq_appointment_id' => $pap_id
                    )); 
                    $cnt_old_papq = $query_get_old_papq->rowCount(); 
                    if ($cnt_old_papq > 0) {
                        while ($row_get_old_papq = $query_get_old_papq->fetch()) {
                            array_push($old_questions_for_delete, $row_get_old_papq['papq_id']); 
                        }
                        if (count($old_questions_for_delete) != $cnt_old_papq) {
                            http_response_code(500);
                            die("Desio se problem prilikom pripreme za brisanje starih podataka o pitanjima ovog termina! Kontaktirajte adminisratora sistema za pomoć!");
                        }
                    } else {
                        http_response_code(500);
                        die("Desio se problem prilikom pripreme za brisanje starih podataka o pitanjima ovog termina! Kontaktirajte adminisratora sistema za pomoć!");
                    }
                }

                //print("<pre>".print_r($old_questions_for_delete,true)."</pre>");

                $insert_values_exp = array();
                $insert_values_imp = ''; 
                foreach ($questions AS $question) {
                    if ($question['pqu_edited'] == 1) {
                        $insert_response = copyQuestionWithNewTextValueArrayR($order_id, $question['pqu_id'], $question['pqu_text_questions']);
                        if ($insert_response['status'] == 1) {
                            addToLogs($log_desc.' '.$insert_response['message'] .' Question ID old = ['.$question['pqu_id'].']. Question ID new = ['.$insert_response['new_id'].']', 0);
                            $question['pqu_id'] = $insert_response['new_id']; 
                        } else {
                            http_response_code(500);
                            die("Desio se problem prilikom kopiranja starog pitanja sa novim tekstom! Kontaktirajte administratora sistema zajedno sa ovom porukom! Detalji problema: Status: {".$insert_response['status']."}, Message: {".$insert_response['message']."}, ID: {".$insert_response['new_id']."}");
                        }
                        unset($insert_response); 
                    }
                    array_push($insert_values_exp, '('.$question['pqu_id'].', '.$pap_id.', '.$question['pqu_order'].')');
                    unset($question); 
                }
                $insert_values_imp = implode(',', $insert_values_exp); 

                //print("<pre>".print_r($insert_values_exp,true)."</pre>");

                if (count($questions) == count($insert_values_exp)) {
                    $query_insert_papq = $db->prepare("
                        INSERT INTO idk_pp_appointments_questions 
                            (
                                papq_question_id, 
                                papq_appointment_id, 
                                papq_order
                            )
                            VALUES
                            ".$insert_values_imp."
                    ");
                    $query_insert_papq->execute();
                    if ($query_insert_papq->rowCount() == count($insert_values_exp)) {
                        $flag_insert_ok = 1;
                        $log_desc = $log_desc. " Korisnik izvršio dodavanje pitanja za casting ID = [".$pap_id."]. Dodane vrijednosti: ".$insert_values_imp.""; 
                        addToLogs($log_desc, 0);
                    } else {
                        http_response_code(500);
                        die("Desio se problem prilikom unosa pitanja ovog termina! Kontaktirajte adminisratora sistema za pomoć!");
                    }

                } else {
                    http_response_code(500);
                    die("Desio se problem prilikom pripreme podataka za unos pitanja ovog termina! Kontaktirajte adminisratora sistema za pomoć!"); 
                }

                if ($edit_questions_flag == 1 AND count($old_questions_for_delete) == $cnt_old_papq AND $flag_insert_ok == 1) {
                    $old_questions_for_delete_imp = implode(',', $old_questions_for_delete); 
                    $query_old_questions_delete = $db->prepare("
                        DELETE FROM idk_pp_appointments_questions 
                        WHERE 
                            papq_id IN (".$old_questions_for_delete_imp.")
                    "); 
                    $query_old_questions_delete->execute();
                    if (count($old_questions_for_delete) != $query_old_questions_delete->rowCount()) {
                        http_response_code(500);
                        die("Desio se problem prilikom brisanja starih podataka o pitanjima ovog termina! Kontaktirajte adminisratora sistema za pomoć!");
                    }
                    unset($old_questions_for_delete_imp);
                }

                unset($insert_values_exp); 
                unset($questions);  
                unset($panel_ids_exp);
                unset($pqu_ids_exp);
                unset($old_questions_for_delete);

                header('Location: ../../nalozi?page=open&id='.$order_id.'&tab=jobstep_pp_settings&tabgs=castingDatesSettings&messCDS='.(($flag_insert_ok == 1) ? 12 : 13).'');
            break; 
            
            default:
                echo 'Undefined page'; 
            break;
        }

    } else {
        header('Location: login.php');
    }
?>