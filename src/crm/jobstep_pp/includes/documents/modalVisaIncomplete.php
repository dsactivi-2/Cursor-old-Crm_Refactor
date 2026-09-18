<!-- 
    *****************************************************************
    *	Modali za određene akcije na listi nepotpuna viza START		*
    *****************************************************************
-->
     <!-- 
        Modal za dodavanje novog dokumenta dokumenata START
    -->
    <script>
       function documentAddModalSetVI(thisRow) {
            //Ovdje ce ici provjera za remindere nekad kad budu bili 
            var crd_id = $(thisRow).data("crd_id");
            var doc_type_id = $(thisRow).data("doc_type_id");
            var candidat_id = $(thisRow).data("candidat_id");
            var nalog_id = $(thisRow).data("nalog_id");
            var vi_id = $(thisRow).data("vi_id");
            //alert(nrd_id+ " " + doc_type_id + " " + candidat_id + " " + nalog_id);
            $("#candidat_id_DA").val(candidat_id);
            $("#nalog_id_DA").val(nalog_id);
            $("#crd_id_DA").val(crd_id);
            $("#doc_type_id_DA").val(doc_type_id);
            $("#vi_id_DA").val(vi_id);
        };
    </script>
    <div class="modal fade" id="documentAddVI" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="documentAddVILabel" tabindex="-1" style = "background-color: rgb(255 255 255 / 50%);">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header border-bottom-0 text-center">
                    <h5 class="modal-title w-100" id="documentAddVILabel"><?php echo $txtArray["Izvršite upload skeniranog dokumenta"][$languageUser]; ?></h5>
                    <!--<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>-->
                </div>
                <div class="modal-body">
                    <div class = "row pt-1 my-3">
                        <div class = "col-12 text-center">
                            <input type="hidden" id="candidat_id_DA">
                            <input type="hidden" id="nalog_id_DA">
                            <input type="hidden" id="crd_id_DA" >
                            <input type="hidden" id="doc_type_id_DA" >
                            <input type="hidden" id="vi_id_DA" >
                            <div class="mb-3 mx-5 row">
                                <div class="col-sm-12">
                                    <div class="input-group">
                                        <input type="file" class="form-control" id = "file_DA" aria-label="Upload" placeholder="Upload" required>
                                        <button class="btn btn-danger" type="button" onclick="resetDocumentAddVI()">Reset</button>
                                    </div>
                                </div>
                            </div>
                            <div class = "row mx-5 mb-3 alertOtherSize_DA visually-hidden">
                                <div class = "col-sm-12">
                                    <div class="alert alert-danger text-center" role="alert">
                                        <?php echo $txtArray["Dokument koji pokuštavate dodati je veći od dozvoljene veličine!"][$languageUser]; ?>
                                    </div>
                                </div>
                            </div>
                            <div class = "row mx-5 mb-3 alertOtherFormat_DA visually-hidden">
                                <div class = "col-sm-12">
                                    <div class="alert alert-danger text-center" role="alert">
                                        <?php echo $txtArray["Format dokumenta koji pokušavate dodati nije dozvoljen!<br>Koristite format '.pdf', '.doc' ili '.docx'."][$languageUser]; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3 mx-5 row">
                                <div class="col-sm-12">
                                    <textarea class="textArreaStyle form-control" id="comment_DA" rows="3" placeholder="<?php echo $txtArray["Unesite vaš komentar ovdje..."][$languageUser]; ?>"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center border-top-0">
                    <button type="button" class="btn btn-secondary" onclick="closeDocumentAddVI()"><?php echo $txtArray["Odustani"][$languageUser]; ?></button>
                    <button class="btn btn-primary saveDocVI" onclick="saveDocumentAddVI()" id = "" ><?php echo $txtArray["Završi"][$languageUser]; ?></button>
                </div>
            </div>
        </div>
    </div>
    <script>
        function resetDocumentAddVI(){
            var file_DA = $("#file_DA").val();
            if(file_DA !== ""){
                $("#file_DA").val(null); 
            }
        };
        function closeDocumentAddVI(){
            $("#comment_DA").val(null);
            $("#file_DA").val(null);
            setTimeout(function(){
                $("#documentAddVI").modal("hide");
            }, 500);
        };
        function saveDocumentButtonEnableDisableVI(vr){
            var vrSAD = parseInt(vr);
            if(vrSAD == 1){
                $(".saveDocVI").prop('disabled', true);
            }else{
                $(".saveDocVI").prop('disabled', false);
            }
            //console.log(vrSAD);
        };
        $(function (){
            $('#file_DA').change(function (){
                if($('#file_DA').val() !== ""){
                    
                    var extOtherDocACFC = $('#file_DA').val().split('.').pop().toLowerCase();

                    if($.inArray(extOtherDocACFC, ['pdf','doc','docx']) == -1) {
                        $('.alertOtherFormat_DA').removeClass('visually-hidden');
                        this.value = null;
                        setTimeout(function(){
                                $('.alertOtherFormat_DA').addClass('visually-hidden');
                            }, 5000
                        );
                    }else{
                        $('.alertOtherFormat_DA').addClass('visually-hidden');
                    }
                }
                if($('#file_DA').val() !== ""){
                    var sizeOtherDocACFC = this.files[0];

                    if(sizeOtherDocACFC.size > 5242880 || sizeOtherDocACFC.fileSize > 5242880){
                        $('.alertOtherSize_DA').removeClass('visually-hidden');
                        this.value = null;
                        setTimeout(function(){
                                $('.alertOtherSize_DA').addClass('visually-hidden');
                            }, 5000
                        );
                    }else{
                        $('.alertOtherSize_DA').addClass('visually-hidden');
                    }
                }
            })
        });
        function saveDocumentAddVI(){
            saveDocumentButtonEnableDisableVI(1);
            var candidat_id_DA = parseInt($("#candidat_id_DA").val());
            var nalog_id_DA = parseInt($("#nalog_id_DA").val());
            var crd_id_DA = parseInt($("#crd_id_DA").val());
            var doc_type_id_DA = parseInt($("#doc_type_id_DA").val());
            var vi_id_DA = parseInt($("#vi_id_DA").val());
            var comment_DA = $("#comment_DA").val();
            var file_DA_check = $("#file_DA").val();
            var file_DA = $('#file_DA').prop('files')[0];
            var user_id_DA = parseInt('<?php echo $userId; ?>');
            //console.log(candidat_id_DA + " " + nalog_id_DA + " " + nrd_id_DA + " " + doc_type_id_DA + " " + comment_DA + " " + file_DA + " ");
            var form_data = new FormData();

            form_data.append('document', file_DA);
            form_data.append('kandidat_id', candidat_id_DA);
            form_data.append('nalog_id', nalog_id_DA);
            form_data.append('status_comment', comment_DA);
            form_data.append('crd_id', crd_id_DA);
            form_data.append('pp_id', user_id_DA);
            form_data.append('new_status_id', 3);
            

            if(candidat_id_DA != 0 && nalog_id_DA != 0 && crd_id_DA != 0 && doc_type_id_DA && vi_id_DA != 0 && file_DA_check != ""){
                //console.log("Sve popunjeno!");

                $.ajax({
                    url: '<?php getCRMUrl(); ?>do.php?form=changeDocumentStatus',
                    type: 'POST',
                    processData: false,
                    contentType: false,
                    data:form_data,
                    success : function (){
                        //console.log($.fn.DataTable.isDataTable('#requiredDocuments'));
                        getRequiredDocumentsVI(nalog_id_DA, candidat_id_DA, vi_id_DA);
                        saveDocumentButtonEnableDisableVI(0);
                        closeDocumentAddVI();
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(xhr.status);
                        alert(thrownError);
                    }
                });
            }else{
                saveDocumentButtonEnableDisableVI(0);
            }
            //alert(candidat_id_DA + " " + nalog_id_DA + " " + nrd_id_DA + " " + doc_type_id_DA + " " + comment_DA + " " + file_DA + " ");
        };
    </script>
    <!-- 
        Modal za dodavanje novog dokumenta dokumenata END
    -->

    <!-- 
        Modal za pregled log statusa dokumenta START
    -->
    <script>
       function documentDetailsVI(thisRow) {
            //Ovdje ce ici provjera za remindere nekad kad budu bili 
            var crd_id = parseInt($(thisRow).data("crd_id"));
            var doc_id = parseInt($(thisRow).data("doc_id"));
            var candidat_id = parseInt($(thisRow).data("candidat_id"));
            var nalog_id = parseInt($(thisRow).data("nalog_id"));
            var vi_id = parseInt($(thisRow).data("vi_id"));
            
            //console.log(nrd_id+ " " + doc_id + " " + candidat_id + " " + nalog_id);

            if(crd_id != 0 && doc_id != 0 && candidat_id != 0 && nalog_id != 0  && vi_id != 0){
                $.ajax({
                    url: 'ajax.php?action=documentDetailsVisaIncomplete',
                    type: 'POST',
                    dataType: 'html',
                    data:{
                        'crd_id': crd_id,
                        'doc_id': doc_id,
                        'candidat_id': candidat_id,
                        'nalog_id': nalog_id,
                        'vi_id': vi_id
                    },
                    success : function (result){
                        $(".documentDetailsBodyVI").html(result);
                        enablePopoversAndTooltipsVI();
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(xhr.status);
                        alert(thrownError);
                    }
                });
            }
            
        };
    </script>
    <div class="modal fade scrollBarVertical" id="documentDetailsVI" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="documentDetailsVILabel" tabindex="-1" style = "background-color: rgb(255 255 255 / 50%);">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header border-bottom-0 text-center">
                    <h5 class="modal-title w-100" id="documentDetailsVILabel"><?php echo $txtArray["Detalji dokumenta"][$languageUser]; ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 py-4">
                    <div class = "row">
                        <div class = "col-md-12 text-center documentDetailsBodyVI">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- 
        Modal za pregled log statusa dokumenta END
    -->


    <!-- 
        Modal za označavanje dokumenata da su poslani START
    -->
    <script>
        function sendDocumentsInfoVI(thisRow){
            var candidate_id = parseInt($(thisRow).data("candidate_id"));
            var nalog_id = parseInt($(thisRow).data("nalog_id"));
            var viId = parseInt($(thisRow).data("vi_id"));
            $.ajax({
                url: 'ajax.php?action=readyToSendIdsVisaIncomplete',
                type: 'POST',
                dataType: 'html',
                data:{
                    'idCandidate': candidate_id,
                    'idNalog': nalog_id,
                    'idVi': viId
                },
                success : function (result){
                    $(".readyToSendIdsVI").html(result);
                    $("#candidate_id_SD").val(candidate_id);
                    $("#nalog_id_SD").val(nalog_id);
                    $("#vi_id_SD").val(viId);
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                }
            });
        }
    </script>
    <div class="modal fade scrollBarVertical" id="sendDocumentsVI" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="sendDocumentsVILabel" tabindex="-1" style = "background-color: rgb(255 255 255 / 50%);">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header border-bottom-0 text-center">
                    <h5 class="modal-title w-100" id="sendDocumentsVILabel"><?php echo $txtArray["Pošalji dokumente"][$languageUser]; ?></h5>
                    <!--<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>-->
                </div>
                <div class="modal-body px-4 py-4">
                    <div class = "row">
                        <div class = "col-md-12 ">
                            <input type="hidden" id="candidate_id_SD">
                            <input type="hidden" id="nalog_id_SD">
                            <input type="hidden" id="vi_id_SD">
                            <div class = "row mx-5 mb-3">
                                <div class = "col-sm-12">
                                    <div class="alert alert-warning text-center" role="alert">
                                        <i class="fa fa-exclamation-circle fa-5x" aria-hidden="true"></i>
                                        <hr>
                                        <div><?php echo $txtArray["Akcija služi da bi se označilo da su originalni dokumenti poslani!"][$languageUser]; ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class = "row mx-5 mb-3 alertDocumentsUnavailable visually-hidden">
                                <div class = "col-sm-12">
                                    <div class="alert alert-danger text-center" role="alert">
                                        <?php echo $txtArray["Dokumenti za slanje su nedostupni! Osvježite stranicu!"][$languageUser]; ?>
                                    </div>
                                </div>
                            </div>
                            <div class = "row mx-5 mb-3">
                                <div class = "col-sm-12 readyToSendIdsVI">
                                </div>
                            </div>
                            <div class="mb-3 mx-5 row">
                                <div class="col-sm-12">
                                    <textarea class="textArreaStyle form-control" id="comment_SD" rows="3" placeholder="<?php echo $txtArray["Unesite vaš komentar ovdje..."][$languageUser]; ?>"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center border-top-0">
                    <button type="button" class="btn btn-secondary" onclick="closeSendDocumentsVI()"><?php echo $txtArray["Odustani"][$languageUser]; ?></button>
                    <button class="btn btn-primary sendDocVi" onclick="sendDocumentsVI()" id = "" ><?php echo $txtArray["Završi"][$languageUser]; ?></button>
                </div>
            </div>
        </div>
    </div>
    <script>
        function closeSendDocumentsVI(){
            $(".readyToSendIdsVI").html("");
            $("#comment_SD").html("");
            setTimeout(function(){
                $("#sendDocumentsVI").modal("hide");
            }, 500);
        };
        function sendDocumentsButtonEnableDisableVI(vr){
            var vrSD = parseInt(vr);
            if(vrSD == 1){
                $(".sendDocVi").prop('disabled', true);
            }else{
                $(".sendDocVi").prop('disabled', false);
            }
        };
        function sendDocumentsVI(){
            sendDocumentsButtonEnableDisableVI(1);
            var candidate_id_SD = parseInt($("#candidate_id_SD").val());
            var nalog_id_SD = parseInt($("#nalog_id_SD").val());
            var vi_id_SD = parseInt($("#vi_id_SD").val());
            var comment_SD = $("#comment_SD").val();
            var user_id_SD = parseInt('<?php echo $userId; ?>');
            var doc_ids_SD = new Array();
            //console.log($("input").hasClass(".doc_ids_SD"));
            $(".doc_ids_SD").each(function() {
                //console.log("DA");
                doc_ids_SD.push($(this).val());
            });
            //console.log(doc_ids_SD);
            if(doc_ids_SD.length != 0){
                var form_data = new FormData();
                form_data.append('kandidat_id', candidate_id_SD);
                form_data.append('nalog_id', nalog_id_SD);
                form_data.append('doc_ids', doc_ids_SD);
                form_data.append('pp_id', user_id_SD);
                form_data.append('status_comment', comment_SD);
                //console.log(form_data);
                $.ajax({
                    url: '<?php getCRMUrl(); ?>do.php?form=poslodavacPoslaoDokumente',
                    type: 'POST',
                    processData: false,
                    contentType: false,
                    data:form_data,
                    success : function (){
                        getRequiredDocumentsVI(nalog_id_SD, candidate_id_SD, vi_id_SD);
                        sendDocumentsButtonEnableDisableVI(0);
                        closeSendDocumentsVI();
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(xhr.status);
                        alert(thrownError);
                    }
                });
            }else{
                $('.alertDocumentsUnavailable').removeClass('visually-hidden');
                setTimeout(function(){
                    $('.alertDocumentsUnavailable').addClass('visually-hidden');
                    sendDocumentsButtonEnableDisableVI(0);
                }, 5000 );
            }
            //console.log(candidate_id_SD+" "+nalog_id_SD+" "+doc_ids_SD);
            //Ovdje nastaviti ujutro
        }
    </script>
    <!-- 
        Modal za označavanje dokumenata da su poslani END 
    -->


    <!-- 
        Modal za upload novog dokumenta koji nije prosao provjeru START 
    -->
    <script>
       function newDocumentAddModalSetVI(thisRow) {
            //Ovdje ce ici provjera za remindere nekad kad budu bili 
            var crd_id = $(thisRow).data("crd_id");
            var doc_type_id = $(thisRow).data("doc_type_id");
            var candidat_id = $(thisRow).data("candidat_id");
            var nalog_id = $(thisRow).data("nalog_id");
            var vi_id = $(thisRow).data("vi_id");
            //alert(nrd_id+ " " + doc_type_id + " " + candidat_id + " " + nalog_id);
            $("#candidat_id_NDA").val(candidat_id);
            $("#nalog_id_NDA").val(nalog_id);
            $("#crd_id_NDA").val(crd_id);
            $("#doc_type_id_NDA").val(doc_type_id);
            $("#vi_id_NDA").val(vi_id);
        };
    </script>
    <div class="modal fade" id="newDocumentAddVI" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="newDocumentAddVILabel" tabindex="-1" style = "background-color: rgb(255 255 255 / 50%);">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header border-bottom-0 text-center">
                    <h5 class="modal-title w-100" id="newDocumentAddVILabel"><?php echo $txtArray["Izvršite upload ispravke dokumenta"][$languageUser]; ?></h5>
                    <!--<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>-->
                </div>
                <div class="modal-body">
                    <div class = "row pt-1 my-3">
                        <div class = "col-12 text-center">
                            <input type="hidden" id="candidat_id_NDA">
                            <input type="hidden" id="nalog_id_NDA">
                            <input type="hidden" id="crd_id_NDA" >
                            <input type="hidden" id="doc_type_id_NDA" >
                            <input type="hidden" id="vi_id_NDA" >
                            <div class="mb-3 mx-5 row">
                                <div class="col-sm-12">
                                    <div class="input-group">
                                        <input type="file" class="form-control" id = "file_NDA" aria-label="Upload" placeholder="Upload" required>
                                        <button class="btn btn-danger" type="button" onclick="resetNewDocumentAddVI()">Reset</button>
                                    </div>
                                </div>
                            </div>
                            <div class = "row mx-5 mb-3 alertOtherSize_NDA visually-hidden">
                                <div class = "col-sm-12">
                                    <div class="alert alert-danger text-center" role="alert">
                                        <?php echo $txtArray["Dokument koji pokuštavate dodati je veći od dozvoljene veličine!"][$languageUser]; ?>
                                    </div>
                                </div>
                            </div>
                            <div class = "row mx-5 mb-3 alertOtherFormat_NDA visually-hidden">
                                <div class = "col-sm-12">
                                    <div class="alert alert-danger text-center" role="alert">
                                        <?php echo $txtArray["Format dokumenta koji pokušavate dodati nije dozvoljen!<br>Koristite format '.pdf', '.doc' ili '.docx'."][$languageUser]; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3 mx-5 row">
                                <div class="col-sm-12">
                                    <textarea class="textArreaStyle form-control" id="comment_NDA" rows="3" placeholder="<?php echo $txtArray["Unesite vaš komentar ovdje..."][$languageUser]; ?>"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center border-top-0">
                    <button type="button" class="btn btn-secondary" onclick="closeNewDocumentAddVI()"><?php echo $txtArray["Odustani"][$languageUser]; ?></button>
                    <button class="btn btn-primary saveNewDocVI" onclick="saveNewDocumentAddVI()" id = "" ><?php echo $txtArray["Završi"][$languageUser]; ?></button>
                </div>
            </div>
        </div>
    </div>
    <script>
        function resetNewDocumentAddVI(){
            var file_NDA = $("#file_NDA").val();
            if(file_NDA !== ""){
                $("#file_NDA").val(null); 
            }
        };
        function closeNewDocumentAddVI(){
            $("#comment_NDA").val(null);
            $("#file_NDA").val(null);
            setTimeout(function(){
                $("#newDocumentAddVI").modal("hide");
            }, 500);
        };
        function saveNewDocumentButtonEnableDisableVI(vr){
            var vrSND = parseInt(vr);
            if(vrSND == 1){
                $(".saveNewDocVI").prop('disabled', true);
            }else{
                $(".saveNewDocVI").prop('disabled', false);
            }
        };
        $(function (){
            $('#file_NDA').change(function (){
                if($('#file_NDA').val() !== ""){
                    
                    var extOtherDocACFC = $('#file_NDA').val().split('.').pop().toLowerCase();

                    if($.inArray(extOtherDocACFC, ['pdf','doc','docx']) == -1) {
                        $('.alertOtherFormat_NDA').removeClass('visually-hidden');
                        this.value = null;
                        setTimeout(function(){
                                $('.alertOtherFormat_NDA').addClass('visually-hidden');
                            }, 5000
                        );
                    }else{
                        $('.alertOtherFormat_NDA').addClass('visually-hidden');
                    }
                }
                if($('#file_NDA').val() !== ""){
                    var sizeOtherDocACFC = this.files[0];

                    if(sizeOtherDocACFC.size > 5242880 || sizeOtherDocACFC.fileSize > 5242880){
                        $('.alertOtherSize_NDA').removeClass('visually-hidden');
                        this.value = null;
                        setTimeout(function(){
                                $('.alertOtherSize_NDA').addClass('visually-hidden');
                            }, 5000
                        );
                    }else{
                        $('.alertOtherSize_NDA').addClass('visually-hidden');
                    }
                }
            })
        });
        function saveNewDocumentAddVI(){
            saveNewDocumentButtonEnableDisableVI(1);
            var candidat_id_NDA = parseInt($("#candidat_id_NDA").val());
            var nalog_id_NDA = parseInt($("#nalog_id_NDA").val());
            var crd_id_NDA = parseInt($("#crd_id_NDA").val());
            var doc_type_id_NDA = parseInt($("#doc_type_id_NDA").val());
            var vi_id_NDA = parseInt($("#vi_id_NDA").val());
            var comment_NDA = $("#comment_NDA").val();
            var file_NDA_check = $("#file_NDA").val();
            var file_NDA = $('#file_NDA').prop('files')[0];
            var user_id_NDA = parseInt('<?php echo $userId; ?>');
            //console.log(candidat_id_DA + " " + nalog_id_DA + " " + nrd_id_DA + " " + doc_type_id_DA + " " + comment_DA + " " + file_DA + " ");
            var form_data = new FormData();

            form_data.append('document', file_NDA);
            form_data.append('kandidat_id', candidat_id_NDA);
            form_data.append('nalog_id', nalog_id_NDA);
            form_data.append('status_comment', comment_NDA);
            form_data.append('crd_id', crd_id_NDA);
            form_data.append('pp_id', user_id_NDA);
            form_data.append('new_status_id', 3);
            

            if(candidat_id_NDA != 0 && nalog_id_NDA != 0 && crd_id_NDA != 0 && doc_type_id_NDA != 0 && vi_id_NDA != 0 && file_NDA_check != ""){
                //console.log("Sve popunjeno!");

                $.ajax({
                    url: '<?php getCRMUrl(); ?>do.php?form=changeDocumentStatus',
                    type: 'POST',
                    processData: false,
                    contentType: false,
                    data:form_data,
                    success : function (){
                        //console.log($.fn.DataTable.isDataTable('#requiredDocuments'));
                        getRequiredDocumentsVI(nalog_id_NDA, candidat_id_NDA, vi_id_NDA);
                        saveNewDocumentButtonEnableDisableVI(0);
                        closeNewDocumentAddVI();
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(xhr.status);
                        alert(thrownError);
                    }
                });
            }else{
                //console.log("Nije popunjeno!");
                saveNewDocumentButtonEnableDisableVI(0);
            }
            //alert(candidat_id_DA + " " + nalog_id_DA + " " + nrd_id_DA + " " + doc_type_id_DA + " " + comment_DA + " " + file_DA + " ");
        };
    </script>
    <!-- 
        Modal za upload novog dokumenta koji nije prosao provjeru END 
    -->

<!-- 
    *****************************************************************
    *	Modali za određene akcije na listi nepotpuna viza END		*
    *****************************************************************
-->