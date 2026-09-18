<?php
    if ($orderId != 0) {

        /*
            Short form for UAS - User Access Settings  
        */

        $queryGetOrderInfoUAS = $db->prepare('
            SELECT 
                nal.nalog_id AS orderId,
                nal.nalog_naziv AS orderName,
                nal.kompanija_id AS orderCompanyId, 
                comp.company_name AS orderCompanyName,
                pp.ppa_id AS partnerId,
                (
                    CASE 
                        WHEN pp.ppa_status = 1 THEN "Active"
                        WHEN pp.ppa_status = 0 THEN "Inactive"
                        ELSE "Undefined"
                    END
                ) AS partnerStatus,
                pp.ppa_company_id AS partnerCompanyId, 
                ppcomp.company_name AS partnerCompanyName
            FROM  
                idk_nalozi nal
            JOIN 
                idk_companies comp
            ON 
                nal.kompanija_id = comp.company_id 
            JOIN 
                idk_pp_partners pp 
            ON 
                nal.nalog_id = pp.ppa_nalog_id 
            JOIN 
                idk_companies ppcomp
            ON 
                pp.ppa_company_id = ppcomp.company_id
            WHERE 
                nal.nalog_id = :orderId
        ');
        $queryGetOrderInfoUAS->execute(array(
            ':orderId' => $orderId
        )); 
        if ($queryGetOrderInfoUAS->rowCount() > 0) {
            $rowGetOrderInfoUAS = $queryGetOrderInfoUAS->fetchAll(PDO::FETCH_ASSOC);
            $companyForAddNew = array();
            $groupedData = array();
            foreach ($rowGetOrderInfoUAS as $row) {
                if (!isset($groupedData[$row['orderId']])) {
                    $groupedData[$row['orderId']] = array(
                        'orderId' => $row['orderId'],
                        'orderName' => $row['orderName'],
                        'orderCompanyId' => $row['orderCompanyId'],
                        'orderCompanyName' => $row['orderCompanyName'],
                        'data' => array()
                    );
                }
                $data = array( 
                    'partnerId' => $row['partnerId'],
                    'partnerCompanyId' => $row['partnerCompanyId'],
                    'partnerCompanyName' => $row['partnerCompanyName'],
                    'partnerStatus' => $row['partnerStatus']
                );
                $groupedData[$row['orderId']]['data'][$row['partnerId']] = $data;
                if ($row['partnerStatus'] == 'Active'){
                    $companyForAddNew[] = $row['partnerCompanyId']; 
                }
                unset($data);
                unset($row);
            }
            $orderInformation = $groupedData;

            if (count($companyForAddNew) > 0) {
                $companyForAddNewIds = implode(',', $companyForAddNew);
                $subConditionFor1039 = (($orderInformation[$orderId]['orderCompanyId'] == 1039) ? '(pu.pu_company_id IN ('.$companyForAddNewIds.') OR pu.pu_company_id = 205)' : 'pu.pu_company_id IN ('.$companyForAddNewIds.')'); 
                
                $queryGetUsersFromCompanies = $db->prepare('
                    SELECT 
                        pu.pu_id AS userId, 
                        CONCAT(pu.pu_fname, " ",pu.pu_lname) As userFullName, 
                        pu.pu_email AS userEmail, 
                        pu.pu_company_id AS userCompanyId, 
                        comp.company_name AS userCompanyName
                    FROM 
                        idk_pp_users pu
                    JOIN 
                        idk_companies comp
                    ON 
                        pu.pu_company_id = comp.company_id
                    LEFT JOIN 
                        idk_pp_user_access pua
                    ON 
                        pu.pu_id = pua.pua_user_id AND pua.pua_nalog_id = :orderId
                    WHERE 
                        pu.pu_status = 1 
                        AND 
                        '.$subConditionFor1039.'
                        /*
                            Ovim uslovom se omogućuje dodavanje neke naše kompanije gdje će biti smješteni svi naši zaposlenici
                            (
                                pu.pu_company_id IN ('.$companyForAddNewIds.')
                                OR
                                pu.pu_company_id = 5
                            )
                        */
                        AND 
                        pua.pua_id is null
                    ORDER BY 
                        pu.pu_id 
                    ASC
                ');
                $queryGetUsersFromCompanies->execute(array(
                    ':orderId' => $orderId
                )); 
                if ($queryGetUsersFromCompanies->rowCount() > 0) { 
                    $rowGetUsersFromCompanies = $queryGetUsersFromCompanies->fetchAll(PDO::FETCH_ASSOC);
                } else{
                    $rowGetUsersFromCompanies = [];
                } 
                unset($companyForAddNewIds); 
                unset($subConditionFor1039);
            }

            ?>
                <div class="row">
                    <div class="col-xs-12">
                        <!--
                            Header options START 
                            -->
                                <div class="row">
                                    <div class="col-xs-12 text-right">
                                        <a href="" data-toggle="modal" data-target="#addNewUAS" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive">
                                            <i class="fa fa-plus" aria-hidden="true">
                                            </i>
                                            <span>
                                                Dodaj pristup
                                            </span>
                                        </a>
                                        <div class="modal material-modal material-modal_primary fade text-left" id="addNewUAS">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content material-modal__content">
                                                    <div class="modal-header material-modal__header">
                                                        <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                                        <h4 class="modal-title material-modal__title"><i style = "margin-right: 10px;" class="fa fa-plus" aria-hidden="true"></i>Dodaj pristup</h4>
                                                    </div> 
                                                    <div class="modal-body material-modal__body">
                                                        <?php
                                                            if (count($rowGetUsersFromCompanies) > 0) { 
                                                                ?>
                                                                    <form action="<?php getSiteURL(); ?>jobsoft_settings/backend/do_settings.php?page=add_new_user_access" enctype="multipart/form-data" method="post" role="form" class="form-horizontal" id="addNewUASForm">
                                                                        <input type="hidden" id="order_id" name="order_id" value="<?php echo $orderId; ?>">
                                                                        <div class="form-group">
                                                                            <div class="col-md-offset-2 col-sm-8">
                                                                                <label for="user_id" class="col-sm-4 control-label">
                                                                                    <span class="text-danger">*</span> Korisnik
                                                                                </label>
                                                                                <div class="col-sm-8">
                                                                                    <div class="">
                                                                                        <select class="selectpicker" name="user_id" id="user_id" title="Odaberite korisnika" required>
                                                                                            <?php 
                                                                                                foreach ($rowGetUsersFromCompanies AS $rowUserFromCompany) {
                                                                                                    ?>
                                                                                                        <option 
                                                                                                            value="<?php echo $rowUserFromCompany['userId']; ?>" 
                                                                                                            data-subtext='
                                                                                                                <?php 
                                                                                                                    echo (($rowUserFromCompany['userCompanyId'] == $orderInformation[$orderId]['orderCompanyId']) ? '<span class="label label-primary">User glavne kompanije '.$rowUserFromCompany['userCompanyName'].'</span>' : ((in_array($rowUserFromCompany['userCompanyId'], $companyForAddNew)) ? '<span class="label label-warning">User partner kompanije '.$rowUserFromCompany['userCompanyName'].'</span>' : '<span class="label label-danger">User kompanije '.$rowUserFromCompany['userCompanyName'].'</span>') );
                                                                                                                ?>
                                                                                                            '
                                                                                                        >
                                                                                                            <?php echo $rowUserFromCompany['userFullName']; ?>
                                                                                                        </option>
                                                                                                    <?php
                                                                                                    unset($rowUserFromCompany); 
                                                                                                }
                                                                                            ?>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <div class="col-md-offset-2 col-sm-8">
                                                                                <label for="user_super_admin" class="col-sm-4 control-label">
                                                                                    <span class="text-danger">*</span> SuperAdmin
                                                                                </label>
                                                                                <div class="col-sm-8">
                                                                                    <div class="">
                                                                                        <select class="selectpicker" name="user_super_admin" id="user_super_admin" title="Odaberite opciju" required>
                                                                                            <option value="1">Aktivan</option>
                                                                                            <option value="0">Neaktivan</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <div class="col-md-offset-2 col-sm-8">
                                                                                <label for="user_admin" class="col-sm-4 control-label">
                                                                                    Admin
                                                                                </label>
                                                                                <div class="col-sm-8">
                                                                                    <div class="">
                                                                                        <select class="selectpicker" name="user_admin[]" id="user_admin" title="Odaberite opciju" multiple>
                                                                                            <?php 
                                                                                                foreach($orderInformation[$orderId]['data'] as $dataOrderInformation) {
                                                                                                    if ($dataOrderInformation['partnerStatus'] == 'Active') {
                                                                                                        ?>
                                                                                                            <option 
                                                                                                                value='<?php echo $dataOrderInformation['partnerId']; ?>'
                                                                                                                data-subtext='
                                                                                                                    <?php 
                                                                                                                        echo ( ($dataOrderInformation['partnerStatus'] != 'Undefined') ? ( ($dataOrderInformation['partnerStatus'] == 'Active') ? '<span class="label label-success">Aktivan</span>' : '<span class="label label-danger">Neaktivan</span>' ) : '<span class="label label-default">Nepoznat</span>' ); 
                                                                                                                        echo ' - '; 
                                                                                                                        echo ( ($dataOrderInformation['partnerCompanyId'] == $orderInformation[$orderId]['orderCompanyId']) ? '<span class="label label-primary">Glavna kompanija</span>' : '<span class="label label-warning">Partner kompanija</span>' );
                                                                                                                    ?>
                                                                                                                '
                                                                                                            >
                                                                                                                <?php echo $dataOrderInformation['partnerCompanyName']; ?>
                                                                                                            </option>
                                                                                                        <?php
                                                                                                    }
                                                                                                    unset($dataOrderInformation); 
                                                                                                }
                                                                                            ?>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer material-modal__footer">
                                                                            <button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
                                                                            <button type="submit" class="btn btn-primary material-btn material-btn_primary" form="addNewUASForm"><i class="fa fa-check-square-o" aria-hidden="true"></i> Spremi</button>
                                                                        </div>
                                                                    </form>
                                                                <?php 
                                                            } else {
                                                                ?> 
                                                                    <div class="alert alert-warning text-center" role="alert">
                                                                        <h4><strong>Upozorenje</strong></h4>
                                                                        Svi korisnici kompanije/a su dodani u pristupe ovog naloga!
                                                                        <br>
                                                                        <br>
                                                                        <small>Koristite opciju <strong>Uredi pristup</strong> u tabeli ispod ako se traženi korisnik već nalazi u tabeli ili koristite opciju <strong>Dodaj korisnika</strong> na profilu pripadajuće Kompanije u rubrici <strong>JobSoft korisnici</strong> ako korisnik nema profil za JobSoft.</small>
                                                                    </div>
                                                                <?php 
                                                            }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                    if (count($rowGetUsersFromCompanies) > 0) { 
                                        ?>
                                            <script>
                                                $('#addNewUAS').on('hidden.bs.modal', function () {
                                                    $('#user_id', '#addNewUASForm').val('').selectpicker('refresh');
                                                    $('#user_super_admin', '#addNewUASForm').val('').selectpicker('refresh');
                                                    $('#user_admin', '#addNewUASForm').val([]).selectpicker('refresh');
                                                });
                                            </script>
                                        <?php 
                                    }
                                ?>
                            <!--
                            Header options END 
                        -->
                        <hr>
                        <!-- 
                            Order Info START
                            -->
                                <div class="row">
                                    <div class="col-xs-12 text-center">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <style>
                                                    #orderInfoUAS>tbody>tr>td, #orderInfoUAS>thead>tr>th {
                                                        vertical-align: middle;
                                                    }
                                                    #panelOrderInfoUAS .scrollBarHorizontal::-webkit-scrollbar {
                                                        height:5px;
                                                        margin-top: 10px;
                                                    }
                                                    #panelOrderInfoUAS .scrollBarHorizontal::-webkit-scrollbar-thumb {
                                                        background: #1D84C0;
                                                        border-radius: 5px;
                                                    }
                                                    #panelOrderInfoUAS .scrollBarHorizontal::-webkit-scrollbar-track {
                                                        background-color: #e9ecef;
                                                        border-radius: 5px;
                                                    }
                                                </style>
                                                <div class="row">
                                                    <div class="col-lg-offset-2 col-lg-8">
                                                        <h4 style = "font-weight: bold;"><i class="fa fa-info-circle" style = "margin-right: 10px;" aria-hidden="true"></i>Informacije naloga</h4>
                                                    </div>
                                                    <div class="col-lg-offset-2 col-lg-8" id="panelOrderInfoUAS">
                                                        <div class="table-responsive scrollBarHorizontal">
                                                            <table class="table table-bordered table-sm" id = "orderInfoUAS">
                                                                <thead>
                                                                    <th class="text-center">Nalog</th>
                                                                    <th class="text-center">Kompanija</th>
                                                                    <th class="text-center">Partner</th>
                                                                    <th class="text-center">Partner status</th>
                                                                    <th class="text-center">Partner uloga</th>
                                                                </thead>
                                                                <tbody>
                                                                    <?php 
                                                                        foreach ($groupedData as $dataOrder) {
                                                                            $dataOrderInfoFlag = false; 
                                                                            foreach($dataOrder['data'] as $dataPartner) {
                                                                                ?>  
                                                                                    <tr>
                                                                                        <?php 
                                                                                            if (!$dataOrderInfoFlag) {
                                                                                                ?>
                                                                                                    <td class="text-center" rowspan="<?php echo count($dataOrder['data']); ?>"><?php echo $dataOrder['orderName']; ?></td>
                                                                                                    <td class="text-center" rowspan="<?php echo count($dataOrder['data']); ?>"><?php echo $dataOrder['orderCompanyName']; ?></td>
                                                                                                <?php
                                                                                                $dataOrderInfoFlag = true; 
                                                                                            }
                                                                                        ?>
                                                                                        <td class="text-center">
                                                                                            <a href="<?php getSiteURL(); ?>companies?page=open&id=<?php echo $dataPartner['partnerCompanyId']; ?>&tab=company_users" title="Pogledajte korisnike kompanije" target="_blank">
                                                                                                <?php echo $dataPartner['partnerCompanyName']; ?>
                                                                                            </a>
                                                                                        </td>
                                                                                        <td class="text-center"><?php echo ( ($dataPartner['partnerStatus'] != 'Undefined') ? ( ($dataPartner['partnerStatus'] == 'Active') ? '<span class="label label-success">Aktivan</span>' : '<span class="label label-danger">Neaktivan</span>' ) : '<span class="label label-default">Nepoznat</span>' ); ?></td>
                                                                                        <td class="text-center" >
                                                                                            <?php 
                                                                                                if ($dataPartner['partnerCompanyId'] == $dataOrder['orderCompanyId']) {
                                                                                                    echo '<span class="label label-primary">Glavna kompanija</span>';
                                                                                                } else {
                                                                                                    echo '<span class="label label-warning">Partner kompanija</span>';
                                                                                                }
                                                                                            ?>
                                                                                        </td>
                                                                                    </tr>
                                                                                <?php
                                                                                unset($dataPartner); 
                                                                            } 
                                                                            unset($dataOrder);
                                                                            unset($dataOrderInfoFlag);
                                                                        }
                                                                    ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <!-- 
                            Order Info END 
                        -->
                        <hr>
                        <!-- 
                            Message Start
                            -->
                                <?php 
                                    if(isset($_GET['message1UAS'])) {
                                        $enabledMessages1UAS = array(1,2,3,4,5,6,7,8);
                                        $message1UAS = $_GET['message1UAS'];
                                        $resultMesssage1UAS = '';
                                        if(in_array($message1UAS, $enabledMessages1UAS)) {
                                            if($message1UAS == 1){
                                                $resultMesssage1UAS = '<div class="alert alert-danger text-center" role="alert"><h4><strong>Greška</strong></h4><br>Akcija nije izvršena!<br>Postoji problem sa slanjem podataka za ažuriranje SuperAdmin pristupa! Obratite se administratoru sistema!</div>';
                                            }elseif($message1UAS == 2){
                                                $resultMesssage1UAS = '<div class="alert alert-success text-center" role="alert"><h4><strong>Uspješno izvršeno</strong></h4><br>Uspješno ste izvršili ažuriranje SuperAdmin pristupa!</div>';
                                            }elseif($message1UAS == 3){
                                                $resultMesssage1UAS = '<div class="alert alert-danger text-center" role="alert"><h4><strong>Greška</strong></h4><br>Akcije nije izvršena!<br>Sistem nije pronašao korisnika nad kojim je moguće izvršiti ažuriranje SuperAdmin pristupa! Obratite se administratoru sistema!</div>';
                                            }elseif($message1UAS == 4){
                                                $resultMesssage1UAS = '<div class="alert alert-success text-center" role="alert"><h4><strong>Uspješno izvršeno</strong></h4><br>Uspješno ste izvršili dodavanje SuperAdmin pristupa!</div>';
                                            }elseif($message1UAS == 5){
                                                $resultMesssage1UAS = '<div class="alert alert-danger text-center" role="alert"><h4><strong>Greška</strong></h4><br>Akcije nije izvršena!<br>Sistem nije izvršio dodavanje SuperAdmin pristupa! Obratite se administratoru sistema!</div>';
                                            }elseif($message1UAS == 6){
                                                $resultMesssage1UAS = '<div class="alert alert-danger text-center" role="alert"><h4><strong>Greška</strong></h4><br>Akcija nije izvršena!<br>Postoji problem sa slanjem podataka za dodavanje novog korisnika sa SuperAdmin pristupom! Obratite se administratoru sistema!</div>';
                                            }elseif($message1UAS == 7){
                                                $resultMesssage1UAS = '<div class="alert alert-success text-center" role="alert"><h4><strong>Uspješno izvršeno</strong></h4><br>Uspješno ste izvršili dodavanje novog korisnika sa SuperAdmin pristupom!</div>';
                                            }elseif($message1UAS == 8){
                                                $resultMesssage1UAS = '<div class="alert alert-danger text-center" role="alert"><h4><strong>Greška</strong></h4><br>Akcije nije izvršena!<br>Sistem nije izvršio dodavanje novog korisnika sa SuperAdmin pristupom! Obratite se administratoru sistema!</div>';
                                            }
                                        } else {
                                            $resultMesssage1UAS = '<div class="alert alert-danger text-center" role="alert"><h4><strong>Greška</strong></h4><br>Nepredviđena poruka odgovora za SuperAdmin pristup! Kontaktirajte administratora sistema!</div>';
                                        }
                                        
                                        ?>
                                            <div class="row">
                                                <div class="col-xs-offset-2 col-xs-8">
                                                    <?php 
                                                        echo $resultMesssage1UAS;
                                                    ?>
                                                </div>
                                            </div>
                                            <hr>
                                        <?php 
                                        unset($enabledMessages1UAS);
                                    }

                                    if(isset($_GET['message2UAS'])) {
                                        $enabledMessages2UAS = array(1,2,3,4,5,6);
                                        $message2UAS = $_GET['message2UAS'];
                                        $resultMesssage2UAS = '';
                                        if(in_array($message2UAS, $enabledMessages2UAS)) {
                                            if($message2UAS == 1){
                                                $resultMesssage2UAS = '<div class="alert alert-danger text-center" role="alert"><h4><strong>Greška</strong></h4><br>Akcija nije izvršena!<br>Postoji problem sa slanjem podataka za ažuriranje Admin pristupa! Obratite se administratoru sistema!</div>';
                                            }elseif($message2UAS == 2){
                                                $resultMesssage2UAS = '<div class="alert alert-danger text-center" role="alert"><h4><strong>Greška</strong></h4><br>Akcije nije ispravno izvršena!<br>Prilikom ažuriranja/dodavanja Admin pristupa desio se problem. Obratite se administratoru sistema!</div>';
                                            }elseif($message2UAS == 3){
                                                $resultMesssage2UAS = '<div class="alert alert-success text-center" role="alert"><h4><strong>Uspješno izvršeno</strong></h4><br>Uspješno ste izvršili ažuriranje/dodavanje Admin pristupa!</div>';
                                            }elseif($message2UAS == 4){
                                                $resultMesssage2UAS = '<div class="alert alert-danger text-center" role="alert"><h4><strong>Greška</strong></h4><br>Akcija nije izvršena!<br>Postoji problem sa slanjem podataka za dodavanje novog korisnika sa Admin pristupom! Obratite se administratoru sistema!</div>';
                                            }elseif($message2UAS == 5){
                                                $resultMesssage2UAS = '<div class="alert alert-danger text-center" role="alert"><h4><strong>Greška</strong></h4><br>Akcije nije ispravno izvršena!<br>Prilikom dodavanja novog korisnika sa Admin pristupom desio se problem. Obratite se administratoru sistema!</div>';
                                            }elseif($message2UAS == 6){
                                                $resultMesssage2UAS = '<div class="alert alert-success text-center" role="alert"><h4><strong>Uspješno izvršeno</strong></h4><br>Uspješno ste izvršili dodavanje novog korisnika sa Admin pristupom!</div>';
                                            }
                                        } else {
                                            $resultMesssage2UAS = '<div class="alert alert-danger text-center" role="alert"><h4><strong>Greška</strong></h4><br>Nepredviđena poruka odgovora za Admin pristup! Kontaktirajte administratora sistema!</div>';
                                        }
                                        
                                        ?>
                                            <div class="row">
                                                <div class="col-xs-offset-2 col-xs-8">
                                                    <?php 
                                                        echo $resultMesssage2UAS;
                                                    ?>
                                                </div>
                                            </div>
                                            <hr>
                                        <?php 
                                        unset($enabledMessages2UAS);
                                    }
                                ?>
                            <!-- 
                            Message End
                        -->
                    </div>
                </div>
                
            <?php

            unset($groupedData); 
            unset($rowGetOrderInfoUAS);
            unset($companyForAddNew);
            unset($rowGetUsersFromCompanies); 

            $queryGetAllUAS = $db->prepare('
                SELECT 
                    pu.pu_id AS userId, 
                    pu.pu_status AS userStatusValue, 
                    (
                        CASE 
                            WHEN pu.pu_status = 1 THEN "Active"
                            WHEN pu.pu_status = 0 THEN "Inactive"
                            ELSE "Undefined"
                        END 
                    ) AS userStatusText,
                    CONCAT(pu.pu_fname, " ", pu.pu_lname) AS userFullName, 
                    pu.pu_email AS userEmail,
                    pu.pu_company_id AS userCompanyValue, 
                    ccpu.company_name AS userCompanyText,
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
                JOIN 
                    idk_pp_users pu 
                ON 
                    pua.pua_user_id = pu.pu_id
                JOIN 
                    idk_companies ccpu 
                ON 
                    pu.pu_company_id = ccpu.company_id
                WHERE 
                    pua.pua_nalog_id = :orderId
                    AND 
                    pu.pu_status = 1
                ORDER BY
                    pu.pu_id 
                ASC
            '); 
            $queryGetAllUAS->execute(array(
                ":orderId" => $orderId
            ));
            
            if ($queryGetAllUAS->rowCount() > 0) { 
                $rowGetAllUAS = $queryGetAllUAS->fetchAll(PDO::FETCH_ASSOC);
                
                $groupedData = array();

                foreach ($rowGetAllUAS as $row) {
                    $userId = $row['userId'];

                    if (!isset($groupedData[$userId])) {
                        $groupedData[$userId] = array(
                            'userId' => $row['userId'],
                            'userStatusValue' => $row['userStatusValue'],
                            'userStatusText' => $row['userStatusText'],
                            'userFullName' => $row['userFullName'],
                            'userEmail' => $row['userEmail'],
                            'userCompanyValue' => $row['userCompanyValue'],
                            'userCompanyText' => $row['userCompanyText'],
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
                        'accessOrderValue' => $row['accessOrderValue'],
                        'accessOrderText' => $row['accessOrderText'],
                        'accessOrderCompanyValue' => $row['accessOrderCompanyValue'],
                        'accessOrderCompanyText' => $row['accessOrderCompanyText'],
                        'accessPartnerValue' => $row['accessPartnerValue'],
                        'accessPartnerText' => $row['accessPartnerText'],
                    );

                    $groupedData[$userId][$accessTypeText]['data'][] = $data;

                    unset($userId); 
                    unset($accessTypeText); 
                    unset($data);
                    unset($row);
                }

                //print("<pre>".print_r($groupedData,true)."</pre>");

                ?>
                    <!-- 
                        Content START 
                        -->
                            <div class="row">
                                <div class="col-xs-12">
                                    <script>
                                        $(document).ready(function(){
                                            $('#allUAS').DataTable({
                                                responsive: true,
                                                "order": [[ 0, "asc" ]],
                                                "bAutoWidth": false,
                                                "aoColumns": [
                                                    { "width": "4%"},
                                                    { "width": "13.5%" },
                                                    { "width": "14%" },
                                                    { "width": "15%" },
                                                    { "width": "8%" },
                                                    { "width": "10%" },
                                                    { "width": "27.5%", "bSortable": false },
                                                    { "width": "8%", "bSortable": false }
                                                ],
                                            });
                                        });
                                    </script>
                                    <table id="allUAS" class="display" cellspacing="0" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th class="text-center">#</th>
                                                <th class="text-center">Ime i prezime</th>
                                                <th class="text-center">Email</th>
                                                <th class="text-center">Kompanija korisnika</th>
                                                <th class="text-center">Status korisnika</th>
                                                <th class="text-center">SuperAdmin pristup</th>
                                                <th class="text-center">Admin pristup</th>
                                                <th class="text-center">Akcija</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                $cnt = 0; 
                                                foreach ($groupedData AS $userData) {
                                                    $cnt = $cnt + 1;
                                                    ?>
                                                        <tr>
                                                            <td class="text-center"><?php echo $cnt; ?></td>
                                                            <td class="text-center"><?php echo $userData['userFullName']; ?></td>
                                                            <td class="text-center"><?php echo $userData['userEmail']; ?></td>
                                                            <td class="text-center"><?php echo $userData['userCompanyText']; ?></td>
                                                            <td class="text-center"><?php echo ( ($userData['userStatusText'] != 'Undefined') ? ( ($userData['userStatusText'] == 'Active') ? '<span class="label label-success">Aktivan</span>' : '<span class="label label-danger">Neaktivan</span>' ) : '<span class="label label-default">Nepoznat</span>' ); ?></td>
                                                            <td class="text-center">
                                                                <?php 
                                                                    $superAdminErrorFlag = 0; 
                                                                    if (count($userData['SuperAdmin']['data']) > 0){
                                                                        if (count($userData['SuperAdmin']['data']) == 1) {
                                                                            echo ( ($userData['SuperAdmin']['data'][0]['accessStatusText'] != 'Undefined') ? ( ($userData['SuperAdmin']['data'][0]['accessStatusText'] == 'Active') ? '<span class="label label-success">Aktivan</span>' : '<span class="label label-danger">Neaktivan</span>' ) : '<span class="label label-default">Nepoznat</span>' );
                                                                        } else {
                                                                            $superAdminErrorFlag = 1;
                                                                            echo '<span class="label label-danger"><i class="fa fa-exclamation-circle" title="Postoji problem sa permisijama korisnika! Kontaktirajte administratora sistema!" aria-hidden="true"></i></span>';
                                                                        }
                                                                    } else {
                                                                        echo '<span class="label label-danger">Neaktivan</span>';
                                                                    }
                                                                ?>
                                                            </td>
                                                            <td class="text-center">
                                                                <?php 
                                                                    $adminDataArray = array();
                                                                    $adminDataPrint = '';
                                                                    if (count($userData['Admin']['data']) > 0){
                                                                        foreach($userData['Admin']['data'] AS $userAdminData) {
                                                                            array_push($adminDataArray, ''.( ($userAdminData['accessStatusText'] != 'Undefined') ? ( ($userAdminData['accessStatusText'] == 'Active') ? '<span class="label label-success" title="Aktivan pristup!">'.$userAdminData['accessPartnerText'].'</span>' : '<span class="label label-danger" title="Neaktivan pristup!">'.$userAdminData['accessPartnerText'].'</span>' ) : '<span class="label label-warning" title="Nepoznat status pristupa za Partnera!">'.$userAdminData['accessPartnerText'].'</span>' ).'');
                                                                        }
                                                                        $adminDataPrint = implode(' ', $adminDataArray); 
                                                                        unset($userAdminData); 
                                                                        echo $adminDataPrint; 
                                                                    } else {
                                                                        echo '<span class="label label-danger">Neaktivan/i</span>';
                                                                    }
                                                                    unset($adminDataArray);
                                                                    unset($adminDataPrint);
                                                                ?>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="btn-group material-btn-group">
                                                                    <button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i><span class="caret material-btn__caret"></span></button>
                                                                    <ul style = "top:33px; left:-60px; min-width:185px !important;" class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
                                                                        <li>
                                                                            <a 
                                                                                class="material-dropdown-menu__link"
                                                                                onclick="changeUAS(this)"
                                                                                data-user_id ="<?php echo $userData['userId']; ?>"
                                                                                data-user_full_name = "<?php echo $userData['userFullName']; ?>"
                                                                                data-user_company = "<?php echo $userData['userCompanyValue']; ?>"
                                                                                data-order_id ="<?php echo $orderId; ?>"
                                                                                data-super_admin_error_flag = "<?php echo $superAdminErrorFlag; ?>"
                                                                            >
                                                                                <i class="fa fa-pencil-square" aria-hidden="true"></i> 
                                                                                Uredi pristup
                                                                            </a>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php 
                                                    unset($superAdminErrorFlag);
                                                    unset($userData); 
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <!-- 
                        Content START 
                    -->
                    <!-- 
                        Modal START 
                        -->
                            <div class="modal material-modal material-modal_primary fade text-left" id="changeUAS">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content material-modal__content">
                                        <div class="modal-header material-modal__header">
                                            <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title material-modal__title"><i style = "margin-right: 10px;" class="fa fa-pencil-square" aria-hidden="true"></i><span id="modal-title-text"></span></h4>
                                        </div> 
                                        <div class="modal-body material-modal__body">
                                            <form action="<?php getSiteURL(); ?>jobsoft_settings/backend/do_settings.php?page=edit_user_access_value" enctype="multipart/form-data" method="post" role="form" class="form-horizontal" id="changeUASForm">
                                                <input type="hidden" id="user_company" name="user_company">
                                                <input type="hidden" id="user_id" name="user_id">
                                                <input type="hidden" id="order_id" name="order_id">
                                                <div class="form-group">
                                                    <div class="col-md-offset-2 col-sm-8">
                                                        <label for="user_super_admin" class="col-sm-4 control-label">
                                                            <span class="text-danger">*</span> SuperAdmin
                                                        </label>
                                                        <div class="col-sm-8">
                                                            <div class="">
                                                                <select class="selectpicker" name="user_super_admin" id="user_super_admin" title="Odaberite opciju" required>
                                                                    <option value="1">Aktivan</option>
                                                                    <option value="0">Neaktivan</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="col-md-offset-2 col-sm-8">
                                                        <label for="user_admin" class="col-sm-4 control-label">
                                                            <span class="text-danger">*</span> Admin
                                                        </label>
                                                        <div class="col-sm-8">
                                                            <div class="">
                                                                <select class="selectpicker" name="user_admin[]" id="user_admin" title="Odaberite opciju" multiple>
                                                                    <?php 
                                                                        foreach($orderInformation[$orderId]['data'] as $dataOrderInformation) {
                                                                            if ($dataOrderInformation['partnerStatus'] == 'Active') {
                                                                                ?>
                                                                                    <option 
                                                                                        value='<?php echo $dataOrderInformation['partnerId']; ?>'
                                                                                        data-subtext='
                                                                                            <?php 
                                                                                                echo ( ($dataOrderInformation['partnerStatus'] != 'Undefined') ? ( ($dataOrderInformation['partnerStatus'] == 'Active') ? '<span class="label label-success">Aktivan</span>' : '<span class="label label-danger">Neaktivan</span>' ) : '<span class="label label-default">Nepoznat</span>' ); 
                                                                                                echo ' - '; 
                                                                                                echo ( ($dataOrderInformation['partnerCompanyId'] == $orderInformation[$orderId]['orderCompanyId']) ? '<span class="label label-primary">Glavna kompanija</span>' : '<span class="label label-warning">Partner kompanija</span>' );
                                                                                            ?>
                                                                                        '
                                                                                    >
                                                                                        <?php echo $dataOrderInformation['partnerCompanyName']; ?>
                                                                                    </option>
                                                                                <?php
                                                                            }
                                                                            unset($dataOrderInformation); 
                                                                        }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer material-modal__footer">
                                                    <button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
                                                    <button type="submit" class="btn btn-primary material-btn material-btn_success" form="changeUASForm"><i class="fa fa-check-square-o" aria-hidden="true"></i> Spremi</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php 
                                unset($orderInformation);
                            ?>
                        <!-- 
                        Modal END  
                    -->
                    <!-- 
                        Script START 
                        -->
                            <script>
                                function changeUAS(thisRow) {
                                    var user_id                 = $(thisRow).data('user_id');
                                    var user_company            = $(thisRow).data('user_company'); 
                                    var user_full_name          = $(thisRow).data('user_full_name');
                                    var order_id                = $(thisRow).data('order_id');
                                    var super_admin_error_flag  = $(thisRow).data('super_admin_error_flag');

                                    if (super_admin_error_flag == 0) {
                                        getChangeModalTitleUAS(user_full_name);
                                        setHiddenFormDataUAS(user_id, user_company, order_id);
                                        getFreshDataUAS(user_id, user_company, order_id);
                                    } else {
                                        alert('Postoji problem sa permisijama korisnika! Kontaktirajte administratora sistema!'); 
                                    }
                                };

                                $('#changeUAS').on('hidden.bs.modal', function () {
                                    resetFormDataUAS();
                                });

                                function resetFormDataUAS() {
                                    setHiddenFormDataUAS('','','');
                                    $('#user_super_admin', '#changeUASForm').val('').selectpicker('refresh');
                                    $('#user_admin', '#changeUASForm').val([]).selectpicker('refresh');
                                }; 

                                function setHiddenFormDataUAS(user_id, user_company, order_id) {
                                    if (user_id != '' && user_company != '' && order_id != '') {
                                        $('#user_id', '#changeUASForm').val(user_id);
                                        $('#user_company', '#changeUASForm').val(user_company);
                                        $('#order_id', '#changeUASForm').val(order_id);
                                    } else {
                                        $('#changeUASForm :input:hidden').removeAttr('value');
                                    }
                                }; 

                                function getChangeModalTitleUAS(user_full_name) {
                                    if (user_full_name != '') {
                                        $('#modal-title-text', '#changeUAS').text('Uredi pristupe korisnika ' + user_full_name);
                                    } else {
                                        $('#modal-title-text', '#changeUAS').text('');
                                    }
                                };

                                function getFreshDataUAS(user_id, user_company, order_id) {
                                    $.ajax({
                                        url: 'jobsoft_settings/backend/do_settings.php?page=get_user_access_value',
                                        type: 'POST',
                                        cache: false,
                                        data:{'user_id': user_id, 'user_company': user_company, 'order_id': order_id},
                                        success : function (result){
                                            var resultDecode = JSON.parse(result);
                                            if (resultDecode.status === 1) {
                                                resultDecode.data.forEach(function(rezultItem){
                                                    
                                                    switch (rezultItem.accessType) {
                                                        case '1':
                                                            $('#user_super_admin', '#changeUASForm').val(rezultItem.accessStatus).selectpicker('refresh');
                                                        break; 
                                                        case '2':
                                                            if (rezultItem.accessStatus == '1') {
                                                                var selectedValuesUserAdmin = $('#user_admin', '#changeUASForm').val() || [];
                                                                selectedValuesUserAdmin.push(rezultItem.accessPartnerId);
                                                                $('#user_admin', '#changeUASForm').val(selectedValuesUserAdmin).selectpicker('refresh');
                                                            }
                                                        break; 
                                                    }
                                                    $('#changeUAS').modal('show');
                                                });
                                            } else {
                                                alert(resultDecode.message);
                                            }
                                        },
                                        error: function (xhr, ajaxOptions, thrownError) {
                                            alert(xhr.status);
                                            alert(thrownError);
                                        }
                                    });
                                };
                            </script>
                        <!-- 
                        Script END 
                    -->

                <?php 

                unset($rowGetAllUAS); 
                unset($groupedData); 
            } else {
                ?>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="alert alert-warning text-center" role="alert">
                                <h4><strong>Upozorenje</strong></h4>
                                <br>
                                Nema aktivnih postavki pristupa za ovaj nalog!
                            </div>
                        </div>
                    </div>
                <?php 
            }
        } else {
            ?>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="alert alert-warning text-center" role="alert">
                            <h4><strong>Upozorenje</strong></h4>
                            <br>
                            Nije aktiviran pristup JobSoftu za ovaj nalog!
                        </div>
                    </div>
                </div>
            <?php 
        }
    }
?>