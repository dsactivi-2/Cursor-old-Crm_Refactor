<?php

include("includes/functions.php");
require_once "dompdf/autoload.inc.php";
include("pdf_generator.php");
include("partnerGutschrift_generator.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/includes/env.php");

use Dompdf\Dompdf;

$form="";

if(isset($_REQUEST["form"])) {
	$form = $_REQUEST["form"];

switch ($form)
{

case "login":

	if(isset($_POST['login_email']))
		$login_email = $_POST['login_email'];
	
	if(isset($_POST['login_password']))
		$login_password = $_POST['login_password'];

	if(isset($_POST['login_rm'])){
		$login_rm = $_POST['login_rm'];
	}else{
		$login_rm = "off";
	}

	$login_query = $db->prepare("
							SELECT employee_id, employee_email, employee_password, employee_key,employee_status, employee_reset_password, employee_last_password_change
							FROM idk_employees
							WHERE employee_email = :employee_email AND employee_status != :employee_status");

	$login_query->execute(array(
					':employee_email' => $login_email,
					':employee_status' => 0));

	$user = $login_query->fetch();
	
	$threeMonthsAgo = date("Y-m-d H:i:s", strtotime('-3 months'));

	if($user['employee_last_password_change'] > $threeMonthsAgo){
		$password_active = true;
	}else{
		$password_active = false;
	}
	
	if($user['employee_reset_password'] == 0 AND $password_active){
		if(md5($login_password) == $user['employee_password']){

			if($login_rm == "on") {
				$month = time() + 60 * 60 * 24 * 30;
				setcookie('idk_session', $user['employee_key'], $month);
			}else{
				$hour = time() + 60 * 60 * 24 * 30;
				setcookie('idk_session', $user['employee_key'], $hour);
			}
	
			//Add to LOGS
			$log_employeeid = $user['employee_id'];
			$log_desc = "Zaposlenik se prijavio";
			$log_date = date('Y-m-d H:i:s');
	
			$log_query = $db->prepare("
							INSERT INTO idk_logs
								(log_employeeid, log_desc, log_date)
							VALUES
								(:log_employeeid, :log_desc, :log_date)");
	
			$log_query->execute(array(
							':log_employeeid' => $log_employeeid,
							':log_desc' => $log_desc,
							':log_date' => $log_date));
			$employee_status = $user['employee_status'];
			if($employee_status == 11) header("Location: " . getSiteURLr() . "dak?page=list_for_dak");
			else header("Location: " . getSiteURLr() . "");
	
		}else{
			header("Location: login/poruka/2");
		}
	}else{
		header("Location: login/poruka/3");
	}
break;

case "logout":

	//Add to LOGS
	$log_desc = "Zaposlenik se odjavio";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_employeeid, log_desc, log_date)
					VALUES
						(:log_employeeid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_employeeid' => $logged_employee_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	unset($_COOKIE['idk_session']);
	setcookie('idk_session', '', time() - 60 * 60 * 24 * 30);

	header("Location: login/poruka/1");

break;

case "reset_password_request":
	$employee_id = $_POST['employee_id'];

	$query_update = $db->prepare("
		UPDATE idk_employees
		SET	employee_reset_password = :employee_reset_password
		WHERE employee_id = :employee_id");

	$query_update->execute(array(
		':employee_id' => $employee_id,
		':employee_reset_password' => 1
		));
	
	//Add to LOGS
	$log_desc = "Zatražio promjenu šifre za: " .getEmployeeFullnameById($employee_id). "";
	$log_type = "0";
	addToLogs($log_desc, $log_type); 

break;

case "user_change_password_request":
	$login_email = $_POST['login_email'];

	$login_query = $db->prepare("
							SELECT employee_id, employee_firstname, employee_lastname
							FROM idk_employees
							WHERE employee_email = :employee_email AND employee_status != :employee_status");

	$login_query->execute(array(
					':employee_email' => $login_email,
					':employee_status' => 0));

	$user_existance = $login_query->rowCount();
	
	if($user_existance > 0){
		$user = $login_query->fetch();
		$employee_id = $user['employee_id'];
		$employee_firstname = $user['employee_firstname'];
		$employee_lastname = $user['employee_lastname'];

		$token = bin2hex(random_bytes(64));
		$employee_reset_token_doe = date("Y-m-d H:i:s");

		$query_update = $db->prepare("
			UPDATE idk_employees
			SET	employee_reset_password = :employee_reset_password, employee_reset_token = :employee_reset_token, employee_reset_token_doe = :employee_reset_token_doe
			WHERE employee_id = :employee_id");

		$query_update->execute(array(
			':employee_id' => $employee_id,
			':employee_reset_password' => 1,
			':employee_reset_token' => $token,
			':employee_reset_token_doe' => $employee_reset_token_doe
			));

		//Send email to user
		$mail_email = $login_email;
		$mail_name = getEmployeeFullnameById($employee_id);
		$mail_subject = "Promjena šifre - JOBSTEP CRM";
		$mail_url = "" . getSiteUrlr() . "/password_reset_page?token=" . $token . "";
		$mail_body = "
						<p>Zatražili ste promjenu šifre. Molimo Vas odite na sljedeći link i generišite novu šifru.</p>
						<p>Link za promjenu šifre: " . $mail_url . "</p>
		";
		$mail_altbody = "
						<p>Zatražili ste promjenu šifre. Molimo Vas odite na sljedeći link i generišite novu šifru.</p>
						<p>Link za promjenu šifre: " . $mail_url . "</p>
		";
		
		sendEmail($mail_email, $mail_name, $mail_subject, $mail_body, $mail_altbody);

		//Add to LOGS
		$log_desc = "Korisnik zatražio promjenu šifre: " .getEmployeeFullnameById($employee_id). "";
		$log_type = "0";
		addToLogs($log_desc, $log_type);
		
		header("Location: change_password_request/poruka/1");
	}else{
		header("Location: change_password_request/poruka/2");
	}

break;

case "change_password":
	$token = $_POST['token'];
	if(isset($_POST['login_password'])){
		$employee_password = MD5($_POST['login_password']);
	}
	
	$login_query = $db->prepare("
							SELECT employee_id, employee_firstname, employee_lastname
							FROM idk_employees
							WHERE employee_reset_token = :employee_reset_token AND employee_status != :employee_status AND employee_reset_token_doe >= DATE_SUB(NOW(), INTERVAL 30 MINUTE)");

	$login_query->execute(array(
					':employee_reset_token' => $token,
					':employee_status' => 0));

	$user_existance = $login_query->rowCount();

	if($user_existance > 0){
		$user = $login_query->fetch();
		$employee_id = $user['employee_id'];
		$employee_firstname = $user['employee_firstname'];
		$employee_lastname = $user['employee_lastname'];

		$query_update = $db->prepare("
			UPDATE idk_employees
			SET	employee_reset_password = :employee_reset_password, 
				employee_reset_token = :employee_reset_token, 
				employee_reset_token_doe = :employee_reset_token_doe, 
				employee_last_password_change = :employee_last_password_change,
				employee_password = :employee_password
			WHERE employee_id = :employee_id");

		$query_update->execute(array(
			':employee_id' => $employee_id,
			':employee_last_password_change' => date("Y-m-d H:i:s"),
			':employee_reset_password' => 0,
			':employee_reset_token' => NULL,
			':employee_reset_token_doe' => NULL,
			':employee_password' => $employee_password
			));
		
		//Add to LOGS
		$log_desc = "Korisnik promjenio šifru: " .getEmployeeFullnameById($employee_id). "";
		$log_type = "0";
		addToLogs($log_desc, $log_type);
		
		header("Location: login/poruka/4");
	}else{
		header("Location: login/poruka/5");
	}

break;

case "add_employees":

	$employee_email = $_POST['employee_email'];

	//Check if user exist
	$check_query = $db->prepare("
							SELECT employee_email
							FROM idk_employees
							WHERE employee_email = :employee_email");

	$check_query->execute(array(
					':employee_email' => $employee_email));

	$number_of_rows = $check_query->rowCount();

	if($number_of_rows == 0){

		$employee_firstname = $_POST['employee_firstname'];
		$employee_lastname = $_POST['employee_lastname'];
		if(!empty($_POST['employee_jmbg'])){ $employee_jmbg = $_POST['employee_jmbg']; }else{ $employee_jmbg = 0; }
		$employee_password = MD5($_POST['employee_password']);
		$employee_key = MD5(rand());
		$employee_color = $_POST['employee_color'];
		$employee_rfid = $_POST['employee_rfid'];
		$employee_position = $_POST['employee_position'];
		$employee_dob = date("Y-m-d", strtotime($_POST['employee_dob']));
		$employee_doe = date("Y-m-d", strtotime($_POST['employee_doe']));
		$employee_phone = $_POST['employee_phone'];
		$employee_address = $_POST['employee_address'];
		$employee_city = $_POST['employee_city'];
		$employee_country = $_POST['employee_country'];
		$employee_info = $_POST['employee_info'];
		$employee_status_array = $_POST['employee_status'];
		$employee_status = implode(',', $employee_status_array);		
		
		$employee_supervizor_array = $_POST['employee_supervizor'];
		$employee_supervizor = implode(',', $employee_supervizor_array);
		$employee_odjel = $_POST['employee_odjel'];
		$employee_poslovnica = $_POST['employee_poslovnica'];
		if($employee_poslovnica == "")
			$employee_poslovnica = null;
		$employee_team = $_POST['employee_team'];
		$employee_inote = "";
		
		$employe_viciUsername = $_POST['employe_vicidial_user'];
		$employe_viciPassword = $_POST['employe_vicidial_pass'];
		
		if(empty($employe_viciUsername) || empty($employe_viciPassword)){
			$employe_viciUsername = "0";
			$employe_viciPassword = "0";
		}
		//Upload and save employee_image
		if($_FILES['employee_image']['size'] !== 0) {
			$employee_image = $_FILES['employee_image'];

			//File properties
			$file_name = $employee_image['name'];
			$file_tmp = $employee_image['tmp_name'];
			$file_size = $employee_image['size'];
			$file_error = $employee_image['error'];

			//File extension
			$file_ext = explode('.', $file_name);
			$file_ext = strtolower(end($file_ext));

			$allowed = array('jpg', 'png');

			if(in_array($file_ext, $allowed)) {

				$employee_image_final = uniqid() . '.' . $file_ext;
				$file_destination = 'files/employees/' . $employee_image_final;

				if(move_uploaded_file($file_tmp, $file_destination)) {

					$path_to_image_directory = "files/employees/";
					$final_width_of_image = 660;

					if(preg_match('/[.](jpg)$/', $employee_image_final)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $employee_image_final);
					} else if (preg_match('/[.](png)$/', $employee_image_final)) {
						$im = imagecreatefrompng($path_to_image_directory . $employee_image_final);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);
					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));
					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);
					imagejpeg($nm, $path_to_image_directory . $employee_image_final);

				}
			}
		}else{
			$employee_image_final = "none";
		}

		//Add user to db
		$query = $db->prepare("
						INSERT INTO idk_employees
							(employee_firstname, employee_lastname, employee_jmbg, employee_email, employee_password, employee_key, employee_color, employee_rfid, employee_position, employee_dob, employee_doe, employee_address, employee_city, employee_country, employee_info, employee_status, employee_supervizor, employee_odjel, employee_poslovnica, employee_image, employee_inote, employee_team, employee_viciuser, employee_vicipass, employee_last_password_change)
						VALUES
							(:employee_firstname, :employee_lastname, :employee_jmbg, :employee_email, :employee_password, :employee_key, :employee_color, :employee_rfid, :employee_position, :employee_dob, :employee_doe, :employee_address, :employee_city, :employee_country, :employee_info, :employee_status, :employee_supervizor, :employee_odjel, :employee_poslovnica, :employee_image, :employee_inote, :employee_team, :employee_viciuser, :employee_vicipass, :employee_last_password_change)");

		$query->execute(array(
					':employee_firstname' => $employee_firstname,
					':employee_lastname' => $employee_lastname,
					':employee_jmbg' => $employee_jmbg,
					':employee_email' => $employee_email,
					':employee_password' => $employee_password,
					':employee_key' => $employee_key,
					':employee_color' => $employee_color,
					':employee_rfid' => $employee_rfid,
					':employee_position' => $employee_position,
					':employee_dob' => $employee_dob,
					':employee_doe' => $employee_doe,
					':employee_address' => $employee_address,
					':employee_city' => $employee_city,
					':employee_country' => $employee_country,
					':employee_info' => $employee_info,
					':employee_status' => $employee_status,
					':employee_supervizor' => $employee_supervizor,
					':employee_odjel' => $employee_odjel,
					':employee_poslovnica' => $employee_poslovnica,
					':employee_image' => $employee_image_final,
					':employee_inote' => $employee_inote,
					':employee_team' => $employee_team,
					':employee_viciuser' => $employe_viciUsername,
					':employee_last_password_change' => date("Y-m-d H:i:s"),
					':employee_vicipass' => $employe_viciPassword
					));
					
		//Get last ID
		$ei_employeeid = $db->lastInsertId();

		//Add primary phone
		if (!empty($_POST['employee_phone'])) {

			$ei_group = 1;
			$ei_title = "Telefon";
			$ei_data = $_POST['employee_phone'];
			$ei_primary = 1;

			$query_phone = $db->prepare("
							INSERT INTO idk_employees_info
								(ei_group, ei_title, ei_data, ei_primary, ei_employeeid)
							VALUES
								(:ei_group, :ei_title, :ei_data, :ei_primary, :ei_employeeid)");

			$query_phone->execute(array(
							':ei_group' => $ei_group,
							':ei_title' => $ei_title,
							':ei_data' => $ei_data,
							':ei_primary' => $ei_primary,
							':ei_employeeid' => $ei_employeeid));
		}

		//Add to LOGS
		$log_desc = "Dodao novog zaposlenika: " . $employee_firstname . " " . $employee_lastname . " ";
		$log_date = date('Y-m-d H:i:s');

			$log_query = $db->prepare("
							INSERT INTO idk_logs
								(log_employeeid, log_desc, log_date)
								VALUES
								(:log_employeeid, :log_desc, :log_date)");
			$log_query->execute(array(
							':log_employeeid' => $logged_employee_id,
							':log_desc' => $log_desc,
							':log_date' => $log_date));

		header("Location: employees?page=list&mess=1");

	}else{
		header("Location: employees?page=list&mess=2");
	}

break;

case "kreiraj_nalog":

	$kompanija_id 							= $_POST['kompanija_id'];
	$broj_naloga 							= $_POST['broj_naloga'];
	$nalog_naziv 							= $_POST['nalog_naziv'];
	$nalog_opis 							= $_POST['nalog_opis'];
	$nalog_partner_provizija 				= $_POST['nalog_partner_provizija'];
	$nalog_dospijece		 				= $_POST['nalog_dospijece'];
	if($nalog_partner_provizija == "")
		$nalog_partner_provizija = null;
	$nalog_datum_potpisa_naloga 			= date('Y-m-d', strtotime($_POST['datum_potpisa_ugovora']));
	$nalog_ugovor 							= $_POST['nalog_ugovor'];
	$nalog_firma_fakturisanja 				= $_POST['nalog_firma_fakturisanja'];
	$nalog_nostrifikacija 					= $_POST['nalog_nostrifikacija'];
	$nalog_provizija_nostrifikacija 		= $_POST['nalog_nostrifikacija_provizija'];
	$nalog_nacin_nostrifikacija 			= $_POST["nalog_nostrifikacija_nacin"];
	$nalog_broj_rata_nostrifikacija 		= $_POST['nalog_nostrifikacija_broj_rata'];

	$nalog_financije						= $_POST['nalog_financije'];
	$nalog_potrebno_kandidata 				= $_POST['nalog_potrebno_kandidata'];
	$nalog_provizija 						= $_POST['nalog_provizija'];
	$nalog_provizija_po_plati 				= $_POST['nalog_provizija_po_plati'];
	$nalog_ima_avans 						= $_POST['nalog_ima_avans'];
	$nalog_avans 							= $_POST['nalog_avans'];
	$nalog_broj_rata 						= $_POST['nalog_broj_rata'];
	if($nalog_broj_rata == "")
		$nalog_broj_rata = null;
	$nalog_poslodavac_trazi_jezik 			= $_POST['nalog_poslodavac_trazi_jezik'];
	$nalog_poslodavac_koristi_pp 			= $_POST['nalog_poslodavac_koristi_pp'];
	$nalog_projekti 						= $_POST['nalog_projekti'];
	$nalog_status 							= 1;

	$flagFinancije = 0; 

	if ( $nalog_financije == 1 ) {
		$nalog_potrebno_kandidata = $nalog_potrebno_kandidata;
		$nalog_provizija = $nalog_provizija;
		$nalog_provizija_po_plati = null;
		$nalog_ima_avans = null; 
		$nalog_avans = null; 
		$nalog_broj_rata = $nalog_broj_rata;
		$flagFinancije = 1;
	} else if ( $nalog_financije == 2 ) {
		$nalog_potrebno_kandidata = $nalog_potrebno_kandidata;
		$nalog_provizija = $nalog_provizija;
		$nalog_provizija_po_plati = null;
		$nalog_ima_avans = $nalog_ima_avans;
		if ( $nalog_ima_avans == 1 ) {
			$nalog_avans = $nalog_avans; 
		} else {
			$nalog_avans = null;
		}
		$nalog_broj_rata = $nalog_broj_rata;
		$flagFinancije = 1;
	} else if ( $nalog_financije == 3) {
		$nalog_potrebno_kandidata = $nalog_potrebno_kandidata;
		$nalog_provizija = null;
		$nalog_provizija_po_plati = $nalog_provizija_po_plati;
		$nalog_ima_avans = null; 
		$nalog_avans = null; 
		$nalog_broj_rata = $nalog_broj_rata;
		$flagFinancije = 1;
	} else {
		$flagFinancije = 0;
	}

	if ( $nalog_nostrifikacija == 1 ){
		$nalog_provizija_nostrifikacija = $nalog_provizija_nostrifikacija; 
		$nalog_nacin_nostrifikacija = $nalog_nacin_nostrifikacija;
		if ($nalog_nacin_nostrifikacija == 2 ) {
			$nalog_broj_rata_nostrifikacija = $nalog_broj_rata_nostrifikacija;
		} else {
			$nalog_broj_rata_nostrifikacija = null;
		}
	} else {
		$nalog_provizija_nostrifikacija = null; 
		$nalog_nacin_nostrifikacija = 0;
		$nalog_broj_rata_nostrifikacija = null;
	} 
	/*Check partners*/
		$js_partner_id = 0;
		$resultForPartner = getCompanyHasJSPartnerArrayR($kompanija_id);
		if ($resultForPartner["status"] == 1){ 
			$resultOrders = getJSPartnerHasNalogArrayR($resultForPartner["parnter_id"], $kompanija_id);
			if ($resultOrders["status"] == 0) { 
				$js_partner_id = $resultForPartner["parnter_id"]; 
			}
		}
		unset($resultForPartner);
		unset($resultOrders); 
	/*Check partners*/
	
	/*
	echo "kompanija_id :".$kompanija_id."<br>";
	echo "broj_naloga :".$broj_naloga."<br>";
	echo "nalog_naziv :".$nalog_naziv."<br>";
	echo "nalog_opis :".$nalog_opis."<br>";
	echo "nalog_partner_provizija :".$nalog_partner_provizija."<br>";
	echo "nalog_datum_potpisa_naloga :".$nalog_datum_potpisa_naloga."<br>";
	echo "nalog_ugovor :".$nalog_ugovor."<br>";
	echo "nalog_financije :".$nalog_financije."<br> <br>";
	echo "flagFinancije :".$flagFinancije."<br>";
	echo "nalog_potrebno_kandidata :".$nalog_potrebno_kandidata."<br>";
	echo "nalog_provizija :".$nalog_provizija."<br>";
	echo "nalog_provizija_po_plati :".$nalog_provizija_po_plati."<br>";
	echo "nalog_ima_avans :".$nalog_ima_avans."<br>";
	echo "nalog_avans :".$nalog_avans."<br>";
	echo "nalog_broj_rata :".$nalog_broj_rata."<br>";
	echo "nalog_projekti :".$nalog_projekti."<br>";
	echo "nalog_status :".$nalog_status."<br>";
	*/

	$query_nalog = $db->prepare("
			INSERT INTO idk_nalozi
				(kompanija_id, employee_id, nalog_broj, nalog_naziv, nalog_opis, nalog_status, nalog_ugovor, nalog_potrebno_kandidata, nalog_provizija, nalog_provizija_po_plati, nalog_procenat_avansa, nalog_broj_rata, nalog_datum_potpisa_naloga, nalog_financije, nalog_partner_provizija, nalog_placa_nostrifikaciju, nalog_nacin_nostrifikacije, nalog_provizija_nostrifikacija, nalog_broj_rata_nostrifikacija, nalog_firma_fakturisanja, nalog_dospijece, nalog_js_partner_id, nalog_poslodavac_trazi_jezik, nalog_poslodavac_koristi_pp)
			VALUES
				(:kompanija_id, :employee_id, :nalog_broj, :nalog_naziv, :nalog_opis, :nalog_status, :nalog_ugovor, :nalog_potrebno_kandidata, :nalog_provizija, :nalog_provizija_po_plati, :nalog_procenat_avansa, :nalog_broj_rata, :nalog_datum_potpisa_naloga, :nalog_financije, :nalog_partner_provizija, :nalog_placa_nostrifikaciju, :nalog_nacin_nostrifikacije, :nalog_provizija_nostrifikacija, :nalog_broj_rata_nostrifikacija, :nalog_firma_fakturisanja, :nalog_dospijece, :nalog_js_partner_id, :nalog_poslodavac_trazi_jezik, :nalog_poslodavac_koristi_pp)");

	$query_nalog->execute(array(
			':kompanija_id' => $kompanija_id,
			':employee_id' => $logged_employee_id,
			':nalog_broj' => $broj_naloga,
			':nalog_naziv' => $nalog_naziv,
			':nalog_opis' => $nalog_opis,
			':nalog_ugovor' => $nalog_ugovor,
			':nalog_potrebno_kandidata' => $nalog_potrebno_kandidata,
			':nalog_provizija' => $nalog_provizija,
			':nalog_provizija_po_plati' => $nalog_provizija_po_plati,
			':nalog_procenat_avansa' => $nalog_avans, 
			':nalog_broj_rata' => $nalog_broj_rata,
			':nalog_status' => $nalog_status,
			':nalog_datum_potpisa_naloga' => $nalog_datum_potpisa_naloga,
			':nalog_financije' => $nalog_financije,
			':nalog_partner_provizija' => $nalog_partner_provizija, 
			':nalog_placa_nostrifikaciju' => $nalog_nostrifikacija, 
			':nalog_nacin_nostrifikacije' => $nalog_nacin_nostrifikacija,
			':nalog_provizija_nostrifikacija' => $nalog_provizija_nostrifikacija, 
			':nalog_broj_rata_nostrifikacija' => $nalog_broj_rata_nostrifikacija,
			':nalog_firma_fakturisanja' => $nalog_firma_fakturisanja,
			':nalog_dospijece' => $nalog_dospijece, 
			':nalog_js_partner_id' => $js_partner_id, 
			':nalog_poslodavac_trazi_jezik' => $nalog_poslodavac_trazi_jezik,
			':nalog_poslodavac_koristi_pp' => $nalog_poslodavac_koristi_pp
			));


	//ADD NALOG LOG
	// var_dump($nalog_partner_provizija);
	// var_dump($query_nalog->errorInfo());
	// exit();
	$nalog_id = $db->lastInsertId();
	
	$query_nalog_log = $db->prepare("
			INSERT INTO idk_nalozi_log
				(n_log_nalogid, n_log_employeeid, n_log_status)
			VALUES
				(:n_log_nalogid, :n_log_employeeid, :n_log_status)");

	$query_nalog_log->execute(array(
			':n_log_nalogid' => $nalog_id,
			':n_log_employeeid' => $logged_employee_id,
			':n_log_status' => $nalog_status));

	if($nalog_projekti == 'Da'){

		//CREATE PROJECTS
		//1 - PRIJAVA
		$project_name = $broj_naloga.' - Prijave';
		$project_plannedhours = 0;
		$project_desc = 'Projekat PRIJAVE za nalog: '.$broj_naloga.' | '.$nalog_naziv;
		$project_companyid = $kompanija_id;
		$project_pmanagerid = $logged_employee_id;
		$project_datetime = date('Y-m-d H:i:s');
		$project_status = 1;
		$project_nalogid = $nalog_id;

		$query = $db->prepare("
						INSERT INTO idk_projects
							(project_name, project_plannedhours, project_desc, project_companyid, project_employeeid, project_pmanagerid, project_datetime, project_status, project_nalogid)
						VALUES
							(:project_name, :project_plannedhours, :project_desc, :project_companyid, :project_employeeid, :project_pmanagerid, :project_datetime, :project_status, :project_nalogid)");

		$query->execute(array(
					':project_name' => $project_name,
					':project_plannedhours' => $project_plannedhours,
					':project_desc' => $project_desc,
					':project_companyid' => $project_companyid,
					':project_employeeid' => $logged_employee_id,
					':project_pmanagerid' => $project_pmanagerid,
					':project_datetime' => $project_datetime,
					':project_status' => $project_status,
					':project_nalogid' => $project_nalogid));
					
		//1a - EU kandidati
		$project_name = $broj_naloga.' - EU kandidati';
		$project_plannedhours = 0;
		$project_desc = 'Projekat EU kandidati za nalog: '.$broj_naloga.' | '.$nalog_naziv;
		$project_companyid = $kompanija_id;
		$project_pmanagerid = $logged_employee_id;
		$project_datetime = date('Y-m-d H:i:s');
		$project_status = 1;
		$project_nalogid = $nalog_id;
		
		// EU kandidati su osobe koje su se prijavile i oznacile da su iz EU - I prebacivaju se u projekat EU kandidati nakon prijave
		$project_eukandidati = 1;

		$query = $db->prepare("
						INSERT INTO idk_projects
							(project_name, project_plannedhours, project_desc, project_companyid, project_employeeid, project_pmanagerid, project_datetime, project_status, project_nalogid, project_eukandidati)
						VALUES
							(:project_name, :project_plannedhours, :project_desc, :project_companyid, :project_employeeid, :project_pmanagerid, :project_datetime, :project_status, :project_nalogid, :project_eukandidati)");

		$query->execute(array(
					':project_name' => $project_name,
					':project_plannedhours' => $project_plannedhours,
					':project_desc' => $project_desc,
					':project_companyid' => $project_companyid,
					':project_employeeid' => $logged_employee_id,
					':project_pmanagerid' => $project_pmanagerid,
					':project_datetime' => $project_datetime,
					':project_status' => $project_status,
					':project_nalogid' => $project_nalogid,
					':project_eukandidati' => 1
					));					

		//2 - U OBRADI
		$project_name = $broj_naloga.' - U obradi';
		$project_plannedhours = 0;
		$project_desc = 'Projekat U OBRADI za nalog: '.$broj_naloga.' | '.$nalog_naziv;
		$project_companyid = $kompanija_id;
		$project_pmanagerid = $logged_employee_id;
		$project_datetime = date('Y-m-d H:i:s');
		$project_status = 1;
		$project_nalogid = $nalog_id;

		$query = $db->prepare("
						INSERT INTO idk_projects
							(project_name, project_plannedhours, project_desc, project_companyid, project_employeeid, project_pmanagerid, project_datetime, project_status, project_nalogid)
						VALUES
							(:project_name, :project_plannedhours, :project_desc, :project_companyid, :project_employeeid, :project_pmanagerid, :project_datetime, :project_status, :project_nalogid)");

		$query->execute(array(
					':project_name' => $project_name,
					':project_plannedhours' => $project_plannedhours,
					':project_desc' => $project_desc,
					':project_companyid' => $project_companyid,
					':project_employeeid' => $logged_employee_id,
					':project_pmanagerid' => $project_pmanagerid,
					':project_datetime' => $project_datetime,
					':project_status' => $project_status,
					':project_nalogid' => $project_nalogid));

		//3 - OBRAĐENO
		$project_name = $broj_naloga.' - Obrađeno';
		$project_plannedhours = 0;
		$project_desc = 'Projekat OBRAĐENO za nalog: '.$broj_naloga.' | '.$nalog_naziv;
		$project_companyid = $kompanija_id;
		$project_pmanagerid = $logged_employee_id;
		$project_datetime = date('Y-m-d H:i:s');
		$project_status = 1;
		$project_nalogid = $nalog_id;

		$query = $db->prepare("
						INSERT INTO idk_projects
							(project_name, project_plannedhours, project_desc, project_companyid, project_employeeid, project_pmanagerid, project_datetime, project_status, project_nalogid)
						VALUES
							(:project_name, :project_plannedhours, :project_desc, :project_companyid, :project_employeeid, :project_pmanagerid, :project_datetime, :project_status, :project_nalogid)");

		$query->execute(array(
					':project_name' => $project_name,
					':project_plannedhours' => $project_plannedhours,
					':project_desc' => $project_desc,
					':project_companyid' => $project_companyid,
					':project_employeeid' => $logged_employee_id,
					':project_pmanagerid' => $project_pmanagerid,
					':project_datetime' => $project_datetime,
					':project_status' => $project_status,
					':project_nalogid' => $project_nalogid));

		//4 - CASTING
		$project_name = $broj_naloga.' - Casting';
		$project_plannedhours = 0;
		$project_desc = 'Projekat CASTING za nalog: '.$broj_naloga.' | '.$nalog_naziv;
		$project_companyid = $kompanija_id;
		$project_pmanagerid = $logged_employee_id;
		$project_datetime = date('Y-m-d H:i:s');
		$project_status = 1;
		$project_nalogid = $nalog_id;

		$query = $db->prepare("
						INSERT INTO idk_projects
							(project_name, project_plannedhours, project_desc, project_companyid, project_employeeid, project_pmanagerid, project_datetime, project_status, project_nalogid)
						VALUES
							(:project_name, :project_plannedhours, :project_desc, :project_companyid, :project_employeeid, :project_pmanagerid, :project_datetime, :project_status, :project_nalogid)");

		$query->execute(array(
					':project_name' => $project_name,
					':project_plannedhours' => $project_plannedhours,
					':project_desc' => $project_desc,
					':project_companyid' => $project_companyid,
					':project_employeeid' => $logged_employee_id,
					':project_pmanagerid' => $project_pmanagerid,
					':project_datetime' => $project_datetime,
					':project_status' => $project_status,
					':project_nalogid' => $project_nalogid));
		
		//5 - INTERVJU
		$project_name = $broj_naloga.' - Intervju';
		$project_plannedhours = 0;
		$project_desc = 'Projekat INTERVJU za nalog: '.$broj_naloga.' | '.$nalog_naziv;
		$project_companyid = $kompanija_id;
		$project_pmanagerid = $logged_employee_id;
		$project_datetime = date('Y-m-d H:i:s');
		$project_status = 1;
		$project_nalogid = $nalog_id;

		$query = $db->prepare("
						INSERT INTO idk_projects
							(project_name, project_plannedhours, project_desc, project_companyid, project_employeeid, project_pmanagerid, project_datetime, project_status, project_nalogid)
						VALUES
							(:project_name, :project_plannedhours, :project_desc, :project_companyid, :project_employeeid, :project_pmanagerid, :project_datetime, :project_status, :project_nalogid)");

		$query->execute(array(
					':project_name' => $project_name,
					':project_plannedhours' => $project_plannedhours,
					':project_desc' => $project_desc,
					':project_companyid' => $project_companyid,
					':project_employeeid' => $logged_employee_id,
					':project_pmanagerid' => $project_pmanagerid,
					':project_datetime' => $project_datetime,
					':project_status' => $project_status,
					':project_nalogid' => $project_nalogid));
		
		//6 - NIJE DOŠAO NA RAZGOVORO
		$project_name = $broj_naloga.' - Nije došao na razgovor';
		$project_plannedhours = 0;
		$project_desc = 'Projekat NIJE DOŠAO NA RAZGOVOR za nalog: '.$broj_naloga.' | '.$nalog_naziv;
		$project_companyid = $kompanija_id;
		$project_pmanagerid = $logged_employee_id;
		$project_datetime = date('Y-m-d H:i:s');
		$project_status = 1;
		$project_nalogid = $nalog_id;

		$query = $db->prepare("
						INSERT INTO idk_projects
							(project_name, project_plannedhours, project_desc, project_companyid, project_employeeid, project_pmanagerid, project_datetime, project_status, project_nalogid)
						VALUES
							(:project_name, :project_plannedhours, :project_desc, :project_companyid, :project_employeeid, :project_pmanagerid, :project_datetime, :project_status, :project_nalogid)");

		$query->execute(array(
					':project_name' => $project_name,
					':project_plannedhours' => $project_plannedhours,
					':project_desc' => $project_desc,
					':project_companyid' => $project_companyid,
					':project_employeeid' => $logged_employee_id,
					':project_pmanagerid' => $project_pmanagerid,
					':project_datetime' => $project_datetime,
					':project_status' => $project_status,
					':project_nalogid' => $project_nalogid));
		
		//7 - UGOVOR
		$project_name = $broj_naloga.' - Ugovor';
		$project_plannedhours = 0;
		$project_desc = 'Projekat UGOVOR za nalog: '.$broj_naloga.' | '.$nalog_naziv;
		$project_companyid = $kompanija_id;
		$project_pmanagerid = $logged_employee_id;
		$project_datetime = date('Y-m-d H:i:s');
		$project_status = 1;
		$project_nalogid = $nalog_id;

		$query = $db->prepare("
						INSERT INTO idk_projects
							(project_name, project_plannedhours, project_desc, project_companyid, project_employeeid, project_pmanagerid, project_datetime, project_status, project_nalogid)
						VALUES
							(:project_name, :project_plannedhours, :project_desc, :project_companyid, :project_employeeid, :project_pmanagerid, :project_datetime, :project_status, :project_nalogid)");

		$query->execute(array(
					':project_name' => $project_name,
					':project_plannedhours' => $project_plannedhours,
					':project_desc' => $project_desc,
					':project_companyid' => $project_companyid,
					':project_employeeid' => $logged_employee_id,
					':project_pmanagerid' => $project_pmanagerid,
					':project_datetime' => $project_datetime,
					':project_status' => $project_status,
					':project_nalogid' => $project_nalogid));

		//8 - KANDIDATI POČELI SA RADOM
		$project_name = $broj_naloga.' - Kandidati počeli sa radom';
		$project_plannedhours = 0;
		$project_desc = 'Projekat Kandidati počeli sa radom za nalog: '.$broj_naloga.' | '.$nalog_naziv;
		$project_companyid = $kompanija_id;
		$project_pmanagerid = $logged_employee_id;
		$project_datetime = date('Y-m-d H:i:s');
		$project_status = 1;
		$project_nalogid = $nalog_id;

		$query = $db->prepare("
						INSERT INTO idk_projects
							(project_name, project_plannedhours, project_desc, project_companyid, project_employeeid, project_pmanagerid, project_datetime, project_status, project_nalogid)
						VALUES
							(:project_name, :project_plannedhours, :project_desc, :project_companyid, :project_employeeid, :project_pmanagerid, :project_datetime, :project_status, :project_nalogid)");

		$query->execute(array(
					':project_name' => $project_name,
					':project_plannedhours' => $project_plannedhours,
					':project_desc' => $project_desc,
					':project_companyid' => $project_companyid,
					':project_employeeid' => $logged_employee_id,
					':project_pmanagerid' => $project_pmanagerid,
					':project_datetime' => $project_datetime,
					':project_status' => $project_status,
					':project_nalogid' => $project_nalogid));

		//9 - ODBIJEN
		$project_name = $broj_naloga.' - Odbijen';
		$project_plannedhours = 0;
		$project_desc = 'Projekat ODBIJEN za nalog: '.$broj_naloga.' | '.$nalog_naziv;
		$project_companyid = $kompanija_id;
		$project_pmanagerid = $logged_employee_id;
		$project_datetime = date('Y-m-d H:i:s');
		$project_status = 1;
		$project_nalogid = $nalog_id;

		$query = $db->prepare("
						INSERT INTO idk_projects
							(project_name, project_plannedhours, project_desc, project_companyid, project_employeeid, project_pmanagerid, project_datetime, project_status, project_nalogid)
						VALUES
							(:project_name, :project_plannedhours, :project_desc, :project_companyid, :project_employeeid, :project_pmanagerid, :project_datetime, :project_status, :project_nalogid)");

		$query->execute(array(
					':project_name' => $project_name,
					':project_plannedhours' => $project_plannedhours,
					':project_desc' => $project_desc,
					':project_companyid' => $project_companyid,
					':project_employeeid' => $logged_employee_id,
					':project_pmanagerid' => $project_pmanagerid,
					':project_datetime' => $project_datetime,
					':project_status' => $project_status,
					':project_nalogid' => $project_nalogid));
		
		//10 - ODUSTAO
		$project_name = $broj_naloga.' - Odustao';
		$project_plannedhours = 0;
		$project_desc = 'Projekat ODUSTAO za nalog: '.$broj_naloga.' | '.$nalog_naziv;
		$project_companyid = $kompanija_id;
		$project_pmanagerid = $logged_employee_id;
		$project_datetime = date('Y-m-d H:i:s');
		$project_status = 1;
		$project_nalogid = $nalog_id;

		$query = $db->prepare("
						INSERT INTO idk_projects
							(project_name, project_plannedhours, project_desc, project_companyid, project_employeeid, project_pmanagerid, project_datetime, project_status, project_nalogid)
						VALUES
							(:project_name, :project_plannedhours, :project_desc, :project_companyid, :project_employeeid, :project_pmanagerid, :project_datetime, :project_status, :project_nalogid)");

		$query->execute(array(
					':project_name' => $project_name,
					':project_plannedhours' => $project_plannedhours,
					':project_desc' => $project_desc,
					':project_companyid' => $project_companyid,
					':project_employeeid' => $logged_employee_id,
					':project_pmanagerid' => $project_pmanagerid,
					':project_datetime' => $project_datetime,
					':project_status' => $project_status,
					':project_nalogid' => $project_nalogid));

		//11 - ZAVRSENI KANDIDATI
		$project_name = $broj_naloga.' - Završeni kandidati';
		$project_plannedhours = 0;
		$project_desc = 'Projekat ZAVRŠENI KANDIDATI za nalog: '.$broj_naloga.' | '.$nalog_naziv;
		$project_companyid = $kompanija_id;
		$project_pmanagerid = $logged_employee_id;
		$project_datetime = date('Y-m-d H:i:s');
		$project_status = 1;
		$project_nalogid = $nalog_id;

		$query = $db->prepare("
						INSERT INTO idk_projects
							(project_name, project_plannedhours, project_desc, project_companyid, project_employeeid, project_pmanagerid, project_datetime, project_status, project_nalogid)
						VALUES
							(:project_name, :project_plannedhours, :project_desc, :project_companyid, :project_employeeid, :project_pmanagerid, :project_datetime, :project_status, :project_nalogid)");

		$query->execute(array(
					':project_name' => $project_name,
					':project_plannedhours' => $project_plannedhours,
					':project_desc' => $project_desc,
					':project_companyid' => $project_companyid,
					':project_employeeid' => $logged_employee_id,
					':project_pmanagerid' => $project_pmanagerid,
					':project_datetime' => $project_datetime,
					':project_status' => $project_status,
					':project_nalogid' => $project_nalogid));
		
		//12 - BOT ISPUNJAVA USLOVE
		$project_name = $broj_naloga." - BOT - ispunjava uslove";
		$project_plannedhours = 0;
		$project_companyid = $kompanija_id;
		$project_pmanagerid = $logged_employee_id;
		$project_datetime = date('Y-m-d H:i:s');
		$project_status = 1;
		$project_nalogid = $nalog_id;

		$query = $db->prepare("
						INSERT INTO idk_projects
							(project_name, project_companyid, project_employeeid, project_pmanagerid, project_datetime, project_status, project_nalogid)
						VALUES
							(:project_name, :project_companyid, :project_employeeid, :project_pmanagerid, :project_datetime, :project_status, :project_nalogid)");

		$query->execute(array(
					':project_name' => $project_name,
					':project_companyid' => $project_companyid,
					':project_employeeid' => $logged_employee_id,
					':project_pmanagerid' => $project_pmanagerid,
					':project_datetime' => $project_datetime,
					':project_status' => $project_status,
					':project_nalogid' => $project_nalogid));
		
		//13 - BOT NE ISPUNJAVA USLOVE
		$project_name = $broj_naloga." - BOT - ne ispunjava uslove";
		$project_plannedhours = 0;
		$project_companyid = $kompanija_id;
		$project_pmanagerid = $logged_employee_id;
		$project_datetime = date('Y-m-d H:i:s');
		$project_status = 1;
		$project_nalogid = $nalog_id;

		$query = $db->prepare("
						INSERT INTO idk_projects
							(project_name, project_companyid, project_employeeid, project_pmanagerid, project_datetime, project_status, project_nalogid)
						VALUES
							(:project_name, :project_companyid, :project_employeeid, :project_pmanagerid, :project_datetime, :project_status, :project_nalogid)");

		$query->execute(array(
			':project_name' => $project_name,
			':project_companyid' => $project_companyid,
			':project_employeeid' => $logged_employee_id,
			':project_pmanagerid' => $project_pmanagerid,
			':project_datetime' => $project_datetime,
			':project_status' => $project_status,
			':project_nalogid' => $project_nalogid
		));

		// 14 - BAZA - ODGOVARA ZA NALOG
		$project_name 		  = $broj_naloga . " - Baza - odgovara za nalog";
		$project_plannedhours = 0;
		$project_companyid    = $kompanija_id;
		$project_pmanagerid   = $logged_employee_id;
		$project_datetime     = date('Y-m-d H:i:s');
		$project_status       = 1;
		$project_nalogid      = $nalog_id;

		$query = $db->prepare("
			INSERT INTO idk_projects
				(project_name, project_companyid, project_employeeid, project_pmanagerid, project_datetime, project_status, project_nalogid)
			VALUES
				(:project_name, :project_companyid, :project_employeeid, :project_pmanagerid, :project_datetime, :project_status, :project_nalogid)
		");

		$query->execute(array(
			':project_name' 	  => $project_name,
			':project_companyid'  => $project_companyid,
			':project_employeeid' => $logged_employee_id,
			':project_pmanagerid' => $project_pmanagerid,
			':project_datetime'   => $project_datetime,
			':project_status'     => $project_status,
			':project_nalogid'    => $project_nalogid
		));

		// 15 - NIJE ZAINTERESIRAN
		$project_name 		  = $broj_naloga . " - Nije zainteresiran";
		$project_plannedhours = 0;
		$project_companyid    = $kompanija_id;
		$project_pmanagerid   = $logged_employee_id;
		$project_datetime     = date('Y-m-d H:i:s');
		$project_status       = 1;
		$project_nalogid      = $nalog_id;

		$query = $db->prepare("
			INSERT INTO idk_projects
				(project_name, project_companyid, project_employeeid, project_pmanagerid, project_datetime, project_status, project_nalogid)
			VALUES
				(:project_name, :project_companyid, :project_employeeid, :project_pmanagerid, :project_datetime, :project_status, :project_nalogid)
		");

		$query->execute(array(
			':project_name' 	  => $project_name,
			':project_companyid'  => $project_companyid,
			':project_employeeid' => $logged_employee_id,
			':project_pmanagerid' => $project_pmanagerid,
			':project_datetime'   => $project_datetime,
			':project_status'     => $project_status,
			':project_nalogid'    => $project_nalogid
		));

		// 16 - POGREŠAN BROJ
		$project_name 		  = $broj_naloga . " - Pogrešan broj";
		$project_plannedhours = 0;
		$project_companyid    = $kompanija_id;
		$project_pmanagerid   = $logged_employee_id;
		$project_datetime     = date('Y-m-d H:i:s');
		$project_status       = 1;
		$project_nalogid      = $nalog_id;

		$query = $db->prepare("
			INSERT INTO idk_projects
				(project_name, project_companyid, project_employeeid, project_pmanagerid, project_datetime, project_status, project_nalogid)
			VALUES
				(:project_name, :project_companyid, :project_employeeid, :project_pmanagerid, :project_datetime, :project_status, :project_nalogid)
		");

		$query->execute(array(
			':project_name' 	  => $project_name,
			':project_companyid'  => $project_companyid,
			':project_employeeid' => $logged_employee_id,
			':project_pmanagerid' => $project_pmanagerid,
			':project_datetime'   => $project_datetime,
			':project_status'     => $project_status,
			':project_nalogid'    => $project_nalogid
		));

		// 17 - NEDOSTUPAN
		$project_name 		  = $broj_naloga . " - Nedostupan";
		$project_plannedhours = 0;
		$project_companyid    = $kompanija_id;
		$project_pmanagerid   = $logged_employee_id;
		$project_datetime     = date('Y-m-d H:i:s');
		$project_status       = 1;
		$project_nalogid      = $nalog_id;

		$query = $db->prepare("
			INSERT INTO idk_projects
				(project_name, project_companyid, project_employeeid, project_pmanagerid, project_datetime, project_status, project_nalogid)
			VALUES
				(:project_name, :project_companyid, :project_employeeid, :project_pmanagerid, :project_datetime, :project_status, :project_nalogid)
		");

		$query->execute(array(
			':project_name' 	  => $project_name,
			':project_companyid'  => $project_companyid,
			':project_employeeid' => $logged_employee_id,
			':project_pmanagerid' => $project_pmanagerid,
			':project_datetime'   => $project_datetime,
			':project_status'     => $project_status,
			':project_nalogid'    => $project_nalogid
		));

		// 18 - NE ISPUNJAVA USLOVE ZA NALOG
		$project_name 		  = $broj_naloga . " - Ne ispunjava uslove za nalog";
		$project_plannedhours = 0;
		$project_companyid    = $kompanija_id;
		$project_pmanagerid   = $logged_employee_id;
		$project_datetime     = date('Y-m-d H:i:s');
		$project_status       = 1;
		$project_nalogid      = $nalog_id;

		$query = $db->prepare("
			INSERT INTO idk_projects
				(project_name, project_companyid, project_employeeid, project_pmanagerid, project_datetime, project_status, project_nalogid)
			VALUES
				(:project_name, :project_companyid, :project_employeeid, :project_pmanagerid, :project_datetime, :project_status, :project_nalogid)
		");

		$query->execute(array(
			':project_name' 	  => $project_name,
			':project_companyid'  => $project_companyid,
			':project_employeeid' => $logged_employee_id,
			':project_pmanagerid' => $project_pmanagerid,
			':project_datetime'   => $project_datetime,
			':project_status'     => $project_status,
			':project_nalogid'    => $project_nalogid
		));
					
	}
	$get_partner_by_company = $db->prepare("SELECT jp_id, jp_lang, jp_fcmtoken FROM idk_jobstep_partners JOIN idk_companies ON idk_jobstep_partners.jp_id = idk_companies.js_partner_id WHERE company_id = :company_id");
	$get_partner_by_company->execute(array(
		':company_id' => $kompanija_id
	));
	$result = $get_partner_by_company->fetch();
	$partner_id = $result['jp_id'];

	$notification_type = getPartnerNotificationTypeId("ORDERS");
	$send_notifications = shouldSendPersonalNotification($partner_id,$notification_type);

	if($js_partner_id != 0 && $send_notifications == 1){
		$onesignal_id = $result['jp_fcmtoken'];
		if($result['jp_lang'] == 'en'){
			$title = "Recruitment order";
			$content = "The company you added created a Recruitment Order";
			sendNotification($onesignal_id, $title, $content);
		} else {
			$title = "Rekrutierungsauftrag";
			$content = "Das von Ihnen hinzugefügte Unternehmen hat einen Rekrutierungsauftrag erstellt";
			sendNotification($onesignal_id, $title, $content);
		}
		$payload = '{"order_id": "'.$nalog_id.'"}';
		$notification_title		='{ "en":"Recruitment order", "de":"Rekrutierungsauftrag" }';
		$notification_content 	='{ "en":"The company you added created a Recruitment Order", "de":"Das von Ihnen hinzugefügte Unternehmen hat einen Rekrutierungsauftrag erstellt"}';
		$action="NAVIGATE_TO_JOB";
		createPartnerPersonalNotification($partner_id, $notification_type, $payload, $action, $notification_title, $notification_content);
	}

	$mess_nostrifikacija = "";

	if ( $nalog_nostrifikacija == 1 AND $nalog_nacin_nostrifikacija == 2){

		$result = insertNalogFinancijeZaNostrifikacijuMjesecnoR($nalog_id); 
		$mess_nostrifikacija = "&mess1=".$result."";

	}

	if($nalog_financije == 1 OR $nalog_financije == 3) {

		header("Location: nalozi?page=add_rate&id=".$nalog_id."&nr_rata=1&datum_pu=".$nalog_datum_potpisa_naloga."".$mess_nostrifikacija."");

	} else if ($nalog_financije == 2) {

		$result = insertNalogFinancijeZaMjesecnoPlacanjeR($nalog_id); 

		header("Location: nalozi?page=add_blokove_prijave&id=".$nalog_id."&mess=".$result."".$mess_nostrifikacija."");

	} else {

		header("Location: nalozi?page=add_blokove_prijave&id=".$nalog_id."".$mess_nostrifikacija."");
		
	}

break;

case "edit_employees":

	if( !empty($_POST['employee_password']) ) {

		$employee_id = $_POST['employee_id'];
		$employee_firstname = $_POST['employee_firstname'];
		$employee_lastname = $_POST['employee_lastname'];
		if(!empty($_POST['employee_jmbg'])){ $employee_jmbg = $_POST['employee_jmbg']; }else{ $employee_jmbg = 0; }
		$employee_email = $_POST['employee_email'];
		$employee_password = MD5($_POST['employee_password']);
		$employee_color = $_POST['employee_color'];
		$employee_rfid = $_POST['employee_rfid'];
		$employee_position = $_POST['employee_position'];
		$employee_dob = date("Y-m-d", strtotime($_POST['employee_dob']));
		$employee_doe = date("Y-m-d", strtotime($_POST['employee_doe']));
		$employee_address = $_POST['employee_address'];
		$employee_city = $_POST['employee_city'];
		$employee_country = $_POST['employee_country'];
		$employee_info = $_POST['employee_info'];
		
		
		$employe_viciUsername = $_POST['employe_vicidial_user'];
		$employe_viciPassword = $_POST['employe_vicidial_pass'];
		
		if(empty($employe_viciUsername) || empty($employe_viciPassword)){
			$employe_viciUsername = "0";
			$employe_viciPassword = "0";
		}
		
		// NOVI STATUSE I IMA NA 2 MJESTA ZBOG GORNJEG USLOVA
		
		$employee_status_array = $_POST['employee_status'];
		$employee_status = implode(',', $employee_status_array);		
		
		$employee_supervizor_array = $_POST['employee_supervizor'];
		$employee_supervizor = implode(',', $employee_supervizor_array);
		
		
		$employee_odjel = $_POST['employee_odjel'];
		$employee_poslovnica = $_POST['employee_poslovnica'];
		if($employee_poslovnica == "")
			$employee_poslovnica = null;
		$employee_warehouse = $_POST['employee_warehouse'];
		$employee_team = $_POST['employee_team'];
		if($employee_warehouse == "on") $employee_warehouse = 1;
		else $employee_warehouse = 0;
		
		//Upload and save employee_image
		if($_FILES['employee_image']['size'] !== 0) {

			//Delete old image
			$del_employee_img_query = $db->prepare("
										SELECT employee_image
										FROM idk_employees
										WHERE employee_id = :employee_id");

			$del_employee_img_query->execute(array(
									':employee_id' => $employee_id));

			$del_employee_img = $del_employee_img_query->fetch();

				$employee_image = $del_employee_img['employee_image'];

			if($employee_image == "" OR $employee_image =="none" OR $employee_image =="none.jpg"){}else{
				unlink("files/employees/" . $employee_image);
			}

			$employee_image = $_FILES['employee_image'];

			//File properties
			$file_name = $employee_image['name'];
			$file_tmp = $employee_image['tmp_name'];
			$file_size = $employee_image['size'];
			$file_error = $employee_image['error'];

			//File extension
			$file_ext = explode('.', $file_name);
			$file_ext = strtolower(end($file_ext));

			$allowed = array('jpg', 'png');

			if(in_array($file_ext, $allowed)) {

				$employee_image_final = uniqid() . '.' . $file_ext;
				$file_destination = 'files/employees/' . $employee_image_final;

				if(move_uploaded_file($file_tmp, $file_destination)) {

					$path_to_image_directory = "files/" . getSubdomainr() . "_files/employees/";
					$final_width_of_image = 660;

					if(preg_match('/[.](jpg)$/', $employee_image_final)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $employee_image_final);
					} else if (preg_match('/[.](png)$/', $employee_image_final)) {
						$im = imagecreatefrompng($path_to_image_directory . $employee_image_final);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);
					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));
					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);
					imagejpeg($nm, $path_to_image_directory . $employee_image_final);

				}
			}
		}else{
			$employee_image_final = $_POST['employee_image_url'];
		}

			$query = $db->prepare("
							UPDATE idk_employees
							SET	employee_firstname = :employee_firstname, employee_lastname = :employee_lastname, employee_jmbg = :employee_jmbg, employee_email = :employee_email, employee_password = :employee_password, employee_color = :employee_color, employee_rfid = :employee_rfid, employee_position = :employee_position, employee_dob = :employee_dob, employee_doe = :employee_doe, employee_address = :employee_address, employee_city = :employee_city, employee_country = :employee_country, employee_info = :employee_info, employee_status = :employee_status,  employee_supervizor = :employee_supervizor, employee_odjel = :employee_odjel, employee_image = :employee_image, employee_warehouse = :employee_warehouse, employee_team = :employee_team, employee_poslovnica = :employee_poslovnica, employee_viciuser = :employee_viciuser, employee_vicipass = :employee_vicipass, employee_last_password_change = :employee_last_password_change
							WHERE employee_id = :employee_id");

			$query->execute(array(
					':employee_firstname' => $employee_firstname,
					':employee_lastname' => $employee_lastname,
					':employee_jmbg' => $employee_jmbg,
					':employee_email' => $employee_email,
					':employee_password' => $employee_password,
					':employee_color' => $employee_color,
					':employee_rfid' => $employee_rfid,
					':employee_position' => $employee_position,
					':employee_dob' => $employee_dob,
					':employee_doe' => $employee_doe,
					':employee_address' => $employee_address,
					':employee_city' => $employee_city,
					':employee_country' => $employee_country,
					':employee_info' => $employee_info,
					':employee_status' => $employee_status,
					':employee_supervizor' => $employee_supervizor,
					':employee_odjel' => $employee_odjel,
					':employee_image' => $employee_image_final,
					':employee_warehouse' => $employee_warehouse,
					':employee_team' => $employee_team,
					':employee_poslovnica' => $employee_poslovnica,
					':employee_viciuser' => $employe_viciUsername,
					':employee_vicipass' => $employe_viciPassword,
					':employee_last_password_change' => date("Y-m-d H:i:s"),
					':employee_id' => $employee_id));

		
		//Add to LOGS
		$log_desc = "Uredio profil zaposlenika: " . $employee_firstname . " " . $employee_lastname . " ";
		$log_date = date('Y-m-d H:i:s');

		$log_query = $db->prepare("
						INSERT INTO idk_logs
							(log_employeeid, log_desc, log_date)
						VALUES
							(:log_employeeid, :log_desc, :log_date)");

		$log_query->execute(array(
						':log_employeeid' => $logged_employee_id,
						':log_desc' => $log_desc,
						':log_date' => $log_date));

		header("Location: employees?page=list&mess=3");

	}else{

		$employee_id = $_POST['employee_id'];
		$employee_firstname = $_POST['employee_firstname'];
		$employee_lastname = $_POST['employee_lastname'];
		if(!empty($_POST['employee_jmbg'])){ $employee_jmbg = $_POST['employee_jmbg']; }else{ $employee_jmbg = 0; }
		$employee_email = $_POST['employee_email'];
		$employee_color = $_POST['employee_color'];
		$employee_rfid = $_POST['employee_rfid'];
		$employee_position = $_POST['employee_position'];
		$employee_dob = date("Y-m-d", strtotime($_POST['employee_dob']));
		$employee_doe = date("Y-m-d", strtotime($_POST['employee_doe']));
		$employee_address = $_POST['employee_address'];
		$employee_city = $_POST['employee_city'];
		$employee_country = $_POST['employee_country'];
		$employee_info = $_POST['employee_info'];
		
		
		$employe_viciUsername = $_POST['employe_vicidial_user'];
		$employe_viciPassword = $_POST['employe_vicidial_pass'];
		
		if(empty($employe_viciUsername) || empty($employe_viciPassword)){
			$employe_viciUsername = "0";
			$employe_viciPassword = "0";
		}
		// NOVI STATUSE I IMA NA 2 MJESTA ZBOG GORNJEG USLOVA
		
		$employee_status_array = $_POST['employee_status'];
		$employee_status = implode(',', $employee_status_array);		
		
		$employee_supervizor_array = $_POST['employee_supervizor'];
		$employee_supervizor = implode(',', $employee_supervizor_array);
		
		$employee_odjel = $_POST['employee_odjel'];
		$employee_poslovnica = $_POST['employee_poslovnica'];
		if($employee_poslovnica == "")
			$employee_poslovnica = null;
		$employee_warehouse = $_POST['employee_warehouse'];
		$employee_team = $_POST['employee_team'];
		if($employee_warehouse == "on") $employee_warehouse = 1;
		else $employee_warehouse = 0;
		
		//Upload and save employee_image
		if($_FILES['employee_image']['size'] !== 0) {

			//Delete old image
			$del_employee_img_query = $db->prepare("
										SELECT employee_image
										FROM idk_employees
										WHERE employee_id = :employee_id");

			$del_employee_img_query->execute(array(
									':employee_id' => $employee_id));

			$del_employee_img = $del_employee_img_query->fetch();

				$employee_image = $del_employee_img['employee_image'];

			if($employee_image == ""){}else{
				unlink("files/" . getSubdomainr() . "_files/employees/" . $employee_image);
			}

			$employee_image = $_FILES['employee_image'];

			//File properties
			$file_name = $employee_image['name'];
			$file_tmp = $employee_image['tmp_name'];
			$file_size = $employee_image['size'];
			$file_error = $employee_image['error'];

			//File extension
			$file_ext = explode('.', $file_name);
			$file_ext = strtolower(end($file_ext));

			$allowed = array('jpg', 'png');

			if(in_array($file_ext, $allowed)) {

				$employee_image_final = uniqid() . '.' . $file_ext;
				$file_destination = 'files/employees/' . $employee_image_final;

				if(move_uploaded_file($file_tmp, $file_destination)) {

					$path_to_image_directory = "files/" . getSubdomainr() . "_files/employees/";
					$final_width_of_image = 660;

					if(preg_match('/[.](jpg)$/', $employee_image_final)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $employee_image_final);
					} else if (preg_match('/[.](png)$/', $employee_image_final)) {
						$im = imagecreatefrompng($path_to_image_directory . $employee_image_final);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);
					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));
					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);
					imagejpeg($nm, $path_to_image_directory . $employee_image_final);

				}
			}
		}else{
			$employee_image_final = $_POST['employee_image_url'];
		}

			$query = $db->prepare("
							UPDATE idk_employees
							SET	employee_firstname = :employee_firstname, employee_lastname = :employee_lastname, employee_jmbg = :employee_jmbg, employee_email = :employee_email, employee_color = :employee_color, employee_rfid = :employee_rfid, employee_position = :employee_position, employee_dob = :employee_dob, employee_doe = :employee_doe, employee_address = :employee_address, employee_city = :employee_city, employee_country = :employee_country, employee_info = :employee_info, employee_status = :employee_status,  employee_supervizor = :employee_supervizor, employee_odjel = :employee_odjel, employee_image = :employee_image, employee_warehouse = :employee_warehouse, employee_team = :employee_team, employee_poslovnica = :employee_poslovnica, employee_viciuser = :employee_viciuser, employee_vicipass = :employee_vicipass
							WHERE employee_id = :employee_id");

			$query->execute(array(
					':employee_firstname' => $employee_firstname,
					':employee_lastname' => $employee_lastname,
					':employee_jmbg' => $employee_jmbg,
					':employee_email' => $employee_email,
					':employee_color' => $employee_color,
					':employee_rfid' => $employee_rfid,
					':employee_position' => $employee_position,
					':employee_dob' => $employee_dob,
					':employee_doe' => $employee_doe,
					':employee_address' => $employee_address,
					':employee_city' => $employee_city,
					':employee_country' => $employee_country,
					':employee_info' => $employee_info,
					':employee_status' => $employee_status,
					':employee_supervizor' => $employee_supervizor,
					':employee_odjel' => $employee_odjel,
					':employee_image' => $employee_image_final,
					':employee_warehouse' => $employee_warehouse,
					':employee_team' => $employee_team,
					':employee_poslovnica' => $employee_poslovnica,
					':employee_viciuser' => $employe_viciUsername,
					':employee_vicipass' => $employe_viciPassword,
					':employee_id' => $employee_id));

		//Add to LOGS
		$log_desc = "Uredio profil zaposlenika: " . $employee_firstname . " " . $employee_lastname . " ";
		$log_date = date('Y-m-d H:i:s');

		$log_query = $db->prepare("
						INSERT INTO idk_logs
							(log_employeeid, log_desc, log_date)
						VALUES
							(:log_employeeid, :log_desc, :log_date)");

		$log_query->execute(array(
						':log_employeeid' => $logged_employee_id,
						':log_desc' => $log_desc,
						':log_date' => $log_date));

		header("Location: employees?page=list&mess=3");
	}

break;

case "edit_profile": 

	if( !empty($_POST['employee_password']) ) {

		$employee_id = $_POST['employee_id'];
		$employee_firstname = $_POST['employee_firstname'];
		$employee_lastname = $_POST['employee_lastname'];
		if(!empty($_POST['employee_jmbg'])){ $employee_jmbg = $_POST['employee_jmbg']; }else{ $employee_jmbg = 0; }
		$employee_color = $_POST['employee_color'];
		$employee_email = $_POST['employee_email'];
		$employee_password = MD5($_POST['employee_password']);
		$employee_dob = date("Y-m-d", strtotime($_POST['employee_dob']));
		$employee_doe = date("Y-m-d", strtotime($_POST['employee_doe']));
		$employee_address = $_POST['employee_address'];
		$employee_city = $_POST['employee_city'];
		$employee_country = $_POST['employee_country'];
		$employee_info = $_POST['employee_info'];
		$employe_viciUsername = $_POST['employe_vicidial_user'];
		$employe_viciPassword = $_POST['employe_vicidial_pass'];
		//Upload and save employee_image
		if($_FILES['employee_image']['size'] !== 0) { 

			//Delete old image
			$del_employee_img_query = $db->prepare("
										SELECT employee_image
										FROM idk_employees
										WHERE employee_id = :employee_id");

			$del_employee_img_query->execute(array(
									':employee_id' => $employee_id));

			$del_employee_img = $del_employee_img_query->fetch();

				$employee_image = $del_employee_img['employee_image'];

			if($employee_image == ""){}else{
				unlink("files/" . getSubdomainr() . "_files/employees/" . $employee_image);
			}

			$employee_image = $_FILES['employee_image'];

			//File properties
			$file_name = $employee_image['name'];
			$file_tmp = $employee_image['tmp_name'];
			$file_size = $employee_image['size'];
			$file_error = $employee_image['error'];

			//File extension
			$file_ext = explode('.', $file_name);
			$file_ext = strtolower(end($file_ext));

			$allowed = array('jpg', 'png');

			if(in_array($file_ext, $allowed)) {

				$employee_image_final = uniqid() . '.' . $file_ext;
				$file_destination = "files/" . getSubdomainr() . "_files/employees/" . $employee_image_final;

				if(move_uploaded_file($file_tmp, $file_destination)) {

					$path_to_image_directory = "files/" . getSubdomainr() . "_files/employees/";
					$final_width_of_image = 660;

					if(preg_match('/[.](jpg)$/', $employee_image_final)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $employee_image_final);
					} else if (preg_match('/[.](png)$/', $employee_image_final)) {
						$im = imagecreatefrompng($path_to_image_directory . $employee_image_final);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);
					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));
					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);
					imagejpeg($nm, $path_to_image_directory . $employee_image_final);

				}
			}
		}else{
			$employee_image_final = $_POST['employee_image_url'];
		}

			$query = $db->prepare("
							UPDATE idk_employees
							SET	employee_firstname = :employee_firstname, employee_lastname = :employee_lastname, employee_jmbg = :employee_jmbg, employee_color = :employee_color, employee_email = :employee_email, employee_password = :employee_password, employee_dob = :employee_dob, employee_doe = :employee_doe, employee_address = :employee_address, employee_city = :employee_city, employee_country = :employee_country, employee_info = :employee_info,employee_viciuser = :employee_viciuser, employee_vicipass = :employee_vicipass, employee_image = :employee_image, employee_last_password_change = :employee_last_password_change
							WHERE employee_id = :employee_id");

			$query->execute(array(
					':employee_firstname' => $employee_firstname,
					':employee_lastname' => $employee_lastname,
					':employee_jmbg' => $employee_jmbg,
					':employee_color' => $employee_color,
					':employee_email' => $employee_email,
					':employee_password' => $employee_password,
					':employee_dob' => $employee_dob,
					':employee_doe' => $employee_doe,
					':employee_address' => $employee_address,
					':employee_city' => $employee_city,
					':employee_country' => $employee_country,
					':employee_info' => $employee_info,
					':employee_viciuser' => $employe_viciUsername,
					':employee_vicipass' => $employe_viciPassword,
					':employee_image' => $employee_image_final,
					':employee_last_password_change' => date("Y-m-d H:i:s"),
					':employee_id' => $employee_id));

		//Add to LOGS
		$log_desc = "Uredio osobni profil: " . $employee_firstname . " " . $employee_lastname . " ";
		$log_date = date('Y-m-d H:i:s');

		$log_query = $db->prepare("
						INSERT INTO idk_logs
							(log_employeeid, log_desc, log_date)
						VALUES
							(:log_employeeid, :log_desc, :log_date)");

		$log_query->execute(array(
						':log_employeeid' => $logged_employee_id,
						':log_desc' => $log_desc,
						':log_date' => $log_date));

		header("Location: employees?page=edit_profile&mess=1");

	}else{

		$employee_id = $_POST['employee_id'];
		$employee_firstname = $_POST['employee_firstname'];
		$employee_lastname = $_POST['employee_lastname'];
		if(!empty($_POST['employee_jmbg'])){ $employee_jmbg = $_POST['employee_jmbg']; }else{ $employee_jmbg = 0; }
		$employee_color = $_POST['employee_color'];
		$employee_email = $_POST['employee_email'];
		$employee_dob = date("Y-m-d", strtotime($_POST['employee_dob']));
		$employee_doe = date("Y-m-d", strtotime($_POST['employee_doe']));
		$employee_address = $_POST['employee_address'];
		$employee_city = $_POST['employee_city'];
		$employee_country = $_POST['employee_country'];
		$employee_info = $_POST['employee_info'];
		
		$employe_viciUsername = $_POST['employe_vicidial_user'];
		$employe_viciPassword = $_POST['employe_vicidial_pass'];
		//Upload and save employee_image
		if($_FILES['employee_image']['size'] !== 0) {

			//Delete old image
			$del_employee_img_query = $db->prepare("
										SELECT employee_image
										FROM idk_employees
										WHERE employee_id = :employee_id");

			$del_employee_img_query->execute(array(
									':employee_id' => $employee_id));

			$del_employee_img = $del_employee_img_query->fetch();

				$employee_image = $del_employee_img['employee_image'];

			if($employee_image == ""){}else{
				unlink("files/" . getSubdomainr() . "_files/employees/" . $employee_image);
			}

			$employee_image = $_FILES['employee_image'];

			//File properties
			$file_name = $employee_image['name'];
			$file_tmp = $employee_image['tmp_name'];
			$file_size = $employee_image['size'];
			$file_error = $employee_image['error'];

			//File extension
			$file_ext = explode('.', $file_name);
			$file_ext = strtolower(end($file_ext));

			$allowed = array('jpg', 'png');

			if(in_array($file_ext, $allowed)) {

				$employee_image_final = uniqid() . '.' . $file_ext;
				$file_destination = "files/" . getSubdomainr() . "_files/employees/" . $employee_image_final;

				if(move_uploaded_file($file_tmp, $file_destination)) {

					$path_to_image_directory = "files/" . getSubdomainr() . "_files/employees/";
					$final_width_of_image = 660;

					if(preg_match('/[.](jpg)$/', $employee_image_final)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $employee_image_final);
					} else if (preg_match('/[.](png)$/', $employee_image_final)) {
						$im = imagecreatefrompng($path_to_image_directory . $employee_image_final);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);
					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));
					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);
					imagejpeg($nm, $path_to_image_directory . $employee_image_final);

				}
			}
		}else{
			$employee_image_final = $_POST['employee_image_url'];
		}

			$query = $db->prepare("
							UPDATE idk_employees
							SET	employee_firstname = :employee_firstname, employee_lastname = :employee_lastname, employee_jmbg = :employee_jmbg, employee_color = :employee_color, employee_email = :employee_email, employee_dob = :employee_dob, employee_doe = :employee_doe, employee_address = :employee_address, employee_city = :employee_city, employee_country = :employee_country, employee_info = :employee_info,employee_viciuser = :employee_viciuser, employee_vicipass = :employee_vicipass, employee_image = :employee_image
							WHERE employee_id = :employee_id");

			$query->execute(array(
					':employee_firstname' => $employee_firstname,
					':employee_lastname' => $employee_lastname,
					':employee_jmbg' => $employee_jmbg,
					':employee_color' => $employee_color,
					':employee_email' => $employee_email,
					':employee_dob' => $employee_dob,
					':employee_doe' => $employee_doe,
					':employee_address' => $employee_address,
					':employee_city' => $employee_city,
					':employee_country' => $employee_country,
					':employee_info' => $employee_info,
					':employee_viciuser' => $employe_viciUsername,
					':employee_vicipass' => $employe_viciPassword,
					':employee_image' => $employee_image_final,
					':employee_id' => $employee_id));

		//Add to LOGS
		$log_desc = "Uredio osobni profil: " . $employee_firstname . " " . $employee_lastname . " ";
		$log_date = date('Y-m-d H:i:s');

		$log_query = $db->prepare("
						INSERT INTO idk_logs
							(log_employeeid, log_desc, log_date)
						VALUES
							(:log_employeeid, :log_desc, :log_date)");

		$log_query->execute(array(
						':log_employeeid' => $logged_employee_id,
						':log_desc' => $log_desc,
						':log_date' => $log_date));

		header("Location: employees?page=edit_profile&mess=1");
	}

break;

case "add_employee_note":

	$note_txt = $_POST['note_txt'];
	$note_datetime = date('Y-m-d H:i:s');
	$note_group = 1;
	$note_dataid = $_POST['note_dataid'];

	$query = $db->prepare("
					INSERT INTO idk_notes
						(note_txt, note_datetime, note_group, note_dataid, note_employeeid)
					VALUES
						(:note_txt, :note_datetime, :note_group, :note_dataid, :note_employeeid)");

	$query->execute(array(
				':note_txt' => $note_txt,
				':note_datetime' => $note_datetime,
				':note_group' => $note_group,
				':note_dataid' => $note_dataid,
				':note_employeeid' => $logged_employee_id));
				
	$query_employee = $db->prepare("
		SELECT employee_firstname, employee_lastname
		FROM idk_employees
		WHERE employee_id = :employee_id");
					
	$query_employee->execute(array(
				':employee_id' => $note_dataid));
				
	$row = $query_employee->fetch();

	$employee_firstname = $row['employee_firstname'];
	$employee_lastname = $row['employee_lastname'];
				
	//Add to LOGS
	$log_desc = "Dodao novu bilješku za zaposlenika: " .$employee_firstname. " " .$employee_lastname. "";
	$log_type = "3";
	addToLogs($log_desc, $log_type); //Log za biljeske - $log_type = 3

	header("Location: employees?page=open&id=$note_dataid&mess=1");


break;

case "add_employee_doc":

	$document_dataid = $_POST['document_dataid'];

	//Upload document
	$document_file = $_FILES['document_file'];

	//File properties
	$file_name = $document_file['name'];
	$file_tmp = $document_file['tmp_name'];

	//File extension
	$file_ext = explode('.', $file_name);
	$file_ext = strtolower(end($file_ext));

	$allowed = array('jpg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png');

	if(in_array($file_ext, $allowed)) {

		$file_name_new = uniqid() . '.' . $file_ext;
		$file_destination = "files/files/employees/" . $file_name_new;

		if(move_uploaded_file($file_tmp, $file_destination)){}
	}

	$document_name = $_POST['document_name'];
	$document_desc = $_POST['document_desc'];
	$document_datetime = date('Y-m-d H:i:s');
	$document_group = 1;

	$query = $db->prepare("
					INSERT INTO idk_documents
						(document_name, document_desc, document_file, document_icon, document_datetime, document_group, document_dataid, document_employeeid)
					VALUES
						(:document_name, :document_desc, :document_file, :document_icon, :document_datetime, :document_group, :document_dataid, :document_employeeid)");

	$query->execute(array(
					':document_name' => $document_name,
					':document_desc' => $document_desc,
					':document_file' => $file_name_new,
					':document_icon' => $file_ext,
					':document_datetime' => $document_datetime,
					':document_group' => $document_group,
					':document_dataid' => $document_dataid,
					':document_employeeid' => $logged_employee_id));

	//Add to LOGS
	$log_desc = "Dodao novi dokument: " . $document_name . " ";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_employeeid, log_desc, log_date)
					VALUES
						(:log_employeeid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_employeeid' => $logged_employee_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: employees?page=open&id=$document_dataid&mess=2");

break;

case "save_employee_inote":

	$employee_id = $_POST['employee_id'];
	$employee_inote = base64_encode($_POST['employee_inote']);

	//Get
	$query_select = $db->prepare("
							SELECT employee_firstname, employee_lastname
							FROM idk_employees
							WHERE employee_id = :employee_id");

	$query_select->execute(array(
						':employee_id' => $employee_id));

	$row_select = $query_select->fetch();

	$employee_firstname = $row_select['employee_firstname'];
	$employee_lastname = $row_select['employee_lastname'];

	//Save
	$query = $db->prepare("
					UPDATE idk_employees
					SET	employee_inote = :employee_inote
					WHERE employee_id = :employee_id");

	$query->execute(array(
				':employee_inote' => $employee_inote,
				':employee_id' => $employee_id));

	//Add to LOGS
	$log_desc = "Snimio važne napomene za zaposlenika: " . $employee_firstname . " " . $employee_lastname . " ";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_employeeid, log_desc, log_date)
					VALUES
						(:log_employeeid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_employeeid' => $logged_employee_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: employees?page=open&id=$employee_id&mess=5");

break;

case "add_contact_type":

	$otherdata_data = $_POST['otherdata_data'];
	$otherdata_group = 2;

	$query = $db->prepare("
					INSERT INTO idk_otherdata
						(otherdata_data, otherdata_group)
					VALUES
						(:otherdata_data, :otherdata_group)");

	$query->execute(array(
				':otherdata_data' => $otherdata_data,
				':otherdata_group' => $otherdata_group));

	header("Location: settings?page=contact_type&mess=1");


break;

case "add_employee_position":

	$otherdata_data = $_POST['otherdata_data'];
	$otherdata_group = 1;

	$query = $db->prepare("
					INSERT INTO idk_otherdata
						(otherdata_data, otherdata_group)
					VALUES
						(:otherdata_data, :otherdata_group)");

	$query->execute(array(
				':otherdata_data' => $otherdata_data,
				':otherdata_group' => $otherdata_group));

	header("Location: settings?page=employee_position&mess=1");


break;

case "add_company_type":

	$otherdata_data = $_POST['otherdata_data'];
	$otherdata_group = 3;

	$query = $db->prepare("
					INSERT INTO idk_otherdata
						(otherdata_data, otherdata_group)
					VALUES
						(:otherdata_data, :otherdata_group)");

	$query->execute(array(
				':otherdata_data' => $otherdata_data,
				':otherdata_group' => $otherdata_group));

	header("Location: settings?page=company_type&mess=1");


break;

case "delete_contact_type":

	$otherdata_id = $_GET['id'];

	$query = $db->prepare("
					DELETE FROM idk_otherdata
					WHERE otherdata_id = :otherdata_id");

	$query->execute(array(
					':otherdata_id' => $otherdata_id));

	header("Location: settings?page=contact_type&mess=2");


break;

case "delete_employee_position":

	$otherdata_id = $_GET['id'];

	$query = $db->prepare("
					DELETE FROM idk_otherdata
					WHERE otherdata_id = :otherdata_id");

	$query->execute(array(
					':otherdata_id' => $otherdata_id));

	header("Location: settings?page=employee_position&mess=2");

break;

case "delete_company_type":

	$otherdata_id = $_GET['id'];

	$query = $db->prepare("
					DELETE FROM idk_otherdata
					WHERE otherdata_id = :otherdata_id");

	$query->execute(array(
					':otherdata_id' => $otherdata_id));

	header("Location: settings?page=company_type&mess=2");


break;

case "add_contact":

		$contact_firstname = $_POST['contact_firstname'];
		$contact_lastname = $_POST['contact_lastname'];
		$contact_companyid = $_POST['contact_companyid'];
		$contact_nickname = $_POST['contact_nickname'];
		if(empty($_POST['contact_dob'])){ $contact_dob = NULL; }else{ $contact_dob = date("Y-m-d", strtotime($_POST['contact_dob'])); }
		$contact_address = $_POST['contact_address'];
		$contact_zipcode = ($_POST['contact_zipcode'] != '') ? $_POST['contact_zipcode'] : null;
		$contact_city = $_POST['contact_city'];
		$contact_state = $_POST['contact_state'];
		$contact_country = $_POST['contact_country'];
		$contact_info = $_POST['contact_info'];
		$contact_type = $_POST['contact_type'];
		$contact_datetime = date('Y-m-d H:i:s');
		$contact_status = 1;

		//Upload and save contact_image
		if($_FILES['contact_image']['size'] !== 0) {
			$contact_image = $_FILES['contact_image'];

			//File properties
			$file_name = $contact_image['name'];
			$file_tmp = $contact_image['tmp_name'];
			$file_size = $contact_image['size'];
			$file_error = $contact_image['error'];

			//File extension
			$file_ext = explode('.', $file_name);
			$file_ext = strtolower(end($file_ext));

			$allowed = array('jpg', 'jpeg', 'png');

			if(in_array($file_ext, $allowed)) {

				$contact_image_final = uniqid() . '.' . $file_ext;
				$file_destination = 'files/contacts/' . $contact_image_final;

				if(move_uploaded_file($file_tmp, $file_destination)) {

					$path_to_image_directory = "files/" . getSubdomainr() . "_files/contacts/";
					$final_width_of_image = 660;

					if(preg_match('/[.](jpg)$/', $contact_image_final)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $contact_image_final);
					} else if (preg_match('/[.](jpeg)$/', $contact_image_final)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $contact_image_final);
					} else if (preg_match('/[.](png)$/', $contact_image_final)) {
						$im = imagecreatefrompng($path_to_image_directory . $contact_image_final);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);
					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));
					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);
					imagejpeg($nm, $path_to_image_directory . $contact_image_final);

				}
			}
		}else{
			$contact_image_final = "none";
		}

		//Add user to db
		$query = $db->prepare("
						INSERT INTO idk_contacts
							(contact_firstname, contact_lastname, contact_nickname, contact_dob, contact_address, contact_zipcode, contact_city, contact_state, contact_country, contact_info, contact_image, contact_type, contact_companyid, contact_datetime, contact_status)
						VALUES
							(:contact_firstname, :contact_lastname, :contact_nickname, :contact_dob, :contact_address, :contact_zipcode, :contact_city, :contact_state, :contact_country, :contact_info, :contact_image, :contact_type, :contact_companyid, :contact_datetime, :contact_status)");

		$query->execute(array(
					':contact_firstname' => $contact_firstname,
					':contact_lastname' => $contact_lastname,
					':contact_nickname' => $contact_nickname,
					':contact_dob' => $contact_dob,
					':contact_address' => $contact_address,
					':contact_zipcode' => $contact_zipcode,
					':contact_city' => $contact_city,
					':contact_state' => $contact_state,
					':contact_country' => $contact_country,
					':contact_info' => $contact_info,
					':contact_image' => $contact_image_final,
					':contact_type' => $contact_type,
					':contact_companyid' => $contact_companyid,
					':contact_datetime' => $contact_datetime,
					':contact_status' => $contact_status));

		//Get last ID
		$ci_contactid = $db->lastInsertId();

		//Add primary phone
		if (!empty($_POST['contact_phone'])) {

			$ci_group = 1;
			$ci_title = "Telefon";
			$ci_data = $_POST['contact_phone'];
			$ci_primary = 1;

			$query_phone = $db->prepare("
							INSERT INTO idk_contacts_info
								(ci_group, ci_title, ci_data, ci_primary, ci_contactid)
							VALUES
								(:ci_group, :ci_title, :ci_data, :ci_primary, :ci_contactid)");

			$query_phone->execute(array(
							':ci_group' => $ci_group,
							':ci_title' => $ci_title,
							':ci_data' => $ci_data,
							':ci_primary' => $ci_primary,
							':ci_contactid' => $ci_contactid));
		}

		//Add primary email
		if (!empty($_POST['contact_email'])) {

			$ci_group = 2;
			$ci_title = "E-mail";
			$ci_data = $_POST['contact_email'];
			$ci_primary = 1;

			$query_phone = $db->prepare("
							INSERT INTO idk_contacts_info
								(ci_group, ci_title, ci_data, ci_primary, ci_contactid)
							VALUES
								(:ci_group, :ci_title, :ci_data, :ci_primary, :ci_contactid)");

			$query_phone->execute(array(
							':ci_group' => $ci_group,
							':ci_title' => $ci_title,
							':ci_data' => $ci_data,
							':ci_primary' => $ci_primary,
							':ci_contactid' => $ci_contactid));
		}

		//Add to LOGS
		$log_desc = "Dodao novi kontakt: " . $contact_firstname . " " . $employee_lastname . " ";
		$log_date = date('Y-m-d H:i:s');

		$log_query = $db->prepare("
						INSERT INTO idk_logs
							(log_employeeid, log_desc, log_date)
						VALUES
							(:log_employeeid, :log_desc, :log_date)");

		$log_query->execute(array(
						':log_employeeid' => $logged_employee_id,
						':log_desc' => $log_desc,
						':log_date' => $log_date));

	header("Location: contacts?page=list&mess=1");

break;

case "edit_contact":

		$contact_id = $_POST['contact_id'];
		$contact_firstname = $_POST['contact_firstname'];
		$contact_lastname = $_POST['contact_lastname'];
		$contact_companyid = $_POST['contact_companyid'];
		$contact_nickname = $_POST['contact_nickname'];
		$contact_dob = date("Y-m-d", strtotime($_POST['contact_dob']));
		$contact_address = $_POST['contact_address'];
		$contact_zipcode = $_POST['contact_zipcode'];
		$contact_city = $_POST['contact_city'];
		$contact_state = $_POST['contact_state'];
		$contact_country = $_POST['contact_country'];
		$contact_info = $_POST['contact_info'];
		$contact_type = $_POST['contact_type'];

		//Upload and save contact_image
		if($_FILES['contact_image']['size'] !== 0) {

			//Delete old image
			$del_img_query = $db->prepare("
										SELECT contact_image
										FROM idk_contacts
										WHERE contact_id = :contact_id");

			$del_img_query->execute(array(
									':contact_id' => $contact_id));

			$del_img = $del_img_query->fetch();

				$contact_image = $del_img['contact_image'];

			if($contact_image == "" OR $contact_image == "none.jpg"){}else{
				unlink("files/" . getSubdomainr() . "_files/contacts/" . $contact_image);
			}

			$contact_image = $_FILES['contact_image'];

			//File properties
			$file_name = $contact_image['name'];
			$file_tmp = $contact_image['tmp_name'];
			$file_size = $contact_image['size'];
			$file_error = $contact_image['error'];

			//File extension
			$file_ext = explode('.', $file_name);
			$file_ext = strtolower(end($file_ext));

			$allowed = array('jpg', 'png');

			if(in_array($file_ext, $allowed)) {

				$contact_image_final = uniqid() . '.' . $file_ext;
				$file_destination = 'files/contacts/' . $contact_image_final;

				if(move_uploaded_file($file_tmp, $file_destination)) {

					$path_to_image_directory = "files/" . getSubdomainr() . "_files/contacts/";
					$final_width_of_image = 660;

					if(preg_match('/[.](jpg)$/', $contact_image_final)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $contact_image_final);
					} else if (preg_match('/[.](png)$/', $contact_image_final)) {
						$im = imagecreatefrompng($path_to_image_directory . $contact_image_final);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);
					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));
					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);
					imagejpeg($nm, $path_to_image_directory . $contact_image_final);

				}
			}
		}else{
			$contact_image_final = $_POST['contact_image_url'];
		}

			$query = $db->prepare("
							UPDATE idk_contacts
							SET	contact_firstname = :contact_firstname, contact_lastname = :contact_lastname, contact_nickname = :contact_nickname, contact_dob = :contact_dob, contact_address = :contact_address, contact_zipcode = :contact_zipcode, contact_city = :contact_city, contact_state = :contact_state, contact_country = :contact_country, contact_info = :contact_info, contact_type = :contact_type, contact_companyid = :contact_companyid, contact_image = :contact_image
							WHERE contact_id = :contact_id");

			$query->execute(array(
					':contact_firstname' => $contact_firstname,
					':contact_lastname' => $contact_lastname,
					':contact_nickname' => $contact_nickname,
					':contact_dob' => $contact_dob,
					':contact_address' => $contact_address,
					':contact_zipcode' => $contact_zipcode,
					':contact_city' => $contact_city,
					':contact_state' => $contact_state,
					':contact_country' => $contact_country,
					':contact_info' => $contact_info,
					':contact_type' => $contact_type,
					':contact_companyid' => $contact_companyid,
					':contact_image' => $contact_image_final,
					':contact_id' => $contact_id));

		//Add to LOGS
		$log_desc = "Uredio profil kontakta: " . $contact_firstname . " " . $contact_lastname . " ";
		$log_date = date('Y-m-d H:i:s');

		$log_query = $db->prepare("
						INSERT INTO idk_logs
							(log_employeeid, log_desc, log_date)
						VALUES
							(:log_employeeid, :log_desc, :log_date)");

		$log_query->execute(array(
						':log_employeeid' => $logged_employee_id,
						':log_desc' => $log_desc,
						':log_date' => $log_date));

		header("Location: contacts?page=open&id=$contact_id&mess=21");

break;

case "add_contact_note":

	$note_txt = $_POST['note_txt'];
	$note_datetime = date('Y-m-d H:i:s');
	$note_group = 6;
	$note_dataid = $_POST['note_dataid'];

	$query = $db->prepare("
					INSERT INTO idk_notes
						(note_txt, note_datetime, note_group, note_dataid, note_employeeid)
					VALUES
						(:note_txt, :note_datetime, :note_group, :note_dataid, :note_employeeid)");

	$query->execute(array(
				':note_txt' => $note_txt,
				':note_datetime' => $note_datetime,
				':note_group' => $note_group,
				':note_dataid' => $note_dataid,
				':note_employeeid' => $logged_employee_id));
				
	$query_contact = $db->prepare("
					SELECT contact_firstname, contact_lastname
					FROM idk_contacts
					WHERE contact_id = :contact_id");
					
	$query_contact->execute(array(
				':contact_id' => $note_dataid));
				
	$row = $query_contact->fetch();

	$contact_firstname = $row['contact_firstname'];
	$contact_lastname = $row['contact_lastname'];
				
	//Add to LOGS
	$log_desc = "Dodao novu bilješku za kontakta: " .$contact_firstname. " " .$contact_lastname. "";
	$log_type = "3";
	addToLogs($log_desc, $log_type); //Log za biljeske - $log_type = 3

	header("Location: contacts?page=open&id=$note_dataid&mess=1");


break;

case "add_company_note":

	$note_txt = $_POST['note_txt'];
	$note_datetime = date('Y-m-d H:i:s');
	$note_group = 3;
	$note_dataid = $_POST['note_dataid'];

	$query = $db->prepare("
					INSERT INTO idk_notes
						(note_txt, note_datetime, note_group, note_dataid, note_employeeid)
					VALUES
						(:note_txt, :note_datetime, :note_group, :note_dataid, :note_employeeid)");

	$query->execute(array(
				':note_txt' => $note_txt,
				':note_datetime' => $note_datetime,
				':note_group' => $note_group,
				':note_dataid' => $note_dataid,
				':note_employeeid' => $logged_employee_id));
				
	$query_company = $db->prepare("
					SELECT company_name
					FROM idk_companies
					WHERE company_id = :company_id");
					
	$query_company->execute(array(
				':company_id' => $note_dataid));
				
	$row = $query_company->fetch();

	$company_name = $row['company_name'];
				
	//Add to LOGS
	$log_desc = "Dodao novu bilješku za kompaniju: " .$company_name. "";
	$log_type = "3";
	addToLogs($log_desc, $log_type); //Log za biljeske - $log_type = 3

	header("Location: companies?page=open&id=$note_dataid&mess=1");


break;

case "add_nalog_note":

	$note_txt = $_POST['note_txt'];
	$note_datetime = date('Y-m-d H:i:s');
	$note_group = 4;
	$note_dataid = $_POST['note_dataid'];

	$query = $db->prepare("
					INSERT INTO idk_notes
						(note_txt, note_datetime, note_group, note_dataid, note_employeeid)
					VALUES
						(:note_txt, :note_datetime, :note_group, :note_dataid, :note_employeeid)");

	$query->execute(array(
				':note_txt' => $note_txt,
				':note_datetime' => $note_datetime,
				':note_group' => $note_group,
				':note_dataid' => $note_dataid,
				':note_employeeid' => $logged_employee_id));
				
	$query_nalog = $db->prepare("
		SELECT nalog_naziv
		FROM idk_nalozi
		WHERE nalog_id = :nalog_id");
					
	$query_nalog->execute(array(
				':nalog_id' => $note_dataid));
				
	$row = $query_nalog->fetch();

	$nalog_naziv = $row['nalog_naziv'];
				
	//Add to LOGS
	$log_desc = "Dodao novu bilješku za nalog: " .$nalog_naziv. "";
	$log_type = "3";
	addToLogs($log_desc, $log_type); //Log za biljeske - $log_type = 3

	header("Location: nalozi?page=open&id=$note_dataid&mess=3");


break;

case "add_project_note":

	$note_txt = $_POST['note_txt'];
	$note_datetime = date('Y-m-d H:i:s');
	$note_group = 5;
	$note_dataid = $_POST['note_dataid'];

	$query = $db->prepare("
					INSERT INTO idk_notes
						(note_txt, note_datetime, note_group, note_dataid, note_employeeid)
					VALUES
						(:note_txt, :note_datetime, :note_group, :note_dataid, :note_employeeid)");

	$query->execute(array(
				':note_txt' => $note_txt,
				':note_datetime' => $note_datetime,
				':note_group' => $note_group,
				':note_dataid' => $note_dataid,
				':note_employeeid' => $logged_employee_id));
				
	$query_project = $db->prepare("
		SELECT project_name
		FROM idk_projects
		WHERE project_id = :project_id");
					
	$query_project->execute(array(
				':project_id' => $note_dataid));
				
	$row = $query_project->fetch();

	$project_name = $row['project_name'];
				
	//Add to LOGS
	$log_desc = "Dodao novu bilješku za projekt: " .$project_name. "";
	$log_type = "3";
	addToLogs($log_desc, $log_type); //Log za biljeske - $log_type = 3

	header("Location: projects?page=open&id=$note_dataid&mess=3");


break;

case "add_contact_doc":

	$document_dataid = $_POST['document_dataid'];

	//Upload document
	$document_file = $_FILES['document_file'];

	//File properties
	$file_name = $document_file['name'];
	$file_tmp = $document_file['tmp_name'];

	//File extension
	$file_ext = explode('.', $file_name);
	$file_ext = strtolower(end($file_ext));

	$allowed = array('jpg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png');

	if(in_array($file_ext, $allowed)) {

		$file_name_new = uniqid() . '.' . $file_ext;
		$file_destination = "files/files/contacts/" . $file_name_new;

		if(move_uploaded_file($file_tmp, $file_destination)){}
	}

	$document_name = $_POST['document_name'];
	$document_desc = $_POST['document_desc'];
	$document_datetime = date('Y-m-d H:i:s');
	$document_group = 2;

	$query = $db->prepare("
					INSERT INTO idk_documents
						(document_name, document_desc, document_file, document_icon, document_datetime, document_group, document_dataid, document_employeeid)
					VALUES
						(:document_name, :document_desc, :document_file, :document_icon, :document_datetime, :document_group, :document_dataid, :document_employeeid)");

	$query->execute(array(
					':document_name' => $document_name,
					':document_desc' => $document_desc,
					':document_file' => $file_name_new,
					':document_icon' => $file_ext,
					':document_datetime' => $document_datetime,
					':document_group' => $document_group,
					':document_dataid' => $document_dataid,
					':document_employeeid' => $logged_employee_id));

	//Add to LOGS
	$log_desc = "Dodao novi dokument: " . $document_name . " ";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_employeeid, log_desc, log_date)
					VALUES
						(:log_employeeid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_employeeid' => $logged_employee_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: contacts?page=open&id=$document_dataid&mess=2");

break;

case "add_company_doc":

	$document_dataid = $_POST['document_dataid'];

	//Upload document
	$document_file = $_FILES['document_file'];

	//File properties
	$file_name = $document_file['name'];
	$file_tmp = $document_file['tmp_name'];

	//File extension
	$file_ext = explode('.', $file_name);
	$file_ext = strtolower(end($file_ext));

	$allowed = array('jpg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png');

	if(in_array($file_ext, $allowed)) {

		$file_name_new = uniqid() . '.' . $file_ext;
		$file_destination = "files/files/companies/" . $file_name_new;

		if(move_uploaded_file($file_tmp, $file_destination)){}
	}

	$document_name = $_POST['document_name'];
	$document_desc = $_POST['document_desc'];
	$document_datetime = date('Y-m-d H:i:s');
	$document_group = 3;

	$query = $db->prepare("
					INSERT INTO idk_documents
						(document_name, document_desc, document_file, document_icon, document_datetime, document_group, document_dataid, document_employeeid)
					VALUES
						(:document_name, :document_desc, :document_file, :document_icon, :document_datetime, :document_group, :document_dataid, :document_employeeid)");

	$query->execute(array(
					':document_name' => $document_name,
					':document_desc' => $document_desc,
					':document_file' => $file_name_new,
					':document_icon' => $file_ext,
					':document_datetime' => $document_datetime,
					':document_group' => $document_group,
					':document_dataid' => $document_dataid,
					':document_employeeid' => $logged_employee_id));

	//Add to LOGS
	$log_desc = "Dodao novi dokument: " . $document_name . " ";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_employeeid, log_desc, log_date)
					VALUES
						(:log_employeeid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_employeeid' => $logged_employee_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: companies?page=open&id=$document_dataid&mess=2");

break;

case "add_nalog_doc":

	$document_dataid = $_POST['document_dataid'];

	//Upload document
	$document_file = $_FILES['document_file'];

	//File properties
	$file_name = $document_file['name'];
	$file_tmp = $document_file['tmp_name'];

	//File extension
	$file_ext = explode('.', $file_name);
	$file_ext = strtolower(end($file_ext));

	$allowed = array('jpg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png');

	if(in_array($file_ext, $allowed)) {

		$file_name_new = uniqid() . '.' . $file_ext;
		$file_destination = "files/files/nalozi/" . $file_name_new;

		if(move_uploaded_file($file_tmp, $file_destination)){}
	}

	$document_name = $_POST['document_name'];
	$document_desc = $_POST['document_desc'];
	$document_datetime = date('Y-m-d H:i:s');
	$document_group = 4;

	$query = $db->prepare("
					INSERT INTO idk_documents
						(document_name, document_desc, document_file, document_icon, document_datetime, document_group, document_dataid, document_employeeid)
					VALUES
						(:document_name, :document_desc, :document_file, :document_icon, :document_datetime, :document_group, :document_dataid, :document_employeeid)");

	$query->execute(array(
					':document_name' => $document_name,
					':document_desc' => $document_desc,
					':document_file' => $file_name_new,
					':document_icon' => $file_ext,
					':document_datetime' => $document_datetime,
					':document_group' => $document_group,
					':document_dataid' => $document_dataid,
					':document_employeeid' => $logged_employee_id));

	//Add to LOGS
	$log_desc = "Dodao novi dokument: " . $document_name . " ";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_employeeid, log_desc, log_date)
					VALUES
						(:log_employeeid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_employeeid' => $logged_employee_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: nalozi?page=open&id=$document_dataid&mess=1");


break;

case "add_project_doc":

	$document_dataid = $_POST['document_dataid'];

	//Upload document
	$document_file = $_FILES['document_file'];

	//File properties
	$file_name = $document_file['name'];
	$file_tmp = $document_file['tmp_name'];

	//File extension
	$file_ext = explode('.', $file_name);
	$file_ext = strtolower(end($file_ext));

	$allowed = array('jpg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png');

	if(in_array($file_ext, $allowed)) {

		$file_name_new = uniqid() . '.' . $file_ext;
		$file_destination = "files/files/projects/" . $file_name_new;

		if(move_uploaded_file($file_tmp, $file_destination)){}
	}

	$document_name = $_POST['document_name'];
	$document_desc = $_POST['document_desc'];
	$document_datetime = date('Y-m-d H:i:s');
	$document_group = 5;

	$query = $db->prepare("
					INSERT INTO idk_documents
						(document_name, document_desc, document_file, document_icon, document_datetime, document_group, document_dataid, document_employeeid)
					VALUES
						(:document_name, :document_desc, :document_file, :document_icon, :document_datetime, :document_group, :document_dataid, :document_employeeid)");

	$query->execute(array(
					':document_name' => $document_name,
					':document_desc' => $document_desc,
					':document_file' => $file_name_new,
					':document_icon' => $file_ext,
					':document_datetime' => $document_datetime,
					':document_group' => $document_group,
					':document_dataid' => $document_dataid,
					':document_employeeid' => $logged_employee_id));

	//Add to LOGS
	$log_desc = "Dodao novi dokument: " . $document_name . " ";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_employeeid, log_desc, log_date)
					VALUES
						(:log_employeeid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_employeeid' => $logged_employee_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: projects?page=open&id=$document_dataid&mess=1");


break;

case "save_contact_inote":

	$contact_id = $_POST['contact_id'];
	$contact_inote = base64_encode($_POST['contact_inote']);

	//Get
	$query_select = $db->prepare("
							SELECT contact_firstname, contact_lastname
							FROM idk_contacts
							WHERE contact_id = :contact_id");

	$query_select->execute(array(
						':contact_id' => $contact_id));

	$row_select = $query_select->fetch();

	$contact_firstname = $row_select['contact_firstname'];
	$contact_lastname = $row_select['contact_lastname'];

	//Save
	$query = $db->prepare("
					UPDATE idk_contacts
					SET	contact_inote = :contact_inote
					WHERE contact_id = :contact_id");

	$query->execute(array(
				':contact_inote' => $contact_inote,
				':contact_id' => $contact_id));

	//Add to LOGS
	$log_desc = "Snimio važne napomene za kontakt: " . $contact_firstname . " " . $contact_lastname . " ";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_employeeid, log_desc, log_date)
					VALUES
						(:log_employeeid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_employeeid' => $logged_employee_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: contacts?page=open&id=$contact_id&mess=5");

break;

case "save_company_inote":

	$company_id = $_POST['company_id'];
	$company_inote = base64_encode($_POST['company_inote']);

	//Get
	$query_select = $db->prepare("
							SELECT company_nam
							FROM idk_companies
							WHERE company_id = :company_id");

	$query_select->execute(array(
						':company_id' => $company_id));

	$row_select = $query_select->fetch();

	$company_name = $row_select['company_name'];

	//Save
	$query = $db->prepare("
					UPDATE idk_companies
					SET	company_inote = :company_inote
					WHERE company_id = :company_id");

	$query->execute(array(
				':company_inote' => $company_inote,
				':company_id' => $company_id));

	//Add to LOGS
	$log_desc = "Snimio važne napomene za kompaniju: " . $company_name . "";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_employeeid, log_desc, log_date)
					VALUES
						(:log_employeeid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_employeeid' => $logged_employee_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: companies?page=open&id=$company_id&mess=5");

break;

case "add_timeline":

	$timeline_type = $_POST['timeline_type'];
	$timeline_txt = $_POST['timeline_txt'];
	$timeline_date = date("Y-m-d", strtotime($_POST['timeline_date']));
	$timeline_time = date("H:i:s", strtotime($_POST['timeline_time']));
	$timeline_datetime = $timeline_date . ' ' . $timeline_time;
	$timeline_dataid = $_POST['timeline_dataid'];
	$timeline_group = 1;

	$query = $db->prepare("
					INSERT INTO idk_timeline
						(timeline_type, timeline_txt, timeline_datetime, timeline_group, timeline_dataid, timeline_employeeid)
					VALUES
						(:timeline_type, :timeline_txt, :timeline_datetime, :timeline_group, :timeline_dataid, :timeline_employeeid)");

	$query->execute(array(
				':timeline_type' => $timeline_type,
				':timeline_txt' => $timeline_txt,
				':timeline_datetime' => $timeline_datetime,
				':timeline_group' => $timeline_group,
				':timeline_dataid' => $timeline_dataid,
				':timeline_employeeid' => $logged_employee_id));

	header("Location: contacts?page=open&id=$timeline_dataid&mess=6");


break;

case "add_timeline_comapny":

	$timeline_type = $_POST['timeline_type'];
	$timeline_txt = $_POST['timeline_txt'];
	$timeline_date = date("Y-m-d", strtotime($_POST['timeline_date']));
	$timeline_time = date("H:i:s", strtotime($_POST['timeline_time']));
	$timeline_datetime = $timeline_date . ' ' . $timeline_time;
	$timeline_dataid = $_POST['timeline_dataid'];
	$timeline_group = 2;

	$query = $db->prepare("
					INSERT INTO idk_timeline
						(timeline_type, timeline_txt, timeline_datetime, timeline_group, timeline_dataid, timeline_employeeid)
					VALUES
						(:timeline_type, :timeline_txt, :timeline_datetime, :timeline_group, :timeline_dataid, :timeline_employeeid)");

	$query->execute(array(
				':timeline_type' => $timeline_type,
				':timeline_txt' => $timeline_txt,
				':timeline_datetime' => $timeline_datetime,
				':timeline_group' => $timeline_group,
				':timeline_dataid' => $timeline_dataid,
				':timeline_employeeid' => $logged_employee_id));

	header("Location: companies?page=open&id=$timeline_dataid&mess=6");


break;

case "add_task":

	$task_txt = $_POST['task_txt'];
	$task_replytxt = "Novi zadatak";
	$task_duedate = date("Y-m-d", strtotime($_POST['task_duedate']));
	$task_duetime = date("H:i:s", strtotime($_POST['task_duetime']));
	$task_duedatetime = $task_duedate . ' ' . $task_duetime;
	$task_assignedid = $_POST['task_assignedid'];
	$task_emailnotifi = $_POST['task_emailnotifi'];
	$task_repeatnotifi = $_POST['task_repeatnotifi'];
	$task_group = 1;
	$task_dataid = $_POST['task_dataid'];
	$task_datetime = date('Y-m-d H:i:s');
	$task_status = 1;

	$query = $db->prepare("
					INSERT INTO idk_tasks
						(task_txt, task_replytxt, task_duedatetime, task_assignedid, task_emailnotifi, task_repeatnotifi, task_group, task_dataid, task_employeeid, task_datetime, task_status)
					VALUES
						(:task_txt, :task_replytxt, :task_duedatetime, :task_assignedid, :task_emailnotifi, :task_repeatnotifi, :task_group, :task_dataid, :task_employeeid, :task_datetime, :task_status)");

	$query->execute(array(
				':task_txt' => $task_txt,
				':task_replytxt' => $task_replytxt,
				':task_duedatetime' => $task_duedatetime,
				':task_assignedid' => $task_assignedid,
				':task_emailnotifi' => $task_emailnotifi,
				':task_repeatnotifi' => $task_repeatnotifi,
				':task_group' => $task_group,
				':task_dataid' => $task_dataid,
				':task_employeeid' => $logged_employee_id,
				':task_datetime' => $task_datetime,
				':task_status' => $task_status));

	$lastInsertId = $db->lastInsertId();

	//Add notification
	$notification_title = "Imate novi zadatak!";
	$notification_icon = "tasks";
	$notification_link = "" . getSiteURLr() . "tasks?page=open&id=" . $lastInsertId . "";

	$query_notification = $db->prepare("
							INSERT INTO idk_notifications
								(notification_datetime, notification_title, notification_icon, notification_link, notification_employeeid, notification_status)
							VALUES
								(:notification_datetime, :notification_title, :notification_icon, :notification_link, :notification_employeeid, :notification_status)");

	$query_notification->execute(array(
							':notification_datetime' => $task_datetime,
							':notification_title' => $notification_title,
							':notification_icon' => $notification_icon,
							':notification_link' => $notification_link,
							':notification_employeeid' => $task_assignedid,
							':notification_status' => 1));

	//Email Trigger
	if($task_emailnotifi == 1){

		$task_duedate_at = date("Y/m/d", strtotime($_POST['task_duedate']));
		$task_duetime_at = date("H:i:s", strtotime($_POST['task_duetime']));

		$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
		$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$lastInsertId";
		$url_tag = "email_task_" . $lastInsertId . "";

		createTrigger($url_link, $url_datetime, $url_tag);

	}elseif($task_emailnotifi == 2){

		$task_duedate_at = date("Y/m/d", strtotime($_POST['task_duedate'] . "-1 days"));
		$task_duetime_at = date("H:i:s", strtotime($_POST['task_duetime']));

		$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
		$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$lastInsertId";
		$url_tag = "email_task_" . $lastInsertId . "";

		createTrigger($url_link, $url_datetime, $url_tag);

	}elseif($task_emailnotifi == 3){

		$task_duedate_at = date("Y/m/d", strtotime($_POST['task_duedate'] . "-2 days"));
		$task_duetime_at = date("H:i:s", strtotime($_POST['task_duetime']));

		$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
		$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$lastInsertId";
		$url_tag = "email_task_" . $lastInsertId . "";

		createTrigger($url_link, $url_datetime, $url_tag);

	}elseif($task_emailnotifi == 4){

		$task_duedate_at = date("Y/m/d", strtotime($_POST['task_duedate'] . "-1 week"));
		$task_duetime_at = date("H:i:s", strtotime($_POST['task_duetime']));

		$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
		$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$lastInsertId";
		$url_tag = "email_task_" . $lastInsertId . "";

		createTrigger($url_link, $url_datetime, $url_tag);

	}elseif($task_emailnotifi == 5){
		$task_duedate_at = date("Y/m/d", strtotime($_POST['task_duedate'] . "-1 month"));
		$task_duetime_at = date("H:i:s", strtotime($_POST['task_duetime']));

		$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
		$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$lastInsertId";
		$url_tag = "email_task_" . $lastInsertId . "";

		createTrigger($url_link, $url_datetime, $url_tag);

	}

	header("Location: contacts?page=open&id=$task_dataid&mess=8");

break;

case "add_task_done":

	$task_id = $_POST['task_id'];

	//Check for repeat tasks
	$query_tasks = $db->prepare("
							SELECT task_txt, task_duedatetime, task_assignedid, task_emailnotifi, task_repeatnotifi, task_dataid, task_employeeid, task_group
							FROM idk_tasks
							WHERE task_id = :task_id");

	$query_tasks->execute(array(
					':task_id' => $task_id));

	$row_tasks = $query_tasks->fetch();

		$task_txt = $row_tasks['task_txt'];
		$task_duedatetime = $row_tasks['task_duedatetime'];
		$task_assignedid = $row_tasks['task_assignedid'];
		$task_emailnotifi = $row_tasks['task_emailnotifi'];
		$task_repeatnotifi = $row_tasks['task_repeatnotifi'];
		$task_dataid = $row_tasks['task_dataid'];
		$task_employeeid = $row_tasks['task_employeeid'];
		$task_group = $row_tasks['task_group'];

		//Remove Trigger
		if($task_emailnotifi != 0){
			$url_tag = "email_task_" . $task_id . "";
			removeTrigger($url_tag);
		}

		//Add done notification
		$notification_title = "Zadatak završen!";
		$notification_icon = "tasks";
		$notification_link = "" . getSiteURLr() . "tasks?page=open&id=" . $task_id . "";
		$task_datetime = date('Y-m-d H:i:s');

		$query_notification = $db->prepare("
								INSERT INTO idk_notifications
									(notification_datetime, notification_title, notification_icon, notification_link, notification_employeeid, notification_status)
								VALUES
									(:notification_datetime, :notification_title, :notification_icon, :notification_link, :notification_employeeid, :notification_status)");

		$query_notification->execute(array(
								':notification_datetime' => $task_datetime,
								':notification_title' => $notification_title,
								':notification_icon' => $notification_icon,
								':notification_link' => $notification_link,
								':notification_employeeid' => $task_employeeid,
								':notification_status' => 1));

	if($task_repeatnotifi == 0){

		$task_replytxt = $_POST['task_replytxt'];
		$task_status = 2;

		//Save
		$query = $db->prepare("
						UPDATE idk_tasks
						SET	task_replytxt = :task_replytxt, task_status = :task_status
						WHERE task_id = :task_id");

		$query->execute(array(
					':task_replytxt' => $task_replytxt,
					':task_status' => $task_status,
					':task_id' => $task_id));
	}else{
		$task_replytxt = $_POST['task_replytxt'];
		$task_status = 2;

		//Save
		$query = $db->prepare("
						UPDATE idk_tasks
						SET	task_replytxt = :task_replytxt, task_status = :task_status
						WHERE task_id = :task_id");

		$query->execute(array(
					':task_replytxt' => $task_replytxt,
					':task_status' => $task_status,
					':task_id' => $task_id));

		//Create repeat task
		$task_replytxt = "Novi zadatak";
		$task_datetime = date('Y-m-d H:i:s');
		$task_status = 1;

		if($task_repeatnotifi == 1){
			$task_duedatetime_new = date('Y-m-d H:i:s',strtotime($task_duedatetime . "+1 days"));
		}elseif($task_repeatnotifi == 2){
			$task_duedatetime_new = date('Y-m-d H:i:s',strtotime($task_duedatetime . "+7 days"));
		}elseif($task_repeatnotifi == 3){
			$task_duedatetime_new = date('Y-m-d H:i:s',strtotime($task_duedatetime . "+1 month"));
		}elseif($task_repeatnotifi == 4){
			$task_duedatetime_new = date('Y-m-d H:i:s',strtotime($task_duedatetime . "+1 years"));
		}

		$query = $db->prepare("
						INSERT INTO idk_tasks
							(task_txt, task_replytxt, task_duedatetime, task_assignedid, task_emailnotifi, task_repeatnotifi, task_group, task_dataid, task_employeeid, task_datetime, task_status)
						VALUES
							(:task_txt, :task_replytxt, :task_duedatetime, :task_assignedid, :task_emailnotifi, :task_repeatnotifi, :task_group, :task_dataid, :task_employeeid, :task_datetime, :task_status)");

		$query->execute(array(
					':task_txt' => $task_txt,
					':task_replytxt' => $task_replytxt,
					':task_duedatetime' => $task_duedatetime_new,
					':task_assignedid' => $task_assignedid,
					':task_emailnotifi' => $task_emailnotifi,
					':task_repeatnotifi' => $task_repeatnotifi,
					':task_group' => $task_group,
					':task_dataid' => $task_dataid,
					':task_employeeid' => $task_employeeid,
					':task_datetime' => $task_datetime,
					':task_status' => $task_status));

		$lastInsertId = $db->lastInsertId();

		//Add notification
		$notification_title = "Imate novi zadatak!";
		$notification_icon = "tasks";
		$notification_link = "" . getSiteURLr() . "tasks?page=open&id=" . $lastInsertId . "";

		$query_notification = $db->prepare("
								INSERT INTO idk_notifications
									(notification_datetime, notification_title, notification_icon, notification_link, notification_employeeid, notification_status)
								VALUES
									(:notification_datetime, :notification_title, :notification_icon, :notification_link, :notification_employeeid, :notification_status)");

		$query_notification->execute(array(
								':notification_datetime' => $task_datetime,
								':notification_title' => $notification_title,
								':notification_icon' => $notification_icon,
								':notification_link' => $notification_link,
								':notification_employeeid' => $task_assignedid,
								':notification_status' => 1));

		//create Trigger
		if($task_emailnotifi == 1){

			$task_duedate_at = date("Y/m/d", strtotime($task_duedatetime_new));
			$task_duetime_at = date("H:i:s", strtotime($task_duedatetime_new));

			$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
			$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$lastInsertId";
			$url_tag = "email_task_" . $lastInsertId . "";

			createTrigger($url_link, $url_datetime, $url_tag);

		}elseif($task_emailnotifi == 2){

			$task_duedate_at = date("Y/m/d", strtotime($task_duedatetime_new . "-1 days"));
			$task_duetime_at = date("H:i:s", strtotime($task_duedatetime_new));

			$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
			$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$lastInsertId";
			$url_tag = "email_task_" . $lastInsertId . "";

			createTrigger($url_link, $url_datetime, $url_tag);

		}elseif($task_emailnotifi == 3){

			$task_duedate_at = date("Y/m/d", strtotime($task_duedatetime_new . "-2 days"));
			$task_duetime_at = date("H:i:s", strtotime($task_duedatetime_new));

			$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
			$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$lastInsertId";
			$url_tag = "email_task_" . $lastInsertId . "";

			createTrigger($url_link, $url_datetime, $url_tag);

		}elseif($task_emailnotifi == 4){

			$task_duedate_at = date("Y/m/d", strtotime($task_duedatetime_new . "-1 week"));
			$task_duetime_at = date("H:i:s", strtotime($task_duedatetime_new));

			$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
			$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$lastInsertId";
			$url_tag = "email_task_" . $lastInsertId . "";

			createTrigger($url_link, $url_datetime, $url_tag);

		}elseif($task_emailnotifi == 5){
			$task_duedate_at = date("Y/m/d", strtotime($task_duedatetime_new . "-1 month"));
			$task_duetime_at = date("H:i:s", strtotime($task_duedatetime_new));

			$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
			$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$lastInsertId";
			$url_tag = "email_task_" . $lastInsertId . "";

			createTrigger($url_link, $url_datetime, $url_tag);

		}

	}

	if($task_group == '1'){
		header("Location: contacts?page=open&id=$task_dataid&mess=9");
	}elseif($task_group == '2'){
		header("Location: employees?page=open&id=$task_dataid&mess=9");
	}elseif($task_group == '3'){
		header("Location: companies?page=open&id=$task_dataid&mess=9");
	}

break;

case "add_task_notdone":

	$task_id = $_POST['task_id'];

	//Check for repeat tasks
	$query_tasks = $db->prepare("
							SELECT task_txt, task_duedatetime, task_assignedid, task_emailnotifi, task_repeatnotifi, task_dataid, task_employeeid, task_group
							FROM idk_tasks
							WHERE task_id = :task_id");

	$query_tasks->execute(array(
					':task_id' => $task_id));

	$row_tasks = $query_tasks->fetch();

		$task_txt = $row_tasks['task_txt'];
		$task_duedatetime = $row_tasks['task_duedatetime'];
		$task_assignedid = $row_tasks['task_assignedid'];
		$task_emailnotifi = $row_tasks['task_emailnotifi'];
		$task_repeatnotifi = $row_tasks['task_repeatnotifi'];
		$task_dataid = $row_tasks['task_dataid'];
		$task_employeeid = $row_tasks['task_employeeid'];
		$task_group = $row_tasks['task_group'];

		//Add done notification
		$notification_title = "Zadatak nije završen!";
		$notification_icon = "tasks";
		$notification_link = "" . getSiteURLr() . "tasks?page=open&id=" . $task_id . "";
		$task_datetime = date('Y-m-d H:i:s');

		$query_notification = $db->prepare("
								INSERT INTO idk_notifications
									(notification_datetime, notification_title, notification_icon, notification_link, notification_employeeid, notification_status)
								VALUES
									(:notification_datetime, :notification_title, :notification_icon, :notification_link, :notification_employeeid, :notification_status)");

		$query_notification->execute(array(
								':notification_datetime' => $task_datetime,
								':notification_title' => $notification_title,
								':notification_icon' => $notification_icon,
								':notification_link' => $notification_link,
								':notification_employeeid' => $task_employeeid,
								':notification_status' => 1));

		//Remove Trigger
		if($task_emailnotifi != 0){
			$url_tag = "email_task_" . $task_id . "";
			removeTrigger($url_tag);
		}

	if($task_repeatnotifi == 0){

		$task_replytxt = $_POST['task_replytxt'];
		$task_status = 3;

		//Save
		$query = $db->prepare("
						UPDATE idk_tasks
						SET	task_replytxt = :task_replytxt, task_status = :task_status
						WHERE task_id = :task_id");

		$query->execute(array(
					':task_replytxt' => $task_replytxt,
					':task_status' => $task_status,
					':task_id' => $task_id));

	}else{
		$task_replytxt = $_POST['task_replytxt'];
		$task_status = 3;

		//Save
		$query = $db->prepare("
						UPDATE idk_tasks
						SET	task_replytxt = :task_replytxt, task_status = :task_status
						WHERE task_id = :task_id");

		$query->execute(array(
					':task_replytxt' => $task_replytxt,
					':task_status' => $task_status,
					':task_id' => $task_id));

		//Create repeat task
		$task_replytxt = "Novi zadatak";
		$task_datetime = date('Y-m-d H:i:s');
		$task_status = 1;

		if($task_repeatnotifi == 1){
			$task_duedatetime_new = date('Y-m-d H:i:s',strtotime($task_duedatetime . "+1 days"));
		}elseif($task_repeatnotifi == 2){
			$task_duedatetime_new = date('Y-m-d H:i:s',strtotime($task_duedatetime . "+7 days"));
		}elseif($task_repeatnotifi == 3){
			$task_duedatetime_new = date('Y-m-d H:i:s',strtotime($task_duedatetime . "+1 month"));
		}elseif($task_repeatnotifi == 4){
			$task_duedatetime_new = date('Y-m-d H:i:s',strtotime($task_duedatetime . "+1 years"));
		}

		$query = $db->prepare("
						INSERT INTO idk_tasks
							(task_txt, task_replytxt, task_duedatetime, task_assignedid, task_emailnotifi, task_repeatnotifi, task_group, task_dataid, task_employeeid, task_datetime, task_status)
						VALUES
							(:task_txt, :task_replytxt, :task_duedatetime, :task_assignedid, :task_emailnotifi, :task_repeatnotifi, :task_group, :task_dataid, :task_employeeid, :task_datetime, :task_status)");

		$query->execute(array(
					':task_txt' => $task_txt,
					':task_replytxt' => $task_replytxt,
					':task_duedatetime' => $task_duedatetime_new,
					':task_assignedid' => $task_assignedid,
					':task_emailnotifi' => $task_emailnotifi,
					':task_repeatnotifi' => $task_repeatnotifi,
					':task_group' => $task_group,
					':task_dataid' => $task_dataid,
					':task_employeeid' => $task_employeeid,
					':task_datetime' => $task_datetime,
					':task_status' => $task_status));

		$lastInsertId = $db->lastInsertId();

		//Add notification
		$notification_title = "Imate novi zadatak!";
		$notification_icon = "tasks";
		$notification_link = "" . getSiteURLr() . "tasks?page=open&id=" . $lastInsertId . "";

		$query_notification = $db->prepare("
								INSERT INTO idk_notifications
									(notification_datetime, notification_title, notification_icon, notification_link, notification_employeeid, notification_status)
								VALUES
									(:notification_datetime, :notification_title, :notification_icon, :notification_link, :notification_employeeid, :notification_status)");

		$query_notification->execute(array(
								':notification_datetime' => $task_datetime,
								':notification_title' => $notification_title,
								':notification_icon' => $notification_icon,
								':notification_link' => $notification_link,
								':notification_employeeid' => $task_assignedid,
								':notification_status' => 1));

		//create Trigger
		if($task_emailnotifi == 1){

			$task_duedate_at = date("Y/m/d", strtotime($task_duedatetime_new));
			$task_duetime_at = date("H:i:s", strtotime($task_duedatetime_new));

			$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
			$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$lastInsertId";
			$url_tag = "email_task_" . $lastInsertId . "";

			createTrigger($url_link, $url_datetime, $url_tag);

		}elseif($task_emailnotifi == 2){

			$task_duedate_at = date("Y/m/d", strtotime($task_duedatetime_new . "-1 days"));
			$task_duetime_at = date("H:i:s", strtotime($task_duedatetime_new));

			$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
			$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$lastInsertId";
			$url_tag = "email_task_" . $lastInsertId . "";

			createTrigger($url_link, $url_datetime, $url_tag);

		}elseif($task_emailnotifi == 3){

			$task_duedate_at = date("Y/m/d", strtotime($task_duedatetime_new . "-2 days"));
			$task_duetime_at = date("H:i:s", strtotime($task_duedatetime_new));

			$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
			$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$lastInsertId";
			$url_tag = "email_task_" . $lastInsertId . "";

			createTrigger($url_link, $url_datetime, $url_tag);

		}elseif($task_emailnotifi == 4){

			$task_duedate_at = date("Y/m/d", strtotime($task_duedatetime_new . "-1 week"));
			$task_duetime_at = date("H:i:s", strtotime($task_duedatetime_new));

			$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
			$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$lastInsertId";
			$url_tag = "email_task_" . $lastInsertId . "";

			createTrigger($url_link, $url_datetime, $url_tag);

		}elseif($task_emailnotifi == 5){
			$task_duedate_at = date("Y/m/d", strtotime($task_duedatetime_new . "-1 month"));
			$task_duetime_at = date("H:i:s", strtotime($task_duedatetime_new));

			$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
			$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$lastInsertId";
			$url_tag = "email_task_" . $lastInsertId . "";

			createTrigger($url_link, $url_datetime, $url_tag);

		}

	}

	if($task_group == '1'){
		header("Location: contacts?page=open&id=$task_dataid&mess=10");
	}elseif($task_group == '2'){
		header("Location: employees?page=open&id=$task_dataid&mess=10");
	}elseif($task_group == '3'){
		header("Location: companies?page=open&id=$task_dataid&mess=10");
	}

break;

case "add_task_emailnotifi":

	$task_id = $_POST['task_id'];
	$task_emailnotifi = $_POST['task_emailnotifi'];
	$task_dataid = $_POST['task_dataid'];

	//Get data from tasks
	$query_tasks = $db->prepare("
							SELECT task_duedatetime, task_assignedid, task_group
							FROM idk_tasks
							WHERE task_id = :task_id");

	$query_tasks->execute(array(
					':task_id' => $task_id));

	$row_tasks = $query_tasks->fetch();

		$task_assignedid = $row_tasks['task_assignedid'];
		$task_group = $row_tasks['task_group'];

	//Save
	$query = $db->prepare("
					UPDATE idk_tasks
					SET	task_emailnotifi = :task_emailnotifi
					WHERE task_id = :task_id");

	$query->execute(array(
				':task_emailnotifi' => $task_emailnotifi,
				':task_id' => $task_id));

	//Email Trigger
	$url_tag = "email_task_" . $task_id . "";
	removeTrigger($url_tag);

	if($task_emailnotifi == 1){

			$task_duedate_at = date("Y/m/d", strtotime($row_tasks['task_duedatetime']));
			$task_duetime_at = date("H:i:s", strtotime($row_tasks['task_duedatetime']));

			$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
			$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$task_id";
			$url_tag = "email_task_" . $task_id . "";

			createTrigger($url_link, $url_datetime, $url_tag);

		}elseif($task_emailnotifi == 2){

			$task_duedate_at = date("Y/m/d", strtotime($row_tasks['task_duedatetime'] . "-1 days"));
			$task_duetime_at = date("H:i:s", strtotime($row_tasks['task_duedatetime']));

			$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
			$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$task_id";
			$url_tag = "email_task_" . $task_id . "";

			createTrigger($url_link, $url_datetime, $url_tag);

		}elseif($task_emailnotifi == 3){

			$task_duedate_at = date("Y/m/d", strtotime($row_tasks['task_duedatetime'] . "-2 days"));
			$task_duetime_at = date("H:i:s", strtotime($row_tasks['task_duedatetime']));

			$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
			$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$task_id";
			$url_tag = "email_task_" . $task_id . "";

			createTrigger($url_link, $url_datetime, $url_tag);

		}elseif($task_emailnotifi == 4){

			$task_duedate_at = date("Y/m/d", strtotime($row_tasks['task_duedatetime'] . "-1 week"));
			$task_duetime_at = date("H:i:s", strtotime($row_tasks['task_duedatetime']));

			$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
			$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$task_id";
			$url_tag = "email_task_" . $task_id . "";

			createTrigger($url_link, $url_datetime, $url_tag);

		}elseif($task_emailnotifi == 5){
			$task_duedate_at = date("Y/m/d", strtotime($row_tasks['task_duedatetime'] . "-1 month"));
			$task_duetime_at = date("H:i:s", strtotime($row_tasks['task_duedatetime']));

			$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
			$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$task_id";
			$url_tag = "email_task_" . $task_id . "";

			createTrigger($url_link, $url_datetime, $url_tag);

		}

		if($task_group == '1'){
			header("Location: contacts?page=open&id=$task_dataid&mess=12");
		}elseif($task_group == '2'){
			header("Location: employees?page=open&id=$task_dataid&mess=12");
		}elseif($task_group == '3'){
			header("Location: companies?page=open&id=$task_dataid&mess=12");
		}

break;

case "add_task_repeat":

	$task_id = $_POST['task_id'];
	$task_repeatnotifi = $_POST['task_repeatnotifi'];
	$task_dataid = $_POST['task_dataid'];

	//Save
	$query = $db->prepare("
					UPDATE idk_tasks
					SET	task_repeatnotifi = :task_repeatnotifi
					WHERE task_id = :task_id");

	$query->execute(array(
				':task_repeatnotifi' => $task_repeatnotifi,
				':task_id' => $task_id));

	$query_tasks = $db->prepare("
							SELECT task_group
							FROM idk_tasks
							WHERE task_id = :task_id");

	$query_tasks->execute(array(
					':task_id' => $task_id));

	$row_tasks = $query_tasks->fetch();

		$task_group = $row_tasks['task_group'];

	if($task_group == '1'){
		header("Location: contacts?page=open&id=$task_dataid&mess=12");
	}elseif($task_group == '2'){
		header("Location: employees?page=open&id=$task_dataid&mess=12");
	}elseif($task_group == '3'){
		header("Location: companies?page=open&id=$task_dataid&mess=12");
	}

break;

case "add_task_company":

	$task_txt = $_POST['task_txt'];
	$task_replytxt = "Novi zadatak";
	$task_duedate = date("Y-m-d", strtotime($_POST['task_duedate']));
	$task_duetime = date("H:i:s", strtotime($_POST['task_duetime']));
	$task_duedatetime = $task_duedate . ' ' . $task_duetime;
	$task_assignedid = $_POST['task_assignedid'];
	$task_emailnotifi = $_POST['task_emailnotifi'];
	$task_repeatnotifi = $_POST['task_repeatnotifi'];
	$task_group = 3;
	$task_dataid = $_POST['task_dataid'];
	$task_datetime = date('Y-m-d H:i:s');
	$task_status = 1;

	$query = $db->prepare("
					INSERT INTO idk_tasks
						(task_txt, task_replytxt, task_duedatetime, task_assignedid, task_emailnotifi, task_repeatnotifi, task_group, task_dataid, task_employeeid, task_datetime, task_status)
					VALUES
						(:task_txt, :task_replytxt, :task_duedatetime, :task_assignedid, :task_emailnotifi, :task_repeatnotifi, :task_group, :task_dataid, :task_employeeid, :task_datetime, :task_status)");

	$query->execute(array(
				':task_txt' => $task_txt,
				':task_replytxt' => $task_replytxt,
				':task_duedatetime' => $task_duedatetime,
				':task_assignedid' => $task_assignedid,
				':task_emailnotifi' => $task_emailnotifi,
				':task_repeatnotifi' => $task_repeatnotifi,
				':task_group' => $task_group,
				':task_dataid' => $task_dataid,
				':task_employeeid' => $logged_employee_id,
				':task_datetime' => $task_datetime,
				':task_status' => $task_status));

	$lastInsertId = $db->lastInsertId();

	//Add notification
	$notification_title = "Imate novi zadatak!";
	$notification_icon = "tasks";
	$notification_link = "" . getSiteURLr() . "tasks?page=open&id=" . $lastInsertId . "";

	$query_notification = $db->prepare("
							INSERT INTO idk_notifications
								(notification_datetime, notification_title, notification_icon, notification_link, notification_employeeid, notification_status)
							VALUES
								(:notification_datetime, :notification_title, :notification_icon, :notification_link, :notification_employeeid, :notification_status)");

	$query_notification->execute(array(
							':notification_datetime' => $task_datetime,
							':notification_title' => $notification_title,
							':notification_icon' => $notification_icon,
							':notification_link' => $notification_link,
							':notification_employeeid' => $task_assignedid,
							':notification_status' => 1));

	//Email Trigger
	if($task_emailnotifi == 1){

		$task_duedate_at = date("Y/m/d", strtotime($_POST['task_duedate']));
		$task_duetime_at = date("H:i:s", strtotime($_POST['task_duetime']));

		$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
		$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$lastInsertId";
		$url_tag = "email_task_" . $lastInsertId . "";

		createTrigger($url_link, $url_datetime, $url_tag);

	}elseif($task_emailnotifi == 2){

		$task_duedate_at = date("Y/m/d", strtotime($_POST['task_duedate'] . "-1 days"));
		$task_duetime_at = date("H:i:s", strtotime($_POST['task_duetime']));

		$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
		$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$lastInsertId";
		$url_tag = "email_task_" . $lastInsertId . "";

		createTrigger($url_link, $url_datetime, $url_tag);

	}elseif($task_emailnotifi == 3){

		$task_duedate_at = date("Y/m/d", strtotime($_POST['task_duedate'] . "-2 days"));
		$task_duetime_at = date("H:i:s", strtotime($_POST['task_duetime']));

		$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
		$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$lastInsertId";
		$url_tag = "email_task_" . $lastInsertId . "";

		createTrigger($url_link, $url_datetime, $url_tag);

	}elseif($task_emailnotifi == 4){

		$task_duedate_at = date("Y/m/d", strtotime($_POST['task_duedate'] . "-1 week"));
		$task_duetime_at = date("H:i:s", strtotime($_POST['task_duetime']));

		$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
		$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$lastInsertId";
		$url_tag = "email_task_" . $lastInsertId . "";

		createTrigger($url_link, $url_datetime, $url_tag);

	}elseif($task_emailnotifi == 5){
		$task_duedate_at = date("Y/m/d", strtotime($_POST['task_duedate'] . "-1 month"));
		$task_duetime_at = date("H:i:s", strtotime($_POST['task_duetime']));

		$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
		$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$lastInsertId";
		$url_tag = "email_task_" . $lastInsertId . "";

		createTrigger($url_link, $url_datetime, $url_tag);

	}

	header("Location: companies?page=open&id=$task_dataid&mess=8");

break;

case "add_task_done_company":

	$task_id = $_POST['task_id'];

	//Check for repeat tasks
	$query_tasks = $db->prepare("
							SELECT task_txt, task_duedatetime, task_assignedid, task_emailnotifi, task_repeatnotifi, task_dataid, task_employeeid
							FROM idk_tasks
							WHERE task_id = :task_id");

	$query_tasks->execute(array(
					':task_id' => $task_id));

	$row_tasks = $query_tasks->fetch();

		$task_txt = $row_tasks['task_txt'];
		$task_duedatetime = $row_tasks['task_duedatetime'];
		$task_assignedid = $row_tasks['task_assignedid'];
		$task_emailnotifi = $row_tasks['task_emailnotifi'];
		$task_repeatnotifi = $row_tasks['task_repeatnotifi'];
		$task_dataid = $row_tasks['task_dataid'];
		$task_employeeid = $row_tasks['task_employeeid'];

		//Remove Trigger
		if($task_emailnotifi != 0){
			$url_tag = "email_task_" . $task_id . "";
			removeTrigger($url_tag);
		}

		//Add done notification
		$notification_title = "Zadatak završen!";
		$notification_icon = "tasks";
		$notification_link = "" . getSiteURLr() . "tasks?page=open&id=" . $task_id . "";
		$task_datetime = date('Y-m-d H:i:s');

		$query_notification = $db->prepare("
								INSERT INTO idk_notifications
									(notification_datetime, notification_title, notification_icon, notification_link, notification_employeeid, notification_status)
								VALUES
									(:notification_datetime, :notification_title, :notification_icon, :notification_link, :notification_employeeid, :notification_status)");

		$query_notification->execute(array(
								':notification_datetime' => $task_datetime,
								':notification_title' => $notification_title,
								':notification_icon' => $notification_icon,
								':notification_link' => $notification_link,
								':notification_employeeid' => $task_employeeid,
								':notification_status' => 1));

	if($task_repeatnotifi == 0){

		$task_replytxt = $_POST['task_replytxt'];
		$task_status = 2;

		//Save
		$query = $db->prepare("
						UPDATE idk_tasks
						SET	task_replytxt = :task_replytxt, task_status = :task_status
						WHERE task_id = :task_id");

		$query->execute(array(
					':task_replytxt' => $task_replytxt,
					':task_status' => $task_status,
					':task_id' => $task_id));
	}else{
		$task_replytxt = $_POST['task_replytxt'];
		$task_status = 2;

		//Save
		$query = $db->prepare("
						UPDATE idk_tasks
						SET	task_replytxt = :task_replytxt, task_status = :task_status
						WHERE task_id = :task_id");

		$query->execute(array(
					':task_replytxt' => $task_replytxt,
					':task_status' => $task_status,
					':task_id' => $task_id));

		//Create repeat task
		$task_replytxt = "Novi zadatak";
		$task_datetime = date('Y-m-d H:i:s');
		$task_group = 3;
		$task_status = 1;

		if($task_repeatnotifi == 1){
			$task_duedatetime_new = date('Y-m-d H:i:s',strtotime($task_duedatetime . "+1 days"));
		}elseif($task_repeatnotifi == 2){
			$task_duedatetime_new = date('Y-m-d H:i:s',strtotime($task_duedatetime . "+7 days"));
		}elseif($task_repeatnotifi == 3){
			$task_duedatetime_new = date('Y-m-d H:i:s',strtotime($task_duedatetime . "+1 month"));
		}elseif($task_repeatnotifi == 4){
			$task_duedatetime_new = date('Y-m-d H:i:s',strtotime($task_duedatetime . "+1 years"));
		}

		$query = $db->prepare("
						INSERT INTO idk_tasks
							(task_txt, task_replytxt, task_duedatetime, task_assignedid, task_emailnotifi, task_repeatnotifi, task_group, task_dataid, task_employeeid, task_datetime, task_status)
						VALUES
							(:task_txt, :task_replytxt, :task_duedatetime, :task_assignedid, :task_emailnotifi, :task_repeatnotifi, :task_group, :task_dataid, :task_employeeid, :task_datetime, :task_status)");

		$query->execute(array(
					':task_txt' => $task_txt,
					':task_replytxt' => $task_replytxt,
					':task_duedatetime' => $task_duedatetime_new,
					':task_assignedid' => $task_assignedid,
					':task_emailnotifi' => $task_emailnotifi,
					':task_repeatnotifi' => $task_repeatnotifi,
					':task_group' => $task_group,
					':task_dataid' => $task_dataid,
					':task_employeeid' => $task_employeeid,
					':task_datetime' => $task_datetime,
					':task_status' => $task_status));

		$lastInsertId = $db->lastInsertId();

		//Add notification
		$notification_title = "Imate novi zadatak!";
		$notification_icon = "tasks";
		$notification_link = "" . getSiteURLr() . "tasks?page=open&id=" . $lastInsertId . "";

		$query_notification = $db->prepare("
								INSERT INTO idk_notifications
									(notification_datetime, notification_title, notification_icon, notification_link, notification_employeeid, notification_status)
								VALUES
									(:notification_datetime, :notification_title, :notification_icon, :notification_link, :notification_employeeid, :notification_status)");

		$query_notification->execute(array(
								':notification_datetime' => $task_datetime,
								':notification_title' => $notification_title,
								':notification_icon' => $notification_icon,
								':notification_link' => $notification_link,
								':notification_employeeid' => $task_assignedid,
								':notification_status' => 1));

		//create Trigger
		if($task_emailnotifi == 1){

			$task_duedate_at = date("Y/m/d", strtotime($task_duedatetime_new));
			$task_duetime_at = date("H:i:s", strtotime($task_duedatetime_new));

			$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
			$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$lastInsertId";
			$url_tag = "email_task_" . $lastInsertId . "";

			createTrigger($url_link, $url_datetime, $url_tag);

		}elseif($task_emailnotifi == 2){

			$task_duedate_at = date("Y/m/d", strtotime($task_duedatetime_new . "-1 days"));
			$task_duetime_at = date("H:i:s", strtotime($task_duedatetime_new));

			$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
			$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$lastInsertId";
			$url_tag = "email_task_" . $lastInsertId . "";

			createTrigger($url_link, $url_datetime, $url_tag);

		}elseif($task_emailnotifi == 3){

			$task_duedate_at = date("Y/m/d", strtotime($task_duedatetime_new . "-2 days"));
			$task_duetime_at = date("H:i:s", strtotime($task_duedatetime_new));

			$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
			$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$lastInsertId";
			$url_tag = "email_task_" . $lastInsertId . "";

			createTrigger($url_link, $url_datetime, $url_tag);

		}elseif($task_emailnotifi == 4){

			$task_duedate_at = date("Y/m/d", strtotime($task_duedatetime_new . "-1 week"));
			$task_duetime_at = date("H:i:s", strtotime($task_duedatetime_new));

			$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
			$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$lastInsertId";
			$url_tag = "email_task_" . $lastInsertId . "";

			createTrigger($url_link, $url_datetime, $url_tag);

		}elseif($task_emailnotifi == 5){
			$task_duedate_at = date("Y/m/d", strtotime($task_duedatetime_new . "-1 month"));
			$task_duetime_at = date("H:i:s", strtotime($task_duedatetime_new));

			$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
			$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$lastInsertId";
			$url_tag = "email_task_" . $lastInsertId . "";

			createTrigger($url_link, $url_datetime, $url_tag);

		}

	}

	header("Location: companies?page=open&id=$task_dataid&mess=9");

break;

case "add_task_notdone_company":

	$task_id = $_POST['task_id'];

	//Check for repeat tasks
	$query_tasks = $db->prepare("
							SELECT task_txt, task_duedatetime, task_assignedid, task_emailnotifi, task_repeatnotifi, task_dataid, task_employeeid
							FROM idk_tasks
							WHERE task_id = :task_id");

	$query_tasks->execute(array(
					':task_id' => $task_id));

	$row_tasks = $query_tasks->fetch();

		$task_txt = $row_tasks['task_txt'];
		$task_duedatetime = $row_tasks['task_duedatetime'];
		$task_assignedid = $row_tasks['task_assignedid'];
		$task_emailnotifi = $row_tasks['task_emailnotifi'];
		$task_repeatnotifi = $row_tasks['task_repeatnotifi'];
		$task_dataid = $row_tasks['task_dataid'];
		$task_employeeid = $row_tasks['task_employeeid'];

		//Add done notification
		$notification_title = "Zadatak nije završen!";
		$notification_icon = "tasks";
		$notification_link = "" . getSiteURLr() . "tasks?page=open&id=" . $task_id . "";
		$task_datetime = date('Y-m-d H:i:s');

		$query_notification = $db->prepare("
								INSERT INTO idk_notifications
									(notification_datetime, notification_title, notification_icon, notification_link, notification_employeeid, notification_status)
								VALUES
									(:notification_datetime, :notification_title, :notification_icon, :notification_link, :notification_employeeid, :notification_status)");

		$query_notification->execute(array(
								':notification_datetime' => $task_datetime,
								':notification_title' => $notification_title,
								':notification_icon' => $notification_icon,
								':notification_link' => $notification_link,
								':notification_employeeid' => $task_employeeid,
								':notification_status' => 1));

		//Remove Trigger
		if($task_emailnotifi != 0){
			$url_tag = "email_task_" . $task_id . "";
			removeTrigger($url_tag);
		}

	if($task_repeatnotifi == 0){

		$task_replytxt = $_POST['task_replytxt'];
		$task_status = 3;

		//Save
		$query = $db->prepare("
						UPDATE idk_tasks
						SET	task_replytxt = :task_replytxt, task_status = :task_status
						WHERE task_id = :task_id");

		$query->execute(array(
					':task_replytxt' => $task_replytxt,
					':task_status' => $task_status,
					':task_id' => $task_id));

	}else{
		$task_replytxt = $_POST['task_replytxt'];
		$task_status = 3;

		//Save
		$query = $db->prepare("
						UPDATE idk_tasks
						SET	task_replytxt = :task_replytxt, task_status = :task_status
						WHERE task_id = :task_id");

		$query->execute(array(
					':task_replytxt' => $task_replytxt,
					':task_status' => $task_status,
					':task_id' => $task_id));

		//Create repeat task
		$task_replytxt = "Novi zadatak";
		$task_datetime = date('Y-m-d H:i:s');
		$task_group = 3;
		$task_status = 1;

		if($task_repeatnotifi == 1){
			$task_duedatetime_new = date('Y-m-d H:i:s',strtotime($task_duedatetime . "+1 days"));
		}elseif($task_repeatnotifi == 2){
			$task_duedatetime_new = date('Y-m-d H:i:s',strtotime($task_duedatetime . "+7 days"));
		}elseif($task_repeatnotifi == 3){
			$task_duedatetime_new = date('Y-m-d H:i:s',strtotime($task_duedatetime . "+1 month"));
		}elseif($task_repeatnotifi == 4){
			$task_duedatetime_new = date('Y-m-d H:i:s',strtotime($task_duedatetime . "+1 years"));
		}

		$query = $db->prepare("
						INSERT INTO idk_tasks
							(task_txt, task_replytxt, task_duedatetime, task_assignedid, task_emailnotifi, task_repeatnotifi, task_group, task_dataid, task_employeeid, task_datetime, task_status)
						VALUES
							(:task_txt, :task_replytxt, :task_duedatetime, :task_assignedid, :task_emailnotifi, :task_repeatnotifi, :task_group, :task_dataid, :task_employeeid, :task_datetime, :task_status)");

		$query->execute(array(
					':task_txt' => $task_txt,
					':task_replytxt' => $task_replytxt,
					':task_duedatetime' => $task_duedatetime_new,
					':task_assignedid' => $task_assignedid,
					':task_emailnotifi' => $task_emailnotifi,
					':task_repeatnotifi' => $task_repeatnotifi,
					':task_group' => $task_group,
					':task_dataid' => $task_dataid,
					':task_employeeid' => $task_employeeid,
					':task_datetime' => $task_datetime,
					':task_status' => $task_status));

		$lastInsertId = $db->lastInsertId();

		//Add notification
		$notification_title = "Imate novi zadatak!";
		$notification_icon = "tasks";
		$notification_link = "" . getSiteURLr() . "tasks?page=open&id=" . $lastInsertId . "";

		$query_notification = $db->prepare("
								INSERT INTO idk_notifications
									(notification_datetime, notification_title, notification_icon, notification_link, notification_employeeid, notification_status)
								VALUES
									(:notification_datetime, :notification_title, :notification_icon, :notification_link, :notification_employeeid, :notification_status)");

		$query_notification->execute(array(
								':notification_datetime' => $task_datetime,
								':notification_title' => $notification_title,
								':notification_icon' => $notification_icon,
								':notification_link' => $notification_link,
								':notification_employeeid' => $task_assignedid,
								':notification_status' => 1));

		//create Trigger
		if($task_emailnotifi == 1){

			$task_duedate_at = date("Y/m/d", strtotime($task_duedatetime_new));
			$task_duetime_at = date("H:i:s", strtotime($task_duedatetime_new));

			$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
			$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$lastInsertId";
			$url_tag = "email_task_" . $lastInsertId . "";

			createTrigger($url_link, $url_datetime, $url_tag);

		}elseif($task_emailnotifi == 2){

			$task_duedate_at = date("Y/m/d", strtotime($task_duedatetime_new . "-1 days"));
			$task_duetime_at = date("H:i:s", strtotime($task_duedatetime_new));

			$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
			$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$lastInsertId";
			$url_tag = "email_task_" . $lastInsertId . "";

			createTrigger($url_link, $url_datetime, $url_tag);

		}elseif($task_emailnotifi == 3){

			$task_duedate_at = date("Y/m/d", strtotime($task_duedatetime_new . "-2 days"));
			$task_duetime_at = date("H:i:s", strtotime($task_duedatetime_new));

			$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
			$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$lastInsertId";
			$url_tag = "email_task_" . $lastInsertId . "";

			createTrigger($url_link, $url_datetime, $url_tag);

		}elseif($task_emailnotifi == 4){

			$task_duedate_at = date("Y/m/d", strtotime($task_duedatetime_new . "-1 week"));
			$task_duetime_at = date("H:i:s", strtotime($task_duedatetime_new));

			$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
			$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$lastInsertId";
			$url_tag = "email_task_" . $lastInsertId . "";

			createTrigger($url_link, $url_datetime, $url_tag);

		}elseif($task_emailnotifi == 5){
			$task_duedate_at = date("Y/m/d", strtotime($task_duedatetime_new . "-1 month"));
			$task_duetime_at = date("H:i:s", strtotime($task_duedatetime_new));

			$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
			$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$lastInsertId";
			$url_tag = "email_task_" . $lastInsertId . "";

			createTrigger($url_link, $url_datetime, $url_tag);

		}

	}

	header("Location: companies?page=open&id=$task_dataid&mess=10");

break;

case "add_task_emailnotifi_company":

	$task_id = $_POST['task_id'];
	$task_emailnotifi = $_POST['task_emailnotifi'];
	$task_dataid = $_POST['task_dataid'];

	//Get data from tasks
	$query_tasks = $db->prepare("
							SELECT task_duedatetime, task_assignedid
							FROM idk_tasks
							WHERE task_id = :task_id");

	$query_tasks->execute(array(
					':task_id' => $task_id));

	$row_tasks = $query_tasks->fetch();

		$task_assignedid = $row_tasks['task_assignedid'];

	//Save
	$query = $db->prepare("
					UPDATE idk_tasks
					SET	task_emailnotifi = :task_emailnotifi
					WHERE task_id = :task_id");

	$query->execute(array(
				':task_emailnotifi' => $task_emailnotifi,
				':task_id' => $task_id));

	//Email Trigger
	$url_tag = "email_task_" . $task_id . "";
	removeTrigger($url_tag);

	if($task_emailnotifi == 1){

			$task_duedate_at = date("Y/m/d", strtotime($row_tasks['task_duedatetime']));
			$task_duetime_at = date("H:i:s", strtotime($row_tasks['task_duedatetime']));

			$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
			$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$task_id";
			$url_tag = "email_task_" . $task_id . "";

			createTrigger($url_link, $url_datetime, $url_tag);

		}elseif($task_emailnotifi == 2){

			$task_duedate_at = date("Y/m/d", strtotime($row_tasks['task_duedatetime'] . "-1 days"));
			$task_duetime_at = date("H:i:s", strtotime($row_tasks['task_duedatetime']));

			$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
			$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$task_id";
			$url_tag = "email_task_" . $task_id . "";

			createTrigger($url_link, $url_datetime, $url_tag);

		}elseif($task_emailnotifi == 3){

			$task_duedate_at = date("Y/m/d", strtotime($row_tasks['task_duedatetime'] . "-2 days"));
			$task_duetime_at = date("H:i:s", strtotime($row_tasks['task_duedatetime']));

			$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
			$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$task_id";
			$url_tag = "email_task_" . $task_id . "";

			createTrigger($url_link, $url_datetime, $url_tag);

		}elseif($task_emailnotifi == 4){

			$task_duedate_at = date("Y/m/d", strtotime($row_tasks['task_duedatetime'] . "-1 week"));
			$task_duetime_at = date("H:i:s", strtotime($row_tasks['task_duedatetime']));

			$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
			$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$task_id";
			$url_tag = "email_task_" . $task_id . "";

			createTrigger($url_link, $url_datetime, $url_tag);

		}elseif($task_emailnotifi == 5){
			$task_duedate_at = date("Y/m/d", strtotime($row_tasks['task_duedatetime'] . "-1 month"));
			$task_duetime_at = date("H:i:s", strtotime($row_tasks['task_duedatetime']));

			$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
			$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$task_id";
			$url_tag = "email_task_" . $task_id . "";

			createTrigger($url_link, $url_datetime, $url_tag);

		}

	header("Location: companies?page=open&id=$task_dataid&mess=12");

break;

case "add_task_repeat_company":

	$task_id = $_POST['task_id'];
	$task_repeatnotifi = $_POST['task_repeatnotifi'];
	$task_dataid = $_POST['task_dataid'];

	//Save
	$query = $db->prepare("
					UPDATE idk_tasks
					SET	task_repeatnotifi = :task_repeatnotifi
					WHERE task_id = :task_id");

	$query->execute(array(
				':task_repeatnotifi' => $task_repeatnotifi,
				':task_id' => $task_id));

	header("Location: companies?page=open&id=$task_dataid&mess=13");

break;

case "add_task_employee":

	$task_txt = $_POST['task_txt'];
	$task_replytxt = "Novi zadatak";
	$task_duedate = date("Y-m-d", strtotime($_POST['task_duedate']));
	$task_duetime = date("H:i:s", strtotime($_POST['task_duetime']));
	$task_duedatetime = $task_duedate . ' ' . $task_duetime;
	$task_assignedid = $_POST['task_assignedid'];
	$task_emailnotifi = $_POST['task_emailnotifi'];
	$task_repeatnotifi = $_POST['task_repeatnotifi'];
	$task_group = 2;
	$task_dataid = $_POST['task_dataid'];
	$task_datetime = date('Y-m-d H:i:s');
	$task_status = 1;

	$query = $db->prepare("
					INSERT INTO idk_tasks
						(task_txt, task_replytxt, task_duedatetime, task_assignedid, task_emailnotifi, task_repeatnotifi, task_group, task_dataid, task_employeeid, task_datetime, task_status)
					VALUES
						(:task_txt, :task_replytxt, :task_duedatetime, :task_assignedid, :task_emailnotifi, :task_repeatnotifi, :task_group, :task_dataid, :task_employeeid, :task_datetime, :task_status)");

	$query->execute(array(
				':task_txt' => $task_txt,
				':task_replytxt' => $task_replytxt,
				':task_duedatetime' => $task_duedatetime,
				':task_assignedid' => $task_assignedid,
				':task_emailnotifi' => $task_emailnotifi,
				':task_repeatnotifi' => $task_repeatnotifi,
				':task_group' => $task_group,
				':task_dataid' => $task_dataid,
				':task_employeeid' => $logged_employee_id,
				':task_datetime' => $task_datetime,
				':task_status' => $task_status));

	$lastInsertId = $db->lastInsertId();

	//Add notification
	$notification_title = "Imate novi zadatak!";
	$notification_icon = "tasks";
	$notification_link = "" . getSiteURLr() . "tasks?page=open&id=" . $lastInsertId . "";

	$query_notification = $db->prepare("
							INSERT INTO idk_notifications
								(notification_datetime, notification_title, notification_icon, notification_link, notification_employeeid, notification_status)
							VALUES
								(:notification_datetime, :notification_title, :notification_icon, :notification_link, :notification_employeeid, :notification_status)");

	$query_notification->execute(array(
							':notification_datetime' => $task_datetime,
							':notification_title' => $notification_title,
							':notification_icon' => $notification_icon,
							':notification_link' => $notification_link,
							':notification_employeeid' => $task_assignedid,
							':notification_status' => 1));

	//Email Trigger
	if($task_emailnotifi == 1){

		$task_duedate_at = date("Y/m/d", strtotime($_POST['task_duedate']));
		$task_duetime_at = date("H:i:s", strtotime($_POST['task_duetime']));

		$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
		$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$lastInsertId";
		$url_tag = "email_task_" . $lastInsertId . "";

		createTrigger($url_link, $url_datetime, $url_tag);

	}elseif($task_emailnotifi == 2){

		$task_duedate_at = date("Y/m/d", strtotime($_POST['task_duedate'] . "-1 days"));
		$task_duetime_at = date("H:i:s", strtotime($_POST['task_duetime']));

		$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
		$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$lastInsertId";
		$url_tag = "email_task_" . $lastInsertId . "";

		createTrigger($url_link, $url_datetime, $url_tag);

	}elseif($task_emailnotifi == 3){

		$task_duedate_at = date("Y/m/d", strtotime($_POST['task_duedate'] . "-2 days"));
		$task_duetime_at = date("H:i:s", strtotime($_POST['task_duetime']));

		$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
		$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$lastInsertId";
		$url_tag = "email_task_" . $lastInsertId . "";

		createTrigger($url_link, $url_datetime, $url_tag);

	}elseif($task_emailnotifi == 4){

		$task_duedate_at = date("Y/m/d", strtotime($_POST['task_duedate'] . "-1 week"));
		$task_duetime_at = date("H:i:s", strtotime($_POST['task_duetime']));

		$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
		$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$lastInsertId";
		$url_tag = "email_task_" . $lastInsertId . "";

		createTrigger($url_link, $url_datetime, $url_tag);

	}elseif($task_emailnotifi == 5){
		$task_duedate_at = date("Y/m/d", strtotime($_POST['task_duedate'] . "-1 month"));
		$task_duetime_at = date("H:i:s", strtotime($_POST['task_duetime']));

		$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
		$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$lastInsertId";
		$url_tag = "email_task_" . $lastInsertId . "";

		createTrigger($url_link, $url_datetime, $url_tag);

	}

	header("Location: employees?page=open&id=$task_dataid&mess=8");

break;

case "add_task_done_employee":

	$task_id = $_POST['task_id'];

	//Check for repeat tasks
	$query_tasks = $db->prepare("
							SELECT task_txt, task_duedatetime, task_assignedid, task_emailnotifi, task_repeatnotifi, task_dataid, task_employeeid
							FROM idk_tasks
							WHERE task_id = :task_id");

	$query_tasks->execute(array(
					':task_id' => $task_id));

	$row_tasks = $query_tasks->fetch();

		$task_txt = $row_tasks['task_txt'];
		$task_duedatetime = $row_tasks['task_duedatetime'];
		$task_assignedid = $row_tasks['task_assignedid'];
		$task_emailnotifi = $row_tasks['task_emailnotifi'];
		$task_repeatnotifi = $row_tasks['task_repeatnotifi'];
		$task_dataid = $row_tasks['task_dataid'];
		$task_employeeid = $row_tasks['task_employeeid'];

		//Remove Trigger
		if($task_emailnotifi != 0){
			$url_tag = "email_task_" . $task_id . "";
			removeTrigger($url_tag);
		}

	if($task_repeatnotifi == 0){

		$task_replytxt = $_POST['task_replytxt'];
		$task_status = 2;

		//Save
		$query = $db->prepare("
						UPDATE idk_tasks
						SET	task_replytxt = :task_replytxt, task_status = :task_status
						WHERE task_id = :task_id");

		$query->execute(array(
					':task_replytxt' => $task_replytxt,
					':task_status' => $task_status,
					':task_id' => $task_id));
	}else{
		$task_replytxt = $_POST['task_replytxt'];
		$task_status = 2;

		//Save
		$query = $db->prepare("
						UPDATE idk_tasks
						SET	task_replytxt = :task_replytxt, task_status = :task_status
						WHERE task_id = :task_id");

		$query->execute(array(
					':task_replytxt' => $task_replytxt,
					':task_status' => $task_status,
					':task_id' => $task_id));

		//Create repeat task
		$task_replytxt = "Novi zadatak";
		$task_datetime = date('Y-m-d H:i:s');
		$task_group = 2;
		$task_status = 1;

		if($task_repeatnotifi == 1){
			$task_duedatetime_new = date('Y-m-d H:i:s',strtotime($task_duedatetime . "+1 days"));
		}elseif($task_repeatnotifi == 2){
			$task_duedatetime_new = date('Y-m-d H:i:s',strtotime($task_duedatetime . "+7 days"));
		}elseif($task_repeatnotifi == 3){
			$task_duedatetime_new = date('Y-m-d H:i:s',strtotime($task_duedatetime . "+1 month"));
		}elseif($task_repeatnotifi == 4){
			$task_duedatetime_new = date('Y-m-d H:i:s',strtotime($task_duedatetime . "+1 years"));
		}

		$query = $db->prepare("
						INSERT INTO idk_tasks
							(task_txt, task_replytxt, task_duedatetime, task_assignedid, task_emailnotifi, task_repeatnotifi, task_group, task_dataid, task_employeeid, task_datetime, task_status)
						VALUES
							(:task_txt, :task_replytxt, :task_duedatetime, :task_assignedid, :task_emailnotifi, :task_repeatnotifi, :task_group, :task_dataid, :task_employeeid, :task_datetime, :task_status)");

		$query->execute(array(
					':task_txt' => $task_txt,
					':task_replytxt' => $task_replytxt,
					':task_duedatetime' => $task_duedatetime_new,
					':task_assignedid' => $task_assignedid,
					':task_emailnotifi' => $task_emailnotifi,
					':task_repeatnotifi' => $task_repeatnotifi,
					':task_group' => $task_group,
					':task_dataid' => $task_dataid,
					':task_employeeid' => $task_employeeid,
					':task_datetime' => $task_datetime,
					':task_status' => $task_status));

		$lastInsertId = $db->lastInsertId();

		//Add notification
		$notification_title = "Imate novi zadatak!";
		$notification_icon = "tasks";
		$notification_link = "" . getSiteURLr() . "tasks?page=open&id=" . $lastInsertId . "";

		$query_notification = $db->prepare("
								INSERT INTO idk_notifications
									(notification_datetime, notification_title, notification_icon, notification_link, notification_employeeid, notification_status)
								VALUES
									(:notification_datetime, :notification_title, :notification_icon, :notification_link, :notification_employeeid, :notification_status)");

		$query_notification->execute(array(
								':notification_datetime' => $task_datetime,
								':notification_title' => $notification_title,
								':notification_icon' => $notification_icon,
								':notification_link' => $notification_link,
								':notification_employeeid' => $task_assignedid,
								':notification_status' => 1));

		//create Trigger
		if($task_emailnotifi == 1){

			$task_duedate_at = date("Y/m/d", strtotime($task_duedatetime_new));
			$task_duetime_at = date("H:i:s", strtotime($task_duedatetime_new));

			$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
			$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$lastInsertId";
			$url_tag = "email_task_" . $lastInsertId . "";

			createTrigger($url_link, $url_datetime, $url_tag);

		}elseif($task_emailnotifi == 2){

			$task_duedate_at = date("Y/m/d", strtotime($task_duedatetime_new . "-1 days"));
			$task_duetime_at = date("H:i:s", strtotime($task_duedatetime_new));

			$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
			$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$lastInsertId";
			$url_tag = "email_task_" . $lastInsertId . "";

			createTrigger($url_link, $url_datetime, $url_tag);

		}elseif($task_emailnotifi == 3){

			$task_duedate_at = date("Y/m/d", strtotime($task_duedatetime_new . "-2 days"));
			$task_duetime_at = date("H:i:s", strtotime($task_duedatetime_new));

			$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
			$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$lastInsertId";
			$url_tag = "email_task_" . $lastInsertId . "";

			createTrigger($url_link, $url_datetime, $url_tag);

		}elseif($task_emailnotifi == 4){

			$task_duedate_at = date("Y/m/d", strtotime($task_duedatetime_new . "-1 week"));
			$task_duetime_at = date("H:i:s", strtotime($task_duedatetime_new));

			$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
			$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$lastInsertId";
			$url_tag = "email_task_" . $lastInsertId . "";

			createTrigger($url_link, $url_datetime, $url_tag);

		}elseif($task_emailnotifi == 5){
			$task_duedate_at = date("Y/m/d", strtotime($task_duedatetime_new . "-1 month"));
			$task_duetime_at = date("H:i:s", strtotime($task_duedatetime_new));

			$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
			$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$lastInsertId";
			$url_tag = "email_task_" . $lastInsertId . "";

			createTrigger($url_link, $url_datetime, $url_tag);

		}

	}

	header("Location: employees?page=open&id=$task_dataid&mess=9");

break;

case "add_task_notdone_employee":

	$task_id = $_POST['task_id'];

	//Check for repeat tasks
	$query_tasks = $db->prepare("
							SELECT task_txt, task_duedatetime, task_assignedid, task_emailnotifi, task_repeatnotifi, task_dataid, task_employeeid
							FROM idk_tasks
							WHERE task_id = :task_id");

	$query_tasks->execute(array(
					':task_id' => $task_id));

	$row_tasks = $query_tasks->fetch();

		$task_txt = $row_tasks['task_txt'];
		$task_duedatetime = $row_tasks['task_duedatetime'];
		$task_assignedid = $row_tasks['task_assignedid'];
		$task_emailnotifi = $row_tasks['task_emailnotifi'];
		$task_repeatnotifi = $row_tasks['task_repeatnotifi'];
		$task_dataid = $row_tasks['task_dataid'];
		$task_employeeid = $row_tasks['task_employeeid'];

		//Remove Trigger
		if($task_emailnotifi != 0){
			$url_tag = "email_task_" . $task_id . "";
			removeTrigger($url_tag);
		}

	if($task_repeatnotifi == 0){

		$task_replytxt = $_POST['task_replytxt'];
		$task_status = 3;

		//Save
		$query = $db->prepare("
						UPDATE idk_tasks
						SET	task_replytxt = :task_replytxt, task_status = :task_status
						WHERE task_id = :task_id");

		$query->execute(array(
					':task_replytxt' => $task_replytxt,
					':task_status' => $task_status,
					':task_id' => $task_id));

	}else{
		$task_replytxt = $_POST['task_replytxt'];
		$task_status = 3;

		//Save
		$query = $db->prepare("
						UPDATE idk_tasks
						SET	task_replytxt = :task_replytxt, task_status = :task_status
						WHERE task_id = :task_id");

		$query->execute(array(
					':task_replytxt' => $task_replytxt,
					':task_status' => $task_status,
					':task_id' => $task_id));

		//Create repeat task
		$task_replytxt = "Novi zadatak";
		$task_datetime = date('Y-m-d H:i:s');
		$task_group = 2;
		$task_status = 1;

		if($task_repeatnotifi == 1){
			$task_duedatetime_new = date('Y-m-d H:i:s',strtotime($task_duedatetime . "+1 days"));
		}elseif($task_repeatnotifi == 2){
			$task_duedatetime_new = date('Y-m-d H:i:s',strtotime($task_duedatetime . "+7 days"));
		}elseif($task_repeatnotifi == 3){
			$task_duedatetime_new = date('Y-m-d H:i:s',strtotime($task_duedatetime . "+1 month"));
		}elseif($task_repeatnotifi == 4){
			$task_duedatetime_new = date('Y-m-d H:i:s',strtotime($task_duedatetime . "+1 years"));
		}

		$query = $db->prepare("
						INSERT INTO idk_tasks
							(task_txt, task_replytxt, task_duedatetime, task_assignedid, task_emailnotifi, task_repeatnotifi, task_group, task_dataid, task_employeeid, task_datetime, task_status)
						VALUES
							(:task_txt, :task_replytxt, :task_duedatetime, :task_assignedid, :task_emailnotifi, :task_repeatnotifi, :task_group, :task_dataid, :task_employeeid, :task_datetime, :task_status)");

		$query->execute(array(
					':task_txt' => $task_txt,
					':task_replytxt' => $task_replytxt,
					':task_duedatetime' => $task_duedatetime_new,
					':task_assignedid' => $task_assignedid,
					':task_emailnotifi' => $task_emailnotifi,
					':task_repeatnotifi' => $task_repeatnotifi,
					':task_group' => $task_group,
					':task_dataid' => $task_dataid,
					':task_employeeid' => $task_employeeid,
					':task_datetime' => $task_datetime,
					':task_status' => $task_status));

		$lastInsertId = $db->lastInsertId();

		//Add notification
		$notification_title = "Imate novi zadatak!";
		$notification_icon = "tasks";
		$notification_link = "" . getSiteURLr() . "tasks?page=open&id=" . $lastInsertId . "";

		$query_notification = $db->prepare("
								INSERT INTO idk_notifications
									(notification_datetime, notification_title, notification_icon, notification_link, notification_employeeid, notification_status)
								VALUES
									(:notification_datetime, :notification_title, :notification_icon, :notification_link, :notification_employeeid, :notification_status)");

		$query_notification->execute(array(
								':notification_datetime' => $task_datetime,
								':notification_title' => $notification_title,
								':notification_icon' => $notification_icon,
								':notification_link' => $notification_link,
								':notification_employeeid' => $task_assignedid,
								':notification_status' => 1));

		//create Trigger
		if($task_emailnotifi == 1){

			$task_duedate_at = date("Y/m/d", strtotime($task_duedatetime_new));
			$task_duetime_at = date("H:i:s", strtotime($task_duedatetime_new));

			$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
			$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$lastInsertId";
			$url_tag = "email_task_" . $lastInsertId . "";

			createTrigger($url_link, $url_datetime, $url_tag);

		}elseif($task_emailnotifi == 2){

			$task_duedate_at = date("Y/m/d", strtotime($task_duedatetime_new . "-1 days"));
			$task_duetime_at = date("H:i:s", strtotime($task_duedatetime_new));

			$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
			$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$lastInsertId";
			$url_tag = "email_task_" . $lastInsertId . "";

			createTrigger($url_link, $url_datetime, $url_tag);

		}elseif($task_emailnotifi == 3){

			$task_duedate_at = date("Y/m/d", strtotime($task_duedatetime_new . "-2 days"));
			$task_duetime_at = date("H:i:s", strtotime($task_duedatetime_new));

			$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
			$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$lastInsertId";
			$url_tag = "email_task_" . $lastInsertId . "";

			createTrigger($url_link, $url_datetime, $url_tag);

		}elseif($task_emailnotifi == 4){

			$task_duedate_at = date("Y/m/d", strtotime($task_duedatetime_new . "-1 week"));
			$task_duetime_at = date("H:i:s", strtotime($task_duedatetime_new));

			$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
			$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$lastInsertId";
			$url_tag = "email_task_" . $lastInsertId . "";

			createTrigger($url_link, $url_datetime, $url_tag);

		}elseif($task_emailnotifi == 5){
			$task_duedate_at = date("Y/m/d", strtotime($task_duedatetime_new . "-1 month"));
			$task_duetime_at = date("H:i:s", strtotime($task_duedatetime_new));

			$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
			$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$lastInsertId";
			$url_tag = "email_task_" . $lastInsertId . "";

			createTrigger($url_link, $url_datetime, $url_tag);

		}

	}

	header("Location: employees?page=open&id=$task_dataid&mess=10");

break;

case "add_task_emailnotifi_employee":

	$task_id = $_POST['task_id'];
	$task_emailnotifi = $_POST['task_emailnotifi'];
	$task_dataid = $_POST['task_dataid'];

	//Get data from tasks
	$query_tasks = $db->prepare("
							SELECT task_duedatetime, task_assignedid
							FROM idk_tasks
							WHERE task_id = :task_id");

	$query_tasks->execute(array(
					':task_id' => $task_id));

	$row_tasks = $query_tasks->fetch();

		$task_assignedid = $row_tasks['task_assignedid'];

	//Save
	$query = $db->prepare("
					UPDATE idk_tasks
					SET	task_emailnotifi = :task_emailnotifi
					WHERE task_id = :task_id");

	$query->execute(array(
				':task_emailnotifi' => $task_emailnotifi,
				':task_id' => $task_id));

	//Email Trigger
	$url_tag = "email_task_" . $task_id . "";
	removeTrigger($url_tag);

	if($task_emailnotifi == 1){

			$task_duedate_at = date("Y/m/d", strtotime($row_tasks['task_duedatetime']));
			$task_duetime_at = date("H:i:s", strtotime($row_tasks['task_duedatetime']));

			$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
			$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$task_id";
			$url_tag = "email_task_" . $task_id . "";

			createTrigger($url_link, $url_datetime, $url_tag);

		}elseif($task_emailnotifi == 2){

			$task_duedate_at = date("Y/m/d", strtotime($row_tasks['task_duedatetime'] . "-1 days"));
			$task_duetime_at = date("H:i:s", strtotime($row_tasks['task_duedatetime']));

			$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
			$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$task_id";
			$url_tag = "email_task_" . $task_id . "";

			createTrigger($url_link, $url_datetime, $url_tag);

		}elseif($task_emailnotifi == 3){

			$task_duedate_at = date("Y/m/d", strtotime($row_tasks['task_duedatetime'] . "-2 days"));
			$task_duetime_at = date("H:i:s", strtotime($row_tasks['task_duedatetime']));

			$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
			$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$task_id";
			$url_tag = "email_task_" . $task_id . "";

			createTrigger($url_link, $url_datetime, $url_tag);

		}elseif($task_emailnotifi == 4){

			$task_duedate_at = date("Y/m/d", strtotime($row_tasks['task_duedatetime'] . "-1 week"));
			$task_duetime_at = date("H:i:s", strtotime($row_tasks['task_duedatetime']));

			$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
			$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$task_id";
			$url_tag = "email_task_" . $task_id . "";

			createTrigger($url_link, $url_datetime, $url_tag);

		}elseif($task_emailnotifi == 5){
			$task_duedate_at = date("Y/m/d", strtotime($row_tasks['task_duedatetime'] . "-1 month"));
			$task_duetime_at = date("H:i:s", strtotime($row_tasks['task_duedatetime']));

			$url_datetime = $task_duedate_at . ':' . $task_duetime_at;
			$url_link = "" . getSiteURLr() . "trigger.php?action=new_task&id=$task_id";
			$url_tag = "email_task_" . $task_id . "";

			createTrigger($url_link, $url_datetime, $url_tag);

		}

	header("Location: employees?page=open&id=$task_dataid&mess=12");

break;

case "add_task_repeat_employee":

	$task_id = $_POST['task_id'];
	$task_repeatnotifi = $_POST['task_repeatnotifi'];
	$task_dataid = $_POST['task_dataid'];

	//Save
	$query = $db->prepare("
					UPDATE idk_tasks
					SET	task_repeatnotifi = :task_repeatnotifi
					WHERE task_id = :task_id");

	$query->execute(array(
				':task_repeatnotifi' => $task_repeatnotifi,
				':task_id' => $task_id));

	header("Location: employees?page=open&id=$task_dataid&mess=13");

break;

case "add_contact_phone":

	$ci_group = 1;
	$ci_title = $_POST['ci_title'];
	$ci_data = $_POST['ci_data'];
	$ci_primary = 0;
	$ci_contactid = $_POST['ci_contactid'];


	$query = $db->prepare("
					INSERT INTO idk_contacts_info
						(ci_group, ci_title, ci_data, ci_primary, ci_contactid)
					VALUES
						(:ci_group, :ci_title, :ci_data, :ci_primary, :ci_contactid)");

	$query->execute(array(
				':ci_group' => $ci_group,
				':ci_title' => $ci_title,
				':ci_data' => $ci_data,
				':ci_primary' => $ci_primary,
				':ci_contactid' => $ci_contactid));

	header("Location: contacts?page=open&id=$ci_contactid&mess=14");

break;

case "set_primary_phone":

	$ci_id = $_GET['ci_id'];
	$ci_contactid = $_GET['contact_id'];

	//Remove default primary phone
	$query = $db->prepare("
					UPDATE idk_contacts_info
					SET	ci_primary = :ci_primary
					WHERE ci_contactid = :ci_contactid AND ci_primary = :ci_primary_current AND ci_group = :ci_group");

	$query->execute(array(
				':ci_primary' => 0,
				':ci_group' => 1,
				':ci_contactid' => $ci_contactid,
				':ci_primary_current' => 1));

	//Add primary phone
	$query = $db->prepare("
					UPDATE idk_contacts_info
					SET	ci_primary = :ci_primary
					WHERE ci_id = :ci_id");

	$query->execute(array(
				':ci_primary' => 1,
				':ci_id' => $ci_id));

	header("Location: contacts?page=open&id=$ci_contactid&mess=15");

break;

case "add_company_phone":

	$comi_group = 1;
	$comi_title = $_POST['comi_title'];
	$comi_data = $_POST['comi_data'];
	$comi_primary = 0;
	$comi_companyid = $_POST['comi_companyid'];


	$query = $db->prepare("
					INSERT INTO idk_companies_info
						(comi_group, comi_title, comi_data, comi_primary, comi_companyid)
					VALUES
						(:comi_group, :comi_title, :comi_data, :comi_primary, :comi_companyid)");

	$query->execute(array(
				':comi_group' => $comi_group,
				':comi_title' => $comi_title,
				':comi_data' => $comi_data,
				':comi_primary' => $comi_primary,
				':comi_companyid' => $comi_companyid));

	header("Location: companies?page=open&id=$comi_companyid&mess=14");

break;

case "set_primary_phone_company":

	$comi_id = $_GET['comi_id'];
	$comi_companyid = $_GET['comi_companyid'];

	//Remove default primary phone
	$query = $db->prepare("
					UPDATE idk_companies_info
					SET	comi_primary = :comi_primary
					WHERE comi_companyid = :comi_companyid AND comi_primary = :comi_primary_current AND comi_group = :comi_group");

	$query->execute(array(
				':comi_primary' => 0,
				':comi_group' => 1,
				':comi_companyid' => $comi_companyid,
				':comi_primary_current' => 1));

	//Add primary phone
	$query = $db->prepare("
					UPDATE idk_companies_info
					SET	comi_primary = :comi_primary
					WHERE comi_id = :comi_id");

	$query->execute(array(
				':comi_primary' => 1,
				':comi_id' => $comi_id));

	header("Location: companies?page=open&id=$comi_companyid&mess=15");

break;

case "add_employee_phone":

	$ei_group = 1;
	$ei_title = $_POST['ei_title'];
	$ei_data = $_POST['ei_data'];
	$ei_primary = 0;
	$ei_employeeid = $_POST['ei_employeeid'];


	$query = $db->prepare("
					INSERT INTO idk_employees_info
						(ei_group, ei_title, ei_data, ei_primary, ei_employeeid)
					VALUES
						(:ei_group, :ei_title, :ei_data, :ei_primary, :ei_employeeid)");

	$query->execute(array(
				':ei_group' => $ei_group,
				':ei_title' => $ei_title,
				':ei_data' => $ei_data,
				':ei_primary' => $ei_primary,
				':ei_employeeid' => $ei_employeeid));

	header("Location: employees?page=open&id=$ei_employeeid&mess=14");

break;

case "set_primary_phone_employee":

	$ei_id = $_GET['ei_id'];
	$ei_employeeid = $_GET['employee_id'];

	//Remove default primary phone
	$query = $db->prepare("
					UPDATE idk_employees_info
					SET	ei_primary = :ei_primary
					WHERE ei_employeeid = :ei_employeeid AND ei_primary = :ei_primary_current AND ei_group = :ei_group");

	$query->execute(array(
				':ei_primary' => 0,
				':ei_group' => 1,
				':ei_employeeid' => $ei_employeeid,
				':ei_primary_current' => 1));

	//Add primary phone
	$query = $db->prepare("
					UPDATE idk_employees_info
					SET	ei_primary = :ei_primary
					WHERE ei_id = :ei_id");

	$query->execute(array(
				':ei_primary' => 1,
				':ei_id' => $ei_id));

	header("Location: employees?page=open&id=$ei_employeeid&mess=15");

break;

case "add_contact_email":

	$ci_group = 2;
	$ci_title = $_POST['ci_title'];
	$ci_data = $_POST['ci_data'];
	$ci_primary = 0;
	$ci_contactid = $_POST['ci_contactid'];


	$query = $db->prepare("
					INSERT INTO idk_contacts_info
						(ci_group, ci_title, ci_data, ci_primary, ci_contactid)
					VALUES
						(:ci_group, :ci_title, :ci_data, :ci_primary, :ci_contactid)");

	$query->execute(array(
				':ci_group' => $ci_group,
				':ci_title' => $ci_title,
				':ci_data' => $ci_data,
				':ci_primary' => $ci_primary,
				':ci_contactid' => $ci_contactid));

	header("Location: contacts?page=open&id=$ci_contactid&mess=17");

break;

case "set_primary_email":

	$ci_id = $_GET['ci_id'];
	$ci_contactid = $_GET['contact_id'];

	//Remove default primary phone
	$query = $db->prepare("
					UPDATE idk_contacts_info
					SET	ci_primary = :ci_primary
					WHERE ci_contactid = :ci_contactid AND ci_primary = :ci_primary_current AND ci_group = :ci_group");

	$query->execute(array(
				':ci_primary' => 0,
				':ci_group' => 2,
				':ci_contactid' => $ci_contactid,
				':ci_primary_current' => 1));

	//Add primary phone
	$query = $db->prepare("
					UPDATE idk_contacts_info
					SET	ci_primary = :ci_primary
					WHERE ci_id = :ci_id");

	$query->execute(array(
				':ci_primary' => 1,
				':ci_id' => $ci_id));

	header("Location: contacts?page=open&id=$ci_contactid&mess=18");

break;

case "add_company_email":

	$comi_group = 2;
	$comi_title = $_POST['comi_title'];
	$comi_data = $_POST['comi_data'];
	$comi_primary = 0;
	$comi_companyid = $_POST['comi_companyid'];


	$query = $db->prepare("
					INSERT INTO idk_companies_info
						(comi_group, comi_title, comi_data, comi_primary, comi_companyid)
					VALUES
						(:comi_group, :comi_title, :comi_data, :comi_primary, :comi_companyid)");

	$query->execute(array(
				':comi_group' => $comi_group,
				':comi_title' => $comi_title,
				':comi_data' => $comi_data,
				':comi_primary' => $comi_primary,
				':comi_companyid' => $comi_companyid));

	header("Location: companies?page=open&id=$comi_companyid&mess=17");

break;

case "set_primary_email_company":

	$comi_id = $_GET['comi_id'];
	$comi_companyid = $_GET['comi_companyid'];

	//Remove default primary phone
	$query = $db->prepare("
					UPDATE idk_companies_info
					SET	comi_primary = :comi_primary
					WHERE comi_companyid = :comi_companyid AND comi_primary = :comi_primary_current AND comi_group = :comi_group");

	$query->execute(array(
				':comi_primary' => 0,
				':comi_group' => 2,
				':comi_companyid' => $comi_companyid,
				':comi_primary_current' => 1));

	//Add primary phone
	$query = $db->prepare("
					UPDATE idk_companies_info
					SET	comi_primary = :comi_primary
					WHERE comi_id = :comi_id");

	$query->execute(array(
				':comi_primary' => 1,
				':comi_id' => $comi_id));

	header("Location: companies?page=open&id=$comi_companyid&mess=18");

break;

case "add_employee_email":

	$ei_group = 2;
	$ei_title = $_POST['ei_title'];
	$ei_data = $_POST['ei_data'];
	$ei_primary = 0;
	$ei_employeeid = $_POST['ei_employeeid'];


	$query = $db->prepare("
					INSERT INTO idk_employees_info
						(ei_group, ei_title, ei_data, ei_primary, ei_employeeid)
					VALUES
						(:ei_group, :ei_title, :ei_data, :ei_primary, :ei_employeeid)");

	$query->execute(array(
				':ei_group' => $ei_group,
				':ei_title' => $ei_title,
				':ei_data' => $ei_data,
				':ei_primary' => $ei_primary,
				':ei_employeeid' => $ei_employeeid));

	header("Location: employees?page=open&id=$ei_employeeid&mess=17");

break;

case "set_primary_email_employee":

	$ei_id = $_GET['ei_id'];
	$ei_employeeid = $_GET['employee_id'];

	//Remove default primary phone
	$query = $db->prepare("
					UPDATE idk_employees_info
					SET	ei_primary = :ei_primary
					WHERE ei_employeeid = :ei_employeeid AND ei_primary = :ei_primary_current AND ei_group = :ei_group");

	$query->execute(array(
				':ei_primary' => 0,
				':ei_group' => 2,
				':ei_employeeid' => $ei_employeeid,
				':ei_primary_current' => 1));

	//Add primary phone
	$query = $db->prepare("
					UPDATE idk_employees_info
					SET	ei_primary = :ei_primary
					WHERE ei_id = :ei_id");

	$query->execute(array(
				':ei_primary' => 1,
				':ei_id' => $ei_id));

	header("Location: employees?page=open&id=$ei_employeeid&mess=18");

break;

case "add_contact_other":

	$ci_group = 3;
	$ci_title = $_POST['ci_title'];
	$ci_data = $_POST['ci_data'];
	$ci_primary = 0;
	$ci_contactid = $_POST['ci_contactid'];


	$query = $db->prepare("
					INSERT INTO idk_contacts_info
						(ci_group, ci_title, ci_data, ci_primary, ci_contactid)
					VALUES
						(:ci_group, :ci_title, :ci_data, :ci_primary, :ci_contactid)");

	$query->execute(array(
				':ci_group' => $ci_group,
				':ci_title' => $ci_title,
				':ci_data' => $ci_data,
				':ci_primary' => $ci_primary,
				':ci_contactid' => $ci_contactid));

	header("Location: contacts?page=open&id=$ci_contactid&mess=19");

break;

case "addToProject":

	$selectedrows = $_POST['selectedrows'];
	$projID = $_POST['proj_id'];
	
	if($projID == NULL || $selectedrows == NULL){
		header('Location: ' . $_SERVER['HTTP_REFERER']);
	}else{
		foreach($selectedrows as $kandidat_id){
			$query = $db->prepare("
						INSERT INTO idk_project_kandidati
							(pk_projectid, pk_kandidatid)
						VALUES
							(:pk_projectid, :pk_kandidatid)");
	
			$query->execute(array(
						':pk_projectid' => $projID,
						':pk_kandidatid' => $kandidat_id));
		}
		header("Location: projects?page=open&id=$projID&mess=11");
	}


	
		

break;

case "set_primary_other":

	$ci_id = $_GET['ci_id'];
	$ci_contactid = $_GET['contact_id'];

	//Remove default primary phone
	$query = $db->prepare("
					UPDATE idk_contacts_info
					SET	ci_primary = :ci_primary
					WHERE ci_contactid = :ci_contactid AND ci_primary = :ci_primary_current AND ci_group = :ci_group");

	$query->execute(array(
				':ci_primary' => 0,
				':ci_group' => 3,
				':ci_contactid' => $ci_contactid,
				':ci_primary_current' => 1));

	//Add primary phone
	$query = $db->prepare("
					UPDATE idk_contacts_info
					SET	ci_primary = :ci_primary
					WHERE ci_id = :ci_id");

	$query->execute(array(
				':ci_primary' => 1,
				':ci_id' => $ci_id));

	header("Location: contacts?page=open&id=$ci_contactid&mess=20");

break;

case "add_company_other":

	$comi_group = 3;
	$comi_title = $_POST['comi_title'];
	$comi_data = $_POST['comi_data'];
	$comi_primary = 0;
	$comi_companyid = $_POST['comi_companyid'];


	$query = $db->prepare("
					INSERT INTO idk_companies_info
						(comi_group, comi_title, comi_data, comi_primary, comi_companyid)
					VALUES
						(:comi_group, :comi_title, :comi_data, :comi_primary, :comi_companyid)");

	$query->execute(array(
				':comi_group' => $comi_group,
				':comi_title' => $comi_title,
				':comi_data' => $comi_data,
				':comi_primary' => $comi_primary,
				':comi_companyid' => $comi_companyid));

	header("Location: companies?page=open&id=$comi_companyid&mess=19");

break;

case "set_primary_other_company":

	$comi_id = $_GET['comi_id'];
	$comi_companyid = $_GET['comi_companyid'];

	//Remove default primary phone
	$query = $db->prepare("
					UPDATE idk_companies_info
					SET	comi_primary = :comi_primary
					WHERE comi_companyid = :comi_companyid AND comi_primary = :comi_primary_current AND comi_group = :comi_group");

	$query->execute(array(
				':comi_primary' => 0,
				':comi_group' => 3,
				':comi_companyid' => $comi_companyid,
				':comi_primary_current' => 1));

	//Add primary phone
	$query = $db->prepare("
					UPDATE idk_companies_info
					SET	comi_primary = :comi_primary
					WHERE comi_id = :comi_id");

	$query->execute(array(
				':comi_primary' => 1,
				':comi_id' => $comi_id));

	header("Location: companies?page=open&id=$comi_companyid&mess=20");

break;

case "add_employee_other":

	$ei_group = 3;
	$ei_title = $_POST['ei_title'];
	$ei_data = $_POST['ei_data'];
	$ei_primary = 0;
	$ei_employeeid = $_POST['ei_employeeid'];


	$query = $db->prepare("
					INSERT INTO idk_employees_info
						(ei_group, ei_title, ei_data, ei_primary, ei_employeeid)
					VALUES
						(:ei_group, :ei_title, :ei_data, :ei_primary, :ei_employeeid)");

	$query->execute(array(
				':ei_group' => $ei_group,
				':ei_title' => $ei_title,
				':ei_data' => $ei_data,
				':ei_primary' => $ei_primary,
				':ei_employeeid' => $ei_employeeid));

	header("Location: employees?page=open&id=$ei_employeeid&mess=19");

break;

case "set_primary_other_employee":

	$ei_id = $_GET['ei_id'];
	$ei_employeeid = $_GET['employee_id'];

	//Remove default primary phone
	$query = $db->prepare("
					UPDATE idk_employees_info
					SET	ei_primary = :ei_primary
					WHERE ei_employeeid = :ei_employeeid AND ei_primary = :ei_primary_current AND ei_group = :ei_group");

	$query->execute(array(
				':ei_primary' => 0,
				':ei_group' => 3,
				':ei_employeeid' => $ei_employeeid,
				':ei_primary_current' => 1));

	//Add primary phone
	$query = $db->prepare("
					UPDATE idk_employees_info
					SET	ei_primary = :ei_primary
					WHERE ei_id = :ei_id");

	$query->execute(array(
				':ei_primary' => 1,
				':ei_id' => $ei_id));

	header("Location: employees?page=open&id=$ei_employeeid&mess=20");

break;

case "notifications_mark_read":

	$notification_employeeid = (int)$_POST['id'];

	$query = $db->prepare("
					UPDATE idk_notifications
					SET	notification_status = :notification_status
					WHERE notification_employeeid = :notification_employeeid AND notification_datetime <= 'NOW()'");

	$query->execute(array(
				':notification_status' => 2,
				':notification_employeeid' => $notification_employeeid));

break;

case "send_message":

	$message_subject = $_POST['message_subject'];
	$message_text = $_POST['message_text'];
	$message_sentid = $_POST['message_sentid'];
	$message_datetime = date('Y-m-d H:i:s');

	$query_message = $db->prepare("
					INSERT INTO idk_messages
						(message_subject, message_text, message_sentid, message_status, message_datetime)
					VALUES
						(:message_subject, :message_text, :message_sentid, :message_status, :message_datetime)");

	$query_message->execute(array(
					':message_subject' => $message_subject,
					':message_text' => $message_text,
					':message_sentid' => $message_sentid,
					':message_status' => 1,
					':message_datetime' => $message_datetime));

	//Get last ID
	$mu_messageid = $db->lastInsertId();

	//Who receive a email
	foreach ($_POST['mu_employeeid'] as $mess_to_array){

		$query_send = $db->prepare("
						INSERT INTO idk_messages_users
							(mu_messageid, mu_employeeid, mu_status)
						VALUES
							(:mu_messageid, :mu_employeeid, :mu_status)");

		$query_send->execute(array(
						':mu_messageid' => $mu_messageid,
						':mu_employeeid' => $mess_to_array,
						':mu_status' => 0));

		//Get User Info
		$user_query = $db->prepare("
								SELECT employee_firstname, employee_lastname, employee_email
								FROM idk_employees
								WHERE employee_id = :employee_id");

		$user_query->execute(array(
						':employee_id' => $mess_to_array));

		$user = $user_query->fetch();

		$employee_firstname = $user['employee_firstname'];
		$employee_lastname = $user['employee_lastname'];
		$employee_email = $user['employee_email'];

		//Send email to user
		$mail_email = $employee_email;
		$mail_name = $employee_firstname . ' ' . $employee_lastname;
		$mail_subject = "Imate novu poruku - IDK CRM";
		$mail_url = "" . getSiteUrlr() . "/messages?page=open&id=" . $mu_messageid . "";
		$mail_body = "
						<p>Imate novu poruku: " . $message_text . "</p>
						<p>Detalji poruke: " . $mail_url . "</p>
		";
		$mail_altbody = "
						<p>Imate novu poruku: " . $message_text . "</p>
						<p>Detalji poruke: " . $mail_url . "</p>
		";

		sendEmail($mail_email, $mail_name, $mail_subject, $mail_body, $mail_altbody);

		//Add to LOGS
		$log_desc = "Poslao poruku korisniku: " . $employee_firstname . " " . $employee_lastname . "";
		$log_date = date('Y-m-d H:i:s');

		$log_query = $db->prepare("
						INSERT INTO idk_logs
							(log_employeeid, log_desc, log_date)
						VALUES
							(:log_employeeid, :log_desc, :log_date)");

		$log_query->execute(array(
						':log_employeeid' => $logged_employee_id,
						':log_desc' => $log_desc,
						':log_date' => $log_date));
	}

	header("Location: messages?page=list&mess=1");

break;

/*
	JOIN - company_registration.php - backend START
	*/
		case "company_registration_job_title": 
			header('Access-Control-Allow-Origin: *');
			header('Access-Control-Allow-Methods: GET, POST');
			header("Access-Control-Allow-Headers: X-Requested-With");

			$current_language = $_POST['current_language'] ?? null; 
			$url_j = $_POST['url_j'] ?? null; 
			$url_c = $_POST['url_c'] ?? null; 

			$result = array(
				'status' => 0,
				'data' => array()
			);

			if ($current_language != null) {
				$language_columns = (($current_language == 'en') ? 'kp_ime_en' : (($current_language == 'de') ? 'kp_ime_de' : (($current_language == 'sr') ? 'kp_ime_rs' : 'kp_ime')));

				$query = $db->prepare("
					SELECT 
						kp_id, 
						".$language_columns." AS profession_name 
					FROM 
						idk_kandidat_pozicija 
					WHERE 
						kp_active = 1
						AND 
						".$language_columns." IS NOT NULL
				");
				$query->execute();
				if ($query->rowCount() > 0) {
					$result['status'] = 1;
					$rows = $query->fetchAll(PDO::FETCH_ASSOC);
					$result['data'] = $rows;
				} else {
					$result['status'] = 2;
				}
			} 

			echo json_encode($result);
		break; 

		case "company_registration":
			header('Access-Control-Allow-Origin: *');
			header('Access-Control-Allow-Methods: GET, POST');
			header("Access-Control-Allow-Headers: X-Requested-With");

			$company_name 					= isset($_POST['company_name']) && $_POST['company_name'] !== '' 							? $_POST['company_name'] 				: '';
			$company_size 					= isset($_POST['company_size']) && $_POST['company_size'] !== '' 							? $_POST['company_size'] 				: null;
			$company_country				= isset($_POST['company_country']) && $_POST['company_country'] !== '' 						? $_POST['company_country'] 			: null;
			$company_city 					= isset($_POST['company_city']) && $_POST['company_city'] !== '' 							? $_POST['company_city'] 				: null;
			$company_zip_code 				= isset($_POST['company_zip_code']) && $_POST['company_zip_code'] !== '' 					? $_POST['company_zip_code'] 			: null;
			$company_address 				= isset($_POST['company_address']) && $_POST['company_address'] !== '' 						? $_POST['company_address'] 			: null;
			$company_contact_name 			= isset($_POST['company_contact_name']) && $_POST['company_contact_name'] !== ''			? $_POST['company_contact_name'] 		: null;
			$company_contact_surnname 		= isset($_POST['company_contact_surnname']) && $_POST['company_contact_surnname'] !== '' 	? $_POST['company_contact_surnname'] 	: null;
			$company_contact_jobtitle 		= isset($_POST['company_contact_jobtitle']) && $_POST['company_contact_jobtitle'] !== '' 	? $_POST['company_contact_jobtitle'] 	: null;
			$company_contact_email 			= isset($_POST['company_contact_email']) && $_POST['company_contact_email'] !== '' 			? $_POST['company_contact_email'] 		: '';
			$company_contact_phone 			= isset($_POST['company_contact_phone']) && $_POST['company_contact_phone'] !== '' 			? $_POST['company_contact_phone'] 		: '';
			$number_of_workers 				= isset($_POST['number_of_workers']) && $_POST['number_of_workers'] !== '' 					? $_POST['number_of_workers'] 			: null;
			$job_title 						= isset($_POST['job_title']) && $_POST['job_title'] !== '' 									? $_POST['job_title'] 					: [];
			$other_job_profession_cnt		= isset($_POST['other_job_profession_cnt']) && $_POST['other_job_profession_cnt'] !== '' 	? $_POST['other_job_profession_cnt'] 	: null; 
			$other_job_professions			= isset($_POST['other_job_professions']) && $_POST['other_job_professions'] !== '' 			? $_POST['other_job_professions'] 		: [];
			$schedule_a_call 				= isset($_POST['schedule_a_call']) && $_POST['schedule_a_call'] !== '' 						? $_POST['schedule_a_call'] 			: '';
			$call_date 						= isset($_POST['call_date']) && $_POST['call_date'] !== '' 									? $_POST['call_date'] 					: '';
			$call_time 						= isset($_POST['call_time']) && $_POST['call_time'] !== '' 									? $_POST['call_time'] 					: '';
			$message 						= isset($_POST['message']) && $_POST['message'] !== '' 										? $_POST['message'] 					: null;
			$current_language 				= isset($_POST['current_language']) && $_POST['current_language'] !== '' 					? $_POST['current_language'] 			: '';
			$current_token 					= isset($_POST['current_token']) && $_POST['current_token'] !== '' 							? $_POST['current_token'] 				: '';
			$url_c 							= isset($_POST['url_c']) && $_POST['url_c'] !== '' 											? $_POST['url_c'] 						: '';
			$url_j 							= isset($_POST['url_j']) && $_POST['url_j'] !== '' 											? $_POST['url_j'] 						: '';

			$response = [];
			
			/*
				Data verification - START 
				*/
					if ($company_name == '' OR $company_contact_email == '' OR $company_contact_phone == '' OR $number_of_workers == '' OR ($schedule_a_call == 'Yes' AND ($call_date == '' OR $call_time == '')) OR (in_array('other', $job_title) AND count($other_job_professions) == 0)) {
						http_response_code(400);
						$response['status'] = 'error';
						if ($current_language == 'bs') {
							$response['message'] = 'Nedostaju podaci! Provjerite obavezna polja i pokušajte ponovno!';
						} else if ($current_language == 'hr') { 
							$response['message'] = 'Nedostaju podaci! Provjerite obavezna polja i pokušajte ponovno!'; 
						} else if ($current_language == 'sr') { 
							$response['message'] = 'Nedostaju podaci! Proverite obavezna polja i pokušajte ponovo!'; 
						} else if ($current_language == 'de') { 
							$response['message'] = 'Es ist nicht schlimm! Versuchen Sie, einen neuen Anstrich zu machen und ihn gleich zu platzieren!'; 
						} else {
							$response['message'] = 'Missing data! Check the required fields and try again!';
						}
					}
				/*
				Data verification - END 
			*/

			/*
				Data formatting - START
				*/
					$partner_id = ($current_token != '') ? getPartnerIdFromToken($current_token) : null;
					$date = date('Y-m-d H:i:s');
					$company_professions = (count($job_title) > 0) ? implode(',', $job_title) : null; 
					$other_company_professions = ((count($other_job_professions) > 0) ? implode(',', $other_job_professions) : null); 
					$meeting_employee = ($partner_id != null) ? 526 : 173; // Dejan Vajagic - Emir Bender stavljen privremeno dok se ne pocne koristiti flow za dodavanje kompanije preko JOIN-a
					$company_origin = ($partner_id != null) ? 5 : 6; 
					$company_origin_desc = ($partner_id != null) ? "Partner Join formi" : "Join formi"; 
					$meeting_appointment = ($schedule_a_call == 'Yes') ? date('Y-m-d H:i:s', strtotime($call_date. ' ' . substr($call_time, 0, 4) . ':00')) : null;
					$meeting_type = 1;  
					$company_id = 0;
					$client_id = 0; 
					$contact_id = 0; 
					$company_info_phone_id = 0;
					$company_contact_phone_id = 0; 
					$company_info_email_id = 0; 
					$company_contact_email_id = 0; 
					$meeting_id = 0;
					$job_title_new_exp = array();
					$job_title_new_imp = null;
					$other_job_ids_exp = array();
					$other_job_ids_imp = null;
					$job_title_merged_exp = array();
					$job_title_merged_imp = null;
					$mail_us_response = '';
					$mail_us_set_from = '';
					$mail_us_sent_to = '';
					$mail_company_response = '';
					$mail_company_set_from = '';
					$mail_company_sent_to = '';
				/*
				Data formatting - END
			*/

			/*
					Add Company START
				*/
					$company_add = $db->prepare("
						INSERT INTO idk_companies
						(
							js_partner_id,
							company_origin,
							company_name,
							company_size,
							company_zipcode,
							company_city,
							company_country,
							company_address,
							company_contact_type, 
							company_message,
							company_datetime,
							company_total_workers_required,
							company_professions,
							company_status
						)
						VALUES
						(
							:js_partner_id,
							:company_origin,
							:company_name,
							:company_size,
							:compnay_zipcode,
							:company_city,
							:company_country,
							:company_address,
							:company_contact_type, 
							:company_message,
							:company_datetime,
							:company_total_workers_required,
							:company_professions,
							:company_status
						)
					");
					$company_add->execute(array(
						':js_partner_id' 					=> 	$partner_id,
						':company_origin' 					=> 	$company_origin,
						':company_name' 					=> 	$company_name,
						':company_size' 					=>	$company_size,
						':compnay_zipcode' 					=>	$company_zip_code,
						':company_city' 					=>	$company_city,
						':company_country' 					=>	$company_country,
						':company_address' 					=> 	$company_address,
						':company_contact_type'				=>  'Lead',
						':company_message' 					=>	$message,
						':company_datetime' 				=>	$date,
						':company_status' 					=>	2,
						':company_total_workers_required' 	=>	$number_of_workers,
						':company_professions'				=>	$company_professions
					));
				/*
					Add Company END
			*/

			if ($company_add->rowCount() > 0) {

				$company_id = $db->lastInsertId();

				/*
					Other profession START
					*/
						if (($key = array_search('other', $job_title)) !== false) {
							unset($job_title[$key]);
							$job_title_new_exp = array_values($job_title);
							$job_title_new_imp = (count($job_title_new_exp) > 0) ? implode(',', $job_title_new_exp) : null;
							if (count($other_job_professions) > 0) {
								foreach ($other_job_professions AS $other_job_profession) {
									$profession_add = $db->prepare("
										INSERT INTO idk_kandidat_pozicija
										(
											kp_ime,
											kp_ime_de,
											kp_ime_en,
											kp_ime_rs,
											kp_created_by,
											kp_source,
											kp_active
										)
										VALUES
										(
											:kp_ime,
											:kp_ime_de,
											:kp_ime_en,
											:kp_ime_rs,
											:kp_created_by,
											:kp_source,
											:kp_active
										)
									"); 
									$profession_add->execute(array(
										':kp_ime' => (($current_language == 'bs' OR $current_language == 'hr' OR $current_language == 'sr') ? $other_job_profession : null), 
										':kp_ime_de' => (($current_language == 'de') ? $other_job_profession : null),
										':kp_ime_en' => (($current_language == 'en') ? $other_job_profession : null),
										':kp_ime_rs' => (($current_language == 'sr') ? $other_job_profession : null),
										':kp_created_by' => (($partner_id != null) ? $partner_id : null),
										':kp_source' => (($partner_id != null) ? 2 : 1),
										':kp_active' => 1
									));
									if ($profession_add->rowCount() > 0) {
										$profession_id = $db->lastInsertId();
										array_push($other_job_ids_exp, $profession_id); 
									}
								}
								$other_job_ids_imp = implode(',', $other_job_ids_exp);
							}

							$job_title_merged_exp = array_merge($job_title_new_exp, $other_job_ids_exp);
							$job_title_merged_imp = (count($job_title_merged_exp) > 0) ? implode(',', $job_title_merged_exp) : null;
							
							$company_update = $db->prepare("
								UPDATE 
									idk_companies 
								SET 
									company_professions = :company_professions 
								WHERE 
									company_id = :company_id
							");
							$company_update->execute(array(
								':company_professions' => $job_title_merged_imp,
								':company_id' => $company_id
							));
						}
					/*
					Other profession END
				*/

				/*
					Add To First Call / Sales START
					*/
						$company_to_first_call_sales = $db->prepare("
							INSERT INTO idk_clients(
								client_name,
								client_country,
								client_city,
								client_address,
								client_pp,
								client_telephone,
								client_email,
								client_origin,
								client_recommendation,
								client_recommendation_company,
								client_fc_or_sales,
								client_fc_status,
								client_sales_status,
								client_manager,
								client_sales_manager,
								client_contract,
								client_description
							)
							VALUES(
								:client_name,
								:client_country,
								:client_city,
								:client_address,
								:client_pp,
								:client_telephone,
								:client_email,
								:client_origin,
								NULL,
								:client_recommendation_company,
								1,
								2,
								0,
								NULL,
								NULL,
								0,
								:client_description
							)
						");
						$company_to_first_call_sales->execute(array(
							':client_name' => $company_name,
							':client_country' => $company_country,
							':client_city' => $company_city,
							':client_address' => $company_address,
							':client_pp' => $company_zip_code,
							':client_telephone' => $company_contact_phone,
							':client_email' => $company_contact_email,
							':client_origin' => $company_origin,
							':client_recommendation_company' => $company_id,
							':client_description' => $message,
						));
						if ($company_to_first_call_sales->rowCount() > 0) {
							$client_id = $db->lastInsertId();
							insertClientStats($client_id, 0, 1, 'Ispunjen nalog od strane kompanije ' . $company_name . ' na ' . $company_origin_desc);
						}
					/*
					Add To First Call / Sales END 
				*/

				/* 
					Company info START 
					*/
						$company_info_add = $db->prepare("
							INSERT INTO idk_contacts
							(
								contact_firstname,
								contact_lastname,
								contact_companyid,
								contact_clientid, 
								contact_status,
								contact_job_title,
								contact_datetime
							)
							VALUES
							(
								:contact_firstname,
								:contact_lastname,
								:contact_companyid,
								:contact_clientid, 
								:contact_status,
								:contact_job_title,
								:contact_datetime
							)
						");
						$company_info_add->execute(array(
							':contact_firstname' => (($company_contact_name != null) ? $company_contact_name : 'Unknown'),
							':contact_lastname' => (($company_contact_surnname != null) ? $company_contact_surnname : 'Unknown'),
							':contact_companyid' => $company_id,
							':contact_clientid' => $client_id, 
							':contact_status' => 1,
							':contact_job_title' => $company_contact_jobtitle,
							':contact_datetime' => $date, 
						));
						if ($company_info_add->rowCount() > 0) {
							$contact_id = $db->lastInsertId();
						}

						$company_info_phone_add = $db->prepare("
							INSERT INTO idk_companies_info
							(
								comi_group,
								comi_title,
								comi_data,
								comi_primary,
								comi_companyid
							)
							VALUES
							(
								:comi_group,
								:comi_title,
								:comi_data,
								:comi_primary,
								:comi_companyid
							)
						");
						$company_info_phone_add->execute(array(
							':comi_group' => 1,
							':comi_title' => 'Telefon',
							':comi_data' => $company_contact_phone,
							':comi_primary' => 1,
							':comi_companyid' => $company_id, 
						));
						if ($company_info_phone_add->rowCount() > 0) {
							$company_info_phone_id = $db->lastInsertId();
						}

						$company_contact_phone_add = $db->prepare("
							INSERT INTO idk_contacts_info
								(
									ci_group, 
									ci_title, 
									ci_data, 
									ci_primary, 
									ci_contactid
								)
							VALUES
								(
									:ci_group, 
									:ci_title, 
									:ci_data, 
									:ci_primary, 
									:ci_contactid
								)
						");
						$company_contact_phone_add->execute(array(
							':ci_group' => 1,
							':ci_title' => "Telefon",
							':ci_data' => $company_contact_phone,
							':ci_primary' => 1,
							':ci_contactid' => $contact_id
						));
						if ($company_contact_phone_add->rowCount() > 0) {
							$company_contact_phone_id = $db->lastInsertId();
						}

						$company_info_email_add = $db->prepare("
							INSERT INTO idk_companies_info
							(
								comi_group,
								comi_title,
								comi_data,
								comi_primary,
								comi_companyid
							)
							VALUES
							(
								:comi_group,
								:comi_title,
								:comi_data,
								:comi_primary,
								:comi_companyid
							)
						");
						$company_info_email_add->execute(array(
							':comi_group' => 2,
							':comi_title' => 'E-mail',
							':comi_data' => $company_contact_email,
							':comi_primary' => 1,
							':comi_companyid' => $company_id
						));
						if ($company_info_email_add->rowCount() > 0) {
							$company_info_email_id = $db->lastInsertId();
						}

						$company_contact_email_add = $db->prepare("
							INSERT INTO idk_contacts_info
								(
									ci_group, 
									ci_title, 
									ci_data, 
									ci_primary, 
									ci_contactid
								)
							VALUES
								(
									:ci_group, 
									:ci_title, 
									:ci_data, 
									:ci_primary, 
									:ci_contactid
								)
						");
						$company_contact_email_add->execute(array(
							':ci_group' => 2,
							':ci_title' => "E-mail",
							':ci_data' => $company_contact_email,
							':ci_primary' => 1,
							':ci_contactid' => $contact_id
						));
						if ($company_contact_email_add->rowCount() > 0) {
							$company_contact_email_id = $db->lastInsertId();
						}
					/* 
					Company info END 
				*/

				/*
					Meeting details START 
					*/
						if ($meeting_appointment != null AND $partner_id != null) {
							$meeting_details = $db->prepare("
								INSERT INTO idk_partner_meetings
								(
									meeting_appointment,
									meeting_type,
									meeting_employee,
									meeting_company_id
								)
								VALUES
								(
									:meeting_appointment,
									:meeting_type,
									:meeting_employee,
									:meeting_company_id
								)
							");
							$meeting_details->execute(array(
								':meeting_appointment'  => $meeting_appointment,
								':meeting_type'         => $meeting_type,
								':meeting_employee'     => $meeting_employee,
								':meeting_company_id'   => $company_id 
							));
							if ($meeting_details->rowCount() > 0) {
								$meeting_id = $db->lastInsertId();
							}
						}

						if ($meeting_appointment != null AND $partner_id == null) {
							//SPREMI SAD TO U BAZU
							$meeting_details = $db->prepare("
								INSERT INTO idk_sales_reminders(
									sr_employee_id,
									sr_client_id,
									sr_call_datetime, 
									sr_type
								)
								VALUES(
									:sr_employee_id,
									:sr_client_id,
									:sr_call_datetime, 
									:sr_type
								)
							");
							$meeting_details->execute(array(
								':sr_employee_id' => $meeting_employee,
								':sr_client_id' => $client_id,
								':sr_call_datetime' => $meeting_appointment, 
								':sr_type' => $meeting_type
							));
							if ($meeting_details->rowCount() > 0) {
								$meeting_id = $db->lastInsertId();
							}
						}
					/*
					Meeting details END  
				*/

				/*
						Send Email START
					*/
						$get_company_info_query = $db->prepare("
							SELECT
								c.company_name,
								c.company_size,
								c.company_country,
								c.company_city,
								c.company_zipcode,
								c.company_address,
								ct.contact_firstname,
								ct.contact_lastname,
								ct.contact_job_title,
								ci_email.comi_data AS contact_email,
								ci_phone.comi_data AS contact_phone,
								c.company_total_workers_required AS number_of_workers,
								c.company_professions,
								c.company_message,
								m.meeting_appointment
							FROM
								idk_companies c
							LEFT JOIN 
								idk_contacts ct 
							ON 
								ct.contact_companyid = c.company_id
							LEFT JOIN 
								idk_companies_info ci_email 
							ON
								ci_email.comi_companyid = c.company_id AND ci_email.comi_group = 2 AND ci_email.comi_primary = 1
							LEFT JOIN 
								idk_companies_info ci_phone 
							ON
								ci_phone.comi_companyid = c.company_id AND ci_phone.comi_group = 1 AND ci_phone.comi_primary = 1
							LEFT JOIN 
								idk_partner_meetings m 
							ON
								m.meeting_company_id = c.company_id AND m.meeting_type = 1
							WHERE
								c.company_id = :company_id
						"); 
						$get_company_info_query->execute(array(':company_id' => $company_id));
						if ($get_company_info_query->rowCount() == 1) {
							$company_data = $get_company_info_query->fetch(PDO::FETCH_ASSOC);
							$professions_string = null; 
							if ($company_data['company_professions'] != null AND ($current_language == 'en' OR $current_language == 'de')) {
								$get_professions_query = $db->prepare("
									SELECT GROUP_CONCAT(".(($current_language == 'en') ? 'kp_ime_en' : 'kp_ime_de') ." SEPARATOR ', ') AS professions
									FROM idk_kandidat_pozicija
									WHERE kp_id IN (".$company_data['company_professions'].")
								");
								$get_professions_query->execute();
								$professions_data = $get_professions_query->fetch(PDO::FETCH_ASSOC);
								if ($professions_data['professions'] != null) {
									$professions_string = $professions_data['professions'];
								}
							}

							$mail_host_name 		= 'smtp.gmail.com';
							$mail_user_name 		= 'no-reply@job-step.com';
							$mail_password 			= 'kfqa orsn epcl bxcw';

							if ($partner_id != null) {
								$get_partner_info_query = $db->prepare("
									SELECT 
										jp_id, jp_imeprezime, jp_email
									FROM 
										idk_jobstep_partners
									WHERE 
										jp_id = :jp_id
								"); 
								$get_partner_info_query->execute(array(
									':jp_id' => $partner_id
								));
								if ($get_partner_info_query->rowCount() == 1) {
									$partner_data = $get_partner_info_query->fetch(PDO::FETCH_ASSOC);

									$mail_us_subject 		= "Neue Firma eingetragen: ".$company_data['company_name']."";
									$mail_us_body 		 	= "
										<h2>Der Benutzer hat folgende Informationen über das Unternehmen eingegeben:</h2><br>
										<h2><u>Firmeninfo</u></h2><br>
										<b>Firmenname: </b> ".$company_data['company_name']."<br>
										".(($company_data['contact_firstname'] != 'Unknown' AND $company_data['contact_lastname'] != null) ? '<b>Kontaktperson: </b>' . $company_data['contact_firstname'].' '.$company_data['contact_lastname'] . '<br>' : '' )."
										".(($company_data['contact_job_title'] != null) ? '<b>Position der Kontaktperson: </b>' . $company_data['contact_job_title'] . '<br>' : '' )."
										".(($company_data['company_size'] != null) ? '<b>Unternehmensgröße: </b>' . $company_data['company_size'] . '<br>' : '' )."
										<b>E-Mail-Adresse des Unternehmens:</b> " .$company_data['contact_email']. "<br>
										<b>Telefonnummer des Unternehmens:</b> " .$company_data['contact_phone']. "<br>
										".(($company_data['company_country'] != null) ? '<b>Land: </b>' . $company_data['company_country'] . '<br>' : '' )."
										".(($company_data['company_city'] != null) ? '<b>Stadt: </b>' . $company_data['company_city'] . '<br>' : '' )."
										".(($company_data['company_zipcode'] != null) ? '<b>Postleitzahl: </b>' . $company_data['company_zipcode'] . '<br>' : '' )."
										".(($company_data['company_address'] != null) ? '<b>Adresse: </b>' . $company_data['company_address'] . '<br>' : '' )."
										".(($company_data['company_message'] != null) ? '<b>Nachricht: </b>' . $company_data['company_message'] . '<br>' : '' )."
										".(($company_data['meeting_appointment'] != null) ? '<b>Termin vereinbart: </b>' . $company_data['meeting_appointment'] . '<br>' : '' )."
										<b>Gesamtzahl der benötigten Arbeitskräfte:</b> ".$company_data['number_of_workers']."<br>
										".(($professions_string != null) ? '<b>Berufe gefragt:</b>' . $professions_string . '<br>' : '' )."
										<br> 
										<h2><u>Partner info</u></h2>
										<b>ID:</b> ".$partner_data['jp_id']."<br>
										<b>Name:</b> ".$partner_data['jp_imeprezime']."<br>
										<b>Email:</b> ".$partner_data['jp_email']."<br>
										<b>Erstellungsdatum:</b> ".$date."<br>";

									$mail_us_set_from 		= $partner_data['jp_email'];
									$mail_us_sent_to = (($envConfig->APP_ENV != "production") ? 'dev-test-pa@job-step.com' : 'partner@job-step.com');
									
									$mail_us = new PHPMailer;
									$mail_us ->isSMTP();
									$mail_us ->Host 		= $mail_host_name;
									$mail_us ->SMTPAuth 	= true;
									$mail_us ->Username 	= $mail_user_name;
									$mail_us ->Password 	= $mail_password;
									$mail_us ->SMTPSecure 	= 'ssl';
									$mail_us ->Port 		= 465;
									$mail_us ->CharSet 		= 'UTF-8';

									$mail_us ->setFrom($mail_us_set_from , $partner_data['jp_imeprezime']);
									$mail_us ->addAddress($mail_us_sent_to);

									$mail_us ->Subject = $mail_us_subject;
									$mail_us ->Body    = $mail_us_body;
									$mail_us ->AltBody = "Neue Firma eingetragen";

									if(!$mail_us->send()) {
										$mail_us_response = 'Support mail failed';
									}else{
										$mail_us_response = 'Support mail sent';
									}
								}
							}

							switch ($current_language) {
								case 'bs':
									$mail_company_subject 	= "Dobrodošli u Jobstep";
									$mail_company_alt_body 	= "Nova firma je dodana"; 
								case 'hr':
									$mail_company_subject 	= "Dobrodošli u Jobstep";
									$mail_company_alt_body 	= "Nova firma je dodana";
								case 'sr':
									$mail_company_subject 	= "Dobrodošli u Jobstep";
									$mail_company_alt_body 	= "Nova firma je dodana";
								case 'de':
									$mail_company_subject 	= "Herzlich willkommen bei Jobstep";
									$mail_company_alt_body 	= "Neue Firma eingetragen";
								default:
									$mail_company_subject 	= "Welcome to Jobstep";
									$mail_company_alt_body 	= "New company registered";
							}
							$mail_company_body = file_get_contents('mail-templates/company-registration/create_company_'.$current_language.'.html');

							$mail_company_set_from 		= 'no-reply@job-step.com';
							$mail_company_sent_to 		= $company_data['contact_email'];

							$mail_company = new PHPMailer;
							$mail_company ->isSMTP();
							$mail_company ->Host 		= $mail_host_name;
							$mail_company ->SMTPAuth 	= true;
							$mail_company ->Username 	= $mail_user_name;
							$mail_company ->Password 	= $mail_password;
							$mail_company ->SMTPSecure 	= 'ssl';
							$mail_company ->Port 		= 465;
							$mail_company ->CharSet 		= 'UTF-8';

							$mail_company ->setFrom($mail_company_set_from , 'Jobstep');
							$mail_company ->addAddress($mail_company_sent_to);
							$mail_company ->isHTML(true);

							$mail_company ->Subject = $mail_company_subject;
							$mail_company ->Body    = $mail_company_body;
							$mail_company ->AltBody = $mail_company_alt_body;

							if(!$mail_company->send()) {
								$mail_company_response = 'Company mail failed';
							}else{
								$mail_company_response = 'Company mail sent';
							}

						}
					/*
						Send Email END 
				*/

				http_response_code(200);
				$response['status'] = 'success'; 
				$response['message'] = 'Successfully added company';

			} else {
				http_response_code(500);
				$response['status'] = 'error';
				if ($current_language == 'bs') {
					$response['message'] = 'Došlo je do greške na serveru. Molimo pokušajte ponovno kasnije.';
				} else if ($current_language == 'hr') {
					$response['message'] = 'Došlo je do greške na serveru. Molimo pokušajte ponovno kasnije.';
				} else if ($current_language == 'sr') {
					$response['message'] = 'Došlo je do greške na serveru. Molimo pokušajte ponovo kasnije.';
				} else if ($current_language == 'de') {
					$response['message'] = 'Es ist ein Serverfehler aufgetreten. Bitte versuchen Sie es später noch einmal.';
				} else {
					$response['message'] = 'Internal server error. Please try again later.';
				}
			}

			$log_data = [
				'response_data' => [
					'response_status'				=> $response['status'], 
					'response_message'				=> $response['message'],
					'access_url'					=> $url_j.'company/add', 
				], 
				'data received' => [
					'company_name'                 	=> $company_name,
					'company_size'                 	=> $company_size,
					'company_country'              	=> $company_country,
					'company_city'                 	=> $company_city,
					'company_zip_code'             	=> $company_zip_code,
					'company_address'              	=> $company_address,
					'company_contact_name'         	=> $company_contact_name,
					'company_contact_surnname'     	=> $company_contact_surnname,
					'company_contact_jobtitle'     	=> $company_contact_jobtitle,
					'company_contact_email'        	=> $company_contact_email,
					'company_contact_phone'        	=> $company_contact_phone,
					'number_of_workers'            	=> $number_of_workers,
					'job_title'                    	=> $job_title,
					'other_job_profession_cnt'		=> $other_job_profession_cnt, 
					'other_job_professions'			=> $other_company_professions,  
					'schedule_a_call'              	=> $schedule_a_call,
					'call_date'                    	=> $call_date,
					'call_time'                    	=> $call_time,
					'message'                      	=> $message,
					'current_language'             	=> $current_language,
					'current_token'                	=> $current_token,
					'partner_id'                   	=> $partner_id, 
					'url_c'                        	=> $url_c,
					'url_j'                        	=> $url_j,
				], 
				'generated data' => [
					'company_id' 					=> $company_id,
					'client_id'						=> $client_id, 
					'contact_id' 					=> $contact_id, 
					'company_info_phone_id'			=> $company_info_phone_id, 
					'company_contact_phone_id' 		=> $company_contact_phone_id, 
					'company_info_email_id' 		=> $company_info_email_id, 
					'company_contact_email_id' 		=> $company_contact_email_id, 
					'meeting_id'					=> $meeting_id,
					'other_job_ids_imp'				=> $other_job_ids_imp, 
					'job_title_merged_imp' 			=> $job_title_merged_imp,
				],
				'mail data' => [
					'Support mail' => [
						'mail_us_response'			=> $mail_us_response,
						'mail_us_set_from'			=> $mail_us_set_from, 
						'mail_us_sent_to' 			=> $mail_us_sent_to,
					],
					'Company mail' => [
						'mail_company_response' 	=> $mail_company_response,
						'mail_company_set_from' 	=> $mail_company_set_from,
						'mail_company_sent_to' 		=> $mail_company_sent_to,
					],
				], 
				'other data' => [
					'date'                         	=> $date,
					'company_professions'          	=> $company_professions,
					'meeting_employee'             	=> $meeting_employee,
					'company_origin'               	=> $company_origin,
					'company_origin_desc' 			=> $company_origin_desc,
					'meeting_appointment'          	=> $meeting_appointment,
					'meeting_type'                 	=> $meeting_type,
					'job_title_new_imp'				=> $job_title_new_imp, 
				]
			];

			$log_json_data = json_encode($log_data);

			$log_desc = 'JOIN -> Company Add -> Request sent to the backend. Details: '. $log_json_data; 

			addToLogs($log_desc, 0);

			echo json_encode($response);
				
		break; 
	/*
	JOIN - company_registration.php - backend START
*/

case "add_company":

		$company_name = $_POST['company_name'];
		$company_type = $_POST['company_type'];
		$company_idnum = $_POST['company_idnum'];
		$company_taxnum = $_POST['company_taxnum'];
		$company_address = $_POST['company_address'];
		$company_zipcode = $_POST['company_zipcode'];
		$company_city = $_POST['company_city'];
		$company_state = $_POST['company_state'];
		$company_country = $_POST['company_country'];
		$company_info = $_POST['company_info'];
		$company_contact_type = $_POST['company_contact_type'];
		$company_datetime = date('Y-m-d H:i:s');
		$company_status = 2;

		//Upload and save company_logo
		if($_FILES['company_logo']['size'] !== 0) {
			$company_logo = $_FILES['company_logo'];

			//File properties
			$file_name = $company_logo['name'];
			$file_tmp = $company_logo['tmp_name'];
			$file_size = $company_logo['size'];
			$file_error = $company_logo['error'];

			//File extension
			$file_ext = explode('.', $file_name);
			$file_ext = strtolower(end($file_ext));

			$allowed = array('jpg', 'jpeg', 'png');

			if(in_array($file_ext, $allowed)) {

				$company_logo_final = uniqid() . '.' . $file_ext;
				$file_destination = 'files/companies/' . $company_logo_final;

				if(move_uploaded_file($file_tmp, $file_destination)) {

					$path_to_image_directory = "files/" . getSubdomainr() . "_files/companies/";
					$final_width_of_image = 660;

						if(preg_match('/[.](jpg)$/', $company_logo_final)) {

							$im = imagecreatefromjpeg($path_to_image_directory . $company_logo_final);
							$ox = imagesx($im);
							$oy = imagesy($im);
							$nx = $final_width_of_image;
							$ny = floor($oy * ($final_width_of_image / $ox));
							$nm = imagecreatetruecolor($nx, $ny);
							imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);
							imagejpeg($nm, $path_to_image_directory . $company_logo_final);

						} else if (preg_match('/[.](jpeg)$/', $company_logo_final)) {

							$im = imagecreatefromjpeg($path_to_image_directory . $company_logo_final);
							$ox = imagesx($im);
							$oy = imagesy($im);
							$nx = $final_width_of_image;
							$ny = floor($oy * ($final_width_of_image / $ox));
							$nm = imagecreatetruecolor($nx, $ny);
							imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);
							imagejpeg($nm, $path_to_image_directory . $company_logo_final);

						} else if (preg_match('/[.](png)$/', $company_logo_final)) {

							$im = imagecreatefrompng($path_to_image_directory . $company_logo_final);
							$ox = imagesx($im);
							$oy = imagesy($im);
							$nx = $final_width_of_image;
							$ny = floor($oy * ($final_width_of_image / $ox));
							$nm = imagecreatetruecolor($nx, $ny);
							$white = imagecolorallocate($nm,  255, 255, 255);
							imagefilledrectangle($nm, 0, 0, $nx, $ny, $white);
							imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);
							imagejpeg($nm, $path_to_image_directory . $company_logo_final);

						}

				}
			}
		}else{
			$company_logo_final = "none";
		}

		//Add user to db
		$query = $db->prepare("
						INSERT INTO idk_companies
							(company_name, company_type, company_idnum, company_taxnum, company_address, company_zipcode, company_city, company_state, company_country, company_info, company_logo, company_contact_type, company_datetime, company_status)
						VALUES
							(:company_name, :company_type, :company_idnum, :company_taxnum, :company_address, :company_zipcode, :company_city, :company_state, :company_country, :company_info, :company_logo, :company_contact_type, :company_datetime, :company_status)");

		$query->execute(array(
					':company_name' => $company_name,
					':company_type' => $company_type,
					':company_idnum' => $company_idnum,
					':company_taxnum' => $company_taxnum,
					':company_address' => $company_address,
					':company_zipcode' => $company_zipcode,
					':company_city' => $company_city,
					':company_state' => $company_state,
					':company_country' => $company_country,
					':company_info' => $company_info,
					':company_logo' => $company_logo_final,
					':company_contact_type' => $company_contact_type,
					':company_datetime' => $company_datetime,
					':company_status' => $company_status));

		//Get last ID
		$comi_companyid = $db->lastInsertId();

		//Add primary phone
		if (!empty($_POST['company_phone'])) {

			$comi_group = 1;
			$comi_title = "Telefon";
			$comi_data = $_POST['company_phone'];
			$comi_primary = 1;

			$query_phone = $db->prepare("
							INSERT INTO idk_companies_info
								(comi_group, comi_title, comi_data, comi_primary, comi_companyid)
							VALUES
								(:comi_group, :comi_title, :comi_data, :comi_primary, :comi_companyid)");

			$query_phone->execute(array(
							':comi_group' => $comi_group,
							':comi_title' => $comi_title,
							':comi_data' => $comi_data,
							':comi_primary' => $comi_primary,
							':comi_companyid' => $comi_companyid));
		}

		//Add primary email
		if (!empty($_POST['company_email'])) {

			$comi_group = 2;
			$comi_title = "E-mail";
			$comi_data = $_POST['company_email'];
			$comi_primary = 1;

			$query_email = $db->prepare("
							INSERT INTO idk_companies_info
								(comi_group, comi_title, comi_data, comi_primary, comi_companyid)
							VALUES
								(:comi_group, :comi_title, :comi_data, :comi_primary, :comi_companyid)");

			$query_email->execute(array(
								':comi_group' => $comi_group,
								':comi_title' => $comi_title,
								':comi_data' => $comi_data,
								':comi_primary' => $comi_primary,
								':comi_companyid' => $comi_companyid));
			}

		//Add to LOGS
		$log_desc = "Dodao novu kompaniju: " . $company_name . "";
		$log_date = date('Y-m-d H:i:s');

		$log_query = $db->prepare("
						INSERT INTO idk_logs
							(log_employeeid, log_desc, log_date)
						VALUES
							(:log_employeeid, :log_desc, :log_date)");

		$log_query->execute(array(
						':log_employeeid' => $logged_employee_id,
						':log_desc' => $log_desc,
						':log_date' => $log_date));

	header("Location: companies?page=list&mess=1");

break;

case "edit_nalog":
		//push_not
		$nalog_id = $_POST['nalog_id'];
		$nalog_broj = $_POST['nalog_broj'];
		$nalog_naziv = $_POST['nalog_naziv'];
		$nalog_opis = $_POST['nalog_opis'];
		$nalog_status = $_POST['nalog_status'];
		$nalog_marketing_menadzer = $_POST['nalog_marketing_menadzer'];
		$nalog_project_menadzer = $_POST['nalog_project_menadzer'];
		$nalog_partner_provizija = $_POST['nalog_partner_provizija'];
		$nalog_dospijece = $_POST['nalog_dospijece'];
		if($nalog_partner_provizija == "")
			$nalog_partner_provizija = null;

		//provjera da li je promjenjen status

		$query = $db->prepare("
								SELECT nalog_id, kompanija_id, nalog_broj, nalog_naziv, nalog_opis, nalog_kreirano, nalog_status, nalog_marketing_menadzer, nalog_partner_active, nalog_js_partner_id
								FROM idk_nalozi
								WHERE nalog_id = :nalog_id");

		$query->execute(array(':nalog_id' => $nalog_id));
		$row = $query->fetch();
		
		$nalog_status_ex = $row['nalog_status'];
		$nalog_partner_active = $row['nalog_partner_active'];
		$partner_id = $row['nalog_js_partner_id'];

		if($nalog_status_ex != $nalog_status){
			//Nalog status log
			$query_nalog_log = $db->prepare("
			INSERT INTO idk_nalozi_log
				(n_log_nalogid, n_log_employeeid, n_log_status)
			VALUES
				(:n_log_nalogid, :n_log_employeeid, :n_log_status)");

			$query_nalog_log->execute(array(
				':n_log_nalogid' => $nalog_id,
				':n_log_employeeid' => $logged_employee_id,
				':n_log_status' => $nalog_status));
		}
		
		
		$query = $db->prepare("
						UPDATE idk_nalozi
						SET	nalog_broj = :nalog_broj, nalog_naziv = :nalog_naziv, nalog_opis = :nalog_opis, nalog_status = :nalog_status, nalog_marketing_menadzer = :nalog_marketing_menadzer, employee_id = :nalog_project_menadzer, nalog_partner_provizija = :nalog_partner_provizija, nalog_dospijece = :nalog_dospijece
						WHERE nalog_id = :nalog_id");

		$query->execute(array(
				':nalog_broj' => $nalog_broj,
				':nalog_naziv' => $nalog_naziv,
				':nalog_opis' => $nalog_opis,
				':nalog_status' => $nalog_status,
				':nalog_marketing_menadzer' => $nalog_marketing_menadzer,
				':nalog_project_menadzer' => $nalog_project_menadzer, 
				':nalog_partner_provizija' => $nalog_partner_provizija,
				':nalog_dospijece' => $nalog_dospijece,
				':nalog_id' => $nalog_id));

		//Add to LOGS
		$log_desc = "Uredio nalog: " . $nalog_broj . "";
		$log_date = date('Y-m-d H:i:s');

		$log_query = $db->prepare("
						INSERT INTO idk_logs
							(log_employeeid, log_desc, log_date)
						VALUES
							(:log_employeeid, :log_desc, :log_date)");

		$log_query->execute(array(
						':log_employeeid' => $logged_employee_id,
						':log_desc' => $log_desc,
						':log_date' => $log_date));

		$notification_type = getPartnerNotificationTypeId("ORDERS");
		$send_notifications = shouldSendPersonalNotification($partner_id,$notification_type);	
		if($nalog_status == 8 && $nalog_partner_active == 1 && $send_notifications == 1){
			//send notification for partner app
			$nalog_naziv_sql = "SELECT no_nalognaziv FROM idk_nalozi_opis WHERE no_nalogid = :nalog_id AND no_lang = 'de'";
			$nalog_naziv_query = $db->prepare($nalog_naziv_sql);
			$nalog_naziv_query->execute(array(':nalog_id' => $nalog_id));
			$nalog_naziv_result = $nalog_naziv_query->fetch();
			$nalog_naziv = $nalog_naziv_result['no_nalognaziv'];

			$partner_sql = "SELECT jp_id, jp_lang, jp_fcmtoken FROM idk_jobstep_partners JOIN idk_nalozi ON idk_jobstep_partners.jp_id = idk_nalozi.nalog_js_partner_id WHERE nalog_id = :nalog_id";
			$partner_query = $db->prepare($partner_sql);
			$partner_query->execute(array(':nalog_id' => $nalog_id));
			$partner = $partner_query->fetch();
			if($partner['jp_lang'] == "en"){
				$title = "Recruitment order";
				$content = 'The Recruitment order status of the company you added has been changed to "Completed"';
			} else {
				$title = "Rekrutierungsauftrag";
				$content = 'Der Status des Rekrutierungsauftrags des von Ihnen hinzugefügten Unternehmens wurde in „Abgeschlossen“ geändert.';
			}
			$payload = '{"order_id": "'.$nalog_id.'"}';
			$notification_title		='{ "en":"Recruitment order", "de":"Rekrutierungsauftrag" }';
			$notification_content 	='{ "en":"The Recruitment order status of the company you added has been changed to \'Completed\'", "de":"Der Status des Rekrutierungsauftrags des von Ihnen hinzugefügten Unternehmens wurde in \'Abgeschlossen\' geändert."}';
			$action="NAVIGATE_TO_JOB";
			createPartnerPersonalNotification($partner_id, $notification_type, $payload, $action, $notification_title, $notification_content);
			$onesignal_id = $partner['jp_fcmtoken'];
			sendNotification($onesignal_id, $title, $content);
		}

		header("Location: nalozi?page=open&id=$nalog_id&mess=5");

break;

case "edit_company":

		$company_id = $_POST['company_id'];
		$company_name = $_POST['company_name'];
		$company_type = $_POST['company_type'];
		$company_idnum = $_POST['company_idnum'];
		$company_taxnum = $_POST['company_taxnum'];
		$company_address = $_POST['company_address'];
		$company_zipcode = $_POST['company_zipcode'];
		$company_city = $_POST['company_city'];
		$company_state = $_POST['company_state'];
		$company_country = $_POST['company_country'];
		$company_info = $_POST['company_info'];
		$company_contact_type = $_POST['company_contact_type'];
		$company_status = $_POST['company_status'];

		$get_partner_id = $db->prepare("SELECT js_partner_id, jp_fcmtoken, jp_lang FROM idk_companies JOIN idk_jobstep_partners ON idk_companies.js_partner_id = idk_jobstep_partners.jp_id WHERE company_id = :company_id");
		$get_partner_id->execute(array(':company_id' => $company_id));
		$partner_id_result = $get_partner_id->fetch();
		$partner_id = $partner_id_result['js_partner_id'];
		$onesignal_id = $partner_id_result['jp_fcmtoken'];
		$partner_lang = $partner_id_result['jp_lang'];
		$send_notifications = 0;
		if($partner_id != NULL){
			$notification_type = getPartnerNotificationTypeId("COMPANIES");
			$send_notifications = shouldSendPersonalNotification($partner_id,$notification_type);
		}

		//Upload and save company_logo
		if($_FILES['company_logo']['size'] !== 0) {

			//Delete old image
			$del_img_query = $db->prepare("
										SELECT company_logo
										FROM idk_companies
										WHERE company_id = :company_id");

			$del_img_query->execute(array(
									':company_id' => $company_id));

			$del_img = $del_img_query->fetch();

				$company_logo = $del_img['company_logo'];

			if($company_logo == "" OR $company_logo == "none"){}else{
				unlink("files/" . getSubdomainr() . "_files/companies/" . $company_logo);
			}

			$company_logo = $_FILES['company_logo'];

			//File properties
			$file_name = $company_logo['name'];
			$file_tmp = $company_logo['tmp_name'];
			$file_size = $company_logo['size'];
			$file_error = $company_logo['error'];

			//File extension
			$file_ext = explode('.', $file_name);
			$file_ext = strtolower(end($file_ext));

			$allowed = array('jpg', 'jpeg', 'png');

			if(in_array($file_ext, $allowed)) {

				$company_logo_final = uniqid() . '.' . $file_ext;
				$file_destination = 'files/companies/' . $company_logo_final;

				if(move_uploaded_file($file_tmp, $file_destination)) {

					$path_to_image_directory = "files/" . getSubdomainr() . "_files/companies/";
					$final_width_of_image = 660;

					if(preg_match('/[.](jpg)$/', $company_logo_final)) {

						$im = imagecreatefromjpeg($path_to_image_directory . $company_logo_final);
						$ox = imagesx($im);
						$oy = imagesy($im);
						$nx = $final_width_of_image;
						$ny = floor($oy * ($final_width_of_image / $ox));
						$nm = imagecreatetruecolor($nx, $ny);
						imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);
						imagejpeg($nm, $path_to_image_directory . $company_logo_final);

					} else if (preg_match('/[.](jpeg)$/', $company_logo_final)) {

						$im = imagecreatefromjpeg($path_to_image_directory . $company_logo_final);
						$ox = imagesx($im);
						$oy = imagesy($im);
						$nx = $final_width_of_image;
						$ny = floor($oy * ($final_width_of_image / $ox));
						$nm = imagecreatetruecolor($nx, $ny);
						imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);
						imagejpeg($nm, $path_to_image_directory . $company_logo_final);

					} else if (preg_match('/[.](png)$/', $company_logo_final)) {

						$im = imagecreatefrompng($path_to_image_directory . $company_logo_final);
						$ox = imagesx($im);
						$oy = imagesy($im);
						$nx = $final_width_of_image;
						$ny = floor($oy * ($final_width_of_image / $ox));
						$nm = imagecreatetruecolor($nx, $ny);
						$white = imagecolorallocate($nm,  255, 255, 255);
						imagefilledrectangle($nm, 0, 0, $nx, $ny, $white);
						imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);
						imagejpeg($nm, $path_to_image_directory . $company_logo_final);

					}

				}
			}
		}else{
			$company_logo_final = $_POST['company_logo_url'];
		}
			$get_company_status = $db->prepare("SELECT company_status FROM idk_companies WHERE company_id = :company");
			$get_company_status->execute(array(':company' => $company_id));
			$company_status_old = $get_company_status->fetch();
			$company_status_old = $company_status_old['company_status'];

			if($company_status_old != $company_status && $send_notifications == 1){
				if($company_status == 0){
					$company_status_name = "'Archived'";
					$company_status_name_de = "'Archived'";
					if($partner_lang == "en"){
						$title = "Company";
						$content = 'The status of the company you added has been changed';
					} else {
						$title = "Unternehmens";
						$content = 'Der Status von Ihnen hinzugefügten Unternehmens wurde geändert';
					}
					$payload = '{"company_id": "'.$company_id.'"}';
					$notification_title		='{ "en":"Company", "de":"Unternehmens" }';
					$notification_content 	='{ "en":"The status of the company you added has been changed to '.$company_status_name.'", "de":"Der Status des von Ihnen hinzugefügten Unternehmens wurde in '.$company_status_name_de.' geändert."}';
					$action="NAVIGATE_TO_COMPANY";
					createPartnerPersonalNotification($partner_id, $notification_type, $payload, $action, $notification_title, $notification_content);
					sendNotification($onesignal_id, $title, $content);
				}elseif($company_status == 1){
					$company_status_name = "Active";
					$company_status_name_de = "Aktiv";
					if($partner_lang == "en"){
						$title = "Company";
						$content = 'The status of the company you added has been changed';
					} else {
						$title = "Unternehmens";
						$content = 'Der Status von Ihnen hinzugefügten Unternehmens wurde geändert';
					}
					$payload = '{"company_id": "'.$company_id.'"}';
					$notification_title		='{ "en":"Company", "de":"Unternehmens" }';
					$notification_content 	='{ "en":"The status of the company you added has been changed to '.$company_status_name.'", "de":"Der Status des von Ihnen hinzugefügten Unternehmens wurde in '.$company_status_name_de.' geändert."}';
					$action="NAVIGATE_TO_COMPANY";
					createPartnerPersonalNotification($partner_id, $notification_type, $payload, $action, $notification_title, $notification_content);
					sendNotification($onesignal_id, $title, $content);
				}elseif($company_status == 2 OR is_null($company_status)){
					$company_status_name = "New";
					$company_status_name_de = "Neu";
					if($partner_lang == "en"){
						$title = "Company";
						$content = 'The status of the company you added has been changed';
					} else {
						$title = "Unternehmens";
						$content = 'Der Status von Ihnen hinzugefügten Unternehmens wurde geändert';
					}
					$payload = '{"company_id": "'.$company_id.'"}';
					$notification_title		='{ "en":"Company", "de":"Unternehmens" }';
					$notification_content 	='{ "en":"The status of the company you added has been changed to '.$company_status_name.'", "de":"Der Status des von Ihnen hinzugefügten Unternehmens wurde in '.$company_status_name_de.' geändert."}';
					$action="NAVIGATE_TO_COMPANY";
					createPartnerPersonalNotification($partner_id, $notification_type, $payload, $action, $notification_title, $notification_content);
					sendNotification($onesignal_id, $title, $content);
				}elseif($company_status == 3){
					$company_status_name = "In progress";
					$company_status_name_de = "In bearbeitung";
					if($partner_lang == "en"){
						$title = "Company";
						$content = 'The status of the company you added has been changed';
					} else {
						$title = "Unternehmens";
						$content = 'Der Status von Ihnen hinzugefügten Unternehmens wurde geändert';
					}
					$payload = '{"company_id": "'.$company_id.'"}';
					$notification_title		='{ "en":"Company", "de":"Unternehmens" }';
					$notification_content 	='{ "en":"The status of the company you added has been changed to '.$company_status_name.'", "de":"Der Status des von Ihnen hinzugefügten Unternehmens wurde in '.$company_status_name_de.' geändert."}';
					$action="NAVIGATE_TO_COMPANY";
					createPartnerPersonalNotification($partner_id, $notification_type, $payload, $action, $notification_title, $notification_content);
					sendNotification($onesignal_id, $title, $content);
				}elseif($company_status == 4){
					$company_status_name = "On hold";
					$company_status_name_de = "In wartestellung";
					if($partner_lang == "en"){
						$title = "Company";
						$content = 'The status of the company you added has been changed';
					} else {
						$title = "Unternehmens";
						$content = 'Der Status von Ihnen hinzugefügten Unternehmens wurde geändert';
					}
					$payload = '{"company_id": "'.$company_id.'"}';
					$notification_title		='{ "en":"Company", "de":"Unternehmens" }';
					$notification_content 	='{ "en":"The status of the company you added has been changed to '.$company_status_name.'", "de":"Der Status des von Ihnen hinzugefügten Unternehmens wurde in '.$company_status_name_de.' geändert."}';
					$action="NAVIGATE_TO_COMPANY";
					createPartnerPersonalNotification($partner_id, $notification_type, $payload, $action, $notification_title, $notification_content);
					sendNotification($onesignal_id, $title, $content);
				}elseif($company_status == 5){
					$company_status_name = "Rejected";
					$company_status_name_de = "Abgelehnt";
					if($partner_lang == "en"){
						$title = "Company";
						$content = 'The status of the company you added has been changed';
					} else {
						$title = "Unternehmens";
						$content = 'Der Status von Ihnen hinzugefügten Unternehmens wurde geändert';
					}
					$payload = '{"company_id": "'.$company_id.'"}';
					$notification_title		='{ "en":"Company", "de":"Unternehmens" }';
					$notification_content 	='{ "en":"The status of the company you added has been changed to '.$company_status_name.'", "de":"Der Status des von Ihnen hinzugefügten Unternehmens wurde in '.$company_status_name_de.' geändert."}';
					$action="NAVIGATE_TO_COMPANY";
					createPartnerPersonalNotification($partner_id, $notification_type, $payload, $action, $notification_title, $notification_content);
					sendNotification($onesignal_id, $title, $content);
				}elseif($company_status == 6){
					$company_status_name = "Finished";
					$company_status_name_de = "Abgeschlossen";
					if($partner_lang == "en"){
						$title = "Company";
						$content = 'The status of the company you added has been changed';
					} else {
						$title = "Unternehmens";
						$content = 'Der Status von Ihnen hinzugefügten Unternehmens wurde geändert';
					}
					$payload = '{"company_id": "'.$company_id.'"}';
					$notification_title		='{ "en":"Company", "de":"Unternehmens" }';
					$notification_content 	='{ "en":"The status of the company you added has been changed to '.$company_status_name.'", "de":"Der Status des von Ihnen hinzugefügten Unternehmens wurde in '.$company_status_name_de.' geändert."}';
					$action="NAVIGATE_TO_COMPANY";
					createPartnerPersonalNotification($partner_id, $notification_type, $payload, $action, $notification_title, $notification_content);
					sendNotification($onesignal_id, $title, $content);
				}
			}

			$query = $db->prepare("
							UPDATE idk_companies
							SET	company_name = :company_name, company_type = :company_type, company_idnum = :company_idnum, company_taxnum = :company_taxnum, company_address = :company_address, company_zipcode = :company_zipcode, company_city = :company_city, company_state = :company_state, company_country = :company_country, company_info = :company_info, company_contact_type = :company_contact_type, company_logo = :company_logo, company_status=:company_status
							WHERE company_id = :company_id");

			$query->execute(array(
					':company_name' => $company_name,
					':company_type' => $company_type,
					':company_idnum' => $company_idnum,
					':company_taxnum' => $company_taxnum,
					':company_address' => $company_address,
					':company_zipcode' => $company_zipcode,
					':company_city' => $company_city,
					':company_state' => $company_state,
					':company_country' => $company_country,
					':company_info' => $company_info,
					':company_contact_type' => $company_contact_type,
					':company_logo' => $company_logo_final,
					':company_status' => $company_status,
					':company_id' => $company_id));

		//Add to LOGS
		$log_desc = "Uredio profil kompanije: " . $company_name . "";
		$log_date = date('Y-m-d H:i:s');

		$log_query = $db->prepare("
						INSERT INTO idk_logs
							(log_employeeid, log_desc, log_date)
						VALUES
							(:log_employeeid, :log_desc, :log_date)");

		$log_query->execute(array(
						':log_employeeid' => $logged_employee_id,
						':log_desc' => $log_desc,
						':log_date' => $log_date));

		header("Location: companies?page=open&id=$company_id&mess=21");

break;

/***********************
KANDIDATI
************************/
case "cv_de_edit":
	$kandidat_id = $_POST['kandidat_id'];
	$kde_spol = $_POST['kde_spol'];
	$kde_drzavljanstvo = $_POST['kde_drzavljanstvo'];
	$kde_prijava_na = $_POST['kde_prijava_na'];
	$kde_vozacka = $_POST['kde_vozacka'];
	
	$query_update = $db->prepare("
		UPDATE idk_kandidat_de
		SET	kde_spol = :kde_spol, kde_drzavljanstvo = :kde_drzavljanstvo, kde_prijava_na = :kde_prijava_na, kde_vozacka = :kde_vozacka
		WHERE kde_kandidat_id = :kandidat_id");

	$query_update->execute(array(
		':kde_spol' => $kde_spol,
		':kde_drzavljanstvo' => $kde_drzavljanstvo,
		':kde_prijava_na' => $kde_prijava_na,
		':kde_vozacka' => $kde_vozacka,
		':kandidat_id' => $kandidat_id
		));

	$select_query_iskustvo = $db->prepare("
		SELECT kri_id, kri_pozicija_de, kri_opis_de
		FROM idk_kandidat_radno_iskustvo
		WHERE kri_kandidat_id = :kri_kandidat_id");

	$select_query_iskustvo->execute(array(
					':kri_kandidat_id' => $kandidat_id));

	$rc = $select_query_iskustvo->rowCount();
	$iskustvo_array = array();

	if($rc > 0){

		while($select_row = $select_query_iskustvo->fetch()) {
			$iskustvo_array[$select_row['kri_id']] = $select_row['kri_id'];
		}

		foreach($iskustvo_array as $kri_id){
			$inp_pozicija = $_POST['kri_pozicija'.$kri_id];
			$inp_opis = $_POST['kri_opis'.$kri_id];
			$kri_naziv_de = $_POST['kri_naziv'.$kri_id];
			$kri_grad_de = $_POST['kri_grad'.$kri_id];

			$query_update = $db->prepare("
				UPDATE idk_kandidat_radno_iskustvo
				SET	kri_pozicija_de = :kri_pozicija_de, kri_opis_de = :kri_opis_de, kri_naziv_de = :kri_naziv_de, kri_grad_de = :kri_grad_de
				WHERE kri_id = :kri_id");

			$query_update->execute(array(
				':kri_pozicija_de' => $inp_pozicija,
				':kri_opis_de' => $inp_opis,
				':kri_naziv_de' => $kri_naziv_de,
				':kri_grad_de' => $kri_grad_de,
				':kri_id' => $kri_id
				));

		}

	}

	$select_query_obrazovanje = $db->prepare("
		SELECT ke_id, ke_naziv_kvalifikacije_de, ke_opis_de
		FROM idk_kandidat_edukacija
		WHERE ke_kandidat_id = :ke_kandidat_id");

	$select_query_obrazovanje->execute(array(
					':ke_kandidat_id' => $kandidat_id));

	$rc2 = $select_query_obrazovanje->rowCount();
	
	$obrazovanje_array = array();

	if($rc2 > 0){

		while($select_row = $select_query_obrazovanje->fetch()) {
			$obrazovanje_array[$select_row['ke_id']] = $select_row['ke_id'];
		}

		foreach($obrazovanje_array as $ke_id){
			$inp_kvalifikacija = $_POST['ke_naziv_kvalifikacije'.$ke_id];
			$inp_opis = $_POST['ke_opis'.$ke_id];
			$ke_naziv_de = $_POST['ke_naziv'.$ke_id];
			$ke_grad_de = $_POST['ke_grad'.$ke_id];

			$query_update = $db->prepare("
				UPDATE idk_kandidat_edukacija
				SET	ke_naziv_kvalifikacije_de = :ke_naziv_kvalifikacije_de, ke_opis_de = :ke_opis_de, ke_naziv_de = :ke_naziv_de, ke_grad_de = :ke_grad_de
				WHERE ke_id = :ke_id");

			$query_update->execute(array(
				':ke_naziv_kvalifikacije_de' => $inp_kvalifikacija,
				':ke_opis_de' => $inp_opis,
				':ke_naziv_de' => $ke_naziv_de,
				':ke_grad_de' => $ke_grad_de,
				':ke_id' => $ke_id
				));

		}

	}

	$select_query_vjestine2 = $db->prepare("
		SELECT kv_id, kv_naziv_de, kv_opis_de
		FROM idk_kandidat_vjestine
		WHERE kv_kandidat_id = :kv_kandidat_id");

	$select_query_vjestine2->execute(array(
					':kv_kandidat_id' => $kandidat_id));

	$rc3 = $select_query_vjestine2->rowCount();

	$vjestine_array = array();

	if($rc3 > 0){

		while($select_row = $select_query_vjestine2->fetch()) {
			$vjestine_array[$select_row['kv_id']] = $select_row['kv_id'];
		}

		foreach($vjestine_array as $kv_id){
			$inp_naziv = $_POST['kv_naziv'.$kv_id];
			$inp_opis = $_POST['kv_opis'.$kv_id];

			$query_update = $db->prepare("
				UPDATE idk_kandidat_vjestine
				SET	kv_naziv_de = :kv_naziv_de, kv_opis_de = :kv_opis_de
				WHERE kv_id = :kv_id");

			$query_update->execute(array(
				':kv_naziv_de' => $inp_naziv,
				':kv_opis_de' => $inp_opis,
				':kv_id' => $kv_id
				));

		}


	}

	header("Location: do.php?form=cv_profil_gen_de&kand_id=$kandidat_id");

break;

case "profil_de_edit":
	$kandidat_id = $_POST['kandidat_id'];
	$kde_spol = $_POST['kde_spol'];
	$kde_drzavljanstvo = $_POST['kde_drzavljanstvo'];
	$kde_prijava_na = $_POST['kde_prijava_na'];
	$kde_vozacka = $_POST['kde_vozacka'];
	
	$query_update = $db->prepare("
		UPDATE idk_kandidat_de
		SET	kde_spol = :kde_spol, kde_drzavljanstvo = :kde_drzavljanstvo, kde_prijava_na = :kde_prijava_na, kde_vozacka = :kde_vozacka
		WHERE kde_kandidat_id = :kandidat_id");

	$query_update->execute(array(
		':kde_spol' => $kde_spol,
		':kde_drzavljanstvo' => $kde_drzavljanstvo,
		':kde_prijava_na' => $kde_prijava_na,
		':kde_vozacka' => $kde_vozacka,
		':kandidat_id' => $kandidat_id
		));

	$select_query_iskustvo = $db->prepare("
		SELECT kri_id, kri_pozicija_de, kri_opis_de
		FROM idk_kandidat_radno_iskustvo
		WHERE kri_kandidat_id = :kri_kandidat_id");

	$select_query_iskustvo->execute(array(
					':kri_kandidat_id' => $kandidat_id));

	$rc = $select_query_iskustvo->rowCount();
	$iskustvo_array = array();

	if($rc > 0){

		while($select_row = $select_query_iskustvo->fetch()) {
			$iskustvo_array[$select_row['kri_id']] = $select_row['kri_id'];
		}

		foreach($iskustvo_array as $kri_id){
			$inp_pozicija = $_POST['kri_pozicija'.$kri_id];
			$inp_opis = $_POST['kri_opis'.$kri_id];

			$query_update = $db->prepare("
				UPDATE idk_kandidat_radno_iskustvo
				SET	kri_pozicija_de = :kri_pozicija_de, kri_opis_de = :kri_opis_de
				WHERE kri_id = :kri_id");

			$query_update->execute(array(
				':kri_pozicija_de' => $inp_pozicija,
				':kri_opis_de' => $inp_opis,
				':kri_id' => $kri_id
				));

		}

	}

	$select_query_obrazovanje = $db->prepare("
		SELECT ke_id, ke_naziv_kvalifikacije_de, ke_opis_de
		FROM idk_kandidat_edukacija
		WHERE ke_kandidat_id = :ke_kandidat_id");

	$select_query_obrazovanje->execute(array(
					':ke_kandidat_id' => $kandidat_id));

	$rc2 = $select_query_obrazovanje->rowCount();
	
	$obrazovanje_array = array();

	if($rc2 > 0){

		while($select_row = $select_query_obrazovanje->fetch()) {
			$obrazovanje_array[$select_row['ke_id']] = $select_row['ke_id'];
		}

		foreach($obrazovanje_array as $ke_id){
			$inp_kvalifikacija = $_POST['ke_naziv_kvalifikacije'.$ke_id];
			$inp_opis = $_POST['ke_opis'.$ke_id];

			$query_update = $db->prepare("
				UPDATE idk_kandidat_edukacija
				SET	ke_naziv_kvalifikacije_de = :ke_naziv_kvalifikacije_de, ke_opis_de = :ke_opis_de
				WHERE ke_id = :ke_id");

			$query_update->execute(array(
				':ke_naziv_kvalifikacije_de' => $inp_kvalifikacija,
				':ke_opis_de' => $inp_opis,
				':ke_id' => $ke_id
				));

		}

	}

	$select_query_vjestine2 = $db->prepare("
		SELECT kv_id, kv_naziv_de, kv_opis_de
		FROM idk_kandidat_vjestine
		WHERE kv_kandidat_id = :kv_kandidat_id");

	$select_query_vjestine2->execute(array(
					':kv_kandidat_id' => $kandidat_id));

	$rc3 = $select_query_vjestine2->rowCount();

	$vjestine_array = array();

	if($rc3 > 0){

		while($select_row = $select_query_vjestine2->fetch()) {
			$vjestine_array[$select_row['kv_id']] = $select_row['kv_id'];
		}

		foreach($vjestine_array as $kv_id){
			$inp_naziv = $_POST['kv_naziv'.$kv_id];
			$inp_opis = $_POST['kv_opis'.$kv_id];

			$query_update = $db->prepare("
				UPDATE idk_kandidat_vjestine
				SET	kv_naziv_de = :kv_naziv_de, kv_opis_de = :kv_opis_de
				WHERE kv_id = :kv_id");

			$query_update->execute(array(
				':kv_naziv_de' => $inp_naziv,
				':kv_opis_de' => $inp_opis,
				':kv_id' => $kv_id
				));

		}


	}

	header("Location: profil_de?page=edited&id=$kandidat_id&mess=1");

break;

case "cv_gen_de":

$kandidat_id = $_GET['kand_id'];

$query = $db->prepare("
				SELECT kandidat_ime, kandidat_prezime, kandidat_datumrodjenja, kandidat_mjestorodjenja, kandidat_adresa, kandidat_grad, kandidat_pbroj, kandidat_drzava, kandidat_slika, cv_de, kde_kandidat_id, kde_prijava_na, kde_spol, kde_drzavljanstvo, kde_vozacka, kandidat_procjenatermina, datum_termina, datum_aplikacije
				FROM idk_kandidati
				INNER JOIN idk_kandidat_de ON idk_kandidati.kandidat_id = idk_kandidat_de.kde_kandidat_id
				WHERE kandidat_id = :kandidat_id");

$query->execute(array(
			':kandidat_id' => $kandidat_id));

$row = $query->fetch();

$kandidat_ime = $row['kandidat_ime'];
$kandidat_prezime = $row['kandidat_prezime'];
$kandidat_spol = $row['kde_spol'];
$kandidat_drzavljanstvo = $row['kde_drzavljanstvo'];
$kandidat_prijava_na = $row['kde_prijava_na'];
$kandidat_vozacka_dozvola = $row['kde_vozacka'];
$kandidat_datumrodjenja = date('d.m.Y.', strtotime($row['kandidat_datumrodjenja']));
$kandidat_datum_aplikacije = date('d.m.Y', strtotime($row['datum_aplikacije']));

$kandidat_procjenatermina = $row['kandidat_procjenatermina'];
$kandidat_datum_termina = date('d.m.Y', strtotime($row['datum_termina']));

if($row['datum_termina'] !=NULL){
	if($kandidat_procjenatermina == 0){
		$kandidat_datum_termina_txt = "<li>Entgültiger Termin bei der Deutschen Botschaft: ".$kandidat_datum_termina."</li>";
	}else{
		$kandidat_datum_termina_txt = "<li>Terminanfrage bei der Deutschen Botschaft: ".$kandidat_datum_aplikacije."</li>";
		
	}
}else{
	$kandidat_datum_termina_txt = "";
}

$kandidat_adresa = $row['kandidat_adresa'];
$kandidat_pbroj = $row['kandidat_pbroj'];
$kandidat_grad = $row['kandidat_grad'];

$cv_de = $row['cv_de'];

if($row['kandidat_slika'] == "none"){
	$kandidat_slika = "none.jpg";
}else{
	$kandidat_slika = $row['kandidat_slika'];
}


//KONTAKTI
$select_query = $db->prepare("
					SELECT kki_id, kki_grupa, kki_naziv_de, kki_podatak
					FROM idk_kandidat_kontakt_info
					WHERE kki_kandidat_id = :kki_kandidat_id");

$select_query->execute(array(
				':kki_kandidat_id' => $kandidat_id));

while($select_row = $select_query->fetch()) {

	$kki_id = $select_row['kki_id'];
	$kki_grupa = $select_row['kki_grupa'];
	if($kki_grupa == 1){
		$kki_grupa = "Telefon";
		$ikona = 'fa-mobile';
	}elseif($kki_grupa == 2){
		$kki_grupa = "E-mail";
		$ikona = 'fa-envelope';
	}elseif($kki_grupa == 3){
		$kki_grupa = "Web";
		$ikona = 'fa-globe';
	}elseif($kki_grupa == 4){
		$kki_grupa = "Messaging";
		$ikona = 'fa-skype';
	}else{};
	$kki_naziv = $select_row['kki_naziv_de'];
	$kki_podatak = $select_row['kki_podatak'];
	
	if($kki_grupa == "Telefon"){
		$kki_naziv = "Telefonnummer";
	}else{
		$kki_naziv = $kki_naziv;
	}

	$kandidat_kontakti[] = "
		<li>$kki_naziv: $kki_podatak</li>
	";
}

//IKSUSTVO
$select_query_iskustvo = $db->prepare("
						SELECT kri_id, kri_darum_od, kri_datum_do, kri_pozicija_de, kri_naziv, kri_grad_de, kri_opis_de, kri_naziv_de, kri_aktuelno
						FROM idk_kandidat_radno_iskustvo
						WHERE kri_kandidat_id = :kri_kandidat_id
						ORDER BY kri_order ASC
						");

$select_query_iskustvo->execute(array(
				':kri_kandidat_id' => $kandidat_id));

	while($select_row = $select_query_iskustvo->fetch()) {

		$kri_id = $select_row['kri_id'];
		$kri_darum_od = date('m.Y', strtotime($select_row['kri_darum_od']));
		$kri_datum_do = $select_row['kri_datum_do'];
		$kri_pozicija = $select_row['kri_pozicija_de'];
		$kri_naziv = $select_row['kri_naziv_de'];
		$kri_grad = $select_row['kri_grad_de'];
		$kri_opis = $select_row['kri_opis_de'];
		$kri_aktuelno = $select_row['kri_aktuelno'];
		
		if($kri_aktuelno != 1){
			$kri_datum_do_f = date('m.Y', strtotime($kri_datum_do));
		}else{
			$kri_datum_do_f = "Aktuell";
		}						

$kandidat_iskustvo[] = "


<article> 
<h2>$kri_pozicija</h2>
<p class='subDetails'>$kri_darum_od - $kri_datum_do_f</p>
<p>$kri_naziv<br/>
		$kri_grad<br/>
		$kri_opis<br/>
		<p>
</article>

";
}

//OBRAZOVANJE
$select_query_obrazovanje = $db->prepare("
					SELECT ke_id, ke_datumod, ke_datumdo, ke_naziv_kvalifikacije_de, ke_naziv, ke_grad, ke_opis_de, ke_naziv_de, ke_grad_de, ke_aktuelno
					FROM idk_kandidat_edukacija
					WHERE ke_kandidat_id = :ke_kandidat_id
					ORDER BY ke_orderid ASC
					");

$select_query_obrazovanje->execute(array(
				':ke_kandidat_id' => $kandidat_id));

while($select_row = $select_query_obrazovanje->fetch()) {

	$ke_id = $select_row['ke_id'];
	$ke_datumod = date('m.Y', strtotime($select_row['ke_datumod']));
	$ke_datumdo = $select_row['ke_datumdo'];
	$ke_naziv_kvalifikacije = $select_row['ke_naziv_kvalifikacije_de'];
	$ke_naziv = $select_row['ke_naziv_de'];
	$ke_grad = $select_row['ke_grad_de'];
	$ke_opis = $select_row['ke_opis_de'];
	$ke_aktuelno = $select_row['ke_aktuelno'];
	
	if($ke_aktuelno != 1){
		$ke_datumdo_f = date('m.Y', strtotime($ke_datumdo));
	}else{
		$ke_datumdo_f = "Aktuell";
	}	

	$kandidat_obrazovanje[] = "

	<article> 
		<h2>$ke_naziv_kvalifikacije</h2>
		<p class='subDetails'>$ke_datumod - $ke_datumdo_f</p>
		<p>
			$ke_naziv<br/>
			$ke_grad<br/>
			$ke_opis<br/>
		<p>
	</article>
	";
}


//VJEŠTINE

$select_query_vjestine = $db->prepare("
				SELECT kj_id, kj_naziv_de, kj_slusanje_de, kj_citanje_de, kj_govorna_interakcija_de, kj_govorna_produkcija_de, kj_pisanje_de
				FROM idk_kandidat_jezici
				WHERE kj_kandidatid = :kj_kandidatid");

$select_query_vjestine->execute(array(
			':kj_kandidatid' => $kandidat_id));

while($select_row = $select_query_vjestine->fetch()) {

	$kj_id = $select_row['kj_id'];
	$kj_naziv = $select_row['kj_naziv_de'];
	$kj_slusanje = $select_row['kj_slusanje_de'];
	$kj_citanje = $select_row['kj_citanje_de'];
	$kj_govorna_interakcija = $select_row['kj_govorna_interakcija_de'];
	$kj_govorna_produkcija = $select_row['kj_govorna_produkcija_de'];
	$kj_pisanje = $select_row['kj_pisanje_de'];
	
	$kandidati_jezici[] = "
		<tr>
			<td> $kj_naziv</td>
			<td> $kj_slusanje </td>
			<td> $kj_citanje </td>
			<td> $kj_govorna_interakcija </td>
			<td> $kj_govorna_produkcija </td>
			<td> $kj_pisanje </td>
		</tr>
	";
}


$select_query_vjestine2 = $db->prepare("
					SELECT kv_id, kv_naziv_de, kv_grupa, kv_opis_de
					FROM idk_kandidat_vjestine
					WHERE kv_kandidat_id = :kv_kandidat_id");

$select_query_vjestine2->execute(array(
				':kv_kandidat_id' => $kandidat_id));

while($select_row = $select_query_vjestine2->fetch()) {

	$kv_id = $select_row['kv_id'];
	$kv_naziv = $select_row['kv_naziv_de'];
	$kv_grupa = $select_row['kv_grupa'];
	if($kv_grupa == 1){
		$kv_grupa = "Grundlegende Fähigkeiten";
	}elseif($kv_grupa == 2){
		$kv_grupa = "Digitale Fähigkeiten";
	}elseif($kv_grupa == 3){
		$kv_grupa = "Zusätzliche informationen";
	}else{};
	$kv_opis = $select_row['kv_opis_de'];

	$dodatne_vjestine[] = " 
	<article> 
		<h2>$kv_grupa</h2>
		<p>$kv_naziv<br/>
			$kv_opis<br/>
		<p>
	</article>
	";
}


$dompdf = new Dompdf();
$html = "
	<head>
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
		<style>
		
			@page { margin-top: 40px!important; margin-bottom: 40px!important; }
			
			html,body,div,span,object,iframe,h1,h2,h3,h4,h5,h6,p,blockquote,pre,abbr,address,cite,code,del,dfn,em,img,ins,kbd,q,samp,small,strong,sub,sup,var,b,i,dl,dt,dd,ol,ul,li,fieldset,form,label,legend,table,caption,tbody,tfoot,thead,tr,th,td,article,aside,canvas,details,figcaption,figure,footer,header,hgroup,menu,nav,section,summary,time,mark,audio,video {
				border:0;
				font:inherit;
				margin:0;
				padding:0;
				vertical-align:baseline;
				font-family: 'DejaVu Sans', sans-serif !important;
				}
				
				article,aside,details,figcaption,figure,footer,header,hgroup,menu,nav,section {
					display:block;
				}
				
				.clear {clear: both;}
				
				p {
					font-size: 14px;
					line-height: 1.4em;
					margin-bottom: 20px;
					color: #444;
				}
				
				#cv {
					background: #fff;
				}
				
				.mainDetails {
					padding: 40px 35px;
					border-bottom: 2px solid #3d7b85;
					background: #ededed;					
					margin-top: -40px;
				}
				
				#name h1 {
					font-size: 2em;
					font-weight: 700;
					margin-bottom: -6px;
				}
				
				#name h2 {
					font-size: 1.3em;
					margin-top: 5px;
					margin-left: 2px;
				}

				#name h3 {
					font-size: 1em;
					margin-top: 5px;
					margin-left: 2px;
				}
				
				#mainArea {
					padding: 0 40px;
				}
				
				#headshot {
					width: 150px;
					float: left;
					margin-right: 30px;
				}
				
				#headshot img {
					width: 100%;
					height: auto;
				}
				
				#name {
					float: left;
					margin-left: 20px;
				}
				
				#contactDetails {
					float: right;
				}
				
				#contactDetails ul {
					list-style-type: none;
					font-size: 0.9em;
					margin-top: 2px;
				}
				
				#contactDetails ul li {
					margin-bottom: 3px;
					color: #444;
				}
				
				#contactDetails ul li a, a[href^=tel] {
					color: #444; 
					text-decoration: none;
					-webkit-transition: all .3s ease-in;
					-moz-transition: all .3s ease-in;
					-o-transition: all .3s ease-in;
					-ms-transition: all .3s ease-in;
					transition: all .3s ease-in;
				}
				
				#contactDetails ul li a:hover { 
					color: #cf8a05;
				}
				
				
				section {
					border-top: 1px solid #dedede;
					padding: 20px 0 0;
				}
				
				section:first-child {
					border-top: 0;
				}
				
				section:last-child {
					padding: 20px 0 10px;
				}
				
				.sectionTitle {
					float: left;
					width: 25%;
				}
				
				.sectionContent {
					float: right;
					width: 72.5%;
				}

				.sectionContent ul{
					list-style-type: none;
					color: #333;
					margin-bottom: 20px;
				}

				.sectionContent li{
					margin: 5px 0;
					font-size: 14px;
				}
				
				.sectionTitle h1 {
					
					font-style: italic;
					font-size: 20px;
					color: #3d7b85;
				}
				
				.sectionTitle.headerTitle h1 {
					
					font-style: italic;
					font-size: 16px;
					color: #3d7b85;
				}
				
				.sectionContent h2 {					
					font-size: 1.5em;
					margin-bottom: -2px;
				}
				
				.subDetails {
					font-size: 0.8em;
					font-style: italic;
					margin-bottom: 3px;
				}
				
				.keySkills {
					list-style-type: none;
					-moz-column-count:3;
					-webkit-column-count:3;
					column-count:3;
					margin-bottom: 20px;
					font-size: 1em;
					color: #444;
				}
				
				.keySkills ul li {
					margin-bottom: 3px;
				}
				
				@media all and (min-width: 602px) and (max-width: 800px) {
					#headshot {
						display: none;
					}
					
					.keySkills {
					-moz-column-count:2;
					-webkit-column-count:2;
					column-count:2;
					}
				}
				
				@media all and (max-width: 601px) {
					#cv {
						width: 95%;
						margin: 10px auto;
						min-width: 280px;
					}
					
					#headshot {
						display: none;
					}
					
					#name, #contactDetails {
						float: none;
						width: 100%;
						text-align: center;
					}
					
					.sectionTitle, .sectionContent {
						float: none;
						width: 100%;
					}
					
					.sectionTitle {
						margin-left: -2px;
						font-size: 1.25em;
					}
					
					.keySkills {
						-moz-column-count:2;
						-webkit-column-count:2;
						column-count:2;
					}
				}
				
				@media all and (max-width: 480px) {
					.mainDetails {
						padding: 15px 15px;
					}
					
					section {
						padding: 15px 0 0;
					}
					
					#mainArea {
						padding: 0 25px;
					}
				
					
					.keySkills {
					-moz-column-count:1;
					-webkit-column-count:1;
					column-count:1;
					}
					
					#name h1 {
						line-height: .8em;
						margin-bottom: 4px;
					}
				}
				
				@media print {
					#cv {
						width: 100%;
					}
				}
		</style>
	<title>CV - $kandidat_ime $kandidat_prezime</title>
	</head>


	<body id='top'>
	<div id='cv' class='instaFade'>
		<div class='mainDetails'>
			<div id='headshot' class='quickFade'>
			<img src='".getsiteurl()."images/Jobstep-logo_news.png'/>
			</div>
			
			<div id='name'>
				<h1 class='quickFade delayTwo'>$kandidat_ime $kandidat_prezime</h1>
				<!--<h2 class='quickFade delayThree'>Angestrebte Stelle:</h2>
				<h3 class='quickFade delayThree'>$kandidat_prijava_na</h3>-->
			</div>
			
			<div id='contactDetails' class='quickFade delayFour'>
				<img src='".getsiteurl()."files/kandidati/$kandidat_slika' width=auto; height=100;/>
			</div>
			<div class='clear'></div>
			
			<div class='sectionTitle headerTitle'>
				<h1>Angestrebte Stelle:</h1>
			</div>
			
			<div class='sectionContent'>
				<h3 class='quickFade delayThree'>$kandidat_prijava_na</h3>
			</div>			
			<div class='clear'></div>
		</div>
		
		<div id='mainArea'>
			<section>
				<article>
					<div class='sectionTitle'>
						<h1>Persönliche Informationen:</h1>
					</div>
					
					<div class='sectionContent'>
						<ul>
							<!--<li>ID: $kandidat_id</li>-->
							<li>Adresse: $kandidat_adresa, $kandidat_pbroj, $kandidat_grad</li>
							<li>Geschlecht: $kandidat_spol</li>
							<li>Geburtsdatum: $kandidat_datumrodjenja</li>
							<li>Staatsangehörigkeit: $kandidat_drzavljanstvo</li>
							$kandidat_datum_termina_txt
							<li>Führerschein / Kategorie: $kandidat_vozacka_dozvola</li>
						</ul>
					</div>
				</article>
				<div class='clear'></div>
			</section>

			<section>
				<article>
					<div class='sectionTitle'>
						<h1>Kontakt:</h1>
					</div>
					
					<div class='sectionContent'>
						<ul>
							";
							foreach($kandidat_kontakti as $kontakt){
								$html.= $kontakt . "<br/>";
							}
							$html.="
						</ul>
					</div>
				</article>
				<div class='clear'></div>
			</section>		
			
			<section>
				<div class='sectionTitle'>
					<h1>Berufserfahrung:</h1>
				</div>
				
				<div class='sectionContent'>
				";
				foreach($kandidat_iskustvo as $iskustvo){
					$html.= $iskustvo;
				}
				$html.="
				</div>
				<div class='clear'></div>
			</section>

			<section>
				<div class='sectionTitle'>
					<h1>Schul-und Berufsbildung:</h1>
				</div>
				
				<div class='sectionContent'>
				";
				foreach($kandidat_obrazovanje as $obrazovanje){
					$html.= $obrazovanje;
				}
				$html.="
				</div>
				<div class='clear'></div>
			</section>
			<style>
				table, td, th {    
					border: 1px solid #ddd;
				}

				th, td {
					padding: 15px;
				}
			</style>
			<section>
				<div class='sectionTitle'>
					<h1>Sprachkenntnisse:</h1>
				</div>
				
				<div class='sectionContent'>
				<table style='font-size: 12px !important;'>
					<tr style='background: #ddd;'>
						<th>Sprache</th>
						<th>Hören</th>
						<th>Lesen</th>
						<th>Sprach-<br/>interaktion</th>
						<th>Sprach-<br/>produktion</th>
						<th>Schreiben</th>
					</tr>

				";
				foreach($kandidati_jezici as $jezik){
					$html.= $jezik;
				}
				$html.="

			  </table>
			  <br/>
				</div>
				<div class='clear'></div>
			</section>
			
			
		</div>
	</div>
	</body>
";

$cv_filename = $kandidat_id."-".$kandidat_ime."_".$kandidat_prezime.".pdf";
$siteUrl = getSiteUrlr();

if($cv_de == 0){
	
	
	
	$dompdf->loadHtml($html);
	$dompdf->setPaper('A4', 'portrait');
	$dompdf->render();
	$pdf = $dompdf->output();
	$file_location = "files/cv/de/".$cv_filename;

	file_put_contents($file_location,$pdf);

	$query_update = $db->prepare("
					UPDATE idk_kandidati
					SET	cv_de = :cv_de
					WHERE kandidat_id = :kandidat_id");

	$query_update->execute(array(
				':cv_de' => 1,
				':kandidat_id' => $kandidat_id
				));
	
	header("Location: kandidati?page=open&id=$kandidat_id&mess=19");
	

}else{
	unlink("files/cv/de/" . $cv_filename);

	$dompdf->loadHtml($html);
	$dompdf->setPaper('A4', 'portrait');
	$dompdf->render();
	$pdf = $dompdf->output();
	$file_location = "files/cv/de/".$cv_filename;
	file_put_contents($file_location,$pdf);

	header("Location: kandidati?page=open&id=$kandidat_id&mess=18");

}

break;

case "cv_gen_ba":

$kandidat_id = $_POST['kand_id'];

$query = $db->prepare("
				SELECT kandidat_ime, kandidat_prezime, kandidat_spol, kandidat_djevojackoprezime, kandidat_jmbg, kandidat_datumrodjenja, kandidat_mjestorodjenja, kandidat_drzavarodjenja, kandidat_drzavljanstvo, kandidat_bracnostanje, kandidat_adresa, kandidat_grad, kandidat_pbroj, kandidat_drzava, kandidat_email, kandidat_slika, kandidat_datetime,kandidat_check, kandidat_vozacka_dozvola, kandidat_prijava_na, cv_ba
				FROM idk_kandidati
				WHERE kandidat_id = :kandidat_id");

$query->execute(array(
			':kandidat_id' => $kandidat_id));

$row = $query->fetch();

$kandidat_ime = $row['kandidat_ime'];
$kandidat_prezime = $row['kandidat_prezime'];
$kandidat_spol = $row['kandidat_spol'];
$kandidat_check = $row['kandidat_check'];
$kandidat_djevojackoprezime = $row['kandidat_djevojackoprezime'];
$kandidat_jmbg = $row['kandidat_jmbg'];
$kandidat_mjestorodjenja = $row['kandidat_mjestorodjenja'];
$kandidat_drzavarodjenja = $row['kandidat_drzavarodjenja'];
$kandidat_drzavljanstvo = $row['kandidat_drzavljanstvo'];
$kandidat_bracnostanje = $row['kandidat_bracnostanje'];
$kandidat_drzava = $row['kandidat_drzava'];
$kandidat_email = $row['kandidat_email'];
$kandidat_drzava = $row['kandidat_drzava'];
$kandidat_datetime = $row['kandidat_datetime'];
$kandidat_vozacka_dozvola = $row['kandidat_vozacka_dozvola'];
$kandidat_prijava_na = $row['kandidat_prijava_na'];
$kandidat_datumrodjenja = date('d.m.Y.', strtotime($row['kandidat_datumrodjenja']));

$kandidat_adresa = $row['kandidat_adresa'];
$kandidat_grad = $row['kandidat_grad'];
$kandidat_pbroj = $row['kandidat_pbroj'];

$cv_ba = $row['cv_ba'];

if($row['kandidat_slika'] == "none"){
	$kandidat_slika = "none.jpg";
}else{
	$kandidat_slika = $row['kandidat_slika'];
}


//KONTAKTI
$select_query = $db->prepare("
					SELECT kki_id, kki_grupa, kki_naziv, kki_podatak
					FROM idk_kandidat_kontakt_info
					WHERE kki_kandidat_id = :kki_kandidat_id");

$select_query->execute(array(
				':kki_kandidat_id' => $kandidat_id));

while($select_row = $select_query->fetch()) {

	$kki_id = $select_row['kki_id'];
	$kki_grupa = $select_row['kki_grupa'];
	if($kki_grupa == 1){
		$kki_grupa = "Telefon";
		$ikona = 'fa-mobile';
	}elseif($kki_grupa == 2){
		$kki_grupa = "E-mail";
		$ikona = 'fa-envelope';
	}elseif($kki_grupa == 3){
		$kki_grupa = "Web";
		$ikona = 'fa-globe';
	}elseif($kki_grupa == 4){
		$kki_grupa = "Messangeri";
		$ikona = 'fa-skype';
	}else{};
	$kki_naziv = $select_row['kki_naziv'];
	$kki_podatak = $select_row['kki_podatak'];

	$kandidat_kontakti[] = "
		<li>$kki_naziv: $kki_podatak</li>
	";
}

//IKSUSTVO
$select_query_iskustvo = $db->prepare("
						SELECT kri_id, kri_darum_od, kri_datum_do, kri_pozicija, kri_naziv, kri_grad, kri_opis, kri_aktuelno
						FROM idk_kandidat_radno_iskustvo
						WHERE kri_kandidat_id = :kri_kandidat_id");

$select_query_iskustvo->execute(array(
				':kri_kandidat_id' => $kandidat_id));

	while($select_row = $select_query_iskustvo->fetch()) {

		$kri_id = $select_row['kri_id'];
		$kri_darum_od = date('m.Y', strtotime($select_row['kri_darum_od']));
		$kri_datum_do = $select_row['kri_datum_do'];
		$kri_pozicija = $select_row['kri_pozicija'];
		$kri_naziv = $select_row['kri_naziv'];
		$kri_grad = $select_row['kri_grad'];
		$kri_opis = $select_row['kri_opis'];
		$kri_aktuelno = $select_row['kri_aktuelno'];
		
		if($kri_aktuelno != 1){
			$kri_datum_do_f = date('m.Y', strtotime($kri_datum_do));
		}else{
			$kri_datum_do_f = "Aktuelno";
		}						

$kandidat_iskustvo[] = "


<article> 
<h2>$kri_pozicija</h2>
<p class='subDetails'>$kri_darum_od - $kri_datum_do_f</p>
<p>$kri_naziv<br/>
		$kri_grad<br/>
		$kri_opis<br/>
		<p>
</article>

";
}

//OBRAZOVANJE
$select_query_obrazovanje = $db->prepare("
					SELECT ke_id, ke_datumod, ke_datumdo, ke_naziv_kvalifikacije, ke_naziv, ke_grad, ke_opis, ke_aktuelno
					FROM idk_kandidat_edukacija
					WHERE ke_kandidat_id = :ke_kandidat_id");

$select_query_obrazovanje->execute(array(
				':ke_kandidat_id' => $kandidat_id));

while($select_row = $select_query_obrazovanje->fetch()) {

	$ke_id = $select_row['ke_id'];
	$ke_datumod = date('m.Y', strtotime($select_row['ke_datumod']));
	$ke_datumdo = $select_row['ke_datumdo'];
	$ke_naziv_kvalifikacije = $select_row['ke_naziv_kvalifikacije'];
	$ke_naziv = $select_row['ke_naziv'];
	$ke_grad = $select_row['ke_grad'];
	$ke_opis = $select_row['ke_opis'];
	$ke_aktuelno = $select_row['ke_aktuelno'];
	
	if($ke_aktuelno != 1){
		$ke_datumdo_f = date('m.Y', strtotime($ke_datumdo));
	}else{
		$ke_datumdo_f = "Aktuelno";
	}	

	$kandidat_obrazovanje[] = "

	<article> 
		<h2>$kri_pozicija</h2>
		<p class='subDetails'>$ke_datumod - $ke_datumdo_f</p>
		<p>$ke_naziv_kvalifikacije<br/>
			$ke_naziv<br/>
			$ke_opis<br/>
		<p>
	</article>
	";
}


//VJEŠTINE

$select_query_vjestine = $db->prepare("
				SELECT kj_id, kj_naziv, kj_slusanje, kj_citanje, kj_govorna_interakcija, kj_govorna_produkcija, kj_pisanje
				FROM idk_kandidat_jezici
				WHERE kj_kandidatid = :kj_kandidatid");

$select_query_vjestine->execute(array(
			':kj_kandidatid' => $kandidat_id));

while($select_row = $select_query_vjestine->fetch()) {

	$kj_id = $select_row['kj_id'];
	$kj_naziv = $select_row['kj_naziv'];
	$kj_slusanje = $select_row['kj_slusanje'];
	$kj_citanje = $select_row['kj_citanje'];
	$kj_govorna_interakcija = $select_row['kj_govorna_interakcija'];
	$kj_govorna_produkcija = $select_row['kj_govorna_produkcija'];
	$kj_pisanje = $select_row['kj_pisanje'];
	
	$kandidati_jezici[] = "
		<tr>
			<td> $kj_naziv</td>
			<td> $kj_slusanje </td>
			<td> $kj_citanje </td>
			<td> $kj_govorna_interakcija </td>
			<td> $kj_govorna_produkcija </td>
			<td> $kj_pisanje </td>
		</tr>
	";
}


$select_query_vjestine2 = $db->prepare("
					SELECT kv_id, kv_naziv, kv_grupa, kv_opis
					FROM idk_kandidat_vjestine
					WHERE kv_kandidat_id = :kv_kandidat_id");

$select_query_vjestine2->execute(array(
				':kv_kandidat_id' => $kandidat_id));

while($select_row = $select_query_vjestine2->fetch()) {

	$kv_id = $select_row['kv_id'];
	$kv_naziv = $select_row['kv_naziv'];
	$kv_grupa = $select_row['kv_grupa'];
	if($kv_grupa == 1){
		$kv_grupa = "Osnovne vještine";
	}elseif($kv_grupa == 2){
		$kv_grupa = "Digitalne kompetencije";
	}elseif($kv_grupa == 3){
		$kv_grupa = "Dodatne informacije";
	}else{};
	$kv_opis = $select_row['kv_opis'];

	$dodatne_vjestine[] = " 
	<article> 
		<h2>$kv_grupa</h2>
		<p>$kv_naziv<br/>
			$kv_opis<br/>
		<p>
	</article>
	";
}


$dompdf = new Dompdf();
$html = "
	<head>
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
		<style>
		
			@page { margin-top: 40px!important; margin-bottom: 40px!important; }
			
			html,body,div,span,object,iframe,h1,h2,h3,h4,h5,h6,p,blockquote,pre,abbr,address,cite,code,del,dfn,em,img,ins,kbd,q,samp,small,strong,sub,sup,var,b,i,dl,dt,dd,ol,ul,li,fieldset,form,label,legend,table,caption,tbody,tfoot,thead,tr,th,td,article,aside,canvas,details,figcaption,figure,footer,header,hgroup,menu,nav,section,summary,time,mark,audio,video {
				border:0;
				font:inherit;
				margin:0;
				padding:0;
				vertical-align:baseline;
				font-family: 'DejaVu Sans', sans-serif !important;
				}
				
				article,aside,details,figcaption,figure,footer,header,hgroup,menu,nav,section {
					display:block;
				}
				
				.clear {clear: both;}
				
				p {
					font-size: 14px;
					line-height: 1.4em;
					margin-bottom: 20px;
					color: #444;
				}
				
				#cv {
					background: #fff;
				}
				
				.mainDetails {
					padding: 40px 35px;
					border-bottom: 2px solid #cf8a05;
					background: #ededed;					
					margin-top: -40px;
				}
				
				#name h1 {
					font-size: 2em;
					font-weight: 700;
					margin-bottom: -6px;
				}
				
				#name h2 {
					font-size: 1.3em;
					margin-top: 5px;
					margin-left: 2px;
				}

				#name h3 {
					font-size: 1em;
					margin-top: 5px;
					margin-left: 2px;
				}
				
				#mainArea {
					padding: 0 40px;
				}
				
				#headshot {
					width: 150px;
					float: left;
					margin-right: 30px;
				}
				
				#headshot img {
					width: 100%;
					height: auto;
				}
				
				#name {
					float: left;
					margin-left: 20px;
				}
				
				#contactDetails {
					float: right;
				}
				
				#contactDetails ul {
					list-style-type: none;
					font-size: 0.9em;
					margin-top: 2px;
				}
				
				#contactDetails ul li {
					margin-bottom: 3px;
					color: #444;
				}
				
				#contactDetails ul li a, a[href^=tel] {
					color: #444; 
					text-decoration: none;
					-webkit-transition: all .3s ease-in;
					-moz-transition: all .3s ease-in;
					-o-transition: all .3s ease-in;
					-ms-transition: all .3s ease-in;
					transition: all .3s ease-in;
				}
				
				#contactDetails ul li a:hover { 
					color: #cf8a05;
				}
				
				
				section {
					border-top: 1px solid #dedede;
					padding: 20px 0 0;
				}
				
				section:first-child {
					border-top: 0;
				}
				
				section:last-child {
					padding: 20px 0 10px;
				}
				
				.sectionTitle {
					float: left;
					width: 25%;
				}
				
				.sectionContent {
					float: right;
					width: 72.5%;
				}

				.sectionContent ul{
					list-style-type: none;
					color: #333;
					margin-bottom: 20px;
				}

				.sectionContent li{
					margin: 5px 0;
					font-size: 14px;
				}
				
				.sectionTitle h1 {
					
					font-style: italic;
					font-size: 20px;
					color: #cf8a05;
				}
				
				.sectionContent h2 {					
					font-size: 1.5em;
					margin-bottom: -2px;
				}
				
				.subDetails {
					font-size: 0.8em;
					font-style: italic;
					margin-bottom: 3px;
				}
				
				.keySkills {
					list-style-type: none;
					-moz-column-count:3;
					-webkit-column-count:3;
					column-count:3;
					margin-bottom: 20px;
					font-size: 1em;
					color: #444;
				}
				
				.keySkills ul li {
					margin-bottom: 3px;
				}
				
				@media all and (min-width: 602px) and (max-width: 800px) {
					#headshot {
						display: none;
					}
					
					.keySkills {
					-moz-column-count:2;
					-webkit-column-count:2;
					column-count:2;
					}
				}
				
				@media all and (max-width: 601px) {
					#cv {
						width: 95%;
						margin: 10px auto;
						min-width: 280px;
					}
					
					#headshot {
						display: none;
					}
					
					#name, #contactDetails {
						float: none;
						width: 100%;
						text-align: center;
					}
					
					.sectionTitle, .sectionContent {
						float: none;
						width: 100%;
					}
					
					.sectionTitle {
						margin-left: -2px;
						font-size: 1.25em;
					}
					
					.keySkills {
						-moz-column-count:2;
						-webkit-column-count:2;
						column-count:2;
					}
				}
				
				@media all and (max-width: 480px) {
					.mainDetails {
						padding: 15px 15px;
					}
					
					section {
						padding: 15px 0 0;
					}
					
					#mainArea {
						padding: 0 25px;
					}
				
					
					.keySkills {
					-moz-column-count:1;
					-webkit-column-count:1;
					column-count:1;
					}
					
					#name h1 {
						line-height: .8em;
						margin-bottom: 4px;
					}
				}
				
				@media print {
					#cv {
						width: 100%;
					}
				}
		</style>
	<title>CV - $kandidat_ime $kandidat_prezime</title>
	</head>


	<body id='top'>
	<div id='cv' class='instaFade'>
		<div class='mainDetails'>
			<div id='headshot' class='quickFade'>
			<img src='".getsiteurl()."images/Jobstep-logo.png'/>
			</div>
			
			<div id='name'>
				<h1 class='quickFade delayTwo'>$kandidat_ime $kandidat_prezime</h1>
				<h2 class='quickFade delayThree'>Prijava za radno mjesto:</h2>
				<h3 class='quickFade delayThree'>$kandidat_prijava_na</h3>
			</div>
			
			<div id='contactDetails' class='quickFade delayFour'>
				<img src='".getsiteurl()."files/kandidati/$kandidat_slika' width=auto; height=100;/>
			</div>
			<div class='clear'></div>
		</div>
		
		<div id='mainArea'>
			<section>
				<article>
					<div class='sectionTitle'>
						<h1>Osobne informacije</h1>
					</div>
					
					<div class='sectionContent'>
						<ul>
							<li>ID: $kandidat_id</li>
							<li>Adresa: $kandidat_adresa, $kandidat_pbroj, $kandidat_grad</li>
							<li>Spol: $kandidat_spol</li>
							<li>Datum rođenja: $kandidat_datumrodjenja</li>
							<li>Državljanstvo: $kandidat_drzavljanstvo</li>
						</ul>
					</div>
				</article>
				<div class='clear'></div>
			</section>

			<section>
				<article>
					<div class='sectionTitle'>
						<h1>Kontakti</h1>
					</div>
					
					<div class='sectionContent'>
						<ul>
							";
							foreach($kandidat_kontakti as $kontakt){
								$html.= $kontakt . "<br/>";
							}
							$html.="
						</ul>
					</div>
				</article>
				<div class='clear'></div>
			</section>		
			
			<section>
				<div class='sectionTitle'>
					<h1>Radno iskustvo</h1>
				</div>
				
				<div class='sectionContent'>
				";
				foreach($kandidat_iskustvo as $iskustvo){
					$html.= $iskustvo;
				}
				$html.="
				</div>
				<div class='clear'></div>
			</section>

			<section>
				<div class='sectionTitle'>
					<h1>Obrazovanje i osposobljavanje</h1>
				</div>
				
				<div class='sectionContent'>
				";
				foreach($kandidat_obrazovanje as $obrazovanje){
					$html.= $obrazovanje;
				}
				$html.="
				</div>
				<div class='clear'></div>
			</section>
			<style>
				table, td, th {    
					border: 1px solid #ddd;
				}

				th, td {
					padding: 15px;
				}
			</style>
			<section>
				<div class='sectionTitle'>
					<h1>Osobne vještine</h1>
				</div>
				
				<div class='sectionContent'>
				<table style='font-size: 12px !important;'>
					<tr style='background: #ddd;'>
						<th>Jezik</th>
						<th>Slušanje</th>
						<th>Čitanje</th>
						<th>Govorna interakcija</th>
						<th>Govorna produkcija</th>
						<th>Pisanje</th>
					</tr>

				";
				foreach($kandidati_jezici as $jezik){
					$html.= $jezik;
				}
				$html.="

			  </table>
			  <br/>
				</div>
				<div class='clear'></div>
			</section>

			<section>
				<div class='sectionTitle'>
					<h1>Dodatne vještine</h1>
				</div>
				
				<div class='sectionContent'>
				";
				foreach($dodatne_vjestine as $vjestina){
					$html.= $vjestina;
				}
				$html.="
				</div>
				<div class='clear'></div>
			</section>
			
			<section>
				<div class='sectionTitle'>
					<h1>Vozačka dozvola</h1>
				</div>
				
				<div class='sectionContent'>
					$kandidat_vozacka_dozvola
				</div>
				<div class='clear'></div>
			</section>
			
			
		</div>
	</div>
	</body>
";

$cv_filename = $kandidat_id."-".$kandidat_ime."_".$kandidat_prezime.".pdf";

if($cv_ba == 0){
	
	$dompdf->loadHtml($html);
	$dompdf->setPaper('A4', 'portrait');
	$dompdf->render();
	$pdf = $dompdf->output();
	$file_location = "files/cv/ba/".$cv_filename;
	file_put_contents($file_location,$pdf);

	$query_update = $db->prepare("
					UPDATE idk_kandidati
					SET	cv_ba = :cv_ba
					WHERE kandidat_id = :kandidat_id");

	$query_update->execute(array(
				':cv_ba' => 1,
				':kandidat_id' => $kandidat_id
				));
	
	header("Location: kandidati?page=open&id=$kandidat_id&mess=17");
	

}else{
	unlink("files/cv/ba/" . $cv_filename);

	$dompdf->loadHtml($html);
	$dompdf->setPaper('A4', 'portrait');
	$dompdf->render();
	$pdf = $dompdf->output();
	$file_location ="files/cv/ba/".$cv_filename;
	file_put_contents($file_location,$pdf);

	header("Location: kandidati?page=open&id=$kandidat_id&mess=18");

}

break;

case "profil_gen_de":

$kandidat_id = $_POST['kand_id'];

$query = $db->prepare("
				SELECT kandidat_ime, kandidat_prezime, kandidat_datumrodjenja, kandidat_mjestorodjenja, kandidat_adresa, kandidat_grad, kandidat_drzava, kandidat_slika, cv_de, profile_de, kde_kandidat_id, kde_prijava_na, kde_spol, kde_drzavljanstvo, kde_vozacka
				FROM idk_kandidati
				INNER JOIN idk_kandidat_de ON idk_kandidati.kandidat_id = idk_kandidat_de.kde_kandidat_id
				WHERE kandidat_id = :kandidat_id");

$query->execute(array(
			':kandidat_id' => $kandidat_id));

$row = $query->fetch();

$kandidat_ime = $row['kandidat_ime'];
$kandidat_prezime = $row['kandidat_prezime'];
$kandidat_spol = $row['kde_spol'];
$kandidat_drzavljanstvo = $row['kde_drzavljanstvo'];
$kandidat_prijava_na = $row['kde_prijava_na'];
$kandidat_vozacka_dozvola = $row['kde_vozacka'];
$kandidat_datumrodjenja = date('Y.', strtotime($row['kandidat_datumrodjenja']));

$kandidat_adresa = $row['kandidat_adresa'];
$kandidat_grad = $row['kandidat_grad'];

$profile_de = $row['profile_de'];

if($row['kandidat_slika'] == "none"){
	$kandidat_slika = "none.jpg";
}else{
	$kandidat_slika = $row['kandidat_slika'];
}


//KONTAKTI
$select_query = $db->prepare("
					SELECT kki_id, kki_grupa, kki_naziv_de, kki_podatak
					FROM idk_kandidat_kontakt_info
					WHERE kki_kandidat_id = :kki_kandidat_id");

$select_query->execute(array(
				':kki_kandidat_id' => $kandidat_id));

while($select_row = $select_query->fetch()) {

	$kki_id = $select_row['kki_id'];
	$kki_grupa = $select_row['kki_grupa'];
	if($kki_grupa == 1){
		$kki_grupa = "Telefon";
		$ikona = 'fa-mobile';
	}elseif($kki_grupa == 2){
		$kki_grupa = "E-mail";
		$ikona = 'fa-envelope';
	}elseif($kki_grupa == 3){
		$kki_grupa = "Web";
		$ikona = 'fa-globe';
	}elseif($kki_grupa == 4){
		$kki_grupa = "Messaging";
		$ikona = 'fa-skype';
	}else{};
	$kki_naziv = $select_row['kki_naziv_de'];
	$kki_podatak = $select_row['kki_podatak'];

	$kandidat_kontakti[] = "
		<li>$kki_naziv: $kki_podatak</li>
	";
}

//IKSUSTVO
$select_query_iskustvo = $db->prepare("
						SELECT kri_id, kri_darum_od, kri_datum_do, kri_pozicija_de, kri_naziv, kri_grad, kri_opis_de, kri_aktuelno
						FROM idk_kandidat_radno_iskustvo
						WHERE kri_kandidat_id = :kri_kandidat_id
						ORDER BY kri_darum_od DESC
						");

$select_query_iskustvo->execute(array(
				':kri_kandidat_id' => $kandidat_id));

	while($select_row = $select_query_iskustvo->fetch()) {

		$kri_id = $select_row['kri_id'];
		$kri_darum_od = date('m.Y', strtotime($select_row['kri_darum_od']));
		$kri_datum_do = $select_row['kri_datum_do'];
		$kri_pozicija = $select_row['kri_pozicija_de'];
		$kri_naziv = $select_row['kri_naziv'];
		$kri_grad = $select_row['kri_grad'];
		$kri_opis = $select_row['kri_opis_de'];
		$kri_aktuelno = $select_row['kri_aktuelno'];
		
		if($kri_aktuelno != 1 && $kri_datum_do != null){
			$kri_datum_do_f = date('m.Y', strtotime($kri_datum_do));
		}else{
			$kri_datum_do_f = "Aktuell";
		}						

$kandidat_iskustvo[] = "


<article> 
<h2>$kri_pozicija</h2>
<p class='subDetails'>$kri_darum_od - $kri_datum_do_f</p>
<p>$kri_naziv<br/>
		$kri_grad<br/>
		$kri_opis<br/>
		<p>
</article>

";
}

//OBRAZOVANJE
$select_query_obrazovanje = $db->prepare("
					SELECT ke_id, ke_datumod, ke_datumdo, ke_naziv_kvalifikacije_de, ke_naziv, ke_grad, ke_opis_de, ke_aktuelno
					FROM idk_kandidat_edukacija
					WHERE ke_kandidat_id = :ke_kandidat_id");

$select_query_obrazovanje->execute(array(
				':ke_kandidat_id' => $kandidat_id));

while($select_row = $select_query_obrazovanje->fetch()) {

	$ke_id = $select_row['ke_id'];
	$ke_datumod = date('m.Y', strtotime($select_row['ke_datumod']));
	$ke_datumdo = $select_row['ke_datumdo'];
	$ke_naziv_kvalifikacije = $select_row['ke_naziv_kvalifikacije_de'];
	$ke_naziv = $select_row['ke_naziv'];
	$ke_grad = $select_row['ke_grad'];
	$ke_opis = $select_row['ke_opis_de'];
	$ke_aktuelno = $select_row['ke_aktuelno'];
	
	if($ke_aktuelno != 1){
		$ke_datumdo_f = date('m.Y', strtotime($ke_datumdo));
	}else{
		$ke_datumdo_f = "Aktuell";
	}	

	$kandidat_obrazovanje[] = "

	<article> 
		<h2>$kri_pozicija</h2>
		<p class='subDetails'>$ke_datumod - $ke_datumdo_f</p>
		<p>$ke_naziv_kvalifikacije<br/>
			$ke_naziv<br/>
			$ke_opis<br/>
		<p>
	</article>
	";
}


//VJEŠTINE

$select_query_vjestine = $db->prepare("
				SELECT kj_id, kj_naziv_de, kj_slusanje_de, kj_citanje_de, kj_govorna_interakcija_de, kj_govorna_produkcija_de, kj_pisanje_de
				FROM idk_kandidat_jezici
				WHERE kj_kandidatid = :kj_kandidatid");

$select_query_vjestine->execute(array(
			':kj_kandidatid' => $kandidat_id));

while($select_row = $select_query_vjestine->fetch()) {

	$kj_id = $select_row['kj_id'];
	$kj_naziv = $select_row['kj_naziv_de'];
	$kj_slusanje = $select_row['kj_slusanje_de'];
	$kj_citanje = $select_row['kj_citanje_de'];
	$kj_govorna_interakcija = $select_row['kj_govorna_interakcija_de'];
	$kj_govorna_produkcija = $select_row['kj_govorna_produkcija_de'];
	$kj_pisanje = $select_row['kj_pisanje_de'];
	
	$kandidati_jezici[] = "
		<tr>
			<td> $kj_naziv</td>
			<td> $kj_slusanje </td>
			<td> $kj_citanje </td>
			<td> $kj_govorna_interakcija </td>
			<td> $kj_govorna_produkcija </td>
			<td> $kj_pisanje </td>
		</tr>
	";
}


$select_query_vjestine2 = $db->prepare("
					SELECT kv_id, kv_naziv_de, kv_grupa, kv_opis_de
					FROM idk_kandidat_vjestine
					WHERE kv_kandidat_id = :kv_kandidat_id");

$select_query_vjestine2->execute(array(
				':kv_kandidat_id' => $kandidat_id));

while($select_row = $select_query_vjestine2->fetch()) {

	$kv_id = $select_row['kv_id'];
	$kv_naziv = $select_row['kv_naziv_de'];
	$kv_grupa = $select_row['kv_grupa'];
	if($kv_grupa == 1){
		$kv_grupa = "Grundlegende Fähigkeiten";
	}elseif($kv_grupa == 2){
		$kv_grupa = "Digitale Fähigkeiten";
	}elseif($kv_grupa == 3){
		$kv_grupa = "Zusätzliche Informationen:";
	}else{};
	$kv_opis = $select_row['kv_opis_de'];

	$dodatne_vjestine[] = " 
	<article> 
		<h2>$kv_grupa</h2>
		<p>$kv_naziv<br/>
			$kv_opis<br/>
		<p>
	</article>
	";
}


$dompdf = new Dompdf();
$html = "
	<head>
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
			<style>
		
			@page { margin-top: 40px!important; margin-bottom: 40px!important; }
			
			html,body,div,span,object,iframe,h1,h2,h3,h4,h5,h6,p,blockquote,pre,abbr,address,cite,code,del,dfn,em,img,ins,kbd,q,samp,small,strong,sub,sup,var,b,i,dl,dt,dd,ol,ul,li,fieldset,form,label,legend,table,caption,tbody,tfoot,thead,tr,th,td,article,aside,canvas,details,figcaption,figure,footer,header,hgroup,menu,nav,section,summary,time,mark,audio,video {
				border:0;
				font:inherit;
				margin:0;
				padding:0;
				vertical-align:baseline;
				font-family: 'DejaVu Sans', sans-serif !important;
				}
				
				article,aside,details,figcaption,figure,footer,header,hgroup,menu,nav,section {
					display:block;
				}
				
				.clear {clear: both;}
				
				p {
					font-size: 14px;
					line-height: 1.4em;
					margin-bottom: 20px;
					color: #444;
				}
				
				#cv {
					background: #fff;
				}
				
				.mainDetails {
					padding: 40px 35px;
					border-bottom: 2px solid #3d7b85;
					background: #ededed;					
					margin-top: -40px;
				}
				
				#name h1 {
					font-size: 2em;
					font-weight: 700;
					margin-bottom: -6px;
				}
				
				#name h2 {
					font-size: 1.3em;
					margin-top: 5px;
					margin-left: 2px;
				}

				#name h3 {
					font-size: 1em;
					margin-top: 5px;
					margin-left: 2px;
				}
				
				#mainArea {
					padding: 0 40px;
				}
				
				#headshot {
					width: 150px;
					float: left;
					margin-right: 30px;
				}
				
				#headshot img {
					width: 100%;
					height: auto;
				}
				
				#name {
					float: left;
					margin-left: 20px;
				}
				
				#contactDetails {
					float: right;
				}
				
				#contactDetails ul {
					list-style-type: none;
					font-size: 0.9em;
					margin-top: 2px;
				}
				
				#contactDetails ul li {
					margin-bottom: 3px;
					color: #444;
				}
				
				#contactDetails ul li a, a[href^=tel] {
					color: #444; 
					text-decoration: none;
					-webkit-transition: all .3s ease-in;
					-moz-transition: all .3s ease-in;
					-o-transition: all .3s ease-in;
					-ms-transition: all .3s ease-in;
					transition: all .3s ease-in;
				}
				
				#contactDetails ul li a:hover { 
					color: #cf8a05;
				}
				
				
				section {
					border-top: 1px solid #dedede;
					padding: 20px 0 0;
				}
				
				section:first-child {
					border-top: 0;
				}
				
				section:last-child {
					padding: 20px 0 10px;
				}
				
				.sectionTitle {
					float: left;
					width: 25%;
				}
				
				.sectionContent {
					float: right;
					width: 72.5%;
				}

				.sectionContent ul{
					list-style-type: none;
					color: #333;
					margin-bottom: 20px;
				}

				.sectionContent li{
					margin: 5px 0;
					font-size: 14px;
				}
				
				.sectionTitle h1 {
					
					font-style: italic;
					font-size: 20px;
					color: #3d7b85;
				}
				
				.sectionTitle.headerTitle h1 {
					
					font-style: italic;
					font-size: 16px;
					color: #3d7b85;
				}
				
				.sectionContent h2 {					
					font-size: 1.5em;
					margin-bottom: -2px;
				}
				
				.subDetails {
					font-size: 0.8em;
					font-style: italic;
					margin-bottom: 3px;
				}
				
				.keySkills {
					list-style-type: none;
					-moz-column-count:3;
					-webkit-column-count:3;
					column-count:3;
					margin-bottom: 20px;
					font-size: 1em;
					color: #444;
				}
				
				.keySkills ul li {
					margin-bottom: 3px;
				}
				
				@media all and (min-width: 602px) and (max-width: 800px) {
					#headshot {
						display: none;
					}
					
					.keySkills {
					-moz-column-count:2;
					-webkit-column-count:2;
					column-count:2;
					}
				}
				
				@media all and (max-width: 601px) {
					#cv {
						width: 95%;
						margin: 10px auto;
						min-width: 280px;
					}
					
					#headshot {
						display: none;
					}
					
					#name, #contactDetails {
						float: none;
						width: 100%;
						text-align: center;
					}
					
					.sectionTitle, .sectionContent {
						float: none;
						width: 100%;
					}
					
					.sectionTitle {
						margin-left: -2px;
						font-size: 1.25em;
					}
					
					.keySkills {
						-moz-column-count:2;
						-webkit-column-count:2;
						column-count:2;
					}
				}
				
				@media all and (max-width: 480px) {
					.mainDetails {
						padding: 15px 15px;
					}
					
					section {
						padding: 15px 0 0;
					}
					
					#mainArea {
						padding: 0 25px;
					}
				
					
					.keySkills {
					-moz-column-count:1;
					-webkit-column-count:1;
					column-count:1;
					}
					
					#name h1 {
						line-height: .8em;
						margin-bottom: 4px;
					}
				}
				
				@media print {
					#cv {
						width: 100%;
					}
				}
		</style>
	<title>Profil - $kandidat_ime</title>
	</head>


	<body id='top'>
	<div id='cv' class='instaFade'>
		<div class='mainDetails'>
			<div id='headshot' class='quickFade'>
			<img src='".getsiteurl()."images/Jobstep-logo_news.png'/>
			</div>
			
			<div id='name'>
				<h1 class='quickFade delayTwo'>$kandidat_ime</h1>
				
			</div>
			
			<div id='contactDetails' class='quickFade delayFour'>
				<img src='".getsiteurl()."files/kandidati/$kandidat_slika' width=auto; height=100;/>
			</div>
			<div class='clear'></div>
		
			<div class='sectionTitle headerTitle'>
				<h1>Angestrebte Stelle:</h1>
			</div>
			
			<div class='sectionContent'>
				<h3 class='quickFade delayThree'>$kandidat_prijava_na</h3>
			</div>			
			<div class='clear'></div>
		</div>
		
		<div id='mainArea'>
			<section>
				<article>
					<div class='sectionTitle'>
						<h1>Persönliche informationen</h1>
					</div>
					
					<div class='sectionContent'>
						<ul>
							<li>ID: $kandidat_id</li>
							<li>Adresse: $kandidat_grad</li>
							<li>Geschlecht: $kandidat_spol</li>
							<li>Geburtsjahr: $kandidat_datumrodjenja</li>
							<li>Staatsangehörigkeit: $kandidat_drzavljanstvo</li>
						</ul>
					</div>
				</article>
				<div class='clear'></div>
			</section>		
			
			<section>
				<div class='sectionTitle'>
					<h1>Berufserfahrung</h1>
				</div>
				
				<div class='sectionContent'>
				";
				foreach($kandidat_iskustvo as $iskustvo){
					$html.= $iskustvo;
				}
				$html.="
				</div>
				<div class='clear'></div>
			</section>

			<section>
				<div class='sectionTitle'>
					<h1>Schul-und berufsbildung</h1>
				</div>
				
				<div class='sectionContent'>
				";
				foreach($kandidat_obrazovanje as $obrazovanje){
					$html.= $obrazovanje;
				}
				$html.="
				</div>
				<div class='clear'></div>
			</section>
			<style>
				table, td, th {    
					border: 1px solid #ddd;
				}

				th, td {
					padding: 15px;
				}
			</style>
			<section>
				<div class='sectionTitle'>
					<h1>Persönliche fähigkeiten</h1>
				</div>
				
				<div class='sectionContent'>
				<table style='font-size: 12px !important;'>
					<tr style='background: #ddd;'>
						<th>Sprache</th>
						<th>Hören</th>
						<th>Lesen</th>
						<th>Sprach-<br/>interaktion</th>
						<th>Sprach-<br/>produktion</th>
						<th>Schreiben</th>
					</tr>

				";
				foreach($kandidati_jezici as $jezik){
					$html.= $jezik;
				}
				$html.="

			  </table>
			  <br/>
				</div>
				<div class='clear'></div>
			</section>

			<section>
				<div class='sectionTitle'>
					<h1>Zusätzliche fähigkeiten</h1>
				</div>
				
				<div class='sectionContent'>
				";
				foreach($dodatne_vjestine as $vjestina){
					$html.= $vjestina;
				}
				$html.="
				</div>
				<div class='clear'></div>
			</section>
			
			<section>
				<div class='sectionTitle'>
					<h1>Führerschein B</h1>
				</div>
				
				<div class='sectionContent'>
					$kandidat_vozacka_dozvola
				</div>
				<div class='clear'></div>
			</section>
			
			
		</div>
	</div>
	</body>
";

$profil_filename = $kandidat_id."-".$kandidat_ime.".pdf";

if($profil_de == 0){
	
	$dompdf->loadHtml($html);
	$dompdf->setPaper('A4', 'portrait');
	$dompdf->render();
	$pdf = $dompdf->output();
	$file_location ="files/profile/de/".$profil_filename;
	file_put_contents($file_location,$pdf);

	$query_update = $db->prepare("
					UPDATE idk_kandidati
					SET	profile_de = :profile_de
					WHERE kandidat_id = :kandidat_id");

	$query_update->execute(array(
				':profile_de' => 1,
				':kandidat_id' => $kandidat_id
				));
	
	header("Location: kandidati?page=open&id=$kandidat_id&mess=22");
	

}else{
	unlink("files/profil/de/" . $profil_filename);

	$dompdf->loadHtml($html);
	$dompdf->setPaper('A4', 'portrait');
	$dompdf->render();
	$pdf = $dompdf->output();
	$file_location ="files/profile/de/".$profil_filename;
	file_put_contents($file_location,$pdf);

	header("Location: kandidati?page=open&id=$kandidat_id&mess=23");

}

break;

case "profil_gen_ba":

$kandidat_id = $_POST['kand_id'];

$query = $db->prepare("
				SELECT kandidat_ime, kandidat_prezime, kandidat_spol, kandidat_djevojackoprezime, kandidat_jmbg, kandidat_datumrodjenja, kandidat_mjestorodjenja, kandidat_drzavarodjenja, kandidat_drzavljanstvo, kandidat_bracnostanje, kandidat_adresa, kandidat_grad, kandidat_pbroj, kandidat_drzava, kandidat_email, kandidat_slika, kandidat_datetime,kandidat_check, kandidat_vozacka_dozvola, kandidat_prijava_na, cv_ba, profile_ba
				FROM idk_kandidati
				WHERE kandidat_id = :kandidat_id");

$query->execute(array(
			':kandidat_id' => $kandidat_id));

$row = $query->fetch();

$kandidat_ime = $row['kandidat_ime'];
$kandidat_prezime = $row['kandidat_prezime'];
$kandidat_spol = $row['kandidat_spol'];
$kandidat_check = $row['kandidat_check'];
$kandidat_djevojackoprezime = $row['kandidat_djevojackoprezime'];
$kandidat_jmbg = $row['kandidat_jmbg'];
$kandidat_mjestorodjenja = $row['kandidat_mjestorodjenja'];
$kandidat_drzavarodjenja = $row['kandidat_drzavarodjenja'];
$kandidat_drzavljanstvo = $row['kandidat_drzavljanstvo'];
$kandidat_bracnostanje = $row['kandidat_bracnostanje'];
$kandidat_drzava = $row['kandidat_drzava'];
$kandidat_email = $row['kandidat_email'];
$kandidat_drzava = $row['kandidat_drzava'];
$kandidat_datetime = $row['kandidat_datetime'];
$kandidat_vozacka_dozvola = $row['kandidat_vozacka_dozvola'];
$kandidat_prijava_na = $row['kandidat_prijava_na'];
$kandidat_datumrodjenja = date('d.m.Y.', strtotime($row['kandidat_datumrodjenja']));

$kandidat_adresa = $row['kandidat_adresa'];
$kandidat_grad = $row['kandidat_grad'];
$kandidat_pbroj = $row['kandidat_pbroj'];

$profile_ba = $row['profile_ba'];

if($row['kandidat_slika'] == "none"){
	$kandidat_slika = "none.jpg";
}else{
	$kandidat_slika = $row['kandidat_slika'];
}


//KONTAKTI
$select_query = $db->prepare("
					SELECT kki_id, kki_grupa, kki_naziv, kki_podatak
					FROM idk_kandidat_kontakt_info
					WHERE kki_kandidat_id = :kki_kandidat_id");

$select_query->execute(array(
				':kki_kandidat_id' => $kandidat_id));

while($select_row = $select_query->fetch()) {

	$kki_id = $select_row['kki_id'];
	$kki_grupa = $select_row['kki_grupa'];
	if($kki_grupa == 1){
		$kki_grupa = "Telefon";
		$ikona = 'fa-mobile';
	}elseif($kki_grupa == 2){
		$kki_grupa = "E-mail";
		$ikona = 'fa-envelope';
	}elseif($kki_grupa == 3){
		$kki_grupa = "Web";
		$ikona = 'fa-globe';
	}elseif($kki_grupa == 4){
		$kki_grupa = "Messangeri";
		$ikona = 'fa-skype';
	}else{};
	$kki_naziv = $select_row['kki_naziv'];
	$kki_podatak = $select_row['kki_podatak'];

	$kandidat_kontakti[] = "
		<li>$kki_naziv: $kki_podatak</li>
	";
}

//IKSUSTVO
$select_query_iskustvo = $db->prepare("
						SELECT kri_id, kri_darum_od, kri_datum_do, kri_pozicija, kri_naziv, kri_grad, kri_opis, kri_aktuelno
						FROM idk_kandidat_radno_iskustvo
						WHERE kri_kandidat_id = :kri_kandidat_id");

$select_query_iskustvo->execute(array(
				':kri_kandidat_id' => $kandidat_id));

	while($select_row = $select_query_iskustvo->fetch()) {

		$kri_id = $select_row['kri_id'];
		$kri_darum_od = date('m.Y', strtotime($select_row['kri_darum_od']));
		$kri_datum_do = $select_row['kri_datum_do'];
		$kri_pozicija = $select_row['kri_pozicija'];
		$kri_naziv = $select_row['kri_naziv'];
		$kri_grad = $select_row['kri_grad'];
		$kri_opis = $select_row['kri_opis'];
		$kri_aktuelno = $select_row['kri_aktuelno'];
		
		if($kri_aktuelno != 1){
			$kri_datum_do_f = date('m.Y', strtotime($kri_datum_do));
		}else{
			$kri_datum_do_f = "Aktuelno";
		}						

$kandidat_iskustvo[] = "


<article> 
<h2>$kri_pozicija</h2>
<p class='subDetails'>$kri_darum_od - $kri_datum_do_f</p>
<p>$kri_naziv<br/>
		$kri_grad<br/>
		$kri_opis<br/>
		<p>
</article>

";
}

//OBRAZOVANJE
$select_query_obrazovanje = $db->prepare("
					SELECT ke_id, ke_datumod, ke_datumdo, ke_naziv_kvalifikacije, ke_naziv, ke_grad, ke_opis, ke_aktuelno
					FROM idk_kandidat_edukacija
					WHERE ke_kandidat_id = :ke_kandidat_id");

$select_query_obrazovanje->execute(array(
				':ke_kandidat_id' => $kandidat_id));

while($select_row = $select_query_obrazovanje->fetch()) {

	$ke_id = $select_row['ke_id'];
	$ke_datumod = date('m.Y', strtotime($select_row['ke_datumod']));
	$ke_datumdo = $select_row['ke_datumdo'];
	$ke_naziv_kvalifikacije = $select_row['ke_naziv_kvalifikacije'];
	$ke_naziv = $select_row['ke_naziv'];
	$ke_grad = $select_row['ke_grad'];
	$ke_opis = $select_row['ke_opis'];
	$ke_aktuelno = $select_row['ke_aktuelno'];
	
	if($ke_aktuelno != 1){
		$ke_datumdo_f = date('m.Y', strtotime($ke_datumdo));
	}else{
		$ke_datumdo_f = "Aktuelno";
	}	

	$kandidat_obrazovanje[] = "

	<article> 
		<h2>$kri_pozicija</h2>
		<p class='subDetails'>$ke_datumod - $ke_datumdo_f</p>
		<p>$ke_naziv_kvalifikacije<br/>
			$ke_naziv<br/>
			$ke_opis<br/>
		<p>
	</article>
	";
}


//VJEŠTINE

$select_query_vjestine = $db->prepare("
				SELECT kj_id, kj_naziv, kj_slusanje, kj_citanje, kj_govorna_interakcija, kj_govorna_produkcija, kj_pisanje
				FROM idk_kandidat_jezici
				WHERE kj_kandidatid = :kj_kandidatid");

$select_query_vjestine->execute(array(
			':kj_kandidatid' => $kandidat_id));

while($select_row = $select_query_vjestine->fetch()) {

	$kj_id = $select_row['kj_id'];
	$kj_naziv = $select_row['kj_naziv'];
	$kj_slusanje = $select_row['kj_slusanje'];
	$kj_citanje = $select_row['kj_citanje'];
	$kj_govorna_interakcija = $select_row['kj_govorna_interakcija'];
	$kj_govorna_produkcija = $select_row['kj_govorna_produkcija'];
	$kj_pisanje = $select_row['kj_pisanje'];
	
	$kandidati_jezici[] = "
		<tr>
			<td> $kj_naziv</td>
			<td> $kj_slusanje </td>
			<td> $kj_citanje </td>
			<td> $kj_govorna_interakcija </td>
			<td> $kj_govorna_produkcija </td>
			<td> $kj_pisanje </td>
		</tr>
	";
}


$select_query_vjestine2 = $db->prepare("
					SELECT kv_id, kv_naziv, kv_grupa, kv_opis
					FROM idk_kandidat_vjestine
					WHERE kv_kandidat_id = :kv_kandidat_id");

$select_query_vjestine2->execute(array(
				':kv_kandidat_id' => $kandidat_id));

while($select_row = $select_query_vjestine2->fetch()) {

	$kv_id = $select_row['kv_id'];
	$kv_naziv = $select_row['kv_naziv'];
	$kv_grupa = $select_row['kv_grupa'];
	if($kv_grupa == 1){
		$kv_grupa = "Osnovne vještine";
	}elseif($kv_grupa == 2){
		$kv_grupa = "Digitalne kompetencije";
	}elseif($kv_grupa == 3){
		$kv_grupa = "Dodatne informacije";
	}else{};
	$kv_opis = $select_row['kv_opis'];

	$dodatne_vjestine[] = " 
	<article> 
		<h2>$kv_grupa</h2>
		<p>$kv_naziv<br/>
			$kv_opis<br/>
		<p>
	</article>
	";
}


$dompdf = new Dompdf();
$html = "
	<head>
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
		<style>
	
			@page { margin-top: 40px!important; margin-bottom: 40px!important; }
			
			html,body,div,span,object,iframe,h1,h2,h3,h4,h5,h6,p,blockquote,pre,abbr,address,cite,code,del,dfn,em,img,ins,kbd,q,samp,small,strong,sub,sup,var,b,i,dl,dt,dd,ol,ul,li,fieldset,form,label,legend,table,caption,tbody,tfoot,thead,tr,th,td,article,aside,canvas,details,figcaption,figure,footer,header,hgroup,menu,nav,section,summary,time,mark,audio,video {
				border:0;
				font:inherit;
				margin:0;
				padding:0;
				vertical-align:baseline;
				font-family: 'DejaVu Sans', sans-serif !important;
				}
				
				article,aside,details,figcaption,figure,footer,header,hgroup,menu,nav,section {
					display:block;
				}
				
				.clear {clear: both;}
				
				p {
					font-size: 14px;
					line-height: 1.4em;
					margin-bottom: 20px;
					color: #444;
				}
				
				#cv {
					background: #fff;
				}
				
				.mainDetails {
					padding: 40px 35px;
					border-bottom: 2px solid #3d7b85;
					background: #ededed;					
					margin-top: -40px;
				}
				
				#name h1 {
					font-size: 2em;
					font-weight: 700;
					margin-bottom: -6px;
				}
				
				#name h2 {
					font-size: 1.3em;
					margin-top: 5px;
					margin-left: 2px;
				}

				#name h3 {
					font-size: 1em;
					margin-top: 5px;
					margin-left: 2px;
				}
				
				#mainArea {
					padding: 0 40px;
				}
				
				#headshot {
					width: 150px;
					float: left;
					margin-right: 30px;
				}
				
				#headshot img {
					width: 100%;
					height: auto;
				}
				
				#name {
					float: left;
					margin-left: 20px;
				}
				
				#contactDetails {
					float: right;
				}
				
				#contactDetails ul {
					list-style-type: none;
					font-size: 0.9em;
					margin-top: 2px;
				}
				
				#contactDetails ul li {
					margin-bottom: 3px;
					color: #444;
				}
				
				#contactDetails ul li a, a[href^=tel] {
					color: #444; 
					text-decoration: none;
					-webkit-transition: all .3s ease-in;
					-moz-transition: all .3s ease-in;
					-o-transition: all .3s ease-in;
					-ms-transition: all .3s ease-in;
					transition: all .3s ease-in;
				}
				
				#contactDetails ul li a:hover { 
					color: #cf8a05;
				}
				
				
				section {
					border-top: 1px solid #dedede;
					padding: 20px 0 0;
				}
				
				section:first-child {
					border-top: 0;
				}
				
				section:last-child {
					padding: 20px 0 10px;
				}
				
				.sectionTitle {
					float: left;
					width: 25%;
				}
				
				.sectionContent {
					float: right;
					width: 72.5%;
				}

				.sectionContent ul{
					list-style-type: none;
					color: #333;
					margin-bottom: 20px;
				}

				.sectionContent li{
					margin: 5px 0;
					font-size: 14px;
				}
				
				.sectionTitle h1 {
					
					font-style: italic;
					font-size: 20px;
					color: #3d7b85;
				}
				
				.sectionTitle.headerTitle h1 {
					
					font-style: italic;
					font-size: 16px;
					color: #3d7b85;
				}
				
				.sectionContent h2 {					
					font-size: 1.5em;
					margin-bottom: -2px;
				}
				
				.subDetails {
					font-size: 0.8em;
					font-style: italic;
					margin-bottom: 3px;
				}
				
				.keySkills {
					list-style-type: none;
					-moz-column-count:3;
					-webkit-column-count:3;
					column-count:3;
					margin-bottom: 20px;
					font-size: 1em;
					color: #444;
				}
				
				.keySkills ul li {
					margin-bottom: 3px;
				}
				
				@media all and (min-width: 602px) and (max-width: 800px) {
					#headshot {
						display: none;
					}
					
					.keySkills {
					-moz-column-count:2;
					-webkit-column-count:2;
					column-count:2;
					}
				}
				
				@media all and (max-width: 601px) {
					#cv {
						width: 95%;
						margin: 10px auto;
						min-width: 280px;
					}
					
					#headshot {
						display: none;
					}
					
					#name, #contactDetails {
						float: none;
						width: 100%;
						text-align: center;
					}
					
					.sectionTitle, .sectionContent {
						float: none;
						width: 100%;
					}
					
					.sectionTitle {
						margin-left: -2px;
						font-size: 1.25em;
					}
					
					.keySkills {
						-moz-column-count:2;
						-webkit-column-count:2;
						column-count:2;
					}
				}
				
				@media all and (max-width: 480px) {
					.mainDetails {
						padding: 15px 15px;
					}
					
					section {
						padding: 15px 0 0;
					}
					
					#mainArea {
						padding: 0 25px;
					}
				
					
					.keySkills {
					-moz-column-count:1;
					-webkit-column-count:1;
					column-count:1;
					}
					
					#name h1 {
						line-height: .8em;
						margin-bottom: 4px;
					}
				}
				
				@media print {
					#cv {
						width: 100%;
					}
				}
		</style>
	<title>Profil - $kandidat_ime</title>
	</head>


	<body id='top'>
	<div id='cv' class='instaFade'>
		<div class='mainDetails'>
			<div id='headshot' class='quickFade'>
			<img src='".getsiteurl()."images/Jobstep-logo_news.png'/>
			</div>
			
			<div id='name'>
				<h1 class='quickFade delayTwo'>$kandidat_ime</h1>
				<!--<h2 class='quickFade delayThree'>Prijava za radno mjesto:</h2>
				<h3 class='quickFade delayThree'>$kandidat_prijava_na</h3>-->
			</div>
			
			<div id='contactDetails' class='quickFade delayFour'>
				<img src='".getsiteurl()."files/kandidati/$kandidat_slika' width=auto; height=100;/>
			</div>
			<div class='clear'></div>
			
			<div class='sectionTitle headerTitle'>
				<h1>Prijava za radno mjesto:</h1>
			</div>
			
			<div class='sectionContent'>
				<h3 class='quickFade delayThree'>$kandidat_prijava_na</h3>
			</div>			
			<div class='clear'></div>				
		</div>
		
		<div id='mainArea'>
			<section>
				<article>
					<div class='sectionTitle'>
						<h1>Osobne informacije</h1>
					</div>
					
					<div class='sectionContent'>
						<ul>
							<li>ID: $kandidat_id</li>
							<li>Adresa: $kandidat_grad</li>
							<li>Spol: $kandidat_spol</li>
							<li>Datum rođenja: $kandidat_datumrodjenja</li>
							<li>Državljanstvo: $kandidat_drzavljanstvo</li>
						</ul>
					</div>
				</article>
				<div class='clear'></div>
			</section>
			
			<section>
				<div class='sectionTitle'>
					<h1>Radno iskustvo</h1>
				</div>
				
				<div class='sectionContent'>
				";
				foreach($kandidat_iskustvo as $iskustvo){
					$html.= $iskustvo;
				}
				$html.="
				</div>
				<div class='clear'></div>
			</section>

			<section>
				<div class='sectionTitle'>
					<h1>Obrazovanje i osposobljavanje</h1>
				</div>
				
				<div class='sectionContent'>
				";
				foreach($kandidat_obrazovanje as $obrazovanje){
					$html.= $obrazovanje;
				}
				$html.="
				</div>
				<div class='clear'></div>
			</section>
			<style>
				table, td, th {    
					border: 1px solid #ddd;
				}

				th, td {
					padding: 15px;
				}
			</style>
			<section>
				<div class='sectionTitle'>
					<h1>Osobne vještine</h1>
				</div>
				
				<div class='sectionContent'>
				<table style='font-size: 12px !important;'>
					<tr style='background: #ddd;'>
						<th>Jezik</th>
						<th>Slušanje</th>
						<th>Čitanje</th>
						<th>Govorna interakcija</th>
						<th>Govorna produkcija</th>
						<th>Pisanje</th>
					</tr>

				";
				foreach($kandidati_jezici as $jezik){
					$html.= $jezik;
				}
				$html.="

			  </table>
			  <br/>
				</div>
				<div class='clear'></div>
			</section>

			<section>
				<div class='sectionTitle'>
					<h1>Dodatne vještine</h1>
				</div>
				
				<div class='sectionContent'>
				";
				foreach($dodatne_vjestine as $vjestina){
					$html.= $vjestina;
				}
				$html.="
				</div>
				<div class='clear'></div>
			</section>
			
			<section>
				<div class='sectionTitle'>
					<h1>Vozačka dozvola</h1>
				</div>
				
				<div class='sectionContent'>
					$kandidat_vozacka_dozvola
				</div>
				<div class='clear'></div>
			</section>
			
			
		</div>
	</div>
	</body>
";

$profile_filename = $kandidat_id."-".$kandidat_ime.".pdf";

if($profile_ba == 0){
	
	$dompdf->loadHtml($html);
	$dompdf->setPaper('A4', 'portrait');
	$dompdf->render();
	$pdf = $dompdf->output();
	$file_location ="files/profile/ba/".$profile_filename;
	file_put_contents($file_location,$pdf);

	$query_update = $db->prepare("
					UPDATE idk_kandidati
					SET	profile_ba = :profile_ba
					WHERE kandidat_id = :kandidat_id");

	$query_update->execute(array(
				':profile_ba' => 1,
				':kandidat_id' => $kandidat_id
				));
	
	header("Location: kandidati?page=open&id=$kandidat_id&mess=20");
	

}else{
	unlink("files/profile/ba/" . $profile_filename);

	$dompdf->loadHtml($html);
	$dompdf->setPaper('A4', 'portrait');
	$dompdf->render();
	$pdf = $dompdf->output();
	$file_location ="files/profile/ba/".$profile_filename;
	file_put_contents($file_location,$pdf);

	header("Location: kandidati?page=open&id=$kandidat_id&mess=21");

}

break;


case "edit_kandidat":

	$kandidat_id = $_POST['kandidat_id'];
	$kandidat_ime = trim($_POST['kandidat_ime']);
	$kandidat_prezime = trim($_POST['kandidat_prezime']);
	$kf_id = $_POST['kf_id'];
	$kf_id_pocetak_rada = $_POST['kf_id_pocetak_rada'];
	$nalog_id = $_POST['nalog_id'];
	$projekt_id = $_POST['projekt_id'];
	$zamjena_check = $_POST['zamjena_check'];
	$kandidat_datum_ugovora_stari = $_POST['kandidat_datum_ugovora_stari'];
	$kandidat_datum_pocetakrada_stari = $_POST['kandidat_datum_pocetakrada_stari'];
	$kandidat_datumrodjenja = date("Y-m-d", strtotime($_POST['kandidat_datumrodjenja']));
	$kandidat_broj_pasosa = $_POST['kandidat_broj_pasosa'];
	
	//napravi username po imenu, prezimenu i godini
	$kandidat_datumrodjenja_username = date("dmY", strtotime($_POST['kandidat_datumrodjenja']));
	$ime_korime = strtolower($kandidat_ime);
	$prezime_korime = strtolower($kandidat_prezime);
	$imeprezime = $ime_korime.''.$prezime_korime;
	$search = array("ć", "č", "ž", "š", "đ");
	$replacement = array("c", "c", "z", "s", "dj");
	$imeprezime_korime = str_replace($search, $replacement, $imeprezime);
	$kandidat_korisnickoime_uf = $imeprezime_korime.''.$kandidat_datumrodjenja_username;
	$kandidat_korisnickoime = str_replace(' ', '', strtolower($kandidat_korisnickoime_uf));
	
	if(!empty($_POST['kandidat_spol'])){ $kandidat_spol = $_POST['kandidat_spol']; }else{ $kandidat_spol = null; }
	if(!empty($_POST['kandidat_djevojackoprezime'])){ $kandidat_djevojackoprezime = $_POST['kandidat_djevojackoprezime']; }else{ $kandidat_djevojackoprezime = null; }
	
	if(!empty($_POST['kandidat_jmbg'])){ $kandidat_jmbg = $_POST['kandidat_jmbg']; }else{ $kandidat_jmbg = 0; }
	if(!empty($_POST['kandidat_brojlk'])){ $kandidat_brojlk = $_POST['kandidat_brojlk']; }else{ $kandidat_jmbg = ""; }
	if(!empty($_POST['kandidat_drzavljanstvo'])){ $kandidat_drzavljanstvo = $_POST['kandidat_drzavljanstvo']; }else{ $kandidat_drzavljanstvo = null; }
	
	$kandidat_mjestorodjenja = $_POST['kandidat_mjestorodjenja'];
	$kandidat_drzavarodjenja = $_POST['kandidat_drzavarodjenja'];
	$kandidat_drzavljanstvo_vrsta = $_POST['kandidat_drzavljanstvo_vrsta'];
	
	if(!empty($_POST['kandidat_adresa'])){ $kandidat_adresa = $_POST['kandidat_adresa']; }else{ $kandidat_adresa = null; }
	if(!empty($_POST['kandidat_grad'])){ $kandidat_grad = $_POST['kandidat_grad']; }else{ $kandidat_grad = null; }
	if(!empty($_POST['kandidat_pbroj'])){ $kandidat_pbroj = $_POST['kandidat_pbroj']; }else{ $kandidat_pbroj = null; }
	if(!empty($_POST['kandidat_drzava'])){ $kandidat_drzava = $_POST['kandidat_drzava']; }else{ $kandidat_drzava = null; }
	
	$kandidat_email = $_POST['kandidat_email'];
	$kandidat_prijava_na = $_POST['kandidat_prijava_na'];
	//$kandidat_vozacka_dozvola = $_POST['kandidat_vozacka_dozvola'];
	$kandidat_kategorija_vozacke = $_POST['kandidat_kategorija_vozacke'];
	
	$kandidat_kategorija_vozackeImp = implode(",", $kandidat_kategorija_vozacke);
	if($kandidat_kategorija_vozackeImp == "Nema"){
		$kandidat_vozacka_dozvola = "Ne";
		$kandidat_vozacka_kategorija = null;
	}else{
		$kandidat_vozacka_dozvola = "Da";
		$kandidat_vozacka_kategorija = $kandidat_kategorija_vozackeImp;
	}
	
	//$kandidat_datum_aplikacije = $_POST['aplikacija_termin'];
	//$kandidat_datum_termina = $_POST['datum_termina'];
	
	$log_automatskog_prebacivanja = "";

	//VIZA I TERMIN
	$kandidat_viza_da_ne = $_POST['kandidat_viza'];
	if(!empty($_POST['kandidat_termin'])){ $kandidat_termin_da_ne = $_POST['kandidat_termin']; }else{ $kandidat_termin_da_ne = 0; }
	if(!empty($_POST['kandidat_apliciranje'])){ $kandidat_apliciranje_da_ne = $_POST['kandidat_apliciranje']; }else{ $kandidat_apliciranje_da_ne = 0; }
	
	if(!empty($_POST['kandidat_viza_vrijedi_do'])){
		$kandidat_viza_vrijedi_do = $_POST['kandidat_viza_vrijedi_do'];
		$kandidat_viza_vrijedi_do = date("Y-m-d", strtotime($kandidat_viza_vrijedi_do));
	}else
		$kandidat_viza_vrijedi_do = null;
	
	if($kandidat_termin_da_ne == 1){
		$kandidat_datum_termina = $_POST['kandidat_termin_date'];
		$kandidat_datum_termina = date("Y-m-d", strtotime($kandidat_datum_termina));
		$kandidat_procjenatermina = 0;
	}else
		$kandidat_datum_termina = null;
	
	if($kandidat_apliciranje_da_ne == 1){
		$kandidat_datum_aplikacije =  $_POST['kandidat_termin_date_app'];
		$kandidat_datum_aplikacije = date("Y-m-d", strtotime($kandidat_datum_aplikacije));
	}else
		$kandidat_datum_aplikacije = null;
	
	if($kandidat_viza_da_ne == 1){
		$kandidat_viza = 1;
	}else{
		$kandidat_viza = 0;
		if($kandidat_termin_da_ne == 1){
			$kandidat_procjenatermina = 0;
		}else{
			if($kandidat_apliciranje_da_ne == 1){
				$kandidat_procjenatermina = 1;
				//$kandidat_datum_termina = date('Y-m-d', strtotime("+20 months", strtotime($kandidat_datum_aplikacije)));
				$kandidat_datum_termina = null;
			}else{
				$kandidat_procjenatermina = 0;
			}
		}
	}
	
	/*
	if(!empty($_POST['datum_termina'])){ $kandidat_datum_termina = $_POST['datum_termina']; }else{ $kandidat_datum_termina = null; }
	
	if($_POST['kandidat_termin_viza'] == "Da"){
		$kandidat_datum_aplikacije = date("Y-m-d", strtotime($_POST['aplikacija_termin']));
		if($_POST['kandidat_termin2'] == "Da"){
			$kandidat_procjenatermina = 0;
			$kandidat_datum_termina = date("Y-m-d", strtotime($_POST['datum_termina']));
		}else{
			$kandidat_procjenatermina = 1;
			$kandidat_datum_termina = date("Y-m-d", strtotime("+20 months", strtotime($kandidat_datum_aplikacije)));
		}
		
	}else{
		$kandidat_datum_aplikacije = NULL;
		$kandidat_procjenatermina = 0;
	}
	*/
	// DATUM UGOVORA
	if($_POST['kandidat_datum_ugovora'] == ""){
		$kandidat_datum_ugovora = NULL;
		$kandidat_datum_ugovora_mjesec = NULL;
	}else{
		$kandidat_datum_ugovora = date("Y-m-d", strtotime($_POST['kandidat_datum_ugovora']));
		
		$kandidat_datum_ugovora_dan = date("d", strtotime($_POST['kandidat_datum_ugovora']));
		
		if($kandidat_datum_ugovora_dan < 15){
			$kandidat_datum_ugovora_mjesec = date("Y-m-d", strtotime($_POST['kandidat_datum_ugovora']));
		}else{
			$kandidat_datum_ugovora_mjesec = date('Y-m-d', strtotime("+1 month", strtotime($kandidat_datum_ugovora)));
		}
	}
	

	// DATUM POCETKA RADA
	if($_POST['kandidat_datum_pocetakrada'] == ""){
		$kandidat_datum_pocetakrada = NULL;
		$kandidat_datum_pocetakrada_mjesec = NULL;
	}else{
		$kandidat_datum_pocetakrada = date("Y-m-d", strtotime($_POST['kandidat_datum_pocetakrada']));
		
		$kandidat_datum_pocetakrada_dan = date("d", strtotime($_POST['kandidat_datum_pocetakrada']));
		
		if($kandidat_datum_pocetakrada_dan < 15){
			$kandidat_datum_pocetakrada_mjesec = date("Y-m-d", strtotime($_POST['kandidat_datum_pocetakrada']));
		}else{
			$kandidat_datum_pocetakrada_mjesec =  date('Y-m-d', strtotime("+1 month", strtotime($kandidat_datum_pocetakrada)));
		}
	}

	$kandidat_password = MD5($kandidat_korisnickoime);
	$kandidat_status = 0;
	$kandidat_datetime = date('Y-m-d H:i:s');


	//Upload and save kandidat_slika
	if($_FILES['kandidat_slika']['size'] !== 0) {
		$kandidat_slika = $_FILES['kandidat_slika'];

		//Delete old image
		$product_img_query = $db->prepare("
									SELECT kandidat_slika
									FROM idk_kandidati
									WHERE kandidat_id = :kandidat_id");

		$product_img_query->execute(array(
								':kandidat_id' => $kandidat_id));

		$product_img = $product_img_query->fetch();

		$kandidat_slika2 = $product_img['kandidat_slika'];

		if($kandidat_slika2 == "none.jpg"){}else{
			unlink("files/kandidati/" . $kandidat_slika2);
		}
		//File properties
		$file_name = $kandidat_slika['name'];
		$file_tmp = $kandidat_slika['tmp_name'];
		$file_size = $kandidat_slika['size'];
		$file_error = $kandidat_slika['error'];

		//File extension
		$file_ext = explode('.', $file_name);
		$file_ext = strtolower(end($file_ext));

		$allowed = array('jpg', 'png');

		if(in_array($file_ext, $allowed)) {

			$kandidat_slika_final = uniqid() . '.' . $file_ext;
			$file_destination = 'files/kandidati/' . $kandidat_slika_final;

			if(move_uploaded_file($file_tmp, $file_destination)) {

				$path_to_image_directory = "files/kandidati/";
				$final_width_of_image = 660;

				if(preg_match('/[.](jpg)$/', $kandidat_slika_final)) {
					$im = imagecreatefromjpeg($path_to_image_directory . $kandidat_slika_final);
				} else if (preg_match('/[.](png)$/', $kandidat_slika_final)) {
					$im = imagecreatefrompng($path_to_image_directory . $kandidat_slika_final);
				}

				$ox = imagesx($im);
				$oy = imagesy($im);
				$nx = $final_width_of_image;
				$ny = floor($oy * ($final_width_of_image / $ox));
				$nm = imagecreatetruecolor($nx, $ny);

				imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);
				imagejpeg($nm, $path_to_image_directory . $kandidat_slika_final);

			}
		}
	}else{
		$kandidat_slika_final = $_POST['kandidat_slika_current'];
	}
	
	$query_update = $db->prepare("
					UPDATE idk_kandidati
					SET	kandidat_ime = :kandidat_ime, kandidat_prezime = :kandidat_prezime, kandidat_spol = :kandidat_spol, kandidat_djevojackoprezime = :kandidat_djevojackoprezime, kandidat_mjestorodjenja = :kandidat_mjestorodjenja, kandidat_drzavarodjenja = :kandidat_drzavarodjenja, kandidat_drzavljanstvo = :kandidat_drzavljanstvo, kandidat_adresa = :kandidat_adresa, kandidat_grad = :kandidat_grad, kandidat_pbroj = :kandidat_pbroj, kandidat_drzava = :kandidat_drzava, kandidat_email = :kandidat_email, kandidat_slika = :kandidat_slika, kandidat_datumrodjenja = :kandidat_datumrodjenja, kandidat_vozacka_dozvola = :kandidat_vozacka_dozvola, kandidat_vozacka_kategorija = :kandidat_vozacka_kategorija, kandidat_prijava_na = :kandidat_prijava_na, kandidat_drzavljanstvo_vrsta = :kandidat_drzavljanstvo_vrsta, datum_termina = :datum_termina, datum_aplikacije = :datum_aplikacije, kandidat_procjenatermina = :kandidat_procjenatermina, kandidat_viza = :kandidat_viza, kandidat_viza_vrijedi_do = :kandidat_viza_vrijedi_do, kandidat_datum_ugovora = :kandidat_datum_ugovora, kandidat_datum_pocetakrada = :kandidat_datum_pocetakrada, kandidat_datum_ugovora_mjesec = :kandidat_datum_ugovora_mjesec, kandidat_datum_pocetakrada_mjesec = :kandidat_datum_pocetakrada_mjesec, kandidat_broj_pasosa = :kandidat_broj_pasosa
					WHERE kandidat_id = :kandidat_id");

	$query_update->execute(array(
				':kandidat_ime' => $kandidat_ime,
				':kandidat_prezime' => $kandidat_prezime,
				':kandidat_spol' => $kandidat_spol,
				':kandidat_djevojackoprezime' => $kandidat_djevojackoprezime,
				':kandidat_mjestorodjenja' => $kandidat_mjestorodjenja,
				':kandidat_drzavarodjenja' => $kandidat_drzavarodjenja,
				':kandidat_drzavljanstvo' => $kandidat_drzavljanstvo,
				':kandidat_adresa' => $kandidat_adresa,
				':kandidat_grad' => $kandidat_grad,
				':kandidat_pbroj' => $kandidat_pbroj,
				':kandidat_drzava' => $kandidat_drzava,
				':kandidat_email' => $kandidat_email,
				':kandidat_slika' => $kandidat_slika_final,
				':kandidat_datumrodjenja' => $kandidat_datumrodjenja,
				':kandidat_vozacka_dozvola' => $kandidat_vozacka_dozvola,
				':kandidat_vozacka_kategorija' => $kandidat_vozacka_kategorija,
				':kandidat_prijava_na' => $kandidat_prijava_na,
				':kandidat_drzavljanstvo_vrsta' => $kandidat_drzavljanstvo_vrsta,
				':datum_termina' => $kandidat_datum_termina,
				':datum_aplikacije' => $kandidat_datum_aplikacije,
				':kandidat_viza' => $kandidat_viza,
				':kandidat_viza_vrijedi_do' => $kandidat_viza_vrijedi_do,
				':kandidat_id' => $kandidat_id,
				':kandidat_procjenatermina' => $kandidat_procjenatermina,
				':kandidat_datum_ugovora' => $kandidat_datum_ugovora,
				':kandidat_datum_pocetakrada' => $kandidat_datum_pocetakrada,
				':kandidat_datum_ugovora_mjesec' => $kandidat_datum_ugovora_mjesec,
				':kandidat_datum_pocetakrada_mjesec' => $kandidat_datum_pocetakrada_mjesec,
				':kandidat_broj_pasosa' => $kandidat_broj_pasosa
	));
	
	$checkDepartureType = getCandidateDepartureType($kandidat_id);

	if($kandidat_termin_da_ne == 1 AND $kandidat_datum_termina != null AND $checkDepartureType[0] == 2){
		try{
			$resultPrebacivanja = prebaciNaPrikupljanjeDokumentacije($kandidat_id, 1);
		}catch (Exception $e){
			$resultPrebacivanja = $e->getMessage();
		}
		$log_automatskog_prebacivanja = "Kandidat ID: [".$kandidat_id."]. Status automatskog prebacivanja na prikupljanje dokumentacije: ".$resultPrebacivanja;
	}
	unset($checkDepartureType); 
	//Add to LOGS
	$log_desc = "Uredio kandidata: " . $kandidat_ime . " " . $kandidat_prezime . ". " . $log_automatskog_prebacivanja;
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_employeeid, log_desc, log_date)
						VALUES
						(:log_employeeid, :log_desc, :log_date)");
	$log_query->execute(array(
					':log_employeeid' => $logged_employee_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: kandidati?page=open&id=$kandidat_id&mess=11");


break;

case "add_kandidat_kontakt":

	$kki_kandidat_id = $_POST['kki_kandidat_id'];
	$kki_kandidat_check = $_POST['kki_kandidat_check'];
	$kki_grupa = $_POST['kki_grupa'];

	if($kki_grupa == 1){
		$kki_naziv = $_POST['kki_naziv'];
		$kki_podatak = $_POST['kki_podatak'];
	}elseif ($kki_grupa == 2){
		$kki_naziv = "E-mail";
		$kki_podatak = $_POST['kki_naziv_email'];
	}elseif ($kki_grupa == 3){
		$kki_naziv = "Web";
		$kki_podatak = $_POST['kki_naziv_web'];
	}elseif ($kki_grupa == 4){
		$kki_naziv = $_POST['kki_naziv_messangeri'];
		$kki_podatak = $_POST['kki_podatak_messangeri'];
	}else{
		header("Location: registracija/korak2/$kki_kandidat_id/$kki_kandidat_check");
	};


			//Add kontakt info to db
		$query = $db->prepare("
						INSERT INTO idk_kandidat_kontakt_info
							(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id)
						VALUES
							(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id)");

		$query->execute(array(
					':kki_grupa' => $kki_grupa,
					':kki_naziv' => $kki_naziv,
					':kki_podatak' => $kki_podatak,
					':kki_kandidat_id' => $kki_kandidat_id));

		header("Location: registracija/korak2/$kki_kandidat_id/$kki_kandidat_check");
break;

case "edit_kandidat_kontakt":

	$kandidat_id = $_POST['kandidat_id'];
	$kki_id      = $_POST['kki_id'];
	$kki_grupa   = $_POST['kki_grupa'];

	$kandidat_ime_prezime = getCandidateFullnameR($kandidat_id);

	if ($kki_grupa == 1) {

		$kki_naziv   = $_POST['kki_naziv'];
		$kki_podatak = $_POST['kki_podatak1'];

		$query = $db->prepare("
			SELECT kki_primary FROM idk_kandidat_kontakt_info WHERE kki_id = :kki_id;
		");

		$query->execute(array(
			':kki_id' => $kki_id
		));

		$primary = $query->fetch();

		if ($primary['kki_primary'] == 1) {

			$query = $db->prepare("
				UPDATE idk_kandidati
				SET	kandidat_mobitel = :kki_podatak
				WHERE kandidat_id = :kandidat_id
			");

			$query->execute(array(
				':kki_podatak' => $kki_podatak,
				':kandidat_id' => $kandidat_id
			));

		}

	} else if ($kki_grupa == 2) {

		$kki_naziv   = "E-mail";
		$kki_podatak = $_POST['kki_naziv_email'];

		$query = $db->prepare("
			SELECT kki_primary FROM idk_kandidat_kontakt_info WHERE kki_id = :kki_id;
		");

		$query->execute(array(
			':kki_id' => $kki_id
		));

		$primary = $query->fetch();

		if ($primary['kki_primary'] == 1) {

			$query = $db->prepare("
				UPDATE idk_kandidati
				SET	kandidat_email = :kki_podatak
				WHERE kandidat_id = :kandidat_id
			");

			$query->execute(array(
				':kki_podatak' => $kki_podatak,
				':kandidat_id' => $kandidat_id
			));

		}

	} else if ($kki_grupa == 3) {

		$kki_naziv   = "Web";
		$kki_podatak = $_POST['kki_naziv_web'];

	} else if ($kki_grupa == 4) {

		$kki_naziv   = $_POST['kki_naziv_messangeri'];
		$kki_podatak = $_POST['kki_podatak_messangeri'];

	}

	$query = $db->prepare("
		UPDATE idk_kandidat_kontakt_info
		SET	kki_naziv = :kki_naziv, kki_podatak = :kki_podatak
		WHERE kki_id = :kki_id
	");

	$query->execute(array(
		':kki_naziv'   => $kki_naziv,
		':kki_podatak' => $kki_podatak,
		':kki_id'      => $kki_id
	));

	//Add to LOGS
	$log_desc = "Uredio kontakt podatke kandidatu: " . $kandidat_ime_prezime . ". " . $kki_naziv . ": " . $kki_podatak . "";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
		INSERT INTO idk_logs
			(log_employeeid, log_desc, log_date)
		VALUES
			(:log_employeeid, :log_desc, :log_date)
	");

	$log_query->execute(array(
		':log_employeeid' => $logged_employee_id,
		':log_desc'       => $log_desc,
		':log_date'       => $log_date
	));

	header("Location: kandidati?page=open&id=$kandidat_id&mess=6");

break;

case "add_kandidat_iskustvo":

		$kri_darum_od = date("Y-m-d", strtotime("01-".$_POST['kri_darum_od']));
		if(isset($_POST['kri_datum_do_aktuelno'])){
			$kri_datum_do = "";
		}else{
			$kri_datum_do = date("Y-m-d", strtotime("01-".$_POST['kri_datum_do']));
		}

		$kri_pozicija = $_POST['kri_pozicija'];
		$kri_naziv = $_POST['kri_naziv'];

		$kri_grad = $_POST['kri_grad'];
		$kri_drzava = $_POST['kri_drzava'];
		$kri_adresa = $_POST['kri_adresa'];
		$kri_telefon = $_POST['kri_telefon'];
		$kri_email = $_POST['kri_email'];
		$kri_web = $_POST['kri_web'];

		$kri_opis = $_POST['kri_opis'];
		$kri_kandidat_id = $_POST['kri_kandidat_id'];
		$kri_kandidat_check = $_POST['kri_kandidat_check'];


		//Add user to db
		$query = $db->prepare("
						INSERT INTO idk_kandidat_radno_iskustvo
							(kri_darum_od, kri_datum_do, kri_pozicija, kri_naziv, kri_grad, kri_drzava, kri_adresa, kri_telefon, kri_email, kri_web, kri_opis, kri_kandidat_id)
						VALUES
							(:kri_darum_od, :kri_datum_do, :kri_pozicija, :kri_naziv, :kri_grad, :kri_drzava, :kri_adresa, :kri_telefon, :kri_email, :kri_web, :kri_opis, :kri_kandidat_id)");

		$query->execute(array(
					':kri_darum_od' => $kri_darum_od,
					':kri_datum_do' => $kri_datum_do,
					':kri_pozicija' => $kri_pozicija,
					':kri_naziv' => $kri_naziv,
					':kri_grad' => $kri_grad,
					':kri_drzava' => $kri_drzava,
					':kri_adresa' => $kri_adresa,
					':kri_telefon' => $kri_telefon,
					':kri_email' => $kri_email,
					':kri_web' => $kri_web,
					':kri_opis' => $kri_opis,
					':kri_kandidat_id' => $kri_kandidat_id));

					header("Location: registracija/korak4/$kri_kandidat_id/$kri_kandidat_check");

break;

case "edit_kandidat_iskustvo":

		$kri_kandidat_id = $_POST['kri_kandidat_id'];
		$kri_id = $_POST['kri_id'];

		$kri_darum_od = date("Y-m-d", strtotime("01-".$_POST['kri_darum_od']));
		if(isset($_POST['kri_datum_do_aktuelno'])){
			$kri_datum_do = null;
		}else{
			$kri_datum_do = date("Y-m-d", strtotime("01-".$_POST['kri_datum_do']));
		}

		$kri_pozicija = $_POST['kri_pozicija'];
		$kri_pozicija_de = $_POST['kri_pozicija_de'];
		$kri_pozicija_en = $_POST['kri_pozicija_en'];
		$kri_naziv = $_POST['kri_naziv'];

		$kri_grad = $_POST['kri_grad'];
		$kri_drzava = $_POST['kri_drzava'];
		$kri_adresa = $_POST['kri_adresa'];
		$kri_telefon = $_POST['kri_telefon'];
		$kri_email = $_POST['kri_email'];
		$kri_web = $_POST['kri_web'];

		$kri_opis_de = $_POST['kri_opis_de'];
		$kri_kandidat_id = $_POST['kri_kandidat_id'];
		$kri_kandidat_check = $_POST['kri_kandidat_check'];
		$kri_prikaz_pp = $_POST['kri_prikaz_pp'];
		
		if($kri_prikaz_pp != 1){
			$kri_prikaz_pp = 0;
		}



	$query = $db->prepare("
					UPDATE idk_kandidat_radno_iskustvo
					SET	kri_darum_od = :kri_darum_od, kri_datum_do = :kri_datum_do, kri_pozicija = :kri_pozicija, kri_pozicija_de = :kri_pozicija_de, kri_pozicija_en = :kri_pozicija_en, kri_naziv = :kri_naziv, kri_opis_de = :kri_opis_de, kri_grad = :kri_grad, kri_prikaz_pp = :kri_prikaz_pp
					WHERE kri_id = :kri_id");

	$query->execute(array(
				':kri_darum_od' => $kri_darum_od,
				':kri_datum_do' => $kri_datum_do,
				':kri_pozicija' => $kri_pozicija,
				':kri_pozicija_de' => $kri_pozicija_de,
				':kri_pozicija_en' => $kri_pozicija_en,
				':kri_naziv' => $kri_naziv,
				':kri_opis_de' => $kri_opis_de,
				':kri_grad' => $kri_grad,
				":kri_prikaz_pp" => $kri_prikaz_pp,
				':kri_id' => $kri_id
				));

	//Add to LOGS
	$kandidat_ime_prezime = getCandidateFullnameR($kri_kandidat_id);
	$log_desc = "Uredio radno iskustvo kandidatu: " . $kandidat_ime_prezime . "";
	$log_date = date('Y-m-d H:i:s');

		$log_query = $db->prepare("
						INSERT INTO idk_logs
							(log_employeeid, log_desc, log_date)
							VALUES
							(:log_employeeid, :log_desc, :log_date)");
		$log_query->execute(array(
						':log_employeeid' => $logged_employee_id,
						':log_desc' => $log_desc,
						':log_date' => $log_date));

header("Location: kandidati?page=open&id=$kri_kandidat_id&mess=8");


break;


case "add_kandidat_edukacija":

		$ke_datumod	 = date("Y-m-d", strtotime("01-".$_POST['ke_datumod']));

		if(isset($_POST['ke_datumdo_aktuelno'])){
			$ke_datumdo = "";
		}else{
			$ke_datumdo = date("Y-m-d", strtotime("01-".$_POST['ke_datumdo']));
		}


		if($_POST['ke_naziv_kvalifikacije'] != "OSTALO"){
			$ke_naziv_kvalifikacije = $_POST['ke_naziv_kvalifikacije'];
		}else{
			$ke_naziv_kvalifikacije = $_POST['ke_naziv_kvalifikacije_ostalo'];
		}

		$ke_naziv = $_POST['ke_naziv'];
		$ke_grad = $_POST['ke_grad'];
		$ke_drzava = $_POST['ke_drzava'];
		$ke_opis = $_POST['ke_opis'];
		$ke_kandidat_id = $_POST['ke_kandidat_id'];
		$ke_kandidat_check = $_POST['ke_kandidat_check'];


		//Add user to db
		$query = $db->prepare("
						INSERT INTO idk_kandidat_edukacija
							(ke_datumod	, ke_datumdo, ke_naziv_kvalifikacije, ke_naziv, ke_grad, ke_drzava, ke_opis, ke_kandidat_id)
						VALUES
							(:ke_datumod, :ke_datumdo, :ke_naziv_kvalifikacije, :ke_naziv, :ke_grad, :ke_drzava, :ke_opis, :ke_kandidat_id)");

		$query->execute(array(
					':ke_datumod' => $ke_datumod	,
					':ke_datumdo' => $ke_datumdo,
					':ke_naziv_kvalifikacije' => $ke_naziv_kvalifikacije,
					':ke_naziv' => $ke_naziv,
					':ke_grad' => $ke_grad,
					':ke_drzava' => $ke_drzava,
					':ke_opis' => $ke_opis,
					':ke_kandidat_id' => $ke_kandidat_id));

		header("Location: registracija/korak3/$ke_kandidat_id/$ke_kandidat_check");

break;

case "edit_kandidat_edukacija":

		$ke_datumod	 = date("Y-m-d", strtotime("01-".$_POST['ke_datumod']));

		if(isset($_POST['ke_datumdo_aktuelno'])){
			$ke_datumdo = NULL;
		}else{
			$ke_datumdo = date("Y-m-d", strtotime("01-".$_POST['ke_datumdo']));
		}

		$ke_id = $_POST['ke_id'];
		$ke_kandidat_id = $_POST['ke_kandidat_id'];
		$ke_grad = $_POST['ke_grad'];
		$ke_drzava = $_POST['ke_drzava'];
		$ke_opis = $_POST['ke_opis'];
		$ke_kandidat_id = $_POST['ke_kandidat_id'];
		$ke_vrsta = $_POST['ke_vrsta'];
		if(isset($_POST['ke_prikaz_pp'])){
			$ke_prikaz_pp = $_POST['ke_prikaz_pp'];	
		} else {
			$ke_prikaz_pp = 0;
		}
		$ke_aktuelno = (isset($_POST['ke_datumdo_aktuelno'])) ? 1 : 0;

	$query = $db->prepare("
					UPDATE idk_kandidat_edukacija
					SET	ke_datumod = :ke_datumod, ke_datumdo = :ke_datumdo, ke_grad = :ke_grad, ke_drzava = :ke_drzava, ke_opis = :ke_opis, ke_vrsta_obrazovanja = :ke_vrsta_obrazovanja, ke_prikaz_pp = :ke_prikaz_pp, ke_aktuelno = :ke_aktuelno
					WHERE ke_id = :ke_id");

	$query->execute(array(
				':ke_datumod' => $ke_datumod,
				':ke_datumdo' => $ke_datumdo,
				':ke_grad' => $ke_grad,
				':ke_drzava' => $ke_drzava,
				':ke_opis' => $ke_opis,
				':ke_vrsta_obrazovanja' => $ke_vrsta,
				':ke_id' => $ke_id,
				':ke_prikaz_pp' => $ke_prikaz_pp,
				':ke_aktuelno' => $ke_aktuelno
				));


	//Add to LOGS
	$kandidat_ime_prezime = getCandidateFullnameR($ke_kandidat_id);
	$log_desc = "Uredio stavku edukacija kandidatu: " . $kandidat_ime_prezime . "";
	$log_date = date('Y-m-d H:i:s');

		$log_query = $db->prepare("
						INSERT INTO idk_logs
							(log_employeeid, log_desc, log_date)
							VALUES
							(:log_employeeid, :log_desc, :log_date)");
		$log_query->execute(array(
						':log_employeeid' => $logged_employee_id,
						':log_desc' => $log_desc,
						':log_date' => $log_date));



		header("Location: kandidati?page=open&id=$ke_kandidat_id&mess=7");

break;

case "add_kandidat_vjestine":

		$kv_naziv = $_POST['kv_naziv'];
		$kv_grupa = $_POST['kv_grupa'];
		$kv_opis = $_POST['kv_opis'];
		$kv_kandidat_id = $_POST['kv_kandidat_id'];
		$kv_kandidat_check = $_POST['kv_kandidat_check'];


		//Add user to db
		$query = $db->prepare("
						INSERT INTO idk_kandidat_vjestine
							(kv_naziv, kv_grupa, kv_opis, kv_kandidat_id)
						VALUES
							(:kv_naziv, :kv_grupa, :kv_opis, :kv_kandidat_id)");

		$query->execute(array(
					':kv_naziv' => $kv_naziv,
					':kv_grupa' => $kv_grupa,
					':kv_opis' => $kv_opis,
					':kv_kandidat_id' => $kv_kandidat_id));

					header("Location: registracija/korak5/$kv_kandidat_id/$kv_kandidat_check");

break;

case "edit_kandidat_vjestine":

		$kv_kandidat_id = $_POST['kv_kandidat_id'];
		$kv_id = $_POST['kv_id'];

		$kv_naziv = $_POST['kv_naziv'];
		$kv_grupa = $_POST['kv_grupa'];
		$kv_opis = $_POST['kv_opis'];


	$query = $db->prepare("
					UPDATE idk_kandidat_vjestine
					SET	kv_naziv = :kv_naziv, kv_grupa = :kv_grupa, kv_opis = :kv_opis
					WHERE kv_id = :kv_id");

	$query->execute(array(
				':kv_naziv' => $kv_naziv,
				':kv_grupa' => $kv_grupa,
				':kv_opis' => $kv_opis,
				':kv_id' => $kv_id
				));

	//Add to LOGS
	$kandidat_ime_prezime = getCandidateFullnameR($kv_kandidat_id);
	$log_desc = "Uredio vještine kandidatu: " . $kandidat_ime_prezime . "";
	$log_date = date('Y-m-d H:i:s');

		$log_query = $db->prepare("
						INSERT INTO idk_logs
							(log_employeeid, log_desc, log_date)
							VALUES
							(:log_employeeid, :log_desc, :log_date)");
		$log_query->execute(array(
						':log_employeeid' => $logged_employee_id,
						':log_desc' => $log_desc,
						':log_date' => $log_date));

		header("Location: kandidati?page=open&id=$kv_kandidat_id&mess=9");

break;

case "add_kandidat_jezik":

		if(!empty($_POST['kj_ostalo'])){
			$kj_naziv = $_POST['kj_ostalo'];
		}else{
			$kj_naziv = $_POST['kj_naziv'];
		}


		//$kj_slusanje = $_POST['kj_slusanje'];
		//$kj_citanje = $_POST['kj_citanje'];
		//$kj_govorna_interakcija = $_POST['kj_govorna_interakcija'];
		//$kj_govorna_produkcija = $_POST['kj_govorna_produkcija'];
		//$kj_pisanje = $_POST['kj_pisanje'];
		$kj_kandidatid = $_POST['kj_kandidatid'];
		$kj_kandidat_check = $_POST['kj_kandidat_check'];

		$kj_slusanje = $_POST['kj_znanje'];
		$kj_citanje = $_POST['kj_znanje'];
		$kj_govorna_interakcija = $_POST['kj_znanje'];
		$kj_govorna_produkcija = $_POST['kj_znanje'];
		$kj_pisanje = $_POST['kj_znanje'];


		// NJEMACKI JEZIK

		$kj_naziv_njemacki = "Njemački";
		$kj_znanje_njemacki = $_POST['kj_znanje_njemacki'];

		if($_POST['kj_znanje_njemacki'] != NULL){
		// Add language knowlege
		$query_njem = $db->prepare("
						INSERT INTO idk_kandidat_jezici
							(kj_naziv, kj_slusanje, kj_citanje, kj_govorna_interakcija, kj_govorna_produkcija, kj_pisanje, kj_kandidatid)
						VALUES
							(:kj_naziv, :kj_slusanje, :kj_citanje, :kj_govorna_interakcija, :kj_govorna_produkcija, :kj_pisanje, :kj_kandidatid)");

		$query_njem->execute(array(
					':kj_naziv' => $kj_naziv_njemacki,
					':kj_slusanje' => $kj_znanje_njemacki,
					':kj_citanje' => $kj_znanje_njemacki,
					':kj_govorna_interakcija' => $kj_znanje_njemacki,
					':kj_govorna_produkcija' => $kj_znanje_njemacki,
					':kj_pisanje' => $kj_znanje_njemacki,
					':kj_kandidatid' => $kj_kandidatid));
		}

		if($_POST['kj_naziv'] != NULL){
		// Add language knowlege
		$query = $db->prepare("
						INSERT INTO idk_kandidat_jezici
							(kj_naziv, kj_slusanje, kj_citanje, kj_govorna_interakcija, kj_govorna_produkcija, kj_pisanje, kj_kandidatid)
						VALUES
							(:kj_naziv, :kj_slusanje, :kj_citanje, :kj_govorna_interakcija, :kj_govorna_produkcija, :kj_pisanje, :kj_kandidatid)");

		$query->execute(array(
					':kj_naziv' => $kj_naziv,
					':kj_slusanje' => $kj_slusanje,
					':kj_citanje' => $kj_citanje,
					':kj_govorna_interakcija' => $kj_govorna_interakcija,
					':kj_govorna_produkcija' => $kj_govorna_produkcija,
					':kj_pisanje' => $kj_pisanje,
					':kj_kandidatid' => $kj_kandidatid));
		}

		header("Location: registracija/korak6/$kj_kandidatid/$kj_kandidat_check");

break;

case "edit_kandidat_jezik":

		$kj_kandidatid = $_POST['kj_kandidatid'];
		$kj_id = $_POST['kj_id'];

		$kj_naziv = $_POST['kj_naziv'];
		$kj_slusanje = $_POST['kj_slusanje'];
		$kj_citanje = $_POST['kj_citanje'];
		$kj_govorna_interakcija = $_POST['kj_govorna_interakcija'];
		$kj_govorna_produkcija = $_POST['kj_govorna_produkcija'];
		$kj_pisanje = $_POST['kj_pisanje'];


	$query = $db->prepare("
					UPDATE idk_kandidat_jezici
					SET	kj_naziv = :kj_naziv, kj_slusanje = :kj_slusanje, kj_citanje = :kj_citanje, kj_govorna_interakcija = :kj_govorna_interakcija, kj_govorna_produkcija = :kj_govorna_produkcija, kj_pisanje = :kj_pisanje
					WHERE kj_id = :kj_id");

	$query->execute(array(
				':kj_naziv' => $kj_naziv,
				':kj_slusanje' => $kj_slusanje,
				':kj_citanje' => $kj_citanje,
				':kj_govorna_interakcija' => $kj_govorna_interakcija,
				':kj_govorna_produkcija' => $kj_govorna_produkcija,
				':kj_pisanje' => $kj_pisanje,
				':kj_id' => $kj_id
				));

	//Add to LOGS
	$kandidat_ime_prezime = getCandidateFullnameR($kj_kandidatid);
	$log_desc = "Uredio stavku jezik kandidatu: " . $kandidat_ime_prezime . "";
	$log_date = date('Y-m-d H:i:s');

		$log_query = $db->prepare("
						INSERT INTO idk_logs
							(log_employeeid, log_desc, log_date)
							VALUES
							(:log_employeeid, :log_desc, :log_date)");
		$log_query->execute(array(
						':log_employeeid' => $logged_employee_id,
						':log_desc' => $log_desc,
						':log_date' => $log_date));


		header("Location: kandidati?page=open&id=$kj_kandidatid&mess=10");

break;
case "provjeri_jezik_kandidata":

	$kj_kandidatid = $_POST['kandidat_id'];
	$cvl_id=$_POST['cvl_id'];
	$odabir_status_jezik=$_POST['odabir_status_jezik'];
	$kj_ustanova=$_POST['kj_ustanova'];				
	if($kj_ustanova==""){
		$kj_ustanova=null;
	}
	$provjera_ustanove = $db->prepare("SELECT kj_ustanova FROM idk_kandidat_jezici WHERE kj_id = :kj_id;");
	$provjera_ustanove -> execute(array(':kj_id' => $cvl_id));
	$provjera_ustanove_row = $provjera_ustanove->fetch();
	$trenutna_ustanova = $provjera_ustanove_row['kj_ustanova'];
	if($trenutna_ustanova!=$kj_ustanova){
		$update_ustanove = $db->prepare("UPDATE idk_kandidat_jezici SET kj_ustanova = :kj_ustanova WHERE kj_id = :kj_id;");
		$update_ustanove -> execute(array(
			':kj_id' => $cvl_id,
			':kj_ustanova' => $kj_ustanova
		)); 
	}
	
	$ispit_datum=$_POST['ispit_datum'];
	if($ispit_datum == ""){
		$ispit_datum = null;
	}
	$file = $_FILES['naziv_dokument_jezik_new'];
	$path=$_POST['certificate_path'];
	if($file["name"] == "" and $path==""){// ako nije unešen fajl nece se izvrsiti upload , file je null zbog insterta 
		$file_status=1;
		$file = null;
	}else if($file['name']!= ""){// ako je unesen fajl prolazi funkcija za upload 
		
		$file_status=0;
	}else if($file['name']== "" and $path!=""){//ako nije ovaj put unesen fajl, ali postoji u bazi, zbog insterta ce se samo unijeti ponovo isti path
		$file=$path;
		$file_status=1;

	}
	$datum_k_certifikata=$_POST['datum_k_certifikata'];
	if($datum_k_certifikata == ""){
		$datum_k_certifikata = null;
	}
	$datum_i_certifikata=$_POST['datum_i_certifikata'];
	if($datum_i_certifikata == ""){
		$datum_i_certifikata = null;
	}
	$datum_pocetka_podnivo_1=$_POST['datum_pocetka_podnivo_1'];
	if($datum_pocetka_podnivo_1 == ""){
		$datum_pocetka_podnivo_1=null;
	}
	$kraj_podnivo_1 = $_POST['kraj_podnivo_1'];
	if($kraj_podnivo_1 == ""){
		$kraj_podnivo_1=null;
	}
	$datum_pocetka_podnivo_2=$_POST['datum_pocetka_podnivo_2'];
	if($datum_pocetka_podnivo_2 == ""){
		$datum_pocetka_podnivo_2=null;
	}
	$kraj_podnivo_2 = $_POST['kraj_podnivo_2'];
	if($kraj_podnivo_2 == ""){
		$kraj_podnivo_2=null;
	}
	$log_date=date('Y-m-d H:i:s');
	$cll_date=date('Y-m-d H:i:s');
	$query=$db->prepare("
				INSERT INTO idk_candidate_verified_languages 
					(cvl_id,cvl_status,cvl_course1_started,cvl_course1_ended,cvl_course2_started,cvl_course2_ended,cvl_exam_date,cvl_certificate_path,cvl_certficate_creation_date,cvl_certificate_expiration_date,cvl_last_updated) 
				VALUES 
					(:cvl_id,:odabir_status_jezik,:cvl_course1_started,:cvl_course1_ended,:cvl_course2_started,:cvl_course2_ended,:ispit_datum,:naziv_dokument_jezik_new,:datum_k_certifikata,:datum_i_certifikata,:log_date)
														ON DUPLICATE KEY UPDATE
														cvl_status=:odabir_status_jezik, cvl_course1_started=:cvl_course1_started, cvl_course1_ended=:cvl_course1_ended, cvl_course2_started=:cvl_course2_started, cvl_course2_ended=:cvl_course2_ended, cvl_exam_date=:ispit_datum,cvl_certificate_path=:naziv_dokument_jezik_new,cvl_certficate_creation_date=:datum_k_certifikata,cvl_certificate_expiration_date=:datum_i_certifikata,cvl_last_updated=:log_date
	");
	$query->execute(array(
		':cvl_id' 					=> $cvl_id,
		':odabir_status_jezik' 		=> $odabir_status_jezik,
		':cvl_course1_started' 		=> $datum_pocetka_podnivo_1,
		':cvl_course1_ended'		=> $kraj_podnivo_1,
		':cvl_course2_started' 		=> $datum_pocetka_podnivo_2,
		':cvl_course2_ended'		=> $kraj_podnivo_2,
		':ispit_datum' 				=> $ispit_datum,
		':naziv_dokument_jezik_new' =>$file,
		
		':datum_k_certifikata'=>$datum_k_certifikata,
		':datum_i_certifikata'=>$datum_i_certifikata,
		':log_date'=>$log_date
	));
	logDateDifference($cvl_id);
	$query_log=$db->prepare("
				INSERT INTO idk_candidate_language_logs
					(cll_candidate_id,cll_cvl_id,cll_status,cll_date,cll_employee_id) 
				VALUE 
					(:cll_candidate_id,:cll_cvl_id,:cll_status,:cll_date,:cll_employee_id)
	");
	$query_log->execute(array(
		':cll_candidate_id'=>$kj_kandidatid,
		':cll_cvl_id'=>$cvl_id,
		':cll_status'=>$odabir_status_jezik,
		':cll_date'=>$cll_date,
		':cll_employee_id'=>$logged_employee_id
	));
	if($odabir_status_jezik == 8 and $file_status==0){
	$filepath="";
	try{
		$filepath = uploadDocumentFileR($file,"/jobstep_pp/files/candidate_documents/");
	}                                                                                                                                                                                                                    
	catch(Exception $e)
	{
		http_response_code(500);
		die($e->getMessage());
	}
	$timeOfUpload=date('Y-m-d H:i:s');
	$query_add_certifcate= $db->prepare("
		UPDATE idk_candidate_verified_languages set cvl_certificate_path=:filepath,cvl_certificate_upload_date=:timeOfUpload WHERE cvl_id=:cvl_id;
		
	");
	$query_add_certifcate->execute(array(
		':filepath'=>$filepath,
		':timeOfUpload'=>$timeOfUpload,
		':cvl_id'=>$cvl_id
	));
	updateLastActiveTaskForCandidate($kj_kandidatid, 31);
	updateLastActiveTaskForCandidate($kj_kandidatid, 32);	
	}

	if ($odabir_status_jezik == 7 AND $ispit_datum != null) {
		updateLastActiveTaskForCandidate($kj_kandidatid, 31);
	}
	//STATUS AKTIVAN
	if($odabir_status_jezik <=9)
	{
		$query_active= $db->prepare("
		UPDATE idk_candidate_verified_languages join idk_kandidat_jezici on idk_candidate_verified_languages.cvl_id=idk_kandidat_jezici.kj_id 
		set idk_candidate_verified_languages.cvl_active=1 
		where idk_kandidat_jezici.kj_kandidatid=:kj_kandidatid and idk_candidate_verified_languages.cvl_id=:cvl_id	");
		$query_inactive= $db->prepare("
		UPDATE idk_candidate_verified_languages join idk_kandidat_jezici on idk_candidate_verified_languages.cvl_id=idk_kandidat_jezici.kj_id 
		set idk_candidate_verified_languages.cvl_active=0 
		where idk_kandidat_jezici.kj_kandidatid=:kj_kandidatid and idk_candidate_verified_languages.cvl_id!=:cvl_id");

		$query_active->execute(array(
			':kj_kandidatid'=>$kj_kandidatid,
			':cvl_id'=>$cvl_id
		));
		$query_inactive->execute(array(
			':kj_kandidatid'=>$kj_kandidatid,
			':cvl_id'=>$cvl_id
		));
	}else if($odabir_status_jezik >9){
		$query_check=$db->prepare("
		select count(cvl_id) as broj,cvl_id from idk_kandidat_jezici join idk_candidate_verified_languages on cvl_id=kj_id where kj_kandidatid=:kj_kandidatid  

		");
		$query_check->execute(array(
			':kj_kandidatid'=>$kj_kandidatid
		));
		$select_row = $query_check->fetch();
		if($select_row['broj']==1){
			$query_active_default= $db->prepare("
			UPDATE idk_candidate_verified_languages join idk_kandidat_jezici on idk_candidate_verified_languages.cvl_id=idk_kandidat_jezici.kj_id 
			set idk_candidate_verified_languages.cvl_active=1 
			where idk_kandidat_jezici.kj_kandidatid=:kj_kandidatid and idk_candidate_verified_languages.cvl_id=:cvl_id	");
			$query_active_default->execute(array(
				':kj_kandidatid'=>$kj_kandidatid,
				':cvl_id'=>$select_row['cvl_id']
			));
		}
		
	}
	$preko=1;

	// UNOSOM NAPREDUJE NA VEĆI NIVO ODRADITI UNOS TOG NIVOA I UPDATE STAROG NA NEAKTIVAN
	if($odabir_status_jezik == 10){
		$kj_naziv = "Njemački";
		$kj_slusanje = $_POST['prelazak_na_veci_nivo'];
		$prelazak_na_veci_nivo_podnivo = 3;
		$prelazak_na_veci_nivo_datum_pocetka = $_POST['prelazak_na_veci_nivo_datum_pocetka'];
		$prelazak_na_veci_nivo_datum_kraja = $_POST['prelazak_na_veci_nivo_datum_kraja'];
		$datum_pocetka_podnivo_1 = $prelazak_na_veci_nivo_datum_pocetka;
		$kraj_podnivo_1 = $prelazak_na_veci_nivo_datum_kraja;
		$datum_pocetka_podnivo_2 = null;
		$kraj_podnivo_2 = null;

		$query = $db->prepare("
						INSERT INTO idk_kandidat_jezici
							(kj_naziv, kj_slusanje, kj_citanje, kj_govorna_interakcija, kj_govorna_produkcija, kj_pisanje, kj_kandidatid,kj_ustanova)
						VALUES
							(:kj_naziv, :kj_slusanje, :kj_citanje, :kj_govorna_interakcija, :kj_govorna_produkcija, :kj_pisanje, :kj_kandidatid, :kj_ustanova)");

		$query->execute(array(
					':kj_naziv' => $kj_naziv,
					':kj_slusanje' => $kj_slusanje,
					':kj_citanje' => $kj_slusanje,
					':kj_govorna_interakcija' => $kj_slusanje,
					':kj_govorna_produkcija' => $kj_slusanje,
					':kj_pisanje' => $kj_slusanje,
					':kj_kandidatid' => $kj_kandidatid,
					':kj_ustanova' => $kj_ustanova));

		$new_lang_id = $db->lastInsertId();

		$query_inactive= $db->prepare("
			UPDATE idk_candidate_verified_languages join idk_kandidat_jezici on idk_candidate_verified_languages.cvl_id=idk_kandidat_jezici.kj_id 
			SET idk_candidate_verified_languages.cvl_active=0 
			WHERE idk_kandidat_jezici.kj_kandidatid=:kj_kandidatid AND idk_candidate_verified_languages.cvl_active=1 AND kj_naziv LIKE '%Njemacki%'");

		$query_inactive->execute(array(
			':kj_kandidatid'=>$kj_kandidatid
		));

		$query_new_insert =$db->prepare("
				INSERT INTO idk_candidate_verified_languages 
					(cvl_id,cvl_status,cvl_course1_started,cvl_course1_ended,cvl_course2_started,cvl_course2_ended,cvl_last_updated,cvl_active) 
				VALUES 
					(:cvl_id,:odabir_status_jezik,:cvl_course1_started,:cvl_course1_ended,:cvl_course2_started,:cvl_course2_ended,:log_date, :cvl_active)
		");

		$query_new_insert->execute(array(
			':cvl_id' 					=> $new_lang_id,
			':odabir_status_jezik' 		=> $prelazak_na_veci_nivo_podnivo,
			':cvl_course1_started' 		=> $datum_pocetka_podnivo_1,
			':cvl_course1_ended'		=> $kraj_podnivo_1,
			':cvl_course2_started' 		=> $datum_pocetka_podnivo_2,
			':cvl_course2_ended'		=> $kraj_podnivo_2,
			':log_date'=>$log_date,
			':cvl_active'=>1
		));

		logDateDifference($new_lang_id);

		$query_log=$db->prepare("
					INSERT INTO idk_candidate_language_logs
						(cll_candidate_id,cll_cvl_id,cll_status,cll_date,cll_employee_id) 
					VALUE 
						(:cll_candidate_id,:cll_cvl_id,:cll_status,:cll_date,:cll_employee_id)
		");

		$query_log->execute(array(
			':cll_candidate_id'=>$kj_kandidatid,
			':cll_cvl_id'=>$new_lang_id,
			':cll_status'=>$prelazak_na_veci_nivo_podnivo,
			':cll_date'=>$cll_date,
			':cll_employee_id'=>$logged_employee_id
		));
	}

	try{
		$result=prebaciNaPrikupljanjeDokumentacije($kj_kandidatid,$preko);
		if($result == 2)
			$msg_nost = "&infoNost=28";
		else
			$msg_nost = "";

	}catch (Exception $e){
		
		$result=$e->getMessage();
		$msg_nost = "";
	}
	//Add to LOGS
	$kandidat_ime_prezime = getCandidateFullnameR($kj_kandidatid);
	$log_desc = "Uredio nivo jezika kandidatu: " . $kandidat_ime_prezime . " Status automatskog prebacivanja na prikupljanje dokumentacije: ".$result."";
	
	$log_type=11;
	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_employeeid, log_desc, log_date, log_type)
						VALUES
						(:log_employeeid, :log_desc, :log_date, :log_type)");
	$log_query->execute(array(
					':log_employeeid' => $logged_employee_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date,
					':log_type' => $log_type
						));
	
	updateCandidateProjectionAndInstallment($kj_kandidatid);
	header("Location: kandidati?page=open&id=$kj_kandidatid$msg_nost");

break;	

case "generate_new_link":
	
	$lg_url = $_POST['lg_url'];
	$lg_link_prijave = $_POST['lg_link_prijave'];
	$lg_desc = $_POST['lg_desc'];
	$lg_project = $_POST['lg_project'];
	$lg_types = $_POST['lg_types'];
	$lg_nalogid = $_POST['lg_nalogid'];
	$lg_language = $_POST['lg_language'];
	$lg_naslov_prijave = $_POST['lg_naslov_prijave'];
	$lg_tekst_prijave = $_POST['lg_tekst_prijave'];
	$poslano_sa_naloga = intval($_POST['poslano_sa_naloga']);
	if($poslano_sa_naloga == 1){
		$lg_partner_app = 1;
	}else{
		$lg_partner_app = 0;
	}
	
	if($lg_language == 0){
		$lg_language = 'bs';
	}
	else if($lg_language == 3){
		$lg_language = 'sr';
	}
	else if($lg_language == 1){
		$lg_language = 'de';
	}
	else if($lg_language == 2){
		$lg_language = 'it';
	}
	
	
	//Upload and save contact_image
	if($_FILES['idk_urlimg_prijave']['size'] !== 0) {
		$idk_urlimg_prijave = $_FILES['idk_urlimg_prijave'];

		//File properties
		$file_name = $idk_urlimg_prijave['name'];
		$file_tmp = $idk_urlimg_prijave['tmp_name'];
		$file_size = $idk_urlimg_prijave['size'];
		$file_error = $idk_urlimg_prijave['error'];

		//File extension
		$file_ext = explode('.', $file_name);
		$file_ext = strtolower(end($file_ext));

		$allowed = array('jpg', 'jpeg', 'png');

		if(in_array($file_ext, $allowed)) {

			$idk_urlimg_prijave_final = uniqid() . '.' . $file_ext;
			$file_destination = 'files/partner_nalogs/' . $idk_urlimg_prijave_final;

			if(move_uploaded_file($file_tmp, $file_destination)) {

				$path_to_image_directory = "files/" . getSubdomainr() . "_files/partner_nalogs/";
				$final_width_of_image = 660;

				if(preg_match('/[.](jpg)$/', $idk_urlimg_prijave_final)) {
					$im = imagecreatefromjpeg($path_to_image_directory . $idk_urlimg_prijave_final);
				} else if (preg_match('/[.](jpeg)$/', $idk_urlimg_prijave_final)) {
					$im = imagecreatefromjpeg($path_to_image_directory . $idk_urlimg_prijave_final);
				} else if (preg_match('/[.](png)$/', $idk_urlimg_prijave_final)) {
					$im = imagecreatefrompng($path_to_image_directory . $idk_urlimg_prijave_final);
				}

				$ox = imagesx($im);
				$oy = imagesy($im);
				$nx = $final_width_of_image;
				$ny = floor($oy * ($final_width_of_image / $ox));
				$nm = imagecreatetruecolor($nx, $ny);

				imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);
				imagejpeg($nm, $path_to_image_directory . $idk_urlimg_prijave_final);

			}
		}
	}else{
		$idk_urlimg_prijave_final = "none";
	}


	$lg_datetime = date('Y-m-d H:i:s');
	$insert = $db->prepare("
					INSERT INTO idk_link_generator
						(lg_url, lg_link_prijave, idk_urlimg_prijave, lg_desc, lg_employeeid, lg_datetime, lg_nalogid, lg_language, lg_partner_app, lg_questions_added, lg_naslov_na_formi, lg_tekst_na_formi)
						VALUES
						(:lg_url, :lg_link_prijave, :idk_urlimg_prijave, :lg_desc, :lg_employeeid, :lg_datetime, :lg_nalogid, :lg_language, :lg_partner_app, :lg_questions_added, :lg_naslov_na_formi, :lg_tekst_na_formi)");
	$insert->execute(array(
					':lg_url' => $lg_url,
					':lg_link_prijave' => $lg_link_prijave,
					':idk_urlimg_prijave' => $idk_urlimg_prijave_final,
					':lg_desc' => $lg_desc,
					':lg_employeeid' => $logged_employee_id,
					':lg_datetime' => $lg_datetime,
					':lg_nalogid' => $lg_nalogid,
					':lg_language' => $lg_language,
					':lg_partner_app' => $lg_partner_app,
					':lg_questions_added' => 1,
					':lg_naslov_na_formi' => $lg_naslov_prijave,
					':lg_tekst_na_formi' => $lg_tekst_prijave
					));

	$lg_id = $db->lastInsertId();

	//Questions for form
	$stmt = $db->prepare("
		SELECT qff_id, qff_short_name, qff_default_sort_order
		FROM idk_questions_for_form
		WHERE qff_is_active = 1
	");
	$stmt->execute();
	$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

	$insert_q = $db->prepare("INSERT INTO idk_link_questions (lq_link_id, lq_question_id, lq_is_required, lq_sort_order) VALUES (?, ?, ?, ?)");
	foreach ($questions as $q){
		$short 	= $q['qff_short_name'];
		$id		= $q['qff_id'];
		$order 	= $q['qff_default_sort_order'];

		$value = isset($_POST['formq'][$short]) ? (int)$_POST['formq'][$short] : 0;
		$insert_q->execute([$lg_id, $id, $value, $order]);
	}

	$dp = $_POST['formdp'] ?? [];
	$ins = $db->prepare("INSERT INTO idk_link_dodatna_pitanja (ldp_link_id, ldp_dp_id) VALUES (?, ?)");

	foreach ($dp as $dp_id => $yesno) {
		if ((int)$yesno === 1) {
			$ins->execute([(int)$lg_id, (int)$dp_id]);
		}
	}
	
	foreach($lg_types as $group_id){
		$insert_group = $db->prepare("
						INSERT INTO idk_link_generator_rel
							(lr_lgid, lr_groupid)
							VALUES
							(:lr_lgid, :lr_groupid)");
		$insert_group->execute(array(
						':lr_lgid' => $lg_id,
						':lr_groupid' => $group_id
						));
	}

	foreach($lg_project as $projectid){
		$insert_project = $db->prepare("
						INSERT INTO idk_link_projects
							(lp_linkid, lp_projectid)
							VALUES
							(:lp_linkid, :lp_projectid)");
		$insert_project->execute(array(
						':lp_linkid' => $lg_id,
						':lp_projectid' => $projectid
						));
	}

	$log_desc = "Generisao novi link: " . $lg_desc . "";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_employeeid, log_desc, log_date)
						VALUES
						(:log_employeeid, :log_desc, :log_date)");
	$log_query->execute(array(
					':log_employeeid' => $logged_employee_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));
	
	if($poslano_sa_naloga == 1){
		header("Location: nalozi?page=open&id=$lg_nalogid&tab=opis_partnerapp");
	}else{
		header("Location: link_generator?page=list&mess=1");
	}
break;

case "generate_new_link_edit":

	$lg_id = $_POST['lg_id'];
	$lg_link_prijave = $_POST['lg_link_prijave'];
	$lg_url = $_POST['lg_url'];
	$lg_desc = $_POST['lg_desc'];
	$lg_types = $_POST['lg_types'];
	$lg_nalogid = $_POST['lg_nalogid'];
	$lg_language = $_POST['lg_language'];
	$lg_naslov_prijave = $_POST['lg_naslov_prijave'];
	$lg_tekst_prijave = $_POST['lg_tekst_prijave'];
	$poslano_sa_naloga = intval($_POST['poslano_sa_naloga']);
	if($poslano_sa_naloga == 1){
		$lg_partner_app = 1;
	}else{
		$lg_partner_app = 0;
	}
	
	
	//Upload and save contact_image
	if($_FILES['idk_urlimg_prijave']['size'] !== 0) {
		$idk_urlimg_prijave = $_FILES['idk_urlimg_prijave'];

		//File properties
		$file_name = $idk_urlimg_prijave['name'];
		$file_tmp = $idk_urlimg_prijave['tmp_name'];
		$file_size = $idk_urlimg_prijave['size'];
		$file_error = $idk_urlimg_prijave['error'];

		//File extension
		$file_ext = explode('.', $file_name);
		$file_ext = strtolower(end($file_ext));

		$allowed = array('jpg', 'jpeg', 'png');

		if(in_array($file_ext, $allowed)) {

			$idk_urlimg_prijave_final = uniqid() . '.' . $file_ext;
			$file_destination = 'files/partner_nalogs/' . $idk_urlimg_prijave_final;

			if(move_uploaded_file($file_tmp, $file_destination)) {

				$path_to_image_directory = "files/" . getSubdomainr() . "_files/partner_nalogs/";
				$final_width_of_image = 660;

				if(preg_match('/[.](jpg)$/', $idk_urlimg_prijave_final)) {
					$im = imagecreatefromjpeg($path_to_image_directory . $idk_urlimg_prijave_final);
				} else if (preg_match('/[.](jpeg)$/', $idk_urlimg_prijave_final)) {
					$im = imagecreatefromjpeg($path_to_image_directory . $idk_urlimg_prijave_final);
				} else if (preg_match('/[.](png)$/', $idk_urlimg_prijave_final)) {
					$im = imagecreatefrompng($path_to_image_directory . $idk_urlimg_prijave_final);
				}

				$ox = imagesx($im);
				$oy = imagesy($im);
				$nx = $final_width_of_image;
				$ny = floor($oy * ($final_width_of_image / $ox));
				$nm = imagecreatetruecolor($nx, $ny);

				imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);
				imagejpeg($nm, $path_to_image_directory . $idk_urlimg_prijave_final);

			}
		}
	}else{
		$idk_urlimg_prijave_final = $_POST['idk_urlimg_prijave_exists'];
	}

	$query_language = $db->prepare("
					SELECT lg_language, lg_questions_added
					FROM idk_link_generator
					WHERE lg_id = :lg_id");
					
	$query_language->execute(array(
					':lg_id' => $lg_id
					));
					
	$row_language = $query_language->fetch();
	$language = $row_language['lg_language'];
	$questions_added = $row_language['lg_questions_added'];

	if($lg_language == 0){
		$lg_language = "bs";
	}
	else if($lg_language == 3){
		$lg_language = "sr";
	}
	else if($lg_language == 1){
		$lg_language = "de";
	}
	else if($lg_language == 2){
		$lg_language = "it";
	}

	$query = $db->prepare("
					UPDATE idk_link_generator
					SET	lg_url = :lg_url, lg_link_prijave = :lg_link_prijave, 
						idk_urlimg_prijave = :idk_urlimg_prijave, 
						lg_desc = :lg_desc, 
						lg_nalogid = :lg_nalogid, 
						lg_language = :lg_language, 
						lg_partner_app = :lg_partner_app, 
						lg_questions_added = :lg_questions_added,
						lg_naslov_na_formi = :lg_naslov_na_formi,
						lg_tekst_na_formi = :lg_tekst_na_formi
					WHERE lg_id = :lg_id");

	$query->execute(array(
				':lg_url' => $lg_url,
				':lg_link_prijave' => $lg_link_prijave,
				':idk_urlimg_prijave' => $idk_urlimg_prijave_final,
				':lg_desc' => $lg_desc,
				':lg_nalogid' => $lg_nalogid,
				':lg_language' => $lg_language,
				':lg_partner_app' => $lg_partner_app,
				':lg_questions_added' => 1,
				':lg_naslov_na_formi' => $lg_naslov_prijave,
				':lg_tekst_na_formi' => $lg_tekst_prijave,
				':lg_id' => $lg_id
				));

	$kar_del_query = $db->prepare("
								DELETE FROM idk_link_generator_rel
								WHERE lr_lgid = :lr_lgid");

	$kar_del_query->execute(array(
						':lr_lgid' => $lg_id));

	foreach($lg_types as $group_id){
		$insert_group = $db->prepare("
						INSERT INTO idk_link_generator_rel
							(lr_lgid, lr_groupid)
							VALUES
							(:lr_lgid, :lr_groupid)");
		$insert_group->execute(array(
						':lr_lgid' => $lg_id,
						':lr_groupid' => $group_id
						));
	}

	//Questions for form
	$stmt = $db->prepare("
		SELECT qff_id, qff_short_name, qff_default_sort_order
		FROM idk_questions_for_form
		WHERE qff_is_active = 1
	");
	$stmt->execute();
	$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

	if($questions_added){
		//UPDATE idk_link_questions: DELETE AND THEN INSERT
		$delete_q = $db->prepare("DELETE FROM idk_link_questions WHERE lq_link_id = :lg_id");
		$delete_q->execute(array(':lg_id' => $lg_id));
	}
	//INSERT idk_link_questions
	$insert_q = $db->prepare("INSERT INTO idk_link_questions (lq_link_id, lq_question_id, lq_is_required, lq_sort_order) VALUES (?, ?, ?, ?)");
	foreach ($questions as $q){
		$short 	= $q['qff_short_name'];
		$id		= $q['qff_id'];
		$order 	= $q['qff_default_sort_order'];

		$value = isset($_POST['formq'][$short]) ? (int)$_POST['formq'][$short] : 0;
		$insert_q->execute([$lg_id, $id, $value, $order]);
	}

	// Dodatna pitanja
	// before inserting new selections:
	$del = $db->prepare("DELETE FROM idk_link_dodatna_pitanja WHERE ldp_link_id = ?");
	$del->execute([(int)$lg_id]);

	$dp = $_POST['formdp'] ?? [];
	$ins = $db->prepare("INSERT INTO idk_link_dodatna_pitanja (ldp_link_id, ldp_dp_id) VALUES (?, ?)");
	foreach ($dp as $dp_id => $yesno) {
		if ((int)$yesno === 1) {
			$ins->execute([(int)$lg_id, (int)$dp_id]);
		}
	}


	$empoyee = getEmployeeFullnameR();
	$log_desc = "".$empoyee." uredio link: " . $lg_desc . "";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_employeeid, log_desc, log_date)
						VALUES
						(:log_employeeid, :log_desc, :log_date)");
	$log_query->execute(array(
					':log_employeeid' => $logged_employee_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	if($poslano_sa_naloga == 1){
		header("Location: nalozi?page=open&id=$lg_nalogid&tab=opis_partnerapp");
	}else{
		header("Location: link_generator?page=list&mess=2");
	}

break;

case "kreiraj_kampanju":
	
	$kd_naziv = $_POST['kd_naziv'];
	$kd_skraceni_naziv = $_POST['kd_skraceni_naziv'];
	$kd_poruka_kampanje = $_POST['kd_poruka_kampanje'];
	$kd_drzava = $_POST['kd_drzava'];
	if($kd_drzava == "Srbija")
		$kd_jezik_forme = "rs";
	else
		$kd_jezik_forme = "bs";
		
	$insert = $db->prepare("
					INSERT INTO idk_kampanje_dipl
						(kd_naziv, kd_skraceni_naziv, kd_poruka_kampanje, kd_drzava, kd_jezik_forme)
						VALUES
						(:kd_naziv, :kd_skraceni_naziv, :kd_poruka_kampanje, :kd_drzava, :kd_jezik_forme)");
	$insert->execute(array(
					':kd_naziv' => $kd_naziv,
					':kd_skraceni_naziv' => $kd_skraceni_naziv,
					':kd_poruka_kampanje' => $kd_poruka_kampanje,
					':kd_drzava' => $kd_drzava,
					':kd_jezik_forme' => $kd_jezik_forme
					));
	$kd_id = $db->lastInsertId();
	
	$file_names = array();
	$broj_slika = count($_FILES['kampanja_image']['name']);
	for($i=0; $i<count($_FILES['kampanja_image']['name']); $i++) {
		$tmpFilePath = $_FILES['kampanja_image']['tmp_name'][$i];
		if ($tmpFilePath != ""){
			$filename = $_FILES['kampanja_image']['name'][$i];
			$file_ext = explode('.', $filename);
			$file_ext = strtolower(end($file_ext));
			$file_name_new = uniqid() . '.' . $file_ext;
			$newFilePath = "files/kampanje/" . $file_name_new;
			
			if(move_uploaded_file($tmpFilePath, $newFilePath)) {
				$file_names[] = $file_name_new;
			}
		}
	}
	$file_names_s = implode(",", $file_names);
	$query = $db->prepare("
					UPDATE idk_kampanje_dipl 
					SET kd_slike = :kd_slike
					WHERE kd_id = :kd_id
					");

	$query->execute(array(
				':kd_slike' => $file_names_s,
				':kd_id' => $kd_id
				));

	$empoyee = getEmployeeFullnameR();
	$log_desc = "".$empoyee." dodao kampanju: " . $kd_id . "";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_employeeid, log_desc, log_date)
						VALUES
						(:log_employeeid, :log_desc, :log_date)");
	$log_query->execute(array(
					':log_employeeid' => $logged_employee_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));
	
	header("Location: kampanje?page=list&mess=1");
break;

case "edit_kampanja":

	$kd_id = $_POST['kd_id'];
	$kd_naziv = $_POST['kd_naziv'];
	$kd_skraceni_naziv = $_POST['kd_skraceni_naziv'];
	$kd_poruka_kampanje = $_POST['kd_poruka_kampanje'];
	$kd_drzava = $_POST['kd_drzava'];
	$kd_slike = $_POST['kd_slike'];
	if($kd_drzava == "Srbija")
		$kd_jezik_forme = "rs";
	else
		$kd_jezik_forme = "bs";
		
	$file_names = array();
	$broj_slika = count($_FILES['kampanja_image']['name']);
	for($i=0; $i<count($_FILES['kampanja_image']['name']); $i++) {
		$tmpFilePath = $_FILES['kampanja_image']['tmp_name'][$i];
		if ($tmpFilePath != ""){
			$filename = $_FILES['kampanja_image']['name'][$i];
			$file_ext = explode('.', $filename);
			$file_ext = strtolower(end($file_ext));
			$file_name_new = uniqid() . '.' . $file_ext;
			$newFilePath = "files/kampanje/" . $file_name_new;
			
			if(move_uploaded_file($tmpFilePath, $newFilePath)) {
				$file_names[] = $file_name_new;
			}
		}
	}
	$file_names_s = implode(",", $file_names);
	$kd_slike = $kd_slike.",".$file_names_s;

	$query = $db->prepare("
					UPDATE idk_kampanje_dipl
					SET	kd_naziv = :kd_naziv, kd_skraceni_naziv = :kd_skraceni_naziv, kd_poruka_kampanje = :kd_poruka_kampanje, kd_jezik_forme = :kd_jezik_forme, kd_drzava = :kd_drzava, kd_slike = :kd_slike
					WHERE kd_id = :kd_id");

	$query->execute(array(
				':kd_naziv' => $kd_naziv,
				':kd_skraceni_naziv' => $kd_skraceni_naziv,
				':kd_poruka_kampanje' => $kd_poruka_kampanje,
				':kd_jezik_forme' => $kd_jezik_forme,
				':kd_drzava' => $kd_drzava,
				':kd_slike' => $kd_slike,
				':kd_id' => $kd_id
				));
	
	
	$empoyee = getEmployeeFullnameR();
	$log_desc = "".$empoyee." uredio kampanju: " . $kd_id . "";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_employeeid, log_desc, log_date)
						VALUES
						(:log_employeeid, :log_desc, :log_date)");
	$log_query->execute(array(
					':log_employeeid' => $logged_employee_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: kampanje?page=list&mess=2");

break;

case "show_candidate_archive":

	$enable = $_GET['enable'];

	if($enable == 1){
		$month = time() + 60 * 60 * 24 * 30;
		setcookie('archive_status', 1, $month);
	}else{
		unset($_COOKIE['archive_status']);
		setcookie('archive_status', '', time() - 60 * 60 * 24 * 30);
	}

	$predhodni_link = $_SERVER['HTTP_REFERER'];

header("Location: $predhodni_link");

break;


// 14.09.2017 Grupe za kandidate

case "add_candidate_groups":

	$kg_title = $_POST['kg_title'];

	$kg_date = date('Y-m-d H:i:s');
	$insert = $db->prepare("
					INSERT INTO idk_kandidati_grupe
						(kg_title, kg_date, kg_employeeid, kg_status)
						VALUES
						(:kg_title, :kg_date, :kg_employeeid, :kg_status)");
	$insert->execute(array(
					':kg_title' => $kg_title,
					':kg_date' => $kg_date,
					':kg_employeeid' => $logged_employee_id,
					':kg_status' => 0
					));

	$log_desc = "Dodao novu grupu: " . $kg_title . "";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_employeeid, log_desc, log_date)
						VALUES
						(:log_employeeid, :log_desc, :log_date)");
	$log_query->execute(array(
					':log_employeeid' => $logged_employee_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: grupe_kandidata?page=list&mess=1");

break;

case "edit_candidate_groups":

	$kg_id = $_POST['kg_id'];
	$kg_title = $_POST['kg_title'];

	$query = $db->prepare("
					UPDATE idk_kandidati_grupe
					SET	kg_title = :kg_title
					WHERE kg_id = :kg_id");

	$query->execute(array(
				':kg_title' => $kg_title,
				':kg_id' => $kg_id
				));

	$empoyee = getEmployeeFullnameR();
	$log_desc = "".$empoyee." uredio grupu: " . $kg_title . "";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_employeeid, log_desc, log_date)
						VALUES
						(:log_employeeid, :log_desc, :log_date)");
	$log_query->execute(array(
					':log_employeeid' => $logged_employee_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: grupe_kandidata?page=list&mess=2");

break;

case "edit_email_data":

	$email_title_registracija_title = $_POST['email_title_registracija_title'];
	$email_title_registracija_txt = $_POST['email_title_registracija_txt'];

	$query_update_email_registration = $db->prepare("
					UPDATE idk_email_text
					SET	email_title = :email_title, email_txt = :email_txt
					WHERE email_value = :email_value");

	$query_update_email_registration->execute(array(
				':email_title' => $email_title_registracija_title,
				':email_txt' => $email_title_registracija_txt,
				':email_value' => 'email_registracija'
				));

	$empoyee = getEmployeeFullnameR();
	$log_desc = "".$empoyee." uredio email predložak: " . $email_title_registracija_title . "";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_employeeid, log_desc, log_date)
						VALUES
						(:log_employeeid, :log_desc, :log_date)");
	$log_query->execute(array(
					':log_employeeid' => $logged_employee_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));


	header("Location: settings.php?page=email&mess=1");

break;

case "rotate_image":

	$kand_id = $_GET['kand_id'];
	$image = $_GET['image'];
	$rotate = $_GET['rotate'];


	$filename = 'files/kandidati_doc/'.$image.'';
	if($rotate == 1){
		$degrees = 90;
	}else if ($rotate == 2){
		$degrees = -90;
	}


	// Content type
	header('Content-type: image/jpeg');

	// Load
	$source = imagecreatefromjpeg($filename);

	// Rotate
	$rotate = imagerotate($source, $degrees, 0);


	// Output
	imagejpeg($rotate,$filename);
	//file_put_contents($image,$rotate);

	// Free the memory
	imagedestroy($source);
	imagedestroy($rotate);

	header("Location: kandidati?page=open&id=$kand_id&mess=13");

break;

case "rotate_profile_image":

	$kand_id = $_GET['kand_id'];
	$image = $_GET['image'];
	$rotate = $_GET['rotate'];
	if ( isset($_COOKIE['image_ctr'])) {    
		setcookie('image_ctr', $_COOKIE['image_ctr'] + 1 );
		//echo 'image_ctr='.$_COOKIE['image_ctr'];
	}  else {
		setcookie('image_ctr', 1);
	}
	
	$image_cr = $_COOKIE['image_ctr'];
	//var_dump($kand_id);
	//var_dump($image);
	//var_dump($rotate);
//
	//exit();


	$filename = 'files/kandidati/'.$image.'';
	if($rotate == 1){
		$degrees = 90;
	}elseif ($rotate == 2){
		$degrees = -90;
	}


	// Content type
	header('Content-type: image/jpeg');

	// Load
	$source = imagecreatefromjpeg($filename);

	// Rotate
	$rotate = imagerotate($source, $degrees, 0);


	// Output
	imagejpeg($rotate,$filename);
	//file_put_contents($filename,$rotate);

	// Free the memory
	imagedestroy($source);
	imagedestroy($rotate);

	header("Location: kandidati?page=open&id=$kand_id&mess=16");

break;

case "add_candidate_note":

	$note_txt = $_POST['note_txt'];
	$note_datetime = date('Y-m-d H:i:s');
	$note_group = 2;
	$note_dataid = $_POST['note_dataid'];
	$note_attachment = intval($_POST['note_attachment']);
	/*
		Prilog biljeske START 
		*/
			
			$file_names_s = NULL;
			$files_log_desc = "";
			if($note_attachment == 1){
				$allowed = array('jpg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png', 'csv');
				$file_names = array();
				for($i = 0; $i < count($_FILES['note_attachment_file']['name']); $i++) {
					$tmpFilePath = $_FILES['note_attachment_file']['tmp_name'][$i];
					if ($tmpFilePath != ""){
						$filename = $_FILES['note_attachment_file']['name'][$i];
						$file_ext = explode('.', $filename);
						$file_ext = strtolower(end($file_ext));
						if(in_array($file_ext, $allowed)) {
							$file_name_new = uniqid() . '.' . $file_ext;
							$newFilePath = "files/prilozi_kandidati/" . $file_name_new;
							
							if(move_uploaded_file($tmpFilePath, $newFilePath)) {
								$file_names[] = $file_name_new;
							}
						}
					}
				}
				$file_names_s = implode(",", $file_names);
				$files_log_desc = ". Files: [".$file_names_s."]";
			}
		/*
		Prilog biljeske END
	*/

	$query = $db->prepare("
					INSERT INTO idk_notes
						(note_txt, note_files, note_datetime, note_group, note_dataid, note_employeeid)
					VALUES
						(:note_txt, :note_files, :note_datetime, :note_group, :note_dataid, :note_employeeid)");

	$query->execute(array(
				':note_txt' => $note_txt,
				':note_files' => $file_names_s, 
				':note_datetime' => $note_datetime,
				':note_group' => $note_group,
				':note_dataid' => $note_dataid,
				':note_employeeid' => $logged_employee_id));
				
	$query_kandidati = $db->prepare("
					SELECT kandidat_ime, kandidat_prezime
					FROM idk_kandidati
					WHERE kandidat_id = :kandidat_id");
					
	$query_kandidati->execute(array(
				':kandidat_id' => $note_dataid));
				
	$row = $query_kandidati->fetch();

	$kandidat_ime = $row['kandidat_ime'];
	$kandidat_prezime = $row['kandidat_prezime'];
				
	//Add to LOGS
	$log_desc = "Dodao novu bilješku za kandidata: " .$kandidat_ime. " " .$kandidat_prezime. "".$files_log_desc;
	$log_type = "3";
	addToLogs($log_desc, $log_type); //Log za biljeske - $log_type = 3

	header("Location: kandidati?page=open&id=$note_dataid&mess=1");


break;

case "add_kandidat_projekat":

	$kandidat_id 		= $_POST['kandidat_id'];
	$nalog_id_to_be 	= $_POST['lg_nalog'];
	$project_id_to_be 	= $_POST['lg_project'];
	$glossa_lead 		= $_POST['glossa_kurs'] ?? null;

	$flag_was_reserved 				= false;
	$flag_candidate_has_nalog_id	= false;
	$nalog_id_was 					= "";
	$project_id_was 				= NULL;
	$project_name_to_be				= "";
	$pk_to_delete_array = array();
	$project_id_to_delete_array = array();
	$query_get_candidate_info_was = $db -> prepare('
		SELECT pr.project_nalogid, pr.project_id, pr.project_name, pk.pk_id, kan.kandidat_nalog_id, kan.kandidat_status_prijave
		FROM idk_projects pr 
		JOIN (
			SELECT sqpk.pk_id, sqpk.pk_projectid, sqpk.pk_kandidatid
			FROM idk_project_kandidati sqpk
			WHERE sqpk.pk_kandidatid = :kandidat_id
		) pk
		ON pk.pk_projectid = pr.project_id
		JOIN (
			SELECT sqkan.kandidat_id, sqkan.kandidat_nalog_id, sqkan.kandidat_status_prijave
			FROM idk_kandidati sqkan
			WHERE sqkan.kandidat_id = :kandidat_id
		) kan
		ON kan.kandidat_id = pk.pk_kandidatid
		WHERE pr.project_nalogid = :nalog_id_to_be
	');
	$query_get_candidate_info_was -> execute(array(
		':kandidat_id' 		=> $kandidat_id,
		':nalog_id_to_be' 	=> $nalog_id_to_be
	));
	while($row_get_candidate_info_was = $query_get_candidate_info_was -> fetch()){
		$nalog_id_was 					= $row_get_candidate_info_was['project_nalogid'];
		$project_id_was 				= $row_get_candidate_info_was['project_id'];
		$project_name_was 				= $row_get_candidate_info_was['project_name'];
		$project_candidate_to_delete 	= $row_get_candidate_info_was['pk_id'];
		$kandidat_nalog_id_was 			= $row_get_candidate_info_was['kandidat_nalog_id'];
		$kandidat_status_prijave 		= $row_get_candidate_info_was['kandidat_status_prijave'];
		
		array_push($pk_to_delete_array, $project_candidate_to_delete);
		array_push($project_id_to_delete_array, $project_id_was);
	}

	if(count($pk_to_delete_array) != 0){
		$pk_to_delete = implode(', ', $pk_to_delete_array);
		$project_id_to_delete = implode(', ', $project_id_to_delete_array);
		$query_delete_project_candidates = $db -> prepare("
			DELETE FROM idk_project_kandidati
			WHERE pk_id IN ($pk_to_delete)
		");
		$query_delete_project_candidates -> execute();
		$log_desc = "Obrisao kandidata: " .$kandidat_id. " iz projekata [" .$project_id_to_delete. "].";
		$log_type = "0";
		addToLogs($log_desc, $log_type);
	}
	
	$query = $db->prepare("
					INSERT INTO idk_project_kandidati
						(pk_projectid, pk_kandidatid)
					VALUES
						(:pk_projectid, :pk_kandidatid)");

	$query->execute(array(
				':pk_projectid' => $project_id_to_be,
				':pk_kandidatid' => $kandidat_id));


	//Add to LOGS
	$log_desc = "Vezao kandidata: " .$kandidat_id. " za projekt " .$project_id_to_be. ".";
	$log_type = "0";
	addToLogs($log_desc, $log_type); //Log za biljeske - $log_type = 3

	$status_prijave_to_be = getStatusPrijaveByProjectId($project_id_to_be);
	$query_update_status_prijave = $db -> prepare('
		UPDATE idk_kandidati
		SET kandidat_status_prijave = :status_prijave_to_be
		WHERE kandidat_id = :kandidat_id
	');
	$query_update_status_prijave -> execute(array(
		':status_prijave_to_be' => $status_prijave_to_be,
		':kandidat_id' => $kandidat_id
	));
	
	if(isReservedByProject(getProjectFullName($project_id_to_be))){
		updateNalogIdForCandidat($kandidat_id, $nalog_id_to_be);
	}
	else{
		resetNalogIdForCandidat($kandidat_id);
	}

	if($status_prijave_to_be == 7){
		//Kada se kandidat ručno prebacuje u projekt Ugovor i kada nema partner ID-a i nalog je na PP-u onda ce ovaj slučaj posluziti da se update-a kandidat_ppa_partner_id.
		//Prvenstveno je zamisljeno da se ovo koristi kada hoce da prebace kandidata iz jednog naloga u drugi, ali da su nalozi od iste kompanije.
		//To ce omoguciti da kandidat ostane vidljiv na PP-u na listama Dashboard Personal. Svi podaci o ugovori se mogu samo ručno povratiti iz logova
		
		$query_check_partner_id = $db->prepare("SELECT kandidat_ppa_partner_id FROM idk_kandidati WHERE kandidat_id = $kandidat_id");
		$query_check_partner_id->execute();
		$row_check_partner_id = $query_check_partner_id->fetch();
		if($row_check_partner_id['kandidat_ppa_partner_id'] == null){
			//Ovo može proći samo ako nalog nema partnera,tj jedna kompanija upravlja nalogom
			$query_get_partner_id = $db->prepare("SELECT ppa_id FROM idk_pp_partners WHERE ppa_nalog_id = $nalog_id_to_be");
			$query_get_partner_id->execute();
			if($query_get_partner_id->rowCount() == 1){
				$row_partner_id = $query_get_partner_id->fetch();
				$new_partner_id = $row_partner_id['ppa_id'];
				$query_update_ppa_partner = $db->prepare("
					UPDATE idk_kandidati
					SET kandidat_ppa_partner_id = :kandidat_ppa_partner_id
					WHERE kandidat_id = :kandidat_id
				");
				$query_update_ppa_partner->execute(array(
					':kandidat_id' => $kandidat_id,
					':kandidat_ppa_partner_id' => $new_partner_id
				));
			}
		}
		
	}
	
	addToLogsStatusPrijave($project_id_was, $project_id_to_be, getStatusPrijaveByProjectId($project_id_to_be), $kandidat_id, 1);
	
	$containsPrijava = strpos(getProjectFullName($project_id_to_be), 'Prijave') !== false;

	if($containsPrijava){
		if(reserveAgentAndCandidateOnProjectChange($logged_employee_id, $nalog_id_to_be, $kandidat_id, $project_id_to_be, 1) == true){
			header("Location: kandidati?page=open&id=$kandidat_id&projekt_id=$project_id_to_be&nalog_id=$nalog_id_to_be&vrsta_id=1&mess=15");
		}else{
			header("Location: kandidati?page=open&id=$kandidat_id&mess=15");
		}
	}else{
		header("Location: kandidati?page=open&id=$kandidat_id&mess=15");
	}
break;

case "insert_glossa_lead":
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$kandidat_id   = intval($_POST['kandidat_id']);
	$glossa 	   = $_POST['glossa_kurs'];
	$response_msg  = $_POST['response_msg'];
	$response_code = $_POST['response_code'];


	if($glossa != NULL && $response_code == "S_001")
	{

		$sql = "UPDATE idk_kandidati SET kandidat_glossa = 1 WHERE kandidat_id = ':kandidat_id'";
		// $sql = "SELECT kandidat_id FROM idk_kandidati WHERE kandidat_id = $kandidat_id";

		$stmt = $db->prepare($sql);
		$stmt->execute(array(':kandidat_id' => $kandidat_id));
		// $row = $stmt->fetch(PDO::FETCH_ASSOC);
		// print_r($row);



		//Add to LOGS
		$log_desc = "Kandidat: " .$kandidat_id. " - " . $response_msg;
		$log_type = "13"; //Bridge sistem API
		addToLogs($log_desc, $log_type); //Log za biljeske - $log_type = 3
	}
	else
	{
		//Add to LOGS
		$log_desc = "ERROR: Kandidat: " .$kandidat_id. " - " . $response_msg;
		$log_type = "13"; //Bridge sistem API
		addToLogs($log_desc, $log_type); //Log za biljeske - $log_type = 3
	}
break;

case "add_kandidat_doc":

if(isset($_POST['document_dataid'])){
	$document_dataid = $_POST['document_dataid'];
	$request_location = $_POST['request_location'] ?? null;
	$nalog_id = $_POST['nalog_id'] ?? null; 

	//Upload document
	$document_file = $_FILES['document_file'];

	//File properties
	$file_name = $document_file['name'];
	$file_tmp = $document_file['tmp_name'];

	//File extension
	$file_ext = explode('.', $file_name);
	$file_ext = strtolower(end($file_ext));

	$allowed = array('jpg', 'jpeg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png');

	if(in_array($file_ext, $allowed)) {

		$file_name_new = uniqid() . '.' . $file_ext;
		$file_destination = 'files/kandidati_doc/' . $file_name_new;

		if(move_uploaded_file($file_tmp, $file_destination)){}
	}

	$document_name = $_POST['document_name'];
	$document_desc = $_POST['document_desc'];
	$document_datetime = date('Y-m-d H:i:s');
	$document_group = 2;

	if ($document_name == 'Dokaz o završenoj srednjoj školi') {
		$document_special_type = 2;
	} else if ($document_name == 'Dokazivo radno iskustvo') {
		$document_special_type = 1;
	} else {
		$document_special_type = NULL;
	}

	$query = $db->prepare("
					INSERT INTO idk_documents
						(document_name, document_desc, document_file, document_icon, document_datetime, document_group, document_dataid, document_employeeid, document_special_type)
					VALUES
						(:document_name, :document_desc, :document_file, :document_icon, :document_datetime, :document_group, :document_dataid, :document_employeeid, :document_special_type)");

	$query->execute(array(
					':document_name' => $document_name,
					':document_desc' => $document_desc,
					':document_file' => $file_name_new,
					':document_icon' => $file_ext,
					':document_datetime' => $document_datetime,
					':document_group' => $document_group,
					':document_dataid' => $document_dataid,
					':document_employeeid' => $logged_employee_id, 
					':document_special_type' => $document_special_type
	));

	$log_za_prebacivanje = "";
	if($document_special_type == 1 OR $document_special_type == 2){
		$departure_type = getCandidateDepartureType($document_dataid);
		if($departure_type[0] == 3){
			$resultPrebacivanja = prebaciNaPrikupljanjeDokumentacijeRadnoIskustvo($document_dataid);
			$log_za_prebacivanje = " Status automatskog prebacivanja na prikupljanje dokumentacije (RI): " . $resultPrebacivanja;
		}else{
			$log_za_prebacivanje = " Status automatskog prebacivanja na prikupljanje dokumentacije (RI): Kandidat nije označen da ide preko Radnog iskustva.";
		}
	}

	//Add to Search
	$search_text = "Dokument: " . $document_name . "";
	$search_tags = $document_name . " " . $document_desc;
	$search_link = "files/kandidati_doc/$file_name_new";
	$search_query = $db->prepare("
							INSERT INTO idk_search
								(search_text, search_tags, search_link)
							VALUES
								(:search_text, :search_tags, :search_link)");
	$search_query->execute(array(
						':search_text' => $search_text,
						':search_tags' => $search_tags,
						':search_link' => $search_link));

	//Add to LOGS
	$log_desc = "Dodao novi dokument: " . $document_name . " (".$file_name_new.") " . $log_za_prebacivanje;
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_employeeid, log_desc, log_date)
					VALUES
						(:log_employeeid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_employeeid' => $logged_employee_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));
	if ($request_location != null AND $request_location == "nalozi_open_status_prijave") {
		header("Location: nalozi.php?page=open_status_prijave&sid=9&nid=".$nalog_id."");
	} else {
		header("Location: kandidati?page=open&id=$document_dataid&mess=2");
	}
}
break;

case "edit_kandidat_doc":

	$kandidat_id = $_POST['kandidat_id'];
	$document_id = $_POST['document_id'];
	$document_desc = $_POST['document_desc'];


	// GET DOCUMENT INFO
	$query_doc = $db->prepare("
					SELECT document_id, document_name, document_desc, document_file, document_icon, document_datetime
					FROM idk_documents
					WHERE document_id = :document_id");

	$query_doc->execute(array(
				':document_id' => $document_id
				));

	$row_doc = $query_doc->fetch();
	$document_name = $row_doc['document_name'];

	//Save
	$query = $db->prepare("
					UPDATE idk_documents
					SET	document_desc = :document_desc
					WHERE document_id = :document_id");

	$query->execute(array(
				':document_desc' => $document_desc,
				':document_id' => $document_id));

	$kandidat_ime_prezime = getCandidateFullnameR($kandidat_id);

	//Add to LOGS
	$log_desc = "Uredio opis dokumenta ".$document_name." kandidatu: " . $kandidat_ime_prezime . "";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_employeeid, log_desc, log_date)
					VALUES
						(:log_employeeid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_employeeid' => $logged_employee_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: kandidati?page=open&id=$kandidat_id&mess=12");

break;

case "remove_from_proj":
	$projectid = $_GET['project'];
	$kandidatid = $_GET['id'];

	$query = $db->prepare("
					DELETE FROM idk_project_kandidati
					WHERE pk_projectid = :projectid
					AND pk_kandidatid = :kandidatid");

	$query->execute(array(
					':projectid' => $projectid,
					':kandidatid' => $kandidatid));
	

	
	$ime_projekta = getProjectFullName($projectid);
	$kandidat_ime_prezime = getCandidateFullnameR($kandidatid);

	//Add to LOGS
	$log_desc = "Uklonio kandidata ".$kandidat_ime_prezime." iz projekta ".$ime_projekta."";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_employeeid, log_desc, log_date)
					VALUES
						(:log_employeeid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_employeeid' => $logged_employee_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));	
	
	header("Location: kandidati?page=open&id=$kandidatid&mess=14");

break;

case "add_kandidat":

	$kandidat_ime = $_POST['kandidat_ime'];
	$kandidat_prezime = $_POST['kandidat_prezime'];
	$urlid = $_POST['urlid'];
	$kandidat_datumrodjenja = date("Y-m-d", strtotime($_POST['kandidat_datumrodjenja']));

	//Check if user exist
	$check_query2 = $db->prepare("
							SELECT lg_id, lg_url, lg_nalogid
							FROM idk_link_generator
							WHERE lg_id = :lg_id");

	$check_query2->execute(array(
					':lg_id' => $urlid));

	$num = $check_query2->rowCount();
	$rowlg = $check_query2->fetch();
		
		$lg_nalogid = $rowlg['lg_nalogid'];

	if($num > 0){
		$url_id = $_POST['urlid'];
	}else{
		$url_id = 1;
	}
	

	//napravi username po imenu, prezimenu i godini
	$kandidat_datumrodjenja_username = date("dmY", strtotime($_POST['kandidat_datumrodjenja']));
	$ime_korime = strtolower($kandidat_ime);
	$prezime_korime = strtolower($kandidat_prezime);
	$imeprezime = $ime_korime.''.$prezime_korime;
	$search = array("ć", "č", "ž", "š", "đ");
	$replacement = array("c", "c", "z", "s", "dj");
	$imeprezime_korime = str_replace($search, $replacement, $imeprezime);
	$kandidat_korisnickoime_uf = $imeprezime_korime.''.$kandidat_datumrodjenja_username;
	$kandidat_korisnickoime = str_replace(' ', '', strtolower($kandidat_korisnickoime_uf));



	//Check if user exist
	$check_query = $db->prepare("
							SELECT kandidat_email
							FROM idk_kandidati
							WHERE kandidat_korisnickoime = :kandidat_korisnickoime");

	$check_query->execute(array(
					':kandidat_korisnickoime' => $kandidat_korisnickoime));

	$number_of_rows = $check_query->rowCount();

	if($number_of_rows == 0){


		$kandidat_spol = $_POST['kandidat_spol'];
		$kandidat_djevojackoprezime = $_POST['kandidat_djevojackoprezime'];
		if(!empty($_POST['kandidat_jmbg'])){ $kandidat_jmbg = $_POST['kandidat_jmbg']; }else{ $kandidat_jmbg = 0; }
		$kandidat_brojlk = $_POST['kandidat_brojlk'];

		$kandidat_mjestorodjenja = $_POST['kandidat_mjestorodjenja'];
		$kandidat_drzavarodjenja = $_POST['kandidat_drzavarodjenja'];
		$kandidat_drzavljanstvo = $_POST['kandidat_drzavljanstvo'];
		$kandidat_drzavljanstvo_vrsta = $_POST['kandidat_drzavljanstvo_vrsta'];
		$kandidat_adresa = $_POST['kandidat_adresa'];
		$kandidat_grad = $_POST['kandidat_grad'];
		$kandidat_pbroj = $_POST['kandidat_pbroj'];
		$kandidat_drzava = $_POST['kandidat_drzava'];
		$kandidat_visitedurl = $_POST['kandidat_visitedurl'];
		$kandidat_email = $_POST['kandidat_email'];
		$kandidat_datum_termina = $_POST['kandidat_termin_date'];
		$kandidat_datum_aplikacije = $_POST['kandidat_termin_date_app'];
		$kandidat_group = $_POST['kandidat_prijava_na'];
		$datum_aplikacije = date("Y-m-d", strtotime($kandidat_datum_aplikacije));
		
		
		// AKO nije unio termin za vizu a jeste datum aplikacije - Dodaj 18 mjeseci na datum aplikacije
		if($kandidat_datum_aplikacije != NULL){
			$kandidat_datum_aplikacije = date("Y-m-d", strtotime($kandidat_datum_aplikacije));
			
			if($kandidat_datum_termina == NULL){
				// AUTOMATSKI PROCJENJEN TERMIN - Dodano 20mj na datum apliciranja
				$kandidat_procjenatermina = 1;
				$kandidat_datum_termina = date('Y-m-d', strtotime("+20 months", strtotime($kandidat_datum_aplikacije)));
			}else{
				$kandidat_procjenatermina = 0;
				$kandidat_datum_termina = $_POST['kandidat_termin_date'];
			}
		}else{
			$kandidat_datum_aplikacije = NULL;
			$kandidat_datum_termina = NULL;
		}
			$mjesectermina = date("m",strtotime($kandidat_datum_termina));
			$godinatermina = date("Y",strtotime($kandidat_datum_termina));
		
		
	//	echo "Datum aplikacije: ".$kandidat_datum_aplikacije."<br/>";
	//	echo "Termin za vizu: ".$kandidat_datum_termina."<br/>";
	//	echo "Nalog id: ".$lg_nalogid."<br/>";
	//	echo "Mjesec termina: ".$mjesectermina."<br/>";
		
		$group_title_query = $db->prepare("
						SELECT kg_id, kg_title
						FROM idk_kandidati_grupe
						WHERE kg_id = :kg_id
						");

		$group_title_query->execute(array(
			":kg_id" => $kandidat_group
		));

		$row_kg_title = $group_title_query->fetch();
		$kandidat_prijava_na = $row_kg_title['kg_title'];

		if($_POST['kandidat_vozacka_dozvola'] == ""){
			$kandidat_vozacka_dozvola = "Ne";
		}else{
			$kandidat_vozacka_dozvola = $_POST['kandidat_vozacka_dozvola'];
		}
		
		//$kandidat_password = MD5($_POST['kandidat_password']);
		$kandidat_password = MD5($kandidat_korisnickoime);
		$kandidat_status = 0;
		$kandidat_datetime = date('Y-m-d H:i:s');

		$kandidat_check = md5(uniqid(rand(), true));

		//Upload and save kandidat_slika
		if($_FILES['kandidat_slika']['size'] !== 0) {
			$kandidat_slika = $_FILES['kandidat_slika'];

			//File properties
			$file_name = $kandidat_slika['name'];
			$file_tmp = $kandidat_slika['tmp_name'];
			$file_size = $kandidat_slika['size'];
			$file_error = $kandidat_slika['error'];

			//File extension
			$file_ext = explode('.', $file_name);
			$file_ext = strtolower(end($file_ext));

			$allowed = array('jpg', 'png');

			if(in_array($file_ext, $allowed)) {

				$kandidat_slika_final = uniqid() . '.' . $file_ext;
				$file_destination = 'files/kandidati/' . $kandidat_slika_final;

				if(move_uploaded_file($file_tmp, $file_destination)) {

					$path_to_image_directory = "files/kandidati/";
					$final_width_of_image = 660;

					if(preg_match('/[.](jpg)$/', $kandidat_slika_final)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $kandidat_slika_final);
					} else if (preg_match('/[.](png)$/', $kandidat_slika_final)) {
						$im = imagecreatefrompng($path_to_image_directory . $kandidat_slika_final);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);
					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));
					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);
					imagejpeg($nm, $path_to_image_directory . $kandidat_slika_final);

				}
			}
		}else{
			$kandidat_slika_final = "none";
		}

		//Add user to db
		$query = $db->prepare("
						INSERT INTO idk_kandidati
							(kandidat_check, kandidat_ime, kandidat_prezime, kandidat_spol, kandidat_djevojackoprezime, kandidat_jmbg, kandidat_brojlk, kandidat_mjestorodjenja, kandidat_drzavarodjenja, kandidat_drzavljanstvo_vrsta, kandidat_drzavljanstvo, kandidat_adresa, kandidat_grad, kandidat_pbroj, kandidat_drzava, kandidat_email, kandidat_password, kandidat_slika, kandidat_status, kandidat_datetime, kandidat_korisnickoime, kandidat_datumrodjenja, kandidat_visitedurl, kandidat_vozacka_dozvola, kandidat_prijava_na, datum_termina, datum_aplikacije, kandidat_group, kandidat_procjenatermina)
						VALUES
							(:kandidat_check, :kandidat_ime, :kandidat_prezime, :kandidat_spol, :kandidat_djevojackoprezime, :kandidat_jmbg, :kandidat_brojlk, :kandidat_mjestorodjenja, :kandidat_drzavarodjenja, :kandidat_drzavljanstvo_vrsta, :kandidat_drzavljanstvo, :kandidat_adresa, :kandidat_grad, :kandidat_pbroj, :kandidat_drzava, :kandidat_email, :kandidat_password, :kandidat_slika, :kandidat_status, :kandidat_datetime, :kandidat_korisnickoime, :kandidat_datumrodjenja, :kandidat_visitedurl, :kandidat_vozacka_dozvola, :kandidat_prijava_na, :datum_termina, :datum_aplikacije, :kandidat_group, :kandidat_procjenatermina)");

		$query->execute(array(
					':kandidat_check' => $kandidat_check,
					':kandidat_ime' => $kandidat_ime,
					':kandidat_prezime' => $kandidat_prezime,
					':kandidat_spol' => $kandidat_spol,
					':kandidat_djevojackoprezime' => $kandidat_djevojackoprezime,
					':kandidat_jmbg' => $kandidat_jmbg,
					':kandidat_brojlk' => $kandidat_brojlk,
					':kandidat_mjestorodjenja' => $kandidat_mjestorodjenja,
					':kandidat_drzavarodjenja' => $kandidat_drzavarodjenja,
					':kandidat_drzavljanstvo_vrsta' => $kandidat_drzavljanstvo_vrsta,
					':kandidat_drzavljanstvo' => $kandidat_drzavljanstvo,
					':kandidat_adresa' => $kandidat_adresa,
					':kandidat_grad' => $kandidat_grad,
					':kandidat_pbroj' => $kandidat_pbroj,
					':kandidat_drzava' => $kandidat_drzava,
					':kandidat_email' => $kandidat_email,
					':kandidat_password' => $kandidat_password,
					':kandidat_slika' => $kandidat_slika_final,
					':kandidat_status' => $kandidat_status,
					':kandidat_datetime' => $kandidat_datetime,
					':kandidat_korisnickoime' => $kandidat_korisnickoime,
					':kandidat_datumrodjenja' => $kandidat_datumrodjenja,
					':kandidat_visitedurl' => $url_id,
					':kandidat_vozacka_dozvola' => $kandidat_vozacka_dozvola,
					':kandidat_prijava_na' => $kandidat_prijava_na,
					':datum_termina' => $kandidat_datum_termina,
					':datum_aplikacije' => $kandidat_datum_aplikacije,
					':kandidat_group' => $kandidat_group,
					':kandidat_procjenatermina' => $kandidat_procjenatermina
					));

					//print_r($query = $db->errorInfo());
					//exit();
					
					$kandidat_id = $db->lastInsertId();
					
					
					
					if($kandidat_drzavljanstvo_vrsta == "EU državljanin"){
						$kandidat_drzavljanstvo_vrsta_txt = "EU";
						
						//IF USER IS EU - Transfer him into EU kandidati project within the order
						$nalog_query = $db->prepare("
												SELECT project_id
												FROM idk_projects
												WHERE project_nalogid = :project_nalogid AND project_eukandidati = 1");
					
						$nalog_query->execute(array(
										':project_nalogid' => $lg_nalogid));
					
						$nalogrow = $nalog_query->fetch();
						
						$project_id = $nalogrow['project_id'];
						
							$query_project = $db->prepare("
							INSERT INTO idk_project_kandidati
								(pk_projectid, pk_kandidatid)
							VALUES
								(:pk_projectid, :pk_kandidatid)");

							$query_project->execute(array(
										':pk_projectid' => $project_id,
										':pk_kandidatid' => $kandidat_id));						
						
						
					}else{
						$kandidat_drzavljanstvo_vrsta_txt = "NONEU";
						
						
						//Provjeri da li postoji mjesec termina unutar projekata
						$check_termin_month = $db->prepare("
												SELECT project_id, project_name
												FROM idk_projects
												WHERE project_nalogid = :project_nalogid AND $mjesectermina BETWEEN MONTH(project_datumtermina) AND MONTH(project_datumterminado) AND YEAR(project_datumtermina) = $godinatermina");
					
						$check_termin_month->execute(array(
										':project_nalogid' => $lg_nalogid));
					
						$terminmonthcount = $check_termin_month->rowcount();						
						$terminmonth = $check_termin_month->fetch();	
						$project_id_termin = $terminmonth['project_id'];					
						$project_name = $terminmonth['project_name'];						
						
						if($terminmonthcount == 0){
							
							//IF USER IS NON EU - Transfer him into Prijave project within the order
							$nalog_query = $db->prepare("
													SELECT project_id
													FROM idk_projects
													WHERE project_nalogid = :project_nalogid AND project_name LIKE '%Prijave%'");
						
							$nalog_query->execute(array(
											':project_nalogid' => $lg_nalogid));
						
							$nalogrow = $nalog_query->fetch();
							
							$project_id = $nalogrow['project_id'];	
							
								$query_project = $db->prepare("
								INSERT INTO idk_project_kandidati
									(pk_projectid, pk_kandidatid)
								VALUES
									(:pk_projectid, :pk_kandidatid)");
	
								$query_project->execute(array(
											':pk_projectid' => $project_id,
											':pk_kandidatid' => $kandidat_id));	
							
						}else{
								$query_project = $db->prepare("
								INSERT INTO idk_project_kandidati
									(pk_projectid, pk_kandidatid)
								VALUES
									(:pk_projectid, :pk_kandidatid)");
	
								$query_project->execute(array(
											':pk_projectid' => $project_id_termin,
											':pk_kandidatid' => $kandidat_id));	
						}
						
						
						
					}	
					

					if($lg_nalogid == NULL){
						//Add in Projects
						if(!empty($_POST['lg_project'])){
	
							$projects = $_POST['lg_project'];
	
							foreach($projects as $project_id){
	
								$query_project = $db->prepare("
								INSERT INTO idk_project_kandidati
									(pk_projectid, pk_kandidatid)
								VALUES
									(:pk_projectid, :pk_kandidatid)");
	
								$query_project->execute(array(
											':pk_projectid' => $project_id,
											':pk_kandidatid' => $kandidat_id));
							}
	
						}
					}else{}


						$kki_grupa = 1;
						$kki_naziv = $_POST['kki_naziv'];
						$kki_podatak = $_POST['kki_podatak'];

						//Add kontakt info to db
						$query_mob = $db->prepare("
										INSERT INTO idk_kandidat_kontakt_info
											(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id)
										VALUES
											(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id)");

						$query_mob->execute(array(
									':kki_grupa' => $kki_grupa,
									':kki_naziv' => $kki_naziv,
									':kki_podatak' => $kki_podatak,
									':kki_kandidat_id' => $kandidat_id));

					if(isset($_POST['kandidat_email'])){
						$kki_grupa = 2;
						$kki_naziv = "E-mail";
						$kki_podatak = $_POST['kandidat_email'];

						//Add kontakt info to db
						$query_email = $db->prepare("
										INSERT INTO idk_kandidat_kontakt_info
											(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id)
										VALUES
											(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id)");

						$query_email->execute(array(
									':kki_grupa' => $kki_grupa,
									':kki_naziv' => $kki_naziv,
									':kki_podatak' => $kki_podatak,
									':kki_kandidat_id' => $kandidat_id));
					}


		//Add to Search

		$search_text = "Kandidat: " . $kandidat_ime . " " . $kandidat_prezime . " ";
		$search_tags = $kandidat_ime . " " . $kandidat_prezime . " " . $kandidat_grad . " " . $kandidat_drzavarodjenja . " " . $kandidat_spol;
		$search_link = "kandidati?page=open&id=$kandidat_id";

		$search_query = $db->prepare("
								INSERT INTO idk_search
									(search_text, search_tags, search_link)
								VALUES
									(:search_text, :search_tags, :search_link)");

		$search_query->execute(array(
							':search_text' => $search_text,
							':search_tags' => $search_tags,
							':search_link' => $search_link));



		header("Location: registracija/korak2/$kandidat_id/$kandidat_check");

	}else{
		header("Location: registracija/korak1/poruka/1");
	}

break;

case "add_kandidat_doc_form":

	$document_dataid = $_POST['document_dataid'];
	$kandidat_id = $_POST['kandidat_id'];
	$kandidat_check = $_POST['kandidat_check'];

	//Upload document
	$document_file = $_FILES['document_file'];

	//File properties
	$file_name = $document_file['name'];
	$file_tmp = $document_file['tmp_name'];

	//File extension
	$file_ext = explode('.', $file_name);
	$file_ext = strtolower(end($file_ext));

	$allowed = array('jpg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png');

	if(in_array($file_ext, $allowed)) {

		$file_name_new = uniqid() . '.' . $file_ext;
		$file_destination = 'files/kandidati_doc/' . $file_name_new;

		if(move_uploaded_file($file_tmp, $file_destination)){}
	}

	$document_name = $_POST['document_name'];
	$document_desc = $_POST['document_desc'];
	$document_datetime = date('Y-m-d H:i:s');
	$document_group = 2;

	$query = $db->prepare("
					INSERT INTO idk_documents
						(document_name, document_desc, document_file, document_icon, document_datetime, document_group, document_dataid, document_employeeid)
					VALUES
						(:document_name, :document_desc, :document_file, :document_icon, :document_datetime, :document_group, :document_dataid, :document_employeeid)");

	$query->execute(array(
					':document_name' => $document_name,
					':document_desc' => $document_desc,
					':document_file' => $file_name_new,
					':document_icon' => $file_ext,
					':document_datetime' => $document_datetime,
					':document_group' => $document_group,
					':document_dataid' => $document_dataid,
					':document_employeeid' => 0));

	//Add to Search
	$search_text = "Dokument: " . $document_name . "";
	$search_tags = $document_name . " " . $document_desc;
	$search_link = "files/kandidati_doc/$file_name_new";
	$search_query = $db->prepare("
							INSERT INTO idk_search
								(search_text, search_tags, search_link)
							VALUES
								(:search_text, :search_tags, :search_link)");
	$search_query->execute(array(
						':search_text' => $search_text,
						':search_tags' => $search_tags,
						':search_link' => $search_link));



	if($kandidat_check !== "0")
		header("Location: registracija/korak7/$kandidat_id/$kandidat_check");

break;
/**************************
SKLADISTE MODUL
***************************/
case "add_skladiste_stavka":

	$skl_title = $_POST['skl_title'];
	$skl_desc = $_POST['skl_desc'];
	$skl_available = $_POST['skl_available'];
	$skl_image = $_POST['skl_image'];
	$skl_branch = $_POST['skl_branch'];
	$skl_kategorija = $_POST['skl_category'];
	$skl_datum = $_POST['skl_datum'];

	$skl_datum_nabavke = date("Y-m-d", strtotime($skl_datum));
	
	//Upload and save skl_image
	if($_FILES['skl_image']['size'] !== 0) {
		$skl_image = $_FILES['skl_image'];

		//File properties
		$file_name = $skl_image['name'];
		$file_tmp = $skl_image['tmp_name'];
		$file_size = $skl_image['size'];
		$file_error = $skl_image['error'];

		//File extension
		$file_ext = explode('.', $file_name);
		$file_ext = strtolower(end($file_ext));

		$allowed = array('jpg', 'png');

		if(in_array($file_ext, $allowed)) {

			$skl_image_final = uniqid() . '.' . $file_ext;
			$file_destination = 'images/' . $skl_image_final;

			if(move_uploaded_file($file_tmp, $file_destination)) {

				$path_to_image_directory = "images/";
				$final_width_of_image = 660;

				if(preg_match('/[.](jpg)$/', $skl_image_final)) {
					$im = imagecreatefromjpeg($path_to_image_directory . $skl_image_final);
				} else if (preg_match('/[.](png)$/', $skl_image_final)) {
					$im = imagecreatefrompng($path_to_image_directory . $skl_image_final);
				}

				$ox = imagesx($im);
				$oy = imagesy($im);
				$nx = $final_width_of_image;
				$ny = floor($oy * ($final_width_of_image / $ox));
				$nm = imagecreatetruecolor($nx, $ny);

				imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);
				imagejpeg($nm, $path_to_image_directory . $skl_image_final);

			}
		}
	}else{
		$skl_image_final = "none";
	}	
	
	//Add item to db
	$query = $db->prepare("
					INSERT INTO idk_skladiste
						(skl_title, skl_desc, skl_image, skl_available, skl_branch, skl_kategorija, skl_datum_nabavke)
					VALUES
						(:skl_title, :skl_desc, :skl_image, :skl_available, :skl_branch, :skl_kategorija, :skl_datum_nabavke)");

	$query->execute(array(
				':skl_title' => $skl_title,
				':skl_desc' => $skl_desc,
				':skl_image' => $skl_image_final,
				':skl_available' => $skl_available,
				':skl_branch' => $skl_branch,
				':skl_kategorija' => $skl_kategorija,
				':skl_datum_nabavke' => $skl_datum_nabavke
				));
	
	//Get last ID
	$lid_sklaid = $db->lastInsertId();	
	
	//Add to LOGS
	$log_desc = "Dodao novu stavku u skladište: " . $skl_title . "";
	$log_type = "1";
	addToLogs($log_desc, $log_type); //Log za skladiste - $log_type = 1 
	


		header("Location: warehouse?page=list&mess=1");

break;

case "edit_skladiste_stavku":

	$skl_id = $_POST['skl_id'];
	$skl_title = $_POST['skl_title'];
	$skl_desc = $_POST['skl_desc'];
	$skl_available = $_POST['skl_available'];
	$skl_image = $_POST['skl_image'];
	$skl_branch = $_POST['skl_branch'];
	$skl_category = $_POST['skl_category'];
	$skl_datum = $_POST['skl_datum'];

	$skl_datum_nabavke = date("Y-m-d", strtotime($skl_datum));
	
		//Upload and save skl_image
		if($_FILES['skl_image']['size'] !== 0) {

			//Delete old image
			$del_employee_img_query = $db->prepare("
										SELECT skl_image
										FROM idk_skladiste
										WHERE skl_id = :skl_id");

			$del_employee_img_query->execute(array(
									':skl_id' => $skl_id));

			$del_employee_img = $del_employee_img_query->fetch();

				$skl_image_check = $del_employee_img['skl_image'];

			if($skl_image_check == "" OR $skl_image_check =="none" OR $skl_image_check =="none.jpg"){}else{
				unlink("files/employees/" . $skl_image_check);
			}

			$skl_image = $_FILES['skl_image'];

			//File properties
			$file_name = $skl_image['name'];
			$file_tmp = $skl_image['tmp_name'];
			$file_size = $skl_image['size'];
			$file_error = $skl_image['error'];

			//File extension
			$file_ext = explode('.', $file_name);
			$file_ext = strtolower(end($file_ext));

			$allowed = array('jpg', 'png');

			if(in_array($file_ext, $allowed)) {

				$skl_image_final = uniqid() . '.' . $file_ext;
				$file_destination = 'images/' . $skl_image_final;

				if(move_uploaded_file($file_tmp, $file_destination)) {

					$path_to_image_directory = "images/";
					$final_width_of_image = 660;

					if(preg_match('/[.](jpg)$/', $skl_image_final)) {
						$im = imagecreatefromjpeg($path_to_image_directory . $skl_image_final);
					} else if (preg_match('/[.](png)$/', $skl_image_final)) {
						$im = imagecreatefrompng($path_to_image_directory . $skl_image_final);
					}

					$ox = imagesx($im);
					$oy = imagesy($im);
					$nx = $final_width_of_image;
					$ny = floor($oy * ($final_width_of_image / $ox));
					$nm = imagecreatetruecolor($nx, $ny);

					imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);
					imagejpeg($nm, $path_to_image_directory . $skl_image_final);

				}
			}
		}else{
			$skl_image_final = $_POST['skl_image_url'];
		}


			$query = $db->prepare("
							UPDATE idk_skladiste
							SET	skl_title = :skl_title, skl_desc = :skl_desc, skl_available = :skl_available, skl_image = :skl_image, skl_branch = :skl_branch, skl_kategorija = :skl_category, skl_datum_nabavke = :skl_datum
							WHERE skl_id = :skl_id");

			$query->execute(array(
					':skl_title' => $skl_title,
					':skl_desc' => $skl_desc,
					':skl_available' => $skl_available,
					':skl_image' => $skl_image_final,
					':skl_branch' => $skl_branch,
					':skl_id' => $skl_id,
					':skl_category' => $skl_category,
					':skl_datum' => $skl_datum_nabavke
					));
	
	//Add to LOGS
	$log_desc = "Uredio stavku u skladištu: " . $skl_title . "";
	$log_type = "1";
	addToLogs($log_desc, $log_type); //Log za skladiste - $log_type = 1 
	


		header("Location: warehouse?page=list&mess=3");

break;

case "skladiste_zaduzi_stavku":
	
	$skl_id = $_POST['skl_id'];
	$sz_employeeid = $_POST['sz_employeeid'];
	$skl_title = $_POST['skl_title'];
	$sz_opis = $_POST['sz_opis'];
	$productid = $_POST['productid'];
	$serijski_broj_d = $_POST['sbroj'];
	
	if($sz_employeeid != 0){
	
		// GET EMPLOYEE INFORMATION
		$get_employee_information = $db->prepare("
									SELECT employee_firstname, employee_lastname
									FROM idk_employees
									WHERE employee_id = :employee_id");

		$get_employee_information->execute(array(
								':employee_id' => $sz_employeeid));

		$employee_information = $get_employee_information->fetch();

			$employee_firstname = $employee_information['employee_firstname'];	
			$employee_lastname = $employee_information['employee_lastname'];	
		

		$insert_query = $db->prepare("
						INSERT INTO idk_skladiste_zaposlenici
							(isz_sklid, sz_employeeid, sz_quantity, sz_opis, isz_status, isz_product_id)
							VALUES
							(:isz_sklid, :sz_employeeid, :sz_quantity, :sz_opis, :isz_status, :isz_product_id)");
		$insert_query->execute(array(
						':isz_sklid' => $skl_id,
						':sz_employeeid' => $sz_employeeid,
						':sz_quantity' => 1,
						':sz_opis' => $sz_opis,
						':isz_status' => 2,
						':isz_product_id' => $productid));

		
		//Add to LOGS
		$log_desc = "Zadužio stavku ".$skl_title."(serijski broj: ".$serijski_broj_d.") zaposleniku: " . $employee_firstname . " " . $employee_lastname . "";
		$log_type = "1";
		addToLogs($log_desc, $log_type); //Log za skladiste - $log_type = 1 
		header("Location: warehouse?page=open&id=$skl_id&mess=1");
	
	}else{
		header("Location: warehouse?page=open&id=$skl_id&mess=3");
	}
	
break;

case "skladiste_primi_stavku":

	$sz_id = $_GET['sz_id'];
	$skl_title = $_GET['skl_title'];
	
	// GET EMPLOYEE INFORMATION
	$get_employee_information = $db->prepare("
								SELECT employee_id, employee_firstname, employee_lastname
								FROM idk_employees
								WHERE employee_id = :employee_id");

	$get_employee_information->execute(array(
							':employee_id' => $logged_employee_id));

	$employee_information = $get_employee_information->fetch();

	$employee_firstname = $employee_information['employee_firstname'];
	$employee_lastname = $employee_information['employee_lastname'];
	$employee_id = $employee_information['employee_id'];


	$update_query = $db->prepare("
					UPDATE idk_skladiste_zaposlenici
					SET isz_status = :isz_status
					WHERE isz_id = :isz_id");
	$update_query->execute(array(
					':isz_id' => $sz_id,
					':isz_status' => 1 ));


	//Add to LOGS
	$log_desc = "Zaposlenik ".$employee_firstname." ".$employee_lastname." primio stavku: " . $skl_title . "";
	$log_type = "1";
	addToLogs($log_desc, $log_type); //Log za skladiste - $log_type = 1 */
	header("Location: employees?page=open&id=$employee_id");


break;

case "skladiste_odbij_stavku":

	$sz_id = $_GET['sz_id'];
	$skl_title = $_GET['skl_title'];
	$razlog = $_GET['razlog'];


	// GET EMPLOYEE INFORMATION
	$get_employee_information = $db->prepare("
								SELECT employee_id, employee_firstname, employee_lastname
								FROM idk_employees
								WHERE employee_id = :employee_id");

	$get_employee_information->execute(array(
							':employee_id' => $logged_employee_id));

	$employee_information = $get_employee_information->fetch();

	$employee_firstname = $employee_information['employee_firstname'];
	$employee_lastname = $employee_information['employee_lastname'];
	$employee_fullname = $employee_firstname." ".$employee_lastname;
	$employee_id = $employee_information['employee_id'];


	$update_query = $db->prepare("
					UPDATE idk_skladiste_zaposlenici
					SET isz_status = :isz_status
					WHERE isz_id = :isz_id");
	$update_query->execute(array(
					':isz_id' => $sz_id,
					':isz_status' => 3 ));
	
	//GET PRODUCT ID
	$get_pid = $db->prepare("
					SELECT isz_product_id
					FROM idk_skladiste_zaposlenici
					WHERE isz_id = :isz_id");

	$get_pid->execute(array(
					':isz_id' => $sz_id));
					
	$product_id_row = $get_pid->fetch();
	$product_id = $product_id_row['isz_product_id'];	
					
	// STARI RAZLOZI ODBIJANJA
	$get_razloge = $db->prepare("
					SELECT isp_razlozi_odbijanja
					FROM idk_skladiste_product
					WHERE isp_id = :isp_id");

	$get_razloge->execute(array(
					':isp_id' => $product_id));
	
	$razlozi_row = $get_razloge->fetch();
	$stari_razlozi = $razlozi_row['isp_razlozi_odbijanja'];	
	
	$novi_razlozi = $stari_razlozi."<br/>".$razlog." (Odbio: ".$employee_fullname.")";
	
	$update_query = $db->prepare("
					UPDATE idk_skladiste_product
					SET isp_razlozi_odbijanja = :isp_razlozi_odbijanja
					WHERE isp_id = :isp_id");
	$update_query->execute(array(
					':isp_id' => $product_id,
					':isp_razlozi_odbijanja' => $novi_razlozi));



	//Add to LOGS
	$log_desc = "Zaposlenik ".$employee_fullname." odbio stavku: " . $skl_title . ". Razlog: ". $razlog;
	$log_type = "1";
	addToLogs($log_desc, $log_type); //Log za skladiste - $log_type = 1 */
	header("Location: employees?page=open&id=$employee_id");


break;

case "skladiste_edit_product":
	
	$skp_id = $_POST['idproduct'];
	$skp_serijski_broj = $_POST['serbroj'];
	$skp_dodatni_info = $_POST['dodinfo'];
	$skl_title = $_POST['skl_title'];
	$skl_id = $_POST['skl_id'];
	$skp_inventarno_e = $_POST['skp_inventarno_e'];
	
	$update_query = $db->prepare("
					UPDATE idk_skladiste_product
					SET isp_serijski_broj = :isp_serijski_broj, isp_dodatni_info = :isp_dodatni_info, isp_inventar = :isp_inventar
					WHERE isp_id = :isp_id");
	$update_query->execute(array(
					':isp_id' => $skp_id,
					':isp_serijski_broj' => $skp_serijski_broj,
					':isp_inventar' => $skp_inventarno_e,
					':isp_dodatni_info' => $skp_dodatni_info));
	
	
	
	//Add to LOGS
	$log_desc = "Uredio stavku: " . $skl_title . " (serijski broj: " . $skp_serijski_broj . ").";
	$log_type = "1";
	addToLogs($log_desc, $log_type); //Log za skladiste - $log_type = 1 */
	header("Location: warehouse?page=open&id=".$skl_id."&mess=5");
	
break;

case "add_skladiste_artikl":
	
	$skl_id = $_POST['skl_id'];
	$isp_serijski_broj = $_POST['skp_serijski_broj'];
	$skp_inventarno = $_POST['skp_inventarno'];
	$skp_info = $_POST['skp_info'];
	
	$insert_query = $db->prepare("
					INSERT INTO idk_skladiste_product
						(isp_skl_id, isp_serijski_broj, isp_dodatni_info, isp_inventar)
						VALUES
						(:isp_skl_id, :isp_serijski_broj, :isp_dodatni_info, :isp_inventar)");
	$insert_query->execute(array(
					':isp_skl_id' => $skl_id,
					':isp_serijski_broj' => $isp_serijski_broj,
					':isp_dodatni_info' => $skp_info,
					':isp_inventar' => $skp_inventarno));
	
	//Add to LOGS
	$log_desc = "Dodao artikl ".$skp_serijski_broj.".";
	$log_type = "1";
	addToLogs($log_desc, $log_type); //Log za skladiste - $log_type = 1

	header("Location: warehouse?page=open&id=".$skl_id."&mess=4");
break;

case "add_branch":

	$branch_name = $_POST['branch_name'];
	$branch_state = $_POST['branch_state'];
	$branch_city = $_POST['branch_city'];
	
	$insert_query = $db->prepare("
					INSERT INTO idk_poslovnice
						(branch_name, branch_state, branch_city)
						VALUES
						(:branch_name, :branch_state, :branch_city)");
	$insert_query->execute(array(
					':branch_name' => $branch_name,
					':branch_state' => $branch_state,
					':branch_city' => $branch_city));

	
	//Add to LOGS
	$log_desc = "Dodao poslovnicu ".$branch_name.".";
	$log_type = "1";
	addToLogs($log_desc, $log_type); //Log za skladiste - $log_type = 1 
		
	header("Location: warehouse?page=list&mess=5");
	
break;

case "edit_branch":

	$branch_id = $_POST['branch_id'];
	$branch_name = $_POST['branch_name'];
	$branch_state = $_POST['branch_state'];
	$branch_city = $_POST['branch_city'];
	
	$insert_query = $db->prepare("
					UPDATE idk_poslovnice
					SET branch_name = :branch_name, branch_state = :branch_state, branch_city = :branch_city
					WHERE branch_id = :branch_id");
	$insert_query->execute(array(
					':branch_name' => $branch_name,
					':branch_state' => $branch_state,
					':branch_city' => $branch_city,
					':branch_id' => $branch_id
					));

	
	//Add to LOGS
	$log_desc = "Uredio poslovnicu ".$branch_name.".";
	$log_type = "1";
	addToLogs($log_desc, $log_type); //Log za skladiste - $log_type = 1 
		
	header("Location: warehouse?page=list&mess=6");
	
break;

case "add_category":

	$category_name = $_POST['category_name'];
	
	$check_query = $db->prepare("
					SELECT isk_id
					FROM idk_skladiste_kategorije
					WHERE isk_naziv_kategorije = :isk_naziv_kategorije");
					
	$check_query->execute(array(
					':isk_naziv_kategorije' => $category_name));
					
	
	if($check_query->rowCount() == 0){
		
		$insert_query = $db->prepare("
						INSERT INTO idk_skladiste_kategorije
							(isk_naziv_kategorije)
							VALUES
							(:category_name)");
		$insert_query->execute(array(
						':category_name' => $category_name));


		//Add to LOGS
		$log_desc = "Dodao kategoriju ".$category_name.".";
		$log_type = "1";
		addToLogs($log_desc, $log_type); //Log za skladiste - $log_type = 1

		header("Location: warehouse?page=list&mess=7");
	}else{
		header("Location: warehouse?page=list&mess=9");
	}

break;

case "edit_category":

	$skk_id = $_POST['isk_id'];
	$cat_name = $_POST['cat_name'];
	
	$insert_query = $db->prepare("
					UPDATE idk_skladiste_kategorije
					SET isk_naziv_kategorije = :cat_name
					WHERE isk_id = :isk_id");
	$insert_query->execute(array(
					':isk_id' => $skk_id,
					':cat_name' => $cat_name
					));


	//Add to LOGS
	$log_desc = "Uredio kategoriju ".$cat_name.".";
	$log_type = "1";
	addToLogs($log_desc, $log_type); //Log za skladiste - $log_type = 1

	header("Location: warehouse?page=list&mess=8");

break;


case "add_positions":

	$kp_ime_de = $_POST['kp_ime_de'];
	$kp_ime_en = $_POST['kp_ime_en'];
	
	//Check if position exists in german
	$check_query_de = $db->prepare("
	SELECT kp_ime_de
	FROM idk_kandidat_pozicija
	WHERE kp_ime_de = :kp_ime_de");
	
	$check_query_de->execute(array(
		':kp_ime_de' => $kp_ime_de));


	$number_of_rows_de = $check_query_de->rowCount();

	//Check if position exists in german
	$check_query_en = $db->prepare("
	SELECT kp_ime_en
	FROM idk_kandidat_pozicija
	WHERE kp_ime_en = :kp_ime_en");
	
	$check_query_en->execute(array(
		':kp_ime_en' => $kp_ime_en));


	$number_of_rows_en = $check_query_en->rowCount();
		
	if($number_of_rows_de == 0 AND $number_of_rows_en == 0){
			
		$kp_ime = ($_POST['kp_ime'] == "") ? NULL : $_POST['kp_ime'];
		$kp_ime_rs = ($_POST['kp_ime_rs'] == "") ? NULL : $_POST['kp_ime_rs'];
		$kp_opis = ($_POST['kp_opis'] == "") ? NULL : $_POST['kp_opis'];


		//Add position to db
		$query = $db->prepare("
						INSERT INTO idk_kandidat_pozicija
							(kp_ime, kp_ime_de,kp_ime_en,kp_ime_rs,kp_created_by ,kp_opis)
						VALUES
							(:kp_ime, :kp_ime_de,:kp_ime_en,:kp_ime_rs,:kp_created_by, :kp_opis)");

		$query->execute(array(
					':kp_ime' => $kp_ime,
					':kp_ime_de' => $kp_ime_de,
					':kp_ime_en' => $kp_ime_en,
					':kp_ime_rs' => $kp_ime_rs,
					':kp_created_by' => $logged_employee_id,
					':kp_opis' => $kp_opis));

		//Get last ID
		$kp_positionid = $db->lastInsertId();


		//Add to LOGS
		$log_desc = "Dodao novu poziciju: " . $kp_ime_de . "(njemacki), ".$kp_ime_en."(engleski)";
		$log_date = date('Y-m-d H:i:s');

			$log_query = $db->prepare("
							INSERT INTO idk_logs
								(log_employeeid, log_desc, log_date)
								VALUES
								(:log_employeeid, :log_desc, :log_date)");
			$log_query->execute(array(
							':log_employeeid' => $logged_employee_id,
							':log_desc' => $log_desc,
							':log_date' => $log_date));

		//Add to LOGS
		$log_desc = "Dodao novu poziciju: " . $kp_ime . " ";
		$log_type = "2";
		addToLogs($log_desc, $log_type); //Log za pozicije - $log_type = 2 

		header("Location: positions?page=list&mess=1");

	}else{
		header("Location: positions?page=list&mess=2");
	}

break;


case "edit_positions":

	$kp_id = $_POST['kp_id'];
	$kp_ime = $_POST['kp_ime'];
	$kp_ime_de = $_POST['kp_ime_de'];
	$kp_ime_en = $_POST['kp_ime_en'];
	$kp_ime_rs = $_POST['kp_ime_rs'];
	$kp_opis = $_POST['kp_opis'];
	

		$query = $db->prepare("
						UPDATE idk_kandidat_pozicija
						SET	kp_ime = :kp_ime, kp_ime_de = :kp_ime_de, kp_opis = :kp_opis,kp_ime_en =:kp_ime_en,kp_ime_rs=:kp_ime_rs
						WHERE kp_id = :kp_id");

		$query->execute(array(
				':kp_ime' => $kp_ime,
				':kp_ime_de' => $kp_ime_de,
				':kp_ime_en' => $kp_ime_en,
				':kp_ime_rs' => $kp_ime_rs,
				':kp_opis' => $kp_opis,
				':kp_id' => $kp_id));

	//Add to LOGS
	$log_desc = "Uredio poziciju: " . $kp_ime . " ";
	$log_type = "2";
	addToLogs($log_desc, $log_type); //Log za pozicije - $log_type = 2 
	
	header("Location: positions?page=list&mess=3");


break;

case "archive_position":

	$kp_id = $_GET['kp_id'];
	

		$query = $db->prepare("
						UPDATE idk_kandidat_pozicija
						SET	kp_active = :kp_active
						WHERE kp_id = :kp_id");

		$query->execute(array(
				':kp_active' => 0,
				':kp_id'=>$kp_id));

	//Add to LOGS
	$log_desc = "Arhivirao poziciju: " . $kp_id . " ";
	$log_type = "2";
	addToLogs($log_desc, $log_type); //Log za pozicije - $log_type = 2 
	
	header("Location: positions?page=list&mess=3");


break;

case "cv_profil_gen_de":

$kandidat_id = $_GET['kand_id'];

$query = $db->prepare("
				SELECT kandidat_ime, kandidat_prezime, kandidat_datumrodjenja, kandidat_mjestorodjenja, kandidat_adresa, kandidat_pbroj, kandidat_grad, kandidat_drzava, kandidat_slika, cv_de, profile_de, kde_kandidat_id, kde_prijava_na, kde_spol, kde_drzavljanstvo, kde_vozacka, kandidat_procjenatermina, datum_termina, datum_aplikacije
				FROM idk_kandidati
				INNER JOIN idk_kandidat_de ON idk_kandidati.kandidat_id = idk_kandidat_de.kde_kandidat_id
				WHERE kandidat_id = :kandidat_id");

$query->execute(array(
			':kandidat_id' => $kandidat_id));

$row = $query->fetch();

$kandidat_ime = $row['kandidat_ime'];
$kandidat_prezime = $row['kandidat_prezime'];
$kandidat_spol = $row['kde_spol'];
$kandidat_drzavljanstvo = $row['kde_drzavljanstvo'];
$kandidat_prijava_na = $row['kde_prijava_na'];
$kandidat_vozacka_dozvola = $row['kde_vozacka'];
$kandidat_datumrodjenja = date('d.m.Y.', strtotime($row['kandidat_datumrodjenja']));
$kandidat_datum_aplikacije = date('d.m.Y', strtotime($row['datum_aplikacije']));

$kandidat_procjenatermina = $row['kandidat_procjenatermina'];
$kandidat_datum_termina = date('d.m.Y', strtotime($row['datum_termina']));

if($row['datum_termina'] !=NULL){
	if($kandidat_procjenatermina == 0){
		$kandidat_datum_termina_txt = "<li>Entgültiger Termin bei der Deutschen Botschaft: ".$kandidat_datum_termina."</li>";
	}else{
		$kandidat_datum_termina_txt = "<li>Terminanfrage bei der Deutschen Botschaft: ".$kandidat_datum_aplikacije."</li>";
		
	}
}else{
	$kandidat_datum_termina_txt = "";
}

$kandidat_adresa = $row['kandidat_adresa'];
$kandidat_pbroj = $row['kandidat_pbroj'];
$kandidat_grad = $row['kandidat_grad'];

$cv_de = $row['cv_de'];
$profile_de = $row['profile_de'];

if($row['kandidat_slika'] == "none"){
	$kandidat_slika = "none.jpg";
}else{
	$kandidat_slika = $row['kandidat_slika'];
}


//KONTAKTI
$select_query = $db->prepare("
					SELECT kki_id, kki_grupa, kki_naziv_de, kki_podatak
					FROM idk_kandidat_kontakt_info
					WHERE kki_kandidat_id = :kki_kandidat_id");

$select_query->execute(array(
				':kki_kandidat_id' => $kandidat_id));

while($select_row = $select_query->fetch()) {

	$kki_id = $select_row['kki_id'];
	$kki_grupa = $select_row['kki_grupa'];
	if($kki_grupa == 1){
		$kki_grupa = "Telefon";
		$ikona = 'fa-mobile';
	}elseif($kki_grupa == 2){
		$kki_grupa = "E-mail";
		$ikona = 'fa-envelope';
	}elseif($kki_grupa == 3){
		$kki_grupa = "Web";
		$ikona = 'fa-globe';
	}elseif($kki_grupa == 4){
		$kki_grupa = "Messaging";
		$ikona = 'fa-skype';
	}else{};
	$kki_naziv = $select_row['kki_naziv_de'];
	$kki_podatak = $select_row['kki_podatak'];
	
	if($kki_grupa == "Telefon"){
		$kki_naziv = "Telefonnummer";
	}elseif($kki_grupa == "E-mail"){
		$kki_naziv = "E-mail";
	}else{
		$kki_naziv = $kki_naziv;
	}

	$kandidat_kontakti[] = "
		<li>$kki_naziv: $kki_podatak</li>
	";
}

//IKSUSTVO
$select_query_iskustvo = $db->prepare("
						SELECT kri_id, kri_darum_od, kri_datum_do, kri_pozicija_de, kri_naziv, kri_naziv_de, kri_grad, kri_opis_de, kri_aktuelno
						FROM idk_kandidat_radno_iskustvo
						WHERE kri_kandidat_id = :kri_kandidat_id
						ORDER BY kri_darum_od DESC
						");

$select_query_iskustvo->execute(array(
				':kri_kandidat_id' => $kandidat_id));

	while($select_row = $select_query_iskustvo->fetch()) {

		$kri_id = $select_row['kri_id'];
		$kri_darum_od = date('m.Y', strtotime($select_row['kri_darum_od']));
		$kri_datum_do = $select_row['kri_datum_do'];
		$kri_pozicija = $select_row['kri_pozicija_de'];
		$kri_naziv = $select_row['kri_naziv'];
		$kri_naziv_de = $select_row['kri_naziv_de'];
		$kri_grad = $select_row['kri_grad'];
		$kri_opis = $select_row['kri_opis_de'];
		$kri_aktuelno = $select_row['kri_aktuelno'];
		
		if($kri_aktuelno != 1 && $kri_datum_do != null){
			$kri_datum_do_f = date('m.Y', strtotime($kri_datum_do));
		}else{
			$kri_datum_do_f = "Aktuell";
		}						

$kandidat_iskustvo[] = "


<article> 
<h2>$kri_pozicija</h2>
<p class='subDetails'>$kri_darum_od - $kri_datum_do_f</p>
<p>$kri_naziv_de<br/>
		$kri_grad<br/>
		$kri_opis<br/>
		<p>
</article>

";
}

//OBRAZOVANJE
$select_query_obrazovanje = $db->prepare("
					SELECT ke_id, ke_datumod, ke_datumdo, ss_naziv_de, ke_naziv_de, ke_grad, ke_opis_de, ke_aktuelno
					FROM idk_kandidat_edukacija JOIN idk_skole_smjerovi ss ON ke_smjer_id = ss.ss_id
					WHERE ke_kandidat_id = :ke_kandidat_id");

$select_query_obrazovanje->execute(array(
				':ke_kandidat_id' => $kandidat_id));

while($select_row = $select_query_obrazovanje->fetch()) {

	$ke_id = $select_row['ke_id'];
	$ke_datumod = date('m.Y', strtotime($select_row['ke_datumod']));
	$ke_datumdo = $select_row['ke_datumdo'];
	$ke_naziv_kvalifikacije = $select_row['ss_naziv_de'];
	$ke_naziv = $select_row['ke_naziv_de'];
	$ke_grad = $select_row['ke_grad'];
	$ke_opis = $select_row['ke_opis_de'];
	$ke_aktuelno = $select_row['ke_aktuelno'];
	
	if($ke_aktuelno != 1){
		$ke_datumdo_f = date('m.Y', strtotime($ke_datumdo));
	}else{
		$ke_datumdo_f = "Aktuell";
	}	

	$kandidat_obrazovanje[] = "

	<article> 
		<h2>$ke_naziv_kvalifikacije</h2>
		<p class='subDetails'>$ke_datumod - $ke_datumdo_f</p>
		<p>
			$ke_naziv<br/>
			$ke_opis<br/>
		<p>
	</article>
	";
}


//VJEŠTINE

$select_query_vjestine = $db->prepare("
				SELECT kj_id, kj_naziv, kj_naziv_de, kj_slusanje, kj_citanje_de, kj_govorna_interakcija_de, kj_govorna_produkcija_de, kj_pisanje_de
				FROM idk_kandidat_jezici
				WHERE kj_kandidatid = :kj_kandidatid");

$select_query_vjestine->execute(array(
			':kj_kandidatid' => $kandidat_id));

while($select_row = $select_query_vjestine->fetch()) {

	$kj_id = $select_row['kj_id'];
	$kj_naziv = $select_row['kj_naziv_de'];
	$kj_naziv_bos = $select_row['kj_naziv'];
	$kj_slusanje = $select_row['kj_slusanje'];
	$kj_citanje = $select_row['kj_citanje_de'];
	$kj_govorna_interakcija = $select_row['kj_govorna_interakcija_de'];
	$kj_govorna_produkcija = $select_row['kj_govorna_produkcija_de'];
	$kj_pisanje = $select_row['kj_pisanje_de'];
	
	if(is_null($kj_naziv) AND $kj_naziv_bos == "Njemački"){
		$kj_naziv = "Deutsch";
	}
	
	$kandidati_jezici[] = "
		<tr>
			<td> $kj_naziv</td>
			<td> $kj_slusanje </td>
			<td> $kj_slusanje </td>
			<td> $kj_slusanje </td>
			<td> $kj_slusanje </td>
			<td> $kj_slusanje </td>
		</tr>
	";
}


$select_query_vjestine2 = $db->prepare("
					SELECT kv_id, kv_naziv_de, kv_grupa, kv_opis_de
					FROM idk_kandidat_vjestine
					WHERE kv_kandidat_id = :kv_kandidat_id");

$select_query_vjestine2->execute(array(
				':kv_kandidat_id' => $kandidat_id));

while($select_row = $select_query_vjestine2->fetch()) {

	$kv_id = $select_row['kv_id'];
	$kv_naziv = $select_row['kv_naziv_de'];
	$kv_grupa = $select_row['kv_grupa'];
	if($kv_grupa == 1){
		$kv_grupa = "Grundlegende Fähigkeiten";
	}elseif($kv_grupa == 2){
		$kv_grupa = "Digitale Fähigkeiten";
	}elseif($kv_grupa == 3){
		$kv_grupa = "Zusätzliche Informationen:";
	}else{};
	$kv_opis = $select_row['kv_opis_de'];

	$dodatne_vjestine[] = " 
	<article> 
		<h2>$kv_grupa</h2>
		<p>$kv_naziv<br/>
			$kv_opis<br/>
		<p>
	</article>
	";
}


$dompdf = new Dompdf();
$html = "
	<head>
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
		<style>
		
			@page { margin-top: 40px!important; margin-bottom: 40px!important; }
			
			html,body,div,span,object,iframe,h1,h2,h3,h4,h5,h6,p,blockquote,pre,abbr,address,cite,code,del,dfn,em,img,ins,kbd,q,samp,small,strong,sub,sup,var,b,i,dl,dt,dd,ol,ul,li,fieldset,form,label,legend,table,caption,tbody,tfoot,thead,tr,th,td,article,aside,canvas,details,figcaption,figure,footer,header,hgroup,menu,nav,section,summary,time,mark,audio,video {
				border:0;
				font:inherit;
				margin:0;
				padding:0;
				vertical-align:baseline;
				font-family: 'DejaVu Sans', sans-serif !important;
				}
				
				article,aside,details,figcaption,figure,footer,header,hgroup,menu,nav,section {
					display:block;
				}
				
				.clear {clear: both;}
				
				p {
					font-size: 14px;
					line-height: 1.4em;
					margin-bottom: 20px;
					color: #444;
				}
				
				#cv {
					background: #fff;
				}
				
				.mainDetails {
					padding: 40px 35px;
					border-bottom: 2px solid #3d7b85;
					background: #ededed;					
					margin-top: -40px;
				}
				
				#name h1 {
					font-size: 2em;
					font-weight: 700;
					margin-bottom: -6px;
				}
				
				#name h2 {
					font-size: 1.3em;
					margin-top: 5px;
					margin-left: 2px;
				}

				#name h3 {
					font-size: 1em;
					margin-top: 5px;
					margin-left: 2px;
				}
				
				#mainArea {
					padding: 0 40px;
				}
				
				#headshot {
					width: 150px;
					float: left;
					margin-right: 30px;
				}
				
				#headshot img {
					width: 100%;
					height: auto;
				}
				
				#name {
					float: left;
					margin-left: 20px;
				}
				
				#contactDetails {
					float: right;
				}
				
				#contactDetails ul {
					list-style-type: none;
					font-size: 0.9em;
					margin-top: 2px;
				}
				
				#contactDetails ul li {
					margin-bottom: 3px;
					color: #444;
				}
				
				#contactDetails ul li a, a[href^=tel] {
					color: #444; 
					text-decoration: none;
					-webkit-transition: all .3s ease-in;
					-moz-transition: all .3s ease-in;
					-o-transition: all .3s ease-in;
					-ms-transition: all .3s ease-in;
					transition: all .3s ease-in;
				}
				
				#contactDetails ul li a:hover { 
					color: #cf8a05;
				}
				
				
				section {
					border-top: 1px solid #dedede;
					padding: 20px 0 0;
				}
				
				section:first-child {
					border-top: 0;
				}
				
				section:last-child {
					padding: 20px 0 10px;
				}
				
				.sectionTitle {
					float: left;
					width: 25%;
				}
				
				.sectionContent {
					float: right;
					width: 72.5%;
				}

				.sectionContent ul{
					list-style-type: none;
					color: #333;
					margin-bottom: 20px;
				}

				.sectionContent li{
					margin: 5px 0;
					font-size: 14px;
				}
				
				.sectionTitle h1 {
					
					font-style: italic;
					font-size: 20px;
					color: #3d7b85;
				}
				
				.sectionTitle.headerTitle h1 {
					
					font-style: italic;
					font-size: 16px;
					color: #3d7b85;
				}
				
				.sectionContent h2 {					
					font-size: 1.5em;
					margin-bottom: -2px;
				}
				
				.subDetails {
					font-size: 0.8em;
					font-style: italic;
					margin-bottom: 3px;
				}
				
				.keySkills {
					list-style-type: none;
					-moz-column-count:3;
					-webkit-column-count:3;
					column-count:3;
					margin-bottom: 20px;
					font-size: 1em;
					color: #444;
				}
				
				.keySkills ul li {
					margin-bottom: 3px;
				}
				
				@media all and (min-width: 602px) and (max-width: 800px) {
					#headshot {
						display: none;
					}
					
					.keySkills {
					-moz-column-count:2;
					-webkit-column-count:2;
					column-count:2;
					}
				}
				
				@media all and (max-width: 601px) {
					#cv {
						width: 95%;
						margin: 10px auto;
						min-width: 280px;
					}
					
					#headshot {
						display: none;
					}
					
					#name, #contactDetails {
						float: none;
						width: 100%;
						text-align: center;
					}
					
					.sectionTitle, .sectionContent {
						float: none;
						width: 100%;
					}
					
					.sectionTitle {
						margin-left: -2px;
						font-size: 1.25em;
					}
					
					.keySkills {
						-moz-column-count:2;
						-webkit-column-count:2;
						column-count:2;
					}
				}
				
				@media all and (max-width: 480px) {
					.mainDetails {
						padding: 15px 15px;
					}
					
					section {
						padding: 15px 0 0;
					}
					
					#mainArea {
						padding: 0 25px;
					}
				
					
					.keySkills {
					-moz-column-count:1;
					-webkit-column-count:1;
					column-count:1;
					}
					
					#name h1 {
						line-height: .8em;
						margin-bottom: 4px;
					}
				}
				
				@media print {
					#cv {
						width: 100%;
					}
				}
		</style>
	<title>CV - $kandidat_ime $kandidat_prezime</title>
	</head>


	<body id='top'>
	<div id='cv' class='instaFade'>
		<div class='mainDetails'>
			<div id='headshot' class='quickFade'>
			<img src='".getsiteurl()."images/Jobstep-logo_news.png'/>
			</div>
			
			<div id='name'>
				<h1 class='quickFade delayTwo'>$kandidat_ime $kandidat_prezime</h1>
				<!--<h2 class='quickFade delayThree'>Angestrebte Stelle:</h2>
				<h3 class='quickFade delayThree'>$kandidat_prijava_na</h3>-->
			</div>
			
			<div id='contactDetails' class='quickFade delayFour'>
				<img src='".getsiteurl()."files/kandidati/$kandidat_slika' width=auto; height=100;/>
			</div>
			<div class='clear'></div>
			
			<div class='sectionTitle headerTitle'>
				<h1>Angestrebte Stelle:</h1>
			</div>
			
			<div class='sectionContent'>
				<h3 class='quickFade delayThree'>$kandidat_prijava_na</h3>
			</div>			
			<div class='clear'></div>
		</div>
		
		<div id='mainArea'>
			<section>
				<article>
					<div class='sectionTitle'>
						<h1>Persönliche Informationen:</h1>
					</div>
					
					<div class='sectionContent'>
						<ul>
							<!--<li>ID: $kandidat_id</li>-->
							<li>Adresse: $kandidat_adresa, $kandidat_pbroj, $kandidat_grad</li>
							<li>Geschlecht: $kandidat_spol</li>
							<li>Geburtsdatum: $kandidat_datumrodjenja</li>
							<li>Staatsangehörigkeit: $kandidat_drzavljanstvo</li>
							$kandidat_datum_termina_txt
							<li>Führerschein / Kategorie: $kandidat_vozacka_dozvola</li>
						</ul>
					</div>
				</article>
				<div class='clear'></div>
			</section>

			<section>
				<article>
					<div class='sectionTitle'>
						<h1>Kontakt:</h1>
					</div>
					
					<div class='sectionContent'>
						<ul>
							";
							foreach($kandidat_kontakti as $kontakt){
								$html.= $kontakt . "<br/>";
							}
							$html.="
						</ul>
					</div>
				</article>
				<div class='clear'></div>
			</section>		
			
			<section>
				<div class='sectionTitle'>
					<h1>Berufserfahrung:</h1>
				</div>
				
				<div class='sectionContent'>
				";
				foreach($kandidat_iskustvo as $iskustvo){
					$html.= $iskustvo;
				}
				$html.="
				</div>
				<div class='clear'></div>
			</section>

			<section>
				<div class='sectionTitle'>
					<h1>Schul-und Berufsbildung:</h1>
				</div>
				
				<div class='sectionContent'>
				";
				foreach($kandidat_obrazovanje as $obrazovanje){
					$html.= $obrazovanje;
				}
				$html.="
				</div>
				<div class='clear'></div>
			</section>
			<style>
				table, td, th {    
					border: 1px solid #ddd;
				}

				th, td {
					padding: 15px;
				}
			</style>
			<section>
				<div class='sectionTitle'>
					<h1>Sprachkenntnisse:</h1>
				</div>
				
				<div class='sectionContent'>
				<table style='font-size: 12px !important;'>
					<tr style='background: #ddd;'>
						<th>Sprache</th>
						<th>Hören</th>
						<th>Lesen</th>
						<th>Sprach-<br/>interaktion</th>
						<th>Sprach-<br/>produktion</th>
						<th>Schreiben</th>
					</tr>

				";
				foreach($kandidati_jezici as $jezik){
					$html.= $jezik;
				}
				$html.="

			  </table>
			  <br/>
				</div>
				<div class='clear'></div>
			</section>
			
			
		</div>
	</div>
	</body>
";

$cv_filename = $kandidat_id."-".$kandidat_ime."_".$kandidat_prezime.".pdf";
$siteUrl = getSiteUrlr();
$kandidat_godinarodjenja = substr($kandidat_datumrodjenja,6);
$kandidat_datumrodjenja = $kandidat_godinarodjenja;


	
	$dompdf_profile = new Dompdf();
	$html_profile = "
		<head>
			<meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
				<style>
			
				@page { margin-top: 40px!important; margin-bottom: 40px!important; }
				
				html,body,div,span,object,iframe,h1,h2,h3,h4,h5,h6,p,blockquote,pre,abbr,address,cite,code,del,dfn,em,img,ins,kbd,q,samp,small,strong,sub,sup,var,b,i,dl,dt,dd,ol,ul,li,fieldset,form,label,legend,table,caption,tbody,tfoot,thead,tr,th,td,article,aside,canvas,details,figcaption,figure,footer,header,hgroup,menu,nav,section,summary,time,mark,audio,video {
					border:0;
					font:inherit;
					margin:0;
					padding:0;
					vertical-align:baseline;
					font-family: 'DejaVu Sans', sans-serif !important;
					}
					
					article,aside,details,figcaption,figure,footer,header,hgroup,menu,nav,section {
						display:block;
					}
					
					.clear {clear: both;}
					
					p {
						font-size: 14px;
						line-height: 1.4em;
						margin-bottom: 20px;
						color: #444;
					}
					
					#cv {
						background: #fff;
					}
					
					.mainDetails {
						padding: 40px 35px;
						border-bottom: 2px solid #3d7b85;
						background: #ededed;					
						margin-top: -40px;
					}
					
					#name h1 {
						font-size: 2em;
						font-weight: 700;
						margin-bottom: -6px;
					}
					
					#name h2 {
						font-size: 1.3em;
						margin-top: 5px;
						margin-left: 2px;
					}

					#name h3 {
						font-size: 1em;
						margin-top: 5px;
						margin-left: 2px;
					}
					
					#mainArea {
						padding: 0 40px;
					}
					
					#headshot {
						width: 150px;
						float: left;
						margin-right: 30px;
					}
					
					#headshot img {
						width: 100%;
						height: auto;
					}
					
					#name {
						float: left;
						margin-left: 20px;
					}
					
					#contactDetails {
						float: right;
					}
					
					#contactDetails ul {
						list-style-type: none;
						font-size: 0.9em;
						margin-top: 2px;
					}
					
					#contactDetails ul li {
						margin-bottom: 3px;
						color: #444;
					}
					
					#contactDetails ul li a, a[href^=tel] {
						color: #444; 
						text-decoration: none;
						-webkit-transition: all .3s ease-in;
						-moz-transition: all .3s ease-in;
						-o-transition: all .3s ease-in;
						-ms-transition: all .3s ease-in;
						transition: all .3s ease-in;
					}
					
					#contactDetails ul li a:hover { 
						color: #cf8a05;
					}
					
					
					section {
						border-top: 1px solid #dedede;
						padding: 20px 0 0;
					}
					
					section:first-child {
						border-top: 0;
					}
					
					section:last-child {
						padding: 20px 0 10px;
					}
					
					.sectionTitle {
						float: left;
						width: 25%;
					}
					
					.sectionContent {
						float: right;
						width: 72.5%;
					}

					.sectionContent ul{
						list-style-type: none;
						color: #333;
						margin-bottom: 20px;
					}

					.sectionContent li{
						margin: 5px 0;
						font-size: 14px;
					}
					
					.sectionTitle h1 {
						
						font-style: italic;
						font-size: 20px;
						color: #3d7b85;
					}
					
					.sectionTitle.headerTitle h1 {
						
						font-style: italic;
						font-size: 16px;
						color: #3d7b85;
					}
					
					.sectionContent h2 {					
						font-size: 1.5em;
						margin-bottom: -2px;
					}
					
					.subDetails {
						font-size: 0.8em;
						font-style: italic;
						margin-bottom: 3px;
					}
					
					.keySkills {
						list-style-type: none;
						-moz-column-count:3;
						-webkit-column-count:3;
						column-count:3;
						margin-bottom: 20px;
						font-size: 1em;
						color: #444;
					}
					
					.keySkills ul li {
						margin-bottom: 3px;
					}
					
					@media all and (min-width: 602px) and (max-width: 800px) {
						#headshot {
							display: none;
						}
						
						.keySkills {
						-moz-column-count:2;
						-webkit-column-count:2;
						column-count:2;
						}
					}
					
					@media all and (max-width: 601px) {
						#cv {
							width: 95%;
							margin: 10px auto;
							min-width: 280px;
						}
						
						#headshot {
							display: none;
						}
						
						#name, #contactDetails {
							float: none;
							width: 100%;
							text-align: center;
						}
						
						.sectionTitle, .sectionContent {
							float: none;
							width: 100%;
						}
						
						.sectionTitle {
							margin-left: -2px;
							font-size: 1.25em;
						}
						
						.keySkills {
							-moz-column-count:2;
							-webkit-column-count:2;
							column-count:2;
						}
					}
					
					@media all and (max-width: 480px) {
						.mainDetails {
							padding: 15px 15px;
						}
						
						section {
							padding: 15px 0 0;
						}
						
						#mainArea {
							padding: 0 25px;
						}
					
						
						.keySkills {
						-moz-column-count:1;
						-webkit-column-count:1;
						column-count:1;
						}
						
						#name h1 {
							line-height: .8em;
							margin-bottom: 4px;
						}
					}
					
					@media print {
						#cv {
							width: 100%;
						}
					}
			</style>
		<title>Profil - $kandidat_id</title>
		</head>


		<body id='top'>
		<div id='cv' class='instaFade'>
			<div class='mainDetails'>
				<div id='headshot' class='quickFade'>
				<img src='".getsiteurl()."images/Jobstep-logo_news.png'/>
				</div>
				
				<div id='name'>
					<h1 class='quickFade delayTwo'>$kandidat_id</h1>
					
				</div>
				
				<div id='contactDetails' class='quickFade delayFour'>
					<img src='".getsiteurl()."files/kandidati/$kandidat_slika' width=auto; height=100;/>
				</div>
				<div class='clear'></div>
			
				<div class='sectionTitle headerTitle'>
					<h1>Angestrebte Stelle:</h1>
				</div>
				
				<div class='sectionContent'>
					<h3 class='quickFade delayThree'>$kandidat_prijava_na</h3>
				</div>			
				<div class='clear'></div>
			</div>
			
			<div id='mainArea'>
				<section>
					<article>
						<div class='sectionTitle'>
							<h1>Persönliche informationen</h1>
						</div>
						
						<div class='sectionContent'>
							<ul>
								<li>ID: $kandidat_id</li>
								<!--<li>Adresse: $kandidat_grad</li>
								<li>Geschlecht: $kandidat_spol</li>-->
								<li>Geburtsjahr: $kandidat_datumrodjenja</li>
								<li>Staatsangehörigkeit: $kandidat_drzavljanstvo</li>
								<li>Führerschein / Kategorie: $kandidat_vozacka_dozvola</li>
							</ul>
						</div>
					</article>
					<div class='clear'></div>
				</section>		
				
				<section>
					<div class='sectionTitle'>
						<h1>Berufserfahrung</h1>
					</div>
					
					<div class='sectionContent'>
					";
					foreach($kandidat_iskustvo as $iskustvo){
						$html_profile.= $iskustvo;
					}
					$html_profile.="
					</div>
					<div class='clear'></div>
				</section>

				<section>
					<div class='sectionTitle'>
						<h1>Schul-und berufsbildung</h1>
					</div>
					
					<div class='sectionContent'>
					";
					foreach($kandidat_obrazovanje as $obrazovanje){
						$html_profile.= $obrazovanje;
					}
					$html_profile.="
					</div>
					<div class='clear'></div>
				</section>
				<style>
					table, td, th {    
						border: 1px solid #ddd;
					}

					th, td {
						padding: 15px;
					}
				</style>
				<section>
					<div class='sectionTitle'>
						<h1>Sprachkenntnisse</h1>
					</div>
					
					<div class='sectionContent'>
					<table style='font-size: 12px !important;'>
						<tr style='background: #ddd;'>
							<th>Sprache</th>
							<th>Hören</th>
							<th>Lesen</th>
							<th>Sprach-<br/>interaktion</th>
							<th>Sprach-<br/>produktion</th>
							<th>Schreiben</th>
						</tr>

					";
					foreach($kandidati_jezici as $jezik){
						$html_profile.= $jezik;
					}
					$html_profile.="

				  </table>
				  <br/>
					</div>
					<div class='clear'></div>
				</section>

				<!--<section>
					<div class='sectionTitle'>
						<h1>Zusätzliche fähigkeiten</h1>
					</div>
					
					<div class='sectionContent'>
					";
					foreach($dodatne_vjestine as $vjestina){
						$html_profile.= $vjestina;
					}
					$html_profile.="
					</div>
					<div class='clear'></div>
				</section>-->
			</div>
		</div>
		</body>
	";

	$profil_filename = $kandidat_id."-".$kandidat_ime.".pdf";

if($cv_de == 0){


	$dompdf->loadHtml($html);
	$dompdf->setPaper('A4', 'portrait');
	$dompdf->render();
	$pdf = $dompdf->output();
	$file_location = "files/cv/de/".$cv_filename;

	file_put_contents($file_location,$pdf);

	$query_update = $db->prepare("
					UPDATE idk_kandidati
					SET	cv_de = :cv_de
					WHERE kandidat_id = :kandidat_id");

	$query_update->execute(array(
				':cv_de' => 1,
				':kandidat_id' => $kandidat_id
				));
	
	

	if($profil_de == 0){
		
		$dompdf_profile->loadHtml($html_profile);
		$dompdf_profile->setPaper('A4', 'portrait');
		$dompdf_profile->render();
		$pdf = $dompdf_profile->output();
		$file_location = "files/profile/de/".$profil_filename;
		file_put_contents($file_location,$pdf);

		$query_update = $db->prepare("
						UPDATE idk_kandidati
						SET	profile_de = :profile_de
						WHERE kandidat_id = :kandidat_id");

		$query_update->execute(array(
					':profile_de' => 1,
					':kandidat_id' => $kandidat_id
					));
		
		header("Location: kandidati?page=open&id=$kandidat_id&mess=24");
		

	}else{
		unlink("files/profil/de/" . $profil_filename);

		$dompdf_profile->loadHtml($html_profile);
		$dompdf_profile->setPaper('A4', 'portrait');
		$dompdf_profile->render();
		$pdf = $dompdf_profile->output();
		$file_location = "files/profile/de/".$profil_filename;
		file_put_contents($file_location,$pdf);

	
	header("Location: kandidati?page=open&id=$kandidat_id&mess=25");
	}

}else{
	unlink("files/cv/de/" . $cv_filename);

	$dompdf->loadHtml($html);
	$dompdf->setPaper('A4', 'portrait');
	$dompdf->render();
	$pdf = $dompdf->output();
	$file_location = "files/cv/de/".$cv_filename;
	file_put_contents($file_location,$pdf);
	

	if($profil_de == 0){
		
		$dompdf_profile->loadHtml($html_profile);
		$dompdf_profile->setPaper('A4', 'portrait');
		$dompdf_profile->render();
		$pdf = $dompdf_profile->output();
		$file_location = "files/profile/de/".$profil_filename;
		file_put_contents($file_location,$pdf);

		$query_update = $db->prepare("
						UPDATE idk_kandidati
						SET	profile_de = :profile_de
						WHERE kandidat_id = :kandidat_id");

		$query_update->execute(array(
					':profile_de' => 1,
					':kandidat_id' => $kandidat_id
					));
		
		header("Location: kandidati?page=open&id=$kandidat_id&mess=24");
		

	}else{
		unlink("files/profil/de/" . $profil_filename);

		$dompdf_profile->loadHtml($html_profile);
		$dompdf_profile->setPaper('A4', 'portrait');
		$dompdf_profile->render();
		$pdf = $dompdf_profile->output();
		$file_location = "files/profile/de/".$profil_filename;
		file_put_contents($file_location,$pdf);

	
	
	
	header("Location: kandidati?page=open&id=$kandidat_id&mess=25");
	}
}

break;

case "cv_profil_gen_ba":

$kandidat_id = $_POST['kand_id'];

$query = $db->prepare("
				SELECT kandidat_ime, kandidat_prezime, kandidat_spol, kandidat_djevojackoprezime, kandidat_jmbg, kandidat_datumrodjenja, kandidat_mjestorodjenja, kandidat_drzavarodjenja, kandidat_drzavljanstvo, kandidat_bracnostanje, kandidat_adresa, kandidat_grad, kandidat_pbroj, kandidat_drzava, kandidat_email, kandidat_slika, kandidat_datetime,kandidat_check, kandidat_vozacka_dozvola, kandidat_prijava_na, cv_ba, profile_ba
				FROM idk_kandidati
				WHERE kandidat_id = :kandidat_id");

$query->execute(array(
			':kandidat_id' => $kandidat_id));

$row = $query->fetch();

$kandidat_ime = $row['kandidat_ime'];
$kandidat_prezime = $row['kandidat_prezime'];
$kandidat_spol = $row['kandidat_spol'];
$kandidat_check = $row['kandidat_check'];
$kandidat_djevojackoprezime = $row['kandidat_djevojackoprezime'];
$kandidat_jmbg = $row['kandidat_jmbg'];
$kandidat_mjestorodjenja = $row['kandidat_mjestorodjenja'];
$kandidat_drzavarodjenja = $row['kandidat_drzavarodjenja'];
$kandidat_drzavljanstvo = $row['kandidat_drzavljanstvo'];
$kandidat_bracnostanje = $row['kandidat_bracnostanje'];
$kandidat_drzava = $row['kandidat_drzava'];
$kandidat_email = $row['kandidat_email'];
$kandidat_drzava = $row['kandidat_drzava'];
$kandidat_datetime = $row['kandidat_datetime'];
$kandidat_vozacka_dozvola = $row['kandidat_vozacka_dozvola'];
$kandidat_prijava_na = $row['kandidat_prijava_na'];
$kandidat_datumrodjenja = date('d.m.Y.', strtotime($row['kandidat_datumrodjenja']));

$kandidat_adresa = $row['kandidat_adresa'];
$kandidat_grad = $row['kandidat_grad'];
$kandidat_pbroj = $row['kandidat_pbroj'];

$cv_ba = $row['cv_ba'];
$profile_ba = $row['profile_ba'];

if($row['kandidat_slika'] == "none"){
	$kandidat_slika = "none.jpg";
}else{
	$kandidat_slika = $row['kandidat_slika'];
}


//KONTAKTI
$select_query = $db->prepare("
					SELECT kki_id, kki_grupa, kki_naziv, kki_podatak
					FROM idk_kandidat_kontakt_info
					WHERE kki_kandidat_id = :kki_kandidat_id");

$select_query->execute(array(
				':kki_kandidat_id' => $kandidat_id));

while($select_row = $select_query->fetch()) {

	$kki_id = $select_row['kki_id'];
	$kki_grupa = $select_row['kki_grupa'];
	if($kki_grupa == 1){
		$kki_grupa = "Telefon";
		$ikona = 'fa-mobile';
	}elseif($kki_grupa == 2){
		$kki_grupa = "E-mail";
		$ikona = 'fa-envelope';
	}elseif($kki_grupa == 3){
		$kki_grupa = "Web";
		$ikona = 'fa-globe';
	}elseif($kki_grupa == 4){
		$kki_grupa = "Messangeri";
		$ikona = 'fa-skype';
	}else{};
	$kki_naziv = $select_row['kki_naziv'];
	$kki_podatak = $select_row['kki_podatak'];

	$kandidat_kontakti[] = "
		<li>$kki_naziv: $kki_podatak</li>
	";
}

//IKSUSTVO
$select_query_iskustvo = $db->prepare("
						SELECT kri_id, kri_darum_od, kri_datum_do, kri_pozicija, kri_naziv, kri_grad, kri_opis, kri_aktuelno
						FROM idk_kandidat_radno_iskustvo
						WHERE kri_kandidat_id = :kri_kandidat_id
						ORDER BY kri_darum_od DESC
						");

$select_query_iskustvo->execute(array(
				':kri_kandidat_id' => $kandidat_id));

	while($select_row = $select_query_iskustvo->fetch()) {

		$kri_id = $select_row['kri_id'];
		$kri_darum_od = date('m.Y', strtotime($select_row['kri_darum_od']));
		$kri_datum_do = $select_row['kri_datum_do'];
		$kri_pozicija = $select_row['kri_pozicija'];
		$kri_naziv = $select_row['kri_naziv'];
		$kri_grad = $select_row['kri_grad'];
		$kri_opis = $select_row['kri_opis'];
		$kri_aktuelno = $select_row['kri_aktuelno'];
		
		if($kri_aktuelno != 1 && $kri_datum_do != null){
			$kri_datum_do_f = date('m.Y', strtotime($kri_datum_do));
		}else{
			$kri_datum_do_f = "Aktuelno";
		}						

$kandidat_iskustvo[] = "


<article> 
<h2>$kri_pozicija</h2>
<p class='subDetails'>$kri_darum_od - $kri_datum_do_f</p>
<p>$kri_naziv<br/>
		$kri_grad<br/>
		$kri_opis<br/>
		<p>
</article>

";
}

//OBRAZOVANJE
$select_query_obrazovanje = $db->prepare("
					SELECT ke_id, ke_datumod, ke_datumdo, ke_naziv_kvalifikacije, ke_naziv, ke_grad, ke_opis, ke_aktuelno
					FROM idk_kandidat_edukacija
					WHERE ke_kandidat_id = :ke_kandidat_id");

$select_query_obrazovanje->execute(array(
				':ke_kandidat_id' => $kandidat_id));

while($select_row = $select_query_obrazovanje->fetch()) {

	$ke_id = $select_row['ke_id'];
	$ke_datumod = date('m.Y', strtotime($select_row['ke_datumod']));
	$ke_datumdo = $select_row['ke_datumdo'];
	$ke_naziv_kvalifikacije = $select_row['ke_naziv_kvalifikacije'];
	$ke_naziv = $select_row['ke_naziv'];
	$ke_grad = $select_row['ke_grad'];
	$ke_opis = $select_row['ke_opis'];
	$ke_aktuelno = $select_row['ke_aktuelno'];
	
	if($ke_aktuelno != 1){
		$ke_datumdo_f = date('m.Y', strtotime($ke_datumdo));
	}else{
		$ke_datumdo_f = "Aktuelno";
	}	

	$kandidat_obrazovanje[] = "

	<article> 
		<h2>$ke_naziv_kvalifikacije</h2>
		<p class='subDetails'>$ke_datumod - $ke_datumdo_f</p>
		<p>$ke_naziv<br/>
			$ke_grad<br/>
			$ke_opis<br/>
		<p>
	</article>
	";
}


//VJEŠTINE

$select_query_vjestine = $db->prepare("
				SELECT kj_id, kj_naziv, kj_slusanje, kj_citanje, kj_govorna_interakcija, kj_govorna_produkcija, kj_pisanje
				FROM idk_kandidat_jezici
				WHERE kj_kandidatid = :kj_kandidatid");

$select_query_vjestine->execute(array(
			':kj_kandidatid' => $kandidat_id));

while($select_row = $select_query_vjestine->fetch()) {

	$kj_id = $select_row['kj_id'];
	$kj_naziv = $select_row['kj_naziv'];
	$kj_slusanje = $select_row['kj_slusanje'];
	$kj_citanje = $select_row['kj_citanje'];
	$kj_govorna_interakcija = $select_row['kj_govorna_interakcija'];
	$kj_govorna_produkcija = $select_row['kj_govorna_produkcija'];
	$kj_pisanje = $select_row['kj_pisanje'];
	
	$kandidati_jezici[] = "
		<tr>
			<td> $kj_naziv</td>
			<td> $kj_slusanje </td>
			<td> $kj_citanje </td>
			<td> $kj_govorna_interakcija </td>
			<td> $kj_govorna_produkcija </td>
			<td> $kj_pisanje </td>
		</tr>
	";
}


$select_query_vjestine2 = $db->prepare("
					SELECT kv_id, kv_naziv, kv_grupa, kv_opis
					FROM idk_kandidat_vjestine
					WHERE kv_kandidat_id = :kv_kandidat_id");

$select_query_vjestine2->execute(array(
				':kv_kandidat_id' => $kandidat_id));

while($select_row = $select_query_vjestine2->fetch()) {

	$kv_id = $select_row['kv_id'];
	$kv_naziv = $select_row['kv_naziv'];
	$kv_grupa = $select_row['kv_grupa'];
	if($kv_grupa == 1){
		$kv_grupa = "Osnovne vještine";
	}elseif($kv_grupa == 2){
		$kv_grupa = "Digitalne kompetencije";
	}elseif($kv_grupa == 3){
		$kv_grupa = "Dodatne informacije";
	}else{};
	$kv_opis = $select_row['kv_opis'];

	$dodatne_vjestine[] = " 
	<article> 
		<h2>$kv_grupa</h2>
		<p>$kv_naziv<br/>
			$kv_opis<br/>
		<p>
	</article>
	";
}


$dompdf = new Dompdf();
$html = "
	<head>
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
		<style>
		
			@page { margin-top: 40px!important; margin-bottom: 40px!important; }
			
			html,body,div,span,object,iframe,h1,h2,h3,h4,h5,h6,p,blockquote,pre,abbr,address,cite,code,del,dfn,em,img,ins,kbd,q,samp,small,strong,sub,sup,var,b,i,dl,dt,dd,ol,ul,li,fieldset,form,label,legend,table,caption,tbody,tfoot,thead,tr,th,td,article,aside,canvas,details,figcaption,figure,footer,header,hgroup,menu,nav,section,summary,time,mark,audio,video {
				border:0;
				font:inherit;
				margin:0;
				padding:0;
				vertical-align:baseline;
				font-family: 'DejaVu Sans', sans-serif !important;
				}
				
				article,aside,details,figcaption,figure,footer,header,hgroup,menu,nav,section {
					display:block;
				}
				
				.clear {clear: both;}
				
				p {
					font-size: 14px;
					line-height: 1.4em;
					margin-bottom: 20px;
					color: #444;
				}
				
				#cv {
					background: #fff;
				}
				
				.mainDetails {
					padding: 40px 35px;
					border-bottom: 2px solid #3d7b85;
					background: #ededed;					
					margin-top: -40px;
				}
				
				#name h1 {
					font-size: 2em;
					font-weight: 700;
					margin-bottom: -6px;
				}
				
				#name h2 {
					font-size: 1.3em;
					margin-top: 5px;
					margin-left: 2px;
				}

				#name h3 {
					font-size: 1em;
					margin-top: 5px;
					margin-left: 2px;
				}
				
				#mainArea {
					padding: 0 40px;
				}
				
				#headshot {
					width: 150px;
					float: left;
					margin-right: 30px;
				}
				
				#headshot img {
					width: 100%;
					height: auto;
				}
				
				#name {
					float: left;
					margin-left: 20px;
				}
				
				#contactDetails {
					float: right;
				}
				
				#contactDetails ul {
					list-style-type: none;
					font-size: 0.9em;
					margin-top: 2px;
				}
				
				#contactDetails ul li {
					margin-bottom: 3px;
					color: #444;
				}
				
				#contactDetails ul li a, a[href^=tel] {
					color: #444; 
					text-decoration: none;
					-webkit-transition: all .3s ease-in;
					-moz-transition: all .3s ease-in;
					-o-transition: all .3s ease-in;
					-ms-transition: all .3s ease-in;
					transition: all .3s ease-in;
				}
				
				#contactDetails ul li a:hover { 
					color: #cf8a05;
				}
				
				
				section {
					border-top: 1px solid #dedede;
					padding: 20px 0 0;
				}
				
				section:first-child {
					border-top: 0;
				}
				
				section:last-child {
					padding: 20px 0 10px;
				}
				
				.sectionTitle {
					float: left;
					width: 25%;
				}
				
				.sectionContent {
					float: right;
					width: 72.5%;
				}

				.sectionContent ul{
					list-style-type: none;
					color: #333;
					margin-bottom: 20px;
				}

				.sectionContent li{
					margin: 5px 0;
					font-size: 14px;
				}
				
				.sectionTitle h1 {
					
					font-style: italic;
					font-size: 20px;
					color: #3d7b85;
				}
				
				.sectionTitle.headerTitle h1 {
					
					font-style: italic;
					font-size: 16px;
					color: #3d7b85;
				}
				
				.sectionContent h2 {					
					font-size: 1.5em;
					margin-bottom: -2px;
				}
				
				.subDetails {
					font-size: 0.8em;
					font-style: italic;
					margin-bottom: 3px;
				}
				
				.keySkills {
					list-style-type: none;
					-moz-column-count:3;
					-webkit-column-count:3;
					column-count:3;
					margin-bottom: 20px;
					font-size: 1em;
					color: #444;
				}
				
				.keySkills ul li {
					margin-bottom: 3px;
				}
				
				@media all and (min-width: 602px) and (max-width: 800px) {
					#headshot {
						display: none;
					}
					
					.keySkills {
					-moz-column-count:2;
					-webkit-column-count:2;
					column-count:2;
					}
				}
				
				@media all and (max-width: 601px) {
					#cv {
						width: 95%;
						margin: 10px auto;
						min-width: 280px;
					}
					
					#headshot {
						display: none;
					}
					
					#name, #contactDetails {
						float: none;
						width: 100%;
						text-align: center;
					}
					
					.sectionTitle, .sectionContent {
						float: none;
						width: 100%;
					}
					
					.sectionTitle {
						margin-left: -2px;
						font-size: 1.25em;
					}
					
					.keySkills {
						-moz-column-count:2;
						-webkit-column-count:2;
						column-count:2;
					}
				}
				
				@media all and (max-width: 480px) {
					.mainDetails {
						padding: 15px 15px;
					}
					
					section {
						padding: 15px 0 0;
					}
					
					#mainArea {
						padding: 0 25px;
					}
				
					
					.keySkills {
					-moz-column-count:1;
					-webkit-column-count:1;
					column-count:1;
					}
					
					#name h1 {
						line-height: .8em;
						margin-bottom: 4px;
					}
				}
				
				@media print {
					#cv {
						width: 100%;
					}
				}
		</style>
	<title>CV - $kandidat_ime $kandidat_prezime</title>
	</head>


	<body id='top'>
	<div id='cv' class='instaFade'>
		<div class='mainDetails'>
			<div id='headshot' class='quickFade'>
			<img src='".getsiteurl()."images/Jobstep-logo_news.png'/>
			</div>
			
			<div id='name'>
				<h1 class='quickFade delayTwo'>$kandidat_ime $kandidat_prezime</h1>
				<!--<h2 class='quickFade delayThree'>Prijava za radno mjesto:</h2>
				<h3 class='quickFade delayThree'>$kandidat_prijava_na</h3>-->
			</div>
			
			<div id='contactDetails' class='quickFade delayFour'>
				<img src='".getsiteurl()."files/kandidati/$kandidat_slika' width=auto; height=100;/>
			</div>
			<div class='clear'></div>
			
			<div class='sectionTitle headerTitle'>
				<h1>Prijava za radno mjesto:</h1>
			</div>
			
			<div class='sectionContent'>
				<h3 class='quickFade delayThree'>$kandidat_prijava_na</h3>
			</div>
			<div class='clear'></div>			
		</div>
		
		<div id='mainArea'>
			<section>
				<article>
					<div class='sectionTitle'>
						<h1>Osobne informacije</h1>
					</div>
					
					<div class='sectionContent'>
						<ul>
							<li>ID: $kandidat_id</li>
							<li>Adresa: $kandidat_adresa, $kandidat_pbroj, $kandidat_grad</li>
							<li>Spol: $kandidat_spol</li>
							<li>Datum rođenja: $kandidat_datumrodjenja</li>
							<li>Državljanstvo: $kandidat_drzavljanstvo</li>
						</ul>
					</div>
				</article>
				<div class='clear'></div>
			</section>

			<section>
				<article>
					<div class='sectionTitle'>
						<h1>Kontakti</h1>
					</div>
					
					<div class='sectionContent'>
						<ul>
							";
							foreach($kandidat_kontakti as $kontakt){
								$html.= $kontakt . "<br/>";
							}
							$html.="
						</ul>
					</div>
				</article>
				<div class='clear'></div>
			</section>		
			
			<section>
				<div class='sectionTitle'>
					<h1>Radno iskustvo</h1>
				</div>
				
				<div class='sectionContent'>
				";
				foreach($kandidat_iskustvo as $iskustvo){
					$html.= $iskustvo;
				}
				$html.="
				</div>
				<div class='clear'></div>
			</section>

			<section>
				<div class='sectionTitle'>
					<h1>Obrazovanje i osposobljavanje</h1>
				</div>
				
				<div class='sectionContent'>
				";
				foreach($kandidat_obrazovanje as $obrazovanje){
					$html.= $obrazovanje;
				}
				$html.="
				</div>
				<div class='clear'></div>
			</section>
			<style>
				table, td, th {    
					border: 1px solid #ddd;
				}

				th, td {
					padding: 15px;
				}
			</style>
			<section>
				<div class='sectionTitle'>
					<h1>Osobne vještine</h1>
				</div>
				
				<div class='sectionContent'>
				<table style='font-size: 12px !important;'>
					<tr style='background: #ddd;'>
						<th>Jezik</th>
						<th>Slušanje</th>
						<th>Čitanje</th>
						<th>Govorna interakcija</th>
						<th>Govorna produkcija</th>
						<th>Pisanje</th>
					</tr>

				";
				foreach($kandidati_jezici as $jezik){
					$html.= $jezik;
				}
				$html.="

			  </table>
			  <br/>
				</div>
				<div class='clear'></div>
			</section>

			<section>
				<div class='sectionTitle'>
					<h1>Dodatne vještine</h1>
				</div>
				
				<div class='sectionContent'>
				";
				foreach($dodatne_vjestine as $vjestina){
					$html.= $vjestina;
				}
				$html.="
				</div>
				<div class='clear'></div>
			</section>
			
			<section>
				<div class='sectionTitle'>
					<h1>Vozačka dozvola</h1>
				</div>
				
				<div class='sectionContent'>
					$kandidat_vozacka_dozvola
				</div>
				<div class='clear'></div>
			</section>
			
			
		</div>
	</div>
	</body>
";

$cv_filename = $kandidat_id."-".$kandidat_ime."_".$kandidat_prezime.".pdf";

$dompdf_profile = new Dompdf();
	$html_profile = "
		<head>
			<meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
			<style>
		
				@page { margin-top: 40px!important; margin-bottom: 40px!important; }
				
				html,body,div,span,object,iframe,h1,h2,h3,h4,h5,h6,p,blockquote,pre,abbr,address,cite,code,del,dfn,em,img,ins,kbd,q,samp,small,strong,sub,sup,var,b,i,dl,dt,dd,ol,ul,li,fieldset,form,label,legend,table,caption,tbody,tfoot,thead,tr,th,td,article,aside,canvas,details,figcaption,figure,footer,header,hgroup,menu,nav,section,summary,time,mark,audio,video {
					border:0;
					font:inherit;
					margin:0;
					padding:0;
					vertical-align:baseline;
					font-family: 'DejaVu Sans', sans-serif !important;
					}
					
					article,aside,details,figcaption,figure,footer,header,hgroup,menu,nav,section {
						display:block;
					}
					
					.clear {clear: both;}
					
					p {
						font-size: 14px;
						line-height: 1.4em;
						margin-bottom: 20px;
						color: #444;
					}
					
					#cv {
						background: #fff;
					}
					
					.mainDetails {
						padding: 40px 35px;
						border-bottom: 2px solid #3d7b85;
						background: #ededed;					
						margin-top: -40px;
					}
					
					#name h1 {
						font-size: 2em;
						font-weight: 700;
						margin-bottom: -6px;
					}
					
					#name h2 {
						font-size: 1.3em;
						margin-top: 5px;
						margin-left: 2px;
					}

					#name h3 {
						font-size: 1em;
						margin-top: 5px;
						margin-left: 2px;
					}
					
					#mainArea {
						padding: 0 40px;
					}
					
					#headshot {
						width: 150px;
						float: left;
						margin-right: 30px;
					}
					
					#headshot img {
						width: 100%;
						height: auto;
					}
					
					#name {
						float: left;
						margin-left: 20px;
					}
					
					#contactDetails {
						float: right;
					}
					
					#contactDetails ul {
						list-style-type: none;
						font-size: 0.9em;
						margin-top: 2px;
					}
					
					#contactDetails ul li {
						margin-bottom: 3px;
						color: #444;
					}
					
					#contactDetails ul li a, a[href^=tel] {
						color: #444; 
						text-decoration: none;
						-webkit-transition: all .3s ease-in;
						-moz-transition: all .3s ease-in;
						-o-transition: all .3s ease-in;
						-ms-transition: all .3s ease-in;
						transition: all .3s ease-in;
					}
					
					#contactDetails ul li a:hover { 
						color: #cf8a05;
					}
					
					
					section {
						border-top: 1px solid #dedede;
						padding: 20px 0 0;
					}
					
					section:first-child {
						border-top: 0;
					}
					
					section:last-child {
						padding: 20px 0 10px;
					}
					
					.sectionTitle {
						float: left;
						width: 25%;
					}
					
					.sectionContent {
						float: right;
						width: 72.5%;
					}

					.sectionContent ul{
						list-style-type: none;
						color: #333;
						margin-bottom: 20px;
					}

					.sectionContent li{
						margin: 5px 0;
						font-size: 14px;
					}
					
					.sectionTitle h1 {
						
						font-style: italic;
						font-size: 20px;
						color: #3d7b85;
					}
					
					.sectionTitle.headerTitle h1 {
						
						font-style: italic;
						font-size: 16px;
						color: #3d7b85;
					}
					
					.sectionContent h2 {					
						font-size: 1.5em;
						margin-bottom: -2px;
					}
					
					.subDetails {
						font-size: 0.8em;
						font-style: italic;
						margin-bottom: 3px;
					}
					
					.keySkills {
						list-style-type: none;
						-moz-column-count:3;
						-webkit-column-count:3;
						column-count:3;
						margin-bottom: 20px;
						font-size: 1em;
						color: #444;
					}
					
					.keySkills ul li {
						margin-bottom: 3px;
					}
					
					@media all and (min-width: 602px) and (max-width: 800px) {
						#headshot {
							display: none;
						}
						
						.keySkills {
						-moz-column-count:2;
						-webkit-column-count:2;
						column-count:2;
						}
					}
					
					@media all and (max-width: 601px) {
						#cv {
							width: 95%;
							margin: 10px auto;
							min-width: 280px;
						}
						
						#headshot {
							display: none;
						}
						
						#name, #contactDetails {
							float: none;
							width: 100%;
							text-align: center;
						}
						
						.sectionTitle, .sectionContent {
							float: none;
							width: 100%;
						}
						
						.sectionTitle {
							margin-left: -2px;
							font-size: 1.25em;
						}
						
						.keySkills {
							-moz-column-count:2;
							-webkit-column-count:2;
							column-count:2;
						}
					}
					
					@media all and (max-width: 480px) {
						.mainDetails {
							padding: 15px 15px;
						}
						
						section {
							padding: 15px 0 0;
						}
						
						#mainArea {
							padding: 0 25px;
						}
					
						
						.keySkills {
						-moz-column-count:1;
						-webkit-column-count:1;
						column-count:1;
						}
						
						#name h1 {
							line-height: .8em;
							margin-bottom: 4px;
						}
					}
					
					@media print {
						#cv {
							width: 100%;
						}
					}
			</style>
		<title>Profil - $kandidat_ime</title>
		</head>


		<body id='top'>
		<div id='cv' class='instaFade'>
			<div class='mainDetails'>
				<div id='headshot' class='quickFade'>
				<img src='".getsiteurl()."images/Jobstep-logo_news.png'/>
				</div>
				
				<div id='name'>
					<h1 class='quickFade delayTwo'>$kandidat_ime</h1>
					<!--<h2 class='quickFade delayThree'>Prijava za radno mjesto:</h2>
					<h3 class='quickFade delayThree'>$kandidat_prijava_na</h3>-->
				</div>
				
				<div id='contactDetails' class='quickFade delayFour'>
					<img src='".getsiteurl()."files/kandidati/$kandidat_slika' width=auto; height=100;/>
				</div>
				<div class='clear'></div>
				
				<div class='sectionTitle headerTitle'>
					<h1>Prijava za radno mjesto:</h1>
				</div>
				
				<div class='sectionContent'>
					<h3 class='quickFade delayThree'>$kandidat_prijava_na</h3>
				</div>			
				<div class='clear'></div>				
			</div>
			
			<div id='mainArea'>
				<section>
					<article>
						<div class='sectionTitle'>
							<h1>Osobne informacije</h1>
						</div>
						
						<div class='sectionContent'>
							<ul>
								<li>ID: $kandidat_id</li>
								<li>Adresa: $kandidat_grad</li>
								<li>Spol: $kandidat_spol</li>
								<li>Datum rođenja: $kandidat_datumrodjenja</li>
								<li>Državljanstvo: $kandidat_drzavljanstvo</li>
							</ul>
						</div>
					</article>
					<div class='clear'></div>
				</section>
				
				<section>
					<div class='sectionTitle'>
						<h1>Radno iskustvo</h1>
					</div>
					
					<div class='sectionContent'>
					";
					foreach($kandidat_iskustvo as $iskustvo){
						$html_profile.= $iskustvo;
					}
					$html_profile.="
					</div>
					<div class='clear'></div>
				</section>

				<section>
					<div class='sectionTitle'>
						<h1>Obrazovanje i osposobljavanje</h1>
					</div>
					
					<div class='sectionContent'>
					";
					foreach($kandidat_obrazovanje as $obrazovanje){
						$html_profile.= $obrazovanje;
					}
					$html_profile.="
					</div>
					<div class='clear'></div>
				</section>
				<style>
					table, td, th {    
						border: 1px solid #ddd;
					}

					th, td {
						padding: 15px;
					}
				</style>
				<section>
					<div class='sectionTitle'>
						<h1>Osobne vještine</h1>
					</div>
					
					<div class='sectionContent'>
					<table style='font-size: 12px !important;'>
						<tr style='background: #ddd;'>
							<th>Jezik</th>
							<th>Slušanje</th>
							<th>Čitanje</th>
							<th>Govorna interakcija</th>
							<th>Govorna produkcija</th>
							<th>Pisanje</th>
						</tr>

					";
					foreach($kandidati_jezici as $jezik){
						$html_profile.= $jezik;
					}
					$html_profile.="

				  </table>
				  <br/>
					</div>
					<div class='clear'></div>
				</section>

				<section>
					<div class='sectionTitle'>
						<h1>Dodatne vještine</h1>
					</div>
					
					<div class='sectionContent'>
					";
					foreach($dodatne_vjestine as $vjestina){
						$html_profile.= $vjestina;
					}
					$html_profile.="
					</div>
					<div class='clear'></div>
				</section>
				
				<section>
					<div class='sectionTitle'>
						<h1>Vozačka dozvola</h1>
					</div>
					
					<div class='sectionContent'>
						$kandidat_vozacka_dozvola
					</div>
					<div class='clear'></div>
				</section>
				
				
			</div>
		</div>
		</body>
	";

	$profile_filename = $kandidat_id."-".$kandidat_ime.".pdf";

if($cv_ba == 0){
	
	$dompdf->loadHtml($html);
	$dompdf->setPaper('A4', 'portrait');
	$dompdf->render();
	$pdf = $dompdf->output();
	$file_location = "files/cv/ba/".$cv_filename;
	file_put_contents($file_location,$pdf);

	$query_update = $db->prepare("
					UPDATE idk_kandidati
					SET	cv_ba = :cv_ba
					WHERE kandidat_id = :kandidat_id");

	$query_update->execute(array(
				':cv_ba' => 1,
				':kandidat_id' => $kandidat_id
				));
				


	if($profile_ba == 0){
		
		$dompdf_profile->loadHtml($html_profile);
		$dompdf_profile->setPaper('A4', 'portrait');
		$dompdf_profile->render();
		$pdf = $dompdf_profile->output();
		$file_location ="files/profile/ba/".$profile_filename;
		file_put_contents($file_location,$pdf);

		$query_update = $db->prepare("
						UPDATE idk_kandidati
						SET	profile_ba = :profile_ba
						WHERE kandidat_id = :kandidat_id");

		$query_update->execute(array(
					':profile_ba' => 1,
					':kandidat_id' => $kandidat_id
					));
		
		header("Location: kandidati?page=open&id=$kandidat_id&mess=26");
		

	}else{
		unlink("files/profile/ba/" . $profile_filename);

		$dompdf_profile->loadHtml($html_profile);
		$dompdf_profile->setPaper('A4', 'portrait');
		$dompdf_profile->render();
		$pdf = $dompdf_profile->output();
		$file_location ="files/profile/ba/".$profile_filename;
		file_put_contents($file_location,$pdf);

		header("Location: kandidati?page=open&id=$kandidat_id&mess=27");

	}
		

}else{
	unlink("files/cv/ba/" . $cv_filename);

	$dompdf->loadHtml($html);
	$dompdf->setPaper('A4', 'portrait');
	$dompdf->render();
	$pdf = $dompdf->output();
	$file_location ="files/cv/ba/".$cv_filename;
	file_put_contents($file_location,$pdf);

	

	if($profile_ba == 0){
		
		$dompdf_profile->loadHtml($html_profile);
		$dompdf_profile->setPaper('A4', 'portrait');
		$dompdf_profile->render();
		$pdf = $dompdf_profile->output();
		$file_location ="files/profile/ba/".$profile_filename;
		file_put_contents($file_location,$pdf);

		$query_update = $db->prepare("
						UPDATE idk_kandidati
						SET	profile_ba = :profile_ba
						WHERE kandidat_id = :kandidat_id");

		$query_update->execute(array(
					':profile_ba' => 1,
					':kandidat_id' => $kandidat_id
					));
		
		header("Location: kandidati?page=open&id=$kandidat_id&mess=20");
		

	}else{
		unlink("files/profile/ba/" . $profile_filename);

		$dompdf_profile->loadHtml($html_profile);
		$dompdf_profile->setPaper('A4', 'portrait');
		$dompdf_profile->render();
		$pdf = $dompdf_profile->output();
		$file_location ="files/profile/ba/".$profile_filename;
		file_put_contents($file_location,$pdf);

		header("Location: kandidati?page=open&id=$kandidat_id&mess=21");

	}
}

break;

case "edit_nalog_finances":

	$nalog_id = $_POST['nalog_id'];
	$nalog_ugovor = $_POST['nalog_ugovor'];
	$nalog_potrebno_kandidata = $_POST['nalog_potrebno_kandidata'];
	$nalog_provizija = $_POST['nalog_provizija'];
	$nalog_broj_rata = $_POST['nalog_broj_rata'];
	$nalog_broj_rata_stari = $_POST['nalog_broj_rata_stari'];
	$nalog_vrsta_placanja = $_POST['vrsta_placanja'];
	$mjesecno_avans = $_POST['avans_choose'];
	$mjesecno_avans_postotak = $_POST['postotak_avans'];
	$koeficijent_plate = $_POST['koeficijent'];
	

	switch($nalog_vrsta_placanja){
		//standardni nacin placanja
		case 1:
			$nalog_query = $db->prepare("
							SELECT nalog_naziv
							FROM idk_nalozi
							WHERE nalog_id = :nalog_id");

			$nalog_query->execute(array(
						':nalog_id' => $nalog_id));

			$nalog_row = $nalog_query->fetch();

			$nalog_naziv = $nalog_row['nalog_naziv'];
			
			if($nalog_broj_rata < $nalog_broj_rata_stari){
				header("Location: nalozi?page=open&id=$nalog_id&mess=8");
			}elseif($nalog_broj_rata == $nalog_broj_rata_stari){
				$query = $db->prepare("
								UPDATE idk_nalozi
								SET	nalog_ugovor = :nalog_ugovor, nalog_potrebno_kandidata = :nalog_potrebno_kandidata, nalog_provizija = :nalog_provizija, nalog_broj_rata = :nalog_broj_rata
								WHERE nalog_id = :nalog_id");

				$query->execute(array(
						':nalog_ugovor' => $nalog_ugovor,
						':nalog_potrebno_kandidata' => $nalog_potrebno_kandidata,
						':nalog_provizija' => $nalog_provizija,
						':nalog_broj_rata' => $nalog_broj_rata,
						':nalog_id' => $nalog_id));
				
				$log_desc = "Promijenio financijske informacije za nalog: ".$nalog_id.". Ugovor: ".$nalog_ugovor." Provizija: ".$nalog_provizija." Potrebno kandidata: ".$nalog_potrebno_kandidata." Broj rata: ".$nalog_broj_rata."";
				$log_type = "4";
				addToLogs($log_desc, $log_type); //Log za financije - $log_type = 4		
				header("Location: nalozi?page=open&id=$nalog_id&mess=12");
			}else{

				$query = $db->prepare("
								UPDATE idk_nalozi
								SET	nalog_ugovor = :nalog_ugovor, nalog_potrebno_kandidata = :nalog_potrebno_kandidata, nalog_provizija = :nalog_provizija, nalog_broj_rata = :nalog_broj_rata
								WHERE nalog_id = :nalog_id");

				$query->execute(array(
						':nalog_ugovor' => $nalog_ugovor,
						':nalog_potrebno_kandidata' => $nalog_potrebno_kandidata,
						':nalog_provizija' => $nalog_provizija,
						':nalog_broj_rata' => $nalog_broj_rata,
						':nalog_id' => $nalog_id));

				if($nalog_broj_rata > 0){
					if($nalog_broj_rata != $nalog_broj_rata_stari){
						$promjena = true;
						$nalog_broj_rata = $nalog_broj_rata - $nalog_broj_rata_stari;
					}
					for($i=0; $i<$nalog_broj_rata; $i++){
						if($promjena == true)
						$nr_rata = $nalog_broj_rata_stari + $i +1;
						else
						$nr_rata = $i+1;
						$nr_datum = date("Y-m-d");
						$rata_query = $db->prepare("
										INSERT INTO idk_nalozi_rate
											(nr_nalog, nr_rata, nr_datum)
										VALUES
											(:nr_nalog, :nr_rata, :nr_datum)");

						$rata_query->execute(array(
										':nr_nalog' => $nalog_id,
										':nr_datum' => $nr_datum,
										':nr_rata' => $nr_rata));
					}
				}
				$log_desc = "Promijenio financijske informacije za nalog: ".$nalog_id.". Ugovor: ".$nalog_ugovor." Provizija: ".$nalog_provizija." Potrebno kandidata: ".$nalog_potrebno_kandidata." Broj rata: ".$nalog_broj_rata."";
				$log_type = "4";
				addToLogs($log_desc, $log_type); //Log za financije - $log_type = 4
				header("Location: nalozi?page=open&id=$nalog_id&mess=12");
			}
		break;

		//mjesecne rate
		case 2:

			if($nalog_broj_rata < $nalog_broj_rata_stari){
				header("Location: nalozi?page=open&id=$nalog_id&mess=8");
			}

			if($mjesecno_avans != NULL){

				$sql = "UPDATE idk_nalozi SET nalog_financije = 2, nalog_potrebno_kandidata = :broj_kandidata, nalog_procenat_avans = :procenat_avans, nalog_broj_rata = :broj_rata WHERE nalog_id = :nalog_id";
				$query = $db->prepare($sql);
				$query->execute(array(
					':broj_kandidata' => $nalog_potrebno_kandidata,
					':procenat_avans' => $mjesecno_avans_postotak,
					':broj_rata' => $nalog_broj_rata,
					':nalog_id' => $nalog_id
				));
				//ovdje bi trebala ici adisova funkcija
				//funkcija za mjesecne rate
			} else {

				$sql = "UPDATE idk_nalozi SET nalog_financije = 2, nalog_potrebno_kandidata = :broj_kandidata, nalog_broj_rata = :broj_rata WHERE nalog_id = :nalog_id";
				$query = $db->prepare($sql);
				$query->execute(array(
					':broj_kandidata' => $nalog_potrebno_kandidata,
					':broj_rata' => $nalog_broj_rata,
					':nalog_id' => $nalog_id
				));
				//ovdje bi trebala ici adisova funkcija
				//funkcija za mjesecne rate
			}
		break;

		//po plati kandidata
		case 3:

			if($nalog_broj_rata < $nalog_broj_rata_stari){
				header("Location: nalozi?page=open&id=$nalog_id&mess=8");
			}

			if($nalog_broj_rata == $nalog_broj_rata_stari){

				$query = $db->prepare("
								UPDATE idk_nalozi
								SET	nalog_financije = 3, nalog_ugovor = :nalog_ugovor, nalog_potrebno_kandidata = :nalog_potrebno_kandidata, nalog_provizija_po_plati = :koeficijent, nalog_broj_rata = :nalog_broj_rata
								WHERE nalog_id = :nalog_id");

				$query->execute(array(
						':nalog_ugovor' => $nalog_ugovor,
						':nalog_potrebno_kandidata' => $nalog_potrebno_kandidata,
						':koeficijent' => $koeficijent_plate,
						':nalog_broj_rata' => $nalog_broj_rata,
						':nalog_id' => $nalog_id));
			} else {

				$query = $db->prepare("
								UPDATE idk_nalozi
								SET	nalog_financije = 3, nalog_ugovor = :nalog_ugovor, nalog_potrebno_kandidata = :nalog_potrebno_kandidata, nalog_provizija_po_plati = :koeficijent, nalog_broj_rata = :nalog_broj_rata
								WHERE nalog_id = :nalog_id");

				$query->execute(array(
						':nalog_ugovor' => $nalog_ugovor,
						':nalog_potrebno_kandidata' => $nalog_potrebno_kandidata,
						':koeficijent' => $koeficijent_plate,
						':nalog_broj_rata' => $nalog_broj_rata,
						':nalog_id' => $nalog_id));

				if($nalog_broj_rata > 0){
					if($nalog_broj_rata != $nalog_broj_rata_stari){
						$promjena = true;
						$nalog_broj_rata = $nalog_broj_rata - $nalog_broj_rata_stari;
					}
					for($i=0; $i<$nalog_broj_rata; $i++){
						if($promjena == true)
						$nr_rata = $nalog_broj_rata_stari + $i +1;
						else
						$nr_rata = $i+1;
						$nr_datum = date("Y-m-d");
						$rata_query = $db->prepare("
										INSERT INTO idk_nalozi_rate
											(nr_nalog, nr_rata, nr_datum)
										VALUES
											(:nr_nalog, :nr_rata, :nr_datum)");

						$rata_query->execute(array(
										':nr_nalog' => $nalog_id,
										':nr_datum' => $nr_datum,
										':nr_rata' => $nr_rata));
					}
				}
			}
		break;
	}
break;

case "edit_rate":

	$nalog_id = $_POST['nr_nalog'];
	$nr_id = $_POST['nr_id'];
	$nr_rata = $_POST['nr_rata'];
	$nr_procenat = $_POST['nr_procenat'];
	$nr_procenat_stari = $_POST['nr_procenat_stari'];
	$nr_vrijeme_placanja = $_POST['nr_vrijeme_placanja'];
	$nr_mjeseci_nakon = $_POST['nr_mjeseci_nakon'];
	$nr_datum_post = $_POST['nr_datum'];
	
	if($nr_vrijeme_placanja == "odmah"){
		$nr_datum = date($nr_datum_post."-01");
	}else{
		$nr_datum = date("Y-m-d");
	}
	
	if($nr_vrijeme_placanja == "mjeseci nakon"){
		$nr_mjeseci_nakon = $nr_mjeseci_nakon;
	}else{
		$nr_mjeseci_nakon = null;
	}
	
	$query = $db->prepare("
					UPDATE idk_nalozi_rate
					SET	nr_rata = :nr_rata, nr_procenat = :nr_procenat, nr_vrijeme_placanja = :nr_vrijeme_placanja, nr_mjeseci_nakon = :nr_mjeseci_nakon, nr_datum = :nr_datum
					WHERE nr_id = :nr_id");

	$query->execute(array(
			':nr_rata' => $nr_rata,
			':nr_procenat' => $nr_procenat,
			':nr_vrijeme_placanja' => $nr_vrijeme_placanja,
			':nr_mjeseci_nakon' => $nr_mjeseci_nakon,
			':nr_datum' => $nr_datum,
			':nr_id' => $nr_id));
			
	
	//Add to LOGS
	if($nr_procenat != $nr_procenat_stari){
		$log_desc = "Promijenio procenat rate ".$nr_id." (".$nr_rata."). sa " .$nr_procenat_stari. "% na " .$nr_procenat. "%";
		$log_type = "4";
		addToLogs($log_desc, $log_type); //Log za financije - $log_type = 4
	}
	else{
		$log_desc = "Uredio vrijeme paćanja rate ".$nr_id.".";
		$log_type = "4";
		addToLogs($log_desc, $log_type); //Log za financije - $log_type = 4
	}
	
	header("Location: nalozi?page=open&id=$nalog_id&mess=11");
break;

case "add_rate":

	$nalog_id = $_POST['nalog_id'];
	$nr_rata = $_POST['nr_rata'];
	$nr_procenat = $_POST['nr_procenat'];
	$nr_vrijeme_placanja = $_POST['nr_vrijeme_placanja'];
	$nr_mjeseci_nakon = $_POST['nr_mjeseci_nakon'];
	
	if($nr_vrijeme_placanja == "odmah"){
		$nalog_datum_potpisa = date("Y-m-d", strtotime($_POST['nalog_datum_potpisa']));
		$nalog_datum_potpisa_dan = date("d", strtotime($_POST['nalog_datum_potpisa']));
		
		if($_POST['nalog_datum_potpisa'] !== null){
			if($nalog_datum_potpisa_dan < 15){
				$nr_datum = $nalog_datum_potpisa;
			}else{
				$nr_datum = date('Y-m-d', strtotime("+1 month", strtotime($nalog_datum_potpisa)));
			}
		}else{
			if(date("d") < 15){
				$nr_datum = date("Y-m-d");
			}else{
				$mjesec = date("m")+1;
				$nr_datum = date("Y-0".$mjesec."-01");
			}
		}

		/*
			Unos u novu tabelu idk_nalog_financije - slucaj kada je definisano avansno placanje
		*/

		$queryCheck = $db->prepare("
			SELECT 
				nalog_potrebno_kandidata, nalog_provizija, nalog_financije
			FROM 
				idk_nalozi 
			WHERE 
				nalog_id = :nalog_id
		");

		$queryCheck->execute(array(
			':nalog_id' => $nalog_id
		));

		$rowCheck = $queryCheck->fetch();

		$nalog_potrebno_kandidata 	= intval($rowCheck["nalog_potrebno_kandidata"]);
		$nalog_provizija			= $rowCheck["nalog_provizija"];
		$nalog_financije 			= $rowCheck["nalog_financije"];

		if ( $nalog_financije == 1 ) {
			$nf_datum_aktiviranja = date("Y-m-d", strtotime($nalog_datum_potpisa));
			$nf_iznos_avans = ( $nr_procenat / 100 ) * $nalog_potrebno_kandidata * $nalog_provizija;

			$queryInsertNalogFinancije = $db->prepare("
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
			$queryInsertNalogFinancije->execute(array(
				':nf_nalog_id' => $nalog_id, 
				':nf_datum_aktiviranja' => $nf_datum_aktiviranja, 
				':nf_type' => 1, 
				':nf_broj_rate' => null, 
				':nf_iznos' => $nf_iznos_avans, 
				':nf_placeno' => 1, 
				':nf_datum_fakturisanja' => null, 
				':nf_datum_placanja' => null, 
			));
			$lastAvansId = $db->lastInsertId();

			if ( $lastAvansId != 0 ){
				$log_desc = "NALOG FINANCIJE - AVANS - Dodano avansno placanje u tabelu idk_nalog_financije sa ID = [".$lastAvansId."] pri postavkama prve rate.";
			} else {
				$log_desc = "NALOG FINANCIJE - AVANS - Problem sa dodavanjem avansnog placanja u tabelu idk_nalog_financije pri postavkama prve rate za nalog ID = [".$nalog_id."]. 
				Ostale informacije:  {'nf_datum_aktiviranja': ".$nf_datum_aktiviranja.", 'nf_iznos_avans': ".$nf_iznos_avans.", 'nf_type' : 1}";
			}
			
			addToLogs($log_desc, 4);
		}

	}else{
		$nr_datum = date("Y-m-d");
	}

	if($nr_vrijeme_placanja == "ugovor"){

		$nalog_datum_potpisa = date("Y-m-d", strtotime($_POST['nalog_datum_potpisa']));
		$nf_datum_aktiviranja = date('Y-m-d', strtotime($nalog_datum_potpisa." + 46 days"));

		$queryCheck = $db->prepare("
			SELECT 
				nalog_potrebno_kandidata, nalog_provizija, nalog_financije
			FROM 
				idk_nalozi 
			WHERE 
				nalog_id = :nalog_id
		");

		$queryCheck->execute(array(
			':nalog_id' => $nalog_id
		));

		$rowCheck = $queryCheck->fetch();

		$nalog_potrebno_kandidata 	= intval($rowCheck["nalog_potrebno_kandidata"]);
		$nalog_provizija			= $rowCheck["nalog_provizija"];
		$nalog_financije 			= $rowCheck["nalog_financije"];
		
		if($nalog_financije == 1){
			$nf_iznos_ugovor 			= ( $nr_procenat / 100 ) * $nalog_potrebno_kandidata * $nalog_provizija;
		
			$queryInsertNalogFinancije = $db->prepare("
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
			$queryInsertNalogFinancije->execute(array(
				':nf_nalog_id' => $nalog_id, 
				':nf_datum_aktiviranja' => $nf_datum_aktiviranja, 
				':nf_type' => 5, 
				':nf_broj_rate' => null, 
				':nf_iznos' => $nf_iznos_ugovor, 
				':nf_placeno' => 0, 
				':nf_datum_fakturisanja' => null, 
				':nf_datum_placanja' => null, 
			));
			$lastNalogFinancijeId = $db->lastInsertId();
		}

		if ( $lastNalogFinancijeId != 0 ){
			$log_desc = "NALOG FINANCIJE - UGOVOR - Dodana simulirana suma svih rata za kandidate za ratu potpis ugovora u tabelu idk_nalog_financije sa ID = [".$lastNalogFinancijeId."] pri unosu u idk_nalozi_rate .";
		} else {
			$log_desc = "NALOG FINANCIJE - UGOVOR - Problem sa dodavanjem simulirane sume svih rata za kandidate za ratu potpis ugovora u tabelu idk_nalog_financije pri unosu u idk_nalozi_rate za nalog ID = [".$nalog_id."]. 
			Ostale informacije:  {'nf_datum_aktiviranja': ".$nf_datum_aktiviranja.", 'nf_iznos_avans': ".$nf_iznos_avans.", 'nf_type' : 5}";
		}
		
		addToLogs($log_desc, 4);
	}
	
	if($nr_vrijeme_placanja == "mjeseci nakon"){
		$nr_mjeseci_nakon = $nr_mjeseci_nakon;
	}else{
		$nr_mjeseci_nakon = null;
	}
	
	//Ako zatreba editovanje ratem nakon kandidata
	/*
	if($nr_vrijeme_placanja == "odmah"){
		$nr_datum = date($nr_datum_post."-01");
		$kf_type = 1;
	}else if($nr_vrijeme_placanja == "ugovor"){
		$nr_datum = date("Y-m-d");
		$kf_type = 2;
	}else if($nr_vrijeme_placanja == "pocetak rada"){
		$nr_datum = date("Y-m-d");
		$kf_type = 3;
	}else if($nr_vrijeme_placanja == "mjeseci nakon"){
		$nr_datum = date("Y-m-d");
		$kf_type = 3;
	}
	*/
	
	$rata_query = $db->prepare("
					INSERT INTO idk_nalozi_rate
						(nr_nalog, nr_rata, nr_procenat, nr_vrijeme_placanja, nr_datum, nr_mjeseci_nakon)
					VALUES
						(:nr_nalog, :nr_rata, :nr_procenat, :nr_vrijeme_placanja, :nr_datum, :nr_mjeseci_nakon)");
	
	$rata_query->execute(array(
					':nr_nalog' => $nalog_id,
					':nr_datum' => $nr_datum,
					':nr_procenat' => $nr_procenat,
					':nr_vrijeme_placanja' => $nr_vrijeme_placanja,
					':nr_mjeseci_nakon' => $nr_mjeseci_nakon,
					':nr_rata' => $nr_rata));
	
	
	
	$nalog_query = $db->prepare("
				SELECT nalog_naziv, nalog_broj_rata
				FROM idk_nalozi
				WHERE nalog_id = :nalog_id");
    
	$nalog_query->execute(array(
				':nalog_id' => $nalog_id));
    
	$nalog_row = $nalog_query->fetch();
	
	$nalog_naziv = $nalog_row['nalog_naziv'];
	$nalog_broj_rata = $nalog_row['nalog_broj_rata'];
	
	//Add to LOGS
	
	$log_desc = "Dodao ratu ".$nr_rata." za nalog ".$nalog_naziv." .Vrijeme plaćanja: ".$nr_vrijeme_placanja." Procenat: " .$nr_procenat. "%";
	$log_type = "4";
	addToLogs($log_desc, $log_type); //Log za financije - $log_type = 4
	
	if($nr_rata+1 > $nalog_broj_rata){
		header("Location: nalozi?page=add_blokove_prijave&id=$nalog_id");
		//header("Location: nalozi?page=open&id=$nalog_id&mess=10");
	}
	else{
		
		$iduca_rata = $nr_rata+1;
		header("Location: nalozi?page=add_rate&id=$nalog_id&nr_rata=$iduca_rata");
	}
break;

case "delete_rate":

	$nalog_id = $_POST['nr_nalog'];
	$nr_id = $_POST['nr_id'];
	$nr_rata = $_POST['nr_rata'];
	$nr_procenat = $_POST['nr_procenat'];
	$nr_procenat_stari = $_POST['nr_procenat_stari'];
	$nr_mjeseci_nakon = $_POST['nr_mjeseci_nakon'];
	$nalog_broj_rata = $_POST['nalog_broj_rata']-1;
	$nr_vrijeme_placanja = $_POST['nr_vrijeme_placanja'];
	
	$query = $db->prepare("
					DELETE FROM idk_nalozi_rate
					WHERE nr_id = :nr_id");

	$query->execute(array(
					':nr_id' => $nr_id));
	
	$query = $db->prepare("
					UPDATE idk_nalozi
					SET	nalog_broj_rata = :nalog_broj_rata
					WHERE nalog_id = :nalog_id");

	$query->execute(array(
					':nalog_broj_rata' => $nalog_broj_rata,
					':nalog_id' => $nalog_id));
				
	$nalog_query = $db->prepare("
					SELECT nr_id, nr_rata
					FROM idk_nalozi_rate
					WHERE nr_nalog = :nr_nalog");

	$nalog_query->execute(array(
				':nr_nalog' => $nalog_id));

	while($nalog_row = $nalog_query->fetch()){
		$nr_rata_del = $nalog_row['nr_rata'];
		$nr_id_del = $nalog_row['nr_id'];
		if($nr_rata_del > $nr_rata){
			$nr_rata_del = $nr_rata_del - 1;
			$query = $db->prepare("
					UPDATE idk_nalozi_rate
					SET	nr_rata = :nr_rata
					WHERE nr_id = :nr_id");

			$query->execute(array(
					':nr_rata' => $nr_rata_del,
					':nr_id' => $nr_id_del));
		}
	}
				
	//Add to LOGS
	$log_desc = "Obrisao ratu: ".$nr_id."(".$nr_rata."). Procenat: " .$nr_procenat. "%. Vrijeme plaćanja: ".$nr_vrijeme_placanja."";
	$log_type = "4";
	addToLogs($log_desc, $log_type); //Log za financije - $log_type = 4
	
	header("Location: nalozi?page=open&id=$nalog_id&mess=9");
break;

case "add_blokove_prijave":

	$nalog_id = $_POST['nalog_id'];
	$nalog_vozacka = $_POST['nalog_vozacka'];
	if($nalog_vozacka == 'block'){
		$kriterij_nalog_vozacka = 1;
	}else{
		$kriterij_nalog_vozacka = 0;
	}
	$nalog_kategorija = $_POST['nalog_kategorija'];
	$nbp_iskustvo_u_struci = $_POST['nalog_iskustvo_struka'];	
	if($nbp_iskustvo_u_struci == 1){
		$radno_iskustvo_trajanje = $_POST['radno_iskustvo_trajanje'];	
	}else{
		$radno_iskustvo_trajanje = NULL;	
	}	
	
	$nalog_starost_kandidata=$_POST["nalog_starost_kandidata"];
	
	if($nalog_starost_kandidata==0){
		$starost_od=null;
		$starost_do=null;
	}else{
		$starost_od=$_POST["starost_od"];
		$starost_do=$_POST["starost_do"];
	}
	if($nalog_kategorija == "ne"){
		if($nalog_vozacka = "block"){
			$nalog_vozacka_kat = array("B");
		}else{
			$nalog_vozacka_kat = null;
		}
	}
	else{
		$nalog_vozacka_kat = $_POST['nalog_vozacka_kat'];
	}

	// 	$nbp_struka_id = $_POST['nbp_struka_id'];
	// if($nbp_struka_id == ""){
	// 	$nbp_struka_id_niz = null;
	// }else{
	// 	$nbp_struka_id_niz = implode(",",$nbp_struka_id);
	// }		
	// $nalog_visoko_obr = $_POST['nalog_visoko_obr'];
	// $nalog_dodatno_obr = $_POST['nalog_dodatno_obr'];
	// $nalog_iskustvo = $_POST['nalog_iskustvo'];
	$min_njem_jez = $_POST['min_njem_jez'];
	if($min_njem_jez == "BZ"){
		$njemacki_jezik = 0;
		$nivo_njem_jezika = null;
	}else{
		$njemacki_jezik = 1;
		$nivo_njem_jezika = $min_njem_jez;
	}
	$nalog_jezici = $_POST['nalog_jezici'];
	if($nalog_jezici == 1){
		$kriterij_engleski_jezik		= $_POST['kriterij_engleski_jezik'] ?? null;

		if($kriterij_engleski_jezik == 1){
			$kriterij_nivo_engleskog_jezika = $_POST['kriterij_nivo_engleskog_jezika'] ?? null;
		}else{
			$kriterij_nivo_engleskog_jezika = null;
		}

		$kriterij_italijanski_jezik			= $_POST['kriterij_italijanski_jezik'] ?? null;

		if($kriterij_italijanski_jezik == 1){
			$kriterij_nivo_italijanskog_jezika = $_POST['kriterij_nivo_italijanskog_jezika'] ?? null;
		}else{
			$kriterij_nivo_italijanskog_jezika = null;
		}

		$kriterij_francuski_jezik			= $_POST['kriterij_francuski_jezik'] ?? null;

		if($kriterij_francuski_jezik == 1){
			$kriterij_nivo_francuskog_jezika = $_POST['kriterij_nivo_francuskog_jezika'] ?? null;
		}else{
			$kriterij_nivo_francuskog_jezika = null;
		}
	}else{
		$kriterij_engleski_jezik 			= null;
		$kriterij_nivo_engleskog_jezika 	= null;
		$kriterij_italijanski_jezik 		= null;
		$kriterij_nivo_italijanskog_jezika 	= null;
		$kriterij_francuski_jezik 			= null;
		$kriterij_nivo_francuskog_jezika	= null;
	}
	// $nalog_dokumenti = $_POST['nalog_dokumenti'];
	if($nalog_vozacka_kat == "" or $nalog_vozacka == "none"){
		$nalog_vozacka_kat = null;
	}else{
		$nalog_vozacka_kat = implode(",", $nalog_vozacka_kat);
	}
	
	// if($nalog_dokumenti == 1){
	// 	$doc_slika = $_POST['doc_slika'];
	// 	// $doc_diploma = $_POST['doc_diploma'];
	// 	// $doc_pripravnicki = $_POST['doc_pripravnicki'];
	// 	// $doc_strucni = $_POST['doc_strucni'];
	// 	// $doc_jezik_cert = $_POST['doc_jezik_cert'];
	// }else{
	// 	$doc_slika = "0";
	// 	// $doc_diploma = "0";
	// 	// $doc_pripravnicki = "0";
	// 	// $doc_strucni = "0";
	// 	// $doc_jezik_cert = "0";
	// }
	
	
	$rata_query = $db->prepare("
					INSERT INTO idk_nalozi_blokovi_prijave
						(nbp_nalogid, nbp_vozacka, nbp_vozacka_kategorija, nbp_njemacki_jezik, nbp_ostali_jezici, nbp_iskustvo_u_struci,nbp_iskustvo_u_struci_trajanje, nbp_kandidat_starost_od,nbp_kandidat_starost_do)
					VALUES
						(:nbp_nalogid, :nbp_vozacka, :nbp_vozacka_kategorija, :nbp_njemacki_jezik, :nbp_ostali_jezici,:nbp_iskustvo_u_struci, :nbp_iskustvo_u_struci_trajanje, :nbp_kandidat_starost_od,:nbp_kandidat_starost_do)");
	
	$rata_query->execute(array(
					':nbp_nalogid' => $nalog_id,
					':nbp_vozacka' => $nalog_vozacka,
					':nbp_vozacka_kategorija' => $nalog_vozacka_kat,
					':nbp_njemacki_jezik' => $min_njem_jez,
					':nbp_ostali_jezici' => $nalog_jezici,
					':nbp_iskustvo_u_struci' => $nbp_iskustvo_u_struci,
					':nbp_iskustvo_u_struci_trajanje' => $radno_iskustvo_trajanje,
					':nbp_kandidat_starost_od'=>$starost_od,
					':nbp_kandidat_starost_do'=>$starost_do));
					
	//Add to LOGS
	$log_desc = "Odabrao pitanja na prijavi za nalog: ".$nalog_id.".";
	$log_type = "0";
	addToLogs($log_desc, $log_type); //Log za financije - $log_type = 4

	// Dodavanje profila
	$query_profil = $db->prepare("
			INSERT INTO idk_nalog_profil
				(naziv, prioritet, zaposlenik, nalog_id)
			VALUES
				(:naziv, :prioritet, :zaposlenik, :nalog_id)");

	$query_profil->execute(array(
			':naziv' => "Profil 1",
			':prioritet' => 1,
			':zaposlenik' => $logged_employee_id,
			':nalog_id' => $nalog_id
	));

	$profil_id = $db->lastInsertId();

	$query_kriteriji = $db->prepare("
			INSERT INTO idk_profil_kriterij
				(vozacka_dozvola, kategorija_vozacke_dozvole, smjerovi_naloga, radno_iskustvo_struka, 
				radno_iskustvo_struka_trajanje, starost, starost_minimum, starost_maksimum, 
				njemacki_jezik, nivo_njemackog_jezika, znanje_drugog_jezika, engleski_jezik, nivo_engleskog_jezika, 
				francuski_jezik, nivo_francuskog_jezika, italijanski_jezik, nivo_italijanskog_jezika, profil_id)
			VALUES
				(:vozacka_dozvola, :kategorija_vozacke_dozvole, :smjerovi_naloga, :radno_iskustvo_struka, 
				:radno_iskustvo_struka_trajanje, :starost, :starost_minimum, :starost_maksimum, 
				:njemacki_jezik, :nivo_njemackog_jezika, :znanje_drugog_jezika, :engleski_jezik, :nivo_engleskog_jezika, 
				:francuski_jezik, :nivo_francuskog_jezika, :italijanski_jezik, :nivo_italijanskog_jezika, :profil_id)");

	$query_kriteriji->execute(array(
			':vozacka_dozvola' => $kriterij_nalog_vozacka,
			':kategorija_vozacke_dozvole' => $nalog_vozacka_kat,
			':smjerovi_naloga' => 0,
			':radno_iskustvo_struka' => $nbp_iskustvo_u_struci,
			':radno_iskustvo_struka_trajanje' => $radno_iskustvo_trajanje,
			':starost' => $nalog_starost_kandidata,
			':starost_minimum' => $starost_od,
			':starost_maksimum' => $starost_do,
			':njemacki_jezik' => $njemacki_jezik,
			':nivo_njemackog_jezika' => $nivo_njem_jezika, 
			':znanje_drugog_jezika' => $nalog_jezici,
			':engleski_jezik' => $kriterij_engleski_jezik,
			':nivo_engleskog_jezika' => $kriterij_nivo_engleskog_jezika,
			':francuski_jezik' => $kriterij_francuski_jezik,
			':nivo_francuskog_jezika' => $kriterij_nivo_francuskog_jezika, 
			':italijanski_jezik' => $kriterij_italijanski_jezik, 
			':nivo_italijanskog_jezika' => $kriterij_nivo_italijanskog_jezika,
			':profil_id' => $profil_id
	));

	//Add to LOGS
	$log_desc = "Dodan profil $profil_id za nalog $nalog_id.";
	$log_type = "0";
	addToLogs($log_desc, $log_type); 
	
	header("Location: nalozi?page=open&id=$nalog_id&tab=kriteriji");
	
break;

case "edit_blokove_prijave":

	$nalog_id = $_POST['nalog_id'];
	$nalog_vozacka = $_POST['nalog_vozacka'];
	$nalog_kategorija = $_POST['nalog_kategorija'];
	$nbp_iskustvo_u_struci = $_POST['nalog_iskustvo_struka'];
	if($nbp_iskustvo_u_struci == 1){
		$radno_iskustvo_trajanje = $_POST['radno_iskustvo_trajanje'];	
	}else{
		$radno_iskustvo_trajanje = NULL;	
	}
	$starost_od=$_POST["starost_od"];
	$starost_do=$_POST["starost_do"];
	$nalog_starost_kandidata=$_POST["nalog_starost_kandidata"];

	if($nalog_starost_kandidata==0){
		unset($starost_do);
		unset($starost_od);
	}

	if($nalog_kategorija == "ne")
		$nalog_vozacka_kat = null;
	else
		$nalog_vozacka_kat = $_POST['nalog_vozacka_kat'];
	
	// $nbp_struka_id = $_POST['nbp_struka_id'];
	// if($nbp_struka_id == ""){
	// 	$nbp_struka_id_niz = null;
	// }else{
	// 	$nbp_struka_id_niz = implode(",",$nbp_struka_id);
	// }	

	$nalog_visoko_obr = $_POST['nalog_visoko_obr'];
	$nalog_dodatno_obr = $_POST['nalog_dodatno_obr'];
	// nalog_iskustvo je u bazi nbp_korak4
	// $nalog_iskustvo = $_POST['nalog_iskustvo'];
	$min_njem_jez = $_POST['min_njem_jez'];
	$nalog_jezici = $_POST['nalog_jezici'];
	$nalog_dokumenti = $_POST['nalog_dokumenti'];
	if($nalog_vozacka_kat == "" or $nalog_vozacka == "none"){
		$nalog_vozacka_kat = null;
	}else{
		$nalog_vozacka_kat = implode(",", $nalog_vozacka_kat);
	}
	if($nalog_dokumenti == 1){
		$doc_slika = $_POST['doc_slika'];
		// $doc_diploma = $_POST['doc_diploma'];
		// $doc_pripravnicki = $_POST['doc_pripravnicki'];
		// $doc_strucni = $_POST['doc_strucni'];
		// $doc_jezik_cert = $_POST['doc_jezik_cert'];
	}else{
		$doc_slika = "0";
		// $doc_diploma = "0";
		// $doc_pripravnicki = "0";
		// $doc_strucni = "0";
		// $doc_jezik_cert = "0";
	}
	
	$rata_query = $db->prepare("
					UPDATE idk_nalozi_blokovi_prijave
					SET nbp_vozacka = :nbp_vozacka, nbp_vozacka_kategorija = :nbp_vozacka_kategorija, nbp_visoko_obr = :nbp_visoko_obr, nbp_dodatno_obr = :nbp_dodatno_obr,
						nbp_njemacki_jezik = :nbp_njemacki_jezik, nbp_ostali_jezici = :nbp_ostali_jezici, nbp_korak7 = :nbp_korak7,
						nbp_slika = :nbp_slika,nbp_iskustvo_u_struci =:nbp_iskustvo_u_struci,nbp_kandidat_starost_od=:nbp_kandidat_starost_od,nbp_kandidat_starost_do=:nbp_kandidat_starost_do, nbp_iskustvo_u_struci_trajanje=:nbp_iskustvo_u_struci_trajanje
					WHERE nbp_nalogid = :nbp_nalogid ");
	
	$rata_query->execute(array(
					':nbp_nalogid' => $nalog_id,
					':nbp_vozacka' => $nalog_vozacka,
					':nbp_vozacka_kategorija' => $nalog_vozacka_kat,
					':nbp_visoko_obr' => $nalog_visoko_obr,
					':nbp_dodatno_obr' => $nalog_dodatno_obr,
					':nbp_njemacki_jezik' => $min_njem_jez,
					':nbp_ostali_jezici' => $nalog_jezici,
					':nbp_korak7' => $nalog_dokumenti,
					':nbp_slika' => $doc_slika,
					':nbp_iskustvo_u_struci'=>$nbp_iskustvo_u_struci,
					':nbp_iskustvo_u_struci_trajanje'=>$radno_iskustvo_trajanje,
					':nbp_kandidat_starost_od'=>$starost_od,
					':nbp_kandidat_starost_do'=>$starost_do));

	//Add to LOGS
	$log_desc = "Uredio pitanja na prijavi za nalog: ".$nalog_id.".";
	$log_type = "0";
	addToLogs($log_desc, $log_type); //Log za financije - $log_type = 4
	
	header("Location: nalozi?page=open&id=$nalog_id&tab=kriteriji");
	
break;

case "editCastingDetails":


	$pap_id								= $_POST["pap_id"];
	$pap_nalog_id						= $_POST["pap_nalog_id"];
	if ($_POST["pap_location_name"] != "") {
		$pap_location_name				= $_POST["pap_location_name"];
	} else {    
		$pap_location_name 				= NULL;
	}
	if ($_POST["pap_google_maps_location"] != "") {
		$pap_google_maps_location 			= $_POST["pap_google_maps_location"];
	} else {    
		$pap_google_maps_location 			= NULL;
	}
	$pap_first_send_enabled				= $_POST["pap_first_send_enabled"];
	$pap_first_sending_number_days 		= $_POST["pap_first_sending_number_days"];
	$pap_second_send_enabled			= $_POST["pap_second_send_enabled"];
	$pap_second_sending_number_days		= $_POST["pap_second_sending_number_days"];
	
	if($pap_first_send_enabled=="0"){
		$pap_first_sending_number_days=0;
	}
	if($pap_second_send_enabled=="0"){
		$pap_second_sending_number_days=0;
	}
	if($pap_first_send_enabled=="1" AND $pap_second_send_enabled=="1"){
		if($pap_first_sending_number_days<$pap_second_sending_number_days){
			$pap_first_sending_number_days	= $_POST["pap_second_sending_number_days"];
			$pap_second_sending_number_days	= $_POST["pap_first_sending_number_days"];
		}
	}

	$getOldCastingInfo = $db->prepare("SELECT pap_location_name, 
												pap_google_maps_location, 
												pap_first_sending_number_days, 
												pap_second_sending_number_days, 
												pap_first_send_enabled, 
												pap_second_send_enabled
												FROM 
													idk_pp_appointments 
												WHERE pap_id = :pap_id"
										);
	$getOldCastingInfo->execute(array(
		":pap_id"	=> $pap_id
	)); 
	$oldCastingInfo=$getOldCastingInfo->fetch();

	$updateCastingInfo = $db->prepare("UPDATE idk_pp_appointments 
											SET pap_first_send_enabled =:pap_first_send_enabled,
												pap_location_name =:pap_location_name,
												pap_google_maps_location=:pap_google_maps_location,
												pap_first_sending_number_days=:pap_first_sending_number_days,
												pap_second_sending_number_days=:pap_second_sending_number_days,
												pap_second_send_enabled=:pap_second_send_enabled 
											WHERE pap_id =:pap_id;
	");

	$updateCastingInfo->execute(array(
		":pap_first_send_enabled"			=> $pap_first_send_enabled,
		":pap_location_name"				=> $pap_location_name,
		":pap_google_maps_location"			=> $pap_google_maps_location,
		":pap_first_sending_number_days"	=> $pap_first_sending_number_days,
		":pap_second_sending_number_days"	=> $pap_second_sending_number_days,
		":pap_second_send_enabled"			=> $pap_second_send_enabled,
		":pap_id"							=> $pap_id	
	));

	$log_desc='Zaposlenik napravio promjene na terminu castinga '.$pap_id.'. Location name: '.$oldCastingInfo["pap_location_name"].' => '.$pap_location_name.' , Google maps: '.$oldCastingInfo["pap_google_maps_location"].' => '.$pap_google_maps_location.', First send enabled: '.$oldCastingInfo["pap_first_send_enabled"].' => '.$pap_first_send_enabled.', First send days: '.$oldCastingInfo["pap_first_sending_number_days"].' => '.$pap_first_sending_number_days.', Second send enabled: '.$oldCastingInfo["pap_second_send_enabled"].' => '.$pap_second_send_enabled.', Second send days: '.$oldCastingInfo["pap_second_sending_number_days"].' => '.$pap_second_sending_number_days.'!';
	$log_type="0";

	addToLogs($log_desc,$log_type);

	header("Location: nalozi?page=open&id=$pap_nalog_id&tab=termini");

break;

case "uredi_dodatne_detalje_nalog":

	$nalog_id = $_POST["nalog_id"];
	$nalog_partner_app_location	= null;
	$nalog_partner_app_until	= null;
    $nalog_partner_app_salary	= null;
	if(!empty($_POST["nalog_partner_app_location"])){
		$nalog_partner_app_location	= $_POST["nalog_partner_app_location"];
	}
	if(!empty($_POST["nalog_partner_app_until"])){
		$nalog_partner_app_until = date('Y-m-d H:i:s',strtotime($_POST["nalog_partner_app_until"])); 
	}
	if(!empty($_POST["nalog_partner_app_salary"])){
       $nalog_partner_app_salary	= $_POST["nalog_partner_app_salary"];
	}

	$update_details_query=$db->prepare("UPDATE idk_nalozi SET 
					nalog_partner_app_until = :nalog_partner_app_until,
	 				nalog_partner_app_location = :nalog_partner_app_location,
					nalog_partner_app_salary = :nalog_partner_app_salary	
					WHERE nalog_id = :nalog_id");
	$update_details_query->execute(array(
		":nalog_id"=>$nalog_id,
		":nalog_partner_app_salary"=>$nalog_partner_app_salary,
		":nalog_partner_app_location"=>$nalog_partner_app_location,
		":nalog_partner_app_until"=>$nalog_partner_app_until
	));				

	header("Location: nalozi?page=open&id=$nalog_id");

break;

case "dodaj_nalog_opis":
	$no_nalogid = intval($_POST['no_nalogid']);
	$no_lang = $_POST['no_lang'];
	$no_nalognaziv = $_POST['no_nalognaziv'];
	$no_nalogopis = $_POST['no_nalogopis'];
	
	$query_no = $db->prepare("
		SELECT no_nalogid
		FROM idk_nalozi_opis
		WHERE no_nalogid = :no_nalogid AND no_lang = :no_lang");
					
	$query_no->execute(array(
						':no_nalogid' => $no_nalogid,
						':no_lang' => $no_lang));
	
	$query_n_o = $query_no->rowCount();
	
	if(intval($query_n_o) > 0){
		//UPDATE
		$query = $db->prepare("
			UPDATE idk_nalozi_opis
			SET	no_nalognaziv = :no_nalognaziv, no_nalogopis = :no_nalogopis
			WHERE no_nalogid = :no_nalogid AND no_lang = :no_lang");

		$query->execute(array(
				':no_nalognaziv' => $no_nalognaziv,
				':no_nalogopis' => $no_nalogopis,
				':no_nalogid' => $no_nalogid,
				':no_lang' => $no_lang));					
	}else{
		//INSERT
		$query = $db->prepare("
						INSERT INTO idk_nalozi_opis
							(no_nalognaziv, no_nalogopis, no_nalogid, no_lang)
						VALUES
							(:no_nalognaziv, :no_nalogopis, :no_nalogid, :no_lang)");

		$query->execute(array(
				':no_nalognaziv' => $no_nalognaziv,
				':no_nalogopis' => $no_nalogopis,
				':no_nalogid' => $no_nalogid,
				':no_lang' => $no_lang));
	}
	
	
	//Add to LOGS
	$log_desc = "Uredio opis naloga: " . $no_nalogid . "  jezika ".$no_lang."";
	$log_date = date('Y-m-d H:i:s');

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_employeeid, log_desc, log_date)
					VALUES
						(:log_employeeid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_employeeid' => $logged_employee_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));

	header("Location: nalozi?page=open&id=$no_nalogid");
	
break;

case "offers_and_benefits_of_order": 

	$orderObo 						= intval($_POST["orderObo"]); 

	$countryCityObo 				= ($_POST["countryCityObo"] !== "") 				? $_POST["countryCityObo"] 					: null;

	$positionObo 					= ($_POST["positionObo"] !== "") 					? $_POST["positionObo"] 					: null;
	$positionDescObo 				= ($_POST["positionDescObo"] !== "") 				? $_POST["positionDescObo"] 				: null;

	$salaryObo 						= ($_POST["salaryObo"] !== "") 						? $_POST["salaryObo"] 						: null;
	$salaryPeriodObo 				= ($_POST["salaryPeriodObo"] !== "") 				? $_POST["salaryPeriodObo"] 				: null;
	$salaryTypeObo 					= ($_POST["salaryTypeObo"] !== "") 					? $_POST["salaryTypeObo"] 					: null;

	$bonusQuestionObo 				= ($_POST["bonusQuestionObo"] !== "") 				? $_POST["bonusQuestionObo"] 				: null;
	$bonusHasAmountQuestionObo 		= ($_POST["bonusHasAmountQuestionObo"] !== "") 		? $_POST["bonusHasAmountQuestionObo"] 		: null;
	$bonusAmountObo 				= ($_POST["bonusAmountObo"] !== "") 				? $_POST["bonusAmountObo"] 					: null;
	$bonusPeriodObo 				= ($_POST["bonusPeriodObo"] !== "") 				? $_POST["bonusPeriodObo"] 					: null;
	$bonusTypeObo 					= ($_POST["bonusTypeObo"] !== "") 					? $_POST["bonusTypeObo"] 					: null;

	$apartmentObo 					= ($_POST["apartmentObo"] !== "") 					? $_POST["apartmentObo"] 					: null;

	$hotMealQuestionObo 			= ($_POST["hotMealQuestionObo"] !== "") 			? $_POST["hotMealQuestionObo"] 				: null;
	$hotMealHasAmountQuestionObo 	= ($_POST["hotMealHasAmountQuestionObo"] !== "") 	? $_POST["hotMealHasAmountQuestionObo"] 	: null;
	$hotMealAmountObo 				= ($_POST["hotMealAmountObo"] !== "") 				? $_POST["hotMealAmountObo"] 				: null;
	$hotMealPeriodObo 				= ($_POST["hotMealPeriodObo"] !== "") 				? $_POST["hotMealPeriodObo"] 				: null;
	$hotMealTypeObo 				= ($_POST["hotMealTypeObo"] !== "") 				? $_POST["hotMealTypeObo"] 					: null;

	$additionallyObo 				= ($_POST["additionallyObo"] !== "") 				? $_POST["additionallyObo"] 				: null;

	$result = array(
		"countryCity" => ((preg_match('~[\r\n]+~', $countryCityObo)) ? preg_replace('~[\r\n]+~', ' ', $countryCityObo) : $countryCityObo), 
		"position" => ((preg_match('~[\r\n]+~', $positionObo)) ? preg_replace('~[\r\n]+~', ' ', $positionObo) : $positionObo),  
		"positionDesc" => ((preg_match('~[\r\n]+~', $positionDescObo)) ? preg_replace('~[\r\n]+~', ' ', $positionDescObo) : $positionDescObo),
		"salary" => array(
			"salary" => $salaryObo, 
			"salaryPeriod" => $salaryPeriodObo, 
			"salaryType" => $salaryTypeObo
		),
		"bonus" => array(
			"bonusQuestion" => $bonusQuestionObo, 
			"bonusHasAmountQuestion" => $bonusHasAmountQuestionObo,
			"bonusAmount" => $bonusAmountObo,
			"bonusPeriod" => $bonusPeriodObo,
			"bonusType" => $bonusTypeObo
		),
		"apartment" => $apartmentObo, 
		"hotMeal" => array(
			"hotMealQuestion" => $hotMealQuestionObo,
			"hotMealHasAmountQuestion" => $hotMealHasAmountQuestionObo,
			"hotMealAmount" => $hotMealAmountObo,
			"hotMealPeriod" => $hotMealPeriodObo,
			"hotMealType" => $hotMealTypeObo
		),
		"additionally" => ((preg_match('~[\r\n]+~', $additionallyObo)) ? preg_replace('~[\r\n]+~', ' ', $additionallyObo) : $additionallyObo)
	);
	//print("<pre>".print_r($result,true)."</pre>");
	$result_json = json_encode($result, JSON_PRETTY_PRINT);
	//print("<pre>".print_r($result_json,true)."</pre>");
	
	$response = editOffersAndBenefitsOfOrderR($orderObo, $result_json);

	unset($result);

	header('Location: nalozi?page=open&id='.$orderObo.'&messageObo='.$response.'');

break;

case "transfer_to_new_nalog":
	$kandidat_id 	= $_POST["candidateCRJFC"];
	$nalog_id 	 	= $_POST["nalogCRJFC"];
	$tf_status_id	= $_POST["statusCRJFC"];
	$tf_note 		= $_POST["notesCRJFC"] ?? null;
	$tf_note    	= "Bilješka pri prebacivanju kandidata u novi nalog. ".$tf_note;
	$tf_vrsta_id 	= 1;
	$file_names_s 	= null;

	if(!empty($_POST["timeCRJFC"])){
		$tf_call_appointment =  date("Y-m-d H:i:ss", strtotime($_POST["timeCRJFC"]));
	}else{
		$tf_call_appointment = null;
	}

	$intervju 		= $_POST["interviewTimeCRJFC"] ?? null;
	$intervju_time 	= date("H:i", strtotime(getTimeForAppointment($intervju)));

	$appointment_id = $_POST["castingDateCRJFC"] ?? null;
	if($appointment_id == null){
		$tf_casting_id = 0;
	}else{
		$tf_casting_id = intval(getAppointmentGroup($appointment_id));
	}

	$tf_status_prijave 		= 3;
	$tfs_has_interview		= hasTaskForceInterview($tf_status_id);
	$tf_brojac_neuspjela_komunikacija = null;

	if($tfs_has_interview == 1){
		addAppointmentForCandidate($kandidat_id, $appointment_id, $intervju_time, $intervju);
	}

	$izvor = 1;
	$projekt_casting_id = getCastingProjectForNalog($nalog_id);
	$project_list = getProjectListForNalog($nalog_id);

	$delete_project_cand = $db->prepare("
		DELETE FROM idk_project_kandidati
		WHERE pk_projectid IN ($project_list) AND pk_kandidatid = $kandidat_id"
	);

	$delete_project_cand->execute();

	$query = $db->prepare("
		INSERT INTO idk_project_kandidati
			(pk_projectid, pk_kandidatid)
		VALUES
			(:pk_projectid, :pk_kandidatid)
	");

	$query->execute(array(
		':pk_projectid' => $projekt_casting_id,
		':pk_kandidatid' => $kandidat_id
	));

	updateKandidatStatusPrijave($kandidat_id, $tf_status_prijave);
	addToLogsStatusPrijave(NULL, $projekt_casting_id, $tf_status_prijave, $kandidat_id, $izvor);

	updateLastActiveTaskForCandidate($kandidat_id, $tf_vrsta_id);
	updateTaskForceStatusForCandidate($kandidat_id, $tf_status_id);

	//Da li je kandidat već bio u ovom castingu i na statusu nije dosao
	//ako jeste treba ga ažurirati na Pristao, ako nije onda ide insert
	$tsr_id = checkTFstatsForCandidate($kandidat_id, $tf_casting_id);
	if($tsr_id != null){
		updateTFStat($tsr_id, 4);
	}else{
		insertTFStat($kandidat_id, $logged_employee_id, $tf_casting_id, $nalog_id, 4);
	}

	$query = $db->prepare("
		INSERT INTO idk_notes
			(note_txt, note_datetime, note_group, note_dataid, note_files, note_employeeid)
		VALUES
			(:note_txt, :note_datetime, :note_group, :note_dataid, :note_files, :note_employeeid)");

	$query->execute(array(
		':note_txt' => $tf_note,
		':note_datetime' => date('Y-m-d H:i:s'),
		':note_group' => 2,
		':note_dataid' => $kandidat_id,
		':note_files' => $file_names_s,
		':note_employeeid' => $logged_employee_id
	));

	$log_desc = "Dodao novu bilješku: " .$tf_note. " za kandidata: " .getCandidateFullnameR($kandidat_id).".";

	addToLogs($log_desc,3);

	connectAgentToCandidate($kandidat_id, $logged_employee_id);

	$query = $db->prepare("
		INSERT INTO idk_task_force
			(tf_candidate_id, tf_agent_id, tf_nalog_id, tf_project_id, tf_casting_id, tf_status_id, tf_call_appointment, tf_note, tf_important_note, tf_last_active_task, tf_files, tf_brojac_neuspjela_komunikacija, tf_vrsta_id)
		VALUES
			(:tf_candidate_id, :tf_agent_id, :tf_nalog_id, :tf_project_id, :tf_casting_id, :tf_status_id, :tf_call_appointment, :tf_note, :tf_important_note, :tf_last_active_task, :tf_files, :tf_brojac_neuspjela_komunikacija, :tf_vrsta_id)");

	$query->execute(array(
		':tf_candidate_id' => $kandidat_id,
		':tf_agent_id' => $logged_employee_id,
		':tf_nalog_id' => $nalog_id,
		':tf_project_id' => $projekt_casting_id,
		':tf_casting_id' => $tf_casting_id,
		':tf_vrsta_id' => $tf_vrsta_id,
		':tf_status_id' => $tf_status_id,
		':tf_call_appointment' => $tf_call_appointment,
		':tf_note' => $tf_note,
		':tf_important_note' => 1,
		':tf_last_active_task' => 1,
		':tf_files' => $file_names_s,
		':tf_brojac_neuspjela_komunikacija' => $tf_brojac_neuspjela_komunikacija
	));

	unreserveAgent($logged_employee_id);
	unreserveCandidate($kandidat_id);

	header('Location: kandidati?page=open&id='.$kandidat_id.'&projekt_id='.$projekt_casting_id.'&nalog_id='.$nalog_id.'&vrsta_id=1');
break;

case "edit_dak_kandidat":

			$id_dak_kandidat = $_POST['id_dak_kandidat'];
			$name_dak_kandidat = $_POST['name_dak_kandidat'];
			$lastname_dak_kandidat = $_POST['lastname_dak_kandidat'];
			$email_dak_kandidat = $_POST['email_dak_kandidat'];
			$comment_dak_kandidat = $_POST['comment_dak_kandidat'];
			$daypart_dak_kandidat = $_POST['daypart_dak_kandidat'];
			$dak_kandidat_poslovnica = $_POST['dak_kandidat_poslovnica'];
			$dak_kandidat_rucno_poslodavac = $_POST['dak_kandidat_rucno_poslodavac'];
			$pocetak_rada = date("Y-m-d", strtotime($_POST["pocetak_rada"]));
			
			$query = $db->prepare("
							UPDATE idk_dak_kandidati
							SET	name_dak_kandidat = :name_dak_kandidat, pocetakrada_dak_kandidat = :pocetakrada_dak_kandidat, poslovnica_dak_kandidat = :poslovnica_dak_kandidat, lastname_dak_kandidat = :lastname_dak_kandidat, email_dak_kandidat = :email_dak_kandidat, comment_dak_kandidat = :comment_dak_kandidat,company_dak_kandidat = :company_dak_kandidat, daypart_dak_kandidat = :daypart_dak_kandidat
							WHERE id_dak_kandidat = :id_dak_kandidat");

			$query->execute(array(
					':id_dak_kandidat' => $id_dak_kandidat,
					':name_dak_kandidat' => $name_dak_kandidat,
					':lastname_dak_kandidat' => $lastname_dak_kandidat,
					':pocetakrada_dak_kandidat' => $pocetak_rada,
					':poslovnica_dak_kandidat' => $dak_kandidat_poslovnica,
					':email_dak_kandidat' => $email_dak_kandidat,
					':comment_dak_kandidat' => $comment_dak_kandidat,
					':company_dak_kandidat' => $dak_kandidat_rucno_poslodavac,
					':daypart_dak_kandidat' => $daypart_dak_kandidat));
		
		
		//Add to LOGS
		$log_desc = "Uredio DAK kandidata: " . $name_dak_kandidat . " ".$lastname_dak_kandidat."";
		$log_date = date('Y-m-d H:i:s');

		$log_query = $db->prepare("
						INSERT INTO idk_logs
							(log_employeeid, log_desc, log_date)
						VALUES
							(:log_employeeid, :log_desc, :log_date)");

		$log_query->execute(array(
						':log_employeeid' => $logged_employee_id,
						':log_desc' => $log_desc,
						':log_date' => $log_date));

		header("Location: dak?page=open&id=$id_dak_kandidat&mess=21");

break;

case "add_dak_note":

	$note_txt = $_POST['note_txt'];
	$note_datetime = date('Y-m-d H:i:s');
	$note_group = 7;
	$note_dataid = $_POST['note_dataid'];

	$query = $db->prepare("
					INSERT INTO idk_notes
						(note_txt, note_datetime, note_group, note_dataid, note_employeeid)
					VALUES
						(:note_txt, :note_datetime, :note_group, :note_dataid, :note_employeeid)");

	$query->execute(array(
				':note_txt' => $note_txt,
				':note_datetime' => $note_datetime,
				':note_group' => $note_group,
				':note_dataid' => $note_dataid,
				':note_employeeid' => $logged_employee_id));
				
	$query_company = $db->prepare("
					SELECT name_dak_kandidat, lastname_dak_kandidat
					FROM idk_companies
					WHERE id_dak_kandidat = :id_dak_kandidat");
					
	$query_company->execute(array(
				':id_dak_kandidat' => $note_dataid));
				
	$row = $query_company->fetch();

	$name = "".$row['name_dak_kandidat']."".$row['lastname_dak_kandidat']."";
				
	//Add to LOGS
	$log_desc = "Dodao novu bilješku za DAK kandidata: " .$name. "";
	$log_type = "3";
	addToLogs($log_desc, $log_type); //Log za biljeske - $log_type = 3 

	header("Location: dak?page=open&id=$note_dataid&mess=1"); 


break;

//VALIDACIJA PODATAKA 

case "validacija_all_X":
	
	$kandidat_id = $_POST['val_kandidat_id'];
	$employee_id = $_POST['val_employee_id'];
	$ro_id = $_POST['ro_select_blok'];
	$status = 2;
	$vrsta_podatka = $_POST['val_vrsta_podatka'];
	$date = date('Y-m-d H:i:s');
	
	$query_lang = $db->prepare("
					SELECT jezik FROM users 
					WHERE kandidat_id = :kandidat_id 
	");
	
	$query_lang->execute(array(
					'kandidat_id' => $kandidat_id
	));
	$rowLang = $query_lang->fetch();
	$lang = $rowLang['jezik'];
	
	$query_check = $db->prepare("
					SELECT * FROM idk_validnosti_inputa 
					WHERE vi_kandidat_id = :vi_kandidat_id 
					AND vi_vrsta_podatka = :vi_vrsta_podatka 
	");
	
	$query_check->execute(array(
					'vi_kandidat_id' => $kandidat_id,
					'vi_vrsta_podatka' => $vrsta_podatka
	));
	
	$br = $query_check->rowCount();
	$rowVal = $query_check->fetch();
	
	if($br == 0){
		$insert_val = $db->prepare("
						INSERT INTO idk_validnosti_inputa
							(vi_kandidat_id, vi_status, vi_employee_id, vi_vrsta_podatka, vi_razlog_id, vi_datetime)
						VALUES
							(:vi_kandidat_id, :vi_status, :vi_employee_id, :vi_vrsta_podatka, :vi_razlog_id, :vi_datetime)");

		$insert_val->execute(array(
						':vi_kandidat_id' => $kandidat_id,
						':vi_status' => $status,
						':vi_employee_id' => $employee_id,
						':vi_vrsta_podatka' => $vrsta_podatka,
						':vi_razlog_id' => $ro_id,
						':vi_datetime' => $date
						));	
		
	}else{
		$vi_id = $rowVal['vi_id'];
		//echo $status;
		$update_val = $db->prepare("
						UPDATE idk_validnosti_inputa 
						SET vi_status = :vi_status
						WHERE vi_id = :vi_id
						");

		$update_val->execute(array(
						':vi_status' => $status,
						':vi_id' => $vi_id
						));	
	}
	
	//POZIV FUNKCIJA ZA PROVJERU DA LI JE ZADNJA VALIDACIJA U PITANJU
	$ukupno_za_validaciju = getBrojUnesenihZaValidaciju($kandidat_id);
	$ukupno_na_validaciji = getBrojNaValidaciji($kandidat_id);
	$ukupno_na_ponovnoj_provjeri = getBrojNaProvjeri($kandidat_id);
	
	//DA LI SU SVI PROVJERENI
	$broj_neprovjerenih = $ukupno_za_validaciju - $ukupno_na_validaciji + $ukupno_na_ponovnoj_provjeri;
	if($broj_neprovjerenih == 0){
		
		//PROVJERA DA LI IMA NOVIH ZA KORIGOVATI
		$query_check_val = $db->prepare("
						SELECT * FROM idk_validnosti_inputa 
						WHERE vi_kandidat_id = :vi_kandidat_id
						AND vi_status = :vi_status
						
		");
		
		$query_check_val->execute(array(
						'vi_status' => 2,
						'vi_kandidat_id' => $kandidat_id
		));
		$br_neval = $query_check_val->rowCount();
		if($br_neval > 0){
		
			//UPDATE KANDIDAT STATUS NA 5 (DOPUNA)
			$update_status = $db->prepare("
							UPDATE idk_kandidati 
							SET kandidat_status = :kandidat_status
							WHERE kandidat_id = :kandidat_id
							");

			$update_status->execute(array(
							':kandidat_status' => 5,
							':kandidat_id' => $kandidat_id
							));	
			
			if($lang == "de")
				$text = "Korriegieren Sie Ihre Daten";
			else
				$text = "Niste unijeli dobre podatke!";
			//GET TOKEN USERA
			$query_token = $db->prepare("
							SELECT token, type FROM users
							WHERE kandidat_id = :id
			");
			
			$query_token->execute(array(
							'id' => $kandidat_id
			));
			
			$rowToken = $query_token->fetch();
			$token = base64_decode($rowToken['token']);
			$type = $rowToken['type'];
			if($type == 'android')
				send_bot_notification_android($token, $text);
			else if($type == 'ios')
				send_bot_notification_ios($token, $text);
		}
	}
	
	header("Location: kandidati?page=open&id=$kandidat_id"); 
	
break;

case "validacija_all":
	
	$kandidat_id = $_POST['val_kandidat_id'];
	$employee_id = $_POST['val_employee_id'];
	$status = 1;
	$vrsta_podatka = $_POST['val_vrsta_podatka'];
	$date = date('Y-m-d H:i:s');
	
	$query_lang = $db->prepare("
					SELECT jezik FROM users 
					WHERE kandidat_id = :kandidat_id 
	");
	
	$query_lang->execute(array(
					'kandidat_id' => $kandidat_id
	));
	$rowLang = $query_lang->fetch();
	$lang = $rowLang['jezik'];
	
	$query_check = $db->prepare("
					SELECT * FROM idk_validnosti_inputa 
					WHERE vi_kandidat_id = :vi_kandidat_id 
					AND vi_vrsta_podatka = :vi_vrsta_podatka
	");
	
	$query_check->execute(array(
					'vi_kandidat_id' => $kandidat_id,
					'vi_vrsta_podatka' => $vrsta_podatka
	));
	
	$br = $query_check->rowCount();
	$rowVal = $query_check->fetch();
	
	if($br == 0){
		$insert_val = $db->prepare("
						INSERT INTO idk_validnosti_inputa
							(vi_kandidat_id, vi_status, vi_employee_id, vi_vrsta_podatka, vi_datetime)
						VALUES
							(:vi_kandidat_id, :vi_status, :vi_employee_id, :vi_vrsta_podatka, :vi_datetime)");

		$insert_val->execute(array(
						':vi_kandidat_id' => $kandidat_id,
						':vi_status' => $status,
						':vi_employee_id' => $employee_id,
						':vi_vrsta_podatka' => $vrsta_podatka,
						':vi_datetime' => $date
						));	
		
	}else{
		$vi_id = $rowVal['vi_id'];
		//echo $status;
		$update_val = $db->prepare("
						UPDATE idk_validnosti_inputa 
						SET vi_status = :vi_status
						WHERE vi_id = :vi_id
						");

		$update_val->execute(array(
						':vi_status' => $status,
						':vi_id' => $vi_id
						));	
	}
	
	//POZIV FUNKCIJA ZA PROVJERU DA LI JE ZADNJA VALIDACIJA U PITANJU
	$ukupno_za_validaciju = getBrojUnesenihZaValidaciju($kandidat_id);
	$ukupno_na_validaciji = getBrojNaValidaciji($kandidat_id);
	$ukupno_na_ponovnoj_provjeri = getBrojNaProvjeri($kandidat_id);
	
	//DA LI SU SVI PROVJERENI
	$broj_neprovjerenih = $ukupno_za_validaciju - $ukupno_na_validaciji + $ukupno_na_ponovnoj_provjeri;
	if($broj_neprovjerenih == 0){
		
		//PROVJERA DA LI IMA NOVIH ZA KORIGOVATI
		$query_check_val = $db->prepare("
						SELECT * FROM idk_validnosti_inputa 
						WHERE vi_kandidat_id = :vi_kandidat_id
						AND vi_status = :vi_status
						
		");
		
		$query_check_val->execute(array(
						'vi_status' => 2,
						'vi_kandidat_id' => $kandidat_id
		));
		$br_neval = $query_check_val->rowCount();
		if($br_neval > 0){
		
			//UPDATE KANDIDAT STATUS NA 5 (DOPUNA)
			$update_status = $db->prepare("
							UPDATE idk_kandidati 
							SET kandidat_status = :kandidat_status
							WHERE kandidat_id = :kandidat_id
							");

			$update_status->execute(array(
							':kandidat_status' => 5,
							':kandidat_id' => $kandidat_id
							));	
			
			
			if($lang == "de")
				$text = "Korriegieren Sie Ihre Daten";
			else
				$text = "Niste unijeli dobre podatke!";
			//GET TOKEN USERA
			$query_token = $db->prepare("
							SELECT token, type FROM users
							WHERE kandidat_id = :id
			");
			
			$query_token->execute(array(
							'id' => $kandidat_id
			));
			
			$rowToken = $query_token->fetch();
			$token = base64_decode($rowToken['token']);
			$type = $rowToken['type'];
			if($type == 'android')
				send_bot_notification_android($token, $text);
			else if($type == 'ios')
				send_bot_notification_ios($token, $text);
		}
	}
	header("Location: kandidati?page=open&id=$kandidat_id"); 
break;

case "validacija_all_sveodjednom":
	
	$kandidat_id = $_POST['val_kandidat_id_sveodjednom'];
	$employee_id = $_POST['val_employee_id_sveodjednom'];
	$status = 1;
	$date = date('Y-m-d H:i:s');
	$vrste_podataka = array("blok_ime", "blok_pre", "blok_dtR", "blok_mjR", "blok_drR", "blok_adr", "blok_gra", "blok_pbr", "blok_drz");
	
	foreach($vrste_podataka as $vrsta_podatka){
		$query_check = $db->prepare("
						SELECT * FROM idk_validnosti_inputa 
						WHERE vi_kandidat_id = :vi_kandidat_id 
						AND vi_vrsta_podatka = :vi_vrsta_podatka
		");
		
		$query_check->execute(array(
						'vi_kandidat_id' => $kandidat_id,
						'vi_vrsta_podatka' => $vrsta_podatka
		));
		
		$br = $query_check->rowCount();
		$rowVal = $query_check->fetch();
	
		if($br == 0){
			$insert_val = $db->prepare("
							INSERT INTO idk_validnosti_inputa
								(vi_kandidat_id, vi_status, vi_employee_id, vi_vrsta_podatka, vi_datetime)
							VALUES
								(:vi_kandidat_id, :vi_status, :vi_employee_id, :vi_vrsta_podatka, :vi_datetime)");

			$insert_val->execute(array(
							':vi_kandidat_id' => $kandidat_id,
							':vi_status' => $status,
							':vi_employee_id' => $employee_id,
							':vi_vrsta_podatka' => $vrsta_podatka,
							':vi_datetime' => $date
							));	
			
		}else{
		}
	}
	
	//POZIV FUNKCIJA ZA PROVJERU DA LI JE ZADNJA VALIDACIJA U PITANJU
	$ukupno_za_validaciju = getBrojUnesenihZaValidaciju($kandidat_id);
	$ukupno_na_validaciji = getBrojNaValidaciji($kandidat_id);
	$ukupno_na_ponovnoj_provjeri = getBrojNaProvjeri($kandidat_id);
	
	//DA LI SU SVI PROVJERENI
	$broj_neprovjerenih = $ukupno_za_validaciju - $ukupno_na_validaciji + $ukupno_na_ponovnoj_provjeri;
	if($broj_neprovjerenih == 0){
		
		//PROVJERA DA LI IMA NOVIH ZA KORIGOVATI
		$query_check_val = $db->prepare("
						SELECT * FROM idk_validnosti_inputa 
						WHERE vi_kandidat_id = :vi_kandidat_id
						AND vi_status = :vi_status
						
		");
		
		$query_check_val->execute(array(
						'vi_status' => 2,
						'vi_kandidat_id' => $kandidat_id
		));
		$br_neval = $query_check_val->rowCount();
		if($br_neval > 0){
		
			//UPDATE KANDIDAT STATUS NA 5 (DOPUNA)
			$update_status = $db->prepare("
							UPDATE idk_kandidati 
							SET kandidat_status = :kandidat_status
							WHERE kandidat_id = :kandidat_id
							");

			$update_status->execute(array(
							':kandidat_status' => 5,
							':kandidat_id' => $kandidat_id
							));	
			
			//GET TOKEN USERA
			$query_token = $db->prepare("
							SELECT token, type FROM users
							WHERE kandidat_id = :id
			");
			
			$query_token->execute(array(
							'id' => $kandidat_id
			));
			
			$rowToken = $query_token->fetch();
			$token = base64_decode($rowToken['token']);
			$type = $rowToken['type'];
			/*if($type == 'android')
				send_bot_notification_android($token, "Niste unijeli dobre podatke!");
			else if($type == 'ios')
				send_bot_notification_ios($token, "Niste unijeli dobre podatke!");*/
		}
	}
	header("Location: kandidati?page=open&id=$kandidat_id"); 
break;


case "partner_nalog_active":
	$nalog_id = intval($_POST['nalog_id']);
	
	//UPDATE KANDIDAT STATUS NA 5 (DOPUNA)
	$update_status = $db->prepare("
					UPDATE idk_nalozi 
					SET nalog_partner_active = :nalog_partner_active
					WHERE nalog_id = :nalog_id");

	$update_status->execute(array(
					':nalog_partner_active' => 1,
					':nalog_id' => $nalog_id));	
	
	//Add to LOGS
	$log_desc = "Zaposlenik sa ID-om:" .$logged_employee_id. " je AKTIVIRAO nalog partnerima sa ID: ". $nalog_id ."";
	$log_type = "9";
	addToLogs($log_desc, $log_type); //Log za partnere - $log_type = 9

	header("Location: nalozi?page=open&id=$nalog_id");
	
break;


case "partner_notification":
	$nalog_id = intval($_POST['nalog_id']);
	

		//MY PARTNERS
	$query = $db->prepare("
				SELECT jp_id, jp_imeprezime,jp_lang, jp_fcmtoken
				FROM idk_jobstep_partners
				WHERE jp_id = 104"); 
				
	$query->execute();
					
	$info_arr = array();
	$info_arr["data"] = array();


	while($row = $query->fetch()){  

		$jp_lang = $row['jp_lang'];
		$jp_fcmtoken = $row['jp_fcmtoken'];
		
		if($jp_lang == 'bs'){
			$notifikacija_title = "Nova poslovna ponuda";
			$notifikacija_text = "Poštovani, Jobstep ima novu poslovnu ponudu. Podijelite i zaradite novac.";
		}elseif($jp_lang == 'en'){
			$notifikacija_title = "New business offer";
			$notifikacija_text = "Hello, Jobstep has a new business offer. Share and make money.";
		}else{
			$notifikacija_title = "Neues Geschäftsangebot";
			$notifikacija_text = "Sehr Geehrte Damen und Herren, Jobstep hat ein neues Geschäftsangebot. Teilen Sie es und verdienen Sie Geld.";
		}
		send_notification_partnerapp($jp_fcmtoken, $notifikacija_title, $notifikacija_text);	
	}

	
	//Add to PushNotificationsLOGS
	$pushnot_datetime = date('Y-m-d H:i:s');

	$pushnot_query = $db->prepare("
					INSERT INTO idk_pushnotifications
						(pushnot_datetime, pushnot_nalogid, pushnot_employeeid)
					VALUES
						(:pushnot_datetime, :pushnot_nalogid, :pushnot_employeeid)");

	$pushnot_query->execute(array(
					':pushnot_datetime' => $pushnot_datetime,
					':pushnot_nalogid' => $nalog_id,
					':pushnot_employeeid' => $logged_employee_id));
	
	//Add to LOGS
	$log_desc = "Zaposlenik sa ID-om:" .$logged_employee_id. " je poslao notifikaciju partnerima za nalog ID: ". $nalog_id ."";
	$log_type = "9";
	addToLogs($log_desc, $log_type); //Log za partnere - $log_type = 9

	header("Location: nalozi?page=open&id=$nalog_id");
	
break;

// POTVRDA UPLATE PARTNERU

case "pay_partner":

	
	$vrsta = intval($_POST['vrsta']);
	$kandidat_id = intval($_POST['kandidat_id']);
	$partner_id = intval($_POST['partner_id']);
	$provizija = $_POST['provizija'];
	if(isset($_POST['datum'])) $vrijeme_potvrde_uplate = date('Y-m-d H:i:s',strtotime($_POST['datum']));
	
	else $vrijeme_potvrde_uplate = date('Y-m-d H:i:s');
	
	if($kandidat_id == "all"){
		$update_query = $db->prepare('
						UPDATE idk_partner_uplate
						SET jp_uplate_status = :jp_uplate_status, jp_uplate_zaposlenikid = :jp_uplate_zaposlenikid, jp_uplate_datum = :jp_uplate_datum
						WHERE jp_uplate_partnerid = :jp_uplate_partnerid AND jp_uplate_vrsta = :jp_uplate_vrsta AND jp_uplate_status = 2');
						
		$update_query->execute(array(
					':jp_uplate_partnerid' => $partner_id,
					':jp_uplate_vrsta' => $vrsta,
					':jp_uplate_status' => 1,
					':jp_uplate_zaposlenikid' => $logged_employee_id,
					':jp_uplate_datum' => $vrijeme_potvrde_uplate
					));
					
		
		 generate_partnerGutschrift($partner_id,'all',$vrsta,$vrijeme_potvrde_uplate);
	}else{
		$update_query = $db->prepare('
						UPDATE idk_partner_uplate
						SET jp_uplate_status = :jp_uplate_status, jp_uplate_zaposlenikid = :jp_uplate_zaposlenikid, jp_uplate_datum = :jp_uplate_datum
						WHERE jp_uplate_partnerid = :jp_uplate_partnerid AND jp_uplate_kandidatid = :jp_uplate_kandidatid AND jp_uplate_vrsta = :jp_uplate_vrsta');
						
		$update_query->execute(array(
					':jp_uplate_partnerid' => $partner_id,
					':jp_uplate_kandidatid' => $kandidat_id,
					':jp_uplate_vrsta' => $vrsta,
					':jp_uplate_status' => 1,
					':jp_uplate_zaposlenikid' => $logged_employee_id,
					':jp_uplate_datum' => $vrijeme_potvrde_uplate
					));
					
		
		 generate_partnerGutschrift($partner_id,$kandidat_id,$vrsta,$vrijeme_potvrde_uplate);
	}
	 $log_desc = "Zaposlenik sa ID-om:" .$logged_employee_id. " je poslao potvrdio uplatu partneru sa ID-om: ". $partner_id ." za kandidata sa ID-om: ".$kandidat_id."";
	 $log_type = "9";
	 addToLogs($log_desc, $log_type); //Log za partnere - $log_type = 9
break;

case "spremanje_u_dipl":
	$partner_id = getPartnerIdFromToken(base64_decode($_POST['token']));

	$kandidat_ime = $_POST["kandidat_ime_dipl"];
	$kandidat_prezime = $_POST["kandidat_prezime_dipl"];
	$kandidat_broj_tel = $_POST["kandidat_telefon_dipl"];
	$kandidat_email = $_POST["kandidat_email_dipl"];
	
	//STATUS PARTNERA
	$query_partnerstatus = $db->prepare("
				SELECT jp_position
				FROM idk_jobstep_partners
				WHERE jp_id = :jp_id");
				
	$query_partnerstatus->execute(array(':jp_id' => $partner_id));
	$row_status = $query_partnerstatus->fetch();
	$partner_nd_status = intval($row_status['jp_position']);
	
	//Provjera da li se nalazi u DIPL VEC
	$provjera_dipl = $db->prepare("
							SELECT id_broj_nd_kandidata, zaduzen_zaposlenik_nd_kandidata
							FROM idk_nd_kandidata
							WHERE mobilni_nd_kandidata = :mobilni_nd_kandidata AND mobilni_nd_kandidata != ''
							");
	$provjera_dipl->execute(array(
							":mobilni_nd_kandidata" => $kandidat_broj_tel
						));
	$dipl_da_ne = $provjera_dipl->rowCount();
	$provjera_dipl_row = $provjera_dipl->fetch();
	$kandidat_id_vec_u_dipl = $provjera_dipl_row['id_broj_nd_kandidata'];
	$zaduzen_id_vec_u_dipl = $provjera_dipl_row['zaduzen_zaposlenik_nd_kandidata'];
	if($dipl_da_ne == 0){
		//Ako se kandidat ne nalazi u diplu
			$menager = getDodjeliAgentuDIPLR($kandidat_broj_tel);
			//REZULTAT ALGORITMA 
			$zadnji_menager = $menager['stari'];
			$novi_menager = $menager['novi'];
			$novi_menager_team = getTeamIdByEmployee($novi_menager);
			
			$ime_new_ND_cand1 = $kandidat_ime;
			$prezime_new_ND_cand1 = $kandidat_prezime;
			$email_new_ND_cand1 = $kandidat_email;
			$mobilni_new_ND_cand1 = $kandidat_broj_tel;
			//UPDATE kandidata u Dipl
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
									kandidat_idd,
									partner_nd_status
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
									:kandidat_idd,
									:partner_nd_status
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
							':povijest_nd_kandidata' => 3,
							':povijest_vrsta_nd_kandidata' => 1,
							':kandidat_idd' => $partner_id,
							':partner_nd_status' => $partner_nd_status
							));
			//Get last ID
			$kandidat_id_nd = $db->lastInsertId();
			
			//UPDATE NULL starom zaduzenom i UPDATE 1 novom zaduzenom 
			if($zadnji_menager == NULL){
				//Ako je zadnji menadzer == NULL onda se samo radi UPDATE prvog u redu menadzera na 1 
				$update_novog_menadzera_sa_1 = $db->prepare("
									UPDATE idk_employees
									SET employee_nostrifikacija_zadnji_menadzer_app	 = :employee_nostrifikacija_zadnji_menadzer_app	
									WHERE employee_id = :employee_id");

				$update_novog_menadzera_sa_1->execute(array(
							':employee_id' => $novi_menager,
							':employee_nostrifikacija_zadnji_menadzer_app' => 1
							));
			}
			else{
				//Update zadnjeg menadzera START
				$update_starog_menadzera_sa_null = $db->prepare("
									UPDATE idk_employees
									SET employee_nostrifikacija_zadnji_menadzer_app	 = :employee_nostrifikacija_zadnji_menadzer_app	
									WHERE employee_id = :employee_id");

				$update_starog_menadzera_sa_null->execute(array(
							':employee_id' => $zadnji_menager,
							':employee_nostrifikacija_zadnji_menadzer_app' => NULL
							));
				
				//Update zadnjeg menadzera START
				$update_novog_menadzera_sa_1 = $db->prepare("
									UPDATE idk_employees
									SET employee_nostrifikacija_zadnji_menadzer_app	 = :employee_nostrifikacija_zadnji_menadzer_app	
									WHERE employee_id = :employee_id");

				$update_novog_menadzera_sa_1->execute(array(
							':employee_id' => $novi_menager,
							':employee_nostrifikacija_zadnji_menadzer_app' => 1
							));
			}
			
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
			";*/
			
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
			
			//sendEmail($mail_email, $mail_name, $mail_subject, $mail_body, $mail_altbody);		
			//ADD TO LOGS START
				$log_date = date('Y-m-d H:i:s');
				$log_desc = "DIPL -> Dodan novi kandidat sa ID = [".$kandidat_id_nd."] iz sa aplikacije Partner APP - Partner ID = [".$partner_id."] - Zadužen zaposlenik: ".$novi_menager." ";
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
			
			//Provjera da li se nalazi u kandidatima
			$provjera_kandidati = $db->prepare("
							SELECT kandidat_id
							FROM idk_kandidati
							WHERE kandidat_mobitel = :kandidat_mobitel
							");
			$provjera_kandidati->execute(array(
									":kandidat_mobitel" => $kandidat_broj_tel
								));
			$kandidati_da_ne = $provjera_kandidati->rowCount();
			$provjera_kandidati_row = $provjera_kandidati->fetch();
			$kandidat_id_vec_u_kan = $provjera_kandidati_row['kandidat_id'];
			if($kandidati_da_ne == 0){
				//DODAVANJE U TABELU IDK_KANDIDATI
				
				$kandidat_check = md5(uniqid(rand(), true));
				
				//Add user to db
				$query_add_user = $db->prepare("
								INSERT INTO idk_kandidati
									(kandidat_check, kandidat_ime, kandidat_prezime, kandidat_email, kandidat_mobitel, kandidat_slika, kandidat_status, kandidat_status_messenger, kandidat_status_prijave, kandidat_datetime, kandidat_visitedurl, kandidat_prijava_na, kandidat_group, povezan_na_dipl, kandidat_porijeklo, kandidat_dipl_id )
								VALUES
									(:kandidat_check, :kandidat_ime, :kandidat_prezime, :kandidat_email, :kandidat_mobitel, :kandidat_slika, :kandidat_status, :kandidat_status_messenger, :kandidat_status_prijave, :kandidat_datetime, :kandidat_visitedurl, :kandidat_prijava_na, :kandidat_group, :povezan_na_dipl, :kandidat_porijeklo, :kandidat_dipl_id)");
		
				$query_add_user->execute(array(
							':kandidat_check' => $kandidat_check,
							':kandidat_ime' => $ime_new_ND_cand1,
							':kandidat_prezime' => $prezime_new_ND_cand1,
							':kandidat_email' => $email_new_ND_cand1,
							':kandidat_mobitel' => $mobilni_new_ND_cand1,
							':kandidat_slika' => "none",
							':kandidat_status' => 0,
							':kandidat_status_messenger' => 1,
							':kandidat_status_prijave' => 1,
							':kandidat_datetime' => date('Y-m-d H:i:s'),
							':kandidat_visitedurl' => 1,
							':kandidat_prijava_na' => "Ostalo",
							':kandidat_group' => 7,
							':povezan_na_dipl' => 1,
							':kandidat_porijeklo' => 1,
							':kandidat_dipl_id' => $kandidat_id_nd
							));
							
				$kandidat_id = $db->lastInsertId();
				//UPDATE ND_KANDIDATA STAVITI OVAJ ID 
				
				$query_log_status = $db->prepare("
						INSERT INTO idk_log_kandidat_statusi
							(lks_kandidat_id, lks_status_obrade, lks_status_messenger, lks_datetime)
						VALUES
							(:lks_kandidat_id, :lks_status_obrade, :lks_status_messenger, :lks_datetime)
				");
				
				$query_log_status->execute(array(
						':lks_kandidat_id' => $kandidat_id,
						':lks_status_obrade' => 0,
						':lks_status_messenger' => 1,
						':lks_datetime' => date('Y-m-d H:i:s')
				));
				
				$nalog_query = $db->prepare("
										SELECT project_id
										FROM idk_projects
										WHERE (project_name LIKE '%Kandidati sa PartnerAPP%') ");
			
				$nalog_query->execute();
			
				$nalogrow = $nalog_query->fetch();
				
				$project_id = $nalogrow['project_id'];
				$query_project = $db->prepare("
								INSERT INTO idk_project_kandidati
									(pk_projectid, pk_kandidatid)
								VALUES
									(:pk_projectid, :pk_kandidatid)");

				$query_project->execute(array(
								':pk_projectid' => $project_id,
								':pk_kandidatid' => $kandidat_id));
				
				addToLogsStatusPrijave(NULL, $project_id, 1, $kandidat_id, 3);
								
				$kki_grupa = 1;
				$kki_naziv = "Mobilni";

				//Add mobilni to db
				$query_mob = $db->prepare("
								INSERT INTO idk_kandidat_kontakt_info
									(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id)
								VALUES
									(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id)");

				$query_mob->execute(array(
							':kki_grupa' => $kki_grupa,
							':kki_naziv' => $kki_naziv,
							':kki_podatak' => $mobilni_new_ND_cand1,
							':kki_kandidat_id' => $kandidat_id));
				
				$kki_grupa_e = 2;
				$kki_naziv_e = "E-mail";
				
				//Add kontakt info to db
				$query_email = $db->prepare("
								INSERT INTO idk_kandidat_kontakt_info
									(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id)
								VALUES
									(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id)");

				$query_email->execute(array(
							':kki_grupa' => $kki_grupa_e,
							':kki_naziv' => $kki_naziv_e,
							':kki_podatak' => $email_new_ND_cand1,
							':kki_kandidat_id' => $kandidat_id));
				
				//Add to table users (chatbot)
				$random_string = generateRandomString();
				$options = [
					'cost' => 10,
				];
				$random_password = password_hash($random_string, PASSWORD_BCRYPT, $options);
				
				$log_desc2 = "Kandidat: " . $ime_new_ND_cand1 . " " . $prezime_new_ND_cand1 . "(".$kandidat_id."). ; (".$random_string.")";
				$log_type2 = "5";
				addToLogs($log_desc2, $log_type2);
				
				$characters = '0123456789';
				$charactersLength = strlen($characters);
				$randomString = '';
				for ($i = 0; $i < 5; $i++) {
					$randomString .= $characters[rand(0, $charactersLength - 1)];
				}
				$bot_koriscnicko_ime = $ime_new_ND_cand1.$randomString;
				$kandidat_full_name = $ime_new_ND_cand1." ".$prezime_new_ND_cand1;
				$query_user = $db->prepare("
							INSERT INTO users
								(phone, name, nalog_id, email, password, kandidat_id)
							VALUES
								(:phone, :name, :nalog_id, :email, :password, :kandidat_id)");

				$query_user->execute(array(
							':phone' => $mobilni_new_ND_cand1,
							':name' => $kandidat_full_name,
							':nalog_id' => 44,
							':email' => $bot_koriscnicko_ime,
							':password' => $random_password,
							':kandidat_id' => $kandidat_id));
							
				//INFOBIP
				sendSmsToCandidateInfobip1($random_string, $mobilni_new_ND_cand1, "NN");
				sleep(1);  // Seconds
				sendSmsToCandidateInfobip2($random_string, $mobilni_new_ND_cand1, "NN");
				sleep(1);  // Seconds
				sendSmsToCandidateInfobip3($random_string, $mobilni_new_ND_cand1, $bot_koriscnicko_ime);
				sleep(1);  // Seconds
				sendSmsToCandidateInfobip4($random_string, $mobilni_new_ND_cand1, $bot_koriscnicko_ime);
				
				sendCandidateMessengerMail($random_string, $email_new_ND_cand1, $bot_koriscnicko_ime);
				
				//KRAJ DODAVANJA U TABELU KANDIDATI
			}
			else{
				$update_query_pov_dipl = $db->prepare("
						UPDATE idk_kandidati
						SET povezan_na_dipl = :povezan_na_dipl, kandidat_dipl_id = :kandidat_dipl_id
						WHERE kandidat_id = :kandidat_id
				");
				
				$update_query_pov_dipl->execute(array(
							':kandidat_id' => $kandidat_id_vec_u_kan,
							':povezan_na_dipl' => 1,
							':kandidat_dipl_id' => $kandidat_id_nd
							));
			}
	}
	else{
		//Ako se kandidat nalazi u diplu
		//Provjera da li se nalazi u kandidatima
			$provjera_kandidati = $db->prepare("
							SELECT kandidat_id
							FROM idk_kandidati
							WHERE kandidat_mobitel = :kandidat_mobitel AND kandidat_status != :kandidat_status
							");
			$provjera_kandidati->execute(array(
									":kandidat_mobitel" => $kandidat_broj_tel,
									":kandidat_status" => 3
								));
			$kandidati_da_ne = $provjera_kandidati->rowCount();
			$provjera_kandidati_row = $provjera_kandidati->fetch();
			$kandidat_id_vec_u_kan = $provjera_kandidati_row['kandidat_id'];
			if($kandidati_da_ne != 0){
				$update_query_pov_dipl = $db->prepare("
						UPDATE idk_kandidati
						SET povezan_na_dipl = :povezan_na_dipl, kandidat_dipl_id = :kandidat_dipl_id
						WHERE kandidat_id = :kandidat_id
				");
				
				$update_query_pov_dipl->execute(array(
							':kandidat_id' => $kandidat_id_vec_u_kan,
							':povezan_na_dipl' => 1,
							':kandidat_dipl_id' => $kandidat_id_vec_u_dipl
							));
			}
		//Ako se kandidat nalazi u diplu
		$desila_se_prijava = insertPonovnePrijaveDIPL($kandidat_id_vec_u_dipl, $kandidat_ime, $kandidat_prezime, $kandidat_broj_tel, $kandidat_email, 3, 1, NULL);
		if($desila_se_prijava == 1){
			/*
			//Slanje maila starom menadzeru sa linkom kandidata njemu dodjeljenog
			$user_query = $db->prepare("
									SELECT employee_firstname, employee_lastname, employee_email
									FROM idk_employees
									WHERE employee_id = :employee_id");

			$user_query->execute(array(
							':employee_id' => $zaduzen_id_vec_u_dipl));

			$user = $user_query->fetch();

			$employee_firstname = $user['employee_firstname'];
			$employee_lastname = $user['employee_lastname'];
			$employee_email = $user['employee_email'];

			//Send email to user
			$mail_email = $employee_email;
			$mail_name = $employee_firstname . ' ' . $employee_lastname;
			$mail_subject = "Dipl modul - Stari kandidat";
			$mail_url = "" . getSiteUrlr() . "/nostrifikacija_diploma?page=otvori_ND_kandidata&id=".$kandidat_id_vec_u_dipl." ";
			$mail_body = "
							<p>Vaš stari kandidat u navedenom linku, pokušao se ponovno prijaviti za nostrifikaciju diplome po preporuci partnera.</p>
							<p>Detalje pogledajte na linku: " . $mail_url . "</p>
			";
			$mail_altbody = "
							<p>Vaš stari kandidat u navedenom linku, pokušao se ponovno prijaviti za nostrifikaciju diplome po preporuci partnera.</p>
							<p>Detalje pogledajte na linku: " . $mail_url . "</p>
			";
			
			sendEmail($mail_email, $mail_name, $mail_subject, $mail_body, $mail_altbody);
			*/
		}
	}
	header("Location: https://svezavizu.eu/");
break;

case "szvinDIPL": 
	// Build POST request:
	$recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
	$recaptcha_secret = '6LfZkPkUAAAAAD6SjD0kRTfSa3RVlWf-27rJuglN';
	$recaptcha_response = $_POST['recaptcha_response'];
	// Make and decode POST request:
	$recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
	$recaptcha = json_decode($recaptcha);
	// Take action based on the score returned:
	if ($recaptcha->score >= 0.3) {
	$kandidat_ime = $_POST["kandidat_ime_dipl"];
	$kandidat_prezime = $_POST["kandidat_prezime_dipl"];
	$kandidat_broj_tel = $_POST["kandidat_telefon_dipl"];
	$kandidat_email = $_POST["kandidat_email_dipl"];

	//Provjera da li se nalazi u DIPL VEC
	$provjera_dipl = $db->prepare("
							SELECT id_broj_nd_kandidata, zaduzen_zaposlenik_nd_kandidata
							FROM idk_nd_kandidata
							WHERE mobilni_nd_kandidata = :mobilni_nd_kandidata AND mobilni_nd_kandidata != ''
							");
	$provjera_dipl->execute(array(
							":mobilni_nd_kandidata" => $kandidat_broj_tel
						));
	$dipl_da_ne = $provjera_dipl->rowCount();
	$provjera_dipl_row = $provjera_dipl->fetch();
	$kandidat_id_vec_u_dipl = $provjera_dipl_row['id_broj_nd_kandidata'];
	$zaduzen_id_vec_u_dipl = $provjera_dipl_row['zaduzen_zaposlenik_nd_kandidata'];
	if($dipl_da_ne == 0){
		//Ako se kandidat ne nalazi u diplu
		
			$menager = getDodjeliAgentuDIPLR($kandidat_broj_tel);
			//REZULTAT ALGORITMA 
			$zadnji_menager = $menager['stari'];
			$novi_menager = $menager['novi'];
			$novi_menager_team = getTeamIdByEmployee($novi_menager);
			//REZULTAT ALGORITMA 
			
			$ime_new_ND_cand1 = $kandidat_ime;
			$prezime_new_ND_cand1 = $kandidat_prezime;
			$email_new_ND_cand1 = $kandidat_email;
			$mobilni_new_ND_cand1 = $kandidat_broj_tel;
			//UPDATE kandidata u Dipl
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
							':povijest_nd_kandidata' => 2,
							':povijest_vrsta_nd_kandidata' => 2,
							':kandidat_idd' => NULL
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
			";*/
			
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
			
			//sendEmail($mail_email, $mail_name, $mail_subject, $mail_body, $mail_altbody);		
			//ADD TO LOGS START
				$log_date = date('Y-m-d H:i:s');
				$log_desc = "DIPL -> Dodan novi kandidat sa ID = [".$kandidat_id_nd."] sa stranice SveZaVizu.eu - Zadužen zaposlenik: ".$novi_menager." ";
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
			
			//Provjera da li se nalazi u kandidatima
			$provjera_kandidati = $db->prepare("
							SELECT kandidat_id
							FROM idk_kandidati
							WHERE kandidat_mobitel = :kandidat_mobitel AND kandidat_status != :kandidat_status
							");
			$provjera_kandidati->execute(array(
									":kandidat_mobitel" => $kandidat_broj_tel,
									":kandidat_status" => 3
								));
			$kandidati_da_ne = $provjera_kandidati->rowCount();
			$provjera_kandidati_row = $provjera_kandidati->fetch();
			$kandidat_id_vec_u_kan = $provjera_kandidati_row['kandidat_id'];
			if($kandidati_da_ne == 0){
				//DODAVANJE U TABELU IDK_KANDIDATI
				
				$kandidat_check = md5(uniqid(rand(), true));
				
				//Add user to db
				$query_add_user = $db->prepare("
								INSERT INTO idk_kandidati
									(kandidat_check, kandidat_ime, kandidat_prezime, kandidat_email, kandidat_mobitel, kandidat_slika, kandidat_status, kandidat_status_messenger, kandidat_status_prijave, kandidat_datetime, kandidat_visitedurl, kandidat_prijava_na, kandidat_group, povezan_na_dipl, kandidat_porijeklo, kandidat_dipl_id )
								VALUES
									(:kandidat_check, :kandidat_ime, :kandidat_prezime, :kandidat_email, :kandidat_mobitel, :kandidat_slika, :kandidat_status, :kandidat_status_messenger, :kandidat_status_prijave, :kandidat_datetime, :kandidat_visitedurl, :kandidat_prijava_na, :kandidat_group, :povezan_na_dipl, :kandidat_porijeklo, :kandidat_dipl_id)");
		
				$query_add_user->execute(array(
							':kandidat_check' => $kandidat_check,
							':kandidat_ime' => $ime_new_ND_cand1,
							':kandidat_prezime' => $prezime_new_ND_cand1,
							':kandidat_email' => $email_new_ND_cand1,
							':kandidat_mobitel' => $mobilni_new_ND_cand1,
							':kandidat_slika' => "none",
							':kandidat_status' => 0,
							':kandidat_status_messenger' => 1,
							':kandidat_status_prijave' => 1,
							':kandidat_datetime' => date('Y-m-d H:i:s'),
							':kandidat_visitedurl' => 1,
							':kandidat_prijava_na' => "Ostalo",
							':kandidat_group' => 7,
							':povezan_na_dipl' => 1,
							':kandidat_porijeklo' => 4,
							':kandidat_dipl_id' => $kandidat_id_nd
							));
							
				$kandidat_id = $db->lastInsertId();
				//UPDATE ND_KANDIDATA STAVITI OVAJ ID 
				
				$query_log_status = $db->prepare("
						INSERT INTO idk_log_kandidat_statusi
							(lks_kandidat_id, lks_status_obrade, lks_status_messenger, lks_datetime)
						VALUES
							(:lks_kandidat_id, :lks_status_obrade, :lks_status_messenger, :lks_datetime)
				");
				
				$query_log_status->execute(array(
						':lks_kandidat_id' => $kandidat_id,
						':lks_status_obrade' => 0,
						':lks_status_messenger' => 1,
						':lks_datetime' => date('Y-m-d H:i:s')
				));
				
				$nalog_query = $db->prepare("
										SELECT project_id
										FROM idk_projects
										WHERE (project_name LIKE '%Kandidati sa stranice SveZaVizu%') ");
			
				$nalog_query->execute();
			
				$nalogrow = $nalog_query->fetch();
				
				$project_id = $nalogrow['project_id'];
				$query_project = $db->prepare("
								INSERT INTO idk_project_kandidati
									(pk_projectid, pk_kandidatid)
								VALUES
									(:pk_projectid, :pk_kandidatid)");

				$query_project->execute(array(
								':pk_projectid' => $project_id,
								':pk_kandidatid' => $kandidat_id));
								
				addToLogsStatusPrijave(NULL, $project_id, 1, $kandidat_id, 3);
				
				$kki_grupa = 1;
				$kki_naziv = "Mobilni";

				//Add mobilni to db
				$query_mob = $db->prepare("
								INSERT INTO idk_kandidat_kontakt_info
									(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id)
								VALUES
									(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id)");

				$query_mob->execute(array(
							':kki_grupa' => $kki_grupa,
							':kki_naziv' => $kki_naziv,
							':kki_podatak' => $mobilni_new_ND_cand1,
							':kki_kandidat_id' => $kandidat_id));
				
				$kki_grupa_e = 2;
				$kki_naziv_e = "E-mail";
				
				//Add kontakt info to db
				$query_email = $db->prepare("
								INSERT INTO idk_kandidat_kontakt_info
									(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id)
								VALUES
									(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id)");

				$query_email->execute(array(
							':kki_grupa' => $kki_grupa_e,
							':kki_naziv' => $kki_naziv_e,
							':kki_podatak' => $email_new_ND_cand1,
							':kki_kandidat_id' => $kandidat_id));
				
				//Add to table users (chatbot)
				$random_string = generateRandomString();
				$options = [
					'cost' => 10,
				];
				$random_password = password_hash($random_string, PASSWORD_BCRYPT, $options);
				
				$log_desc2 = "Kandidat: " . $ime_new_ND_cand1 . " " . $prezime_new_ND_cand1 . "(".$kandidat_id."). ; (".$random_string.")";
				$log_type2 = "5";
				addToLogs($log_desc2, $log_type2);
				
				$characters = '0123456789';
				$charactersLength = strlen($characters);
				$randomString = '';
				for ($i = 0; $i < 5; $i++) {
					$randomString .= $characters[rand(0, $charactersLength - 1)];
				}
				$bot_koriscnicko_ime = $ime_new_ND_cand1.$randomString;
				$kandidat_full_name = $ime_new_ND_cand1." ".$prezime_new_ND_cand1;
				$query_user = $db->prepare("
							INSERT INTO users
								(phone, name, nalog_id, email, password, kandidat_id)
							VALUES
								(:phone, :name, :nalog_id, :email, :password, :kandidat_id)");

				$query_user->execute(array(
							':phone' => $mobilni_new_ND_cand1,
							':name' => $kandidat_full_name,
							':nalog_id' => 44,
							':email' => $bot_koriscnicko_ime,
							':password' => $random_password,
							':kandidat_id' => $kandidat_id));
							
				//INFOBIP
				sendSmsToCandidateInfobip1($random_string, $mobilni_new_ND_cand1, "NN");
				sleep(1);  // Seconds
				sendSmsToCandidateInfobip2($random_string, $mobilni_new_ND_cand1, "NN");
				sleep(1);  // Seconds
				sendSmsToCandidateInfobip3($random_string, $mobilni_new_ND_cand1, $bot_koriscnicko_ime);
				sleep(1);  // Seconds
				sendSmsToCandidateInfobip4($random_string, $mobilni_new_ND_cand1, $bot_koriscnicko_ime);
				
				sendCandidateMessengerMail($random_string, $email_new_ND_cand1, $bot_koriscnicko_ime);
				
				//KRAJ DODAVANJA U TABELU KANDIDATI
			}
			else{
				$update_query_pov_dipl = $db->prepare("
						UPDATE idk_kandidati
						SET povezan_na_dipl = :povezan_na_dipl, kandidat_dipl_id = :kandidat_dipl_id
						WHERE kandidat_id = :kandidat_id
				");
				
				$update_query_pov_dipl->execute(array(
							':kandidat_id' => $kandidat_id_vec_u_kan,
							':povezan_na_dipl' => 1,
							':kandidat_dipl_id' => $kandidat_id_nd
							));
			}
	}
	else{
		//Provjera da li se nalazi u kandidatima
			$provjera_kandidati = $db->prepare("
							SELECT kandidat_id
							FROM idk_kandidati
							WHERE kandidat_mobitel = :kandidat_mobitel AND kandidat_status != :kandidat_status
							");
			$provjera_kandidati->execute(array(
									":kandidat_mobitel" => $kandidat_broj_tel,
									":kandidat_status" => 3
								));
			$kandidati_da_ne = $provjera_kandidati->rowCount();
			$provjera_kandidati_row = $provjera_kandidati->fetch();
			$kandidat_id_vec_u_kan = $provjera_kandidati_row['kandidat_id'];
			if($kandidati_da_ne != 0){
				$update_query_pov_dipl = $db->prepare("
						UPDATE idk_kandidati
						SET povezan_na_dipl = :povezan_na_dipl, kandidat_dipl_id = :kandidat_dipl_id
						WHERE kandidat_id = :kandidat_id
				");
				
				$update_query_pov_dipl->execute(array(
							':kandidat_id' => $kandidat_id_vec_u_kan,
							':povezan_na_dipl' => 1,
							':kandidat_dipl_id' => $kandidat_id_vec_u_dipl
							));
			}
		//Ako se kandidat nalazi u diplu
		$desila_se_prijava = insertPonovnePrijaveDIPL($kandidat_id_vec_u_dipl, $kandidat_ime, $kandidat_prezime, $kandidat_broj_tel, $kandidat_email, 2, 2, NULL);
		if($desila_se_prijava == 1){
			/*
			//Slanje maila starom menadzeru sa linkom kandidata njemu dodjeljenog
			$user_query = $db->prepare("
									SELECT employee_firstname, employee_lastname, employee_email
									FROM idk_employees
									WHERE employee_id = :employee_id");

			$user_query->execute(array(
							':employee_id' => $zaduzen_id_vec_u_dipl));

			$user = $user_query->fetch();

			$employee_firstname = $user['employee_firstname'];
			$employee_lastname = $user['employee_lastname'];
			$employee_email = $user['employee_email'];

			//Send email to user
			$mail_email = $employee_email;
			$mail_name = $employee_firstname . ' ' . $employee_lastname;
			$mail_subject = "Dipl modul - Stari kandidat";
			$mail_url = "" . getSiteUrlr() . "/nostrifikacija_diploma?page=otvori_ND_kandidata&id=".$kandidat_id_vec_u_dipl." ";
			$mail_body = "
							<p>Vaš stari kandidat u navedenom linku, pokušao se ponovno prijaviti za nostrifikaciju diplome putem linka na stranici Sve za vizu.</p>
							<p>Detalje pogledajte na linku: " . $mail_url . "</p>
			";
			$mail_altbody = "
							<p>Vaš stari kandidat u navedenom linku, pokušao se ponovno prijaviti za nostrifikaciju diplome putem linka na stranici Sve za vizu..</p>
							<p>Detalje pogledajte na linku: " . $mail_url . "</p>
			";
			
			sendEmail($mail_email, $mail_name, $mail_subject, $mail_body, $mail_altbody);
			*/
		}
	}
	header("Location: https://svezavizu.eu/");
	}else {
		header("Location: https://svezavizu.eu/error.php");
	}
break;
case "CompanyContact":
	if(!$_POST) exit;
	$company_name = $_POST['company_ime'];
	$company_entitet = $_POST['company_entitet'];
	$company_kontakt = $_POST['company_kontakt'];
	$company_tel = $_POST['company_tel'];
	$company_textarea = $_POST['company_textarea'];
	
	
	if(trim($company_name) == '') {
		echo '<div class="error_message">Morate unijeti Vaše ime.</div>';
		exit();
	} else if(trim($company_tel ) == '') {
		echo '<div class="error_message">Morate unijeti Vaše prezime.</div>';
		exit();
	} else if(!is_numeric($company_tel)) {
		echo '<div class="error_message">Telefonski broj može sadržavati samo brojeve.</div>';
		exit();
	} else if(trim($company_kontakt) == ''){
		$company_kontakt = "Nije navedeno";
		
	}else if(trim($company_textarea) == ''){
		$company_textarea = "Nema poruke";
	}else if(trim($company_entitet) == ''){
		$company_entitet = "Nije navedeno";
	}

	if(get_magic_quotes_gpc()) {
		$company_textarea = stripslashes($company_textarea);
	}
	
	$mail_referentDak = "i.suljic@wwtravel.net";
	$subject = "JOBSTEP - KOMPANIJA ŽELI STUPITI U KONTAKT";
	$body = 'Kompanija : <u>'.$company_name.'</u> je ispunila prijavu. <br><br>
			Bundesland: <b>'.$company_entitet.'</b><br>
			Kontakt osoba: <b>'.$company_kontakt.'</b><br>
			Poruka: <b>'.$company_textarea.'</b><br>
			Telefon: <b>'.$company_tel.'</b><br>
			</b><br><br> <i>JOBSTEP - sistem automatskog obavještavanja</i>';
	$alt = 'JOBSTEP - KOMPANIJA ŽELI STUPITI U KONTAKT';
	
	header("Location: https://job-step.de/");
break;

case "uplati_predracun":

	$pr_id = $_POST['pr_id'];
	$pr_kandidat_id = $_POST['pr_kandidat_id'];
	$bf = $_POST['bf'];
	$pr_datum_uplate = $_POST['pr_datum_uplate'];
	$query = $db->prepare("
					SELECT *
					FROM idk_predracuni
					WHERE pr_id = :pr_id");

	$query->execute(array(
					':pr_id' => $pr_id)); 
					
	$row = $query->fetch();
	
	$pr_datum_kreiranja = $row['pr_datum_kreiranja'];
	$pr_datum_kreiranja_plusmjesec = date('Y-m-d' ,strtotime($pr_datum_kreiranja ." +1 month"));
	$today = date('Y-m-d');
	$pr_rata = $row['pr_rata'];
	$pr_domaca_valuta = $row['pr_domaca_valuta'];
	
	if($pr_domaca_valuta == 'RSD'){
		$drzava = "Srbija";
	}elseif($pr_domaca_valuta == 'BAM'){
		$drzava = "BiH";
	}
	
	$month = date('m');
	$day = date('d');
	$year = date('Y');
	$year_skr = date('y');
	
	// POKUPI BROJ RATA ZA UPLATITI
	// DA BI SE KREIRAO NOVI PREDRACUN BROJ RATA MORA BITI VECI 2
	// DA BI SE KREIRAO NOVI PREDRACUN ZADNJI PREDRACUN KOJI IMA ZA TOG KANDIDATA KAD SE SABERE SA 1 MORA BITI MANJI
	// OD BROJA RATA
	
	$ugovor_pr = getVrstaUgovora($pr_kandidat_id); 
	$broj_rata = getBrojRataNDR($pr_kandidat_id);
	$zadnja_kreirana_rata = getZadnjaRataNDR($pr_kandidat_id);
	$rata_za_kreirati = $zadnja_kreirana_rata + 1;
	$rata_za_kreirati_tekst = 'rata'.$rata_za_kreirati.'';
	/*if($broj_rata > 2 AND $rata_za_kreirati < $broj_rata){
		if($pr_datum_kreiranja_plusmjesec < $today){  
		
			$brojac_predracuna = createBrojPredracuna($drzava);
			$iznos_rate = getIznosRate($ugovor_pr, $drzava, $rata_za_kreirati_tekst);
			
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
			
			$insert_predracun = $db->prepare("	
						INSERT INTO idk_predracuni	
						(pr_broj_predracuna,  pr_kandidat_id, pr_zaposlenik, pr_vrsta_predracuna, pr_rata, pr_domaca_valuta, pr_vrijednost_BAM, pr_vrijednost_RSD, pr_vrijednost_EUR, pr_file)	
						VALUES	
						(:pr_broj_predracuna,:pr_kandidat_id,:pr_zaposlenik,:pr_vrsta_predracuna,:pr_rata,:pr_domaca_valuta,:pr_vrijednost_BAM,	:pr_vrijednost_RSD,:pr_vrijednost_EUR,:pr_file)	
						");	
			$insert_predracun->execute(array(	
						':pr_broj_predracuna' => $novi_predracun,	
						':pr_kandidat_id' => $pr_kandidat_id,	
						':pr_zaposlenik' => $logged_employee_id,	
						':pr_vrsta_predracuna' => 1,	
						':pr_rata' => $rata_za_kreirati,	
						':pr_domaca_valuta' => $domaca_valuta,	
						':pr_vrijednost_BAM' => $rata_bam_f,	
						':pr_vrijednost_RSD' => $rata_rsd_f,	
						':pr_vrijednost_EUR' => $rata_eur_f,	
						':pr_file' => $file_name	
						));
			
			
			//Get last ID
			$predracun_id = $db->lastInsertId();
			
			if($drzava == "Srbija"){
				createPredracunSRB($predracun_id);
				$putanja_uplatnica = createUplatnicaSRB($predracun_id); 
				sendMailPredracunSRB($file_name, $putanja_uplatnica, $pr_kandidat_id);
			}
			else{
				createPredracunBIH($predracun_id);
				$putanja_uplatnica = createUplatnicaBIH($predracun_id);
				sendMailPredracunBIH($file_name, $putanja_uplatnica, $pr_kandidat_id);
			}
			
			//KREIRANJE NOVIH OBRACUNA
			ubaciObracune($predracun_id);
		
		}
	}*/
	
	// PROMJENA STATUSA UPLACENOG PREDRACUNA I UPDATE LOGA DA JE TO URADJENO
	$query = $db->prepare("
					UPDATE idk_predracuni
					SET	pr_uplaceno = :pr_uplaceno, pr_datum_uplate = :pr_datum_uplate, pr_status = :pr_status
					WHERE pr_id = :pr_id");

	$query->execute(array(
			':pr_id' => $pr_id,
			':pr_uplaceno' => 1,
			':pr_datum_uplate' => date("Y-m-d", strtotime($pr_datum_uplate)),
			':pr_status' => 2));
			
	$query_provjera = $db->prepare("
		SELECT pr_id
		FROM idk_predracuni
		WHERE pr_id = :pr_id AND pr_uplaceno = :pr_uplaceno
	");

	$query_provjera->execute(array(
		':pr_id' => $pr_id,
		':pr_uplaceno' => 1
	));
	
	if(($query_provjera->rowCount()) != 0){
		sendMailUplataPredracuna($pr_id);
		sendMailViberCheckListDIPL($pr_id);
	}
	
	// AKO JE PLACENA ZADNJA RATA ONDA TREBA KREIRATI RACUN
	if($bf != 'nemabf'){
		
		if($drzava == "Srbija"){
			// createRacunBIH($pr_kandidat_id);
			//fja za slanje maila za srbiju
		}
		else{
			$putanja_racuna = createRacunBIH($pr_kandidat_id, $bf);
			//fja za slanje maila za bih
			sendMailRacun($putanja_racuna);
		}
		
		
	} 
	
	
	// AKO JE PLACENA PRVA RADA OVO SE RADI, A TO JE UPDATE STATUSA KANDIDATA I LOG STATUSA
	if($pr_rata == 1){
		promjenaStatusaDIPLKandidat($pr_kandidat_id, 2, 1, 1);

		updateProjekcijeKandidat(1, $pr_kandidat_id);
	}
	
	$vr_rate_sati = date("H:i:s");
	$vr_rate_x = $pr_datum_uplate.' '.$vr_rate_sati;
	$rata_query = $db->prepare("
					INSERT INTO idk_nd_rate
						(id_nd_kan, br_r, vr_u_r)
					VALUES
						(:id_nd_kan, :br_r, :vr_u_r)");

	$rata_query->execute(array(
					':id_nd_kan' => $pr_kandidat_id,
					':br_r' => $pr_rata,
					':vr_u_r' => date("Y-m-d H:i:s", strtotime($vr_rate_x))
					));
	
	
	//UPLATA OBRACUNA fja sa id-em predracuna
	uplatiObracune($pr_id);
	
	//Add to LOGS
	$log_desc = "Označio da je naplaćena rata: " . $pr_id . " ";
	$log_date = date('Y-m-d H:i:s'); 

	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_employeeid, log_desc, log_date)
					VALUES
						(:log_employeeid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_employeeid' => $logged_employee_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date));


break;

case 'createRucnoPredracunDipl':
	
	include("html_pdf_generator.php");
	//Adis Novosti za CH START
	$pr_kandidat_id = $_POST['pr_kandidat_id'];
	$broj_rata = $_POST['pr_broj_rate_za_kreirati'];
	
	$query = $db->prepare("
		SELECT 
			* 
		FROM 
			idk_predracuni
		WHERE  
			pr_kandidat_id = :pr_kandidat_id 
			AND 
			pr_rata = 1
			AND 
			pr_status != 0
	");
	
	$query->execute(array(
		':pr_kandidat_id' => $pr_kandidat_id
	));
	
	$cnt = $query->rowCount();
	if($cnt != 0){
		$row = $query->fetch();
		$today = date('Y-m-d');
		$pr_domaca_valuta = $row['pr_domaca_valuta'];
		$pr_naplata_preko = intval($row['pr_naplata_preko']);
		//Naplata 1 - CH 2 - Stari nacin naplate
		if($pr_naplata_preko == 1){
			//******************************************************************************
			//GENERISANJE CH PREDRAČUNA START **********************************************
			//******************************************************************************
			
			$month = date('m');
			$day = date('d');
			$year = date('Y');
			$year_skr = date("y");
			
			$brojac_predracuna = createBrojPredracuna("ch");
			$novi_predracun = "DIPLCH-".$brojac_predracuna."-".$year_skr;
			$vrstaUgovora = getVrstaUgovora($pr_kandidat_id); 
			$valutaPredracuna = $pr_domaca_valuta;
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
			
			//iznos dobijam preko fje u markama  i onda gledam drzavu klijenta i onda prebacivam valute
			$iznos_rate = getIznosRate($vrstaUgovora, "ch", "rata".$broj_rata."");
			
			$rata_eur = $iznos_rate / $EUR;
			$rata_rsd = 100*$iznos_rate / $RSD;
			
			$rata_bam_f = number_format((float)$iznos_rate, 2, '.', '');
			$rata_eur_f = number_format((float)$rata_eur, 2, '.', '');
			$rata_rsd_f = number_format((float)$rata_rsd, 2, '.', '');
			
			$slovo_drz = "";
			$domaca_valuta = "";
			$jezik_ugovora = "";
			$drzava = "";
			
			if($valutaPredracuna == "BAM"){
				$drzava = "BiH"; 
				$slovo_drz = "B";
				$domaca_valuta = "BAM";
				$jezik_ugovora = "bs";
			}elseif($valutaPredracuna == "RSD"){
				$drzava = "Srbija";
				$slovo_drz = "S";
				$domaca_valuta = "RSD";
				$jezik_ugovora = "sr";
			}elseif($valutaPredracuna == "EUR"){
				$drzava = "Njemacka";
				$slovo_drz = "D";
				$domaca_valuta = "EUR";
				$jezik_ugovora = "de";
			}else{
				$drzava = "";
				$slovo_drz = "";
				$domaca_valuta = "";
				$jezik_ugovora = "";
			}
			
			if($slovo_drz != "" AND $domaca_valuta != "" AND $jezik_ugovora != "" AND $drzava != ""){
			
				$file_datum = date('YmdHis'); 
				$predracun_putanja = $file_datum."DIPL".$slovo_drz.$brojac_predracuna.".pdf";
				$insert_predracun = $db->prepare("	
					INSERT INTO idk_predracuni	
						(
							pr_broj_predracuna, 
							pr_naplata_preko, 
							pr_kandidat_id, 
							pr_datum_kreiranja, 
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
							:pr_datum_kreiranja,
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
					':pr_naplata_preko' => 1,
					':pr_kandidat_id' => $pr_kandidat_id,
					':pr_datum_kreiranja' => date("Y-m-d H:i:s"),
					':pr_zaposlenik' => $logged_employee_id,
					':pr_vrsta_predracuna' => 1,
					':pr_rata' => $broj_rata,
					':pr_domaca_valuta' => $domaca_valuta,
					':pr_vrijednost_BAM' => $rata_bam_f,
					':pr_vrijednost_RSD' => $rata_rsd_f,
					':pr_vrijednost_EUR' => $rata_eur_f
				));
				
				$predracun_id = $db->lastInsertId();
				
				//createPredracun($predracun_id, $drzava);
				//Adis Komentarisao Generisanje na Prihvati START --------------------------------------
					if($drzava != "Njemacka"){
						generisiPredracun($predracun_id, $drzava);
						generisiPredracun($predracun_id, "Njemacka");
					}else{
						generisiPredracun($predracun_id, "Njemacka");
					}
				//Adis Komentarisao Generisanje na Prihvati END -------------------------------------- 
				// var_dump("vratilo se na zivote");
				// exit();
				//tekst za ratu START
				$text_rata = "";
				$text_rata_sms = $text_rata;
				switch($broj_rata){
					case 2:
						if($drzava == "Njemacka"){
							$text_rata = "zweite";
							$text_rata_sms = $text_rata;
						}else{
							$text_rata = "druge";
							$text_rata_sms = $text_rata;
						}
					break;
					case 3:
						if($drzava == "Njemacka"){
							$text_rata = "dritte";
							$text_rata_sms = $text_rata;
						}else{
							$text_rata = "treće";
							$text_rata_sms = "trece";
						}
					break;
					case 4:
						if($drzava == "Njemacka"){
							$text_rata = "vierte";
							$text_rata_sms = $text_rata;
						}else{
							$text_rata = "četvrte";
							$text_rata_sms = "cetvrte";
						}
					break;
					case 5:
						if($drzava == "Njemacka"){
							$text_rata = "fünfte";
							$text_rata_sms = $text_rata;
						}else{
							$text_rata = "pete";
							$text_rata_sms = $text_rata;
						}
					break;
					case 6:
						if($drzava == "Njemacka"){
							$text_rata = "sechste";
							$text_rata_sms = $text_rata;
						}else{
							$text_rata = "šeste";
							$text_rata_sms = "seste";
						}
					break;
					case 7:
						if($drzava == "Njemacka"){
							$text_rata = "siebte";
							$text_rata_sms = $text_rata;
						}else{
							$text_rata = "sedme";
							$text_rata_sms = $text_rata;
						}
					break;
					case 8:
						if($drzava == "Njemacka"){
							$text_rata = "achte";
							$text_rata_sms = $text_rata;
						}else{
							$text_rata = "osme";
							$text_rata_sms = $text_rata;
						}
					break;
					case 9:
						if($drzava == "Njemacka"){
							$text_rata = "neunte";
							$text_rata_sms = $text_rata;
						}else{
							$text_rata = "devete";
							$text_rata_sms = $text_rata;
						}
					break;
					case 10:
						if($drzava == "Njemacka"){
							$text_rata = "zehnte";
							$text_rata_sms = $text_rata;
						}else{
							$text_rata = "desete";
							$text_rata_sms = $text_rata;
						}
					break;
					case 11:
						if($drzava == "Njemacka"){
							$text_rata = "elfte";
							$text_rata_sms = $text_rata;
						}else{
							$text_rata = "jedanaeste";
							$text_rata_sms = $text_rata;
						}
					break;
					case 12:
						if($drzava == "Njemacka"){
							$text_rata = "zwölfte";
							$text_rata_sms = $text_rata;
						}else{
							$text_rata = "dvanaeste";
							$text_rata_sms = $text_rata;
						}
					break;
				}
				//tekst za ratu END
				
				//ugovor link query START
				$query_ugovor_info = $db->prepare("
					SELECT 
						ug.ug_token, 
						ug.ug_jezik
					FROM 
						idk_nd_ugovori ug
					WHERE 
						ug.ug_status = 2
						AND 
						ug.ug_kandidat_id = :kanId
				");
				$query_ugovor_info->execute(array(
					':kanId' => $pr_kandidat_id
				));
				$row_ugovor_info = $query_ugovor_info->fetch();
				$tokenUgovora = $row_ugovor_info["ug_token"];
				$jezikUgovora = $row_ugovor_info["ug_jezik"];
				$ugovor_link = getSiteUrlr()."ugovor/".$tokenUgovora."/".$jezikUgovora;
				//ugovor link query END 
				
				$poruka_text_viber 	= "";
				$poruka_text_sms 	= "";
				$poruka_button		= "";
				//na mail ide $predracun_putanja, $putanja_uplatnica, $ugovor_link, odvojeni mailovi za srb i bih
				if($drzava == "Srbija"){

					createUplatnicaSRB($predracun_id);
					$poruka_text_mail	= "Pozdrav! 🤗<br><br>Vreme je za uplatu Vaše *".$text_rata." rate*! Predračun i elektronsku uplatnicu možete pregledati i preuzeti klikom na link ispod.<br><br>Hvala Vam što ste deo naše priče! Za sve informacije, tu smo za Vas. <br><a href='".$ugovor_link."'> LINK</a>";
					$poruka_text_viber	= "Pozdrav! 🤗\n\nVreme je za uplatu Vaše *".$text_rata." rate*! Predračun i elektronsku uplatnicu možete pregledati i preuzeti klikom na dugme ispod.\n\nHvala Vam što ste deo naše priče! Za sve informacije, tu smo za Vas.\n\nJSI Team";
					$poruka_text_sms	= "Pozdrav! 🤗\n\nVreme je za uplatu Vase *".$text_rata_sms." rate*! Predracun i elektronsku uplatnicu mozete pregledati i preuzeti klikom na link ispod.\n\nHvala Vam sto ste deo nase price! Za sve informacije, tu smo za Vas.\n\nJSI Team\n\n".$ugovor_link;
					$poruka_button 		= "Dokumenti";
					
				}elseif($drzava == "BiH"){

					createUplatnicaBIH($predracun_id);
					$poruka_text_mail	= "Pozdrav! 🤗<br><br>Vrijeme za uplatu Vaše *".$text_rata." rate*! Predračun i elektronsku uplatnicu možete pregledati i preuzeti klikom na link ispod.<br><br>Hvala Vam što ste dio naše priče! Za sve informacije, tu smo za Vas. <br><a href='".$ugovor_link."'> LINK</a>";
					$poruka_text_viber	= "Pozdrav! 🤗\n\nVrijeme za uplatu Vaše *".$text_rata." rate*! Predračun i elektronsku uplatnicu možete pregledati i preuzeti klikom na dugme ispod.\n\nHvala Vam što ste dio naše priče! Za sve informacije, tu smo za Vas.\n\nJSI Team";
					$poruka_text_sms	= "Pozdrav! 🤗\n\nVrijeme za uplatu Vase *".$text_rata_sms." rate*! Predracun i elektronsku uplatnicu mozete pregledati i preuzeti klikom na link ispod.\n\nHvala Vam sto ste dio nase price! Za sve informacije, tu smo za Vas.\n\nJSI Team\n\n".$ugovor_link;
					$poruka_button 		=  "Dokumenti";
					
				}else{
					
					generisiInoUplatnicu($predracun_id);
					$poruka_text_mail	= "Schöne Grüße!\n\nEs ist Zeit Ihre *".$text_rata." Rate zu zahlen!* Sie können die Pro-forma-Rechnung und den elektronischen Einzahlungsschein durchlesen, sowie herunterladen, indem Sie auf die Schaltfläche unten klicken.\n\nDanke, dass Sie ein Teil unserer Geschichte sind! \n\nFür weitere Informationen stehen wir jederzeit gerne zur Verfügung. <br><a href='".$ugovor_link."'> LINK</a>";
					$poruka_text_viber	= "Schöne Grüße!\n\nEs ist Zeit Ihre *".$text_rata." Rate zu zahlen!* Sie können die Pro-forma-Rechnung und den elektronischen Einzahlungsschein durchlesen, sowie herunterladen, indem Sie auf die Schaltfläche unten klicken.\n\nDanke, dass Sie ein Teil unserer Geschichte sind! \n\nFür weitere Informationen stehen wir jederzeit gerne zur Verfügung.\n\n";
					$poruka_text_sms	= "Schöne Grüße!\n\nEs ist Zeit Ihre *".$text_rata." Rate zu zahlen!* Sie können die Pro-forma-Rechnung und den elektronischen Einzahlungsschein durchlesen, sowie herunterladen, indem Sie auf die Schaltfläche unten klicken.\n\nDanke, dass Sie ein Teil unserer Geschichte sind! \n\nFür weitere Informationen stehen wir jederzeit gerne zur Verfügung.\n\n".$ugovor_link;
					$poruka_button 		= "Unterlagen";
					//ako kandidat nije ni iz BiH onda ide info o ino uplatama, maybe, ceka se odg od Sabine
				}
				$subject_ugovor = "";
				sendViberUgovorLink($pr_kandidat_id, $poruka_text_viber, $poruka_text_sms, $ugovor_link, $poruka_button);
				sendMailUgovorPredracun($pr_kandidat_id, $poruka_text_mail, $ugovor_link, $subject_ugovor);
				//Add to LOGS
				$log_desc = "RUČNO CH KREIRANJE PREDRAČUNA: Kreiran predračun ID = [".$predracun_id."] za ratu rata br. ".$broj_rata.". Kandidat ID = [".$pr_kandidat_id."]";
				
				//KREIRANJE NOVIH OBRACUNA
				ubaciObracune($predracun_id);
				
			}else{
				$log_desc = "RUČNO CH KREIRANJE PREDRAČUNA: Greška - problem za ključnim informacijama za predračun kod kandidata ID = [" . $pr_kandidat_id . "]. ";
			}
			//******************************************************************************
			//GENERISANJE CH PREDRAČUNA END   **********************************************
			//******************************************************************************
		} else if ($pr_naplata_preko == 2) {
			/*
				Mix način - digitalno / drzava domacin START 
				*/
					$month = date('m');
					$day = date('d');
					$year = date('Y');
					$year_skr = date("y");

					$drzava = "Srbija";
					$brojac_predracuna = createBrojPredracuna($drzava);//
					$vrstaUgovora = getVrstaUgovora($pr_kandidat_id);//
					$iznos_rate = getIznosRate($vrstaUgovora, $drzava, "rata".$broj_rata); //

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

					if($slovo_drz != "" AND $domaca_valuta != "" AND $jezik_ugovora != "" AND $drzava != ""){
						$file_datum = date('YmdHis'); 
						$predracun_putanja = $file_datum."DIPL".$slovo_drz.$brojac_predracuna.".pdf";
						$novi_predracun = "DIPL".$slovo_drz."-".$brojac_predracuna."-".$year_skr;
						/*
							Insert predracuna START 
							*/
								$datum_kreiranja_predracuna = date("Y-m-d H:i:s");

								$insert_predracun = $db->prepare("	
								INSERT INTO idk_predracuni	
									(
										pr_broj_predracuna, 
										pr_naplata_preko, 
										pr_kandidat_id,
										pr_datum_kreiranja, 
										pr_zaposlenik, 
										pr_vrsta_predracuna, 
										pr_rata, 
										pr_domaca_valuta, 
										pr_vrijednost_BAM, 
										pr_vrijednost_RSD, 
										pr_vrijednost_EUR,
										pr_file
									)	
								VALUES	
									(
										:pr_broj_predracuna,
										:pr_naplata_preko,
										:pr_kandidat_id,
										:pr_datum_kreiranja,
										:pr_zaposlenik,
										:pr_vrsta_predracuna,
										:pr_rata,
										:pr_domaca_valuta,
										:pr_vrijednost_BAM,
										:pr_vrijednost_RSD,
										:pr_vrijednost_EUR,
										:pr_file
									)	
								");	
								
								$insert_predracun->execute(array(	
									':pr_broj_predracuna' => $novi_predracun,
									':pr_naplata_preko' => $pr_naplata_preko,
									':pr_kandidat_id' => $pr_kandidat_id,
									':pr_datum_kreiranja' => $datum_kreiranja_predracuna,
									':pr_zaposlenik' => $logged_employee_id,
									':pr_vrsta_predracuna' => 1,
									':pr_rata' => $broj_rata,
									':pr_domaca_valuta' => $domaca_valuta,
									':pr_vrijednost_BAM' => $rata_bam_f,
									':pr_vrijednost_RSD' => $rata_rsd_f,
									':pr_vrijednost_EUR' => $rata_eur_f,
									':pr_file' => $predracun_putanja
								));

								$predracun_id = $db->lastInsertId();

								/*
									Generisanje predracuna start
									*/
									createRucnoPredracunSRB($predracun_id, $datum_kreiranja_predracuna);
									/*
									Generisanje predracuna end	
								*/
								//Tekst za ratu Start
									$text_rata = "";
									$text_rata_sms = $text_rata;
									switch($broj_rata){
										case 2:
											if($drzava == "Njemacka"){
												$text_rata = "zweite";
												$text_rata_sms = $text_rata;
											}else{
												$text_rata = "druge";
												$text_rata_sms = $text_rata;
											}
										break;
										case 3:
											if($drzava == "Njemacka"){
												$text_rata = "dritte";
												$text_rata_sms = $text_rata;
											}else{
												$text_rata = "treće";
												$text_rata_sms = "trece";
											}
										break;
										case 4:
											if($drzava == "Njemacka"){
												$text_rata = "vierte";
												$text_rata_sms = $text_rata;
											}else{
												$text_rata = "četvrte";
												$text_rata_sms = "cetvrte";
											}
										break;
										case 5:
											if($drzava == "Njemacka"){
												$text_rata = "fünfte";
												$text_rata_sms = $text_rata;
											}else{
												$text_rata = "pete";
												$text_rata_sms = $text_rata;
											}
										break;
										case 6:
											if($drzava == "Njemacka"){
												$text_rata = "sechste";
												$text_rata_sms = $text_rata;
											}else{
												$text_rata = "šeste";
												$text_rata_sms = "seste";
											}
										break;
										case 7:
											if($drzava == "Njemacka"){
												$text_rata = "siebte";
												$text_rata_sms = $text_rata;
											}else{
												$text_rata = "sedme";
												$text_rata_sms = $text_rata;
											}
										break;
										case 8:
											if($drzava == "Njemacka"){
												$text_rata = "achte";
												$text_rata_sms = $text_rata;
											}else{
												$text_rata = "osme";
												$text_rata_sms = $text_rata;
											}
										break;
										case 9:
											if($drzava == "Njemacka"){
												$text_rata = "neunte";
												$text_rata_sms = $text_rata;
											}else{
												$text_rata = "devete";
												$text_rata_sms = $text_rata;
											}
										break;
										case 10:
											if($drzava == "Njemacka"){
												$text_rata = "zehnte";
												$text_rata_sms = $text_rata;
											}else{
												$text_rata = "desete";
												$text_rata_sms = $text_rata;
											}
										break;
										case 11:
											if($drzava == "Njemacka"){
												$text_rata = "elfte";
												$text_rata_sms = $text_rata;
											}else{
												$text_rata = "jedanaeste";
												$text_rata_sms = $text_rata;
											}
										break;
										case 12:
											if($drzava == "Njemacka"){
												$text_rata = "zwölfte";
												$text_rata_sms = $text_rata;
											}else{
												$text_rata = "dvanaeste";
												$text_rata_sms = $text_rata;
											}
										break;
									}
								//tekst za ratu END

								//ugovor link query START
									$query_ugovor_info = $db->prepare("
										SELECT 
											ug.ug_token, 
											ug.ug_jezik
										FROM 
											idk_nd_ugovori ug
										WHERE 
											ug.ug_status = 2
											AND 
											ug.ug_kandidat_id = :kanId
									");
									$query_ugovor_info->execute(array(
										':kanId' => $pr_kandidat_id
									));
									$row_ugovor_info = $query_ugovor_info->fetch();
									$tokenUgovora = $row_ugovor_info["ug_token"];
									$jezikUgovora = $row_ugovor_info["ug_jezik"];
									$ugovor_link = getSiteUrlr()."ugovorNew/".$tokenUgovora."/".$jezikUgovora;
									//ugovor link query END 
									
									$poruka_text_viber 	= "";
									$poruka_text_sms 	= "";
									$poruka_button		= "";

									createUplatnicaSRB($predracun_id);
									$poruka_text_mail	= "Pozdrav! 🤗<br><br>Vreme je za uplatu Vaše *".$text_rata." rate*! Predračun i elektronsku uplatnicu možete pregledati i preuzeti klikom na link ispod.<br><br>Hvala Vam što ste deo naše priče! Za sve informacije, tu smo za Vas. <br><a href='".$ugovor_link."'> LINK</a>";
									$poruka_text_viber	= "Pozdrav! 🤗\n\nVreme je za uplatu Vaše *".$text_rata." rate*! Predračun i elektronsku uplatnicu možete pregledati i preuzeti klikom na dugme ispod.\n\nHvala Vam što ste deo naše priče! Za sve informacije, tu smo za Vas.\n\nJSI Team";
									$poruka_text_sms	= "Pozdrav! 🤗\n\nVreme je za uplatu Vase *".$text_rata_sms." rate*! Predracun i elektronsku uplatnicu mozete pregledati i preuzeti klikom na link ispod.\n\nHvala Vam sto ste deo nase price! Za sve informacije, tu smo za Vas.\n\nJSI Team\n\n".$ugovor_link;
									$poruka_button 		= "Dokumenti";

										
									$subject_ugovor = "";
									sendViberUgovorLink($pr_kandidat_id, $poruka_text_viber, $poruka_text_sms, $ugovor_link, $poruka_button);
									sendMailUgovorPredracun($pr_kandidat_id, $poruka_text_mail, $ugovor_link, $subject_ugovor, $pr_naplata_preko);
									//Add to LOGS
									$log_desc = "RUČNO DIGITALNO/DOMACIN DRZAVA KREIRANJE PREDRAČUNA: Kreiran predračun ID = [".$predracun_id."] za ratu rata br. ".$broj_rata.". Kandidat ID = [".$pr_kandidat_id."]";
									
									//KREIRANJE NOVIH OBRACUNA
									// ubaciObracune($predracun_id);
								
							/*
							Insert predracuna END 
						*/

					}else{
						$log_desc = "RUČNO  DIGITALNO/DOMACIN DRZAVA KREIRANJE PREDRAČUNA: Greška - problem za ključnim informacijama za predračun kod kandidata ID = [" . $pr_kandidat_id . "]. ";
					}
				/*
				Mix način - digitalno / drzava domacin START 
			*/
		} else{
			if($pr_domaca_valuta == 'RSD'){
				$drzava = "Srbija";
				$country = 2;
			}elseif($pr_domaca_valuta == 'BAM'){
				$drzava = "BiH";
				$country = 1;
			}
			
			$month = date('m');
			$day = date('d');
			$year = date('Y');
			$year_skr = date('y');
			
			// POKUPI BROJ RATA ZA UPLATITI
			// DA BI SE KREIRAO NOVI PREDRACUN BROJ RATA MORA BITI VECI 2
			// DA BI SE KREIRAO NOVI PREDRACUN ZADNJI PREDRACUN KOJI IMA ZA TOG KANDIDATA KAD SE SABERE SA 1 MORA BITI MANJI
			// OD BROJA RATA
			
			$ugovor_pr = getVrstaUgovora($pr_kandidat_id); 
			// $broj_rata = getBrojRataNDR($pr_kandidat_id);
			$rata_za_kreirati = $broj_rata;
			$rata_za_kreirati_tekst = 'rata'.$rata_za_kreirati.'';
				
			$brojac_predracuna = createBrojPredracuna($drzava);
			$iznos_rate = getIznosRate($ugovor_pr, $drzava, $rata_za_kreirati_tekst);
					
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
					
			$insert_predracun = $db->prepare("	
						INSERT INTO idk_predracuni	
						(pr_broj_predracuna,  pr_kandidat_id, pr_zaposlenik, pr_vrsta_predracuna, pr_rata, pr_domaca_valuta, pr_vrijednost_BAM, pr_vrijednost_RSD, pr_vrijednost_EUR, pr_file)	
						VALUES	
						(:pr_broj_predracuna,:pr_kandidat_id,:pr_zaposlenik,:pr_vrsta_predracuna,:pr_rata,:pr_domaca_valuta,:pr_vrijednost_BAM,	:pr_vrijednost_RSD,:pr_vrijednost_EUR,:pr_file)	
						");	
			$insert_predracun->execute(array(	
						':pr_broj_predracuna' => $novi_predracun,	
						':pr_kandidat_id' => $pr_kandidat_id,	
						':pr_zaposlenik' => $logged_employee_id,	
						':pr_vrsta_predracuna' => 1,	
						':pr_rata' => $rata_za_kreirati,	
						':pr_domaca_valuta' => $domaca_valuta,	
						':pr_vrijednost_BAM' => $rata_bam_f,	
						':pr_vrijednost_RSD' => $rata_rsd_f,	
						':pr_vrijednost_EUR' => $rata_eur_f,	
						':pr_file' => $file_name	
						));

			//Get last ID
			$predracun_id = $db->lastInsertId();
			
			if($drzava == "Srbija"){
				createPredracunSRB($predracun_id);
				$putanja_uplatnica = createUplatnicaSRB($predracun_id); 
				sendMailPredracunSRB($file_name, $putanja_uplatnica, $pr_kandidat_id);
			}
			else{
				createPredracunBIH($predracun_id);
				$putanja_uplatnica = createUplatnicaBIH($predracun_id);
				if($ugovor_pr == 11 OR $ugovor_pr == 12){
					sendMailPredracunUgovorBIH("ne", $putanja_ugovor, "ne", $id_nd_kandidata_comun_new);
				}else{
					sendMailPredracunBIH($file_name, $putanja_uplatnica, $pr_kandidat_id);
				}
			}
			
			//KREIRANJE NOVIH OBRACUNA
			ubaciObracune($predracun_id);
			
			$log_desc = "RUČNO KREIRANJE PREDRAČUNA: Kreiran predračun ID = [".$predracun_id."] za ratu rata br. ".$broj_rata.". Kandidat ID = [".$pr_kandidat_id."]";
		}
	}else{
		$log_desc = "RUČNO KREIRANJE PREDRAČUNA: Greška - query za provjeru podataka nije našao rezultata za kandidata ID = [" . $pr_kandidat_id . "]. ";
	}
	
	
	//UPLATA OBRACUNA fja sa id-em predracuna
	//NE IDE OVDJE UPLATA OBRACUNA!!??
	
	//Add to LOGS
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
	
	header("Location: nostrifikacija_diploma?page=otvori_ND_kandidata&id=".$pr_kandidat_id);
	
break;

case "kandidat_go_online":
	
	$kandidat_id_go = $_POST['kandidat_id_go'];
	$stari_project_go = $_POST['stari_project_go'];
	$nalog_id_go = $_POST['nalog_id_go'];
	
	//mijenjane statusa prijave i premjestanje u projekat
	$upd_status_prijave = $db->prepare("UPDATE idk_kandidati SET kandidat_status_prijave = 3 WHERE kandidat_id = $kandidat_id_go");
	$upd_status_prijave->execute();
	
	$del_project = $db->prepare("DELETE FROM idk_project_kandidati WHERE pk_projectid = $stari_project_go AND pk_kandidatid = $kandidat_id_go");
	$del_project->execute();
	
	if($nalog_id_go == 217 OR $nalog_id_go == 218 OR $nalog_id_go == 219 OR $nalog_id_go == 220){
		$nalog_intervju = 222;
	}else{
		$nalog_intervju = $nalog_id_go;
	}
	$get_new_project = $db->prepare("SELECT project_id FROM idk_projects WHERE project_nalogid = $nalog_intervju AND project_name LIKE '%Intervju%'");
	$get_new_project->execute();
	$row_new_project = $get_new_project->fetch();
	$new_project_id = $row_new_project['project_id'];
	$query = $db->prepare("
					INSERT INTO idk_project_kandidati
						(pk_projectid, pk_kandidatid)
					VALUES
						(:pk_projectid, :pk_kandidatid)");

	$query->execute(array(
					':pk_projectid' => $new_project_id,
					':pk_kandidatid' => $kandidat_id_go
	));
	$status_id = 3;
	$izvor = 1;
	addToLogsStatusPrijave($stari_project_go, $new_project_id, $status_id, $kandidat_id_go, $izvor);
	header("Location: casting?page=open&id=".$stari_project_go);
	
break;

case "upload_document":
	$kandidat_id 		= $_POST['kandidat_id'];
	$nalog_id 			= $_POST['nalog_id'];
	$stari_status_id 	= $_POST['status_id'];
	$vrsta_dokumenta	= $_POST['dokument_select'];
	$glossa_slanje 		= $_POST['glossa_slanje'];
	$uploaded_document 	= $_FILES['kandidat_dokument'];
	$vrijeme 			= date("y-m-d_H:i");
	$ext 				= explode(".", $uploaded_document['name']);
	$ext 				= strtolower(end($ext));
	$file_name 			= "UG".$kandidat_id.$vrijeme.".".$ext;
	$izvor				= 1;
	
	if($glossa_slanje == 3){
		$query = $db->prepare("UPDATE idk_kandidati SET kandidat_glossa = 3 WHERE kandidat_id = :kandidat_id");
		$query->execute(array(
			':kandidat_id' => $kandidat_id
		));
	}

	$dateReceiving = $_POST['date_receiving'];

	if ($dateReceiving != "") {
	
		updateStatusContractSentR($kandidat_id, 2, $dateReceiving);
		updateStatusContractSentR($kandidat_id, 3);

	}

	if ($vrsta_dokumenta == 1) {

		$location = $_SERVER['DOCUMENT_ROOT']."/jobstep_pp/files/candidate_contracts/";
		if(!move_uploaded_file($uploaded_document["tmp_name"], $location.$file_name)) {

			http_response_code(500);
			die("File could not be uploaded!");

		}

		if ($stari_status_id == 7) {

			$vrsta_ugovora 	= 0;
			$novi_status_id = 8;
			$reminder_type 	= 5;

		} else {

			$vrsta_ugovora 	= 1;
			$novi_status_id = 9;
			$reminder_type	= 7;

			// prelazak na Potpisan ugovor gasi task force za Task force - Potpisan ugovor (TimeUp for Poslan ugovor)
			updateLastActiveTaskForCandidate($kandidat_id, 10);
		}

		$query_get_partner = $db->prepare("
			SELECT ppa_id
			FROM idk_pp_partners

			JOIN idk_kandidati
			ON idk_kandidati.kandidat_ppa_partner_id = idk_pp_partners.ppa_id 

			WHERE kandidat_id = :kandidat_id
		");

		$query_get_partner->execute(array(
			":kandidat_id" => $kandidat_id
		));

		$result_partner = $query_get_partner->fetch();
		$partner_id 	= $result_partner['ppa_id'];
		
		$query_get_project = $db->prepare("
			SELECT project_id 
			FROM idk_projects
				
			JOIN idk_project_kandidati
			ON idk_projects.project_id = idk_project_kandidati.pk_projectid
				
			WHERE idk_project_kandidati.pk_kandidatid = :kandidat_id
			AND idk_projects.project_nalogid = :nalog_id
				
			ORDER BY idk_projects.project_id DESC
		");

		$query_get_project->execute(array(
			":kandidat_id" => $kandidat_id,
			":nalog_id"	   => $nalog_id
		));

		$result_project = $query_get_project->fetch();
		$project_id = $result_project['project_id'];
		
		$query_insert_contract = $db->prepare("
			INSERT INTO idk_kandidati_contracts
			(
				kc_file_name, 
				kc_candidate_id, 
				kc_source, 
				kc_user_id, 
				kc_nalog_id, 
				kc_partner_id, 
				kc_signed, 
				kc_visibility_status
			)
			
			VALUES (
				:file_name,
				:candidate_id,
				:source,
				:user_id,
				:nalog_id,
				:partner_id,
				:signed,
				:visibility_status
			)
		");

		$query_insert_contract->execute(array(
			":file_name" 		 => $file_name,
			":candidate_id" 	 => $kandidat_id,
			":source" 			 => 1,
			":user_id" 			 => $logged_employee_id,
			":nalog_id" 		 => $nalog_id,
			":partner_id" 		 => $partner_id,
			":signed" 			 => $vrsta_ugovora,
			":visibility_status" => 2
		));
		
		$query_update_kandidat_sp = $db->prepare("
			UPDATE idk_kandidati
			SET kandidat_status_prijave = :status_prijave
			WHERE kandidat_id = :kandidat_id
		");

		$query_update_kandidat_sp->execute(array(
			":status_prijave" => $novi_status_id,
			":kandidat_id" 	  => $kandidat_id
		));

		addToLogsStatusPrijave($project_id, $project_id, $novi_status_id, $kandidat_id, $izvor);
		updateReminderStatusCRM($kandidat_id, $reminder_type);	

		if ($novi_status_id == 9) {

			generisiRateZaKandidata($kandidat_id, $nalog_id);

			$tip_ugovora = "potpisan";

			try {

				$preko = 1;
				$resultPrebacivanja = prebaciNaPrikupljanjeDokumentacije($kandidat_id,$preko);
				
				if ($resultPrebacivanja == 2) $headerLocation = "kandidati?page=open&id=".$kandidat_id."&infoNost=28";
				else $headerLocation = "kandidati?page=open&id=".$kandidat_id;

			} catch(Exception $e) {

				$resultPrebacivanja = $e->getMessage();
				$headerLocation = "nalozi?page=open_status_prijave&sid=" . $stari_status_id . "&nid="  .$nalog_id;

			}

			$log_automatsko_prebacivanje = "Status automatskog prebacivanja na prikupljanje dokumentacije: " . $resultPrebacivanja;
			
		} else {

			$tip_ugovora = "nepotpisan";
			$log_automatsko_prebacivanje = "";
			$headerLocation = "nalozi?page=open_status_prijave&sid=" . $stari_status_id . "&nid=" . $nalog_id;

		}

		$log_desc = "Zaposlenik uploadao ".$tip_ugovora." ugovor za  kandidata (".$kandidat_id."). ".$log_automatsko_prebacivanje."";
		$log_type = 0;
		addToLogs($log_desc, $log_type);

		header("Location: $headerLocation");
	} else {
		$location = $_SERVER['DOCUMENT_ROOT']."/jobstep_pp/files/candidate_documents/";
	}
	 
break;
case "documentsIncomplete":
	header("Content-Type: application/json");

	$kandidat_id = $_REQUEST["kandidat_id"];
	$incomplete_id = $_REQUEST["incomplete_id"];
	if(!$kandidat_id)
	{
		http_response_code(500);
		die("Missing kandidat_id query parameter!");
	}
	if(!$incomplete_id)
	{
		http_response_code(500);
		die("Missing incomplete_id query parameter!");
	}

	$nalog_id   = getNalogIdByCandidateId($kandidat_id);

	// Required dokumenti za nalog
	$nalog_docs = getNalogDocs($nalog_id, $kandidat_id);
    $nalog_docs = [];

	// Required dokumenti za kandidata
	$kandidat_docs = getKandidatDocs($nalog_id, $kandidat_id, $incomplete_id);

	//addStatusesToDocument($nalog_docs, $kandidat_id);
	addStatusesToDocument($kandidat_docs, $kandidat_id);

	echo json_encode($kandidat_docs);

	break;
case "documents":
	header("Content-Type: application/json");

	$kandidat_id = $_REQUEST["kandidat_id"];
	if(!$kandidat_id)
	{
		http_response_code(500);
		die("Missing kandidat_id query parameter!");
	}

	$nalog_id   = getNalogIdByCandidateId($kandidat_id);

	// Required dokumenti za nalog
	$nalog_docs = getNalogDocs($nalog_id, $kandidat_id);

	// Required dokumenti za kandidata
	//$kandidat_docs = getKandidatDocs($nalog_id, $kandidat_id);
	$kandidat_docs = [];

	addStatusesToDocument($nalog_docs, $kandidat_id);
	addStatusesToDocument($kandidat_docs, $kandidat_id);

	$all_documents = array_merge($nalog_docs, $kandidat_docs);

	echo json_encode($all_documents);

	break;
case "changeDocumentStatus":
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: GET, POST');
	header("Access-Control-Allow-Headers: X-Requested-With");

	$kandidat_id            = $_REQUEST["kandidat_id"] ?? null;
	$nalog_id               = $_REQUEST["nalog_id"] ?? null;
	$new_status_id          = $_REQUEST["new_status_id"] ?? null;
    $status_comment         = $_REQUEST["status_comment"] ?? null; 
	$doc_id		            = $_REQUEST["doc_id"] ?? null;
    $nrd_id                 = $_REQUEST["nrd_id"] ?? null; 
    $crd_id                 = $_REQUEST["crd_id"] ?? null; 
    $employee_id            = $_REQUEST["employee_id"] ?? null; 
    $pp_id                  = $_REQUEST["pp_id"] ?? null; 
    $file                   = $_FILES["document"] ?? null;
	$partner_id             = getPartnerIdByCandidateId($kandidat_id);
	
	// Checks if all required parameters are present and valid and returns error if not
	checkChangeDocumentStatusParams($kandidat_id, $nalog_id, $new_status_id, $status_comment, $doc_id, $nrd_id, $crd_id, $employee_id, $pp_id, $file);

    switch($new_status_id)
    {
        case 3:
            if(documentAlreadyExistsAndIsValid($kandidat_id, $nalog_id, $crd_id, $nrd_id))
			{
                http_response_code(500);
                die("Dokument već postoji i aktivan je!");
			}

			$filepath = "";
			$doc_id = NULL;
			try{
				$filepath = uploadDocumentFileR($file, "/jobstep_pp/files/candidate_documents/");
				$doc_id = insertDocument($filepath, $kandidat_id, $nalog_id, $partner_id, $nrd_id, $crd_id);

				changeStatusAndInsertLog($doc_id, $new_status_id, $status_comment, $employee_id, $pp_id);
				archivePreviouslyUploaded($kandidat_id, $nalog_id, $nrd_id, $crd_id ,$employee_id, $pp_id);

				if($nrd_id != NULL)
				{
					updateReminderStatusCRM($kandidat_id, 10, $nrd_id);
					updateReminderStatusCRM($kandidat_id, 11, $nrd_id);
					updateReminderStatusCRM($kandidat_id, 13, $nrd_id);
					$nrd_doc = getNrdDocument($nrd_id);
					// Ako je uploadovan Qualiplan, provjeriti da li postoji reminder i ugasiti ga
					if($nrd_doc->nrd_type_id == 14) {
						/*
							Ako postoji aktivan reminder za Preuzmi nostrifikovanu diplomu i zatrazi Qualiplan, prilikom
							uploada Qualiplana od strane CRM usera - izvrši reminder
							START
							*/
								updateReminderStatusCRM($kandidat_id, 8);
							/*
							END	
						*/
						turnOffQualiplanReminder($kandidat_id);
					}

					if($nrd_doc->nrd_done_by == 2 OR $nrd_doc->nrd_done_by == 3)
					{
						changeStatusAndInsertLog($doc_id, 6, "Automatski prebačeno pri uploadu", $employee_id, $pp_id);
						changeStatusAndInsertLog($doc_id, 9, "Automatski prebačeno pri uploadu", $employee_id, $pp_id);
						changeStatusAndInsertLog($doc_id, 12, "Automatski prebačeno pri uploadu", $employee_id, $pp_id);
						changeStatusAndInsertLog($doc_id, 15, "Automatski prebačeno pri uploadu", $employee_id, $pp_id);
						changeStatusAndInsertLog($doc_id, 18, "Automatski prebačeno pri uploadu", $employee_id, $pp_id);
					}
				}
				if($crd_id != NULL)
				{
					$crd_doc = getCrdDocument($crd_id);
					if($crd_doc->crd_done_by == 2 OR $crd_doc->crd_done_by == 3)
					{
						changeStatusAndInsertLog($doc_id, 6, "Automatski prebačeno pri uploadu", $employee_id, $pp_id);
						changeStatusAndInsertLog($doc_id, 9, "Automatski prebačeno pri uploadu", $employee_id, $pp_id);
						changeStatusAndInsertLog($doc_id, 12, "Automatski prebačeno pri uploadu", $employee_id, $pp_id);
						changeStatusAndInsertLog($doc_id, 15, "Automatski prebačeno pri uploadu", $employee_id, $pp_id);
						changeStatusAndInsertLog($doc_id, 18, "Automatski prebačeno pri uploadu", $employee_id, $pp_id);
					}
				}
			}
			catch(Exception $e)
			{
				http_response_code(500);
				die($e->getMessage());
			}
            break;
        case 6:
            // Ide na provjeren
            changeStatusAndInsertLog($doc_id, $new_status_id, $status_comment, $employee_id, $pp_id);

            // Ugasiti reminder za provjeru dokumenta
			if(is_null($nrd_id))
			{
				updateReminderStatusCRM($kandidat_id, 12, $crd_id);
			}else{
				updateReminderStatusCRM($kandidat_id, 12, $nrd_id);
			}

            // Ako su svi dokumenti za koje je zadužen poslodavac na statusu 6 - provjeren,
            // sve prebacujemo na status 9 - čekamo original

            $nalog_docs = getNalogDocs($nalog_id, $kandidat_id);


            $candidate_docs = getKandidatDocs($nalog_id, $kandidat_id, NULL);

            addStatusesToDocument($nalog_docs, $kandidat_id);
            addStatusesToDocument($candidate_docs, $kandidat_id);

            $all_docs = array_merge($nalog_docs, $candidate_docs);

            $broj_neprovjerenih_dokumenata = count(array_filter($all_docs, function($n){return 
                   $n->zaduzeni                == "Poslodavac"
                && $n->statuses["arhiviran"]   == NULL
                && $n->statuses["odbijen"]     == NULL
                && $n->statuses["provjeren"]   == NULL;
            }));

            if($broj_neprovjerenih_dokumenata == 0)
            {
                foreach(array_filter($all_docs, function($n){ return 
                               $n->zaduzeni                         == "Poslodavac"
                            && $n->statuses["nijeProsaoProvjeru"]   == NULL
                            && $n->statuses["arhiviran"]            == NULL
                            && $n->statuses["odbijen"]              == NULL
                            && $n->statuses["cekamoOriginal"]       == NULL; }) 
                as $doc)
                {
                    // changeStatusAndInsertLog($doc->id, 9, "Automatski prebačeno jer su svi potrebni dokumenti provjereni.", $employee_id, $pp_id);
                    changeStatusAndInsertLog($doc->id, 9, "Status automatisch geändert, weil alle Dokumente geprüft sind.", $employee_id, $pp_id);
                }
            }

            break;
        case 15:
            // Ide na imamo original
            changeStatusAndInsertLog($doc_id, $new_status_id, $status_comment, $employee_id, $pp_id);

            // Odmah ide na 18: Spreman
            // changeStatusAndInsertLog($doc_id, $new_status_id + 3, "Automatski prebačen na spreman jer imamo original", $employee_id, $pp_id);
            changeStatusAndInsertLog($doc_id, $new_status_id + 3, "Status automatisch geändert.", $employee_id, $pp_id);
            break;
        default:
            changeStatusAndInsertLog($doc_id, $new_status_id, $status_comment, $employee_id, $pp_id);
			if($new_status_id == 21){
				 // Ugasiti reminder za provjeru dokumenta
				if(is_null($nrd_id))
				{
					updateReminderStatusCRM($kandidat_id, 12, $crd_id);
				}else{
					updateReminderStatusCRM($kandidat_id, 12, $nrd_id);
				}
			}

			if ($new_status_id == 12) {
				$nalog_docs = getNalogDocs($nalog_id, $kandidat_id);
				addStatusesToDocument($nalog_docs, $kandidat_id);
				$candidate_docs = getKandidatDocs($nalog_id, $kandidat_id, NULL);
				addStatusesToDocument($candidate_docs, $kandidat_id);
				$all_docs = array_merge($nalog_docs, $candidate_docs);
				$broj_dokumenata_poslan = count(array_filter($all_docs, function($n){return 
					$n->zaduzeni                			== "Poslodavac"
					&& 	$n->statuses["arhiviran"]   		== NULL
					&& 	$n->statuses["odbijen"]     		== NULL
					&& 	$n->statuses["poslan"] 	   			== NULL
					&& 	$n->statuses["imamoOriginal"] 	   	== NULL
					&& 	$n->statuses["spreman"] 	   		== NULL;
				}));

				if ($broj_dokumenata_poslan == 0){
					if(is_null($nrd_id)){
						updateReminderStatusCRM($kandidat_id, 24);
					}else{
						updateReminderStatusCRM($kandidat_id, 14);
					}
				}
			}
			
            break;
    }
	break;
case "poslodavacPoslaoDokumente":
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: GET, POST');
	header("Access-Control-Allow-Headers: X-Requested-With");
	$kandidat_id            = $_REQUEST["kandidat_id"];
	$nalog_id               = $_REQUEST["nalog_id"];
	$doc_ids                = explode(",",$_REQUEST["doc_ids"]);
	$pp_id                  = $_REQUEST["pp_id"];
	$employee_id            = $_REQUEST["employee_id"];
	$status_comment         = $_REQUEST["status_comment"];
    $new_status_id          = 12;
	
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    try 
    {
        $db->beginTransaction();

        foreach($doc_ids as $doc_id)
        {
            updatePreviousDocumentStatusNumberOfDays($doc_id);

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

        }

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

    break;

case "new_pp_doc_type":
	//------------------- PODACI ------------------------
		$dt_name = $_POST["dt_name"]; //naziv na bosanskom jeziku
		$dt_name_de = $_POST["dt_name_de"]; //naziv na njemackom jeziku
		$dt_date = date("Y-m-d H:i:s"); //datum dodavanja
		$dt_entered_user = $logged_employee_id; //zaposlenik koji je unio tip dokumenta
	//------------------- PODACI ------------------------

	//-------------------- UNOS -------------------------
		$queryInsert = $db->prepare("
			INSERT INTO idk_pp_document_types
			(
				doc_type_name,
				doc_type_name_de,
				doc_type_date,
				doc_type_entered_user
			)
			VALUES
			(
				:doc_type_name,
				:doc_type_name_de,
				:doc_type_date,
				:doc_type_entered_user
			)
		");
		$queryInsert->execute(array(
			":doc_type_name" => $dt_name,
			":doc_type_name_de" => $dt_name_de,
			":doc_type_date" => $dt_date,
			":doc_type_entered_user" => $dt_entered_user
		));

		$lastId = $db->lastInsertId(); //Zadnji id

		$log_desc = "TIPOVI DOKUMENATA -> Dodan tip dokumenta ID = [".$lastId."].";
		$log_query = $db->prepare("
			INSERT INTO idk_logs
				(log_employeeid, log_desc, log_date)
			VALUES
				(:log_employeeid, :log_desc, :log_date)
		");
		$log_query->execute(array(
			':log_employeeid' => $dt_entered_user,
			':log_desc' => $log_desc,
			':log_date' => $dt_date
		));

		header("Location: nalozi?page=predefinisani_dokumenti");
	//-------------------- UNOS -------------------------
break;

case "activate_document_pp":
	$acd_nalog = intval($_POST["acd_nalog"]);
	$acd_doc_id = intval($_POST["acd_doc_id"]);
	$acd_done_by = intval($_POST["acd_done_by"]);
	$acd_comment = $_POST["acd_comment"];
	$acd_zb_potreban = $_POST["zb_potreban"];
	$acd_ri_potreban = $_POST["ri_potreban"]; 
	$acd_sk_potreban = $_POST["sk_potreban"];
	$dateActivated = date("Y-m-d H:i:s");
	$queryInsert = $db->prepare("
		INSERT INTO idk_pp_nalog_required_documents
		(
			nrd_nalog_id,
			nrd_type_id,
			nrd_done_by,
			nrd_status,
			nrd_date_activated,
			nrd_user_activated,
			nrd_comment,
			nrd_skilled_candidates,
			nrd_west_balkan, 
			nrd_work_experience
		)
		VALUES(
			:nrd_nalog_id,
			:nrd_type_id,
			:nrd_done_by,
			:nrd_status,
			:nrd_date_activated,
			:nrd_user_activated,
			:nrd_comment,
			:nrd_skilled_candidates,
			:nrd_west_balkan, 
			:nrd_work_experience
		)
	");
	$queryInsert->execute(array(
		':nrd_nalog_id' => $acd_nalog,
		':nrd_type_id' => $acd_doc_id,
		':nrd_done_by' => $acd_done_by,
		':nrd_status' => 1,
		':nrd_date_activated' => $dateActivated,
		':nrd_user_activated' => $logged_employee_id,
		':nrd_comment' => $acd_comment,
		':nrd_skilled_candidates' => $acd_sk_potreban,
		':nrd_west_balkan' => $acd_zb_potreban,
		':nrd_work_experience' => $acd_ri_potreban
	));

	$nrd_insert_id = $db->lastInsertId();

	$log_desc = "AKTIVIRAN TIP [DOKUMENTI PROCESA ODLASKA] -> Aktiviran tip dokumenta ID = [".$nrd_insert_id."].";
	$log_query = $db->prepare("
		INSERT INTO idk_logs
			(log_employeeid, log_desc, log_date)
		VALUES
			(:log_employeeid, :log_desc, :log_date)
	");
	$log_query->execute(array(
		':log_employeeid' => $logged_employee_id,
		':log_desc' => $log_desc,
		':log_date' => $dateActivated
	));

	header("Location: nalozi?page=open&id=".$acd_nalog."&tab=vizadokumenti");
break;

case "archived_document_pp":
	$ard_nalog = $_POST["ard_nalog"];
	$ard_nrd_id = $_POST["ard_nrd_id"];
	$dateArchived = date("Y-m-d H:i:s");
	$updateIdImp = implode(",",$ard_nrd_id);
	$updateIdExp = explode(",", $updateIdImp);
	foreach($updateIdExp AS $updateVal){
		$queryUpdate = $db->prepare("
		UPDATE
			idk_pp_nalog_required_documents
		SET
			nrd_status = 0, nrd_date_archived = :date, nrd_user_archived = :user
		WHERE
			nrd_id = :nrd_ids
		");
		$queryUpdate->execute(array(
			':nrd_ids' => $updateVal,
			':date' => $dateArchived,
			':user' => $logged_employee_id
		));
	}
	
	$log_desc = "ARHIVIRAN/I TIP/OVI [DOKUMENTI PROCESA ODLASKA] -> Arhiviran tip dokumenta ID/s = [".$updateIdImp."].";
	$log_query = $db->prepare("
		INSERT INTO idk_logs
			(log_employeeid, log_desc, log_date)
		VALUES
			(:log_employeeid, :log_desc, :log_date)
	");
	$log_query->execute(array(
		':log_employeeid' => $logged_employee_id,
		':log_desc' => $log_desc,
		':log_date' => $dateArchived
	));
	header("Location: nalozi?page=open&id=".$ard_nalog."&tab=vizadokumenti");

break;

case "add_datum_termina":
	$kandidat_id = $_POST["kandidat_id"];
	$datum_termina = $_POST["kandidat_datum_termina"];
	$datum_termina_f = date("Y-m-d", strtotime($datum_termina));

	$update_datum_termina = $db->prepare("UPDATE idk_kandidati SET datum_termina = :datum_termina, kandidat_status_prijave = 15, kandidat_bio_na_terminu = NULL WHERE kandidat_id = :kandidat_id");
	$update_datum_termina->execute([
		":datum_termina" => $datum_termina_f,
		":kandidat_id" => $kandidat_id
	]);

	$get_stari_datum = $db->prepare("SELECT datum_termina FROM idk_kandidati WHERE kandidat_id = $kandidat_id");
	$get_stari_datum->execute();
	$result_stari_datum = $get_stari_datum->fetch();
	$stari_datum = $result_stari_datum["datum_termina"];

	$get_nalog_id = $db->prepare("SELECT kandidat_nalog_id FROM idk_kandidati WHERE kandidat_id = :kandidat_id");
	$get_nalog_id->execute([
		":kandidat_id" => $kandidat_id
	]);
	$result = $get_nalog_id->fetch();
	$nalog_id = $result["kandidat_nalog_id"];

	$kandidat_project = getProjectForCandidatR($kandidat_id, $nalog_id);

	addToLogsStatusPrijave($kandidat_project, $kandidat_project, 15, $kandidat_id, 1);
	$log_desc = "Zaposlenik dodao datum termina: " . $datum_termina . " kod kandidata " . $kandidat_id . " stari datum: " . $stari_datum;
	$log_type = 0;
	addToLogs($log_desc, $log_type);
	updateReminderStatusCRM($kandidat_id, 15);
	header("Location: kandidati.php?page=open&id=" . $kandidat_id . "#proces_odlaska");	
break;

case "kandidat_termin_check":
	$provjera_termina = $_POST["provjera_termina"];
	$kandidat_id = $_POST["kandidat_id"];
	$nalog_id = $_POST["nalog_id"];
	if($provjera_termina == 1){
		$update_termin_check = $db->prepare("UPDATE idk_kandidati SET kandidat_bio_na_terminu = 1, kandidat_status_prijave = 18 WHERE kandidat_id = $kandidat_id");
		$update_termin_check->execute();

		$kandidat_project = getProjectForCandidatR($kandidat_id, $nalog_id); 
		addToLogsStatusPrijave($kandidat_project, $kandidat_project, 18, $kandidat_id, 1);

		$log_desc = "Zaposlenik potvrdio datum termina: " . $datum_termina . " kod kandidata " . $kandidat_id;
		$log_type = 0;
		addToLogs($log_desc, $log_type);
		updateReminderStatusCRM($kandidat_id, 16);
		header("Location: kandidati.php?page=open&id=" . $kandidat_id . "#proces_odlaska");	
	} else {
		if(isset($_POST["kandidat_datum_termina_novi"]) && $_POST["kandidat_datum_termina_novi"] != null){
			$novi_datum_termina = $_POST["kandidat_datum_termina_novi"];
			$novi_datum_termina_f = date("Y-m-d", strtotime($novi_datum_termina));
			$get_stari_datum = $db->prepare("SELECT datum_termina FROM idk_kandidati WHERE kandidat_id = $kandidat_id");
			$get_stari_datum->execute();
			$result_stari_datum = $get_stari_datum->fetch();
			$stari_datum = $result_stari_datum["datum_termina"];

			$update_novi_datum = $db->prepare("UPDATE idk_kandidati SET datum_termina = :novi_datum, kandidat_bio_na_terminu = NULL WHERE kandidat_id = $kandidat_id");
			$update_novi_datum->execute([
				":novi_datum" => $novi_datum_termina_f
			]);

			$log_desc = "Zaposlenik postavio novi datum termina: " . $novi_datum_termina . " kod kandidata: " . $kandidat_id ." stari termin: " . $stari_datum . "";
			$log_type = 0;
			addToLogs($log_desc, $log_type);
			updateReminderStatusCRM($kandidat_id, 16);
			header("Location: kandidati.php?page=open&id=" . $kandidat_id . "#proces_odlaska");	
		}
		elseif($provjera_termina == 0){
			$update_check_termin = $db->prepare("UPDATE idk_kandidati SET kandidat_bio_na_terminu = 0 WHERE kandidat_id = $kandidat_id");
			$update_check_termin->execute();

			$log_desc = "Zaposlenik oznacio da kandidat ($kandidat_id) nije bio na terminu";
			$log_type = 0;
			addToLogs($log_desc, $log_type);
			updateReminderStatusCRM($kandidat_id, 16);
			header("Location: kandidati.php?page=open&id=" . $kandidat_id . "#proces_odlaska");	
		}
	}
break;

case 'status_dopuna':

	$kandidat_id=$_POST['kandidat_id'];
	$dopuna_poslana_da=$_POST['dopuna_poslana'];
	//if($dopuna_poslana_da=="block"){
		prebaci_na_status($kandidat_id,18);	
		$visa_inc_end_query=$db->prepare("UPDATE
												idk_pp_visa_incomplete
											SET
												vi_status = 0
											WHERE
												vi_cand_id =(
												SELECT
													kandidat_id
												FROM
													`idk_kandidati`
												WHERE
													`kandidat_id` = :kandidat_id
											) AND vi_nalog_id =(
												SELECT
													kandidat_nalog_id
												FROM
													`idk_kandidati`
												WHERE
													`kandidat_id` = :kandidat_id
											) AND vi_type = 1");
		$visa_inc_end_query->execute(array( ':kandidat_id' => $kandidat_id));
	//}
	
	header("Location: kandidati.php?page=open&id=" . $kandidat_id . "#proces_odlaska");
	
	
	
break;

case 'status_odbijen':

	$kandidat_id=$_POST['kandidat_id'];
	$zalba_poslana=$_POST['zalba_poslana'];
	//if($zalba_poslana=="block"){
		prebaci_na_status($kandidat_id,18);	
		$visa_inc_end_query=$db->prepare("UPDATE
												idk_pp_visa_incomplete
											SET
												vi_status = 0
											WHERE
												vi_cand_id =(
												SELECT
													kandidat_id
												FROM
													`idk_kandidati`
												WHERE
													`kandidat_id` = :kandidat_id
											) AND vi_nalog_id =(
												SELECT
													kandidat_nalog_id
												FROM
													`idk_kandidati`
												WHERE
													`kandidat_id` = :kandidat_id
											) AND vi_type = 2");
		$visa_inc_end_query->execute(array( ':kandidat_id' => $kandidat_id));
	//}
	
	header("Location: kandidati.php?page=open&id=" . $kandidat_id . "#proces_odlaska");
	
	
	
break;

case "visa_incomplete_edit":
	
	$vi_id = intval($_POST["vi_id"]);
	$vi_cand_id = intval($_POST["vi_cand_id"]);
	$vi_type = intval($_POST["vi_type"]);
	if($vi_type == 1){
		$subSubDescLog1 = "dopunu";
	}else{
		$subSubDescLog1 = "odbijenicu";
	}
	$vi_status = intval($_POST["vi_status"]);
	$vi_nalog_id = intval($_POST["vi_nalog_id"]);
	$vi_pp_partner_id = intval($_POST["vi_pp_partner_id"]);

	$new_received_VI = intval($_POST["new_received_VI"]);

	if($new_received_VI == 1 OR $new_received_VI == 2){
		$new_reported_VI = intval($_POST["new_reported_VI"]);

		if($new_reported_VI == 1){
			$new_date_received_VI = date("Y-m-d H:i:s", strtotime($_POST["new_date_received_VI"]));
			$new_date_we_received_VI = date("Y-m-d H:i:s", strtotime($_POST["new_date_we_received_VI"]));
			$new_deadline_date_VI = date("Y-m-d", strtotime($_POST["new_deadline_date_VI"]));
			$subQuerySql = "";
			$querySql = "";
			$subDescLog1 = "";
			//Slučaj kada je naglašeno da je javio:
			if($new_received_VI == 1){
				// --> Kandidat
				$subQuerySql = "
					vi_date_received_candidate = :datumPrijema,
					vi_date_we_received_candidate = :nasDatumPrijema,
					vi_deadline_date_candidate = :krajnjiDatum
				";
				$subDescLog1 = "Zaposlenik naznačio pristuglu informaciju od kandidata za ".$subSubDescLog1.". Kandidat ID = [".$vi_cand_id."].";
			}else{
				// -->Poslodavac
				$subQuerySql = "
					vi_date_received_employer = :datumPrijema,
					vi_date_we_received_employer = :nasDatumPrijema,
					vi_deadline_date_employer = :krajnjiDatum
				";
				$subDescLog1 = "Zaposlenik naznačio pristuglu informaciju od poslodavca za ".$subSubDescLog1.". Kandidat ID = [".$vi_cand_id."].";
			}

			$querySql = "
				UPDATE idk_pp_visa_incomplete
				SET
					".$subQuerySql."
				WHERE 
					vi_id = :vi_id
					AND 
					vi_cand_id = :vi_cand_id
					AND 
					vi_nalog_id = :vi_nalog_id
			";

			if($subQuerySql != ""){
				$queryUpdate1 = $db->prepare($querySql);
				$queryUpdate1->execute(array(
					':vi_id' => $vi_id,
					':vi_cand_id' => $vi_cand_id,
					':vi_nalog_id' => $vi_nalog_id,
					':datumPrijema' => $new_date_received_VI,
					':nasDatumPrijema' => $new_date_we_received_VI,
					':krajnjiDatum' => $new_deadline_date_VI
				));

				$logDesc1 = $subDescLog1." INFO: Datum prijema: ".$new_date_received_VI." Datum našeg prijema: ".$new_date_we_received_VI." Krajnji datum: ".$new_deadline_date_VI."";
				addToLogs($logDesc1, 0);
			}
		}
	}
	//UNOS DOKUMENATA START
	$arrayLastInsertIDsCRD = array();
	$document_types_VI = $_POST["document_types_VI"];
	foreach($document_types_VI AS $typeVal){
		$doneByVal = 0;
		$doneByVal = intval($_POST["done_by_doc_VI_".$typeVal]); 
		$komentarVal = "";
		$komentarVal = $_POST["comment_doc_VI_".$typeVal];
		/*var_dump($tipoviVal);
		echo "<br>";
		var_dump($doneByVal);
		echo "<br>";
		var_dump($komentarVal);
		echo "<br>";*/
		//Ovdje napraviti backend za unos crd dokumenata
		$lastInsertCrd = 0;
		$insertCrd = $db->prepare("
			INSERT INTO idk_pp_cand_required_documents
			(
				crd_cand_id,
				crd_nalog_id,
				crd_type_id,
				crd_done_by,
				crd_status,
				crd_date_activated,
				crd_user_activated,
				crd_comment,
				crd_vi_id
			)
			VALUES
			(
				:crd_cand_id,
				:crd_nalog_id,
				:crd_type_id,
				:crd_done_by,
				:crd_status,
				:crd_date_activated,
				:crd_user_activated,
				:crd_comment,
				:crd_vi_id
			)
		");
		$insertCrd->execute(array(
			':crd_cand_id' => $vi_cand_id,
			':crd_nalog_id' => $vi_nalog_id,
			':crd_type_id' => intval($typeVal),
			':crd_done_by' => $doneByVal,
			':crd_status' => 1,
			':crd_date_activated' => date("Y-m-d H:i:s"),
			':crd_user_activated' => $logged_employee_id,
			':crd_comment' => $komentarVal,
			':crd_vi_id' => $vi_id
		));
		$lastInsertCrd = $db->lastInsertId();
		array_push($arrayLastInsertIDsCRD,$lastInsertCrd);
		archiveDocumentTypeForCandidate($vi_cand_id, $vi_nalog_id, $typeVal);
	}

	$implodeLastInsertIdsCRD = implode(",", $arrayLastInsertIDsCRD);
	//UNOS DOKUMENATA END 

	$log_desc = "Zaposlenik dopunio check listu dokumenata za ".$subSubDescLog1." kod kandidata ID = [".$vi_cand_id."] vezanih za ".$subSubDescLog1." ID = [".$vi_id."]. Dokumenti IDs = [".$implodeLastInsertIdsCRD."].";
	addToLogs($log_desc, 0);
	header("Location: kandidati.php?page=open&id=" . $vi_cand_id . "#proces_odlaska");	
	
break; 

case "add_kandidat_visa":
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$kandidat_id = $_POST["kandidat_id"];
	$nalog_id = $_POST["nalog_id"];
	$ishod_termina = intval($_POST["ishod_termina"]);
	$datum_prijema_dopune = $_POST["datum_prijema_dopune"];
	$nas_datum_prijema_dopune = $_POST["nas_datum_prijema_dopune"];
	$krajnji_datum_dopune = $_POST["krajnji_datum_dopune"];
	
	if($ishod_termina == 1){
		//exit(); //ODKOMENTARISATI _____________________________________________________________
		$visa_start = $_POST["kandidat_datum_viza_start"];
		$visa_end = $_POST["kandidat_datum_viza_end"]; 

		$update_kandidat_visa = $db->prepare("UPDATE 
												idk_kandidati 
											SET 
												kandidat_viza_vrijedi_od = :visa_start, 
												kandidat_viza_vrijedi_do = :visa_end, 
												kandidat_status_prijave = 27 
											WHERE 
												kandidat_id = :kandidat_id"
											);
		$update_kandidat_visa->execute([
			":visa_start" 	=> $visa_start,
			":visa_end" 	=> $visa_end,
			":kandidat_id" 	=> $kandidat_id
		]);

		$kandidat_project = getProjectForCandidatR($kandidat_id, $nalog_id);
		addToLogsStatusPrijave($kandidat_project, $kandidat_project, 27, $kandidat_id, 1);
		assignToMakler($kandidat_id, $nalog_id);

		$rate = getNalogRate($nalog_id);
		$rata_postoji = false;
		$date = getKandidatVisaStartDate($kandidat_id);
		
		foreach($rate as $rata){
			if(in_array("dobio vizu", $rata)){
				$rata_postoji = true;
				break;
			}
		}
		if($rata_postoji){
			updateKandidatRata($kandidat_id, $nalog_id, getKandidatRata($kandidat_id, 2), $date);
		}

		$log_desc = "Zaposlenik dodao datume trajanja vize (". $visa_start . " - ". $visa_end . ") kod kandidata: " . $kandidat_id;
		$log_type = 0;
		addToLogs($log_desc, $log_type);
		updateReminderStatusCRM($kandidat_id,17);
		
	}else if($ishod_termina == 2){
		$dopunu_dobio = intval($_POST["dopunu_dobio"]);
		$datum_prijema_dopune = date("Y-m-d H:i",strtotime($_POST["datum_prijema_dopune"]));
		$nas_datum_prijema_dopune = date("Y-m-d H:i",strtotime($_POST["nas_datum_prijema_dopune"]));
		$krajnji_datum_dopune = date("Y-m-d",strtotime($_POST["krajnji_datum_dopune"]));
		$sqlSubQuery = "";

		$nalogId = intval(getNalogIdByCandidateId($kandidat_id));
		$partnerId = intval(getPartnerIdByCandidateId($kandidat_id));
		if($dopunu_dobio == 1){
			$sqlSubQuery = "
				vi_date_received_candidate,
				vi_date_we_received_candidate,
				vi_deadline_date_candidate,
			";
		}else if($dopunu_dobio == 2){
			$sqlSubQuery = "
				vi_date_received_employer,
				vi_date_we_received_employer,
				vi_deadline_date_employer,
			";
		}else{
			$sqlSubQuery = "";
		}

		if($sqlSubQuery != "" AND $nalogId != 0 AND $partnerId != 0){
			/*var_dump($dopunu_dobio);
			echo "<br>";
			var_dump($datum_prijema_dopune);
			echo "<br>";
			var_dump($nas_datum_prijema_dopune);
			echo "<br>";
			var_dump($krajnji_datum_dopune);
			echo "<br>";
			var_dump($tipovi_dokumenata_dopune);
			echo "<br>";
			echo "<br>";*/
			//*******************************************************
			//Unos dopune START
			//*******************************************************
			$insertVisaIncomplete = $db->prepare("
				INSERT INTO idk_pp_visa_incomplete
				(
					vi_cand_id,
					vi_type,
					vi_status,
					".$sqlSubQuery."
					vi_nalog_id,
					vi_pp_partner_id
				)
				VALUES(
					:vi_cand_id,
					:vi_type,
					:vi_status,
					:vi_date_received,
					:vi_date_we_received,
					:vi_deadline_date,
					:vi_nalog_id,
					:vi_pp_partner_id
				)
			");
			$insertVisaIncomplete->execute(array(
				':vi_cand_id' => $kandidat_id,
				':vi_type' => 1,
				':vi_status' => 1,
				':vi_date_received' => $datum_prijema_dopune,
				':vi_date_we_received' => $nas_datum_prijema_dopune,
				':vi_deadline_date' => $krajnji_datum_dopune,
				':vi_nalog_id' => $nalogId,
				':vi_pp_partner_id' => $partnerId
			));

			$lastInsertIdVisaIncomplete = $db->lastInsertId();
			//*******************************************************
			//Unos dopune END
			//*******************************************************
			$arrayLastInsertIDsCRD = array();
			$tipovi_dokumenata_dopune = $_POST["tipovi_dokumenata_dopune"];
			foreach($tipovi_dokumenata_dopune AS $tipoviVal){
				$doneByVal = 0;
				$doneByVal = intval($_POST["done_by_doc".$tipoviVal]); 
				$komentarVal = "";
				$komentarVal = $_POST["comment_doc_".$tipoviVal];
				/*var_dump($tipoviVal);
				echo "<br>";
				var_dump($doneByVal);
				echo "<br>";
				var_dump($komentarVal);
				echo "<br>";*/
				//Ovdje napraviti backend za unos crd dokumenata
				$lastInsertCrd = 0;
				$insertCrd = $db->prepare("
					INSERT INTO idk_pp_cand_required_documents
					(
						crd_cand_id,
						crd_nalog_id,
						crd_type_id,
						crd_done_by,
						crd_status,
						crd_date_activated,
						crd_user_activated,
						crd_comment,
						crd_vi_id
					)
					VALUES
					(
						:crd_cand_id,
						:crd_nalog_id,
						:crd_type_id,
						:crd_done_by,
						:crd_status,
						:crd_date_activated,
						:crd_user_activated,
						:crd_comment,
						:crd_vi_id
					)
				");
				$insertCrd->execute(array(
					':crd_cand_id' => $kandidat_id,
					':crd_nalog_id' => $nalogId,
					':crd_type_id' => intval($tipoviVal),
					':crd_done_by' => $doneByVal,
					':crd_status' => 1,
					':crd_date_activated' => date("Y-m-d H:i:s"),
					':crd_user_activated' => $logged_employee_id,
					':crd_comment' => $komentarVal,
					':crd_vi_id' => $lastInsertIdVisaIncomplete
				));
				$lastInsertCrd = $db->lastInsertId();
				array_push($arrayLastInsertIDsCRD,$lastInsertCrd);
				archiveDocumentTypeForCandidate($kandidat_id, $nalogId, $tipoviVal);
			}

			$implodeLastInsertIdsCRD = implode(",", $arrayLastInsertIDsCRD);
			$update_kandidat_visa = $db->prepare("
				UPDATE 
					idk_kandidati 
				SET 
					kandidat_status_prijave = 21 
				WHERE 
					kandidat_id = :kandidat_id
			");
			$update_kandidat_visa->execute(array(
				":kandidat_id" 	=> $kandidat_id
			));
			$kandidat_project = getProjectForCandidatR($kandidat_id, $nalogId);
			addToLogsStatusPrijave($kandidat_project, $kandidat_project, 21, $kandidat_id, 1);
			$log_desc = "Zaposlenik napravio check listu dokumenata za kandidata ID = [".$kandidat_id."] vezanih za dopunu ID = [".$lastInsertIdVisaIncomplete."]. Dokumenti IDs = [".$implodeLastInsertIdsCRD."].";
			$log_type = 0;
			addToLogs($log_desc, $log_type);
			updateReminderStatusCRM($kandidat_id,17);

			unset($arrayLastInsertIDsCRD);
		}
	}else if($ishod_termina == 3){

		$uslov_za_odbijenicu = intval($_POST["uslov_za_odbijenicu"]); //1 - NE 2 - DA
		$uslov_za_zalbu_sql = 0;
		if($uslov_za_odbijenicu == 2){
			$uslov_za_zalbu_sql = 1;
		}
		$odbijenicu_dobio = intval($_POST["odbijenicu_dobio"]); // 1 - Kandidat 2 - Poslodavac
		$datum_prijema_odbijenice = date("Y-m-d H:i",strtotime($_POST["datum_prijema_odbijenice"]));
		$nas_datum_prijema_odbijenice = date("Y-m-d H:i",strtotime($_POST["nas_datum_prijema_odbijenice"]));
		if($uslov_za_odbijenicu == 2){
			$krajnji_datum_odbijenice = date("Y-m-d",strtotime($_POST["krajnji_datum_odbijenice"]));
		}else{
			$krajnji_datum_odbijenice = NULL;
		}
		$sqlSubQuery = "";

		$nalogId = intval(getNalogIdByCandidateId($kandidat_id));
		$partnerId = intval(getPartnerIdByCandidateId($kandidat_id));
		if($odbijenicu_dobio == 1){
			$sqlSubQuery = "
				vi_date_received_candidate,
				vi_date_we_received_candidate,
				vi_deadline_date_candidate,
			";
		}else if($odbijenicu_dobio == 2){
			$sqlSubQuery = "
				vi_date_received_employer,
				vi_date_we_received_employer,
				vi_deadline_date_employer,
			";
		}else{
			$sqlSubQuery = "";
		}

		if($sqlSubQuery != "" AND $nalogId != 0 AND $partnerId != 0){
			/*var_dump($uslov_za_odbijenicu);
			echo "<br>";
			var_dump($odbijenicu_dobio);
			echo "<br>";
			var_dump($datum_prijema_odbijenice);
			echo "<br>";
			var_dump($nas_datum_prijema_odbijenice);
			echo "<br>";
			var_dump($krajnji_datum_odbijenice);
			echo "<br>";*/
			//*******************************************************
			//Unos odbijenice START
			//*******************************************************
			$insertVisaIncomplete = $db->prepare("
				INSERT INTO idk_pp_visa_incomplete
				(
					vi_cand_id,
					vi_type,
					vi_complaint,
					vi_status,
					".$sqlSubQuery."
					vi_nalog_id,
					vi_pp_partner_id
				)
				VALUES(
					:vi_cand_id,
					:vi_type,
					:vi_complaint,
					:vi_status,
					:vi_date_received,
					:vi_date_we_received,
					:vi_deadline_date,
					:vi_nalog_id,
					:vi_pp_partner_id
				)
			");
			$insertVisaIncomplete->execute(array(
				':vi_cand_id' => $kandidat_id,
				':vi_type' => 2,
				':vi_complaint' => $uslov_za_zalbu_sql,
				':vi_status' => 1,
				':vi_date_received' => $datum_prijema_odbijenice,
				':vi_date_we_received' => $nas_datum_prijema_odbijenice,
				':vi_deadline_date' => $krajnji_datum_odbijenice,
				':vi_nalog_id' => $nalogId,
				':vi_pp_partner_id' => $partnerId
			));

			$lastInsertIdVisaIncomplete = $db->lastInsertId();
			//*******************************************************
			//Unos odbijenice END
			//*******************************************************
			if($uslov_za_odbijenicu == 2){
				$arrayLastInsertIDsCRD = array();
				$tipovi_dokumenata_odbijenice = $_POST["tipovi_dokumenata_odbijenice"];
				/*var_dump($tipovi_dokumenata_odbijenice);
				echo "<br>";
				echo "<br>";*/
				foreach($tipovi_dokumenata_odbijenice AS $tipoviVal){
					$doneByVal = 0;
					$doneByVal = intval($_POST["done_by_odbijenica_doc".$tipoviVal]); 
					$komentarVal = "";
					$komentarVal = $_POST["comment_doc_odbijenica_".$tipoviVal];
					/*var_dump($tipoviVal);
					echo "<br>";
					var_dump($doneByVal);
					echo "<br>";
					var_dump($komentarVal);
					echo "<br>";*/
					//Ovdje napraviti backend za unos crd dokumenata
					$lastInsertCrd = 0;
					$insertCrd = $db->prepare("
						INSERT INTO idk_pp_cand_required_documents
						(
							crd_cand_id,
							crd_nalog_id,
							crd_type_id,
							crd_done_by,
							crd_status,
							crd_date_activated,
							crd_user_activated,
							crd_comment,
							crd_vi_id
						)
						VALUES
						(
							:crd_cand_id,
							:crd_nalog_id,
							:crd_type_id,
							:crd_done_by,
							:crd_status,
							:crd_date_activated,
							:crd_user_activated,
							:crd_comment,
							:crd_vi_id
						)
					");
					$insertCrd->execute(array(
						':crd_cand_id' => $kandidat_id,
						':crd_nalog_id' => $nalogId,
						':crd_type_id' => intval($tipoviVal),
						':crd_done_by' => $doneByVal,
						':crd_status' => 1,
						':crd_date_activated' => date("Y-m-d H:i:s"),
						':crd_user_activated' => $logged_employee_id,
						':crd_comment' => $komentarVal,
						':crd_vi_id' => $lastInsertIdVisaIncomplete
					));
					$lastInsertCrd = $db->lastInsertId();
					array_push($arrayLastInsertIDsCRD,$lastInsertCrd);
					archiveDocumentTypeForCandidate($kandidat_id, $nalogId, $tipoviVal);
				}

				$implodeLastInsertIdsCRD = implode(",", $arrayLastInsertIDsCRD);
				
				$log_desc = "Zaposlenik napravio check listu dokumenata za kandidata ID = [".$kandidat_id."] vezanih za odbijenicu ID = [".$lastInsertIdVisaIncomplete."]. Dokumenti IDs = [".$implodeLastInsertIdsCRD."]. Kandidat ima uslova za žalbu!";
				
				unset($arrayLastInsertIDsCRD);
			}else{
				$log_desc = "Zaposlenik označio odbijenicu vize za kandidata ID = [".$kandidat_id."] sa detaljima odbijenice ID = [".$lastInsertIdVisaIncomplete."]. Kandidat nema uslova za žalbu!";
			}

			$update_kandidat_visa = $db->prepare("
				UPDATE 
					idk_kandidati 
				SET 
					kandidat_status_prijave = 24 
				WHERE 
					kandidat_id = :kandidat_id
			");
			$update_kandidat_visa->execute(array(
				":kandidat_id" 	=> $kandidat_id
			));
			$kandidat_project = getProjectForCandidatR($kandidat_id, $nalogId);
			addToLogsStatusPrijave($kandidat_project, $kandidat_project, 24, $kandidat_id, 1);
			
			$log_type = 0;
			addToLogs($log_desc, $log_type);

			updateReminderStatusCRM($kandidat_id,17);
		}
		
	}else if($ishod_termina == 4){
		//Napravljeno je da se može označiti da kandidat nije jos dobio odgovor o ishodu termina
		//I u tom slučaju treba se update-ati reminder i unijeti bilješka
		$biljeska_nije_dobio = $_POST["biljeska_nije_dobio"];
		$insert_note = $db->prepare("
			INSERT INTO idk_notes
				(note_txt, note_datetime, note_group, note_dataid, note_employeeid)
			VALUES
				(:note_txt, :note_datetime, :note_group, :note_dataid, :note_employeeid)");

		$insert_note->execute(array(
					':note_txt' => $biljeska_nije_dobio,
					':note_datetime' => date('Y-m-d H:i:s'),
					':note_group' => 2,
					':note_dataid' => $kandidat_id,
					':note_employeeid' => $logged_employee_id));

		$log_desc = "Zaposlenik označio da ishod termina još nije dobijen kod kandidata: " . $kandidat_id;
		$log_type = 0;
		addToLogs($log_desc, $log_type);
		updateReminderStatusCRM($kandidat_id,17);
	}else{
		//Ništa se ne desi u slučaju druge opcije 
	}
	
	header("Location: kandidati.php?page=open&id=" . $kandidat_id . "#proces_odlaska");	
break;

case "unesi_datum_pocetak_rada": 
	
	$kandidat_id 		= $_POST["kandidat_id"];
	$datum_pocetak_rada = $_POST["datum_pocetak_rada"];
	$nalog_id = getNalogIdByCandidateId($kandidat_id);

	$update_kandidat_dogovoreni_pocetak_rada = $db->prepare("UPDATE idk_kandidati SET kandidat_dogovoreni_pocetak_rada =:kandidat_dogovoreni_pocetak_rada WHERE kandidat_id =:kandidat_id;");
	$update_kandidat_dogovoreni_pocetak_rada->execute(array(
		':kandidat_id'						=>$kandidat_id,
		':kandidat_dogovoreni_pocetak_rada'	=>$datum_pocetak_rada
	));

	if(checkEUKandidat($kandidat_id)){
		assignToMakler($kandidat_id, $nalog_id);
		$rate = getNalogRate($nalog_id);	
		$rata_postoji = false;
		$date = date("Y-m-d");

		foreach($rate as $rata){
			if(in_array("dobio vizu", $rata)){
				$rata_postoji = true;
				break;
			}
		}
		if($rata_postoji){
			updateKandidatRata($kandidat_id, $nalog_id, getKandidatRata($kandidat_id, 2), $date);
		}

	}

	$provjeri_status_prijave = $db->prepare("SELECT	kandidat_status_prijave FROM idk_kandidati WHERE kandidat_id=:kandidat_id");
	$provjeri_status_prijave->execute(array(
		':kandidat_id' =>$kandidat_id
	));
	$provjeri_status_prijave_row = $provjeri_status_prijave->fetch();
	$kandidat_status_prijave = $provjeri_status_prijave_row["kandidat_status_prijave"];
	if($kandidat_status_prijave == 10){
		// akcija za reminder 18 - unio dogovoreni početak rada, ovdje ide i 19 jer je vec poceo raditi
		updateReminderStatusCRM($kandidat_id, 18);	
		updateReminderStatusCRM($kandidat_id, 19);	
		$kandidat_poceo_raditi_query = $db->prepare("UPDATE idk_kandidati SET kandidat_potvrden_pocetak_rada = 1 WHERE kandidat_id =:kandidat_id;");
		$kandidat_poceo_raditi_query->execute(array(
			':kandidat_id' => $kandidat_id
		));

		// $get_visak_rate = $db->prepare("SELECT kf_id, kf_type FROM idk_kandidat_financije WHERE kandidat_id = $kandidat_id AND kf_status = 3");
		// $get_visak_rate->execute();
		// while($rata = $get_visak_rate->fetch()){
		// 	$rata_id = $rata["kf_id"];
		// 	$rata_type = $rata["kf_type"];

		// 	$update_rate = $db->prepare("UPDATE idk_kandidat_financije SET kf_status = 1 WHERE kf_id = $rata_id");
		// 	$update_rate->execute();
		// }

		$rate = getNalogRate($nalog_id);	
		$rata_postoji = false;
		$rata_postoji_mn = false;
		$date = getKandidatDogovoreniPocetakRada($kandidat_id);

		foreach($rate as $rata){
			if(in_array("pocetak rada", $rata)){
				$rata_postoji = true;
			}
			if(in_array("mjeseci nakon", $rata)){
				$rata_postoji_mn = true;
			}
		}
		if($rata_postoji){
			updateKandidatRata($kandidat_id, $nalog_id, getKandidatRata($kandidat_id, 3), $date);
		}
		if($rata_postoji_mn){
			updateRateMjeseciNakon($kandidat_id, $date);
		}
		
		// NEW START Ako je placanje != 2 (mjesecno)
		if(checkNalogPaymentType($nalog_id) == 2 OR checkNalogPaymentType($nalog_id) == 0){
			$status_id = 4;
			$izvor = 1; 

			$novi_projekt_id = getProjectIDForNalogByName("- Završen" ,$nalog_id);

			$get_old_project_query = $db->prepare("SELECT  pr.project_id, pk.pk_id
												FROM idk_projects pr 
												JOIN (
													SELECT sqpk.pk_id, sqpk.pk_projectid, sqpk.pk_kandidatid
													FROM idk_project_kandidati sqpk
													WHERE sqpk.pk_kandidatid = :kandidat_id
												) pk
												ON pk.pk_projectid = pr.project_id
												WHERE pr.project_nalogid = :nalog_id
												AND pr.project_name LIKE '%Završen%'");
			$get_old_project_query->execute(array(
				':kandidat_id'	=>	$kandidat_id,
				':nalog_id'		=>	$nalog_id
			));								
			$get_old_project = $get_old_project_query->fetch();		

			$old_project_id = $get_old_project["project_id"];
			$pk_id 		= $get_old_project["pk_id"];

			$delete_old_project = $db->prepare("
								DELETE FROM idk_project_kandidati
								WHERE pk_id=:pk_id
								");
			$delete_old_project->execute(array(
				':pk_id'	=> $pk_id
			));							
			
			$add_new_project = $db->prepare("
					INSERT INTO idk_project_kandidati
						(pk_projectid, pk_kandidatid)
					VALUES
						(:pk_projectid, :pk_kandidatid)");		
			$add_new_project->execute(array(
				':pk_projectid'		=>	$novi_projekt_id,
				':pk_kandidatid'	=>	$kandidat_id
			));

			updateKandidatStatusPrijave($kandidat_id, $status_id);

			addToLogsStatusPrijave(NULL, $novi_projekt_id, $status_id, $kandidat_id, $izvor);
			$log_desc3 = "Zaposlenik prebacio kandidata: ".$kandidat_id." iz projekta: ".$old_project_id." u ".$novi_projekt_id.".";
			addToLogs($log_desc3, 0);
		}
		// NEW END

		$log_desc = "Zaposlenik unio datum početka rada(".$datum_pocetak_rada.") za kandidata(".$kandidat_id.").";
		addToLogs($log_desc, 0);
	}else{
		// akcija za reminder 18 - unio dogovoreni početak rada
		updateReminderStatusCRM($kandidat_id, 18);

		//Unosom dogovorenog početka rada moraju su ažurirati datumi faktura za rate pocetak rada i mjeseci nakon
		$rate = getNalogRate($nalog_id);	
		$rata_postoji = false;
		$rata_postoji_mn = false;
		$date = getKandidatDogovoreniPocetakRada($kandidat_id);

		foreach($rate as $rata){
			if(in_array("pocetak rada", $rata)){
				$rata_postoji = true;
			}
			if(in_array("mjeseci nakon", $rata)){
				$rata_postoji_mn = true;
			}
		}
		if($rata_postoji){
			updateKandidatRata($kandidat_id, $nalog_id, getKandidatRata($kandidat_id, 3), $date);
		}
		if($rata_postoji_mn){
			updateRateMjeseciNakon($kandidat_id, $date);
		}

		$log_desc = "Zaposlenik dodao dogovoreni datum pocetka rada (".$datum_pocetak_rada.") kandidatu: ".$kandidat_id.".";
		addToLogs($log_desc, 0);
	}
	
	header("Location: kandidati.php?page=open&id=" . $kandidat_id . "#proces_odlaska");	
break;

case "uredi_pocetak_rada":

	$kandidat_id						= $_POST["kandidat_id"];
	$potvrdi_pocetak_rada				= $_POST["potvrdi_pocetak_rada"];
	$provjera_novog_datuma_pocetka_rada	= $_POST["provjera_novog_datuma_pocetka_rada"];
	$novi_datum_pocetak_rada			= $_POST["novi_datum_pocetak_rada"];

	if($potvrdi_pocetak_rada == 1){
	
		$kandidat_poceo_raditi_query = $db->prepare("UPDATE idk_kandidati SET kandidat_potvrden_pocetak_rada = 1 WHERE kandidat_id =:kandidat_id;");
		$kandidat_poceo_raditi_query->execute(array(
			':kandidat_id' => $kandidat_id
		));

		$update_status_prijave = $db->prepare("UPDATE idk_kandidati SET kandidat_status_prijave = 10 WHERE kandidat_id = :kandidat_id;");
		$update_status_prijave ->execute(array(
			':kandidat_id' => $kandidat_id
		));

		// $get_visak_rate = $db->prepare("SELECT kf_id, kf_type FROM idk_kandidat_financije WHERE kandidat_id = $kandidat_id AND kf_status = 3");
		// $get_visak_rate->execute();
		// while($rata = $get_visak_rate->fetch()){
		// 	$rata_id = $rata["kf_id"];
		// 	$rata_type = $rata["kf_type"];

		// 	$update_rate = $db->prepare("UPDATE idk_kandidat_financije SET kf_status = 1 WHERE kf_id = $rata_id");
		// 	$update_rate->execute();
		// }

		$nalog_id   = getNalogIdByCandidateId($kandidat_id);
		$kandidat_project = getProjectForCandidatR($kandidat_id, $nalog_id);

		$nalog_id = getNalogIdByCandidateId($kandidat_id);
		$rate = getNalogRate($nalog_id);	
		$rata_postoji = false;
		$rata_postoji_mn = false;
		$date = getKandidatDogovoreniPocetakRada($kandidat_id);

		foreach($rate as $rata){
			if(in_array("pocetak rada", $rata)){
				$rata_postoji = true;
			}
			if(in_array("mjeseci nakon", $rata)){
				$rata_postoji_mn = true;
			}
		}
		if($rata_postoji){
			updateKandidatRata($kandidat_id, $nalog_id, getKandidatRata($kandidat_id, 3), $date);
		}
		if($rata_postoji_mn){
			updateRateMjeseciNakon($kandidat_id, $date);
		}

		$log_desc = "Zaposlenik potvrdio dogovoreni datum pocetka rada kandidatu: ".$kandidat_id.".";
		addToLogs($log_desc, 0);

		$get_new_project_query = $db->prepare("SELECT project_id 
												FROM idk_projects 
												WHERE project_name 
													LIKE '%Kandidati počeli sa radom%' 
													AND project_nalogid=:project_nalogid
													AND project_status = 1;");
		
		$get_new_project_query->execute(array(
			':project_nalogid' => $nalog_id
		));

		$get_new_project = $get_new_project_query->fetch();

		$new_project_id = $get_new_project["project_id"];
		
		if(isset($new_project_id)){

			$get_old_project_query = $db->prepare("SELECT  pr.project_id, pk.pk_id
												FROM idk_projects pr 
												JOIN (
													SELECT sqpk.pk_id, sqpk.pk_projectid, sqpk.pk_kandidatid
													FROM idk_project_kandidati sqpk
													WHERE sqpk.pk_kandidatid = :kandidat_id
												) pk
												ON pk.pk_projectid = pr.project_id
												WHERE pr.project_nalogid = :nalog_id
												AND pr.project_name LIKE '%Ugovor%'");
			$get_old_project_query->execute(array(
				':kandidat_id'	=>	$kandidat_id,
				':nalog_id'		=>	$nalog_id
			));								
			$get_old_project = $get_old_project_query->fetch();		

			$old_project_id = $get_old_project["project_id"];
			$pk_id 		= $get_old_project["pk_id"];

			$delete_old_project = $db->prepare("
								DELETE FROM idk_project_kandidati
								WHERE pk_id=:pk_id
								");
			$delete_old_project->execute(array(
				':pk_id'	=> $pk_id
			));							
			

			$add_new_project = $db->prepare("
					INSERT INTO idk_project_kandidati
						(pk_projectid, pk_kandidatid)
					VALUES
						(:pk_projectid, :pk_kandidatid)");		
			$add_new_project->execute(array(
				':pk_projectid'		=>	$new_project_id,
				':pk_kandidatid'	=>	$kandidat_id
			));
			$log_desc2 = "Zaposlenik prebacio kandidata: ".$kandidat_id." iz projekta: ".$old_project_id." u ".$new_project_id.".";
			addToLogsStatusPrijave($old_project_id, $new_project_id, 10, $kandidat_id, 1);
			addToLogs($log_desc2, 0);
			
			// NEW Ako je placanje != 2 (mjesecno)
			if(checkNalogPaymentType($nalog_id) == 2 OR checkNalogPaymentType($nalog_id) == 0){
				$status_id = 4;
				$izvor = 1; 

				$novi_projekt_id = getProjectIDForNalogByName("- Završen" ,$nalog_id);

				$get_old_project_query = $db->prepare("SELECT  pr.project_id, pk.pk_id
													FROM idk_projects pr 
													JOIN (
														SELECT sqpk.pk_id, sqpk.pk_projectid, sqpk.pk_kandidatid
														FROM idk_project_kandidati sqpk
														WHERE sqpk.pk_kandidatid = :kandidat_id
													) pk
													ON pk.pk_projectid = pr.project_id
													WHERE pr.project_nalogid = :nalog_id
													AND pr.project_name LIKE '%Kandidati počeli sa radom%'");
				$get_old_project_query->execute(array(
					':kandidat_id'	=>	$kandidat_id,
					':nalog_id'		=>	$nalog_id
				));								
				$get_old_project = $get_old_project_query->fetch();		

				$old_project_id = $get_old_project["project_id"];
				$pk_id 		= $get_old_project["pk_id"];

				$delete_old_project = $db->prepare("
									DELETE FROM idk_project_kandidati
									WHERE pk_id=:pk_id
									");
				$delete_old_project->execute(array(
					':pk_id'	=> $pk_id
				));							
				

				$add_new_project = $db->prepare("
						INSERT INTO idk_project_kandidati
							(pk_projectid, pk_kandidatid)
						VALUES
							(:pk_projectid, :pk_kandidatid)");		
				$add_new_project->execute(array(
					':pk_projectid'		=>	$novi_projekt_id,
					':pk_kandidatid'	=>	$kandidat_id
				));

				updateKandidatStatusPrijave($kandidat_id, $status_id);
				
				addToLogsStatusPrijave(NULL, $novi_projekt_id, $status_id, $kandidat_id, $izvor);
				$log_desc3 = "Zaposlenik prebacio kandidata: ".$kandidat_id." iz projekta: ".$old_project_id." u ".$novi_projekt_id.".";
				addToLogs($log_desc3, 0);
			}
			// NEW END
		}else{
			addToLogsStatusPrijave($kandidat_project, $kandidat_project, 10, $kandidat_id, 1);
		}
		// Potvrdi reminder 19
		updateReminderStatusCRM($kandidat_id, 19);	

		header("Location: kandidati.php?page=open&id=" . $kandidat_id . "#proces_odlaska");

	}else if($potvrdi_pocetak_rada == 0){

		if($provjera_novog_datuma_pocetka_rada == 0){

			$kandidat_poceo_raditi_query = $db->prepare("UPDATE idk_kandidati SET kandidat_potvrden_pocetak_rada = 0 WHERE kandidat_id =:kandidat_id;");
			$kandidat_poceo_raditi_query->execute(array(
				':kandidat_id' => $kandidat_id
			));

			$log_desc = "Zaposlenik označio da kandidat(".$kandidat_id.") nije počeo sa radom na dogovoreni datum.";
			
			// Potvrdi reminder 19
			updateReminderStatusCRM($kandidat_id, 19);
			addToLogs($log_desc, 0);
			header("Location: kandidati.php?page=open&id=" . $kandidat_id . "#proces_odlaska");


		}
		if($provjera_novog_datuma_pocetka_rada == 1){
			
			$kandidat_poceo_raditi_query = $db->prepare("UPDATE idk_kandidati SET kandidat_potvrden_pocetak_rada = NULL WHERE kandidat_id =:kandidat_id;");
			$kandidat_poceo_raditi_query->execute(array(
				':kandidat_id' => $kandidat_id
			));

			$update_kandidat_dogovoreni_pocetak_rada = $db->prepare("UPDATE idk_kandidati SET kandidat_dogovoreni_pocetak_rada =:kandidat_dogovoreni_pocetak_rada WHERE kandidat_id =:kandidat_id;");
			$update_kandidat_dogovoreni_pocetak_rada->execute(array(
				':kandidat_id'						=>$kandidat_id,
				':kandidat_dogovoreni_pocetak_rada'	=>$novi_datum_pocetak_rada
			));
			$log_desc = "Zaposlenik dodao dogovoreni datum pocetka rada (".$novi_datum_pocetak_rada.") kandidatu: ".$kandidat_id.".";

			$nalog_id = getNalogIdByCandidateId($kandidat_id);
			$rate = getNalogRate($nalog_id);	
			$rata_postoji = false;
			$rata_postoji_mn = false;
			$date = getKandidatDogovoreniPocetakRada($kandidat_id);

			foreach($rate as $rata){
				if(in_array("pocetak rada", $rata)){
					$rata_postoji = true;
				}
				if(in_array("mjeseci nakon", $rata)){
					$rata_postoji_mn = true;
				}
			}
			if($rata_postoji){
				updateKandidatRata($kandidat_id, $nalog_id, getKandidatRata($kandidat_id, 3), $date);
			}
			if($rata_postoji_mn){
				updateRateMjeseciNakon($kandidat_id, $date);
			}

			// Potvrdi reminder 19
			updateReminderStatusCRM($kandidat_id, 19);
			addToLogs($log_desc, 0);
			header("Location: kandidati.php?page=open&id=" . $kandidat_id . "#proces_odlaska");	


		}
	}

break;	
case "insert_contract_sent":
	
	$candId 			= 	$_POST["candidate_id"];
	$trackingCode 		= 	$_POST["tracking_code"];
	$linkTrackingCode 	= 	$_POST["link_tracking_code"];
	$sentDate 			= 	$_POST["send_date"]; //date("y-m-d")
	$nalog_id			= 	$_POST["id_nalog"];
	
	$response 			= 	insertContractSentR($candId, $trackingCode, $linkTrackingCode, $sentDate);

	/* var_dump($candId);
	var_dump($trackingCode);
	var_dump($linkTrackingCode);
	var_dump($sentDate);
	var_dump($nalog_id); */
	echo "RESPONSE: ".$response;
	if($response==1){
		updateReminderStatusCRM($candId, 5);
	}
	//echo "<br><br>Za response message pogledati implementaciju funkcije! <br>";
	header("Location: nalozi.php?page=open_status_prijave&sid=7&nid=".$nalog_id);
	//echo "Na case-u uredite header('Location: ...Link...')! Nije bilo poznato gdje vodi pri izradi backend-a.";	
break;

case "update_contract_sent":

	$candId 	  = $_POST["kandidat_id"];
	$statusChange = $_POST["status_change"];

	if ($statusChange == 2) {

		$dateReceiving = $_POST["date_receiving"];
		$response 	   = updateStatusContractSentR($candId, $statusChange, $dateReceiving);

	} else {

		$dateReceiving = NULL;
		$response 	   = updateStatusContractSentR($candId, $statusChange);
		
	}

	echo "RESPONSE: ".$response;

	echo "<br><br>Za response message pogledati implementaciju funkcije! <br>";
	//header("Location: ...Link...");
	echo "Na case-u uredite header('Location: ...Link...')! Nije bilo poznato gdje vodi pri izradi backend-a.";
break;

case "povezi_dipl":
	$kandidat_id 	= $_GET["id"];
	$kandidat_id_nd = $_GET["nd_id"];
	$dipl_status = $_GET["dipl_status"];

	$sql = "UPDATE idk_kandidati SET kandidat_dipl_id = $kandidat_id_nd, povezan_na_dipl = '1' WHERE kandidat_id = $kandidat_id";
	$povezi_dipl = $db->prepare($sql);
	$povezi_dipl->execute();

	$query = "UPDATE `idk_nostrifikovane_diplome` SET `id_cand_job` = $kandidat_id WHERE `idk_nostrifikovane_diplome`.`id_cand_dipl` = $kandidat_id_nd";
	$povezi_dokumente = $db->prepare($query);
	$povezi_dokumente -> execute();

	if($dipl_status == 6){
		$update_idk_kandidati = $db->prepare("UPDATE idk_kandidati SET kandidat_ima_nostrifikaciju = 1 WHERE kandidat_id = $kandidat_id");
		$update_idk_kandidati->execute();
		try{
			$resultPrebacivanja = prebaciNaPrikupljanjeDokumentacije($kandidat_id, 1);
		}catch (Exception $e){
			$resultPrebacivanja = $e->getMessage();
		}
		
	}else{
		$resultPrebacivanja = "Nije dovoljan status DIPL-a";
	}
	$log_automatsko_prebacivanje = "Status automatskog prebacivanja na prikupljanje dokumentacije: ".$resultPrebacivanja;

	$log_desc = "Zaposlenik povezao kandidata (".$kandidat_id.") sa DIPL kandidatom (".$kandidat_id_nd."). ".$log_automatsko_prebacivanje."";
	$log_type = 0;
	addToLogs($log_desc, $log_type);

	header("Location: kandidati.php?page=open&id=$kandidat_id");	
break;

case "add_nostrifikacija":
	$nostrifikacija     = $_POST["nostrifikacija"];
	$kandidat_id 	    = $_POST["kandidat_id"];
	$kandidat_dipl_id   = $_POST["dipl_id"];
	$povezivanje		= $_POST["povezivanje"];
	$approved_pstatuses = [1,2,3,4,6,7,8,9,10,11,12];

	///////////////////////////////////////////////////////////////////////////////////////////
	//---------SLUCAJ KAD SE KANDIDATU OZNACAVA NOSTRIFIKACIJA PREKO POVEZIVANJA---------///
	//////////////////////////////////////////////////////////////////////////////////////////
	if($povezivanje == 1){

		$sql_povezivanje = "UPDATE idk_kandidati SET kandidat_dipl_id = $kandidat_dipl_id, povezan_na_dipl = '1' WHERE kandidat_id = $kandidat_id";
		$povezi_dipl = $db->prepare($sql_povezivanje);
		$povezi_dipl->execute();

		$log_desc = "Zaposlenik povezao kandidata ($kandidat_id) sa DIPL kandidatom ($kandidat_dipl_id).";
		$log_type = 0;
		addToLogs($log_desc, $log_type);

		$sql= "SELECT 
					status_nd_kandidata,
					pstatus_nd_kandidata,
					razlog_biljeska_nd
				FROM 
					idk_nd_kandidata 
				LEFT JOIN
					(
						SELECT
							id_kandidata_biljeska_nd,
							razlog_biljeska_nd
						FROM
							idk_nd_kandidata_biljeske
						WHERE
							razlog_biljeska_nd = 32
						AND
							id_kandidata_biljeska_nd = $kandidat_dipl_id
					) as biljeska
				ON 
					idk_nd_kandidata.id_broj_nd_kandidata = biljeska.id_kandidata_biljeska_nd
				WHERE 
					id_broj_nd_kandidata = $kandidat_dipl_id
				";
		$get_dipl_status = $db->prepare($sql);
		$get_dipl_status->execute();
		$result = $get_dipl_status->fetch();

		$dipl_status  = $result["status_nd_kandidata"];
		$dipl_pstatus = $result["pstatus_nd_kandidata"];
		$razlog 	  = $result["razlog_biljeska_nd"];

	//--------SLUCAJ 1: kandidat postoji u DIPL-u i nalazai se na statusima prodaje---------//
		if($dipl_status == 1 && in_array($dipl_pstatus, $approved_pstatuses) && $nostrifikacija == 1){
			$update_idk_kandidati = $db->prepare("UPDATE idk_kandidati SET kandidat_ima_nostrifikaciju = 1 WHERE kandidat_id = $kandidat_id");
			$update_idk_kandidati->execute();
			
			promjenaStatusaDIPLKandidat($kandidat_dipl_id,7,0,1);
			$biljeska_text = "Automatska biljeska: zaposlenik je oznacio da kandidat vec ima nostrifikovanu diplomu";
			$biljeska_sql = "INSERT INTO 
								idk_nd_kandidata_biljeske 
								(
									id_kandidata_biljeska_nd, 
									status_biljeska_nd, 
									tip_biljeska_nd, 
									razlog_biljeska_nd, 
									sadrzaj_biljeska_nd, 
									vrijeme_dodavanja_biljeska_nd, 
									vrijeme_grupa_biljeska_nd, 
									dodao_zaposlenik_biljeska_nd 
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
									:dodao_zaposlenik_biljeska_nd
								)";
			$insert_biljeska = $db->prepare($biljeska_sql);
			$insert_biljeska->execute([
				":id_kandidata_biljeska_nd" => $kandidat_dipl_id, 
				":status_biljeska_nd" => 2,
				":tip_biljeska_nd" => 15,
				":razlog_biljeska_nd" => 32,
				":sadrzaj_biljeska_nd" => $biljeska_text,
				":vrijeme_dodavanja_biljeska_nd" => date("Y-m-d H:i:s"),
				":vrijeme_grupa_biljeska_nd" => date("Y"),
				":dodao_zaposlenik_biljeska_nd" => $logged_employee_id
			]);
			
			try{
				$resultPrebacivanja = prebaciNaPrikupljanjeDokumentacije($kandidat_id, 1);
			}catch (Exception $e){
				$resultPrebacivanja = $e->getMessage();
			}

			$log_desc = "Zaposlenik oznacio da kandidat (".$kandidat_id.") ima nostrifikovanu diplomu. Status automatskog prebacivanja na prikupljanje dokumentacije: ".$resultPrebacivanja."";
			$log_type = 0;
			addToLogs($log_desc, $log_type); 
		}

	//--------SLUCAJ 2: kandidat postoji u DIPL-u i nalazai se na statusu arhiva, ali nije oznacen razlog da ima nostrifikovanu diplomu---------//
		elseif($dipl_status == 7 && $razlog == NULL && $nostrifikacija == 1){
			$update_idk_kandidati = $db->prepare("UPDATE idk_kandidati SET kandidat_ima_nostrifikaciju = 1 WHERE kandidat_id = $kandidat_id");
			$update_idk_kandidati->execute();

			$biljeska_text = "Automatska biljeska: zaposlenik je oznacio da kandidat vec ima nostrifikovanu diplomu";
			$biljeska_sql = "INSERT INTO 
								idk_nd_kandidata_biljeske 
								(
									id_kandidata_biljeska_nd, 
									status_biljeska_nd, 
									tip_biljeska_nd, 
									razlog_biljeska_nd, 
									sadrzaj_biljeska_nd, 
									vrijeme_dodavanja_biljeska_nd, 
									vrijeme_grupa_biljeska_nd, 
									dodao_zaposlenik_biljeska_nd 
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
									:dodao_zaposlenik_biljeska_nd 
								)";
			$insert_biljeska = $db->prepare($biljeska_sql);
			$insert_biljeska->execute([
				":id_kandidata_biljeska_nd" => $kandidat_dipl_id, 
				":status_biljeska_nd" => 2,
				":tip_biljeska_nd" => 15,
				":razlog_biljeska_nd" => 32,
				":sadrzaj_biljeska_nd" => $biljeska_text,
				":vrijeme_dodavanja_biljeska_nd" => date("Y-m-d H:i:s"),
				":vrijeme_grupa_biljeska_nd" => date("Y"),
				":dodao_zaposlenik_biljeska_nd" => $logged_employee_id
			]);

			try{
				$resultPrebacivanja = prebaciNaPrikupljanjeDokumentacije($kandidat_id, 1);
			}catch (Exception $e){
				$resultPrebacivanja = $e->getMessage();
			}

			$log_desc = "Zaposlenik oznacio da kandidat (".$kandidat_id.") ima nostrifikovanu diplomu. Status automatskog prebacivanja na prikupljanje dokumentacije: ".$resultPrebacivanja."";
			$log_type = 0;
			addToLogs($log_desc, $log_type); 
		}

	//--------SLUCAJ 3: kandidat postoji u DIPL-u i nalazai se na statusu arhiva i oznacen je pod razlogom da ima nostrifikovanu diplomu---------//
		elseif($dipl_status == 7 && $razlog == 32 && $nostrifikacija == 1){
			$update_idk_kandidati = $db->prepare("UPDATE idk_kandidati SET kandidat_ima_nostrifikaciju = 1 WHERE kandidat_id = $kandidat_id");
			$update_idk_kandidati->execute();

			try{
				$resultPrebacivanja = prebaciNaPrikupljanjeDokumentacije($kandidat_id, 1);
			}catch (Exception $e){
				$resultPrebacivanja = $e->getMessage();
			}

			$log_desc = "Zaposlenik oznacio da kandidat (".$kandidat_id.") ima nostrifikovanu diplomu. Status automatskog prebacivanja na prikupljanje dokumentacije: ".$resultPrebacivanja."";
			$log_type = 0;
			addToLogs($log_desc, $log_type); 
		}

		header("Location: kandidati.php?page=open&id=$kandidat_id");	
	///////////////////////////////////////////////////////////////////////////////////////////
	//---------SLUCAJ KAD SE KANDIDATU NE OZNACAVA NOSTRIFIKACIJA PREKO POVEZIVANJA---------///
	//////////////////////////////////////////////////////////////////////////////////////////
	} else {
		$sql = "SELECT 
					status_nd_kandidata, 
					pstatus_nd_kandidata,
					razlog_biljeska_nd
				FROM 
					idk_nd_kandidata 
				LEFT JOIN
					(
						SELECT
							id_kandidata_biljeska_nd,
							razlog_biljeska_nd
						FROM
							idk_nd_kandidata_biljeske
						WHERE
							razlog_biljeska_nd = 32
						AND
							id_kandidata_biljeska_nd = $kandidat_dipl_id
					) as biljeska
				ON 
					idk_nd_kandidata.id_broj_nd_kandidata = biljeska.id_kandidata_biljeska_nd
				WHERE 
					id_broj_nd_kandidata = $kandidat_dipl_id";

		$get_dipl_status = $db->prepare($sql);
		$get_dipl_status->execute();
		$result = $get_dipl_status->fetch();

		$dipl_status  = $result["status_nd_kandidata"];
		$dipl_pstatus = $result["pstatus_nd_kandidata"];
		$razlog 	  = $result["razlog_biljeska_nd"];

	//--------SLUCAJ 1: kandidat postoji u DIPL-u i nalazai se na statusima prodaje---------//
		if($dipl_status == 1 && in_array($dipl_pstatus, $approved_pstatuses) && $nostrifikacija == 1){
			$update_idk_kandidati = $db->prepare("UPDATE idk_kandidati SET kandidat_ima_nostrifikaciju = 1 WHERE kandidat_id = $kandidat_id");
			$update_idk_kandidati->execute();
			
			promjenaStatusaDIPLKandidat($kandidat_dipl_id,7,0,1);

			try{
				$resultPrebacivanja = prebaciNaPrikupljanjeDokumentacije($kandidat_id, 1);
			}catch (Exception $e){
				$resultPrebacivanja = $e->getMessage();
			}

			$biljeska_text = "Automatska biljeska: zaposlenik je oznacio da kandidat vec ima nostrifikovanu diplomu";
			$biljeska_sql = "INSERT INTO 
								idk_nd_kandidata_biljeske 
								(
									id_kandidata_biljeska_nd, 
									status_biljeska_nd, 
									tip_biljeska_nd, 
									razlog_biljeska_nd, 
									sadrzaj_biljeska_nd, 
									vrijeme_dodavanja_biljeska_nd, 
									vrijeme_grupa_biljeska_nd, 
									dodao_zaposlenik_biljeska_nd 
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
									:dodao_zaposlenik_biljeska_nd 
								)";
			$insert_biljeska = $db->prepare($biljeska_sql);
			$insert_biljeska->execute([
				":id_kandidata_biljeska_nd" => $kandidat_dipl_id, 
				":status_biljeska_nd" => 2,
				":tip_biljeska_nd" => 15,
				":razlog_biljeska_nd" => 32,
				":sadrzaj_biljeska_nd" => $biljeska_text,
				":vrijeme_dodavanja_biljeska_nd" => date("Y-m-d H:i:s"),
				":vrijeme_grupa_biljeska_nd" => date("Y"),
				":dodao_zaposlenik_biljeska_nd" => $logged_employee_id
			]);
			
			$log_desc = "Zaposlenik oznacio da kandidat (".$kandidat_id.") ima nostrifikovanu diplomu. Status automatskog prebacivanja na prikupljanje dokumentacije: ".$resultPrebacivanja."";
			$log_type = 0;
			addToLogs($log_desc, $log_type); 
		}

	//--------SLUCAJ 2: kandidat postoji u DIPL-u i nalazai se na statusu arhiva, ali nije oznacen razlog da ima nostrifikovanu diplomu---------//
		elseif($dipl_status == 7 && $razlog == NULL && $nostrifikacija == 1){
			$update_idk_kandidati = $db->prepare("UPDATE idk_kandidati SET kandidat_ima_nostrifikaciju = 1 WHERE kandidat_id = $kandidat_id");
			$update_idk_kandidati->execute();

			try{
				$resultPrebacivanja = prebaciNaPrikupljanjeDokumentacije($kandidat_id, 1);
			}catch (Exception $e){
				$resultPrebacivanja = $e->getMessage();
			}

			$biljeska_text = "Automatska biljeska: zaposlenik je oznacio da kandidat vec ima nostrifikovanu diplomu";
			$biljeska_sql = "INSERT INTO 
								idk_nd_kandidata_biljeske 
								(
									id_kandidata_biljeska_nd, 
									status_biljeska_nd, 
									tip_biljeska_nd, 
									razlog_biljeska_nd, 
									sadrzaj_biljeska_nd, 
									vrijeme_dodavanja_biljeska_nd, 
									vrijeme_grupa_biljeska_nd, 
									dodao_zaposlenik_biljeska_nd 
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
									:dodao_zaposlenik_biljeska_nd 
								)";
			$insert_biljeska = $db->prepare($biljeska_sql);
			$insert_biljeska->execute([
				":id_kandidata_biljeska_nd" => $kandidat_dipl_id, 
				":status_biljeska_nd" => 2,
				":tip_biljeska_nd" => 15,
				":razlog_biljeska_nd" => 32,
				":sadrzaj_biljeska_nd" => $biljeska_text,
				":vrijeme_dodavanja_biljeska_nd" => date("Y-m-d H:i:s"),
				":vrijeme_grupa_biljeska_nd" => date("Y"),
				":dodao_zaposlenik_biljeska_nd" => $logged_employee_id
			]);

			$log_desc = "Zaposlenik oznacio da kandidat (".$kandidat_id.") ima nostrifikovanu diplomu. Status automatskog prebacivanja na prikupljanje dokumentacije: ".$resultPrebacivanja."";
			$log_type = 0;
			addToLogs($log_desc, $log_type); 
		}

	//--------SLUCAJ 3: kandidat postoji u DIPL-u i nalazai se na statusu arhiva i oznacen je pod razlogom da ima nostrifikovanu diplomu---------//
		elseif($dipl_status == 7 && $razlog == 32 && $nostrifikacija == 1){
			$update_idk_kandidati = $db->prepare("UPDATE idk_kandidati SET kandidat_ima_nostrifikaciju = 1 WHERE kandidat_id = $kandidat_id");
			$update_idk_kandidati->execute();

			try{
				$resultPrebacivanja = prebaciNaPrikupljanjeDokumentacije($kandidat_id, 1);
			}catch (Exception $e){
				$resultPrebacivanja = $e->getMessage();
			}

			$log_desc = "Zaposlenik oznacio da kandidat (".$kandidat_id.") ima nostrifikovanu diplomu. Status automatskog prebacivanja na prikupljanje dokumentacije: ".$resultPrebacivanja."";
			$log_type = 0;
			addToLogs($log_desc, $log_type); 
		}

	//-------SLUCAJ 4: kandidat ne postoji u DIPL-u, a ima nostrifikovanu diplomu-------//
		elseif($kandidat_dipl_id == NULL && $nostrifikacija == 1){
			$update_idk_kandidati = $db->prepare("UPDATE idk_kandidati SET kandidat_ima_nostrifikaciju = 1 WHERE kandidat_id = $kandidat_id");
			$update_idk_kandidati->execute();

			$log_desc = "Zaposlenik oznacio da kandidat ($kandidat_id) ima nostrifikovanu diplomu";
			$log_type = 0;
			addToLogs($log_desc, $log_type); 
		}

	//---------Oznacavanje da kandidat nema nostrifikovanu diplomu---------//
		elseif($nostrifikacija == 0){
			$update_idk_kandidati = $db->prepare("UPDATE idk_kandidati SET kandidat_ima_nostrifikaciju = 0 WHERE kandidat_id = $kandidat_id");
			$update_idk_kandidati->execute();

			$log_desc = "Zaposlenik oznacio da kandidat ($kandidat_id) nema nostrifikovanu diplomu";
			$log_type = 0;
			addToLogs($log_desc, $log_type); 
		}
		header("Location: kandidati.php?page=open&id=$kandidat_id");	
	}
break;
case "getProjectionDuration":
	function getStatusNameFromDPR($dpr_id){
		switch ($dpr_id) {
			case 2:
				return "Priprema za intervju";
			break;
			case 4:
				return "Intervju";
			break;
			case 6:
				return "Čeka ugovor";
			break;
			case 8:
				return "Potpisan ugovor";
			break;
			case 10:
				return "Lead";
			break;
			case 12:
				return "Prikupljanje dokumentacije";
			break;
			case 14:
				return "Poslana pošta";
			break;
			case 16:
				return "U obradi";
			break;
			case 18:
				return "Dopuna dokumentacije";
			break;
			case 34:
				return "Kretanje na kurs";
			break;
			case 36:
				return "Podnivo";
			break;
			case 38:
				return "Čeka datum ispita";
			break;
			case 46:
				return "Prikupljanje dokumentacije";
			break;
			case 48:
				return "Čeka termin";
			break;
			case 50:
				return "Čeka vizu";
			break;
			case 52:
				return "Dopuna dokumentacije";
			break;
			case 54:
				return "Odbijena viza";
			break;
			case 56:
				return "Dobio vizu";
			break;
			case 58:
				return "Kretanje na kurs (Škola)";
			break;
			default:
				return "Ako ovo vidite, kontaktirajte programere.";
			break;
		}
	}
	header("Content-Type: application/json");
	$query = $db->prepare("SELECT dpr_id as statusId, dpr_group as groupId, dpr_duration as statusDuration, dpr_description as statusDescription FROM idk_duration_per_status");
	$query->execute();
	$rows = $query->fetchAll(PDO::FETCH_OBJ);

	$groups = [];
	$groupTitles = [
		"1" => "Recruiting",
		"2" => "DIPL",
		"3" => "Jezik",
		"4" => "Viza" 
	];

	foreach($rows as $row){
		$groups[$row->groupId]["groupTitle"] = $groupTitles[$row->groupId];
		$groups[$row->groupId]["statuses"][] = $row;
		end($groups[$row->groupId]["statuses"])->statusTitle = getStatusNameFromDPR($row->statusId);
		end($groups[$row->groupId]["statuses"])->statusDuration = intval($row->statusDuration);
	}

	$groupsArray = [];

	foreach($groups as $key => $group){
		$groupsArray[] = $group;
		end($groupsArray)["groupId"] = $key;
	}


	echo json_encode($groupsArray);

break;
case "setProjectionDuration":
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$db->beginTransaction();


	foreach($_POST as $key => $value){
		try{

			$query = $db->prepare("UPDATE idk_duration_per_status SET dpr_duration = :duration WHERE dpr_id = :id");
			$query->execute([
				":duration" => $value,
				":id"  => $key
			]);


		}catch(Exception $e){

			$db->rollBack();
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
			http_response_code(500);
			die($e->getMessage());

		}
	}

	$db->commit();
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);

	$ids = implode(" ,", array_keys($_POST));
	$values = implode(" ,", array_values($_POST));

	addToLogs("Promijenjeno trajanje statusa za projekciju. ($ids) => ($values)", 0);


break;
case 'poveziNostrifikaciju':
	
	$kandidat_id     	= $_POST['kandidat_id'];
	$kandidat_dipl_id	= $_POST['kandidat_dipl_id'];
	$nalog_id			= $_POST['nalog_id'];
	$documentFullPath	= $_POST['documentFullPath'];
	$full_recognition   = $_POST['full_recognition'];

	$log_date=date('Y-m-d H:i:s');
	
	$add_new_query=$db->prepare("
				INSERT INTO idk_nostrifikovane_diplome (id_cand_dipl, id_cand_job, file_nd, upload_date_nd, upload_employee_id, full_recognition) 
												VALUES (:id_cand_dipl, :id_cand_job, :file_nd, :upload_date_nd, :upload_employee_id, :full_recognition);");
	$add_new_query->execute(array(
		':id_cand_dipl'			=> $kandidat_dipl_id,
		':id_cand_job'  		=> $kandidat_id,
		':file_nd'				=> $documentFullPath,
		':upload_date_nd'		=> $log_date,
		':upload_employee_id'	=> $logged_employee_id, 
		':full_recognition'		=> $full_recognition
	));
	$preko=1;
	try{
		$result=prebaciNaPrikupljanjeDokumentacije($kandidat_id,$preko);
		if($result == 2)
			$msg_nost = "&infoNost=28";
		else
			$msg_nost = "";

	}catch (Exception $e){
		
		$result=$e->getMessage();
		$msg_nost = "";
	}
	$log_desc="Zaposlenik povezao nostrifikaciju diplome kandidatu ".$kandidat_id;
	$log_type=0;
	addToLogs($log_desc, $log_type);
	header("Location: nalozi.php?page=open_status_prijave&sid=9&nid=$nalog_id");	
break;	

case 'dodajNostrifikaciju':
	
	$kandidat_id     	= $_POST['kandidat_id'];
	$kandidat_dipl_id	= $_POST['kandidat_dipl_id'];
	$nalog_id			= $_POST['nalog_id'];
	$documentFile		= $_FILES['naziv_dokument_new'];
	$diploma_path		= $_POST['certificate_path'];
	$fullRecognition    = $_POST['fullRecognition'];
	$date_now			= date('Y-m-d H:i:s');
	$filepath="";
	try{
		$filepath = uploadDocumentFileR($documentFile,"/files/dokumenti_ND_kandidat/");
	}                                                                                                                                                                                                                    
	catch(Exception $e)
	{
		http_response_code(500);
		die($e->getMessage());
	}
	$add_new_query=$db->prepare("
				INSERT INTO idk_nostrifikovane_diplome (id_cand_dipl, id_cand_job, file_nd, upload_date_nd, upload_employee_id, full_recognition) 
												VALUES (:id_cand_dipl, :id_cand_job, :file_nd, :upload_date_nd, :upload_employee_id, :full_recognition);");
	$add_new_query->execute(array(
		':id_cand_dipl'			=> $kandidat_dipl_id,
		':id_cand_job'  		=> $kandidat_id,
		':file_nd'				=> $filepath,
		':upload_date_nd'		=> $date_now,
		':upload_employee_id'	=> $logged_employee_id, 
		':full_recognition'     => $fullRecognition
	));
	$file_name;
	//File extension
	$file_name = explode('/files/dokumenti_ND_kandidat/', $filepath);
	$add_to_documents_nd_query = $db->prepare("INSERT INTO `idk_nd_kandidata_dokumenti` 
													(naziv_dokument_nd, naziv_dokument_ostali_nd, id_kandidata_dokument_nd, vrijeme_dodavanja_dokument_nd, dodao_zaposlenik_dokument_nd, tip_dokumenta_status, broj_rate)
												VALUES 
													(:file_realname, 'Gleichwertigkeitsbescheid', :kandidat_id, :date_now, :employee_id, 1, 1)");
	$add_to_documents_nd_query -> execute(array(
		':file_realname' 	=> $file_name[1],
		':kandidat_id' 		=> $kandidat_dipl_id,
		':date_now' 		=> $date_now,
		':employee_id'		=> $logged_employee_id


	));
	$preko=1;
	try{
		$result=prebaciNaPrikupljanjeDokumentacije($kandidat_id,$preko);
		if($result == 2)
			$msg_nost = "&infoNost=28";
		else
			$msg_nost = "";

	}catch (Exception $e){
		
		$result=$e->getMessage();
		$msg_nost = "";
	}
	$log_desc="Zaposlenik dodao i povezao nostrifikaciju diplome kandidatu ".$kandidat_id;
	$log_type=0;
	addToLogs($log_desc, $log_type);
	header("Location: nalozi.php?page=open_status_prijave&sid=9&nid=$nalog_id");	
break;	
case "kandidati":
    $id = $_REQUEST["id"];
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   
    $query = $db->prepare("SELECT kandidat_ime, kandidat_prezime FROM idk_kandidati WHERE kandidat_id = :id");
    $query->execute([":id" => $id]);

    $result = $query->fetch(PDO::FETCH_OBJ);

    echo json_encode($result);
break;
case "kandidatiDIPL":
    $id = $_REQUEST["id"];
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   
    $query = $db->prepare("SELECT id_broj_nd_kandidata, ime_nd_kandidata as kandidat_ime, prezime_nd_kandidata as kandidat_prezime FROM idk_nd_kandidata WHERE id_broj_nd_kandidata = :id");
    $query->execute([":id" => $id]);

    $result = $query->fetch(PDO::FETCH_OBJ);

    echo json_encode($result);
break;
case "nalozi":
    $id = $_REQUEST["id"];
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   
    $query = $db->prepare("SELECT nalog_naziv FROM idk_nalozi WHERE nalog_id = :id");
    $query->execute([":id" => $id]);

    $result = $query->fetch(PDO::FETCH_OBJ);

    echo json_encode($result);
break;
case "fuzzyKandidat":
    $search = $_REQUEST["search"];
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   
    $sql = "
        SELECT kandidat_id, kandidat_ime, kandidat_prezime FROM idk_kandidati WHERE CONCAT(TRIM(kandidat_ime), TRIM(kandidat_prezime)) LIKE '%$search%' OR CONCAT(TRIM(kandidat_prezime), TRIM(kandidat_ime)) LIKE '%$search%' 
";
    $query = $db->prepare($sql);
    $query->execute();

    $result = $query->fetchAll(PDO::FETCH_OBJ);

    echo json_encode($result);
break;

case "bracnoStanjeKandidatEdit":
	$kandidatId = $_POST['kandidatId'] ?? null;
	$bracnoStanje = $_POST['bracnoStanje'] ?? null;

	if($kandidatId == null || $bracnoStanje == null)
	{
		http_response_code(500);
		die("Nisu proslijedjeni svi parametri");
	}

	$sql = "UPDATE idk_kandidati SET kandidat_bracno_stanje = :kandidat_bracno_stanje WHERE kandidat_id = :kandidat_id";
	$query = $db->prepare($sql);
	$res = $query->execute(array(
		':kandidat_bracno_stanje' => $bracnoStanje,
		':kandidat_id' => $kandidatId
	));
break; 

case "atuPaketEdit":
	$kandidatId = $_POST['kandidatId'] ?? null;
	$paket = $_POST['paketAtu'] ?? null;
	
	if($kandidatId == null || $paket == null)
	{
		http_response_code(500);
		die("Nisu proslijedjeni svi parametri");
	}

	$sql = "UPDATE idk_kandidati SET kandidat_atu_paket = :kandidat_atu_paket WHERE kandidat_id = :kandidat_id";
	$query = $db->prepare($sql);
	$res = $query->execute(array(
		':kandidat_atu_paket' => $paket,
		':kandidat_id' => $kandidatId
	));
break; 

case "updateRadnoIskustvoUStruci":
	$kandidat_id = $_POST['kandidatId'] ?? null;
	$radno_iskustvo = $_POST['radnoIskustvo'] ?? null;
	$radno_iskustvo_trajanje = $_POST['radnoIskustvoGodine'] ?? null;

	if($kandidat_id == null || $radno_iskustvo == null)
	{
		http_response_code(500);
		die("Nisu proslijedjeni svi parametri");
	}

	if($radno_iskustvo != 0 && $radno_iskustvo != 1)
	{
		http_response_code(500);
		die("Radno iskustvo nije validno");
	}

	if($radno_iskustvo != 1){
		$radno_iskustvo_trajanje = NULL;
	}

	$sql = "UPDATE idk_kandidati SET kandidat_iskustvo_u_struci = :radno_iskustvo, kandidat_iskustvo_u_struci_trajanje = :kandidat_iskustvo_u_struci_trajanje WHERE kandidat_id = :kandidat_id";
	$query = $db->prepare($sql);
	$res = $query->execute(array(
		':radno_iskustvo' => $radno_iskustvo,
		':kandidat_iskustvo_u_struci_trajanje' => $radno_iskustvo_trajanje,
		':kandidat_id' => $kandidat_id
	));
break;

case "nalogSmjerKriterijEdit":
	$nalogSmjerId = $_POST['nalogSmjerId'] ?? null;
	$nalogSmjerVr = $_POST['nalogSmjerVr'] ?? null;

	if($nalogSmjerId == null || $nalogSmjerVr == null)
	{
		http_response_code(500);
		die("Nisu proslijedjeni svi parametri");
	}

	$sql = "UPDATE idk_nalog_smjer SET nalog_kriterij = :nalog_kriterij WHERE id  = :id ";
	$query = $db->prepare($sql);
	$res = $query->execute(array(
		':nalog_kriterij' => $nalogSmjerVr,
		':id' => $nalogSmjerId
	));
break; 

case "nalogSmjeroviKriterijEdit":
	$nalogId = $_POST['nalogId'] ?? null;
	$nalogVr = $_POST['nalogVr'] ?? null;
	
	if($nalogId == null || $nalogVr == null)
	{
		http_response_code(500);
		die("Nisu proslijedjeni svi parametri");
	}

	$sql = "UPDATE idk_nalozi SET nalog_smjerovi_kriterij = :nalog_smjerovi_kriterij WHERE nalog_id   = :nalog_id";
	$query = $db->prepare($sql);
	$res = $query->execute(array(
		':nalog_smjerovi_kriterij' => $nalogVr,
		':nalog_id' => $nalogId
	));
break; 

case "updateVozackaDozvolaKandidat":
	$kandidatId = $_POST['kandidatId'] ?? null;
	$vozackaDozvola = $_POST['vozackaDozvola'] ?? null;
	$vozackaDozvolaKategorija = $_POST['vozackaDozvolaKategorija'] ?? null;

	if ( $kandidatId == null ) {
		http_response_code(500);
		die("Problem sa ID kandidata");
	} 

	if( ($vozackaDozvola == "Ne" AND $vozackaDozvolaKategorija != NULL) OR ($vozackaDozvola == "Da" AND $vozackaDozvolaKategorija == NULL) ) {
		http_response_code(500);
		die("Nisu proslijedjeni svi parametri");
	}

	$checkOldValue = $db->prepare("SELECT kandidat_vozacka_dozvola, kandidat_vozacka_kategorija FROM idk_kandidati WHERE kandidat_id = :kandidat_id");
	$checkOldValue->execute(array(':kandidat_id' => $kandidatId ));
	$rowCheckOldValue = $checkOldValue->fetch();

	$oldValueVozacka = $rowCheckOldValue["kandidat_vozacka_dozvola"];
	$oldValueKategorija = $rowCheckOldValue["kandidat_vozacka_kategorija"];

	if ( $oldValueVozacka == NULL ) {

		$sub_log_desc = "Kandidat nije imao unešenu informaciju o vozackoj.";

	}else {

		$sub_log_desc = "Kandidat je imao unešene informacije o vozackoj [".$oldValueVozacka."] i kategorije [".$oldValueKategorija."].";

	}

	$sql = "UPDATE idk_kandidati SET kandidat_vozacka_dozvola = :vozacka_dozvola, kandidat_vozacka_kategorija = :vozacka_kategorija WHERE kandidat_id = :kandidat_id";
	$query = $db->prepare($sql);
	$res = $query->execute(array(
		':vozacka_dozvola' => $vozackaDozvola,
		':vozacka_kategorija' => $vozackaDozvolaKategorija,
		':kandidat_id' => $kandidatId
	));

	$log_desc = "DIPL -> Zaposlenik uredio informaciju za vozačku dozvolu kod kandidata ID: [".$kandidatId."] sa vrijednostima [".$vozackaDozvola." : ".$vozackaDozvolaKategorija."]. ".$sub_log_desc."";
	addToLogs($log_desc, 0);

break;

case "updateDatumRodjenjaKandidat":

	$kandidatIdSave = $_POST['kandidatIdSave'] ?? null;
	$datumRodjenjaSave = $_POST['datumRodjenjaSave'] ?? null;

	if($kandidatIdSave == null || $datumRodjenjaSave == null)
	{
		http_response_code(500);
		die("Nisu proslijedjeni svi parametri");
	}

	$checkOldValue = $db->prepare("SELECT kandidat_datumrodjenja FROM idk_kandidati WHERE kandidat_id = :kandidat_id");
	$checkOldValue->execute(array(':kandidat_id' => $kandidatIdSave ));
	$rowCheckOldValue = $checkOldValue->fetch();

	$oldValue = $rowCheckOldValue["kandidat_datumrodjenja"];
	
	if ( $oldValue == NULL ) {
		$sub_log_desc = "Kandidat nije imao unešen datum rođenja.";
	}else {
		$sub_log_desc = "Stara vrijednost je [".$oldValue."]";
	}

	$sql = "UPDATE idk_kandidati SET kandidat_datumrodjenja = :datumRodjenja WHERE kandidat_id = :kandidat_id";
	$query = $db->prepare($sql);
	$res = $query->execute(array(
		':datumRodjenja' => $datumRodjenjaSave,
		':kandidat_id' => $kandidatIdSave
	));

	$log_desc = "DIPL -> Zaposlenik uredio informaciju za datum rodjenja kod kandidata ID: [".$kandidatIdSave."] čija je vrijednost [".$datumRodjenjaSave."]. ".$sub_log_desc."";
	addToLogs($log_desc, 0);

break;

case "updateSlanjeKandidataNaGlossu":
	$nalog_id = $_POST['nalogId'] ?? null;
	$ide_na_glossu = $_POST['ideNaGlossu'] ?? null;

	if($nalog_id == null || $ide_na_glossu == null)
	{
		http_response_code(500);
		die("Nisu proslijedjeni svi parametri");
	}

	if($ide_na_glossu != 0 && $ide_na_glossu != 1)
	{
		http_response_code(500);
		die("Vrijednost nije validna");
	}

	$sql = "UPDATE idk_nalozi SET nalog_slanje_na_glosu = :ide_na_glossu WHERE nalog_id = :nalog_id";
	$query = $db->prepare($sql);
	$res = $query->execute(array(
		':ide_na_glossu' => $ide_na_glossu,
		':nalog_id' => $nalog_id
	));
break;
case "changeDocumentZB":
	$nrd_id = $_POST['nrd_id_zb'];
	$to_update = $_POST['to_update_zb'];
	$nalog_id = $_POST['nalog_id_zb'];
	$query_update = $db->prepare("UPDATE idk_pp_nalog_required_documents SET nrd_west_balkan = :nrd_west_balkan WHERE nrd_id = :nrd_id");
	$query_update->execute(array(
		":nrd_west_balkan" => $to_update,
		":nrd_id" => $nrd_id
	));
	// var_dump($query_update->errorInfo());
	$log_desc = "Promijenjena potrebnost dokumenta ".$nrd_id." zapadni balkan na ".$to_update.".";
	addToLogs($log_desc, 0);

	header("Location: nalozi?page=open&id=$nalog_id&tab=vizadokumenti");

break;
case "changeDocumentRI":
	$nrd_id = $_POST['nrd_id_ri'];
	$to_update = $_POST['to_update_ri'];
	$nalog_id = $_POST['nalog_id_ri'];
	$query_update = $db->prepare("UPDATE idk_pp_nalog_required_documents SET nrd_work_experience = :nrd_work_experience WHERE nrd_id = :nrd_id");
	$query_update->execute(array(
		":nrd_work_experience" => $to_update,
		":nrd_id" => $nrd_id
	));
	// var_dump($query_update->errorInfo());
	$log_desc = "Promijenjena potrebnost dokumenta ".$nrd_id." radno iskustvo na ".$to_update.".";
	addToLogs($log_desc, 0);

	header("Location: nalozi?page=open&id=$nalog_id&tab=vizadokumenti");

break;

case "changeDocumentSK":
	$nrd_id = $_POST['nrd_id_sk'];
	$to_update = $_POST['to_update_sk'];
	$nalog_id = $_POST['nalog_id_sk'];
	$query_update = $db->prepare("UPDATE idk_pp_nalog_required_documents SET nrd_skilled_candidates = :nrd_skilled_candidates WHERE nrd_id = :nrd_id");
	$query_update->execute(array(
		":nrd_skilled_candidates" => $to_update,
		":nrd_id" => $nrd_id
	));
	// var_dump($query_update->errorInfo());
	$log_desc = "Promijenjena potrebnost dokumenta ".$nrd_id." strucni kadar na ".$to_update.".";
	addToLogs($log_desc, 0);

	header("Location: nalozi?page=open&id=$nalog_id&tab=vizadokumenti");

break;

case "kandidat_dosao_na_intervju":
	$kandidat_id = $_POST['kandidat_id_dosao'];
	$nalog_id = $_POST['nalog_id_dosao'];
	$project_id = $_POST['project_id_dosao'];
	$pap_group_id = $_POST['pap_group_id_dosao'];
	$note = "Zaposlenik ".getEmployeeFullnameR()." potvrdio da je kandidat došao na razgovor.";

	/* UPDATE ZADNJE AKTIVNE FB BILJESKE KOD KANDIDATA START */
	/* 1 - CASTING */
		updateLastActiveTaskForCandidate($kandidat_id, 1);
	/* UPDATE ZADNJE AKTIVNE FB BILJESKE KOD KANDIDATA END */

	/* RESET TF STATUSA U GLAVNOJ TABELI KANDIDATA START */
		updateTaskForceStatusForCandidate($kandidat_id, null);
	/* RESET TF STATUSA U GLAVNOJ TABELI KANDIDATA END */

	/* UNOS TASK FORCE START */
	$queryInsertTF = $db->prepare("
	INSERT INTO idk_task_force
			(
				tf_candidate_id, 
				tf_nalog_id,
				tf_project_id,
				tf_casting_id, 
				tf_status_id, 
				tf_note,
				tf_important_note, 
				tf_last_active_task
			)
		VALUES
			(
				:tf_candidate_id, 
				:tf_nalog_id,
				:tf_project_id,
				:tf_casting_id, 
				:tf_status_id, 
				:tf_note,
				:tf_important_note, 
				:tf_last_active_task
			)
	");
	$queryInsertTF->execute(array(
		':tf_candidate_id' => $kandidat_id,
		':tf_nalog_id' => $nalog_id,
		':tf_project_id' => $project_id,
		':tf_casting_id' => $pap_group_id, 
		'tf_status_id' => 20, 
		'tf_note' => $note,
		'tf_important_note' => 0,
		'tf_last_active_task' => 0
	));
	/* UNOS TASK FORCE END */

	/* UNOS TASK FORCE STATISTIKA */
	$last_agent_id = disconnectAgentFromCandidate($kandidat_id);
	$active_tsr_id = checkTFstatsPristaoDolaziForCandidate($kandidat_id, $pap_group_id);
	if($active_tsr_id != null){
		updateTFStat($active_tsr_id, 1);
	}
	// if($last_agent_id != null)
	// 	insertTFStat($kandidat_id, $last_agent_id, $pap_group_id, $nalog_id, 1);

	header("Location: projects?page=open&id=$project_id&tab=kandidati");

break;

case "kandidat_nije_dosao_na_intervju":
	$kandidat_id = $_POST['kandidat_id_nije_dosao'];
	$nalog_id = $_POST['nalog_id_nije_dosao'];
	$project_id = $_POST['project_id_nije_dosao'];
	$pap_group_id = $_POST['pap_group_id_nije_dosao'];
	$note = "Zaposlenik ".getEmployeeFullnameR()." označio da kandidat nije došao na razgovor.";

	/* Update glavne tabele idk_kandidati sa TF statusom 22*/
	$update_candidate_table = $db->prepare("UPDATE idk_kandidati SET kandidat_tf_status = 22 WHERE kandidat_id = :candId");
	$update_candidate_table->bindParam(':candId', $kandidat_id);
	$update_candidate_table->execute();

	//Trenutni tf status update last_active na 0 
	$sql = "UPDATE idk_task_force SET tf_last_active_task = 0 WHERE tf_candidate_id = :candidate_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':candidate_id', $kandidat_id);
    $stmt->execute();

	//Postavi kandidata na tf status 22 (nije dosao na razgovor)
    $sql = "INSERT INTO 
                idk_task_force 
                (
                    tf_candidate_id, 
                    tf_nalog_id,
                    tf_project_id,
                    tf_casting_id,
                    tf_status_id, 
                    tf_note,
                    tf_important_note,
                    tf_call_appointment,
                    tf_last_active_task
                ) 
            VALUES 
                (
                    :candidate_id, 
                    :nalog_id, 
                    :project_id,
                    :casting_id,
                    22, 
                    :note,
                    1,
                    NOW(),
                    1
                )";

    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':candidate_id' => $kandidat_id,
        ':nalog_id' => $nalog_id,
        ':project_id' => $project_id,
        ':casting_id' => $pap_group_id,
        ':note' => $note
    ]);

	$last_agent_id = getFullReservedAgentFromCandidate($kandidat_id);
	$active_tsr_id = checkTFstatsPristaoDolaziForCandidate($kandidat_id, $pap_group_id);
	if($active_tsr_id != null){
		updateTFStat($active_tsr_id, 2);
	}
    // if($last_agent_id != null)
    //     insertTFStat($kandidat_id, $last_agent_id, $pap_group_id, $nalog_id, 2);
	
	//PREBACI KANDIDATA IZ PROJEKTA INTERVJU U PROJEKT NIJE DOSAO
	$interview_id = getProjectIDForNalogByName("- Intervju", $nalog_id);
	$nije_dosao_id = getProjectIDForNalogByName("- Nije došao na razgovor", $nalog_id);
	$delete_from_project = $db->prepare("DELETE FROM idk_project_kandidati WHERE pk_kandidatid = :kandidat_id AND pk_projectid = :project_id");
	$delete_from_project->execute(array(
		":kandidat_id" 	=> $kandidat_id,
		":project_id" 	=> $interview_id
	));
	$insert_into_nije_dosao = $db->prepare("INSERT INTO idk_project_kandidati (pk_projectid, pk_kandidatid) VALUES (:project_id, :kandidat_id)");
	$insert_into_nije_dosao->execute(array(
		":project_id" 	=> $nije_dosao_id,
		":kandidat_id" 	=> $kandidat_id
	));
	// POSTAVI STATUS PRIJAVE NA U projekt NR
	$set_status_prijave = $db->prepare("UPDATE idk_kandidati SET kandidat_status_prijave = :status_prijave WHERE kandidat_id = :kandidat_id");
	$set_status_prijave->execute(array(
		":status_prijave" 	=> 2,
		":kandidat_id"		=> $kandidat_id
	));
	// POSTAVI STATUS idk_pp_cand_appts NA ARHIVIRAN
	$archive_pca = $db->prepare("UPDATE idk_pp_cand_appts SET pca_status = :new_status WHERE pca_kandidat_id = :kandidat_id AND pca_status = :old_status");
	$archive_pca->execute(array(
		":new_status" => 0,
		":old_status" => 1,
		":kandidat_id" => $kandidat_id
	));
	addToLogsStatusPrijave($interview_id, $nije_dosao_id, 2, $kandidat_id, 1);

	$query = $db->prepare("
		INSERT INTO idk_notes
			(note_txt, note_datetime, note_group, note_dataid, note_employeeid)
		VALUES
			(:note_txt, :note_datetime, :note_group, :note_dataid, :note_employeeid)");

	$query->execute(array(
				':note_txt' => $note,
				':note_datetime' => date('Y-m-d H:i:s'),
				':note_group' => 2,
				':note_dataid' => $kandidat_id,
				':note_employeeid' => $logged_employee_id));
	
	header("Location: projects?page=open&id=$project_id&tab=kandidati");

break;

case "odvezi_dipl_profil":
	$kandidat_id = $_GET['kandidat_id'];

	$get_dipl_id = $db->prepare("SELECT kandidat_dipl_id FROM idk_kandidati WHERE kandidat_id = $kandidat_id");
	$get_dipl_id->execute();
	$dipl_id_result = $get_dipl_id->fetch();
	$dipl_id = $dipl_id_result['kandidat_dipl_id'];

	$sql2 = "UPDATE idk_kandidati SET kandidat_dipl_id = 0, povezan_na_dipl = 0 WHERE kandidat_id = $kandidat_id";
	$stmt2 = $db->prepare($sql2);
	$stmt2->execute();

	$sql = "UPDATE idk_nostrifikovane_diplome SET id_cand_job = NULL WHERE id_cand_job = $kandidat_id";
	$stmt = $db->prepare($sql);
	$stmt->execute();

	$log_desc = "Zaposlenik odvezao kandidata [$kandidat_id] sa dipl profila [$dipl_id]";
	$log_type = 0;
	addToLogs($log_desc, $log_type, $logged_employee_id);

	header("Location: kandidati?page=open&id=$kandidat_id");
break;

case "devalidate_document":
	header("Content-Type: application/json");

	$document_id = intval($_REQUEST['document_id']);

	updatePreviousDocumentStatusNumberOfDays($document_id);

	$sql = "UPDATE idk_pp_documents SET doc_status = 21 WHERE doc_id = :doc_id";
	$update_document = $db->prepare($sql);
	$update_document->execute(array(':doc_id' => $document_id));


	$doc_status_log_SQL = "INSERT INTO idk_pp_documents_status_logs VALUES (:doc_id, :new_status, :date, :employee_id, :pp_id, :days, :comment)";
	$doc_status_log_query = $db->prepare($doc_status_log_SQL);
	$doc_status_log_query->execute([
		":doc_id"       => $document_id,
		":new_status"   => 21,
		":date"         => date("Y-m-d H:i:s"),
		":employee_id"  => $logged_employee_id,
		":pp_id"        => NULL,
		":days"         => NULL,
		":comment"      => "Dokument devalidiran!"
	]);
	echo json_encode([]);

break;

case "get_grad":
	$grad_id = $_POST['grad_id'] ?? null;

    if ($grad_id !== null) {
        $sql = "SELECT pc_name FROM idk_pp_city WHERE pc_id = :pc_id";
        $query = $db->prepare($sql);
        $query->execute(array(':pc_id' => $grad_id));
        $cityName = $query->fetchColumn();
        
        if ($cityName !== false) {
            echo $cityName; 
        } else {
            echo "Nedefinisano"; 
        }
    } else {
        echo "Nedefinisano"; 
    }
break;

case "get_regija":
	$regija_id = $_POST['regija_id'] ?? null;

    if ($regija_id !== null) {
        $sql = "SELECT pr_name FROM idk_pp_regions WHERE pr_id = :pr_id";
        $query = $db->prepare($sql);
        $query->execute(array(':pr_id' => $regija_id));
        $regijaName = $query->fetchColumn();
        
        if ($regijaName !== false) {
            echo $regijaName;
        } else {
            echo "Nedefinisano";
        }
    } else {
        echo "Nedefinisano";
    }
break;

case "get_regija_options":
    
	$sql = "SELECT pr_id, pr_name FROM idk_pp_regions WHERE pr_status = 1";
	$query = $db->prepare($sql);
	$query->execute();
	$rows = $query->fetchAll(PDO::FETCH_ASSOC);

	if ($rows !== false) {
		echo json_encode($rows);
	} else {
		echo "Nedefinisano";
	}
break;

case "get_grad_options":
	$regija_id = $_POST["regija_id"];
    
	$sql = "SELECT pc_id, pc_name FROM idk_pp_city WHERE pc_status = 1 AND pc_region = :pc_region";
	$query = $db->prepare($sql);
	$query->execute(array(':pc_region' => $regija_id));
	$rows = $query->fetchAll(PDO::FETCH_ASSOC);

	if ($rows !== false) {
		echo json_encode($rows);
	} else {
		echo "Nedefinisano";
	}
break;

case "update_kandidat_regija_grad":
	$kandidat_id = $_POST['kandidat_id'] ?? null;
	$regija_id = $_POST['regija_id'] ?? null;
	$grad_id = $_POST['grad_id'] ?? null;

	if($kandidat_id == null || $regija_id == null)
	{
		http_response_code(500);
		die("Nisu proslijedjeni svi parametri");
	}

	if($regija_id == 0){
		$grad_id = null;
	}

	$sql = "UPDATE idk_kandidati SET kandidat_zeljena_regija = :kandidat_zeljena_regija, kandidat_zeljeni_grad = :kandidat_zeljeni_grad WHERE kandidat_id = :kandidat_id";
	$query = $db->prepare($sql);
	$res = $query->execute(array(
		':kandidat_zeljena_regija' => $regija_id,
		':kandidat_zeljeni_grad' => $grad_id,
		':kandidat_id' => $kandidat_id
	));
break;

case "get_employees_for_module_permission":
	$module_permission_id = $_POST["module_permission_id"];
	$employees_ids = $_POST["employees_ids"];

	$selected_employees = (($employees_ids != 'Undefined') ? explode(',', $employees_ids) : array());

	$result = '';
	$sql = '
		SELECT 
			emp.employee_id 											AS employee_id,
			CONCAT(emp.employee_firstname, " ", emp.employee_lastname) 	AS employee_full_name
		FROM 
			idk_employees emp 
		WHERE 
			emp.employee_status != 0
		ORDER BY
			employee_full_name
		ASC
	';
	$query = $db->prepare($sql);
	$query->execute();
	if ($query->rowCount() != 0) {
		$rows = $query->fetchAll(PDO::FETCH_ASSOC);
		$options = array();
		foreach($rows AS $row) {
			if (in_array($row["employee_id"], $selected_employees))
				$txt_selected = "selected";
			else
				$txt_selected = "";
			
			array_push($options, '<option value="'.$row["employee_id"].'" '.$txt_selected.' >'.$row["employee_full_name"].'</option>'); 
			
		}
		$result = implode("", $options); 
		unset($rows);
		unset($options); 
	} else {
		$result = 'No results found for query!';
	}

	echo $result;
	
break;

case "chang_module_permission_users":

	$module_permission_id = $_POST['module_permission_id'];
	$select_new_employees = $_POST['select_new_employees'];
	$old_employees = $_POST['employees_ids'];

	$select_new_employees_imp = implode(',', $select_new_employees);

	$update_mp = $db->prepare('
		UPDATE idk_module_permissions
		SET mp_employees = :mp_employeees
		WHERE mp_id = :mp_id
	');
	$update_mp->execute(array(
		':mp_id' => $module_permission_id,
		':mp_employeees' => $select_new_employees_imp
	));

	$log_desc = "Zaposlenik promijenio permisiju $module_permission_id. Stari zaposlenici: $old_employees, novi zaposlenici: $select_new_employees_imp.";
	$log_type = 0;
	addToLogs($log_desc, $log_type, $logged_employee_id);

	header("Location: module_permissions?page=list_all");

break;

case "change_dvag_status":
	$employee_id = $_POST['employee_id'];
	$dvag_status = $_POST['dvag_status'];

	$query_update = $db->prepare("
		UPDATE idk_employees
		SET	employee_dvag = :employee_dvag
		WHERE employee_id = :employee_id");

	$query_update->execute(array(
		':employee_id' => $employee_id,
		':employee_dvag' => $dvag_status
		));
	
	//Add to LOGS
	if($dvag_status == 0){
		$log_desc = "Deaktiviran Dvag agent: " .getEmployeeFullnameById($employee_id). "";
	}else{
		$log_desc = "Aktiviran Dvag agent: " .getEmployeeFullnameById($employee_id). "";
	}
	$log_type = "0";
	addToLogs($log_desc, $log_type); 

break;

case "new_partner_account":

	$makler_firstname = $_POST['makler_firstname'];
	$makler_lastname = $_POST['makler_lastname'];
	$makler_email = $_POST['makler_email'];
	$makler_company = $_POST['makler_company'];
	$makler_language = $_POST['makler_language'];
	$makler_id = $_POST['makler_id'];
	// $tutorial_file_name = $_POST['tutorial_file_name'];
	$makler_broj_direkcije = $_POST['makler_broj_direkcije'];

	$makler_fullname = $makler_firstname.' '.$makler_lastname;

	$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    $password = '';
	$token = '';

    for ($i = 0; $i < 8; $i++) {
        $password .= $characters[rand(0, strlen($characters) - 1)];
        $token .= $characters[rand(0, strlen($characters) - 1)];
    }

	$hashed_password = md5($password);
	$hashed_token = md5($token);

	if(isLoggedEmployeeRepresentative()){
		$representative_id = $logged_employee_id;
	}
	else{
		$representative_id = getDefaultReperesentativeEmployeeId();
	}

	$insert = $db -> prepare("
		INSERT INTO idk_jobstep_partners (jp_ime, jp_prezime, jp_email, jp_lang, jp_mailconfirmation_token, jp_confirmedaccount, jp_position, jp_source, jp_user_type, jp_partner_company, jp_makler_id, jp_password, jp_register_date, jp_imeprezime, jp_direction_number, jp_representative_employee_id)
		VALUES ('$makler_firstname', '$makler_lastname', '$makler_email', '$makler_language', '$hashed_token', 1, 1, 1, 1, '$makler_company', '$makler_id', '$hashed_password', now(), '$makler_fullname', '$makler_broj_direkcije', '$representative_id')
	");
	$insert -> execute();

	$last_insert = $db -> prepare("
		SELECT jp_id as last_insert_id, jp_mailconfirmation_token
		FROM idk_jobstep_partners
		WHERE jp_id = (
			SELECT max(jp_id) FROM idk_jobstep_partners
		)
	");
	$last_insert -> execute();
	$result = $last_insert -> fetch();
	$id = $result['last_insert_id'];
	$dummy_token = $result['jp_mailconfirmation_token'];

	if($envConfig->APP_ENV != 'production'){
		addPartnerAppDummyData($dummy_token);
	}
	else{
		createPersonalNotificationPreferences($id);
		
		$url = 'https://staging.crm.job-step.com/do.php?form=copy_new_partner_account_to_stage';

		$data = array(
			'makler_firstname' => $makler_firstname,
			'makler_lastname' => $makler_lastname,
			'makler_email' => $makler_email,
			'makler_language' => $makler_language,
			'hashed_token' => $hashed_token,
			'makler_company' => $makler_company,
			'hashed_password' => $hashed_password,
			'makler_fullname' => $makler_fullname,
			'makler_broj_direkcije' => $makler_broj_direkcije,
			'logged_employee_id' => $representative_id,
			'makler_id' => $makler_id,
		);

		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

		curl_exec($ch);
	}

	$mail_status = sendMailMaklerPassword($makler_email, $password, $makler_fullname, $tutorial_file_name);

	$log_desc = "Dodan novi partner user: $makler_email, mail status: $mail_status";
	$log_type = "0";
	addToLogs($log_desc, $log_type);

	header("Location:partners/new_account");
break;

case "copy_new_partner_account_to_stage":

	if($envConfig->APP_ENV == "staging") {
		$makler_firstname = $_POST['makler_firstname'];
		$makler_lastname = $_POST['makler_lastname'];
		$makler_email = $_POST['makler_email'];
		$makler_company = $_POST['makler_company'];
		$makler_language = $_POST['makler_language'];
		$makler_id = $_POST['makler_id'];
		$tutorial_file_name = $_POST['tutorial_file_name'];
		$makler_broj_direkcije = $_POST['makler_broj_direkcije'];
		$hashed_token = $_POST['hashed_token'];
		$hashed_password = $_POST['hashed_password'];
		$makler_fullname = $_POST['makler_fullname'];
		$logged_employee_id = $_POST['logged_employee_id'];
		$makler_id = $_POST['makler_id'];
	
		$insert = $db -> prepare("
			INSERT INTO idk_jobstep_partners (jp_ime, jp_prezime, jp_email, jp_lang, jp_mailconfirmation_token, jp_confirmedaccount, jp_position, jp_source, jp_user_type, jp_partner_company, jp_makler_id, jp_password, jp_register_date, jp_imeprezime, jp_direction_number, jp_representative_employee_id)
			VALUES ('$makler_firstname', '$makler_lastname', '$makler_email', '$makler_language', '$hashed_token', 1, 1, 1, 1, '$makler_company', '$makler_id', '$hashed_password', now(), '$makler_fullname', '$makler_broj_direkcije', '$logged_employee_id')
		");
		$insert -> execute();
	
		$last_insert = $db -> prepare("
			SELECT jp_id as last_insert_id, jp_mailconfirmation_token
			FROM idk_jobstep_partners
			WHERE jp_id = (
				SELECT max(jp_id) FROM idk_jobstep_partners
			)
		");
		$last_insert -> execute();
		$result = $last_insert -> fetch();
		$id = $result['last_insert_id'];
		$dummy_token = $result['jp_mailconfirmation_token'];
	
		addPartnerAppDummyData($dummy_token);


		$log_desc = "Dodan novi partner user: $makler_email, mail status: $mail_status";
		$log_type = "0";
		addToLogs($log_desc, $log_type);
	
		header("Location:partners/new_account");
	}
	
break;

case "upload_new_partner_tutorial":

	$tutorial_name = $_POST['tutorial_name'];
	$tutorial_file = $_FILES['tutorial_file'];

	$file_name = $tutorial_file['name'];
	$file_tmp = $tutorial_file['tmp_name'];

	$file_ext = explode('.', $file_name);
	$file_ext = strtolower(end($file_ext));

	$file_name_new = uniqid() . '.' . $file_ext;
	$file_destination = 'files/partner-app/tutorials/' . $file_name_new;

	move_uploaded_file($file_tmp, $file_destination);

	$sql = $db -> prepare("INSERT INTO idk_partner_tutorials (name, file_name, is_default, doe, employee_id) VALUES (:tutorial_name, :file_name_new, 0, now(), :employee_id)");
	$sql -> execute(array(
		':tutorial_name' => $tutorial_name, 
		':file_name_new' => $file_name_new,
		':employee_id' => $logged_employee_id
	));

	header("Location:partners/new_account#existing_tutorials");
	
break;

case "edit_makler":
	$makler_firstname = $_POST['edit_makler_firstname'];
	$makler_lastname = $_POST['edit_makler_lastname'];
	$makler_fullname = $makler_firstname.' '.$makler_lastname;
	$makler_language = $_POST['edit_makler_language'];
	$makler_id = $_POST['edit_makler_id'];
	$makler_broj_direkcije = $_POST['edit_makler_broj_direkcije'];
	$makler_representative_employee = $_POST['edit_makler_representative_employee'];
	$actual_id = $_POST['actual_id'];

	$edit_makler = $db -> prepare("
		UPDATE idk_jobstep_partners
		SET 
			jp_ime = :makler_firstname,
			jp_prezime = :makler_lastname,
			jp_imeprezime = :makler_fullname,
			jp_lang = :makler_language,
			jp_makler_id = :makler_id,
			jp_direction_number = :makler_broj_direkcije
		WHERE 
			jp_id = :actual_id
	");

	$edit_makler -> execute(array(
		':makler_firstname' => $makler_firstname,
		':makler_lastname' => $makler_lastname,
		':makler_fullname' => $makler_fullname,
		':makler_language' => $makler_language,
		':makler_id' => $makler_id,
		':makler_broj_direkcije' => $makler_broj_direkcije,
		':actual_id' => $actual_id
	));

	header("Location:partners/new_account");
break;

case "change_message_provider":
	$psm_id = $_POST['psm_id'];
	$psm_group_type = $_POST['psm_group_type'];
	$psm_active_provider = $_POST['psm_active_provider'];

	editActiveProviderForSendingMessages($psm_id, $psm_group_type, $psm_active_provider);

	header("Location: message_providers?page=list_all");

break;

case "change_message_provider_all":
	$psm_active_provider_all = $_POST['psm_active_provider_all'];

	editActiveProviderForAllSendingMessages($psm_active_provider_all);

	header("Location: message_providers?page=list_all");

break;

case "delete_question":

    $id = json_decode(file_get_contents("php://input"), true);

	if($id){
		return deleteChatQuestion($id);
	}else{
		http_response_code(500);
		die("Id not found");
	}	

break;	

case "add_question":

	$data = json_decode(file_get_contents("php://input"), true);

	if($data){
		return addChatQuestion($data["type"], json_encode($data["question_json"],JSON_UNESCAPED_UNICODE ), json_encode($data["answer_json"],JSON_UNESCAPED_UNICODE ),$data['parent_id']);
	}else{
		die('Missing data');
	}	

break;

case "edit_question":

	$data = json_decode(file_get_contents("php://input"), true);

	if($data){
		return updateChatQuestion($data["id"], json_encode($data["question_json"],JSON_UNESCAPED_UNICODE), json_encode($data["answer_json"],JSON_UNESCAPED_UNICODE), $data["visibility"], $data["type"]);
	}else{
		http_response_code(500);
		die("Missing data");
	}	

break;

case "edit_question_visibility":
	$data = json_decode(file_get_contents("php://input"), true);

	if($data){
		return updateChatQuestionVisibility($data["id"], $data["visibility"], $data["type"]);
	}else{
		http_response_code(500);
		die("Missing data");
	}	

break;

case "add_nalog_profile":
	$nalog_id 							= $_POST['nalog_id'] ?? null;
	$naziv_profila 						= $_POST['naziv_profila'] ?? null;
	$prioritet_profila 					= $_POST['prioritet_profila'] ?? null;
	$kriterij_smjerovi_naloga 			= $_POST['kriterij_smjerovi_naloga'] ?? null;
	$kriterij_vozacka_dozvola 			= $_POST['kriterij_vozacka_dozvola'] ?? null;

	if($kriterij_vozacka_dozvola == 1){
		$kriterij_vozacka_kat 			= $_POST['kriterij_vozacka_kat'] ?? [];
		$kriterij_vozacka_kat_imploded	= implode(",", $kriterij_vozacka_kat);
	}else{
		$kriterij_vozacka_kat_imploded  = null;
	}

	$kriterij_iskustvo_struka 			= $_POST['kriterij_iskustvo_struka'] ?? null;

	if($kriterij_iskustvo_struka == 1){
		$kriterij_iskustvo_trajanje 	= $_POST['kriterij_iskustvo_trajanje'] ?? null;
	}else{
		$kriterij_iskustvo_trajanje     = null;
	}

	$kriterij_starost_kandidata			= $_POST['kriterij_starost_kandidata'] ?? null;

	if($kriterij_starost_kandidata == 1){
		$kriterij_starost_od 			= $_POST['kriterij_starost_od'] ?? null;
		$kriterij_starost_do 			= $_POST['kriterij_starost_do'] ?? null;
	}else{
		$kriterij_starost_od     		= null;
		$kriterij_starost_do     		= null;
	}
	
	$kriterij_njemacki_jezik 			= $_POST['kriterij_njemacki_jezik'] ?? null;

	if($kriterij_njemacki_jezik == 1){
		$kriterij_nivo_njemackog_jezika = $_POST['kriterij_nivo_njemackog_jezika'] ?? null;
	}else{
		$kriterij_nivo_njemackog_jezika = null;
	}

	$kriterij_jezici 					= $_POST['kriterij_jezici'] ?? null;

	if($kriterij_jezici == 1){
		$kriterij_engleski_jezik		= $_POST['kriterij_engleski_jezik'] ?? null;

		if($kriterij_engleski_jezik == 1){
			$kriterij_nivo_engleskog_jezika = $_POST['kriterij_nivo_engleskog_jezika'] ?? null;
		}else{
			$kriterij_nivo_engleskog_jezika = null;
		}

		$kriterij_italijanski_jezik			= $_POST['kriterij_italijanski_jezik'] ?? null;

		if($kriterij_italijanski_jezik == 1){
			$kriterij_nivo_italijanskog_jezika = $_POST['kriterij_nivo_italijanskog_jezika'] ?? null;
		}else{
			$kriterij_nivo_italijanskog_jezika = null;
		}

		$kriterij_francuski_jezik			= $_POST['kriterij_francuski_jezik'] ?? null;

		if($kriterij_francuski_jezik == 1){
			$kriterij_nivo_francuskog_jezika = $_POST['kriterij_nivo_francuskog_jezika'] ?? null;
		}else{
			$kriterij_nivo_francuskog_jezika = null;
		}
	}else{
		$kriterij_engleski_jezik 			= null;
		$kriterij_nivo_engleskog_jezika 	= null;
		$kriterij_italijanski_jezik 		= null;
		$kriterij_nivo_italijanskog_jezika 	= null;
		$kriterij_francuski_jezik 			= null;
		$kriterij_nivo_francuskog_jezika	= null;
	}

	$query_profil = $db->prepare("
			INSERT INTO idk_nalog_profil
				(naziv, prioritet, zaposlenik, nalog_id)
			VALUES
				(:naziv, :prioritet, :zaposlenik, :nalog_id)");

	$query_profil->execute(array(
			':naziv' => $naziv_profila,
			':prioritet' => $prioritet_profila,
			':zaposlenik' => $logged_employee_id,
			':nalog_id' => $nalog_id
	));

	$profil_id = $db->lastInsertId();

	$query_kriteriji = $db->prepare("
			INSERT INTO idk_profil_kriterij
				(vozacka_dozvola, kategorija_vozacke_dozvole, smjerovi_naloga, radno_iskustvo_struka, 
				radno_iskustvo_struka_trajanje, starost, starost_minimum, starost_maksimum, 
				njemacki_jezik, nivo_njemackog_jezika, znanje_drugog_jezika, engleski_jezik, nivo_engleskog_jezika, 
				francuski_jezik, nivo_francuskog_jezika, italijanski_jezik, nivo_italijanskog_jezika, profil_id)
			VALUES
				(:vozacka_dozvola, :kategorija_vozacke_dozvole, :smjerovi_naloga, :radno_iskustvo_struka, 
				:radno_iskustvo_struka_trajanje, :starost, :starost_minimum, :starost_maksimum, 
				:njemacki_jezik, :nivo_njemackog_jezika, :znanje_drugog_jezika, :engleski_jezik, :nivo_engleskog_jezika, 
				:francuski_jezik, :nivo_francuskog_jezika, :italijanski_jezik, :nivo_italijanskog_jezika, :profil_id)");

	$query_kriteriji->execute(array(
			':vozacka_dozvola' => $kriterij_vozacka_dozvola,
			':kategorija_vozacke_dozvole' => $kriterij_vozacka_kat_imploded,
			':smjerovi_naloga' => $kriterij_smjerovi_naloga,
			':radno_iskustvo_struka' => $kriterij_iskustvo_struka,
			':radno_iskustvo_struka_trajanje' => $kriterij_iskustvo_trajanje,
			':starost' => $kriterij_starost_kandidata,
			':starost_minimum' => $kriterij_starost_od,
			':starost_maksimum' => $kriterij_starost_do,
			':njemacki_jezik' => $kriterij_njemacki_jezik,
			':nivo_njemackog_jezika' => $kriterij_nivo_njemackog_jezika, 
			':znanje_drugog_jezika' => $kriterij_jezici,
			':engleski_jezik' => $kriterij_engleski_jezik,
			':nivo_engleskog_jezika' => $kriterij_nivo_engleskog_jezika,
			':francuski_jezik' => $kriterij_francuski_jezik,
			':nivo_francuskog_jezika' => $kriterij_nivo_francuskog_jezika, 
			':italijanski_jezik' => $kriterij_italijanski_jezik, 
			':nivo_italijanskog_jezika' => $kriterij_nivo_italijanskog_jezika,
			':profil_id' => $profil_id
	));

	//Add to LOGS
	$log_desc = "Dodan profil $profil_id za nalog $nalog_id.";
	$log_type = "0";
	addToLogs($log_desc, $log_type); 

	header("Location: nalozi?page=open&id=" . $nalog_id . "&tab=kriteriji");	
break;

case "edit_nalog_profile":
	$nalog_id 							= $_POST['nalog_id'] ?? null;
	$profil_id 							= $_POST['profil_id'] ?? null;
	$naziv_profila 						= $_POST['naziv_profila'] ?? null;
	$prioritet_profila 					= $_POST['prioritet_profila'] ?? null;
	$kriterij_smjerovi_naloga 			= $_POST['kriterij_smjerovi_naloga'] ?? null;
	$kriterij_vozacka_dozvola 			= $_POST['kriterij_vozacka_dozvola'] ?? null;

	if($kriterij_vozacka_dozvola == 1){
		$kriterij_vozacka_kat 			= $_POST['kriterij_vozacka_kat'] ?? [];
		$kriterij_vozacka_kat_imploded	= implode(",", $kriterij_vozacka_kat);
	}else{
		$kriterij_vozacka_kat_imploded	= null;
	}

	$kriterij_iskustvo_struka 			= $_POST['kriterij_iskustvo_struka'] ?? null;

	if($kriterij_iskustvo_struka == 1){
		$kriterij_iskustvo_trajanje 	= $_POST['kriterij_iskustvo_trajanje'] ?? null;
	}else{
		$kriterij_iskustvo_trajanje     = null;
	}

	$kriterij_starost_kandidata			= $_POST['kriterij_starost_kandidata'] ?? null;

	if($kriterij_starost_kandidata == 1){
		$kriterij_starost_od 			= $_POST['kriterij_starost_od'] ?? null;
		$kriterij_starost_do 			= $_POST['kriterij_starost_do'] ?? null;
	}else{
		$kriterij_starost_od     		= null;
		$kriterij_starost_do     		= null;
	}
	
	$kriterij_njemacki_jezik 			= $_POST['kriterij_njemacki_jezik'] ?? null;

	if($kriterij_njemacki_jezik == 1){
		$kriterij_nivo_njemackog_jezika = $_POST['kriterij_nivo_njemackog_jezika'] ?? null;
	}else{
		$kriterij_nivo_njemackog_jezika = null;
	}

	$kriterij_jezici 					= $_POST['kriterij_jezici'] ?? null;

	if($kriterij_jezici == 1){
		$kriterij_engleski_jezik		= $_POST['kriterij_engleski_jezik'] ?? null;

		if($kriterij_engleski_jezik == 1){
			$kriterij_nivo_engleskog_jezika = $_POST['kriterij_nivo_engleskog_jezika'] ?? null;
		}else{
			$kriterij_nivo_engleskog_jezika = null;
		}

		$kriterij_italijanski_jezik			= $_POST['kriterij_italijanski_jezik'] ?? null;

		if($kriterij_italijanski_jezik == 1){
			$kriterij_nivo_italijanskog_jezika = $_POST['kriterij_nivo_italijanskog_jezika'] ?? null;
		}else{
			$kriterij_nivo_italijanskog_jezika = null;
		}

		$kriterij_francuski_jezik			= $_POST['kriterij_francuski_jezik'] ?? null;

		if($kriterij_francuski_jezik == 1){
			$kriterij_nivo_francuskog_jezika = $_POST['kriterij_nivo_francuskog_jezika'] ?? null;
		}else{
			$kriterij_nivo_francuskog_jezika = null;
		}
	}else{
		$kriterij_engleski_jezik 			= null;
		$kriterij_nivo_engleskog_jezika 	= null;
		$kriterij_italijanski_jezik 		= null;
		$kriterij_nivo_italijanskog_jezika 	= null;
		$kriterij_francuski_jezik 			= null;
		$kriterij_nivo_francuskog_jezika	= null;
	}

	$query_profil = $db->prepare("
			UPDATE idk_nalog_profil
			SET naziv = :naziv, prioritet = :prioritet
			WHERE id = :profil_id");

	$query_profil->execute(array(
			':naziv' => $naziv_profila,
			':prioritet' => $prioritet_profila,
			':profil_id' => $profil_id
	));

	$query_kriteriji = $db->prepare("
			UPDATE idk_profil_kriterij
			SET vozacka_dozvola = :vozacka_dozvola, kategorija_vozacke_dozvole = :kategorija_vozacke_dozvole, smjerovi_naloga = :smjerovi_naloga, radno_iskustvo_struka = :radno_iskustvo_struka, 
				radno_iskustvo_struka_trajanje = :radno_iskustvo_struka_trajanje, starost = :starost, starost_minimum = :starost_minimum, starost_maksimum = :starost_maksimum, 
				njemacki_jezik = :njemacki_jezik, nivo_njemackog_jezika = :nivo_njemackog_jezika, znanje_drugog_jezika = :znanje_drugog_jezika, engleski_jezik = :engleski_jezik, nivo_engleskog_jezika = :nivo_engleskog_jezika, 
				francuski_jezik = :francuski_jezik, nivo_francuskog_jezika = :nivo_francuskog_jezika, italijanski_jezik = :italijanski_jezik, nivo_italijanskog_jezika = :nivo_italijanskog_jezika
			WHERE profil_id = :profil_id");

	$query_kriteriji->execute(array(
			':vozacka_dozvola' => $kriterij_vozacka_dozvola,
			':kategorija_vozacke_dozvole' => $kriterij_vozacka_kat_imploded,
			':smjerovi_naloga' => $kriterij_smjerovi_naloga,
			':radno_iskustvo_struka' => $kriterij_iskustvo_struka,
			':radno_iskustvo_struka_trajanje' => $kriterij_iskustvo_trajanje,
			':starost' => $kriterij_starost_kandidata,
			':starost_minimum' => $kriterij_starost_od,
			':starost_maksimum' => $kriterij_starost_do,
			':njemacki_jezik' => $kriterij_njemacki_jezik,
			':nivo_njemackog_jezika' => $kriterij_nivo_njemackog_jezika, 
			':znanje_drugog_jezika' => $kriterij_jezici,
			':engleski_jezik' => $kriterij_engleski_jezik,
			':nivo_engleskog_jezika' => $kriterij_nivo_engleskog_jezika,
			':francuski_jezik' => $kriterij_francuski_jezik,
			':nivo_francuskog_jezika' => $kriterij_nivo_francuskog_jezika, 
			':italijanski_jezik' => $kriterij_italijanski_jezik, 
			':nivo_italijanskog_jezika' => $kriterij_nivo_italijanskog_jezika,
			':profil_id' => $profil_id
	));

	//Add to LOGS
	$log_desc = "Uredio profil $profil_id za nalog $nalog_id.";
	$log_type = "0";
	addToLogs($log_desc, $log_type); 

	header("Location: nalozi?page=open&id=" . $nalog_id . "&tab=kriteriji");	
break;

case "delete_nalog_profile":
	$profil_id = $_POST["profil_id"];
	$nalog_id = $_POST["nalog_id"];

	$query_update = $db->prepare("
		UPDATE idk_nalog_profil
		SET	status = :status
		WHERE id = :profil_id");

	$query_update->execute(array(
		':profil_id' => $profil_id,
		':status' => 0
		));
	
	//Add to LOGS
	$log_desc = "Arhiviran profil $profil_id za nalog $nalog_id.";
	$log_type = "0";
	addToLogs($log_desc, $log_type); 
	
	header("Location: nalozi?page=open&id=" . $nalog_id . "&tab=kriteriji");	

break;

case "mark_new_representative":
	$employee_id = $_POST['employee_id'];

	$mark_employee = $db -> prepare("
		UPDATE idk_employees
		SET employee_makler_representative_status = 1
		WHERE employee_id = :employee_id
	");

	$mark_employee -> execute(array(':employee_id' => $employee_id));

	$log_desc = "Marked user [".$employee_id."] eligible for representing a makler.";
	$log_type = "0";
	addToLogs($log_desc, $log_type);
	
	header("Location:partners/new_account");
break;
}
}


?>
