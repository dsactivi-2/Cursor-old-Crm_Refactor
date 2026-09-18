<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//Connect to db
ob_start();
include("../includes/connect.php");


  		//MY PARTNERS
	$query = $db->prepare("
				SELECT jp_id, jp_imeprezime,jp_lang, jp_fcmtoken , jp_email, jp_brtelefona,  jp_register_date
				FROM idk_jobstep_partners
				WHERE jp_confirmedaccount = 1 AND jp_id > 936
				ORDER BY jp_id DESC"); 
				
	$query->execute();
					
	$i = 0;
	while($row = $query->fetch()){  

		$jp_id = $row['jp_id'];
		$jp_lang = $row['jp_lang'];
		$jp_imeprezime = $row['jp_imeprezime'];
		$jp_fcmtoken = $row['jp_fcmtoken'];
		
		$jp_email = $row['jp_email'];
		$jp_brtelefona = $row['jp_brtelefona'];		
		$jp_register_date = date('H:i d.m.Y', strtotime($row['jp_register_date']));

						
		echo $jp_id;
		echo ' ------ '; 
		echo $jp_imeprezime;
		echo ' ------ ';
		echo $jp_email;
		echo ' ------ ';
		echo $jp_brtelefona;
		echo ' ------ ';
		echo $jp_register_date;
		echo '<br />'; 
		
		
		if($jp_lang == 'bs'){
			$notifikacija_title = "Jobstep Partner";
			$notifikacija_text = "Nastavite dijeliti oglase kako bi što prije došli do provizije od Vaših zaposlenih kandidata.";
		 
		}elseif($jp_lang == 'en'){
			$notifikacija_title = "Jobstep Partner";
			$notifikacija_text = "Keep sharing job postings, to get a commission from your recruited candidates as soon as possible.";
				
		}else{
			$notifikacija_title = "Jobstep Partner";
			$notifikacija_text = "Teilen Sie auch weiterhin Stellenanzeigen, um so schnell wie möglich eine Provision der Ihrerseits rekrutierten Kandidaten zu erhalten.";

		}
		
		
		$result = send_notification_partnerapp($jp_fcmtoken, $notifikacija_title, $notifikacija_text);
		$sms_data_object = json_decode($result, true);
				
		echo '<pre>';
		print_r ($result);
		echo '</pre>'; 
		echo '<br />'; 
		
		if( intval($sms_data_object["success"]) == 1){
			
			
		}else{
			
			$log_query = $db->prepare("
					INSERT INTO idk_partner_pushnot
						(idk_partnerid, idk_partner_imeprezime, idk_partner_telefon, idk_partner_email, idk_datum_registracije)
					VALUES
						(:idk_partnerid, :idk_partner_imeprezime, :idk_partner_telefon, :idk_partner_email, :idk_datum_registracije)");

			$log_query->execute(array(
							':idk_partnerid' => $jp_id,
							':idk_partner_imeprezime' => $jp_imeprezime,
							':idk_partner_telefon' => $jp_brtelefona,
							':idk_partner_email' => $jp_email,
							':idk_datum_registracije' => $jp_register_date));
			
		}
		
	
	}
	
	
	
		/*
		//BROJ PREGLEDA
		$query_pregled = $db->prepare("
				SELECT SUM(jpp_pregledi_count) as jpp_pregledi_count
				FROM idk_jobstep_partners_pregledi
				WHERE jpp_partnerid = :jpp_partnerid");
					
		$query_pregled->execute(array(
						':jpp_partnerid' => $jp_id));  
		$row = $query_pregled->fetch();
		$jpp_pregledi_count = intval($row['jpp_pregledi_count']);


				// PK
		$partner_query = $db->prepare("
			SELECT kandidat_id
			FROM idk_kandidati
			WHERE kandidat_partnerid = :kandidat_partnerid AND kandidat_id >= 11000");
			
		$partner_query->execute(array(':kandidat_partnerid' => $jp_id));
		$partner_counter = $partner_query->rowCount();

													
			//SVI DIPL
		$query_DIPL = $db->prepare("
					SELECT id_broj_nd_kandidata
					FROM idk_nd_kandidata
					WHERE kandidat_idd = :kandidat_idd AND povijest_nd_kandidata = :povijest_nd_kandidata");
					
		$query_DIPL->execute(array(
						':kandidat_idd' => $jp_id,
						':povijest_nd_kandidata' => 3));
		$query_DIPL_counter = $query_DIPL->rowCount();


		$i++;
		echo '<br />'; 		
		echo '  '.$jp_id.' '; 			
		echo $jp_imeprezime;
		echo ' ------ ';
		echo $jpp_pregledi_count;
		echo ' --';
		echo $partner_counter;
		echo ' --';
		echo $query_DIPL_counter;
		echo '<br />'; 
		echo '<br />'; 
		*/
		
 
 
	/*
 		//MY PARTNERS
	$query = $db->prepare("
				SELECT jp_id, jp_imeprezime,jp_lang, jp_fcmtoken
				FROM idk_jobstep_partners
				WHERE jp_brtelefona = '+387'  OR jp_brtelefona = '+385'  OR jp_brtelefona = '+49'    OR jp_brtelefona = '+680'    OR jp_brtelefona = '+381'   OR jp_brtelefona = '+45'    "); 
				
	$query->execute();
					

	while($row = $query->fetch()){  

		$jp_id = $row['jp_id'];
		$jp_lang = $row['jp_lang'];
		$jp_imeprezime = $row['jp_imeprezime'];
		$jp_fcmtoken = $row['jp_fcmtoken'];
		
		echo $jp_imeprezime;
		echo '------';
		echo '<br />'; 
		
		if($jp_lang == 'bs'){
			$notifikacija_title = "Informacije o profilu";
			$notifikacija_text = "Ažurirajte informacije o svom profilu radi lakšeg kontaktiranja i isplate.";
			
				
			$result = send_notification_partnerapp($jp_fcmtoken, $notifikacija_title, $notifikacija_text);
			
			
			
			$sms_data_object = json_decode($result, true);
			 echo '<pre>';
			 print_r ($result);
			 echo '</pre>'; 
			 echo '<br />'; 
			 echo '<br />'; 
		 
		}elseif($jp_lang == 'en'){
			$notifikacija_title = "Profile information";
			$notifikacija_text = "Update your profile information for easier contact and payment.";
			
				
			$result = send_notification_partnerapp($jp_fcmtoken, $notifikacija_title, $notifikacija_text);
			
			
			
			$sms_data_object = json_decode($result, true);
			 echo '<pre>';
			 print_r ($result);
			 echo '</pre>'; 
			 echo '<br />'; 
			 echo '<br />'; 
		}else{
			$notifikacija_title = "Profile information";
			$notifikacija_text = "Update your profile information for easier contact and payment.";
			
				
			$result = send_notification_partnerapp($jp_fcmtoken, $notifikacija_title, $notifikacija_text);
			
			
			
			$sms_data_object = json_decode($result, true);
			 echo '<pre>';
			 print_r ($result);
			 echo '</pre>'; 
			 echo '<br />'; 
			 echo '<br />'; 
		}
		
		
			//Save
			$query_update = $db->prepare("
				UPDATE  idk_jobstep_partners
				SET	jp_brtelefona = :jp_brtelefona
				WHERE jp_id = :jp_id");

			$query_update->execute(array(
						':jp_brtelefona' => NULL,
						':jp_id' => $jp_id));
	
	}
	
	*/
	
	
	
	
	
	
	
	
	
	
	
	
	
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
 
 
 
 
exit();


//  ć č đ se ne ispisuju dok š ž se ispisuju

// $db = new PDO('mysql:dbname=c2crm_db;host=10.100.1.21', 'c2crm_user', 'Y3b@yfZiqTzYG');

// $db->query('set character_set_client=utf8');
// $db->query('set character_set_connection=utf8');
// $db->query('set character_set_results=utf8');
// $db->query('set character_set_server=utf8');


// //MY PARTNERS
// $query = $db->prepare("
			// SELECT jp_id, jp_imeprezime, jp_position
			// FROM idk_jobstep_partners
			// WHERE jp_preporuka_id = :jp_preporuka_id"); 
			
// $query->execute(array(
				// ':jp_preporuka_id' => 7));
				
// $info_arr = array();
// $info_arr["data"] = array();


// while($row = $query->fetch()){  

	// $jp_id = $row['jp_id'];
	// $jp_imeprezime = explode(" ", $row['jp_imeprezime']);
	
	// $nashalatinica = array('Č','Ć','Š','Ž','Đ');
	// $latinica = array('C','C','S','Z','DJ');
	
	
	// $ime_partnera = str_replace($nashalatinica,$latinica,$jp_imeprezime[0]);
	// $partner_prezime = str_replace($nashalatinica,$latinica,$jp_imeprezime[1]);
	
	// echo $ime_partnera[0];
	// echo $partner_prezime[0]; 

	// //$partner_shortname = '' . $ime_partnera[0] . 'Š';
	 

	// $jp_position = intval($row['jp_position']);
			
	// if($jp_position == 1){
		// $position = "Junior Partner";
	// }elseif($jp_position == 2){
		// $position = "Partner";
	// }elseif($jp_position == 3){
		// $position = "Senior Partner";
	// }else{
		// $position = "Junior Partner";
	// }
	 
	// $info_item = array(
		// 'id' => $jp_id,
		// 'ime_partnera' => $ime_partnera,
		// 'partner_shortname' => $partner_shortname,
		// 'position' => $position
	// );
		 
	
	// array_push($info_arr["data"], $info_item);
			
// }
// http_response_code(200);
			
// //echo '<pre>';
// echo json_encode($info_arr);
// //echo '</pre>';






// exit();
 
?>