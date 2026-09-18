<?php


	include("includes/functions.php");
	include("includes/common.php");

	echo "qwe";
	exit();

	$dipl_candidates_ids = $_GET['ids'];
	if($dipl_candidates_ids != ""){
		$dipl_candidates_array = explode(',', $dipl_candidates_ids);
		var_dump($dipl_candidates_array);

		$get_info_from_dipl = $db->prepare(
			"SELECT id_broj_nd_kandidata, ime_nd_kandidata, prezime_nd_kandidata, nivo_poznavanja_jezika, skola_nd_kandidata, skola_smjer_nd_kandidata, mobilni_nd_kandidata, email_nd_kandidata
			FROM idk_nd_kandidata WHERE id_broj_nd_kandidata IN (" . $dipl_candidates_ids . ")"
		);
		$get_info_from_dipl->execute();

		while($row_info_from_dipl = $get_info_from_dipl->fetch()){
			echo "<br>";
			// var_dump($row_info_from_dipl);

			$dipl_id 		= $row_info_from_dipl["id_broj_nd_kandidata"];
			$ime 			= $row_info_from_dipl["ime_nd_kandidata"];
			$prezime 		= $row_info_from_dipl["prezime_nd_kandidata"];
			$jezik 			= $row_info_from_dipl["nivo_poznavanja_jezika"];
			$skola_id 		= $row_info_from_dipl["skola_nd_kandidata"];
			$smjer_id 		= $row_info_from_dipl["skola_smjer_nd_kandidata"];
			$telefon 		= $row_info_from_dipl["mobilni_nd_kandidata"];
			$email	 		= $row_info_from_dipl["email_nd_kandidata"];
			
			$kandidat_check = md5(uniqid(rand(), true));

			//Add user to db - START
				$query_add_user = $db->prepare("
							INSERT INTO idk_kandidati
								(kandidat_check, kandidat_ime, kandidat_prezime, kandidat_email, kandidat_mobitel, kandidat_slika, kandidat_status, kandidat_status_messenger, kandidat_status_prijave, kandidat_datetime, kandidat_visitedurl, kandidat_prijava_na, kandidat_group, povezan_na_dipl, kandidat_porijeklo, kandidat_dipl_id )
							VALUES
								(:kandidat_check, :kandidat_ime, :kandidat_prezime, :kandidat_email, :kandidat_mobitel, :kandidat_slika, :kandidat_status, :kandidat_status_messenger, :kandidat_status_prijave, :kandidat_datetime, :kandidat_visitedurl, :kandidat_prijava_na, :kandidat_group, :povezan_na_dipl, :kandidat_porijeklo, :kandidat_dipl_id)");

				$query_add_user->execute(array(
						':kandidat_check' => $kandidat_check,
						':kandidat_ime' => $ime,
						':kandidat_prezime' => $prezime,
						':kandidat_email' => $email,
						':kandidat_mobitel' => $telefon,
						':kandidat_slika' => "none",
						':kandidat_status' => 0,
						':kandidat_status_messenger' => 0,
						':kandidat_status_prijave' => 1,
						':kandidat_datetime' => date('Y-m-d H:i:s'),
						':kandidat_visitedurl' => 1,
						':kandidat_prijava_na' => "Ostalo",
						':kandidat_group' => 7,
						':povezan_na_dipl' => 1,
						':kandidat_porijeklo' => 1,
						':kandidat_dipl_id' => $dipl_id
				));
			//Add user to db - END
		
			$kandidat_id = $db->lastInsertId();

			//LOG Status - START
				$query_log_status = $db->prepare("
						INSERT INTO idk_log_kandidat_statusi
							(lks_kandidat_id, lks_status_obrade, lks_status_messenger, lks_datetime)
						VALUES
							(:lks_kandidat_id, :lks_status_obrade, :lks_status_messenger, :lks_datetime)
				");
				
				$query_log_status->execute(array(
						':lks_kandidat_id' => $kandidat_id,
						':lks_status_obrade' => 0,
						':lks_status_messenger' => 0,
						':lks_datetime' => date('Y-m-d H:i:s')
				));
			//LOG Status - END

			//PROJECT Insert - START
				$project_id = 1269;
				$query_project = $db->prepare("
								INSERT INTO idk_project_kandidati
									(pk_projectid, pk_kandidatid)
								VALUES
									(:pk_projectid, :pk_kandidatid)"
				);

				$query_project->execute(array(
								':pk_projectid' => $project_id,
								':pk_kandidatid' => $kandidat_id
				));
			//PROJECT Insert - END
			
			addToLogsStatusPrijave(NULL, $project_id, 1, $kandidat_id, 1);

			//CONTACT INFO Insert - START
				//Add mobilni to db
				$query_mob = $db->prepare("
								INSERT INTO idk_kandidat_kontakt_info
									(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id)
								VALUES
									(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id)"
				);

				$query_mob->execute(array(
							':kki_grupa' => 1,
							':kki_naziv' => "Mobilni",
							':kki_podatak' => $telefon,
							':kki_kandidat_id' => $kandidat_id
				));

				if($email != null){
					$query_email = $db->prepare("
									INSERT INTO idk_kandidat_kontakt_info
										(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id)
									VALUES
										(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id)"
					);

					$query_email->execute(array(
								':kki_grupa' => 2,
								':kki_naziv' => "E-mail",
								':kki_podatak' => $email,
								':kki_kandidat_id' => $kandidat_id
					));
				}

				$insert_skole = copyCandidateDiplEducationInCandidateR($dipl_id, $skola_id, $smjer_id);
				var_dump($insert_skole);


				
			//CONTACT INFO Insert - END

			//JEZIK - START

			if($jezik != 0){
				switch($jezik){
					case 1:
						$kj_znanje_njemacki = "Bez znanja";
					break;
					case 2:
						$kj_znanje_njemacki = "A1";
					break;
					case 3:
						$kj_znanje_njemacki = "A2";
					break;
					case 4:
						$kj_znanje_njemacki = "B1";
					break;
					case 5:
						$kj_znanje_njemacki = "B2";
					break;
					case 6:
						$kj_znanje_njemacki = "C1";
					break;
					case 7:
						$kj_znanje_njemacki = "C2";
					break;
					default:
						$kj_znanje_njemacki = "Bez znanja";
				}
				// Add language knowlege
				$query_njem = $db->prepare("
								INSERT INTO idk_kandidat_jezici
									(kj_naziv, kj_slusanje, kj_citanje, kj_govorna_interakcija, kj_govorna_produkcija, kj_pisanje, kj_kandidatid)
								VALUES
									(:kj_naziv, :kj_slusanje, :kj_citanje, :kj_govorna_interakcija, :kj_govorna_produkcija, :kj_pisanje, :kj_kandidatid)");
		
				$query_njem->execute(array(
							':kj_naziv' => "Njemački",
							':kj_slusanje' => $kj_znanje_njemacki,
							':kj_citanje' => $kj_znanje_njemacki,
							':kj_govorna_interakcija' => $kj_znanje_njemacki,
							':kj_govorna_produkcija' => $kj_znanje_njemacki,
							':kj_pisanje' => $kj_znanje_njemacki,
							':kj_kandidatid' => $kandidat_id));
				}

			//JEZIK - END
			
		}

		// var_dump($info_fetch);
		// foreach($dipl_candidates_array as $dipl_id){
		
		// }
	}else{
		echo "prazno";
	}

	exit();

	//NTH slanje START
		$params =
						
			'{
				"channels" : [ "VIBER" ],
				"destinations" : [ {
				"phoneNumber" : "38761938892"
				} ],
				"requestId" : "REQ_ID_12345",
				"transactionId" : "TRN_ID_12345",
				"dlr" : true,
				"dlrUrl" : "http://example.com/dlr",
				"viber" : {
					"sender" : "Jobstep Int",
					"tag": "test",
					"text": "Test message",
					"ttl": 14440,
					"label": "transaction",
				}
			}'
		;

		// echo $token = base64_encode('jobstep:RWFh!StLIMq<');
		// echo"<br>";
		$curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL => 'https://msg.mobile-gw.com:9000/v1/omni-channel/message',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => $params,
		CURLOPT_HTTPHEADER => array(
			'Authorization: Basic am9ic3RlcDpSV0ZoIVN0TElNcTw=',
			'Content-Type: application/json',
			'Accept: application/json'
		),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		var_dump($response);
		var_dump($err);

		curl_close($curl);
	//NTH slanje END

	$tekst_za_slanje_sms = "Poštovani, \n\nradi bolje pristupačnosti, promenili smo lokaciju razgovora za posao. \nNova lokacija je: Hotel Elegance, Zrenjaninski put 98A, Beograd.\n\nLINK google maps: https://maps.app.goo.gl/LVy2bgdzSuXEmumf7?g_st=ic \n\nRadujemo se susretu sa Vama i želimo Vam sreću na razgovoru za posao!\n\nVaš Jobstep";
	$tekst_za_slanje_viber = "Poštovani, \n\nradi bolje pristupačnosti, promenili smo lokaciju razgovora za posao. \nNova lokacija je: Hotel Elegance, Zrenjaninski put 98A, Beograd.\n\nRadujemo se susretu sa Vama i želimo Vam sreću na razgovoru za posao!\n\nVaš Jobstep";
	$button_viber = "LINK google maps";
	$link_viber = "https://maps.app.goo.gl/LVy2bgdzSuXEmumf7?g_st=ic";
	
	// sendOnlyViber("+38763021436", $tekst_za_slanje_viber, $button_viber, $link_viber);
	sendOnlySMS("+38763021436", $tekst_za_slanje_sms);
	function sendOnlyViber($phone, $text_viber, $button_text, $link_url){
		$broj = str_replace("+","",$phone);
		$curl = curl_init();

		$params = array(
			
			"scenarioKey" => "C5BA6D5354E4296DD0BB9ACAC382C498",
			"destinations" => array(
				"to" => array(
					"phoneNumber" => $broj,
					)
			),
			"viber" => array(
				"text" => $text_viber,
				// "imageURL" => $imageURL,
				"buttonText" => $button_text,
				"buttonURL" => $link_url,
				"isPromotional" => "false"
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
		echo $err = curl_error($curl);

		curl_close($curl);

		$xmldata = json_decode($response);
		var_dump($xmldata);
	}

	function sendOnlySMS($phone, $text_sms){
		$phone = str_replace("+","",$phone);
		$curl = curl_init();

		$params = array(
			"scenarioKey" => "E351295F36C3677206F28380311E91A2",
			"destinations" => array(
				"to" => array(
					"phoneNumber" => $phone,
				)
			),
			"sms" => array(
				"text" => $text_sms,
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
		echo $err = curl_error($curl);

		curl_close($curl);

		$xmldata = json_decode($response);
		var_dump($xmldata);
	}

	exit();

	$niz_brojeva = array('+3871548247','+38761123456','+387652260778','+387601619188','+387637547887','+387605594204','+387638160477','+387604685655','+387654779810','+387656858953','+387628796153','+387658518577','+387691555988','+387604020035','+387606424622','+387638193224','+387693962309','+387613114902','+387621092687','+387628967380','+38762225122','+387628386159','+387638620445','+387601311936','+38762302755','+387604171747','+387613012784','+387631171044','+387616342475','+387629410516','+387658610467','+38766052705','+387653933369','+387641547538','+387612848008','+387656858326','+387628947127','+387638839524','+38770323407','+387628142975','+387643477286','+387628206606','+387628261900','+387611575455','+387616599629','+387637141070','+387601400322','+38763484424','+387612125362','+387381669193720','+38763655403','+387645076224','+387643952293','+387602424190','+387642066091','+387642756377','+387615575747','+38763638001','+387604640806','+387652055982','+387641452077','+387637015411','+387640519960','+387644673589','+387604919751','+387603055123','+387603374546','+387628224421','+387694363816','+387613309898','+387616315759','+38763404441','+387611486046','+38769603388','+387637210936','+387631019955','+387638697968','+387631782849','+387600350327','+387644765796','+387669040369','+387637402954','+387600997609','+387653403443','+387693126811','+387643873416','+387621428198','+38762525044','+387645799902','+387605237220','+387604221203','+387653327002','+387637761183','+387648333757','+387600172738','+38762779704','+387641436868','+387692502270','+38763369553','+387692211815','+387605510651','+387691461176','+38763350367','+387621085755','+387605107620','+38763288751','+387649112030','+387640810015','+387637142315','+387652651626','+387691910799','+387644502349','+387638044843','+387612216587','+387612804605','+387654210187','+387641339730','+387611529095','+38763656029','+387381641829106','+387631779467','+387621596420','+387641177273','+387638339539','+387605881505','+387631276722','+38766741777','+387668006622','+387605332560','+387616097741','+38762768066','+387605626016','+387621977330','+387612555245','+387616448174','+387649656361','+387600491387','+3870381642127876','+387600393042','+387642354381','+387642459102','+387644809009','+387645028629','+387653630389','+387692213844','+387659666540','+387638880574','+387652795633','+387607516517','+387607127188','+387631199807','+387612828788','+38761248685','+38763379167','+387658029931','+387638730001','+38738162565680','+387621710471','+387600134474','+387621428017','+387607151966','+387638680813','+387641467037','+38763608230','+38766541149','+387648968530','+387638325849','+387603046565','+387638882005','+387641382677','+387652237723','+387638130711','+387692552164','+38763324551','+387643174318','+387628102433','+387652509183','+387691844682','+387644665367','+387659306753','+387603291002','+387381641361994','+387631207164','+387611804675','+387919713640','+387694745602','+387652230210','+387694241510','+387612356255','+387646360952','+38763207960','+387603167550','+387652130973','+387604226517','+387652208687','+387648677866','+38763396637','+387152 19516205','+387611957321','+387614988442','+387638757362','+38766247800','+387644326073','+38763818083','+387642728361','+387605201501','+387612446314','+387604480290','+387381641222572','+387611665067','+387638956776','+387637135478','+387631800336','+387607007508','+387616937477','+387655448182','+387631515155','+387605470517','+387691223197','+387612933389','+387646146373','+3876677886691','+387641257259','+387638963235','+387604409269','+387644343656','+387613139267','+387606260201','+387641590742','+387612602228','+387605485596','+387612675093','+387605867741','+387640727463','+387616699917','+387653424054','+387603961870','+387629697580','+387758256076','+387621134422','+387644938888','+387612798051','+387630348103','+387638990435','+38763433305','+387616299839','+387616299839','+38752782274','+387612105842','+387612926868','+387691483507','+387605060221','+387642065622','+38762499230','+387628261900','+387691217122','+387601450318','+387641234552','+387621268050','+387605169128','+387605401164','+387644183612','+387600730151','+387631904094','+387648834220','+387616315638','+387658084943','+387600861962','+387613131353','+387640443870','+387612141428','+387640031789','+387611715000','+387612709421','+38766659161','+387656162877','+38766120777','+38769606760','+387631709749','+387600460601','+387646657714','+387652471644','+387644468486','+387611338909','+387628629646','+387638936461','+387604390996','+387621458190','+387637027747','+387601323097','+38766067338','+387643901346','+387656254976','+387637675257','+387652473684','+387648853666','+387643501499','+387641359210','+387611054016','+387601600366','+387612792882','+387612542566','+387612325367','+387642824499','+387644790332','+38763354991','+387628724931','+387637008937','+387641162465','+387616684326','+387629628160','+387665397239','+387641955734','+387640478622','+387607090009','+387642481772','+387691255181','+387616433255','+387606009700','+387606202422','+38761420870','+387616674204','+387641888248','+387665120512','+387637250888','+387629614348','+387631418210','+387601645333','+387600325645','+387621615399','+387691919000','+387637289561','+387669429019','+38763302767','+3871777726322','+387654501118','+387608007926','+387642070070','+387652711091','+387637347468','+387642531117','+387600768714','+387600426677','+387641783529','+387628709289','+387646156108','+387642836499','+387603448448','+387601514514','+387611896827','+387643954696','+387608318589','+387637177737','+38766345776','+38706102325366','+387629675557','+387638902393','+387612032484','+387641596372','+387600211068','+38706130299209','+387621631679','+387601444979','+387614060903','+387652715629','+387612485430','+387692406977','+387606681086','+387616856747','+387607054979','+387644720945','+38766247638','+387631126710','+387637891134','+387656851313','+387611840439','+387637628707','+387653696030','+387643263005','+387612757146','+387612013000','+387611965087','+387642643816','+387691312440','+38763448735','+387690204578','+387648701777','+38706001737372','+387621174370','+38766288729','+387644686817','+38763321866','+387642169908','+387603243246','+387652238909','+387644882630','+387615057772','+38769767478','+387693358892','+38766209166','+387616701577','+387604636578','+387638114148','+387606913660','+387638157721','+387621866','+387615525553','+387652057516','+387631717179','+387603406250','+387637123843','+387606660571','+387616682155','+387606698898','+387642703850','+387642241027','+387644906198','+3871616272424','+387616682155','+387613197976','+387641880971','+387641238363','+387612064642','+387631798331','+387616420285','+387637310411','+387645164232','+387695507301','+387643559284','+387652809664','+38763402075','+38769791141','+387600303756','+38766254026','+387637207185','+387655272658','+387606080731','+387648478104','+387605010287','+387637607733','+387631034131','+38766450610','+387691717852','+387613535509','+387645676536','+38769713996','+387641341129','+38769653711','+387628554272','+387652335514','+387641648923','+387612045953','+387658621010','+387606128585','+387642432542','+38763312733','+387615719633','+387641129942','+387652006247','+387616828358','+387642735008','+387691710990','+387640813305','+387615352317','+38763225923','+387638197755','+38762896352','+387606170899','+387638090692','+387621074887','+38766380220','+387638190100','+387616290334','+387628109182','+387604009193','+387612636931','+387621428017','+387638113151','+387614000416','+387649824800','+387603263960');

	echo '<table>';
	$ct = 1;
	$cisti_kandidati = array();
	$dupli_kandidati = array();
	$nepronadjeni = array();
	foreach($niz_brojeva as $broj){
		echo '<tr>';
		echo '<td>'.$ct++.'</td>';
		echo '<td>'.$broj.'</td>';
		$get_cand = $db->prepare("SELECT kandidat_id FROM idk_kandidati WHERE kandidat_mobitel = :kandidat_mobitel");
		$get_cand->execute(array(':kandidat_mobitel' => $broj));
		$broj_kan = $get_cand->rowCount();
		echo '<td>Broj kand -'.$broj_kan.'- </td>';
		$nadjeni = array();
		while($row_cand = $get_cand->fetch()){
			array_push($nadjeni, $row_cand['kandidat_id']);
		}
		$imp_nadjeni = implode(',', $nadjeni);
		echo '<td>'.$imp_nadjeni.' </td>';

		echo '</tr>';
		if($broj_kan == 1){
			array_push($cisti_kandidati, $imp_nadjeni);
		}elseif($broj_kan == 2){
			array_push($dupli_kandidati, $imp_nadjeni);
		}elseif($broj_kan == 0){
			array_push($nepronadjeni, $broj);
		}
	}


	echo '</table>';

	var_dump(implode(',', $cisti_kandidati));
	var_dump(implode(';', $dupli_kandidati));
	var_dump($cisti_kandidati);
	var_dump($dupli_kandidati);
	var_dump($nepronadjeni);


	exit();

	$casting_id = $_REQUEST['casting'];
	// $agent_id = $_REQUEST['agent'];

	$sql1 = 'SELECT 
                    employee_id,
                    kan.tf_reserved_agent as agent_id, 
                    kan.kandidat_visitedurl as id_urla,
					kan.kandidat_id,
                    kan.kandidat_nalog_id
                FROM idk_kandidati kan
                JOIN idk_employees emp
                ON kan.tf_reserved_agent = emp.employee_id
                INNER JOIN idk_pp_cand_appts pca ON kan.kandidat_id = pca.pca_kandidat_id  AND pca.pca_status = 1
				JOIN idk_pp_appointments pap on pap.pap_id = pca.pca_appointment_id  
				JOIN idk_link_generator link
                ON kan.kandidat_visitedurl = link.lg_id and kan.kandidat_tf_status IN (16,18)
				WHERE pap.pap_group_id IN ('.$casting_id.')
				';

	$res1 = $db->query($sql1)->fetchAll(PDO::FETCH_CLASS);
	$ct = 1;
	foreach($res1 as $kandidat){
		echo $ct++." - ".$kandidat->kandidat_id."<br>"; 
		$query = $db->prepare("
				INSERT INTO idk_tf_stats_reservations
					(tsr_candidate_id, tsr_agent_id, tsr_interview_id, tsr_nalog_id, tsr_link_id, tsr_status)
				VALUES
					(:tsr_candidate_id, :tsr_agent_id, :tsr_interview_id, :tsr_nalog_id, :tsr_link_id, :tsr_status)");

		$query->execute(array(
				':tsr_candidate_id' => $kandidat->kandidat_id,
				':tsr_agent_id' => $kandidat->agent_id,
				':tsr_interview_id' => $casting_id,
				':tsr_nalog_id' => $kandidat->kandidat_nalog_id,
				':tsr_link_id' => $kandidat->id_urla,
				':tsr_status' => 4
		));
	}
	// var_dump($res1);
	// echo $casting_id;



	exit();
	$query_cand = $db->prepare(
		"SELECT kandidat_id, kandidat_status_prijave FROM idk_kandidati 
		JOIN idk_project_kandidati pk ON kandidat_id = pk.pk_kandidatid AND pk_projectid = 3571"
	);
	$query_cand->execute();
	$ct = 1;
	while($row_cand = $query_cand->fetch()){
		echo $ct++." ".$row_cand['kandidat_id']." - ".$row_cand['kandidat_status_prijave']." - ".scriptCheckCandidateInputs($row_cand['kandidat_id'])."<br>";
	}





	function scriptCheckCandidateInputs($kandidat_id, $nalog_from_queue = null){
	
		Global $db;
		$query_kandidat = $db->prepare("
			SELECT * FROM idk_kandidati WHERE kandidat_id = $kandidat_id ");
		$query_kandidat->execute();
		$row_kandidat = $query_kandidat->fetch();
		$kandidat_vozacka = $row_kandidat['kandidat_vozacka_dozvola'];
		$kandidat_vozacka_kategorija = $row_kandidat['kandidat_vozacka_kategorija'];
		$kandidat_status_messenger = $row_kandidat['kandidat_status_messenger'];
	
		if(!isReservedByStatusPrijave($kandidat_id)){
	
			$query_kandidat_edukacija_visoko = $db->prepare("
				SELECT ke_id FROM idk_kandidat_edukacija WHERE ke_kandidat_id = $kandidat_id AND ke_vrsta_obrazovanja = 'visoko' ");
			$query_kandidat_edukacija_visoko->execute();
			$row_kandidat_edukacija_visoko = $query_kandidat_edukacija_visoko->rowCount();
	
			$query_kandidat_edukacija_ostalo = $db->prepare("
				SELECT ke_id FROM idk_kandidat_edukacija WHERE ke_kandidat_id = $kandidat_id AND ke_vrsta_obrazovanja = 'ostalo' ");
			$query_kandidat_edukacija_ostalo->execute();
			$row_kandidat_edukacija_ostalo = $query_kandidat_edukacija_ostalo->rowCount();
	
			$query_kandidat_iskustvo = $db->prepare("
				SELECT kri_id FROM idk_kandidat_radno_iskustvo WHERE kri_kandidat_id = $kandidat_id ");
			$query_kandidat_iskustvo->execute();
			$row_kandidat_iskustvo = $query_kandidat_iskustvo->rowCount();
	
			$query_kandidat_jezik = $db->prepare("
				SELECT kj_slusanje FROM idk_kandidat_jezici WHERE kj_kandidatid = $kandidat_id AND kj_naziv = 'Njemački' ");
			$query_kandidat_jezik->execute();
			$row_kandidat_jezik = $query_kandidat_jezik->fetch();
			$kandidat_jezik = $row_kandidat_jezik['kj_slusanje'];
			
			if($nalog_from_queue == null){
				$query_user = $db->prepare("
					SELECT * FROM users WHERE kandidat_id = $kandidat_id ");
				$query_user->execute();
				$row_user = $query_user->fetch();
				$nalog_id = $row_user['nalog_id'];
			}else{
				$nalog_id = $nalog_from_queue;
			}
	
			$query_uslovi = $db->prepare("
				SELECT * FROM idk_nalozi_blokovi_prijave WHERE nbp_nalogid = $nalog_id ");
			$query_uslovi->execute();
			$row_uslovi = $query_uslovi->fetch();
			$vozacka = $row_uslovi['nbp_vozacka'];
			$vozacka_kategorija = $row_uslovi['nbp_vozacka_kategorija'];
			$visoko_obrazovanje = $row_uslovi['nbp_visoko_obr'];
			$dodatno_obrazovanje = $row_uslovi['nbp_dodatno_obr'];
			$iskustvo = $row_uslovi['nbp_korak4'];
			$min_njemacki = $row_uslovi['nbp_njemacki_jezik'];
			$dokumenti = $row_uslovi['nbp_korak7'];
			$diploma = $row_uslovi['nbp_diploma'];
			$pripravnicki = $row_uslovi['nbp_pripravnicki'];
			$strucni = $row_uslovi['nbp_strucni'];
			$jezik_cert = $row_uslovi['nbp_jezik_cert'];
			$nalog_struka = $row_uslovi['nbp_struka'];
			$iskustvo_u_struci = $row_uslovi['nbp_iskustvo_u_struci'];
			$starost_od = $row_uslovi['nbp_kandidat_starost_od'];
			$starost_do = $row_uslovi['nbp_kandidat_starost_do'];
	
			$query_doc_diploma = $db->prepare("
				SELECT document_id FROM idk_documents WHERE document_dataid = $kandidat_id AND document_name = 'Diploma završene škole' ");
			$query_doc_diploma->execute();
			$row_doc_diploma = $query_doc_diploma->rowCount();
	
			$query_doc_pripravnicki = $db->prepare("
				SELECT document_id FROM idk_documents WHERE document_dataid = $kandidat_id AND document_name = 'Uvjerenje o pripravničkom stažu' ");
			$query_doc_pripravnicki->execute();
			$row_doc_pripravnicki= $query_doc_pripravnicki->rowCount();
	
			$query_doc_strucni = $db->prepare("
				SELECT document_id FROM idk_documents WHERE document_dataid = $kandidat_id AND document_name = 'Uvjerenje o položenom stručnom ispitu' ");
			$query_doc_strucni->execute();
			$row_doc_strucni= $query_doc_strucni->rowCount();
	
			$query_doc_cert = $db->prepare("
				SELECT document_id FROM idk_documents WHERE document_dataid = $kandidat_id AND document_name = 'Certifikati o poznavanju jezika' ");
			$query_doc_cert->execute();
			$row_doc_cert= $query_doc_cert->rowCount();
	
			$ispunjava_uslove = 0;
	
			if($iskustvo_u_struci == 1 && $row_kandidat["kandidat_iskustvo_u_struci"] != 1){
				$ispunjava_uslove++;
			}
	
			$today = new DateTime();
			$birthdate = new DateTime($row_kandidat['kandidat_datumrodjenja']);
			$interval = $today->diff($birthdate);
			$age = $interval->y;
			
			if($starost_od !== null && $age < $starost_od){
				$ispunjava_uslove++;
			}
	
			if($starost_do !== null && $age > $starost_do){
				$ispunjava_uslove++;
			}
	
			//gledanje obrazovanja preko struka
			/*if($nalog_struka != null){
				$query_kandidat_edukacija_srednja = $db->prepare("
						SELECT ke_id, ke_smjer_id, ss_struka_id FROM idk_kandidat_edukacija 
						LEFT JOIN idk_skole_smjerovi sm on ke_smjer_id = sm.ss_id
						WHERE ke_kandidat_id = $kandidat_id AND ss_struka_id IN ($nalog_struka) ");
				$query_kandidat_edukacija_srednja->execute();
				$nr_kes = $query_kandidat_edukacija_srednja->rowCount();
				if($nr_kes < 1){
					$ispunjava_uslove++;
				}
			}else{}*/
	
			//gledanje obrazovanja preko smjerova vezanih za nalog
			//prvo pokupiti sve smjerovi koji su vezani za nalog, ako nema nijednog onda ne treba nista raditi
			$query_get_nalog_smjer = $db->prepare("
					SELECT smjer_id FROM idk_nalog_smjer WHERE nalog_id = $nalog_id
			");
			$query_get_nalog_smjer->execute();
			if($query_get_nalog_smjer->rowCount() > 0){
				
				$query_kandidat_edukacija_srednja = $db->prepare("
							SELECT ke_id FROM idk_kandidat_edukacija 
							LEFT JOIN idk_skole_smjerovi sm on ke_smjer_id = sm.ss_id
							WHERE ke_kandidat_id = $kandidat_id AND sm.ss_naziv IN 
							(SELECT iss.ss_naziv FROM idk_nalog_smjer ins
							JOIN idk_skole_smjerovi iss ON ins.smjer_id = iss.ss_id
							WHERE ins.nalog_id = $nalog_id ) "
				);
				$query_kandidat_edukacija_srednja->execute();
				$nr_kes = $query_kandidat_edukacija_srednja->rowCount();
				if($nr_kes < 1){
					$ispunjava_uslove++;
				}
			}else{}
			
			
			if($vozacka == 'block'){
				if($kandidat_vozacka !== 'Da'){
					$ispunjava_uslove++;
				}else{}
				
			}
			if($vozacka_kategorija == null){
				
			}else{
				$niz_kat = explode(',', $vozacka_kategorija);
				$niz_kand_vk = explode(',',$kandidat_vozacka_kategorija);
				
				$niz_final = array();
				if(in_array("CE", $niz_kand_vk)){
					$nize_kategorije = array("B","C1","C","C1E","CE");
					foreach($nize_kategorije as $niza_kat){
						if(!in_array($niza_kat, $niz_final, true)){
							array_push($niz_final, $niza_kat);
						}
					}
				}
				if(in_array("C1E", $niz_kand_vk)){
					$nize_kategorije = array("B","C1","C","C1E");
					foreach($nize_kategorije as $niza_kat){
						if(!in_array($niza_kat, $niz_final, true)){
							array_push($niz_final, $niza_kat);
						}
					}
				}
				if(in_array("C", $niz_kand_vk)){
					$nize_kategorije = array("B","C1","C");
					foreach($nize_kategorije as $niza_kat){
						if(!in_array($niza_kat, $niz_final, true)){
							array_push($niz_final, $niza_kat);
						}
					}
				}
				if(in_array("C1", $niz_kand_vk)){
					$nize_kategorije = array("B","C1");
					foreach($nize_kategorije as $niza_kat){
						if(!in_array($niza_kat, $niz_final, true)){
							array_push($niz_final, $niza_kat);
						}
					}
				}
				if(in_array("BE", $niz_kand_vk)){
					$nize_kategorije = array("B","BE");
					foreach($nize_kategorije as $niza_kat){
						if(!in_array($niza_kat, $niz_final, true)){
							array_push($niz_final, $niza_kat);
						}
					}
				}
				if(in_array("B", $niz_kand_vk)){
					$nize_kategorije = array("B");
					foreach($nize_kategorije as $niza_kat){
						if(!in_array($niza_kat, $niz_final, true)){
							array_push($niz_final, $niza_kat);
						}
					}
				}
				
				$niz_check = array_intersect($niz_final,$niz_kat);
				if(count($niz_check) == 0){
					$ispunjava_uslove++;
				}
			}
			/*switch($vozacka_kategorija){
				case "B":
					if($kandidat_vozacka_kategorija == "NN" || $kandidat_vozacka_kategorija == null)
						$ispunjava_uslove++;
				break;
				case "C1":
					if(in_array($kandidat_vozacka_kategorija , array(null, "NN", "B")))
						$ispunjava_uslove++;
				break;
				case "C":
					if(in_array($kandidat_vozacka_kategorija , array(null, "NN", "B", "C1")))
						$ispunjava_uslove++;
				break;
				case "BE":
					if(in_array($kandidat_vozacka_kategorija , array(null, "NN", "B")))
						$ispunjava_uslove++;
				break;
				case "C1E":
					if(in_array($kandidat_vozacka_kategorija , array(null, "NN", "B", "C1", "C")))
						$ispunjava_uslove++;
				break;
				case "CE":
				if(in_array($kandidat_vozacka_kategorija , array(null, "NN", "B", "C1", "C", "C1E")))
						$ispunjava_uslove++;
				break;
			}*/
			if($visoko_obrazovanje == 1){
				if($row_kandidat_edukacija_visoko < 1){
					$ispunjava_uslove++;
				}else{}
			}
			if($dodatno_obrazovanje == 1){
				if($row_kandidat_edukacija_ostalo < 1){
					$ispunjava_uslove++;
				}else{}
			}
			if($iskustvo == 1){
				if($row_kandidat_iskustvo < 1){
					$ispunjava_uslove++;
				}else{}
			}
	
			switch($kandidat_jezik){
				case "A1":
					$kandidat_znanje_njem = 1;
				break;
				case "A2":
					$kandidat_znanje_njem = 2;
				break;
				case "B1":
					$kandidat_znanje_njem = 3;
				break;
				case "B2":
					$kandidat_znanje_njem = 4;
				break;
				case "C1":
					$kandidat_znanje_njem = 5;
				break;
				case "C2":
					$kandidat_znanje_njem = 6;
				break;
				default:
					$kandidat_znanje_njem = 0;
			}
			switch($min_njemacki){
				case "A1":
					$min_njemacki_broj = 1;
				break;
				case "A2":
					$min_njemacki_broj = 2;
				break;
				case "B1":
					$min_njemacki_broj = 3;
				break;
				case "B2":
					$min_njemacki_broj = 4;
				break;
				case "C1":
					$min_njemacki_broj = 5;
				break;
				case "C2":
					$min_njemacki_broj = 6;
				break;
				default:
					$min_njemacki_broj = 0;
			}
			
			if($kandidat_znanje_njem < $min_njemacki_broj){
				$ispunjava_uslove++;
			}else{}
	
			if($dokumenti == 1){
				if($diploma == 1){
					if($row_doc_diploma < 1){
						$ispunjava_uslove++;
					}else{}
				}
				if($pripravnicki == 1){
					if($row_doc_pripravnicki < 1){
						$ispunjava_uslove++;
					}else{}
				}
				if($strucni == 1){
					if($row_doc_strucni < 1){
						$ispunjava_uslove++;
					}else{}
				}
				if($jezik_cert == 1){
					if($row_doc_cert < 1){
						$ispunjava_uslove++;
					}else{}
				}
			}
			return $ispunjava_uslove;
			if($ispunjava_uslove == 0){
				$izvor = 3;
				$status_id = 6;
				$stari_projekt_id = null;
				$query_bot_project = $db->prepare("
					SELECT project_id FROM idk_projects WHERE project_nalogid = $nalog_id AND project_name LIKE '%BOT - ispunjava uslove%' ");
				$query_bot_project->execute();
				$row_project = $query_bot_project->fetch();
				$project_id = $row_project['project_id'];
				$novi_projekt_id = $project_id;
				
				$q_oposite_project = $db->prepare("
					SELECT project_id FROM idk_projects WHERE project_nalogid = $nalog_id AND project_name LIKE '%BOT - ne ispunjava uslove%' ");
				$q_oposite_project->execute();
				$row_oposite_project = $q_oposite_project->fetch();
				$oposite_project_id = $row_oposite_project['project_id'];
				
				$query_delete = $db->prepare("DELETE FROM idk_project_kandidati WHERE pk_projectid = $oposite_project_id AND pk_kandidatid = $kandidat_id");
				$query_delete->execute();
				
				$query_check = $db->prepare("SELECT * FROM idk_project_kandidati
											WHERE pk_projectid = :pk_projectid AND pk_kandidatid = :pk_kandidatid");
				$query_check->execute(array(
								':pk_projectid' => $project_id,
								':pk_kandidatid' => $kandidat_id));
				if($query_check->rowCount() == 0){
					
					$query = $db->prepare("
									INSERT INTO idk_project_kandidati
										(pk_projectid, pk_kandidatid)
									VALUES
										(:pk_projectid, :pk_kandidatid)");
	
					$query->execute(array(
								':pk_projectid' => $project_id,
								':pk_kandidatid' => $kandidat_id));
				}
				$query_update_sp = $db->prepare("UPDATE idk_kandidati SET kandidat_status_prijave = $status_id WHERE kandidat_id = $kandidat_id");
				$query_update_sp->execute();
	
				$query_bot_project_prijave = $db->prepare("
					SELECT project_id FROM idk_projects WHERE project_nalogid = $nalog_id AND (project_name LIKE '%Prijave%' OR project_name LIKE '%Prijava%') ");
				$query_bot_project_prijave->execute();
				$row_project_prijave = $query_bot_project_prijave->fetch();
				$prijave_project_id = $row_project_prijave['project_id'];
				$query_delete = $db->prepare("DELETE FROM idk_project_kandidati WHERE pk_projectid = $prijave_project_id AND pk_kandidatid = $kandidat_id");
				$query_delete->execute();
	
				//Add to LOGS
				$log_desc = "Bot vezao kandidata: " .$kandidat_id. " za projekt " .$project_id. ".";
				$log_type = "0";
				addToLogs($log_desc, $log_type); //Log za biljeske - $log_type = 3
				addToLogsStatusPrijave($stari_projekt_id, $novi_projekt_id, $status_id, $kandidat_id, $izvor);
				
			}else{
				$izvor = 3;
				$status_id = 2;
				$stari_projekt_id = null; 
				$query_bot_project = $db->prepare("
					SELECT project_id FROM idk_projects WHERE project_nalogid = $nalog_id AND project_name LIKE '%BOT - ne ispunjava uslove%' ");
				$query_bot_project->execute();
				$row_project = $query_bot_project->fetch();
				$project_id = $row_project['project_id'];
				$novi_projekt_id = $project_id;
				if($kandidat_status_messenger == 2 OR $kandidat_status_messenger == 5 OR $kandidat_status_messenger == 6){
					$q_oposite_project = $db->prepare("
						SELECT project_id FROM idk_projects WHERE project_nalogid = $nalog_id AND project_name LIKE '%BOT -  ispunjava uslove%' ");
					$q_oposite_project->execute();
					$row_oposite_project = $q_oposite_project->fetch();
					$oposite_project_id = $row_oposite_project['project_id'];
	
					$query_delete = $db->prepare("DELETE FROM idk_project_kandidati WHERE pk_projectid = $oposite_project_id AND pk_kandidatid = $kandidat_id");
					$query_delete->execute();
					
					$query_check = $db->prepare("SELECT * FROM idk_project_kandidati
												WHERE pk_projectid = :pk_projectid AND pk_kandidatid = :pk_kandidatid");
					$query_check->execute(array(
									':pk_projectid' => $project_id,
									':pk_kandidatid' => $kandidat_id));
					if($query_check->rowCount() == 0){
						
						$query = $db->prepare("
										INSERT INTO idk_project_kandidati
											(pk_projectid, pk_kandidatid)
										VALUES
											(:pk_projectid, :pk_kandidatid)");
	
						$query->execute(array(
									':pk_projectid' => $project_id,
									':pk_kandidatid' => $kandidat_id));
					}
	
					//Add to LOGS
					$log_desc = "Bot vezao kandidata: " .$kandidat_id. " za projekt " .$project_id. ".";
					$log_type = "0";
					addToLogs($log_desc, $log_type); //Log za biljeske - $log_type = 3
					
					$query_bot_project_prijave = $db->prepare("
					SELECT project_id FROM idk_projects WHERE project_nalogid = $nalog_id AND project_name LIKE '%Prijave%' ");
					$query_bot_project_prijave->execute();
					$row_project_prijave = $query_bot_project_prijave->fetch();
					$prijave_project_id = $row_project_prijave['project_id'];
					$query_delete = $db->prepare("DELETE FROM idk_project_kandidati WHERE pk_projectid = $prijave_project_id AND pk_kandidatid = $kandidat_id");
					$query_delete->execute();
					addToLogsStatusPrijave($stari_projekt_id, $novi_projekt_id, $status_id, $kandidat_id, $izvor);
				}
				
			}
		}
	}
	exit();
	/* Skripta za odrezervaciju - start */
	$time_start = microtime(true);

	$limit = intval($_REQUEST["limit"]);
	$slucaj = $_REQUEST["slucaj"];
	echo "Podešeni parametri ";
	echo "Limit: ".$limit;
	echo "Slucaj: ".$slucaj;
	echo "<br>";
	$query = "";
	switch($slucaj){
		case "stari":
			$query = "
				SELECT kan.kandidat_id, pk.project_nalogid, pk.project_id
				FROM (
					SELECT sqkan.kandidat_id, sqkan.kandidat_nalog_id
					FROM idk_kandidati sqkan
					WHERE sqkan.kandidat_nalog_id IS NULL
					AND (sqkan.kandidat_status_prijave IS NULL OR
						sqkan.kandidat_status_prijave IN (0,1,2,5,6))
				) kan
				JOIN (
					SELECT sqpk.pk_kandidatid, sqpk.pk_projectid, pr.project_nalogid, pr.project_id
					FROM idk_project_kandidati sqpk
					JOIN (
						SELECT sqpr.project_id, sqpr.project_nalogid
						FROM idk_projects sqpr
						WHERE (sqpr.project_name LIKE ('%BOT - Ispunjava%')
						OR sqpr.project_name LIKE ('%Casting%'))
						AND sqpr.project_nalogid NOT IN (0,262,272,278)
					) pr
					ON pr.project_id = sqpk.pk_projectid
				)pk
				ON pk.pk_kandidatid = kan.kandidat_id
				GROUP BY kan.kandidat_id, pk.project_id
				LIMIT ".$limit."
			";
		break;
		case "novi":
			$query = "
				SELECT kan.kandidat_id, kan.kandidat_nalog_id, pk.project_nalogid, pk.project_id
				FROM (
					SELECT sqkan.kandidat_id, sqkan.kandidat_nalog_id
					FROM idk_kandidati sqkan
					WHERE sqkan.kandidat_nalog_id IS NOT NULL
					AND sqkan.kandidat_nalog_id IN (254,289)
					AND (sqkan.kandidat_status_prijave IS NULL OR
						sqkan.kandidat_status_prijave IN (0,1,2,5,6))
				) kan
				JOIN (
					SELECT sqpk.pk_kandidatid, sqpk.pk_projectid, pr.project_nalogid, pr.project_id
					FROM idk_project_kandidati sqpk
					JOIN (
						SELECT sqpr.project_id, sqpr.project_nalogid
						FROM idk_projects sqpr
						WHERE (sqpr.project_name LIKE ('%BOT - Ispunjava%')
						OR sqpr.project_name LIKE ('%Casting%'))
						AND sqpr.project_nalogid IN (254,289)
					) pr
					ON pr.project_id = sqpk.pk_projectid
				)pk
				ON pk.pk_kandidatid = kan.kandidat_id 
				GROUP BY kan.kandidat_id, pk.project_id
				LIMIT ".$limit."
			";
		break;
		default:
			echo "Nedefinisan slucaj!";
			exit();
		break;
	}

	if($query != "" AND $limit != 0){
		$result_query = $db->prepare($query);
		$result_query->execute();
		if($result_query->rowCount() != 0){

			echo "<br><h3>Rezultati query-a</h3>";

			while($result_row = $result_query->fetch()){
				$candidateId = intval($result_row["kandidat_id"]);
				$nalogId = intval($result_row["project_nalogid"]);
				echo "<br> Kandidat ID: ".$candidateId." Nalog ID: ".$nalogId." ";
				/*
					***************************************************
					Kôd prebačen sa do.php case "add_kandidat_projekat"
					***************************************************
										START
					***************************************************
				*/
					$kandidat_id 		= $candidateId;
					$nalog_id_to_be 	= $nalogId;

					$project_prijave_query = $db->prepare("
						SELECT project_id
						FROM idk_projects
						WHERE project_nalogid = :project_nalogid AND (project_name LIKE '%Prijave%' OR project_name LIKE '%Prijava%') AND project_nalogid != 0 
					");
					$project_prijave_query->execute(array(
						':project_nalogid' => $nalog_id_to_be
					));
					$project_prijave_row = $project_prijave_query->fetch();
					
					$project_id_to_be = intval($project_prijave_row['project_id']);
					echo " Project ID: ".$project_id_to_be."";

					if($project_id_to_be == 0){
						continue;
					}
					$flag_was_reserved 				= false;
					$flag_candidate_has_nalog_id	= false;
					$nalog_id_was 					= "";
					$project_id_was 				= NULL;
					$project_name_to_be				= "";
					$project_candidate_to_delete_array = array();
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
						
						array_push($project_candidate_to_delete_array, $project_candidate_to_delete);
					}

					if(count($project_candidate_to_delete_array) != 0){
						$pk_to_delete = implode(', ', $project_candidate_to_delete_array);
						$query_delete_project_candidates = $db -> prepare("
							DELETE FROM idk_project_kandidati
							WHERE pk_id IN ($pk_to_delete)
						");
						$query_delete_project_candidates -> execute();
						$log_desc = "Obrisao kandidata: " .$kandidat_id. " iz projekata [" .$pk_to_delete. "].";
						$log_type = "0";
						addToLogs($log_desc, $log_type);
					}
					
					$query_insert_pr = $db->prepare("
									INSERT INTO idk_project_kandidati
										(pk_projectid, pk_kandidatid)
									VALUES
										(:pk_projectid, :pk_kandidatid)");

					$query_insert_pr->execute(array(
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

					addToLogsStatusPrijave($project_id_was, $project_id_to_be, getStatusPrijaveByProjectId($project_id_to_be), $kandidat_id, 1);
				/*
					***************************************************
										END
					***************************************************
					Kôd prebačen sa do.php case "add_kandidat_projekat"
					***************************************************
				*/
			}

		}else{
			echo "<br>Query nije pronašao ni jedan rezultat!"; 
		}
	}else{
		echo "<br>Prazan query ili nepravilan limit!";
	}

	$time_end = microtime(true);
	$time = $time_end - $time_start;

	echo "<br><br>Vrijeme ".$time;
/* Skripta za odrezervaciju - end */



// include("pdf_generator.php");
// include("includes/functions.php");
// include('lang/bs.php');
// include('lang/sr.php');
// include('lang/de.php');

/*
$employee_status = explode( ',' , getEmployeeStatus());
$employee_supervizor = explode( ',' , getEmployeeSupervizor());

if(in_array( "18" , $employee_status) OR in_array( "18" , $employee_supervizor)){
	?>
	<li class= "submenu">
		Task Force
		<ul>
			<li>Telefonija</li>
			<?php 
			if(in_array("18", $employee_supervizor)){
				?>
				<li>TF statistika</li>
				<li>Tekući castinzi</li>
				<?php ;
			} ?>
		</ul>
	</li>
	<?
}

if(in_array( "1" , $employee_status) OR in_array("9", $employee_status) OR in_array("14", $employee_status)){
	?>
	<li>Statistike</li>
	<?php 
}
*/
// $kandidat_id = 47195;
// $racun_bf = 1009;
// $tadasnji_dan = "19-04-2022 11:52:35";

// createRacunBIHispravljanje($kandidat_id, $racun_bf, $tadasnji_dan);

// sendMailRacun($putanja_racuna);
// createRacunBIHispravljanje(8378,1034,"20220608145401");
function createRacunBIHispravljanje($kandidat_id, $racun_bf, $tadasnji_dan){

	Global $db; 
	Global $logged_employee_id;
	Global $vrsta_dokumenta;
	$year_skr = date('y');
	$vrsta_dokumenta = "predracun";
	
	$racun_datum_kreiranja = date('Y-m-d H:i:s', strtotime($tadasnji_dan));
	$danas = date('d.m.Y', strtotime($tadasnji_dan));
	$rok_uplate = date('d.m.Y', strtotime($danas. ' + 10 days'));
	
	$get_kand_info = $db->prepare("
					SELECT ime_nd_kandidata, prezime_nd_kandidata, ulica_nd_kandidata, postanski_broj_nd_kandidata, grad_nd_kandidata, vrsta_ugovora_nd_kandidata, jmbg_nd_kandidata
					FROM idk_nd_kandidata
					WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata
					");

	$get_kand_info->execute(array(
						':id_broj_nd_kandidata' => $kandidat_id));

	$kand_info_row = $get_kand_info->fetch();
	
	$ime = $kand_info_row["ime_nd_kandidata"];
	$prezime = $kand_info_row["prezime_nd_kandidata"];
	$ulica = $kand_info_row["ulica_nd_kandidata"];
	$pbroj = $kand_info_row["postanski_broj_nd_kandidata"];
	$grad = $kand_info_row["grad_nd_kandidata"];
	$adresa = $ulica.", ".$pbroj." ".$grad;
	$jmbg = $kand_info_row["jmbg_nd_kandidata"];
	$vrsta_ugovora = $kand_info_row["vrsta_ugovora_nd_kandidata"];
	
	switch($vrsta_ugovora){
		case 1: case 2: case 22: case 62: case 72: case 52: case 82: case 42:
			$broj_rata = 2;
			$rate_text2 = "/".$broj_rata;
		break;
		case 5: case 6: case 23: case 63: case 73: case 53: case 83: case 43:
			$broj_rata = 3;
			$rate_text2 = "/".$broj_rata;
		break;
		case 7: case 8: case 24: case 64: case 74: case 54: case 84: case 44:
			$broj_rata = 4;
			$rate_text2 = "/".$broj_rata;
		break;
		case 3: case 4: case 25: case 65: case 75: case 55: case 85: case 45:
			$broj_rata = 5;
			$rate_text2 = "/".$broj_rata;
		break;
		default:
			$broj_rata = 1;
			$rate_text2 = "";
    }
	
	if(in_array(($vrsta_ugovora), array(21,22,23,24,25,61,62,63,64,65))){
		$popust_part = true;
		$popust_procent = 20;
	}elseif(in_array(($vrsta_ugovora), array(2,4,6,8,10,12,71,72,73,74,75))){
		$popust_part = true;
		$popust_procent = 30;
	}elseif(in_array(($vrsta_ugovora), array(9,82,83,84,85))){ //sve dok je 10 popust na placanje u cijelosti(1 rata-> 9)
		$popust_part = true;
		$popust_procent = 10;
	}elseif(in_array(($vrsta_ugovora), array(51,52,53,54,55))){
		$popust_part = true;
		$popust_procent = 50;
	}elseif(in_array(($vrsta_ugovora), array(41,42,43,44,45))){
		$popust_part = true;
		$popust_procent = 70;
	}elseif($vrsta_ugovora == 99){ 
		$popust_part = true;
		$popust_procent = 100;
	}else{
		$popust_part = false;
		$popust_procent = 0;
	}

	//$brojac_racuna = createBrojRacuna("BiH");
	$brojac_racuna = 87;
	$novi_racun = "DIPLRB-".$brojac_racuna."-".$year_skr;
	$file_datum = date('YmdHis', strtotime($tadasnji_dan)); 
	$racun_file = $file_datum."DIPLRB".$brojac_racuna.".pdf";
	
	$pdf = new PDF('P','mm','A4');
	

	$pdf->AliasNbPages();
	$pdf->SetDisplayMode('real', 'single');

	$pdf->AddPage();

	$pdf->Cell(180,7,"Kupac:",'T,B',0,'',0); $pdf->Ln();
	$pdf->Cell(50,7,"Ime i prezime:",0,0,'',0); 
	$pdf->Cell(130,7,$ime." ".$prezime,0,0,'',0); 
	$pdf->Ln();
	$pdf->Cell(50,7,"Adresa:",'',0,'',0); 
	$pdf->Cell(130,7,$adresa,'',0,'',0); 
	$pdf->Ln();
	$pdf->Cell(50,7,"JMBG:",'B',0,'',0); 
	$pdf->Cell(130,7,$jmbg,'B',0,'',0); 
	$pdf->Ln(14);
	$pdf->Cell(180,7,'Bihać, dana '.$danas,0,0,'',0);
	$pdf->Ln();
	$pdf->Cell(180,7,'Isporuka: '.$danas,0,0,'',0); 
	$pdf->Ln();
	$pdf->Cell(180,7,'Rok za uplatu: '.$rok_uplate,0,0,'',0); 
	$pdf->Ln(14);

	$pdf->SetFont('DejaVu','B',18);
	$pdf->Cell(80,10,'Račun: '.$novi_racun,'B'); /*$pdf->Line(11,147,90,147);*/ $pdf->Ln();
	$pdf->SetFont('DejaVu','',9);
	$pdf->Cell(80,7,'BF: '.$racun_bf,''); /*$pdf->Line(11,147,90,147);*/ $pdf->Ln(25);
	
	$pdf->SetFont('DejaVu','B',9);
	$pdf->Cell(12,7,'Šifra','T,L,B','','C',0); 
	$pdf->Cell(98,7,'Naziv','T,B','','L',0); 
	$pdf->Cell(16,7,'Količina','T,B','','C',0); 
	$pdf->Cell(27,7,'Cijena','T,B','','R',0); 
	$pdf->Cell(27,7,'Ukupno','T,B,R',1,'R',0); 
	
	$ukupno_za_placanje = 0;
	$ukupno_za_placanje_EUR = 0;
	$ukupno_za_placanje_RSD = 0;
	// for($i=1; $i<=$broj_rata; $i++){
		$get_predracuni = $db->prepare("
						SELECT pr_vrijednost_BAM, pr_vrijednost_EUR, pr_vrijednost_RSD, pr_rata, pr_id
						FROM idk_predracuni
						WHERE pr_kandidat_id = :pr_kandidat_id AND pr_status = 1 AND pr_stornirano = 0 AND pr_vrsta_predracuna = 1 AND pr_izdan_racun = 0 AND pr_rata IN(2,3,4,5)
						ORDER BY pr_rata
						");

		$get_predracuni->execute(array(
						':pr_kandidat_id' => $kandidat_id
						));
	$i = 1;
	$iznos = 0;
	while($predracuni_row = $get_predracuni->fetch()){
	
		$pr_rata = $predracuni_row['pr_rata'];
		
		switch($pr_rata){
			case 1:
				$rate_text1 = " - Prvi ";
			break;
			case 2:
				$rate_text1 = " - Drugi ";
			break;
			case 3:
				$rate_text1 = " - Treći ";
			break;
			case 4:
				$rate_text1 = " - Četvrti ";
			break;
			case 5:
				$rate_text1 = " - Peti ";
			break;
			default:
				$rate_text1 = "";
		}
		if($broj_rata == 1){
			$rate_txt_3 = "";
		}else{
			$rate_txt_3 = $rate_text1."dio usluge ".$pr_rata.$rate_text2;
		}
		
		$pr_vrijednost_BAM = $predracuni_row['pr_vrijednost_BAM'];
		$pr_vrijednost_EUR = $predracuni_row['pr_vrijednost_EUR'];
		$pr_vrijednost_RSD = $predracuni_row['pr_vrijednost_RSD'];
		$pr_id = $predracuni_row['pr_id'];
        
		$pr_vrijednost_BAM = number_format($pr_vrijednost_BAM, 2, '.', '');
		$pr_vrijednost_EUR = number_format($pr_vrijednost_EUR, 2, '.', '');
		$pr_vrijednost_RSD = number_format($pr_vrijednost_RSD, 2, '.', '');
		
		$ukupno_za_placanje += $pr_vrijednost_BAM;
		$ukupno_za_placanje_EUR += $pr_vrijednost_EUR;
		$ukupno_za_placanje_RSD += $pr_vrijednost_RSD;
		
		if($popust_procent != 100){
			$cijena = ($pr_vrijednost_BAM/1.17) * (100 / (100 - $popust_procent));
		}else{
			$cijena = 811.97;
		}
		$cijena_f = number_format($cijena, 2, '.', '');
        $iznos += $cijena;
		
		$pdf->SetFont('DejaVu','',10);
		$pdf->Cell(12,7,$i,0,'','C',0); 
		$pdf->Cell(98,7,'Obrada podataka za nostrifikaciju diplome'.$rate_txt_3,0,'','L',0); 
		$pdf->Cell(16,7,'1',0,'','C',0); 
		$pdf->Cell(27,7,$cijena_f." KM",0,'','R',0); 
		$pdf->Cell(27,7,$cijena_f." KM",0,'','R',0); 
		$pdf->Ln();
		$i=$i+1 ;
		
		//UPDATE IZDAN RACUN ZA PREDRACUNE
		/*$update_predracuni = $db->prepare("
									UPDATE idk_predracuni
									SET pr_izdan_racun = 1
									WHERE pr_id = :pr_id
									");
		
		$update_predracuni->execute(array(
			':pr_id' => $pr_id
		));*/
	}
	$pdv_stopa = 0.17;
	$ukupno = $ukupno_za_placanje / (1 + $pdv_stopa);
	$ukupno_f = number_format($ukupno, 2, '.', '');
	$ukupno_za_placanje_f = number_format($ukupno_za_placanje, 2, '.', '');
	$pdv_iznos = $ukupno_za_placanje_f - $ukupno_f;
	$pdv_iznos_f = number_format($pdv_iznos, 2, '.', '');
	$iznos_f = number_format($iznos, 2, '.', '');
	
	$popust = $iznos_f - $ukupno_f;
	
	$popust_f = number_format($popust, 2, '.', '');
	
	$pdf->Ln(22);
	
    
	if($popust_part){
		$pdf->Cell(100,7,'','T','','C',0); 
		$pdf->Cell(53,7,'Iznos','T','','L',0); 
		$pdf->Cell(27,7,$iznos_f." KM",'T','','R',0); $pdf->Ln();
		
		$pdf->Cell(100,7,'','','','C',0); 
		$pdf->Cell(45,7,'Popust','','','L',0); 
		$pdf->Cell(8,7,$popust_procent.'% - ','','','L',0); 
		$pdf->Cell(27,7,$popust_f." KM",'','','R',0); $pdf->Ln();
		//$pdf->Cell(27,7,$popust_f." KM",'','','R',0); $pdf->Ln();
		
		$pdf->Cell(100,7,'','','','C',0); 
		$pdf->Cell(53,7,'Ukupno','','','L',0); 
		$pdf->Cell(27,7,$ukupno_f." KM",'','','R',0); $pdf->Ln();
		
	}else{
		$pdf->Cell(100,7,'','T','','C',0); 
		$pdf->Cell(53,7,'Ukupno','T','','L',0); 
		$pdf->Cell(27,7,$ukupno_f." KM",'T','','R',0); $pdf->Ln();
		
	}
	
	$pdf->Cell(100,7,'',0,'','C',0); 
	$pdf->Cell(53,7,'PDV',0,'','L',0); 
	$pdf->Cell(27,7,$pdv_iznos_f." KM",0,'','R',0); $pdf->Ln();
	
	$pdf->Cell(100,7,'',0,'','C',0); 
	$pdf->Cell(53,7,'Ukupno za plaćanje KM',0,'','L',0); 
	$pdf->Cell(27,7,$ukupno_za_placanje_f." KM",0,'','R',0); $pdf->Ln();
	
	$pdf->Cell(100,7,'',0,'','C',0); 
	$pdf->Cell(53,7,'EUR',0,'','L',0); 
	$pdf->Cell(27,7,'€ '.$ukupno_za_placanje_EUR,0,'','R',0); $pdf->Ln();
	
	$pdf->Ln(22);	
	
	$filename="files/racuni_dipl/".$racun_file."";
	// var_dump($filename);
	// exit();
	
	$pdf->Output();
	//$pdf->Output($filename,'F');
	
	//INSER RACUN
	/*$insert_racun = $db->prepare("	
				INSERT INTO idk_racuni	
				(racun_broj,  racun_kandidat_id, racun_datum_kreiranja, racun_status, racun_domaca_valuta, racun_vrijednost_BAM, racun_vrijednost_RSD, racun_vrijednost_EUR, racun_file, racun_bf)	
				VALUES	
				(:racun_broj,:racun_kandidat_id,:racun_datum_kreiranja,:racun_status,:racun_domaca_valuta,:racun_vrijednost_BAM,:racun_vrijednost_RSD,:racun_vrijednost_EUR,:racun_file,:racun_bf)	
				");	
	$insert_racun->execute(array(	
				':racun_broj' => $novi_racun,	
				':racun_kandidat_id' => $kandidat_id,	
				':racun_datum_kreiranja' => $racun_datum_kreiranja,	
				':racun_status' => 1,	
				':racun_domaca_valuta' => 'BAM',	
				':racun_vrijednost_BAM' => $ukupno_za_placanje,	
				':racun_vrijednost_RSD' => $ukupno_za_placanje_RSD,	
				':racun_vrijednost_EUR' => $ukupno_za_placanje_EUR,	
				':racun_file' => $racun_file,
				':racun_bf' => $racun_bf
				));*/
	
	return $racun_file;
}

// echo count($niz_check);
// $token = "cZzYBscMxnI:APA91bEEZiKAqMZ113PftVpK_XCtXFMoHiyarYXBZnlyW5S2q_QVQ5tu_A6G9KgU9Ai5S1EHhI1j9XQced2ZFD5zCUZpv87_biU2JawkMM61ZwwXb9AubYMTY2p6M4CZ9xx7xBl2MWF3";
// $token_mes = "YzdqWHVPSTNYaDg6QVBBOTFiRm5fa1JTb3N1NE05MC1NLU0zeUtDdVhtY1Izbk1OdXI0YUltR1N6RnZoMXBjX2x1ZXAzSnVTNVBoX2lvMDlYaWdvV0lzYUNLeEtNX29qeGpSU0F2Ulg2TWtaYkVGYno4d2RGd2pBeTJrbk5CR3Q4Rk5aZWYyNGdrbVVCeHhLS1YyLXYtbXo=";
// $token_mes = "ZUFXcjlDYmRvRkk6QVBBOTFiSFB0aWNqN3hQa1ZsWG5KaGdPWUxHOC1fU0JQSkNTU1Q1dVRrM2RfVXVnZW9wamIzWHlYR0NkQ1JiZGFzZHhReGVBSUZsWl9NenlkMDE0anI4OXBFeE5MLWVSRzZDS3ZIWVlraXJfRGFqUjRCT3pnSXdzSHc3Rk01eDI3LU5HREloOUNDLTU=";
// $token = base64_decode($token_mes);
// $token_partner = "cZzYBscMxnI:APA91bEEZiKAqMZ113PftVpK_XCtXFMoHiyarYXBZnlyW5S2q_QVQ5tu_A6G9KgU9Ai5S1EHhI1j9XQced2ZFD5zCUZpv87_biU2JawkMM61ZwwXb9AubYMTY2p6M4CZ9xx7xBl2MWF3";
// $token_partner = "dvTlZ_XKZmI:APA91bHhzVFD12vuPMCwTLqatCwo6psNQHk_0LSv1UvULHlpVh1DYXhkb8z74VdhCyrvRbuBuFaBPc6UWqxWKz4Tf4iCwzgUClruqeCLjHtlScB8S-tSMVDguCyhJtjQ0c5_vu7scLcP";
// $token_partner = "e-13WO7wVUU:APA91bEOqghLM7_Nd9v21Phqh1yCMWdM_i9cvIr1hiHmJQ0ojye2_LZ4h8wkUmfNUJivyR0xFD568_zfl2Pu8L-bVDqv9-n_OIcpJBAN4fosoA4etH7ae56DpUDHsojGnqtAzN_mO6f0";
// $partner_ime = "Emir Bender";
// $notifikacija_text = "Novi kandidat";
// echo send_notification_partnerapp($token_partner, $partner_ime, "Test");
// echo send_bot_notification_android($token, $notifikacija_text);
// echo getEmployeeStatusById(49);



/*$ids_kand = array(2247,5731,10197,10281,10829,11412,12223,12983,17550,17734,18442,18957,20754,20886,20942,21077,21359,22738,23664,24486,25038,25550,25771,26896,27171,28148,28568,28579,29720,30333,30504,31402,31733,32605,33618,33656,37072,37423,38240,39195,41080,42361,42601,42718,44133,44261,45268,45851,45915,45997,46589,47957,48472,50260,50867,51673,52085,52716,54147,54264,54326,54646,54692,55973,56070,56104
);
foreach($ids_kand as $kandidat_id){
	$check_projekat = $db->prepare("
		SELECT pk_projectid
		FROM idk_project_kandidati
		WHERE pk_kandidatid = $kandidat_id AND pk_projectid IN (191,192,194,424,425,499,500,515,555,556,558,563,564,566,577,578,580,585,586,588,598,621,630,631,656,657,659,823,825,826,1380,1381,1383,1397,1398,1400,1426,1427,1429,1436,1437,1439,1660,1661,1662,1696,1697,1723,1728,1729,1731,1738,1739,1741)");
	
	$check_projekat->execute(); 
	$broj_projekata = $check_projekat->rowCount();
	if($broj_projekata == 0){
		echo $kandidat_id.",";
	}else{
		//echo $kandidat_id." imaga</br>";
	}
}
*/

/*************************
Prilivi predracuna - START
*************************/
/*
$get_candidates = $db->prepare("
				SELECT pr_kandidat_id, pr_datum_kreiranja, pr_rata, pr_vrijednost_BAM, pr_datum_uplate, vrsta_ugovora_nd_kandidata
				FROM idk_predracuni JOIN idk_nd_kandidata ON pr_kandidat_id = id_broj_nd_kandidata
				WHERE pr_rata = 1 AND pr_domaca_valuta = 'BAM' AND pr_status = 2 AND pr_uplaceno = 1 AND pr_datum_kreiranja BETWEEN '2020-12-01 00:00:00' AND '2021-09-29 00:00:00' AND status_nd_kandidata != 7
");

$get_candidates->execute();
while($row_candidates = $get_candidate->fetch()){
	$kandidat_id = $row_candidates['pr_kandidat_id'];
	$vrsta_ugovora = $row_candidates['vrsta_ugovora_nd_kandidata'];
	if($vrsta_ugovora == 1 OR $vrsta_ugovora == 2 OR $vrsta_ugovora == 22){
		$broj_rata = 2;
	}elseif($vrsta_ugovora == 3 OR $vrsta_ugovora == 4 OR $vrsta_ugovora == 25){
		$broj_rata = 5;
	}elseif($vrsta_ugovora == 5 OR $vrsta_ugovora == 6 OR $vrsta_ugovora == 23){
		$broj_rata = 3;
	}elseif($vrsta_ugovora == 7 OR $vrsta_ugovora == 8 OR $vrsta_ugovora == 24){
		$broj_rata = 4;
	}elseif($vrsta_ugovora == 26 OR $vrsta_ugovora == 28){
		$broj_rata = 6;
	}elseif($vrsta_ugovora == 27 OR $vrsta_ugovora == 29){
		$broj_rata = 12;
	}else{
		$broj_rata = 1;
	}
	
}
*/
/*************************
Prilivi predracuna - END
*************************/





// uplatiObracune(2898);
/*
function updatePrethodneObracunetest($vrijednost_tipa_f, $vrijednost_kategorije_f, $prov_bam_f, $prov_rsd_f, $prov_eur_f, $id, $datum_uplate){
	Global $db;
	$current_month_start = date('Y-m-01 00:00:00', strtotime($datum_uplate));
	$current_month_end = date('Y-m-t 23:59:59', strtotime($datum_uplate));
	$query_upd = $db->prepare("UPDATE idk_obracuni SET vrijednost_tipa = :vrijednost_tipa, vrijednost_kategorije = :vrijednost_kategorije, iznos_obracuna_bam = :iznos_obracuna_bam, iznos_obracuna_rsd = :iznos_obracuna_rsd, iznos_obracuna_eur = :iznos_obracuna_eur
								WHERE id = :id
	");
	$query_upd->execute(array(
				':vrijednost_tipa' => $vrijednost_tipa_f,
				':vrijednost_kategorije' => $vrijednost_kategorije_f,
				':iznos_obracuna_bam' => $prov_bam_f,
				':iznos_obracuna_rsd' => $prov_rsd_f,
				':iznos_obracuna_eur' => $prov_eur_f,
				':id' => $id,
				':start_date' => $current_month_start,
				':end_date' => $current_month_end
	));
	
}*/


function getProvizijaAgentatest($agent_id){
	Global $db;
	
	$f_from_f = date('Y-m-01 00:00:00');
	$f_to_f = date('Y-m-t 23:59:59');
	$query = $db->prepare("SELECT e.employee_poslovnica,
								SUM(CASE WHEN o.status_predracuna = 1 AND o.status_obracuna = 0 AND o.vrijeme_uplate BETWEEN '$f_from_f' AND '$f_to_f' THEN o.iznos_obracuna_bam ELSE NULL END) as suma_bam,
								SUM(CASE WHEN o.status_predracuna = 1 AND o.status_obracuna = 0 AND o.vrijeme_uplate BETWEEN '$f_from_f' AND '$f_to_f' THEN o.iznos_obracuna_rsd ELSE NULL END) as suma_rsd
							FROM idk_obracuni o
							JOIN idk_employees e ON e.employee_id = o.employee_id
							WHERE o.employee_id = :employee_id");
	$query->execute(array(
				'employee_id' => $agent_id
	));
	$row_prov = $query->fetch();
	$employee_poslovnica = $row_prov['employee_poslovnica'];
	$suma_bam = $row_prov['suma_bam'];
	$suma_rsd = $row_prov['suma_rsd'];
	$drzava = getDrzavaPoslovnice($employee_poslovnica);
	if($drzava == "Srbija"){
		$suma = number_format($suma_rsd, 2, ',', '');
		$suma_f = $suma." RSD";
	}else{
		$suma = number_format($suma_bam, 2, ',', '');
		$suma_f = $suma." KM";
	}
	return $suma_f;
}

function getDrzavaPoslovnicetest($poslovnica_id){
	Global $db;
	$query = $db->prepare("SELECT branch_state FROM idk_poslovnice WHERE branch_id = :branch_id");
	$query->execute(array('branch_id' => $poslovnica_id));
	$row = $query->fetch();
	
	return $row['branch_state'];
}

function testcreateUgovorSRB($kandidat_id){
	Global $db;
	Global $logged_employee_id;
	Global $vrsta_dokumenta;
	$vrsta_dokumenta = "ugovor_rs";
	Global $valutaCheck;
	$valutaCheck = "RSD";
	
	$danas = date('d.m.Y');
	$datetime = date('Y-m-d H:i:s');
	
	$day = date('d');
	$mjesec = date('m');
	$godina = date('y');
	if($day > 28)
		$day = 1;
	//pokupiti info iz baze
	$get_info = $db->prepare("
					SELECT ime_nd_kandidata, prezime_nd_kandidata, ulica_nd_kandidata, postanski_broj_nd_kandidata, grad_nd_kandidata, vrsta_ugovora_nd_kandidata, jmbg_nd_kandidata, broj_licne_karte_nd_kandidata, ulica_bor_nd_kandidata, postanski_broj_bor_nd_kandidata, grad_bor_nd_kandidata, izdao_licnu_nd_kandidata
					FROM idk_nd_kandidata
					WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata
					");

	$get_info->execute(array(
						':id_broj_nd_kandidata' => $kandidat_id));

	$info_row = $get_info->fetch();
	
	$prezime = $info_row["prezime_nd_kandidata"];
	$ime = $info_row["ime_nd_kandidata"];
	$grad = $info_row["grad_nd_kandidata"];
	$ulica = $info_row["ulica_nd_kandidata"];
	$vrsta_ugovora = $info_row["vrsta_ugovora_nd_kandidata"];
	$jmbg = $info_row["jmbg_nd_kandidata"];
	$licna = $info_row["broj_licne_karte_nd_kandidata"];
	$pbroj = $info_row["postanski_broj_nd_kandidata"];
	$ulica_bor = $info_row["ulica_bor_nd_kandidata"];
	if($ulica_bor == null OR $ulica_bor == ""){
		$ulica_bor = $ulica;
		$postanski_broj_bor = $pbroj;
		$grad_bor = $grad;
	}else{
		$postanski_broj_bor = $info_row["postanski_broj_bor_nd_kandidata"];
		$grad_bor = $info_row["grad_bor_nd_kandidata"];
	}
	$izdata = $info_row["izdao_licnu_nd_kandidata"];
	
	$brojac_ugovora = createBrojUgovora("Srbija");
	$ugovor_putanja = "UGS-".$brojac_ugovora."-".$mjesec."-".$godina.".pdf";
	
	// $txt_broj_ugovora = "DIPLS-".$brojac_ugovora."-".$mjesec."/".$godina;
	$txt_broj_ugovora = "DIPLS-00-12/21";
	//INSERT UGOVORA
	// $insert_ugovor = $db->prepare("	
				// INSERT INTO idk_nd_kandidata_dokumenti	
				// (naziv_dokument_nd,  naziv_dokument_ostali_nd, id_kandidata_dokument_nd, vrijeme_dodavanja_dokument_nd, dodao_zaposlenik_dokument_nd, tip_dokumenta)	
				// VALUES	
				// (:naziv_dokument_nd,:naziv_dokument_ostali_nd,:id_kandidata_dokument_nd,:vrijeme_dodavanja_dokument_nd,:dodao_zaposlenik_dokument_nd,:tip_dokumenta)	
				// ");	
	// $insert_ugovor->execute(array(	
				// ':naziv_dokument_nd' => $ugovor_putanja,	
				// ':naziv_dokument_ostali_nd' => "ugovor",	
				// ':id_kandidata_dokument_nd' => $kandidat_id,	
				// ':vrijeme_dodavanja_dokument_nd' => $datetime,	
				// ':dodao_zaposlenik_dokument_nd' => $logged_employee_id,	
				// ':tip_dokumenta' => 1
				// ));
	
	$pdf = new PDF('P','mm','A4');
	

	$pdf->AliasNbPages();
	$pdf->SetDisplayMode('real', 'single');

	$pdf->AddPage();

	// Add a Unicode font (uses UTF-9)
	$pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
	$pdf->AddFont('DejaVu','B','DejaVuSansCondensed-Bold.ttf',true);
	$pdf->AddFont('DejaVuSerif','','DejaVuSerifCondensed.ttf',true);
	$pdf->AddFont('DejaVuSerif','B','DejaVuSerifCondensed-Bold.ttf',true);
	$pdf->AddFont('DejaVuSerif','BI','DejaVuSerifCondensed-BoldItalic.ttf',true);

	$pdf->SetLeftMargin(20);
	$pdf->Ln(10);
	$pdf->SetFont('DejaVuSerif','',14);
	
	$pdf->MultiCell(170,7,'UGOVOR O PRUŽANJU USLUGE POSREDOVANJA U POSTUPKU NOSTRIFIKACIJE DIPLOME','','C',0);
	$pdf->Ln(5);
	
	$pdf->SetFont('DejaVuSerif','',11);
	$pdf->MultiCell(170,5,'Jobstep International d.o.o. Beograd (Jobstep International d.o.o.), sa adresom sedišta: Jurija Gagarina 32, 11070 Novi Beograd; matični broj (MB): 21569143; poreski identifikacioni broj (PIB): 111915414, koje zastupa direktor Denis Selmanović, kao pružalac usluge sa jedne strane (dalje: pružalac usluge)','','',0);
	$pdf->MultiCell(170,7,'i','','',0);
	//$pdf->MultiCell(170,5,$ime.' '.$prezime.', sa adresom prebivališta: '.$ulica.', '.$pbroj.' '.$grad.'; i sa adresom boravišta: '.$ulica_bor.', '.$postanski_broj_bor.' '.$grad_bor.'; jedinstveni matični broj građana (JMBG): '.$jmbg.'; broj lične karte: '.$licna.', izdata od: '.$izdata.', kao korisnik usluge sa druge strane (dalje: korisnik usluge),','','',0);
	$pdf->MultiCell(170,5,'Ime Prezime sa adresom prebivališta: Ulica, 101800 Beograd; i sa adresom boravišta: Ulica boravista, 1018001 Beograd; jedinstveni matični broj građana (JMBG): 12345678901234; broj lične karte: 123457, izdata od: Izdavalac, kao korisnik usluge sa druge strane (dalje: korisnik usluge),','','',0);
	$pdf->MultiCell(170,7,'(zajedno označeni kao: ugovorne strane),','','',0);
	$pdf->MultiCell(170,7,'dana '.$danas.'. godine zaključuju','','',0);
	$pdf->MultiCell(170,7,'UGOVOR O PRUŽANJU USLUGE POSREDOVANJA U POSTUPKU NOSTRIFIKACIJE DIPLOME','','C',0);
	$pdf->MultiCell(170,7,'na sledeći način:','','',0);
	
	//CLAN 1
	$pdf->MultiCell(170,8,'Član 1','','C',0);
	$pdf->MultiCell(170,5,'Predmet ovog ugovora između ugovornih strana je obavljanje usluge posredovanja u postupku nostrifikacije diplome u zemljama Evropske unije, pretežno nemačkog govornog područja.','','',0);
	$pdf->Ln(7);
	
	//CLAN 2
	$pdf->MultiCell(170,8,'Član 2','','C',0);
	$pdf->MultiCell(170,5,'Pod obavljanjem usluge posredovanja u postupku nostrifikacije diplome u smislu člana 1 ovog ugovora podrazumeva se da se ovim ugovorom pružalac usluge obavezuje da korisnika usluge uputi u sledeće:','','',0);
	$pdf->MultiCell(170,5,'- neophodna dokumentacija potrebna za nostrifikaciju diplome, ','','',0);
	$pdf->MultiCell(170,5,'- prevod dostavljene dokumentacije na nemački jezik,','','',0);
	$pdf->MultiCell(170,5,'- sprovođenje kompletnog postupka do nadležne ustanove za nostrifikaciju diplome.','','',0);
	$pdf->MultiCell(170,5,'Neophodna dokumentacija će biti definisana od strane ustanove nadležne za nostrifikaciju diplome i činiće sastavni deo ovog ugovora.','','',0);
	$pdf->Ln(7);
	$pdf->AddPage();
	
	//CLAN 3
	$pdf->MultiCell(170,8,'Član 3','','C',0);
	$pdf->MultiCell(170,5,'Za navedene poslove iz člana 2 ovog ugovora korisnik usluge se obavezuje platiti novčanu naknadu u iznosu od 59.000,00 RSD (slovima: pedesetdevethiljadadinara).','','',0);
	$pdf->Ln();	
	$pdf->MultiCell(170,5,'Korisnik usluge je dužan platiti pružaocu usluge celokupan iznos novčane naknade navedene u prethodnom stavu ovog člana Ugovora prilikom potpisivanja ugovora o pružanju usluge posredovanja u postupku nostrifikacije diplome sa pružaocem usluge. ','','',0);
	$pdf->Ln();
	$pdf->MultiCell(170,5,'Ukoliko korisnik usluge ugovorenu novčanu naknadu iz ovog člana Ugovora plaća u dve rate, avans u iznosu od 50% (slovima: pedesetposto) od novčane naknade iz stava 1 ovog člana Ugovora se plaća prilikom potpisivanja ugovora o pružanju usluge posredovanja u postupku nostrifikacije diplome sa pružaocem usluge, a ostatak iznosa novčane naknade se plaća ODMAH po okončanju postupka nostrifikacije diplome, bez obzira na ishod postupka.','','',0);
	$pdf->Ln();
	$pdf->MultiCell(170,5,'Ukoliko korisnik usluge ugovorenu novčanu naknadu iz ovog člana Ugovora plaća u tri ili više rata, novčana naknada iz stava 1 ovog člana Ugovora se plaća avansno u dogovorenom broju rata, u jednakim iznosima, tako što se prva rata plaća prilikom potpisivanja ugovora o pružanju usluge posredovanja u postupku nostrifikacije diplome sa pružaocem usluge, a počev od druge rate svaka sledeća rata se plaća u roku od mesec dana od prethodno plaćene rate, dok se poslednja rata plaća ODMAH po okončanju postupka nostrifikacije diplome, bez obzira na ishod postupka. ','','',0);
	$pdf->Ln();
	$pdf->MultiCell(170,5,'Eventualne akcije (popusti) odobravaju se na osnovu i u skladu sa pravilnikom o akcijama (popustima) i/ili odlukom o akcijama (popustima). ','','',0);
	$pdf->Ln(7);
	
	//CLAN 4
	$pdf->MultiCell(170,8,'Član 4','','C',0);
	$pdf->MultiCell(170,5,'Troškovi pribavljanja neophodne dokumentacije (takse, naknade i overa original dokumentacije), kao i trošak nostrifikacije diplome nisu uključeni u cenu navedenu u članu 3 ovog ugovora.','','',0);
	$pdf->MultiCell(170,5,'Sve neophodne dokumente dužan je pribaviti korisnik usluge o svom trošku.','','',0);
	$pdf->MultiCell(170,5,'Trošak takse nostrifikacije diplome dužan je snositi korisnik usluge u celosti.','','',0);
	$pdf->Ln(7);
	
	//CLAN 5
	$pdf->MultiCell(170,8,'Član 5','','C',0);
	$pdf->MultiCell(170,5,'Obaveza pružaoca usluge prema korisniku usluge prestaje danom okončanja postupka nostrifikacije diplome.','','',0);
	$pdf->MultiCell(170,5,'Obaveza korisnika usluge prema pružaocu usluge prestaje danom izmirenja ukupne cene usluge posredovanja u postupku nostrifikacije diplome.','','',0);
	$pdf->AddPage();
	
	//CLAN 6
	$pdf->MultiCell(170,8,'Član 6','','C',0);
	$pdf->MultiCell(170,5,'Nalogoprimac može odustati od ugovora jednostranom izjavom volje i bez povrata uplaćenih sredstava u sledećim situacijama:','','',0);
	$pdf->MultiCell(170,5,'- ukoliko nalogodavac u roku od tri meseca od dana potpisivanja ovog ugovora ne dostavi kompletnu dokumentaciju neophodnu za nostrifikaciju diplome,','','',0);
	$pdf->MultiCell(170,5,'- ukoliko se ustanovi da su dostavljene informacije i dokumenti od strane korisnika usluge falsifikati.','','',0);
	$pdf->MultiCell(170,5,'Nalogodavac je obavezan izmiriti preostali deo naknade navedene u članu 3 u roku od 7 (sedam) dana od dana prijema obaveštenja o raskidu ugovora.','','',0);
	$pdf->MultiCell(170,5,'Nalogodavac je takođe obavezan da nalogoprimcu nadoknadi štetu nastalu dostavljanjem netačnih, neispravnih i falsifikovanih dokumenata.','','',0);
	$pdf->Ln(7);
	
	//CLAN 7
	$pdf->MultiCell(170,8,'Član 7','','C',0);
	$pdf->MultiCell(170,5,'Ako nalogodavac nakon što je pokrenut postupak nostrifikacije diplome odustane svojom voljom od postupka nostrifikacije diplome neposredno nakon pokretanja istog, nalogoprimac nije dužan da vrati primljeni avans i nalogodavac je dužan da izmiri preostali, neizmireni iznos naknade iz člana 3 ovog Ugovora.','','',0);
	$pdf->Ln();	
	$pdf->MultiCell(170,5,'Pod pokretanjem postupka nostrifikacije smatra se uplata celog ili delimičnog iznosa naknade iz člana 3. ovog ugovora. ','','',0);
	$pdf->Ln(7);
	
	//CLAN 8
	$pdf->MultiCell(170,8,'Član 8','','C',0);
	$pdf->MultiCell(170,5,'U skladu sa Zakonom o zaštiti podataka o ličnosti, korisnik usluge je prilikom komunikacije sa pružaocem usluge, a na ime posredovanja u postupku nostrifikacije diplome, te zaključenja ovog ugovora, obavešten od strane pružaoca usluge o svrsi i načinu obrade (prikupljanje, obrada, čuvanje i uništavanje) podataka o ličnosti korisnika usluge, a koju pružalac usluge vrši u svojstvu rukovaoca, prikupljajući podatke od korisnika usluge, koji su neophodno potrebni radi obavljanja usluge posredovanja u postupku nostrifikacije diplome- kao što su: ime i prezime, datum i mesto rođenja, adresa prebivališta, adresa boravišta, JMBG, br. lične karte i izdavalac lične karte, obrazovanje- zvanje  i zanimanje, diploma (dokument), CV, kontakt podaci- mejl adresa, adresa stanovanja, broj telefona i dr. (podaci o ličnosti korisnika usluge koji se obrađuju uključuju, ali izuzetno ne i ograničavaju prethodno navedene lične podatke). ','','',0);
	$pdf->AddPage();
	$pdf->MultiCell(170,5,'Podaci o ličnosti korisnika usluge, i to: ime i prezime, adresa stanovanja i kontakt telefon biće dati kurirskoj službi (a koja je takođe dužna voditi računa i brinuti o zaštiti podataka o ličnosti u poslovanju, shodno zakonu, propisima i ugovoru sa pružaocem usluge), a radi slanja i dostavljanja potrebne dokumentacije na ime ugovorene usluge. ','','',0);
	$pdf->Ln();
	$pdf->MultiCell(170,5,'Namera je da se podaci iznesu u Republiku Nemačku, koja se nalazi na listi iz člana 64 stav 7 Zakona o zaštiti podataka o ličnosti, tj. država, delova njihovih teritorija ili jednog ili više sektora određenih delatnosti u tim državama i međunarodnih organizacija u kojima se smatra da jeste obezbeđen primereni nivo zaštite podataka.','','',0);
	$pdf->Ln();
	$pdf->MultiCell(170,5,'O eventualnoj potrebi da lične podatke korisnika usluge obrađuje u svrhu koja je različita od svrhe za koju su dati podaci prikupljeni, pružalac usluge će pre započinjanja dalje obrade podataka obavestiti korisnika usluge o svim informacijama vezanim za tu drugu svrhu obrade datih podataka, kao i o svim drugim važnim informacijama koje su sa tim u vezi. ','','',0);
	$pdf->Ln();
	$pdf->MultiCell(170,5,'Ukoliko korisnik usluge ima bilo kakvih pitanja, dilema ili nejasnoća u vezi sa postupanjem sa njegovim ličnim podacima, može se obratiti pružaocu usluge na njegove kontakt podatke, koji su navedeni i istaknuti na internet sajtu i/ili oglasnoj tabli pružoca usluge (info@job-step.rs).','','',0);
	$pdf->Ln(7);
	
	//CLAN 9
	$pdf->MultiCell(170,8,'Član 9','','C',0);
	$pdf->MultiCell(170,5,'Korisnik usluge uz ovaj ugovor potpisuje i Informativni list, a koji je prethodno u celosti pročitao i razumeo i koji mu je rastumačen, koji čini sastavni deo ovog ugovora.','','',0);
	$pdf->Ln(7);
	
	//CLAN 10
	$pdf->MultiCell(170,8,'Član 10','','C',0);
	$pdf->MultiCell(170,5,'Na sva prava, obaveze i odgovornosti koje nisu uređene ovim ugovorom primenjuju se odgovarajuće odredbe propisa.','','',0);
	$pdf->Ln(7);
	
	//CLAN 11
	$pdf->MultiCell(170,8,'Član 11','','C',0);
	$pdf->MultiCell(170,5,'Sve eventualne sporove koji nastanu po osnovu ovog ugovora ugovorne strane će nastojati da reše mirnim putem sporazumno, a ako to ne bude moguće ugovaraju mesnu nadležnost stvarno nadležnog suda u Beogradu.','','',0);
	$pdf->Ln();
	$pdf->AddPage();
	
	
	//CLAN 12
	$pdf->MultiCell(170,8,'Član 12','','C',0);
	$pdf->MultiCell(170,5,'Obe ugovorne strane su Ugovor u celosti pročitale i razumele, isti im je rastumačen, pa ga u znaku da on u potpunosti sadrži i odražava njihovu izraženu volju svojeručno potpisuju.','','',0);
	$pdf->Ln(3);
	$pdf->MultiCell(170,5,'Ovaj ugovor sačinjen je u 4 (četiri) istovetna primerka, od kojih svaka ugovorna strana zadržava po 2 (dva) primerka. ','','',0);
	$pdf->Ln(3);
	$pdf->MultiCell(170,5,'Ovaj ugovor stupa na snagu danom potpisivanja obeju ugovornih strana.','','',0);
	$pdf->Ln(10);
	
	$pdf->MultiCell(170,5,'Broj ugovora: '.$txt_broj_ugovora.'','','',0);
	$pdf->Cell(120,5,'U Beogradu, dana '.$danas.'. godine',0,'','L',0);
	$pdf->Cell(50,5,'',0,'','L',0);
	$pdf->Ln(10);
	
	//$pdf->Image('images/srb_potpis.png',20,135,35);
	
	$pdf->Cell(120,7,'ZA PRUŽAOCA USLUGE: ',0,'','L',0);
	$pdf->Cell(50,7,'ZA KORISNIKA USLUGE: ','','','L',0);
	$pdf->Ln();
	$pdf->Cell(120,7,'______________________________',0,'','L',0);
	$pdf->Cell(50,7,'______________________________',0,'','L',0);
	$pdf->Ln();
	$pdf->Cell(120,7,'(mesto i datum)',0,'','L',0);
	$pdf->Cell(50,7,'(mesto i datum)',0,'','L',0);
	$pdf->Ln();
	$pdf->Cell(120,7,'______________________________',0,'','L',0);
	$pdf->Cell(50,7,'______________________________',0,'','L',0);
	$pdf->Ln();
	$pdf->Cell(120,7,'(potpis i pečat)',0,'','L',0);
	$pdf->Cell(50,7,'(ime, prezime i potpis)',0,'','L',0);
	$pdf->Ln();
	

	$filename="files/ugovori_uplatnice_dipl/".$ugovor_putanja;
	
	$pdf->Output();
	//$pdf->Output($filename,'F');
	
	return $ugovor_putanja;
}

function testcreateUgovorBIH($kandidat_id){
	Global $db;
	Global $logged_employee_id;
	Global $vrsta_dokumenta;
	$vrsta_dokumenta = "";
	$danas = date('d.m.Y');
	$datetime = date('Y-m-d H:i:s');
	$date_ispis = date('d.m.Y');
	$mjesec = date('m');
	$godina = date('y');
	$day = date('d');
	if($day > 28)
		$day = 1;
	//pokupiti info iz baze
	$get_info = $db->prepare("
					SELECT ime_nd_kandidata, prezime_nd_kandidata, ulica_nd_kandidata, postanski_broj_nd_kandidata, grad_nd_kandidata, vrsta_ugovora_nd_kandidata, jmbg_nd_kandidata, broj_licne_karte_nd_kandidata
					FROM idk_nd_kandidata
					WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata
					");

	$get_info->execute(array(
						':id_broj_nd_kandidata' => $kandidat_id));

	$info_row = $get_info->fetch();
	
	$prezime = $info_row["prezime_nd_kandidata"];
	$ime = $info_row["ime_nd_kandidata"];
	$grad = $info_row["grad_nd_kandidata"];
	$ulica = $info_row["ulica_nd_kandidata"];
	$vrsta_ugovora = $info_row["vrsta_ugovora_nd_kandidata"];
	$jmbg = $info_row["jmbg_nd_kandidata"];
	$licna = $info_row["broj_licne_karte_nd_kandidata"];
	
	$txt_iznos_bez_p = "950,00 KM (slovima:devestotinapedeset i 00/100 KM)";
	
	$brojac_ugovora = createBrojUgovora("BiH");
	$ugovor_putanja = "UGB-".$brojac_ugovora."-".$mjesec."-".$godina.".pdf";
	
	$txt_broj_ugovora = $brojac_ugovora."-".$mjesec."/".$godina;
	//INSERT UGOVORA
	/*$insert_ugovor = $db->prepare("	
				INSERT INTO idk_nd_kandidata_dokumenti	
				(naziv_dokument_nd,  naziv_dokument_ostali_nd, id_kandidata_dokument_nd, vrijeme_dodavanja_dokument_nd, dodao_zaposlenik_dokument_nd, tip_dokumenta)	
				VALUES	
				(:naziv_dokument_nd,:naziv_dokument_ostali_nd,:id_kandidata_dokument_nd,:vrijeme_dodavanja_dokument_nd,:dodao_zaposlenik_dokument_nd,:tip_dokumenta)	
				");	
	$insert_ugovor->execute(array(	
				':naziv_dokument_nd' => $ugovor_putanja,	
				':naziv_dokument_ostali_nd' => "ugovor",	
				':id_kandidata_dokument_nd' => $kandidat_id,	
				':vrijeme_dodavanja_dokument_nd' => $datetime,	
				':dodao_zaposlenik_dokument_nd' => $logged_employee_id,	
				':tip_dokumenta' => 1
				));
	*/
	// Header
	$txt_head_1 = "„Jobstep International“ d.o.o. Bihać, ul. Hamze Hume bb- Poslovna zona Kombiteks, JIB: 4263788850005, zastupano po direktoru, Nanić Emiru (u daljnjem tekstu: nalogoprimac), ";
	$txt_head_2 = "i  ".$prezime." ".$ime." iz ".$grad." ul.  ".$ulica.", JMB: ".$jmbg.", broj LK: ".$licna." (u daljnjem tekstu: nalogodavac), s druge strane";
	$txt_head_3 = "sklapaju ovaj";

	$txt_naslov_1 = "UGOVOR";	
	$txt_naslov_2 = "O OBRADI PODATAKA U VEZI SA";	
	$txt_naslov_3 = "NOSTRIFIKACIJOM DIPLOMA";	

	// CLAN 1
	$c1_tacka1 = "Predmet ovog ugovora  između ugovornih strana je obavljanje usluge obrade podataka  u vezi sa nostrifikacijom diploma u zemljama Europske unije, pretežito njemačkog govornog područja.";
	
	// CLAN 2
	$c2_tacka1 =  "Pod obavljanjem usluga obrade podataka u vezi sa nostrifikacijom diploma u smislu člana 1. ovog ugovora podrazumijeva se da se ovim ugovorom nalogoprimac obavezuje da  nalogodavca uputi u sljedeće:";
	$c2_tacka2 = "Neophodna dokumentacija potrebna za nostrifikaciju diplome";
	$c2_tacka3 = "Prevod dostavljene dokumentacije na njemački jezik";
	$c2_tacka4 = "Provođenje kompletnog postupka do nadležne ustanove";
	$c2_tacka5 = "Neophodna dokumentacija će biti definisana od strane  Ustanove nadležne za nostrifikaciju i biti će sastavni dio ovog ugovora.";
	
	// CLAN 3
	$c3_tacka1 = "Za navedene poslove iz člana  2. ovog ugovora nalogodavac se obavezuje platiti naknadu u iznosu od ".$txt_iznos_bez_p.".";
	
	$c3_tacka3 = "Nalogoprimac može, na zahtjev nalogodavca, odobriti plaćanje naknade iz stava 1.ovog člana na rate.";
	$c3_tacka4 = "Nalogodavac je dužan plaćati ugovorenu naknade u skladu sa planom plaćanja koji je sastavni dio ovog ugovora.";
	$c3_popusti = "Eventualni popusti na ugovoreni iznos naknade odobravaju se na osnovu  Pravilnika o uvjetima i načinu formiranja cijena usluga i odluke o odobravanju popusta.";
	
	// CLAN 4
	$c4_tacka1 = "Troškovi pribavljanja neophodne dokumentacije (takse, naknade i ovjera original dokumentacije), kao i trošak nostrifikacije diplome nisu uključeni u cijenu navedenu u članu 3.";
	$c4_tacka2 = "Sve neophodne dokumente dužan je pribaviti nalogodavac o svom trošku.";
	$c4_tacka3 = "Trošak takse nostrifikacije diplome dužan je snositi nalogodavac u cijelosti.";
	
	// CLAN 5
	$c5_tacka1 = "Obaveza nalogoprimca prema nalogodavcu prestaje danom okončanja postupka nostrifikacije, neovisno od ishoda postupka.";
	$c5_tacka2 = "Obaveza nalogodavca prema nalogoprimcu prestaje danom izmirenja ukupne cijene usluge obrade podataka u vezi sa nostrifikacijom diploma.";
	
	// CLAN 6
	$c6_tacka1 = "Nalogoprimac može odustati od ugovora bez povrata uplaćenog iznosa u sljedećim situacijama:";
	$c6_tacka2 = "Ukoliko nalogodavac u roku od tri mjeseca od dana potpisivanja ovog ugovora ne dostavi kompletnu dokumentaciju neophodnu za nostrifikaciju diplome, pri čemu je nalogoprimac dužan izmiriti i preostale, neplaćene rate naknade navedene u članu 3. ovog Ugovora,";
	$c6_tacka3 = "Ukoliko se ustanovi da su dostavljene informacije i dokumenti od strane nalogodavca falsifikati, pri čemu je nalogoprimac dužan nadoknaditi štetu nalogoprimcu koja je srazmjerna troškovima koje je nalogoprimac imao do trenutka raskida ugovora.";
	
	// CLAN 7
	$c7_tacka1 = "Ako nalogodavac nakon što je pokrenut postupak nostrifikacije diplome odustane svojom voljom od postupka nostrifikacije diplome neposredno nakon pokretanja istog, nalogoprimac nije dužan da mu vrati do tada uplaćene rate.";
	$c7_tacka2 = "Pod pokretanjem postupka nostrifikacije smatra se uplata prve rate.";
	$c7_tacka3 = "Ukoliko nalogodavac odustane od Ugovora nakon pokretanja postupka nostrifikacije, odnosno nakon slanja zahtjeva za nostrifikaciju dužan je izmiriti i preostale, neplaćene rate naknade navedene u članu 3. ovog Ugovora.";
	
	// CLAN 8
	$c8_tacka1 = "Sve eventualne sporove stranke će rješavati sporazumno.";
	$c8_tacka2 = "Ukoliko nije moguće rješenje prema stavu 1. ovog člana nadležan će biti stvarno nadležni sud u Bihaću.";
	
	// CLAN 9
	$c9_tacka1 = "Ovaj ugovor sastavljen je u 4 (slovima: četiri) istovjetna primjerka, od kojih po 2 (slovima: dva) pripadaju svakoj od ugovornih strana.";
	
	// CLAN 10
	$c10_tacka1 = "Ovaj ugovor stupa na snagu danom potpisivanja obiju stranaka.";
	
	
	$pdf = new tFPDF();
	$pdf->AddPage();

	// Add a Unicode font (uses UTF-9)
	$pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
	$pdf->AddFont('DejaVu','B','DejaVuSansCondensed-Bold.ttf',true);
	$pdf->AddFont('DejaVuSerif','','DejaVuSerifCondensed.ttf',true);
	$pdf->AddFont('DejaVuSerif','B','DejaVuSerifCondensed-Bold.ttf',true);
	$pdf->AddFont('DejaVuSerif','BI','DejaVuSerifCondensed-BoldItalic.ttf',true);

	$pdf->SetLeftMargin(20);
	$pdf->Ln(20);
	$pdf->SetFont('DejaVuSerif','',11);
	
	$pdf->MultiCell(170,5,''.$txt_head_1.'','','J',0);
	$pdf->Ln(3);
	$pdf->MultiCell(170,5,''.$txt_head_2.'','','J',0);
	$pdf->Ln(3);
	$pdf->MultiCell(170,5,''.$txt_head_3.'','','J',0);
	$pdf->Ln(5);
	
	$pdf->SetFont('DejaVuSerif','BI',12);
	$pdf->Cell(170,5,''.$txt_naslov_1.'','',1,'C',0);
	$pdf->Cell(170,5,''.$txt_naslov_2.'','',1,'C',0);
	$pdf->Cell(170,5,''.$txt_naslov_3.'','',1,'C',0);
	$pdf->Ln(15);
	
	//CLAN 1
	$pdf->SetFont('DejaVuSerif','BI',12);
	$pdf->MultiCell(170,5,'Član 1.','','C',0);
	$pdf->Ln(5);
	$pdf->SetFont('DejaVuSerif','',11);
	$pdf->MultiCell(170,5,''.$c1_tacka1.'','','J',0);
	$pdf->Ln(10);
	
	//CLAN 2
	$pdf->SetFont('DejaVuSerif','BI',12);
	$pdf->MultiCell(170,5,'Član 2.','','C',0);
	$pdf->Ln(5);
	$pdf->SetFont('DejaVuSerif','',11);
	$pdf->MultiCell(170,5,''.$c2_tacka1.'','','J',0);
	$pdf->Ln();
	$pdf->Cell(10,5,' - ','','R',0);
	$pdf->MultiCell(160,5,''.$c2_tacka2.'','','J',0);
	$pdf->Cell(10,5,' - ','','R',0);
	$pdf->MultiCell(160,5,''.$c2_tacka3.'','','J',0);
	$pdf->Cell(10,5,' - ','','R',0);
	$pdf->MultiCell(160,5,''.$c2_tacka4.'','','J',0);
	$pdf->Ln();
	$pdf->MultiCell(170,5,''.$c2_tacka5.'','','J',0);
	$pdf->Ln(10);

	//CLAN 3
	$pdf->SetFont('DejaVuSerif','BI',12);
	$pdf->MultiCell(170,5,'Član 3.','','C',0);
	$pdf->Ln(5);
	$pdf->SetFont('DejaVuSerif','',11);
	$pdf->MultiCell(170,5,''.$c3_tacka1.'','','J',0);
	$pdf->Ln();
	
	$pdf->MultiCell(170,5,''.$c3_tacka3.'','','L',0);
	$pdf->Ln();
	$pdf->MultiCell(170,5,''.$c3_tacka4.'','','L',0);
	$pdf->Ln();
	
	$pdf->MultiCell(170,5,''.$c3_popusti.'','','L',0);
	$pdf->Ln(10);
	$pdf->AddPage();
	
	//CLAN 4
	$pdf->Ln(10);
	$pdf->SetFont('DejaVuSerif','BI',12);
	$pdf->MultiCell(170,5,'Član 4.','','C',0);
	$pdf->Ln(5);
	$pdf->SetFont('DejaVuSerif','',11);
	$pdf->MultiCell(170,5,''.$c4_tacka1.'','','J',0);
	$pdf->MultiCell(170,5,''.$c4_tacka2.'','','J',0);
	$pdf->Ln();
	$pdf->MultiCell(170,5,''.$c4_tacka3.'','','J',0);
	$pdf->Ln(10);
	
	//CLAN 5
	$pdf->SetFont('DejaVuSerif','BI',12);
	$pdf->MultiCell(170,5,'Član 5.','','C',0);
	$pdf->Ln(5);
	$pdf->SetFont('DejaVuSerif','',11);
	$pdf->MultiCell(170,5,''.$c5_tacka1.'','','J',0);
	$pdf->Ln();
	$pdf->MultiCell(170,5,''.$c5_tacka2.'','','J',0);
	$pdf->Ln(10);
	
	//CLAN 6
	$pdf->SetFont('DejaVuSerif','BI',12);
	$pdf->MultiCell(170,5,'Član 6.','','C',0);
	$pdf->Ln(5);
	$pdf->SetFont('DejaVuSerif','',11);
	$pdf->MultiCell(170,5,''.$c6_tacka1.'','','J',0);
	$pdf->Cell(10,5,' - ','','R',0);
	$pdf->MultiCell(160,5,''.$c6_tacka2.'','','J',0);
	$pdf->Cell(10,5,' - ','','R',0);
	$pdf->MultiCell(160,5,''.$c6_tacka3.'','','J',0);
	$pdf->Ln(10);
	
	//CLAN 7
	$pdf->SetFont('DejaVuSerif','BI',12);
	$pdf->MultiCell(170,5,'Član 7.','','C',0);
	$pdf->Ln(5);
	$pdf->SetFont('DejaVuSerif','',11);
	$pdf->MultiCell(170,5,''.$c7_tacka1.'','','J',0);
	$pdf->Ln();
	$pdf->MultiCell(170,5,''.$c7_tacka2.'','','J',0);
	$pdf->Ln();
	$pdf->MultiCell(170,5,''.$c7_tacka3.'','','J',0);
	$pdf->Ln(10);
	
	//CLAN 8
	$pdf->SetFont('DejaVuSerif','BI',12);
	$pdf->MultiCell(170,5,'Član 8.','','C',0);
	$pdf->Ln(5);
	$pdf->SetFont('DejaVuSerif','',11);
	$pdf->MultiCell(170,5,''.$c8_tacka1.'','','J',0);
	$pdf->Ln();
	$pdf->MultiCell(170,5,''.$c8_tacka2.'','','J',0);
	$pdf->Ln(10);
	$pdf->AddPage();
	
	//CLAN 9
	$pdf->SetFont('DejaVuSerif','BI',12);
	$pdf->MultiCell(170,5,'Član 9.','','C',0);
	$pdf->Ln(5);
	$pdf->SetFont('DejaVuSerif','',11);
	$pdf->MultiCell(170,5,''.$c9_tacka1.'','','J',0);
	$pdf->Ln(10);
	
	//CLAN 10
	$pdf->Ln(10);
	$pdf->SetFont('DejaVuSerif','BI',12);
	$pdf->MultiCell(170,5,'Član 10.','','C',0);
	$pdf->Ln(5);
	$pdf->SetFont('DejaVuSerif','',11);
	$pdf->MultiCell(170,5,''.$c10_tacka1.'','','J',0);
	$pdf->Ln(20);
	
	$pdf->Image('images/bih_potpis_new.png',20,90,70);
	
	$pdf->Cell(120,5,'Nalogoprimac: ',0,'','L',0);
	$pdf->Cell(50,5,'Nalogodavac: ',0,'','L',0);
	$pdf->Ln(25);
	$pdf->Cell(40,5,'Bihać, dana '.$date_ispis.'',0,'','L',0);


	$filename="files/ugovori_uplatnice_dipl/".$ugovor_putanja;
	
	$pdf->Output();
	//$pdf->Output($filename,'F');
	
	return $ugovor_putanja;
}

?>