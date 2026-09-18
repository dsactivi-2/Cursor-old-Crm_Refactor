<?php
	
	error_reporting(0);
	// ini_set('display_errors', 1);
	// ini_set('display_startup_errors', 1);
	// error_reporting(E_ALL);
	include("includes/functions.php"); //Ostaviti
	
	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: adiss_testt?page=completeDigitalniNalog");
	}
	
	//PHPMailer
	// require $_SERVER['DOCUMENT_ROOT'].'/mail/Exception.php';
	// require $_SERVER['DOCUMENT_ROOT'].'/mail/PHPMailer.php';
	// require $_SERVER['DOCUMENT_ROOT'].'/mail/SMTP.php';
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;
	
	switch ($page){
		case "completeDigitalniNalog":
		
			// echo "Radi";
			// echo $logged_employee_id;
			// exit();
			//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			//PORUKE GRESKE START
			//	102		- nije urađen unos u bazu i user ce dobiti poruku da ponovno popuni formu
			//PORUKE GRESKE END
			//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			
			//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			//Poslani podaci sa forme START
				$origin = $_POST["origin"];
				$language = $_POST["language"];
				$imePoslodavac = $_POST["imePoslodavac"];
				$prezimePoslodavac = $_POST["prezimePoslodavac"];
				$emailPoslodavac = $_POST["emailPoslodavac"];
				$telefonPoslodavac = $_POST["telefonPoslodavac"];
				$nazivKompanije = $_POST["nazivKompanije"];
				// $opisPoslaKompanije = $_POST["opisPoslaKompanije"];
				// $ulogaUKompaniji = $_POST["ulogaUKompaniji"];
				// $brojZaposlenih = $_POST["brojZaposlenih"];
				// $zanimajuStruke = $_POST["zanimajuStruke"];
				$adresaKompanije = $_POST["adresaKompanije"];
				$postanskiBrojKompanije = $_POST["postanskiBrojKompanije"];
				$mjestoKompanije = $_POST["mjestoKompanije"];
				$brojZaposlenih = $_POST["brojZaposlenih"];
				$potrebnoRadnika = $_POST["potrebnoRadnika"];
				$pazicijeRadnika = $_POST["pazicijeRadnika"];
				$pozicijeRadnikaOstalo = $_POST["pozicijeRadnikaOstalo"];
				if($origin == 4){
					$originDesc = "sajmu ANGACOM";
				}else{
					$originDesc = "nepoznatom sajmu";
				}
				$pozicijeDesc = "";
				$pozicijeResult = array();
				if(is_array($pazicijeRadnika)){
					$pozicijeNiz = $pazicijeRadnika;
				}else{
					$pozicijeNiz = explode(",", $pazicijeRadnika);
				}
				$pozicijeCount = count($pozicijeNiz);
				for($i=0; $i<$pozicijeCount; $i++){
					if($pozicijeNiz[$i] != "Ostalo"){
						array_push($pozicijeResult, $pozicijeNiz[$i]); 
					}else{
						array_push($pozicijeResult, "Ostalo ->".$pozicijeRadnikaOstalo);
					}
				}
				$pozicijeDesc = implode(",", $pozicijeResult);
				
			//Poslani podaci sa forme START
			//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			
			//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			//Log START - Ovdje unosimo log za svaki slučaj sa svim informacijama koje su unešene na prijavnoj formi
			//		- "2097810751" je samo random oznaka i zajedno sa DIGITALNI NALOG omogućit će u slučaju potrebe lakše pretraživanje po logovima SQL: log_desc LIKE '%2097810751 DIGITALNI NALOG ->'
			$logDesc1 = '2097810751 DIGITALNI NALOG -> Porijeklo: '.$origin.' Jezik: '.$language.' Ime: '.$imePoslodavac.' Prezime: '.$prezimePoslodavac.' Broj telefona: '.$telefonPoslodavac.' Email: '.$emailPoslodavac.' Naziv kompanije: '.$nazivKompanije.' Adresa kompanije: '.$adresaKompanije.' Postanski broj kompanije: '.$postanskiBrojKompanije.' Mjesto kompanije: '.$mjestoKompanije.' Broj zaposlenih u kompaniji: '.$brojZaposlenih.' Potrebno radnika: '.$potrebnoRadnika.' Pozicije radnika: '.$pozicijeDesc.' ';
			$logDate1 = date('Y-m-d H:i:s'); 
			$logQuery1 = $db->prepare("
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
			$logQuery1->execute(array(
				':log_employeeid' => $logged_employee_id,
				':log_desc' => $logDesc1,
				':log_date' => $logDate1
			));
			//		- "2097810751" je samo random oznaka i zajedno sa DIGITALNI NALOG omogućit će u slučaju potrebe lakše pretraživanje po logovima SQL: log_desc LIKE '%2097810751 DIGITALNI NALOG ->'
			//Log END - Ovdje unosimo log za svaki slučaj sa svim informacijama koje su unešene na prijavnoj formi
			//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			
			// echo"
				// <br>
				// Prijeklo: ".$origin."<br>
				// Jezik: ".$language."<br>
				// Ime poslodavca: ".$imePoslodavac."<br>
				// Prezime poslodavca: ".$prezimePoslodavac. "<br>
				// Email poslodavca: ".$emailPoslodavac."<br>
				// Telefon poslodavca: ".$telefonPoslodavac."<br>
				// Naziv kompanije: ".$nazivKompanije."<br>
				// Opis posla kompanije: ".$opisPoslaKompanije."<br>
				// Uloga u kompaniji: ".$ulogaUKompaniji."<br>
				// Broj zaposlenih: ".$brojZaposlenih."<br>
				// Zanimaju struke: ".$zanimajuStruke."
			// ";
			
			//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			//Generisanje komentara koji ce se unositi kao description potreban agentu za prvi poziv START
				$companyInfo = '	
					ISPUNIO DIGITALNI NALOG: "'.$imePoslodavac.' '.$prezimePoslodavac.' '.$emailPoslodavac.' '.$telefonPoslodavac.'" <br>
					PODACI O KOMPANIJI: (Naziv) "'.$nazivKompanije.'" (Adresa) "'.$adresaKompanije.'" (Poštanski broj) "'.$postanskiBrojKompanije.'" (Mjesto kompanije) "'.$mjestoKompanije.'" <br>
					BROJ ZAPOSLENIH: "'.$brojZaposlenih.'" <br>
					POTREBNO RADNIKA: "'.$potrebnoRadnika.'" <br>
					POZICIJE RADNIKA: "'.$pozicijeDesc.'" <br>
				';
			//Generisanje komentara koji ce se unositi kao description potreban agentu za prvi poziv START
			//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			
			//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			// Ubacivanje kompanije START
				$queryCompany = $db->prepare("
					INSERT INTO idk_companies
						(
							company_name, 
							company_address, 
							company_zipcode,
							company_city,
							company_info,
							company_contact_type, 
							company_datetime, 
							company_status, 
							company_origin
						)
					VALUES
						(
							:company_name, 
							:company_address, 
							:company_zipcode,
							:company_city,
							:company_info,
							:company_contact_type, 
							:company_datetime, 
							:company_status, 
							:company_origin
						)
				");

				$queryCompany->execute(array(
					':company_name' => $nazivKompanije,
					':company_address' => $adresaKompanije,
					':company_zipcode' => $postanskiBrojKompanije,
					':company_city' => $mjestoKompanije,
					':company_info' => $companyInfo,
					':company_contact_type' => "Lead",
					':company_datetime' => date('Y-m-d H:i:s'),
					':company_status' => 1,
					':company_origin' => $origin
				));
				$lastCompanyId = $db->lastInsertId();
			// Ubacivanje kompanije END
			//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			
			
			//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			//Ubacivanje u prodajni modul START
				$queryClient = $db->prepare("
					INSERT INTO idk_clients
						(
							client_name,
							client_address,
							client_pp,
							client_city,
							client_telephone,
							client_email,
							client_origin,
							client_recommendation_company,
							client_fc_or_sales,
							client_fc_status,
							client_sales_status,
							client_description
						)
					VALUES
						(
							:client_name,
							:client_address,
							:client_pp,
							:client_city,
							:client_telephone,
							:client_email,
							:client_origin,
							:client_recommendation_company,
							:client_fc_or_sales,
							:client_fc_status,
							:client_sales_status,
							:client_description
						)
				");
				$queryClient->execute(array(
					':client_name' => $nazivKompanije,
					':client_address' => $adresaKompanije,
					':client_pp' => $postanskiBrojKompanije,
					':client_city' => $mjestoKompanije,
					':client_telephone' => $telefonPoslodavac,
					':client_email' => $emailPoslodavac,
					':client_origin' => $origin,
					':client_recommendation_company' => $lastCompanyId,
					':client_fc_or_sales' => 0,
					':client_fc_status' => 0,
					':client_sales_status' => 0,
					':client_description' => $companyInfo
				));
				
				$lastClientId = $db->lastInsertId();
				$clientDesc = 'Klijent '.$imePoslodavac.' '.$prezimePoslodavac.' je ispunio Digitalni nalog na '.$originDesc;
				insertClientStats($lastClientId, 0, 0, $clientDesc);
			//Ubacivanje u prodajni modul END
			//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			
			//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			//Ubacivanje kontakta START
				$queryContact = $db->prepare("
					INSERT INTO idk_contacts
						(
							contact_firstname, 
							contact_lastname, 
							contact_companyid, 
							contact_clientid, 
							contact_datetime, 
							contact_status
						)
					VALUES
						(
							:contact_firstname, 
							:contact_lastname, 
							:contact_companyid, 
							:contact_clientid, 
							:contact_datetime, 
							:contact_status
						)
				");

				$queryContact->execute(array(
					':contact_firstname' => $imePoslodavac,
					':contact_lastname' => $prezimePoslodavac,
					':contact_companyid' => $lastCompanyId,
					':contact_clientid' => $lastClientId,
					':contact_datetime' => date('Y-m-d H:i:s'),
					':contact_status' => 1
				));
				
				$lastContactId = $db->lastInsertId();
			//Ubacivanje kontakta END
			//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			
			//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			//Unos kontakt informacija START
				if ($telefonPoslodavac != "") {
					$queryPhone = $db->prepare("
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

					$queryPhone->execute(array(
						':ci_group' => 1,
						':ci_title' => "Telefon",
						':ci_data' => $telefonPoslodavac,
						':ci_primary' => 1,
						':ci_contactid' => $lastContactId
					));
					$contactTelefonId = $db->lastInsertId();
				}
				
				if ($emailPoslodavac != "") {

					$ci_group = 2;
					$ci_title = "E-mail";
					$ci_data = $poslodavac_mail_new_ND_cand1;
					$ci_primary = 1;

					$query_email = $db->prepare("
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

					$query_email->execute(array(
						':ci_group' => 2,
						':ci_title' => "E-mail",
						':ci_data' => $emailPoslodavac,
						':ci_primary' => 1,
						':ci_contactid' => $lastContactId
					));
					$contactEmailId = $db->lastInsertId();
				}
			//Unos kontakt informacija END 
			//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			
			//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			//ADD TO LOGS START
				$logDate2 = date('Y-m-d H:i:s');
				$logDesc2 = "
					2097810752 DIGITALNI NALOG -> Dodana nova kompanija sa ID = [".$lastCompanyId."] i novi klijent sa ID = [".$lastClientId."]. 
					Dodan kontakt sa ID = [".$lastContactId."] sa kontakt podacima za telefon sa ID = [".$contactTelefonId."] i email sa ID = [".$contactEmailId."]. 
				";
				$logQuery2 = $db->prepare("
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

				$logQuery2->execute(array(
					':log_employeeid' => $logged_employee_id,
					':log_desc' => $logDesc2,
					':log_date' => $logDate2
				));
			//ADD TO LOGS END
			//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			
			//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			//Slanje maila klijentu koji je popunio formu START
			
				//$mailText = "";
				$mailSubject = "";
				if($language == 0){
					$mailSubject = "The registration form was successfully submitted"; 
					$porukaNaslov = "Thank you for filling out the registration form";
					$porukaText1 = "Dear Mr/Mrs/r ".$imePoslodavac.' '.$prezimePoslodavac;
					$porukaText2 = "Your registration form has been successfully submitted. Someone from our professional team will contact you as soon as possible to arrange further steps.";
					$porukaText3 = "With best regards,";
					$aboutTxt = "About us";
					$emailTxt = "E-mail";
					
					$foot1 = "Please feel free to contact us if you have any questions.";
					$foot2 = "JOBSTEP | Esslingen am Neckar | Boschstr. 10.  73734 | Deutschland | info@job-step.de | +49 711 460 54 195";

				}else if($language == 1){
					$mailSubject = "The registration form was successfully submitted"; 
					$porukaNaslov = "Thank you for filling out the registration form";
					$porukaText1 = "Dear Mr/Mrs/r ".$imePoslodavac.' '.$prezimePoslodavac;
					$porukaText2 = "Your registration form has been successfully submitted. Someone from our professional team will contact you as soon as possible to arrange further steps.";
					$porukaText3 = "With best regards,";
					$aboutTxt = "About us";
					$emailTxt = "E-mail";
					
					$foot1 = "Please feel free to contact us if you have any questions.";
					$foot2 = "JOBSTEP | Esslingen am Neckar | Boschstr. 10.  73734 | Deutschland | info@job-step.de | +49 711 460 54 195";
				}else{
					$mailSubject = " Das Anmeldeformular wurde erfolgreich übermittelt";
					$porukaNaslov = "Vielen Dank f&#252;r das Ausf&#252;llen des Formulars";
					$porukaText1 = "Sehr Geehrte/r ".$imePoslodavac.' '.$prezimePoslodavac;
					$porukaText2 = "Ihr Anmeldeformular wurde erfolgreich empfangen. Jemand aus unserem professionellen Team wird sich so schnell wie m&#246;glich mit Ihnen in Verbindung setzen, um die weiteren Schritte zu vereinbaren.";
					$porukaText3 = "Mit freundlichen Gr&#252;&#223;en,";
					$aboutTxt = "&#220;ber uns";
					$emailTxt = "E-mail";
					
					$foot1 = "Bitte z&#246;gern Sie nicht, uns zu kontaktieren, wenn Sie Fragen haben.";
					$foot2 = "Esslingen am Neckar | Boschstr. 10. 73734 | +49 711 460 54 195";
				}
				
				$mailText = '
					<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
					<html>
					  <head>
						<!-- Compiled with Bootstrap Email version: 1.1.5 --><meta http-equiv="x-ua-compatible" content="ie=edge">
						<meta name="x-apple-disable-message-reformatting">
						<meta name="viewport" content="width=device-width, initial-scale=1">
						<meta name="format-detection" content="telephone=no, date=no, address=no, email=no">
						<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
						<style type="text/css">
						  body,table,td{font-family:Helvetica,Arial,sans-serif !important}.ExternalClass{width:100%}.ExternalClass,.ExternalClass p,.ExternalClass span,.ExternalClass font,.ExternalClass td,.ExternalClass div{line-height:150%}a{text-decoration:none}*{color:inherit}a[x-apple-data-detectors],u+#body a,#MessageViewBody a{color:inherit;text-decoration:none;font-size:inherit;font-family:inherit;font-weight:inherit;line-height:inherit}img{-ms-interpolation-mode:bicubic}table:not([class^=s-]){font-family:Helvetica,Arial,sans-serif;mso-table-lspace:0pt;mso-table-rspace:0pt;border-spacing:0px;border-collapse:collapse}table:not([class^=s-]) td{border-spacing:0px;border-collapse:collapse}@media screen and (max-width: 600px){.row-responsive.row{margin-right:0 !important}td.col-lg-6{display:block;width:100% !important;padding-left:0 !important;padding-right:0 !important}.w-full,.w-full>tbody>tr>td{width:100% !important}*[class*=s-lg-]>tbody>tr>td{font-size:0 !important;line-height:0 !important;height:0 !important}.s-2>tbody>tr>td{font-size:8px !important;line-height:8px !important;height:8px !important}.s-5>tbody>tr>td{font-size:20px !important;line-height:20px !important;height:20px !important}.s-10>tbody>tr>td{font-size:40px !important;line-height:40px !important;height:40px !important}}
						</style>
					  </head>
					  <body class="bg-light" style="outline: 0; width: 100%; min-width: 100%; height: 100%; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; font-family: Helvetica, Arial, sans-serif; line-height: 24px; font-weight: normal; font-size: 16px; -moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; color: #000000; margin: 0; padding: 0; border-width: 0;" bgcolor="#f7fafc">
						<table class="bg-light body" valign="top" role="presentation" border="0" cellpadding="0" cellspacing="0" style="outline: 0; width: 100%; min-width: 100%; height: 100%; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; font-family: Helvetica, Arial, sans-serif; line-height: 24px; font-weight: normal; font-size: 16px; -moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; color: #000000; margin: 0; padding: 0; border-width: 0;" bgcolor="#f7fafc">
						  <tbody>
							<tr>
							  <td valign="top" style="line-height: 24px; font-size: 16px; margin: 0;" align="left" bgcolor="#f7fafc">
								<table class="container" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
								  <tbody>
									<tr>
									  <td align="center" style="line-height: 24px; font-size: 16px; margin: 0; padding: 0 16px;">
										<!--[if (gte mso 9)|(IE)]>
										  <table align="center" role="presentation">
											<tbody>
											  <tr>
												<td width="600">
										<![endif]-->
										<table align="center" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%; max-width: 600px; margin: 0 auto;">
										  <tbody>
											<tr>
											  <td style="line-height: 24px; font-size: 16px; margin: 0;" align="left">
												<table class="s-10 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
												  <tbody>
													<tr>
													  <td style="line-height: 40px; font-size: 40px; width: 100%; height: 40px; margin: 0;" align="left" width="100%" height="40">
														&#160;
													  </td>
													</tr>
												  </tbody>
												</table>
												<table class="card" role="presentation" border="0" cellpadding="0" cellspacing="0" style="border-radius: 6px; border-collapse: separate !important; width: 100%; overflow: hidden; border: 1px solid #e2e8f0;" bgcolor="#ffffff">
												  <tbody>
													<tr>
													  <td style="line-height: 24px; font-size: 16px; width: 100%; margin: 0;" align="left" bgcolor="#ffffff">
														<table class="card-body myBgColor" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" bgcolor="#6097A0">
														  <tbody>
															<tr>
															  <td style="line-height: 24px; font-size: 16px; width: 100%; margin: 0; padding: 20px;" align="left">
																<div class="row" style="margin-right: -24px;">
																  <table class="" role="presentation" border="0" cellpadding="0" cellspacing="0" style="table-layout: fixed; width: 100%;" width="100%">
																	<tbody>
																	  <tr>
																		<td class="col-12 text-center" style="line-height: 24px; font-size: 16px; min-height: 1px; font-weight: normal; width: 100%; margin: 0;" align="center" valign="top">
																		  <img class="img-fluid" style="width: 100px; height: auto; line-height: 100%; outline: none; text-decoration: none; display: block; max-width: 100%; border-style: none; border-width: 0;" src="https://crm.job-step.com/images/digitalni_nalog_images/bijeli_vektorski_logo_Jobstepa_1.png" alt="Some Image" width="100%">
																		</td>
																	  </tr>
																	</tbody>
																  </table>
																</div>
																<table class="s-5 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
																  <tbody>
																	<tr>
																	  <td style="line-height: 20px; font-size: 20px; width: 100%; height: 20px; margin: 0;" align="left" width="100%" height="20">
																		&#160;
																	  </td>
																	</tr>
																  </tbody>
																</table>
																<table class="hr" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
																  <tbody>
																	<tr>
																	  <td style="line-height: 24px; font-size: 16px; border-top-width: 1px; border-top-color: #e2e8f0; border-top-style: solid; height: 1px; width: 100%; margin: 0;" align="left">
																	  </td>
																	</tr>
																  </tbody>
																</table>
																<table class="s-5 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
																  <tbody>
																	<tr>
																	  <td style="line-height: 20px; font-size: 20px; width: 100%; height: 20px; margin: 0;" align="left" width="100%" height="20">
																		&#160;
																	  </td>
																	</tr>
																  </tbody>
																</table>
																<div class="row" style="margin-right: -24px;">
																  <table class="" role="presentation" border="0" cellpadding="0" cellspacing="0" style="table-layout: fixed; width: 100%;" width="100%">
																	<tbody>
																	  <tr>
																		<td class="col-12" style="line-height: 24px; font-size: 16px; min-height: 1px; font-weight: normal; width: 100%; margin: 0;" align="left" valign="top">
																		  <h3 class="myTextColor text-center fw-700" style="padding-top: 0; padding-bottom: 0; font-weight: 700 !important; vertical-align: baseline; font-size: 28px; line-height: 33.6px; color: #ffffff; margin: 0;" align="center">
																			'.$porukaNaslov.'
																		  </h3>
																		</td>
																	  </tr>
																	</tbody>
																  </table>
																</div>
																<table class="s-5 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
																  <tbody>
																	<tr>
																	  <td style="line-height: 20px; font-size: 20px; width: 100%; height: 20px; margin: 0;" align="left" width="100%" height="20">
																		&#160;
																	  </td>
																	</tr>
																  </tbody>
																</table>
																<table class="hr" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
																  <tbody>
																	<tr>
																	  <td style="line-height: 24px; font-size: 16px; border-top-width: 1px; border-top-color: #e2e8f0; border-top-style: solid; height: 1px; width: 100%; margin: 0;" align="left">
																	  </td>
																	</tr>
																  </tbody>
																</table>
																<table class="s-5 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
																  <tbody>
																	<tr>
																	  <td style="line-height: 20px; font-size: 20px; width: 100%; height: 20px; margin: 0;" align="left" width="100%" height="20">
																		&#160;
																	  </td>
																	</tr>
																  </tbody>
																</table>
																<div class="row" style="margin-right: -24px;">
																  <table class="" role="presentation" border="0" cellpadding="0" cellspacing="0" style="table-layout: fixed; width: 100%;" width="100%">
																	<tbody>
																	  <tr>
																		<td class="col-12 space-y-2" style="line-height: 24px; font-size: 16px; min-height: 1px; font-weight: normal; width: 100%; margin: 0;" align="left" valign="top">
																		  <p class="myTextColor" style="line-height: 24px; font-size: 16px; width: 100%; color: #ffffff; margin: 0;" align="left">'.$porukaText1.'</p>
																		  <table class="s-2 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
																			<tbody>
																			  <tr>
																				<td style="line-height: 8px; font-size: 8px; width: 100%; height: 8px; margin: 0;" align="left" width="100%" height="8">
																				  &#160;
																				</td>
																			  </tr>
																			</tbody>
																		  </table>
																		  <p class="myTextColor" style="line-height: 24px; font-size: 16px; width: 100%; color: #ffffff; margin: 0;" align="left">
																			'.$porukaText2.'
																		  </p>
																		  <table class="s-2 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
																			<tbody>
																			  <tr>
																				<td style="line-height: 8px; font-size: 8px; width: 100%; height: 8px; margin: 0;" align="left" width="100%" height="8">
																				  &#160;
																				</td>
																			  </tr>
																			</tbody>
																		  </table>
																		  <p class="myTextColor" style="line-height: 24px; font-size: 16px; width: 100%; color: #ffffff; margin: 0;" align="left">
																			'.$porukaText3.'
																		  </p>
																		  <table class="s-2 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
																			<tbody>
																			  <tr>
																				<td style="line-height: 8px; font-size: 8px; width: 100%; height: 8px; margin: 0;" align="left" width="100%" height="8">
																				  &#160;
																				</td>
																			  </tr>
																			</tbody>
																		  </table>
																		  <p class="myTextColor" style="line-height: 24px; font-size: 16px; width: 100%; color: #ffffff; margin: 0;" align="left">
																			Jobstep.
																		  </p>
																		</td>
																	  </tr>
																	</tbody>
																  </table>
																</div>
																<table class="s-5 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
																  <tbody>
																	<tr>
																	  <td style="line-height: 20px; font-size: 20px; width: 100%; height: 20px; margin: 0;" align="left" width="100%" height="20">
																		&#160;
																	  </td>
																	</tr>
																  </tbody>
																</table>
																<table class="hr" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
																  <tbody>
																	<tr>
																	  <td style="line-height: 24px; font-size: 16px; border-top-width: 1px; border-top-color: #e2e8f0; border-top-style: solid; height: 1px; width: 100%; margin: 0;" align="left">
																	  </td>
																	</tr>
																  </tbody>
																</table>
																<table class="s-5 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
																  <tbody>
																	<tr>
																	  <td style="line-height: 20px; font-size: 20px; width: 100%; height: 20px; margin: 0;" align="left" width="100%" height="20">
																		&#160;
																	  </td>
																	</tr>
																  </tbody>
																</table>
																<div class="row" style="margin-right: -24px;">
																  <table class="" role="presentation" border="0" cellpadding="0" cellspacing="0" style="table-layout: fixed; width: 100%;" width="100%">
																	<tbody>
																	  <tr>
																		<td class="col-12" style="line-height: 24px; font-size: 16px; min-height: 1px; font-weight: normal; width: 100%; margin: 0;" align="left" valign="top">
																		  <div class="row row-responsive" style="margin-right: -24px;">
																			<table class="" role="presentation" border="0" cellpadding="0" cellspacing="0" style="table-layout: fixed; width: 100%;" width="100%">
																			  <tbody>
																				<tr>
																				  <td class="col-lg-6 text-center" style="line-height: 24px; font-size: 16px; min-height: 1px; font-weight: normal; width: 50%; margin: 0;" align="center" valign="top">
																					<img class="img-fluid" style="width: 50px; height: auto; line-height: 100%; outline: none; text-decoration: none; display: block; max-width: 100%; border-style: none; border-width: 0;" src="https://crm.job-step.com/images/digitalni_nalog_images/globee-512.png" alt="Some Image" width="100%">
																					<table class="s-5 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
																					  <tbody>
																						<tr>
																						  <td style="line-height: 20px; font-size: 20px; width: 100%; height: 20px; margin: 0;" align="left" width="100%" height="20">
																							&#160;
																						  </td>
																						</tr>
																					  </tbody>
																					</table>
																					<p class="text-white  fw-700 text-center" style="line-height: 24px; font-size: 16px; color: #ffffff; font-weight: 700 !important; width: 100%; margin: 0;" align="center">'.$aboutTxt.'</p>
																					<table class="s-2 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
																					  <tbody>
																						<tr>
																						  <td style="line-height: 8px; font-size: 8px; width: 100%; height: 8px; margin: 0;" align="left" width="100%" height="8">
																							&#160;
																						  </td>
																						</tr>
																					  </tbody>
																					</table>
																					<p class="text-white  text-center" style="line-height: 24px; font-size: 16px; color: #ffffff; width: 100%; margin: 0;" align="center">www.job-step.de</p>
																					<table class="s-2 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
																					  <tbody>
																						<tr>
																						  <td style="line-height: 8px; font-size: 8px; width: 100%; height: 8px; margin: 0;" align="left" width="100%" height="8">
																							&#160;
																						  </td>
																						</tr>
																					  </tbody>
																					</table>
																				  </td>
																				  <td class="col-lg-6 text-center" style="line-height: 24px; font-size: 16px; min-height: 1px; font-weight: normal; width: 50%; margin: 0;" align="center" valign="top">
																					<img class="img-fluid" style="width: 50px; height: auto; line-height: 100%; outline: none; text-decoration: none; display: block; max-width: 100%; border-style: none; border-width: 0;" src="https://crm.job-step.com/images/digitalni_nalog_images/maill-512.png" alt="Some Image" width="100%">
																					<table class="s-5 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
																					  <tbody>
																						<tr>
																						  <td style="line-height: 20px; font-size: 20px; width: 100%; height: 20px; margin: 0;" align="left" width="100%" height="20">
																							&#160;
																						  </td>
																						</tr>
																					  </tbody>
																					</table>
																					<p class="text-white  fw-700 text-center" style="line-height: 24px; font-size: 16px; color: #ffffff; font-weight: 700 !important; width: 100%; margin: 0;" align="center">'.$emailTxt.'</p>
																					<table class="s-2 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
																					  <tbody>
																						<tr>
																						  <td style="line-height: 8px; font-size: 8px; width: 100%; height: 8px; margin: 0;" align="left" width="100%" height="8">
																							&#160;
																						  </td>
																						</tr>
																					  </tbody>
																					</table>
																					<p class="text-white  text-center" style="line-height: 24px; font-size: 16px; color: #ffffff; width: 100%; margin: 0;" align="center">info@job-step.de</p>
																					<table class="s-2 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
																					  <tbody>
																						<tr>
																						  <td style="line-height: 8px; font-size: 8px; width: 100%; height: 8px; margin: 0;" align="left" width="100%" height="8">
																							&#160;
																						  </td>
																						</tr>
																					  </tbody>
																					</table>
																				  </td>
																				</tr>
																			  </tbody>
																			</table>
																		  </div>
																		</td>
																	  </tr>
																	</tbody>
																  </table>
																</div>
																<table class="s-5 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
																  <tbody>
																	<tr>
																	  <td style="line-height: 20px; font-size: 20px; width: 100%; height: 20px; margin: 0;" align="left" width="100%" height="20">
																		&#160;
																	  </td>
																	</tr>
																  </tbody>
																</table>
																<table class="hr" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
																  <tbody>
																	<tr>
																	  <td style="line-height: 24px; font-size: 16px; border-top-width: 1px; border-top-color: #e2e8f0; border-top-style: solid; height: 1px; width: 100%; margin: 0;" align="left">
																	  </td>
																	</tr>
																  </tbody>
																</table>
																<table class="s-5 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
																  <tbody>
																	<tr>
																	  <td style="line-height: 20px; font-size: 20px; width: 100%; height: 20px; margin: 0;" align="left" width="100%" height="20">
																		&#160;
																	  </td>
																	</tr>
																  </tbody>
																</table>
																<div class="row" style="margin-right: -24px;">
																  <table class="" role="presentation" border="0" cellpadding="0" cellspacing="0" style="table-layout: fixed; width: 100%;" width="100%">
																	<tbody>
																	  <tr>
																		<td class="col-12" style="line-height: 24px; font-size: 16px; min-height: 1px; font-weight: normal; width: 100%; margin: 0;" align="left" valign="top">
																		  <div class="row" style="margin-right: -24px;">
																			<table class="" role="presentation" border="0" cellpadding="0" cellspacing="0" style="table-layout: fixed; width: 100%;" width="100%">
																			  <tbody>
																				<tr>
																				  <td class="col-12" style="line-height: 24px; font-size: 16px; min-height: 1px; font-weight: normal; width: 100%; margin: 0;" align="left" valign="top">
																					<p class="text-white  text-center" style="line-height: 24px; font-size: 16px; color: #ffffff; width: 100%; margin: 0;" align="center">'.$foot1.'</p>
																					<table class="s-2 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
																					  <tbody>
																						<tr>
																						  <td style="line-height: 8px; font-size: 8px; width: 100%; height: 8px; margin: 0;" align="left" width="100%" height="8">
																							&#160;
																						  </td>
																						</tr>
																					  </tbody>
																					</table>
																				  </td>
																				</tr>
																			  </tbody>
																			</table>
																		  </div>
																		  <table class="s-2 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
																			<tbody>
																			  <tr>
																				<td style="line-height: 8px; font-size: 8px; width: 100%; height: 8px; margin: 0;" align="left" width="100%" height="8">
																				  &#160;
																				</td>
																			  </tr>
																			</tbody>
																		  </table>
																		  <div class="row" style="margin-right: -24px;">
																			<table class="" role="presentation" border="0" cellpadding="0" cellspacing="0" style="table-layout: fixed; width: 100%;" width="100%">
																			  <tbody>
																				<tr>
																				  <td class="col-12" style="line-height: 24px; font-size: 16px; min-height: 1px; font-weight: normal; width: 100%; margin: 0;" align="left" valign="top">
																					<p class="text-white  fw-700 text-center" style="line-height: 24px; font-size: 16px; color: #ffffff; font-weight: 700 !important; width: 100%; margin: 0;" align="center">'.$foot2.'</p>
																					<table class="s-2 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
																					  <tbody>
																						<tr>
																						  <td style="line-height: 8px; font-size: 8px; width: 100%; height: 8px; margin: 0;" align="left" width="100%" height="8">
																							&#160;
																						  </td>
																						</tr>
																					  </tbody>
																					</table>
																				  </td>
																				</tr>
																			  </tbody>
																			</table>
																		  </div>
																		</td>
																	  </tr>
																	</tbody>
																  </table>
																</div>
															  </td>
															</tr>
														  </tbody>
														</table>
													  </td>
													</tr>
												  </tbody>
												</table>
												<table class="s-10 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
												  <tbody>
													<tr>
													  <td style="line-height: 40px; font-size: 40px; width: 100%; height: 40px; margin: 0;" align="left" width="100%" height="40">
														&#160;
													  </td>
													</tr>
												  </tbody>
												</table>
											  </td>
											</tr>
										  </tbody>
										</table>
										<!--[if (gte mso 9)|(IE)]>
										</td>
									  </tr>
									</tbody>
								  </table>
										<![endif]-->
									  </td>
									</tr>
								  </tbody>
								</table>
							  </td>
							</tr>
						  </tbody>
						</table>
					  </body>
					</html>
				';
				
				$hostName = "smtp.gmail.com";
				$userName = "support@job-step.com";
				$password = "eooc nnxo aylp lqkh";
				$setFrom = "support@job-step.com";
				
				$mail = new PHPMailer;
				$mail->isSMTP();											// Set mailer to use SMTP
				$mail->Host = $hostName;									// Specify main and backup SMTP servers
				$mail->SMTPAuth = true;										// Enable SMTP authentication
				$mail->Username = $userName;								// SMTP username
				$mail->Password = $password;								// SMTP password
				$mail->SMTPSecure = 'ssl';									// Enable TLS encryption, `ssl` also accepted
				$mail->Port = 465;											// TCP port to connect to
				$mail->CharSet = 'UTF-8';
				$mail->setFrom($setFrom, 'JobStep');    								// Add a recipient
				$mail->addAddress($emailPoslodavac); 						// Add a recipient
				
				
				$mail->Subject = "".$mailSubject."";
				
				$mail->Body = "
					".$mailText."
				";
				$mail->AltBody = "ALT";
				if(!$mail->send()) {
					//Greska
					$logDesc3 = "
						2097810753 DIGITALNI NALOG -> NIJE POSLAN auto responder za kompaniju ID = [".$lastCompanyId."] 
						i klijenta ID = [".$lastClientId."] 
						sa kontaktom ID = [".$lastContactId."] 
						i kontakt podacima za 
						telefon ID = [".$contactTelefonId."] i 
						email ID = [".$contactEmailId."].";
				}else{
					//Nije greska
					$logDesc3 = "
						2097810753 DIGITALNI NALOG -> POSLAN auto responder za kompaniju ID = [".$lastCompanyId."] 
						i klijenta ID = [".$lastClientId."] 
						sa kontaktom ID = [".$lastContactId."] 
						i kontakt podacima za 
						telefon ID = [".$contactTelefonId."] i 
						email ID = [".$contactEmailId."].";
				}
				$logDate3 = date('Y-m-d H:i:s');
				
				$logQuery3 = $db->prepare("
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

				$logQuery3->execute(array(
					':log_employeeid' => $logged_employee_id,
					':log_desc' => $logDesc3,
					':log_date' => $logDate3
				));
			//Slanje maila klijentu koji je popunio formu END 
			//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			
			echo 1;
		break;
		
		default:
			echo 102; //Poruka greške
		break;
	}
?>