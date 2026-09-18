function getAccessControl(
  nalog_id,
  candidate_id,
  partner_id,
  reminder_type,
  callback,
  doc_id = 0
) {
  return $.ajax({
    url: "ajax.php?action=get_access_control",
    type: "POST",
    dataType: "json",
    data: {
      nalog_id: nalog_id,
      candidate_id: candidate_id,
      partner_id: partner_id,
      reminder_type: reminder_type,
      doc_id: doc_id,
    },
    success: callback,
    error: function (xhr, ajaxOptions, thrownError) {
      alert(xhr.status);
      alert(thrownError);
    },
  });
}

function updateReminderStatus(reminder_id, status, callback) {
  return $.ajax({
    url: "ajax.php?action=update_reminder_status",
    type: "POST",
    dataType: "json",
    data: {
      reminder_id: reminder_id,
      status: status,
    },
    success: callback,
    error: function (xhr, ajaxOptions, thrownError) {
      alert(xhr.status);
      alert(thrownError);
    },
  });
}

function showToast(message, title) {
  $("#toast_text").empty().append(message);
  $("#toast_title").empty().append(title);
  $("#notification_toast").show("clip", 600);
  $("#notification_toast").effect("highlight");
}

//You may be wondering ... kako provjeriti remindere?????

// PODACI POTREBNI ZA POZIV FUNKCIJE
// getAccessControl(nalog_id, candidate_id, null, 3, function(response_access_control){

// var reminder_status 			= response_access_control['status'];
// var reminder_id 				= response_access_control['id'];
// var reminder_assigned_user 		= response_access_control['assigned'];
// var reminder_is_logged_user 	= response_access_control['isLogged'];
// var reminder_response_message	= response_access_control['message'];

// if((reminder_status == 101) || reminder_status == 1 || (reminder_status == 2 && reminder_is_logged_user == 1)){
// alert('Uradi sve normalno');
// if(reminder_status = 1){
// updateReminderStatus(reminder_id, 3, function(response_update_status){
// alert(response_update_status);
// });
// }
// }
// else if(reminder_status == 104){
// alert('uradi refresh');
// }
// else{
// alert(reminder_response_message);
// }
// });
function checkNaloziPartneriBox(do_option) {
  if (do_option == 1) {
    // $('#db_list_nalozi').removeClass('col-md-5');
    // $('#db_list_nalozi').removeClass('offset-1');
    // $('#db_list_nalozi').addClass('col-md-6');
    // $('#db_list_nalozi').addClass('offset-3');
    $("#db_list_nalozi").removeClass("col-md-6");
    $("#db_list_nalozi").removeClass("offset-3");
    $("#db_list_nalozi").addClass("col-md-5");
    $("#db_list_nalozi").addClass("offset-1");
  } else if (do_option == 2) {
    $("#db_list_nalozi").removeClass("col-md-6");
    $("#db_list_nalozi").removeClass("offset-3");
    $("#db_list_nalozi").addClass("col-md-5");
    $("#db_list_nalozi").addClass("offset-1");
  }
}

function getLoaderBig() {
  $("#page-cover").css("z-index", 2000);
  $("#page-cover")
    .css("opacity", 0.3)
    .fadeIn(300, function () {
      $(".lds-dual-ring_big").css({ "z-index": 9999 }).fadeIn();
    });
}

function removeLoader() {
  $("#page-cover").fadeOut(200, function () {
    $(".lds-dual-ring_big").fadeOut(500);
    $("#page-cover").css("opacity", 0.0);
    $("#page-cover").css("z-index", -1);
  });
}

function handleSuperadminClick() {
  if (!$(this).hasClass("view_choice_superadmin_selected")) {
    $("#view_choice_superadmin").addClass("view_choice_superadmin_selected");
    $("#view_choice_admin").removeClass("view_choice_admin_selected");

    getLoaderBig();
    $("#db_list_nalozi").hide("fade", 350, function () {
      $("#db_progress_bar").hide("blind", 350, function () {
        var nalog_id = 0;
        var bar_id = 0;
        var partner_id = 0;
        var type = 1;

        var get_progress_bar = $.ajax({
          url: "ajax.php?action=get_progress_bar",
          type: "POST",
          dataType: "json",
          data: {
            nalog_ids: nalog_id,
            type: type,
            bar_id: bar_id,
            partner_ids: partner_id,
          },
          error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          },
        });
        var get_list_nalozi = $.ajax({
          url: "ajax.php?action=get_list_nalozi",
          type: "POST",
          dataType: "html",
          data: {
            type: 1,
          },
          error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          },
        });
        var get_list_partners = $.ajax({
          url: "ajax.php?action=get_list_partners",
          type: "POST",
          dataType: "html",
          data: {
            nalog_ids: 0,
          },
          error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          },
        });

        checkNaloziPartneriBox(2);
        $.when(get_progress_bar, get_list_nalozi, get_list_partners).then(
          function (
            response_progress_bar,
            response_list_nalozi,
            response_list_partners
          ) {
            $("#db_progress_bar")
              .empty()
              .append(response_progress_bar[0]["code_to_append"]);
            $("#applied_candidates")
              .empty()
              .append(response_progress_bar[0]["applied_candidates"]);
            for (
              var i = 0;
              i < parseInt(response_progress_bar[0]["bar_type"].length);
              i++
            ) {
              $(response_progress_bar[0]["bar_type"][i]).circleProgress({
                animationDuration: 0,
                max: parseInt(response_progress_bar[0]["bar_max"][i]),
                value: parseInt(response_progress_bar[0]["bar_value"][i]),
                textFormat: function (value, max) {
                  return (
                    response_progress_bar[0]["bar_text"][i] +
                    " " +
                    response_progress_bar[0]["bar_value"][i]
                  );
                },
              });
              $(response_progress_bar[0]["bar_type"][i])
                .children()
                .children(".circle-progress-value")
                .addClass(response_progress_bar[0]["bar_style"][i]);
            }
            $("#db_list_nalozi").empty().append(response_list_nalozi[0]);
            $("#db_list_partners").empty().append(response_list_partners[0]);

            $(".progress_bar_click")
              .unbind("click")
              .bind("click", handleProgressBarClick);
            $(".row_handle_nalog_click")
              .unbind("click")
              .bind("click", handleNalogClick);
            $(".row_handle_partner_click")
              .unbind("click")
              .bind("click", handlePartnerClick);

            $("#db_progress_bar").show("blind", 600, function () {
              $("#db_list_nalozi").show("fade", 600, function () {
                $("#db_list_partners").show("slide", 600);
                removeLoader();
              });
            });
          }
        );
      });
    });
  }
}

function handleAdminClick() {
  if (!$(this).hasClass("view_choice_admin_selected")) {
    $("#view_choice_superadmin").removeClass("view_choice_superadmin_selected");
    $("#view_choice_admin").addClass("view_choice_admin_selected");

    getLoaderBig();
    $("#db_list_partners").hide("slide", 250, function () {
      $("#db_list_nalozi").hide("fade", 250, function () {
        $("#db_progress_bar").hide("blind", 250, function () {
          var nalog_id = 0;
          var bar_id = 0;
          var partner_id = 0;
          var type = 2;

          var get_progress_bar = $.ajax({
            url: "ajax.php?action=get_progress_bar",
            type: "POST",
            dataType: "json",
            data: {
              nalog_ids: nalog_id,
              partner_ids: partner_id,
              type: type,
            },
            error: function (xhr, ajaxOptions, thrownError) {
              alert(xhr.status);
              alert(thrownError);
            },
          });
          var get_list_nalozi = $.ajax({
            url: "ajax.php?action=get_list_nalozi",
            type: "POST",
            dataType: "html",
            data: {
              type: 2,
            },
            error: function (xhr, ajaxOptions, thrownError) {
              alert(xhr.status);
              alert(thrownError);
            },
          });
          $.when(get_progress_bar, get_list_nalozi).then(function (
            response_progress_bar,
            response_list_nalozi
          ) {
            $("#db_progress_bar")
              .empty()
              .append(response_progress_bar[0]["code_to_append"]);

            for (
              var i = 0;
              i < parseInt(response_progress_bar[0]["bar_type"].length);
              i++
            ) {
              $(response_progress_bar[0]["bar_type"][i]).circleProgress({
                animationDuration: 0,
                max: parseInt(response_progress_bar[0]["bar_max"][i]),
                value: parseInt(response_progress_bar[0]["bar_value"][i]),
                textFormat: function (value, max) {
                  return response_progress_bar[0]["bar_text"][i];
                },
              });
              $(response_progress_bar[0]["bar_type"][i])
                .children()
                .children("text")
                .attr("y", 35);
              $(response_progress_bar[0]["bar_type"][i])
                .children()
                .children(".circle-progress-value")
                .addClass(response_progress_bar[0]["bar_style"][i]);
            }
            $("#db_list_nalozi").empty().append(response_list_nalozi[0]);

            $(".progress_bar_click")
              .unbind("click")
              .bind("click", handleProgressBarClick);
            $(".row_handle_nalog_click")
              .unbind("click")
              .bind("click", handleNalogClick);

            checkNaloziPartneriBox(1);
            $("#db_progress_bar").show("blind", 600, function () {
              $("#db_list_nalozi").show("fade", 600);
              removeLoader();
            });
          });
        });
      });
    });
  }
}

