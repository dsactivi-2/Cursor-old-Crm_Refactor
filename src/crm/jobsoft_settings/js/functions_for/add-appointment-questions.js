function messageAQCD(type, message) {
    let message_AQCD = $('.message_AQCD','#addQuestionsCD', '#castingDatesSettings');
    message_AQCD.append(`
        <div class="col-sm-12">
            <div class="alert alert-` + type + ` text-center" role="alert">
                ` + message + `
            </div>
        </div>
    `).removeClass('hidden');
    
    setTimeout(function(){
        message_AQCD.html('').addClass('hidden');
    }, 7000);
};
function addAppointmentQuestionsCD(thisRow) {
    let pap_id = $(thisRow).data('pap_id');
    let pap_city = $(thisRow).data('pap_city');
    let pap_date = $(thisRow).data('pap_date');
    let edit_questions_flag = $(thisRow).data('edit_questions_flag');
    let order_name = $(thisRow).data('order_name');
    let company_name = $(thisRow).data('company_name'); 

    setModalTitleForAQCD(pap_city, pap_date, order_name, company_name, edit_questions_flag); 

    setInitialValueForAQCD(pap_id, edit_questions_flag);

    $('#addQuestionsCD', '#castingDatesSettings').modal('show');
};
function setModalTitleForAQCD(pap_city, pap_date, order_name, company_name, edit_questions_flag) {
    let modal_title_text = $('#modal-title-text', '#addQuestionsCD', '#castingDatesSettings'); 
    let modal_title_icon = $('.modal-title i', '#addQuestionsCD', '#castingDatesSettings'); 
    let new_modal_title_text = ''; 
    let new_modal_title_icon = ''; 
    if (pap_city != '' && pap_date != '' && order_name != '' && company_name != '') {
        if (edit_questions_flag == 0) {
            new_modal_title_icon = 'fa fa-plus me-5';
            new_modal_title_text = 'Dodaj pitanja za ' + pap_city + ' - ' + pap_date + ' (' + order_name + ' - ' + company_name + ')';
        } else if (edit_questions_flag == 1) {
            new_modal_title_icon = 'fa fa-pencil-square me-5';
            new_modal_title_text = 'Uredi pitanja za ' + pap_city + ' - ' + pap_date + ' (' + order_name + ' - ' + company_name + ')';
        } else {
            new_modal_title_icon = 'fa fa-eye me-5';
            new_modal_title_text = 'Pregled pitanja za ' + pap_city + ' - ' + pap_date + ' (' + order_name + ' - ' + company_name + ')';
        }
        modal_title_icon.removeClass().addClass(new_modal_title_icon);
    } else {
        modal_title_icon.removeClass(); 
    }
    modal_title_text.text(new_modal_title_text);
};
function setInitialValueForAQCD(pap_id, edit_questions_flag) {
    let order_id = $('#order_id', '#addQuestionsCD', '#castingDatesSettings').val();
    let pap_id_field = $('#pap_id', '#addQuestionsCD', '#castingDatesSettings'); 
    let edit_questions_field = $('#edit_questions_flag', '#addQuestionsCD', '#castingDatesSettings'); 
    let setting_mode_field = $('#setting_mode', '#addQuestionsCD', '#castingDatesSettings'); 
    let panel_ids_field = $('#panel_ids', '#addQuestionsCD', '#castingDatesSettings'); 
    let pqu_ids_field = $('#pqu_ids', '#addQuestionsCD', '#castingDatesSettings'); 
    let appointment_dates_field = $('#appointment_dates', '#addQuestionsCD', '#castingDatesSettings'); 
    let button_modal_footer = $('.modal-footer.material-modal__footer button[type="submit"]', '#addQuestionsCD', '#castingDatesSettings');
    let dismiss_modal_footer = $('.modal-footer.material-modal__footer button[data-dismiss="modal"]', '#addQuestionsCD', '#castingDatesSettings');
    let remark_form = $('.remark_form', '#addQuestionsCD', '#castingDatesSettings'); 
    if (pap_id != '') {
        pap_id_field.val(pap_id);
        edit_questions_field.val(edit_questions_flag);
        button_modal_footer.removeClass('hidden');
        remark_form.removeClass('hidden');
        dismiss_modal_footer.text('Odustani');
        if (edit_questions_flag == 0) {
            setting_mode_field.val(null).selectpicker('refresh').closest('.form-group').removeClass('hidden');
            panel_ids_field.val(null);
            pqu_ids_field.val(null);
            appointment_dates_field.empty().val(null).prop('required', false).selectpicker('refresh').closest('.form-group').addClass('hidden');
            showHideNewQuestionsAQCD('hide','hide','hide'); 
            destroySortableAQCD('hideen and destory');
        } else if (edit_questions_flag == 1) { 
            setting_mode_field.val('new').selectpicker('refresh').closest('.form-group').removeClass('hidden');
            panel_ids_field.val(null);
            pqu_ids_field.val(null);
            appointment_dates_field.empty().val(null).prop('required', false).selectpicker('refresh').closest('.form-group').addClass('hidden');
            destroySortableAQCD('hideen and destory');
            showHideNewQuestionsAQCD('show','hide','hide'); 
            getQuestionsForAppointmentAQCD(pap_id, order_id);
        } else {
            button_modal_footer.addClass('hidden');
            dismiss_modal_footer.text('Zatvori');
            remark_form.addClass('hidden');
            setting_mode_field.val(null).selectpicker('refresh').closest('.form-group').addClass('hidden');
            panel_ids_field.val(null);
            pqu_ids_field.val(null);
            appointment_dates_field.empty().val(null).prop('required', false).selectpicker('refresh').closest('.form-group').addClass('hidden');
            destroySortableAQCD('hideen and destory');
            showHideNewQuestionsAQCD('hide','hide','hide'); 
            getQuestionsForAppointmentAQCD(pap_id, order_id, 0);
        }
    } else {
        pap_id_field.val(null);
        edit_questions_field.val(null);
        button_modal_footer.removeClass('hidden');
        remark_form.removeClass('hidden');
        dismiss_modal_footer.text('Odustani');
        setting_mode_field.val(null).selectpicker('refresh').closest('.form-group').removeClass('hidden');
        panel_ids_field.val(null);
        pqu_ids_field.val(null);
        appointment_dates_field.empty().val(null).prop('required', false).selectpicker('refresh').closest('.form-group').addClass('hidden');
        showHideNewQuestionsAQCD('hide','hide','hide'); 
        destroySortableAQCD('hideen and destory');
    }
};
$('#addQuestionsCD', '#castingDatesSettings').on('hidden.bs.modal', function() {
    setModalTitleForAQCD('', '', '', '', 0); 
    setInitialValueForAQCD('', '');
});
function getAppointmentDatesCD(mode) {
    let appointment_dates = $('#appointment_dates', '#addQuestionsCD', '#castingDatesSettings');
    let questions_AQCD = $('.questions_AQCD', '#addQuestionsCD', '#castingDatesSettings');
    let order_id = $('#order_id', '#addQuestionsCD', '#castingDatesSettings').val();
    if (mode == 'get') {
        $.ajax({
            url: 'jobsoft_settings/ajax/ajax_data.php?page=casting_dates_for_nalog',
            type: 'POST',
            cache: false,
            data:{'order_id': order_id},
            success : function (result){
                var result_decode = JSON.parse(result);
                if (result_decode.status === 1) {
                    appointment_dates.empty();

                    $.each(result_decode.data, function (index, appointment) {
                        appointment_dates.append($('<option>', {
                            value: appointment.pap_id,
                            text: appointment.pap_date + ' - ' + appointment.pap_city
                        }));
                    });

                    appointment_dates.prop('required', true).selectpicker('refresh').closest('.form-group').removeClass('hidden');
                } else {
                    messageAQCD('warning', result_decode.message); 
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                if (xhr.status == 400) {
                    messageAQCD('danger', xhr.responseText); 
                } else {
                    messageAQCD('danger', thrownError);
                }
            }
        });
    } else {
        appointment_dates.empty().val(null).prop('required', false).selectpicker('refresh').closest('.form-group').addClass('hidden');
        destroySortableAQCD('hideen and destory');
        setPanelAndPquIdsAQCD(0, 0, 'unset');
    }
};
$('#setting_mode', '#addQuestionsCD', '#castingDatesSettings').on('change', function() {
    let setting_mode = $('#setting_mode', '#addQuestionsCD', '#castingDatesSettings').val();
    if (setting_mode == 'past') {
        getAppointmentDatesCD('delete');
        getAppointmentDatesCD('get');
        showHideNewQuestionsAQCD('hide','hide','hide'); 
    } else {
        getAppointmentDatesCD('delete');
        showHideNewQuestionsAQCD('show','show','hide');
        getNewQuestionsAQCD();
    }
});
function destroySortableAQCD(mode) {
    $('.questions_AQCD .panel .panel-body:first', '#addQuestionsCD', '#castingDatesSettings').each(function() {
        let sortableInstance = $(this).sortable("instance");
        if (sortableInstance) {
            $(this).sortable("destroy");
            $(this).empty();
        } else {
            $(this).empty();
        }
    });
    let appointment_dates = $('#appointment_dates', '#addQuestionsCD', '#castingDatesSettings');
    let questions_AQCD = $('.questions_AQCD', '#addQuestionsCD', '#castingDatesSettings');
    let sortableInstanceCategory = questions_AQCD.sortable("instance");
    if (mode == 'hideen and destory') {
        if (sortableInstanceCategory) {
            questions_AQCD.sortable("destroy");
            questions_AQCD.empty().addClass('hidden');
        } else {
            questions_AQCD.empty().addClass('hidden');
        }
        appointment_dates.val(null).selectpicker('refresh');
    } else {
        if (sortableInstanceCategory) {
            questions_AQCD.sortable("destroy");
            questions_AQCD.empty();
        } else {
            questions_AQCD.empty();
        }
    }
};
function showHideCategoryQuestions(thisRow) {
    let category_id = $(thisRow).data('category_id');
    let mode = $(thisRow).data('mode');
    if (mode == 'show') {
        $('.showCategoryQuestions', `#${category_id}`, '.questions_AQCD', '#addQuestionsCD', '#castingDatesSettings').addClass('hidden');
        $('.hideCategoryQuestions', `#${category_id}`, '.questions_AQCD', '#addQuestionsCD', '#castingDatesSettings').removeClass('hidden');
        $(`#${category_id} .panel-body:first`, '.questions_AQCD', '#addQuestionsCD', '#castingDatesSettings').addClass('hidden');
    }
    if (mode == 'hide') {
        $('.hideCategoryQuestions', `#${category_id}`, '.questions_AQCD', '#addQuestionsCD', '#castingDatesSettings').addClass('hidden');
        $('.showCategoryQuestions', `#${category_id}`, '.questions_AQCD', '#addQuestionsCD', '#castingDatesSettings').removeClass('hidden');
        $(`#${category_id} .panel-body:first`, '.questions_AQCD', '#addQuestionsCD', '#castingDatesSettings').removeClass('hidden');
    }
};
function setPanelAndPquIdsAQCD(panel_id, pqu_id, mode) {
    let panel_ids = $('#panel_ids', '#addQuestionsCD', '#castingDatesSettings');
    let pqu_ids = $('#pqu_ids', '#addQuestionsCD', '#castingDatesSettings');
    if (mode == 'set') {
        let currentPanelValue;
        let currentPquValue;

        if (panel_ids.val() === '' || panel_ids.val() === '[]') {
            currentPanelValue = [];
        } else {
            currentPanelValue = JSON.parse(panel_ids.val());
        }
        currentPanelValue.push(panel_id);
        panel_ids.val(JSON.stringify(currentPanelValue));

        if (pqu_ids.val() === '' || pqu_ids.val() === '[]') {
            currentPquValue = [];
        } else {
            currentPquValue = JSON.parse(pqu_ids.val());
        }
        currentPquValue.push(pqu_id);
        pqu_ids.val(JSON.stringify(currentPquValue));
    } else {
        panel_ids.val(null);
        pqu_ids.val(null);
    }
}; 
function updatePanelIdsAQCD() {
    setPanelAndPquIdsAQCD(0, 0, 'unset');
    $('.questions_AQCD form-field-question', '#addQuestionsCD', '#castingDatesSettings').each(function(index, element) {
        let newPanelId = index + 1;
        let formFieldQuestion = element;
        formFieldQuestion.panel_id = newPanelId;
        formFieldQuestion.requestUpdate();
        setPanelAndPquIdsAQCD(formFieldQuestion.panel_id, formFieldQuestion.pqu_id, 'set'); 
    });
}; 
function checkAndRemoveEmptyPanelsAQCD() {
    $('.panel-body:first', '.questions_AQCD .panel', '#addQuestionsCD', '#castingDatesSettings').each(function() {
        if ($(this).html().trim() === "") {
            let sortableInstance = $(this).sortable("instance");
            if (sortableInstance) {
                $(this).sortable("destroy");
                $(this).closest('.panel').remove();
            } else {
                $(this).closest('.panel').remove();
            }
        }
    });
    if ($('.questions_AQCD', '#addQuestionsCD', '#castingDatesSettings').html().trim() === "") {
        destroySortableAQCD('hideen and destory');
    }
};
function renderQuestionAQCD(result, editing_enabled) {
    setPanelAndPquIdsAQCD(0, 0, 'unset'); 
    let panel_id = 1;
    $.each(result.categories, function(index, category) {
        let panelSelector = `category_${((category.pqcId == null) ? 0 : category.pqcId)}_1`;
        let categoryPanel = $('#'+panelSelector);
        if (categoryPanel.length === 0) {
            let categoryPanel = $(`
                <div class="panel panel-primary" id="${panelSelector}">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-10">
                                <i class="fa fa-arrows-v me-3" aria-hidden="true"></i>
                                <strong>${((category.pqcId == null) ? 'Bez kategorije / Ohne Kategorie' : category.pqcName + ' / ' + category.pqcNameDe)}</strong>
                            </div>
                            <div class="col-xs-2 text-right">
                                <span class="showCategoryQuestions btn btn-default btn-xs" onclick="showHideCategoryQuestions(this)" data-mode="show" data-category_id="category_${((category.pqcId == null) ? 0 : category.pqcId)}_1"><i class="fa fa-eye" aria-hidden="true"></i></span>
                                <span class="hideCategoryQuestions btn btn-default btn-xs hidden" onclick="showHideCategoryQuestions(this)" data-mode="hide" data-category_id="category_${((category.pqcId == null) ? 0 : category.pqcId)}_1"><i class="fa fa-eye-slash" aria-hidden="true"></i></span>
                            </div>
                        </div>
                        
                    </div>
                    <div class="panel-body"></div>
                </div>
            `);
            $('.questions_AQCD', '#addQuestionsCD', '#castingDatesSettings').append(categoryPanel);
        }
        
        $.each(result.questions, function(questionId, question) {
            if (question.pqcId == category.pqcId) {
                let questionElement = $(`
                    <form-field-question
                        panel_id="${panel_id}"
                        pqu_id="${question.pqId}"
                        pqu_question="${question.pqQuestion}"
                        pqu_question_old="${question.pqQuestion}"
                        pqu_nalog_id="${question.pqOrder}"
                        pqu_has_text="${question.pqHasText}"
                        pqu_has_rating="${question.hasRating}"
                        pqu_has_dropdown="${question.hasDropdown}"
                        pqc_options='${JSON.stringify(Object.values(question.optionsData))}'
                        pqc_id="${question.pqcId}"
                        pqc_name="${question.pqcName}"
                        pqc_name_de="${question.pqcNameDe}"
                        checked="1"
                        enable_editing_text="${editing_enabled}"
                        question_in_form="${editing_enabled}"
                        flag_enpal_access="${question.pqEnpalAccess}"
                    ></form-field-question>
                `);
                $(`#${panelSelector} .panel-body:first`, '.questions_AQCD', '#addQuestionsCD', '#castingDatesSettings').append(questionElement);
                setPanelAndPquIdsAQCD(panel_id, question.pqId, 'set'); 
                panel_id++;
            }
        });

    });

    $('.questions_AQCD', '#addQuestionsCD', '#castingDatesSettings').removeClass('hidden'); 

    if (editing_enabled == 1) {
        $('.questions_AQCD', '#addQuestionsCD', '#castingDatesSettings').sortable({
            cursor: "move",
            update: function(event, ui) {
                updatePanelIdsAQCD();
            }
        });

        $.each(result.categories, function (index, category) {
            $(`#category_${((category.pqcId == null) ? 0 : category.pqcId)}_1 .panel-body:first`, '.questions_AQCD', '#addQuestionsCD', '#castingDatesSettings').sortable({
                cursor: "move", 
                update: function(event, ui) {
                    updatePanelIdsAQCD();
                }
            });
        });
    }

    updatePanelIdsAQCD();
}; 
function getQuestionsForAppointmentAQCD(pap_id, order_id, editing_enabled = 1) {
    $.ajax({
        url: 'jobsoft_settings/ajax/ajax_data.php?page=question_for_appointment',
        type: 'POST',
        cache: false,
        data:{'pap_id': pap_id, 'order_id': order_id},
        success : function (result){
            var result_decode = JSON.parse(result);
            if (result_decode.status === 1) {
                destroySortableAQCD('destory');
                renderQuestionAQCD(result_decode, editing_enabled);
                if (editing_enabled == 1) {
                    showHideNewQuestionsAQCD('show','hide','hide');
                }
            } else {
                messageAQCD('warning', result_decode.message);
                destroySortableAQCD('hideen and destory');
                showHideNewQuestionsAQCD('hide','hide','hide'); 
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            if (xhr.status == 400) {
                messageAQCD('danger', xhr.responseText); 
            } else {
                messageAQCD('danger', thrownError);
            }
            destroySortableAQCD('hideen and destory');
            showHideNewQuestionsAQCD('hide','hide','hide'); 
        }
    });
}; 
$('#appointment_dates', '#addQuestionsCD', '#castingDatesSettings').on('change', function() {
    let appointment_dates = $('#appointment_dates', '#addQuestionsCD', '#castingDatesSettings').val();
    let order_id = $('#order_id', '#addQuestionsCD', '#castingDatesSettings').val();
    getQuestionsForAppointmentAQCD(appointment_dates, order_id);
});
function showHideNewQuestionsAQCD(new_questions_add, new_questions, new_questions_decision) {
    let field_questions_add = $('#new_questions_add', '#addQuestionsCD', '#castingDatesSettings'); 
    let field_questions = $('#new_questions', '#addQuestionsCD', '#castingDatesSettings');
    let field_questions_decision = $('.new_questions_decision', '#addQuestionsCD', '#castingDatesSettings');

    if (new_questions_add != 'nothing') {
        if (new_questions_add == 'hide') {
            field_questions_add.closest('.form-group').addClass('hidden'); 
        } else {
            field_questions_add.closest('.form-group').removeClass('hidden'); 
        }
    }

    if (new_questions != 'nothing') {
        if (new_questions == 'hide') {
            field_questions.prop('required', false).empty().val(null).selectpicker('refresh').closest('.form-group').addClass('hidden');
        } else {
            field_questions.prop('required', true).val(null).selectpicker('refresh').closest('.form-group').removeClass('hidden');
        }
    }

    if (new_questions_decision != 'nothing') {
        if (new_questions_decision == 'hide') {
            field_questions_decision.addClass('hidden'); 
        } else {
            field_questions_decision.removeClass('hidden'); 
        }
    }
};
function getNewQuestionsAQCD() {
    let pqu_ids = $('#pqu_ids', '#addQuestionsCD', '#castingDatesSettings').val() || [];
    let order_id = $('#order_id', '#addQuestionsCD', '#castingDatesSettings').val();
    let new_questions = $('#new_questions', '#addQuestionsCD', '#castingDatesSettings');
    $.ajax({
        url: 'jobsoft_settings/ajax/ajax_data.php?page=question_for_order',
        type: 'POST',
        cache: false,
        data:{'order_id': order_id, 'pqu_ids': pqu_ids},
        success : function (result){
            var result_decode = JSON.parse(result);
            if (result_decode.status === 1) {
                new_questions.empty();
                let optgroupMap = {};
                $.each(result_decode.data, function (index, question) {
                    let category = question.pqc_catrgory_value;
                    let shortQuestion = question.pqu_question.length > 100 ? 
                        question.pqu_question.substring(0, 100) + '...' : 
                        question.pqu_question;

                    if (!optgroupMap[category]) {
                        optgroupMap[category] = $('<optgroup>', {
                            label: category
                        }).appendTo(new_questions);
                    }
                    optgroupMap[category].append($('<option>', {
                        value: question.pqu_id,
                        text: shortQuestion,
                        title: question.pqu_question,
                        'data-subtext': 'ID:' + question.pqu_id
                    }));
                });
                showHideNewQuestionsAQCD('show','show','hide');
            } else {
                messageAQCD('warning', result_decode.message); 
                showHideNewQuestionsAQCD('show','hide','hide');
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            if (xhr.status == 400) {
                messageAQCD('danger', xhr.responseText); 
                showHideNewQuestionsAQCD('show','hide','hide');
            } else {
                messageAQCD('danger', thrownError);
                showHideNewQuestionsAQCD('show','hide','hide');
            }
        }
    });
}; 
$('#new_questions', '#addQuestionsCD', '#castingDatesSettings').on('change', function() {
    let new_questions = $('#new_questions', '#addQuestionsCD', '#castingDatesSettings').val();
    if (new_questions !== null) {
        showHideNewQuestionsAQCD('nothing','nothing','show');
    } else {
        showHideNewQuestionsAQCD('nothing','nothing','hide');
    }
});
function decisionYesNewQuestions() {
    let new_questions = $('#new_questions', '#addQuestionsCD', '#castingDatesSettings').val();
    let order_id = $('#order_id', '#addQuestionsCD', '#castingDatesSettings').val();
    if (new_questions !== null) {
        $.ajax({
            url: 'jobsoft_settings/ajax/ajax_data.php?page=questions_for_order_with_ids',
            type: 'POST',
            cache: false,
            data:{'pqu_ids': new_questions, 'order_id':order_id},
            success : function (result){
                var result_decode = JSON.parse(result);
                if (result_decode.status === 1) {
                    renderQuestionAQCD(result_decode, 1);
                    showHideNewQuestionsAQCD('show','hide','hide'); 
                } else {
                    messageAQCD('warning', result_decode.message);
                    showHideNewQuestionsAQCD('hide','hide','hide'); 
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                if (xhr.status == 400) {
                    messageAQCD('danger', xhr.responseText); 
                } else {
                    messageAQCD('danger', thrownError);
                }
                destroySortableAQCD('hideen and destory');
                showHideNewQuestionsAQCD('hide','hide','hide'); 
            }
        });
    } else {
        showHideNewQuestionsAQCD('nothing','nothing','hide');
    }
};
function decisionNoNewQuestions() {
    showHideNewQuestionsAQCD('show','hide','hide');
};
$('#addQuestionsCDForm', '#addQuestionsCD', '#castingDatesSettings').on('submit', function(event) {
    event.preventDefault();
    let new_questions_decision = $('.new_questions_decision', '#addQuestionsCD', '#castingDatesSettings');
    if ($('.questions_AQCD', '#addQuestionsCD', '#castingDatesSettings').html().trim() === "") {
        messageAQCD('danger', "Za spremanje forme potrebno je odabrati barem jedno pitanje!");
    } else {
        if (new_questions_decision.hasClass('hidden')) {
            $('#addQuestionsCDForm', '#addQuestionsCD', '#castingDatesSettings').unbind('submit').submit();
        } else {
            messageAQCD('danger', "Za spremanje forme potrebno je potvrditi odabir na opciju <strong>Potvrdi odabir</strong> ili koristiti opciju <strong>Odustani</strong>!");
        }
    }
});

$(document).ready(function() {
    $(document).on('change', '#castingDatesSettings #addQuestionsCD .questions_AQCD form-field-question .materail-switch__element', function() {
        var switchElement = this;
        setTimeout(function() {
            if (!switchElement.checked) {
                $(switchElement).closest('form-field-question').remove();
                updatePanelIdsAQCD();
                checkAndRemoveEmptyPanelsAQCD();
            }
        }, 1000);
    });
});