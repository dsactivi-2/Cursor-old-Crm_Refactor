<?php
    include('includes/functions.inc.php');
?>
<!doctype html>
<html lang="bs">
    <head>

        <title>Konkursi - JobStep</title>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <?php include('includes/head.inc.php'); ?>

    </head>

    <body>

        <div class="container idk_margin_top30">
            <?php include('header.inc.php'); ?>
        </div>

        <div class="idk_title_box">
            <h1>KONKURSI</h1>
            <h2>Na ovoj stranice možete pronaći aktuelne konkurse za posao u Njemačkoj. Pronađite posao za Vas i prijavite se u našu bazu.</h2>
        </div>

        <div class="container idk_margin_top150">
            <form class="idk_form" action="index.html" method="post" style="display:none">
                <div class="row">
                        <div class="col-lg-4">
                            <input type="text" name="" placeholder="Pretraži sve poslove ...">
                        </div>
                        <div class="col-lg-4">
                            <select name="">
                                <option value="">Lokacija</option>
                                <option value="">Bihać</option>
                                <option value="">Nürnberg - München</option>
                                <option value="">Njemačka</option>
                                <option value="">ULM</option>
                                <option value="">Svi gradovi, Njemačka</option>
                            </select>
                        </div>
                        <div class="col-lg-4">
                            <select name="">
                                <option value="">Zanimanje</option>
                                <option value="">Građevinski radnici</option>
                                <option value="">Servisni Tehničar</option>
                                <option value="">Električar</option>
                                <option value="">Automehaničar</option>
                                <option value="">Građevinski tehničar</option>
                            </select>
                        </div>
                </div>
            </form>

            <div class="row idk_margin_top50">
                <?php

                    $query_2 = $db->prepare("
                                    SELECT job_lang_url, job_lang_title, job_lang_img, job_lang_salary, jlocation_lang_name
                                    FROM idk_jobs
                                    INNER JOIN idk_jobs_lang ON idk_jobs.job_id = idk_jobs_lang.job_lang_jobid
                                    INNER JOIN idk_jobs_locations ON idk_jobs.job_locationid = idk_jobs_locations.jlocation_id
                                    INNER JOIN idk_jobs_location_langs ON idk_jobs_locations.jlocation_id = idk_jobs_location_langs.jlocation_lang_locationid
                                    WHERE job_status = :job_status AND job_lang_langid = :job_lang_langid
                                    GROUP BY job_id
                                    ORDER BY job_id DESC
                                    LIMIT 3");


                    $query_2->execute(array(
                                ':job_status' => 1,
                                ':job_lang_langid' => $lang_id));

                    while($row_2 = $query_2->fetch()){

                        $job_lang_url = $row_2['job_lang_url'];
                        $job_lang_title = $row_2['job_lang_title'];
                        $job_lang_img = $row_2['job_lang_img'];
                        $job_lang_salary = $row_2['job_lang_salary'];
                        $jlocation_lang_name = $row_2['jlocation_lang_name'];

                 ?>
                <div class="col-md-4">
                    <div class="idk_box idk_box_konkursi">
                        <a class="idk_none" href="<?php getSiteUrlFront(); ?><?php echo $job_lang_url; ?>"><img src="<?php getSiteUrlFrontFiles(); ?>files/jobs/thumbs/<?php echo $job_lang_img; ?>" alt="<?php echo $job_lang_title; ?>"></a>
                        <div class="idk_box_konkursi_desc">
                            <span><?php echo $jlocation_lang_name; ?></span>
                            <a class="idk_none" href="<?php getSiteUrlFront(); ?><?php echo $job_lang_url; ?>"><h4><?php echo $job_lang_title; ?></h4></a>
                            <div class="row">
                                <div class="col-8">
                                    <p><?php echo $job_lang_salary; ?></p>
                                </div>
                                <div class="col-4 text-end">
                                    <a class="idk_none" href="<?php getSiteUrlFront(); ?><?php echo $job_lang_url; ?>">
                                        <ul class="list-inline">
                                            <li class="list-inline-item">Vidi više</li>
                                            <li class="list-inline-item"><i class="fa-solid fa-chevron-right"></i></li>
                                        </ul>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
            <script>
                $(function() {
                    $('.idk_box_konkursi').matchHeight();
                });
            </script>
        </div>

        <footer>
            <?php include('footer.inc.php'); ?>
        </footer>

    </body>

</html>
