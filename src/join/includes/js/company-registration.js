var iti;

$('#language').on('change', function() {
    changeLanguage();
});

function changeLanguage() {
    let selectedLanguage = $('#language option:selected');
    let newURL = selectedLanguage.data('address');
    window.location.href = newURL;
};

$('.step1 .btn-submit').on('click', function(event) {
    event.preventDefault();

    let isValid = true; 

    let company_name = $('#company_name');
    company_name.removeClass('is-invalid');
    let company_name_message_1 = $('.company_name_message_1');
    company_name_message_1.attr('hidden', true);
    let company_name_message_2 = $('.company_name_message_2');
    company_name_message_2.attr('hidden', true);
    if (company_name.attr('required')) {
        if (!company_name.val() || company_name.val().trim() === '') {
            company_name_message_1.removeAttr('hidden');
            company_name.addClass('is-invalid');
            isValid = false;
        } else {
            if (company_name.attr('minlength')) { 
                if (company_name.val().length < parseInt(company_name.attr('minlength'))) {
                    company_name_message_2.removeAttr('hidden');
                    company_name.addClass('is-invalid'); 
                    isValid = false;
                }
            }
        }
    } else {
        if (company_name.val() || company_name.val().trim() !== '') { 
            if (company_name.attr('minlength')) { 
                if (company_name.val().length < parseInt(company_name.attr('minlength'))) {
                    company_name_message_2.removeAttr('hidden');
                    company_name.addClass('is-invalid'); 
                    isValid = false;
                }
            }
        }
    }

    let company_zip_code = $('#company_zip_code');
    company_zip_code.removeClass('is-invalid');
    let company_zip_code_message_1 = $('.company_zip_code_message_1');
    company_zip_code_message_1.attr('hidden', true);
    if (company_zip_code.val() || company_zip_code.val().trim() !== '') { 
        if (company_zip_code.attr('minlength') || company_zip_code.attr('maxlength')) {
            if (company_zip_code.val().length != parseInt(company_zip_code.attr('minlength')) || company_zip_code.val().length != parseInt(company_zip_code.attr('maxlength'))) {
                company_zip_code_message_1.removeAttr('hidden');
                company_zip_code.addClass('is-invalid'); 
                isValid = false;
            }
        }
    }

    if (isValid) {
        stepsHiddenAtribute(true, false, true);
        updateStepState('step1', 'completed'); 
        updateStepState('step2', 'active');
    }
});

$('.step2 .btn-submit').on('click', function(event) {
    event.preventDefault();

    let isValid = true;

    let company_contact_email = $('#company_contact_email');
    company_contact_email.removeClass('is-invalid');
    let company_contact_email_message_1 = $('.company_contact_email_message_1');
    company_contact_email_message_1.attr('hidden', true);
    let company_contact_email_message_2 = $('.company_contact_email_message_2');
    company_contact_email_message_2.attr('hidden', true);
    let email_pattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    if (!company_contact_email.val() || company_contact_email.val().trim() === '') {
        company_contact_email_message_1.removeAttr('hidden');
        company_contact_email.addClass('is-invalid');
        isValid = false;
    } else if (!email_pattern.test(company_contact_email.val().trim())) {
        company_contact_email_message_2.removeAttr('hidden');
        company_contact_email.addClass('is-invalid');
        isValid = false;
    }

    let company_contact_phone = $('#company_contact_phone');
    company_contact_phone.removeClass('is-invalid');
    let company_contact_phone_message_1 = $('.company_contact_phone_message_1');
    company_contact_phone_message_1.attr('hidden', true);
    let company_contact_phone_message_2 = $('.company_contact_phone_message_2');
    company_contact_phone_message_2.attr('hidden', true);
    if (!company_contact_phone.val() || company_contact_phone.val().trim() === '') {
        company_contact_phone_message_1.removeAttr('hidden');
        company_contact_phone.addClass('is-invalid');
        isValid = false;
    } else if (!iti.isValidNumber()) {
        company_contact_phone_message_2.removeAttr('hidden');
        company_contact_phone.addClass('is-invalid');
        isValid = false;
    }

    if (isValid) {
        stepsHiddenAtribute(true, true, false);
        updateStepState('step2', 'completed'); 
        updateStepState('step3', 'active');
    }
});

