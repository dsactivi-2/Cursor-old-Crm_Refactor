<?php
    include('includes/functions.inc.php');


    $post_id = intval($_GET['id']);

    getPost($post_id);

?>
<!doctype html>
<html lang="bs">
    <head>

        <title><?php echo $post_lang_title; ?>  - JobStep</title>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <?php include('includes/head.inc.php'); ?>

        <meta name="description" content="<?php echo $post_lang_seodesc; ?>" />
        <meta name="keywords" content="<?php echo $post_lang_seokeywords; ?>"/>
        <meta property="og:title" content="<?php echo $post_lang_seoname; ?>" />
        <meta property="og:description" content="<?php echo $post_lang_seodesc; ?>" />
        <meta property="og:image" content="<?php getSiteUrlFrontFiles(); ?>files/posts/<?php echo $post_lang_img; ?>" />
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="JobStep">
        <meta property="og:url" content="<?php getSiteUrlFront(); ?><?php echo $post_lang_url; ?>">

    </head>

    <body>

        <div class="container idk_margin_top30">
            <?php include('header.inc.php'); ?>
        </div>

        <div class="idk_title_box">
            <h1>BLOG</h1>
        </div>

        <div class="container idk_margin_top150">
            <div class="idk_blog_holder">
                <div class="row">
                    <div class="col-12">
                        <span><?php echo $post_datetime; ?></span>
                        <h1><?php echo $post_lang_title; ?></h1>
                        <img src="<?php getSiteUrlFrontFiles(); ?>files/posts/<?php echo $post_lang_img; ?>" alt="<?php echo $post_lang_title; ?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-1"></div>
                <div class="col-lg-10">
                    <?php echo $post_lang_content; ?>
                    <div class="idk_share_icons text-end idk_margin_top50">
                        <ul class="list-inline">
                            <li class="list-inline-item"><p>Podijeli:</p></li>
                            <li class="list-inline-item"><div class="sharethis-inline-share-buttons"></div></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-1"></div>
            </div>
        </div>

        <footer>
            <?php include('footer.inc.php'); ?>
        </footer>

    </body>

</html>
