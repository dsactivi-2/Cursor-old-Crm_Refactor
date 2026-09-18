<?php
	
include("../includes/connect.php");
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set('display_errors', 1);



/****************************
		PRODUCTION QUERY: 
****************************/

// global $db;
// $query = $db->prepare("
					
// SELECT kandidat_id, kandidat_ime, kandidat_prezime, kandidat_mobitel, kandidat_email
                    // FROM idk_kandidati
                    // WHERE kandidat_status != 3
                    // AND kandidat_zaposlen_kod IS NULL
                    // AND(kandidat_status_prijave != '4' OR kandidat_status_prijave is NULL)
                    // AND (kandidat_mobitel LIKE '+387%' OR kandidat_mobitel LIKE '+381%')
                    // AND kandidat_group IN (2,3,6,48) 
                    // AND kandidat_id > 139
// ");
// $query->execute();

/****************************
			QUERY KRAJ 
****************************/

/****************************
			TESTNI QUERY: 
****************************/

// global $db;
// $query = $db->prepare("
					
// SELECT kandidat_id, kandidat_ime, kandidat_prezime, kandidat_mobitel, kandidat_email
                    // FROM idk_kandidati
                    // WHERE kandidat_mobitel LIKE '+387603358470'
					// LIMIT 1
// ");
// $query->execute();

/****************************
			QUERY KRAJ 
****************************/


