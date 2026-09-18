<!--
    This file is included in the nalozi.php file

    DESC: 
        Obo -> offers and benefits of order
-->
<?php 
    $orderObo = intval($nalog_id);
    $fieldStyle = '';
    if ($orderObo != 0) {
        $infoObo = getOffersAndBenefitsOfOrderArrayR($orderObo);
        if (count($infoObo) > 0) {
            $fieldStyle = 'success';
        } else {
            $fieldStyle = 'warning';
        }
    } else {
        $infoObo = array();
        $fieldStyle = 'danger'; 
    }
?>
<style>
    #changeObo .fa-info-circle:not(.fa-3x), #changeObo .fa-times-circle:not(.fa-3x) {
        font-size: 22px; 
    }
    #changeObo .alert {
        margin-bottom: 0px;
        margin-top: 20px;
    }
</style>
<div class="row idk_margin_top20">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-sm-9 text-left">
                <h5>Ponude i benefiti od poslodavca</h5>
            </div>
            <div class="col-sm-3 text-right">
                <!-- 
                    Change Modal START
                -->
                    <button 
                        data-toggle="modal" data-target="#changeObo"
                        class="btn material-btn material-btn-icon-<?php echo $fieldStyle; ?> material-btn_<?php echo $fieldStyle; ?> main-container__column" 
                        <?php echo (($orderObo == 0) ? 'disabled' : ''); ?>
                    >
                        <i class="fa fa-pencil" aria-hidden="true"></i>
                        <span>
                            <?php echo (($orderObo != 0) ? ((count($infoObo) > 0) ? 'Uredi' : 'Dodaj') : ''); ?>
                        </span>
                    </button>
                    <div class="modal material-modal material-modal_<?php echo $fieldStyle; ?> fade text-left" id="changeObo">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content material-modal__content">
                                <div class="modal-header material-modal__header">
                                    <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title material-modal__title">
                                        <i style = "margin-right: 10px;" class="fa fa-pencil" aria-hidden="true"></i>
                                        <span>
                                            <?php echo (($orderObo != 0) ? ((count($infoObo) > 0) ? "Uredi <strong>Ponude i benefite poslodavca</strong>" : "Dodaj <strong>Ponude i benefite poslodavca</strong>") : ''); ?>
                                        </span>
                                    </h4>
                                </div> 
                                <div class="modal-body material-modal__body">
                                    <form action="<?php getSiteURL(); ?>do.php?form=offers_and_benefits_of_order" enctype="multipart/form-data" method="post" role="form" class="form-horizontal" id="changeOboForm">
                                        <input type="hidden" id="orderObo" name="orderObo" value="<?php echo $orderObo; ?>">
                                        <div class="row">
                                            <div class="col-md-offset-2 col-sm-8">
                                                <?php 
                                                    if ($orderObo != 0 AND count($infoObo) > 0 ) {
                                                        ?>
                                                            <div class="form-group">
                                                                <div class="col-sm-12 text-center">
                                                                    <small>
                                                                        <span class="text-danger">
                                                                            Zatvaranjem modala - novododane vrijednosti se vraćaju na početne vrijednosti!
                                                                        </span>  
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        <?php 
                                                    }
                                                ?>
                                                <!-- 
                                                    Country - city START
                                                -->
                                                    <div class="panel panel-default">
                                                        <div class="panel-body">
                                                            <div class="form-group">
                                                                <div class="col-sm-12">
                                                                    <label for="countryCityObo" class="col-sm-3 control-label">
                                                                        <span class="text-danger">
                                                                            *
                                                                        </span>
                                                                        Država/grad:
                                                                    </label>
                                                                    <div class="col-sm-8">
                                                                        <div class="materail-input-block materail-input-block_<?php echo $fieldStyle; ?> materail-input_slide-line">
                                                                            <textarea class="form-control materail-input material-textarea" name="countryCityObo" id="countryCityObo" placeholder="Unesite države/gradove" rows="4" required></textarea>
                                                                            <span class="materail-input-block__line"></span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-1 control-label">
                                                                        <i class="openInfoCountryCityObo fa fa-info-circle" aria-hidden="true"></i>
                                                                        <i class="closeInfoCountryCityObo fa fa-times-circle hidden" aria-hidden="true"></i>
                                                                    </div>
                                                                    <div class="messageCountryCityObo col-sm-12 hidden">
                                                                        <div class="alert alert-warning text-center">
                                                                            <i class="fa fa-info-circle fa-3x" aria-hidden="true"></i>
                                                                            <br>
                                                                            <h4><strong>Informacije o formatu unosa</strong></h4>
                                                                            <br>
                                                                            Unos u polje <strong>Država/grad</strong> poželjno je da bude u narednim formatima!
                                                                            <br><br>
                                                                            Ako je nalog kreiran samo za jednu državu, poželjan unos je npr:
                                                                            <br>
                                                                            <i>
                                                                                Njemačka: München, Frankfurt, Stuttgart, Berlin; 
                                                                            </i>
                                                                            <br><br>
                                                                            Ako je nalog kreiran za više država, poželjan unos je npr: 
                                                                            <br>
                                                                            <i>
                                                                                Njemačka: München, Frankfurt, Stuttgart, Berlin; Austrija: Beč, Graz, Salzburg, Bregenz; 
                                                                            </i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <script>
                                                                $('.openInfoCountryCityObo', '#changeOboForm').on('click', function(){
                                                                    $('.openInfoCountryCityObo', '#changeOboForm').addClass('hidden');
                                                                    $('.closeInfoCountryCityObo', '#changeOboForm').removeClass('hidden');
                                                                    $('.messageCountryCityObo', '#changeOboForm').removeClass('hidden');
                                                                });
                                                                
                                                                $('.closeInfoCountryCityObo', '#changeOboForm').on('click', function(){
                                                                    $('.openInfoCountryCityObo', '#changeOboForm').removeClass('hidden');
                                                                    $('.closeInfoCountryCityObo', '#changeOboForm').addClass('hidden');
                                                                    $('.messageCountryCityObo', '#changeOboForm').addClass('hidden');
                                                                });
                                                            </script>
                                                        </div>
                                                    </div>
                                                <!-- 
                                                    Country - city END 
                                                -->

                                                <!-- 
                                                    Position START
                                                -->
                                                    <div class="panel panel-default">
                                                        <div class="panel-body">
                                                            <div class="form-group">
                                                                <div class="col-sm-12">
                                                                    <label for="positionObo" class="col-sm-3 control-label">
                                                                        <span class="text-danger">
                                                                            *
                                                                        </span>
                                                                        Naziv pozicije:
                                                                    </label>
                                                                    <div class="col-sm-9">
                                                                        <div class="materail-input-block materail-input-block_<?php echo $fieldStyle; ?> materail-input_slide-line">
                                                                            <textarea class="form-control materail-input material-textarea" name="positionObo" id="positionObo" placeholder="Unesite naziv pozicije" rows="2" required></textarea>
                                                                            <span class="materail-input-block__line"></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <!-- 
                                                    Position END 
                                                -->

                                                <!-- 
                                                    Position desc START
                                                -->
                                                    <div class="panel panel-default">
                                                        <div class="panel-body">
                                                            <div class="form-group">
                                                                <div class="col-sm-12">
                                                                    <label for="positionDescObo" class="col-sm-3 control-label">
                                                                        <span class="text-danger">
                                                                            *
                                                                        </span>
                                                                        Opis pozicije:
                                                                    </label>
                                                                    <div class="col-sm-9">
                                                                        <div class="materail-input-block materail-input-block_<?php echo $fieldStyle; ?> materail-input_slide-line">
                                                                            <textarea class="form-control materail-input material-textarea" name="positionDescObo" id="positionDescObo" placeholder="Unesite opis pozicije" rows="4" required></textarea>
                                                                            <span class="materail-input-block__line"></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <!-- 
                                                    Position desc END 
                                                -->
                                                    
                                                <!-- 
                                                    Salary START 
                                                -->
                                                    <div class="panel panel-default">
                                                        <div class="panel-body">
                                                            <div class="form-group">
                                                                <div class="col-sm-12">
                                                                    <label for="salaryObo" class="col-sm-3 control-label">
                                                                        <span class="text-danger">
                                                                            *
                                                                        </span>
                                                                        Iznos zarade:
                                                                    </label>
                                                                    <div class="col-sm-8">
                                                                        <div class="materail-input-block materail-input-block_<?php echo $fieldStyle; ?>">
                                                                            <input class="form-control materail-input" type="text" name="salaryObo" id="salaryObo" placeholder="Unesite iznos" required>
                                                                            <span class="materail-input-block__line"></span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-1 control-label">
                                                                        <i class="openInfoSalaryObo fa fa-info-circle" aria-hidden="true"></i>
                                                                        <i class="closeInfoSalaryObo fa fa-times-circle hidden" aria-hidden="true"></i>
                                                                    </div>
                                                                    <div class="messageSalaryObo col-sm-12 hidden">
                                                                        <div class="alert alert-warning text-center">
                                                                            <i class="fa fa-info-circle fa-3x" aria-hidden="true"></i>
                                                                            <br>
                                                                            <h4><strong>Informacije o formatu unosa</strong></h4>
                                                                            <br>
                                                                            Unos u polje <strong>Iznos zarade</strong> poželjno je da bude u narednim formatima!
                                                                            <br><br>
                                                                            Ako je određena specifična vrijednost zarade, poželjan unos je npr:
                                                                            <br>
                                                                            <i>
                                                                                3500,00 € 
                                                                            </i>
                                                                            <br><br>
                                                                            Ako je vrijednost zarade u određenom rasponu, poželjan unos je npr: 
                                                                            <br>
                                                                            <i>
                                                                                od 2700,00 € do 3500,00 €
                                                                            </i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <script>
                                                                $('.openInfoSalaryObo', '#changeOboForm').on('click', function(){
                                                                    $('.openInfoSalaryObo', '#changeOboForm').addClass('hidden');
                                                                    $('.closeInfoSalaryObo', '#changeOboForm').removeClass('hidden');
                                                                    $('.messageSalaryObo', '#changeOboForm').removeClass('hidden');
                                                                });
                                                                
                                                                $('.closeInfoSalaryObo', '#changeOboForm').on('click', function(){
                                                                    $('.openInfoSalaryObo', '#changeOboForm').removeClass('hidden');
                                                                    $('.closeInfoSalaryObo', '#changeOboForm').addClass('hidden');
                                                                    $('.messageSalaryObo', '#changeOboForm').addClass('hidden');
                                                                });
                                                            </script>
                                                            <div class="form-group">    
                                                                <div class="col-sm-12">
                                                                    <label for="salaryPeriodObo" class="col-sm-3 control-label">
                                                                        <span class="text-danger">
                                                                            *
                                                                        </span>
                                                                        Period zarade:
                                                                    </label>
                                                                    <div class="col-sm-9">
                                                                        <div class="">
                                                                            <select class="selectpicker" id="salaryPeriodObo" name="salaryPeriodObo" title = "Odaberite opciju" required>
                                                                                <option value = "mjesečno">Mjesečno</option>
                                                                                <option value = "godišnje">Godišnje</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">    
                                                                <div class="col-sm-12">
                                                                    <label for="salaryTypeObo" class="col-sm-3 control-label">
                                                                        <span class="text-danger">
                                                                            *
                                                                        </span>
                                                                        Tip zarade:
                                                                    </label>
                                                                    <div class="col-sm-9">
                                                                        <div class="">
                                                                            <select class="selectpicker" id="salaryTypeObo" name="salaryTypeObo" title = "Odaberite opciju" required>
                                                                                <option value = "brutto">Brutto</option>
                                                                                <option value = "netto">Netto</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <!-- 
                                                    Salary END 
                                                -->

                                                <!-- 
                                                    Bonus START 
                                                -->
                                                    <div class="panel panel-default">
                                                        <div class="panel-body">
                                                            <script>
                                                                function showHideBonusQuestionFields() {
                                                                    let bonusQuestionObo = $('#bonusQuestionObo', '#changeOboForm').val();
                                                                    if (bonusQuestionObo == "da") {
                                                                        $('#bonusHasAmountQuestionObo', '#changeOboForm').prop('required', true).val(`<?php echo (($orderObo != 0 AND count($infoObo) > 0 ) ? $infoObo['bonus']['bonusHasAmountQuestion'] : null); ?>`).selectpicker('refresh').closest('.form-group').removeClass('hidden');
                                                                        let bonusHasAmountQuestionObo = $('#bonusHasAmountQuestionObo', '#changeOboForm').val();
                                                                        if (bonusHasAmountQuestionObo == "da") {
                                                                            $('#bonusAmountObo', '#changeOboForm').prop('required', true).val(`<?php echo (($orderObo != 0 AND count($infoObo) > 0 ) ? $infoObo['bonus']['bonusAmount'] : null); ?>`).closest('.form-group').removeClass('hidden');
                                                                            $('#bonusPeriodObo', '#changeOboForm').prop('required', true).val(`<?php echo (($orderObo != 0 AND count($infoObo) > 0 ) ? $infoObo['bonus']['bonusPeriod'] : null); ?>`).selectpicker('refresh').closest('.form-group').removeClass('hidden');
                                                                            $('#bonusTypeObo', '#changeOboForm').prop('required', true).val(`<?php echo (($orderObo != 0 AND count($infoObo) > 0 ) ? $infoObo['bonus']['bonusType'] : null); ?>`).selectpicker('refresh').closest('.form-group').removeClass('hidden');
                                                                        } else {
                                                                            $('#bonusAmountObo', '#changeOboForm').prop('required', false).val(null).closest('.form-group').addClass('hidden');
                                                                            $('#bonusPeriodObo', '#changeOboForm').prop('required', false).val(null).selectpicker('refresh').closest('.form-group').addClass('hidden');
                                                                            $('#bonusTypeObo', '#changeOboForm').prop('required', false).val(null).selectpicker('refresh').closest('.form-group').addClass('hidden');
                                                                        }
                                                                    } else {
                                                                        $('#bonusHasAmountQuestionObo', '#changeOboForm').prop('required', false).val(null).selectpicker('refresh').closest('.form-group').addClass('hidden');
                                                                        $('#bonusAmountObo', '#changeOboForm').prop('required', false).val(null).closest('.form-group').addClass('hidden');
                                                                        $('#bonusPeriodObo', '#changeOboForm').prop('required', false).val(null).selectpicker('refresh').closest('.form-group').addClass('hidden');
                                                                        $('#bonusTypeObo', '#changeOboForm').prop('required', false).val(null).selectpicker('refresh').closest('.form-group').addClass('hidden');
                                                                    }
                                                                }
                                                                function showHideBonusHasAmountQuestionFields() {
                                                                    let bonusHasAmountQuestionObo = $('#bonusHasAmountQuestionObo', '#changeOboForm').val();
                                                                    if (bonusHasAmountQuestionObo == "da") {
                                                                        $('#bonusAmountObo', '#changeOboForm').prop('required', true).val(`<?php echo (($orderObo != 0 AND count($infoObo) > 0 ) ? $infoObo['bonus']['bonusAmount'] : null); ?>`).closest('.form-group').removeClass('hidden');
                                                                        $('#bonusPeriodObo', '#changeOboForm').prop('required', true).val(`<?php echo (($orderObo != 0 AND count($infoObo) > 0 ) ? $infoObo['bonus']['bonusPeriod'] : null); ?>`).selectpicker('refresh').closest('.form-group').removeClass('hidden');
                                                                        $('#bonusTypeObo', '#changeOboForm').prop('required', true).val(`<?php echo (($orderObo != 0 AND count($infoObo) > 0 ) ? $infoObo['bonus']['bonusType'] : null); ?>`).selectpicker('refresh').closest('.form-group').removeClass('hidden');
                                                                    } else {
                                                                        $('#bonusAmountObo', '#changeOboForm').prop('required', false).val(null).closest('.form-group').addClass('hidden');
                                                                        $('#bonusPeriodObo', '#changeOboForm').prop('required', false).val(null).selectpicker('refresh').closest('.form-group').addClass('hidden');
                                                                        $('#bonusTypeObo', '#changeOboForm').prop('required', false).val(null).selectpicker('refresh').closest('.form-group').addClass('hidden');
                                                                    }
                                                                }
                                                            </script>
                                                            <div class="form-group">    
                                                                <div class="col-sm-12">
                                                                    <label for="bonusQuestionObo" class="col-sm-3 control-label">
                                                                        <span class="text-danger">
                                                                            *
                                                                        </span>
                                                                        Ima bonus:
                                                                    </label>
                                                                    <div class="col-sm-9">
                                                                        <div class="">
                                                                            <select class="selectpicker" id="bonusQuestionObo" name="bonusQuestionObo" title = "Odaberite opciju" required>
                                                                                <option value = "da">Da</option>
                                                                                <option value = "ne">Ne</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <script>
                                                                $('#bonusQuestionObo', '#changeOboForm').on('change', showHideBonusQuestionFields);
                                                            </script>
                                                            <div class="form-group hidden">    
                                                                <div class="col-sm-12">
                                                                    <label for="bonusHasAmountQuestionObo" class="col-sm-3 control-label">
                                                                        <span class="text-danger">
                                                                            *
                                                                        </span>
                                                                        Bonus ima iznos:
                                                                    </label>
                                                                    <div class="col-sm-9">
                                                                        <div class="">
                                                                            <select class="selectpicker" id="bonusHasAmountQuestionObo" name="bonusHasAmountQuestionObo" title = "Odaberite opciju">
                                                                                <option value = "da">Da</option>
                                                                                <option value = "ne">Ne</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <script>
                                                                $('#bonusHasAmountQuestionObo', '#changeOboForm').on('change', showHideBonusHasAmountQuestionFields);
                                                            </script>
                                                            <div class="form-group hidden">
                                                                <div class="col-sm-12">
                                                                    <label for="bonusAmountObo" class="col-sm-3 control-label">
                                                                        <span class="text-danger">
                                                                            *
                                                                        </span>
                                                                        Iznos bonusa:
                                                                    </label>
                                                                    <div class="col-sm-8">
                                                                        <div class="materail-input-block materail-input-block_<?php echo $fieldStyle; ?>">
                                                                            <input class="form-control materail-input" type="text" name="bonusAmountObo" id="bonusAmountObo" placeholder="Unesite iznos">
                                                                            <span class="materail-input-block__line"></span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-1 control-label">
                                                                        <i class="openInfoBonusAmountObo fa fa-info-circle" aria-hidden="true"></i>
                                                                        <i class="closeInfoBonusAmountObo fa fa-times-circle hidden" aria-hidden="true"></i>
                                                                    </div>
                                                                    <div class="messageBonusAmountObo col-sm-12 hidden">
                                                                        <div class="alert alert-warning text-center">
                                                                            <i class="fa fa-info-circle fa-3x" aria-hidden="true"></i>
                                                                            <br>
                                                                            <h4><strong>Informacije o formatu unosa</strong></h4>
                                                                            <br>
                                                                            Unos u polje <strong>Iznos bonusa</strong> poželjno je da bude u narednim formatima!
                                                                            <br><br>
                                                                            Ako je određena specifična vrijednost bonusa, poželjan unos je npr:
                                                                            <br>
                                                                            <i>
                                                                                350,00 € 
                                                                            </i>
                                                                            <br><br>
                                                                            Ako je vrijednost bonusa u određenom rasponu, poželjan unos je npr: 
                                                                            <br>
                                                                            <i>
                                                                                od 270,00 € do 350,00 €
                                                                            </i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <script>
                                                                $('.openInfoBonusAmountObo', '#changeOboForm').on('click', function(){
                                                                    $('.openInfoBonusAmountObo', '#changeOboForm').addClass('hidden');
                                                                    $('.closeInfoBonusAmountObo', '#changeOboForm').removeClass('hidden');
                                                                    $('.messageBonusAmountObo', '#changeOboForm').removeClass('hidden');
                                                                });
                                                                
                                                                $('.closeInfoBonusAmountObo', '#changeOboForm').on('click', function(){
                                                                    $('.openInfoBonusAmountObo', '#changeOboForm').removeClass('hidden');
                                                                    $('.closeInfoBonusAmountObo', '#changeOboForm').addClass('hidden');
                                                                    $('.messageBonusAmountObo', '#changeOboForm').addClass('hidden');
                                                                });
                                                            </script>
                                                            <div class="form-group hidden">    
                                                                <div class="col-sm-12">
                                                                    <label for="bonusPeriodObo" class="col-sm-3 control-label">
                                                                        <span class="text-danger">
                                                                            *
                                                                        </span>
                                                                        Period:
                                                                    </label>
                                                                    <div class="col-sm-9">
                                                                        <div class="">
                                                                            <select class="selectpicker" id="bonusPeriodObo" name="bonusPeriodObo" title = "Odaberite opciju">
                                                                                <option value = "mjesečno">Mjesečno</option>
                                                                                <option value = "godišnje">Godišnje</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group hidden">    
                                                                <div class="col-sm-12">
                                                                    <label for="bonusTypeObo" class="col-sm-3 control-label">
                                                                        <span class="text-danger">
                                                                            *
                                                                        </span>
                                                                        Tip:
                                                                    </label>
                                                                    <div class="col-sm-9">
                                                                        <div class="">
                                                                            <select class="selectpicker" id="bonusTypeObo" name="bonusTypeObo" title = "Odaberite opciju">
                                                                                <option value = "brutto">Brutto</option>
                                                                                <option value = "netto">Netto</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <!-- 
                                                    Bonus END 
                                                -->

                                                <!-- 
                                                    Apartment START 
                                                -->
                                                    <div class="panel panel-default">
                                                        <div class="panel-body">
                                                            <div class="form-group">
                                                                <div class="col-sm-12">
                                                                    <label for="apartmentObo" class="col-sm-3 control-label">
                                                                        <span class="text-danger">
                                                                            *
                                                                        </span>
                                                                        Smještaj:
                                                                    </label>
                                                                    <div class="col-sm-9">
                                                                        <div class="">
                                                                            <select class="selectpicker" id="apartmentObo" name="apartmentObo" title = "Odaberite opciju" required>
                                                                                <option value = "ne">Ne</option>
                                                                                <option value = "obezbjeđen_i_plaćen">Obezbjeđen i plaćen</option>
                                                                                <option value = "obezbjeđen_i_odbija_se_od_plate">Obezbjeđen i odbija se od plate</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <!-- 
                                                    Apartment START 
                                                -->

                                                <!-- 
                                                    Hot meal START
                                                -->
                                                    <div class="panel panel-default">
                                                        <div class="panel-body">
                                                            <script>
                                                                function showHideHotMealQuestionFields() {
                                                                    let hotMealQuestionObo = $('#hotMealQuestionObo', '#changeOboForm').val();
                                                                    if (hotMealQuestionObo == "da") {
                                                                        $('#hotMealHasAmountQuestionObo', '#changeOboForm').prop('required', true).val(`<?php echo (($orderObo != 0 AND count($infoObo) > 0 ) ? $infoObo['hotMeal']['hotMealHasAmountQuestion'] : null); ?>`).selectpicker('refresh').closest('.form-group').removeClass('hidden');
                                                                        let hotMealHasAmountQuestionObo = $('#hotMealHasAmountQuestionObo', '#changeOboForm').val();
                                                                        if (hotMealHasAmountQuestionObo == "da") {
                                                                            $('#hotMealAmountObo', '#changeOboForm').prop('required', true).val(`<?php echo (($orderObo != 0 AND count($infoObo) > 0 ) ? $infoObo['hotMeal']['hotMealAmount'] : null); ?>`).closest('.form-group').removeClass('hidden');
                                                                            $('#hotMealPeriodObo', '#changeOboForm').prop('required', true).val(`<?php echo (($orderObo != 0 AND count($infoObo) > 0 ) ? $infoObo['hotMeal']['hotMealPeriod'] : null); ?>`).selectpicker('refresh').closest('.form-group').removeClass('hidden');
                                                                            $('#hotMealTypeObo', '#changeOboForm').prop('required', true).val(`<?php echo (($orderObo != 0 AND count($infoObo) > 0 ) ? $infoObo['hotMeal']['hotMealType'] : null); ?>`).selectpicker('refresh').closest('.form-group').removeClass('hidden');
                                                                        } else {
                                                                            $('#hotMealAmountObo', '#changeOboForm').prop('required', false).val(null).closest('.form-group').addClass('hidden');
                                                                            $('#hotMealPeriodObo', '#changeOboForm').prop('required', false).val(null).selectpicker('refresh').closest('.form-group').addClass('hidden');
                                                                            $('#hotMealTypeObo', '#changeOboForm').prop('required', false).val(null).selectpicker('refresh').closest('.form-group').addClass('hidden');
                                                                        }
                                                                    } else {
                                                                        $('#hotMealHasAmountQuestionObo', '#changeOboForm').prop('required', false).val(null).selectpicker('refresh').closest('.form-group').addClass('hidden');
                                                                        $('#hotMealAmountObo', '#changeOboForm').prop('required', false).val(null).closest('.form-group').addClass('hidden');
                                                                        $('#hotMealPeriodObo', '#changeOboForm').prop('required', false).val(null).selectpicker('refresh').closest('.form-group').addClass('hidden');
                                                                        $('#hotMealTypeObo', '#changeOboForm').prop('required', false).val(null).selectpicker('refresh').closest('.form-group').addClass('hidden');
                                                                    }
                                                                }
                                                                function showHideHotMealHasAmountQuestionFields() {
                                                                    let hotMealHasAmountQuestionObo = $('#hotMealHasAmountQuestionObo', '#changeOboForm').val();
                                                                    if (hotMealHasAmountQuestionObo == "da") {
                                                                        $('#hotMealAmountObo', '#changeOboForm').prop('required', true).val(`<?php echo (($orderObo != 0 AND count($infoObo) > 0 ) ? $infoObo['hotMeal']['hotMealAmount'] : null); ?>`).closest('.form-group').removeClass('hidden');
                                                                        $('#hotMealPeriodObo', '#changeOboForm').prop('required', true).val(`<?php echo (($orderObo != 0 AND count($infoObo) > 0 ) ? $infoObo['hotMeal']['hotMealPeriod'] : null); ?>`).selectpicker('refresh').closest('.form-group').removeClass('hidden');
                                                                        $('#hotMealTypeObo', '#changeOboForm').prop('required', true).val(`<?php echo (($orderObo != 0 AND count($infoObo) > 0 ) ? $infoObo['hotMeal']['hotMealType'] : null); ?>`).selectpicker('refresh').closest('.form-group').removeClass('hidden');
                                                                    } else {
                                                                        $('#hotMealAmountObo', '#changeOboForm').prop('required', false).val(null).closest('.form-group').addClass('hidden');
                                                                        $('#hotMealPeriodObo', '#changeOboForm').prop('required', false).val(null).selectpicker('refresh').closest('.form-group').addClass('hidden');
                                                                        $('#hotMealTypeObo', '#changeOboForm').prop('required', false).val(null).selectpicker('refresh').closest('.form-group').addClass('hidden');
                                                                    }
                                                                }
                                                            </script>
                                                            <div class="form-group">    
                                                                <div class="col-sm-12">
                                                                    <label for="hotMealQuestionObo" class="col-sm-3 control-label">
                                                                        <span class="text-danger">
                                                                            *
                                                                        </span>
                                                                        Ima topli obrok:
                                                                    </label>
                                                                    <div class="col-sm-9">
                                                                        <div class="">
                                                                            <select class="selectpicker" id="hotMealQuestionObo" name="hotMealQuestionObo" title = "Odaberite opciju" required>
                                                                                <option value = "da">Da</option>
                                                                                <option value = "ne">Ne</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <script>
                                                                $('#hotMealQuestionObo', '#changeOboForm').on('change', showHideHotMealQuestionFields);
                                                            </script>
                                                            <div class="form-group hidden">    
                                                                <div class="col-sm-12">
                                                                    <label for="hotMealHasAmountQuestionObo" class="col-sm-3 control-label">
                                                                        <span class="text-danger">
                                                                            *
                                                                        </span>
                                                                        Topli obrok ima iznos:
                                                                    </label>
                                                                    <div class="col-sm-9">
                                                                        <div class="">
                                                                            <select class="selectpicker" id="hotMealHasAmountQuestionObo" name="hotMealHasAmountQuestionObo" title = "Odaberite opciju">
                                                                                <option value = "da">Da</option>
                                                                                <option value = "ne">Ne</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <script>
                                                                $('#hotMealHasAmountQuestionObo', '#changeOboForm').on('change', showHideHotMealHasAmountQuestionFields);
                                                            </script>
                                                            <div class="form-group hidden">
                                                                <div class="col-sm-12">
                                                                    <label for="hotMealAmountObo" class="col-sm-3 control-label">
                                                                        <span class="text-danger">
                                                                            *
                                                                        </span>
                                                                        Iznos toplog obroka:
                                                                    </label>
                                                                    <div class="col-sm-8">
                                                                        <div class="materail-input-block materail-input-block_<?php echo $fieldStyle; ?>">
                                                                            <input class="form-control materail-input" type="text" name="hotMealAmountObo" id="hotMealAmountObo" placeholder="Unesite iznos">
                                                                            <span class="materail-input-block__line"></span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-1 control-label">
                                                                        <i class="openInfoHotMealAmountObo fa fa-info-circle" aria-hidden="true"></i>
                                                                        <i class="closeInfoHotMealAmountObo fa fa-times-circle hidden" aria-hidden="true"></i>
                                                                    </div>
                                                                    <div class="messageHotMealAmountObo col-sm-12 hidden">
                                                                        <div class="alert alert-warning text-center">
                                                                            <i class="fa fa-info-circle fa-3x" aria-hidden="true"></i>
                                                                            <br>
                                                                            <h4><strong>Informacije o formatu unosa</strong></h4>
                                                                            <br>
                                                                            Unos u polje <strong>Iznos toplog obroka</strong> poželjno je da bude u narednim formatima!
                                                                            <br><br>
                                                                            Ako je određena specifična vrijednost toplog obroka, poželjan unos je npr:
                                                                            <br>
                                                                            <i>
                                                                                250,00 € 
                                                                            </i>
                                                                            <br><br>
                                                                            Ako je vrijednost toplog obroka u određenom rasponu, poželjan unos je npr: 
                                                                            <br>
                                                                            <i>
                                                                                od 180,00 € do 250,00 €
                                                                            </i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <script>
                                                                $('.openInfoHotMealAmountObo', '#changeOboForm').on('click', function(){
                                                                    $('.openInfoHotMealAmountObo', '#changeOboForm').addClass('hidden');
                                                                    $('.closeInfoHotMealAmountObo', '#changeOboForm').removeClass('hidden');
                                                                    $('.messageHotMealAmountObo', '#changeOboForm').removeClass('hidden');
                                                                });
                                                                
                                                                $('.closeInfoHotMealAmountObo', '#changeOboForm').on('click', function(){
                                                                    $('.openInfoHotMealAmountObo', '#changeOboForm').removeClass('hidden');
                                                                    $('.closeInfoHotMealAmountObo', '#changeOboForm').addClass('hidden');
                                                                    $('.messageHotMealAmountObo', '#changeOboForm').addClass('hidden');
                                                                });
                                                            </script>
                                                            <div class="form-group hidden">    
                                                                <div class="col-sm-12">
                                                                    <label for="hotMealPeriodObo" class="col-sm-3 control-label">
                                                                        <span class="text-danger">
                                                                            *
                                                                        </span>
                                                                        Period:
                                                                    </label>
                                                                    <div class="col-sm-9">
                                                                        <div class="">
                                                                            <select class="selectpicker" id="hotMealPeriodObo" name="hotMealPeriodObo" title = "Odaberite opciju">
                                                                                <option value = "mjesečno">Mjesečno</option>
                                                                                <option value = "godišnje">Godišnje</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group hidden">    
                                                                <div class="col-sm-12">
                                                                    <label for="hotMealTypeObo" class="col-sm-3 control-label">
                                                                        <span class="text-danger">
                                                                            *
                                                                        </span>
                                                                        Tip:
                                                                    </label>
                                                                    <div class="col-sm-9">
                                                                        <div class="">
                                                                            <select class="selectpicker" id="hotMealTypeObo" name="hotMealTypeObo" title = "Odaberite opciju">
                                                                                <option value = "brutto">Brutto</option>
                                                                                <option value = "netto">Netto</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <!-- 
                                                    Hot meal END
                                                -->
                                                
                                                <!-- 
                                                    Additionally START 
                                                -->
                                                    <div class="panel panel-default">
                                                        <div class="panel-body">
                                                            <div class="form-group">
                                                                <div class="col-sm-12">
                                                                    <label for="additionallyObo" class="col-sm-3 control-label">
                                                                        Dodatno:
                                                                    </label>
                                                                    <div class="col-sm-8">
                                                                        <div class="materail-input-block materail-input-block_<?php echo $fieldStyle; ?> materail-input_slide-line">
                                                                            <textarea class="form-control materail-input material-textarea" name="additionallyObo" id="additionallyObo" placeholder="Primjer: Poslodavac plaća nostrifikaciju i troškove dolaska" rows="4"></textarea>
                                                                            <span class="materail-input-block__line"></span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-1 control-label">
                                                                        <i class="openInfoAdditionallyObo fa fa-info-circle" aria-hidden="true"></i>
                                                                        <i class="closeInfoAdditionallyObo fa fa-times-circle hidden" aria-hidden="true"></i>
                                                                    </div>
                                                                    <div class="messageAdditionallyObo col-sm-12 hidden">
                                                                        <div class="alert alert-warning text-center">
                                                                            <i class="fa fa-info-circle fa-3x" aria-hidden="true"></i>
                                                                            <br>
                                                                            <h4><strong>Informacije</strong></h4>
                                                                            <br>
                                                                            Unos u polje <strong>Dodatno</strong> je opcionalni unos!
                                                                            <br>
                                                                            <br>
                                                                            Primjer unosa: <i>Poslodavac plaća nostrifikaciju i troškove dolaska</i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <script>
                                                                $('.openInfoAdditionallyObo', '#changeOboForm').on('click', function(){
                                                                    $('.openInfoAdditionallyObo', '#changeOboForm').addClass('hidden');
                                                                    $('.closeInfoAdditionallyObo', '#changeOboForm').removeClass('hidden');
                                                                    $('.messageAdditionallyObo', '#changeOboForm').removeClass('hidden');
                                                                });
                                                                
                                                                $('.closeInfoAdditionallyObo', '#changeOboForm').on('click', function(){
                                                                    $('.openInfoAdditionallyObo', '#changeOboForm').removeClass('hidden');
                                                                    $('.closeInfoAdditionallyObo', '#changeOboForm').addClass('hidden');
                                                                    $('.messageAdditionallyObo', '#changeOboForm').addClass('hidden');
                                                                });
                                                            </script>
                                                        </div>
                                                    </div>
                                                <!-- 
                                                    Additionally END 
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
                                            <button type="submit" class="btn btn-<?php echo $fieldStyle; ?> material-btn material-btn_<?php echo $fieldStyle; ?>" form="changeOboForm"><i class="fa fa-check-square-o" aria-hidden="true"></i> Spremi</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php 
                        if ($orderObo != 0 AND count($infoObo) > 0 ) {
                            ?>
                                <script>
                                    function setInitialValuesObo () {
                                        let countryCityObo = `<?php echo $infoObo['countryCity']; ?>`;
                                        $('#countryCityObo', '#changeOboForm').val(countryCityObo);

                                        let positionObo = `<?php echo $infoObo['position'] ?? null; ?>`;
                                        $('#positionObo', '#changeOboForm').val(positionObo);

                                        let positionDescObo = `<?php echo $infoObo['positionDesc'] ?? null; ?>`;
                                        $('#positionDescObo', '#changeOboForm').val(positionDescObo);

                                        let salaryObo = `<?php echo $infoObo['salary']['salary']; ?>`;
                                        $('#salaryObo', '#changeOboForm').val(salaryObo);
                                        let salaryPeriodObo = `<?php echo $infoObo['salary']['salaryPeriod']; ?>`;
                                        $('#salaryPeriodObo', '#changeOboForm').val(salaryPeriodObo).selectpicker('refresh');
                                        let salaryTypeObo = `<?php echo $infoObo['salary']['salaryType']; ?>`;
                                        $('#salaryTypeObo', '#changeOboForm').val(salaryTypeObo).selectpicker('refresh');

                                        let bonusQuestionObo = `<?php echo $infoObo['bonus']['bonusQuestion']; ?>`;
                                        $('#bonusQuestionObo', '#changeOboForm').val(bonusQuestionObo).selectpicker('refresh');
                                        if (bonusQuestionObo == "da") {
                                            let bonusHasAmountQuestionObo = `<?php echo $infoObo['bonus']['bonusHasAmountQuestion']; ?>`;
                                            $('#bonusHasAmountQuestionObo', '#changeOboForm').prop('required', true).val(bonusHasAmountQuestionObo).selectpicker('refresh').closest('.form-group').removeClass('hidden');
                                            if (bonusHasAmountQuestionObo == "da") {
                                                let bonusAmountObo = `<?php echo $infoObo['bonus']['bonusAmount']; ?>`;
                                                $('#bonusAmountObo', '#changeOboForm').prop('required', true).val(bonusAmountObo).closest('.form-group').removeClass('hidden');
                                                let bonusPeriodObo = `<?php echo $infoObo['bonus']['bonusPeriod']; ?>`;
                                                $('#bonusPeriodObo', '#changeOboForm').prop('required', true).val(bonusPeriodObo).selectpicker('refresh').closest('.form-group').removeClass('hidden');
                                                let bonusTypeObo = `<?php echo $infoObo['bonus']['bonusType']; ?>`;
                                                $('#bonusTypeObo', '#changeOboForm').prop('required', true).val(bonusTypeObo).selectpicker('refresh').closest('.form-group').removeClass('hidden');
                                            } else {
                                                $('#bonusAmountObo', '#changeOboForm').prop('required', false).closest('.form-group').addClass('hidden');
                                                $('#bonusPeriodObo', '#changeOboForm').prop('required', false).closest('.form-group').addClass('hidden');
                                                $('#bonusTypeObo', '#changeOboForm').prop('required', false).closest('.form-group').addClass('hidden');
                                            }
                                        } else {
                                            $('#bonusHasAmountQuestionObo', '#changeOboForm').prop('required', false).closest('.form-group').addClass('hidden');
                                            $('#bonusAmountObo', '#changeOboForm').prop('required', false).closest('.form-group').addClass('hidden');
                                            $('#bonusPeriodObo', '#changeOboForm').prop('required', false).closest('.form-group').addClass('hidden');
                                            $('#bonusTypeObo', '#changeOboForm').prop('required', false).closest('.form-group').addClass('hidden');
                                        }

                                        let hotMealQuestionObo = `<?php echo $infoObo['hotMeal']['hotMealQuestion']; ?>`;
                                        $('#hotMealQuestionObo', '#changeOboForm').val(hotMealQuestionObo).selectpicker('refresh');
                                        if (hotMealQuestionObo == "da") {
                                            let hotMealHasAmountQuestionObo = `<?php echo $infoObo['hotMeal']['hotMealHasAmountQuestion']; ?>`;
                                            $('#hotMealHasAmountQuestionObo', '#changeOboForm').prop('required', true).val(hotMealHasAmountQuestionObo).selectpicker('refresh').closest('.form-group').removeClass('hidden');
                                            if (hotMealHasAmountQuestionObo == "da") {
                                                let hotMealAmountObo = `<?php echo $infoObo['hotMeal']['hotMealAmount']; ?>`;
                                                $('#hotMealAmountObo', '#changeOboForm').prop('required', true).val(hotMealAmountObo).closest('.form-group').removeClass('hidden');
                                                let hotMealPeriodObo = `<?php echo $infoObo['hotMeal']['hotMealPeriod']; ?>`;
                                                $('#hotMealPeriodObo', '#changeOboForm').prop('required', true).val(hotMealPeriodObo).selectpicker('refresh').closest('.form-group').removeClass('hidden');
                                                let hotMealTypeObo = `<?php echo $infoObo['hotMeal']['hotMealType']; ?>`;
                                                $('#hotMealTypeObo', '#changeOboForm').prop('required', true).val(hotMealTypeObo).selectpicker('refresh').closest('.form-group').removeClass('hidden');
                                            } else {
                                                $('#hotMealAmountObo', '#changeOboForm').prop('required', false).closest('.form-group').addClass('hidden');
                                                $('#hotMealPeriodObo', '#changeOboForm').prop('required', false).closest('.form-group').addClass('hidden');
                                                $('#hotMealTypeObo', '#changeOboForm').prop('required', false).closest('.form-group').addClass('hidden');
                                            }
                                        } else {
                                            $('#hotMealHasAmountQuestionObo', '#changeOboForm').prop('required', false).closest('.form-group').addClass('hidden');
                                            $('#hotMealAmountObo', '#changeOboForm').prop('required', false).closest('.form-group').addClass('hidden');
                                            $('#hotMealPeriodObo', '#changeOboForm').prop('required', false).closest('.form-group').addClass('hidden');
                                            $('#hotMealTypeObo', '#changeOboForm').prop('required', false).closest('.form-group').addClass('hidden');
                                        }

                                        let apartmentObo = `<?php echo $infoObo['apartment']; ?>`;
                                        $('#apartmentObo', '#changeOboForm').val(apartmentObo).selectpicker('refresh');

                                        let additionallyObo = `<?php echo $infoObo['additionally']; ?>`;
                                        $('#additionallyObo', '#changeOboForm').val(additionallyObo);
                                    }
                                    $('#changeObo').on('shown.bs.modal',setInitialValuesObo);
                                    $('#changeObo').on('hidden.bs.modal',setInitialValuesObo);
                                </script>
                            <?php 
                        }
                    ?>
                <!-- 
                    Change Modal END
                -->
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <!-- 
                    Response Message Start
                -->
                    <?php 
                        if(isset($_GET['messageObo'])) {
                            $enabledMessagesObo = array(1,0,100);
                            $messageObo = $_GET['messageObo'];
                            $resultMessageObo = '';
                            if(in_array($messageObo, $enabledMessagesObo)) {
                                if($messageObo == 100){
                                    $resultMessageObo = '<div class="alert alert-danger text-center" role="alert"><h4><strong>Greška</strong></h4><br>Desio se problem sa ažuriranjem <strong>Ponuda i benefita od poslodavca</strong>! Obratite se administratoru sistema!</div>';
                                }elseif($messageObo == 1){
                                    $resultMessageObo = '<div class="alert alert-success text-center" role="alert"><h4><strong>Uspješno ažurirano</strong></h4><br>Uspješno ste ažurirali <strong>Ponude i benefite od poslodavca</strong></div>';
                                }elseif($messageObo == 0){
                                    $resultMessageObo = '<div class="alert alert-warning text-center" role="alert"><h4><strong>Upozorenje</strong></h4><br>Nije izvršeno ažuriranje <strong>Ponuda i benefita od poslodavca</strong>! Obratite se administratoru sistema!</div>';
                                }
                            } else {
                                $resultMessageObo = '<div class="alert alert-danger text-center" role="alert"><h4><strong>Greška</strong></h4><br>Nepredviđena poruka odgovora!</div>';
                            }
                            
                            ?>
                                <div class="row">
                                    <div class="col-xs-offset-2 col-xs-8">
                                        <?php 
                                            echo $resultMessageObo;
                                        ?>
                                    </div>
                                </div>
                            <?php 
                            unset($enabledMessagesObo);
                        }
                    ?>
                <!-- 
                    Response Message End
                -->

                <!-- 
                    Information START
                -->
                    <?php 
                        if ($orderObo != 0) {
                            //print("<pre>".print_r($infoObo,true)."</pre>");
                            if (count($infoObo) > 0) {
                                ?> 
                                    <div class="row">
                                        <strong class="col-sm-4 text-right">Država/grad:</strong>
                                        <div class="col-sm-8"><?php echo $infoObo['countryCity']; ?></div>
                                    </div>
                                    <div class="row">
                                        <strong class="col-sm-4 text-right">Naziv pozicije:</strong>
                                        <div class="col-sm-8"><?php echo $infoObo['position'] ?? '<span class="label label-danger">Potrebno urediti</span>'; ?></div>
                                    </div>
                                    <div class="row">
                                        <strong class="col-sm-4 text-right">Opis pozicije:</strong>
                                        <div class="col-sm-8"><?php echo $infoObo['positionDesc'] ?? '<span class="label label-danger">Potrebno urediti</span>'; ?></div>
                                    </div>
                                    <div class="row">
                                        <strong class="col-sm-4 text-right">Zarada:</strong>
                                        <div class="col-sm-8"><?php echo '<span class="label label-success">'.$infoObo['salary']['salary'].' '.$infoObo['salary']['salaryPeriod'] .' '.$infoObo['salary']['salaryType'].'</span>'; ?></div>
                                    </div>
                                    <div class="row">
                                        <strong class="col-sm-4 text-right">Bonus:</strong>
                                        <div class="col-sm-8"><?php echo (($infoObo['bonus']['bonusQuestion'] == "da") ? (($infoObo['bonus']['bonusHasAmountQuestion'] == "da") ? '<span class="label label-success">'.$infoObo['bonus']['bonusAmount'].' '.$infoObo['bonus']['bonusPeriod'].' '.$infoObo['bonus']['bonusType'].'</span>' : '<span class="label label-warning">DA - nepoznat iznos</span>' ) : '<span class="label label-danger">NE</span>'); ?></div>
                                    </div>
                                    <div class="row">
                                        <strong class="col-sm-4 text-right">Smještaj:</strong>
                                        <div class="col-sm-8"><?php echo (($infoObo['apartment'] != "da") ? (($infoObo['apartment'] != "ne") ? (($infoObo['apartment'] == "obezbjeđen_i_plaćen") ? '<span class="label label-success">Obezbjeđen i plaćen</span>' : '<span class="label label-warning">Obezbjeđen i odbija se od plate</span>') : '<span class="label label-danger">NE</span>') : '<span class="label label-danger">Potrebno urediti</span>'); ?></div>
                                    </div>
                                    <div class="row">
                                        <strong class="col-sm-4 text-right">Topli obrok:</strong>
                                        <div class="col-sm-8"><?php echo (($infoObo['hotMeal']['hotMealQuestion'] == "da") ? (($infoObo['hotMeal']['hotMealHasAmountQuestion'] == "da") ? '<span class="label label-success">'.$infoObo['hotMeal']['hotMealAmount'].' '.$infoObo['hotMeal']['hotMealPeriod'].' '.$infoObo['hotMeal']['hotMealType'].'</span>' : '<span class="label label-warning">DA - nepoznat iznos</span>' ) : '<span class="label label-danger">NE</span>'); ?></div>
                                    </div>
                                    <div class="row">
                                        <strong class="col-sm-4 text-right">Dodatno:</strong>
                                        <div class="col-sm-8"><?php echo (($infoObo['additionally'] != null) ? $infoObo['additionally'] : '<span class="label label-default">Nisu unešene dodatne informacije</span>'); ?></div>
                                    </div>
                                <?php 
                            } else {
                                ?>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="alert alert-warning text-center">
                                                Nisu dodane informacije o <strong>Ponudama i benefitima poslodavca</strong>!
                                            </div>
                                        </div>
                                    </div>
                                <?php 
                            }
                        } else {
                            ?>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="alert alert-danger text-center">
                                            Problem sa nalogom! Nije moguće učitati <strong>Ponude i benefite od poslodavca</strong>!
                                        </div>
                                    </div>
                                </div>
                            <?php
                        }
                
                    ?>
                <!-- 
                    Information End
                -->
            </div>
        </div>
    </div>
</div>
<?php 
    unset($orderObo);
    unset($infoObo); 
?>