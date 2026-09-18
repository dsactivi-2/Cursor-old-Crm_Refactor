<?php
    include('includes/functions.inc.php');
?>
<!doctype html>
<html lang="bs">
    <head>

        <title>Usluge - JobStep</title>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <?php include('includes/head.inc.php'); ?>

    </head>

    <body>

        <div class="container idk_margin_top30">
            <?php include('header.inc.php'); ?>
        </div>

        <div class="idk_title_box">
            <h1>USLUGE</h1>
        </div>

        <div class="container idk_margin_top150">
            <div class="row">
                <?php

                    $query_1 = $db->prepare("
                                    SELECT content_lang_name, content_lang_content, content_lang_img, content_lang_url
                                    FROM idk_content
                                    INNER JOIN idk_content_lang ON idk_content.content_id = idk_content_lang.content_lang_postid
                                    WHERE content_sub = :content_sub AND content_lang_langid = :content_lang_langid
                                    ORDER BY content_sort ASC");


                    $query_1->execute(array(
                                ':content_sub' => 37,
                                ':content_lang_langid' => $lang_id));

                    while($row_1 = $query_1->fetch()){

                        $content_lang_name = $row_1['content_lang_name'];

                        $content_lang_content = strip_tags($row_1['content_lang_content']);
                        if(strlen($content_lang_content) > 220){
                            $pos = strpos($content_lang_content, ' ', 210);
                            $content_lang_content_format = substr($content_lang_content, 0, $pos) . " ...";
                        }else{
                            $content_lang_content_format = $content_lang_content;
                        }

                        $content_lang_img = $row_1['content_lang_img'];
                        $content_lang_url = $row_1['content_lang_url'];

                 ?>
                <div class="col-md-4">
                    <div class="idk_box idk_box_industrije">
                        <a class="idk_none" href="<?php getSiteUrlFront(); ?><?php echo $content_lang_url; ?>"><img src="<?php getSiteUrlFrontFiles(); ?>files/content/<?php echo $content_lang_img; ?>" alt="<?php echo $content_lang_name; ?>"></a>
                        <div class="idk_box_industrije_desc">
                            <a class="idk_none" href="<?php getSiteUrlFront(); ?><?php echo $content_lang_url; ?>"><h4><?php echo $content_lang_name; ?></h4></a>
                            <?php echo $content_lang_content_format; ?>
                            <div class="text-end">
                                <a class="idk_none" href="<?php getSiteUrlFront(); ?><?php echo $content_lang_url; ?>">
                                    <br>
                                    <ul class="list-inline">
                                        <li class="list-inline-item">Vidi više</li>
                                        <li class="list-inline-item"><i class="fa-solid fa-chevron-right"></i></li>
                                    </ul>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
                <script>
                    $(function() {
                        $('.idk_box').matchHeight();
                    });
                </script>

            </div>
            <?php include('contact.inc.php'); ?>
        </div>

        <footer>
            <?php include('footer.inc.php'); ?>
        </footer>

    </body>

</html>
