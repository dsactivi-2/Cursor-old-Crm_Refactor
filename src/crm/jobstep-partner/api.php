<?php
//Error log enabled
error_reporting(E_ALL); // mogući NOTICE PHP-a uvode grešku u json
ini_set('display_errors', 0);

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

//Connect to db
ob_start();
include("../includes/connect.php");
require_once '../gt/gtranslate.php';
//PHPMailer
require '../mail/Exception.php';
require '../mail/PHPMailer.php';
require '../mail/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$action=""; 

function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
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
function getPartnerLangFromToken($token){
	
	Global $db;
	// POKUPI INFORMACIJU O IDU-U PARTNERA NA OSNOVU TOKENA
	$query_partner = $db->prepare("
				SELECT jp_lang
				FROM idk_jobstep_partners
				WHERE jp_mailconfirmation_token = :jp_mailconfirmation_token");
				
	$query_partner->execute(array(
					':jp_mailconfirmation_token' => $token));
	
	$row = $query_partner->fetch();
					
	return $row['jp_lang'];
}

function updateLangOfPartner($jp_id , $jp_lang){
	
	Global $db;
	//Save
	$query = $db->prepare("
		UPDATE idk_jobstep_partners
		SET	jp_lang = :jp_lang
		WHERE jp_id = :jp_id");

	$query->execute(array(
		':jp_lang' => $jp_lang,
		':jp_id' => $jp_id));
}



function getPartnerPositionPercentage($token){
	
	Global $db;
	// POKUPI INFORMACIJU O IDU-U PARTNERA NA OSNOVU TOKENA
	$query_partner = $db->prepare("
				SELECT jp_position
				FROM idk_jobstep_partners
				WHERE jp_mailconfirmation_token = :jp_mailconfirmation_token");
				
	$query_partner->execute(array(
					':jp_mailconfirmation_token' => $token));
	
	$row = $query_partner->fetch();
					
	if($row['jp_position'] == 1){
		return $partner_procentage = 0.8;
	}elseif($row['jp_position'] == 2){
		return $partner_procentage = 0.9;
	}elseif($row['jp_position'] == 3){
		return $partner_procentage = 1;
	}elseif($row['jp_position'] == 4){
		return $partner_procentage = 1.1;
	}elseif($row['jp_position'] == 5){
		return $partner_procentage = 1.25;
	}else{
		return $partner_procentage = 0.8;
	} 
}


function getPartnerPositionForNostDiploma($partner_status){
	
	if($partner_status == 1){
		$dipl_price = 20;
	}elseif($partner_status == 2){
		$dipl_price = 22.5;
	}elseif($partner_status == 3){
		$dipl_price = 25;
	}elseif($partner_status == 4){
		$dipl_price = 25;
	}elseif($partner_status == 5){
		$dipl_price = 25;
	}
	
	return $dipl_price;
}

function getPartnerPhoneNumber($jp_id){
	
	Global $db;
	// POKUPI INFORMACIJU O IDU-U PARTNERA NA OSNOVU TOKENA
	$query_partner = $db->prepare("
				SELECT jp_brtelefona
				FROM idk_jobstep_partners
				WHERE jp_id = :jp_id");
				
	$query_partner->execute(array(
					':jp_id' => $jp_id));
	
	$row = $query_partner->fetch();
					
	return $row['jp_brtelefona'];
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


if(isset($_REQUEST["action"])) {
	$action = $_REQUEST["action"];
	
	switch ($action)
	{
		
	
	case "register":
		$json = json_decode(file_get_contents('php://input'), true);

		$registerdata = array();
		$registerdata['jp_imeprezime'] = $json['jp_imeprezime']; 
		$registerdata['jp_email'] = $json['jp_email']; 
		$registerdata['jp_password'] = $json['jp_password'];
		$registerdata['jp_fcmtoken'] = $json['jp_fcmtoken'];
		$registerdata['jp_preporuka_id'] = $json['jp_preporuka_id'];
		
		$register_date = date('Y-m-d H:i:s');
		$token = bin2hex(openssl_random_pseudo_bytes(12));
		

		/******************************
		 *      Check if user exist
		 ******************************/
		$check_query = $db->prepare("
				SELECT jp_imeprezime
				FROM idk_jobstep_partners
				WHERE jp_email = :jp_email");
				
		$check_query->execute(array(
						':jp_email' => $registerdata['jp_email']));

		$number_of_rows = $check_query->rowCount();

		if($number_of_rows == 0){
			/******************************
			 *      Insert into database
			 ******************************/
			$register_query = $db->prepare("
							INSERT INTO idk_jobstep_partners
								(jp_imeprezime, jp_email, jp_password, jp_register_date, jp_fcmtoken, jp_mailconfirmation_token, jp_preporuka_id)
								VALUES
								(:jp_imeprezime, :jp_email, :jp_password, :jp_register_date, :jp_fcmtoken, :jp_mailconfirmation_token, :jp_preporuka_id)");
			$register_query->execute(array(
							':jp_imeprezime' => $registerdata['jp_imeprezime'],
							':jp_email' => $registerdata['jp_email'],
							':jp_password' => md5($registerdata['jp_password']),
							':jp_register_date' => $register_date,
							':jp_fcmtoken' => $registerdata['jp_fcmtoken'],
							':jp_mailconfirmation_token' => $token,
							':jp_preporuka_id' => $registerdata['jp_preporuka_id'] 
							));
			
			
			$body = '
				<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head> <meta name="viewport" content="width=device-width" /> <meta http-equiv="Content-Type" content="text/html; " /> <title>Jobstep Partner - Action Required: Please Confirm Your Email.</title> <style> * { margin: 0; padding: 0; font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; } img { max-width: 90px;height: auto; } body { -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; } a { color: #62969D; } .last { margin-bottom: 0; } .first { margin-top: 0; } /* ------------------------------------- BODY ------------------------------------- */ table.body-wrap { width: 100%; padding: 
				px; } table.body-wrap .container { border: 1px solid #f0f0f0; } /* ------------------------------------- FOOTER ------------------------------------- */ table.footer-wrap { width: 100%; clear: both !important; } .footer-wrap .container p { font-size: 12px; color: #666; } table.footer-wrap a { color: #999; } /* ------------------------------------- TYPOGRAPHY ------------------------------------- */ h1, h2, h3 { font-family: "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif; line-height: 1.1; margin-bottom: 15px; color: #000; margin: 40px 0 10px; line-height: 1.2; font-weight: 200; } h1 { font-size: 28px;margin: 50px 0px 50px 0px; } h2 { font-size: 22px; } h3 { font-size: 18px; } p, ul { margin-bottom: 10px; font-weight: normal; font-size: 14px; } ul li { margin-left: 5px; list-style-position: inside; } /* Set a max-width, and make it display as block so it will automatically stretch to that width, but will also shrink down on a phone or something */ .container { display: block !important; max-width: 600px !important; margin: 0 auto !important; /* makes it centered */ clear: both !important; } /* This should also be a block element, so that it will fill 100% of the .container */ .content { padding: 20px; max-width: 600px; margin: 0 auto; display: block; } /* Lets make sure tables in the content area are 100% wide */ .content table { width: 100%; } </style></head><body bgcolor="#f6f6f6"> <!-- body --> <table class="body-wrap"> <tr> <td></td> <td class="container" bgcolor="#FFFFFF"> <div class="content"> <table> <tr> <td> <img src="https://job-step.net/wp-content/uploads/2019/02/Logo-Jobstep-International-PNG.png" style="width:100px;height:auto;float:right;" alt="Jobstep International d.o.o." /> </td> </tr> <tr> <td> <h1 style="color: #62969D;"> Verify your e-mail to finish signing up for Jobstep Partner </h1> <p> Hi '. $registerdata['jp_imeprezime'] .', <br /> Thank you for choosing Jobstep Partner APP. </p> <p> Please confirm that '. $registerdata['jp_email'] .' is your e-mail address by clicking on the button or use this link <a style="color: #62969D;" target="_blank" href="https://crm.job-step.com/jobstep-partner/api?action=confirm_mail&token='. $token .'">https://crm.job-step.com/jobstep-partner/api?action=confirm_mail&token='. $token .'</a> within 48 hours. </p><a href="https://crm.job-step.com/jobstep-partner/api?action=confirm_mail&token='. $token .'" target="_blank"><button style="width:100%;height:50px; text-align: center; color: #fff; background: #62969D; border-radius:5px;">VERIFY</button></a> <br /> <br /> <p style="text-align:center;"><a href="https://job-step.net">Jobstep International d.o.o.</a></p> </td> </tr> </table> </div> </td> <td></td> </tr> </table> <!-- /body --> <!-- footer --> <table class="footer-wrap"> <tr> <td></td> <td class="container"> <div class="content"> <table> <tr> <td align="center"> <p> You are not subscribed to anything! <a href="https://job-step.net/?page_id=62">Contact Us</a>. </p> </td> </tr> </table> </div> </td> <td></td> </tr> </table> <!-- /footer --></body></html>
			';

			$mail = new PHPMailer;
			$mail->isSMTP();											// Set mailer to use SMTP
			$mail->Host = 'smtp.gmail.com';  			// Specify main and backup SMTP servers
			$mail->SMTPAuth = true;						// Enable SMTP authentication
			$mail->Username = 'support@job-step.com';	// SMTP username
			$mail->Password = 'eooc nnxo aylp lqkh';		// SMTP password
			$mail->SMTPSecure = 'ssl';					// Enable TLS encryption, `ssl` also accepted
			$mail->Port = 465;							// TCP port to connect to
			$mail->CharSet = 'UTF-8';
			$mail->setFrom('support@job-step.com', 'JobStep Partner');
			$mail->addAddress($registerdata['jp_email'], $registerdata['jp_email']);		// Add a recipient


			$mail->Subject = "JobStep Partner - Action Required: Please Confirm Your Email.";
			$mail->Body    = $body;
			$mail->AltBody = "Please Confirm Your Email.";

			if(!$mail->send()) {
				echo 'Message could not be sent.';
				echo 'Mailer Error: ' . $mail->ErrorInfo;
			}
			echo 'true';
		}else{
			echo 'false';
		}
	break;
	
	case "register_custom_user":
		$json = json_decode(file_get_contents('php://input'), true);

		$registerdata = array();
		$registerdata['jp_email'] = $json['jp_email']; 
		$registerdata['jp_password'] = $json['jp_password'];
		$registerdata['jp_fcmtoken'] = $json['jp_fcmtoken'];
		
	   if(empty($registerdata['jp_fcmtoken'])) {
		   echo json_encode('false');
			exit();
	   }

		/******************************
		 *      Check if user exist
		 ******************************/
		$check_query = $db->prepare("
				SELECT jp_imeprezime
				FROM idk_jobstep_partners
				WHERE jp_email = :jp_email AND jp_password = :jp_password");
				
		$check_query->execute(array(
						':jp_email' => $registerdata['jp_email'],
						':jp_password' => md5($registerdata['jp_password'])
						));

		$number_of_rows = $check_query->rowCount();
		
		
		if($number_of_rows > 0){
			
			/******************************
			 *      Insert into database
			 ******************************/
			$register_query = $db->prepare("
							UPDATE idk_jobstep_partners 
							SET jp_fcmtoken = :jp_fcmtoken
							WHERE jp_email = :jp_email AND jp_password = :jp_password");
			$register_query->execute(array(
							':jp_email' => $registerdata['jp_email'],
							':jp_password' => md5($registerdata['jp_password']),
							':jp_fcmtoken' => $registerdata['jp_fcmtoken']));
			echo json_encode('true');
		}else{
			
			echo json_encode('false');
		}
	break;
	
	case "login":
		$json = json_decode(file_get_contents('php://input'), true);

		$logindata = array();
		$logindata['jp_email'] = $json['jp_email']; 
		$logindata['jp_password'] = $json['jp_password'];
		

		/******************************
		 *      Check if user exist
		 ******************************/
		$check_query = $db->prepare("
				SELECT jp_mailconfirmation_token
				FROM idk_jobstep_partners
				WHERE jp_email = :jp_email AND jp_password = :jp_password AND jp_confirmedaccount = :jp_confirmedaccount");
				
		$check_query->execute(array(
						':jp_email' => $logindata['jp_email'],
						':jp_password' => md5($logindata['jp_password']),
						':jp_confirmedaccount' => 1));

		$number_of_rows = $check_query->rowCount();
		if($number_of_rows == 1){
			$user = $check_query->fetch();
			$jp_mailconfirmation_token = $user['jp_mailconfirmation_token'];
			//echo $jp_mailconfirmation_token; 
			echo json_encode($jp_mailconfirmation_token);
		}else{
			echo json_encode('false');
		}
		
		
	break; 
	
	case "loginDemo":
		$json = json_decode(file_get_contents('php://input'), true);
		 
		$checkerData = array();
		$logindata = array();
		$logindata['jp_email'] = $json['jp_email']; 
		$logindata['jp_password'] = $json['jp_password'];

		/******************************
		 *      Check if user exist
		 ******************************/
		$check_query = $db->prepare("
				SELECT jp_mailconfirmation_token, jp_fcmtoken
				FROM idk_jobstep_partners
				WHERE jp_email = :jp_email AND jp_password = :jp_password AND jp_confirmedaccount = :jp_confirmedaccount");
				
		$check_query->execute(array(
						':jp_email' => $logindata['jp_email'],
						':jp_password' => md5($logindata['jp_password']),
						':jp_confirmedaccount' => 1));
						
		$number_of_rows = $check_query->rowCount();
		
		if($number_of_rows == 1){
			$user = $check_query->fetch();
			$jp_mailconfirmation_token = $user['jp_mailconfirmation_token'];
			$jp_fcm = $user['jp_fcmtoken'];
			
			
			if(empty($jp_fcm)){
				$checkerData[] = "CustomUser";
				$checkerData[] = $jp_mailconfirmation_token;
				echo json_encode($checkerData);
			}
			else 
				echo json_encode($jp_mailconfirmation_token);
		}else{
			echo json_encode('false');
		}
		
		
	break; 
	
	
	case "confirm_mail":
		$token = $_GET["token"];
		
		//Save
		$query = $db->prepare("
						UPDATE idk_jobstep_partners
						SET	jp_confirmedaccount = :jp_confirmedaccount
						WHERE jp_mailconfirmation_token = :jp_mailconfirmation_token");

		$query->execute(array(
					':jp_confirmedaccount' => 1,
					':jp_mailconfirmation_token' => $token));
					
		header("Location: https://crm.job-step.com/jobstep-partner/home.html"); 
	break;

	
	case "nalogs":
	
		$json = json_decode(file_get_contents('php://input'), true);

		$infodata = array();
		$infodata['token'] = $json['token'];   
		$infodata['lang'] = $json['lang'];   
	
		$check_query = $db->prepare("
			SELECT *
			FROM idk_jobstep_partners
			WHERE jp_mailconfirmation_token = :jp_mailconfirmation_token");
			
		$check_query->execute(array(
					':jp_mailconfirmation_token' => $infodata['token']));
					
		$rowCount = $check_query->rowCount();
		if($rowCount > 0){
			
			$user = $check_query->fetch();
			$jp_position = intval($user['jp_position']);
			
			$partner_procentage = getPartnerPositionPercentage($infodata['token']);
			$partner_dipl_price = getPartnerPositionForNostDiploma($jp_position);
			
			$nalogs_arr = array();
			// $product_item = array(
				// 'nalog_id' => '',
				// 'lg_language' => '',
				// 'kompanija_id' => '',
				// 'nalog_broj' => '',
				// 'nalog_naziv' => '',
				// 'nalog_opis' => '',		
				// 'lg_link_prijave' => '',
				// 'idk_urlimg_prijave' => '',
				// 'nalog_partner_provizija' => '',
			// );

			$nalogs_arr["data"] = array();
			//array_push($nalogs_arr["data"], $product_item);
			
			//lg_language = '" . $infodata['lang'] ."' AND
			$query = $db->prepare("
					SELECT nalog_id, kompanija_id, nalog_broj, nalog_naziv, nalog_opis,lg_language, nalog_kreirano, nalog_status, nalog_marketing_menadzer, lg_link_prijave, idk_urlimg_prijave, nalog_partner_provizija, no_nalognaziv, no_nalogopis, lg_id
					FROM idk_nalozi	
					INNER JOIN idk_link_generator ON idk_nalozi.nalog_id = idk_link_generator.lg_nalogid
					INNER JOIN 	idk_nalozi_opis ON idk_nalozi.nalog_id = idk_nalozi_opis.no_nalogid
					WHERE  lg_link_prijave LIKE '%https://job-step.net/konkurs/%' AND nalog_partner_active = 1 AND idk_nalozi_opis.no_lang = '" . $infodata['lang'] ."'  AND (idk_urlimg_prijave IS NOT NULL AND idk_urlimg_prijave != 'none')
					GROUP BY nalog_id
					ORDER BY idk_link_generator.lg_id DESC");
     
			$query->execute();

			$i = 0;
			while($row = $query->fetch()){
				if(!empty($row['no_nalogopis'])){ // PROVJERA ZA PREVOD

				
					//empty slots for nostrifikacija diplome in app
					if ($i%3 == 0 AND $i != 0) {
						$product_item = array('nalog_id' => '','lg_language' => '','kompanija_id' => '','nalog_broj' => '','nalog_naziv' => '','nalog_opis' => '','lg_link_prijave' => '','idk_urlimg_prijave' => '','nalog_partner_provizija' => '' , 'partner_dipl_price' => $partner_dipl_price);
						array_push($nalogs_arr["data"], $product_item);
					}
					
					$product_item = array(
						'nalog_id' => $row['nalog_id'],
						'lg_language' => $row['lg_language'],
						'kompanija_id' => $row['kompanija_id'],
						'nalog_broj' => $row['nalog_broj'],
						'nalog_naziv' => $row['no_nalognaziv'],
						'nalog_opis' => $row['no_nalogopis'],					
						'lg_link_prijave' => $row['lg_link_prijave'],
						'idk_urlimg_prijave' => $row['idk_urlimg_prijave'],
						'nalog_partner_provizija' => round(intval($row['nalog_partner_provizija'])*$partner_procentage , 2 ),
						'partner_dipl_price' => $partner_dipl_price,
					);
			  
					array_push($nalogs_arr["data"], $product_item);
					
					$i++;
				}
			}
			
			// set response code - 200 OK
			http_response_code(200);
			
			// show products data in json format
			echo json_encode($nalogs_arr);  
		}	
	break;
	
	
	case "info":
	
		$json = json_decode(file_get_contents('php://input'), true);

		$infodata = array();
		$infodata['token'] = $json['token'];   
		
		$info_arr = array();
		$info_arr["data"] = array();
		
		$check_query = $db->prepare("
			SELECT *
			FROM idk_jobstep_partners
			WHERE jp_mailconfirmation_token = :jp_mailconfirmation_token");
			
		$check_query->execute(array(
					':jp_mailconfirmation_token' => $infodata['token']));
		
					
		$user = $check_query->fetch();						
		$info_item = array(
			'jp_id' => $user['jp_id'],
			'jp_imeprezime' => $user['jp_imeprezime'],
			'jp_email' => $user['jp_email'],
			'jp_position' => $user['jp_position'],
			'jp_social_imgurl' => $user['jp_social_imgurl'],
			'jp_placanjeimeprezime' => $user['jp_placanjeimeprezime'],
			'jp_brtelefona' => $user['jp_brtelefona'],
			'jp_drzava' => $user['jp_drzava'],
			'jp_grad' => $user['jp_grad'],
			'jp_postanskibroj' => $user['jp_postanskibroj'],
			'jp_ulica' => $user['jp_ulica'],
			'jp_register_date' => $user['jp_register_date'],
			'jp_posta' => $user['jp_posta'],
			'jp_racun' => $user['jp_racun'],
			'jp_iban' => $user['jp_iban'],
			'jp_swift' => $user['jp_swift']
		);
 
		array_push($info_arr["data"], $info_item);
		http_response_code(200);
		
		echo json_encode($info_arr); 
		
		
	break;
	
	
	
	case "social_login":
	
		$json = json_decode(file_get_contents('php://input'), true);

		$infodata = array();
		$infodata['jp_imeprezime'] = $json['jp_imeprezime'];   
		$infodata['jp_email'] = $json['jp_email'];   
		$infodata['jp_socialid'] = $json['jp_socialid'];   
		$infodata['jp_social_token'] = $json['jp_social_token'];   
		$infodata['jp_social_imgurl'] = $json['jp_social_imgurl'];   
		$infodata['jp_provider'] = $json['jp_provider'];   
		$infodata['jp_fcmtoken'] = $json['jp_fcmtoken'];   
		$infodata['jp_preporuka_id'] = $json['jp_preporuka_id'];   
		
		/******************************
		 *      Check if user exist
		 ******************************/
		$check_query = $db->prepare("
				SELECT jp_id, jp_mailconfirmation_token
				FROM idk_jobstep_partners
				WHERE jp_email = :jp_email");
				
		$check_query->execute(array(
						':jp_email' => $infodata['jp_email']));

		$number_of_rows = $check_query->rowCount();

		if($number_of_rows == 1){
			$user = $check_query->fetch();
			$jp_id = $user['jp_id'];
			$jp_mailconfirmation_token = $user['jp_mailconfirmation_token'];
			
			//Save NEW NOTIFICATION TOKEN :)
			$query = $db->prepare("
				UPDATE idk_jobstep_partners
				SET	jp_fcmtoken = :jp_fcmtoken
				WHERE jp_id = :jp_id");

			$query->execute(array(
				':jp_fcmtoken' => $infodata['jp_fcmtoken'],
				':jp_id' => $jp_id));
				
				
			//echo $jp_mailconfirmation_token; 
			echo json_encode($jp_mailconfirmation_token);
		}else{
			/******************************
			 *      If Not, Save in DB
			 ******************************/	
			$register_date = date('Y-m-d H:i:s');
			$jp_mailconfirmation_token = bin2hex(openssl_random_pseudo_bytes(12));
			 
			 
			$save_query = $db->prepare("
							INSERT INTO idk_jobstep_partners
								(jp_imeprezime, jp_email, jp_password, jp_provider, jp_register_date, jp_fcmtoken, jp_mailconfirmation_token, jp_confirmedaccount, jp_socialid, jp_social_token, jp_social_imgurl, jp_preporuka_id)
								VALUES
								(:jp_imeprezime, :jp_email, :jp_password, :jp_provider, :jp_register_date, :jp_fcmtoken, :jp_mailconfirmation_token, :jp_confirmedaccount, :jp_socialid, :jp_social_token, :jp_social_imgurl, :jp_preporuka_id)");
			$save_query->execute(array(
							':jp_imeprezime' => $infodata['jp_imeprezime'],
							':jp_email' => $infodata['jp_email'],
							':jp_password' => md5('social_network_login'),
							':jp_provider' => $infodata['jp_provider'],
							':jp_register_date' => $register_date,
							':jp_fcmtoken' => $infodata['jp_fcmtoken'],
							':jp_mailconfirmation_token' => $jp_mailconfirmation_token,
							':jp_confirmedaccount' => 1,
							':jp_socialid' => $infodata['jp_socialid'],
							':jp_social_token' => $infodata['jp_social_token'],
							':jp_social_imgurl' => $infodata['jp_social_imgurl'],
							':jp_preporuka_id' => $infodata['jp_preporuka_id']
							));
							
							
			echo json_encode($jp_mailconfirmation_token);
			// print_r(json_encode($save_query->errorInfo()));
		}
		
		
	break;
	
	case "partner_stats":
	
	$json = json_decode(file_get_contents('php://input'), true);

	$infodata = array();
	$infodata['token'] = $json['token'];   
	
	if(!empty($json['lang'])){  $infodata['lang'] = $json['lang'];  }		

	$id = getPartnerIdFromToken($infodata['token']);
	$jp_brtelefona = getPartnerPhoneNumber($id);
	//UPDATE PARTNER LANG FOR NOTIFICATIONS
	if(!empty($infodata['lang'])){ updateLangOfPartner( $id, $infodata['lang']); }
	
	
	//SVI KANDIDATI
	$query_ukupan = $db->prepare("
				SELECT kandidat_id,kandidat_status_prijave,kandidat_partner_status 
				FROM idk_kandidati
				WHERE kandidat_partnerid = :kandidat_partnerid");
				
	$query_ukupan->execute(array(
					':kandidat_partnerid' => $id));
	
	$ukupan_broj_kandidata = $query_ukupan->rowCount();
	$moguca_isplata = 0;
	$isplaceno = 0;
	$ukupan_broj_kandidata_zaposlenih = 0;
	$moguca_isplata_dipl = 0;
	$dipl_zarada = 0;
	$ukupan_broj_kandidata_dipl_zavrsenih = 0;
	
	
	// MOGUCA ISPLATA
	while($moguca_isplata_row = $query_ukupan->fetch()){
		
		// // POKUPI KANDIDAT ID 
		$kandidat_id = $moguca_isplata_row["kandidat_id"];  
		$kandidat_partner_status = $moguca_isplata_row["kandidat_partner_status"];

		if($kandidat_partner_status == 1){
			$procenat = 0.8;
		}elseif($kandidat_partner_status == 2){
			$procenat = 0.9;
		}elseif($kandidat_partner_status == 3){
			$procenat = 1;
		}elseif($kandidat_partner_status == 4){
			$procenat = 1.1;
		}elseif($kandidat_partner_status == 5){
			$procenat = 1.25;
		}else{
			$procenat = 0.8;
		}
		
		// POKUPI KOJEM PROJEKTU PRIPADA KANDIDATI
		$query_kandidat_project = $db->prepare("
					SELECT pk_projectid
					FROM idk_project_kandidati
					WHERE pk_kandidatid = :pk_kandidatid");
					
		$query_kandidat_project->execute(array(
						':pk_kandidatid' => $kandidat_id
						));
						
		$row_project = $query_kandidat_project->fetch(); 
		
		$projekat = intval($row_project['pk_projectid']);
		if($projekat != 0){
			// POKUPI KOJEM NALOGU PRIPADA KANDIDAT PREKO PROJEKTA
			$query_kandidat_nalog = $db->prepare("
						SELECT project_nalogid
						FROM idk_projects
						WHERE project_id = :project_id"); 
						
			$query_kandidat_nalog->execute(array(
							':project_id' => $projekat
							));
							
			$row_nalog = $query_kandidat_nalog->fetch();
			
			$nalog = intval($row_nalog['project_nalogid']);
			if($nalog != 0){
				// POKUPI INFORMACIJE O NALOGU
				$query_kandidat_nalog_info = $db->prepare("
							SELECT nalog_partner_provizija
							FROM idk_nalozi
							WHERE nalog_id = :nalog_id");
							
				$query_kandidat_nalog_info->execute(array(
								':nalog_id' => $nalog
								));
								
				$row_provizija = $query_kandidat_nalog_info->fetch();
								
				$provizija = intval($row_provizija['nalog_partner_provizija']) * $procenat;       
				
				// AKO JE NALOG PLACEN I U STATUSU POTPIS UGOVORA ONDA DODAJ PROVIZIJU-MOGUCA ISPLATA
				$query_isplacen_nalog_moguca_zarada = $db->prepare("
							SELECT kf_id
							FROM idk_kandidat_financije
							WHERE kandidat_id = :kandidat_id AND kf_placeno = :kf_placeno AND kf_type = :kf_type");
							
				$query_isplacen_nalog_moguca_zarada->execute(array(
								':kandidat_id' => $kandidat_id,
								':kf_placeno' => 1,
								':kf_type' => 1
								)); 
								
				$isplacen_nalog_moguca_zarada = $query_isplacen_nalog_moguca_zarada->rowCount();
				
				if($isplacen_nalog_moguca_zarada > 0){
					$moguca_isplata = $moguca_isplata + $provizija; 
				}else{
					$moguca_isplata = $moguca_isplata + 0;
				} 
				
				// AKO JE NALOG PLACEN I U STATUSU POCETAK RADA ONDA DODAJ PROVIZIJU-ISPLACENO
				$query_isplacen_nalog_isplaceno = $db->prepare("
							SELECT kf_id
							FROM idk_kandidat_financije
							WHERE kandidat_id = :kandidat_id AND kf_placeno = :kf_placeno AND kf_type = :kf_type");
							
				$query_isplacen_nalog_isplaceno->execute(array(
								':kandidat_id' => $kandidat_id,
								':kf_placeno' => 1,
								':kf_type' => 2
								));
								
				$isplacen_nalog_isplaceno = $query_isplacen_nalog_isplaceno->rowCount();
				
				if($isplacen_nalog_isplaceno > 0){
					$isplaceno = $isplaceno + $provizija;
				}else{
					$isplaceno = $isplaceno + 0;
				}
			}
		}
	} 
	
	
	//SVI DIPL
	$query_DIPL = $db->prepare("
				SELECT id_broj_nd_kandidata, ime_nd_kandidata, prezime_nd_kandidata, rata1_nd_kandidata, rata2_nd_kandidata, partner_nd_status
				FROM idk_nd_kandidata
				WHERE kandidat_idd = :kandidat_idd AND povijest_nd_kandidata = :povijest_nd_kandidata");
				
	$query_DIPL->execute(array(
					':kandidat_idd' => $id,
					':povijest_nd_kandidata' => 3));

	
	$ukupan_broj_kandidata_dipl = $query_DIPL->rowCount();
	// MOGUCA ISPLATA
	while($row_dipl = $query_DIPL->fetch()){
		$dipl_price = 25;
		$id_broj_nd_kandidata = $row_dipl["id_broj_nd_kandidata"]; 
		$ime_nd_kandidata = $row_dipl["ime_nd_kandidata"]; 
		$prezime_nd_kandidata = $row_dipl["prezime_nd_kandidata"];
		$rata1_nd_kandidata = $row_dipl["rata1_nd_kandidata"]; 
		$rata2_nd_kandidata = $row_dipl["rata2_nd_kandidata"];		
		$partner_nd_status = $row_dipl["partner_nd_status"];

		if($partner_nd_status == 1){
			$dipl_price = 20;
		}elseif($partner_nd_status == 2){
			$dipl_price = 22.5;
		}elseif($partner_nd_status == 3){
			$dipl_price = 25;
		}elseif($partner_nd_status == 4){
			$dipl_price = 25;
		}elseif($partner_nd_status == 5){
			$dipl_price = 25;
		}
		
		$check_isplaceno = getUplacenaRataND($id_broj_nd_kandidata , 1);
		
		if($check_isplaceno == 0){
			$moguca_isplata += 0;
			$moguca_isplata_dipl += $dipl_price;
			$isplaceno += 0;
			
			$dipl_zarada +=0;
			$ukupan_broj_kandidata_dipl_zavrsenih +=0;
			$ukupan_broj_kandidata_zaposlenih += 0; 
		}
		else if($check_isplaceno == 1){
			$moguca_isplata += 0;
			$moguca_isplata_dipl += 0;
			$isplaceno += 0;
			$dipl_zarada +=$dipl_price;
			$ukupan_broj_kandidata_dipl_zavrsenih +=1;
		}
		
	}

	
	
	//SVI U CASTINGU
	$query_casting = $db->prepare("
				SELECT kandidat_id
				FROM idk_kandidati
				WHERE kandidat_partnerid = :kandidat_partnerid AND kandidat_status_prijave = :kandidat_status_prijave");
				
	$query_casting->execute(array(
					':kandidat_partnerid' => $id,
					':kandidat_status_prijave' => 3));  
	
	$ukupan_broj_kandidata_casting = $query_casting->rowCount();
	
	//SVI ZAPOSLENI
	$query_zaposlenih = $db->prepare("
				SELECT kandidat_id
				FROM idk_kandidati
				WHERE kandidat_partnerid = :kandidat_partnerid AND kandidat_status_prijave = :kandidat_status_prijave");
				
	$query_zaposlenih->execute(array(
					':kandidat_partnerid' => $id,
					':kandidat_status_prijave' => 4));  
	
	$ukupan_broj_kandidata_zaposlenih += $query_zaposlenih->rowCount(); // + DIPL KANDIDATI
	
	//BROJ PREGLEDA
	$query_pregled = $db->prepare("
			SELECT SUM(jpp_pregledi_count) as jpp_pregledi_count
			FROM idk_jobstep_partners_pregledi
			WHERE jpp_partnerid = :jpp_partnerid");
				
	$query_pregled->execute(array(
					':jpp_partnerid' => $id));  
	
	$row = $query_pregled->fetch();

	$jpp_pregledi_count = intval($row['jpp_pregledi_count']);

	
	$info_item = array(
		'broj_kandidata' => utf8_encode ( $ukupan_broj_kandidata) ,
		'broj_casting_kandidata' => utf8_encode ( $ukupan_broj_kandidata_casting) ,
		'broj_zaposlenih_kandidata' => utf8_encode ( $ukupan_broj_kandidata_zaposlenih) ,
		'broj_dipl_preporuka' => utf8_encode ( $ukupan_broj_kandidata_dipl) ,
		'broj_dipl_zavrsenih' => utf8_encode ( $ukupan_broj_kandidata_dipl_zavrsenih) ,
		'broj_pregleda' => utf8_encode ( $jpp_pregledi_count) ,
		'moguca_isplata' => utf8_encode ( $moguca_isplata) ,
		'moguca_isplata_dipl' => utf8_encode ( $moguca_isplata_dipl) ,
		'dipl_zarada' => utf8_encode ( $dipl_zarada) ,
		'isplaceno' => utf8_encode ( $isplaceno) ,
		'jp_brtelefona' => utf8_encode ( $jp_brtelefona) ,
	);
		
		
	$info_arr = array();
	$info_arr["data"] = array();
	
	array_push($info_arr["data"], $info_item);
	http_response_code(200);
	
	echo json_encode($info_arr);
					
	break; 
	  
	
	
	
	case "get_lang":
		$json = json_decode(file_get_contents('php://input'), true);

		$infodata = array();
		$infodata['token'] = $json['token'];  
		
		$partner_lang = getPartnerLangFromToken(base64_decode($infodata['token']));
 
		http_response_code(200);
		echo $partner_lang;
	break;
	
	
	case "count":

		$url_id = $_POST['id'];
		$token = $_POST['token'];
		
		$partner_id = intval(getPartnerIdFromToken($token));


		//Get
		$query_select = $db->prepare("
			SELECT lg_nalogid
			FROM idk_link_generator
			WHERE lg_id = :lg_id");

		$query_select->execute(array(
							':lg_id' => $url_id));

		$row_select = $query_select->fetch();
		$lg_nalogid = $row_select['lg_nalogid'];
		
		
		$select_query = $db->prepare("
			SELECT jpp_id
			FROM idk_jobstep_partners_pregledi
			WHERE jpp_partnerid = :jpp_partnerid  AND jpp_nalogid = :jpp_nalogid");

		$select_query->execute(array(
						':jpp_partnerid' => $partner_id,
						':jpp_nalogid' => $lg_nalogid	));

		$rowCount = $select_query->rowCount();

		if($rowCount > 0){
			//Save
			$query = $db->prepare("
				UPDATE idk_jobstep_partners_pregledi
				SET	jpp_pregledi_count = jpp_pregledi_count + 1
				WHERE jpp_partnerid = :jpp_partnerid  AND jpp_nalogid = :jpp_nalogid");

			$query->execute(array(
						':jpp_partnerid' => $partner_id,
						':jpp_nalogid' => $lg_nalogid));
		}else{
			// INSERT
			
			$insert_query = $db->prepare("
							INSERT INTO idk_jobstep_partners_pregledi
								(jpp_partnerid, jpp_nalogid, jpp_pregledi_count)
								VALUES
								(:jpp_partnerid, :jpp_nalogid, :jpp_pregledi_count)");
			$insert_query->execute(array(
							':jpp_partnerid' => $partner_id,
							':jpp_nalogid' => $lg_nalogid,
							':jpp_pregledi_count' => 1));
										
		}

	break;
	
	case "forgot_password":
	
		$json = json_decode(file_get_contents('php://input'), true);

		$infodata = array();
		$infodata['jp_email'] = $json['jp_email']; 
		
		
		/******************************
		 *      Check if user exist
		 ******************************/
		$check_query = $db->prepare("
				SELECT jp_id
				FROM idk_jobstep_partners
				WHERE jp_email = :jp_email");
				
		$check_query->execute(array(
						':jp_email' => $infodata['jp_email']));

		$number_of_rows = $check_query->rowCount();

		if($number_of_rows == 1){
			$user = $check_query->fetch();
			$jp_id = $user['jp_id'];
			$password = bin2hex(openssl_random_pseudo_bytes(4));
			// MAIL WITH PASSWORD
			
			//UPDATE
			$query = $db->prepare("
							UPDATE idk_jobstep_partners
							SET	jp_password = :jp_password
							WHERE jp_id = :jp_id");

			$query->execute(array(
						':jp_password' => md5($password),
						':jp_id' => $jp_id));

			
			$body = '
				<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head> <meta name="viewport" content="width=device-width" /> <meta http-equiv="Content-Type" content="text/html; " /> <title>Jobstep Partner - New password.</title> <style> * { margin: 0; padding: 0; font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6; } img { max-width: 90px;height: auto; } body { -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; } a { color: #62969D; } .last { margin-bottom: 0; } .first { margin-top: 0; } /* ------------------------------------- BODY ------------------------------------- */ table.body-wrap { width: 100%; padding: 20px; } table.body-wrap .container { border: 1px solid #f0f0f0; } /* ------------------------------------- FOOTER ------------------------------------- */ table.footer-wrap { width: 100%; clear: both !important; } .footer-wrap .container p { font-size: 12px; color: #666; } table.footer-wrap a { color: #999; } /* ------------------------------------- TYPOGRAPHY ------------------------------------- */ h1, h2, h3 { font-family: "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif; line-height: 1.1; margin-bottom: 15px; color: #000; margin: 40px 0 10px; line-height: 1.2; font-weight: 200; } h1 { font-size: 28px;margin: 50px 0px 50px 0px; } h2 { font-size: 22px; } h3 { font-size: 18px; } p, ul { margin-bottom: 10px; font-weight: normal; font-size: 14px; } ul li { margin-left: 5px; list-style-position: inside; } /* Set a max-width, and make it display as block so it will automatically stretch to that width, but will also shrink down on a phone or something */ .container { display: block !important; max-width: 600px !important; margin: 0 auto !important; /* makes it centered */ clear: both !important; } /* This should also be a block element, so that it will fill 100% of the .container */ .content { padding: 20px; max-width: 600px; margin: 0 auto; display: block; } /* Lets make sure tables in the content area are 100% wide */ .content table { width: 100%; } </style></head><body bgcolor="#f6f6f6"> <!-- body --> <table class="body-wrap"> <tr> <td></td> <td class="container" bgcolor="#FFFFFF"> <div class="content"> <table> <tr> <td> <img src="https://job-step.net/wp-content/uploads/2019/02/Logo-Jobstep-International-PNG.png" style="width:100px;height:auto;float:right;" alt="Jobstep International d.o.o." /> </td> </tr> <tr> <td> <h1> This is your new password </h1> <p> Password: '. $password .', <br /> Thank you for choosing Jobstep Partner APP. </p><br /> <br /> <p style="text-align:center;"><a href="https://job-step.net">Jobstep International d.o.o.</a></p> </td> </tr> </table> </div> </td> <td></td> </tr> </table> <!-- /body --> <!-- footer --> <table class="footer-wrap"> <tr> <td></td> <td class="container"> <div class="content"> <table> <tr> <td align="center"> <p> You are not subscribed to anything! <a href="https://job-step.net/?page_id=62">Contact Us</a>. </p> </td> </tr> </table> </div> </td> <td></td> </tr> </table> <!-- /footer --></body></html>
			';

			$mail = new PHPMailer;
			$mail->isSMTP();											// Set mailer to use SMTP
			$mail->Host = 'smtp.gmail.com';  			// Specify main and backup SMTP servers
			$mail->SMTPAuth = true;						// Enable SMTP authentication
			$mail->Username = 'support@job-step.com';	// SMTP username
			$mail->Password = 'eooc nnxo aylp lqkh';		// SMTP password
			$mail->SMTPSecure = 'ssl';					// Enable TLS encryption, `ssl` also accepted
			$mail->Port = 465;							// TCP port to connect to
			$mail->CharSet = 'UTF-8';
			
			$mail->setFrom('support@job-step.com', 'JobStep Partner');
			$mail->addAddress($infodata['jp_email'], $infodata['jp_email']);		// Add a recipient


			$mail->Subject = "JobStep Partner - New password.";
			$mail->Body    = $body;
			$mail->AltBody = "This is your new password.";

			if(!$mail->send()) {
				echo 'Message could not be sent.';
				echo 'Mailer Error: ' . $mail->ErrorInfo;  
			}
		}else{
			http_response_code(200);
			echo json_encode('false');
		}
	break;
	
	
	case "updatepartner_info":
	
		$json = json_decode(file_get_contents('php://input'), true);

		$infodata = array();
		$infodata['token'] = $json['token']; 
		
		$infodata['jp_placanjeimeprezime'] = $json['jp_placanjeimeprezime']; 
		$infodata['jp_brtelefona'] = $json['jp_brtelefona']; 
		$infodata['jp_drzava'] = $json['jp_drzava']; 
		$infodata['jp_grad'] = $json['jp_grad']; 
		$infodata['jp_postanskibroj'] = $json['jp_postanskibroj']; 
		$infodata['jp_ulica'] = $json['jp_ulica']; 
		
		$infodata['jp_posta'] = $json['jp_posta']; 
		$infodata['jp_racun'] = $json['jp_racun']; 
		$infodata['jp_iban'] = $json['jp_iban']; 
		$infodata['jp_swift'] = $json['jp_swift']; 
		
		  
		//Save
		$query = $db->prepare("
						UPDATE idk_jobstep_partners
						SET	jp_placanjeimeprezime = :jp_placanjeimeprezime,	jp_brtelefona = :jp_brtelefona, jp_drzava = :jp_drzava , jp_grad = :jp_grad, jp_postanskibroj = :jp_postanskibroj ,jp_ulica = :jp_ulica ,jp_posta = :jp_posta ,jp_racun = :jp_racun ,jp_iban = :jp_iban ,jp_swift = :jp_swift
						WHERE jp_mailconfirmation_token = :jp_mailconfirmation_token");

		$query->execute(array(
				':jp_placanjeimeprezime' => $infodata['jp_placanjeimeprezime'],
				':jp_brtelefona' => $infodata['jp_brtelefona'],
				':jp_drzava' => $infodata['jp_drzava'],
				':jp_grad' => $infodata['jp_grad'],
				':jp_postanskibroj' => $infodata['jp_postanskibroj'],
				':jp_ulica' => $infodata['jp_ulica'],
				':jp_posta' => $infodata['jp_posta'],
				':jp_racun' => $infodata['jp_racun'],
				':jp_iban' => $infodata['jp_iban'],
				':jp_swift' => $infodata['jp_swift'],			
				':jp_mailconfirmation_token' => $infodata['token']));
		
	break;
	
	case "candidate_list":
	$json = json_decode(file_get_contents('php://input'), true);

	$infodata = array();
	$infodata['token'] = $json['token'];   
	$infodata['lang'] = $json['lang'];   
				

	$partner_id = getPartnerIdFromToken($infodata['token']);
	
	
	//SVI KANDIDATI 
	$query = $db->prepare("
				SELECT *
				FROM idk_kandidati
				WHERE kandidat_partnerid = :kandidat_partnerid"); 
				
	$query->execute(array(
					':kandidat_partnerid' => $partner_id));
					
	$info_arr = array();
	$info_arr["data"] = array();
	
	
	$gt = new gtranslate();
	while($row = $query->fetch()){  
		$id = $row['kandidat_id'];
		$ime_kandidata = $row['kandidat_ime'];
		$kandidat_prezime = $row['kandidat_prezime'];
		
		$nashalatinica = array('Č','Ć','Š','Ž','Đ');
		$latinica = array('C','C','S','Z','DJ');

		$ime_k = str_replace($nashalatinica,$latinica,$ime_kandidata);
		$prezime_k = str_replace($nashalatinica,$latinica,$kandidat_prezime);
		
		$kandidat_shortname = ''.$ime_k[0].''.$prezime_k[0].'';
		
		
		
		$kandidat_status_prijave = $row['kandidat_status_prijave'];
		$nalog_naziv = $row['kandidat_prijava_na'];	
		if(!empty($row['kandidat_datum_pocetakrada'])){
			$pocetak_rada_kandidata = date('d.m.Y', strtotime($row['kandidat_datum_pocetakrada']));
		}else{
			$pocetak_rada_kandidata = '-';			
		}
	
		$datum_prijave_kandidata = date('d.m.Y', strtotime($row['kandidat_datetime']));
		$kandidat_partner_status = $row['kandidat_partner_status'];
		
		if($kandidat_partner_status == 1){
			$procenat = 0.8;
		}elseif($kandidat_partner_status == 2){
			$procenat = 0.9;
		}elseif($kandidat_partner_status == 3){
			$procenat = 1;
		}elseif($kandidat_partner_status == 4){
			$procenat = 1.1;
		}elseif($kandidat_partner_status == 5){
			$procenat = 1.25;
		}else{
			$procenat = 0.8;
		}
	
		// STATUS KANDIDATA
		$query_projekti = $db->prepare("
					SELECT status_naziv
					FROM idk_kandidat_status_prijave
					WHERE status_id = :status_id");

		$query_projekti->execute(array(':status_id' => $kandidat_status_prijave));
		$projekti = $query_projekti->fetch();
		$status_naziv = $projekti['status_naziv'];
		if(empty($status_naziv)) $status_naziv = 'U Čekanju';
		
		//TRANSLATE
		if($infodata['lang'] != 'bs'){
			$status_naziv = $gt->translate($status_naziv , ''. $infodata['lang'] .'','bs');
			$nalog_naziv = $gt->translate($nalog_naziv , ''. $infodata['lang'] .'','bs');
		}
		
		// POKUPI KOJEM PROJEKTU PRIPADA KANDIDATI
		$query_kandidat_project = $db->prepare("
					SELECT pk_projectid
					FROM idk_project_kandidati
					WHERE pk_kandidatid = :pk_kandidatid
					ORDER BY pk_id DESC");
					
		$query_kandidat_project->execute(array(
						':pk_kandidatid' => $id
						));
						
		$row_project = $query_kandidat_project->fetch(); 
		
		$projekat = intval($row_project['pk_projectid']);
		if($projekat != 0){
			// POKUPI KOJEM NALOGU PRIPADA KANDIDAT PREKO PROJEKTA 
			$query_kandidat_nalog = $db->prepare("
						SELECT project_nalogid
						FROM idk_projects
						WHERE project_id = :project_id"); 
						
			$query_kandidat_nalog->execute(array(
							':project_id' => $projekat
							));
							
			$row_nalog = $query_kandidat_nalog->fetch();
			
			$nalog = intval($row_nalog['project_nalogid']);
			
			
			if($nalog != 0){
		
			// POKUPI INFORMACIJE O NALOGU
			$query_kandidat_nalog_info = $db->prepare("
						SELECT nalog_partner_provizija
						FROM idk_nalozi
						WHERE nalog_id = :nalog_id");
						
			$query_kandidat_nalog_info->execute(array(
							':nalog_id' => $nalog
							));
						
				$row_provizija = $query_kandidat_nalog_info->fetch();
						
				$provizija = intval($row_provizija['nalog_partner_provizija']);
				
				$info_item = array(
					'id' => $id,
					'ime_kandidata' => $ime_kandidata,
					'kandidat_shortname' => $kandidat_shortname,
					'status_kandidata' => $status_naziv,
					'pocetak_rada_kandidata' => $pocetak_rada_kandidata,
					'datum_prijave_kandidata' => $datum_prijave_kandidata,
					'nalog_naziv' => $nalog_naziv,
					'provizija' => round($provizija * $procenat , 2 ),
				);
					
				
				
				array_push($info_arr["data"], $info_item);
				
	
			} 
		}
	}

	// //SVI DIPL
	// $query_DIPL = $db->prepare("
				// SELECT id_broj_nd_kandidata, ime_nd_kandidata, prezime_nd_kandidata,status_nd_kandidata,  partner_nd_status
				// FROM idk_nd_kandidata
				// WHERE kandidat_idd = :kandidat_idd AND povijest_nd_kandidata = :povijest_nd_kandidata");
				
	// $query_DIPL->execute(array(
					// ':kandidat_idd' => $partner_id,
					// ':povijest_nd_kandidata' => 3));

	// // MOGUCA ISPLATA
	// while($row_dipl = $query_DIPL->fetch()){
		// $dipl_price = 25;
		// $id_broj_nd_kandidata = $row_dipl["id_broj_nd_kandidata"]; 
		// $ime_nd_kandidata = $row_dipl["ime_nd_kandidata"]; 
		// $prezime_nd_kandidata = $row_dipl["prezime_nd_kandidata"];	
		// $status_nd_kandidata = $row_dipl["status_nd_kandidata"];	
		// $partner_nd_status = $row_dipl["partner_nd_status"];
		
		// $nashalatinica = array('Č','Ć','Š','Ž','Đ');
		// $latinica = array('C','C','S','Z','DJ');

		// $ime_k = str_replace($nashalatinica,$latinica,$ime_nd_kandidata);
		// $prezime_k = str_replace($nashalatinica,$latinica,$prezime_nd_kandidata);
		
		// $kandidat_shortname = ''.$ime_k[0].''.$prezime_k[0].'';

		// if($partner_nd_status == 1){
			// $dipl_price = 20;
		// }elseif($partner_nd_status == 2){
			// $dipl_price = 22.5;
		// }elseif($partner_nd_status == 3){
			// $dipl_price = 25;
		// }elseif($partner_nd_status == 4){
			// $dipl_price = 25;
		// }elseif($partner_nd_status == 5){
			// $dipl_price = 25;
		// }
		
		

		// if($status_nd_kandidata == 1 OR $status_nd_kandidata == 2 OR $status_nd_kandidata == 3 OR $status_nd_kandidata == 4 OR $status_nd_kandidata == 5){
			// $dipl_status_naziv = "U obradi";	
		// }elseif($status_nd_kandidata == 6){
			// $dipl_status_naziv = "Završen";	
		// }elseif($status_nd_kandidata == 7){
			// $dipl_status_naziv = "Odbio";	
		// }else{
			// $dipl_status_naziv = "";
		// }
		
		// if($infodata['lang'] == 'bs'){
			// $nostrifikacija_diplome = "Nostrifikacija diplome";
		// }elseif($infodata['lang'] == 'de'){
			// $nostrifikacija_diplome = "Diplomanerkennung";
		// }else{
			// $nostrifikacija_diplome = "Diploma nostrification";
		// }
		
		// //TRANSLATE
		// if($infodata['lang'] != 'bs'){
			// $dipl_status_naziv = $gt->translate($dipl_status_naziv , ''. $infodata['lang'] .'','bs');
		// }	
		
		// $info_item = array(
			// 'id' => utf8_encode( $id_broj_nd_kandidata) ,
			// 'ime_kandidata' => utf8_encode( $ime_nd_kandidata) ,
			// 'kandidat_shortname' => utf8_encode( $kandidat_shortname) ,
			// 'status_kandidata' => utf8_encode( $dipl_status_naziv) ,
			// 'pocetak_rada_kandidata' => utf8_encode( '-') ,
			// 'datum_prijave_kandidata' => utf8_encode( '-') ,
			// 'nalog_naziv' => utf8_encode( $nostrifikacija_diplome) ,
			// 'provizija' => utf8_encode( $dipl_price) ,
		// );	
				
		
		// array_push($info_arr["data"], $info_item);
	
	// }
	
	
	
	http_response_code(200);
				
	echo json_encode($info_arr);
	
	break;
	
	
	case "candidate_list_dipl":
	
		$json = json_decode(file_get_contents('php://input'), true);

		$infodata = array();
		$infodata['token'] = $json['token'];   
		$infodata['lang'] = $json['lang'];   
					
		
		
		$partner_id = getPartnerIdFromToken($infodata['token']);
		//SVI DIPL
		$query_DIPL = $db->prepare("
					SELECT id_broj_nd_kandidata, ime_nd_kandidata, prezime_nd_kandidata,status_nd_kandidata,  partner_nd_status
					FROM idk_nd_kandidata
					WHERE kandidat_idd = :kandidat_idd AND povijest_nd_kandidata = :povijest_nd_kandidata");
					
		$query_DIPL->execute(array(
						':kandidat_idd' => $partner_id,
						// ':kandidat_idd' => 93,
						':povijest_nd_kandidata' => 3));
		// MOGUCA ISPLATA
		
		
		
		$info_arr = array();
		$info_arr["data"] = array();
		
		while($row_dipl = $query_DIPL->fetch()){
			$dipl_price = 25;
			$id_broj_nd_kandidata = $row_dipl["id_broj_nd_kandidata"]; 
			$ime_nd_kandidata = $row_dipl["ime_nd_kandidata"]; 
			$prezime_nd_kandidata = $row_dipl["prezime_nd_kandidata"];	
			$status_nd_kandidata = $row_dipl["status_nd_kandidata"];	
			$partner_nd_status = $row_dipl["partner_nd_status"];
			
			$nashalatinica = array('Č','Ć','Š','Ž','Đ');
			$latinica = array('C','C','S','Z','DJ');

			$ime_k = str_replace($nashalatinica,$latinica,$ime_nd_kandidata);
			$prezime_k = str_replace($nashalatinica,$latinica,$prezime_nd_kandidata);
			
			$kandidat_shortname = ''.$ime_k[0].''.$prezime_k[0].'';

			if($partner_nd_status == 1){
				$dipl_price = 20;
			}elseif($partner_nd_status == 2){
				$dipl_price = 22.5;
			}elseif($partner_nd_status == 3){
				$dipl_price = 25;
			}elseif($partner_nd_status == 4){
				$dipl_price = 25;
			}elseif($partner_nd_status == 5){
				$dipl_price = 25;
			}
			
			

			if($status_nd_kandidata == 1 OR $status_nd_kandidata == 2 OR $status_nd_kandidata == 3 OR $status_nd_kandidata == 4 OR $status_nd_kandidata == 5){
				// $dipl_status_naziv = "U obradi";	
				
				if($infodata['lang'] == 'bs'){
					$dipl_status_naziv = "U obradi";
				}elseif($infodata['lang'] == 'de'){
					$nostrifikacija_diplome = "In Bearbeitung";
				}else{
					$nostrifikacija_diplome = "In process";
				}
			}elseif($status_nd_kandidata == 6){
				if($infodata['lang'] == 'bs'){
					$dipl_status_naziv = "Završen";
				}elseif($infodata['lang'] == 'de'){
					$dipl_status_naziv = "Beendet";
				}else{
					$dipl_status_naziv = "Completed";
				}
			}elseif($status_nd_kandidata == 7){
				if($infodata['lang'] == 'bs'){
					$dipl_status_naziv = "Odbijen";
				}elseif($infodata['lang'] == 'de'){
					$dipl_status_naziv = "Abgelehnt";
				}else{
					$dipl_status_naziv = "Declined";
				}
			}else{
				$dipl_status_naziv = "";
			}
			
			
			if($infodata['lang'] == 'bs'){
				$nostrifikacija_diplome = "Nostrifikacija diplome";
			}elseif($infodata['lang'] == 'de'){
				$nostrifikacija_diplome = "Diplomanerkennung";
			}else{
				$nostrifikacija_diplome = "Diploma nostrification";
			}
			
			//TRANSLATE  --- POSSIBLE BUG -> GT-TRANSLATE UNEXPECTEDLY STOPS WORKING
			// if($infodata['lang'] != 'bs'){
				// $dipl_status_naziv = $gt->translate($dipl_status_naziv , ''. $infodata['lang'] .'','bs');
			// }	
			
			
			// var_dump($row_dipl);
			$info_item = array(
				'id' => $id_broj_nd_kandidata ,
				'ime_kandidata' => utf8_encode( $ime_nd_kandidata) ,
				'kandidat_shortname' => utf8_encode( $kandidat_shortname) ,
				'status_kandidata' => utf8_encode( $dipl_status_naziv) ,
				'pocetak_rada_kandidata' => utf8_encode( '-') ,
				'datum_prijave_kandidata' => utf8_encode( '-') ,
				'nalog_naziv' => utf8_encode( $nostrifikacija_diplome) ,
				'provizija' => utf8_encode( $dipl_price) ,
			);	
					
			array_push($info_arr["data"], $info_item);
		}
		
		
		
		
		$json = json_encode( $info_arr );
		http_response_code(200);
		echo $json;
	
	break;
	
	case "candidate_list_demo":
	$json = json_decode(file_get_contents('php://input'), true);

		$infodata = array();
		$infodata['token'] = $_POST['token'];   
		$infodata['lang'] = $_POST['lang'];   
					

		$partner_id = getPartnerIdFromToken($infodata['token']);
		
		
		//SVI KANDIDATI 
		$query = $db->prepare("
					SELECT *
					FROM idk_kandidati
					WHERE kandidat_partnerid = :kandidat_partnerid"); 
					
		$query->execute(array(
						':kandidat_partnerid' => $partner_id));
						
		$info_arr = array();
		$info_arr["data"] = array();
		
		
		$gt = new gtranslate();
		while($row = $query->fetch()){  
			$id = $row['kandidat_id'];
			$ime_kandidata = $row['kandidat_ime'];
			$kandidat_prezime = $row['kandidat_prezime'];
			
			$nashalatinica = array('Č','Ć','Š','Ž','Đ');
			$latinica = array('C','C','S','Z','DJ');

			$ime_k = str_replace($nashalatinica,$latinica,$ime_kandidata);
			$prezime_k = str_replace($nashalatinica,$latinica,$kandidat_prezime);
			
			$kandidat_shortname = ''.$ime_k[0].''.$prezime_k[0].'';
			
			
			
			$kandidat_status_prijave = $row['kandidat_status_prijave'];
			$nalog_naziv = $row['kandidat_prijava_na'];	
			if(!empty($row['kandidat_datum_pocetakrada'])){
				$pocetak_rada_kandidata = date('d.m.Y', strtotime($row['kandidat_datum_pocetakrada']));
			}else{
				$pocetak_rada_kandidata = '-';			
			}
		
			$datum_prijave_kandidata = date('d.m.Y', strtotime($row['kandidat_datetime']));
			$kandidat_partner_status = $row['kandidat_partner_status'];
			
			if($kandidat_partner_status == 1){
				$procenat = 0.8;
			}elseif($kandidat_partner_status == 2){
				$procenat = 0.9;
			}elseif($kandidat_partner_status == 3){
				$procenat = 1;
			}elseif($kandidat_partner_status == 4){
				$procenat = 1.1;
			}elseif($kandidat_partner_status == 5){
				$procenat = 1.25;
			}else{
				$procenat = 0.8;
			}
		
			// STATUS KANDIDATA
			$query_projekti = $db->prepare("
						SELECT status_naziv
						FROM idk_kandidat_status_prijave
						WHERE status_id = :status_id");

			$query_projekti->execute(array(':status_id' => $kandidat_status_prijave));
			$projekti = $query_projekti->fetch();
			$status_naziv = $projekti['status_naziv'];
			if(empty($status_naziv)) $status_naziv = 'U Čekanju';
			
			//TRANSLATE
			if($infodata['lang'] != 'bs'){
				$status_naziv = $gt->translate($status_naziv , ''. $infodata['lang'] .'','bs');
				$nalog_naziv = $gt->translate($nalog_naziv , ''. $infodata['lang'] .'','bs');
			}
			
			// POKUPI KOJEM PROJEKTU PRIPADA KANDIDATI
			$query_kandidat_project = $db->prepare("
						SELECT pk_projectid
						FROM idk_project_kandidati
						WHERE pk_kandidatid = :pk_kandidatid
						ORDER BY pk_id DESC");
						
			$query_kandidat_project->execute(array(
							':pk_kandidatid' => $id
							));
							
			$row_project = $query_kandidat_project->fetch(); 
			
			$projekat = intval($row_project['pk_projectid']);
			if($projekat != 0){
				// POKUPI KOJEM NALOGU PRIPADA KANDIDAT PREKO PROJEKTA 
				$query_kandidat_nalog = $db->prepare("
							SELECT project_nalogid
							FROM idk_projects
							WHERE project_id = :project_id"); 
							
				$query_kandidat_nalog->execute(array(
								':project_id' => $projekat
								));
								
				$row_nalog = $query_kandidat_nalog->fetch();
				
				$nalog = intval($row_nalog['project_nalogid']);
				
				
				if($nalog != 0){
			
				// POKUPI INFORMACIJE O NALOGU
				$query_kandidat_nalog_info = $db->prepare("
							SELECT nalog_partner_provizija
							FROM idk_nalozi
							WHERE nalog_id = :nalog_id");
							
				$query_kandidat_nalog_info->execute(array(
								':nalog_id' => $nalog
								));
							
					$row_provizija = $query_kandidat_nalog_info->fetch();
							
					$provizija = intval($row_provizija['nalog_partner_provizija']);
					
					$info_item = array(
						'id' => $id,
						'ime_kandidata' => $ime_kandidata,
						'kandidat_shortname' => $kandidat_shortname,
						'status_kandidata' => $status_naziv,
						'pocetak_rada_kandidata' => $pocetak_rada_kandidata,
						'datum_prijave_kandidata' => $datum_prijave_kandidata,
						'nalog_naziv' => $nalog_naziv,
						'provizija' => round($provizija * $procenat , 2 ),
					);
						
					
					
					array_push($info_arr["data"], $info_item);
					
		
				} 
			}
		}

		//SVI DIPL
		$query_DIPL = $db->prepare("
					SELECT id_broj_nd_kandidata, ime_nd_kandidata, prezime_nd_kandidata,status_nd_kandidata,  partner_nd_status
					FROM idk_nd_kandidata
					WHERE kandidat_idd = :kandidat_idd AND povijest_nd_kandidata = :povijest_nd_kandidata");
					
		$query_DIPL->execute(array(
						':kandidat_idd' => $partner_id,
						':povijest_nd_kandidata' => 3));

		// MOGUCA ISPLATA
		while($row_dipl = $query_DIPL->fetch()){
			$dipl_price = 25;
			$id_broj_nd_kandidata = $row_dipl["id_broj_nd_kandidata"]; 
			$ime_nd_kandidata = $row_dipl["ime_nd_kandidata"]; 
			$prezime_nd_kandidata = $row_dipl["prezime_nd_kandidata"];	
			$status_nd_kandidata = $row_dipl["status_nd_kandidata"];	
			$partner_nd_status = $row_dipl["partner_nd_status"];
			
			$nashalatinica = array('Č','Ć','Š','Ž','Đ');
			$latinica = array('C','C','S','Z','DJ');

			$ime_k = str_replace($nashalatinica,$latinica,$ime_nd_kandidata);
			$prezime_k = str_replace($nashalatinica,$latinica,$prezime_nd_kandidata);
			
			$kandidat_shortname = ''.$ime_k[0].''.$prezime_k[0].'';

			if($partner_nd_status == 1){
				$dipl_price = 20;
			}elseif($partner_nd_status == 2){
				$dipl_price = 22.5;
			}elseif($partner_nd_status == 3){
				$dipl_price = 25;
			}elseif($partner_nd_status == 4){
				$dipl_price = 25;
			}elseif($partner_nd_status == 5){
				$dipl_price = 25;
			}
			
			

			if($status_nd_kandidata == 1 OR $status_nd_kandidata == 2 OR $status_nd_kandidata == 3 OR $status_nd_kandidata == 4 OR $status_nd_kandidata == 5){
				$dipl_status_naziv = 'U obradi';	
			}elseif($status_nd_kandidata == 6){
				$dipl_status_naziv = 'Završen';	
			}elseif($status_nd_kandidata == 7){
				$dipl_status_naziv = 'Odbijen';	
			}else{
				$dipl_status_naziv = '-';
			}
			
			if($infodata['lang'] == 'bs'){
				$nostrifikacija_diplome = "Nostrifikacija diplome";
			}elseif($infodata['lang'] == 'de'){
				$nostrifikacija_diplome = "Diplomanerkennung";
			}else{
				$nostrifikacija_diplome = "Diploma nostrification";
			}
			
			$info_item = array(
				'id' => utf8_encode( $id_broj_nd_kandidata) ,
				'ime_kandidata' => utf8_encode( $ime_nd_kandidata) ,
				'kandidat_shortname' => utf8_encode( $kandidat_shortname) ,
				'status_kandidata' => utf8_encode( $dipl_status_naziv) ,
				'pocetak_rada_kandidata' => utf8_encode( '-') ,
				'datum_prijave_kandidata' => utf8_encode( '-') ,
				'nalog_naziv' => utf8_encode( $nostrifikacija_diplome) ,
				'provizija' => utf8_encode( $dipl_price) ,
			);	
				
			
			array_push($info_arr["data"], $info_item);
		
		}

		$json = json_encode( $info_arr );
		http_response_code(200);
		echo $json;
	
	break;
	
	
	case "payment_list":
	
	$json = json_decode(file_get_contents('php://input'), true);

	$infodata = array();
	$infodata['token'] = $json['token'];   
	$infodata['lang'] = $json['lang'];   
				

	$partner_id = getPartnerIdFromToken($infodata['token']);
	
	
	//SVI KANDIDATI 
	$query = $db->prepare("
				SELECT *
				FROM idk_kandidati
				WHERE kandidat_partnerid = :kandidat_partnerid"); 
				
	$query->execute(array(
					':kandidat_partnerid' => $partner_id));
					
	$info_arr = array();
	$info_arr["data"] = array();
	
	
	$gt = new gtranslate();
	while($row = $query->fetch()){  
		$id = $row['kandidat_id'];
		$ime_kandidata = $row['kandidat_ime'];
		$kandidat_prezime = $row['kandidat_prezime'];
		
		$nashalatinica = array('Č','Ć','Š','Ž','Đ');
		$latinica = array('C','C','S','Z','DJ');

		$ime_k = str_replace($nashalatinica,$latinica,$ime_kandidata);
		$prezime_k = str_replace($nashalatinica,$latinica,$kandidat_prezime);
		
		$kandidat_shortname = ''.$ime_k[0].''.$prezime_k[0].'';
		
		
		
		$kandidat_status_prijave = $row['kandidat_status_prijave'];
		$nalog_naziv = $row['kandidat_prijava_na'];	
		if(!empty($row['kandidat_datum_pocetakrada'])){
			$pocetak_rada_kandidata = date('d.m.Y', strtotime($row['kandidat_datum_pocetakrada']));
		}else{
			$pocetak_rada_kandidata = '-';			
		}
	
		$datum_prijave_kandidata = date('d.m.Y', strtotime($row['kandidat_datetime']));
		$kandidat_partner_status = $row['kandidat_partner_status'];
		
		if($kandidat_partner_status == 1){
			$procenat = 0.8;
		}elseif($kandidat_partner_status == 2){
			$procenat = 0.9;
		}elseif($kandidat_partner_status == 3){
			$procenat = 1;
		}elseif($kandidat_partner_status == 4){
			$procenat = 1.1;
		}elseif($kandidat_partner_status == 5){
			$procenat = 1.25;
		}else{
			$procenat = 0.8;
		}
	
		// STATUS KANDIDATA
		$query_projekti = $db->prepare("
					SELECT status_naziv
					FROM idk_kandidat_status_prijave
					WHERE status_id = :status_id");

		$query_projekti->execute(array(':status_id' => $kandidat_status_prijave));
		$projekti = $query_projekti->fetch();
		$status_naziv = $projekti['status_naziv'];
		if(empty($status_naziv)) $status_naziv = 'U Čekanju';
		
		//TRANSLATE
		if($infodata['lang'] != 'bs'){
			$status_naziv = $gt->translate($status_naziv , ''. $infodata['lang'] .'','bs');
			$nalog_naziv = $gt->translate($nalog_naziv , ''. $infodata['lang'] .'','bs');
		}
		
		// POKUPI KOJEM PROJEKTU PRIPADA KANDIDATI
		$query_kandidat_project = $db->prepare("
					SELECT pk_projectid
					FROM idk_project_kandidati
					WHERE pk_kandidatid = :pk_kandidatid");
					
		$query_kandidat_project->execute(array(
						':pk_kandidatid' => $id
						));
						
		$row_project = $query_kandidat_project->fetch(); 
		
		$projekat = intval($row_project['pk_projectid']);
		if($projekat != 0){
			// POKUPI KOJEM NALOGU PRIPADA KANDIDAT PREKO PROJEKTA 
			$query_kandidat_nalog = $db->prepare("
						SELECT project_nalogid
						FROM idk_projects
						WHERE project_id = :project_id"); 
						
			$query_kandidat_nalog->execute(array(
							':project_id' => $projekat
							));
							
			$row_nalog = $query_kandidat_nalog->fetch();
			
			$nalog = intval($row_nalog['project_nalogid']);
			
			
			if($nalog != 0){
		
			// POKUPI INFORMACIJE O NALOGU
			$query_kandidat_nalog_info = $db->prepare("
						SELECT nalog_partner_provizija
						FROM idk_nalozi
						WHERE nalog_id = :nalog_id");
						
			$query_kandidat_nalog_info->execute(array(
							':nalog_id' => $nalog
							));
						
				$row_provizija = $query_kandidat_nalog_info->fetch();
						
				$provizija = intval($row_provizija['nalog_partner_provizija']);
				
				
				// AKO JE NALOG PLACEN I U STATUSU POCETAK RADA ONDA DODAJ PROVIZIJU-ISPLACENO
				$query_isplacen_nalog_isplaceno = $db->prepare("
							SELECT kf_id
							FROM idk_kandidat_financije
							WHERE kandidat_id = :kandidat_id AND kf_placeno = :kf_placeno AND kf_type = :kf_type");
							
				$query_isplacen_nalog_isplaceno->execute(array(
								':kandidat_id' => $id,
								':kf_placeno' => 1,
								':kf_type' => 2
								));
								
				$isplacen_nalog_isplaceno = $query_isplacen_nalog_isplaceno->rowCount();
				
				if($isplacen_nalog_isplaceno > 0){
					$info_item = array(
						'id' => $id,
						'ime_kandidata' => $ime_kandidata,
						'kandidat_shortname' => $kandidat_shortname,
						'status_kandidata' => $status_naziv,
						'pocetak_rada_kandidata' => $pocetak_rada_kandidata,
						'datum_prijave_kandidata' => $datum_prijave_kandidata,
						'nalog_naziv' => $nalog_naziv,
						'provizija' => $provizija * $procenat,
					);
						
					
					
					array_push($info_arr["data"], $info_item);
				}						
			} 
		}
	}

	//SVI DIPL
	$query_DIPL = $db->prepare("
				SELECT id_broj_nd_kandidata, ime_nd_kandidata, prezime_nd_kandidata, partner_nd_status, rata1_nd_kandidata, rata2_nd_kandidata
				FROM idk_nd_kandidata
				WHERE kandidat_idd = :kandidat_idd AND povijest_nd_kandidata = :povijest_nd_kandidata");
				
	$query_DIPL->execute(array(
					':kandidat_idd' => $partner_id,
					':povijest_nd_kandidata' => 3));

	// MOGUCA ISPLATA
	while($row_dipl = $query_DIPL->fetch()){
		$dipl_price = 25;
		$id_broj_nd_kandidata = $row_dipl["id_broj_nd_kandidata"]; 
		$ime_nd_kandidata = $row_dipl["ime_nd_kandidata"]; 
		$prezime_nd_kandidata = $row_dipl["prezime_nd_kandidata"];	
		$rata1_nd_kandidata = $row_dipl["rata1_nd_kandidata"];
		$rata2_nd_kandidata = $row_dipl["rata2_nd_kandidata"];
		$partner_nd_status = $row_dipl["partner_nd_status"];
		
		$nashalatinica = array('Č','Ć','Š','Ž','Đ');
		$latinica = array('C','C','S','Z','DJ');

		$ime_k = str_replace($nashalatinica,$latinica,$ime_nd_kandidata);
		$prezime_k = str_replace($nashalatinica,$latinica,$prezime_nd_kandidata);
		
		$kandidat_shortname = ''.$ime_k[0].''.$prezime_k[0].'';

		if($partner_nd_status == 1){
			$dipl_price = 20;
		}elseif($partner_nd_status == 2){
			$dipl_price = 22.5;
		}elseif($partner_nd_status == 3){
			$dipl_price = 25;
		}elseif($partner_nd_status == 4){
			$dipl_price = 25;
		}elseif($partner_nd_status == 5){
			$dipl_price = 25;
		}
		
		if($rata1_nd_kandidata == 1 AND $rata2_nd_kandidata == 1){
			$dipl_status_naziv = "Isplaćeno";
			$nostrifikacija_diplome = "Nostrifikacija diplome";
			//TRANSLATE
			if($infodata['lang'] != 'bs'){
				$dipl_status_naziv = $gt->translate($dipl_status_naziv , ''. $infodata['lang'] .'','bs');
				$dipl_nostrifikacija_diplome = $gt->translate($nostrifikacija_diplome , ''. $infodata['lang'] .'','bs');
			}		
			
			$info_item = array(
				'id' => $id_broj_nd_kandidata,
				'ime_kandidata' => $ime_nd_kandidata,
				'kandidat_shortname' => $kandidat_shortname,
				'status_kandidata' => $dipl_status_naziv,
				'pocetak_rada_kandidata' => '-',
				'datum_prijave_kandidata' => '-',
				'nalog_naziv' => $nostrifikacija_diplome,
				'provizija' => $dipl_price,
			);	
			
			array_push($info_arr["data"], $info_item);
		}
	
	}
	
	
	
	http_response_code(200);
				
	echo json_encode($info_arr);
	
	break;
	
	
	
	
	
	
	case "partner_list":

	$json = json_decode(file_get_contents('php://input'), true);

	$infodata = array();
	$infodata['jp_id'] = $json['jp_id'];    
					

	//MY PARTNERS
	$query = $db->prepare("
				SELECT jp_id, jp_imeprezime, jp_position
				FROM idk_jobstep_partners
				WHERE jp_preporuka_id = :jp_preporuka_id"); 
				
	$query->execute(array(
					':jp_preporuka_id' => intval($infodata['jp_id'])));
					
	$info_arr = array();
	$info_arr["data"] = array();


	while($row = $query->fetch()){  

		$jp_id = $row['jp_id'];
		$jp_imeprezime = explode(" ", $row['jp_imeprezime']);
		
		$nashalatinica = array('Č','Ć','Š','Ž','Đ');
		$latinica = array('C','C','S','Z','DJ');


		$ime_partnera = str_replace($nashalatinica,$latinica,$jp_imeprezime[0]);
		$partner_prezime = str_replace($nashalatinica,$latinica,$jp_imeprezime[1]);
		
		$partner_shortname = '' . $ime_partnera[0] .'' . $partner_prezime[0] .'';
		
  
		$jp_position = intval($row['jp_position']);
				
		if($jp_position == 1){
			$position = "Junior Partner";
		}elseif($jp_position == 2){
			$position = "Partner";
		}elseif($jp_position == 3){
			$position = "Senior Partner";
		}elseif($jp_position == 4){
			$position = "Agency";
		}elseif($jp_position == 5){
			$position = "Superearner";
		}else{
			$position = "Junior Partner";
		}
		 
		$info_item = array(
			'id' => $jp_id,
			'ime_partnera' => $ime_partnera,
			'partner_shortname' => $partner_shortname,
			'position' => $position
		);
			 
		
		array_push($info_arr["data"], $info_item);
				
	}
	http_response_code(200);
				
	echo json_encode($info_arr);

	break;
	
	case "partner_promotions": 
		
		$json = json_decode(file_get_contents('php://input'), true);

		$infodata = array();
		$infodata['jp_id'] = $json['jp_id']; 
		
		$info_arr = array();
		$info_arr["data"] = array();
		
		$check_query = $db->prepare("
			SELECT *
			FROM idk_jobstep_partners
			WHERE jp_id = :jp_id");
			
		$check_query->execute(array(
					':jp_id' => $infodata['jp_id']));
		
					
		$user = $check_query->fetch();
		
		$register_date = $user["jp_register_date"];
		$jp_position = $user["jp_position"];
	
		$today = date("Y-m-d H:i:s");
		
		$date_count = strtotime($today) - strtotime($register_date);
		$days = floor($date_count/86400);
		
		// JUNIOR U PARTNERA
		$junior_days_percentage = ($days/90)*100;
		
		// DANI
		if($junior_days_percentage < 100){
			$junior_days_percentage = ($days/90)*100;
			$junior_missing_days = 90 - $days;
		}else{
			$junior_days_percentage = 100; 
			$junior_missing_days = 0;
		}
		//PARTNERI
		$partner_query = $db->prepare("
			SELECT *
			FROM idk_jobstep_partners
			WHERE jp_preporuka_id = :jp_preporuka_id");
			
		$partner_query->execute(array(
					':jp_preporuka_id' => $infodata['jp_id']));
		
					
		$partner_counter = $partner_query->rowCount();
		
		if($partner_counter <= 5){
			$junior_partner_percentage = ($partner_counter/5)*100;  
			$junior_missing_partners = 5 - $partner_counter;
		}else{
			$junior_partner_percentage = 100;  
			$junior_missing_partners = 0;
		}
		
		$full_junior_percentage = ($junior_partner_percentage + $junior_days_percentage)/2;
		$full_junior_percentage = round($full_junior_percentage);
		
		// DANI U SENIORA
		$senior_days_percentage = ($days/180)*100;
		if($senior_days_percentage < 100){ 
			$senior_days_percentage = ($days/180)*100; 
			$senior_missing_days = 180 - $days;
		}else{
			$senior_days_percentage = 100; 
			$senior_missing_days = 0;
		}
		// ZAPOSLENIK U SENIORA
		$partner_query = $db->prepare("
			SELECT *
			FROM idk_kandidati
			WHERE kandidat_partnerid = :kandidat_partnerid AND kandidat_status_prijave = :kandidat_status_prijave");
			
		$partner_query->execute(array(
					':kandidat_partnerid' => $infodata['jp_id'],
					':kandidat_status_prijave' => 4));
					
		$partner_counter = $partner_query->rowCount();
		if($partner_counter > 0){
			$partner_percentage = 100;
			$senior_missing_zaposlenik = 0;
		}else{
			$partner_percentage = 0;
			$senior_missing_zaposlenik = 1;
		}
		
		$full_senior_percentage = ($partner_percentage + $senior_days_percentage)/2;
		$full_senior_percentage = round($full_senior_percentage);
		
		if($full_senior_percentage > 99){ $completed_senior_percentage = '100'; }else{	 $completed_senior_percentage = '0'; }
		if($jp_position == '3'){ $completed_senior_percentage = '100'; }
			
		$info_item = array(
			'full_junior_percentage' => $full_junior_percentage,
			'full_senior_percentage' => $full_senior_percentage,
			'junior_missing_partners' => $junior_missing_partners,
			'junior_missing_days' => $junior_missing_days,
			'senior_missing_zaposlenik' => $senior_missing_zaposlenik,
			'senior_missing_days' => $senior_missing_days,
			'completed_senior_percentage' => $completed_senior_percentage
		);
		
 
		array_push($info_arr["data"], $info_item);
		http_response_code(200);
		
		echo json_encode($info_arr); 
	
	
	break;
	case "pageinfo": 
		$query_kandidati = $db->prepare("
			SELECT kandidat_id
			FROM idk_kandidati WHERE kandidat_status != 3");
			
		$query_kandidati->execute();
		$broj_kandidata = $query_kandidati->rowCount();
		
		$query = $db->prepare("
				SELECT company_id
				FROM idk_companies
				WHERE company_status != 0 AND company_contact_type = 'Klijent'");

		$query->execute();
		$broj_poslodavaca = $query->rowCount();
		
		$info_arr = array();
		$info_arr["data"] = array();
		
		$info_item = array(
			'broj_kandidata' => $broj_kandidata,
			'broj_poslodavaca' => $broj_poslodavaca,
		);
		
 
		array_push($info_arr["data"], $info_item);
		http_response_code(200);
		
		echo json_encode($info_arr); 
	
	
	break;
	
	case "contactus": 
	
		$json = json_decode(file_get_contents('php://input'), true);

		$infodata = array();
		$infodata['jp_imeprezime'] = $json['jp_imeprezime']; 
		$infodata['jp_email'] = $json['jp_email']; 
		$infodata['textContactUs'] = $json['textContactUs']; 
		
		
		
		$body = '
			<!DOCTYPE html><html><head><title>Novi upit Jobstep Partnera</title><meta http-equiv="content-type" content="text/html; charset=utf-8" ><meta name="viewport" content="width=device-width, initial-scale=1.0"><style type="text/css">body {/*background: linear-gradient(90deg, white, gray);*/background-color: #62969D;}body, h1, p {font-family: "Helvetica Neue", "Segoe UI", Segoe, Helvetica, Arial, "Lucida Grande", sans-serif;font-weight: normal;margin: 0;padding: 0;text-align: center;color: #fff;}.container {margin-left: auto;margin-right: auto;margin-top: 177px;max-width: 1170px;padding-right: 15px;padding-left: 15px;}.row:before, .row:after {display: table;content: " ";}h1 {font-size: 48px;font-weight: 300;margin: 0 0 20px 0;}.lead {font-size: 21px;font-weight: 200;margin-bottom: 20px;}p {margin: 0 0 10px;}a {color: #eee;text-decoration: none;}</style></head><body><div class="container text-center" id="error"><svg height="100" width="100"><circle cx="50" cy="50" r="31" stroke="#fff" stroke-width="9.5" fill="none" /><circle cx="50" cy="50" r="6" stroke="#fff" stroke-width="1" fill="#fff" /><line x1="50" y1="50" x2="35" y2="50" style="stroke:#fff;stroke-width:6" /><line x1="65" y1="35" x2="50" y2="50" style="stroke:#fff;stroke-width:6" /><path d="M59 65 L83 65 L75 87 Z" fill="#fff" /><rect width="20" height="9" x="70" y="56" style="fill:#fff;stroke-width:0;" /></svg><div class="row"><div class="col-md-12"><div class="main-icon" style="color: #fff;"><span class="uxicon uxicon-clock-refresh"></span></div><h1>Partner: ' . $infodata['jp_imeprezime'] .'</h1><p class="lead">Imate novi upit od Jobstep Partnera.</p><p class="lead">Email: ' . $infodata['jp_email'] . '</p><p class="lead">Poruka: ' . $infodata['textContactUs'] .' </a></p></div></div></div></body></html>
		';

		$mail = new PHPMailer;
		$mail->isSMTP();											// Set mailer to use SMTP
		$mail->Host = 'smtp.gmail.com';  			// Specify main and backup SMTP servers
		$mail->SMTPAuth = true;						// Enable SMTP authentication
		$mail->Username = 'support@job-step.com';	// SMTP username
		$mail->Password = 'eooc nnxo aylp lqkh';		// SMTP password
		$mail->SMTPSecure = 'ssl';					// Enable TLS encryption, `ssl` also accepted
		$mail->Port = 465;							// TCP port to connect to
		$mail->CharSet = 'UTF-8';
		
		$mail->setFrom($infodata['jp_email'], 'JobStep Partner ' . $infodata['jp_imeprezime'] .'');
		$mail->addAddress('support@job-step.com');		// Add a recipient
		$mail->addAddress('e.bender@job-step.com');		// Add a recipient


		$mail->Subject = "JobStep Partner - Novi upit";
		$mail->Body    = $body;
		$mail->AltBody = "Novi upit";

		if(!$mail->send()) {
			echo json_encode('false');
		}else{
			echo json_encode('true');
		}
	
	break;
	
	
	case "setphonenumber": 
	
		$json = json_decode(file_get_contents('php://input'), true);

		$infodata = array();		
		$infodata['token'] = $json['token'];   
		$infodata['jp_brtelefona'] = $json['jp_brtelefona'];   
		$infodata['jp_drzava'] = $json['jp_drzava'];   
				
		
		//Save
		$query = $db->prepare("
			UPDATE idk_jobstep_partners
			SET	jp_brtelefona = :jp_brtelefona, jp_drzava = :jp_drzava
			WHERE jp_mailconfirmation_token = :jp_mailconfirmation_token");

		$query->execute(array(
				':jp_brtelefona' => $infodata['jp_brtelefona'],
				':jp_drzava' => $infodata['jp_drzava'],		
				':jp_mailconfirmation_token' => $infodata['token']));
				
		echo json_encode('true');
	
	break;
	
	
	
	}
}



?>