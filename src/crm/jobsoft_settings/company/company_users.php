<?php 
    /*
        This file is included in the companies.php file
    */

    $companyId = intval($company_id);

    if ($companyId != 0) {
        $random_password = generateRandomPassword(10);
        ?> 
            <div class="row">
                <div class="col-xs-12">
                    <!--
                        Header options START 
                        -->
                            <div class="row">
                                <div class="col-xs-12 text-right">
                                    <a href="" data-toggle="modal" data-target="#addCompanyUser" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive">
                                        <i class="fa fa-plus" aria-hidden="true">
                                        </i>
                                        <span>
                                            Dodaj korisnika
                                        </span>
                                    </a>
                                    <div class="modal material-modal material-modal_primary fade text-left" id="addCompanyUser">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content material-modal__content">
                                                <div class="modal-header material-modal__header">
                                                    <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                                    <h4 class="modal-title material-modal__title"><i style = "margin-right: 10px;" class="fa fa-plus" aria-hidden="true"></i>Dodaj korisnika</h4>
                                                </div> 
                                                <div class="modal-body material-modal__body">
                                                    <form action="<?php getSiteURL(); ?>jobsoft_settings/backend/do_settings.php?page=add_company_user" enctype="multipart/form-data" method="post" role="form" class="form-horizontal" id="addCompanyUserCUForm">
                                                        <input type="hidden" id="company_id" name="company_id" value="<?php echo $companyId; ?>">
                                                        <div class="form-group">
                                                            <div class="col-md-offset-2 col-sm-8">
                                                                <label for="user_first_name" class="col-sm-3 control-label">
                                                                    <span class="text-danger">*</span> Ime:
                                                                </label>
                                                                <div class="col-sm-8">
                                                                    <div class="materail-input-block materail-input-block_primary">
                                                                        <input class="form-control materail-input" type="text" name="user_first_name" id="user_first_name" placeholder="Unesite ime" autocomplete="off" required>
                                                                        <span class="materail-input-block__line">
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="col-md-offset-2 col-sm-8">
                                                                <label for="user_last_name" class="col-sm-3 control-label">
                                                                    <span class="text-danger">*</span> Prezime:
                                                                </label>
                                                                <div class="col-sm-9">
                                                                    <div class="materail-input-block materail-input-block_primary">
                                                                        <input class="form-control materail-input" type="text" name="user_last_name" id="user_last_name" placeholder="Unesite prezime" autocomplete="off" required>
                                                                        <span class="materail-input-block__line">
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="col-md-offset-2 col-sm-8">
                                                                <label for="user_email" class="col-sm-3 control-label">
                                                                    <span class="text-danger">*</span> Email:
                                                                </label>
                                                                <div class="col-sm-9">
                                                                    <div class="materail-input-block materail-input-block_primary">
                                                                        <input class="form-control materail-input" type="email" name="user_email" id="user_email" placeholder="Unesite email" autocomplete="off" required>
                                                                        <span class="materail-input-block__line">
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="col-md-offset-2 col-sm-8">
                                                                <label for="user_password" class="col-sm-3 control-label">
                                                                    <span class="text-danger">*</span> Lozinka:
                                                                </label>
                                                                <div class="col-sm-7">
                                                                    <div class="materail-input-block materail-input-block_primary">
                                                                        <input class="form-control materail-input" type="text" name="user_password" id="user_password" placeholder="Unesite lozinku" autocomplete="off" value="<?php echo $random_password; ?>" required readonly>
                                                                        <span class="materail-input-block__line">
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-2 text-right">
                                                                    <span onclick="copyPasswordCU()" class="btn btn-primary material-btn material-btn_primary"><i class="fa fa-clipboard" aria-hidden="true"></i></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="col-md-offset-2 col-sm-8">
                                                                <label for="user_language" class="col-sm-3 control-label">
                                                                    <span class="text-danger">*</span> Jezik:
                                                                </label>
                                                                <div class="col-sm-9">
                                                                    <div class="">
                                                                        <select class="selectpicker" name="user_language" id="user_language" title="Odaberite jezik korisnika" required>
                                                                            <option value="0">Bosnian/Croatian/Serbian</option>
                                                                            <option value="1">German</option>
                                                                            <option value="2">English</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="col-md-offset-2 col-sm-8">
                                                                <label for="user_gender" class="col-sm-3 control-label">
                                                                    <span class="text-danger">*</span> Spol:
                                                                </label>
                                                                <div class="col-sm-9">
                                                                    <div class="">
                                                                        <select class="selectpicker" name="user_gender" id="user_gender" title="Odaberite spol korisnika" required>
                                                                            <option value="Male">Muško</option>
                                                                            <option value="Female">Žensko</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer material-modal__footer">
                                                            <button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
                                                            <button type="submit" class="btn btn-primary material-btn material-btn_primary" form="addCompanyUserCUForm"><i class="fa fa-check-square-o" aria-hidden="true"></i> Spremi</button>
                                                        </div>
                                                    </form>
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
                        Header script START 
                        -->
                            <script>
                                function copyPasswordCU() {
                                    var user_password = $('#user_password', '#addCompanyUserCUForm');
                                    user_password.select();
                                    navigator.clipboard.writeText(user_password.val());
                                    //alert("Copied the text: " + user_password.val());
                                }
                            </script>
                        <!-- 
                        Header script END 
                    -->
                    <hr>
                    <!-- 
                        Message Start
                        -->
                            <?php 
                                if(isset($_GET['company_users_message'])) {
                                    $enabledMessagesCU = array(1,2,3,4,5,6,7,8);
                                    $company_users_message = $_GET['company_users_message'];
                                    $resultMessCU = '';
                                    if(in_array($company_users_message, $enabledMessagesCU)) {
                                        if($company_users_message == 1){
                                            $resultMessCU = '<div class="alert alert-danger text-center" role="alert"><h4><strong>Greška</strong></h4><br>Akcija nije izvršena!<br>Postoji problem sa slanjem podataka za ažuriranje! Obratite se administratoru sistema!</div>';
                                        }elseif($company_users_message == 2){
                                            $resultMessCU = '<div class="alert alert-danger text-center" role="alert"><h4><strong>Greška</strong></h4><br>Akcije nije izvršena!<br>Akcijom bi se izvršila promjena za više korisnika ili sistem nije pronašao ni jednog korisnika nad kojim je moguće izvršiti ažuriranje! Obratite se administratoru sistema!</div>';
                                        }elseif($company_users_message == 3){
                                            $resultMessCU = '<div class="alert alert-danger text-center" role="alert"><h4><strong>Greška</strong></h4><br>Akcije nije izvršena!<br>Sistem nije pronašao korisnika nad kojim je moguće izvršiti ažuriranje! Obratite se administratoru sistema!</div>';
                                        }elseif($company_users_message == 4){
                                            $resultMessCU = '<div class="alert alert-danger text-center" role="alert"><h4><strong>Greška</strong></h4><br>Akcije nije izvršena!<br>Sistem je pronašao više korisnika nad kojima je izvršio ažuriranje! <strong>Odmah se obratite administratoru sistema!</strong></div>';
                                        }elseif($company_users_message == 5){
                                            $resultMessCU = '<div class="alert alert-success text-center" role="alert"><h4><strong>Uspješno izvršeno</strong></h4><br>Uspješno ste izvršili ažuriranje korisnika!</div>';
                                        }elseif($company_users_message == 6){
                                            $resultMessCU = '<div class="alert alert-danger text-center" role="alert"><h4><strong>Greška</strong></h4><br>Akcija nije izvršena!<br>Postoji problem sa dodavanjem korisnika! Obratite se administratoru sistema!</div>';
                                        }elseif($company_users_message == 7){
                                            $resultMessCU = '<div class="alert alert-danger text-center" role="alert"><h4><strong>Greška</strong></h4><br>Akcija nije izvršena!<br>Iz nekog razloga nije se desilo dodavanje korisnika! Obratite se administratoru sistema!</div>';
                                        }elseif($company_users_message == 8){
                                            $resultMessCU = '<div class="alert alert-success text-center" role="alert"><h4><strong>Uspješno izvršeno</strong></h4><br>Uspješno ste dodali novog korisnika!</div>';
                                        }
                                    } else {
                                        $resultMessCU = '<div class="alert alert-danger text-center" role="alert"><h4><strong>Greška</strong></h4><br>Nepredviđena poruka odgovora! Kontaktirajte administratora sistema!</div>';
                                    }
                                    
                                    ?>
                                        <div class="row">
                                            <div class="col-xs-offset-2 col-xs-8">
                                                <?php 
                                                    echo $resultMessCU;
                                                ?>
                                            </div>
                                        </div>
                                        <hr>
                                    <?php 
                                    unset($enabledMessagesCU);
                                }
                            ?>
                            
                        <!-- 
                        Message End
                    -->
                    <?php
                        $queryUsersForCompany = $db->prepare("
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
                                CONCAT(pu.pu_fname, ' ', pu.pu_lname)   AS userFullName, 
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
                                pu.pu_company_id = :pu_company_id 
                        ");
                        $queryUsersForCompany->execute(array(
                            ':pu_company_id' => $companyId
                        ));
                        if ($queryUsersForCompany->rowCount() > 0) { 
                            $rowUsersForCompany = $queryUsersForCompany->fetchAll(PDO::FETCH_ASSOC);

                            //print("<pre>".print_r($rowUsersForCompany,true)."</pre>");

                            ?> 
                                <!-- 
                                    Content START 
                                    -->
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <script>
                                                    $(document).ready(function(){
                                                        $('#usersForCompany').DataTable({
                                                            responsive: true,
                                                            "order": [[ 0, "asc" ]],
                                                            "bAutoWidth": false,
                                                            "aoColumns": [
                                                                { "width": "5%"},
                                                                { "width": "20%" },
                                                                { "width": "20%" },
                                                                { "width": "15%" },
                                                                { "width": "15%" },
                                                                { "width": "15%" },
                                                                { "width": "10%", "bSortable": false }
                                                            ],
                                                        });
                                                    });
                                                </script>
                                                <table id="usersForCompany" class="display" cellspacing="0" style="width:100%">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">#</th>
                                                            <th class="text-center">Ime i prezime</th>
                                                            <th class="text-center">Email</th>
                                                            <th class="text-center">Jezik</th>
                                                            <th class="text-center">Spol</th>
                                                            <th class="text-center">Status</th>
                                                            <th class="text-center">Akcija</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php 
                                                            $cnt = 0; 
                                                            foreach ($rowUsersForCompany AS $userForCompany) {
                                                                $cnt = $cnt + 1; 
                                                                ?>
                                                                    <tr>
                                                                        <td class="text-center"><?php echo $cnt; ?></td>
                                                                        <td class="text-center"><?php echo $userForCompany['userFullName']; ?></td>
                                                                        <td class="text-center"><?php echo $userForCompany['userEmail']; ?></td>
                                                                        <td class="text-center"><?php echo ( ($userForCompany['userLanguageText'] != 'Undefined') ? '<span class="label label-'.(($userForCompany['userLanguageText'] == 'German') ? 'primary' : ( ($userForCompany['userLanguageText'] == 'English') ? 'info' : 'warning' )).'">'.$userForCompany['userLanguageText'].'</span>' : '<span class="label label-default">Nepoznat</span>' ); ?></td>
                                                                        <td class="text-center"><?php echo ( ($userForCompany['userGenderText'] != 'Undefined') ? ( ($userForCompany['userGenderText'] == 'Male') ? '<i class="fa fa-male fa-2x" title="Male" aria-hidden="true"></i>' : '<i class="fa fa-female fa-2x" title="Female" aria-hidden="true"></i>' ) : '<span class="label label-default">Neodređen</span>' ); ?></td>
                                                                        <td class="text-center"><?php echo ( ($userForCompany['userStatusText'] != 'Undefined') ? ( ($userForCompany['userStatusText'] == 'Active') ? '<span class="label label-success">Aktivan</span>' : '<span class="label label-danger">Neaktivan</span>' ) : '<span class="label label-default">Nepoznat</span>' ); ?></td>
                                                                        <td class="text-center">
                                                                            <div class="btn-group material-btn-group">
                                                                                <button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i><span class="caret material-btn__caret"></span></button>
                                                                                <ul style = "top:33px; left:-60px; min-width:185px !important;" class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
                                                                                    <li>
                                                                                        <a 
                                                                                            class="material-dropdown-menu__link"
                                                                                            onclick="changeUserCU(this)"
                                                                                            data-company_id = "<?php echo $companyId; ?>"
                                                                                            data-user_id ="<?php echo $userForCompany['userId']; ?>"
                                                                                            data-user_full_name ="<?php echo $userForCompany['userFullName']; ?>"
                                                                                        >
                                                                                            <i class="fa fa-pencil-square" aria-hidden="true"></i> 
                                                                                            Uredi korisnika
                                                                                        </a>
                                                                                    </li>
                                                                                    <li>
                                                                                        <a 
                                                                                            class="material-dropdown-menu__link"
                                                                                            onclick="accessUserCU(this)"
                                                                                            data-company_id = "<?php echo $companyId; ?>"
                                                                                            data-user_id ="<?php echo $userForCompany['userId']; ?>"
                                                                                            data-user_full_name ="<?php echo $userForCompany['userFullName']; ?>"
                                                                                        >
                                                                                            <i class="fa fa-info-circle" aria-hidden="true"></i>
                                                                                            Pregled pristupa
                                                                                        </a>
                                                                                    </li>
                                                                                </ul>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                <?php 
                                                                unset($userForCompany);
                                                            }
                                                            unset($rowUsersForCompany);
                                                        ?>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <th class="text-center">#</th>
                                                            <th class="text-center">Ime i prezime</th>
                                                            <th class="text-center">Email</th>
                                                            <th class="text-center">Jezik</th>
                                                            <th class="text-center">Spol</th>
                                                            <th class="text-center">Status</th>
                                                            <th class="text-center">Akcija</th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    <!-- 
                                    Content END 
                                -->
                                <!-- 
                                    Style
                                    -->
                                        <style>
                                            @media (min-width:1202px){#accessUserCU .modal-lg{width:1200px}}
                                            #accessUserCUContent .scrollBarHorizontal::-webkit-scrollbar {
                                                height:5px;
                                                margin-top: 10px;
                                            }
                                            #accessUserCUContent .scrollBarHorizontal::-webkit-scrollbar-thumb {
                                                background: #1D84C0;
                                                border-radius: 5px;
                                            }
                                            #accessUserCUContent .scrollBarHorizontal::-webkit-scrollbar-track {
                                                background-color: #e9ecef;
                                                border-radius: 5px;
                                            }
                                        </style>
                                <!-- 
                                    Modal START 
                                    -->
                                        <div class="modal material-modal material-modal_primary fade text-left" id="changeUserCU">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content material-modal__content">
                                                    <div class="modal-header material-modal__header">
                                                        <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                                        <h4 class="modal-title material-modal__title"><i style = "margin-right: 10px;" class="fa fa-pencil-square" aria-hidden="true"></i><span id="modal-title-text"></span></h4>
                                                    </div> 
                                                    <div class="modal-body material-modal__body">
                                                        <form action="<?php getSiteURL(); ?>jobsoft_settings/backend/do_settings.php?page=edit_user_info" enctype="multipart/form-data" method="post" role="form" class="form-horizontal" id="changeUserCUForm">
                                                            <input type="hidden" id="company_id" name="company_id">
                                                            <input type="hidden" id="user_id" name="user_id">
                                                            <div class="form-group">
                                                                <div class="col-md-offset-2 col-sm-8">
                                                                    <label for="user_first_name" class="col-sm-4 control-label">
                                                                        <span class="text-danger">*</span> Ime:
                                                                    </label>
                                                                    <div class="col-sm-8">
                                                                        <div class="materail-input-block materail-input-block_primary">
                                                                            <input class="form-control materail-input" type="text" name="user_first_name" id="user_first_name" placeholder="Unesite ime" autocomplete="off" required>
                                                                            <span class="materail-input-block__line">
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <div class="col-md-offset-2 col-sm-8">
                                                                    <label for="user_last_name" class="col-sm-4 control-label">
                                                                        <span class="text-danger">*</span> Prezime:
                                                                    </label>
                                                                    <div class="col-sm-8">
                                                                        <div class="materail-input-block materail-input-block_primary">
                                                                            <input class="form-control materail-input" type="text" name="user_last_name" id="user_last_name" placeholder="Unesite prezime" autocomplete="off" required>
                                                                            <span class="materail-input-block__line">
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <div class="col-md-offset-2 col-sm-8">
                                                                    <label for="user_email" class="col-sm-4 control-label">
                                                                        <span class="text-danger">*</span> Email:
                                                                    </label>
                                                                    <div class="col-sm-8">
                                                                        <div class="materail-input-block materail-input-block_primary">
                                                                            <input class="form-control materail-input" type="email" name="user_email" id="user_email" placeholder="Unesite email" autocomplete="off" required>
                                                                            <span class="materail-input-block__line">
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <div class="col-md-offset-2 col-sm-8">
                                                                    <label for="user_language" class="col-sm-4 control-label">
                                                                        <span class="text-danger">*</span> Jezik:
                                                                    </label>
                                                                    <div class="col-sm-8">
                                                                        <div class="">
                                                                            <select class="selectpicker" name="user_language" id="user_language" title="Odaberite jezik korisnika" required>
                                                                                <option value="0">Bosnian/Croatian/Serbian</option>
                                                                                <option value="1">German</option>
                                                                                <option value="2">English</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <div class="col-md-offset-2 col-sm-8">
                                                                    <label for="user_gender" class="col-sm-4 control-label">
                                                                        <span class="text-danger">*</span> Spol:
                                                                    </label>
                                                                    <div class="col-sm-8">
                                                                        <div class="">
                                                                            <select class="selectpicker" name="user_gender" id="user_gender" title="Odaberite spol korisnika" required>
                                                                                <option value="Male">Muško</option>
                                                                                <option value="Female">Žensko</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <div class="col-md-offset-2 col-sm-8">
                                                                    <label for="user_status" class="col-sm-4 control-label">
                                                                        <span class="text-danger">*</span> Status:
                                                                    </label>
                                                                    <div class="col-sm-8">
                                                                        <div class="">
                                                                            <select class="selectpicker" name="user_status" id="user_status" title="Odaberite status korisnika" required>
                                                                                <option value="0">Neaktivan</option>
                                                                                <option value="1">Aktivan</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer material-modal__footer">
                                                                <button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
                                                                <button type="submit" class="btn btn-primary material-btn material-btn_success" form="changeUserCUForm"><i class="fa fa-check-square-o" aria-hidden="true"></i> Spremi</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal material-modal material-modal_primary fade text-left" id="accessUserCU">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content material-modal__content">
                                                    <div class="modal-header material-modal__header">
                                                        <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                                        <h4 class="modal-title material-modal__title"><i style = "margin-right: 10px;" class="fa fa-pencil-square" aria-hidden="true"></i><span id="modal-title-text"></span></h4>
                                                    </div> 
                                                    <div class="modal-body material-modal__body">
                                                        <div class="row" style="margin-bottom: 15px;">
                                                            <div class="col-md-offset-2 col-sm-8 text-center">
                                                                <small>Dobijeni podaci su informativnog karaktera. Za uređivanje podataka o pristupu za određeni nalog, pristupite stranici određenog Naloga u sekciju JobSoft postavke.</small>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-12 text-center" id="accessUserCUContent">
                                                                
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer material-modal__footer">
                                                            <button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
                                                        </div>
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
                                            /*
                                                Modal changeUserCU START 
                                                */
                                                    function changeUserCU(thisRow) {
                                                        var company_id              = $(thisRow).data('company_id'); 
                                                        var user_id                 = $(thisRow).data('user_id');
                                                        var user_full_name          = $(thisRow).data('user_full_name');

                                                        getChangeModalTitleCU(user_full_name);
                                                        getFreshUserData(user_id, company_id);
                                                    };

                                                    $('#changeUserCU').on('hidden.bs.modal', function () {
                                                        resetChangeUserFormData();
                                                    });

                                                    function getChangeModalTitleCU(user_full_name) {
                                                        if (user_full_name != '') {
                                                            $('#modal-title-text', '#changeUserCU').text('Uredi korisnika ' + user_full_name);
                                                        } else {
                                                            $('#modal-title-text', '#changeUserCU').text('');
                                                        }
                                                    };

                                                    function getFreshUserData(user_id, company_id) {
                                                        $.ajax({
                                                            url: 'jobsoft_settings/backend/do_settings.php?page=get_user_info',
                                                            type: 'POST',
                                                            cache: false,
                                                            data:{'user_id': user_id},
                                                            success : function (result){
                                                                var resultDecode = JSON.parse(result);
                                                                if (resultDecode.status === 1) {
                                                                    var userData = resultDecode.data[0];
                                                                    var responseSetUserData = setChangeUserFormData(userData, company_id);
                                                                    if (responseSetUserData == true) {
                                                                        $('#changeUserCU').modal('show');
                                                                    } else {
                                                                        alert('Postoji problem prilikom setovanja podataka o korisniku. Zbog toga nije moguće pokrenuti opciju "Uredi korisnika".'); 
                                                                    }
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

                                                    function resetChangeUserFormData() {
                                                        getChangeModalTitleCU('');
                                                        setChangeUserFormData('','');
                                                    };

                                                    function setChangeUserFormData(userData, company_id) {
                                                        if (userData != '' && company_id != '') {
                                                            $('#user_id', '#changeUserCUForm').val(userData.userId);
                                                            $('#company_id', '#changeUserCUForm').val(company_id);
                                                            $('#user_first_name', '#changeUserCUForm').val(userData.userFirstName);
                                                            $('#user_last_name', '#changeUserCUForm').val(userData.userLastName);
                                                            $('#user_email', '#changeUserCUForm').val(userData.userEmail);
                                                            $('#user_language', '#changeUserCUForm').val(userData.userLanguageValue).selectpicker('refresh');
                                                            $('#user_gender', '#changeUserCUForm').val(userData.userGenderValue).selectpicker('refresh');
                                                            $('#user_status', '#changeUserCUForm').val(userData.userStatusValue).selectpicker('refresh');
                                                        } else {
                                                            $('#changeUserCUForm :input:hidden').removeAttr('value');
                                                            $('#user_first_name', '#changeUserCUForm').val(null);
                                                            $('#user_last_name', '#changeUserCUForm').val(null);
                                                            $('#user_email', '#changeUserCUForm').val(null);
                                                            $('#user_language', '#changeUserCUForm').val(null).selectpicker('refresh');
                                                            $('#user_gender', '#changeUserCUForm').val(null).selectpicker('refresh');
                                                            $('#user_status', '#changeUserCUForm').val(null).selectpicker('refresh');
                                                        }
                                                        return true;
                                                    }
                                                /*
                                                Modal changeUserCU END 
                                            */
                                            
                                            /*
                                                Modal accessUserCU START 
                                                */
                                                    function accessUserCU(thisRow) {
                                                        var company_id              = $(thisRow).data('company_id'); 
                                                        var user_id                 = $(thisRow).data('user_id');
                                                        var user_full_name          = $(thisRow).data('user_full_name');
                                                        
                                                        getAccessModalTitleCU(user_full_name);
                                                        getFreshAccessUserData(user_id);
                                                    };

                                                    $('#accessUserCU').on('hidden.bs.modal', function () {
                                                        resetAccessUserData();
                                                    });

                                                    function getAccessModalTitleCU(user_full_name) {
                                                        if (user_full_name != '') { 
                                                            $('#modal-title-text', '#accessUserCU').text('Pregled pristupa korisnika ' + user_full_name);
                                                        } else {
                                                            $('#modal-title-text', '#accessUserCU').text('');
                                                        }
                                                        
                                                    };

                                                    function getFreshAccessUserData(user_id) {
                                                        $.ajax({
                                                            url: 'jobsoft_settings/backend/do_settings.php?page=get_user_access',
                                                            type: 'POST',
                                                            cache: false,
                                                            data:{'user_id': user_id},
                                                            success : function (result){
                                                                var responseSetAccessUser = setAccessUserData(result);
                                                                if (responseSetAccessUser == true) {
                                                                    $('#accessUserCU').modal('show');
                                                                } else {
                                                                    alert('Postoji problem prilikom dobijanja podataka o pristupima korisniku. Zbog toga nije moguće pokrenuti opciju "Pregled pristupa".');
                                                                }
                                                            },
                                                            error: function (xhr, ajaxOptions, thrownError) {
                                                                alert(xhr.status);
                                                                alert(thrownError);
                                                            }
                                                        });
                                                    };

                                                    function resetAccessUserData() {
                                                        getAccessModalTitleCU('');
                                                        setAccessUserData('');
                                                    }

                                                    function setAccessUserData(userInfo) {
                                                        if (userInfo != '') {
                                                            $('#accessUserCUContent', '#accessUserCU').html(userInfo);
                                                        } else {
                                                            $('#accessUserCUContent', '#accessUserCU').html('');
                                                        }
                                                        return true;
                                                    }
                                                /*
                                                Modal accessUserCU END 
                                            */
                                        </script>
                                    <!-- 
                                    Script END  
                                -->
                            <?php 

                        } else {
                            ?> 
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="alert alert-warning text-center" role="alert">
                                            <h4><strong>Upozorenje</strong></h4>
                                            <br>
                                            Nema aktivnih Jobsoft korisnika za ovu kompaniju!
                                        </div>
                                    </div>
                                </div>
                            <?php 
                        }
                    ?>
                </div>
            </div>
        <?php
    } else {
        ?>
            <div class="row">
                <div class="col-xs-12">
                    <div class="alert alert-danger text-center" role="alert">
						<h4><strong>Greška</strong></h4>
						<br>
						Problem sa skriptom za JobSoft korisnike! Obratite se administratoru sistema!
					</div>
                </div>
            </div>
        <?php 
    }
?>