<?php 
    include('translations/company-registration-trans.php'); 
    $lang = $_GET['lang'] ?? 'en';
    $token = $_GET['token'] ?? 0;
    $trans = $translations[$lang];
    include("includes/functions.php"); 
?>
<html>
    <head>
        <meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Jobstep</title>
        <link rel="icon" href="<?php getSiteUrl(); ?>images/jobstep-logo.svg">

        <?php 
            include('includes/head.php'); 
        ?>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
        <link href="<?php getSiteUrl(); ?>includes/css/company-registration.css" rel="stylesheet">
    </head>
    <body>
        <div class="container">
            <div class="card border-0 rounded-3 w-100 py-5 px-5 mx-auto my-5">
                <div class="row gy-4">
                    <div class="col-md-12">
                        <!-- 
                            Logo START
                            -->
                                <img src="<?php getSiteUrl(); ?>images/jobstep-logo.svg" class="rounded mx-auto d-block" alt="JobStep Logo">
                            <!-- 
                            Logo END
                        -->
                    </div>
                    <div class="col-md-12">
                        <!-- 
                            Steps START
                            -->
                                <div class="step-container">
                                    <div class="step active" id="step1"></div>
                                    <div class="step default" id="step2"></div>
                                    <div class="step default" id="step3"></div>
                                </div>
                            <!-- 
                            Steps END
                        -->
                    </div>
                    <div class="col-md-12">
                        <!-- 
                            Languages START
                            -->
                                <div class="w-50">
                                    <label for="language" class="form-label inter-500 fs-7 js-gray-700-color"><?php echo $trans['Jezik']; ?></label>
                                    <select class="selectpicker" id="language" data-width="100%">
                                        <option value="en" data-address="<?php echo getSiteUrlr() . 'company/add/en' . ( ($token !== 0) ? '/' . $token : '' ); ?>" <?php echo(($lang == 'en') ? 'selected': ''); ?>><?php echo $trans['Engleski']; ?></option>
                                        <option value="de" data-address="<?php echo getSiteUrlr() . 'company/add/de' . ( ($token !== 0) ? '/' . $token : '' ); ?>" <?php echo(($lang == 'de') ? 'selected': ''); ?>><?php echo $trans['Njemački']; ?></option>
                                        <option value="bs" data-address="<?php echo getSiteUrlr() . 'company/add/bs' . ( ($token !== 0) ? '/' . $token : '' ); ?>" <?php echo(($lang == 'bs') ? 'selected': ''); ?>><?php echo $trans['Bosanski']; ?></option>
                                        <option value="sr" data-address="<?php echo getSiteUrlr() . 'company/add/sr' . ( ($token !== 0) ? '/' . $token : '' ); ?>" <?php echo(($lang == 'sr') ? 'selected': ''); ?>><?php echo $trans['Srpski']; ?></option>
                                        <option value="hr" data-address="<?php echo getSiteUrlr() . 'company/add/hr' . ( ($token !== 0) ? '/' . $token : '' ); ?>" <?php echo(($lang == 'hr') ? 'selected': ''); ?>><?php echo $trans['Hrvatski']; ?></option>
                                    </select>
                                </div>
                            <!-- 
                            Languages END
                        -->
                    </div>
                    <div class="col-md-12">
                        <!-- 
                            Form START
                            -->
                                <form class="mb-0" id="company-registration" autocomplete="off" novalidate>
                                    <input type="hidden" id="current_language" name="current_language" value="<?php echo $lang; ?>"/>
                                    <input type="hidden" id="current_token" name="current_token" value="<?php echo $token; ?>"/>
                                    <input type="hidden" id="url_c" name="url_c" value="<?php getCRMUrl(); ?>"/>
                                    <input type="hidden" id="url_j" name="url_j" value="<?php getSiteUrl(); ?>"/>
                                    <main class="step1">
                                        <p class="h4 inter-700 js-gray-700-color mb-0"><?php echo $trans['Dodaj kompaniju']; ?></p>
                                        <p class="inter-400 fs-6 js-gray-700-color mb-3">* <?php echo $trans['Obavezna polja']; ?></p>
                                        <div class="row gx-3 gy-3">
                                            <div class="col-md-6">
                                                <label for="company_name" class="form-label inter-500 fs-7 js-gray-700-color">* <?php echo $trans['Naziv kompanije']; ?></label>
                                                <input type="text" class="form-control inter-400 fs-6" id="company_name" name="company_name" placeholder="<?php echo $trans['Unesite naziv kompanije']; ?>" minlength="2" maxlength="149" required> 
                                                <div class="form-text inter-400 fs-8 company_name_message_1" hidden>
                                                    <?php echo $trans['Molimo popunite ovo polje']; ?>
                                                </div>
                                                <div class="form-text inter-400 fs-8 company_name_message_2" hidden>
                                                    <?php echo $trans['Molimo unesite najmanje 2 karaktera']; ?>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="company_size" class="form-label inter-500 fs-7 js-gray-700-color"><?php echo $trans['Veličina kompanije']; ?></label>
                                                <select class="selectpicker" id="company_size" name="company_size" data-width="100%" title="<?php echo $trans['Odaberite veličinu']; ?>">
                                                    <option value="1-10">1 - 10</option>
                                                    <option value="11-50">11 - 50</option>
                                                    <option value="51-100">51 - 100</option>
                                                    <option value="101-500">101 - 500</option>
                                                    <option value=">501">> 501</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="company_country" class="form-label inter-500 fs-7 js-gray-700-color"><?php echo $trans['Država']; ?></label>
                                                <select class="selectpicker" id="company_country" name="company_country" data-width="100%" title="<?php echo $trans['Odaberite državu']; ?>">
                                                    <option value="Germany"><?php echo $trans['Njemačka']; ?></option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="company_city" class="form-label inter-500 fs-7 js-gray-700-color"><?php echo $trans['Grad']; ?></label>
                                                <input type="text" class="form-control inter-400 fs-6" id="company_city" name="company_city" placeholder="<?php echo $trans['Unesite grad']; ?>" maxlength="149">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="company_zip_code" class="form-label inter-500 fs-7 js-gray-700-color"><?php echo $trans['Poštanski broj']; ?></label>
                                                <input type="number" class="form-control inter-400 fs-6" id="company_zip_code" name="company_zip_code" placeholder="<?php echo $trans['XXXXX']; ?>" minlength="5" maxlength="5">
                                                <div class="form-text inter-400 fs-8 company_zip_code_message_1" hidden>
                                                    <?php echo $trans['Poštanski broj mora imati tačno 5 znakova']; ?>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="company_address" class="form-label inter-500 fs-7 js-gray-700-color"><?php echo $trans['Adresa']; ?></label>
                                                <input type="text" class="form-control inter-400 fs-6" id="company_address" name="company_address" placeholder="<?php echo $trans['Unesite adresu']; ?>" maxlength="249"> 
                                            </div>
                                        </div>
                                        <div class="row gy-2 mt-5">
                                            <div class="col-md-12">
                                                <button class="btn btn-submit w-100 inter-400 fs-6"><?php echo $trans['Nastaviti']; ?></button>
                                            </div>
                                            <div class="col-md-12">
                                                <button class="btn btn-quit w-100 inter-400 fs-6"><?php echo $trans['Otkazati']; ?></button>
                                            </div>
                                            <div class="col-md-12">
                                                <p class="inter-400 fs-8 js-gray-700-color mb-3">* <?php echo $trans['Sva polja označena sa * su obavezna']; ?></p>
                                            </div>
                                        </div>
                                    </main>
                                    <main class="step2" hidden>
                                        <p class="h4 inter-700 js-gray-700-color mb-0"><?php echo $trans['Dodaj kontakt osobu']; ?></p>
                                        <p class="inter-400 fs-6 js-gray-700-color mb-3">* <?php echo $trans['Obavezna polja']; ?></p>
                                        <div class="row gx-3 gy-3">
                                            <div class="col-md-6">
                                                <label for="company_contact_name" class="form-label inter-500 fs-7 js-gray-700-color"><?php echo $trans['Ime']; ?></label>
                                                <input type="text" class="form-control inter-400 fs-6" id="company_contact_name" name="company_contact_name" placeholder="<?php echo $trans['Unesite ime']; ?>" maxlength="149">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="company_contact_surnname" class="form-label inter-500 fs-7 js-gray-700-color"><?php echo $trans['Prezime']; ?></label>
                                                <input type="text" class="form-control inter-400 fs-6" id="company_contact_surnname" name="company_contact_surnname" placeholder="<?php echo $trans['Unesite prezime']; ?>" maxlength="149">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="company_contact_jobtitle" class="form-label inter-500 fs-7 js-gray-700-color"><?php echo $trans['Naziv posla kontakta']; ?></label>
                                                <input type="text" class="form-control inter-400 fs-6" id="company_contact_jobtitle" name="company_contact_jobtitle" placeholder="<?php echo $trans['Unesite naziv posla']; ?>" maxlength="254">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="company_contact_email" class="form-label inter-500 fs-7 js-gray-700-color">* <?php echo $trans['Email adresa']; ?></label>
                                                <input type="email" class="form-control inter-400 fs-6" id="company_contact_email" name="company_contact_email" placeholder="<?php echo $trans['Unesite email adresu']; ?>" maxlength="249" required>
                                                <div class="form-text inter-400 fs-8 company_contact_email_message_1" hidden>
                                                    <?php echo $trans['Molimo popunite ovo polje']; ?>
                                                </div>
                                                <div class="form-text inter-400 fs-8 company_contact_email_message_2" hidden>
                                                    <?php echo $trans['Molimo unesite email adresu']; ?>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <label for="company_contact_phone" class="form-label inter-500 fs-7 js-gray-700-color">* <?php echo $trans['Broj telefona']; ?></label>
                                                <input type="tel" class="form-control inter-400 fs-6" id="company_contact_phone" name="company_contact_phone" required>
                                                <div class="form-text inter-400 fs-8 company_contact_phone_message_1" hidden>
                                                    <?php echo $trans['Molimo popunite ovo polje']; ?>
                                                </div>
                                                <div class="form-text inter-400 fs-8 company_contact_phone_message_2" hidden>
                                                    <?php echo $trans['Molimo unesite broj telefona']; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row gy-2 mt-5">
                                            <div class="col-md-12">
                                                <button class="btn btn-submit w-100 inter-400 fs-6"><?php echo $trans['Nastaviti']; ?></button>
                                            </div>
                                            <div class="col-md-12">
                                                <button class="btn btn-quit w-100 inter-400 fs-6"><?php echo $trans['Nazad']; ?></button>
                                            </div>
                                            <div class="col-md-12">
                                                <p class="inter-400 fs-8 js-gray-700-color mb-3">* <?php echo $trans['Sva polja označena sa * su obavezna']; ?></p>
                                            </div>
                                        </div>
                                    </main>
                                    <main class="step3" hidden>
                                        <p class="h4 inter-700 js-gray-700-color mb-0"><?php echo $trans['Dodaj potrebnu profesiju']; ?></p>
                                        <p class="inter-400 fs-6 js-gray-700-color mb-3">* <?php echo $trans['Obavezna polja']; ?></p>
                                        <div class="row gx-3 gy-3">
                                            <div class="col-md-6 d-flex flex-column justify-content-end">
                                                <label for="number_of_workers" class="form-label inter-500 fs-7 js-gray-700-color">* <?php echo $trans['Ukupan broj potrebnih radnika']; ?></label>
                                                <input type="number" class="form-control inter-400 fs-6" id="number_of_workers" name="number_of_workers" placeholder="<?php echo $trans['Unesite broj']; ?>" required> 
                                                <div class="form-text inter-400 fs-8 number_of_workers_message_1" hidden>
                                                    <?php echo $trans['Molimo popunite ovo polje']; ?>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="job_title" class="form-label">
                                                    <span class="inter-500 fs-7 js-gray-700-color"><?php echo $trans['Naziv radnog mjesta']; ?></span><br>
                                                    <span class="inter-400 fs-7 js-gray-600-color"><?php echo $trans['Označite tražena zanimanja ili ih upišite']; ?></span>
                                                </label>
                                                <select class="selectpicker" id="job_title" name="job_title" data-width="100%" title="<?php echo $trans['Odaberite zanimanja']; ?>" data-live-search="true" data-size="5" multiple>
                                                </select>
                                            </div>
                                            <div class="col-md-12" id="other_job_professions" hidden>
                                                <input type="hidden" id="other_job_profession_cnt" name="other_job_profession_cnt"/>
                                                <div class="row gx-3 gy-3 offset-md-6">
                                                    <div class="col-md-12" id="other_job_professions_fields">
                                                    </div>
                                                    <div class="col-md-12">
                                                        <button type="button" class="border-0 bg-white text-center fs-6 inter-400 js-color mb-0 add_other_job_proffesion_field">
                                                            <i class="fa fa-plus me-1" aria-hidden="true"></i> <?php echo $trans['Dodajte zanimanje']; ?>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <label for="schedule_a_call" class="form-label">
                                                    <span class="inter-500 fs-7 js-gray-700-color"><?php echo $trans['Datum sastanka']; ?></span><br>
                                                    <span class="inter-400 fs-7 js-gray-600-color"><?php echo $trans['Želite li zakazati poziv?']; ?></span>
                                                </label>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="schedule_a_call" name="schedule_a_call" value="1">
                                                    <label class="form-check-label inter-500 fs-7 js-gray-700-color" for="schedule_a_call">
                                                        <?php echo $trans['Zakažite poziv']; ?>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-12" hidden>
                                                <label for="call_date" class="form-label inter-500 fs-7 js-gray-700-color">* <?php echo $trans['Datum']; ?></label>
                                                <input type="text" class="form-control inter-400 fs-6" id="call_date" name="call_date" placeholder="<?php echo $trans['Odaberite datum']; ?>"> 
                                                <div class="form-text inter-400 fs-8 call_date_message_1" hidden>
                                                    <?php echo $trans['Molimo popunite ovo polje']; ?>
                                                </div>
                                            </div>
                                            <div class="col-md-12" hidden>
                                                <label for="call_time" class="form-label inter-500 fs-7 js-gray-700-color">* <?php echo $trans['Vrijeme']; ?></label>
                                                <select class="selectpicker" id="call_time" name="call_time" data-width="100%" title="<?php echo $trans['Odaberite vrijeme']; ?>" data-size="5">
                                                    <option value="08:00 - 09:00">08:00 - 09:00</option>
                                                    <option value="09:00 - 10:00">09:00 - 10:00</option>
                                                    <option value="10:00 - 11:00">10:00 - 11:00</option>
                                                    <option value="11:00 - 12:00">11:00 - 12:00</option>
                                                    <option value="12:00 - 13:00">12:00 - 13:00</option>
                                                    <option value="13:00 - 14:00">13:00 - 14:00</option>
                                                    <option value="14:00 - 15:00">14:00 - 15:00</option>
                                                    <option value="15:00 - 16:00">15:00 - 16:00</option>
                                                    <option value="16:00 - 17:00">16:00 - 17:00</option>
                                                </select>
                                                <div class="form-text inter-400 fs-8 call_time_message_1" hidden>
                                                    <?php echo $trans['Molimo odaberite vrijednost']; ?>
                                                </div>
                                            </div>
                                            <div class="col-md-12"> 
                                                <label for="message" class="form-label inter-500 fs-7 js-gray-700-color"><?php echo $trans['Poruka']; ?></label>
                                                <textarea class="form-control inter-400 fs-6 mb-1" id="message" name="message" rows="4" maxlength="200" placeholder="<?php echo $trans['Unesite poruku']; ?>"></textarea>
                                                <span class="inter-400 fs-7 js-gray-600-color"><?php echo $trans['Još N znakova']; ?></span>
                                            </div>
                                        </div>
                                        <div class="row gy-2 mt-5">
                                            <div class="col-md-12">
                                                <button class="btn btn-submit w-100 inter-400 fs-6">
                                                    <span class="spinner-border spinner-border-sm me-1 visually-hidden" role="status" aria-hidden="true"></span>
                                                    <?php echo $trans['Poslati']; ?>
                                                </button>
                                            </div>
                                            <div class="col-md-12">
                                                <button class="btn btn-quit w-100 inter-400 fs-6"><?php echo $trans['Nazad']; ?></button>
                                            </div>
                                            <div class="col-md-12">
                                                <p class="inter-400 fs-8 js-gray-700-color mb-3">* <?php echo $trans['Sva polja označena sa * su obavezna']; ?></p>
                                            </div>
                                        </div>
                                    </main>
                                </form>
                            <!-- 
                            Form END
                        -->
                    </div>
                    <div class="col-md-12">
                        <!-- 
                            Footer START
                            -->
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <p class="text-center fs-6 inter-400 js-gray-700-color mb-0"><?php echo $trans['Nastavkom prihvatate uslove o zaštiti privatnosti.']; ?></p>
                                        <button type="button" class="border-0 bg-white text-center fs-6 inter-400 js-color mb-0" data-bs-toggle="modal" data-bs-target="#privacy_statement">
                                            <?php echo $trans['Pogledaj izjavu o zaštiti privatnosti']; ?>
                                        </button>
                                    </div>
                                    <div class="col-md-12">
                                        <p class="text-center fs-8 inter-400 js-gray-500-color mb-0 border-top-gray-200 pt-3 mt-3"><?php echo '©'. date('Y') . ' ' . $trans['Sva prava pridržana - JOBSTEP']; ?></p>
                                    </div>
                                </div>
                            <!-- 
                            Footer END
                        -->
                        <!-- 
                            Modal START
                            -->
                            <div class="modal fade" id="privacy_statement" tabindex="-1" aria-labelledby="privacy_statement_label" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <p class="h4 inter-700 js-gray-700-color mb-0" id="privacy_statement_label"><?php echo $trans['privacy_title']; ?></p>
                                        </div>
                                        <div class="modal-body">
                                            <p class="inter-500 fs-7 js-gray-700-color mb-0"><?php echo $trans['privacy_body']; ?></p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-quit w-100 inter-400 fs-6" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- 
                            Modal END
                        -->
                    </div>
                </div>
            </div>
        </div>
        <script src="<?php getSiteUrl(); ?>includes/js/company-registration.js?time=<?php echo time(); ?>"></script>
    </body>
</html>
<?php 
    unset($translations); 
    unset($trans);
?>