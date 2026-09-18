
//FUNCTIONS START
    function bezGrupacija(){
        //REMOVE PREVIOUS HTML ELEMENTS
        $("#kompanije-grupacije").remove();
        $("#kompanije").remove();
        $("#grupacije2").remove();
    }
    function saGrupacijama(){
        //ADD NEW HTML ELEMENTS
        $("#tip-grupacija").after('<div class="form-group" id="kompanije-grupacije"></div>');
        $("#kompanije-grupacije").append('<label for="kompanije" class="col-sm-3 control-label"><strong>Partneri:</strong></label>');
        $("#kompanije-grupacije").append('<div class="col-sm-6" id="kompanije"></div>');
        $("#kompanije").append('<select class="selectpicker" id="kompanije_select" name="kompanije" data-live-search="true"></select>');
        $("#kompanije-grupacije").append('<div id="card-container"></div>');
        
    }
    
    ///////////////////////////////////////////////////
    //-------FUNKCIJA ZA DODAVANJE SUPERADMINA------//
    /////////////////////////////////////////////////
    function addSuperadmin(nalog, kompanija){
        $(".superadmin").click(function(){
            var user_id 	= $(this).data("value");
            var nalog_id 	= nalog; 
            $("#superadmin" + user_id).hide();
            $("#list_superadmin").empty();
            //DODAVANJE SUPERADMINA U TABELU idk_pp_user_access
            $.ajax({
                url: 'ajax_data.php?page=add_superadmin',
                type: 'POST',
                data: {
                    'user_id':user_id,
                    'nalog_id':nalog_id
                },
                dataType: 'html',
                success: function(data) {
                    console.log('success!');
                    //ISPISIVANJE TABELE SUPERADMINA U LISTI POSTAVKI
                    $.ajax({
                        url: 'ajax_data.php?page=list_superadmin',
                        type: 'POST',
                        data: {'nalog_id':nalog_id},
                        dataType: 'html',
                        success: function(data) {
                            $("#list_superadmin").html(data);
                            $('#idk_table_list_superadmin').DataTable({
                                responsive: true,
                                searching: false,
                                paging: false,
                                "order": [[ 0, "asc" ]],

                                    "bAutoWidth": false,

                                "aoColumns": [
                                        { "width": "5%" },
                                        { "width": "25%" },
                                        { "width": "15%" },
                                        { "width": "10%", "bSortable": false }
                                    ]
                            });
                            removeSuperadmin(nalog);
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            alert(xhr.status);
                            alert(thrownError);
                        }
                    });
                    //------END ISPIS-----//
                    //REFRESH SUPERADMIN TABELE
                    $("#superadmin_table").empty();
                    var kompanija_id = kompanija;
                    $.ajax({
                        url: 'ajax_data.php?page=superadmin_table',
                        type: 'POST',
                        data: {
                            'kompanija_id':kompanija_id,
                            'nalog_id':nalog_id
                        },
                        dataType: 'html',
                        success: function(data){
                            $("#superadmin_table").html(data);
                            $('#idk_table').DataTable({
                                responsive: true,
                                searching: false,
                                paging: false,
                                "order": [[ 0, "asc" ]],

                                    "bAutoWidth": false
                            });
                            addSuperadmin(nalog, kompanija);
                            addAdmin(nalog, kompanija);
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            alert(xhr.status);
                            alert(thrownError);
                        }
                    });
                    //-----END REFRESH----//
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                }
            });
            //----END DODAVANJE---//
        });
    }
    
    //////////////////////////////////////////////////
    //------FUNKCIJA ZA UKLANJANJE SUPERADMINA-----//
    ////////////////////////////////////////////////
    function removeSuperadmin(nalog, kompanija){
        $(".remove-superadmin").click(function(){
            var user_id 	= $(this).data("value");
            var nalog_id 	= nalog;
            $.ajax({
                url: 'ajax_data.php?page=remove_superadmin',
                type: 'POST',
                data: {
                    'user_id':user_id,
                    'nalog_id':nalog_id
                },
                dataType: 'html',
                success: function(data){
                    //REFRESH SUPERADMIN TABELU U LISTI POSTAVKI
                    $("#list_superadmin").empty();
                    var nalog_id = nalog;
                    $.ajax({
                        url: 'ajax_data.php?page=list_superadmin',
                        type: 'POST',
                        data: {'nalog_id':nalog_id},
                        dataType: 'html',
                        success: function(data) {
                            $("#list_superadmin").html(data);
                            $('#idk_table_list_superadmin').DataTable({
                                responsive: true,
                                searching: false,
                                paging: false,
                                "order": [[ 0, "asc" ]],

                                    "bAutoWidth": false,

                                "aoColumns": [
                                        { "width": "5%" },
                                        { "width": "25%" },
                                        { "width": "15%" },
                                        { "width": "10%", "bSortable": false }
                                    ]
                            });
                            removeSuperadmin(nalog, kompanija);
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            alert(xhr.status);
                            alert(thrownError);
                        }
                    });
                    //REFRESH SUPERADMIN TABELU
                    var kompanija_id 	= kompanija;
                    var nalog_id 		= nalog;
                    $("#superadmin_table").empty();
                    $.ajax({
                        url: 'ajax_data.php?page=superadmin_table',
                        type: 'POST',
                        data: {
                            'kompanija_id':kompanija_id,
                            'nalog_id':nalog_id
                        },
                        dataType: 'html',
                        success: function(data){
                            $("#superadmin_table").html(data);
                            $('#idk_table').DataTable({
                                responsive: true,
                                searching: false,
                                paging: false,
                                "order": [[ 0, "asc" ]],

                                    "bAutoWidth": false
                            });
                            addSuperadmin(nalog, kompanija);
                            addAdmin(nalog, kompanija);
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            alert(xhr.status);
                            alert(thrownError);
                        }
                    });
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                }
            });
        });
    }
    
    ////////////////////////////////////////////
    //------FUNKCIJA ZA DODAVANJE ADMINA------//
    ///////////////////////////////////////////
    function addAdmin(nalog, kompanija){
        $(".add-admin").click(function(){
            var user_id 		= $(this).data("value");
            var nalog_id 		= nalog;
            var kompanija_id 	= kompanija;
            // console.log(user_id);
            // console.log(nalog_id);
            // console.log(kompanija_id);
            $("#admin" + user_id).hide();
            $.ajax({
                url: 'ajax_data.php?page=add_admin',
                type: 'POST',
                data: {
                    'kompanija_id':kompanija_id,
                    'nalog_id':nalog_id,
                    'user_id':user_id
                },
                dataType: 'html',
                success: function(data){
                    //REFRESH ADMIN TABELU U LISTI POSTAVKI
                    $("#list_admin").empty();
                    $.ajax({
                        url: 'ajax_data.php?page=admin_list',
                        type: 'POST',
                        data: {
                            'kompanija_id':kompanija_id,
                            'nalog_id':nalog_id
                        },
                        dataType: 'html',
                        success: function(data){
                            $("#list_admin").html(data);
                            $('#idk_table_list_admin').DataTable({
                                responsive: true,
                                searching: false,
                                paging: false,
                                "order": [[ 0, "asc" ]],

                                    "bAutoWidth": false,

                                "aoColumns": [
                                        { "width": "5%" },
                                        { "width": "25%" },
                                        { "width": "15%" },
                                        { "width": "10%", "bSortable": false }
                                    ]
                            });
                            removeAdmin(nalog, kompanija);
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            alert(xhr.status);
                            alert(thrownError);
                        }
                    });
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                }
            });
        });
    }
    
    ////////////////////////////////////////////
    //-----FUNKCIJA ZA UKLANJANJE ADMINA-----//
    //////////////////////////////////////////
    function removeAdmin(nalog, kompanija){
        $(".remove-admin").click(function(){
            var user_id 		= $(this).data("value");
            var kompanija_id 	= kompanija;
            var nalog_id 		= nalog;
            $.ajax({
                url: 'ajax_data.php?page=remove_admin',
                type: 'POST',
                data: {
                    'kompanija_id':kompanija_id,
                    'nalog_id':nalog_id,
                    'user_id':user_id
                },
                dataType: 'html',
                success:function(data){
                    $("#superadmin_table").empty();
                    var nalog_id = nalog;
                    $.ajax({
                        url: 'ajax_data.php?page=superadmin_table',
                        type: 'POST',
                        data: {
                            'kompanija_id'	:kompanija_id,
                            'nalog_id'		:nalog_id
                        },
                        dataType: 'html',
                        success: function(data) {
                            $("#superadmin_table").html(data);
                                $('#idk_table').DataTable({

                                responsive: true,
                                searching: false,
                                paging: false,
                                "order": [[ 0, "asc" ]],

                                    "bAutoWidth": false
                            });
                            addSuperadmin(nalog, kompanija);
                            addAdmin(nalog, kompanija);
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            alert(xhr.status);
                            alert(thrownError);
                        }
                    });
                    $("#list_admin").empty();
                    $.ajax({
                        url: 'ajax_data.php?page=admin_list',
                        type: 'POST',
                        data: {
                            'kompanija_id':kompanija_id,
                            'nalog_id':nalog_id
                        },
                        dataType: 'html',
                        success: function(data){
                            $("#list_admin").html(data);
                            $('#idk_table_list_admin').DataTable({
                                responsive: true,
                                searching: false,
                                paging: false,
                                "order": [[ 0, "asc" ]],

                                    "bAutoWidth": false,

                                "aoColumns": [
                                        { "width": "5%" },
                                        { "width": "25%" },
                                        { "width": "15%" },
                                        { "width": "10%", "bSortable": false }
                                    ]
                            });
                            removeAdmin(nalog, kompanija);
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            alert(xhr.status);
                            alert(thrownError);
                        }
                    });
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                }
            });
        });
    }
    
    ////////////////////////////////////////////////////
    //-----FUNKCIJA ZA DODAVANJE PARTNER ADMINA------//
    //////////////////////////////////////////////////
    function addPartnerAdmin(nalog, kompanija){
        $(".add_partner_admin").click(function(){
            var user_id 	= $(this).data("value");
            var partner_id 	= $("#partner_id_edit").val();
            var nalog_id 	= nalog;
            $.ajax({
                url: 'ajax_data.php?page=add_partner_admin',
                type: 'POST',
                data: {
                    'user_id':user_id,
                    'partner_id':partner_id,
                    'nalog_id':nalog_id
                },
                dataType: 'html',
                success: function(){
                    $("#card-container").empty();
                    var kompanija_id = $("#kompanije_select").val();
                    var nalog_id = nalog;
                    $.ajax({
                        url: 'ajax_data.php?page=form_company',
                        type: 'POST',
                        data: {
                            'kompanija_id'	:kompanija_id,
                            'nalog_id' 		:nalog_id
                        },
                        dataType: 'html',
                        success: function(data) {
                            $("#card-container").html(data);
                            $('#idk_table3').DataTable({
                                responsive: true,
                                searching: false,
                                paging: false,
                                "order": [[ 0, "asc" ]],

                                    "bAutoWidth": false,

                                "aoColumns": [
                                        { "width": "5%" },
                                        { "width": "25%" },
                                        { "width": "15%" },
                                        { "width": "10%", "bSortable": false }
                                    ]
                            });
                            addPartnerAdmin(nalog, kompanija);
                            removePartnerAdmin(nalog, kompanija);
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            alert(xhr.status);
                            alert(thrownError);
                        }
                    });
                    $("#partner_container").empty();
                    var nalog_id 		= nalog;
                    var kompanija_id 	= kompanija;
                    $.ajax({
                        url: 'ajax_data.php?page=list_partner',
                        type: 'POST',
                        data: {
                            'nalog_id'		:nalog_id,
                            'kompanija_id' 	:kompanija_id
                        },
                        dataType: 'html',
                        success: function(data) {
                            $("#partner_container").html(data);
                        
                            removePartnerAdmin2(nalog, kompanija);
                            removePartner(nalog, kompanija);
                            addPartner(nalog, kompanija);
                            editPartner(nalog, kompanija);
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            alert(xhr.status);
                            alert(thrownError);
                        }
                    });
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                }
            });
        });
    }
    
    //////////////////////////////////////////////////////////////////////////////////////////////////////
    //-----FUNKCIJA ZA UKLANJANJE PARTNER ADMINA PRILIKOM DODAVANJA PARTNERA-----//
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    function removePartnerAdmin(nalog, kompanija){
        $(".remove_partner_admin").click(function(){
            var user_id 	= $(this).data("value");
            var partner_id 	= $("#partner_id").val();
            var nalog_id 	= nalog;
            $.ajax({
                url: 'ajax_data.php?page=remove_admin',
                type: 'POST',
                data: {
                    'user_id':user_id,
                    'kompanija_id':partner_id,
                    'nalog_id':nalog_id
                },
                dataType: 'html',
                success: function(){
                    $("#card-container").empty();
                    var kompanija_id 	= $("#kompanije_select").val();
                    var nalog_id 		= nalog;
                    $.ajax({
                        url: 'ajax_data.php?page=form_company',
                        type: 'POST',
                        data: {
                            'kompanija_id'	:kompanija_id,
                            'nalog_id'		:nalog_id 
                        },
                        dataType: 'html',
                        success: function(data) {
                            $("#card-container").html(data);
                            $('#idk_table3').DataTable({
                                responsive: true,
                                searching: false,
                                paging: false,
                                "order": [[ 0, "asc" ]],

                                    "bAutoWidth": false,

                                "aoColumns": [
                                        { "width": "5%" },
                                        { "width": "25%" },
                                        { "width": "15%" },
                                        { "width": "10%", "bSortable": false }
                                    ]
                            });
                            addPartnerAdmin(nalog, kompanija);
                            removePartnerAdmin(nalog, kompanija);
                            addPartner(nalog, kompanija);
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            alert(xhr.status);
                            alert(thrownError);
                        }
                    });
                    var nalog_id 		= nalog;
                    var kompanija_id 	= kompanija;
                    $.ajax({
                        url: 'ajax_data.php?page=list_partner',
                        type: 'POST',
                        data: {
                            'nalog_id'		:nalog_id,
                            'kompanija_id' 	:kompanija_id
                        },
                        dataType: 'html',
                        success: function(data) {
                            $("#partner_container").html(data);
                        
                            removePartnerAdmin(nalog, kompanija);
                            removePartner(nalog, kompanija);
                            editPartner(nalog, kompanija);
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            alert(xhr.status);
                            alert(thrownError);
                        }
                    });
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                }
            });
        });
    }


    function addPartnerAdminEdit(nalog, kompanija){
        $(".add_partner_admin").click(function() {
            var user_id 	= $(this).data("value");
            var partner_id 	= $("#partner_id_edit").val();
            var nalog_id 	= nalog;
            $.ajax({
                url: 'ajax_data.php?page=add_partner_admin',
                type: 'POST',
                data: {
                    'user_id':user_id,
                    'partner_id':partner_id,
                    'nalog_id':nalog_id
                },
                dataType: 'html',
                success: function(){
                    $("#partner_container").empty();
                    $.ajax({
                        url: 'ajax_data.php?page=list_partner',
                        type: 'POST',
                        data: {
                            'nalog_id'		:nalog_id,
                            'kompanija_id' 	:kompanija_id
                        },
                        dataType: 'html',
                        success: function(data) {
                            $("#partner_container").html(data);
                        
                            removePartnerAdmin2(nalog, kompanija);
                            editPartner(nalog, kompanija);
                            removePartner(nalog, kompanija);
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            alert(xhr.status);
                            alert(thrownError);
                        }
                    });
                    addPartnerAdmin(nalog, kompanija);
                    removePartnerAdmin(nalog, kompanija);
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                }
            });
        })
    }
    
    ///////////////////////////////////////////////////////////////////////
    //------FUNKCIJA ZA UKLANJANJE PARTNER ADMINA U LISTI PARTNERA------//
    /////////////////////////////////////////////////////////////////////
    function removePartnerAdmin2(nalog, kompanija){
        $(".remove_partner_admin").click(function(){
            var user_id 	= $(this).data("value");
            var partner_id 	= $("#partner_id").val();
            var nalog_id 	= nalog;
            $.ajax({
                url: 'ajax_data.php?page=remove_admin',
                type: 'POST',
                data: {
                    'user_id':user_id,
                    'kompanija_id':partner_id,
                    'nalog_id':nalog_id
                },
                dataType: 'html',
                success: function(){
                    var nalog_id 		= nalog;
                    var kompanija_id 	= kompanija;
                    $.ajax({
                        url: 'ajax_data.php?page=list_partner',
                        type: 'POST',
                        data: {
                            'nalog_id'		:nalog_id,
                            'kompanija_id' 	:kompanija_id
                        },
                        dataType: 'html',
                        success: function(data) {
                            $("#partner_container").html(data);
                        
                            removePartnerAdmin2(nalog, kompanija);
                            removePartner(nalog, kompanija);
                            editPartner(nalog, kompanija);
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            alert(xhr.status);
                            alert(thrownError);
                        }
                    });
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                }
            });
        });
    }
    
    /////////////////////////////////////////////
    //-----FUNKCIJA ZA DODAVANJE PARTNERA-----//
    ///////////////////////////////////////////
    function addPartner(nalog, kompanija){
        $("#add_partner").click(function(){
            var partner_id 		= $("#partner_id").val();
            var nalog_id 		= nalog;
            var broj_kandidata	= $("#broj_kandidata").val();
            $.ajax({
                url: 'ajax_data.php?page=add_partner',
                type: 'POST',
                data: {
                    'partner_id'	:partner_id,
                    'nalog_id'		:nalog_id,
                    'broj_kandidata':broj_kandidata 
                },
                dataType: 'html',
                success: function(data) {
                    var nalog_id 		= nalog;
                    var kompanija_id 	= kompanija;
                    $.ajax({
                        url: 'ajax_data.php?page=list_partner',
                        type: 'POST',
                        data: {
                            'nalog_id'		:nalog_id,
                            'kompanija_id' 	:kompanija_id
                        },
                        dataType: 'html',
                        success: function(data) {
                            $("#partner_container").html(data);
                            
                            removePartnerAdmin(nalog, kompanija);
                            removePartner(nalog, kompanija);
                            editPartner(nalog, kompanija);
                            $("#kompanije-grupacije").remove();
                            var nalog_id = nalog;
                            $.ajax({
                                url: 'ajax_data.php?page=choose_partner',
                                type: 'POST',
                                data: {'nalog_id':nalog_id},
                                dataType: 'html',
                                success: function(data){
                                    $("#tip-grupacija").empty();
                                    $("#tip-grupacija").html(data);
                                    switchInputChecked(nalog, kompanija);
                                    switchInputClicked(nalog, kompanija);
                                },
                                error: function (xhr, ajaxOptions, thrownError) {
                                    alert(xhr.status);
                                    alert(thrownError);
                                }
                            });
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            alert(xhr.status);
                            alert(thrownError);
                        }
                    });
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                }
            });
        });
    }
    
    /////////////////////////////////////////////////
    //-------FUNKCIJA ZA EDITOVANJE PARTNERA------//
    ///////////////////////////////////////////////
    function editPartner(nalog, kompanija){
        $(".edit_partner").click(function(){
            var partner_id 	= $(this).data("value");
            console.log(partner_id);
            var nalog_id 	= nalog;
            $.ajax({
                url: 'ajax_data.php?page=edit_partner',
                type: 'POST',
                data: {
                    'partner_id':partner_id,
                    'nalog_id'	:nalog_id
                },
                dataType: 'html',
                success: function(data) {	
                    $("#edit_container" + partner_id).empty();
                    $("#edit_container" + partner_id).html(data);
                    $('#idk_table4').DataTable({
                        responsive: true,
                        searching: false,
                        paging: false,
                        "order": [[ 0, "asc" ]],

                            "bAutoWidth": false,

                        "aoColumns": [
                                { "width": "5%" },
                                { "width": "25%" },
                                { "width": "15%" },
                                { "width": "10%", "bSortable": false }
                            ]
                    });
                    saveEditPartner(nalog, kompanija);
                    addPartnerAdmin(nalog, kompanija);
                    removePartnerAdmin(nalog, kompanija);
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                }
            });
        });
    }
    
    //////////////////////////////////////////////
    //-----FUNKCIJA ZA UKLANJANJE PARTNERA-----//
    ////////////////////////////////////////////
    function removePartner(nalog, kompanija){
        $("#remove_partner").click(function(){
            var partner_id 	= $(this).data("value");
            var nalog_id 	= nalog;
            console.log(partner_id);
            console.log(nalog_id);
            return;
                $.ajax({
                url: 'ajax_data.php?page=remove_partner',
                type: 'POST',
                data: {
                    'partner_id':partner_id,
                    'nalog_id'	:nalog_id
                },
                dataType: 'html',
                success: function(data) {
                    $("#partner_container").empty();
                        var nalog_id 		= nalog;
                        var kompanija_id 	= kompanija;
                        $.ajax({
                            url: 'ajax_data.php?page=list_partner',
                            type: 'POST',
                            data: {
                                'nalog_id'		:nalog_id,
                                'kompanija_id' 	:kompanija_id
                            },
                            dataType: 'html',
                            success: function(data) {
                                $("#partner_container").html(data);
                            
                                removePartnerAdmin(nalog, kompanija);
                                editPartner(nalog, kompanija);
                                removePartner(nalog, kompanija);
                                var nalog_id = nalog;
                                $("#kompanije-grupacije").remove();
                                $.ajax({
                                    url: 'ajax_data.php?page=choose_partner',
                                    type: 'POST',
                                    data: {'nalog_id':nalog_id},
                                    dataType: 'html',
                                    success: function(data){
                                        $("#tip-grupacija").empty();
                                        $("#tip-grupacija").html(data);
                                        switchInputChecked(nalog, kompanija);
                                        switchInputClicked(nalog, kompanija);
                                    },
                                    error: function (xhr, ajaxOptions, thrownError) {
                                        alert(xhr.status);
                                        alert(thrownError);
                                    }
                                });
                            },
                            error: function (xhr, ajaxOptions, thrownError) {
                                alert(xhr.status);
                                alert(thrownError);
                            }
                        });
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                }
            });
        });
    }
    
    /////////////////////////////////////////////////////////
    //-----FUNKCIJA ZA SPREMANJE EDITOVANOG PARTNERA------//
    ///////////////////////////////////////////////////////
    function saveEditPartner(){
        $("#save_edit").click(function(){
            var partner_id 			= $("#partner_id_edit").val();
            var candidate_number 	= $("#broj_kandidata_edit").val();
            $.ajax({
                url: 'ajax_data.php?page=save_edit_partner',
                type: 'POST',
                data: {
                    'partner_id'		:partner_id,
                    'candidate_number'	:candidate_number
                },
                dataType: 'html',
                success: function(data) {
                    $("#edit_container").empty();
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                }
            });
        });
    }
    
    ///////////////////////////////////////////////////////////////////////
    //------FUNKCIJA KOJA PROVJERAVA DA LI JE SWITCH INPUT OZNACEN------//
    /////////////////////////////////////////////////////////////////////
    function switchInputChecked(nalog, kompanija){
        var grupacija = $("#switch_input1").val();
        var kompanija_id = kompanija;
        
        if($("#switch_input1").is(":checked")){
            saGrupacijama();
            $.ajax({
                url: 'ajax_data.php?page=ajax_companies',
                type: 'POST',
                data: {'grupacija':grupacija},
                dataType: 'html',
                success: function(data) {
                    $("#kompanije_select").html(data).selectpicker('refresh');
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                }
            });
            
            $("#kompanije_select").change(function(){
                var kompanija_id = $(this).val();
                var nalog_id = nalog;
                console.log(kompanija_id);
                $.ajax({
                    url: 'ajax_data.php?page=form_company',
                    type: 'POST',
                    data: {
                        'kompanija_id'	:kompanija_id,
                        'nalog_id'		:nalog_id
                    },
                    dataType: 'html',
                    success: function(data) {
                        $("#card-container").html(data);
                        $('#idk_table3').DataTable({
                            responsive: true,
                            searching: false,
                            paging: false,
                            "order": [[ 0, "asc" ]],

                                "bAutoWidth": false,

                            "aoColumns": [
                                    { "width": "5%" },
                                    { "width": "25%" },
                                    { "width": "15%" },
                                    { "width": "10%", "bSortable": false }
                                ]
                        });
                        //DODAVANJE ADMINA PARTNERU I DODAVANJE PARTNERA
                        addPartnerAdmin(nalog, kompanija);
                        removePartnerAdmin(nalog, kompanija);
                        addPartner(nalog, kompanija);
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(xhr.status);
                        alert(thrownError);
                    }
                });
            });
        } else {
            bezGrupacija();
        }
    }
    
    //////////////////////////////////////////////////////////////////////
    //------FUNKCIJA KOJA PROVJERAVA DA LI JE SWITCH INPUT KLINUT------//
    ////////////////////////////////////////////////////////////////////
    function switchInputClicked(nalog, kompanija){
        $("#switch_input1").click(function(){
            var grupacija = $(this).val();
            var kompanija_id = kompanija;
            
            if($("#switch_input1").is(":checked")){
                saGrupacijama();
                $.ajax({
                    url: 'ajax_data.php?page=ajax_companies',
                    type: 'POST',
                    data: {'grupacija':grupacija},
                    dataType: 'html',
                    success: function(data) {
                        $("#kompanije_select").html(data).selectpicker('refresh');
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(xhr.status);
                        alert(thrownError);
                    }
                });
                
                $("#kompanije_select").change(function(){
                    var kompanija_id = $(this).val();
                    var nalog_id = nalog;
                    console.log(kompanija_id);
                    $.ajax({
                        url: 'ajax_data.php?page=form_company',
                        type: 'POST',
                        data: {
                            'kompanija_id'	:kompanija_id,
                            'nalog_id'		:nalog_id
                        },
                        dataType: 'html',
                        success: function(data) {
                            $("#card-container").html(data);
                            $('#idk_table3').DataTable({
                                responsive: true,
                                searching: false,
                                paging: false,
                                "order": [[ 0, "asc" ]],

                                    "bAutoWidth": false,

                                "aoColumns": [
                                        { "width": "5%" },
                                        { "width": "25%" },
                                        { "width": "15%" },
                                        { "width": "10%", "bSortable": false }
                                    ]
                            });
                            //DODAVANJE ADMINA PARTNERU I DODAVANJE PARTNERA
                            addPartnerAdmin(nalog, kompanija);
                            removePartnerAdmin(nalog, kompanija);
                            addPartner(nalog, kompanija);
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            alert(xhr.status);
                            alert(thrownError);
                        }
                    });
                });
            } else {
                bezGrupacija();
            }
        });
    }

    /////////////////////////////////////////
    //--- FUNKCIJA ZA DODAVANJE TERMINA ---//
    /////////////////////////////////////////
    function addTermin(nalog){
        $("#broj_termina").keyup(function() {
            let value = +$(this).val();
            let ctr = 0;
            $("#termin_input").empty();
            while(ctr < value){
                let termin_number = ctr + 1;
                $("#termin_input").append("<label for='termin" + ctr +"' class='col-sm-5 control-label' style='padding: 3px;'><strong>Termin " + termin_number +":</strong></label><div class='col-sm-3' style='padding: 3px;'><div class='material-input-block material-input-block_success'><input type='date' class='form-control material-input' name='termin" + ctr + "' id='termin" + ctr + "'></div></div>");
                $("#termin_input").append("<label for='grad" + ctr +"' class='col-sm-5 control-label' style='padding: 3px;'><strong>Grad " + termin_number +":</strong></label><div class='col-sm-3' style='padding: 3px;'><div class='material-input-block material-input-block_success'><input class='form-control material-input' name='grad" + ctr + "' id='grad" + ctr + "'></div></div>");
                ctr++;
            }
            if(value > 0){
                $("#termin_input").append('<label for="add_button" class="col-sm-5 control-label"></label><div class="col-sm-3" id="add_button"><a href="#" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj</span></a></div>');
            } else {
                $("#add_button").remove();
            }
            $("#add_button").click(function() {
                let ctr = 0;
                let num_inputs = $("#broj_termina").val();
                let nalog_id = nalog;
                const termini = [];
                const gradovi = [];
                while(ctr < num_inputs){
                    let termin = $("#termin" + ctr).val();
                    let grad   = $("#grad" + ctr).val();
                    termini.push(termin);
                    gradovi.push(grad);
                    ctr++;	
                }
                console.log(nalog_id);
                $.ajax({
                    url: 'ajax_data.php?page=add_termin',
                    type: 'POST',
                    data: {
                        'termini'      : termini,
                        'gradovi' 	   : gradovi,
                        'broj_termina' : num_inputs,
                        'nalog_id'     : nalog_id
                    },
                    success: function(data){
                        $.ajax({
                            url: 'ajax_data.php?page=check_termin',
                            type: 'POST',
                            data: {
                                'nalog_id'     : nalog_id
                            },
                            success: function(data){
                                if(!$.trim(data)){
                                    addTermin();
                                }else{
                                    $("#input_broj_termina").empty();
                                    $("#termin_input").empty();
                                    $("#termin_input").html(data);
                                }
                            },
                            error: function (xhr, ajaxOptions, thrownError) {
                                alert(xhr.status);
                                alert(thrownError);
                            }
                        });
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(xhr.status);
                        alert(thrownError);
                    }
                });
            });
        });
    }

    //////////////////////////////////////
    //---FUNKCIJA ZA BRISANJE TERMINA---//
    //////////////////////////////////////
    function deleteAppt(){
        $(".delete_appt").click(function() {
            let appt_id 	= $(this).data("appt_id");
            let appt_date 	= $(this).data("appt_date");
            let group_id 	= $(this).data("group_id");
            $.ajax({
                url: 'ajax_data.php?page=delete_termin',
                type: 'POST',
                data: {
                    'appt_id' 	: appt_id,
                    'appt_date' : appt_date,
                    'group_id'	: group_id
                },
                success: function(data){
                    $.ajax({
                        url: 'ajax_data.php?page=check_termin',
                        type: 'POST',
                        data: {
                            'nalog_id'     : nalog_id
                        },
                        success: function(data){
                            if(!$.trim(data)){
                                addTermin();
                            }else{
                                $("#input_broj_termina").empty();
                                $("#termin_input").empty();
                                $("#termin_input").html(data);
                                deleteAppt();
                            }
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            alert(xhr.status);
                            alert(thrownError);
                        }
                    });
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.status);
                    alert(thrownError);
                }
            });
        });
    }
//FUNCTIONS END