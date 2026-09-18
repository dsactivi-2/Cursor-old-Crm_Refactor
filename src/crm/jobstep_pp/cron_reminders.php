<?php
	include("includes/connect.php");
  require_once($_SERVER["DOCUMENT_ROOT"] . "/includes/env.php");
	//PHPMailer
	/*
	require '../mail/Exception.php';
	require '../mail/PHPMailer.php';
	require '../mail/SMTP.php';
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;*/

	//$reminder_type 	= getenv("reminder_type");
  if(!isset($_GET['reminder_type'])){
    die("Reminder type is not set");
  }

	$reminder_type 	= $_GET['reminder_type'];

	$candidate_imploded = '';
	$date_sent			= date('Y-m-d H:i:s');

	if($reminder_type && ($reminder_type >= 1 && $reminder_type <= 19))
	{

		//Get reminder type info
		$reminder_type_info = $db->prepare("SELECT * FROM idk_pp_reminder_types WHERE prt_id = $reminder_type ");
		$reminder_type_info->execute();
		$row_rt = $reminder_type_info->fetch();
		$prt_name = $row_rt['prt_name'];
		$mail_subject = $row_rt['prt_mail_subject_de'];
		$mail_body = $row_rt['prt_mail_text_de'];
		$frequency = intval ($row_rt['prt_day_freq']);
		$prt_user_type = intval ($row_rt['prt_user_type']);

		$prt_has_documents = intval($row_rt['prt_has_documents']); //Varijabla koja označava da li tip remindera ima dokumente (1 - DA, 0 - NE)

		if($prt_has_documents == 0){
			// Get all reminder settings
			$reminder_settings_query = $db->prepare("
				SELECT
					*
				FROM
					idk_pp_reminder_settings
				WHERE
					prs_reminder_type_id = :reminder_type
					AND
					prs_active = 1
			");
			$reminder_settings_query->execute(
				[':reminder_type' => $reminder_type]
			);
		}else{
			$reminder_settings_query = $db->prepare("
				SELECT
					*
				FROM
					idk_pp_reminder_settings prs
				JOIN
					idk_pp_reminder_documents prd
				ON
					prs.prs_id = prd.prd_prs_id
				WHERE
					prs.prs_active = 1
					AND
					prd.prd_active = 1
					AND
					prs.prs_reminder_type_id = :reminder_type
			");
			$reminder_settings_query->execute(
				[':reminder_type' => $reminder_type]
			);
		}

		while ($reminder_settings = $reminder_settings_query->fetch()){
			$prs_id  = $reminder_settings['prs_id'];
			$prs_nalog_id = $reminder_settings['prs_nalog_id'];
			$prs_partner_id = $reminder_settings['prs_partner_id'];
			$prs_pua_ids = $reminder_settings['prs_pua_ids'];
			$prs_first_trigger_date = $reminder_settings['prs_first_trigger_date'];
			$prs_reminder_type_id = $reminder_settings['prs_reminder_type_id'];
			$doc_id = 0;
			if($prt_has_documents == 0){
				$prs_create_after = intval($reminder_settings['prs_create_after']);
				$prd_id = 0;
				$prd_prs_id = 0;
				$prd_nrd_id = 0;
				$prd_crd_id = 0;
				$prd_day_freq = 0;
				$prd_create_after = 0;

				$candidates = getAllCandidates($prs_nalog_id, $prs_partner_id, $reminder_type, $frequency,$prs_create_after);
			}else{
				$prd_id = $reminder_settings["prd_id"];
				$prd_prs_id = intval($reminder_settings["prd_prs_id"]);
				$prd_nrd_id = intval($reminder_settings["prd_nrd_id"]);
				$prd_crd_id = intval($reminder_settings["prd_crd_id"]);
				if($prd_nrd_id != 0 AND $prd_crd_id == 0){
					$doc_id = $prd_nrd_id;
				}else if($prd_nrd_id == 0 AND $prd_crd_id != 0){
					$doc_id = $prd_crd_id;
				}
				$prd_day_freq = intval($reminder_settings["prd_day_freq"]);
				$prd_create_after = intval($reminder_settings["prd_create_after"]);
				$candidates = getAllCandidates($prs_nalog_id, $prs_partner_id, $reminder_type, $prd_day_freq, $prd_create_after, $doc_id);
			}
			// ZA PRVO POKRETANJE :
			// $candidates = getAllCandidates($prs_nalog_id, $prs_partner_id, $reminder_type, 1);

			$nr_of_candidates = count($candidates);
			if($nr_of_candidates > 0){
				$candidate_imploded = implode(',', $candidates);
				$nalog_naziv = getNazivNalogaReminder($prs_nalog_id);
				if($prt_has_documents == 0){
					$update_old_r = $db->prepare("
						UPDATE idk_pp_reminders SET pr_status = 4
						WHERE pr_candidate_id IN (".$candidate_imploded.") AND pr_reminder_setting_id = :pr_reminder_setting_id
						AND pr_status IN (1,2)

					");
					$update_old_r->execute(array(
						"pr_reminder_setting_id" => $prs_id
					));

					$result_level = getLastLevelOfRemindersFromCandidatesArrayR($candidates, 0, $prs_id);
				}else{
					$update_old_r = $db->prepare("
						UPDATE idk_pp_reminders SET pr_status = 4
						WHERE pr_candidate_id IN (".$candidate_imploded.") AND pr_reminder_document_id = :pr_reminder_document_id
						AND pr_status IN (1,2)

					");
					$update_old_r->execute(array(
						"pr_reminder_document_id" => $prd_id
					));

					$result_level = getLastLevelOfRemindersFromCandidatesArrayR($candidates, 1, $prd_id);
				}
				$values_to_insert 	= array();
				$reminders_query_column = "";
				if($prt_has_documents == 0){
					$reminders_query_column = "pr_reminder_setting_id";
					foreach($candidates as $candidate_id){
						/*
							Level Reminder
							*/
								$reminder_level = 0;
								if (count($result_level["count"]) > 0){
									if (in_array($candidate_id, $result_level["candidateId"])){
										$candidate_position_level = array_search($candidate_id,$result_level["candidateId"]);
										if ($candidate_position_level !== false){
											if (($result_level["reminderStatus"][$candidate_position_level]) != 3){
												$reminder_level = $result_level["reminderLevel"][$candidate_position_level] + 1;
											} else {
												$reminder_level = 1;
											}
										} else {
											$reminder_level = 1;
										}
									} else {
										$reminder_level = 1; 
									}
								} else {
									$reminder_level = 1;
								}
							/*
							Level Reminder
						*/
						array_push($values_to_insert, "(".$prs_id.",".$candidate_id.",".$reminder_level.",'".$date_sent."','".$prs_pua_ids."',1 )");
					}
				}else{
					$reminders_query_column = "pr_reminder_document_id";
					foreach($candidates as $candidate_id){
						/*
							Level Reminder
							*/
								$reminder_level = 0;
								if (count($result_level["count"]) > 0){
									if (in_array($candidate_id, $result_level["candidateId"])){
										$candidate_position_level = array_search($candidate_id,$result_level["candidateId"]);
										if ($candidate_position_level !== false){
											if (($result_level["reminderStatus"][$candidate_position_level]) != 3){
												$reminder_level = $result_level["reminderLevel"][$candidate_position_level] + 1;
											} else {
												$reminder_level = 1;
											}
										} else {
											$reminder_level = 1;
										}
									} else {
										$reminder_level = 1; 
									}
								} else {
									$reminder_level = 1;
								}
							/*
							Level Reminder
						*/
						array_push($values_to_insert, "(".$prd_id.",".$candidate_id.",".$reminder_level.",'".$date_sent."','".$prs_pua_ids."',1 )");
					}
				}

				$values_to_insert = implode(', ',$values_to_insert);

				$reminders_query = $db->prepare("
					INSERT INTO idk_pp_reminders
						( ".$reminders_query_column.", pr_candidate_id, pr_level, pr_date_sent, pr_users_sent, pr_status)
					VALUES
						".$values_to_insert
				);

				$reminders_query->execute();

				//SLANJE MAILA
					//SPAM JE BELAJ:
						//CHECK MOD CURRENT DATE i triger MOD FREQ = 0
							// POKUPITI kandidate iz remindera
							//salji mail

					//nije belaj
						//imamo kandidate (oni koji su upravo unseseni)
						//salji mail

				$today = new DateTime();
				$trigger_date = new DateTime($prs_first_trigger_date);

				//AKO SPAM BUDE BELAJ OTKOMENTARISATI ISPOD:
				//if(shouldSendReminder($trigger_date, $today, $frequency)){

					//QUERY get candidates iz remindera sa statusom 1 i 2 !!! otkomentarisati ako spam bude belaj
					/*$get_cand_from_rem = $db->prepare("
							SELECT pr_candidate_id FROM idk_pp_reminders
							WHERE pr_reminder_setting_id = $prs_id AND pr_status IN (1,2)
					");
					$get_cand_from_rem->execute();
					$nr_of_candidates = $get_cand_from_rem->rowCount();
					*/

					// // // // // Otkomentarisati kada mailovi budu trebali ići !!! -----------START

					// $mail_body = str_replace("{{var1}}", $nr_of_candidates, $mail_body);
					// $mail_body = str_replace("{{var2}}", $nalog_naziv, $mail_body);

					// $pua_ids_arr = explode(",",$prs_pua_ids);

					// $hostName = "smtp.strato.de;smtp.strato.de";
					// $userName = "info@job-step.de";
					// $password = "info@5500!Maraka";
					// $setFrom = "info@job-step.de";

					// $mail = new PHPMailer;
					// //$mail->isSMTP();											// Set mailer to use SMTP
					// $mail->Host = $hostName;									// Specify main and backup SMTP servers
					// $mail->SMTPAuth = true;										// Enable SMTP authentication
					// $mail->Username = $userName;								// SMTP username
					// $mail->Password = $password;								// SMTP password
					// $mail->SMTPSecure = 'ssl';									// Enable TLS encryption, `ssl` also accepted
					// $mail->Port = 465;											// TCP port to connect to
					// $mail->CharSet = 'UTF-8';
					// $mail->setFrom($setFrom);    								// Add a recipient

					// if($prt_user_type != 3){
					// 	//Za admine i superadmine
					// 	$pu_emails = array();
					// 	foreach($pua_ids_arr as $pua_id){
					// 		$get_mail = $db->prepare("
					// 				SELECT pu_email FROM idk_pp_users
					// 				JOIN idk_pp_user_access ON pua_user_id = pu_id
					// 				WHERE pua_id = $pua_id"
					// 		);
					// 		$get_mail->execute();
					// 		$row_mail = $get_mail->fetch();
					// 		$pu_email = $row_mail['pu_email'];
					// 		// echo $pu_email."<br/>";
					// 		$mail->addAddress($pu_email); 						// Add a recipient
					// 		array_push($pu_emails, $pu_email);
					// 		$mail->Subject = $mail_subject;

					// 		$mail->Body = $mail_body;

					// 		if(!$mail->send()) {
					// 			//Greska
					// 			echo "greska";
					// 		}else{
					// 			"poslano";
					// 		}
					// 	}
					// }
					// // // // // Otkomentarisati kada mailovi budu trebali ići !!! -----------END


					// // // // // OSTAJE ZAKOMENTARISANO, PMOVIMA MAILOVI NE IDU --------------START
					// else{
					// 		//za PM-ove
					// 		$mail->addAddress($pm_mail);
					// 	}

					// if($prt_user_type != 3){
					// 	//OBAVIJEST PM-u DA JE OTISAO REMINDER userima
					// 	$mail_pm = new PHPMailer;
					// 	$mail_pm->Host = $hostname;
					// 	$mail_pm->SMTPAuth = true;
					// 	$mail_pm->Username = $userName;
					// 	$mail_pm->Password = $password;
					// 	$mail_pm->SMTPSecure = 'ssl';
					// 	$mail_pm->Port = 465;
					// 	$mail_pm->CharSet = 'UTF-8';
					// 	$mail_pm->setFrom($setFrom);
					// 	$mail_pm->addAddress($pm_mail);
					// 	$mail_pm->Subject = "REMINDER '".$prt_name."' za nalog ".$prs_nalog_id;
					// 	$mail_pm->Body = "Za nalog ".$nalog_naziv." poslan reminder '".$prt_name."' userima: ". implode(',', $pu_emails) ." sa ".$nr_of_candidates." kandidata.";
					// 	if(!$mail_pm->send()) {
					// 		//Greska
					// 		echo "greska za pm";

					// 	}else{
					// 		"poslano pm ";
					// 	}
					// }
					// // // // // OSTAJE ZAKOMENTARISANO, PMOVIMA MAILOVI NE IDU ---------------END
					if($prt_has_documents == 0){
						$log_desc = "Jobstep PP APP - Poslan reminder [Tip: ".$reminder_type.", Postavka: ".$prs_id.", Nalog: ".$prs_nalog_id."] zaposlenicima: ".$prs_pua_ids.".";
					}else{
						$log_desc = "Jobstep PP APP - Poslan reminder [Tip: ".$reminder_type.", Postavka dokument: ".$prd_id.", Nalog: ".$prs_nalog_id."] zaposlenicima: ".$prs_pua_ids.".";
					}

					$log_query = $db->prepare("
						INSERT INTO idk_logs
							(log_employeeid, log_desc, log_date, log_type)
						VALUES
							(:log_employeeid, :log_desc, :log_date, :log_type)"
					);

					$log_query->execute(array(
						':log_employeeid' => 0,
						':log_desc' => $log_desc,
						':log_date' => date('Y-m-d H:i:s'),
						':log_type' => 10
					));

					//AKO SPAM BUDE BELAJ OTKOMENTARISATI ZAGRADU ISPOD:
				//}
			}
		}

	}
	else{
		echo "ERROR:\t cron_reminders.php\t Invalid reminder_type: " . $reminder_type . "\n";
	}

	// function getNalogPMMail($nalogIdVr){
	// 	Global $db;
	// 	$query = $db->prepare("SELECT e.employee_email FROM idk_employees e JOIN idk_nalozi n ON e.employee_id = n.employee_id WHERE n.nalog_id = $nalogIdVr");
	// 	$query->execute();
	// 	$row = $query->fetch();
	// 	return $row['employee_email'];
	// }
	function getNazivNalogaReminder($nalogIdVr){
		Global $db;
		$nalogId = intval($nalogIdVr);
		if($nalogId != 0){
			$cntQuery = 0;
			$query = $db->prepare("
				SELECT
					nalog_naziv
				FROM
					idk_nalozi
				WHERE
					nalog_id = :nalog_id
			");
			$query->execute(array(
				':nalog_id' => $nalogId
			));
			$cntQuery = $query->rowCount();
			if($cntQuery != 0){
				$row = $query->fetch();
				if($nalogId == 213){
					$nalogNaziv = str_replace("25","", $row["nalog_naziv"]);
				}
				else if($nalogId == 211){
					$nalogNaziv = str_replace("55","", $row["nalog_naziv"]);
				}else{
					$nalogNaziv = $row["nalog_naziv"];
				}

				return $nalogNaziv;
			}else{
				return "Undefined";
			}

		}else{
			return "Undefined";
		}
	}
	/**
     * numberOfWorkingDays
     * Returns number of workdays between $start and $end. If $start > $end, returns -1.
     * @param  DateTime $start
     * @param  DateTime $end
     * @return int
     */
    function numberOfWorkingDays($start, $end) {
            // Clone objects to prevent modification of original objects
            $from = new DateTime($start->format("Y-m-d"));
            $to = new DateTIme($end->format("Y-m-d"));

            $workingDays = [1, 2, 3, 4, 5];

            $to->modify('+1 day');
            $interval = new DateInterval('P1D');
            $periods = new DatePeriod($from, $interval, $to);

            $days = 0;
            foreach ($periods as $period) {
                if (!in_array($period->format('N'), $workingDays)) continue;
                $days++;
            }
            return $days-1;
    }

    /**
     * shouldSendReminder
     * Returns true if the reminder needs to be sent today.  Otherwise, returns false.
     * @param  DateTime $start
     * @param  DateTime $end
     * @param  int $frequency
     * @return bool
     */
    function shouldSendReminder($start, $end, $frequency)
    {
        $numberOfDays = numberOfWorkingDays($start,$end);
        if($numberOfDays % $frequency == 0 && in_array($end->format("N"),[1,2,3,4,5]))
        {
            return true;
        }

        return false;
    }

	function getAllCandidates($prs_nalog_id, $prs_partner_id, $reminder_type, $frequency, $prs_create_after, $doc_id = 0){
		/*
			*********************************************************
			*		Postavke za remindere koji imaju dokument		*
			*						START							*
			*********************************************************
		*/
			//doc_id - nrd_id ili crd_id - dalje - na osnovu tipa remindera - setovat ce se određena vrijednost

			//Tipovi remindera za nrd_dokumente i crd_dokumente
			$tipoviNrd = array(10,11,12,13);
			$tipoviCrd = array(20,21,22,23);
			$prd_nrd_id = 0;
			$prd_crd_id = 0;

			if($doc_id != 0){
				if(in_array($reminder_type, $tipoviNrd)){
					$prd_nrd_id = $doc_id;
				}else if(in_array($reminder_type, $tipoviCrd)){
					$prd_crd_id = $doc_id;
				}else{
					unset($tipoviNrd);
					unset($tipoviCrd);
					return array(); //Jer funkcija dole vraća array kandidata. U ovom slučaju vrati prazan niz - a gore postoji provjera count(array()) != 0 - tako da će preskočiti ovaj reminder ako ima greska
				}
			}
			unset($tipoviNrd);
			unset($tipoviCrd);
		/*
			*********************************************************
			*		Postavke za remindere koji imaju dokument		*
			*						END								*
			*********************************************************
		*/
		Global $db;
		switch($reminder_type){
			case 1:
				$query_gc = $db->prepare("
					SELECT
						pra.pra_kandidat_id AS kandidat_id
					FROM
						idk_pp_ratings pra
					JOIN
						(
							SELECT
								pca1.pca_kandidat_id AS kandidat_id,
								pca1.pca_appointment_id AS appointment_id
							FROM
								idk_pp_cand_appts pca1
							JOIN
								(
									SELECT
										pap1.pap_id AS pap_id
									FROM
										idk_pp_appointments pap1
									JOIN
										idk_pp_appointment_groups pag1
									ON
										pap1.pap_nalog_id = :nalogId
										AND
										pap1.pap_group_id = pag1.ppaq_id
										AND
										MOD(getNumberOfWeekdays(pag1.ppaq_end_date, CURRENT_DATE),:freq)=0
										AND
										pag1.ppaq_end_date < CURRENT_DATE
								)
								AS
								pap
							ON
								pca1.pca_appointment_id = pap.pap_id
								AND
								pca1.pca_avg_rating is null
								AND
								pca1.pca_status = 1
							JOIN
								(
									SELECT
										kan1.kandidat_id AS kandidat_id
									FROM
										idk_kandidati kan1
									JOIN
										idk_project_kandidati pk1
									ON
										pk1.pk_kandidatid = kan1.kandidat_id
									JOIN
										(
											SELECT
												pro2.project_id AS project_id
											FROM
												idk_projects pro2
											WHERE
												pro2.project_name LIKE ('%Intervju%')
												AND
												pro2.project_nalogid = :nalogId
										)
										AS
										pro1
									ON
										pk1.pk_projectid = pro1.project_id
								)
								AS
								kan
							ON
								pca1.pca_kandidat_id = kan.kandidat_id
						)
						AS
						pca
					ON
						pra.pra_kandidat_id = pca.kandidat_id
					JOIN
						idk_pp_appointments_questions papq
					ON
						pca.appointment_id = papq.papq_appointment_id
						AND
						papq.papq_id  = pra.pra_appointment_question_id
					GROUP BY pra.pra_kandidat_id
				");

				$query_gc->execute(array(
					":nalogId" => $prs_nalog_id,
					":freq" => $frequency
				));
				$candidates = array();
				while($row_gc = $query_gc->fetch()){
					array_push($candidates, $row_gc['kandidat_id']);
				}
				return $candidates;
			break;
			case 2:
				$query_gc = $db->prepare("
					SELECT
						pca1.pca_kandidat_id AS kandidat_id
					FROM
						idk_pp_cand_appts pca1
					JOIN
						(
							SELECT
								pap1.pap_id AS pap_id
							FROM
								idk_pp_appointments pap1
							JOIN
								idk_pp_appointment_groups pag1
							ON
								pap1.pap_nalog_id = :nalogId
								AND
								pap1.pap_group_id = pag1.ppaq_id
								AND
								MOD(getNumberOfWeekdays(pag1.ppaq_end_date, CURRENT_DATE),:freq)=0
								AND
								pag1.ppaq_end_date < CURRENT_DATE
						)
						AS
						pap
					ON
						pca1.pca_appointment_id = pap.pap_id
						AND
						pca1.pca_avg_rating is not null
						AND
						pca1.pca_status = 1
					JOIN
						(
							SELECT
								kan1.kandidat_id AS kandidat_id
							FROM
								idk_kandidati kan1
							JOIN
								idk_project_kandidati pk1
							ON
								pk1.pk_kandidatid = kan1.kandidat_id
							JOIN
								(
									SELECT
										pro2.project_id AS project_id
									FROM
										idk_projects pro2
									WHERE
										pro2.project_name LIKE ('%Intervju%')
										AND
										pro2.project_nalogid = :nalogId
								)
								AS
								pro1
							ON
								pk1.pk_projectid = pro1.project_id
						)
						AS
						kan
					ON
						pca1.pca_kandidat_id = kan.kandidat_id
				");

				$query_gc->execute(array(
					":nalogId" => $prs_nalog_id,
					":freq" => $frequency
				));
				$candidates = array();
				while($row_gc = $query_gc->fetch()){
					array_push($candidates, $row_gc['kandidat_id']);
				}
				return $candidates;
			break;
			case 3:
				$query_gc = $db->prepare("
					SELECT
						kan.candId AS kandidat_id
					FROM
						(
							SELECT
								kand.kandidat_id AS candId
							FROM
								idk_kandidati kand
							JOIN
								idk_project_kandidati pkand
							ON
								kand.kandidat_ppa_partner_id is null
								AND
								kand.kandidat_status_prijave = 7
								AND
								kand.kandidat_id = pkand.pk_kandidatid
							JOIN
								(
									SELECT
										proje.project_id AS project_id
									FROM
										idk_projects proje
									WHERE
										proje.project_name LIKE ('%- Ugovor%')
										AND
										proje.project_nalogid = :nalogId
								)
								AS
								proj
							ON
								pkand.pk_projectid = proj.project_id
						)
						AS kan
					JOIN
						(
							SELECT
								pcap.pca_kandidat_id AS candId
							FROM
								idk_pp_cand_appts pcap
							JOIN
								(
									SELECT
										pappo.pap_id AS pap_id
									FROM
										idk_pp_appointments pappo
									JOIN
										idk_pp_appointment_groups pagr
									ON
										pappo.pap_nalog_id = :nalogId
										AND
										pappo.pap_group_id = pagr.ppaq_id
										AND
										MOD(getNumberOfWeekdays(pagr.ppaq_end_date, CURRENT_DATE),:freq)=0
										AND
										pagr.ppaq_end_date < CURRENT_DATE
								)
								AS
								papp
							ON
								pcap.pca_appointment_id = papp.pap_id
								AND
								pcap.pca_avg_rating is not null
								AND
								pcap.pca_id IN (
									SELECT
										MAX(pcapp.pca_id) AS pcaId
									FROM
										idk_pp_cand_appts pcapp
									JOIN
										idk_pp_appointments pappoi
									ON
										pcapp.pca_appointment_id = pappoi.pap_id
										AND
										pcapp.pca_avg_rating is not null
										AND
										pappoi.pap_nalog_id = :nalogId
										AND
										pcapp.pca_kandidat_id IN (
											SELECT
												kandi.kandidat_id AS candId
											FROM
												idk_kandidati kandi
											JOIN
												idk_project_kandidati pkandi
											ON
												kandi.kandidat_ppa_partner_id is null
												AND
												kandi.kandidat_status_prijave = 7
												AND
												kandi.kandidat_id = pkandi.pk_kandidatid
											JOIN
												(
													SELECT
														projec.project_id AS project_id
													FROM
														idk_projects projec
													WHERE
														projec.project_name LIKE ('%- Ugovor%')
														AND
														projec.project_nalogid = :nalogId
												)
												AS
												projee
											ON
												pkandi.pk_projectid = projee.project_id
										)
									GROUP BY
										pcapp.pca_kandidat_id
								)
						)
						AS pca
					ON
						kan.candId = pca.candId
				");

				$query_gc->execute(array(
					":nalogId" => $prs_nalog_id,
					":freq" => $frequency
				));
				$candidates = array();
				while($row_gc = $query_gc->fetch()){
					array_push($candidates, $row_gc['kandidat_id']);
				}
				return $candidates;
			break;
			case 4:
				$query_gc = $db->prepare("
					SELECT kan.kandidat_id
					FROM idk_kandidati kan
					JOIN (
						SELECT sq_pk.pk_kandidatid
						FROM idk_projects sq_pr
						JOIN idk_project_kandidati sq_pk
						ON sq_pk.pk_projectid = sq_pr.project_id
						WHERE sq_pr.project_name LIKE '%- Ugovor%'
						AND sq_pr.project_nalogid = :nalog_id
					) pr
					ON pr.pk_kandidatid = kan.kandidat_id
					WHERE kan.kandidat_ppa_partner_id IS NOT NULL
					AND kan.kandidat_status_prijave = 7
					AND (
						kan.kandidat_pp_lokacija = ''
					 OR kan.kandidat_pp_pozicija = ''
					 OR kan.kandidat_pp_plata = ''
					 OR kan.kandidat_pp_lokacija IS NULL
					 OR kan.kandidat_pp_pozicija IS NULL
					 OR kan.kandidat_pp_plata IS NULL
					)
					AND getNumberOfWeekdays(kan.kandidat_pp_datum_prihvatanja, CURRENT_DATE()) % $frequency = 0
					GROUP BY kan.kandidat_id
				");

				$query_gc->execute(array(
					":nalog_id" => $prs_nalog_id
				));
				$candidates = array();
				while($row_gc = $query_gc->fetch()){
					array_push($candidates, $row_gc['kandidat_id']);
				}
				return $candidates;
			break;
			case 5:
				$query_gc = $db->prepare("SELECT
											kandidat_id
										FROM
											idk_kandidati
										LEFT JOIN
											idk_pp_contract_sent
										ON
											idk_kandidati.kandidat_id = idk_pp_contract_sent.cs_kandidat_id
										AND
											idk_kandidati.kandidat_nalog_id = idk_pp_contract_sent.cs_nalog_id
										AND
											idk_kandidati.kandidat_ppa_partner_id = idk_pp_contract_sent.cs_partner_id
										WHERE
											idk_kandidati.kandidat_status_prijave = 7
										AND
											idk_kandidati.kandidat_nalog_id = :nalog_id
										AND
											idk_kandidati.kandidat_ppa_partner_id = :partner_id
										AND
											idk_pp_contract_sent.id_cs IS NULL
										AND
											getNumberOfWeekdays(idk_kandidati.kandidat_pp_datum_prihvatanja, CURRENT_DATE()) % $frequency = 0;");

				$query_gc->execute(array(
						":nalog_id" => $prs_nalog_id,
						":partner_id" => $prs_partner_id
				));
				$candidates = array();
				while($row_gc = $query_gc->fetch()){
					array_push($candidates, $row_gc['kandidat_id']);
				}
				return $candidates;
			break;
			case 7:
				$query_gc = $db->prepare("
					SELECT kan.kandidat_id
					FROM idk_kandidati kan
					WHERE kan.kandidat_status_prijave = 8
					AND kan.kandidat_nalog_id = :nalog_id
					AND (getNumberOfWeekdays(date(kan.kandidat_latest_reserved_time),CURRENT_DATE())- :prs_create_after) >= 0
					AND (getNumberOfWeekdays(date(kan.kandidat_latest_reserved_time), CURRENT_DATE()) - :prs_create_after) % $frequency = 0;
				");

				$query_gc->execute(array(
						":nalog_id" => $prs_nalog_id,
						":partner_id" => $prs_partner_id,
						":prs_create_after" => $prs_create_after
				));
				$candidates = array();
				while($row_gc = $query_gc->fetch()){
					array_push($candidates, $row_gc['kandidat_id']);
				}
				return $candidates;
			break;

			case 8:
				$query_gc = $db->prepare("
					SELECT
						kandidat_id
					FROM
						idk_kandidati
					JOIN
						idk_nostrifikovane_diplome
					ON
						kandidat_dipl_id = id_cand_dipl
					JOIN
						idk_pp_nalog_required_documents
					ON
						kandidat_nalog_id = nrd_nalog_id
					WHERE
						kandidat_status_prijave IN(9, 12)
					AND kandidat_drzavljanstvo_vrsta != 'EU državljanin'
					AND kandidat_nalog_id = :nalog_id
					AND kandidat_ppa_partner_id = :partner_id
					AND	id_cand_job IS NOT NULL
					AND nrd_type_id = 14
					AND nrd_status = 1
					AND (full_recognition is null OR full_recognition = 0)
					AND kandidat_nacin_odlaska = 0
					AND kandidat_ppa_partner_id is not null
					AND kandidat_id NOT IN(
						SELECT
							doc_candidate_id
						FROM
							idk_pp_documents
						JOIN
							idk_pp_nalog_required_documents
						ON
							doc_nrd_id = nrd_id
						WHERE
							nrd_type_id = 14
					)
					AND kandidat_id NOT IN(
						SELECT
							pr_candidate_id
						FROM
							idk_pp_reminders
						JOIN
							idk_pp_reminder_settings
						ON
							pr_reminder_setting_id = prs_id
						WHERE
							prs_reminder_type_id = 8
						AND
							pr_status = 3
					)
					AND getNumberOfWeekdays(DATE(upload_date_nd), CURRENT_DATE()) % $frequency = 0;
				");
				$query_gc->execute(array(
					":nalog_id"=>$prs_nalog_id, 
					":partner_id" => $prs_partner_id
				));
				$candidates = array();
				while($row_gc = $query_gc->fetch()){
					array_push($candidates,$row_gc['kandidat_id']);
				}
				return $candidates;
			break;

			case 9:
				// POKUPI KANDIDATE KOJI SU NA STATUSU PRIKUPLJA DOKUMENTACIJU (12) ZA ODGOVARAJUĆI NALOG_ID I PARTNER_ID
				// KANDIDATI MORAJU IMATI QUALIPLAN ZA TAJ NALOG (GLEDA SE IMA LI ZA NALOG TOG KANDIDATA UPLOADAN DOKUMENT QUALIPLAN (STATUS: 14))
				// KANDIDATI MORAJU IMATI PREUZETU DIPLOMU (GLEDA SE DA LI JE IZVRŠEN REMINDER (STATUS IZVRŠEN:3) PREUZMI NOST.DIPLOMU (STATUS:8))
				// AKTIVIRA SE 15 DANA NAKON PREUZIMANJA DIPLOME
				/*
					Stari query
					
						$query_gc = $db->prepare("
							SELECT kan.kandidat_id
							FROM idk_kandidati kan
							INNER JOIN idk_pp_nalog_required_documents ON kan.kandidat_nalog_id = idk_pp_nalog_required_documents.nrd_nalog_id
							INNER JOIN idk_pp_reminders ON kan.kandidat_id = idk_pp_reminders.pr_candidate_id
							INNER JOIN idk_pp_reminder_settings ON idk_pp_reminders.pr_reminder_setting_id = idk_pp_reminder_settings.prs_id
							WHERE kan.kandidat_status_prijave = 12
							AND kan.kandidat_nalog_id = :nalog_id
							AND kan.kandidat_ppa_partner_id = :partner_id
							AND idk_pp_nalog_required_documents.nrd_type_id = 14
							AND idk_pp_reminders.pr_status = 3
							AND idk_pp_reminder_settings.prs_reminder_type_id = 8
							AND (getNumberOfWeekdays(CONVERT(idk_pp_reminders.pr_date_completed, DATE), CURRENT_DATE()) - $prs_create_after) % $frequency = 0
							AND (getNumberOfWeekdays(CONVERT(idk_pp_reminders.pr_date_completed, DATE), CURRENT_DATE()) - $prs_create_after) >= 0
							GROUP BY kan.kandidat_id;
						");
				*/

				$query_gc = $db->prepare("
					SELECT 
						kan.kandidat_id
					FROM 
						idk_kandidati kan 
					INNER JOIN 
						(
							SELECT 
								nrd1.nrd_id, 
								nrd1.nrd_nalog_id
							FROM 
								idk_pp_nalog_required_documents nrd1 
							WHERE 
								nrd1.nrd_nalog_id = :nalog_id
								AND 
								nrd1.nrd_type_id = 14 
								AND 
								nrd1.nrd_status = 1
						)
						AS 
						nrd 
					ON 
						kan.kandidat_nalog_id = nrd.nrd_nalog_id 
					INNER JOIN 
						(
							SELECT 
								pr1.pr_candidate_id, 
								pr1.pr_date_completed
							FROM 
								idk_pp_reminders pr1 
							INNER JOIN 
								idk_pp_reminder_settings prs1
							ON 
								pr1.pr_reminder_setting_id = prs1.prs_id
							WHERE 
								pr1.pr_status = 3
								AND 
								prs1.prs_nalog_id = :nalog_id
								AND 
								prs1.prs_partner_id = :partner_id
								AND 
								prs1.prs_reminder_type_id = 8
						)
						AS 
						pr 
					ON 
						kan.kandidat_id = pr.pr_candidate_id
					WHERE 
						kan.kandidat_status_prijave = 12 
						AND 
						kan.kandidat_nalog_id = :nalog_id
						AND 
						kan.kandidat_ppa_partner_id = :partner_id
						AND 
						(getNumberOfWeekdays(CONVERT(pr.pr_date_completed, DATE), CURRENT_DATE()) - :prs_create_after) % :frequency = 0				
						AND 
						(getNumberOfWeekdays(CONVERT(pr.pr_date_completed, DATE), CURRENT_DATE()) - :prs_create_after) >= 0
						AND 
						kan.kandidat_id NOT IN (
							SELECT 
								pd2.doc_candidate_id 
							FROM 
								idk_pp_documents pd2 
							INNER JOIN 
								idk_pp_nalog_required_documents nrd2
							ON 
								pd2.doc_nrd_id = nrd2.nrd_id
							INNER JOIN 
								idk_kandidati kan2 
							ON 
								pd2.doc_candidate_id = kan2.kandidat_id
							WHERE 
								kan2.kandidat_status_prijave = 12
								AND 
								kan2.kandidat_nalog_id = :nalog_id
								AND 
								kan2.kandidat_ppa_partner_id = :partner_id
								AND 
								nrd2.nrd_nalog_id = :nalog_id
								AND 
								nrd2.nrd_type_id = 14 
								AND 
								nrd2.nrd_status = 1
								AND 
								pd2.doc_status IN(3,6,9,12,15,18)
						)
						AND 
						kan.kandidat_drzavljanstvo_vrsta NOT LIKE ('EU državljanin')
						AND 
						kan.kandidat_nacin_odlaska = 0
						AND 
						kan.kandidat_id NOT IN (
							SELECT 
								nd3.id_cand_job 
							FROM 
								idk_nostrifikovane_diplome nd3
							INNER JOIN 
								idk_kandidati kan3 
							ON 
								nd3.id_cand_job = kan3.kandidat_id
							WHERE 
								nd3.full_recognition IN (1,2)
								AND 
								kan3.kandidat_status_prijave = 12
								AND 
								kan3.kandidat_nalog_id = :nalog_id
								AND 
								kan3.kandidat_ppa_partner_id = :partner_id
						)
				");

				$query_gc->execute(array(
					":nalog_id" => $prs_nalog_id,
					":partner_id" => $prs_partner_id, 
					":prs_create_after" => $prs_create_after,
					":frequency" => $frequency
				));
				$candidates = array();
				while($row_gc = $query_gc->fetch()){
					array_push($candidates,$row_gc['kandidat_id']);
				}
				return $candidates;

			break;
			case 10:

				$query_gc = $db->prepare("
					/*
						Stručni kadar START
						*/
							SELECT 
								kan.kandidat_id, 
								candidatesInfo.documentationDate,
								candidatesInfo.departureType
							FROM 
								idk_kandidati kan 
							JOIN 
								(
									/*
										Ovaj query izvlaci:
										-   kandidate na statusu 12 i koji su na određenom nalogu
										-   aktuelni log status prijave 12 zbog datuma
									*/
									SELECT 
										kan1.kandidat_id AS candidateId, 
										lsp1.lsp_datetime AS documentationDate, 
										'Strucni kadar' AS departureType
									FROM 
										idk_log_statusi_prijave lsp1
									JOIN 
										idk_kandidati kan1
									ON 
										lsp1.lsp_kandidat_id = kan1.kandidat_id
									LEFT JOIN
										idk_nostrifikovane_diplome nd1 
									ON 
										kan1.kandidat_id = nd1.id_cand_job
									WHERE 
										lsp1.lsp_status_prijave_id = 12 
										AND 
										lsp1.lsp_broj_dana IS NULL
										AND
										kan1.kandidat_status_prijave = 12
										AND
										kan1.kandidat_nalog_id = :nalog_id
										/*
											kan1.kandidat_ppa_partner_id = :partnerId
										*/
										AND 
										kan1.kandidat_nacin_odlaska = 0
										AND
										nd1.full_recognition NOT IN (1,2)
								) 
								AS 
								candidatesInfo
							ON 
								kan.kandidat_id = candidatesInfo.candidateId
							JOIN 
								(
									/*
										Ovaj query izvlaci:
										-   kandidate koji imaju uploadan qualiplan
										-   kandidate na statusu 12 i koji su na određenom nalogu
									*/
									SELECT 
										doc1.doc_candidate_id AS candidateId
									FROM 
										idk_pp_documents doc1
									JOIN 
										idk_pp_nalog_required_documents nrd1 
									ON 
										doc1.doc_nalog_id = :nalog_id
										AND 
										nrd1.nrd_nalog_id = :nalog_id
										AND 
										doc1.doc_nrd_id = nrd1.nrd_id 
										AND 
										doc1.doc_crd_id IS NULL
										AND 
										nrd1.nrd_status = 1
									JOIN 
										idk_kandidati kan1
									ON 
										doc1.doc_candidate_id = kan1.kandidat_id 
									LEFT JOIN
										idk_nostrifikovane_diplome nd1 
									ON 
										kan1.kandidat_id = nd1.id_cand_job
									WHERE 
										doc1.doc_status IN (3, 6, 9, 12, 15, 18, 21)
										AND 
										nrd1.nrd_type_id = 14
										AND 
										nrd1.nrd_skilled_candidates = 1
										AND
										kan1.kandidat_status_prijave = 12
										AND
										kan1.kandidat_nalog_id = :nalog_id
										AND 
										kan1.kandidat_nacin_odlaska IN (0)
										AND
										nd1.full_recognition NOT IN (1,2)
								)
								AS 
								candidatesHasQualiplan
							ON 
								kan.kandidat_id = candidatesHasQualiplan.candidateId
							WHERE 
								kan.kandidat_id NOT IN (
									/*
										Ovaj query izvlaci:
										-   kandidate koji imaju uploadovan traženi dokument za koji je zaduzen PM
										-   kandidate na statusu 12 i koji su na određenom nalogu
									*/
									SELECT 
										doc1.doc_candidate_id
									FROM 
										idk_pp_documents doc1
									JOIN 
										idk_pp_nalog_required_documents nrd1 
									ON 
										doc1.doc_nalog_id = :nalog_id
										AND 
										nrd1.nrd_nalog_id = :nalog_id
										AND 
										doc1.doc_nrd_id = nrd1.nrd_id 
										AND 
										doc1.doc_crd_id IS NULL
										AND 
										nrd1.nrd_status = 1
									JOIN 
										idk_kandidati kan1
									ON 
										doc1.doc_candidate_id = kan1.kandidat_id 
									LEFT JOIN
										idk_nostrifikovane_diplome nd1 
									ON 
										kan1.kandidat_id = nd1.id_cand_job
									WHERE 
										doc1.doc_status != 27 
										AND 
										nrd1.nrd_id = :prd_nrd_id
										AND 
										nrd1.nrd_done_by IN (2, 3)
										AND
										kan1.kandidat_status_prijave = 12
										AND
										kan1.kandidat_nalog_id = :nalog_id
										AND 
										kan1.kandidat_nacin_odlaska = 0
										AND
										nd1.full_recognition NOT IN (1,2)
										AND
										nrd1.nrd_skilled_candidates = 1
								)
								AND 
								1 = (
									/*
										Ovaj query izvlaci:
										-   informaciju da li je za nalog potreban određeni dokument
									*/
									SELECT
										(
											CASE 
												WHEN nrdInfo.countNrd > 0 THEN 1
												ELSE 0
											END 
										) AS flagDocuments
									FROM
										(
											SELECT 
												COUNT(nrd1.nrd_id) AS countNrd
											FROM 
												idk_pp_nalog_required_documents nrd1
											WHERE
												nrd1.nrd_id = :prd_nrd_id
												AND 
												nrd1.nrd_status = 1 
												AND
												nrd1.nrd_skilled_candidates = 1
												AND 
												nrd1.nrd_nalog_id = :nalog_id
												AND 
												nrd1.nrd_done_by IN (2,3)
										)
										AS 
										nrdInfo
								)
								AND 
								(getNumberOfWeekdays(DATE(candidatesInfo.documentationDate),CURRENT_DATE()) - :prs_create_after) % :triggerDate = 0 
								AND
								(getNumberOfWeekdays(DATE(candidatesInfo.documentationDate),CURRENT_DATE()) - :prs_create_after) >= 0
						/*
						Stručni kadar END
					*/
						UNION
					/*
						West balkan START
						*/
							SELECT 
								kan.kandidat_id, 
								candidatesInfo.documentationDate,
								candidatesInfo.departureType
							FROM 
								idk_kandidati kan 
							JOIN 
								(
									/*
										Ovaj query izvlaci:
										-   kandidate na statusu 12 i koji su na određenom nalogu
										-   aktuelni log status prijave 12 zbog datuma
									*/
									SELECT 
										kan1.kandidat_id AS candidateId, 
										lsp1.lsp_datetime AS documentationDate, 
										'West balkan' AS departureType
									FROM 
										idk_log_statusi_prijave lsp1
									JOIN 
										idk_kandidati kan1
									ON 
										lsp1.lsp_kandidat_id = kan1.kandidat_id
									LEFT JOIN
										idk_nostrifikovane_diplome nd1 
									ON 
										kan1.kandidat_id = nd1.id_cand_job
									WHERE 
										lsp1.lsp_status_prijave_id = 12 
										AND 
										lsp1.lsp_broj_dana IS NULL
										AND
										kan1.kandidat_status_prijave = 12
										AND
										kan1.kandidat_nalog_id = :nalog_id
										/*
											kan1.kandidat_ppa_partner_id = :partnerId
										*/
										AND 
										(
											kan1.kandidat_nacin_odlaska = 2
											OR
											nd1.full_recognition IN (1,2)
										)
								) 
								AS 
								candidatesInfo
							ON 
								kan.kandidat_id = candidatesInfo.candidateId
							WHERE 
								kan.kandidat_id NOT IN (
									/*
										Ovaj query izvlaci:
										-   kandidate koji imaju uploadovan traženi dokument za koji je zaduzen PM
										-   kandidate na statusu 12 i koji su na određenom nalogu
									*/
									SELECT 
										doc1.doc_candidate_id
									FROM 
										idk_pp_documents doc1
									JOIN 
										idk_pp_nalog_required_documents nrd1 
									ON 
										doc1.doc_nalog_id = :nalog_id
										AND 
										nrd1.nrd_nalog_id = :nalog_id
										AND 
										doc1.doc_nrd_id = nrd1.nrd_id 
										AND 
										doc1.doc_crd_id IS NULL
										AND 
										nrd1.nrd_status = 1
									JOIN 
										idk_kandidati kan1
									ON 
										doc1.doc_candidate_id = kan1.kandidat_id 
									LEFT JOIN
										idk_nostrifikovane_diplome nd1 
									ON 
										kan1.kandidat_id = nd1.id_cand_job
									WHERE 
										doc1.doc_status != 27 
										AND 
										nrd1.nrd_id = :prd_nrd_id
										AND 
										nrd1.nrd_done_by IN (2, 3)
										AND
										kan1.kandidat_status_prijave = 12
										AND
										kan1.kandidat_nalog_id = :nalog_id
										AND 
										(
											kan1.kandidat_nacin_odlaska = 2
											OR
											nd1.full_recognition IN (1,2)
										)
										AND
										nrd1.nrd_west_balkan = 1
								)
								AND 
								1 = (
									/*
										Ovaj query izvlaci:
										-   informaciju da li je za nalog potreban određeni dokument
									*/
									SELECT
										(
											CASE 
												WHEN nrdInfo.countNrd > 0 THEN 1
												ELSE 0
											END 
										) AS flagDocuments
									FROM
										(
											SELECT 
												COUNT(nrd1.nrd_id) AS countNrd
											FROM 
												idk_pp_nalog_required_documents nrd1
											WHERE
												nrd1.nrd_id = :prd_nrd_id
												AND 
												nrd1.nrd_status = 1 
												AND
												nrd1.nrd_west_balkan = 1
												AND 
												nrd1.nrd_nalog_id = :nalog_id
												AND 
												nrd1.nrd_done_by IN (2,3)
										)
										AS 
										nrdInfo
								)
								AND 
								(getNumberOfWeekdays(DATE(candidatesInfo.documentationDate),CURRENT_DATE()) - :prs_create_after) % :triggerDate = 0 
								AND
								(getNumberOfWeekdays(DATE(candidatesInfo.documentationDate),CURRENT_DATE()) - :prs_create_after) >= 0
						/*
						West balkan END
					*/
						UNION 
					/*
						Radno iskustvo START
						*/
							SELECT 
								kan.kandidat_id, 
								candidatesInfo.documentationDate,
								candidatesInfo.departureType
							FROM 
								idk_kandidati kan 
							JOIN 
								(
									/*
										Ovaj query izvlaci:
										-   kandidate na statusu 12 i koji su na određenom nalogu
										-   aktuelni log status prijave 12 zbog datuma
									*/
									SELECT 
										kan1.kandidat_id AS candidateId, 
										lsp1.lsp_datetime AS documentationDate, 
										'Radno iskustvo' AS departureType
									FROM 
										idk_log_statusi_prijave lsp1
									JOIN 
										idk_kandidati kan1
									ON 
										lsp1.lsp_kandidat_id = kan1.kandidat_id
									LEFT JOIN
										idk_nostrifikovane_diplome nd1 
									ON 
										kan1.kandidat_id = nd1.id_cand_job
									WHERE 
										lsp1.lsp_status_prijave_id = 12 
										AND 
										lsp1.lsp_broj_dana IS NULL
										AND
										kan1.kandidat_status_prijave = 12
										AND
										kan1.kandidat_nalog_id = :nalog_id
										/*
											kan1.kandidat_ppa_partner_id = :partnerId
										*/
										AND
										kan1.kandidat_nacin_odlaska = 3
								) 
								AS 
								candidatesInfo
							ON 
								kan.kandidat_id = candidatesInfo.candidateId
							WHERE 
								kan.kandidat_id NOT IN (
									/*
										Ovaj query izvlaci:
										-   kandidate koji imaju uploadovan traženi dokument za koji je zaduzen PM
										-   kandidate na statusu 12 i koji su na određenom nalogu
									*/
									SELECT 
										doc1.doc_candidate_id
									FROM 
										idk_pp_documents doc1
									JOIN 
										idk_pp_nalog_required_documents nrd1 
									ON 
										doc1.doc_nalog_id = :nalog_id
										AND 
										nrd1.nrd_nalog_id = :nalog_id
										AND 
										doc1.doc_nrd_id = nrd1.nrd_id 
										AND 
										doc1.doc_crd_id IS NULL
										AND 
										nrd1.nrd_status = 1
									JOIN 
										idk_kandidati kan1
									ON 
										doc1.doc_candidate_id = kan1.kandidat_id 
									LEFT JOIN
										idk_nostrifikovane_diplome nd1 
									ON 
										kan1.kandidat_id = nd1.id_cand_job
									WHERE 
										doc1.doc_status != 27 
										AND 
										nrd1.nrd_id = :prd_nrd_id
										AND 
										nrd1.nrd_done_by IN (2, 3)
										AND
										kan1.kandidat_status_prijave = 12
										AND
										kan1.kandidat_nalog_id = :nalog_id
										AND 
										kan1.kandidat_nacin_odlaska = 3
										AND
										nrd1.nrd_work_experience = 1
								)
								AND 
								1 = (
									/*
										Ovaj query izvlaci:
										-   informaciju da li je za nalog potreban određeni dokument
									*/
									SELECT
										(
											CASE 
												WHEN nrdInfo.countNrd > 0 THEN 1
												ELSE 0
											END 
										) AS flagDocuments
									FROM
										(
											SELECT 
												COUNT(nrd1.nrd_id) AS countNrd
											FROM 
												idk_pp_nalog_required_documents nrd1
											WHERE
												nrd1.nrd_id = :prd_nrd_id
												AND 
												nrd1.nrd_status = 1 
												AND
												nrd1.nrd_work_experience = 1
												AND 
												nrd1.nrd_nalog_id = :nalog_id
												AND 
												nrd1.nrd_done_by IN (2,3)
										)
										AS 
										nrdInfo
								)
								AND 
								(getNumberOfWeekdays(DATE(candidatesInfo.documentationDate),CURRENT_DATE()) - :prs_create_after) % :triggerDate = 0 
								AND
								(getNumberOfWeekdays(DATE(candidatesInfo.documentationDate),CURRENT_DATE()) - :prs_create_after) >= 0
						/*
						Radno iskustvo END
					*/
				");

				$query_gc->execute(array(
					":nalog_id" => $prs_nalog_id,
					":prd_nrd_id" => $prd_nrd_id,
					":triggerDate" => $frequency,
					":prs_create_after" => $prs_create_after
				));
				$candidates = array();
				while($row_gc = $query_gc->fetch()){
					array_push($candidates, $row_gc['kandidat_id']);
				}
				return $candidates;

			break;

			case 11:

				$query_gc = $db->prepare("
					/*
						Stručni kadar START
						*/
							SELECT 
								kan.kandidat_id, 
								candidatesInfo.documentationDate,
								candidatesInfo.departureType
							FROM 
								idk_kandidati kan 
							JOIN 
								(
									/*
										Ovaj query izvlaci:
										-   kandidate na statusu 12 i koji su na određenom nalogu
										-   aktuelni log status prijave 12 zbog datuma
									*/
									SELECT 
										kan1.kandidat_id AS candidateId, 
										lsp1.lsp_datetime AS documentationDate, 
										'Strucni kadar' AS departureType
									FROM 
										idk_log_statusi_prijave lsp1
									JOIN 
										idk_kandidati kan1
									ON 
										lsp1.lsp_kandidat_id = kan1.kandidat_id
									LEFT JOIN
										idk_nostrifikovane_diplome nd1 
									ON 
										kan1.kandidat_id = nd1.id_cand_job
									WHERE 
										lsp1.lsp_status_prijave_id = 12 
										AND 
										lsp1.lsp_broj_dana IS NULL
										AND
										kan1.kandidat_status_prijave = 12
										AND
										kan1.kandidat_nalog_id = :nalog_id
										AND
										kan1.kandidat_ppa_partner_id = :partnerId
										AND 
										kan1.kandidat_nacin_odlaska = 0
										AND
										nd1.full_recognition NOT IN (1,2)
								) 
								AS 
								candidatesInfo
							ON 
								kan.kandidat_id = candidatesInfo.candidateId
							JOIN 
								(
									/*
										Ovaj query izvlaci:
										-   kandidate koji imaju uploadan qualiplan
										-   kandidate na statusu 12 i koji su na određenom nalogu
									*/
									SELECT 
										doc1.doc_candidate_id AS candidateId
									FROM 
										idk_pp_documents doc1
									JOIN 
										idk_pp_nalog_required_documents nrd1 
									ON 
										doc1.doc_nalog_id = :nalog_id
										AND 
										nrd1.nrd_nalog_id = :nalog_id
										AND 
										doc1.doc_nrd_id = nrd1.nrd_id 
										AND 
										doc1.doc_crd_id IS NULL
										AND 
										nrd1.nrd_status = 1
									JOIN 
										idk_kandidati kan1
									ON 
										doc1.doc_candidate_id = kan1.kandidat_id 
									LEFT JOIN
										idk_nostrifikovane_diplome nd1 
									ON 
										kan1.kandidat_id = nd1.id_cand_job
									WHERE 
										doc1.doc_status IN (3, 6, 9, 12, 15, 18, 21)
										AND 
										nrd1.nrd_type_id = 14
										AND 
										nrd1.nrd_skilled_candidates = 1
										AND
										kan1.kandidat_status_prijave = 12
										AND
										kan1.kandidat_nalog_id = :nalog_id
										AND
										kan1.kandidat_ppa_partner_id = :partnerId
										AND 
										kan1.kandidat_nacin_odlaska IN (0)
										AND
										nd1.full_recognition NOT IN (1,2)
								)
								AS 
								candidatesHasQualiplan
							ON 
								kan.kandidat_id = candidatesHasQualiplan.candidateId
							WHERE 
								kan.kandidat_id NOT IN (
									/*
										Ovaj query izvlaci:
										-   kandidate koji imaju uploadovan traženi dokument za koji je zaduzen PM
										-   kandidate na statusu 12 i koji su na određenom nalogu
									*/
									SELECT 
										doc1.doc_candidate_id
									FROM 
										idk_pp_documents doc1
									JOIN 
										idk_pp_nalog_required_documents nrd1 
									ON 
										doc1.doc_nalog_id = :nalog_id
										AND 
										nrd1.nrd_nalog_id = :nalog_id
										AND 
										doc1.doc_nrd_id = nrd1.nrd_id 
										AND 
										doc1.doc_crd_id IS NULL
										AND 
										nrd1.nrd_status = 1
									JOIN 
										idk_kandidati kan1
									ON 
										doc1.doc_candidate_id = kan1.kandidat_id 
									LEFT JOIN
										idk_nostrifikovane_diplome nd1 
									ON 
										kan1.kandidat_id = nd1.id_cand_job
									WHERE 
										doc1.doc_status != 27 
										AND 
										nrd1.nrd_id = :prd_nrd_id
										AND 
										nrd1.nrd_done_by = 1
										AND
										kan1.kandidat_status_prijave = 12
										AND
										kan1.kandidat_nalog_id = :nalog_id
										AND
										kan1.kandidat_ppa_partner_id = :partnerId
										AND 
										kan1.kandidat_nacin_odlaska = 0
										AND
										nd1.full_recognition NOT IN (1,2)
										AND
										nrd1.nrd_skilled_candidates = 1
								)
								AND 
								1 = (
									/*
										Ovaj query izvlaci:
										-   informaciju da li je za nalog potreban određeni dokument
									*/
									SELECT
										(
											CASE 
												WHEN nrdInfo.countNrd > 0 THEN 1
												ELSE 0
											END 
										) AS flagDocuments
									FROM
										(
											SELECT 
												COUNT(nrd1.nrd_id) AS countNrd
											FROM 
												idk_pp_nalog_required_documents nrd1
											WHERE
												nrd1.nrd_id = :prd_nrd_id
												AND 
												nrd1.nrd_status = 1 
												AND
												nrd1.nrd_skilled_candidates = 1
												AND 
												nrd1.nrd_nalog_id = :nalog_id
												AND 
												nrd1.nrd_done_by = 1
										)
										AS 
										nrdInfo
								)
								AND 
								(getNumberOfWeekdays(DATE(candidatesInfo.documentationDate),CURRENT_DATE()) - :prs_create_after) % :triggerDate = 0 
								AND
								(getNumberOfWeekdays(DATE(candidatesInfo.documentationDate),CURRENT_DATE()) - :prs_create_after) >= 0
						/*
						Stručni kadar END
					*/
						UNION
					/*
						West balkan START
						*/
							SELECT 
								kan.kandidat_id, 
								candidatesInfo.documentationDate,
								candidatesInfo.departureType
							FROM 
								idk_kandidati kan 
							JOIN 
								(
									/*
										Ovaj query izvlaci:
										-   kandidate na statusu 12 i koji su na određenom nalogu
										-   aktuelni log status prijave 12 zbog datuma
									*/
									SELECT 
										kan1.kandidat_id AS candidateId, 
										lsp1.lsp_datetime AS documentationDate, 
										'West balkan' AS departureType
									FROM 
										idk_log_statusi_prijave lsp1
									JOIN 
										idk_kandidati kan1
									ON 
										lsp1.lsp_kandidat_id = kan1.kandidat_id
									LEFT JOIN
										idk_nostrifikovane_diplome nd1 
									ON 
										kan1.kandidat_id = nd1.id_cand_job
									WHERE 
										lsp1.lsp_status_prijave_id = 12 
										AND 
										lsp1.lsp_broj_dana IS NULL
										AND
										kan1.kandidat_status_prijave = 12
										AND
										kan1.kandidat_nalog_id = :nalog_id
										AND
										kan1.kandidat_ppa_partner_id = :partnerId
										AND 
										(
											kan1.kandidat_nacin_odlaska = 2
											OR
											nd1.full_recognition IN (1,2)
										)
								) 
								AS 
								candidatesInfo
							ON 
								kan.kandidat_id = candidatesInfo.candidateId
							WHERE 
								kan.kandidat_id NOT IN (
									/*
										Ovaj query izvlaci:
										-   kandidate koji imaju uploadovan traženi dokument za koji je zaduzen PM
										-   kandidate na statusu 12 i koji su na određenom nalogu
									*/
									SELECT 
										doc1.doc_candidate_id
									FROM 
										idk_pp_documents doc1
									JOIN 
										idk_pp_nalog_required_documents nrd1 
									ON 
										doc1.doc_nalog_id = :nalog_id
										AND 
										nrd1.nrd_nalog_id = :nalog_id
										AND 
										doc1.doc_nrd_id = nrd1.nrd_id 
										AND 
										doc1.doc_crd_id IS NULL
										AND 
										nrd1.nrd_status = 1
									JOIN 
										idk_kandidati kan1
									ON 
										doc1.doc_candidate_id = kan1.kandidat_id 
									LEFT JOIN
										idk_nostrifikovane_diplome nd1 
									ON 
										kan1.kandidat_id = nd1.id_cand_job
									WHERE 
										doc1.doc_status != 27 
										AND 
										nrd1.nrd_id = :prd_nrd_id
										AND 
										nrd1.nrd_done_by = 1
										AND
										kan1.kandidat_status_prijave = 12
										AND
										kan1.kandidat_nalog_id = :nalog_id
										AND 
										kan1.kandidat_ppa_partner_id = :partnerId
										AND 
										(
											kan1.kandidat_nacin_odlaska = 2
											OR
											nd1.full_recognition IN (1,2)
										)
										AND
										nrd1.nrd_west_balkan = 1
								)
								AND 
								1 = (
									/*
										Ovaj query izvlaci:
										-   informaciju da li je za nalog potreban određeni dokument
									*/
									SELECT
										(
											CASE 
												WHEN nrdInfo.countNrd > 0 THEN 1
												ELSE 0
											END 
										) AS flagDocuments
									FROM
										(
											SELECT 
												COUNT(nrd1.nrd_id) AS countNrd
											FROM 
												idk_pp_nalog_required_documents nrd1
											WHERE
												nrd1.nrd_id = :prd_nrd_id
												AND 
												nrd1.nrd_status = 1 
												AND
												nrd1.nrd_west_balkan = 1
												AND 
												nrd1.nrd_nalog_id = :nalog_id
												AND 
												nrd1.nrd_done_by = 1
										)
										AS 
										nrdInfo
								)
								AND 
								(getNumberOfWeekdays(DATE(candidatesInfo.documentationDate),CURRENT_DATE()) - :prs_create_after) % :triggerDate = 0 
								AND
								(getNumberOfWeekdays(DATE(candidatesInfo.documentationDate),CURRENT_DATE()) - :prs_create_after) >= 0
						/*
						West balkan END
					*/
						UNION 
					/*
						Radno iskustvo START
						*/
							SELECT 
								kan.kandidat_id, 
								candidatesInfo.documentationDate,
								candidatesInfo.departureType
							FROM 
								idk_kandidati kan 
							JOIN 
								(
									/*
										Ovaj query izvlaci:
										-   kandidate na statusu 12 i koji su na određenom nalogu
										-   aktuelni log status prijave 12 zbog datuma
									*/
									SELECT 
										kan1.kandidat_id AS candidateId, 
										lsp1.lsp_datetime AS documentationDate, 
										'Radno iskustvo' AS departureType
									FROM 
										idk_log_statusi_prijave lsp1
									JOIN 
										idk_kandidati kan1
									ON 
										lsp1.lsp_kandidat_id = kan1.kandidat_id
									LEFT JOIN
										idk_nostrifikovane_diplome nd1 
									ON 
										kan1.kandidat_id = nd1.id_cand_job
									WHERE 
										lsp1.lsp_status_prijave_id = 12 
										AND 
										lsp1.lsp_broj_dana IS NULL
										AND
										kan1.kandidat_status_prijave = 12
										AND
										kan1.kandidat_nalog_id = :nalog_id
										AND 
										kan1.kandidat_ppa_partner_id = :partnerId
										AND
										kan1.kandidat_nacin_odlaska = 3
								) 
								AS 
								candidatesInfo
							ON 
								kan.kandidat_id = candidatesInfo.candidateId
							WHERE 
								kan.kandidat_id NOT IN (
									/*
										Ovaj query izvlaci:
										-   kandidate koji imaju uploadovan traženi dokument za koji je zaduzen PM
										-   kandidate na statusu 12 i koji su na određenom nalogu
									*/
									SELECT 
										doc1.doc_candidate_id
									FROM 
										idk_pp_documents doc1
									JOIN 
										idk_pp_nalog_required_documents nrd1 
									ON 
										doc1.doc_nalog_id = :nalog_id
										AND 
										nrd1.nrd_nalog_id = :nalog_id
										AND 
										doc1.doc_nrd_id = nrd1.nrd_id 
										AND 
										doc1.doc_crd_id IS NULL
										AND 
										nrd1.nrd_status = 1
									JOIN 
										idk_kandidati kan1
									ON 
										doc1.doc_candidate_id = kan1.kandidat_id 
									LEFT JOIN
										idk_nostrifikovane_diplome nd1 
									ON 
										kan1.kandidat_id = nd1.id_cand_job
									WHERE 
										doc1.doc_status != 27 
										AND 
										nrd1.nrd_id = :prd_nrd_id
										AND 
										nrd1.nrd_done_by = 1
										AND
										kan1.kandidat_status_prijave = 12
										AND
										kan1.kandidat_nalog_id = :nalog_id
										AND 
										kan1.kandidat_ppa_partner_id = :partnerId
										AND 
										kan1.kandidat_nacin_odlaska = 3
										AND
										nrd1.nrd_work_experience = 1
								)
								AND 
								1 = (
									/*
										Ovaj query izvlaci:
										-   informaciju da li je za nalog potreban određeni dokument
									*/
									SELECT
										(
											CASE 
												WHEN nrdInfo.countNrd > 0 THEN 1
												ELSE 0
											END 
										) AS flagDocuments
									FROM
										(
											SELECT 
												COUNT(nrd1.nrd_id) AS countNrd
											FROM 
												idk_pp_nalog_required_documents nrd1
											WHERE
												nrd1.nrd_id = :prd_nrd_id
												AND 
												nrd1.nrd_status = 1 
												AND
												nrd1.nrd_work_experience = 1
												AND 
												nrd1.nrd_nalog_id = :nalog_id
												AND 
												nrd1.nrd_done_by = 1
										)
										AS 
										nrdInfo
								)
								AND 
								(getNumberOfWeekdays(DATE(candidatesInfo.documentationDate),CURRENT_DATE()) - :prs_create_after) % :triggerDate = 0 
								AND
								(getNumberOfWeekdays(DATE(candidatesInfo.documentationDate),CURRENT_DATE()) - :prs_create_after) >= 0
						/*
						Radno iskustvo END
					*/
				");

				$query_gc->execute(array(
					":nalog_id" => $prs_nalog_id,
					":prd_nrd_id" => $prd_nrd_id,
					":triggerDate" => $frequency,
					":partnerId" => $prs_partner_id,
					":prs_create_after" => $prs_create_after
				));
				$candidates = array();
				while($row_gc = $query_gc->fetch()){
					array_push($candidates, $row_gc['kandidat_id']);
				}
				return $candidates;

			break;//ovdje se zaboravilo break staviti , provjeirit nakon merganja grane zato sto sam na jos jednoj grani dodao break

			case 12:
				$query_gc = $db->prepare("
					SELECT
						kan.kandidat_id
					FROM
						idk_kandidati kan
					INNER JOIN
						idk_pp_documents doc
					ON
						kan.kandidat_id = doc.doc_candidate_id
						AND
						kan.kandidat_status_prijave = 12
						AND
						kan.kandidat_nalog_id = :nalogId
						AND
						kan.kandidat_nalog_id = doc.doc_nalog_id
						AND
						doc.doc_status = 3
						AND
						doc.doc_nrd_id = :nrdId
					INNER JOIN
						idk_pp_documents_status_logs dsl
					ON
						doc.doc_id = dsl.dsl_doc_id
						AND
						doc.doc_status = dsl.dsl_doc_status
						AND
						dsl.dsl_days_count is null
						AND
						MOD(getNumberOfWeekdays(DATE(dsl.dsl_date), CURRENT_DATE),:freq) = 0
						AND
						getNumberOfWeekdays(DATE(dsl.dsl_date), CURRENT_DATE) > 0
					INNER JOIN
						idk_pp_nalog_required_documents nrd
					ON
						doc.doc_nrd_id = nrd.nrd_id
						AND
						nrd.nrd_status = 1
						AND
						nrd.nrd_done_by = 1
					GROUP BY kan.kandidat_id
				");

				$query_gc->execute(array(
					":nalogId" => $prs_nalog_id,
					":nrdId" => $prd_nrd_id,
					":freq" => $frequency
				));
				$candidates = array();
				while($row_gc = $query_gc->fetch()){
					array_push($candidates, $row_gc['kandidat_id']);
				}
				return $candidates;
			break;

			case 13:
				$query_gc = $db->prepare("
				SELECT
					idk_kandidati.kandidat_id
				FROM
					idk_kandidati
				JOIN
					idk_pp_documents
				ON
					idk_kandidati.kandidat_id = idk_pp_documents.doc_candidate_id
				JOIN
					idk_pp_nalog_required_documents
				ON
					idk_pp_documents.doc_nrd_id = idk_pp_nalog_required_documents.nrd_id
				JOIN
					(
						SELECT
							dsl_doc_id, dsl_date
						FROM
							idk_pp_documents_status_logs
						JOIN
							idk_pp_documents
						ON
							idk_pp_documents_status_logs.dsl_doc_id = idk_pp_documents.doc_id
						WHERE
							dsl_doc_status = 21
						AND
							idk_pp_documents.doc_nalog_id = :nalog_id
						AND
							idk_pp_documents.doc_partner_id = :partner_id
					)
					DCS
				ON
					idk_pp_documents.doc_id = DCS.dsl_doc_id
				WHERE
					idk_pp_documents.doc_status = 21
					AND
					idk_pp_nalog_required_documents.nrd_id = :nrd_id
					AND
					idk_kandidati.kandidat_nalog_id = :nalog_id
					AND
					idk_kandidati.kandidat_ppa_partner_id = :partner_id
					AND
					idk_kandidati.kandidat_status_prijave = 12
					AND
					(getNumberOfWeekdays(DATE(DCS.dsl_date), CURRENT_DATE()) - :prs_create_after) % :freq = 0
					AND
					(getNumberOfWeekdays(DATE(DCS.dsl_date), CURRENT_DATE()) - :prs_create_after) >= 0
				GROUP BY
					idk_kandidati.kandidat_id;
				");

				$query_gc->execute(array(
					":nalog_id" => $prs_nalog_id,
					":partner_id" => $prs_partner_id,
					":nrd_id" => $prd_nrd_id,
					":freq" => $frequency,
					":prs_create_after" => $prs_create_after
				));
				$candidates = array();
				while($row_gc = $query_gc->fetch()){
					array_push($candidates, $row_gc['kandidat_id']);
				}
				return $candidates;
			break;
			case 14:
				/*
					*****************************************************************
					*							START								*
					*****************************************************************
					*			REMINDER TYPE 14 - POŠALJI DOKUMENTE				*
					*****************************************************************
				*/
					$query_gc = $db->prepare("
						SELECT
							docs.candidate AS kandidat_id
						FROM
							(
								SELECT
									doc.doc_candidate_id AS candidate,
									count(doc.doc_id) AS added
								FROM
									idk_pp_documents doc
								JOIN
								(
									SELECT
										nrd1.nrd_id
									FROM
										idk_pp_nalog_required_documents nrd1
									WHERE
										nrd1.nrd_nalog_id = :nalogId
									AND
										nrd1.nrd_status = 1
									AND
										nrd1.nrd_done_by = 1
									AND
										nrd1.nrd_type_id != 1
								)
								AS
									nrd
								ON
									doc.doc_nrd_id = nrd.nrd_id
								JOIN
								(
									SELECT
										kan1.kandidat_id
									FROM
										idk_kandidati kan1
									WHERE
										kan1.kandidat_status_prijave = 12
										AND
										kan1.kandidat_nalog_id = :nalogId
										AND
										kan1.kandidat_ppa_partner_id = :partnerId
								)
								AS
									kan
								ON
									doc.doc_candidate_id = kan.kandidat_id
								JOIN
									idk_pp_documents_status_logs docl
								ON
									doc.doc_id = docl.dsl_doc_id
								AND
									doc.doc_status = 9
								AND
									doc.doc_status = docl.dsl_doc_status
								AND
									MOD(getNumberOfWeekdays(CONVERT(docl.dsl_date, DATE), CURRENT_DATE()),:triggerDate) = 0
								GROUP BY doc.doc_candidate_id
							)
							AS
								docs
						WHERE
							docs.added = (
								SELECT
									count(nrd.nrd_id) AS potrebni
								FROM
									idk_pp_nalog_required_documents nrd
								WHERE
									nrd.nrd_nalog_id = :nalogId
								AND
									nrd.nrd_status = 1
								AND
									nrd.nrd_done_by = 1
								AND
									nrd.nrd_type_id != 1
							)
					");
					$query_gc->execute(array(
						":nalogId" => $prs_nalog_id,
						":partnerId" => $prs_partner_id,
						":triggerDate" => $frequency
					));
					$candidates = array();
					while($row_gc = $query_gc->fetch()){
						array_push($candidates, $row_gc['kandidat_id']);
					}
					return $candidates;
				/*
					*****************************************************************
					*			REMINDER TYPE 14 - POŠALJI DOKUMENTE				*
					*****************************************************************
					*							END									*
					*****************************************************************
				*/
			break;

			case 15:

				$query_gc = $db->prepare("SELECT
						docs.candidate AS kandidat_id
					FROM
						(
							/* Select koji uzima kandidata, broj dokumenata koji su spremni i ostale potrebne informacije*/
							SELECT
								doc.doc_candidate_id AS candidate,
								COUNT(doc.doc_id) AS added,
								kan.kandidat_nacin_odlaska as departure_type,
								kan.full_recognition as full_recognition,
								nal.nalog_poslodavac_trazi_jezik as language_required
							FROM
								idk_pp_documents doc
							
							JOIN(
								SELECT nrd1.nrd_id
								FROM
									idk_pp_nalog_required_documents nrd1
								WHERE
									nrd1.nrd_nalog_id = :nalog_id AND nrd1.nrd_status = 1 AND nrd1.nrd_type_id != 1
							) AS nrd
							ON doc.doc_nrd_id = nrd.nrd_id AND doc.doc_status = 18
							
							JOIN(
								SELECT kan1.kandidat_id, kan1.kandidat_nacin_odlaska, nd.full_recognition, kan1.kandidat_nalog_id
								FROM
									idk_kandidati kan1
								LEFT JOIN idk_nostrifikovane_diplome nd
								ON kan1.kandidat_id = nd.id_cand_job
								WHERE
									kan1.kandidat_status_prijave = 12 AND kan1.kandidat_nalog_id = :nalog_id
							) AS kan
							ON doc.doc_candidate_id = kan.kandidat_id
							JOIN idk_nalozi nal 
							ON kan.kandidat_nalog_id = nal.nalog_id
					
							GROUP BY
								doc.doc_candidate_id
						) AS docs
					WHERE
						/* U WHERE-u se gleda da je broj spremnih dokumenata jednak broju traženih, ali za svaki način odlaska posebno*/
						(
							/* West Balkan ili Potpuno priznata diploma/Evaluacija uz dodatni uslov da poslodavac ne traži jezik*/
							(docs.departure_type = 2 OR ((docs.full_recognition = 1 OR docs.full_recognition = 2) AND docs.language_required = 0) )
							AND docs.added =(
								SELECT
									COUNT(nrd.nrd_id) AS potrebni
								FROM
									idk_pp_nalog_required_documents nrd
								WHERE
									nrd.nrd_nalog_id = :nalog_id AND nrd.nrd_status = 1 AND nrd.nrd_type_id != 1 AND nrd.nrd_west_balkan = 1
							)
						)
						OR
						(
							/* Potpuno priznata diploma/Evaluacija uz dodatni uslov da poslodavac TRAŽI jezik*/
							((docs.full_recognition = 1 OR docs.full_recognition = 2) AND docs.language_required = 1) 
							AND docs.added =(
								SELECT
									COUNT(nrd.nrd_id) AS potrebni
								FROM
									idk_pp_nalog_required_documents nrd
								WHERE
									nrd.nrd_nalog_id = :nalog_id AND nrd.nrd_status = 1 AND nrd.nrd_type_id != 1 AND nrd.nrd_west_balkan = 1
							)
							AND 
							(
								SELECT 
									cvl_id  
								FROM 
									idk_candidate_verified_languages 
								JOIN 
									idk_kandidat_jezici 
								ON 
									idk_candidate_verified_languages.cvl_id = idk_kandidat_jezici.kj_id
								WHERE
									kj_kandidatid = docs.candidate
								AND
									cvl_certificate_expiration_date > NOW()
								AND
									cvl_active = 1
							)
						)
						OR
						(
							/* Radno iskustvo */
							docs.departure_type = 3 
							AND docs.added =(
								SELECT
									COUNT(nrd.nrd_id) AS potrebni
								FROM
									idk_pp_nalog_required_documents nrd
								WHERE
									nrd.nrd_nalog_id = :nalog_id AND nrd.nrd_status = 1 AND nrd.nrd_type_id != 1 AND nrd.nrd_work_experience = 1
							)
						)
						OR
						(
							/* Stručni kadar uz jezik */
							docs.departure_type = 0 
							AND docs.added =(
								SELECT
									COUNT(nrd.nrd_id) AS potrebni
								FROM
									idk_pp_nalog_required_documents nrd
								WHERE
									nrd.nrd_nalog_id = :nalog_id AND nrd.nrd_status = 1 AND nrd.nrd_type_id != 1 AND nrd.nrd_skilled_candidates = 1
							)
							AND 
							(
								SELECT 
									cvl_id  
								FROM 
									idk_candidate_verified_languages 
								JOIN 
									idk_kandidat_jezici 
								ON 
									idk_candidate_verified_languages.cvl_id = idk_kandidat_jezici.kj_id
								WHERE
									kj_kandidatid = docs.candidate
								AND
									cvl_certificate_expiration_date > NOW()
								AND
									cvl_active = 1
							)
						)
				");
				$query_gc->execute(array(
						":nalog_id" => $prs_nalog_id
				));

				$candidates = array();
				while($row_gc = $query_gc->fetch()){
					array_push($candidates, $row_gc['kandidat_id']);
				}
				return $candidates;

			break;

			case 16:

				$query_gc = $db->prepare("
					SELECT kandidat_id
					FROM idk_kandidati
					WHERE kandidat_status_prijave = 15
					AND kandidat_bio_na_terminu is null
					AND kandidat_nalog_id = :nalog_id
					AND datum_termina <= CURRENT_DATE();
				");
				$query_gc->execute(array(
						":nalog_id" => $prs_nalog_id
				));

				$candidates = array();
				while($row_gc = $query_gc->fetch()){
					array_push($candidates, $row_gc['kandidat_id']);
				}
				return $candidates;

			break;

			case 17:

				$query_gc = $db->prepare("
					SELECT kandidat_id
					FROM idk_kandidati
					WHERE kandidat_status_prijave = 18
					AND kandidat_nalog_id = :nalog_id
					AND (getNumberOfWeekdays(datum_termina,CURRENT_DATE())- :prs_create_after) >= 0
					AND (getNumberOfWeekdays(datum_termina,CURRENT_DATE())- :prs_create_after) % $frequency = 0
				");
				$query_gc->execute(array(
					":nalog_id"=>$prs_nalog_id,
					":prs_create_after"=>$prs_create_after
				));
				$candidates = array();
				while($row_gc = $query_gc->fetch()){
					array_push($candidates, $row_gc['kandidat_id']);
				}
				return $candidates;

			break;

			case 18:
				/*	POKUPI SVE KANDIDATE ZA ODGOVARAJUĆI NALOG GDJE NIJE UNEŠEN DATUM POČETAK RADA
				   	AKO JE KANDIDAT NON EU DRŽAVLJANIN ON MORA BITI NA STATUSU PRIJAVE DOBIO VIZU, A EU DRŽAVLJANIN NA STATUSU POTPISAN UGOVOR
					KREIRA SE U ZAVISNOSTI OD DATUMA ??
				*/
				$query_gc = $db->prepare("
					SELECT kandidat_id
					FROM idk_kandidati
					INNER JOIN idk_log_statusi_prijave ON idk_kandidati.kandidat_id = idk_log_statusi_prijave.lsp_kandidat_id
					WHERE kandidat_nalog_id = :nalog_id
					AND lsp_broj_dana IS NULL
					AND kandidat_dogovoreni_pocetak_rada IS NULL
					AND ((kandidat_drzavljanstvo_vrsta = 'NON-EU državljanin' AND kandidat_status_prijave = 27) OR (kandidat_drzavljanstvo_vrsta = 'EU državljanin' AND kandidat_status_prijave = 9))
					AND (getNumberOfWeekdays(CONVERT(lsp_datetime, DATE),CURRENT_DATE())- :prs_create_after) >= 0
					AND (getNumberOfWeekdays(CONVERT(lsp_datetime, DATE),CURRENT_DATE())- :prs_create_after) % $frequency = 0
				");
				$query_gc->execute(array(
					":nalog_id"=>$prs_nalog_id,
					":prs_create_after"=>$prs_create_after
				));
				$candidates = array();
				while($row_gc = $query_gc->fetch()){
					array_push($candidates, $row_gc['kandidat_id']);
				}
				return $candidates;

			break;

			case 19:
				/*	POKUPI SVE KANDIDATE ZA ODGOVARAJUĆI NALOG GDJE JE VEĆ UNEŠEN DATUM POČETAK RADA I GDJE NIJE TAJ POČETAK POTVRĐEN (NULL)
				   	AKO JE KANDIDAT NON EU DRŽAVLJANIN ON MORA BITI NA STATUSU PRIJAVE DOBIO VIZU, A EU DRŽAVLJANIN NA STATUSU POTPISAN UGOVOR
					KREIRA SE U ZAVISNOSTI OD DOGOVORENOG DATUMA POČETKA RADA
				*/
				$query_gc = $db->prepare("
					SELECT kandidat_id
					FROM idk_kandidati
					WHERE kandidat_nalog_id = :nalog_id
					AND kandidat_dogovoreni_pocetak_rada IS NOT NULL
					AND kandidat_potvrden_pocetak_rada IS NULL
					AND ((kandidat_drzavljanstvo_vrsta = 'NON-EU državljanin' AND kandidat_status_prijave = 27) OR (kandidat_drzavljanstvo_vrsta = 'EU državljanin' AND kandidat_status_prijave = 9))
					AND (getNumberOfWeekdays(kandidat_dogovoreni_pocetak_rada,CURRENT_DATE())- :prs_create_after) >= 0
					AND (getNumberOfWeekdays(kandidat_dogovoreni_pocetak_rada,CURRENT_DATE())- :prs_create_after) % $frequency = 0
				");
				$query_gc->execute(array(
					":nalog_id"=>$prs_nalog_id,
					":prs_create_after"=>$prs_create_after
				));
				$candidates = array();
				while($row_gc = $query_gc->fetch()){
					array_push($candidates, $row_gc['kandidat_id']);
				}
				return $candidates;

			break;

			default:
				$candidates = array();
				return $candidates;
			break;

		}

	}

	function getLastLevelOfRemindersFromCandidatesArrayR($candidateIds, $hasDocuments, $reminderSetting){

		Global $db;
		$result = array(
			"count" => array(),
			"candidateId" => array(),
			"reminderLevel" => array(), 
			"reminderStatus" => array()
		);

		/*
			BITNA NAPOMENA

			Ovdje stavljam paramatar 'has_documents' jer mi je bitno u funkciji koju kolonu da gledam za određeni tip remindera
			Sta se ovdje treba gledati
			Da prebrojim koliko remindera ima određeni kanidat za određeni pr_reminder_setting_id ili pr_reminder_document_id
			Iz tog razloga u zavisnosti od 'has_documents' znam da li cu gledati pr_reminder_setting_id ili pr_reminder_document_id
			Radim na taj nacin jer u tabeli idk_pp_reminders nemam informaciju o reminder_type
		*/

		$candidateIdsImplode 		= implode(",",$candidateIds); 
		$hasDocuments 				= intval($hasDocuments);
		$reminderSetting 			= intval($reminderSetting);

		if ( $candidateIdsImplode != '' AND in_array($hasDocuments, array(0,1)) AND $reminderSetting != 0 ) {
		
			if ($hasDocuments == 1) {
				$condition = 'pr.pr_reminder_document_id = '.$reminderSetting.'';
			} else {
				$condition = 'pr.pr_reminder_setting_id = '.$reminderSetting.'';
			}

			$query = $db->prepare('
				SELECT 
					pr_candidate_id, 
					pr_level AS reminder_level, 
					pr_status
				FROM 
					idk_pp_reminders 
				WHERE
					pr_id IN (
						SELECT 
							MAX(pr.pr_id) AS pr_id1
						FROM 
							idk_pp_reminders pr 
						WHERE 
							pr.pr_candidate_id IN ('.$candidateIdsImplode.')
							AND 
							'.$condition.'
						GROUP BY 
							pr.pr_candidate_id
					)
			');
			$query->execute();

			if ( $query->rowCount() != 0 ) {

				$cnt = 0;

				while($row = $query->fetch()) {
					$pr_candidate_id = intval($row["pr_candidate_id"]); 
					$reminder_level = intval($row["reminder_level"]);
					$pr_status = intval($row["pr_status"]);
					array_push($result["count"], $cnt);
					array_push($result["candidateId"], $pr_candidate_id);
					array_push($result["reminderLevel"], $reminder_level);
					array_push($result["reminderStatus"], $pr_status);

					$cnt++;
				}

				return $result;

			} else {

				return $result;

			}

		} else {

			return $result; 
		
		}

	}



?>