function handleProgressBarClick() {
  var type = $(this).attr("type");
  var bar_id = $(this).attr("bar_id");
  var nalog_id = 0;
  var partner_id = 0;
  if ($(".casting_view").hasClass("view_selected")) {
    nalog_id = $(".table_nalog_selected").parent().attr("nalog_id");
    companyEnpal = parseInt($(".table_nalog_selected").parent().attr("company_enpal"));
  } else {
    nalog_id = $(".table_partner_selected").parent().attr("nalog_id");
    partner_id = $(".table_partner_selected").parent().attr("partner_id");
    companyEnpal = parseInt($(".table_partner_selected").parent().attr("company_enpal"));
  }
  if (typeof partner_id === "undefined") {
    partner_id = $(".table_nalog_selected").parent().attr("partner_id");
  }

  $("#db_list_partners").hide("slide", 250, function () {
    $("#db_list_nalozi").hide("slide", 250, function () {
      $("#db_progress_bar").hide("fade", 250, function () {
        $("#db_view_choice").hide("blind", 250, function () {
          getLoaderBig();
          $.ajax({
            url: "ajax.php?action=get_list_candidates",
            type: "POST",
            dataType: "html",
            data: {
              bar_id: bar_id,
              nalog_id: nalog_id,
              type: type,
              partner_id: partner_id,
            },
            success: function (response) {
              $("#db_list_candidates")
                .empty()
                .append(response)
                .show("slide", function () {
                  if (type == 1) {
                    $("#reject_candidate")
                      .unbind("click")
                      .bind("click", handleRejectCandidateClick);
                    $("#reject_candidate").prop("disabled", false);
                    $("#hire_candidate")
                      .unbind("click")
                      .bind("click", handleHireCandidateClick);
                    $("#hire_candidate").prop("disabled", false);
                    $("#assign_candidate_to_partner")
                      .unbind("click")
                      .bind("click", handleAssignCandidateToPartnerClick);
                    $("#assign_candidate_to_partner").prop("disabled", false);
                  }
                  $("#edit_candidate_partner_data")
                    .unbind("click")
                    .bind("click", handleEditCandidatePartnerData);
                  $("#edit_candidate_partner_data").prop("disabled", false);
                  $("#select_appointment").unbind("change");
                  $("#select_partner").unbind("change");
                  $("#btn_refresh_table").unbind("click");
                  $("#btn_refresh_table").bind("click", function () {
                    getLoaderBig();
                    var appointment_ids = $("#select_appointment").val();
                    var partner_ids = $("#select_partner").val();
                    $("#select_appointment").prop("disabled", true);
                    $("#select_partner").prop("disabled", true);
                    $("#select_working_position").prop("disabled", true);
                    var carglass_export = {
                      text: "Carglass export",
                      action: function (e, dt, node, config) {
                        getLoaderBig();
                        $.ajax({
                          url: "export_carglass.php",
                          type: "POST",
                          dataType: "html",
                          data: {
                            bar_id: bar_id,
                            nalog_id: nalog_id,
                            type: type,
                          },
                          success: function (data) {
                            $("#to_append_export").html("");
                            $("#to_append_export").append(data);
                            $("#to_append_export").table2excel({
                              exclude: ".noExl",
                              name: "Export Carglass",
                              filename: "Export Carglass",
                              fileext: ".xls",
                            });
                            removeLoader();
                          },
                          error: function (xhr, ajaxOptions, thrownError) {
                            alert(xhr.status);
                            alert(thrownError);
                          },
                        });
                      },
                    };
                    var enpal_export = {
                      text: "Enpal export",
                      action: function (e, dt, node, config) {
                        getLoaderBig();
                        $.ajax({
                          url: "export_enpal.php",
                          type: "POST",
                          dataType: "html",
                          data: {
                            bar_id: bar_id,
                            nalog_id: nalog_id,
                            type: type,
                          },
                          success: function (data) {
                            $("#to_append_enpal_export").html("");
                            $("#to_append_enpal_export").append(data);
                            $("#to_append_enpal_export").table2excel({
                              exclude: ".noExl",
                              name: "Export Enpal",
                              filename: "Export Enpal",
                              fileext: ".xls",
                            });
                            removeLoader();
                          },
                          error: function (xhr, ajaxOptions, thrownError) {
                            alert(xhr.status);
                            alert(thrownError);
                          },
                        });
                      },
                    };

                    var interview_export = {
                      text: "Interview export",
                      action: function (e, dt, node, config) {
                        getLoaderBig();
                        $.ajax({
                          url: "export_interview.php",
                          type: "POST",
                          dataType: "html",
                          data: {
                            bar_id: bar_id,
                            nalog_id: nalog_id,
                            type: type,
                          },
                          success: function (data) {
                            $("#to_append_interview_export").html("");
                            $("#to_append_interview_export").append(data);
                            $("#to_append_interview_export").table2excel({
                              exclude: ".noExl",
                              name: "Export Interview",
                              filename: "Export Interview",
                              fileext: ".xls",
                            });
                            removeLoader();
                          },
                          error: function (xhr, ajaxOptions, thrownError) {
                            alert(xhr.status);
                            alert(thrownError);
                          },
                        });
                      },
                    };

                    if (type != 1 || (bar_id != 3 && bar_id != 4) || nalog_id != 272) {
                      carglass_export = "";
                    }
                    if (type != 1 || (bar_id != 3 && bar_id != 4) || companyEnpal != 1) {
                      enpal_export = "";
                    }
                    if (type != 1 || (bar_id != 3) || nalog_id != 301) {
                      interview_export = "";
                    }
                    $("#table_list_from_projects").DataTable({
                      destroy: true,
                      responsive: true,
                      dom: "Blfrtip",
                      buttons: ["csvHtml5", "excelHtml5", carglass_export, enpal_export, interview_export],
                      pageLength: 10,
                      processing: true,
                      serverSide: true,
                      order: [[0, "desc"]],
                      lengthMenu: [
                        [10, 25, 50, -1],
                        [10, 25, 50, "All"],
                      ],
                      ajax: {
                        url: "serverside.php?page=list_from_project",
                        type: "POST",
                        data: {
                          bar_id: bar_id,
                          nalog_id: nalog_id,
                          type: type,
                          partner_id: partner_id,
                        },

                        error: function (data) {
                          $(".list-grid-error").html("");
                          $("#list-grid_processing").css("display", "none");
                        },
                      },
                    });
                    removeLoader();
                    $("#select_appointment").prop("disabled", false);
                    $("#select_partner").prop("disabled", false);
                    $("#select_working_position").prop("disabled", false);

                    $(".goToDash")
                      .unbind("click")
                      .bind("click", handleBackToDashboard);
                    $(".progress_bar_click")
                      .unbind("click")
                      .bind("click", handleProgressBarClick);
                  });
                  $(
                    "#select_appointment, #select_partner, #select_working_psition"
                  ).on("change", function () {
                    getLoaderBig();
                    var appointment_ids = $("#select_appointment").val();
                    var partner_ids = $("#select_partner").val();
                    var working_positions = $("#select_working_position").val();
                    var carglass_export = {
                      text: "Carglass export",
                      action: function (e, dt, node, config) {
                        getLoaderBig();
                        $.ajax({
                          url: "export_carglass.php",
                          type: "POST",
                          dataType: "html",
                          data: {
                            bar_id: bar_id,
                            nalog_id: nalog_id,
                            type: type,
                          },
                          success: function (data) {
                            $("#to_append_export").html("");
                            $("#to_append_export").append(data);
                            $("#to_append_export").table2excel({
                              exclude: ".noExl",
                              name: "Export Carglass",
                              filename: "Export Carglass",
                              fileext: ".xls",
                            });
                            removeLoader();
                          },
                          error: function (xhr, ajaxOptions, thrownError) {
                            alert(xhr.status);
                            alert(thrownError);
                          },
                        });
                      },
                    };
                    var enpal_export = {
                      text: "Enpal export",
                      action: function (e, dt, node, config) {
                        getLoaderBig();
                        $.ajax({
                          url: "export_enpal.php",
                          type: "POST",
                          dataType: "html",
                          data: {
                            bar_id: bar_id,
                            nalog_id: nalog_id,
                            type: type,
                          },
                          success: function (data) {
                            $("#to_append_enpal_export").html("");
                            $("#to_append_enpal_export").append(data);
                            $("#to_append_enpal_export").table2excel({
                              exclude: ".noExl",
                              name: "Export Enpal",
                              filename: "Export Enpal",
                              fileext: ".xls",
                            });
                            removeLoader();
                          },
                          error: function (xhr, ajaxOptions, thrownError) {
                            alert(xhr.status);
                            alert(thrownError);
                          },
                        });
                      },
                    };

                    var interview_export = {
                      text: "Interview export",
                      action: function (e, dt, node, config) {
                        getLoaderBig();
                        $.ajax({
                          url: "export_interview.php",
                          type: "POST",
                          dataType: "html",
                          data: {
                            bar_id: bar_id,
                            nalog_id: nalog_id,
                            type: type,
                          },
                          success: function (data) {
                            $("#to_append_interview_export").html("");
                            $("#to_append_interview_export").append(data);
                            $("#to_append_interview_export").table2excel({
                              exclude: ".noExl",
                              name: "Export Interview",
                              filename: "Export Interview",
                              fileext: ".xls",
                            });
                            removeLoader();
                          },
                          error: function (xhr, ajaxOptions, thrownError) {
                            alert(xhr.status);
                            alert(thrownError);
                          },
                        });
                      },
                    };

                    if (type != 1 ||  (bar_id != 3 && bar_id != 4) || nalog_id != 272) {
                      carglass_export = "";
                    }
                    if (type != 1 || (bar_id != 3 && bar_id != 4) || companyEnpal != 1) {
                      enpal_export = "";
                    }
                    if (type != 1 || (bar_id != 3) || nalog_id != 301) {
                      interview_export = "";
                    }
                    $("#btn_expoprt_table").data(
                      "appointments",
                      appointment_ids
                    );
                    $("#btn_expoprt_table").data("partners", partner_ids);
                    $("#select_appointment").prop("disabled", true);
                    $("#select_partner").prop("disabled", true);
                    $("#select_working_position").prop("disabled", true);

                    $("#table_list_from_projects").DataTable({
                      destroy: true,
                      responsive: true,
                      dom: "Blfrtip",
                      buttons: ["csvHtml5", "excelHtml5", carglass_export, enpal_export, interview_export],
                      pageLength: 10,
                      processing: true,
                      serverSide: true,
                      order: [[0, "desc"]],
                      lengthMenu: [
                        [10, 25, 50, -1],
                        [10, 25, 50, "All"],
                      ],
                      ajax: {
                        url: "serverside.php?page=list_from_project",
                        type: "POST",
                        data: {
                          bar_id: bar_id,
                          partner_id: partner_id,
                          nalog_id: nalog_id,
                          type: type,
                          appointment_ids: appointment_ids,
                          partner_ids: partner_ids,
                          working_positions: working_positions,
                        },

                        error: function (data) {
                          $(".list-grid-error").html("");
                          $("#list-grid_processing").css("display", "none");
                        },
                      },
                    });
                    removeLoader();
                    $("#select_appointment").prop("disabled", false);
                    $("#select_partner").prop("disabled", false);
                    $("#select_working_position").prop("disabled", false);

                    $(".goToDash")
                      .unbind("click")
                      .bind("click", handleBackToDashboard);
                    $(".progress_bar_click")
                      .unbind("click")
                      .bind("click", handleProgressBarClick);
                  });
                  var carglass_export = {
                    text: "Carglass export",
                    action: function (e, dt, node, config) {
                      getLoaderBig();
                      $.ajax({
                        url: "export_carglass.php",
                        type: "POST",
                        dataType: "html",
                        data: {
                          bar_id: bar_id,
                          nalog_id: nalog_id,
                          type: type,
                        },
                        success: function (data) {
                          $("#to_append_export").html("");
                          $("#to_append_export").append(data);
                          $("#to_append_export").table2excel({
                            exclude: ".noExl",
                            name: "Export Carglass",
                            filename: "Export Carglass",
                            fileext: ".xls",
                          });
                          removeLoader();
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                          alert(xhr.status);
                          alert(thrownError);
                        },
                      });
                    },
                  };
                  var enpal_export = {
                    text: "Enpal export",
                    action: function (e, dt, node, config) {
                      getLoaderBig();
                      $.ajax({
                        url: "export_enpal.php",
                        type: "POST",
                        dataType: "html",
                        data: {
                          bar_id: bar_id,
                          nalog_id: nalog_id,
                          type: type,
                        },
                        success: function (data) {
                          $("#to_append_enpal_export").html("");
                          $("#to_append_enpal_export").append(data);
                          $("#to_append_enpal_export").table2excel({
                            exclude: ".noExl",
                            name: "Export Enpal",
                            filename: "Export Enpal",
                            fileext: ".xls",
                          });
                          removeLoader();
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                          alert(xhr.status);
                          alert(thrownError);
                        },
                      });
                    },
                  };

                  var interview_export = {
                    text: "Interview export",
                    action: function (e, dt, node, config) {
                      getLoaderBig();
                      $.ajax({
                        url: "export_interview.php",
                        type: "POST",
                        dataType: "html",
                        data: {
                          bar_id: bar_id,
                          nalog_id: nalog_id,
                          type: type,
                        },
                        success: function (data) {
                          $("#to_append_interview_export").html("");
                          $("#to_append_interview_export").append(data);
                          $("#to_append_interview_export").table2excel({
                            exclude: ".noExl",
                            name: "Export Interview",
                            filename: "Export Interview",
                            fileext: ".xls",
                          });
                          removeLoader();
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                          alert(xhr.status);
                          alert(thrownError);
                        },
                      });
                    },
                  };

                  if (type != 1 ||  (bar_id != 3 && bar_id != 4) || nalog_id != 272) {
                    carglass_export = "";
                  }
                  
                  if (type != 1 || (bar_id != 3 && bar_id != 4) || companyEnpal != 1) {
                    enpal_export = "";
                  }
                  if (type != 1 || (bar_id != 3) || nalog_id != 301) {
                    interview_export = "";
                  }
                  $("#table_list_from_projects").DataTable({
                    destroy: true,
                    responsive: true,
                    dom: "Blfrtip",
                    buttons: ["csvHtml5", "excelHtml5", carglass_export, enpal_export, interview_export],
                    pageLength: 10,
                    processing: true,
                    serverSide: true,
                    order: [[0, "desc"]],
                    lengthMenu: [
                      [10, 25, 50, -1],
                      [10, 25, 50, "All"],
                    ],
                    ajax: {
                      url: "serverside.php?page=list_from_project",
                      type: "POST",
                      data: {
                        bar_id: bar_id,
                        partner_id: partner_id,
                        nalog_id: nalog_id,
                        type: type,
                        appointment_ids: null,
                      },
                      error: function (data) {
                        $(".list-grid-error").html("");
                        $("#list-grid_processing").css("display", "none");
                      },
                    },
                  });
                  $(".goToDash")
                    .unbind("click")
                    .bind("click", handleBackToDashboard);
                  $(".progress_bar_click")
                    .unbind("click")
                    .bind("click", handleProgressBarClick);

                  removeLoader();
                });
            },
            error: function (xhr, ajaxOptions, thrownError) {
              alert(xhr.status);
              alert(thrownError);
            },
          });
        });
      });
    });
  });
}

function handleBackToDashboard() {
  back_to_type = $(".goToDash").attr("back_to_type");
  user_type = $(".goToDash").attr("user_type");
  nalog_id = $(".goToDash").attr("nalog_id");
  partner_id = $(".goToDash").attr("partner_id");

  if (
    $("#view_choice_superadmin").hasClass("view_choice_superadmin_selected")
  ) {
    user_type = 1;
  } else if ($("#view_choice_admin").hasClass("view_choice_admin_selected")) {
    user_type = 2;
  }
  getLoaderBig();
  $.ajax({
    url: "ajax.php?action=get_progress_bar",
    type: "POST",
    dataType: "json",
    data: {
      nalog_ids: nalog_id,
      type: back_to_type,
      partner_ids: partner_id,
    },
    success: function (response_progress_bar) {
      $("#applied_candidates")
        .fadeOut(200)
        .empty()
        .append(response_progress_bar["applied_candidates"])
        .fadeIn();
      for (
        var i = 0;
        i < parseInt(response_progress_bar["bar_type"].length);
        i++
      ) {
        if (response_progress_bar["returns_status_bar"]) {
          progress_bar_text = response_progress_bar["bar_text"][i];
        } else {
          progress_bar_text =
            response_progress_bar["bar_text"][i] +
            " " +
            response_progress_bar["bar_value"][i];
        }
        $(response_progress_bar["bar_type"][i]).circleProgress({
          max: response_progress_bar["bar_max"][i],
          value: response_progress_bar["bar_value"][i],
          textFormat: function (value, max) {
            return progress_bar_text;
          },
        });
      }
    },
    error: function (xhr, ajaxOptions, thrownError) {
      alert(xhr.status);
      alert(thrownError);
    },
  });
  $("#db_list_candidates").hide("slide", function () {
    $("#db_view_choice").show("slide", 250, function () {
      $("#db_progress_bar").show("blind", 250, function () {
        $("#db_list_nalozi").show("fade", 250, function () {
          removeLoader();
          if (back_to_type == 1 || user_type == 1) {
            $("#db_list_partners").show("slide", 250);
            $("#candidates_list_table_container").empty();
          }
        });
      });
    });
  });
}

function removeHighlightedPartners() {
  $(".row_handle_partner_click>td.table_text").removeClass(
    "table_partner_selected"
  );
  $(".row_handle_partner_click>td.table_partner_icon_column").removeClass(
    "table_partner_icon_selected"
  );
}

function handleNalogClick() {
  var nalog_id = $(this).attr("nalog_id");
  var partner_id = 3;
  var type = $(".progress_bar_click").attr("type");
  var bar_id = 0;
  var continue_flag = true;

  // $('#db_progress_bar').children().children().each(function(){
  // 	if($(this).attr('type') == 1 || $(this).attr('type') == 2){
  // 		progress_bar_type = $(this).attr('type');
  // 	}
  // });
  var nalog_ids = [];
  $(".table_nalog_selected").each(function () {
    nalog_ids.push($(this).parent().attr("nalog_id"));
  });
  var partner_ids = [];
  $(".table_partner_selected").each(function () {
    partner_ids.push($(this).parent().attr("partner_id"));
  });

  $(".row_handle_partner_click")
    .unbind("click")
    .bind("click", handlePartnerClick);
  if (!$(this).children(".table_text").hasClass("table_nalog_selected")) {
    // removeHighlightedPartners();
    $(this).children(".table_text").addClass("table_nalog_selected");
    $(this)
      .children(".table_nalog_icon_column")
      .addClass("table_nalog_icon_selected");
    nalog_ids.push($(this).attr("nalog_id"));
  } else if (nalog_ids.length != 1) {
    $(this).children(".table_text").removeClass("table_nalog_selected");
    $(this)
      .children(".table_nalog_icon_column")
      .removeClass("table_nalog_icon_selected");
    nalog_ids.splice(nalog_ids.indexOf($(this).attr("nalog_id")), 1);
  } else {
    if ($("#box_partners").hasClass("partners_list_selected")) {
      continue_flag = true;
      $("#box_nalozi").addClass("nalozi_list_selected");
      $("#box_partners").removeClass("partners_list_selected");
    } else {
      continue_flag = false;
    }
  }

  if (continue_flag) {
    getLoaderBig();
    var flag_bind_progress_bars = 0;
    if (nalog_ids.length != 1) {
      $(".progress_bar_click").each(function () {
        $(this).unbind("click");
      });
    } else {
      var flag_bind_progress_bars = 0;
    }
    if (partner_ids.length == 0) {
      partner_ids = 0;
    }

    var progress_bar_type = 0;
    $("#db_progress_bar")
      .children()
      .children()
      .each(function () {
        if ($(this).attr("type") == 1 || $(this).attr("type") == 2) {
          progress_bar_type = $(this).attr("type");
        }
      });
    var get_partner_list = $.ajax({
      url: "ajax.php?action=get_list_partners",
      type: "POST",
      dataType: "html",
      data: {
        nalog_ids: nalog_ids,
        partner_ids: partner_ids,
      },
      error: function (xhr, ajaxOptions, thrownError) {
        alert(xhr.status);
        alert(thrownError);
      },
    });

    // var  get_progress_bar =
    // 	$.ajax({
    // 		url: 'ajax.php?action=get_progress_bar',
    // 		type: 'POST',
    // 		dataType: 'json',
    // 		data:{
    // 			'nalog_ids'		: nalog_ids,
    // 			'type' 			: type,
    // 			'partner_ids'	: partner_ids
    // 		},
    // 		error: function (xhr, ajaxOptions, thrownError) {
    // 			alert(xhr.status);
    // 			alert(thrownError);
    // 		}
    // 	});

    $("#box_nalozi").addClass("nalozi_list_selected");
    $("#box_partners").removeClass("partners_list_selected");
    $.when(get_partner_list).then(function (response_partner_list) {
      $("#db_list_partners").empty().append(response_partner_list);

      var nalog_ids = [];
      $(".table_nalog_selected").each(function () {
        nalog_ids.push($(this).parent().attr("nalog_id"));
      });

      $(".row_handle_partner_click")
        .unbind("click")
        .bind("click", handlePartnerClick);
      $(".table_partner_icon_column").each(function () {
        if (nalog_ids.includes($(this).parent().attr("nalog_id"))) {
          $(this).addClass("table_partner_icon_selected");
          $(this).next().addClass("table_partner_selected");
        }
      });

      var partner_ids = [];
      $(".table_partner_selected").each(function () {
        partner_ids.push($(this).parent().attr("partner_id"));
      });
      if (type == 1) {
        partner_ids = 0;
      }

      $.ajax({
        url: "ajax.php?action=get_progress_bar",
        type: "POST",
        dataType: "json",
        data: {
          nalog_ids: nalog_ids,
          type: type,
          partner_ids: partner_ids,
        },
        success: function (response_progress_bar) {
          $("#applied_candidates")
            .empty()
            .append(response_progress_bar["applied_candidates"]);
          for (
            var i = 0;
            i < parseInt(response_progress_bar["bar_type"].length);
            i++
          ) {
            if (response_progress_bar["returns_status_bar"]) {
              progress_bar_text = response_progress_bar["bar_text"][i];
            } else {
              progress_bar_text =
                response_progress_bar["bar_text"][i] +
                " " +
                response_progress_bar["bar_value"][i];
            }
            $(response_progress_bar["bar_type"][i]).circleProgress({
              max: response_progress_bar["bar_max"][i],
              value: response_progress_bar["bar_value"][i],
              textFormat: function (value, max) {
                return progress_bar_text;
              },
            });
          }
          if (type == 1 && nalog_ids.length == 1) {
            $(".progress_bar_click").each(function () {
              $(this).unbind("click").bind("click", handleProgressBarClick);
            });
          } else {
            $(".progress_bar_click").each(function () {
              $(this).unbind("click");
            });
          }
          if (type != 1 && partner_ids.length == 1) {
            $(".progress_bar_click").each(function () {
              $(this).unbind("click").bind("click", handleProgressBarClick);
            });
          } else if (type != 1 && partner_ids.length != 0) {
            $(".progress_bar_click").each(function () {
              $(this).unbind("click");
            });
          }

          removeLoader();
        },
        error: function (xhr, ajaxOptions, thrownError) {
          alert(xhr.status);
          alert(thrownError);
        },
      });
    });
  }
}