$('.step3 .btn-submit').on('click', function(event) {
    event.preventDefault();

    $('.step3 .btn-submit').prop('disabled', true);
    $('.step3 .btn-quit').prop('disabled', true);
    $('.step3 .btn-submit .spinner-border').removeClass('visually-hidden');

    let isValid = true;

    let number_of_workers = $('#number_of_workers');
    number_of_workers.removeClass('is-invalid');
    let number_of_workers_message_1 = $('.number_of_workers_message_1');
    number_of_workers_message_1.attr('hidden', true);
    if (!number_of_workers.val() || number_of_workers.val().trim() === '') {
        number_of_workers_message_1.removeAttr('hidden');
        number_of_workers.addClass('is-invalid');
        isValid = false;
    }

    let schedule_a_call = $('#schedule_a_call').is(':checked');
    if (schedule_a_call) {
        let call_date = $('#call_date');
        call_date.removeClass('is-invalid');
        let call_date_message_1 = $('.call_date_message_1');
        call_date_message_1.attr('hidden', true);
        if (!call_date.val() || call_date.val().trim() === '') {
            call_date_message_1.removeAttr('hidden');
            call_date.addClass('is-invalid');
            isValid = false;
        }

        let call_time = $('#call_time');
        call_time.removeClass('is-invalid');
        let call_time_message_1 = $('.call_time_message_1');
        call_time_message_1.attr('hidden', true);
        let call_time_picker = call_time.closest('.bootstrap-select');
        call_time_picker.removeClass('is-invalid');
        if (!call_time.val() || call_time.val().trim() === '') {
            call_time_message_1.removeAttr('hidden');
            call_time.addClass('is-invalid');
            call_time_picker.addClass('is-invalid');
            isValid = false;
        }
    }

    let job_title = $('#job_title').val();
    let other_job_professions = [];
    if (job_title.includes("other")) {
        $('.other_job_profession_field').each(function() {
            let field_id = $(this).data('id');
            let input_field = $(`#other_job_profession_${field_id}`);
            input_field.removeClass('is-invalid');
            let error_message = $(`.other_job_profession_${field_id}_message_1`);
            error_message.attr('hidden', true);
            if (!input_field.val() || input_field.val().trim() === '') {
                input_field.addClass('is-invalid');
                error_message.removeAttr('hidden');
                isValid = false;
            } else {
                other_job_professions.push(input_field.val());
            }
        });
    }

    if (isValid) {

        let data = {};

        $('#company-registration input:not([type="hidden"]), #company-registration select').each(function() {
            data[$(this).attr('name')] = $(this).val();
        });

        $('#company-registration input[type="hidden"]').each(function() {
            data[$(this).attr('name')] = $(this).val();
        });

        if (schedule_a_call) {
            data['schedule_a_call'] = 'Yes';
        } else {
            data['schedule_a_call'] = 'No';
        }
        

        data['message'] = $('#message').val();

        data['company_contact_phone'] = iti.getNumber();
        data['company_contact_phone_country'] = iti.getSelectedCountryData().name;

        if (job_title.includes("other")) {
            data['other_job_professions'] = other_job_professions;
        } else {
            data['other_job_professions'] = [];
        }

        console.log(data);
        
        $.ajax({
            url: data['url_c'] + 'do.php?form=company_registration',
            type: 'POST',
            cache: false,
            data: data, 
            success : function (response){
                try {
                    var res = JSON.parse(response);
                    if (res.status === 'error') {
                        alert(res.message);
                    } else {
                        window.location.href = data['url_j'] + 'company/thankyou/' + data['current_language'];
                    }
                } catch (e) {
                    alert('Unexpected error occurred.');
                }

                $('.step3 .btn-submit').prop('disabled', false);
                $('.step3 .btn-quit').prop('disabled', false);
                $('.step3 .btn-submit .spinner-border').addClass('visually-hidden');

            },
            error: function (xhr, ajaxOptions, thrownError) {
                try {
                    var res = JSON.parse(xhr.responseText);
                    if (res.status === 'error') {
                        alert(res.message);
                    } else {
                        alert(thrownError);
                    }
                } catch (e) {
                    alert('Unexpected error occurred.');
                }

                $('.step3 .btn-submit').prop('disabled', false);
                $('.step3 .btn-quit').prop('disabled', false);
                $('.step3 .btn-submit .spinner-border').addClass('visually-hidden');
            }
        });
    } else {
        $('.step3 .btn-submit').prop('disabled', false);
        $('.step3 .btn-quit').prop('disabled', false);
        $('.step3 .btn-submit .spinner-border').addClass('visually-hidden');
    }
});

