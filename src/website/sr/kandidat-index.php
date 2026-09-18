<?php

    include('includes/functions.inc.php');

    getContent(17);

?>
<!doctype html>
<html lang="<?php echo $lang_code; ?>">
    <head>

        <title><?php echo $content_lang_name; ?> - JobStep</title>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <?php include('includes/head.inc.php'); ?>

    </head>

    <body>

        <div class="container idk_margin_top30">
            <?php include('header.inc.php'); ?>
        </div>

        <div class="idk_title_box">
            <h1><?php echo $content_lang_name; ?></h1>
            <h2><?php echo $content_lang_content; ?></h2>
        </div>

        <div class="container">

            <div class="row idk_margin_top150">
                <?php getContent(18); ?>
                <div class="col-lg-3 col-md-12">
                    <h3 class="idk_title"><?php echo $content_lang_name; ?></h3>
                </div>
                <?php

                    $query_1 = $db->prepare("
                                    SELECT content_lang_name, content_lang_content, content_lang_img, content_lang_url
                                    FROM idk_content
                                    INNER JOIN idk_content_lang ON idk_content.content_id = idk_content_lang.content_lang_postid
                                    WHERE content_sub = :content_sub AND content_lang_langid = :content_lang_langid
                                    ORDER BY content_sort ASC");


                    $query_1->execute(array(
                                ':content_sub' => 18,
                                ':content_lang_langid' => $lang_id));

                    while($row_1 = $query_1->fetch()){

                        $content_lang_name = $row_1['content_lang_name'];
                        $content_lang_content = $row_1['content_lang_content'];
                        $content_lang_img = $row_1['content_lang_img'];
                        $content_lang_url = $row_1['content_lang_url'];

                 ?>
                <div class="col-lg-3 col-md-4">
                    <div class="idk_box idk_box_ponuda text-center">
                        <a href="<?php echo $content_lang_url; ?>"><img src="<?php getSiteUrlFrontFiles(); ?>files/content/<?php echo $content_lang_img; ?>" alt="<?php echo $content_lang_name; ?>"></a>
                        <h4><?php echo $content_lang_name; ?></h4>
                        <?php echo $content_lang_content; ?>
                    </div>
                </div>
                <?php } ?>
            </div>
            <script>
                $(function() {
                    $('.idk_box_ponuda').matchHeight();
                });
            </script>

            <div class="row idk_margin_top150">
                <div class="col-12">
                    <h3 class="idk_title"><?php echo $lang_Konkursi_za_posao; ?></h3>
                </div>
            </div>

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
                                            <li class="list-inline-item"><?php echo $lang_Vidi_vise; ?></li>
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

            <div class="row idk_margin_top150">
                <div class="col-md-3">
                    <h3 class="idk_landing_title"><?php echo $lang_Nasi_kandidati_o_nama; ?></h3>
                    <div class="text-end idk_margin_top100">
                        <ul class="list-inline">
                            <li class="list-inline-item"><a class="prev" href="#"><i class="fa-solid fa-chevron-left"></i></a></li>
                            <li class="list-inline-item"><a class="next" href="#"><i class="fa-solid fa-chevron-right"></i></a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-9">
                    <div id="idk_reviews_slider">
                        <?php
                            $query_a = $db->prepare("
                                            SELECT testimonial_img, testimonial_lang_name, testimonial_lang_txt, testimonial_lang_location
                                            FROM idk_testimonials
                                            LEFT JOIN idk_testimonials_lang ON idk_testimonials.testimonial_id = idk_testimonials_lang.testimonial_lang_testimonialid
                                            WHERE testimonial_status = :testimonial_status AND testimonial_lang_langid = :testimonial_lang_langid
                                            GROUP BY testimonial_id
                                            ORDER BY testimonial_id DESC");

                            $query_a->execute(array(
                                            ':testimonial_status' => 1,
                                            ':testimonial_lang_langid' => $lang_id));

                            while($row_a = $query_a->fetch()){

                                $testimonial_lang_name = $row_a['testimonial_lang_name'];
                                $testimonial_lang_txt = $row_a['testimonial_lang_txt'];
                                $testimonial_lang_location = $row_a['testimonial_lang_location'];

                                if($row_a['testimonial_img'] == NULL){
                                    $testimonial_img = "none.jpg";
                                }else{
                                    $testimonial_img = $row_a['testimonial_img'];
                                }
                        ?>
                        <div>
                            <div class="idk_landing_review">
                                <?php echo $testimonial_lang_txt; ?>
                                <div class="row idk_margin_top30">
                                    <div class="col-4">
                                        <img src="<?php getSiteUrlFrontFiles(); ?>files/testimonials/thumbs/<?php echo $testimonial_img; ?>" alt="<?php echo $testimonial_lang_name; ?>">
                                    </div>
                                    <div class="col-8">
                                        <h4><?php echo $testimonial_lang_name; ?></h4>
                                        <span><?php echo $testimonial_lang_location; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <script>
               $('#idk_reviews_slider').slick({
                   dots: false,
                   arrows: true,
                   infinite: true,
                   speed: 300,
                   slidesToShow: 3,
                   slidesToScroll: 1,
                   prevArrow: $('.prev'),
                   nextArrow: $('.next'),
                   responsive: [
                     {
                       breakpoint: 1200,
                       settings: {
                         slidesToShow: 2,
                         slidesToScroll: 1
                       }
                   },
                     {
                       breakpoint: 992,
                       settings: {
                         slidesToShow: 1,
                         slidesToScroll: 1
                       }
                     }
                   ]
               });
           </script>

           <div class="row idk_margin_top150">
                <?php getContent(22); ?>
               <div class="col-12">
                   <h3 class="idk_title"><?php echo $content_lang_name; ?></h3>
               </div>
           </div>

           <div class="row idk_margin_top50">
               <div class="col-md-5">
                   <img class="idk_onama_img" src="<?php getSiteUrlFrontFiles(); ?>files/content/<?php echo $content_lang_img; ?>" alt="<?php echo $content_lang_name; ?>">
               </div>
               <div class="col-md-1"></div>
               <div class="col-md-6 idk_margin_top30">
                   <?php echo $content_lang_content; ?>
               </div>
           </div>

           <div class="row idk_margin_top150 text-center">
               <div class="col-12">
                   <h3 class="idk_title">Blog</h3>
               </div>
           </div>

           <div class="row idk_margin_top50">
               <?php
                   $query_b = $db->prepare("
                                   SELECT post_lang_title, post_lang_url, post_lang_content, post_lang_img, post_datetime
                                   FROM idk_posts
                                   LEFT JOIN idk_posts_lang ON idk_posts.post_id = idk_posts_lang.post_lang_postid
                                   WHERE post_status = :post_status AND post_lang_langid = :post_lang_langid AND post_featured = :post_featured
                                   GROUP BY post_id
                                   ORDER BY post_id DESC
                                   LIMIT 1");

                   $query_b->execute(array(
                                   ':post_status' => 1,
                                   ':post_featured' => 1,
                                   ':post_lang_langid' => $lang_id));

                   while($row_b = $query_b->fetch()){

                       $post_lang_title = $row_b['post_lang_title'];
                       $post_lang_url = $row_b['post_lang_url'];
                       $post_datetime = date('d.m.Y.', strtotime($row_b['post_datetime']));

                       $post_lang_content = strip_tags($row_b['post_lang_content']);
                       if(strlen($post_lang_content) > 200){
                           $pos = strpos($post_lang_content, ' ', 190);
                           $post_lang_content_format = substr($post_lang_content, 0, $pos) . " ...";
                       }else{
                           $post_lang_content_format = $post_lang_content;
                       }

                       if($row_b['post_lang_img'] == NULL){
                           $post_lang_img = "none.jpg";
                       }else{
                           $post_lang_img = $row_b['post_lang_img'];
                       }
               ?>
               <div class="col-lg-6 col-md-12">
                   <div class="idk_box idk_blog_box">
                       <a href="<?php getSiteUrlFront(); ?><?php echo $post_lang_url; ?>"><img src="<?php getSiteUrlFrontFiles(); ?>files/posts/thumbs/<?php echo $post_lang_img; ?>" alt="<?php echo $post_lang_title; ?>"></a>
                       <div class="idk_box_blog_desc">
                           <span><?php echo $post_datetime; ?></span>
                           <a class="idk_none" href="<?php getSiteUrlFront(); ?><?php echo $post_lang_url; ?>"><h4><?php echo $post_lang_title; ?></h4></a>
                           <p><?php echo $post_lang_content_format; ?></p>
                       </div>
                   </div>
               </div>
               <?php } ?>
               <div class="col-lg-6 col-md-12">
                   <div class="row">
                       <?php
                           $query_c = $db->prepare("
                                           SELECT post_lang_title, post_lang_url, post_lang_img, post_datetime
                                           FROM idk_posts
                                           LEFT JOIN idk_posts_lang ON idk_posts.post_id = idk_posts_lang.post_lang_postid
                                           WHERE post_status = :post_status AND post_lang_langid = :post_lang_langid AND post_featured = :post_featured
                                           GROUP BY post_id
                                           ORDER BY post_id DESC
                                           LIMIT 1,4");

                           $query_c->execute(array(
                                           ':post_status' => 1,
                                           ':post_featured' => 1,
                                           ':post_lang_langid' => $lang_id));

                           while($row_c = $query_c->fetch()){

                               $post_lang_title = $row_c['post_lang_title'];
                               $post_lang_url = $row_c['post_lang_url'];
                               $post_datetime = date('d.m.Y.', strtotime($row_c['post_datetime']));

                               if($row_c['post_lang_img'] == NULL){
                                   $post_lang_img = "none.jpg";
                               }else{
                                   $post_lang_img = $row_c['post_lang_img'];
                               }
                       ?>
                       <div class="col-md-6">
                           <div class="idk_box idk_blog_box_s">
                               <a href="<?php getSiteUrlFront(); ?><?php echo $post_lang_url; ?>"><img src="<?php getSiteUrlFrontFiles(); ?>files/posts/thumbs/<?php echo $post_lang_img; ?>" alt="<?php echo $post_lang_title; ?>"></a>
                               <div class="idk_box_blog_desc">
                                   <span><?php echo $post_datetime; ?></span>
                                   <a class="idk_none" href="<?php getSiteUrlFront(); ?><?php echo $post_lang_url; ?>"><h4><?php echo $post_lang_title; ?></h4></a>
                               </div>
                           </div>
                       </div>
                       <?php } ?>
                   </div>
               </div>
           </div>
           <script>
               $(function() {
                   $('.idk_blog_box_s').matchHeight();
               });
           </script>

           <div class="row idk_margin_top150 text-center">
               <div class="col-12">
                   <h3 class="idk_title"><?php echo $lang_Cesto_postavljena_pitanja; ?></h3>
               </div>
           </div>

           <div class="row idk_margin_top50 idk_faq">
               <?php

                   $query = $db->prepare("
                                   SELECT faq_id, faq_lang_question, faq_lang_answer
                                   FROM idk_faq
                                   LEFT JOIN idk_faq_lang ON idk_faq.faq_id = idk_faq_lang.faq_lang_faqid
                                   WHERE faq_lang_langid = :faq_lang_langid
                                   GROUP BY faq_id
                                   ORDER BY faq_id ASC
                                   LIMIT 4");

                   $query->execute(array(
                                   ':faq_lang_langid' => $lang_id));

                   while($row = $query->fetch()){

                       $faq_id = $row['faq_id'];
                       $faq_lang_question = $row['faq_lang_question'];
                       $faq_lang_answer = $row['faq_lang_answer'];

               ?>
               <div class="col-md-6">
                   <div class="row">
                       <div class="col-1">
                           <i class="fa-solid fa-question"></i>
                       </div>
                       <div class="col-10">
                           <h4><?php echo $faq_lang_question; ?></h4>
                           <?php echo $faq_lang_answer; ?>
                       </div>
                   </div>
               </div>
               <?php } ?>
               <div class="col-12 text-center">
                   <a class="idk_btn_link" href="<?php getSiteUrlFront(); ?>faq"><?php echo $lang_VIDI_VISE1; ?></a>
               </div>
           </div>


           <?php include('contact.inc.php'); ?>

        </div>

        <footer>
            <?php include('footer.inc.php'); ?>
        </footer>

    </body>

</html>