function handlePartnerClick() {
  var partner_ids;
  var nalog_ids;
  var type = $(".progress_bar_click").attr("type");
  var bar_id = 0;
  var continue_flag = true;
  var my_company_flag = false;
  var nalog_ids = [];
  var partner_ids = [];

  // var nalog_ids = [];
  // $('.table_partner_selected').each(function(){
  // 	nalog_ids.push($(this).parent().attr('nalog_id'));
  // });
  // var partner_ids = [];
  $(".table_partner_selected").each(function () {
    partner_ids.push($(this).parent().attr("partner_id"));
  });

  var progress_bar_type = 0;
  $("#db_progress_bar")
    .children()
    .children()
    .each(function () {
      if ($(this).attr("type") == 1 || $(this).attr("type") == 2) {
        progress_bar_type = $(this).attr("type");
      }
    });

  if (!$(this).children(".table_text").hasClass("table_partner_selected")) {
    $(this).children(".table_text").addClass("table_partner_selected");
    $(this)
      .children(".table_partner_icon_column ")
      .addClass("table_partner_icon_selected");
    // partner_ids.push($(this).attr('partner_id'));
    // nalog_ids.push($(this).attr('nalog_id'));
  } else if (partner_ids.length != 1) {
    $(this).children(".table_text").removeClass("table_partner_selected");
    $(this)
      .children(".table_partner_icon_column ")
      .removeClass("table_partner_icon_selected");
    // partner_ids.splice(partner_ids.indexOf($(this).attr('partner_id')),1);
    // nalog_ids.splice(partner_ids.indexOf($(this).attr('nalog_id')),1);
  } else {
    if ($("#box_nalozi").hasClass("nalozi_list_selected")) {
      continue_flag = true;
      $("#box_partners").addClass("partners_list_selected");
      $("#box_nalozi").removeClass("nalozi_list_selected");
    } else if ($("#box_partneri").hasClass("partners_list_selected")) {
      continue_flag = true;
      $("#box_partners").removeClass("partners_list_selected");
      $("#box_nalozi").addClass("nalozi_list_selected");
    } else {
      continue_flag = false;
    }
  }

  var partner_ids = [];
  $(".table_partner_selected").each(function () {
    nalog_ids.push($(this).parent().attr("nalog_id"));
  });
  $(".table_partner_selected").each(function () {
    partner_ids.push($(this).parent().attr("partner_id"));
  });

  $(".table_partner_selected").each(function () {
    if ($(this).hasClass("table_partner_my_company")) {
      my_company_flag = true;
    }
  });
  if (continue_flag) {
    getLoaderBig();

    $("#box_partners").addClass("partners_list_selected");
    $("#box_nalozi").removeClass("nalozi_list_selected");
    $.ajax({
      url: "ajax.php?action=get_progress_bar",
      type: "POST",
      dataType: "json",
      data: {
        type: type,
        partner_ids: partner_ids,
        nalog_ids: nalog_ids,
      },
      success: function (response) {
        for (var i = 0; i < parseInt(response["bar_type"].length); i++) {
          $(response["bar_type"][i]).circleProgress({
            max: response["bar_max"][i],
            value: response["bar_value"][i],
            textFormat: function (value, max) {
              return response["bar_text"][i];
            },
          });
        }
        if (type != 1 && partner_ids.length != 1) {
          $(".progress_bar_click").each(function () {
            $(this).unbind("click");
          });
        } else {
          if (my_company_flag) {
            $(".progress_bar_click").each(function () {
              $(this).unbind("click").bind("click", handleProgressBarClick);
            });
          }
        }
        removeLoader();
      },

      error: function (xhr, ajaxOptions, thrownError) {
        alert(xhr.status);
        alert(thrownError);
      },
    });
  }
}

function handleRejectCandidateModalOpen(element) {
  var nalog_id = $(element).attr("nalog_id");
  var candidate_id = $(element).attr("candidate_id");
  var pap_id = $(element).attr("pap_id");
  $("#modalOdbij").modal("show");
  $("#reject_candidate").attr("candidate_id", candidate_id);
  $("#reject_candidate").attr("nalog_id", nalog_id);
  $("#reject_candidate").attr("pap_id", pap_id);
}

function handleHireCandidateModalOpen(element) {
  var nalog_id = $(element).attr("nalog_id");
  var candidate_id = $(element).attr("candidate_id");
  $("#modalAccept").modal("show");
  $("#hire_candidate").attr("candidate_id", candidate_id);
  $("#hire_candidate").attr("nalog_id", nalog_id);
}

function handleAsignCandidateModalOpen(element) {
  $("#partnerIDVal").selectpicker();
  $("#partnerLocation").selectpicker();
  $("#partnerPosition").selectpicker();
  var nalog_id = $(element).attr("nalog_id");
  var candidate_id = $(element).attr("candidate_id");
  $("#modalPartner").modal("show");
  $("#assign_candidate_to_partner").attr("candidate_id", candidate_id);
  $("#assign_candidate_to_partner").attr("nalog_id", nalog_id);
}

function handleRejectCandidateClick() {
  var nalog_id = $(this).attr("nalog_id");
  var candidate_id = $(this).attr("candidate_id");
  var pap_id = $(this).attr("pap_id");
  var razlog_odbijanja_id = $("#razlogOdbijanjaId").val();

  getAccessControl(
    nalog_id,
    candidate_id,
    null,
    2,
    function (response_access_control) {
      var reminder_status = response_access_control["status"];
      var reminder_id = response_access_control["id"];
      var reminder_assigned_user = response_access_control["assigned"];
      var reminder_is_logged_user = response_access_control["isLogged"];
      var reminder_response_message = response_access_control["message"];
      var reminder_response_title = response_access_control["title"];

      if (reminder_status == 101 || reminder_status == 103 || reminder_status == 1 || reminder_status == 2) {
        $(this).prop("disabled", true);
        if (razlog_odbijanja_id == "") {
          $(this).prop("disabled", false);
          $("#razlogOdbijanjaIdEffect").effect("shake");
          $("#razlogOdbijanjaIdEffect").effect("bounce");
        } else {
          if (reminder_status == 103 || reminder_status == 1 || reminder_status == 2) {
            updateReminderStatus(
              reminder_id,
              3,
              function (response_update_reminder) {
                var update_status = response_update_reminder["status"];
                var update_message = response_update_reminder["message"];
                if (update_status == 3) {
                  showToast(update_message, reminder_response_title);
                }
              }
            );
          }
          $.ajax({
            url: "ajax.php?action=reject_candidate",
            type: "POST",
            dataType: "html",
            data: {
              kandidat_id: candidate_id,
              nalog_id: nalog_id,
              pap_id: pap_id,
              razlog_odbijanja_id: razlog_odbijanja_id,
            },
            success: function (response) {
              $("#modalOdbij").modal("hide");
              $("#child_reject_candidate_" + candidate_id)
                .parent()
                .parent()
                .parent()
                .parent()
                .css("background-color", "#FCE9EB");
              $("#child_reject_candidate_" + candidate_id)
                .parent()
                .parent()
                .fadeOut(200, function () {
                  $("#candidate_profile_link_" + candidate_id)
                    .unbind("click")
                    .bind("click", function (e) {
                      e.preventDefault();
                    });
                });
            },
            error: function (xhr, ajaxOptions, thrownError) {
              alert(xhr.status);
              alert(thrownError);
            },
          });
        }
      } else if (reminder_status == 104) {
        showToast(reminder_response_message, reminder_response_title);
      } else {
        showToast(reminder_response_message, reminder_response_title);
      }
    }
  );
}

function handleHireCandidateClick() {
  var nalog_id = $(this).attr("nalog_id");
  var candidate_id = $(this).attr("candidate_id");

  getAccessControl(
    nalog_id,
    candidate_id,
    null,
    2,
    function (response_access_control) {
      var reminder_status = response_access_control["status"];
      var reminder_id = response_access_control["id"];
      var reminder_assigned_user = response_access_control["assigned"];
      var reminder_is_logged_user = response_access_control["isLogged"];
      var reminder_response_message = response_access_control["message"];
      var reminder_response_title = response_access_control["title"];

      if (reminder_status == 101 || reminder_status == 103 || reminder_status == 1 || reminder_status == 2) {
        $(this).prop("disabled", true);
        $.ajax({
          url: "ajax.php?action=hire_candidate",
          type: "POST",
          dataType: "html",
          data: {
            kandidat_id: candidate_id,
            nalog_id: nalog_id,
          },
          success: function (response) {
            $("#modalAccept").modal("hide");
            $("#child_hire_candidate_" + candidate_id)
              .parent()
              .parent()
              .parent()
              .parent()
              .css("background-color", "#C9ECC0");
            $("#child_hire_candidate_" + candidate_id)
              .parent()
              .parent()
              .fadeOut(200, function () {
                $("#candidate_profile_link_" + candidate_id)
                  .unbind("click")
                  .bind("click", function (e) {
                    e.preventDefault();
                  });
                $("#modalPartner").modal("show");
                $("#assign_candidate_to_partner").attr(
                  "candidate_id",
                  candidate_id
                );
                $("#assign_candidate_to_partner").attr("nalog_id", nalog_id);
              });
            
            /* UGASI GLOSSU */
            /*
            fetch(`ajax.php?action=check_glossa&cid=${candidate_id}`)
              .then((res) => res.json())
              .then((data) => {
                if (data.glossa == 1) {
                  var myHeaders = new Headers();
                  myHeaders.append(
                    "Authorization",
                    "Bearer 8d81150a-8cd6-4a8a-9279-d4a4a45ccdea"
                  );

                  var requestOptions = {
                    method: "GET",
                    headers: myHeaders,
                    redirect: "follow",
                  };

                  fetch(
                    `https://glossa-crm.com/api/Person/UpdateStatus/${candidate_id}`,
                    requestOptions
                  )
                    .then((response) => response.json())
                    .then((result) => {
                      let response_msg = result.message;
                      let response_status = result.code;
                      fetch(
                        `ajax.php?action=make_glossa_lead&cid=${candidate_id}&msg=${response_msg}&status=${response_status}`
                      );
                      console.log(result);
                    })
                    .catch((error) => console.log("error", error));
                }
              });
            */
          },
          error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          },
        });
        if (reminder_status == 103 || reminder_status == 1 || reminder_status == 2) {
          updateReminderStatus(
            reminder_id,
            3,
            function (response_update_reminder) {
              var update_status = response_update_reminder["status"];
              var update_message = response_update_reminder["message"];
              if (update_status == 3) {
                showToast(update_message, reminder_response_title);
              }
            }
          );
        }
      } else if (reminder_status == 104) {
        showToast(reminder_response_message, reminder_response_title);
      } else {
        showToast(reminder_response_message, reminder_response_title);
      }
    }
  );
}

function handleAssignCandidateToPartnerClick() {
  var partnerIDVal = $("#partnerIDVal").val();
  var partnerLocation = $("#partnerLocation").val();
  var partnerLocationOstalo = $("#partnerLocationOstalo").val();
  var partnerPosition = $("#partnerPosition").val();
  var partnerPositionOstalo = $("#partnerPositionOstalo").val();
  var partnerSalary = $("#partnerSalary").val();
  var candidate_id = $(this).attr("candidate_id");
  var nalog_id = $(this).attr("nalog_id");
  getAccessControl(
    nalog_id,
    candidate_id,
    null,
    3,
    function (response_access_control) {
      var reminder_status = response_access_control["status"];
      var reminder_id = response_access_control["id"];
      var reminder_assigned_user = response_access_control["assigned"];
      var reminder_is_logged_user = response_access_control["isLogged"];
      var reminder_response_message = response_access_control["message"];
      var reminder_response_title = response_access_control["title"];

      if (reminder_status == 101 || reminder_status == 103 || reminder_status == 1 || reminder_status == 2) {
        $(this).prop("disabled", true);

        if (partnerIDVal == "") {
          $(this).prop("disabled", false);
          $("#partnerIDVal").parent().effect("bounce");
        } else {
          $.ajax({
            url: "ajax.php?action=assign_candidate_to_partner",
            type: "POST",
            dataType: "html",
            data: {
              partnerIDVal: partnerIDVal,
              partnerLocation: partnerLocation,
              partnerLocationOstalo: partnerLocationOstalo,
              partnerPosition: partnerPosition,
              partnerPositionOstalo: partnerPositionOstalo,
              partnerSalary: partnerSalary,
              candidate_id: candidate_id,
              nalog_id: nalog_id,
            },
            success: function (response) {
              $("#modalPartner").modal("hide");
              $("#child_assign_partner" + candidate_id)
                .parent()
                .empty()
                .append(
                  '<p class = "text-center"><i class="fa fa-check" style = "color: #1289C8;font-size: 20px!important;"aria-hidden="true"></i></p>'
                );
            },
            error: function (xhr, ajaxOptions, thrownError) {
              alert(xhr.status);
              alert(thrownError);
            },
          });
          if(reminder_status == 103 || reminder_status == 1 || reminder_status == 2){
            updateReminderStatus(
              reminder_id,
              3,
              function (response_update_reminder) {
                var update_status = response_update_reminder["status"];
                var update_message = response_update_reminder["message"];
                if (update_status == 3) {
                  showToast(update_message, reminder_response_title);
                }
              }
            );
          }
        }
      } else if (reminder_status == 104) {
        showToast(reminder_response_message, reminder_response_title);
      } else {
        showToast(reminder_response_message, reminder_response_title);
      }
    }
  );
}