$('.step1 .btn-quit').on('click', function(event) {
    event.preventDefault();
});

$('.step2 .btn-quit').on('click', function(event) {
    event.preventDefault();
    stepsHiddenAtribute(false, true, true);
    updateStepState('step1', 'active'); 
    updateStepState('step2', 'default');
});

$('.step3 .btn-quit').on('click', function(event) {
    event.preventDefault();
    stepsHiddenAtribute(true, false, true);
    updateStepState('step2', 'active'); 
    updateStepState('step3', 'default');
});

function stepsHiddenAtribute(step1, step2, step3) {
    var step1Field = $('form .step1'); 
    var step2Field = $('form .step2'); 
    var step3Field = $('form .step3'); 

    if (step1 == true) {
        if (!step1Field.attr('hidden')) {
            step1Field.attr('hidden', true);
        }   
    } else {
        if (step1Field.attr('hidden')) {
            step1Field.removeAttr('hidden'); 
        } 
    }

    if (step2 == true) {
        if (!step2Field.attr('hidden')) {
            step2Field.attr('hidden', true);
        }   
    } else {
        if (step2Field.attr('hidden')) {
            step2Field.removeAttr('hidden'); 
        } 
    }

    if (step3 == true) {
        if (!step3Field.attr('hidden')) {
            step3Field.attr('hidden', true);
        }   
    } else {
        if (step3Field.attr('hidden')) {
            step3Field.removeAttr('hidden'); 
        } 
    }
}; 

function updateStepState(stepId, state) {
    $('#' + stepId).removeClass('active completed default');
    $('#' + stepId).addClass(state);
};

$(document).ready(function() {

    updateRemainingChars();
    toggleFields();
    getJobTitle();
    updateButtonStyles();

    var company_contact_phone = document.querySelector("#company_contact_phone");
    iti = window.intlTelInput(company_contact_phone, {
        initialCountry: 'auto',
        geoIpLookup: function(callback) {
            $.get('https://ipinfo.io', function() {}, "jsonp").always(function(resp) {
                var countryCode = (resp && resp.country) ? resp.country : "us";
                callback(countryCode);
            });
        },
        separateDialCode: true,
        showSelectedDialCode: true,
        countryOrder: ['de','ba','hr','rs'],
        utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.11/build/js/utils.js",
    });
});

$('#message').on('input', function() {
    updateRemainingChars();
});

function updateRemainingChars() {
    var maxLength = $('#message').attr('maxlength');
    var currentLength = $('#message').val().length;
    var remainingChars = maxLength - currentLength;
    $('.char-counter').text(remainingChars);
}; 

document.addEventListener('DOMContentLoaded', function() {
    flatpickr("#call_date", {
        dateFormat: "d.m.Y",
        allowInput: false,
        locale: {
            firstDayOfWeek: 1
        }
    });
});

