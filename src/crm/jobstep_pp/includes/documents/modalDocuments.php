<!-- 
    *************************************************************
    *	Modali za određene akcije na listi dokumenata START		*
    *************************************************************
-->
    
    <!-- 
        Modal za dodavanje novog dokumenta dokumenata START
    -->
    <script>
       function documentAddModalSet(thisRow) {
            //Ovdje ce ici provjera za remindere nekad kad budu bili 
            var flagEnpalAccess = parseInt("<?php echo $flagEnpalAccess; ?>");
            var nrd_id = $(thisRow).data("nrd_id");
            var doc_type_id = $(thisRow).data("doc_type_id");
            var candidat_id = $(thisRow).data("candidat_id");
            var nalog_id = $(thisRow).data("nalog_id");
            var candidat_partner_id = $(thisRow).data("candidat_partner_id");
            var candidat_key = $(thisRow).data("candidat_key");
            //alert(nrd_id+ " " + doc_type_id + " " + candidat_id + " " + nalog_id);
            $("#candidat_id_DA").val(candidat_id);
            $("#nalog_id_DA").val(nalog_id);
            $("#nrd_id_DA").val(nrd_id);
            $("#doc_type_id_DA").val(doc_type_id);
            $("#candidat_partner_id_DA").val(candidat_partner_id);
            $("#candidat_key_DA").val(candidat_key);

            if(doc_type_id == 14){

                if (flagEnpalAccess == 1) {
                    $("#documentAdd").modal("show");
                } else {
                    getAccessControl(nalog_id, candidat_key, candidat_partner_id, 9, function (responseAddDocuments){
                        var reminderStatus = responseAddDocuments['status'];
                        var reminderId = responseAddDocuments['id'];
                        var reminderAssignedUser = responseAddDocuments['assigned'];
                        var reminderIsLoggedUser = responseAddDocuments['isLogged'];
                        var reminderResponseMessage = responseAddDocuments['message'];
                        var reminderResponseTitle = responseAddDocuments['title'];

                        if(reminderStatus == 101 || reminderStatus == 1 || (reminderStatus == 2 && reminderIsLoggedUser == 1)){
                            
                            $("#documentAdd").modal("show");

                            if(reminderStatus == 1){
                                updateReminderStatus(reminderId, 2, function(responseUpdateAddDocuments){
                                    var updateStatus 	= responseUpdateAddDocuments["status"];
                                    var updateMessage 	= responseUpdateAddDocuments["message"];
                                    if(updateStatus == 2){
                                        showToast(updateMessage, reminderResponseTitle);
                                    }
                                });
                            }
                        }else if (reminderStatus == 104){
                            showToast(reminderResponseMessage, reminderResponseTitle);
                        }else{
                            showToast(reminderResponseMessage, reminderResponseTitle);
                        }
                    });
                }
            }else{
                if (flagEnpalAccess == 1) {
                    $("#documentAdd").modal("show");
                } else {
                    getAccessControl(nalog_id, candidat_key, candidat_partner_id, 11, function (responseAccessControl) { 
                        var reminderStatus              = responseAccessControl['status']; 
                        var reminderId                  = responseAccessControl['id']; 
                        var reminderAssignedUser        = responseAccessControl['assigned']; 
                        var reminderIsLoggedUser        = responseAccessControl['isLogged']; 
                        var reminderResponseMessage     = responseAccessControl['message']; 
                        var reminderResponseTitle       = responseAccessControl['title']; 
                        // console.log(reminderStatus+"-"+reminderId+"-"+reminderAssignedUser+"-"+reminderIsLoggedUser+"-"+reminderResponseMessage+"-"+reminderResponseTitle);
                        //alert(nalog_id_DA+"-"+candidat_key_DA+"-"+candidat_partner_id_DA+"-"+nrd_id_DA); 
                            if(reminderStatus == 101 || reminderStatus == 1 || (reminderStatus == 2 && reminderIsLoggedUser == 1)){
                            $("#documentAdd").modal("show");
                            
                            if(reminderStatus == 1){ 
                                updateReminderStatus(reminderId, 2, function (responseUpdateReminder){ 
                                    var updateStatus       = responseUpdateReminder["status"]; 
                                    var updateMessage      = responseUpdateReminder["message"]; 
                                    if(updateStatus == 2){ 
                                        showToast(updateMessage, reminderResponseTitle); 
                                    } 
                                }); 
                            } 
                        }else if(reminderStatus == 104){ 
                            showToast(reminderResponseMessage, reminderResponseTitle); 
                        }else{ 
                            showToast(reminderResponseMessage, reminderResponseTitle); 
                        }  
                    },nrd_id);
                }
            }
        };
    </script>
    <div class="modal fade" id="documentAdd" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="documentAddLabel" tabindex="-1" style = "background-color: rgb(255 255 255 / 50%);">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header border-bottom-0 text-center">
                    <h5 class="modal-title w-100" id="documentAddLabel"><?php echo $txtArray["Izvršite upload skeniranog dokumenta"][$languageUser]; ?></h5>
                    <!--<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>-->
                </div>
                <div class="modal-body">
                    <div class = "row pt-1 my-3">
                        <div class = "col-12 text-center">
                            <input type="hidden" id="candidat_id_DA">
                            <input type="hidden" id="nalog_id_DA">
                            <input type="hidden" id="nrd_id_DA" >
                            <input type="hidden" id="doc_type_id_DA" >
                            <input type="hidden" id="candidat_partner_id_DA" >
                            <input type="hidden" id="candidat_key_DA" >
                            <div class="mb-3 mx-5 row">
                                <div class="col-sm-12">
                                    <div class="input-group">
                                        <input type="file" class="form-control" id = "file_DA" aria-label="Upload" placeholder="Upload" required>
                                        <button class="btn btn-danger" type="button" onclick="resetDocumentAdd()">Reset</button>
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
                    <button type="button" class="btn btn-secondary" onclick="closeDocumentAdd()"><?php echo $txtArray["Odustani"][$languageUser]; ?></button>
                    <button class="btn btn-primary saveDoc" onclick="saveDocumentAdd()" id = "" ><?php echo $txtArray["Završi"][$languageUser]; ?></button>
                </div>
            </div>
        </div>
    </div>
    <script>
        function resetDocumentAdd(){
            var file_DA = $("#file_DA").val();
            if(file_DA !== ""){
                $("#file_DA").val(null); 
            }
        };
        function closeDocumentAdd(){
            $("#comment_DA").val(null);
            $("#file_DA").val(null);
            setTimeout(function(){
                $("#documentAdd").modal("hide");
            }, 500);
        };
        function saveDocumentButtonEnableDisable(vr){
            var vrSAD = parseInt(vr);
            if(vrSAD == 1){
                $(".saveDoc").prop('disabled', true);
            }else{
                $(".saveDoc").prop('disabled', false);
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
        function saveDocumentAdd(){
            saveDocumentButtonEnableDisable(1);
            var candidat_id_DA = parseInt($("#candidat_id_DA").val());
            var nalog_id_DA = parseInt($("#nalog_id_DA").val());
            var nrd_id_DA = parseInt($("#nrd_id_DA").val());
            var doc_type_id_DA = parseInt($("#doc_type_id_DA").val());
            var candidat_partner_id_DA = parseInt($("#candidat_partner_id_DA").val());
            var candidat_key_DA = $("#candidat_key_DA").val();
            var comment_DA = $("#comment_DA").val();
            var file_DA_check = $('#file_DA').val();
            var file_DA = $('#file_DA').prop('files')[0];
            var user_id_DA = parseInt('<?php echo $userId; ?>');
            //console.log(candidat_id_DA + " " + nalog_id_DA + " " + nrd_id_DA + " " + doc_type_id_DA + " " + comment_DA + " " + file_DA + " ");
            var form_data = new FormData();

            form_data.append('document', file_DA);
            form_data.append('kandidat_id', candidat_id_DA);
            form_data.append('nalog_id', nalog_id_DA);
            form_data.append('status_comment', comment_DA);
            form_data.append('nrd_id', nrd_id_DA);
            form_data.append('pp_id', user_id_DA);
            form_data.append('new_status_id', 3);
            

            if(candidat_id_DA != 0 && nalog_id_DA != 0 && nrd_id_DA != 0 && doc_type_id_DA != 0 && file_DA_check != ""){
                //console.log("Sve popunjeno!");
                if(doc_type_id_DA == 14){
                    /*
                        Ovo je vanredno mjesto na kojem se izvršava Reminder 8 - preuzmi nostrifikovanu diplomu
                        Smisao je ako se uploada qualiplan a postoji aktivan reminder preuzmi nostrifikovanu diplomu,
                        da se izvrsi reminder jer bi inace ostao aktivan
                        Rješenje je da se ne ispisuje da je izvrsen reminder - nego da ga skripta samo izvrsi

                        START
                    */
                        getAccessControl(nalog_id_DA, candidat_key_DA, candidat_partner_id_DA, 8, function(responseAccessReminder8){
                            var reminder8Status 			= responseAccessReminder8['status'];
                            var reminder8Id 				= responseAccessReminder8['id'];
                            var reminder8Assigned 	        = responseAccessReminder8['assigned'];
                            var reminder8IsLogged 	        = responseAccessReminder8['isLogged'];
                            var reminder8Message	        = responseAccessReminder8['message'];
                            var reminder8Title	            = responseAccessReminder8['title'];
                            if (reminder8Status == 101 || reminder8Status == 103 || reminder8Status == 1 || reminder8Status == 2) {
                                if(reminder8Status == 1 || reminder8Status == 2 || reminder8Status == 103){
                                    updateReminderStatus(reminder8Id, 3, function(responseUpdateReminder8){
                                        var updateReminder8Status 	    = responseUpdateReminder8["status"];
                                        var updateReminder8Message 	    = responseUpdateReminder8["message"];
                                        if(updateReminder8Status == 3){
                                            showToast(updateReminder8Message, reminder8Title);
                                        }
                                    });
                                }
                            }
                        });
                    /*
                        END
                    */

                    getAccessControl(nalog_id_DA, candidat_key_DA, candidat_partner_id_DA, 9, function(responceAccessControlR9){
                        var reminderR9Status 			= responceAccessControlR9['status'];
                        var reminderR9Id 				= responceAccessControlR9['id'];
                        var reminderR9AssignedUser 	    = responceAccessControlR9['assigned'];
                        var reminderR9IsLoggedUser 	    = responceAccessControlR9['isLogged'];
                        var reminderR9ResponseMessage	= responceAccessControlR9['message'];
                        var reminderR9ResponseTitle	    = responceAccessControlR9['title'];

                        if(reminderR9Status == 101 || reminderR9Status == 103 || reminderR9Status == 1 || reminderR9Status == 2){
                            $.ajax({
                                url: '<?php getCRMUrl(); ?>do.php?form=changeDocumentStatus',
                                type: 'POST',
                                processData: false,
                                contentType: false,
                                data:form_data,
                                success : function (){
                                    //console.log($.fn.DataTable.isDataTable('#requiredDocuments'));
                                    getRequiredDocuments(nalog_id_DA, candidat_id_DA);
                                    saveDocumentButtonEnableDisable(0);
                                    closeDocumentAdd();
                                },
                                error: function (xhr, ajaxOptions, thrownError) {
                                    alert(xhr.status);
                                    alert(thrownError);
                                }
                            });
                            // console.log("ušo sam");
                            if(reminderR9Status == 1 || reminderR9Status == 2 || reminderR9Status == 103){
                                updateReminderStatus(reminderR9Id, 3, function(responseUpdateReminderR9){
                                    var updateStatusR9 	    = responseUpdateReminderR9["status"];
                                    var updateMessageR9 	= responseUpdateReminderR9["message"];
                                    if(updateStatusR9 == 3){
                                        showToast(updateMessageR9, reminderR9ResponseTitle);
                                    }
                                });
                            }
                        }else if (reminderR9Status == 104){
                            showToast(reminderR9ResponseMessage, reminderR9ResponseTitle);
                        }else{
                            showToast(reminderR9ResponseMessage, reminderR9ResponseTitle);
                        }
                    });
                }else{
                    
                    getAccessControl(nalog_id_DA, candidat_key_DA, candidat_partner_id_DA, 11, function (responseAccessControl) { 
                        var reminderStatus              = responseAccessControl['status']; 
                        var reminderId                  = responseAccessControl['id']; 
                        var reminderAssignedUser        = responseAccessControl['assigned']; 
                        var reminderIsLoggedUser        = responseAccessControl['isLogged']; 
                        var reminderResponseMessage     = responseAccessControl['message']; 
                        var reminderResponseTitle       = responseAccessControl['title']; 
                        /* console.log(reminderStatus+"-"+reminderId+"-"+reminderAssignedUser+"-"+reminderIsLoggedUser+"-"+reminderResponseMessage+"-"+reminderResponseTitle);
                        alert(nalog_id_DA+"-"+candidat_key_DA+"-"+candidat_partner_id_DA+"-"+nrd_id_DA); */
                          if(reminderStatus == 101 || reminderStatus == 103 || reminderStatus == 1 || reminderStatus == 2){
                            
                            $.ajax({
                                url: '<?php getCRMUrl(); ?>do.php?form=changeDocumentStatus',
                                type: 'POST',
                                processData: false,
                                contentType: false,
                                data:form_data,
                                success : function (){
                                    //console.log($.fn.DataTable.isDataTable('#requiredDocuments'));
                                    getRequiredDocuments(nalog_id_DA, candidat_id_DA);
                                    saveDocumentButtonEnableDisable(0);
                                    closeDocumentAdd();
                                },
                                error: function (xhr, ajaxOptions, thrownError) {
                                    alert(xhr.status);
                                    alert(thrownError);
                                }
                            });
                            if(reminderStatus == 2 || reminderStatus == 1 || reminderStatus == 103){ 
                                updateReminderStatus(reminderId, 3, function (responseUpdateReminder){ 
                                    var updateStatus       = responseUpdateReminder["status"]; 
                                    var updateMessage      = responseUpdateReminder["message"]; 
                                    if(updateStatus == 3){ 
                                        showToast(updateMessage, reminderResponseTitle); 
                                    } 
                                }); 
                            } 
                        }else if(reminderStatus == 104){ 
                            showToast(reminderResponseMessage, reminderResponseTitle); 
                        }else{ 
                            showToast(reminderResponseMessage, reminderResponseTitle); 
                        }  
                    },nrd_id_DA);

                }
                
            }else{
                saveDocumentButtonEnableDisable(0);
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
       function documentDetails(thisRow) {
            //Ovdje ce ici provjera za remindere nekad kad budu bili 
            var nrd_id = parseInt($(thisRow).data("nrd_id"));
            var doc_id = parseInt($(thisRow).data("doc_id"));
            var candidat_id = parseInt($(thisRow).data("candidat_id"));
            var nalog_id = parseInt($(thisRow).data("nalog_id"));
            
            //console.log(nrd_id+ " " + doc_id + " " + candidat_id + " " + nalog_id);

            if(nrd_id != 0 && doc_id != 0 && candidat_id != 0 && nalog_id != 0){
                $.ajax({
                    url: 'ajax.php?action=documentDetails',
                    type: 'POST',
                    dataType: 'html',
                    data:{
                        'nrd_id': nrd_id,
                        'doc_id': doc_id,
                        'candidat_id': candidat_id,
                        'nalog_id': nalog_id,
                    },
                    success : function (result){
                        $(".documentDetailsBody").html(result);
                        enablePopoversAndTooltips();
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(xhr.status);
                        alert(thrownError);
                    }
                });
            }
            
        };
    </script>
    <div class="modal fade scrollBarVertical" id="documentDetails" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="documentDetailsLabel" tabindex="-1" style = "background-color: rgb(255 255 255 / 50%);">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header border-bottom-0 text-center">
                    <h5 class="modal-title w-100" id="documentDetailsLabel"><?php echo $txtArray["Detalji dokumenta"][$languageUser]; ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 py-4">
                    <div class = "row">
                        <div class = "col-md-12 text-center documentDetailsBody">
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
        function sendDocumentsInfo(thisRow){
            var flagEnpalAccess = parseInt("<?php echo $flagEnpalAccess; ?>");
            var candidateIdKey = "<?php echo $kandidat_key; ?>";
            var candidate_id = parseInt($(thisRow).data("candidate_id")); 
            var nalog_id = parseInt($(thisRow).data("nalog_id"));
            var partner_id = parseInt($(thisRow).data("partner_id"));
            
            if (flagEnpalAccess == 1) {
                $.ajax({
                    url: 'ajax.php?action=readyToSendIds',
                    type: 'POST',
                    dataType: 'html',
                    data:{
                        'idCandidate': candidate_id,
                        'idNalog': nalog_id
                    },
                    success : function (result){
                        $(".readyToSendIds").html(result);
                        $("#candidate_id_SD").val(candidate_id);
                        $("#nalog_id_SD").val(nalog_id);
                        $("#partner_id_SD").val(partner_id);
                        $("#sendDocuments").modal("show");
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(xhr.status);
                        alert(thrownError);
                    }
                });
            } else {
                getAccessControl(nalog_id, candidateIdKey, partner_id, 14, function(responceAccessControlR14){
                    var reminderR14Status 			= responceAccessControlR14['status'];
                    var reminderR14Id 				= responceAccessControlR14['id'];
                    var reminderR14AssignedUser 	= responceAccessControlR14['assigned'];
                    var reminderR14IsLoggedUser 	= responceAccessControlR14['isLogged'];
                    var reminderR14ResponseMessage	= responceAccessControlR14['message'];
                    var reminderR14ResponseTitle	= responceAccessControlR14['title'];

                    //console.log("Status: "+reminderR14Status+" ReminderId: "+reminderR14Id+" ReminderAssignedUser: "+reminderR14AssignedUser+" ReminderLoggedUser: "+reminderR14IsLoggedUser+" ReminderMessage: "+reminderR14ResponseMessage+" ReminderTitle: "+reminderR14ResponseTitle);

                    if(reminderR14Status == 101 || reminderR14Status == 1 || (reminderR14Status == 2 && reminderR14IsLoggedUser == 1)){
                        $.ajax({
                            url: 'ajax.php?action=readyToSendIds',
                            type: 'POST',
                            dataType: 'html',
                            data:{
                                'idCandidate': candidate_id,
                                'idNalog': nalog_id
                            },
                            success : function (result){
                                $(".readyToSendIds").html(result);
                                $("#candidate_id_SD").val(candidate_id);
                                $("#nalog_id_SD").val(nalog_id);
                                $("#partner_id_SD").val(partner_id);
                                $("#sendDocuments").modal("show");
                            },
                            error: function (xhr, ajaxOptions, thrownError) {
                                alert(xhr.status);
                                alert(thrownError);
                            }
                        });
                        if(reminderR14Status == 1){
                            updateReminderStatus(reminderR14Id, 2, function(responseUpdateReminderR14){
                                var updateStatusR14 	= responseUpdateReminderR14["status"];
                                var updateMessageR14 	= responseUpdateReminderR14["message"];
                                if(updateStatusR14 == 2){
                                    showToast(updateMessageR14, reminderR14ResponseTitle);
                                }
                            });
                        }
                    }else if (reminderR14Status == 104){
                        showToast(reminderR14ResponseMessage, reminderR14ResponseTitle);
                    }else{
                        showToast(reminderR14ResponseMessage, reminderR14ResponseTitle);
                    }
                });
            }
        }
    </script>
    <div class="modal fade scrollBarVertical" id="sendDocuments" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="sendDocumentsLabel" tabindex="-1" style = "background-color: rgb(255 255 255 / 50%);">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header border-bottom-0 text-center">
                    <h5 class="modal-title w-100" id="sendDocumentsLabel"><?php echo $txtArray["Pošalji dokumente"][$languageUser]; ?></h5>
                    <!--<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>-->
                </div>
                <div class="modal-body px-4 py-4">
                    <div class = "row">
                        <div class = "col-md-12 ">
                            <input type="hidden" id="candidate_id_SD">
                            <input type="hidden" id="nalog_id_SD">
                            <input type="hidden" id="partner_id_SD">
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
                                <div class = "col-sm-12 readyToSendIds">
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
                    <button type="button" class="btn btn-secondary" onclick="closeSendDocuments()"><?php echo $txtArray["Odustani"][$languageUser]; ?></button>
                    <button class="btn btn-primary sendDoc" onclick="sendDocuments()" id = "" ><?php echo $txtArray["Završi"][$languageUser]; ?></button>
                </div>
            </div>
        </div>
    </div>
    <script>
        function closeSendDocuments(){
            $(".readyToSendIds").html("");
            $("#comment_SD").html("");
            setTimeout(function(){
                $("#sendDocuments").modal("hide");
            }, 500);
        };
        function sendDocumentsButtonEnableDisable(vr){
            var vrSD = parseInt(vr);
            if(vrSD == 1){
                $(".sendDoc").prop('disabled', true);
            }else{
                $(".sendDoc").prop('disabled', false);
            }
        };
        function sendDocuments(){
            sendDocumentsButtonEnableDisable(1);
            var candidateIdKeySD = "<?php echo $kandidat_key; ?>";
            var candidate_id_SD = parseInt($("#candidate_id_SD").val());
            var nalog_id_SD = parseInt($("#nalog_id_SD").val());
            var partner_id_SD = parseInt($("#partner_id_SD").val());
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

                getAccessControl(nalog_id_SD, candidateIdKeySD, partner_id_SD, 14, function(responceAccessControlR14){
                    var reminderR14Status 			= responceAccessControlR14['status'];
                    var reminderR14Id 				= responceAccessControlR14['id'];
                    var reminderR14AssignedUser 	= responceAccessControlR14['assigned'];
                    var reminderR14IsLoggedUser 	= responceAccessControlR14['isLogged'];
                    var reminderR14ResponseMessage	= responceAccessControlR14['message'];
                    var reminderR14ResponseTitle	= responceAccessControlR14['title'];

                    console.log("Status: "+reminderR14Status+" ReminderId: "+reminderR14Id+" ReminderAssignedUser: "+reminderR14AssignedUser+" ReminderLoggedUser: "+reminderR14IsLoggedUser+" ReminderMessage: "+reminderR14ResponseMessage+" ReminderTitle: "+reminderR14ResponseTitle);

                    if(reminderR14Status == 101 || reminderR14Status == 103 || reminderR14Status == 1 || reminderR14Status == 2){
                        $.ajax({
                            url: '<?php getCRMUrl(); ?>do.php?form=poslodavacPoslaoDokumente',
                            type: 'POST',
                            processData: false,
                            contentType: false,
                            data:form_data,
                            success : function (){
                                getRequiredDocuments(nalog_id_SD, candidate_id_SD);
                                sendDocumentsButtonEnableDisable(0);
                                closeSendDocuments();
                            },
                            error: function (xhr, ajaxOptions, thrownError) {
                                alert(xhr.status);
                                alert(thrownError);
                            }
                        });
                        if(reminderR14Status == 1 || reminderR14Status == 2 || reminderR14Status == 103){
                            updateReminderStatus(reminderR14Id, 3, function(responseUpdateReminderR14){
                                var updateStatusR14 	= responseUpdateReminderR14["status"];
                                var updateMessageR14 	= responseUpdateReminderR14["message"];
                                if(updateStatusR14 == 3){
                                    showToast(updateMessageR14, reminderR14ResponseTitle);
                                }
                            });
                        }
                    }else if (reminderR14Status == 104){
                        showToast(reminderR14ResponseMessage, reminderR14ResponseTitle);
                    }else{
                        showToast(reminderR14ResponseMessage, reminderR14ResponseTitle);
                    }
                });
            }else{
                $('.alertDocumentsUnavailable').removeClass('visually-hidden');
                setTimeout(function(){
                    $('.alertDocumentsUnavailable').addClass('visually-hidden');
                    sendDocumentsButtonEnableDisable(0);
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
        function newDocumentAddModalSet(thisRow) {
            var flagEnpalAccess = parseInt("<?php echo $flagEnpalAccess; ?>");
            //Ovdje ce ici provjera za remindere nekad kad budu bili 
            var nrd_id = $(thisRow).data("nrd_id");
            var doc_type_id = $(thisRow).data("doc_type_id");
            var candidat_id = $(thisRow).data("candidat_id");
            var nalog_id = $(thisRow).data("nalog_id");
            var candidateIdKey = $(thisRow).data("candidate_key");
            var partnerId = $(thisRow).data("candidat_partner_id");

            if (flagEnpalAccess == 0) {
                getAccessControl(nalog_id, candidateIdKey, partnerId, 13, function (responseAccessControl) { 
                    
                    var reminderStatus          = responseAccessControl['status']; 
                    var reminderId              = responseAccessControl['id']; 
                    var reminderAssignedUser    = responseAccessControl['assigned']; 
                    var reminderIsLoggedUser    = responseAccessControl['isLogged']; 
                    var reminderResponseMessage = responseAccessControl['message']; 
                    var reminderResponseTitle   = responseAccessControl['title']; 
                    
                    if (reminderStatus == 101 || reminderStatus == 1 || (reminderStatus == 2 && reminderIsLoggedUser == 1)) { 
                        // Kod za poziv tražene akcije
                        if (reminderStatus == 1) { 

                            updateReminderStatus(reminderId, 2, function (responseUpdateReminder){ 

                                var updateStatus  = responseUpdateReminder["status"]; 
                                var updateMessage = responseUpdateReminder["message"]; 
                                if(updateStatus == 2) showToast(updateMessage, reminderResponseTitle); 

                            });

                        }
                    } else if (reminderStatus == 104) showToast(reminderResponseMessage, reminderResponseTitle);  
                    else showToast(reminderResponseMessage, reminderResponseTitle);  
                        
                }, nrd_id);
            }

            //alert(nrd_id+ " " + doc_type_id + " " + candidat_id + " " + nalog_id);
            $("#candidat_id_NDA").val(candidat_id);
            $("#nalog_id_NDA").val(nalog_id);
            $("#nrd_id_NDA").val(nrd_id);
            $("#doc_type_id_NDA").val(doc_type_id);
            $("#candidate_id_key_NDA").val(candidateIdKey);
            $("#partner_id_NDA").val(partnerId);

        };
    </script>
    <div class="modal fade" id="newDocumentAdd" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="newDocumentAddLabel" tabindex="-1" style = "background-color: rgb(255 255 255 / 50%);">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header border-bottom-0 text-center">
                    <h5 class="modal-title w-100" id="newDocumentAddLabel"><?php echo $txtArray["Izvršite upload ispravke dokumenta"][$languageUser]; ?></h5>
                    <!--<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>-->
                </div>
                <div class="modal-body">
                    <div class = "row pt-1 my-3">
                        <div class = "col-12 text-center">
                            <input type="hidden" id="candidat_id_NDA">
                            <input type="hidden" id="nalog_id_NDA">
                            <input type="hidden" id="nrd_id_NDA">
                            <input type="hidden" id="doc_type_id_NDA">
                            <input type="hidden" id="candidate_id_key_NDA">
                            <input type="hidden" id="partner_id_NDA">
                            <div class="mb-3 mx-5 row">
                                <div class="col-sm-12">
                                    <div class="input-group">
                                        <input type="file" class="form-control" id = "file_NDA" aria-label="Upload" placeholder="Upload" required>
                                        <button class="btn btn-danger" type="button" onclick="resetNewDocumentAdd()">Reset</button>
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
                    <button type="button" class="btn btn-secondary" onclick="closeNewDocumentAdd()"><?php echo $txtArray["Odustani"][$languageUser]; ?></button>
                    <button class="btn btn-primary saveNewDoc" onclick="saveNewDocumentAdd(this)"><?php echo $txtArray["Završi"][$languageUser]; ?></button>
                </div>
            </div>
        </div>
    </div>
    <script>
        function resetNewDocumentAdd(){
            var file_NDA = $("#file_NDA").val();
            if(file_NDA !== ""){
                $("#file_NDA").val(null); 
            }
        };
        function closeNewDocumentAdd(){
            $("#comment_NDA").val(null);
            $("#file_NDA").val(null);
            setTimeout(function(){
                $("#newDocumentAdd").modal("hide");
            }, 500);
        };
        function saveNewDocumentButtonEnableDisable(vr){
            var vrSND = parseInt(vr);
            if(vrSND == 1){
                $(".saveNewDoc").prop('disabled', true);
            }else{
                $(".saveNewDoc").prop('disabled', false);
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
        function saveNewDocumentAdd(thisRow){
            saveNewDocumentButtonEnableDisable(1);
            var candidat_id_NDA = parseInt($("#candidat_id_NDA").val());
            var nalog_id_NDA = parseInt($("#nalog_id_NDA").val());
            var partner_id_NDA = parseInt($("#partner_id_NDA").val());
            var candidate_id_key_NDA = $("#candidate_id_key_NDA").val();
            var nrd_id_NDA = parseInt($("#nrd_id_NDA").val());
            var doc_type_id_NDA = parseInt($("#doc_type_id_NDA").val());
            var comment_NDA = $("#comment_NDA").val();
            var file_NDA_check = $('#file_NDA').val();
            var file_NDA = $('#file_NDA').prop('files')[0];
            var user_id_NDA = parseInt('<?php echo $userId; ?>');
            //console.log(candidat_id_DA + " " + nalog_id_DA + " " + nrd_id_DA + " " + doc_type_id_DA + " " + comment_DA + " " + file_DA + " ");
            var form_data = new FormData();

            form_data.append('document', file_NDA);
            form_data.append('kandidat_id', candidat_id_NDA);
            form_data.append('nalog_id', nalog_id_NDA);
            form_data.append('status_comment', comment_NDA);
            form_data.append('nrd_id', nrd_id_NDA);
            form_data.append('pp_id', user_id_NDA);
            form_data.append('new_status_id', 3);

            if(candidat_id_NDA != 0 && nalog_id_NDA != 0 && nrd_id_NDA != 0 && doc_type_id_NDA != 0 && file_NDA_check != "" && partner_id_NDA != 0 && candidate_id_key_NDA != ""){

                //console.log("Sve popunjeno!");
                getAccessControl(nalog_id_NDA, candidate_id_key_NDA, partner_id_NDA, 13, function (responseAccessControl) { 

                    var reminderStatus          = responseAccessControl['status'];
                    var reminderId              = responseAccessControl['id']; 
                    var reminderAssignedUser    = responseAccessControl['assigned']; 
                    var reminderIsLoggedUser    = responseAccessControl['isLogged']; 
                    var reminderResponseMessage = responseAccessControl['message']; 
                    var reminderResponseTitle   = responseAccessControl['title']; 
                    
                    if (reminderStatus == 101 || reminderStatus == 103 || reminderStatus == 1 || reminderStatus == 2) { 

                        // Kod za poziv tražene akcije
                        $.ajax({
                            url: '<?php getCRMUrl(); ?>do.php?form=changeDocumentStatus',
                            type: 'POST',
                            processData: false,
                            contentType: false,
                            data:form_data,
                            
                            success : function () {

                                //console.log($.fn.DataTable.isDataTable('#requiredDocuments'));
                                getRequiredDocuments(nalog_id_NDA, candidat_id_NDA);
                                saveNewDocumentButtonEnableDisable(0);
                                closeNewDocumentAdd();
                            
                            },

                            error: function (xhr, ajaxOptions, thrownError) {
                                alert(xhr.status);
                                alert(thrownError);
                            }
                        });

                        if (reminderStatus == 2 || reminderStatus == 1 || reminderStatus == 103) { 

                            updateReminderStatus(reminderId, 3, function (responseUpdateReminder){ 

                                var updateStatus  = responseUpdateReminder["status"]; 
                                var updateMessage = responseUpdateReminder["message"]; 
                                if(updateStatus == 3) showToast(updateMessage, reminderResponseTitle); 

                            });

                        }
                    } else if (reminderStatus == 104) showToast(reminderResponseMessage, reminderResponseTitle);  
                    else showToast(reminderResponseMessage, reminderResponseTitle);  
                        
                }, nrd_id_NDA);
                
            } else {

                //console.log("Nije popunjeno!");
                saveNewDocumentButtonEnableDisable(0);

            }
            //alert(candidat_id_DA + " " + nalog_id_DA + " " + nrd_id_DA + " " + doc_type_id_DA + " " + comment_DA + " " + file_DA + " ");
        };
    </script>
    <!-- 
        Modal za upload novog dokumenta koji nije prosao provjeru END 
    -->

<!-- 
    *************************************************************
    *	Modali za određene akcije na listi dokumenata END		*
    *************************************************************
-->