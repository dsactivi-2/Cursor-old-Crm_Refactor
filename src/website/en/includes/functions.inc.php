<?php
include("idkadmin/includes/connect.php");

ob_start();

//Error log enabled
ini_set('display_errors', 1);
ini_set('error_log', 'error_log');
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//PHPMailer
require 'mail/Exception.php';
require 'mail/PHPMailer.php';
require 'mail/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once($_SERVER["DOCUMENT_ROOT"] . "/includes/env.php");

date_default_timezone_set('Europe/Sarajevo');

//Get Language
Global $lang_id;
Global $db;

if(isset($_GET['lang'])){
    $lang_code = "bs";

    $query_lang = $db->prepare("
                    SELECT lang_id
                    FROM idk_langs
                    WHERE lang_code = :lang_code");

    $query_lang->execute(array(
                ':lang_code' => $lang_code));

    $row_lang = $query_lang->fetch();

    $lang_id = $row_lang['lang_id'];

    include('langs/' . $lang_code . '.php');

}else{

    $query_lang = $db->prepare("
                    SELECT lang_id, lang_code
                    FROM idk_langs
                    WHERE lang_default = :lang_default");

    $query_lang->execute(array(
                ':lang_default' => 1));

    $row_lang = $query_lang->fetch();

    $lang_id = $row_lang['lang_id'];
    $lang_code = $row_lang['lang_code'];

    include('langs/en.php');

}





//Update statistics
$stats_month = date('Y-m-01');

$check_date_query = $db->prepare("
							SELECT stats_id
							FROM idk_stats
							WHERE stats_month = :stats_month");

$check_date_query->execute(array(
						':stats_month' => $stats_month));

$check_date = $check_date_query->rowCount();

if($check_date > 0){

	$update_stats_query = $db->prepare("
							UPDATE idk_stats
							SET	stats_count = stats_count + 1
							WHERE stats_month = :stats_month");

	$update_stats_query->execute(array(
					':stats_month' => $stats_month));

}else{

	$add_stats_query = $db->prepare("
						INSERT INTO idk_stats
							(stats_month, stats_count)
						VALUES
							(:stats_month ,:stats_count)");

	$add_stats_query->execute(array(
					':stats_month' => $stats_month,
					':stats_count' => 1));
}



function sendEmail($mail_email, $mail_name, $mail_subject, $mail_body, $mail_altbody, $doc_cv, $doc_dip, $doc_doc) {

	$mail = new PHPMailer(true);

	try {
		//Server settings
		$mail->Host = 'mail.swk-group.com;mail.swk-group.com';
		$mail->SMTPAuth = true;
		$mail->Username = 'noreply@swk-group.com';
		$mail->Password = '@huZyX=&qKNW';
		$mail->SMTPSecure = 'TLS';
		$mail->Port = 465;
		$mail->CharSet = 'UTF-8';

        $mail->addAttachment('files/attachments/' . $doc_cv . '');
        $mail->addAttachment('files/attachments/' . $doc_dip . '');
        $mail->addAttachment('files/attachments/' . $doc_doc . '');

		//Recipients
		$mail->setFrom('noreply@swk-group.com', 'Upit sa web stranice');
		$mail->addAddress('info@swk-group.com', 'SWK');

		//Content
		$mail->isHTML(true);
		$mail->Subject = $mail_subject;
		$mail->Body    = $mail_body;
		$mail->AltBody = $mail_altbody;

		$mail->send();

	}catch (Exception $e){
		echo 'Message could not be sent.';
		echo 'Mailer Error: ' . $mail->ErrorInfo;
	}
}



function getSiteName() {

	Global $db;

	$settings_query = $db->prepare("
							SELECT settings_value
							FROM idk_settings
							WHERE settings_name = :settings_name");

	$settings_query->execute(array(
						':settings_name' => 'page_name'));

	$settings = $settings_query->fetch();

	$page_name = $settings['settings_value'];

	echo $page_name;

}



//Site url echo
function getSiteUrlFront() {
  Global $envConfig;

  echo $envConfig->WEBSITE_URL . "/en/";
}

function getSiteUrlFront1() {
  Global $envConfig;

  echo $envConfig->WEBSITE_URL . "/en/";
}



//Site url return

function getSiteUrlFrontr() {
  Global $envConfig;

  return $envConfig->WEBSITE_URL . "/en/";
}



function getSiteUrlFrontFiles() {
  Global $envConfig;

  echo $envConfig->WEBSITE_URL . "/en/";
}



//Site url return

function getSiteUrlFrontFilesr() {
  Global $envConfig;

  return $envConfig->WEBSITE_URL . "/en/";
}



//Site site menu

function getSiteMenu($nav_sub = 0) {

	Global $db;
	Global $lang_id;
	Global $lang_code;
	Global $lang_code;

	$site_url = getSiteUrlFrontr();

	$menu_query = $db->prepare("
						SELECT nav_id, nav_lang_name, nav_lang_link, nav_sub, nav_contentid
						FROM idk_navigation
						INNER JOIN idk_navigation_lang ON idk_navigation.nav_id = idk_navigation_lang.nav_lang_navid
						WHERE nav_lang_langid = :nav_lang_langid AND nav_sub = :nav_sub
						GROUP BY nav_id
						ORDER BY nav_sort ASC");

	$menu_query->execute(array(
					':nav_lang_langid' => $lang_id,
					':nav_sub' => $nav_sub));

	$count = $menu_query->rowCount();

	if($count == 0) {} else {

		echo "<ul>";

			while($menu_row = $menu_query->fetch()) {

				$nav_id =  $menu_row['nav_id'];
				$nav_lang_name =  $menu_row['nav_lang_name'];

                if($menu_row['nav_contentid'] == NULL){
                    $nav_lang_link =  $menu_row['nav_lang_link'];
                }else{

                    $nav_contentid = $menu_row['nav_contentid'];

                    $content_link_query = $db->prepare("
                						SELECT content_lang_url
                						FROM idk_content_lang
                						WHERE content_lang_langid = :content_lang_langid AND content_lang_postid = :content_lang_postid");

                    $content_link_query->execute(array(
                					':content_lang_langid' => $lang_id,
                					':content_lang_postid' => $nav_contentid));

                    $content_link_row = $content_link_query->fetch();

                    $nav_lang_link =  $site_url . $content_link_row['content_lang_url'];

                }

				echo "<li><a href=" . $nav_lang_link . ">" . $nav_lang_name . "</a>";
					getSiteMenu($nav_id);
				echo "</li>";
			}

		echo "</ul>";

	}

}



function GetContent($id){

    Global $db;
    Global $lang_id;

    $query = $db->prepare("
                    SELECT content_sub, content_sort, content_comment, content_share, content_status, content_datetime,
                            content_lang_name, content_lang_url, content_lang_content, content_lang_img, content_lang_seoname, content_lang_seodesc, content_lang_seokeywords
                    FROM idk_content
                    INNER JOIN idk_content_lang ON idk_content.content_id = idk_content_lang.content_lang_postid
                    WHERE content_id = :content_id AND content_lang_langid = :content_lang_langid");

    $query->execute(array(
                ':content_id' => $id,
                ':content_lang_langid' => $lang_id));

    $row = $query->fetch();

        Global $content_lang_name;
        $content_lang_name = $row['content_lang_name'];
        Global $content_lang_url;
        $content_lang_url = $row['content_lang_url'];
        Global $content_lang_content;
        $content_lang_content = $row['content_lang_content'];
        Global $content_lang_seoname;
        $content_lang_seoname = $row['content_lang_seoname'];
        Global $content_lang_seodesc;
        $content_lang_seodesc = $row['content_lang_seodesc'];
        Global $content_lang_seokeywords;
        $content_lang_seokeywords = $row['content_lang_seokeywords'];

        Global $content_sub;
        $content_sub = $row['content_sub'];
        Global $content_sort;
        $content_sort = $row['content_sort'];
        Global $content_comment;
        $content_comment = $row['content_comment'];
        Global $content_share;
        $content_share = $row['content_share'];

        Global $content_lang_img;

        if($row['content_lang_img'] == NULL){
            $content_lang_img = "none.jpg";
        }else{
            $content_lang_img = $row['content_lang_img'];
        }

    //Count +1
    $update_query = $db->prepare("
                    UPDATE idk_content
                    SET	content_count = content_count + 1
                    WHERE content_id = :content_id");

    $update_query->execute(array(
            ':content_id' => $id));

}



function getCategory($id){



    Global $db;

    Global $lang_id;



    $cat_query = $db->prepare("

						SELECT postcat_lang_name, postcat_lang_img, postcat_lang_url

						FROM idk_postcat

						INNER JOIN idk_postcat_lang ON idk_postcat.postcat_id = idk_postcat_lang.postcat_lang_postcatid

						WHERE postcat_lang_langid = :postcat_lang_langid AND postcat_id = :postcat_id AND postcat_status = :postcat_status

						GROUP BY postcat_id");



	$cat_query->execute(array(

					':postcat_lang_langid' => $lang_id,

					':postcat_id' => $id,

					':postcat_status' => 1));



	$cat = $cat_query->fetch();



    Global $postcat_lang_name;

    $postcat_lang_name = $cat['postcat_lang_name'];



    Global $postcat_lang_img;

    if($cat['postcat_lang_img'] == NULL){

        $postcat_lang_img = "none.jpg";

    }else{

        $postcat_lang_img = $cat['postcat_lang_img'];

    }



    Global $postcat_lang_url;

    $postcat_lang_url = $cat['postcat_lang_url'];



    //Count +1

    $update_query = $db->prepare("

                    UPDATE idk_postcat

                    SET	postcat_count = postcat_count + 1

                    WHERE postcat_id = :postcat_id");



    $update_query->execute(array(

            ':postcat_id' => $id));

}



function getProductCategory($id){



    Global $db;

    Global $lang_id;



    $cat_query = $db->prepare("

						SELECT productcat_id, productcat_lang_name

						FROM idk_productcat

						INNER JOIN idk_productcat_lang ON idk_productcat.productcat_id = idk_productcat_lang.productcat_lang_productid

						WHERE productcat_lang_langid = :productcat_lang_langid AND productcat_id = :productcat_id AND productcat_status = :productcat_status

						GROUP BY productcat_id");



	$cat_query->execute(array(

					':productcat_lang_langid' => $lang_id,

					':productcat_id' => $id,

					':productcat_status' => 1));



	$cat = $cat_query->fetch();



    Global $productcat_lang_name;

    $productcat_lang_name = $cat['productcat_lang_name'];



    //Count +1

    $update_query = $db->prepare("

                    UPDATE idk_productcat

                    SET	productcat_count = productcat_count + 1

                    WHERE productcat_id = :productcat_id");



    $update_query->execute(array(

            ':productcat_id' => $id));

}



function getProduct($id){



    Global $db;

    Global $lang_id;



    $query = $db->prepare("

                    SELECT product_brand, product_comment, product_share,

                            product_lang_name, product_lang_url, product_lang_desc, product_lang_img, product_lang_seoname, product_lang_seodesc, product_lang_seokeywords

                    FROM idk_products

                    INNER JOIN idk_products_lang ON idk_products.product_id = idk_products_lang.product_lang_productid

                    WHERE product_id = :product_id AND product_lang_langid = :product_lang_langid AND product_status = :product_status");



    $query->execute(array(

                ':product_id' => $id,

                ':product_lang_langid' => $lang_id,

                ':product_status' => 1));



    $row = $query->fetch();



        Global $product_lang_name;

        $product_lang_name = $row['product_lang_name'];

        Global $product_lang_url;

        $product_lang_url = $row['product_lang_url'];

        Global $product_lang_desc;

        $product_lang_desc = $row['product_lang_desc'];

        Global $product_lang_seoname;

        $product_lang_seoname = $row['product_lang_seoname'];

        Global $product_lang_seodesc;

        $product_lang_seodesc = $row['product_lang_seodesc'];

        Global $product_lang_seokeywords;

        $product_lang_seokeywords = $row['product_lang_seokeywords'];



        Global $product_brand;

        $product_brand = $row['product_brand'];

        Global $product_comment;

        $product_comment = $row['product_comment'];

        Global $product_share;

        $product_share = $row['product_share'];



        Global $product_lang_img;



        if($row['product_lang_img'] == NULL){

            $product_lang_img = "none.jpg";

        }else{

            $product_lang_img = $row['product_lang_img'];

        }



    //Count +1

    $update_query = $db->prepare("

                    UPDATE idk_products

                    SET	product_count = product_count + 1

                    WHERE product_id = :product_id");



    $update_query->execute(array(

            ':product_id' => $id));



}



function getProductsCategories($productcat_sub = 0){

	global $db;

	global $lang_id;



	$cat_query = $db->prepare("

						SELECT productcat_id, productcat_lang_name, productcat_sub, productcat_lang_url

						FROM idk_productcat

						INNER JOIN idk_productcat_lang ON idk_productcat.productcat_id = idk_productcat_lang.productcat_lang_productid

						WHERE productcat_lang_langid = :productcat_lang_langid AND productcat_sub = :productcat_sub AND productcat_status = :productcat_status

						GROUP BY productcat_id");



	$cat_query->execute(array(

					':productcat_lang_langid' => $lang_id,

					':productcat_sub' => $productcat_sub,

					':productcat_status' => 1));



	echo '<ul class="list-unstyled">';



	while ($cat = $cat_query->fetch()) {



		$productcat_id = $cat['productcat_id'];

		$productcat_lang_name = $cat['productcat_lang_name'];

		$productcat_lang_url = $cat['productcat_lang_url'];

		$productcat_sub = $cat['productcat_sub'];



        echo '<li><a href="' . getSiteUrlFrontr() . '' . $productcat_lang_url . '"><i class="fas fa-chevron-right"></i> ' . $productcat_lang_name . '</a></li>';

        getProductsCategories($productcat_id);



	}



	echo "</ul>";

}



function getPostCategories(){



	global $db;

	global $lang_id;



	$cat_query = $db->prepare("

						SELECT postcat_id, postcat_lang_name, postcat_lang_img, postcat_lang_url

						FROM idk_postcat

						INNER JOIN idk_postcat_lang ON idk_postcat.postcat_id = idk_postcat_lang.postcat_lang_postcatid

						WHERE postcat_lang_langid = :postcat_lang_langid AND postcat_status = :postcat_status

						GROUP BY postcat_id");



	$cat_query->execute(array(

					':postcat_lang_langid' => $lang_id,

					':postcat_status' => 1));



	echo '<ul class="list-unstyled">';



	while ($cat = $cat_query->fetch()) {



		$postcat_id = $cat['postcat_id'];

		$postcat_lang_name = $cat['postcat_lang_name'];

		$postcat_lang_img = $cat['postcat_lang_img'];

		$postcat_lang_url = $cat['postcat_lang_url'];



        echo '<li><a href="' . getSiteUrlFrontr() . '' . $postcat_lang_url . '"><i class="fas fa-chevron-right"></i> ' . $postcat_lang_name . '</a></li>';



	}



	echo "</ul>";

}



function getCatalogs(){



	global $db;

	global $lang_id;



    $query = $db->prepare("

                    SELECT catalog_lang_name, catalog_lang_file

                    FROM idk_catalogs

                    LEFT JOIN idk_catalogs_lang ON idk_catalogs.catalog_id = idk_catalogs_lang.catalog_lang_catalogid

                    WHERE catalog_status = :catalog_status AND catalog_lang_langid = :catalog_lang_langid

                    GROUP BY catalog_id

                    ORDER BY catalog_sort ASC");



	$query->execute(array(

					':catalog_lang_langid' => $lang_id,

					':catalog_status' => 1));



	echo '<ul class="list-unstyled">';



	while ($row = $query->fetch()) {



		$catalog_lang_name = $row['catalog_lang_name'];

		$catalog_lang_file = $row['catalog_lang_file'];



        echo '<li><a href="' . getSiteUrlFrontFilesr() . 'files/catalogs-document/' . $catalog_lang_file . '"" target="_blank><i class="fas fa-chevron-right"></i> ' . $catalog_lang_name . '</a></li>';



	}



	echo "</ul>";

}



function getBrands(){

	global $db;

	global $lang_proizvodi1;

	global $lang_code;



	$query = $db->prepare("

						SELECT product_brand

						FROM idk_products

						WHERE product_status = :product_status AND product_brand IS NOT NULL AND product_brand != :product_brand

						GROUP BY product_brand

                        ORDER BY product_brand ASC");



	$query->execute(array(

					':product_status' => 1,

					':product_brand' => ''));



	echo '<ul class="list-unstyled">';



	while ($row = $query->fetch()) {



		$product_brand = $row['product_brand'];



        echo '<li><a href="https://www.latex.ba/proizvodi.php?lang=' . $lang_code . '&brand=' . $product_brand . '"><i class="fas fa-chevron-right"></i> ' . $product_brand . '</a></li>';



	}



	echo "</ul>";

}



function GetPost($id){



    Global $db;

    Global $lang_id;



    $query = $db->prepare("

                    SELECT post_author, post_comment, post_share, post_datetime,

                            post_lang_title, post_lang_url, post_lang_content, post_lang_img, post_lang_seoname, post_lang_seodesc, post_lang_seokeywords

                    FROM idk_posts

                    INNER JOIN idk_posts_lang ON idk_posts.post_id = idk_posts_lang.post_lang_postid

                    WHERE post_id = :post_id AND post_lang_langid = :post_lang_langid AND post_status = :post_status");



    $query->execute(array(

                ':post_id' => $id,

                ':post_lang_langid' => $lang_id,

                ':post_status' => 1));



    $row = $query->fetch();



        Global $post_author;

        $post_author = $row['post_author'];

        Global $post_comment;

        $post_comment = $row['post_comment'];

        Global $post_share;

        $post_share = $row['post_share'];

        Global $post_datetime;

        $post_datetime = date('d.m.Y.', strtotime($row['post_datetime']));

        Global $post_lang_title;

        $post_lang_title = $row['post_lang_title'];

        Global $post_lang_url;

        $post_lang_url = $row['post_lang_url'];

        Global $post_lang_content;

        $post_lang_content = $row['post_lang_content'];



        Global $post_lang_img;

        if($row['post_lang_img'] == NULL){

            $post_lang_img = "none.jpg";

        }else{

            $post_lang_img = $row['post_lang_img'];

        }



        Global $post_lang_seoname;

        $post_lang_seoname = $row['post_lang_seoname'];

        Global $post_lang_seodesc;

        $post_lang_seodesc = $row['post_lang_seodesc'];

        Global $post_lang_seokeywords;

        $post_lang_seokeywords = $row['post_lang_seokeywords'];



    //Count +1

    $update_query = $db->prepare("

                    UPDATE idk_posts

                    SET	post_count = post_count + 1

                    WHERE post_id = :post_id");



    $update_query->execute(array(

            ':post_id' => $id));



}



function GetContactSettingsValue($var){



    Global $db;

    Global $lang_id;



    $query = $db->prepare("

                    SELECT settings_form_lang_value

                    FROM idk_settings_form

                    INNER JOIN idk_settings_form_lang ON idk_settings_form.settings_form_id = idk_settings_form_lang.settings_form_lang_settingsformid

                    WHERE settings_form_variable = :settings_form_variable AND settings_form_lang_langid = :settings_form_lang_langid");



    $query->execute(array(

                ':settings_form_variable' => $var,

                ':settings_form_lang_langid' => $lang_id));



    $row = $query->fetch();



        echo $settings_form_lang_value = $row['settings_form_lang_value'];



}



function GetContactSettingsName($var){



    Global $db;

    Global $lang_id;



    $query = $db->prepare("

                    SELECT settings_form_lang_name

                    FROM idk_settings_form

                    INNER JOIN idk_settings_form_lang ON idk_settings_form.settings_form_id = idk_settings_form_lang.settings_form_lang_settingsformid

                    WHERE settings_form_variable = :settings_form_variable AND settings_form_lang_langid = :settings_form_lang_langid");



    $query->execute(array(

                ':settings_form_variable' => $var,

                ':settings_form_lang_langid' => $lang_id));



    $row = $query->fetch();



        echo $settings_form_lang_name = $row['settings_form_lang_name'];



}







?>