function handleEditCandidatePartnerDataModalOpen(element) {
  if (
    $(element).hasClass("click_edit_candidate") ||
    $(element).hasClass("click_edit_candidate_no_style")
  ) {
    var nalog_id = $(element).attr("nalog_id");
    var candidate_id = $(element).attr("candidate_id");
    var partner_id = $(element).attr("partner_id");
    var candidate_location = $(element).attr("candidate_location");
    var candidate_position = $(element).attr("candidate_position");
    var candidate_salary = $(element).attr("candidate_salary");
    $("#editPartnerIDVal").val(partner_id).attr("selected", "selected");
    $("#editPartnerLocation")
      .val(candidate_location)
      .attr("selected", "selected");
    $("#editPartnerPosition")
      .val(candidate_position)
      .attr("selected", "selected");
    $("#editPartnerSalary").val(candidate_salary);
    if ($("#editPartnerLocation option:selected").val() == undefined) {
      $("#editHideShowLocOstalo").fadeIn(200);
      $("#editPartnerLocation").val("0").attr("selected", "selected");
      $("#editPartnerLocationOstalo").val(candidate_location);
    }
    if ($("#editPartnerPosition option:selected").val() == undefined) {
      $("#editHideShowPositionOstalo").fadeIn(200);
      $("#editPartnerPosition").val("0").attr("selected", "selected");
      $("#editPartnerPositionOstalo").val(candidate_position);
    }
    $(".selectpicker").selectpicker("refresh");
    $("#modalEditCandidatePartnerData").modal("show");
    $("#edit_candidate_partner_data").attr("candidate_id", candidate_id);
    $("#edit_candidate_partner_data").attr("nalog_id", nalog_id);
    $("#edit_candidate_partner_data").attr("partner_id", partner_id);
  } else {
    $(".click_edit_candidate_disabled").effect("shake");
    // $('.click_edit_candidate_disabled').effect('highlight');
  }
}

function handleEditCandidatePartnerData() {
  var nalog_id = $(this).attr("nalog_id");
  var candidate_id = $(this).attr("candidate_id");
  var location = $(this).attr("location");

  var partnerIDVal = $("#editPartnerIDVal").val();
  var partnerLocation = $("#editPartnerLocation").val();
  var partnerLocationOstalo = $("#editPartnerLocationOstalo").val();
  var partnerPosition = $("#editPartnerPosition").val();
  var partnerPositionOstalo = $("#editPartnerPositionOstalo").val();
  var partnerSalary = $("#editPartnerSalary").val();

  var flag_all_fields_inserted = true;
  if (partnerIDVal == "") {
    flag_all_fields_inserted = false;
  }
  if (partnerSalary == "") {
    flag_all_fields_inserted = false;
  }
  if (
    partnerLocation == "" ||
    (partnerLocation == "0" && partnerLocationOstalo == "")
  ) {
    flag_all_fields_inserted = false;
  }
  if (
    partnerPosition == "" ||
    (partnerPosition == "0" && partnerPositionOstalo == "")
  ) {
    flag_all_fields_inserted = false;
  }

  getAccessControl(
    nalog_id,
    candidate_id,
    partnerIDVal,
    4,
    function (response_access_control) {
      var reminder_status = response_access_control["status"];
      var reminder_id = response_access_control["id"];
      var reminder_assigned_user = response_access_control["assigned"];
      var reminder_is_logged_user = response_access_control["isLogged"];
      var reminder_response_message = response_access_control["message"];
      var reminder_response_title = response_access_control["title"];

      if (reminder_status == 101 || reminder_status == 103 || reminder_status == 1 || reminder_status == 2) {
        $(this).prop("disabled", true);

        if (partnerIDVal == "") {
          $(this).prop("disabled", false);
          $("#editPartnerIDVal").parent().effect("bounce");
        } else {
          getLoaderBig();

          $(this).prop("disabled", true);
          $.ajax({
            url: "ajax.php?action=assign_candidate_to_partner",
            type: "POST",
            dataType: "html",
            data: {
              partnerIDVal: partnerIDVal,
              partnerLocation: partnerLocation,
              partnerLocationOstalo: partnerLocationOstalo,
              partnerPosition: partnerPosition,
              partnerPositionOstalo: partnerPositionOstalo,
              partnerSalary: partnerSalary,
              candidate_id: candidate_id,
              nalog_id: nalog_id,
            },
            success: function (response) {
              $("#modalEditCandidatePartnerData").on(
                "hidden.bs.modal",
                function (e) {
                  if (location == "l") {
                    $("#child_edit_candidate" + candidate_id)
                      .parent()
                      .empty()
                      .append(
                        '<p class = "text-center"><i class="fa fa-check" style = "color: #ffe569;font-size: 20px!important;"aria-hidden="true"></i></p>'
                      );
                    $("#child_edit_candidate" + candidate_id)
                      .parent()
                      .parent()
                      .fadeOut(200);
                    removeLoader();
                  } else if (location == "p") {
                    $(".click_edit_candidate").effect("highlight");
                    $(".click_edit_candidate").effect("highlight");
                    window.location.reload();
                  }
                }
              );
              $("#modalEditCandidatePartnerData").modal("hide");
            },
            error: function (xhr, ajaxOptions, thrownError) {
              alert(xhr.status);
              alert(thrownError);
            },
          });
          if ((reminder_status == 103 || reminder_status == 1 || reminder_status == 2) && flag_all_fields_inserted === true) {
            updateReminderStatus(
              reminder_id,
              3,
              function (response_update_reminder) {
                var update_status = response_update_reminder["status"];
                var update_message = response_update_reminder["message"];
                if (update_status == 3) {
                  showToast(update_message, reminder_response_title);
                }
              }
            );
          }
        }
        
      } else if (reminder_status == 104) {
        showToast(reminder_response_message, reminder_response_title);
      } else if (reminder_status == 3 && !flag_all_fields_inserted) {
        $("#modalEditCandidatePartnerData").effect("shake");
      } else {
        showToast(reminder_response_message, reminder_response_title);
      }
    }
  );
}

function handleManageContractSentModalOpen(element) {
  let nalog_id = $(element).attr("nalog_id");
  let candidate_key = $(element).attr("candidate_id");
  let partner_id = $(element).attr("partner_id");
  let location = $(element).attr("location");
  let reminder_type = 5;
  //console.log(nalog_id, candidate_key, candidate_key, partner_id, reminder_type);

  getAccessControl(
    nalog_id,
    candidate_key,
    partner_id,
    reminder_type,
    function (response_access_control) {
      let reminder_status = response_access_control["status"];
      let reminder_id = response_access_control["id"];
      let reminder_assigned_user = response_access_control["assigned"];
      let reminder_is_logged_user = response_access_control["isLogged"];
      let reminder_response_message = response_access_control["message"];
      let reminder_response_title = response_access_control["title"];

      if (
        reminder_status == 101 ||
        reminder_status == 1 ||
        (reminder_status == 2 && reminder_is_logged_user == 1) ||
        (reminder_status == 3 && reminder_is_logged_user == 1)
      ) {
        // If task exists and can be assigned to current user
        if (reminder_status == 1) {
          // Assign task to current user
          updateReminderStatus(
            reminder_id,
            2,
            function (responseUpdateReminder) {
              let updateStatus = responseUpdateReminder["status"];
              let updateMessage = responseUpdateReminder["message"];
              if (updateStatus == 2) {
                showToast(updateMessage, reminder_response_title);
              }
            }
          );
        }
        $.ajax({
          url: "ajax.php?action=load_modal_manage_documents",
          type: "POST",
          dataType: "html",
          data: {
            nalog_id: nalog_id,
            partner_id: partner_id,
            candidate_id: candidate_key,
            location: location,
          },
          success: function (response) {
            $("#to_append_to_modal_manage_documents").empty().append(response);
            $("#modalManageDocuments").modal("show");

            // If form empty, make it functional
            if (!$("#contractDateSent").val()) {
              $("#contractDateSent").flatpickr({
                dateFormat: "Y-m-d",
              });
              // Hacky way to make flatpickr required
              $("#contractDateSent").prop("readonly", false);
              $("#contractDateSent")
                .unbind("focus")
                .bind("focus", ({ currentTarget }) => $(currentTarget).blur());

              $("#contractSentForm")
                .unbind("submit")
                .bind("submit", handleContractSentFormSubmit);
            }
          },
        });
      } else {
        showToast(reminder_response_message, reminder_response_title);
      }
    }
  );
}

function handleContractSentFormSubmit(submitEvent) {
  submitEvent.preventDefault();

  let nalog_id = $(this).attr("nalog_id");
  let candidate_key = $(this).attr("candidate_id");
  let partner_id = $(this).attr("partner_id");
  let location = $(this).attr("location");
  let reminder_type = 5;

  let contractDateSent = $("#contractDateSent").val();
  let contractTrackingCode = $("#contractTrackingCode").val();
  let contractTrackingLink = $("#contractTrackingLink").val();

  // Check if info already exists
  $.ajax({
    url: "ajax.php?action=check_contract_sent",
    type: "POST",
    data: {
      canKey: candidate_key,
    },
    dataType: "json",
    success: function (data) {
      let infoAlreadyExists = data.contractInfoExists;

      // If info doesn't already exist, continue
      if (!infoAlreadyExists) {
        getAccessControl(
          nalog_id,
          candidate_key,
          partner_id,
          reminder_type,
          function (response_access_control) {
            let reminder_status = response_access_control["status"];
            let reminder_id = response_access_control["id"];
            let reminder_assigned_user = response_access_control["assigned"];
            let reminder_is_logged_user = response_access_control["isLogged"];
            let reminder_response_message = response_access_control["message"];
            let reminder_response_title = response_access_control["title"];

            if (
              reminder_status == 101 ||
              reminder_status == 1 ||
              (reminder_status == 2 && reminder_is_logged_user == 1)
            ) {
              // Useful for debugging
              //console.log(nalog_id, candidate_id, partner_id, location, contractDateSent, contractTrackingCode, contractTrackingLink);

              // If task exists and can be assigned to current user
              if (reminder_status == 1) {
                // Assign task to current user
                updateReminderStatus(
                  reminder_id,
                  2,
                  function (responseUpdateReminder) {
                    let updateStatus = responseUpdateReminder["status"];
                    let updateMessage = responseUpdateReminder["message"];
                    if (updateStatus == 2) {
                      showToast(updateMessage, reminder_response_title);
                    }
                  }
                );
              }

              // Send form
              let formData = new FormData();
              formData.append("can_key", candidate_key);
              formData.append("sent_date", contractDateSent);
              formData.append("tracking_code", contractTrackingCode);
              formData.append("link_tracking_code", contractTrackingLink);

              getLoaderBig();

              $.ajax({
                url: "ajax.php?action=insert_contract_sent",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                cache: false,
                success: function (response) {
                  if (response != "1") {
                    showToast(
                      getTranslation(
                        "Informacije o poslanom ugovoru nije moguće spremiti"
                      ),
                      getTranslation("Greška")
                    );
                  } else {
                    // If the task is assigned to current user
                    if (reminder_status == 2) {
                      // Mark task as completed
                      updateReminderStatus(
                        reminder_id,
                        3,
                        function (responseUpdateReminder) {
                          let updateStatus = responseUpdateReminder["status"];
                          let updateMessage = responseUpdateReminder["message"];
                          if (updateStatus == 3) {
                            showToast(updateMessage, reminder_response_title);
                          }
                        }
                      );
                    }
                  }

                  $("#modalManageDocuments").modal("hide");
                  removeLoader();
                },
              });
            } else {
              showToast(reminder_response_message, reminder_response_title);
            }
          }
        );
      } else {
        showToast(
          getTranslation("Informacije o poslanom ugovoru već postoje"),
          getTranslation("Greška")
        );
      }
    },
    error: function (xhr, ajaxOptions, thrownError) {
      alert(xhr.status);
      alert(thrownError);
    },
  });

  return false;
}

function removeDocumentHighlights() {
  $(".cell_icon_document").each(function () {
    $(this).removeClass("document_uploaded_color_medium");
    $(this).removeClass("document_not_uploaded_color_medium");
  });
  $(".document_not_uploaded_selected").each(function () {
    $(this).removeClass("document_not_uploaded_selected");
    $(this).addClass("document_not_uploaded_color_light");
  });
  $(".document_uploaded_selected").each(function () {
    $(this).removeClass("document_uploaded_selected");
    $(this).addClass("document_uploaded_color_light");
  });
}