function toggleFields() {
    const checkbox = $('#schedule_a_call');
    const dateField = $('#call_date').closest('.col-md-12');
    const timeField = $('#call_time').closest('.col-md-12');
    const dateFieldRequired = $('#call_date'); 
    const timeFieldRequired = $('#call_time'); 

    if (checkbox.is(':checked')) {
        if (dateField.attr('hidden')) {
            dateField.removeAttr('hidden'); 
        }
        if (!dateFieldRequired.attr('required')) {
            dateFieldRequired.attr('required', 'required'); 
        }
        if (timeField.attr('hidden')) {
            timeField.removeAttr('hidden'); 
        }
        if (!timeFieldRequired.attr('required')) {
            timeFieldRequired.attr('required', 'required'); 
        }
    } else {
        if (!dateField.attr('hidden')) {
            dateField.attr('hidden', true);
        }
        if (dateFieldRequired.attr('required')) {
            dateFieldRequired.removeAttr('required'); 
        }
        if (!timeField.attr('hidden')) {
            timeField.attr('hidden', true);
        } 
        if (timeFieldRequired.attr('required')) {
            timeFieldRequired.removeAttr('required'); 
        }
    }
}; 

$('#schedule_a_call').on('change', function() {
    toggleFields();
});

$('#number_of_workers').on('input', function() {
    this.value = this.value.replace(/\D/g, '');
});

$('#company_zip_code').on('input', function() {
    this.value = this.value.replace(/\D/g, '');
    if (this.value.length > 5) {
        this.value = this.value.slice(0, 5);
    }
});

$('.selectpicker').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
    var selectedValue = $(this).val();
    var button = $(this).siblings('.btn');
    
    if (!selectedValue || selectedValue.length === 0) {
        button.addClass('placeholder-selected').removeClass('value-selected');
    } else {
        button.addClass('value-selected').removeClass('placeholder-selected');
    }
});

function updateButtonStyles() {
    $('.selectpicker').each(function() {
        var selectedValue = $(this).val();
        var button = $(this).siblings('.btn');

        if (!selectedValue || selectedValue.length === 0) {
            button.addClass('placeholder-selected').removeClass('value-selected');
        } else {
            button.addClass('value-selected').removeClass('placeholder-selected');
        }
    });
}; 

function getJobTitle() {
    let url_c = $('#url_c').val();
    let url_j = $('#url_j').val();
    let current_language = $('#current_language').val();

    getTranslations(current_language, url_j).then(translations => {
        $.ajax({
            url: url_c + 'do.php?form=company_registration_job_title',
            type: 'POST',
            cache: false,
            data:{
                'current_language': current_language,
                'url_j': url_j, 
                'url_c': url_c
            },
            success : function (result){
                var result_decode = JSON.parse(result);
                let job_title = $('#job_title'); 
                if (result_decode.status === 1) {
                    job_title.empty();
                    result_decode.data.forEach(function(item) {
                        job_title.append('<option value="' + item.kp_id + '">' + item.profession_name + '</option>');
                    });
                }
                job_title.append('<option value="other">'+translations.Ostalo+'</option>');
                job_title.selectpicker('refresh');
            },
            error: function (xhr, ajaxOptions, thrownError) {
                if (xhr.status == 400) {
                    alert(xhr.responseText);
                } else {
                    alert(thrownError);
                }
            }
        });
    })
    .catch(error => {
        console.error('Error fetching translations:', error);
    });
    
};

$('#job_title').on('change', function() {
    let job_title = $('#job_title').val();
    if (job_title.includes("other")) {
        if ($('#other_job_profession_cnt').val() === '' || $('#other_job_profession_cnt').val() === '[]') { 
            addOtherJobProfessionField();
            $('#other_job_professions').removeAttr('hidden');  
        }
    } else {
        deleteAllOtherJobProfessionField();
    }
});

function getTranslations(currentLanguage, urlJ) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: urlJ +'translations/get-trans.php?page=company_registration_trans',
            type: 'POST',
            data: { 'current_language': currentLanguage },
            success: function (result) {
                try {
                    const translations = JSON.parse(result);
                    resolve(translations);
                } catch (error) {
                    reject('Error parsing JSON');
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                reject(thrownError);
            }
        });
    });
}; 

