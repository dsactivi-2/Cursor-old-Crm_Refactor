<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Jobstep</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Style -->
    <link href="https://crm.job-step.com/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://crm.job-step.com/css/jasny-bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://crm.job-step.com/css/bootstrap-select.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="https://crm.job-step.com/css/calendar.css" />
    <link rel="stylesheet" type="text/css" href="https://crm.job-step.com/css/timedropper.min.css" rel="stylesheet">
    <link href="https://crm.job-step.com/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://crm.job-step.com/css/responsive.dataTables.min.css" rel="stylesheet">
    <link href="https://crm.job-step.com/css/responsive.bootstrap.min.css" rel="stylesheet">
    <link href="https://crm.job-step.com/css/jquery.fancybox.css" rel="stylesheet">
    <link href="https://crm.job-step.com/js/ui/trumbowyg.min.css" rel="stylesheet">
    <link href="https://crm.job-step.com/css/select2.min.css" rel="stylesheet">
    <link href="https://crm.job-step.com/css/style.css" rel="stylesheet">
    <link type="text/css" href="https://gyrocode.github.io/jquery-datatables-checkboxes/1.2.11/css/dataTables.checkboxes.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="https://api.pa.job-step.com/mail-templates/jobstep-logo-green.png">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700&amp;subset=latin-ext" rel="stylesheet">
    <script src="https://use.fontawesome.com/758aa0fdaa.js"></script>
    <!-- FLATPICK -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/airbnb.css">
    <!-- TELEFON -->
    <link rel="stylesheet" href="https://crm.job-step.com/buildTelInput/css/intlTelInput.css">

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://crm.job-step.com/js/bootstrap.min.js"></script>
    <script src="https://crm.job-step.com/js/chart.min.js"></script>
    <script src="https://crm.job-step.com/js/modernizr.custom.63321.js"></script>
    <script type="text/javascript" src="https://crm.job-step.com/js/jquery.calendario.js"></script>
    <script src="https://crm.job-step.com/js/jquery.slimscroll.min.js"></script>
    <script src="https://crm.job-step.com/js/jquery.matchHeight-min.js"></script>
    <script src="https://crm.job-step.com/js/jasny-bootstrap.min.js"></script>
    <script src="https://crm.job-step.com/js/bootstrap-select.min.js"></script>
    <!-- TELEFON -->
    <script src="https://crm.job-step.com/buildTelInput/js/intlTelInput.js"></script>
    <script src="https://crm.job-step.com/js/jquery.dataTables.min.js"></script>

    <script src="https://crm.job-step.com/js/dataTables.responsive.min.js"></script>
    <script src="https://crm.job-step.com/js/responsive.bootstrap.min.js"></script>
    <script src="https://crm.job-step.com/js/timedropper.min.js"></script>
    <script src="https://crm.job-step.com/js/jquery.fancybox.js"></script>
    <script src="https://crm.job-step.com/js/jquery.mask.min.js"></script>
    <script src="https://crm.job-step.com/js/trumbowyg.min.js"></script>
    <script src="https://crm.job-step.com/js/timeago.js"></script>
    <script src="https://crm.job-step.com/js/select2.min.js"></script>
    <script src="https://crm.job-step.com/js/jquery.table2excel.js"></script>
    <script src="https://crm.job-step.com/js/jquery.password-generator-plugin.min.js"></script>
    <script type="text/javascript" src="https://crm.job-step.com/js/langs/hr.min.js"></script>
    <script type="text/javascript" src="https://gyrocode.github.io/jquery-datatables-checkboxes/1.2.11/js/dataTables.checkboxes.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
    <!-- FLATPICK -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <title>Confirmation Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            padding: 20px;
        }

        .confirmation-message {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 510px;
            margin: 0 auto;
        }

        .confirmation-message h2 {
            color: #333;
        }

        .confirmation-message p {
            color: #666;
        }

        .back-to-home {
            margin-top: 20px;
        }
    </style>
</head>
<?php
$language = "de";
if (isset($_GET['lang'])) {
    $language = $_GET['lang'];
} else {
    $language = 'de';
}

