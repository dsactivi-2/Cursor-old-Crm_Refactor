<?php
	//1212
	include("includes/functions.php");
	require 'mail/PHPMailerAutoload.php';
	
	function getStatusDIPLKandidatTextR($status, $podstatus){
		$statusPrikaz = "";
		if($status == 1 AND $podstatus == 1){
			$statusPrikaz = 'Lead';
		}else if($status == 1 AND $podstatus == 6){
			$statusPrikaz = 'Neuspješan kontakt 1';
		}else if($status == 1 AND $podstatus == 2){
			$statusPrikaz = 'Neuspješan kontakt 3';
		}else if($status == 1 AND $podstatus == 3){
			$statusPrikaz = 'Zainteresiran Lead';
		}else if($status == 1 AND $podstatus == 4){
			$statusPrikaz = 'Nezainteresiran Lead';
		}else if($status == 1 AND $podstatus == 5){
			$statusPrikaz = 'U obradi Lead';
		}else if($status == 1 AND $podstatus == 7){
			$statusPrikaz = 'Neuspješan Lead 1';
		}else if($status == 1 AND $podstatus == 8){
			$statusPrikaz = 'Neuspješan Lead 2';
		}else if($status == 1 AND $podstatus == 9){
			$statusPrikaz = 'Termin Zainteresiran';
		}else if($status == 1 AND $podstatus == 10){
			$statusPrikaz = 'Termin Ostali';
		}else if($status == 1 AND $podstatus == 11){
			$statusPrikaz = 'Lead NL';
		}else if($status == 1 AND $podstatus == 12){
			$statusPrikaz = 'Lead NZ';
		}else if($status == 2){
			$statusPrikaz = 'Prikupljanje dokumentacije';
		}else if($status == 3){
			$statusPrikaz = 'Poslana pošta';
		}else if($status == 4){
			$statusPrikaz = 'U obradi';
		}else if($status == 5){
			$statusPrikaz = 'Dopuna dokumentacije';
		}else if($status == 6){
			$statusPrikaz = 'Završen';
		}else if($status == 7){
			$statusPrikaz = 'Arhiv';
		}else{
			$statusPrikaz = 'Nije definisano';
		}
		
		return $statusPrikaz;
	}
	$trenutnoVrijeme = date("Y-m-d H:i:s");
	
	$queryPostavke = $db->prepare("
		SELECT 
			id_s, employees_s, control_employees_s, candidate_status_s, status_days_s, communication_days_s
		FROM 
			idk_nd_cron_settings 
		WHERE 
			type_s = 1
			AND 
			status_s = 1
			AND 
			employees_s is not null 
			AND 
			control_employees_s is not null
			AND 
			end_day_s is null
	");
	$queryPostavke->execute();
	// echo $queryPostavke->rowCount();
	// exit();
	if($queryPostavke->rowCount() != 0){
		while($rowPostavke = $queryPostavke->fetch()){
			$id_s = intval($rowPostavke["id_s"]);
			$employees_s = explode(",",$rowPostavke["employees_s"]);
			$sent_to_employees_s = $rowPostavke["employees_s"];
			$control_employees_s = explode(",",$rowPostavke["control_employees_s"]);
			$sent_to_control_employees_s = $rowPostavke["control_employees_s"];
			$candidate_status_s = intval($rowPostavke["candidate_status_s"]);
			$status_days_s = intval($rowPostavke["status_days_s"]);
			$communication_days_s = intval($rowPostavke["communication_days_s"]);
			
			$statusUslov = "";
			$komunikacijaUslov = "";
			
			if($status_days_s != 0){
				$statusUslov = "DATEDIFF('".$trenutnoVrijeme."', slog.vrijeme_promjene_statusa_nd_kandidata) >= ".$status_days_s."";
			}else{
				$statusUslov = "slog.vrijeme_promjene_statusa_nd_kandidata is not null";
			}
			if($communication_days_s != 0){
				$komunikacijaUslov = "DATEDIFF('".$trenutnoVrijeme."', bilj.vrijeme_dodavanja_biljeska_nd) >= ".$communication_days_s."";
			}else{
				$komunikacijaUslov = "bilj.vrijeme_dodavanja_biljeska_nd is not null";
			}
			$slucajPostavke = 0; // Ne salje se nikako kontrolingu
			if($trenutnoVrijeme > "2021-12-15 23:59:59"){
				//If sluzi da se narednu sedmicu ne salje nikako kontrolingu dok se malo kandidati ne pročiste - jer ih je puno
				if($status_days_s == 0 AND $communication_days_s != 0){
					$slucajPostavke = 2; // Salje se kontrolingu dodavajuci na definisani broj dana komunikacije + 5 dana
				}else if($status_days_s != 0 AND $communication_days_s == 0){
					$slucajPostavke = 3; // Salje se kontrolingu dodavajuci na definisani broj dana statusa + 5 dana
				}else if($status_days_s != 0 AND $communication_days_s != 0){
					$slucajPostavke = 4; // Salje se kontrolingu dodavajuci na definisani broj dana statusa i komunikacije + 5 dana
				}else{
					$slucajPostavke = 1; // Ne salje se nikako kontrolingu
				}
			}
			$queryKandidati = $db->prepare("
				SELECT
					kan.id_broj_nd_kandidata AS idKandidata, 
					kan.ime_nd_kandidata AS imeKandidata, 
					kan.prezime_nd_kandidata AS prezimeKandidata, 
					kan.mobilni_nd_kandidata AS brojTelefonaKandidata,
					kan.skola_nd_kandidata AS skolaKandidata,
					kan.skola_smjer_nd_kandidata AS smjerSkoleKandidata,
					kan.status_nd_kandidata AS statusKandidata,
					kan.pstatus_nd_kandidata AS pstatusKandidata,
					slog.vrijeme_promjene_statusa_nd_kandidata AS vrijemeUlaskaUStatus,
					DATEDIFF('".$trenutnoVrijeme."', slog.vrijeme_promjene_statusa_nd_kandidata) AS brojDanaNaStatusu,
					bilj.vrijeme_dodavanja_biljeska_nd AS vrijemeZadnjeBiljeske,
					DATEDIFF('".$trenutnoVrijeme."', bilj.vrijeme_dodavanja_biljeska_nd) AS brojDanaOdZadnjeBiljeske,
					bilj.vrijeme_ponovnog_zvanja AS vrijemePonovnogZvanja
				FROM 
					idk_nd_kandidata kan
				INNER JOIN
					idk_nd_kandidata_status_log slog
				ON 
					slog.idd_broj_nd_kandidata = kan.id_broj_nd_kandidata 
					AND 
						slog.id_log_status_nd_kandidata IN (
							SELECT 
								MAX(maxlog.id_log_status_nd_kandidata)
							FROM 
								idk_nd_kandidata_status_log maxlog
							WHERE 
								maxlog.broj_dana_statusa_nd_kandidata is null
							GROUP BY
							maxlog.idd_broj_nd_kandidata
						)
				INNER JOIN 
					idk_nd_kandidata_biljeske bilj
				ON 
					kan.id_broj_nd_kandidata = bilj.id_kandidata_biljeska_nd
				AND 
					bilj.id_biljeska_nd IN (
						SELECT 
							MAX(maxbilj.id_biljeska_nd)
						FROM 
							idk_nd_kandidata_biljeske maxbilj
						WHERE 
							maxbilj.status_biljeska_nd = 4 
						GROUP BY
							maxbilj.id_kandidata_biljeska_nd
					)
				WHERE 
					kan.status_nd_kandidata = ".$candidate_status_s."
				AND 
					".$statusUslov."
				AND 
					".$komunikacijaUslov."
			");
			
			$queryKandidati->execute();
			$brojacKandidataZap = 0;
			$brojacKandidataKont = 0;
			$sumExportZap = "";
			$glavniExportZap = "";
			$sumExportKont = "";
			$glavniExportKont = "";
			if($queryKandidati->rowCount() != 0){
				while($rowKandidati = $queryKandidati->fetch()){
					$brojacKandidataZap++;
					$idKandidata = $rowKandidati["idKandidata"];
					$imeKandidata = $rowKandidati["imeKandidata"];
					$prezimeKandidata = $rowKandidati["prezimeKandidata"];
					$kandidatIspisHref = '<a target="_blank" href="'.getSiteUrlr().'nostrifikacija_diploma?page=otvori_ND_kandidata&id='.$idKandidata.'">'.$imeKandidata.' '.$prezimeKandidata.'</a>';
					$brojTelefonaKandidata = $rowKandidati["brojTelefonaKandidata"];
					$skolaKandidata = getSkolaNDKanidataR($rowKandidati["skolaKandidata"]);
					$smjerSkoleKandidata = getSkolaSmjerNDKanidataR($rowKandidati["smjerSkoleKandidata"]);
					$statusKandidata = $rowKandidati["statusKandidata"];
					$pstatusKandidata = $rowKandidati["pstatusKandidata"];
					$vrijemeUlaskaUStatus = date("Y-m-d H:i",strtotime($rowKandidati["vrijemeUlaskaUStatus"]));
					$brojDanaNaStatusu = $rowKandidati["brojDanaNaStatusu"];
					$vrijemeZadnjeBiljeske = date("Y-m-d H:i",strtotime($rowKandidati["vrijemeZadnjeBiljeske"]));
					$brojDanaOdZadnjeBiljeske = $rowKandidati["brojDanaOdZadnjeBiljeske"];
					$vrijemePonovnogZvanja = $rowKandidati["vrijemePonovnogZvanja"];
					
					if($vrijemePonovnogZvanja != NULL){
						continue;
					}
					
					if(strpos(substr($brojTelefonaKandidata, 0, 4), "381")){
						$drzavaKandidata = '<img src="'.getSiteUrlr().'images/sr3d.png" width=20>';
					}else if(strpos(substr($brojTelefonaKandidata, 0, 4), "387")){
						$drzavaKandidata = '<img src="'.getSiteUrlr().'images/bs3d.png" width=20>';
					}else if(strpos(substr($brojTelefonaKandidata, 0, 3), "49")){
						$drzavaKandidata = '<img src="'.getSiteUrlr().'images/de3d.png" width=20>';
					}else{
						$drzavaKandidata = '<img src="'.getSiteUrlr().'images/globe3d.png" width=20>';
					}
					
					$sumExportZap = $sumExportZap."
						<tr>
							<td>".$brojacKandidataZap."</td>
							<td>".$idKandidata."</td>
							<td>".$kandidatIspisHref."</td>
							<td>".$drzavaKandidata."</td>
							<td>".$brojTelefonaKandidata."</td>
							<td>".getStatusDIPLKandidatTextR($statusKandidata, $pstatusKandidata)."</td>
							<td>".$skolaKandidata."</td>
							<td>".$smjerSkoleKandidata."</td>
							<td>".$vrijemeUlaskaUStatus."</td>
							<td>".$brojDanaNaStatusu."</td>
							<td>".$vrijemeZadnjeBiljeske."</td>
							<td>".$brojDanaOdZadnjeBiljeske."</td>
						</tr>
					";
					$ubaciKandidata = 0;
					if($slucajPostavke != 0 AND $slucajPostavke != 1 AND ($slucajPostavke == 2 OR $slucajPostavke == 3 OR $slucajPostavke == 4)){
						if($slucajPostavke == 2){
							if($brojDanaOdZadnjeBiljeske >= ($communication_days_s + 5)){
								$ubaciKandidata = 1;
							}
						}else if($slucajPostavke == 3){
							if($brojDanaNaStatusu >= ($status_days_s + 5)){
								$ubaciKandidata = 1;
							}
						}else{
							if(($brojDanaNaStatusu >= ($status_days_s + 5)) AND ($brojDanaOdZadnjeBiljeske >= ($communication_days_s + 5))){
								$ubaciKandidata = 1;
							}
						}
					}
					
					if($ubaciKandidata == 1){
						$brojacKandidataKont++;
						$sumExportKont = $sumExportKont."
							<tr>
								<td>".$brojacKandidataKont."</td>
								<td>".$idKandidata."</td>
								<td>".$kandidatIspisHref."</td>
								<td>".$drzavaKandidata."</td>
								<td>".$brojTelefonaKandidata."</td>
								<td>".getStatusDIPLKandidatTextR($statusKandidata, $pstatusKandidata)."</td>
								<td>".$skolaKandidata."</td>
								<td>".$smjerSkoleKandidata."</td>
								<td>".$vrijemeUlaskaUStatus."</td>
								<td>".$brojDanaNaStatusu."</td>
								<td>".$vrijemeZadnjeBiljeske."</td>
								<td>".$brojDanaOdZadnjeBiljeske."</td>
							</tr>
						";
					}
				}
				
				if($brojacKandidataZap != 0){
					$glavniExportZap = '
						<table id="customers" style = "text-align: center;">
							<tr>
								<th colspan = "12">Kandidati pod statusom "'.getStatusDIPLKandidatTextR($candidate_status_s, 0).'"</th>
							</tr>
							<tr>
								<th>Broj</th>
								<th>ID</th>
								<th>Ime Prezime</th>
								<th>Država</th>
								<th>Mobitel</th>
								<th>Status</th>
								<th>Škola</th>
								<th>Smjer</th>
								<th>Ušao u status</th>
								<th>Dana na statusu</th>
								<th>Zadnja bilješka</th>
								<th>Dana od zadnje bilješke</th>
							</tr>
							'.$sumExportZap.'
						</table>
					';
					$queryInsertZap = $db->prepare("
						INSERT INTO idk_nd_cron_export
							(type_c, result_c, datetime_c, date_c, sending_type_c, sent_to_employees_c, settings_id)
						VALUES
							(:type_c, :result_c, :datetime_c, :date_c, :sending_type_c, :sent_to_employees_c, :settings_id)
					");
					$queryInsertZap->execute(array(
						':type_c' => 1,
						':result_c' => $glavniExportZap,
						':datetime_c' => date("Y-m-d H:i:s"),
						':date_c' => date("Y-m-d"),
						':sending_type_c' => 1,
						':sent_to_employees_c' => $sent_to_employees_s,
						':settings_id' => $id_s
					));
				}
				
				if($brojacKandidataKont != 0){
					$glavniExportKont = '
						<table id="customers" style = "text-align: center;">
							<tr>
								<th colspan = "12">Kandidati pod statusom "'.getStatusDIPLKandidatTextR($candidate_status_s, 0).'</th>
							</tr>
							<tr>
								<th>Broj</th>
								<th>ID</th>
								<th>Ime Prezime</th>
								<th>Država</th>
								<th>Mobitel</th>
								<th>Status</th>
								<th>Škola</th>
								<th>Smjer</th>
								<th>Ušao u status</th>
								<th>Dana na statusu</th>
								<th>Zadnja bilješka</th>
								<th>Dana od zadnje bilješke</th>
							</tr>
							'.$sumExportKont.'
						</table>
					';
					$queryInsertKont = $db->prepare("
						INSERT INTO idk_nd_cron_export
							(type_c, result_c, datetime_c, date_c, sending_type_c, sent_to_employees_c, settings_id)
						VALUES
							(:type_c, :result_c, :datetime_c, :date_c, :sending_type_c, :sent_to_employees_c, :settings_id)
					");
					$queryInsertKont->execute(array(
						':type_c' => 1,
						':result_c' => $glavniExportKont,
						':datetime_c' => date("Y-m-d H:i:s"),
						':date_c' => date("Y-m-d"),
						':sending_type_c' => 2,
						':sent_to_employees_c' => $sent_to_control_employees_s,
						':settings_id' => $id_s
					));
				}
			}
		}
	}
	
	$trenutniDan = date("Y-m-d");
	$tipoviSlanja = array(1,2);
	foreach($tipoviSlanja AS $tipoviValue){
		$putanja_signatura = $_SERVER['DOCUMENT_ROOT']."/files/ugovori_uplatnice_dipl/signatura_bih.jpg";
		$queryZaposlenicimaSlanje = $db->prepare("
			SELECT 
				ex.id_c, sit.candidate_status_s, sit.status_days_s, sit.communication_days_s, ex.sent_to_employees_c
			FROM 
				idk_nd_cron_export ex 
			INNER JOIN 
				idk_nd_cron_settings sit
			ON 
				ex.settings_id = sit.id_s
			WHERE 
				ex.date_c = '".$trenutniDan."'
				AND 
				ex.sending_type_c = ".$tipoviValue."
				AND 
				ex.type_c = 1
			ORDER BY 
				sit.candidate_status_s
			ASC
		");
		$queryZaposlenicimaSlanje->execute();
		if($queryZaposlenicimaSlanje->rowCount() != 0){
			while($rowZaposlenicimaSlanje = $queryZaposlenicimaSlanje->fetch()){
				$idExporta = $rowZaposlenicimaSlanje["id_c"];
				$kandidatStatusExporta = $rowZaposlenicimaSlanje["candidate_status_s"];
				$danaStatusExporta = intval($rowZaposlenicimaSlanje["status_days_s"]);
				$danaKomunikacijaExporta = intval($rowZaposlenicimaSlanje["communication_days_s"]);
				$zaposleniciExporta = $rowZaposlenicimaSlanje["sent_to_employees_c"];
				$textSubject = "";
				if($tipoviValue == 1){
					$textSubject = "Reminder obrada DIPL";
				}else{
					$textSubject = "Reminder controling obrada DIPL";
				}
				$textMail = "";
				$subtextMail = "";
				if($danaStatusExporta == 0 AND $danaKomunikacijaExporta != 0){
					if($tipoviValue == 1){
						$subtextMail = "više od ".$danaKomunikacijaExporta." dan/a.";
					}else{
						$subtextMail = "više od ".$danaKomunikacijaExporta." dan/a. Kandidati se šalju već 5 dana redom zaposlenicima i nije se desila promjena.";
					}
					$textMail = "Kandidati koji imaju zadnju komunikaciju ".$subtextMail."";
				}else if($danaStatusExporta != 0 AND $danaKomunikacijaExporta == 0){
					if($tipoviValue == 1){
						$subtextMail = "više od ".$danaStatusExporta." dan/a.";
					}else{
						$subtextMail = "više od ".$danaStatusExporta." dan/a. Kandidati se šalju već 5 dana redom zaposlenicima i nije se desila promjena.";
					}
					$textMail = "Kandidati koji se nalaze na statusu ".$subtextMail."";
				}else if($danaStatusExporta != 0 AND $danaKomunikacijaExporta != 0){
					if($tipoviValue == 1){
						$subtextMail = "više od ".$danaStatusExporta." dan/a i čija je zadnja komunikacija više od od ".$danaKomunikacijaExporta." dan/a.";
					}else{
						$subtextMail = "više od ".$danaStatusExporta." dan/a i čija je zadnja komunikacija više od od ".$danaKomunikacijaExporta." dan/a. Kandidati se šalju već 5 dana redom zaposlenicima i nije se desila promjena.";
					}
					$textMail = "Kandidati koji se nalaze na statusu ".$subtextMail."";
				}else{
					$textMail = "Kandidati koji se nalaze na statusu.";
				}
				
				if($zaposleniciExporta != NULL){
					$mail1 = new PHPMailer;
					$mail1->isSMTP();

					$mail1->Host = 'smtp.gmail.com';                 // Specify main and backup SMTP servers
					$mail1->SMTPAuth = true;                         // Enable SMTP authentication
					$mail1->Username = 'support@job-step.com';        // SMTP username
					$mail1->Password = 'eooc nnxo aylp lqkh';          // SMTP password
					$mail1->SMTPSecure = 'ssl';                      // Enable TLS encryption, `ssl` also accepted
					$mail1->Port = 465;                              // TCP port to connect to
					$mail1->CharSet = 'UTF-8';

					//Recipients
					$mail1->setFrom('support@job-step.com', 'JobStep');
					
					$zaposleniciExporta = explode(",",$zaposleniciExporta);
					$zaposleniciCNT = 0;
					foreach($zaposleniciExporta AS $zaposlenikExporta){
						$userQuery = $db->prepare("
							SELECT 
								employee_email
							FROM 
								idk_employees
							WHERE 
								employee_id = :employee_id
								AND 
								employee_status NOT LIKE '0'
						");
						$userQuery->execute(array(
							':employee_id' => $zaposlenikExporta
						));
						if ($userQuery->rowCount() != 0) {
							$userRow = $userQuery->fetch();
							$employeeEmail = $userRow['employee_email'];
							
							$mail1->addAddress($employeeEmail);
							$zaposleniciCNT++; 
						}
					}
					$linkMail = '<a href = "'.getSiteUrlr().'reminders_obrada_dipl?page=croneExport&id='.$idExporta.'">Link</a>';
					$mail1->Subject = "".$textSubject." - ".getStatusDIPLKandidatTextR($kandidatStatusExporta, 0)."";
					$mail1->Body = "
						<p>
							Poštovani,<br>
							Klikom na link, možete preuzeti export kandidata prema kriteriju: <br>
							'".$textMail."'<br>
							Export kreiran: ".date("d.m.Y")."
						</p>
						<p>
							Detalje pogledajte na linku: ".$linkMail."
						</p>
						<br><br><br>
						<img src='cid:logo_2u1' width='100%'>
					";
					$mail1->AddEmbeddedImage($putanja_signatura, 'logo_2u1');
					$mail1->AltBody = "ALT";
					if ($zaposleniciCNT > 0) {
						if(!$mail1->send()) {
							$log_desc = "DIPL Reminder obrada - Nije poslan mail zaposlenicima za export ID = [".$idExporta."]. Zaposlenici ID = [".implode(",", $zaposleniciExporta)."].";
							
						}else{
							$log_desc = "DIPL Reminder obrada - Poslan mail zaposlenicima za export ID = [".$idExporta."]. Zaposlenici ID = [".implode(",", $zaposleniciExporta)."].";
						}
					} else {
						$log_desc = "DIPL Reminder obrada - Nije poslan mail zaposlenicima za export ID = [".$idExporta."]. Code: Svi zaposlenici su deaktivirani!.";
					}
					$log_query = $db->prepare("
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

					$log_query->execute(array(
						':log_employeeid' => 139,
						':log_desc' => $log_desc,
						':log_date' => date("Y-m-d H:i:s")
					));
				}
			}
		}
	}
	
?>