function handleGetDocument() {
  var candidate_id = $("#manage_documents_candidate_key").val();
  var document_type = $(this).attr("document_type");
  var partner_id = $(this).attr("partner_id");
  var nalog_id = $(this).attr("nalog_id");
  var is_uploaded = $(this).attr("is_uploaded");
  var location = $(this).attr("location");

  var row_child_element = $(this).children(".cell_text_document");
  var row_child_icon = $(this).children(".cell_icon_document");
  $(".selected_row").removeClass("selected_row");

  if (
    row_child_element.hasClass("document_not_uploaded_selected") ||
    row_child_element.hasClass("document_uploaded_selected")
  ) {
    if (row_child_element.hasClass("document_not_uploaded_selected")) {
      row_child_element.addClass("document_not_uploaded_color_light");
      row_child_element.removeClass("document_not_uploaded_selected");
      row_child_icon.removeClass("document_not_uploaded_color_medium");
    } else if (row_child_element.hasClass("document_uploaded_selected")) {
      row_child_element.addClass("document_uploaded_color_light");
      row_child_element.removeClass("document_uploaded_selected");
      row_child_icon.removeClass("document_uploaded_color_medium");
    }
    $("#to_append_to_document_manager").hide("blind", 300);
  } else {
    $(this).addClass("selected_row");
    removeDocumentHighlights();
    if (row_child_element.hasClass("document_not_uploaded_color_light")) {
      row_child_element.removeClass("document_not_uploaded_color_light");
      row_child_element.addClass("document_not_uploaded_selected");
      row_child_icon.addClass("document_not_uploaded_color_medium");
    } else if (row_child_element.hasClass("document_uploaded_color_light")) {
      row_child_element.removeClass("document_uploaded_color_light");
      row_child_element.addClass("document_uploaded_selected");
      row_child_icon.addClass("document_uploaded_color_medium");
    }
    getLoaderBig();
    $("#to_append_to_document_manager").hide("blind", 300, function () {
      $.ajax({
        url: "ajax.php?action=get_document",
        type: "POST",
        dataType: "html",
        data: {
          nalog_id: nalog_id,
          candidate_id: candidate_id,
          partner_id: partner_id,
          is_uploaded: is_uploaded,
          document_type: document_type,
          location: location,
        },
        success: function (response) {
          $("#to_append_to_document_manager")
            .empty()
            .append(response)
            .show("blind", 300, function () {
              $(".document_view")
                .unbind("click")
                .bind("click", function () {
                  window.open($(this).attr("file_name"), "Vertrag");
                });
              $(".document_archive")
                .unbind("click")
                .bind("click", function () {
                  $("#document_management_row").hide("blind", 200, function () {
                    $("#document_delete_row").show("blind", 300);
                  });
                });
              $(".disconfirm_document_delete")
                .unbind("click")
                .bind("click", function () {
                  $("#document_delete_row").hide("blind", 200, function () {
                    $("#document_management_row").show("blind", 300);
                  });
                });
              $("#upload_document").on("change", function () {
                var f = this.files[0];
                // if (f.size > 20388608 || f.fileSize > 20388608){
                // 	$('.document_search').effect('highlight');
                // 	$('.document_search').effect('highlight');
                // 	$('#alert_exceeds_size').show('blind', 300);
                // 	this.value = null;
                // 	$('#btn_upload_document').addClass('document_upload_not_allowed');
                // 	$('#btn_upload_document').removeClass('document_upload_allowed');
                // 	$('#btn_upload_document').effect('highlight');
                // 	$('#btn_upload_document').effect('highlight');
                // 	$('#btn_upload_document').unbind('click');
                // }else{
                $("#alert_exceeds_size").hide("blind", 300);
                var ext = $("#upload_document")
                  .val()
                  .split(".")
                  .pop()
                  .toLowerCase();

                if (
                  $.inArray(ext, [
                    "pdf",
                    "jpg",
                    "jpeg",
                    "doc",
                    "docx",
                    "xls",
                    "xlsx",
                    "txt",
                    "ppt",
                    "pptx",
                    "png",
                  ]) == -1
                ) {
                  $("#alert_invalid_extension").show("blind", 300);
                  this.value = null;
                  $("#btn_upload_document").addClass(
                    "document_upload_not_allowed"
                  );
                  $("#btn_upload_document").removeClass(
                    "document_upload_allowed"
                  );
                  $("#btn_upload_document").effect("highlight");
                  $("#btn_upload_document").effect("highlight");
                  $("#btn_upload_document").unbind("click");
                } else {
                  $("#alert_invalid_extension").hide("blind", 300);
                  $("#btn_upload_document").removeClass(
                    "document_upload_not_allowed"
                  );
                  $("#btn_upload_document").addClass("document_upload_allowed");
                  $("#btn_upload_document").effect("highlight");
                  $("#btn_upload_document").effect("highlight");
                  $("#btn_upload_document").unbind("click").bind(
                    "click",
                    {
                      row_child_element: row_child_element,
                      row_child_icon: row_child_icon,
                    },
                    handleDocumentUpload
                  );
                }
                // }
              });
              removeLoader();
            });
        },
        error: function (xhr, ajaxOptions, thrownError) {
          alert(xhr.status);
          alert(thrownError);
        },
      });
    });
  }
}

function handleDocumentUpload(row_child_element, row_child_icon) {
  var nalog_id = $(this).attr("nalog_id");
  var candidate_id = $(this).attr("candidate_id");
  var partner_id = $(this).attr("partner_id");
  var document_type = $(this).attr("document_type");
  var location = $(this).attr("location");
  var uploaded_contract = $("#upload_document").prop("files")[0];
  var allow_upload_flag = false;
  var reminder_type = 0;
  if (document_type == "cus") {
    reminder_type = 5;
  }

  if (reminder_type != 0) {
    getAccessControl(
      nalog_id,
      candidate_id,
      partner_id,
      reminder_type,
      function (response_access_control) {
        var reminder_status = response_access_control["status"];
        var reminder_id = response_access_control["id"];
        var reminder_assigned_user = response_access_control["assigned"];
        var reminder_is_logged_user = response_access_control["isLogged"];
        var reminder_response_message = response_access_control["message"];
        var reminder_response_title = response_access_control["title"];

        if (
          reminder_status == 101 ||
          reminder_status == 1 ||
          (reminder_status == 2 && reminder_is_logged_user == 1)
        ) {
          var form_data = new FormData();
          form_data.append("nalog_id", nalog_id);
          form_data.append("candidate_id", candidate_id);
          form_data.append("partner_id", partner_id);
          form_data.append("document_type", document_type);
          form_data.append("uploaded_contract", uploaded_contract);
          getLoaderBig();

          $.ajax({
            url: "ajax.php?action=upload_document",
            type: "POST",
            dataType: "html",
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            success: function (response) {
              var row_child_text = $(".selected_row").children(
                ".cell_text_document"
              );
              var row_child_icon = $(".selected_row").children(
                ".cell_icon_document"
              );

              row_child_text.removeClass("document_not_uploaded_selected");
              row_child_icon.removeClass("document_not_uploaded_color_medium");
              row_child_icon
                .children()
                .removeClass("document_not_uploaded_color_dark");

              row_child_text.addClass("document_uploaded_color_light");
              row_child_icon.addClass("document_uploaded_color_medium");
              row_child_icon
                .children()
                .addClass("document_uploaded_color_dark");
              row_child_icon.children(".fa-times").addClass("fa-check");
              row_child_icon.children(".fa-times").removeClass("fa-times");
              row_child_text.parent().attr("is_uploaded", 1);

              $(".selected_row").children().effect("highlight");

              $("#to_append_to_document_manager").hide(
                "blind",
                250,
                function () {
                  row_child_icon.removeClass("document_uploaded_color_medium");
                  removeLoader();
                }
              );

              //PREKOPIRAN KOOD ZA UČITAVANJE PROGRESS BARA (VRLO RUŽNO I KNOW BUT DEADLINE)
              // btw danas prvi dan u novom radnom prostoru, jučer se ne računa jer te je ušteklo u leđi,
              // sad se osjecas bolje dosta i ne boli kad pokušaš dohvatiti tastaturu
              // START
              type = 2;
              bar_id = 0;

              $.ajax({
                url: "ajax.php?action=get_progress_bar",
                type: "POST",
                dataType: "json",
                data: {
                  nalog_ids: nalog_id,
                  type: type,
                  bar_id: bar_id,
                  partner_ids: partner_id,
                },
                success: function (response_progress_bar) {
                  $("#db_progress_bar")
                    .empty()
                    .append(response_progress_bar["code_to_append"]);

                  for (
                    var i = 0;
                    i < parseInt(response_progress_bar["bar_type"].length);
                    i++
                  ) {
                    $(response_progress_bar["bar_type"][i]).circleProgress({
                      animationDuration: 0,
                      max: parseInt(response_progress_bar["bar_max"][i]),
                      value: parseInt(response_progress_bar["bar_value"][i]),
                      textFormat: function (value, max) {
                        return response_progress_bar["bar_text"][i];
                      },
                    });
                    $(response_progress_bar["bar_type"][i])
                      .children()
                      .children("text")
                      .attr("y", 35);
                    $(response_progress_bar["bar_type"][i])
                      .children()
                      .children(".circle-progress-value")
                      .addClass(response_progress_bar["bar_style"][i]);
                  }
                  $(".progress_bar_click")
                    .unbind("click")
                    .bind("click", handleProgressBarClick);
                  if (location == "p") {
                    if (document_type == "cus") {
                      $(".s7").removeClass("cekaUgovor text-white fs-5");
                      $(".s7").addClass("text-dark");
                      $(".s8").addClass("poslanUgovor text-white fs-5");
                      $(".s8").effect("highlight");
                      $(".s8").effect("highlight");
                      $(".click_edit_candidate").addClass(
                        "click_edit_candidate_disabled"
                      );
                      $(".click_edit_candidate").removeClass(
                        "click_edit_candidate"
                      );
                    } else if (document_type == "cs") {
                      $(".s8").removeClass("poslanUgovor text-white fs-5");
                      $(".s8").addClass("text-dark");
                      $(".s9").addClass("potpisanUgovor text-white fs-5");
                      $(".s9").effect("highlight");
                      $(".s9").effect("highlight");
                    }
                  }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                  alert(xhr.status);
                  alert(thrownError);
                },
              });
              //END
              //PREKOPIRAN KOOD ZA UČITAVANJE PROGRESS BARA (VRLO RUŽNO I KNOW BUT DEADLINE)
            },
            error: function (xhr, ajaxOptions, thrownError) {
              alert(xhr.status);
              alert(thrownError);
            },
          });
          if (reminder_status == 1 || reminder_status == 2) {
            updateReminderStatus(
              reminder_id,
              3,
              function (response_update_reminder) {
                var update_status = response_update_reminder["status"];
                var update_message = response_update_reminder["message"];
                if (update_status == 3) {
                  showToast(update_message, reminder_response_title);
                }
              }
            );
          }
        } else if (reminder_status == 104) {
          showToast(reminder_response_message, reminder_response_title);
        } else {
          showToast(reminder_response_message, reminder_response_title);
        }
      }
    );
  } else {
    var form_data = new FormData();
    form_data.append("nalog_id", nalog_id);
    form_data.append("candidate_id", candidate_id);
    form_data.append("partner_id", partner_id);
    form_data.append("document_type", document_type);
    form_data.append("uploaded_contract", uploaded_contract);
    getLoaderBig();

    $.ajax({
      url: "ajax.php?action=upload_document",
      type: "POST",
      dataType: "html",
      cache: false,
      contentType: false,
      processData: false,
      data: form_data,
      success: function (response) {
        var row_child_text = $(".selected_row").children(".cell_text_document");
        var row_child_icon = $(".selected_row").children(".cell_icon_document");

        row_child_text.removeClass("document_not_uploaded_selected");
        row_child_icon.removeClass("document_not_uploaded_color_medium");
        row_child_icon
          .children()
          .removeClass("document_not_uploaded_color_dark");

        row_child_text.addClass("document_uploaded_color_light");
        row_child_icon.addClass("document_uploaded_color_medium");
        row_child_icon.children().addClass("document_uploaded_color_dark");
        row_child_icon.children(".fa-times").addClass("fa-check");
        row_child_icon.children(".fa-times").removeClass("fa-times");
        row_child_text.parent().attr("is_uploaded", 1);

        $(".selected_row").children().effect("highlight");

        $("#to_append_to_document_manager").hide("blind", 250, function () {
          row_child_icon.removeClass("document_uploaded_color_medium");
          removeLoader();
        });

        //PREKOPIRAN KOOD ZA UČITAVANJE PROGRESS BARA (VRLO RUŽNO I KNOW BUT DEADLINE)
        // btw danas prvi dan u novom radnom prostoru, jučer se ne računa jer te je ušteklo u leđi,
        // sad se osjecas bolje dosta i ne boli kad pokušaš dohvatiti tastaturu
        // START
        type = 2;
        bar_id = 0;

        $.ajax({
          url: "ajax.php?action=get_progress_bar",
          type: "POST",
          dataType: "json",
          data: {
            nalog_ids: nalog_id,
            type: type,
            bar_id: bar_id,
            partner_ids: partner_id,
          },
          success: function (response_progress_bar) {
            $("#db_progress_bar")
              .empty()
              .append(response_progress_bar["code_to_append"]);

            for (
              var i = 0;
              i < parseInt(response_progress_bar["bar_type"].length);
              i++
            ) {
              $(response_progress_bar["bar_type"][i]).circleProgress({
                animationDuration: 0,
                max: parseInt(response_progress_bar["bar_max"][i]),
                value: parseInt(response_progress_bar["bar_value"][i]),
                textFormat: function (value, max) {
                  return response_progress_bar["bar_text"][i];
                },
              });
              $(response_progress_bar["bar_type"][i])
                .children()
                .children("text")
                .attr("y", 35);
              $(response_progress_bar["bar_type"][i])
                .children()
                .children(".circle-progress-value")
                .addClass(response_progress_bar["bar_style"][i]);
            }
            $(".progress_bar_click")
              .unbind("click")
              .bind("click", handleProgressBarClick);
            if (location == "p") {
              if (document_type == "cus") {
                $(".s7").removeClass("cekaUgovor text-white fs-5");
                $(".s7").addClass("text-dark");
                $(".s8").addClass("poslanUgovor text-white fs-5");
                $(".s8").effect("highlight");
                $(".s8").effect("highlight");
                $(".click_edit_candidate").addClass(
                  "click_edit_candidate_disabled"
                );
                $(".click_edit_candidate").removeClass("click_edit_candidate");
              } else if (document_type == "cs") {
                $(".s8").removeClass("poslanUgovor text-white fs-5");
                $(".s8").addClass("text-dark");
                $(".s9").addClass("potpisanUgovor text-white fs-5");
                $(".s9").effect("highlight");
                $(".s9").effect("highlight");
              }
            }
          },
          error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
          },
        });
        //END
        //PREKOPIRAN KOOD ZA UČITAVANJE PROGRESS BARA (VRLO RUŽNO I KNOW BUT DEADLINE)
      },
      error: function (xhr, ajaxOptions, thrownError) {
        alert(xhr.status);
        alert(thrownError);
      },
    });
  }
}

function checkReminderAvailability() {
  var rrnalogid = $(this).data("rrnalogid");
  var rrpartnerid = $(this).data("rrpartnerid");
  var rrcandidatecheck = $(this).data("rrcandidatecheck");
  var rrremindertype = $(this).data("rrremindertype");
  var rrreminderid = $(this).data("rrreminderid");
  var remEvent = false;

  if (!rrpartnerid) rrpartnerid = null;

  getAccessControl(
    rrnalogid,
    rrcandidatecheck,
    rrpartnerid,
    rrremindertype,
    function (response_access_control) {
      var reminder_status = response_access_control["status"];
      var reminder_id = response_access_control["id"];
      var reminder_assigned_user = response_access_control["assigned"];
      var reminder_is_logged_user = response_access_control["isLogged"];
      var reminder_response_message = response_access_control["message"];
      var reminder_response_title = response_access_control["title"];
      if (
        reminder_status == 101 ||
        reminder_status == 1 ||
        (reminder_status == 2 && reminder_is_logged_user == 1)
      ) {
        if (reminder_status == 1) {
          updateReminderStatus(
            reminder_id,
            2,
            function (response_update_reminder) {
              var update_status = response_update_reminder["status"];
              var update_message = response_update_reminder["message"];
              if (update_status == 2) {
                showToast(update_message, reminder_response_title);
                remEvent = true;
                setTimeout(function () {
                  window.open(
                    "/profile?kandidat_id=" + rrcandidatecheck,
                    "_blank"
                  );
                }, 3000);
              }
            }
          );
        }
        if (reminder_status == 2 && reminder_is_logged_user == 1) {
          setTimeout(function () {
            window.open(
              "/profile?kandidat_id=" + rrcandidatecheck,
              "_blank"
            );
          }, 1200);
        }
      } else if (reminder_status == 104) {
        showToast(reminder_response_message, reminder_response_title);
      } else {
        showToast(reminder_response_message, reminder_response_title);
      }
    }
  );
  $(this).effect("highlight", 500, function () {
    $(this).effect("bounce", 500, function () {
      if (remEvent != false) {
        const d = new Date();
        const date = d.getDate();
        const month = d.getMonth() + 1;
        const year = d.getFullYear();
        $(this).css("background-color", "#c01d1d12");
        $(".remStatus" + rrreminderid).text("Angenommen");
        $(".remDate" + rrreminderid).text("" + date + "." + month + "." + year);
        $(".remIcon" + rrreminderid).html(
          '<i class="fa fa-envelope-open" aria-hidden="true"></i>'
        );
      }
    });
  });
}

