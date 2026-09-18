<?php
include('includes/functions.inc.php');

$content_id = intval($_GET['id']);

getContent($content_id);

?>
<!doctype html>
<html lang="bs">
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
        </div>

        <div class="container idk_margin_top150">
            <div class="idk_blog_holder">
                <div class="row">
                    <div class="col-12">
                        <img src="<?php getSiteUrlFrontFiles(); ?>files/content/<?php echo $content_lang_img; ?>" alt="<?php echo $content_lang_name; ?>">
                    </div>
                </div>


            </div>
            <div class="row">
                <div class="col-lg-1"></div>
                <div class="col-lg-10">
                    <?php echo $content_lang_content; ?>
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
