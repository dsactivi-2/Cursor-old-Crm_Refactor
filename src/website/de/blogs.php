<?php
    include('includes/functions.inc.php');
?>
<!doctype html>
<html lang="bs">
    <head>

        <title>BLOG - JobStep</title>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <?php include('includes/head.inc.php'); ?>

    </head>

    <body>

        <div class="container idk_margin_top30">
            <?php include('header.inc.php'); ?>
        </div>

        <div class="idk_title_box">
            <h1>BLOG</h1>
        </div>

        <div class="container idk_margin_top150">
            <div class="row">
                <?php
                    $query_b = $db->prepare("
                                    SELECT post_lang_title, post_lang_url, post_lang_content, post_lang_img, post_datetime
                                    FROM idk_posts
                                    LEFT JOIN idk_posts_lang ON idk_posts.post_id = idk_posts_lang.post_lang_postid
                                    WHERE post_status = :post_status AND post_lang_langid = :post_lang_langid AND post_featured = :post_featured
                                    GROUP BY post_id
                                    ORDER BY post_id DESC
                                    LIMIT 2");

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
                <div class="col-lg-6">
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
                <?php
                    $query_c = $db->prepare("
                                    SELECT post_lang_title, post_lang_url, post_lang_img, post_datetime
                                    FROM idk_posts
                                    LEFT JOIN idk_posts_lang ON idk_posts.post_id = idk_posts_lang.post_lang_postid
                                    WHERE post_status = :post_status AND post_lang_langid = :post_lang_langid AND post_featured = :post_featured
                                    GROUP BY post_id
                                    ORDER BY post_id DESC
                                    LIMIT 2,9999");

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
                <div class="col-lg-3">
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

        <footer>
            <?php include('footer.inc.php'); ?>
        </footer>

    </body>

</html>