function openSearchCandidateModal() {
  $("#modalSearchCandidate").modal("show");
}

function getCircleBarMenu(callback) {
  return $.ajax({
    url: "ajax.php?action=get_circle_bar_menu",
    type: "POST",
    dataType: "html",
    data: {},
    success: callback,
    error: function (xhr, ajaxOptions, thrownError) {
      alert(xhr.status);
      alert(thrownError);
    },
  });
}

function handleRemoveAbsentCandidateModalOpen(element) {
  var nalog_id = $(element).attr("nalog_id");
  var candidate_id = $(element).attr("candidate_id");
  var pap_id = $(element).attr("pap_id");

  $.ajax({
    url: "ajax.php?action=load_modal_candidate_absent",
    type: "POST",
    dataType: "html",
    data: {
      nalog_id: nalog_id,
      pap_id: pap_id,
      candidate_id: candidate_id,
    },
    success: function (response) {
      $("#to_append_to_modal_candidate_absent").empty().append(response);
      $("#modalAbsentCandidate").modal("show");
      $("#remove_absent_candidate")
        .unbind("click")
        .bind("click", removeAbsentCandidate);
    },
  });
}

function removeAbsentCandidate() {
  var nalog_id = $("#remove_absent_candidate").attr("nalog_id");
  var candidate_key = $("#remove_absent_candidate").attr("candidate_key");
  var pap_id = $("#remove_absent_candidate").attr("pap_id");

  $.ajax({
    url: "ajax.php?action=remove_absent_candidate",
    type: "POST",
    dataType: "html",
    data: {
      candidate_key: candidate_key,
      nalog_id: nalog_id,
      pap_id: pap_id,
    },
    success: function () {
      $("#modalAbsentCandidate").modal("hide");
      $("#btn_refresh_table").trigger("click");
    },
    error: function (xhr, ajaxOptions, thrownError) {
      alert(xhr.status);
      alert(thrownError);
    },
  });
}
function handleCircleBarMenuClick() {
  if ($(this).hasClass("circle_bar_menu_selected")) {
    return;
  } else {
    getLoaderBig();
    $("#menu_bars_contract").removeClass("circle_bar_menu_selected");
    $("#menu_bars_diploma_certificate").removeClass("circle_bar_menu_selected");
    $("#menu_bars_visa").removeClass("circle_bar_menu_selected");
    $(this).addClass("circle_bar_menu_selected");

    var nalog_ids = [];
    $(".table_nalog_selected").each(function () {
      nalog_ids.push($(this).parent().attr("nalog_id"));
    });
    var partner_ids = [];
    $(".table_partner_icon_selected").each(function () {
      partner_ids.push($(this).parent().attr("partner_id"));
    });
    var type = $(this).attr("type");
    $.ajax({
      url: "ajax.php?action=get_progress_bar",
      type: "POST",
      dataType: "json",
      data: {
        nalog_ids: nalog_ids,
        type: type,
        partner_ids: partner_ids,
      },
      success: function (response_progress_bar) {
        $("#db_progress_bar").fadeOut(200, function () {
          $("#db_progress_bar")
            .empty()
            .append(response_progress_bar["code_to_append"])
            .fadeIn(300);
          for (
            var i = 0;
            i < parseInt(response_progress_bar["bar_type"].length);
            i++
          ) {
            $(response_progress_bar["bar_type"][i]).circleProgress({
              animationDuration: 0,
              max: parseInt(response_progress_bar["bar_max"][i]),
              value: parseInt(response_progress_bar["bar_value"][i]),
              textFormat: function (value, max) {
                return response_progress_bar["bar_text"][i];
              },
            });
            $(response_progress_bar["bar_type"][i])
              .children()
              .children("text")
              .attr("y", 35);
            $(response_progress_bar["bar_type"][i])
              .children()
              .children(".circle-progress-value")
              .addClass(response_progress_bar["bar_style"][i]);
            var my_company_flag = false;

            $(".table_partner_selected").each(function () {
              if ($(this).hasClass("table_partner_my_company")) {
                my_company_flag = true;
              }
            });
            if (partner_ids.length == 1 && my_company_flag) {
              $(".progress_bar_click")
                .unbind("click")
                .bind("click", handleProgressBarClick);
            }
          }
          removeLoader();
        });
      },
      error: function (xhr, ajaxOptions, thrownError) {
        alert(xhr.status);
        alert(thrownError);
      },
    });
  }
}

function handleCastingViewMouseover() {
  $(this).stop(true, false);
  if ($(this).hasClass("view_unselected"))
    $(this).animate(
      {
        fontSize: "27px",
      },
      400
    );
}
function handleCastingViewMouseleave() {
  $(this).stop(true, false);
  if ($(this).hasClass("view_unselected"))
    $(this).animate(
      {
        fontSize: "20px",
      },
      400
    );
}
function handleCastingViewClick() {
  if (!$(this).hasClass("view_selected") && !$(this).hasClass("unclickable")) {
    $("#departure_view, #forecast_view").animate(
      {
        fontSize: "20px",
      },
      400
    );
    $(this).stop(true, true);

    $("#casting_view").removeClass("view_unselected");
    $("#casting_view").addClass("view_selected");
    $("#forecast_view").removeClass("view_selected");
    $("#forecast_view").addClass("view_unselected");
    $("#departure_view").removeClass("view_selected");
    $("#departure_view").addClass("view_unselected");
    getLoaderBig();
    $("#db_list_candidates").fadeOut(300);
    $("#db_view_choice").hide("blind", 300, function () {
      $(this).empty();
    });
    $("#db_forecast_calendar").hide("blind", 300, function () {
      $("#db_forecast_list").hide("blind", 300, function () {
        $("#db_progress_bar").hide("blind", 300, function () {
          var type = 1;
          var nalog_ids = [];
          var partner_ids = 0;
          var flag_enable_click = false;

          $(".table_nalog_selected").each(function () {
            nalog_ids.push($(this).parent().attr("nalog_id"));
          });
          if (nalog_ids.length == 0) nalog_ids = 0;
          if (nalog_ids.length == 1) flag_enable_click = true;

          $.ajax({
            url: "ajax.php?action=get_progress_bar",
            type: "POST",
            dataType: "json",
            data: {
              nalog_ids: nalog_ids,
              type: type,
              partner_ids: partner_ids,
            },
            success: function (response_progress_bar) {
              $("#db_progress_bar")
                .empty()
                .append(response_progress_bar["code_to_append"]);
              $("#applied_candidates")
                .empty()
                .append(response_progress_bar["applied_candidates"]);
              for (
                var i = 0;
                i < parseInt(response_progress_bar["bar_type"].length);
                i++
              ) {
                $(response_progress_bar["bar_type"][i]).circleProgress({
                  animationDuration: 0,
                  max: parseInt(response_progress_bar["bar_max"][i]),
                  value: parseInt(response_progress_bar["bar_value"][i]),
                  textFormat: function (value, max) {
                    return (
                      response_progress_bar["bar_text"][i] +
                      " " +
                      response_progress_bar["bar_value"][i]
                    );
                  },
                });
                $(response_progress_bar["bar_type"][i])
                  .children()
                  .children(".circle-progress-value")
                  .addClass(response_progress_bar["bar_style"][i]);
              }
              $(".progress_bar_click").unbind("click");
              if (flag_enable_click)
                $(".progress_bar_click").bind("click", handleProgressBarClick);

              $("#db_progress_bar").show("blind", 600, function () {
                if ($("#db_list_nalozi").css("display") == "none") {
                  $("#db_list_nalozi").show("slide", 300, function () {
                    $("#db_list_partners").show("slide", 300);
                  });
                }
                removeLoader();
              });
            },
            error: function (xhr, ajaxOptions, thrownError) {
              alert(xhr.status);
              alert(thrownError);
            },
          });
          checkNaloziPartneriBox(2);
        });
      });
    });
  } else if ($(this).hasClass("unclickable")) {
    $(this).effect("shake");
  }
}
function handleDepartureViewMouseover() {
  $(this).stop(true, false);
  if ($(this).hasClass("view_unselected"))
    $(this).animate(
      {
        fontSize: "27px",
      },
      400
    );
}
function handleDepartureViewMouseleave() {
  $(this).stop(true, false);
  if ($(this).hasClass("view_unselected"))
    $(this).animate(
      {
        fontSize: "20px",
      },
      400
    );
}
function handleDepartureViewClick() {
  if (!$(this).hasClass("view_selected")) {
    $("#casting_view, #forecast_view").animate(
      {
        fontSize: "20px",
      },
      400
    );
    $(this).stop(true, true);
    $("#departure_view").removeClass("view_unselected");
    $("#departure_view").addClass("view_selected");
    $("#casting_view").removeClass("view_selected");
    $("#casting_view").addClass("view_unselected");
    $("#forecast_view").removeClass("view_selected");
    $("#forecast_view").addClass("view_unselected");
    var flag_enable_click = false;
    var my_company_flag = false;
    getLoaderBig();
    $("#db_list_candidates").fadeOut(300);
    $("#db_forecast_calendar").hide("blind", 300, function () {
      $("#db_forecast_list").hide("blind", 300, function () {
        $("#db_progress_bar").hide("blind", 300, function () {
          var type = 2;
          var partner_ids = [];
          var nalog_ids = [];
          $(".table_partner_icon_selected").each(function () {
            partner_ids.push($(this).parent().attr("partner_id"));
          });
          $(".table_nalog_selected").each(function () {
            nalog_ids.push($(this).parent().attr("nalog_id"));
          });

          if (nalog_ids.length == 0) nalog_ids = 0;
          if (partner_ids.length == 0) partner_ids = 0;

          $(".table_partner_selected").each(function () {
            if ($(this).hasClass("table_partner_my_company")) {
              my_company_flag = true;
            }
          });
          if (partner_ids.length == 1 && my_company_flag)
            flag_enable_click = true;

          $.ajax({
            url: "ajax.php?action=get_progress_bar",
            type: "POST",
            dataType: "json",
            data: {
              nalog_ids: nalog_ids,
              partner_ids: partner_ids,
              type: type,
            },
            success: function (response_progress_bar) {
              $("#db_progress_bar")
                .empty()
                .append(response_progress_bar["code_to_append"]);
              $("#applied_candidates")
                .empty()
                .append(response_progress_bar["applied_candidates"]);
              for (
                var i = 0;
                i < parseInt(response_progress_bar["bar_type"].length);
                i++
              ) {
                $(response_progress_bar["bar_type"][i]).circleProgress({
                  animationDuration: 0,
                  max: parseInt(response_progress_bar["bar_max"][i]),
                  value: parseInt(response_progress_bar["bar_value"][i]),
                  textFormat: function (value, max) {
                    return response_progress_bar["bar_text"][i];
                  },
                });
                $(response_progress_bar["bar_type"][i])
                  .children()
                  .children(".circle-progress-value")
                  .addClass(response_progress_bar["bar_style"][i]);
                $(response_progress_bar["bar_type"][i])
                  .children()
                  .children("text")
                  .attr("y", 35);
              }

              $(".progress_bar_click").unbind("click");
              if (flag_enable_click)
                $(".progress_bar_click").bind("click", handleProgressBarClick);

              getCircleBarMenu(function (response_circle_bar_menu) {
                $("#db_view_choice").fadeOut(200, function () {
                  $(this)
                    .empty()
                    .append(response_circle_bar_menu)
                    .show("size", 400);
                  $("#menu_bars_contract").addClass("circle_bar_menu_selected");
                  $("#menu_bars_contract")
                    .unbind()
                    .bind("click", handleCircleBarMenuClick);
                  $("#menu_bars_diploma_certificate")
                    .unbind()
                    .bind("click", handleCircleBarMenuClick);
                  $("#menu_bars_visa")
                    .unbind()
                    .bind("click", handleCircleBarMenuClick);
                  $("#db_progress_bar").show("blind", 600, function () {
                    if ($("#db_list_nalozi").css("display") == "none") {
                      $("#db_list_nalozi").show("slide", 300, function () {
                        $("#db_list_partners").show("slide", 300);
                      });
                    }
                    removeLoader();
                  });
                });
              });
            },
            error: function (xhr, ajaxOptions, thrownError) {
              alert(xhr.status);
              alert(thrownError);
            },
          });
        });
      });
    });
  }
}

function handleForecastViewMouseover() {
  $(this).stop(true, false);
  if ($(this).hasClass("view_unselected"))
    $(this).animate(
      {
        fontSize: "27px",
      },
      400
    );
}
function handleForecastViewMouseleave() {
  $(this).stop(true, false);
  if ($(this).hasClass("view_unselected"))
    $(this).animate(
      {
        fontSize: "20px",
      },
      400
    );
}

