<?php 
    $orderId = intval($nalog_id);
    if ($orderId != 0) {
        //Provjera da li je Jobsoft aktiviran za nalog
        $check_pp = $db->prepare("SELECT pristup_poslodavcima FROM idk_nalozi WHERE nalog_id = $orderId");
        $check_pp->execute();
        $row_pp = $check_pp->fetch();
        $omogucen_pristup = $row_pp["pristup_poslodavcima"];
        if($omogucen_pristup == 1){

            $tabgs = ( ( isset($_GET['tabgs']) ) ? $_GET['tabgs'] : "userAccessSettings" );
            ?>
                <link href="jobsoft_settings/css/style.css" rel="stylesheet">
                <div class="row">
                    <div class="col-xs-12">
                        <div id="JobSoftSetting" class="panel-group material-tabs-group">
                            <ul class="nav nav-tabs material-tabs material-tabs_primary">
                                <!-- 
                                    U slučaju uređivanja permisija na ovom file-u, postaviti također novododanu permisiju na file: nalozi.php -> $tab=="jobstep_pp_settings"
                                -->
                                <?php 
                                    if( in_array( "1" , $employee_status) OR in_array( "2" , $employee_status) OR in_array( "2" , $employee_supervizor) ){ 
                                        ?>
                                            <li class="<?php if($tabgs=="userAccessSettings"){echo "active";} ?>">
                                                <a href="#userAccessSettings" class="material-tabs__tab-link" data-toggle="tab">Postavke pristupa</a>
                                            </li>
                                        <?php 
                                    }

                                    if( in_array( "1" , $employee_status) OR in_array( "2" , $employee_status) OR in_array( "2" , $employee_supervizor) ){ 
                                        ?>
                                            <li class="<?php if($tabgs=="orderPartnersSettings"){echo "active";} ?>">
                                                <a href="#orderPartnersSettings" class="material-tabs__tab-link" data-toggle="tab">Partneri naloga</a>
                                            </li>
                                        <?php 
                                    }

                                    if(getModulePermission(2)){ 
                                        ?>
                                            <li class="<?php if($tabgs=="reminderSettings"){echo "active";} ?>">
                                                <a href="#reminderSettings" class="material-tabs__tab-link" data-toggle="tab">Postavke remindera</a>
                                            </li>
                                        <?php 
                                    }

                                    if( in_array( "1" , $employee_status) OR in_array( "2" , $employee_status) OR in_array( "2" , $employee_supervizor) ){ 
                                        ?>
                                            <li class="<?php if($tabgs=="castingDatesSettings"){echo "active";} ?>">
                                                <a href="#castingDatesSettings" class="material-tabs__tab-link" data-toggle="tab">Datumi castinga</a>
                                            </li>
                                        <?php 
                                    }

                                    if( in_array( "1" , $employee_status) OR in_array( "2" , $employee_status) OR in_array( "2" , $employee_supervizor) ){ 
                                        ?>
                                            <li class="<?php if($tabgs=="predefinedQuestionsSettings"){echo "active";} ?>">
                                                <a href="#predefinedQuestionsSettings" class="material-tabs__tab-link" data-toggle="tab">Predefinisana pitanja</a>
                                            </li>
                                        <?php 
                                    }
                                    
                                ?>
                            </ul>
                            <div class="tab-content materail-tabs-content">
                                <?php 
                                    if( in_array( "1" , $employee_status) OR in_array( "2" , $employee_status) OR in_array( "2" , $employee_supervizor) ){
                                        ?>
                                            <div class="tab-pane fade <?php if($tabgs=="userAccessSettings"){echo "active in";} ?>" id="userAccessSettings">
                                                <?php 
                                                    include("modules/user_access_settings.php");
                                                ?>
                                            </div>
                                        <?php
                                    }

                                    if( in_array( "1" , $employee_status) OR in_array( "2" , $employee_status) OR in_array( "2" , $employee_supervizor) ){
                                        ?>
                                            <div class="tab-pane fade <?php if($tabgs=="orderPartnersSettings"){echo "active in";} ?>" id="orderPartnersSettings">
                                                <?php 
                                                    include("modules/orderPartners.php");
                                                ?>
                                            </div>
                                        <?php
                                    }

                                    if(getModulePermission(2)){ 
                                        ?>
                                            <div class="tab-pane fade <?php if($tabgs=="reminderSettings"){echo "active in";} ?>" id="reminderSettings">
                                                <?php 
                                                    include("modules/remindersSetup.php");
                                                ?>
                                            </div>
                                        <?php
                                    }

                                    if( in_array( "1" , $employee_status) OR in_array( "2" , $employee_status) OR in_array( "2" , $employee_supervizor) ){
                                        ?>
                                            <div class="tab-pane fade <?php if($tabgs=="castingDatesSettings"){echo "active in";} ?>" id="castingDatesSettings">
                                                <?php 
                                                    include("modules/castingDates.php");
                                                ?>
                                            </div>
                                        <?php
                                    }

                                    if( in_array( "1" , $employee_status) OR in_array( "2" , $employee_status) OR in_array( "2" , $employee_supervizor) ){
                                        ?>
                                            <div class="tab-pane fade <?php if($tabgs=="predefinedQuestionsSettings"){echo "active in";} ?>" id="predefinedQuestionsSettings">
                                                <?php 
                                                    include("modules/predefinedQuestions.php");
                                                ?>
                                            </div>
                                        <?php
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php 
        }else{
            ?>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="alert alert-warning text-center" role="alert">
                            <h4><strong>Upozorenje!</strong></h4>
                            <br>
                            Nije aktiviran pristup JobSoftu za ovaj nalog!
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 text-center">
                        <a
                            data="<?php getSiteURL(); ?>jobsoft_settings/backend/do_settings.php?page=activate_order_pp&order_id=<?php echo $orderId; ?>" 
                            data-toggle="modal" data-target="#activate_pp_gs" 
                            class = "activate_pp btn btn-primary material-btn material-btn_primary"
                        >
                                <i class="fa fa-plus" style = "margin-right: 10px; pointer-events: none;" aria-hidden="true"></i>Aktiviraj
                        </a>
                    </div>
                </div>
                <!-- MODAL ZA AKTIVACIJU JOBSOFT PRISTUPA START -->
                <script>
                    $(".activate_pp").click(function () {
                        var addressValue = $(this).attr("data");
                        document.getElementById("anchor_activate_pp").href = addressValue;
                    });										
                </script>
                <div class="modal material-modal material-modal_primary fade" id="activate_pp_gs">
                    <div class="modal-dialog">
                        <div class="modal-content material-modal__content">
                            <div class="modal-header material-modal__header">
                                <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title material-modal__title">Aktiviraj Jobsoft pristup</h4>
                            </div>
                            <div class="modal-body material-modal__body">
                                <p>Jeste li sigurni da želite aktivirati Jobsoft pristup za ovaj nalog?</p>
                            </div>
                            <div class="modal-footer material-modal__footer">
                                <button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
                                <a id="anchor_activate_pp" href=""><button class="btn btn-primary material-btn material-btn_primary">Aktiviraj</button></a>
                            </div>
                        </div>
                    </div> 
                </div>
                <!-- MODAL ZA AKTIVACIJU JOBSOFT PRISTUPA END -->
            <?php 
        }
    } else {
        ?>
            <div class="row">
                <div class="col-xs-12">
                    <div class="alert alert-danger text-center" role="alert">
						<h4><strong>Greška</strong></h4>
						<br>
						Problem sa skriptom za setup JobSoft-a! Obratite se administratoru sistema!
					</div>
                </div>
            </div>
        <?php 
    }
?>