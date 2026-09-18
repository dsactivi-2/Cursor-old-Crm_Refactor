<?php
//Error log enabled
ini_set('display_errors', 0);
ini_set('error_log', 'error_log');
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//PHPMailer
require $_SERVER['DOCUMENT_ROOT'].'/mail/Exception.php';
require $_SERVER['DOCUMENT_ROOT'].'/mail/PHPMailer.php';
require $_SERVER['DOCUMENT_ROOT'].'/mail/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Env
require_once 'env.php';

if($envConfig->APP_ENV == "dev")
{
    ini_set("log_errors", 1);
}

//Connect to db
ob_start();
include($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");
include("one-signal.php");
date_default_timezone_set('Europe/Sarajevo');

	/***************************************
	GET USER IP
	**************************************/
	if(isset($_COOKIE['idk_session'])){

		$ip_blocker_status = getIpWhitelistStatusR();

		Global $getUserIp;
		if($ip_blocker_status == 1){
			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
				$getUserIp = $_SERVER['HTTP_CLIENT_IP'];
			} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$getUserIp = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$getUserIp = $_SERVER['REMOTE_ADDR'];
			}
		}else{
			$getUserIp = "Noip";
		}
		/***************************************
		IP WHITELIST
		**************************************/
		Global $getIpWhiteList;

		if($ip_blocker_status == 1){
			$query_ips = $db->prepare("
							SELECT ipwl_ip
							FROM idk_ipwhitelist
							WHERE ipwl_status = :ipwl_status");

			$query_ips->execute(array(
						':ipwl_status' => 0));

			$getIpWhiteList = array();
			while($ips = $query_ips->fetch()){
				$ipwl_ip = $ips['ipwl_ip'];
				$getIpWhiteList[] = $ipwl_ip;
			}
		}else{
			$getIpWhiteList[] = "Noip";
		}
	}else{
		$getUserIp = "nema_sesije";
		$getIpWhiteList[] = "Noip";
	}


//User LogIn Session
if(isset($_COOKIE['idk_session'])){

	Global $db;
	$employee_key = $_COOKIE['idk_session'];

	$query = $db->prepare("
					SELECT employee_id
					FROM idk_employees
					WHERE employee_key = :employee_key AND employee_reset_password = 0");

	$query->execute(array(
				':employee_key' => $employee_key));

	$employee = $query->fetch();

	if($query->rowCount()>0){
		Global $logged_employee_id;
		$logged_employee_id = $employee['employee_id'];
	}else{
		unset($_COOKIE['idk_session']);
		setcookie('idk_session', '', time() - 60 * 60 * 24 * 30);

		header("Location: login.php");
		exit();
	}
}else{
	$logged_employee_id = 0;
}

//User LogIn Session - department
if(isset($_COOKIE['idk_session'])){

	Global $db;
	$employee_department_key = $_COOKIE['idk_session'];

	$query_department = $db->prepare("
					SELECT employee_odjel
					FROM idk_employees
					WHERE employee_key = :employee_key");

	$query_department->execute(array(
				':employee_key' => $employee_department_key));

	$employee_department = $query_department->fetch();

	Global $logged_employee_department_id;
	$logged_employee_department_id = $employee_department['employee_odjel'];
}else{
	$logged_employee_department_id = 0;
}

//Language
$query_language = $db->prepare("
				SELECT employee_dlanguage
				FROM idk_employees
				WHERE employee_id = :employee_id");

$query_language->execute(array(
			':employee_id' => $logged_employee_id));

$langiage = $query_language->fetch();

if($langiage['employee_dlanguage'] == "bs"){
	include($_SERVER['DOCUMENT_ROOT']."/lang/bs.php");
}else if($langiage['employee_dlanguage'] == "de"){
	include($_SERVER['DOCUMENT_ROOT']."/lang/de.php");
}

//Site URL
function getSiteUrl() {
  Global $envConfig;
  echo $envConfig->CRM_URL;
}

//Site url return
function getSiteUrlr() {
  Global $envConfig;
  return $envConfig->CRM_URL;
}

//Get url of join folder
function getJoinUrlr(){
  Global $envConfig;
  return $envConfig->JOIN_URL;
}

// GET GROUP LIST FOR CANDIDATES
function getGroupList(){
	Global $db;

	$query = $db->prepare("
					SELECT kg_id, kg_title, kg_date
					FROM idk_kandidati_grupe
					WHERE kg_status = 0
					ORDER BY kg_id DESC
					");

	$query->execute();

	while($row = $query->fetch()){

		$kg_id = $row['kg_id'];
		$kg_title = $row['kg_title'];

		echo '<option value="'.$kg_id.'">'.$kg_title.'</option>';

	}

}

// GET PROJECT LIST FOR CANDIDATES
function getProjectList(){
	Global $db;

	$select_query = $db->prepare("
						SELECT project_id, project_name, project_status
						FROM idk_projects
						WHERE project_status != 0");

	$select_query->execute();

	while($select_row = $select_query->fetch()) {
		echo "<option value='" . $select_row['project_id'] . "'>" . $select_row['project_name'] . "</option>";
	}

}

function getPartnerIdFromToken($token){

	Global $db;
	// POKUPI INFORMACIJU O IDU-U PARTNERA NA OSNOVU TOKENA
	$query_partner = $db->prepare("
				SELECT jp_id
				FROM idk_jobstep_partners
				WHERE jp_mailconfirmation_token = :jp_mailconfirmation_token");

	$query_partner->execute(array(
					':jp_mailconfirmation_token' => $token));

	$row = $query_partner->fetch();

	return $row['jp_id'];
}

// GET NALOG
function getNalogList(){
	Global $db;

	$query = $db->prepare("
                        SELECT nalog_id, nalog_broj, nalog_naziv, nalog_opis
                        FROM idk_nalozi
                        WHERE nalog_status NOT IN (8,12)");

	$query->execute();

	while($row = $query->fetch()){

        $nalog_id = $row['nalog_id'];
		$nalog_broj = $row['nalog_broj'];
		$nalog_naziv = $row['nalog_naziv'];

		echo "<option value='" . $row['nalog_id'] . "'>" . $row['nalog_broj'] . " - " . $row['nalog_naziv'] . "</option>";
	}

}

// EDIT NALOG
function getNalogListEdit($lg_nalogid){
	Global $db;

	$query = $db->prepare("
                        SELECT nalog_id, nalog_broj, nalog_naziv, nalog_opis
                        FROM idk_nalozi
                        WHERE nalog_status NOT IN (8,12)");

	$query->execute(array());

	while($row = $query->fetch()){

        $nalog_id = $row['nalog_id'];
		$nalog_broj = $row['nalog_broj'];
		$nalog_naziv = $row['nalog_naziv'];

		if($nalog_id == $lg_nalogid){
			$selected_or_not = "selected";
		}else{
			$selected_or_not = "";
		}

		echo "<option value='" . $row['nalog_id'] . "' ".$selected_or_not.">" . $row['nalog_broj'] . " - " . $row['nalog_naziv'] . "</option>";
	}

}

// GET NUMBER OF CANDIDATES IN PROJECT
function getNumberOfCandidatesProject($project_id){

	Global $db;

	$query = $db->prepare("
					SELECT kandidat_id, kandidat_ime, kandidat_prezime, kandidat_spol, kandidat_jmbg, kandidat_status, kandidat_slika, kandidat_email, kandidat_datetime, kandidat_visitedurl, kandidat_prijava_na, kandidat_group, cv_ba, cv_de
					FROM idk_kandidati
					INNER JOIN idk_project_kandidati ON idk_kandidati.kandidat_id = idk_project_kandidati.pk_kandidatid
					WHERE pk_projectid = :pk_projectid AND kandidat_status !=3
					GROUP BY pk_kandidatid");

	$query->execute(array(':pk_projectid' => $project_id));

	$cont = $query->rowCount();

	if($cont > 0){
		return '<span class="label label-success">'.$cont.'</span>';
	}else{
		return '<span class="label label-warning">'.$cont.'</span>';
	}
}

// GET DOUBLE EXISTENCE
function getDoubleExistence($kandidat_ime, $kandidat_prezime, $kandidat_datumrodjenja){

	Global $db;

	$query = $db->prepare("
					SELECT kandidat_id
					FROM idk_kandidati
					WHERE kandidat_ime = :kandidat_ime AND kandidat_prezime = :kandidat_prezime AND kandidat_datumrodjenja = :kandidat_datumrodjenja AND kandidat_status !=3");

	$query->execute(array(
			':kandidat_ime' => $kandidat_ime,
			':kandidat_datumrodjenja' => $kandidat_datumrodjenja,
			':kandidat_prezime' => $kandidat_prezime));

	$cont = $query->rowCount();

	if($cont > 1){
		return 1;
	}else{
		return 0;
	}
}

function getIdsOfCandidatesProject($project_id){

	Global $db;

	$ids = array();

	$query = $db->prepare("
					SELECT kandidat_id, kandidat_ime, kandidat_prezime, kandidat_spol, kandidat_jmbg, kandidat_status, kandidat_slika, kandidat_email, kandidat_datetime, kandidat_visitedurl, kandidat_prijava_na, kandidat_group, cv_ba, cv_de
					FROM idk_kandidati
					INNER JOIN idk_project_kandidati ON idk_kandidati.kandidat_id = idk_project_kandidati.pk_kandidatid
					WHERE pk_projectid = :pk_projectid AND kandidat_status !=3");

	$query->execute(array(':pk_projectid' => $project_id));

	while($row = $query->fetch()){
		array_push($ids, $row['kandidat_id']);
	}
	return $ids;
}

// GET STATUS LIST
function getStatusList(){
	Global $db;

	echo '<option value="0" selected>Na provjeri</option>';
	echo '<option value="1" selected>U obradi</option>';
	echo '<option value="2" selected>Obrađen</option>';
	echo '<option value="3" selected>Arhiviran</option>';
	echo '<option value="4" selected>Kontrola</option>';
	echo '<option value="5" selected>Dopuna</option>';
	echo '<option value="6" selected>Odbio Messenger</option>';
	echo '<option value="7" selected>U obradi više od 3 dana</option>';
	echo '<option value="8" selected>Na dopuni više od 3 dana</option>';

}

function getStatusListNew(){
	Global $db;

	echo '<option value="0">Na provjeri</option>';
	echo '<option value="1">U obradi</option>';
	echo '<option value="5">Dopuna</option>';
	echo '<option value="4">Kontrola</option>';
	echo '<option value="2">Obrađen</option>';
	echo '<option value="7">U obradi više od 3 dana</option>';
	echo '<option value="8">Na dopuni više od 3 dana</option>';
	echo '<option value="6">Odbio messenger</option>';
	echo '<option value="3">Arhiviran</option>';

}

//Get Name of CRM
function getCrmNamer() {

	Global $db;

	$settings_query = $db->prepare("
							SELECT settings_value
							FROM idk_settings
							WHERE settings_name = :settings_name");

	$settings_query->execute(array(
						':settings_name' => 'crm_name'));

	$settings = $settings_query->fetch();

	return $crm_name = $settings['settings_value'];

}

//Get Name of subdomain
function getSubdomainr() {

	Global $db;

	$settings_query = $db->prepare("
							SELECT settings_value
							FROM idk_settings
							WHERE settings_name = :settings_name");

	$settings_query->execute(array(
						':settings_name' => 'crm_subdomain'));

	$settings = $settings_query->fetch();

	return $crm_subdomain = $settings['settings_value'];

}

//Get Folder size CRM
function getCrmFolderSizer() {

	Global $db;

	$settings_query = $db->prepare("
							SELECT settings_value
							FROM idk_settings
							WHERE settings_name = :settings_name");

	$settings_query->execute(array(
						':settings_name' => 'crm_folder_size'));

	$settings = $settings_query->fetch();

	return $crm_folder_size = $settings['settings_value'];

}

//Get Number of users
function getNumberUsers() {

	Global $db;

	$query = $db->prepare("
					SELECT COUNT(employee_id) AS employee_total
					FROM idk_employees
					WHERE employee_status != :employee_status");

	$query->execute(array(
			':employee_status' => 0));

	$row = $query->fetch();

	return $row['employee_total'];
}

//Get Number of users
function getDBSize() {

	Global $db;

	$size = 0;
	$query = $db->prepare("SHOW TABLE STATUS");
	$query->execute();
	$result = $query->fetchAll();

	foreach ($result as $row){
		$size += $row["Data_length"] + $row["Index_length"];
	}
	return $size;
}

//Copyright
function getCopyright() {
	// if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		// $ip = $_SERVER['HTTP_CLIENT_IP'];
	// } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		// $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	// } else {
		// $ip = $_SERVER['REMOTE_ADDR'];
	// }
	// $ipdat = @json_decode(file_get_contents( 
		// "http://www.geoplugin.net/json.gp?ip=" . $ip)); 
	   
	// $countryCode = strtolower($ipdat->geoplugin_countryCode);

	// if($countryCode == "de")
		// include("lang/de.php");
	// else
		include($_SERVER["DOCUMENT_ROOT"] . "/lang/bs.php");
	echo "<p>©" . date('Y') . " ".$txt_prava." - Jobstep IT Solutions - ".$txt_verzija." 1.15.5 </p>";
}

function getTitle(){
	echo "" . getCrmNamer();
}

//Create trigger
function createTrigger($url_link, $url_datetime, $url_tag){

	Global $db;

	$query = $db->prepare("
					INSERT INTO idk_triggerurl
						(url_link, url_datetime, url_tag)
					VALUES
						(:url_link, :url_datetime, :url_tag)");

	$query->execute(array(
					':url_link' => $url_link,
					':url_datetime' => $url_datetime,
					':url_tag' => $url_tag));
}

//Remove trigger
function removeTrigger($url_tag){

	Global $db;

	$query = $db->prepare("
						DELETE FROM idk_triggerurl
						WHERE url_tag = :url_tag");

	$query->execute(array(
				':url_tag' => $url_tag));
}


//All functions
function getEmployeeFullname() {

	Global $db;
	Global $logged_employee_id;

	$query = $db->prepare("
					SELECT employee_firstname, employee_lastname
					FROM idk_employees
					WHERE employee_id = :employee_id");

	$query->execute(array(
				':employee_id' => $logged_employee_id));

	$employee = $query->fetch();

	echo $employee['employee_firstname'] . " " . $employee['employee_lastname'];

}

function getEmployeeFullnameById($employee_id) {

	Global $db;

	$query = $db->prepare("
					SELECT employee_firstname, employee_lastname
					FROM idk_employees
					WHERE employee_id = :employee_id");

	$query->execute(array(
				':employee_id' => $employee_id));

	$employee = $query->fetch();

	return $employee['employee_firstname'] . " " . $employee['employee_lastname'];

}

function getLinkNazivById($link_id) {

	Global $db;

	$query = $db->prepare("
					SELECT lg_url
					FROM idk_link_generator
					WHERE lg_id = :lg_id");

	$query->execute(array(
				':lg_id' => $link_id));

	$link = $query->fetch();

	return $link['lg_url'];

}

function getEmployeeEmail() {

	Global $db;
	Global $logged_employee_id;

	$query = $db->prepare("
					SELECT employee_email
					FROM idk_employees
					WHERE employee_id = :employee_id");

	$query->execute(array(
				':employee_id' => $logged_employee_id));

	$employee = $query->fetch();

	echo $employee['employee_email'];

}

function getEmployeeEmailR($employee_id) {

	Global $db;

	$query = $db->prepare(" 
					SELECT employee_email
					FROM idk_employees
					WHERE employee_id = :employee_id");

	$query->execute(array(
				':employee_id' => $employee_id));

	$employee = $query->fetch();

	return $employee['employee_email'];

}

function getEmployeeImage() {

	Global $db;
	Global $logged_employee_id;

	$query = $db->prepare("
					SELECT employee_image
					FROM idk_employees
					WHERE employee_id = :employee_id");

	$query->execute(array(
				':employee_id' => $logged_employee_id));

	$employee = $query->fetch();

	if($employee['employee_image'] == "none"){
		echo "none.jpg";
	}else{
		echo $employee['employee_image'];
	}

}

function getEmployeePosition() {

	Global $db;
	Global $logged_employee_id;

	$query = $db->prepare("
					SELECT employee_position
					FROM idk_employees
					WHERE employee_id = :employee_id");

	$query->execute(array(
				':employee_id' => $logged_employee_id));

	$employee = $query->fetch();

	echo $employee['employee_position'];

}

function getEmployeeStatus() {

	Global $db;
	Global $logged_employee_id;

	$query = $db->prepare("
					SELECT employee_status
					FROM idk_employees
					WHERE employee_id = :employee_id");

	$query->execute(array(
			':employee_id' => $logged_employee_id));

	$user = $query->fetch();

	return $user['employee_status'];
}

function getEmployeeStatusById($employee_id) {

	Global $db;
	Global $logged_employee_id;

	$query = $db->prepare("
					SELECT employee_status
					FROM idk_employees
					WHERE employee_id = :employee_id");

	$query->execute(array(
			':employee_id' => $employee_id));

	$user = $query->fetch();

	return $user['employee_status'];
}

function getEmployeeSupervizor() {

	Global $db;
	Global $logged_employee_id;

	$query = $db->prepare("
					SELECT employee_supervizor
					FROM idk_employees
					WHERE employee_id = :employee_id");

	$query->execute(array(
			':employee_id' => $logged_employee_id));

	$user = $query->fetch();

	return $user['employee_supervizor'];
}

function getEmployeWarehouse() {

	Global $db;
	Global $logged_employee_id;

	$query = $db->prepare("
					SELECT employee_warehouse
					FROM idk_employees
					WHERE employee_id = :employee_id");

	$query->execute(array(
			':employee_id' => $logged_employee_id));

	$user = $query->fetch();

	return $user['employee_warehouse'];
}

function getEmployeeLastReport() {

	Global $db;
	Global $logged_employee_id;

	//Get last report
	$query_report = $db->prepare("
							SELECT er_id, er_date
							FROM idk_employees_reports
							WHERE er_employeeid = :er_employeeid
							ORDER BY er_id DESC
							LIMIT 1");

	$query_report->execute(array('er_employeeid' => $logged_employee_id));

	$row_report = $query_report->fetch();

	if(isset($row_report['er_date'])){
		if($row_report['er_date'] == date('Y-m-d')){
			$er_date = '<a class="idk_date_success" href="employees-reports?page=add&date=' . date('d-m-Y', strtotime($row_report['er_date'])) . '">' . date('d.m.Y.', strtotime($row_report['er_date'])) . '</a>';
		}else{
			$er_date = '<a class="idk_date_danger" href="employees-reports?page=add&date=' . date('d-m-Y', strtotime($row_report['er_date'])) . '">' . date('d.m.Y.', strtotime($row_report['er_date'])) . '</a>';
		}
	}else{
		$er_date = '<a class="idk_date_danger" href="#">Nema izvještaja</a>';
	}

	return $er_date;
}

/* VICIDIAL PRISTUPNI PODACI - vraća niz sa korisničkim imenom i lozinkom */
function getZaposlenikVicidialParams($employee_id){
	Global $db;
	
	$read_query = $db->prepare("SELECT employee_viciuser, employee_vicipass
							   FROM idk_employees
							   WHERE employee_id = :employee_id");
	$read_query->execute(array(
				':employee_id' => $employee_id
	));
	
	$row = $read_query->fetch();
	
	$user = $row['employee_viciuser'];
	$password = $row['employee_vicipass'];
	
	$params = array($user,$password);
	return $params;
}


function sendEmail($mail_recipient, $mail_name, $mail_subject, $mail_body, $mail_altbody) {

	$mail = new PHPMailer(true);					// Passing `true` enables exceptions
	$mail->isSMTP();
	try {
		//Server settings
		$mail->Host = 'smtp.gmail.com';  			// Specify main and backup SMTP servers
		$mail->SMTPAuth = true;						// Enable SMTP authentication
		$mail->Username = 'support@job-step.com';	// SMTP username
		$mail->Password = 'eooc nnxo aylp lqkh';		// SMTP password
		$mail->SMTPSecure = 'ssl';					// Enable TLS encryption, `ssl` also accepted
		$mail->Port = 465;							// TCP port to connect to
		$mail->CharSet = 'UTF-8';
		$mail->setFrom('support@job-step.com', 'JobStep');

		//Recipients
		$mail->addAddress($mail_recipient, $mail_name);     // Add a recipient

		//Content
		$mail->isHTML(true);                                  // Set email format to HTML
		$mail->Subject = $mail_subject;
		$mail->Body    = $mail_body;
		$mail->AltBody = $mail_altbody;

		$mail->send();

	}catch (Exception $e){
		// echo 'Message could not be sent.';
		// echo 'Mailer Error: ' . $mail->ErrorInfo;
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
	$currentTime = time($todayDate);

	echo $day, date(", d.m.Y.");

}

function getTemperature() {

	$xml=simplexml_load_file("http://api.openweathermap.org/data/2.5/weather?q=Bihac&units=metric&mode=xml&appid=33d81ab4b496f28eabc7f40fb79d0500") or die("Error: Cannot create object");

	$temperature = $xml->temperature[0]->attributes();
	echo round($temperature);

}

function getWeatherIcon() {

	//Code: http://openweathermap.org/weather-conditions
	$xml=simplexml_load_file("http://api.openweathermap.org/data/2.5/weather?q=Bihac&units=metric&mode=xml&appid=33d81ab4b496f28eabc7f40fb79d0500") or die("Error: Cannot create object");

	$icon_code = $xml->weather[0]->attributes();

	if($icon_code == "802" OR $icon_code == "803" OR $icon_code == "804"){
		$weather_icon = "w_icon3.png";
	}elseif($icon_code == "600"){
		$weather_icon = "w_icon7.png";
	}elseif($icon_code == "800"){
		$weather_icon = "w_icon1.png";
	}elseif($icon_code == "801"){
		$weather_icon = "w_icon2.png";
	}elseif($icon_code == "500" OR $icon_code == "501"){
		$weather_icon = "w_icon5.png";
	}else{
		$weather_icon = "none";
	}

	echo $weather_icon;

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

function replace_africates($slug){
	$bad = array('Š','Ž','š','ž','ć','Ć','č','Č','đ','Đ','Ä','Ö','Ü','ẞ','ä','ö','ü','ß',' ','"',',','.',':');
	$good = array('S','Z','s','z','c','C','c','C','d','D','A','O','U','S','a','o','u','s','-','','','','');

	$slug = str_replace($bad, $good, $slug);

	if(empty($slug)){
		return 'n-a';
	}

	return $slug;
}

function getCandidateFullnameR($kandidat_id) {

	Global $db;
	Global $logged_employee_id;

	$query_kandidat = $db->prepare("
					SELECT kandidat_id, kandidat_ime, kandidat_prezime
					FROM idk_kandidati
					WHERE kandidat_id = :kandidat_id");

	$query_kandidat->execute(array(
			':kandidat_id' => $kandidat_id
			));

	$kandidat = $query_kandidat->fetch();
		$kandidat_ime = $kandidat['kandidat_ime'];
		$kandidat_prezime = $kandidat['kandidat_prezime'];
		$kandidat_ime_prezime = $kandidat_ime.' '.$kandidat_prezime;

		return $kandidat_ime_prezime;

}

function getDiplCandidateFullnameR($kandidat_id) {

	Global $db;

	$query_kandidat = $db->prepare("
					SELECT ime_nd_kandidata, prezime_nd_kandidata
					FROM idk_nd_kandidata
					WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata");

	$query_kandidat->execute(array(
			':id_broj_nd_kandidata' => $kandidat_id
			));

	$kandidat = $query_kandidat->fetch();
		$kandidat_ime = $kandidat['ime_nd_kandidata'];
		$kandidat_prezime = $kandidat['prezime_nd_kandidata'];
		$kandidat_ime_prezime = $kandidat_ime.' '.$kandidat_prezime;

		return $kandidat_ime_prezime;

}

function getInformacijeSkolaSmjerArrayR($idSkola, $idSmjer){
	Global $db; 
	/*
		Array
		(
			[status] => 1
			[data] => Array
				(
					[skola_naziv] => Ekonomska škola
					[skola_naziv_de] => Wirtschaftsschule
					[skola_tip_obrazovanja] => srednje
					[ss_naziv] => Ekonomski tehničar
					[ss_naziv_de] =>  Wirtschaftstechniker
					[ss_naziv_en] => Economic technician
				)

		)
	*/
	$result = array(
		"status" => 0,
		"data" => array(
			"skola_naziv" => "", 
			"skola_naziv_de" => "",
			"skola_tip_obrazovanja" => "",
			"ss_naziv" => "",
			"ss_naziv_de" => "",
			"ss_naziv_en" => "",
		),
	);
	$queryInfo = $db->prepare("
		SELECT 
			s.skola_naziv, 
			s.skola_naziv_de,
			s.skola_tip_obrazovanja,
			ss.ss_naziv, 
			ss.ss_naziv_de,
			ss.ss_naziv_en
		FROM 
			idk_skole s 
		INNER JOIN
			idk_skole_smjerovi ss
		ON 
			ss.ss_skola_id = s.skola_id
		WHERE 
			s.skola_id = :skolaId
			AND 
			ss.ss_id = :smjerId
	");
	$queryInfo->execute(array(
		':skolaId' => $idSkola, 
		':smjerId' => $idSmjer
	));
	if ($queryInfo->rowCount() == 1){
		$rowInfo = $queryInfo->fetch();
		$result["status"] = 1;
		$result["data"]["skola_naziv"] 				= $rowInfo["skola_naziv"]; 
		$result["data"]["skola_naziv_de"] 			= $rowInfo["skola_naziv_de"];
		$result["data"]["skola_tip_obrazovanja"] 	= $rowInfo["skola_tip_obrazovanja"];
		$result["data"]["ss_naziv"] 				= $rowInfo["ss_naziv"];
		$result["data"]["ss_naziv_de"] 				= $rowInfo["ss_naziv_de"];
		$result["data"]["ss_naziv_en"] 				= $rowInfo["ss_naziv_en"];
	}
	return $result;
}

function checkInsertUpdateCandidateDiplEducationInCandidateJobArrayR($kandidatIdDIPL, $skolaIdNew, $smjerIdNew, $skolaIdOld, $smjerIdOld) {
	/*
		DESC FUNCTION: 
			Funkcija je kreirana radi kopiranja edukacija iz profila DIPL u profil POSAO za kandidata. 
			Funkcija se u trenutku kreiranja poziva prilikom editovanja skole i smjera na DIPL profilu kandidata - te je u kasnijim pozivima iste potrebno provjeriti da li odgovara scenariju. 
			Dakle, prilikom edita skole i smjera na DIPL profilu kandidata - potrebno je novounešenu školu i smjer kopirati u POSAO profil kandidata.
			Pri tom se rade određene provjere. 
			Ako funkcija pronađe staru školu na profilu POSAO i uspostavi da se vrši izmjena iste - prvo se gleda da li prikaz PP označen.
			Ako je prikaz PP označen - vrši se insert novog reda u Edukacije na profilu POSAO. Razlog je jednostavan - ne možemo handlati prevode i sve ostale stvari kako bi automatski mogli zamjeniti školu i smjer sa novounešenom školom i smjerom. 
			Ako prikaz PP nije označen - vrši se update reda sa novounešenim vrijednostima škole i smjera.
	*/
	Global $db; 
	$kandidatIdDIPL 	= intval($kandidatIdDIPL);
	$skolaIdNew 		= intval($skolaIdNew);
	$smjerIdNew 		= intval($smjerIdNew);
	$skolaIdOld 		= intval($skolaIdOld);
	$smjerIdOld 		= intval($smjerIdOld);
	$kandidatIdJOB 		= 0;
	$result = array(
		"statusCode" => 0, 
		"statusMessage" => ""
	);
	$firstPartMessage = "
		DIPL -> Edit škole/smjera kandidata -> Kandidat edukacije -> Prilikom korištenja opcije za kandidata ID = [".$kandidatIdDIPL."] 
		gdje je izvršen update vrijednosti škole i smjera sa vrijednosti ['skola_nd_kandidata' => '".$skolaIdOld."', 'skola_smjer_nd_kandidata' => '".$smjerIdOld."'] 
		na vrijednosti ['skola_nd_kandidata' => '".$skolaIdNew."', 'skola_smjer_nd_kandidata' => '".$smjerIdNew."'] u Edukacijama za vezanog kandidata profila Posao desio se naredni ishod: 
	";
	if ( $kandidatIdDIPL != 0 AND $skolaIdNew != 0 AND $smjerIdNew != 0 AND $skolaIdOld != 0 AND $smjerIdOld ) { 
		$queryCheck1 = $db->prepare("
			/*
				Query koji radi provjeru da li je profil kandidat DIPL vezan za profil kandidat POSAO
				functions.php checkInsertUpdateCandidateDiplEducationInCandidateJobArrayR
				kandidatId => ".$kandidatIdDIPL."
			*/
			SELECT 
				kan.kandidat_id 
			FROM 
				idk_kandidati kan 
			INNER JOIN  
				idk_nd_kandidata dipl_kan
			ON 
				dipl_kan.id_broj_nd_kandidata = kan.kandidat_dipl_id
			WHERE 
				dipl_kan.id_broj_nd_kandidata = :kandidatId
		");
		$queryCheck1->execute(array(
			':kandidatId' => $kandidatIdDIPL
		));
		if ($queryCheck1->rowCount() == 1) {
			$rowCheck1 = $queryCheck1->fetch();
			$kandidatIdJOB = intval($rowCheck1["kandidat_id"]);
			$queryCheck2 = $db->prepare("
				/*
					Query koji povlači informacije o edukacijama za profil kandidat POSAO
					functions.php checkInsertUpdateCandidateDiplEducationInCandidateJobArrayR 
					ke_kandidat_id => ".$kandidatIdJOB."
					ke_skola_id => ".$skolaIdOld." 
					ke_smjer_id => ".$smjerIdOld." 
				*/
				SELECT
					ke_id, 
					ke_datumod, 
					ke_datumdo,
					ke_smjer_id,
					ke_skola_id,
					ke_grad, 
					ke_drzava,
					ke_prikaz_pp
				FROM 
					idk_kandidat_edukacija 
				WHERE 
					ke_kandidat_id = :ke_kandidat_id 
					AND 
					ke_skola_id = :ke_skola_id
					AND 
					ke_smjer_id = :ke_smjer_id
				ORDER BY
					ke_prikaz_pp
				ASC
			");
			$queryCheck2->execute(array(
				':ke_kandidat_id' => $kandidatIdJOB, 
				':ke_skola_id' => $skolaIdOld, 
				":ke_smjer_id" => $smjerIdOld
			));
			if ($queryCheck2->rowCount() != 0) {
				$infoSkolaSmjer = getInformacijeSkolaSmjerArrayR($skolaIdNew, $smjerIdNew);
				if ($infoSkolaSmjer["status"] == 1){
					$insertIdsExp = array();
					$editIdsExp = array();
					$insertIdsImp = "";
					$editIdsImp = "";
					$hasDuplicateDisplayed = 0;
					while($rowCheck2 = $queryCheck2->fetch()) {
						$ke_id = intval($rowCheck2["ke_id"]);
						$ke_datumod = $rowCheck2["ke_datumod"];
						$ke_datumdo = $rowCheck2["ke_datumdo"];
						$ke_smjer_id = intval($rowCheck2["ke_smjer_id"]);
						$ke_skola_id = intval($rowCheck2["ke_skola_id"]);
						$ke_grad = $rowCheck2["ke_grad"];
						$ke_drzava = $rowCheck2["ke_drzava"];
						$ke_prikaz_pp = intval($rowCheck2["ke_prikaz_pp"]);
						if ($ke_prikaz_pp == 1) {
							if (count($insertIdsExp) == 0 AND $hasDuplicateDisplayed == 0) {
								$queryInsert = $db->prepare("
									INSERT INTO idk_kandidat_edukacija
									(
										ke_smjer_id, ke_naziv_kvalifikacije, ke_skola_id, ke_naziv, ke_kandidat_id, ke_vrsta_obrazovanja
									)
									VALUES
									(
										:ke_smjer_id, :ke_naziv_kvalifikacije, :ke_skola_id, :ke_naziv, :ke_kandidat_id, :ke_vrsta_obrazovanja
									)
								");
								$queryInsert->execute(array(
									':ke_smjer_id' 				=> $smjerIdNew, 
									':ke_naziv_kvalifikacije' 	=> $infoSkolaSmjer["data"]["ss_naziv"], 
									':ke_skola_id' 				=> $skolaIdNew, 
									':ke_naziv' 				=> $infoSkolaSmjer["data"]["skola_naziv"], 
									':ke_kandidat_id' 			=> $kandidatIdJOB, 
									':ke_vrsta_obrazovanja' 	=> $infoSkolaSmjer["data"]["skola_tip_obrazovanja"] 
								));
								
								$educationId = $db->lastInsertId();

								array_push($insertIdsExp, $educationId);
							} else {
								continue;
							}
						} else {
							$hasDuplicateDisplayed = 1;
							$queryUpdate = $db->prepare("
								UPDATE 
									idk_kandidat_edukacija 
								SET 
									ke_smjer_id = :ke_smjer_id, 
									ke_naziv_kvalifikacije = :ke_naziv_kvalifikacije, 
									ke_skola_id = :ke_skola_id, 
									ke_naziv = :ke_naziv, 
									ke_vrsta_obrazovanja = :ke_vrsta_obrazovanja
								WHERE 
									ke_id = :ke_id
									AND
									ke_kandidat_id = :ke_kandidat_id
							");
							$queryUpdate->execute(array(
								':ke_id' => $ke_id, 
								':ke_kandidat_id' => $kandidatIdJOB, 
								':ke_smjer_id' => $smjerIdNew, 
								':ke_naziv_kvalifikacije' => $infoSkolaSmjer["data"]["ss_naziv"], 
								':ke_skola_id' => $skolaIdNew, 
								':ke_naziv' => $infoSkolaSmjer["data"]["skola_naziv"], 
								':ke_vrsta_obrazovanja' => $infoSkolaSmjer["data"]["skola_tip_obrazovanja"]
							));
							array_push($editIdsExp, $ke_id);
						}
					}
					$insertIdsImp = implode(",",$insertIdsExp);
					$editIdsImp = implode(",",$editIdsExp);

					$messageInsert = ($insertIdsImp != "" ? "Izvršen insert nove edukacije na osnovu edukacije koja se prikazuje na PPu sa IDs = [".$insertIdsImp."]" : "");
					$messageUpdate = ($editIdsImp != "" ? "Izvršen update edukacija sa IDs = [".$editIdsImp."]" : "");

					$result["statusCode"] = 201;
					$result["statusMessage"] = $firstPartMessage. " " . $messageInsert . " " . $messageUpdate;

					unset($insertIdsExp);
					unset($editIdsExp);
				} else {
					$result["statusCode"] = 104;
					$result["statusMessage"] = $firstPartMessage. "Problem sa povlačenjem informacija o novounešenoj školi i smjeru!";
				}
				unset($infoSkolaSmjer);
			} else {
				$infoSkolaSmjer = getInformacijeSkolaSmjerArrayR($skolaIdNew, $smjerIdNew);
				if ($infoSkolaSmjer["status"] == 1){ 
					$queryInsert = $db->prepare("
						INSERT INTO idk_kandidat_edukacija
						(
							ke_smjer_id, ke_naziv_kvalifikacije, ke_skola_id, ke_naziv, ke_kandidat_id, ke_vrsta_obrazovanja
						)
						VALUES
						(
							:ke_smjer_id, :ke_naziv_kvalifikacije, :ke_skola_id, :ke_naziv, :ke_kandidat_id, :ke_vrsta_obrazovanja
						)
					");
					$queryInsert->execute(array(
						':ke_smjer_id' 				=> $smjerIdNew, 
						':ke_naziv_kvalifikacije' 	=> $infoSkolaSmjer["data"]["ss_naziv"], 
						':ke_skola_id' 				=> $skolaIdNew, 
						':ke_naziv' 				=> $infoSkolaSmjer["data"]["skola_naziv"], 
						':ke_kandidat_id' 			=> $kandidatIdJOB, 
						':ke_vrsta_obrazovanja' 	=> $infoSkolaSmjer["data"]["skola_tip_obrazovanja"] 
					));
					$educationId = $db->lastInsertId();
					$result["statusCode"] = 200;
					$result["statusMessage"] = $firstPartMessage. "Pretragom je ustanovljeno da ne postoje škola i smjer adekvatan za zamjenu sa novounešenom školom i smjerom. Izvršen insert nove edukacije ID = [".$educationId."]!";
				} else {
					$result["statusCode"] = 104;
					$result["statusMessage"] = $firstPartMessage. "Problem sa povlačenjem informacija o novounešenoj školi i smjeru!";
				}
			}
		} else {
			$result["statusCode"] = 102;
			$result["statusMessage"] = $firstPartMessage. "Profil DIPL kandidata nije vezan za profil kandidata Posao!";
		}
	} else {
		$result["statusCode"] = 101;
		$result["statusMessage"] = $firstPartMessage. "Neadekvatni parametri poslani prema funkciji!";
	}
	return $result;
}

function copyCandidateDiplEducationInCandidateR($kandidatId, $skolaId, $smjerId){

	Global $db;

	$kandidatId = intval($kandidatId);
	$skolaId = intval($skolaId);
	$smjerId = intval($smjerId);

	if ( $kandidatId != 0 AND $skolaId != 0 AND $smjerId != 0 ) {

		/*
			Provjera da li je DIPL kandidat povezan sa kandidatom za posao
		*/

		$queryCheck = $db->prepare("
			SELECT 
				kan.kandidat_id 
			FROM 
				idk_kandidati kan 
			INNER JOIN  
				idk_nd_kandidata dipl_kan
			ON 
				dipl_kan.id_broj_nd_kandidata = kan.kandidat_dipl_id
			WHERE 
				dipl_kan.id_broj_nd_kandidata = :kandidatId
		");
		$queryCheck->execute(array(
			':kandidatId' => $kandidatId
		));
		
		if ( $queryCheck->rowCount() == 1 ){

			$rowCheck = $queryCheck->fetch();

			$can_id = intval($rowCheck["kandidat_id"]);

			/*
				Na osnovu IDs skole i smjera - potrebno je uzeti informacije o nazivima skole i smjera
			*/

			$queryInfo = $db->prepare("
				SELECT 
					s.skola_naziv, 
					s.skola_naziv_de,
					s.skola_tip_obrazovanja,
					ss.ss_naziv, 
					ss.ss_naziv_de
				FROM 
					idk_skole s 
				INNER JOIN
					idk_skole_smjerovi ss
				ON 
					ss.ss_skola_id = s.skola_id
				WHERE 
					s.skola_id = :skolaId
					AND 
					ss.ss_id = :smjerId
			");
			$queryInfo->execute(array(
				':skolaId' => $skolaId, 
				':smjerId' => $smjerId
			));

			if ( $queryInfo->rowCount() == 1 ) {

				$rowInfo = $queryInfo->fetch();

				$skola_naziv 			= $rowInfo["skola_naziv"];
				$skola_naziv_de 		= $rowInfo["skola_naziv_de"];
				$skola_tip_obrazovanja 	= $rowInfo["skola_tip_obrazovanja"];
				$ss_naziv				= $rowInfo["ss_naziv"];
				$ss_naziv_de 			= $rowInfo["ss_naziv_de"];

				/* 
					Nakon dobijenih traženih informacija - može se raditi insert u edukacije
				*/

				$queryInsert = $db->prepare("
					INSERT INTO idk_kandidat_edukacija
						(
							ke_smjer_id, ke_naziv_kvalifikacije, ke_skola_id, ke_naziv, ke_kandidat_id, ke_vrsta_obrazovanja
						)
					VALUES
						(
							:ke_smjer_id, :ke_naziv_kvalifikacije, :ke_skola_id, :ke_naziv, :ke_kandidat_id, :ke_vrsta_obrazovanja
						)
				");
				$queryInsert->execute(array(
					':ke_smjer_id' 				=> $smjerId, 
					':ke_naziv_kvalifikacije' 	=> $ss_naziv, 
					':ke_skola_id' 				=> $skolaId, 
					':ke_naziv' 				=> $skola_naziv, 
					':ke_kandidat_id' 			=> $can_id, 
					':ke_vrsta_obrazovanja' 	=> $skola_tip_obrazovanja
				));

				$edukacija_id = $db->lastInsertId();

				$log_desc = "Kopirana edukcija dipl kandidata u profil kandidata za posao -> KandidatId: [".$can_id."] koji je vezan sa DIPL KandidatId: [".$kandidatId."]. EdukacijaID: [".$edukacija_id."]";
				$log_type = "0";
				addToLogs($log_desc, $log_type);

				return 1;

			} else {

				return 103;

			}

		} else {

			return 102;

		}

	} else {

		return 101;

	}

}

function getDakCandidateFullnameR($kandidat_id) {

	Global $db;

	$query_kandidat = $db->prepare("
					SELECT name_dak_kandidat, lastname_dak_kandidat
					FROM idk_dak_kandidati
					WHERE id_dak_kandidat = :id_dak_kandidat");

	$query_kandidat->execute(array(
			':id_dak_kandidat' => $kandidat_id
			));

	$kandidat = $query_kandidat->fetch();
		$kandidat_ime = $kandidat['name_dak_kandidat'];
		$kandidat_prezime = $kandidat['lastname_dak_kandidat'];
		$kandidat_ime_prezime = $kandidat_ime.' '.$kandidat_prezime;

		return $kandidat_ime_prezime;

}

function getEmployeeFullnameR() {

	Global $db;
	Global $logged_employee_id;

	$query = $db->prepare("
					SELECT employee_firstname, employee_lastname
					FROM idk_employees
					WHERE employee_id = :employee_id");

	$query->execute(array(
				':employee_id' => $logged_employee_id));

	$employee = $query->fetch();

	return $employee['employee_firstname'] . " " . $employee['employee_lastname'];

}

function getZaposlenikimeR($employee_id) {

	Global $db;

	$query = $db->prepare("
					SELECT employee_firstname, employee_lastname
					FROM idk_employees
					WHERE employee_id = :employee_id");

	$query->execute(array(
				':employee_id' => $employee_id));

	$employee = $query->fetch();
	$employee_f_name = $employee['employee_firstname'];
	$employee_l_name = $employee['employee_lastname'];
	$employee_f_l_name = $employee_f_name.' '.$employee_l_name;

	return $employee_f_l_name;
}
//getBranchNameR - funkcija za informacija o poslovnici
function getBranchNameR($branch_id) {

	Global $db;

	$query = $db->prepare("
					SELECT branch_name, branch_city, branch_state
					FROM idk_poslovnice
					WHERE branch_id = :branch_id");

	$query->execute(array(
				':branch_id' => $branch_id));

	$branch = $query->fetch();
	$b_name = $branch['branch_name'];
	$b_city = $branch['branch_city'];
	$b_state = $branch['branch_state'];
	$branch_full = $b_name.', '.$b_city.', '.$b_state;

	return $branch_full;
}
//getBranchIdByEmployee - funkcija za id poslovnice na osnovu id-a zaposlenika
function getBranchIdByEmployee($employee_id) {

	Global $db;

	$query = $db->prepare("
					SELECT employee_poslovnica
					FROM idk_employees
					WHERE employee_id = :employee_id");

	$query->execute(array(
				':employee_id' => $employee_id));

	$branch = $query->fetch();
	$branch_id = $branch['employee_poslovnica'];

	return $branch_id;
}

function getBrojUplataAgentaZaMjesec($agent_id, $datum_uplate){
	Global $db;
	$current_month_start = date('Y-m-01', strtotime($datum_uplate));
	$current_month_end = date('Y-m-t', strtotime($datum_uplate));
	$query = $db->prepare("
					SELECT COUNT(pr_id) as broj
					FROM idk_predracuni 
					WHERE pr_zaposlenik = :pr_zaposlenik AND pr_rata = 1 AND pr_status = 2 AND pr_uplaceno = 1 AND pr_datum_uplate BETWEEN :start_date AND :end_date
	");
	$query->execute(array(
					':pr_zaposlenik' => $agent_id,
					':start_date' => $current_month_start,
					':end_date' => $current_month_end
	));
	$row = $query->fetch();
	
	return $row['broj'];
}

function buInkAgent($agent_id, $datum_uplate){
	Global $db;
	$current_month_start = date('Y-m-01', strtotime($datum_uplate));
	$current_month_end = date('Y-m-t', strtotime($datum_uplate));
	$predracuni = array();
	//brojanje normalnih inkaso uplata
	$query = $db->prepare("
					SELECT id_biljeska_nd, predracun_id FROM idk_nd_kandidata_biljeske 
					JOIN idk_predracuni on idk_predracuni.pr_id = idk_nd_kandidata_biljeske.predracun_id
					WHERE zadnja_inkaso_biljeska = 1 AND status_biljeska_nd = 3 AND tip_biljeska_nd = 5 AND dodao_zaposlenik_biljeska_nd = :agent_id AND pr_status = 2 AND pr_uplaceno = 1 AND pr_rata = 1 AND pr_datum_uplate BETWEEN :start_date AND :end_date
	");
	$query->execute(array(
					':agent_id' => $agent_id,
					':start_date' => $current_month_start,
					':end_date' => $current_month_end
	));
	while($row = $query->fetch()){
		array_push($predracuni, $row['predracun_id']);
	}
	$broj_pr_1 = count($predracuni);
	$predracuni_query = implode(",", $predracuni);
	//brojanje inkaso uplata preko promjene ugovora
	$query2 = $db->prepare("
					SELECT COUNT(pr_id) as broj_p FROM idk_predracuni
					WHERE pr_status = 2 AND pr_uplaceno = 1 AND pr_datum_uplate BETWEEN :start_date AND :end_date AND pr_inkaso_agent = :agent_id AND pr_id NOT IN ($predracuni_query)
					");
	$query2->execute(array(
					':agent_id' => $agent_id,
					':start_date' => $current_month_start,
					':end_date' => $current_month_end
	));
	$row2 = $query2->fetch();
	$broj_p_2 = $row2['broj_p'];
	
	return $broj_p_2 + $broj_pr_1;
}

function updatePrethodneObracune($vrijednost_tipa_f, $vrijednost_kategorije_f, $prov_bam_f, $prov_rsd_f, $prov_eur_f, $employee_id, $datum_uplate){
	Global $db;
	$current_month_start = date('Y-m-01 00:00:00', strtotime($datum_uplate));
	$current_month_end = date('Y-m-t 23:59:59', strtotime($datum_uplate));
	$query_upd = $db->prepare("UPDATE idk_obracuni SET vrijednost_tipa = :vrijednost_tipa, vrijednost_kategorije = :vrijednost_kategorije, iznos_obracuna_bam = :iznos_obracuna_bam, iznos_obracuna_rsd = :iznos_obracuna_rsd, iznos_obracuna_eur = :iznos_obracuna_eur
								WHERE employee_id = :employee_id AND vrijeme_uplate BETWEEN :start_date AND :end_date AND kategorija_provizije IN (1,2)
	");
	$query_upd->execute(array(
				':vrijednost_tipa' => $vrijednost_tipa_f,
				':vrijednost_kategorije' => $vrijednost_kategorije_f,
				':iznos_obracuna_bam' => $prov_bam_f,
				':iznos_obracuna_rsd' => $prov_rsd_f,
				':iznos_obracuna_eur' => $prov_eur_f,
				':employee_id' => $employee_id,
				':start_date' => $current_month_start,
				':end_date' => $current_month_end
	));
}

function getEmployeeDoe($agent_id){
	Global $db;
	$query = $db->prepare("
					SELECT employee_doe
					FROM idk_employees 
					WHERE employee_id = $agent_id
	");
	$query->execute();
	
	$row = $query->fetch();
	return $row['employee_doe'];
}

//Modul Nostrifikacija Diploma START

function getNivoJezikaKandidatDIPL($vr){
	//$vr - vrijednost u bazi (tabela: idk_nd_kandidata, kolona: nivo_poznavanja_jezika)
	if(isset($vr)){
		if($vr == 0){
			return '<span style="background-color: rgb(243, 65, 60); color: white;" class="label label-default material-label material-label_default main-container__column text-center">Nije unešena informacija!</span>';
		}else if($vr == 1){
			return '<span style="background-color: rgb(191, 33, 75); color: white;" class="label label-default material-label material-label_default main-container__column text-center">Bez znanja</span>';
		}else if($vr == 2){
			return '<span style="background-color: rgb(242, 228, 46); color: white;" class="label label-default material-label material-label_default main-container__column text-center">A1</span>';
		}else if($vr == 3){
			return '<span style="background-color: rgb(242, 161, 46); color: white;" class="label label-default material-label material-label_default main-container__column text-center">A2</span>';
		}else if($vr == 4){
			return '<span style="background-color: rgb(0, 250, 251); color: white;" class="label label-default material-label material-label_default main-container__column text-center">B1</span>';
		}else if($vr == 5){
			return '<span style="background-color: rgb(139, 218, 242); color: white;" class="label label-default material-label material-label_default main-container__column text-center">B2</span>';
		}else if($vr == 6){
			return '<span style="background-color: rgb(0, 251, 83); color: white;" class="label label-default material-label material-label_default main-container__column text-center">C1</span>';
		}else if($vr == 7){
			return '<span style="background-color: rgb(104, 195, 104); color: white;" class="label label-default material-label material-label_default main-container__column text-center">C2</span>';
		}else{
			return '<span style="background-color: rgb(243, 65, 60); color: white;" class="label label-default material-label material-label_default main-container__column text-center">Problem sa podatkom!</span>';
		}
	}else{
		return '<span style="background-color: rgb(243, 65, 60); color: white;" class="label label-default material-label material-label_default main-container__column text-center">Problem sa podatkom!</span>';
	}
}

function getVrstaObradeKandidataDIPL($vr){
	//$vr - vrijednost u bazi (tabela: idk_nd_kandidata, kolona: vrsta_obrade)
	if(isset($vr)){
		if($vr == 0){
			return '<span style="background-color: rgb(243, 65, 60); color: white;" class="label label-default material-label material-label_default main-container__column text-center">Nije unešena informacija!</span>';
		}else if($vr == 1){
			return '<span style="background-color: rgb(104, 195, 104); color: white;" class="label label-default material-label material-label_default main-container__column text-center">Ausbildung</span>';
		}else if($vr == 2){
			return '<span style="background-color: rgb(199, 156, 255); color: white;" class="label label-default material-label material-label_default main-container__column text-center">Srednja stručna sprema</span>';
		}else if($vr == 3){
			return '<span style="background-color: rgb(0, 250, 251); color: white;" class="label label-default material-label material-label_default main-container__column text-center">Visoka stručna sprema</span>';
		}else{
			return '<span style="background-color: rgb(243, 65, 60); color: white;" class="label label-default material-label material-label_default main-container__column text-center">Problem sa podatkom!</span>';
		}
	}else{
		return '<span style="background-color: rgb(243, 65, 60); color: white;" class="label label-default material-label material-label_default main-container__column text-center">Problem sa podatkom!</span>';
	}
}

function sendMailUplataPredracuna($id_predracuna){
	//funkcija koja prilikom uplate određenog predracuna - salje mail zaposleniku koji je kreirao taj predracun i zaposlenicima iz obrade
	Global $db;
	Global $logged_employee_id;
	$zaposlenici = array();
	$log_zaposlenici = "";
	if(isset($id_predracuna)){
		$query = $db->prepare("
			SELECT 
				pred.pr_broj_predracuna, 
				pred.pr_zaposlenik, 
				pred.pr_domaca_valuta, 
				pred.pr_rata, 
				kan.ime_nd_kandidata, 
				kan.prezime_nd_kandidata, 
				kan.mobilni_nd_kandidata, 
				kan.id_broj_nd_kandidata, 
				pred.pr_datum_uplate
			FROM idk_predracuni pred
			INNER JOIN idk_nd_kandidata kan
			ON pred.pr_kandidat_id = kan.id_broj_nd_kandidata
			WHERE pred.pr_uplaceno = 1 AND pred.pr_id = ".$id_predracuna."
		");
		$query->execute();
		$row = $query->fetch();
		$pr_broj_predracuna = $row["pr_broj_predracuna"];
		$pr_zaposlenik = $row["pr_zaposlenik"];
		$pr_domaca_valuta = $row["pr_domaca_valuta"];
		$pr_rata = intval($row["pr_rata"]);
		$ime_nd_kandidata = $row["ime_nd_kandidata"];
		$prezime_nd_kandidata = $row["prezime_nd_kandidata"];
		$mobilni_nd_kandidata = $row["mobilni_nd_kandidata"];
		$id_broj_nd_kandidata = intval($row["id_broj_nd_kandidata"]);
		$pr_datum_uplate = date("d.m.Y", strtotime($row["pr_datum_uplate"]));
		
		
		if($pr_domaca_valuta == "BAM" OR $pr_domaca_valuta == "EUR"){
			if($pr_zaposlenik != 67)
				array_push($zaposlenici, $pr_zaposlenik);
			array_push($zaposlenici, 43);
			array_push($zaposlenici, 158);
		}else if($pr_domaca_valuta == "RSD"){
			if($pr_zaposlenik != 67)
				array_push($zaposlenici, $pr_zaposlenik);
			array_push($zaposlenici, 203);
			array_push($zaposlenici, 158);
		}else{
			
		}
		
		if(count($zaposlenici) != 0){
			foreach ($zaposlenici as $value) {
				$user_query = $db->prepare("
					SELECT employee_firstname, employee_lastname, employee_email
					FROM idk_employees
					WHERE employee_id = :employee_id AND employee_status NOT LIKE '0'
				");

				$user_query->execute(array(
					':employee_id' => $value
				));
				if ($user_query->rowCount() > 0) {

					$user = $user_query->fetch();

					$employee_firstname = $user['employee_firstname'];
					$employee_lastname = $user['employee_lastname'];
					$employee_email = $user['employee_email'];

					//Send email to user
					$mail_email = $employee_email;
					$mail_name = $employee_firstname . ' ' . $employee_lastname;
					$mail_subject = "Uplata predračuna ".$pr_broj_predracuna."";
					$mail_url = "" . getSiteUrlr() . "/nostrifikacija_diploma?page=otvori_ND_kandidata&id=".$id_broj_nd_kandidata."";
					$mail_url = '<a href = "'.$mail_url.'" >LINK</a>';
					$mail_body = "
						<p>
						Označena uplata za kandidata. <br><br>
						Kandidat: ".$ime_nd_kandidata." ".$prezime_nd_kandidata." <br>
						Broj telefona: ".$mobilni_nd_kandidata."<br>
						Rata: ".$pr_rata."<br>
						Datum uplate: ".$pr_datum_uplate."<br><br>
						</p>
						<p>Detalje pogledajte na linku: " . $mail_url . "</p>
					";
					$mail_altbody = "
						<p>
						Označena uplata za kandidata. <br><br>
						Kandidat: ".$ime_nd_kandidata." ".$prezime_nd_kandidata." <br>
						Broj telefona: ".$mobilni_nd_kandidata."<br>
						Rata: ".$pr_rata."<br>
						Datum uplate: ".$pr_datum_uplate."<br><br>
						</p>
						<p>Detalje pogledajte na linku: " . $mail_url . "</p>
					";
					
					sendEmail($mail_email, $mail_name, $mail_subject, $mail_body, $mail_altbody);
				}
			}
			$log_zaposlenici = implode(",",$zaposlenici);
			$log_desc = "UPLATA PREDRACUNA: Kandidat ID = [".$id_broj_nd_kandidata."]. Poslan e-mail zaposlenicima ID = [".$log_zaposlenici."].";
			$log_date = date('Y-m-d H:i:s');

			$log_query = $db->prepare("
				INSERT INTO idk_logs 
					(log_employeeid, log_desc, log_date)
				VALUES 
					(:log_employeeid, :log_desc, :log_date)
			");

			$log_query->execute(array(
				':log_employeeid' => $logged_employee_id,
				':log_desc' => $log_desc,
				':log_date' => $log_date
			));
		}
	}
}

function updateVrijemeStatusZakazanogPoziva($id_kandidata, $vrijeme){
	Global $db;
	Global $logged_employee_id;
	
	if(isset($id_kandidata) AND isset($vrijeme)){
		$query = $db->prepare("
			UPDATE idk_nd_kandidata
			SET vrijeme_zakaznog_poziva = :vrijeme_zakaznog_poziva, status_zakaznog_poziva = 0
			WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata
		");
		$query->execute(array(
			':vrijeme_zakaznog_poziva' => $vrijeme, 
			':id_broj_nd_kandidata' => $id_kandidata
		));
		
		$log_date = date('Y-m-d H:i:s');
		$log_desc = "Zakazan poziv za kandidata ID = [".$id_kandidata."] u vrijeme ".$vrijeme.". Poziv zakazao zaposlenik ID = [".$logged_employee_id."]";
		$log_query = $db->prepare("
			INSERT INTO idk_logs 
				(log_employeeid, log_desc, log_date)
			VALUES
				(:log_employeeid, :log_desc, :log_date)
		");

		$log_query->execute(array(
			':log_employeeid' => $logged_employee_id,
			':log_desc' => $log_desc,
			':log_date' => $log_date
		));
	}
}

function updateStatusZakazanogPoziva($id_kandidata){
	Global $db;
	Global $logged_employee_id;
	
	if(isset($id_kandidata)){
		$provjera = $db->prepare("
			SELECT status_zakaznog_poziva
			FROM idk_nd_kandidata 
			WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata AND vrijeme_zakaznog_poziva < '".date('Y-m-d H:i:s')."' AND status_zakaznog_poziva is not null
		");
		$provjera->execute(array(
			':id_broj_nd_kandidata' => $id_kandidata
		));
		if($provjera->rowCount() != 0){
			$row_pro = $provjera->fetch();
			if(intval($row_pro["status_zakaznog_poziva"]) == 0){
				$query = $db->prepare("
					UPDATE idk_nd_kandidata
					SET status_zakaznog_poziva = 1
					WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata
				");
				$query->execute(array(
					':id_broj_nd_kandidata' => $id_kandidata
				));
				
				$log_date = date('Y-m-d H:i:s');
				$log_desc = "Zaposlenik ID = [".$logged_employee_id."] prozvao kandidata ID = [".$id_kandidata."] na zakazano vrijeme. Obrađen zakazan poziv.";
				$log_query = $db->prepare("
					INSERT INTO idk_logs 
						(log_employeeid, log_desc, log_date)
					VALUES
						(:log_employeeid, :log_desc, :log_date)
				");

				$log_query->execute(array(
					':log_employeeid' => $logged_employee_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date
				));
			}
		}
	}
}

function getBrojPozoviKasnijeDIPLR($type, $status, $vrijeme, $employee){
	Global $db;
	//$status - 1 obrađeno, 0 - nije obrađeno
	//vrijeme - 2 Prosli, 0 - Danas , 1 - Budući
	$current_date = date("Y-m-d");// current date
	$date_plus30 = date("Y-m-d",strtotime('+30 days',strtotime($current_date))) . " 23:59:59";
	$date_minus30 = date("Y-m-d",strtotime('-30 days',strtotime($current_date))) . " 00:00:00";
	$date_plus1 = date("Y-m-d",strtotime('+1 day',strtotime($current_date))) . " 00:00:00";
	$date_minus1 = date("Y-m-d",strtotime('-1 day',strtotime($current_date))) . " 23:59:59";
	$uslov_obradjeno = "";
	$uslov_vrijeme = "";
	//Uslov za status
	if($status == 1){
		$uslov_obradjeno = " (kan.status_zakaznog_poziva = 1 AND kan.status_zakaznog_poziva is not null) ";
	}else{
		$uslov_obradjeno = " (kan.status_zakaznog_poziva = 0 AND kan.status_zakaznog_poziva is not null) ";
	}
	//Uslov za vrijeme
	if($vrijeme == 1){
		$uslov_vrijeme = " kan.vrijeme_zakaznog_poziva BETWEEN '".$date_plus1."' AND '".$date_plus30."' ";
	}else if($vrijeme == 2){
		$uslov_vrijeme = " kan.vrijeme_zakaznog_poziva BETWEEN '".$current_date." 00:00:00' AND '".$current_date." 23:59:59' ";
	}else{
		$uslov_vrijeme = " kan.vrijeme_zakaznog_poziva BETWEEN '".$date_minus30."' AND '".$date_minus1."' ";
	}
	
	if($type == 1){
		$team = getLoggedEmployeeTeam();
		$employess_id_team = implode(", ", getIdOfEmployeeTeam($team));
		//pregled svih uslovi
		if($team == 1){
			$uslov_query = " (kan.zaduzen_zaposlenik_nd_kandidata is not null) ";
		}else{
			$uslov_query = " (kan.zaduzen_zaposlenik_nd_kandidata is not null AND kan.zaduzen_zaposlenik_nd_kandidata IN (".$employess_id_team.")) ";
		}
	}else{
		//pregled pregled od zaposlenika
		$uslov_query = " kan.zaduzen_zaposlenik_nd_kandidata = ".$employee." ";
	}
	
	$query = $db->prepare("
	SELECT count(kan.id_broj_nd_kandidata) AS broj_zvati_ponovo 
	FROM idk_nd_kandidata kan 
	WHERE  ".$uslov_query." AND kan.status_nd_kandidata NOT IN (2,3,4,5,6,7)
	AND 
		".$uslov_obradjeno." 
	AND 
		".$uslov_vrijeme." 
	");
	$query->execute();
	$row = $query->fetch();
	return $row["broj_zvati_ponovo"];
}

function getImePrezimeNDKanR($id){
	Global $db;
	
	$query = $db->prepare("
					SELECT ime_nd_kandidata, prezime_nd_kandidata
					FROM idk_nd_kandidata
					WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata
	");
	
	$query->execute(array(
					':id_broj_nd_kandidata' => $id
	));
	
	$row = $query->fetch();
	
	$kan = $row['ime_nd_kandidata']." ".$row['prezime_nd_kandidata'];
	
	return $kan;
}

function getImePrezimeKandidata($id){
	Global $db;
	
	$query = $db->prepare("
					SELECT kandidat_ime, kandidat_prezime
					FROM idk_kandidati
					WHERE kandidat_id = :id
	");
	
	$query->execute(array(
					':id' => $id
	));
	
	$row = $query->fetch();
	
	$kan = $row['kandidat_ime']." ".$row['kandidat_prezime'];
	
	return $kan;
}

function getBrPredracunaNDKanR($id){
	Global $db;
	
	$query = $db->prepare("
					SELECT pr_id
					FROM idk_predracuni
					WHERE pr_kandidat_id = :pr_kandidat_id AND pr_vrsta_predracuna = :pr_vrsta_predracuna AND pr_status != 0
	");
	
	$query->execute(array(
					':pr_kandidat_id' => $id,
					':pr_vrsta_predracuna' => 1
	));
	
	$br = $query->rowCount();
	
	return $br;
}

function getSkolaNDKanidataR($skola_nd_kandidata){
	Global $db;

	$query = $db->prepare("
					SELECT skola_naziv
					FROM idk_skole
					WHERE skola_id = :skola_id
	");

	$query->execute(array(
					':skola_id' => $skola_nd_kandidata
	));

	$skola_nd_kandidata_row = $query->fetch();

	$skola_nd_kandidata_naziv = $skola_nd_kandidata_row['skola_naziv'];

	return $skola_nd_kandidata_naziv;
}

function getZaposlenikDiplR($employee_id){
	Global $db;

	$query = $db->prepare("
					SELECT employee_nostrifikacija_diploma
					FROM idk_employees
					WHERE employee_id = :employee_id");

	$query->execute(array(
				':employee_id' => $employee_id));

	$employee = $query->fetch();
	$employee_dipl = $employee['employee_nostrifikacija_diploma'];
	return $employee_dipl;
}

function getSkolaSmjerNDKanidataR($skola_smjer_nd_kandidata){
	Global $db;

	$query = $db->prepare("
					SELECT ss_naziv
					FROM idk_skole_smjerovi
					WHERE ss_id = :ss_id
	");

	$query->execute(array(
					':ss_id' => $skola_smjer_nd_kandidata
	));

	$skola_smjer_nd_kandidata_row = $query->fetch();

	$skola_smjer_nd_kandidata_naziv = $skola_smjer_nd_kandidata_row['ss_naziv'];

	return $skola_smjer_nd_kandidata_naziv;
}
//Kampanje na diplu - puni naziv - izvor dolaska
function getKampanjePuniNazivDIPLR($id){
	Global $db;

	$query_kamp = $db->prepare("SELECT kd_poruka_kampanje FROM idk_kampanje_dipl WHERE kd_id = :kd_id");
	$query_kamp->execute(array(':kd_id' => $id));
	$row_kamp = $query_kamp->fetch();
	$puni_naziv_kamp = $row_kamp["kd_poruka_kampanje"];

	return $puni_naziv_kamp;
}
//Kampanje na diplu - skraceni naziv - izvor dolaska
function getKampanjeSkrNazivDIPLR($id){
	Global $db;

	$query_kamp = $db->prepare("SELECT kd_skraceni_naziv FROM idk_kampanje_dipl WHERE kd_id = :kd_id");
	$query_kamp->execute(array(':kd_id' => $id));
	$row_kamp = $query_kamp->fetch();
	$sk_naziv_kamp = $row_kamp["kd_skraceni_naziv"];

	return $sk_naziv_kamp;
}

function getPoslodavacNDKanidataR($id_klijenta_nd_kandidata){
	Global $db;

	$query = $db->prepare("
					SELECT 	company_id, company_name
					FROM idk_companies
					WHERE company_id = :company_id
	");

	$query->execute(array(
					':company_id' => $id_klijenta_nd_kandidata
	));

	$klijent_nd_kandidata_row = $query->fetch();

	$klijent_nd_kandidata_naziv_ispis = $klijent_nd_kandidata_row['company_name'];
	return $klijent_nd_kandidata_naziv_ispis;
}

function getPoslodavacTypeNDKanidataR($id_klijenta_type_nd_kandidata){
	Global $db;

	$query = $db->prepare("
					SELECT 	company_id, company_contact_type
					FROM idk_companies
					WHERE company_id = :company_id
	");

	$query->execute(array(
					':company_id' => $id_klijenta_type_nd_kandidata
	));

	$klijent_type_nd_kandidata_row = $query->fetch();

	$klijent_type_nd_kandidata_naziv_ispis = $klijent_type_nd_kandidata_row['company_contact_type'];
	return $klijent_type_nd_kandidata_naziv_ispis;
}

function getTipDokumentaNDUstanoveR($id_tip_dokumenta){
	Global $db;

	$query = $db->prepare("
					SELECT naziv_tip_dokumenta_ustanove_nd
					FROM idk_nd_ustanove_tip_dokumenta
					WHERE id_tip_dokumenta_ustanove_nd = :id_tip_dokumenta_ustanove_nd
	");

	$query->execute(array(
					':id_tip_dokumenta_ustanove_nd' => $id_tip_dokumenta
	));

	$document_type_nd_ustanove_row = $query->fetch();

	$document_type_nd_ustanove_ispis = $document_type_nd_ustanove_row['naziv_tip_dokumenta_ustanove_nd'];
	return $document_type_nd_ustanove_ispis;
}

function getUplacenaRataND($id_kan, $br_rate){
	Global $db;
	
	$query = $db->prepare("
					SELECT id_r
					FROM idk_nd_rate
					WHERE id_nd_kan = :id_nd_kan AND br_r = :br_r
				");
	$query->execute(array(
					':id_nd_kan' => $id_kan,
					':br_r' => $br_rate
				));
	$check = $query->rowCount();

	return $check;
}
function getBrojRataNDR($id){
	Global $db;
	
	$query = $db->prepare("
					SELECT vrsta_ugovora_nd_kandidata
					FROM idk_nd_kandidata
					WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata
	");
	
	$query->execute(array(
					':id_broj_nd_kandidata' => $id
	));
	
	$row = $query->fetch();
	
	$vrsta_ugovora = $row['vrsta_ugovora_nd_kandidata'];
	
	switch($vrsta_ugovora){
		case 1: case 2: case 22: case 52: case 62: case 72: case 82: case 42:
			$broj_rata = 2;
		break;
		case 5: case 6: case 23: case 53: case 63: case 73: case 83: case 43:
			$broj_rata = 3;
		break;
		case 7: case 8: case 24: case 54: case 64: case 74: case 84: case 44:
			$broj_rata = 4;
		break;
		case 3: case 4: case 25: case 55: case 65: case 75: case 85: case 45:
			$broj_rata = 5;
		break;
		case 26: case 28:
			$broj_rata = 6;
		break;
		case 27: case 29:
			$broj_rata = 12;
		break;
		default:
		$broj_rata = 1;
	}
	
	return $broj_rata;
}
function getVrstaUgovora($id){
	Global $db;
	
	$query = $db->prepare("
					SELECT vrsta_ugovora_nd_kandidata
					FROM idk_nd_kandidata
					WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata
	");
	
	$query->execute(array(
					':id_broj_nd_kandidata' => $id
	));
	
	$row = $query->fetch();
	
	$vrsta_ugovora = $row['vrsta_ugovora_nd_kandidata'];
	
	return $vrsta_ugovora;
}
function getZadnjaRataNDR($id){
	Global $db;
	
	$query = $db->prepare("
					SELECT MAX(pr_rata) as rata
					FROM idk_predracuni
					WHERE pr_kandidat_id = :pr_kandidat_id AND pr_status != 0
	");
	
	$query->execute(array(
					':pr_kandidat_id' => $id
	));
	
	$row = $query->fetch();
	
	$najveca_rata = $row['rata'];
	
	return $najveca_rata;
}
function checkUplacenostSvihRata($id){
	Global $db;
	
	$query = $db->prepare("
					SELECT pr_id
					FROM idk_predracuni
					WHERE pr_kandidat_id = :pr_kandidat_id AND pr_uplaceno = 0
	");
	
	$query->execute(array(
					':pr_kandidat_id' => $id
	));
	
	$row = $query->rowCount();
	
	// AKO JE BROJ VECI OD NULA ZNACI DA NISU SVE UPLACENE I VRACA NULU ILITIGA FALSE SUPROTNO VRACA 1 ILITIGA TRUE
	if($row > 0){
		return 0;
	}else{
		return 1;
	}
}
function checkUplacenostSvihPrethodnihRata($id, $rata){
	Global $db;
	
	$query = $db->prepare("
					SELECT pr_id
					FROM idk_predracuni
					WHERE pr_kandidat_id = :pr_kandidat_id AND pr_uplaceno = 0 AND pr_status != 0 AND pr_rata < $rata
	");
	
	$query->execute(array(
					':pr_kandidat_id' => $id
	));
	
	$row = $query->rowCount();
	
	// AKO JE BROJ VECI OD NULA ZNACI DA NISU SVE UPLACENE I VRACA NULU ILITIGA FALSE SUPROTNO VRACA 1 ILITIGA TRUE
	if($row > 0){
		return 0;
	}else{
		return 1;
	}
}

//Funkcija koja vrsi promjenu menadzera kod unosa prve komunikacije koja je tipa 3,4,5
function insertNewMenagerND($tip_komunikacije, $id_kandidata, $logirani_zaposlenik){
	Global $db;
	if(intval($tip_komunikacije) == 3 OR intval($tip_komunikacije) == 4 OR intval($tip_komunikacije) == 5){
		//Prvo je potrebno uraditi provjeru da nema ni jedna dodana biljeska tipa 3,4,5
		//ako nema ni jedna dodana onda se provjerava zaposlenik
		//ako ima dodana onda se ne radi nista
		$query_provjera = $db->prepare("
							SELECT COUNT(id_biljeska_nd) AS br_biljeski
							FROM idk_nd_kandidata_biljeske
							WHERE id_kandidata_biljeska_nd = :id_kandidata_biljeska_nd AND status_biljeska_nd = :status_biljeska_nd AND tip_biljeska_nd IN (3,4,5)
							");
		$query_provjera->execute(array(':id_kandidata_biljeska_nd' => $id_kandidata, ':status_biljeska_nd' => 2));
		$row_provjera = $query_provjera->fetch();
		$broj_biljeski = $row_provjera["br_biljeski"];
		if(intval($broj_biljeski) == 0){
			$query = $db->prepare("SELECT zaduzen_zaposlenik_nd_kandidata 
									FROM idk_nd_kandidata
									WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata
								");
			$query->execute(array(':id_broj_nd_kandidata' => $id_kandidata));
			if($query->rowCount() != 0){
				$row = $query->fetch();
				$trenutni_menadzer = $row["zaduzen_zaposlenik_nd_kandidata"];
				if(intval($logirani_zaposlenik) != intval($trenutni_menadzer)){
					//Unos novog menadzera START
					$query1 = $db->prepare("
										UPDATE idk_nd_kandidata
										SET zaduzen_zaposlenik_nd_kandidata = :zaduzen_zaposlenik_nd_kandidata
										WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata");

					$query1->execute(array(
								':id_broj_nd_kandidata' => $id_kandidata,
								':zaduzen_zaposlenik_nd_kandidata' => $logirani_zaposlenik
								));
					//Unos novog menadzera END
					
					//Update statistike menadzera START
					$insert_statistike_menagera1 = $db->prepare("
								INSERT INTO idk_nd_menadzeri_statistike
									(idd_broj_nd_kandidata, zaduzen_zaposlenik_id, prethodni_zaposlenik_id, vrsta_aktivnosti, vrijeme_aktivnosti)
								VALUES
									(:idd_broj_nd_kandidata, :zaduzen_zaposlenik_id, :prethodni_zaposlenik_id, :vrsta_aktivnosti, :vrijeme_aktivnosti)");

					$insert_statistike_menagera1->execute(array(
								':idd_broj_nd_kandidata' => $id_kandidata,
								':zaduzen_zaposlenik_id' => $logirani_zaposlenik,
								':prethodni_zaposlenik_id' => $trenutni_menadzer,
								':vrsta_aktivnosti' => 1,
								':vrijeme_aktivnosti' => date('Y-m-d H:i:s')
								));
					//Update statistike menadzera START
				}
			}
		}
	}
}

function getZadnjaKomunikacijaNDR($id){
	Global $db;
	$query = $db->prepare("
						SELECT MAX(vrijeme_dodavanja_biljeska_nd) AS vr_dodavanja
						FROM idk_nd_kandidata_biljeske
						WHERE id_kandidata_biljeska_nd = :id_kandidata_biljeska_nd AND status_biljeska_nd = :status_biljeska_nd
						");
	$query->execute(array(':id_kandidata_biljeska_nd' => $id, ':status_biljeska_nd' => 2));
	if($query->rowCount() != 0){
		//ako ima vec dodana jedna komunikacija onda se provjerava vrijeme zadnje komunikacije
		$row_query = $query->fetch();
		$vrijeme_dodavanja = $row_query["vr_dodavanja"];
		$trenutno_vrijeme = date("Y-m-d H:i:s");
		$diff = strtotime($trenutno_vrijeme) - strtotime($vrijeme_dodavanja);
		$days = floor($diff/86400);
		$hours = floor(($diff - $days * 86400) / 3600);
		$minutes = floor(($diff - $days * 86400 - $hours * 3600) / 60);
		$seconds = floor($diff - $days * 86400 - $hours * 3600 - $minutes * 60);
		if($days == 0 AND $hours == 0 AND $minutes == 0 AND $seconds < 11){
			//ako nije proslo 10 sekundi
			return 0;
		}else{
			//ako je proslo 10 sekundi
			return 1;
		}
	}else{
		//ako nema ni jedna dodana komunikacija - odnosno unosi se prva komunikacija za tog kandidata
		return 2;
	}
}

function getDatumZadnjaKomunikacijaNDR($id){
	Global $db;
	$query = $db->prepare("
						SELECT MAX(vrijeme_dodavanja_biljeska_nd) AS vr_dodavanja
						FROM idk_nd_kandidata_biljeske
						WHERE id_kandidata_biljeska_nd = :id_kandidata_biljeska_nd AND status_biljeska_nd = :status_biljeska_nd
						");
	$query->execute(array(':id_kandidata_biljeska_nd' => $id, ':status_biljeska_nd' => 2));
	$row_query = $query->fetch();
	
	if($row_query["vr_dodavanja"] != NULL){
		//ako ima vec dodana jedna komunikacija onda se provjerava vrijeme zadnje komunikacije
		$vrijeme_dodavanja = date("d.m.Y H:i:s", strtotime($row_query["vr_dodavanja"]));
		return '
			<span class="label label-default material-label material-label_default main-container__column text-center">
			'.$vrijeme_dodavanja.'
			</span>
		';
	}else{
		return '
		<span class="label label-warning material-label material-label_warning main-container__column text-center">
			Nema dodano!
		<span>
		';
	}
}

function getNaplataPrekoDIPLR($drzava){

	Global $db;

	/*

		DRZAVA: 
			1	-	BIH
			2	-	SRB
			3	-	EU
		
		Nema smisla za pojam EU da se vodi pod drzavom al sta je tu je
		Razlog za uzimanje int vrijednosti je taj jer se na mjestima na kojima ce biti pozvana funkcija, 
		razlicito formatira varijabla drzava. Iz tog razloga vrijednost ce se provjeravati prije poziva funkcije zbog bitnih razlika
		i nakon toga ce se proslijediti funkciji jedinstvena vrijednost

	*/
	$drzava = intval($drzava); 

	$country_code = ( ( $drzava == 1 ) ? 'BIH' : ( ( $drzava == 2 ) ? 'SRB' : 'EU' ) );

	$query = $db->prepare("
		SELECT 
			fk_naplata_preko
		FROM 
			idk_nd_naplata_preko 
		WHERE 
			aktivno = 1
			AND 
			drzava = :drzava
	");
	$query->execute(array(
		':drzava' => $country_code
	));

	$row = $query->fetch();

	$result = intval($row["fk_naplata_preko"]);
	
	return $result;

}

function getSamoDatumZadnjaKom($id){
	Global $db;
	$query = $db->prepare("
						SELECT MAX(vrijeme_dodavanja_biljeska_nd) AS vr_dodavanja
						FROM idk_nd_kandidata_biljeske
						WHERE id_kandidata_biljeska_nd = :id_kandidata_biljeska_nd AND status_biljeska_nd = :status_biljeska_nd
						");
	$query->execute(array(':id_kandidata_biljeska_nd' => $id, ':status_biljeska_nd' => 2));
	$row_query = $query->fetch();
	
	return $vrijeme_dodavanja = $row_query["vr_dodavanja"];
}

function updateNovaVrstaUgovoraDIPL($id_kand, $drzava_novi, $ugovor_novi){
	Global $db;
	Global $logged_employee_id;
	
	//Ovaj query je postavljen cisto iz razloga provjere odredjenih informacija 
	//NPR: 	
	//		na kojem se statusu nalazi zaposlenik
	//		Trenutna vrsta ugovora i sl
	$query_provjera = $db->prepare("
		SELECT status_nd_kandidata, pstatus_nd_kandidata, vrsta_ugovora_nd_kandidata, zaduzen_zaposlenik_nd_kandidata
		FROM idk_nd_kandidata
		WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata
	");
	$query_provjera->execute(array(
		':id_broj_nd_kandidata' => $id_kand
	));
	$query_provjera1 = $db->prepare("
		SELECT pr_id, pr_zaposlenik, pr_naplata_preko, pr_stornirano
		FROM idk_predracuni
		WHERE pr_kandidat_id = :pr_kandidat_id AND pr_rata = 1 AND pr_status = 1
	");
	$query_provjera1->execute(array(
		':pr_kandidat_id' => $id_kand
	));
	
	if($query_provjera->rowCount() != 0 AND $query_provjera1->rowCount() != 0){
		$row_provjera = $query_provjera->fetch();
		$row_provjera1 = $query_provjera1->fetch();
		$pr_naplata_preko = $row_provjera1["pr_naplata_preko"];
		$pr_id = $row_provjera1["pr_id"];
		$pr_stornirano = $row_provjera1["pr_stornirano"];
		$zaposlenik = $row_provjera["zaduzen_zaposlenik_nd_kandidata"];
		$status = $row_provjera["status_nd_kandidata"]; //trenutni status
		$pstatus = $row_provjera["pstatus_nd_kandidata"]; //trenutni podstatus
		$vrstaugovora = $row_provjera["vrsta_ugovora_nd_kandidata"]; //trenutna vrsta ugovora (ako se bude biljezilo u logove sa koje vrste na koju je prebacio)
		//ako se vec nalazi na statusu U obradi Lead, onda je samo potrebno promjeniti ugovor, predracun i ostalo (bez promjene odredjenog statusa)
		
		//Update Starih Ugovora, Uplatnica, Predracuna START
		//-----------------------------------------------------------------
			//Provjeravam funkcijom da li postoji vec izdat jedan predracun za tog kandidata - ako postoji - onda je to sigurno za prvu ratu i njega arhiviram
			if(getBrPredracunaNDKanR($id_kand) == 1){
				
				//U slucaju promjene ugovora nakon stornacije ne arhivirati stari predracun jer se na osnovu njega vec uplatilo
				if($pr_stornirano == 1){
					
				}else{
					//Update starog predracuna - status arhiva
					$update_stari_predracun = $db->prepare("
						UPDATE idk_predracuni
						SET pr_status = :pr_status
						WHERE pr_id = :pr_id AND pr_kandidat_id = :pr_kandidat_id
					");
					$update_stari_predracun->execute(array(
						':pr_status' => 0,
						':pr_id' => $pr_id,
						':pr_kandidat_id' => $id_kand
					));
					
					//nema potrebe kreirati nove uplatnice jer mora ici stari ugovor
					$update_stara_uplatnica = $db->prepare("
						UPDATE idk_nd_kandidata_dokumenti
						SET tip_dokumenta_status = :tip_dokumenta_status
						WHERE id_kandidata_dokument_nd = :id_kandidata_dokument_nd AND tip_dokumenta = :tip_dokumenta
					");
					$update_stara_uplatnica->execute(array(
						':tip_dokumenta_status' => 0,
						':id_kandidata_dokument_nd' => $id_kand,
						':tip_dokumenta' => 2
					));
				}
				//Update obracuna za stari predracun
				$update_obracune = $db->prepare("
					UPDATE idk_obracuni
					SET status_obracuna = :status_obracuna
					WHERE predracun_id = :predracun_id AND status_obracuna = 1
				");
				$update_obracune->execute(array(
					':status_obracuna' => 2,
					':predracun_id' => $pr_id
				));
				
				//Sad je potrebno staviti ugovor i uplatnicu iz dokumenata kao neaktivne dokumente
				
				if($pr_naplata_preko == 1 OR $pr_naplata_preko == 2){
					//update ugovora koji su preko CH
					$update_stari_ugovor = $db->prepare("
						UPDATE idk_nd_ugovori
						SET ug_status = :ug_status, ug_datum_arhiviranja = NOW()
						WHERE ug_kandidat_id = :ug_kandidat_id
					");
					$update_stari_ugovor->execute(array(
						':ug_status' => 0,
						':ug_kandidat_id' => $id_kand
					));
				}else{
					$update_stari_ugovor = $db->prepare("
						UPDATE idk_nd_kandidata_dokumenti
						SET tip_dokumenta_status = :tip_dokumenta_status
						WHERE id_kandidata_dokument_nd = :id_kandidata_dokument_nd AND tip_dokumenta = :tip_dokumenta
					");
					$update_stari_ugovor->execute(array(
						':tip_dokumenta_status' => 0,
						':id_kandidata_dokument_nd' => $id_kand,
						':tip_dokumenta' => 1
					));
				}
				
				//UPDATE
			}
			
		//-----------------------------------------------------------------
		//Update Starih Ugovora, Uplatnica, Predracuna END
		
		
		//Dodavanje Novih START
		//-----------------------------------------------------------------
			if($drzava_novi == 1){
				$drzava = "BiH";
				$jezik_ugovora = "bs";
			}elseif($drzava_novi == 2){
				$drzava = "Srbija";
				$jezik_ugovora = "sr";
			}else{
				$drzava = "Njemacka";
				$jezik_ugovora = "de";
			}
			$month = date('m');
			$day = date('d');
			$year = date('Y');
			$year_skr = date('y');
			
			
			
			//Radi se update za vrstu ugovora
			$vrsta_ugovora_x = $db->prepare("
				UPDATE idk_nd_kandidata
				SET vrsta_ugovora_nd_kandidata = :vrsta_ugovora_nd_kandidata
				WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata
			");
			$vrsta_ugovora_x->execute(array( 
				':id_broj_nd_kandidata' => $id_kand,
				':vrsta_ugovora_nd_kandidata' => $ugovor_novi
			));
			if($ugovor_novi == 99){
				$naplata_preko = 0;
			}else{
				//Aktivna firma za naplate
				// 1 - CH; 0 - stari nacin: BiH i SRB zasebno
				/*$get_naplata = $db->prepare("
							SELECT fk_naplata_preko 
							FROM idk_nd_naplata_preko 
							WHERE aktivno = 1
				");
				$get_naplata->execute();
				$row_naplata = $get_naplata->fetch();
				$naplata_preko = $row_naplata["fk_naplata_preko"];*/

				$naplata_preko = getNaplataPrekoDIPLR($drzava_novi);
			}
			
			if($naplata_preko == 1){
				if($pr_stornirano == 1){
					//ne treba kreirati novi predracun u slucaju stornacije
				}else{
					$brojac_predracuna = createBrojPredracuna("ch");
					$novi_predracun = "DIPLCH-".$brojac_predracuna."-".$year_skr;
					
					//iznos dobijam preko fje u markama  i onda gledam drzavu klijenta i onda prebacivam valute
					$iznos_rate = getIznosRate($ugovor_novi, "ch", "rata1");
					
					$ch = curl_init();
					// Disable SSL verification
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
					// Will return the response, if false it print the response
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					// Set the url
					$rls="https://www.cbbh.ba/CurrencyExchange/GetJson?date=".$month."%2F".$day."%2F".$year."%2000%3A00%3A00";
					curl_setopt($ch, CURLOPT_URL,$rls);
					// Execute
					$result=curl_exec($ch);
					curl_close($ch);

					$data = json_decode($result, TRUE);
					$EUR1 = $data['CurrencyExchangeItems'][0]['Middle'];
					$EUR2 = $data['CurrencyExchangeItems'][0]['Units'];
					$RSD1 = $data['CurrencyExchangeItems'][15]['Middle'];
					$RSD2 = $data['CurrencyExchangeItems'][15]['Units'];
					
					
					$EUR = str_replace(',', '.', $EUR1);
					$RSD = str_replace(',', '.', $RSD1);
					
					$rata_eur = $iznos_rate / $EUR;
					$rata_rsd = 100*$iznos_rate / $RSD;
					
					$rata_bam_f = number_format((float)$iznos_rate, 2, '.', '');
					$rata_eur_f = number_format((float)$rata_eur, 2, '.', '');
					$rata_rsd_f = number_format((float)$rata_rsd, 2, '.', '');
					
					if($drzava_novi == 1){
						$slovo_drz = "B";
						$domaca_valuta = "BAM";
						$jezik_ugovora = "bs";
					}
					elseif($drzava_novi == 2){
						$slovo_drz = "S";
						$domaca_valuta = "RSD";
						$jezik_ugovora = "sr";
					}
					elseif($drzava_novi == 3){
						$slovo_drz = "D";
						$domaca_valuta = "EUR";
						$jezik_ugovora = "de";
					}
					
					$file_datum = date('YmdHis'); 
					$predracun_putanja = $file_datum."DIPL".$slovo_drz.$brojac_predracuna.".pdf";
					$insert_predracun = $db->prepare("	
								INSERT INTO idk_predracuni	
								(pr_broj_predracuna,  pr_naplata_preko, pr_kandidat_id, pr_zaposlenik, pr_vrsta_predracuna, pr_rata, pr_domaca_valuta, pr_vrijednost_BAM, pr_vrijednost_RSD, pr_vrijednost_EUR)	
								VALUES	
								(:pr_broj_predracuna,:pr_naplata_preko,:pr_kandidat_id,:pr_zaposlenik,:pr_vrsta_predracuna,:pr_rata,:pr_domaca_valuta,:pr_vrijednost_BAM,:pr_vrijednost_RSD,:pr_vrijednost_EUR)	
								");	
					
					$insert_predracun->execute(array(	
								':pr_broj_predracuna' => $novi_predracun,
								':pr_naplata_preko' => $naplata_preko,
								':pr_kandidat_id' => $id_kand,
								':pr_zaposlenik' => $zaposlenik,
								':pr_vrsta_predracuna' => 1,
								':pr_rata' => 1,
								':pr_domaca_valuta' => $domaca_valuta,
								':pr_vrijednost_BAM' => $rata_bam_f,
								':pr_vrijednost_RSD' => $rata_rsd_f,
								':pr_vrijednost_EUR' => $rata_eur_f
								));
								
					$predracun_id = $db->lastInsertId();
				}
				// Komentarisao jer se generise kad se prihvati tek Adis
				// if($drzava != "Njemacka"){
					// generisiPredracun($predracun_id, $drzava);
					// generisiPredracun($predracun_id, "Njemacka");
				// }else{
					// generisiPredracun($predracun_id, "Njemacka");
				// }

				//generisanje tokena
				$token = $file_datum.$id_kand;
				$token_h = hash(md5, $token);
				
				$insert_ugovor = $db->prepare("	
							INSERT INTO idk_nd_ugovori	
							(ug_kandidat_id, ug_vrsta, ug_zaposlenik_id, ug_token, ug_jezik, ug_status, ug_datum_slanja)	
							VALUES	
							(:ug_kandidat_id, :ug_vrsta, :ug_zaposlenik_id,:ug_token,:ug_jezik,:ug_status,:ug_datum_slanja)	
							");	
				$insert_ugovor->execute(array(	
							':ug_kandidat_id' => $id_kand,
							':ug_vrsta' => $ugovor_novi,
							':ug_zaposlenik_id' => $zaposlenik,
							':ug_token' => $token_h,
							':ug_jezik' => $jezik_ugovora,
							':ug_status' => 1,
							':ug_datum_slanja' => date('Y-m-d H:i:s')	
							));
				$ugovor_link = getSiteUrlr()."ugovor/".$token_h."/".$jezik_ugovora;
				$poruka_text_viber 	= "";
				$poruka_text_sms 	= "";
				$poruka_button		= "";
				
				//na mail ide $predracun_putanja, $putanja_uplatnica, $ugovor_link, odvojeni mailovi za srb i bih
				if($drzava == "Srbija"){
					if($pr_stornirano == 1){
						//ne treba kreirati uplatnicu
					}else{
						createUplatnicaSRB($predracun_id);
					}
					$poruka_text_viber	= "Vaš proces nostrifikacije u zajedničkoj saradnji je počeo! 🤗\n\n";
					$poruka_text_viber .= "Nakon što otvorite ugovor, sledite upute kako biste prihvatili isti. Možete također pogledati  predračun, te preuzeti uplatnicu.\n\n";
					$poruka_text_viber .= "Zahvaljujemo Vam se na poverenju! Tu smo za Vas i sve što Vas interesuje.\n\n";
					$poruka_text_viber .= "Srdačan pozdrav,\n\nJSI Team";

					$poruka_text_sms	= "Vas proces nostrifikacije u zajednickoj saradnji je poceo! 🤗\n\n";
					$poruka_text_sms   .= "Nakon sto otvorite ugovor, sledite upute kako biste prihvatili isti. Mozete takoder pogledati  predracun, te preuzeti uplatnicu.\n\n";
					$poruka_text_sms   .= "Zahvaljujemo Vam se na poverenju! Tu smo za Vas i sve sto Vas interesuje.\n\n";
					$poruka_text_sms   .= "Srdacan pozdrav,\n\nJSI Team\n\n".$ugovor_link;
																		
					$poruka_button 		=  "Ugovor";
				}elseif($drzava == "BiH"){
					if($pr_stornirano == 1){
						//ne treba kreirati uplatnicu
					}else{
						createUplatnicaBIH($predracun_id);
					}
					$poruka_text_viber	= "Vaš proces nostrifikacije u zajedničkoj saradnji je počeo! 🤗\n\n";
					$poruka_text_viber .= "Nakon što otvorite ugovor, slijedite upute kako biste prihvatili isti. Možete također pogledati  predračun, te preuzeti uplatnicu.\n\n";
					$poruka_text_viber .= "Zahvaljujemo Vam se na povjerenju, tu smo za Vas i sve što Vas interesuje.\n\n";
					$poruka_text_viber .= "Lijep pozdrav,\nJSI Team";
					
					$poruka_text_sms	= "Vas proces nostrifikacije u zajednickoj saradnji je poceo! 🤗\n\n";
					$poruka_text_sms   .= "Nakon sto otvorite ugovor, slijedite upute kako biste prihvatili isti. Mozete takoder pogledati  predracun, te preuzeti uplatnicu.\n\n";
					$poruka_text_sms   .= "Zahvaljujemo Vam se na povjerenju, tu smo za Vas i sve sto Vas interesuje.\n\n";
					$poruka_text_sms   .= "Lijep pozdrav,\nJSI Team\n\n".$ugovor_link;
					
					$poruka_button 		=  "Ugovor";
					
				}else{
					//ako kandidat nije ni iz BiH onda ide info o ino uplatama, maybe, ceka se odg od Sabine
					if($pr_stornirano == 1){
						//ne treba kreirati uplatnicu
					}else{
						generisiInoUplatnicu($predracun_id);
					}
					$poruka_text_viber	= "Ihr Anerkennungsverfahren in gegenseitiger Zusammenarbeit hat begonnen! 🤗\n\n";
					$poruka_text_viber .= "Nachdem Sie den Vertrag geöffnet haben, folgen Sie die Anweisungen, um den Vertrag zu akzeptieren. Sie können auch die Pro-forma-Rechnung durchlesen und den Einzahlungsschein herunterladen.\n\n";
					$poruka_text_viber .= "Wir bedanken uns für Ihr Vertrauen und stehen für alle Fragen gerne zur Verfügung.\n\n";
					$poruka_text_viber .= "Mit freundlichen Grüßen,\n\nJobstep International Team";
					
					$poruka_text_sms	= "Ihr Anerkennungsverfahren in gegenseitiger Zusammenarbeit hat begonnen! 🤗\n\n";
					$poruka_text_sms   .= "Nachdem Sie den Vertrag geöffnet haben, folgen Sie die Anweisungen, um den Vertrag zu akzeptieren. Sie können auch die Pro-forma-Rechnung durchlesen und den Einzahlungsschein herunterladen.\n\n";
					$poruka_text_sms   .= "Wir bedanken uns für Ihr Vertrauen und stehen für alle Fragen gerne zur Verfügung.\n\n";
					$poruka_text_sms   .= "Mit freundlichen Grüßen,\n\nJobstep International Team\n\n".$ugovor_link;
																		
					$poruka_button 		=  "Vertrag";
				}
				sendMailUgovorLink($id_kand, $ugovor_link);
				sendViberUgovorLink($id_kand, $poruka_text_viber, $poruka_text_sms, $ugovor_link, $poruka_button);
				
			} else if ($naplata_preko == 2) {
				/*
					Mix način - digitalno / drzava domacin START 
					*/

						if($pr_stornirano == 1){
							//ne treba kreirati novi predracun u slucaju stornacije
						}else{
							$brojac_predracuna = createBrojPredracuna($drzava);
							$iznos_rate = getIznosRate($ugovor_novi, $drzava, "rata1");

							/*
								Dio odradjen samo za Srbiju START 
								*/
									$slovo_drz = "S";
									$domaca_valuta = "RSD";
									$jezik_ugovora = "sr";
									$rata_rsd = $iznos_rate;
									
									$ch = curl_init();
									// Disable SSL verification
									curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
									// Will return the response, if false it print the response
									curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
									// Set the url
									$rls="https://www.cbbh.ba/CurrencyExchange/GetJson?date=".$month."%2F".$day."%2F".$year."%2000%3A00%3A00";
									// var_dump($rls);
									// exit();
									curl_setopt($ch, CURLOPT_URL,$rls);
									curl_setopt($ch, CURLOPT_FAILONERROR, true); // Required for HTTP error codes to be reported via our call to curl_error($ch)
									// Execute
									$result=curl_exec($ch);
									
									curl_close($ch);

									$data = json_decode($result, TRUE);
									$EUR1 = $data['CurrencyExchangeItems'][0]['Middle'];
									$EUR2 = $data['CurrencyExchangeItems'][0]['Units'];
									$RSD1 = $data['CurrencyExchangeItems'][15]['Middle'];
									$RSD2 = $data['CurrencyExchangeItems'][15]['Units'];
									
									$EUR = str_replace(',', '.', $EUR1);
									$RSD = str_replace(',', '.', $RSD1);
									$rata_bam = ($rata_rsd / 100) * $RSD ;
									$rata_eur = $rata_bam / $EUR;
									
									$rata_bam_f = number_format((float)$rata_bam, 2, '.', '');
									$rata_eur_f = number_format((float)$rata_eur, 2, '.', '');
									$rata_rsd_f = number_format((float)$rata_rsd, 2, '.', '');
								/*
								Dio odradjen samo za Srbiju START 
							*/

							$file_datum = date('YmdHis'); 
							$file_name = $file_datum."DIPL".$slovo_drz.$brojac_predracuna.".pdf";
							$novi_predracun = "DIPL".$slovo_drz."-".$brojac_predracuna."-".$year_skr;

							/*
								Insert predracuna START 
								*/
									$insert_predracun = $db->prepare("	
										INSERT INTO idk_predracuni	
											(
												pr_broj_predracuna, 
												pr_naplata_preko, 
												pr_kandidat_id, 
												pr_zaposlenik, 
												pr_vrsta_predracuna, 
												pr_rata, 
												pr_domaca_valuta, 
												pr_vrijednost_BAM, 
												pr_vrijednost_RSD, 
												pr_vrijednost_EUR
											)	
										VALUES	
											(
												:pr_broj_predracuna,
												:pr_naplata_preko,
												:pr_kandidat_id,
												:pr_zaposlenik,
												:pr_vrsta_predracuna,
												:pr_rata,
												:pr_domaca_valuta,
												:pr_vrijednost_BAM,
												:pr_vrijednost_RSD,
												:pr_vrijednost_EUR
											)	
									");	
									
									$insert_predracun->execute(array(	
										':pr_broj_predracuna' => $novi_predracun,
										':pr_naplata_preko' => $naplata_preko,
										':pr_kandidat_id' => $id_kand,
										':pr_zaposlenik' => $zaposlenik,
										':pr_vrsta_predracuna' => 1,
										':pr_rata' => 1,
										':pr_domaca_valuta' => $domaca_valuta,
										':pr_vrijednost_BAM' => $rata_bam_f,
										':pr_vrijednost_RSD' => $rata_rsd_f,
										':pr_vrijednost_EUR' => $rata_eur_f
									));
									
									$predracun_id = $db->lastInsertId();
								/*
								Insert predracuna END 
							*/
						}

						/*
							Insert ugovora START
							*/

								//KREIRANJE BROJA UGOVORA U FORMATU DIPLS-BROJ UGOVORA U MJESECU-MJESEC(dvocifren 01)/zadnje dvije cifre godine
								$ug_broj = createBrojUgovoraSrbija();
								$ug_full_broj = "DIPLS-".$ug_broj."-".date('m')."/".date('y');
								$token = $file_datum.$id_kand;
								$token_h = hash("md5", $token);
								
								$insert_ugovor = $db->prepare("	
									INSERT INTO idk_nd_ugovori	
										(
											ug_kandidat_id,
											ug_broj,
											ug_vrsta,
											ug_zaposlenik_id, 
											ug_token, 
											ug_jezik, 
											ug_status, 
											ug_datum_slanja
										)	
									VALUES	
										(
											:ug_kandidat_id,
											:ug_broj,
											:ug_vrsta,
											:ug_zaposlenik_id,
											:ug_token,
											:ug_jezik,
											:ug_status,
											:ug_datum_slanja
										)	
								");	
								$insert_ugovor->execute(array(	
									':ug_kandidat_id' => $id_kand,
									':ug_broj' => $ug_full_broj,
									':ug_vrsta' => $ugovor_novi,
									':ug_zaposlenik_id' => $zaposlenik,
									':ug_token' => $token_h,
									':ug_jezik' => $jezik_ugovora,
									':ug_status' => 1,
									':ug_datum_slanja' => date('Y-m-d H:i:s')	
								));
								$ugovor_link = getSiteUrlr()."ugovorNew/".$token_h."/".$jezik_ugovora;
							/*
							Insert ugovora END
						*/
						
						/*
							Slanje ugovora START
							*/
								// Provjeriti da li je bilo promjena na porukama sms i viber Adis 444
								$poruka_text_viber 	= "";
								$poruka_text_sms 	= "";
								$poruka_button		= "";

								if($pr_stornirano == 1){
									//Ne treba kreirati
								}else{
									createUplatnicaSRB($predracun_id);
								}
								$poruka_text_viber	= "Vaš proces nostrifikacije/evaluacije u zajedničkoj saradnji je počeo! 🤗\n\n";
								$poruka_text_viber .= "Nakon što otvorite ugovor, sledite upute kako biste prihvatili isti. Možete također pogledati  predračun, te preuzeti uplatnicu.\n\n";
								$poruka_text_viber .= "Zahvaljujemo Vam se na poverenju! Tu smo za Vas i sve što Vas interesuje.\n\n";
								$poruka_text_viber .= "Srdačan pozdrav,\n\nJSI Team";

								$poruka_text_sms	= "Vas proces nostrifikacije/evaluacije u zajednickoj saradnji je poceo! 🤗\n\n";
								$poruka_text_sms   .= "Nakon sto otvorite ugovor, sledite upute kako biste prihvatili isti. Mozete takoder pogledati  predracun, te preuzeti uplatnicu.\n\n";
								$poruka_text_sms   .= "Zahvaljujemo Vam se na poverenju! Tu smo za Vas i sve sto Vas interesuje.\n\n";
								$poruka_text_sms   .= "Srdacan pozdrav,\n\nJSI Team\n\n".$ugovor_link;
																					
								$poruka_button 		=  "Ugovor";

								sendMailUgovorLink($id_kand, $ugovor_link, $naplata_preko);
								sendViberUgovorLink($id_kand, $poruka_text_viber, $poruka_text_sms, $ugovor_link, $poruka_button);	
							/*
							Slanje ugovora END
						*/
					/*
					Mix način - digitalno / drzava domacin END 
				*/
			} elseif($naplata_preko == 0){
				
				$brojac_predracuna = createBrojPredracuna($drzava);
				$iznos_rate = getIznosRate($ugovor_novi, $drzava, "rata1");
				
				if($drzava == "Srbija"){
					$slovo_drz = "S";
					$domaca_valuta = "RSD";
					$rata_rsd = $iznos_rate;
					
					$ch = curl_init();
					// Disable SSL verification
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
					// Will return the response, if false it print the response
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					// Set the url
					$rls="https://www.cbbh.ba/CurrencyExchange/GetJson?date=".$month."%2F".$day."%2F".$year."%2000%3A00%3A00";
					// var_dump($rls);
					// exit();
					curl_setopt($ch, CURLOPT_URL,$rls);
					curl_setopt($ch, CURLOPT_FAILONERROR, true); // Required for HTTP error codes to be reported via our call to curl_error($ch)
					// Execute
					$result=curl_exec($ch);
					
					curl_close($ch);

					$data = json_decode($result, TRUE);
					$EUR1 = $data['CurrencyExchangeItems'][0]['Middle'];
					$EUR2 = $data['CurrencyExchangeItems'][0]['Units'];
					$RSD1 = $data['CurrencyExchangeItems'][15]['Middle'];
					$RSD2 = $data['CurrencyExchangeItems'][15]['Units'];
					
					$EUR = str_replace(',', '.', $EUR1);
					$RSD = str_replace(',', '.', $RSD1);
					$rata_bam = ($rata_rsd / 100) * $RSD ;
					$rata_eur = $rata_bam / $EUR;
					
					$rata_bam_f = number_format((float)$rata_bam, 2, '.', '');
					$rata_eur_f = number_format((float)$rata_eur, 2, '.', '');
					$rata_rsd_f = number_format((float)$rata_rsd, 2, '.', '');
					
				}else{
					$slovo_drz = "B";
					$domaca_valuta = "BAM";
					$rata_bam = $iznos_rate;
					
					$ch = curl_init();
					// Disable SSL verification
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
					// Will return the response, if false it print the response
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					// Set the url
					$rls="https://www.cbbh.ba/CurrencyExchange/GetJson?date=".$month."%2F".$day."%2F".$year."%2000%3A00%3A00";
					curl_setopt($ch, CURLOPT_URL,$rls);
					// Execute
					$result=curl_exec($ch);
					curl_close($ch);

					$data = json_decode($result, TRUE);
					$EUR1 = $data['CurrencyExchangeItems'][0]['Middle'];
					$EUR2 = $data['CurrencyExchangeItems'][0]['Units'];
					$RSD1 = $data['CurrencyExchangeItems'][15]['Middle'];
					$RSD2 = $data['CurrencyExchangeItems'][15]['Units'];
					
					$EUR = str_replace(',', '.', $EUR1);
					$RSD = str_replace(',', '.', $RSD1);
					
					$rata_eur = $rata_bam / $EUR;
					$rata_rsd = 100*$rata_bam / $RSD;
					
					$rata_bam_f = number_format((float)$rata_bam, 2, '.', '');
					$rata_eur_f = number_format((float)$rata_eur, 2, '.', '');
					$rata_rsd_f = number_format((float)$rata_rsd, 2, '.', '');
					
				}
				$novi_predracun = "DIPL".$slovo_drz."-".$brojac_predracuna."-".$year_skr;
				$file_datum = date('YmdHis'); 
				$file_name = $file_datum."DIPL".$slovo_drz.$brojac_predracuna.".pdf";
				
				//AKO SE RADI O 100% POPUSTU ONDA OZNACITI PREDRACUN DA JE VEC UPLACEN
				if($ugovor_novi == 99){
					$pr_status = 2;
					$pr_uplaceno = 1;
					$pr_datum_uplate = date("Y-m-d H:i:s");
				}else{
					$pr_status = 1;
					$pr_uplaceno = 0;
					$pr_datum_uplate = null;
				}
				$insert_predracun = $db->prepare("	
							INSERT INTO idk_predracuni	
							(pr_broj_predracuna,  pr_naplata_preko, pr_kandidat_id, pr_zaposlenik, pr_vrsta_predracuna, pr_rata, pr_domaca_valuta, pr_vrijednost_BAM, pr_vrijednost_RSD, pr_vrijednost_EUR, pr_file, pr_status, pr_uplaceno, pr_datum_uplate)	
							VALUES	
							(:pr_broj_predracuna,:pr_naplata_preko,:pr_kandidat_id,:pr_zaposlenik,:pr_vrsta_predracuna,:pr_rata,:pr_domaca_valuta,:pr_vrijednost_BAM,:pr_vrijednost_RSD,:pr_vrijednost_EUR,:pr_file,:pr_status,:pr_uplaceno,:pr_datum_uplate)	
							");	
				$insert_predracun->execute(array(	
							':pr_broj_predracuna' => $novi_predracun,	
							':pr_naplata_preko' => $naplata_preko,	
							':pr_kandidat_id' => $id_kand,	
							':pr_zaposlenik' => $zaposlenik,	
							':pr_vrsta_predracuna' => 1,	
							':pr_rata' => 1,	
							':pr_domaca_valuta' => $domaca_valuta,	
							':pr_vrijednost_BAM' => $rata_bam_f,	
							':pr_vrijednost_RSD' => $rata_rsd_f,	
							':pr_vrijednost_EUR' => $rata_eur_f,	
							':pr_file' => $file_name,
							':pr_status' => $pr_status,
							':pr_uplaceno' => $pr_uplaceno,
							':pr_datum_uplate' => $pr_datum_uplate
							));

				//Get last ID
				$predracun_id = $db->lastInsertId();
				
				if($drzava == "Srbija"){
					createPredracunSRB($predracun_id);
					$putanja_uplatnica = createUplatnicaSRB($predracun_id);
					$putanja_ugovor = createUgovorSRB($id_kand);
					//createInfoListSRB();
					if($ugovor_novi != 99){
						sendMailPredracunUgovorSRB($file_name, $putanja_ugovor, $putanja_uplatnica, $id_kand);
					}
				}
				else{
					createPredracunBIH($predracun_id);
					$putanja_uplatnica = createUplatnicaBIH($predracun_id);
					$putanja_ugovor = createUgovorBIH($id_kand);
					//fja za slanje maila za bih
					if($ugovor_novi == 11 OR $ugovor_novi == 12){
						sendMailPredracunUgovorBIH("", $putanja_ugovor, "ne", $id_kand);
					}elseif($ugovor_novi == 99){
						
					}else{
						sendMailPredracunUgovorBIH($file_name, $putanja_ugovor, $putanja_uplatnica, $id_kand);
					}
				}
			}
			//KREIRANJE NOVIH OBRACUNA
			if($pr_stornirano == 1){
				//ne treba ubacivati nove obracune
			}else{
				ubaciObracune($predracun_id);
			}
			arhiviranReminderUgovor($id_kand);
			$log_desc = "DIPL -> Izvrsena PROMJENA UGOVORA za kandidata ID = [".$id_kand."] sa ugovora BROJ = [".$vrstaugovora."] na ugovor BROJ = [".$ugovor_novi."].";
			$log_date = date('Y-m-d H:i:s');

			$log_query = $db->prepare("
				INSERT INTO idk_logs
					(log_employeeid, log_desc, log_date)
				VALUES
					(:log_employeeid, :log_desc, :log_date)
			");

			$log_query->execute(array(
				':log_employeeid' => $logged_employee_id,
				':log_desc' => $log_desc,
				':log_date' => $log_date
			));
		//-----------------------------------------------------------------
		//Dodavanje Novih END
		
		if($status == 1 AND $pstatus != 5){
			$vrijeme_stari_status = $db->prepare("
				SELECT id_log_status_nd_kandidata, vrijeme_promjene_statusa_nd_kandidata
				FROM idk_nd_kandidata_status_log
				WHERE idd_broj_nd_kandidata = :idd_broj_nd_kandidata AND broj_dana_statusa_nd_kandidata is null
			");
			$vrijeme_stari_status->execute(array(
				':idd_broj_nd_kandidata' => $id_kand
			));
			$vrijeme_stari_status_row = $vrijeme_stari_status->fetch();
			$id_log_statusa = $vrijeme_stari_status_row['id_log_status_nd_kandidata'];
			$vrijeme_log_statusa = $vrijeme_stari_status_row['vrijeme_promjene_statusa_nd_kandidata'];
			
			$trenutno_vrijeme_statusne_promjene = date('Y-m-d H:i:s');
			
			$diff_novi = strtotime($trenutno_vrijeme_statusne_promjene) - strtotime($vrijeme_log_statusa);
			$day_novi = floor($diff_novi/86400);
			
			$update_vrijeme_stari = $db->prepare("
							UPDATE idk_nd_kandidata_status_log
							SET broj_dana_statusa_nd_kandidata = :broj_dana_statusa_nd_kandidata
							WHERE idd_broj_nd_kandidata = :idd_broj_nd_kandidata AND id_log_status_nd_kandidata = :id_log_status_nd_kandidata");

			$update_vrijeme_stari->execute(array(
						':idd_broj_nd_kandidata' => $id_kand,
						':id_log_status_nd_kandidata' => $id_log_statusa,
						':broj_dana_statusa_nd_kandidata' => $day_novi
						));
						
			$new_ND_status = $db->prepare("
								INSERT INTO idk_nd_kandidata_status_log
								(
									idd_broj_nd_kandidata,
									status_nd_kandidata,
									pstatus_nd_kandidata,
									vrijeme_promjene_statusa_nd_kandidata,
									promjenio_zaposlenik_nd_kandidata
								)
								VALUES
								(
									:idd_broj_nd_kandidata,
									:status_nd_kandidata,
									:pstatus_nd_kandidata,
									:vrijeme_promjene_statusa_nd_kandidata,
									:promjenio_zaposlenik_nd_kandidata
								)
			");
			
			$new_ND_status->execute(array(
							':idd_broj_nd_kandidata' => $id_kand,
							':status_nd_kandidata' => 1,
							':pstatus_nd_kandidata' => 5,
							':vrijeme_promjene_statusa_nd_kandidata' => date('Y-m-d H:i:s'),
							':promjenio_zaposlenik_nd_kandidata' => $logged_employee_id
							));
			//Update proslog statusa END
			$query_p_s = $db->prepare("
				UPDATE idk_nd_kandidata
				SET pstatus_nd_kandidata = :pstatus_nd_kandidata
				WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata");

			$query_p_s->execute(array(
						':id_broj_nd_kandidata' => $id_kand,
						':pstatus_nd_kandidata' => 5
						));

			updateProjekcijeKandidat(1, $id_kand);
		}
		if($ugovor_novi == 99){
			promjenaStatusaDIPLKandidat($id_kand, 2, 0, 1);
			sendMailViberCheckListDIPL($predracun_id);
		}
	}
}

//FunkcijA za provjeru uslova za pregled profila kandidata pod određenim uslovima 
function omoguciPregledDIPLkandidata($id_kandidata, $emp_status, $emp_supervizor){
	Global $db;
	Global $logged_employee_id;
	//Potreban ispis informacija o kandidatu (Informacije u Select navedene)
	$query1 = $db->prepare("
		SELECT ime_nd_kandidata, prezime_nd_kandidata, status_nd_kandidata, pstatus_nd_kandidata, zaduzen_zaposlenik_nd_kandidata 
		FROM idk_nd_kandidata
		WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata
	");
	$query1->execute(array(
		':id_broj_nd_kandidata' => $id_kandidata
	));
	$row1 = $query1->fetch();
	$ime_kan = $row1["ime_nd_kandidata"];
	$prezime_kan = $row1["prezime_nd_kandidata"];
	$status_kan = intval($row1["status_nd_kandidata"]);
	$pstatus_kan = intval($row1["pstatus_nd_kandidata"]);
	$zaduzen_za_kan = intval($row1["zaduzen_zaposlenik_nd_kandidata"]);
	//Potrebna provjera da li kandidat ima ima bilo kakav predracun da je na statusu inkaso - ako ima nece vidjeti kandidata
	$query2 = $db->prepare("
		SELECT 
			pr_status, 
			pr_stari_ink_status,
			pr_broj_predracuna
		FROM 
			idk_predracuni
		WHERE 
			pr_kandidat_id = :pr_kandidat_id 
			AND 
			pr_vrsta_predracuna = 1 
			AND
			pr_status != 0 
		ORDER BY pr_id ASC
	");
	$query2->execute(array(
		':pr_kandidat_id' => $id_kandidata
	));
	if($query2->rowCount() != 0){
		while($row2 = $query2->fetch()){
			$status_predracun = intval($row2["pr_status"]);
			$inkaso_predracun = $row2["pr_stari_ink_status"];
			if($status_predracun == 1 AND $inkaso_predracun == NULL){
				$broj_inkaso_predracuna = 0;
			}else if($status_predracun == 1 AND $inkaso_predracun != NULL){
				$broj_inkaso_predracuna = 1;
				break;
			}else{
				if($status_predracun == 3 OR $status_predracun == 4 OR $status_predracun == 5){
					$broj_inkaso_predracuna = 1;
					break;
				}else{
					$broj_inkaso_predracuna = 0;
				}
			}
		}
	}else{
		$broj_inkaso_predracuna = 0;
	}
	
	//broj_inkaso_predracuna Ako je 1 - vidi inkaso, Ako je 0 - vidi menadzer
	
	if((in_array( "1" , $emp_status)) OR (in_array( "7" , $emp_status)) OR (in_array( "9" , $emp_status)) OR (in_array( "2" , $emp_supervizor)) OR (in_array( "3" , $emp_supervizor)) OR (in_array( "7" , $emp_supervizor)) OR (in_array( "9" , $emp_supervizor)) OR (in_array( "15" , $emp_supervizor))){
		//Ovaj uslov ima dozvolu za pregled kandidata bez obzira na ostale kriterije
	}else if((in_array( "2" , $emp_status)) OR (in_array( "3" , $emp_status)) OR (in_array( "16" , $emp_status)) OR (in_array( "16" , $emp_supervizor)) OR (in_array( "14" , $emp_status)) OR (in_array( "15" , $emp_status))){
		if((in_array( "14" , $emp_status)) AND $broj_inkaso_predracuna != 0){
			//Ovdje vide inkaso agenti
		}else if(((in_array( "16" , $emp_status)) OR (in_array( "16" , $emp_supervizor))) AND ($status_kan == 2 OR $status_kan == 3 OR $status_kan == 4 OR $status_kan == 5 OR $status_kan == 6)){
			//Ovdje vidi odjel prodaje
		}else{
			if(($status_kan == 1 OR $status_kan == 6 OR $status_kan == 7)){
				if($zaduzen_za_kan == $logged_employee_id){
					if($broj_inkaso_predracuna == 0){
						//Kad je ispunjen ovaj uslov Menadzer ima pregled kandidata
					}else{
						echo '
							<div class="alert material-alert material-alert_danger">
								<h4>NEMATE PRIVILEGIJE!</h4>
								<p>Nemate mogućnost pregleda kandidata <span style = "font-weight: bold;">'.$ime_kan.' '.$prezime_kan.'</span> jer kandidata obrađuje INKASO odjel!</p>
								<br />
								<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
							</div>
						';
						exit();
					}
					
				}else{
					echo '
						<div class="alert material-alert material-alert_danger">
							<h4>NEMATE PRIVILEGIJE!</h4>
							<p>Nemate mogućnost pregleda kandidata <span style = "font-weight: bold;">'.$ime_kan.' '.$prezime_kan.'</span> jer niste zaduženi za istog!</p>
							<br />
							<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
						</div>
					';
					exit();
				}
			}else{
				echo '
					<div class="alert material-alert material-alert_danger">
						<h4>NEMATE PRIVILEGIJE!</h4>
						<p>Nemate mogućnost pregleda kandidata <span style = "font-weight: bold;">'.$ime_kan.' '.$prezime_kan.'</span> jer se kandidat nalazi u Obradi!</p>
						<br />
						<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
					</div>
				';
				exit();
			}
		}
	}else{
		echo '
			<div class="alert material-alert material-alert_danger">
				<h4>NEMATE PRIVILEGIJE!</h4>
				<p>Nemate mogućnost pregleda kandidata <span style = "font-weight: bold;">'.$ime_kan.' '.$prezime_kan.'</span>. Kontaktirajte Administratora za pomoć!</p>
				<br />
				<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
			</div>
		';
		exit();
	}
}

function arhivirajUgovorPredracunDIPL($id_kan){
	Global $db;
	Global $logged_employee_id;
	if(isset($id_kan)){
		if(getBrPredracunaNDKanR($id_kan) > 0){
			$query = $db->prepare("
							SELECT pr_id, pr_naplata_preko
							FROM idk_predracuni
							WHERE pr_kandidat_id = :pr_kandidat_id AND pr_vrsta_predracuna = :pr_vrsta_predracuna AND pr_status != 0
			");
			$query->execute(array(
							':pr_kandidat_id' => $id_kan,
							':pr_vrsta_predracuna' => 1
			));
			while($row = $query->fetch()){
				$pr_id = $row["pr_id"];
				$pr_naplata_preko = intval($row["pr_naplata_preko"]);
				
				//Update starog predracuna - status arhiva
				$update_stari_predracun = $db->prepare("
					UPDATE idk_predracuni
					SET pr_status = :pr_status
					WHERE pr_id = :pr_id AND pr_kandidat_id = :pr_kandidat_id
				");
				$update_stari_predracun->execute(array(
					':pr_status' => 0,
					':pr_id' => $pr_id,
					':pr_kandidat_id' => $id_kan
				));
				
				//Update obracuna za stari predracun
				$update_obracune = $db->prepare("
					UPDATE idk_obracuni
					SET status_obracuna = :status_obracuna
					WHERE predracun_id = :predracun_id
				");
				$update_obracune->execute(array(
					':status_obracuna' => 2,
					':predracun_id' => $pr_id
				));
			}
			
			$update_stari_ugovor = $db->prepare("
				UPDATE idk_nd_kandidata
				SET vrsta_ugovora_nd_kandidata = :vrsta_ugovora_nd_kandidata
				WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata
			");
			$update_stari_ugovor->execute(array(
				':vrsta_ugovora_nd_kandidata' => NULL,
				':id_broj_nd_kandidata' => $id_kan
			));
			//Sad je potrebno staviti ugovor i uplatnicu iz dokumenata kao neaktivne dokumente
			
			if($pr_naplata_preko == 1 OR $pr_naplata_preko == 2){
				//update ugovora koji su preko CH
				$update_stari_ugovor = $db->prepare("
					UPDATE idk_nd_ugovori
					SET ug_status = :ug_status, ug_datum_arhiviranja = NOW()
					WHERE ug_kandidat_id = :ug_kandidat_id
				");
				$update_stari_ugovor->execute(array(
					':ug_status' => 0,
					':ug_kandidat_id' => $id_kan
				));
			}else{
				$update_stari_ugovor = $db->prepare("
					UPDATE idk_nd_kandidata_dokumenti
					SET tip_dokumenta_status = :tip_dokumenta_status
					WHERE id_kandidata_dokument_nd = :id_kandidata_dokument_nd AND tip_dokumenta = :tip_dokumenta AND tip_dokumenta_status != 0
				");
				$update_stari_ugovor->execute(array(
					':tip_dokumenta_status' => 0,
					':id_kandidata_dokument_nd' => $id_kan,
					':tip_dokumenta' => 1
				));
			}
			
			$update_stara_uplatnica = $db->prepare("
				UPDATE idk_nd_kandidata_dokumenti
				SET tip_dokumenta_status = :tip_dokumenta_status
				WHERE id_kandidata_dokument_nd = :id_kandidata_dokument_nd AND tip_dokumenta = :tip_dokumenta AND tip_dokumenta_status != 0
			");
			$update_stara_uplatnica->execute(array(
				':tip_dokumenta_status' => 0,
				':id_kandidata_dokument_nd' => $id_kan,
				':tip_dokumenta' => 2
			));
			
			$log_desc = "DIPL -> Arhiviran predračun ID = [".$pr_id."] za kandidata [".$id_kan."]. Arhiviran ugovor i uplatnica. ";
			$log_date = date('Y-m-d H:i:s');

			$log_query = $db->prepare("
				INSERT INTO idk_logs
					(log_employeeid, log_desc, log_date)
				VALUES
					(:log_employeeid, :log_desc, :log_date)
			");

			$log_query->execute(array(
				':log_employeeid' => $logged_employee_id,
				':log_desc' => $log_desc,
				':log_date' => $log_date
			));
		}
	}
}

function getBrojArhiviranihPredracunaDIPL($id_kan){
	Global $db;
	$query1 = $db->prepare("
		SELECT count(pr_id) as broj1
		FROM idk_predracuni
		WHERE pr_kandidat_id = :pr_kandidat_id AND pr_vrsta_predracuna = :pr_vrsta_predracuna AND pr_status != 0
	");
	$query1->execute(array(
		':pr_kandidat_id' => $id_kan,
		':pr_vrsta_predracuna' => 1
	));
	$row1 = $query1->fetch();
	$brojAktivnih = intval($row1["broj1"]);
	
	$query = $db->prepare("
		SELECT count(pr_id) as broj
		FROM idk_predracuni
		WHERE pr_kandidat_id = :pr_kandidat_id AND pr_vrsta_predracuna = :pr_vrsta_predracuna AND pr_status = 0
	");
	$query->execute(array(
		':pr_kandidat_id' => $id_kan,
		':pr_vrsta_predracuna' => 1
	));
	$row = $query->fetch();
	$brojArhiviranih = intval($row["broj"]);
	//Kombinacije
	//Aktivni	Arhivirani	RETURN
	//0			0			0
	//0			1			1 -> znaci treba mi ova kombinacija u kojoj nema ni jedan aktivan predracun ali ima jedan arhiviran ili vise arhiviranih
	//1			0			0
	//1			1			0
	
	if($brojAktivnih == 0 AND $brojArhiviranih != 0){
		return 1;
	}else if($brojAktivnih == 0 AND $brojArhiviranih == 0){
		return 0;
	}else if($brojAktivnih != 1 AND $brojArhiviranih == 0){
		return 0;
	}else if($brojAktivnih != 0 AND $brojArhiviranih != 0){
		return 0;
	}
}

function setInboundCallStatus($kandidatId, $status){
	Global $db;
	Global $logged_employee_id;
	//Funkcija prima dva parametra
	//	$kandidatId -> kandidat id iz tabele idk_nd_kandidata
	// 	$status -> moguce vrijednosti parametra status su: 0 - obrađen inbound, 1 - desio se inbound
	//Funkcija ne vraća ništa

	//START ***************************
		$kandidatId = intval($kandidatId); 
		$status = intval($status);
		$flag = 0;
		if($kandidatId != 0 AND ($status == 0 OR $status == 1)){
			$query = $db->prepare("
				SELECT 
					inbound_aktivan
				FROM 
					idk_nd_kandidata
				WHERE 
					id_broj_nd_kandidata = :id_broj_nd_kandidata
			");
			$query->execute(array(
				':id_broj_nd_kandidata' => $kandidatId
			));

			$row = $query->fetch();

			$trenutniStatus = intval($row["inbound_aktivan"]);

			if($status == 1){
				if($trenutniStatus == 0){
					$flag = 1;
				}
			}else{
				if($trenutniStatus == 1){
					$flag = 1;
				}
			}

			if($flag == 1){
				$queryUpdate = $db->prepare("
					UPDATE idk_nd_kandidata
					SET inbound_aktivan = :inbound_aktivan
					WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata
				");
				$queryUpdate->execute(array(
					':id_broj_nd_kandidata' => $kandidatId,
					':inbound_aktivan' => $status
				));
				
				if($status == 1){
					$log_desc = "INBOUND POZIV DIPL -> Desio se inbound poziv za kandidata ID = [".$kandidatId."]. Inbound komunikaciju unio zaposlenik ID = [".$logged_employee_id."].";
				}else{
					$log_desc = "INBOUND POZIV DIPL -> Zaposlenik obradio inbound poziv za kandidata ID = [".$kandidatId."].";
				}
				
				$log_date = date('Y-m-d H:i:s');
				$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_employeeid, log_desc, log_date)
					VALUES
						(:log_employeeid, :log_desc, :log_date)
				");
				$log_query->execute(array(
					':log_employeeid' => $logged_employee_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date
				));
			}
		}
	//END *****************************
}

//Modul Nostrifikacija Diploma END

function getZaposlenikDepartmentR($employee_id) {

	Global $db;

	$query = $db->prepare("
					SELECT employee_odjel
					FROM idk_employees
					WHERE employee_id = :employee_id");

	$query->execute(array(
				':employee_id' => $employee_id));

	$employee = $query->fetch();
	$employee_odjel_id = $employee['employee_odjel'];

	return $employee_odjel_id;
}

function checkUserR($kandidat_id, $kandidat_check){
	Global $db;

	$query = $db->prepare("
					SELECT kandidat_id
					FROM idk_kandidati
					WHERE kandidat_id = :kandidat_id AND kandidat_check = :kandidat_check");

	$query->execute(array(
			':kandidat_id' => $kandidat_id,
			':kandidat_check' => $kandidat_check
			));

	$check = $query->rowCount();

	return $check;

}

function notifForOnlneRegister($kandidat_id, $kandidat_check){
	Global $db;
	
	$not_url = "kandidati?page=open&id=$kandidat_id";
	$not_date = date('Y-m-d H:i:s');
	$kandidat_ime_prezime = getCandidateFullnameR($kandidat_id);

	
	$query = $db->prepare("
					SELECT not_kandidatid
					FROM idk_notification
					WHERE not_kandidatid = :not_kandidatid AND not_type = :not_type");

	$query->execute(array(
			':not_kandidatid' => $kandidat_id,
			':not_type' => 1
			));

	$check = $query->rowCount();


	if($check == 0){
		//SEND NOTIFICATION FOR SUCCESSFUL ONLINE REGISTER
		
		$not_text = "$kandidat_ime_prezime je uspješno ispunio online obrazac.";
		$query = $db->prepare("
						INSERT INTO idk_notification
							(not_kandidatid, not_text, not_url, not_date, not_type, not_status)
						VALUES
							(:not_kandidatid, :not_text, :not_url, :not_date, :not_type, :not_status)");

		$query->execute(array(
					':not_kandidatid' => $kandidat_id,
					':not_text' => $not_text,
					':not_url' => $not_url,
					':not_date' => $not_date,
					':not_type' => 1,
					':not_status' => 0
					));

	}else{
		
		$not_text = "$kandidat_ime_prezime se ponovo prijavio.";
		$query = $db->prepare("
						INSERT INTO idk_notification
							(not_kandidatid, not_text, not_url, not_date, not_type, not_status)
						VALUES
							(:not_kandidatid, :not_text, :not_url, :not_date, :not_type, :not_status)");

		$query->execute(array(
					':not_kandidatid' => $kandidat_id,
					':not_text' => $not_text,
					':not_url' => $not_url,
					':not_date' => $not_date,
					':not_type' => 2,
					':not_status' => 0
					));
	}

}

function notifForPonovnaPrijava($kandidat_id, $kandidat_check){
	Global $db;
	
	$not_url = "kandidati?page=open&id=$kandidat_id";
	$not_date = date('Y-m-d H:i:s');
	$kandidat_ime_prezime = getCandidateFullnameR($kandidat_id);
	$not_text = "$kandidat_ime_prezime se ponovo prijavio.";
	$query = $db->prepare("
					INSERT INTO idk_notification
						(not_kandidatid, not_text, not_url, not_date, not_type, not_status)
					VALUES
						(:not_kandidatid, :not_text, :not_url, :not_date, :not_type, :not_status)");

	$query->execute(array(
				':not_kandidatid' => $kandidat_id,
				':not_text' => $not_text,
				':not_url' => $not_url,
				':not_date' => $not_date,
				':not_type' => 2,
				':not_status' => 0
				));
}

function sendCandidateRegMail($kandidat_id) {
	Global $db;

	// GET CANDIDATE EMAIL
	$querySendMail = $db->prepare("
					SELECT kandidat_email, kandidat_mailsent
					FROM idk_kandidati
					WHERE kandidat_id = :kandidat_id
					");

	$querySendMail->execute(array(
		":kandidat_id" => $kandidat_id
	));

	$rowMail = $querySendMail->fetch();
		$kandidat_email = $rowMail['kandidat_email'];
		$kandidat_mailsent = $rowMail['kandidat_mailsent'];

	// GET REGISTRATION EMAIL
	$query_email_registration = $db->prepare("
					SELECT email_title, email_txt
					FROM idk_email_text
					WHERE email_value = :email_value");

	$query_email_registration->execute(array(
				':email_value' => 'email_registracija'));

	$row_email_registration = $query_email_registration->fetch();

		$email_title = $row_email_registration['email_title'];
		$email_txt = $row_email_registration['email_txt'];


	if (!empty($kandidat_email) AND $kandidat_mailsent == 0) {
	$mail = new PHPMailer;

	$mail->isSMTP();											// Set mailer to use SMTP

	$mail->Host = 'smtp.strato.de;smtp.strato.de';				// Specify main and backup SMTP servers
	$mail->SMTPAuth = true;										// Enable SMTP authentication
	$mail->Username = 'no-reply@wwtravel.net';					// SMTP username
	$mail->Password = 'fdsaSD43fds';							// SMTP password
	$mail->SMTPSecure = 'ssl';									// Enable TLS encryption, `ssl` also accepted
	$mail->Port = 465;											// TCP port to connect to
	$mail->CharSet = 'UTF-8';

	$mail->setFrom('info@job-step.net', 'info@job-step.net');     		// Add a recipient
	$mail->addAddress($kandidat_email, $kandidat_email);		// Add a recipient

	$mail->Subject ="$email_title";
	$mail->Body    = "$email_txt";
	$mail->AltBody = "$email_txt";

	if(!$mail->send()) {
		// echo 'Message could not be sent.';
		// echo 'Mailer Error: ' . $mail->ErrorInfo;
	}else{
		$updateSendMailStatus = $db->prepare("
						UPDATE idk_kandidati
						SET	kandidat_mailsent = :kandidat_mailsent
						WHERE kandidat_id = :kandidat_id
						");

		$updateSendMailStatus->execute(array(
					':kandidat_mailsent' => 1,
					':kandidat_id' => $kandidat_id
					));

	}
	}else{}

}

function updateNotificationStatus($update_not, $not_id){
	Global $db;

	$query = $db->prepare("
					UPDATE idk_notification
					SET	not_status = :not_status
					WHERE not_id = :not_id
					");

	$query->execute(array(
				':not_status' => $update_not,
				':not_id' => $not_id
				));

}

function getUnseenPonovnePrijave() {
	Global $db;

	$query_notifications_sum = $db->prepare("
					SELECT COUNT(not_id) as broj_neprocitanih_pri
					FROM idk_notification
					WHERE not_type = 2 AND not_status = 0
					");

	$query_notifications_sum->execute();

	$not_sum = $query_notifications_sum->fetch();
		$broj_neprocitanih_pri = $not_sum['broj_neprocitanih_pri'];

		return $broj_neprocitanih_pri;

}

function getUnredNotification() {
	Global $db;

	$query_notifications_sum = $db->prepare("
					SELECT COUNT(not_id) as broj_neprocitanih_not
					FROM idk_notification
					WHERE not_type = 1 AND not_status = 0
					");

	$query_notifications_sum->execute();

	$not_sum = $query_notifications_sum->fetch();
	$broj_neprocitanih_not = $not_sum['broj_neprocitanih_not'];
	
	echo $broj_neprocitanih_not;

}

function getProjectFullName($project_id) {

	Global $db;
	Global $logged_employee_id;

	$query_kandidat = $db->prepare("
					SELECT project_name
					FROM idk_projects
					WHERE project_id = :project_id");

	$query_kandidat->execute(array(
			':project_id' => $project_id
			));

	$kandidat = $query_kandidat->fetch();
		$project_name = $kandidat['project_name'];

		return $project_name;

}

//Site URL
function getIpWhitelistStatusR() {
	Global $db;

	$settings_query = $db->prepare("
							SELECT settings_value
							FROM idk_settings
							WHERE settings_name = :settings_name");

	$settings_query->execute(array(
						':settings_name' => 'crm_ip_blocker'));

	$settings = $settings_query->fetch();

	$crm_ip_blocker = $settings['settings_value'];

	return $crm_ip_blocker;
}

//Check modules

//Module projects
$query_projects = $db->prepare("
					SELECT module_status
					FROM idk_modules
					WHERE module_name = :module_name");

$query_projects->execute(array(
					':module_name' => 'projects'));

$row_projects = $query_projects->fetch();

$module_projects = $row_projects['module_status'];

//Module employees-reports
$query_employees_reports = $db->prepare("
								SELECT module_status
								FROM idk_modules
								WHERE module_name = :module_name");

$query_employees_reports->execute(array(
							':module_name' => 'employees-reports'));

$row_employees_reports = $query_employees_reports->fetch();

$module_employees_reports = $row_employees_reports['module_status'];

//Funkcija addToLogs
function addToLogs($log_desc, $log_type){
	Global $db;
	Global $logged_employee_id;
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_employeeid, log_desc, log_date, log_type)
					VALUES
						(:log_employeeid, :log_desc, :log_date, :log_type)");

	$log_query->execute(array(
					':log_employeeid' => $logged_employee_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date,
					':log_type' => $log_type));

	//Log za skladiste - $log_type = 1
	//Log za pozicije  - $log_type = 2
	//Log za biljeske  - $log_type = 3
}

function gelEmployeList() {
	Global $db;
	$query = $db->prepare("
					SELECT employee_firstname, employee_lastname, employee_id
					FROM idk_employees
					WHERE employee_status != 0
					");

	$query->execute();

	while($employee = $query->fetch()){
		echo "<option value='" . $employee['employee_id'] . "'>" . $employee['employee_firstname'] . " " . $employee['employee_lastname'] . "</option>";
	}
}

/* Get number of candidates per status */
function getEmployeesPerStatus($status) {
	Global $db;

	$query = $db->prepare("
					SELECT kandidat_id
					FROM idk_kandidati
					WHERE kandidat_status = $status");

	$query->execute();

	$count = $query->rowCount();

	return $count;

}
/* Get number of candidates per status per date */
function getEmployeesPerStatusDate($status, $date_from, $date_to) {
	Global $db;

	$query = $db->prepare("
					SELECT kandidat_id
					FROM idk_kandidati
					WHERE kandidat_status = :status AND kandidat_datetime BETWEEN :date_from AND :date_to");

	$query->execute(array(
			":status" => $status,
			":date_from" => $date_from,
			":date_to" => $date_to
	));

	$count = $query->rowCount();

	return $count;

}
/**
 ** Get number of candidates per status from DAK table **
 **
 **
 **/
function getEmployeesPerStatusDAK($status) {
	Global $db;

	$query = $db->prepare("
					SELECT id_dak_kandidat
					FROM idk_dak_kandidati
					WHERE status_dak_kandidat = $status");

	$query->execute();

	$count = $query->rowCount();

	return $count;

}

/**
 ** Get razlog odbijanja po idu table ro_usluge **
 **
 **
 **/
function getRazlogOdbijanja($id) {
	Global $db;

	$query = $db->prepare("
					SELECT naziv_ro_de
					FROM idk_ro_usluge
					WHERE id_ro = $id");

	$query->execute();

	$razlog = $query->fetch();

	return $razlog['naziv_ro_de'];

}

/* Get number of candidates per messenger status */
function getCandidatesPerMessengerStatus($status, $date_from, $date_to) {
	Global $db;

	$query = $db->prepare("
					SELECT kandidat_id
					FROM idk_kandidati
					WHERE kandidat_status_messenger = :status AND kandidat_status != 3 AND kandidat_datetime BETWEEN :date_from AND :date_to");

	$query->execute(array(
			":status" => $status,
			":date_from" => $date_from,
			":date_to" => $date_to
	));

	$count = $query->rowCount();

	return $count;

}

/* Get number of candidates per status with installed messenger */
function getCandidatesPerStatusWithMessenger($status, $date_from, $date_to) {
	Global $db;

	$query = $db->prepare("
					SELECT kandidat_id
					FROM idk_kandidati
					WHERE kandidat_status = :status AND (kandidat_status_messenger = 2 OR kandidat_status_messenger = 5) AND kandidat_datetime BETWEEN :date_from AND :date_to");

	$query->execute(array(
			":status" => $status,
			":date_from" => $date_from,
			":date_to" => $date_to
	));

	$count = $query->rowCount();

	return $count;

}

/* Get number of candidates which did not finish messenger */
function getNezavrseni($status, $date_from, $date_to) {
	Global $db;

	$query = $db->prepare("
					SELECT kandidat_id
					FROM idk_kandidati
					WHERE kandidat_status = :status AND kandidat_datetime BETWEEN :date_from AND :date_to");

	$query->execute(array(
			":status" => $status,
			":date_from" => $date_from,
			":date_to" => $date_to
	));

	$count = $query->rowCount();

	return $count;

}

/* Get number of candidates per status and registration url */
function getEmployeesPerStatusAndUrl($status, $kandidat_visitedurl) {
	Global $db;

	$query = $db->prepare("
					SELECT kandidat_id
					FROM idk_kandidati
					WHERE kandidat_status = $status AND kandidat_visitedurl = $kandidat_visitedurl
					");

	$query->execute();

	$count = $query->rowCount();

	echo $count;

}

/* Return number of candidates per status and registration url */
function getEmployeesPerStatusAndUrlR($status, $kandidat_visitedurl) {
	Global $db;

	$query = $db->prepare("
					SELECT kandidat_id
					FROM idk_kandidati
					WHERE kandidat_status = $status AND kandidat_visitedurl = $kandidat_visitedurl
					");

	$query->execute();

	$count = $query->rowCount();

	return $count;

}

/* Get number of candidates per nalog */
function getEmployeesPerNalog($status, $project_ids) {
	Global $db;

	$query = $db->prepare("
					SELECT kandidat_id, kandidat_ime, kandidat_prezime, kandidat_spol, kandidat_jmbg, kandidat_status, kandidat_slika, kandidat_email, kandidat_datetime, kandidat_visitedurl, kandidat_prijava_na, kandidat_group, cv_ba, cv_de
					FROM idk_kandidati
					INNER JOIN idk_project_kandidati ON idk_kandidati.kandidat_id = idk_project_kandidati.pk_kandidatid
					WHERE kandidat_status = $status AND pk_projectid IN($project_ids)
					");

	$query->execute();

	$count = $query->rowCount();

	echo $count;

}

/* Get number of candidates per project */
function getEmployeesPerProjects($status, $project_id) {
	Global $db;

	$query = $db->prepare("
					SELECT kandidat_id, kandidat_ime, kandidat_prezime, kandidat_spol, kandidat_jmbg, kandidat_status, kandidat_slika, kandidat_email, kandidat_datetime, kandidat_visitedurl, kandidat_prijava_na, kandidat_group, cv_ba, cv_de
					FROM idk_kandidati
					INNER JOIN idk_project_kandidati ON idk_kandidati.kandidat_id = idk_project_kandidati.pk_kandidatid
					WHERE kandidat_status = $status AND pk_projectid = $project_id
					");

	$query->execute();

	$count = $query->rowCount();

	echo $count;

}
/* Return number of candidates per project */
function getEmployeesPerProjectsR($status, $project_id) {
	Global $db;

	$query = $db->prepare("
					SELECT kandidat_id, kandidat_ime, kandidat_prezime, kandidat_spol, kandidat_jmbg, kandidat_status, kandidat_slika, kandidat_email, kandidat_datetime, kandidat_visitedurl, kandidat_prijava_na, kandidat_group, cv_ba, cv_de
					FROM idk_kandidati
					INNER JOIN idk_project_kandidati ON idk_kandidati.kandidat_id = idk_project_kandidati.pk_kandidatid
					WHERE kandidat_status = $status AND pk_projectid = $project_id
					");

	$query->execute();

	$count = $query->rowCount();

	return $count;

}

// GET POSITIONS LIST FOR EDITING CV'S
function getPositionsList() {
	Global $db;
	$query = $db->prepare("
					SELECT kp_id, kp_ime
					FROM  idk_kandidat_pozicija
					");

	$query->execute();

	while($employee = $query->fetch()){
		echo "<option value='" . $employee['kp_id'] . "'>" . $employee['kp_ime'] . "</option>";
	}
}

// GET NALOG LIST FOR ADDING CANDIDATES TO PROJECT
function getNalogListForProject(){
	Global $db;

	$query = $db->prepare("
                        SELECT nalog_id, nalog_broj, nalog_naziv, nalog_opis
                        FROM idk_nalozi
                        WHERE nalog_status != :nalog_status");

	$query->execute(array(':nalog_status' => 8));

	while($row = $query->fetch()){
		echo "<option value='" . $row['nalog_id'] . "'>" . $row['nalog_broj'] . " - " . $row['nalog_naziv'] . "</option>";
	}

}

function getNalogListByCandidateId($kandidat_id){
	Global $db;
	$return_string = "";
	//Prvi dio provjerava da li kandidat ima nalog_id u tabeli kandidati 
	$query_check_nalog_id_from_candidates = $db->prepare('
		SELECT kandidat_nalog_id, kandidat_zaposlen_kod
		FROM idk_kandidati
		WHERE kandidat_id = :kandidat_id
	');
	$query_check_nalog_id_from_candidates -> execute(array(':kandidat_id' => $kandidat_id));
	$row_query_check = $query_check_nalog_id_from_candidates -> fetch();
	$nalog_id 				= $row_query_check['kandidat_nalog_id'];
	$kandidat_zaposlen_kod 	= $row_query_check['kandidat_zaposlen_kod'];

	if(!is_null($nalog_id)){
		$query_get_nalog_naziv = $db -> prepare('
			SELECT nalog_naziv, nalog_broj
			FROM idk_nalozi
			WHERE nalog_id = :nalog_id
		');
		$query_get_nalog_naziv -> execute(array(':nalog_id' => $nalog_id));
		$row_get_nalog_naziv = $query_get_nalog_naziv -> fetch();
		$nalog_naziv 	= $row_get_nalog_naziv['nalog_naziv'];
		$nalog_broj 	= $row_get_nalog_naziv['nalog_broj'];
					
		$return_string = '
			<input type="hidden" name="kandidat_id" value="'.$kandidat_id.'"/>
			<div class="form-group">
				<label for="lg_nalog" class="col-sm-3 control-label"><span class="text-danger">*</span> Nalog:</label>
				<div class="col-sm-9">
					<select class="selectpicker" id="lg_nalog" data-live-search="true" name="lg_nalog" onchange="promjena_pozicije()">
						<option selected value="'.$nalog_id.'">'.$nalog_broj.' | '.$nalog_naziv.'</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="lg_project" class="col-sm-3 control-label"><span class="text-danger">*</span> Projekat:</label>
				<div class="col-sm-9" id="divura">
					<select  class="selectpicker" id="lg_project" data-live-search="true" name="lg_project">
						'.getProjectListByNalogId($nalog_id).'
					</select>
				</div>
			</div>

		';
		
	}
	else if(checkIfCandidateZaposlen($kandidat_id)){
		$possible_nalog_ids = zavrsenCandidatePossibleNalogIds($kandidat_id);
		// var_dump($possible_nalog_ids);
		if($possible_nalog_ids){
			$query_get_nalog_names = $db -> prepare('
				SELECT nalog_id, nalog_naziv, nalog_broj
				FROM idk_nalozi
				WHERE nalog_id IN ('.implode(',', $possible_nalog_ids).')
			');
			$query_get_nalog_names -> execute();
			$cnt_nalog = $query_get_nalog_names -> rowCount();
			$nalog_options = '';
			while($row_get_nalog_names = $query_get_nalog_names -> fetch()){
				$nalog_id 		= $row_get_nalog_names['nalog_id'];
				$nalog_naziv 	= $row_get_nalog_names['nalog_naziv'];
				$nalog_broj 	= $row_get_nalog_names['nalog_broj'];
				$nalog_options .= '<option value="'.$nalog_id.'">'.$nalog_broj.' | '.$nalog_naziv.'</option>';
			}
			if($cnt_nalog == 1){
				$return_string = '
					<input type="hidden" name="kandidat_id" value="'.$kandidat_id.'"/>
					<div class="form-group">
						<label for="lg_nalog" class="col-sm-3 control-label"><span class="text-danger">*</span> Nalog:</label>
						<div class="col-sm-9">
							<select class="selectpicker" id="lg_nalog" data-live-search="true" name="lg_nalog" onchange="promjena_pozicije()">
								'.str_replace('value','selected value',$nalog_options).'
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="lg_project" class="col-sm-3 control-label"><span class="text-danger">*</span> Projekat:</label>
						<div class="col-sm-9" id="divura">
							<select  class="selectpicker" id="lg_project" data-live-search="true" name="lg_project">
								'.getProjectListByNalogId($nalog_id).'
							</select>
						</div>
					</div>
				';
			}
			else{
				$return_string = '
					<input type="hidden" name="kandidat_id" value="'.$kandidat_id.'"/>
					<div class="form-group">
						<label for="lg_nalog" class="col-sm-3 control-label"><span class="text-danger">*</span> Nalog:</label>
						<div class="col-sm-9">
							<select class="selectpicker" id="lg_nalog" data-live-search="true" name="lg_nalog" onchange="promjena_pozicije()">
								<option disabled selected value = "0">Odaberi</option>
								'.$nalog_options.'
							</select>
						</div>
					</div>
					<div class="form-group" id = "project_pick" style = "display:none">
						
					</div>
				';		
			}
		}
		else{
			echo 'Kandidat je zaposlen kod "'.getCompanyNameById($kandidat_zaposlen_kod).'". Sistem nije uspio detektovati u kojem nalogu.';
		}
		
	}
	else{
		$query_get_nalog_names = $db -> prepare('
			SELECT n.nalog_id, n.nalog_naziv, pr.project_name, n.nalog_broj
			FROM idk_nalozi n
			JOIN idk_projects pr
			ON pr.project_nalogid = n.nalog_id
			JOIN (
				SELECT sqpk.pk_projectid
				FROM idk_project_kandidati sqpk
				WHERE sqpk.pk_kandidatid = :kandidat_id
			) pk
			ON pk.pk_projectid = pr.project_id
			WHERE nalog_status != 12
			AND nalog_id != 44
		');
		$query_get_nalog_names -> execute(array(':kandidat_id' => $kandidat_id));
		$nalog_options = "";
		while($row_get_nalog_names = $query_get_nalog_names -> fetch()){
			$nalog_id 		= $row_get_nalog_names['nalog_id'];
			$nalog_naziv 	= $row_get_nalog_names['nalog_naziv'];
			$project_name 	= $row_get_nalog_names['project_name'];				
			$nalog_broj 	= $row_get_nalog_names['nalog_broj'];				
			if(		
					strpos($project_name, 'BOT - ispunjava uslove') 	!= false
				OR	strpos($project_name, 'Završeni kandidati') 		!= false
				OR	strpos($project_name, 'Obrađeno') 					!= false
				OR	strpos($project_name, 'Intervju') 					!= false
				OR	strpos($project_name, 'Casting') 					!= false
				OR	strpos($project_name, 'Ugovor') 					!= false
				OR	strpos($project_name, 'Baza - odgovara za nalog') 	!= false
			){
				$nalog_options .= '<option value="'.$nalog_id.'">'.$nalog_broj.' | '.$nalog_naziv.'</option>';
			}
		}
		if($nalog_options == ''){
			$query_get_all_nalozi = $db -> prepare("
				SELECT nalog_id, nalog_naziv, nalog_broj
				FROM idk_nalozi
				LEFT JOIN idk_nedostupan_log ON idk_nalozi.nalog_id = idk_nedostupan_log.nalog AND kandidat = $kandidat_id AND idk_nedostupan_log.status = 1 AND idk_nedostupan_log.brojac != 2
				WHERE nalog_status != 12
				AND nalog_status != 8
				AND idk_nedostupan_log.id IS NULL
				ORDER BY (nalog_naziv) ASC
			");
			$query_get_all_nalozi -> execute();
			while($row_get_all_nalozi = $query_get_all_nalozi -> fetch()){
				$nalog_id 		= $row_get_all_nalozi['nalog_id'];
				$nalog_naziv 	= $row_get_all_nalozi['nalog_naziv'];
				$nalog_broj 	= $row_get_all_nalozi['nalog_broj'];
				$nalog_options .= '<option value="'.$nalog_id.'">'.$nalog_broj.' | '.$nalog_naziv.'</option>';
			}
		}
		
		$return_string = '
			<input type="hidden" name="kandidat_id" value="'.$kandidat_id.'"/>
			<div class="form-group">
				<label for="lg_nalog" class="col-sm-3 control-label"><span class="text-danger">*</span> Nalog:</label>
				<div class="col-sm-9">
					<select class="selectpicker" id="lg_nalog" data-live-search="true" name="lg_nalog" onchange="promjena_pozicije()">
						<option disabled selected value = "0">Odaberi</option>
						'.$nalog_options.'
					</select>
				</div>
			</div>
			<div class="form-group" id = "project_pick" style = "display:none">
				
			</div>
		';				
	}
	return $return_string;

}

function getProjectListWithoutNalog(){
	Global $db;

	$select_query = $db->prepare("
						SELECT project_id, project_name, project_status, project_nalogid
						FROM idk_projects
						WHERE project_status != 0 AND project_nalogid = 0 OR project_nalogid IS NULL");

	$select_query->execute();

	while($select_row = $select_query->fetch()) {
		echo "<option value='" . $select_row['project_id'] . "'>" . $select_row['project_name'] . "</option>";
	}
}

function generateRandomString($length = 6) {
	$characters = '0123456789';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}

function generateRandomPassword($length) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
	
	for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }

    return $randomString;
}

//SMS ZA MESSENGER
function sendSmsToCandidate1($sifra, $phone_f, $kandidat_prijava_na){
	Global $db;

	$secret_token = 'aebf56deb2ef2ff5ccfd8f48456f8bb4';
	$received_message_origin = "IDK_STUDIO";
	$received_message_content = "JOBSTEP Agencija Vam se zahvaljuje na registraciji. U narednoj SMS poruci cete dobiti link putem kojeg mozete preuzeti nasu aplikaciju.";
	$received_message_number = $phone_f;

	$message_content_formatted = str_replace("@","(at)", $received_message_content);
	$message_content_formatted = str_replace("đ","dj", $message_content_formatted);
	$message_content_formatted = str_replace("Đ","DJ", $message_content_formatted);
	$message_content_formatted = rawurlencode(iconv("UTF-8", "ASCII//TRANSLIT", $message_content_formatted) );

	// INSERT INTO DB THE RETREIVED SMS
	$db_message_query = $db->prepare("
					INSERT INTO idk_sms_messages
						(sms_origin, sms_number, sms_content, sms_status, sms_created_at, sms_type)
					VALUES
						(:sms_origin, :sms_number, :sms_content, :sms_status, :sms_created_at, :sms_type)");

	$db_message_query->execute(array(
					':sms_origin' => $received_message_origin,
					':sms_number' => $received_message_number,
					':sms_content' => $message_content_formatted,
					':sms_status' => 0,
					':sms_type' => 1,
					':sms_created_at' => date('Y-m-d H:i:s')
					));
	$message_id = $db->lastInsertId();

	/*
	 * NTH SMS Gateway INFO
	 */
	$nth_gateway_endpoint = 'http://bulk.mobile-gw.com';
	$nth_gateway_endpoint_port = '9000';
	$nth_customer_username = 'IDK_Acc';
	$nth_customer_password = 'GadOSYY2';

	// Additional gateway options

	$nth_gateway_message_allow_adaption = 1;

	/*
	 * Building URL
	 */
	$gateway_url = $nth_gateway_endpoint . ':' . $nth_gateway_endpoint_port . '/?username=' . $nth_customer_username . '&password=' . $nth_customer_password;
	// Adding options
	$gateway_url .= '&allow_adaption=' . $nth_gateway_message_allow_adaption;
	// Adding notification info
	$gateway_url .= '&status_report=' . 7;
	$gateway_url .= '&status_url=' . urlencode(getSiteUrlr().'get_sms_notification.php');
	// Adding SMS message info
	$gateway_url .= '&origin=' . $received_message_origin;
	$gateway_url .= '&call-number=' . $received_message_number;
	$gateway_url .= '&text=' . $message_content_formatted;
	$gateway_url .= '&messageid=' . $message_id;

	$headers = [];
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $gateway_url);
	//Set CURLOPT_RETURNTRANSFER so that the content is returned as a variable.
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	//Set CURLOPT_FOLLOWLOCATION to true to follow redirects.
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	//Get headers
	curl_setopt($ch, CURLOPT_HEADERFUNCTION,
	  function($curl, $header) use (&$headers)
	  {
		$len = strlen($header);
		$header = explode(':', $header, 2);
		if (count($header) < 2) // ignore invalid headers
		  return $len;

		$name = strtolower(trim($header[0]));
		if (!array_key_exists($name, $headers))
		  $headers[$name] = [trim($header[1])];
		else
		  $headers[$name][] = trim($header[1]);

		return $len;
	  }
	);
	//Execute the request.
	$body = curl_exec($ch);
	$info = curl_getinfo($ch);

	// echo '<pre>';
	// if($info === FALSE)
	// {
	   // echo 'Curl Failed: ' . curl_error($ch);
	// }
	// var_dump($info);
	// var_dump($body);
	// print_r($headers);

	//Close the cURL handle.
	curl_close($ch);
	$message_response_nthsmsid = $headers['x-nth-smsid'][0];
	$message_count = substr_count($message_response_nthsmsid , ';') + 1;

	$res_codes_matches = [];
	$regex_res_code = '/(?s)(?<=Result_code: )[0-9]{2}(?=,)/mi';
	preg_match_all($regex_res_code, $body, $res_codes_matches, PREG_PATTERN_ORDER, 0);
	$res_codes = $res_codes_matches[0];
	$message_response_statuscode = $res_codes[0];

	$db_message_update_query = $db->prepare("
					UPDATE idk_sms_messages
					SET sms_status = 1, sms_response_nthsmsid = :sms_response_nthsmsid, sms_response_statuscode = :sms_response_statuscode, sms_updated_at = :sms_updated_at, sms_count = :sms_count
					WHERE sms_id = :id
					");

	$db_message_update_query->execute(array(
					':id' => $message_id,
					':sms_response_nthsmsid' => $message_response_nthsmsid,
					':sms_response_statuscode' => $message_response_statuscode,
					':sms_count' => $message_count,
					':sms_updated_at' => date('Y-m-d H:i:s')
					));
}

function sendSmsToCandidate2($sifra, $phone_f, $kandidat_prijava_na){
	Global $db;

	$secret_token = 'aebf56deb2ef2ff5ccfd8f48456f8bb4';
	$received_message_origin = "IDK_STUDIO";
	$received_message_content = "Aplikaciju preuzmite na sljedecem linku : ".getSiteUrlr()."download ";
	$received_message_number = $phone_f;

	$message_content_formatted = str_replace("@","(at)", $received_message_content);
	$message_content_formatted = str_replace("đ","dj", $message_content_formatted);
	$message_content_formatted = str_replace("Đ","DJ", $message_content_formatted);
	$message_content_formatted = rawurlencode(iconv("UTF-8", "ASCII//TRANSLIT", $message_content_formatted) );

	// INSERT INTO DB THE RETREIVED SMS
	$db_message_query = $db->prepare("
					INSERT INTO idk_sms_messages
						(sms_origin, sms_number, sms_content, sms_status, sms_created_at, sms_type)
					VALUES
						(:sms_origin, :sms_number, :sms_content, :sms_status, :sms_created_at, :sms_type)");

	$db_message_query->execute(array(
					':sms_origin' => $received_message_origin,
					':sms_number' => $received_message_number,
					':sms_content' => $message_content_formatted,
					':sms_status' => 0,
					':sms_type' => 1,
					':sms_created_at' => date('Y-m-d H:i:s')
					));
	$message_id = $db->lastInsertId();

	/*
	 * NTH SMS Gateway INFO
	 */
	$nth_gateway_endpoint = 'http://bulk.mobile-gw.com';
	$nth_gateway_endpoint_port = '9000';
	$nth_customer_username = 'IDK_Acc';
	$nth_customer_password = 'GadOSYY2';

	// Additional gateway options

	$nth_gateway_message_allow_adaption = 1;

	/*
	 * Building URL
	 */
	$gateway_url = $nth_gateway_endpoint . ':' . $nth_gateway_endpoint_port . '/?username=' . $nth_customer_username . '&password=' . $nth_customer_password;
	// Adding options
	$gateway_url .= '&allow_adaption=' . $nth_gateway_message_allow_adaption;
	// Adding notification info
	$gateway_url .= '&status_report=' . 7;
	$gateway_url .= '&status_url=' . urlencode(getSiteUrlr().'get_sms_notification.php');
	// Adding SMS message info
	$gateway_url .= '&origin=' . $received_message_origin;
	$gateway_url .= '&call-number=' . $received_message_number;
	$gateway_url .= '&text=' . $message_content_formatted;
	$gateway_url .= '&messageid=' . $message_id;

	$headers = [];
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $gateway_url);
	//Set CURLOPT_RETURNTRANSFER so that the content is returned as a variable.
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	//Set CURLOPT_FOLLOWLOCATION to true to follow redirects.
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	//Get headers
	curl_setopt($ch, CURLOPT_HEADERFUNCTION,
	  function($curl, $header) use (&$headers)
	  {
		$len = strlen($header);
		$header = explode(':', $header, 2);
		if (count($header) < 2) // ignore invalid headers
		  return $len;

		$name = strtolower(trim($header[0]));
		if (!array_key_exists($name, $headers))
		  $headers[$name] = [trim($header[1])];
		else
		  $headers[$name][] = trim($header[1]);

		return $len;
	  }
	);
	//Execute the request.
	$body = curl_exec($ch);
	$info = curl_getinfo($ch);

	// echo '<pre>';
	// if($info === FALSE)
	// {
	   // echo 'Curl Failed: ' . curl_error($ch);
	// }
	// var_dump($info);
	// var_dump($body);
	// print_r($headers);

	//Close the cURL handle.
	curl_close($ch);
	$message_response_nthsmsid = $headers['x-nth-smsid'][0];
	$message_count = substr_count($message_response_nthsmsid , ';') + 1;

	$res_codes_matches = [];
	$regex_res_code = '/(?s)(?<=Result_code: )[0-9]{2}(?=,)/mi';
	preg_match_all($regex_res_code, $body, $res_codes_matches, PREG_PATTERN_ORDER, 0);
	$res_codes = $res_codes_matches[0];
	$message_response_statuscode = $res_codes[0];

	$db_message_update_query = $db->prepare("
					UPDATE idk_sms_messages
					SET sms_status = 1, sms_response_nthsmsid = :sms_response_nthsmsid, sms_response_statuscode = :sms_response_statuscode, sms_updated_at = :sms_updated_at, sms_count = :sms_count
					WHERE sms_id = :id
					");

	$db_message_update_query->execute(array(
					':id' => $message_id,
					':sms_response_nthsmsid' => $message_response_nthsmsid,
					':sms_response_statuscode' => $message_response_statuscode,
					':sms_count' => $message_count,
					':sms_updated_at' => date('Y-m-d H:i:s')
					));
}

function sendSmsToCandidate3($sifra, $phone_f, $kandidat_email){
	Global $db;

	$secret_token = 'aebf56deb2ef2ff5ccfd8f48456f8bb4';
	$received_message_origin = "IDK_STUDIO";
	$received_message_content = "Koristite Vasu E- mail adresu: ".$kandidat_email." i PIN: ".$sifra." za prijavu u Jobstep Messenger. ";
	$received_message_number = $phone_f;

	//$message_content_formatted = str_replace("@","(at)", $received_message_content);
	$message_content_formatted = str_replace("đ","dj", $received_message_content);
	$message_content_formatted = str_replace("Đ","DJ", $message_content_formatted);
	$message_content_formatted = rawurlencode(iconv("UTF-8", "ASCII//TRANSLIT", $message_content_formatted) );

	// INSERT INTO DB THE RETREIVED SMS
	$db_message_query = $db->prepare("
					INSERT INTO idk_sms_messages
						(sms_origin, sms_number, sms_content, sms_status, sms_created_at, sms_type)
					VALUES
						(:sms_origin, :sms_number, :sms_content, :sms_status, :sms_created_at, :sms_type)");

	$db_message_query->execute(array(
					':sms_origin' => $received_message_origin,
					':sms_number' => $received_message_number,
					':sms_content' => $message_content_formatted,
					':sms_status' => 0,
					':sms_type' => 1,
					':sms_created_at' => date('Y-m-d H:i:s')
					));
	$message_id = $db->lastInsertId();

	/*
	 * NTH SMS Gateway INFO
	 */
	$nth_gateway_endpoint = 'http://bulk.mobile-gw.com';
	$nth_gateway_endpoint_port = '9000';
	$nth_customer_username = 'IDK_Acc';
	$nth_customer_password = 'GadOSYY2';

	// Additional gateway options

	$nth_gateway_message_allow_adaption = 1;

	/*
	 * Building URL
	 */
	$gateway_url = $nth_gateway_endpoint . ':' . $nth_gateway_endpoint_port . '/?username=' . $nth_customer_username . '&password=' . $nth_customer_password;
	// Adding options
	$gateway_url .= '&allow_adaption=' . $nth_gateway_message_allow_adaption;
	// Adding notification info
	$gateway_url .= '&status_report=' . 7;
	$gateway_url .= '&status_url=' . urlencode(getSiteUrlr().'get_sms_notification.php');
	// Adding SMS message info
	$gateway_url .= '&origin=' . $received_message_origin;
	$gateway_url .= '&call-number=' . $received_message_number;
	$gateway_url .= '&text=' . $message_content_formatted;
	$gateway_url .= '&messageid=' . $message_id;

	$headers = [];
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $gateway_url);
	//Set CURLOPT_RETURNTRANSFER so that the content is returned as a variable.
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	//Set CURLOPT_FOLLOWLOCATION to true to follow redirects.
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	//Get headers
	curl_setopt($ch, CURLOPT_HEADERFUNCTION,
	  function($curl, $header) use (&$headers)
	  {
		$len = strlen($header);
		$header = explode(':', $header, 2);
		if (count($header) < 2) // ignore invalid headers
		  return $len;

		$name = strtolower(trim($header[0]));
		if (!array_key_exists($name, $headers))
		  $headers[$name] = [trim($header[1])];
		else
		  $headers[$name][] = trim($header[1]);

		return $len;
	  }
	);
	//Execute the request.
	$body = curl_exec($ch);
	$info = curl_getinfo($ch);

	// echo '<pre>';
	// if($info === FALSE)
	// {
	   // echo 'Curl Failed: ' . curl_error($ch);
	// }
	// var_dump($info);
	// var_dump($body);
	// print_r($headers);

	//Close the cURL handle.
	curl_close($ch);
	$message_response_nthsmsid = $headers['x-nth-smsid'][0];
	$message_count = substr_count($message_response_nthsmsid , ';') + 1;

	$res_codes_matches = [];
	$regex_res_code = '/(?s)(?<=Result_code: )[0-9]{2}(?=,)/mi';
	preg_match_all($regex_res_code, $body, $res_codes_matches, PREG_PATTERN_ORDER, 0);
	$res_codes = $res_codes_matches[0];
	$message_response_statuscode = $res_codes[0];

	$db_message_update_query = $db->prepare("
					UPDATE idk_sms_messages
					SET sms_status = 1, sms_response_nthsmsid = :sms_response_nthsmsid, sms_response_statuscode = :sms_response_statuscode, sms_updated_at = :sms_updated_at, sms_count = :sms_count
					WHERE sms_id = :id
					");

	$db_message_update_query->execute(array(
					':id' => $message_id,
					':sms_response_nthsmsid' => $message_response_nthsmsid,
					':sms_response_statuscode' => $message_response_statuscode,
					':sms_count' => $message_count,
					':sms_updated_at' => date('Y-m-d H:i:s')
					));
}
function sendSmsToCandidate4($sifra, $phone_f, $kandidat_email){
	Global $db;

	$secret_token = 'aebf56deb2ef2ff5ccfd8f48456f8bb4';
	$received_message_origin = "IDK_STUDIO";
	$received_message_content = "U slucaju dodatnih informacija oko preuzimanja i popunjavanja trazenih podataka, upute pogledajte na videu koji se nalazi na linku: https://bit.ly/3V177tF ";
	$received_message_number = $phone_f;

	//$message_content_formatted = str_replace("@","(at)", $received_message_content);
	$message_content_formatted = str_replace("đ","dj", $received_message_content);
	$message_content_formatted = str_replace("Đ","DJ", $message_content_formatted);
	$message_content_formatted = rawurlencode(iconv("UTF-8", "ASCII//TRANSLIT", $message_content_formatted) );

	// INSERT INTO DB THE RETREIVED SMS
	$db_message_query = $db->prepare("
					INSERT INTO idk_sms_messages
						(sms_origin, sms_number, sms_content, sms_status, sms_created_at, sms_type)
					VALUES
						(:sms_origin, :sms_number, :sms_content, :sms_status, :sms_created_at, :sms_type)");

	$db_message_query->execute(array(
					':sms_origin' => $received_message_origin,
					':sms_number' => $received_message_number,
					':sms_content' => $message_content_formatted,
					':sms_status' => 0,
					':sms_type' => 1,
					':sms_created_at' => date('Y-m-d H:i:s')
					));
	$message_id = $db->lastInsertId();

	/*
	 * NTH SMS Gateway INFO
	 */
	$nth_gateway_endpoint = 'http://bulk.mobile-gw.com';
	$nth_gateway_endpoint_port = '9000';
	$nth_customer_username = 'IDK_Acc';
	$nth_customer_password = 'GadOSYY2';

	// Additional gateway options

	$nth_gateway_message_allow_adaption = 1;

	/*
	 * Building URL
	 */
	$gateway_url = $nth_gateway_endpoint . ':' . $nth_gateway_endpoint_port . '/?username=' . $nth_customer_username . '&password=' . $nth_customer_password;
	// Adding options
	$gateway_url .= '&allow_adaption=' . $nth_gateway_message_allow_adaption;
	// Adding notification info
	$gateway_url .= '&status_report=' . 7;
	$gateway_url .= '&status_url=' . urlencode(getSiteUrlr().'get_sms_notification.php');
	// Adding SMS message info
	$gateway_url .= '&origin=' . $received_message_origin;
	$gateway_url .= '&call-number=' . $received_message_number;
	$gateway_url .= '&text=' . $message_content_formatted;
	$gateway_url .= '&messageid=' . $message_id;

	$headers = [];
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $gateway_url);
	//Set CURLOPT_RETURNTRANSFER so that the content is returned as a variable.
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	//Set CURLOPT_FOLLOWLOCATION to true to follow redirects.
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	//Get headers
	curl_setopt($ch, CURLOPT_HEADERFUNCTION,
	  function($curl, $header) use (&$headers)
	  {
		$len = strlen($header);
		$header = explode(':', $header, 2);
		if (count($header) < 2) // ignore invalid headers
		  return $len;

		$name = strtolower(trim($header[0]));
		if (!array_key_exists($name, $headers))
		  $headers[$name] = [trim($header[1])];
		else
		  $headers[$name][] = trim($header[1]);

		return $len;
	  }
	);
	//Execute the request.
	$body = curl_exec($ch);
	$info = curl_getinfo($ch);

	// echo '<pre>';
	// if($info === FALSE)
	// {
	   // echo 'Curl Failed: ' . curl_error($ch);
	// }
	// var_dump($info);
	// var_dump($body);
	// print_r($headers);

	//Close the cURL handle.
	curl_close($ch);
	$message_response_nthsmsid = $headers['x-nth-smsid'][0];
	$message_count = substr_count($message_response_nthsmsid , ';') + 1;

	$res_codes_matches = [];
	$regex_res_code = '/(?s)(?<=Result_code: )[0-9]{2}(?=,)/mi';
	preg_match_all($regex_res_code, $body, $res_codes_matches, PREG_PATTERN_ORDER, 0);
	$res_codes = $res_codes_matches[0];
	$message_response_statuscode = $res_codes[0];

	$db_message_update_query = $db->prepare("
					UPDATE idk_sms_messages
					SET sms_status = 1, sms_response_nthsmsid = :sms_response_nthsmsid, sms_response_statuscode = :sms_response_statuscode, sms_updated_at = :sms_updated_at, sms_count = :sms_count
					WHERE sms_id = :id
					");

	$db_message_update_query->execute(array(
					':id' => $message_id,
					':sms_response_nthsmsid' => $message_response_nthsmsid,
					':sms_response_statuscode' => $message_response_statuscode,
					':sms_count' => $message_count,
					':sms_updated_at' => date('Y-m-d H:i:s')
					));
}
//SMS ZA MESSENGER - INFOBIP
function sendSmsToCandidateInfobip1($sifra, $phone_f){

	Global $db;
	$sender = "JOBSTEP";
	$text = "JOBSTEP Agencija Vam se zahvaljuje na registraciji. U narednoj SMS poruci cete dobiti link putem kojeg mozete preuzeti nasu aplikaciju.";
	
	$broj = str_replace("+","",$phone_f);

	$active_provider = getActiveProviderForSendingMessages(4);
	if ($active_provider == 1) {
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://ej8w3r.api.infobip.com/sms/1/text/single",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => "{ \"from\":\"".$sender."\", \"to\":\"".$broj."\", \"text\":\"".$text."\" }",
			CURLOPT_HTTPHEADER => array(
			"accept: application/json",
			"authorization: Basic ZWJlbmRlcjE6RUJlbmRlcjY0MzI=",
			"content-type: application/json"
			),
		));
		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
	} else {
		$phoneNumber = checkPhoneNumberForNTH($broj);
		$params = array(
			"channels" => array(
				"SMS"
			),
			"destinations" => array(
				array(
					"phoneNumber" => $phoneNumber
				)
			),
			"sms" => array(
				"sender" => "Jobstep Int",
				"text" => $text
			)
		);
		$params_encode = json_encode($params);
		$response = sendMessageViaNTH($params_encode);
	}
}
//FJE ZA SLANJE VIBER PORUKA NAKON PRIJAVE
function viberPrijava1($broj, $text_sms, $text_viber){
	$active_provider = getActiveProviderForSendingMessages(4);
	if ($active_provider == 1) {
		$curl = curl_init();

		curl_setopt_array($curl, array(
		CURLOPT_URL => "https://ej8w3r.api.infobip.com/omni/1/advanced",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_POSTFIELDS => "{ \"scenarioKey\":\"7A32331B2103607D2F890C04FEB34942\", \"destinations\":[ { \"to\":{ \"phoneNumber\":\"".$broj."\" } } ], \"sms\":{ \"text\":\"".$text_sms."\" }, \"viber\":{ \"text\":\"".$text_viber."\" } }",
		CURLOPT_HTTPHEADER => array(
			"accept: application/json",
			"authorization: Basic ZWJlbmRlcjE6RUJlbmRlcjY0MzI=",
			"content-type: application/json"
		),
		));
		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		//echo "cURL Error #:" . $err;
		} else {
		//echo $response;
		}
		//echo "<br>".$broj."<br>";
	} else {
		$phoneNumber = checkPhoneNumberForNTH($broj);
		$params = '
			{
				"channels": [
					"VIBER",
					"SMS"
				],
				"destinations": [
					{
						"phoneNumber": "'.$phoneNumber.'"
					}
				],
				"viber": {
					"priority": 1,
					"sender": "Jobstep Int",
					"text": "'.$text_viber.'",
					"ttl": 14440,
					"label": "promotion"
				},
				"sms": {
					"priority": 2,
					"sender": "Jobstep Int",
					"text": "'.$text_sms.'"
				}
			}
		';
		$response = sendMessageViaNTH($params);
	}
}
function viberPrijava2($broj, $text_sms, $text_viber, $btn_text, $link){
	$active_provider = getActiveProviderForSendingMessages(4);
	if ($active_provider == 1) {
		$curl = curl_init();

		curl_setopt_array($curl, array(
		CURLOPT_URL => "https://ej8w3r.api.infobip.com/omni/1/advanced",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_POSTFIELDS => "{ \"scenarioKey\":\"7A32331B2103607D2F890C04FEB34942\", \"destinations\":[ { \"to\":{ \"phoneNumber\":\"".$broj."\" } } ], \"sms\":{ \"text\":\"".$text_sms."\" }, \"viber\":{ \"text\":\"".$text_viber."\", \"buttonText\":\"".$btn_text."\", \"buttonURL\":\"".$link."\" } }",
		CURLOPT_HTTPHEADER => array(
			"accept: application/json",
			"authorization: Basic ZWJlbmRlcjE6RUJlbmRlcjY0MzI=",
			"content-type: application/json"
		),
		));
		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		//echo "cURL Error #:" . $err;
		} else {
		// echo $response;
		}
		//echo "<br>".$broj."<br>";
	} else {
		$phoneNumber = checkPhoneNumberForNTH($broj);
		$params = '
			{
				"channels": [
					"VIBER",
					"SMS"
				],
				"destinations": [
					{
						"phoneNumber": "'.$phoneNumber.'"
					}
				],
				"viber": {
					"priority": 1,
					"sender": "Jobstep Int",
					"text": "'.$text_viber.'",
					"buttonCaption": "'.$btn_text.'",
					"buttonAction": "'.$link.'",
					"ttl": 14440,
					"label": "promotion"
				},
				"sms": {
					"priority": 2,
					"sender": "Jobstep Int",
					"text": "'.$text_sms.'"
				}
			}
		';
		$response = sendMessageViaNTH($params);
	}
}

function sendSmsToCandidateInfobip2($sifra, $phone_f){

	Global $db;
	$sender = "JOBSTEP";
	// $broj = "38761938892";
	$text = "Aplikaciju preuzmite na sljedecem linku : ".getSiteUrlr()."download ";
	//$text = "JOBSTEP Agencija Vam se zahvaljuje na registraciji za posao ".$kandidat_prijava_na.". Da bi ste finalizirali Vasu prijavu preuzmite nasu aplikaciju sa Google Play-a: https://bit.ly/33NvCi0 ili App Store-a https://apple.co/32Q14eh  . Pin: ".$sifra." .";

	$broj = str_replace("+","",$phone_f);
	$active_provider = getActiveProviderForSendingMessages(4);
	if ($active_provider == 1) {
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://ej8w3r.api.infobip.com/sms/1/text/single",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => "{ \"from\":\"".$sender."\", \"to\":\"".$broj."\", \"text\":\"".$text."\" }",
			CURLOPT_HTTPHEADER => array(
			"accept: application/json",
			"authorization: Basic ZWJlbmRlcjE6RUJlbmRlcjY0MzI=",
			"content-type: application/json"
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		// if ($err) {
			// echo "cURL Error #:" . $err;
		// } else {
			// echo $response;
		// }
	} else {
		$phoneNumber = checkPhoneNumberForNTH($broj);
		$params = array(
			"channels" => array(
				"SMS"
			),
			"destinations" => array(
				array(
					"phoneNumber" => $phoneNumber
				)
			),
			"sms" => array(
				"sender" => "Jobstep Int",
				"text" => $text
			)
		);
		$params_encode = json_encode($params);
		$response = sendMessageViaNTH($params_encode);
	}
}
function sendSmsToCandidateInfobip3($sifra, $phone_f, $kandidat_email){

	Global $db;
	$sender = "JOBSTEP";
	// $broj = "38761938892";
	$text = "Koristite Vase korisnicko ime: ".$kandidat_email." i PIN: ".$sifra." za prijavu u Jobstep Messenger. ";
	//$text = "JOBSTEP Agencija Vam se zahvaljuje na registraciji za posao ".$kandidat_prijava_na.". Da bi ste finalizirali Vasu prijavu preuzmite nasu aplikaciju sa Google Play-a: https://bit.ly/33NvCi0 ili App Store-a https://apple.co/32Q14eh  . Pin: ".$sifra." .";

	$broj = str_replace("+","",$phone_f);
	$active_provider = getActiveProviderForSendingMessages(4);
	if ($active_provider == 1) {
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://ej8w3r.api.infobip.com/sms/1/text/single",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => "{ \"from\":\"".$sender."\", \"to\":\"".$broj."\", \"text\":\"".$text."\" }",
			CURLOPT_HTTPHEADER => array(
			"accept: application/json",
			"authorization: Basic ZWJlbmRlcjE6RUJlbmRlcjY0MzI=",
			"content-type: application/json"
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		// if ($err) {
			// echo "cURL Error #:" . $err;
		// } else {
			// echo $response;
		// }
	} else {
		$phoneNumber = checkPhoneNumberForNTH($broj);
		$params = array(
			"channels" => array(
				"SMS"
			),
			"destinations" => array(
				array(
					"phoneNumber" => $phoneNumber
				)
			),
			"sms" => array(
				"sender" => "Jobstep Int",
				"text" => $text
			)
		);
		$params_encode = json_encode($params);
		$response = sendMessageViaNTH($params_encode);
	}
}

function sendSmsToCandidateInfobip4($sifra, $phone_f, $kandidat_email){

	Global $db;
	$sender = "JOBSTEP";
	// $broj = "38761938892";
	$text = "U slucaju dodatnih informacija oko preuzimanja i popunjavanja trazenih podataka, upute pogledajte na videu koji se nalazi na linku: https://bit.ly/3V177tF ";
	//$text = "JOBSTEP Agencija Vam se zahvaljuje na registraciji za posao ".$kandidat_prijava_na.". Da bi ste finalizirali Vasu prijavu preuzmite nasu aplikaciju sa Google Play-a: https://bit.ly/33NvCi0 ili App Store-a https://apple.co/32Q14eh  . Pin: ".$sifra." .";

	$broj = str_replace("+","",$phone_f);
	$active_provider = getActiveProviderForSendingMessages(4);
	if ($active_provider == 1) {
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://ej8w3r.api.infobip.com/sms/1/text/single",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => "{ \"from\":\"".$sender."\", \"to\":\"".$broj."\", \"text\":\"".$text."\" }",
			CURLOPT_HTTPHEADER => array(
			"accept: application/json",
			"authorization: Basic ZWJlbmRlcjE6RUJlbmRlcjY0MzI=",
			"content-type: application/json"
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		// if ($err) {
			// echo "cURL Error #:" . $err;
		// } else {
			// echo $response;
		// }
	} else {
		$phoneNumber = checkPhoneNumberForNTH($broj);
		$params = array(
			"channels" => array(
				"SMS"
			),
			"destinations" => array(
				array(
					"phoneNumber" => $phoneNumber
				)
			),
			"sms" => array(
				"sender" => "Jobstep Int",
				"text" => $text
			)
		);
		$params_encode = json_encode($params);
		$response = sendMessageViaNTH($params_encode);
	}
}

//VIBER PORUKA ZA MESSENGER - PONOVO
function sendViberToCandidateAgain($broj){
	$text = "Dovrsite svoju prijavu. Preuzmite aplikaciju sa Play Store: https://bit.ly/33NvCi0 ili App Store: https://apple.co/32Q14eh .";
	$active_provider = getActiveProviderForSendingMessages(5);
	if ($active_provider == 1) {
		$curl = curl_init();

		curl_setopt_array($curl, array(
		CURLOPT_URL => "https://ej8w3r.api.infobip.com/omni/1/advanced",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_POSTFIELDS => "{ \"scenarioKey\":\"7A32331B2103607D2F890C04FEB34942\", \"destinations\":[ { \"to\":{ \"phoneNumber\":\"".$broj."\" } } ], \"sms\":{ \"text\":\"".$text."\" }, \"viber\":{ \"text\":\"".$text."\" } }",
		CURLOPT_HTTPHEADER => array(
			"accept: application/json",
			"authorization: Basic ZWJlbmRlcjE6RUJlbmRlcjY0MzI=",
			"content-type: application/json"
		),
		));
		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		echo "cURL Error #:" . $err;
		} else {
		echo $response;
		}
	} else {
		$phoneNumber = checkPhoneNumberForNTH($broj);
		$params = array(
			"channels" => array(
				"VIBER",
				"SMS"
			),
			"destinations" => array(
				array(
					"phoneNumber" => $phoneNumber
				)
			),
			"viber" => array(
				"priority" => 1,
				"sender" => "Jobstep Int",
				"text" => $text,
				"ttl" => 14440,
				"label" => "promotion"
			),
			"sms" => array(
				"priority" => 2,
				"sender" => "Jobstep Int",
				"text" => $text
			)
		);
		$params_encode = json_encode($params);
		$response = sendMessageViaNTH($params_encode);
	}
	echo "<br>".$broj."<br>";
}

//SMS ZA MESSENGER PONOVO - INFOBIP
function sendSmsToCandidateAgain($phone_f, $kandidat_prijava_na){

	Global $db;
	$sender = "JOBSTEP";
	// $broj = "38761938892";
	$text = "Dovrsite svoju prijavu. Preuzmite aplikaciju sa Play Store: https://bit.ly/33NvCi0 ili App Store: https://apple.co/32Q14eh .";

	$broj = str_replace("+","",$phone_f);
	$curl = curl_init();

	curl_setopt_array($curl, array(
		CURLOPT_URL => "https://ej8w3r.api.infobip.com/sms/1/text/single",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_POSTFIELDS => "{ \"from\":\"".$sender."\", \"to\":\"".$broj."\", \"text\":\"".$text."\" }",
		CURLOPT_HTTPHEADER => array(
		"accept: application/json",
		"authorization: Basic ZWJlbmRlcjE6RUJlbmRlcjY0MzI=",
		"content-type: application/json"
		),
	));

	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);

	// if ($err) {
		// echo "cURL Error #:" . $err;
	// } else {
		// echo $response;
	// }
}

//SMS ZA MESSENGER PONOVO - NTH
function sendSmsToCandidateAgainNTH($phone_f, $kandidat_prijava_na){
	Global $db;

	$secret_token = 'aebf56deb2ef2ff5ccfd8f48456f8bb4';
	$received_message_origin = "IDK_STUDIO";
	$received_message_content = "Dovrsite svoju prijavu za posao ".$kandidat_prijava_na.". Preuzmite aplikaciju sa Googl Play: https://bit.ly/33NvCi0 ili App Store: https://apple.co/32Q14eh .";
	$phone_f = str_replace("+", '00', $phone_f);
	$received_message_number = $phone_f;

	$message_content_formatted = str_replace("@","(at)", $received_message_content);
	$message_content_formatted = str_replace("đ","dj", $message_content_formatted);
	$message_content_formatted = str_replace("Đ","DJ", $message_content_formatted);
	$message_content_formatted = rawurlencode(iconv("UTF-8", "ASCII//TRANSLIT", $message_content_formatted) );

	// INSERT INTO DB THE RETREIVED SMS
	$db_message_query = $db->prepare("
					INSERT INTO idk_sms_messages
						(sms_origin, sms_number, sms_content, sms_status, sms_created_at, sms_type)
					VALUES
						(:sms_origin, :sms_number, :sms_content, :sms_status, :sms_created_at, :sms_type)");

	$db_message_query->execute(array(
					':sms_origin' => $received_message_origin,
					':sms_number' => $received_message_number,
					':sms_content' => $message_content_formatted,
					':sms_status' => 0,
					':sms_type' => 1,
					':sms_created_at' => date('Y-m-d H:i:s')
					));
	$message_id = $db->lastInsertId();

	/*
	 * NTH SMS Gateway INFO
	 */
	$nth_gateway_endpoint = 'http://bulk.mobile-gw.com';
	$nth_gateway_endpoint_port = '9000';
	$nth_customer_username = 'IDK_Acc';
	$nth_customer_password = 'GadOSYY2';

	// Additional gateway options

	$nth_gateway_message_allow_adaption = 1;

	/*
	 * Building URL
	 */
	$gateway_url = $nth_gateway_endpoint . ':' . $nth_gateway_endpoint_port . '/?username=' . $nth_customer_username . '&password=' . $nth_customer_password;
	// Adding options
	$gateway_url .= '&allow_adaption=' . $nth_gateway_message_allow_adaption;
	// Adding notification info
	$gateway_url .= '&status_report=' . 7;
	$gateway_url .= '&status_url=' . urlencode(getSiteUrlr().'get_sms_notification.php');
	// Adding SMS message info
	$gateway_url .= '&origin=' . $received_message_origin;
	$gateway_url .= '&call-number=' . $received_message_number;
	$gateway_url .= '&text=' . $message_content_formatted;
	$gateway_url .= '&messageid=' . $message_id;

	$headers = [];
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $gateway_url);
	//Set CURLOPT_RETURNTRANSFER so that the content is returned as a variable.
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	//Set CURLOPT_FOLLOWLOCATION to true to follow redirects.
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	//Get headers
	curl_setopt($ch, CURLOPT_HEADERFUNCTION,
	  function($curl, $header) use (&$headers)
	  {
		$len = strlen($header);
		$header = explode(':', $header, 2);
		if (count($header) < 2) // ignore invalid headers
		  return $len;

		$name = strtolower(trim($header[0]));
		if (!array_key_exists($name, $headers))
		  $headers[$name] = [trim($header[1])];
		else
		  $headers[$name][] = trim($header[1]);

		return $len;
	  }
	);
	//Execute the request.
	$body = curl_exec($ch);
	$info = curl_getinfo($ch);

	// echo '<pre>';
	// if($info === FALSE)
	// {
	   // echo 'Curl Failed: ' . curl_error($ch);
	// }
	// var_dump($info);
	// var_dump($body);
	// print_r($headers);

	//Close the cURL handle.
	curl_close($ch);
	$message_response_nthsmsid = $headers['x-nth-smsid'][0];
	$message_count = substr_count($message_response_nthsmsid , ';') + 1;

	$res_codes_matches = [];
	$regex_res_code = '/(?s)(?<=Result_code: )[0-9]{2}(?=,)/mi';
	preg_match_all($regex_res_code, $body, $res_codes_matches, PREG_PATTERN_ORDER, 0);
	$res_codes = $res_codes_matches[0];
	$message_response_statuscode = $res_codes[0];

	$db_message_update_query = $db->prepare("
					UPDATE idk_sms_messages
					SET sms_status = 1, sms_response_nthsmsid = :sms_response_nthsmsid, sms_response_statuscode = :sms_response_statuscode, sms_updated_at = :sms_updated_at, sms_count = :sms_count
					WHERE sms_id = :id
					");

	$db_message_update_query->execute(array(
					':id' => $message_id,
					':sms_response_nthsmsid' => $message_response_nthsmsid,
					':sms_response_statuscode' => $message_response_statuscode,
					':sms_count' => $message_count,
					':sms_updated_at' => date('Y-m-d H:i:s')
					));
}

//SMS ZA MESSENGER -STARIM KANDIDATIMA INFOBIP
function sendSMSStariKandidatiINFOBIP($kandidat_id, $phone_f, $link, $kandidat_full_name){
	$real_response = array();

	Global $db;
	$sender = "JOBSTEP";
	// $broj = "38761938892";
	//$text = "Zainteresirani ste za Jobstepove nove poslovne ponude?  Prijavite se putem sljedeceg linka za posao u Vasoj struci: ".$link." .";
	$text = "Zdravo ".$kandidat_full_name.", poslodavci iz Njemacke dolaze u Sarajevo i Banja Luku! Razgovori za posao INTERNET MONTAZERA od 16.03.2020 do 24.03.2020 u Sarajevu i Banja Luci. Prijavu za razgovor i detalje oglasa potrazite na sljedecem linku: ".$link;
	//$text = "JOBSTEP Agencija Vam se zahvaljuje na registraciji za posao ".$kandidat_prijava_na.". Da bi ste finalizirali Vasu prijavu preuzmite nasu aplikaciju sa Google Play-a: https://bit.ly/33NvCi0 ili App Store-a https://apple.co/32Q14eh  . Pin: ".$sifra." .";

	$broj = str_replace("+","",$phone_f);
	$curl = curl_init();

	curl_setopt_array($curl, array(
		CURLOPT_URL => "https://ej8w3r.api.infobip.com/sms/1/text/single",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_POSTFIELDS => "{ \"from\":\"".$sender."\", \"to\":\"".$broj."\", \"text\":\"".$text."\" }",
		CURLOPT_HTTPHEADER => array(
		"accept: application/json",
		"authorization: Basic ZWJlbmRlcjE6RUJlbmRlcjY0MzI=",
		"content-type: application/json"
		),
	));

	$response = curl_exec($curl);
	echo $err = curl_error($curl);

	curl_close($curl);

	$xmldata = json_decode($response);
	// echo '<pre>';
		// print_r(json_decode($response));
	// echo '</pre>';

	foreach($xmldata->messages as $messages){
		$kandidat_sms_id = $messages->messageId;
		$kandidat_status_sent = $messages->status->groupName;
	}


	$update_kandidati = $db->prepare("
					UPDATE idk_kandidati
					SET kandidat_poslan_sms = :kandidat_poslan_sms, kandidat_sms_id = :kandidat_sms_id
					WHERE kandidat_id = :kandidat_id
					");

	$update_kandidati->execute(array(
					':kandidat_id' => $kandidat_id,
					':kandidat_sms_id' => $kandidat_sms_id,
					':kandidat_poslan_sms' => $kandidat_status_sent
					));

	// if ($err) {
		// echo "cURL Error #:" . $err;
	// } else {
		// echo $response;
	// }
}

//SMS ZA DIPL - INFOBIP
function sendSMSdiplINFOBIP($kandidat_id, $phone_f, $link, $kandidat_full_name){
	$real_response = array();

	Global $db;
	$sender = "JOBSTEP";
	$text = "Kako dobiti termin u Njemackoj ambasadi za 10 dana? (za vise informacija otvori link) ".$link ;

	$broj = str_replace("+","",$phone_f);
	$curl = curl_init();

	curl_setopt_array($curl, array(
		CURLOPT_URL => "https://ej8w3r.api.infobip.com/sms/1/text/single",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_POSTFIELDS => "{ \"from\":\"".$sender."\", \"to\":\"".$broj."\", \"text\":\"".$text."\" }",
		CURLOPT_HTTPHEADER => array(
		"accept: application/json",
		"authorization: Basic ZWJlbmRlcjE6RUJlbmRlcjY0MzI=",
		"content-type: application/json"
		),
	));

	$response = curl_exec($curl);
	echo $err = curl_error($curl);

	curl_close($curl);

	$xmldata = json_decode($response);
	// echo '<pre>';
		// print_r(json_decode($response));
	// echo '</pre>';

	foreach($xmldata->messages as $messages){
		$kandidat_sms_id = $messages->messageId;
		$kandidat_status_sent = $messages->status->groupName;
	}
	// var_dump($kandidat_sms_id);
	// var_dump($kandidat_status_sent);

	$update_kandidati = $db->prepare("
					UPDATE idk_kandidati
					SET kandidat_poslan_dipl = :kandidat_poslan_dipl
					WHERE kandidat_id = :kandidat_id
					");

	$update_kandidati->execute(array(
					':kandidat_id' => $kandidat_id,
					':kandidat_poslan_dipl' => 2
					));

	// if ($err) {
		// echo "cURL Error #:" . $err;
	// } else {
		// echo $response;
	// }
}

//VIBER ZA DIPL - INFOBIP
function sendVIBERdiplINFOBIP($kandidat_id, $kandidat_mobitel, $link, $kandidat_full_name){
	$broj = str_replace("+","",$kandidat_mobitel);
	Global $db;
	$text_sms = 'Uz nostrificiranu diplomu mozete brzo do termina! Saznaj vise na linku: '.$link ;
	$text_viber = 'Uz nostrificiranu diplomu možete brzo do termina!' ;
	$curl = curl_init();
	
	curl_setopt_array($curl, array(
	  CURLOPT_URL => "https://ej8w3r.api.infobip.com/omni/1/advanced",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 30,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_POSTFIELDS => "{ \"scenarioKey\":\"7A32331B2103607D2F890C04FEB34942\", \"destinations\":[ { \"to\":{ \"phoneNumber\":\"".$broj."\" } } ], \"sms\":{ \"text\":\"".$text_sms."\" }, \"viber\":{ \"text\":\"".$text_viber."\", \"buttonText\":\"SAZNAJ VIŠE\", \"buttonURL\":\"".$link."\", \"imageURL\":\"https://crm.job-step.com/images/dipl_viber.png\", \"isPromotional\":\"true\" } }",
	  CURLOPT_HTTPHEADER => array(
		"accept: application/json",
		"authorization: Basic ZWJlbmRlcjE6RUJlbmRlcjY0MzI=",
		"content-type: application/json"
	  ),
	));
	
	$update_kandidati = $db->prepare("
					UPDATE idk_kandidati
					SET kandidat_poslan_dipl = :kandidat_poslan_dipl
					WHERE kandidat_id = :kandidat_id
					");

	$update_kandidati->execute(array(
					':kandidat_id' => $kandidat_id,
					':kandidat_poslan_dipl' => 5
					));
				
	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);

	if ($err) {
	  echo "cURL Error #:" . $err;
	} else {
	  echo $response;
	}
	echo "<br>".$broj."<br>";
}

//VIBER ZA DIPL SRBIJA - INFOBIP
function sendVIBERdiplINFOBIPsrbija($kandidat_id, $kandidat_mobitel, $link, $kandidat_full_name){
	$broj = str_replace("+","",$kandidat_mobitel);
	Global $db;
	$text_sms = 'Ambasada je stornirala sve termine za radnu vizu! Saznajte kako mozete otici po novom zakonu u najkracem roku: '.$link ;
	$text_viber = 'Ambasada je stornirala sve termine za radnu vizu!!\nŽelite li saznati kako možete otići po novom zakonu u najkraćem vremenskom periodu\n' ;
	$curl = curl_init();
	
	curl_setopt_array($curl, array(
	  CURLOPT_URL => "https://ej8w3r.api.infobip.com/omni/1/advanced",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 30,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_POSTFIELDS => "{ \"scenarioKey\":\"7A32331B2103607D2F890C04FEB34942\", \"destinations\":[ { \"to\":{ \"phoneNumber\":\"".$broj."\" } } ], \"sms\":{ \"text\":\"".$text_sms."\" }, \"viber\":{ \"text\":\"".$text_viber."\", \"buttonText\":\"SAZNAJ VIŠE\", \"buttonURL\":\"".$link."\", \"imageURL\":\"https://crm.job-step.com/images/stornirani_termini_rs.png\", \"isPromotional\":\"true\" } }",
	  CURLOPT_HTTPHEADER => array(
		"accept: application/json",
		"authorization: Basic ZWJlbmRlcjE6RUJlbmRlcjY0MzI=",
		"content-type: application/json"
	  ),
	));
	
	$update_kandidati = $db->prepare("
					UPDATE idk_kandidati
					SET kandidat_poslan_dipl = :kandidat_poslan_dipl
					WHERE kandidat_id = :kandidat_id
					");

	$update_kandidati->execute(array(
					':kandidat_id' => $kandidat_id,
					':kandidat_poslan_dipl' => 9
					));
				
	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);

	if ($err) {
	  echo "cURL Error #:" . $err;
	} else {
	  echo $response;
	}
	echo "<br>".$broj."<br>";
}

//FJA ZA SLANJE VIBER PORUKA - INFOBIP
function sendViberMessage($broj, $text_viber, $text_sms){
	$curl = curl_init();

	curl_setopt_array($curl, array(
	  CURLOPT_URL => "https://ej8w3r.api.infobip.com/omni/1/advanced",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 30,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_POSTFIELDS => "{ \"scenarioKey\":\"7A32331B2103607D2F890C04FEB34942\", \"destinations\":[ { \"to\":{ \"phoneNumber\":\"".$broj."\" } } ], \"sms\":{ \"text\":\"".$text_sms."\" }, \"viber\":{ \"text\":\"".$text_viber."\", \"imageURL\":\"https://crm.job-step.com/viberslika2.png\", \"buttonText\":\"Prijavi se\", \"buttonURL\":\"https://crm.job-step.com/registracija/446/korak1\", \"isPromotional\":\"true\" } }",
	  CURLOPT_HTTPHEADER => array(
		"accept: application/json",
		"authorization: Basic ZWJlbmRlcjE6RUJlbmRlcjY0MzI=",
		"content-type: application/json"
	  ),
	));
	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);

	if ($err) {
	  echo "cURL Error #:" . $err;
	} else {
	  echo $response;
	}
	echo "<br>".$broj."<br>";
}

//SMS ZA MESSENGER -STARIM KANDIDATIMA
function sendSMSStariKandidatiNTH($kandidat_id, $phone_f, $link){
	Global $db;

	$secret_token = 'aebf56deb2ef2ff5ccfd8f48456f8bb4';
	$received_message_origin = "IDK_STUDIO";
	$received_message_content = "Postovanje, zainteresirani ste za Jobstepove nove poslovne ponude?  Prijavite se putem sljedeceg linka za posao u Vasoj struci: ".$link." .";
	$received_message_number = $phone_f;

	$message_content_formatted = str_replace("@","(at)", $received_message_content);
	$message_content_formatted = str_replace("đ","dj", $message_content_formatted);
	$message_content_formatted = str_replace("Đ","DJ", $message_content_formatted);
	$message_content_formatted = rawurlencode(iconv("UTF-8", "ASCII//TRANSLIT", $message_content_formatted) );

	// INSERT INTO DB THE RETREIVED SMS
	$db_message_query = $db->prepare("
					INSERT INTO idk_sms_messages
						(sms_origin, sms_number, sms_content, sms_status, sms_created_at, sms_type)
					VALUES
						(:sms_origin, :sms_number, :sms_content, :sms_status, :sms_created_at, :sms_type)");

	$db_message_query->execute(array(
					':sms_origin' => $received_message_origin,
					':sms_number' => $received_message_number,
					':sms_content' => $message_content_formatted,
					':sms_status' => 0,
					':sms_type' => 2,
					':sms_created_at' => date('Y-m-d H:i:s')
					));
	$message_id = $db->lastInsertId();

	/*
	 * NTH SMS Gateway INFO
	 */
	$nth_gateway_endpoint = 'http://bulk.mobile-gw.com';
	$nth_gateway_endpoint_port = '9000';
	$nth_customer_username = 'IDK_Acc';
	$nth_customer_password = 'GadOSYY2';

	// Additional gateway options

	$nth_gateway_message_allow_adaption = 1;

	/*
	 * Building URL
	 */
	$gateway_url = $nth_gateway_endpoint . ':' . $nth_gateway_endpoint_port . '/?username=' . $nth_customer_username . '&password=' . $nth_customer_password;
	// Adding options
	$gateway_url .= '&allow_adaption=' . $nth_gateway_message_allow_adaption;
	// Adding notification info
	$gateway_url .= '&status_report=' . 7;
	$gateway_url .= '&status_url=' . urlencode(getSiteUrlr().'get_sms_notification.php');
	// Adding SMS message info
	$gateway_url .= '&origin=' . $received_message_origin;
	$gateway_url .= '&call-number=' . $received_message_number;
	$gateway_url .= '&text=' . $message_content_formatted;
	$gateway_url .= '&messageid=' . $message_id;

	$headers = [];
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $gateway_url);
	//Set CURLOPT_RETURNTRANSFER so that the content is returned as a variable.
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	//Set CURLOPT_FOLLOWLOCATION to true to follow redirects.
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	//Get headers
	curl_setopt($ch, CURLOPT_HEADERFUNCTION,
	  function($curl, $header) use (&$headers)
	  {
		$len = strlen($header);
		$header = explode(':', $header, 2);
		if (count($header) < 2) // ignore invalid headers
		  return $len;

		$name = strtolower(trim($header[0]));
		if (!array_key_exists($name, $headers))
		  $headers[$name] = [trim($header[1])];
		else
		  $headers[$name][] = trim($header[1]);

		return $len;
	  }
	);
	//Execute the request.
	$body = curl_exec($ch);
	$info = curl_getinfo($ch);

	// echo '<pre>';
	// if($info === FALSE)
	// {
	   // echo 'Curl Failed: ' . curl_error($ch);
	// }
	// var_dump($info);
	// var_dump($body);
	// print_r($headers);

	//Close the cURL handle.
	curl_close($ch);
	$message_response_nthsmsid = $headers['x-nth-smsid'][0];
	$message_count = substr_count($message_response_nthsmsid , ';') + 1;

	$res_codes_matches = [];
	$regex_res_code = '/(?s)(?<=Result_code: )[0-9]{2}(?=,)/mi';
	preg_match_all($regex_res_code, $body, $res_codes_matches, PREG_PATTERN_ORDER, 0);
	$res_codes = $res_codes_matches[0];
	$message_response_statuscode = $res_codes[0];

	$db_message_update_query = $db->prepare("
					UPDATE idk_sms_messages
					SET sms_status = 1, sms_response_nthsmsid = :sms_response_nthsmsid, sms_response_statuscode = :sms_response_statuscode, sms_updated_at = :sms_updated_at, sms_count = :sms_count
					WHERE sms_id = :id
					");

	$db_message_update_query->execute(array(
					':id' => $message_id,
					':sms_response_nthsmsid' => $message_response_nthsmsid,
					':sms_response_statuscode' => $message_response_statuscode,
					':sms_count' => $message_count,
					':sms_updated_at' => date('Y-m-d H:i:s')
					));

	$update_kandidati = $db->prepare("
					UPDATE idk_kandidati
					SET kandidat_poslan_sms = :kandidat_poslan_sms
					WHERE kandidat_id = :kandidat_id
					");

	$update_kandidati->execute(array(
					':kandidat_id' => $kandidat_id,
					':kandidat_poslan_sms' => $message_response_statuscode
					));
}

//MAIL ZA MESSENGER AKO NE PRODJE SMS
function sendCandidateMessengerMail($random_string, $kandidat_email, $bot_koriscnicko_ime) {
	/*
	Global $db;
	$mail = new PHPMailer;

	$mail->isSMTP();											// Set mailer to use SMTP

	$mail->Host = 'smtp.gmail.com';                 // Specify main and backup SMTP servers
	$mail->SMTPAuth = true;                         // Enable SMTP authentication
	$mail->Username = 'support@job-step.com';        // SMTP username
	$mail->Password = 'eooc nnxo aylp lqkh';          // SMTP password
	$mail->SMTPSecure = 'ssl';                      // Enable TLS encryption, `ssl` also accepted
	$mail->Port = 465;                              // TCP port to connect to
	$mail->CharSet = 'UTF-8';

	//Recipients
	$mail->setFrom('support@job-step.com', 'JobStep');
	$mail->addAddress($kandidat_email, $kandidat_email);		// Add a recipient

	$mail->Subject ="Vaša prijava na konkurs ";
	$mail->Body    = "<p>Poštovani,</p>

<p>Ovim putem potvrđujemo da smo zaprimili Vašu prijavu. Klikom na link ispod možete preuzeti našu aplikaciju, finalizirati prijavu i pronaći pravi posao za Vas. </p>
<p><br><a href = 'https://play.google.com/store/apps/details?id=net.job_step.jobstepmessenger'>Google play</a></p>
<p><a href = 'https://apple.co/32Q14eh'>App store</a></p>
<p>Pristupni podaci za aplikaciju: <br>Korisnicko ime: $bot_koriscnicko_ime <br> Pin: $random_string</p>


<p>Srdačan pozdrav,</p>";
	$mail->AltBody = "ALT";

	if(!$mail->send()) {
		echo 'Message could not be sent. ';
		echo 'Mailer Error: ' . $mail->ErrorInfo;
	}else{
		// $updateSendMailStatus = $db->prepare("
						// UPDATE idk_kandidati
						// SET	kandidat_mailsent = :kandidat_mailsent
						// WHERE kandidat_id = :kandidat_id
						// ");

		// $updateSendMailStatus->execute(array(
					// ':kandidat_mailsent' => 1,
					// ':kandidat_id' => $kandidat_id
					// ));

	}
	*/
}

/*
	*******************************************************
	* JobStep Providers - sve funkcije za providere START *
	*******************************************************
	Sve funkcije su isključivo vezane za tabelu 
	'provider_for_sending_messages'
*/
	function getActiveProviderForSendingMessages($psm_group_type) {
		Global $db;
		$psm_group_type = intval($psm_group_type);
		$result = 0;

		$query = $db->prepare("
			SELECT 
				psm_active_provider
			FROM 
				idk_provider_for_sending_messages 
			WHERE 
				psm_group_type = :psm_group_type
		");
		$query->execute(array(
			':psm_group_type' => $psm_group_type
		));
		if($query->rowCount() == 1) {
			$row = $query->fetch();
			$result = intval($row["psm_active_provider"]);
		} 
		
		return $result;
	}

	function getNextProviderGroupTypeForSendingMessages() {
		Global $db;

		$query = $db->prepare("
			SELECT 
				(MAX(psm_group_type) + 1) AS max_group_type
			FROM 
				idk_provider_for_sending_messages 
		");
		$query->execute();
		$row = $query->fetch();

		return intval($row["max_group_type"]);
	}

	function getAllProviderGroupTypeForSendingMessages() {
		Global $db; 

		$query = $db->prepare("
			SELECT 
				psm_id, 
				psm_group_type, 
				psm_group_name, 
				psm_active_provider
			FROM 
				idk_provider_for_sending_messages 
			ORDER BY 
				psm_group_type 
			ASC
		");
		$query->execute();
		$rows = $query->fetchAll(PDO::FETCH_ASSOC);

		return $rows;
	}

	function insertGroupInProviderForSendingMessages($psm_group_name, $psm_active_provider) {
		Global $db; 
		$result = 0;
		$psm_group_type = getNextProviderGroupTypeForSendingMessages();

		$query = $db->prepare("
			INSERT INTO idk_provider_for_sending_messages 
			(
				psm_group_type, psm_group_name, psm_active_provider
			) 
			VALUES
			(
				:psm_group_type, :psm_group_name, :psm_active_provider
			)
		"); 
		$query->execute(array(
			':psm_group_type' 		=> $psm_group_type, 
			':psm_group_name' 		=> $psm_group_name, 
			':psm_active_provider' 	=> $psm_active_provider, 
		));

		if ($query->rowCount() == 1) {
			$last_id = $db->lastInsertId();
			$result = 1;
			$log_desc = "Provider For Sending Messages -> Izvršeno dodavanje nove grupe poruka sa ID = [".$last_id."]. Detalji unosa: {'psm_group_type' => '".$psm_group_type."', 'psm_group_name' => '".$psm_group_name."', 'psm_active_provider' => '".$psm_active_provider."'}"; 
			addToLogs($log_desc, 0);
		} 

		return $result;
	}

	function editActiveProviderForSendingMessages($psm_id, $psm_group_type, $psm_active_provider) {
		Global $db;
		$psm_active_provider_before = getActiveProviderForSendingMessages($psm_group_type);
		$result = 0;

		$query = $db->prepare("
			UPDATE 
				idk_provider_for_sending_messages 
			SET 
				psm_active_provider = :psm_active_provider
			WHERE 
				psm_id = :psm_id
		"); 
		$query->execute(array(
			':psm_active_provider' => $psm_active_provider,
			':psm_id' => $psm_id
		));

		if ($query->rowCount() == 1) {
			$result = 1;
			$log_desc = "Provider For Sending Messages -> Izvršeno editovanje aktivnog providera grupe poruka ID = [".$psm_id."] i TYPE = [".$psm_group_type."]. Detalji unosa: {'Old value' => '".$psm_active_provider_before."', 'New value' => '".$psm_active_provider."'}"; 
			addToLogs($log_desc, 0);
		}

		return $result;
	}
	
	function editActiveProviderForAllSendingMessages($psm_active_provider) {
		Global $db;
		$result = 0;

		$query = $db->prepare("
			UPDATE 
				idk_provider_for_sending_messages 
			SET 
				psm_active_provider = :psm_active_provider
		"); 
		$query->execute(array(
			':psm_active_provider' => $psm_active_provider
		));

		if ($query->rowCount() > 0) {
			$result = 1;
			$log_desc = "Provider For Sending Messages -> Izvršeno editovanje aktivnog providera za sve grupe na vrijednost $psm_active_provider."; 
			addToLogs($log_desc, 0);
		}

		return $result;
	}
/*
	*******************************************************
	*  JobStep Providers - sve funkcije za providere END  *
	*******************************************************
*/

/*
	**********************************************************
	* NTH API NEW - sve funkcije za NTH API 12.02.2024 START *
	**********************************************************
*/
	function sendMessageViaNTH($params) {
		/*
			Format varijable $params prikazat će se u dokumentima na linkovima: 
			https://app.clickup.com/24391024/v/dc/q8bbg-28548
		*/

		$curl = curl_init();
		curl_setopt_array($curl, 
			array(
				CURLOPT_URL => 'https://msg.mobile-gw.com:9000/v1/omni-channel/message',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS => $params,
				CURLOPT_HTTPHEADER => array(
					'Authorization: Basic am9ic3RlcDpSV0ZoIVN0TElNcTw=',
					'Content-Type: application/json',
					'Accept: application/json'
				),
			)
		);
		$response = curl_exec($curl);
		curl_close($curl);
		return $response;
	}

	function checkPhoneNumberForNTH($phone) {
		$phone = preg_replace('/\s+/', '', $phone);
		if (substr($phone, 0, 2) === "00") {
			$phone = substr($phone, 2);
		} else if (substr($phone, 0, 1) === "+") {
			$phone = substr($phone, 1);
		}
		return $phone;
	}
/*
	**********************************************************
	*  NTH API NEW - sve funkcije za NTH API 12.02.2024 END  *
	**********************************************************
*/


//GET CANDIDATE STATUS
function getCandidateStatus($kandidat_id){
	Global $db;
	$select_query = $db->prepare("
						SELECT kandidat_status
						FROM idk_kandidati
						WHERE kandidat_id = :kandidat_id");

	$select_query->execute(array(
						':kandidat_id' => $kandidat_id
	));

	$select_row = $select_query->fetch();

	return ($select_row['kandidat_status']);
}
//GET VALIDACIJE
function getValidacijaViza($kandidat_id, $naziv_bloka){
	Global $db;

	$select_query = $db->prepare("
						SELECT vi_status
						FROM idk_validnosti_inputa
						WHERE vi_kandidat_id = :kandidat_id AND vi_vrsta_podatka = :naziv_bloka");

	$select_query->execute(array(
						':kandidat_id' => $kandidat_id,
						':naziv_bloka' => $naziv_bloka
	));

	$select_row = $select_query->fetch();

	return ($select_row['vi_status']);
}
//GET VALIDACIJE ZA OSNOVNE INFORMACIJE
function getValidacijaOsnovneInformacije($kandidat_id){
	Global $db;
	$brojac = 0;
	$vrste_podataka = array("blok_ime", "blok_pre", "blok_dtR", "blok_mjR", "blok_drR", "blok_adr", "blok_gra", "blok_pbr", "blok_drz");
	foreach($vrste_podataka as $naziv_bloka){
		$select_query = $db->prepare("
							SELECT vi_status
							FROM idk_validnosti_inputa
							WHERE vi_kandidat_id = :kandidat_id AND vi_vrsta_podatka = :naziv_bloka");

		$select_query->execute(array(
							':kandidat_id' => $kandidat_id,
							':naziv_bloka' => $naziv_bloka
		));

		$select_row = $select_query->fetch();
		$status = $select_row['vi_status'];
		if($status != null){
			$brojac++;
		}
	}

	return $brojac;
}
function getValidacijaObrIsk($kandidat_id, $vi_vrsta_podatka, $vi_podatak_id){
	Global $db;

	$select_query = $db->prepare("
						SELECT vi_status
						FROM idk_validnosti_inputa
						WHERE vi_kandidat_id = :kandidat_id AND vi_vrsta_podatka = :vi_vrsta_podatka AND vi_podatak_id = :vi_podatak_id");

	$select_query->execute(array(
						':vi_podatak_id' => $vi_podatak_id,
						':kandidat_id' => $kandidat_id,
						':vi_vrsta_podatka' => $vi_vrsta_podatka
	));

	$select_row = $select_query->fetch();

	return ($select_row['vi_status']);
}
function getBrojUnesenihZaValidaciju($kandidat_id){
	Global $db;
	$broj_unesenih_za_validaciju = 0;

	//DA LI POSTOJI VIZA/TERMIN/APL ZA VALIDACIJU
	$select_viza = $db->prepare("SELECT kandidat_viza, datum_termina, datum_aplikacije FROM idk_kandidati WHERE kandidat_id = :kandidat_id ");
	$select_viza->execute(array( 'kandidat_id' => $kandidat_id));
	$vize_row = $select_viza->fetch();
	if($vize_row['kandidat_viza'] != 1 and $vize_row['datum_termina'] == null and $vize_row['datum_aplikacije'] == null){}
	else
		$broj_unesenih_za_validaciju++;

	//BROJ OBRAZOVANJA ZA VALIDACIJU
	$select_obr = $db->prepare("SELECT ke_id FROM idk_kandidat_edukacija WHERE ke_kandidat_id = :ke_kandidat_id");
	$select_obr->execute(array(':ke_kandidat_id' => $kandidat_id));
	while($obrazovanja = $select_obr->fetch()){
		$broj_unesenih_za_validaciju++;
	}

	//BROJ ISKUSTAVA ZA VALIDACIJU
	$select_isk = $db->prepare("SELECT kri_id FROM idk_kandidat_radno_iskustvo WHERE kri_kandidat_id = :kri_kandidat_id");
	$select_isk->execute(array(':kri_kandidat_id' => $kandidat_id));
	while($iskustva = $select_isk->fetch()){
		$broj_unesenih_za_validaciju++;
	}

	//BROJ DOKUMENATA ZA VALIDACIJU
	$select_doc = $db->prepare("SELECT document_id FROM idk_documents WHERE document_dataid = :document_dataid");
	$select_doc->execute(array(':document_dataid' => $kandidat_id));
	while($dokumenti = $select_doc->fetch()){
		$broj_unesenih_za_validaciju++;
	}
	//+1 za ime
	return($broj_unesenih_za_validaciju+9);
}
function getBrojNaValidaciji($kandidat_id){
	Global $db;
	$select_viza = $db->prepare("SELECT * FROM idk_validnosti_inputa WHERE ( vi_vrsta_podatka = 'blok_apl' OR vi_vrsta_podatka = 'blok_termin' OR vi_vrsta_podatka = 'blok_viza') AND vi_kandidat_id = :kandidat_id");
	$select_viza->execute(array('kandidat_id' => $kandidat_id));
	$br_viza = $select_viza->rowCount();

	$select_obr = $db->prepare("SELECT * FROM idk_validnosti_inputa WHERE vi_vrsta_podatka = 'obrazovanje' AND vi_kandidat_id = :kandidat_id GROUP BY vi_podatak_id");
	$select_obr->execute(array( 'kandidat_id' => $kandidat_id));
	$br_obr = $select_obr->rowCount();

	$select_isk = $db->prepare("SELECT * FROM idk_validnosti_inputa WHERE vi_vrsta_podatka = 'iskustvo' AND vi_kandidat_id = :kandidat_id GROUP BY vi_podatak_id");
	$select_isk->execute(array( 'kandidat_id' => $kandidat_id));
	$br_isk = $select_isk->rowCount();

	$select_doc = $db->prepare("SELECT * FROM idk_validnosti_inputa WHERE vi_vrsta_podatka = 'dokument' AND vi_kandidat_id = :kandidat_id GROUP BY vi_podatak_id");
	$select_doc->execute(array( 'kandidat_id' => $kandidat_id));
	$br_doc = $select_doc->rowCount();

	$select_ime = $db->prepare("SELECT * FROM idk_validnosti_inputa WHERE vi_vrsta_podatka = 'blok_ime' AND vi_kandidat_id = :kandidat_id");
	$select_ime->execute(array( 'kandidat_id' => $kandidat_id));
	$br_ime = $select_ime->rowCount();

	$select_pre = $db->prepare("SELECT * FROM idk_validnosti_inputa WHERE vi_vrsta_podatka = 'blok_pre' AND vi_kandidat_id = :kandidat_id");
	$select_pre->execute(array( 'kandidat_id' => $kandidat_id));
	$br_pre = $select_pre->rowCount();

	$select_dtR = $db->prepare("SELECT * FROM idk_validnosti_inputa WHERE vi_vrsta_podatka = 'blok_dtR' AND vi_kandidat_id = :kandidat_id");
	$select_dtR->execute(array( 'kandidat_id' => $kandidat_id));
	$br_dtR = $select_dtR->rowCount();

	$select_mjR = $db->prepare("SELECT * FROM idk_validnosti_inputa WHERE vi_vrsta_podatka = 'blok_mjR' AND vi_kandidat_id = :kandidat_id");
	$select_mjR->execute(array( 'kandidat_id' => $kandidat_id));
	$br_mjR = $select_mjR->rowCount();

	$select_drR = $db->prepare("SELECT * FROM idk_validnosti_inputa WHERE vi_vrsta_podatka = 'blok_drR' AND vi_kandidat_id = :kandidat_id");
	$select_drR->execute(array( 'kandidat_id' => $kandidat_id));
	$br_drR = $select_drR->rowCount();

	$select_adr = $db->prepare("SELECT * FROM idk_validnosti_inputa WHERE vi_vrsta_podatka = 'blok_adr' AND vi_kandidat_id = :kandidat_id");
	$select_adr->execute(array( 'kandidat_id' => $kandidat_id));
	$br_adr = $select_adr->rowCount();

	$select_gra = $db->prepare("SELECT * FROM idk_validnosti_inputa WHERE vi_vrsta_podatka = 'blok_gra' AND vi_kandidat_id = :kandidat_id");
	$select_gra->execute(array( 'kandidat_id' => $kandidat_id));
	$br_gra = $select_gra->rowCount();

	$select_pbr = $db->prepare("SELECT * FROM idk_validnosti_inputa WHERE vi_vrsta_podatka = 'blok_pbr' AND vi_kandidat_id = :kandidat_id");
	$select_pbr->execute(array( 'kandidat_id' => $kandidat_id));
	$br_pbr = $select_pbr->rowCount();

	$select_drz = $db->prepare("SELECT * FROM idk_validnosti_inputa WHERE vi_vrsta_podatka = 'blok_drz' AND vi_kandidat_id = :kandidat_id");
	$select_drz->execute(array( 'kandidat_id' => $kandidat_id));
	$br_drz = $select_drz->rowCount();

	$brojNaValidaciji = $br_viza + $br_obr + $br_isk + $br_doc + $br_ime + $br_pre + $br_dtR + $br_mjR + $br_drR + $br_adr + $br_gra + $br_pbr + $br_drz;

	return($brojNaValidaciji);

}
function getBrojNaProvjeri($kandidat_id){
	Global $db;
	$select_broj = $db->prepare("SELECT * FROM idk_validnosti_inputa WHERE vi_status = 3 AND vi_kandidat_id = :kandidat_id");
	$select_broj->execute(array( 'kandidat_id' => $kandidat_id));
	$broj = $select_broj->rowCount();
	return $broj;
}
function getSveValidacije($kandidat_id){
	Global $db;
	$validacija_prosla = 0;
	$ukupno_za_validaciju = getBrojUnesenihZaValidaciju($kandidat_id);
	$ukupno_na_validaciji = getBrojNaValidaciji($kandidat_id);

	//DA LI SU SVI PROVJERENI
	$broj_neprovjerenih = $ukupno_za_validaciju - $ukupno_na_validaciji;
	if($broj_neprovjerenih < 1){
		// DA LI IMA NEVALIDNIH (STATUS=2); ON SAM NE MOZE ICI ZATO STO NA POCETKU NEMA NIJEDNA VRIJEDNOST U TABELI VALIDNOSTI
		$select_val_ne = $db->prepare("
							SELECT vi_id FROM idk_validnosti_inputa WHERE (vi_status = 2 OR vi_status = 3) AND vi_kandidat_id = :vi_kandidat_id");

		$select_val_ne->execute(array(
							':vi_kandidat_id' => $kandidat_id
		));
		$br_nevalidnih = $select_val_ne->rowCount();
		if($br_nevalidnih == 0)
			$validacija_prosla = 1;
	}

	return($validacija_prosla);
}

//PUSH NOTIFICATION FUNCTION FOR MESSENGER - ANDROID
function send_bot_notification_android($tokens, $log_poruka)
{
	$message = array(
	"body" => $log_poruka,
	"title" => "Jobstep Messenger",
	"sound"=> "default",
	"icon" => "ic_launcher",
	"vibrate" => 1,
	"badge" => 1
	);

	$url = 'https://fcm.googleapis.com/fcm/send';

	$fields = array(
		 'to' => $tokens,
		 'notification' => $message
		);

	$headers = array(
		'Authorization:key = AIzaSyCX0v3WYVyg4o3M9TH3x0I4CWA3wXXOris',
		'Content-Type: application/json'
		);

   $ch = curl_init();
	  curl_setopt($ch, CURLOPT_URL, $url);
	  curl_setopt($ch, CURLOPT_POST, true);
	  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	  curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
	  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
	  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
	  $result = curl_exec($ch);
	  if ($result === FALSE) {
		  die('Curl failed: ' . curl_error($ch));
	  }
	  curl_close($ch); 
	  return $result;
}
//PUSH NOTIFICATION FUNCTION FOR MESSENGER - IOS
function send_bot_notification_ios($tokens, $log_poruka)
{
	$message = array(
	"body" => $log_poruka,
	"title" => "Jobstep Messenger",
	"sound" => 1,
	"vibrate" => 1,
	"badge" => 1,
	);

	$url = 'https://fcm.googleapis.com/fcm/send';

	$fields = array(
		 'to' => $tokens,
		 'notification' => $message,
		 'priority' => 'high'
		);

	$headers = array(
		'Authorization:key = AIzaSyD8gil81LAQjDJKaShtHrMpO_4babgAtX8',
		'Content-Type: application/json'
		);

   $ch = curl_init();
	  curl_setopt($ch, CURLOPT_URL, $url);
	  curl_setopt($ch, CURLOPT_POST, true);
	  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	  curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
	  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
	  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
	  $result = curl_exec($ch);
	  if ($result === FALSE) {
		  die('Curl failed: ' . curl_error($ch));
	  }
	  curl_close($ch);
	  return $result;

}
//PUSH NOTIFICATION FUNCTION FOR PARTNER APP
function send_notification_partnerapp($tokens, $notifikacija_title, $notifikacija_text)
{

	$message = array(
	"body" => $notifikacija_text,
	"title" => "" . $notifikacija_title ."",
	"sound"=> "default",
	"icon" => "ic_launcher",
	"vibrate" => 1,
	"badge" => 1
	);

	$url = 'https://fcm.googleapis.com/fcm/send';

	$fields = array(
		 'to' => $tokens,
		 'notification' => $message
		);

	$headers = array(
		'Authorization:key = AIzaSyAgPovuUclLIYmjrbKfVwE3wb_AO3yFkaI',
		'Content-Type: application/json'
		);

   $ch = curl_init();
	  curl_setopt($ch, CURLOPT_URL, $url);
	  curl_setopt($ch, CURLOPT_POST, true);
	  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	  curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
	  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
	  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
	  $result = curl_exec($ch);
	  if ($result === FALSE) {
		  die('Curl failed: ' . curl_error($ch));
	  }
	  curl_close($ch);
	  return $result;
}

function insertClientStats($client_id, $status, $fc_or_sales, $stats_desc){
	Global $db;
	Global $logged_employee_id;
	$date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_clients_stats
						(client_id, status, fc_or_sales, date, zaposlenik_id, stats_desc)
					VALUES
						(:client_id, :status, :fc_or_sales, :date, :zaposlenik_id, :stats_desc)");

	$log_query->execute(array(
					':client_id' => $client_id,
					':status' => $status,
					':fc_or_sales' => $fc_or_sales,
					':date' => $date,
					':zaposlenik_id' => $logged_employee_id,
					':stats_desc' => $stats_desc
					));
}

function CopyKandidatinDipl($kandidat_id, $povijest, $sms_bot_crm){
	Global $db;
	Global $logged_employee_id;

		//Uzimanje podataka iz idk_kandidati start

		$info_idk_kandidati = $db->prepare("
									SELECT
										kandidat_id,
										kandidat_ime,
										kandidat_prezime,
										kandidat_adresa,
										kandidat_pbroj,
										kandidat_grad,
										kandidat_mobitel,
										kandidat_email
									FROM
										idk_kandidati
									WHERE
										kandidat_id = :kandidat_id
									");
		$info_idk_kandidati->execute(array(
									':kandidat_id' => $kandidat_id
									));
		$info_idk_kandidati_row = $info_idk_kandidati->fetch();
		$id_kand = $info_idk_kandidati_row['kandidat_id'];
		$ime_new_ND_cand1 = $info_idk_kandidati_row['kandidat_ime'];
		$prezime_new_ND_cand1 = $info_idk_kandidati_row['kandidat_prezime'];
		$ulica_new_ND_cand1 = $info_idk_kandidati_row['kandidat_adresa'];
		$postanski_broj_new_ND_cand1 = $info_idk_kandidati_row['kandidat_pbroj'];
		$grad_new_ND_cand1 = $info_idk_kandidati_row['kandidat_grad'];
		$email_new_ND_cand1 = $info_idk_kandidati_row['kandidat_email'];
		$mobilni_new_ND_cand1 = $info_idk_kandidati_row['kandidat_mobitel'];
		
		$menager = getDodjeliAgentuDIPLR($mobilni_new_ND_cand1);
		//REZULTAT ALGORITMA 
		$zadnji_menager = $menager['stari'];
		$novi_menager = $menager['novi'];
		$novi_menager_team = getTeamIdByEmployee($novi_menager);
		//UPDATE kandidata u Dipl
		$new_ND_kandidat = $db->prepare("
						INSERT INTO idk_nd_kandidata
							(
								ime_nd_kandidata,
								prezime_nd_kandidata,
								ulica_nd_kandidata,
								postanski_broj_nd_kandidata,
								grad_nd_kandidata,
								mobilni_nd_kandidata,
								email_nd_kandidata,
								vrijeme_kreiranja_nd_kandidata,
								zaduzen_zaposlenik_nd_kandidata,
								tim_nd_kandidata,
								status_nd_kandidata,
								povijest_nd_kandidata,
								povijest_vrsta_nd_kandidata,
								kandidat_idd
							)
						VALUES
							(
								:ime_nd_kandidata,
								:prezime_nd_kandidata,
								:ulica_nd_kandidata,
								:postanski_broj_nd_kandidata,
								:grad_nd_kandidata,
								:mobilni_nd_kandidata,
								:email_nd_kandidata,
								:vrijeme_kreiranja_nd_kandidata,
								:zaduzen_zaposlenik_nd_kandidata,
								:tim_nd_kandidata,
								:status_nd_kandidata,
								:povijest_nd_kandidata,
								:povijest_vrsta_nd_kandidata,
								:kandidat_idd
							)");

		$new_ND_kandidat->execute(array(
						':ime_nd_kandidata' => $ime_new_ND_cand1,
						':prezime_nd_kandidata' => $prezime_new_ND_cand1,
						':ulica_nd_kandidata' => $ulica_new_ND_cand1,
						':postanski_broj_nd_kandidata' => $postanski_broj_new_ND_cand1,
						':grad_nd_kandidata' => $grad_new_ND_cand1,
						':mobilni_nd_kandidata' => $mobilni_new_ND_cand1,
						':email_nd_kandidata' => $email_new_ND_cand1,
						':vrijeme_kreiranja_nd_kandidata' => date('Y-m-d H:i:s'),
						':zaduzen_zaposlenik_nd_kandidata' => $novi_menager,
						':tim_nd_kandidata' => $novi_menager_team,
						':status_nd_kandidata' => 1,
						':povijest_nd_kandidata' => $povijest,
						':povijest_vrsta_nd_kandidata' => $sms_bot_crm,
						':kandidat_idd' => $kandidat_id
						));
		//Get last ID
		$kandidat_id_nd = $db->lastInsertId();

		//Update statistike menadzera START
		// vrsta_aktivnosti == 0 -> oznacava da je sistem automatski dodjelio tom menadzeru kandidata
		$insert_statistike_menagera = $db->prepare("
					INSERT INTO idk_nd_menadzeri_statistike
						(idd_broj_nd_kandidata, zaduzen_zaposlenik_id, vrsta_aktivnosti, vrijeme_aktivnosti)
					VALUES
						(:idd_broj_nd_kandidata, :zaduzen_zaposlenik_id, :vrsta_aktivnosti, :vrijeme_aktivnosti)");

		$insert_statistike_menagera->execute(array(
					':idd_broj_nd_kandidata' => $kandidat_id_nd,
					':zaduzen_zaposlenik_id' => $novi_menager,
					':vrsta_aktivnosti' => 0,
					':vrijeme_aktivnosti' => date('Y-m-d H:i:s')
					));

		//Slanje maila novom menadzeru sa linkom kandidata njemu dodjeljenog
		/*$user_query = $db->prepare("
								SELECT employee_firstname, employee_lastname, employee_email
								FROM idk_employees
								WHERE employee_id = :employee_id");

		$user_query->execute(array(
						':employee_id' => $novi_menager));

		$user = $user_query->fetch();

		$employee_firstname = $user['employee_firstname'];
		$employee_lastname = $user['employee_lastname'];
		$employee_email = $user['employee_email'];

		//Send email to user
		$mail_email = $employee_email;
		$mail_name = $employee_firstname . ' ' . $employee_lastname;
		$mail_subject = "Dipl modul - Novi kandidat";
		$mail_url = "" . getSiteUrlr() . "/nostrifikacija_diploma?page=otvori_ND_kandidata&id=".$kandidat_id_nd." ";
		$mail_body = "
						<p>Zaduženi ste za novog kandidata Na DIPL modulu.</p>
						<p>Detalje pogledajte na linku: " . $mail_url . "</p>
		";
		$mail_altbody = "
						<p>Zaduženi ste za novog kandidata Na DIPL modulu.</p>
						<p>Detalje pogledajte na linku: " . $mail_url . "</p>
		";
		sendEmail($mail_email, $mail_name, $mail_subject, $mail_body, $mail_altbody);
		*/
		//Update log statusa ND kandidata
		$new_ND_status = $db->prepare("
							INSERT INTO idk_nd_kandidata_status_log
							(
								idd_broj_nd_kandidata,
								status_nd_kandidata,
								vrijeme_promjene_statusa_nd_kandidata
							)
							VALUES
							(
								:idd_broj_nd_kandidata,
								:status_nd_kandidata,
								:vrijeme_promjene_statusa_nd_kandidata
							)
		");
		$new_ND_status->execute(array(
						':idd_broj_nd_kandidata' => $kandidat_id_nd,
						':status_nd_kandidata' => 1,
						':vrijeme_promjene_statusa_nd_kandidata' => date('Y-m-d H:i:s')
						));

		
		//ADD TO LOGS START
			$log_date = date('Y-m-d H:i:s');
			$log_desc = "DIPL -> Dodan novi kandidat sa ID = [".$kandidat_id_nd."] iz kandidata sa ID = [".$kandidat_id."] - Zadužen zaposlenik: ".$novi_menager." ";
			$log_query = $db->prepare("
							INSERT INTO idk_logs
								(log_employeeid, log_desc, log_date)
							VALUES
								(:log_employeeid, :log_desc, :log_date)");

			$log_query->execute(array(
							':log_employeeid' => $logged_employee_id,
							':log_desc' => $log_desc,
							':log_date' => $log_date));
		//ADD TO LOGS END
}

function CopyKandidatCRMinDipl($kandidat_id, $povijest, $sms_bot_crm){
	Global $db;
	Global $logged_employee_id;
	
		$novi_menager = $logged_employee_id;
		$novi_menager_team = getTeamIdByEmployee($novi_menager);
		//Uzimanje podataka iz idk_kandidati start

		$info_idk_kandidati = $db->prepare("
									SELECT
										kandidat_id,
										kandidat_ime,
										kandidat_prezime,
										kandidat_adresa,
										kandidat_pbroj,
										kandidat_grad,
										kandidat_mobitel,
										kandidat_email
									FROM
										idk_kandidati
									WHERE
										kandidat_id = :kandidat_id
									");
		$info_idk_kandidati->execute(array(
									':kandidat_id' => $kandidat_id
									));
		$info_idk_kandidati_row = $info_idk_kandidati->fetch();
		$id_kand = $info_idk_kandidati_row['kandidat_id'];
		$ime_new_ND_cand1 = $info_idk_kandidati_row['kandidat_ime'];
		$prezime_new_ND_cand1 = $info_idk_kandidati_row['kandidat_prezime'];
		$ulica_new_ND_cand1 = $info_idk_kandidati_row['kandidat_adresa'];
		$postanski_broj_new_ND_cand1 = $info_idk_kandidati_row['kandidat_pbroj'];
		$grad_new_ND_cand1 = $info_idk_kandidati_row['kandidat_grad'];
		$email_new_ND_cand1 = $info_idk_kandidati_row['kandidat_email'];
		$mobilni_new_ND_cand1 = $info_idk_kandidati_row['kandidat_mobitel'];
		//UPDATE kandidata u Dipl
		$new_ND_kandidat = $db->prepare("
						INSERT INTO idk_nd_kandidata
							(
								ime_nd_kandidata,
								prezime_nd_kandidata,
								ulica_nd_kandidata,
								postanski_broj_nd_kandidata,
								grad_nd_kandidata,
								mobilni_nd_kandidata,
								email_nd_kandidata,
								vrijeme_kreiranja_nd_kandidata,
								zaduzen_zaposlenik_nd_kandidata,
								tim_nd_kandidata,
								status_nd_kandidata,
								povijest_nd_kandidata,
								povijest_vrsta_nd_kandidata,
								kandidat_idd
							)
						VALUES
							(
								:ime_nd_kandidata,
								:prezime_nd_kandidata,
								:ulica_nd_kandidata,
								:postanski_broj_nd_kandidata,
								:grad_nd_kandidata,
								:mobilni_nd_kandidata,
								:email_nd_kandidata,
								:vrijeme_kreiranja_nd_kandidata,
								:zaduzen_zaposlenik_nd_kandidata,
								:tim_nd_kandidata,
								:status_nd_kandidata,
								:povijest_nd_kandidata,
								:povijest_vrsta_nd_kandidata,
								:kandidat_idd
							)");

		$new_ND_kandidat->execute(array(
						':ime_nd_kandidata' => $ime_new_ND_cand1,
						':prezime_nd_kandidata' => $prezime_new_ND_cand1,
						':ulica_nd_kandidata' => $ulica_new_ND_cand1,
						':postanski_broj_nd_kandidata' => $postanski_broj_new_ND_cand1,
						':grad_nd_kandidata' => $grad_new_ND_cand1,
						':mobilni_nd_kandidata' => $mobilni_new_ND_cand1,
						':email_nd_kandidata' => $email_new_ND_cand1,
						':vrijeme_kreiranja_nd_kandidata' => date('Y-m-d H:i:s'),
						':zaduzen_zaposlenik_nd_kandidata' => $novi_menager,
						':tim_nd_kandidata' => $novi_menager_team,
						':status_nd_kandidata' => 1,
						':povijest_nd_kandidata' => $povijest,
						':povijest_vrsta_nd_kandidata' => $sms_bot_crm,
						':kandidat_idd' => $kandidat_id
						));
		//Get last ID
		$kandidat_id_nd = $db->lastInsertId();

		//Update statistike menadzera START
		// vrsta_aktivnosti == 0 -> oznacava da je sistem automatski dodjelio tom menadzeru kandidata
		$insert_statistike_menagera = $db->prepare("
					INSERT INTO idk_nd_menadzeri_statistike
						(idd_broj_nd_kandidata, zaduzen_zaposlenik_id, vrsta_aktivnosti, vrijeme_aktivnosti)
					VALUES
						(:idd_broj_nd_kandidata, :zaduzen_zaposlenik_id, :vrsta_aktivnosti, :vrijeme_aktivnosti)");

		$insert_statistike_menagera->execute(array(
					':idd_broj_nd_kandidata' => $kandidat_id_nd,
					':zaduzen_zaposlenik_id' => $novi_menager,
					':vrsta_aktivnosti' => 0,
					':vrijeme_aktivnosti' => date('Y-m-d H:i:s')
					));
		
		$new_ND_status = $db->prepare("
							INSERT INTO idk_nd_kandidata_status_log
							(
								idd_broj_nd_kandidata,
								status_nd_kandidata,
								vrijeme_promjene_statusa_nd_kandidata
							)
							VALUES
							(
								:idd_broj_nd_kandidata,
								:status_nd_kandidata,
								:vrijeme_promjene_statusa_nd_kandidata
							)
		");
		$new_ND_status->execute(array(
						':idd_broj_nd_kandidata' => $kandidat_id_nd,
						':status_nd_kandidata' => 1,
						':vrijeme_promjene_statusa_nd_kandidata' => date('Y-m-d H:i:s')
						));

		$update_idk_kandidati = $db->prepare("UPDATE idk_kandidati SET kandidat_dipl_id = $kandidat_id_nd WHERE kandidat_id = $kandidat_id");
		$update_idk_kandidati->execute();
		
		//ADD TO LOGS START
			$log_date = date('Y-m-d H:i:s');
			$log_desc = "DIPL -> Dodan novi kandidat sa ID = [".$kandidat_id_nd."] iz kandidata sa ID = [".$kandidat_id."] - Zadužen zaposlenik: ".$novi_menager." ";
			$log_query = $db->prepare("
							INSERT INTO idk_logs
								(log_employeeid, log_desc, log_date)
							VALUES
								(:log_employeeid, :log_desc, :log_date)");

			$log_query->execute(array(
							':log_employeeid' => $logged_employee_id,
							':log_desc' => $log_desc,
							':log_date' => $log_date));
		//ADD TO LOGS END
}

function CopyDAKinDipl($kandidat_id, $povijest, $apl_crm){
	Global $db;
	Global $logged_employee_id;


	//Uzimanje podataka iz idk_kandidati start

	$info_idk_dak_kandidati = $db->prepare("
								SELECT
									id_dak_kandidat,
									name_dak_kandidat,
									lastname_dak_kandidat,
									email_dak_kandidat,
									tel_dak_kandidat
								FROM
									idk_dak_kandidati
								WHERE
									id_dak_kandidat = :id_dak_kandidat
								");
	$info_idk_dak_kandidati->execute(array(
								':id_dak_kandidat' => $kandidat_id
								));
	$info_dak_row = $info_idk_dak_kandidati->fetch();
	$id_kand = $info_dak_row['id_dak_kandidat'];
	$ime_new_ND_cand1 = $info_dak_row['name_dak_kandidat'];
	$prezime_new_ND_cand1 = $info_dak_row['lastname_dak_kandidat'];
	$email_new_ND_cand1 = $info_dak_row['email_dak_kandidat'];
	$mobilni_new_ND_cand1 = $info_dak_row['tel_dak_kandidat'];
	$menager = getDodjeliAgentuDIPLR($mobilni_new_ND_cand1);
	//REZULTAT ALGORITMA 
	$zadnji_menager = $menager['stari'];
	$novi_menager = $menager['novi'];
	$novi_menager_team = getTeamIdByEmployee($novi_menager);
	//UPDATE DAK u Dipl
	$new_ND_kandidat = $db->prepare("
					INSERT INTO idk_nd_kandidata
						(
							ime_nd_kandidata,
							prezime_nd_kandidata,
							mobilni_nd_kandidata,
							email_nd_kandidata,
							vrijeme_kreiranja_nd_kandidata,
							zaduzen_zaposlenik_nd_kandidata,
							tim_nd_kandidata,
							status_nd_kandidata,
							povijest_nd_kandidata,
							povijest_vrsta_nd_kandidata,
							kandidat_idd
						)
					VALUES
						(
							:ime_nd_kandidata,
							:prezime_nd_kandidata,
							:mobilni_nd_kandidata,
							:email_nd_kandidata,
							:vrijeme_kreiranja_nd_kandidata,
							:zaduzen_zaposlenik_nd_kandidata,
							:tim_nd_kandidata,
							:status_nd_kandidata,
							:povijest_nd_kandidata,
							:povijest_vrsta_nd_kandidata,
							:kandidat_idd
						)");

	$new_ND_kandidat->execute(array(
					':ime_nd_kandidata' => $ime_new_ND_cand1,
					':prezime_nd_kandidata' => $prezime_new_ND_cand1,
					':mobilni_nd_kandidata' => $mobilni_new_ND_cand1,
					':email_nd_kandidata' => $email_new_ND_cand1,
					':vrijeme_kreiranja_nd_kandidata' => date('Y-m-d H:i:s'),
					':zaduzen_zaposlenik_nd_kandidata' => $novi_menager,
					':tim_nd_kandidata' => $novi_menager_team,
					':status_nd_kandidata' => 1,
					':povijest_nd_kandidata' => $povijest,
					':povijest_vrsta_nd_kandidata' => $apl_crm,
					':kandidat_idd' => $kandidat_id
					));
	//Get last ID
	$kandidat_id_nd = $db->lastInsertId();

	//Update statistike menadzera START
	// vrsta_aktivnosti == 0 -> oznacava da je sistem automatski dodjelio tom menadzeru kandidata
	$insert_statistike_menagera = $db->prepare("
				INSERT INTO idk_nd_menadzeri_statistike
					(idd_broj_nd_kandidata, zaduzen_zaposlenik_id, vrsta_aktivnosti, vrijeme_aktivnosti)
				VALUES
					(:idd_broj_nd_kandidata, :zaduzen_zaposlenik_id, :vrsta_aktivnosti, :vrijeme_aktivnosti)");

	$insert_statistike_menagera->execute(array(
				':idd_broj_nd_kandidata' => $kandidat_id_nd,
				':zaduzen_zaposlenik_id' => $novi_menager,
				':vrsta_aktivnosti' => 0,
				':vrijeme_aktivnosti' => date('Y-m-d H:i:s')
				));

	//Slanje maila novom menadzeru sa linkom kandidata njemu dodjeljenog
	/*$user_query = $db->prepare("
							SELECT employee_firstname, employee_lastname, employee_email
							FROM idk_employees
							WHERE employee_id = :employee_id");

	$user_query->execute(array(
					':employee_id' => $novi_menager));

	$user = $user_query->fetch();

	$employee_firstname = $user['employee_firstname'];
	$employee_lastname = $user['employee_lastname'];
	$employee_email = $user['employee_email'];

	//Send email to user
	$mail_email = $employee_email;
	$mail_name = $employee_firstname . ' ' . $employee_lastname;
	$mail_subject = "Dipl modul - Novi kandidat";
	$mail_url = "" . getSiteUrlr() . "/nostrifikacija_diploma?page=otvori_ND_kandidata&id=".$kandidat_id_nd." ";
	$mail_body = "
					<p>Zaduženi ste za novog kandidata Na DIPL modulu.</p>
					<p>Detalje pogledajte na linku: " . $mail_url . "</p>
	";
	$mail_altbody = "
					<p>Zaduženi ste za novog kandidata Na DIPL modulu.</p>
					<p>Detalje pogledajte na linku: " . $mail_url . "</p>
	";

	sendEmail($mail_email, $mail_name, $mail_subject, $mail_body, $mail_altbody);
	*/
	//Update log statusa ND kandidata
	$new_ND_status = $db->prepare("
						INSERT INTO idk_nd_kandidata_status_log
						(
							idd_broj_nd_kandidata,
							status_nd_kandidata,
							vrijeme_promjene_statusa_nd_kandidata
						)
						VALUES
						(
							:idd_broj_nd_kandidata,
							:status_nd_kandidata,
							:vrijeme_promjene_statusa_nd_kandidata
						)
	");
	$new_ND_status->execute(array(
					':idd_broj_nd_kandidata' => $kandidat_id_nd,
					':status_nd_kandidata' => 1,
					':vrijeme_promjene_statusa_nd_kandidata' => date('Y-m-d H:i:s')
					));

	//ADD TO LOGS START
		$log_date = date('Y-m-d H:i:s');
		$log_desc = "DIPL -> Dodan novi kandidat sa ID = [".$kandidat_id_nd."] iz DAK sa ID = [".$kandidat_id."] - Zadužen zaposlenik: ".$novi_menager." ";
		$log_query = $db->prepare("
						INSERT INTO idk_logs
							(log_employeeid, log_desc, log_date)
						VALUES
							(:log_employeeid, :log_desc, :log_date)");

		$log_query->execute(array(
						':log_employeeid' => $logged_employee_id,
						':log_desc' => $log_desc,
						':log_date' => $log_date));
	//ADD TO LOGS END

	return $kandidat_id_nd;
}
function getBrKandFinc($lg_id){
	
	Global $db;
	$query = $db->prepare("
					SELECT kandidat_id
					FROM idk_kandidati
					WHERE kandidat_visitedurl = $lg_id
					ORDER BY kandidat_id ASC
					");

	$query->execute();
	$br_kandidata = 0;
	while($row = $query->fetch()){

		$kandidat_id = $row['kandidat_id'];
		$query_kan_fin = $db->prepare("
						SELECT nalog_id
						FROM idk_kandidat_financije
						WHERE kandidat_id = :kandidat_id AND kf_status != :kf_status");
		$query_kan_fin->execute(array(
			'kandidat_id' => $kandidat_id,
			'kf_status' => 2
		));
		if($query_kan_fin->rowCount() > 0)
			$br_kandidata++;
		
	}
	return $br_kandidata;
	
}

function getJbNetCustomerRating($stars,$date_od,$date_do){
	//dohvati broj određene ocjene u tabeli
	Global $db;
	
	if($date_od == ""){
	$query = $db->prepare("
					SELECT count(*) AS 'count'
					FROM idk_analitika
					WHERE analitika_rating = $stars
					");

	$query->execute();
	$row = $query->fetch();
	$broj = $row['count'];
	return $broj;
	
	}else{
		$query = $db->prepare("
					SELECT count(*) AS 'count'
					FROM idk_analitika
					WHERE analitika_rating = $stars AND analitika_datum between '$date_od' AND '$date_do'
					");

		$query->execute();
		$row = $query->fetch();
		$broj = $row['count'];
		return $broj;
	}
}
function getInfoPartnerPreporuka($jp_id){
		Global $db;
		
		$query = $db->prepare("
		SELECT jp_imeprezime
		FROM  idk_jobstep_partners
		WHERE jp_id = :jp_id");

		$query->execute(array(
					':jp_id' => $jp_id));

		$row = $query->fetch();
		$jp_imeprezime = $row['jp_imeprezime'];
		echo $jp_imeprezime;
}

function getInfoPartnerPreporukaR($jp_id){
		Global $db;
		
		$query = $db->prepare("
		SELECT jp_imeprezime
		FROM  idk_jobstep_partners
		WHERE jp_id = :jp_id");

		$query->execute(array(
					':jp_id' => $jp_id));

		$row = $query->fetch();
		$jp_imeprezime = $row['jp_imeprezime'];
		return $jp_imeprezime;
}
function getPartnerAdresaR($jp_id){ // VRAĆA NIZ SA ADRESOM
	Global $db;
	$rezultat = array();
	
	
		$query = $db->prepare("
		SELECT jp_ulica, jp_postanskibroj, jp_grad, jp_drzava
		FROM  idk_jobstep_partners
		WHERE jp_id = :jp_id");

		$query->execute(array(
					':jp_id' => $jp_id));

		$row = $query->fetch();
		if($row['jp_drzava'] == "BA"){
			$drzava = "Bosna i Hercegovina";
		}
		
		
		array_push($rezultat,$row['jp_ulica']);
		array_push($rezultat,$row['jp_postanskibroj']);
		array_push($rezultat,$row['jp_grad']);
		array_push($rezultat,$drzava);
		
		
		return $rezultat;
}
// DOBAVI IZNOS PROVIZIJA ISPLAĆENIH PARTNERU !
function getPartnerIzvrseneUplateR($jp_id){
		Global $db;
		$sum_uplata = 0;
		
		$query = $db->prepare("
		SELECT jp_uplate_provizija
		FROM  idk_partner_uplate
		WHERE jp_uplate_partnerid = :jp_uplate_partnerid AND jp_uplate_status = :jp_uplate_status"); 

		$query->execute(array(
					':jp_uplate_partnerid' => $jp_id,
					':jp_uplate_status' => 1 
					));

		while($row = $query->fetch()){
			$sum_uplata += $row['jp_uplate_provizija'];
		}
		return $sum_uplata;
}
function getIznosRate($vrsta_ugovora, $drzava, $broj_rate){
	
	switch($vrsta_ugovora){
		case 1: 
			if($drzava == "Srbija"){
				$rata1 = 29500;
				$rata2 = 29500;
				$rata3 = 0;
				$rata4 = 0;
				$rata5 = 0;
			}else{
				$rata1 = 475;
				$rata2 = 475;
				$rata3 = 0;
				$rata4 = 0;
				$rata5 = 0;
			}
		break;
		case 2: case 72:
			if($drzava == "Srbija"){
				$rata1 = 20650;
				$rata2 = 20650;
				$rata3 = 0;
				$rata4 = 0;
				$rata5 = 0;
			}else{
				$rata1 = 332.5;
				$rata2 = 332.5;
				$rata3 = 0;
				$rata4 = 0;
				$rata5 = 0;
			}
		break;
		case 5:
			if($drzava == "Srbija"){
				$rata1 = 19666;
				$rata2 = 19667;
				$rata3 = 19667;
				$rata4 = 0;
				$rata5 = 0;
			}else{
				$rata1 = 350;
				$rata2 = 300;
				$rata3 = 300;
				$rata4 = 0;
				$rata5 = 0;
			}
		break;
		case 6: case 73:
			if($drzava == "Srbija"){
				$rata1 = 13766;
				$rata2 = 13767;
				$rata3 = 13767;
				$rata4 = 0;
				$rata5 = 0;
			}else{
				$rata1 = 225;
				$rata2 = 220;
				$rata3 = 220;
				$rata4 = 0;
				$rata5 = 0;
			}
		break;
		case 7: 
			if($drzava == "Srbija"){
				$rata1 = 14750;
				$rata2 = 14750;
				$rata3 = 14750;
				$rata4 = 14750;
				$rata5 = 0;
			}else{
				$rata1 = 260;
				$rata2 = 230;
				$rata3 = 230;
				$rata4 = 230;
				$rata5 = 0;
			}
		break;
		case 8: case 74:
			if($drzava == "Srbija"){
				$rata1 = 10325;
				$rata2 = 10325;
				$rata3 = 10325;
				$rata4 = 10325;
				$rata5 = 0;
			}else{
				$rata1 = 185;
				$rata2 = 160;
				$rata3 = 160;
				$rata4 = 160;
				$rata5 = 0;
			}
		break;
		case 3: 
			if($drzava == "Srbija"){
				$rata1 = 11800;
				$rata2 = 11800;
				$rata3 = 11800;
				$rata4 = 11800;
				$rata5 = 11800;
			}else{
				$rata1 = 190;
				$rata2 = 190;
				$rata3 = 190;
				$rata4 = 190;
				$rata5 = 190;
			}
		break;
		case 4: case 75:
			if($drzava == "Srbija"){
				$rata1 = 8260;
				$rata2 = 8260;
				$rata3 = 8260;
				$rata4 = 8260;
				$rata5 = 8260;
			}else{
				$rata1 = 165;
				$rata2 = 125;
				$rata3 = 125;
				$rata4 = 125;
				$rata5 = 125;
			}
		break;
		case 9: case 11:
			if($drzava == "Srbija"){
				$rata1 = 53100;
				$rata2 = 0;
				$rata3 = 0;
				$rata4 = 0;
				$rata5 = 0;
			}else{
				$rata1 = 855;
				$rata2 = 0;
				$rata3 = 0;
				$rata4 = 0;
				$rata5 = 0;
			}
		break;
		case 10: case 12: case 71:
			if($drzava == "Srbija"){
				$rata1 = 41300;
				$rata2 = 0;
				$rata3 = 0;
				$rata4 = 0;
				$rata5 = 0;
			}else{
				$rata1 = 665;
				$rata2 = 0;
				$rata3 = 0;
				$rata4 = 0;
				$rata5 = 0;
			}
		break;
		case 21: case 61:
			if($drzava == "Srbija"){
				$rata1 = 47200;
				$rata2 = 0;
				$rata3 = 0;
				$rata4 = 0;
				$rata5 = 0;
			}else{
				$rata1 = 760;
				$rata2 = 0;
				$rata3 = 0;
				$rata4 = 0;
				$rata5 = 0;
			}
		break;
		case 22: case 62:
			if($drzava == "Srbija"){
				$rata1 = 23600;
				$rata2 = 23600;
				$rata3 = 0;
				$rata4 = 0;
				$rata5 = 0;
			}else{
				$rata1 = 380;
				$rata2 = 380;
				$rata3 = 0;
				$rata4 = 0;
				$rata5 = 0;
			}
		break;
		case 23: case 63:
			if($drzava == "Srbija"){
				$rata1 = 15734;
				$rata2 = 15733;
				$rata3 = 15733;
				$rata4 = 0;
				$rata5 = 0;
			}else{
				$rata1 = 260;
				$rata2 = 250;
				$rata3 = 250;
				$rata4 = 0;
				$rata5 = 0;
			}
		break;
		case 24: case 64:
			if($drzava == "Srbija"){
				$rata1 = 11800;
				$rata2 = 11800;
				$rata3 = 11800;
				$rata4 = 11800;
				$rata5 = 0;
			}else{
				$rata1 = 190;
				$rata2 = 190;
				$rata3 = 190;
				$rata4 = 190;
				$rata5 = 0;
			}
		break;
		case 25: case 65:
			if($drzava == "Srbija"){
				$rata1 = 9440;
				$rata2 = 9440;
				$rata3 = 9440;
				$rata4 = 9440;
				$rata5 = 9440;
			}else{
				$rata1 = 160;
				$rata2 = 150;
				$rata3 = 150;
				$rata4 = 150;
				$rata5 = 150;
			}
		break;
		case 51:
			if($drzava == "Srbija"){
				$rata1 = 29500;
				$rata2 = 0;
				$rata3 = 0;
				$rata4 = 0;
				$rata5 = 0;
			}else{
				$rata1 = 475;
				$rata2 = 0;
				$rata3 = 0;
				$rata4 = 0;
				$rata5 = 0;
			}
		break;
		case 52:
			if($drzava == "Srbija"){
				$rata1 = 14750;
				$rata2 = 14750;
				$rata3 = 0;
				$rata4 = 0;
				$rata5 = 0;
			}else{
				$rata1 = 240;
				$rata2 = 235;
				$rata3 = 0;
				$rata4 = 0;
				$rata5 = 0;
			}
		break;
		case 53:
			if($drzava == "Srbija"){
				$rata1 = 9834;
				$rata2 = 9833;
				$rata3 = 9833;
				$rata4 = 0;
				$rata5 = 0;
			}else{
				$rata1 = 160;
				$rata2 = 160;
				$rata3 = 155;
				$rata4 = 0;
				$rata5 = 0;
			}
		break;
		case 54:
			if($drzava == "Srbija"){
				$rata1 = 7375;
				$rata2 = 7375;
				$rata3 = 7375;
				$rata4 = 7375;
				$rata5 = 0;
			}else{
				$rata1 = 120;
				$rata2 = 120;
				$rata3 = 120;
				$rata4 = 115;
				$rata5 = 0;
			}
		break;
		case 55:
			if($drzava == "Srbija"){
				$rata1 = 5900;
				$rata2 = 5900;
				$rata3 = 5900;
				$rata4 = 5900;
				$rata5 = 5900;
			}else{
				$rata1 = 95;
				$rata2 = 95;
				$rata3 = 95;
				$rata4 = 95;
				$rata5 = 95;
			}
		break;
		case 99:
			if($drzava == "Srbija"){
				$rata1 = 0;
				$rata2 = 0;
				$rata3 = 0;
				$rata4 = 0;
				$rata5 = 0;
			}else{
				$rata1 = 0;
				$rata2 = 0;
				$rata3 = 0;
				$rata4 = 0;
				$rata5 = 0;
			}
		break;
		case 82:
			if($drzava == "Srbija"){
				$rata1 = 26550;
				$rata2 = 26550;
				$rata3 = 0;
				$rata4 = 0;
				$rata5 = 0;
			}else{
				$rata1 = 427.5;
				$rata2 = 427.5;
				$rata3 = 0;
				$rata4 = 0;
				$rata5 = 0;
			}
		break;
		case 83:
			if($drzava == "Srbija"){
				$rata1 = 17700;
				$rata2 = 17700;
				$rata3 = 17700;
				$rata4 = 0;
				$rata5 = 0;
			}else{
				$rata1 = 285;
				$rata2 = 285;
				$rata3 = 285;
				$rata4 = 0;
				$rata5 = 0;
			}
		break;
		case 84:
			if($drzava == "Srbija"){
				$rata1 = 13275;
				$rata2 = 13275;
				$rata3 = 13275;
				$rata4 = 13275;
				$rata5 = 0;
			}else{
				$rata1 = 225;
				$rata2 = 210;
				$rata3 = 210;
				$rata4 = 210;
				$rata5 = 0;
			}
		break;
		case 85:
			if($drzava == "Srbija"){
				$rata1 = 10620;
				$rata2 = 10620;
				$rata3 = 10620;
				$rata4 = 10620;
				$rata5 = 10620;
			}else{
				$rata1 = 171;
				$rata2 = 171;
				$rata3 = 171;
				$rata4 = 171;
				$rata5 = 171;
			}
		break;
		case 41:
			if($drzava == "Srbija"){
				$rata1 = 17700;
				$rata2 = 0;
				$rata3 = 0;
				$rata4 = 0;
				$rata5 = 0;
			}else{
				$rata1 = 285;
				$rata2 = 0;
				$rata3 = 0;
				$rata4 = 0;
				$rata5 = 0;
			}
		break;
		case 42:
			if($drzava == "Srbija"){
				$rata1 = 8850;
				$rata2 = 8850;
				$rata3 = 0;
				$rata4 = 0;
				$rata5 = 0;
			}else{
				$rata1 = 142.5;
				$rata2 = 142.5;
				$rata3 = 0;
				$rata4 = 0;
				$rata5 = 0;
			}
		break;
		case 43:
			if($drzava == "Srbija"){
				$rata1 = 5900;
				$rata2 = 5900;
				$rata3 = 5900;
				$rata4 = 0;
				$rata5 = 0;
			}else{
				$rata1 = 95;
				$rata2 = 95;
				$rata3 = 95;
				$rata4 = 0;
				$rata5 = 0;
			}
		break;
		case 44:
			if($drzava == "Srbija"){
				$rata1 = 4425;
				$rata2 = 4425;
				$rata3 = 4425;
				$rata4 = 4425;
				$rata5 = 0;
			}else{
				$rata1 = 75;
				$rata2 = 70;
				$rata3 = 70;
				$rata4 = 70;
				$rata5 = 0;
			}
		break;
		case 45:
			if($drzava == "Srbija"){
				$rata1 = 3540;
				$rata2 = 3540;
				$rata3 = 3540;
				$rata4 = 3540;
				$rata5 = 3540;
			}else{
				$rata1 = 57;
				$rata2 = 57;
				$rata3 = 57;
				$rata4 = 57;
				$rata5 = 57;
			}
		break;
		case 26:
			if($drzava == "Srbija"){
				$rata1 = 0;
				$rata2 = 0;
				$rata3 = 0;
				$rata4 = 0;
				$rata5 = 0;
				$rata6 = 0;
			}else{
				$rata1 = 360;
				$rata2 = 360;
				$rata3 = 360;
				$rata4 = 360;
				$rata5 = 360;
				$rata6 = 360;
			}
		break;
		case 27:
			if($drzava == "Srbija"){
				$rata1 = 0;
				$rata2 = 0;
				$rata3 = 0;
				$rata4 = 0;
				$rata5 = 0;
				$rata6 = 0;
				$rata7 = 0;
				$rata8 = 0;
				$rata9 = 0;
				$rata10 = 0;
				$rata11 = 0;
				$rata12 = 0;
			}else{
				$rata1 = 180;
				$rata2 = 180;
				$rata3 = 180;
				$rata4 = 180;
				$rata5 = 180;
				$rata6 = 180;
				$rata7 = 180;
				$rata8 = 180;
				$rata9 = 180;
				$rata10 = 180;
				$rata11 = 180;
				$rata12 = 180;
			}
		break;
		case 28:
			if($drzava == "Srbija"){
				$rata1 = 0;
				$rata2 = 0;
				$rata3 = 0;
				$rata4 = 0;
				$rata5 = 0;
				$rata6 = 0;
			}else{
				$rata1 = 312;
				$rata2 = 312;
				$rata3 = 312;
				$rata4 = 312;
				$rata5 = 312;
				$rata6 = 312;
			}
		break;
		case 29:
			if($drzava == "Srbija"){
				$rata1 = 0;
				$rata2 = 0;
				$rata3 = 0;
				$rata4 = 0;
				$rata5 = 0;
				$rata6 = 0;
				$rata7 = 0;
				$rata8 = 0;
				$rata9 = 0;
				$rata10 = 0;
				$rata11 = 0;
				$rata12 = 0;
			}else{
				$rata1 = 156;
				$rata2 = 156;
				$rata3 = 156;
				$rata4 = 156;
				$rata5 = 156;
				$rata6 = 156;
				$rata7 = 156;
				$rata8 = 156;
				$rata9 = 156;
				$rata10 = 156;
				$rata11 = 156;
				$rata12 = 156;
			}
		break;
		default:
			$rata1 = 0;
			$rata2 = 0;
			$rata3 = 0;
			$rata4 = 0;
			$rata5 = 0;
			$rata6 = 0;
			$rata7 = 0;
			$rata8 = 0;
			$rata9 = 0;
			$rata10 = 0;
			$rata11 = 0;
			$rata12 = 0;
	}
	
	return  $$broj_rate;
}

function createBrojPredracuna($drzava){
	//chekirati i godinu !!!!!!!!!!!!!!!
	Global $db;
	$slovo = substr($drzava, 0, 1);
	$godina = date('y');
	$query_getLast = $db->prepare("
					SELECT pr_broj_predracuna, MAX(CONVERT(SUBSTRING_INDEX(SUBSTRING_INDEX(pr_broj_predracuna, '-', -2), '-',1), UNSIGNED INTEGER)) AS zadnjiBroj 
					FROM idk_predracuni 
					WHERE substring(pr_broj_predracuna, 5, 1) = :slovo 
					AND (SUBSTRING_INDEX(pr_broj_predracuna, '-', -1) = :godina)
					ORDER BY zadnjiBroj DESC");
	$query_getLast->execute(array(
					":slovo" => $slovo,
					":godina" => $godina
					));
	
	$row_last = $query_getLast->fetch();
	$zadnji_broj = $row_last['zadnjiBroj'];
	$novi_broj = $zadnji_broj + 1;
	
	return $novi_broj;
}

function createBrojUgovora($drzava){
	//chekirati i godinu !!!!!!!!!!!!!!!
	Global $db;
	$slovo = substr($drzava, 0, 1);
	$mjesec = date('m');
	$godina = date('y');
	$query_getLast = $db->prepare("
					SELECT naziv_dokument_nd,  MAX(CONVERT(SUBSTRING_INDEX(SUBSTRING_INDEX(naziv_dokument_nd, '-', -3), '-',1), UNSIGNED INTEGER)) AS zadnjiBroj 
					FROM idk_nd_kandidata_dokumenti 
					WHERE tip_dokumenta = 1 AND (SUBSTRING_INDEX(SUBSTRING_INDEX(naziv_dokument_nd, '-', -2),'-',1) = $mjesec) 
					AND (SUBSTRING_INDEX(naziv_dokument_nd, '-', -1) = $godina)
					AND (SUBSTR(naziv_dokument_nd, 3, 1) = '$slovo')
					");
	$query_getLast->execute(array(
					':mjesec' => $mjesec,
					':godina' => $godina,
					':slovo' => $slovo
	));
	
	$row_last = $query_getLast->fetch();
	$zadnji_broj = $row_last['zadnjiBroj'];
	$novi_broj = $zadnji_broj + 1;
	
	return $novi_broj;
}

function createBrojUgovoraSrbija(){
	//chekirati i godinu !!!!!!!!!!!!!!!
	Global $db;
	$mjesec = date('m');
	$godina = date('y');
	$query_getLast = $db->prepare("
					SELECT MAX(CONVERT(SUBSTRING_INDEX(SUBSTRING_INDEX(ug_broj, '-', -2), '-',1), UNSIGNED INTEGER)) AS zadnjiBroj
					FROM idk_nd_ugovori 
					WHERE ug_jezik = 'sr' AND (SUBSTRING_INDEX(SUBSTRING_INDEX(ug_broj, '-', -1),'/',1) = $mjesec) 
					AND (SUBSTRING_INDEX(ug_broj, '/', -1) = $godina)
					");
	$query_getLast->execute(array(
					':mjesec' => $mjesec,
					':godina' => $godina
	));
	
	$row_last = $query_getLast->fetch();
	$zadnji_broj = $row_last['zadnjiBroj'];
	$novi_broj = $zadnji_broj + 1;
	
	return $novi_broj;
}

function createBrojUplatnice($drzava){
	//chekirati i godinu !!!!!!!!!!!!!!!
	Global $db;
	$slovo = substr($drzava, 0, 1);
	$mjesec = date('m');
	$godina = date('y');
	$query_getLast = $db->prepare("
					SELECT naziv_dokument_nd,  MAX(CONVERT(SUBSTRING_INDEX(SUBSTRING_INDEX(naziv_dokument_nd, '-', -3), '-',1), UNSIGNED INTEGER)) AS zadnjiBroj 
					FROM idk_nd_kandidata_dokumenti 
					WHERE tip_dokumenta = 2 AND (SUBSTRING_INDEX(SUBSTRING_INDEX(naziv_dokument_nd, '-', -2),'-',1) = $mjesec) AND (SUBSTRING_INDEX(naziv_dokument_nd, '-', -1) = $godina) AND (SUBSTR(naziv_dokument_nd, 3, 1) = '$slovo')
					");
	$query_getLast->execute();
	
	$row_last = $query_getLast->fetch();
	$zadnji_broj = $row_last['zadnjiBroj'];
	
	$novi_broj = $zadnji_broj + 1;
	
	return $novi_broj;
}

function createBrojRacuna($drzava){
	//chekirati i godinu !!!!!!!!!!!!!!!
	Global $db;
	$slovo = substr($drzava, 0, 1);
	$godina = date('y');
	$query_getLast = $db->prepare("
					SELECT racun_broj, MAX(CONVERT(SUBSTRING_INDEX(SUBSTRING_INDEX(racun_broj, '-', -2), '-',1), UNSIGNED INTEGER)) AS zadnjiBroj 
					FROM idk_racuni 
					WHERE substring(racun_broj, 6, 1) = :slovo 
					AND (SUBSTRING_INDEX(racun_broj, '-', -1) = :godina)
					ORDER BY zadnjiBroj DESC");
	$query_getLast->execute(array(
					":slovo" => $slovo,
					":godina" => $godina
					));
	
	$row_last = $query_getLast->fetch();
	$zadnji_broj = $row_last['zadnjiBroj'];
	$novi_broj = $zadnji_broj + 1;
	
	return $novi_broj;
}

function sendMailPredracunUgovorBIH($predracun_putanja, $ugovor_putanja, $uplatnica_putanja, $kandidat_id){
	
	Global $db;
	Global $logged_employee_id;
	
	$query_mail = $db->prepare("
					SELECT email_nd_kandidata, vrsta_ugovora_nd_kandidata
					FROM idk_nd_kandidata
					WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata");

	$query_mail->execute(array(
					':id_broj_nd_kandidata' => $kandidat_id
	));

	$row_mail = $query_mail->fetch();
	$kandidat_mail = $row_mail['email_nd_kandidata'];
	$vrsta_ugovora = $row_mail['vrsta_ugovora_nd_kandidata'];
	
	if($vrsta_ugovora != 99){
		
		$signatura_bih = $_SERVER['DOCUMENT_ROOT']."/files/ugovori_uplatnice_dipl/signatura_bih.jpg";
		$filename_predracun =  $_SERVER['DOCUMENT_ROOT']."/files/predracuni_dipl/".$predracun_putanja;
		$filename_ugovor =  $_SERVER['DOCUMENT_ROOT']."/files/ugovori_uplatnice_dipl/".$ugovor_putanja;
		$filename_uplatnica =  $_SERVER['DOCUMENT_ROOT']."/files/ugovori_uplatnice_dipl/".$uplatnica_putanja;
		
		$mail = new PHPMailer;

		$mail->isSMTP();											// Set mailer to use SMTP

		$mail->Host = 'smtp.gmail.com';                 // Specify main and backup SMTP servers
		$mail->SMTPAuth = true;                         // Enable SMTP authentication
		$mail->Username = 'support@job-step.com';        // SMTP username
		$mail->Password = 'eooc nnxo aylp lqkh';          // SMTP password
		$mail->SMTPSecure = 'ssl';                      // Enable TLS encryption, `ssl` also accepted
		$mail->Port = 465;                              // TCP port to connect to
		$mail->CharSet = 'UTF-8';

		//Recipients
		$mail->setFrom('support@job-step.com', 'JobStep');
		
		$mail->addAddress($kandidat_mail);		// Add a recipient
		if($predracun_putanja != "ne"){
			$mail->AddAttachment($filename_predracun); 
			$txt_predracun = "<br><br>2.	Predračun";
			$head_predracun = "i predračun";
		}else{
			$txt_predracun = "";
			$head_predracun = "";
		}
		$mail->AddAttachment($filename_ugovor); 
		if($uplatnica_putanja != "ne"){
			$mail->AddAttachment($filename_uplatnica);
			$txt_uplatnica = "<br><br>3.	Primjer uplatnice";
		}else{
			$txt_uplatnica = "";
		}
		
		$mail->Subject = "Nostrifikacija: Ugovor ".$head_predracun."";
		$mail->Body    = "Poštovani,
							<br><br>kako bismo Vam olakšali prve korake procesa nostrifikacije Vaše diplome, kao što smo se dogovorili, u prilogu Vam šaljemo:
							<br><br>1.	Ugovor o nostrifikaciji
							".$txt_predracun."
							".$txt_uplatnica."

							<br><br>Molimo Vas da nas obavijestite o Vašoj uplati kako bismo u što kraćem roku poduzeli sljedeće korake, a Vama približili odlazak u Njemačku.

							<br><br>Ukoliko imate dodatnih pitanja, rado Vam stojimo na raspolaganju.
							<br><br>Vaš Jobstep Team
							<br><br>
							<img src='cid:logo_2u' width='100%'>
		";
		
		$mail->AddEmbeddedImage($signatura_bih, 'logo_2u');
		
		$mail->AltBody = "ALT";
		if(!$mail->send()) {
			echo $id.'-2 Message could not be sent. ';
			echo 'Mailer Error: ' . $mail->ErrorInfo;
			echo "<br/>";
			$razlog = $mail->ErrorInfo;
			
			$array .= '"'.$id.'"=>"2",';
			
			//ADD TO LOGS START
			$log_date = date('Y-m-d H:i:s');
			$log_desc = "DIPL -> Nije poslan predracun i ugovor = [".$kandidat_id."] . Razlog: ".$razlog;
			$log_query = $db->prepare("
							INSERT INTO idk_logs
								(log_employeeid, log_desc, log_date)
							VALUES
								(:log_employeeid, :log_desc, :log_date)");

			$log_query->execute(array(
							':log_employeeid' => $logged_employee_id,
							':log_desc' => $log_desc,
							':log_date' => $log_date));
			//ADD TO LOGS END
		}else{
			echo $id.'-1 Mail sent';
			echo 'Mailer Error: ' . $mail->ErrorInfo;
			echo "<br/>";
			
			
			//$array .= '"'.$id.'"=>"1",';
			
			//ADD TO LOGS START
			$log_date = date('Y-m-d H:i:s');
			$log_desc = "DIPL -> Poslan predracun i ugovor = [".$kandidat_id."] ";
			$log_query = $db->prepare("
							INSERT INTO idk_logs
								(log_employeeid, log_desc, log_date)
							VALUES
								(:log_employeeid, :log_desc, :log_date)");

			$log_query->execute(array(
							':log_employeeid' => $logged_employee_id,
							':log_desc' => $log_desc,
							':log_date' => $log_date));
			//ADD TO LOGS END

		}
	}
}

function sendMailPredracunBIH($predracun_putanja, $uplatnica_putanja, $kandidat_id){
	
	Global $db;
	Global $logged_employee_id;
	
	$query_mail = $db->prepare("
					SELECT email_nd_kandidata, vrsta_ugovora_nd_kandidata
					FROM idk_nd_kandidata
					WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata");

	$query_mail->execute(array(
					':id_broj_nd_kandidata' => $kandidat_id
	));

	$row_mail = $query_mail->fetch();
	$kandidat_mail = $row_mail['email_nd_kandidata'];
	$vrsta_ugovora = $row_mail['vrsta_ugovora_nd_kandidata'];
	
	if($vrsta_ugovora != 99 ){
		$signatura_bih = $_SERVER['DOCUMENT_ROOT']."/files/ugovori_uplatnice_dipl/signatura_bih.jpg";
		$filename_predracun =  $_SERVER['DOCUMENT_ROOT']."/files/predracuni_dipl/".$predracun_putanja;
		$filename_uplatnica =  $_SERVER['DOCUMENT_ROOT']."/files/ugovori_uplatnice_dipl/".$uplatnica_putanja;
		
		$mail = new PHPMailer;

		$mail->isSMTP();											// Set mailer to use SMTP

		$mail->Host = 'smtp.gmail.com';                 // Specify main and backup SMTP servers
		$mail->SMTPAuth = true;                         // Enable SMTP authentication
		$mail->Username = 'support@job-step.com';        // SMTP username
		$mail->Password = 'eooc nnxo aylp lqkh';          // SMTP password
		$mail->SMTPSecure = 'ssl';                      // Enable TLS encryption, `ssl` also accepted
		$mail->Port = 465;                              // TCP port to connect to
		$mail->CharSet = 'UTF-8';

		//Recipients
		$mail->setFrom('support@job-step.com', 'JobStep');
		
		$mail->addAddress($kandidat_mail);		// Add a recipient
		$mail->AddAttachment($filename_predracun); 
		$mail->AddAttachment($filename_uplatnica); 
		
		$mail->Subject = "Nostrifikacija: Ugovor i predračun";
		$mail->Body    = "Poštovani,
							<br><br>kako bismo Vam olakšali prve korake procesa nostrifikacije Vaše diplome, kao što smo se dogovorili, u prilogu Vam šaljemo:
							<br><br>1.	Predračun
							<br><br>2.	Primjer uplatnice

							<br><br>Molimo Vas da nas obavijestite o Vašoj uplati kako bismo u što kraćem roku poduzeli sljedeće korake, a Vama približili odlazak u Njemačku.

							<br><br>Ukoliko imate dodatnih pitanja, rado Vam stojimo na raspolaganju.
							<br><br>Vaš Jobstep Team
							<br><br>
							<img src='cid:logo_2u' width='100%'>
		";
		
		$mail->AddEmbeddedImage($signatura_bih, 'logo_2u');
		
		$mail->AltBody = "ALT";
		if(!$mail->send()) {
			echo $id.'-2 Message could not be sent. ';
			echo 'Mailer Error: ' . $mail->ErrorInfo;
			echo "<br/>";
			$razlog = $mail->ErrorInfo;
			$array .= '"'.$id.'"=>"2",';
			
			//ADD TO LOGS START
			$log_date = date('Y-m-d H:i:s');
			$log_desc = "DIPL -> Nije poslan predracun = [".$kandidat_id."] . Razlog: ".$razlog;
			$log_query = $db->prepare("
							INSERT INTO idk_logs
								(log_employeeid, log_desc, log_date)
							VALUES
								(:log_employeeid, :log_desc, :log_date)");

			$log_query->execute(array(
							':log_employeeid' => $logged_employee_id,
							':log_desc' => $log_desc,
							':log_date' => $log_date));
			//ADD TO LOGS END
		}else{
			echo $id.'-1 Mail sent';
			echo 'Mailer Error: ' . $mail->ErrorInfo;
			echo "<br/>";
			$razlog = $mail->ErrorInfo;
			
			//$array .= '"'.$id.'"=>"1",';
			
			//ADD TO LOGS START
			$log_date = date('Y-m-d H:i:s');
			$log_desc = "DIPL -> Poslan predracun = [".$kandidat_id."] ";
			$log_query = $db->prepare("
							INSERT INTO idk_logs
								(log_employeeid, log_desc, log_date)
							VALUES
								(:log_employeeid, :log_desc, :log_date)");

			$log_query->execute(array(
							':log_employeeid' => $logged_employee_id,
							':log_desc' => $log_desc,
							':log_date' => $log_date));
			//ADD TO LOGS END

		}
	}
}

function sendMailPredracunUgovorSRB($predracun_putanja, $ugovor_putanja, $uplatnica_putanja, $kandidat_id){
	
	Global $db;
	Global $logged_employee_id;
	
	$query_mail = $db->prepare("
					SELECT email_nd_kandidata, vrsta_ugovora_nd_kandidata
					FROM idk_nd_kandidata
					WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata");

	$query_mail->execute(array(
					':id_broj_nd_kandidata' => $kandidat_id
	));

	$row_mail = $query_mail->fetch();
	$kandidat_mail = $row_mail['email_nd_kandidata'];
	$vrsta_ugovora = $row_mail['vrsta_ugovora_nd_kandidata'];
	
	if($vrsta_ugovora != 99){
		
		$signatura_srb = $_SERVER['DOCUMENT_ROOT']."/files/ugovori_uplatnice_dipl/signatura_srb.jpg";
		$filename_predracun =  $_SERVER['DOCUMENT_ROOT']."/files/predracuni_dipl/".$predracun_putanja;
		$filename_ugovor =  $_SERVER['DOCUMENT_ROOT']."/files/ugovori_uplatnice_dipl/".$ugovor_putanja;
		$filename_uplatnica =  $_SERVER['DOCUMENT_ROOT']."/files/ugovori_uplatnice_dipl/".$uplatnica_putanja;
		$filename_infolist =  $_SERVER['DOCUMENT_ROOT']."/files/ugovori_uplatnice_dipl/infolist_rs.pdf";
		
		$mail = new PHPMailer;

		$mail->isSMTP();											// Set mailer to use SMTP

		$mail->Host = 'smtp.gmail.com';                 // Specify main and backup SMTP servers
		$mail->SMTPAuth = true;                         // Enable SMTP authentication
		$mail->Username = 'support@job-step.com';        // SMTP username
		$mail->Password = 'eooc nnxo aylp lqkh';          // SMTP password
		$mail->SMTPSecure = 'ssl';                      // Enable TLS encryption, `ssl` also accepted
		$mail->Port = 465;                              // TCP port to connect to
		$mail->CharSet = 'UTF-8';

		//Recipients
		$mail->setFrom('support@job-step.com', 'JobStep');
		
		$mail->addAddress($kandidat_mail);		// Add a recipient
		$mail->AddAttachment($filename_predracun); 
		$mail->AddAttachment($filename_ugovor); 
		$mail->AddAttachment($filename_uplatnica); 
		$mail->AddAttachment($filename_infolist); 
		
		$mail->Subject = "Nostrifikacija: Ugovor i predračun";
		$mail->Body    = "Poštovani,
							<br><br>po prethodnom dogovoru, a na osnovu Vašeg iskazanog interesovanja, želje i volje, u prilogu ovog mejla Vam dostavljam sledeću dokumentaciju, a na ime pružanja usluge posredovanja u postupku nostrifikacije diplome:
							<br>- Ugovor o pružanju usluge posredovanja u postupku nostrifikacije diplome,
							<br>- Informativni list,
							<br>- Predračun,
							<br>- primer ispunjenog naloga za uplatu.

							<br><br>Molim Vas da:

							<br><br>- dati ugovor odštampate u četiri primerka i potpišete onako kako se traži na mestu naznačenom za potpis korisnika usluge,
							<br>te dva primjerka pošaljete na našu adresu
							<br>- dati informativni list odštampate u dva primerka i potpišete onako kako se traži na mestu naznačenom za potpis korisnika usluge, 
							<br>te da prethodno navedenu i tako potpisanu dokumentaciju pošaljete / dostavite na adresu firme- podaci firme:
							<b><br>Jobstep International d.o.o.
							<br>Kneza Miloša 78
							<br>11000 Beograd- Savski Venac, Srbija.</b>
							
							<br><br>Uplatu novčanog iznosa vršite po osnovu ispostavljenog predračuna ( predračun nije neophodno štampati, punovažan je u elektronskom obliku).
							<br><br>Za sve dalje informacije, pitanja, dileme i nejasnoće budite slobodni da nas kontaktirate. Stojimo Vam na raspolaganju i radujemo se uspešnoj saradnji.
							
							<br><br>Sa poštovanjem,
							<br>Vaš Jobstep Team

							<br><br>
							<br><br>
							<img src='cid:logo_2u' width='100%'>
		";
		
		$mail->AddEmbeddedImage($signatura_srb, 'logo_2u');
		
		$mail->AltBody = "ALT";
		if(!$mail->send()) {
			echo $id.'-2 Message could not be sent. ';
			echo 'Mailer Error: ' . $mail->ErrorInfo;
			echo "<br/>";
			$razlog = $mail->ErrorInfo;
			
			$array .= '"'.$id.'"=>"2",';
			
			//ADD TO LOGS START
			$log_date = date('Y-m-d H:i:s');
			$log_desc = "DIPL -> Nije poslan predracun i ugovor = [".$kandidat_id."] . Razlog: ".$razlog;
			$log_query = $db->prepare("
							INSERT INTO idk_logs
								(log_employeeid, log_desc, log_date)
							VALUES
								(:log_employeeid, :log_desc, :log_date)");

			$log_query->execute(array(
							':log_employeeid' => $logged_employee_id,
							':log_desc' => $log_desc,
							':log_date' => $log_date));
			//ADD TO LOGS END
		}else{
			echo $id.'-1 Mail sent';
			echo 'Mailer Error: ' . $mail->ErrorInfo;
			echo "<br/>";
			
			
			//$array .= '"'.$id.'"=>"1",';
			
			//ADD TO LOGS START
			$log_date = date('Y-m-d H:i:s');
			$log_desc = "DIPL -> Poslan predracun i ugovor = [".$kandidat_id."] ";
			$log_query = $db->prepare("
							INSERT INTO idk_logs
								(log_employeeid, log_desc, log_date)
							VALUES
								(:log_employeeid, :log_desc, :log_date)");

			$log_query->execute(array(
							':log_employeeid' => $logged_employee_id,
							':log_desc' => $log_desc,
							':log_date' => $log_date));
			//ADD TO LOGS END

		}
	}
}

function sendMailPredracunSRB($predracun_putanja, $uplatnica_putanja, $kandidat_id){
	
	Global $db;
	Global $logged_employee_id;
	
	$query_mail = $db->prepare("
					SELECT email_nd_kandidata, vrsta_ugovora_nd_kandidata
					FROM idk_nd_kandidata
					WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata");

	$query_mail->execute(array(
					':id_broj_nd_kandidata' => $kandidat_id
	));

	$row_mail = $query_mail->fetch();
	$kandidat_mail = $row_mail['email_nd_kandidata'];
	$vrsta_ugovora = $row_mail['vrsta_ugovora_nd_kandidata'];
	
	if($vrsta_ugovora != 99){
		
		$signatura_srb = $_SERVER['DOCUMENT_ROOT']."/files/ugovori_uplatnice_dipl/signatura_srb.jpg";
		$filename_predracun =  $_SERVER['DOCUMENT_ROOT']."/files/predracuni_dipl/".$predracun_putanja;
		$filename_uplatnica =  $_SERVER['DOCUMENT_ROOT']."/files/ugovori_uplatnice_dipl/".$uplatnica_putanja;
		
		$mail = new PHPMailer;

		$mail->isSMTP();											// Set mailer to use SMTP

		$mail->Host = 'smtp.gmail.com';                 // Specify main and backup SMTP servers
		$mail->SMTPAuth = true;                         // Enable SMTP authentication
		$mail->Username = 'support@job-step.com';        // SMTP username
		$mail->Password = 'eooc nnxo aylp lqkh';          // SMTP password
		$mail->SMTPSecure = 'ssl';                      // Enable TLS encryption, `ssl` also accepted
		$mail->Port = 465;                              // TCP port to connect to
		$mail->CharSet = 'UTF-8';

		//Recipients
		$mail->setFrom('support@job-step.com', 'JobStep');
		
		$mail->addAddress($kandidat_mail);		// Add a recipient
		$mail->AddAttachment($filename_predracun); 
		$mail->AddAttachment($filename_uplatnica); 
		
		$mail->Subject = "Nostrifikacija: Ugovor i predračun";
		$mail->Body    = "Poštovani,
							<br><br>po prethodnom dogovoru, a na osnovu Vašeg iskazanog interesovanja, želje i volje, u prilogu ovog mejla Vam dostavljam sledeću dokumentaciju, a na ime pružanja usluge posredovanja u postupku nostrifikacije diplome:
							<br>- Predračun,
							<br>- primer ispunjenog naloga za uplatu.

							<br><br>Molim Vas da:
							
							<br><br>Uplatu novčanog iznosa vršite po osnovu ispostavljenog predračuna ( predračun nije neophodno štampati, punovažan je u elektronskom obliku).
							<br><br>Za sve dalje informacije, pitanja, dileme i nejasnoće budite slobodni da nas kontaktirate. Stojimo Vam na raspolaganju i radujemo se uspešnoj saradnji.
							
							<br><br>Sa poštovanjem,
							<br>Vaš Jobstep Team

							<br><br>
							<br><br>
							<img src='cid:logo_2u' width='100%'>
		";
		
		$mail->AddEmbeddedImage($signatura_srb, 'logo_2u');
		
		$mail->AltBody = "ALT";
		if(!$mail->send()) {
			echo $id.'-2 Message could not be sent. ';
			echo 'Mailer Error: ' . $mail->ErrorInfo;
			echo "<br/>";
			
			$array .= '"'.$id.'"=>"2",';
			
			//ADD TO LOGS START
			$log_date = date('Y-m-d H:i:s');
			$log_desc = "DIPL -> Nije poslan predracun  = [".$kandidat_id."] . Razlog: ".$razlog;
			$log_query = $db->prepare("
							INSERT INTO idk_logs
								(log_employeeid, log_desc, log_date)
							VALUES
								(:log_employeeid, :log_desc, :log_date)");

			$log_query->execute(array(
							':log_employeeid' => $logged_employee_id,
							':log_desc' => $log_desc,
							':log_date' => $log_date));
			//ADD TO LOGS END
		}else{
			echo $id.'-1 Mail sent';
			echo 'Mailer Error: ' . $mail->ErrorInfo;
			echo "<br/>";
			$razlog = $mail->ErrorInfo;
			
			//$array .= '"'.$id.'"=>"1",';
			
			//ADD TO LOGS START
			$log_date = date('Y-m-d H:i:s');
			$log_desc = "DIPL -> Poslan predracun  = [".$kandidat_id."] ";
			$log_query = $db->prepare("
							INSERT INTO idk_logs
								(log_employeeid, log_desc, log_date)
							VALUES
								(:log_employeeid, :log_desc, :log_date)");

			$log_query->execute(array(
							':log_employeeid' => $logged_employee_id,
							':log_desc' => $log_desc,
							':log_date' => $log_date));
			//ADD TO LOGS END

		}
	}
}

function sendMailRacun($putanja){
	//send mail curama iz financija
	$mail = new PHPMailer;
	$mail->isSMTP();
	$filename_racun =  $_SERVER['DOCUMENT_ROOT']."/files/racuni_dipl/".$putanja;
	// try {
		$mail->Host = 'smtp.gmail.com';                 // Specify main and backup SMTP servers
		$mail->SMTPAuth = true;                         // Enable SMTP authentication
		$mail->Username = 'support@job-step.com';        // SMTP username
		$mail->Password = 'eooc nnxo aylp lqkh';          // SMTP password
		$mail->SMTPSecure = 'ssl';                      // Enable TLS encryption, `ssl` also accepted
		$mail->Port = 465;                              // TCP port to connect to
		$mail->CharSet = 'UTF-8';

		//Recipients
		$mail->setFrom('support@job-step.com', 'JobStep');

		$mail->addAddress('dz.komic@job-step.com');		// Add a recipient
		$mail->AddAttachment($filename_racun); 
		
		$mail->Subject ="Racun";
		$mail->Body    = "U prilogu je racun.";
		$mail->AltBody = "";
		$mail->send();
		// if(!$mail->send()) {
			// echo 'Message could not be sent.';
			// echo 'Mailer Error: ' . $mail->ErrorInfo;
		// }else{
			// // echo "Mail sent";
		// }
	// } catch (phpmailerException $e) {
		// echo $e->errorMessage(); //Pretty error messages from PHPMailer
	// }
}

function getTeamName($id_t){
	Global $db;
	
	$query_get = $db->prepare("
					SELECT naziv_t
					FROM idk_timovi
					WHERE id_t = :id_t");
	$query_get->execute(array(
					":id_t" => $id_t
					));
	
	$row = $query_get->fetch();
	$naziv_t = $row['naziv_t'];
	
	return $naziv_t;
}

function getTeamNameByEmployeeId($id_e){
	Global $db;
	
	$query_get = $db->prepare("
					SELECT naziv_t FROM idk_timovi
					JOIN idk_employees emp ON id_t = emp.employee_team
					WHERE employee_id = :employee_id");
	$query_get->execute(array(
					":employee_id" => $id_e
					));
	
	$row = $query_get->fetch();
	$naziv_t = $row['naziv_t'];
	
	return $naziv_t;
}

function getLoggedEmployeeTeam(){
	Global $db;
	Global $logged_employee_id;
	
	$query_get = $db->prepare("
					SELECT employee_team
					FROM idk_employees
					WHERE employee_id = :employee_id");
	$query_get->execute(array(
					":employee_id" => $logged_employee_id
					));
	
	$row = $query_get->fetch();
	$employee_team = $row['employee_team'];
	
	return $employee_team;
}

function getIdOfEmployeeTeam($team){

	Global $db;

	$ids = array();

	$query = $db->prepare("
					SELECT employee_id
					FROM idk_employees
					WHERE employee_status != :employee_status AND employee_team = :employee_team");

	$query->execute(array(':employee_status' => 0, ':employee_team' => $team));

	while($row = $query->fetch()){
		array_push($ids, $row['employee_id']);
	}
	return $ids;
}

function getTeamIdByEmployee($id_novi_zaposlenik){
		
	Global $db;
	
	if($id_novi_zaposlenik != 139){
		$query = $db->prepare("
			SELECT employee_team
			FROM idk_employees
			WHERE employee_id = :employee_id
		");
		$query->execute(array(
			':employee_id' => $id_novi_zaposlenik
		));
		$row = $query->fetch();
		return intval($row["employee_team"]);
	}else{
		return 0;
	}
}

function ubaciObracune($predracun_id){
	Global $db;
	
	//get datum kreiranja obracuna
	$query_date = $db->prepare("SELECT pr_datum_kreiranja, pr_rata, pr_kandidat_id, pr_domaca_valuta, pr_zaposlenik FROM idk_predracuni WHERE pr_id = $predracun_id");
	$query_date->execute();
	$row_date = $query_date->fetch();
	$datum_kreiranja = $row_date['pr_datum_kreiranja'];
	$pr_rata = $row_date['pr_rata'];
	$pr_kandidat_id = $row_date['pr_kandidat_id'];
	$valuta = $row_date['pr_domaca_valuta'];
	$agent_id = $row_date['pr_zaposlenik'];
	$vrsta_ugovora = getVrstaUgovora($pr_kandidat_id);
	$rata = $pr_rata;
	
	echo "pr_id: ".$predracun_id." --- vrsta_ug: ".$vrsta_ugovora." --- valuta: ".$valuta." --- agent: ".$agent_id." --- datum_kreiranja: ".$datum_kreiranja."<br/>";
	//Ako je u pitanju predracun broj 2,3,4,5 onda gledati kad je napravljen prvi, ako je prije 2021 onda nista ne raditi
	if($pr_rata != 1){
		$query_first_pr = $db->prepare("SELECT pr_datum_kreiranja, pr_zaposlenik FROM idk_predracuni WHERE pr_kandidat_id = $pr_kandidat_id AND pr_rata = 1 AND pr_status != 0");
		$query_first_pr->execute();
		$row_first_pr = $query_first_pr->fetch();
		$datum_kreiranja_prvog_pr = $row_first_pr['pr_datum_kreiranja'];
		$agent_id = $row_first_pr['pr_zaposlenik'];
		$new_year = "2021-01-01 00:00:00";
		$novi_datum = "2021-02-06 00:00:00";
		//var_dump($datum_kreiranja_prvog_pr);
		if($datum_kreiranja_prvog_pr > $new_year AND $datum_kreiranja_prvog_pr < $novi_datum){
			//IDE UBACIVANJE OBRACUNA PO STAROM SISTEMU
			// var_dump("usao u if");
			// exit();
			//get tim id
			$query_tim = $db->prepare("SELECT employee_team FROM idk_employees WHERE employee_id = $agent_id");
			$query_tim->execute();
			$row_tim = $query_tim->fetch();
			$tim_id = $row_tim['employee_team'];
			
			switch($vrsta_ugovora){
				case 1: case 2: case 22: case 52: case 62: case 72: case 82: case 42:
					$broj_rata = 2;
				break;
				case 5: case 6: case 23: case 53: case 63: case 73: case 83: case 43:
					$broj_rata = 3;
				break;
				case 7: case 8: case 24: case 54: case 64: case 74: case 84: case 44:
					$broj_rata = 4;
				break;
				case 3: case 4: case 25: case 55: case 65: case 75: case 85: case 45:
					$broj_rata = 5;
				break;
				default:
				$broj_rata = 1;
			}
			
			//Za Currency exchange date
			$month = date('m');
			$day = date('d');
			$year = date('Y');
			
			$tipovi = $db->prepare("
							SELECT tp_iznos, tp_iznos_rs, tp_tip, tp_id
							FROM idk_tipovi_provizija
							WHERE tp_status = 1 AND (tp_tim = :tp_tim OR tp_tim = 0) AND tp_tim != 3
							");

			$tipovi->execute(array(
							":tp_tim" => $tim_id
			));
			
			while($tip_row = $tipovi->fetch()){
				$tp_iznos = $tip_row["tp_iznos"];
				$tp_iznos_rs = $tip_row["tp_iznos_rs"];
				$tp_tip = $tip_row["tp_tip"];
				$tp_id = $tip_row["tp_id"];
				if($valuta == "BAM" or $valuta == "EUR"){
					$vrijednost_tipa = $tp_iznos/$broj_rata;
				}elseif($valuta == "RSD"){
					$vrijednost_tipa = $tp_iznos_rs/$broj_rata;
				}
				$vrijednost_tipa_f = number_format((float)$vrijednost_tipa, 4, '.', '');
				
				$kategorije = $db->prepare("
								SELECT kp_iznos, kp_iznos_rs, kp_odjeli, kp_vrsta, kp_employee_id
								FROM idk_kategorije_provizija
								WHERE kp_status = 1 AND kp_tip_id = :kp_tip_id
								");

				$kategorije->execute(array(
								':kp_tip_id' => $tp_id
				));

				while($kategorija_row = $kategorije->fetch()){
					
					$kp_iznos = $kategorija_row["kp_iznos"];
					$kp_iznos_rs = $kategorija_row["kp_iznos_rs"];
					$kp_odjeli = $kategorija_row["kp_odjeli"];
					$kp_vrsta = $kategorija_row["kp_vrsta"];
					$kp_employee_id = $kategorija_row["kp_employee_id"];
					$kp_odjeli = $kategorija_row['kp_odjeli'];
					$odjeli = explode(",", $kp_odjeli);
					
					if($valuta == "BAM" or $valuta == "EUR"){
						$vrijednost_kategorije = $kp_iznos/$broj_rata;
						$faktor_nula = 'AND employee_faktor_provizije_bih > 0';
					}elseif($valuta == "RSD"){
						$vrijednost_kategorije = $kp_iznos_rs/$broj_rata;
						$faktor_nula = 'AND employee_faktor_provizije_srb > 0';
					}
					$vrijednost_kategorije_f = number_format((float)$vrijednost_kategorije, 4, '.', '');
					
					//GET SUMU FAKTORA ZA TU KATEGORIJU
					$faktor_query = $db->prepare("
										SELECT SUM(employee_faktor_provizije_bih) as suma_bih, SUM(employee_faktor_provizije_srb) as suma_srb
										FROM idk_employees
										WHERE employee_odjel IN ($kp_odjeli) AND employee_status != 0 ");

					$faktor_query->execute();
					$faktor_row = $faktor_query->fetch();
					$suma_faktora_bih = $faktor_row['suma_bih'];
					$suma_faktora_srb = $faktor_row['suma_srb'];
					
					if($kp_vrsta == 1){
						$employee_id = $agent_id;
						if($valuta == "BAM" or $valuta == "EUR"){
							$valuta_provizije = "BAM";
							$provizija_zaposlenika_bih = $kp_iznos/$broj_rata;
							$ch = curl_init();
							// Disable SSL verification
							curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
							// Will return the response, if false it print the response
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							// Set the url
							$rls="https://www.cbbh.ba/CurrencyExchange/GetJson?date=".$month."%2F".$day."%2F".$year."%2000%3A00%3A00";
							curl_setopt($ch, CURLOPT_URL,$rls);
							// Execute
							$result=curl_exec($ch);
							curl_close($ch);

							$data = json_decode($result, TRUE);
							$EUR1 = $data['CurrencyExchangeItems'][0]['Middle'];
							$EUR2 = $data['CurrencyExchangeItems'][0]['Units'];
							$RSD1 = $data['CurrencyExchangeItems'][15]['Middle'];
							$RSD2 = $data['CurrencyExchangeItems'][15]['Units'];
							
							$EUR = str_replace(',', '.', $EUR1);
							$RSD = str_replace(',', '.', $RSD1);
							
							$provizija_zaposlenika_eur = $provizija_zaposlenika_bih / $EUR;
							$provizija_zaposlenika_srb = 100*$provizija_zaposlenika_bih / $RSD;
						}elseif($valuta == "RSD"){
							$valuta_provizije = "RSD";
							$provizija_zaposlenika_srb = $kp_iznos_rs/$broj_rata;
							$ch = curl_init();
							// Disable SSL verification
							curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
							// Will return the response, if false it print the response
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							// Set the url
							$rls="https://www.cbbh.ba/CurrencyExchange/GetJson?date=".$month."%2F".$day."%2F".$year."%2000%3A00%3A00";
							// var_dump($rls);
							// exit();
							curl_setopt($ch, CURLOPT_URL,$rls);
							curl_setopt($ch, CURLOPT_FAILONERROR, true); // Required for HTTP error codes to be reported via our call to curl_error($ch)
							// Execute
							$result=curl_exec($ch);
							
							curl_close($ch);

							$data = json_decode($result, TRUE);
							$EUR1 = $data['CurrencyExchangeItems'][0]['Middle'];
							$EUR2 = $data['CurrencyExchangeItems'][0]['Units'];
							$RSD1 = $data['CurrencyExchangeItems'][15]['Middle'];
							$RSD2 = $data['CurrencyExchangeItems'][15]['Units'];
							
							$EUR = str_replace(',', '.', $EUR1);
							$RSD = str_replace(',', '.', $RSD1);
							
							$provizija_zaposlenika_bih = ($provizija_zaposlenika_srb / 100) * $RSD ;
							$provizija_zaposlenika_eur = $provizija_zaposlenika_bih / $EUR;
						}
						$prov_bam_f = number_format((float)$provizija_zaposlenika_bih, 4, '.', '');
						$prov_eur_f = number_format((float)$provizija_zaposlenika_eur, 4, '.', '');
						$rprov_rsd_f = number_format((float)$provizija_zaposlenika_srb, 4, '.', '');
						
						echo $employee_id."---".$predracun_id."---".$valuta."---".$vrijednost_tipa_f."---".$vrijednost_kategorije_f."---".$prov_bam_f."---".$prov_eur_f."---".$rprov_rsd_f."<br/>";
						
						//INSERT
						$insert_obracun = $db->prepare("	
									INSERT INTO idk_obracuni	
									(employee_id,  predracun_id, valuta, rata, broj_rata, vrijednost_tipa, vrijednost_kategorije, iznos_obracuna_bam, iznos_obracuna_rsd, iznos_obracuna_eur, vrijeme_kreiranja, tip_provizije)	
									VALUES	
									(:employee_id,:predracun_id,:valuta,:rata,:broj_rata,:vrijednost_tipa,:vrijednost_kategorije,:iznos_obracuna_bam,:iznos_obracuna_rsd,:iznos_obracuna_eur,:vrijeme_kreiranja,:tip_provizije)	
									");	
						$insert_obracun->execute(array(	
									':employee_id' => $employee_id,	
									':predracun_id' => $predracun_id,	
									':valuta' => $valuta_provizije,	
									':rata' => $rata,	
									':broj_rata' => $broj_rata,	
									':vrijednost_tipa' => $vrijednost_tipa_f,	
									':vrijednost_kategorije' => $vrijednost_kategorije_f,	
									':iznos_obracuna_bam' => $prov_bam_f,	
									':iznos_obracuna_rsd' => $rprov_rsd_f,	
									':iznos_obracuna_eur' => $prov_eur_f,
									':vrijeme_kreiranja' => $datum_kreiranja,
									':tip_provizije' => $tp_id
									));
						
						
					}elseif($kp_vrsta == 2){
						foreach($odjeli as $odjel_id){
							$odjeli_query = $db->prepare("
												SELECT employee_id, employee_faktor_provizije_bih, employee_faktor_provizije_srb
												FROM idk_employees
												WHERE employee_odjel = :employee_odjel AND employee_status != 0 $faktor_nula
												ORDER BY employee_id ");

							$odjeli_query->execute(array(
												":employee_odjel" => $odjel_id
							));
							
							while($employee_row = $odjeli_query->fetch()){

								$employee_id = $employee_row['employee_id'];
								$faktor_bih = $employee_row['employee_faktor_provizije_bih'];
								$faktor_srb = $employee_row['employee_faktor_provizije_srb'];
								
								if($valuta == "BAM" or $valuta == "EUR"){
									$valuta_provizije = "BAM";
									$provizija_zaposlenika_bih = (($kp_iznos * $faktor_bih)/$suma_faktora_bih)/$broj_rata;
									$ch = curl_init();
									// Disable SSL verification
									curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
									// Will return the response, if false it print the response
									curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
									// Set the url
									$rls="https://www.cbbh.ba/CurrencyExchange/GetJson?date=".$month."%2F".$day."%2F".$year."%2000%3A00%3A00";
									curl_setopt($ch, CURLOPT_URL,$rls);
									// Execute
									$result=curl_exec($ch);
									curl_close($ch);

									$data = json_decode($result, TRUE);
									$EUR1 = $data['CurrencyExchangeItems'][0]['Middle'];
									$EUR2 = $data['CurrencyExchangeItems'][0]['Units'];
									$RSD1 = $data['CurrencyExchangeItems'][15]['Middle'];
									$RSD2 = $data['CurrencyExchangeItems'][15]['Units'];
									
									$EUR = str_replace(',', '.', $EUR1);
									$RSD = str_replace(',', '.', $RSD1);
									
									$provizija_zaposlenika_eur = $provizija_zaposlenika_bih / $EUR;
									$provizija_zaposlenika_srb = 100*$provizija_zaposlenika_bih / $RSD;
									
									
								}elseif($valuta == "RSD"){
									$valuta_provizije = "RSD";
									$provizija_zaposlenika_srb = (($kp_iznos_rs * $faktor_srb)/$suma_faktora_srb)/$broj_rata;
									$ch = curl_init();
									// Disable SSL verification
									curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
									// Will return the response, if false it print the response
									curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
									// Set the url
									$rls="https://www.cbbh.ba/CurrencyExchange/GetJson?date=".$month."%2F".$day."%2F".$year."%2000%3A00%3A00";
									// var_dump($rls);
									// exit();
									curl_setopt($ch, CURLOPT_URL,$rls);
									curl_setopt($ch, CURLOPT_FAILONERROR, true); // Required for HTTP error codes to be reported via our call to curl_error($ch)
									// Execute
									$result=curl_exec($ch);
									
									curl_close($ch);

									$data = json_decode($result, TRUE);
									$EUR1 = $data['CurrencyExchangeItems'][0]['Middle'];
									$EUR2 = $data['CurrencyExchangeItems'][0]['Units'];
									$RSD1 = $data['CurrencyExchangeItems'][15]['Middle'];
									$RSD2 = $data['CurrencyExchangeItems'][15]['Units'];
									
									$EUR = str_replace(',', '.', $EUR1);
									$RSD = str_replace(',', '.', $RSD1);
									
									$provizija_zaposlenika_bih = ($provizija_zaposlenika_srb / 100) * $RSD ;
									$provizija_zaposlenika_eur = $provizija_zaposlenika_bih / $EUR;
								}
								$prov_bam_f = number_format((float)$provizija_zaposlenika_bih, 4, '.', '');
								$prov_eur_f = number_format((float)$provizija_zaposlenika_eur, 4, '.', '');
								$rprov_rsd_f = number_format((float)$provizija_zaposlenika_srb, 4, '.', '');
								
								echo $employee_id."---".$predracun_id."---".$valuta."---".$vrijednost_tipa_f."---".$vrijednost_kategorije_f."---".$prov_bam_f."---".$prov_eur_f."---".$rprov_rsd_f."<br/>";
								//INSERT
								$insert_obracun = $db->prepare("	
											INSERT INTO idk_obracuni	
											(employee_id,  predracun_id, valuta, rata, broj_rata, vrijednost_tipa, vrijednost_kategorije, iznos_obracuna_bam, iznos_obracuna_rsd, iznos_obracuna_eur, vrijeme_kreiranja, tip_provizije)	
											VALUES	
											(:employee_id,:predracun_id,:valuta,:rata,:broj_rata,:vrijednost_tipa,:vrijednost_kategorije,:iznos_obracuna_bam,:iznos_obracuna_rsd,:iznos_obracuna_eur,:vrijeme_kreiranja,:tip_provizije)	
											");	
								$insert_obracun->execute(array(	
											':employee_id' => $employee_id,	
											':predracun_id' => $predracun_id,	
											':valuta' => $valuta_provizije,	
											':rata' => $rata,	
											':broj_rata' => $broj_rata,	
											':vrijednost_tipa' => $vrijednost_tipa_f,	
											':vrijednost_kategorije' => $vrijednost_kategorije_f,	
											':iznos_obracuna_bam' => $prov_bam_f,	
											':iznos_obracuna_rsd' => $rprov_rsd_f,	
											':iznos_obracuna_eur' => $prov_eur_f,
											':vrijeme_kreiranja' => $datum_kreiranja,
											':tip_provizije' => $tp_id
											));
							}
						}
						
					}elseif($kp_vrsta == 3){
						
						$employee_id = $kp_employee_id;
						if($valuta == "BAM" or $valuta == "EUR"){
							$valuta_provizije = "BAM";
							$provizija_zaposlenika_bih = $kp_iznos/$broj_rata;
							$ch = curl_init();
							// Disable SSL verification
							curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
							// Will return the response, if false it print the response
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							// Set the url
							$rls="https://www.cbbh.ba/CurrencyExchange/GetJson?date=".$month."%2F".$day."%2F".$year."%2000%3A00%3A00";
							curl_setopt($ch, CURLOPT_URL,$rls);
							// Execute
							$result=curl_exec($ch);
							curl_close($ch);

							$data = json_decode($result, TRUE);
							$EUR1 = $data['CurrencyExchangeItems'][0]['Middle'];
							$EUR2 = $data['CurrencyExchangeItems'][0]['Units'];
							$RSD1 = $data['CurrencyExchangeItems'][15]['Middle'];
							$RSD2 = $data['CurrencyExchangeItems'][15]['Units'];
							
							$EUR = str_replace(',', '.', $EUR1);
							$RSD = str_replace(',', '.', $RSD1);
							
							$provizija_zaposlenika_eur = $provizija_zaposlenika_bih / $EUR;
							$provizija_zaposlenika_srb = 100*$provizija_zaposlenika_bih / $RSD;
						}elseif($valuta == "RSD"){
							$valuta_provizije = "RSD";
							$provizija_zaposlenika_srb = $kp_iznos_rs/$broj_rata;
							$ch = curl_init();
							// Disable SSL verification
							curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
							// Will return the response, if false it print the response
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							// Set the url
							$rls="https://www.cbbh.ba/CurrencyExchange/GetJson?date=".$month."%2F".$day."%2F".$year."%2000%3A00%3A00";
							// var_dump($rls);
							// exit();
							curl_setopt($ch, CURLOPT_URL,$rls);
							curl_setopt($ch, CURLOPT_FAILONERROR, true); // Required for HTTP error codes to be reported via our call to curl_error($ch)
							// Execute
							$result=curl_exec($ch);
							
							curl_close($ch);

							$data = json_decode($result, TRUE);
							$EUR1 = $data['CurrencyExchangeItems'][0]['Middle'];
							$EUR2 = $data['CurrencyExchangeItems'][0]['Units'];
							$RSD1 = $data['CurrencyExchangeItems'][15]['Middle'];
							$RSD2 = $data['CurrencyExchangeItems'][15]['Units'];
							
							$EUR = str_replace(',', '.', $EUR1);
							$RSD = str_replace(',', '.', $RSD1);
							
							$provizija_zaposlenika_bih = ($provizija_zaposlenika_srb / 100) * $RSD ;
							$provizija_zaposlenika_eur = $provizija_zaposlenika_bih / $EUR;
						}
						$prov_bam_f = number_format((float)$provizija_zaposlenika_bih, 4, '.', '');
						$prov_eur_f = number_format((float)$provizija_zaposlenika_eur, 4, '.', '');
						$rprov_rsd_f = number_format((float)$provizija_zaposlenika_srb, 4, '.', '');
						
						echo $employee_id."---".$predracun_id."---".$valuta."---".$vrijednost_tipa_f."---".$vrijednost_kategorije_f."---".$prov_bam_f."---".$prov_eur_f."---".$rprov_rsd_f."<br/>";
						//INSERT
						$insert_obracun = $db->prepare("	
									INSERT INTO idk_obracuni	
									(employee_id,  predracun_id, valuta, rata, broj_rata, vrijednost_tipa, vrijednost_kategorije, iznos_obracuna_bam, iznos_obracuna_rsd, iznos_obracuna_eur, vrijeme_kreiranja, tip_provizije)	
									VALUES	
									(:employee_id,:predracun_id,:valuta,:rata,:broj_rata,:vrijednost_tipa,:vrijednost_kategorije,:iznos_obracuna_bam,:iznos_obracuna_rsd,:iznos_obracuna_eur,:vrijeme_kreiranja,:tip_provizije)	
									");	
						$insert_obracun->execute(array(	
									':employee_id' => $employee_id,	
									':predracun_id' => $predracun_id,	
									':valuta' => $valuta_provizije,	
									':rata' => $rata,	
									':broj_rata' => $broj_rata,	
									':vrijednost_tipa' => $vrijednost_tipa_f,	
									':vrijednost_kategorije' => $vrijednost_kategorije_f,	
									':iznos_obracuna_bam' => $prov_bam_f,	
									':iznos_obracuna_rsd' => $rprov_rsd_f,	
									':iznos_obracuna_eur' => $prov_eur_f,
									':vrijeme_kreiranja' => $datum_kreiranja,
									':tip_provizije' => $tp_id
									));
					}elseif($kp_vrsta == 4){
						//SUMA FAKTORA ZA PROVIZIJE OD TIMOVA
						$faktor_tim_query = $db->prepare("
											SELECT SUM(employee_faktor_za_timove) as suma_tim
											FROM idk_employees
											WHERE employee_status != 0 ");

						$faktor_tim_query->execute();
						$faktor_tim_row = $faktor_tim_query->fetch();
						$suma_faktora_tim = $faktor_tim_row['suma_tim'];
						
						$emp_query = $db->prepare("
											SELECT employee_id, employee_firstname, employee_lastname, employee_faktor_za_timove, employee_odjel
											FROM idk_employees
											WHERE employee_status != 0 AND employee_team = 1 AND employee_faktor_za_timove > 0
											ORDER BY employee_odjel,employee_id ");

						$emp_query->execute();
						
						while($emp_row = $emp_query->fetch()){

							$employee_id = $emp_row['employee_id'];
							$employee_faktor_za_timove = $emp_row['employee_faktor_za_timove'];
							
							if($valuta == "BAM" or $valuta == "EUR"){
								$valuta_provizije = "BAM";
								$provizija_zaposlenika_bih = (($kp_iznos * $employee_faktor_za_timove)/$suma_faktora_tim)/$broj_rata;
								$ch = curl_init();
								// Disable SSL verification
								curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
								// Will return the response, if false it print the response
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
								// Set the url
								$rls="https://www.cbbh.ba/CurrencyExchange/GetJson?date=".$month."%2F".$day."%2F".$year."%2000%3A00%3A00";
								curl_setopt($ch, CURLOPT_URL,$rls);
								// Execute
								$result=curl_exec($ch);
								curl_close($ch);

								$data = json_decode($result, TRUE);
								$EUR1 = $data['CurrencyExchangeItems'][0]['Middle'];
								$EUR2 = $data['CurrencyExchangeItems'][0]['Units'];
								$RSD1 = $data['CurrencyExchangeItems'][15]['Middle'];
								$RSD2 = $data['CurrencyExchangeItems'][15]['Units'];
								
								$EUR = str_replace(',', '.', $EUR1);
								$RSD = str_replace(',', '.', $RSD1);
								
								$provizija_zaposlenika_eur = $provizija_zaposlenika_bih / $EUR;
								$provizija_zaposlenika_srb = 100*$provizija_zaposlenika_bih / $RSD;
								
								
							}elseif($valuta == "RSD"){
								$valuta_provizije = "RSD";
								$provizija_zaposlenika_srb = (($kp_iznos_rs * $employee_faktor_za_timove)/$suma_faktora_tim)/$broj_rata;
								$ch = curl_init();
								// Disable SSL verification
								curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
								// Will return the response, if false it print the response
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
								// Set the url
								$rls="https://www.cbbh.ba/CurrencyExchange/GetJson?date=".$month."%2F".$day."%2F".$year."%2000%3A00%3A00";
								// var_dump($rls);
								// exit();
								curl_setopt($ch, CURLOPT_URL,$rls);
								curl_setopt($ch, CURLOPT_FAILONERROR, true); // Required for HTTP error codes to be reported via our call to curl_error($ch)
								// Execute
								$result=curl_exec($ch);
								
								curl_close($ch);

								$data = json_decode($result, TRUE);
								$EUR1 = $data['CurrencyExchangeItems'][0]['Middle'];
								$EUR2 = $data['CurrencyExchangeItems'][0]['Units'];
								$RSD1 = $data['CurrencyExchangeItems'][15]['Middle'];
								$RSD2 = $data['CurrencyExchangeItems'][15]['Units'];
								
								$EUR = str_replace(',', '.', $EUR1);
								$RSD = str_replace(',', '.', $RSD1);
								
								$provizija_zaposlenika_bih = ($provizija_zaposlenika_srb / 100) * $RSD ;
								$provizija_zaposlenika_eur = $provizija_zaposlenika_bih / $EUR;
							}
							$prov_bam_f = number_format((float)$provizija_zaposlenika_bih, 4, '.', '');
							$prov_eur_f = number_format((float)$provizija_zaposlenika_eur, 4, '.', '');
							$rprov_rsd_f = number_format((float)$provizija_zaposlenika_srb, 4, '.', '');
							
							echo $employee_id."---".$predracun_id."---".$valuta."---".$vrijednost_tipa_f."---".$vrijednost_kategorije_f."---".$prov_bam_f."---".$prov_eur_f."---".$rprov_rsd_f."<br/>";
							//INSERT
							$insert_obracun = $db->prepare("	
										INSERT INTO idk_obracuni	
										(employee_id,  predracun_id, valuta, rata, broj_rata, vrijednost_tipa, vrijednost_kategorije, iznos_obracuna_bam, iznos_obracuna_rsd, iznos_obracuna_eur, vrijeme_kreiranja, tip_provizije)	
										VALUES	
										(:employee_id,:predracun_id,:valuta,:rata,:broj_rata,:vrijednost_tipa,:vrijednost_kategorije,:iznos_obracuna_bam,:iznos_obracuna_rsd,:iznos_obracuna_eur,:vrijeme_kreiranja,:tip_provizije)	
										");	
							$insert_obracun->execute(array(	
										':employee_id' => $employee_id,	
										':predracun_id' => $predracun_id,	
										':valuta' => $valuta_provizije,	
										':rata' => $rata,	
										':broj_rata' => $broj_rata,	
										':vrijednost_tipa' => $vrijednost_tipa_f,	
										':vrijednost_kategorije' => $vrijednost_kategorije_f,	
										':iznos_obracuna_bam' => $prov_bam_f,	
										':iznos_obracuna_rsd' => $rprov_rsd_f,	
										':iznos_obracuna_eur' => $prov_eur_f,
										':vrijeme_kreiranja' => $datum_kreiranja,
										':tip_provizije' => $tp_id
										));
						}
					}
				}
			}
			
		}else{
			//NE IDE NISTA
		}
	}else{
		// var_dump("prva rata");
		// exit();
		//AKO JE PRVA RADI PO NOVOM SISTEMU
	
		//get tim id
		$query_tim = $db->prepare("SELECT employee_team FROM idk_employees WHERE employee_id = $agent_id");
		$query_tim->execute();
		$row_tim = $query_tim->fetch();
		$tim_id = $row_tim['employee_team'];
		
		switch($vrsta_ugovora){
			case 1: case 2: case 22: case 52: case 62: case 72: case 82: case 42:
				$broj_rata = 2;
			break;
			case 5: case 6: case 23: case 53: case 63: case 73: case 83: case 43:
				$broj_rata = 3;
			break;
			case 7: case 8: case 24: case 54: case 64: case 74: case 84: case 44:
				$broj_rata = 4;
			break;
			case 3: case 4: case 25: case 55: case 65: case 75: case 85: case 45:
				$broj_rata = 5;
			break;
			default:
			$broj_rata = 1;
		}
		
		//Za Currency exchange date
		$month = date('m');
		$day = date('d');
		$year = date('Y');
		
		$tipovi = $db->prepare("
						SELECT tp_iznos, tp_iznos_rs, tp_tip, tp_id
						FROM idk_tipovi_provizija
						WHERE tp_status = 1 AND (tp_tim = :tp_tim OR tp_tim = 0) AND tp_tim != 3
						");

		$tipovi->execute(array(
						":tp_tim" => $tim_id
		));
		
		while($tip_row = $tipovi->fetch()){
			$tp_iznos = $tip_row["tp_iznos"];
			$tp_iznos_rs = $tip_row["tp_iznos_rs"];
			$tp_tip = $tip_row["tp_tip"];
			$tp_id = $tip_row["tp_id"];
			if($valuta == "BAM" or $valuta == "EUR"){
				$vrijednost_tipa = $tp_iznos;
			}elseif($valuta == "RSD"){
				$vrijednost_tipa = $tp_iznos_rs;
			}
			$vrijednost_tipa_f = number_format((float)$vrijednost_tipa, 4, '.', '');
			
			$kategorije = $db->prepare("
							SELECT kp_iznos, kp_iznos_rs, kp_odjeli, kp_vrsta, kp_employee_id
							FROM idk_kategorije_provizija
							WHERE kp_status = 1 AND kp_tip_id = :kp_tip_id
							");

			$kategorije->execute(array(
							':kp_tip_id' => $tp_id
			));

			while($kategorija_row = $kategorije->fetch()){
				
				$kp_iznos = $kategorija_row["kp_iznos"];
				$kp_iznos_rs = $kategorija_row["kp_iznos_rs"];
				$kp_odjeli = $kategorija_row["kp_odjeli"];
				$kp_vrsta = $kategorija_row["kp_vrsta"];
				$kp_employee_id = $kategorija_row["kp_employee_id"];
				$kp_odjeli = $kategorija_row['kp_odjeli'];
				$odjeli = explode(",", $kp_odjeli);
				
				if($valuta == "BAM" or $valuta == "EUR"){
					$vrijednost_kategorije = $kp_iznos;
					$faktor_nula = 'AND employee_faktor_provizije_bih > 0';
				}elseif($valuta == "RSD"){
					$vrijednost_kategorije = $kp_iznos_rs;
					$faktor_nula = 'AND employee_faktor_provizije_srb > 0';
				}
				$vrijednost_kategorije_f = number_format((float)$vrijednost_kategorije, 4, '.', '');
				
				//GET SUMU FAKTORA ZA TU KATEGORIJU
				$faktor_query = $db->prepare("
									SELECT SUM(employee_faktor_provizije_bih) as suma_bih, SUM(employee_faktor_provizije_srb) as suma_srb
									FROM idk_employees
									WHERE employee_odjel IN ($kp_odjeli) AND employee_status != 0 ");

				$faktor_query->execute();
				$faktor_row = $faktor_query->fetch();
				$suma_faktora_bih = $faktor_row['suma_bih'];
				$suma_faktora_srb = $faktor_row['suma_srb'];
				
				if($kp_vrsta == 2){
					foreach($odjeli as $odjel_id){
						$odjeli_query = $db->prepare("
											SELECT employee_id, employee_faktor_provizije_bih, employee_faktor_provizije_srb
											FROM idk_employees
											WHERE employee_odjel = :employee_odjel AND employee_status != 0 $faktor_nula
											ORDER BY employee_id ");

						$odjeli_query->execute(array(
											":employee_odjel" => $odjel_id
						));
						
						while($employee_row = $odjeli_query->fetch()){

							$employee_id = $employee_row['employee_id'];
							$faktor_bih = $employee_row['employee_faktor_provizije_bih'];
							$faktor_srb = $employee_row['employee_faktor_provizije_srb'];
							
							if($valuta == "BAM" or $valuta == "EUR"){
								$valuta_provizije = "BAM";
								$provizija_zaposlenika_bih = (($kp_iznos * $faktor_bih)/$suma_faktora_bih);
								$ch = curl_init();
								// Disable SSL verification
								curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
								// Will return the response, if false it print the response
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
								// Set the url
								$rls="https://www.cbbh.ba/CurrencyExchange/GetJson?date=".$month."%2F".$day."%2F".$year."%2000%3A00%3A00";
								curl_setopt($ch, CURLOPT_URL,$rls);
								// Execute
								$result=curl_exec($ch);
								curl_close($ch);

								$data = json_decode($result, TRUE);
								$EUR1 = $data['CurrencyExchangeItems'][0]['Middle'];
								$EUR2 = $data['CurrencyExchangeItems'][0]['Units'];
								$RSD1 = $data['CurrencyExchangeItems'][15]['Middle'];
								$RSD2 = $data['CurrencyExchangeItems'][15]['Units'];
								
								$EUR = str_replace(',', '.', $EUR1);
								$RSD = str_replace(',', '.', $RSD1);
								
								$provizija_zaposlenika_eur = $provizija_zaposlenika_bih / $EUR;
								$provizija_zaposlenika_srb = 100*$provizija_zaposlenika_bih / $RSD;
								
								
							}elseif($valuta == "RSD"){
								$valuta_provizije = "RSD";
								$provizija_zaposlenika_srb = (($kp_iznos_rs * $faktor_srb)/$suma_faktora_srb);
								$ch = curl_init();
								// Disable SSL verification
								curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
								// Will return the response, if false it print the response
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
								// Set the url
								$rls="https://www.cbbh.ba/CurrencyExchange/GetJson?date=".$month."%2F".$day."%2F".$year."%2000%3A00%3A00";
								// var_dump($rls);
								// exit();
								curl_setopt($ch, CURLOPT_URL,$rls);
								curl_setopt($ch, CURLOPT_FAILONERROR, true); // Required for HTTP error codes to be reported via our call to curl_error($ch)
								// Execute
								$result=curl_exec($ch);
								
								curl_close($ch);

								$data = json_decode($result, TRUE);
								$EUR1 = $data['CurrencyExchangeItems'][0]['Middle'];
								$EUR2 = $data['CurrencyExchangeItems'][0]['Units'];
								$RSD1 = $data['CurrencyExchangeItems'][15]['Middle'];
								$RSD2 = $data['CurrencyExchangeItems'][15]['Units'];
								
								$EUR = str_replace(',', '.', $EUR1);
								$RSD = str_replace(',', '.', $RSD1);
								
								$provizija_zaposlenika_bih = ($provizija_zaposlenika_srb / 100) * $RSD ;
								$provizija_zaposlenika_eur = $provizija_zaposlenika_bih / $EUR;
							}
							$prov_bam_f = number_format((float)$provizija_zaposlenika_bih, 4, '.', '');
							$prov_eur_f = number_format((float)$provizija_zaposlenika_eur, 4, '.', '');
							$rprov_rsd_f = number_format((float)$provizija_zaposlenika_srb, 4, '.', '');
							
							echo $employee_id."---".$predracun_id."---".$valuta."---".$vrijednost_tipa_f."---".$vrijednost_kategorije_f."---".$prov_bam_f."---".$prov_eur_f."---".$rprov_rsd_f."<br/>";
							//INSERT
							$insert_obracun = $db->prepare("	
										INSERT INTO idk_obracuni	
										(employee_id,  predracun_id, valuta, rata, broj_rata, vrijednost_tipa, vrijednost_kategorije, iznos_obracuna_bam, iznos_obracuna_rsd, iznos_obracuna_eur, vrijeme_kreiranja, tip_provizije)	
										VALUES	
										(:employee_id,:predracun_id,:valuta,:rata,:broj_rata,:vrijednost_tipa,:vrijednost_kategorije,:iznos_obracuna_bam,:iznos_obracuna_rsd,:iznos_obracuna_eur,:vrijeme_kreiranja,:tip_provizije)	
										");	
							$insert_obracun->execute(array(	
										':employee_id' => $employee_id,	
										':predracun_id' => $predracun_id,	
										':valuta' => $valuta_provizije,	
										':rata' => $rata,	
										':broj_rata' => $broj_rata,	
										':vrijednost_tipa' => $vrijednost_tipa_f,	
										':vrijednost_kategorije' => $vrijednost_kategorije_f,	
										':iznos_obracuna_bam' => $prov_bam_f,	
										':iznos_obracuna_rsd' => $rprov_rsd_f,	
										':iznos_obracuna_eur' => $prov_eur_f,
										':vrijeme_kreiranja' => $datum_kreiranja,
										':tip_provizije' => $tp_id
										));
						}
					}
					
				}elseif($kp_vrsta == 3){
					
					$employee_id = $kp_employee_id;
					if($valuta == "BAM" or $valuta == "EUR"){
						$valuta_provizije = "BAM";
						$provizija_zaposlenika_bih = $kp_iznos;
						$ch = curl_init();
						// Disable SSL verification
						curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
						// Will return the response, if false it print the response
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						// Set the url
						$rls="https://www.cbbh.ba/CurrencyExchange/GetJson?date=".$month."%2F".$day."%2F".$year."%2000%3A00%3A00";
						curl_setopt($ch, CURLOPT_URL,$rls);
						// Execute
						$result=curl_exec($ch);
						curl_close($ch);

						$data = json_decode($result, TRUE);
						$EUR1 = $data['CurrencyExchangeItems'][0]['Middle'];
						$EUR2 = $data['CurrencyExchangeItems'][0]['Units'];
						$RSD1 = $data['CurrencyExchangeItems'][15]['Middle'];
						$RSD2 = $data['CurrencyExchangeItems'][15]['Units'];
						
						$EUR = str_replace(',', '.', $EUR1);
						$RSD = str_replace(',', '.', $RSD1);
						
						$provizija_zaposlenika_eur = $provizija_zaposlenika_bih / $EUR;
						$provizija_zaposlenika_srb = 100*$provizija_zaposlenika_bih / $RSD;
					}elseif($valuta == "RSD"){
						$valuta_provizije = "RSD";
						$provizija_zaposlenika_srb = $kp_iznos_rs;
						$ch = curl_init();
						// Disable SSL verification
						curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
						// Will return the response, if false it print the response
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						// Set the url
						$rls="https://www.cbbh.ba/CurrencyExchange/GetJson?date=".$month."%2F".$day."%2F".$year."%2000%3A00%3A00";
						// var_dump($rls);
						// exit();
						curl_setopt($ch, CURLOPT_URL,$rls);
						curl_setopt($ch, CURLOPT_FAILONERROR, true); // Required for HTTP error codes to be reported via our call to curl_error($ch)
						// Execute
						$result=curl_exec($ch);
						
						curl_close($ch);

						$data = json_decode($result, TRUE);
						$EUR1 = $data['CurrencyExchangeItems'][0]['Middle'];
						$EUR2 = $data['CurrencyExchangeItems'][0]['Units'];
						$RSD1 = $data['CurrencyExchangeItems'][15]['Middle'];
						$RSD2 = $data['CurrencyExchangeItems'][15]['Units'];
						
						$EUR = str_replace(',', '.', $EUR1);
						$RSD = str_replace(',', '.', $RSD1);
						
						$provizija_zaposlenika_bih = ($provizija_zaposlenika_srb / 100) * $RSD ;
						$provizija_zaposlenika_eur = $provizija_zaposlenika_bih / $EUR;
					}
					$prov_bam_f = number_format((float)$provizija_zaposlenika_bih, 4, '.', '');
					$prov_eur_f = number_format((float)$provizija_zaposlenika_eur, 4, '.', '');
					$rprov_rsd_f = number_format((float)$provizija_zaposlenika_srb, 4, '.', '');
					
					echo $employee_id."---".$predracun_id."---".$valuta."---".$vrijednost_tipa_f."---".$vrijednost_kategorije_f."---".$prov_bam_f."---".$prov_eur_f."---".$rprov_rsd_f."<br/>";
					//INSERT
					$insert_obracun = $db->prepare("	
								INSERT INTO idk_obracuni	
								(employee_id,  predracun_id, valuta, rata, broj_rata, vrijednost_tipa, vrijednost_kategorije, iznos_obracuna_bam, iznos_obracuna_rsd, iznos_obracuna_eur, vrijeme_kreiranja, tip_provizije)	
								VALUES	
								(:employee_id,:predracun_id,:valuta,:rata,:broj_rata,:vrijednost_tipa,:vrijednost_kategorije,:iznos_obracuna_bam,:iznos_obracuna_rsd,:iznos_obracuna_eur,:vrijeme_kreiranja,:tip_provizije)	
								");	
					$insert_obracun->execute(array(	
								':employee_id' => $employee_id,	
								':predracun_id' => $predracun_id,	
								':valuta' => $valuta_provizije,	
								':rata' => $rata,	
								':broj_rata' => $broj_rata,	
								':vrijednost_tipa' => $vrijednost_tipa_f,	
								':vrijednost_kategorije' => $vrijednost_kategorije_f,	
								':iznos_obracuna_bam' => $prov_bam_f,	
								':iznos_obracuna_rsd' => $rprov_rsd_f,	
								':iznos_obracuna_eur' => $prov_eur_f,
								':vrijeme_kreiranja' => $datum_kreiranja,
								':tip_provizije' => $tp_id
								));
				}elseif($kp_vrsta == 4){
					//SUMA FAKTORA ZA PROVIZIJE OD TIMOVA
					$faktor_tim_query = $db->prepare("
										SELECT SUM(employee_faktor_za_timove) as suma_tim
										FROM idk_employees
										WHERE employee_status != 0 ");

					$faktor_tim_query->execute();
					$faktor_tim_row = $faktor_tim_query->fetch();
					$suma_faktora_tim = $faktor_tim_row['suma_tim'];
					
					$emp_query = $db->prepare("
										SELECT employee_id, employee_firstname, employee_lastname, employee_faktor_za_timove, employee_odjel
										FROM idk_employees
										WHERE employee_status != 0 AND employee_team = 1 AND employee_faktor_za_timove > 0
										ORDER BY employee_odjel,employee_id ");

					$emp_query->execute();
					
					while($emp_row = $emp_query->fetch()){

						$employee_id = $emp_row['employee_id'];
						$employee_faktor_za_timove = $emp_row['employee_faktor_za_timove'];
						
						if($valuta == "BAM" or $valuta == "EUR"){
							$valuta_provizije = "BAM";
							$provizija_zaposlenika_bih = (($kp_iznos * $employee_faktor_za_timove)/$suma_faktora_tim);
							$ch = curl_init();
							// Disable SSL verification
							curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
							// Will return the response, if false it print the response
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							// Set the url
							$rls="https://www.cbbh.ba/CurrencyExchange/GetJson?date=".$month."%2F".$day."%2F".$year."%2000%3A00%3A00";
							curl_setopt($ch, CURLOPT_URL,$rls);
							// Execute
							$result=curl_exec($ch);
							curl_close($ch);

							$data = json_decode($result, TRUE);
							$EUR1 = $data['CurrencyExchangeItems'][0]['Middle'];
							$EUR2 = $data['CurrencyExchangeItems'][0]['Units'];
							$RSD1 = $data['CurrencyExchangeItems'][15]['Middle'];
							$RSD2 = $data['CurrencyExchangeItems'][15]['Units'];
							
							$EUR = str_replace(',', '.', $EUR1);
							$RSD = str_replace(',', '.', $RSD1);
							
							$provizija_zaposlenika_eur = $provizija_zaposlenika_bih / $EUR;
							$provizija_zaposlenika_srb = 100*$provizija_zaposlenika_bih / $RSD;
							
							
						}elseif($valuta == "RSD"){
							$valuta_provizije = "RSD";
							$provizija_zaposlenika_srb = (($kp_iznos_rs * $employee_faktor_za_timove)/$suma_faktora_tim);
							$ch = curl_init();
							// Disable SSL verification
							curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
							// Will return the response, if false it print the response
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							// Set the url
							$rls="https://www.cbbh.ba/CurrencyExchange/GetJson?date=".$month."%2F".$day."%2F".$year."%2000%3A00%3A00";
							// var_dump($rls);
							// exit();
							curl_setopt($ch, CURLOPT_URL,$rls);
							curl_setopt($ch, CURLOPT_FAILONERROR, true); // Required for HTTP error codes to be reported via our call to curl_error($ch)
							// Execute
							$result=curl_exec($ch);
							
							curl_close($ch);

							$data = json_decode($result, TRUE);
							$EUR1 = $data['CurrencyExchangeItems'][0]['Middle'];
							$EUR2 = $data['CurrencyExchangeItems'][0]['Units'];
							$RSD1 = $data['CurrencyExchangeItems'][15]['Middle'];
							$RSD2 = $data['CurrencyExchangeItems'][15]['Units'];
							
							$EUR = str_replace(',', '.', $EUR1);
							$RSD = str_replace(',', '.', $RSD1);
							
							$provizija_zaposlenika_bih = ($provizija_zaposlenika_srb / 100) * $RSD ;
							$provizija_zaposlenika_eur = $provizija_zaposlenika_bih / $EUR;
						}
						$prov_bam_f = number_format((float)$provizija_zaposlenika_bih, 4, '.', '');
						$prov_eur_f = number_format((float)$provizija_zaposlenika_eur, 4, '.', '');
						$rprov_rsd_f = number_format((float)$provizija_zaposlenika_srb, 4, '.', '');
						
						echo $employee_id."---".$predracun_id."---".$valuta."---".$vrijednost_tipa_f."---".$vrijednost_kategorije_f."---".$prov_bam_f."---".$prov_eur_f."---".$rprov_rsd_f."<br/>";
						//INSERT
						$insert_obracun = $db->prepare("	
									INSERT INTO idk_obracuni	
									(employee_id,  predracun_id, valuta, rata, broj_rata, vrijednost_tipa, vrijednost_kategorije, iznos_obracuna_bam, iznos_obracuna_rsd, iznos_obracuna_eur, vrijeme_kreiranja, tip_provizije)	
									VALUES	
									(:employee_id,:predracun_id,:valuta,:rata,:broj_rata,:vrijednost_tipa,:vrijednost_kategorije,:iznos_obracuna_bam,:iznos_obracuna_rsd,:iznos_obracuna_eur,:vrijeme_kreiranja,:tip_provizije)	
									");	
						$insert_obracun->execute(array(	
									':employee_id' => $employee_id,	
									':predracun_id' => $predracun_id,	
									':valuta' => $valuta_provizije,	
									':rata' => $rata,	
									':broj_rata' => $broj_rata,	
									':vrijednost_tipa' => $vrijednost_tipa_f,	
									':vrijednost_kategorije' => $vrijednost_kategorije_f,	
									':iznos_obracuna_bam' => $prov_bam_f,	
									':iznos_obracuna_rsd' => $rprov_rsd_f,	
									':iznos_obracuna_eur' => $prov_eur_f,
									':vrijeme_kreiranja' => $datum_kreiranja,
									':tip_provizije' => $tp_id
									));
					}
				}
			}
		}
	}
}

function updatePrethodneObracunetest($vrijednost_tipa_f, $vrijednost_kategorije_f, $prov_bam_f, $prov_rsd_f, $prov_eur_f, $id, $datum_uplate){
	Global $db;
	$query_upd = $db->prepare("UPDATE idk_obracuni SET vrijednost_tipa = :vrijednost_tipa, vrijednost_kategorije = :vrijednost_kategorije, iznos_obracuna_bam = :iznos_obracuna_bam, iznos_obracuna_rsd = :iznos_obracuna_rsd, iznos_obracuna_eur = :iznos_obracuna_eur
								WHERE id = :id
	");
	
	$query_upd->execute(array(
				':vrijednost_tipa' => $vrijednost_tipa_f,
				':vrijednost_kategorije' => $vrijednost_kategorije_f,
				':iznos_obracuna_bam' => $prov_bam_f,
				':iznos_obracuna_rsd' => $prov_rsd_f,
				':iznos_obracuna_eur' => $prov_eur_f,
				':id' => $id
	));
	
}

function uplatiObracune($predracun_id){
	Global $db;
	
	$query_date = $db->prepare("SELECT pr_datum_uplate, pr_datum_kreiranja, pr_kandidat_id, pr_domaca_valuta, pr_zaposlenik, pr_rata FROM idk_predracuni WHERE pr_id = $predracun_id");
	$query_date->execute();

	$row_date = $query_date->fetch();
	$pr_datum_kreiranja = $row_date['pr_datum_kreiranja'];
	$pr_datum_kreiranja_calc = strtotime($row_date['pr_datum_kreiranja']);
	
	$datum_uplate = $row_date['pr_datum_uplate'];
	$pr_kandidat_id = $row_date['pr_kandidat_id'];
	$valuta = $row_date['pr_domaca_valuta'];
	$employee_id = $row_date['pr_zaposlenik'];
	$agent_prodao = $row_date['pr_zaposlenik'];
	$pr_rata = $row_date['pr_rata'];
	$rata = $pr_rata;

	//Za Currency exchange date
	$month = date('m');
	$day = date('d');
	$year = date('Y');
	
	$id_tima = getTeamIdByEmployee($employee_id);
	
	$vrsta_ugovora = getVrstaUgovora($pr_kandidat_id);
	switch($vrsta_ugovora){
		case 1: case 2: case 22: case 52: case 62: case 72: case 82: case 42:
			$broj_rata = 2;
		break;
		case 5: case 6: case 23: case 53: case 63: case 73: case 83: case 43:
			$broj_rata = 3;
		break;
		case 7: case 8: case 24: case 54: case 64: case 74: case 84: case 44:
			$broj_rata = 4;
		break;
		case 3: case 4: case 25: case 55: case 65: case 75: case 85: case 45:
			$broj_rata = 5;
		break;
		default:
		$broj_rata = 1;
	}

	$vrijeme_uplate = date('Y-m-d 07:00:00', strtotime($datum_uplate));
	$vrijeme_uplate_f = strtotime($vrijeme_uplate);
	
	if($id_tima == 1){
		$tp_id_za_inkaso = 1;
	}elseif($id_tima == 2){
		$tp_id_za_inkaso = 3;
	}elseif($id_tima == 4){
		$tp_id_za_inkaso = 10;
	}
	
	$ink_koeficijent_1 = getInkasoKoef1($predracun_id); //promjena ugovora
	$ink_koeficijent_2 = getInkasoKoef2($predracun_id); //normalni inkaso
	if($ink_koeficijent_1 > 0 OR $ink_koeficijent_2 > 0){
		$inkaso_check = true;
		$koef_agent = 0.5;
		$tp_minus_inkaso = 98;
	}else{
		$inkaso_check = false;
		$koef_agent = 1;
		$tp_minus_inkaso = 1;
	}
	
	if($pr_rata == 1){
		// PRIJE UPDATE OBRACUNA DA SU UPLACENI TREBA PROVJERITI DA LI SE RADI O INKASU, 
		// IZ TOGA SE DOBIJE KOEF ZA IZNOS ZA KP_VRSTU=1, 0.5 AKO IMA INKASA, 1 AKO NEMA INKASA(gledati po agent idu i tipu provIzije i vecem iznosu), 
		// AKO JE PREDRACUN KREIRAN PRIJE 1.9. EDITUJE SE IZNOS, AKO JE POSLIJE 1.9 ONDA SE NOVI KREIRA
		// PRVO DODATI PROVIZIJE ZA INKASO ODJEL
		
		if(!$inkaso_check){
			//ako nema inkasa treba samo dodati proviziju za agenta
		}else{
			//ako ima inkasa onda treba dodati i proviziju za inkaso agenta, 
			//u slucaju da je predracun napravljen prije 1.9.2021 onda dodati novu za inkaso agenta,
			//stare inkaso obracune je potrebno arhivirati
			if($ink_koeficijent_2 > 0){
				//AKO je normalni inkaso nema potrebe nista raditi, sigurno ??????
				//DOBITI id AGENTA
				$get_ink_agent = $db->prepare("SELECT dodao_zaposlenik_biljeska_nd FROM idk_nd_kandidata_biljeske WHERE predracun_id = :predracun_id AND status_biljeska_nd = 3 AND tip_biljeska_nd = 5 AND zadnja_inkaso_biljeska = 1");
				$get_ink_agent->execute(array( ':predracun_id' => $predracun_id));
				$row_ink_agent = $get_ink_agent->fetch();
				$inkaso_agent_id = $row_ink_agent['dodao_zaposlenik_biljeska_nd'];
			}else{
				if($ink_koeficijent_1 > 0){
					//ako je inkaso preko promjene ugovora onda treba arhivirati stare inkaso-odjel provizije u slucaju da je predracun kreiran prije septembra
					//u svakom slucaju treba ovo arhivirati
					$get_ink_agent = $db->prepare("SELECT pr_inkaso_agent FROM idk_predracuni WHERE pr_id = :pr_id");
					$get_ink_agent->execute(array( ':pr_id' => $predracun_id));
					$row_ink_agent = $get_ink_agent->fetch();
					$inkaso_agent_id = $row_ink_agent['pr_inkaso_agent'];
					if($pr_datum_kreiranja < '2021-09-01 00:00:00'){
						//ARHIVIRATI STARE INKASO OBRACUNE
						$upd_ink_obracuni = $db->prepare("UPDATE idk_obracuni SET status_obracuna = 2 WHERE predracun_id = :predracun_id AND tip_provizije = 99");
						$upd_ink_obracuni->execute(array(':predracun_id' => $predracun_id));
					}
				}
			}
			
			//INSERT INKASO PROVIZIJA
			$tp_id_ink = 99;
			$bu_ink_agent = buInkAgent($inkaso_agent_id, $datum_uplate); //broj uplata inkaso agenta za mjesec
			var_dump($bu_ink_agent);
			switch($bu_ink_agent){
				case 0: case 1: case 2:
					$kp_iznos_new_ia = 0;
				break;
				case 3: case 4: case 5:
					$kp_iznos_new_ia = 12.5;
				break;
				case 6: case 7: case 8: case 9:
					$kp_iznos_new_ia = 17.5;
				break;
				default:
					$kp_iznos_new_ia = 25;
			}
			
			$prov_za_ink_agenta_bam = $kp_iznos_new_ia;
			//$prov_za_ink_agenta_bam 
			$ch = curl_init();
			// Disable SSL verification
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			// Will return the response, if false it print the response
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			// Set the url
			$rls="https://www.cbbh.ba/CurrencyExchange/GetJson?date=".$month."%2F".$day."%2F".$year."%2000%3A00%3A00";
			curl_setopt($ch, CURLOPT_URL,$rls);
			// Execute
			$result=curl_exec($ch);
			curl_close($ch);

			$data = json_decode($result, TRUE);
			$EUR1 = $data['CurrencyExchangeItems'][0]['Middle'];
			$EUR2 = $data['CurrencyExchangeItems'][0]['Units'];
			$RSD1 = $data['CurrencyExchangeItems'][15]['Middle'];
			$RSD2 = $data['CurrencyExchangeItems'][15]['Units'];
			
			$EUR = str_replace(',', '.', $EUR1);
			$RSD = str_replace(',', '.', $RSD1);
			
			$prov_za_ink_agenta_eur = $prov_za_ink_agenta_bam / $EUR;
			$prov_za_ink_agenta_rsd = 100*$prov_za_ink_agenta_bam / $RSD;
			
			$prov_ink_bam_f = number_format((float)$prov_za_ink_agenta_bam, 4, '.', '');
			$prov_ink_eur_f = number_format((float)$prov_za_ink_agenta_eur, 4, '.', '');
			$rprov_ink_rsd_f = number_format((float)$prov_za_ink_agenta_rsd, 4, '.', '');
			
			if($valuta == "BAM" or $valuta == "EUR"){
				$valuta_provizije = "BAM";
				$vrijednost_tipa = 30 + $kp_iznos_new_ia * 2;
				$vrijednost_kategorije = $kp_iznos_new_ia * 2;
			}elseif($valuta == "RSD"){
				$valuta_provizije = "RSD";
				$vrijednost_tipa = 1803 + ($prov_za_ink_agenta_rsd * 2);
				$vrijednost_kategorije = ($prov_za_ink_agenta_rsd * 2);
			}
			$vrijednost_tipa_f = number_format((float)$vrijednost_tipa, 4, '.', '');
			$vrijednost_kategorije_f = number_format((float)$vrijednost_kategorije, 4, '.', '');
			
			if($bu_ink_agent == 3 OR $bu_ink_agent == 6 OR $bu_ink_agent == 10){
				updatePrethodneObracune($vrijednost_tipa_f, $vrijednost_kategorije_f, $prov_ink_bam_f, $rprov_ink_rsd_f, $prov_ink_eur_f, $inkaso_agent_id, $datum_uplate);
			}
			//echo $inkaso_agent_id."---".$predracun_id."---".$valuta."---".$vrijednost_tipa_f."---".$vrijednost_kategorije_f."---".$prov_ink_bam_f."---".$prov_ink_eur_f."---".$rprov_ink_rsd_f."---".$tp_id_ink."<br/>";
			
			//INSERT
			$insert_obracun = $db->prepare("	
						INSERT INTO idk_obracuni	
						(employee_id,  predracun_id, valuta, rata, broj_rata, vrijednost_tipa, vrijednost_kategorije, iznos_obracuna_bam, iznos_obracuna_rsd, iznos_obracuna_eur, vrijeme_kreiranja, tip_provizije, kategorija_provizije)	
						VALUES	
						(:employee_id,:predracun_id,:valuta,:rata,:broj_rata,:vrijednost_tipa,:vrijednost_kategorije,:iznos_obracuna_bam,:iznos_obracuna_rsd,:iznos_obracuna_eur,:vrijeme_kreiranja,:tip_provizije,:kategorija_provizije)	
						");	
			$insert_obracun->execute(array(	
						':employee_id' => $inkaso_agent_id,	
						':predracun_id' => $predracun_id,	
						':valuta' => $valuta_provizije,	
						':rata' => $rata,	
						':broj_rata' => $broj_rata,	
						':vrijednost_tipa' => $vrijednost_tipa_f,	
						':vrijednost_kategorije' => $vrijednost_kategorije_f,	
						':iznos_obracuna_bam' => $prov_ink_bam_f,	
						':iznos_obracuna_rsd' => $rprov_ink_rsd_f,	
						':iznos_obracuna_eur' => $prov_ink_eur_f,
						':vrijeme_kreiranja' => $vrijeme_uplate,
						':tip_provizije' => $tp_id_ink,
						':kategorija_provizije' => 2
						));
		}
		
		$agent_status = explode( ',' , getEmployeeStatusById($agent_prodao));
		
		if(in_array("15", $agent_status) OR $agent_prodao == 121 OR $agent_prodao == 163 OR $agent_prodao == 204){
			//proracun provizija za agente koji imaju drugacije umjesto onih konstantih 20KM
			
			$broj_uplata = getBrojUplataAgentaZaMjesec($agent_prodao, $datum_uplate) ;
			switch($broj_uplata){
				case 0: case 1: case 2:
					$kp_iznos_new = 0;
				break;
				case 3: case 4: case 5:
					$kp_iznos_new = 25;
				break;
				case 6: case 7: case 8: case 9:
					$kp_iznos_new = 35;
				break;
				case 10: case 11: case 12: case 13:
					$kp_iznos_new = 50;
				break;
				case 14: case 15: case 16:
					$kp_iznos_new = 60;
				break;
				case 17: case 18: case 19:
					$kp_iznos_new = 70;
				break;
				default:
					$kp_iznos_new = 80;
			}
			// var_dump($broj_uplata);
			// var_dump($kp_iznos_new);
		}else{
			//vrijednost provizije za obicne zaposlenike koji naprave prodaju je po starom: 20KM
			$kp_iznos_new = 20;
			$broj_uplata = 0;
		}
		$provizija_zaposlenika_bih = $kp_iznos_new * $koef_agent;
		$ch = curl_init();
		// Disable SSL verification
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		// Will return the response, if false it print the response
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// Set the url
		$rls="https://www.cbbh.ba/CurrencyExchange/GetJson?date=".$month."%2F".$day."%2F".$year."%2000%3A00%3A00";
		curl_setopt($ch, CURLOPT_URL,$rls);
		// Execute
		$result=curl_exec($ch);
		curl_close($ch);

		$data = json_decode($result, TRUE);
		$EUR1 = $data['CurrencyExchangeItems'][0]['Middle'];
		$EUR2 = $data['CurrencyExchangeItems'][0]['Units'];
		$RSD1 = $data['CurrencyExchangeItems'][15]['Middle'];
		$RSD2 = $data['CurrencyExchangeItems'][15]['Units'];
		
		$EUR = str_replace(',', '.', $EUR1);
		$RSD = str_replace(',', '.', $RSD1);
		
		$provizija_zaposlenika_eur = $provizija_zaposlenika_bih / $EUR;
		$provizija_zaposlenika_srb = 100*$provizija_zaposlenika_bih / $RSD;
		
		$prov_bam_f = number_format((float)$provizija_zaposlenika_bih, 4, '.', '');
		$prov_eur_f = number_format((float)$provizija_zaposlenika_eur, 4, '.', '');
		$rprov_rsd_f = number_format((float)$provizija_zaposlenika_srb, 4, '.', '');
		
		
		if($valuta == "BAM" or $valuta == "EUR"){
			$valuta_provizije = "BAM";
			$vrijednost_tipa = 30 + $kp_iznos_new;
			$vrijednost_kategorije = $kp_iznos_new;
		}elseif($valuta == "RSD"){
			$valuta_provizije = "RSD";
			$vrijednost_tipa = 1803 + ($provizija_zaposlenika_srb / $koef_agent);
			$vrijednost_kategorije = ($provizija_zaposlenika_srb / $koef_agent);
		}
		$vrijednost_tipa_f = number_format((float)$vrijednost_tipa, 4, '.', '');
		$vrijednost_kategorije_f = number_format((float)$vrijednost_kategorije, 4, '.', '');
		
		if($broj_uplata == 3 OR $broj_uplata == 6 OR $broj_uplata == 10 OR $broj_uplata == 14 OR $broj_uplata == 17 OR $broj_uplata == 20){
			$current_month_start = date('Y-m-01 00:00:00', strtotime($datum_uplate));
			$current_month_end = date('Y-m-t 23:59:59', strtotime($datum_uplate));
			$query_get_o_ids = $db->prepare("SELECT id, valuta, tip_provizije FROM idk_obracuni WHERE employee_id = :employee_id AND vrijeme_uplate BETWEEN :start_date AND :end_date AND kategorija_provizije = 1 ");

			$query_get_o_ids->execute(array(
						':employee_id' => $employee_id,
						':start_date' => $current_month_start,
						':end_date' => $current_month_end
			));
			while($row_o_ids = $query_get_o_ids->fetch()){
				$o_id = $row_o_ids['id'];
				$o_valuta = $row_o_ids['valuta'];
				$o_tip_provizije = $row_o_ids['tip_provizije'];
				if($o_tip_provizije == 1)
					$o_koef = 1;
				else
					$o_koef = 0.5;
				$o_kp_iznos_new = $kp_iznos_new * $o_koef;
				if($o_valuta == "BAM"){
					$o_vrijednost_tipa = 30 + $kp_iznos_new;
					$o_vrijednost_kategorije = $kp_iznos_new;
				}elseif($o_valuta == "RSD"){
					$o_vrijednost_tipa = 1803 + ($provizija_zaposlenika_srb / $koef_agent);
					$o_vrijednost_kategorije = ($provizija_zaposlenika_srb / $koef_agent);
				}
				$o_vrijednost_tipa_f = number_format((float)$o_vrijednost_tipa, 4, '.', '');
				$o_vrijednost_kategorije_f = number_format((float)$o_vrijednost_kategorije, 4, '.', '');
				$o_prov_bam_f = number_format((float)$kp_iznos_new*$o_koef, 4, '.', '');
				$o_prov_eur_f = number_format((float)($provizija_zaposlenika_eur*$o_koef /$koef_agent), 4, '.', '');
				$o_rprov_rsd_f = number_format((float)($provizija_zaposlenika_srb*$o_koef / $koef_agent), 4, '.', '');
				//echo "predracun id: ".$o_id." - valuta: ".$o_valuta." - tip pr: ".$o_tip_provizije." - vrijednost tipa: ".$o_vrijednost_tipa_f." - vr kat: ".$o_vrijednost_kategorije_f." - prov bam: ".$o_prov_bam_f." - prov rsd: ".$o_rprov_rsd_f." - prov eur".$o_prov_eur_f."<br>";
				updatePrethodneObracunetest($o_vrijednost_tipa_f, $o_vrijednost_kategorije_f, $o_prov_bam_f, $o_rprov_rsd_f, $o_prov_eur_f, $o_id, $datum_uplate);
			}
			
		}
		
		if($pr_datum_kreiranja < '2021-09-01 00:00:00'){
			// manji od sept, tj predracun kreiran prije septembra
			
			$get_stari_obracun_za_edit = $db->prepare("SELECT * FROM idk_obracuni 
												WHERE employee_id = $agent_prodao AND predracun_id = $predracun_id AND kategorija_provizije = 1 ");
			$get_stari_obracun_za_edit->execute();
			$row_obracun_stari_za_edit = $get_stari_obracun_za_edit->fetch();
			$id_stari_obracun = $row_obracun_stari_za_edit['id'];
			//EDIT provizije za agenta
			//echo "<br>".$employee_id."---Novi iznosi: novi_bam: ".$prov_bam_f."---novi_rsd: ".$rprov_rsd_f."---novi_eur: ".$prov_eur_f."--- tp_minus_inkaso: ".$tp_minus_inkaso." --- stari id obr".$id_stari_obracun."<br>";
			$update_obracun_ink = $db->prepare("
							UPDATE idk_obracuni
							SET vrijednost_tipa = :vrijednost_tipa, vrijednost_kategorije = :vrijednost_kategorije, iznos_obracuna_bam = :iznos_obracuna_bam, iznos_obracuna_rsd = :iznos_obracuna_rsd, iznos_obracuna_eur = :iznos_obracuna_eur, tip_provizije = :tip_provizije
							WHERE id = :id ");

			$update_obracun_ink->execute(array(
						':vrijednost_tipa' => $vrijednost_tipa_f,	
						':vrijednost_kategorije' => $vrijednost_kategorije_f,	
						':iznos_obracuna_bam' => $prov_bam_f,
						':iznos_obracuna_rsd' => $rprov_rsd_f,
						':iznos_obracuna_eur' => $prov_eur_f,
						':tip_provizije' => $tp_minus_inkaso,
						':id' => $id_stari_obracun
						));
			
		}else{
			//predracun kreiran poslije 1.septembra
			
			//echo $employee_id."---".$predracun_id."---".$valuta."---".$vrijednost_tipa_f."---".$vrijednost_kategorije_f."---".$prov_bam_f."---".$prov_eur_f."---".$rprov_rsd_f."<br/>";
			
			if($vrsta_ugovora != 99){
				//INSERT
				$insert_obracun = $db->prepare("	
							INSERT INTO idk_obracuni	
							(employee_id,  predracun_id, status_predracuna, valuta, rata, broj_rata, vrijednost_tipa, vrijednost_kategorije, iznos_obracuna_bam, iznos_obracuna_rsd, iznos_obracuna_eur, vrijeme_kreiranja, vrijeme_uplate, tip_provizije, kategorija_provizije)	
							VALUES	
							(:employee_id,:predracun_id,:status_predracuna,:valuta,:rata,:broj_rata,:vrijednost_tipa,:vrijednost_kategorije,:iznos_obracuna_bam,:iznos_obracuna_rsd,:iznos_obracuna_eur,:vrijeme_kreiranja,:vrijeme_uplate,:tip_provizije,:kategorija_provizije)	
							");	
				$insert_obracun->execute(array(	
							':employee_id' => $employee_id,	
							':predracun_id' => $predracun_id,	
							':status_predracuna' => 1,	
							':valuta' => $valuta_provizije,	
							':rata' => $rata,	
							':broj_rata' => $broj_rata,	
							':vrijednost_tipa' => $vrijednost_tipa_f,	
							':vrijednost_kategorije' => $vrijednost_kategorije_f,	
							':iznos_obracuna_bam' => $prov_bam_f,	
							':iznos_obracuna_rsd' => $rprov_rsd_f,	
							':iznos_obracuna_eur' => $prov_eur_f,
							':vrijeme_kreiranja' => $vrijeme_uplate,
							':vrijeme_uplate' => $vrijeme_uplate,
							':tip_provizije' => $tp_minus_inkaso,
							':kategorija_provizije' => 1
							));
			}
		}
	}
	if($vrsta_ugovora != 99){
		$update_obracuni = $db->prepare("
						UPDATE idk_obracuni
						SET status_predracuna = :status_predracuna, vrijeme_uplate = :vrijeme_uplate
						WHERE predracun_id = :predracun_id AND employee_id != 107");

		$update_obracuni->execute(array(
					':status_predracuna' => 1,
					':predracun_id' => $predracun_id,
					':vrijeme_uplate' => $vrijeme_uplate
					));
	}
	
	if ($pr_rata == 1){
		
		//Adil provizija->nekad bila sada ide Minki (od 1.11.2022)
		if($datum_uplate >= date('2022-11-01')){
			$branch_id = getBranchIdByEmployee($agent_prodao);
			$branches = array(1,3,4,5,6,7); //poslovnice od kojih ne dobija proviziju
			// $branches = array(2,8,10);
			if(!in_array($branch_id, $branches )){
				
				$tipovi = $db->prepare("
								SELECT tp_iznos, tp_iznos_rs, tp_tip, tp_id
								FROM idk_tipovi_provizija
								WHERE tp_id = 11
								");

				$tipovi->execute();
				
				$tip_row = $tipovi->fetch();
				$tp_iznos = $tip_row["tp_iznos"]; //nece se vise uzimati ova vrijednost jer je iznos prozvizije varijabilan u odnosu na broj uplata agenta
				$tp_iznos_rs = $tip_row["tp_iznos_rs"];
				$tp_tip = $tip_row["tp_tip"];
				$tp_id = $tip_row["tp_id"];

				$pocetak_rada = getEmployeeDoe($agent_prodao);
				$pocetak_rada_c = date('Y-m-d', strtotime($pocetak_rada.' +3 months'));
				
				$broj_uplata = getBrojUplataAgentaZaMjesec($agent_prodao, $datum_uplate);
				
				if(($datum_uplate > $pocetak_rada_c) && $broj_uplata < 4){
					//nema provizije u slucaju da agent radi vise od 3 mjeseca i radi se o prvoj, drugoj ili trecoj uplati za taj mjesec
				}else{
					
					//switch-case za tp_iznos koja ce vraca vrijednost iznosa na osnovu broja uplata agenta
					switch($broj_uplata){
						case 0:
							$tp_iznos = 0;
							break;
						case 1: case 2: case 3: case 4:
							$tp_iznos = 20;
							break;
						case 5: case 6:
							$tp_iznos = 25;
							break;
						case 7: case 8:
							$tp_iznos = 30;
							break;
						case 9:
							$tp_iznos = 35;
							break;
						case 10: 
							$tp_iznos = 40;
							break;
						case 11: case 12: case 13: case 14:
							$tp_iznos = 10;
							break;
						case 15: case 16: case 17: case 18: case 19:
							$tp_iznos = 15;
							break;
						default:
							$tp_iznos = 20;
					}
					
					$vrijednost_tipa = $tp_iznos;
					$vrijednost_tipa_f = number_format((float)$vrijednost_tipa, 4, '.', '');
					$vrijednost_kategorije_f = number_format((float)$vrijednost_tipa, 4, '.', '');
					
					$provizija_zaposlenika_bih = $tp_iznos;
					$ch = curl_init();
					// Disable SSL verification
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
					// Will return the response, if false it print the response
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					// Set the url
					$rls="https://www.cbbh.ba/CurrencyExchange/GetJson?date=".$month."%2F".$day."%2F".$year."%2000%3A00%3A00";
					curl_setopt($ch, CURLOPT_URL,$rls);
					// Execute
					$result=curl_exec($ch);
					curl_close($ch);

					$data = json_decode($result, TRUE);
					$EUR1 = $data['CurrencyExchangeItems'][0]['Middle'];
					$EUR2 = $data['CurrencyExchangeItems'][0]['Units'];
					$RSD1 = $data['CurrencyExchangeItems'][15]['Middle'];
					$RSD2 = $data['CurrencyExchangeItems'][15]['Units'];
					
					$EUR = str_replace(',', '.', $EUR1);
					$RSD = str_replace(',', '.', $RSD1);
					
					$provizija_zaposlenika_eur = $provizija_zaposlenika_bih / $EUR;
					$provizija_zaposlenika_srb = 100*$provizija_zaposlenika_bih / $RSD;
					
					$prov_bam_f = number_format((float)$provizija_zaposlenika_bih, 4, '.', '');
					$prov_eur_f = number_format((float)$provizija_zaposlenika_eur, 4, '.', '');
					$rprov_rsd_f = number_format((float)$provizija_zaposlenika_srb, 4, '.', '');
					
					$pocetak_rada = getEmployeeDoe($agent_prodao);
					$pocetak_rada_c = date('Y-m-d', strtotime($pocetak_rada.' +3 months'));
					
					//echo "<br/>".$agent_prodao."---".$predracun_id."---".$valuta."---".$vrijednost_tipa_f."---".$vrijednost_kategorije_f."---".$prov_bam_f."---".$prov_eur_f."---".$rprov_rsd_f."<br/>";
					$insert_obracun = $db->prepare("	
								INSERT INTO idk_obracuni	
								(employee_id,  predracun_id, status_predracuna, valuta, rata, broj_rata, vrijednost_tipa, vrijednost_kategorije, iznos_obracuna_bam, iznos_obracuna_rsd, iznos_obracuna_eur, vrijeme_kreiranja, vrijeme_uplate, tip_provizije)	
								VALUES	
								(:employee_id,:predracun_id,:status_predracuna,:valuta,:rata,:broj_rata,:vrijednost_tipa,:vrijednost_kategorije,:iznos_obracuna_bam,:iznos_obracuna_rsd,:iznos_obracuna_eur,:vrijeme_kreiranja,:vrijeme_uplate,:tip_provizije)	
								");	
					$insert_obracun->execute(array(	
								':employee_id' => 158,	
								':predracun_id' => $predracun_id,	
								':status_predracuna' => 1,	
								':valuta' => $valuta_provizije,	
								':rata' => $rata,	
								':broj_rata' => $broj_rata,	
								':vrijednost_tipa' => $vrijednost_tipa_f,	
								':vrijednost_kategorije' => $vrijednost_kategorije_f,	
								':iznos_obracuna_bam' => $prov_bam_f,	
								':iznos_obracuna_rsd' => $rprov_rsd_f,	
								':iznos_obracuna_eur' => $prov_eur_f,
								':vrijeme_kreiranja' => $vrijeme_uplate,
								':vrijeme_uplate' => $vrijeme_uplate,
								':tip_provizije' => $tp_id
								));
				}
				
			}else{
				//echo "nije u toj poslovnici";
			}
		}
		
		
	}
}

function getInkasoKoef1($predracun_id){
	// Provjera inkasa kod dodavanja novog predracuna i novih obracuna KROZ PROMJENU UGOVORA
	Global $db;
	$get_ink_status = $db->prepare("SELECT pr_stari_ink_status FROM idk_predracuni WHERE pr_id = :pr_id");
	$get_ink_status->execute(array( ':pr_id' => $predracun_id));
	$row_ink_status = $get_ink_status->fetch();
	$stari_ink_status = $row_ink_status['pr_stari_ink_status'];
	switch($stari_ink_status){
		case 3: $koef = 0.1; break;
		case 4: $koef = 0.2; break;
		case 5: $koef = 0.3; break;
		default: $koef = 0;
		break;
	}
	
	return $koef;
}

function getInkasoKoef2($predracun_id){
	// Provjera inkasa kod uplacivanja
	Global $db;
	
	//Provjera da li je uplata nakon inkasa
	$get_ink_biljesku = $db->prepare("SELECT predracun_status FROM idk_nd_kandidata_biljeske 
										JOIN idk_predracuni ON predracun_id = idk_predracuni.pr_id
										WHERE predracun_id = :predracun_id AND status_biljeska_nd = 3 AND tip_biljeska_nd = 5 AND zadnja_inkaso_biljeska = 1");
	$get_ink_biljesku->execute(array( ':predracun_id' => $predracun_id));
	$row_ink_biljeska = $get_ink_biljesku->fetch();
	$predracun_ink_status = $row_ink_biljeska['predracun_status'];
	switch($predracun_ink_status){
		case 3: $koef = 0.1; break;
		case 4: $koef = 0.2; break;
		case 5: $koef = 0.3; break;
		default: $koef = 0;
	}
	
	return $koef;
}

//Ponovne prijave funkcije START
//Funkcija koja broji kandidate pod određenim statusom prioriteta (1,2,3)
function getBrojPrioritetKandidataDIPLR($status_prioritet, $zaposlenik){
	Global $db;
	
	
	if($zaposlenik == ""){
		$team = getLoggedEmployeeTeam();
		$employess_id_team = implode(", ", getIdOfEmployeeTeam($team));
		if($team == 1){
			$uslov_sql_zap = " zaduzen_zaposlenik_nd_kandidata is not null";
		}else{
			$uslov_sql_zap = " zaduzen_zaposlenik_nd_kandidata IN (".$employess_id_team.")";
		}
		$uslov = " status_pp = ".$status_prioritet." AND ".$uslov_sql_zap."";
	}else{
		$uslov = " zaduzen_zaposlenik_nd_kandidata = ".$zaposlenik." AND status_pp = ".$status_prioritet." ";
	}
	
	$query = $db->prepare("
		SELECT COUNT(id_broj_nd_kandidata) AS broj_prioritet 
		FROM idk_nd_kandidata
		WHERE ((status_nd_kandidata IN (1) AND pstatus_nd_kandidata IN (1,2,3,4,5,6,7,8,9,10,11,12)) OR (status_nd_kandidata IN (7))) AND ".$uslov."
	");
	$query->execute();
	$row = $query->fetch();
	$broj = $row["broj_prioritet"];
	return $broj;
}

function getBrojPrioritetPartnerKandidataDIPLR($type, $zaposlenik){
	Global $db;
	//$type -> 0 - samo od logiranog zaposlenika, 1 - svi prioriteti
	
	if($type == 1){
		$team = getLoggedEmployeeTeam();
		$employess_id_team = implode(", ", getIdOfEmployeeTeam($team));
		if($team == 1){
			$uslov_sql_zap = " zaduzen_zaposlenik_nd_kandidata is not null";
		}else{
			$uslov_sql_zap = " zaduzen_zaposlenik_nd_kandidata IN (".$employess_id_team.")";
		}
		$uslov = " status_nd_kandidata = 1 AND pstatus_nd_kandidata = 1 AND ".$uslov_sql_zap."";
	}else{
		$uslov = " zaduzen_zaposlenik_nd_kandidata = ".$zaposlenik." AND status_nd_kandidata = 1 AND pstatus_nd_kandidata = 1 ";
	}
	
	$query = $db->prepare("
		SELECT COUNT(id_broj_nd_kandidata) AS broj_partner 
		FROM idk_nd_kandidata
		WHERE povijest_nd_kandidata = 3 AND kandidat_idd is not null AND ".$uslov."
	");
	$query->execute();
	$row = $query->fetch();
	$broj = $row["broj_partner"];
	return $broj;
}

function getBrPrioritetFacebookInstagramDIPLR($type, $zaposlenik){
	Global $db;
	//$type -> 0 - samo od logiranog zaposlenika, 1 - svi prioriteti
	
	if($type == 1){
		$team = getLoggedEmployeeTeam();
		$employess_id_team = implode(", ", getIdOfEmployeeTeam($team));
		if($team == 1){
			$uslov_sql_zap = " zaduzen_zaposlenik_nd_kandidata is not null";
		}else{
			$uslov_sql_zap = " zaduzen_zaposlenik_nd_kandidata IN (".$employess_id_team.")";
		}
		$uslov = " status_nd_kandidata = 1 AND pstatus_nd_kandidata = 1 AND ".$uslov_sql_zap."";
	}else{
		$uslov = " zaduzen_zaposlenik_nd_kandidata = ".$zaposlenik." AND status_nd_kandidata = 1 AND pstatus_nd_kandidata = 1 ";
	}
	
	$query = $db->prepare("
		SELECT COUNT(id_broj_nd_kandidata) AS broj_fbins 
		FROM idk_nd_kandidata
		WHERE povijest_nd_kandidata = 5 AND (kampanja_id = 100 OR kampanja_id = 157) AND ".$uslov."
	");
	$query->execute();
	$row = $query->fetch();
	$broj = $row["broj_fbins"];
	return $broj;
}

function updateStatusPonovnaPrijavaDIPL($id_kand){
	Global $db;
	$query = $db->prepare("SELECT status_pp FROM idk_nd_kandidata WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata");
	$query->execute(array(':id_broj_nd_kandidata' => $id_kand));
	if(($query->rowCount()) != 0){
		$row_podaci = $query->fetch();
		$status_pp = $row_podaci["status_pp"];
		if($status_pp != 3){
			$update = $db->prepare("
				UPDATE idk_nd_kandidata
				SET status_pp = :status_pp
				WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata
			");
			$update->execute(array(
				':id_broj_nd_kandidata' => $id_kand,
				':status_pp' => 3
			));
		}
	}
}

function insertPonovnePrijaveDIPL($id_kan, $ime_kan, $prezime_kan, $mobitel_kan, $email_kan, $povijest, $povijest_vrsta, $kampanja){
	Global $db;
	//$id_kan - označava ID kandidata iz tabele idk_nd_kandidata
	//$ime_kan - označava podatke koje je kandidat unio prilikom ponovne prijave
	//$prezime_kan - označava podatke koje je kandidat unio prilikom ponovne prijave
	//$mobitel_kan - označava podatke koje je kandidat unio prilikom ponovne prijave
	//$email_kan - označava podatke koje je kandidat unio prilikom ponovne prijave
	//$povijest - povijest u zavisnosti od forme
	//$povijest_vrsta - povijest vrsta u zavisnosti od forme
	//$kampanja - id kampanje iz tabele idk_kampanje_dipl

	/*
		Pravim ovaj log da bi uhvatio ponovne prijave kod kojih se desava problem
		status_pp se update-uje na 1 ili 2
		idk_ponovne_prijave ne izvrsi se insert za ponovnu prijavu
	*/

	$log_desc = "PONOVNE PRIJAVE DIPL -> Izvrsen poziv funkcije sa parametrima: (' ".$id_kan." ', ' ".$ime_kan." ', ' ".$prezime_kan." ', ' ".$mobitel_kan." ', ' ".$email_kan." ', ' ".$povijest." ', ' ".$povijest_vrsta." ', ' ".$kampanja." ')";
	$log_date = date("Y-m-d H:i:s"); 

	$log_query = $db->prepare("
		INSERT INTO idk_logs 
			(log_employeeid, log_desc, log_date)
		VALUES
			(:log_employeeid, :log_desc, :log_date)
	");

	$log_query->execute(array(
		':log_employeeid' => 75,
		':log_desc' => $log_desc,
		':log_date' => $log_date
	));

	$status_vr = 0;
	$trenutno_vrijeme = date("Y-m-d H:i:s");
	if($id_kan != "" AND $ime_kan != "" AND $prezime_kan != "" AND $mobitel_kan != ""){
		$query_podaci = $db->prepare("SELECT status_pp, vrijeme_kreiranja_nd_kandidata, zaduzen_zaposlenik_nd_kandidata FROM idk_nd_kandidata WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata");
		$query_podaci->execute(array(':id_broj_nd_kandidata' => $id_kan));
		if(($query_podaci->rowCount()) != 0){
			$row_podaci = $query_podaci->fetch();
			$status_pp = $row_podaci["status_pp"];
			$vrijeme_prijave1 = $row_podaci["vrijeme_kreiranja_nd_kandidata"];
			$zaduzen = $row_podaci["zaduzen_zaposlenik_nd_kandidata"];
			$date_pocetak = date("Y-m-d")." 00:00:00";
			$date_kraj = date("Y-m-d")." 23:59:59";
			$provjera_pp_query = $db->prepare("
				SELECT COUNT(id_pp) AS broj_pp_danas
				FROM idk_ponovne_prijave
				WHERE id_kandidata = :id_kandidata AND datum_pp BETWEEN '".$date_pocetak."' AND '".$date_kraj."'
			");
			$provjera_pp_query->execute(array(
				':id_kandidata' => $id_kan
			));
			$row_provjera_pp = $provjera_pp_query->fetch();
			$broj_prijava_danas = $row_provjera_pp["broj_pp_danas"];
			if($broj_prijava_danas == 0){
				//Odredi vrijeme proteklo od prve prijave START
				$diff = strtotime($trenutno_vrijeme) - strtotime($vrijeme_prijave1);
				$days = floor($diff/86400);
				$hours = floor(($diff - $days * 86400) / 3600);
				$minutes = floor(($diff - $days * 86400 - $hours * 3600) / 60);
				$seconds = floor($diff - $days * 86400 - $hours * 3600 - $minutes * 60);
				//Odredi vrijeme proteklo od prve prijave END
				if($days == 0 AND $hours == 0 AND $minutes == 0 AND $seconds <= 59){
					//Ne radi nista ako je manje od minute
					return 0;
				}else{
					//Uslovi za update statusa ponovne prijave START
					if($days < 1 AND $hours <= 23 AND $minutes <= 59 AND $seconds <= 59){
						$status_vr = 2;
					}else{
						$status_vr = 1;
					}
					//Uslovi za update statusa ponovne prijave END
					
					//Update status_pp u tabelu idk_nd_kandidata START
					$query_update = $db->prepare("
						UPDATE idk_nd_kandidata
						SET status_pp = :status_pp
						WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata
					");
					$query_update->execute(array(':id_broj_nd_kandidata' => $id_kan, ':status_pp' => $status_vr));
					//Update status_pp u tabelu idk_nd_kandidata END
					
					$podaci_unos = "Prilikom ponovne prijave kandidat je unio podatke: Ime: ".$ime_kan." Prezime: ".$prezime_kan." Email: ".$email_kan." Kontakt: ".$mobitel_kan."";
					//Insert u tabelu ponovnih prijava START
					$query_insert = $db->prepare("
						INSERT INTO idk_ponovne_prijave 
							(porijeklo_pp, id_kandidata, employee_pp, datum_pp, povijest_pp, povijest_vrsta_pp, kampanja_pp, opis_pp) 
						VALUES 
							(:porijeklo_pp, :id_kandidata, :employee_pp, :datum_pp, :povijest_pp, :povijest_vrsta_pp, :kampanja_pp, :opis_pp)
					");
					$query_insert->execute(array(
						':porijeklo_pp' => 1,
						':id_kandidata' => $id_kan,
						':employee_pp' => $zaduzen,
						':datum_pp' => date("Y-m-d H:i:s"),
						':povijest_pp' => $povijest,
						':povijest_vrsta_pp' => $povijest_vrsta,
						':kampanja_pp' => $kampanja,
						':opis_pp' => $podaci_unos
					));
					//Insert u tabelu ponovnih prijava END
					return 1;
				}
			}else{
				return 0;
			}
		}
	}
}
function getIconTeam($id_zap_icon){
    Global $db;
    $q_team_zap = $db->prepare("SELECT employee_team FROM idk_employees WHERE employee_id = :employee_id");
    $q_team_zap->execute(array(':employee_id' => $id_zap_icon));
    $r_team_zap = $q_team_zap->fetch();
    $team_zap_id = $r_team_zap["employee_team"];
    
    $icon_team = $db->prepare("SELECT skraceni_naziv_t, boja_t FROM idk_timovi WHERE id_t = :id_t");
    $icon_team->execute(array(':id_t' => $team_zap_id));
    $r_icon_team = $icon_team->fetch();
    
    return '<span style = "background-color: '.$r_icon_team["boja_t"].'; color:white; border-radius: 2.25rem;" class="label label-default material-label material-label_default main-container__column text-left">'.$r_icon_team["skraceni_naziv_t"].'</span>';
}


/*********************************************
				GEODECODING -- START
*********************************************/
function getDistanceFromTwoGeoLocations($lat1,$long1,$lat2,$long2){ //  ‘haversine FORMULA -> for more info and JS version: https://www.movable-type.co.uk/scripts/latlong.html

	$rad = pi()/180;
	$circ_earth = 6371; 

	$fi_first = $lat1 * $rad; 
	$fi_second = $lat2 * $rad; 
 
	$deltaFi = ($lat2-$lat1)* $rad;
	$deltaLamb = ($long2-$long1)*$rad;

	$a = (sin($deltaFi/2) * sin($deltaFi/2)) +
			  (cos($fi_first) * cos($fi_second)) *
			  (sin($deltaLamb/2) * sin($deltaLamb/2));
			  
	$c = 2 * atan2(sqrt($a), sqrt(1-$a));

	$distance = $circ_earth * $c; // in metres
	return $distance;
}


function getLongLatFromAddress($adresa){ /*** e.g "15. Majevičke brigade bb, Odžak" // vraća *NIZ* sa koordinatama geografske širine i dužine u decimalnom obliku ***/
	$geolokacija = array();
	$params = array(
		  'location' => $adresa,
		  'options' => array(
			'thumbMaps' => false,
			'maxResults' => 1,
		  )
		);
	$queryString = json_encode($params);
	
	$url = 'http://www.mapquestapi.com/geocoding/v1/address?key=6ZBXbHDW8rnfnxuPegPPTK0GAXxt54LO'; // GEODECODING API -> ISMAIL SULJIC PERSONAL KEY www.mapquestapi.com

	// FETCH/cURL API RESPONSE
	$ch = curl_init($url);

	curl_setopt($ch, CURLOPT_POSTFIELDS, $queryString);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$json = curl_exec($ch);
	curl_close($ch);
	$apiResult = json_decode($json, true); // API RETURNS JSON OBJECT
	$latitutde = $apiResult["results"][0]['locations'][0]['displayLatLng']['lat'];
	$longitude = $apiResult["results"][0]['locations'][0]['displayLatLng']['lng'];	
	
	array_push($geolokacija,$latitutde);
	array_push($geolokacija,$longitude);
	return $geolokacija;
	
}


function getNajblizaPoslovnica($geolokacija_klijent, $geolokacije_mikrofin_poslovnica,$adrese,$naziv_grada){ /***  Proračunava udaljenost između poslovnica mikrofina i vraća adresu najbliže ***/
	$min = 100000;
	foreach($geolokacije_mikrofin_poslovnica as $index => $geolokacija_poslovnice){
		$distance = getDistanceFromTwoGeoLocations($geolokacija_klijent[0],$geolokacija_klijent[1],$geolokacija_poslovnice[0],$geolokacija_poslovnice[1]);
													//  [0] je geo. širina, [1] je geo. dužina
		if($distance < $min){ 
			$min = $distance;
			$najblizaLokacija = $adrese[$index].", ".$naziv_grada[$index]; // Cazinskih brigada bb, Cazin 
		}
	}

	return $najblizaLokacija;
} 


function getAdresaPoslovniceString($kandidat_id){ // skup ostalih funkcija, čija je povratna informacija string/adresa najbliže poslovnice
	global $db;
	
	$read_query = $db->prepare("
								SELECT ulica_nd_kandidata, postanski_broj_nd_kandidata, grad_nd_kandidata
								FROM idk_nd_kandidata
								WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata
								AND ulica_nd_kandidata IS NOT NULL
								AND postanski_broj_nd_kandidata IS NOT NULL
								AND grad_nd_kandidata IS NOT NULL
							   ");
	$read_query->execute(array(
						"id_broj_nd_kandidata" => $kandidat_id,
						));
	$row = $read_query->fetch();
	if(empty($row)) return false; // AKO NISU PRONAĐENI PODACI ZA KLIJENTA -> BREAK
	else{
		$adresaKlijenta = $row['ulica_nd_kandidata'].", ".$row['postanski_broj_nd_kandidata']." ".$row['grad_nd_kandidata'].", Bosnia and Herzegovina";
		$geolokacija_klijent = getLongLatFromAddress($adresaKlijenta);
		
		/***************************************************
					MIKROFIN PODACI KROZ NIZ
		***************************************************/
		
		$geolokacije_mikrofin_poslovnica = array(array("44.4505556","18.6498391"),array("44.4245639","18.0296972"),array("43.3796777","17.5722674"),array("44.2833566","17.0836473"),array("45.061131","18.4676883"),array("44.7278346","17.3210535"),array("43.1094117","17.7008145"),array("44.3817866","19.1005003"),array("44.2030585","17.9035199"),array("44.439056","18.14636"),array("43.899786","18.346205"),array("44.1799774","18.9396309"),array("43.7834499","19.291263"),array("43.9918582","18.1763276"),array("45.1795326","15.8084053"),array("44.69303","18.9926402"),array("44.5345007","18.6976815"),array("42.7128656","18.3407097"),array("44.2247595","17.6628855"),array("44.6098227","17.9861017"),array("44.6064196","17.8575826"),array("43.8604745","18.4240454"),array("44.7409247","17.8640454"),array("44.7055874","18.4899621"),array("45.0946547","17.5185863"),array("43.9348931","18.7925161"),array("43.8476269","18.3750951"),array("44.7659381","16.6613025"),array("43.6177279","19.3637144"),array("43.7988425","18.999921"),array("44.8689996","17.6597551"),array("44.976359","16.7031514"),array("43.8076322","18.5753076"),array("45.0101422","18.3240652"),array("44.1706613","17.6586709","17"),array("45.0488309","16.3749058"),array("45.0520301","17.305912"),array("43.2580066","18.1118443"),array("44.4162536","17.0820365"),array("43.341772","17.799025"),array("44.9590858","18.3011118"),array("44.1660267","19.0753445"),array("44.5477568","18.0975586"),array("44.5367863","18.5240798"),array("43.1989428","17.5444784"),array("43.8229401","17.0021581"),array("44.9063727","17.2979654"),array("45.1844964","16.8054771"),array("44.6221007","17.3705421"),array("45.2187322","16.5447538"),array("43.6515895","17.9630821"),array("44.5331364","16.7722338"),array("44.4434434","18.8735904"),array("44.1274585","18.1150968"),array("43.8587589","18.4166448"),array("43.8307774","18.3040917"),array("43.8577342","18.4246909"),array("43.8213317","18.1996884"),array("44.7020139","18.3067632"),array("45.1444295","17.2486729"),array("44.8787608","18.4220488"),array("43.6653435","18.9768622"),array("43.1662348","18.5329613"),array("43.5085168","18.7737009"),array("44.3762646","16.3797435"),array("44.7313545","18.0895384"),array("44.9795549","17.9044671"),array("44.9657449","15.9370265"),array("45.0625601","16.0292753"),array("44.0554348","17.4449707"),array("44.87207","18.814103"),array("44.0181484","18.2591066"),array("44.1834999","19.3256926"),array("44.550306","16.3621873"),array("45.147613","17.9968416"),array("44.8823261","16.1484604"),array("42.8789338","18.42734"),array("44.7616949","19.2070714"),array("44.8135893","15.8625052"),array("44.4055278","18.5212835"),array("44.7719582","17.194268"));
		$gradovi = array("Živinice","Žepče","Široki Brijeg","Šipovo","Šamac","Čelinac","Čapljina","Zvornik","Zenica","Zavidovići","Vogošća","Vlasenica","Višegrad","Visoko","Velika Kladuša","Ugljevik","Tuzla","Trebinje","Travnik","Tešanj","Teslić","Stari Grad Sarajevo","Stanari","Srebrenik","Srbac","Sokolac","Sarajevo","Sanski Most","Rudo","Rogatica","Prnjavor","Prijedor","Pale","Odžak","Novi Travnik","Novi Grad","Nova Topola","Nevesinje","Mrkonjić Grad","Mostar","Modriča","Milići","Maglaj","Lukavac","Ljubuški","Livno","Laktaši","Kozarska Dubica","Kotor Varoš","Kostajnica","Konjic","Ključ","Kalesija","Kakanj","Jelah","Ilidža","I. Sarajevo","Hadžići","Gračanica","Gradiška","Gradačac","Goražde","Gacko","Foča","Drvar","Doboj","Derventa","Cazin","Bužim","Bugojno","Brčko","Breza","Bratunac","Bosanski Petrovac","Bosanski Brod","Bosanska Krupa","Bileća","Bijeljina","Bihać","Banovići","Banja Luka"); 
		$adrese = array("Gradski bulevar “Nesib Malkić”, poslovni objekat Bulevar","Prva br.17","Trnska cesta br. 57","Vojvode Radomira Putnika bb","Svetosavska br.11","Cara Lazara 23","Ante Starčevića bb","Svetog Save “SPO Tri česme” (preko puta škole)","Školska bb","Mehmed Paše Sokolovića br.88","Omladinska c","Jurišnog odreda br.46","Ul. Kosovska br. 1.","Mehmeda Skopljaka 83","Zuhdije Žalića br. 1","Karađorđeva bb","Bećarevac 1","Sokolska br.1","Erika Brandisa bb (Zvijezda I)","Osmana Pobrića bb","Karađorđeva br. 7","Edhema Mulabdića br. 6","Donja Ostružnja bb","21. Srebreničke brigade bb","Mome Vidovića br. 20","Cara Lazara bb","Azize Šaćirbegović 122","Aleja Šehida br. 1","Trg Slobode br. 1","Omladinska br. 9","Trg sprskih boraca bb","Kralja Petra I oslobodioca 37","4. juni bb","15. Majevičke brigade bb","Kralja Tvrtka 5","Karađorđa Petrovića br. 25","Banjalučki put bb","Trg Blagoja Parovića bb","Karađorđeva bb","Biskupa Čule bb(preko puta benzinske pumpe Lamina)","Svetosavska bb","Dragana Brkića 6a","Viteška bb","Redžepa Efendije Muminhodžića br.2","Fra Lovre Šitovića bb","Župana Želimira bb","Majke Jugovića 2","Svetosavska 2","Cara Dušana bb","Ranka Šipke br. 6","Suhi do br.1","Branilaca BiH bb","Kalesijskih brigada bb","Alije Izetbegovića bb","Maršala Tita br. 42","Rustem-pašina do 21","Sime Milutinovića Sarajlije br. 4","Hadželi 150","Bosanskih kraljeva bb","Mis A.P. Irbi 5/II","Husein-kapetana Gradaščevića bb","Ferida Dizdarevića bb","Solunskih dobrovoljaca bb","Principova bb","Titova br. 21","Nikole Pašića bb","Kralja Petra I bb","Cazinskih brigada bb","Generala Izeta Nanića bb","Bosanska 18","Uzunovića bb","Bosanska 26","Gavrila Principa 3","Srebrenička bb","Svetog Save 27","Sokak br. 6","Obilićev vjenac bb","Cara Uroša 6","Harmanska bb","Božićka banovićka do br.8","Vase Pelagića 22");
		 
		/*****************   END   *************************/
		
		return getNajblizaPoslovnica($geolokacija_klijent,$geolokacije_mikrofin_poslovnica,$adrese,$gradovi);
	}
}

/*********************************************
				GEODECODING -- END
*********************************************/
	
function checkIfTempFileExists($kandidat_id){ // AKO ZA KAND_ID IMA FAJL -> VRATI PUTANJU | .htaccess modificirano
	$dir = new DirectoryIterator($_SERVER["DOCUMENT_ROOT"]."/files/public_temp_files/predracuni_dipl");
	foreach ($dir as $fileinfo) {
			if(substr($fileinfo->getFilename(),0,strpos($fileinfo->getFilename(),"_")) == $kandidat_id)
				return "https://crm.job-step.com/predracun/".$fileinfo->getFilename();
	}
	return false;
}
function createTempFile($kandidat_id){ // kreiraj novi fajl i vrati putanju | .htaccess modificirano
		if(!checkIfTempFileExists($kandidat_id)){
			Global $db;
			$find_file = $db->prepare("SELECT pr_file 
									   FROM idk_predracuni
									   WHERE pr_kandidat_id = :pr_kandidat_id
									   AND pr_rata = 1 
									   AND pr_status != 0
									  ");
			$find_file->execute(array(
						':pr_kandidat_id' => $kandidat_id
						));
						
			$query_result = $find_file->fetch();
			if($query_result['pr_file']){
				$vrijeme_kreiranja =  preg_replace('/[^0-9]+/','', date("Y-m-d H:i:s"));
				$nasumican_broj = mt_getrandmax() ;
				$random_filename = $kandidat_id."_".$vrijeme_kreiranja.$nasumican_broj;
				
				$file = $_SERVER["DOCUMENT_ROOT"].'/files/predracuni_dipl/'.$query_result['pr_file'];
				$newfile = $_SERVER["DOCUMENT_ROOT"].'/files/public_temp_files/predracuni_dipl/'.$random_filename.".pdf";
				
				if (!copy($file, $newfile)) 
						return false;
				else 
						return "https://crm.job-step.com/predracun/".$random_filename.".pdf"; // temp privremni link za kandidata
			}else 
					return false;
		}
		else return checkIfTempFileExists($kandidat_id);
}

function getDiplKandidatPhoneNoREGX($kandidat_id){ // VRATI BROJ TELEFONA KANDIDATA BEZ + DIPL
	global $db;
	
	$query = $db->prepare("SELECT mobilni_nd_kandidata
							FROM idk_nd_kandidata
							WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata
							");
	$query->execute(array(
				":id_broj_nd_kandidata" => $kandidat_id
				));
	$result = $query->fetch();
	
	$telefon = preg_replace('/[^0-9]+/', '', $result['mobilni_nd_kandidata']);
	return $telefon;
}

function getPartnerPhoneNoREGX($jp_id){ // VRATI BROJ TELEFONA KANDIDATA BEZ + DIPL
	global $db;
	
	$query = $db->prepare("SELECT jp_brtelefona
							FROM idk_jobstep_partners
							WHERE jp_id = :jp_id
							");
	$query->execute(array(
				":jp_id" => $jp_id
				));
	$result = $query->fetch();
	
	$telefon = preg_replace('/[^0-9]+/', '', $result['jp_brtelefona']);
	return $telefon;
}
/**************************************************************************************/

/*********************************************
				VIBER/SMS SLANJE -- START
*********************************************/
function viberKandidateMikrofinData($kandidat_id){ // VIBER SLANJE SA SMS->BACKUP-OM |		SAMO ZA MIKROFIN
		$link = "".geSiteUrlr()."dokumenti/mikrofin/".$kandidat_id;
		
		/****************************
		TESTNI PARAMETARI: 
		****************************/
		$broj = getDiplKandidatPhoneNoREGX($kandidat_id);
		$imageURL = "".geSiteUrlr()."images/viber_preuzmi_dokumentaciju.png";
		$buttonText = "PREDRAČUN";
		$buttonURL = $link;
		$mikrofinPoslovnica = getAdresaPoslovniceString($kandidat_id);
		if($mikrofinPoslovnica){
			$textViber = "
						Poštovani,
						kako bismo Vam olakšali naredni korak u procesu nostrifikacije Vaše diplome, 
						u nastavku Vam šaljemo adresu najbliže poslovnice Mikrofina. \n
						".$mikrofinPoslovnica." \n
						Dokumente koje trebate ponijeti prilikom Vaše posjete Mikrofin agenciji su: \n
						1. CIPS prijava
						2. Kopija lične karte
						3. Tri zadnje platne liste ili tri zadnja izvoda iz banke \n
						Vaš predračun za nostrifikaciju možete preuzeti klikom na dugme PREDRAČUN. \n
						Hvala na ukazanom povjerenju!
						Jobstep Team
						";
			$textSMS = "Poštovani,
						kako bismo Vam olakšali naredni korak u procesu nostrifikacije Vaše diplome, 
						u nastavku Vam šaljemo adresu najbliže poslovnice Mikrofina. \n
						".$mikrofinPoslovnica." \n
						Dokumente koje trebate ponijeti prilikom Vaše posjete Mikrofin agenciji su: \n
						1. CIPS prijava
						2. Kopija lične karte
						3. Tri zadnje platne liste ili tri zadnja izvoda iz banke \n
						Vaš predračun za nostrifikaciju možete preuzeti na sljedećem linku: ".$link.". \n
						Hvala na ukazanom povjerenju!
						Jobstep Team";
		}else{
			$textViber = "
						Poštovani,
						kako bismo Vam olakšali naredni korak u procesu nostrifikacije Vaše diplome, 
						u nastavku Vam šaljemo spisak dokumenata koje trebate ponijeti, prilikom Vaše posjete Mikrofin agenciji: \n
						1. CIPS prijava 
						2. Kopija lične karte
						3. Tri zadnje platne liste ili tri zadnja izvoda iz banke \n
						Najbližu mikrofin poslovnicu možete pronaći na sljedećoj web stranici : 
						https://mikrofin.com/mapa-poslovnica/ \n
						Vaš predračun za nostrifikaciju možete preuzeti klikom na dugme PREDRAČUN \n
						Hvala na ukazanom povjerenju!
						Jobstep Team
						";
			$textSMS = "
						Poštovani,
						kako bismo Vam olakšali naredni korak u procesu nostrifikacije Vaše diplome, 
						u nastavku Vam šaljemo spisak dokumenata koje trebate ponijeti, prilikom Vaše posjete Mikrofin agenciji: \n
						1. CIPS prijava 
						2. Kopija lične karte
						3. Tri zadnje platne liste ili tri zadnja izvoda iz banke \n
						Najbližu mikrofin poslovnicu možete pronaći na sljedećoj web stranici : 
						https://mikrofin.com/mapa-poslovnica/ \n
						Vaš predračun za nostrifikaciju možete preuzeti na sljedećem linku: ".$link.". \n
						Hvala na ukazanom povjerenju!
						Jobstep Team
						";
		}
		/****************************
		KRAJ
		****************************/
		$active_provider = getActiveProviderForSendingMessages(6);
		if ($active_provider == 1) {
			$curl = curl_init();
			$params = array(
				
			"scenarioKey" => "7A32331B2103607D2F890C04FEB34942",
			"destinations" => array(
				"to" => array(
					"phoneNumber" => $broj,
					)
			),
			"sms" => array(
				"text" =>$textSMS,
				),
			"viber" => array(
				"text" => $textViber,
				"imageURL" => $imageURL,
				"buttonText" => $buttonText,
				"buttonURL" => $buttonURL,
				"isPromotional" => "true"
			)
			);


			$data = json_encode($params);


			curl_setopt_array($curl, array(
			CURLOPT_URL => "https://ej8w3r.api.infobip.com/omni/1/advanced",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => $data,
			CURLOPT_HTTPHEADER => array(
				"accept: application/json",
				"authorization: Basic ZWJlbmRlcjE6RUJlbmRlcjY0MzI=",
				"content-type: application/json"
			),
			));

			$response = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);
		} else {
			$phoneNumber = checkPhoneNumberForNTH($broj);
			$params = array(
				"channels" => array(
					"VIBER",
					"SMS"
				),
				"destinations" => array(
					array(
						"phoneNumber" => $phoneNumber
					)
				),
				"viber" => array(
					"priority" => 1,
					"sender" => "Jobstep Int",
					"buttonCaption" => $buttonText,
					"buttonAction" => $buttonURL,
					"image" => $imageURL,
					"text" => $textViber,
					"ttl" => 14440,
					"label" => "promotion"
				),
				"sms" => array(
					"priority" => 2,
					"sender" => "Jobstep Int",
					"text" => $textSMS
				)
			);
			$params_encode = json_encode($params);
			$response = sendMessageViaNTH($params_encode);
		}
	
}


/*********************************************
				VIBER/SMS SLANJE -- END
*********************************************/
//Funkcije za dodjelu LEAD-ova na DIPL modulu START

function updateLimitCNT($id_agenta){
	Global $db;
	if(isset($id_agenta)){
		$query1 = $db->prepare("
			UPDATE idk_nd_limiti
			SET	lt_dnevni_cnt = lt_dnevni_cnt + 1, lt_sedmicni_cnt = lt_sedmicni_cnt + 1, lt_mjesecni_cnt = lt_mjesecni_cnt + 1
			WHERE lt_emp_id = :lt_emp_id
		");
		$query1->execute(array(
			':lt_emp_id' => $id_agenta
		));
	}
}

function updateZadnjiDobioStariNovi($id_novi, $id_stari, $drzava){
	Global $db;
	if(isset($id_stari)){
		if($id_stari != NULL){
			$query2 = $db->prepare("
				UPDATE idk_nd_limiti
				SET lt_zadnji_dobio_".$drzava." = :vrijednost
				WHERE lt_emp_id = :lt_emp_id
			");
			$query2->execute(array(
				':lt_emp_id' => $id_stari,
				':vrijednost' => NULL
			));
		}
	}
	if(isset($id_novi)){
		$query1 = $db->prepare("
			UPDATE idk_nd_limiti
			SET lt_zadnji_dobio_".$drzava." = 1
			WHERE lt_emp_id = :lt_emp_id
		");
		$query1->execute(array(
			':lt_emp_id' => $id_novi
		));
	}
}
function sendMailNotifyBug($broj_mob){
	//send mail curama iz financija
	$mail = new PHPMailer;
	$mail->isSMTP();
	// try {
		
		$mail->Host = 'smtp.gmail.com';                 // Specify main and backup SMTP servers
		$mail->SMTPAuth = true;                         // Enable SMTP authentication
		$mail->Username = 'support@job-step.com';        // SMTP username
		$mail->Password = 'eooc nnxo aylp lqkh';          // SMTP password
		$mail->SMTPSecure = 'ssl';                      // Enable TLS encryption, `ssl` also accepted
		$mail->Port = 465;                              // TCP port to connect to
		$mail->CharSet = 'UTF-8';

		//Recipients
		$mail->setFrom('support@job-step.com', 'JobStep');
		
		$mail->addAddress('e.bender@job-step.com');		// Add a recipient

		
		$mail->Subject ="ND kandidat usao, allgoritam zaobisao kategoriju";
		$mail->Body    = $broj_mob;
		$mail->AltBody = "";
		$mail->send();
		// if(!$mail->send()) {
			// echo 'Message could not be sent.';
			// echo 'Mailer Error: ' . $mail->ErrorInfo;
		// }else{
			// // echo "Mail sent";
		// }
	// } catch (phpmailerException $e) {
		// echo $e->errorMessage(); //Pretty error messages from PHPMailer
	// }
}
function getDodjeliAgentuDIPLR($br_telefona, $campaign_id = NULL, $category = NULL){
	Global $db;
	// $campaign_id = NULL; // jelkonadu ZAKOMENTARISI
	if(isset($br_telefona)){
		$broj_telefona = substr($br_telefona, 1, 3);
		if(strpos($broj_telefona, "387") !== false){
			//Srbija
			$drzava = "bih";
		}else if(strpos($broj_telefona, "381") !== false){
			//Bosna
			$drzava = "srb";
		}else if(strpos($broj_telefona, "49") !== false){
			//Njemacka
			$drzava = "de";
		}else{
			//Ostalo
			$drzava = "ostalo";
		}
	
	
	
		if(is_null($campaign_id)){
			sendMailNotifyBug($br_telefona); // jelkonadu UKLONI KOMENTAR
			//Prvo je potrebno provjeriti da li medju zaposlenima postoji zaposlenik koji ispunjava uslove
			//Odnosno da li su mu brojaci dodijeljenih kandidata manji od postavljenog limita
			//U slucaju ako upit nadje takve zaposlenike (brojac != 0) onda se nastavlja algoritam dalje
			//U slucaju ako upit nije nasao takve zaposlenike (brojac == 0) onda se automatski kandidat stavlja u skladiste (jer su svi zaposlenici dostigli limit)
			$query0 = $db->prepare("
				SELECT count(lt_emp_id) as brojac0
				FROM idk_nd_limiti lim
				JOIN idk_employees emp
				ON lim.lt_emp_id = emp.employee_id 
				WHERE emp.employee_nostrifikacija_diploma = 1
				AND 
				lim.lt_drzava LIKE '%".$drzava."%'
				AND 
				(lt_dnevni_cnt < lt_dnevni AND lt_sedmicni_cnt < lt_sedmicni AND lt_mjesecni_cnt < lt_mjesecni)
				AND 
				(SELECT count(id_broj_nd_kandidata) FROM idk_nd_kandidata WHERE zaduzen_zaposlenik_nd_kandidata = lim.lt_emp_id AND status_nd_kandidata = 1 AND pstatus_nd_kandidata IN(1,6,7) ) < 60
				ORDER BY lt_emp_id ASC;
			");
			$query0->execute();
			$row0 = $query0->fetch();
			$brojac0_q0 = intval($row0["brojac0"]);
			if($brojac0_q0 != 0){
				//Ovdje se trazi zaposlenik koji je zadnji dobio kandidata ne uzimajuci u obzir uslove za limite
				//Uslov za limite se ne uzima u obzir jer zadnji koji je dobio kanidata moze biti zaposlenik koji je dostigao svoj limit (npr lt_dnevni_cnt == lt_dnevni)
				//Znaci kod prethodnog kandidata koji je usao on je imao npr. 4/5 i to mu je bio peti kanidat kojim je dostigao svoj limit i u trenutnoj iteraciji se mora uzeti u obzir
				$query1 = $db->prepare("
					SELECT lt_emp_id AS emp_id1, count(lt_emp_id) as brojac1
					FROM idk_nd_limiti 
					WHERE
					lt_zadnji_dobio_".$drzava." = 1
					ORDER BY lt_emp_id ASC;
				");
				$query1->execute();
				$row1 = $query1->fetch();
				$emp_id_q1 = intval($row1["emp_id1"]);
				$brojac_q1 = intval($row1["brojac1"]);
				if($brojac_q1 > 0){
					//Trazi narednog u nizu veceg od prethodnog 
					//Ako ga nadje njemu dodijeli kandidata
					//Ako ne nadje onda query3 
					$query2 = $db->prepare("
						SELECT MIN(lim.lt_emp_id) AS emp_id2, count(lim.lt_emp_id) AS brojac2
						FROM idk_nd_limiti lim 
						JOIN idk_employees emp 
						ON lim.lt_emp_id = emp.employee_id 
						WHERE 
							emp.employee_nostrifikacija_diploma = 1 
						AND 
							(lim.lt_dnevni_cnt < lim.lt_dnevni AND lim.lt_sedmicni_cnt < lim.lt_sedmicni AND lim.lt_mjesecni_cnt < lim.lt_mjesecni)
						AND 
							lim.lt_drzava LIKE '%".$drzava."%'
						AND 
							lim.lt_emp_id > :prethodni_zaduzeni
						AND 
							(SELECT count(id_broj_nd_kandidata) FROM idk_nd_kandidata WHERE zaduzen_zaposlenik_nd_kandidata = lim.lt_emp_id AND status_nd_kandidata = 1 AND pstatus_nd_kandidata IN(1,6,7) ) < 60
						ORDER BY lim.lt_emp_id ASC;
					");
					$query2->execute(array(
						':prethodni_zaduzeni' => $emp_id_q1
					));
					$row2 = $query2->fetch();
					$emp_id_q2 = $row2["emp_id2"];
					$brojac_q2 = $row2["brojac2"];
					if($brojac_q2 > 0){
						$stari_zaduzeni = $emp_id_q1;
						$novi_zaduzeni = $emp_id_q2; 
					}else{
						//Ako ne nadje veceg od prethodnog
						//Onda pretrazuje red od pocetka
						//Ako nadje dodijeli kandidata
						//Ako ne nadje ponovno ide u skladiste
						$query3 = $db->prepare("
							SELECT MIN(lim.lt_emp_id) AS emp_id3, count(lim.lt_emp_id) AS brojac3
							FROM idk_nd_limiti lim 
							JOIN idk_employees emp 
							ON lim.lt_emp_id = emp.employee_id 
							WHERE 
								emp.employee_nostrifikacija_diploma = 1 
							AND 
								(lim.lt_dnevni_cnt < lim.lt_dnevni AND lim.lt_sedmicni_cnt < lim.lt_sedmicni AND lim.lt_mjesecni_cnt < lim.lt_mjesecni)
							AND 
								lim.lt_drzava LIKE '%".$drzava."%' 
							AND 
								(SELECT count(id_broj_nd_kandidata) FROM idk_nd_kandidata WHERE zaduzen_zaposlenik_nd_kandidata = lim.lt_emp_id AND status_nd_kandidata = 1 AND pstatus_nd_kandidata IN(1,6,7) ) < 60
							ORDER BY lim.lt_emp_id ASC;
						");
						$query3->execute();
						$row3 = $query3->fetch();
						$emp_id_q3 = intval($row3["emp_id3"]);
						$brojac_q3 = intval($row3["brojac3"]);
						if($brojac_q3 > 0){
							$stari_zaduzeni = $emp_id_q1;
							$novi_zaduzeni = $emp_id_q3; 
						}else{
							$stari_zaduzeni = NULL;
							$novi_zaduzeni = 139;
						}
					}
					
				}else{
					//Ako ne nadje zadnjeg dodijeljenog
					//Onda provjerava cijeli niz i dodijeli prvom
					//Ako ne nadje onda ide u skladiste
					$query4 = $db->prepare("
						SELECT MIN(lim.lt_emp_id) AS emp_id4, count(lim.lt_emp_id) AS brojac4
						FROM idk_nd_limiti lim 
						JOIN idk_employees emp 
						ON lim.lt_emp_id = emp.employee_id 
						WHERE 
							emp.employee_nostrifikacija_diploma = 1 
						AND 
							(lim.lt_dnevni_cnt < lim.lt_dnevni AND lim.lt_sedmicni_cnt < lim.lt_sedmicni AND lim.lt_mjesecni_cnt < lim.lt_mjesecni)
						AND 
							lim.lt_drzava LIKE '%".$drzava."%'
						AND 
							(SELECT count(id_broj_nd_kandidata) FROM idk_nd_kandidata WHERE zaduzen_zaposlenik_nd_kandidata = lim.lt_emp_id AND status_nd_kandidata = 1 AND pstatus_nd_kandidata IN(1,6,7) ) < 60
						ORDER BY lim.lt_emp_id ASC;
					");
					$query4->execute();
					$row4 = $query4->fetch();
					$emp_id_q4 = intval($row4["emp_id4"]);
					$brojac_q4 = intval($row4["brojac4"]);
					if($brojac_q4 > 0){
						$stari_zaduzeni = NULL;
						$novi_zaduzeni = $emp_id_q4; 
					}else{
						$stari_zaduzeni = NULL;
						$novi_zaduzeni = 139;
					}
				}
			}else{
				$stari_zaduzeni = NULL;
				$novi_zaduzeni = 139;
			}
		}
		else{
			if($campaign_id != 0){

				$query_get_campaign_category = $db -> prepare("
					SELECT kd.kd_category
					FROM idk_kampanje_dipl kd
					WHERE kd.kd_id = ".$campaign_id.";
				");
				
				$query_get_campaign_category -> execute();
				$row_get_campaign_category = $query_get_campaign_category -> fetch();
				$campaign_category = $row_get_campaign_category['kd_category'];
			}else{
				$campaign_category = $category;
			}
			$query_get_next_employee = $db -> prepare("
				SELECT lim.lt_emp_id, lim.lt_dnevni_cnt
				FROM idk_nd_limiti lim
				JOIN idk_employees emp 
				ON lim.lt_emp_id = emp.employee_id 
				WHERE emp.employee_nostrifikacija_diploma = 1 
				AND (lim.lt_dnevni_cnt < lim.lt_dnevni AND lim.lt_sedmicni_cnt < lim.lt_sedmicni AND lim.lt_mjesecni_cnt < lim.lt_mjesecni)
				AND lim.lt_drzava LIKE '%".$drzava."%'
				AND lim.lt_campaign_category LIKE '%".$campaign_category."%'
				AND (
					SELECT count(id_broj_nd_kandidata) 
					FROM idk_nd_kandidata 
					WHERE zaduzen_zaposlenik_nd_kandidata = lim.lt_emp_id 
					AND status_nd_kandidata = 1 
					AND pstatus_nd_kandidata IN(1,6,7) 
				) < 60
				ORDER BY lim.lt_dnevni_cnt ASC;
			");
			
			$query_get_next_employee -> execute();
			$row_get_next_employee = $query_get_next_employee -> fetch();
			$novi_zaduzeni = $row_get_next_employee['lt_emp_id'];
			if(is_null($novi_zaduzeni)){
				$novi_zaduzeni = 139;
			}
			$stari_zaduzeni = NULL;
		}
	}else{
		$stari_zaduzeni = NULL;
		$novi_zaduzeni = 139;
	}
	//Update limit
	if($novi_zaduzeni != 139){
		updateLimitCNT($novi_zaduzeni);
		if(is_null($campaign_id))
			updateZadnjiDobioStariNovi($novi_zaduzeni, $stari_zaduzeni, $drzava);
	}
	$ret['novi'] = $novi_zaduzeni;
	$ret['stari'] = $stari_zaduzeni;
	return $ret;
}

function redirectZajednickiKandidat($id_kandidata){
	Global $db;
	if(isset($id_kandidata)){
		$query = $db->prepare("
			SELECT zaduzen_zaposlenik_nd_kandidata, tim_nd_kandidata
			FROM idk_nd_kandidata 
			WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata
		");
		$query->execute(array(
			':id_broj_nd_kandidata' => $id_kandidata
		));
		$row = $query->fetch();
		$zaduzen = $row["zaduzen_zaposlenik_nd_kandidata"];
		$team = $row["tim_nd_kandidata"];
		if($zaduzen == 139 AND $team == 0){
			header("Location: " . getSiteURL() . "dipl?page=prebaciKandidata&id=".$id_kandidata);
		}
	}
}

function getNazivRazlogOdbijanjaDIPL($id_razlog){
	//Funckija koja se koristi samo kod ispisa komunikacija
	Global $db;
	$query = $db->prepare("
		SELECT naziv_ro_bs, ponovno_zvanje_ro, br_dana_ro
		FROM idk_ro_usluge
		WHERE id_ro = :id_ro
	");
	$query->execute(array(
		':id_ro' => $id_razlog
	));
	$row = $query->fetch();
	$naziv = $row['naziv_ro_bs'];
	$ponovo = $row['ponovno_zvanje_ro'];
	$dana = $row['br_dana_ro'];
	if($ponovo == 1){
		$ispis = ' '.$naziv.' - Pozvati nakon '.$dana.' dan/a.';
	}
	else{
		$ispis = ' '.$naziv.' - Arhiviran kandidat.';
	}
	
	return $ispis;
}

//Funkcije za dodjelu LEAD-ova na DIPL modulu END

//Funkcije za broj kandidata po statusi i drzavi za razlicita skladista
function getBrojKandidata($status, $drzava, $tim){
	
	Global $db;
	if($status == 11){
		$status_uslov = " status_nd_kandidata = 1 AND pstatus_nd_kandidata = 1 ";
	}else if($status == 12){
		$status_uslov = " status_nd_kandidata = 1 AND pstatus_nd_kandidata = 2 ";
	}else if($status == 13){
		$status_uslov = " status_nd_kandidata = 1 AND pstatus_nd_kandidata = 3 ";
	}else if($status == 14){
		$status_uslov = " status_nd_kandidata = 1 AND pstatus_nd_kandidata = 4 ";
	}else if($status == 15){
		$status_uslov = " status_nd_kandidata = 1 AND pstatus_nd_kandidata = 5 ";
	}else if($status == 16){
		$status_uslov = " status_nd_kandidata = 1 AND pstatus_nd_kandidata = 6 ";
	}else if($status == 17){
		$status_uslov = " status_nd_kandidata = 1 AND pstatus_nd_kandidata = 7 ";
	}else if($status == 18){
		$status_uslov = " status_nd_kandidata = 1 AND pstatus_nd_kandidata = 8 ";
	}else if($status == 19){
		$status_uslov = " status_nd_kandidata = 1 AND pstatus_nd_kandidata = 9 ";
	}else if($status == 110){
		$status_uslov = " status_nd_kandidata = 1 AND pstatus_nd_kandidata = 10 ";
	}else if($status == 111){
		$status_uslov = " status_nd_kandidata = 1 AND pstatus_nd_kandidata = 11 ";
	}else if($status == 112){
		$status_uslov = " status_nd_kandidata = 1 AND pstatus_nd_kandidata = 12 ";
	}else if($status == 2){
		$status_uslov = " status_nd_kandidata = 2 ";
	}else if($status == 3){
		$status_uslov = " status_nd_kandidata = 3 ";
	}else if($status == 4){
		$status_uslov = " status_nd_kandidata = 4 ";
	}else if($status == 5){
		$status_uslov = " status_nd_kandidata = 5 ";
	}else if($status == 6){
		$status_uslov = " status_nd_kandidata = 6 ";
	}else if($status == 7 || $status == 77){
		$status_uslov = " status_nd_kandidata = 7 ";
	}else{
		$status_uslov = " status_nd_kandidata is not null ";
	}
	
	if($drzava == "000")  // za sve drzave
		$telefon_uslov = "";
	elseif($drzava == "111") // za ostale drzave
		$telefon_uslov = " AND (mobilni_nd_kandidata NOT LIKE '+387%' AND mobilni_nd_kandidata NOT LIKE '+381%' AND mobilni_nd_kandidata NOT LIKE '+49%' OR mobilni_nd_kandidata is null)";
	else
		$telefon_uslov = " AND mobilni_nd_kandidata LIKE '".$drzava."%' ";
	
	
	if($tim == null){
		$tim_uslov = "";
	}else{
		$tim_uslov = "AND tim_nd_kandidata = $tim";
	}

	if($status != 77){
		$query_getB = $db->prepare("
									SELECT count(id_broj_nd_kandidata) AS broj_leadova
									FROM idk_nd_kandidata WHERE zaduzen_zaposlenik_nd_kandidata = 139 AND $status_uslov $tim_uslov $telefon_uslov
									");		
		$query_getB->execute();
	
		$row_getB = $query_getB->fetch();
		
		$broj_leadova = $row_getB['broj_leadova'];
	}
	else{
		
		$query_get_razlozi_odbijanja_arhiv = $db->prepare('
			SELECT id_ro
			FROM idk_ro_usluge
			WHERE br_dana_ro IS NULL
			AND status_ro != 1
		');
		$array_razlozi_odbijanja_arhiva = array();
		$query_get_razlozi_odbijanja_arhiv -> execute();
		while($row_get_razlozi_odbijanja_arhiv = $query_get_razlozi_odbijanja_arhiv->fetch()){
			array_push($array_razlozi_odbijanja_arhiva, $row_get_razlozi_odbijanja_arhiv['id_ro']);
		}
		$string_razlozi_odbijanja_arhiva = implode(',',$array_razlozi_odbijanja_arhiva);
		// var_dump($query_get_razlozi_odbijanja_arhiv);
		$query_get_max_biljeske = $db->prepare('
			SELECT max(bilj.id_biljeska_nd) as max_biljeska
			FROM idk_nd_kandidata_biljeske bilj
			JOIN idk_nd_kandidata kan
			ON kan.id_broj_nd_kandidata = bilj.id_kandidata_biljeska_nd
			WHERE kan.status_nd_kandidata = 7
			AND kan.zaduzen_zaposlenik_nd_kandidata = 139
			'.$telefon_uslov.'
			'.$tim_uslov.'
			GROUP BY(bilj.id_kandidata_biljeska_nd)
		');
		// var_dump($query_get_max_biljeske);
		$query_get_max_biljeske -> execute();
		$array_max_biljeske = array();
		while($row_get_max_biljeske = $query_get_max_biljeske->fetch()){
			array_push($array_max_biljeske, $row_get_max_biljeske['max_biljeska']);
		}
		// var_dump($array_max_biljeske);
		$string_max_biljeske = implode(',',$array_max_biljeske);
		$broj_leadova = count($array_max_biljeske);
		
		$query_get_3_2_biljeske = $db->prepare('
			SELECT count(bilj.id_biljeska_nd) as cnt
			FROM idk_nd_kandidata_biljeske bilj
			WHERE bilj.status_biljeska_nd = 2
			AND bilj.tip_biljeska_nd = 3
			AND bilj.razlog_biljeska_nd IN('.$string_razlozi_odbijanja_arhiva.')
			AND bilj.id_biljeska_nd IN ('.$string_max_biljeske.')
		');
		// var_dump($query_get_3_2_biljeske);
		// exit();
		
		$query_get_3_2_biljeske -> execute();
		$row_get_3_2_biljeske = $query_get_3_2_biljeske->fetch();
		$broj_leadova = $row_get_3_2_biljeske['cnt'];	
		
	}
	return $broj_leadova;
}
// funkcija koja prima datum formata Y-m-d i prebacuje ga u Y,m,d stim da od mjeseca m oduzima 1 d abi se prilagodio format JS-u ------------------START
function adjustDate($date_arg){
			$year = date('Y', strtotime($date_arg));
			$month = date('m', strtotime($date_arg));
			$day = date('d', strtotime($date_arg));
			$month=$month-1;
			return($year.",".$month.",".$day);	
}
// funkcija koja prima datum formata Y-m-d i prebacuje ga u Y,m,d stim da od mjeseca m oduzima 1 d abi se prilagodio format JS-u ------------------END

// funkcija koja prima dva broja te vraca rast ili pad procentualno $sadasnji varijable od $prosli------------------------------------------------START
function rast($prosli,$sadasnji){
		if($prosli)
			$rez = round(($sadasnji-$prosli)*100/$prosli,2);
		else
			$rez = 0;
		return '('.$rez.'%)';
}
// funkcija koja prima dva broja te vraca rast ili pad procentualno $sadasnji varijable od $prosli------------------------------------------------END

// funkcija koja prima broj dana te provjerava koliko je bilo subota i nedjelja izmedju danas-$br_dana i danas, vraća broj subota i nedjelja------START
function brVikenda($br_dana){
		
		$danas = date("Y-m-d");
		$br_vikenda = 0;
		for($i = 0; $i < $br_dana; $i++){
			$string_dani = '-'.$i.' day';
			$dan = date('D', strtotime($string_dani." ".$danas));
			if($dan == "Sun" || $dan == "Sat")
				$br_vikenda = $br_vikenda+1;
		}
		return $br_vikenda;
}
// funkcija koja prima broj dana te provjerava koliko je bilo subota i nedjelja izmedju danas-$br_dana i danas, vraća broj subota i nedjelja------START

// PROVJERA DA LI KANDIDAT ISPUNJAVA USLOVE ZA NALOG I SMJESTANJE U ODGOVARAJUCI PROJEKAT
function checkCandidateInputs($kandidat_id, $nalog_from_queue = null){
	
	Global $db;
	$query_kandidat = $db->prepare("SELECT * FROM idk_kandidati WHERE kandidat_id = $kandidat_id ");
	$query_kandidat->execute();
	$row_kandidat = $query_kandidat->fetch();
	$kandidat_vozacka = $row_kandidat['kandidat_vozacka_dozvola'];
	$kandidat_vozacka_kategorija = $row_kandidat['kandidat_vozacka_kategorija'];
	$kandidat_status_messenger = $row_kandidat['kandidat_status_messenger'];
	
	if(!isReservedByStatusPrijave($kandidat_id)){
		/** STARO */
			// $query_kandidat_edukacija_visoko = $db->prepare("
			// 	SELECT ke_id FROM idk_kandidat_edukacija WHERE ke_kandidat_id = $kandidat_id AND ke_vrsta_obrazovanja = 'visoko' ");
			// $query_kandidat_edukacija_visoko->execute();
			// $row_kandidat_edukacija_visoko = $query_kandidat_edukacija_visoko->rowCount();

			// $query_kandidat_edukacija_ostalo = $db->prepare("
			// 	SELECT ke_id FROM idk_kandidat_edukacija WHERE ke_kandidat_id = $kandidat_id AND ke_vrsta_obrazovanja = 'ostalo' ");
			// $query_kandidat_edukacija_ostalo->execute();
			// $row_kandidat_edukacija_ostalo = $query_kandidat_edukacija_ostalo->rowCount();

			// $query_kandidat_iskustvo = $db->prepare("
			// 	SELECT kri_id FROM idk_kandidat_radno_iskustvo WHERE kri_kandidat_id = $kandidat_id ");
			// $query_kandidat_iskustvo->execute();
			// $row_kandidat_iskustvo = $query_kandidat_iskustvo->rowCount();
		/** STARO */

		/* Njemački jezik */
		if(getActiveLanguage($kandidat_id)){
			$kandidat_jezik = getActiveLanguage($kandidat_id);
		}else{
			$query_kandidat_jezik = $db->prepare("SELECT kj_slusanje FROM idk_kandidat_jezici WHERE kj_kandidatid = $kandidat_id AND kj_naziv = 'Njemački' ");
			$query_kandidat_jezik->execute();
			$row_kandidat_jezik = $query_kandidat_jezik->fetch();
			$kandidat_jezik = $row_kandidat_jezik['kj_slusanje'];
		}

		$query_kandidat_engleski_jezik = $db->prepare("SELECT kj_slusanje FROM idk_kandidat_jezici WHERE kj_kandidatid = $kandidat_id AND kj_naziv = 'Engleski' ");
		$query_kandidat_engleski_jezik->execute();
		$row_kandidat_engleski_jezik = $query_kandidat_engleski_jezik->fetch();
		$kandidat_engleski_jezik = $row_kandidat_engleski_jezik['kj_slusanje'];

		$query_kandidat_francuski_jezik = $db->prepare("SELECT kj_slusanje FROM idk_kandidat_jezici WHERE kj_kandidatid = $kandidat_id AND kj_naziv = 'Francuski' ");
		$query_kandidat_francuski_jezik->execute();
		$row_kandidat_francuski_jezik = $query_kandidat_francuski_jezik->fetch();
		$kandidat_francuski_jezik = $row_kandidat_francuski_jezik['kj_slusanje'];

		$query_kandidat_italijanski_jezik = $db->prepare("SELECT kj_slusanje FROM idk_kandidat_jezici WHERE kj_kandidatid = $kandidat_id AND kj_naziv = 'Talijanski' ");
		$query_kandidat_italijanski_jezik->execute();
		$row_kandidat_italijanski_jezik = $query_kandidat_italijanski_jezik->fetch();
		$kandidat_italijanski_jezik = $row_kandidat_italijanski_jezik['kj_slusanje'];

		if($nalog_from_queue == null){
			$query_user = $db->prepare("SELECT * FROM users WHERE kandidat_id = $kandidat_id ");
			$query_user->execute();
			$row_user = $query_user->fetch();
			$nalog_id = $row_user['nalog_id'];
			if($nalog_id == null){
				$nalog_id = getNalogIdForLinkId(intval(getLinkForCandidate($kandidat_id)));
			}
		}else{
			$nalog_id = $nalog_from_queue;
		}

		if($nalog_id != 0 ){
			$profiles = getProfilesForNalog($nalog_id);

			// Stari kriteriji
			if(count($profiles) == 0){
				$query_uslovi = $db->prepare("SELECT * FROM idk_nalozi_blokovi_prijave WHERE nbp_nalogid = $nalog_id ");
				$query_uslovi->execute();
				$row_uslovi = $query_uslovi->fetch();
				$vozacka = $row_uslovi['nbp_vozacka'];
				$vozacka_kategorija = $row_uslovi['nbp_vozacka_kategorija'];
				$visoko_obrazovanje = $row_uslovi['nbp_visoko_obr'];
				$dodatno_obrazovanje = $row_uslovi['nbp_dodatno_obr'];
				$iskustvo = $row_uslovi['nbp_korak4'];
				$min_njemacki = $row_uslovi['nbp_njemacki_jezik'];
				$dokumenti = $row_uslovi['nbp_korak7'];
				$diploma = $row_uslovi['nbp_diploma'];
				$pripravnicki = $row_uslovi['nbp_pripravnicki'];
				$strucni = $row_uslovi['nbp_strucni'];
				$jezik_cert = $row_uslovi['nbp_jezik_cert'];
				$nalog_struka = $row_uslovi['nbp_struka'];
				$iskustvo_u_struci = $row_uslovi['nbp_iskustvo_u_struci'];
				$nbp_iskustvo_u_struci_trajanje = $row_uslovi['nbp_iskustvo_u_struci_trajanje'];
				$starost_od = $row_uslovi['nbp_kandidat_starost_od'];
				$starost_do = $row_uslovi['nbp_kandidat_starost_do'];

				/** Staro */
					// $query_doc_diploma = $db->prepare("
					// 	SELECT document_id FROM idk_documents WHERE document_dataid = $kandidat_id AND document_name = 'Diploma završene škole' ");
					// $query_doc_diploma->execute();
					// $row_doc_diploma = $query_doc_diploma->rowCount();

					// $query_doc_pripravnicki = $db->prepare("
					// 	SELECT document_id FROM idk_documents WHERE document_dataid = $kandidat_id AND document_name = 'Uvjerenje o pripravničkom stažu' ");
					// $query_doc_pripravnicki->execute();
					// $row_doc_pripravnicki= $query_doc_pripravnicki->rowCount();

					// $query_doc_strucni = $db->prepare("
					// 	SELECT document_id FROM idk_documents WHERE document_dataid = $kandidat_id AND document_name = 'Uvjerenje o položenom stručnom ispitu' ");
					// $query_doc_strucni->execute();
					// $row_doc_strucni= $query_doc_strucni->rowCount();

					// $query_doc_cert = $db->prepare("
					// 	SELECT document_id FROM idk_documents WHERE document_dataid = $kandidat_id AND document_name = 'Certifikati o poznavanju jezika' ");
					// $query_doc_cert->execute();
					// $row_doc_cert= $query_doc_cert->rowCount();
				/** Staro */

				$ispunjava_uslove = 0;

				if($iskustvo_u_struci == 1 && $row_kandidat["kandidat_iskustvo_u_struci"] != 1){
					$ispunjava_uslove++;
				}

				if($iskustvo_u_struci == 1){
					if($nbp_iskustvo_u_struci_trajanje > $row_kandidat["kandidat_iskustvo_u_struci_trajanje"]){
						$ispunjava_uslove++;
					}
				}

				$today = new DateTime();
				$birthdate = new DateTime($row_kandidat['kandidat_datumrodjenja']);
				$interval = $today->diff($birthdate);
				$age = $interval->y;
				
				if($starost_od !== null && $age < $starost_od){
					$ispunjava_uslove++;
				}

				if($starost_do !== null && $age > $starost_do){
					$ispunjava_uslove++;
				}

				//gledanje obrazovanja preko struka
					/*if($nalog_struka != null){
						$query_kandidat_edukacija_srednja = $db->prepare("
								SELECT ke_id, ke_smjer_id, ss_struka_id FROM idk_kandidat_edukacija 
								LEFT JOIN idk_skole_smjerovi sm on ke_smjer_id = sm.ss_id
								WHERE ke_kandidat_id = $kandidat_id AND ss_struka_id IN ($nalog_struka) ");
						$query_kandidat_edukacija_srednja->execute();
						$nr_kes = $query_kandidat_edukacija_srednja->rowCount();
						if($nr_kes < 1){
							$ispunjava_uslove++;
						}
					}else{}*/

					//U toku naloga promijenjena odluka da treba skola, a hoce da smjerovi naloga ostanu na prijavnoj formi.
					//Ova provjera inputa za skolu je bazirana na smjerovima naloga pa je sad ovo najbrze rjesenje kako bi se
					//kandidati bez skole smjestali u bot ispunjava a istovremeno da na prijavu ostanu izabrani smjerovi.
					//Za ubuduce je potrebno razdvojiti ovo. Smjerovi naloga nek uticu samo na formu, a za inpute trebaju se gledati
					//kriteriji, sto znaci da za kriterije za skolu treba napraviti da funkcionise iste kao smjerovi naloga
				//gledanje obrazovanja preko struka

				$nalog_smjerovi_kriterij = getNalogSmjeroviKriterij($nalog_id);

				if($nalog_smjerovi_kriterij != 100 AND $nalog_smjerovi_kriterij != 101 AND $nalog_smjerovi_kriterij == 1){
					
					//gledanje obrazovanja preko smjerova vezanih za nalog
					//prvo pokupiti sve smjerovi koji su vezani za nalog, ako nema nijednog onda ne treba nista raditi
					$nalog_smjerovi_cnt = getNalogSmjeroviCntR($nalog_id);

					if($nalog_smjerovi_cnt > 0){
						
						$query_kandidat_edukacija_srednja = $db->prepare("
									SELECT ke_id FROM idk_kandidat_edukacija 
									LEFT JOIN idk_skole_smjerovi sm on ke_smjer_id = sm.ss_id
									WHERE ke_kandidat_id = $kandidat_id AND sm.ss_naziv IN 
									(SELECT iss.ss_naziv FROM idk_nalog_smjer ins
									JOIN idk_skole_smjerovi iss ON ins.smjer_id = iss.ss_id
									WHERE ins.nalog_id = $nalog_id ) "
						);
						$query_kandidat_edukacija_srednja->execute();
						$nr_kes = $query_kandidat_edukacija_srednja->rowCount();
						if($nr_kes < 1){
							$ispunjava_uslove++;
						}
					}else{}
				}
				
				if($vozacka == 'block'){
					if($kandidat_vozacka !== 'Da'){
						$ispunjava_uslove++;
					}else{}
					
				}
				if($vozacka_kategorija == null){
					
				}else{
					$niz_kat = explode(',', $vozacka_kategorija);
					$niz_kand_vk = explode(',',$kandidat_vozacka_kategorija);
					
					$niz_final = array();
					if(in_array("CE", $niz_kand_vk)){
						$nize_kategorije = array("B","C1","C","C1E","CE");
						foreach($nize_kategorije as $niza_kat){
							if(!in_array($niza_kat, $niz_final, true)){
								array_push($niz_final, $niza_kat);
							}
						}
					}
					if(in_array("C1E", $niz_kand_vk)){
						$nize_kategorije = array("B","C1","C","C1E");
						foreach($nize_kategorije as $niza_kat){
							if(!in_array($niza_kat, $niz_final, true)){
								array_push($niz_final, $niza_kat);
							}
						}
					}
					if(in_array("C", $niz_kand_vk)){
						$nize_kategorije = array("B","C1","C");
						foreach($nize_kategorije as $niza_kat){
							if(!in_array($niza_kat, $niz_final, true)){
								array_push($niz_final, $niza_kat);
							}
						}
					}
					if(in_array("C1", $niz_kand_vk)){
						$nize_kategorije = array("B","C1");
						foreach($nize_kategorije as $niza_kat){
							if(!in_array($niza_kat, $niz_final, true)){
								array_push($niz_final, $niza_kat);
							}
						}
					}
					if(in_array("BE", $niz_kand_vk)){
						$nize_kategorije = array("B","BE");
						foreach($nize_kategorije as $niza_kat){
							if(!in_array($niza_kat, $niz_final, true)){
								array_push($niz_final, $niza_kat);
							}
						}
					}
					if(in_array("B", $niz_kand_vk)){
						$nize_kategorije = array("B");
						foreach($nize_kategorije as $niza_kat){
							if(!in_array($niza_kat, $niz_final, true)){
								array_push($niz_final, $niza_kat);
							}
						}
					}
					
					$niz_check = array_intersect($niz_final,$niz_kat);
					if(count($niz_check) == 0){
						$ispunjava_uslove++;
					}
				}
				/** STARO */
					// if($visoko_obrazovanje == 1){
					// 	if($row_kandidat_edukacija_visoko < 1){
					// 		$ispunjava_uslove++;
					// 	}else{}
					// }
					// if($dodatno_obrazovanje == 1){
					// 	if($row_kandidat_edukacija_ostalo < 1){
					// 		$ispunjava_uslove++;
					// 	}else{}
					// }

					// if($iskustvo == 1){
					// 	if($row_kandidat_iskustvo < 1){
					// 		$ispunjava_uslove++;
					// 	}else{}
					// }
				/** STARO */
				$kandidat_znanje_njem = transformLanguageToNumber($kandidat_jezik);
				$min_njemacki_broj = transformLanguageToNumber($min_njemacki);
				
				if($kandidat_znanje_njem < $min_njemacki_broj){
					$ispunjava_uslove++;
				}else{}

				/** STARO */
					// if($dokumenti == 1){
					// 	if($diploma == 1){
					// 		if($row_doc_diploma < 1){
					// 			$ispunjava_uslove++;
					// 		}else{}
					// 	}
					// 	if($pripravnicki == 1){
					// 		if($row_doc_pripravnicki < 1){
					// 			$ispunjava_uslove++;
					// 		}else{}
					// 	}
					// 	if($strucni == 1){
					// 		if($row_doc_strucni < 1){
					// 			$ispunjava_uslove++;
					// 		}else{}
					// 	}
					// 	if($jezik_cert == 1){
					// 		if($row_doc_cert < 1){
					// 			$ispunjava_uslove++;
					// 		}else{}
					// 	}
					// }
				/** STARO */
			}else{
				foreach($profiles as $profile){
					$profil_id = $profile["profil_id"];
					
					$query_uslovi = $db->prepare("SELECT * FROM idk_profil_kriterij WHERE profil_id = $profil_id");

					$query_uslovi->execute();

					$row_uslovi = $query_uslovi->fetch();
					$vozacka_dozvola = $row_uslovi['vozacka_dozvola'];
					$kategorija_vozacke_dozvole = $row_uslovi['kategorija_vozacke_dozvole'];
					$smjerovi_naloga = $row_uslovi['smjerovi_naloga'];
					$radno_iskustvo_struka = $row_uslovi['radno_iskustvo_struka'];
					$radno_iskustvo_struka_trajanje = $row_uslovi['radno_iskustvo_struka_trajanje'];
					$starost = $row_uslovi['starost'];
					$starost_minimum = $row_uslovi['starost_minimum'];
					$starost_maksimum = $row_uslovi['starost_maksimum'];
					$njemacki_jezik = $row_uslovi['njemacki_jezik'];
					$nivo_njemackog_jezika = $row_uslovi['nivo_njemackog_jezika'];
					$znanje_drugog_jezika = $row_uslovi['znanje_drugog_jezika'];
					$engleski_jezik = $row_uslovi['engleski_jezik'];
					$nivo_engleskog_jezika = $row_uslovi['nivo_engleskog_jezika'];
					$francuski_jezik = $row_uslovi['francuski_jezik'];
					$nivo_francuskog_jezika = $row_uslovi['nivo_francuskog_jezika'];
					$italijanski_jezik = $row_uslovi['italijanski_jezik'];
					$nivo_italijanskog_jezika = $row_uslovi['nivo_italijanskog_jezika'];

					$ispunjava_uslove = 0;

					if($radno_iskustvo_struka == 1 && $row_kandidat["kandidat_iskustvo_u_struci"] != 1){
						$ispunjava_uslove++;
						continue;
					}

					if($radno_iskustvo_struka == 1){
						if($radno_iskustvo_struka_trajanje > $row_kandidat["kandidat_iskustvo_u_struci_trajanje"]){
							$ispunjava_uslove++;
							continue;
						}
					}
					
					if($starost == 1){
						$today = new DateTime();
						$birthdate = new DateTime($row_kandidat['kandidat_datumrodjenja']);
						$interval = $today->diff($birthdate);
						$age = $interval->y;

						if($starost_minimum !== null && $age < $starost_minimum){
							$ispunjava_uslove++;
							continue;
						}

						if($starost_maksimum !== null && $age > $starost_maksimum){
							$ispunjava_uslove++;
							continue;
						}
					}

					if($smjerovi_naloga == 1){
						
						//gledanje obrazovanja preko smjerova vezanih za nalog
						//prvo pokupiti sve smjerovi koji su vezani za nalog, ako nema nijednog onda ne treba nista raditi
						$nalog_smjerovi_cnt = getNalogSmjeroviCntR($nalog_id);

						if($nalog_smjerovi_cnt > 0){
							
							$query_kandidat_edukacija_srednja = $db->prepare("
										SELECT ke_id FROM idk_kandidat_edukacija 
										LEFT JOIN idk_skole_smjerovi sm on ke_smjer_id = sm.ss_id
										WHERE ke_kandidat_id = $kandidat_id AND sm.ss_naziv IN 
										(SELECT iss.ss_naziv FROM idk_nalog_smjer ins
										JOIN idk_skole_smjerovi iss ON ins.smjer_id = iss.ss_id
										WHERE ins.nalog_id = $nalog_id ) "
							);
							$query_kandidat_edukacija_srednja->execute();
							$nr_kes = $query_kandidat_edukacija_srednja->rowCount();
							if($nr_kes < 1){
								$ispunjava_uslove++;
								continue;
							}
						}else{}
					}

					if($vozacka_dozvola == 1){
						if($kandidat_vozacka !== 'Da'){
							$ispunjava_uslove++;
							continue;
						}else{}
						
					}
					
					if($vozacka_dozvola == 1){
						$niz_kat = explode(',', $kategorija_vozacke_dozvole);
						$niz_kand_vk = explode(',',$kandidat_vozacka_kategorija);
						
						$niz_final = array();
						if(in_array("CE", $niz_kand_vk)){
							$nize_kategorije = array("B","C1","C","C1E","CE");
							foreach($nize_kategorije as $niza_kat){
								if(!in_array($niza_kat, $niz_final, true)){
									array_push($niz_final, $niza_kat);
								}
							}
						}
						if(in_array("C1E", $niz_kand_vk)){
							$nize_kategorije = array("B","C1","C","C1E");
							foreach($nize_kategorije as $niza_kat){
								if(!in_array($niza_kat, $niz_final, true)){
									array_push($niz_final, $niza_kat);
								}
							}
						}
						if(in_array("C", $niz_kand_vk)){
							$nize_kategorije = array("B","C1","C");
							foreach($nize_kategorije as $niza_kat){
								if(!in_array($niza_kat, $niz_final, true)){
									array_push($niz_final, $niza_kat);
								}
							}
						}
						if(in_array("C1", $niz_kand_vk)){
							$nize_kategorije = array("B","C1");
							foreach($nize_kategorije as $niza_kat){
								if(!in_array($niza_kat, $niz_final, true)){
									array_push($niz_final, $niza_kat);
								}
							}
						}
						if(in_array("BE", $niz_kand_vk)){
							$nize_kategorije = array("B","BE");
							foreach($nize_kategorije as $niza_kat){
								if(!in_array($niza_kat, $niz_final, true)){
									array_push($niz_final, $niza_kat);
								}
							}
						}
						if(in_array("B", $niz_kand_vk)){
							$nize_kategorije = array("B");
							foreach($nize_kategorije as $niza_kat){
								if(!in_array($niza_kat, $niz_final, true)){
									array_push($niz_final, $niza_kat);
								}
							}
						}
						
						$niz_check = array_intersect($niz_final,$niz_kat);
						if(count($niz_check) == 0){
							$ispunjava_uslove++;
							continue;
						}
					}

					if($njemacki_jezik == 1){
						$kandidat_znanje_njem = transformLanguageToNumber($kandidat_jezik);
						$min_njemacki_broj = transformLanguageToNumber($nivo_njemackog_jezika);

						if($kandidat_znanje_njem < $min_njemacki_broj){
							$ispunjava_uslove++;
							continue;
						}
					}

					if($znanje_drugog_jezika == 1){
						if($engleski_jezik == 1){

							if(transformLanguageToNumber($kandidat_engleski_jezik) < transformLanguageToNumber($nivo_engleskog_jezika)){
								$ispunjava_uslove++;
								continue;
							}
						}
	
						if($italijanski_jezik == 1){
							if(transformLanguageToNumber($kandidat_italijanski_jezik) < transformLanguageToNumber($nivo_italijanskog_jezika)){
								$ispunjava_uslove++;
								continue;
							}
						}
	
						if($francuski_jezik == 1){
							if(transformLanguageToNumber($kandidat_francuski_jezik) < transformLanguageToNumber($nivo_francuskog_jezika)){
								$ispunjava_uslove++;
								continue;
							}
						}
					}

					// Ako zadovolji jedan profil odmah prekinuti foreach i staviti ga u bot ispunjava
					if($ispunjava_uslove == 0){
						break;
					}
				}
			}
			
			/* ODLUKA O KANDIDATU */
			if($ispunjava_uslove == 0){
				$izvor = 3;
				$status_id = 6;
				$stari_projekt_id = null;
				$query_bot_project = $db->prepare("
					SELECT project_id FROM idk_projects WHERE project_nalogid = $nalog_id AND project_name LIKE '%BOT - ispunjava uslove%' ");
				$query_bot_project->execute();
				$row_project = $query_bot_project->fetch();
				$project_id = $row_project['project_id'];
				$novi_projekt_id = $project_id;
				
				$q_oposite_project = $db->prepare("
					SELECT project_id FROM idk_projects WHERE project_nalogid = $nalog_id AND project_name LIKE '%BOT - ne ispunjava uslove%' ");
				$q_oposite_project->execute();
				$row_oposite_project = $q_oposite_project->fetch();
				$oposite_project_id = $row_oposite_project['project_id'];
				
				$query_delete = $db->prepare("DELETE FROM idk_project_kandidati WHERE pk_projectid = $oposite_project_id AND pk_kandidatid = $kandidat_id");
				$query_delete->execute();
				
				$query_check = $db->prepare("SELECT * FROM idk_project_kandidati
											WHERE pk_projectid = :pk_projectid AND pk_kandidatid = :pk_kandidatid");
				$query_check->execute(array(
								':pk_projectid' => $project_id,
								':pk_kandidatid' => $kandidat_id));
				if($query_check->rowCount() == 0){
					
					$query = $db->prepare("
									INSERT INTO idk_project_kandidati
										(pk_projectid, pk_kandidatid)
									VALUES
										(:pk_projectid, :pk_kandidatid)");

					$query->execute(array(
								':pk_projectid' => $project_id,
								':pk_kandidatid' => $kandidat_id));
				}
				$query_update_sp = $db->prepare("UPDATE idk_kandidati SET kandidat_status_prijave = $status_id WHERE kandidat_id = $kandidat_id");
				$query_update_sp->execute();

				$query_bot_project_prijave = $db->prepare("
					SELECT project_id FROM idk_projects WHERE project_nalogid = $nalog_id AND (project_name LIKE '%Prijave%' OR project_name LIKE '%Prijava%') ");
				$query_bot_project_prijave->execute();
				$row_project_prijave = $query_bot_project_prijave->fetch();
				$prijave_project_id = $row_project_prijave['project_id'];
				$query_delete = $db->prepare("DELETE FROM idk_project_kandidati WHERE pk_projectid = $prijave_project_id AND pk_kandidatid = $kandidat_id");
				$query_delete->execute();

				//Add to LOGS
				$log_desc = "Bot vezao kandidata: " .$kandidat_id. " za projekt " .$project_id. ".";
				$log_type = "0";
				addToLogs($log_desc, $log_type); //Log za biljeske - $log_type = 3
				addToLogsStatusPrijave($stari_projekt_id, $novi_projekt_id, $status_id, $kandidat_id, $izvor);

				$to_return = 0; // kandidat ispunjava uslove
				
			}else{
				$izvor = 3;
				$status_id = 2;
				$stari_projekt_id = null; 
				$query_bot_project = $db->prepare("
					SELECT project_id FROM idk_projects WHERE project_nalogid = $nalog_id AND project_name LIKE '%BOT - ne ispunjava uslove%' ");
				$query_bot_project->execute();
				$row_project = $query_bot_project->fetch();
				$project_id = $row_project['project_id'];
				$novi_projekt_id = $project_id;
				if($kandidat_status_messenger == 2 OR $kandidat_status_messenger == 5 OR $kandidat_status_messenger == 6){
					//Uslov iznad je postojao da se kandidati na prijavi ne smjeste odmah u Ne ispunjava uslove jer na prijavi nije bilo
					//svih pitanja koja trebaju za nalog. U ne ispunjava su se stavljali kandidati koji odrade nesto na messengeru.
					//Statusi 2-logovao se, 5-u toku popunjavanja profila, 6-dopunjava podatke su znacili da je kandidat popunjavao informacije
					//na messengeru i onda se mogla raditi ova provjera i smjestati kandidate u BOT ne ispunjava.
					//Posto sad imamo sva pitanja na prijavi sklonjamo ovaj uslov i kandidate smjestamo u BOT ne ispunjava.
				
					
				$q_oposite_project = $db->prepare("
					SELECT project_id FROM idk_projects WHERE project_nalogid = $nalog_id AND project_name LIKE '%BOT -  ispunjava uslove%' ");
				$q_oposite_project->execute();
				$row_oposite_project = $q_oposite_project->fetch();
				$oposite_project_id = $row_oposite_project['project_id'];

				$query_delete = $db->prepare("DELETE FROM idk_project_kandidati WHERE pk_projectid = $oposite_project_id AND pk_kandidatid = $kandidat_id");
				$query_delete->execute();
				
				$query_check = $db->prepare("SELECT * FROM idk_project_kandidati
											WHERE pk_projectid = :pk_projectid AND pk_kandidatid = :pk_kandidatid");
				$query_check->execute(array(
								':pk_projectid' => $project_id,
								':pk_kandidatid' => $kandidat_id));
				if($query_check->rowCount() == 0){
					
					$query = $db->prepare("
									INSERT INTO idk_project_kandidati
										(pk_projectid, pk_kandidatid)
									VALUES
										(:pk_projectid, :pk_kandidatid)");

					$query->execute(array(
								':pk_projectid' => $project_id,
								':pk_kandidatid' => $kandidat_id));
				}

				//Add to LOGS
				$log_desc = "Bot vezao kandidata: " .$kandidat_id. " za projekt " .$project_id. ".";
				$log_type = "0";
				addToLogs($log_desc, $log_type); //Log za biljeske - $log_type = 3
				
				$query_bot_project_prijave = $db->prepare("
				SELECT project_id FROM idk_projects WHERE project_nalogid = $nalog_id AND project_name LIKE '%Prijave%' ");
				$query_bot_project_prijave->execute();
				$row_project_prijave = $query_bot_project_prijave->fetch();
				$prijave_project_id = $row_project_prijave['project_id'];
				$query_delete = $db->prepare("DELETE FROM idk_project_kandidati WHERE pk_projectid = $prijave_project_id AND pk_kandidatid = $kandidat_id");
				$query_delete->execute();
				addToLogsStatusPrijave($stari_projekt_id, $novi_projekt_id, $status_id, $kandidat_id, $izvor);
				}

				$to_return = 1; // kandidat ne ispunjava uslove
				
			}
		}else{
			$to_return = 4; //kandidat nije vezan za nalog
		}
	}else{
		$to_return = 2; //kandidat je rezervisan
	}

	return $to_return;
}

function getNalogSmjeroviKriterij($nalogId) {
	Global $db;
	$nalogId = intval($nalogId); 

	if ($nalogId != 0){

		$query = $db->prepare("
			SELECT 
				nalog_smjerovi_kriterij
			FROM 
				idk_nalozi 
			WHERE  
				nalog_id = :nalog_id
		");

		$query->execute(array(
			':nalog_id' => $nalogId
		));

		if ($query->rowCount() == 1) {

			$row = $query->fetch();

			return intval($row["nalog_smjerovi_kriterij"]);

		} else {

			return 101;

		}

	} else {

		return 100; 

	}

}

function getNalogSmjeroviCntR($nalogId){

	Global $db;
	$nalogId = intval($nalogId);

	if ($nalogId != 0){
		$query = $db->prepare("
			SELECT 
				count(id) as broj
			FROM 
				idk_nalog_smjer 
			WHERE  
				nalog_id = :nalog_id
		");
		$query->execute(array(
			':nalog_id' => $nalogId
		));
		if ($query->rowCount() == 1) {
			$row = $query->fetch();
			return intval($row["broj"]);
		} else {
			return 0;
		}
	} else {
		return 0;
	}

}

function getDefaultProfileName($nalog_id){
	Global $db;
	$nalog_id = $nalog_id;

	$query = $db->prepare("
		SELECT id
		FROM 
			idk_nalog_profil 
		WHERE  
			nalog_id = :nalog_id AND status = 1
	");
	$query->execute(array(
		':nalog_id' => $nalog_id
	));
	
	$count = $query->rowCount() + 1;
	return $count;
}

// SAMO VRACA PROCENAT 2 VRIJEDNOSTI ---------------------------START
	function getProcenat($glavnica, $dio){
		if($glavnica == 0)
			$rez = 0;
		else
			$rez=round(($dio/$glavnica)*100,2);
		return $rez;
		
	}
// SAMO VRACA PROCENAT 2 VRIJEDNOSTI ---------------------------END





//PRIMA MJESEC FORMATA MM I GODINU YYYY TE VRAĆA RIJEČ ZA TAJ MJESEC NA BOSANSKOM I NA TO DODA GODINU -------- START
function getPrevodMjesecStr($month, $year){
	
	$mjesec = "Error";

	if ($month == '01'){
		$mjesec = "Januar";
	}
	else if ($month == '02'){
		$mjesec = "Februar";
	}
	else if ($month == '03'){
		$mjesec = "Mart";
	}
	else if ($month == '04'){
		$mjesec = "April";
	}
	else if ($month == '05'){
		$mjesec = "Maj";
	}
	else if ($month == '06'){
		$mjesec = "Juni";
	}
	else if ($month == '07'){
		$mjesec = "Juli";
	}
	else if ($month == '08'){
		$mjesec = "August";
	}
	else if ($month == '09'){
		$mjesec = "Septembar";
	}
	else if ($month == '10'){
		$mjesec = "Oktobar";
	}
	else if ($month == '11'){
		$mjesec = "Novembar";
	}
	else if ($month == '12'){
		$mjesec = "Decembar";
	}
	
	return $mjesec.', '.$year;
}

function getPrevodMjesec($month, $year){
	
	$mjesec = "Error";

	if ($month == '01'){
		$mjesec = "Januar";
	}
	else if ($month == '02'){
		$mjesec = "Februar";
	}
	else if ($month == '03'){
		$mjesec = "Mart";
	}
	else if ($month == '04'){
		$mjesec = "April";
	}
	else if ($month == '05'){
		$mjesec = "Maj";
	}
	else if ($month == '06'){
		$mjesec = "Juni";
	}
	else if ($month == '07'){
		$mjesec = "Juli";
	}
	else if ($month == '08'){
		$mjesec = "August";
	}
	else if ($month == '09'){
		$mjesec = "Septembar";
	}
	else if ($month == '10'){
		$mjesec = "Oktobar";
	}
	else if ($month == '11'){
		$mjesec = "Novembar";
	}
	else if ($month == '12'){
		$mjesec = "Decembar";
	}
	
	echo $mjesec.', '.$year;
}

//PRIMA MJESEC FORMATA MM I GODINU YYYY TE VRAĆA RIJEČ ZA TAJ MJESEC NA BOSANSKOM I NA TO DODA GODINU -------- START

function getStyle4DataTableRows($a, $b){
	$prvi_dio = '<td data-order = "'.getProcenat($a,$b).'">';
	$drugi_dio = '<div class="text-right" style="float:left;width:50%">'.$b.'/'.$a.'</div>';
	$treci_dio = '<div class="text-left" style="float:right;width:50%"> &nbsp;('.getProcenat($a,$b).'%)</div></td>';

	return $prvi_dio.$drugi_dio.$treci_dio;
}

//DIPL OBRADA Funkcije za slanje Viber SmS Email poruka sa zahtjevom i spiskom dokumenatacije START

	function addLogDIPL($log_desc){
		Global $db;
		Global $logged_employee_id;
		$log_date = date('Y-m-d H:i:s');
		
		$log_query = $db->prepare("
			INSERT INTO idk_logs 
				(log_employeeid, log_desc, log_date)
			VALUES
				(:log_employeeid, :log_desc, :log_date)
		");

		$log_query->execute(array(
			':log_employeeid' => $logged_employee_id,
			':log_desc' => $log_desc,
			':log_date' => $log_date
		));
	}
	function sendMailViberRequestInstitutionDIPL($kandidat_id, $ustanova_id, $dokument_id){
		Global $db;
		$kan_id = intval($kandidat_id);
		$zaposlenici = array();
		$flagCheck = 0;
		$log_desc = "";
		if($kan_id != 0){
			$flagCheck = 1;
			$query = $db->prepare("
				SELECT kan.ime_nd_kandidata, kan.prezime_nd_kandidata, kan.mobilni_nd_kandidata, kan.email_nd_kandidata, kan.idd_ustanova_nd, pr.pr_domaca_valuta, pr.pr_naplata_preko
				FROM idk_nd_kandidata kan
				INNER JOIN idk_predracuni pr
				ON kan.id_broj_nd_kandidata = pr.pr_kandidat_id
				WHERE kan.id_broj_nd_kandidata = ".$kan_id." AND pr.pr_rata = 1 AND pr.pr_uplaceno = 1
			");
			$query->execute();
			if(intval($query->rowCount()) != 0){
				$flagCheck = 1;
				$row = $query->fetch();
				$ime_nd_kandidata = $row["ime_nd_kandidata"];
				$prezime_nd_kandidata = $row["prezime_nd_kandidata"];
				$mobilni_nd_kandidata = $row["mobilni_nd_kandidata"];
				$email_nd_kandidata = $row["email_nd_kandidata"];
				$idd_ustanova_nd = intval($row["idd_ustanova_nd"]);
				$pr_domaca_valuta = $row["pr_domaca_valuta"];
				$pr_naplata_preko = $row["pr_naplata_preko"];
				
				$hostName = "smtp.gmail.com";
				$userName = "support@job-step.com";
				$password = "eooc nnxo aylp lqkh";
				$setFrom = "support@job-step.com";
				$sendProvjera = 0;
				$putanja_signatura = "";
				$textSMS = "";
				$textViber = "";
				$naslovMail = "";
				$textMail = "";
				if($pr_domaca_valuta == "BAM" OR $pr_domaca_valuta == "EUR"){
					if ($pr_domaca_valuta == "BAM") {
						array_push($zaposlenici, 43);
					} else {
						array_push($zaposlenici, 43);
						array_push($zaposlenici, 203);
					}
					
					// $hostName = "smtp.strato.de;smtp.strato.de";
					// $userName = "obrada@job-step.net";
					// $password = "Obrada6432BH";
					// $setFrom = "obrada@job-step.net";
					$sendProvjera = 1;

					if($pr_naplata_preko == 1){
						$putanja_signatura = $_SERVER['DOCUMENT_ROOT']."/files/ugovori_uplatnice_dipl/nostrifikacija_CH.gif";
					} else{
						$putanja_signatura = $_SERVER['DOCUMENT_ROOT']."/files/ugovori_uplatnice_dipl/signatura_bih.jpg";
					}
					
					$textSMS = "Poštovani/a ".$ime_nd_kandidata." ".$prezime_nd_kandidata."";
					$textSMS .= "na Vašu e-mail adresu navedenu u prijavi za Nostrifikaciju diplome, poslan je zahtjev za nostrifikaciju koji trebate samo potpisati i dostaviti.";
					$textSMS .= "U slučaju dodatnih pitanja, kontaktirajte na broj: +387 37 961 346.";
					$textSMS .= "Vaš JobStep.";
					
					$textViber = "Poštovani/a ".$ime_nd_kandidata." ".$prezime_nd_kandidata.",\n";
					$textViber .= "na Vašu e-mail adresu navedenu u prijavi za Nostrifikaciju diplome, poslan je zahtjev za nostrifikaciju koji trebate samo potpisati i dostaviti.\n";
					$textViber .= "U slučaju dodatnih pitanja, kontaktirajte na broj:\n +387 37 961 346\n";
					$textViber .= "Vaš JobStep.";
					
					$naslovMail = "Zahtjev za nostrifikaciju";
					
					$textMail = "
						Poštovani/a ".$ime_nd_kandidata." ".$prezime_nd_kandidata.", <br>
						u prilogu Vam dostavljamo zahtjev za Nostrifikaciju diplome za koji je ranije naglašeno da će Vam biti naknadno dostavljen. <br>
						Dokument je potrebno samo potpisati i dostaviti na dolje navedenu adresu. <br>
						U slučaju dodatnih pitanja stojimo Vam na raspolaganju na broj: +387 37 961 346.<br>
						Vaš JobStep!
					";
					
				}else if($pr_domaca_valuta == "RSD"){
					array_push($zaposlenici, 203);
					// $hostName = "mail.job-step.rs";
					// $userName = "obrada@job-step.rs";
					// $password = "Obrada6432RS";
					// $setFrom = "obrada@job-step.rs";
					$sendProvjera = 1;

					if($pr_naplata_preko == 1){
						$putanja_signatura = $_SERVER['DOCUMENT_ROOT']."/files/ugovori_uplatnice_dipl/nostrifikacija_CH.gif";
					} else if ($pr_naplata_preko == 2) {
						$putanja_signatura = $_SERVER['DOCUMENT_ROOT']."/files/ugovori_uplatnice_dipl/signatura-SRB-automatski-mail-new.gif"; /*Nova signatura*/
					} else{
						$putanja_signatura = $_SERVER['DOCUMENT_ROOT']."/files/ugovori_uplatnice_dipl/signatura_srb.jpg";
					}
					
					$textSMS = "Poštovani/a ".$ime_nd_kandidata." ".$prezime_nd_kandidata."";
					$textSMS .= "na Vašu e-mail adresu navedenu u prijavi za Nostrifikaciju diplome, poslan je zahtev za nostrifikaciju koji trebate samo potpisati i dostaviti.";
					$textSMS .= "U slučaju dodatnih pitanja, kontaktirajte na broj: +381 62 274 607.";
					$textSMS .= "Vaš JobStep!";
					
					$textViber = "Poštovani/a ".$ime_nd_kandidata." ".$prezime_nd_kandidata.",\n";
					$textViber .= "na Vašu e-mail adresu navedenu u prijavi za Nostrifikaciju diplome, poslan je zahtev za nostrifikaciju koji trebate samo potpisati i dostaviti.\n";
					$textViber .= "U slučaju dodatnih pitanja, kontaktirajte na broj:\n +381 62 274 607\n";
					$textViber .= "Vaš JobStep!";
					
					$naslovMail = "Zahtev za nostrifikaciju";
					
					$textMail = "
						Poštovani/a ".$ime_nd_kandidata." ".$prezime_nd_kandidata.", <br>
						u prilogu Vam dostavljamo zahtev za Nostrifikaciju diplome za koji je ranije naglašeno da će Vam biti naknadno dostavljen. <br>
						Dokument je potrebno samo potpisati i dostaviti na dolje navedenu adresu. <br>
						U slučaju dodatnih pitanja stojimo Vam na raspolaganju na broj: +381 62 274 607.<br>
						Vaš JobStep!
					";
					
				}else{
					$hostName = "";
					$userName = "";
					$password = "";
					$setFrom = "";
					$sendProvjera = 0;
					$putanja_signatura = "";
					$textSMS = "";
					$textViber = "";
					$naslovMail = "";
					$textMail = "";
				}
				
				if($sendProvjera == 1){
					$flagCheck = 1;
					
					if(intval($ustanova_id) == $idd_ustanova_nd){
						$flagCheck = 1;
						
						$queryDocument = $db->prepare("
							SELECT template_naziv_dokumenta_ustanove_nd
							FROM idk_nd_ustanove_tip_dokumenta
							WHERE id_tip_dokumenta_ustanove_nd = ".$dokument_id." AND template_dokumenta_ustanove_nd = 1 AND idd_ustanove_nd = ".$idd_ustanova_nd." AND template_naziv_dokumenta_ustanove_nd is not null
						");
						$queryDocument->execute();
						
						if(intval($queryDocument->rowCount()) == 1){
							$flagCheck = 1;
							$rowDocument = $queryDocument->fetch();
							$naziv_dokumenta_ustanove = $rowDocument["template_naziv_dokumenta_ustanove_nd"];
							
							$putanjaDocumenta = $_SERVER['DOCUMENT_ROOT']."/files/dokumenti_ND_ustanove/".$naziv_dokumenta_ustanove;
							
							$mail = new PHPMailer;
							$mail->isSMTP();											// Set mailer to use SMTP
							$mail->Host = $hostName;				// Specify main and backup SMTP servers
							$mail->SMTPAuth = true;										// Enable SMTP authentication
							$mail->Username = $userName;					// SMTP username
							$mail->Password = $password;							// SMTP password
							$mail->SMTPSecure = 'ssl';									// Enable TLS encryption, `ssl` also accepted
							$mail->Port = 465;											// TCP port to connect to
							$mail->CharSet = 'UTF-8';
							
							$mail->setFrom($setFrom, 'JobStep');    		// Add a recipient
							$mail->addAddress($email_nd_kandidata);		// Add a recipient
							$mail->AddAttachment($putanjaDocumenta); 
							
							$mail->Subject = "".$naslovMail."";
							$mail->Body = "
								".$textMail."
								<img src='cid:logo_2u' width='100%'>
							";
							
							$mail->AddEmbeddedImage($putanja_signatura, 'logo_2u');
							
							$mail->AltBody = "ALT";
							//$mail_bh->send();
							if(!$mail->send()) {
								if(count($zaposlenici)){
									$mail1 = new PHPMailer;
									$mail->isSMTP();

									$mail1->Host = 'smtp.gmail.com';                 // Specify main and backup SMTP servers
									$mail1->SMTPAuth = true;                         // Enable SMTP authentication
									$mail1->Username = 'support@job-step.com';        // SMTP username
									$mail1->Password = 'eooc nnxo aylp lqkh';          // SMTP password
									$mail1->SMTPSecure = 'ssl';                      // Enable TLS encryption, `ssl` also accepted
									$mail1->Port = 465;                              // TCP port to connect to
									$mail1->CharSet = 'UTF-8';

									//Recipients
									$mail1->setFrom('support@job-step.com', 'JobStep');
									$cnt_zaposlenici = 0; 
									foreach ($zaposlenici as $value) {
										$user_query = $db->prepare("
											SELECT 
												employee_email
											FROM 
												idk_employees
											WHERE 
												employee_id = :employee_id
												AND 
												employee_status NOT LIKE '0'
										");

										$user_query->execute(array(
											':employee_id' => $value
										));

										if ($user_query->rowCount() != 0) {
										
											$user = $user_query->fetch();
											$employee_email = $user['employee_email'];
											
											$mail1->addAddress($employee_email); // Add a recipient
											$cnt_zaposlenici++;
										}
									}
									$mail1->AddAttachment($putanjaDocumenta);
									
									$mail1->Subject = "Zahtjev za nostrifikaciju ".$ime_nd_kandidata." ".$prezime_nd_kandidata."";
									$mail1->Body = "
										<p>
											Kandidatu nije automatski poslan Zahtjev za nostrifikaciju na e-mail adresu.<br>
											Potrebno je dostaviti isti. <br><br>
											Kandidat: ".$ime_nd_kandidata." ".$prezime_nd_kandidata." <br>
											Broj telefona: ".$mobilni_nd_kandidata." <br>
										</p>
										<p>
											Detalje pogledajte na linku: " . getSiteUrlr() . "/nostrifikacija_diploma?page=otvori_ND_kandidata&id=".$kan_id."
										</p>
										<br><br><br>
										<img src='cid:logo_2u1' width='100%'>
									";
									
									$mail1->AddEmbeddedImage($putanja_signatura, 'logo_2u1');
									
									$mail1->AltBody = "ALT";
									if ($cnt_zaposlenici > 0) {
										if(!$mail1->send()) {
											//echo 'Mailer Error: ' . $mail1->ErrorInfo;
											//echo "Nije poslano zaposleniku!<br/>";
											$log_desc = "DIPL - Zahtjev za nostrifikaciju - Nije poslan e-mail zaposlenicima da kandidatu nije poslan e-mail sa zahtjevom. Zaposlenici ID = [".implode(",", $zaposlenici)."]. Kandidat ID = [".$kan_id."]";
										}else{
											//echo "Poslano zaposleniku!<br/>";
											$log_desc = "DIPL - Zahtjev za nostrifikaciju - Poslan e-mail zaposlenicima da kandidatu nije poslan e-mail sa zahtjevom. Zaposlenici ID = [".implode(",", $zaposlenici)."]. Kandidat ID = [".$kan_id."]";
										}
									} else {
										$log_desc = "DIPL - Zahtjev za nostrifikaciju - Nije poslan e-mail zaposlenicima da kandidatu nije poslan e-mail sa zahtjevom. Zaposlenici ID = [".implode(",", $zaposlenici)."]. Kandidat ID = [".$kan_id."]. Code: Svi zaposlenici su deaktivirani!";
									}
									addLogDIPL($log_desc);
								}
							}else{
								$log_desc = "DIPL - Zahtjev za nostrifikaciju - Poslan e-mail kandidatu sa zahtjevom. Kandidat ID = [".$kan_id."]";
								addLogDIPL($log_desc);
								$log_desc = "";
								if($mobilni_nd_kandidata != ""){
									$active_provider = getActiveProviderForSendingMessages(7);
									if ($active_provider == 1) {
										$params = array(
				
											"scenarioKey" => "7A32331B2103607D2F890C04FEB34942",
											"destinations" => array(
												"to" => array(
													"phoneNumber" => $mobilni_nd_kandidata,
													)
											),
											"sms" => array(
												"text" =>$textSMS,
												),
											"viber" => array(
												"text" => $textViber,
												//"imageURL" => $imageURL,
												//"buttonText" => $buttonText,
												//"buttonURL" => $link,
												// "isPromotional" => "true"
											)
										);

										$data = json_encode($params);
										$curl = curl_init();

										curl_setopt_array($curl, array(
										CURLOPT_URL => "https://ej8w3r.api.infobip.com/omni/1/advanced",
										CURLOPT_RETURNTRANSFER => true,
										CURLOPT_ENCODING => "",
										CURLOPT_MAXREDIRS => 10,
										CURLOPT_TIMEOUT => 30,
										CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
										CURLOPT_CUSTOMREQUEST => "POST",
										CURLOPT_POSTFIELDS => $data,
										CURLOPT_HTTPHEADER => array(
											"accept: application/json",
											"authorization: Basic ZWJlbmRlcjE6RUJlbmRlcjY0MzI=",
											"content-type: application/json"
										),
										));

										$response = curl_exec($curl);
										$err = curl_error($curl);

										curl_close($curl);

										if ($err) {
										//echo "cURL Error #:" . $err;
										$log_desc = "DIPL - Zahtjev za nostrifikaciju - Greška kod slanja Viber/SMS poruke. Kandidat ID = [".$kan_id."]";
										} else {
										//echo $response;
										$log_desc = "DIPL - Zahtjev za nostrifikaciju - Poslana zahtjev za slanje Viber/SMS poruke. Kandidat ID = [".$kan_id."]";
										}
										addLogDIPL($log_desc);
									} else {
										$phoneNumber = checkPhoneNumberForNTH($mobilni_nd_kandidata);
										$params = array(
											"channels" => array(
												"VIBER",
												"SMS"
											),
											"destinations" => array(
												array(
													"phoneNumber" => $phoneNumber
												)
											),
											"viber" => array(
												"priority" => 1,
												"sender" => "Jobstep Int",
												"text" => $textViber,
												"ttl" => 14440,
												"label" => "promotion"
											),
											"sms" => array(
												"priority" => 2,
												"sender" => "Jobstep Int",
												"text" => $textSMS
											)
										);
										$params_encode = json_encode($params);
										$response = sendMessageViaNTH($params_encode);
										$log_desc = "DIPL - Zahtjev za nostrifikaciju - Poslana zahtjev za slanje Viber/SMS poruke. Kandidat ID = [".$kan_id."]";
										addLogDIPL($log_desc);
									}
									$log_desc = "";
								}
								if(count($zaposlenici)){
									$mail1 = new PHPMailer;
									$mail->isSMTP();

									$mail1->Host = 'smtp.gmail.com';                 // Specify main and backup SMTP servers
									$mail1->SMTPAuth = true;                         // Enable SMTP authentication
									$mail1->Username = 'support@job-step.com';        // SMTP username
									$mail1->Password = 'eooc nnxo aylp lqkh';          // SMTP password
									$mail1->SMTPSecure = 'ssl';                      // Enable TLS encryption, `ssl` also accepted
									$mail1->Port = 465;                              // TCP port to connect to
									$mail1->CharSet = 'UTF-8';

									//Recipients
									$mail1->setFrom('support@job-step.com', 'JobStep');
									$cnt_zaposlenici = 0; 
									foreach ($zaposlenici as $value) {
										$user_query = $db->prepare("
											SELECT 
												employee_email
											FROM 
												idk_employees
											WHERE 
												employee_id = :employee_id
												AND 
												employee_status NOT LIKE '0'
										");

										$user_query->execute(array(
											':employee_id' => $value
										));
										if ($user_query->rowCount() != 0) {
											$user = $user_query->fetch();
											$employee_email = $user['employee_email'];
											
											$mail1->addAddress($employee_email); // Add a recipient
											$cnt_zaposlenici++;
										}
									}
									$mail1->AddAttachment($putanjaDocumenta); 
									
									$mail1->Subject = "Zahtjev za nostrifikaciju ".$ime_nd_kandidata." ".$prezime_nd_kandidata."";
									$mail1->Body = "
										<p>
											Kandidatu je automatski poslan Zahtjev za nostrifikaciju na e-mail adresu.<br>
											Kandidat: ".$ime_nd_kandidata." ".$prezime_nd_kandidata." <br>
											Broj telefona: ".$mobilni_nd_kandidata." <br>
										</p>
										<p>
											Detalje pogledajte na linku: " . getSiteUrlr() . "/nostrifikacija_diploma?page=otvori_ND_kandidata&id=".$kan_id."
										</p>
										<br><br><br>
										<img src='cid:logo_2u1' width='100%'>
									";
									
									$mail1->AddEmbeddedImage($putanja_signatura, 'logo_2u1');
									
									$mail1->AltBody = "ALT";
									if ($cnt_zaposlenici > 0) {
										if(!$mail1->send()) {
											//echo 'Mailer Error: ' . $mail1->ErrorInfo;
											//echo "Nije poslano zaposleniku!<br/>";
											$log_desc = "DIPL - Zahtjev za nostrifikaciju - Nije poslan e-mail zaposlenicima da je kandidatu poslan e-mail sa zahtjevom. Zaposlenici ID = [".implode(",", $zaposlenici)."]. Kandidat ID = [".$kan_id."]";
										}else{
											//echo "Poslano zaposleniku!<br/>";
											$log_desc = "DIPL - Zahtjev za nostrifikaciju - Poslan e-mail zaposlenicima da je kandidatu poslan e-mail sa zahtjevom. Zaposlenici ID = [".implode(",", $zaposlenici)."]. Kandidat ID = [".$kan_id."]";
										}
									} else {
										$log_desc = "DIPL - Zahtjev za nostrifikaciju - Nije poslan e-mail zaposlenicima da je kandidatu poslan e-mail sa zahtjevom. Zaposlenici ID = [".implode(",", $zaposlenici)."]. Kandidat ID = [".$kan_id."]. Code: Svi zaposlenici su deaktivirani!";
									}
									addLogDIPL($log_desc);
								}
							}
						}else{
							$flagCheck = 2;
						}
						
					}else{
						$flagCheck = 2;
					}
				}else{
					$flagCheck = 2;
				}
			}else{
				$flagCheck = 2;
			}
		}else{
			$flagCheck = 2;
		}
		
		if($flagCheck == 2){
			array_push($zaposlenici, 43);
			array_push($zaposlenici, 203);
			$putanja_signatura = $_SERVER['DOCUMENT_ROOT']."/files/ugovori_uplatnice_dipl/signatura_bih.jpg";
			$mail = new PHPMailer;
			$mail->isSMTP();											// Set mailer to use SMTP

			$mail->Host = 'smtp.gmail.com';                 // Specify main and backup SMTP servers
			$mail->SMTPAuth = true;                         // Enable SMTP authentication
			$mail->Username = 'support@job-step.com';        // SMTP username
			$mail->Password = 'eooc nnxo aylp lqkh';          // SMTP password
			$mail->SMTPSecure = 'ssl';                      // Enable TLS encryption, `ssl` also accepted
			$mail->Port = 465;                              // TCP port to connect to
			$mail->CharSet = 'UTF-8';

			//Recipients
			$mail->setFrom('support@job-step.com', 'JobStep');
			$cnt_zaposlenici = 0;
			foreach ($zaposlenici as $value) {
				$user_query = $db->prepare("
					SELECT 
						employee_email
					FROM 
						idk_employees
					WHERE 
						employee_id = :employee_id
						AND 
						employee_status NOT LIKE '0'
				");

				$user_query->execute(array(
					':employee_id' => $value
				));
				if ($user_query->rowCount() != 0) {
					$user = $user_query->fetch();
					$employee_email = $user['employee_email'];
					
					$mail->addAddress($employee_email); // Add a recipient
					$cnt_zaposlenici++; 
				}
			}
			
			$mail->Subject = "Obavijest";
			$mail->Body = "
				Kandidatu nije poslan automatski Zahtjev za Nostrifikaciju. <br>
				
				Detalje pogledajte na linku: " . getSiteUrlr() . "/nostrifikacija_diploma?page=otvori_ND_kandidata&id=".$kan_id."<br><br><br>
				<img src='cid:logo_2u' width='100%'>
			";
			
			$mail->AddEmbeddedImage($putanja_signatura, 'logo_2u');
			
			$mail->AltBody = "ALT";
			//$mail_bh->send();
			if ($cnt_zaposlenici > 0) {
				if(!$mail->send()) {
					//echo "Flag 2 : Nije poslano!";
					$log_desc = "DIPL - Zahtjev za nostrifikaciju - Nije poslan e-mail zaposlenicima da kandidatu nije poslan e-mail sa zahtjevom. Zaposlenici ID = [".implode(",", $zaposlenici)."]. Kandidat ID = [".$kan_id."].";
				}else{
					//echo "Flag 2 : Poslano!";
					$log_desc = "DIPL - Zahtjev za nostrifikaciju - Poslan e-mail zaposlenicima da kandidatu nije poslan e-mail sa zahtjevom. Zaposlenici ID = [".implode(",", $zaposlenici)."]. Kandidat ID = [".$kan_id."].";
				}
			} else {
				$log_desc = "DIPL - Zahtjev za nostrifikaciju - Nije poslan e-mail zaposlenicima da kandidatu nije poslan e-mail sa zahtjevom. Zaposlenici ID = [".implode(",", $zaposlenici)."]. Kandidat ID = [".$kan_id."]. Code: Svi zaposlenici su deaktivirani!";
			}
			addLogDIPL($log_desc);
		}
	}
	
	//Funkcija za check listu dokumenata DIPL kandidatu START
	function sendMailViberCheckListDIPL($predracun_id){
		Global $db;
		$pr_id = intval($predracun_id);
		$zaposlenici = array();
		if($pr_id != 0){
			
			$query = $db->prepare("
				SELECT pr.pr_id, pr.pr_naplata_preko, pr.pr_kandidat_id, pr.pr_domaca_valuta, kan.ime_nd_kandidata, kan.prezime_nd_kandidata, kan.mobilni_nd_kandidata, kan.email_nd_kandidata, kan.vrsta_obrade, kan.vrsta_ugovora_nd_kandidata, pr.pr_rata
				FROM idk_predracuni pr 
				INNER JOIN idk_nd_kandidata kan
				ON pr.pr_kandidat_id = kan.id_broj_nd_kandidata
				WHERE pr.pr_id = ".$pr_id." AND pr.pr_uplaceno = 1
			");
			$query->execute();
			if(intval($query->rowCount()) != 0){
				$row = $query->fetch();
				$pr_id = intval($row["pr_id"]);
				$pr_naplata_preko = intval($row["pr_naplata_preko"]);
				$pr_kandidat_id = intval($row["pr_kandidat_id"]);
				$pr_domaca_valuta = $row["pr_domaca_valuta"];
				$ime_nd_kandidata = $row["ime_nd_kandidata"];
				$prezime_nd_kandidata = $row["prezime_nd_kandidata"];
				$mobilni_nd_kandidata = $row["mobilni_nd_kandidata"];
				$email_nd_kandidata = $row["email_nd_kandidata"];
				$vrsta_obrade = intval($row["vrsta_obrade"]);
				$vrsta_ugovora_nd_kandidata = intval($row["vrsta_ugovora_nd_kandidata"]);
				$pr_rata = intval($row["pr_rata"]);
				
				//Uslov za kandidate koji imaju ugovor sa 100% popusta START
					$posredovanjeFlag = "";
					if($vrsta_ugovora_nd_kandidata == 99 AND ($pr_domaca_valuta == "BAM" OR $pr_domaca_valuta == "EUR" OR $pr_domaca_valuta == "RSD")){
						//Svi kandidati koji imaju 100 % popusta i kojima je domaca valuta iz uslova
						$posredovanjeFlag = "P";
					}
				//Uslov za kandidate koji imaju ugovor sa 100% popusta START


				if($pr_rata == 1){
					$putanja_drzava = "";
					$putanja_sig = "";
					$putanja_dokumenta = "";
					$putanja_punomoc = "";
					$putanja_signatura = "";
					$hostName = "smtp.gmail.com";
					$userName = "support@job-step.com";
					$password = "eooc nnxo aylp lqkh";
					$setFrom = "support@job-step.com";
					$textSMS = "";
					$textViber = "";
					$naslovMail = "";
					$textMail = "";
					$firmaDokument = "";
					if($pr_naplata_preko == 1){
						$firmaDokument = "JSGmbh";
					} else if($pr_naplata_preko == 2) {
						$firmaDokument = "";
					} else{
						$firmaDokument = "";
					}
					
					if($vrsta_obrade != 0){
						
						if($pr_domaca_valuta == "BAM"){
							array_push($zaposlenici, 43);
							$putanja_drzava = "BiH";

							if($pr_naplata_preko == 1){
								$putanja_sig = "nostrifikacija_CH.gif";
							}else{
								$putanja_sig = "signatura_bih.jpg";
							}
							
							// $hostName = "smtp.strato.de;smtp.strato.de";
							// $userName = "obrada@job-step.net";
							// $password = "Obrada6432BH";
							// $setFrom = "obrada@job-step.net";
							
							$textSMS = "Poštovani/a ".$ime_nd_kandidata." ".$prezime_nd_kandidata."";
							$textSMS .= "Na vašu e-mail adresu navedenu u prijavi za Nostrifikaciju diplome, poslan je spisak dokumentacije koju nam trebate dostaviti.";
							$textSMS .= "Provjerite Vašu e-mail adresu (Inbox, Spam ili Junk).";
							$textSMS .= "U slučaju dodatnih pitanja, kontaktirajte na broj: +387 37 961 346.";
							$textSMS .= "Vaš JobStep.";
							
							$textViber = "Poštovani/a ".$ime_nd_kandidata." ".$prezime_nd_kandidata.",\n";
							$textViber .= "na vašu e-mail adresu navedenu u prijavi za Nostrifikaciju diplome, poslan je spisak dokumentacije koju nam trebate dostaviti.\n";
							$textViber .= "Provjerite Vašu e-mail adresu (Inbox, Spam ili Junk).\n";
							$textViber .= "U slučaju dodatnih pitanja, kontaktirajte na broj:\n +387 37 961 346\n";
							$textViber .= "Vaš JobStep.";
							
							$naslovMail = "Spisak dokumentacije";
							
							$textMail = "
								Poštovani/a ".$ime_nd_kandidata." ".$prezime_nd_kandidata.", <br>
								u prilogu Vam dostavljamo spisak dokumentacije koju nam trebate dostaviti u svrhu postupka Nostrifikacije diplome. <br>
								Dokumente je potrebno dostaviti na dolje navedenu adresu. <br>
								U slučaju dodatnih pitanja stojimo Vam na raspolaganju na broj: +387 37 961 346.<br>
								Vaš JobStep!
							";
							
						}else if($pr_domaca_valuta == "EUR"){
							array_push($zaposlenici, 43);
							array_push($zaposlenici, 203);
							$putanja_drzava = "BiH";
							
							if($pr_naplata_preko == 1){
								$putanja_sig = "nostrifikacija_CH.gif";
							}else{
								$putanja_sig = "signatura_bih.jpg";
							}

							// $hostName = "smtp.strato.de;smtp.strato.de";
							// $userName = "obrada@job-step.net";
							// $password = "Obrada6432BH";
							// $setFrom = "obrada@job-step.net";
							
							$textSMS = "Poštovani/a ".$ime_nd_kandidata." ".$prezime_nd_kandidata."";
							$textSMS .= "Na vašu e-mail adresu navedenu u prijavi za Nostrifikaciju diplome, poslan je spisak dokumentacije koju nam trebate dostaviti.";
							$textSMS .= "Provjerite Vašu e-mail adresu (Inbox, Spam ili Junk).";
							$textSMS .= "U slučaju dodatnih pitanja, kontaktirajte na broj: +387 37 961 346.";
							$textSMS .= "Vaš JobStep.";
							
							$textViber = "Poštovani/a ".$ime_nd_kandidata." ".$prezime_nd_kandidata.",\n";
							$textViber .= "na vašu e-mail adresu navedenu u prijavi za Nostrifikaciju diplome, poslan je spisak dokumentacije koju nam trebate dostaviti.\n";
							$textViber .= "Provjerite Vašu e-mail adresu (Inbox, Spam ili Junk).\n";
							$textViber .= "U slučaju dodatnih pitanja, kontaktirajte na broj:\n +387 37 961 346\n";
							$textViber .= "Vaš JobStep.";
							
							$naslovMail = "Spisak dokumentacije";
							
							$textMail = "
								Poštovani/a ".$ime_nd_kandidata." ".$prezime_nd_kandidata.", <br>
								u prilogu Vam dostavljamo spisak dokumentacije koju nam trebate dostaviti u svrhu postupka Nostrifikacije diplome. <br>
								Dokumente je potrebno dostaviti na dolje navedenu adresu. <br>
								U slučaju dodatnih pitanja stojimo Vam na raspolaganju na broj: +387 37 961 346.<br>
								Vaš JobStep!
							";
							
						}else if($pr_domaca_valuta == "RSD"){
							array_push($zaposlenici, 203);
							$putanja_drzava = "SRB";
							
							if($pr_naplata_preko == 1){
								$putanja_sig = "nostrifikacija_CH.gif";
							} else if ($pr_naplata_preko == 2) {
								$putanja_sig = "signatura-SRB-automatski-mail-new.gif"; /*Nova signatura*/
							} else{
								$putanja_sig = "signatura_srb.jpg";
							}

							// $hostName = "smtp.strato.de;smtp.strato.de";
							// $userName = "obrada@job-step.net";
							// $password = "Obrada6432BH";
							// $setFrom = "obrada@job-step.net";
							
							$textSMS = "Poštovani/a ".$ime_nd_kandidata." ".$prezime_nd_kandidata."";
							$textSMS .= "Na vašu e-mail adresu navedenu u prijavi za Nostrifikaciju diplome, poslan je spisak dokumentacije koju nam trebate dostaviti.";
							$textSMS .= "Proverite Vašu e-mail adresu (Inbox, Spam ili Junk).";
							$textSMS .= "U slučaju dodatnih pitanja, kontaktirajte na broj: +381 62 274 607.";
							$textSMS .= "Vaš JobStep.";
							
							$textViber = "Poštovani/a ".$ime_nd_kandidata." ".$prezime_nd_kandidata.",\n";
							$textViber .= "na vašu e-mail adresu navedenu u prijavi za Nostrifikaciju diplome, poslan je spisak dokumentacije koju nam trebate dostaviti.\n";
							$textViber .= "Proverite Vašu e-mail adresu (Inbox, Spam ili Junk).";
							$textViber .= "U slučaju dodatnih pitanja, kontaktirajte na broj:\n +381 62 274 607\n";
							$textViber .= "Vaš JobStep.";
							
							$naslovMail = "Spisak dokumentacije";
							
							$textMail = "
								Poštovani/a ".$ime_nd_kandidata." ".$prezime_nd_kandidata.", <br>
								u prilogu Vam dostavljamo spisak dokumentacije koju nam trebate dostaviti u svrhu postupka Nostrifikacije diplome. <br>
								Dokumente je potrebno dostaviti na dolje navedenu adresu. <br>
								U slučaju dodatnih pitanja stojimo Vam na raspolaganju na broj: +381 62 274 607.<br>
								Vaš JobStep!
							";
							
						}else{
							$putanja_drzava = "";
							$putanja_sig = "";
							$hostName = "";
							$userName = "";
							$password = "";
							$setFrom = "";
							$textSMS = "";
							$textViber = "";
							$naslovMail = "";
							$textMail = "";
						}
						
						if($vrsta_obrade == 1){
							// 1 -> aubildung
							$putanja_dokumenta = $_SERVER['DOCUMENT_ROOT']."/files/obrada_DIPL/ausbildung".$putanja_drzava.".pdf";
							$putanja_punomoc = $_SERVER['DOCUMENT_ROOT']."/files/obrada_DIPL/punomoc".$firmaDokument."".$putanja_drzava.".pdf";
							$putanja_signatura = $_SERVER['DOCUMENT_ROOT']."/files/ugovori_uplatnice_dipl/".$putanja_sig;
						}else if($vrsta_obrade == 2){
							// 2 -> srednja skola
							$putanja_dokumenta = $_SERVER['DOCUMENT_ROOT']."/files/obrada_DIPL/sss".$posredovanjeFlag.$putanja_drzava.".pdf";
							$putanja_punomoc = $_SERVER['DOCUMENT_ROOT']."/files/obrada_DIPL/punomoc".$firmaDokument."".$putanja_drzava.".pdf";
							$putanja_signatura = $_SERVER['DOCUMENT_ROOT']."/files/ugovori_uplatnice_dipl/".$putanja_sig;
						}else{
							// 3 -> visoka skola
							$putanja_dokumenta = $_SERVER['DOCUMENT_ROOT']."/files/obrada_DIPL/vss".$posredovanjeFlag.$putanja_drzava.".pdf";
							$putanja_punomoc = $_SERVER['DOCUMENT_ROOT']."/files/obrada_DIPL/punomoc".$firmaDokument."".$putanja_drzava.".pdf";
							$putanja_signatura = $_SERVER['DOCUMENT_ROOT']."/files/ugovori_uplatnice_dipl/".$putanja_sig;
						}
						
						//Slanje maila
						if($putanja_drzava != ""){
							$mail = new PHPMailer;

							$mail->isSMTP();											// Set mailer to use SMTP

							$mail->Host = $hostName;				// Specify main and backup SMTP servers
							$mail->SMTPAuth = true;										// Enable SMTP authentication
							$mail->Username = $userName;					// SMTP username
							$mail->Password = $password;							// SMTP password
							$mail->SMTPSecure = 'ssl';									// Enable TLS encryption, `ssl` also accepted
							$mail->Port = 465;											// TCP port to connect to
							$mail->CharSet = 'UTF-8';
							
							$mail->setFrom($setFrom, 'JobStep');    		// Add a recipient
							$mail->addAddress($email_nd_kandidata);		// Add a recipient
							$mail->AddAttachment($putanja_dokumenta); 
							$mail->AddAttachment($putanja_punomoc); 
							
							$mail->Subject = "".$naslovMail."";
							$mail->Body = "
								".$textMail."
								<img src='cid:logo_2u'>
							";
							
							$mail->AddEmbeddedImage($putanja_signatura, 'logo_2u');
							
							$mail->AltBody = "ALT";
							//$mail_bh->send();
							if(!$mail->send()) {
								// echo 'Mailer Error: ' . $mail->ErrorInfo;
								// echo "Nije poslano<br/>";
								if(count($zaposlenici)){
									$mail1 = new PHPMailer;
									$mail->isSMTP();
									$mail1->Host = 'smtp.gmail.com';                 // Specify main and backup SMTP servers
									$mail1->SMTPAuth = true;                         // Enable SMTP authentication
									$mail1->Username = 'support@job-step.com';        // SMTP username
									$mail1->Password = 'eooc nnxo aylp lqkh';          // SMTP password
									$mail1->SMTPSecure = 'ssl';                      // Enable TLS encryption, `ssl` also accepted
									$mail1->Port = 465;                              // TCP port to connect to
									$mail1->CharSet = 'UTF-8';

									//Recipients
									$mail1->setFrom('support@job-step.com', 'JobStep');
									$cnt_zaposlenici = 0;
									foreach ($zaposlenici as $value) {
										$user_query = $db->prepare("
											SELECT 
												employee_email
											FROM 
												idk_employees
											WHERE 
												employee_id = :employee_id
												AND 
												employee_status NOT LIKE '0'
										");

										$user_query->execute(array(
											':employee_id' => $value
										));
										if ($user_query->rowCount() != 0) {
											$user = $user_query->fetch();
											$employee_email = $user['employee_email'];
											
											$mail1->addAddress($employee_email); // Add a recipient
											$cnt_zaposlenici++;
										}
									}
									$mail1->AddAttachment($putanja_dokumenta); 
									$mail1->AddAttachment($putanja_punomoc); 
									
									$mail1->Subject = "Spisak dokumentacije ".$ime_nd_kandidata." ".$prezime_nd_kandidata."";
									$mail1->Body = "
										<p>
											Kandidatu nije automatski poslan spisak dokumentacije na e-mail adresu.<br>
											Potrebno je dostaviti spisak dokumentacije. <br><br>
											Kandidat: ".$ime_nd_kandidata." ".$prezime_nd_kandidata." <br>
											Broj telefona: ".$mobilni_nd_kandidata." <br>
										</p>
										<p>
											Detalje pogledajte na linku: " . getSiteUrlr() . "/nostrifikacija_diploma?page=otvori_ND_kandidata&id=".$pr_kandidat_id."
										</p>
										<br><br><br>
										<img src='cid:logo_2u1'>
									";
									
									$mail1->AddEmbeddedImage($putanja_signatura, 'logo_2u1');
									
									$mail1->AltBody = "ALT";
									if ($cnt_zaposlenici > 0) {
										if(!$mail1->send()) {
											// echo 'Mailer Error: ' . $mail1->ErrorInfo;
											// echo "Nije poslano zaposleniku!<br/>";
											$log_desc = "DIPL - Spisak dokumentacije - Nije poslan e-mail zaposlenicima da kandidatu nije poslan e-mail sa spiskom dokumentacije. Zaposlenici ID = [".implode(",", $zaposlenici)."]. Kandidat ID = [".$pr_kandidat_id."]";
											
										}else{
											// echo "Poslano zaposleniku!<br/>";
											$log_desc = "DIPL - Spisak dokumentacije - Poslan e-mail zaposlenicima da kandidatu nije poslan e-mail sa spiskom dokumentacije. Zaposlenici ID = [".implode(",", $zaposlenici)."]. Kandidat ID = [".$pr_kandidat_id."]";
										}
									} else {
										$log_desc = "DIPL - Spisak dokumentacije - Nije poslan e-mail zaposlenicima da kandidatu nije poslan e-mail sa spiskom dokumentacije. Zaposlenici ID = [".implode(",", $zaposlenici)."]. Kandidat ID = [".$pr_kandidat_id."]. Code: Svi zaposlenici su deaktivirani!";
									}
									addLogDIPL($log_desc);
								}
							}else{
								$log_desc = "DIPL - Spisak dokumentacije - Poslan e-mail kandidatu sa spiskom dokumentacije. Kandidat ID = [".$pr_kandidat_id."]";
								addLogDIPL($log_desc);
								$log_desc = "";
								//echo 'Mailer Error: ' . $mail_bh->ErrorInfo;
								//echo "Poslano<br/>";
								if($mobilni_nd_kandidata != ""){
									$active_provider = getActiveProviderForSendingMessages(8);
									if ($active_provider == 1) {
										$params = array(
				
											"scenarioKey" => "7A32331B2103607D2F890C04FEB34942",
											"destinations" => array(
												"to" => array(
													"phoneNumber" => $mobilni_nd_kandidata,
													)
											),
											"sms" => array(
												"text" =>$textSMS,
												),
											"viber" => array(
												"text" => $textViber,
												//"imageURL" => $imageURL,
												//"buttonText" => $buttonText,
												//"buttonURL" => $link,
												// "isPromotional" => "true"
											)
										);

										$data = json_encode($params);
										$curl = curl_init();

										curl_setopt_array($curl, array(
										CURLOPT_URL => "https://ej8w3r.api.infobip.com/omni/1/advanced",
										CURLOPT_RETURNTRANSFER => true,
										CURLOPT_ENCODING => "",
										CURLOPT_MAXREDIRS => 10,
										CURLOPT_TIMEOUT => 30,
										CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
										CURLOPT_CUSTOMREQUEST => "POST",
										CURLOPT_POSTFIELDS => $data,
										CURLOPT_HTTPHEADER => array(
											"accept: application/json",
											"authorization: Basic ZWJlbmRlcjE6RUJlbmRlcjY0MzI=",
											"content-type: application/json"
										),
										));

										$response = curl_exec($curl);
										$err = curl_error($curl);

										curl_close($curl);
										
										if ($err) {
										//echo "cURL Error #:" . $err;
										$log_desc = "DIPL - Spisak dokumentacije - Greška kod slanja Viber/SMS poruke. Kandidat ID = [".$pr_kandidat_id."]";
										} else {
										//echo $response;
										$log_desc = "DIPL - Spisak dokumentacije - Poslana zahtjev za slanje Viber/SMS poruke. Kandidat ID = [".$pr_kandidat_id."]";
										}
										addLogDIPL($log_desc);
									} else {
										$phoneNumber = checkPhoneNumberForNTH($mobilni_nd_kandidata);
										$params = array(
											"channels" => array(
												"VIBER",
												"SMS"
											),
											"destinations" => array(
												array(
													"phoneNumber" => $phoneNumber
												)
											),
											"viber" => array(
												"priority" => 1,
												"sender" => "Jobstep Int",
												"text" => $textViber,
												"ttl" => 14440,
												"label" => "promotion"
											),
											"sms" => array(
												"priority" => 2,
												"sender" => "Jobstep Int",
												"text" => $textSMS
											)
										);
										$params_encode = json_encode($params);
										$response = sendMessageViaNTH($params_encode);
										$log_desc = "DIPL - Spisak dokumentacije - Poslana zahtjev za slanje Viber/SMS poruke. Kandidat ID = [".$pr_kandidat_id."]";
										addLogDIPL($log_desc);
									}
									$log_desc = "";
								}
								if(count($zaposlenici)){
									$mail1 = new PHPMailer;
									$mail->isSMTP();
									$mail1->Host = 'smtp.gmail.com';                 // Specify main and backup SMTP servers
									$mail1->SMTPAuth = true;                         // Enable SMTP authentication
									$mail1->Username = 'support@job-step.com';        // SMTP username
									$mail1->Password = 'eooc nnxo aylp lqkh';          // SMTP password
									$mail1->SMTPSecure = 'ssl';                      // Enable TLS encryption, `ssl` also accepted
									$mail1->Port = 465;                              // TCP port to connect to
									$mail1->CharSet = 'UTF-8';

									//Recipients
									$mail1->setFrom('support@job-step.com', 'JobStep');
									$cnt_zaposlenici = 0; 
									foreach ($zaposlenici as $value) {
										$user_query = $db->prepare("
											SELECT 
												employee_email
											FROM 
												idk_employees
											WHERE 
												employee_id = :employee_id
												AND 
												employee_status NOT LIKE '0'
										");

										$user_query->execute(array(
											':employee_id' => $value
										));
										if ($user_query->rowCount() != 0) {
											$user = $user_query->fetch();
											$employee_email = $user['employee_email'];
											
											$mail1->addAddress($employee_email); // Add a recipient
											$cnt_zaposlenici++; 
										}
									}
									$mail1->AddAttachment($putanja_dokumenta); 
									$mail1->AddAttachment($putanja_punomoc); 
									
									$mail1->Subject = "Spisak dokumentacije ".$ime_nd_kandidata." ".$prezime_nd_kandidata."";
									$mail1->Body = "
										<p>
											Kandidatu je automatski poslan spisak dokumentacije na e-mail adresu.<br>
											Kandidat: ".$ime_nd_kandidata." ".$prezime_nd_kandidata." <br>
											Broj telefona: ".$mobilni_nd_kandidata." <br>
										</p>
										<p>
											Detalje pogledajte na linku: " . getSiteUrlr() . "/nostrifikacija_diploma?page=otvori_ND_kandidata&id=".$pr_kandidat_id."
										</p>
										<br><br><br>
										<img src='cid:logo_2u1'>
									";
									
									$mail1->AddEmbeddedImage($putanja_signatura, 'logo_2u1');
									
									$mail1->AltBody = "ALT";
									if ($cnt_zaposlenici > 0) {
										if(!$mail1->send()) {
											// echo 'Mailer Error: ' . $mail1->ErrorInfo;
											// echo "Nije poslano zaposleniku!<br/>";
											$log_desc = "DIPL - Spisak dokumentacije - Nije poslan e-mail zaposlenicima da je kandidatu poslan e-mail sa spiskom dokumentacije. Zaposlenici ID = [".implode(",", $zaposlenici)."]. Kandidat ID = [".$pr_kandidat_id."]";
											
										}else{
											// echo "Poslano zaposleniku!<br/>";
											$log_desc = "DIPL - Spisak dokumentacije - Poslan e-mail zaposlenicima da je kandidatu poslan e-mail sa spiskom dokumentacije. Zaposlenici ID = [".implode(",", $zaposlenici)."]. Kandidat ID = [".$pr_kandidat_id."]";
										}
									} else {
										$log_desc = "DIPL - Spisak dokumentacije - Nije poslan e-mail zaposlenicima da je kandidatu poslan e-mail sa spiskom dokumentacije. Zaposlenici ID = [".implode(",", $zaposlenici)."]. Kandidat ID = [".$pr_kandidat_id."]. Code: Svi zaposlenici su deaktivirani!";
									}
									addLogDIPL($log_desc);
								}
							}
						}
						
					}else{
						$hostName = "smtp.gmail.com";
						$userName = "support@job-step.com";
						$password = "eooc nnxo aylp lqkh";
						$setFrom = "support@job-step.com";
						$putanja_sig = "";
						$putanja_signatura = "";
						$putanja_punomoc = "";
						$putanja_document_1 = "";
						$putanja_document_2 = "";
						$putanja_document_3 = "";
						$putanja_document_4 = "";
						$putanja_document_5 = "";
						//prema domacoj valuti stavlja u niz kome mail da salje
						if($pr_domaca_valuta == "BAM"){
							array_push($zaposlenici, 43);
							if($pr_naplata_preko == 1){
								$putanja_sig = "nostrifikacija_CH.gif";
							}else{
								$putanja_sig = "signatura_bih.jpg";
							}
							// $hostName = "smtp.strato.de;smtp.strato.de";
							// $userName = "obrada@job-step.net";
							// $password = "Obrada6432BH";
							// $setFrom = "obrada@job-step.net";
							$putanja_signatura = $_SERVER['DOCUMENT_ROOT']."/files/ugovori_uplatnice_dipl/".$putanja_sig;
							$putanja_punomoc = $_SERVER['DOCUMENT_ROOT']."/files/obrada_DIPL/punomoc".$firmaDokument."BiH.pdf";
							$putanja_document_1 = $_SERVER['DOCUMENT_ROOT']."/files/obrada_DIPL/ausbildungBiH.pdf";
							$putanja_document_2 = $_SERVER['DOCUMENT_ROOT']."/files/obrada_DIPL/vssBiH.pdf";
							$putanja_document_3 = $_SERVER['DOCUMENT_ROOT']."/files/obrada_DIPL/sssBiH.pdf";
							$putanja_document_4 = $_SERVER['DOCUMENT_ROOT']."/files/obrada_DIPL/sssPBiH.pdf";
							$putanja_document_5 = $_SERVER['DOCUMENT_ROOT']."/files/obrada_DIPL/vssPBiH.pdf";
						}else if($pr_domaca_valuta == "EUR"){
							array_push($zaposlenici, 43);
							array_push($zaposlenici, 203);
							if($pr_naplata_preko == 1){
								$putanja_sig = "nostrifikacija_CH.gif";
							} else{
								$putanja_sig = "signatura_bih.jpg";
							}
							// $hostName = "smtp.strato.de;smtp.strato.de";
							// $userName = "obrada@job-step.net";
							// $password = "Obrada6432BH";
							// $setFrom = "obrada@job-step.net";
							$putanja_signatura = $_SERVER['DOCUMENT_ROOT']."/files/ugovori_uplatnice_dipl/".$putanja_sig;
							$putanja_punomoc = $_SERVER['DOCUMENT_ROOT']."/files/obrada_DIPL/punomoc".$firmaDokument."BiH.pdf";
							$putanja_document_1 = $_SERVER['DOCUMENT_ROOT']."/files/obrada_DIPL/ausbildungBiH.pdf";
							$putanja_document_2 = $_SERVER['DOCUMENT_ROOT']."/files/obrada_DIPL/vssBiH.pdf";
							$putanja_document_3 = $_SERVER['DOCUMENT_ROOT']."/files/obrada_DIPL/sssBiH.pdf";
							$putanja_document_4 = $_SERVER['DOCUMENT_ROOT']."/files/obrada_DIPL/sssPBiH.pdf";
							$putanja_document_5 = $_SERVER['DOCUMENT_ROOT']."/files/obrada_DIPL/vssPBiH.pdf";
						}else if($pr_domaca_valuta == "RSD"){
							array_push($zaposlenici, 203);
							if($pr_naplata_preko == 1){
								$putanja_sig = "nostrifikacija_CH.gif";
							} else if ($pr_naplata_preko == 2) {
								$putanja_sig = "signatura-SRB-automatski-mail-new.gif"; /*Nova signatura*/
							} else {
								$putanja_sig = "signatura_srb.jpg";
							}
							// $hostName = "smtp.strato.de;smtp.strato.de";
							// $userName = "obrada@job-step.net";
							// $password = "Obrada6432BH";
							// $setFrom = "obrada@job-step.net";
							$putanja_signatura = $_SERVER['DOCUMENT_ROOT']."/files/ugovori_uplatnice_dipl/".$putanja_sig;
							$putanja_punomoc = $_SERVER['DOCUMENT_ROOT']."/files/obrada_DIPL/punomoc".$firmaDokument."SRB.pdf";
							$putanja_document_1 = $_SERVER['DOCUMENT_ROOT']."/files/obrada_DIPL/ausbildungSRB.pdf";
							$putanja_document_2 = $_SERVER['DOCUMENT_ROOT']."/files/obrada_DIPL/vssSRB.pdf";
							$putanja_document_3 = $_SERVER['DOCUMENT_ROOT']."/files/obrada_DIPL/sssSRB.pdf";
							$putanja_document_4 = "";
							$putanja_document_5 = "";
						}else{
							$hostName = "";
							$userName = "";
							$password = "";
							$setFrom = "";
							$putanja_signatura = "";
							$putanja_punomoc = "";
							$putanja_document_1 = "";
							$putanja_document_2 = "";
							$putanja_document_3 = "";
							$putanja_document_4 = "";
							$putanja_document_5 = "";
						}
						
						if(count($zaposlenici) != 0){
							//Send email to user
							$mail = new PHPMailer;
							$mail->isSMTP();											// Set mailer to use SMTP
							$mail->Host = $hostName;				// Specify main and backup SMTP servers
							$mail->SMTPAuth = true;										// Enable SMTP authentication
							$mail->Username = $userName;					// SMTP username
							$mail->Password = $password;							// SMTP password
							$mail->SMTPSecure = 'ssl';									// Enable TLS encryption, `ssl` also accepted
							$mail->Port = 465;											// TCP port to connect to
							$mail->CharSet = 'UTF-8';
							
							$mail->setFrom($setFrom, 'JobStep');  // Add a recipient
							$cnt_zaposlenici = 0;    		
							foreach ($zaposlenici as $value) {
								$user_query = $db->prepare("
									SELECT 
										employee_email
									FROM 
										idk_employees
									WHERE 
										employee_id = :employee_id
										AND 
										employee_status NOT LIKE '0'
								");

								$user_query->execute(array(
									':employee_id' => $value
								));
								if ($user_query->rowCount() != 0) {
									$user = $user_query->fetch();
									$employee_email = $user['employee_email'];
									
									$mail->addAddress($employee_email); // Add a recipient
									$cnt_zaposlenici++; 
								}
							}
							$mail->AddAttachment($putanja_punomoc); 
							$mail->AddAttachment($putanja_document_1); 
							$mail->AddAttachment($putanja_document_2); 
							$mail->AddAttachment($putanja_document_3);
							if($putanja_document_4 != ""){
								$mail->AddAttachment($putanja_document_4);
							}
							if($putanja_document_5 != ""){
								$mail->AddAttachment($putanja_document_5);
							}
							$mail->Subject = "Dokumentacija ".$ime_nd_kandidata." ".$prezime_nd_kandidata."";
							$mail->Body = "
								<p>
									Kod kandidata nije oznacena vrsta obrade na Nostrifikaciju diplome.<br>
									Kandidatu je potrebno dostaviti spisak dokumentacije. <br><br>
									Kandidat: ".$ime_nd_kandidata." ".$prezime_nd_kandidata." <br>
									Broj telefona: ".$mobilni_nd_kandidata." <br>
								</p>
								<p>
									Detalje pogledajte na linku: " . getSiteUrlr() . "/nostrifikacija_diploma?page=otvori_ND_kandidata&id=".$pr_kandidat_id."
								</p>
								<br><br><br>
								<img src='cid:logo_2u'>
							";
							
							$mail->AddEmbeddedImage($putanja_signatura, 'logo_2u');
							
							$mail->AltBody = "ALT";
							if ($cnt_zaposlenici > 0) {
								if(!$mail->send()) {
									// echo 'Mailer Error: ' . $mail1->ErrorInfo;
									// echo "Nije poslano zaposleniku!<br/>";
									$log_desc = "DIPL - Spisak dokumentacije - Nije poslan e-mail zaposlenicima da kod kandidata nije označena vrsta obrade. Zaposlenici ID = [".implode(",", $zaposlenici)."]. Kandidat ID = [".$pr_kandidat_id."]";
									
								}else{
									// echo "Poslano zaposleniku!<br/>";
									$log_desc = "DIPL - Spisak dokumentacije - Poslan e-mail zaposlenicima da kod kandidata nije označena vrsta obrade. Zaposlenici ID = [".implode(",", $zaposlenici)."]. Kandidat ID = [".$pr_kandidat_id."]";
								}
							} else {
								$log_desc = "DIPL - Spisak dokumentacije - Nije poslan e-mail zaposlenicima da kod kandidata nije označena vrsta obrade. Zaposlenici ID = [".implode(",", $zaposlenici)."]. Kandidat ID = [".$pr_kandidat_id."]. Code: Svi zaposlenici su deaktivirani!";
							}
							addLogDIPL($log_desc);
						}
					}
				}
			}
		}
	}
	//Funkcija za check listu dokumenata DIPL kandidatu END 
	
//DIPL OBRADA Funkcije za slanje Viber SmS Email poruka sa zahtjevom i spiskom dokumenatacije END

	/**
	*
	*	FUNKCIJA GENERISANJA SLJEDEĆEG KANDIDATA ZA POZIV
	*	FUNKCIJA GENERISANJA SQL IZRAZA ZA POTREBNE UPITE
	* 	@function getNextCandidate();
	*	@function generateSqlStatement(int $case);
	*
	*/
	/*
	function generateSqlStatement($case){
		global $logged_employee_id;
		
		switch($case){
			// case "FB/IG":
			case 1:
				return "WHERE kampanja_id = 100 AND status_nd_kandidata = 1 AND pstatus_nd_kandidata = 1 AND zaduzen_zaposlenik_nd_kandidata  = ".$logged_employee_id . " ORDER BY vrijeme_kreiranja_nd_kandidata ASC";
			break;
			// case "Prioritet 1":
			case 2:
				return "INNER JOIN
							idk_ponovne_prijave pp
						ON 
							id_broj_nd_kandidata = pp.id_kandidata
							AND
							pp.id_pp = (
								SELECT 
									MAX(ppk.id_pp)
								FROM 
									idk_ponovne_prijave ppk
								WHERE 
									ppk.id_kandidata = id_broj_nd_kandidata
							)
						WHERE 
							status_pp = 1
							AND 
							(
								(
									status_nd_kandidata IN (1) 
									AND 
									pstatus_nd_kandidata IN (1,2,3,4,5,6,7,8,9,10,11,12)
								) 
								OR 
								(
									status_nd_kandidata IN (7)
								)
							)
							AND
							zaduzen_zaposlenik_nd_kandidata = ". $logged_employee_id ."
						ORDER BY 
							pp.datum_pp 
						DESC
						";
			break;
			case 3:
			// case "Prioritet 2":
				return "INNER JOIN
							idk_ponovne_prijave pp
						ON 
							id_broj_nd_kandidata = pp.id_kandidata
							AND
							pp.id_pp = (
								SELECT 
									MAX(ppk.id_pp)
								FROM 
									idk_ponovne_prijave ppk
								WHERE 
									ppk.id_kandidata = id_broj_nd_kandidata
							)
						WHERE 
							status_pp = 1
							AND 
							(
								(
									status_nd_kandidata IN (1) 
									AND 
									pstatus_nd_kandidata IN (1,2,3,4,5,6,7,8,9,10,11,12)
								) 
								OR 
								(
									status_nd_kandidata IN (7)
								)
							)
							AND
							zaduzen_zaposlenik_nd_kandidata = ". $logged_employee_id ."
						ORDER BY 
							pp.datum_pp 
						DESC
						";
			break;
			case 4:
			// case "Termin Zainteresiran":
				$current_time = date('Y-m-d H:i:s');
				return 'INNER JOIN idk_nd_termini termin
						ON idk_nd_kandidata.id_broj_nd_kandidata = termin.termin_kandidat_id
						WHERE zaduzen_zaposlenik_nd_kandidata  = ' .$logged_employee_id . ' 
						AND termin.termin_status = 1 
						AND termin.termin_kandidat_status = 1 
						AND termin.termin_kandidat_pstatus = 9 
						AND termin.termin_vrijeme <= "' .$current_time. '" 
						ORDER BY termin.termin_vrijeme ASC';
			break;
			case 5:
				// case "Termin ostali":
				$current_time = date('Y-m-d H:i:s');
				
				return 'INNER JOIN idk_nd_termini termin
						ON idk_nd_kandidata.id_broj_nd_kandidata = termin.termin_kandidat_id
						WHERE zaduzen_zaposlenik_nd_kandidata  = ' .$logged_employee_id . ' 
						AND termin.termin_status = 1 
						AND termin.termin_kandidat_status = 1 
						AND termin.termin_kandidat_pstatus = 10 
						AND termin.termin_vrijeme <= "' .$current_time. '" 
						ORDER BY termin.termin_vrijeme ASC';
			break;
			case 6:
			// case "NewLead":
				return 'WHERE  zaduzen_zaposlenik_nd_kandidata  = ' .$logged_employee_id . ' AND status_nd_kandidata = 1 AND pstatus_nd_kandidata = 1  ORDER BY vrijeme_kreiranja_nd_kandidata ASC';
			break;
			case 7:
			// case "NL1":
				$current_time = date('Y-m-d H:i:s');
				return 'INNER JOIN idk_nd_termini termin
						ON idk_nd_kandidata.id_broj_nd_kandidata = termin.termin_kandidat_id
						WHERE zaduzen_zaposlenik_nd_kandidata  = ' .$logged_employee_id . ' 
						AND termin.termin_status = 1 
						AND termin.termin_kandidat_status = 1 
						AND termin.termin_kandidat_pstatus = 7 
						AND termin.termin_vrijeme <= "' .$current_time. '" 
						ORDER BY termin.termin_vrijeme ASC';
			break;
			case 8:
			// case "LEADNL":
				return 'WHERE  zaduzen_zaposlenik_nd_kandidata  = ' .$logged_employee_id . ' AND status_nd_kandidata = 1 AND pstatus_nd_kandidata = 11  ORDER BY vrijeme_kreiranja_nd_kandidata ASC';
			break;
			case 9:
			// case "LeadNZ":
				return 'WHERE  zaduzen_zaposlenik_nd_kandidata  = ' .$logged_employee_id . ' AND status_nd_kandidata = 1 AND pstatus_nd_kandidata = 12  ORDER BY vrijeme_kreiranja_nd_kandidata ASC';
			break;
			default: 
				return false;
			break; 
		}
	}
					
					
	function getNextCandidate(){
		global $db;
		
		$korak_algoritma = 1;

		while($korak_algoritma < 10){ // trenutan broj slučajeva za provjeriti 
			
			$main_sql_statement = "SELECT id_broj_nd_kandidata
								   FROM idk_nd_kandidata
								   ";
			$where_clause_redoslijed = generateSqlStatement($korak_algoritma);
			$limit_sql = "LIMIT 1";
			
			if($where_clause_redoslijed !== false){
				$sql = $main_sql_statement . " " . $where_clause_zaposlenik . " " . $where_clause_redoslijed;
				$main_query = $db->prepare($sql);
				$main_query->execute();
				

				if($main_query->rowCount() > 0){ // VRATI PRVI KOJI ODGOVARA QUERY-JU I PREKINI SKRIPTU
					$row = $main_query->fetch();
					
					$kandidat_id = $row['id_broj_nd_kandidata'];
					return $kandidat_id;
				}
			}
			
			$korak_algoritma++;
		}
	}
	*/
	/**
	*	END
	*/
	
//FUNKCIJE UBACENE PRILIKOM PROMJENA U DIPL PRODAJI 17.09.2021 - START

//111Adis222 4 START
function arhivirajAktivneTermineDIPLKandidat($idKandidata){
	Global $db;
	if(isset($idKandidata)){
		//provjerava se da li postoji termina koji su trenutno aktivni za tog kandidata
		$queryProvjeraTermina = $db->prepare("
			SELECT 
				COUNT(termin_id) AS brojTermina
			FROM 
				idk_nd_termini
			WHERE
				termin_kandidat_id = :idKan AND termin_status = :terStatus
		");
		$queryProvjeraTermina->execute(array(
			':idKan' => $idKandidata,
			':terStatus' => 1
		));
		$rowProvjeraTermina = $queryProvjeraTermina->fetch();
		$brojTermina = intval($rowProvjeraTermina["brojTermina"]);
		
		//ako postoji aktivnih termina - njihov status se prebacuje na "0"
		if($brojTermina != 0){
			$queryStatusTermina = $db->prepare("
				UPDATE 
					idk_nd_termini
				SET 
					termin_status = 0
				WHERE 
					termin_kandidat_id = :idKan AND termin_status = :terStatus
			");
			$queryStatusTermina->execute(array(
				':idKan' => $idKandidata,
				':terStatus' => 1
			));
		}
	}
}

function dodajTerminDIPLKandidat($idKandidata, $vrijemeTermina, $komunikacijaId){
	Global $db;
	Global $logged_employee_id;
	//START 1
	//uzima se status pod kojim je kandidat bio u trenutku unosa novog termina
	$queryStatus = $db->prepare("
		SELECT
			status_nd_kandidata, pstatus_nd_kandidata
		FROM 
			idk_nd_kandidata
		WHERE
			id_broj_nd_kandidata = :id
	");
	$queryStatus->execute(array(
		':id' => $idKandidata
	));
	$rowStatus = $queryStatus->fetch();
	$status = $rowStatus["status_nd_kandidata"];
	$podstatus = $rowStatus["pstatus_nd_kandidata"];
	//END 1
	
	arhivirajAktivneTermineDIPLKandidat($idKandidata);
	
	//START 2
	//unos novog termina
	$queryTermin = $db->prepare("
		INSERT INTO idk_nd_termini
		(
			termin_kandidat_id,
			termin_vrijeme,
			termin_kandidat_status,
			termin_kandidat_pstatus,
			termin_status,
			termin_zaposlenik_id,
			termin_biljeska_id
		)
		VALUES
		(
			:termin_kandidat_id,
			:termin_vrijeme,
			:termin_kandidat_status,
			:termin_kandidat_pstatus,
			:termin_status,
			:termin_zaposlenik_id,
			:termin_biljeska_id
		)
	");
	$queryTermin->execute(array(
		':termin_kandidat_id' => $idKandidata,
		':termin_vrijeme' => $vrijemeTermina,
		':termin_kandidat_status' => $status,
		':termin_kandidat_pstatus' => $podstatus,
		':termin_status' => 1, 
		':termin_zaposlenik_id' => $logged_employee_id,
		':termin_biljeska_id' => $komunikacijaId
	));
	//END 2
	
	$zadnjiTermin = $db->lastInsertId();
	
	$log_desc = "DIPL -> Dodan novi termin ID = [".$zadnjiTermin."] za kandidata ID = [".$idKandidata."]";
	$log_date = date("Y-m-d H:i:s");

	$log_query = $db->prepare("
		INSERT INTO idk_logs
			(log_employeeid, log_desc, log_date)
		VALUES
			(:log_employeeid, :log_desc, :log_date)
	");

	$log_query->execute(array(
		':log_employeeid' => $logged_employee_id,
		':log_desc' => $log_desc,
		':log_date' => $log_date
	));
}


function promjenaStatusaDIPLKandidat($idKandidata, $status, $podstatus, $logDaNe){
	Global $db;
	Global $logged_employee_id;
	//$logDaNe -> 1 - Unosi se log, 0 - Ne unosi se log
	$pStatus = 0;
	if($podstatus == 0){
		$pStatus = 1;
	}else{
		$pStatus = $podstatus;
	}
	$queryStariStatus = $db->prepare("
		SELECT 
			id_log_status_nd_kandidata, vrijeme_promjene_statusa_nd_kandidata
		FROM 
			idk_nd_kandidata_status_log
		WHERE 
			idd_broj_nd_kandidata = :idd_broj_nd_kandidata 
			AND 
			broj_dana_statusa_nd_kandidata is null
	");
	$queryStariStatus->execute(array(
		':idd_broj_nd_kandidata' => $idKandidata
	));
	$rowStariStatus = $queryStariStatus->fetch();
	$idStatusLog = $rowStariStatus["id_log_status_nd_kandidata"];
	$vrijemeStatusLog = $rowStariStatus["vrijeme_promjene_statusa_nd_kandidata"];
	
	$diffNovi = strtotime(date("Y-m-d H:i:s")) - strtotime($vrijemeStatusLog);
	$dayNovi = floor($diffNovi/86400);
	
	$updateStariStatus = $db->prepare("
		UPDATE 
			idk_nd_kandidata_status_log
		SET 
			broj_dana_statusa_nd_kandidata = :broj_dana_statusa_nd_kandidata
		WHERE 
			idd_broj_nd_kandidata = :idd_broj_nd_kandidata 
			AND 
			id_log_status_nd_kandidata = :id_log_status_nd_kandidata
	");

	$updateStariStatus->execute(array(
		':idd_broj_nd_kandidata' => $idKandidata,
		':id_log_status_nd_kandidata' => $idStatusLog,
		':broj_dana_statusa_nd_kandidata' => $dayNovi
	));
	
	$insertNoviStatus = $db->prepare("
		INSERT INTO idk_nd_kandidata_status_log
		(
			idd_broj_nd_kandidata,
			status_nd_kandidata,
			pstatus_nd_kandidata,
			vrijeme_promjene_statusa_nd_kandidata,
			promjenio_zaposlenik_nd_kandidata
		)
		VALUES
		(
			:idd_broj_nd_kandidata,
			:status_nd_kandidata,
			:pstatus_nd_kandidata,
			:vrijeme_promjene_statusa_nd_kandidata,
			:promjenio_zaposlenik_nd_kandidata
		)
	");
	$insertNoviStatus->execute(array(
		':idd_broj_nd_kandidata' => $idKandidata,
		':status_nd_kandidata' => $status,
		':pstatus_nd_kandidata' => $pStatus,
		':vrijeme_promjene_statusa_nd_kandidata' => date('Y-m-d H:i:s'),
		':promjenio_zaposlenik_nd_kandidata' => $logged_employee_id
	));
	
	$zadnjiNoviStatus = $db->lastInsertId();
	
	// if($podstatus == 0){
	// 	$updateNoviStatus = $db->prepare("
	// 		UPDATE 
	// 			idk_nd_kandidata
	// 		SET 
	// 			status_nd_kandidata = :status_nd_kandidata
	// 		WHERE 
	// 			id_broj_nd_kandidata = :id_broj_nd_kandidata
	// 	");

	// 	$updateNoviStatus->execute(array(
	// 		':id_broj_nd_kandidata' => $idKandidata,
	// 		':status_nd_kandidata' => $status
	// 	));
	// }else{
		$updateNoviStatus = $db->prepare("
			UPDATE 
				idk_nd_kandidata
			SET 
				status_nd_kandidata = :status_nd_kandidata, 
				pstatus_nd_kandidata = :pstatus_nd_kandidata
			WHERE 
				id_broj_nd_kandidata = :id_broj_nd_kandidata
		");

		$updateNoviStatus->execute(array(
			':id_broj_nd_kandidata' => $idKandidata,
			':status_nd_kandidata' => $status,
			':pstatus_nd_kandidata' => $pStatus
		));

		if ($status == 2 AND $pStatus == 1){
			$query_poslan_spisak_datum = $db->prepare("
				UPDATE 
					idk_nd_kandidata 
				SET 
					poslan_spisak_datum = :poslan_spisak_datum
				WHERE
					id_broj_nd_kandidata = :id_broj_nd_kandidata
			");
			$query_poslan_spisak_datum->execute(array(
				':poslan_spisak_datum' => date("Y-m-d"),
				':id_broj_nd_kandidata' => $idKandidata
			));
		}
		
	// }
	if($logDaNe == 1){
		$log_desc = "DIPL -> Promjena statusa za kandidata ID = [".$idKandidata."]. Log status ID = [".$zadnjiNoviStatus."].";
		$log_date = date("Y-m-d H:i:s");

		$log_query = $db->prepare("
			INSERT INTO idk_logs
				(
					log_employeeid, 
					log_desc, 
					log_date
				)
			VALUES
				(
					:log_employeeid, 
					:log_desc, 
					:log_date
				)
		");

		$log_query->execute(array(
			':log_employeeid' => $logged_employee_id,
			':log_desc' => $log_desc,
			':log_date' => $log_date
		));
	}

	updateProjekcijeKandidat(1, $idKandidata);
}

function getStatusDIPLKandidatR($status, $podstatus){
	Global $db;
	$statusPrikaz = "";
	if($status == 1 AND $podstatus == 1){
		$statusPrikaz = '<span style = "background-color: rgb(131, 144, 152); color: white;" class="label label-default material-label material-label_default main-container__column text-center">Lead</span>';
	}else if($status == 1 AND $podstatus == 6){
		$statusPrikaz = '<span style = "background-color: rgb(0, 250, 251); color: black;" class="label label-default material-label material-label_default main-container__column text-center">Neuspješan kontakt 1</span>';
	}else if($status == 1 AND $podstatus == 2){
		$statusPrikaz = '<span style = "background-color: rgb(0, 251, 83); color: white;" class="label label-default material-label material-label_default main-container__column text-center">Neuspješan kontakt 3 </span>';
	}else if($status == 1 AND $podstatus == 3){
		$statusPrikaz = '<span style = "background-color: rgb(14, 105, 115); color: white;" class="label label-default material-label material-label_default main-container__column text-center">Zainteresiran Lead</span>';
	}else if($status == 1 AND $podstatus == 4){
		$statusPrikaz = '<span style = "background-color: rgb(191, 33, 75); color: white;" class="label label-default material-label material-label_default main-container__column text-center">Nezainteresiran Lead</span>';
	}else if($status == 1 AND $podstatus == 5){
		$statusPrikaz = '<span style = "background-color: rgb(199, 156, 255); color: white; " class="label label-default material-label material-label_default main-container__column text-center">U obradi Lead</span>';
	}else if($status == 1 AND $podstatus == 7){
		$statusPrikaz = '<span style = "background-color: rgb(183, 182,249); color: black; " class="label label-default material-label material-label_default main-container__column text-center">Neuspješan Lead 1</span>';
	}else if($status == 1 AND $podstatus == 8){
		$statusPrikaz = '<span style = "background-color: rgb(207,95,250); color: white; " class="label label-default material-label material-label_default main-container__column text-center">Neuspješan Lead 2</span>';
	}else if($status == 1 AND $podstatus == 9){
		$statusPrikaz = '<span style = "background-color: rgb(117,34,99); color: white;" class="label label-default material-label material-label_default main-container__column text-center">Termin Zainteresiran</span>';
	}else if($status == 1 AND $podstatus == 10){
		$statusPrikaz = '<span style = "background-color: rgb(204,177,122); color: white;" class="label label-default material-label material-label_default main-container__column text-center">Termin Ostali</span>';
	}else if($status == 1 AND $podstatus == 11){
		$statusPrikaz = '<span style = "background-color: rgb(128,123,70); color: white;" class="label label-default material-label material-label_default main-container__column text-center">Lead NL</span>';
	}else if($status == 1 AND $podstatus == 12){
		$statusPrikaz = '<span style = "background-color: rgb(214,76,10); color: white;" class="label label-default material-label material-label_default main-container__column text-center">Lead NZ</span>';
	}else if($status == 2 AND $podstatus == 1){
		$statusPrikaz = '<span style = "background-color: rgb(242, 228, 46); color:black; " class="label label-default material-label material-label_default main-container__column text-center">Prikupljanje dokumentacije</span>';
	}else if($status == 2 AND $podstatus == 2){
		$statusPrikaz = '<span style = "background-color: rgb(176, 167, 55); color:black; " class="label label-default material-label material-label_default main-container__column text-center">Nepotpuna dokumentacija</span>';
	}else if($status == 2 AND $podstatus == 3){
		$statusPrikaz = '<span style = "background-color: rgb(242, 201, 46); color:black; " class="label label-default material-label material-label_default main-container__column text-center">Na prevodu</span>';
	}else if($status == 2 AND $podstatus == 4){
		$statusPrikaz = '<span style = "background-color: rgb(46, 242, 128); color:black; " class="label label-default material-label material-label_default main-container__column text-center">Dokumentacija kompletirana</span>';
	}else if($status == 2 AND $podstatus == 5){
		$statusPrikaz = '<span style = "background-color: rgb(183, 211, 36); color:black; " class="label label-default material-label material-label_default main-container__column text-center">Prevod završen</span>';
	}else if($status == 2 AND $podstatus == 6){
		$statusPrikaz = '<span style = "background-color: rgb(46, 175, 242); color:black; " class="label label-default material-label material-label_default main-container__column text-center">Poslan zahtjev</span>';
	}else if($status == 2 AND $podstatus == 7){
		$statusPrikaz = '<span style = "background-color: rgb(203, 242, 46); color:black; " class="label label-default material-label material-label_default main-container__column text-center">Potpisan zahtjev</span>';
	}else if($status == 3 AND $podstatus == 1){
		$statusPrikaz = '<span style = "background-color: rgb(64, 146, 217); color: white; " class="label label-default material-label material-label_default main-container__column text-center">Poslana pošta</span>';
	}else if($status == 3 AND $podstatus == 2){
		$statusPrikaz = '<span style = "background-color: rgb(64, 217, 212); color: white; " class="label label-default material-label material-label_default main-container__column text-center">Zaprimili dokumentaciju</span>';
	}else if($status == 4 AND $podstatus == 1){
		$statusPrikaz = '<span style = "background-color: rgb(139, 218, 242); color: black; " class="label label-default material-label material-label_default main-container__column text-center">U obradi</span>';
	}else if($status == 4 AND $podstatus == 2){
		$statusPrikaz = '<span style = "background-color: rgb(242, 139, 139); color: black; " class="label label-default material-label material-label_default main-container__column text-center">Stigla taksa / Dopuna</span>';
	}else if($status == 5){
		$statusPrikaz = '<span style = "background-color: rgb(242, 161, 46); color: white;" class="label label-default material-label material-label_default main-container__column text-center">Plaćena taksa / Poslana dopuna</span>';
	}else if($status == 6){
		$statusPrikaz = '<span style = "background-color: rgb(104, 195, 104); color: white;" class="label label-default material-label material-label_default main-container__column text-center">Završen</span>';
	}else if($status == 7){
		$statusPrikaz = '<span style = "background-color: rgb(243, 65, 60); color: white; " class="label label-default material-label material-label_default main-container__column text-center">Arhiv</span>';
	}else{
		$statusPrikaz = '<span style = "background-color: rgb(0,0,0); color: white;" class="label label-default material-label material-label_default main-container__column text-center">Nije definisano</span>';
	}
	
	return $statusPrikaz;
}

function getBrojTerminaDIPLKandidatR($idKandidat, $status, $podstatus, $aktivnost){
	Global $db;
	Global $logged_employee_id; 
	
	$queryTermini = $db->prepare("
		SELECT 
			count(termin_id) AS brojTermina
		FROM 
			idk_nd_termini
		WHERE 
			termin_kandidat_id = :kanID 
			AND 
			termin_kandidat_status = :kanStat
			AND 
			termin_kandidat_pstatus = :kanPstat
			AND 
			termin_status = :terAkt
	");
	$queryTermini->execute(array(
		':kanID' => $idKandidat,
		':kanStat' => $status,
		':kanPstat' => $podstatus,
		':terAkt' => $aktivnost
	));
	
	$rowTermini = $queryTermini->fetch();
	
	$brojTermina = intval($rowTermini["brojTermina"]);
	
	return $brojTermina;
}

function promjenaAgentaProdajeDiplKandidata($idKandidat, $idAgent, $logDaNe){
	Global $db;
	Global $logged_employee_id;
	
	//$logDaNe -> 1 - Unosi se log, 0 - Ne unosi se log
	$stariZaduzeni = 0;
	$teamAgenta = 0;
	$idSkladista = 139;
	if(isset($idKandidat) AND isset($idAgent)){
		$queryStari = $db->prepare("
			SELECT 
				zaduzen_zaposlenik_nd_kandidata, tim_nd_kandidata
			FROM 
				idk_nd_kandidata
			WHERE 
				id_broj_nd_kandidata = :id_broj_nd_kandidata
		");
		$queryStari->execute(array(
			':id_broj_nd_kandidata' => $idKandidat
		));
		$brojStari = $queryStari->rowCount();
		if($brojStari == 1){
			$rowStari = $queryStari->fetch();
			$stariZaduzeni = $rowStari["zaduzen_zaposlenik_nd_kandidata"];
			$timZaduzeni = $rowStari["tim_nd_kandidata"];
			
			if($stariZaduzeni != $idAgent){
				//Team Novog menadzera START
				if($idAgent != $idSkladista){
					$teamAgenta = getTeamIdByEmployee($idAgent);
				}else{
					$teamAgenta = $timZaduzeni;
				}
				//Provjera da li team agenta aktivan START
				$queryTeam = $db->prepare("
					SELECT
						status_t
					FROM 
						idk_timovi
					WHERE 
						id_t = :id_t
				");
				$queryTeam->execute(array(
					':id_t' => $teamAgenta
				));
				$rowTeam = $queryTeam->fetch();
				$statusTeam = intval($rowTeam["status_t"]);
				if($statusTeam == 0){
					$teamAgenta = 1;
				}
				//Provjera da li team agenta aktivan END
				//Team Novog menadzera END
				
				//Unos novog menadzera START
				$queryNovi = $db->prepare("
					UPDATE 
						idk_nd_kandidata
					SET 
						zaduzen_zaposlenik_nd_kandidata = :zaduzen_zaposlenik_nd_kandidata, 
						tim_nd_kandidata = :tim_nd_kandidata
					WHERE 
						id_broj_nd_kandidata = :id_broj_nd_kandidata
				");

				$queryNovi->execute(array(
					':id_broj_nd_kandidata' => $idKandidat,
					':zaduzen_zaposlenik_nd_kandidata' => $idAgent,
					':tim_nd_kandidata' => $teamAgenta
				));
				//Unos novog menadzera END
				
				//Update statistike menadzera START
				$insertNovi = $db->prepare("
					INSERT INTO idk_nd_menadzeri_statistike
						(
							idd_broj_nd_kandidata, 
							zaduzen_zaposlenik_id, 
							prethodni_zaposlenik_id, 
							vrsta_aktivnosti, 
							vrijeme_aktivnosti
						)
					VALUES
						(
							:idd_broj_nd_kandidata, 
							:zaduzen_zaposlenik_id, 
							:prethodni_zaposlenik_id, 
							:vrsta_aktivnosti, 
							:vrijeme_aktivnosti
						)
				");

				$insertNovi->execute(array(
					':idd_broj_nd_kandidata' => $idKandidat,
					':zaduzen_zaposlenik_id' => $idAgent,
					':prethodni_zaposlenik_id' => $stariZaduzeni,
					':vrsta_aktivnosti' => 1,
					':vrijeme_aktivnosti' => date('Y-m-d H:i:s')
				));
				
				if($logDaNe == 1){
					$log_desc = "DIPL -> Izvrsena promjena agenta kod kandidata ID = [".$idKandidat."] sa zaposlenika ID = [".$stariZaduzeni."] na ID = [".$idAgent."].";
					$log_date = date("Y-m-d H:i:s");

					$log_query = $db->prepare("
						INSERT INTO idk_logs
							(
								log_employeeid, 
								log_desc, 
								log_date
							)
						VALUES
							(
								:log_employeeid, 
								:log_desc, 
								:log_date
							)
					");

					$log_query->execute(array(
						':log_employeeid' => $logged_employee_id,
						':log_desc' => $log_desc,
						':log_date' => $log_date
					));
				}
			}
			
		}
	}
}

function omoguciTipoveKomunikacijaDIPLKandidatR($idKandidata){
	Global $db;
	Global $logged_employee_id;
	//Tipovi koji se mogu pojaviti unutar funkcije: 11,12,13,14,15,16,17
	//Znacenje tipova je:
	// (11) - Neuspješna komunikacija 
	// (12) - Termin zainteresiran 
	// (13) - Nije zainteresiran
	// (14) - Termin ostali
	// (15) - Direkt u arhivu
	// (16) - Poslan pravi ugovor i predračun
	// (17) - Poslan pravi ugovor i predračun - nisu dopunjene informacije određene
	$omogucenTip = array(); //Niz u koji ce se prema uslovima smjestiti određene vrijednosti
	
	if(isset($idKandidata)){
		$kandidatInfoQuery = $db->prepare("
			SELECT
				status_nd_kandidata, 
				pstatus_nd_kandidata, 
				skola_nd_kandidata,
				skola_smjer_nd_kandidata
			FROM 
				idk_nd_kandidata
			WHERE 
				id_broj_nd_kandidata = :id_broj_nd_kandidata
		");
		$kandidatInfoQuery->execute(array(
			':id_broj_nd_kandidata' => $idKandidata
		));
		$kandidatProvjera = $kandidatInfoQuery->rowCount();
		if($kandidatProvjera != 0){
			$kandidatInfoRow = $kandidatInfoQuery->fetch();
			$statusKan = intval($kandidatInfoRow["status_nd_kandidata"]);
			$pstatusKan = intval($kandidatInfoRow["pstatus_nd_kandidata"]);
			$skolaKan = intval($kandidatInfoRow["skola_nd_kandidata"]);
			$smjerKan = intval($kandidatInfoRow["skola_smjer_nd_kandidata"]);
			
			//Samo u slucajevima kada je status kandidata razlicit od statusa "U obradi Lead" - vrsi se provjera da li su dopunjene informacije i omogucuje se tip komunikacije 16 ili 17 
			if($statusKan == 1 AND $pstatusKan != 5){
				array_push($omogucenTip, 16);
			}
			
			//Za sve ostale statuse u kojima je kandidat - omogucuje se odredjeni tip
			if($statusKan == 1 AND $pstatusKan == 1){
				array_push($omogucenTip, 11,12,13,14,15,17);
			}else if($statusKan == 1 AND $pstatusKan == 6){
				array_push($omogucenTip, 11,12,13,14,15,17);
			}else if($statusKan == 1 AND $pstatusKan == 2){
				array_push($omogucenTip, 11,12,13,14,15,17);
			}else if($statusKan == 1 AND $pstatusKan == 3){
				array_push($omogucenTip, 12,13,14,15,17);
			}else if($statusKan == 1 AND $pstatusKan == 4){
				array_push($omogucenTip, 12,14,15,17);
			}else if($statusKan == 1 AND $pstatusKan == 5){
				array_push($omogucenTip, 1,13,15,17);
			}else if($statusKan == 1 AND $pstatusKan == 7){
				array_push($omogucenTip, 11,12,13,14,15,17);
			}else if($statusKan == 1 AND $pstatusKan == 8){
				array_push($omogucenTip, 12,13,14,15,17);
			}else if($statusKan == 1 AND $pstatusKan == 9){
				array_push($omogucenTip, 12,13,14,15,17);
			}else if($statusKan == 1 AND $pstatusKan == 10){
				array_push($omogucenTip, 12,13,14,15,17);
			}else if($statusKan == 1 AND $pstatusKan == 11){
				array_push($omogucenTip, 11,12,13,14,15,17);
			}else if($statusKan == 1 AND $pstatusKan == 12){
				array_push($omogucenTip, 12,13,14,15,17);
			}else{
				array_push($omogucenTip, 0);
			}
			
		}else{
			array_push($omogucenTip, 0);
		}
	}else{
		array_push($omogucenTip, 0);
	}
	
	return implode(",", $omogucenTip);
}

function getStatusValueDIPLKandidatR($idKandidat){
	Global $db;
	$statusKandidata = array();
	if(isset($idKandidat)){
		$queryStatus = $db->prepare("
			SELECT
				status_nd_kandidata, 
				pstatus_nd_kandidata
			FROM 
				idk_nd_kandidata
			WHERE 
				id_broj_nd_kandidata = :id_broj_nd_kandidata
		");
		$queryStatus->execute(array(
			':id_broj_nd_kandidata' => $idKandidat
		));
		$infoProvjera = $queryStatus->rowCount();
		if($infoProvjera != 0){
			$rowStatus = $queryStatus->fetch();
			$statusKandidat = intval($rowStatus["status_nd_kandidata"]);
			$pstatusKandidat = intval($rowStatus["pstatus_nd_kandidata"]);
			$statusKandidata["status"] = $statusKandidat;
			$statusKandidata["podstatus"] = $pstatusKandidat;
			
		}else{
			$statusKandidata["status"] = 0;
			$statusKandidata["podstatus"] = 0;
		}
	}else{
		$statusKandidata["status"] = 0;
		$statusKandidata["podstatus"] = 0;
	}
	
	return $statusKandidata;
}
//111Adis222 4 END

// SAMO SA ZMAJU <3 <3 <3 START 
function getProdajeUplatePerioda($datum_od, $datum_do, $zaposlenik_id){
	Global $db;
	$query_get_prodaje_uplate_perioda = $db->prepare("
		SELECT 	SUM(
					CASE
						WHEN 1
						THEN 1
						ELSE 1
					END
				) as prodano,
				SUM(
					CASE
						WHEN (
							pr_uplaceno = 1
						)
						THEN 1
						ELSE 0
					END
				) as uplaceno
		FROM idk_predracuni
		WHERE
			pr_rata = 1 
		AND pr_status is not NULL 
		AND pr_vrsta_predracuna = 1
		AND pr_zaposlenik = :zaposlenik_id
		AND pr_datum_kreiranja BETWEEN '$datum_od' AND '$datum_do'
		AND pr_id IN(
			SELECT MAX(pr.pr_id) 
			FROM idk_predracuni pr 
			WHERE pr.pr_rata = 1 
			GROUP BY pr.pr_kandidat_id
		)
	");

	$query_get_prodaje_uplate_perioda -> execute(array(':zaposlenik_id' => $zaposlenik_id));
	$row_get_prodaje_uplate_perioda = $query_get_prodaje_uplate_perioda -> fetch();
	
	$prodano   = $row_get_prodaje_uplate_perioda['prodano'];
	$uplaceno = $row_get_prodaje_uplate_perioda['uplaceno'];
	$procenat = 0;
	if(!is_null($prodano)){
		$procenat = round (($uplaceno * 100)/$prodano);
	}
	$return_array = [
		'prodano' => $prodano, 
		'uplaceno' => $uplaceno, 
		'procenat' => $procenat
	];
	
	return $return_array;
}

function getUplateProvizije($zaposlenik_id){

	$trenutni_mjesec = date("Ym");
	
	Global $db;
	$query_get_uplate_trenutni_mjesec_prosli_mjeseci = $db->prepare("
		SELECT
			SUM(
				CASE 
					WHEN EXTRACT(YEAR_MONTH FROM pr_datum_uplate) = $trenutni_mjesec
					THEN 1
					ELSE 0
				END
			) as trenutni_mjesec,
			SUM(
				CASE
					WHEN EXTRACT(YEAR_MONTH FROM pr_datum_uplate) = $trenutni_mjesec AND  EXTRACT(YEAR_MONTH FROM pr_datum_kreiranja) < $trenutni_mjesec
					THEN 1
					ELSE 0
				END
			) as prosli_mjeseci
		FROM idk_predracuni
		WHERE
			pr_rata = 1 
		AND pr_status is not NULL 
		AND pr_vrsta_predracuna = 1
		AND pr_uplaceno = 1
		AND pr_zaposlenik = :zaposlenik_id
		AND pr_id IN(
			SELECT MAX(pr.pr_id) 
			FROM idk_predracuni pr 
			WHERE pr.pr_rata = 1 
			GROUP BY pr.pr_kandidat_id
		)
	");

	$query_get_uplate_trenutni_mjesec_prosli_mjeseci->execute(array(':zaposlenik_id' => $zaposlenik_id));
	
	$row_get_uplate_trenutni_mjesec_prosli_mjesec = $query_get_uplate_trenutni_mjesec_prosli_mjeseci->fetch();
	
	$return_array = [
		'trenutni_mjesec' => $row_get_uplate_trenutni_mjesec_prosli_mjesec['trenutni_mjesec'],
		'prosli_mjeseci'  => $row_get_uplate_trenutni_mjesec_prosli_mjesec['prosli_mjeseci']
	];
	
	return $return_array;
}

function getProvizijaAgenta($agent_id){
	Global $db;
	
	$f_from_f = date('Y-m-01 00:00:00');
	$f_to_f = date('Y-m-t 23:59:59');
	$query = $db->prepare("SELECT e.employee_poslovnica,
								SUM(CASE WHEN o.status_predracuna = 1 AND o.status_obracuna = 0 AND o.vrijeme_uplate BETWEEN '$f_from_f' AND '$f_to_f' THEN o.iznos_obracuna_bam ELSE NULL END) as suma_bam,
								SUM(CASE WHEN o.status_predracuna = 1 AND o.status_obracuna = 0 AND o.vrijeme_uplate BETWEEN '$f_from_f' AND '$f_to_f' THEN o.iznos_obracuna_rsd ELSE NULL END) as suma_rsd
							FROM idk_obracuni o
							JOIN idk_employees e ON e.employee_id = o.employee_id
							WHERE o.employee_id = :employee_id");
	$query->execute(array(
				'employee_id' => $agent_id
	));
	$row_prov = $query->fetch();
	$employee_poslovnica = $row_prov['employee_poslovnica'];
	$suma_bam = $row_prov['suma_bam'];
	$suma_rsd = $row_prov['suma_rsd'];
	$drzava = getDrzavaPoslovnice($employee_poslovnica);
	if($drzava == "Srbija"){
		$suma = number_format($suma_rsd, 2, ',', '');
		$suma_f = $suma." RSD";
	}else{
		$suma = number_format($suma_bam, 2, ',', '');
		$suma_f = $suma." KM";
	}
	return $suma_f;
}

function getDrzavaPoslovnice($poslovnica_id){
	Global $db;
	$query = $db->prepare("SELECT branch_state FROM idk_poslovnice WHERE branch_id = :branch_id");
	$query->execute(array('branch_id' => $poslovnica_id));
	$row = $query->fetch();
	
	return $row['branch_state'];
}
// SAMO SA ZMAJU <3 <3 <3 END

/**
*
*	FUNKCIJA GENERISANJA SLJEDEĆEG KANDIDATA ZA POZIV
*	FUNKCIJA GENERISANJA SQL IZRAZA ZA POTREBNE UPITE
* 	@function getNextCandidate();
*	@function generateSqlStatement(int $case);
*/

function generateSqlStatement($case){
	global $logged_employee_id;
	
	switch($case){
		// case "inbound poziv":
		case 1:
			return "WHERE status_nd_kandidata = 1 AND inbound_aktivan = 1 AND zaduzen_zaposlenik_nd_kandidata = ".$logged_employee_id." ORDER BY zadnja_komunikacija ASC ";
		break;
		// case "FB/IG/Inbound Lead":
		case 2:
			return "WHERE (kampanja_id = 100 OR kampanja_id = 157) AND status_nd_kandidata = 1 AND pstatus_nd_kandidata = 1 AND zaduzen_zaposlenik_nd_kandidata  = ".$logged_employee_id . " ORDER BY vrijeme_kreiranja_nd_kandidata ASC";
		break;
		// case "Prioritet 1":
		case 3:
			return "INNER JOIN
						idk_ponovne_prijave pp
					ON 
						id_broj_nd_kandidata = pp.id_kandidata
						AND
						pp.id_pp = (
							SELECT 
								MAX(ppk.id_pp)
							FROM 
								idk_ponovne_prijave ppk
							WHERE 
								ppk.id_kandidata = id_broj_nd_kandidata
						)
					WHERE 
						status_pp = 1
						AND 
						(
							(
								status_nd_kandidata IN (1) 
								AND 
								pstatus_nd_kandidata IN (1,2,3,4,5,6,7,8,9,10,11,12)
							) 
							OR 
							(
								status_nd_kandidata IN (7)
							)
						)
						AND
						zaduzen_zaposlenik_nd_kandidata = ". $logged_employee_id ."
					ORDER BY 
						pp.datum_pp 
					DESC
					";
		break;
		case 4:
		// case "Prioritet 2":
			return "INNER JOIN
						idk_ponovne_prijave pp
					ON 
						id_broj_nd_kandidata = pp.id_kandidata
						AND
						pp.id_pp = (
							SELECT 
								MAX(ppk.id_pp)
							FROM 
								idk_ponovne_prijave ppk
							WHERE 
								ppk.id_kandidata = id_broj_nd_kandidata
						)
					WHERE 
						status_pp = 2
						AND 
						(
							(
								status_nd_kandidata IN (1) 
								AND 
								pstatus_nd_kandidata IN (1,2,3,4,5,6,7,8,9,10,11,12)
							) 
							OR 
							(
								status_nd_kandidata IN (7)
							)
						)
						AND
						zaduzen_zaposlenik_nd_kandidata = ". $logged_employee_id ."
					ORDER BY 
						pp.datum_pp 
					DESC
					";
		break;
		case 5:
			return 'WHERE  zaduzen_zaposlenik_nd_kandidata  = ' .$logged_employee_id . ' AND status_nd_kandidata = 1 AND pstatus_nd_kandidata = 1 AND povijest_nd_kandidata = 3 AND povijest_vrsta_nd_kandidata = 1 ORDER BY vrijeme_kreiranja_nd_kandidata ASC'; 
		
		break;
		case 6:
		// case "Termin Zainteresiran":
			$current_time = date('Y-m-d H:i:s');
			return 'INNER JOIN idk_nd_termini termin
					ON idk_nd_kandidata.id_broj_nd_kandidata = termin.termin_kandidat_id
					WHERE zaduzen_zaposlenik_nd_kandidata  = ' .$logged_employee_id . ' 
					AND termin.termin_status = 1 
					AND termin.termin_kandidat_status = 1 
					AND termin.termin_kandidat_pstatus = 9 
					AND termin.termin_vrijeme <= "' .$current_time. '" 
					ORDER BY termin.termin_vrijeme ASC';
		break;
		case 7:
			// case STARI STATUS - ZAINTERESIRAN LEAD 
			return 'WHERE  zaduzen_zaposlenik_nd_kandidata  = ' .$logged_employee_id . ' AND status_nd_kandidata = 1 AND pstatus_nd_kandidata = 3  ORDER BY vrijeme_kreiranja_nd_kandidata ASC';
		break;
		case 8:
		// case "NewLead":
			return 'WHERE  zaduzen_zaposlenik_nd_kandidata  = ' .$logged_employee_id . ' AND status_nd_kandidata = 1 AND pstatus_nd_kandidata = 1  ORDER BY vrijeme_kreiranja_nd_kandidata ASC';
		break;
		case 9:
			// case stari status - NEUSPJEŠAN KONTAKT 1
			return 'WHERE  zaduzen_zaposlenik_nd_kandidata  = ' .$logged_employee_id . ' AND status_nd_kandidata = 1 AND pstatus_nd_kandidata = 6  ORDER BY vrijeme_kreiranja_nd_kandidata ASC';
		break;
		case 10:
			// case stari status - NEUSPJEŠAN KONTAKT 3
			return 'WHERE  zaduzen_zaposlenik_nd_kandidata  = ' .$logged_employee_id . ' AND status_nd_kandidata = 1 AND pstatus_nd_kandidata = 2  ORDER BY vrijeme_kreiranja_nd_kandidata ASC';
		break;
		case 11:
		// case "NL1":
			$current_time = date('Y-m-d H:i:s');
			return 'INNER JOIN idk_nd_termini termin
					ON idk_nd_kandidata.id_broj_nd_kandidata = termin.termin_kandidat_id
					WHERE zaduzen_zaposlenik_nd_kandidata  = ' .$logged_employee_id . ' 
					AND termin.termin_status = 1 
					AND termin.termin_kandidat_status = 1 
					AND termin.termin_kandidat_pstatus = 7 
					AND termin.termin_vrijeme <= "' .$current_time. '" 
					ORDER BY termin.termin_vrijeme ASC';
		break;
		case 12:
			// case "Termin ostali":
			$current_time = date('Y-m-d H:i:s');
			
			return 'INNER JOIN idk_nd_termini termin
					ON idk_nd_kandidata.id_broj_nd_kandidata = termin.termin_kandidat_id
					WHERE zaduzen_zaposlenik_nd_kandidata  = ' .$logged_employee_id . ' 
					AND termin.termin_status = 1 
					AND termin.termin_kandidat_status = 1 
					AND termin.termin_kandidat_pstatus = 10 
					AND termin.termin_vrijeme <= "' .$current_time. '" 
					ORDER BY termin.termin_vrijeme ASC';
		break;
		case 13:
		// case "LEADNL" SA TERMINOM:
			$current_time = date('Y-m-d H:i:s');
			return 'INNER JOIN idk_nd_termini termin
					ON idk_nd_kandidata.id_broj_nd_kandidata = termin.termin_kandidat_id
					WHERE zaduzen_zaposlenik_nd_kandidata  = ' .$logged_employee_id . ' 
					AND termin.termin_status = 1 
					AND termin.termin_kandidat_status = 1 
					AND termin.termin_kandidat_pstatus = 11
					AND termin.termin_vrijeme <= "' .$current_time. '" 
					ORDER BY termin.termin_vrijeme ASC';
		break;
		case 14:
		// case "LEADNL":
				return 
				'INNER JOIN idk_nd_termini termin ON idk_nd_kandidata.id_broj_nd_kandidata = termin.termin_kandidat_id
				WHERE zaduzen_zaposlenik_nd_kandidata  = ' .$logged_employee_id . ' AND status_nd_kandidata = 1 AND pstatus_nd_kandidata = 11
				AND (termin.termin_kandidat_id NOT IN 
					 (
					  SELECT termin_kandidat_id 
					  FROM idk_nd_termini 
					  INNER JOIN idk_nd_kandidata as kandidat ON idk_nd_termini.termin_kandidat_id = kandidat.id_broj_nd_kandidata 	  
					  WHERE zaduzen_zaposlenik_nd_kandidata  = ' .$logged_employee_id . ' 
					  AND kandidat.status_nd_kandidata = 1
					  AND kandidat.pstatus_nd_kandidata = 11 
					  AND termin_status = 1
					 )
					 
				OR termin.termin_kandidat_id IS NULL)
				GROUP BY id_broj_nd_kandidata
				ORDER BY vrijeme_kreiranja_nd_kandidata ASC';
		break;
		case 15:
		// case "LeadNZ":
			return 'WHERE  zaduzen_zaposlenik_nd_kandidata  = ' .$logged_employee_id . ' AND status_nd_kandidata = 1 AND pstatus_nd_kandidata = 12  ORDER BY vrijeme_kreiranja_nd_kandidata ASC';
		break;
		default: 
			return false;
		break; 
	}
}
				
				
function getNextCandidate(){
	global $db;
	
	$korak_algoritma = 1;

	while($korak_algoritma < 16){ // trenutan broj slučajeva za provjeriti 
		
		$main_sql_statement = "SELECT id_broj_nd_kandidata
							   FROM idk_nd_kandidata
							   ";
		$where_clause_redoslijed = generateSqlStatement($korak_algoritma);
		$limit_sql = "LIMIT 1";
		
		if($where_clause_redoslijed !== false){
			$sql = $main_sql_statement . " " . $where_clause_redoslijed . " ". $limit_sql;
			// echo '<hr>'.$sql.'<hr>';
			$main_query = $db->prepare($sql);
			$main_query->execute();
			

			if($main_query->rowCount() > 0){ // VRATI PRVI KOJI ODGOVARA QUERY-JU I PREKINI SKRIPTU
				$row = $main_query->fetch();
				// echo $korak_algoritma;
				$kandidat_id = $row['id_broj_nd_kandidata'];
				return $kandidat_id;
			}
		}
		
		$korak_algoritma++;
	}
}

/**
*	END
*/

function insertNalogFinancijeZaNostrifikacijuMjesecnoR($nalogId) {
	Global $db;

	/*
		Result: 
			100 -> neispravan nalog id
			101 -> nije pronadjen nalog u bazi
			102 -> nije postavljeno da poslodavac placa nostrifikaciju i nije nacin nostrifikacije mjesecno
			103 -> desio se problem kod inserta neke od rata
			1	-> izvrsen insert
	*/

	$nalogId = intval($nalogId); 

	if ( $nalogId != 0 ) { 

		$queryCheck = $db->prepare("
			SELECT 
				nalog_placa_nostrifikaciju, nalog_nacin_nostrifikacije, nalog_provizija_nostrifikacija, nalog_broj_rata_nostrifikacija, nalog_datum_potpisa_naloga, nalog_potrebno_kandidata
			FROM 
				idk_nalozi 
			WHERE 
				nalog_id = :nalog_id
		");
		$queryCheck->execute(array(
			':nalog_id' => $nalogId
		));

		if ( $queryCheck->rowCount() == 1 ) {
			
			$rowCheck = $queryCheck->fetch();

			$nalog_placa_nostrifikaciju 		= intval($rowCheck["nalog_placa_nostrifikaciju"]);
			$nalog_nacin_nostrifikacije 		= intval($rowCheck["nalog_nacin_nostrifikacije"]);
			$nalog_provizija_nostrifikacija 	= $rowCheck["nalog_provizija_nostrifikacija"];
			$nalog_broj_rata_nostrifikacija 	= intval($rowCheck["nalog_broj_rata_nostrifikacija"]);
			$nalog_datum_potpisa_naloga			= $rowCheck["nalog_datum_potpisa_naloga"];
			$nalog_potrebno_kandidata			= intval($rowCheck["nalog_potrebno_kandidata"]);

			if ( $nalog_placa_nostrifikaciju == 1 AND $nalog_nacin_nostrifikacije == 2 ) {

				$rate_insert_ids_explode 	= array();
				$rate_insert_ids_implode 	= "";

				/*
					Na osnovu formule - određuje se cijena rate
				*/

				$iznos_rate = ( $nalog_provizija_nostrifikacija * $nalog_potrebno_kandidata ) / $nalog_broj_rata_nostrifikacija;
				$datum_aktivacije_od_i = date("Y-m-01", strtotime($nalog_datum_potpisa_naloga));

				for ( $i = 1; $i <= $nalog_broj_rata_nostrifikacija; $i++ ) {

					if ( $i > 1 ) {
						$middle_day_of_the_month = date("Y-m-15", strtotime($datum_aktivacije_od_i));
						$datum_aktivacije_od_i = date("Y-m-01", strtotime($middle_day_of_the_month. " +1 month"));
					}

					$rate_insert_id = 0;

					$queryRate = $db->prepare("
						INSERT INTO idk_nalog_financije
						(
							nf_nalog_id, 
							nf_datum_aktiviranja, 
							nf_type, 
							nf_broj_rate, 
							nf_iznos, 
							nf_placeno, 
							nf_datum_fakturisanja, 
							nf_datum_placanja
						)
						VALUES 
						(
							:nf_nalog_id,
							:nf_datum_aktiviranja,
							:nf_type,
							:nf_broj_rate, 
							:nf_iznos, 
							:nf_placeno,
							:nf_datum_fakturisanja,
							:nf_datum_placanja
						)
					");
					$queryRate->execute(array(
						':nf_nalog_id' => $nalogId, 
						':nf_datum_aktiviranja' => $datum_aktivacije_od_i, 
						':nf_type' => 4, 
						':nf_broj_rate' => $i, 
						':nf_iznos' => $iznos_rate, 
						':nf_placeno' => 1, 
						':nf_datum_fakturisanja' => null, 
						':nf_datum_placanja' => null, 
					));

					$rate_insert_id = $db->lastInsertId();
					array_push($rate_insert_ids_explode, $rate_insert_id);

				}

				$rate_insert_ids_implode = implode(",", $rate_insert_ids_explode);

				if ( in_array(0, $rate_insert_ids_explode) ) {

					$log_desc = "NALOG FINANCIJE - RATE NOSTRIFIKACIJE - Problem kod dodavanja rata u tabelu idk_nalog_financije. Rate IDs = [".$rate_insert_ids_implode."]. Slučaj 103.";
					addToLogs($log_desc, 4);
					return 103;

				} else {

					$log_desc = "NALOG FINANCIJE - RATE NOSTRIFIKACIJE - Dodane rate u tabelu idk_nalog_financije. Rate IDs = [".$rate_insert_ids_implode."].";
					addToLogs($log_desc, 4);
					return 1;

				}

			} else {

				return 102;

			}

		} else {
			
			return 101;

		}

	} else {

		return 100; 

	}
}

function updateFakturaNostrifikacijePoslanaPosta($diplId) {
	Global $db; 

	$diplId = intval($diplId); 

	if ($diplId != 0){

		/*
			Prvo je potrebno doći do id kandidata - provjeriti vezu izmedju kandidata i dipl kandidata START
			*/
				$queryCheck = $db->prepare("
					SELECT 
						kan.kandidat_id 
					FROM 
						idk_kandidati kan 
					INNER JOIN 
						idk_nd_kandidata dipl 
					ON 
						kan.kandidat_dipl_id = dipl.id_broj_nd_kandidata 
					WHERE 
						dipl.id_broj_nd_kandidata = :diplId
				");
				$queryCheck->execute(array(
					':diplId' => $diplId
				));

				if ( $queryCheck->rowCount() == 1 ) {
					
					$rowCheck = $queryCheck->fetch();

					$candidateId = intval($rowCheck["kandidat_id"]); 

					if ( $candidateId != 0 ) {

						/*
							Kada je pronadjen kandidat posao id - onda je potrebno provjeriti da li kandidat ima unos u idk_kandidat_financije
							*/

								$queryCheckKandidatFinancije = $db->prepare("
									SELECT 
										kf.kf_id 
									FROM  
										idk_kandidat_financije kf 
									INNER JOIN 
										idk_kandidati kan
									ON 
										kf.kandidat_id = kan.kandidat_id 
										AND 
										kf.nalog_id = kan.kandidat_nalog_id
									WHERE 
										kan.kandidat_id = :candidateId
										AND 
										kan.kandidat_status_prijave > 8
										AND 
										kf.kf_type = 0
										AND 
										kf.kf_placeno = 0
								");
								$queryCheckKandidatFinancije->execute(array(
									':candidateId' => $candidateId
								)); 

								if ( $queryCheckKandidatFinancije->rowCount() == 1 ) {

									$rowCheckKandidatFinancije = $queryCheckKandidatFinancije->fetch();

									$candidateFinancijeId = intval($rowCheckKandidatFinancije["kf_id"]); 

									if ( $candidateFinancijeId != 0 ) {

										$updateFinancije = $db->prepare("
											UPDATE 
												idk_kandidat_financije
											SET 
												kf_placeno = 1, 
												kf_datum = CURRENT_DATE(),
												kf_datum_stvarni = CURRENT_DATE()
											WHERE 
												kf_id = :kf_id
										");
										$updateFinancije->execute(array(
											':kf_id' => $candidateFinancijeId
										));

										$log_desc = "NALOG FINANCIJE - UPDATE FAKTURE ZA NOSTRIFIKACIJU - Izvršen update fakture za nostrifikaciju na status TREBA FAKTURISATI. Kandidat faktura ID = [".$candidateFinancijeId."]. Kandidat ID = [".$candidateId."]. Kandidat DIPL ID = [".$diplId."]";
										addToLogs($log_desc, 4);

									}

								} 

							/*
							Kada je pronadjen kandidat posao id - onda je potrebno provjeriti da li kandidat ima unos u idk_kandidat_financije
						*/

					}
				}

			/* 
			Prvo je potrebno doći do id kandidata - provjeriti vezu izmedju kandidata i dipl kandidata END 
		*/

	}

}

function insertNalogFinancijeZaMjesecnoPlacanjeR($nalogId) {
	Global $db; 

	/*
		Result: 
			100 -> neispravan nalog id
			101 -> nije pronadjen nalog u bazi
			102 -> kod naloga nije postavljen mjesecni nacin placanja
			103 -> nije izvrsen insert neke od rata
			104 -> izvrsen insert avanta ali nije izvrsen insert neke od rata
			105 -> nije izvrsen insert avansa i nije izvrsen isert neke od rata
			106 -> izvrsen insert rata ali nije izvrsen insert avansa
			1	-> izvrsen insert
	*/

	$nalogId = intval($nalogId); 

	if ( $nalogId != 0 ) {

		$queryCheck = $db->prepare("
			SELECT 
				nalog_financije, nalog_procenat_avansa, nalog_potrebno_kandidata, nalog_broj_rata, nalog_datum_potpisa_naloga, nalog_provizija
			FROM 
				idk_nalozi 
			WHERE 
				nalog_id = :nalog_id
		");
		$queryCheck->execute(array(
			':nalog_id' => $nalogId
		));

		if ( $queryCheck->rowCount() == 1 ) {

			$rowCheck = $queryCheck->fetch();

			$nalog_financije 			= intval($rowCheck["nalog_financije"]);
			$nalog_procenat_avansa 		= $rowCheck["nalog_procenat_avansa"];
			$nalog_potrebno_kandidata 	= intval($rowCheck["nalog_potrebno_kandidata"]);
			$nalog_broj_rata 			= intval($rowCheck["nalog_broj_rata"]);
			$nalog_datum_potpisa_naloga	= $rowCheck["nalog_datum_potpisa_naloga"];
			$nalog_provizija			= $rowCheck["nalog_provizija"];

			if ( $nalog_financije == 2 ) {

				$avans_insert_id 			= 0; 
				$rate_insert_ids_explode 	= array();
				$rate_insert_ids_implode 	= "";

				/*
						Prvo se vrsi provjera da li je oznaceno da nalog ima avans START
					*/
						$flag_avans = 0;

						if ( $nalog_procenat_avansa != null ) {

							$flag_avans = 1; 

							/*
								Na osnovu datuma potpisa ugovora određuje se datum aktiviranja koji je +10 dana od dana potpisa ugovora
							*/
							$nf_datum_aktiviranja_avans = date("Y-m-01", strtotime($nalog_datum_potpisa_naloga));

							/*
								Na osnovu informacija odredjuje se iznos avansa za slucaj mjesecnih rata
							*/
							$nf_iznos_avans = ( $nalog_procenat_avansa / 100 ) * $nalog_potrebno_kandidata * $nalog_provizija;

							$queryAvans = $db->prepare("
								INSERT INTO idk_nalog_financije
								(
									nf_nalog_id, 
									nf_datum_aktiviranja, 
									nf_type, 
									nf_broj_rate, 
									nf_iznos, 
									nf_placeno, 
									nf_datum_fakturisanja, 
									nf_datum_placanja
								)
								VALUES 
								(
									:nf_nalog_id,
									:nf_datum_aktiviranja,
									:nf_type,
									:nf_broj_rate, 
									:nf_iznos, 
									:nf_placeno,
									:nf_datum_fakturisanja,
									:nf_datum_placanja
								)
							");
							$queryAvans->execute(array(
								':nf_nalog_id' => $nalogId, 
								':nf_datum_aktiviranja' => $nf_datum_aktiviranja_avans, 
								':nf_type' => 2, 
								':nf_broj_rate' => null, 
								':nf_iznos' => $nf_iznos_avans, 
								':nf_placeno' => 1, 
								':nf_datum_fakturisanja' => null, 
								':nf_datum_placanja' => null, 
							));

							$avans_insert_id = $db->lastInsertId();

						} else {

							$flag_avans = 0;
							$nf_iznos_avans = 0;
							$nf_datum_aktiviranja_start = date("Y-m-01", strtotime($nalog_datum_potpisa_naloga));

						}
					/*
						Prvo se vrsi provjera da li je oznaceno da nalog ima avans END
				*/

				/*
						Zatim se vrši insert ostalih rata na osnovu broja rata START
					*/

						/*
							Na osnovu formule - određuje se cijena rate
						*/
						$nf_iznos_rate = ( ( $nalog_potrebno_kandidata * $nalog_provizija ) - $nf_iznos_avans ) / $nalog_broj_rata;

						$datum_aktivacije_od_i = date("Y-m-d");
						if ( $flag_avans == 1 ) {
							$middle_day_of_the_month = date("Y-m-15", strtotime($nf_datum_aktiviranja_avans));
							$datum_aktivacije_od_i = date("Y-m-01", strtotime($middle_day_of_the_month. " +1 month"));
						} else {
							$middle_day_of_the_month = date("Y-m-15", strtotime($nf_datum_aktiviranja_start));
							$datum_aktivacije_od_i = date("Y-m-01", strtotime($middle_day_of_the_month));
						}

						for ( $i = 1; $i <= $nalog_broj_rata; $i++ ) {

							if ( $i > 1 ) {
								$middle_day_of_the_month = date("Y-m-15", strtotime($datum_aktivacije_od_i));
								$datum_aktivacije_od_i = date("Y-m-01", strtotime($middle_day_of_the_month. " +1 month"));
							}

							$rate_insert_id = 0;

							$queryRate = $db->prepare("
								INSERT INTO idk_nalog_financije
								(
									nf_nalog_id, 
									nf_datum_aktiviranja, 
									nf_type, 
									nf_broj_rate, 
									nf_iznos, 
									nf_placeno, 
									nf_datum_fakturisanja, 
									nf_datum_placanja
								)
								VALUES 
								(
									:nf_nalog_id,
									:nf_datum_aktiviranja,
									:nf_type,
									:nf_broj_rate, 
									:nf_iznos, 
									:nf_placeno,
									:nf_datum_fakturisanja,
									:nf_datum_placanja
								)
							");
							$queryRate->execute(array(
								':nf_nalog_id' => $nalogId, 
								':nf_datum_aktiviranja' => $datum_aktivacije_od_i, 
								':nf_type' => 3, 
								':nf_broj_rate' => $i, 
								':nf_iznos' => $nf_iznos_rate, 
								':nf_placeno' => 1, 
								':nf_datum_fakturisanja' => null, 
								':nf_datum_placanja' => null, 
							));

							$rate_insert_id = $db->lastInsertId();
							array_push($rate_insert_ids_explode, $rate_insert_id);

						}
					
						$rate_insert_ids_implode = implode(",", $rate_insert_ids_explode); 

					/*
						Zatim se vrši insert ostalih rata na osnovu broja rata END
				*/

				if ( $flag_avans == 1 ) {

					if ( in_array(0, $rate_insert_ids_explode) AND $avans_insert_id != 0 ) {

						$log_desc = "NALOG FINANCIJE - AVANS + RATE - Problem kod dodavanja rata u tabelu idk_nalog_financije. Avans ID = [".$avans_insert_id."]. Rate IDs = [".$rate_insert_ids_implode."]. Slučaj 104.";
						addToLogs($log_desc, 4);
						return 104;

					} else if ( in_array(0, $rate_insert_ids_explode) AND $avans_insert_id == 0 ) {

						$log_desc = "NALOG FINANCIJE - AVANS + RATE - Problem kod dodavanja avansa i rata u tabelu idk_nalog_financije. Avans ID = [".$avans_insert_id."]. Rate IDs = [".$rate_insert_ids_implode."]. Slučaj 105.";
						addToLogs($log_desc, 4);
						return 105;
					
					} else if ( !in_array(0, $rate_insert_ids_explode) AND $avans_insert_id == 0 ) {

						$log_desc = "NALOG FINANCIJE - AVANS + RATE - Problem kod dodavanja avansa u tabelu idk_nalog_financije. Avans ID = [".$avans_insert_id."]. Rate IDs = [".$rate_insert_ids_implode."]. Slučaj 106.";
						addToLogs($log_desc, 4);
						return 106;
					
					} else {

						$log_desc = "NALOG FINANCIJE - AVANS + RATE - Dodano avansno placanje i rate u tabelu idk_nalog_financije. Avans ID = [".$avans_insert_id."]. Rate IDs = [".$rate_insert_ids_implode."].";
						addToLogs($log_desc, 4);
						return 1;

					}

				} else {

					if ( in_array(0, $rate_insert_ids_explode) ) {

						$log_desc = "NALOG FINANCIJE - RATE - Problem kod dodavanja rata u tabelu idk_nalog_financije. Rate IDs = [".$rate_insert_ids_implode."]. Slučaj 103.";
						addToLogs($log_desc, 4);
						return 103;

					} else {

						$log_desc = "NALOG FINANCIJE - RATE - Dodane rate u tabelu idk_nalog_financije. Rate IDs = [".$rate_insert_ids_implode."].";
						addToLogs($log_desc, 4);
						return 1;

					}

				}

			} else {

				return 102; 

			}

		} else {

			return 101;
			
		}

	} else {

		return 100; 

	}
}

//FUNKCIJE UBACENE PRILIKOM PROMJENA U DIPL PRODAJI 17.09.2021 - END

/**
* ISPIS BROJA KANDIDATA PONOVNE PRIJAVE NA LISTI KAMPANJA
*/
function countKandidatiPonovnePrijave($kampanja_id){
	
	global $db;
	
	$read_query = $db->prepare ('SELECT COUNT(id_pp) as broj_kandidata
								 FROM idk_ponovne_prijave
								 WHERE kampanja_pp = :kampanja_pp
								');
	$read_query -> execute (array ( 
		':kampanja_pp' => $kampanja_id
	));
	
	$row = $read_query->fetch();
	echo $row['broj_kandidata'];
}

function getInkasoStatusDIPLKR($predvidjenoVrijeme){
	$result = 1; 
	$trenutnoVrijeme = date("Y-m-d");
	
	$inCaso1 = date("Y-m-d", strtotime($predvidjenoVrijeme."+21 days"));
	$inCaso2 = date("Y-m-d", strtotime($predvidjenoVrijeme."+40 days"));
	$inCaso3 = date("Y-m-d", strtotime($predvidjenoVrijeme."+90 days")); 
	
	if($inCaso1 <= $trenutnoVrijeme){
		$result = 3; 
	}
	if($inCaso2 <= $trenutnoVrijeme){
		$result = 4; 
	}
	if($inCaso3 <= $trenutnoVrijeme){
		$result = 5; 
	}
	
	return $result;
}

//Funkcije za REMINDER-e u Obradi DIPL START

function insertReminderCandidateDIPL($type, $candidate, $date_time, $employee, $status, $foreign_key){
	Global $db;
	Global $logged_employee_id;
	if(isset($type) AND isset($candidate) AND isset($date_time) AND isset($employee) AND isset($status) AND isset($foreign_key)){
		$candidateStatus = getStatusValueDIPLKandidatR($candidate);
		
		$queryInsert = $db->prepare("
			INSERT INTO idk_reminders
			(
				reminder_type,
				reminder_candidate_id,
				reminder_date_time,
				reminder_candidate_status,
				reminder_candidate_substatus,
				reminder_employee,
				reminder_status,
				reminder_foreign_key
			)
			VALUES
			(
				:reminder_type,
				:reminder_candidate_id,
				:reminder_date_time,
				:reminder_candidate_status,
				:reminder_candidate_substatus,
				:reminder_employee,
				:reminder_status,
				:reminder_foreign_key
			)
		");
		
		$queryInsert->execute(array(
			':reminder_type' => $type,
			':reminder_candidate_id' => $candidate,
			':reminder_date_time' => $date_time,
			':reminder_candidate_status' => $candidateStatus["status"],
			':reminder_candidate_substatus' => $candidateStatus["podstatus"],
			':reminder_employee' => $employee, 
			':reminder_status' => $status,
			':reminder_foreign_key' => $foreign_key
		));
		
		$lastInsertId = $db->lastInsertId();  
		
		if(intval($lastInsertId) != 0){
			$log_desc = "DIPL REMINDERS - Dodan novi reminder sa ID = [".$lastInsertId."]. Kandidat ID = [".$candidate."]";
			$log_date = date("Y-m-d H:i:s");
			$log_query = $db->prepare("
				INSERT INTO idk_logs
					(
						log_employeeid, 
						log_desc, 
						log_date
					)
				VALUES
					(
						:log_employeeid, 
						:log_desc, 
						:log_date
					)
			");

			$log_query->execute(array(
				':log_employeeid' => $logged_employee_id,
				':log_desc' => $log_desc,
				':log_date' => $log_date
			));
		}
	}
}

function updateReminderStatusDIPL($candidate){
	Global $db;
	$trenutnoVrijeme = date("Y-m-d H:i:s");
	if(isset($candidate)){
		$queryCount = $db->prepare("
			SELECT 
				COUNT(reminder_id) AS broj
			FROM
				idk_reminders
			WHERE 
				reminder_candidate_id = :reminder_candidate_id
				AND 
				reminder_type = 1
				AND 
				reminder_status = 1
				AND 
				reminder_date_time <= '".$trenutnoVrijeme."'
		");
		$queryCount->execute(array(
			':reminder_candidate_id' => $candidate
		));
		$rowCount = $queryCount->fetch();
		$brojCount = intval($rowCount["broj"]);
		if($brojCount != 0){
			$queryUpdate = $db->prepare("
				UPDATE 
					idk_reminders
				SET 
					reminder_status = 0
				WHERE 
					reminder_candidate_id = :reminder_candidate_id 
					AND 
					reminder_type = 1
					AND 
					reminder_status = 1
					AND 
					reminder_date_time <= '".$trenutnoVrijeme."'
			");
			$queryUpdate->execute(array(
				':reminder_candidate_id' => $candidate
			));
		}
	}
}

function updateReminderStatusDIPLBuduce($candidate, $reminder_ids){
	Global $db;
	$trenutnoVrijeme = date("Y-m-d H:i:s");
	$reminder_ids_imp = implode(",",$reminder_ids);

	if(isset($candidate) AND $reminder_ids_imp != ""){
		$queryCount = $db->prepare("
			SELECT 
				COUNT(reminder_id) AS broj
			FROM
				idk_reminders
			WHERE 
				reminder_candidate_id = :reminder_candidate_id
				AND 
				reminder_type = 1
				AND 
				reminder_status = 1
				AND 
				reminder_date_time > '".$trenutnoVrijeme."'
				AND 
				reminder_id IN (".$reminder_ids_imp.")
		");
		$queryCount->execute(array(
			':reminder_candidate_id' => $candidate
		));
		$rowCount = $queryCount->fetch();
		$brojCount = intval($rowCount["broj"]);
		if($brojCount != 0){
			$queryUpdate = $db->prepare("
				UPDATE 
					idk_reminders
				SET 
					reminder_status = 0
				WHERE 
					reminder_candidate_id = :reminder_candidate_id 
					AND 
					reminder_type = 1
					AND 
					reminder_status = 1
					AND 
					reminder_date_time > '".$trenutnoVrijeme."'
					AND 
					reminder_id IN (".$reminder_ids_imp.")
			");
			$queryUpdate->execute(array(
				':reminder_candidate_id' => $candidate
			));

			$zaposlenici_exp = array();
			$zaposlenici_imp = "";
			$reminders_exp = array();
			$reminders_imp = "";

			$queryGetEmployees = $db->prepare("
				SELECT 
					r.reminder_employee,
					r.reminder_date_time, 
					e.employee_email
				FROM 
					idk_reminders r
				JOIN 
					idk_employees e
				ON 
					r.reminder_employee = e.employee_id
				WHERE 
					r.reminder_id IN (".$reminder_ids_imp.")
					AND 
					employee_status NOT LIKE '0'
				GROUP BY
					r.reminder_employee
			");
			$queryGetEmployees->execute();
			while($rowGetEmployees = $queryGetEmployees->fetch()){
				if($rowGetEmployees["reminder_employee"] != $logged_employee_id)
					array_push($zaposlenici_exp,$rowGetEmployees["employee_email"]);
				array_push($reminders_exp,$rowGetEmployees["reminder_date_time"]);
			}

			if ( count($zaposlenici_exp) > 0 ) {

				$reminders_imp = implode(",",$reminders_exp);
				$zaposlenici_imp = implode(",",$zaposlenici_exp);

				$mail = new PHPMailer;
				$mail->isSMTP();

				$mail->Host = 'smtp.gmail.com';                 // Specify main and backup SMTP servers
				$mail->SMTPAuth = true;                         // Enable SMTP authentication
				$mail->Username = 'support@job-step.com';        // SMTP username
				$mail->Password = 'eooc nnxo aylp lqkh';          // SMTP password
				$mail->SMTPSecure = 'ssl';                      // Enable TLS encryption, `ssl` also accepted
				$mail->Port = 465;                              // TCP port to connect to
				$mail->CharSet = 'UTF-8';

				//Recipients
				$mail->setFrom('support@job-step.com', 'JobStep');
				
				foreach ($zaposlenici_exp as $value) {
					$mail->addAddress($value); 								// Add a recipient
				}
				
				$mail->Subject = "Arhiviran reminder kandidatu ".$candidate."";
				$mail->Body = "
					<p>
						Kandidatu arhiviran reminder iz obrade na datum/e: ".$reminders_imp.".<br>
					</p>
					<p>
						Detalje pogledajte na linku: " . getSiteUrlr() . "/nostrifikacija_diploma?page=otvori_ND_kandidata&id=".$candidate."
					</p>
				";
				$mail->AltBody = "ALT";

				if(!$mail->send()) {
					// echo 'Mailer Error: ' . $mail->ErrorInfo;
					// echo "Nije poslano zaposleniku!<br/>";
					$log_desc = "DIPL - Reminder Obrada Arhiv - Nije poslan e-mail zaposeliniku/cima [".$zaposlenici_imp."] da je/su arhiviran/i reminder/i na datum/e [".$reminders_imp."] za kandidat ID = [".$candidate."].";
				}else{
					// echo "Poslano zaposleniku!<br/>";
					$log_desc = "DIPL - Reminder Obrada Arhiv - Poslan e-mail zaposeliniku/cima [".$zaposlenici_imp."] da je/su arhiviran/i reminder/i na datum/e [".$reminders_imp."] za kandidat ID = [".$candidate."].";
				}
				
			} else {

				$log_desc = "DIPL - Reminder Obrada Arhiv - Nisu pronađeni zaposlenici za slanje obavijesti putem e-maila o arhiviranju remindera za kandidat ID = [".$candidate."].";

			}

			addLogDIPL($log_desc);
		}
	}
}

function getFileForCandidateNoteR($idNote) {
	Global $db; 

	$idNote = intval($idNote); 

	if ( $idNote != 0 ) {

		$query = $db->prepare("
			SELECT 
				note_files 
			FROM 
				idk_notes 
			WHERE 
				note_id  = :note_id 
		");
		$query->execute(array(
			':note_id' => $idNote
		));

		if ($query->rowCount() == 1 ) { 

			$row = $query->fetch();

			return $row["note_files"];
			
		} else {
			return null;
		}
	} else {

		return null; 

	}
}

function getReminderForNoteDIPLR($noteId, $candidateId){
	Global $db;
	$result = "";
	$queryCount = $db->prepare("
		SELECT 
			COUNT(reminder_id) AS broj
		FROM
			idk_reminders
		WHERE 
			reminder_candidate_id = :reminder_candidate_id
			AND
			reminder_foreign_key = :reminder_foreign_key
			AND 
			reminder_type = 1
	");
	$queryCount->execute(array(
		':reminder_candidate_id' => $candidateId,
		':reminder_foreign_key' => $noteId
	));
	$rowCount = $queryCount->fetch();
	$brojCount = intval($rowCount["broj"]);
	if($brojCount == 1){
		$querySelect = $db->prepare("
			SELECT 
				reminder_date_time,
				reminder_status
			FROM 
				idk_reminders
			WHERE 
				reminder_candidate_id = :reminder_candidate_id
				AND
				reminder_foreign_key = :reminder_foreign_key
				AND 
				reminder_type = 1
		");
		$querySelect->execute(array(
			':reminder_candidate_id' => $candidateId,
			':reminder_foreign_key' => $noteId
		));
		$rowSelect = $querySelect->fetch();
		$reminder_date_time = date("d.m.Y H:i", strtotime($rowSelect["reminder_date_time"]));
		if(intval($rowSelect["reminder_status"]) == 1){
			$result = '<span class="label label-danger material-label material-label_danger main-container__column"><i class="fa fa-bell" aria-hidden="true" style = "margin-right: 15px;"></i> '.$reminder_date_time.'</span>';
		}else{
			$result = '<span class="label label-success material-label material-label_success main-container__column"><i class="fa fa-bell" aria-hidden="true" style = "margin-right: 15px;"></i> '.$reminder_date_time.'</span>';
		}
	}else{
		$result = '<span class="label label-warning material-label material-label_warning main-container__column"><i class="fa fa-bell" aria-hidden="true" style = "margin-right: 15px;"></i> Nije dodan podsjetnik</span>';
	}
	return $result;
}
  
//Funkcije za REMINDER-e u Obradi DIPL END  

//FUNKCIJA UGOVORI DIPL START
function getOmoguciTipoviUgovoraDIPLK($idKan, $drzavaKandidat){
	Global $db;
	Global $logged_employee_id;
	$idKan = intval($idKan);
	$drzavaKandidat = intval($drzavaKandidat); // 1-BIH 2-SRB 3-DE
	$omogucenoZaposlenicima = array(11,32,33,43,48,49,83,88,67,75,69,158); //Ovdje su ID-ovi zaposlenika kojima su omogućeni ugovori od 30 % za nase kandidate
	$arrayOption = array(); //rezultat algoritma se sprema u ovu varijablu - return u obliku implode array
	$arrayOptionImplode = '';
	$opcijeVrijednost = '';
	$brProvjera = 0;
	$smjeroviOmoguceno = array();
	$get_smjerove_za_popust = $db->prepare("SELECT ss_id FROM idk_skole_smjerovi WHERE ss_struka_id IN (1,2)");
	$get_smjerove_za_popust->execute();
	while($row_smjerovi = $get_smjerove_za_popust->fetch()){
		array_push($smjeroviOmoguceno, $row_smjerovi['ss_id']);
	}
	//$smjeroviOmoguceno = array(49,245,253,264,278,293,601,614,620,656,673,724,775,9,16,18,19,20,58,61,243,260,273,306,310,334,361,383,404,408,412,445,479,489,500,522,546,559,562,570,583,586,595,602,604,627,630,636,643,653,665,687,703,717,726,729,736,754,758,760,769,770,789); //ovdje upisati smjerove koji ulaze u obzir EMIRRRNIZ
	if($idKan != 0 AND $drzavaKandidat != 0){
		
		//rucno dodavanje za jednog kandidata
		if($idKan == 46628 OR $idKan == 126659){
			$opcijeVrijednost = '
				<optgroup class="klasa1" label="Ugovori sa popustom od 20%">
					<option  value = "21">
						1 rata
					</option>
					<option value = "22">
						2 rate
					</option>
					<option  value = "23">
						3 rate
					</option>
					<option  value = "24">
						4 rate
					</option>
					<option  value = "25">
						5 rata
					</option>
				</optgroup>
			';
		}else{
			
			//---------------------------------------Bez popusta START
			if($drzavaKandidat == 1){
				//Ako je drzava kandidata BiH onda se omogućuje Mikrofin - inače ne
				$opcijeVrijednost = '
					<optgroup label="Ugovori bez popusta">
						<option  value = "9" data-subtext="10% popust">
							1 rata
						</option>
						<option value = "1">
							2 rate
						</option>
						<option  value = "5">
							3 rate
						</option>
						<option  value = "7">
							4 rate
						</option>
						<option  value = "3">
							5 rata
						</option>
						<option value = "11">
							Mikrofin
						</option>
					</optgroup>
				';
			}else{
				$opcijeVrijednost = '
					<optgroup label="Ugovori bez popusta">
						<option  value = "9" data-subtext="10% popust">
							1 rata
						</option>
						<option value = "1">
							2 rate
						</option>
						<option  value = "5">
							3 rate
						</option>
						<option  value = "7">
							4 rate
						</option>
						<option  value = "3">
							5 rata
						</option>
					</optgroup>
				';
			}
		}
		//---------------------------------------Bez popusta END
		array_push($arrayOption, $opcijeVrijednost);
		$opcijeVrijednost = '';
		//---------------------------------------30 % za nase kandidate START
		if(in_array($logged_employee_id, $omogucenoZaposlenicima)){
			//Ako je drzava kandidata BiH onda se omogućuje Mikrofin - inače ne
			if($drzavaKandidat == 1){
				$opcijeVrijednost = '
					<optgroup label="Ugovori sa popustom 30% za naše kandidate">
						<option value = "10">
							1 rata
						</option>
						<option value = "2" >
							2 rate
						</option>
						<option value = "6">
							3 rate
						</option>
						<option value = "8">
							4 rate
						</option>
						<option value = "4">
							5 rata
						</option>
						<option value = "12">
							Mikrofin
						</option>
					</optgroup>
				';
			}else{
				$opcijeVrijednost = '
					<optgroup label="Ugovori sa popustom 30% za naše kandidate">
						<option value = "10">
							1 rata
						</option>
						<option value = "2" >
							2 rate
						</option>
						<option value = "6">
							3 rate
						</option>
						<option value = "8">
							4 rate
						</option>
						<option value = "4">
							5 rata
						</option>
					</optgroup>
				';
			}
			array_push($arrayOption, $opcijeVrijednost);
			$opcijeVrijednost = '';
		}
		//---------------------------------------30 % za nase kandidate END
		
		//---------------------------------------NOVI UGOVORI START
		$queryProvjeraInfo = $db->prepare("
			SELECT 
				skola_nd_kandidata, skola_smjer_nd_kandidata, nivo_poznavanja_jezika, certifikat_nd_kandidata, kampanja_id
			FROM 
				idk_nd_kandidata
			WHERE 
				id_broj_nd_kandidata = :id_broj_nd_kandidata
		");
		$queryProvjeraInfo->execute(array(
			':id_broj_nd_kandidata' => $idKan
		));
		$brProvjera = $queryProvjeraInfo->rowCount();
		if(intval($brProvjera) != 0){
			$rowProvjeraInfo = $queryProvjeraInfo->fetch();
			$skolaKan = intval($rowProvjeraInfo["skola_nd_kandidata"]);
			$smjerSkolaKan = intval($rowProvjeraInfo["skola_smjer_nd_kandidata"]);
			$nivoJezikaKan = intval($rowProvjeraInfo["nivo_poznavanja_jezika"]); // 0 - nije oznaceno, 1 - bez znanja, 2- A1, 3-A2, 4-B1, 5-B2, 6-C1, 7-C2
			$certifikatKan = intval($rowProvjeraInfo["certifikat_nd_kandidata"]); // 1 - ima, 0 - nema
			$kampanja_id = intval($rowProvjeraInfo["kampanja_id"]); 
			//Provjerava se da li kandidat ima određeni smjer - ako ima idi dalje, inače nista
			if(in_array($smjerSkolaKan, $smjeroviOmoguceno)){
				//provjerava se da li kandidat ima certifikat ili nema
				if($certifikatKan == 1){
					//provjeravaju se nivoi jezika 
					if($nivoJezikaKan == 2){
						//Ako je A1 onda su ugovori od "71" do "75" - 30 %
						$opcijeVrijednost = '
							<optgroup label="Ugovori sa popustom od 30% za kandidate sa A1 certifikatom">
								<option  value = "71">
									1 rata
								</option>
								<option value = "72">
									2 rate
								</option>
								<option  value = "73">
									3 rate
								</option>
								<option  value = "74">
									4 rate
								</option>
								<option  value = "75">
									5 rata
								</option>
							</optgroup>
						';
					}else if($nivoJezikaKan == 3){
						//Ako je A2 onda su ugovori od "51" do "55" - 50 %
						$opcijeVrijednost = '
							<optgroup label="Ugovori sa popustom od 50% za kandidate sa A2 certifikatom">
								<option  value = "51">
									1 rata
								</option>
								<option value = "52">
									2 rate
								</option>
								<option  value = "53">
									3 rate
								</option>
								<option  value = "54">
									4 rate
								</option>
								<option  value = "55">
									5 rata
								</option>
							</optgroup>
						';
					}else if($nivoJezikaKan == 4 OR $nivoJezikaKan == 5 OR $nivoJezikaKan == 6 OR $nivoJezikaKan == 7){
						
						if( $logged_employee_id == 49 AND $drzavaKandidat != 3 ){
							//Ako je B1,B2,C1,C2 i zaposlenik je Emina Cehic onda je ugovor "99" - 100 %
							$opcijeVrijednost = '
								<optgroup label="Ugovor sa popustom od 100% za kandidate sa B1,B2,C1,C2 certifikatom">
									<option  value = "99">
										1 rata
									</option>
								</optgroup>
							';
						}else{
							//Ako je B1,B2,C1,C2 onda su ugovori od "41" do "45" - 70 %
							$opcijeVrijednost = '
							<optgroup label="Ugovori sa popustom od 70% za kandidate sa B1,B2,C1,C2 certifikatom">
								<option  value = "41">
									1 rata
								</option>
								<option value = "42">
									2 rate
								</option>
								<option  value = "43">
									3 rate
								</option>
								<option  value = "44">
									4 rate
								</option>
								<option  value = "45">
									5 rata
								</option>
							</optgroup>
						';
						}
					}else{
						$opcijeVrijednost = '';
					}
					
				}else{
					//Kandidat nema certifikata i dobija mogućnost 20% popusta
					$opcijeVrijednost = '
						<optgroup label="Ugovori sa popustom od 20% za kandidate bez certifikata">
							<option  value = "61">
								1 rata
							</option>
							<option value = "62">
								2 rate
							</option>
							<option  value = "63">
								3 rate
							</option>
							<option  value = "64">
								4 rate
							</option>
							<option  value = "65">
								5 rata
							</option>
						</optgroup>
					';
				}
				array_push($arrayOption, $opcijeVrijednost);
				$opcijeVrijednost = '';
			}
			if($kampanja_id == 162 OR $kampanja_id == 183 OR $kampanja_id == 210){
				//Akcija 20% popusta za sve struke - maj 2022
				$opcijeVrijednost = '
					<optgroup label="Ugovori sa popustom od 20% za sve struke - kampanje gastarbajter i messenger">
						<option  value = "61">
							1 rata
						</option>
						<option value = "62">
							2 rate
						</option>
						<option value = "63">
							3 rate
						</option>
						<option value = "64">
							4 rate
						</option>
						<option value = "65">
							5 rata
						</option>
					</optgroup>
				';
				array_push($arrayOption, $opcijeVrijednost);
				$opcijeVrijednost = '';
			}elseif($kampanja_id == 244 OR $kampanja_id == 246){
				//Akcija 30% popusta za black friday nov 2022
				$opcijeVrijednost = '
					<optgroup label="Ugovori sa popustom od 30% kampanje black friday">
						<option value = "71">
							1 rata
						</option>
						<option value = "72">
							2 rate
						</option>
						<option value = "73">
							3 rate
						</option>
						<option value = "74">
							4 rate
						</option>
						<option value = "75">
							5 rata
						</option>
					</optgroup>
				';
				array_push($arrayOption, $opcijeVrijednost);
				$opcijeVrijednost = '';
			}else{
				//PROVJERA PONOVNIH PRIJAVA ZA POPUST NA KAMPANJE
				$queryPonovnaPrijava = $db->prepare("
				SELECT 
				SUM(CASE WHEN kampanja_pp IN (183,162,210) then 1 else 0 end) as kam1,
				SUM(CASE WHEN kampanja_pp IN (244,246) then 1 else 0 end) as kam2
				FROM 
					idk_ponovne_prijave
				WHERE 
					id_kandidata = :id_broj_nd_kandidata	
				
				");
				$queryPonovnaPrijava->execute(array(
					':id_broj_nd_kandidata' => $idKan
				));
				if($queryPonovnaPrijava->rowCount() > 0){
					$row_kampanje = $queryPonovnaPrijava->fetch();
					$kampanje_20 = $row_kampanje['kam1'];
					$kampanje_30 = $row_kampanje['kam2'];
					if($kampanje_20 > 0){
						//Akcija 20% popusta za sve struke - maj 2022
						$opcijeVrijednost = '
							<optgroup label="Ugovori sa popustom od 20% za sve struke - kampanje gastarbajter i messenger">
								<option  value = "61">
									1 rata
								</option>
								<option value = "62">
									2 rate
								</option>
								<option  value = "63">
									3 rate
								</option>
								<option  value = "64">
									4 rate
								</option>
								<option  value = "65">
									5 rata
								</option>
							</optgroup>
						';
						array_push($arrayOption, $opcijeVrijednost);
						$opcijeVrijednost = '';
					}
					if($kampanje_30 > 0){
						//Akcija 30% popusta za black friday nov 2022
						$opcijeVrijednost = '
							<optgroup label="Ugovori sa popustom od 30% kampanje black friday">
								<option value = "71">
									1 rata
								</option>
								<option value = "72">
									2 rate
								</option>
								<option value = "73">
									3 rate
								</option>
								<option value = "74">
									4 rate
								</option>
								<option value = "75">
									5 rata
								</option>
							</optgroup>
						';
						array_push($arrayOption, $opcijeVrijednost);
						$opcijeVrijednost = '';
					}
				}
			}
		}
		
		//ZA INKASO 2 I INKASO 3 MOGUC JE POPUST 0D 10 % NA 2,3,4 I 5 RATA
		$queryProvjeraInkaso23 = $db->prepare("
			SELECT id_broj_nd_kandidata
			FROM idk_nd_kandidata
			JOIN idk_predracuni ON id_broj_nd_kandidata = pr_kandidat_id
			WHERE pr_vrsta_predracuna = 1 AND pr_status IN (4,5) AND pr_uplaceno = 0 AND pr_rata = 1 AND id_broj_nd_kandidata = :id_broj_nd_kandidata
		");
		$queryProvjeraInkaso23->execute(array(
			':id_broj_nd_kandidata' => $idKan
		));
		$brProvjeraI23 = $queryProvjeraInkaso23->rowCount();
		if($brProvjeraI23 > 0){
			$opcijeVrijednost = '
				<optgroup label="Ugovori sa popustom od 10% za inkaso kandidate">
					<option value = "82">
						2 rate
					</option>
					<option  value = "83">
						3 rate
					</option>
					<option  value = "84">
						4 rate
					</option>
					<option  value = "85">
						5 rata
					</option>
				</optgroup>
			';
			array_push($arrayOption, $opcijeVrijednost);
			$opcijeVrijednost = '';
		}
		//Omogucavanje Dijani da da oznaci ugovor kojeg kandidat ne placa - Sluzi samo da se prati njegov proces preko naseg sistema, Dijanin ID = 43
		if(($logged_employee_id == 43 OR $logged_employee_id == 369 OR $idKan == 52628 ) AND $drzavaKandidat != 3){
			$opcijeVrijednost = '
				<optgroup label="Ugovor sa popustom od 100% za kandidate sa B1,B2,C1,C2 certifikatom">
					<option  value = "99">
						1 rata
					</option>
				</optgroup>
			';
			array_push($arrayOption, $opcijeVrijednost);
			$opcijeVrijednost = '';
		}
		
		
		//---------------------------------------NOVI UGOVORI END 
		$arrayOptionImplode = implode("", $arrayOption);
		return $arrayOptionImplode;
	}else{
		return '';
	}
}
//FUNKCIJA UGOVORI DIPL END

function insertReminderOdbioUgovor($tokenUgovor){
	Global $db;
	if(isset($tokenUgovor)){
		$dateReminder = date("Y-m-d H:i:s", strtotime("+1 day")); //ovdje doghovoriti datum koji ide
		$queryInfoUgovor = $db->prepare("
			SELECT 
				ug_id,
				ug_zaposlenik_id,
				ug_kandidat_id
			FROM 
				idk_nd_ugovori
			WHERE 
				ug_token = :token
		");
		$queryInfoUgovor->execute(array(
			':token' => $tokenUgovor
		));
		if($queryInfoUgovor->rowCount() != 0){
			$rowInfoUgovor = $queryInfoUgovor->fetch();
			$reminderForeignKey = intval($rowInfoUgovor["ug_id"]);
			$reminderEmployee = intval($rowInfoUgovor["ug_zaposlenik_id"]);
			$idKan = $rowInfoUgovor["ug_kandidat_id"];
			$statusKan = getStatusValueDIPLKandidatR($idKan);
			$queryInsert = $db->prepare("
				INSERT INTO idk_reminders
					(
						reminder_type,
						reminder_candidate_id, 
						reminder_date_time, 
						reminder_candidate_status, 
						reminder_candidate_substatus, 
						reminder_employee, 
						reminder_status, 
						reminder_foreign_key
						
					)
				VALUES
					(
						:reminder_type,
						:reminder_candidate_id,
						:reminder_date_time,
						:reminder_candidate_status,
						:reminder_candidate_substatus,
						:reminder_employee,
						:reminder_status,
						:reminder_foreign_key
					)
			");
			
			$queryInsert->execute(array(
				':reminder_type' => 2,
				':reminder_candidate_id' => $idKan,
				':reminder_date_time' => $dateReminder,
				':reminder_candidate_status' => $statusKan["status"],
				':reminder_candidate_substatus' => $statusKan["podstatus"],
				':reminder_employee' => $reminderEmployee,
				':reminder_status' => 1,
				':reminder_foreign_key' => $reminderForeignKey
			));
			$lastReminderID = $db->lastInsertId();
			$log_desc = "UGOVOR/REMINDER -> Dodan reminder ID = [".$lastReminderID."] za ugovor ID = [".$reminderForeignKey."].";
			
			$log_date = date('Y-m-d H:i:s');

			$log_query = $db->prepare("
				INSERT INTO idk_logs
					(log_employeeid, log_desc, log_date)
				VALUES
					(:log_employeeid, :log_desc, :log_date)
			");

			$log_query->execute(array(
				':log_employeeid' => 69, //Stavio sebe - NA live treba biti Skladiste ili nesto
				':log_desc' => $log_desc,
				':log_date' => $log_date
			));
		}
	}
}

function arhiviranReminderUgovor($idKan){
	Global $db;
	Global $logged_employee_id;
	$idKan = intval($idKan);
	if($idKan != 0){
		$remNizExp = array();
		$remNizImp = "";
		$queryProvjera = $db->prepare("
			SELECT
				rem.reminder_id
			FROM 
				idk_reminders rem
			JOIN 
				idk_nd_ugovori ug 
			ON 
				ug.ug_id = rem.reminder_foreign_key
			WHERE 
				rem.reminder_type = 2
				AND 
				rem.reminder_status = 1
				AND 
				rem.reminder_candidate_id = ".$idKan."
		");
		$queryProvjera->execute();
		$brojQueryProvjera = $queryProvjera->rowCount();
		if($brojQueryProvjera != 0){
			while($rowProvjera = $queryProvjera->fetch()){
				$idRem = intval($rowProvjera["reminder_id"]);
				array_push($remNizExp, $idRem);
			}
			$remNizImp = implode(",", $remNizExp);
			if($remNizImp != ""){
				$queryUpdate = $db->prepare("
					UPDATE idk_reminders
					SET reminder_status = 0
					WHERE reminder_id IN (".$remNizImp.")
				");
				$queryUpdate->execute();
				$log_desc = "UGOVOR/REMINDER -> Arhiviran/i Reminder/i ID = [".$remNizImp."] za kandidata ID = [".$idKan."].";
			
				$log_date = date('Y-m-d H:i:s');

				$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_employeeid, log_desc, log_date)
					VALUES
						(:log_employeeid, :log_desc, :log_date)
				");

				$log_query->execute(array(
					':log_employeeid' => $logged_employee_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date
				));
			}
		}
	}
}

function checkOdustao($kandidat_id, $predracun_id){
	Global $db;
	$queryCount = $db->prepare("
		SELECT id_broj_nd_kandidata 
		FROM idk_nd_kandidata 
		JOIN idk_predracuni ON id_broj_nd_kandidata = pr_kandidat_id 
		WHERE status_nd_kandidata = 7 AND pr_uplaceno = 1 AND id_broj_nd_kandidata = :id_broj_nd_kandidata AND pr_id = :pr_id
	");
	$queryCount->execute(array(
		':id_broj_nd_kandidata' => $kandidat_id,
		':pr_id' => $predracun_id
	));
	$rowCount = $queryCount->rowCount();
	if($rowCount > 0){
		return true;
	}else{
		return false;
	}
}





				/**************************************************
				*		FUNKCIJE ZA PRAĆENJE STATUSA AGENTA
				***************************************************/

/**
*	GET AGENT TIME SPENT ON A STATUS
*	@parmas statusID
*/
function getStatusTimeForLoggedAgent( $status){
	
	global $db;
	global $logged_employee_id;
	
	$today = date('Y-m-d');
	
	$read_query 
				= $db -> prepare("
							SELECT activity_pocetak
							FROM idk_agenti_activity
							WHERE activity_pocetak BETWEEN '$today 00:00:00' AND '$today 23:59:59' 
							AND	 activity_status = :status 
							AND	 activity_aktivan = 1 
							AND	 employee_id = :employee_id
						"); 	
	$read_query -> execute (array (
		':status' => $status,
		':employee_id' => $logged_employee_id
	));

	
	$result = $read_query->fetch ();
	
	$activity_ukupno = calculateTime( $result['activity_pocetak'] );		
	
	return $activity_ukupno;

}

function getAllStatusTimesForLoggedAgent()
{
	global $db;
	global $logged_employee_id;
	
	$today = date('Y-m-d');
	
	$read_query 
				= $db -> prepare("
							SELECT activity_ukupno, activity_aktivan, activity_status
							FROM idk_agenti_activity
							WHERE activity_pocetak BETWEEN '$today 00:00:00' AND '$today 23:59:59'
							AND	 employee_id = :employee_id
						"); 	
						
	$read_query -> execute (array (
		':employee_id' => $logged_employee_id
	));

	$vremena = array();
	while($result = $read_query->fetch ())
	{
		if( $result['activity_aktivan'] )
		{
			$activity_ukupno =  calculateTime( $result['activity_status'] );		
			$vremena[]		 = $activity_ukupno;
		}
		else
		{
			$activity_ukupno =  $result['activity_ukupno'] ;		
			$vremena[]		 = $activity_ukupno;
		}
	}
	return $vremena;

}


function getCurrentStatus()
{
	global $db;
	global $logged_employee_id;
	
	$today = date('Y-m-d');
	
	$read_query 
				= $db -> prepare("
							SELECT activity_status
							FROM idk_agenti_activity
							WHERE activity_pocetak BETWEEN '$today 00:00:00' AND '$today 23:59:59'
							AND	 activity_aktivan = 1
							AND	 employee_id = :employee_id
						"); 	
						
						
	$read_query -> execute (array (
		':employee_id' => $logged_employee_id
	));
	
	if($read_query->rowCount() > 0)
	{
		$status_row  = $read_query->fetch();
		$status 	 = $status_row['activity_status'];
		
		if ( $status == 0 )
				return false;
		else
				return $status;
	}
	
	return false;

}

function startTimeTracking( $status )
{
	global $db;
	global $logged_employee_id;
	
	$today = date( 'Y-m-d' );
	$now   = date( 'Y-m-d H:i:s' );
	
	$update_query 
				= $db -> prepare("
							UPDATE idk_agenti_activity 
							SET   activity_pocetak = :activity_pocetak,  activity_aktivan = 1
							WHERE activity_pocetak BETWEEN '$today 00:00:00' AND '$today 23:59:59'
							AND	 activity_status = :status 
							AND	 employee_id = :employee_id
							AND	 activity_aktivan = 0
							"); 	

								
	$update_query -> execute (
						array 
						(
							':activity_pocetak' => $now,
							':status' 			=> $status,
							':employee_id' 		=> $logged_employee_id
						)
					 );

	$read_query 
				= $db -> prepare("
							SELECT activity_ukupno
							FROM idk_agenti_activity
							WHERE activity_pocetak BETWEEN '$today 00:00:00' AND '$today 23:59:59' 
							AND	 activity_status = :status 
							AND	 activity_aktivan = 1 
							AND	 employee_id = :employee_id
						"); 	
						
	$read_query -> execute (
					 array 
					 (
						':status' => $status,
						':employee_id' => $logged_employee_id
				     )
					);

	
	$result = $read_query->fetch ();
	return $result['activity_ukupno'] ;
}

function stopAgentStatusTracking( $status )
{
	global $db;
	global $logged_employee_id;
	
	$today = date('Y-m-d');
	$now = date('Y-m-d H:i:s');
	
	if(isLastStatusIdle())
	{
		$duration_in_seconds	  = getDurationOfActiveIdleStatus();
		
		if( $duration_in_seconds > 30 ) // IDLE MAKSIMALNO MOŽE TRAJATI 30 SEKUNDI 
		{
			// PREBACITI TRAJANJE IDLEA NA PAUZU - 30 SEKUNDI
			$duration_in_seconds -= 30;
			addDurationToPauseStatus( $duration_in_seconds );
			
			// ukupno vrijeme je 30 sekundi ( max )
			$activity_ukupno 	=  date('H:i:s', (strtotime('00:00:00') + 30 ) );
		}
		else
		{
			// idle status nije prešao maksimum, te se njegova trenutna vrijednost dodaje kao ukupno vrijeme
			$activity_ukupno 	=  date('H:i:s', (strtotime('00:00:00') + $duration_in_seconds ) );
		}

		$sql_statement 		= 
							   "UPDATE idk_agenti_activity 
								SET activity_aktivan = 0, activity_kraj = :activity_kraj, activity_ukupno = ADDTIME( activity_ukupno, :activity_ukupno )
								WHERE  activity_pocetak BETWEEN '$today 00:00:00' AND '$today 23:59:59' 
								AND	 activity_aktivan = 1 
								AND	 employee_id = :employee_id 
								";
							
		$placeholder_array 	= array (
									':activity_kraj' 	=> $now,
									':activity_ukupno' 	=> $activity_ukupno,
									':employee_id' 		=> $logged_employee_id
									);
	}
	else
	{
		$activity_ukupno 	= calculateTime($status);	
		
		$sql_statement 		= 
							   "UPDATE idk_agenti_activity 
								SET activity_aktivan = 0, activity_kraj = :activity_kraj, activity_ukupno = :activity_ukupno
								WHERE  activity_pocetak BETWEEN '$today 00:00:00' AND '$today 23:59:59' 
								AND	 activity_aktivan = 1 
								AND	 employee_id = :employee_id 
								";
							
		$placeholder_array 	= array (
									':activity_kraj' 	=> $now,
									':activity_ukupno' 	=> $activity_ukupno,
									':employee_id' 		=> $logged_employee_id
									);
	}
	
	
	
	$update_query 	 = 
			$db -> prepare( $sql_statement ); 	
								
	$success 		= 
		$update_query -> execute ( $placeholder_array );
				
	if($success) 
		return true;
	else 
		return false;
}

/**
*	CALCULATE TIME FROM STATUS BEGINNING TO NOW
*	@returns ukupno_vrijeme spent on a status
*/
function calculateTime( $status )
{
	global $db;
	global $logged_employee_id;
	
	$today = date('Y-m-d');
	$now = date('Y-m-d H:i:s');
	
	
	if($status !== null )
	{
		// za *prosljeđeni* status, vrati početno i ukupno vrijeme
		$sql_statement 		= 
							"SELECT activity_ukupno, activity_pocetak 
							 FROM   idk_agenti_activity
							 WHERE  activity_pocetak BETWEEN '$today 00:00:00' AND '$today 23:59:59' 
							 AND	 activity_status = :status 
							 AND	 activity_aktivan = 1 
							 AND	 employee_id = :employee_id
							";
							
		$placeholder_array 	= array (
								':status' => $status,
								':employee_id' => $logged_employee_id
							);
	}
	else
	{			
		// *pronađi* status koji je aktivan i vrati početno i ukupno vrijeme
		$sql_statement 		= 
							"SELECT activity_ukupno, activity_pocetak 
							 FROM   idk_agenti_activity
							 WHERE  activity_pocetak BETWEEN '$today 00:00:00' AND '$today 23:59:59'
							 AND	 activity_aktivan = 1 
							 AND	 employee_id = :employee_id
							";
							
		$placeholder_array 	= array (
								':employee_id' => $logged_employee_id
							);
	}
	
	$read_query = $db -> prepare( $sql_statement ); 						
	$read_query -> execute ( $placeholder_array );
	
	
	$row = $read_query -> fetch();
	
	$ukupno_vrijeme 		= $row['activity_ukupno'];
	$activity_pocetak 	 	= $row['activity_pocetak'];
	
	$trajanje_statusa		=  abs( strtotime( $now ) - strtotime ( $activity_pocetak ) ); 
	$novo_ukupno_vrijeme 	=  date( 'H:i:s', ( strtotime( $ukupno_vrijeme ) + $trajanje_statusa ) );
	
	return $novo_ukupno_vrijeme;
}

function setStatusToIdle()
{
	
	global $db;
	global $logged_employee_id;
	
	$today = date('Y-m-d');
	$now = date('Y-m-d H:i:s');
	
	$update_query 
				= $db -> prepare("
							UPDATE idk_agenti_activity 
							SET   activity_pocetak = :activity_pocetak,  activity_aktivan = 1
							WHERE activity_pocetak BETWEEN '$today 00:00:00' AND '$today 23:59:59'
							AND	 activity_status = :status 
							AND	 employee_id = :employee_id
							AND	 activity_aktivan = 0
							"); 	

								
	$success 	= 
				  $update_query -> execute (
										array (
											':activity_pocetak' => $now,
											':status' 			=> 0,
											':employee_id' 		=> $logged_employee_id
										)
									);
	if( $success )
		return true;
	else 
		return false;
}

function isLastStatusIdle()
{
	
	global $db;
	global $logged_employee_id;
	
	$today = date('Y-m-d');

	$sql_statement 		= 
							"SELECT activity_status 
							 FROM   idk_agenti_activity
							 WHERE  activity_pocetak BETWEEN '$today 00:00:00' AND '$today 23:59:59'
							 AND	 activity_aktivan = 1 
							 AND	 employee_id = :employee_id
							";
							
	$placeholder_array 	= array (
							':employee_id' => $logged_employee_id
						);

	
	$read_query = $db -> prepare ( $sql_statement ); 	
	$read_query 	  -> execute ( $placeholder_array );
	
	$row 				= $read_query -> fetch();
	$aktivan_status		= $row['activity_status'];
	
	if( $aktivan_status == 0)
		return true;
	else 
		return false;
}

function getDurationOfActiveIdleStatus()
{
	global $db;
	global $logged_employee_id;
	
	$today = date( 'Y-m-d' );
	$now   = date( 'Y-m-d H:i:s' );

	$sql_statement 		= 
						"SELECT activity_pocetak 
						 FROM   idk_agenti_activity
						 WHERE  activity_pocetak BETWEEN '$today 00:00:00' AND '$today 23:59:59' 
						 AND	 activity_status = :status 
						 AND	 activity_aktivan = 1 
						 AND	 employee_id = :employee_id
						";
						
	$placeholder_array 	= array (
							':status' => 0,
							':employee_id' => $logged_employee_id
						);

	
	$read_query = $db  -> prepare ( $sql_statement ); 	
	$read_query		   -> execute ( $placeholder_array );
	
	$row = $read_query -> fetch();
	
	$activity_pocetak 	 	 = $row['activity_pocetak'];
	
	if( isEndOfWorkingHours() )
		$trajanje_statusa		 =  abs( strtotime( '17:00:00' ) - strtotime ( $activity_pocetak ) );
	else
		$trajanje_statusa		 =  abs( strtotime( $now ) 		 - strtotime ( $activity_pocetak ) );
	
	
	return $trajanje_statusa;
}

function isEndOfWorkingHours()
{
	$now = date('H:i:s');
	
	if( $now > '17:00:00' )
		return true;
	else 
		return false;
}

function addDurationToPauseStatus( $trajanje_idle_statusa )
{
	global $db;
	global $logged_employee_id;
	
	$today = date('Y-m-d');
	$now = date('Y-m-d H:i:s');
	
	
	$novo_ukupno_vrijeme 	 =  date('H:i:s', (strtotime('00:00:00') + $trajanje_idle_statusa ) ); 
	// strtotime ( '00:00:00' ) jer mi inače date funkcija doda sat ako ne počinjem od 00:00:00  - pretpostavljam zbog UTC+1 serverskog vremena
	
	
	$update_query 	 = 
			$db -> prepare("
					UPDATE idk_agenti_activity 
					SET activity_ukupno = ADDTIME(activity_ukupno, :novo_ukupno_vrijeme) 
					WHERE  activity_pocetak BETWEEN '$today 00:00:00' AND '$today 23:59:59' 
					AND	 activity_status = 1 
					AND	 employee_id = :employee_id 
				"); 	
								
	$success 		= 
		$update_query -> execute (
					array (
						':novo_ukupno_vrijeme' => $novo_ukupno_vrijeme,
						':employee_id' 		   => $logged_employee_id
					)
				);


	if($success) 
		return true;
	else 
		return false;
}

/**
*
* Funkcija koja dodaje redove u tabelu idk_agent_activity kojom se započinje radni dan agenta 
* @params NO PARAMS 
* @returns -true ako nema dodanih redova za danas - za pomenutog agenta | u suprotnom -false
*
**/

function createTimetablesForToday(){
	global $db;
	global $logged_employee_id;
	
	$date_pocetak 	= date ('Y-m-d 00:00:00');
	$date_kraj 		= date ('Y-m-d 23:59:59');
	
	
	/**
	* 	Pomocni niz da se opišu dostupni statusi
	*	Novi status samo dodati u ovaj niz i u frontendu
	**/
	
	$statusi = array(
		0 => 'idle',
		1 => 'pauza',
		2 => 'live prodaja',
		3 => 'viber',
		4 => 'obuka',
		5 => 'inbound',
	);
	
	// provjeriti da li je agent zapoceo vec danasnjim radom
	
	$count_query = $db->prepare('	
								SELECT COUNT(*) as kreirani_redovi
								FROM idk_agenti_activity 
								WHERE activity_pocetak BETWEEN "'. $date_pocetak .'" AND "'. $date_kraj. '"
								AND employee_id = :employee_id
								'
								);
	
	$count_query ->execute(
					array (
						':employee_id' 		   => $logged_employee_id
					)
				);
	
	$row = $count_query->fetch();
	
	
	if( $row['kreirani_redovi'] == 0 )
	{
		foreach( $statusi as $index => $status )
		{
			
			// idle status se automatski postavlja kao defaultni (potreban pocetak statusa ) i navodi se kao aktivan
			if( $index == 0 )
			{
				$date		 = date ('Y-m-d H:i:s');
				$date_kraj	 = date ('Y-m-d H:i:s');
				$aktivan = 1;
			}
			else
			{
				$date	 = date ('Y-m-d H:i:s');
				$date_kraj	 = date ('Y-m-d H:i:s');
				$aktivan = 0;
			}
			
			// insert into table
			$update_query = $db->prepare(' 
									INSERT INTO idk_agenti_activity
										( employee_id, activity_status, activity_pocetak, activity_kraj, activity_aktivan)
									VALUES
										( :employee_id, :activity_status, :activity_pocetak, :activity_kraj, :activity_aktivan )
									');
			
			$update_query->execute(array(
						':employee_id' => $logged_employee_id,
						':activity_status' => $index, // index se unosi u bazu ne tekst!
						':activity_pocetak' => $date,
						':activity_kraj' => $date_kraj,
						':activity_aktivan' => $aktivan,
					));
					
			// print_r($update_query->errorInfo());
		}
		
		session_start();
			$_SESSION['timetables_set'] = date ('Y-m-d H:i:s');
		session_destroy();
		
		
		return true;
	}
	else return false;
}

				/**************************************************
				*		FUNKCIJE ZA PRAĆENJE STATUSA AGENTA
				***************************************************
										END	
				***************************************************/
//FUNKCIJA KOJA SLUŽI ZA SLANJE LINKA UGOVORA PREKO VIBERA KAD SE: 
// - PRODA UGOVOR, URADI PROMJENA UGOVORA, 
// - GENERIŠE PREDRAČUN PRVE RATE (prihvati ugovor)
// - GENERIŠE PREDRAČUN n-TE RATE (cronjob, ručno generisanje predračuna)
// 																					START
function sendViberUgovorLink($idKandidat, $poruka_text_viber, $poruka_text_sms, $ugovor_link, $poruka_button){
	
	Global $db;
	$query_get_broj = $db->prepare("
		SELECT mobilni_nd_kandidata 
		FROM idk_nd_kandidata
		WHERE id_broj_nd_kandidata = :nd_kandidat_id
	");
	
	$query_get_broj -> execute(array(':nd_kandidat_id' => $idKandidat));
	
	$row_get_broj = $query_get_broj->fetch();
	
	$nd_kandidat_mobitel = $row_get_broj['mobilni_nd_kandidata'];
	//$nd_kandidat_mobitel = "38762473740";
	
	// $jezik = substr($link,-2);
	//$image_url = "https://crm.job-step.com/images/medicinska_sestra_tehnicar_berlin.jpg";
	
	$active_provider = getActiveProviderForSendingMessages(9);
	if ($active_provider == 1) {
		$params = array(
				
			"scenarioKey" => "7A32331B2103607D2F890C04FEB34942",
			"destinations" => array(
				"to" => array(
					"phoneNumber" => $nd_kandidat_mobitel,
					)
			),
			"sms" => array(
				"text" =>$poruka_text_sms,
				),
			"viber" => array(
				"text" => $poruka_text_viber,
				//"imageURL" => $imageURL,
				"buttonText" => $poruka_button,
				"buttonURL" => $ugovor_link,
				// "isPromotional" => "true"
			)
		);


		$data = json_encode($params);
		$curl = curl_init();

		curl_setopt_array($curl, array(
		CURLOPT_URL => "https://ej8w3r.api.infobip.com/omni/1/advanced",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_POSTFIELDS => $data,
		CURLOPT_HTTPHEADER => array(
			"accept: application/json",
			"authorization: Basic ZWJlbmRlcjE6RUJlbmRlcjY0MzI=",
			"content-type: application/json"
		),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);
	} else {
		$phoneNumber = checkPhoneNumberForNTH($nd_kandidat_mobitel);
		$params = array(
			"channels" => array(
				"VIBER",
				"SMS"
			),
			"destinations" => array(
				array(
					"phoneNumber" => $phoneNumber
				)
			),
			"viber" => array(
				"priority" => 1,
				"sender" => "Jobstep Int",
				"buttonCaption" => $poruka_button,
				"buttonAction" => $ugovor_link,
				"text" => $poruka_text_viber,
				"ttl" => 14440,
				"label" => "promotion"
			),
			"sms" => array(
				"priority" => 2,
				"sender" => "Jobstep Int",
				"text" => $poruka_text_sms
			)
		);
		$params_encode = json_encode($params);
		$response = sendMessageViaNTH($params_encode);
	}
	return;
	// if ($err) {
	  // echo "cURL Error #:" . $err;
	// } else {
	  // echo $response;
	// }
}	

//FUNKCIJA KOJA SLUŽI ZA SLANJE LINKA UGOVORA PREKO VIBERA KAD SE: 
// - PRODA UGOVOR, URADI PROMJENA UGOVORA, 
// - GENERIŠE PREDRAČUN PRVE RATE (prihvati ugovor)
// - GENERIŠE PREDRAČUN n-TE RATE (cronjob, ručno generisanje predračuna)
// 																					END



//FUNKCIJA KOJA SLUŽI ZA SLANJE LINKA UGOVORA PREKO MAILA KAD SE: 
// - PRODA UGOVOR, URADI PROMJENA UGOVORA, 
// - GENERIŠE PREDRAČUN PRVE RATE (prihvati ugovor)
// - GENERIŠE PREDRAČUN n-TE RATE (cronjob, ručno generisanje predračuna)
// 																					START
function sendMailUgovorLink($kandidat_id, $ugovor_link, $nacin_placanja = 1) {
	//fja za slanje linka za prihvatanje ugovora
	Global $db;
	Global $logged_employee_id;

	// GET CANDIDATE EMAIL
	$querySendMail = $db->prepare("
					SELECT email_nd_kandidata
					FROM idk_nd_kandidata
					WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata
					");

	$querySendMail->execute(array(
		":id_broj_nd_kandidata" => $kandidat_id
	));

	$rowMail = $querySendMail->fetch();
	$kandidat_email = $rowMail['email_nd_kandidata'];
	
	$jezik = substr($ugovor_link,-2);

	if ( $nacin_placanja == 2 ){
		$signatura = $_SERVER['DOCUMENT_ROOT']."/files/ugovori_uplatnice_dipl/signatura-SRB-automatski-mail-new.gif"; //Adis - Signatura za Srbiju 444
	} else {
		$signatura = $_SERVER['DOCUMENT_ROOT']."/files/ugovori_uplatnice_dipl/nostrifikacija_CH.gif"; 
	}
	
	
	$mail = new PHPMailer;

	$mail->isSMTP();											// Set mailer to use SMTP

	$mail->Host = 'smtp.gmail.com';                 // Specify main and backup SMTP servers
	$mail->SMTPAuth = true;                         // Enable SMTP authentication
	$mail->Username = 'support@job-step.com';        // SMTP username
	$mail->Password = 'eooc nnxo aylp lqkh';          // SMTP password
	$mail->SMTPSecure = 'ssl';                      // Enable TLS encryption, `ssl` also accepted
	$mail->Port = 465;                              // TCP port to connect to
	$mail->CharSet = 'UTF-8';

	//Recipients
	$mail->setFrom('support@job-step.com', 'JobStep');
	
	$mail->addAddress($kandidat_email);		// Add a recipient

	if($jezik == "bs"){
		$subject ="Nostrifikacija: Ugovor i predračun";
		$postovani = "Poštovani";
	}elseif($jezik == "sr"){
		$subject ="Nostrifikacija/Evaluacija: Ugovor i predračun";
		$postovani = "Poštovani";
	}else{
		$subject = "Anerkennungsverfahren: Vertrag und Vorrechnung";
		$postovani = "Sehr geehrte Dammen und Herren";
	}
	$mail->Subject = $subject;
	if($jezik == "bs"){
		$mail->Body    = 'Poštovani,
						<br><br>Hvala Vam što ste nam ukazali svoje povjerenje i što zajedno ulazimo u proces nostrifikacije Vaše diplome.
						<br><br>Klikom na link možete da pregledate ugovor: <a href="'.$ugovor_link.'">LINK</a>
						<br>Molimo Vas da:
						
						<br><br>	- Nakon otvaranja ugovora pritisnete dugme "Prihvati ugovor" kako bi načinili prvi korak ka nostrifikaciji Vaše diplome.
						<br><br>	- Uplatu novčanog iznosa vršite po osnovu ispostavljenog predračuna (predračun nije neophodni štampati, punovažan je i u elektronskom obliku).
						<br><br>	- Primjer uplatnice također možete preuzeti klikom na dugme "Uplatnica".

						<br><br>Za sve dodatne informacije i pitanja budite slobodni da nas kontaktirate. Stojimo Vam na raspolaganju i radujemo se uspješnoj saradnji.
						
						<br><br>S poštovanjem,
						<br><br>Vaš Jobstep Team
						<br><br>
						<img src="cid:logo_2u">
		';
	}elseif($jezik == "sr"){
		$mail->Body    = 'Poštovani,
						<br><br>Hvala Vam što ste nam ukazali svoje poverenje i što zajedno ulazimo u proces nostrifikacije/evaluacije Vaše diplome.
						<br><br>Klikom na link možete pogledati ugovor: <a href="'.$ugovor_link.'">LINK</a>
						<br>Molimo Vas da:
						
						<br><br>	- Nakon otvaranja ugovora, pritisnite dugme "Prihvati ugovor" kako bi načinili prvi korak ka nostrifikaciji/evaluaciji Vaše diplome.
						<br><br>	- Uplatu novčanog iznosa vršite po osnovu ispostavljenog predračuna (predračun nije neophodno štampati, punovažan je i u elektronskom obliku).
						<br><br>	- Primer uplatnice također možete preuzeti klikom na dugme "Uplatnica".

						<br><br>Za sve dodatne informacije i pitanja budite slobodni da nas kontaktirate. Stojimo Vam na raspolaganju i radujemo se uspešnoj saradnji.
						
						<br><br>S poštovanjem,
						<br><br>Vaš Jobstep Team
						<br><br>
						<img src="cid:logo_2u">
		';
	}else{
		$mail->Body    = 'Sehr geehrte Damen und Herren,
						<br><br>Vielen Dank, dass Sie uns Ihr Vertrauen schenken und gemeinsam in das Anerkennungsverfahren Ihres Diploms eintreten.
						<br><br>Durch Anklicken des Links können Sie den Vertrag einsehen: <a href="'.$ugovor_link.'">LINK</a>
						<br>Bitte:
						
						<br><br>	- Nach Vertragseröffnung  "Vertrag akzeptieren" anklicken, um den ersten Schritt zur Anerkennung  Ihres Diploms zu machen
						<br><br>	- Sie bezahlen den Geldbetrag anhand der ausgestellten Vorrechnung (die Vorrechnung muss nicht ausgedruckt werden, sie ist auch in elektronischer Form gültig).
						<br><br>	- Sie können auch ein Beispiel für einen Einzahlungsschein herunterladen, indem Sie auf  "Einzahlungsschein" klicken.

						<br><br>Für weitere Informationen und Fragen können Sie uns gerne kontaktieren. Wir stehen Ihnen gerne zur Verfügung und freuen uns auf eine erfolgreiche Zusammenarbeit.
						
						<br><br>Hochachtungsvoll,
						<br><br>Ihr Jobstep-Team
						<br><br>
						<img src="cid:logo_2u">
		';
	}
	
	$mail->AddEmbeddedImage($signatura, 'logo_2u');
	
	$mail->AltBody = "ALT";

	if(!$mail->send()) {
		echo '-2 Message could not be sent. ';
		echo 'Mailer Error: ' . $mail->ErrorInfo;
		echo "<br/>";
		$razlog = $mail->ErrorInfo;
		
		$array .= '"'.$id.'"=>"2",';
		
		//ADD TO LOGS START
		$log_date = date('Y-m-d H:i:s');
		$log_desc = "DIPL -> Nije poslan predracun = [".$kandidat_id."] . Razlog: ".$razlog;
		$log_query = $db->prepare("
						INSERT INTO idk_logs
							(log_employeeid, log_desc, log_date)
						VALUES
							(:log_employeeid, :log_desc, :log_date)");

		$log_query->execute(array(
						':log_employeeid' => $logged_employee_id,
						':log_desc' => $log_desc,
						':log_date' => $log_date));
		//ADD TO LOGS END
	}else{
		echo '-1 Mail sent';
		echo 'Mailer Error: ' . $mail->ErrorInfo;
		echo "<br/>";
		$razlog = $mail->ErrorInfo;
		
		//$array .= '"'.$id.'"=>"1",';
		
		//ADD TO LOGS START
		$log_date = date('Y-m-d H:i:s');
		$log_desc = "DIPL -> Poslan predracun = [".$kandidat_id."] ";
		$log_query = $db->prepare("
						INSERT INTO idk_logs
							(log_employeeid, log_desc, log_date)
						VALUES
							(:log_employeeid, :log_desc, :log_date)");

		$log_query->execute(array(
						':log_employeeid' => $logged_employee_id,
						':log_desc' => $log_desc,
						':log_date' => $log_date));
		//ADD TO LOGS END
	}
}
//FUNKCIJA KOJA SLUŽI ZA SLANJE LINKA UGOVORA PREKO MAILA KAD SE: 
// - PRODA UGOVOR, URADI PROMJENA UGOVORA, 
// - GENERIŠE PREDRAČUN PRVE RATE (prihvati ugovor)
// - GENERIŠE PREDRAČUN n-TE RATE (cronjob, ručno generisanje predračuna)
// 																					END

function getEnableDateInputButtonR($kandidatId, $status, $podstatus){

	Global $db; 

	$kandidatId = intval($kandidatId); 
	$status = intval($status); 
	$podstatus = intval($podstatus);
	
	$flagStatus = 0; 

	if ($status == 2 AND $podstatus == 1) {
		$flagStatus = 1;
	} else if ($status == 2 AND $podstatus == 2) {
		$flagStatus = 2;
	} else if ($status == 2 AND $podstatus == 4) {
		$flagStatus = 3;
	} else if ($status == 2 AND $podstatus == 3) {
		$flagStatus = 4;
	} else if ($status == 2 AND $podstatus == 5) {
		$flagStatus = 5;
	} else if ($status == 2 AND $podstatus == 6) {
		$flagStatus = 6;
	} else if ($status == 2 AND $podstatus == 7) {
		$flagStatus = 7;
	} else if ($status == 3 AND $podstatus == 1) {
		$flagStatus = 8;
	} else if ($status == 3 AND $podstatus == 2) {
		$flagStatus = 9;
	} else if ($status == 4 AND $podstatus == 1) {
		$flagStatus = 10;
	} else if ($status == 4 AND $podstatus == 2) {
		$flagStatus = 11;
	} else if ($status == 5) {
		$flagStatus = 12;
	} else if ($status == 6) {
		$flagStatus = 13;
	} else {
		$flagStatus = 0; 
	}

	$result = array(
		"poslan_spisak_datum" => 0,
		"dokumentacija_kompletna_datum" => 0,
		"na_prevodu_datum" => 0,
		"prevod_zavrsen_datum" => 0,
		"poslana_posta_datum" => 0,
		"zaprimljena_dokumentacija_datum" => 0,
		"stigla_taksa_dopuna_datum" => 0,
		"placena_taksa_poslana_dopuna_datum" => 0,
		"zavrsen_datum" => 0
	);

	$query = $db->prepare("
		SELECT
			poslan_spisak_datum,
			dokumentacija_kompletna_datum,
			na_prevodu_datum, 
			prevod_zavrsen_datum,
			poslana_posta_datum,
			zaprimljena_dokumentacija_datum, 
			stigla_taksa_dopuna_datum, 
			placena_taksa_poslana_dopuna_datum, 
			zavrsen_datum
		FROM 
			idk_nd_kandidata
		WHERE 
			id_broj_nd_kandidata = :kandidat_id
	");
	$query->execute(array(
		":kandidat_id" => $kandidatId
	));

	$row = $query->fetch();

	$poslan_spisak_datum 						= $row["poslan_spisak_datum"];
	$dokumentacija_kompletna_datum 				= $row["dokumentacija_kompletna_datum"];
	$na_prevodu_datum 							= $row["na_prevodu_datum"];
	$prevod_zavrsen_datum 						= $row["prevod_zavrsen_datum"];
	$poslana_posta_datum 						= $row["poslana_posta_datum"];
	$zaprimljena_dokumentacija_datum 			= $row["zaprimljena_dokumentacija_datum"];
	$stigla_taksa_dopuna_datum 					= $row["stigla_taksa_dopuna_datum"];
	$placena_taksa_poslana_dopuna_datum 		= $row["placena_taksa_poslana_dopuna_datum"];
	$zavrsen_datum	 							= $row["zavrsen_datum"];

	if($status == 2 AND $podstatus == 1){
		$result["poslan_spisak_datum"] = 1;
	} else {
		if($poslan_spisak_datum == null AND $flagStatus > 1) {
			$result["poslan_spisak_datum"] = 2;
		}
	}
	
	if($status == 2 AND $podstatus == 3){
		$result["na_prevodu_datum"] = 1;
	} else {
		if($na_prevodu_datum == null AND $flagStatus > 4) {
			$result["na_prevodu_datum"] = 2;
		}
	}
	
	if($status == 2 AND $podstatus == 4){
		$result["dokumentacija_kompletna_datum"] = 1;
	} else {
		if($dokumentacija_kompletna_datum == null AND $flagStatus > 3) {
			$result["dokumentacija_kompletna_datum"] = 2;
		}
	}
	
	if($status == 2 AND $podstatus == 5){
		$result["prevod_zavrsen_datum"] = 1;
	} else {
		if($prevod_zavrsen_datum == null AND $flagStatus > 5) {
			$result["prevod_zavrsen_datum"] = 2;
		}
	}
	
	if($status == 3 AND $podstatus == 1){
		$result["poslana_posta_datum"] = 1;
	} else {
		if($poslana_posta_datum == null AND $flagStatus > 8) {
			$result["poslana_posta_datum"] = 2;
		}
	}
	
	if($status == 3 AND $podstatus == 2){
		$result["zaprimljena_dokumentacija_datum"] = 1;
	} else {
		if($zaprimljena_dokumentacija_datum == null AND $flagStatus > 9) {
			$result["zaprimljena_dokumentacija_datum"] = 2;
		}
	}
	
	if($status == 4 AND $podstatus == 2){
		$result["stigla_taksa_dopuna_datum"] = 1;
	} else {
		if($stigla_taksa_dopuna_datum == null AND $flagStatus > 11) {
			$result["stigla_taksa_dopuna_datum"] = 2;
		}
	}
	
	if($status == 5){
		$result["placena_taksa_poslana_dopuna_datum"] = 1;
	} else {
		if($placena_taksa_poslana_dopuna_datum == null AND $flagStatus > 12) {
			$result["placena_taksa_poslana_dopuna_datum"] = 2;
		}
	}

	if($status == 6){
		if($zavrsen_datum == null) {
			$result["zavrsen_datum"] = 1;
		}
	} else {
		if($zavrsen_datum == null AND $flagStatus == 13) {
			$result["zavrsen_datum"] = 2;
		}
	}

	return $result;
}

function getVrstaUgovoraDiplR($vrsta){
	//Kad se dodaje nova vrsta ugovora obavezno popuniti ovaj ispis
	$vrsta = intval($vrsta);
	$rezultat = "";
	if($vrsta == 1){
		$rezultat = 'Ugovor bez popusta na 2 rate!';
	} else if($vrsta == 2){
		$rezultat = 'Ugovor sa popustom na 2 rate!';
	}else if($vrsta == 3){
		$rezultat = 'Ugovor bez popusta na 5 rata!';
	}else if($vrsta == 4){
		$rezultat = 'Ugovor sa popustom na 5 rata!';
	}else if($vrsta == 5){
		$rezultat = 'Ugovor bez popusta na 3 rate!';
	}else if($vrsta == 6){
		$rezultat = 'Ugovor sa popustom na 3 rate!';
	}else if($vrsta == 7){
		$rezultat = 'Ugovor bez popusta na 4 rate!';
	}else if($vrsta == 8){ 
		$rezultat = 'Ugovor sa popustom na 4 rate!';
	}else if($vrsta == 9){ 
		$rezultat = 'Ugovor bez popusta na 1 ratu!';
	}else if($vrsta == 10){ 
		$rezultat = 'Ugovor sa popustom na 1 ratu!';
	}else if($vrsta == 11){
		$rezultat = 'Mikrofin ugovor bez popusta!';
	}else if($vrsta == 12){
		$rezultat = 'Mikrofin ugovor sa popustom!';
	}else if($vrsta == 21){
		$rezultat = 'Ugovor sa popustom 20% na 1 ratu!';
	}else if($vrsta == 22){ 
		$rezultat = 'Ugovor sa popustom 20% na 2 rate!';
	}else if($vrsta == 23){ 
		$rezultat = 'Ugovor sa popustom 20% na 3 rate!';
	}else if($vrsta == 24){ 
		$rezultat = 'Ugovor sa popustom 20% na 4 rate!';
	}else if($vrsta == 25){ 
		$rezultat = 'Ugovor sa popustom 20% na 5 rata!';
	}else if($vrsta == 71){ 
		$rezultat = 'Ugovor za struke na 1 ratu sa 30% popusta!';
	}else if($vrsta == 72){ 
		$rezultat = 'Ugovor za struke na 2 rate sa 30% popusta!';
	}else if($vrsta == 73){ 
		$rezultat = 'Ugovor za struke na 3 rate sa 30% popusta!';
	}else if($vrsta == 74){ 
		$rezultat = 'Ugovor za struke na 4 rate sa 30% popusta!';
	}else if($vrsta == 75){ 
		$rezultat = 'Ugovor za struke na 5 rata sa 30% popusta!';
	}else if($vrsta == 51){ 
		$rezultat = 'Ugovor za struke na 1 ratu sa 50% popusta!';
	}else if($vrsta == 52){ 
		$rezultat = 'Ugovor za struke na 2 rate sa 50% popusta!';
	}else if($vrsta == 53){ 
		$rezultat = 'Ugovor za struke na 3 rate sa 50% popusta!';
	}else if($vrsta == 54){ 
		$rezultat = 'Ugovor za struke na 4 rate sa 50% popusta!';
	}else if($vrsta == 55){ 
		$rezultat = 'Ugovor za struke na 5 rata sa 50% popusta!';
	}else if($vrsta == 99){ 
		$rezultat = 'Ugovor za struke na 1 ratu sa 100% popusta!';
	}else if($vrsta == 61){ 
		$rezultat = 'Ugovor za struke na 1 ratu sa 20% popusta!';
	}else if($vrsta == 62){ 
		$rezultat = 'Ugovor za struke na 2 rate sa 20% popusta!';
	}else if($vrsta == 63){ 
		$rezultat = 'Ugovor za struke na 3 rate sa 20% popusta!';
	}else if($vrsta == 64){ 
		$rezultat = 'Ugovor za struke na 4 rate sa 20% popusta!';
	}else if($vrsta == 65){ 
		$rezultat = 'Ugovor za struke na 5 rata sa 20% popusta!';
	}else if($vrsta == 41){ 
		$rezultat = 'Ugovor za struke na 1 ratu sa 70% popusta!';
	}else if($vrsta == 42){ 
		$rezultat = 'Ugovor za struke na 2 rate sa 70% popusta!';
	}else if($vrsta == 43){ 
		$rezultat = 'Ugovor za struke na 3 rate sa 70% popusta!';
	}else if($vrsta == 44){ 
		$rezultat = 'Ugovor za struke na 4 rate sa 70% popusta!';
	}else if($vrsta == 45){ 
		$rezultat = 'Ugovor za struke na 5 rata sa 70% popusta!';
	}else if($vrsta == 82){ 
		$rezultat = 'Ugovor sa popustom 10% na 2 rate!';
	}else if($vrsta == 83){ 
		$rezultat = 'Ugovor sa popustom 10% na 3 rate!';
	}else if($vrsta == 84){ 
		$rezultat = 'Ugovor sa popustom 10% na 4 rate!';
	}else if($vrsta == 85){ 
		$rezultat = 'Ugovor sa popustom 10% na 5 rata!';
	}else{
	   $rezultat = "Nepoznata vrijednost ugovora";
	}
	return  $rezultat;
}

function sendMailUgovorPredracun($idKandidat, $poruka_text_mail, $ugovor_link, $subject_ugovor, $nacin_naplate = 1){
	//fja za slanje ugovora i predracuna
	Global $db;
	Global $logged_employee_id;
	
	// GET CANDIDATE EMAIL
	$querySendMail = $db->prepare("
					SELECT email_nd_kandidata
					FROM idk_nd_kandidata
					WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata
					");

	$querySendMail->execute(array(
		":id_broj_nd_kandidata" => $idKandidat
	));

	$rowMail = $querySendMail->fetch();
	$kandidat_email = $rowMail['email_nd_kandidata'];
	
	$jezik = substr($ugovor_link,-2);
	
	if ($nacin_naplate == 2){
		$signatura = $_SERVER['DOCUMENT_ROOT']."/files/ugovori_uplatnice_dipl/signatura-SRB-automatski-mail-new.gif"; // ADissssss
	} else {
		$signatura = $_SERVER['DOCUMENT_ROOT']."/files/ugovori_uplatnice_dipl/nostrifikacija_CH.gif";
	}
	$mail = new PHPMailer;

	$mail->isSMTP();											// Set mailer to use SMTP

	$mail->Host = 'smtp.gmail.com';                 // Specify main and backup SMTP servers
	$mail->SMTPAuth = true;                         // Enable SMTP authentication
	$mail->Username = 'support@job-step.com';        // SMTP username
	$mail->Password = 'eooc nnxo aylp lqkh';          // SMTP password
	$mail->SMTPSecure = 'ssl';                      // Enable TLS encryption, `ssl` also accepted
	$mail->Port = 465;                              // TCP port to connect to
	$mail->CharSet = 'UTF-8';

	//Recipients
	$mail->setFrom('support@job-step.com', 'JobStep');
	
	$mail->addAddress($kandidat_email);		// Add a recipient

	if($jezik == "bs"){
		$subject ="Nostrifikacija: ".$subject_ugovor."Predračun";
		$postovani = "Poštovani";
		$pozdrav = "<br><br>S poštovanjem,
					<br><br>Vaš Jobstep Team<br><br>";
	}elseif($jezik == "sr"){
		$subject ="Nostrifikacija/Evaluacija: ".$subject_ugovor."Predračun";
		$postovani = "Poštovani";
		$pozdrav = "<br><br>S poštovanjem,
					<br><br>Vaš Jobstep Team<br><br>";
	}else{
		$subject = "Anerkennungsverfahren: ".$subject_ugovor."Vorrechnung";
		$postovani = "Sehr geehrte Dammen und Herren";
		$pozdrav = "<br><br>Hochachtungsvoll,
					<br><br>Ihr Jobstep-Team<br><br>";
	}
	$mail->Subject = $subject;
	$mail->Body    = $postovani.',<br><br>'.$poruka_text_mail.$pozdrav.'
					<img src="cid:logo_2u">
	';


	$mail->AddEmbeddedImage($signatura, 'logo_2u');

	$mail->AltBody = "ALT";

	if(!$mail->send()) {
		// echo '-2 Message could not be sent. ';
		// echo 'Mailer Error: ' . $mail->ErrorInfo;
		// echo "<br/>";
		$razlog = $mail->ErrorInfo;
		$array .= '"'.$id.'"=>"2",';
		
		//ADD TO LOGS START
		$log_date = date('Y-m-d H:i:s');
		$log_desc = "DIPL -> Nije poslan predracun = [".$idKandidat."] . Razlog: ".$razlog;
		$log_query = $db->prepare("
						INSERT INTO idk_logs
							(log_employeeid, log_desc, log_date)
						VALUES
							(:log_employeeid, :log_desc, :log_date)");

		$log_query->execute(array(
						':log_employeeid' => $logged_employee_id,
						':log_desc' => $log_desc,
						':log_date' => $log_date));
		//ADD TO LOGS END
	}else{
		// echo '-1 Mail sent';
		// echo 'Mailer Error: ' . $mail->ErrorInfo;
		// echo "<br/>";
		$razlog = $mail->ErrorInfo;
		
		//$array .= '"'.$id.'"=>"1",';
		
		//ADD TO LOGS START
		$log_date = date('Y-m-d H:i:s');
		$log_desc = "DIPL -> Poslan predracun = [".$idKandidat."] ";
		$log_query = $db->prepare("
						INSERT INTO idk_logs
							(log_employeeid, log_desc, log_date)
						VALUES
							(:log_employeeid, :log_desc, :log_date)");

		$log_query->execute(array(
						':log_employeeid' => $logged_employee_id,
						':log_desc' => $log_desc,
						':log_date' => $log_date));
		//ADD TO LOGS END

	}
}
function vracanjePredracunaStorn($racunId, $racunRazlogStornacije){
	
	Global $db;
	Global $logged_employee_id;
	
	if($racunId != 0 AND $racunRazlogStornacije != 0){
		
		//Na osnovu racunId kupim određene podatke iz racuna - kao što je kandidat id i slično (navedeno u query)
		$queryRacunInfo = $db->prepare("
			SELECT 
				rac.predracun_id, 
				rac.racun_broj,
				rac.racun_kandidat_id,
				pred.pr_rata,
				pred.pr_datum_kreiranja,
				DATEDIFF(CURDATE(), DATE_FORMAT(pred.pr_datum_uplate, '%Y-%m-%d')) AS brojDanaOdKreiranja,
				pred.pr_zaposlenik
			FROM 
				idk_racuni rac
			INNER JOIN 
				idk_predracuni pred
			ON 
				rac.predracun_id = pred.pr_id
			WHERE 
				rac.racun_id = :racun_id
				AND 
				pred.pr_vrsta_predracuna = 1
				AND 
				pred.pr_uplaceno = 1
				AND 
				pred.pr_status = 2
		");
		$queryRacunInfo->execute(array(
			':racun_id' => $racunId
		));
	
		if($queryRacunInfo->rowCount()){
			
			$rowRacunInfo = $queryRacunInfo->fetch();
			$predracunId = intval($rowRacunInfo["predracun_id"]);
			$racunBroj = $rowRacunInfo["racun_broj"];
			$kandidatId = intval($rowRacunInfo["racun_kandidat_id"]);
			$predracunRata = intval($rowRacunInfo["pr_rata"]);
			$predracunDatumKreiranja = $rowRacunInfo["pr_datum_kreiranja"];
			$predracunStarDana = intval($rowRacunInfo["brojDanaOdKreiranja"]);
			$predracunZaposlenik = intval($rowRacunInfo["pr_zaposlenik"]);
			
			$kandidatStatus = getStatusValueDIPLKandidatR($kandidatId); //funkcija koja vraca brojcanu vrijednost statusa i pristupa joj se na nacin $kandidatStatus["status"] $kandidatStatus["podstatus"]
			
		}
		switch($racunRazlogStornacije){
			case 1:
				
				//Status kandidata vratiti na u Obradi Lead, ne brisati log statusa, vec dodati novi da se vratio na "U obradi Lead"
				if($kandidatStatus["status"] == 2 AND $predracunRata == 1){
					//echo "<br>".$kandidatStatus["status"]." ".$kandidatStatus["podstatus"];
					
					//Radim update statusa na prethodno - odnosno U obradi Lead
					$updateStatus = $db->prepare("
						UPDATE 
							idk_nd_kandidata
						SET
							status_nd_kandidata = :status_nd_kandidata,
							pstatus_nd_kandidata = :pstatus_nd_kandidata
						WHERE 
							id_broj_nd_kandidata = :id_broj_nd_kandidata
					");
					$updateStatus->execute(array(
						':status_nd_kandidata' => 1,
						':pstatus_nd_kandidata' => 5,
						':id_broj_nd_kandidata' => $kandidatId
					));
					
					$vrijeme_stari_status = $db->prepare("
						SELECT id_log_status_nd_kandidata, vrijeme_promjene_statusa_nd_kandidata
						FROM idk_nd_kandidata_status_log
						WHERE idd_broj_nd_kandidata = :idd_broj_nd_kandidata AND broj_dana_statusa_nd_kandidata is null
					");
					$vrijeme_stari_status->execute(array(
						':idd_broj_nd_kandidata' => $kandidatId
					));
					$vrijeme_stari_status_row = $vrijeme_stari_status->fetch();
					$id_log_statusa = $vrijeme_stari_status_row['id_log_status_nd_kandidata'];
					$vrijeme_log_statusa = $vrijeme_stari_status_row['vrijeme_promjene_statusa_nd_kandidata'];
					
					$trenutno_vrijeme_statusne_promjene = date('Y-m-d H:i:s');
					
					$diff_novi = strtotime($trenutno_vrijeme_statusne_promjene) - strtotime($vrijeme_log_statusa);
					$day_novi = floor($diff_novi/86400);
					
					$update_vrijeme_stari = $db->prepare("
									UPDATE idk_nd_kandidata_status_log
									SET broj_dana_statusa_nd_kandidata = :broj_dana_statusa_nd_kandidata
									WHERE idd_broj_nd_kandidata = :idd_broj_nd_kandidata AND id_log_status_nd_kandidata = :id_log_status_nd_kandidata
					");
					
					$update_vrijeme_stari->execute(array(
									':idd_broj_nd_kandidata' => $kandidatId,
									':id_log_status_nd_kandidata' => $id_log_statusa,
									':broj_dana_statusa_nd_kandidata' => $day_novi
					));
					$new_ND_status = $db->prepare("
									INSERT INTO idk_nd_kandidata_status_log
									(
										idd_broj_nd_kandidata,
										status_nd_kandidata,
										pstatus_nd_kandidata,
										vrijeme_promjene_statusa_nd_kandidata,
										promjenio_zaposlenik_nd_kandidata
									)
									VALUES
									(
										:idd_broj_nd_kandidata,
										:status_nd_kandidata,
										:pstatus_nd_kandidata,
										:vrijeme_promjene_statusa_nd_kandidata,
										:promjenio_zaposlenik_nd_kandidata
									)
					");
					
					$new_ND_status->execute(array(
									':idd_broj_nd_kandidata' => $kandidatId,
									':status_nd_kandidata' => 1,
									':pstatus_nd_kandidata' => 5,
									':vrijeme_promjene_statusa_nd_kandidata' => date('Y-m-d H:i:s'),
									':promjenio_zaposlenik_nd_kandidata' => $logged_employee_id
					));

					updateProjekcijeKandidat(1, $kandidatId);
					
				}
				
				//Kod update predracuna za brojDanaOdKreiranja raditi date_diff na datumu uplate i datumu kreiranja jer curent date moze biti puno kasniji 
				// i onda predracun moze dobiti status na kojem nije bio
				$vratiNaStatus = 1;
				// if($predracunStarDana < 21){
					// $vratiNaStatus = 1;
				// }else if($predracunStarDana >= 21 AND $predracunStarDana < 40){
					// $vratiNaStatus = 3;
				// }else if($predracunStarDana >= 40 AND $predracunStarDana < 90){
					// $vratiNaStatus = 4;
				// }else{
					// $vratiNaStatus = 5;
				// }
				// echo " ".$vratiNaStatus;
				
				$updatePredracun = $db->prepare("
					UPDATE
						idk_predracuni
					SET 
						pr_status = :pr_status,
						pr_uplaceno = 0,
						pr_datum_uplate = NULL, 
						pr_izdan_racun = 0,
						pr_file = NULL,
						pr_file_de = NULL,
						pr_stornirano = 1
					WHERE 
						pr_id = :pr_id
				");
				$updatePredracun->execute(array(
					':pr_status' => $vratiNaStatus,
					':pr_id' => $predracunId
				));
				
				$deleteRateUplate = $db->prepare("
					DELETE FROM
						idk_nd_rate
					WHERE 
						id_nd_kan = :id_nd_kan
						AND 
						br_r = :br_r
				");
				$deleteRateUplate->execute(array(
					':id_nd_kan' => $kandidatId,
					':br_r' => $predracunRata
				));
				
				//Nakon sto se sve izvrsilo - agenta treba obavijestiti putem maila da se desila stornacija racuna zbog pogresnih podataka i da je kandidatu moguce izvrsiti promjenu ugovora
				$zaposleniciObavijestNizExp = array(69,67); //U slucaju da treba jos nekom poslati mail - ovdje samo dodati id-eve - NPR: array(67,75)
				array_push($zaposleniciObavijestNizExp, $predracunZaposlenik);
				$zaposleniciObavijestNizImp = implode(",", $zaposleniciObavijestNizExp);
				if(count($zaposleniciObavijestNizExp) != 0){
					foreach($zaposleniciObavijestNizExp AS $valueZaposlenici){
						$user_query = $db->prepare("
							SELECT employee_firstname, employee_lastname, employee_email
							FROM idk_employees
							WHERE employee_id = :employee_id AND employee_status NOT LIKE '0'
						");

						$user_query->execute(array(
							':employee_id' => $valueZaposlenici
						));
						if ($user_query->rowCount() > 0) {
							$user = $user_query->fetch();

							$employee_firstname = $user['employee_firstname'];
							$employee_lastname = $user['employee_lastname'];
							$employee_email = $user['employee_email'];

							//Send email to user
							$mail_email = $employee_email;
							$mail_name = $employee_firstname . ' ' . $employee_lastname;
							$mail_subject = "Stornacija računa kandidata #".$kandidatId."";
							$mail_url = "" . getSiteUrlr() . "/nostrifikacija_diploma?page=otvori_ND_kandidata&id=".$kandidatId."";
							$mail_url = '<a href = "'.$mail_url.'" >LINK</a>';
							$mail_body = "
								<p>
								Poštovani, <br><br>
								Za kandidata čiji profil možete pogledati na ".$mail_url."-u, izvršena je stornacija računa zbog pogrešno unešenih podataka.
								Ovim putem Vas molimo da ispravite pogrešne informacije i izvršite promjenu ugovora kako bi podaci u dokumentima bili ispravni. 
								</p>
							";
							$mail_altbody = "
								<p>
								Poštovani, <br><br>
								Za kandidata čiji profil možete pogledati na ".$mail_url."-u, izvršena je stornacija računa zbog pogrešno unešenih podataka.
								Ovim putem Vas molimo da ispravite pogrešne informacije i izvršite promjenu ugovora kako bi podaci u dokumentima bili ispravni. 
								</p>
							";
							
							sendEmail($mail_email, $mail_name, $mail_subject, $mail_body, $mail_altbody);
						}
					}
				}
				
			break;
			case 2:
				//Status kandidata vratiti na u Obradi Lead, ne brisati log statusa, vec dodati novi da se vratio na "U obradi Lead"
				if($kandidatStatus["status"] == 2 AND $predracunRata == 1){
					$updateStatus = $db->prepare("
						UPDATE 
							idk_nd_kandidata
						SET
							status_nd_kandidata = :status_nd_kandidata,
							pstatus_nd_kandidata = :pstatus_nd_kandidata
						WHERE 
							id_broj_nd_kandidata = :id_broj_nd_kandidata
					");
					$updateStatus->execute(array(
						':status_nd_kandidata' => 1,
						':pstatus_nd_kandidata' => 5,
						':id_broj_nd_kandidata' => $kandidatId
					));
					
					$vrijeme_stari_status = $db->prepare("
						SELECT id_log_status_nd_kandidata, vrijeme_promjene_statusa_nd_kandidata
						FROM idk_nd_kandidata_status_log
						WHERE idd_broj_nd_kandidata = :idd_broj_nd_kandidata AND broj_dana_statusa_nd_kandidata is null
					");
					$vrijeme_stari_status->execute(array(
						':idd_broj_nd_kandidata' => $kandidatId
					));
					$vrijeme_stari_status_row = $vrijeme_stari_status->fetch();
					$id_log_statusa = $vrijeme_stari_status_row['id_log_status_nd_kandidata'];
					$vrijeme_log_statusa = $vrijeme_stari_status_row['vrijeme_promjene_statusa_nd_kandidata'];
					
					$trenutno_vrijeme_statusne_promjene = date('Y-m-d H:i:s');
					
					$diff_novi = strtotime($trenutno_vrijeme_statusne_promjene) - strtotime($vrijeme_log_statusa);
					$day_novi = floor($diff_novi/86400);
					
					$update_vrijeme_stari = $db->prepare("
									UPDATE idk_nd_kandidata_status_log
									SET broj_dana_statusa_nd_kandidata = :broj_dana_statusa_nd_kandidata
									WHERE idd_broj_nd_kandidata = :idd_broj_nd_kandidata AND id_log_status_nd_kandidata = :id_log_status_nd_kandidata
					");
					
					$update_vrijeme_stari->execute(array(
									':idd_broj_nd_kandidata' => $kandidatId,
									':id_log_status_nd_kandidata' => $id_log_statusa,
									':broj_dana_statusa_nd_kandidata' => $day_novi
					));
					$new_ND_status = $db->prepare("
									INSERT INTO idk_nd_kandidata_status_log
									(
										idd_broj_nd_kandidata,
										status_nd_kandidata,
										pstatus_nd_kandidata,
										vrijeme_promjene_statusa_nd_kandidata,
										promjenio_zaposlenik_nd_kandidata
									)
									VALUES
									(
										:idd_broj_nd_kandidata,
										:status_nd_kandidata,
										:pstatus_nd_kandidata,
										:vrijeme_promjene_statusa_nd_kandidata,
										:promjenio_zaposlenik_nd_kandidata
									)
					");
					
					$new_ND_status->execute(array(
									':idd_broj_nd_kandidata' => $kandidatId,
									':status_nd_kandidata' => 1,
									':pstatus_nd_kandidata' => 5,
									':vrijeme_promjene_statusa_nd_kandidata' => date('Y-m-d H:i:s'),
									':promjenio_zaposlenik_nd_kandidata' => $logged_employee_id
					));

					//U tabeli obračuna staviti da je predračun neuplaćen samo ako je status obračuna neisplaćen
					//Ako se već desila isplata nije moguće vracati programski 
					//Update obracuna za stari predracun
					$update_obracune = $db->prepare("
						UPDATE idk_obracuni
						SET status_predracuna = :status_predracuna
						WHERE predracun_id = :predracun_id AND status_obracuna = 0
					");
					$update_obracune->execute(array(
						':status_predracuna' => 0,
						':predracun_id' => $pr_id
					));

					//Potrebno je izbrisati obracun za agenta jer se on kreira pri uplati predracuna
					$delete_obracun = $db->prepare("
						DELETE FROM idk_obracuni
						WHERE predracun_id = :predracun_id AND kategorija_provizije = 1 AND status_obracuna = 0
					");
					$delete_obracun->execute(array(
						':predracun_id' => $pr_id
					));

					updateProjekcijeKandidat(1, $kandidatId);
				}
				//Kod update predracuna za brojDanaOdKreiranja raditi date_diff na datumu uplate i datumu kreiranja jer curent date moze biti puno kasniji 
				// i onda predracun moze dobiti status na kojem nije bio
				if($predracunStarDana < 21){
					$vratiNaStatus = 1;
				}else if($predracunStarDana >= 21 AND $predracunStarDana < 40){
					$vratiNaStatus = 3;
				}else if($predracunStarDana >= 40 AND $predracunStarDana < 90){
					$vratiNaStatus = 4;
				}else{
					$vratiNaStatus = 5;
				}
				
				$updatePredracun = $db->prepare("
					UPDATE
						idk_predracuni
					SET 
						pr_status = :pr_status,
						pr_uplaceno = 0,
						pr_datum_uplate = NULL, 
						pr_izdan_racun = 0,
						pr_stornirano = 1
					WHERE 
						pr_id = :pr_id
				");
				$updatePredracun->execute(array(
					':pr_status' => $vratiNaStatus,
					':pr_id' => $predracunId
				));
				
				$deleteRateUplate = $db->prepare("
					DELETE FROM
						idk_nd_rate
					WHERE 
						id_nd_kan = :id_nd_kan
						AND 
						br_r = :br_r
				");
				$deleteRateUplate->execute(array(
					':id_nd_kan' => $kandidatId,
					':br_r' => $predracunRata
				));
			break;
			case 3:
				
				//Status kandidata vratiti na u Obradi Lead, ne brisati log statusa, vec dodati novi da se vratio na "U obradi Lead"
				if($kandidatStatus["status"] == 2 AND $predracunRata == 1){
					//echo "<br>".$kandidatStatus["status"]." ".$kandidatStatus["podstatus"];
					
					//Radim update statusa na prethodno - odnosno U obradi Lead
					$updateStatus = $db->prepare("
						UPDATE 
							idk_nd_kandidata
						SET
							status_nd_kandidata = :status_nd_kandidata
						WHERE 
							id_broj_nd_kandidata = :id_broj_nd_kandidata
					");
					$updateStatus->execute(array(
						':status_nd_kandidata' => 7,
						':id_broj_nd_kandidata' => $kandidatId
					));
					
					//INSERT BILJESKE RADI SPREMANJA RAZLOGA ARHIVIRANJA
					$sadrzajKomunikacije = "Stornacija računa, povrat sredstava.";
					$insertKomunikacija = $db->prepare("
						INSERT INTO idk_nd_kandidata_biljeske
							(
								id_kandidata_biljeska_nd, 
								status_biljeska_nd, 
								tip_biljeska_nd, 
								razlog_biljeska_nd, 
								sadrzaj_biljeska_nd, 
								vrijeme_dodavanja_biljeska_nd, 
								vrijeme_grupa_biljeska_nd, 
								dodao_zaposlenik_biljeska_nd, 
								vrijeme_ponovnog_zvanja
							)
						VALUES
							(
								:id_kandidata_biljeska_nd, 
								:status_biljeska_nd, 
								:tip_biljeska_nd, 
								:razlog_biljeska_nd, 
								:sadrzaj_biljeska_nd, 
								:vrijeme_dodavanja_biljeska_nd, 
								:vrijeme_grupa_biljeska_nd, 
								:dodao_zaposlenik_biljeska_nd, 
								:vrijeme_ponovnog_zvanja
							)
					");

					$insertKomunikacija->execute(array(
						':id_kandidata_biljeska_nd' => $kandidatId,
						':status_biljeska_nd' => 2,
						':sadrzaj_biljeska_nd' => $sadrzajKomunikacije,
						':tip_biljeska_nd' => 15,
						':razlog_biljeska_nd' => 392,
						':vrijeme_dodavanja_biljeska_nd' => date("Y-m-d H:i:s"),
						':vrijeme_grupa_biljeska_nd' => date("Y"),
						':dodao_zaposlenik_biljeska_nd' => $logged_employee_id,
						':vrijeme_ponovnog_zvanja' => NULL
					));
					
					$vrijeme_stari_status = $db->prepare("
						SELECT id_log_status_nd_kandidata, vrijeme_promjene_statusa_nd_kandidata
						FROM idk_nd_kandidata_status_log
						WHERE idd_broj_nd_kandidata = :idd_broj_nd_kandidata AND broj_dana_statusa_nd_kandidata is null
					");
					$vrijeme_stari_status->execute(array(
						':idd_broj_nd_kandidata' => $kandidatId
					));
					$vrijeme_stari_status_row = $vrijeme_stari_status->fetch();
					$id_log_statusa = $vrijeme_stari_status_row['id_log_status_nd_kandidata'];
					$vrijeme_log_statusa = $vrijeme_stari_status_row['vrijeme_promjene_statusa_nd_kandidata'];
					
					$trenutno_vrijeme_statusne_promjene = date('Y-m-d H:i:s');
					
					$diff_novi = strtotime($trenutno_vrijeme_statusne_promjene) - strtotime($vrijeme_log_statusa);
					$day_novi = floor($diff_novi/86400);
					
					$update_vrijeme_stari = $db->prepare("
									UPDATE idk_nd_kandidata_status_log
									SET broj_dana_statusa_nd_kandidata = :broj_dana_statusa_nd_kandidata
									WHERE idd_broj_nd_kandidata = :idd_broj_nd_kandidata AND id_log_status_nd_kandidata = :id_log_status_nd_kandidata
					");
					
					$update_vrijeme_stari->execute(array(
									':idd_broj_nd_kandidata' => $kandidatId,
									':id_log_status_nd_kandidata' => $id_log_statusa,
									':broj_dana_statusa_nd_kandidata' => $day_novi
					));
					$new_ND_status = $db->prepare("
									INSERT INTO idk_nd_kandidata_status_log
									(
										idd_broj_nd_kandidata,
										status_nd_kandidata,
										pstatus_nd_kandidata,
										vrijeme_promjene_statusa_nd_kandidata,
										promjenio_zaposlenik_nd_kandidata
									)
									VALUES
									(
										:idd_broj_nd_kandidata,
										:status_nd_kandidata,
										:pstatus_nd_kandidata,
										:vrijeme_promjene_statusa_nd_kandidata,
										:promjenio_zaposlenik_nd_kandidata
									)
					");
					
					$new_ND_status->execute(array(
									':idd_broj_nd_kandidata' => $kandidatId,
									':status_nd_kandidata' => 7,
									':pstatus_nd_kandidata' => 1,
									':vrijeme_promjene_statusa_nd_kandidata' => date('Y-m-d H:i:s'),
									':promjenio_zaposlenik_nd_kandidata' => $logged_employee_id
					));

					updateProjekcijeKandidat(1, $kandidatId);
					
				}
				
				$updatePredracun = $db->prepare("
					UPDATE
						idk_predracuni
					SET 
						pr_stornirano = 1
					WHERE 
						pr_id = :pr_id
				");
				$updatePredracun->execute(array(
					':pr_id' => $predracunId
				));
				arhivirajUgovorPredracunDIPL($kandidatId);
				
			break;
			default:
			
			break;
		}
	}
}

function checkJobSoftUserAccess($user_id, $user_company, $order_id, $partner_id, $type) {
	Global $db; 

	$partner_subcondition = (($partner_id != 0) ? 'pua.pua_partner_id = '.$partner_id.'' : 'pua.pua_partner_id is null'); 
	$result = array(
		'accessId' => 0, 
		'accessStatus' => 0
	);

	$query_check_access = $db->prepare('
		SELECT 
			pua.pua_id AS accessId,
			pua.pua_status AS accessStatus
		FROM 
			idk_pp_user_access pua
		JOIN 
			idk_pp_users pu
		ON 
			pua.pua_user_id = pu.pu_id
		WHERE 
			pua.pua_type = :accessType 
			AND 
			pua.pua_nalog_id = :orderId
			AND 
			'.$partner_subcondition.'
			AND 
			pua.pua_user_id = :userId
			AND 
			pu.pu_company_id = :userCompanyId
	');
	$query_check_access->execute(array(
		':accessType' => $type,
		':orderId' => $order_id, 
		':userId' => $user_id, 
		':userCompanyId' => $user_company
	));

	if ($query_check_access->rowCount() == 1) {
		$row_check_access = $query_check_access->fetch();
		$result['accessId'] = $row_check_access['accessId'];
		$result['accessStatus'] = $row_check_access['accessStatus'];
	}

	return $result;
}

function getActivePartnersForOrderArrayR($orderId) {
	Global $db; 

	$result = array();

	if ($orderId != 0) {
		$query = $db->prepare('
			SELECT
				pp.ppa_id AS partnerId
			FROM
				idk_pp_partners pp 
			WHERE 
				pp.ppa_nalog_id = :orderId
				AND 
				pp.ppa_status = 1
		');
		$query->execute(array(
			':orderId' => $orderId
		)); 
		if ($query->rowCount() != 0) {
			while ($row = $query->fetch()) {
				array_push($result, $row['partnerId']); 
			}
		}
	}

	return $result;

}

function superadminAccess($user_id, $nalog_id){
	Global $db;
	$query_check_superadmin = $db->prepare("
											SELECT
												pua_user_id
											FROM
												idk_pp_user_access
											WHERE
												pua_user_id = :user_id
											AND
												pua_nalog_id = :nalog_id
											AND
												pua_type = 1
										");
	$query_check_superadmin->execute(array(
		":user_id" => $user_id,
		":nalog_id" => $nalog_id
	));
	$superadmin_count = $query_check_superadmin->rowCount();
	if($superadmin_count > 0){
		$query_update_superadmin = $db->prepare("
													UPDATE
														idk_pp_user_access
													SET
														pua_status = 1
													WHERE
														pua_user_id = :user_id
													AND
														pua_nalog_id = :nalog_id
													AND
														pua_type = 1
											");
		$query_update_superadmin->execute(array(
			":user_id" => $user_id,
			":nalog_id" => $nalog_id
		));
	} else {
		$query_add_superadmin = $db->prepare("
										INSERT INTO
											idk_pp_user_access
											(
												pua_type,
												pua_nalog_id,
												pua_user_id,
												pua_permission_id
											)
										VALUES
											(
												:pua_type,
												:pua_nalog_id,
												:pua_user_id,
												:pua_permission_id
											)
									");
		$query_add_superadmin->execute(array(
			":pua_type" => 1,
			":pua_nalog_id" => $nalog_id,
			":pua_user_id" => $user_id,
			":pua_permission_id" => 1
		));
	}
}

function adminAccess($user_id, $nalog_id, $ppa_id){
	Global $db;
	$query_check_admin = $db->prepare("
											SELECT
												pua_user_id
											FROM
												idk_pp_user_access
											WHERE
												pua_user_id = :user_id
											AND
												pua_nalog_id = :nalog_id
											AND
												pua_partner_id = :ppa_id
											AND
												pua_type = 2
										");
	$query_check_admin->execute(array(
		":user_id" => $user_id,
		":nalog_id" => $nalog_id,
		":ppa_id" => $ppa_id
	));
	$admin_count = $query_check_admin->rowCount();
	if($admin_count > 0){
		$query_update_admin = $db->prepare("
													UPDATE
														idk_pp_user_access
													SET
														pua_status = 1
													WHERE
														pua_user_id = :user_id
													AND
														pua_nalog_id = :nalog_id
													AND
														pua_partner_id = :ppa_id
													AND 
														pua_type = 2
											");
		$query_update_admin->execute(array(
			":user_id" => $user_id,
			":nalog_id" => $nalog_id,
			":ppa_id" => $ppa_id
		));
	} else {
		$query_update_admin = $db->prepare("
										INSERT INTO
											idk_pp_user_access
											(
												pua_type,
												pua_nalog_id,
												pua_user_id,
												pua_permission_id,
												pua_partner_id
											)
										VALUES
											(
												:pua_type,
												:pua_nalog_id,
												:pua_user_id,
												:pua_permission_id,
												:pua_partner_id
											)
									");
		$query_update_admin->execute(array(
			":pua_type" => 2,
			":pua_nalog_id" => $nalog_id,
			":pua_user_id" => $user_id,
			":pua_permission_id" => 2,
			":pua_partner_id" => $ppa_id
		));
	}
}

function checkAvailabilityLink($date_sent, $first_sending_day, $second_sending_day, $interview_date, $counter_sent){
	
	Global $db;
	
	$current_date_time = date("Y-m-d H:i:s"); 

	$date_sent = date("Y-m-d H:i:s", strtotime($date_sent));
	$first_sending_day = intval($first_sending_day);
	$second_sending_day = intval($second_sending_day);
	$interview_date = date("Y-m-d 23:59:59", strtotime($interview_date));
	$counter_sent = intval($counter_sent); 

	if($counter_sent == 1){

		$difference_between_sending = 0;

		if($second_sending_day > 0 AND $first_sending_day > $second_sending_day){

			$difference_between_sending = $first_sending_day - $second_sending_day; 

			$query = $db->prepare("
				SELECT
					DATE_ADD('".$date_sent."',INTERVAL ".$difference_between_sending." DAY) 
				AS 
					endDate
			");
			$query->execute();

			$row = $query->fetch();

			$date_end = date("Y-m-d H:i:s", strtotime($row["endDate"]));

			if(($current_date_time >= $date_sent) AND ($current_date_time <= $date_end)){

				return 1;

			}else{

				return 0;

			}
			
		}else{

			if(($current_date_time >= $date_sent) AND ($current_date_time <= $interview_date)){

				return 1;

			}else{

				return 0;

			}

		}

	}elseif ($counter_sent == 2){

		if(($current_date_time >= $date_sent) AND ($current_date_time <= $interview_date)){

			return 1;

		}else{

			return 0;

		}

	}else{

		return 0;

	}

}

function getNumberOfDaysToCasting($interview_date){

	Global $db; 

	$current_date = date("Y-m-d"); 
	$interview_date = date("Y-m-d", strtotime($interview_date));

	$query = $db->prepare("
		SELECT
			DATEDIFF('".$interview_date."', '".$current_date."') AS numberOfDays
	");
	$query->execute();
	$row = $query->fetch();

	$numberOfDays = intval($row["numberOfDays"]); 

	return $numberOfDays;

}

function setStatusAppointmentInviteLinkR($key, $value){

	Global $db; 
	$enabled_value = array(0,1,2,3);

	if($key != "" AND in_array($value, $enabled_value)){

		$result = getInfoAppointmentInviteLinkArrayR($key); 

		if($result["message"] == "Success!"){
			
			if($result["data"]["status"] != $value){
				
				$column = "";

				if($value == 0){

					$column = "date_archived";

				}elseif($value == 1){

					$column = "date_sent";

				}elseif($value == 2){

					$column = "date_open";

				}else{

					$column = "date_confirmed"; 

				}
				
				$query_update = $db->prepare("
					UPDATE  
						idk_appointment_invite_links
					SET 
						link_status = :link_status, 
						".$column." = CURRENT_TIMESTAMP()
					WHERE 
						id = :id
				");
				$query_update->execute(array(
					":id" => $result["data"]["id"], 
					":link_status" => $value
				));

				return "Successfully updated!";

			}else{

				return "The status is on the requested status!";

			}

		}else{

			return "SQL query does not return results!"; 

		}

	}else{

		return "Invalid arguments passed!"; 

	}

}

function getInfoAppointmentInviteLinkArrayR($key){

	Global $db;

	$result = array(
		"message" => "", 
		"data" => array(
			"id" => 0,
			"candidate_id" => 0,
			"interview_id" => 0,
			"status" => 0, 
			"date_sent" => "",
			"date_open" => "",
			"date_confirmed" => "",
			"date_archived" => "",
			"send_key" => "",
			"sent_days_before" => 0,
			"counter_sent" => 0, 
			"pca_appointment_id" => 0, 
			"pca_time" => "", 
			"pca_status" => 0, 
			"pap_date" => 0, 
			"pap_nalog_id" => 0, 
			"pap_city" => "",
			"pap_location_name" => "",
			"pap_google_maps_location" => "", 
			"pap_first_sending_number_days" => 0, 
			"pap_second_sending_number_days" => 0, 
			"pap_first_send_enabled" => 0,
			"pap_second_send_enabled" => 0,
			"pap_group_id" => 0
		)
	);

	if($key != ""){
		$query = $db->prepare("
			SELECT 
				ail.id,
				ail.candidate_id, 
				ail.interview_id, 
				ail.link_status, 
				ail.date_sent, 
				ail.date_open, 
				ail.date_confirmed, 
				ail.date_archived, 
				ail.send_key, 
				ail.sent_days_before, 
				ail.counter_sent,
				pca.pca_appointment_id, 
				pca.pca_time, 
				pca.pca_status, 
				pap.pap_date, 
				pap.pap_nalog_id, 
				pap.pap_city, 
				pap.pap_location_name, 
				pap.pap_google_maps_location, 
				pap.pap_first_sending_number_days, 
				pap.pap_second_sending_number_days, 
				pap.pap_first_send_enabled, 
				pap.pap_second_send_enabled, 
				pap.pap_group_id
			FROM 
				idk_appointment_invite_links ail 
			JOIN  
				idk_pp_cand_appts pca 
			ON 
				pca.pca_id = ail.interview_id 
			JOIN 
				idk_pp_appointments pap
			ON
				pap.pap_id = pca.pca_appointment_id 
			WHERE 
				ail.send_key = :send_key
		");
		$query->execute(array(
			":send_key" => $key
		));
		if($query->rowCount() == 1){

			$row = $query->fetch();

			$result["message"] = "Success!";
			$result["data"]["id"] = $row["id"];
			$result["data"]["candidate_id"] = $row["candidate_id"];
			$result["data"]["interview_id"] = $row["interview_id"];
			$result["data"]["status"] = $row["link_status"];
			$result["data"]["date_sent"] = $row["date_sent"];
			$result["data"]["date_open"] = $row["date_open"];
			$result["data"]["date_confirmed"] = $row["date_confirmed"];
			$result["data"]["date_archived"] = $row["date_archived"];
			$result["data"]["send_key"] = $row["send_key"];
			$result["data"]["sent_days_before"] = $row["sent_days_before"];
			$result["data"]["counter_sent"] = $row["counter_sent"];
			$result["data"]["pca_appointment_id"] = $row["pca_appointment_id"];
			$result["data"]["pca_time"] = $row["pca_time"];
			$result["data"]["pca_status"] = $row["pca_status"];
			$result["data"]["pap_date"] = $row["pap_date"];
			$result["data"]["pap_nalog_id"] = $row["pap_nalog_id"];
			$result["data"]["pap_city"] = $row["pap_city"];
			$result["data"]["pap_location_name"] = $row["pap_location_name"];
			$result["data"]["pap_google_maps_location"] = $row["pap_google_maps_location"];
			$result["data"]["pap_first_sending_number_days"] = $row["pap_first_sending_number_days"];
			$result["data"]["pap_second_sending_number_days"] = $row["pap_second_sending_number_days"];
			$result["data"]["pap_first_send_enabled"] = $row["pap_first_send_enabled"];
			$result["data"]["pap_second_send_enabled"] = $row["pap_second_send_enabled"];
			$result["data"]["pap_group_id"] = $row["pap_group_id"];

		}else{

			$result["message"] = "SQL query does not return results!";

		}
	}else{

		$result["message"] = "Invalid arguments passed!";

	}

	return $result;

}

function getProgressPp($nalog_id){
	Global $db;
	
	$query_get_superadmin = $db->prepare("
										SELECT
											pua_user_id
										FROM
											idk_pp_user_access
										WHERE
											pua_nalog_id = :nalog_id
										AND
											pua_type = 1
										AND
											pua_status = 1
										");
	$query_get_superadmin->execute(array(
		":nalog_id" => $nalog_id
	));
	$count_superadmin = $query_get_superadmin->rowCount();
	$query_get_admin = $db->prepare("
									SELECT
										pua_user_id
									FROM
										idk_pp_user_access
									WHERE
										pua_nalog_id = :nalog_id
									AND
										pua_type = 2
									AND
										pua_status = 1
									");
	$query_get_admin->execute(array(
		":nalog_id" => $nalog_id
	));
	$count_admin = $query_get_admin->rowCount();
	
	if($count_superadmin == 0 and $count_admin == 0){
		echo "<span class='label label-danger'>Nije omogućeno</span>";
	} 
	else if($count_superadmin > 0 and $count_admin == 0){
		echo "<span class='label label-warning'>U toku</span>";
	}
	else if($count_superadmin == 0 and $count_admin > 0){
		echo "<span class='label label-warning'>U toku</span>";
	} else {
		echo "<span class='label label-success'>Omogućeno</span>";
	}
}

function updateNalogPp($nalog_id){
	Global $db;
	
	$query_get_superadmin = $db->prepare("
										SELECT
											pua_user_id
										FROM
											idk_pp_user_access
										WHERE
											pua_nalog_id = :nalog_id
										AND
											pua_type = 1
										AND
											pua_status = 1
										");
	$query_get_superadmin->execute(array(
		":nalog_id" => $nalog_id
	));
	$count_superadmin = $query_get_superadmin->rowCount();
	$query_get_admin = $db->prepare("
									SELECT
										pua_user_id
									FROM
										idk_pp_user_access
									WHERE
										pua_nalog_id = :nalog_id
									AND
										pua_type = 2
									AND
										pua_status = 1
									");
	$query_get_admin->execute(array(
		":nalog_id" => $nalog_id
	));
	$count_admin = $query_get_admin->rowCount();
	
	if($count_superadmin > 0 and $count_admin > 0){
		$query_update_nalog = $db->prepare("
											UPDATE
												idk_nalozi
											SET
												pristup_poslodavcima = 1
											WHERE
												nalog_id = :nalog_id
										");
		$query_update_nalog->execute(array(
			":nalog_id" => $nalog_id
		));
	} else {
		$query_update_nalog = $db->prepare("
										UPDATE
											idk_nalozi
										SET
											pristup_poslodavcima = 0
										WHERE
											nalog_id = :nalog_id
									");
		$query_update_nalog->execute(array(
			":nalog_id" => $nalog_id
		));
	}
}

function updateNalogPartner($partner_id, $nalog_id){
	Global $db;
	
	$query_check_partners = $db->prepare("
										SELECT
											ppa_id
										FROM
											idk_pp_partners
										WHERE
											ppa_nalog_id = :nalog_id
										AND
											ppa_company_id = :partner_id
										AND
											ppa_status = 1
										");
	$query_check_partners->execute(array(
		":nalog_id" 	=> $nalog_id,
		":partner_id" 	=> $partner_id
	));
	$count_partners = $query_check_partners->rowCount();
	
	if($count_partners > 0){
		$query_update_nalog_partner = $db->prepare("
													UPDATE
														idk_nalozi
													SET
														partneri_pp = 1
													WHERE
														nalog_id = :nalog_id
												");
		$query_update_nalog_partner->execute(array(
			":nalog_id" => $nalog_id
		));	
	} else {
		$query_update_nalog_partner = $db->prepare("
													UPDATE
														idk_nalozi
													SET
														partneri_pp = 0
													WHERE
														nalog_id = :nalog_id
												");
		$query_update_nalog_partner->execute(array(
			":nalog_id" => $nalog_id
		));	
	}
}

function ppButton($nalog_id){
	Global $db;
	
	$query_get_superadmin = $db->prepare("
										SELECT
											pua_user_id
										FROM
											idk_pp_user_access
										WHERE
											pua_nalog_id = :nalog_id
										AND
											pua_type = 1
										AND
											pua_status = 1
										");
	$query_get_superadmin->execute(array(
		":nalog_id" => $nalog_id
	));
	$count_superadmin = $query_get_superadmin->rowCount();
	$query_get_admin = $db->prepare("
									SELECT
										pua_user_id
									FROM
										idk_pp_user_access
									WHERE
										pua_nalog_id = :nalog_id
									AND
										pua_type = 2
									AND
										pua_status = 1
									");
	$query_get_admin->execute(array(
		":nalog_id" => $nalog_id
	));
	$count_admin = $query_get_admin->rowCount();
	
	if($count_superadmin == 0 and $count_admin == 0){
		echo "Omogući pristup";
	} 
	else if($count_superadmin > 0 and $count_admin == 0){
		echo "Uredi pristup";
	}
	else if($count_superadmin == 0 and $count_admin > 0){
		echo "Uredi pristup";
	} else {
		echo "Uredi pristup";
	}
}

function addToLogsStatusPrijave($stari_projekt_id, $novi_projekt_id, $status_id, $kandidat_id, $izvor, $is_from_queue = NULL){
	Global $db;
	Global $logged_employee_id;
	$log_date = date('Y-m-d H:i:s');
	
	$partner_sql = "SELECT jp_id, kandidat_ime, kandidat_prezime, jp_lang, jp_fcmtoken FROM idk_jobstep_partners JOIN idk_kandidati ON idk_jobstep_partners.jp_id = idk_kandidati.kandidat_partnerid WHERE kandidat_id = :kandidat_id AND jp_user_type = 1";
	$partner_query = $db->prepare($partner_sql);
	$partner_query->execute(array(
		":kandidat_id" => $kandidat_id
	));
	$partner_row = $partner_query->fetch();
	$partner_id = $partner_row['jp_id'];
	$kandidat_name = $partner_row['kandidat_ime']." ".$partner_row['kandidat_prezime'];
	$onesignal_id = $partner_row['jp_fcmtoken'];
	if($partner_id != null){
		$notification_type = getPartnerNotificationTypeId("CANDIDATES");
		$send_notifications = shouldSendPersonalNotification($partner_id,$notification_type);
	}
	$stari_status_query = $db->prepare("SELECT lsp_status_prijave_id FROM idk_log_statusi_prijave WHERE lsp_kandidat_id = :kandidat_id AND lsp_broj_dana IS NULL");
	$stari_status_query->execute(array(
		":kandidat_id" => $kandidat_id
	));
	$stari_status_row = $stari_status_query->fetch();
	$stari_status_id = $stari_status_row['lsp_status_prijave_id'];

	if($onesignal_id != null && $send_notifications == 1){
		if($status_id == 2 && $partner_id != null && $stari_status_id != 2){
			if($partner_row['jp_lang'] == 'en'){
				$title = "Candidate";
				$content = "A new candidate has applied to the ad you shared";
			} else {
				$title = "Kandidat";
				$content = "Ein neuer Kandidat hat sich auf die von Ihnen geteilte Anzeige beworben";
			}
			$payload = '{"candidate_id": "'.$kandidat_id.'"}';
			$notification_title		='{ "en":"Candidate", "de":"Kandidat" }';
			$notification_content 	='{ "en":"A new candidate has applied to the ad you shared", "de":"Ein neuer Kandidat hat sich auf die von Ihnen geteilte Anzeige beworben"}';
			$action="NAVIGATE_TO_CANDIDATE";
			createPartnerPersonalNotification($partner_id, $notification_type, $payload, $action, $notification_title, $notification_content);
			sendNotification($onesignal_id, $title, $content);
		} 
		elseif($status_id == 10 && $partner_id != null){
			if($partner_row['jp_lang'] == "en"){
				$title = "Candidate changed status";
				$content = "The candidate's status has been changed to 'Work started'";
			} else {
				$title = "Kandidat";
				$content = 'Der Status des Kandidaten wurde in „Arbeitsbeginn“ geändert.';
			}
			$payload = '{"candidate_id": "'.$kandidat_id.'"}';
			$notification_title		='{ "en":"Candidate changd statuse", "de":"Kandidat" }';
			$notification_content 	='{ "en": "The candidate\'s status has been changed to \'Work started\'", "de": "Der Status des Kandidaten wurde in \'Arbeitsbeginn\' geändert." }';
			$action="NAVIGATE_TO_CANDIDATE";
			createPartnerPersonalNotification($partner_id, $notification_type, $payload, $action, $notification_title, $notification_content);
			sendNotification($onesignal_id, $title, $content);
		}
	}
	
	//check da li ima unesen log za prethodni projekt, posto je na pocetku sve prazno, treba unijeti, samo u slucaju kada se prebaciva sa castinga na intervju
	if($stari_projekt_id != null){
		$stari_projekt_naziv = getProjectFullName($stari_projekt_id);
	}else{
		$stari_projekt_naziv = "";
	}
	
	$novi_projekt_naziv = getProjectFullName($novi_projekt_id);
	
	if(strpos($stari_projekt_naziv, 'Casting') == true AND strpos($novi_projekt_naziv, 'Intervju') == true){
		
		$check_casting = $db->prepare("
						SELECT lsp_kandidat_id, lsp_datetime, lsp_id
						FROM idk_log_statusi_prijave 
						WHERE lsp_kandidat_id = :lsp_kandidat_id AND lsp_projekt_id = :lsp_projekt_id AND lsp_status_prijave_id = :lsp_status_prijave_id AND lsp_broj_dana is NULL
		");
		$check_casting->execute(array(
						":lsp_kandidat_id" => $kandidat_id,
						":lsp_projekt_id" => $stari_projekt_id,
						":lsp_status_prijave_id" => $status_id
		));
		if($check_casting->rowCount() > 0){
			$row_check_casting = $check_casting->fetch();
			$lsp_id = $row_check_casting['lsp_id'];
			$lsp_datetime = $row_check_casting['lsp_datetime'];
			$diff_novi = strtotime($log_date) - strtotime($lsp_datetime);
			$day_novi = floor($diff_novi/86400);
			//UPDATE BROJA DANA
			$update_days = $db->prepare("
						UPDATE idk_log_statusi_prijave
						SET lsp_broj_dana = :lsp_broj_dana
						WHERE lsp_kandidat_id = :lsp_kandidat_id AND lsp_id = :lsp_id
			");
			$update_days->execute(array(
						':lsp_id' => $lsp_id,
						':lsp_kandidat_id' => $kandidat_id,
						':lsp_broj_dana' => $day_novi
			));
		}else{
			
			$query_logs = $db->prepare("SELECT log_employeeid, log_date FROM idk_logs WHERE log_desc LIKE 'Vezao kandidata: $kandidat_id za projekt $stari_projekt_id.' ");
			$query_logs->execute();
			$row_logs = $query_logs->fetch();
			$stari_employee_id = $row_logs['log_employeeid'];
			$stari_log_date = $row_logs['log_date'];
			
			$diff_novi = strtotime($log_date) - strtotime($stari_log_date);
			$day_novi = floor($diff_novi/86400);
			
			//INSERT STAROG LOGA
			$insert_log_s = $db->prepare("
							INSERT INTO idk_log_statusi_prijave
								(lsp_kandidat_id, lsp_projekt_id, lsp_status_prijave_id, lsp_employee_id, lsp_izvor, lsp_datetime, lsp_broj_dana)
							VALUES
								(:lsp_kandidat_id,:lsp_projekt_id,:lsp_status_prijave_id,:lsp_employee_id,:lsp_izvor,:lsp_datetime,:lsp_broj_dana)
			");
			$insert_log_s->execute(array(
							':lsp_kandidat_id' => $kandidat_id,
							':lsp_projekt_id' => $stari_projekt_id,
							':lsp_status_prijave_id' => $status_id,
							':lsp_employee_id' => $stari_employee_id,
							':lsp_izvor' => 1,
							':lsp_datetime' => $stari_log_date,
							':lsp_broj_dana' => $day_novi
			));
		}
		//echo $stari_employee_id." ".$stari_log_date;
	}else{
		$normal_check = $db->prepare("
						SELECT lsp_id, lsp_datetime
						FROM idk_log_statusi_prijave
						WHERE lsp_kandidat_id = :lsp_kandidat_id AND lsp_broj_dana is NULL
		");
		$normal_check->execute(array(
						":lsp_kandidat_id" => $kandidat_id
		));
		if($normal_check->rowCount() > 0){
			$row_normal_check = $normal_check->fetch();
			$lsp_id = $row_normal_check['lsp_id'];
			$lsp_datetime = $row_normal_check['lsp_datetime'];
			$diff_novi = strtotime($log_date) - strtotime($lsp_datetime);
			$day_novi = floor($diff_novi/86400);
			//normalni updejt broja dana za stari status
			$update_days = $db->prepare("
						UPDATE idk_log_statusi_prijave
						SET lsp_broj_dana = :lsp_broj_dana
						WHERE lsp_kandidat_id = :lsp_kandidat_id AND lsp_id = :lsp_id
			");
			$update_days->execute(array(
						':lsp_id' => $lsp_id,
						':lsp_kandidat_id' => $kandidat_id,
						':lsp_broj_dana' => $day_novi
			));
		}
	}

	updateCandidateProjectionAndInstallment($kandidat_id);

	//INSERT NOVOG LOGA
	$insert_log = $db->prepare("
					INSERT INTO idk_log_statusi_prijave
						(lsp_kandidat_id, lsp_projekt_id, lsp_status_prijave_id, lsp_employee_id, lsp_izvor, lsp_datetime)
					VALUES
						(:lsp_kandidat_id,:lsp_projekt_id,:lsp_status_prijave_id,:lsp_employee_id,:lsp_izvor,:lsp_datetime)
	");
	$insert_log->execute(array(
					':lsp_kandidat_id' => $kandidat_id,
					':lsp_projekt_id' => $novi_projekt_id,
					':lsp_status_prijave_id' => $status_id,
					':lsp_employee_id' => $logged_employee_id,
					':lsp_izvor' => $izvor,
					':lsp_datetime' => $log_date,
	));

	$nalogId = getNalogIdForProjektR($novi_projekt_id);
	if(is_null($is_from_queue)){
		if((isReservedByProject($novi_projekt_naziv) == true AND $nalogId != 44) OR isReservedByStatusPrijave($kandidat_id) == true){
			updateNalogIdForCandidat($kandidat_id, $nalogId);
		}else{
			resetNalogIdForCandidat($kandidat_id);
			pullFromProjectCandidateQueue($kandidat_id, $stari_projekt_id);
		}
	}
	else{
		checkCandidateInputs($kandidat_id, $nalogId);
	}
}


function isReservedByStatusPrijave($kandidat_id){
	Global $db;
	$rezervisanje = false;
	
	$query_get_status_prijave = $db -> prepare('
		SELECT sp.rezervisanje
		FROM (
			SELECT sqk.kandidat_status_prijave
			FROM idk_kandidati sqk
			WHERE sqk.kandidat_id = :kandidat_id
		) k
		JOIN idk_kandidat_status_prijave sp
		ON sp.status_id = k.kandidat_status_prijave
	');
	$query_get_status_prijave -> execute(array(':kandidat_id' => $kandidat_id));
	if($query_get_status_prijave -> rowCount() != 0){
		$row_get_status_prijave = $query_get_status_prijave -> fetch();
		$rezervisanje = boolval($row_get_status_prijave['rezervisanje']);
	}
	
	return $rezervisanje;
}

function isReservedByProject($project_name){
	$return_value	= false;
	if(		
			strpos($project_name, 'BOT - ispunjava uslove')
		OR	strpos($project_name, 'Završeni kandidati')
		OR	strpos($project_name, 'Obrađeno')
		OR	strpos($project_name, 'Intervju')
		OR	strpos($project_name, 'Casting')
		OR	strpos($project_name, '- Ugovor')
		OR	strpos($project_name, 'počeli')
		OR 	strpos($project_name, 'Baza - odgovara za nalog')
	){
		$return_value	= true;
	}
	return $return_value;
}

function getNalogIdForProjektR($projektId){
	Global $db; 
	$query = $db->prepare("
		SELECT 
			project_nalogid
		FROM 
			idk_projects
		WHERE 
			project_id = :project_id
	");
	$query->execute(array(
		':project_id' => $projektId
	));
	$row = $query->fetch();
	
	$project_nalogid = $row["project_nalogid"];
	
	return $project_nalogid; 
}

function updateNalogIdForCandidat($idKandidat, $idNalog){
	Global $db;
	$query = $db->prepare("
		UPDATE
			idk_kandidati
		SET 
			kandidat_nalog_id = :kandidat_nalog_id,
			kandidat_latest_reserved_time = :kandidat_latest_reserved_time
		WHERE 
			kandidat_id = :kandidat_id
	");
	$query->execute(array(
		':kandidat_id' => $idKandidat, 
		':kandidat_nalog_id' => $idNalog, 
		':kandidat_latest_reserved_time' => date("Y-m-d H:i:s")
	));
}

function resetNalogIdForCandidat($idKandidat){
	Global $db;
	$getCurrentDetails = $db->prepare("
				SELECT
				kandidat_nalog_id,
				kandidat_latest_reserved_time,
				kandidat_ppa_partner_id,
				kandidat_pp_pozicija,
				kandidat_pp_lokacija,
				kandidat_pp_plata,
				kandidat_pp_povezao_user_id,
				kandidat_pp_razlog_odbijanja,
				kandidat_pp_datum_prihvatanja
			FROM
				idk_kandidati
			WHERE
				kandidat_id = :kandidat_id
	");
	$getCurrentDetails ->execute(array(
			'kandidat_id'	=> $idKandidat
	)); 

	$currentDetails 				= $getCurrentDetails->fetch();

	$kandidat_nalog_id 				= $currentDetails["kandidat_nalog_id"];
	$kandidat_latest_reserved_time	= $currentDetails["kandidat_latest_reserved_time"];
	$kandidat_ppa_partner_id 		= $currentDetails["kandidat_ppa_partner_id"];
	$kandidat_pp_pozicija 			= $currentDetails["kandidat_pp_pozicija"];
	$kandidat_pp_lokacija		 	= $currentDetails["kandidat_pp_lokacija"];
	$kandidat_pp_plata 				= $currentDetails["kandidat_pp_plata"];
	$kandidat_pp_povezao_user_id 	= $currentDetails["kandidat_pp_povezao_user_id"];
	$kandidat_pp_razlog_odbijanja 	= $currentDetails["kandidat_pp_razlog_odbijanja"];
	$kandidat_pp_datum_prihvatanja 	= $currentDetails["kandidat_pp_datum_prihvatanja"];

	$query = $db->prepare("
		UPDATE
			idk_kandidati
		SET 
			kandidat_nalog_id = :kandidat_nalog_id,
			kandidat_latest_reserved_time = :kandidat_latest_reserved_time,
			kandidat_ppa_partner_id = :kandidat_ppa_partner_id,
			kandidat_pp_pozicija = :kandidat_pp_pozicija,
			kandidat_pp_lokacija = :kandidat_pp_lokacija,
			kandidat_pp_plata = :kandidat_pp_plata,
			kandidat_pp_povezao_user_id = :kandidat_pp_povezao_user_id,
			kandidat_pp_razlog_odbijanja = :kandidat_pp_razlog_odbijanja,
			kandidat_pp_datum_prihvatanja = :kandidat_pp_datum_prihvatanja
		WHERE 
			kandidat_id = :kandidat_id
	");
	$query->execute(array(
		':kandidat_id' 						=> $idKandidat, 
		':kandidat_latest_reserved_time'	=> NULL, 
		':kandidat_nalog_id' 				=> NULL, 
		':kandidat_ppa_partner_id' 			=> NULL,
		':kandidat_pp_lokacija' 			=> NULL,
		':kandidat_pp_plata' 				=> NULL,
		':kandidat_pp_povezao_user_id' 		=> NULL,
		':kandidat_pp_razlog_odbijanja' 	=> NULL,
		':kandidat_pp_pozicija' 			=> NULL,
		':kandidat_pp_datum_prihvatanja' 	=> NULL
	));
	$log_desc = "Resetovani podaci za kandidata ID=[".$idKandidat."]. 
					Podaci resetovani na NULL vrijednost sa vrijednosti: 
						kandidat_latest_reserved_time=".$kandidat_latest_reserved_time." ,
						kandidat_nalog_id=".$kandidat_nalog_id." ,
						kandidat_ppa_partner_id=".$kandidat_ppa_partner_id." ,
						kandidat_pp_lokacija=".$kandidat_pp_lokacija." ,
						kandidat_pp_plata=".$kandidat_pp_plata." ,
						kandidat_pp_povezao_user_id=".$kandidat_pp_povezao_user_id." ,
						kandidat_pp_razlog_odbijanja=".$kandidat_pp_razlog_odbijanja." ,
						kandidat_pp_pozicija=".$kandidat_pp_pozicija." ,
						kandidat_pp_datum_prihvatanja=".$kandidat_pp_datum_prihvatanja." 
				";
	addToLogs($log_desc, 0);
	// Global $db;
	// $query = $db->prepare("
		// UPDATE
			// idk_kandidati
		// SET 
			// kandidat_nalog_id = :kandidat_nalog_id,
			// kandidat_latest_reserved_time = :kandidat_latest_reserved_time
		// WHERE 
			// kandidat_id = :kandidat_id
	// ");
	// $query->execute(array(
		// ':kandidat_id' => $idKandidat, 
		// ':kandidat_nalog_id' => NULL, 
		// ':kandidat_latest_reserved_time' => NULL
	// ));
}
function getStatusPrijaveByProjectId($project_id){
	Global $db;
	$query_get_project_name = $db -> prepare('
		SELECT project_name
		FROM idk_projects
		WHERE project_id = :project_id
	');
	$query_get_project_name -> execute(array(':project_id' => $project_id));
	$status_prijave = 0;
	if($query_get_project_name -> rowCount()){
		$row_get_project_name = $query_get_project_name -> fetch();
		$project_name = $row_get_project_name['project_name'];
		if(
				strpos($project_name, 'Prijav')
			OR  strpos($project_name, 'U obradi')
			OR  strpos($project_name, 'BOT - ne ispunjava uslove')
			OR 	strpos($project_name, 'Nije došao na razgovor')
			OR 	strpos($project_name, 'Baza - odgovara za nalog')
			OR 	strpos($project_name, 'Nije zainteresiran')
			OR 	strpos($project_name, 'Pogrešan broj')
			OR 	strpos($project_name, 'Nedostupan')
		){$status_prijave = 2;}
		else if(
				strpos($project_name, 'BOT - ispunjava uslove')
			OR  strpos($project_name, 'Obrađeno')
			OR  strpos($project_name, 'Baza - odgovara za nalog')
		){$status_prijave = 6;}
		else if(
				strpos($project_name, 'Casting')
			OR 	strpos($project_name, 'Intervju')
			 
		){$status_prijave = 3;}
		else if(strpos($project_name, 'Ugovor')){
			$status_prijave = 7;
		}
		else if(strpos($project_name, 'Odbijen')){
			$status_prijave = 5;
		}
		else if(
				strpos($project_name, 'Završeni kandidati')
			OR	strpos($project_name, 'počeli')
			
		){$status_prijave = 10;}
		else if(strpos($project_name, '- Odustao')){
			$status_prijave = 1;
		}
	}
	return $status_prijave;			
}

function getNalogIdByCandidateId($candidate_id){
	Global $db;
	$query_get_nalog_id = $db -> prepare('
		SELECT kandidat_nalog_id
		FROM idk_kandidati
		WHERE kandidat_id = :candidate_id
	');
	$query_get_nalog_id -> execute(array(':candidate_id' => $candidate_id));
	$row_get_nalog_id = $query_get_nalog_id -> fetch();
	$nalog_id = $row_get_nalog_id['kandidat_nalog_id'];
	
	return $nalog_id;
}
function getPartnerIdByCandidateId($candidateId){
	Global $db;
	$query = $db->prepare("
		SELECT 
			kandidat_ppa_partner_id
		FROM 
			idk_kandidati
		WHERE 
			kandidat_id = :kandidat_id
	");
	$query->execute(array(
		':kandidat_id' => $candidateId
	));
	$row = $query->fetch();
	$result = $row["kandidat_ppa_partner_id"];

	return $result;
}
function pushToProjectCandidateQueue($candidate_id, $project_id, $parnter_id = null){
	Global $db;

	$query_check_if_already_in_queue = $db -> prepare('
		SELECT queue_candidate_id, queue_project_id
		FROM idk_project_candidate_queue
		WHERE queue_candidate_id = :candidate_id
		AND queue_project_id = :project_id
		AND queue_is_assigned = 0
	');
	$query_check_if_already_in_queue -> execute(array(
		':candidate_id' => $candidate_id,
		':project_id' 	=> $project_id
	));
	
	if($query_check_if_already_in_queue->rowCount())
		return;

	$new_nalog_id = getNalogIdForProjektR($project_id);
	$query_check_dupla_prijava_isti_nalog = $db -> prepare("
		SELECT 
			pk.pk_id
		FROM 
			idk_project_kandidati pk
		JOIN 
			idk_projects p
		ON 
			pk.pk_projectid = p.project_id
		WHERE 
			pk.pk_kandidatid = :candidate_id
			AND 
			(
				project_name LIKE '%Intervju%' 
				OR 
				project_name LIKE '%Obrađeno%' 
				OR 
				project_name LIKE '%Završeni kandidati%' 
				OR 
				project_name LIKE '%BOT - ispunjava uslove%' 
				OR 
				project_name LIKE '%Baza - odgovara za nalog%'
				OR 
				project_name LIKE '%Casting%' 
				OR 
				project_name LIKE '%Ugovor%'
			)
			AND p.project_nalogid = :new_nalog_id
	");
	$query_check_dupla_prijava_isti_nalog -> execute(array(
		':new_nalog_id' => $new_nalog_id,
		':candidate_id' => $candidate_id
	));
	$dp_check_isti_nalog = $query_check_dupla_prijava_isti_nalog -> rowCount();
	if($dp_check_isti_nalog OR getNalogIdByCandidateId($candidate_id) == $new_nalog_id){
		return;
	}
	else{
		$query_insert_into_queue = $db -> prepare('
			INSERT INTO idk_project_candidate_queue
				(queue_project_id, queue_candidate_id, queue_entry_date, queue_partner_id)
			VALUES
				(:project_id, :candidate_id, NOW(), :queue_partner_id)
		');
		
		$query_insert_into_queue -> execute(array(
			':project_id' 	=> $project_id,
			':candidate_id' => $candidate_id,
			':queue_partner_id' => $parnter_id
		));
		
		$log_desc = "Kandidat: " .$candidate_id. " prebačen u queue za projekat " .$project_id. ". Partner id: ".$parnter_id. ". ";
		$log_type = "0";
		addToLogs($log_desc, $log_type);
	}
}

function pullFromProjectCandidateQueue($candidate_id, $current_project_id){
	Global $db;
	$query_get_first_in_queue = $db -> prepare('
		SELECT queue_project_id, queue_candidate_id, queue_id, queue_partner_id
		FROM idk_project_candidate_queue
		WHERE queue_is_assigned = 0
		AND queue_candidate_id = :candidate_id
		ORDER BY queue_id DESC
	');
	
	$return_value = 0;
	$query_get_first_in_queue -> execute(array(':candidate_id' => $candidate_id));
	if($query_get_first_in_queue -> rowCount()){
		$row_get_first_in_queue = $query_get_first_in_queue -> fetch();
		$queue_project_id 	= $row_get_first_in_queue['queue_project_id'];
		$queue_candidate_id = $row_get_first_in_queue['queue_candidate_id'];
		$queue_id 			= $row_get_first_in_queue['queue_id'];
		$queue_partner_id 	= $row_get_first_in_queue['queue_partner_id'];
		
		$query_insert_into_pk = $db -> prepare('
			INSERT INTO idk_project_kandidati
				(pk_projectid, pk_kandidatid)
			VALUES 
				(:queue_project_id, :queue_candidate_id)
		');
		$query_insert_into_pk -> execute(array(
			':queue_project_id' 	=> $queue_project_id,
			':queue_candidate_id' 	=> $queue_candidate_id
		));

		if($queue_partner_id != null){
			$upd_partner_kandidat = $db->prepare("
				UPDATE idk_kandidati
				SET kandidat_partnerid = :kandidat_partnerid, kandidat_partner_status = :kandidat_partner_status, kandidat_porijeklo = :kandidat_porijeklo
				WHERE kandidat_id = :kandidat_id
			");
			$upd_partner_kandidat->execute(array(
				':kandidat_partnerid' => $queue_partner_id,
				':kandidat_partner_status' => 1,
				':kandidat_porijeklo' => 7,
				':kandidat_id' => $queue_candidate_id
				));
		}
		
		$update_project_candidate_queue = $db -> prepare('
			UPDATE idk_project_candidate_queue
			SET queue_is_assigned = 1
			WHERE queue_id = :queue_id
		');
		$update_project_candidate_queue -> execute(array(':queue_id' => $queue_id));
		
		
		$log_desc = "Kandidat: " .$queue_candidate_id. " prebačen iz queue u projekat " .$queue_project_id. ". Partner ID: ".$partner_id. ". ";
		$log_type = "0";
		addToLogs($log_desc, $log_type);
		addToLogsStatusPrijave($current_project_id, $queue_project_id, getStatusPrijaveByProjectId($queue_project_id), $candidate_id, '4', 1);
		$return_value = 1;
	}
	return $return_value;
}
function getProjectListByNalogId($nalog_id){
	Global $db;
	if($nalog_id != 0){
		$select_query = $db->prepare("
				SELECT project_id, project_name, project_status, project_nalogid
				FROM idk_projects
				WHERE project_status != 0 AND project_nalogid =" .$nalog_id);

		$select_query->execute();
		$return_html = "";
		while($select_row = $select_query->fetch()) {
			$pr_name = $select_row['project_name'];
			if(!strpos($pr_name, 'Intervju')){			
				$return_html.= "<option value='" . $select_row['project_id'] . "'>".$pr_name."</option>";
			}
		}
	}
	else{
		$select_query = $db->prepare("
				SELECT project_id, project_name, project_status, project_nalogid
				FROM idk_projects
				WHERE project_status != 0 AND project_nalogid =0 OR project_nalogid IS NULL");

		$select_query->execute();
		$return_html = "";
		while($select_row = $select_query->fetch()) {
			$return_html.= "<option value='" . $select_row['project_id'] . "'>" . $select_row['project_name'] . "</option>";
		}
	}
	return $return_html;
}

function getCompanyNameById($company_id){
	Global $db;
	if(is_null($company_id)){
		$company_name = 'Nije poznato';
	}
	else{
		$query_get_company_name = $db -> prepare('
			SELECT company_name
			FROM idk_companies
			WHERE company_id = :company_id
		');
		$query_get_company_name -> execute(array(':company_id' => $company_id));
		$row_get_company_name = $query_get_company_name -> fetch();
		$company_name = $row_get_company_name['company_name'];
	}
	return $company_name;
}

function checkIfNalog44byProjectId($project_id){
	Global $db;
	$query_check = $db -> prepare('
		SELECT 
		CASE 
			WHEN project_nalogid = 44
			THEN 1
			ELSE 0
		END as is_nalog44
		FROM idk_projects
		WHERE project_id = :project_id
	');
	$query_check -> execute(array(':project_id' => $project_id));
	$row_check = $query_check -> fetch();
	$is_nalog44 = $row_check['is_nalog44'];
	return $is_nalog44;
}
function checkIfCandidateZaposlen($kandidat_id){
	Global $db;
	$return_value = false;
	$query_get_candidate_status_prijave = $db -> prepare('
		SELECT kandidat_status_prijave
		FROM idk_kandidati
		WHERE kandidat_id = :kandidat_id
	');
	$query_get_candidate_status_prijave -> execute(array(':kandidat_id' => $kandidat_id));
	$row_get_candidate_status_prijave = $query_get_candidate_status_prijave -> fetch();
	$kandidat_status_prijave = $row_get_candidate_status_prijave['kandidat_status_prijave'];
	
	if($kandidat_status_prijave == 4){
		$return_value = true;
	}
	// var_dump($return_value);
	return $return_value;
}
function zavrsenCandidatePossibleNalogIds($kandidat_id){
	Global $db;
	$return_value = false;
	$query_get_candidate_status_prijave = $db -> prepare('
		SELECT kandidat_status_prijave, kandidat_zaposlen_kod
		FROM idk_kandidati
		WHERE kandidat_id = :kandidat_id
	');
	$query_get_candidate_status_prijave -> execute(array(':kandidat_id' => $kandidat_id));
	
	if($query_get_candidate_status_prijave -> rowCount() != 0){
		$row_get_candidate_status_prijave = $query_get_candidate_status_prijave -> fetch();
		$kandidat_status_prijave 	= $row_get_candidate_status_prijave['kandidat_status_prijave'];
		$kandidat_zaposlen_kod 		= $row_get_candidate_status_prijave['kandidat_zaposlen_kod'];
		if($kandidat_status_prijave == 4){
			$array_nalozi = array();
			$query_get_project_names = $db -> prepare('
				SELECT pk.pk_projectid, pr.project_name, pr.project_nalogid
				FROM (
					SELECT sqpk.pk_projectid
					FROM idk_project_kandidati sqpk
					
					WHERE sqpk.pk_kandidatid = :kandidat_id
				) pk
				JOIN idk_projects pr
				ON pr.project_id = pk.pk_projectid
				JOIN(
					SELECT sqn.nalog_id, sqn.kompanija_id
					FROM idk_nalozi sqn
					WHERE sqn.kompanija_id = :kandidat_zaposlen_kod
				) n
				ON n.nalog_id = pr.project_nalogid
				WHERE pr.project_nalogid != 44

			');
			$query_get_project_names-> execute(array(
				':kandidat_id'				=> $kandidat_id,
				':kandidat_zaposlen_kod' 	=> $kandidat_zaposlen_kod,
			));
			while($row_get_project_names = $query_get_project_names -> fetch()){
				$project_name = $row_get_project_names['project_name'];
				if(		
						!strpos($project_name, 'spunjava uslove')
					AND	!strpos($project_name, 'EU kandidati')
					AND	!strpos($project_name, 'Obrađeno')
					AND	!strpos($project_name, 'Intervju')
					AND	!strpos($project_name, 'U obradi')
					AND	!strpos($project_name, 'Prijave')
					AND	!strpos($project_name, 'Casting')
					AND	!strpos($project_name, 'Odbijen')
				){
					array_push($array_nalozi, $row_get_project_names['project_nalogid']);
				}
			}
			// var_dump($array_nalozi);
			if(count($array_nalozi) != 0){
				$return_value = array();
				$return_value = $array_nalozi;
			}
			// var_dump($return_value);
		}
	}
	return $return_value;
}
function getCandidateStatusPrijave($candidate_id){
	Global $db;
	$candidate_status_prijave = 0;
	$query_get_status_prijave = $db -> prepare('
		SELECT kandidat_status_prijave
		FROM idk_kandidati
		WHERE kandidat_id = :candidate_id
	');
	
	$query_get_status_prijave -> execute(array(':candidate_id' => $candidate_id));
	$row_get_status_prijave = $query_get_status_prijave -> fetch();
	$candidate_status_prijave = $row_get_status_prijave['kandidat_status_prijave'];
	
	return $candidate_status_prijave;
}
function updateReminderUgovor($kandidat_id, $reminder_type){
	Global $db;
	Global $logged_employee_id;
	$current_date = date("Y-m-d H:i:s");
	//Kombinacije rezultata
	//101	-	Nisu poslani odgovarajući parametri funkciji
	//102	-	Status koji nije pokriven u funkciji. Pokrivene vrijednosti su: 2, 3
	//103	- 	User id = 0
	
	$get_reminder = $db -> prepare('
		SELECT 
			pr_id 
		FROM 
			idk_pp_reminders
		JOIN
			idk_pp_reminder_settings
		ON
			idk_pp_reminders.pr_reminder_setting_id = idk_pp_reminder_settings.prs_id
		WHERE
			pr_candidate_id = :candidate_id
		AND
			prs_reminder_type_id = :type_id
		AND
			prs_active = 1
	');
	$get_reminder -> execute(array(
		":candidate_id" => $kandidat_id,
		":type_id"		=> $reminder_type
	));
	$result_reminder = $get_reminder -> fetch();
	$reminder_id = $result_reminder['pr_id'];
	if($reminder_type == 5){
		$update_reminder_done = $db -> prepare("
			UPDATE 
				idk_pp_reminders
			SET
				pr_status = 3,
				pr_date_completed = :current_date
			WHERE
				pr_id = :reminder_id
		");
		$update_reminder_done -> execute(array(
			":reminder_id" 	=> $reminder_id,
			":current_date" => $current_date
		));
	}
	elseif($reminder_type == 6){
		$update_reminder_done = $db -> prepare("
			UPDATE 
				idk_pp_reminders
			SET
				pr_status = 3,
				pr_date_completed = :current_date,
				pr_user_assigned = :logged_employee
			WHERE
				pr_id = :reminder_id
		");
		$update_reminder_done -> execute(array(
			":reminder_id" 		=> $reminder_id,
			":current_date" 	=> $current_date,
			":logged_employee" 	=> $logged_employee_id
		));
	}	
}

function getLspDatetime($kandidat_id, $status_id){
	Global $db;

	$get_lsp_datetime = $db->prepare("SELECT 
										lsp_datetime 
									FROM 
										idk_log_statusi_prijave 
									WHERE 
										lsp_kandidat_id = :kandidat_id 
									AND 
										lsp_status_prijave_id = :status_id
									");
	$get_lsp_datetime->execute([
		":kandidat_id" => $kandidat_id,
		":status_id" => $status_id
	]);
	$result = $get_lsp_datetime->fetch();
	if($result["lsp_datetime"] == null){
		$lsp_datetime = "-";
	} else {
		$lsp_datetime = date("d.m.Y", strtotime($result["lsp_datetime"]));
	}
	return $lsp_datetime;
}

function getCheckVisaProcessDocumentsR($idCan){
	Global $db;
	//Funkacija vraća rezultate:
	//		* PORUKE GRESKE		->		100		- 	Neispravno proslijeđen idCan parametar funkciji
	//							->		101 	- 	Kandidat nije ni u jednom nalogu
	//							->		102 	- 	Nije urađen setup dokumenata na taj nalog (nema aktivan ni jedan dokument)
	//		* PRAVI REZULTAT 	->		1		-	Kandidat posjeduje sve dokumente (nalaze se na statusu SPREMAN)
	//							->		0		- 	Kandidat nema sve dokumente

	$idCan = intval($idCan);

	if($idCan != 0){
		$nalogId = intval(getNalogIdByCandidateId($idCan));
		if($nalogId != 0){
			$query = $db->prepare("
				SELECT
					(
						SELECT 
							count(nrd.nrd_id) AS countNrd
						FROM 
							idk_pp_nalog_required_documents nrd
						WHERE 
							nrd.nrd_nalog_id = :nalogId
							AND 
							nrd.nrd_status = 1
					) AS numberRequired, 
					(
						SELECT 
							count(d.doc_id) AS countDoc
						FROM 
							idk_pp_documents d
						WHERE 
							d.doc_candidate_id = :candId
							AND 
							d.doc_nalog_id = :nalogId
							AND 
							d.doc_status = 18
							AND 
							d.doc_nrd_id IN (
								SELECT 
									nrd1.nrd_id
								FROM 
									idk_pp_nalog_required_documents nrd1
								WHERE 
									nrd1.nrd_nalog_id = :nalogId
									AND 
									nrd1.nrd_status = 1
							)
					) AS numberAdded
			");
			$query->execute(array(
				':nalogId' => $nalogId,
				':candId' => $idCan
			));
			$row = $query->fetch();
			$numberRequired = intval($row["numberRequired"]);
			$numberAdded = intval($row["numberAdded"]);

			if($numberRequired != 0){
				if($numberRequired == $numberAdded){
					return 1;
				}else{
					return 0;
				}
			}else{
				return 102;
			}
		}else{
			return 101;
		}
	}else{
		return 100; 
	}
}

function uploadDocumentFileR($file, $location){
	/*
		*********************************************************************************************************
		*									DESCRIPTION OF INPUT PARAMETERS										*
		*********************************************************************************************************
		*	$file 				- example: $_FILES["input_name_for_the_file"];									*
		*	$location			- example: $_SERVER['DOCUMENT_ROOT']."/jobstep_pp/files/candidate_contracts/";	*
		*********************************************************************************************************

		*********************************************************************************************************
		*									DESCRIPTION OF INPUT RESULTS										*
		*********************************************************************************************************
		*	101 				- location is not set															*
		*	102					- the file does not exist or file is not uploaded via HTTP POST					*
		*	103					- invalid file extension														*
		*	0					- no document uploaded															*
		*	$file_destination	- uploaded document																*
		*********************************************************************************************************
	*/

	// START 
    //
    if($location == "" OR $location == NULL)
        throw new Exception("Location is not set!");


    if(!file_exists($file['tmp_name']) && !is_uploaded_file($file['tmp_name']))
        throw new Exception("File does not exist or is not uploaded via HTTP POST!");

    //File properties
    $file_name = $file['name'];
    $file_tmp = $file['tmp_name'];

    //File extension
    $file_ext = explode('.', $file_name);
    $file_ext = strtolower(end($file_ext));

    $allowed = array('jpg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png', 'csv');


    if(!in_array($file_ext, $allowed))
        throw new Exception("Invalid file extension!");


    $file_name_new = uniqid() . '.' . $file_ext;
    $file_destination = $location. $file_name_new; 

    if(move_uploaded_file($file_tmp, $_SERVER["DOCUMENT_ROOT"] . $file_destination)){
        return $file_destination; 
    }else {
        throw new Exception("File could not be uploaded!");
    }

	//END
}

function getNalogPoslodavacTraziJezik($nalogId) {

	Global $db; 
	$nalogId = intval($nalogId);
	$query = $db->prepare("
		SELECT 
			nalog_poslodavac_trazi_jezik
		FROM 
			idk_nalozi 
		WHERE 
			nalog_id = :nalog_id
	");
	$query->execute(array(
		':nalog_id' => $nalogId
	));
	if ($query->rowCount() == 1) {
		$row = $query->fetch();
		return intval($row["nalog_poslodavac_trazi_jezik"]);
	} else {
		return 101;
	}

}

function checkDocumentsForWorkExperience($kandidat_id){
	Global $db;
	$query = $db->prepare("
		SELECT 
			document_id
		FROM 
			idk_documents 
		WHERE 
			document_dataid = :kandidat_id AND document_special_type IN (1,2)
	");
	$query->execute(array(
		':kandidat_id' => $kandidat_id
	));
	if ($query->rowCount() == 2) {
		$row = $query->fetch();
		return 1;
	} else {
		return 0;
	}
}

function prebaciNaPrikupljanjeDokumentacijeZapadniBalkan($kandidat_id){
	Global $db;

	$status_prijave = checkKandidatStatusPrijave($kandidat_id);
	if ($status_prijave != 9){
		if($status_prijave == 12){
			return 'Kandidat je već na statusu Prikupljanja dokumentacije!';
		}else{
			return 'Kandidat nije na statusu prijave Potpisan ugovor!';
		}
	}
	

	$nalog_id = getNalogIdByCandidateId($kandidat_id);
	
	if($nalog_id == null)
		return 'Kandidat nije povezan za nalog! (ZB)';

	/*
		Provjera datuma termina
		*/
			$checkDatumTermina = $db->prepare("SELECT datum_termina FROM idk_kandidati WHERE kandidat_id = :kandidat_id"); 
			$checkDatumTermina->execute(array(':kandidat_id' => $kandidat_id));
			$rowCheckDatumTermina = $checkDatumTermina->fetch();
			$datum_termina = $rowCheckDatumTermina["datum_termina"]; 

			if ($datum_termina == null) {
				return 'Kandidat nema unešen datum termina za način odlaska Zapadni balkan!';
			}
		/*
		Provjera datuma termina
	*/

	$upd_kand_status = $db->prepare("UPDATE idk_kandidati SET kandidat_status_prijave = 12 WHERE kandidat_id = $kandidat_id");
	$upd_kand_status->execute();

	updateLastActiveTaskForCandidate($kandidat_id, 33);

	$get_project = $db->prepare(
			"SELECT project_id FROM idk_projects WHERE project_nalogid = $nalog_id AND project_name LIKE '% - Ugovor' "
	);
	$get_project->execute();
	$row_project = $get_project->fetch();
	$project_id = $row_project['project_id'];
	if($project_id == null)
		return 'Nije nađen projekt! (ZB)';
		
	addToLogsStatusPrijave($project_id, $project_id, 12, $kandidat_id, 1);

	//prebacivanje potpisanog ugovora u dokumente
	prebaciUgovorUDokumente($kandidat_id, $nalog_id);

	return 1;
}

function prebaciNaPrikupljanjeDokumentacijeRadnoIskustvo($kandidat_id){
	Global $db;

	$status_prijave = checkKandidatStatusPrijave($kandidat_id);
	if ($status_prijave != 9){
		if($status_prijave == 12){
			return 'Kandidat je već na statusu Prikupljanja dokumentacije!';
		}else{
			return 'Kandidat nije na statusu prijave Potpisan ugovor!';
		}
	}
	
	$nalog_id = getNalogIdByCandidateId($kandidat_id);
	
	if($nalog_id == null)
		return 'Kandidat nije povezan za nalog! (RI)';

	//Provjera da li su uploadana oba dokumenta koja su potrebna za Radno iskustvo
	$documents_check = checkDocumentsForWorkExperience($kandidat_id);
	if($documents_check == 0)
		return 'Kandidat nema potrebne dokumente kao dokaz za radno iskustvo!';

	$upd_kand_status = $db->prepare("UPDATE idk_kandidati SET kandidat_status_prijave = 12 WHERE kandidat_id = $kandidat_id");
	$upd_kand_status->execute();

	updateLastActiveTaskForCandidate($kandidat_id, 33);
	
	$get_project = $db->prepare(
			"SELECT project_id FROM idk_projects WHERE project_nalogid = $nalog_id AND project_name LIKE '% - Ugovor' "
	);
	$get_project->execute();
	$row_project = $get_project->fetch();
	$project_id = $row_project['project_id'];
	if($project_id == null)
		return 'Nije nađen projekt! (ZB)';
		
	addToLogsStatusPrijave($project_id, $project_id, 12, $kandidat_id, 1);

	//prebacivanje potpisanog ugovora u dokumente
	prebaciUgovorUDokumente($kandidat_id, $nalog_id);

	return 1;
}

function prebaciNaPrikupljanjeDokumentacije($proslijedeni_id, $preko){
	//$preko = 1 -> preko jezika ili uploada ugovora prebacujemo i proslijeđuje se kandidat_id iz tabele idk_kandidati
	//$preko = 2 -> preko dipla prebacujemo i proslijeđuje se id_broj_nd_kandidata iz tabele idk_nd_kandidata
	
	Global $db;

	//provjera drzavljanstva, ako je EU-kandidat nece se prebaciti na status prikupljanja dokumentacije
	if($preko == 1){

		$provjeri_drzavljanstvo_kandidata_query = $db->prepare("SELECT kandidat_drzavljanstvo_vrsta, kandidat_nacin_odlaska FROM idk_kandidati WHERE kandidat_id=:kandidat_id");
		$provjeri_drzavljanstvo_kandidata_query->execute(array(
			':kandidat_id'	=> $proslijedeni_id
		));
		$provjeri_drzavljanstvo_kandidata = $provjeri_drzavljanstvo_kandidata_query->fetch();
		$kandidat_drzavljanstvo_vrsta = $provjeri_drzavljanstvo_kandidata ["kandidat_drzavljanstvo_vrsta"];
		$kandidat_nacin_odlaska = $provjeri_drzavljanstvo_kandidata ["kandidat_nacin_odlaska"];
		if($kandidat_nacin_odlaska == 2){
			$result = prebaciNaPrikupljanjeDokumentacijeZapadniBalkan($proslijedeni_id);
			
			if($result == 1){
				return 1;
			}else{
				throw new Exception($result);
			}
		}elseif($kandidat_nacin_odlaska == 3){
			$result = prebaciNaPrikupljanjeDokumentacijeRadnoIskustvo($proslijedeni_id);
			
			if($result == 1){
				return 1;
			}else{
				throw new Exception($result);
			}
		}
	}else if($preko == 2){
		
		$provjeri_drzavljanstvo_kandidata_query = $db->prepare("SELECT kandidat_drzavljanstvo_vrsta, kandidat_nacin_odlaska FROM idk_kandidati WHERE kandidat_dipl_id=:kandidat_dipl_id");
		$provjeri_drzavljanstvo_kandidata_query->execute(array(
			':kandidat_dipl_id'	=> $proslijedeni_id
		));
		$provjeri_drzavljanstvo_kandidata = $provjeri_drzavljanstvo_kandidata_query->fetch();
		$kandidat_drzavljanstvo_vrsta = $provjeri_drzavljanstvo_kandidata ["kandidat_drzavljanstvo_vrsta"];
		$kandidat_nacin_odlaska = $provjeri_drzavljanstvo_kandidata ["kandidat_nacin_odlaska"];
	}
	if($kandidat_drzavljanstvo_vrsta == "EU državljanin"){
		throw new Exception('Kandidat EU državljanin, preskače proces odlaska do početak rada');
		
	}

	// Radno iskustvo se počinje koristi i ovaj dio code-a koji blokira provjeru više ne treba
	// if ($kandidat_nacin_odlaska == 3){
	// 	throw new Exception('Kandidatu označen način odlaska Radno iskustvo s kojim je blokirano prebacivanje na status Prikupljanje dokumentacije!'); 
	// }
	

	if($preko == 1){
		$kandidat_id = $proslijedeni_id;
		
		//ide provjera statusa nostrifikacije
		
		$get_dipl_status = $db->prepare("
					SELECT status_nd_kandidata, kandidat_ima_nostrifikaciju, id_broj_nd_kandidata 
					FROM idk_nd_kandidata 
					JOIN idk_kandidati ON id_broj_nd_kandidata = kandidat_dipl_id 
					WHERE kandidat_id = $kandidat_id AND kandidat_dipl_id != 0");
		$get_dipl_status->execute();
		$row_dipl_status = $get_dipl_status->fetch();
		
		$kandidat_ima_nostrifikaciju = $row_dipl_status['kandidat_ima_nostrifikaciju'];
		$dipl_id_kandidat = $row_dipl_status['id_broj_nd_kandidata'];
		if($kandidat_ima_nostrifikaciju == 1){
			$dipl_status = 6;
		}else{
			$dipl_status = $row_dipl_status['status_nd_kandidata'];
			if($dipl_status == 7)
				return 2;
				// ako je kandidat povezan na dipl i na arhivi vraca se poruka zaposleniku sa napomenom da odradi oznacavanje nostrifikacije ukoliko je vec ima
		}
	}elseif($preko == 2 or $preko==3){
		//zvace se samo kada se klikne na zavrsen, tako da samo treba naci tog kandidata u idk_kandidati i za njega onda provjeriti jezik
		$get_posao_cand_id = $db->prepare("SELECT kandidat_id FROM idk_kandidati WHERE kandidat_dipl_id = $proslijedeni_id");
		$get_posao_cand_id->execute();
		$row_posao_cand_id = $get_posao_cand_id->fetch();
		$kandidat_id = $row_posao_cand_id['kandidat_id'];
		$dipl_id_kandidat = $proslijedeni_id;
		$dipl_status = 6;
		if($kandidat_id == null)
			throw new Exception('Kandidata nema u poslovima!');
	}else{
		throw new Exception('Neispravno proslijeđeni parametri!');
	}
	if($dipl_status == 6){

		//Provjera da li je uploadana nostrifikacija
		$check_nost_upload = $db->prepare("SELECT id_nd FROM idk_nostrifikovane_diplome WHERE (id_cand_job = :id_cand OR id_cand_dipl = :id_cand_dipl)");
		$check_nost_upload->execute(array(
				":id_cand" => $kandidat_id,
				":id_cand_dipl" => $dipl_id_kandidat
		));
		if($check_nost_upload->rowCount() > 0){

			/*
				Check full_recognition From idk_nostrifikovane_diplome START 
			*/
				$full_recognition = null;

				$check_full_recognition = $db->prepare("
					SELECT 
						full_recognition
					FROM 
						idk_nostrifikovane_diplome 
					WHERE 
						(id_cand_job = :id_cand OR id_cand_dipl = :id_cand_dipl)
				");
				$check_full_recognition->execute(array(
					':id_cand' => $kandidat_id,
					":id_cand_dipl" => $dipl_id_kandidat
				));

				if ( $check_full_recognition->rowCount() == 1 ){
					
					$row_full_recognition = $check_full_recognition->fetch();

					$full_recognition = $row_full_recognition["full_recognition"];

				}
			/*
				Check full_recognition From idk_nostrifikovane_diplome END 
			*/

			$status_prijave = getCandidateStatusPrijave($kandidat_id);
			if($status_prijave != 9)
				throw new Exception('Kandidat nije na potrebnom statusu: Potpisan ugovor!');	
			
			$nalog_id = getNalogIdByCandidateId($kandidat_id);
			if($nalog_id == null)
				throw new Exception('Kandidat nije povezan za nalog!');
			
			$minimalni_jezik = "A2";
			
			$poslodavac_trazi_jezik = getNalogPoslodavacTraziJezik($nalog_id);

			/*
				Dio koda unutar if-a ispod će biti izvršen samo ako je:
					- full recognition null ili 0: null (što znaci da kandidatu nije označeno je li potpuna ili nepotpuna); 0 (kandidat ima nepotpunu nostrifikaciju)
				
				Dio koda neće biti izvršen u slucaju kada je varijabla $full_recognition 1 - što znači da kandidat ima potpuno priznatu diplomu
				Na taj način preskočit će se provjera jezika koja se nalazi unutar koda i preći na dio koda ispod koji prebacuje kandidata na status 12 (PD)
			*/
			
			if($minimalni_jezik == "A2" AND ( $full_recognition == null OR $full_recognition == 0 OR (in_array($full_recognition, array(2,1)) AND $poslodavac_trazi_jezik == 1))){
				$get_nivo_status = $db->prepare(
					"SELECT cvl_status, kj_slusanje 
					FROM idk_candidate_verified_languages n 
					JOIN idk_kandidat_jezici j ON n.cvl_id = j.kj_id
					WHERE j.kj_kandidatid = :kandidat_id AND cvl_active=1"
				);
				$get_nivo_status->execute(array(
					':kandidat_id' => $kandidat_id
				));
				$row_nivo = $get_nivo_status->fetch();
				$status_jezika = $row_nivo['cvl_status'];
				$znanje_jezika = $row_nivo['kj_slusanje'];
				
				if($status_jezika == 6 OR $status_jezika == 7 OR $status_jezika == 8){
					$minimalni_jezik_id=2;
					switch($znanje_jezika){
						case "A1": $kandidat_znanje_njem = 1; break;
						case "A2": $kandidat_znanje_njem = 2; break;
						case "B1": $kandidat_znanje_njem = 3; break;
						case "B2": $kandidat_znanje_njem = 4; break;
						case "C1": $kandidat_znanje_njem = 5; break;
						case "C2": $kandidat_znanje_njem = 6; break;
						default:   $kandidat_znanje_njem = 0;
					}
					if($kandidat_znanje_njem < $minimalni_jezik_id){
						throw new Exception('Kandidat nema dovoljno poznavanje jezika!');
					}else{
						//ide dalje prebacivanje
					}
				}else{
					throw new Exception('Status nivoa jezika je manji nego sto treba!');
				}
			}
			/*else if($minimalni_jezik!="A2"){
				
				$get_nivo_status = $db->prepare(
						"SELECT cvl_status, kj_slusanje 
						FROM idk_candidate_verified_languages n 
						JOIN idk_kandidat_jezici j ON n.cvl_id = j.kj_id
						WHERE j.kj_kandidatid = :kandidat_id AND cvl_active=1"
				);
				$get_nivo_status->execute(array(
					':kandidat_id' => $kandidat_id
				));
				$row_nivo = $get_nivo_status->fetch();
				$status_jezika = $row_nivo['cvl_status'];
				$znanje_jezika = $row_nivo['kj_slusanje'];
				
				if($status_jezika == 6 OR $status_jezika == 7 OR $status_jezika == 8){
					switch($minimalni_jezik){
						case "A1": $min_njemacki_broj = 1; break;
						case "A2": $min_njemacki_broj = 2; break;
						case "B1": $min_njemacki_broj = 3; break;
						case "B2": $min_njemacki_broj = 4; break;
						case "C1": $min_njemacki_broj = 5; break;
						case "C2": $min_njemacki_broj = 6; break;
						default:   $min_njemacki_broj = 0;
					}
					switch($znanje_jezika){
						case "A1": $kandidat_znanje_njem = 1; break;
						case "A2": $kandidat_znanje_njem = 2; break;
						case "B1": $kandidat_znanje_njem = 3; break;
						case "B2": $kandidat_znanje_njem = 4; break;
						case "C1": $kandidat_znanje_njem = 5; break;
						case "C2": $kandidat_znanje_njem = 6; break;
						default:   $kandidat_znanje_njem = 0;
					}
					if($kandidat_znanje_njem < $min_njemacki_broj){
						throw new Exception('Kandidat nema dovoljno poznavanje jezika!');
					}else{
						//ide dalje prebacivanje
					}
				}else{
					throw new Exception('Status nivoa jezika je manji nego sto treba!');
				}
			
			}*/
				
			//moze prebacivanje
			//fja za prebacivanje
			$upd_kand_status = $db->prepare("UPDATE idk_kandidati SET kandidat_status_prijave = 12 WHERE kandidat_id = $kandidat_id");
			$upd_kand_status->execute();
			$get_project = $db->prepare(
					"SELECT project_id FROM idk_projects WHERE project_nalogid = $nalog_id AND project_name LIKE '% - Ugovor' "
			);
			$get_project->execute();
			$row_project = $get_project->fetch();
			$project_id = $row_project['project_id'];
			if($project_id == null)
				throw new Exception('Nije nađen projekt!');
				
			addToLogsStatusPrijave($project_id, $project_id, 12, $kandidat_id, 1);

			//prebacivanje potpisanog ugovora u dokumente
			prebaciUgovorUDokumente($kandidat_id, $nalog_id);
			
			return 1;
			
		}else{
			throw new Exception('Kandidata nema uploadovanu diplomu!');
		}
	}elseif($dipl_status == null){
		throw new Exception('Kandidata nema na DIPL-u!');
	}else{
		throw new Exception('Nostrifikacija jos nije gotova!');
	}
}

function prebaciUgovorUDokumente($kandidat_id, $nalog_id){
	//funkcija za prebacivanje potpisanog ugovora u tabelu pp_documents
	Global $db;
	Global $logged_employee_id;
	$get_ugovor = $db->prepare("
		SELECT kc_file_name, kc_partner_id, kc_source, kc_user_id, kc_upload_time 
		FROM idk_kandidati_contracts 
		WHERE kc_candidate_id = $kandidat_id AND kc_signed = 1 AND kc_visibility_status = 2 AND kc_nalog_id = $nalog_id
	");
	$get_ugovor->execute();
	$row_ugovor = $get_ugovor->fetch();
	if($get_ugovor->rowCount() > 0){
		$ugovor_path = "/jobstep_pp/files/candidate_contracts/".$row_ugovor['kc_file_name'];
		$kc_partner_id = $row_ugovor['kc_partner_id'];
		$kc_source = $row_ugovor['kc_source'];
		$kc_user_id = $row_ugovor['kc_user_id'];
		$kc_upload_time = $row_ugovor['kc_upload_time'];

		$get_nrd_id = $db->prepare("SELECT nrd_id FROM idk_pp_nalog_required_documents WHERE nrd_nalog_id = $nalog_id AND nrd_type_id = 1 AND nrd_status = 1");
		$get_nrd_id->execute();
		
		if($get_nrd_id->rowCount() == 0){
			//ako nije setovan ugovor za dokumente onda ce se ovdje dodati, i to ce se odraditi samo kod prebacivanja prvog kandidata u tom nalogu
			$queryInsert = $db->prepare("
				INSERT INTO idk_pp_nalog_required_documents
				(
					nrd_nalog_id,nrd_type_id,nrd_done_by,nrd_status,nrd_date_activated,nrd_user_activated,nrd_west_balkan,nrd_work_experience
				)
				VALUES(
					:nrd_nalog_id,:nrd_type_id,:nrd_done_by,:nrd_status,:nrd_date_activated,:nrd_user_activated,:nrd_west_balkan,:nrd_work_experience
				)
			");
			$queryInsert->execute(array(
				':nrd_nalog_id' => $nalog_id,
				':nrd_type_id' => 1,
				':nrd_done_by' => 2,
				':nrd_status' => 1,
				':nrd_date_activated' => date("Y-m-d H:i:s"),
				':nrd_user_activated' => $logged_employee_id,
				':nrd_west_balkan' => 1,
				':nrd_work_experience' => 1
			));
			$nrd_id = $db->lastInsertId();
		}else{
			$row_nrd_id = $get_nrd_id->fetch();
			$nrd_id = $row_nrd_id['nrd_id'];
		}

		$insert_ugovor = $db->prepare("
						INSERT INTO idk_pp_documents
							(doc_file_name, doc_candidate_id, doc_nalog_id, doc_partner_id, doc_nrd_id, doc_status)
						VALUES
							(:doc_file_name, :doc_candidate_id, :doc_nalog_id, :doc_partner_id, :doc_nrd_id, :doc_status)");

		$insert_ugovor->execute(array(
						':doc_file_name' => $ugovor_path,
						':doc_candidate_id' => $kandidat_id,
						':doc_nalog_id' => $nalog_id,
						':doc_partner_id' => $kc_partner_id,
						':doc_nrd_id' => $nrd_id,
						':doc_status' => 18
					));
		$document_id = $db->lastInsertId();
			//potrebni napraviti niz sa podacima o logovima statusa i ispratiti source i user id i sve to u while i u jedan insert
		$document_status = array(3,6,9,12,15,18);
		if($kc_source == 1){
			$dsl_user_id = $kc_user_id;
			$dsl_pp_user_id = null;
		}else{
			$dsl_user_id = null;
			$dsl_pp_user_id = $kc_user_id;
		}
		$days_count = array(0,0,0,0,0,null);
		$dsl_comment = "Automatski prebačeno pri prelasku na prikupljanje dokumentacije";
		$dsl_comments = array("", $dsl_comment, $dsl_comment, $dsl_comment, $dsl_comment, $dsl_comment);
		for($br=0;$br<6;$br++){
			$insert_log_docs = $db->prepare("
					INSERT INTO idk_pp_documents_status_logs
						(dsl_doc_id, dsl_doc_status, dsl_date, dsl_user_id, dsl_pp_user_id, dsl_days_count, dsl_comment)
					VALUES
						(:dsl_doc_id, :dsl_doc_status, :dsl_date, :dsl_user_id, :dsl_pp_user_id, :dsl_days_count, :dsl_comment)");

			$insert_log_docs->execute(array(
					':dsl_doc_id' => $document_id,
					':dsl_doc_status' => $document_status[$br],
					':dsl_date' => $kc_upload_time,
					':dsl_user_id' => $dsl_user_id,
					':dsl_pp_user_id' => $dsl_pp_user_id,
					':dsl_days_count' => $days_count[$br],
					':dsl_comment' => $dsl_comments[$br]
				));
		}
	}
}

function getNostrificationDocumentInOtherDocuments($idCanDipl, $idCanJob){
	Global $db;

	$idCanDipl = intval($idCanDipl);
	$idCanJob = intval($idCanJob);

	$resultArray = array(
		"count" => array(),
		"candidateIdDipl" => array(),
		"candidateIdJob" => array(),
		"documentId" => array(),
		"documentName" => array(),
		"documentFile" => array()
	);

	if($idCanDipl != 0 AND $idCanJob != 0){
		$query = $db->prepare("
			SELECT 
				d.id_dokument_nd, d.naziv_dokument_nd, d.naziv_dokument_ostali_nd
			FROM 
				idk_nd_kandidata_dokumenti d
			INNER JOIN 
				idk_nd_kandidata dk
			ON 
				dk.id_broj_nd_kandidata = d.id_kandidata_dokument_nd
			INNER JOIN 
				idk_kandidati jk
			ON
				jk.kandidat_dipl_id = dk.id_broj_nd_kandidata
			WHERE 
				d.status_dokument_nd is null
				AND 
				d.naziv_dokument_ostali_nd is not null
				AND 
				d.tip_dokumenta is null
				AND 
				d.tip_dokumenta_status = 1
				AND 
				d.broj_rate = 1
				AND 
				(
					d.naziv_dokument_ostali_nd LIKE '%Gleichwertigkeitsbescheid%' 
					OR 
					(
						d.naziv_dokument_ostali_nd LIKE 'Bescheid%'
						AND
						d.naziv_dokument_ostali_nd NOT LIKE '%über%'
						AND 
						d.naziv_dokument_ostali_nd NOT LIKE '%Gleichwertigkeitsfest%'
					)
					OR 
					d.naziv_dokument_ostali_nd LIKE '%Završena nostrifikacija%'
				)
				AND 
				dk.id_broj_nd_kandidata = :diplId
				AND 
				jk.kandidat_id = :idCanJob
		");
		$query->execute(array(
			":diplId" => $idCanDipl,
			":idCanJob" => $idCanJob
		));
		if($query->rowCount() != 0){
			$count = 0;
			while($row = $query->fetch()){

				$documentId = intval($row["id_dokument_nd"]);
				$documentName = $row["naziv_dokument_ostali_nd"];
				$documentFile = '/files/dokumenti_ND_kandidat/'.$row["naziv_dokument_nd"];

				array_push($resultArray["count"], $count);
				array_push($resultArray["candidateIdDipl"], $idCanDipl);
				array_push($resultArray["candidateIdJob"], $idCanJob);
				array_push($resultArray["documentId"], $documentId);
				array_push($resultArray["documentName"], $documentName);
				array_push($resultArray["documentFile"], $documentFile);

				$count++;
			}
			return $resultArray;
		}else{
			return $resultArray;
		}
	}else{
		return $resultArray;
	}
}

function getNalogDocs($nalog_id, $kandidat_id)
{
    Global $db;
	
	$departure_type = getCandidateDepartureType($kandidat_id);
	$full_recognition = getCandidateFullRecognition($kandidat_id);
	$sql_departure = "";
	if($departure_type[0] == 2 OR $full_recognition == 1 OR $full_recognition == 2){
		$sql_departure = "AND nrd_west_balkan = 1";
	} else if ($departure_type[0] == 3) {
		$sql_departure = "AND nrd_work_experience = 1";
	}else if($departure_type[0] == 0) {
		$sql_departure = "AND nrd_skilled_candidates = 1";
	}

    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "
        SELECT 
            doc_type_name as name,
            nrd_id,
            nrd_done_by as zaduzeni,
            doc_id as id,
            doc_file_name as filename,
            nrd_nalog_id as nalog_id
        FROM 
            (SELECT * FROM idk_pp_nalog_required_documents WHERE nrd_nalog_id = :nalog_id AND nrd_status = 1 $sql_departure) as nrd_docs
        INNER JOIN idk_pp_document_types on nrd_type_id = doc_type_id
        LEFT JOIN
            (SELECT * FROM idk_pp_documents WHERE idk_pp_documents.doc_status != 27) as docs
        ON
                    docs.doc_nalog_id = :nalog_id
                AND
                    docs.doc_candidate_id = :kandidat_id
                AND
                    docs.doc_nrd_id = nrd_docs.nrd_id
        ORDER BY nrd_done_by, doc_type_name
    ";
    $query = $db->prepare($sql);
    $query->execute([":nalog_id" => $nalog_id, ":kandidat_id" => $kandidat_id]);
    $nalog_docs = $query->fetchAll(PDO::FETCH_CLASS);

    return $nalog_docs;
}
function addStatusesToDocument($docs, $kandidat_id)
{
    // Svakom document objektu dodajemo niz koji sadrži sve statuse.
    // Ukoliko je dokument nekad bio na statusu, u tom nizu se nalazi
    // objekt status koji sadrži informacije o statusu.
    //
    // Ukoliko nije bio na tom statusu, u niz se dodaje null vrijednost.

    $statusi = getAllDocumentStatuses();
    foreach($docs as &$doc)
    {
            $doc->statuses=[];
            $doc->kandidat_id = $kandidat_id;
            $postojeciStatusi = getExistingStatuses($doc->id);

            switch($doc->zaduzeni)
            {
                case "1":
                    $doc->zaduzeni = "Poslodavac";
                    break;
                case "2":
                    $doc->zaduzeni = "Kandidat";
                    break;
                case "3":
                    $doc->zaduzeni = "Jobstep";
                    break;
            }

            //var_dump($postojeciStatusi);
            foreach($statusi as $status)
            {
                $postojeciStatus = array_filter($postojeciStatusi, function($s) use($status){return $s->status == $status->ds_id;});

                if($postojeciStatus)
                {
                    $doc->statuses[camelCase($status->ds_name)] = reset($postojeciStatus);
                }
                else
                {
                    $doc->statuses[camelCase($status->ds_name)] = null;
                }
            }
    }

}
function getKandidatDocs($nalog_id, $kandidat_id, $incomplete_id = null)
{
    Global $db;
	$sql = "";
	if($incomplete_id)
	{
		$sql = "
			SELECT 
				doc_type_name as name,
				crd_id,
				crd_done_by as zaduzeni,
				doc_id as id,
				doc_file_name as filename,
				crd_nalog_id as nalog_id
			FROM 
				(SELECT * FROM idk_pp_cand_required_documents WHERE crd_nalog_id = :nalog_id AND crd_status = 1) as crd_docs
			INNER JOIN 
				(SELECT * FROM idk_pp_visa_incomplete WHERE vi_cand_id = :kandidat_id AND vi_nalog_id = :nalog_id AND vi_id = :incomplete_id) as dopune
			ON crd_docs.crd_vi_id = dopune.vi_id
			INNER JOIN idk_pp_document_types on crd_type_id = doc_type_id
			LEFT JOIN
				(SELECT * FROM idk_pp_documents WHERE idk_pp_documents.doc_status != 27) as docs
			ON
						docs.doc_nalog_id = :nalog_id
					AND
						docs.doc_candidate_id = :kandidat_id
					AND
						docs.doc_crd_id = crd_docs.crd_id
			ORDER BY crd_done_by, doc_type_name
    ";
		$query = $db->prepare($sql);
		$query->execute([":nalog_id" => $nalog_id, ":kandidat_id" => $kandidat_id, ":incomplete_id" => $incomplete_id]);
		$kandidat_docs = $query->fetchAll(PDO::FETCH_CLASS);

		return $kandidat_docs;
	}
	else{
		$sql = "
			SELECT 
				doc_type_name as name,
				crd_id,
				crd_done_by as zaduzeni,
				doc_id as id,
				doc_file_name as filename,
				crd_nalog_id as nalog_id
			FROM 
				(SELECT * FROM idk_pp_cand_required_documents WHERE crd_nalog_id = :nalog_id AND crd_status = 1) as crd_docs
			INNER JOIN 
				(SELECT * FROM idk_pp_visa_incomplete WHERE vi_cand_id = :kandidat_id AND vi_nalog_id = :nalog_id AND vi_status = 1) as dopune
			ON crd_docs.crd_vi_id = dopune.vi_id
			INNER JOIN idk_pp_document_types on crd_type_id = doc_type_id
			LEFT JOIN
				(SELECT * FROM idk_pp_documents WHERE idk_pp_documents.doc_status != 27) as docs
			ON
						docs.doc_nalog_id = :nalog_id
					AND
						docs.doc_candidate_id = :kandidat_id
					AND
						docs.doc_crd_id = crd_docs.crd_id
			ORDER BY crd_done_by, doc_type_name
    ";
		$query = $db->prepare($sql);
		$query->execute([":nalog_id" => $nalog_id, ":kandidat_id" => $kandidat_id, ":incomplete_id" => $incomplete_id]);
		$kandidat_docs = $query->fetchAll(PDO::FETCH_CLASS);

		return $kandidat_docs;
	
	}
}

function getAllDocumentStatuses()
{
    Global $db;
    $sql = "SELECT ds_id, ds_name FROM idk_pp_documents_statuses";
    $query = $db->prepare($sql);
    $query->execute();
    $statusi = $query->fetchAll(PDO::FETCH_CLASS);

    return $statusi;

}

function getExistingStatuses($doc_id)
{
    Global $db;

    $sql = "SELECT dsl_date as date, dsl_days_count as days, dsl_comment as comment, dsl_doc_status as status FROM idk_pp_documents_status_logs WHERE dsl_doc_id = :doc_id";
    $query = $db->prepare($sql);
    $query->execute([":doc_id"=>$doc_id]);
    $postojeciStatusi = $query->fetchAll(PDO::FETCH_CLASS);

    return $postojeciStatusi;
}
function camelCase($str, array $noStrip = [])
{
    // non-alpha and non-numeric characters become spaces
    $str = str_replace(["ć", "č", "Ć", "Č"],		"c",	$str);
    $str = str_replace(["š", "Š"],					"s",	$str);
    $str = str_replace(["ž","Ž"],					"z",	$str);
    $str = str_replace(["đ","Đ"],					"d",	$str);

    $str = preg_replace('/[^a-z0-9' . implode("", $noStrip) . ']+/i', ' ', $str);
    $str = trim($str);

    // uppercase the first character of each word
    $str = ucwords($str);
    $str = str_replace(" ", "", $str);
    $str = lcfirst($str);

    return $str;
}

function getProjectForCandidatR($idCan, $idNalog){
		Global $db; 
		$idCan = intval($idCan);
		$idNalog = intval($idNalog);
		$idProject = 0;
		if($idCan != 0 AND $idNalog != 0){
			$query = $db->prepare("
				SELECT 
					project_id 
				FROM 
					idk_projects
				JOIN
					idk_project_kandidati
				ON
					idk_projects.project_id = idk_project_kandidati.pk_projectid
				WHERE
					idk_project_kandidati.pk_kandidatid = :kandidat_id
				AND
					idk_projects.project_nalogid = :nalog_id
				ORDER BY idk_projects.project_id DESC
			");
			$query->execute(array(
				":kandidat_id" 	=> $idCan,
				":nalog_id"		=> $idNalog
			));
			if($query->rowCount() != 0){
				$row = $query->fetch();
				$idProject = intval($row['project_id']);
			}else{
				$idProject = 0;
			}
		}else{
			$idProject = 0;
		}
		return $idProject;
	}
function logDateDifference($cvl_id){
	Global $db;
	$query = $db->prepare("
						SELECT cll_id,cll_date 
						FROM idk_candidate_language_logs
						WHERE cll_cvl_id = :cvl_id AND cll_days_count is null
						");
	$query->execute(array(
		':cvl_id' => $cvl_id
		
		));
	$select_row = $query->fetch();
	$cll_date=strtotime($select_row['cll_date']);	
	$cll_id=$select_row['cll_id'];
	
	$now=strtotime(date('Y-m-d H:i:s'));
	$dateDifference=round(($now-$cll_date)/(60*60*24));
	$insertDiff= $db->prepare("
			 UPDATE idk_candidate_language_logs SET cll_days_count =:dateDifference  WHERE `cll_id` = :cll_id;
	");
	$insertDiff->execute(array(
			':dateDifference'=>$dateDifference,
			':cll_id'=>$cll_id			

	));	
				
}
function prebaci_na_status($kandidat_id,$na_status){
	//Proslijediti id kandidata i na koji status ga treba prebaciti
	Global $db;

	$query=$db->prepare("
		UPDATE idk_kandidati set kandidat_status_prijave=:na_status where kandidat_id=:kandidat_id	
	");
	$query->execute(array(
		':kandidat_id'=>$kandidat_id,
		':na_status'=>$na_status
	));
	$nalog_id = getNalogIdByCandidateId($kandidat_id);
	$project_id = getProjectForCandidatR($kandidat_id, $nalog_id);
	addToLogsStatusPrijave($project_id, $project_id, $na_status, $kandidat_id, 1);

	
}
function getDocumentStatus($doc_id)
{
	Global $db;
	$statusSQL = "SELECT doc_status FROM idk_pp_documents WHERE doc_id = :doc_id";
	$query = $db->prepare($statusSQL);
	$query->execute([":doc_id" => $doc_id]);

    $result = $query->fetch();
    $broj = intval($result["doc_status"]);

	return $broj;
}

function preskocenKorak($new_status, $doc_id)
{
	$current_status = getDocumentStatus($doc_id);


	if($new_status == 3)
	{
		// Ako je upload, nisu preskoceni koraci jer je upload prvi korak.
		return false;
	}

	
	// Za svaki novi status, provjeriti da li je trenutni
	// status njegov prethodni. Kako su trenutno u bazi
	// ID-evi statusa 3,6,9,12,... dovoljno je provjeriti
	// da li je njihova razlika jednaka broju 3.
	// Takav način nije dovoljno generalan, ali je jednostavniji.

	if($new_status - $current_status == 3)
	{
		return false;
	}
	else
	{
		return true;
	}

	/*
		* U slučaju da se nekad promijene ID-evi statusa
		* ovaj kod je ispravniji i lakši za specijalizaciju.

	if($new_status == 6 && $current_status != 3)
	{
		return true;
	}

	if($new_status == 9 && $current_status != 6)
	{
		return true;
	}

	if($new_status == 12 && $current_status != 9)
	{
		return true;
	}

	if($new_status == 15 && $current_status != 12)
	{
		return true;
	}
	if($new_status == 18 && $current_status != 15)
	{
		return true;
	}
	*/
}


function updatePreviousDocumentStatusNumberOfDays($doc_id)
{
	Global $db;
	$days_past_SQL = "SELECT
							DATEDIFF(CURRENT_DATE(), dsl_date) AS days_past,
							dsl_doc_status as current_status
						FROM
							`idk_pp_documents`
						INNER JOIN `idk_pp_documents_status_logs` ON doc_id = dsl_doc_id AND doc_status = dsl_doc_status
						WHERE
							doc_id = :doc_id";
	$days_past_query = $db->prepare($days_past_SQL);

	$days_past_query->execute([
		":doc_id" => $doc_id
	]);

	$rows = $days_past_query->fetch();
	$days_past = $rows["days_past"];
	$current_status = $rows["current_status"];

	

	$update_days_SQL = "UPDATE
							idk_pp_documents_status_logs 
						SET 
							dsl_days_count = :days_past 
						WHERE 
							dsl_doc_id = :doc_id AND 
							dsl_doc_status = :current_status";
	$update_days_query = $db->prepare($update_days_SQL);

	$update_days_query->execute([
		":days_past" 		=> $days_past,
		":doc_id" 			=> $doc_id,
		":current_status"	=> $current_status
	]);
	
}

function changeStatusAndInsertLog($doc_id, $new_status_id, $status_comment, $employee_id, $pp_id)
{
	Global $db;
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	if($new_status_id != 3)
	{
		updatePreviousDocumentStatusNumberOfDays($doc_id);
	}

	try {
		$db->beginTransaction();

		$doc_status_SQL = "UPDATE idk_pp_documents SET doc_status = :doc_status WHERE doc_id = :doc_id";
		$doc_status_query = $db->prepare($doc_status_SQL);
		$doc_status_query->execute([
			":doc_status" => $new_status_id,
			":doc_id" => $doc_id
		]);



		$doc_status_log_SQL = "INSERT INTO idk_pp_documents_status_logs VALUES (:doc_id, :new_status, :date, :employee_id, :pp_id, :days, :comment)";
		$doc_status_log_query = $db->prepare($doc_status_log_SQL);
		$doc_status_log_query->execute([
			":doc_id"       => $doc_id,
			":new_status"   => $new_status_id,
			":date"         => date("Y-m-d H:i:s"),
			":employee_id"  => $employee_id,
			":pp_id"        => $pp_id,
			":days"         => NULL,
			":comment"      => $status_comment 
		]);

		$db->commit();
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);

	} catch (\PDOException $e) {
		// rollback the transaction
		$db->rollBack();
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);

		http_response_code(500);
		// show the error message
		die($e->getMessage());
	}

}

function insertDocument($filepath, $kandidat_id, $nalog_id, $partner_id, $nrd_id, $crd_id)
{
	Global $db;


	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$insert_document_SQL = "INSERT INTO idk_pp_documents (doc_file_name, doc_candidate_id, doc_nalog_id, doc_partner_id, doc_nrd_id, doc_crd_id, doc_status) VALUES (:filepath, :kandidat_id, :nalog_id, :partner_id, :nrd_id, :crd_id, 3)";

	$query = $db->prepare($insert_document_SQL);
	$query->execute([
		":filepath"     => $filepath,
		":kandidat_id"  => $kandidat_id,
		":nalog_id"     => $nalog_id,
		":partner_id"   => $partner_id,
		":nrd_id"       => $nrd_id,
		":crd_id"       => $crd_id
	]);
	
	return $db->lastInsertId();
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
}

function documentAlreadyExistsAndIsValid($kandidat_id, $nalog_id, $crd_id, $nrd_id)
{
	Global $db;
	$sql = "SELECT 
				doc_id
			FROM idk_pp_documents 
			WHERE 
				doc_candidate_id  = :kandidat_id    AND 
				doc_nalog_id      = :nalog_id       AND
				(
					doc_nrd_id = :nrd_id OR
					doc_crd_id = :crd_id
				)                                   AND
				doc_status NOT IN (21,24,27)
			";

	$query = $db->prepare($sql);

	$query->execute([
		":kandidat_id" => $kandidat_id,
		":nalog_id" => $nalog_id,
		":nrd_id" => $nrd_id,
		":crd_id" => $crd_id
	]);


	$rows = $query->fetchAll();
	if(count($rows) > 0)
	{
		return true;
	}

	return false;
}

function getDocumentTypeNameR($typeId, $language){
	Global $db;
	//language -> 1 - BIH, 2 - DE
	$typeId = intval($typeId);
	$language = intval($language);
	$langSql = "";
	$result = "";
	if($typeId != 0 AND $language != 0){
		if($language == 2){
			$langSql = "doc_type_name_de";
		}else{
			$langSql = "doc_type_name";
		}
		$query = $db->prepare("
			SELECT 
				".$langSql." AS name
			FROM 
				idk_pp_document_types
			WHERE 
				doc_type_id = :typeId
		");
		$query->execute(array(
			':typeId' => $typeId
		));
		$row = $query->fetch();
		$result = $row["name"];
		return $result;
	}else{
		return "undefined";
	}
}

function getActiveVisaIncompleteR($candId, $typeVi){
	Global $db; 
	$statusSql = 0;
	$candidateId = intval($candId);
	$typeVisaIncomplete = intval($typeVi);
	if($typeVisaIncomplete == 1){
		$statusSql = 21;
	}else{
		$statusSql = 24;
	}
	$nalogId = intval(getNalogIdByCandidateId($candidateId));
	$partnerId = intval(getPartnerIdByCandidateId($candidateId));
	if($candidateId != 0 AND $nalogId != 0 AND $partnerId != 0 AND ($typeVisaIncomplete == 1 OR $typeVisaIncomplete == 2)){
		$query = $db->prepare("
			SELECT
				vi.vi_id
			FROM 
				idk_pp_visa_incomplete vi
			INNER JOIN 
				idk_kandidati k
			ON 
				vi.vi_cand_id = k.kandidat_id 
			WHERE 
				k.kandidat_nalog_id = :nalogId
				AND 
				k.kandidat_ppa_partner_id = :partnerId
				AND 
				vi.vi_nalog_id = :nalogId
				AND 
				vi.vi_pp_partner_id = :partnerId
				AND 
				k.kandidat_status_prijave = :statusPrijave
				AND 
				vi.vi_cand_id = :candidateId
				AND 
				vi.vi_type = :typeVi
				AND
				vi.vi_status = 1
		");
		$query->execute(array(
			':nalogId' => $nalogId, 
			':partnerId' => $partnerId,
			':statusPrijave' => $statusSql,
			':candidateId' => $candidateId,
			':typeVi' => $typeVisaIncomplete
		));
		if($query->rowCount() == 1){
			$row = $query->fetch();
			return intval($row["vi_id"]);
		}else{
			return 0;
		}
	}else{
		return 0;
	}
}

function getDetailsForVisaIncompleteArrayR($viId){
	Global $db;
	$result = array(
		"count" =>array(),
		"vi_id" => array(),
		"vi_cand_id" => array(),
		"vi_type" => array(),
		"vi_status" => array(),
		"vi_date_received_candidate" => array(),
		"vi_date_we_received_candidate" => array(),
		"vi_deadline_date_candidate" => array(),
		"vi_date_received_employer" => array(),
		"vi_date_we_received_employer" => array(),
		"vi_deadline_date_employer" => array(),
		"vi_nalog_id" => array(),
		"vi_pp_partner_id" => array()
	);

	$visaIncompleteId = intval($viId);

	if($visaIncompleteId != 0){
		$query = $db->prepare("
			SELECT 
				*
			FROM 
				idk_pp_visa_incomplete
			WHERE 
				vi_id = :vi_id
		");
		$query->execute(array(
			':vi_id' => $visaIncompleteId
		));
		if($query->rowCount() == 1){
			$row = $query->fetch();
			array_push($result["count"], 0);
			array_push($result["vi_id"], $row["vi_id"]);
			array_push($result["vi_cand_id"], $row["vi_cand_id"]);
			array_push($result["vi_type"], $row["vi_type"]);
			array_push($result["vi_status"], $row["vi_status"]);
			array_push($result["vi_date_received_candidate"], $row["vi_date_received_candidate"]);
			array_push($result["vi_date_we_received_candidate"], $row["vi_date_we_received_candidate"]);
			array_push($result["vi_deadline_date_candidate"], $row["vi_deadline_date_candidate"]);
			array_push($result["vi_date_received_employer"], $row["vi_date_received_employer"]);
			array_push($result["vi_date_we_received_employer"], $row["vi_date_we_received_employer"]);
			array_push($result["vi_deadline_date_employer"], $row["vi_deadline_date_employer"]);
			array_push($result["vi_nalog_id"], $row["vi_nalog_id"]);
			array_push($result["vi_pp_partner_id"], $row["vi_pp_partner_id"]);
		}
	}

	return $result;
}

function archiveDocumentTypeForCandidate($kandidatId, $nalogId, $typeId){
	Global $db;
	Global $logged_employee_id;
	$kandidatId = intval($kandidatId);
	$nalogId = intval($nalogId);
	$typeId = intval($typeId);
	$docIdsArray = array();
	$docIds = "";
	if($kandidatId != 0 AND $nalogId != 0 AND $typeId != 0){
		//Nalog Required Documents Check
		$queryCheck1 = $db->prepare("
			SELECT 
				d.doc_id
			FROM 
				idk_pp_documents d
			INNER JOIN 
				idk_pp_nalog_required_documents nrd
			ON 
				d.doc_nrd_id = nrd.nrd_id AND d.doc_nalog_id = :nalogId AND nrd.nrd_nalog_id = :nalogId AND d.doc_candidate_id = :kandidatId AND d.doc_status = 18 AND d.doc_crd_id is null
			INNER JOIN 
				idk_pp_document_types dt
			ON 
				nrd.nrd_type_id = dt.doc_type_id AND nrd.nrd_type_id = :typeId AND dt.doc_type_id = :typeId
		");
		$queryCheck1->execute(array(
			":kandidatId" => $kandidatId,
			":nalogId" => $nalogId, 
			":typeId" => $typeId
		)); 
		if($queryCheck1->rowCount() != 0){
			while($rowCheck1 = $queryCheck1->fetch()){
				$idDocNRD = intval($rowCheck1["doc_id"]);
				array_push($docIdsArray, $idDocNRD);
			}
		}

		//Candidat Required Documents Check
		$queryCheck2 = $db->prepare("
			SELECT 
				d.doc_id
			FROM 
				idk_pp_documents d 
			INNER JOIN 
				idk_pp_cand_required_documents crd
			ON 
				d.doc_crd_id = crd.crd_id AND d.doc_nalog_id = :nalogId AND crd.crd_nalog_id = :nalogId AND crd.crd_cand_id = :kandidatId AND d.doc_candidate_id = :kandidatId AND d.doc_status = 18 AND d.doc_nrd_id is null
			INNER JOIN 
				idk_pp_document_types dt
			ON 
				crd.crd_type_id = dt.doc_type_id AND crd.crd_type_id = :typeId AND dt.doc_type_id = :typeId
		");
		$queryCheck2->execute(array(
			":kandidatId" => $kandidatId,
			":nalogId" => $nalogId, 
			":typeId" => $typeId
		)); 
		if($queryCheck2->rowCount() != 0){
			while($rowCheck2 = $queryCheck2->fetch()){
				$idDocCRD = intval($rowCheck2["doc_id"]);
				array_push($docIdsArray, $idDocCRD);
			}
		}

		if(count($docIdsArray) != 0){
			foreach($docIdsArray AS $docIdVal){
				//Prebaci na status
				changeStatusAndInsertLog($docIdVal, 24, "Automatisch in den Status überführt, weil ein neues Dokument des gleichen Typs angefordert wurde." , $logged_employee_id, NULL);
			}
			$docIds = implode(",",$docIdsArray);

			$logDesc = "Prilikom označavanja dopune/odbijenice za kandidata ID = [".$kandidatId."] u nalogu ID = [".$nalogId."], za tip dokumenta ID = [".$typeId."] stavljeni su dokumenti IDs = [".$docIds."] na status Odbijen.";
			addToLogs($logDesc, 0);
		}
	}
	unset($docIdsArray);
}

function archivePreviouslyUploaded($kandidat_id, $nalog_id, $nrd_id, $crd_id ,$employee_id, $pp_id)
{
	Global $db;

	$previously_uploaded_docs_SQL = "SELECT * FROM idk_pp_documents WHERE doc_candidate_id = :kandidat_id AND doc_nalog_id = :nalog_id AND (doc_nrd_id = :nrd_id OR doc_crd_id = :crd_id) AND doc_status IN (21,24)";

	$previously_uploaded_query = $db->prepare($previously_uploaded_docs_SQL);
	$previously_uploaded_query->execute([
		":kandidat_id"      => $kandidat_id,
		":nalog_id"         => $nalog_id,
		":nrd_id"           => $nrd_id,
		":crd_id"           => $crd_id
	]);

	$previously_uploaded_docs = $previously_uploaded_query->fetchAll(PDO::FETCH_CLASS);

	foreach($previously_uploaded_docs as $doc)
	{
		// changeStatusAndInsertLog($doc->doc_id, 27, "Automatski prebačen u trenutku uploadanja ispravne verzije" , $employee_id, $pp_id);
		changeStatusAndInsertLog($doc->doc_id, 27, "Wurde automatisch archiviert nach dem Upload des korrigierten Dokuments." , $employee_id, $pp_id);
	}

}

function turnOffQualiplanReminder($kandidat_id){
	$qualiplanReminderID = 9;
	updateReminderStatusCRM($kandidat_id, $qualiplanReminderID);
}

function getCrdDocument($crd_id)
{
	Global $db;

	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "SELECT * FROM idk_pp_cand_required_documents WHERE crd_id = :crd_id";
	$query = $db->prepare($sql);
	$query->execute([
		":crd_id" => $crd_id
	]);

	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
	return $query->fetchAll(PDO::FETCH_CLASS)[0];
}
function getNrdDocument($nrd_id)
{
	Global $db;

	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "SELECT * FROM idk_pp_nalog_required_documents WHERE nrd_id = :nrd_id";
	$query = $db->prepare($sql);
	$query->execute([
		":nrd_id" => $nrd_id
	]);

	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
	return $query->fetchAll(PDO::FETCH_CLASS)[0];
}
function checkChangeDocumentStatusParams($kandidat_id, $nalog_id, $new_status_id, $status_comment, $doc_id, &$nrd_id, &$crd_id, $employee_id, $pp_id, $file)
{
    if($new_status_id == 3 && $_FILES["document"] == NULL)
    {
            http_response_code(500);
            die("Missing 'document' query parameter!");
    }

    function assertKeyExists($key)
    {
        if(!isset($_REQUEST[$key]) || empty($_REQUEST[$key]))
        {
            http_response_code(500);
            die("Missing $key query parameter!");
        }
    }
    if(!isset($_REQUEST["nrd_id"]) || empty($_REQUEST["nrd_id"]))
    {
        $nrd_id = NULL;
    }

    if(!isset($_REQUEST["crd_id"]) || empty($_REQUEST["crd_id"]))
    {
        $crd_id = NULL;
    }

    assertKeyExists("kandidat_id");
    assertKeyExists("nalog_id");
    assertKeyExists("new_status_id");
    assertKeyExists("new_status_id");

    if(!$pp_id && !$employee_id)
    {
		http_response_code(500);
		die("Missing both pp_id and employee_id query parameter!");
    }

    if($pp_id && $employee_id)
    {
		http_response_code(500);
		die("Can't have both pp_id and employee_id!");
    }

	if(!$doc_id && $new_status_id != 3)
	{
        // Ako nije u pitanju upload, mora se proslijediti doc_id iz
        // tabele idk_pp_documents.
		http_response_code(500);
		die("Missing doc_id query parameter!");
	}
    if(!$crd_id && !$nrd_id)
    {
		http_response_code(500);
		die("Missing both crd_id and nrd_id query parameter!");
    }

    if($crd_id && $nrd_id)
    {
		http_response_code(500);
		die("Can't have both nrd_id and crd_id!");
    }

    if(!in_array($new_status_id, [21, 24, 27]) && preskocenKorak($new_status_id, $doc_id))
    {
		http_response_code(500);
		die("Preskačete korak! Statusi se moraju updateati redom!");
    }

	if(is_null(getPartnerIdByCandidateId($kandidat_id)))
	{
		http_response_code(500);
		die("Kandidat nema partnera!");
	}
}

function allDocumentsReadyDate($kandidat_id, $nalog_id){
	Global $db;

	$departure_type = getCandidateDepartureType($kandidat_id);
	$full_recognition = getCandidateFullRecognition($kandidat_id);

	$sql_departure = "";
	if($departure_type[0] == 2 OR $full_recognition == 1 OR $full_recognition == 2){
		$sql_departure_nrd = "AND nrd.nrd_west_balkan = 1";
		$sql_departure_nrd1 = "AND nrd1.nrd_west_balkan = 1";
	} else if ($departure_type[0] == 3) {
		$sql_departure_nrd = "AND nrd.nrd_work_experience = 1";
		$sql_departure_nrd1 = "AND nrd1.nrd_work_experience = 1";
	} else if ($departure_type[0] == 0 ) {
		$sql_departure_nrd = "AND nrd.nrd_skilled_candidates = 1";
		$sql_departure_nrd1 = "AND nrd1.nrd_skilled_candidates = 1";
	}

	$sql = "SELECT
				docs.candidate AS kandidat_id
			FROM
				(
				SELECT
					doc.doc_candidate_id AS candidate,
					COUNT(doc.doc_id) AS added
				FROM
					idk_pp_documents doc
				JOIN(
					SELECT nrd1.nrd_id
					FROM
						idk_pp_nalog_required_documents nrd1
					WHERE
						nrd1.nrd_nalog_id = :nalog_id AND nrd1.nrd_status = 1
						$sql_departure_nrd1
				) AS nrd
			ON
				doc.doc_nrd_id = nrd.nrd_id AND doc.doc_status = 18
			JOIN(
				SELECT kan1.kandidat_id
				FROM
					idk_kandidati kan1
				WHERE
					kan1.kandidat_status_prijave = 12 AND kan1.kandidat_nalog_id = :nalog_id AND kan1.kandidat_id = :kandidat_id
			) AS kan
			ON
				doc.doc_candidate_id = kan.kandidat_id
			GROUP BY
				doc.doc_candidate_id
			) AS docs
			WHERE
				docs.added =(
				SELECT
					COUNT(nrd.nrd_id) AS potrebni
				FROM
					idk_pp_nalog_required_documents nrd
				WHERE
					nrd.nrd_nalog_id = :nalog_id AND nrd.nrd_status = 1
					$sql_departure_nrd
			)";
	$get_all_docs = $db->prepare($sql);
	$get_all_docs->execute([
		":kandidat_id" => $kandidat_id,
		":nalog_id"	   => $nalog_id
	]);
	$result = $get_all_docs->fetch();

	if($result["kandidat_id"] != $kandidat_id){
		return "Dokumenti nisu spremni!";
	} else {
		$sql = "SELECT
					max(dsl_date) as max_date
				FROM
					(
						SELECT
							*
						FROM
							idk_pp_nalog_required_documents
						LEFT JOIN
							idk_pp_documents
						ON
							idk_pp_nalog_required_documents.nrd_id = idk_pp_documents.doc_nrd_id
						WHERE
							doc_candidate_id = :kandidat_id
						AND
							doc_nalog_id = :nalog_id
					) docs
				INNER JOIN
					(
						SELECT
							dsl_date,
							dsl_doc_status,
							dsl_doc_id
						FROM
							idk_pp_documents_status_logs
						WHERE
							dsl_doc_status = 18
					) log
				ON
					docs.doc_id = log.dsl_doc_id
				GROUP BY doc_candidate_id";

		$get_all_ready_docs_date = $db->prepare($sql);
		$get_all_ready_docs_date->execute([
			":kandidat_id" => $kandidat_id,
			":nalog_id" => $nalog_id
		]);
		$result = $get_all_ready_docs_date->fetch();
		$max_date = date("d.m.Y", strtotime($result["max_date"]));
		return $max_date;
	}
}

function getQueryForOdlazakList($nalog_id, $status_id){
	$sql = "";
	switch($status_id){
		case 3:
			$sql = "SELECT 
						kandidat_id, 
						CONCAT(kandidat_ime,' ',kandidat_prezime) as kandidat_full_name,
						kj_slusanje, 
						ke_naziv_kvalifikacije, 
						ke_vrsta_obrazovanja
					FROM 
						idk_kandidati
					LEFT JOIN 
						idk_kandidat_jezici
					ON 
						idk_kandidati.kandidat_id = idk_kandidat_jezici.kj_kandidatid
					LEFT JOIN 
						idk_kandidat_edukacija
					ON 
						idk_kandidati.kandidat_id = idk_kandidat_edukacija.ke_kandidat_id
					WHERE 
						kandidat_nalog_id IN ($nalog_id)
					AND 
						kandidat_status_prijave = $status_id
					GROUP BY kandidat_id ";
		break;

		case 4:
			$sql = "SELECT 
						kandidat_dogovoreni_pocetak_rada, 
						kandidat_id, 
						CONCAT(kandidat_ime,' ',kandidat_prezime) as kandidat_full_name, 
						kandidat_pp_lokacija, 
						kandidat_pp_pozicija, 
						kandidat_pp_plata
					FROM 
						idk_kandidati
					WHERE 
						kandidat_nalog_id IN ($nalog_id)
					AND
						kandidat_status_prijave = $status_id
					GROUP BY kandidat_id ";
		break;

		case 7:
		case 8:
			$sql = "SELECT 
						kandidat_potencijalni_pocetak_rada, 
						kandidat_id, 
						CONCAT(kandidat_ime,' ',kandidat_prezime) as kandidat_full_name, 
						kandidat_pp_lokacija, 
						kandidat_pp_pozicija, 
						kandidat_pp_plata,
						kc_file_name,
						kandidat_status_prijave,
						kandidat_ppa_partner_id
					FROM 
						idk_kandidati
					LEFT JOIN 
						idk_kandidati_contracts
					ON 
						idk_kandidati.kandidat_id = idk_kandidati_contracts.kc_candidate_id
					WHERE 
						kandidat_nalog_id IN ($nalog_id)
					AND 
						kandidat_status_prijave = $status_id
					".  ($status_id == "9" ? "AND (kc_signed = 1 OR kc_signed IS NULL)" : "") . ($status_id == "8" ? "AND (kc_signed = 0 OR kc_signed IS NULL)" : "") ." 
					GROUP BY kandidat_id ";
		break;

		case 9:
			$sql = "SELECT 
						kandidat_potencijalni_pocetak_rada, 
						kandidat_id,
						kandidat_dipl_id, 
						kandidat_nacin_odlaska,
						CONCAT(kandidat_ime,' ',kandidat_prezime) as kandidat_full_name, 
						kandidat_pp_pozicija,
						(
							CASE 
								WHEN kandidat_nacin_odlaska = 3 THEN dokaz_radno_iskustvo.documents 
								ELSE NULL
							END
						) AS doc_dokaz_radno_iskustvo,
						(
							CASE 
								WHEN kandidat_nacin_odlaska = 3 THEN dokaz_zavrsena_skola.documents 
								ELSE NULL
							END
						) AS doc_dokaz_zavrsena_skola,
						file_nd,  
						kc_file_name,
						kandidat_jezici.max_jezik,
						kandidat_jezici.cvl_id,
						kandidat_jezici.cvl_status,
						kandidat_jezici.kj_ustanova,
						kandidat_ima_nostrifikaciju,
						CASE
							WHEN status_nd_kandidata IN (1,2,7) THEN 'U pripremi'
							WHEN status_nd_kandidata IN (3,4,5) THEN 'U procesu' 
							WHEN status_nd_kandidata = 6 THEN 'Zavrsen'	
							ELSE 'Nepoznato'
						END AS dipl_status
					FROM 
						idk_kandidati
					LEFT JOIN 
						/*
							Stari nacin cupanja informacija o jeziku
							Ne izvuce ispravan kj_id
						(
							SELECT
								kj_id,
								kj_kandidatid,
								CASE
									WHEN max_jezik 	LIKE '%C2%' THEN 'C2'
									WHEN max_jezik  LIKE '%C1%' THEN 'C1'
									WHEN max_jezik  LIKE '%B2%' THEN 'B2'
									WHEN max_jezik  LIKE '%B1%' THEN 'B1'
									WHEN max_jezik  LIKE '%A2%' THEN 'A2'
									WHEN max_jezik  LIKE '%A1%' THEN 'A1'
									WHEN max_jezik  LIKE '%Bez znanja%' THEN 'Bez znanja'
								END as max_jezik
							FROM
								(
									SELECT kj_id, kj_kandidatid, GROUP_CONCAT(kj_slusanje SEPARATOR ' ') as max_jezik FROM idk_kandidat_jezici
									WHERE 
										kj_naziv LIKE '%Njemacki%'
									GROUP BY kj_kandidatid
								) as k
								GROUP BY k.kj_kandidatid
						) as kandidat_jezici
							Stari nacin cupanja informacija o jeziku
							Ne izvuce ispravan kj_id
						*/
						(
							/*
								Uzmi one koji imaju aktivan jezik
							*/
							SELECT 
								kan_j.kj_id, 
								kan_j.kj_kandidatid,
								kan_j.kj_ustanova,
								kan_j_max1.max_jezik,
								kan_j_max1.cvl_id, 
								kan_j_max1.cvl_status, 
								kan_j_max1.cvl_active
							FROM 
								idk_kandidat_jezici kan_j
							JOIN 
								(
									SELECT 
										kan_j_max.kj_id,
										kan_j_max.kj_kandidatid, 
										kan_j_max.kj_ustanova,
										CASE
											WHEN kan_j_max.max_jezik LIKE '%C2%' THEN 'C2'
											WHEN kan_j_max.max_jezik LIKE '%C1%' THEN 'C1'
											WHEN kan_j_max.max_jezik LIKE '%B2%' THEN 'B2'
											WHEN kan_j_max.max_jezik LIKE '%B1%' THEN 'B1'
											WHEN kan_j_max.max_jezik LIKE '%A2%' THEN 'A2'
											WHEN kan_j_max.max_jezik LIKE '%A1%' THEN 'A1'
											WHEN kan_j_max.max_jezik LIKE '%Bez znanja%' THEN 'Bez znanja'
										END as max_jezik, 
										kan_j_max.cvl_id, 
										kan_j_max.cvl_status,
										kan_j_max.cvl_active
									FROM (
										SELECT 
											kan_j_gc.kj_id,
											kan_j_gc.kj_kandidatid, 
											kan_j_gc.kj_ustanova,
											GROUP_CONCAT(kan_j_gc.kj_slusanje SEPARATOR ' ') as max_jezik, 
											cvl_gc.cvl_id, 
											cvl_gc.cvl_status,
											cvl_gc.cvl_active
										FROM 
											idk_kandidat_jezici kan_j_gc 
										JOIN 
											idk_candidate_verified_languages cvl_gc
										ON 
											kan_j_gc.kj_id = cvl_gc.cvl_id
											AND 
											cvl_gc.cvl_active = 1
										JOIN 
											idk_kandidati kand_nalog
										ON 
											kan_j_gc.kj_kandidatid = kand_nalog.kandidat_id
										WHERE 
											kan_j_gc.kj_naziv LIKE '%Njemacki%'
											AND 
											kand_nalog.kandidat_status_prijave = 9 
											AND 
											kand_nalog.kandidat_nalog_id IN ($nalog_id)
										GROUP BY 
											kan_j_gc.kj_kandidatid
									) AS kan_j_max
									GROUP BY kan_j_max.kj_kandidatid
								) AS kan_j_max1
							ON 
								kan_j.kj_kandidatid = kan_j_max1.kj_kandidatid
								AND 
								kan_j.kj_slusanje LIKE kan_j_max1.max_jezik
								AND 
								kan_j.kj_id = kan_j_max1.kj_id
							GROUP BY 
								kan_j.kj_kandidatid
							/*
								Uzmi one koji imaju aktivan jezik
							*/
							UNION 
							/*
								Uzmi one koji nemaju ni jedan aktivan jezik a različiti su od onih koji imaju (razlicit ID)
							*/
							SELECT 
								kan_j.kj_id, 
								kan_j.kj_kandidatid,
								kan_j.kj_ustanova,
								kan_j_max1.max_jezik,
								kan_j_max1.cvl_id, 
								kan_j_max1.cvl_status, 
								kan_j_max1.cvl_active
							FROM 
								idk_kandidat_jezici kan_j
							JOIN 
								(
									SELECT 
										kan_j_max.kj_id,
										kan_j_max.kj_kandidatid, 
										kan_j_max.kj_ustanova,
										CASE
											WHEN kan_j_max.max_jezik LIKE '%C2%' THEN 'C2'
											WHEN kan_j_max.max_jezik LIKE '%C1%' THEN 'C1'
											WHEN kan_j_max.max_jezik LIKE '%B2%' THEN 'B2'
											WHEN kan_j_max.max_jezik LIKE '%B1%' THEN 'B1'
											WHEN kan_j_max.max_jezik LIKE '%A2%' THEN 'A2'
											WHEN kan_j_max.max_jezik LIKE '%A1%' THEN 'A1'
											WHEN kan_j_max.max_jezik LIKE '%Bez znanja%' THEN 'Bez znanja'
										END as max_jezik, 
										kan_j_max.cvl_id, 
										kan_j_max.cvl_status,
										kan_j_max.cvl_active
									FROM (
										SELECT 
											kan_j_gc.kj_id,
											kan_j_gc.kj_kandidatid, 
											kan_j_gc.kj_ustanova,
											GROUP_CONCAT(kan_j_gc.kj_slusanje SEPARATOR ' ') as max_jezik, 
											cvl_gc.cvl_id, 
											cvl_gc.cvl_status,
											cvl_gc.cvl_active
										FROM 
											idk_kandidat_jezici kan_j_gc 
										LEFT JOIN  
											idk_candidate_verified_languages cvl_gc
										ON 
											kan_j_gc.kj_id = cvl_gc.cvl_id
											AND 
											cvl_gc.cvl_active = 1
										JOIN 
											idk_kandidati kand_nalog
										ON 
											kan_j_gc.kj_kandidatid = kand_nalog.kandidat_id
										WHERE 
											kan_j_gc.kj_naziv LIKE '%Njemacki%'
											AND 
											kand_nalog.kandidat_status_prijave = 9 
											AND 
											kand_nalog.kandidat_nalog_id IN ($nalog_id)
											AND 
											kan_j_gc.kj_kandidatid NOT IN (
												/*
													IZBJEGNI GORNJE REZULTATE
												*/
													SELECT 
														kan_j.kj_kandidatid
													FROM 
														idk_kandidat_jezici kan_j
													JOIN 
														(
															SELECT 
																kan_j_max.kj_id,
																kan_j_max.kj_kandidatid, 
																kan_j_max.kj_ustanova,
																CASE
																	WHEN kan_j_max.max_jezik LIKE '%C2%' THEN 'C2'
																	WHEN kan_j_max.max_jezik LIKE '%C1%' THEN 'C1'
																	WHEN kan_j_max.max_jezik LIKE '%B2%' THEN 'B2'
																	WHEN kan_j_max.max_jezik LIKE '%B1%' THEN 'B1'
																	WHEN kan_j_max.max_jezik LIKE '%A2%' THEN 'A2'
																	WHEN kan_j_max.max_jezik LIKE '%A1%' THEN 'A1'
																	WHEN kan_j_max.max_jezik LIKE '%Bez znanja%' THEN 'Bez znanja'
																END as max_jezik, 
																kan_j_max.cvl_id, 
																kan_j_max.cvl_status,
																kan_j_max.cvl_active
															FROM (
																SELECT 
																	kan_j_gc.kj_id,
																	kan_j_gc.kj_kandidatid, 
																	kan_j_gc.kj_ustanova,
																	GROUP_CONCAT(kan_j_gc.kj_slusanje SEPARATOR ' ') as max_jezik, 
																	cvl_gc.cvl_id, 
																	cvl_gc.cvl_status,
																	cvl_gc.cvl_active
																FROM 
																	idk_kandidat_jezici kan_j_gc 
																JOIN 
																	idk_candidate_verified_languages cvl_gc
																ON 
																	kan_j_gc.kj_id = cvl_gc.cvl_id
																	AND 
																	cvl_gc.cvl_active = 1
																JOIN 
																	idk_kandidati kand_nalog
																ON 
																	kan_j_gc.kj_kandidatid = kand_nalog.kandidat_id
																WHERE 
																	kan_j_gc.kj_naziv LIKE '%Njemacki%'
																	AND 
																	kand_nalog.kandidat_status_prijave = 9 
																	AND 
																	kand_nalog.kandidat_nalog_id IN ($nalog_id)
																GROUP BY 
																	kan_j_gc.kj_kandidatid
															) AS kan_j_max
															GROUP BY kan_j_max.kj_kandidatid
														) AS kan_j_max1
													ON 
														kan_j.kj_kandidatid = kan_j_max1.kj_kandidatid
														AND 
														kan_j.kj_slusanje LIKE kan_j_max1.max_jezik
														AND 
														kan_j.kj_id = kan_j_max1.kj_id
													GROUP BY 
														kan_j.kj_kandidatid
												/*
													IZBJEGNI GORNJE REZULTATE
												*/
											)
										GROUP BY 
											kan_j_gc.kj_kandidatid
									) AS kan_j_max
									GROUP BY kan_j_max.kj_kandidatid
								) AS kan_j_max1
							ON 
								kan_j.kj_kandidatid = kan_j_max1.kj_kandidatid
								AND 
								kan_j.kj_slusanje LIKE kan_j_max1.max_jezik
								AND 
								kan_j.kj_id = kan_j_max1.kj_id
							GROUP BY 
								kan_j.kj_kandidatid
							/*
								Uzmi one koji nemaju ni jedan aktivan jezik a različiti su od onih koji imaju (razlicit ID)
							*/
						) as kandidat_jezici
					ON 
						idk_kandidati.kandidat_id = kandidat_jezici.kj_kandidatid
					LEFT JOIN 
						idk_kandidati_contracts
					ON 
						idk_kandidati.kandidat_id = idk_kandidati_contracts.kc_candidate_id
					LEFT JOIN
						idk_nd_kandidata
					ON
						idk_kandidati.kandidat_dipl_id = idk_nd_kandidata.id_broj_nd_kandidata
					LEFT JOIN 
						idk_nostrifikovane_diplome
					ON
						idk_nd_kandidata.id_broj_nd_kandidata = idk_nostrifikovane_diplome.id_cand_dipl
					LEFT JOIN 
						idk_nd_kandidata_dokumenti
					ON
						idk_kandidati.kandidat_dipl_id = idk_nd_kandidata_dokumenti.id_kandidata_dokument_nd	
					LEFT JOIN 
						(
							SELECT 
								kan.kandidat_id AS candId, 
								GROUP_CONCAT(doc.document_file) AS documents
							FROM 
								idk_kandidati kan
							JOIN 
								idk_documents doc
							ON 
								kan.kandidat_id = doc.document_dataid
								AND
								doc.document_special_type = 1
							WHERE 
								kan.kandidat_nalog_id IN ($nalog_id)
								AND
								kan.kandidat_status_prijave = $status_id
							GROUP BY 
								kan.kandidat_id

						) AS dokaz_radno_iskustvo
					ON 
						idk_kandidati.kandidat_id = dokaz_radno_iskustvo.candId
					LEFT JOIN 
						(
							SELECT 
								kan.kandidat_id AS candId, 
								GROUP_CONCAT(doc.document_file) AS documents
							FROM 
								idk_kandidati kan
							JOIN 
								idk_documents doc
							ON 
								kan.kandidat_id = doc.document_dataid
								AND
								doc.document_special_type = 2
							WHERE 
								kan.kandidat_nalog_id IN ($nalog_id)
								AND
								kan.kandidat_status_prijave = $status_id
							GROUP BY 
								kan.kandidat_id

						) AS dokaz_zavrsena_skola
					ON 
						idk_kandidati.kandidat_id = dokaz_zavrsena_skola.candId
					WHERE 
						kandidat_nalog_id IN ($nalog_id)
					AND
						kandidat_status_prijave = $status_id
					".  ($status_id == "9" ? "AND (kc_signed = 1 OR kc_signed IS NULL)" : "") . ($status_id == "8" ? "AND (kc_signed = 0 OR kc_signed IS NULL)" : "") ." 
					GROUP BY kandidat_id ";
		break;

		case 12:
			$sql = "SELECT 
						kandidat_id, 
						CONCAT(kandidat_ime,' ',kandidat_prezime) as kandidat_full_name, 
						lsp_datetime
					FROM 
						idk_kandidati
					LEFT JOIN 
						idk_log_statusi_prijave
					ON 
						idk_kandidati.kandidat_id = idk_log_statusi_prijave.lsp_kandidat_id
					WHERE 
						kandidat_nalog_id IN ($nalog_id)
					AND
						kandidat_status_prijave = $status_id
					AND
						(lsp_status_prijave_id = $status_id OR lsp_status_prijave_id IS NULL)
					GROUP BY kandidat_id ";
		break;

		case 18:
			$sql = "SELECT 
						kandidat_potencijalni_pocetak_rada, 
						kandidat_id, 
						CONCAT(kandidat_ime,' ',kandidat_prezime) as kandidat_full_name, 
						datum_termina, 
						CASE
							WHEN kandidat_status_prijave = 18 THEN (SELECT DATEDIFF(CURRENT_TIMESTAMP(), kandidat_latest_reserved_time))
							ELSE 'Ceka termin'
						END AS broj_dana_viza
					FROM 
						idk_kandidati
					LEFT JOIN 
						idk_log_statusi_prijave
					ON 
						idk_kandidati.kandidat_id = idk_log_statusi_prijave.lsp_kandidat_id
					WHERE 
						(kandidat_status_prijave = 18 OR kandidat_status_prijave = 15)
					AND
						(lsp_status_prijave_id IN (15, 18) OR lsp_status_prijave_id IS NULL)
					AND
						kandidat_nalog_id IN ($nalog_id)
					GROUP BY kandidat_id ";
		break;

		case 21:
			$sql = "SELECT 
						kandidat_id, 
						CONCAT(kandidat_ime,' ',kandidat_prezime) as kandidat_full_name, 
						lsp_datetime,
						vi_date_received_candidate,
						vi_date_received_employer,
						vi_deadline_date_candidate,
						vi_deadline_date_employer
					FROM 
						idk_kandidati
					JOIN
						idk_pp_visa_incomplete
					ON
						idk_kandidati.kandidat_id = idk_pp_visa_incomplete.vi_cand_id
					LEFT JOIN 
						idk_log_statusi_prijave
					ON 
						idk_kandidati.kandidat_id = idk_log_statusi_prijave.lsp_kandidat_id
					WHERE
						vi_type = 1 
					AND
						kandidat_nalog_id IN ($nalog_id)
					AND
						kandidat_status_prijave = $status_id
					AND
						(lsp_status_prijave_id = $status_id OR lsp_status_prijave_id IS NULL)
					GROUP BY kandidat_id ";
		break;

		case 24:
			$sql = "SELECT 
						kandidat_id, 
						CONCAT(kandidat_ime,' ',kandidat_prezime) as kandidat_full_name, 
						lsp_datetime,
						vi_date_received_candidate,
						vi_date_received_employer,
						vi_deadline_date_candidate,
						vi_deadline_date_employer
					FROM 
						idk_kandidati
					JOIN
						idk_pp_visa_incomplete
					ON
						idk_kandidati.kandidat_id = idk_pp_visa_incomplete.vi_cand_id
					LEFT JOIN 
						idk_log_statusi_prijave
					ON 
						idk_kandidati.kandidat_id = idk_log_statusi_prijave.lsp_kandidat_id
					WHERE
						vi_type = 2 
					AND
						kandidat_nalog_id IN ($nalog_id)
					AND
						kandidat_status_prijave = $status_id
					AND
						(lsp_status_prijave_id = $status_id OR lsp_status_prijave_id IS NULL)
					GROUP BY kandidat_id ";
		break;

		case 27:
			$sql = "SELECT 
						kandidat_potencijalni_pocetak_rada, 
						kandidat_id, 
						CONCAT(kandidat_ime,' ',kandidat_prezime) as kandidat_full_name, 
						kandidat_viza_vrijedi_od,
						kandidat_viza_vrijedi_do,
						datum_termina, 
						lsp_datetime
					FROM 
						idk_kandidati
					LEFT JOIN 
						idk_log_statusi_prijave
					ON 
						idk_kandidati.kandidat_id = idk_log_statusi_prijave.lsp_kandidat_id
					WHERE 
						kandidat_nalog_id IN ($nalog_id)
					AND
						kandidat_status_prijave = $status_id
					AND
						(lsp_status_prijave_id = $status_id OR lsp_status_prijave_id IS NULL)
					GROUP BY kandidat_id ";
		break;

		case 10:
			$sql = "SELECT 
						kandidat_dogovoreni_pocetak_rada, 
						kandidat_id, 
						CONCAT(kandidat_ime,' ',kandidat_prezime) as kandidat_full_name, 
						kandidat_pp_lokacija, 
						kandidat_pp_pozicija, 
						kandidat_pp_plata
					FROM 
						idk_kandidati
					WHERE 
						kandidat_nalog_id IN ($nalog_id)
					AND
						kandidat_status_prijave = $status_id
					GROUP BY kandidat_id ";
		break;
	}
	return $sql;
}

function getActiveLanguage($kandidat_id){
	Global $db;
	$sql = "SELECT
				kj_slusanje
			FROM
			(
				SELECT 
					kj_slusanje,
					kj_id
				FROM
					idk_kandidat_jezici
				WHERE
					kj_kandidatid = $kandidat_id
				AND
					kj_naziv LIKE '%Njemacki%'
			) as jezik
			JOIN
				(
					SELECT
						cvl_id
					FROM
						idk_candidate_verified_languages
					WHERE
						cvl_active = 1
				) as aktivni
			ON
				jezik.kj_id = aktivni.cvl_id
			";
	$get_active_language = $db->prepare($sql);
	$get_active_language->execute();
	$result = $get_active_language->fetch();
	return $result['kj_slusanje'];
}

function getNostrifikacijaBtn($kandidat_id){
	Global $db;
	$approved_pstatuses = [1,2,3,4,6,7,8,9,10,11,12];

	$sql = "SELECT 
				kandidat_dipl_id,
				status_nd_kandidata, 
				pstatus_nd_kandidata,
				razlog_biljeska_nd,
				kandidat_ima_nostrifikaciju
			FROM 
				idk_kandidati
			LEFT JOIN
				idk_nd_kandidata
			ON
				idk_kandidati.kandidat_dipl_id = idk_nd_kandidata.id_broj_nd_kandidata	
			LEFT JOIN
				(
					SELECT
						id_kandidata_biljeska_nd,
						razlog_biljeska_nd
					FROM
						idk_nd_kandidata_biljeske
					WHERE
						razlog_biljeska_nd = 32
				) as biljeska
			ON
					idk_nd_kandidata.id_broj_nd_kandidata = biljeska.id_kandidata_biljeska_nd
			WHERE 
				kandidat_id = $kandidat_id";

	$check_kandidat_dipl = $db->prepare($sql);
	$check_kandidat_dipl->execute();
	$result = $check_kandidat_dipl->fetch();

	$dipl_status  	= $result["status_nd_kandidata"];
	$dipl_pstatus 	= $result["pstatus_nd_kandidata"];
	$dipl_id 	  	= $result["kandidat_dipl_id"];
	$razlog		  	= $result["razlog_biljeska_nd"];
	$nostrifikacija = $result["kandidat_ima_nostrifikaciju"];

	if((($dipl_status == 1 && in_array($dipl_pstatus, $approved_pstatuses) ) || $dipl_status == 7) && $nostrifikacija != 1){
		  echo '<li>
					<a 
						href="#" 
						id="nostrifikacija_btn"
						class="btn material-btn material-btn-icon-success material-btn_success main-container__column" 
						data-toggle="modal" 
						data-target="#nostrifikovana_dipl"
					>
						<i class="fa fa-plus-square" aria-hidden="true"></i> 
						<span>
							Nostrifikacija
						</span>
					</a>
				</li>';
	}
}

function getCountPPRemindersForEmployeArrayR($type) {
	Global $db; 
	Global $logged_employee_id; 

	$type = intval($type); 
	$result = array(
		'status' => 0, 
		'count' => 0
	);

	$sql = '
		SELECT 
			pr.pr_id
		FROM
			idk_pp_reminders pr
		JOIN 
			idk_pp_reminder_settings prs 
		ON 
			pr.pr_reminder_setting_id = prs.prs_id
		JOIN 
			idk_pp_reminder_types prt
		ON 
			prs.prs_reminder_type_id = prt.prt_id
		WHERE 
			pr.pr_status = 1
			AND 
			pr.pr_reminder_document_id is null 
			AND 
			prs.prs_active = 1 
			AND 
			prt.prt_user_type = 3
			AND
			prt.prt_has_documents = 0
			AND 
			'.(($type == 1) ? 'FIND_IN_SET(:userId, prs.prs_pua_ids) > 0' : 'FIND_IN_SET(:userId, prs.prs_controlling_pua_ids) > 0').'

		UNION

		SELECT 
			pr.pr_id
		FROM
			idk_pp_reminders pr
		JOIN
			idk_pp_reminder_documents prd 
		ON 
			pr.pr_reminder_document_id = prd.prd_id
		JOIN 
			idk_pp_reminder_settings prs 
		ON 
			prd.prd_prs_id = prs.prs_id
		JOIN 
			idk_pp_reminder_types prt
		ON 
			prs.prs_reminder_type_id = prt.prt_id
		WHERE 
			pr.pr_status = 1
			AND 
			pr.pr_reminder_setting_id is null 
			AND
			prd.prd_active = 1 
			AND 
			prs.prs_active = 1 
			AND 
			prt.prt_user_type = 3
			AND
			prt.prt_has_documents = 1
			AND
			'.(($type == 1) ? 'FIND_IN_SET(:userId, prs.prs_pua_ids) > 0' : 'FIND_IN_SET(:userId, prs.prs_controlling_pua_ids) > 0').'
	'; 

	$query = $db->prepare($sql);
	$query->execute(array(
		':userId' => $logged_employee_id
	));
	$cnt = $query->rowCount();
	if ($cnt > 0) {
		$result['status'] = 3; 
		$result['count'] = $cnt;
	} else {
		$result['status'] = 2;
	}

	return $result; 
}

function reminderHasDocuments($reminder_type){
	Global $db;
	$reminder_type = intval($reminder_type);
	$has_documents = $db -> prepare('
		SELECT
			prt_has_documents
		FROM
			idk_pp_reminder_types
		WHERE
			prt_id = :reminder_type
	');
	$has_documents -> bindParam(':reminder_type', $reminder_type, PDO::PARAM_INT);
	$has_documents -> execute();
	$result = $has_documents -> fetch();
	return $result['prt_has_documents'] == 1 ? true : false;
}

function checkContractSentR($canId){
	
	Global $db; 

	if ($canId != "") {

		$nalogId   = getNalogIdByCandidateId($canId); 
		$partnerId = getPartnerIdByCandidateId($canId); 

		if ($canId != 0 AND $nalogId != 0 AND $partnerId != 0) {

			$queryCheck = $db->prepare("
				SELECT 
					id_cs,
					cs_tracking_code,
					cs_tracking_link, 
					cs_sent_date
				FROM idk_pp_contract_sent
					
				WHERE 
					cs_kandidat_id = :cs_kandidat_id
				AND 
					cs_nalog_id = :cs_nalog_id
				AND 
					cs_partner_id = :cs_partner_id
				AND 
					cs_status != 3
			");

			$queryCheck->execute(array(
				':cs_kandidat_id' => $canId,
				':cs_nalog_id'    => $nalogId,
				':cs_partner_id'  => $partnerId
			));

			if ($queryCheck->rowCount() == 1) return true;
			else return false;

		} else return false;

	} else return false;
	
}

function insertContractSentR($canId, $trackingCode, $linkTrackingCode, $sentDate){
	Global $logged_employee_id;
	Global $db;
	/* 
		Response:
			1 		-	izvršen insert
			100		-	nije proslijeđen neki od parametara funkciji
			101		-	kandidat nema nalogId ili partnerId ili nije pronađen candidateId
	*/
	if($trackingCode != "" AND $linkTrackingCode != "" AND $sentDate != ""){
		$canId = intval($canId);
		$nalogId = getNalogIdByCandidateId($canId);
		$partnerId = getPartnerIdByCandidateId($canId);
		if($canId != 0 AND $nalogId != 0 AND $partnerId != 0){
			$queryInsert = $db->prepare("
				INSERT INTO idk_pp_contract_sent
					(
						cs_kandidat_id, 
						cs_nalog_id, 
						cs_partner_id,
						cs_tracking_code,
						cs_tracking_link,
						cs_sent_date,
						cs_status,
						cs_entry_date,
						cs_entry_employee_id
					)
				VALUES
					(
						:cs_kandidat_id, 
						:cs_nalog_id, 
						:cs_partner_id,
						:cs_tracking_code,
						:cs_tracking_link,
						:cs_sent_date,
						:cs_status,
						:cs_entry_date,
						:cs_entry_employee_id
					)
			");
			$queryInsert->execute(array(
				':cs_kandidat_id' => $canId,
				':cs_nalog_id' => $nalogId,
				':cs_partner_id' => $partnerId,
				':cs_tracking_code' => $trackingCode,
				':cs_tracking_link' => $linkTrackingCode,
				':cs_sent_date' => $sentDate,
				':cs_status' => 1,
				':cs_entry_date' => date("Y-m-d H:i:s"),
				':cs_entry_employee_id' => $logged_employee_id
			));
			
			$logDesc = "Zaposlenik Id = [".$logged_employee_id."] je označio slanje ugovora sa informacijama: Tracking code = ".$trackingCode."; Link = ".$linkTrackingCode."; Datum slanja: ".$sentDate.";";
			addToLogs($logDesc, 0); 
			return 1;
		}else{
			return 101; //101	-	kandidat nema nalogId ili partnerId ili nije pronađen candidateId
		}
	}else{
		return 100; //100	-	nije proslijeđen neki od parametara funkciji
	}
}

function updateStatusContractSentR($canId, $statusChange, $dateReceiving = NULL){
	Global $logged_employee_id;
	Global $db;

	/* 
		Response:
			1 		-	izvršen update prijema poste
			2		-	izvršen update arhiviranja
			100		-	naispravan status ili fali datum u slučaju statusa primljeno
			101		-	kandidat nema nalogId ili partnerId ili nije pronađen candidateId
			102		- 	Nije pronađen red na kojem se može izvrsiti update
	*/

	$statusChange = intval($statusChange);
	if($dateReceiving != NULL){
		$dateReceiving = date("Y-m-d", strtotime($dateReceiving));
	}
	if(in_array($statusChange, array(2,3)) AND ($statusChange == 3 OR ($statusChange == 2 AND $dateReceiving != NULL))){
		$canId = intval($canId);
		$nalogId = getNalogIdByCandidateId($canId);
		$partnerId = getPartnerIdByCandidateId($canId);
		if($canId != 0 AND $nalogId != 0 AND $partnerId != 0){
			
			$queryCheck = $db->prepare("
				SELECT 
					cs.id_cs, cs.cs_status
				FROM 
					idk_pp_contract_sent cs 
				JOIN 
					idk_kandidati kan 
				ON 
					cs.cs_kandidat_id = kan.kandidat_id
					AND 
					cs.cs_nalog_id = kan.kandidat_nalog_id
					AND 
					cs.cs_partner_id = kan.kandidat_ppa_partner_id
				WHERE 
					cs.cs_kandidat_id = :canId
					AND 
					cs.cs_nalog_id = :nalogId
					AND 
					cs.cs_partner_id = :partnerId
					AND 
					cs.cs_status != 3
			");
			$queryCheck->execute(array(
				':canId' => $canId,
				':nalogId' => $nalogId,
				':partnerId' => $partnerId
			));
			if($queryCheck->rowCount() == 1){
				$rowCheck = $queryCheck->fetch();
				$idCs = intval($rowCheck["id_cs"]);
				$statusCs = intval($rowCheck["cs_status"]);
				
				if($statusCs == 1 AND $statusChange == 2){
					//Zaprimljena pošta
					$queryUpdate = $db->prepare("
						UPDATE 
							idk_pp_contract_sent
						SET 
							cs_status = :status,
							cs_received_date = :date,
							cs_received_employee_id = :employee
						WHERE 
							id_cs = :idCs
					");
					$queryUpdate->execute(array(
						':status' => $statusChange,
						':date' => $dateReceiving,
						':employee' => $logged_employee_id,
						':idCs' => $idCs
					));
					
					//Ubaciti log
					$logDesc = "Zaposlenik Id = [".$logged_employee_id."] je označio prijem pošte za IdCs = [".$idCs."] na Datum = [".$dateReceiving."].";
					addToLogs($logDesc, 0);  

					return 1;
				}
				
				if(in_array($statusCs,array(1,2)) AND $statusChange == 3){
					//Arhiviranje reda
					$queryUpdate = $db->prepare("
						UPDATE 
							idk_pp_contract_sent
						SET 
							cs_status = :status,
							cs_archived_date = :date,
							cs_archived_employee_id = :employee
						WHERE 
							id_cs = :idCs
					");
					$queryUpdate->execute(array(
						':status' => $statusChange,
						':date' => date("Y-m-d H:i:s"),
						':employee' => $logged_employee_id,
						':idCs' => $idCs
					));
					
					//Ubaciti log 
					$logDesc = "Zaposlenik Id = [".$logged_employee_id."] je arhivirao prijem pošte za IdCs = [".$idCs."].";
					addToLogs($logDesc, 0); 

					return 2;
				}
			}else{
				return 102; //Nije pronađen red na kojem se može izvrsiti update
			}
		}else{
			return 101; //kandidat nema nalogId ili partnerId ili nije pronađen candidateId
		}
	}else{
		return 100; //naispravan status ili fali datum u slučaju statusa primljeno
	}
}

function getActiveDocumentReminders($kandidat_id, $reminder_type, $doc_id){	
	Global $db;
	$nalogId = intval(getNalogIdByCandidateId($kandidat_id));
	if(!reminderHasDocuments($reminder_type))
		return null;

	$bitanPartner = array(4,5,8,9,11,13,14,21,23,24);
	if(in_array($reminder_type,$bitanPartner)){
		$partnerId = intval(getPartnerIdByCandidateId($kandidat_id));
		$partner_uslov = " AND prs_partner_id = ".$partnerId."";
	}else{
		$partner_uslov = "";
	}

	//Tipovi remindera za nrd_dokumente i crd_dokumente
	$tipoviNrd = array(10,11,12,13);
	$tipoviCrd = array(20,21,22,23);

	$documentIdColumn = null;

	if(in_array($reminder_type, $tipoviNrd)){
		$documentIdColumn = "prd_nrd_id";
	}else if(in_array($reminder_type, $tipoviCrd)){
		$documentIdColumn = "prd_crd_id";
	}else{
		return null;
	}

	$get_reminder = $db -> prepare('
		SELECT 
			pr_id, prt_user_type 
		FROM 
			idk_pp_reminders
		JOIN
			idk_pp_reminder_documents
		ON
			idk_pp_reminders.pr_reminder_document_id = idk_pp_reminder_documents.prd_id
		JOIN
			idk_pp_reminder_settings
		ON
			idk_pp_reminder_documents.prd_prs_id = idk_pp_reminder_settings.prs_id
		JOIN 
			idk_pp_reminder_types
		ON
			idk_pp_reminder_settings.prs_reminder_type_id = idk_pp_reminder_types.prt_id
		WHERE
			pr_candidate_id = :candidate_id
		AND
			prs_reminder_type_id = :type_id
		AND
			idk_pp_reminder_documents.'.$documentIdColumn.' = :doc_id
		AND
			prs_active = 1
		AND
			(pr_status = 1 OR pr_status = 2)
		AND
			prs_nalog_id = :nalog_id
		'.$partner_uslov.'
	');

	$get_reminder -> execute(array(
		":candidate_id" => $kandidat_id,
		":type_id"		=> $reminder_type,
		":nalog_id"		=> $nalogId,
		":doc_id"		=> $doc_id
	));

	return $get_reminder;

}

function getActiveReminders($kandidat_id, $reminder_type){
	Global $db;
	$nalogId = intval(getNalogIdByCandidateId($kandidat_id));

	$bitanPartner = array(4,5,8,9,11,13,14,21,23,24);
	if(in_array($reminder_type,$bitanPartner)){
		$partnerId = intval(getPartnerIdByCandidateId($kandidat_id));
		$partner_uslov = " AND prs_partner_id = ".$partnerId."";
	}else{
		$partner_uslov = "";
	}

	$get_reminder = $db -> prepare('
		SELECT 
			pr_id, prt_user_type 
		FROM 
			idk_pp_reminders
		JOIN
			idk_pp_reminder_settings
		ON
			idk_pp_reminders.pr_reminder_setting_id = idk_pp_reminder_settings.prs_id
		JOIN 
			idk_pp_reminder_types
		ON
			idk_pp_reminder_settings.prs_reminder_type_id = idk_pp_reminder_types.prt_id
		WHERE
			pr_candidate_id = :candidate_id
		AND
			prs_reminder_type_id = :type_id
		AND
			prs_active = 1
		AND
			(pr_status = 1 OR pr_status = 2)
		AND
			prs_nalog_id = :nalog_id
		'.$partner_uslov.'
	');
	$get_reminder -> execute(array(
		":candidate_id" => $kandidat_id,
		":type_id"		=> $reminder_type,
		":nalog_id"		=> $nalogId
	));

	return $get_reminder;
}

function updateReminderStatusCRM($kandidat_id, $reminder_type, $doc_id = null){
	Global $db;
	Global $logged_employee_id;
	$current_date = date("Y-m-d H:i:s");
	$nalogId = intval(getNalogIdByCandidateId($kandidat_id));
	
	$get_reminder = null;
	// Na osnovu $doc_id odrediti koje remindere gledati
	if(is_null($doc_id))
		$get_reminder = getActiveReminders($kandidat_id, $reminder_type);
	else
		$get_reminder = getActiveDocumentReminders($kandidat_id, $reminder_type, $doc_id);
	
	if(is_null($get_reminder))
		return;


	if($get_reminder->rowCount() == 1){
		$result_reminder = $get_reminder -> fetch();
		$reminder_id = $result_reminder['pr_id'];
		$user_type = $result_reminder['prt_user_type'];
		if($user_type == 3){
			//zaduzen CRM zaposlenik
			$update_reminder_done = $db -> prepare("
				UPDATE 
					idk_pp_reminders
				SET
					pr_status = 3,
					pr_date_completed = :current_date,
					pr_user_assigned = :logged_employee
				WHERE
					pr_id = :reminder_id
			");
			$update_reminder_done -> execute(array(
				":reminder_id" 		=> $reminder_id,
				":current_date" 	=> $current_date,
				":logged_employee" 	=> $logged_employee_id
			));
			
		}else{
			//zaduzen poslodavac
			$update_reminder_done = $db -> prepare("
				UPDATE 
					idk_pp_reminders
				SET
					pr_status = 3,
					pr_date_completed = :current_date,
					pr_done_by_crm_employee = :employee_id
				WHERE
					pr_id = :reminder_id
			");
			$update_reminder_done -> execute(array(
				":reminder_id" 	=> $reminder_id,
				":current_date" => $current_date,
				":employee_id" => $logged_employee_id
			));
		}
		$log_desc = "Azuriran reminder: " .$reminder_id. " za kandidata " .$kandidat_id. ".";
		$log_type = "12";
		addToLogs($log_desc, $log_type); //Log za remindere - $log_type = 3
	}elseif($get_reminder->rowCount() > 1){
		$log_desc = "Nije azuriran reminder tip: " .$reminder_type. " za kandidata " .$kandidat_id. ". Pronađeno više od jednog aktivnog remindera!";
		$log_type = "12";
		addToLogs($log_desc, $log_type); //Log za remindere - $log_type = 3
	}
}

function updateReminderOnCandidateQuit($kandidat_id){
	Global $db;
	Global $logged_employee_id;
	$current_date = date("Y-m-d H:i:s");

	//zaduzen CRM zaposlenik
	$update_reminder_done_crm = $db -> prepare("
		UPDATE 
			idk_pp_reminders
		JOIN idk_pp_reminder_settings rs ON pr_reminder_setting_id = rs.prs_id
		JOIN idk_pp_reminder_types rt ON rs.prs_reminder_type_id = rt.prt_id
		SET
			pr_status = 5,
			pr_date_completed = :current_date,
			pr_user_assigned = :logged_employee
		WHERE
			pr_candidate_id = :pr_candidate_id AND (pr_status = 1 OR pr_status = 2) AND rt.prt_user_type = 3
	");
	$update_reminder_done_crm -> execute(array(
		":pr_candidate_id" 	=> $kandidat_id,
		":current_date" 	=> $current_date,
		":logged_employee" 	=> $logged_employee_id
	));
		
	//zaduzen poslodavac
	$update_reminder_done_pp = $db -> prepare("
		UPDATE 
			idk_pp_reminders
		JOIN idk_pp_reminder_settings rs ON pr_reminder_setting_id = rs.prs_id
		JOIN idk_pp_reminder_types rt ON rs.prs_reminder_type_id = rt.prt_id
		SET
			pr_status = 5,
			pr_date_completed = :current_date,
			pr_done_by_crm_employee = :logged_employee
		WHERE
			pr_candidate_id = :pr_candidate_id AND (pr_status = 1 OR pr_status = 2) AND (rt.prt_user_type = 1 OR rt.prt_user_type = 2)
	");
	$update_reminder_done_pp -> execute(array(
		":pr_candidate_id" 	=> $kandidat_id,
		":current_date" => $current_date,
		":logged_employee" => $logged_employee_id
	));

	//zaduzen CRM zaposlenik - reminderi za dokumente
	$update_reminder_doc_done_crm = $db -> prepare("
		UPDATE
			idk_pp_reminders
		JOIN idk_pp_reminder_documents prd ON pr_reminder_document_id = prd.prd_id
		JOIN idk_pp_reminder_settings prs ON prd.prd_prs_id = prs.prs_id
		JOIN idk_pp_reminder_types prt ON prs.prs_reminder_type_id = prt.prt_id
		SET
			pr_status = 5,
			pr_date_completed = :current_date,
			pr_user_assigned = :logged_employee
		WHERE 
			pr_candidate_id = :pr_candidate_id AND (pr_status = 1 OR pr_status = 2) AND prt.prt_user_type = 3
	");
	$update_reminder_doc_done_crm -> execute(array(
		":pr_candidate_id" 	=> $kandidat_id,
		":current_date" 	=> $current_date,
		":logged_employee" 	=> $logged_employee_id
	));

	//zaduzen poslodavac - reminderi za dokumente
	$update_reminder_doc_done_crm = $db -> prepare("
		UPDATE
			idk_pp_reminders
		JOIN idk_pp_reminder_documents prd ON pr_reminder_document_id = prd.prd_id
		JOIN idk_pp_reminder_settings prs ON prd.prd_prs_id = prs.prs_id
		JOIN idk_pp_reminder_types prt ON prs.prs_reminder_type_id = prt.prt_id
		SET
			pr_status = 5,
			pr_date_completed = :current_date,
			pr_done_by_crm_employee = :logged_employee
		WHERE 
			pr_candidate_id = :pr_candidate_id AND (pr_status = 1 OR pr_status = 2) AND (prt.prt_user_type = 1 OR prt.prt_user_type = 2)
	");
	$update_reminder_doc_done_crm -> execute(array(
		":pr_candidate_id" 	=> $kandidat_id,
		":current_date" 	=> $current_date,
		":logged_employee" 	=> $logged_employee_id
	));

	$log_desc = "Odustankom azurirani reminderi za kandidata " .$kandidat_id. ".";
	$log_type = "12";
	addToLogs($log_desc, $log_type); //Log za remindere - $log_type = 3
	
}

function getNalogNameById($nalog_id){
	Global $db;

	$query = $db -> prepare('
		SELECT nalog_naziv
		FROM idk_nalozi
		WHERE nalog_id = :nalog_id
	');

	$query -> execute(array(':nalog_id' => $nalog_id));

	$row = $query -> fetch();

	return $row['nalog_naziv'];
}

function getOffersAndBenefitsOfOrderArrayR($orderId) {
	Global $db; 
	$query = $db->prepare("
		SELECT 
			offers_and_benefits_of_employers 
		FROM 
			idk_nalozi 
		WHERE 
			nalog_id = :nalog_id 
	"); 
	$query->execute(array(
		':nalog_id' => $orderId
	)); 
	$row = $query->fetch(); 

	if ($row["offers_and_benefits_of_employers"] != null) {
		return json_decode($row["offers_and_benefits_of_employers"], TRUE);
	} else {
		return array();
	}
}

function editOffersAndBenefitsOfOrderR($orderId, $jsonData) {
	Global $db; 

	/*
		Response code desc: 
			100 - The orderid or jsondata variable is not set
			0	- Not updated
			1	- Updated
	*/

	$response = 100; 
	$logDesc = 'EDIT OFFERS AND BENEFITS OF ORDER -> ';

	if ($orderId AND $jsonData) {
		$oldJsonData = json_encode(getOffersAndBenefitsOfOrderArrayR($orderId)); 

		$query = $db->prepare("
			UPDATE
				idk_nalozi 
			SET 
				offers_and_benefits_of_employers = :offers_and_benefits_of_employers
			WHERE 
				nalog_id = :nalog_id
		"); 
		$query->execute(array(
			':nalog_id' => $orderId, 
			':offers_and_benefits_of_employers' => $jsonData 
		)); 

		if ($query->rowCount() == 1) {
			$response = 1; 
			$logDesc = $logDesc. ' Order ID ['.$orderId.'], Response code ['.$response.'], Old values [ '.$oldJsonData.' ], Sent values [ '.$jsonData.' ], Message [Success - Izvršen update!]';
		} else {
			$response = 0;
			$logDesc = $logDesc. ' Order ID ['.$orderId.'], Response code ['.$response.'], Old values [ '.$oldJsonData.' ], Sent values [ '.$jsonData.' ], Message [Warning - Nije izvršen update!]';
		}
		unset($oldJsonData);
	} else {
		$logDesc = $logDesc. ' Order ID ['.$orderId.'], Response code ['.$response.'], Sent values [ '.$jsonData.' ], Message [Alert - Desio se problem na backend-u sa potrebnim varijablama!]';
	}
	
	addToLogs($logDesc, 0);

	return $response; 
}

function getTaskForceStatusNameById($task_force_status_id){
	Global $db;

	$query = $db -> prepare('
		SELECT tfs_name
		FROM idk_tf_statusi
		WHERE tfs_id = :tfs_id 
	');

	$query -> execute(array(':tfs_id' => $task_force_status_id));

	$row = $query -> fetch();

	return $row['tfs_name'];
}

function getLastTFStatus($kandidat_id, $tf_nalog_id, $tf_vrsta_id){
	Global $db;

	$inbound_status = "24, 43, 44, 45, 46, 47, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 88, 89, 90, 91, 140, 141, 142, 143, 144, 145, 146";

	$query = $db -> prepare("
		SELECT tf_status_id
		FROM idk_task_force
		WHERE tf_candidate_id = :tf_candidate_id AND tf_nalog_id = :tf_nalog_id 
		AND tf_status_id NOT IN ($inbound_status)  
		AND tf_vrsta_id = :tf_vrsta_id
		ORDER BY tf_id 
		DESC LIMIT 1 
	");

	$query -> execute(array(':tf_candidate_id' => $kandidat_id, ':tf_nalog_id' => $tf_nalog_id, ':tf_vrsta_id' => $tf_vrsta_id));

	$row = $query -> fetch();

	return $row['tf_status_id'];
}

function getCandidateTFStatus($kandidat_id){
	Global $db;

	$query = $db -> prepare('
		SELECT kandidat_tf_status
		FROM idk_kandidati
		WHERE kandidat_id  = :kandidat_id  
	');

	$query -> execute(array(':kandidat_id' => $kandidat_id));

	$row = $query -> fetch();

	return intval($row['kandidat_tf_status']);
}

function getTFNote($tf_id){
	Global $db;

	$query = $db -> prepare('
		SELECT tf_note
		FROM idk_task_force
		WHERE tf_id = :tf_id
	');

	$query -> execute(array(':tf_id' => $tf_id));

	$row = $query -> fetch();

	return $row['tf_note'];
}

function checkAgentReservation($agent_id){
	Global $db;

	$query = $db -> prepare('
		SELECT COUNT(1) as isReserved
		FROM idk_tf_reservations
		WHERE tf_agent_id = :agent_id;
	');

	$query -> execute(array(':agent_id' => $agent_id));

	$row = $query -> fetch();

	return $row["isReserved"];
}

function checkCandidateReservation($kandidat_id){
	Global $db;

	$query = $db -> prepare("
		SELECT COUNT(1) as isReserved
		FROM idk_tf_reservations
		WHERE tf_kandidat_id  = :kandidat_id;
	");

	$query -> execute(array(':kandidat_id' => $kandidat_id));

	$row = $query -> fetch();
	
	return $row["isReserved"];
}

function checkAgentReservationForCandidate($agent_id, $kandidat_id){
	Global $db;

	$query = $db -> prepare("
		SELECT COUNT(1) as isReserved
		FROM idk_tf_reservations
		WHERE tf_kandidat_id  = :tf_kandidat_id AND tf_agent_id = :tf_agent_id;
	");

	$query -> execute(array(':tf_kandidat_id' => $kandidat_id, ':tf_agent_id' => $agent_id));

	$row = $query -> fetch();
	
	return $row["isReserved"];
}

function isCandidateFreeForCall($agent_id, $kandidat_id){
	Global $db;

	$query = $db -> prepare("
		SELECT COUNT(1) as isReserved
		FROM idk_tf_reservations
		WHERE tf_kandidat_id  = :tf_kandidat_id AND tf_agent_id != :tf_agent_id;
	");

	$query -> execute(array(':tf_kandidat_id' => $kandidat_id, ':tf_agent_id' => $agent_id));

	$row = $query -> fetch();
	
	return $row["isReserved"];
}

function getAgentReservedInCallWithCandidate($kandidat_id, $agent_id){
	Global $db;

	$query = $db -> prepare("
		SELECT employee_firstname, employee_lastname
		FROM idk_tf_reservations
		INNER JOIN idk_employees ON idk_tf_reservations.tf_agent_id = idk_employees.employee_id
		WHERE tf_kandidat_id  = :tf_kandidat_id AND tf_agent_id != :tf_agent_id;
	");

	$query -> execute(array(':tf_kandidat_id' => $kandidat_id, ':tf_agent_id' => $agent_id));
	
	$employee = $query->fetch();
	$result = array();

	if (!$employee) {
        $result["status"] = 1;
        $result["message"] = "";
    } else {
        $employee_fullname = $employee["employee_firstname"] . " " . $employee["employee_lastname"];
        $result["status"] = 0;
        $result["message"] = "Kandidat je rezervisan za agenta: $employee_fullname.";
    }

    return json_encode($result);
}

function getTaskForceStatusesForCandidate($kandidat_id, $tf_vrsta_id){
	Global $db;

	$kandidat_tf_status = getCandidateTFStatus($kandidat_id);

	if($kandidat_tf_status == 16 OR $kandidat_tf_status == 22){
		$unwanted_statuses = "tfs_name NOT IN ('Došao', 'Pogresan Broj', 'Nedostupan (bez kontakta)')";
	}else if($kandidat_tf_status == 18){
		$unwanted_statuses = "tfs_name NOT IN ('Došao', 'Pogresan Broj', 'Nedostupan (bez kontakta)')";
	}else if($kandidat_tf_status == 12 OR $kandidat_tf_status == 14){
		$unwanted_statuses = "tfs_name NOT IN ('Pogresan Broj', 'Nedostupan (bez kontakta)')";
	}else if($kandidat_tf_status == 8 OR $kandidat_tf_status == 2){
		$unwanted_statuses = "tfs_name NOT IN ('Došao', 'Dolazi', 'Nije došao')";
	}else{
		$unwanted_statuses = "tfs_name NOT IN ('Došao', 'Dolazi', 'Nije došao', 'Nedostupan (bez kontakta)')";
	}

	// Kad je kandidat tf status Pristao ili Nije dosao onda se vidi i Dolazi i Pristao i Nije Dosao
	if($kandidat_tf_status == 16 OR $kandidat_tf_status == 22){
		$query = $db -> prepare("
			SELECT *
			FROM idk_tf_statusi
			WHERE ($unwanted_statuses AND ((tfs_id >= $kandidat_tf_status) OR (tfs_is_positive = 0) OR (tfs_id=16))) AND tfs_vrsta_id = $tf_vrsta_id
		");
	// Kad je kandidat tf status na Dolazi onda se vidi i Nije došao
	}elseif($kandidat_tf_status == 18){
		$query = $db -> prepare("
			SELECT *
			FROM idk_tf_statusi
			WHERE (tfs_name = 'Dolazi' OR tfs_name = 'Nije došao' OR tfs_is_positive = 0) AND $unwanted_statuses AND tfs_vrsta_id = $tf_vrsta_id
		");
	}elseif($kandidat_tf_status == 2){
		// Inače se vidi svaki veći osim Dolazi, Dosao i Nije Dosao
		$query = $db -> prepare("
			SELECT *
			FROM idk_tf_statusi
			WHERE $unwanted_statuses AND tfs_vrsta_id = $tf_vrsta_id;
		");
	}else{
		// Inače se vidi svaki veći osim Dolazi, Dosao i Nije Dosao
		$query = $db -> prepare("
			SELECT *
			FROM idk_tf_statusi
			WHERE ($unwanted_statuses AND ((tfs_id >= $kandidat_tf_status) OR (tfs_is_positive = 0) OR (tfs_id = 2))) AND tfs_vrsta_id = $tf_vrsta_id;
		");
	}

	$query -> execute();

	$results = $query->fetchAll(PDO::FETCH_ASSOC);
	$json = json_encode($results);

	return $results;
}

function getTaskForceStatusesForCandidatePosredovanjeObrada($tf_vrsta_id){
	Global $db;

	$query = $db -> prepare("
		SELECT *
		FROM idk_tf_statusi
		WHERE tfs_vrsta_id = :tfs_vrsta_id ORDER BY tfs_order
	");
	
	$query -> execute(array(':tfs_vrsta_id' => $tf_vrsta_id));

	$results = $query->fetchAll(PDO::FETCH_ASSOC);
	$json = json_encode($results);

	return $results;
}

function updateLastActiveTaskForCandidate($kandidat_id, $tf_vrsta_id){
	Global $db;

	$query = $db->prepare("
		UPDATE idk_task_force
		SET tf_last_active_task = :tf_last_active_task
		WHERE tf_candidate_id = :tf_candidate_id AND tf_last_active_task = 1 AND tf_vrsta_id = :tf_vrsta_id
	");

	$query->execute(array(
		':tf_last_active_task' => 0,
		':tf_candidate_id' => $kandidat_id,
		':tf_vrsta_id' => $tf_vrsta_id
	));
}

function updateTaskForceStatusForCandidate($kandidat_id, $kandidat_tf_status){
	Global $db;

	$query = $db->prepare("
		UPDATE idk_kandidati
		SET kandidat_tf_status = :kandidat_tf_status
		WHERE kandidat_id = :kandidat_id 
	");

	$query->execute(array(
		':kandidat_id' => $kandidat_id,
		':kandidat_tf_status' => $kandidat_tf_status,
	));
}

function isTaskStatusRemovingFromTaskForce($task_force_status_id){
	Global $db;

	$query = $db->prepare("
		SELECT tfs_is_positive
		FROM idk_tf_statusi
		WHERE tfs_id  = :tfs_id  
	");

	$query->execute(array(
		':tfs_id' => $task_force_status_id
	));

	$results = $query->fetch();

	return $results["tfs_is_positive"];
}

function unreserveAgent($agent_id){
	Global $db;

	$query = $db->prepare("
						DELETE FROM idk_tf_reservations
						WHERE tf_agent_id = :tf_agent_id");

	$query->execute(array(
				':tf_agent_id' => $agent_id));
}

function unreserveCandidate($kandidat_id){
	Global $db;

	$query = $db->prepare("
						DELETE FROM idk_tf_reservations
						WHERE tf_kandidat_id = :tf_kandidat_id");

	$query->execute(array(
				':tf_kandidat_id' => $kandidat_id));
}

function getTFStatusPrijave($tf_status_id){
	Global $db;

	$query = $db->prepare("
		SELECT tfs_status_prijave
		FROM idk_tf_statusi
		WHERE tfs_id  = :tfs_id  
	");

	$query->execute(array(
		':tfs_id' => $tf_status_id
	));

	$results = $query->fetch();

	return $results["tfs_status_prijave"];
}

function updateKandidatStatusPrijave($kandidat_id, $kandidat_status_prijave){
	Global $db;

	$query = $db->prepare("
		UPDATE idk_kandidati
		SET kandidat_status_prijave = :kandidat_status_prijave
		WHERE kandidat_id = :kandidat_id
	");

	$query->execute(array(
		':kandidat_id' => $kandidat_id,
		':kandidat_status_prijave' => $kandidat_status_prijave,
	));
}

function checkKandidatStatusPrijave($kandidat_id){
	Global $db;

	$query = $db->prepare("
		SELECT kandidat_status_prijave
		FROM idk_kandidati
		WHERE kandidat_id  = :kandidat_id
	");

	$query->execute(array(
		':kandidat_id' => $kandidat_id
	));

	$results = $query->fetch();

	return $results["kandidat_status_prijave"];
}

function brojacNeuspjelaKomunikacija($tf_id){
	Global $db;

	$query = $db->prepare("
		SELECT tf_brojac_neuspjela_komunikacija
		FROM idk_task_force
		WHERE tf_id  = :tf_id AND tf_status_id = 2
	");

	$query->execute(array(
		':tf_id' => $tf_id
	));

	$results = $query->fetch();

	return intval($results["tf_brojac_neuspjela_komunikacija"]);
}

function getLastTFNalog($kandidat_id, $vrsta_id){
	Global $db;

	$query = $db -> prepare('
		SELECT tf_nalog_id
		FROM idk_task_force
		WHERE tf_candidate_id = :tf_candidate_id AND tf_vrsta_id = :tf_vrsta_id
		ORDER BY tf_id 
		DESC LIMIT 1 
	');

	$query -> execute(array(
		':tf_candidate_id' => $kandidat_id,
		':tf_vrsta_id' => $vrsta_id
	));

	$row = $query -> fetch();

	return $row['tf_nalog_id'];
}

function getLastTFProjekt($kandidat_id, $vrsta_id){
	Global $db;

	$query = $db -> prepare('
		SELECT tf_project_id
		FROM idk_task_force
		WHERE tf_candidate_id = :tf_candidate_id AND tf_vrsta_id = :tf_vrsta_id
		ORDER BY tf_id 
		DESC LIMIT 1 
	');

	$query -> execute(array(
		':tf_candidate_id' => $kandidat_id,
		':tf_vrsta_id' => $vrsta_id,
	));

	$row = $query -> fetch();

	return $row['tf_project_id'];
}

function getLastTFId($kandidat_id, $tf_nalog_id, $tf_vrsta_id){
	Global $db;
	$inbound_status = "24, 43, 44, 45, 46, 47, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 88, 89, 90, 91, 140, 141, 142, 143, 144, 145, 146";

	$query = $db -> prepare("
		SELECT tf_id
		FROM idk_task_force
		WHERE tf_candidate_id = :tf_candidate_id AND tf_nalog_id = :tf_nalog_id 
		AND tf_status_id NOT IN ($inbound_status) 
		AND tf_vrsta_id = :tf_vrsta_id
		ORDER BY tf_id 
		DESC LIMIT 1 
	");

	$query -> execute(array(':tf_candidate_id' => $kandidat_id, ':tf_nalog_id' => $tf_nalog_id, ':tf_vrsta_id' => $tf_vrsta_id));

	$row = $query -> fetch();

	return $row['tf_id'];
}

function hasInboundActiveCall($kandidat_id, $tf_nalog_id, $tf_vrsta_id){
	Global $db;
	$inbound_status = "24, 43, 44, 45, 46, 47, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 88, 89, 90, 91, 140, 141, 142, 143, 144, 145, 146";

	$query = $db -> prepare("
		SELECT tf_id
		FROM idk_task_force
		WHERE tf_candidate_id = :tf_candidate_id AND tf_nalog_id = :tf_nalog_id 
		AND tf_status_id IN ($inbound_status) 
		AND tf_last_active_task = 1 AND tf_vrsta_id = :tf_vrsta_id
		ORDER BY tf_id 
		DESC LIMIT 1 
	");

	$query -> execute(array(':tf_candidate_id' => $kandidat_id, ':tf_nalog_id' => $tf_nalog_id, ':tf_vrsta_id' => $tf_vrsta_id));

	$row = $query -> fetch();

	return $row['tf_id'];
}


function getLastTFIdInbound($kandidat_id, $tf_nalog_id, $tf_vrsta_id){
	Global $db;
	$inbound_status = "24, 43, 44, 45, 46, 47, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 88, 89, 90, 91, 140, 141, 142, 143, 144, 145, 146";

	$query = $db -> prepare("
		SELECT tf_id
		FROM idk_task_force
		WHERE tf_candidate_id = :tf_candidate_id AND tf_nalog_id = :tf_nalog_id 
		AND tf_status_id IN ($inbound_status) 
		AND tf_last_active_task = 1 AND tf_vrsta_id = :tf_vrsta_id
		ORDER BY tf_id 
		DESC LIMIT 1 
	");

	$query -> execute(array(':tf_candidate_id' => $kandidat_id, ':tf_nalog_id' => $tf_nalog_id, ':tf_vrsta_id' => $tf_vrsta_id));

	$row = $query -> fetch();

	return $row['tf_id'];
}

function getProjectListForNalog($nalog_id){
	Global $db;

	$array = array();

	$query = $db->prepare("
				SELECT project_id
				FROM idk_projects
				WHERE project_nalogid = :project_nalogid");

	$query->execute(array(':project_nalogid' => $nalog_id));

	$row = $query -> fetchAll();

	foreach($row as $element){
		array_push($array, $element["project_id"]);
	}

	$list = implode(',', $array);

	return $list;
}

function getCastingProjectForNalog($nalog_id){
	Global $db;

	$query = $db->prepare("
				SELECT project_id
				FROM idk_projects
				WHERE project_nalogid = :project_nalogid AND project_name LIKE '%Casting%'");

	$query->execute(array(':project_nalogid' => $nalog_id));

	$row = $query -> fetch();

	return $row["project_id"];
}

function getProjectIDForNalogByName($project_name, $nalog_id){
	
	Global $db;

	$query = $db->prepare("
		SELECT project_id
		FROM idk_projects
		WHERE project_name LIKE '%$project_name%'
		AND project_nalogid = :project_nalogid
	");

	$query->execute(array(
		':project_nalogid' => $nalog_id
	));
	
	$row = $query->fetch();

	return $row["project_id"];

}

function getCastingIdForNalog($nalog_id){
	Global $db;

	$query = $db->prepare("
					SELECT ppaq_id 
					FROM idk_pp_appointment_groups
					WHERE papq_nalog_id = :papq_nalog_id AND ppaq_casting_cron_executed = 0
					ORDER BY ppaq_id 
					DESC");

	$query->execute(array(':papq_nalog_id' => $nalog_id));

	$rows = $query->fetchAll(PDO::FETCH_ASSOC);

	$resultArray = array();
	foreach ($rows as $row) {
		$resultArray[] = $row["ppaq_id"];
	}

	return $resultArray;
}

function checkAdequateJobs($kandidat_id){
	//GET CANDIDATE INFO
	Global $db;
	$query_kandidat = $db->prepare("
		SELECT * FROM idk_kandidati WHERE kandidat_id = $kandidat_id ");
	$query_kandidat->execute();
	$row_kandidat = $query_kandidat->fetch();
	$kandidat_vozacka 						= $row_kandidat['kandidat_vozacka_dozvola'];
	$kandidat_vozacka_kategorija 			= $row_kandidat['kandidat_vozacka_kategorija'];
	$kandidat_iskustvo_u_struci 			= $row_kandidat["kandidat_iskustvo_u_struci"];
	$kandidat_iskustvo_u_struci_trajanje 	= $row_kandidat["kandidat_iskustvo_u_struci_trajanje"];
	$kandidat_datumrodjenja 				= $row_kandidat['kandidat_datumrodjenja'];

	//GODINE
		$today = new DateTime();
		$birthdate = new DateTime($kandidat_datumrodjenja);
		$interval = $today->diff($birthdate);
		$kandidat_age = $interval->y;
	//GODINE

	//RADNO ISKUSTVO
		if($kandidat_iskustvo_u_struci == 1){
			$where_clause_iskustvo = " pk.radno_iskustvo_struka_trajanje <= " . intval($kandidat_iskustvo_u_struci_trajanje);
		}else{
			$where_clause_iskustvo = " pk.radno_iskustvo_struka_trajanje < 0";
		}
	//RADNO ISKUSTVO

	//VOZACKA
		$niz_final = array();
		if($kandidat_vozacka !== "Ne" AND $kandidat_vozacka != null){
			if($kandidat_vozacka == "Da"){
				$niz_kand_vk = explode(',',$kandidat_vozacka_kategorija);
			}else{
				//Postoje kandidati gdje za vozacku dozvolu nije unešeno niti "Da" niti "Ne" već kategorija i ovaj else pokriva taj slučaj
				$niz_kand_vk = explode(',',$kandidat_vozacka);
			}
			if(in_array("CE", $niz_kand_vk)){
				$nize_kategorije = array("B","C1","C","C1E","CE");
				foreach($nize_kategorije as $niza_kat){
					if(!in_array($niza_kat, $niz_final, true)){
						array_push($niz_final, $niza_kat);
					}
				}
			}
			if(in_array("C1E", $niz_kand_vk)){
				$nize_kategorije = array("B","C1","C","C1E");
				foreach($nize_kategorije as $niza_kat){
					if(!in_array($niza_kat, $niz_final, true)){
						array_push($niz_final, $niza_kat);
					}
				}
			}
			if(in_array("C", $niz_kand_vk)){
				$nize_kategorije = array("B","C1","C");
				foreach($nize_kategorije as $niza_kat){
					if(!in_array($niza_kat, $niz_final, true)){
						array_push($niz_final, $niza_kat);
					}
				}
			}
			if(in_array("C1", $niz_kand_vk)){
				$nize_kategorije = array("B","C1");
				foreach($nize_kategorije as $niza_kat){
					if(!in_array($niza_kat, $niz_final, true)){
						array_push($niz_final, $niza_kat);
					}
				}
			}
			if(in_array("BE", $niz_kand_vk)){
				$nize_kategorije = array("B","BE");
				foreach($nize_kategorije as $niza_kat){
					if(!in_array($niza_kat, $niz_final, true)){
						array_push($niz_final, $niza_kat);
					}
				}
			}
			if(in_array("B", $niz_kand_vk)){
				$nize_kategorije = array("B");
				foreach($nize_kategorije as $niza_kat){
					if(!in_array($niza_kat, $niz_final, true)){
						array_push($niz_final, $niza_kat);
					}
				}
			}
		}else{
			//Ako je vozačka "Ne" ili nije unešena onda u finalni niz trpamo string kojeg nece naci u kriterijima za profile
			array_push($niz_final, 'nema');
		}
		
		$conditions = [];
		foreach ($niz_final as $item) {
			$conditions[] = "FIND_IN_SET('$item', pk.kategorija_vozacke_dozvole) > 0";
		}
		$where_clause_vozacka = implode(" OR ", $conditions);
		
	//VOZACKA

	//JEZIK
		//NJEMACKI
			if(getActiveLanguage($kandidat_id)){
				$njemacki_jezik = getActiveLanguage($kandidat_id);
			}else{
				$query_kandidat_jezik_n = $db->prepare("
					SELECT kj_slusanje FROM idk_kandidat_jezici WHERE kj_kandidatid = $kandidat_id AND kj_naziv = 'Njemački' ORDER BY kj_id DESC");
				$query_kandidat_jezik_n->execute();
				$row_kandidat_jezik_n = $query_kandidat_jezik_n->fetch();
				$njemacki_jezik = $row_kandidat_jezik_n['kj_slusanje'];
			}
			if(in_array($njemacki_jezik, array("A1", "A2", "B1", "B2", "C1", "C2"))){
				$where_clause_njemacki = $njemacki_jezik;
			}else{
				$where_clause_njemacki = "A0";
			}
		//NJEMACKI
		
		//ENGLESKI
			$query_kandidat_jezik_e = $db->prepare("
				SELECT kj_slusanje FROM idk_kandidat_jezici WHERE kj_kandidatid = $kandidat_id AND kj_naziv = 'Engleski' ORDER BY kj_id DESC");
			$query_kandidat_jezik_e->execute();
			$row_kandidat_jezik_e = $query_kandidat_jezik_e->fetch();
			$engleski_jezik = $row_kandidat_jezik_e['kj_slusanje'];
			if(in_array($engleski_jezik, array("A1", "A2", "B1", "B2", "C1", "C2"))){
				$where_clause_engleski = $engleski_jezik;
			}else{
				$where_clause_engleski = "A0";
			}
		//ENGLESKI

		//ITALIJANSKI
			$query_kandidat_jezik_i = $db->prepare("
				SELECT kj_slusanje FROM idk_kandidat_jezici WHERE kj_kandidatid = $kandidat_id AND kj_naziv = 'Talijanski' ORDER BY kj_id DESC");
			$query_kandidat_jezik_i->execute();
			$row_kandidat_jezik_i = $query_kandidat_jezik_i->fetch();
			$talijanski_jezik = $row_kandidat_jezik_i['kj_slusanje'];
			if(in_array($talijanski_jezik, array("A1", "A2", "B1", "B2", "C1", "C2"))){
				$where_clause_talijanski = $talijanski_jezik;
			}else{
				$where_clause_talijanski = "A0";
			}
		//ITALIJANSKI

		//FRANCUSKI
			$query_kandidat_jezik_f = $db->prepare("
				SELECT kj_slusanje FROM idk_kandidat_jezici WHERE kj_kandidatid = $kandidat_id AND kj_naziv = 'Francuski' ORDER BY kj_id DESC");
			$query_kandidat_jezik_f->execute();
			$row_kandidat_jezik_f = $query_kandidat_jezik_f->fetch();
			$francuski_jezik = $row_kandidat_jezik_f['kj_slusanje'];
			if(in_array($francuski_jezik, array("A1", "A2", "B1", "B2", "C1", "C2"))){
				$where_clause_francuski = $francuski_jezik;
			}else{
				$where_clause_francuski = "A0";
			}
		//FRANCUSKI


	//JEZIK

	//GET ADEQUATE JOBS
	$query_jobs = $db->prepare("
		SELECT np.id, np.naziv, np.nalog_id FROM idk_nalog_profil np
		JOIN idk_profil_kriterij pk ON np.id = pk.profil_id
		WHERE np.status = 1
		AND (
			pk.starost = 0
			OR (
				pk.starost = 1 AND pk.starost_minimum <= $kandidat_age AND pk.starost_maksimum >= $kandidat_age
			)
		)
		AND (
			pk.radno_iskustvo_struka = 0 
			OR (
				pk.radno_iskustvo_struka = 1 AND $where_clause_iskustvo
			)
		)
		AND (
			pk.vozacka_dozvola = 0
			OR (
				pk.vozacka_dozvola = 1 
				AND
				( $where_clause_vozacka )
			)
		)
		AND (
			pk.smjerovi_naloga = 0 
			OR (
				pk.smjerovi_naloga = 1 
				AND 
				EXISTS (
					SELECT ke_id FROM idk_kandidat_edukacija 
					LEFT JOIN idk_skole_smjerovi sm on ke_smjer_id = sm.ss_id
					WHERE ke_kandidat_id = $kandidat_id AND sm.ss_naziv IN (
						SELECT iss.ss_naziv FROM idk_nalog_smjer ins
						JOIN idk_skole_smjerovi iss ON ins.smjer_id = iss.ss_id
						WHERE ins.nalog_id = np.nalog_id 
					) 
				)
			)
		)
		AND	(
			pk.njemacki_jezik = 0 OR pk.njemacki_jezik is null
			OR (
				pk.njemacki_jezik = 1 AND '$where_clause_njemacki' >= pk.nivo_njemackog_jezika
			)
		)
		AND	(
			pk.engleski_jezik = 0 OR pk.engleski_jezik is null
			OR (
				pk.engleski_jezik = 1 AND '$where_clause_engleski' >= pk.nivo_engleskog_jezika
			)
		)
		AND (
			pk.francuski_jezik = 0 OR pk.francuski_jezik is null
			OR (
				pk.francuski_jezik = 1 AND '$where_clause_francuski'>= pk.nivo_francuskog_jezika
			)
		)
		AND (
			pk.italijanski_jezik = 0 OR pk.italijanski_jezik is null
			OR (
				pk.italijanski_jezik = 1 AND '$where_clause_talijanski' >= pk.nivo_italijanskog_jezika
			)
		)
		
		");
	// return $query_jobs;
	$query_jobs->execute();
	$row_jobs = $query_jobs->fetchAll(PDO::FETCH_ASSOC);

	return $row_jobs;
}

function addAppointmentForCandidate($kandidat_id, $appointment_id, $time_format, $time_id){
	Global $db;

	$query_get_candidate_appointment = $db->prepare("
													SELECT
														pca_id
													FROM
														idk_pp_cand_appts
													WHERE
														pca_appointment_id = :pca_appointment_id
													AND
														pca_kandidat_id = :kandidat_id
													");
	$query_get_candidate_appointment->execute(array(
		":pca_appointment_id" 	=> $appointment_id,
		":kandidat_id" 		=> $kandidat_id));
	
	$count_candidate_appointment = $query_get_candidate_appointment->rowCount();

	if($count_candidate_appointment > 0){
		$query_update_candidate_appointment = $db->prepare("
															UPDATE
																idk_pp_cand_appts
															SET
																pca_time = :time,
																pca_pah_id = :pca_pah_id,
																pca_status = :pca_status
																
															WHERE
																pca_appointment_id = :pca_appointment_id
															AND
																pca_kandidat_id = :kandidat_id
														");
		$query_update_candidate_appointment->execute(array(
			":time" 			=> $time_format,
			":pca_pah_id" 			=> $time_id,
			":pca_status" 			=> 1,
			":pca_appointment_id" 	=> $appointment_id,
			":kandidat_id" 		=> $kandidat_id
		));
	} else {

		$groupId = intval(getAppointmentGroup($appointment_id));

		$belongsToSameGroup = intval(checkAppointmentGroupExistance($groupId, $kandidat_id));

		//TREBALO NEKAD RADI UNOSA PREDEFINISANOG TEKSTA U ENPAL GENERAL NOTES
		// $enpal_nalog = getCompanyAccessCRMR(getNalogIdByAppointment($appointment_id));
		// if($enpal_nalog == 1 ){
		// 	$general_notice = getEnpalPreDefinedNotice();
		// }else{
		// 	$general_notice = null;
		// }

		if($belongsToSameGroup > 0){

			$get_all_appt_from_same_group = $db->prepare("
						SELECT pca_id FROM idk_pp_cand_appts
						INNER JOIN idk_pp_appointments ON idk_pp_cand_appts.pca_appointment_id = idk_pp_appointments.pap_id
						WHERE pap_group_id  = :pap_group_id AND pca_kandidat_id = :pca_kandidat_id
					");

			$get_all_appt_from_same_group->execute(array(
				':pap_group_id' => $groupId,
				':pca_kandidat_id' => $kandidat_id
			));

			$pca_ids = $get_all_appt_from_same_group->fetchAll(PDO::FETCH_COLUMN);

			$pca_ids_string = implode(",", $pca_ids);

			$delete_query = $db->prepare("
				DELETE FROM idk_pp_cand_appts
				WHERE pca_id IN ($pca_ids_string)
			");
			
			$delete_query->execute();
			
			// Dodati novi
			$query_insert_candidate_appointment = $db->prepare("
																INSERT INTO
																	idk_pp_cand_appts
																	(
																		pca_appointment_id,
																		pca_kandidat_id,
																		pca_time,
																		pca_pah_id,
																		pca_status
																	)
																VALUES
																	(
																		:pca_appointment_id,
																		:kandidat_id,
																		:time,
																		:pca_pah_id,
																		:pca_status
																	)
															");
			$query_insert_candidate_appointment->execute(array(
				":pca_appointment_id" 	=> $appointment_id,
				":kandidat_id" 		=> $kandidat_id,
				":time" 			=> $time_format,
				":pca_pah_id" 			=> $time_id,
				":pca_status" 		=> 1
			));
		}else{
			$query_update_pca_status = $db->prepare("
								UPDATE
									idk_pp_cand_appts
								SET
									pca_status = 0
								WHERE
									pca_kandidat_id = :pca_kandidat_id
								AND
									pca_status = 1"
								);

			$query_update_pca_status->execute(array(
								':pca_kandidat_id' => $kandidat_id));
			
			$query_insert_candidate_appointment = $db->prepare("
																INSERT INTO
																	idk_pp_cand_appts
																	(
																		pca_appointment_id,
																		pca_kandidat_id,
																		pca_time,
																		pca_pah_id,
																		pca_status
																	)
																VALUES
																	(
																		:pca_appointment_id,
																		:kandidat_id,
																		:time,
																		:pca_pah_id,
																		:pca_status
																	)
															");
			$query_insert_candidate_appointment->execute(array(
				":pca_appointment_id" 	=> $appointment_id,
				":kandidat_id" 		=> $kandidat_id,
				":time" 			=> $time_format,
				":pca_pah_id" 			=> $time_id,
				":pca_status" 		=> 1
			));
		}
	}

	$query_get_nalog_id = $db->prepare("
										SELECT
											pap_nalog_id
										FROM
											idk_pp_appointments
										WHERE
											pap_id = :pap_id
									");
	$query_get_nalog_id->execute(array(
		":pap_id" => $appointment_id
	));

	$result_nalog_id 	= $query_get_nalog_id->fetch();
	$nalog_id 			= $result_nalog_id['pap_nalog_id'];

	updateNalogIdForCandidat($kandidat_id, $nalog_id);
}

function hasTaskForceInterview($task_force_status_id){
	Global $db;

	$query = $db->prepare("
		SELECT tfs_has_interview
		FROM idk_tf_statusi
		WHERE tfs_id  = :tfs_id  
	");

	$query->execute(array(
		':tfs_id' => $task_force_status_id
	));

	$results = $query->fetch();

	return $results["tfs_has_interview"];
}

function isCandidateInTF($kandidat_id, $nalog_id){
	Global $db;

	$query = $db->prepare("
		SELECT tf_id
		FROM idk_task_force
		WHERE tf_candidate_id  = :tf_candidate_id AND tf_nalog_id = :tf_nalog_id 
	");

	$query->execute(array(
		':tf_candidate_id' => $kandidat_id,
		':tf_nalog_id' => $nalog_id
	));

	$result_count = $query->rowCount();

	return $result_count;
}

function isCandidateRemovedFromTF($kandidat_id, $nalog_id){
	
	//Izbačeno korištenje funkcije sa jedinog mjesta: prikaz unošenja tf statusa i bilješke.
	//Izbačeno jer su se kandidati ponovno prijavljivali na oglase od istog naloga,
	//kandidati koji su vec prozvani i oznaceni sa negativnom statusom.
	//Agenti zbog toga nisu imali mogućnost unosa novog statusa, a ostajali su rezervisani.
	//Funkcija ne koristi ničemu pa je obrisana, a ako bude trebala se koristi na tim istom mjestu
	//onda u nju treba dodati provjeru da li je kandidat opet u nekom projektu od naloga.
	Global $db;

	if(isCandidateInTF($kandidat_id, $nalog_id) != 0){
		
		$last_tf_id = getLastTFId($kandidat_id,  $nalog_id);
		$query = $db->prepare("
			SELECT tf_last_active_task
			FROM idk_task_force
			WHERE tf_id  = :tf_id AND tf_nalog_id = :tf_nalog_id
		");

		$query->execute(array(
			':tf_id' => $last_tf_id,
			':tf_nalog_id' => $nalog_id
		));

		$results = $query->fetch();
		// vrati 1 ako je i dalje u tf, 0 ako nije
		return $results["tf_last_active_task"];
		
	}else{
		return 1;
	}
}

function getGlosaInfoForNalog($nalog_id){
	Global $db;

	$query = $db -> prepare('
		SELECT nalog_slanje_na_glosu
		FROM idk_nalozi
		WHERE nalog_id = :nalog_id
	');

	$query->execute(array(':nalog_id' => $nalog_id));

	$row = $query -> fetch();

	return $row['nalog_slanje_na_glosu'];
}

function checkKandidatGlossa($kandidat_id){
	Global $db;

	$query = $db->prepare("
		SELECT kandidat_glossa
		FROM idk_kandidati
		WHERE kandidat_id  = :kandidat_id
	");

	$query->execute(array(
		':kandidat_id' => $kandidat_id
	));

	$results = $query->fetch();

	return $results["kandidat_glossa"];
}

function getCastingConfirmationOutput($kandidat_id, $pca_id){
	Global $db;
	$get_invite_links = $db->prepare("
				SELECT l1.link_status as status_prvog , l2.link_status as status_drugog
				FROM idk_appointment_invite_links l1
				LEFT JOIN idk_appointment_invite_links l2
				ON (l1.candidate_id = l2.candidate_id AND l1.counter_sent != l2.counter_sent AND l2.counter_sent = 2 AND l1.interview_id = l2.interview_id)
				WHERE l1.candidate_id = :candidate_id and l1.interview_id = :interview_id AND l1.counter_sent = 1
				LIMIT 1;
	");
	$get_invite_links->execute(array(
				':candidate_id' => $kandidat_id,
				':interview_id' => $pca_id
	));
	$row_links = $get_invite_links->fetch();
	$status_prvog = $row_links['status_prvog'];
	$status_drugog = $row_links['status_drugog'];
	switch($status_prvog){
		case null:
			$confirmation_link_name1 = "NIJE POSLANO";
			$confirmation_color1 = "default";
			break;
		case 0:
			$confirmation_link_name1 = "ARHIVA";
			$confirmation_color1 = "default";
			break;
		case 1:
			$confirmation_link_name1 = "POSLAN LINK";
			$confirmation_color1 = "warning";
			break;
		case 2:
			$confirmation_link_name1 = "OTVOREN LINK";
			$confirmation_color1 = "info";
			break;
		case 3:
			$confirmation_link_name1 = "POTVRĐEN";
			$confirmation_color1 = "success";
			break;
		default:
			$confirmation_link_name1 = "NEPOZNATO";
			$confirmation_color1 = "danger";
			break;
	}
	switch($status_drugog){
		case null:
			$confirmation_link_name2 = "NIJE POSLANO";
			$confirmation_color2 = "default";
			break;
		case 0:
			$confirmation_link_name2 = "ARHIVA";
			$confirmation_color2 = "default";
			break;
		case 1:
			$confirmation_link_name2 = "POSLAN LINK";
			$confirmation_color2 = "warning";
			break;
		case 2:
			$confirmation_link_name2 = "OTVOREN LINK";
			$confirmation_color2 = "info";
			break;
		case 3:
			$confirmation_link_name2 = "POTVRĐEN";
			$confirmation_color2 = "success";
			break;
		default:
			$confirmation_link_name2 = "NEPOZNATO";
			$confirmation_color2 = "danger";
			break;
	}

	return [$confirmation_link_name1, $confirmation_color1, $confirmation_link_name2, $confirmation_color2];
}

function getCastingConfirmationOutputByStatus($status_prvog, $status_drugog){
	switch($status_prvog){
		case null:
			$confirmation_link_name1 = "NIJE POSLANO";
			$confirmation_color1 = "default";
			break;
		case 0:
			$confirmation_link_name1 = "ARHIVA";
			$confirmation_color1 = "default";
			break;
		case 1:
			$confirmation_link_name1 = "POSLAN LINK";
			$confirmation_color1 = "warning";
			break;
		case 2:
			$confirmation_link_name1 = "OTVOREN LINK";
			$confirmation_color1 = "info";
			break;
		case 3:
			$confirmation_link_name1 = "POTVRĐEN";
			$confirmation_color1 = "success";
			break;
		default:
			$confirmation_link_name1 = "NEPOZNATO";
			$confirmation_color1 = "danger";
			break;
	}
	switch($status_drugog){
		case null:
			$confirmation_link_name2 = "NIJE POSLANO";
			$confirmation_color2 = "default";
			break;
		case 0:
			$confirmation_link_name2 = "ARHIVA";
			$confirmation_color2 = "default";
			break;
		case 1:
			$confirmation_link_name2 = "POSLAN LINK";
			$confirmation_color2 = "warning";
			break;
		case 2:
			$confirmation_link_name2 = "OTVOREN LINK";
			$confirmation_color2 = "info";
			break;
		case 3:
			$confirmation_link_name2 = "POTVRĐEN";
			$confirmation_color2 = "success";
			break;
		default:
			$confirmation_link_name2 = "NEPOZNATO";
			$confirmation_color2 = "danger";
			break;
	}

	return [$confirmation_link_name1, $confirmation_color1, $confirmation_link_name2, $confirmation_color2];
}
function disconnectAgentFromCandidate($kandidat_id){
	Global $db;

	$query_get_agent = $db->prepare("
		SELECT tf_reserved_agent 
		FROM idk_kandidati
		WHERE kandidat_id = :kandidat_id
	");
	$query_get_agent->execute(array(
		':kandidat_id' => $kandidat_id
	));
	$row_get_agent = $query_get_agent->fetch();
	$agent_id = $row_get_agent['tf_reserved_agent'];

	if($agent_id != null){

		$query = $db->prepare("
			UPDATE idk_kandidati
			SET tf_reserved_agent = :tf_reserved_agent
			WHERE kandidat_id = :kandidat_id
		");

		$query->execute(array(
			':tf_reserved_agent' => NULL,
			':kandidat_id' => $kandidat_id
		));

		return $agent_id;
	}else{
		return null;
	}
}

function connectAgentToCandidate($kandidat_id, $agent_id){
	Global $db;
	$query = $db->prepare("
		UPDATE idk_kandidati
		SET tf_reserved_agent = :tf_reserved_agent
		WHERE kandidat_id = :kandidat_id
	");

	$query->execute(array(
		':tf_reserved_agent' => $agent_id,
		':kandidat_id' => $kandidat_id
	));
}

function getFullReservedAgentFromCandidate($kandidat_id){
	Global $db;

	$query_get_agent = $db->prepare("
		SELECT tf_reserved_agent 
		FROM idk_kandidati
		WHERE kandidat_id = :kandidat_id
	");
	$query_get_agent->execute(array(
		':kandidat_id' => $kandidat_id
	));
	$row_get_agent = $query_get_agent->fetch();
	$agent_id = $row_get_agent['tf_reserved_agent'];
	
	return $agent_id;
}

function checkTFstatsForCandidate($kandidat_id, $casting_group_id){
	Global $db;

	$query_check_tf_stats = $db->prepare("
		SELECT tsr_id FROM idk_tf_stats_reservations
		WHERE tsr_candidate_id = :kandidat_id AND tsr_interview_id = :casting_group_id AND tsr_status IN (2,3,4,5)
	");

	$query_check_tf_stats->execute(array(
		':kandidat_id' => $kandidat_id,
		':casting_group_id' => $casting_group_id
	));
	$row_tf_stats = $query_check_tf_stats->fetch();
	$tsr_id = $row_tf_stats['tsr_id'];
	
	return $tsr_id;
}

function checkTFstatsPristaoDolaziForCandidate($kandidat_id, $casting_group_id){
	Global $db;

	$query_check_tf_stats = $db->prepare("
		SELECT tsr_id FROM idk_tf_stats_reservations
		WHERE tsr_candidate_id = :kandidat_id AND tsr_interview_id = :casting_group_id AND tsr_status IN (4,5)
	");

	$query_check_tf_stats->execute(array(
		':kandidat_id' => $kandidat_id,
		':casting_group_id' => $casting_group_id
	));
	$row_tf_stats = $query_check_tf_stats->fetch();
	$tsr_id = $row_tf_stats['tsr_id'];
	
	return $tsr_id;
}

function updateTFStat($tsr_id, $status){
	Global $db;
	$query = $db->prepare("UPDATE idk_tf_stats_reservations SET tsr_status = $status WHERE tsr_id = $tsr_id");
	$query->execute();
}
function updateTFStatWithAgent($tsr_id, $status, $agent_id){
	Global $db;
	$query = $db->prepare("UPDATE idk_tf_stats_reservations SET tsr_status = $status, tsr_agent_id = $agent_id WHERE tsr_id = $tsr_id");
	$query->execute();
}

function insertTFStat($tsr_candidate_id, $tsr_agent_id, $tsr_interview_id, $tsr_nalog_id, $tsr_status){
	Global $db;

	$query_check = $db->prepare("
			SELECT tsr_id FROM idk_tf_stats_reservations WHERE tsr_candidate_id = :tsr_candidate_id AND tsr_interview_id = :tsr_interview_id AND tsr_status = :tsr_status
	");
	$query_check->execute(array(
		':tsr_candidate_id' => $tsr_candidate_id,
		':tsr_interview_id' => $tsr_interview_id,
		':tsr_status' => $tsr_status
	));
	if($query_check->rowCount() == 0){
		$tsr_link_id = getLinkForCandidate($tsr_candidate_id);
		$query = $db->prepare("
				INSERT INTO idk_tf_stats_reservations
					(tsr_candidate_id, tsr_agent_id, tsr_interview_id, tsr_nalog_id, tsr_link_id, tsr_status)
				VALUES
					(:tsr_candidate_id, :tsr_agent_id, :tsr_interview_id, :tsr_nalog_id, :tsr_link_id, :tsr_status)");

		$query->execute(array(
				':tsr_candidate_id' => $tsr_candidate_id,
				':tsr_agent_id' => $tsr_agent_id,
				':tsr_interview_id' => $tsr_interview_id,
				':tsr_nalog_id' => $tsr_nalog_id,
				':tsr_link_id' => $tsr_link_id,
				':tsr_status' => $tsr_status
		));
	}else
		return "cxvx";
}

function getLinkForCandidate($kandidat_id){
	Global $db;

	$query = $db->prepare("SELECT kandidat_visitedurl FROM idk_kandidati WHERE kandidat_id = $kandidat_id");
	$query->execute();
	$row = $query->fetch();

	return $row['kandidat_visitedurl'];
}

function getCandidateDepartureType($candidate_id){
	// Return values:
	// [0, "Stručni kadar"]
	// [1, "EU Kandidat"]
	// [2, "Zapadno-balkanski sistem"]
	// [3, "Radno iskustvo"]

	Global $db;

	$query_get_departure_type = $db -> prepare("
		SELECT
			CASE 
				WHEN kandidat_drzavljanstvo_vrsta LIKE  ('EU državljanin') THEN 1
				WHEN kandidat_nacin_odlaska != 0 THEN kandidat_nacin_odlaska 
				ELSE 0 
			END 
			AS 
			departure_type
		FROM 
			idk_kandidati
		WHERE 
			kandidat_id = $candidate_id
	");

	$query_get_departure_type -> execute();
	$row = $query_get_departure_type -> fetch();
	$departure_type = $row['departure_type'];


	if($departure_type == 0)
		return array($departure_type, "Stručni kadar");

	else if($departure_type == 1)
		return array($departure_type, "EU Kandidat");

	else if($departure_type == 3)
		return array($departure_type, "Radno iskustvo");

	return array($departure_type, "Zapadno-balkanski sistem");
}

function getCandidateParallelAppliesWestBalkan($candidateId) {
	Global $db; 
	$candidateId = intval($candidateId);

	$query = $db->prepare("
		SELECT
			kandidat_paralelno_zb
		FROM 
			idk_kandidati
		WHERE 
			kandidat_id = :kandidat_id
	");
	$query->execute(array(
		':kandidat_id' => $candidateId
	));
	if ($query->rowCount() == 1) {
		$row = $query->fetch();
		return intval($row["kandidat_paralelno_zb"]); 
	} else {
		return 0;
	}
}

function getCandidateFullRecognitionArrayR($candidateID, $type) {
	Global $db; 

	$type = intval($type); 
	$candidateID = intval($candidateID);

	$result = array(
		"count" => 0,
		"id_nd" => 0,
		"file_nd" => "",
		"upload_date_nd" => "",
		"upload_employee_id" => 0, 
		"full_recognition" => ""
	);

	if ($type != 0 AND $candidateID != 0){

		$flagType = 0;

		if ($type == 1) {
			$flagType = 1;
			//proslijedjeni candidatID je kandidat posao id
			$conditionId = "id_cand_job = :candidateId"; 
		} else if ($type == 2) {
			$flagType = 1;
			//proslijedjeni candidatID je kandidat dipl id
			$conditionId = "id_cand_dipl = :candidateId"; 
		}

		if ($flagType == 1) {
			$query = $db->prepare("
				SELECT 
					id_nd,
					file_nd,
					upload_date_nd,
					upload_employee_id, 
					full_recognition
				FROM 
					idk_nostrifikovane_diplome
				WHERE 
					".$conditionId."
			"); 
			$query->execute(array(
				":candidateId" => $candidateID
			));
			if ($query->rowCount() == 1) {
				$row = $query->fetch();
				$result["count"] = 1; 
				$result["id_nd"] = intval($row["id_nd"]);
				$result["file_nd"] = $row["file_nd"]; 
				$result["upload_date_nd"] = date("d.m.Y", strtotime($row["upload_date_nd"])); 
				$result["upload_employee_id"] = getEmployeeFullnameById($row["upload_employee_id"]); 
				$result["full_recognition"] = (($row["full_recognition"] != null) ? $row["full_recognition"] : ""); 
			}
		}
	}

	return $result;
}

function getCandidateFullRecognition($candidateId) {

	Global $db; 
	$candidateId = intval($candidateId); //kandidat posao id

	if ( $candidateId != 0 ) {
		
		$queryCheck = $db->prepare("
			SELECT 
				kandidat_dipl_id 
			FROM 
				idk_kandidati
			WHERE 
				kandidat_id = :kandidat_id 
		");  

		$queryCheck->execute(array(
			':kandidat_id' => $candidateId
		));

		$rowCheck = $queryCheck->fetch();

		$candidateDiplId = intval($rowCheck["kandidat_dipl_id"]); 

		$queryCondition = "";

		if( $candidateDiplId != 0 ) {

			$queryCondition = "AND id_cand_dipl = ".$candidateDiplId."";

		}

		$query = $db->prepare("
			SELECT 
				full_recognition
			FROM 
				idk_nostrifikovane_diplome
			WHERE 
				id_cand_job = :id_cand_job
				".$queryCondition."
		");

		$query->execute(array(
			':id_cand_job' => $candidateId
		));

		if ( $query->rowCount() == 1 ) {

			$row = $query->fetch();

			if ( $row["full_recognition"] != null ) {
				return $row["full_recognition"];
			}else {
				return 100;
			}
			
			

		} else {

			return 101;

		}

	} else {

		return 102;

	}
}

function getTaskForceInfoForNalog($nalog_id, $vrsta_id) {
    Global $db;
    $query = $db->prepare("SELECT tf_candidate_id, tf_status_id, tf_project_id FROM idk_task_force WHERE tf_nalog_id = :nalog_id AND tf_vrsta_id = :tf_vrsta_id AND tf_last_active_task = 1 GROUP BY tf_candidate_id");
    $query->execute(array(':nalog_id' => $nalog_id, ':tf_vrsta_id' => $vrsta_id));
    $row = $query->fetchAll();
    return json_encode($row);
}

function updateLastActiveTaskForceCandForNalog($nalog_id, $vrsta_id) {
    Global $db;
	//Sa update-om last active taska mora se update-ati i tabela kandidata, kandidat_tf_status i tf_reserved_agent (zbog dopune i zainteresiran)
	//Prvo se trebaju pokupiti svi kandidati kojima će se odraditi update
	$candidates_ids = array();
	$get_cand = $db->prepare("SELECT tf_candidate_id FROM idk_task_force where tf_nalog_id = :tf_nalog_id AND tf_vrsta_id = :tf_vrsta_id AND tf_last_active_task = 1 AND tf_status_id NOT IN (16,18)");
	$get_cand->execute(array(':tf_nalog_id' => $nalog_id, ':tf_vrsta_id' => $vrsta_id));
	while($row_cand = $get_cand->fetch()){
		array_push($candidates_ids, $row_cand['tf_candidate_id']);
	}
	$candidates = implode(",", $candidates_ids);

	$update_cand = $db->prepare("UPDATE idk_kandidati SET kandidat_tf_status = null, tf_reserved_agent = null WHERE kandidat_id IN ($candidates)");
	$update_cand->execute();
    $query = $db->prepare("
							UPDATE idk_task_force
							SET tf_last_active_task = 0
							WHERE tf_nalog_id = :nalog_id AND tf_last_active_task = 1 AND tf_status_id NOT IN (16,18) AND tf_vrsta_id = :tf_vrsta_id
						");
    
	$query->execute(array(':nalog_id' => $nalog_id, ':tf_vrsta_id' => $vrsta_id));
}

function checkIfTFHasUnfinishedCandidatesOnStatus($nalog_id, $status) {
    Global $db;
    $query = $db->prepare("SELECT tf_candidate_id, tf_status_id, tf_project_id FROM idk_task_force WHERE tf_nalog_id = :nalog_id AND tf_last_active_task = 1 AND tf_status_id IN ($status) GROUP BY tf_candidate_id");
    $query->execute(array(':nalog_id' => $nalog_id));
    $count = $query->rowCount();
	// ako je nula nema onda na pristao i dolazi
    return intval($count);
}

function getAppointmentPredefinedTimes($pap_id){
	Global $db;
    $query = $db->prepare("SELECT pah_id, pah_time FROM idk_pp_appointment_hours WHERE pap_id = :pap_id");
    $query->execute(array(':pap_id' => $pap_id));
    $row = $query->fetchAll();

	return $row;
}

function getTimeForAppointment($pah_id){
	Global $db;
    $query = $db->prepare("SELECT pah_time FROM idk_pp_appointment_hours WHERE pah_id = :pah_id");
    $query->execute(array(':pah_id' => $pah_id));
    $row = $query->fetch();

	return $row["pah_time"];
}
/////////////////////////////////////////////////////
//FUNKCIJE VEZANE ZA KREIRANJE RATA KANDIDATA START//
/////////////////////////////////////////////////////
function getNalogNacinPlacanja($nalog_id){
	Global $db;
	$sql = "SELECT nalog_financije FROM idk_nalozi WHERE nalog_id = :nalog_id";
	$query = $db->prepare($sql);
	$query->execute(array(':nalog_id' => $nalog_id));
	$row = $query->fetch();

	return $row["nalog_financije"];
}

function getNalogRate($nalog_id){
	Global $db;

	$rate = [];
	
	$sql = "SELECT nr_id, nr_procenat, nr_vrijeme_placanja, nr_mjeseci_nakon FROM idk_nalozi_rate WHERE nr_nalog = :nalog_id";
	$query = $db->prepare($sql);
	$query->execute(array(':nalog_id' => $nalog_id));

	while($row = $query->fetch()){
		$rata = [];

		$rata["nr_id"] = $row["nr_id"];
		$rata["nr_procenat"] = $row["nr_procenat"];
		$rata["nr_vrijeme_placanja"] = $row["nr_vrijeme_placanja"];
		$rata["nr_mjeseci_nakon"] = $row["nr_mjeseci_nakon"];

		$rate[] = $rata;
	}

	return $rate;
}

function getNalogProvizija($nalog_id){
	Global $db;

	$sql = "SELECT nalog_provizija FROM idk_nalozi WHERE nalog_id = :nalog_id";
	$query = $db->prepare($sql);
	$query->execute(array(':nalog_id' => $nalog_id));
	$row = $query->fetch();

	return $row["nalog_provizija"];
}

function getNalogProvizijaPlata($nalog_id){
	Global $db;

	$sql = "SELECT nalog_provizija_po_plati FROM idk_nalozi WHERE nalog_id = :nalog_id";
	$query = $db->prepare($sql);
	$query->execute(array(':nalog_id' => $nalog_id));
	$row = $query->fetch();

	return $row["nalog_provizija_po_plati"];
}

function getKandidatPlata($kandidat_id){
	Global $db;

	$sql = "SELECT kandidat_pp_plata FROM idk_kandidati WHERE kandidat_id = :kandidat_id";
	$query = $db->prepare($sql);
	$query->execute(array(':kandidat_id' => $kandidat_id));
	$row = $query->fetch();

	return $row["kandidat_pp_plata"];
}

function getIznosRateKandidata($provizija, $procenat, $plata = null, $nacin_placanja){
	$iznos = 0;

	switch($nacin_placanja){
		//standardni nacin
		case 1:
			//provizija po kandidatu, npr. 5000eura
			$iznos = $provizija * ($procenat / 100);
		break;

		//po plati kandidata
		case 3:
			//provizija je koeficijent po plati, npr. 2.5 plate
			$iznos = $provizija * $plata * ($procenat / 100);
		break;
	}

	return $iznos;
}

function getDurationZaPrikupljanjeDokumentacijeDIPL(){
	Global $db;

	$sql = "SELECT dpr_duration FROM idk_duration_per_status WHERE dpr_status_name LIKE 'Prikupljanje dokumentacije'";
	$query = $db->prepare($sql);
	$query->execute();
	$row = $query->fetch();

	return $row["dpr_duration"];
}

function insertNostrifikacijaRata($kandidat_id, $nalog_id){
	Global $db;

	if(!checkEUKandidat($kandidat_id)){

		$sql = "SELECT nalog_placa_nostrifikaciju, nalog_nacin_nostrifikacije, nalog_provizija_nostrifikacija FROM idk_nalozi WHERE nalog_id = :nalog_id";
		$query = $db->prepare($sql);
		$query->execute(array(':nalog_id' => $nalog_id));
		$row = $query->fetch();

		if($row['nalog_placa_nostrifikaciju'] == 1 && $row['nalog_nacin_nostrifikacije'] == 1){

			if(!checkIfKandidatRataNostrifikacijaPostoji($kandidat_id, $nalog_id)){
				$provizija = $row['nalog_provizija_nostrifikacija'];
				$kf_placeno = 0;
				$current_date = date("Y-m-d");

				/*
					Provjera Datum potpisa ugovora START
					*/
						$flagUgovor = 0; 
						$datum_ugovor = date("Y-m-d");
						$queryCheckCandidateUgovor = $db ->prepare("
							SELECT 
								MAX(lsp.lsp_datetime) AS datum_ugovor
							FROM 
								idk_log_statusi_prijave lsp
							INNER JOIN 
								idk_projects pr 
							ON 
								pr.project_id = lsp.lsp_projekt_id
							WHERE 
								lsp.lsp_kandidat_id = :lsp_kandidat_id 
								AND 
								lsp.lsp_status_prijave_id = 9 
								AND 
								pr.project_nalogid = :nalog_id 
						");
						$queryCheckCandidateUgovor->execute(array(
							':lsp_kandidat_id' => $kandidat_id, 
							':nalog_id' => $nalog_id
						));
						
						if ( $queryCheckCandidateUgovor->rowCount() == 1 ){
							$rowCheckCandidateUgovor = $queryCheckCandidateUgovor->fetch();

							if ($rowCheckCandidateUgovor["datum_ugovor"] != NULL) {
								$flagUgovor = 1;
								$datum_ugovor = date("Y-m-d", strtotime($rowCheckCandidateUgovor["datum_ugovor"]));
							}
							
						}
					/*
					Provjera Datum potpisa ugovora END
				*/

				/*
					Provjera DIPL statusa START
					*/
						$queryCheckCandidateDIPL = $db->prepare("
							SELECT 
								nd_kan.id_broj_nd_kandidata,
								nd_kan.status_nd_kandidata
							FROM 
								idk_kandidati kan
							INNER JOIN 
								idk_nd_kandidata nd_kan
							ON 
								nd_kan.id_broj_nd_kandidata = kan.kandidat_dipl_id
							WHERE 
								kan.kandidat_id = :candidate_id
						");
						$queryCheckCandidateDIPL->execute(array(
							':candidate_id' => $kandidat_id
						));

						if ( $queryCheckCandidateDIPL->rowCount() == 1 ) { 

							$rowCheckCandidateDIPL 		= $queryCheckCandidateDIPL->fetch();
							$id_broj_nd_kandidata 		= $rowCheckCandidateDIPL["id_broj_nd_kandidata"];
							$status_nd_kandidata		= $rowCheckCandidateDIPL["status_nd_kandidata"]; 

							if ( in_array($status_nd_kandidata, array(3,4,5,6))) {

								$queryLogStatus = $db->prepare("
									SELECT
										vrijeme_promjene_statusa_nd_kandidata 
									FROM 
										idk_nd_kandidata_status_log 
									WHERE 
										idd_broj_nd_kandidata = :idd_broj_nd_kandidata 
										AND
										status_nd_kandidata = 3 
										AND 
										id_log_status_nd_kandidata IN (
											SELECT 
												MAX(id_log_status_nd_kandidata) 
											FROM 
												idk_nd_kandidata_status_log
											WHERE 
												idd_broj_nd_kandidata = :idd_broj_nd_kandidata 
												AND
												status_nd_kandidata = 3
										)
								");
								$queryLogStatus->execute(array(
									':idd_broj_nd_kandidata' => $id_broj_nd_kandidata
								));

								if ( $queryLogStatus->rowCount() == 1 ) {

									$rowLogStatus = $queryLogStatus->fetch();
									$vrijeme_poslana_posta = date("Y-m-d", strtotime($rowLogStatus["vrijeme_promjene_statusa_nd_kandidata"])); 
									
									if ($flagUgovor == 1) {

										if ( $vrijeme_poslana_posta < $datum_ugovor ) {
											$date = date('Y-m-d', strtotime($datum_ugovor));
										} else {
											$date = date('Y-m-d', strtotime($vrijeme_poslana_posta));
										}
			
										if ($date <= $current_date){
											$kf_placeno = 1;
										} else {
											$kf_placeno = 0;
										}

									} else {

										$kf_placeno = 4;
										$date = date('Y-m-d');

									}
								
								} else {

									$broj_dana = getDurationZaPrikupljanjeDokumentacijeDIPL();
									if ($flagUgovor == 1) {
										$date = date('Y-m-d', strtotime($datum_ugovor." +$broj_dana days"));
										if ($date <= $current_date){
											$kf_placeno = 1;
										} else {
											$kf_placeno = 0;
										}
									} else {
										$kf_placeno = 4;
										$date = date('Y-m-d');
									}

								}

							} else {

								$broj_dana = getDurationZaPrikupljanjeDokumentacijeDIPL();
								if ($flagUgovor == 1) {
									$date = date('Y-m-d', strtotime($datum_ugovor." +$broj_dana days"));
									$kf_placeno = 0;
									//Maknuo sam odavde provjeru da li je predviđeno vrijeme za fakturisanje proslo
									//jer kandidati provedu više od 15 dana na prikupljanju dokumentacije.
									//Ako stavimo kf_placeno = 1 onda se nece update-ovati datum kad predju na poslanu postu.
								} else {
									$kf_placeno = 4;
									$date = date('Y-m-d');
								}
								
							}

						} else {

							$broj_dana = getDurationZaPrikupljanjeDokumentacijeDIPL();
							if ($flagUgovor == 1) {
								$date = date('Y-m-d', strtotime($datum_ugovor." +$broj_dana days"));
								if ($date <= $current_date){
									$kf_placeno = 1;
								} else {
									$kf_placeno = 0;
								}
							} else {
								$kf_placeno = 4;
								$date = date('Y-m-d');
							}
							
						}
					/*
					Provjera DIPL statusa END
				*/
				
				// if(checkKandidatVisak($nalog_id)){
				// 	$kf_status = 3;
				// }else{
					$kf_status = 1;
				// }

				/*
					Insert faktura nostrifikacija START
					*/
						$sql = "
							INSERT INTO idk_kandidat_financije 
								(nalog_id, kandidat_id, kf_datum, kf_datum_stvarni, kf_type, kf_iznos, kf_placeno, kf_status)
							VALUES 
								(:nalog_id, :kandidat_id, :datum, :datum_stvarni, 0, :iznos, :kf_placeno, :kf_status)
						";
						$query = $db->prepare($sql);
						$query->execute(array(
							':nalog_id' 		=> $nalog_id,
							':kandidat_id' 		=> $kandidat_id,
							':datum' 			=> $date,
							':datum_stvarni' 	=> $date,
							':iznos' 			=> $provizija, 
							':kf_placeno' 		=> $kf_placeno,
							':kf_status' 		=> $kf_status
						));
					/*
					Insert faktura nostrifikacija END
				*/
			}
		}
	}
}

function checkKandidatVisak($nalog_id){
	Global $db;

	$sql_nadjeni = "SELECT count(distinct kandidat_id) as found FROM idk_kandidat_financije WHERE nalog_id = :nalog_id AND kf_status = 1";
	$stmt_nadjeni = $db->prepare($sql_nadjeni);
	$stmt_nadjeni->execute(array(':nalog_id' => $nalog_id));
	$row_nadjeni = $stmt_nadjeni->fetch();

	$sql_broj_kandidata = "SELECT nalog_potrebno_kandidata FROM idk_nalozi WHERE nalog_id = :nalog_id";
	$stmt_broj_kandidata = $db->prepare($sql_broj_kandidata);
	$stmt_broj_kandidata->execute(array(':nalog_id' => $nalog_id));
	$row_broj_kandidata = $stmt_broj_kandidata->fetch();


	if($row_nadjeni['found'] >= $row_broj_kandidata['nalog_potrebno_kandidata']){
		return true;
	}else{
		return false;
	}
}

function checkExistenceSimulatedPotpisUgovora($nalog_id){
	Global $db;
	$check_faktura = $db->prepare("SELECT nf_id, nf_datum_aktiviranja , nf_iznos FROM idk_nalog_financije WHERE nf_nalog_id = $nalog_id AND nf_type = 5");
	$check_faktura->execute();
	if($check_faktura->rowCount() > 0){
		$row_faktura = $check_faktura->fetch();
		$nf_id = $row_faktura['nf_id'];
		deleteSimulatedPotpisUgovora($nf_id);
		
		$nf_datum_aktiviranja = $row_faktura['nf_datum_aktiviranja'];
		$nf_iznos = $row_faktura['nf_iznos'];
		
		$log_desc = "Obrisana simulirana rata za potpis ugovora za nalog ".$nalog_id.". Iznos: ".$nf_iznos.". Datum aktiviranja: ".$nf_datum_aktiviranja;

		addToLogs($log_desc, 4);
	}
}

function deleteSimulatedPotpisUgovora($nf_id){
	Global $db;
	$delete_faktura = $db->prepare("DELETE FROM idk_nalog_financije WHERE nf_id = $nf_id");
	$delete_faktura->execute();

}

function generisiRateZaKandidata($kandidat_id, $nalog_id){
	Global $db;
	//kandidat projekcija
	include_once($_SERVER["DOCUMENT_ROOT"] . '/jobstep_pp/includes/classes/candidatesProjection.php');
	$durationPerStatus = new durationPerStatus();

	$nalog_nacin_placanja = getNalogNacinPlacanja($nalog_id);

	$candidatesProjection = new candidatesProjection($durationPerStatus, $kandidat_id);
	$candidate_date = $candidatesProjection -> getCandidateProjectionRows();
	$candidate_potential_work_start = getPotentialWorkStart($kandidat_id);
	if($candidate_potential_work_start != null){
		$new_date_for_projection = $candidate_potential_work_start;
	}else{
		$new_date_for_projection = $candidate_date[0]["candidate_assessment"];
	}

	$work_start_date = date("Y-m-d", $new_date_for_projection);
	
	switch($nalog_nacin_placanja){
		//standardni nacin
		case 1:
			checkExistenceSimulatedPotpisUgovora($nalog_id);
			$rate = getNalogRate($nalog_id);
			$provizija = getNalogProvizija($nalog_id);
			$visak_flag = 0;
			$kf_status = 1;

			// if(checkKandidatVisak($nalog_id)){
			// 	$visak_flag = 1;
			// 	$kf_status = 3;
			// }
			// $rate_zamjena = getOdustaneRateBezZamjene($nalog_id);

			// if(count($rate_zamjena) > 0){
			// 	foreach($rate_zamjena as $rata_zamjena){
			// 		$sql = "UPDATE idk_kandidat_financije SET kf_zamjena_id = $kandidat_id WHERE kf_id = $rata_zamjena";
			// 		$query = $db->prepare($sql);
			// 		$query->execute();
			// 	}
			// }
			foreach($rate as $rata){
				if($rata['nr_vrijeme_placanja'] != "odmah"){
					$iznos = getIznosRateKandidata($provizija, $rata['nr_procenat'], null, $nalog_nacin_placanja);

					if($rata['nr_vrijeme_placanja'] == "ugovor"){

						if(!checkIfKandidatRataPostoji(1, $kandidat_id, $rata['nr_id'])){
							$date = date("Y-m-d");
							$rata_id = $rata['nr_id'];

							$sql = "INSERT INTO 
										idk_kandidat_financije (nalog_id, kandidat_id, kf_datum, kf_datum_stvarni, kf_nalog_rata_id, kf_type, kf_iznos, kf_placeno, kf_status)
									VALUES 
										(:nalog_id, :kandidat_id, :datum, :datum_stvarni, :kf_nalog_rata_id, 1, :iznos, 1, :kf_status)";

							$query = $db->prepare($sql);
							$query->execute(array(
								':nalog_id' => $nalog_id,
								':kandidat_id' => $kandidat_id,
								':datum' => $date,
								':datum_stvarni' => $date,
								':kf_nalog_rata_id' => $rata_id,
								':iznos' => $iznos,
								':kf_status' => $kf_status
							));
						}

					}
					elseif($rata['nr_vrijeme_placanja'] == "dobio vizu"){

						if(!checkIfKandidatRataPostoji(2, $kandidat_id, $rata['nr_id'])){
							$date = date("Y-m-d", $candidate_date[0]["visa_acquired"]);
							$rata_id = $rata['nr_id'];

							$sql = "INSERT INTO 
										idk_kandidat_financije (nalog_id, kandidat_id, kf_datum, kf_datum_stvarni, kf_nalog_rata_id, kf_type, kf_iznos, kf_placeno, kf_status)
									VALUES 
										(:nalog_id, :kandidat_id, :datum, :datum_stvarni, :kf_nalog_rata_id, 2, :iznos, 0, :kf_status)";

							$query = $db->prepare($sql);
							$query->execute(array(
								':nalog_id' => $nalog_id,
								':kandidat_id' => $kandidat_id,
								':datum' => $date,
								':datum_stvarni' => $date,
								':kf_nalog_rata_id' => $rata_id,
								':iznos' => $iznos,
								':kf_status' => $kf_status
							));
						}

					}
					elseif($rata['nr_vrijeme_placanja'] == "pocetak rada"){

						if(!checkIfKandidatRataPostoji(3, $kandidat_id, $rata['nr_id'])){
							$rata_id = $rata['nr_id'];

							$sql = "INSERT INTO 
										idk_kandidat_financije (nalog_id, kandidat_id, kf_datum, kf_datum_stvarni, kf_nalog_rata_id, kf_type, kf_iznos, kf_placeno, kf_status)
									VALUES 
										(:nalog_id, :kandidat_id, :datum, :datum_stvarni, :kf_nalog_rata_id, 3, :iznos, 0, :kf_status)";
							$query = $db->prepare($sql);
							$query->execute(array(
								':nalog_id' => $nalog_id,
								':kandidat_id' => $kandidat_id,
								':datum' => $work_start_date,
								':datum_stvarni' => $work_start_date,
								':kf_nalog_rata_id' => $rata_id,
								':iznos' => $iznos,
								':kf_status' => $kf_status
							));
						}
					}
					elseif($rata['nr_vrijeme_placanja'] == "mjeseci nakon"){

						if(!checkIfKandidatRataPostoji(4, $kandidat_id, $rata['nr_id'])){
							$broj_mjeseci = $rata['nr_mjeseci_nakon'];

							$date = date('Y-m-d', strtotime($work_start_date . " + $broj_mjeseci months"));
							$rata_id = $rata['nr_id'];

							$sql = "INSERT INTO 
										idk_kandidat_financije (nalog_id, kandidat_id, kf_datum, kf_datum_stvarni, kf_nalog_rata_id, kf_type, kf_iznos, kf_placeno, kf_status)
									VALUES 
										(:nalog_id, :kandidat_id, '$date', '$date', :kf_nalog_rata_id, 4, :iznos, 0, :kf_status)";

							$query = $db->prepare($sql);
							$query->execute(array(
								':nalog_id' => $nalog_id,
								':kandidat_id' => $kandidat_id,
								':kf_nalog_rata_id' => $rata_id,
								':iznos' => $iznos,
								':kf_status' => $kf_status
							));
						}

					}
				}
			}
		break;

		//po plati kandidata
		case 3:
			$rate = getNalogRate($nalog_id);
			$provizija = getNalogProvizijaPlata($nalog_id);
			$plata = getKandidatPlata($kandidat_id);
			$kf_status = 1;

			$visak_flag = 0;
			
			// if(checkKandidatVisak($nalog_id)){
			// 	$visak_flag = 1;
			// 	$kf_status = 3;
			// }
			// $rate_zamjena = getOdustaneRateBezZamjene($nalog_id);

			// if(count($rate_zamjena) > 0){
			// 	foreach($rate_zamjena as $rata_zamjena){
			// 		$sql = "UPDATE idk_kandidat_financije SET kf_zamjena_id = $kandidat_id WHERE kf_id = $rata_zamjena";
			// 		$query = $db->prepare($sql);
			// 		$query->execute();
			// 	}
			// }
			foreach($rate as $rata){
				if($rata['nr_vrijeme_placanja'] != "odmah"){
					$iznos = getIznosRateKandidata($provizija, $rata['nr_procenat'], $plata, $nalog_nacin_placanja);

					switch($rata['nr_vrijeme_placanja']){
						case "ugovor":

							if(!checkIfKandidatRataPostoji(1, $kandidat_id, $rata['nr_id'])){
								$date = date("Y-m-d");
								$rata_id = $rata['nr_id'];

								$sql = "INSERT INTO 
											idk_kandidat_financije (nalog_id, kandidat_id, kf_datum, kf_datum_stvarni, kf_nalog_rata_id, kf_type, kf_iznos, kf_placeno, kf_status)
										VALUES 
											(:nalog_id, :kandidat_id, :datum, :datum_stvarni, :kf_nalog_rata_id, 1, :iznos, 1, :kf_status)";

								$query = $db->prepare($sql);
								$query->execute(array(
									':nalog_id' => $nalog_id,
									':kandidat_id' => $kandidat_id,
									':datum' => $date,
									':datum_stvarni' => $date,
									':kf_nalog_rata_id' => $rata_id,
									':iznos' => $iznos,
									':kf_status' => $kf_status
								));
							}

						break;

						case "dobio vizu":

							if(!checkIfKandidatRataPostoji(2, $kandidat_id, $rata['nr_id'])){
								$date = date("Y-m-d", $candidate_date[0]["acquired_visa"]);
								$rata_id = $rata['nr_id'];

								$sql = "INSERT INTO 
											idk_kandidat_financije (nalog_id, kandidat_id, kf_datum, kf_datum_stvarni, kf_nalog_rata_id, kf_type, kf_iznos, kf_placeno, kf_status)
										VALUES 
											(:nalog_id, :kandidat_id, :datum, :datum_stvarni, :kf_nalog_rata_id, 3, :iznos, 0, :kf_status)";

								$query = $db->prepare($sql);
								$query->execute(array(
									':nalog_id' => $nalog_id,
									':kandidat_id' => $kandidat_id,
									':datum' => $date,
									':datum_stvarni' => $date,
									':kf_nalog_rata_id' => $rata_id,
									':iznos' => $iznos,
									':kf_status' => $kf_status
								));
							}

						break;

						case "pocetak rada":

							if(!checkIfKandidatRataPostoji(3, $kandidat_id, $rata['nr_id'])){
								$rata_id = $rata['nr_id'];

								$sql = "INSERT INTO 
											idk_kandidat_financije (nalog_id, kandidat_id, kf_datum, kf_datum_stvarni, kf_nalog_rata_id, kf_type, kf_iznos, kf_placeno, kf_status)
										VALUES 
											(:nalog_id, :kandidat_id, :datum, :datum_stvarni, :kf_nalog_rata_id, 3, :iznos, 0, :kf_status)";

								$query = $db->prepare($sql);
								$query->execute(array(
									':nalog_id' => $nalog_id,
									':kandidat_id' => $kandidat_id,
									':datum' => $work_start_date,
									':datum_stvarni' => $work_start_date,
									':kf_nalog_rata_id' => $rata_id,
									':iznos' => $iznos,
									':kf_status' => $kf_status
								));
							}

						break;

						case "mjeseci nakon":

							if(!checkIfKandidatRataPostoji(4, $kandidat_id, $rata['nr_id'])){
								$broj_mjeseci = $rata['nr_mjeseci_nakon'];

								$date = date('Y-m-d', strtotime($work_start_date. ' + '.$broj_mjeseci.' months'));
								$rata_id = $rata['nr_id'];

								$sql = "INSERT INTO 
											idk_kandidat_financije (nalog_id, kandidat_id, kf_datum, kf_datum_stvarni, kf_nalog_rata_id, kf_type, kf_iznos, kf_placeno, kf_status)
										VALUES 
											(:nalog_id, :kandidat_id, :datum, :datum_stvarni, :kf_nalog_rata_id, 4, :iznos, 0, :kf_status)";

								$query = $db->prepare($sql);
								$query->execute(array(
									':nalog_id' => $nalog_id,
									':kandidat_id' => $kandidat_id,
									':datum' => $date,
									':datum_stvarni' => $date,
									':kf_nalog_rata_id' => $rata_id,
									':iznos' => $iznos,
									':kf_status' => $kf_status
								));
							}

						break;
					}
				}
			}
		break;
	}
	//INSERT RATE ZA NOSTRIFIKACIJU JER ONA NE OVISI O NACINU PLACANJA PROVIZIJE
	insertNostrifikacijaRata($kandidat_id, $nalog_id);
}
/////////////////////////////////////////////////////
//FUNKCIJE VEZANE ZA KREIRANJE RATA KANDIDATA END////
/////////////////////////////////////////////////////

function getCompanyNameByNalogId($nalog_id){
	Global $db;

	$query_get_company_name = $db -> prepare('
		SELECT company_name
		FROM idk_companies
		INNER JOIN idk_nalozi ON idk_companies.company_id = idk_nalozi.kompanija_id
		WHERE idk_nalozi.nalog_id = :nalog_id
	');

	$query_get_company_name -> execute(array(':nalog_id' => $nalog_id));
	$row_get_company_name = $query_get_company_name -> fetch();
	$company_name = $row_get_company_name['company_name'];
	return $company_name;
}

function getJSPartnerHasNalogArrayR($partnerId, $companyId){
	Global $db; 
	$partnerId = intval($partnerId); 
	$companyId = intval($companyId);
	$result = array(
		"status" => 0,
		"count" => array(), 
		"nalogIDs" => array(), 
		"nalogName" => array()
	);

	if ($partnerId != 0 AND $companyId != 0){
		$query = $db->prepare("
			SELECT 
				nalog_id,
				nalog_naziv
			FROM 
				idk_nalozi 
			WHERE 
				kompanija_id = :kompanija_id
				AND 
				nalog_js_partner_id = :nalog_js_partner_id
				AND 
				nalog_status != 12
		");
		$query->execute(array(
			':kompanija_id' => $companyId, 
			':nalog_js_partner_id' => $partnerId
		));
		if ($query->rowCount() != 0){
			$cnt = 0;
			while ($row = $query->fetch()){
				$nalog_id = intval($row["nalog_id"]);
				$nalog_naziv = $row["nalog_naziv"]; 
				array_push($result["count"], $cnt);
				array_push($result["nalogIDs"], $nalog_id);
				array_push($result["nalogName"], $nalog_naziv);
				$cnt++;
			}
			$result["status"] = 1;
		}else{
			$result["status"] = 0;
		}
	}else{
		$result["status"] = 102;
	}

	return $result;
}

function getJSPartnerForNalogArrayR($nalogId) {
	Global $db; 
	$nalogId = intval($nalogId); 
	$partnerId = 0;
	$partnerName = "";
	$result = array(
		"status" => 0, 
		"partner_id" => 0, 
		"partner_name" => ""
	);
	if ($nalogId != 0){
		$queryCheck = $db->prepare("
			SELECT 
				nalog_js_partner_id
			FROM 
				idk_nalozi 
			WHERE 
				nalog_id = :nalog_id 
		");
		$queryCheck->execute(array(
			':nalog_id' => $nalogId
		));
		if ($queryCheck->rowCount() == 1) {
			$rowCheck = $queryCheck->fetch();
			$partnerId = intval($rowCheck["nalog_js_partner_id"]); 
			if ($partnerId != 0) {
				$queryName = $db->prepare("
					SELECT 
						jp_imeprezime 
					FROM 
						idk_jobstep_partners 
					WHERE 
						jp_id = :jp_id
				");
				$queryName->execute(array(
					':jp_id' => $partnerId
				));
				$rowName = $queryName->fetch();
				$partnerName = $rowName["jp_imeprezime"];
				$result["status"] = 1;
				$result["partner_id"] = $partnerId;
				$result["partner_name"] = $partnerName;
			}
		}
	}

	return $result;

}

function getCompanyHasJSPartnerArrayR($companyId) {
	Global $db;
	/*
		Response status: 
			102 - neispravno proslijeđen paramtri funkcije
			101 - nije pronađena kompanija sa tim id-em
			1 - kompanija ima partner id
			0 - kompanija nema partner id
	*/
	$companyId = intval($companyId);
	$partnerId = 0;
	$partnerName = "";
	$result = array(
		"status" => 0,
		"parnter_id" => 0, 
		"partner_name" => "undefined"
	);
	if ($companyId != 0){
		$queryCheck = $db->prepare("
			SELECT 
				js_partner_id
			FROM 
				idk_companies 
			WHERE 
				company_id = :company_id 
		");
		$queryCheck->execute(array(
			':company_id' => $companyId
		));
		if ($queryCheck->rowCount() == 1) {
			$rowCheck = $queryCheck->fetch();
			$partnerId = intval($rowCheck["js_partner_id"]); 
			if ($partnerId != 0) {
				$queryName = $db->prepare("
					SELECT 
						jp_imeprezime 
					FROM 
						idk_jobstep_partners 
					WHERE 
						jp_id = :jp_id
				");
				$queryName->execute(array(
					':jp_id' => $partnerId
				));
				$rowName = $queryName->fetch();
				$partnerName = $rowName["jp_imeprezime"];
				$result["status"] = 1;
				$result["parnter_id"] = $partnerId;
				$result["partner_name"] = $partnerName;
			} else {
				$result["status"] = 0;
			}
		} else {
			$result["status"] = 101;
		}
	} else {
		$result["status"] = 102;
	}

	return $result;
}

function getCompanyIdByNalogId($nalog_id){
	Global $db;

	$query_get_company_id = $db -> prepare('
		SELECT company_id
		FROM idk_companies
		INNER JOIN idk_nalozi ON idk_companies.company_id = idk_nalozi.kompanija_id
		WHERE idk_nalozi.nalog_id = :nalog_id
	');

	$query_get_company_id -> execute(array(':nalog_id' => $nalog_id));
	$row_get_company_id = $query_get_company_id -> fetch();
	$company_id = $row_get_company_id['company_id'];
	return $company_id;
}

function getCompanyAccessR($nalogId) {
	Global $db; 
	$companyIdsForAccess = array(1204); 
	$nalogId = intval($nalogId); 
	/*
		companyIdsForAccess - je niz vrijednosti u koje se upisuju IDs kompanija koje imaju pristup za pitanja po Enpal zahtjevima
		Ako se nekad kasnije pojavi da nekoj kompaniji treba odobriti pristup kao i enpalu - samo upisati ID i voditi racuna oko setup-a pitanja
	*/ 
	if ($nalogId != 0) {
		$companyId = 0;
		$cntQuery = 0;
		$query = $db->prepare("
			SELECT 
				kompanija_id
			FROM 
				idk_nalozi 
			WHERE
				nalog_id  = :nalog_id 
		");
		$query->execute(array(
			':nalog_id' => $nalogId
		));
		$cntQuery = $query->rowCount();
		if($cntQuery != 0){
			$row = $query->fetch();
			$companyId = intval($row["kompanija_id"]);
			if (in_array($companyId, $companyIdsForAccess)) {
				return 1;
			} else {
				return 0;
			}
		} else {
			return 0;
		}
	} else {
		return 0;
	}
}

function copyQuestionWithNewTextValueArrayR($order_id, $pqu_id_copy, $new_text) {
	Global $db; 
	Global $logged_employee_id; 

	$result = array(
		'status' => 0, 
		'message' => '',
		'new_id' => 0
	);

	$get_old_questions = $db->prepare("
		SELECT 
			* 
		FROM 
			idk_pp_questions 
		WHERE 
			pqu_id = :pqu_id
	"); 
	$get_old_questions->execute(array(
		':pqu_id' => $pqu_id_copy
	));
	if ($get_old_questions->rowCount() == 1) {
		$rows_old_questions = $get_old_questions->fetchAll(PDO::FETCH_ASSOC);
		$row_old_question = $rows_old_questions[0];

		$insert_new_question = $db->prepare("
			INSERT INTO 
				idk_pp_questions 
				(
					pqu_question, 
					pqu_nalog_id, 
					pqu_user_id, 
					pqu_category_id, 
					pqu_has_text, 
					pqu_has_rating, 
					pqu_has_dropdown
				)
				SELECT 
					:new_text, 
                    pqu_nalog_id, 
                    :logged_employee_id, 
					pqu_category_id, 
					pqu_has_text, 
					pqu_has_rating, 
					pqu_has_dropdown
				FROM 
					idk_pp_questions
				WHERE 
					pqu_id = :pqu_id
		");
		$insert_new_question->execute(array(
			':new_text' => $new_text,
            ':logged_employee_id' => $logged_employee_id,
            ':pqu_id' => $pqu_id_copy
		)); 
		$new_id = $db->lastInsertId(); 
		if ($new_id) {
			if ($row_old_question['pqu_has_dropdown'] == 1) {

				$get_old_options = $db->prepare("
					SELECT 
						* 
					FROM 
						idk_pp_question_options 
					WHERE 
						pqo_question_id = :pqo_question_id
				"); 
				$get_old_options->execute(array(
					':pqo_question_id' => $pqu_id_copy
				));
				$count_old_options = $get_old_options->rowCount();

				$insert_new_options = $db->prepare("
					INSERT INTO idk_pp_question_options 
						( 
							pqo_question_id, 
							pqo_value, 
							pqo_value_text, 
							pqo_value_text_de, 
							pqo_value_subtext, 
							pqo_value_subtext_de
						)
					SELECT 
						:new_id, 
						pqo_value, 
						pqo_value_text, 
						pqo_value_text_de, 
						pqo_value_subtext, 
						pqo_value_subtext_de
					FROM 
						idk_pp_question_options
					WHERE 
						pqo_question_id = :pqo_question_id
				"); 
				$insert_new_options->execute(array(
					':new_id' => $new_id,
					':pqo_question_id' => $pqu_id_copy
				));
				$count_new_options = $insert_new_options->rowCount();

				if ($count_new_options == $count_old_options) {
					$result['status'] = 1;
					$result['message'] = "Copy Question With New Text Value: Uspješno unešeno novo pitanje zajedno sa pripadajućim opcijama!";
					$result['new_id'] = $new_id;
				} else {
					$result['status'] = 102; 
					$result['message'] = "Copy Question With New Text Value: Desio se problem prilikom unosa opcija za novo pitanje! Kontaktirajte administratora sistema!";
					$result['new_id'] = $new_id;
				}

			} else {
				$result['status'] = 1;
				$result['message'] = "Copy Question With New Text Value: Uspješno unešeno novo pitanje bez opcija!";
				$result['new_id'] = $new_id;
			}

		} else {
			$result['status'] = 101; 
			$result['message'] = "Copy Question With New Text Value: Desio se problem prilikom unosa novog pitanja! Kontaktirajte administratora sistema!";  
		}

		unset($rows_old_questions); 

	} else {
		$result['status'] = 100; 
		$result['message'] = "Copy Question With New Text Value: Desio se problem prilikom pretrage podataka o editovanom pitanju! Kontaktirajte administratora sistema!"; 
	}

	return $result; 
}

function getMonthsAfterByRateId($kf_nalog_rata_id){
	Global $db;

	$query = $db -> prepare('
		SELECT nr_mjeseci_nakon
		FROM idk_nalozi_rate
		WHERE nr_id = :nr_id  
	');

	$query -> execute(array(
		':nr_id' => $kf_nalog_rata_id
	));

	$row = $query -> fetch();
	$nr_mjeseci_nakon = $row['nr_mjeseci_nakon'];
	return $nr_mjeseci_nakon;
}

function getFirmaFakturisanja($nalog_id){
	Global $db;

	$query = $db -> prepare('
		SELECT nalog_firma_fakturisanja
		FROM idk_nalozi
		WHERE nalog_id = :nalog_id 
	');

	$query -> execute(array(
		':nalog_id' => $nalog_id
	));

	$row = $query -> fetch();
	$nalog_firma_fakturisanja = $row['nalog_firma_fakturisanja'];
	return $nalog_firma_fakturisanja;
}

function isMaxRata($kf_id, $kandidat_id){
	Global $db;

	$query = $db -> prepare('
		SELECT kf_id
		FROM idk_kandidat_financije
		WHERE kf_id > :kf_id AND kandidat_id = :kandidat_id AND kf_status = 1 AND kf_type IN (1,2,3,4)
	');

	$query -> execute(array(
		':kf_id' => $kf_id,
		':kandidat_id' => $kandidat_id
	));

    return ($query->rowCount() === 0);
}



function candidateHasProjection($candidate_id){
	Global $db;
	
	$query_get_candidate = $db -> prepare('
		SELECT kandidat_id
		FROM idk_kandidat_projekcije
		WHERE kandidat_id = :candidate_id
	');

	$query_get_candidate -> execute(array(':candidate_id' => $candidate_id));

	$row_get_candidate = $query_get_candidate -> fetch();

	if(is_null($row_get_candidate['kandidat_id'])){
		return 0;
	}
	return 1;
}


function getCandidateStatusPrijaveByCandidateId($candidate_id){
	Global $db;

	$query_get_status = $db -> prepare('
		SELECT kandidat_status_prijave
		FROM idk_kandidati
		WHERE kandidat_id = :candidate_id
	');
	$query_get_status -> execute(array(':candidate_id' => $candidate_id));

	$row_get_status = $query_get_status -> fetch();

	return $row_get_status['kandidat_status_prijave'];
}

function deleteCandidatesProjection($candidate_id){
	Global $db;

	$query_delete_candidate_projection = $db -> prepare('
		DELETE FROM idk_kandidat_projekcije
		WHERE kandidat_id = :candidate_id
	');

	$query_delete_candidate_projection -> execute(array(':candidate_id' => $candidate_id));
}

function getCandidatesQuickAccessProjection($candidate_id){
	Global $db;

	$query_get_projection = $db -> prepare('
		SELECT proracunati_pocetak_rada
		FROM idk_kandidat_projekcije
		WHERE kandidat_id = :candidate_id
	');

	$query_get_projection -> execute(array(':candidate_id' => $candidate_id));

	$row_get_projection = $query_get_projection -> fetch();

	return strtotime($row_get_projection['proracunati_pocetak_rada']);
}

function calculateCandidatesProjection($candidate_id){
	include_once($_SERVER["DOCUMENT_ROOT"] . '/jobstep_pp/includes/classes/candidatesProjection.php');
	$durationPerStatus = new durationPerStatus();
	$candidates_projection = new candidatesProjection($durationPerStatus, $candidate_id);

	$projection_object       = $candidates_projection -> getCandidateProjectionRows();
	return $projection_object[0]['candidate_assessment'];
}

function updateCandidatesQuickAccessProjection($candidate_id, $projection_date){
	Global $db;

	$query_update_projection =  $db -> prepare('
		UPDATE idk_kandidat_projekcije
		SET proracunati_pocetak_rada =  :projection_date
		WHERE kandidat_id = :candidate_id
	');

	$query_update_projection -> execute(array(
		':candidate_id' => $candidate_id,
		':projection_date' => $projection_date
	));
}

function insertCandidateQuickAccessProjection($candidate_id, $projection_date){
	Global $db;
	$query_insert_projection = $db -> prepare('
		INSERT INTO idk_kandidat_projekcije (kandidat_id, proracunati_pocetak_rada)
		VALUES (:candidate_id, :projection_date)
	');
	$query_insert_projection -> execute(array(
		':candidate_id' => $candidate_id,
		':projection_date' => $projection_date
	));
}

function updateCandidateInstallments($candidate_id, $shift_dates){
	Global $db;

	$query_update_instalments = $db -> prepare('
		UPDATE idk_kandidat_financije
		SET kf_datum = DATE_ADD(kf_datum, INTERVAL :shift_dates DAY), kf_datum_stvarni = DATE_ADD(kf_datum_stvarni, INTERVAL :shift_dates DAY)
		WHERE kandidat_id = :candidate_id
		AND kf_placeno = 0
		AND kf_type != 0

	');

	$query_update_instalments -> execute(array(
		':candidate_id' => $candidate_id,
		':shift_dates' => $shift_dates
	));
}

function updateCandidateProjectionAndInstallment($candidate_id){

	$shift_dates = 0;

	$candidate_status = getCandidateStatusPrijaveByCandidateId($candidate_id);
	$candidate_has_projection = candidateHasProjection($candidate_id);

	$negative_statuses = array(1,2,3,5,6);
	// $positive_statuses = array(4,7,8,9,10,12,15,18,21,24,27);

	if(in_array($candidate_status, $negative_statuses)){
		if($candidate_has_projection){
			deleteCandidatesProjection($candidate_id);
			//KANDIDAT BIO NA MINIMALNO STATUSU ČEKA UGOVOR, POTREBAN KOD ZA ZAMJENU
		}
		return;
	}
	else{
		$candidate_calculated_projection  = calculateCandidatesProjection($candidate_id);
		$candidate_potential_work_start = getPotentialWorkStart($candidate_id);
		
		if($candidate_potential_work_start != null){
			$new_date_for_projection = $candidate_potential_work_start;
		}else{
			$new_date_for_projection = $candidate_calculated_projection;
		}

		if($candidate_has_projection){

			$candidate_quick_access_projection = getCandidatesQuickAccessProjection($candidate_id);				

			if($candidate_quick_access_projection != $new_date_for_projection){
				$shift_dates = floor(($new_date_for_projection - $candidate_quick_access_projection)/86400);
				updateCandidatesQuickAccessProjection($candidate_id, date('Y-m-d', $new_date_for_projection));
			}
			else return;
		}
		else{
			insertCandidateQuickAccessProjection($candidate_id, date('Y-m-d', $new_date_for_projection));
		}
	}

	if($shift_dates != 0){
		updateCandidateInstallments($candidate_id, $shift_dates);
	}
}

function getPotentialWorkStart($kandidat_id){
	Global $db;
	
	$sql = "SELECT 
				 CASE 
					WHEN kandidat_potencijalni_pocetak_rada IS NOT NULL 
					THEN 
						CASE
							WHEN kandidat_potencijalni_pocetak_rada LIKE '%to%'
							THEN STR_TO_DATE(CONCAT('1.', SUBSTRING_INDEX(kandidat_potencijalni_pocetak_rada, 'to ', -1)), '%d.%m.%Y')
							ELSE STR_TO_DATE(CONCAT('1.',kandidat_potencijalni_pocetak_rada), '%d.%m.%Y')
						END
					ELSE null
				END as potencijalni_pocetak_rada_formatted
			FROM idk_kandidati 
			WHERE kandidat_id = $kandidat_id";
	$stmt = $db->prepare($sql);
	$stmt->execute();
	$row = $stmt->fetch();

	return strtotime($row["potencijalni_pocetak_rada_formatted"]);

}

function getKandidatVisaStartDate($kandidat_id){
	Global $db;

	$sql = "SELECT kandidat_viza_vrijedi_od FROM idk_kandidati WHERE kandidat_id = $kandidat_id";
	$stmt = $db->prepare($sql);
	$stmt->execute();
	$row = $stmt->fetch();

	return $row["kandidat_viza_vrijedi_od"];
}

function getKandidatRata($kandidat_id, $type){
	Global $db;

	$sql = "SELECT kf_id FROM idk_kandidat_financije WHERE kandidat_id = :kandidat_id AND kf_type = :kf_type";
	$stmt = $db->prepare($sql);
	$stmt->execute(array(
		':kandidat_id' => $kandidat_id,
		':kf_type' => $type
	));
	$row = $stmt->fetch();

	return $row["kf_id"];
}

function getPrvaRataKandidata($kandidat_id, $nalog_id){
	Global $db;

	$sql = "SELECT kf_id FROM idk_kandidat_financije WHERE kandidat_id = :kandidat_id AND kf_type != 0 AND nalog_id = :nalog_id ORDER BY kf_type ASC LIMIT 1";
	$stmt = $db->prepare($sql);
	$stmt->execute(array(
		':kandidat_id' => $kandidat_id,
		':nalog_id' => $nalog_id
	));
	$row = $stmt->fetch();

	return $row["kf_id"];
}

function updateKandidatRata($kandidat_id, $nalog_id, $rata_id, $date){
	Global $db;
	
	$sql = "UPDATE idk_kandidat_financije SET kf_placeno = 1, kf_datum = :kf_datum, kf_datum_stvarni = :kf_datum WHERE kf_id = :rata_id AND kandidat_id = :kandidat_id";
	$stmt = $db->prepare($sql);
	$stmt->execute(array(
		':rata_id' => $rata_id,
		':kandidat_id' => $kandidat_id,
		':kf_datum' => $date
	));
}

function checkEUKandidat($kandidat_id){
	Global $db;

	$sql = "SELECT kandidat_drzavljanstvo_vrsta FROM idk_kandidati WHERE kandidat_id = $kandidat_id";
	$stmt = $db->prepare($sql);
	$stmt->execute();
	$row = $stmt->fetch();

	if($row['kandidat_drzavljanstvo_vrsta'] == "EU državljanin"){
		return true;
	} else {
		return false;
	}
}

function getKandidatDogovoreniPocetakRada($kandidat_id){
	Global $db;

	$sql = "SELECT kandidat_dogovoreni_pocetak_rada FROM idk_kandidati WHERE kandidat_id = $kandidat_id";
	$stmt = $db->prepare($sql);
	$stmt->execute();
	$row = $stmt->fetch();

	return $row["kandidat_dogovoreni_pocetak_rada"];
}

function updateRateMjeseciNakon($kandidat_id, $date){
	Global $db;

	$sql = "SELECT 
				kf_id,
				nr_mjeseci_nakon
			FROM 
				idk_kandidat_financije 
			JOIN 
				idk_nalozi_rate 
			ON 
				idk_kandidat_financije.kf_nalog_rata_id = idk_nalozi_rate.nr_id 
			WHERE 
				kandidat_id = :kandidat_id 
			AND 
				kf_type = 4";

	$stmt = $db->prepare($sql);
	$stmt->execute(array(
		':kandidat_id' => $kandidat_id
	));

	while($row = $stmt->fetch()){
		$kandidat_rata_id = $row["kf_id"];
		$broj_mjeseci = $row["nr_mjeseci_nakon"];
		$kf_date = date('Y-m-d', strtotime($date. ' + '.$broj_mjeseci.' months'));

		$sql = "UPDATE idk_kandidat_financije SET kf_datum = :kf_datum, kf_datum_stvarni = :kf_datum, kf_placeno = 1 WHERE kf_id = :kf_id";
		$stmt2 = $db->prepare($sql);
		$stmt2->execute(array(
			':kf_id' => $kandidat_rata_id,
			':kf_datum' => $kf_date
		));
	}
}

function updateProjekcijeKandidat($type, $candidateId) {
	Global $db; 
	/*
		Request 
			$type -> 1 - DIPL, 2 - Posredovanje
			$candidateId -> ( $type == 1) ? $candidateDiplId : $candidateJobId
	*/
	$type = intval($type); 
	$candidateId = intval($candidateId); 

	if ( $type != 0 AND $candidateId != 0) {

		switch ( $type ) {

			case 1:

				$candidateDiplId = $candidateId;

				/*
					NOSTRIFIKACIJA CASE START
					*/

						$queryCheckCandidateJob = $db->prepare("
							SELECT 
								kan.kandidat_id,
								kan.kandidat_status_prijave, 
								nd_kan.status_nd_kandidata, 
								nd_kan.pstatus_nd_kandidata
							FROM 
								idk_kandidati kan
							INNER JOIN 
								idk_nd_kandidata nd_kan
							ON 
								nd_kan.id_broj_nd_kandidata = kan.kandidat_dipl_id
							WHERE 
								nd_kan.id_broj_nd_kandidata = :candidateDiplId
						");

						$queryCheckCandidateJob->execute(array(
							':candidateDiplId' => $candidateDiplId
						));

						if ( $queryCheckCandidateJob->rowCount() == 1 ) {

							$rowCheckCandidateJob 			= $queryCheckCandidateJob->fetch();

							$candidateJobId					= $rowCheckCandidateJob["kandidat_id"]; 
							$candidateJobStatusPrijave 		= $rowCheckCandidateJob["kandidat_status_prijave"];
							$candidateDiplStatus			= $rowCheckCandidateJob["status_nd_kandidata"];
							$candidateDiplPodstatus			= $rowCheckCandidateJob["pstatus_nd_kandidata"];

							/*
								array(2,3,4,5,6) -> oznacava statuse za koje je bitna projekcija i koji uticu na projekciju
								Sa druge strane - funkcija se poziva na dosta mjesta
								Izmedju ostaloga u funkciji "promjenaStatusaDIPLKandidat"
								Ta funkcija se poziva u npr: cron_lead_NL, cron_lead_NZ i slicno
								Nepotrebno je za takve kandidate koji su na tom statusu pokretati racun projekcije
								Također to bi uticalo na performanse tih kronova
								Jer bi unutar while petlje - za svakog kandidata posebno se vrtila projekcija

								Što više - moguć je i ispis Errora
								Odnosno prekiranje skripte

								Zbog toga je uslov ispod jako važan i ovaj komentar neka stoji kao objasnjenje
							*/

							if ( in_array( $candidateDiplStatus, array(2,3,4,5,6) ) ) {
								
								/*
									PROJEKCIJA START
									*/

									updateCandidateProjectionAndInstallment($candidateJobId);

									/*
									PROJEKCIJA END
								*/

							}

						}
					
					/*
					NOSTRIFIKACIJA CASE END
				*/
				
			break;

			case 2:

				$candidateJobId = $candidateId;

				/*
					POSAO CASE START
					*/

						$queryCheckCandidateStatus = $db->prepare("
							SELECT 
								kan.kandidat_status_prijave
							FROM 
								idk_kandidati kan
							WHERE 
								kan.kandidat_id = :candidateJobId
						");

						$queryCheckCandidateStatus->execute(array(
							':candidateJobId' => $candidateJobId
						));

						if ( $queryCheckCandidateStatus->rowCount() == 1 ) {

							$rowCheckCandidateStatus 		= $queryCheckCandidateStatus->fetch();
							$candidateJobStatusPrijave 		= $rowCheckCandidateStatus["kandidat_status_prijave"];

							/*
								PROJEKCIJA START
								*/

								updateCandidateProjectionAndInstallment($candidateJobId);

								/*
								PROJEKCIJA END
							*/

						}

					/*
					POSAO CASE END
				*/
			
			break;

			default: 

				/*
					Do nothing
				*/

			break;

		}

	}
}

function kandidatOdustao($stari_kandidat, $novi_kandidat){
	Global $db;

	//postavi zamjenu za rate koje su placene
	$sql = "UPDATE idk_kandidat_financije SET kf_zamjena_id = $novi_kandidat, kf_status = 2 WHERE kandidat_id = $stari_kandidat AND kf_placeno IN (2, 3)";
	$stmt = $db->prepare($sql);
	$stmt->execute();
	
	//arhiviraj sve rate koje su NDF/treba fakturisati/fakturisano i nemaju zamjenu
	$sql = "UPDATE idk_kandidat_financije SET kf_status = 0 WHERE kandidat_id = $stari_kandidat AND kf_status = 1 AND kf_placeno IN (0, 1)";
	$stmt2 = $db->prepare($sql);
	$stmt2->execute();
	
	//uzmi tip rate koje imaju zamjenu
	$sql = "SELECT kf_type FROM idk_kandidat_financije WHERE kandidat_id = $stari_kandidat AND kf_zamjena_id = $novi_kandidat";
	$stmt3 = $db->prepare($sql);
	$stmt3->execute();

	while($row = $stmt3->fetch()){
		$tip_rate = $row["kf_type"];

		//arhiviraj rate iste vrste koje je novi kandidat naslijedio
		$sql = "UPDATE idk_kandidat_financije SET kf_status = 0 WHERE kandidat_id = $novi_kandidat AND kf_status = 3 AND kf_type = $tip_rate";
		$stmt4 = $db->prepare($sql);
		$stmt4->execute();
	}

	//postavi status normalna rata za sve rate koje su na statusu visak za novog kandidata
	$sql = "UPDATE idk_kandidat_financije SET kf_status = 1 WHERE kandidat_id = $novi_kandidat AND kf_status = 3";
	$stmt5 = $db->prepare($sql);
	$stmt5->execute();
}

function getKandidatVisak($nalog_id){
	Global $db;

	$sql = "SELECT
				kandidat_id, log_status_prijave.lsp_datetime
			FROM
				idk_kandidat_financije
			JOIN 
			(
				SELECT
				lsp.lsp_kandidat_id, lsp.lsp_datetime
				FROM
					idk_log_statusi_prijave lsp
				INNER JOIN 
					idk_projects pr 
				ON 
					pr.project_id = lsp.lsp_projekt_id
				WHERE 
					pr.project_nalogid = $nalog_id 
				AND
					lsp.lsp_status_prijave_id = 9
			) as log_status_prijave
			ON idk_kandidat_financije.kandidat_id = log_status_prijave.lsp_kandidat_id
			WHERE
				kf_status = 3
			AND
				nalog_id = $nalog_id
			ORDER BY log_status_prijave.lsp_datetime
	";
	$stmt = $db->prepare($sql);
	$stmt->execute();
	$row = $stmt->fetch();

	return $row["kandidat_id"];
}

function getOdustaneRateBezZamjene($nalog_id){
	Global $db;

	$sql = "SELECT kf_id 
			FROM idk_kandidat_financije 
			WHERE nalog_id = $nalog_id AND kf_status = 2 AND kf_zamjena_id IS NULL
				AND kandidat_id = ( 
					SELECT kandidat_id 
					FROM idk_kandidat_financije 
					WHERE nalog_id = $nalog_id AND kf_status = 2 AND kf_zamjena_id IS NULL 
					ORDER BY kf_datum_fakturisanja LIMIT 1 )
	";
	$stmt = $db->prepare($sql);
	$stmt->execute();

	$rate = array();
	while($row = $stmt->fetch()){
		$rate[] = $row["kf_id"];
	}

	return $rate;
}

function checkIfKandidatRataPostoji($kf_type, $kandidat_id, $nr_id){
	Global $db;

	$sql = "SELECT kf_id FROM idk_kandidat_financije WHERE (kandidat_id = $kandidat_id OR kf_zamjena_id = $kandidat_id) AND kf_type = $kf_type AND kf_nalog_rata_id = $nr_id";
	$stmt = $db->prepare($sql);
	$stmt->execute();

	if($stmt->rowCount() > 0){
		return true;
	} else {
		return false;
	}
}

function checkIfKandidatRataNostrifikacijaPostoji($kandidat_id, $nalog_id){
	Global $db;

	$sql = "SELECT kf_id FROM idk_kandidat_financije WHERE kandidat_id = $kandidat_id AND kf_type = 0 AND nalog_id = $nalog_id AND kf_status != 0";
	$stmt = $db->prepare($sql);
	$stmt->execute();

	if($stmt->rowCount() > 0){
		return true;
	} else {
		return false;
	}
}

function checkNalogPaymentType($nalog_id){
	Global $db;

	$query = $db -> prepare('
		SELECT nalog_financije
		FROM idk_nalozi
		WHERE nalog_id  = :nalog_id 
	');

	$query -> execute(array(
		':nalog_id' => $nalog_id 
	));

	$row = $query->fetch();

    return $row["nalog_financije"];
}

function isTokenCorrect($token){
	Global $db;

	$query = $db -> prepare('
		SELECT employee_id
		FROM idk_employees
		WHERE employee_reset_token  = :employee_reset_token AND employee_reset_token_doe >= DATE_SUB(NOW(), INTERVAL 15 MINUTE) 
	');

	$query -> execute(array(
		':employee_reset_token' => $token 
	));

	$row = $query->rowCount();
	
	if($row > 0){
		return true;
	}else{
		return false;
	}
}

function getAppointmentGroup($pap_id){
	Global $db;

	$query = $db -> prepare('
		SELECT pap_group_id
		FROM idk_pp_appointments
		WHERE pap_id  = :pap_id 
	');

	$query -> execute(array(
		':pap_id' => $pap_id 
	));

	$row = $query->fetch();

	return $row["pap_group_id"];
}

// This checks if there is an appointment with the same group connected to candidate
function checkAppointmentGroupExistance($pap_group_id, $pca_kandidat_id){
	Global $db;

	$query = $db -> prepare('
		SELECT pca_id
		FROM idk_pp_cand_appts
		INNER JOIN idk_pp_appointments ON idk_pp_cand_appts.pca_appointment_id = idk_pp_appointments.pap_id
		WHERE pap_group_id  = :pap_group_id AND pca_kandidat_id = :pca_kandidat_id
	');

	$query -> execute(array(
		':pap_group_id' => $pap_group_id, 
		':pca_kandidat_id' => $pca_kandidat_id 
	));

	$count = $query->rowCount();

    return $count;
}

function getActiveAppointmentForCandidate($kandidat_id){
	Global $db;

	$query = $db->prepare("SELECT pca_appointment_id FROM idk_pp_cand_appts WHERE pca_kandidat_id = $kandidat_id AND pca_status = 1 ORDER BY pca_id DESC LIMIT 1");
	$query->execute();
	$row = $query->fetch();

	return $row["pca_appointment_id"];
	
}


function daysPassedFromDateToToday($dateString)
{
	if($dateString != NULL){
		$dateTimestamp = strtotime($dateString);

		$todayTimestamp = time();
	
		$timeDifference = $todayTimestamp - $dateTimestamp;
	
		$daysPassed = floor($timeDifference / (60 * 60 * 24));
	
		return $daysPassed;
	}else{
		return 0;
	}
    
}

function getTFAgentsForNalog($nalog_id){
	Global $db;

	$query = $db -> prepare('
		SELECT idk_tf_agent_nalog.*, CONCAT(employee_firstname," ",employee_lastname) as employee_fullname, idk_tf_vrste.name as tf_vrsta_name
		FROM idk_tf_agent_nalog
		INNER JOIN idk_employees
		ON idk_tf_agent_nalog.tfan_agent = idk_employees.employee_id
		INNER JOIN idk_tf_vrste ON idk_tf_agent_nalog.tfan_vrsta_id = idk_tf_vrste.id
		WHERE tfan_nalog = :tfan_nalog
		ORDER BY idk_tf_agent_nalog.tfan_vrsta_id
	');

	$query -> execute(array(
		':tfan_nalog' => $nalog_id
	));

	$rows = $query->fetchAll(PDO::FETCH_ASSOC);

    return $rows;
}

function getNalogPPProfilSettings($nalog_id){
	Global $db;

	$query = $db -> prepare('SELECT
								pnp_id
							FROM
								idk_pp_nalog_profil
							JOIN idk_pp_informacije_profil ON idk_pp_nalog_profil.pnp_info_id = idk_pp_informacije_profil.pip_id
							WHERE
								pnp_nalog_id = :pnp_nalog_id AND pip_status = 1 
							');

	$query -> execute(array(
		':pnp_nalog_id' => $nalog_id
	));

	$rows = $query->fetchAll(PDO::FETCH_ASSOC);

    return $rows;
}

function getUniqueAgentCountForNalog($nalog_id){
    Global $db;

    $query = $db->prepare('
        SELECT idk_tf_agent_nalog.tfan_agent
        FROM idk_tf_agent_nalog
        WHERE tfan_nalog = :tfan_nalog
    ');

    $query->execute(array(
        ':tfan_nalog' => $nalog_id
    ));

    $uniqueAgents = array();
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $uniqueAgents[$row['tfan_agent']] = true;
    }

    $uniqueAgentCount = count($uniqueAgents);
    return $uniqueAgentCount;
}

function getVrstaName($vrsta_id){
    Global $db;

    $query = $db->prepare('
        SELECT name
        FROM idk_tf_vrste
        WHERE id = :id
    ');

    $query->execute(array(
        ':id' => $vrsta_id
    ));

    $row = $query->fetch();

	return $row["name"];
}

function insert_reject_reason($nalog_id, $status_id, $novi_status_id, $kandidat_id, $rr_id, $razlog_opis, $stari_projekt, $izvor){
	Global $db;
	Global $logged_employee_id;

	// PROJEKT KANDIDAT ODUSTAO
	$query_get_project = $db->prepare("
		SELECT 
			project_id
		FROM 
			idk_projects 
		WHERE 
			project_nalogid = :nalog_id 
		AND 
			project_name LIKE '%- Odustao%'
	");
	$query_get_project->execute(array(
	":nalog_id" => $nalog_id
	));
	$result_project = $query_get_project->fetch();
	$novi_projekt = $result_project['project_id'];

	$query_get_partner_id = $db->prepare("
		SELECT 
			ppa_company_id 
		FROM 
			idk_pp_partners 
		JOIN 
			idk_kandidati 
		ON 
			idk_kandidati.kandidat_ppa_partner_id = idk_pp_partners.ppa_id 
		WHERE 
			kandidat_id = :kandidat_id
	");
	$query_get_partner_id->execute(array(
	":kandidat_id" => $kandidat_id
	));
	$result_partner_id = $query_get_partner_id->fetch();
	if($result_partner_id > 0){
		$partner_id = $result_partner_id['idk_pp_partners'];
	} else {
		$partner_id = null;
	}


	if($status_id == 3){
		$query_get_appt_id = $db->prepare("SELECT pca_appointment_id FROM idk_pp_cand_appts WHERE pca_kandidat_id = :kandidat_id");
		$query_get_appt_id->execute(array(
			":kandidat_id" => $kandidat_id
		));
		$result_appt_id = $query_get_appt_id->fetch();
		$appt_id = $result_appt_id['pca_appointment_id'];
	} else {
		$appt_id = null;
	}

	$query_insert_reason = $db->prepare("
		INSERT INTO 
			idk_kandidati_reject_reasons
			(
				krr_reason_id,
				krr_candidate_id,
				krr_nalog_id,
				krr_partner_id,
				krr_apointment_id,
				krr_description,
				krr_source,
				krr_done_by,
				krr_last_status
			)
		VALUES
			(
				:reason_id,
				:candidate_id,
				:nalog_id,
				:partner_id,
				:apointment_id,
				:description,
				:source,
				:done_by,
				:last_status
			)
	");
	$query_insert_reason->execute(array(
		":reason_id"		=> $rr_id,
		":candidate_id" 	=> $kandidat_id,
		":nalog_id" 		=> $nalog_id,
		":partner_id" 		=> $partner_id,
		":apointment_id" 	=> $appt_id,
		":description" 		=> $razlog_opis,
		":source" 			=> 1,
		":done_by" 			=> $logged_employee_id,
		":last_status" 		=> $status_id
	));

	$query_update_kandidat_sp = $db->prepare("
		UPDATE
			idk_kandidati
		SET
			kandidat_status_prijave = 1,
			kandidat_nalog_id = null
		WHERE
			kandidat_id = :kandidat_id
	");
	$query_update_kandidat_sp->execute(array(
		":kandidat_id" => $kandidat_id
	));

	$query_insert_into_project = $db->prepare("
		INSERT INTO
			idk_project_kandidati
			(
				pk_projectid,
				pk_kandidatid
			)
		VALUES
			(
				:project_id,
				:kandidat_id
			)
	");
	$query_insert_into_project->execute(array(
		":project_id" 	=> $novi_projekt,
		":kandidat_id" 	=> $kandidat_id
	));

	$query_get_all_projects = $db->prepare("SELECT 
			project_id 
		FROM 
			idk_projects 
		JOIN 
			idk_project_kandidati 
		ON 
			idk_projects.project_id = idk_project_kandidati.pk_projectid 
		WHERE 
			pk_kandidatid = :kandidat_id
		AND
			project_nalogid = :nalog_id
		AND
			project_id != :novi_projekt
	");
	$query_get_all_projects->execute(array(
		":kandidat_id" 	=> $kandidat_id,
		":nalog_id" 	=> $nalog_id,
		":novi_projekt" => $novi_projekt
	));
	while($result_all_projects = $query_get_all_projects->fetch()){
		$project_id = $result_all_projects['project_id'];
		$query_delete_from_project = $db->prepare("DELETE FROM idk_project_kandidati WHERE pk_projectid = :project_id AND pk_kandidatid = :kandidat_id");
		$query_delete_from_project->execute(array(
		":project_id" => $project_id,
		":kandidat_id" 	=> $kandidat_id
		));
	}
	// $zamjena_id = getKandidatVisak($nalog_id);

	// if($zamjena_id != NULL){
	// 	kandidatOdustao($kandidat_id, $zamjena_id);
	// } else {
		$sql = "UPDATE idk_kandidat_financije SET kf_status = 2 WHERE kandidat_id = :kandidat_id AND kf_placeno IN (2,3)";
		$query = $db->prepare($sql);
		$query->execute(array(
		":kandidat_id" => $kandidat_id
		));

		$sql = "UPDATE idk_kandidat_financije SET kf_status = 0 WHERE kandidat_id = :kandidat_id AND kf_placeno IN (0,1)";
		$query = $db->prepare($sql);
		$query->execute(array(
		":kandidat_id" => $kandidat_id
		));
	// }

	$log_desc = "Označeno da kandidat odustaje od posredovanja: " .$kandidat_id. ".";
	$log_type = "0";
	addToLogs($log_desc, $log_type); 

	//Izvršavanje aktivnih remindera
	updateReminderOnCandidateQuit($kandidat_id);

	//Arhiviranje svih prilkupljenih dokumenata
	archiveDocuments($kandidat_id, $nalog_id);

	addToLogsStatusPrijave($stari_projekt, $novi_projekt, $novi_status_id, $kandidat_id, $izvor);

	updateLastActiveTaskForCandidate($kandidat_id, 33); 
	
	/*
		Slanje mail-a Account Manager-u START
		*/
			$getInfoForEmail = $db->prepare("
				SELECT 
					n.employee_id AS employeeId, 
					CONCAT(e.employee_firstname, ' ', e.employee_lastname) AS employeeFullName,
					e.employee_email AS employeeEmail, 
					c.company_name AS companyName, 
					n.nalog_naziv AS orderName
				FROM 
					idk_nalozi n
				JOIN 
					idk_employees e
				ON 
					n.employee_id = e.employee_id 
				JOIN 
					idk_companies c
				ON 
					n.kompanija_id = c.company_id
				WHERE 
					n.employee_id is not null 
					AND 
					e.employee_status NOT LIKE '0'
					AND 
					n.nalog_id = :nalogId
			");
			$getInfoForEmail->execute(array(
				':nalogId' => $nalog_id
			));
			if ($getInfoForEmail->rowCount() == 1){
				$rowInfoForEmail = $getInfoForEmail->fetch();
				$employeeId = $rowInfoForEmail["employeeId"]; 
				$employeeFullName = $rowInfoForEmail["employeeFullName"]; 
				$employeeEmail = $rowInfoForEmail["employeeEmail"]; 
				$companyName = $rowInfoForEmail["companyName"]; 
				$orderName = $rowInfoForEmail["orderName"]; 

				$getInfoForCandidate = $db->prepare("
					SELECT 
						kan.kandidat_id AS candidateId, 
						CONCAT(kan.kandidat_ime, ' ', kan.kandidat_prezime) As candidateFullName
					FROM 
						idk_kandidati kan 
					WHERE 
						kan.kandidat_id = :kandidat_id 
				");
				$getInfoForCandidate->execute(array(
					':kandidat_id' => $kandidat_id
				));
				if ($getInfoForCandidate->rowCount() == 1){
					$rowInfoForCandidate = $getInfoForCandidate->fetch();
					$candidateId = $rowInfoForCandidate["candidateId"];
					$candidateFullName = $rowInfoForCandidate["candidateFullName"];

					$subjectEmail = "Kandidat odustao - ".$kandidat_id; 
					$emailBody = "
						<p>
							Poštovani/a ".$employeeFullName.",
						</p>
						<p>
							Obavještavamo Vas kao Project Manager-a naloga 
							<a href='" . getSiteUrlr() . "nalozi?page=open&id=".$nalog_id."'>
								<strong>".$orderName."</strong> 
							</a>
							od kompanije <strong>".$companyName."</strong> 
							da je kandidat 
							<a href='" . getSiteUrlr() . "kandidati?page=open&id=".$candidateId."'>
								<strong>".$candidateFullName."</strong> 
							</a>
							odustao u procesu odlaska.</p>
						<p>
							Vaš JobStep!
						</p>
					";
					$emailAltBody = "
						Poštovani/a ".$employeeFullName.",
						Obavještavamo Vas kao Project Manager-a naloga ".$orderName." (ID: ".$nalog_id.")
						od kompanije ".$companyName." da je kandidat ".$candidateFullName." (ID: ".$candidateId.") 
						odustao u procesu odlaska. Vaš JobStep!
					";
					sendEmail($employeeEmail, $employeeFullName, $subjectEmail, $emailBody, $emailAltBody);
					$log_desc = "Send Email - Odustanak kandidata - Prilikom označavanja da je kandidat ID = [".$kandidat_id."] odustao na nalogu ID = [".$nalog_id."], sistem je poslao Email korisniku ".$employeeFullName."."; 
					addToLogs($log_desc, 0);
				} else {
					$log_desc = "Send Email - Odustanak kandidata - Prilikom označavanja da je kandidat ID = [".$kandidat_id."] odustao na nalogu ID = [".$nalog_id."], sistem nije pronašao informacije o kandidatu i zbog toga nije poslan Email korisniku ".$employeeFullName."."; 
					addToLogs($log_desc, 0);
				}
			} else {
				$log_desc = "Send Email - Odustanak kandidata - Prilikom označavanja da je kandidat ID = [".$kandidat_id."] odustao na nalogu ID = [".$nalog_id."], sistem nije pronašao aktivnog Project Managera i zbog toga nije poslan Email."; 
				addToLogs($log_desc, 0);
			}
		/*
		Slanje mail-a Account Manager-u	END
	*/
	
}

function archiveDocuments($kandidat_id, $nalog_id){
	Global $db;
	Global $logged_employee_id;

	$get_documents = $db->prepare("SELECT doc_id FROM idk_pp_documents WHERE doc_candidate_id = $kandidat_id AND doc_nalog_id = $nalog_id AND doc_status != 27");
	$get_documents->execute();
	while($row_documents = $get_documents->fetch()){
		$doc_id = $row_documents['doc_id'];
		changeStatusAndInsertLog($doc_id, 27, "Wurde automatisch archiviert, nachdem der Kandidat sich zurückgezogen hat.", $logged_employee_id, NULL);
	}

	$update_ugovor = $db->prepare("UPDATE idk_kandidati_contracts SET kc_visibility_status = 0 WHERE kc_candidate_id = $kandidat_id AND kc_nalog_id = $nalog_id");
	$update_ugovor->execute();
	
	$log_desc = "Odustankom arhivirani dokumenti za posredovanje za kandidata " .$kandidat_id. ".";
	$log_type = "0";
	addToLogs($log_desc, $log_type); 
}

function getLastTFVrsta($kandidat_id){
	Global $db;

	$query = $db -> prepare('
		SELECT tf_vrsta_id
		FROM idk_task_force
		WHERE tf_candidate_id = :tf_candidate_id
		ORDER BY tf_id 
		DESC LIMIT 1 
	');

	$query -> execute(array(
		':tf_candidate_id' => $kandidat_id
	));

	$row = $query -> fetch();

	return $row['tf_vrsta_id'];
}

function getCandidateStatusPrijaveRedoslijed($candidate_id){
	Global $db;
	$candidate_status_prijave_redoslijed = 0;
	$query_get_status_prijave_redoslijed = $db -> prepare('
		SELECT redoslijed_statusa
		FROM idk_kandidati
		INNER JOIN idk_kandidat_status_prijave ON idk_kandidati.kandidat_status_prijave = idk_kandidat_status_prijave.status_id
		WHERE kandidat_id = :candidate_id
	');
	
	$query_get_status_prijave_redoslijed -> execute(array(':candidate_id' => $candidate_id));
	$row_get_status_prijave_redoslijed = $query_get_status_prijave_redoslijed -> fetch();
	$candidate_status_prijave_redoslijed = $row_get_status_prijave_redoslijed['redoslijed_statusa'];
	
	return $candidate_status_prijave_redoslijed;
}

function getCompanyAccessCRMR($nalogId){
		
	Global $db; 
	$companyIdsForAccess = array(1204); 
	$nalogId = intval($nalogId); 
	/*
		companyIdsForAccess - je niz vrijednosti u koje se upisuju IDs kompanija koje imaju pristup za pitanja po Enpal zahtjevima
		Ako se nekad kasnije pojavi da nekoj kompaniji treba odobriti pristup kao i enpalu - samo upisati ID i voditi racuna oko setup-a pitanja
	*/ 
	if($nalogId != 0){

		$companyId = getCompanyIdByNalogId($nalogId);
		if($companyId != "Undefined"){

			if(in_array($companyId, $companyIdsForAccess)){
				return 1;
			}else{
				return 0;
			}

		}else{
			return 0;
		}
		
	}else{
		return 0;
	}
}

function getNalogIdByAppointment($appointment_id){

	Global $db;
	$query_get_nalog_id = $db -> prepare('
		SELECT pap_nalog_id
		FROM idk_pp_appointments
		WHERE pap_id = :pap_id
	');
	$query_get_nalog_id -> execute(array(':pap_id' => $appointment_id));
	$row_get_nalog_id = $query_get_nalog_id -> fetch();
	$nalog_id = $row_get_nalog_id['pap_nalog_id'];
	
	return $nalog_id;
}

function getEnpalPreDefinedNotice(){
	return "
	1 Phase - Relaxing into the interview
	General impression:
	
	
	2 Phase - Job Experience and Motive
	Job Experience?
	
	Why this Job?
	
	Experience in Germany?
	
	Family Stance?
	
	
	3 Phase - Anti selling
	Reaction to Hard Anti-selling
	
	Worked in these conditions before?
	
	Hard working / Works over hours?";
}

function getCandidateIdFromDipl($dipl_id){
	Global $db;

	$query = $db -> prepare('
		SELECT kandidat_id
		FROM idk_kandidati
		WHERE kandidat_dipl_id  = :kandidat_dipl_id
	');

	$query -> execute(array(':kandidat_dipl_id' => $dipl_id));

	$row = $query -> fetch();
	$kandidat_id = $row['kandidat_id'];
	
	return $kandidat_id;
}

function getDiplIdFromCandidate($kandidat_id){
	Global $db;

	$query = $db -> prepare('
		SELECT kandidat_dipl_id
		FROM idk_kandidati
		WHERE kandidat_id  = :kandidat_id
	');

	$query -> execute(array(':kandidat_id' => $kandidat_id));

	$row = $query -> fetch();
	$dipl_id = $row['kandidat_dipl_id'];
	
	return $dipl_id;
}

function getTFVrsteForDiplAgent($agent_id){
	Global $db;

	$query = $db -> prepare('
		SELECT tfan_id
		FROM idk_tf_agent_nalog
		INNER JOIN idk_tf_vrste ON idk_tf_agent_nalog.tfan_vrsta_id = idk_tf_vrste.id
		WHERE tfan_agent = :tfan_agent AND category = 3
	');

	$query -> execute(array(':tfan_agent' => $agent_id));

	$tasks = array();
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $tasks[$row['tfan_id']] = true;
    }

    return $tasks;
}

function getTFTasksForAgent($agent_id){
	Global $db;

	$query = $db -> prepare('
		SELECT idk_tf_agent_nalog.*, CONCAT(employee_firstname," ",employee_lastname) as employee_fullname, idk_tf_vrste.name as tf_vrsta_name
		FROM idk_tf_agent_nalog
		INNER JOIN idk_employees
		ON idk_tf_agent_nalog.tfan_agent = idk_employees.employee_id
		INNER JOIN idk_tf_vrste ON idk_tf_agent_nalog.tfan_vrsta_id = idk_tf_vrste.id
		WHERE tfan_agent = :tfan_agent AND idk_tf_vrste.category = 3 AND tfan_nalog = 0
		ORDER BY idk_tf_agent_nalog.tfan_vrsta_id
	');

	$query -> execute(array(
		':tfan_agent' => $agent_id
	));

	$rows = $query->fetchAll(PDO::FETCH_ASSOC);

    return $rows;
}

function getNalogIdForLinkId($linkId) {
	Global $db; 
	$linkId = intval($linkId);
	if ($linkId != 0) {
		$query = $db->prepare("
			SELECT 
				lg_nalogid 
			FROM 
				idk_link_generator
			WHERE 
				lg_id = :lg_id
		");
		$query->execute(array(
			':lg_id' => $linkId
		));
		$row = $query->fetch();
		return intval($row["lg_nalogid"]); 
	} else {
		return 0; 
	}
}

function insertCandidatInProjectForPartnerDVAGArrayR($candidateId, $insertORget) {
	Global $db;
	Global $logged_employee_id; 

	$candidateId 					= intval($candidateId);
	$linkId 						= intval(getLinkForCandidate($candidateId));
    $eu_citizenship                 = checkEUKandidat($candidateId);
	/*
		Setup START
		*/
			$linkIdGeneral					= 2040;
			$projectConditionGeneral 		= "(project_name LIKE '%Kandidati Partnera DVAG%')"; 
			$projectConditionOther			= "(project_name LIKE '%Prijave%' OR project_name LIKE '%Prijava%')";
			$projectConditionEU			    = "project_eukandidati = 1";
		/*
		Setup END
	*/

	/*
		******************************************************************************************
		*                                   Rezultati funkcije                                   *
		******************************************************************************************
		* status * * projectId * * projectCandidateId * * message *
		******************************************************************************************
		*    0   * *     0     * * 	        0         * * The function was not executed
		*   100  * *     0     * * 	        0         * * Function called with invalid parameters
		*   101  * *     0     * * 	        0         * * Order id not found
		*   102  * *     0     * * 	        0         * * No project id found
		*   103  * *  number   * * 	        0         * * Error in the insert
		*   104  * *  number   * * 	        0         * * Error for parameter getORinsert
		*    1   * *  number   * * 	     number       * * Candidate added to the project
		******************************************************************************************
	*/

	$result = array(
		"status" => 0, 
		"message" => "The function was not executed", 
		"projectId" => 0, 
		"projectCandidateId" => 0
	);

	if ($candidateId != 0 AND $linkId != 0) {
		$nalogIdForLink = intval(getNalogIdForLinkId($linkId));
		if ($nalogIdForLink != 0) {
			$sqlQueryCheckProjectId = "";
			if ($linkId == $linkIdGeneral) {
				$sqlQueryCheckProjectId = $projectConditionGeneral;
			} else {
                if($eu_citizenship){
                    $sqlQueryCheckProjectId = $projectConditionEU;
                }else{
				    $sqlQueryCheckProjectId = $projectConditionOther;
                }
			}

			$queryCheckProjectId = $db->prepare("
				SELECT 
					project_id
				FROM 
					idk_projects
				WHERE ".$sqlQueryCheckProjectId." AND project_nalogid = :project_nalogid
			");
			$queryCheckProjectId->execute(array(
				':project_nalogid' => $nalogIdForLink
			));
			if ($queryCheckProjectId->rowCount() != 0) {
				$rowCheckProjectId = $queryCheckProjectId->fetch();
				$projectId = $rowCheckProjectId["project_id"];

				if($insertORget == 2){
					
					$result["status"] 				= 1; 
					$result["message"] 				= "Returned only project ID";
					$result["projectId"] 			= $projectId;

				}elseif($insertORget == 1){
					$queryInsert = $db->prepare("
						INSERT INTO idk_project_kandidati
							(pk_projectid, pk_kandidatid)
						VALUES
							(:pk_projectid, :pk_kandidatid)
					");
					$queryInsert->execute(array(
						':pk_projectid' => $projectId,
						':pk_kandidatid' => $candidateId
					));

					if ($queryInsert) {
						$lastInsert = $db->lastInsertId();
						$result["status"] 				= 1; 
						$result["message"] 				= "Candidate added to the project";
						$result["projectId"] 			= $projectId;
						$result["projectCandidateId"] 	= $lastInsert;

						addToLogsStatusPrijave(NULL, $projectId, 2, $candidateId, 3);
					} else {
						$result["status"] 				= 103; 
						$result["message"] 				= "Error in the insert";
						$result["projectId"] 			= $projectId;
					}
				}else{
					$result["status"] 				= 104; 
					$result["message"] 				= "Error for parameter getORinsert";
					$result["projectId"] 			= $projectId;
				}

				
			} else {
				$result["status"] 	= 102; 
				$result["message"] 	= "No project id found";
			}
		} else {
			$result["status"] 	= 101; 
			$result["message"] 	= "Order id not found";
		}
	} else {
		$result["status"] 	= 100; 
		$result["message"] 	= "Function called with invalid parameters";
	}
	return $result;
}

function getStatusPrijavePrint($status_prijave){

	$status_prijave_return = "";
	if($status_prijave == 1){
		$status_prijave_return = '<span class="label label-default material-label material-label_default main-container__column text-left">Slobodan</span>';
	}
	else if($status_prijave == 2){
		$status_prijave_return = '<span class="label label-warning material-label material-label_warning main-container__column text-left">Projekt NR</span>';
	}
	else if($status_prijave == 3){ 
		$status_prijave_return = '<span class="label label-info material-label material-label_info main-container__column text-left">Casting</span>';
	}
	else if($status_prijave == 4){
		$status_prijave_return = '<span class="label label-success material-label material-label_success main-container__column text-left">Završen</span>';
	}
	else if($status_prijave == 5){
		$status_prijave_return = '<span class="label label-danger material-label material-label_danger main-container__column text-left">Odbijen</span>';
	}
	else if($status_prijave == 6){
		$status_prijave_return = '<span class="label label-default material-label material-label_default main-container__column text-left" style="background-color: #B60606; color: white;">U projektu RZ</span>';
	}
	else if($status_prijave == 7){
		$status_prijave_return = '<span class="label label-default material-label material-label_default main-container__column text-left" style="background-color: #E2E241; color: white;">Čeka ugovor</span>';
	}
	else if($status_prijave == 8){
		$status_prijave_return = '<span class="label label-primary material-label material-label_primary main-container__column text-left">Poslan ugovor</span>';
	}
	else if($status_prijave == 9){
		$status_prijave_return = '<span class="label label-danger material-label main-container__column text-left" style="background-color: #66FFB2; color: black;">Potpisan ugovor</span>';
	}
	else if($status_prijave == 10){
		$status_prijave_return = '<span class="label label-light material-label material-label_light main-container__column text-left" style="background-color: #33FF33; color: white;">Početak rada</span>';
	}
	else if($status_prijave == 12){
		$status_prijave_return = '<span class="label label-light material-label main-container__column text-left" style="background-color: #E9EFC0; color: black;">Prikupljanje dokumentacije</span>';
	}
	else if($status_prijave == 15){
		$status_prijave_return = '<span class="label label-light material-label main-container__column text-left" style="background-color: #B4E197; color: black;">Čeka termin</span>';
	}
	else if($status_prijave == 18){
		$status_prijave_return = '<span class="label label-light material-label main-container__column text-left" style="background-color: #83BD75; color: black;">Čeka vizu</span>';
	}
	else if($status_prijave == 27){
		$status_prijave_return = '<span class="label label-light material-label main-container__column text-left" style="background-color: #4E944F; color: black;">Dobio vizu</span>';
	}
	else if($status_prijave == 21){
		$status_prijave_return = '<span class="label label-light material-label main-container__column text-left" style="background-color: #FFC3C3; color: black;">Dopuna dokumenata</span>';
	}
	else if($status_prijave == 24){
		$status_prijave_return = '<span class="label label-light material-label main-container__column text-left" style="background-color: #FF8C8C; color: black;">Odbijena viza</span>';
	}

	return $status_prijave_return;
}

function getTFCategoryFromVrstaId($vrsta_id){
	Global $db;

	$query = $db -> prepare('
		SELECT category
		FROM idk_tf_vrste
		WHERE id = :id
	');

	$query -> execute(array(':id' => $vrsta_id));
	$row = $query -> fetch();
	$category = $row['category'];

    return $category;
}

function getOpenPlzResponse($term, $page){
	$city = "";
	$plz = "";
	
	if(is_numeric($term)){
		$plz = $term;
	}
	else{
		$city = $term;
	}

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$rls="https://openplzapi.org/de/Localities?postalCode=".$plz."&name=".urlencode($city)."&page=".$page."&pageSize=50";

	curl_setopt($ch, CURLOPT_URL,$rls);
	curl_setopt($ch, CURLOPT_FAILONERROR, true);
	$result=curl_exec($ch);
	
	curl_close($ch);

	$data = json_decode($result, TRUE);

	return $data;
}

function findCityIndex($cities_data, $city){
	for($i = 0; $i < count($cities_data); $i++){
		if($cities_data[$i]['name'] == $city) return $i;
	}
}

function getModulePermission($module_permission_id){
	Global $db;
	Global $logged_employee_id;
	$check_query = $db->prepare("SELECT mp_employees FROM idk_module_permissions WHERE mp_id = $module_permission_id");
	$check_query->execute();
	$check_row = $check_query->fetch();
	$check_row_exp = explode(",", $check_row['mp_employees']);
	if(in_array($logged_employee_id, $check_row_exp))
		return true;
	else
		return false;
}

function isCandidateDVAG($kandidat_id){
	Global $db;

	$query = $db -> prepare('SELECT
								kandidat_id
							FROM
							idk_kandidati
							JOIN idk_jobstep_partners ON idk_kandidati.kandidat_partnerid = idk_jobstep_partners.jp_id
							WHERE idk_jobstep_partners.jp_partner_company = 3 AND kandidat_id = :kandidat_id');

	$query -> execute(array(
		':kandidat_id' => $kandidat_id 
	));

	$row = $query->rowCount();
	
	if($row > 0){
		return true;
	}else{
		return false;
	}

}

function sendMailMaklerPassword($email_address, $new_password, $user_fullname, $file = NULL){

	$mail_subject = "Ihr neues Passwort";
	$mail_body = "Your password has been generated and it's: $new_password";
	$mail_body = file_get_contents('https://api.pa.job-step.com/mail-templates/account_password.html');

	$tutorial_path_ios = 'https://jobstep-public-assets.s3.eu-west-1.amazonaws.com/instructions/partnerapp/Installationsanleitung+Partner-App+(iPhone).pdf';
	$tutorial_path_android = 'https://jobstep-public-assets.s3.eu-west-1.amazonaws.com/instructions/partnerapp/Installationsanleitung+Partner-App+(Android-Handys)+.pdf';


	$placeholders = array(
		'{email}' => $email_address,
		'{user_fullname}' => $user_fullname,
		'{password}' => $new_password,
		'{android_link}' => $tutorial_path_android,
		'{ios_link}' => $tutorial_path_ios,
	);
	
	$mail_body = str_replace(array_keys($placeholders), array_values($placeholders), $mail_body);

	$hostName = "smtp.gmail.com";
	$userName = "support@job-step.com";
	$password = "eooc nnxo aylp lqkh";
	
	$mail = new PHPMailer;
	$mail->isSMTP();
	$mail->Host = $hostName;
	$mail->SMTPAuth = true;
	$mail->Username = $userName;
	$mail->Password = $password;
	$mail->SMTPSecure = 'ssl';
	$mail->Port = 465;
	$mail->CharSet = 'UTF-8';
	$mail->setFrom('no-reply@job-step.com','Partner App');

	$mail->addAddress($email_address);

	$mail->Subject = $mail_subject;
	$mail->Body    = $mail_body;
	$mail->AltBody = "Password";

	if(!$mail->send()) {
		return $mail->ErrorInfo;
	}else{
		return "Sent";
	}
}

function addPartnerAppDummyData($dummy_token){

	Global $db;

	$get_id = $db -> prepare("
		SELECT jp_id FROM idk_jobstep_partners WHERE jp_mailconfirmation_token = :token
	");

	$get_id -> execute(array(':token' => $dummy_token));
	$row_id = $get_id -> fetch();

	$id = $row_id['jp_id'];

	//NOTIFICATION PREFERENCES
	createPersonalNotificationPreferences($id);
	
	// CANDIDATES AND LEADS
	$query = $db->prepare("
		INSERT INTO idk_kandidati
			(kandidat_ime, kandidat_prezime, kandidat_email, kandidat_mobitel, kandidat_status, kandidat_datumrodjenja, zaduzeni_makler_id, zaduzen_makleru_datum, kandidat_destinacija_grad, kandidat_destinacija_postanski_broj, kandidat_nalog_id, kandidat_partner_lead_status, kandidat_status_prijave, assigned_to_makler)
		VALUES
			(:kandidat_ime, :kandidat_prezime, :kandidat_email, :kandidat_mobitel, :kandidat_status, :kandidat_datumrodjenja, :zaduzeni_makler_id, now(), :kandidat_destinacija_grad, :kandidat_destinacija_postanski_broj, :kandidat_nalog_id, :kandidat_partner_lead_status, :kandidat_status_prijave, :assigned_to_makler)
	");

	$query->execute(array(
		':kandidat_ime' => "Shawn",
		':kandidat_prezime' => "Bernard",
		':kandidat_email' => "example@gmail.com",
		':kandidat_mobitel' => "+38761234567",
		':kandidat_status' => 1,
		':kandidat_datumrodjenja' => '1992-04-05',
		':zaduzeni_makler_id' => $id,
		':assigned_to_makler' => $id,
		':kandidat_destinacija_grad' => 'Berlin',
		':kandidat_destinacija_postanski_broj' => '10115',
		':kandidat_partner_lead_status' => 1,
		':kandidat_status_prijave' => 2,
		':kandidat_nalog_id' => '338'

	));

	$query->execute(array(
		':kandidat_ime' => "Mattie",
		':kandidat_prezime' => "Thomson",
		':kandidat_email' => "example@gmail.com",
		':kandidat_mobitel' => "+38761234567",
		':kandidat_status' => 1,
		':kandidat_datumrodjenja' => '1990-04-07',
		':zaduzeni_makler_id' => $id,
		':assigned_to_makler' => $id,
		':kandidat_destinacija_grad' => 'Bremen',
		':kandidat_destinacija_postanski_broj' => '28195',
		':kandidat_partner_lead_status' => 1,
		':kandidat_status_prijave' => 2,
		':kandidat_nalog_id' => '338'

	));

	$query->execute(array(
		':kandidat_ime' => "Faris",
		':kandidat_prezime' => "Warren",
		':kandidat_email' => "example@gmail.com",
		':kandidat_mobitel' => "+38761234567",
		':kandidat_status' => 1,
		':kandidat_datumrodjenja' => '1995-09-05',
		':zaduzeni_makler_id' => $id,
		':assigned_to_makler' => $id,
		':kandidat_destinacija_grad' => 'Potsdam',
		':kandidat_destinacija_postanski_broj' => '14467',
		':kandidat_partner_lead_status' => 1,
		':kandidat_status_prijave' => 2,
		':kandidat_nalog_id' => '338'

	));

                      
	// COMPANIES
	generatePartnerCompanyData($id, "AeroVanguard Technologies");
	generatePartnerCompanyData($id, "BioInfo Nexus");
	generatePartnerCompanyData($id, "GreenTech Genesis");

}

function generatePartnerCompanyData($id, $company_name){
	Global $db;

	$contact_person_fname = "Karl";
	$contact_person_lname = "Müller";
	$contact_job = "CEO";
	$company_size = "51-100";
	$company_email = "mail@mail.de";
	$company_phone = "4915123456789";   
	$company_country = "Germany";
	$company_city = "Stuttgart";
	$company_zip_code = "70174";
	$company_address = "456 Renewable Lane";
	$message = "Sehr geehrte Damen und Herren, ich bin auf der Suche nach Informationen zu Ihren Kfz-Werkstattleistungen und würde gerne mehr erfahren.";
	$date = date("Y-m-d H:i:s");
	$meeting_appointment = '2024-10-12 10:00:00';
	$meeting_type = 1;
	$total_workers_required =  "30";
	$company_professions = "1,2";
	
	$lang = 'de';
	
	$sql = "INSERT INTO idk_companies (js_partner_id, company_origin, company_name, company_size, company_zipcode, company_city, company_country, company_address, company_message, company_datetime,company_total_workers_required,company_professions)
			VALUES (:js_partner_id, :company_origin, :company_name, :company_size, :compnay_zipcode, :company_city, :company_country, :company_address, :company_message, :company_datetime, :company_total_workers_required,:company_professions)";
	$stmt = $db->prepare($sql);
	$stmt->execute(array(
		':js_partner_id' => $id,
		':company_origin' => 5,
		':company_name' => $company_name,
		':company_size' => $company_size,
		':compnay_zipcode' => $company_zip_code,
		':company_city' => $company_city,
		':company_country' => $company_country,
		':company_address' => $company_address,
		':company_message' => $message,
		':company_datetime' => $date,
		':company_total_workers_required' => $total_workers_required,
		':company_professions'=>$company_professions
	));

	$company_id = $db->lastInsertId();

	$profession_name_sql = "kp_ime_de";
	if($lang=='en'){
		$profession_name_sql = "kp_ime_en";
	}else{
		$profession_name_sql = "kp_ime_de";
	} 


	$sql = "INSERT INTO idk_companies_info (comi_group, comi_title, comi_data, comi_primary, comi_companyid)
			VALUES (:comi_group, :comi_title, :comi_data, :comi_primary, :comi_companyid)";
	$stmt = $db->prepare($sql);
	$stmt->execute(array(
		':comi_group' => 1,
		':comi_title' => 'Telefon',
		':comi_data' => $company_phone,
		':comi_primary' => 1,
		':comi_companyid' => $company_id
	));

	$sql = "INSERT INTO idk_companies_info (comi_group, comi_title, comi_data, comi_primary, comi_companyid)
			VALUES (:comi_group, :comi_title, :comi_data, :comi_primary, :comi_companyid)";
	$stmt = $db->prepare($sql);
	$stmt->execute(array(
		':comi_group' => 2,
		':comi_title' => 'E-mail',
		':comi_data' => $company_email,
		':comi_primary' => 1,
		':comi_companyid' => $company_id
	));
	if(!empty($contact_person_fname)){
		$sql = "INSERT INTO idk_contacts (contact_firstname, contact_lastname, contact_companyid, contact_status, contact_job_title, contact_datetime)
				VALUES (:contact_firstname, :contact_lastname, :contact_companyid, :contact_status, :contact_job_title, :contact_datetime)";
		$stmt = $db->prepare($sql);
		$stmt->execute(array(
			':contact_firstname' => $contact_person_fname,
			':contact_lastname' => $contact_person_lname,
			':contact_companyid' => $company_id,
			':contact_status' => 1,
			':contact_job_title' => $contact_job,
			':contact_datetime' => $date
		));
	}
	
	//INSERT COMPANY INTO CLIENTS FOR SALE/FIRST CALL
	$sql = "INSERT INTO idk_clients (client_name, client_country, client_city, client_address, client_pp, client_telephone, client_email, client_origin, client_recommendation, client_recommendation_company, client_fc_or_sales, client_fc_status, client_sales_status, client_manager, client_sales_manager, client_contract, client_description) 
			VALUES (:client_name, :client_country, :client_city, :client_address, NULL, :client_telephone, :client_email, 5, NULL, :client_recommendation_company, 1, 2, 0,NULL, NULL, 0, :client_description);";

	$stmt = $db->prepare($sql);
	$stmt->execute(array(
		':client_name' => $company_name,
		':client_country' => $company_country,
		':client_city' => $company_city,
		':client_address' => $company_address,
		':client_telephone' => $company_phone,
		':client_email' => $company_email,
		':client_recommendation_company'=> $company_id,
		':client_description' => $message,
	));
	$employee_id_adnan = 493;//Za sada je receno da svi sastanci idu na adnana 
	//INSERT MEETING DETAILS
	$meeting_sql = "INSERT INTO idk_partner_meetings (meeting_appointment,meeting_type,meeting_employee,meeting_company_id) VALUES (:meeting_appointment , :meeting_type,:meeting_employee, :meeting_company_id);";

	$meeting_stmt = $db->prepare($meeting_sql);
	$meeting_stmt->execute(array(
		':meeting_appointment'  => $meeting_appointment,
		':meeting_type'         => $meeting_type,
		':meeting_employee'     => $employee_id_adnan,
		':meeting_company_id'   => $company_id
	));
}

function getChatDetails($chatId = NULL, $getMain = 0){
	Global $db;

	$sqlConcat = "";
	if(isset($chatId)){
		$sqlConcat = "AND parentQuestion = $chatId";
	}
	if($getMain == 1){
		$sqlConcat = "AND parentQuestion IS NULL";
	}	

	$getAnswerStmt = $db->prepare("SELECT id, type, question, answer, parentQuestion FROM idk_chat_question WHERE  deletedAt IS NULL $sqlConcat");
	$getAnswerStmt->execute();
	$getAnswers = $getAnswerStmt->fetchAll();


	$data = [];
	foreach ($getAnswers as $getAnswer) {
		$allLanguages = [];
		$answer_json = json_decode($getAnswer['answer'], true);
		$question_json = json_decode($getAnswer['question'], true);

		$allLanguages = array_merge(
			$allLanguages, 
			array_map('ucfirst', array_keys($answer_json)), 
			array_map('ucfirst', array_keys($question_json))
		);

		$allLanguages = array_unique($allLanguages);
    	sort($allLanguages);

		$data[] = [
			"id" => $getAnswer['id'],
			"type" => explode(',', $getAnswer['type']),
			"isExpanded"=> false,
			"subquestions" => getSubquestionsIds($getAnswer['id']),
			"isSubquestion" => $getAnswer['parentQuestion'] ? true : false,
			"parentQuestion" => $getAnswer['parentQuestion'],
			"selectedLanguages" => $allLanguages
		];
		foreach ($question_json as $lang => $question) {
            $data[count($data) - 1]["question" . ucfirst($lang)] = $question;
        }

        foreach ($answer_json as $lang => $answer) {
            $data[count($data) - 1]["answer" . ucfirst($lang)] = $answer;
        }
	}

	return $data;
}

function getSubquestionsIds($parentId) {
    global $db;
	$getSubquestionsSql = "SELECT id FROM idk_chat_question WHERE parentQuestion = :parentId AND deletedAt IS NULL";
    $getSubquestionsStmt = $db->prepare($getSubquestionsSql);
    $getSubquestionsStmt->bindParam(':parentId', $parentId);
    $getSubquestionsStmt->execute();
    $subquestions = $getSubquestionsStmt->fetchAll(PDO::FETCH_COLUMN);

    $stringSubquestions = [];
    foreach ($subquestions as $subquestion) {
        $stringSubquestions[] = strval($subquestion);
    }

    return $stringSubquestions;
}

function updateChatQuestion($id, $question_json, $answer_json, $visibility, $type){
	Global $db;
	$fetchTypesStmt = $db->prepare("SELECT idk_chat_question.type FROM idk_chat_question WHERE id = $id");
	$fetchTypesStmt->execute();
	$currentTypesString = $fetchTypesStmt->fetch(PDO::FETCH_COLUMN);
	$currentTypes = explode(',', $currentTypesString);

    $typeIndex = array_search($type, $currentTypes);

    if ($visibility === 1 && $typeIndex === false) {
        $currentTypes[] = $type;
    } elseif ($visibility === 0 && $typeIndex !== false) {
        unset($currentTypes[$typeIndex]);
    }

    $updatedTypesString = implode(',', $currentTypes);

	$updateStmt = $db->prepare("UPDATE idk_chat_question SET question = :question_json, answer = :answer_json, idk_chat_question.type = :updatedTypesString WHERE id = :id");
	$updateStmt->execute([
		'question_json' => $question_json,
		'answer_json' => $answer_json,
		'updatedTypesString' => $updatedTypesString,
		'id' => $id
	]);

	return "Updated question with id: $id";
}

function updateChatQuestionVisibility($id, $visibility, $type){
	Global $db;
	$fetchTypesStmt = $db->prepare("SELECT idk_chat_question.type FROM idk_chat_question WHERE id = $id");
	$fetchTypesStmt->execute();
	$currentTypesString = $fetchTypesStmt->fetch(PDO::FETCH_COLUMN);
	$currentTypes = explode(',', $currentTypesString);

    $typeIndex = array_search($type, $currentTypes);

    if ($visibility === 1 && $typeIndex === false) {
        $currentTypes[] = $type;
    } elseif ($visibility === 0 && $typeIndex !== false) {
        unset($currentTypes[$typeIndex]);
    }
    $updatedTypesString = implode(',', $currentTypes);

	$updateStmt = $db->prepare("UPDATE idk_chat_question SET  idk_chat_question.type = :updatedTypesString WHERE id = :id");
	$updateStmt->execute([
		'updatedTypesString' => $updatedTypesString,
		'id' => $id
	]);

	return "Updated visibility for question with id: $id";
}

function deleteChatQuestion($id){
	Global $db;

	$deleteStmt = $db->prepare("UPDATE idk_chat_question SET deletedAt = NOW() WHERE id = $id;");
	$deleteStmt->execute();

	return "Deleted question with id: $id";
}
function addChatQuestion($type_id, $question_json, $answer_json, $parent_id = NULL){
	Global $db;
	
	$insertStmt = $db->prepare("INSERT INTO idk_chat_question (type, question, answer, parentQuestion) VALUES (:type_id, :question_json, :answer_json, :parent_id);");
	$insertStmt->bindParam(':type_id', $type_id);
	$insertStmt->bindParam(':question_json', $question_json);
	$insertStmt->bindParam(':answer_json', $answer_json);
	$insertStmt->bindParam(':parent_id', $parent_id);
	$insertStmt->execute();
	return print_r($insertStmt->errorInfo());

	$id = $db->lastInsertId();

	return "Added new quesiton with id: $id";
}

function checkAgentNalogConnection($agent_id, $nalog_id, $tf_vrsta_id){
	Global $db;
	
	$query = $db -> prepare('
		SELECT COUNT(1) as isConnected
		FROM idk_tf_agent_nalog
		WHERE tfan_nalog = :tfan_nalog AND tfan_agent = :tfan_agent AND tfan_vrsta_id = :tfan_vrsta_id;
	');

	$query -> execute(array(
		':tfan_nalog' => $nalog_id, 
		':tfan_agent' => $agent_id, 
		':tfan_vrsta_id' => $tf_vrsta_id 
	));

	$row = $query -> fetch();
	return $row["isConnected"];

}

function getNalogPriority($nalog_id){
	Global $db;
	
	$query = $db -> prepare('
		SELECT nalog_prioritet
		FROM idk_nalozi
		WHERE nalog_id = :nalog_id
	');

	$query -> execute(array(
		':nalog_id' => $nalog_id
	));

	$row = $query -> fetch();

	return $row["nalog_prioritet"];

}

function hasCandidateActiveTaskForVrsta($kandidat_id, $tf_vrsta_id){
	Global $db;

	$query = $db->prepare("
		SELECT COUNT(1) as hasActiveTask
		FROM idk_task_force
		WHERE tf_candidate_id = :tf_candidate_id AND tf_last_active_task = 1 AND tf_vrsta_id = :tf_vrsta_id
	");

	$query->execute(array(
		':tf_candidate_id' => $kandidat_id,
		':tf_vrsta_id' => $tf_vrsta_id
	));

	$row = $query -> fetch();

	return $row["hasActiveTask"];
}

function reserveAgentAndCandidateOnProjectChange($agent_id, $nalog_id, $candidate_id, $project_id, $tf_vrsta_id) {
    global $db;

    $isAgentReserved = checkAgentReservation($agent_id);
    $isCandidateReserved = checkCandidateReservation($candidate_id);
    $isAgentConnectedToNalog = checkAgentNalogConnection($agent_id, $nalog_id, $tf_vrsta_id);
    $hasPriority = getNalogPriority($nalog_id);
    $hasActiveTask = hasCandidateActiveTaskForVrsta($candidate_id, $tf_vrsta_id);
	$employee_status = getEmployeeStatusById($agent_id);
	$employee_status = explode( ',' , $employee_status);
    $flag = true;

    $tf_agent_error = "";
    if (!in_array("18", $employee_status)) {
        $tf_agent_error = "$agent_id nije TF Agent.";
        $flag = false;
    }

    $agent_reservation_error = "";
    if ($isAgentReserved > 0) {
        $agent_reservation_error = "Agent $agent_id je rezervisan.";
        $flag = false;
    }

    $candidate_reservation_error = "";
    if ($isCandidateReserved > 0) {
        $candidate_reservation_error = "Kandidat $candidate_id je rezervisan.";
        $flag = false;
    }

    $candidate_connection_error = "";
    if ($isAgentConnectedToNalog < 1) {
        $candidate_connection_error = "Agent $agent_id nije povezan za $nalog_id nalog.";
        $flag = false;
    }

    $priority_error = "";
    if (!$hasPriority) {
        $priority_error = "Nalog $nalog_id nema podešen prioritet";
        $flag = false;
    }

    $candidate_active_task_error = "";
    if ($hasActiveTask > 0) {
        $candidate_active_task_error = "Kandidat $candidate_id ima aktivan task za casting.";
        $flag = false;
    }

    $response = [
		'success' => $flag,
		'message' => ($flag)
			? "Zaposlenik $agent_id dobio kandidata $candidate_id na zvanje za TF casting prebacivanjem u projekt Prijave."
			: "Zaposlenik $agent_id nije dobio kandidata $candidate_id na zvanje za TF casting prebacivanjem u projekt Prijave.",
	];

	if (!$flag) {
		$response['errors'] = [
			'tf_agent_error' => $tf_agent_error,
			'agent_reservation_error' => $agent_reservation_error,
			'candidate_reservation_error' => $candidate_reservation_error,
			'candidate_connection_error' => $candidate_connection_error,
			'priority_error' => $priority_error,
			'candidate_active_task_error' => $candidate_active_task_error,
		];
	}

	if($flag){
		$insert_reservation = $db->prepare("
			INSERT INTO idk_tf_reservations
				(tf_kandidat_id, tf_agent_id, tf_projekt_id, tf_nalog_id, tf_vrsta_id, tf_dipl_id)
			VALUES
				(:candidate_id, :tf_agent_id, :projekt_id, :nalog_id, :vrsta_id, :tf_dipl_id)
		");

		$insert_reservation->execute(array(
			':tf_agent_id' => $agent_id,
			':candidate_id' => $candidate_id,
			':projekt_id' => $project_id,
			':nalog_id' => $nalog_id,
			':vrsta_id' => $tf_vrsta_id,
			':tf_dipl_id' => null
		));
		addToLogs(json_encode($response), 0);
		return true;
	}else{
		addToLogs(json_encode($response), 0);
		return false;
	}
}
function getActiveLanguageID($kandidat_id){
	Global $db;
	$sql = "SELECT
				kj_id
			FROM
			(
				SELECT 
					kj_slusanje,
					kj_id
				FROM
					idk_kandidat_jezici
				WHERE
					kj_kandidatid = $kandidat_id
				AND
					kj_naziv LIKE '%Njemacki%'
			) as jezik
			JOIN
				(
					SELECT
						cvl_id
					FROM
						idk_candidate_verified_languages
					WHERE
						cvl_active = 1
				) as aktivni
			ON
				jezik.kj_id = aktivni.cvl_id
			";
	$get_active_language = $db->prepare($sql);
	$get_active_language->execute();
	$result = $get_active_language->fetch();
	return $result['kj_id'];
}

function insertCandidateLanguageLogs($cll_candidate_id, $cll_cvl_id, $cll_status) {
	Global $db;
	Global $logged_employee_id;
	
	$log_date=date('Y-m-d H:i:s');

	$query_log=$db->prepare("
				INSERT INTO idk_candidate_language_logs
					(cll_candidate_id,cll_cvl_id,cll_status,cll_date,cll_employee_id) 
				VALUE 
					(:cll_candidate_id,:cll_cvl_id,:cll_status,:cll_date,:cll_employee_id)
	");

	$query_log->execute(array(
		':cll_candidate_id'=>$cll_candidate_id,
		':cll_cvl_id'=>$cll_cvl_id,
		':cll_status'=>$cll_status,
		':cll_date'=>$log_date,
		':cll_employee_id'=>$logged_employee_id
	));
}

function hasCandidateWrongNumber($kandidat_id){
	Global $db;

	$query = $db -> prepare('SELECT
								kandidat_pogresan_broj
							FROM
								idk_kandidati
							WHERE kandidat_id = :kandidat_id');

	$query -> execute(array(
		':kandidat_id' => $kandidat_id 
	));

	$row = $query->fetch();
	return $row['kandidat_pogresan_broj'];
}

function updateCandidateWrongNumber($kandidat_id, $status){
	Global $db;

	$query = $db->prepare("
						UPDATE idk_kandidati
						SET	kandidat_pogresan_broj = :kandidat_pogresan_broj
						WHERE kandidat_id = :kandidat_id
						");

	$query->execute(array(
				':kandidat_pogresan_broj' => $status,
				':kandidat_id' => $kandidat_id
				));
}

function assignToMakler($kandidat_id, $nalog_id){
	Global $db;
	$sql = "SELECT js_partner_id FROM idk_nalozi JOIN idk_companies ON idk_nalozi.kompanija_id = idk_companies.company_id WHERE nalog_id = :nalog_id";
	$stmt = $db->prepare($sql);
	$stmt->execute([':nalog_id' => $nalog_id]);
	$result = $stmt->fetch();
	$partner_id = $result['js_partner_id'];

	if($partner_id != null){
		$sql = "UPDATE idk_kandidati SET assigned_to_makler = :assigned_to_makler WHERE kandidat_id = :kandidat_id";
		$stmt = $db->prepare($sql);
		$stmt->execute([':assigned_to_makler' => $partner_id, ':kandidat_id' => $kandidat_id]);
	}
}

function getUnavailableActiveCounter($kandidat_id){
	Global $db;

	$query = $db -> prepare('SELECT
								brojac
							FROM
								idk_nedostupan_log
							WHERE kandidat = :kandidat AND status=1');

	$query -> execute(array(
		':kandidat' => $kandidat_id 
	));

	$row = $query->fetch();
	return $row['brojac'];
}

function checkNalogAvailability($kandidat_id, $nalog_id){
	Global $db;

	$query = $db -> prepare('SELECT
								id
							FROM
								idk_nedostupan_log
							WHERE kandidat = :kandidat AND nalog = :nalog AND status=1');

	$query -> execute(array(
		':kandidat' => $kandidat_id, 
		':nalog' => $nalog_id 
	));

	$row = $query->fetch();
	return $row['id'];
}

function updateCandidateAvailability($kandidat_id, $availability){
	Global $db;

	$query = $db->prepare("
						UPDATE idk_kandidati
						SET	kandidat_nedostupan = :kandidat_nedostupan
						WHERE kandidat_id = :kandidat_id
						");

	$query->execute(array(
				':kandidat_nedostupan' => $availability,
				':kandidat_id' => $kandidat_id
				));
}

function addUnavailableLog($kandidat_id, $nalog_id){
	Global $db;
	Global $logged_employee_id;

	$brojac = intval(getUnavailableActiveCounter($kandidat_id))+1;

	$query_deactivate = $db->prepare("
						UPDATE idk_nedostupan_log
						SET	status = 0
						WHERE kandidat = :kandidat AND status = 1
						");

	$query_deactivate->execute(array(
				':kandidat' => $kandidat_id
				));

	$query_log=$db->prepare("
				INSERT INTO idk_nedostupan_log
					(kandidat,nalog,agent,brojac,status) 
				VALUE 
					(:kandidat,:nalog,:agent,:brojac,:status)
	");

	$query_log->execute(array(
		':kandidat'=>$kandidat_id,
		':nalog'=>$nalog_id,
		':agent'=>$logged_employee_id,
		':brojac'=>$brojac,
		':status'=>1
	));

	if($brojac == 4){
		updateCandidateAvailability($kandidat_id, 1);
	}
}

function checkCandidateFreezed($kandidat_id){
	Global $db;

	$query = $db -> prepare('SELECT
								kandidat_nedostupan
							FROM
								idk_kandidati
							WHERE kandidat_id  = :kandidat_id');

	$query -> execute(array(
		':kandidat_id' => $kandidat_id 
	));

	$row = $query->fetch();
	return $row['kandidat_nedostupan'];
}

function checkCandidateCooling($kandidat_id){
	Global $db;

	$query = $db -> prepare('SELECT
								id
							FROM
								idk_nedostupan_log
							WHERE kandidat = :kandidat AND brojac=2 AND status=1 AND DATE(NOW() - INTERVAL 30 DAY) < DATE(doe)');

	$query -> execute(array(
		':kandidat' => $kandidat_id 
	));

	$row = $query->fetch();
	if($row['id'] != NULL){
		return True;
	}else{
		return False;
	}
}
function shouldSendPersonalNotification($partner_id,$notification_type){
	Global $db;

	createPersonalNotificationPreferences($partner_id);

	//check if notifications should be sent based on partner preferences, 0-false 1-true
	$get_partner_data_query=$db->prepare("SELECT preference_id,mute_all_notifications,blocked_notification_types  FROM idk_partner_personal_notifications_preferences WHERE partner_id = :partner_id");
	$get_partner_data_query->execute(array(
		':partner_id'=>$partner_id
	));
	$partner_data = $get_partner_data_query->fetch();
	$preference_id 					= $partner_data['preference_id'];
	$partner_notifications_mute 	= $partner_data['mute_all_notifications'];
	$partner_blocked_notifications 	= $partner_data['blocked_notification_types'];
	$row_count = $get_partner_data_query->rowCount();

	if(in_array($notification_type,explode(',', $partner_blocked_notifications)) OR $partner_notifications_mute==1 OR $row_count == 0){
		return 0;
	}else{
		return 1;
	} 	
	
}

function createPartnerPersonalNotification($parnter_id, $notification_type, $payload, $action, $title, $content){
	Global $db;
	$create_notification_query = $db -> prepare("INSERT INTO idk_partner_notification(notification_type_id, notification_audience, notification_payload, 
																notification_title, notification_content, notification_partner_id, notification_action)
													VALUES(:type_id,'PERSONAL',:payload,:title,:content,:partner_id,:action_enum);");

	$create_notification_query->execute(array(
		':type_id'=>$notification_type,
		':payload'=>$payload,
		':title'=>$title,
		':content'=>$content,
		':partner_id'=>$parnter_id,
		':action_enum'=>$action
	));


}

function createPartnerCompanyNotification($company_id, $notification_type, $payload, $action, $title, $content){
	Global $db;

	$company_name_query=$db->prepare("SELECT pc_name  FROM idk_partner_companies WHERE pc_id = $company_id");
	$company_name_query->execute();
	$company_data=$company_name_query->fetch();
	$company_name=$company_data['pc_name'];

	$create_notification_query = $db -> prepare("INSERT INTO idk_partner_notification(notification_type_id, notification_audience, notification_payload, 
																notification_title, notification_content, notification_action)
													VALUES(:type_id,:company,:payload,:title,:content,:action_enum);");

	$create_notification_query->execute(array(
		':type_id'=>$notification_type,
		':company'=>$company_name,
		':payload'=>$payload,
		':title'=>$title,
		':content'=>$content,
		':action_enum'=>$action
	));

}

function createPartnerGlobalNotification($notification_type, $payload, $action, $title, $content){
	Global $db;


	$create_notification_query = $db -> prepare("INSERT INTO idk_partner_notification(notification_type_id, notification_audience, notification_payload, 
																notification_title, notification_content, notification_action)
													VALUES(:type_id,:company,:payload,:title,:content,:action_enum);");

	$create_notification_query->execute(array(
		':type_id'=>$notification_type,
		':payload'=>$payload,
		':title'=>$title,
		':content'=>$content,
		':action_enum'=>$action
	));

}

function getPartnerNotificationTypeId($notification_type){
	Global $db;

	$get_notification_type=$db->prepare("SELECT type_id  FROM idk_partner_personal_notification_types WHERE type_name LIKE '$notification_type'");
	$get_notification_type->execute();
	$notification_id=$get_notification_type->fetch();

	return $notification_id['type_id'];

}
 function createPersonalNotificationPreferences($partner_id){
	Global $db;

	$get_notification_preferences = $db->prepare("SELECT partner_id FROM idk_partner_personal_notifications_preferences WHERE partner_id = :partner_id");
	$get_notification_preferences->execute(array(
		':partner_id'=>$partner_id
	));

	$preferences = $get_notification_preferences->fetch();

	if($preferences['partner_id'] == NULL){
		$create_preferences = $db->prepare("INSERT INTO idk_partner_personal_notifications_preferences (partner_id) VALUES (:partner_id)");
		$create_preferences->execute(array(
			':partner_id'=>$partner_id
		));
	}
 }

 function isLoggedEmployeeRepresentative(){
	Global $db;
	Global $logged_employee_id;

	$sql = $db -> prepare("
		SELECT employee_id
		FROM idk_employees
		WHERE employee_makler_representative_status IN (1,2)
	");

	$sql -> execute();

	while($result = $sql -> fetch()){
		if($result['employee_id'] == $logged_employee_id){
			return 1;
		}
	}
	return 0;
}

function getDefaultReperesentativeEmployeeId(){
	Global $db;

	$sql = $db -> prepare("
		SELECT employee_id
		FROM idk_employees
		WHERE employee_makler_representative_status = 2
	");

	$sql -> execute();
	$return = $sql -> fetch();

	return $return['employee_id'];
}

function getProfilesForNalog($nalog_id){
	Global $db;

	$query_profili = $db->prepare("SELECT *
						FROM idk_nalog_profil
						INNER JOIN idk_profil_kriterij ON idk_nalog_profil.id = idk_profil_kriterij.profil_id
						WHERE nalog_id = :nalog_id AND status = 1 ORDER BY prioritet");
					
	$query_profili->execute(array(':nalog_id' => $nalog_id));
												
	$profili = $query_profili->fetchAll(PDO::FETCH_ASSOC);

	return $profili;
}

function transformLanguageToNumber($level){
	switch($level){
		case "A1":
			$lang_level = 1;
		break;
		case "A2":
			$lang_level = 2;
		break;
		case "B1":
			$lang_level = 3;
		break;
		case "B2":
			$lang_level = 4;
		break;
		case "C1":
			$lang_level = 5;
		break;
		case "C2":
			$lang_level = 6;
		break;
		default:
			$lang_level = 0;
	}

	return $lang_level;
}

function getMaklerInfoByOnesignal($onesingal_id){
	Global $db;

	$sql = "SELECT jp_id, jp_lang FROM idk_jobstep_partners WHERE jp_fcmtoken = :token";
	$stmt = $db->prepare($sql);
	$stmt->execute([':token' => $onesingal_id]);
	$result = $stmt->fetch();
	$data = [];
	if($result){
		$data['partner_id'] = $result['jp_id'];
		$data['lang'] = $result['jp_lang'];
		$data['onesignal'] = $onesingal_id;

		return $data;
	}
}

// Aktivan casting task su Dopuna, Zainteresiran, Pristao, Dolazi, Došao, Nije došao
function getCandidateActiveCastingTask($kandidat_id){
	Global $db;

	$query = $db->prepare("
		SELECT COUNT(1) as hasActiveTask
		FROM idk_task_force
		WHERE tf_candidate_id = :tf_candidate_id AND tf_last_active_task = 1 AND tf_vrsta_id = 1 AND tf_status_id IN (12, 14, 16, 18, 20, 22)
	");

	$query->execute(array(
		':tf_candidate_id' => $kandidat_id
	));

	$row = $query -> fetch();

	return $row["hasActiveTask"];
}

function checkIsCandidateFreeForTransferToNalog($kandidat_id){
	Global $db;

	$candidateReservedForNalog = getNalogIdByCandidateId($kandidat_id);
	$candidateHasActiveTask = getCandidateActiveCastingTask($kandidat_id);
	$candidateStatusPrijaveRedoslijed = getCandidateStatusPrijaveRedoslijed($kandidat_id);

	$result = array();

	$result["status"] = 1;
	$message_candidateReservedForNalog = "";
	$message_candidateHasActiveTask = "";
	if ($candidateReservedForNalog OR ($candidateStatusPrijaveRedoslijed > 2)) {
        $result["status"] = 0;
        $message_candidateReservedForNalog = "Kandidat je rezervisan za nalog.";
    }

	if ($candidateHasActiveTask) {
        $result["status"] = 0;
        $message_candidateHasActiveTask = "Kandidat ima aktivan task.";
    }

	$result["message"] = $message_candidateReservedForNalog.$message_candidateHasActiveTask;

    return $result;
}

function getCallAppointmentSalesForEmployee($employee_id, $type){
	Global $db;
	/*
		type: 
			0 - First call
			1 - Sales
	*/
	$date_now = date("Y-m-d H:i:s");
	$query = $db->prepare("
		SELECT sr_client_id, sr_call_datetime, sr_status
		FROM idk_sales_reminders
		WHERE sr_employee_id = :sr_employee_id AND sr_status = 1 AND sr_type = :sr_type AND sr_call_datetime < '$date_now'
	");

	$query->execute(array(
		':sr_employee_id' => $employee_id, 
		':sr_type' => $type
	));

	// $row = $query -> fetch();
	$row = $query->fetchAll(PDO::FETCH_ASSOC);

	return $row;

}
function getCallAppointmentSales($client_id, $type){
	Global $db;
	/*
		type: 
			0 - First call
			1 - Sales
	*/
	$query = $db->prepare("
		SELECT sr_employee_id, sr_call_datetime, sr_status
		FROM idk_sales_reminders
		WHERE sr_client_id = :sr_client_id AND sr_status = 1 AND sr_type = :sr_type
	");

	$query->execute(array(
		':sr_client_id' => $client_id, 
		':sr_type' => $type
	));

	$row = $query -> fetch();

	return $row;

}

?>
