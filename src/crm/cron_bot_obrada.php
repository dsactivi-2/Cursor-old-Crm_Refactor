<?php

    include("includes/functions.php");
	
	$danas = date('Y-m-d H:i:s');
	$datum1 = date("Y-m-d H:i:s", strtotime("-10 days"));
	$datum2 = date("Y-m-d H:i:s", strtotime("-1 days"));
	
    $query = $db->prepare("
                        SELECT users.kandidat_id, idk_kandidati.kandidat_status, idk_kandidati.kandidat_ime, idk_kandidati.kandidat_prezime, idk_kandidati.kandidat_status
						FROM users
						JOIN idk_kandidati
						ON users.kandidat_id = idk_kandidati.kandidat_id
						WHERE (users.last_online BETWEEN :datum AND :juce) AND (idk_kandidati.kandidat_status = 5 OR idk_kandidati.kandidat_status = 1) AND idk_kandidati.kandidat_status_messenger = 2
						");

    $query->execute(array(
						':datum' => $datum1,
						':juce' => $datum2
	));
	
	$ct = 1;
	while($row = $query->fetch()){
		
		$kandidat_id = $row['kandidat_id'];
		$kandidat_status = $row['kandidat_status'];
		if($kandidat_status == 1){
			$slijedeci_status = 7;
			$slijedeci_status_m = 5;
		}else if($kandidat_status == 5){
			$slijedeci_status = 8;
			$slijedeci_status_m = 6;
		}
		
		$query_check = $db->prepare("
						SELECT COUNT(mo_id) as broj_obavijesti
							FROM idk_messenger_obavijesti
							WHERE mo_kandidat_id = :mo_kandidat_id AND mo_tip = :mo_tip
		");
		$query_check->execute(array(
						':mo_kandidat_id' => $kandidat_id,
						':mo_tip' => 2));
						
		$row_check = $query_check->fetch();
		$broj_obavijesti = $row_check['broj_obavijesti'];
		
		if($broj_obavijesti < 3){
			
			$query_insert = $db->prepare("
							INSERT INTO idk_messenger_obavijesti
								(mo_kandidat_id, mo_tip, mo_datum_slanja)
							VALUES
								(:mo_kandidat_id, :mo_tip, :mo_datum_slanja)");

			$query_insert->execute(array(
							':mo_kandidat_id' => $kandidat_id,
							':mo_tip' => 2,
							':mo_datum_slanja' => $danas));
			
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
				send_bot_notification_android($token, "Kompletirajte svoju prijavu");
			else if($type == 'ios')
				send_bot_notification_ios($token, "Kompletirajte svoju prijavu");
			
			//sendSmsToCandidateAgain($phone, $kandidat_prijava_na);
		}else{
			//UPDATE NA STATUS NEZAVRSEN KANDIDAT
			$updateCandidatStatus = $db->prepare("
						UPDATE idk_kandidati
						SET	kandidat_status = :kandidat_status, kandidat_status_messenger = :kandidat_status_messenger
						WHERE kandidat_id = :kandidat_id
						");

			$updateCandidatStatus->execute(array(
						':kandidat_status' => $slijedeci_status,
						':kandidat_status_messenger' => $slijedeci_status_m,
						':kandidat_id' => $kandidat_id
						));
						
			//INSERT INTO LOG STATUSA
			$query_log_status = $db->prepare("
					INSERT INTO idk_log_kandidat_statusi
						(lks_kandidat_id, lks_status_obrade, lks_status_messenger, lks_datetime)
					VALUES
						(:lks_kandidat_id, :lks_status_obrade, :lks_status_messenger, :lks_datetime)
			");
			
			$query_log_status->execute(array(
					':lks_kandidat_id' => $kandidat_id,
					':lks_status_obrade' => $slijedeci_status,
					':lks_status_messenger' => $slijedeci_status_m,
					':lks_datetime' => $danas
			));
		}
		//echo $ct++."---".$kandidat_id."---".$broj_obavijesti."<br/>";
	}
	

?>
