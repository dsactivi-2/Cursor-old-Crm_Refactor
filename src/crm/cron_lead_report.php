<?php

    include("includes/functions.php");
    include("includes/head.php");
	//echo "wrr";
	$datum = date('d.m.Y', strtotime(" - 1 days"));
	//$datum = date('d.m.Y', strtotime("2021-10-04"));
	$datum_poct = date('Y-m-d 00:00:00', strtotime($datum));
	//$datum_poct = date('2021-01-08 00:00:00', strtotime($datum." - 1 days"));
	$datum_kraj = date('Y-m-d 23:59:59', strtotime($datum));
	
	//echo $datum_poct."<br>".$datum_kraj."<br>".$datum."<br>";
	
	//$broj = "38761938892";
	//$broj = "38763453196"; //marketing-poslovni
	//$broj = "38761210765"; //denis
	//$broj = "387603427107"; //faris
	//$broj = "38763295064"; //adil
	//$broj = "38763722704"; //???
	//$broj = "387603390608"; //arman
	//$broj = "38763722705"; //minka
	$brojevi = array('38761938892', '38763453196', '38761210765', '387603427107', '38763722704','38763722705', '38763295064', '38763995659');
	// $brojevi = array('38761938892');
	$text_sms = 'Broj leadova za dan '.$datum.': ';
	//$text_viber = 'Broj leadova za dane 08.01-10.01: \n';
	$text_viber = 'Broj leadova za dan '.$datum.': \n';
	
    $query = $db->prepare("
						SELECT kam.kd_naziv, kam.kd_skraceni_naziv, COUNT(id_broj_nd_kandidata) as broj_l, kd_jezik_forme 
						FROM idk_nd_kandidata 
						JOIN idk_kampanje_dipl kam ON kampanja_id = kam.kd_id 
						WHERE (vrijeme_kreiranja_nd_kandidata BETWEEN :datum_poct AND :datum_kraj) AND kampanja_id IS NOT NULL GROUP BY kampanja_id
						");

    $query->execute(array(
					':datum_poct' => $datum_poct,
					':datum_kraj' => $datum_kraj
	));
	
	
	$row_number = $query->rowCount();
	if($row_number == 0){
		$text_viber = 'Nema leadova za dan '.$datum;
		$text_sms = 'Nema leadova za dan'.$datum;
	}else{
		$ukupno = 0;
		$ukupno_bih = 0;
		$ukupno_srb = 0;
		//echo 'Broj leadova za dan '.$datum.': <br>';
		while($row = $query->fetch()){
			//echo "asdasd<br>";
			//$naziv_kampanje = $row['kd_naziv'];
			$naziv_kampanje = $row['kd_skraceni_naziv'];
			$broj_leadova = $row['broj_l'];
			$kd_jezik_forme = $row['kd_jezik_forme'];
			if($kd_jezik_forme == "bs"){
				$ukupno_bih += $broj_leadova;
			}elseif($kd_jezik_forme == "rs"){
				$ukupno_srb += $broj_leadova;
			}
			$text_viber .= '\n'.$naziv_kampanje.': '.$broj_leadova;
			$ukupno += $broj_leadova;
			echo '<br>'.$naziv_kampanje.': '.$broj_leadova;
		}
		$text_viber .= '\n\nUkupno BiH: '.$ukupno_bih;
		$text_viber .= '\nUkupno Srbija: '.$ukupno_srb;
		$text_viber .= '\nUkupno: '.$ukupno;
		$text_sms .= $ukupno;
		// echo '<br><br> Ukupno BiH: '.$ukupno_bih;
		// echo '<br> Ukupno Srbija: '.$ukupno_srb;
		// echo '<br> Ukupno: '.$ukupno;
	}
	
	
	//ponovne prijave
	$text_sms_pp = 'Broj ponovnih prijava za dan '.$datum.': ';
	//$text_viber = 'Broj leadova za dane 08.01-10.01: \n';
	$text_viber_pp = 'Broj ponovnih prijava za dan '.$datum.': \n';
	$query_pp = $db->prepare("
						SELECT kam.kd_naziv, kam.kd_skraceni_naziv, COUNT(id_pp) as broj_l, kd_jezik_forme 
						FROM idk_ponovne_prijave 
						JOIN idk_kampanje_dipl kam ON kampanja_pp = kam.kd_id 
						WHERE (datum_pp BETWEEN :datum_poct AND :datum_kraj) AND kampanja_pp IS NOT NULL GROUP BY kampanja_pp
						");

    $query_pp->execute(array(
					':datum_poct' => $datum_poct,
					':datum_kraj' => $datum_kraj
	));
	
	
	$row_number_pp = $query_pp->rowCount();
	
	if($row_number_pp == 0){
		$text_viber_pp = 'Nema ponovnih prijava za dan '.$datum;
		$text_sms_pp = 'Nema ponovnih prijava za dan '.$datum;
	}else{
		$ukupno_pp = 0;
		$ukupno_bih_pp = 0;
		$ukupno_srb_pp = 0;
		//echo 'Broj leadova za dan '.$datum.': <br>';
		while($row_pp = $query_pp->fetch()){
			//echo "asdasd<br>";
			//$naziv_kampanje_pp = $row_pp['kd_naziv'];
			$naziv_kampanje_pp = $row_pp['kd_skraceni_naziv'];
			$broj_leadova_pp = $row_pp['broj_l'];
			$kd_jezik_forme_pp = $row_pp['kd_jezik_forme'];
			if($kd_jezik_forme_pp == "bs"){
				$ukupno_bih_pp += $broj_leadova_pp;
			}elseif($kd_jezik_forme_pp == "rs"){
				$ukupno_srb_pp += $broj_leadova_pp;
			}
			$text_viber_pp .= '\n'.$naziv_kampanje_pp.': '.$broj_leadova_pp;
			$ukupno_pp += $broj_leadova_pp;
			echo '<br>'.$naziv_kampanje_pp.': '.$broj_leadova_pp;
		}
		$text_viber_pp .= '\n\nUkupno BiH: '.$ukupno_bih_pp;
		$text_viber_pp .= '\nUkupno Srbija: '.$ukupno_srb_pp;
		$text_viber_pp .= '\nUkupno: '.$ukupno_pp;
		$text_sms_pp .= $ukupno_pp;
	}
	
	// Kandidati
						
	$query_cand = $db->prepare("
						SELECT
							kandidat_id, kandidat_ime, kandidat_prezime, lks_datetime, lks_status_obrade, lg.lg_url, lg.lg_id
						FROM
							idk_log_kandidat_statusi
						JOIN(
							SELECT
								MAX(lks_id) AS max_log_id
							FROM
								idk_log_kandidat_statusi
							WHERE
								lks_kandidat_id != 0 AND lks_status_obrade IN(0, 9)
							GROUP BY
								lks_kandidat_id
						) max_log
						ON
							lks_id = max_log.max_log_id
						JOIN idk_link_generator lg ON
							lks_link_id = lg.lg_id AND lks_link_id IS NOT NULL AND lks_link_id != 1
						JOIN idk_nalozi ON 
							lg.lg_nalogid = idk_nalozi.nalog_id
						JOIN idk_kandidati ON 
							kandidat_id = lks_kandidat_id
						WHERE lks_datetime BETWEEN :datum_poct AND :datum_kraj AND idk_nalozi.nalog_status != 12
						");

    $query_cand->execute(array(
					':datum_poct' => $datum_poct,
					':datum_kraj' => $datum_kraj
	));
	
	
	$row_candidate_number = $query_cand->rowCount();
	if($row_candidate_number == 0){
		$text_cand_viber = 'Nema kandidata za dan '.$datum;
		$text_cand_sms = 'Nema kandidata za dan '.$datum;
	}else{
		$text_cand_viber = 'Broj kandidata '.$row_candidate_number;
		$text_cand_sms = 'Broj kandidata '.$row_candidate_number;
	}

	// DVAG kandidati
	$query_dvag = $db->prepare("
						SELECT
							kandidat_id, kandidat_ime, kandidat_prezime, lks_datetime, lks_status_obrade, lg.lg_url, lg.lg_id
						FROM
							idk_log_kandidat_statusi
						JOIN(
							SELECT
								MAX(lks_id) AS max_log_id
							FROM
								idk_log_kandidat_statusi
							WHERE
								lks_kandidat_id != 0 AND lks_status_obrade IN(0, 9)
							GROUP BY
								lks_kandidat_id
						) max_log
						ON
							lks_id = max_log.max_log_id
						JOIN idk_link_generator lg ON
							lks_link_id = lg.lg_id AND lks_link_id IS NOT NULL AND lks_link_id != 1

						JOIN idk_kandidati ON 
							kandidat_id = lks_kandidat_id
						JOIN idk_nalozi ON 
							lg.lg_nalogid = idk_nalozi.nalog_id
						JOIN idk_jobstep_partners ON 
							idk_kandidati.kandidat_partnerid = idk_jobstep_partners.jp_id
						WHERE idk_jobstep_partners.jp_partner_company = 3 AND lks_datetime BETWEEN :datum_poct AND :datum_kraj AND idk_nalozi.nalog_status != 12
						");

    $query_dvag->execute(array(
					':datum_poct' => $datum_poct,
					':datum_kraj' => $datum_kraj
	));

	$row_dvag_number = $query_dvag->rowCount();
	if($row_dvag_number == 0){
		$text_dvag_viber = 'Nema DVAG za dan '.$datum;
		$text_dvag_sms = 'Nema DVAG za dan '.$datum;
	}else{
		$text_dvag_viber = 'Broj DVAG '.$row_dvag_number;
		$text_dvag_sms = 'Broj DVAG '.$row_dvag_number;
	}

	$text_viber = $text_viber.'\n\n'.$text_viber_pp.'\n\n'.$text_cand_viber.'\n\n'.$text_dvag_viber;
	$text_sms = $text_sms.'.  '.$text_sms_pp.'.  '.$text_cand_sms.'.  '.$text_dvag_sms;
	
	$active_provider = getActiveProviderForSendingMessages(2);

	foreach($brojevi as $broj){
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
			echo "cURL Error #:" . $err;
			} else {
			echo $response;
			}
			echo "<br>".$broj."<br>";
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
			
			echo $response;
			echo "<br>".$broj."<br>";
		}

	}
	//echo "<br><hr>".$text_viber;
?>
