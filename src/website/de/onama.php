<?php

    include('includes/functions.inc.php');

    getContent(24);

?>
<!doctype html>
<html lang="bs">
    <head>

        <title>O nama - JobStep</title>

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
                <?php getContent(25); ?>
                <div class="col-lg-3 col-md-12">
                    <h3 class="idk_title"><?php echo $content_lang_name; ?></h3>
                </div>
                <div class="col-lg-8 col-md-12">
                    <div class="idk_content_onama">
                        <img src="<?php getSiteUrlFrontFiles(); ?>files/content/<?php echo $content_lang_img; ?>" alt="<?php echo $content_lang_name; ?>">
                        <div class="row">
                            <div class="col-lg-1"></div>
                            <div class="col-lg-10">
                                <?php echo $content_lang_content; ?>
                            </div>
                            <div class="col-lg-1"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row idk_margin_top150">
                <?php getContent(26); ?>
                <div class="col-12 text-center">
                    <h3 class="idk_title"><?php echo $content_lang_name; ?></h3>
                </div>
            </div>

            <div class="row idk_margin_top50">
                <?php

                    $query_1 = $db->prepare("
                                    SELECT content_lang_name, content_lang_content, content_lang_img
                                    FROM idk_content
                                    INNER JOIN idk_content_lang ON idk_content.content_id = idk_content_lang.content_lang_postid
                                    WHERE content_sub = :content_sub AND content_lang_langid = :content_lang_langid
                                    ORDER BY content_sort ASC");


                    $query_1->execute(array(
                                ':content_sub' => 26,
                                ':content_lang_langid' => $lang_id));

                    while($row_1 = $query_1->fetch()){

                        $content_lang_name = $row_1['content_lang_name'];
                        $content_lang_content = $row_1['content_lang_content'];
                        $content_lang_img = $row_1['content_lang_img'];

                 ?>
                <div class="col-lg-3 col-md-4">
                    <div class="idk_box idk_box_ponuda text-center">
                        <a href="#"><img src="<?php getSiteUrlFrontFiles(); ?>files/content/<?php echo $content_lang_img; ?>" alt="<?php echo $content_lang_name; ?>"></a>
                        <h4><?php echo $content_lang_name; ?></h4>
                        <p><?php echo $content_lang_content; ?></p>
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
                <?php getContent(31); ?>
                <div class="col-lg-3 col-md-12">
                    <h3 class="idk_title"><?php echo $content_lang_name; ?></h3>
                </div>
                <div class="col-lg-8 col-md-12">
                    <div class="idk_content_onama">
                        <img src="<?php getSiteUrlFrontFiles(); ?>files/content/<?php echo $content_lang_img; ?>" alt="<?php echo $content_lang_name; ?>">
                        <div class="row">
                            <div class="col-lg-1"></div>
                            <div class="col-lg-10">
                                <?php echo $content_lang_content; ?>
                            </div>
                            <div class="col-lg-1"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row idk_margin_top150">
                <div class="col-12 text-center">
                    <h3 class="idk_title">Brojke</h3>
                </div>
            </div>

            <div class="row idk_margin_top50">
                <?php

                    $query_2 = $db->prepare("
                                    SELECT content_lang_name, content_lang_content
                                    FROM idk_content
                                    INNER JOIN idk_content_lang ON idk_content.content_id = idk_content_lang.content_lang_postid
                                    WHERE content_sub = :content_sub AND content_lang_langid = :content_lang_langid
                                    ORDER BY content_sort ASC");


                    $query_2->execute(array(
                                ':content_sub' => 32,
                                ':content_lang_langid' => $lang_id));

                    while($row_2 = $query_2->fetch()){

                        $content_lang_name = $row_2['content_lang_name'];
                        $content_lang_content = $row_2['content_lang_content'];

                        $doc = new DOMDocument();
                        libxml_use_internal_errors(true);
                        $doc->loadHTML($content_lang_content);
                        $h3_element = $doc->getElementsByTagName('h3')->item(0);
                        $h3_content = $h3_element->textContent;

                        $p_element = $doc->getElementsByTagName('p')->item(0);
                        $p_content = $p_element->textContent;

                 ?>
                <div class="col-lg-3 col-md-4">
                    <div class="idk_box idk_box_brojke text-center">
                        <h3><?php echo $h3_content; ?></h3>
                        <h4><?php echo $content_lang_name; ?></h4>
                        <p><?php echo $p_content; ?></p>
                    </div>
                </div>
                <?php } ?>
            </div>
            <script>
                $(function() {
                    $('.idk_box_brojke').matchHeight();
                });
            </script>

           <?php include('contact.inc.php'); ?>

        </div>

        <footer>
            <?php include('footer.inc.php'); ?>
        </footer>

    </body>

</html>