$translations = array(
    "bs" => array(
        "header" => "Poštovani,",
        "subheader_success" => "Uspješno sto potvrdili svoj račun, provjerite email za pristupne podatke i upute kako instalirati Partner App",
        "subheader_invalid_link" => "Vaš link je istekao, kontaktirajte nas na email adresu: support@job-step.com",
        "subheader_account_already_active" => "Vaš račun je već registrovan, provjerite prethodne mailove koje sadrže pristupne podakte i upute kako instalirati Parner App",
        "privacy_statement" => "Nastavkom prihvatate uslove o zaštiti privatnosti.",
        "privacy_archor" => "Pogledaj izjavu o zaštiti privatnosti",
        "rights_reserved" => "Sva prava pridržana - JOBSTEP",
        "privacy_title" => "Izjava o zaštiti privatnosti",
        "privacy_body" => "Hvala vam na interesovanju za apliciranje kod Jobstep Int GmbH. Zaštita vaših ličnih podataka nam je važna. Niže želimo da vas informišemo o tome kako postupamo sa vašim podacima:
										<br>
										<br>
										1. Prikupljanje podataka: Prikupljamo lične podatke koje nam dostavljate kao deo vaše prijave. To uključuje, između ostalog, vaše ime, kontakt informacije, profesionalno iskustvo i kvalifikacije.
										<br>
										<br>
										2. Upotreba vaših podataka: Podaci koje pružate koristiće se isključivo u okviru procesa prijave. Vaše informacije će se tretirati poverljivo i neće biti dostavljene trećim stranama bez vašeg pristanka.
										<br>
										<br>
										3. Čuvanje podataka: Vaši podaci će biti čuvani tokom trajanja procesa prijave. U slučaju uspešnog zaposlenja, vaše informacije će biti prenesene u našu bazu podataka zaposlenih.
										<br>
										<br>
										4. Bezbednost podataka: Sprovodimo odgovarajuće sigurnosne mere radi zaštite vaših podataka od neovlašćenog pristupa.
										<br>
										<br>
										5. Prava aplikanta: Imate pravo tražiti informacije o vašim sačuvanim podacima, ispraviti ih ili ih izbrisati. Molimo vas da nas kontaktirate na <b>info@job-step.com</b> za sva pitanja.
										<br>
										<br>
										Podnošenjem vaše aplikacije, saglasni ste sa navedenim uslovima.
										Hvala vam na poverenju u Jobstep Int GmbH.",
        "privacy_close" => "Zatvori",
        "back_to_home" => "Natrag na početnu stranicu",
    ),
    "de" => array(
        "header" => "Lieber Benutzer,",
        "subheader_success" => "Sie haben Ihr Konto erfolgreich bestätigt! Überprüfen Sie bitte Ihre E-Mails für die Anmeldedaten und Anweisungen zur Installation der Partner App.",
        "subheader_invalid_link" => "Ihr Link ist abgelaufen, kontaktieren Sie uns bitte unter der E-Mail-Adresse: support@job-step.com",
        "subheader_account_already_active" => "Ihr Konto ist bereits registriert, überprüfen Sie bitte frühere E-Mails mit Zugangsdaten und Anweisungen zur Installation der Partner-App",
        "privacy_statement" => "Mit der Fortsetzung akzeptieren Sie die Datenschutzbestimmungen.",
        "privacy_archor" => "Lesen Sie die Datenschutzerklärung",
        "rights_reserved" => "Alle Rechte vorbehalten - JOBSTEP",
        "privacy_title" => "Datenschutzerklärung",
        "privacy_body" => "Vielen Dank für Ihr Interesse an einer Bewerbung bei der Jobstep Int GmbH. Der Schutz Ihrer persönlichen Daten ist uns wichtig. Nachfolgend möchten wir Sie darüber informieren, wie wir mit Ihren Daten umgehen:
										<br>
										<br>
										1. Datenerhebung: Wir erheben personenbezogene Daten, die Sie uns im Rahmen Ihrer Bewerbung zur Verfügung stellen. Dazu gehören unter anderem Name, Kontaktdaten, beruflicher Werdegang und Qualifikationen.
										<br>
										<br>
										2. Verwendung Ihrer Daten: Die von Ihnen bereitgestellten Daten werden ausschließlich für den Bewerbungsprozess genutzt. Ihre Informationen werden vertraulich behandelt und nicht ohne Ihre Zustimmung an Dritte weitergegeben.
										<br>
										<br>
										3. Speicherung: Ihre Daten werden für die Dauer des Bewerbungsprozesses gespeichert. Bei einer erfolgreichen Anstellung werden Ihre Informationen in unsere Mitarbeiterdatenbank übernommen.
										<br>
										<br>
										4. Datensicherheit: Wir treffen angemessene Sicherheitsmaßnahmen, um Ihre Daten vor unbefugtem Zugriff zu schützen.
										<br>
										<br>
										5. Rechte der Bewerber: Sie haben das Recht, Auskunft über Ihre gespeicherten Daten zu erhalten, diese zu korrigieren oder löschen zu lassen. Bitte kontaktieren Sie uns dazu unter <b>info@job-step.com</b>.
										<br>
										<br>
										Mit der Einreichung Ihrer Bewerbung erklären Sie sich mit den oben genannten Bedingungen einverstanden.
										Vielen Dank für Ihr Vertrauen in die Jobstep Int GmbH.",
        "privacy_close" => "Schließen",
        "back_to_home" => "Zurück zur Startseite",
    ),
    "en" => array(
        "header" => "Dear user,",
        "subheader_success" => "Successfully confirmed your account! Please check your email for login details and instructions on how to install the Partner App.",
        "subheader_invalid_link" => "Your link has expired, please contact us at email address: support@job-step.com",       
        "subheader_account_already_active" => "Your account is already registered, please check previous emails containing access data and instructions on how to install the Partner App",
        "privacy_statement" => "By continuing, you accept the privacy conditions.",
        "privacy_archor" => "See privacy statement",
        "rights_reserved" => "All rights reserved - JOBSTEP",
        "privacy_title" => "Privacy Statement",
        "privacy_body" => "Thank you for your interest in applying to Jobstep Int GmbH. The protection of your personal data is important to us. Below, we would like to inform you about how we handle your data:
										<br>
										<br>
										1. Data Collection: We collect personal data that you provide to us as part of your application. This includes, among other things, your name, contact details, professional background, and qualifications.
										<br>
										<br>
										2. Use of Your Data: The data you provide will be used exclusively for the application process. Your information will be treated confidentially and will not be disclosed to third parties without your consent.
										<br>
										<br>
										3. Storage: Your data will be stored for the duration of the application process. In the case of a successful hiring, your information will be transferred to our employee database.
										<br>
										<br>
										4. Data Security: We implement appropriate security measures to protect your data from unauthorized access.
										<br>
										<br>
										5. Applicant's Rights: You have the right to request information about your stored data, to correct it, or to have it deleted. Please contact us at <b>info@job-step.com</b> for any inquiries.
										<br>
										<br>
										By submitting your application, you agree to the conditions mentioned above.
										Thank you for your trust in Jobstep Int GmbH.",
        "privacy_close" => "Close",
        "back_to_home" => "Back to home",
    ),
    "sr" => array(
        "header" => "Poštovani,",
        "subheader_success" => "Uspešno sto potvrdili svoj račun, provjerite email za pristupne podatke i upute kako instalirati Partner App",
        "subheader_invalid_link" => "Vaš link je istekao, molimo kontaktirajte nas na adresi e-pošte: support@job-step.com",
        "subheader_account_already_active" => "Vaš nalog je već registrovan, proverite prethodne e-poruke koje sadrže podatke za pristup i uputstva kako instalirati Partner aplikaciju",
        "privacy_statement" => "Nastavkom prihvatate uslove o zaštiti privatnosti.",
        "privacy_archor" => "Pogledaj izjavu o zaštiti privatnosti",
        "rights_reserved" => "Sva prava pridržana - JOBSTEP",
        "privacy_title" => "Izjava o zaštiti privatnosti",
        "privacy_body" => "Hvala vam na interesovanju za apliciranje kod Jobstep Int GmbH. Zaštita vaših ličnih podataka nam je važna. Niže želimo da vas informišemo o tome kako postupamo sa vašim podacima:
										<br>
										<br>
										1. Prikupljanje podataka: Prikupljamo lične podatke koje nam dostavljate kao deo vaše prijave. To uključuje, između ostalog, vaše ime, kontakt informacije, profesionalno iskustvo i kvalifikacije.
										<br>
										<br>
										2. Upotreba vaših podataka: Podaci koje pružate koristiće se isključivo u okviru procesa prijave. Vaše informacije će se tretirati poverljivo i neće biti dostavljene trećim stranama bez vašeg pristanka.
										<br>
										<br>
										3. Čuvanje podataka: Vaši podaci će biti čuvani tokom trajanja procesa prijave. U slučaju uspešnog zaposlenja, vaše informacije će biti prenesene u našu bazu podataka zaposlenih.
										<br>
										<br>
										4. Bezbednost podataka: Sprovodimo odgovarajuće sigurnosne mere radi zaštite vaših podataka od neovlašćenog pristupa.
										<br>
										<br>
										5. Prava aplikanta: Imate pravo tražiti informacije o vašim sačuvanim podacima, ispraviti ih ili ih izbrisati. Molimo vas da nas kontaktirate na <b>info@job-step.com</b> za sva pitanja.
										<br>
										<br>
										Podnošenjem vaše aplikacije, saglasni ste sa navedenim uslovima.
										Hvala vam na poverenju u Jobstep Int GmbH.",
        "privacy_close" => "Zatvori",
        "back_to_home" => "Natrag na početnu stranicu",
    ),
    "hr" => array(
        "header" => "Poštovani,",
        "subheader_success" => "Uspješno sto potvrdili svoj račun, provjerite email za pristupne podatke i upute kako instalirati Partner App",
        "subheader_invalid_link" => "Vaš link je istekao, molimo kontaktirajte nas na adresi e-pošte: support@job-step.com",
        "subheader_account_already_active" => "Vaš račun već je registriran, provjerite prethodne e-pošte koje sadrže podatke za pristup i upute za instalaciju Partner App",
        "privacy_archor" => "Pogledaj izjavu o zaštiti privatnosti",
        "privacy_statement" => "Nastavkom prihvatate uslove o zaštiti privatnosti.",
        "rights_reserved" => "Sva prava pridržana - JOBSTEP",
        "privacy_title" => "Izjava o zaštiti privatnosti",
        "privacy_body" => "Hvala vam na interesovanju za apliciranje kod Jobstep Int GmbH. Zaštita vaših ličnih podataka nam je važna. Niže želimo da vas informišemo o tome kako postupamo sa vašim podacima:
										<br>
										<br>
										1. Prikupljanje podataka: Prikupljamo lične podatke koje nam dostavljate kao deo vaše prijave. To uključuje, između ostalog, vaše ime, kontakt informacije, profesionalno iskustvo i kvalifikacije.
										<br>
										<br>
										2. Upotreba vaših podataka: Podaci koje pružate koristiće se isključivo u okviru procesa prijave. Vaše informacije će se tretirati poverljivo i neće biti dostavljene trećim stranama bez vašeg pristanka.
										<br>
										<br>
										3. Čuvanje podataka: Vaši podaci će biti čuvani tokom trajanja procesa prijave. U slučaju uspešnog zaposlenja, vaše informacije će biti prenesene u našu bazu podataka zaposlenih.
										<br>
										<br>
										4. Bezbednost podataka: Sprovodimo odgovarajuće sigurnosne mere radi zaštite vaših podataka od neovlašćenog pristupa.
										<br>
										<br>
										5. Prava aplikanta: Imate pravo tražiti informacije o vašim sačuvanim podacima, ispraviti ih ili ih izbrisati. Molimo vas da nas kontaktirate na <b>info@job-step.com</b> za sva pitanja.
										<br>
										<br>
										Podnošenjem vaše aplikacije, saglasni ste sa navedenim uslovima.
										Hvala vam na poverenju u Jobstep Int GmbH.",
        "privacy_close" => "Zatvori",
        "back_to_home" => "Natrag na početnu stranicu",
    ),
);

