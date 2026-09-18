<?php

    include('includes/functions.inc.php');

    getContent(5);
    if(isset($_GET['mess'])) {
        $mess = $_GET['mess'];
    }else{
        $mess = 0;
    }
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

            <div id="p" class="row idk_margin_top150">
                <?php getContent(6); ?>
                <div class="col-lg-3 col-md-12">
                    <h3 class="idk_title"><?php echo $content_lang_name; ?></h3>
                </div>
                <?php

                    $query_1 = $db->prepare("
                                    SELECT content_lang_name, content_lang_content, content_lang_img
                                    FROM idk_content
                                    INNER JOIN idk_content_lang ON idk_content.content_id = idk_content_lang.content_lang_postid
                                    WHERE content_sub = :content_sub AND content_lang_langid = :content_lang_langid
                                    ORDER BY content_sort ASC");


                    $query_1->execute(array(
                                ':content_sub' => 6,
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

            <div id="iz" class="row idk_margin_top150">
                <?php getContent(11); ?>
                <div class="col-12">
                    <h3 class="idk_title"><?php echo $content_lang_name; ?></h3>
                </div>
            </div>

            <div class="row idk_margin_top50">
                <?php

                    $query_1 = $db->prepare("
                                    SELECT content_lang_name, content_lang_content, content_lang_img, content_lang_url
                                    FROM idk_content
                                    INNER JOIN idk_content_lang ON idk_content.content_id = idk_content_lang.content_lang_postid
                                    WHERE content_sub = :content_sub AND content_lang_langid = :content_lang_langid
                                    ORDER BY content_sort ASC");


                    $query_1->execute(array(
                                ':content_sub' => 11,
                                ':content_lang_langid' => $lang_id));

                    while($row_1 = $query_1->fetch()){

                        $content_lang_name = $row_1['content_lang_name'];
                        $content_lang_content = $row_1['content_lang_content'];

                        $content_lang_content = strip_tags($row_1['content_lang_content']);
                        if (strlen($content_lang_content) > 220) {
                            $pos = strpos($content_lang_content, ' ', 210);
                            $content_lang_content_format = substr($content_lang_content, 0, $pos) . " ...";
                        } else {
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
            </div>
            <script>
                $(function() {
                    $('.idk_box_industrije').matchHeight();
                });
            </script>

           <div class="row idk_margin_top150">
               <?php getContent(15); ?>
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

           <div class="row idk_margin_top150">
               <div class="col-12">
                   <?php getContent(16); ?>
                   <h3 class="idk_title"><?php echo $content_lang_name; ?></h3>
               </div>
           </div>

           <div class="row idk_margin_top50">
           <div  class="row alert alert-success"  style="margin-left: 0px; margin-right: 0px; display: <?php if($mess==1){echo "block"; }else{echo "none";} ?>">
                Vaša poruka je uspješno poslana!
            </div>
               <div class="col-lg-5">
                   <?php echo $content_lang_content; ?>
               </div>
               <div class="col-lg-1"></div>
               <div class="col-lg-6">
                   <form class="idk_form" action="<?php echo getSiteUrlFrontr() . 'mail.php?form=employeer_form' ; ?>" method="post">
                       <input type="text" class="form-control" name="nameSurname" placeholder="<?php echo $lang_Ime_i_prezime; ?> *" required>
                       <input type="text" class="form-control" name="companyName" placeholder="<?php echo $lang_Naziv_kompanije; ?> *" required>
                       <input type="email" class="form-control" name="email" placeholder="<?php echo $lang_Email; ?> *" required>
                       <input type="text" class="form-control" name="phone" placeholder="<?php echo $lang_Telefon; ?>">
                       <div class="row">
                           <div class="col-md-7">
                               <textarea class="form-control" name="name" placeholder="<?php echo $lang_Poruka; ?>" rows="6" cols="60"></textarea>
                           </div>
                           <div class="col-md-5">
                               <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="flexCheckChecked">
                                    <label for="flexCheckChecked"><?php echo $lang_checkbox_txt; ?></label>
                                </div>
                                <button type="submit" name="button"><?php echo $lang_POSALJI; ?></button>
                           </div>
                       </div>
                   </form>
               </div>
           </div>



           <?php include('contact.inc.php'); ?>

        </div>

        <footer>
            <?php include('footer.inc.php'); ?>
        </footer>

    </body>

</html>
