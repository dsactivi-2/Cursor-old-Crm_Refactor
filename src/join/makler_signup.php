<html>
<!DOCTYPE html>
<?php
$language = "de";

if (isset($_GET['lang'])) {
    $allowedLanguages = array("en", "ba", "hr", "sr", "de");
    $language = $_GET['lang'];

    if (!in_array($language, $allowedLanguages)) {
        $language = "de";
    }
} else {
    $language = "de";
}

$company = "dvag";
if (isset($_GET['type'])) {
    $company = $_GET['type'];
} else {
    $company = 'dvag';
}

?>

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


</head>
<?php
$translations = array(
    "ba" => array(
        "header" => "Kreiraj profil za Partner App",
        "subheader" => "Popuni sva obavezna polja i registruj se!",
        "register" => "Registruj se",
        "firstname" => "Ime",
        "lastname" => "Prezime",
        "email" => "Email",
        "phone_number" => "Mobilni telefon",
        "os" => "Operativni sistem mobilnog telefona",
        "required" => "Sva polja označena sa * su obavezna",
        "privacy_statement" => "Nastavkom prihvatate uslove o zaštiti privatnosti.",
        "privacy_archor" => "Pogledaj izjavu o zaštiti privatnosti",
        "rights_reserved" => "Sva prava pridržana - JOBSTEP",
        "direction_number" => "Broj direkcije",
        "makler_id" => "VM-broj",
        "required_field" => "Ispunite ovo polje",
        "invalid_value" => "Neispravna vrijednost",
        "valid_phone" => "Validan broj",
        "invalid_phone" => "Pogrešan broj",
        "invalid_country_code" => "Netačan pozivni broj",
        "number_too_long" => "Broj je predug",
        "number_too_short" => "Broj je prekratak",
        "privacy_title" => "Izjava o zaštiti privatnosti",
        "privacy_body" => "Hvala vam na interesovanju za apliciranje kod JOBSTEP. Zaštita vaših ličnih podataka nam je važna. Niže želimo da vas informišemo o tome kako postupamo sa vašim podacima:
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
										Hvala vam na poverenju u JOBSTEP.",
        "privacy_close" => "Zatvori",
        "de" => "Njemački",
        "en" => "Engleski",
        "sr" => "Srpski",
        "hr" => "Hrvatski",
        "ba" => "Bosanski",
        "language" => "Jezik",
        "email_unavailable" => "Vaša e-mail adresa nije na listi postojećih partnera.",
        "email_taken" => "Email adresa zauzeta",
        "back_to_home" => "Nazad na početnu stranicu",
    ),
    "de" => array(
        "header" => "Erstelle Sie ein Profil für die Partner-App",
        "subheader" => "Füllen Sie alle Pflichtfelder aus und registrieren Sie sich!",
        "register" => "Registrieren",
        "firstname" => "Vorname",
        "lastname" => "Nachname",
        "email" => "E-Mail",
        "phone_number" => "Handynummer",
        "os" => "Betriebssystem des Mobiltelefons",
        "required" => "Alle mit * gekennzeichneten Felder sind Pflichtfelder",
        "privacy_statement" => "Mit der Fortsetzung akzeptieren Sie die Datenschutzbestimmungen.",
        "privacy_archor" => "Lesen Sie die Datenschutzerklärung",
        "rights_reserved" => "Alle Rechte vorbehalten - JOBSTEP",
        "direction_number" => "Direktionsnummer",
        "makler_id" => "VM-Nummer",
        "required_field" => "Feld ausfüllen",
        "invalid_value" => "Ungültiger Wert",
        "valid_phone" => "Korrekte Nummer",
        "invalid_phone" => "Falsche Nummer",
        "invalid_country_code" => "Ungültiger Ländercode",
        "number_too_long" => "Zu lange Nummer",
        "number_too_short" => "Zu kurze Nummer",
        "privacy_title" => "Datenschutzerklärung",
        "privacy_body" => "Vielen Dank für Ihr Interesse an einer Bewerbung bei der JOBSTEP. Der Schutz Ihrer persönlichen Daten ist uns wichtig. Nachfolgend möchten wir Sie darüber informieren, wie wir mit Ihren Daten umgehen:
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
										Vielen Dank für Ihr Vertrauen in die JOBSTEP.",
        "privacy_close" => "Schließen",
        "de" => "Deutsch",
        "en" => "Englisch",
        "sr" => "Serbisch",
        "hr" => "Kroatisch",
        "ba" => "Bosnisch",
        "language" => "Sprache",
        "email_unavailable" => "Deine E-Mail-Adresse ist nicht in der Liste der bestehenden Partner.",
        "email_taken" => "E-Mail-Adresse bereits vergeben",
        "back_to_home" => "Zurück zur Startseite",
    ),
    "en" => array(
        "header" => "Create a profile for Partner App",
        "subheader" => "Fill in all mandatory fields and register!",
        "register" => "Register",
        "firstname" => "First Name",
        "lastname" => "Last Name",
        "email" => "Email",
        "phone_number" => "Phone Number",
        "os" => "Mobile Operating System",
        "required" => "All fields marked with * are mandatory",
        "privacy_statement" => "By continuing, you accept the privacy conditions.",
        "privacy_archor" => "See privacy statement",
        "rights_reserved" => "All rights reserved - JOBSTEP",
        "direction_number" => "Direction Number",
        "makler_id" => "VM-number",
        "required_field" => "Fill out the field",
        "invalid_value" => "Invalid Value",
        "valid_phone" => "Correct number",
        "invalid_phone" => "Invalid phone",
        "invalid_country_code" => "Invalid country code",
        "number_too_long" => "Number too long",
        "number_too_short" => "Number too short",
        "privacy_title" => "Privacy Statement",
        "privacy_body" => "Thank you for your interest in applying to JOBSTEP. The protection of your personal data is important to us. Below, we would like to inform you about how we handle your data:
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
										Thank you for your trust in JOBSTEP.",
        "privacy_close" => "Close",
        "de" => "German",
        "en" => "English",
        "sr" => "Serbian",
        "hr" => "Croatian",
        "ba" => "Bosnian",
        "language" => "Language",
        "email_unavailable" => "Your email is not on the list of existing partners",
        "email_taken" => "Email address already taken",
        "back_to_home" => "Back to home",
    ),
    "sr" => array(
        "header" => "Kreiraj profil za Partner App",
        "subheader" => "Popuni sva obavezna polja i registruj se!",
        "register" => "Registruj se",
        "firstname" => "Ime",
        "lastname" => "Prezime",
        "email" => "Email",
        "phone_number" => "Mobilni telefon",
        "os" => "Operativni sistem mobilnog telefona",
        "required" => "Sva polja označena sa * su obavezna",
        "privacy_statement" => "Nastavkom prihvatate uslove o zaštiti privatnosti.",
        "privacy_archor" => "Pogledaj izjavu o zaštiti privatnosti",
        "rights_reserved" => "Sva prava pridržana - JOBSTEP",
        "direction_number" => "Broj direkcije",
        "makler_id" => "VM-broj",
        "required_field" => "Ispunite ovo polje",
        "invalid_value" => "Neispravna vrednost",
        "valid_phone" => "Validan broj",
        "invalid_phone" => "Pogrešan broj",
        "invalid_country_code" => "Netačan pozivni broj",
        "number_too_long" => "Broj je predug",
        "number_too_short" => "Broj je prekratak",
        "privacy_title" => "Izjava o zaštiti privatnosti",
        "privacy_body" => "Hvala vam na interesovanju za apliciranje kod JOBSTEP. Zaštita vaših ličnih podataka nam je važna. Niže želimo da vas informišemo o tome kako postupamo sa vašim podacima:
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
										Hvala vam na poverenju u JOBSTEP.",
        "privacy_close" => "Zatvori",
        "de" => "Njemački",
        "en" => "Engleski",
        "sr" => "Srpski",
        "hr" => "Hrvatski",
        "ba" => "Bosanski",
        "language" => "Jezik",
        "email_unavailable" => "Vaša e-mail adresa nije na listi postojećih partnera.",
        "email_taken" => "Email adresa zauzeta",
        "back_to_home" => "Nazad na početnu stranicu",
    ),
    "hr" => array(
        "header" => "Kreiraj profil za Partner App",
        "subheader" => "Popuni sva obavezna polja i registriraj se!",
        "register" => "Registriraj se",
        "firstname" => "Ime",
        "lastname" => "Prezime",
        "email" => "Email",
        "phone_number" => "Mobilni telefon",
        "os" => "Operativni sistem mobilnog telefona",
        "required" => "Sva polja označena sa * su obavezna",
        "privacy_statement" => "Nastavkom prihvatate uslove o zaštiti privatnosti.",
        "privacy_archor" => "Pogledaj izjavu o zaštiti privatnosti",
        "rights_reserved" => "Sva prava pridržana - JOBSTEP",
        "direction_number" => "Broj direkcije",
        "makler_id" => "VM-broj",
        "required_field" => "Ispunite ovo polje",
        "invalid_value" => "Neispravna vrijednost",
        "valid_phone" => "Validan broj",
        "invalid_phone" => "Pogrešan broj",
        "invalid_country_code" => "Netočan pozivni broj",
        "number_too_long" => "Broj je predug",
        "number_too_short" => "Broj je prekratak",
        "privacy_title" => "Izjava o zaštiti privatnosti",
        "privacy_body" => "Hvala vam na interesovanju za apliciranje kod JOBSTEP. Zaštita vaših ličnih podataka nam je važna. Niže želimo da vas informišemo o tome kako postupamo sa vašim podacima:
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
										Hvala vam na poverenju u JOBSTEP.",
        "privacy_close" => "Zatvori",
        "de" => "Njemački",
        "en" => "Engleski",
        "sr" => "Srpski",
        "hr" => "Hrvatski",
        "ba" => "Bosanski",
        "language" => "Jezik",
        "email_unavailable" => "Vaša e-mail adresa nije na listi postojećih partnera.",
        "email_taken" => "Email adresa zauzeta",
        "back_to_home" => "Natrag na početnu stranicu",
    ),
);