function translateTextCon($key)
{
    global $language;
    global $translations;


    echo $translations[$language][$key];
}

?>
<style>
    body {
        margin: 0;
        padding: 0;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: space-between
    }

    .content {
        flex: 1;
    }

    .footer {
        padding: 10px;
        text-align: center;
    }

    .logo_container {
        margin-top: 50px;
        height: 80px;
    }

    .JS_logo {
        border: solid #EAECF0 0.5px;
        border-top-style: none;
        border-right-style: solid;
        border-bottom-style: none;
        border-left-style: none;
    }

    .DV_logo {
        border: solid #EAECF0 0.5px;
        border-top-style: none;
        border-right-style: none;
        border-bottom-style: none;
        border-left-style: solid;
    }

    .sign_up {
        text-align: left;
        color: #6097A0;
        border-bottom: 1px solid #0000002e;
        padding: 0;
    }

    .sign_up_dvag {
        text-align: left;
        color: #C8AA22;
        border-bottom: 1px solid #0000002e;
        padding: 0;
    }

    .sign_up h4 {
        font-weight: bold;
    }

    .sign_up_dvag h4 {
        font-weight: bold;
    }

    @media (max-width: 991px) {

        .input,
        .select-posao {
            width: 100% !important;
        }
    }

    .zvjezdica {
        color: #6097A0;
    }

    .zvjezdica_dvag {
        color: #C8AA22;
    }

    .select-posao {
        width: 100%;
        height: 42px;
        border: 1px solid rgb(0 0 0 / 74%);
        background-color: #FFFFFF;
        padding: 5px;
    }

    .input {
        width: 100%;
        height: 42px;
        padding: 5px;
        border: 1px solid rgb(0 0 0 / 74%);
    }

    .nastavi {
        width: 202px;
        height: 42px;
        background: #6097A0;
        color: #FFFFFF;
        font-weight: bold;
        margin-top: 10px;
        border: 0;
    }

    .nastavi_dvag {
        width: 202px;
        height: 42px;
        background: #C8AA22;
        color: #FFFFFF;
        font-weight: bold;
        margin-top: 10px;
        border: 0;
    }

    .bootstrap-select {
        width: 100% !important;
        height: 42px !important;
        border: 1px solid rgba(17, 17, 19, 0.2) !important;
        border-radius: 4px;
        background-color: #FFFFFF;
    }

    .bootstrap-select>.dropdown-toggle {
        width: 100%;
        padding-right: 25px;
        z-index: 1;
        height: 100%;
    }

    .custom-select {
        width: 100%;
        position: relative;
    }

    .custom-select select {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        width: 100%;
        padding: 10px;
        cursor: pointer;
        font-family: Inter;
    }

    .custom-select select>option {
        color: #101828;
        font-family: Inter;
    }

    .material-radio-group__caption {
        font-family: Inter;
        font-size: 14px;
        font-style: normal;
        line-height: 20px;
    }

    .custom-select::after {
        font-family: FontAwesome;
        content: '\f078';
        /* Unicode character for down arrow */
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        pointer-events: none;
        font-size: 10px;
        color: #667085;
    }

    .confirmation-message {
        margin-top: 150px;
    }

    .back-to-home {
        margin-top: 20px;
        /* Adjust as needed */
        text-align: center;
    }

    .back-to-home a {
        display: inline-block;
        padding: 10px 20px;
        background-color: #6097A0;
        color: #FFFFFF;
        text-decoration: none;
        font-weight: bold;
        border-radius: 5px;
        transition: background-color 0.1s ease-in-out;
    }

    .back-to-home a:hover {
        background-color: #91d1db;
    }

    .back-to-home-dvag {
        margin-top: 20px;
        text-align: center;
    }

    .back-to-home-dvag a {
        display: inline-block;
        padding: 10px 20px;
        background-color: #C8AA22;
        color: #FFFFFF;
        text-decoration: none;
        font-weight: bold;
        border-radius: 5px;
        transition: background-color 0.1s ease-in-out;
    }

    .back-to-home-dvag a:hover {
        background-color: #dbbc30;
    }

    @media (max-width: 991px) {
        .select-posao {
            width: 85vw;
            height: 50px;
        }

        .bootstrap-select {
            width: 85vw !important;
            height: 50px !important;
        }

        .input {
            width: 85vw;
            height: 50px;
        }
    }
