<?php
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

//Connect to db
ob_start();
include("includes/connect.php");

//User LogIn Session
if(isset($_COOKIE['idk_cms_session'])){

	Global $db;
	$user_key = $_COOKIE['idk_cms_session'];

	$query = $db->prepare("
					SELECT user_id
					FROM idk_users
					WHERE user_key = :user_key");

	$query->execute(array(
				':user_key' => $user_key));

	$user = $query->fetch();

	Global $logged_user_id;
	$logged_user_id = $user['user_id'];
}else{
	$logged_user_id = 0;
}

//Language
include("lang/bs.php");

//Site URL
function getSiteUrl() {
  Global $envConfig;
  echo $envConfig->WEBSITE_URL . "/bs/idkadmin/";
}

//Site url return
function getSiteUrlr() {
  Global $envConfig;
  return $envConfig->WEBSITE_URL . "/bs/idkadmin/";
}

function getSiteUrlFront() {
  Global $envConfig;
  echo $envConfig->WEBSITE_URL . "/bs/";
}

//Site url return
function getSiteUrlFrontr() {
  Global $envConfig;
  return $envConfig->WEBSITE_URL . "/bs/";
}

//Get Name of CRM
function getSiteNamer() {

	Global $db;

	$settings_query = $db->prepare("
							SELECT setting_value
							FROM idk_settings
							WHERE setting_name = :setting_name");

	$settings_query->execute(array(
						':setting_name' => 'site_name'));

	$settings = $settings_query->fetch();

	return $site_name = $settings['setting_value'];

}

//Mark notification as read
if(isset($_GET['nid'])) {
	$notification_id = $_GET['nid'];

	$query_update = $db->prepare("
						UPDATE idk_notifications
						SET	notification_status = :notification_status
						WHERE notification_id = :notification_id AND notification_datetime <= NOW()");

	$query_update->execute(array(
					':notification_status' => 2,
					':notification_id' => $notification_id));
}

//Copyright
function getCopyright() {
	echo "<p>©" . date('Y') . " Sva prava pridržana - IDK Studio d.o.o. | Licenca za " . getSiteNamer() . " - Bez prava daljnje distribucije.</p>";
}

function getTitle(){
	echo "" . getSiteNamer() . " - IDK CMS Admin";
}

//All functions
function getUserFullname() {

	Global $db;
	Global $logged_user_id;

	$query = $db->prepare("
					SELECT user_fullname
					FROM idk_users
					WHERE user_id = :user_id");

	$query->execute(array(
				':user_id' => $logged_user_id));

	$row = $query->fetch();

	echo $row['user_fullname'];

}

function getUserImage() {

	Global $db;
	Global $logged_user_id;

	$query = $db->prepare("
					SELECT user_image
					FROM idk_users
					WHERE user_id = :user_id");

	$query->execute(array(
				':user_id' => $logged_user_id));

	$row = $query->fetch();

	if($row['user_image'] == "none" OR $row['user_image'] == NULL){
		echo "none.jpg";
	}else{
		echo $row['user_image'];
	}

}

function getUserStatus() {

	Global $db;
	Global $logged_user_id;

	$query = $db->prepare("
					SELECT user_status
					FROM idk_users
					WHERE user_id = :user_id");

	$query->execute(array(
			':user_id' => $logged_user_id));

	$row = $query->fetch();

	return $row['user_status'];
}

function getUserStatusName() {

	Global $db;
	Global $logged_user_id;

	$query = $db->prepare("
					SELECT user_status
					FROM idk_users
					WHERE user_id = :user_id");

	$query->execute(array(
			':user_id' => $logged_user_id));

	$row = $query->fetch();

	$user_status = $row['user_status'];

	if($user_status == 1){
		echo "Administrator";
	}elseif($user_status == 2){
		echo "Super korisnik";
	}else{
		echo "Korisnik";
	}
}


function sendEmail($mail_email, $mail_name, $mail_subject, $mail_body, $mail_altbody) {

	$mail = new PHPMailer(true);                              // Passing `true` enables exceptions
	try {
		//Server settings
		$mail->Host = 'idkcrm.com;idkcrm.com';  // Specify main and backup SMTP servers
		$mail->SMTPAuth = true;                               // Enable SMTP authentication
		$mail->Username = 'noreply@idkcrm.com';                 // SMTP username
		$mail->Password = 'hhWno3RW@2';                           // SMTP password
		$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
		$mail->Port = 465;                                    // TCP port to connect to
		$mail->CharSet = 'UTF-8';

		//Recipients
		$mail->setFrom('noreply@idkcrm.com', 'IDK CRM');
		$mail->addAddress($mail_email, $mail_name);     // Add a recipient

		//Content
		$mail->isHTML(true);                                  // Set email format to HTML
		$mail->Subject = $mail_subject;
		$mail->Body    = $mail_body;
		$mail->AltBody = $mail_altbody;

		$mail->send();

	}catch (Exception $e){
		echo 'Message could not be sent.';
		echo 'Mailer Error: ' . $mail->ErrorInfo;
	}

}

function getAdminDate() {

	$day = date("l");

	if($day == "Monday"){
		$day = "Ponedjeljak";
	}elseif($day == "Tuesday"){
		$day = "Utorak";
	}elseif($day == "Wednesday"){
		$day = "Srijeda";
	}elseif($day == "Thursday"){
		$day = "Četvrtak";
	}elseif($day == "Friday"){
		$day = "Petak";
	}elseif($day == "Saturday"){
		$day = "Subota";
	}elseif($day == "Sunday"){
		$day = "Nedjelja";
	}

	$todayDate = date("g:i a");
	//$currentTime = time($todayDate);

	echo $day, date(", d.m.Y.");

}

function create_slug($slug){
	$bad = array('Š','Ž','š','ž','ć','Ć','č','Č','đ','Đ','Ä','Ö','Ü','ẞ','ä','ö','ü','ß',' ','"',',','.',':');
	$good = array('S','Z','s','z','c','C','c','C','d','D','A','O','U','S','a','o','u','s','-','','','','');

	$slug = str_replace($bad, $good, $slug);
	$slug = preg_replace('~[^\\pL\d]+~u', '-', $slug);
	$slug = trim($slug, '-');
	$slug = iconv('utf-8', 'us-ascii//TRANSLIT', $slug);
	$slug = strtolower($slug);
	$slug = preg_replace('~[^-\w]+~', '', $slug);

	if(empty($slug)){
		return 'n-a';
	}

	return $slug;
}

function create_keywords($keywords) {
	$bad = array('Š','Ž','š','ž','ć','Ć','č','Č','đ','Đ','Ä','Ö','Ü','ẞ','ä','ö','ü','ß');
	$good = array('S','Z','s','z','c','C','c','C','d','D','A','O','U','S','a','o','u','s');

	$keywords = str_replace($bad, $good, $keywords);

    preg_match_all("/[a-z0-9\-]{4,}/i", $keywords, $output_array);

    if(is_array($output_array) && count($output_array[0])) {
        return strtolower(implode(',', $output_array[0]));
    } else {
        return '';
    }
}

function get_string_between($string, $start, $end){
	$string = ' ' . $string;
	$ini = strpos($string, $start);
	if ($ini == 0) return '';
	$ini += strlen($start);
	$len = strpos($string, $end, $ini) - $ini;
	return substr($string, $ini, $len);
}

function getCategories($productcat_sub = 0)
{
	global $db;

	$cat_query = $db->prepare("
						SELECT productcat_id, productcat_lang_name, productcat_sub
						FROM idk_productcat
						INNER JOIN idk_productcat_lang ON idk_productcat.productcat_id = idk_productcat_lang.productcat_lang_productid
						WHERE productcat_lang_langid = :productcat_lang_langid AND productcat_sub = :productcat_sub AND productcat_status != :productcat_status
						GROUP BY productcat_id");

	$cat_query->execute(array(
					':productcat_lang_langid' => 1,
					':productcat_sub' => $productcat_sub,
					':productcat_status' => 0));

	echo "<ul>";

	while ($cat = $cat_query->fetch()) {

		$productcat_id = $cat['productcat_id'];
		$productcat_lang_name = $cat['productcat_lang_name'];
		$productcat_sub = $cat['productcat_sub'];

		echo "<li class='list-group-item vb_hoverbg'>
					<div class='pull-left' style='line-height: 34px;'>" . $productcat_lang_name . "</div>
					<div class='pull-right text-center'>
						<div class='btn-group material-btn-group'>
						<button
						class='dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table'
						data-toggle='dropdown'><i class='fa fa-cogs fa-lg' aria-hidden='true'></i> <span class='caret material-btn__caret'></span></button>
							<ul class='dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table' role='menu'>

							<li>
							<a href='product-category?page=edit&id=" . $productcat_id . "'
							class='material-dropdown-menu__link'><i class='fa fa-pencil-square-o'
								aria-hidden='true'></i> Uredi</a>
							</li>

							<li class='idk_dropdown_danger'>
							<a onclick='getHref()' href='#' data='product-category?page=archive&id=" . $productcat_id . "' data-toggle='modal' data-target='#modalDelete'
							class='obrisi material-dropdown-menu__link'><i class='fa fa-trash'
								aria-hidden='true'></i> Obriši</a>
							</li>

							</ul>
						</div>
					</div><div class='clearfix'></div></li>";

		getCategories($productcat_id);
	}

	echo "</ul>";
}

function getCatOption($cat_sub = 0) {

	Global $db;
	$cat_query = $db->prepare("
						SELECT productcat_id, productcat_lang_name
						FROM idk_productcat
						LEFT JOIN idk_productcat_lang ON idk_productcat.productcat_id = idk_productcat_lang.productcat_lang_productid
						WHERE productcat_lang_langid = :productcat_lang_langid AND productcat_status != :productcat_status AND productcat_sub = :productcat_sub
						GROUP BY productcat_id");

	$cat_query->execute(array(
					':productcat_lang_langid' => 1,
					':productcat_status' => 0,
					':productcat_sub' => $cat_sub));

	while($cat = $cat_query->fetch()) {

		if($cat_sub == 0){
			echo "<option value='" . $cat['productcat_id'] . "'>" . $cat['productcat_lang_name'] . "";
					getCatOption($cat['productcat_id']);
			echo "</option>";

		}else{
			echo "<option value='" . $cat['productcat_id'] . "'>&nbsp;&nbsp;&nbsp;&nbsp;" . $cat['productcat_lang_name'] . "";
					getCatOption($cat['productcat_id']);
			echo "</option>";
		}

	}
}

function getCatOptionEdit($cat_sub = 0) {

	Global $db;
	Global $product_id;

	$cat_query = $db->prepare("
						SELECT productcat_id, productcat_lang_name, productcat_relation_catid
						FROM idk_productcat
						LEFT JOIN idk_productcat_lang ON idk_productcat.productcat_id = idk_productcat_lang.productcat_lang_productid
						LEFT JOIN idk_productcat_relation ON idk_productcat.productcat_id = idk_productcat_relation.productcat_relation_catid AND productcat_relation_productid = :productcat_relation_productid
						WHERE productcat_lang_langid = :productcat_lang_langid AND productcat_status != :productcat_status AND productcat_sub = :productcat_sub
						GROUP BY productcat_id");

	$cat_query->execute(array(
					':productcat_lang_langid' => 1,
					':productcat_status' => 0,
					':productcat_sub' => $cat_sub,
					':productcat_relation_productid' => $product_id));

	while($cat = $cat_query->fetch()) {

		if($cat['productcat_id'] == $cat['productcat_relation_catid']) { $selected = "selected"; }else{ $selected = ""; }

		if($cat_sub == 0){

			echo "<option value='" . $cat['productcat_id'] . "' " . $selected . ">" . $cat['productcat_lang_name'] . "";
					getCatOptionEdit($cat['productcat_id']);
			echo "</option>";

		}else{

			echo "<option value='" . $cat['productcat_id'] . "' " . $selected . ">&nbsp;&nbsp;&nbsp;&nbsp;" . $cat['productcat_lang_name'] . "";
					getCatOptionEdit($cat['productcat_id']);
			echo "</option>";
		}

	}
}

function getNavigation($nav_sub = 0)
{
	global $db;

	$query = $db->prepare("
						SELECT nav_id, nav_lang_name, nav_sub
						FROM idk_navigation
						INNER JOIN idk_navigation_lang ON idk_navigation.nav_id = idk_navigation_lang.nav_lang_navid
						WHERE nav_lang_langid = :nav_lang_langid AND nav_sub = :nav_sub
						GROUP BY nav_id
						ORDER BY nav_sort ASC");

	$query->execute(array(
					':nav_lang_langid' => 1,
					':nav_sub' => $nav_sub));

	echo "<ul>";

	while ($row = $query->fetch()) {

		$nav_id = $row['nav_id'];
		$nav_lang_name = $row['nav_lang_name'];
		$nav_sub = $row['nav_sub'];

		echo "<li class='list-group-item vb_hoverbg'>
					<div class='pull-left' style='line-height: 34px;'>" . $nav_lang_name . "</div>
					<div class='pull-right text-center'>
						<div class='btn-group material-btn-group'>
						<button
						class='dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table'
						data-toggle='dropdown'><i class='fa fa-cogs fa-lg' aria-hidden='true'></i> <span class='caret material-btn__caret'></span></button>
							<ul class='dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table' role='menu'>

							<li>
							<a href='navigation?page=edit&id=" . $nav_id . "'
							class='material-dropdown-menu__link'><i class='fa fa-pencil-square-o'
								aria-hidden='true'></i> Uredi</a>
							</li>

							<li class='idk_dropdown_danger'>
							<a onclick='getHref()' href='#' data='navigation?page=archive&id=" . $nav_id . "' data-toggle='modal' data-target='#modalDelete'
							class='obrisi material-dropdown-menu__link'><i class='fa fa-trash'
								aria-hidden='true'></i> Obriši</a>
							</li>

							</ul>
						</div>
					</div><div class='clearfix'></div></li>";

		getNavigation($nav_id);
	}

	echo "</ul>";
}

?>