function makeOtherJobProfessionField(generateId, labelTrans, placeholderTrans, errorMessage) {
    return `
        <div class="row other_job_profession_field mb-3" data-id="${generateId}">
            <div class="col-9">
                <label for="other_job_profession_${generateId}" class="form-label inter-500 fs-7 js-gray-700-color">* ${labelTrans}</label>
                <input type="text" class="form-control inter-400 fs-6" id="other_job_profession_${generateId}" name="other_job_profession_${generateId}" placeholder="${placeholderTrans}" maxlength="149" required>
            </div>
            <div class="col-3 d-flex flex-column justify-content-end">
                <button class="btn btn-delete w-100 inter-400 js-gray-500-color fs-6 mb-2 delete_other_job_proffesion_field" data-id="${generateId}"><i class="fa fa-times" aria-hidden="true"></i></button>
            </div>
            <div class="col-9">
                <div class="form-text inter-400 fs-8 other_job_profession_${generateId}_message_1" hidden>
                    ${errorMessage}
                </div>
            </div>
        </div>
    `;
}; 

function addOtherJobProfessionField() {
    let url_j = $('#url_j').val();
    let current_language = $('#current_language').val();
    getTranslations(current_language, url_j).then(translations => {
        var currentValue;
        var nextValue;
        var nextHtml;  
        if ($('#other_job_profession_cnt').val() === '' || $('#other_job_profession_cnt').val() === '[]') {
            currentValue = [];
            nextValue = 1; 
        } else {
            currentValue = JSON.parse($('#other_job_profession_cnt').val());
            nextValue = currentValue[currentValue.length - 1] + 1;
        }
        currentValue.push(nextValue);
        $('#other_job_profession_cnt').val(JSON.stringify(currentValue));
        nextHtml = makeOtherJobProfessionField(nextValue, translations.Drugo_zanimanje, translations.Unesite_drugo_zanimanje, translations.Molimo_popunite_ovo_polje); 
        $('#other_job_professions_fields').append(nextHtml);
    })
    .catch(error => {
        console.error('Error fetching translations:', error);
    });
}; 

function deleteOtherJobProfessionField() {
    var optionHtml = $(this);
    var optionId = optionHtml.attr('data-id');
    optionHtml.closest('.other_job_profession_field').remove();

    var currentValue = JSON.parse($('#other_job_profession_cnt').val());
    var index = currentValue.indexOf(parseInt(optionId));
    if (index !== -1) {
        currentValue.splice(index, 1);
    }
    $('#other_job_profession_cnt').val(JSON.stringify(currentValue));
    if (currentValue.length === 0) {
        deleteAllOtherJobProfessionField();
        let job_title = $('#job_title').val();
        console.log('job_title: ' + job_title); 
        let index_other = job_title.indexOf("other");
        console.log('index_other: ' +index_other);
        if (index_other > -1) {
            job_title.splice(index_other, 1);
            $('#job_title').selectpicker('destroy');
            $('#job_title').val(job_title);
            $('#job_title').selectpicker('destroy');
            $('#job_title').selectpicker('render');
            var selectedValue = $('#job_title').val();
            var button = $('#job_title').siblings('.btn');
            
            if (!selectedValue || selectedValue.length === 0) {
                button.addClass('placeholder-selected').removeClass('value-selected');
            } else {
                button.addClass('value-selected').removeClass('placeholder-selected');
            }
        }
    }
};

function deleteAllOtherJobProfessionField() {
    $('#other_job_professions_fields').empty();
    $('#other_job_profession_cnt').val(null).closest('#other_job_professions').attr('hidden', true);
};

$('#other_job_professions').on('click', '.delete_other_job_proffesion_field', deleteOtherJobProfessionField);
$('#other_job_professions').on('click', '.add_other_job_proffesion_field', addOtherJobProfessionField);

    
