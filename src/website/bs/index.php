<?php

    include('includes/functions.inc.php');

?>
<!doctype html>
<html lang="<?php echo $lang_code; ?>">
    <head>

        <title>JobStep</title>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <?php include('includes/head.inc.php'); ?>

    </head>

    <body>

        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-2 idk_landing_left">
                    <div class="idk_landing_logo_holder">
                        <img src="images/logo.png" alt="JobStep">

                        <div class="idk_landing_social">
                            <p>Find us on social:</p>
                            <ul class="list-inline">
                                <li class="list-inline-item"><a href="https://www.linkedin.com/company/jobstep-int-gmbh" target="_blank"><i class="fa-brands fa-linkedin-in"></i></a></li>
                                <li class="list-inline-item"><a href="https://www.facebook.com/JobStepEU/" target="_blank"><i class="fa-brands fa-facebook-f"></i></a></li>
                                <li class="list-inline-item"><a href="https://www.instagram.com/meet.jobstep/" target="_blank"><i class="fa-brands fa-instagram"></i></a></li>
                                <li class="list-inline-item"><a href="https://www.youtube.com/@jobstepinternationaldoo2856" target="_blank"><i class="fa-brands fa-youtube"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-10 idk_landing_right">
                    <div class="idk_landing_logo_mobile">
                        <a href="#"><img src="images/logo_m.png"></a>
                    </div>
                    <?php

                        $query_slider = $db->prepare("
                                                    SELECT slider_id, slider_lang_title, slider_lang_img
                                                    FROM idk_slider
                                                    INNER JOIN idk_slider_lang ON idk_slider.slider_id = idk_slider_lang.slider_lang_sliderid
                                                    WHERE slider_lang_langid = :slider_lang_langid AND slider_status = :slider_status
                                                    GROUP BY slider_id
                                                    ORDER BY slider_sort ASC");

                        $query_slider->execute(array(
                            ':slider_lang_langid' => $lang_id,
                            ':slider_status' => 1
                        ));

                        $row_slide = $query_slider->fetch();
                            $slider_lang_img = $row_slide['slider_lang_img'];
                    ?>
                    <img class="idk_landing_bg" src="<?php getSiteUrlFrontFiles(); ?>files/slider/<?php echo $slider_lang_img; ?>" alt="JobStep">
                    <div class="idk_landing_bg_filter"></div>

                    <div class="container idk_margin_top30">

                        <div class="row">
                            <div class="col-9">
                                <!--<div id='cssmenu_landing'>
                                    <ul>
                                       <li><a href='<?php getSiteUrlFront1(); ?>'><i class="fa-solid fa-house-chimney"></i></a></li>
                                       <li><a href='<?php getSiteUrlFront1(); ?>/onama'>O nama</a></li>
                                       <li><a href='<?php getSiteUrlFront1(); ?>/kontakt'>Kontakt</a></li>
                                    </ul>
                                </div>-->
                            </div>
                            <div class="col-3 text-end">
                                <div class="dropdown idk_landing_langsm">
                                    <a class="idk_landing_langs" href="#" data-bs-toggle="dropdown">
                                        <ul class="list-inline">
                                            <li class="list-inline-item"><i class="fa-solid fa-globe fa-lg"></i></li>
                                            <li class="list-inline-item">BS</li>
                                        </ul>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="<?php echo $envConfig->WEBSITE_URL . "bs"; ?>">BS</a></li>
                                        <li><a class="dropdown-item" href="<?php echo $envConfig->WEBSITE_URL . "sr"; ?>">SR</a></li>
                                        <li><a class="dropdown-item" href="<?php echo $envConfig->WEBSITE_URL . "de"; ?>">DE</a></li>
                                        <li><a class="dropdown-item" href="<?php echo $envConfig->WEBSITE_URL . "en"; ?>">EN</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="row idk_margin_top200 idk_margin_top100m idk_landing_header">
                            <div class="col-md-5 text-center text-md-start">
                                <?php getContent(2); ?>
                                <h2><?php echo $content_lang_name; ?></h2>
                                <?php echo $content_lang_content; ?>
                            </div>
                            <div class="col-md-2"></div>
                            <div class="col-md-5 text-center text-md-start">
                                <?php getContent(3); ?>
                                <h2><?php echo $content_lang_name; ?></h2>
                                <?php echo $content_lang_content; ?>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>


    </body>

</html>
