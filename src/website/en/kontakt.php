<?php

    include('includes/functions.inc.php');

    getContent(39);
    if(isset($_GET['mess'])) {
        $mess = $_GET['mess'];
    }else{
        $mess = 0;
    }
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
            <h2><?php /*echo $content_lang_content;*/ ?></h2>
        </div>

        <div class="container">
            <div class="row idk_margin_top150">
            <div  class="row alert alert-success"  style="margin-left: 0px; margin-right: 0px; display: <?php if($mess==1){echo "block"; }else{echo "none";} ?>">
                Vaša poruka je uspješno poslana!
            </div>
                <div class="col-lg-3 col-md-12">
                    <h3 class="idk_title"><?php echo $lang_Ostanimo_u_kontaktu; ?></h3>
                </div>
                <div class="col-lg-1 col-md-12"></div>
                <div class="col-lg-7 col-md-12">
                    <form class="idk_form" action="<?php echo getSiteUrlFrontr() . 'mail.php?form=footer_form' ; ?>" method="post">
                        <input type="text" class="form-control" name="firstName" placeholder="<?php echo $lang_Ime; ?> *" required>
                        <input type="text" class="form-control" name="lastName" placeholder="<?php echo $lang_Prezime; ?> *" required>
                        <input type="email" class="form-control" name="email" placeholder="<?php echo $lang_Email; ?> *" required>
                        <input type="text" class="form-control" name="phone" placeholder="<?php echo $lang_Telefon; ?>">
                        <input type="text" class="form-control" name="subject" placeholder="<?php echo $lang_Predmet; ?>">
                        <textarea class="form-control" name="message" placeholder="<?php echo $lang_Poruka; ?>" rows="8" cols="80"></textarea>
                        <button type="submit" name="button"><?php echo $lang_POSALJI; ?></button>
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