function getProjectionPartnerDataByNalog(calendar, nalog_id, year) {
  var return_table =
    '<table style = "border-bottom: solid 1px;border-left: solid 1px;border-right: solid 1px;width:100%; padding:0px; margin:0px">';
  var partner_id = "";

  for (var i = 0; i < Object.keys(calendar).length; i++) {
    if (calendar[i]["nalog_id"] == nalog_id) {
      for (
        var j = 0;
        j < Object.keys(calendar[i]["partnersData"]).length;
        j++
      ) {
        var count_01 = 0;
        var count_02 = 0;
        var count_03 = 0;
        var count_04 = 0;
        var count_05 = 0;
        var count_06 = 0;
        var count_07 = 0;
        var count_08 = 0;
        var count_09 = 0;
        var count_10 = 0;
        var count_11 = 0;
        var count_12 = 0;
        var partner_id = calendar[i]["partnersData"][j]["partner_id"];
        if (calendar[i]["partnersData"][j]["partner_name"] == null) {
          return_table +=
            '<tr><td class = "projection_partner_table_partner_name">Bez partnera</td>';
        } else {
          return_table +=
            '<tr><td class = "projection_partner_table_partner_name">' +
            calendar[i]["partnersData"][j]["partner_name"] +
            "</td>";
        }
        for (
          var k = 0;
          k <
          Object.keys(calendar[i]["partnersData"][j]["partnerCountByMonthYear"])
            .length;
          k++
        ) {
          if (
            calendar[i]["partnersData"][j]["partnerCountByMonthYear"][k][
              "year"
            ] == year
          ) {
            if (
              calendar[i]["partnersData"][j]["partnerCountByMonthYear"][k][
                "month"
              ] == 1
            ) {
              count_01 =
                '<div class = "month_year_count_partner">' +
                calendar[i]["partnersData"][j]["partnerCountByMonthYear"][k][
                  "count"
                ] +
                "</div>";
            } else if (
              calendar[i]["partnersData"][j]["partnerCountByMonthYear"][k][
                "month"
              ] == 2
            ) {
              count_02 =
                '<div class = "month_year_count_partner">' +
                calendar[i]["partnersData"][j]["partnerCountByMonthYear"][k][
                  "count"
                ] +
                "</div>";
            } else if (
              calendar[i]["partnersData"][j]["partnerCountByMonthYear"][k][
                "month"
              ] == 3
            ) {
              count_03 =
                '<div class = "month_year_count_partner">' +
                calendar[i]["partnersData"][j]["partnerCountByMonthYear"][k][
                  "count"
                ] +
                "</div>";
            } else if (
              calendar[i]["partnersData"][j]["partnerCountByMonthYear"][k][
                "month"
              ] == 4
            ) {
              count_04 =
                '<div class = "month_year_count_partner">' +
                calendar[i]["partnersData"][j]["partnerCountByMonthYear"][k][
                  "count"
                ] +
                "</div>";
            } else if (
              calendar[i]["partnersData"][j]["partnerCountByMonthYear"][k][
                "month"
              ] == 5
            ) {
              count_05 =
                '<div class = "month_year_count_partner">' +
                calendar[i]["partnersData"][j]["partnerCountByMonthYear"][k][
                  "count"
                ] +
                "</div>";
            } else if (
              calendar[i]["partnersData"][j]["partnerCountByMonthYear"][k][
                "month"
              ] == 6
            ) {
              count_06 =
                '<div class = "month_year_count_partner">' +
                calendar[i]["partnersData"][j]["partnerCountByMonthYear"][k][
                  "count"
                ] +
                "</div>";
            } else if (
              calendar[i]["partnersData"][j]["partnerCountByMonthYear"][k][
                "month"
              ] == 7
            ) {
              count_07 =
                '<div class = "month_year_count_partner">' +
                calendar[i]["partnersData"][j]["partnerCountByMonthYear"][k][
                  "count"
                ] +
                "</div>";
            } else if (
              calendar[i]["partnersData"][j]["partnerCountByMonthYear"][k][
                "month"
              ] == 8
            ) {
              count_08 =
                '<div class = "month_year_count_partner">' +
                calendar[i]["partnersData"][j]["partnerCountByMonthYear"][k][
                  "count"
                ] +
                "</div>";
            } else if (
              calendar[i]["partnersData"][j]["partnerCountByMonthYear"][k][
                "month"
              ] == 9
            ) {
              count_09 =
                '<div class = "month_year_count_partner">' +
                calendar[i]["partnersData"][j]["partnerCountByMonthYear"][k][
                  "count"
                ] +
                "</div>";
            } else if (
              calendar[i]["partnersData"][j]["partnerCountByMonthYear"][k][
                "month"
              ] == 10
            ) {
              count_10 =
                '<div class = "month_year_count_partner">' +
                calendar[i]["partnersData"][j]["partnerCountByMonthYear"][k][
                  "count"
                ] +
                "</div>";
            } else if (
              calendar[i]["partnersData"][j]["partnerCountByMonthYear"][k][
                "month"
              ] == 11
            ) {
              count_11 =
                '<div class = "month_year_count_partner">' +
                calendar[i]["partnersData"][j]["partnerCountByMonthYear"][k][
                  "count"
                ] +
                "</div>";
            } else if (
              calendar[i]["partnersData"][j]["partnerCountByMonthYear"][k][
                "month"
              ] == 12
            ) {
              count_12 =
                '<div class = "month_year_count_partner">' +
                calendar[i]["partnersData"][j]["partnerCountByMonthYear"][k][
                  "count"
                ] +
                "</div>";
            }
          }
        }
        return_table +=
          '<td class = "loads_list projection_partner_table_month_count" month = "1" year = "' +
          year +
          '" nalog_id = "' +
          nalog_id +
          '" partner_id = "' +
          partner_id +
          '">' +
          count_01 +
          "</td>";
        return_table +=
          '<td class = "loads_list projection_partner_table_month_count" month = "2" year = "' +
          year +
          '" nalog_id = "' +
          nalog_id +
          '" partner_id = "' +
          partner_id +
          '">' +
          count_02 +
          "</td>";
        return_table +=
          '<td class = "loads_list projection_partner_table_month_count" month = "3" year = "' +
          year +
          '" nalog_id = "' +
          nalog_id +
          '" partner_id = "' +
          partner_id +
          '">' +
          count_03 +
          "</td>";
        return_table +=
          '<td class = "loads_list projection_partner_table_month_count" month = "4" year = "' +
          year +
          '" nalog_id = "' +
          nalog_id +
          '" partner_id = "' +
          partner_id +
          '">' +
          count_04 +
          "</td>";
        return_table +=
          '<td class = "loads_list projection_partner_table_month_count" month = "5" year = "' +
          year +
          '" nalog_id = "' +
          nalog_id +
          '" partner_id = "' +
          partner_id +
          '">' +
          count_05 +
          "</td>";
        return_table +=
          '<td class = "loads_list projection_partner_table_month_count" month = "6" year = "' +
          year +
          '" nalog_id = "' +
          nalog_id +
          '" partner_id = "' +
          partner_id +
          '">' +
          count_06 +
          "</td>";
        return_table +=
          '<td class = "loads_list projection_partner_table_month_count" month = "7" year = "' +
          year +
          '" nalog_id = "' +
          nalog_id +
          '" partner_id = "' +
          partner_id +
          '">' +
          count_07 +
          "</td>";
        return_table +=
          '<td class = "loads_list projection_partner_table_month_count" month = "8" year = "' +
          year +
          '" nalog_id = "' +
          nalog_id +
          '" partner_id = "' +
          partner_id +
          '">' +
          count_08 +
          "</td>";
        return_table +=
          '<td class = "loads_list projection_partner_table_month_count" month = "9" year = "' +
          year +
          '" nalog_id = "' +
          nalog_id +
          '" partner_id = "' +
          partner_id +
          '">' +
          count_09 +
          "</td>";
        return_table +=
          '<td class = "loads_list projection_partner_table_month_count" month = "10" year = "' +
          year +
          '" nalog_id = "' +
          nalog_id +
          '" partner_id = "' +
          partner_id +
          '">' +
          count_10 +
          "</td>";
        return_table +=
          '<td class = "loads_list projection_partner_table_month_count" month = "11" year = "' +
          year +
          '" nalog_id = "' +
          nalog_id +
          '" partner_id = "' +
          partner_id +
          '">' +
          count_11 +
          "</td>";
        return_table +=
          '<td class = "loads_list projection_partner_table_month_count" month = "12" year = "' +
          year +
          '" nalog_id = "' +
          nalog_id +
          '" partner_id = "' +
          partner_id +
          '">' +
          count_12 +
          "</td>";
        return_table += "</tr>";
      }
    }
  }
  return_table += "</table>";
  return return_table;
}
function getCountCandidatesCell(calendar, nalog_id, month, year) {
  for (var i = 0; i < Object.keys(calendar).length; i++) {
    if (calendar[i]["nalog_id"] == nalog_id) {
      for (
        var j = 0;
        j < Object.keys(calendar[i]["nalogCountsByMonthYear"]).length;
        j++
      ) {
        if (
          calendar[i]["nalogCountsByMonthYear"][j]["month"] == month &&
          calendar[i]["nalogCountsByMonthYear"][j]["year"] == year
        ) {
          return calendar[i]["nalogCountsByMonthYear"][j]["count"];
        }
      }
    }
  }
  return 0;
}
function getForecastCalendarFooter(calendar, year) {
  var count_01 = 0;
  var count_02 = 0;
  var count_03 = 0;
  var count_04 = 0;
  var count_05 = 0;
  var count_06 = 0;
  var count_07 = 0;
  var count_08 = 0;
  var count_09 = 0;
  var count_10 = 0;
  var count_11 = 0;
  var count_12 = 0;

  for (var i = 0; i < Object.keys(calendar).length; i++) {
    for (
      var j = 0;
      j < Object.keys(calendar[i]["nalogCountsByMonthYear"]).length;
      j++
    ) {
      if (calendar[i]["nalogCountsByMonthYear"][j]["year"] == year) {
        switch (calendar[i]["nalogCountsByMonthYear"][j]["month"]) {
          case "1":
            count_01 += calendar[i]["nalogCountsByMonthYear"][j]["count"];
            break;
          case "2":
            count_02 += calendar[i]["nalogCountsByMonthYear"][j]["count"];
            break;
          case "3":
            count_03 += calendar[i]["nalogCountsByMonthYear"][j]["count"];
            break;
          case "4":
            count_04 += calendar[i]["nalogCountsByMonthYear"][j]["count"];
            break;
          case "5":
            count_05 += calendar[i]["nalogCountsByMonthYear"][j]["count"];
            break;
          case "6":
            count_06 += calendar[i]["nalogCountsByMonthYear"][j]["count"];
            break;
          case "7":
            count_07 += calendar[i]["nalogCountsByMonthYear"][j]["count"];
            break;
          case "8":
            count_08 += calendar[i]["nalogCountsByMonthYear"][j]["count"];
            break;
          case "9":
            count_09 += calendar[i]["nalogCountsByMonthYear"][j]["count"];
            break;
          case "10":
            count_10 += calendar[i]["nalogCountsByMonthYear"][j]["count"];
            break;
          case "11":
            count_11 += calendar[i]["nalogCountsByMonthYear"][j]["count"];
            break;
          case "12":
            count_12 += calendar[i]["nalogCountsByMonthYear"][j]["count"];
            break;
        }
      }
    }
  }

  var footer = "";
  footer +=
    '<td style = "font-weight:bold;text-align:right;">' +
    getTranslation("Ukupno:") +
    "</td>";
  footer +=
    '<td style = "font-weight:bold;" class = "calendar_forecast_footer" month = "1"  year = "' +
    year +
    '">' +
    count_01 +
    "</td>";
  footer +=
    '<td style = "font-weight:bold;" class = "calendar_forecast_footer" month = "2"  year = "' +
    year +
    '">' +
    count_02 +
    "</td>";
  footer +=
    '<td style = "font-weight:bold;" class = "calendar_forecast_footer" month = "3"  year = "' +
    year +
    '">' +
    count_03 +
    "</td>";
  footer +=
    '<td style = "font-weight:bold;" class = "calendar_forecast_footer" month = "4"  year = "' +
    year +
    '">' +
    count_04 +
    "</td>";
  footer +=
    '<td style = "font-weight:bold;" class = "calendar_forecast_footer" month = "5"  year = "' +
    year +
    '">' +
    count_05 +
    "</td>";
  footer +=
    '<td style = "font-weight:bold;" class = "calendar_forecast_footer" month = "6"  year = "' +
    year +
    '">' +
    count_06 +
    "</td>";
  footer +=
    '<td style = "font-weight:bold;" class = "calendar_forecast_footer" month = "7"  year = "' +
    year +
    '">' +
    count_07 +
    "</td>";
  footer +=
    '<td style = "font-weight:bold;" class = "calendar_forecast_footer" month = "8"  year = "' +
    year +
    '">' +
    count_08 +
    "</td>";
  footer +=
    '<td style = "font-weight:bold;" class = "calendar_forecast_footer" month = "9"  year = "' +
    year +
    '">' +
    count_09 +
    "</td>";
  footer +=
    '<td style = "font-weight:bold;" class = "calendar_forecast_footer" month = "10" year = "' +
    year +
    '">' +
    count_10 +
    "</td>";
  footer +=
    '<td style = "font-weight:bold;" class = "calendar_forecast_footer" month = "11" year = "' +
    year +
    '">' +
    count_11 +
    "</td>";
  footer +=
    '<td style = "font-weight:bold;" class = "calendar_forecast_footer" month = "12" year = "' +
    year +
    '">' +
    count_12 +
    "</td>";

  return footer;
}
function handleForecastCalendarFooterClick() {
  var count = $(this).text();
  var month = $(this).attr("month");
  var year = $(this).attr("year");
  var nalog_id = 0;

  if (count != 0 && $(this).prop("tagName") != "TH") {
    getLoaderBig();
    $("#db_forecast_list_table").empty();
    $("#db_forecast_calendar").hide("fade", 300, function () {
      $.ajax({
        url: "ajax.php?action=get_forecast_list",
        type: "POST",
        dataType: "json",
        data: {
          month: month,
          year: year,
          nalog_id: nalog_id,
        },
        success: function (list) {
          console.log(list);
          $("#db_forecast_list").show("fade", 400, function () {
            $("#db_forecast_list_table").empty().append(list[0]["table"]);
            var table_forecast = $("#table_forecast_list").DataTable({
              data: list,
              // "ordering": false,
              lengthChange: false,
              searching: false,
              info: false,
              columns: [
                {
                  data: "candidate_id",
                  render: function (data) {
                    return getCandidateProfileLinkByCandidateID(list, data);
                  },
                },
                {
                  className: "cell_forecast_list",
                  data: "language_level",
                  render: function (data) {
                    if (data == 0) {
                      return getTranslation("Bez znanja");
                    } else {
                      return data;
                    }
                  },
                },
                {
                  className: "cell_forecast_list",
                  data: "nd_status",
                  render: function (data) {
                    return getDIPLStatusOutput(data);
                  },
                },
                {
                  className: "cell_forecast_list",
                  data: "candidate_assessment",
                  render: function (data) {
                    return data;
                  },
                },
                {
                  className: "cell_forecast_list",
                  data: "nalog_name",
                  render: function (data) {
                    return data;
                  },
                },
              ],
            });
            $(".back_to_forecast_calendar")
              .unbind()
              .bind("click", handleBackToForecastCalender);
            removeLoader();
          });
        },
        error: function (xhr, ajaxOptions, thrownError) {
          alert(xhr.status);
          alert(thrownError);
        },
      });
    });
  }
}
function getNalogNameByNalogId(calendar, nalog_id) {
  for (var i = 0; i < Object.keys(calendar).length; i++) {
    if (calendar[i].nalog_id == nalog_id) {
      return calendar[i].nalog_name;
    }
  }
  return 0;
}
function handleForecastViewClick() {
  if (!$(this).hasClass("view_selected") && !$(this).hasClass("unclickable")) {
    $("#forecast_view").animate(
      {
        fontSize: "27px",
      },
      400
    );
    $(this).stop(true, true);
    $(this).removeClass("view_unselected");
    $(this).addClass("view_selected");
    $("#departure_view, #casting_view").animate(
      {
        fontSize: "20px",
      },
      400
    );
    $(this).stop(true, true);
    $("#casting_view").removeClass("view_selected");
    $("#casting_view").addClass("view_unselected");
    $("#departure_view").removeClass("view_selected");
    $("#departure_view").addClass("view_unselected");
    getLoaderBig();
    var selected_year = 0;
    var year = selected_year;
    $("#db_list_candidates").hide("slide", 200, function () {
      $("#db_list_partners").hide("slide", 200, function () {
        $("#db_list_nalozi").hide("fade", 200, function () {
          $("#db_progress_bar").hide("blind", 200, function () {
            $("#db_view_choice").hide("blind", 200, function () {
              getForecastCalendar(0);
            });
          });
        });
      });
    });
  } else if ($(this).hasClass("unclickable")) {
    $(this).effect("shake");
  }
}

