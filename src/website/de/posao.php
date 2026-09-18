<?php
    include('includes/functions.inc.php');

    $job_id = intval($_GET['id']);

    $query = $db->prepare("
                    SELECT job_lang_url, job_lang_title, job_lang_img, job_lang_salary, jlocation_lang_name, job_lang_accommodation, job_lang_content, job_application_form_link
                    FROM idk_jobs
                    INNER JOIN idk_jobs_lang ON idk_jobs.job_id = idk_jobs_lang.job_lang_jobid
                    INNER JOIN idk_jobs_locations ON idk_jobs.job_locationid = idk_jobs_locations.jlocation_id
                    INNER JOIN idk_jobs_location_langs ON idk_jobs_locations.jlocation_id = idk_jobs_location_langs.jlocation_lang_locationid
                    WHERE job_status = :job_status AND job_lang_langid = :job_lang_langid AND job_id = :job_id
                    GROUP BY job_id
                    ORDER BY job_id DESC
                    LIMIT 3");


    $query->execute(array(
                ':job_status' => 1,
                ':job_id' => $job_id,
                ':job_lang_langid' => $lang_id));

    $row = $query->fetch();

        $job_lang_url = $row['job_lang_url'];
        $job_lang_title = $row['job_lang_title'];
        $job_lang_img = $row['job_lang_img'];
        $job_lang_salary = $row['job_lang_salary'];
        $jlocation_lang_name = $row['jlocation_lang_name'];
        $job_lang_accommodation = $row['job_lang_accommodation'];
        $job_lang_content = $row['job_lang_content'];
        $job_app_form = $row['job_application_form_link'];

 ?>
<!doctype html>
<html lang="bs">
    <head>

        <title><?php echo $job_lang_title; ?> - JobStep</title>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <?php include('includes/head.inc.php'); ?>

    </head>

    <body>

        <div class="container idk_margin_top30">
            <?php include('header.inc.php'); ?>
        </div>

        <div class="idk_title_box">
            <h1><?php echo $job_lang_title; ?></h1>
            <h2><?php echo $jlocation_lang_name; ?> | <?php echo $job_lang_salary; ?></h2>
        </div>

        <div class="container idk_margin_top150">
            <div class="row">
                <div class="col-lg-4">
                    <div class="idk_box idk_box_konkurs">
                        <img src="<?php getSiteUrlFrontFiles(); ?>files/jobs/<?php echo $job_lang_img; ?>" alt="">
                        <div class="idk_box_konkurs_desc">
                            <ul class="fa-ul">
                                <li><span class="fa-li" style="--fa-li-width: 4em;"><i class="fa-solid fa-location-dot"></i></span><?php echo $jlocation_lang_name; ?></li>
                                <li><span class="fa-li" style="--fa-li-width: 4em;"><i class="fa-solid fa-money-bill"></i></span><?php echo $job_lang_salary; ?></li>
                                <li><span class="fa-li" style="--fa-li-width: 4em;"><i class="fa-solid fa-bed"></i></span>Smještaj: <?php echo $job_lang_accommodation; ?></li>
                            </ul>
                            <div class="text-center idk_margin_top50">
                                <a class="idk_btn_link_dark" href="<?php echo $job_app_form; ?>">PRIJAVI SE!</a>
                            </div>
                        </div>
                    </div>
                    <div class="row idk_share_icons">
                        <div class="col-3">
                            <p>Podijeli:</p>
                        </div>
                        <div class="col-9">
                            <div class="sharethis-inline-share-buttons"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-1"></div>
                <div class="col-lg-7">
                    <?php echo $job_lang_content; ?>
                </div>
            </div>

            <div class="row idk_margin_top150">
                <?php getContent(23); ?>
                <div class="col-lg-3">
                    <h3 class="idk_title"><?php echo $content_lang_name; ?></h3>
                </div>
                <div class="col-lg-1"></div>
                <div class="col-lg-8">
                    <div class="idk_box_dark">
                        <?php echo $content_lang_content; ?>
                    </div>
                    <div class="row idk_margin_top30">
                        <div class="col-md-8">
                            <a class="idk_btn_link_dark" href="<?php echo $job_app_form; ?>">PRIJAVI SE!</a>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="row idk_share_icons">
                                <div class="col-3">
                                    <p>Podijeli:</p>
                                </div>
                                <div class="col-9">
                                    <div class="sharethis-inline-share-buttons"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <footer>
            <?php include('footer.inc.php'); ?>
        </footer>

    </body>

</html>
