<?php

    include("includes/functions.php");
	
	$danas = date('Y-m-d H:i:s');
	$datum1 = date("Y-m-d H:i:s", strtotime("-10 days"));
	$datum2 = date("Y-m-d H:i:s", strtotime("-1 days"));
	
    $query = $db->prepare("
                        SELECT kandidat_id, kandidat_ime, kandidat_prezime, kandidat_datetime, kandidat_prijava_na
                        FROM idk_kandidati
                        WHERE (kandidat_datetime BETWEEN :datum AND :juce) AND kandidat_status_messenger = 1 AND kandidat_status = 0 ");

    $query->execute(array(
						':datum' => $datum1,
						':juce' => $datum2
	));
	
	while($row = $query->fetch()){
		
		$kandidat_id = $row['kandidat_id'];
		$kandidat_prijava_na = $row['kandidat_prijava_na'];	
		
		//PROVJERA KOLIKO SE PUTA SLAO SMS SA POZIVOM NA INSTALACIJU
		$query_check = $db->prepare("
						SELECT COUNT(mo_id) as broj_obavijesti
							FROM idk_messenger_obavijesti
							WHERE mo_kandidat_id = :mo_kandidat_id AND mo_tip = :mo_tip
		");
		$query_check->execute(array(
						':mo_kandidat_id' => $kandidat_id,
						':mo_tip' => 1));
						
		$row_check = $query_check->fetch();
		$broj_obavijesti = $row_check['broj_obavijesti'];
		//echo "<br/>broj obavijesti: ".$broj_obavijesti."<br/>";
		if($broj_obavijesti < 3){
			
			//UBACIVANJE PODATAKA O SLANJU
			$query_insert = $db->prepare("
							INSERT INTO idk_messenger_obavijesti
								(mo_kandidat_id, mo_tip, mo_datum_slanja)
							VALUES
								(:mo_kandidat_id, :mo_tip, :mo_datum_slanja)");

			$query_insert->execute(array(
							':mo_kandidat_id' => $kandidat_id,
							':mo_tip' => 1,
							':mo_datum_slanja' => $danas));
			
			//KUPLJENJE BROJA NA KOJI SE SALJE SMS
			$query_phone = $db->prepare("
								SELECT phone
								FROM users
								WHERE kandidat_id = :kandidat_id");

			$query_phone->execute(array(
								':kandidat_id' => $kandidat_id
			));
			$row_phone = $query_phone->fetch();
			$phone = $row_phone['phone'];
			$phone_f = str_replace("+", '', $phone);
			//echo "<br/>".$phone;
			sendViberToCandidateAgain($phone_f);
			//echo "<br/>".$ct++.". ".$kandidat_id."---".$phone;
			//sendSmsToCandidateAgain($phone, $kandidat_prijava_na);
			//sendSmsToCandidateAgainNTH($phone, $kandidat_prijava_na);
			
		}else{
			//AKO JE VEC TRI PUTA POSLAN SMS PREBACITI STATUSE KANDIDATA
			$updateCandidatStatus = $db->prepare("
						UPDATE idk_kandidati
						SET	kandidat_status = :kandidat_status, kandidat_status_messenger = :kandidat_status_messenger
						WHERE kandidat_id = :kandidat_id
						");

			$updateCandidatStatus->execute(array(
						':kandidat_status' => 6,
						':kandidat_status_messenger' => 3,
						':kandidat_id' => $kandidat_id
						));
		}
	}
	

?>
