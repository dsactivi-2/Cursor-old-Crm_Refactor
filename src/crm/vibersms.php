<?php
	include("includes/functions.php");
	include("pdf_generator.php");
	//createRacunBIH(5524, "138");
	
	
	?>
	<table>
	<th>#</th>
	<th>kan_id</th>
	<th>ime</th>
	<th>mobitel</th>
	<th>grupa</th>
	<th>poruka</th>
	<?php
	$br = 1;
	$query = $db->prepare("
							SELECT kandidat_id, kandidat_ime, kandidat_prezime, kandidat_mobitel, kandidat_group
							FROM idk_kandidati 
							WHERE kandidat_id BETWEEN 11585 AND 53019 AND kandidat_status != 3 AND kandidat_mobitel LIKE '+387%' AND (kandidat_status_prijave != 4 OR kandidat_status_prijave is null) 
							AND kandidat_group IN (22)
						");
	//AND kandidat_group IN (9,11,22,34,36,39,41,50,53,56,61)
	//AND kandidat_group IN (9,11)+
	//AND kandidat_group IN (22) - kandidat_id BETWEEN 7328 AND 11563 AND 
	//AND kandidat_group IN (22) - kandidat_id BETWEEN 11585 AND 53019 AND 
	//AND kandidat_group IN (34,36)+
	//AND kandidat_group IN (39,41)+
	//AND kandidat_group IN (50,53,56,61)+
	
	$query->execute();
	
	while($row=$query->fetch()){
		$kandidat_id = $row['kandidat_id'];
		$kandidat_ime = $row['kandidat_ime'];
		$kandidat_prezime = $row['kandidat_prezime'];
		$kandidat_mobitel = $row['kandidat_mobitel'];
		//$kandidat_mobitel = "+38761938892"; //emir
		//$kandidat_mobitel = "+387603427107"; //faris
		$kandidat_group = $row['kandidat_group'];
		$text_viber = " Njemačka nikad nije bila bliže. Od sada u Jobstepu usluga koju ste željeli! Pronalazimo ti posao, a uslugu plaćaš nakon što se zaposliš! Predobro i istinito. Čekamo te! 😉";
		$text_sms = " Njemačka nikad nije bila bliže. Od sada u Jobstepu usluga koju ste željeli! Pronalazimo ti posao, a uslugu plaćaš nakon što se zaposliš! Predobro i istinito. Čekamo te! 😉 Prijavi se: https://forms.gle/HxHJe4tnSjc4mGDC8";
		//$text_sms = "Njemaka nikad nije bila blize. Od sada u Jobstepu usluga koju ste zeljeli! Pronalazimo ti posao, a uslugu placas nakon sto se zaposlis! Predobro i istinito. Cekamo te! Prijavi se: https://forms.gle/HxHJe4tnSjc4mGDC8";
		$link = "https://forms.gle/HxHJe4tnSjc4mGDC8";
		
		$notf_new_year = $row_user['notf_new_year'];
		$check_projekat = $db->prepare("
				SELECT pk_projectid
				FROM idk_project_kandidati
				WHERE pk_kandidatid = $kandidat_id AND pk_projectid IN (4,17,32,56,63,70,75,80,85,93,110,116,119,129,132,136,139,143,146,150,153,170,173,177,180,184,187,191,203,206,210,213,225,228,233,236,260,263,278,281,288,291,292,297,300,305,308,315,318,330,333,338,341,351,354,359,362,372,375,382,385,396,399,401,418,421,424,425,433,436,444,447,454,457,458,488,491,493,499,502,507,510,515,520,523,528,531,536,539,542,547,550,555,558,563,566,567,568,577,580,585,588,593,596,597,598,604,607,608,609,614,617,618,619,626,629,631,637,640,642,647,650,651,656,659,664,667,672,675,680,683,688,691,695,696,697,702,705,714,717,814,819,822,823,826,829,853,856,861,864,885,888,893,896,901,904,909,912,917,920,925,928,933,936,942,945,950,953,959,962,964,987,990,999,1002,1007,1010,1015,1018,1023,1026,1031,1034,1039,1042,1047,1050,1055,1058,1063,1066,1071,1074,1079,1082,1087,1090,1095,1098,1103,1106,1111,1114,1119,1122,1127,1130,1135,1138,1143,1146,1151,1154,1159,1162,1207,1210,1215,1218,1223,1226,1231,1234,1239,1242,1247,1250,1252,1257,1260,1265,1268,1274,1277,1282,1285,1290,1293,1298,1301,1306,1309,1314,1317,1322,1325,1330,1333,1371,1374,1375,1380,1383,1384,1389,1392,1397,1400,1406,1409,1416,1419,1426,1429,1436,1439,1446,1449,1452,1457,1460,1467,1470,1482,1485,1494,1497,1504,1507,1524,1527,1535,1538,1545,1548,1556,1559,1569,1572,1579,1582,1589,1592,1599,1602,1609,1612,1619,1622,1630,1633,1642,1645,1652,1655,1667,1670,1680,1683,1690,1693)");

		//				WHERE pk_kandidatid = $kandidat_id AND pk_projectid IN (4,17,32,56,63,70,75,80,85,93,110,116,119,129,132,136,139,143,146,150,153,170,173,177,180,184,187,191,203,206,210,213,225,228,233,236,260,263,278,281,288,291,292,297,300,305,308,315,318,330,333,338,341,351,354,359,362,372,375,382,385,396,399,401,418,421,424,425,433,436,444,447,454,457,458,488,491,493,499,502,507,510,515,520,523,528,531,536,539,542,547,550,555,558,563,566,567,568,577,580,585,588,593,596,597,598,604,607,608,609,614,617,618,619,626,629,631,637,640,642,647,650,651,656,659,664,667,672,675,680,683,688,691,695,696,697,702,705,714,717,814,819,822,823,826,829,853,856,861,864,885,888,893,896,901,904,909,912,917,920,925,928,933,936,942,945,950,953,959,962,964,987,990,999,1002,1007,1010,1015,1018,1023,1026,1031,1034,1039,1042,1047,1050,1055,1058,1063,1066,1071,1074,1079,1082,1087,1090,1095,1098,1103,1106,1111,1114,1119,1122,1127,1130,1135,1138,1143,1146,1151,1154,1159,1162,1207,1210,1215,1218,1223,1226,1231,1234,1239,1242,1247,1250,1252,1257,1260,1265,1268,1274,1277,1282,1285,1290,1293,1298,1301,1306,1309,1314,1317,1322,1325,1330,1333,1371,1374,1375,1380,1383,1384,1389,1392,1397,1400,1406,1409,1416,1419,1426,1429,1436,1439,1446,1449,1452,1457,1460,1467,1470,1482,1485,1494,1497,1504,1507,1524,1527,1535,1538,1545,1548,1556,1559,1569,1572,1579,1582,1589,1592,1599,1602,1609,1612,1619,1622,1630,1633,1642,1645,1652,1655,1667,1670,1680,1683,1690,1693)");
		// (project_name LIKE '%cast%' OR project_name LIKE '%zavrs%')
		$check_projekat->execute(); 
		$broj_projekata = $check_projekat->rowCount();
		if($broj_projekata == 0){
			?>
			<tr>
				<td><?php echo $br++;?></td>
				<td><?php echo $kandidat_id;?></td>
				<td><?php echo $kandidat_ime." ".$kandidat_prezime;?></td>
				<td><?php echo $kandidat_mobitel;?></td>
				<td><?php echo "grupa".$kandidat_group."gg";?></td>
				<td>
				<?php
					/*$curl = curl_init();
					
					curl_setopt_array($curl, array(
					  CURLOPT_URL => "https://ej8w3r.api.infobip.com/omni/1/advanced",
					  CURLOPT_RETURNTRANSFER => true,
					  CURLOPT_ENCODING => "",
					  CURLOPT_MAXREDIRS => 10,
					  CURLOPT_TIMEOUT => 30,
					  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					  CURLOPT_CUSTOMREQUEST => "POST",
					  CURLOPT_POSTFIELDS => "{ \"scenarioKey\":\"7A32331B2103607D2F890C04FEB34942\", \"destinations\":[ { \"to\":{ \"phoneNumber\":\"".$kandidat_mobitel."\" } } ], \"sms\":{ \"text\":\"".$text_sms."\" }, \"viber\":{ \"text\":\"".$text_viber."\", \"imageURL\":\"https://jobstep-app.com/images/jobstepTiTraziPosao.png\", \"buttonText\":\"Prijavi se!\", \"buttonURL\":\"".$link."\", \"isPromotional\":\"true\" } }",
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
					}*/
				?>
				
				</td>
			</tr>
			<?php
			
		}
	}
	 ?>
	</table>
	<?php
	
	/*********SLANJE NOTIFIKACIJE ZA KANDIDATE U KONTROLI KOJI NEMAJU UNESEN DATUM ROĐENJA START *****************/
	/* ?>
	<table>
	<th>#</th>
	<th>kan_id</th>
	<th>ime</th>
	<th>mobitel</th>
	<th>datum_rod</th>
	<th>status mess</th>
	<th>status obrade</th>
	<?php
	$br = 1;
	$query = $db->prepare("
							SELECT kandidat_id, kandidat_ime, kandidat_prezime, kandidat_mobitel, kandidat_status_messenger
							FROM idk_kandidati 
							WHERE kandidat_status = 2 AND (kandidat_status_messenger = 2 OR kandidat_status_messenger = 5) 
						");
	$query->execute();
	
	while($row=$query->fetch()){
		$kandidat_id = $row['kandidat_id'];
		$kandidat_ime = $row['kandidat_ime'];
		$kandidat_prezime = $row['kandidat_prezime'];
		$kandidat_mobitel = $row['kandidat_mobitel'];
		$kandidat_status = $row['kandidat_status'];
		$kandidat_status_messenger = $row['kandidat_status_messenger'];
		$kandidat_datumrodjenja = $row['kandidat_datumrodjenja'];
		
		switch($kandidat_status){
			case 0: $status_obrade = "na provjeri"; break;
			case 1: $status_obrade = "u obradi"; break;
			case 2: $status_obrade = "obraden"; break;
			case 4: $status_obrade = "kontrola"; break;
			case 5: $status_obrade = "dopuna"; break;
			case 6: $status_obrade = "odbio_mes_obrada"; break;
			case 7: $status_obrade = "obrada>3"; break;
			case 8: $status_obrade = "dopuna>3"; break;
		}
		switch($kandidat_status_messenger){
			case 0: $status_messenger = "stari kandidati"; break;
			case 1: $status_messenger = "na cekanju"; break;
			case 2: $status_messenger = "logovan"; break;
			case 3: $status_messenger = "odbio messenger"; break;
			case 4: $status_messenger = "cetvorka"; break;
			case 5: $status_messenger = "nije zavrsio obradu3"; break;
			case 6: $status_messenger = "nije zavrsio dopunu3"; break;
		}
		
		$query_user = $db->prepare("
							SELECT id, token, type, dipl_obavijest, notifikacije, notf_new_year
							FROM users
							WHERE kandidat_id = $kandidat_id ");

		$query_user->execute();
		
		$row_user = $query_user->fetch();
		$user_id = $row_user['id'];
		$token = base64_decode($row_user['token']);
		$type = $row_user['type'];
		$dipl_obavijest = $row_user['dipl_obavijest'];
		$notf_new_year = $row_user['notf_new_year'];
		
		if($type == "android"){
			$result = send_bot_notification_android($token, "Sretnu i uspješnu Novu godinu Vam želi Jobstep tim!");
			$sms_data_object = json_decode($result, true);
			$not_dipl_prosla = $sms_data_object['success'];
					
			//USLOV TRECI: DA LI JE PROSLA NOTIFIKACIJA
			if($not_dipl_prosla == 1){
				$notifikacije = 1;
			}else{
				$notifikacije = 3;
			}
		}else{
			send_bot_notification_ios($token, "Sretnu i uspješnu Novu godinu Vam želi Jobstep tim!");
			$notifikacije = 4;
		}
		
		$users_prosla = $db->prepare("
					UPDATE users
					SET notf_new_year = :notf_new_year
					WHERE id = :id
		");
		$users_prosla->execute(array(
					'id' => $user_id,
					'notf_new_year' => $notifikacije
					));
		
		?>
		<tr>
			<td><?php echo $br++;?></td>
			<td><?php echo $kandidat_id;?></td>
			<td><?php echo $kandidat_ime." ".$kandidat_prezime;?></td>
			<td><?php echo $kandidat_mobitel;?></td>
			<td><?php echo "s_m".$kandidat_status_messenger;?></td>
			<td><?php echo $status_messenger;?></td>
			<td><?php echo "notf".$notf_new_year;?></td>
		</tr>
		<?php
	}
	 ?>
	</table>
	*/
	/*********SLANJE NOTIFIKACIJE ZA KANDIDATE U KONTROLI KOJI NEMAJU UNESEN DATUM ROĐENJA END *****************/
	
	
	
	//// drzave za viber BiH, Srbija, Hrvatska, Crna Gora, Slovenija i Makedonija
	
	/*************** testna slanja poruka *********************/
	
	//AMMAR "38761920215"
	//ja "38761938892"
	//ADO "38763453196"
	//DEBELA "38762926573"
	//ENES "387603540815"
	//Denis "38761210765"
	/*$sender = "Jobstep";
	$brojevi = array("38762926573", "38761938892");*/
	//$broj = str_replace("+","",$poc_broj);
	
	// $btn_text = "DALJE";
	// $link = "https://jobstep-app.com/medicinari_prijava.php";
	// $text_sms = 'Dobar Vam dan! Imamo odlicne vijesti za Vas! U Sarajevo dolazi njemacki poslodavac koji je u potrazi za VECIM BROJEM KANDIDATA medicinskih tehnicara! Ako ste zainteresirani da dodete na razgovor u Sarajevo i zaposlite se u Njemackoj posjetite ovaj link za vise detalja: '.$link ;
	// $text_viber = 'Dobar Vam dan! Nadamo se da ste dobro danas! Imamo odlične vijesti za Vas! U Sarajevo dolazi njemački poslodavac koji je u potrazi za VEĆIM BROJEM KANDIDATA medicinskih tehničara! Da li ste zainteresirani da dođete na razgovor u Sarajevo i zaposlite se u Njemačkoj?' ;
	/*
	foreach($brojevi as $broj){
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://ej8w3r.api.infobip.com/omni/1/advanced",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => "{ \"scenarioKey\":\"7A32331B2103607D2F890C04FEB34942\", \"destinations\":[ { \"to\":{ \"phoneNumber\":\"".$broj."\" } } ], \"sms\":{ \"text\":\"".$text_sms."\" }, \"viber\":{ \"text\":\"".$text_viber."\", \"imageURL\":\"https://jobstep-app.com/images/viber_posl.jpg\", \"buttonText\":\"DA\", \"buttonURL\":\"".$link."\", \"isPromotional\":\"true\" } }",
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
	*//*
	//testni SMS
	foreach($brojevi as $broj){
		$sender = "JOBSTEP";
		//$text = "Poznajete nekog ko zeli raditi u Njemackoj? Podijeli mu oglas i za svakog zaposlenog kandidata zaradi do 500 €! Vise na linku: https://bit.ly/3cOYk7L";
		
		//$broj = str_replace("+","",$phone_f);
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://ej8w3r.api.infobip.com/sms/1/text/single",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => "{ \"from\":\"".$sender."\", \"to\":\"".$broj."\", \"text\":\"".$text_sms."\" }",
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
		echo "<br>".$broj."<br>";
		curl_close($curl);
	}*/
	
	/* SLANJE ZA MEDICINSKE TEHNICARE -  novi poslodavac u sarajevu*/
	/*
	$text_sms = 'Dobar Vam dan! Imamo odlicne vijesti za Vas! U Sarajevo dolazi njemacki poslodavac koji je u potrazi za VECIM BROJEM KANDIDATA medicinskih tehnicara! Ako ste zainteresirani da dodete na razgovor u Sarajevo i zaposlite se u Njemackoj posjetite ovaj link za vise detalja: '.$link ;
	$text_viber = 'Dobar Vam dan! Nadamo se da ste dobro danas! Imamo odlične vijesti za Vas! U Sarajevo dolazi njemački poslodavac koji je u potrazi za VEĆIM BROJEM KANDIDATA medicinskih tehničara! Da li ste zainteresirani da dođete na razgovor u Sarajevo i zaposlite se u Njemačkoj?' ;
	$q_get_all_cand = $db->prepare("
						SELECT kandidat_id, kandidat_ime, kandidat_prezime, kandidat_mobitel 
						FROM idk_kandidati 
						WHERE kandidat_group = 2
						AND kandidat_status != 3
						AND kandidat_zaposlen_kod IS NULL
						AND kandidat_mobitel LIKE '+387%' 
						AND kandidat_id BETWEEN 1 AND 10003"
	);
	$q_get_all_cand->execute();
	$ct = 1;
	?> 
	<table>
	<?php
	while($row_cand = $q_get_all_cand->fetch()){
		
		$kandidat_id = $row_cand['kandidat_id'];
		$kandidat_ime = $row_cand['kandidat_ime'];
		$kandidat_prezime = $row_cand['kandidat_prezime'];
		$kandidat_mobitel = $row_cand['kandidat_mobitel'];
		$link = "https://jobstep-app.com/medicinari_prijava/".$kandidat_id;
		
		
		//ZABRANJENI PROJEKTI: 
		// 	521,628
		$check_projekat = $db->prepare("
				SELECT pk_projectid
				FROM idk_project_kandidati
				WHERE pk_kandidatid = $kandidat_id AND pk_projectid IN (521, 618)");

		$check_projekat->execute(); 
		$broj_projekata = $check_projekat->rowCount();
		if($broj_projekata == 0){
		
			?>
			<tr>
			<td><?php echo  $ct++; ?> </td>
			<td><?php echo  $kandidat_id; ?> </td>
			<td><?php echo  $kandidat_ime; ?> </td>
			<td><?php echo  $kandidat_prezime; ?> </td>
			<td><?php echo  $kandidat_mobitel; ?> </td>
			<td>
			<?php
			
			$curl = curl_init();

			curl_setopt_array($curl, array(
			  CURLOPT_URL => "https://ej8w3r.api.infobip.com/omni/1/advanced",
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 30,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => "POST",
			  CURLOPT_POSTFIELDS => "{ \"scenarioKey\":\"7A32331B2103607D2F890C04FEB34942\", \"destinations\":[ { \"to\":{ \"phoneNumber\":\"".$kandidat_mobitel."\" } } ], \"sms\":{ \"text\":\"".$text_sms."\" }, \"viber\":{ \"text\":\"".$text_viber."\", \"imageURL\":\"https://jobstep-app.com/images/viber_posl.jpg\", \"buttonText\":\"DA\", \"buttonURL\":\"".$link."\", \"isPromotional\":\"true\" } }",
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
		}
		?>
		</td></tr>
		<?php
	}
	?>
	</table>
	<?php
	*/
	/* SLANJE ZA PARTNER SVIMA */
	/*$link = "https://job-step.net/partners/jobstep-partner-app/6";
	$text_viber = 'Poznaješ osobe koje žele u Njemačku?\nPodijeli im naš oglas, mi ćemo ih zaposliti, a ti ostvaruješ zaradu do 500 € za svakog zaposlenog kandidata!' ;
	$text_sms = "Poznajete nekog ko zeli raditi u Njemackoj? Podijeli mu oglas i za svakog zaposlenog kandidata zaradi do 500 €! Vise na linku: https://bit.ly/3cOYk7L";
	$q_get_all_cand = $db->prepare("
						SELECT kandidat_id, kandidat_ime, kandidat_prezime, kandidat_mobitel 
						FROM idk_kandidati 
						WHERE kandidat_mobitel NOT IN (SELECT jp_brtelefona FROM idk_jobstep_partners WHERE jp_brtelefona IS NOT NULL ) 
						AND kandidat_status != 3
						AND kandidat_mobitel LIKE '+381%' 
						AND kandidat_id BETWEEN 5866 AND 12506"
	);
	$q_get_all_cand->execute();
	$ct = 1;
	?> 
	<table>
	<?php
	while($row_cand = $q_get_all_cand->fetch()){
		
		$kandidat_id = $row_cand['kandidat_id'];
		$kandidat_ime = $row_cand['kandidat_ime'];
		$kandidat_prezime = $row_cand['kandidat_prezime'];
		$kandidat_mobitel = $row_cand['kandidat_mobitel'];
		
		?>
		<tr>
		<td><?php echo  $ct++; ?> </td>
		<td><?php echo  $kandidat_id; ?> </td>
		<td><?php echo  $kandidat_ime; ?> </td>
		<td><?php echo  $kandidat_prezime; ?> </td>
		<td><?php echo  $kandidat_mobitel; ?> </td>
		<td>
		<?php
		
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://ej8w3r.api.infobip.com/omni/1/advanced",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => "{ \"scenarioKey\":\"7A32331B2103607D2F890C04FEB34942\", \"destinations\":[ { \"to\":{ \"phoneNumber\":\"".$kandidat_mobitel."\" } } ], \"sms\":{ \"text\":\"".$text_sms."\" }, \"viber\":{ \"text\":\"".$text_viber."\", \"imageURL\":\"https://jobstep-app.com/images/viber_partner_all.jpg\", \"buttonText\":\"Više informacija\", \"buttonURL\":\"".$link."\", \"isPromotional\":\"true\" } }",
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
		?>
		</td></tr>
		<?php
	}
	?>
	</table>
	<?php
	*/
	/* SLANJE ZA PARTNER PO GRUPAMA
	SELECT kandidat_group, kg.kg_title, COUNT(kandidat_group) AS broj_kandidata FROM idk_kandidati JOIN idk_kandidati_grupe kg ON kandidat_group = kg.kg_id WHERE kandidat_status != 3 AND (kandidat_mobitel LIKE '%+389%' OR kandidat_mobitel LIKE '%+386%' OR kandidat_mobitel LIKE '%+382%' OR kandidat_mobitel LIKE '%+385%' OR kandidat_mobitel LIKE '%+381%' OR kandidat_mobitel LIKE '%+387%') GROUP BY kandidat_group ORDER BY COUNT(kandidat_group) DESC
	*/
	
	
	/*********** pocetak - slanje viber poruka za parnter app *******************/
	/*
	$query = $db->prepare("
                        SELECT kandidat_id, kandidat_ime, kandidat_prezime, kandidat_mobitel, kandidat_group
						FROM idk_kandidati
						WHERE kandidat_status != 3
						AND (kandidat_mobitel LIKE '%+389%' OR kandidat_mobitel LIKE '%+386%' OR kandidat_mobitel LIKE '%+382%' OR kandidat_mobitel LIKE '%+385%' OR kandidat_mobitel LIKE '%+381%' OR kandidat_mobitel LIKE '%+387%') 
						ORDER BY kandidat_id
						");

    $query->execute();	
	$ct = 1;
	?> 
	<table>
	<?php
	while($row = $query->fetch()){
		
		$kandidat_id = $row['kandidat_id'];
		$kandidat_ime = $row['kandidat_ime'];
		$kandidat_prezime = $row['kandidat_prezime'];
		$kandidat_datumrodjenja = $row['kandidat_datumrodjenja'];
		$kandidat_mobitel = $row['kandidat_mobitel'];
		$kandidat_group = $row['kandidat_group'];
		
		//check partnere
		$query_p = $db->prepare("
								SELECT jp_id FROM idk_jobstep_partners WHERE jp_brtelefona = :jp_brtelefona AND jp_id > 200
								");
		$query_p->execute(array(
				':jp_brtelefona' => $kandidat_mobitel
		));
		
		
		if($query_p->rowCount() > 0){
			$row_p = $query_p->fetch();
			$partner_id = $row_p['jp_id'];
		}else{
			$partner_id = "nema_partnera";
		}
		
		?>
		<tr>
		<td><?php echo  $ct++; ?> </td>
		<td><?php echo  $kandidat_id; ?> </td>
		<td><?php echo  $kandidat_ime; ?> </td>
		<td><?php echo  $kandidat_prezime; ?> </td>
		<td><?php echo  $kandidat_mobitel; ?> </td>
		<td><?php echo  "grupa-".$kandidat_group."."; ?> </td>
		<td><?php echo  $partner_id; ?> </td>
		<td>
		<?php
	
		$text_viber = 'Poznaješ automehaničare koji žele u Njemačku?\n\nPodijeli im naš oglas, mi ćemo ih zaposliti, a ti ostvaruješ zaradu od 200€ za svakog zaposlenog automehaničara!';
		$text_viber_m = 'Poznaješ medicinare koji žele u Njemačku?\n\nPodijeli im naš oglas, mi ćemo ih zaposliti, a ti ostvaruješ zaradu od 500€ za svakog zaposlenog medicinara!';
		$text_sms = 'Poznajes automehanicare koji zele u Njemacku?  Podijeli im nas oglas, mi cemo ih zaposliti, a ti ostvarujes zaradu od 200 eura za svakog zaposlenog automehanicara! job-step.net/partners/automechaniker';
		$text_sms_m = 'Poznajes medicinare koji zele u Njemacku?  Podijeli im nas oglas, mi cemo ih zaposliti, a ti ostvarujes zaradu od 500 eura za svakog zaposlenog medicinara! job-step.net/partners/mediziner';
		$btn_text = "DALJE";
		$link = "https://job-step.net/partners/automechaniker";
		$link_m = "https://job-step.net/partners/mediziner";
		*/
		// -------------AUTOMEHANICARI
		//if($kandidat_mobitel == "38761938892"){
			/*$curl = curl_init();
			
			curl_setopt_array($curl, array(
			  CURLOPT_URL => "https://ej8w3r.api.infobip.com/omni/1/advanced",
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 30,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => "POST",
			  CURLOPT_POSTFIELDS => "{ \"scenarioKey\":\"7A32331B2103607D2F890C04FEB34942\", \"destinations\":[ { \"to\":{ \"phoneNumber\":\"".$kandidat_mobitel."\" } } ], \"sms\":{ \"text\":\"".$text_sms."\" }, \"viber\":{ \"text\":\"".$text_viber."\", \"imageURL\":\"https://jobstep-app.com/images/viber_slika_parnter_autom.jpg\", \"buttonText\":\"".$btn_text."\", \"buttonURL\":\"".$link."\", \"isPromotional\":\"true\" } }",
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
			*/
		//}else{}
		
		// --------------MEDICINARI
		//if($kandidat_mobitel == "38761938892"){
			/*$curl = curl_init();
			
			curl_setopt_array($curl, array(
			  CURLOPT_URL => "https://ej8w3r.api.infobip.com/omni/1/advanced",
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 30,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => "POST",
			  CURLOPT_POSTFIELDS => "{ \"scenarioKey\":\"7A32331B2103607D2F890C04FEB34942\", \"destinations\":[ { \"to\":{ \"phoneNumber\":\"".$kandidat_mobitel."\" } } ], \"sms\":{ \"text\":\"".$text_sms_m."\" }, \"viber\":{ \"text\":\"".$text_viber_m."\", \"imageURL\":\"https://jobstep-app.com/images/viber_slika_parnter_medicinar.png\", \"buttonText\":\"".$btn_text."\", \"buttonURL\":\"".$link_m."\", \"isPromotional\":\"true\" } }",
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
			*/
		//}else{}
	/*}
	?>
	</td></tr>
	</table>
	<?php
	*/
	// ------------ kraj - slanje viber poruka za parnter app ----------------
	
	
	/*******testni za dipl*********/
	/*$kandidat_id = 11585;
	$link = "https://jobstep-app.com/info/";
	$broj = "38761938892";
	$text_sms = 'U Njemacku i bez B1 certifikata? Kako? Vise detalja na slijedecem linku'.$link ;
	$text_viber = 'U Njemačku i bez B1 certifikata? Kako?\n\nViše detalja na slijedećem linku:\n\n'.$link.'' ;
	$curl = curl_init();
	
	curl_setopt_array($curl, array(
	  CURLOPT_URL => "https://ej8w3r.api.infobip.com/omni/1/advanced",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 30,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_POSTFIELDS => "{ \"scenarioKey\":\"7A32331B2103607D2F890C04FEB34942\", \"destinations\":[ { \"to\":{ \"phoneNumber\":\"".$broj."\" } } ], \"sms\":{ \"text\":\"".$text_sms."\" }, \"viber\":{ \"text\":\"".$text_viber."\", \"imageURL\":\"https://jobstep-app.com/images/dipl_viber_bj.png\", \"buttonText\":\"Više informacija\", \"buttonURL\":\"".$link."\", \"isPromotional\":\"true\" } }",
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
	*/
	
	/*************** generisanje scenarija *********************/
	/* key: 7A32331B2103607D2F890C04FEB34942 */
	/*$curl = curl_init();
	  curl_setopt_array($curl, array(
	  CURLOPT_URL => "https://ej8w3r.api.infobip.com/omni/1/scenarios",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 30,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "POST",
	  //CURLOPT_POSTFIELDS => "{ \"name\":\"Test SMS or Viber\", \"flow\": [ { \"from\": \"InfoSMS\", \"channel\": \"SMS\" }, { \"from\": \"Jobstep\", \"channel\": \"VIBER\" } ], \"default\": false }",
	  CURLOPT_POSTFIELDS => "{ \"name\":\"Test SMS or Viber\", \"flow\": [ { \"from\":\"".$sender."\", \"channel\": \"VIBER\" }, { \"from\": \"JOBSTEP\", \"channel\": \"SMS\" } ], \"default\": true }",
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
	*/
	//"{ \"name\":\"Test SMS or Viber\", \"flow\": [ { \"from\":\"".$sender."\", \"channel\": \"VIBER\" }, { \"from\": \"JOBSTEP\", \"channel\": \"SMS\" } ], \"default\": false }"
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
/* staro enesovo
	echo '<b> TEST IOS </b>';
	$token = base64_decode("ZFB6VlZoVzY2aUE6QVBBOTFiSDAzSm01VDZvVXhLbHVVNDBCRVFQbmdrMDg5TDBTZ2U1dXFBSV8wcUx4aDBKMk43OXMwWW05bF9oSldyTXozOVNzSF9BeWFPQmNxWjFFXzAzcVdNQzU3ZTNCeG5RRS1DcTlHMWFpUC0yR2E4RHNjeWY4Z1NuMDZvTjNiQXZ1TnZoT3JMM1I=");
	$result = send_bot_notification_ios($token, "Kompletirajte svoju prijavu");
	
	$success_notification_object = json_decode($result, true);
	$success_notification = intval($success_notification_object['success']);
	echo '<pre>';print_r ($result);echo '</pre>';
	 
	if($success_notification == 1)
		echo 'ISPRAVNO INSTALIRANA APP I STIGLA NOTIFIKACIJA';
	else
		echo 'NIJE DOBAR TOKEN I DEINSTALIRANA APLIKACIJA';	
	
	echo '<br /><br /><br /><b> TEST ANDROID </b>';
	$token = base64_decode("Zm5ocjRjaVMya1E6QVBBOTFiRW00bFdNREdCVm5ubExCMWctZFJEbTIwTDdUd2EtY2txbG5md3RiZWFmOFBpQU9sbFpPTXppSFNUWFEwOGM0LWEyN2luZzdGa3JVN0tubGxNZVVTN3U3dzlFY0tBVkhpMHVqR09UQVdMSFJUdTRlXzctekhUdkl3RlRkaG5YTE9mWkNDN3o=");
	$result = send_bot_notification_android($token, "Kompletirajte svoju prijavu");
	
	$success_notification_object = json_decode($result, true);
	$success_notification = intval($success_notification_object['success']);
	echo '<pre>';print_r ($result);echo '</pre>';
	 
	 
	if($success_notification == 1)
		echo 'ISPRAVNO INSTALIRANA APP I STIGLA NOTIFIKACIJA';
	else
		echo 'NIJE DOBAR TOKEN I DEINSTALIRANA APLIKACIJA';
exit();
$url = 'https://chatapi.viber.com/pa/set_webhook';
$jsonData='{ 
	"auth_token": "4b2bdfbb2667d2a4-62ea11be040b4088-522a78cc7bce2de",
	"url": "https://job-step.net"
}';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
$result = curl_exec($ch);
*/
?>