<?php
exit();
/*********************************************************
	CRON OPERACIJE:	
		****** PROMOCIJA
		--------- PROMOCIJA PARTNERA KROZ STATUSE I PUSHNOTIFIKACIJE ZA ISTO
		--------- SLANJE MAILA ZA PROMOCIJU
		****** INFOBIP
		--------- SLANJE INFOBIM KANALIMA LOGIN INFORMACIJA
		--------- META SLANJA SU PARTNERI KOJI SU NASTALI RUČNO MARKETING KAMPANJOM
		
		
	ZADNJI AUTOR: ISMAIL SULJIC
	DATUM ZADNJE IZMJENE: 07.04.2021
	
	*****
	Dodane funkcije:	
						congratulationPromote 
						send_notification_partnerapp 
						send_partner_promote_email 
						getPartnerPhoneNoREGX 
						getLoginData
						sendLoginInfoPartner
*********************************************************/


		//Error log enabled
		error_reporting(E_ALL);
		ini_set('display_errors', 1);
		
		include("../includes/connect.php");
		//PHPMailer
		require '../mail/Exception.php';
		require '../mail/PHPMailer.php';
		require '../mail/SMTP.php';
		use PHPMailer\PHPMailer\PHPMailer;
		use PHPMailer\PHPMailer\Exception;
		
		
		
		$query = $db->prepare("
			SELECT jp_id, jp_imeprezime, jp_email, jp_brtelefona, jp_social_imgurl, jp_provider, jp_drzava, jp_position, jp_register_date, jp_fcmtoken
			FROM  idk_jobstep_partners
			WHERE jp_confirmedaccount = 1
			ORDER BY jp_id DESC");

		$query->execute();
		
		$trenutno_vrijeme = date('Y-m-d H:i:s',time());	

		while($row = $query->fetch()){

			$jp_id = $row['jp_id'];
			$jp_imeprezime = $row['jp_imeprezime'];
			$jp_email = $row['jp_email'];
			$jp_brtelefona = $row['jp_brtelefona'];
			$jp_provider = $row['jp_provider'];
			$jp_drzava = $row['jp_drzava'];
			$jp_fcmtoken = $row['jp_fcmtoken'];
			
			
			
			$jp_register_date = date('Y-m-d', strtotime($row['jp_register_date']));
			
			
			$jp_promote_junior_days = date('Y-m-d', strtotime($row["jp_register_date"] . " +90 days"));	 // BROJ DANA 90 - (90 DANA ZA PROMOCIJU PARTNERA IZ JUNIOR U PARTNER(90%))
			
			
			$jp_promote_partner_days = date('Y-m-d', strtotime($row["jp_register_date"] . " +180 days"));	// BROJ DANA 180 - (180 DANA ZA PROMOCIJU PARTNERA IZ PARTNER(90%) U SENIOR(100%))

			/*
			echo $jp_id;
			echo ' ------ ';
			echo $jp_imeprezime;
			echo ' ------ <br />';
			echo $jp_register_date;
			echo ' ------ ';
			echo $jp_promote_junior_days;
			echo ' ------ ';
			echo $jp_promote_partner_days;
			echo '<br />'; 
			*/

			
			

			if($row['jp_position'] == 1){
				$jp_position = "Junior";
				//Preporuceni partneri
				$partner_query = $db->prepare("
					SELECT jp_id
					FROM idk_jobstep_partners
					WHERE jp_preporuka_id = :jp_preporuka_id");
					
				$partner_query->execute(array(
							':jp_preporuka_id' => $jp_id));
				$partner_counter = $partner_query->rowCount();
				
			

				if($partner_counter >= 5 AND $trenutno_vrijeme >= $jp_promote_junior_days ){
					$notifikacija_title = "Jobstep Partner";
					$notifikacija_text = "You've Been Promoted";
					$jp_position_new = 2;
					congratulationPromote($jp_id , $jp_position_new, $jp_position,  $jp_email, $jp_fcmtoken, $notifikacija_title, $notifikacija_text);
				}
				
				
				
			}elseif($row['jp_position'] == 2){
				$jp_position = "Partner";
				//Zaposleni kandidati
				$query_zaposlenih = $db->prepare("
							SELECT kandidat_id
							FROM idk_kandidati
							WHERE kandidat_partnerid = :kandidat_partnerid AND kandidat_status_prijave = :kandidat_status_prijave");
							
				$query_zaposlenih->execute(array(
								':kandidat_partnerid' => $jp_id,
								':kandidat_status_prijave' => 4));  
				
				$ukupan_broj_kandidata_zaposlenih = $query_zaposlenih->rowCount();
				
				
				if($ukupan_broj_kandidata_zaposlenih >= 1 AND $trenutno_vrijeme >= $jp_promote_partner_days ){
					$notifikacija_title = "Jobstep Partner";
					$notifikacija_text = "You've Been Promoted";
					$jp_position_new = 3;
					congratulationPromote($jp_id , $jp_position_new,  $jp_email, $jp_fcmtoken, $notifikacija_title, $notifikacija_text);
				}
				
				
			}


		}
		
		
	function congratulationPromote($jp_id , $jp_position_new , $jp_email, $jp_fcmtoken, $notifikacija_title, $notifikacija_text){
		//Update( Query + Pushnotifikacija + Email)
		Global $db;
		
		$query = $db->prepare("
			UPDATE idk_jobstep_partners
			SET	jp_position = :jp_position
			WHERE jp_id = :jp_id");

		$query->execute(array(
			':jp_position' => $jp_position_new,
			':jp_id' => $jp_id));
		
		send_partner_promote_email($jp_email);
		$result = send_notification_partnerapp($jp_fcmtoken, $notifikacija_title, $notifikacija_text);
		/*
		$sms_data_object = json_decode($result, true);		
		echo '<pre>';
		print_r ($result);
		echo '</pre>'; 
		*/
		echo 'Update';
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
	
	function send_partner_promote_email($jp_email){
					$body = '
					<!DOCTYPE html><html><head><title>You`ve Been Promoted</title><meta http-equiv="content-type" content="text/html; charset=utf-8" ><meta name="viewport" content="width=device-width, initial-scale=1.0"><style type="text/css">body {/*background: linear-gradient(90deg, white, gray);*/background-color: #62969D;}body, h1, p {font-family: "Helvetica Neue", "Segoe UI", Segoe, Helvetica, Arial, "Lucida Grande", sans-serif;font-weight: normal;margin: 0;padding: 0;text-align: center;color: #fff;}.container {margin-left: auto;margin-right: auto;margin-top: 177px;max-width: 1170px;padding-right: 15px;padding-left: 15px;}.row:before, .row:after {display: table;content: " ";}h1 {font-size: 48px;font-weight: 300;margin: 0 0 20px 0;}.lead {font-size: 21px;font-weight: 200;margin-bottom: 20px;}p {margin: 0 0 10px;}a {color: #eee;text-decoration: none;}</style></head><body><div class="container text-center" id="error"><svg height="100" width="100"><circle cx="50" cy="50" r="31" stroke="#fff" stroke-width="9.5" fill="none" /><circle cx="50" cy="50" r="6" stroke="#fff" stroke-width="1" fill="#fff" /><line x1="50" y1="50" x2="35" y2="50" style="stroke:#fff;stroke-width:6" /><line x1="65" y1="35" x2="50" y2="50" style="stroke:#fff;stroke-width:6" /><path d="M59 65 L83 65 L75 87 Z" fill="#fff" /><rect width="20" height="9" x="70" y="56" style="fill:#fff;stroke-width:0;" /></svg><div class="row"><div class="col-md-12"><div class="main-icon" style="color: #fff;"><span class="uxicon uxicon-clock-refresh"></span></div><h1>You`ve Been Promoted.</h1><p class="lead">Now you can share with your new Jobstep Partner provision.</p><p class="lead">For more info you can contact us <a href="https://job-step.net">Jobstep International d.o.o. </a></p></div></div></div></body></html>
					';

			//$mail->isSMTP();											// Set mailer to use SMTP
			$mail = new PHPMailer;
			$mail->Host = 'mail.idkcrm.com;mail.idkcrm.com';  // Specify main and backup SMTP servers
			$mail->SMTPAuth = true;                               // Enable SMTP authentication
			$mail->Username = 'noreply@idkcrm.com';                 // SMTP username
			$mail->Password = 'hhWno3RW@2';                           // SMTP password
			$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
			$mail->Port = 465;                                    // TCP port to connect to
			$mail->CharSet = 'UTF-8';

			$mail->setFrom('no-reply@jobstep-app.com', 'Jobstep Partner');     		// Add a recipient
			$mail->addAddress($jp_email, $jp_email);		// Add a recipient


			$mail->Subject = "Jobstep Partner - Promoted";
			$mail->Body    = $body;
			$mail->AltBody = "You've Been Promoted";

			if(!$mail->send()) {
				echo 'Message could not be sent.';
				echo 'Mailer Error: ' . $mail->ErrorInfo;
			}
	}
	
	
	
/*****************************************************
*	PARTNER MARKETING - INFOBIP 
*****************************************************/


	function getPartnerPhoneNoREGX($jp_id){ // VRATI BROJ TELEFONA KANDIDATA BEZ "+" IZ DIPL TABELE
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

	function getLoginData($jp_id){ // GET EMAIL/USERNAME I GENERIŠI NOVU ŠIFRU 
		global $db;
		
		$query = $db->prepare("SELECT jp_email, jp_imeprezime
							   FROM idk_jobstep_partners
							   WHERE jp_id = :jp_id");
		$query->execute(array( ":jp_id" => $jp_id ));
		
		if($query->rowCount() > 0){
			$row = $query->fetch();
			
				$jp_imeprezime = explode(" ",$row['jp_imeprezime']);
				$jp_ime = $jp_imeprezime[0];
				$jp_password = $jp_ime."$!".bin2hex(openssl_random_pseudo_bytes(2)); // kreacija nove šifre
				$array = array(
					"nickname" => $row['jp_email'],
					"password" => $jp_password
				);
				return $array;
			
			
		}else return false;
	}

	function sendLoginInfoPartner($jp_id){
		$curl = curl_init();
		
		if($kontakt_podaci = getLoginData($jp_id)){
				/****************************
					PARAMETRI PORUKE
				****************************/
				// echo $kontakt_podaci["nickname"]." ".$kontakt_podaci["password"]."<br>";
				$broj = getPartnerPhoneNoREGX($jp_id);
				
				// $broj = "0603358470";
				// echo "broj: ".$broj."<br>";
			
				$pozivni = substr(trim($broj," "),0,3);
				if($pozivni == "381")	$link = "https://job-step.net/partners/jobstep-partner-app/6";
					else  $link = "https://job-step.net/partners/jobstep-partner-app/6";
					
				
				$imageURL = "https://crm.job-step.com/partner.png";
				$buttonText = "Saznaj više";
				$buttonURL = $link;

				if($pozivni=="387"){
					$textViber = "Poštovani,
					Jobstep International tim Vam se srdačno zahvaljuje na Vašoj preporuci kandidata. 😊 \n 
					Instalirajte PartnerApp i pratite status Vašeg kandidata!  \n
					Ovo su Vaši podaci za prijavu: \nKorisnicko ime: ".$kontakt_podaci['nickname']."\n Lozinka : ".$kontakt_podaci['password']."\n
					Više informacija na: ".$link;
					
					$textSMS = "Poštovani,
					Jobstep International tim Vam se srdačno zahvaljuje na Vašoj preporuci kandidata. 😊 \n 
					Instalirajte PartnerApp i pratite status Vašeg kandidata!  \n
					Ovo su Vaši podaci za prijavu: \n Korisnicko ime: ".$kontakt_podaci['nickname']." \n Lozinka : ".$kontakt_podaci['password']."\n
					KViše informacija na: ".$link;
				}else{
					$textViber = "Poštovani,
					Jobstep International tim Vam se srdačno zahvaljuje na Vašoj preporuci kandidata. 😊  
					Ovo su Vaši kontakt podaci: Korisnicko ime: ".$kontakt_podaci['nickname']." | Lozinka : ".$kontakt_podaci['password']."
					Više informacija na: ".$link;
					
					$textSMS = "Poštovani,
					Jobstep International tim Vam se srdačno zahvaljuje na Vašoj preporuci kandidata. 😊  
					Ovo su Vaši kontakt podaci: Korisnicko ime: ".$kontakt_podaci['nickname']." | Lozinka : ".$kontakt_podaci['password']."
					Više informacija na: ".$link;
				}
				
				/****************************
							PARAMETRI KRAJ
				****************************/

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
				
				if ($err) {
				  echo "cURL Error #:" . $err;
				} else {
				  echo $response;
				}
				
				curl_close($curl);
		}
	}
	
	/****************************************
	// START CHECK PROCESS FOR PARTNER TABLE 
	/****************************************/
	
	$partner_query = $db->prepare("
									SELECT DISTINCT jp_id
									FROM idk_jobstep_partners p
									INNER JOIN idk_kandidati k ON p.jp_id = k.kandidat_partnerid
									WHERE jp_source = :jp_source AND jp_confirmedaccount = :jp_confirmedaccount
								");
	$partner_query->execute(array(
							":jp_source" => 1,
							":jp_confirmedaccount" => 0
							)); 		
	while($partner_row = $partner_query->fetch()){
			$jp_id = $partner_row['jp_id'];
			$kontatkt_podaci = getLoginData($jp_id);
			
			/***********************************
			*  UPDATE QUERY & SEND INFO
			************************************/
			$update_partner_query = $db->prepare("
									UPDATE idk_jobstep_partners
									SET jp_confirmedaccount = :jp_confirmedaccount, jp_password = :jp_password
									WHERE jp_id = :jp_id
									");
			$update_partner_query -> execute(array(
					":jp_confirmedaccount" => 1,
					":jp_password" => md5($kontatkt_podaci['password']), // prvo sam morao unijeti lozinku u bazu neheširanu, tako da sad heširam
					":jp_id" => $jp_id
					));
			
			sendLoginInfoPartner($jp_id); // SEND LOGIN DATA
	}	
	
?>