function getForecastCalendar(year) {
  var selected_year = parseInt(year);
  getLoaderBig();
  $.ajax({
    url: "ajax.php?action=get_forecast_calendar",
    type: "POST",
    dataType: "json",
    data: {
      selected_year: selected_year,
    },
    success: function (calendar) {
      $("#db_forecast_calendar_table").empty().append(calendar[0]["table"]);
      $("#db_forecast_calendar").show("fade", 400, function () {
        if (selected_year == 0) {
          $("#db_forecast_calendar_input")
            .empty()
            .append(calendar[0]["select_year"]);
          selected_year = calendar[0]["min_year"];
        } else {
          selected_year = $("#selected_forecast_year").text();
        }
        var table_forecast = $("#table_forecast").DataTable({
          ordering: false,
          bPaginate: false,
          // dom: "Blfrtip",
          // buttons: [
          // 	'csvHtml5','excelHtml5'
          // ],
          searching: false,
          info: false,
          data: calendar,
          columns: [
            {
              className: "clickable_nalog projection_nalog_table_nalog_name",
              data: "nalog_id",
              render: function (data) {
                return getNalogNameByNalogId(calendar, data);
              },
              createdCell: function (td, data, rowData, row, col) {
                $(td).attr("nalog_id", data);
              },
            },
            {
              className: "loads_list projection_nalog_table_month_count",
              data: "nalog_id",
              render: function (data) {
                var count = getCountCandidatesCell(
                  calendar,
                  data,
                  1,
                  selected_year
                );
                if (count) {
                  return '<div class = "month_year_count">' + count + "</div>";
                } else {
                  return count;
                }
              },
              createdCell: function (td, data, rowData, row, col) {
                $(td).attr("nalog_id", data);
                $(td).attr("month", 1);
                $(td).attr("year", selected_year);
              },
            },
            {
              className: "loads_list projection_nalog_table_month_count",
              data: "nalog_id",
              render: function (data) {
                var count = getCountCandidatesCell(
                  calendar,
                  data,
                  2,
                  selected_year
                );
                if (count) {
                  return '<div class = "month_year_count">' + count + "</div>";
                } else {
                  return count;
                }
              },
              createdCell: function (td, data, rowData, row, col) {
                $(td).attr("nalog_id", data);
                $(td).attr("month", 2);
                $(td).attr("year", selected_year);
              },
            },
            {
              className: "loads_list projection_nalog_table_month_count",
              data: "nalog_id",
              render: function (data) {
                var count = getCountCandidatesCell(
                  calendar,
                  data,
                  3,
                  selected_year
                );
                if (count) {
                  return '<div class = "month_year_count">' + count + "</div>";
                } else {
                  return count;
                }
              },
              createdCell: function (td, data, rowData, row, col) {
                $(td).attr("nalog_id", data);
                $(td).attr("month", 3);
                $(td).attr("year", selected_year);
              },
            },
            {
              className: "loads_list projection_nalog_table_month_count",
              data: "nalog_id",
              render: function (data) {
                var count = getCountCandidatesCell(
                  calendar,
                  data,
                  4,
                  selected_year
                );
                if (count) {
                  return '<div class = "month_year_count">' + count + "</div>";
                } else {
                  return count;
                }
              },
              createdCell: function (td, data, rowData, row, col) {
                $(td).attr("nalog_id", data);
                $(td).attr("month", 4);
                $(td).attr("year", selected_year);
              },
            },
            {
              className: "loads_list projection_nalog_table_month_count",
              data: "nalog_id",
              render: function (data) {
                var count = getCountCandidatesCell(
                  calendar,
                  data,
                  5,
                  selected_year
                );
                if (count) {
                  return '<div class = "month_year_count">' + count + "</div>";
                } else {
                  return count;
                }
              },
              createdCell: function (td, data, rowData, row, col) {
                $(td).attr("nalog_id", data);
                $(td).attr("month", 5);
                $(td).attr("year", selected_year);
              },
            },
            {
              className: "loads_list projection_nalog_table_month_count",
              data: "nalog_id",
              render: function (data) {
                var count = getCountCandidatesCell(
                  calendar,
                  data,
                  6,
                  selected_year
                );
                if (count) {
                  return '<div class = "month_year_count">' + count + "</div>";
                } else {
                  return count;
                }
              },
              createdCell: function (td, data, rowData, row, col) {
                $(td).attr("nalog_id", data);
                $(td).attr("month", 6);
                $(td).attr("year", selected_year);
              },
            },
            {
              className: "loads_list projection_nalog_table_month_count",
              data: "nalog_id",
              render: function (data) {
                var count = getCountCandidatesCell(
                  calendar,
                  data,
                  7,
                  selected_year
                );
                if (count) {
                  return '<div class = "month_year_count">' + count + "</div>";
                } else {
                  return count;
                }
              },
              createdCell: function (td, data, rowData, row, col) {
                $(td).attr("nalog_id", data);
                $(td).attr("month", 7);
                $(td).attr("year", selected_year);
              },
            },
            {
              className: "loads_list projection_nalog_table_month_count",
              data: "nalog_id",
              render: function (data) {
                var count = getCountCandidatesCell(
                  calendar,
                  data,
                  8,
                  selected_year
                );
                if (count) {
                  return '<div class = "month_year_count">' + count + "</div>";
                } else {
                  return count;
                }
              },
              createdCell: function (td, data, rowData, row, col) {
                $(td).attr("nalog_id", data);
                $(td).attr("month", 8);
                $(td).attr("year", selected_year);
              },
            },
            {
              className: "loads_list projection_nalog_table_month_count",
              data: "nalog_id",
              render: function (data) {
                var count = getCountCandidatesCell(
                  calendar,
                  data,
                  9,
                  selected_year
                );
                if (count) {
                  return '<div class = "month_year_count">' + count + "</div>";
                } else {
                  return count;
                }
              },
              createdCell: function (td, data, rowData, row, col) {
                $(td).attr("nalog_id", data);
                $(td).attr("month", 9);
                $(td).attr("year", selected_year);
              },
            },
            {
              className: "loads_list projection_nalog_table_month_count",
              data: "nalog_id",
              render: function (data) {
                var count = getCountCandidatesCell(
                  calendar,
                  data,
                  10,
                  selected_year
                );
                if (count) {
                  return '<div class = "month_year_count">' + count + "</div>";
                } else {
                  return count;
                }
              },
              createdCell: function (td, data, rowData, row, col) {
                $(td).attr("nalog_id", data);
                $(td).attr("month", 10);
                $(td).attr("year", selected_year);
              },
            },
            {
              className: "loads_list projection_nalog_table_month_count",
              data: "nalog_id",
              render: function (data) {
                var count = getCountCandidatesCell(
                  calendar,
                  data,
                  11,
                  selected_year
                );
                if (count) {
                  return '<div class = "month_year_count">' + count + "</div>";
                } else {
                  return count;
                }
              },
              createdCell: function (td, data, rowData, row, col) {
                $(td).attr("nalog_id", data);
                $(td).attr("month", 11);
                $(td).attr("year", selected_year);
              },
            },
            {
              className: "loads_list projection_nalog_table_month_count",
              data: "nalog_id",
              render: function (data) {
                var count = getCountCandidatesCell(
                  calendar,
                  data,
                  12,
                  selected_year
                );
                if (count) {
                  return '<div class = "month_year_count">' + count + "</div>";
                } else {
                  return count;
                }
              },
              createdCell: function (td, data, rowData, row, col) {
                $(td).attr("nalog_id", data);
                $(td).attr("month", 12);
                $(td).attr("year", selected_year);
              },
            },
          ],
        });
        $("#forecast_calendar_append_footer_cells").append(
          getForecastCalendarFooter(calendar, selected_year)
        );
        $(".calendar_forecast_footer")
          .unbind("click")
          .bind("click", handleForecastCalendarFooterClick);
        $(".loads_list")
          .unbind("click", handleCalendarCellClick)
          .bind("click", handleCalendarCellClick);
        $("#table_forecast tbody tr .clickable_nalog").on("click", function () {
          var tr = $(this).closest("tr");
          var row = table_forecast.row(tr);
          var nalog_id = $(this).attr("nalog_id");
          if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass("shown");
          } else {
            row
              .child(
                getProjectionPartnerDataByNalog(
                  calendar,
                  nalog_id,
                  selected_year
                )
              )
              .show();
            // $('.dt-hasChild').css('padding', '0px');
            tr.addClass("shown");
            tr.next().first().children().css("padding", "0px");

            $(".loads_list")
              .unbind("click", handleCalendarCellClick)
              .bind("click", handleCalendarCellClick);
          }
        });
        $(".year_change")
          .unbind("click")
          .bind("click", function () {
            var selected_year = $("#selected_forecast_year").text();
            console.log($(this).attr("id"));
            if ($(this).attr("id") == "forecast_year_left") {
              if (selected_year > $(this).attr("min")) {
                selected_year--;
                $("#selected_forecast_year").empty().append(selected_year);
                getForecastCalendar(selected_year);
              } else {
                $("#selected_forecast_year").animate(
                  { color: "#a64452", fontSize: "20px" },
                  100
                );
                $("#selected_forecast_year").animate({ color: "#c7c6c6" }, 100);
                $("#selected_forecast_year").animate({ color: "#a64452" }, 300);
                $("#selected_forecast_year").animate(
                  { color: "#c7c6c6", fontSize: "24px" },
                  100
                );
              }
            } else {
              if (selected_year < $(this).attr("max")) {
                selected_year++;
                $("#selected_forecast_year").empty().append(selected_year);
                getForecastCalendar(selected_year);
              } else {
                $("#selected_forecast_year").animate(
                  { color: "#a64452", fontSize: "20px" },
                  100
                );
                $("#selected_forecast_year").animate({ color: "#c7c6c6" }, 100);
                $("#selected_forecast_year").animate({ color: "#a64452" }, 300);
                $("#selected_forecast_year").animate(
                  { color: "#c7c6c6", fontSize: "24px" },
                  100
                );
              }
            }
          });
        // $('#forecast_calendar_append_footer_cells').append('')
        removeLoader();
      });
      // $('#table_forecast thead tr th').each(function(){
      // 	$(this).unbind('click', handleCalendarCellClick);
      // });
    },
    error: function (xhr, ajaxOptions, thrownError) {
      alert(xhr.status);
      alert(thrownError);
    },
  });
}
function getCandidateProfileLinkByCandidateID(list, candidate_id) {
  for (var i = 0; i < Object.keys(list).length; i++) {
    if (list[i]["candidate_id"] == candidate_id) {
      return (
        '<a target="_blank" href="/profile?kandidat_id=' +
        list[i]["candidate_key"] +
        "&n=" +
        list[i]["nalog_id"] +
        '">' +
        list[i]["candidate_fullname"] +
        "</a>"
      );
    }
  }
}
function handleCalendarCellClick() {
  var count = $(this).text();
  var month = $(this).attr("month");
  var year = $(this).attr("year");
  var nalog_id = $(this).attr("nalog_id");
  var partner_id = $(this).attr("partner_id");
  // console.log(partner_id);

  if (count != 0 && $(this).prop("tagName") != "TH") {
    getLoaderBig();
    $("#db_forecast_list_table").empty();
    $("#db_forecast_calendar").hide("fade", 300, function () {
      $.ajax({
        url: "ajax.php?action=get_forecast_list",
        type: "POST",
        dataType: "json",
        data: {
          month: month,
          year: year,
          nalog_id: nalog_id,
          partner_id: partner_id,
        },
        success: function (list) {
          $("#db_forecast_list").show("fade", 400, function () {
            $("#db_forecast_list_table").empty().append(list[0]["table"]);
            var table_forecast = $("#table_forecast_list").DataTable({
              data: list,
              ordering: false,
              lengthChange: false,
              searching: false,
              info: false,
              columns: [
                {
                  data: "candidate_id",
                  render: function (data) {
                    return getCandidateProfileLinkByCandidateID(list, data);
                  },
                },
                {
                  className: "cell_forecast_list",
                  data: "language_level",
                  render: function (data) {
                    if (data == 0) {
                      return getTranslation("Bez znanja");
                    } else {
                      return data;
                    }
                  },
                },
                {
                  className: "cell_forecast_list",
                  data: "nd_status",
                  render: function (data) {
                    return getDIPLStatusOutput(data);
                  },
                },
                {
                  className: "cell_forecast_list",
                  data: "candidate_assessment",
                  render: function (data) {
                    return data;
                  },
                },
              ],
            });
            $(".back_to_forecast_calendar")
              .unbind()
              .bind("click", handleBackToForecastCalender);
            removeLoader();
          });
        },
        error: function (xhr, ajaxOptions, thrownError) {
          alert(xhr.status);
          alert(thrownError);
        },
      });
    });
  }
}

function handleBackToForecastCalender() {
  $("#db_forecast_list").hide("slide", 200, function () {
    $("#db_forecast_calendar").show("slide", 200);
  });
}
// function getTranslation(word){
// 	var result="";
//    	$.ajax({
// 	   	url: 'ajax.php?action=get_translation',
// 	  	type: 'POST',
// 		dataType: 'html',
// 		async: false,
// 		data:{
// 			'word'		: word
// 		},
// 		success:function(data) {
// 			result = data;
// 		},
// 		error: function (xhr, ajaxOptions, thrownError) {
// 			alert(xhr.status);
// 			alert(thrownError);
// 		}
// 	});
// 	return result;
// }
function getDIPLStatusOutput(status_id) {
  var cell_text_status = "";
  if (status_id == 1 || status_id == 7) {
    cell_text_status =
      '<div style = "text-align:center;margin:auto;" class = "nostrification_status nostrification_lead_in_process">' +
      getTranslation("U obradi Lead") +
      "</div>";
  } else if (status_id == 2) {
    cell_text_status =
      '<div style = "text-align:center;margin:auto;" class = "nostrification_status nostrification_collecting_documents">' +
      getTranslation("Prikupljanje dokumentacije") +
      "</div>";
  } else if (status_id == 3) {
    cell_text_status =
      '<div style = "text-align:center;margin:auto;" class = "nostrification_status nostrification_request_sent">' +
      getTranslation("Poslan zahtjev") +
      "</div>";
  } else if (status_id == 4) {
    cell_text_status =
      '<div style = "text-align:center;margin:auto;" class = "nostrification_status nostrification_in_process">' +
      getTranslation("U obradi") +
      "</div>";
  } else if (status_id == 5) {
    cell_text_status =
      '<div style = "text-align:center;margin:auto;" class = "nostrification_status nostrification_supplementing_documents">' +
      getTranslation("Dopuna dokumentacije") +
      "</div>";
  } else if (status_id == 6) {
    cell_text_status =
      '<div style = "text-align:center;margin:auto;" class = "nostrification_status nostrification_finished">' +
      getTranslation("Završeno") +
      "</div>";
  }
  return cell_text_status;
}

function handleTaskManagerOpen() {
  $("#main-container").hide("fade", 300, function () {
    $(this).removeClass("d-flex");
    $("#main-navbar").hide("blind", 300, function () {
      $("task-manager").show("fade");
      document.querySelector("task-manager").requestUpdate();
      document.querySelector("task-manager").shadowRoot.querySelector("tm-menu").manualUpdate();
    });
  });
}