while($kandidat_info = $query->fetch()){

		$curl = curl_init();
		$broj  = preg_replace('/[^0-9]+/', '', $kandidat_info['kandidat_mobitel']);
		 
		 
		/****************************
			TESTNI PARAMETARI: 
		****************************/
		 
		// $broj  = "38761210765"; //deno
		// $broj = "387603358470";
		// $broj = "+38761938892";
		// $broj = "+38763071829";
		// $broj = "+38761395909";
		// $broj = "38763071829";
		// $broj = "38762473740";
		/****************************
		KRAJ
		****************************/
		
		$pozivni = substr(trim($broj," "),0,3);
		
		/*******************************
			PARTNER - PARAMETARI: 
		*********************************/
		
		$token = add_partner_manually($kandidat_info);
		
		if($pozivni == "381")	$link = "https://job-step.net/postanipartner/".$token."/srb";
			else  $link = "https://job-step.net/postanipartner/".$token."/ba";
			
		$imageURL = "https://jobstep-app.com/images/medicinska_sestra_2.png";	
		$buttonText = "Saznaj više";
		
		
		if($pozivni=="387"){
			$textViber = "Poznaješ medicinsku sestru/tehničara pedijatrijskog smjera, a želi se zaposliti u Njemačkoj?\nPreporuči posao i zaradi svoju proviziju u visini od 200€ onda kada se kandidat zaposli! 😊";
			$textSMS = "Poznaješ medicinsku sestru/tehničara pedijatrijskog smjera, a želi se zaposliti u Njemačkoj?\nPreporuči posao i zaradi svoju proviziju u visini od 200€ onda kada se kandidat zaposli!\n ".$link;
		}else{
			$textViber = "Poznaješ medicinsku sestru/tehničara pedijatrijskog smera, a želi se zaposliti u Nemačkoj?\nPreporuči posao i zaradi svoju proviziju u visini od 200€ onda kada se kandidat zaposli! 😊";
			$textSMS = "Poznaješ medicinsku sestru/tehničara pedijatrijskog smera, a želi se zaposliti u Nemačkoj?\nPreporuči posao i zaradi svoju proviziju u visini od 200€ onda kada se kandidat zaposli!\n ".$link;
		}	
			
		/****************************
		KRAJ
		****************************/
		
		
		
		/*******************************
			DIREKTNE PORUKE - PARAMETARI: 
		*********************************/
		// $link = "https://jobstep-app.com/registracija/518/korak1";
		
		// $imageURL = "https://jobstep-app.com/jobstep-partner/marketing-slike/viber85medicinari.jpg";
		// $buttonText = "Prijavi se";
		
		
		
		// if($pozivni=="387"){
			// $textViber = "	Medicinska sestra – tehničar
							// Hamburg | Hannover | Frankfurt
							// POSLODAVAC NUDI:
							// ✔️Brutto platu od min 2.300  EUR (početna plata)
							// ✔️Osiguran smještaj
							// ✔️Kurs njemačkog jezika do B2 nivoa
							// ✔️Pripreme za polaganje nostrifikacionog ispita
							// ✔️Podrška pri nostrifikaciji diplome
							// AKO POSJEDUJEŠ:
							// ✔️Završenu srednju medicinsku školu (opći smjer)
							// ✔️Minimalno poznavanje njemačkog jezika na B1 nivou
							// ✔️Odrađen pripravnički staž i položen stručni ispit
							// ";
			// $textSMS = "POSAO U NJEMAČKOJ\nMEDICINSKA SESTRA-TEHNIČAR \n https://jobstep-app.com/registracija/518/korak1";
		// }else{
			// $textViber = 	"Medicinska sestra – tehničar
							// Hamburg | Hannover | Frankfurt
							// POSLODAVAC NUDI:
							// ✔️Brutto platu od min 2.300  EUR (početna plata)
							// ✔️Osiguran smeštaj
							// ✔️Kurs nemačkog jezika do B2 nivoa
							// ✔️Pripreme za polaganje nostrifikacionog ispita
							// ✔️Podrška pri nostrifikaciji diplome
							// AKO POSEDUJEŠ:
							// ✔️Završenu srednju medicinsku školu (opći smer)
							// ✔️Minimalno poznavanje nemačkog jezika na B1 nivou
							// ✔️Odrađen pripravnički staž i položen stručni ispit
							// ";
			// $textSMS = "POSAO U NEMAČKOJ\nMEDICINSKA SESTRA-TEHNIČAR \n https://jobstep-app.com/registracija/518/korak1";
			// }
		/****************************
		KRAJ
		****************************/
		
		$buttonURL = $link;
		
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


		
/***********************************
*  PARTNER INSERT NEW - QUERY
************************************/			


function add_partner_manually($kandidatedata){ // proslijedi podatke od kandidata kao niz ovoj funkciji - vraća link koji se šalje kandidatu za dalje prosljeđivanje
		global $db;
		$registerdata['jp_imeprezime'] = $kandidatedata['kandidat_ime']." ".$kandidatedata['kandidat_prezime']; 
		$registerdata['jp_email'] = $kandidatedata['kandidat_email']; 
		if(empty($registerdata['jp_email'])) { $registerdata['jp_email'] = $kandidatedata['kandidat_ime']."#".bin2hex(openssl_random_pseudo_bytes(3)); }
		$registerdata['jp_password'] = bin2hex(openssl_random_pseudo_bytes(8));
		$registerdata['jp_telefon'] = $kandidatedata['kandidat_mobitel'];
		$register_date = date('Y-m-d H:i:s');
		$token = bin2hex(openssl_random_pseudo_bytes(12));
		

		/******************************
		 *      Check if user exist
		 ******************************/
		$check_query = $db->prepare("
				SELECT jp_imeprezime,jp_id
				FROM idk_jobstep_partners
				WHERE jp_email = :jp_email OR jp_brtelefona = :jp_brtelefona");
				
		$check_query->execute(array(
						':jp_email' => $registerdata['jp_email'],
						':jp_brtelefona' => $registerdata['jp_telefon']
						));
		$check_row = $check_query->fetch();
		$number_of_rows = $check_query->rowCount();
		if($number_of_rows == 0){
			/******************************
			 *      Insert into database
			 ******************************/
			$register_query = $db->prepare("
							INSERT INTO idk_jobstep_partners
								(jp_imeprezime, jp_email, jp_password,jp_brtelefona, jp_register_date, jp_mailconfirmation_token, jp_source)
								VALUES
								(:jp_imeprezime, :jp_email, :jp_password,:jp_brtelefona, :jp_register_date, :jp_mailconfirmation_token, :jp_source)");
			$register_query->execute(array(
							':jp_imeprezime' => $registerdata['jp_imeprezime'],
							':jp_email' => $registerdata['jp_email'],
							':jp_password' => $registerdata['jp_password'],
							':jp_brtelefona' => $registerdata['jp_telefon'],
							':jp_register_date' => $register_date,
							':jp_mailconfirmation_token' => $token,
							':jp_source' => 1
							));
			print_r($register_query->errorInfo());
			return (base64_encode ($token));
			
		}
		else
		{
				$read_token = $db->prepare("
								SELECT jp_mailconfirmation_token
								FROM idk_jobstep_partners
								WHERE jp_id = :jp_id");
				$read_token->execute(array(
								':jp_id' => $check_row['jp_id']
								));
								
				$row = $read_token -> fetch();
				
				return (base64_encode ($row['jp_mailconfirmation_token']));
				
		}
}

		
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
				":jp_password" => $kontatkt_podaci['password'], // prvo sam morao unijeti lozinku u bazu neheširanu, tako da sad heširam
				":jp_id" => $jp_id
				));
		
		sendLoginInfoPartner($jp_id);
}

		
/***********************************
*  PARTNER INSERT NEW - END
************************************/	
?>