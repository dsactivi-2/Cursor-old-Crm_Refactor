<?php 

//Site URL
function getSiteUrl() {
    echo "https://crm.job-step.com/";
}

$kandidat_id = $_REQUEST['id'];
$kandidat_check = $_REQUEST['check'];
$kandidat_lang = $_REQUEST['lang'];

if($kandidat_lang == "de"){
    $txt_reg_ty_1 = "Danke Ihnen! Ihre Daten sind erforgleich zugesendet.";
    $txt_reg_ty_2 = "Finalisieren Sie Ihre Bewerbung!";
    $txt_reg_ty_3 = "Sehen Sie sich das Video an, laden Sie die App herunter und vollenden Sie Ihre Bewerbung. Der richtige Job wartet schon auf Sie.";
    $txt_reg_ty_4 = "Sie sind interessiert für die Anerkennung Ihres Diploms.";
    $txt_reg_ty_6 = "Die Jobstep Partner App ist eine App für  Ausendienstmitarbeitervon Jobstep. Sie können unser Partner werden, indem Sie sich über die Partner App bewerben.Empfehlen Sie Jobstep und verdienen Sie!";
    $txt_reg_ty_5 = "JobStep übernimmt für Sie denn kompletten Anerkennungsprocesses und notwendige administrative Maßnahmen! Mit unseren professionellen Mitarbeitern und einem  dafür kreierten Programm sind Sie nur einen Schritt von Ihrer Karriere in Deutschland entfernt.";
    $txt_reg_ty_7 = "Nähert sich die Visumsfrist? Sie haben die Unterlagen gesammelt, haben aber das Gefühl, dass Ihnen noch etwas fehlt? Haben Sie es satt, lange Schlange zu stehen oder sich in die freien Termine von Agenturen einzufügen, die sich mit dem Ausfüllen von Visa-Anträgen befassen? Jobstep bietet Ihnen eine einfache Lösung. Laden Sie den kostenlosen Antrag zum Ausfüllen eines Visumantrags herunter, füllen Sie die grundlegenden Informationen aus und laden Sie den ausgefüllten Visumantrag in einer unserer Filialen herunter.";
    $txt_prijavi_se = "ANMELDUNG";

}else{
    $txt_reg_ty_1 = "Hvala Vam! Vaši podaci su uspješno poslani.";
    $txt_reg_ty_2 = "Finalizirajte Vašu prijavu!";
    $txt_reg_ty_3 = "Pogledajte video ispod, preuzmite našu aplikaciju, finalizirajte prijavu i pronađite pravi posao za Vas.";
    $txt_reg_ty_4 = "Zainteresovani ste za nostrifikaciju diplome?";
    $txt_reg_ty_5 = "JobStep za Vas preuzima cjelokupnu brigu oko nostrifikacije i potrebnih administrativnih radnji! Uz naše stručno osoblje, te za ovu svrhu posebno kreiran program, ste samo na korak do Vaše karijere u Njemačkoj.";
    $txt_reg_ty_6 = "Jobstep Partner App je aplikacija za vanjske saradnike Jobstepa. Svako može postati naš partner prijavom na ovu aplikaciju. Preporuči Jobstep i zaradi!";
    $txt_reg_ty_7 = "Bliži Vam se termin za vizu? Prikupili ste dokumentaciju ali imate osjećaj da Vam još uvijek nešto nedostaje? Dosadilo Vam je dugo čekanje u redu ili uklapanje u slobodne termine agencija koje se bave popunjavanjem zahtjeva za vizu? Jobstep nudi jednostavno rješenje za Vas. Preuzmite besplatnu aplikaciju za popunjavanje zahtjeva za vizu, ispunite osnove podatke i preuzmite popunjen zahtjev za vizu u nekoj od naših poslovnica.";
    $txt_prijavi_se = "PRIJAVI SE";

}
// echo $kandidat_id." ".$kandidat_check." ".$kandidat_lang;
$old_stuff = 1;
?>

