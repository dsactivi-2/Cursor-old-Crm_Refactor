<!-- PROCES ODLASKA PANEL - START -->
    <div class="row">
        <div class = "row">
            <div class = "col-xs-12 text-center">
                <!-- MODAL CEKA TERMIN START -->
                    <div class="modal material-modal material-modal_primary fade text-left" id="kandidat_unos_termina_m">
                        <div class="modal-dialog ">
                            <div class="modal-content material-modal__content">
                                <div class="modal-header material-modal__header">
                                    <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title material-modal__title">Unos termina za vizu</h4>
                                </div>
                                <div class="modal-body material-modal__body">
                                    <form action="<?php getSiteURL(); ?>do.php?form=add_datum_termina" method="post" role="form" class="form-horizontal">
                                    <?php
                                        if($result["datum_termina"] != null){
                                            echo '<div class="form-group">
                                                    <div class="col-sm-2"></div>
                                                    <div class="col-sm-8 text-center">
                                                        <p class="text-danger">Kandidat vec ima unesen termin. Ako je tacan klikni "Spremi"</p>
                                                    </div>
                                                    <div class="col-sm-2"></div>
                                                </div>';
                                        }
                                    ?>
                                        <div class="form-group">
                                            <label for="kandidat_datum_termina" class="col-sm-4 control-label">Datum termina za vizu</label>
                                            <div class="col-sm-8">
                                                <div class="materail-input-block materail-input-block_success">
                                                    <input type="hidden" name="kandidat_id" value="<?php echo $kandidat_id; ?>">
                                                    <input class="form-control materail-input" type="text" name="kandidat_datum_termina" autocomplete="off" id="kandidat_datum_termina" >
                                                    <span class="materail-input-block__line"></span>
                                                </div>
                                            </div>
                                        </div>	
                                        <script>
                                            $(function() {
                                                initDateSelect();
                                            });
                                            function initDateSelect() {
                                                $("#kandidat_datum_termina").flatpickr({
                                                    dateFormat: "Y-m-d",
                                                    defaultDate: "<?php echo $result["datum_termina"]; ?>"
                                                });
                                            }
                                            $( document ).ready(function() {
                                                let datum_termina = $.trim($("#kandidat_datum_termina").val());
                                                if(datum_termina == ""){
                                                    $(".save_date").prop("disabled", true);
                                                } 
                                                else {
                                                    $(".save_date").prop("disabled", false);
                                                }
                                            })
                                            $("#kandidat_datum_termina").change(function(){
                                                if(!$.trim(this.value).length){
                                                    $(".save_date").prop("disabled", true);
                                                } 
                                                else if($.trim(this.value).length) {
                                                    $(".save_date").prop("disabled", false);
                                                }
                                            });
                                        </script>
                                </div>
                                <div class="modal-footer material-modal__footer">
                                        <button type="submit" class="btn btn-primary material-btn material-btn_success save_date"><i class="fa fa-edit" aria-hidden="true"></i> Spremi</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <!-- MODAL CEKA TERMIN END -->

                <!-- MODAL PROVJERA TERMINA START -->
                    <div class="modal material-modal material-modal_primary fade text-left" id="provjera_termina_m">
                        <div class="modal-dialog ">
                            <div class="modal-content material-modal__content">
                                <div class="modal-header material-modal__header">
                                    <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title material-modal__title">Provjera termina</h4>
                                </div>
                                <div class="modal-body material-modal__body">
                                    <form action="<?php getSiteURL(); ?>do.php?form=kandidat_termin_check" method="post" role="form" class="form-horizontal">
                                        <input type="hidden" name="kandidat_id" value="<?php echo $kandidat_id; ?>">
                                        <input type="hidden" name="nalog_id" value="<?php echo $nalog_id; ?>">
                                        <div class="form-group">
                                            <div class="col-sm-4" style="padding-top:5px; text-align:right;">Da li je kandidat bio na terminu:
                                            </div>
                                            <div class="col-sm-8">
                                                <div class="materail-input-block materail-input-block_success idk_radio_buttons">
                                                    <label class="main-container__column material-radio-group material-radio-group_success" for="provjera_termina_da" style="padding-right: 5px;">
                                                        <input type="radio" name="provjera_termina" id="provjera_termina_da" class="material-radiobox" value="1" />
                                                        <span class="material-radio-group__element material-radio-group__check-radio"></span>
                                                        <span class="material-radio-group__element material-radio-group__caption">Da </span>
                                                    </label>													
                                                    
                                                    <label class="main-container__column material-radio-group material-radio-group_danger" for="provjera_termina_ne">
                                                        <input type="radio" name="provjera_termina" id="provjera_termina_ne" class="material-radiobox" value="0" />
                                                        <span class="material-radio-group__element material-radio-group__check-radio"></span>
                                                        <span class="material-radio-group__element material-radio-group__caption">Ne </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>	
                                        <script>
                                            $(document).ready(function () {
                                                if( $('#provjera_termina_ne').is(':checked') ){
                                                    $('.razlozi_termin').css('display', 'block');
                                                    $(".save_change").prop("disabled", false);
                                                }
                                                else if($('#provjera_termina_da').is(':checked') ){
                                                    $('.razlozi_termin').css('display', 'none');
                                                    $('.novi_termin').css('display', 'none');
                                                    $(".save_change").prop("disabled", false);
                                                }
                                                else if( !$("provjera_termina_da").is(":checked") && !$("#provjera_termina_ne").is(":checked")){
                                                    $(".save_change").prop("disabled", true);
                                                }
                                                $("#kandidat_datum_termina_novi").flatpickr({
                                                    dateFormat: "Y-m-d",
                                                });
                                            });
                                            $('#provjera_termina_ne').click(function(){
                                                $('.razlozi_termin').css('display', 'block');
                                                $(".save_change").prop("disabled", false);
                                                if( !$("#provjera_novog_termina_da").is(":checked") && !$("#provjera_novog_termina_ne").is(":checked")){
                                                    $(".save_change").prop("disabled", true);
                                                }
                                                $("#provjera_novog_termina_da").click(function() {
                                                    $(".novi_termin").css('display', 'block');
                                                    $("#kandidat_datum_termina_novi").change(function(){
                                                        if(!$.trim(this.value).length){
                                                            $(".save_change").prop("disabled", true);
                                                        } 
                                                        else if($.trim(this.value).length) {
                                                            $(".save_change").prop("disabled", false);
                                                        }
                                                    });
                                                })
                                                
                                                $("#provjera_novog_termina_ne").click(function() {
                                                    $(".novi_termin").css('display', 'none');
                                                    $(".save_change").prop("disabled", false);
                                                })
                                                
                                            });
                                            $('#provjera_termina_da').click(function(){
                                                $('.razlozi_termin').css('display', 'none');
                                                $('.novi_termin').css('display', 'none');
                                                $(".save_change").prop("disabled", false);
                                            });
                                        </script>	
                                        <div class="form-group razlozi_termin" style="display: none;">
                                            <div class="col-sm-4" style="padding-top:5px; text-align:right;">
                                                Da li kandidat ima novi termin:
                                            </div>
                                            <div class="col-sm-8">
                                                <div class="materail-input-block materail-input-block_success idk_radio_buttons">
                                                    <label class="main-container__column material-radio-group material-radio-group_success" for="provjera_novog_termina_da" style="padding-right: 5px;">
                                                        <input type="radio" name="provjera_novog_termina" id="provjera_novog_termina_da" class="material-radiobox" value="1" />
                                                        <span class="material-radio-group__element material-radio-group__check-radio"></span>
                                                        <span class="material-radio-group__element material-radio-group__caption">Da </span>
                                                    </label>													
                                                    
                                                    <label class="main-container__column material-radio-group material-radio-group_danger" for="provjera_novog_termina_ne">
                                                        <input type="radio" name="provjera_novog_termina" id="provjera_novog_termina_ne" class="material-radiobox" value="0" />
                                                        <span class="material-radio-group__element material-radio-group__check-radio"></span>
                                                        <span class="material-radio-group__element material-radio-group__caption">Ne </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>	
                                        <div class="form-group novi_termin" style="display: none;">
                                            <label for="kandidat_datum_termina_novi" class="col-sm-4 control-label">Datum termina za vizu</label>
                                            <div class="col-sm-8">
                                                <div class="materail-input-block materail-input-block_success">
                                                    <input class="form-control materail-input" type="text" name="kandidat_datum_termina_novi" autocomplete="off" id="kandidat_datum_termina_novi" >
                                                    <span class="materail-input-block__line"></span>
                                                </div>
                                            </div>
                                        </div>	
                                </div>
                                <div class="modal-footer material-modal__footer">
                                            <button type="submit" class="btn btn-primary material-btn material-btn_success save_change"><i class="fa fa-edit" aria-hidden="true"></i> Spremi</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <!-- MODAL PROVJERA TERMINA END -->

                <!-- MODAL ISHOD TERMINA START -->
                    <div class="modal material-modal material-modal_success fade text-left" id="ishod_termina_m">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content material-modal__content">
                                <div class="modal-header material-modal__header">
                                    <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title material-modal__title">Ishod termina za vizu</h4>
                                </div>
                                <form action="<?php getSiteURL(); ?>do.php?form=add_kandidat_visa" enctype="multipart/form-data" method="post" role="form" class="form-horizontal" id = "form_add_kandidat_visa">
                                    <div class="modal-body material-modal__body">
                                        <style>
                                            .cardDocumentTypes{
                                                border: 1px solid #cccccc;border-radius: 15px; background-color: #cccccc30; padding: 0px 50px;
                                            }
                                            .marginCardDocumentTypes{
                                                margin-bottom: 25px;
                                            }
                                            .textAreaDocument{
                                                border-radius: 20px;
                                                padding: 20px;
                                            }
                                        </style>
                                        <input type="hidden" name="kandidat_id" value="<?php echo $kandidat_id; ?>">
                                        <input type="hidden" name="nalog_id" value="<?php echo $nalog_id; ?>">
                                        <div class="form-group">
                                            <div class="col-md-offset-1 col-sm-10 text-center">
                                                <h4>Odaberite ishod termina</h4>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <div class="col-sm-3 text-center">
                                                    <label class="main-container__column material-radio-group material-radio-group_success" for="ishod_termina_dobio">
                                                        <input type="radio" name="ishod_termina" id="ishod_termina_dobio" class="material-radiobox" value="1"/>
                                                        <span class="material-radio-group__element material-radio-group__check-radio"></span>
                                                        <span class="material-radio-group__element material-radio-group__caption">Dobio</span>
                                                    </label>
                                                </div>	
                                                <div class="col-sm-3 text-center">											
                                                    <label class="main-container__column material-radio-group material-radio-group_warning" for="ishod_termina_dopuna">
                                                        <input type="radio" name="ishod_termina" id="ishod_termina_dopuna" class="material-radiobox" value="2"/>
                                                        <span class="material-radio-group__element material-radio-group__check-radio"></span>
                                                        <span class="material-radio-group__element material-radio-group__caption">Dopuna</span>
                                                    </label>
                                                </div>	
                                                <div class="col-sm-3 text-center">
                                                    <label class="main-container__column material-radio-group material-radio-group_danger" for="ishod_termina_odbijen">
                                                        <input type="radio" name="ishod_termina" id="ishod_termina_odbijen" class="material-radiobox" value="3"/>
                                                        <span class="material-radio-group__element material-radio-group__check-radio"></span>
                                                        <span class="material-radio-group__element material-radio-group__caption">Odbijenica</span>
                                                    </label>
                                                </div>	
                                                <div class="col-sm-3 text-center">
                                                    <label class="main-container__column material-radio-group material-radio-group_info" for="ishod_termina_nemaodgovora">
                                                        <input type="radio" name="ishod_termina" id="ishod_termina_nemaodgovora" class="material-radiobox" value="4"/>
                                                        <span class="material-radio-group__element material-radio-group__check-radio"></span>
                                                        <span class="material-radio-group__element material-radio-group__caption">Nije dobio odgovor</span>
                                                    </label>
                                                </div>
                                            </div>
                                            
                                        </div>	
                                        <div class="dobio_termin_group">	
                                            <div class="form-group">
                                                <div class="col-md-offset-1 col-md-10">
                                                    <label for="kandidat_datum_viza_start" class="col-sm-4 control-label text-right"><span class="text-danger">*</span> Od kad vrijedi viza:</label>
                                                    <div class="col-sm-8">
                                                        <div class="materail-input-block materail-input-block_success">
                                                            <input class="form-control materail-input" type="text" name="kandidat_datum_viza_start" autocomplete="off" id="kandidat_datum_viza_start" placeholder="Unesite datum važenja vize">
                                                            <span class="materail-input-block__line"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>	
                                            <div class="form-group">
                                                <div class="col-md-offset-1 col-md-10">
                                                    <label for="kandidat_datum_viza_end" class="col-sm-4 control-label text-right"><span class="text-danger">*</span> Do kad vrijedi viza:</label>
                                                    <div class="col-sm-8">
                                                        <div class="materail-input-block materail-input-block_success">
                                                            <input class="form-control materail-input" type="text" name="kandidat_datum_viza_end" autocomplete="off" id="kandidat_datum_viza_end" placeholder="Unesite datum isteka vize">
                                                            <span class="materail-input-block__line"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="dobio_dopunu_group">
                                            <div class="form-group">
                                                <div class="col-md-offset-1 col-md-10">
                                                    <div class="alert alert-danger text-center" role="alert">
                                                        U slučaju da je potrebno evidentirati da su vas i <strong>Poslodavac</strong> i <strong>Kandidat</strong> obavijestili o dopuni,
                                                        ovdje izvršite unos primjerice za <strong>Poslodavaca</strong>. Nakon unosa, biti će vam omogućena opcija unosa za <strong>Kandidata</strong>. 
                                                        <br><br>
                                                        <small>Ovakav način unosa predviđen je zbog razlike u vremenu prijema informacije o dopuni od strane <strong>Poslodavca</strong> i <strong>Kandidata</strong>.</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-md-offset-1 col-md-10">
                                                    <label for="dopunu_dobio" class="col-sm-4 control-label text-right"><span class="text-danger">*</span> Obavijestio o dopuni:</label>
                                                    <div class="col-sm-8">
                                                        <div class="">
                                                            <select class="selectpicker" id="dopunu_dobio" name="dopunu_dobio" title = "Odaberite ko vas je obavijestio za dopunu">
                                                                <option value="1">Kandidat</option>
                                                                <option value="2">Poslodavac</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-md-offset-1 col-md-10">
                                                    <label for="datum_prijema_dopune" class="col-sm-4 control-label text-right"><span class="text-danger">*</span> Datum prijema dopune:</label>
                                                    <div class="col-sm-8">
                                                        <div class="materail-input-block materail-input-block_success">
                                                            <input class="form-control materail-input" type="text" name="datum_prijema_dopune" autocomplete="off" id="datum_prijema_dopune" placeholder="Unesite datum prijema dopune">
                                                            <span class="materail-input-block__line"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>	
                                            <div class="form-group">
                                                <div class="col-md-offset-1 col-md-10">
                                                    <label for="nas_datum_prijema_dopune" class="col-sm-4 control-label text-right"><span class="text-danger">*</span> Datum našeg prijema:</label>
                                                    <div class="col-sm-8">
                                                        <div class="materail-input-block materail-input-block_success">
                                                            <input class="form-control materail-input" type="text" name="nas_datum_prijema_dopune" autocomplete="off" id="nas_datum_prijema_dopune" placeholder="Unesite datum našeg prijema informacije o dopuni">
                                                            <span class="materail-input-block__line"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-md-offset-1 col-md-10">
                                                    <label for="krajnji_datum_dopune" class="col-sm-4 control-label text-right"><span class="text-danger">*</span> Krajnji datum dopune:</label>
                                                    <div class="col-sm-8">
                                                        <div class="materail-input-block materail-input-block_success">
                                                            <input class="form-control materail-input" type="text" name="krajnji_datum_dopune" autocomplete="off" id="krajnji_datum_dopune" placeholder="Unesite krajnji datum za dostavljanje dopune">
                                                            <span class="materail-input-block__line"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-md-offset-1 col-md-10">
                                                    <hr>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-md-offset-1 col-md-10">
                                                    <label for="tipovi_dokumenata_dopune" class="col-sm-4 control-label text-right"><span class="text-danger">*</span> Dokumenti dopune:</label>
                                                    <div class="col-sm-8">
                                                        <div class="">
                                                            <select class="selectpicker" id="tipovi_dokumenata_dopune" name="tipovi_dokumenata_dopune[]" title = "Odaberite tipove dokumenata za dopunu" multiple>
                                                                <?php 
                                                                    $queryDocumentTypes = $db->prepare("
                                                                        SELECT 
                                                                            dt.doc_type_id, dt.doc_type_name, dt.doc_type_name_de
                                                                        FROM
                                                                            idk_pp_document_types dt
                                                                    ");
                                                                    $queryDocumentTypes->execute();
                                                                    while($rowDocumentTypes = $queryDocumentTypes->fetch()){
                                                                        echo '<option value="'.intval($rowDocumentTypes["doc_type_id"]).'" data-subtext="'.$rowDocumentTypes["doc_type_name_de"].'">'.$rowDocumentTypes["doc_type_name"].'</option>';
                                                                    }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-md-offset-1 col-md-10">
                                                    <hr>
                                                </div>
                                            </div>
                                            <script>
                                                Array.prototype.diff = function(a){
                                                    return this.filter(function(i) {return a.indexOf(i) < 0;});
                                                };
                                                function makeInput(id_doc){
                                                    var typeName = $("#tipovi_dokumenata_dopune > option[value=" + id_doc + "]").text();
                                                    return `
                                                        <div id="detalji_dokumenta_${id_doc}" data-id_doc="${id_doc}" class="">
                                                            <div class="col-md-offset-1 col-sm-10 text-center cardDocumentTypes marginCardDocumentTypes">
                                                                <div class="form-group">
                                                                    <div class="col-sm-12 text-center">
                                                                        <h4><strong>${typeName}</strong></h4>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="done_by_doc${id_doc}" class="col-sm-4 control-label text-right"><span class="text-danger">*</span> Zadužen:</label>
                                                                    <div class="col-sm-8">
                                                                        <div class="">
                                                                            <select class="selectpicker" id="done_by_doc${id_doc}" name="done_by_doc${id_doc}" title = "Postavite zaduženje za ovaj dokument" required>
                                                                                <option value="1">Poslodavac</option>
                                                                                <option value="2">Kandidat</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="comment_doc_${id_doc}" class="col-sm-4 control-label text-right">
                                                                        <span class="text-danger">*</span> Komentar:
                                                                    </label>
                                                                    <div class="col-sm-8">
                                                                        <div class="form-group materail-input-block materail-input-block_success">
                                                                            <textarea class="form-control materail-input material-textarea textAreaDocument" name="comment_doc_${id_doc}" id="comment_doc_${id_doc}" placeholder="Unesite opis i razlog dopune za ovaj dokument" rows="4" required></textarea>
                                                                            <!--<span class="materail-input-block__line"></span>-->
                                                                            <span class="text-danger"><small>U slučaju zaduženja poslodavca za dokument, komentar napisati na njemačkom jeziku!</small></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    `;
                                                    
                                                };
                                                function removeChildrenInGroup(arrayChildren){
                                                    //console.log("--- Remove START ---");
                                                    if(arrayChildren.length != 0){
                                                        const arrayChildrenValue = [];
                                                        $('.detalji_dokumenta_group').children('div').each(function () {
                                                            arrayChildrenValue.push($(this).data("id_doc"));
                                                        });
                                                        //console.log("Postoji: "+arrayChildrenValue);
                                                        let differentArrayChildren = arrayChildrenValue.diff(arrayChildren);
                                                        //console.log("Razlika: "+differentArrayChildren);
                                                        differentArrayChildren.forEach(element => {
                                                            $("#detalji_dokumenta_"+element).remove();
                                                            //console.log(" -> Obrisan: "+element);
                                                        });
                                                    }else{
                                                        $(".detalji_dokumenta_group").html("");
                                                        //console.log("Prazan div!");
                                                    }
                                                    //console.log("--- Remove END ---");
                                                }
                                                function addChildrenInGroup(arrayChildren){
                                                    //console.log("--- ADD START ---");
                                                    //console.log("Proslijeđen: "+arrayChildren);
                                                    const arrayChildrenValue = [];
                                                    $('.detalji_dokumenta_group').children('div').each(function () {
                                                        arrayChildrenValue.push($(this).data("id_doc"));
                                                    });
                                                    //console.log("Postoji: "+arrayChildrenValue);
                                                    arrayChildren.forEach(element => {
                                                        if (jQuery.inArray(element, arrayChildrenValue) == -1){
                                                            $(".detalji_dokumenta_group").append(makeInput(element));
                                                            $("#done_by_doc"+element).selectpicker("refresh");
                                                            //console.log(" -> Dodan koji ne postoji: "+element);
                                                        }
                                                    });
                                                    //console.log("--- ADD END ---");
                                                };
                                                $("#tipovi_dokumenata_dopune").change(function(){
                                                    //console.clear();
                                                    if($("#tipovi_dokumenata_dopune").val() != null){
                                                        var typeDocuments = $("#tipovi_dokumenata_dopune").val().map((e)=>parseInt(e));
                                                        removeChildrenInGroup(typeDocuments);
                                                        addChildrenInGroup(typeDocuments);
                                                    }else{
                                                        $(".detalji_dokumenta_group").html("");
                                                    }
                                                });
                                            </script>
                                            <div class="detalji_dokumenta_group"> 
                                                
                                            </div>
                                        </div>
                                        <div class="dobio_odbijenicu_group">
                                            <div class="form-group">
                                                <div class="col-md-offset-1 col-md-10">
                                                    <label for="uslov_za_odbijenicu" class="col-sm-4 control-label text-right"><span class="text-danger">*</span> Uslov za žalbu:</label>
                                                    <div class="col-sm-8">
                                                        <div class="">
                                                            <select class="selectpicker" id="uslov_za_odbijenicu" name="uslov_za_odbijenicu" title = "Da li ima uslova za žalbu">
                                                                <option value="1">NE</option>
                                                                <option value="2">DA</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-md-offset-1 col-md-10">
                                                    <label for="odbijenicu_dobio" class="col-sm-4 control-label text-right"><span class="text-danger">*</span> Obavijestio o odbijenici:</label>
                                                    <div class="col-sm-8">
                                                        <div class="">
                                                            <select class="selectpicker" id="odbijenicu_dobio" name="odbijenicu_dobio" title = "Odaberite ko vas je obavijestio za odbijenicu">
                                                                <option value="1">Kandidat</option>
                                                                <option value="2">Poslodavac</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-md-offset-1 col-md-10">
                                                    <label for="datum_prijema_odbijenice" class="col-sm-4 control-label text-right"><span class="text-danger">*</span> Datum prijema odbijenice:</label>
                                                    <div class="col-sm-8">
                                                        <div class="materail-input-block materail-input-block_success">
                                                            <input class="form-control materail-input" type="text" name="datum_prijema_odbijenice" autocomplete="off" id="datum_prijema_odbijenice" placeholder="Unesite datum prijema odbijenice">
                                                            <span class="materail-input-block__line"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>	
                                            <div class="form-group">
                                                <div class="col-md-offset-1 col-md-10">
                                                    <label for="nas_datum_prijema_odbijenice" class="col-sm-4 control-label text-right"><span class="text-danger">*</span> Datum našeg prijema:</label>
                                                    <div class="col-sm-8">
                                                        <div class="materail-input-block materail-input-block_success">
                                                            <input class="form-control materail-input" type="text" name="nas_datum_prijema_odbijenice" autocomplete="off" id="nas_datum_prijema_odbijenice" placeholder="Unesite datum našeg prijema informacije o odbijenici">
                                                            <span class="materail-input-block__line"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="krajnji_datum_odbijenice_group">
                                                <div class="form-group">
                                                    <div class="col-md-offset-1 col-md-10">
                                                        <label for="krajnji_datum_odbijenice" class="col-sm-4 control-label text-right"><span class="text-danger">*</span> Krajnji datum odbijenice:</label>
                                                        <div class="col-sm-8">
                                                            <div class="materail-input-block materail-input-block_success">
                                                                <input class="form-control materail-input" type="text" name="krajnji_datum_odbijenice" autocomplete="off" id="krajnji_datum_odbijenice" placeholder="Unesite krajnji datum za dostavljanje odbijenice">
                                                                <span class="materail-input-block__line"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="dokumenti_odbijenice_group">
                                                <div class="form-group">
                                                    <div class="col-md-offset-1 col-md-10">
                                                        <label for="tipovi_dokumenata_odbijenice" class="col-sm-4 control-label text-right"><span class="text-danger">*</span> Dokumenti dopune:</label>
                                                        <div class="col-sm-8">
                                                            <div class="">
                                                                <select class="selectpicker" id="tipovi_dokumenata_odbijenice" name="tipovi_dokumenata_odbijenice[]" title = "Odaberite tipove dokumenata za odbijenicu" multiple>
                                                                    <?php 
                                                                        $queryDocumentTypesOdb = $db->prepare("
                                                                            SELECT 
                                                                                dt.doc_type_id, dt.doc_type_name, dt.doc_type_name_de
                                                                            FROM
                                                                                idk_pp_document_types dt
                                                                        ");
                                                                        $queryDocumentTypesOdb->execute();
                                                                        while($rowDocumentTypesOdb = $queryDocumentTypesOdb->fetch()){
                                                                            echo '<option value="'.intval($rowDocumentTypesOdb["doc_type_id"]).'" data-subtext="'.$rowDocumentTypesOdb["doc_type_name_de"].'">'.$rowDocumentTypesOdb["doc_type_name"].'</option>';
                                                                        }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> 
                                                <script>
                                                    Array.prototype.diffOd = function(a){
                                                        return this.filter(function(i) {return a.indexOf(i) < 0;});
                                                    };
                                                    function makeInputOd(id_doc){
                                                        var typeName = $("#tipovi_dokumenata_odbijenice > option[value=" + id_doc + "]").text();
                                                        return `
                                                            <div id="detalji_dokumenta_odbijenice_${id_doc}" data-id_doc="${id_doc}" class="">
                                                                <div class="col-md-offset-1 col-sm-10 text-center cardDocumentTypes marginCardDocumentTypes">
                                                                    <div class="form-group">
                                                                        <div class="col-sm-12 text-center">
                                                                            <h4><strong>${typeName}</strong></h4>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="done_by_odbijenica_doc${id_doc}" class="col-sm-4 control-label text-right"><span class="text-danger">*</span> Zadužen:</label>
                                                                        <div class="col-sm-8">
                                                                            <div class="">
                                                                                <select class="selectpicker" id="done_by_odbijenica_doc${id_doc}" name="done_by_odbijenica_doc${id_doc}" title = "Postavite zaduženje za ovaj dokument" required>
                                                                                    <option value="1">Poslodavac</option>
                                                                                    <option value="2">Kandidat</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="comment_doc_odbijenica_${id_doc}" class="col-sm-4 control-label text-right">
                                                                            <span class="text-danger">*</span> Komentar:
                                                                        </label>
                                                                        <div class="col-sm-8">
                                                                            <div class="form-group materail-input-block materail-input-block_success">
                                                                                <textarea class="form-control materail-input material-textarea textAreaDocument" name="comment_doc_odbijenica_${id_doc}" id="comment_doc_odbijenica_${id_doc}" placeholder="Unesite opis i razlog odbijenice za ovaj dokument" rows="4" required></textarea>
                                                                                <!--<span class="materail-input-block__line"></span>-->
                                                                                <span class="text-danger"><small>U slučaju zaduženja poslodavca za dokument, komentar napisati na njemačkom jeziku!</small></span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        `;
                                                    };
                                                    function removeChildrenInGroupOd(arrayChildrenOd){
                                                        //console.log("--- Remove START ---");
                                                        if(arrayChildrenOd.length != 0){
                                                            const arrayChildrenOdValue = [];
                                                            $('.detalji_dokumenta_odbijenice_group').children('div').each(function () {
                                                                arrayChildrenOdValue.push($(this).data("id_doc"));
                                                            });
                                                            //console.log("Postoji: "+arrayChildrenOdValue);
                                                            let differentArrayChildrenOd = arrayChildrenOdValue.diffOd(arrayChildrenOd);
                                                            //console.log("Razlika: "+differentArrayChildrenOd);
                                                            differentArrayChildrenOd.forEach(elementOd => {
                                                                $("#detalji_dokumenta_odbijenice_"+elementOd).remove();
                                                                //console.log(" -> Obrisan: "+elementOd);
                                                            });
                                                        }else{
                                                            $(".detalji_dokumenta_odbijenice_group").html("");
                                                            //console.log("Prazan div!");
                                                        }
                                                        //console.log("--- Remove END ---");
                                                    }
                                                    function addChildrenInGroupOd(arrayChildrenOd){
                                                        //console.log("--- ADD START ---");
                                                        //console.log("Proslijeđen: "+arrayChildrenOd);
                                                        const arrayChildrenOdValue = [];
                                                        $('.detalji_dokumenta_odbijenice_group').children('div').each(function () {
                                                            arrayChildrenOdValue.push($(this).data("id_doc"));
                                                        });
                                                        //console.log("Postoji: "+arrayChildrenOdValue);
                                                        arrayChildrenOd.forEach(elementOd => {
                                                            if (jQuery.inArray(elementOd, arrayChildrenOdValue) == -1){
                                                                $(".detalji_dokumenta_odbijenice_group").append(makeInputOd(elementOd));
                                                                $("#done_by_odbijenica_doc"+elementOd).selectpicker("refresh");
                                                                //console.log(" -> Dodan koji ne postoji: "+elementOd);
                                                            }
                                                        });
                                                        //console.log("--- ADD END ---");
                                                    };
                                                    $("#tipovi_dokumenata_odbijenice").change(function(){
                                                        //console.clear();
                                                        if($("#tipovi_dokumenata_odbijenice").val() != null){
                                                            var typeDocumentsOd = $("#tipovi_dokumenata_odbijenice").val().map((e)=>parseInt(e));
                                                            removeChildrenInGroupOd(typeDocumentsOd);
                                                            addChildrenInGroupOd(typeDocumentsOd);
                                                        }else{
                                                            $(".detalji_dokumenta_odbijenice_group").html("");
                                                        }
                                                    });
                                                </script>
                                                <div class="detalji_dokumenta_odbijenice_group"> 
                                                    
                                                </div>
                                            </div>
                                        </div>
                                        <div class="nije_dobio_odgovor_group">
                                            <div class="form-group">
                                                <div class="col-md-offset-1 col-md-10">
                                                    <label for="biljeska_nije_dobio" class="col-sm-4 control-label text-right"><span class="text-danger">*</span> Unesite bilješku:</label>
                                                    <div class="col-sm-8">
                                                        <div class="materail-input-block materail-input-block_success">
                                                            <textarea class="form-control materail-input material-textarea" name="biljeska_nije_dobio" id="biljeska_nije_dobio"></textarea>
						                                    <span class="materail-input-block__line"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <script>
                                            //***************************************************
                                            //	FUNKCIJE START
                                            //*************************************************** 
                                            function divShowHideIshodTermina(vr1,vr2,vr3,vr4){
                                                var vrIT1 = Boolean(vr1);
                                                var vrIT2 = Boolean(vr2);
                                                var vrIT3 = Boolean(vr3);
                                                var vrIT4 = Boolean(vr4);
                                                if(vrIT1 == true){
                                                    $('.dobio_termin_group').show();
                                                    requiredDobioTermin(true);
                                                }else{
                                                    $('.dobio_termin_group').hide();
                                                    requiredDobioTermin(false);
                                                }
                                                if(vrIT2 == true){
                                                    $('.dobio_dopunu_group').show();
                                                    requiredDopunaTermin(true);
                                                }else{
                                                    $('.dobio_dopunu_group').hide();
                                                    requiredDopunaTermin(false);
                                                }
                                                if(vrIT3 == true){
                                                    $('.dobio_odbijenicu_group').show();
                                                    //requiredDobioTermin(true);
                                                    requiredOdbijenTermin(true);
                                                }else{
                                                    $('.dobio_odbijenicu_group').hide();
                                                    //requiredDobioTermin(false);
                                                    requiredOdbijenTermin(false);
                                                }
                                                if(vrIT4 == true){
                                                    $('.nije_dobio_odgovor_group').show();
                                                    requiredBiljeskaNijeDobio(true);
                                                }else{
                                                    $('.nije_dobio_odgovor_group').hide();
                                                    requiredBiljeskaNijeDobio(false);
                                                }
                                            };
                                            function requiredDobioTermin(vr){
                                                var vrDT = Boolean(vr);
                                                $( "#kandidat_datum_viza_start" ).prop( "required", vrDT ).val(null);
                                                $( "#kandidat_datum_viza_end" ).prop( "required", vrDT ).val(null);
                                            };
                                            function requiredDopunaTermin(vr){
                                                var vrDTE = Boolean(vr);
                                                $("#dopunu_dobio").prop("required", vrDTE).val(null).selectpicker("refresh");
                                                $( "#datum_prijema_dopune" ).prop( "required", vrDTE ).val(null);
                                                $( "#nas_datum_prijema_dopune" ).prop( "required", vrDTE ).val(null);
                                                $( "#krajnji_datum_dopune" ).prop( "required", vrDTE ).val(null);
                                                $("#tipovi_dokumenata_dopune").prop("required", vrDTE).val(null).selectpicker("refresh");
                                                $(".detalji_dokumenta_group").html("");
                                            };
                                            function requiredOdbijenTermin(vr){
                                                var vrOT = Boolean(vr);
                                                $("#uslov_za_odbijenicu").prop("required", vrOT).val(null).selectpicker("refresh");
                                                $("#odbijenicu_dobio").prop("required", vrOT).val(null).selectpicker("refresh");
                                                $("#datum_prijema_odbijenice").prop( "required", vrOT ).val(null);
                                                $("#nas_datum_prijema_odbijenice").prop( "required", vrOT ).val(null);
                                            };
                                            function requiredBiljeskaNijeDobio(vr){
                                                var vrOT = Boolean(vr);
                                                $("#biljeska_nije_dobio").prop( "required", vrOT ).val(null);
                                            };
                                            function requiredDokumentiOdbijenice(vr){
                                                var vrDO = Boolean(vr);
                                                $("#tipovi_dokumenata_odbijenice").prop("required", vrDO).val(null).selectpicker("refresh");
                                                $("#krajnji_datum_odbijenice").prop( "required", vrDO ).val(null);
                                            };
                                            function divShowHideDokumentiOdbijenice(vr){
                                                var vrDo = Boolean(vr);
                                                if(vrDo == true){
                                                    $(".krajnji_datum_odbijenice_group").show();
                                                    $(".dokumenti_odbijenice_group").show();
                                                    requiredDokumentiOdbijenice(true);
                                                    $(".detalji_dokumenta_odbijenice_group").html("");
                                                }else{
                                                    $(".krajnji_datum_odbijenice_group").hide();
                                                    $(".dokumenti_odbijenice_group").hide();
                                                    requiredDokumentiOdbijenice(false);
                                                    $(".detalji_dokumenta_odbijenice_group").html("");
                                                }
                                            };
                                            //***************************************************
                                            //	FUNKCIJE END
                                            //***************************************************

                                            //***************************************************
                                            //	Funkcionalnosti forme START
                                            //***************************************************

                                            $(document).ready(function () {
                                                divShowHideIshodTermina(false,false,false,false);
                                                divShowHideDokumentiOdbijenice(false);
                                                $("#ishod_termina_odbijen").prop( "required", true);
                                                $("#kandidat_datum_viza_start").flatpickr({
                                                    dateFormat: "Y-m-d",
                                                    disableMobile: "true",
                                                    allowInput: "true"
                                                });
                                                $("#kandidat_datum_viza_end").flatpickr({
                                                    dateFormat: "Y-m-d",
                                                    disableMobile: "true",
                                                    allowInput: "true"
                                                });
                                                $( "#datum_prijema_dopune" ).flatpickr({
                                                    dateFormat: "Y-m-d H:i",
                                                    disableMobile: "true",
                                                    allowInput: "true",
                                                    enableTime: true,
                                                    time_24hr: true
                                                });
                                                $( "#nas_datum_prijema_dopune" ).flatpickr({
                                                    dateFormat: "Y-m-d H:i",
                                                    disableMobile: "true",
                                                    allowInput: "true",
                                                    enableTime: true,
                                                    time_24hr: true
                                                });
                                                $( "#krajnji_datum_dopune" ).flatpickr({
                                                    dateFormat: "Y-m-d",
                                                    disableMobile: "true",
                                                    allowInput: "true"
                                                });
                                                $( "#datum_prijema_odbijenice" ).flatpickr({
                                                    dateFormat: "Y-m-d H:i",
                                                    disableMobile: "true",
                                                    allowInput: "true",
                                                    enableTime: true,
                                                    time_24hr: true
                                                });
                                                $( "#nas_datum_prijema_odbijenice" ).flatpickr({
                                                    dateFormat: "Y-m-d H:i",
                                                    disableMobile: "true",
                                                    allowInput: "true",
                                                    enableTime: true,
                                                    time_24hr: true
                                                });
                                                $( "#krajnji_datum_odbijenice" ).flatpickr({
                                                    dateFormat: "Y-m-d",
                                                    disableMobile: "true",
                                                    allowInput: "true"
                                                });
                                            });
                                            $('#ishod_termina_odbijen').click(function(){
                                                divShowHideIshodTermina(false,false,true,false);
                                                divShowHideDokumentiOdbijenice(false);
                                            });
                                            $('#ishod_termina_dopuna').click(function(){
                                                divShowHideIshodTermina(false,true,false,false);
                                                divShowHideDokumentiOdbijenice(false);
                                            });
                                            $('#ishod_termina_dobio').click(function(){
                                                divShowHideIshodTermina(true,false,false,false);
                                                divShowHideDokumentiOdbijenice(false);
                                            });
                                            $('#ishod_termina_nemaodgovora').click(function(){
                                                divShowHideIshodTermina(false,false,false,true);
                                                divShowHideDokumentiOdbijenice(false);
                                            });
                                            $('#uslov_za_odbijenicu').change(function(){
                                                var uslovZaOdbijenicu = parseInt($("#uslov_za_odbijenicu").val());
                                                if(uslovZaOdbijenicu == 2){
                                                    divShowHideDokumentiOdbijenice(true);
                                                }else{
                                                    divShowHideDokumentiOdbijenice(false);
                                                }
                                            });

                                            //***************************************************
                                            //	Funkcionalnosti forme END
                                            //***************************************************
                                        </script>
                                        
                                        <div class="form-group" style = "margin-top: 15px;">
                                            <div class="col-md-offset-1 col-md-10 text-center">
                                                <small>Sva polja označena sa <span class="text-danger">*</span> su obavezna!</small>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="modal-footer material-modal__footer">
                                        <button type="submit" class="btn btn-primary material-btn material-btn_success" form="form_add_kandidat_visa"><i class="fa fa-edit" aria-hidden="true"></i> Spremi</button>
                                    </div>
                                    
                                </form>
                            </div>
                        </div>
                    </div>
                <!-- MODAL ISHOD TERMINA END -->

               
                <!-- STATUSI EXPANDERI START -->
                    <div style="text-align: left; margin-top: 10px;">
                        <?php 
                        //uzimamo sve projekte kandidata na trenutom nalogu
                        $projekti_kandidata_query=$db->prepare(" SELECT project_id FROM idk_projects WHERE project_status != 0 AND project_nalogid = :project_nalogid");											
                        $projekti_kandidata_query->execute(array(
                            ':project_nalogid' => $nalog_id
                        ));
                        
                        $projekti_kandidata=array();
                        while($lista_projekta = $projekti_kandidata_query->fetch()){
                            array_push($projekti_kandidata, $lista_projekta['project_id']);
                        }
                        /* PRIKUPLJANJE DOKUMENTACIJE START */
                            if($status_prijave_id>=12 or $status_prijave_id==4 or $status_prijave_id==10){
                                
                                $datum_prikupljanje_doc_query= $db->prepare("
                                                                SELECT lsp_datetime
                                                                FROM idk_log_statusi_prijave
                                                                WHERE lsp_kandidat_id = :lsp_kandidat_id AND lsp_projekt_id in (".implode(",",$projekti_kandidata).") AND lsp_status_prijave_id = 12"
                                );

                                $datum_prikupljanje_doc_query->execute(array(
                                    ':lsp_kandidat_id' => $kandidat_id
                                ));		
                                    
                                $datum_prikupljanje_doc_row = $datum_prikupljanje_doc_query->fetch();
                                $datum_prikupljanje_doc = date("d.m.Y", strtotime($datum_prikupljanje_doc_row['lsp_datetime']));
                                ?>
                                <div class="panel-group material-accordion material-accordion_success" id="prikupljanjeDoc">
                                    <div class="panel panel-default material-accordion__panel">
                                        <div class="panel-heading material-accordion__heading">
                                            <h4 class="panel-title">
                                                <a class="material-accordion__title" data-toggle="collapse" data-parent="#prikupljanjeDoc"  href="#status1">Prikupljanje dokumentacije 
                                                    <span style="float: right;"><?php echo $datum_prikupljanje_doc; ?></span>
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="status1" class="panel-collapse <?php if($status_prijave_id==12){ echo "in"; } ?> collapse material-accordion__collapse">
                                            <div class="panel-body">
                                                <div>
                                                    <div style="display: flex; justify-content:center; align-items:center; flex-direction: column">
                                                        <h1 style="text-align:center">Dokumenti za vizu</h1>
                                                        <br />

                                                        <document-checklist 
                                                            id="procesOdlaskaChecklist"
                                                            kandidat_id=<?php echo $kandidat_id;?>
                                                            employee_id=<?php echo $logged_employee_id;?>
                                                            kandidat_status=<?php echo $status_prijave_id;?>
                                                        ></document-checklist>
                                            
                                                        <?php 
                                                        if($status_prijave_id==12){ 
                                                            $candidate_departure_type = getCandidateDepartureType($kandidat_id);
                                                            $candidate_full_recognition = getCandidateFullRecognition($kandidat_id);
                                                            $nalog_poslodavac_trazi_jezik = getNalogPoslodavacTraziJezik($nalog_id);
                                                            ?>
                                                            <script>  
                                                                    procesOdlaskaChecklist.addEventListener("allReady", () => {
                                                                        let kandidat_id = <?php echo $kandidat_id; ?>;
                                                                        let dep_type = <?php echo $candidate_departure_type[0]; ?>;
                                                                        let full_recognition = <?php echo $candidate_full_recognition; ?>; 
                                                                        let poslodavac_trazi_jezik = <?php echo $nalog_poslodavac_trazi_jezik; ?>;
                                                                        
                                                                        if (dep_type == 2 || dep_type == 3 || ( (full_recognition == 1 || full_recognition == 2) && poslodavac_trazi_jezik == 0) ){
                                                                            unosTerminaVizeContainer.style.display = "flex";
                                                                        }else{
                                                                            $.ajax({
                                                                                url: 'ajax_data.php?page=check_certificate',
                                                                                type: 'POST',
                                                                                data: {'kandidat_id':kandidat_id},
                                                                                success: function(data) {
                                                                                    if(data == 1)
                                                                                    {
                                                                                        unosTerminaVizeContainer.style.display = "flex";
                                                                                    }
                                                                                    else
                                                                                    {
                                                                                        $("#certifikatAlertContainer").css("display", "block");
                                                                                    }
                                                                                },
                                                                                error: function (xhr, ajaxOptions, thrownError) {
                                                                                    alert(xhr.status);
                                                                                    alert(thrownError);
                                                                                }
                                                                            });
                                                                        }
                                                                    });
                                                            </script>  

                                                            <div style=" display: none; flex-direction: column; justify-content:center; align-items:center" id="unosTerminaVizeContainer">
                                                                
                                                                <h1>Ispunjeni su uslovi za unos termina vize!</h1>
                                                                <br />
                                                                <a href="#" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" data-toggle="modal" data-target="#kandidat_unos_termina_m"><i class="fa fa-plus" aria-hidden="true"></i> <span><?php echo $termin_btn; ?></span></a>
                                                            </div>
                                                            <div style="display: none" id="certifikatAlertContainer">
                                                                <h1>Unos termina onemogućen, jer kandidat nema certifikat ili je istekao!</h1>
                                                            </div>
                                                            <?php 
                                                        } ?>
                                                        <hr />
                                                    </div>	
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        /* PRIKUPLJANJE DOKUMENTACIJE END */

                        /* ČEKA TERMIN/VIZU START */
                            if($status_prijave_id>=15 or $status_prijave_id==4 or $status_prijave_id==10){
                                
                                $provjera_dopune_odbijenice_query = $db->prepare("
                                            SELECT lsp_datetime,lsp_status_prijave_id
                                            FROM idk_log_statusi_prijave
                                            WHERE lsp_kandidat_id = :lsp_kandidat_id AND lsp_projekt_id in (".implode(",",$projekti_kandidata).") AND lsp_status_prijave_id >=15"
                                );
                                $provjera_dopune_odbijenice_query->execute(array(
                                    ':lsp_kandidat_id' => $kandidat_id
                                ));
                                $bio_na_visem_statusu = 0;
                                $provjera_dopune_odbijenice_rows = $provjera_dopune_odbijenice_query->fetchAll();
                                $datum_status_termin=date("d.m.Y", strtotime($provjera_dopune_odbijenice_rows[0]['lsp_datetime']));
                                
                                if($provjera_dopune_odbijenice_rows[1]['lsp_status_prijave_id']==18){
                                    $datum_status_viza=date("d.m.Y", strtotime($provjera_dopune_odbijenice_rows[1]['lsp_datetime']));
                                }
                                foreach($provjera_dopune_odbijenice_rows as $provjera_dopune_odbijenice_row){
                                    if($provjera_dopune_odbijenice_row['lsp_status_prijave_id']>18){//ako je ikad bio na visem statusu
                                        $bio_na_visem_statusu = 1;
                                    }
                                }
                                ?>
                                <div class="panel-group material-accordion material-accordion_success" id="cekanje">
                                    <div class="panel panel-default material-accordion__panel "  >
                                        <div class="panel-heading material-accordion__heading">
                                            <h4 class="panel-title">
                                                <a class="material-accordion__title" data-toggle="collapse" data-parent="#cekanje"  href="#status2">Čeka termin <?php if($datum_status_viza){ echo "/ vizu";} ?> 
                                                    <span style="float: right;"><?php echo $datum_status_termin; if($datum_status_viza){ echo "     / ".$datum_status_viza; } ?></span>
                                                </a>
                                            </h4>
                                        </div>
                    
                                        <div id="status2" class="panel-collapse <?php if(($status_prijave_id==15 or $status_prijave_id==18) and $bio_na_visem_statusu==0){ echo "in"; } ?> collapse material-accordion__collapse">
                                            <div class="panel-body">
                                                <?php 
                                                    if($result["kandidat_bio_na_terminu"] == 0 ){ echo $new_termin_btn; }
                                                ?>
                                                <div class="row">
                                                    <div class="col-sm-3"></div>
                                                    <div class="col-sm-6 text-right">
                                                        <table id="datum_termina_table">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-center">Datum termina</th>
                                                                    <th class="text-center">Potvrda termina</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td class="text-center"><span class="<?php echo $termin_class; ?>"><?php echo $datum_termina; ?></span></td>
                                                                    <td class="text-center">
                                                                        <?php if($show_confirmation_btn){ ?>
                                                                        <a href="#" class="validate_docs btn material-btn material-btn_success main-container__column" data-toggle="modal" data-target="#provjera_termina_m" style="<?php echo $check_btn_style; ?>"><i class="fa fa-check" aria-hidden="true"></i></a>
                                                                    <?php } ?>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                        <hr>
                                                        <script>
                                                            $(document).ready(function() {
                                                                $("#datum_termina_table").DataTable({
                                                                    responsive: true,
                                                                    searching: false,
                                                                    paging: false,
                                                                    "ordering": false,
                                                                    "info":     false,
                                                                    "bAutoWidth": false,
                                                                    "aoColumns": [
                                                                            { "width": "50%" },
                                                                            { "width": "50%" }
                                                                        ]
                                                                });
                                                            });
                                                        </script>
                                                    </div>
                                                </div >
                                                <?php
                                                    if($result["kandidat_bio_na_terminu"] == 1 and $bio_na_visem_statusu==0){ echo $ishod_btn; }
                                                ?>
                                                <hr />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        /* ČEKA TERMIN/VIZU END */

                        /* DOPUNE/ODBIJENICE I ČEKA VIZU(novi) START */
                            /* PRIPREMA ZA DOPUNE/ODBIJENICE I CEKA VIZU START */
                               
                                $viza_incomplete_query = $db->prepare("
                                        SELECT
                                            vi_id, vi_status, vi_type, vi_complaint
                                        FROM
                                            `idk_pp_visa_incomplete`
                                        WHERE
                                            vi_cand_id = :kandidat_id
                                            AND vi_nalog_id = :nalog_id
                                ");

                                $viza_incomplete_query->execute(array(
                                        ':kandidat_id' => $kandidat_id,
                                        ':nalog_id' => $nalog_id
                                ));
                                $visas = $viza_incomplete_query->fetchAll();

                                $ceka_vizu_query = $db->prepare("
                                                                SELECT
                                                                    lsp_id, lsp_datetime, lsp_status_prijave_id, lsp_projekt_id
                                                                FROM
                                                                    idk_log_statusi_prijave
                                                                WHERE
                                                                    lsp_kandidat_id = :lsp_kandidat_id 
                                                                    AND lsp_projekt_id in (".implode(",",$projekti_kandidata).")
                                                                    AND lsp_status_prijave_id = 18
                                ");

                                $ceka_vizu_query->execute(array(
                                    ':lsp_kandidat_id' => $kandidat_id

                                ));
                                $ceka_vizu = $ceka_vizu_query->fetchAll(); 

                                $datum_dopuna_odbijenica_query= $db->prepare("
                                                                SELECT
                                                                    lsp_datetime,lsp_id
                                                                FROM
                                                                    idk_log_statusi_prijave
                                                                WHERE
                                                                    lsp_kandidat_id = :lsp_kandidat_id 
                                                                    AND lsp_projekt_id in (".implode(",",$projekti_kandidata).") 
                                                                    AND lsp_status_prijave_id in (21,24)"
                                );
                                
                                $datum_dopuna_odbijenica_query->execute(array(
                                    ':lsp_kandidat_id' => $kandidat_id

                                ));				
                                $datum_dopuna_odbijenica_rows=$datum_dopuna_odbijenica_query->fetchAll();
                                
                            /* PRIPREMA ZA DOPUNE/ODBIJENICE I CEKA VIZU END */
                        
                            /* PROLAZAK KROZ SVE visa_incomplete START */
                                /* prolazi se kroz sve redove dopuna/odbijenica kako bi se mogli povuci i expanderi za "ceka vizu" koji inace treba doci poslije */
                                for($visaIndex = 0; $visaIndex < count($visas); $visaIndex++){

                                    $viza_incomplete_query_row = $visas[$visaIndex];
                                    $ceka_vizu_query_row = $ceka_vizu[$visaIndex+1];
                                    $datum_dopuna_odbijenica_row = $datum_dopuna_odbijenica_rows[$visaIndex];

                                    $ceka_vizu_datum = date("d.m.Y", strtotime($ceka_vizu_query_row['lsp_datetime']));
                                    $datum_dop_odb = date("d.m.Y", strtotime($datum_dopuna_odbijenica_row['lsp_datetime']));

                                    $ceka_vizu_id = $ceka_vizu_query_row['lsp_id'];				
                                    $viza_inc_id = $viza_incomplete_query_row['vi_id'];
                                    $viza_inc_status = $viza_incomplete_query_row['vi_status'];
                                    $viza_inc_type = $viza_incomplete_query_row['vi_type'];
                                    $vi_complaint = $viza_incomplete_query_row['vi_complaint'];

                                    /* DOPUNA START */
                                        if($viza_inc_type == 1){
                                            ?>
                                            <div class="panel-group material-accordion material-accordion_success" id="dopuna<?php echo $viza_inc_id;?>">
                                                <div class="panel panel-default material-accordion__panel"  >
                                                    <div class="panel-heading material-accordion__heading">
                                                        <h4 class="panel-title">
                                                            <a class="material-accordion__title" data-toggle="collapse" data-parent="#dopuna<?php echo $viza_inc_id;?>" href="#statusDopuna<?php echo $viza_inc_id;?>">Dopuna dokumentacije 
                                                                <span style="float: right;"><?php echo $datum_dop_odb; ?></span>
                                                            </a>
                                                        </h4>
                                                    </div>
                                                    <div id="statusDopuna<?php echo $viza_inc_id;?>" class="panel-collapse <?php if($viza_inc_status==1){ echo "in"; } ?>  collapse material-accordion__collapse">
                                                        <div class="panel-body">
                                                            <div class="row">
                                                                <div class="col-sm-12">
                                                                    <div class="row">
                                                                        <div class="col-xs-12 text-right">
                                                                            <!-- Opcija  #urediVI AZURIRAJ DOPUNU START -->
                                                                            <?php
                                                                            if($viza_inc_status == 1){
                                                                                ?>
                                                                                <a href="" class="btn material-btn material-btn-icon-warning material-btn_warning main-container__column" data-toggle="modal" data-target="#urediVI">
                                                                                    <i class="fa fa-thumb-tack" aria-hidden="true"></i>
                                                                                    <span>Ažuriraj dopunu</span>
                                                                                </a>
                                                                                <?php 
                                                                            } ?>
                                                                            <!-- Opcija  #urediVI AZURIRAJ DOPUNU END -->
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <hr />
                                                            <div style="display: flex; justify-content:center; align-items:center; flex-direction: column">
                                                                <h1 style="text-align:center">Dokumenti dopune</h1>
                                                                <br />

                                                                <document-checklist
                                                                    id='<?php echo "dokumenti".$viza_inc_id; ?>'		
                                                                    kandidat_id=<?php echo $kandidat_id;?>
                                                                    employee_id=<?php echo $logged_employee_id;?>
                                                                    dopuna=<?php echo $viza_inc_id; ?>
                                                                ></document-checklist>

                                                                <?php
                                                                if($viza_inc_status == 1){ 
                                                                        $aktivnaDopunaId = $viza_inc_id;	
                                                                    ?>
                                                                    <script>  
                                                                        <?php echo "dokumenti".$viza_inc_id; ?>.addEventListener("allReady", () => {
                                                                            document.querySelector("#unosIshodaDopune").style.display = "block";
                                                                        });
                                                                    </script>  
                                                                    <div class="col-xs-12 text-center" style="display: none; margin-top: 24px;" id="unosIshodaDopune" >
                                                                        <!-- Opcija  #provjera_dopune START -->
                                                                        <a href="#" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" data-toggle="modal" data-target="#provjera_dopune">
                                                                            <i class="fa fa-plus" aria-hidden="true"></i> 
                                                                            <span>Provjera dopune</span>
                                                                        </a>
                                                                        <!-- Opcija  #provjera_dopune END -->
                                                                    </div>
                                                            
                                                                    <?php 
                                                                } ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php 
                                            /* ČEKA VIZU NAKON DOPUNE START */
                                                if($viza_inc_status == 0 ){
                                                    ?>	
                                                    <!--  
                                                        Zbog ispisa svakog unosa vize nakon same odbijenice ili dopune, ovdje ih uzimam iz baze i provjeravam da li 
                                                        je dopuna/odbijenica zavrsena, ako jest ispisuje se expander ceka vizu, samo zadnji ce biti otvoren i samo 
                                                        njemu ce button ishod termina biti enabled 
                                                    -->
                                                    <div class="panel-group material-accordion material-accordion_success" id="cekanje<?php echo $ceka_vizu_id; ?>">
                                                        <div class="panel panel-default material-accordion__panel "  >
                                                            <div class="panel-heading material-accordion__heading">
                                                                <h4 class="panel-title">
                                                                    <a class="material-accordion__title" data-toggle="collapse" data-parent="#cekanje<?php echo $ceka_vizu_id; ?>"  href="#status2<?php echo $ceka_vizu_id; ?>"> Čeka vizu 
                                                                        <span style="float: right;"><?php echo $ceka_vizu_datum; ?></span>
                                                                    </a>
                                                                </h4>
                                                            </div>
                                                            
                                                            <div id="status2<?php echo $ceka_vizu_id; ?>" class="panel-collapse <?php if($status_prijave_id==18 and $visaIndex == count($visas)-1){echo "in"; $disabled="";}else{ $disabled="disabled"; } ?> collapse material-accordion__collapse">
                                                                <div class="panel-body">
                                                                        <div class="row">
                                                                            <div class="col-sm-5"></div>
                                                                            <div class="col-sm-3">
                                                                                <a href="#" class="<?php echo $disabled; ?> btn material-btn material-btn-icon-success material-btn_success main-container__column" data-toggle="modal" data-target="#ishod_termina_m">
                                                                                    <i class="fa fa-plus" aria-hidden="true"></i> 
                                                                                    <span>Ishod termina</span>
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    <hr />
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php 
                                                }
                                            /* ČEKA VIZU NAKON DOPUNE END */
                                        }	
                                    /* DOPUNA END */

                                    /* ODBIJENICA START */
                                        if($viza_inc_type == 2){
                                            if($vi_complaint==1){
                                                //Ima uslov za žalbu
                                                ?>
                                                <div class="panel-group material-accordion material-accordion_success" id="odbijen<?php echo $viza_inc_id;?>">
                                                    <div class="panel panel-default material-accordion__panel"  >
                                                        <div class="panel-heading material-accordion__heading">
                                                            <h4 class="panel-title">
                                                                <a class="material-accordion__title" data-toggle="collapse" data-parent="#odbijen<?php echo $viza_inc_id;?>"  href="#statusOdbijen<?php echo $viza_inc_id;?>">Odbijenica 
                                                                    <span style="float: right;"><?php echo $datum_dop_odb; ?></span>
                                                                </a>
                                                            </h4>
                                                        </div>
                                                        <div id="statusOdbijen<?php echo $viza_inc_id;?>" class="panel-collapse <?php if($viza_inc_status==1){ echo "in"; } ?>  collapse material-accordion__collapse">
                                                            <div class="panel-body">
                                                                <div class="row">
                                                                    <div class="col-sm-12">
                                                                        <div class="row">
                                                                            <div class="col-xs-12 text-right">
                                                                                <!-- Opcija  #urediVI ODBIJENICU START -->
                                                                                <?php 
                                                                                if($viza_inc_status == 1){
                                                                                    ?>
                                                                                    <a href="" class="btn material-btn material-btn-icon-warning material-btn_warning main-container__column" data-toggle="modal" data-target="#urediVI">
                                                                                        <i class="fa fa-thumb-tack" aria-hidden="true"></i>
                                                                                        <span>Ažuriraj odbijenicu</span>
                                                                                    </a>
                                                                                    <?php 
                                                                                } ?>
                                                                                <!-- Opcija  #urediVI ODBIJENICU END -->
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <hr />
                                                                <div style="display: flex; justify-content:center; align-items:center; flex-direction: column">
                                                                    <h1 style="text-align:center">Dokumenti odbijenice</h1>
                                                                    <br />

                                                                    <document-checklist 
                                                                        id='<?php echo "dokumenti".$viza_inc_id; ?>'		
                                                                        kandidat_id=<?php echo $kandidat_id;?>
                                                                        employee_id=<?php echo $logged_employee_id;?>
                                                                        dopuna=<?php echo $viza_inc_id; ?>
                                                                    ></document-checklist>

                                                                    <?php 
                                                                    if($viza_inc_status==1){
                                                                        $aktivnaOdbijenicaId = $viza_inc_id;
                                                                        ?>
                                                                        <script>  
                                                                            <?php echo "dokumenti".$viza_inc_id; ?>.addEventListener("allReady", () => {
                                                                                document.querySelector("#unosIshodaOdbijenice").style.display = "block";
                                                                            });
                                                                        </script>  
                                                                        <div class="col-xs-12 text-center" style="display: none; margin-top: 24px;" id="unosIshodaOdbijenice" >
                                                                                <!-- Opcija #provjera_zalbe START -->
                                                                                <a href="#" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" data-toggle="modal" data-target="#provjera_zalbe">
                                                                                    <i class="fa fa-plus" aria-hidden="true"></i> 
                                                                                    <span>Provjeri odbijenicu</span>
                                                                                </a>
                                                                                <!-- Opcija #provjera_zalbe END -->
                                                                        </div>
                                                                        <?php 
                                                                    } ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <?php
                                                /* ČEKA VIZU NAKON ODBIJENICE START */
                                                    if($viza_inc_status == 0 ){ 
                                                        ?>
                                                        <!--  
                                                            Zbog ispisa svakog unosa vize nakon same odbijenice ili dopune, ovdje ih uzimam iz baze i provjeravam da li 
                                                            je dopuna/odbijenica zavrsena, ako jest ispisuje se expander ceka vizu, samo zadnji ce biti otvoren i samo 
                                                            njemu ce button ishod termina biti enabled 
                                                        -->
                                                        <div class="panel-group material-accordion material-accordion_success" id="cekanje<?php echo $ceka_vizu_id; ?>">
                                                            <div class="panel panel-default material-accordion__panel "  >
                                                                <div class="panel-heading material-accordion__heading">
                                                                    <h4 class="panel-title">
                                                                        <a class="material-accordion__title" data-toggle="collapse" data-parent="#cekanje<?php echo $ceka_vizu_id; ?>"  href="#status2<?php echo $ceka_vizu_id; ?>">Čeka vizu 
                                                                            <span style="float: right;"><?php echo $ceka_vizu_datum; ?></span>
                                                                        </a>
                                                                    </h4>
                                                                </div>
                                                                
                                                                <div id="status2<?php echo $ceka_vizu_id; ?>" class="panel-collapse <?php if($status_prijave_id==18 and $visaIndex == count($visas)-1){echo "in"; $disabled="";}else{ $disabled="disabled"; } ?> collapse material-accordion__collapse">
                                                                    <div class="panel-body">
                                                                        <div class="row">
                                                                            <div class="col-sm-5"></div>
                                                                            <div class="col-sm-3">
                                                                                <a href="#" class="<?php echo $disabled; ?> btn material-btn material-btn-icon-success material-btn_success main-container__column" data-toggle="modal" data-target="#ishod_termina_m">
                                                                                    <i class="fa fa-plus" aria-hidden="true"></i> 
                                                                                    <span>Ishod termina</span>
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                        <hr />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php 
                                                    }
                                                /* ČEKA VIZU NAKON ODBIJENICE END */
                                            }else if($vi_complaint == 0){
                                                $disabled="disabled ";	
                                                if($viza_inc_status == 1){
                                                    $aktivnaOdbijenicaId=$viza_inc_id;
                                                    $disabled="";
                                                }
                                                ?> <!-- Nema uslova za žalbu -->
                                                <div class="panel-group material-accordion material-accordion_success" id="odbijen<?php echo $viza_inc_id;?>">
                                                    <div class="panel panel-default material-accordion__panel"  >
                                                        <div class="panel-heading material-accordion__heading">
                                                            <h4 class="panel-title">
                                                                <a class="material-accordion__title" data-toggle="collapse" data-parent="#odbijen<?php echo $viza_inc_id;?>"  href="#statusOdbijen<?php echo $viza_inc_id;?>">Odbijenica 
                                                                    <span style="float: right;"><?php echo $datum_dop_odb; ?></span>
                                                                </a>
                                                            </h4>
                                                        </div>
                                                        <div id="statusOdbijen<?php echo $viza_inc_id;?>" class="panel-collapse <?php if($viza_inc_status==1){ echo "in"; } ?>  collapse material-accordion__collapse">
                                                            <div class="panel-body">
                                                                <div class="row">
                                                                    <div class="col-xs-12 text-center">
                                                                        <!-- Opcija #ishod_zalbe START   /za sada nema funkcionalost jer ne znam hoce ovako ostat -->
                                                                        <a href="#" class="<?php echo $disabled; ?>btn material-btn material-btn-icon-success material-btn_success main-container__column" data-toggle="modal" data-target="#ishod_zalbe">
                                                                            <i class="fa fa-plus" aria-hidden="true"></i> 
                                                                            <span>Ishod žalbe</span>
                                                                        </a>
                                                                        <!-- Opcija #ishod_zalbe END -->
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                        }
                                    /* ODBIJENICA END */
                                }
                            /* PROLAZAK KROZ SVE visa_incomplete END */
                        
                            /* Modali za dopune i odbijenice START */ 
                            
                                $flagDopunaOdbijenica = 0;
                                /*
                                    DESC flagDopunaOdbijenica
                                    0 - nedozvoljeno stanje / nevidljivo
                                    1 - Dopuna
                                    2 - Odbijenica
                                */
                                if($aktivnaDopunaId != 0 AND $aktivnaOdbijenicaId == 0){
                                    $flagDopunaOdbijenica = 1;
                                }else if($aktivnaDopunaId == 0 AND $aktivnaOdbijenicaId != 0){
                                    $flagDopunaOdbijenica = 2;
                                }else{
                                    $flagDopunaOdbijenica = 0;
                                }
                                if($flagDopunaOdbijenica != 0){

                                    if($flagDopunaOdbijenica == 1){
                                        //Dopuna
                                        $visaIncompleteId = $aktivnaDopunaId;
                                        $modalTitleEditVi = "Uredi dopunu";
                                    }else{
                                        //Odbijenica
                                        $visaIncompleteId = $aktivnaOdbijenicaId;
                                        $modalTitleEditVi = "Uredi odbijenicu";
                                    }	?>
                                
                                    <!-- Modal za opciju  #provjera_dopune START -->
                                        <div class="modal material-modal material-modal_primary fade text-left" id="provjera_dopune">
                                            <div class="modal-dialog ">
                                                <div class="modal-content material-modal__content">
                                                    <div class="modal-header material-modal__header">
                                                        <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                                        <h4 class="modal-title material-modal__title text-center">Da li su svi dokumenti dopune poslani na ambasadu</h4>
                                                    </div>
                                                    <form action="<?php getSiteURL();?>do.php?form=status_dopuna" method="post" role="form" class="form-horizontal">
                                                        <div class="modal-body material-modal__body">
                                                            <div class="form-group text-center">
                                                                <button type="submit" class="btn btn-primary material-btn material-btn_success"> Da</button>
                                                                <button type="buton" class="btn btn-primary material-btn material-btn_danger" data-dismiss="modal">Ne</button>
                                                                <input type="hidden" value="<?php echo $kandidat_id; ?>" name="kandidat_id">
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <!-- Modal za opciju  #provjera_dopune END	-->
                                        
                                    <!-- Modal za opciju  #provjera_zalbe START -->
                                        <div class="modal material-modal material-modal_primary fade text-left" id="provjera_zalbe">
                                            <div class="modal-dialog ">
                                                <div class="modal-content material-modal__content">
                                                    <div class="modal-header material-modal__header">
                                                        <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                                        <h4 class="modal-title material-modal__title text-center">Da li je žalba poslana na ambasadu</h4>
                                                    </div>
                                                    <form action="<?php getSiteURL();?>do.php?form=status_odbijen" method="post" role="form" class="form-horizontal">
                                                        <div class="modal-body material-modal__body">
                                                            <div class="form-group text-center">
                                                                <button type="submit" class="btn btn-primary material-btn material-btn_success"> Da</button>
                                                                <button type="buton" class="btn btn-primary material-btn material-btn_danger" data-dismiss="modal">Ne</button>
                                                                <input type="hidden" value="<?php echo $kandidat_id; ?>" name="kandidat_id">
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <!-- Modal za opciju  #provjera_zalbe END -->
                                    
                                    <!-- Modali za opcije { #ishod_zalbe } START -->
                                        <div class="modal material-modal material-modal_primary fade text-left" id="ishod_zalbe">
                                            <div class="modal-dialog ">
                                                <div class="modal-content material-modal__content">
                                                    <div class="modal-header material-modal__header">
                                                        <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                                        <h4 class="modal-title material-modal__title">Kandidat nema uslova za žalbu</h4>
                                                    </div>
                                                    <form action="<?php getSiteURL();?>do.php?form=ishod_zalbe" method="post" role="form" class="form-horizontal">
                                                        <div class="modal-body material-modal__body">
                                                            <div class="form-group">
                                                                <div class="form-group col-sm-12">
                                                                    <div class="col-sm-4" style="padding-top:5px; text-align:right;">Odustao:</div>
                                                                    <div class="col-sm-8 " style="padding-left:0px;">
                                                                        <select class="selectpicker" id="ishod_zalbe_select" name="ishod_zalbe_select[]"  style="width: 75%;" required>
                                                                            <option value="" selected disabled ></option>
                                                                            <option value=""   >Kandidat</option>
                                                                            <option value=""   >Poslodavac</option>
                                                                    
                                                                        </select>
                                                                    </div>
                                                                </div>	
                                                            </div>
                                                            <div class="modal-footer material-modal__footer">
                                                                <button type="submit" disabled class="btn btn-primary material-btn material-btn_success"><i class="fa fa-edit" aria-hidden="true"></i> Spremi</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <!-- Modali za opcije { #ishod_zalbe } END -->

                                    <!-- Modal za opciju { #urediVI } START	-->
                                        <div class="modal material-modal material-modal_success fade text-left" id="urediVI">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content material-modal__content">
                                                    <div class="modal-header material-modal__header">
                                                        <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                                        <h4 class="modal-title material-modal__title"><?php echo $modalTitleEditVi; ?></h4>
                                                    </div>
                                                    <form action="<?php getSiteURL();?>do.php?form=visa_incomplete_edit" enctype="multipart/form-data" method="post" role="form" class="form-horizontal" id="editVisaIncompleteForm">
                                                        <div class="modal-body material-modal__body">
                                                            <div class="row">
                                                                <div class="col-md-offset-2 col-md-8">
                                                                    <?php 
                                                                    $detailsForVisaIncomplete = getDetailsForVisaIncompleteArrayR($visaIncompleteId);
                                                                    $countVisaIncomplete = count($detailsForVisaIncomplete["count"]);
                                                                    if($countVisaIncomplete == 1){
                                                                        ?>
                                                                        <!-- Skrivene stavke forme START -->
                                                                        <input type="hidden" name="vi_id" id="vi_id" value="<?php echo $detailsForVisaIncomplete["vi_id"][0]; ?>">
                                                                        <input type="hidden" name="vi_cand_id" id="vi_cand_id" value="<?php echo $detailsForVisaIncomplete["vi_cand_id"][0]; ?>">
                                                                        <input type="hidden" name="vi_type" id="vi_type" value="<?php echo $detailsForVisaIncomplete["vi_type"][0]; ?>">
                                                                        <input type="hidden" name="vi_status" id="vi_status" value="<?php echo $detailsForVisaIncomplete["vi_status"][0]; ?>">
                                                                        <input type="hidden" name="vi_nalog_id" id="vi_nalog_id" value="<?php echo $detailsForVisaIncomplete["vi_nalog_id"][0]; ?>">
                                                                        <input type="hidden" name="vi_pp_partner_id" id="vi_pp_partner_id" value="<?php echo $detailsForVisaIncomplete["vi_pp_partner_id"][0]; ?>">
                                                                        <!-- Skrivene stavke forme END -->

                                                                        <!-- 
                                                                            *********************************************************
                                                                            * 	PRIKAZ ZA KORISNIKA START 							*
                                                                            *********************************************************
                                                                        -->
                                                                        <?php 
                                                                        /*
                                                                            Provjera da li je informacija pristugla od kandidata i od poslodavca. Mogući ishodi su:
                                                                                VI - Visa Incomplete
                                                                                
                                                                                KANDIDAT	POSLODAVAC	REZULTAT
                                                                                0			0			Slučaj koji se ne može desiti jer VI ne može biti unešen bez jedne od kombinacije
                                                                                0			1			Slučaj kada je prethodno javio poslodavac za VI - ovdje će se postaviti pitanje da li je možda kandidat javio za VI
                                                                                1			0			Slučaj kada je prethodno javio kandidat za VI - ovdje će se postaviti pitanje da li je možda poslodavac javio za VI
                                                                                1			1			Slučaj kada je naznačeno da su i kandidat i poslodavac javili za dopunu - u ovom slučaju će biti omogućeno samo dodavanje novih dokumenata kojih nema trenutno dodanih i vezanih za tu dopunu

                                                                        */
                                                                        $flagReceivedCandidate = 0;
                                                                        if($detailsForVisaIncomplete["vi_date_received_candidate"][0] != NULL AND $detailsForVisaIncomplete["vi_date_we_received_candidate"][0] != NULL AND $detailsForVisaIncomplete["vi_deadline_date_candidate"][0] != NULL){
                                                                            $flagReceivedCandidate = 1;
                                                                        }
                                                                        $flagReceivedEmployer = 0;
                                                                        if($detailsForVisaIncomplete["vi_date_received_employer"][0] != NULL AND $detailsForVisaIncomplete["vi_date_we_received_employer"][0] != NULL AND $detailsForVisaIncomplete["vi_deadline_date_employer"][0] != NULL){
                                                                            $flagReceivedEmployer = 1;
                                                                        }

                                                                        $newReceivedVi = 0;
                                                                        $newReceivedLabel = "";
                                                                        $newReceivedMessage = "";
                                                                        if($flagReceivedCandidate == 0 AND $flagReceivedEmployer == 1){
                                                                            $newReceivedVi = 1;
                                                                            $newReceivedLabel = "Da li je kandidat javio:";
                                                                            $newReceivedMessage = '
                                                                                Prva informacija o dopuni/odbijenici dobijena je od strane <strong>Poslodavca</strong>. 
                                                                                U slučaju da uređujete dopunu/odbijenicu jer ste zaprimili informaciju od <strong>Kandidata</strong>, 
                                                                                polje sa pitanjem <strong>"Da li je kandidat javio"</strong> označite sa <strong>DA</strong> i izvršite označavanje traženih datuma.
                                                                                Ako informacija nije zaprimljena od strane <strong>Kandidata</strong>, označite sa <strong>NE</strong>. 
                                                                            ';
                                                                        }else if($flagReceivedCandidate == 1 AND $flagReceivedEmployer == 0){
                                                                            $newReceivedVi = 2;
                                                                            $newReceivedLabel = "Da li je poslodavac javio:";
                                                                            $newReceivedMessage = '
                                                                                Prva informacija o dopuni/odbijenici dobijena je od strane <strong>Kandidata</strong>. 
                                                                                U slučaju da uređujete dopunu/odbijenicu jer ste zaprimili informaciju od <strong>Poslodavca</strong>, 
                                                                                polje sa pitanjem <strong>"Da li je poslodavac javio"</strong> označite sa <strong>DA</strong> i izvršite označavanje traženih datuma.
                                                                                Ako informacija nije zaprimljena od strane <strong>Poslodavca</strong>, označite sa <strong>NE</strong>.
                                                                            ';
                                                                        }else if($flagReceivedCandidate == 1 AND $flagReceivedEmployer == 1){
                                                                            $newReceivedVi = 3;
                                                                            $newReceivedLabel = "";
                                                                            $newReceivedMessage = "";
                                                                        }else{
                                                                            $newReceivedVi = 0;
                                                                            $newReceivedLabel = "";
                                                                            $newReceivedMessage = "";
                                                                        }
                                                                        //echo "Kandidat: ".$flagReceivedCandidate." Poslodavac: ".$flagReceivedEmployer." Slučaj: ".$newReceivedVi;
                                                                        ?>

                                                                        <input type="hidden" name="new_received_VI" id="new_received_VI" value="<?php echo $newReceivedVi; ?>">
                                                                        <?php 
                                                                        if($newReceivedVi == 1 OR $newReceivedVi == 2){
                                                                            ?>
                                                                            <div class="form-group">
                                                                                <div class="alert alert-warning text-center" role="alert">
                                                                                    <?php echo $newReceivedMessage; ?>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label for="new_reported_VI" class="col-sm-4 control-label text-right"><span class="text-danger">*</span> <?php echo $newReceivedLabel; ?></label>
                                                                                <div class="col-sm-8">
                                                                                    <div class="">
                                                                                        <select class="selectpicker" id="new_reported_VI" name="new_reported_VI" title = "Odaberite opciju" required>
                                                                                            <option value="1">DA</option>
                                                                                            <option value="0">NE</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="new_received_vi_group">
                                                                                <div class="form-group">
                                                                                    <label for="new_date_received_VI" class="col-sm-4 control-label text-right"><span class="text-danger">*</span> Datum prijema: </label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="materail-input-block materail-input-block_success">
                                                                                            <input class="form-control materail-input" type="text" name="new_date_received_VI" autocomplete="off" id="new_date_received_VI" placeholder="Unesite datum prijema">
                                                                                            <span class="materail-input-block__line"></span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>	
                                                                                <div class="form-group">
                                                                                    <label for="new_date_we_received_VI" class="col-sm-4 control-label text-right"><span class="text-danger">*</span> Datum našeg prijema:</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="materail-input-block materail-input-block_success">
                                                                                            <input class="form-control materail-input" type="text" name="new_date_we_received_VI" autocomplete="off" id="new_date_we_received_VI" placeholder="Unesite datum našeg prijema informacije">
                                                                                            <span class="materail-input-block__line"></span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <label for="new_deadline_date_VI" class="col-sm-4 control-label text-right"><span class="text-danger">*</span> Krajnji datum:</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="materail-input-block materail-input-block_success">
                                                                                            <input class="form-control materail-input" type="text" name="new_deadline_date_VI" autocomplete="off" id="new_deadline_date_VI" placeholder="Unesite krajnji datum za dostavljanje">
                                                                                            <span class="materail-input-block__line"></span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <script>
                                                                                function requiredNewReceived(vr){
                                                                                    var vrNR = Boolean(vr);
                                                                                    $("#new_date_received_VI").prop( "required", vrNR ).val(null);
                                                                                    $("#new_date_we_received_VI").prop( "required", vrNR ).val(null);
                                                                                    $("#new_deadline_date_VI").prop( "required", vrNR ).val(null);
                                                                                };
                                                                                function divShowHideNewReceived(vr){
                                                                                    var vrSHNR = Boolean(vr);
                                                                                    if(vrSHNR == true){
                                                                                        $(".new_received_vi_group").show();
                                                                                        requiredNewReceived(true);
                                                                                    }else{
                                                                                        $(".new_received_vi_group").hide();
                                                                                        requiredNewReceived(false);
                                                                                    }
                                                                                };
                                                                                $(document).ready(function () {
                                                                                    $("#new_reported_VI").val(null).selectpicker("refresh");
                                                                                    divShowHideNewReceived(false);
                                                                                    $("#new_date_received_VI").flatpickr({
                                                                                        dateFormat: "Y-m-d H:i",
                                                                                        disableMobile: "true",
                                                                                        allowInput: "true",
                                                                                        enableTime: true,
                                                                                        time_24hr: true
                                                                                    });
                                                                                    $("#new_date_we_received_VI").flatpickr({
                                                                                        dateFormat: "Y-m-d H:i",
                                                                                        disableMobile: "true",
                                                                                        allowInput: "true",
                                                                                        enableTime: true,
                                                                                        time_24hr: true
                                                                                    });
                                                                                    $("#new_deadline_date_VI").flatpickr({
                                                                                        dateFormat: "Y-m-d",
                                                                                        disableMobile: "true",
                                                                                        allowInput: "true"
                                                                                    });
                                                                                });
                                                                                $('#new_reported_VI').change(function(){
                                                                                    var newReportedVI = parseInt($("#new_reported_VI").val());
                                                                                    if(newReportedVI == 1){
                                                                                        divShowHideNewReceived(true);
                                                                                    }else{
                                                                                        divShowHideNewReceived(false);
                                                                                    }
                                                                                });
                                                                            </script>
                                                                            <?php 
                                                                        }  ?>

                                                                        <!-- UNOS DOKUMENATA START -->
                                                                        <style>
                                                                            .cardDocumentTypesVI{
                                                                                border: 1px solid #cccccc;border-radius: 15px; background-color: #cccccc30; padding: 0px 50px;
                                                                            }
                                                                            .marginCardDocumentTypesVI{
                                                                                margin-bottom: 25px;
                                                                            }
                                                                            .textAreaDocumentVI{
                                                                                border-radius: 20px;
                                                                                padding: 20px;
                                                                            }
                                                                        </style>
                                                                        <div class="form-group">
                                                                            <label for="document_types_VI" class="col-sm-4 control-label text-right"><span class="text-danger">*</span> Dokumenti:</label>
                                                                            <div class="col-sm-8">
                                                                                <div class="">
                                                                                    <select class="selectpicker" id="document_types_VI" name="document_types_VI[]" title = "Odaberite tipove dokumenata" multiple required>
                                                                                        <?php 
                                                                                        $queryDocumentTypesVI = $db->prepare("
                                                                                            SELECT 
                                                                                                dt.doc_type_id, dt.doc_type_name, dt.doc_type_name_de
                                                                                            FROM
                                                                                                idk_pp_document_types dt
                                                                                            WHERE 
                                                                                                dt.doc_type_id NOT IN (
                                                                                                    SELECT 
                                                                                                        crd.crd_type_id
                                                                                                    FROM 
                                                                                                        idk_pp_cand_required_documents crd
                                                                                                    WHERE 
                                                                                                        crd.crd_cand_id = :crd_cand_id
                                                                                                        AND 
                                                                                                        crd.crd_nalog_id = :crd_nalog_id
                                                                                                        AND 
                                                                                                        crd.crd_status = 1
                                                                                                        AND 
                                                                                                        crd.crd_vi_id = :crd_vi_id
                                                                                                )
                                                                                        ");
                                                                                        $queryDocumentTypesVI->execute(array(
                                                                                            ':crd_cand_id' => $detailsForVisaIncomplete["vi_cand_id"][0],
                                                                                            ':crd_nalog_id' => $detailsForVisaIncomplete["vi_nalog_id"][0],
                                                                                            ':crd_vi_id' => $detailsForVisaIncomplete["vi_id"][0],
                                                                                        ));
                                                                                        while($rowDocumentTypesVI = $queryDocumentTypesVI->fetch()){
                                                                                            echo '<option value="'.intval($rowDocumentTypesVI["doc_type_id"]).'" data-subtext="'.$rowDocumentTypesVI["doc_type_name_de"].'">'.$rowDocumentTypesVI["doc_type_name"].'</option>';
                                                                                        }
                                                                                        ?>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <script>
                                                                            Array.prototype.diffVI = function(a){
                                                                                return this.filter(function(i) {return a.indexOf(i) < 0;});
                                                                            };
                                                                            function makeInputVI(id_doc){
                                                                                var typeName = $("#document_types_VI > option[value=" + id_doc + "]").text();
                                                                                return `
                                                                                    <div id="detail_document_VI_${id_doc}" data-id_doc="${id_doc}" class="">
                                                                                        <div class="col-sm-12 text-center cardDocumentTypesVI marginCardDocumentTypesVI">
                                                                                            <div class="form-group">
                                                                                                <div class="col-sm-12 text-center">
                                                                                                    <h4><strong>${typeName}</strong></h4>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="form-group">
                                                                                                <label for="done_by_doc_VI_${id_doc}" class="col-sm-4 control-label text-right"><span class="text-danger">*</span> Zadužen:</label>
                                                                                                <div class="col-sm-8">
                                                                                                    <div class="">
                                                                                                        <select class="selectpicker" id="done_by_doc_VI_${id_doc}" name="done_by_doc_VI_${id_doc}" title = "Postavite zaduženje za ovaj dokument" required>
                                                                                                            <option value="1">Poslodavac</option>
                                                                                                            <option value="2">Kandidat</option>
                                                                                                        </select>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="form-group">
                                                                                                <label for="comment_doc_${id_doc}" class="col-sm-4 control-label text-right">
                                                                                                    <span class="text-danger">*</span> Komentar:
                                                                                                </label>
                                                                                                <div class="col-sm-8">
                                                                                                    <div class="form-group materail-input-block materail-input-block_success">
                                                                                                        <textarea class="form-control materail-input material-textarea textAreaDocumentVI" name="comment_doc_VI_${id_doc}" id="comment_doc_${id_doc}" placeholder="Unesite opis i razlog dopune za ovaj dokument" rows="4" required></textarea>
                                                                                                        <!--<span class="materail-input-block__line"></span>-->
                                                                                                        <span class="text-danger"><small>U slučaju zaduženja poslodavca za dokument, komentar napisati na njemačkom jeziku!</small></span>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                `;
                                                                                
                                                                            };
                                                                            function removeChildrenInGroupVI(arrayChildrenVI){
                                                                                if(arrayChildrenVI.length != 0){
                                                                                    const arrayChildrenValueVI = [];
                                                                                    $('.documents_VI').children('div').each(function () {
                                                                                        arrayChildrenValueVI.push($(this).data("id_doc"));
                                                                                    });
                                                                                    let differentArrayChildrenVI = arrayChildrenValueVI.diffVI(arrayChildrenVI);
                                                                                    differentArrayChildrenVI.forEach(elementVI => {
                                                                                        $("#detail_document_VI_"+elementVI).remove();
                                                                                    });
                                                                                }else{
                                                                                    $(".documents_VI").html("");
                                                                                }
                                                                            };
                                                                            function addChildrenInGroupVI(arrayChildrenVI){
                                                                                const arrayChildrenVIValue = [];
                                                                                $('.documents_VI').children('div').each(function () {
                                                                                    arrayChildrenVIValue.push($(this).data("id_doc"));
                                                                                });
                                                                                arrayChildrenVI.forEach(elementVI => {
                                                                                    if (jQuery.inArray(elementVI, arrayChildrenVIValue) == -1){
                                                                                        $(".documents_VI").append(makeInputVI(elementVI));
                                                                                        $("#done_by_doc_VI_"+elementVI).selectpicker("refresh");
                                                                                    }
                                                                                });
                                                                            };
                                                                            $("#document_types_VI").change(function(){
                                                                                if($("#document_types_VI").val() != null){
                                                                                    var typeDocumentsVI = $("#document_types_VI").val().map((e)=>parseInt(e));
                                                                                    removeChildrenInGroupVI(typeDocumentsVI);
                                                                                    addChildrenInGroupVI(typeDocumentsVI);
                                                                                }else{
                                                                                    $(".documents_VI").html("");
                                                                                }
                                                                            });
                                                                        </script>
                                                                        <div class="form-group">
                                                                            <div class="col-sm-12">
                                                                                <div class="documents_VI">
                                                                                    
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <!-- UNOS DOKUMENATA END -->
                                                                        
                                                                        <!-- Napomena start -->
                                                                        <div class="form-group" style = "margin-top: 15px; margin-bottom: 15px;">
                                                                            <div class="col-sm-12 text-center">
                                                                                <small>Sva polja označena sa <span class="text-danger">*</span> su obavezna!</small>
                                                                            </div>
                                                                        </div>
                                                                        <!-- Napomena end -->

                                                                        <!-- 
                                                                            *********************************************************
                                                                            * 	PRIKAZ ZA KORISNIKA END 							*
                                                                            *********************************************************
                                                                        -->
                                                                        <?php
                                                                    }else{
                                                                        ?>
                                                                        <div class="form-group" style = "margin-top: 15px; margin-bottom: 15px;">
                                                                            <div class="alert alert-danger text-center" role="alert">
                                                                                <strong>Greška</strong><br>
                                                                                Kontaktirajte Administratora sistema!
                                                                            </div>
                                                                        </div>
                                                                        <?php 
                                                                    }
                                                                    unset($detailsForVisaIncomplete);
                                                                    ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php 
                                                        if($countVisaIncomplete == 1){
                                                            ?>
                                                            <div class="modal-footer material-modal__footer">
                                                                <button type="submit" class="btn btn-primary material-btn material-btn_success" form="editVisaIncompleteForm"><i class="fa fa-edit" aria-hidden="true"></i> Spremi</button>
                                                            </div>
                                                            <?php 
                                                        } ?>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <!-- Modal za opciju { #urediVI } END -->
                                    <?php 
                                }
                            /* Modali za dopune i odbijenice END */

                        /* DOPUNE/ODBIJENICE I ČEKA VIZU(novi) END */

                        /* DOBIO VIZU START */
                            if($status_prijave_id==27 or $status_prijave_id==4 or $status_prijave_id==10){
                                
                                $datum_dobio_vizu_query= $db->prepare("
                                                    SELECT lsp_datetime
                                                    FROM idk_log_statusi_prijave
                                                    WHERE lsp_kandidat_id = :lsp_kandidat_id AND lsp_projekt_id in (".implode(",",$projekti_kandidata).") AND lsp_status_prijave_id = 27
                                ");
                                $datum_dobio_vizu_query->execute(array(
                                    ':lsp_kandidat_id' => $kandidat_id
                                ));
                                $datum_dobio_vizu_row=$datum_dobio_vizu_query->fetch();
                                $datum_dobio_vizu=date("d.m.Y", strtotime($datum_dobio_vizu_row['lsp_datetime']));
                                $datum_pocetak_rada_query = $db ->prepare("SELECT kandidat_potvrden_pocetak_rada, kandidat_dogovoreni_pocetak_rada, kandidat_destinacija_grad, kandidat_destinacija_postanski_broj FROM idk_kandidati WHERE kandidat_id=:kandidat_id;");
                                $datum_pocetak_rada_query->execute(array(
                                    'kandidat_id' => $kandidat_id
                                    ));
                                $datum_pocetak_rada_row=$datum_pocetak_rada_query->fetch();
                                $kandidat_potvrden_pocetak_rada         = $datum_pocetak_rada_row["kandidat_potvrden_pocetak_rada"];
                                $kandidat_dogovoreni_pocetak_rada       = $datum_pocetak_rada_row["kandidat_dogovoreni_pocetak_rada"];  
                                $kandidat_destinacija_grad              = $datum_pocetak_rada_row["kandidat_destinacija_grad"];  
                                $kandidat_destinacija_postanski_broj    = $datum_pocetak_rada_row["kandidat_destinacija_postanski_broj"];  
                                    ?>
                                <div class="panel-group material-accordion material-accordion_success" id="dobioVizu">
                                    <div class="panel panel-default material-accordion__panel"  >
                                        <div class="panel-heading material-accordion__heading">
                                            <h4 class="panel-title">
                                                <a class="material-accordion__title" data-toggle="collapse" data-parent="#dobioVizu"  href="#status3">Dobio vizu 
                                                    <span style="float: right;"><?php echo $datum_dobio_vizu; ?></span>
                                                </a>
                                            </h4>
                                        </div>
                                        <?php 
                                        $get_visa = $db->prepare("SELECT kandidat_viza_vrijedi_od, kandidat_viza_vrijedi_do FROM idk_kandidati WHERE kandidat_id = $kandidat_id");
                                        $get_visa->execute();
                                        $result_visa = $get_visa->fetch();
                                        ?>	
                                        <div id="status3" class="panel-collapse <?php if($status_prijave_id==27){ echo "in"; } ?>  collapse material-accordion__collapse">
                                            <div class="panel-body">
                                                <div class="row">
                                                    <div class="col-sm-3"></div>
                                                    <div class="col-sm-6 text-right">
                                                        <table id="datum_vize_table">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-center">Datum otkad vrijedi viza</th>
                                                                    <th class="text-center">Datum do kad vrijedi viza</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td class="text-center"><?php echo $result_visa["kandidat_viza_vrijedi_od"]; ?></td>
                                                                    <td class="text-center"><?php echo $result_visa["kandidat_viza_vrijedi_do"]; ?></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                        <hr>
                                                        <script>
                                                            $(document).ready(function() {
                                                                $("#datum_vize_table").DataTable({
                                                                    responsive: true,
                                                                    searching: false,
                                                                    paging: false,
                                                                    "ordering": false,
                                                                    "info":     false,
                                                                    "bAutoWidth": false,
                                                                    "aoColumns": [
                                                                            { "width": "50%" },
                                                                            { "width": "50%" }
                                                                        ]
                                                                });
                                                            });
                                                        </script>
                                                    </div>
                                                </div>
                                                
                                                <!-- MODAL POČETAK RADA START -->
                                                    <div class="modal material-modal material-modal_primary fade text-left" id="početak_rada_m">
                                                        <div class="modal-dialog ">
                                                            <div class="modal-content material-modal__content">
                                                                <div class="modal-header material-modal__header">
                                                                    <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                                                    <h4 class="modal-title material-modal__title">Unos početka rada</h4>
                                                                </div>
                                                                <div class="modal-body material-modal__body">
                                                                    <form action="<?php getSiteURL();?>do.php?form=unesi_datum_pocetak_rada" method="post" role="form" class="form-horizontal">
                                                                        <div class="form-group col-sm-12 text-right">
                                                                            <input type="hidden" name="kandidat_id" value="<?php echo $kandidat_id; ?>">
                                                                            <label for="datum_pocetak_rada" class="col-sm-5 control-label">Unesi dogovoreni datum početka rada:</label>
                                                                            <div class="col-sm-6">
                                                                                <div class="materail-input-block materail-input-block_success">
                                                                                    
                                                                                    <input class="form-control materail-input" type="text" name="datum_pocetak_rada" autocomplete="off" id="datum_pocetak_rada"  >
                                                                                    <span class="materail-input-block__line"></span>
                                                                                </div>
                                                                            </div>
                                                                            <script>
                                                                                $(function() {
                                                                                    initDateSelectNew();
                                                                                });

                                                                                function initDateSelectNew() {
                                                                                    $("#datum_pocetak_rada").flatpickr({
                                                                                        minDate: "2000-01-01"
                                                                                    });
                                                                                }
                                                                            </script>
                                                                        </div>
                                                                       
                                                                </div>
                                                                <div class="modal-footer material-modal__footer">
                                                                        <button type="submit" class="btn btn-primary material-btn material-btn_success"><i class="fa fa-edit" aria-hidden="true"></i> Spremi</button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <!-- MODAL POČETAK RADA END -->
                                                
                                                <!-- MODAL POTVRDI POČETAK RADA START-->
                                                    <div class="modal material-modal material-modal_primary fade text-left" id="potvrdi_pocetak_rada">
                                                        <div class="modal-dialog ">
                                                            <div class="modal-content material-modal__content">
                                                                <div class="modal-header material-modal__header">
                                                                    <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                                                    <h4 class="modal-title material-modal__title">Unos početka rada</h4>
                                                                </div>
                                                                <div class="modal-body material-modal__body">
                                                                    <form action="<?php getSiteURL();?>do.php?form=uredi_pocetak_rada" method="post" role="form" class="form-horizontal">
                                                                    <input type="hidden" name="kandidat_id" value="<?php echo $kandidat_id; ?>">
                                                                    <div class="form-group">
                                                                        <div class="col-sm-5" style="padding-top:5px; text-align:right;">Da li je kandidat počeo sa radom:
                                                                        </div>
                                                                        <div class="col-sm-7">
                                                                            <div class="materail-input-block materail-input-block_success idk_radio_buttons">
                                                                                <label class="main-container__column material-radio-group material-radio-group_success" for="potvrdi_pocetak_rada_da" style="padding-right: 5px;">
                                                                                    <input type="radio" name="potvrdi_pocetak_rada" id="potvrdi_pocetak_rada_da" class="material-radiobox" value="1" />
                                                                                    <span class="material-radio-group__element material-radio-group__check-radio"></span>
                                                                                    <span class="material-radio-group__element material-radio-group__caption">Da </span>
                                                                                </label>													
                                                                                
                                                                                <label class="main-container__column material-radio-group material-radio-group_danger" for="potvrdi_pocetak_rada_ne">
                                                                                    <input type="radio" name="potvrdi_pocetak_rada" id="potvrdi_pocetak_rada_ne" <?php if(isset($kandidat_potvrden_pocetak_rada) AND $kandidat_potvrden_pocetak_rada == 0){echo "checked";} ?> class="material-radiobox" value="0" />
                                                                                    <span class="material-radio-group__element material-radio-group__check-radio"></span>
                                                                                    <span class="material-radio-group__element material-radio-group__caption">Ne </span>
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>	
                                                                    <script>
                                                                        $(document).ready(function () {
                                                                            if( $('#potvrdi_pocetak_rada_ne').is(':checked') ){
                                                                                const novi_datum_pocetak_rada = $("#novi_datum_pocetak_rada").flatpickr();
                                                                                $('.novi_datum_pr').css('display', 'block');
                                                                                $("#spremi_detalje_pr").prop("disabled", true);
                                                                                
                                                                                $("#provjera_novog_datuma_pocetka_rada_da").click(function() {
                                                                                    

                                                                                    $(".novi_datum_pocetak_rada").css('display', 'block');
                                                                                    $("#spremi_detalje_pr").prop("disabled", true);
                                                                                    $("#novi_datum_pocetak_rada").change(function(){
                                                                                        if(!$.trim(this.value).length){
                                                                                            $("#spremi_detalje_pr").prop("disabled", true);
                                                                                        } 
                                                                                        else if($.trim(this.value).length) {
                                                                                            $("#spremi_detalje_pr").prop("disabled", false);
                                                                                        }
                                                                                    });
                                                                                })
                                                                                
                                                                                $("#provjera_novog_datuma_pocetka_rada_ne").click(function() {
                                                                                    $(".novi_datum_pocetak_rada").css('display', 'none');
                                                                                    $("#spremi_detalje_pr").prop("disabled", false);
                                                                                    
                                                                                    novi_datum_pocetak_rada.clear();
                                                                                })
                                                                                    }
                                                                            
                                                                        });
                                                                        $('#potvrdi_pocetak_rada_ne').click(function(){

                                                                            const novi_datum_pocetak_rada = $("#novi_datum_pocetak_rada").flatpickr();
                                                                            $("#provjera_novog_datuma_pocetka_rada_ne").prop("checked", false);
                                                                            $("#provjera_novog_datuma_pocetka_rada_da").prop("checked", false);
                                                                            novi_datum_pocetak_rada.clear();
                                                                            $('.novi_datum_pr').css('display', 'block');
                                                                            $("#spremi_detalje_pr").prop("disabled", true);
                                                                            if( !$("#provjera_novog_datuma_pocetka_rada_da").is(":checked") && !$("#provjera_novog_datuma_pocetka_rada_ne").is(":checked")){
                                                                                $("#spremi_detalje_pr").prop("disabled", true);
                                                                            }
                                                                            $("#provjera_novog_datuma_pocetka_rada_da").click(function() {
                                                                               

                                                                                $(".novi_datum_pocetak_rada").css('display', 'block');
                                                                                $("#spremi_detalje_pr").prop("disabled", true);
                                                                                $("#novi_datum_pocetak_rada").change(function(){
                                                                                    if(!$.trim(this.value).length){
                                                                                        $("#spremi_detalje_pr").prop("disabled", true);
                                                                                    } 
                                                                                    else if($.trim(this.value).length) {
                                                                                        $("#spremi_detalje_pr").prop("disabled", false);
                                                                                    }
                                                                                });
                                                                            })
                                                                            
                                                                            $("#provjera_novog_datuma_pocetka_rada_ne").click(function() {
                                                                                $(".novi_datum_pocetak_rada").css('display', 'none');
                                                                                $("#spremi_detalje_pr").prop("disabled", false);
                                                                                
                                                                                novi_datum_pocetak_rada.clear();
                                                                            })
                                                                            
                                                                        });
                                                                        $('#potvrdi_pocetak_rada_da').click(function(){

                                                                            $('.novi_datum_pr').css('display', 'none');
                                                                            $('.novi_datum_pocetak_rada').css('display', 'none');
                                                                            $("#spremi_detalje_pr").prop("disabled", false);

                                                                        });
                                                                    </script>	
                                                                    <div class="form-group novi_datum_pr" style="display: none;">
                                                                        <div class="col-sm-5" style="padding-top:5px; text-align:right;">
                                                                            Da li kandidat ima novi datum početka rada:
                                                                        </div>
                                                                        <div class="col-sm-7">
                                                                            <div class="materail-input-block materail-input-block_success idk_radio_buttons">
                                                                                <label class="main-container__column material-radio-group material-radio-group_success" for="provjera_novog_datuma_pocetka_rada_da" style="padding-right: 5px;">
                                                                                    <input type="radio" name="provjera_novog_datuma_pocetka_rada" id="provjera_novog_datuma_pocetka_rada_da" class="material-radiobox" value="1" />
                                                                                    <span class="material-radio-group__element material-radio-group__check-radio"></span>
                                                                                    <span class="material-radio-group__element material-radio-group__caption">Da </span>
                                                                                </label>													
                                                                                
                                                                                <label class="main-container__column material-radio-group material-radio-group_danger" for="provjera_novog_datuma_pocetka_rada_ne">
                                                                                    <input type="radio" name="provjera_novog_datuma_pocetka_rada" id="provjera_novog_datuma_pocetka_rada_ne" class="material-radiobox" value="0" />
                                                                                    <span class="material-radio-group__element material-radio-group__check-radio"></span>
                                                                                    <span class="material-radio-group__element material-radio-group__caption">Ne </span>
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group col-sm-12 text-right novi_datum_pocetak_rada" style="display: none;">
                                                                        <label for="novi_datum_pocetak_rada" class="col-sm-5 control-label">Unesi novi datum početka rada:</label>
                                                                        <div class="col-sm-6">
                                                                            <div class="materail-input-block materail-input-block_success">
                                                                                
                                                                                <input class="form-control materail-input" type="text" name="novi_datum_pocetak_rada" autocomplete="off" id="novi_datum_pocetak_rada"  >
                                                                                <span class="materail-input-block__line"></span>
                                                                            </div>
                                                                        </div>
                                                                        <script>
                                                                            $(function() {
                                                                                initDateSelectNPR();
                                                                            });

                                                                            function initDateSelectNPR() {
                                                                                $("#novi_datum_pocetak_rada").flatpickr({
                                                                                    minDate: "2000-01-01"
                                                                                });
                                                                            }
                                                                        </script>
                                                                    </div>	
                                                                </div>
                                                                <div class="modal-footer material-modal__footer">
                                                                        <button type="submit" class="btn btn-primary material-btn material-btn_success" disabled id="spremi_detalje_pr"><i class="fa fa-edit" aria-hidden="true"></i> Spremi</button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <!-- MODAL POTVRDI POČETAK RADA END -->
                                                <div class="row">
                                                    <div class="col-sm-12 text-center">
                                                        <?php if(!isset($kandidat_dogovoreni_pocetak_rada)){  
                                                            ?> 
                                                            <a href="#" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" data-toggle="modal" <?php if($status_prijave_id==27){ echo ' data-target="#početak_rada_m"';}else{ echo 'disabled';} ?>>
                                                                <i class="fa fa-plus" aria-hidden="true"></i>
                                                                <span>Unesi dogovoreni datum početka rada</span>
                                                            </a>
                                                        <?php }else{ ?>    

                                                            <div class="col-sm-3"></div>
                                                            <div class="col-sm-6 text-right">
                                                                <table id="detalji_pocetka_rada_table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th class="text-center">Datum početka rada</th>
                                                                            <th class="text-center">Potvrdi početak rada</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td class="text-center"><?php if(isset($kandidat_dogovoreni_pocetak_rada)){echo $kandidat_dogovoreni_pocetak_rada;}else{ echo "Kandidat nema unešen datum početka rada!";} ?></td>
                                                                            <td class="text-center"><a href="" class="btn material-btn material-btn_success main-container__column" data-toggle="modal" <?php if($status_prijave_id==27){ echo ' data-target="#potvrdi_pocetak_rada"';}else{ echo 'disabled';} ?> ><i class="fa fa-check" aria-hidden="true"></i></a></td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                                <script>
                                                                    $(document).ready(function() {
                                                                        $("#detalji_pocetka_rada_table").DataTable({
                                                                            responsive: true,
                                                                            searching: false,
                                                                            paging: false,
                                                                            "ordering": false,
                                                                            "info":     false,
                                                                            "bAutoWidth": false,
                                                                            "aoColumns": [
                                                                                { "width": "50%" },
                                                                                { "width": "50%" }
                                                                            ]
                                                                        });
                                                                    });
                                                                </script>
                                                                <div id = "candidate_id" style = "display:none"><?php echo $kandidat_id;?></div>
                                                                <hr>
                                                                <?php
                                                                            $kandidat_destinacija_grad              = $datum_pocetak_rada_row["kandidat_destinacija_grad"];  
                                                                            $kandidat_destinacija_postanski_broj    = $datum_pocetak_rada_row["kandidat_destinacija_postanski_broj"];  
                                                                            if(!is_null($kandidat_destinacija_grad) AND !is_null($kandidat_destinacija_postanski_broj)){
                                                                                ?>
                                                                                    <p style = "font-size:20px; text-align:left;"> Već odabrana lokacija: <?php echo "<b>$kandidat_destinacija_grad, $kandidat_destinacija_postanski_broj</b>";?></p>
                                            
                                                                                <?php
                                                                            }
                                                                        ?>
                                                                <table id="grad_i_plz_table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th class="text-center">Pretraga</th>   
                                                                            <th class="text-center">Grad</th>
                                                                            <th class="text-center">Poštanski broj</th>
                                                                            <th class="text-center"></th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td>
                                                                                <input id = "open_plz_search_term" style = "height: 38px" type=text></input>
                                                                                <a id = "open_plz_search" style = "vertical-align: baseline!important;" class="btn material-btn material-btn_success main-container__column"><i class="fa fa-search" aria-hidden="true"></i></a>
                                                                            </td>
                                                                            <td class = "hideables" style="visibility:hidden">
                                                                                <select class="selectpicker" id="select_city" data-live-search="true" data-actions-box="true" style="display:none;"></select>
                                                                            </td>
                                                                            <td class = "hideables" style="visibility:hidden">
                                                                                <select class="selectpicker" id="select_plz" data-live-search="true" data-actions-box="true" style="display:none;"></select>
                                                                            </td>
                                                                            <td class="text-center"><a id = "update_city_and_plz" class="btn material-btn material-btn_success main-container__column"><i class="fa fa-check" aria-hidden="true"></i></a></td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                                <script>
                                                                    $(document).ready(function() {
                                                                        var global_data;
                                                                        $("#select_city").on('change', function(){
                                                                            $('#select_plz').empty();
                                                                            $('#select_plz').selectpicker('refresh');
                                                                            var city = $(this).val();
                                                                            var i = -1;
                                                                            do{
                                                                                i++;
                                                                                if(city == global_data[i]['name']){
                                                                                    
                                                                                    var plzs = global_data[i]['plz'];
                                                                                    plzs.forEach((plz) => {
                                                                                        $('#select_plz').append('<option value = "'+plz+'">'+plz+'</option>')
                                                                                    });
                                                                                    $('#select_plz').selectpicker('refresh');
                                                                                }
                                                                            }while(city != global_data[i]['name']);
                                                                        });
                                                                        $("#open_plz_search").on('click', function(){
                                                                            var search_term = $("#open_plz_search_term").val();
                                                                            
                                                                            if(search_term == "" || search_term.length < 3){
                                                                                $("#open_plz_search_term").effect('shake');
                                                                            }
                                                                            else{
                                                                                $('.hideables').css('visibility', 'hidden');
                                                                                    $('#to_append_to_select_city').empty();
                                                                                    $.ajax({
                                                                                        url: 'ajax_data.php?page=open_plz_search',
                                                                                        type: 'POST',    
                                                                                        data: {
                                                                                            'search_term':search_term
                                                                                        },
                                                                                        dataType: 'json',
                                                                                        success: function(data) {
                                                                                            global_data = data;
                                                                                            
                                                                                            $('#select_city').empty().selectpicker('refresh');
                                                                                            $('#select_plz').empty().selectpicker('refresh');;
                                                                                        
                                                                                            var isFirst = true;
                                                                                            data.forEach((city) => {
                                                                                                $('#select_city').append('<option value = "'+city['name']+'">'+city['name']+'</option>');
                                                                                                if(isFirst){
                                                                                                    var plzs = city['plz'];
                                                                                                    isFirst = false;
                                                                                                    plzs.forEach((plz) => {
                                                                                                        $('#select_plz').append('<option value = "'+plz+'">'+plz+'</option>')
                                                                                                    });
                                                                                                    $('#select_plz').selectpicker('refresh');
                                                                                                }
                                                                                            });
                                                                                                
                                                                                            $('#select_city').selectpicker('refresh');
                                                                                            $('#select_plz').selectpicker('refresh');
                                                                                            $('.hideables').css('visibility', 'visible');
                                                                                            
                                                                                        }
                                                                                    });
                                                                                
                                                                            }
                                                                        });

                                                                        $("#grad_i_plz_table").DataTable({
                                                                            responsive: true,
                                                                            searching: false,
                                                                            paging: false,
                                                                            "ordering": false,
                                                                            "info":     false,
                                                                            "bAutoWidth": false,
                                                                            "aoColumns": [
                                                                                { "width": "35%" },
                                                                                { "width": "30%" },
                                                                                { "width": "30%" },
                                                                                { "width": "5%" }
                                                                            ]
                                                                        });
                                                                        $("#update_city_and_plz").on('click', function(){
                                                                            var selected_city = $("#select_city").val();
                                                                            var plz = $("#select_plz").val();
                                                                            var candidate_id = $("#candidate_id").text();
                                                                            if(selected_city == null || plz == ""){
                                                                                $(this).effect('shake');
                                                                            }
                                                                            else{
                                                                                $.ajax({
                                                                                    url: 'ajax_data.php?page=update_city_and_plz',
                                                                                    type: 'POST',    
                                                                                    data: {
                                                                                        'selected_city':selected_city, 
                                                                                        'candidate_id':candidate_id,
                                                                                        'plz':plz
                                                                                    },
                                                                                    dataType: 'html',
                                                                                    success: function(data) {
                                                                                        alert("Grad i Postanski broj uspješno updatovani");
                                                                                        location.reload();
                                                                                    }
                                                                                });
                                                                            }
                                                                        });
                                                                    });
                                                                </script>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                                <hr />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php 
                            }
                        /* DOBIO VIZU END */

                        /* POČETAK RADA START */
                            if($status_prijave_id==4 or $status_prijave_id==10){
                                
                                $log_datum_pocetak_rada_query= $db->prepare("
                                                    SELECT lsp_datetime
                                                    FROM idk_log_statusi_prijave
                                                    WHERE lsp_kandidat_id = :lsp_kandidat_id AND lsp_projekt_id in (".implode(",",$projekti_kandidata).") AND lsp_status_prijave_id = 10
                                ");
                                $log_datum_pocetak_rada_query->execute(array(
                                    ':lsp_kandidat_id' => $kandidat_id
                                ));				
                                $log_datum_pocetak_rada_row=$log_datum_pocetak_rada_query->fetch();
                                $log_datum_pocetak_rada=date("d.m.Y", strtotime($log_datum_pocetak_rada_row['lsp_datetime']));
                                $datum_pocetak_rada_query = $db ->prepare("SELECT kandidat_dogovoreni_pocetak_rada FROM idk_kandidati WHERE kandidat_id=:kandidat_id;");
                                $datum_pocetak_rada_query->execute(array(
                                    'kandidat_id' => $kandidat_id
                                    ));
                                $datum_pocetak_rada_row=$datum_pocetak_rada_query->fetch();
                                if(isset($datum_pocetak_rada_row["kandidat_dogovoreni_pocetak_rada"])){
                                    $kandidat_dogovoreni_pocetak_rada   = date("d.m.Y", strtotime($datum_pocetak_rada_row["kandidat_dogovoreni_pocetak_rada"]));     
                                }
                                ?>	
                                <div class="panel-group material-accordion material-accordion_success" id="pocetRad">
                                    <div class="panel panel-default material-accordion__panel "  >
                                        <div class="panel-heading material-accordion__heading">
                                            <h4 class="panel-title">
                                                <a class="material-accordion__title" data-toggle="collapse" data-parent="#pocetRad"  href="#status4">Početak rada <span style="float: right;"><?php echo $log_datum_pocetak_rada; ?></span></a>
                                            </h4>
                                        </div>
                                           <!-- MODAL POČETAK RADA START -->
                                                <div class="modal material-modal material-modal_primary fade text-left" id="konacni_početak_rada_m">
                                                        <div class="modal-dialog ">
                                                            <div class="modal-content material-modal__content">
                                                                <div class="modal-header material-modal__header">
                                                                    <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                                                    <h4 class="modal-title material-modal__title">Unos početka rada</h4>
                                                                </div>
                                                                <div class="modal-body material-modal__body">
                                                                    <form action="<?php getSiteURL();?>do.php?form=unesi_datum_pocetak_rada" method="post" role="form" class="form-horizontal">
                                                                        <div class="form-group col-sm-12 text-right">
                                                                            <input type="hidden" name="kandidat_id" value="<?php echo $kandidat_id; ?>">
                                                                            <label for="datum_pocetak_rada" class="col-sm-5 control-label">Unesi konačni datum početka rada:</label>
                                                                            <div class="col-sm-6">
                                                                                <div class="materail-input-block materail-input-block_success">
                                                                                    
                                                                                    <input class="form-control materail-input" type="text" name="datum_pocetak_rada" autocomplete="off" id="konacni_datum_pocetak_rada"  >
                                                                                    <span class="materail-input-block__line"></span>
                                                                                </div>
                                                                            </div>
                                                                            <script>
                                                                                $(function() {
                                                                                    initDateSelectKPC();//Konacni pocetak rada 
                                                                                });

                                                                                function initDateSelectKPC() {
                                                                                    $("#konacni_datum_pocetak_rada").flatpickr({
                                                                                        minDate: "2000-01-01"
                                                                                    });
                                                                                }
                                                                            </script>
                                                                        </div>
                                                                       
                                                                </div>
                                                                <div class="modal-footer material-modal__footer">
                                                                        <button type="submit" class="btn btn-primary material-btn material-btn_success"><i class="fa fa-edit" aria-hidden="true"></i> Spremi</button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                            <!-- MODAL POČETAK RADA END -->
                                        <div id="status4" class="panel-collapse <?php if($status_prijave_id==10){ echo "in"; } ?> collapse material-accordion__collapse">
                                            <div class="panel-body">
                                                <div class="row">
                                                    <div class="col-sm-3"></div>
                                                    <div class="col-sm-6 text-right">
                                                        <table id="datum_pocetka_rada_table">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-center">Datum početka rada:</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td class="text-center"><?php if(isset($kandidat_dogovoreni_pocetak_rada)){echo $kandidat_dogovoreni_pocetak_rada;}else{ 
                                                                        ?>
                                                                            <a href="#" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" data-toggle="modal" data-target="#konacni_početak_rada_m">
                                                                                <i class="fa fa-plus" aria-hidden="true"></i>
                                                                                <span>Unesi datum početka rada</span>
                                                                            </a>
                                                                        <?php
                                                                    } ?></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                        <script>
                                                            $(document).ready(function() {
                                                                $("#datum_pocetka_rada_table").DataTable({
                                                                    responsive: true,
                                                                    searching: false,
                                                                    paging: false,
                                                                    "ordering": false,
                                                                    "info":     false,
                                                                    "bAutoWidth": false
                                                                });
                                                            });
                                                        </script>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php 
                            }
                        /* POČETAK RADA END */

                        /* ZAPOSLEN START */
                            if($status_prijave_id==4){
                                
                                $datum_zaposlen_query= $db->prepare("
                                                SELECT lsp_datetime
                                                FROM idk_log_statusi_prijave
                                                WHERE lsp_kandidat_id = :lsp_kandidat_id AND lsp_projekt_id in (".implode(",",$projekti_kandidata).") AND lsp_status_prijave_id = 4
                                ");

                                $datum_zaposlen_query->execute(array(
                                    ':lsp_kandidat_id' => $kandidat_id
                                ));				
                                $datum_zaposlen_row=$datum_zaposlen_query->fetch();
                                $datum_zaposlen=date("d.m.Y", strtotime($datum_zaposlen_row['lsp_datetime']));
                                ?>	
                                <div class="panel-group material-accordion material-accordion_success" id="zaposlen">
                                    <div class="panel panel-default material-accordion__panel "  >
                                        <div class="panel-heading material-accordion__heading">
                                            <h4 class="panel-title">
                                                <a class="material-accordion__title" data-toggle="collapse" data-parent="#zaposlen"  href="#status5">Zaposlen 
                                                    <span style="float: right;"><?php echo $datum_zaposlen; ?></span>
                                                </a>
                                            </h4>
                                        </div>
                                        
                                        <div id="status5" class="panel-collapse <?php if($status_prijave_id==4){ echo "in"; } ?> collapse material-accordion__collapse">
                                            <div class="panel-body">
                                                <div class="row">
                                                    <div class="col-sm-10">
                                                        <p></p>
                                                    </div>
                                                </div>
                                                <hr />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php 
                            }?>
                        <!--  ZAPOSLEN END -->
                                
                    </div>
                <!-- STATUSI EXPANDERI END -->
            </div>
        </div>
    </div>

<!-- PROCES ODLASKA PANEL - END -->

