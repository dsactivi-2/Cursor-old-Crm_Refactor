<?php

    include('includes/functions.inc.php');

?>
<!doctype html>
<html lang="bs">
    <head>

        <title>Često postavljena pitanja - JobStep</title>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <?php include('includes/head.inc.php'); ?>

    </head>

    <body>

        <div class="container idk_margin_top30">
            <?php include('header.inc.php'); ?>
        </div>

        <div class="idk_title_box">
            <h1>ČESTO POSTAVLJENA PITANJA</h1>
        </div>

        <div class="container idk_margin_top150">
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
                <div class="col-12 text-end">
                    <div class="idk_share_icons text-end">
                        <ul class="list-inline">
                            <li class="list-inline-item"><p>Podijeli:</p></li>
                            <li class="list-inline-item"><div class="sharethis-inline-share-buttons"></div></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <footer>
            <?php include('footer.inc.php'); ?>
        </footer>

    </body>

</html>