<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Jobstep Thank You Page</title>

    <link href="https://crm.job-step.com/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="https://crm.job-step.com/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <style>
        @import url("https://fonts.googleapis.com/css?family=Open+Sans:300,400,700&amp;subset=latin-ext");

        body {
            font-family: "Open Sans", sans-serif;
            padding-top: 30px;
        }

        .full-screen {
            padding: 6rem 0;
        }

        .small-text {
            color: #5b5b5b;
            font-size: 14px;
            font-weight: bold;
            padding: 8px 16px;
            border-radius: 50px;
            letter-spacing: 0.2px;
        }

        ul {
            margin: 0;
            padding: 0;
        }

            ul li {
                list-style: none;
            }

        a {
            font-weight: normal;
            text-decoration: none !important;
            transition: all 0.4s ease;
        }

            a:hover {
                color: #6097A0 !important;
            }

        .navbar-brand .uil {
            font-size: 40px;
        }

        p {
            font-size: 18px;
            font-weight: 300;
            line-height: 1.5;
            color: #5b5b5b;
        }

        h1, h2, h3, h4, h5, h6 {
            font-weight: bold;
            letter-spacing: -1px;
        }

        h1 {
            color: #212121;
            font-size: 2.8em;
            margin: 24px 0;
        }

        h2 {
            color: #353535;
            font-size: 2.4em;
            font-weight: bold;
        }

        h3 {
            color: #484848;
        }

        h3,
        b, strong {
            font-weight: bold;
        }
    </style>


