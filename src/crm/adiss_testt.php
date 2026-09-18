<?php
	include("includes/functions.php");
	include("includes/common.php");

	include_once($_SERVER["DOCUMENT_ROOT"] . '/jobstep_pp/includes/classes/candidatesProjection.php');
	$durationPerStatus = new durationPerStatus();

	function checkSlucajScript($statusKanididata) {
		$statusKanididata = intval($statusKanididata); 
		$result = array();
		if ( in_array($statusKanididata, array(9,12,15,18,21,24)) ) {
			array_push($result, "ugovor");
		} else if ( in_array($statusKanididata, array(27)) ) {
			array_push($result, "ugovor");
			array_push($result, "dobio vizu");
		} else {
			array_push($result, "ugovor");
			array_push($result, "dobio vizu");
			array_push($result, "pocetak rada");
			array_push($result, "mjeseci nakon");
		}
		return $result;
	}

	function generisiRateZaKandidataCOPY($kandidat_id, $kandidat_status_prijave, $nalog_id, $durationPerStatus){
		Global $db;
		$nalog_nacin_placanja = getNalogNacinPlacanja($nalog_id);
	
		$candidatesProjection = new candidatesProjection($durationPerStatus, $kandidat_id);
		$candidate_date = $candidatesProjection -> getCandidateProjectionRows();
		$candidate_case = checkSlucajScript($kandidat_status_prijave);

		switch($nalog_nacin_placanja){
			//standardni nacin
			case 1:
				$rate = getNalogRate($nalog_id);
				$provizija = getNalogProvizija($nalog_id);
	
				foreach($rate as $rata){
					if($rata['nr_vrijeme_placanja'] != "odmah"){
						$iznos = getIznosRateKandidata($provizija, $rata['nr_procenat'], null, $nalog_nacin_placanja);
						
						if($rata['nr_vrijeme_placanja'] == "ugovor"){

							/* Na osnovu statusa prijave - projeravaj koje vrijeme ugovora uzeti START */
							$kf_placeno_ugovor = 4; 
							if (in_array("ugovor", $candidate_case)){
								$queryCheck = $db->prepare("
									SELECT 
										MAX(lsp.lsp_datetime) AS datum_ugovor
									FROM 
										idk_log_statusi_prijave lsp
									INNER JOIN 
										idk_projects pr 
									ON 
										pr.project_id = lsp.lsp_projekt_id
									WHERE 
										lsp.lsp_kandidat_id = :lsp_kandidat_id 
										AND 
										lsp.lsp_status_prijave_id = 9 
										AND 
										pr.project_nalogid = :nalog_id
								");
								$queryCheck->execute(array(
									':lsp_kandidat_id' => $kandidat_id, 
									':nalog_id' => $nalog_id
								));

								if ($queryCheck->rowCount() == 1) {
									$rowCheck = $queryCheck->fetch();
									if ( $rowCheck["datum_ugovor"] != NULL ) {
										$datum_ugovor = date("Y-m-d", strtotime($rowCheck["datum_ugovor"]));

										if (checkDatum($datum_ugovor) == 1){
											$kf_placeno_ugovor = 1;
										}else{
											$kf_placeno_ugovor = 0;
										}
										
									} else {
										$datum_ugovor = date("Y-m-d");
									}
								} else {
									$datum_ugovor = date("Y-m-d");
								}
							} else {
								$datum_ugovor = date("Y-m-d");
							}
							/* Na osnovu statusa prijave - projeravaj koje vrijeme ugovora uzeti END  */

							$date = $datum_ugovor;
							$rata_id = $rata['nr_id'];
	
							$sql = "INSERT INTO 
										idk_kandidat_financije (nalog_id, kandidat_id, kf_datum, kf_datum_stvarni, kf_nalog_rata_id, kf_type, kf_iznos, kf_placeno)
									VALUES 
										(:nalog_id, :kandidat_id, :datum, :datum_stvarni, :kf_nalog_rata_id, 1, :iznos, :placeno)";
	
							$query = $db->prepare($sql);
							$query->execute(array(
								':nalog_id' => $nalog_id,
								':kandidat_id' => $kandidat_id,
								':datum' => $date,
								':datum_stvarni' => $date,
								':kf_nalog_rata_id' => $rata_id,
								':iznos' => $iznos, 
								':placeno' => $kf_placeno_ugovor
							));
	
						}
						elseif($rata['nr_vrijeme_placanja'] == "dobio vizu"){

							/* Na osnovu statusa prijave - projeravaj koje vrijeme vize uzeti START */
							$kf_placeno_dobio_vizu = 0; 
							if (in_array("dobio vizu", $candidate_case)){
								$queryCheck = $db->prepare("
									SELECT 
										kandidat_viza_vrijedi_od, kandidat_dogovoreni_pocetak_rada, kandidat_potvrden_pocetak_rada
									FROM 
										idk_kandidati 
									WHERE 
										kandidat_id = :kandidat_id 
										AND 
										kandidat_nalog_id  = :kandidat_nalog_id 
								");
								$queryCheck->execute(array(
									':kandidat_id' => $kandidat_id, 
									':kandidat_nalog_id' => $nalog_id
								));

								if ($queryCheck->rowCount() == 1) {
									$rowCheck = $queryCheck->fetch();
									if ($rowCheck["kandidat_viza_vrijedi_od"] != NULL){
										$datum_viza = date("Y-m-d", strtotime($rowCheck["kandidat_viza_vrijedi_od"]));
										$kf_placeno_dobio_vizu = 1;
									} else {
										/* Provjera pocetak rada ako nema datum vize START*/
										if (intval($rowCheck["kandidat_potvrden_pocetak_rada"]) == 1) { 
											if ( $rowCheck["kandidat_dogovoreni_pocetak_rada"] != NULL ) {
												$datum_viza = date("Y-m-d", strtotime($rowCheck["kandidat_dogovoreni_pocetak_rada"]));
												// datum_viza = datum_pocetak_rada
												$kf_placeno_dobio_vizu = 1;
											}else {
												$kf_placeno_dobio_vizu = 4;
												$datum_viza = date("Y-m-d");
											}
										} else {
											$kf_placeno_dobio_vizu = 5;
											$datum_viza = date("Y-m-d");
										}
										/* Provjera pocetak rada ako nema datum vize START*/
									}
								} else {
									$kf_placeno_dobio_vizu = 4;
									$datum_viza = date("Y-m-d");
								}
							} else {
								$kf_placeno_dobio_vizu = 0;
								$datum_viza = date("Y-m-d", $candidate_date[0]["visa_acquired"]);
							}
							/* Na osnovu statusa prijave - projeravaj koje vrijeme vize uzeti END  */

							$date = $datum_viza;
							$rata_id = $rata['nr_id'];
							
							$sql = "INSERT INTO 
										idk_kandidat_financije (nalog_id, kandidat_id, kf_datum, kf_datum_stvarni, kf_nalog_rata_id, kf_type, kf_iznos, kf_placeno)
									VALUES 
										(:nalog_id, :kandidat_id, :datum, :datum_stvarni, :kf_nalog_rata_id, 2, :iznos, :kf_placeno)";
	
							$query = $db->prepare($sql);
							$query->execute(array(
								':nalog_id' => $nalog_id,
								':kandidat_id' => $kandidat_id,
								':datum' => $date,
								':datum_stvarni' => $date,
								':kf_nalog_rata_id' => $rata_id,
								':iznos' => $iznos, 
								':kf_placeno' => $kf_placeno_dobio_vizu
							));
						}
						elseif($rata['nr_vrijeme_placanja'] == "pocetak rada"){

							/* Na osnovu statusa prijave - projeravaj koje vrijeme početka rada uzeti START */
							$kf_placeno_pocetak_rada = 0; 
							if (in_array("pocetak rada", $candidate_case)){
								$queryCheck = $db->prepare("
									SELECT 
										kandidat_dogovoreni_pocetak_rada, kandidat_potvrden_pocetak_rada
									FROM 
										idk_kandidati 
									WHERE 
										kandidat_id = :kandidat_id 
										AND 
										kandidat_nalog_id = :kandidat_nalog_id 
								");
								$queryCheck->execute(array(
									':kandidat_id' => $kandidat_id, 
									':kandidat_nalog_id' => $nalog_id
								));

								if ($queryCheck->rowCount() == 1) {
									$rowCheck = $queryCheck->fetch();
									if (intval($rowCheck["kandidat_potvrden_pocetak_rada"]) == 1) {
										if ( $rowCheck["kandidat_dogovoreni_pocetak_rada"] != NULL ) {
											$pocetak_rada = date("Y-m-d", strtotime($rowCheck["kandidat_dogovoreni_pocetak_rada"]));
											$kf_placeno_pocetak_rada = 1;
										}else {
											$kf_placeno_pocetak_rada = 4;
											$pocetak_rada = date("Y-m-d");
										}
									} else {
										$kf_placeno_pocetak_rada = 4;
										$pocetak_rada = date("Y-m-d");
									}
								} else {
									$kf_placeno_pocetak_rada = 4;
									$pocetak_rada = date("Y-m-d");
								}
							} else {
								$kf_placeno_pocetak_rada = 0;
								$pocetak_rada = date("Y-m-d", $candidate_date[0]["candidate_assessment"]);
							}
							/* Na osnovu statusa prijave - projeravaj koje vrijeme početka rada uzeti END  */

							$date = $pocetak_rada;
	
							$rata_id = $rata['nr_id'];
	
							$sql = "INSERT INTO 
										idk_kandidat_financije (nalog_id, kandidat_id, kf_datum, kf_datum_stvarni, kf_nalog_rata_id, kf_type, kf_iznos, kf_placeno)
									VALUES 
										(:nalog_id, :kandidat_id, :datum, :datum_stvarni, :kf_nalog_rata_id, 3, :iznos, :kf_placeno)";
							$query = $db->prepare($sql);
							$query->execute(array(
								':nalog_id' => $nalog_id,
								':kandidat_id' => $kandidat_id,
								':datum' => $date,
								':datum_stvarni' => $date,
								':kf_nalog_rata_id' => $rata_id,
								':iznos' => $iznos,
								':kf_placeno' => $kf_placeno_pocetak_rada
							));
	
						}
						elseif($rata['nr_vrijeme_placanja'] == "mjeseci nakon"){
							$broj_mjeseci = $rata['nr_mjeseci_nakon'];
							
							/* Na osnovu statusa prijave - projeravaj koje vrijeme početka rada uzeti START */
							$kf_placeno_pocetak_rada = 0; 
							if (in_array("pocetak rada", $candidate_case)){
								$queryCheck = $db->prepare("
									SELECT 
										kandidat_dogovoreni_pocetak_rada, kandidat_potvrden_pocetak_rada
									FROM 
										idk_kandidati 
									WHERE 
										kandidat_id = :kandidat_id 
										AND 
										kandidat_nalog_id  = :kandidat_nalog_id 
								");
								$queryCheck->execute(array(
									':kandidat_id' => $kandidat_id, 
									':kandidat_nalog_id' => $nalog_id
								));

								if ($queryCheck->rowCount() == 1) {
									$rowCheck = $queryCheck->fetch();
									if (intval($rowCheck["kandidat_potvrden_pocetak_rada"]) == 1) {
										if ( $rowCheck["kandidat_dogovoreni_pocetak_rada"] != NULL ) {
											$pocetak_rada_date = date("Y-m-d", strtotime($rowCheck["kandidat_dogovoreni_pocetak_rada"]));
											$pocetak_rada = date('Y-m-d', strtotime($pocetak_rada_date . " + $broj_mjeseci months"));
											$kf_placeno_pocetak_rada = 1;
										}else {
											$pocetak_rada = date("Y-m-d");
											$kf_placeno_pocetak_rada = 4;
										}
									} else {
										$pocetak_rada = date("Y-m-d");
										$kf_placeno_pocetak_rada = 4;
									}
								} else {
									$kf_placeno_pocetak_rada = 4;
									$pocetak_rada = date("Y-m-d");
								}
							} else {
								$pocetak_rada_date = date("Y-m-d", $candidate_date[0]["candidate_assessment"]);
								$pocetak_rada = date('Y-m-d', strtotime($pocetak_rada_date . " + $broj_mjeseci months"));
								$kf_placeno_pocetak_rada = 0;
							}
							/* Na osnovu statusa prijave - projeravaj koje vrijeme početka rada uzeti END  */
							$date = $pocetak_rada;
							$rata_id = $rata['nr_id'];
	
							$sql = "INSERT INTO 
										idk_kandidat_financije (nalog_id, kandidat_id, kf_datum, kf_datum_stvarni, kf_nalog_rata_id, kf_type, kf_iznos, kf_placeno)
									VALUES 
										(:nalog_id, :kandidat_id, '$date', '$date', :kf_nalog_rata_id, 4, :iznos, :kf_placeno)";
	
							$query = $db->prepare($sql);
							$query->execute(array(
								':nalog_id' => $nalog_id,
								':kandidat_id' => $kandidat_id,
								
								':kf_nalog_rata_id' => $rata_id,
								':iznos' => $iznos, 
								':kf_placeno' => $kf_placeno_pocetak_rada
							));
						}
					}
				}
			break;
	
			//po plati kandidata
			case 3:
				$rate = getNalogRate($nalog_id);
				$provizija = getNalogProvizijaPlata($nalog_id);
				$plata = getKandidatPlata($kandidat_id);
	
				foreach($rate as $rata){
					if($rata['nr_vrijeme_placanja'] != "odmah"){
						$iznos = getIznosRateKandidata($provizija, $rata['nr_procenat'], $plata, $nalog_nacin_placanja);
	
						switch($rata['nr_vrijeme_placanja']){
							case "ugovor":

								/* Na osnovu statusa prijave - projeravaj koje vrijeme ugovora uzeti START */
								$kf_placeno_ugovor = 4; 
								if (in_array("ugovor", $candidate_case)){
									$queryCheck = $db->prepare("
										SELECT 
											MAX(lsp.lsp_datetime) AS datum_ugovor
										FROM 
											idk_log_statusi_prijave lsp
										INNER JOIN 
											idk_projects pr 
										ON 
											pr.project_id = lsp.lsp_projekt_id
										WHERE 
											lsp.lsp_kandidat_id = :lsp_kandidat_id 
											AND 
											lsp.lsp_status_prijave_id = 9 
											AND 
											pr.project_nalogid = :nalog_id
									");
									$queryCheck->execute(array(
										':lsp_kandidat_id' => $kandidat_id, 
										':nalog_id' => $nalog_id
									));

									if ($queryCheck->rowCount() == 1) {
										$rowCheck = $queryCheck->fetch();
										if ( $rowCheck["datum_ugovor"] != NULL ) {
											$datum_ugovor = date("Y-m-d", strtotime($rowCheck["datum_ugovor"]));
											if (checkDatum($datum_ugovor) == 1){
												$kf_placeno_ugovor = 1;
											}else{
												$kf_placeno_ugovor = 0;
											}
										} else {
											$datum_ugovor = date("Y-m-d");
										}
									} else {
										$datum_ugovor = date("Y-m-d");
									}
								} else {
									$datum_ugovor = date("Y-m-d");
								}
								/* Na osnovu statusa prijave - projeravaj koje vrijeme ugovora uzeti END  */

								$date = $datum_ugovor;
								$rata_id = $rata['nr_id'];
	
								$sql = "INSERT INTO 
											idk_kandidat_financije (nalog_id, kandidat_id, kf_datum, kf_datum_stvarni, kf_nalog_rata_id, kf_type, kf_iznos, kf_placeno)
										VALUES 
											(:nalog_id, :kandidat_id, :datum, :datum_stvarni, :kf_nalog_rata_id, 1, :iznos, :kf_placeno)";
	
								$query = $db->prepare($sql);
								$query->execute(array(
									':nalog_id' => $nalog_id,
									':kandidat_id' => $kandidat_id,
									':datum' => $date,
									':datum_stvarni' => $date,
									':kf_nalog_rata_id' => $rata_id,
									':iznos' => $iznos, 
									':kf_placeno' => $kf_placeno_ugovor
								));
	
							break;
	
							case "dobio vizu":

								/* Na osnovu statusa prijave - projeravaj koje vrijeme vize uzeti START */
								$kf_placeno_dobio_vizu = 0; 
								if (in_array("dobio vizu", $candidate_case)){
									$queryCheck = $db->prepare("
										SELECT 
											kandidat_viza_vrijedi_od, kandidat_potvrden_pocetak_rada, kandidat_dogovoreni_pocetak_rada
										FROM 
											idk_kandidati 
										WHERE 
											kandidat_id = :kandidat_id 
											AND 
											kandidat_nalog_id  = :kandidat_nalog_id 
									");
									$queryCheck->execute(array(
										':kandidat_id' => $kandidat_id, 
										':kandidat_nalog_id' => $nalog_id
									));

									if ($queryCheck->rowCount() == 1) {
										$rowCheck = $queryCheck->fetch();
										if ($rowCheck["kandidat_viza_vrijedi_od"] != NULL){
											$datum_viza = date("Y-m-d", strtotime($rowCheck["kandidat_viza_vrijedi_od"]));
											$kf_placeno_dobio_vizu = 1;
										} else {
											/* Provjera pocetak rada ako nema datum vize START*/
											if (intval($rowCheck["kandidat_potvrden_pocetak_rada"]) == 1) { 
												if ( $rowCheck["kandidat_dogovoreni_pocetak_rada"] != NULL ) {
													$datum_viza = date("Y-m-d", strtotime($rowCheck["kandidat_dogovoreni_pocetak_rada"]));
													// datum_viza = datum_pocetak_rada
													$kf_placeno_dobio_vizu = 1;
												}else {
													$kf_placeno_dobio_vizu = 4;
													$datum_viza = date("Y-m-d");
												}
											} else {
												$kf_placeno_dobio_vizu = 5;
												$datum_viza = date("Y-m-d");
											}
											/* Provjera pocetak rada ako nema datum vize START*/
										}
									} else {
										$kf_placeno_dobio_vizu = 4;
										$datum_viza = date("Y-m-d");
									}
								} else {
									$kf_placeno_dobio_vizu = 0;
									$datum_viza = date("Y-m-d", $candidate_date[0]["visa_acquired"]);
								}
								/* Na osnovu statusa prijave - projeravaj koje vrijeme vize uzeti END  */

								$date = $datum_viza;
								$rata_id = $rata['nr_id'];
	
								$sql = "INSERT INTO 
											idk_kandidat_financije (nalog_id, kandidat_id, kf_datum, kf_datum_stvarni, kf_nalog_rata_id, kf_type, kf_iznos, kf_placeno)
										VALUES 
											(:nalog_id, :kandidat_id, :datum, :datum_stvarni, :kf_nalog_rata_id, 3, :iznos, :kf_placeno)";
	
								$query = $db->prepare($sql);
								$query->execute(array(
									':nalog_id' => $nalog_id,
									':kandidat_id' => $kandidat_id,
									':datum' => $date,
									':datum_stvarni' => $date,
									':kf_nalog_rata_id' => $rata_id,
									':iznos' => $iznos, 
									':kf_placeno' => $kf_placeno_dobio_vizu
								));
							break;
	
							case "pocetak rada":

								/* Na osnovu statusa prijave - projeravaj koje vrijeme početka rada uzeti START */
								$kf_placeno_pocetak_rada = 0; 
								if (in_array("pocetak rada", $candidate_case)){
									$queryCheck = $db->prepare("
										SELECT 
											kandidat_dogovoreni_pocetak_rada, kandidat_potvrden_pocetak_rada
										FROM 
											idk_kandidati 
										WHERE 
											kandidat_id = :kandidat_id 
											AND 
											kandidat_nalog_id  = :kandidat_nalog_id 
									");
									$queryCheck->execute(array(
										':kandidat_id' => $kandidat_id, 
										':kandidat_nalog_id' => $nalog_id
									));

									if ($queryCheck->rowCount() == 1) {
										$rowCheck = $queryCheck->fetch();
										if (intval($rowCheck["kandidat_potvrden_pocetak_rada"]) == 1) {
											if ( $rowCheck["kandidat_dogovoreni_pocetak_rada"] != NULL ) {
												$pocetak_rada = date("Y-m-d", strtotime($rowCheck["kandidat_dogovoreni_pocetak_rada"]));
												$kf_placeno_pocetak_rada = 1;
											}else {
												$kf_placeno_pocetak_rada = 4;
												$pocetak_rada = date("Y-m-d");
											}
										} else {
											$kf_placeno_pocetak_rada = 4;
											$pocetak_rada = date("Y-m-d");
										}
									} else {
										$kf_placeno_pocetak_rada = 4;
										$pocetak_rada = date("Y-m-d");
									}
								} else {
									$kf_placeno_pocetak_rada = 0;
									$pocetak_rada = date("Y-m-d", $candidate_date[0]["candidate_assessment"]);
								}
								/* Na osnovu statusa prijave - projeravaj koje vrijeme početka rada uzeti END  */

								$date = $pocetak_rada;
	
								$rata_id = $rata['nr_id'];
	
	
								$sql = "INSERT INTO 
											idk_kandidat_financije (nalog_id, kandidat_id, kf_datum, kf_datum_stvarni, kf_nalog_rata_id, kf_type, kf_iznos, kf_placeno)
										VALUES 
											(:nalog_id, :kandidat_id, :datum, :datum_stvarni, :kf_nalog_rata_id, 3, :iznos, :kf_placeno)";
	
								$query = $db->prepare($sql);
								$query->execute(array(
									':nalog_id' => $nalog_id,
									':kandidat_id' => $kandidat_id,
									':datum' => $date,
									':datum_stvarni' => $date,
									':kf_nalog_rata_id' => $rata_id,
									':iznos' => $iznos, 
									':kf_placeno' => $kf_placeno_pocetak_rada
								));
							break;
	
							case "mjeseci nakon":
								$broj_mjeseci = $rata['nr_mjeseci_nakon'];

								/* Na osnovu statusa prijave - projeravaj koje vrijeme početka rada uzeti START */
								$kf_placeno_pocetak_rada = 0; 
								if (in_array("pocetak rada", $candidate_case)){
									$queryCheck = $db->prepare("
										SELECT 
											kandidat_dogovoreni_pocetak_rada, kandidat_potvrden_pocetak_rada
										FROM 
											idk_kandidati 
										WHERE 
											kandidat_id = :kandidat_id 
											AND 
											kandidat_nalog_id  = :kandidat_nalog_id 
									");
									$queryCheck->execute(array(
										':kandidat_id' => $kandidat_id, 
										':kandidat_nalog_id' => $nalog_id
									));

									if ($queryCheck->rowCount() == 1) {
										$rowCheck = $queryCheck->fetch();
										if (intval($rowCheck["kandidat_potvrden_pocetak_rada"]) == 1) {
											if ( $rowCheck["kandidat_dogovoreni_pocetak_rada"] != NULL ) {
												$pocetak_rada_date = date("Y-m-d", strtotime($rowCheck["kandidat_dogovoreni_pocetak_rada"]));
												$pocetak_rada = date('Y-m-d', strtotime($pocetak_rada_date . " + $broj_mjeseci months"));
												$kf_placeno_pocetak_rada = 1;
											}else {
												$kf_placeno_pocetak_rada = 4;
												$pocetak_rada = date("Y-m-d");
											}
										} else {
											$kf_placeno_pocetak_rada = 4;
											$pocetak_rada = date("Y-m-d");
										}
									} else {
										$kf_placeno_pocetak_rada = 4;
										$pocetak_rada = date("Y-m-d");
									}
								} else {
									$pocetak_rada_date = date("Y-m-d", $candidate_date[0]["candidate_assessment"]);
									$pocetak_rada = date('Y-m-d', strtotime($pocetak_rada_date . " + $broj_mjeseci months"));
									$kf_placeno_pocetak_rada = 0;
								}
								/* Na osnovu statusa prijave - projeravaj koje vrijeme početka rada uzeti END  */
	
								$date = $pocetak_rada;
								$rata_id = $rata['nr_id'];
	
	
								$sql = "INSERT INTO 
											idk_kandidat_financije (nalog_id, kandidat_id, kf_datum, kf_datum_stvarni, kf_nalog_rata_id, kf_type, kf_iznos, kf_placeno)
										VALUES 
											(:nalog_id, :kandidat_id, :datum, :datum_stvarni, :kf_nalog_rata_id, 4, :iznos, :kf_placeno)";
	
								$query = $db->prepare($sql);
								$query->execute(array(
									':nalog_id' => $nalog_id,
									':kandidat_id' => $kandidat_id,
									':datum' => $date,
									':datum_stvarni' => $date,
									':kf_nalog_rata_id' => $rata_id,
									':iznos' => $iznos, 
									':kf_placeno' => $kf_placeno_pocetak_rada
								));
							break;
						}
					}
				}
			break;
		}
		//INSERT RATE ZA NOSTRIFIKACIJU JER ONA NE OVISI O NACINU PLACANJA PROVIZIJE
		insertNostrifikacijaRata($kandidat_id, $nalog_id);
	}

	function ispisStatusPrijaveR($vr) { 

		switch ( $vr ) {
			case 9:
				return "<strong>Potpisan ugovor<strong>";
			break;

			case 10:
				return "<strong>Početak rada<strong>";
			break;

			case 12:
				return "<strong>Skuplja dokumentaciju<strong>";
			break;

			case 15:
				return "<strong>Čeka termin<strong>";
			break;

			case 18:
				return "<strong>Čeka vizu<strong>";
			break;

			case 21:
				return "<strong>Dopuna dokumentacije<strong>";
			break;

			case 24:
				return "<strong>Odbijena viza<strong>";
			break;

			case 27:
				return "<strong>Dobio vizu<strong>";
			break;
			
			default: 
				return "<strong>Neodređen<strong>";
			break;
		}

	}

	function checkDatum($datum) {

		$datum = date("Y-m-d", strtotime($datum));
		$trenutni_datum = date("Y-m-d"); 

		if ( $datum != "") {

			if ( $datum <= $trenutni_datum) {

				return 1;

			} else {

				return 0;

			}

		} else {

			return 0;

		}

	}
?> 
<!DOCTYPE html>
<html>
	<head>
		<style>
			table {
			font-family: arial, sans-serif;
			border-collapse: collapse;
			width: 100%;
			}

			td, th {
			border: 1px solid #dddddd;
			text-align: left;
			padding: 8px;
			}
			/*
			tr:nth-child(even) {
			background-color: #dddddd;
			} 
			*/
		</style>
	</head>
	<body>
		<?php

			$time_start = microtime(true);
			
			$idNalog 		= intval($_GET["nalogId"]);
			$izvrsi_akcije 	= intval($_GET["izvrsi_akcije"]); 

			if ( $idNalog != 0 AND (in_array($izvrsi_akcije, array(1,0)))) {

				$queryCheckNalog = "
					SELECT 
						kompanija_id,
						nalog_naziv, 
						nalog_financije, 
						nalog_potrebno_kandidata, 
						nalog_provizija, 
						nalog_provizija_po_plati, 
						nalog_broj_rata, 
						nalog_procenat_avansa, 
						nalog_placa_nostrifikaciju, 
						nalog_nacin_nostrifikacije, 
						nalog_provizija_nostrifikacija,
						nalog_broj_rata_nostrifikacija
					FROM 
						idk_nalozi
					WHERE 
						nalog_id = :nalog_id 
				";

				$resultQueryCheckNalog = $db->prepare($queryCheckNalog);
				$resultQueryCheckNalog->execute(array(
					':nalog_id' => $idNalog
				));
				
				if( $resultQueryCheckNalog->rowCount() == 1 ){

					/*
						Ovdje imamo informacije o postavkama naloga START 
						*/
					
							$resultRowCheckNalog = $resultQueryCheckNalog->fetch(); 

							$kompanija_naziv 				 			= getCompanyNameByNalogId($idNalog);
							$nalog_naziv 				 				= $resultRowCheckNalog["nalog_naziv"];
							$nalog_financije 							= intval($resultRowCheckNalog["nalog_financije"]);
							$nalog_potrebno_kandidata 	 				= intval($resultRowCheckNalog["nalog_potrebno_kandidata"]);
							$nalog_provizija 							= $resultRowCheckNalog["nalog_provizija"];
							$nalog_provizija_po_plati 					= $resultRowCheckNalog["nalog_provizija_po_plati"];
							$nalog_broj_rata 							= intval($resultRowCheckNalog["nalog_broj_rata"]);
							$nalog_procenat_avansa 						= $resultRowCheckNalog["nalog_procenat_avansa"];
							$nalog_placa_nostrifikaciju 				= $resultRowCheckNalog["nalog_placa_nostrifikaciju"];
							$nalog_nacin_nostrifikacije 				= intval($resultRowCheckNalog["nalog_nacin_nostrifikacije"]);
							$nalog_provizija_nostrifikacija 			= $resultRowCheckNalog["nalog_provizija_nostrifikacija"];
							$nalog_broj_rata_nostrifikacija 			= intval($resultRowCheckNalog["nalog_broj_rata_nostrifikacija"]);

							echo "
								<br>
								<h1 style='color:red; margin: 0;'>Postavke naloga</h1>
								<br>
							";

							echo '
								<table>
									<tr>
										<th>Stavka</td>
										<th>Vrijednost</td>
									</tr>
									<tr>
										<td><strong>Kompanija:</strong></td>
										<td>'.$kompanija_naziv.'</td>
									</tr>
									<tr>
										<td><strong>Nalog:</strong></td>
										<td>'.$nalog_naziv.'</td>
									</tr>
									<tr>
										<td><strong>Način plaćanja:</strong></td>
										<td>'.( ( $nalog_financije == 1 ) ? "Standarni" : ( ( $nalog_financije == 2 ) ? "Mjesecno" : "Po plati" ) ).'</td>
									</tr>
									<tr>
										<td><strong>Nalog potrebno kaniddata:</strong></td>
										<td>'.$nalog_potrebno_kandidata.'</td>
									</tr>
									<tr>
										<td><strong>Provizija:</strong></td>
										<td>'.( ( $nalog_financije == 3 ) ? $nalog_provizija_po_plati . "x plata kandidata" : $nalog_provizija."€" ).'</td>
									</tr>
									<tr>
										<td><strong>Broj rata:</strong></td>
										<td>'.$nalog_broj_rata.'</td>
									</tr>
									<tr>
										<td><strong>Procenat avansa:</strong></td>
										<td>'.( ( $nalog_procenat_avansa != null ) ? $nalog_procenat_avansa . "%" : "Nema avansa" ).'</td>
									</tr>
									<tr>
										<td><strong>Nostrifikacija:</strong></td>
										<td>'.( ( $nalog_placa_nostrifikaciju == 1 ) ? "DA" : "NE" ).'</td>
									</tr>
							';
							if ( $nalog_placa_nostrifikaciju == 1 ) {
								
								echo '
									<tr>
										<td><strong>Nostrifikacija način:</strong></td>
										<td>'.( ( $nalog_nacin_nostrifikacije == 1 ) ? "Standardni" : "Mjesecno" ).'</td>
									</tr>
									<tr>
										<td><strong>Nostrifikacija provizija:</strong></td>
										<td>'.$nalog_provizija_nostrifikacija.'</td>
									</tr>
								';

								if ( $nalog_nacin_nostrifikacije == 2 ) {

									echo '
										<tr>
											<td><strong>Nostrifikacija broj rata:</strong></td>
											<td>'.$nalog_broj_rata_nostrifikacija.'</td>
										</tr>
									';

								}

							}



							echo '
								</table>
							';

							echo "
								<br>
								<hr>
							";

						/*
						Ovdje imamo informacije o postavkama naloga END  
					*/

					/* 
						Dio koda dalje izvršava se u koliko je način financija standardni i po plati
						*/

						if ( $nalog_financije == 1 OR $nalog_financije == 2 OR $nalog_financije == 3 ) {

							echo "
								<br>
								<h1 style='color:red; margin: 0;'>Kandidati</h1>
								<br>
							";
								/* 
									Dio koda koji vrši pretragu kandidata za taj nalog START 
									*/ 

										$queryCandidate = $db->prepare("
											SELECT 
												kan.kandidat_id, 
												concat(kan.kandidat_ime,' ', kan.kandidat_prezime) AS ime_prezime,
												kan.kandidat_mobitel, 
												kan.kandidat_status_prijave
											FROM 
												idk_kandidati kan 
											WHERE 
												kan.kandidat_nalog_id = :kandidat_nalog_id 
												AND 
												kan.kandidat_status_prijave IN (9,10,12,15,18,21,24,27)
											ORDER BY kan.kandidat_id ASC
										");

										$queryCandidate->execute(array(
											':kandidat_nalog_id' => $idNalog
										));

										if ( $queryCandidate->rowCount() != 0 ) {

											$cnt = 0; 
											$sum = 0;
											$exp_kandidat_financije_ids = array();
											$imp_kandidat_financije_ids = "";

											echo '
												<table>
													<tr>
														<th>RB</th>
														<th>ID</th>
														<th>Ime i prezime</th>
														<th>Status prijave</th>
														<th>KF ID</th>
														<th>TIP</th>
														<th>Datum aktivacije</th>
														<th>Iznos</th>
														<th>Status</th>
														<th>Prebačeno</th>
													</tr>
											';
											
											while ( $rowCandidate = $queryCandidate->fetch() ) {

												$cnt = $cnt + 1;
												
												$kandidat_id						= intval($rowCandidate["kandidat_id"]); 
												$kandidat_ime_prezime				= $rowCandidate["ime_prezime"];
												$kandidat_mobitel					= $rowCandidate["kandidat_mobitel"];
												$kandidat_status_prijave			= intval($rowCandidate["kandidat_status_prijave"]);
												$kandidat_status_prijave_ispis 		= ispisStatusPrijaveR($kandidat_status_prijave);

												/*
													Ovdje sad ide poziv funkcije generisiRateZaKandidata START
													*/
														if ( $izvrsi_akcije == 1 ) {
														
															generisiRateZaKandidataCOPY($kandidat_id, $kandidat_status_prijave, $idNalog, $durationPerStatus);
														
														}
													/*
													Ovdje sad ide poziv funkcije generisiRateZaKandidata END
												*/

												/* 
													Ovdje sad uzimamo financije kandidata START
													*/

														$queryFinancije = $db->prepare("
															SELECT 
																kf_id, 
																kf_datum_stvarni, 
																kf_nalog_rata_id, 
																kf_type, 
																kf_iznos, 
																kf_placeno, 
																kf_datum_fakturisanja, 
																kf_datum_placanja
															FROM 
																idk_kandidat_financije
															WHERE 
																kandidat_id = :kandidat_id
																AND 
																nalog_id = :nalog_id
															ORDER BY kf_id ASC
														"); 

														$queryFinancije->execute(array(
															':kandidat_id' => $kandidat_id, 
															':nalog_id' => $idNalog
														));

														$rowCountFinancije = $queryFinancije->rowCount();
														
														if ( $rowCountFinancije != 0 ) {
															
															$cntKf = 0; 

															while ( $rowFinancije = $queryFinancije->fetch() ) {

																$cntKf = $cntKf + 1;

																$kf_id 				= intval($rowFinancije["kf_id"]); 
																$kf_datum_stvarni 	= date("d-m-Y", strtotime($rowFinancije["kf_datum_stvarni"]));
																$kf_type			= intval($rowFinancije["kf_type"]);
																$kf_type_ispis 		= "<strong>".( ( $rowFinancije["kf_type"] == 0 ) ? "Nostrifikacija" : ( ( $rowFinancije["kf_type"] == 1 ) ? "Ugovor" : ( ( $rowFinancije["kf_type"] == 2 ) ? "Dobio vizu" : ( ( $rowFinancije["kf_type"] == 3 ) ? "Pocetak rada" : "Mjeseci nakon" ) ) ) )."</strong>";
																$kf_iznos			= $rowFinancije["kf_iznos"];
																$kf_placeno 		= intval( $rowFinancije["kf_placeno"] );
																$kf_placeno_ispis	= "<strong>". ( ( $rowFinancije["kf_placeno"] == 0 ) ? "NDF" : ( ( $rowFinancije["kf_placeno"]  == 1 ) ? "Treba fakturisati" : ( ( $rowFinancije["kf_placeno"]  == 2 ) ? "Fakturisano" : ( ( $rowFinancije["kf_placeno"]  == 4 ) ? "<span style='color:red;'>Problem<span>" : (($rowFinancije["kf_placeno"] == 5 ) ? "<span style='color:orange;'>Problem 2<span>" : "Plaćeno") ) ) ) ) ."</strong>";
																

																if ( $kf_placeno == 1 AND $izvrsi_akcije == 1 ) {

																	array_push($exp_kandidat_financije_ids, $kf_id);

																}

																if ( $cntKf == 1 ) {

																	echo '
																		<tr>
																			<td rowspan = "'.$rowCountFinancije.'" >'.$cnt.'</td>
																			<td rowspan = "'.$rowCountFinancije.'">'.$kandidat_id.'</td>
																			<td rowspan = "'.$rowCountFinancije.'">'.$kandidat_ime_prezime.'</td>
																			<td rowspan = "'.$rowCountFinancije.'">'.$kandidat_status_prijave_ispis.'</td>
																			<td>'.$kf_id.'</td>
																			<td>'.$kf_type_ispis.'</td>
																			<td>'.$kf_datum_stvarni.'</td>
																			<td>'.$kf_iznos.'</td>
																			<td>'.$kf_placeno_ispis.'</td>
																			<td>'.( ($kf_placeno == 4) ? '<strong style="color: red;">Ima Problem</strong>' : ( ( $kf_placeno == 1 ) ? '<strong style="color: blue;">DA</strong>' : ( ( $kf_placeno == 5 ) ? '<strong style="color: orange;">Ima Problem 2</strong>' : '<strong style="color: yellow;">NE</strong>' ))).'</td>
																		</tr>
																	';

																} else {
																	echo '
																		<tr>
																			<td>'.$kf_id.'</td>
																			<td>'.$kf_type_ispis.'</td>
																			<td>'.$kf_datum_stvarni.'</td>
																			<td>'.$kf_iznos.'</td>
																			<td>'.$kf_placeno_ispis.'</td>
																			<td>'.( ($kf_placeno == 4) ? '<strong style="color: red;">Ima Problem</strong>' : ( ( $kf_placeno == 1 ) ? '<strong style="color: blue;">DA</strong>' : ( ( $kf_placeno == 5 ) ? '<strong style="color: orange;">Ima Problem 2</strong>' : '<strong style="color: yellow;">NE</strong>' ))).'</td>
																		</tr>
																	';
																}

															}

														} else {

															echo '
																<tr>
																	<td>'.$cnt.'</td>
																	<td>'.$kandidat_id.'</td>
																	<td>'.$kandidat_ime_prezime.'</td>
																	<td>'.$kandidat_status_prijave_ispis.'</td>
																	<td>Nema</td>
																	<td>Nema</td>
																	<td>Nema</td>
																	<td>Nema</td>
																	<td>Nema</td>
																	<td>Nema</td>
																</tr>
															';

														}

													/*
													Ovdje sad uzimamo financije kandidata END
												*/

											}

											echo '
												<tr>
													<td colspan="2"><strong>Broj kandidata</strong></td>
													<td colspan="8"><strong>'.$cnt.'</strong></td>
												</tr>
											';
											
											/* 
												Ovaj dio vrši update onih redova koji po uslovu trebaju preći na status treba fakturisati
												*/
													if ( count($exp_kandidat_financije_ids) > 0 ) {
														
														$imp_kandidat_financije_ids = implode(", ", $exp_kandidat_financije_ids);

														if ( $imp_kandidat_financije_ids != "" ) {

															$log_desc = "KANDIDAT FINANCIJE - SKRIPTA ZA UPDATE DOSPJELIH FAKTURA - Izvršen update faktura na status 'Treba fakturisati' za IDs = [".$imp_kandidat_financije_ids."]. Nalog ID = [".$idNalog."]";
															addToLogs($log_desc, 4);

														}

													}
												/* 
												Ovaj dio vrši update onih redova koji po uslovu trebaju preći na status treba fakturisati
											*/

											echo '
												<tr>
													<td colspan="2"><strong>Update count</strong></td>
													<td colspan="8">'.( ( count($exp_kandidat_financije_ids) != 0 ) ? "<strong style='color: green;'>".count($exp_kandidat_financije_ids)."</strong>" : "<strong style='color: red;'>Nije izvršen update ni jedne fakture</strong>" ).'</td>
												</tr>
												<tr>
													<td colspan="2"><strong>Update IDs</strong></td>
													<td colspan="8">'.( ( $imp_kandidat_financije_ids != "" ) ? "<strong style='color: green;'>".$imp_kandidat_financije_ids."</strong>" : "<strong style='color: red;'>Nije izvršen update ni jedne fakture</strong>" ).'</td>
												</tr>
											';

											echo '
												</table>
											';

										} else {

											echo "<br><br> <strong>MESSEGE: </strong> Query za pretragu kandidata nije pronašao ni jednog kandidata!";

										}

									/* 
									Dio koda koji vrši pretragu kandidata za taj nalog START 
								*/ 
							echo "
								<br>
								<hr>
							";

						} else {

							echo "<br><br> <strong>MESSEGE: </strong> Ostatak koda nije moguće izvršiti jer je način plaćanja postavljen na mjesečni način!"; 

						}

				}else{

					echo "<br><br> <strong>MESSEGE: </strong> Query za provjeru informacija o nalog! "; 

				}

				
			} else {

				echo "<br><br> <strong>ERROR: </strong> Neispravno postavljen ID naloga ili parametar za izvršavanje akcija! "; 

			}

			$time_end = microtime(true);
			$time = $time_end - $time_start;

			echo "<br><br><strong>Vrijeme skripte: </strong> ".$time;
			echo "<br><strong>Vrijeme pokretanja skripte: </strong>". date("d.m.Y H:i:s"); 
			echo "<br><strong>Zaposlenik: </strong>". getEmployeeFullnameById($logged_employee_id);
		?>
	</body>
</html>