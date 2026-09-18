<?php 
    if ($orderId != 0) { 
        ?>
            <style>
                #predefinedQuestionsSettings #addNewPQ .modal-body #numberOptionsANPQ {
                    text-align: center; 
                    font-size: large; 
                    font-weight: bold;
                }

                #predefinedQuestionsSettings #addNewPQ .modal-body .fieldsOptionANPQ .row{
                    display: flex; 
                    align-items: center;
                }

                #predefinedQuestionsSettings #editPQ .modal-body .fieldsOptionEPQ .row{
                    display: flex; 
                    align-items: center;
                }

                #predefinedQuestionsSettings #addNewPQ .mt-15{
                    margin-top: 15px;
                }
            </style>
        <?php 
            /*
                Short form for PQ - Predefined Questions
            */
        ?>
            <!-- 
                Modal ADD START 
                -->
                    <div class="row">
                        <div class="col-sm-12 text-right">
                            <button 
                                data-toggle="modal" data-target="#addNewPQ"
                                class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column"
                            >
                                <i class="fa fa-plus" aria-hidden="true"></i>
                                <span>
                                    Dodaj pitanje
                                </span>
                            </button>
                        </div>
                    </div>
                    <div class="modal material-modal material-modal_primary fade text-left" id="addNewPQ">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content material-modal__content">
                                <div class="modal-header material-modal__header">
                                    <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title material-modal__title">
                                        <i style = "margin-right: 10px;" class="fa fa-plus" aria-hidden="true"></i>
                                        <span>
                                            Dodaj novo <strong>Predefinisano pitanje</strong>
                                        </span>
                                    </h4>
                                </div> 
                                <div class="modal-body material-modal__body">
                                    <form action="<?php getSiteURL(); ?>jobsoft_settings/backend/do_settings.php?page=add_new_predefined_question" enctype="multipart/form-data" method="post" role="form" class="form-horizontal" id="formAddNewPQ">
                                        <input type="hidden" id="orderANPQ" name="orderANPQ" value="<?php echo $orderId; ?>">
                                        <!-- 
                                            Short form for ANPQ - Add New Predefined Questions
                                        -->
                                        <div class="row">
                                            <div class="col-md-offset-2 col-sm-8">
                                                <!-- 
                                                    Text pitanja START
                                                    -->
                                                        <div class="form-group">
                                                            <div class="col-sm-12">
                                                                <label for="questionTextANPQ" class="col-sm-4 control-label">
                                                                    <span class="text-danger">
                                                                        *
                                                                    </span>
                                                                    Text:
                                                                </label>
                                                                <div class="col-sm-8">
                                                                    <div class="materail-input-block materail-input-block_primary materail-input_slide-line">
                                                                        <textarea class="form-control materail-input material-textarea" name="questionTextANPQ" id="questionTextANPQ" placeholder="Unesite text pitanja" rows="4" required></textarea>
                                                                        <span class="materail-input-block__line"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <!-- 
                                                    Text pitanja END
                                                -->

                                                <!-- 
                                                    Kategorija START
                                                    -->
                                                        <div class="form-group">
                                                            <div class="col-sm-12">
                                                                <label for="categoryANPQ" class="col-sm-4 control-label">
                                                                    <span class="text-danger">
                                                                        *
                                                                    </span>
                                                                    Kategorije:
                                                                </label>
                                                                <div class="col-sm-8">
                                                                    <select class="selectpicker" id="categoryANPQ" name="categoryANPQ" data-live-search="true" title = "Odaberite kategoriju" required>

                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <!-- 
                                                    Kategorija END
                                                -->

                                                <!-- 
                                                    Nova kategorija START
                                                    -->
                                                        <div class="form-group hidden">
                                                            <div class="col-sm-12">
                                                                <label for="newCategoryANPQ" class="col-sm-4 control-label">
                                                                    <span class="text-danger">
                                                                        *
                                                                    </span>
                                                                    Nova kategorija:
                                                                </label>
                                                                <div class="col-sm-8">
                                                                    <div class="materail-input-block materail-input-block_primary materail-input_slide-line">
                                                                        <input class="form-control materail-input" type="text" name="newCategoryANPQ" id="newCategoryANPQ" placeholder="Unesite naziv kategorije">
                                                                        <span class="materail-input-block__line"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="form-group hidden">
                                                            <div class="col-sm-12">
                                                                <label for="newDeCategoryANPQ" class="col-sm-4 control-label">
                                                                    <span class="text-danger">
                                                                        *
                                                                    </span>
                                                                    Nova kategorija (DE):
                                                                </label>
                                                                <div class="col-sm-8">
                                                                    <div class="materail-input-block materail-input-block_primary materail-input_slide-line">
                                                                        <input class="form-control materail-input" type="text" name="newDeCategoryANPQ" id="newDeCategoryANPQ" placeholder="Unesite naziv kategorije (DE)">
                                                                        <span class="materail-input-block__line"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <!-- 
                                                    Nova kategorija END
                                                -->

                                                <!-- 
                                                    Opcije START
                                                    -->
                                                        <div class="form-group">
                                                            <div class="col-sm-12">
                                                                <label for="hasTextANPQ" class="col-sm-4 control-label">
                                                                    Ima text:
                                                                </label>
                                                                <div class="col-sm-8">
                                                                    <div class="main-container__column materail-switch materail-switch_primary">
                                                                        <input class="materail-switch__element" type="checkbox" id="hasTextANPQ" name="hasTextANPQ" value="1">
                                                                        <label class="materail-switch__label" for="hasTextANPQ"></label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="col-sm-12">
                                                                <label for="hasRatingANPQ" class="col-sm-4 control-label">
                                                                    Ima ocjenu:
                                                                </label>
                                                                <div class="col-sm-8">
                                                                    <div class="main-container__column materail-switch materail-switch_primary">
                                                                        <input class="materail-switch__element" type="checkbox" id="hasRatingANPQ" name="hasRatingANPQ" value="1">
                                                                        <label class="materail-switch__label" for="hasRatingANPQ"></label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="col-sm-12">
                                                                <label for="hasDropdownANPQ" class="col-sm-4 control-label">
                                                                    Ima dropdown:
                                                                </label>
                                                                <div class="col-sm-8">
                                                                    <div class="main-container__column materail-switch materail-switch_primary">
                                                                        <input class="materail-switch__element" type="checkbox" id="hasDropdownANPQ" name="hasDropdownANPQ" value="1">
                                                                        <label class="materail-switch__label" for="hasDropdownANPQ"></label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <!-- 
                                                    Opcije END
                                                -->
                                                <!-- 
                                                    Opcije dropdown START
                                                    -->
                                                        <div id="contentOptionsANPQ" class="hidden">
                                                            
                                                        </div>
                                                    <!-- 
                                                    Opcije dropdown END
                                                -->
                                                <!-- 
                                                    Dodaj opciju START
                                                    -->
                                                        <div class="form-group mt-15 hidden">
                                                            <div class="col-sm-12">
                                                                <label for="plusNumberOptionsANPQ" class="col-sm-10 control-label text-right">
                                                                    Dodaj novo polje opcije:
                                                                </label>
                                                                <div class="col-sm-2 text-center">
                                                                    <span id="plusNumberOptionsANPQ" class="btn material-btn btn-success"><i class="fa fa-plus" aria-hidden="true"></i></span>
                                                                </div>
                                                                <div class="hidden">
                                                                    <input type="hidden" id="numbersOptionsANPQ" name="numbersOptionsANPQ" />
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <!-- 
                                                    Dodaj opciju END
                                                -->
                                            </div>
                                        </div>
                                        <div class="form-group idk_margin_top20">
                                            <div class="col-md-offset-2 col-sm-8 text-center">
                                                <small>
                                                    <strong>
                                                        Sva polja označena sa 
                                                        <span class="text-danger">
                                                            *
                                                        </span>  
                                                        su obavezna!
                                                    </strong>
                                                </small>
                                            </div>
                                        </div>
                                        <div class="modal-footer material-modal__footer">
                                            <button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
                                            <button type="submit" class="btn btn-primary material-btn material-btn_primary" form="formAddNewPQ"><i class="fa fa-check-square-o" aria-hidden="true"></i> Spremi</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                        function makeOptionFieldsANPQ(genereteId) {
                            return `
                                <div class="fieldsOptionANPQ panel panel-default" data-option="`+genereteId+`">
                                    <div class="panel-heading">
                                        <div class="row">
                                            <div class="col-xs-10 text-left">
                                                <strong>Opcija `+genereteId+`</strong>
                                            </div>
                                            <div class="col-xs-2 text-right">
                                                <span class="btn material-btn btn-danger removeNumberOptionsANPQ"><i class="fa fa-trash" aria-hidden="true"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="textOptions`+genereteId+`ANPQ" class="col-sm-4 control-label">
                                                    <span class="text-danger">
                                                        *
                                                    </span>
                                                    Text opcije (bs, sr, hr):
                                                </label>
                                                <div class="col-sm-8">
                                                    <div class="materail-input-block materail-input-block_primary">
                                                        <input class="form-control materail-input" type="text" name="textOptions`+genereteId+`ANPQ" id="textOptions`+genereteId+`ANPQ" placeholder="Unesite text (bs,sr,hr)" required>
                                                        <span class="materail-input-block__line"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="textdeOptions`+genereteId+`ANPQ" class="col-sm-4 control-label">
                                                    <span class="text-danger">
                                                        *
                                                    </span>
                                                    Text opcije (de):
                                                </label>
                                                <div class="col-sm-8">
                                                    <div class="materail-input-block materail-input-block_primary">
                                                        <input class="form-control materail-input" type="text" name="textdeOptions`+genereteId+`ANPQ" id="textdeOptions`+genereteId+`ANPQ" placeholder="Unesite text (de)" required>
                                                        <span class="materail-input-block__line"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="subtextOptions`+genereteId+`ANPQ" class="col-sm-4 control-label">
                                                    Subtext opcije (bs, sr, hr):
                                                </label>
                                                <div class="col-sm-8">
                                                    <div class="materail-input-block materail-input-block_primary">
                                                        <input class="form-control materail-input" type="text" name="subtextOptions`+genereteId+`ANPQ" id="subtextOptions`+genereteId+`ANPQ" placeholder="Unesite subtext (bs,sr,hr)">
                                                        <span class="materail-input-block__line"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label for="subtextdeOptions`+genereteId+`ANPQ" class="col-sm-4 control-label">
                                                    Subtext opcije (de):
                                                </label>
                                                <div class="col-sm-8">
                                                    <div class="materail-input-block materail-input-block_primary">
                                                        <input class="form-control materail-input" type="text" name="subtextdeOptions`+genereteId+`ANPQ" id="subtextdeOptions`+genereteId+`ANPQ" placeholder="Unesite subtext (de)">
                                                        <span class="materail-input-block__line"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        };
                        function addOptionsFieldsANPQ() {
                            var currentValue;
                            var nextValue;
                            var nextHtml;  
                            if ($('#numbersOptionsANPQ', '#addNewPQ', '#predefinedQuestionsSettings').val() === '' || $('#numbersOptionsANPQ', '#addNewPQ', '#predefinedQuestionsSettings').val() === '[]') {
                                currentValue = [];
                                nextValue = 1; 
                            } else {
                                currentValue = JSON.parse($('#numbersOptionsANPQ', '#addNewPQ', '#predefinedQuestionsSettings').val());
                                nextValue = currentValue[currentValue.length - 1] + 1;
                            }
                            currentValue.push(nextValue);
                            $('#numbersOptionsANPQ', '#addNewPQ', '#predefinedQuestionsSettings').val(JSON.stringify(currentValue));
                            nextHtml = makeOptionFieldsANPQ(nextValue); 
                            $('#contentOptionsANPQ', '#addNewPQ', '#predefinedQuestionsSettings').append(nextHtml);
                        };
                        function deleteOptionFieldANPQ() {
                            var optionHtml = $(this).closest('.fieldsOptionANPQ', '#contentOptionsANPQ', '#addNewPQ', '#predefinedQuestionsSettings');
                            var optionId = optionHtml.attr('data-option');
                            optionHtml.remove();

                            var currentValue = JSON.parse($('#numbersOptionsANPQ', '#addNewPQ', '#predefinedQuestionsSettings').val());
                            var index = currentValue.indexOf(parseInt(optionId));
                            if (index !== -1) {
                                currentValue.splice(index, 1);
                            }
                            $('#numbersOptionsANPQ', '#addNewPQ', '#predefinedQuestionsSettings').val(JSON.stringify(currentValue));
                            if (currentValue.length === 0) {
                                $('#numbersOptionsANPQ', '#addNewPQ', '#predefinedQuestionsSettings').val(null).closest('.form-group').addClass('hidden');
                                $('#contentOptionsANPQ', '#addNewPQ', '#predefinedQuestionsSettings').empty().addClass('hidden');
                                $('#hasDropdownANPQ', '#addNewPQ', '#predefinedQuestionsSettings').prop('checked', false);
                            }
                        };
                        function showHideOptionsFieldsANPQ() {
                            let hasDropdownANPQ = $('#hasDropdownANPQ', '#addNewPQ', '#predefinedQuestionsSettings');
                            if (hasDropdownANPQ.is(':checked')) {
                                $('#numbersOptionsANPQ', '#addNewPQ', '#predefinedQuestionsSettings').val(null).closest('.form-group').removeClass('hidden');
                                $('#contentOptionsANPQ', '#addNewPQ', '#predefinedQuestionsSettings').empty().removeClass('hidden');
                                addOptionsFieldsANPQ();
                            } else {
                                $('#numbersOptionsANPQ', '#addNewPQ', '#predefinedQuestionsSettings').val(null).closest('.form-group').addClass('hidden');
                                $('#contentOptionsANPQ', '#addNewPQ', '#predefinedQuestionsSettings').empty().addClass('hidden');
                            }
                        };
                        function getQuestionCategoryANPQ() {
                            $.ajax({
                                url: 'jobsoft_settings/backend/do_settings.php?page=get_question_categories',
                                type: 'POST',
                                processData: false,
                                contentType: false,
                                cache: false,
                                dataType: 'JSON',
                                success : function (response){
                                    let categoryANPQ = $('#categoryANPQ', '#addNewPQ', '#predefinedQuestionsSettings');
                                    let newCategoryANPQ = $('#newCategoryANPQ', '#addNewPQ', '#predefinedQuestionsSettings');
                                    let newDeCategoryANPQ = $('#newDeCategoryANPQ', '#addNewPQ', '#predefinedQuestionsSettings');
                                    categoryANPQ.append($('<option>', {
                                        value: 'without',
                                        text: 'Bez kategorije',
                                        style: 'background: #f0ad4e; color: #fff;' 
                                    }));
                                    categoryANPQ.append($('<option>', {
                                        value: 'new',
                                        text: 'Dodaj novu kategoriju',
                                        style: 'background: #5cb85c; color: #fff;'
                                    }));
                                    response.forEach(function(item) {
                                        categoryANPQ.append($('<option>', {
                                            value: item.pqc_id,
                                            text: item.pqc_name + ' / ' + item.pqc_name_de
                                        }));
                                    }); 
                                    categoryANPQ.val(null).selectpicker('refresh');
                                    newCategoryANPQ.val(null).prop('required', false).closest('.form-group').addClass('hidden'); 
                                    newDeCategoryANPQ.val(null).prop('required', false).closest('.form-group').addClass('hidden'); 
                                },
                                error: function (xhr, ajaxOptions, thrownError) {
                                    if (xhr.status === 204) {
                                        alert(xhr.responseText);
                                    } else {
                                        alert(xhr.status);
                                    }
                                }
                            });
                        };
                        function resetQuestionCategoryANPQ() {
                            if ($('#categoryANPQ', '#addNewPQ', '#predefinedQuestionsSettings').children().length > 0) {
                                $('#categoryANPQ', '#addNewPQ', '#predefinedQuestionsSettings').empty();
                            }
                            $('#categoryANPQ', '#addNewPQ', '#predefinedQuestionsSettings').val(null).selectpicker('refresh');
                            $('#newCategoryANPQ', '#addNewPQ', '#predefinedQuestionsSettings').val(null).prop('required', false).closest('.form-group').addClass('hidden');
                            $('#newDeCategoryANPQ', '#addNewPQ', '#predefinedQuestionsSettings').val(null).prop('required', false).closest('.form-group').addClass('hidden');
                        };
                        function showHideNewCategoryANPQ() {
                            let categoryANPQ = $('#categoryANPQ', '#addNewPQ', '#predefinedQuestionsSettings').val();
                            if (categoryANPQ == 'new') {
                                $('#newCategoryANPQ', '#addNewPQ', '#predefinedQuestionsSettings').val(null).prop('required', true).closest('.form-group').removeClass('hidden');
                                $('#newDeCategoryANPQ', '#addNewPQ', '#predefinedQuestionsSettings').val(null).prop('required', true).closest('.form-group').removeClass('hidden');
                            } else {
                                $('#newCategoryANPQ', '#addNewPQ', '#predefinedQuestionsSettings').val(null).prop('required', false).closest('.form-group').addClass('hidden');
                                $('#newDeCategoryANPQ', '#addNewPQ', '#predefinedQuestionsSettings').val(null).prop('required', false).closest('.form-group').addClass('hidden');
                            }
                        }; 
                       
                        $('#hasDropdownANPQ', '#addNewPQ', '#predefinedQuestionsSettings').on('click', showHideOptionsFieldsANPQ);
                        $('#plusNumberOptionsANPQ', '#addNewPQ', '#predefinedQuestionsSettings').on('click', addOptionsFieldsANPQ);
                        $('#contentOptionsANPQ', '#addNewPQ', '#predefinedQuestionsSettings').on('click', '.removeNumberOptionsANPQ', deleteOptionFieldANPQ);
                        $('#addNewPQ', '#predefinedQuestionsSettings').on('shown.bs.modal', getQuestionCategoryANPQ);
                        $('#addNewPQ', '#predefinedQuestionsSettings').on('hidden.bs.modal', resetQuestionCategoryANPQ);
                        $('#categoryANPQ', '#addNewPQ', '#predefinedQuestionsSettings').on('change', showHideNewCategoryANPQ);
                    </script>
                    <div class="row">
                        <div class="col-xs-12">
                            <hr>
                        </div>
                    </div>
                <!-- 
                Modal ADD END 
            -->
        <?php 

            $queryUsersCRM = $db->prepare("
                SELECT 
                    employee_id AS userId, 
                    CONCAT(employee_firstname, ' ', employee_lastname) AS userFullName
                FROM 
                    idk_employees
            "); 
            $queryUsersCRM->execute(); 
            $rowsUsersCRM = $queryUsersCRM->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
            $rowsUsersCRM = array_map('reset', $rowsUsersCRM);

            $queryGetPQ = $db->prepare('
                SELECT 
                    pqu.pqu_id AS pqId,
                    pqu.pqu_nalog_id AS pqOrder, 
                    pqu.pqu_question AS pqQuestion, 
                    pqu.pqu_user_id AS pqUser,
                    pqc.pqc_id AS pqcId,
                    pqc.pqc_name AS pqcName, 
                    pqc.pqc_name_de AS pqcNameDe,
                    pqu.pqu_has_text AS pqHasText, 
                    pqu.pqu_has_rating AS hasRating, 
                    pqu.pqu_has_dropdown AS hasDropdown, 
                    pqo.pqo_id AS pqoId, 
                    pqo.pqo_value AS pqoValue,
                    pqo.pqo_value_text AS pqoValueText,
                    pqo.pqo_value_text_de AS pqoValueTextDe,
                    pqo.pqo_value_subtext AS pqoValueSubtext,
                    pqo.pqo_value_subtext_de AS pqoValueSubtextDe
                FROM 
                    idk_pp_questions pqu 
                LEFT JOIN 
                    idk_pp_question_categories pqc 
                ON 
                    pqu.pqu_category_id = pqc.pqc_id
                LEFT JOIN 
                    idk_pp_question_options pqo 
                ON 
                    pqu.pqu_id = pqo.pqo_question_id
                    AND 
                    pqu.pqu_has_dropdown = 1
                WHERE 
                    pqu.pqu_nalog_id = :orderId
                ORDER BY
                    pqu.pqu_id
                DESC
            ');
            $queryGetPQ->execute(array(
                ':orderId' => $orderId
            )); 
            if ($queryGetPQ->rowCount() > 0) {
                $rowsGetPQ = $queryGetPQ->fetchAll(PDO::FETCH_ASSOC);
                $groupedData = array();
                foreach ($rowsGetPQ as $rowGetPQ) {
                    if (!isset($groupedData[$rowGetPQ['pqId']])) {
                        $groupedData[$rowGetPQ['pqId']] = array(
                            'pqId' => $rowGetPQ['pqId'],
                            'pqOrder' => $rowGetPQ['pqOrder'],
                            'pqQuestion' => $rowGetPQ['pqQuestion'],
                            'pqUser' => $rowGetPQ['pqUser'],
                            'pqcId' => $rowGetPQ['pqcId'],
                            'pqcName' => $rowGetPQ['pqcName'],
                            'pqcNameDe' => $rowGetPQ['pqcNameDe'],
                            'pqHasText' => $rowGetPQ['pqHasText'],
                            'hasRating' => $rowGetPQ['hasRating'],
                            'hasDropdown' => $rowGetPQ['hasDropdown'],
                            'optionsData' => array()
                        );
                    }
                    if($rowGetPQ['pqoId'] !== NULL) {
                        $dataPQ = array( 
                            'pqoId' => $rowGetPQ['pqoId'],
                            'pqoValue' => $rowGetPQ['pqoValue'],
                            'pqoValueText' => $rowGetPQ['pqoValueText'],
                            'pqoValueTextDe' => $rowGetPQ['pqoValueTextDe'],
                            'pqoValueSubtext' => $rowGetPQ['pqoValueSubtext'],
                            'pqoValueSubtextDe' => $rowGetPQ['pqoValueSubtextDe']
                        );
                        $groupedData[$rowGetPQ['pqId']]['optionsData'][$rowGetPQ['pqoId']] = $dataPQ;
                        unset($dataPQ);
                    }
                    unset($rowGetPQ);
                }
                unset($rowsGetPQ);

                //print("<pre>".print_r($groupedData,true)."</pre>");
                
                ?>

                    <!-- 
                        Message Start
                        -->
                            <?php 
                                if(isset($_GET['messPQS'])) {
                                    $enabledMessagesPQS = array(1,2,3,4,5,6);
                                    $messPQS = $_GET['messPQS'];
                                    $resultMessPQS = '';
                                    if(in_array($messPQS, $enabledMessagesPQS)) {
                                        if($messPQS == 1){
                                            $resultMessPQS = '<div class="alert alert-danger text-center" role="alert"><h4><strong>Greška</strong></h4><br>Akcija nije izvršena!<br>Postoji problem sa slanjem podataka za dodavanje novog predefinisanog pitanja! Obratite se administratoru sistema!</div>';
                                        }elseif($messPQS == 2){
                                            $resultMessPQS = '<div class="alert alert-danger text-center" role="alert"><h4><strong>Greška</strong></h4><br>Akcije nije izvršena!<br>Sistem nije izvršio dodavanje novog predefinisanog pitanja! Obratite se administratoru sistema!</div>';
                                        }elseif($messPQS == 3){
                                            $resultMessPQS = '<div class="alert alert-success text-center" role="alert"><h4><strong>Uspješno izvršeno</strong></h4><br>Uspješno ste izvršili dodavnje novog pitanja!</div>';
                                        }elseif($messPQS == 4){
                                            $resultMessPQS = '<div class="alert alert-success text-center" role="alert"><h4><strong>Uspješno izvršeno</strong></h4><br>Uspješno ste izvršili dodavanje novog pitanja i njemu pripadajućih opcija!</div>';
                                        }elseif($messPQS == 5){
                                            $resultMessPQS = '<div class="alert alert-warning text-center" role="alert"><h4><strong>Upozorenje</strong></h4><br>Uspješno ste izvršili dodavanje novog pitanja! Međutim postoji problem sa dodavanje pripadajućih opcija! Obratite se administratoru sistema!</div>';
                                        }elseif($messPQS == 6){
                                            $resultMessPQS = '<div class="alert alert-warning text-center" role="alert"><h4><strong>Napomena</strong></h4><br>Uspješno ste izvršili akciju <strong>Uredi pitanje</strong>! <br> Koristite opciju <strong>Pregled</strong> u tabeli kako bi izvršili provjeru uređenog pitanja!</div>';
                                        }
                                    } else {
                                        $resultMessPQS = '<div class="alert alert-danger text-center" role="alert"><h4><strong>Greška</strong></h4><br>Nepredviđena poruka odgovora! Kontaktirajte administratora sistema!.</div>';
                                    }
                                    
                                    ?>
                                        <div class="row">
                                            <div class="col-xs-offset-2 col-xs-8">
                                                <?php 
                                                    echo $resultMessPQS;
                                                ?>
                                            </div>
                                        </div>
                                    <?php 
                                    unset($enabledMessagesPQS);
                                }
                            ?>
                        <!-- 
                        Message End
                    -->
                    <!-- 
                        Content START 
                        -->
                            <div class="row">
                                <div class="col-xs-12">
                                    <script>
                                        $(document).ready(function(){
                                            var allPQ = $('#allPQ').DataTable({
                                                responsive: true,
                                                "bAutoWidth": false,
                                                "aoColumns": [
                                                    { "width": "4%"},
                                                    { "width": "23%" },
                                                    { "width": "9%" },
                                                    { "width": "13%" },
                                                    { "width": "5%", "bSortable": false },
                                                    { "width": "5%", "bSortable": false },
                                                    { "width": "33%", "bSortable": false },
                                                    { "width": "8%", "bSortable": false }
                                                ],
                                            });
                                            allPQ.on('draw.dt', function () {
                                                $('.selectpicker', '#allPQ', '#predefinedQuestionsSettings').selectpicker('refresh');
                                            });
                                        });
                                    </script>
                                    <table id="allPQ" class="display" cellspacing="0" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th class="text-center" title="Redni broj pitanja">#</th>
                                                <th class="text-center" title="Text pitanja">Pitanje</th>
                                                <th class="text-center" title="Korisnik koji je izvršio dodavanje pitanja">Korisnik</th>
                                                <th class="text-center" title="Kategorija ili grupa pitanja. Ispis je u formatu: (bs, sr, hr) / de.">Kategorija</th>
                                                <th class="text-center" title="Po postavkama pitanje ima text field. Odnosno, moguće je u text formatu napisati odgovor.">Ima tekst</th>
                                                <th class="text-center" title="Po postavkama pitanje ima field za unos ocjene.">Ima ocjenu</th>
                                                <th class="text-center" title="Po postavkama pitanje ima/nema polje sa dropdown opcijama. Ako ima, ispis je u formatu (bs, sr, hr) / de za text i subtext opcije u dropdown polju.">Ima dropdown</th>
                                                <th class="text-center">Akcija</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                foreach ($groupedData AS $questionData) {
                                                    ?>
                                                        <tr>
                                                            <td class="text-center">
                                                                <?php echo $questionData['pqId']; ?>
                                                            </td>
                                                            <td>
                                                                <?php echo $questionData['pqQuestion']; ?>
                                                            </td>
                                                            <td class="text-center">
                                                                <?php echo (($questionData['pqUser'] != NULL) ? $rowsUsersCRM[$questionData['pqUser']]['userFullName'] : '<span class="label label-default">Nepoznato</span>'); ?>
                                                            </td>
                                                            <td class="text-center">
                                                                <?php echo (($questionData['pqcId'] !== NULL) ? $questionData['pqcName'].' / '.$questionData['pqcNameDe'] : '<span class="label label-default">Nema kategoriju</span>'); ?>
                                                            </td>
                                                            <td class="text-center">
                                                                <?php echo (($questionData['pqHasText'] == 1) ? '<span class="label label-success">DA</span>' : '<span class="label label-warning">NE</span>'); ?>
                                                            </td>
                                                            <td class="text-center">
                                                                <?php echo (($questionData['hasRating'] == 1) ? '<span class="label label-success">DA</span>' : '<span class="label label-warning">NE</span>'); ?>
                                                            </td>
                                                            <td class="text-center">
                                                                <?php
                                                                    if ($questionData['hasDropdown'] == 1 AND count($questionData['optionsData']) > 0) {
                                                                        $optionsData = $questionData['optionsData']; 
                                                                        $optionsDataExp = array();
                                                                        $optionsDataImp = '';
                                                                        foreach ($optionsData AS $optionData) {
                                                                            array_push($optionsDataExp, '<option value="'.$optionData['pqoValue'].'" '.(($optionData['pqoValueSubtext'] !== NULL) ? 'data-subtext=" '.$optionData['pqoValueSubtext'].' / '.$optionData['pqoValueSubtextDe'].' " ' : '').'>'.$optionData['pqoValueText'].' / '.$optionData['pqoValueTextDe'].'</option>');
                                                                            unset($optionData);
                                                                        }
                                                                        unset($optionsData);

                                                                        $optionsDataImp = '
                                                                            <div class="row">
                                                                                <div class="col-xs-12"> 
                                                                                    <select class="selectpicker" title="Kliknite za prikaz opcija">
                                                                                        '.implode('', $optionsDataExp).'
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                        ';
                                                                        unset($optionsDataExp);

                                                                        echo $optionsDataImp;
                                                                        unset($optionsDataImp);
                                                                    } else {
                                                                        echo '<span class="label label-warning">NE</span>';
                                                                    }
                                                                ?>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="btn-group material-btn-group">
                                                                    <button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i><span class="caret material-btn__caret"></span></button>
                                                                    <ul style = "top:33px; left:-60px; min-width:185px !important;" class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
                                                                        <li>
                                                                            <a 
                                                                                class="material-dropdown-menu__link" 
                                                                                onclick="viewPQ(this)"
                                                                                data-question_data='<?php echo json_encode($questionData); ?>'
                                                                            >
                                                                                <i class="fa fa-eye me-4" aria-hidden="true"></i> 
                                                                                Pregled
                                                                            </a>
                                                                        </li>
                                                                        <li>
                                                                            <a 
                                                                                class="material-dropdown-menu__link" 
                                                                                onclick="editPQ(this)"
                                                                                data-question_data='<?php echo json_encode($questionData); ?>'
                                                                            >
                                                                                <i class="fa fa-pencil-square-o me-4" aria-hidden="true"></i> 
                                                                                Uredi pitanje
                                                                            </a>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php 
                                                    unset($questionData);
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <!-- 
                        Content END 
                    -->
                    <!-- 
                        Modals START 
                        -->
                            <div class="modal material-modal material-modal_primary fade text-left" id="viewPQ">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content material-modal__content">
                                        <div class="modal-header material-modal__header">
                                            <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title material-modal__title">
                                                <i style = "margin-right: 10px;" class="fa fa-eye" aria-hidden="true"></i>
                                                Pregled pitanja
                                            </h4>
                                        </div> 
                                        <div class="modal-body material-modal__body">
                                            
                                        </div>
                                        <div class="modal-footer material-modal__footer">
                                            <button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="modal material-modal material-modal_primary fade text-left" id="editPQ">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content material-modal__content">
                                        <div class="modal-header material-modal__header">
                                            <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title material-modal__title"><i class="fa fa-pencil-square me-4" aria-hidden="true"></i><span id="modal-title-text"></span></h4>
                                        </div> 
                                        <div class="modal-body material-modal__body">
                                            <form action="<?php getSiteURL(); ?>jobsoft_settings/backend/do_settings.php?page=edit_predefined_question" enctype="multipart/form-data" method="post" role="form" class="form-horizontal" id="formEditPQ">
                                                <input type="hidden" id="orderEPQ" name="orderEPQ" value="<?php echo $orderId; ?>">
                                                <input type="hidden" id="questionIdEPQ" name="questionIdEPQ">
                                                <input type="hidden" id="questionOldDataEPQ" name="questionOldDataEPQ">
                                                <!-- 
                                                    Short form for EPQ - Edit Predefined Questions
                                                -->
                                                <div class="row">
                                                    <div class="col-md-offset-2 col-sm-8">

                                                        <div class="message_EPQ form-group hidden">    
                                                            
                                                        </div>

                                                        <!-- 
                                                            Text pitanja START
                                                            -->
                                                                <div class="form-group">
                                                                    <div class="col-sm-12">
                                                                        <label for="questionTextEPQ" class="col-sm-4 control-label">
                                                                            <span class="text-danger">
                                                                                *
                                                                            </span>
                                                                            Text:
                                                                        </label>
                                                                        <div class="col-sm-8">
                                                                            <div class="materail-input-block materail-input-block_primary materail-input_slide-line">
                                                                                <textarea class="form-control materail-input material-textarea" name="questionTextEPQ" id="questionTextEPQ" placeholder="Unesite text pitanja" rows="4" required></textarea>
                                                                                <span class="materail-input-block__line"></span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <!-- 
                                                            Text pitanja END
                                                        -->

                                                        <!-- 
                                                            Kategorija START
                                                            -->
                                                                <div class="form-group">
                                                                    <div class="col-sm-12">
                                                                        <label for="categoryEPQ" class="col-sm-4 control-label">
                                                                            <span class="text-danger">
                                                                                *
                                                                            </span>
                                                                            Kategorije:
                                                                        </label>
                                                                        <div class="col-sm-8">
                                                                            <select class="selectpicker" id="categoryEPQ" name="categoryEPQ" data-live-search="true" title = "Odaberite kategoriju" required>

                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <!-- 
                                                            Kategorija END
                                                        -->

                                                        <!-- 
                                                            Nova kategorija START
                                                            -->
                                                                <div class="form-group hidden">
                                                                    <div class="col-sm-12">
                                                                        <label for="newCategoryEPQ" class="col-sm-4 control-label">
                                                                            <span class="text-danger">
                                                                                *
                                                                            </span>
                                                                            Nova kategorija:
                                                                        </label>
                                                                        <div class="col-sm-8">
                                                                            <div class="materail-input-block materail-input-block_primary materail-input_slide-line">
                                                                                <input class="form-control materail-input" type="text" name="newCategoryEPQ" id="newCategoryEPQ" placeholder="Unesite naziv kategorije">
                                                                                <span class="materail-input-block__line"></span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="form-group hidden">
                                                                    <div class="col-sm-12">
                                                                        <label for="newDeCategoryEPQ" class="col-sm-4 control-label">
                                                                            <span class="text-danger">
                                                                                *
                                                                            </span>
                                                                            Nova kategorija (DE):
                                                                        </label>
                                                                        <div class="col-sm-8">
                                                                            <div class="materail-input-block materail-input-block_primary materail-input_slide-line">
                                                                                <input class="form-control materail-input" type="text" name="newDeCategoryEPQ" id="newDeCategoryEPQ" placeholder="Unesite naziv kategorije (DE)">
                                                                                <span class="materail-input-block__line"></span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <!-- 
                                                            Nova kategorija END
                                                        -->

                                                        <!-- 
                                                            Opcije START
                                                            -->
                                                                <div class="form-group">
                                                                    <div class="col-sm-12">
                                                                        <label for="hasTextEPQ" class="col-sm-4 control-label">
                                                                            Ima text:
                                                                        </label>
                                                                        <div class="col-sm-8">
                                                                            <div class="main-container__column materail-switch materail-switch_primary">
                                                                                <input class="materail-switch__element" type="checkbox" id="hasTextEPQ" name="hasTextEPQ" value="1">
                                                                                <label class="materail-switch__label" for="hasTextEPQ"></label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <div class="col-sm-12">
                                                                        <label for="hasRatingEPQ" class="col-sm-4 control-label">
                                                                            Ima ocjenu:
                                                                        </label>
                                                                        <div class="col-sm-8">
                                                                            <div class="main-container__column materail-switch materail-switch_primary">
                                                                                <input class="materail-switch__element" type="checkbox" id="hasRatingEPQ" name="hasRatingEPQ" value="1">
                                                                                <label class="materail-switch__label" for="hasRatingEPQ"></label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <div class="col-sm-12">
                                                                        <label for="hasDropdownEPQ" class="col-sm-4 control-label">
                                                                            Ima dropdown:
                                                                        </label>
                                                                        <div class="col-sm-8">
                                                                            <div class="main-container__column materail-switch materail-switch_primary">
                                                                                <input class="materail-switch__element" type="checkbox" id="hasDropdownEPQ" name="hasDropdownEPQ" value="1">
                                                                                <label class="materail-switch__label" for="hasDropdownEPQ"></label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <!-- 
                                                            Opcije END
                                                        -->
                                                        <!-- 
                                                            Opcije dropdown START
                                                            -->
                                                                <div id="contentOptionsEPQ" class="hidden">
                                                                    
                                                                </div>
                                                            <!-- 
                                                            Opcije dropdown END
                                                        -->
                                                        <!-- 
                                                            Dodaj opciju START
                                                            -->
                                                                <div class="form-group mt-3 hidden">
                                                                    <div class="col-sm-12">
                                                                        <label for="plusNumberOptionsEPQ" class="col-sm-10 control-label text-right">
                                                                            Dodaj novo polje opcije:
                                                                        </label>
                                                                        <div class="col-sm-2 text-center">
                                                                            <span id="plusNumberOptionsEPQ" class="btn material-btn btn-success"><i class="fa fa-plus" aria-hidden="true"></i></span>
                                                                        </div>
                                                                        <div class="hidden">
                                                                            <input type="hidden" id="numbersOptionsEPQ" name="numbersOptionsEPQ" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <!-- 
                                                            Dodaj opciju END
                                                        -->
                                                    </div>
                                                </div>
                                                <div class="form-group idk_margin_top20">
                                                    <div class="col-md-offset-2 col-sm-8 text-center">
                                                        <small>
                                                            <strong>
                                                                Sva polja označena sa 
                                                                <span class="text-danger">
                                                                    *
                                                                </span>  
                                                                su obavezna!
                                                            </strong>
                                                        </small>
                                                    </div>
                                                </div>
                                                <div class="modal-footer material-modal__footer">
                                                    <button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
                                                    <button type="submit" id="submitEditPQ" class="btn btn-primary material-btn material-btn_primary" form="formEditPQ"><i class="fa fa-check-square-o" aria-hidden="true"></i> Spremi</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <!-- 
                        Modals END 
                    -->
                    <!-- 
                        Script START 
                        -->
                            <script>
                                /*
                                    View Predefined Question START
                                    */
                                        function messageViewPQ(type, title, message) {
                                            $('.modal-body', '#viewPQ', '#predefinedQuestionsSettings').html('<div class="alert alert-'+type+' text-center" role="alert"><i class="fa fa-info-circle fa-3x" aria-hidden="true"></i><br><h4><strong>'+title+'</strong></h4><br><br>'+message+'</div>');
                                            $('#viewPQ', '#predefinedQuestionsSettings').modal('show');
                                        };

                                        function errorInfoViewPQ(xhr, ajaxOptions, thrownError) {
                                            let errorMessage = ((xhr.status == 400 || xhr.status == 500) ? xhr.responseText : thrownError);
                                            messageViewPQ('danger', 'Greška', errorMessage);
                                        };

                                        function viewPQ(thisRow) { 
                                            let question_data = $(thisRow).data('question_data');
                                            if (question_data) {
                                                $.ajax({
                                                    url: 'jobsoft_settings/ajax/ajax_data.php?page=viewPredefinedQuestion',
                                                    type: 'POST',
                                                    dataType: 'html',
                                                    data:{
                                                        'question_data': question_data,
                                                    },
                                                    success : function (response){
                                                        setViewPQ(response);
                                                    },
                                                    error: function (xhr, ajaxOptions, thrownError) {
                                                        errorInfoViewPQ(xhr, ajaxOptions, thrownError);
                                                    }
                                                });
                                            } else {
                                                messageViewPQ('danger', 'Greška', 'Postoji problem sa pregledom pitanja! Kontaktirajte administratora sistem!'); 
                                            }
                                        };

                                        function setViewPQ(data) {
                                            $('.modal-body', '#viewPQ', '#predefinedQuestionsSettings').html(data);
                                            $('#viewPQ', '#predefinedQuestionsSettings').modal('show');
                                            $('.selectpicker', '.modal-body', '#viewPQ', '#predefinedQuestionsSettings').selectpicker('refresh');
                                        };

                                        function resetViewPQ() {
                                            $('.modal-body', '#viewPQ', '#predefinedQuestionsSettings').html('');
                                        };

                                        $('#viewPQ', '#predefinedQuestionsSettings').on('hidden.bs.modal', resetViewPQ);
                                    /*
                                    View Predefined Question END
                                */
                                /*
                                    Edit Predefined Question START
                                    */ 
                                        function getQuestionCategoryEPQ(pqcId) {
                                            $.ajax({
                                                url: 'jobsoft_settings/backend/do_settings.php?page=get_question_categories',
                                                type: 'POST',
                                                processData: false,
                                                contentType: false,
                                                cache: false,
                                                dataType: 'JSON',
                                                success : function (response){
                                                    let categoryEPQ = $('#categoryEPQ', '#editPQ', '#predefinedQuestionsSettings');
                                                    let newCategoryEPQ = $('#newCategoryEPQ', '#editPQ', '#predefinedQuestionsSettings');
                                                    let newDeCategoryEPQ = $('#newDeCategoryEPQ', '#editPQ', '#predefinedQuestionsSettings');
                                                    categoryEPQ.append($('<option>', {
                                                        value: 'without',
                                                        text: 'Bez kategorije',
                                                        style: 'background: #f0ad4e; color: #fff;' 
                                                    }));
                                                    categoryEPQ.append($('<option>', {
                                                        value: 'new',
                                                        text: 'Dodaj novu kategoriju',
                                                        style: 'background: #5cb85c; color: #fff;'
                                                    }));
                                                    response.forEach(function(item) {
                                                        categoryEPQ.append($('<option>', {
                                                            value: item.pqc_id,
                                                            text: item.pqc_name + ' / ' + item.pqc_name_de
                                                        }));
                                                    }); 
                                                    if (pqcId == null) {
                                                        categoryEPQ.val('without').selectpicker('refresh');
                                                    } else {
                                                        categoryEPQ.val(pqcId).selectpicker('refresh');
                                                    }
                                                    newCategoryEPQ.val(null).prop('required', false).closest('.form-group').addClass('hidden'); 
                                                    newDeCategoryEPQ.val(null).prop('required', false).closest('.form-group').addClass('hidden'); 
                                                },
                                                error: function (xhr, ajaxOptions, thrownError) {
                                                    if (xhr.status === 204) {
                                                        messageEPQ('warning', xhr.responseText); 
                                                    } else {
                                                        messageEPQ('warning', thrownError);
                                                    }
                                                }
                                            });
                                        };
                                        function resetQuestionCategoryEPQ() {
                                            if ($('#categoryEPQ', '#editPQ', '#predefinedQuestionsSettings').children().length > 0) {
                                                $('#categoryEPQ', '#editPQ', '#predefinedQuestionsSettings').empty();
                                            }
                                            $('#categoryEPQ', '#editPQ', '#predefinedQuestionsSettings').val(null).selectpicker('refresh');
                                            $('#newCategoryEPQ', '#editPQ', '#predefinedQuestionsSettings').val(null).prop('required', false).closest('.form-group').addClass('hidden');
                                            $('#newDeCategoryEPQ', '#editPQ', '#predefinedQuestionsSettings').val(null).prop('required', false).closest('.form-group').addClass('hidden');
                                        };
                                        function showHideNewCategoryEPQ() {
                                            let categoryEPQ = $('#categoryEPQ', '#editPQ', '#predefinedQuestionsSettings').val();
                                            if (categoryEPQ == 'new') {
                                                $('#newCategoryEPQ', '#editPQ', '#predefinedQuestionsSettings').val(null).prop('required', true).closest('.form-group').removeClass('hidden');
                                                $('#newDeCategoryEPQ', '#editPQ', '#predefinedQuestionsSettings').val(null).prop('required', true).closest('.form-group').removeClass('hidden');
                                            } else {
                                                $('#newCategoryEPQ', '#editPQ', '#predefinedQuestionsSettings').val(null).prop('required', false).closest('.form-group').addClass('hidden');
                                                $('#newDeCategoryEPQ', '#editPQ', '#predefinedQuestionsSettings').val(null).prop('required', false).closest('.form-group').addClass('hidden');
                                            }
                                        };
                                        function setModalTitleEPQ(pqQuestion) {
                                            if (pqQuestion != '') {
                                                $('#modal-title-text', '#editPQ', '#predefinedQuestionsSettings').html('Uredi pitanje <strong>' + pqQuestion.substring(0, 70) + '...</strong>');
                                            } else {
                                                $('#modal-title-text', '#editPQ', '#predefinedQuestionsSettings').html('');
                                            }
                                        };
                                        function messageEPQ(type, message) {
                                            $('.message_EPQ','#editPQ', '#predefinedQuestionsSettings').html(`
                                                <div class="col-sm-12">
                                                    <div class="alert alert-` + type + ` text-center" role="alert">
                                                        ` + message + `
                                                    </div>
                                                </div>
                                            `).removeClass('hidden');
                                            
                                            setTimeout(function(){
                                                $('.message_EPQ','#editPQ', '#predefinedQuestionsSettings').html('').addClass('hidden');
                                            }, 10000);
                                        };
                                        function editPQ(thisRow) {
                                            let question_data = $(thisRow).data('question_data');
                                            setInitialValueEPQ(question_data);
                                            setModalTitleEPQ(question_data['pqQuestion']);
                                            questionHasUsed(question_data['pqId'], function(result) {
                                                if (result == 0) {
                                                    $('#submitEditPQ', '#editPQ', '#predefinedQuestionsSettings').prop('disabled', true);
                                                } else {
                                                    $('#submitEditPQ', '#editPQ', '#predefinedQuestionsSettings').prop('disabled', false);
                                                }
                                            });
                                            $('#editPQ', '#predefinedQuestionsSettings').modal('show');
                                        }; 
                                        function setInitialValueEPQ(question_data) {
                                            if (question_data != '') { 
                                                $('#questionIdEPQ','#editPQ', '#predefinedQuestionsSettings').val(question_data['pqId']);
                                                $('#questionOldDataEPQ','#editPQ', '#predefinedQuestionsSettings').val(JSON.stringify(question_data));
                                                $('#questionTextEPQ','#editPQ', '#predefinedQuestionsSettings').val(question_data['pqQuestion']);
                                                getQuestionCategoryEPQ(question_data['pqcId']);
                                                $('#hasTextEPQ', '#editPQ', '#predefinedQuestionsSettings').prop('checked', ((question_data['pqHasText'] == 1) ? true : false));
                                                $('#hasRatingEPQ', '#editPQ', '#predefinedQuestionsSettings').prop('checked', ((question_data['hasRating'] == 1) ? true : false));
                                                setInitialOptionFieldsEPQ(question_data);
                                            } else {
                                                $('#questionIdEPQ','#editPQ', '#predefinedQuestionsSettings').val(null);
                                                $('#questionOldDataEPQ','#editPQ', '#predefinedQuestionsSettings').val(null);
                                                $('#questionTextEPQ','#editPQ', '#predefinedQuestionsSettings').val(null);
                                                resetQuestionCategoryEPQ();
                                                $('#hasTextEPQ', '#editPQ', '#predefinedQuestionsSettings').prop('checked', false);
                                                $('#hasRatingEPQ', '#editPQ', '#predefinedQuestionsSettings').prop('checked', false);
                                                setInitialOptionFieldsEPQ('');
                                            }
                                        };
                                        function initialOptionFieldCreatedEPQ(genereteId, options_data) {
                                            return `
                                                <div class="fieldsOptionEPQ panel panel-default" data-option="`+genereteId+`">
                                                    <div class="panel-heading">
                                                        <div class="row">
                                                            <div class="col-xs-10 text-left">
                                                                <strong>Opcija `+genereteId+`</strong>
                                                            </div>
                                                            <div class="col-xs-2 text-right">
                                                                <span class="btn material-btn btn-danger removeNumberOptionsEPQ"><i class="fa fa-trash" aria-hidden="true"></i></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="panel-body">
                                                        <input type="hidden" name="optionsId`+genereteId+`EPQ" id="optionsId`+genereteId+`EPQ" value="`+options_data['pqoId']+`"/>
                                                        <div class="form-group">
                                                            <div class="col-sm-12">
                                                                <label for="textOptions`+genereteId+`EPQ" class="col-sm-4 control-label">
                                                                    <span class="text-danger">
                                                                        *
                                                                    </span>
                                                                    Text opcije (bs, sr, hr):
                                                                </label>
                                                                <div class="col-sm-8">
                                                                    <div class="materail-input-block materail-input-block_primary">
                                                                        <input class="form-control materail-input" type="text" name="textOptions`+genereteId+`EPQ" id="textOptions`+genereteId+`EPQ" placeholder="Unesite text (bs,sr,hr)" value="`+options_data['pqoValueText']+`" required>
                                                                        <span class="materail-input-block__line"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="col-sm-12">
                                                                <label for="textdeOptions`+genereteId+`EPQ" class="col-sm-4 control-label">
                                                                    <span class="text-danger">
                                                                        *
                                                                    </span>
                                                                    Text opcije (de):
                                                                </label>
                                                                <div class="col-sm-8">
                                                                    <div class="materail-input-block materail-input-block_primary">
                                                                        <input class="form-control materail-input" type="text" name="textdeOptions`+genereteId+`EPQ" id="textdeOptions`+genereteId+`EPQ" placeholder="Unesite text (de)" value="`+options_data['pqoValueTextDe']+`" required>
                                                                        <span class="materail-input-block__line"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="col-sm-12">
                                                                <label for="subtextOptions`+genereteId+`EPQ" class="col-sm-4 control-label">
                                                                    Subtext opcije (bs, sr, hr):
                                                                </label>
                                                                <div class="col-sm-8">
                                                                    <div class="materail-input-block materail-input-block_primary">
                                                                        <input class="form-control materail-input" type="text" name="subtextOptions`+genereteId+`EPQ" id="subtextOptions`+genereteId+`EPQ" placeholder="Unesite subtext (bs,sr,hr)" value="`+((options_data['pqoValueSubtext'] != null) ? options_data['pqoValueSubtext'] : ``)+`">
                                                                        <span class="materail-input-block__line"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="col-sm-12">
                                                                <label for="subtextdeOptions`+genereteId+`EPQ" class="col-sm-4 control-label">
                                                                    Subtext opcije (de):
                                                                </label>
                                                                <div class="col-sm-8">
                                                                    <div class="materail-input-block materail-input-block_primary">
                                                                        <input class="form-control materail-input" type="text" name="subtextdeOptions`+genereteId+`EPQ" id="subtextdeOptions`+genereteId+`EPQ" placeholder="Unesite subtext (de)" value="`+((options_data['pqoValueSubtextDe'] != null) ? options_data['pqoValueSubtextDe'] : ``)+`">
                                                                        <span class="materail-input-block__line"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            `;
                                        };
                                        function setInitialOptionFieldsEPQ(question_data) {
                                            if (question_data != '') {
                                                if (question_data['hasDropdown'] == 1) {
                                                    let hasDropdownEPQ = $('#hasDropdownEPQ', '#editPQ', '#predefinedQuestionsSettings');
                                                    hasDropdownEPQ.prop('checked', true);
                                                    let numbersOptionsEPQ = $('#numbersOptionsEPQ', '#editPQ', '#predefinedQuestionsSettings');
                                                    numbersOptionsEPQ.val(null).closest('.form-group').removeClass('hidden');
                                                    let contentOptionsEPQ = $('#contentOptionsEPQ', '#editPQ', '#predefinedQuestionsSettings');
			                                        contentOptionsEPQ.empty().removeClass('hidden');

                                                    let currentValue;
                                                    let nextValue;
                                                    let nextHtml;

                                                    Object.values(question_data['optionsData']).forEach(function(option) {
                                                        console.log(option); 
                                                        let numbersOptionsEPQ = $('#numbersOptionsEPQ', '#editPQ', '#predefinedQuestionsSettings');
                                                        nextValue = parseInt(option.pqoValue); 
                                                        if (numbersOptionsEPQ.val() === '' || numbersOptionsEPQ.val() === '[]') {
                                                            currentValue = [];
                                                        } else {
                                                            currentValue = JSON.parse(numbersOptionsEPQ.val());
                                                        }
                                                        currentValue.push(nextValue);
                                                        numbersOptionsEPQ.val(JSON.stringify(currentValue));
                                                        contentOptionsEPQ.append(initialOptionFieldCreatedEPQ(nextValue, option));
                                                    }); 

                                                } else {
                                                    $('#numbersOptionsEPQ', '#editPQ', '#predefinedQuestionsSettings').val(null).closest('.form-group').addClass('hidden');
                                                    $('#contentOptionsEPQ', '#editPQ', '#predefinedQuestionsSettings').empty().addClass('hidden');
                                                    $('#hasDropdownEPQ', '#editPQ', '#predefinedQuestionsSettings').prop('checked', false);
                                                }
                                            } else {
                                                $('#numbersOptionsEPQ', '#editPQ', '#predefinedQuestionsSettings').val(null).closest('.form-group').addClass('hidden');
			                                    $('#contentOptionsEPQ', '#editPQ', '#predefinedQuestionsSettings').empty().addClass('hidden');
			                                    $('#hasDropdownEPQ', '#editPQ', '#predefinedQuestionsSettings').prop('checked', false);
                                            }
                                        };
                                        function makeNewOptionFieldEPQ(genereteId) {
                                            return `
                                                <div class="fieldsOptionEPQ panel panel-default" data-option="`+genereteId+`">
                                                    <div class="panel-heading">
                                                        <div class="row">
                                                            <div class="col-xs-10 text-left">
                                                                <strong>Opcija `+genereteId+`</strong>
                                                            </div>
                                                            <div class="col-xs-2 text-right">
                                                                <span class="btn material-btn btn-danger removeNumberOptionsEPQ"><i class="fa fa-trash" aria-hidden="true"></i></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="panel-body">
                                                        <input type="hidden" name="optionsId`+genereteId+`EPQ" id="optionsId`+genereteId+`EPQ" value="0"/>
                                                        <div class="form-group">
                                                            <div class="col-sm-12">
                                                                <label for="textOptions`+genereteId+`EPQ" class="col-sm-4 control-label">
                                                                    <span class="text-danger">
                                                                        *
                                                                    </span>
                                                                    Text opcije (bs, sr, hr):
                                                                </label>
                                                                <div class="col-sm-8">
                                                                    <div class="materail-input-block materail-input-block_primary">
                                                                        <input class="form-control materail-input" type="text" name="textOptions`+genereteId+`EPQ" id="textOptions`+genereteId+`EPQ" placeholder="Unesite text (bs,sr,hr)" required>
                                                                        <span class="materail-input-block__line"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="col-sm-12">
                                                                <label for="textdeOptions`+genereteId+`EPQ" class="col-sm-4 control-label">
                                                                    <span class="text-danger">
                                                                        *
                                                                    </span>
                                                                    Text opcije (de):
                                                                </label>
                                                                <div class="col-sm-8">
                                                                    <div class="materail-input-block materail-input-block_primary">
                                                                        <input class="form-control materail-input" type="text" name="textdeOptions`+genereteId+`EPQ" id="textdeOptions`+genereteId+`EPQ" placeholder="Unesite text (de)" required>
                                                                        <span class="materail-input-block__line"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="col-sm-12">
                                                                <label for="subtextOptions`+genereteId+`EPQ" class="col-sm-4 control-label">
                                                                    Subtext opcije (bs, sr, hr):
                                                                </label>
                                                                <div class="col-sm-8">
                                                                    <div class="materail-input-block materail-input-block_primary">
                                                                        <input class="form-control materail-input" type="text" name="subtextOptions`+genereteId+`EPQ" id="subtextOptions`+genereteId+`EPQ" placeholder="Unesite subtext (bs,sr,hr)">
                                                                        <span class="materail-input-block__line"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="col-sm-12">
                                                                <label for="subtextdeOptions`+genereteId+`EPQ" class="col-sm-4 control-label">
                                                                    Subtext opcije (de):
                                                                </label>
                                                                <div class="col-sm-8">
                                                                    <div class="materail-input-block materail-input-block_primary">
                                                                        <input class="form-control materail-input" type="text" name="subtextdeOptions`+genereteId+`EPQ" id="subtextdeOptions`+genereteId+`EPQ" placeholder="Unesite subtext (de)">
                                                                        <span class="materail-input-block__line"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            `;
                                        }; 
                                        function addOptionFieldEPQ() {
                                            let numbersOptionsEPQ = $('#numbersOptionsEPQ', '#editPQ', '#predefinedQuestionsSettings');
                                            let currentValue;
                                            let nextValue;
                                            let nextHtml;  
                                            if (numbersOptionsEPQ.val() === '' || numbersOptionsEPQ.val() === '[]') {
                                                currentValue = [];
                                                nextValue = 1; 
                                            } else {
                                                currentValue = JSON.parse(numbersOptionsEPQ.val());
                                                nextValue = currentValue[currentValue.length - 1] + 1;
                                            }
                                            currentValue.push(nextValue);
                                            numbersOptionsEPQ.val(JSON.stringify(currentValue));
                                            nextHtml = makeNewOptionFieldEPQ(nextValue); 
                                            $('#contentOptionsEPQ', '#editPQ', '#predefinedQuestionsSettings').append(nextHtml);
                                        };
                                        function deleteOptionFieldEPQ() {
                                            console.log();
                                            var optionHtml = $(this).closest('.fieldsOptionEPQ', '#contentOptionsEPQ', '#editPQ', '#predefinedQuestionsSettings');
                                            var optionId = optionHtml.attr('data-option');
                                            optionHtml.remove();
                                            console.log(optionId);
                                            let numbersOptionsEPQ = $('#numbersOptionsEPQ', '#editPQ', '#predefinedQuestionsSettings');
                                            let currentValue = JSON.parse(numbersOptionsEPQ.val());
                                            let index = currentValue.indexOf(parseInt(optionId));
                                            if (index !== -1) {
                                                currentValue.splice(index, 1);
                                            }
                                            numbersOptionsEPQ.val(JSON.stringify(currentValue));
                                            if (currentValue.length === 0) {
                                                $('#numbersOptionsEPQ', '#editPQ', '#predefinedQuestionsSettings').val(null).closest('.form-group').addClass('hidden');
                                                $('#contentOptionsEPQ', '#editPQ', '#predefinedQuestionsSettings').empty().addClass('hidden');
                                                $('#hasDropdownEPQ', '#editPQ', '#predefinedQuestionsSettings').prop('checked', false);
                                            }
                                        };
                                        function showHideOptionsFieldsEPQ() {
                                            let hasDropdownEPQ = $('#hasDropdownEPQ', '#editPQ', '#predefinedQuestionsSettings');
                                            if (hasDropdownEPQ.is(':checked')) {
                                                $('#numbersOptionsEPQ', '#editPQ', '#predefinedQuestionsSettings').val(null).closest('.form-group').removeClass('hidden');
                                                $('#contentOptionsEPQ', '#editPQ', '#predefinedQuestionsSettings').empty().removeClass('hidden');
                                                addOptionFieldEPQ();
                                            } else {
                                                $('#numbersOptionsEPQ', '#editPQ', '#predefinedQuestionsSettings').val(null).closest('.form-group').addClass('hidden');
                                                $('#contentOptionsEPQ', '#editPQ', '#predefinedQuestionsSettings').empty().addClass('hidden');
                                            }
                                        };
                                        function questionHasUsed(pquId, callback) {
                                            $.ajax({
                                                url: 'jobsoft_settings/ajax/ajax_data.php?page=question_has_used',
                                                type: 'POST',
                                                cache: false,
                                                data:{'pqu_id': pquId},
                                                success : function (response){
                                                    var response_decode = JSON.parse(response);
                                                    if (response_decode.status === 1) {
                                                        callback(1);
                                                    } else {
                                                        messageEPQ('warning', response_decode.message);
                                                        callback(0);
                                                    } 
                                                },
                                                error: function (xhr, ajaxOptions, thrownError) {
                                                    if (xhr.status === 400 || xhr.status === 404) {
                                                        messageEPQ('danger', xhr.responseText); 
                                                    } else {
                                                        messageEPQ('danger', thrownError);
                                                    }
                                                    callback(0);
                                                }
                                            });
                                        }; 

                                        $('#editPQ', '#predefinedQuestionsSettings').on('hidden.bs.modal', function(){
                                            setInitialValueEPQ('');
                                            setModalTitleEPQ('');
                                        });
                                        $('#categoryEPQ', '#editPQ', '#predefinedQuestionsSettings').on('change', showHideNewCategoryEPQ);
                                        $('#plusNumberOptionsEPQ', '#editPQ', '#predefinedQuestionsSettings').on('click', addOptionFieldEPQ);
                                        $('#contentOptionsEPQ', '#editPQ', '#predefinedQuestionsSettings').on('click', '.removeNumberOptionsEPQ', deleteOptionFieldEPQ);
                                        $('#hasDropdownEPQ', '#editPQ', '#predefinedQuestionsSettings').on('click', showHideOptionsFieldsEPQ);
                                        $('#formEditPQ', '#predefinedQuestionsSettings').on('submit', function(event) {
                                            event.preventDefault();
                                            let questionIdEPQ = $('#questionIdEPQ', '#editPQ', '#predefinedQuestionsSettings').val();
                                            questionHasUsed(questionIdEPQ, function(result) {
                                                if (result == 0) {
                                                    $('#submitEditPQ', '#editPQ', '#predefinedQuestionsSettings').prop('disabled', true);
                                                } else {
                                                    $('#submitEditPQ', '#editPQ', '#predefinedQuestionsSettings').prop('disabled', false);
                                                    $('#formEditPQ', '#predefinedQuestionsSettings').unbind('submit').submit();
                                                }
                                            });
                                        });
                                    /*
                                    Edit Predefined Question END
                                */
                            </script>
                        <!-- 
                        Script END 
                    -->
                <?php 

                unset($groupedData);
            } else {
                ?>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="alert alert-warning text-center" role="alert">
                                <h4><strong>Upozorenje</strong></h4>
                                <br>
                                Nema dodanih pitanja za ovaj nalog!
                            </div>
                        </div>
                    </div>
                <?php 
            }
            unset($rowsUsersCRM);
        ?>
        <?php 
    }
?>