</style>
<script>
    $(document).ready(function() {
        function getToken() {
            var url = window.location.href;
            var parsedUrl = new URL(url);
            var pathname = parsedUrl.pathname;
            var parts = pathname.split('/');
            var token = parts[3];

            return token;
        }

        function getSubdomain() {
            var url = window.location.href;
            var parsedUrl = new URL(url);
            var hostname = parsedUrl.hostname;
            var parts = hostname.split('.');

            if (parts[0] === "dev") {
                return "http://dev.";
            } else if (parts[0] === "staging") {
                return "https://staging.";
            } else {
                return "https://";
            }
        }

        $.ajax({
            type: 'POST',
            url: getSubdomain() + 'api.pa.job-step.com/?request=activate_account',
            data: {
                'token': getToken()
            },
            success: function(response) {
                $('#subheader_success').fadeIn();
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
                console.log(xhr);
                if (xhr.responseText == 'account_already_active') {
                    $('#subheader_account_already_active').fadeIn();
                }
                else{
                    $('#subheader_invalid_link').fadeIn();
                }
            }
        });
    });
</script>

<body>
    <div class="container">
        <div class="row logo_container">
            <div id="JS_logo" class="<?php if ($_GET['company'] == "3") {
                                            echo "JS_logo col-xs-6 text-right";
                                        } else {
                                            echo "col-xs-12 text-center";
                                        } ?>">
                <a href="https://job-step.com">
                    <svg width="78" height="78" viewBox="0 0 49 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19.5531 6.5357C21.0277 6.5357 22.2231 5.28753 22.2231 3.74783C22.2231 2.20813 21.0277 0.959961 19.5531 0.959961C18.0786 0.959961 16.8832 2.20813 16.8832 3.74783C16.8832 5.28753 18.0786 6.5357 19.5531 6.5357Z" fill="#6097A0" />
                        <path d="M44.3982 20.659C41.2428 23.1267 33.4433 26.3447 27.118 23.263C18.7338 19.1781 16.6477 10.8754 16.6607 7.54953C16.6737 4.22364 13.4056 18.5593 24.003 25.2905C33.9829 31.6295 42.6862 24.6599 44.6349 20.8823C44.7265 20.7046 44.5474 20.5423 44.3982 20.659Z" fill="#6097A0" />
                        <path d="M4.66963 46.915C6.49379 42.1581 11.7416 33.5458 20.2206 32.3868C31.3454 30.8662 37.5753 39.4832 39.3553 43.0314C41.1352 46.5796 36.6853 27.8249 21.778 28.0783C7.58929 28.3195 4.08351 41.648 4.35408 46.8659C4.36475 47.0716 4.5972 47.1038 4.66963 46.915Z" fill="#6097A0" />
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M32.4578 22.2492C35.6527 22.2492 38.2427 19.6393 38.2427 16.42C38.2427 13.2006 35.6527 10.5908 32.4578 10.5908C29.2629 10.5908 26.6729 13.2006 26.6729 16.42C26.6729 19.6393 29.2629 22.2492 32.4578 22.2492ZM30.2329 16.6735C31.4617 16.6735 32.4579 15.6522 32.4579 14.3925C32.4579 13.1327 31.4617 12.1115 30.2329 12.1115C29.0041 12.1115 28.0079 13.1327 28.0079 14.3925C28.0079 15.6522 29.0041 16.6735 30.2329 16.6735Z" fill="#6097A0" />
                    </svg>
                </a>
            </div>
            <div id="DV_logo" style="<?php if ($_GET['company'] != "3") {
                                            echo "display: none";
                                        } ?>" class="DV_logo col-xs-6 text-left">
                <svg width="78" height="78" viewBox="0 0 49 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M41.6625 10.3411L37.9437 18.058C37.9086 18.1317 37.9033 18.2158 37.9314 18.293C38.4278 19.7115 38.6997 21.23 38.6997 22.8134C38.6997 30.5023 32.3426 36.7358 24.4998 36.7358C16.657 36.7358 10.2999 30.5023 10.2999 22.8134C10.2999 21.2353 10.5701 19.7203 11.063 18.307C11.0893 18.2299 11.0858 18.1475 11.0507 18.0738L7.34945 10.3271C7.24771 10.1149 6.95827 10.0903 6.81969 10.2797C4.21653 13.8007 2.68164 18.1317 2.68164 22.8134C2.68164 34.6281 12.4505 44.2055 24.4998 44.2055C36.5491 44.2055 46.318 34.6281 46.318 22.8134C46.318 18.1387 44.7866 13.8129 42.1905 10.2937C42.0536 10.1044 41.7642 10.1289 41.6625 10.3411Z" fill="#C8AA22" />
                    <path d="M28.7415 34.8649L41.1855 9.03133C39.5945 7.18144 37.6877 5.59808 35.5477 4.36365L24.5017 27.3146L13.7154 4.83357C13.5891 4.5688 13.2611 4.47587 13.0085 4.62842C11.1719 5.74537 9.51775 7.12182 8.09864 8.70694C7.96708 8.85423 7.93726 9.06991 8.02321 9.24876L20.3269 34.9947C20.3462 35.035 20.383 35.0666 20.4268 35.0788C25.242 36.4571 28.2591 35.1349 28.6714 34.9368C28.7029 34.921 28.7275 34.8965 28.7415 34.8649Z" fill="#C8AA22" />
                </svg>
            </div>
        </div>
        <div class="confirmation-message">
            <h2> <?php translateTextCon("header"); ?> </h2>
            <p id = "subheader_success" style = "display:none;"><?php translateTextCon("subheader_success"); ?></p>
            <p id = "subheader_invalid_link" style = "display:none;"><?php translateTextCon("subheader_invalid_link"); ?></p>
            <p id = "subheader_account_already_active" style = "display:none;"><?php translateTextCon("subheader_account_already_active"); ?></p>
        </div>
    </div>
    <div class="footer">
        <div class="<?php if ($_GET['company'] == "3") {
                        echo "back-to-home-dvag";
                    } else {
                        echo "back-to-home";
                    } ?> ">
            <a href="https://join.job-step.com"><?php translateTextCon("back_to_home"); ?> </a>
        </div>
        <div class="form-group">
            <label for="asd" class="col-sm-3 control-label"><span class="text-danger"></span></label>
            <div class="col-sm-6" style="padding-top: 10px;">
                <p class="text-center"> <?php translateTextCon("privacy_statement"); ?>
                    <br><a href="" type="button" data-toggle="modal" data-target="#privacyModal"><?php translateTextCon("privacy_archor"); ?> </a>
                </p>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <div class="modal fade modal-fullscreen text-left" id="privacyModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="col-md-offset-3 col-md-6  col-xs-12  col-sm-offset-2 col-sm-8 col-lg-offset-4 col-lg-4">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content" style="padding: 30px;">
                                <div class="modal-header" style="display: flex;border-bottom: none;">
                                    <h5 class="modal-title" style="width: 90%; padding: 0; border-left: none; font-weight: bold;" id="exampleModalLabel"><?php translateTextCon("privacy_title"); ?></h5>
                                    <button class="close material-modal__close" style="width: 10%;" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <p><?php translateTextCon("privacy_body"); ?></p>
                                </div>
                                <div class="modal-footer" style="border-top: none;">
                                    <button type="button" style="color: #344054; margin-top: 10px; border: 1px solid #D0D5DD; border-radius: 6px; height: 44px; background: transparent; font-weight: bold;" class="btn btn-secondary col-xs-12" data-dismiss="modal"><?php translateTextCon("privacy_close"); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <?php
                echo "<p>©" . date('Y') . " " . $translations[$language]["rights_reserved"] . "</p>";
                ?>
            </div>
        </div>
    </div>
</body>

</html>