</head>
<body style="width: 100%; display:flex; flex-direction: column;" >
    <div class="container" style="flex: 1; display:flex; flex-direction: column; justify-content: center; align-items:center; text-align:center; ">
    
        <div class="row">
            <div class="col-xs-12 text-center">
                <img class="img-responsive" src="<?php getSiteURL(); ?>images/Jobstep_logo_new.png" alt="" style="max-width:200px; text-align: center !important; margin: 0px auto;">
            </div>
            <div class="col-xs-12">
                <br />
            </div>
        </div>
    
        
        <section class="justify-content-center align-items-center">
            <div style="margin-top:0px;">
                <div class="row">
                    <div class="col-lg-12" align="center" style="padding-bottom:20px;">
                        <img style="padding-top:10px;" width="40" height="50" src="<?php getSiteURL(); ?>images/thankyoupage/uspjesno.png"  />
                        <p><?php echo $txt_reg_ty_1; ?></p>
                    </div>

                </div>

                <?php if($old_stuff != 1){ ?>
                    <!--You Tube tutorijal i Jobstep Messenger-->
                    <div class="row">

                        <div class="col-lg-7 col-md-12 col-12 d-flex align-items-center">
                            <div class="about-text">
                                <h2><?php echo $txt_reg_ty_2; ?></h2>
                                <p><?php echo $txt_reg_ty_3; ?></p>
                                <div style=" position: relative; overflow: hidden; width: 100%; padding-top: 56.25%;">
                                    <iframe style="position: absolute; top: 0; left: 0; bottom: 0; right: 0; width: 100%; height: 100%;" src="https://www.youtube.com/embed/rttgZz8ohEI"></iframe>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-5" align="center" style="padding-top:30px;">
                            <img src="<?php getSiteURL(); ?>images/thankyoupage/jobstep_messenger.png" />
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-lg-12" align="center">
                            <div class="custom-btn-group mt-4" style="margin-top:0px;">
                                <a href="https://play.google.com/store/apps/details?id=net.job_step.jobstepmessenger"><img src="<?php getSiteURL(); ?>images/thankyoupage/google_play.png" style="margin-bottom:10px;" /></a>
                                <a href="https://apps.apple.com/tt/app/jobstep-messenger/id1486805317"><img src="<?php getSiteURL(); ?>images/thankyoupage/app_store.png" style="margin-bottom:10px;" /></a>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </section>

        <?php if($old_stuff != 1){ ?>
            <!--Nostrifikacija diploma-->
            <section>
                <div class="row">
                    <div class="col-lg-11 text-center mx-auto col-12">
                        <div class="col-lg-12 mx-auto" style="padding-top:60px;">
                            <h2><?php echo $txt_reg_ty_4; ?></h2>
                        </div>

                    </div>

                </div>
                <div class="row" style="padding-bottom:60px;">
                    <div class="col-lg-12" align="center" style="padding-top:20px;">
                        <p><?php echo $txt_reg_ty_5; ?></p>
                        <a href="https://job-step.net/usluga/priznavanje-diplome/3"><button type="button" class="btn btn-outline-info" style="border-radius: 50px; padding: 10px 50px 10px 50px; font-size: 20px;"><?php echo $txt_prijavi_se; ?></button></a>
                    </div>
                </div>
            </section>

            <!--Jobstep Partner App-->
            <section class="justify-content-center align-items-center">
                <div class="container" style="margin-top:0px;">
                    <div class="row">

                        <div class="col-lg-7" align="center">
                            <img width="300" height="550" src="<?php getSiteURL(); ?>images/thankyoupage/jobstep_partner_app.png" />
                        </div>

                        <div class="col-lg-5" align="center" style="padding-top:30px;">
                            <div class="custom-btn-group mt-4 " style="padding-top:20px;">
                                <h2>Jobstep Partner App</h2>
                                <p><?php echo $txt_reg_ty_6; ?></p>
                                <a href="https://play.google.com/store/apps/details?id=com.partnerjobstep"><img width="220" height="65" src="<?php getSiteURL(); ?>images/thankyoupage/google_play_crna.png" style="margin-bottom:10px;" alt="Android - Jobstep Messenger" /></a>
                                <a href="https://apps.apple.com/tt/app/jobstep-partner-app/id1498761708?ign-mpt=uo%3D2"><img width="220" height="65" src="<?php getSiteURL(); ?>images/thankyoupage/app_store_crna.png" style="margin-bottom:10px;" alt="iOS - Jobstep Messenger" /></a>
                            </div>
                        </div>

                    </div>

                </div>
            </section>

            <!--Footer - Društvene mreže-->
            <footer class="text-center text-white" style="background-color: #f1f1f1; margin-top:30px;">
                <div class="container pt-4">
                    <p>Zapratite nas na:</p>
                    <section class="mb-4">
                        <a href="https://www.facebook.com/JobStepInternational" alt="Jobstep Facebook"> <img width="35" height="35" src="<?php getSiteURL(); ?>images/thankyoupage/facebook_logo.png" /></a>
                        <a href="https://www.instagram.com/jobstepinternational/" alt="Jobstep Instagram"> <img width="35" height="35" src="<?php getSiteURL(); ?>images/thankyoupage/instagram_logo.png" /> </a>
                        <a href="https://www.linkedin.com/in/jobstepint/" alt="Jobstep LinkedIn" role="button" data-mdb-ripple-color="dark"> <img width="35" height="35" src="<?php getSiteURL(); ?>images/thankyoupage/linked_in_logo.png" /></a>
                        <a href=" https://www.youtube.com/channel/UChL-bdalb8qxtYckAVZkXZw" alt="Jobstep Youtube" role="button" data-mdb-ripple-color="dark"> <img width="35" height="35" src="<?php getSiteURL(); ?>images/thankyoupage/youtube_logo.png" /></a>
                    </section>

                </div>
            </footer>
        <?php } ?>

    </div>
    <footer style="text-align: center; padding: 10px; width:100%">
        <?php
        echo "<p><b>JOBSTEP</b></p>";
        echo "<p>©" . date('Y') . " Sva prava pridržana - Jobstep IT Solutions</p>";
        ?>
    </footer>



</body>
</html>

<?php

?>