function translateText($key)
{
    global $language;
    global $translations;


    echo $translations[$language][$key];
}



include("includes/functions.php");

?>
<style>
    body {
        background-color: #FFFFFF;
        color: #344054;
    }

    .anchor_js {
        color: #80ACB3;
    }

    .anchor_js:hover {
        color: #80ACB3;
    }

    .anchor_dvag {
        color: #C8AA22;
    }

    .anchor_dvag:hover {
        color: #C8AA22;
    }

    .logo_container {
        margin-top: 24px;
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

    .form_input_custom {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    @media (max-width: 991px) {

        .input,
        .select-posao {
            width: 100% !important;
        }
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
        border: 1px solid #D0D5DD;
    }

    .nastavi {
        background: #6097A0;
        color: #FFFFFF;
        font-weight: bold;
        margin-top: 10px;
        border: 0;
        border-radius: 6px;
        height: 44px;
    }

    .nastavi_dvag {
        background: #C8AA22;
        color: #FFFFFF;
        font-weight: bold;
        margin-top: 10px;
        border: 0;
        border-radius: 6px;
        height: 44px;
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
    }

    .custom-select select>option {
        color: #101828;
    }

    .material-radio-group__caption {
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

    .error-message {
        color: red;
        font-size: 14px;
        margin-top: 5px;
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



    #content_public {
        background-image: url('<?php getSiteUrl(); ?>/images/partner_app_registration.png');
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
        z-index: 1;
    }



    #main_content {
        background-color: #FFFFFF;
        margin-top: 2%;
        margin-bottom: 200px;
        border-radius: 16px;
        z-index: 3;
    }

    .select_js {
        border: 1px solid #D0D5DD;
        border-radius: 6px;
    }

    .select_js:focus {
        outline: none;
        border: 1px solid #80ACB3;
        box-shadow: 0px 1px 2px rgba(16, 24, 40, 0.05), 0px 0px 0px 4px #EDF4F5;
    }

    .select_dvag {
        border: 1px solid #D0D5DD;
        border-radius: 6px;
    }

    .select_dvag:focus {
        outline: none;
        border: 1px solid #C8AA22;
        box-shadow: 0px 1px 2px rgba(16, 24, 40, 0.05), 0px 0px 0px 4px #FDFAEC;
    }

    .backdrop_custom {
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0;
        right: 0;
        opacity: 40%;
        z-index: 2;
        background-color: black;
    }

    .value_invalid {
        border: 2px solid red;
        box-shadow: 0px 1px 2px rgba(16, 24, 40, 0.05), 0px 0px 0px 4px #FEE4E2;
    }

    .form-custom-label {
        font-size: 12px;
        font-weight: bold !important;
    }

    .text_required {
        font-size: 11px;
    }

    @media (min-width: 2000px) {
        body {
            font-size: large;
        }

        #content_public {
            height: 110vh;
        }

        .error-message {
            font-size: 19px;
        }

        .material-radio-group__caption {
            font-size: 17px;
        }

        .custom-select {
            font-size: 20px;
        }

        .select-posao {
            width: 100%;
            height: 62px;
            border: 1px solid rgb(0 0 0 / 74%);
            background-color: #FFFFFF;
            padding: 5px;
            font-size: 24px;
        }

        .input {
            width: 100%;
            height: 62px;
            padding: 5px;
            border: 1px solid #D0D5DD;
        }

        .form-custom-label {
            font-size: 17px;
        }

        .text_required {
            font-size: 15px;
        }

    }
</style>

<body>
    <div id="content_public" class="col-md-12 col-xs-12 col-sm-12 col-lg-12">
        <div class="backdrop_custom"></div>
        <div id="main_content" class="col-md-offset-3 col-md-6  col-xs-12  col-sm-offset-2 col-sm-8 col-lg-offset-4 col-lg-4">
            <div class="row logo_container">
                <div id="JS_logo" class="col-md-12 text-center">
                    <a href="https://job-step.com">
                        <svg width="55" height="55" viewBox="0 0 49 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19.5531 6.5357C21.0277 6.5357 22.2231 5.28753 22.2231 3.74783C22.2231 2.20813 21.0277 0.959961 19.5531 0.959961C18.0786 0.959961 16.8832 2.20813 16.8832 3.74783C16.8832 5.28753 18.0786 6.5357 19.5531 6.5357Z" fill="#6097A0" />
                            <path d="M44.3982 20.659C41.2428 23.1267 33.4433 26.3447 27.118 23.263C18.7338 19.1781 16.6477 10.8754 16.6607 7.54953C16.6737 4.22364 13.4056 18.5593 24.003 25.2905C33.9829 31.6295 42.6862 24.6599 44.6349 20.8823C44.7265 20.7046 44.5474 20.5423 44.3982 20.659Z" fill="#6097A0" />
                            <path d="M4.66963 46.915C6.49379 42.1581 11.7416 33.5458 20.2206 32.3868C31.3454 30.8662 37.5753 39.4832 39.3553 43.0314C41.1352 46.5796 36.6853 27.8249 21.778 28.0783C7.58929 28.3195 4.08351 41.648 4.35408 46.8659C4.36475 47.0716 4.5972 47.1038 4.66963 46.915Z" fill="#6097A0" />
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M32.4578 22.2492C35.6527 22.2492 38.2427 19.6393 38.2427 16.42C38.2427 13.2006 35.6527 10.5908 32.4578 10.5908C29.2629 10.5908 26.6729 13.2006 26.6729 16.42C26.6729 19.6393 29.2629 22.2492 32.4578 22.2492ZM30.2329 16.6735C31.4617 16.6735 32.4579 15.6522 32.4579 14.3925C32.4579 13.1327 31.4617 12.1115 30.2329 12.1115C29.0041 12.1115 28.0079 13.1327 28.0079 14.3925C28.0079 15.6522 29.0041 16.6735 30.2329 16.6735Z" fill="#6097A0" />
                        </svg>
                    </a>
                </div>
                <div id="DV_logo" style="display: none;" class="col-xs-6 col-sm-6 col-ms-6 col-lg-6 text-left">
                    <svg width="55" height="55" viewBox="0 0 49 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M41.6625 10.3411L37.9437 18.058C37.9086 18.1317 37.9033 18.2158 37.9314 18.293C38.4278 19.7115 38.6997 21.23 38.6997 22.8134C38.6997 30.5023 32.3426 36.7358 24.4998 36.7358C16.657 36.7358 10.2999 30.5023 10.2999 22.8134C10.2999 21.2353 10.5701 19.7203 11.063 18.307C11.0893 18.2299 11.0858 18.1475 11.0507 18.0738L7.34945 10.3271C7.24771 10.1149 6.95827 10.0903 6.81969 10.2797C4.21653 13.8007 2.68164 18.1317 2.68164 22.8134C2.68164 34.6281 12.4505 44.2055 24.4998 44.2055C36.5491 44.2055 46.318 34.6281 46.318 22.8134C46.318 18.1387 44.7866 13.8129 42.1905 10.2937C42.0536 10.1044 41.7642 10.1289 41.6625 10.3411Z" fill="#C8AA22" />
                        <path d="M28.7415 34.8649L41.1855 9.03133C39.5945 7.18144 37.6877 5.59808 35.5477 4.36365L24.5017 27.3146L13.7154 4.83357C13.5891 4.5688 13.2611 4.47587 13.0085 4.62842C11.1719 5.74537 9.51775 7.12182 8.09864 8.70694C7.96708 8.85423 7.93726 9.06991 8.02321 9.24876L20.3269 34.9947C20.3462 35.035 20.383 35.0666 20.4268 35.0788C25.242 36.4571 28.2591 35.1349 28.6714 34.9368C28.7029 34.921 28.7275 34.8965 28.7415 34.8649Z" fill="#C8AA22" />
                    </svg>
                </div>
            </div>
            <div id="main_form" class="form-group list-inline col-md-offset-1 col-md-10 col-xs-offset-1 col-xs-10 col-sm-offset-1 col-sm-10">
                <div class="row">
                    <div class="col-md-5 col-sm-6 col-xs-6 col-lg-4" style="padding-left: 5px;">
                        <label for="odabir_jezika" class="col-sm-3 control-label list-inline" style="padding-left: 0;"><?php translateText("language") ?></label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-5 col-sm-6 col-xs-6 col-lg-4" style="padding-left: 0;">
                        <form method="" action="" style="display:flex;" autocomplete="off">
                            <div class="custom-select">
                                <select autocomplete="off" class="select-lang pull-right select_js" id="odabir_jezika" name="lang" data-live-search="true" onchange="changeLang(this)" required>
                                    <option value="de" <?php if ($language == 'de') {
                                                            echo "selected";
                                                        } else {
                                                            echo "";
                                                        } ?>><?php translateText("de"); ?></option>
                                    <option value="en" <?php if ($language == 'en') {
                                                            echo "selected";
                                                        } else {
                                                            echo "";
                                                        } ?>><?php translateText("en"); ?></option>
                                    <option value="ba" <?php if ($language == 'ba') {
                                                            echo "selected";
                                                        } else {
                                                            echo "";
                                                        } ?>><?php translateText("ba"); ?></option>
                                    <option value="sr" <?php if ($language == 'sr') {
                                                            echo "selected";
                                                        } else {
                                                            echo "";
                                                        } ?>><?php translateText("sr") ?></option>
                                    <option value="hr" <?php if ($language == 'hr') {
                                                            echo "selected";
                                                        } else {
                                                            echo "";
                                                        } ?>><?php translateText("hr"); ?></option>
                                </select>
                            </div>
                        </form>
                        <script>
                            function changeLang(thisRow) {

                                var lang = thisRow.value;
                                window.location.href = "<?php getSiteUrl(); ?>" + "partner/signup/" + lang;

                            }
                            (function() {
                                window.onpageshow = function(event) {
                                    if (event.persisted) {
                                        window.location.reload();
                                    }
                                };
                            })()
                        </script>
                    </div>
                </div>
                <br>

                <div class="row" id="makler_signup_form">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12 text-left" style="padding-left: 0; margin-top: -10px;">
                        <h3><strong><?php translateText("header"); ?></strong></h3>
                        <p><?php translateText("subheader"); ?></p>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="">
                        <form method="post" enctype="multipart/form-data" class="form-horizontal" role="form" id="main_form">
                            <div class="form-group form_input_custom">
                                <div class="col-md-12 col-xs-12 col-sm-12">
                                    <label for="first_name" class="control-label form-custom-label"><?php translateText("firstname"); ?><span>*</span><strong>:</strong></label>
                                    <div class="col-md-12 col-xs-12 col-sm-12" style="padding: 0;">
                                        <input class="input select_js" type="text" name="first_name" id="first_name" placeholder="<?php translateText("firstname"); ?>" required>
                                        <div id="firstname_error_message" class="error-message"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group form_input_custom">
                                <div class="col-md-12 col-xs-12 col-sm-12">
                                    <label for="last_name" class="control-label form-custom-label"><?php translateText("lastname"); ?><span>*</span><strong>:</strong></label>
                                    <div class="col-md-12  col-xs-12 col-sm-12" style="padding: 0;">
                                        <input class="input select_js" type="text" name="last_name" id="last_name" placeholder="<?php translateText("lastname"); ?>" required>
                                        <div id="lastname_error_message" class="error-message"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group form_input_customi">
                                <div class="col-md-12 col-xs-12 col-sm-12">
                                    <label for="email" class=" control-label form-custom-label"><?php translateText("email"); ?><span>*</span><strong>:</strong></label>
                                    <div class="col-md-12 col-xs-12 col-sm-12" style="padding: 0;">
                                        <input class="input select_js" type="email" name="email" id="email" placeholder="<?php translateText("email"); ?>" required>
                                        <div id="email_error_message" class="error-message"></div>
                                    </div>
                                </div>
                            </div>
                            <div id="makler_id_view" style="display: none;" class="form-group form_input_custom">
                                <div class="col-md-12 col-xs-12 col-sm-12 ">
                                    <label for="makler_id" class="control-label form-custom-label"><?php translateText("makler_id"); ?><span>*</span><strong>:</strong></label>
                                    <div class="col-md-12 col-xs-12 col-sm-12" style="padding: 0;">
                                        <input class="input select_js" type="number" name="makler_id" id="makler_id" placeholder="<?php translateText("makler_id"); ?>" required>
                                        <div id="maklerid_error_message" class="error-message"></div>
                                    </div>
                                </div>
                            </div>
                            <div id="directorate_number_view" style="display: none;" class="form-group form_input_custom">
                                <div class="col-md-12">
                                    <label for="directorate_number" class="control-label form-custom-label"><strong><?php translateText("direction_number"); ?></strong><span>*</span><strong>:</strong></label>
                                    <div class="col-md-12 col-xs-12 col-sm-12" style="padding: 0;">
                                        <input class="input select_js" type="number" name="directorate_number" id="directorate_number" placeholder="<?php translateText("direction_number"); ?>" required>
                                        <div id="directorate_error_message" class="error-message"></div>
                                    </div>
                                </div>
                            </div>
                            <script>
                                $(document).ready(function() {
                                    $('#directorate_number').on('input', function() {
                                        $('#directorate_number').removeClass('value_invalid');
                                        $('#directorate_error_message').text('');
                                    });
                                    $('#makler_id').on('input', function() {
                                        $('#makler_id').removeClass('value_invalid');
                                        $('#maklerid_error_message').text('');
                                    });
                                    $('#first_name').on('input', function() {
                                        $('#first_name').removeClass('value_invalid');
                                        $('#firstname_error_message').text('');
                                    });
                                    $('#last_name').on('input', function() {
                                        $('#last_name').removeClass('value_invalid');
                                        $('#lastname_error_message').text('');
                                    });

                                    $('#email').on('input', function() {
                                        var email = $(this).val();
                                        var pattern = /^.+@dvag\.de$/;
                                        var dvLogoDiv = $('#DV_logo');
                                        var makId = $('#makler_id_view');
                                        var dirNum = $('#directorate_number_view');

                                        $('#email').removeClass('value_invalid');
                                        $('#email_error_message').text('');
                                        if (pattern.test(email) || (email.endsWith('m.abdijanovic@jobstep.com') || email.endsWith('b.bender@jobstep.com'))) {
                                            //logo change
                                            $('#JS_logo').addClass('JS_logo');
                                            $('#JS_logo').addClass('col-xs-6');
                                            $('#JS_logo').addClass('col-sm-6');
                                            $('#JS_logo').addClass('col-md-6');
                                            $('#JS_logo').addClass('col-lg-6');
                                            $('#JS_logo').addClass('text-right');
                                            $('#JS_logo').removeClass('col-xs-12');
                                            $('#JS_logo').removeClass('col-sm-12');
                                            $('#JS_logo').removeClass('col-md-12');
                                            $('#JS_logo').removeClass('col-lg-12');
                                            $('#JS_logo').removeClass('text-center');

                                            //outline on inputs and selects
                                            $('#odabir_jezika').addClass('select_dvag');
                                            $('#odabir_jezika').removeClass('select_js');
                                            $('#first_name').addClass('select_dvag');
                                            $('#first_name').removeClass('select_js');
                                            $('#last_name').addClass('select_dvag');
                                            $('#last_name').removeClass('select_js');
                                            $('#email').addClass('select_dvag');
                                            $('#email').removeClass('select_js');
                                            $('#makler_id').addClass('select_dvag');
                                            $('#makler_id').removeClass('select_js');
                                            $('#directorate_number').addClass('select_dvag');
                                            $('#directorate_number').removeClass('select_js');

                                            //other elements
                                            $('#sign_up_title').addClass('sign_up_dvag');
                                            $('#sign_up_title').removeClass('sign_up');
                                            $('#button_nastavi').addClass('nastavi_dvag');
                                            $('#button_nastavi').removeClass('nastavi');
                                            $('#privacy_anchor').addClass('anchor_dvag');
                                            $('#privacy_anchor').removeClass('anchor_js');

                                            //show elements for dvag
                                            dvLogoDiv.css('display', 'block');
                                            makId.css('display', 'block');
                                            dirNum.css('display', 'block');

                                            //reset validation
                                            $('#email').removeClass('value_invalid');
                                            $('#email_error_message').text('');
                                            $('#makler_id').removeClass('value_invalid');
                                            $('#maklerid_error_message').text('');
                                            $('#directorate_number').removeClass('value_invalid');
                                            $('#directorate_error_message').text('');

                                        } else {
                                            //logo change
                                            $('#JS_logo').removeClass('JS_logo');
                                            $('#JS_logo').addClass('col-xs-12');
                                            $('#JS_logo').addClass('col-sm-12');
                                            $('#JS_logo').addClass('col-md-12');
                                            $('#JS_logo').addClass('col-lg-12');
                                            $('#JS_logo').addClass('text-center');
                                            $('#JS_logo').removeClass('col-xs-6');
                                            $('#JS_logo').removeClass('col-sm-6');
                                            $('#JS_logo').removeClass('col-md-6');
                                            $('#JS_logo').removeClass('col-lg-6');
                                            $('#JS_logo').removeClass('text-right');

                                            //inputs and selects change
                                            $('#odabir_jezika').addClass('select_js');
                                            $('#odabir_jezika').removeClass('select_dvag');
                                            $('#first_name').removeClass('select_dvag');
                                            $('#first_name').addClass('select_js');
                                            $('#last_name').removeClass('select_dvag');
                                            $('#last_name').addClass('select_js');
                                            $('#email').removeClass('select_dvag');
                                            $('#email').addClass('select_js');
                                            $('#makler_id').removeClass('select_dvag');
                                            $('#makler_id').addClass('select_js');
                                            $('#makler_id').val('');
                                            $('#directorate_number').val('');
                                            $('#directorate_number').removeClass('select_dvag');
                                            $('#directorate_number').addClass('select_js');
                                            $('#back-to-home').addClass('back-to-home');
                                            $('#back-to-home').removeClass('back-to-home-dvag');

                                            //other elements
                                            $('#sign_up_title').addClass('sign_up');
                                            $('#sign_up_title').removeClass('sign_up_dvag');
                                            $('#button_nastavi').removeClass('nastavi_dvag');
                                            $('#button_nastavi').addClass('nastavi');
                                            $('#privacy_anchor').removeClass('anchor_dvag');
                                            $('#privacy_anchor').addClass('anchor_js');

                                            //hide dvag elements
                                            dvLogoDiv.css('display', 'none');
                                            makId.css('display', 'none');
                                            dirNum.css('display', 'none');

                                        }
                                    });
                                });
                            </script>
                            <br />
                            <div class="form-group">
                                <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                                    <div>
                                        <button id="button_nastavi" type="button" class="nastavi col-sm-12 col-xs-12 col-md-12 col-lg-12"><?php translateText("register"); ?></button>
                                        <button id="button_back" type="button" style="color: #344054; margin-top: 10px; border: 1px solid #D0D5DD; border-radius: 6px; height: 44px; background: transparent; font-weight: bold;" class="col-sm-12 col-xs-12 col-md-12 col-lg-12"><?php translateText("back_to_home"); ?></button>
                                    </div>
                                    <p style="margin-top: 110px;" class="text_required"><?php translateText("required"); ?></p>
                                </div>
                            </div>
                            <script>
                                $(document).ready(function() {
                                    $('#button_back').on('click', function() {
                                        window.location.href = '/';
                                    });

                                    function getToken() {
                                        var url = window.location.href;
                                        var parsedUrl = new URL(url);
                                        var pathname = parsedUrl.pathname;
                                        var parts = pathname.split('/');
                                        var token = parts[2];

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
                                    $('#button_nastavi').on('click', function() {
                                        var maklerFirstnameInput = document.getElementById('first_name');
                                        var maklerLastnameInput = document.getElementById('last_name');
                                        var maklerEmailInput = document.getElementById('email');
                                        var maklerInternalIdInput = document.getElementById('makler_id');
                                        var maklerDirectionIdInput = document.getElementById('directorate_number');

                                        var maklerFirstnameValue = maklerFirstnameInput.value;
                                        var maklerLastnameValue = maklerLastnameInput.value;
                                        var maklerEmailValue = maklerEmailInput.value;
                                        var maklerInternalIdValue = maklerInternalIdInput.value;
                                        var maklerDirectionIdValue = maklerDirectionIdInput.value;
                                        var checked = true;

                                        if (!maklerEmailValue.endsWith('@dvag.de') && maklerEmailValue != 'm.abdijanovic@jobstep.com' && maklerEmailValue != 'b.bender@jobstep.com') {
                                            $('#email').removeClass('value_invalid');
                                            $('#email_error_message').text('');
                                            $('#email').addClass('value_invalid');
                                            $('#email_error_message').text('<?php translateText("email_unavailable"); ?>');
                                            checked = false;
                                        } else if ((maklerEmailValue.trim() === "" || !/^.+@dvag\.de$/.test(maklerEmailValue)) && maklerEmailValue != 'm.abdijanovic@jobstep.com' && maklerEmailValue != 'b.bender@jobstep.com') {
                                            $('#email').removeClass('value_invalid');
                                            $('#email_error_message').text('');
                                            $('#email').addClass('value_invalid');
                                            $('#email_error_message').text('<?php translateText("invalid_value"); ?>');
                                            checked = false;
                                        } else {
                                            $('#email').removeClass('value_invalid');
                                            $('#email_error_message').text('');
                                        }

                                        if (maklerFirstnameValue.trim() === "") {
                                            $('#first_name').addClass('value_invalid');
                                            $('#firstname_error_message').text('<?php translateText("invalid_value"); ?>');
                                            checked = false;
                                        } else {
                                            $('#first_name').removeClass('value_invalid');
                                            $('#firstname_error_message').text('');
                                        }
                                        if (maklerLastnameValue.trim() === "") {
                                            $('#last_name').addClass('value_invalid');
                                            $('#lastname_error_message').text('<?php translateText("invalid_value"); ?>');
                                            checked = false;
                                        } else {
                                            $('#last_name').removeClass('value_invalid');
                                            $('#lastname_error_message').text('');
                                        }
                                        if (maklerInternalIdValue.trim() === "") {
                                            $('#makler_id').addClass('value_invalid');
                                            $('#maklerid_error_message').text('<?php translateText("invalid_value"); ?>');
                                            checked = false;
                                        } else {
                                            $('#makler_id').removeClass('value_invalid');
                                            $('#maklerid_error_message').text('');
                                        }
                                        if (maklerDirectionIdValue.trim() === "") {
                                            $('#directorate_number').addClass('value_invalid');
                                            $('#directorate_error_message').text('<?php translateText("invalid_value"); ?>');
                                            checked = false;
                                        } else {
                                            $('#directorate_number').removeClass('value_invalid');
                                            $('#directorate_error_message').text('');
                                        }
                                        if (checked) {
                                            $.ajax({
                                                type: 'POST',
                                                url: getSubdomain() + 'api.pa.job-step.com/?request=register',
                                                data: {
                                                    "first_name": maklerFirstnameValue.trim(),
                                                    "last_name": maklerLastnameValue.trim(),
                                                    "email": maklerEmailValue.trim(),
                                                    "makler_id": maklerInternalIdValue.trim(),
                                                    "directorate_number": maklerDirectionIdValue.trim(),
                                                },
                                                success: function(response) {
                                                    if (response["message"] === 'success') {
                                                        window.location.href = '/partner/confirmationsent/3/<?php echo $language; ?>';
                                                    } else if (response === 'mail_duplicate') {

                                                    } else {
                                                        alert('Error processing form.');
                                                    }
                                                },
                                                error: function(xhr, status, error) {
                                                    if (xhr.responseText === 'account_exists') {
                                                        $('#email').removeClass('value_invalid');
                                                        $('#email_error_message').text('');
                                                        $('#email').addClass('value_invalid');
                                                        $('#email_error_message').text('<?php translateText("email_taken"); ?>');
                                                    } else if (xhr.responseText === 'mail_not_sent') {
                                                        alert('Anmeldeinformationen nicht gesendet.');
                                                    } else if (xhr.responseText === 'is_not_dvag') {
                                                        $('#email').removeClass('value_invalid');
                                                        $('#email_error_message').text('');
                                                        $('#email').addClass('value_invalid');
                                                        $('#email_error_message').text('<?php translateText("email_unavailable"); ?>');
                                                    } else {
                                                        alert('Error processing form.');
                                                    }
                                                    console.log(xhr.responseText);
                                                }
                                            });
                                        }
                                    });
                                });
                            </script>

                            <div class="form-group">
                                <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12 text-center" style="padding-top: 20px;">
                                    <p class=""> <?php translateText("privacy_statement"); ?>
                                        <br><a href="" id="privacy_anchor" class="anchor_js" type="button" data-toggle="modal" data-target="#privacyModal"><?php translateText("privacy_archor"); ?> </a>
                                    </p>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>

                <footer style="margin-top: 10px;">
                    <?php
                    echo "<p>©" . date('Y') . " " . $translations[$language]["rights_reserved"] . "</p>";
                    ?>
                </footer>
            </div>
        </div>
    </div>
    <div>
        <div class="modal fade modal-fullscreen" id="privacyModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="col-md-offset-3 col-md-6  col-xs-12  col-sm-offset-2 col-sm-8 col-lg-offset-4 col-lg-4">
                <div class="modal-dialog" role="document">
                    <div class="modal-content" style="padding: 30px;">
                        <div class="modal-header" style="display: flex;border-bottom: none;">
                            <h5 class="modal-title" style="width: 90%; padding: 0; border-left: none; font-weight: bold;" id="exampleModalLabel"><?php translateText("privacy_title"); ?></h5>
                            <button class="close material-modal__close" style="width: 10%;" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <p><?php translateText("privacy_body"); ?></p>
                        </div>
                        <div class="modal-footer" style="border-top: none;">
                            <button type="button" style="color: #344054; margin-top: 10px; border: 1px solid #D0D5DD; border-radius: 6px; height: 44px; background: transparent; font-weight: bold;" class="btn col-xs-12" data-dismiss="modal"><?php translateText("privacy_close"); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>