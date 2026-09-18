<?php
//Error log enabled
ini_set('display_errors', 0);
ini_set('error_log', 'error_log');
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Env
require_once 'env.php';

//Connect to db
ob_start();
include("includes/connect.php");
include("one-signal.php");
date_default_timezone_set('Europe/Sarajevo');

	
	//User LogIn Session
	if(isset($_COOKIE['JSAppSession'])){

		Global $db;
		$employee_key = $_COOKIE["JSAppSession"];

		$query = $db->prepare("
						SELECT employee_id
						FROM idk_employees
						WHERE employee_key = :employee_key");

		$query->execute(array(
					':employee_key' => $employee_key));

		$employee = $query->fetch();

		Global $logged_employee_id;
		$logged_employee_id = $employee['employee_id'];
	}else{
		$logged_employee_id = 0;
	}

	function isLoggedIn(){
		Global $db;
		if(isset($_COOKIE['JSAppSession'])){
			$employee_key = $_COOKIE["JSAppSession"];
			$query = $db->prepare("
				SELECT employee_id
				FROM idk_employees
				WHERE employee_key = :employee_key
			");

			$query->execute(array(
				':employee_key' => $employee_key
			));
			if($query->rowCount() != 0){
				return 1;
			}else{
				return 0;
			}
		}else{
			return 0;
		}
	}
	
	function getEmployeeStatus(){
		Global $db;
		Global $logged_employee_id;
		
		$queryStatus = $db->prepare("
			SELECT employee_status
			FROM idk_employees
			WHERE employee_id = :employee_id
		");
		$queryStatus->execute(array(
			':employee_id' => $logged_employee_id
		));
		
		$rowStatus = $queryStatus->fetch();
		
		return $rowStatus["employee_status"];
		
	}
	function getEmployeeSupervizor(){
		Global $db;
		Global $logged_employee_id;
		
		$querySupervizor = $db->prepare("
			SELECT employee_supervizor
			FROM idk_employees
			WHERE employee_id = :employee_id
		");
		$querySupervizor->execute(array(
			':employee_id' => $logged_employee_id
		));
		
		$rowSupervizor = $querySupervizor->fetch();
		
		return $rowSupervizor["employee_supervizor"];
		
	}
	
	function getSkolaKandidata($id){
		Global $db;
		$query = $db->prepare("
			SELECT skola_naziv
			FROM idk_skole
			WHERE skola_id = :skola_id
		");
		$query->execute(array(
			':skola_id' => $id
		));
		$row = $query->fetch();
		$skola_naziv = $row["skola_naziv"];
		return $skola_naziv;
	}
	
	function getSkolaSmjerKandidata($id){
		Global $db;
		$query = $db->prepare("
			SELECT ss_naziv
			FROM idk_skole_smjerovi
			WHERE ss_id = :ss_id
		");
		$query->execute(array(
			':ss_id' => $id
		));
		$row = $query->fetch();
		$ss_naziv = $row["ss_naziv"];
		return $ss_naziv;
	}
	
	function getImePrezimeZaposlenika($id){
		Global $db;
		$query = $db->prepare("
			SELECT employee_firstname, employee_lastname
			FROM idk_employees
			WHERE employee_id = :employee_id
		");
		$query->execute(array(
			':employee_id' => $id
		));
		$row = $query->fetch();
		$employee_firstname = $row["employee_firstname"];
		$employee_lastname = $row["employee_lastname"];
		return $employee_firstname." ".$employee_lastname;
	}

	function getFirstAndLastNameUserPPR($id){
		Global $db;
		$query = $db->prepare("
			SELECT pu_fname, pu_lname
			FROM idk_pp_users
			WHERE pu_id = :pu_id
		");
		$query->execute(array(
			':pu_id' => $id
		));
		$row = $query->fetch();
		$firstnameUser = $row["pu_fname"];
		$lastnameUser = $row["pu_lname"];
		return $firstnameUser." ".$lastnameUser;
	}
	
	function getImeZaposlenika($id){
		Global $db;
		$query = $db->prepare("
			SELECT employee_firstname
			FROM idk_employees
			WHERE employee_id = :employee_id
		");
		$query->execute(array(
			':employee_id' => $id
		));
		$row = $query->fetch();
		$employee_firstname = $row["employee_firstname"];
		return $employee_firstname;
	}
	
	function getUstanovaKandidata($id){
		Global $db;
		$query = $db->prepare("
			SELECT naziv_ustanove_nd
			FROM idk_nd_ustanove
			WHERE id_ustanove_nd = :id_ustanove_nd
		");
		$query->execute(array(
			':id_ustanove_nd' => $id
		));
		$row = $query->fetch();
		$naziv_ustanove_nd = $row["naziv_ustanove_nd"];
		return $naziv_ustanove_nd;
	}
	
	function getSiteUrl() {
    Global $envConfig;
    echo $envConfig->CRM_URL;
	}

	//Site url return
	function getSiteUrlr() {
    Global $envConfig;
    return $envConfig->CRM_URL;
	}

	/*
		Funkcija za modula JOBSTEP PP START
	*/
	function getNalogForCandidatR($idCan){
		Global $db;
		$idCan = intval($idCan);
		if($idCan != 0){
			$query = $db->prepare("
				SELECT 
					kandidat_nalog_id
				FROM 
					idk_kandidati
				WHERE 
					kandidat_id = :kandidat_id
			");
			$query->execute(array( 
				':kandidat_id' => $idCan
			));
			$row = $query->fetch();
			$kandidat_nalog_id = intval($row["kandidat_nalog_id"]);
		}else{
			$kandidat_nalog_id = 0;
		}
		return $kandidat_nalog_id;
	}

	function getNazivNalogaR($nalogIdVr){
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
				$nalogNaziv = $row["nalog_naziv"];
				return $nalogNaziv; 
			}else{
				return "Undefined";
			}
		}else{
			return "Undefined";
		}
	}
	function getCompanyIdFromNalogR($nalogId){
		Global $db;
		$nalogId = intval($nalogId);
		if($nalogId != 0){
			$cntQuery = 0;
			$query = $db->prepare("
				SELECT 
					kompanija_id
				FROM 
					idk_nalozi 
				WHERE
					nalog_id  = :nalog_id 
			");
			$query->execute(array(
				':nalog_id' => $nalogId
			));
			$cntQuery = $query->rowCount();
			if($cntQuery != 0){
				$row = $query->fetch();
				$companyId = intval($row["kompanija_id"]);
				// return setNameForDenis($companyName); 
				return $companyId; 
			}else{
				return "Undefined";
			}
			
		}else{
			return "Undefined";
		}
	}
	function getCompanyNameR($companyIdVr){
		Global $db;
		$companyId = intval($companyIdVr);
		if($companyId != 0){
			$cntQuery = 0;
			$query = $db->prepare("
				SELECT 
					company_name
				FROM 
					idk_companies 
				WHERE
					company_id = :company_id
			");
			$query->execute(array(
				':company_id' => $companyId
			));
			$cntQuery = $query->rowCount();
			if($cntQuery != 0){
				$row = $query->fetch();
				$companyName = $row["company_name"];
				// return setNameForDenis($companyName); 
				return $companyName; 
			}else{
				return "Undefined";
			}
		}else{
			return "Undefined";
		}
	}

	function getCandidateStatusPrijave($candidate_id){
		Global $db;
		$candidate_status_prijave = 0;
		$query_get_status_prijave = $db -> prepare('
			SELECT kandidat_status_prijave
			FROM idk_kandidati
			WHERE kandidat_id = :candidate_id
		');
		
		$query_get_status_prijave -> execute(array(':candidate_id' => $candidate_id));
		$row_get_status_prijave = $query_get_status_prijave -> fetch();
		$candidate_status_prijave = $row_get_status_prijave['kandidat_status_prijave'];
		
		return $candidate_status_prijave;
	}

	function getPartnerForCandidatR($idCan){
		Global $db;
		$idCan = intval($idCan);
		if($idCan != 0){
			$query = $db->prepare("
				SELECT 
					kandidat_ppa_partner_id
				FROM 
					idk_kandidati
				WHERE 
					kandidat_id = :kandidat_id
			");
			$query->execute(array( 
				':kandidat_id' => $idCan
			));
			$row = $query->fetch();
			$kandidat_partner_id = intval($row["kandidat_ppa_partner_id"]);
		}else{
			$kandidat_partner_id = 0;
		}
		return $kandidat_partner_id;
	}

	function getCompanyForPartnerIdR($partnerIdVr){
		Global $db;
		$partnerId = intval($partnerIdVr);
		if($partnerId != 0){
			$cntQuery = 0;
			$query = $db->prepare("
				SELECT 
					ppa_company_id
				FROM 
					idk_pp_partners
				WHERE 
					ppa_id = :ppa_id
			");
			$query->execute(array(
				':ppa_id' => $partnerId
			));
			$cntQuery = $query->rowCount();
			if($cntQuery != 0){
				$row = $query->fetch();
				$companyId = intval($row["ppa_company_id"]);
				return $companyId;
			}else{
				return 0;
			}
		}else{
			return 0;
		}
	}

	function getProjectForCandidatR($idCan, $idNalog){
		Global $db; 
		$idCan = intval($idCan);
		$idNalog = intval($idNalog);
		$idProject = 0;
		if($idCan != 0 AND $idNalog != 0){
			$query = $db->prepare("
				SELECT 
					project_id 
				FROM 
					idk_projects
				JOIN
					idk_project_kandidati
				ON
					idk_projects.project_id = idk_project_kandidati.pk_projectid
				WHERE
					idk_project_kandidati.pk_kandidatid = :kandidat_id
				AND
					idk_projects.project_nalogid = :nalog_id
			");
			$query->execute(array(
				":kandidat_id" 	=> $idCan,
				":nalog_id"		=> $idNalog
			));
			if($query->rowCount() != 0){
				$row = $query->fetch();
				$idProject = intval($row['project_id']);
			}else{
				$idProject = 0;
			}
		}else{
			$idProject = 0;
		}
		return $idProject;
	}

	function setStatusPrijaveForCandidatR($idCan, $statusVr){
		Global $db;
		$idCan = intval($idCan);
		$statusVr = intval($statusVr);
		if($idCan != 0){
			$query = $db->prepare("
				UPDATE
					idk_kandidati
				SET
					kandidat_status_prijave = :status_prijave
				WHERE
					kandidat_id = :kandidat_id
			");
			$query->execute(array(
				":status_prijave" 	=> $statusVr,
				":kandidat_id" 		=> $idCan
			));
			return 1;
		}else{
			return 0;
		}
	}
	/*
		Funkcija za modula JOBSTEP PP END 
	*/

	function addToLogsStatusPrijave($stari_projekt_id, $novi_projekt_id, $status_id, $kandidat_id, $izvor, $is_from_queue = NULL){
		Global $db;
		Global $logged_employee_id;
		$log_date = date('Y-m-d H:i:s');
		$partner_sql = "SELECT jp_id, kandidat_ime, kandidat_prezime, jp_lang, jp_fcmtoken FROM idk_jobstep_partners JOIN idk_kandidati ON idk_jobstep_partners.jp_id = idk_kandidati.kandidat_partnerid WHERE kandidat_id = :kandidat_id";
		$partner_query = $db->prepare($partner_sql);
		$partner_query->execute(array(
			":kandidat_id" => $kandidat_id
		));
		$partner_row = $partner_query->fetch();
		$partner_id = $partner_row['jp_id'];
		$kandidat_name = $partner_row['kandidat_ime']." ".$partner_row['kandidat_prezime'];
		$onesignal_id = $partner_row['jp_fcmtoken'];
		if($partner_id != null){
			$notification_type = getPartnerNotificationTypeId("CANDIDATES");
			$send_notifications = shouldSendPersonalNotification($partner_id,$notification_type);
		}

		$stari_status_query = $db->prepare("SELECT lsp_status_prijave_id FROM idk_log_statusi_prijave WHERE lsp_kandidat_id = :kandidat_id AND lsp_broj_dana IS NULL");
		$stari_status_query->execute(array(
			":kandidat_id" => $kandidat_id
		));
		$stari_status_row = $stari_status_query->fetch();
		$stari_status_id = $stari_status_row['lsp_status_prijave_id'];

		if($onesignal_id != null && $send_notifications == 1){
			if($status_id == 2 && $partner_id != null && $stari_status_id != 2){
				if($partner_row['jp_lang'] == 'en'){
					$title = "Candidate";
					$content = "A new candidate has applied to the ad you shared";
				} else {
					$title = "Kandidat";
					$content = "Ein neuer Kandidat hat sich auf die von Ihnen geteilte Anzeige beworben";
				}
				$payload = '{"candidate_id": "'.$kandidat_id.'"}';
				$notification_title		='{ "en":"Candidate", "de":"Kandidat" }';
				$notification_content 	='{ "en":"A new candidate has applied to the ad you shared", "de":"Ein neuer Kandidat hat sich auf die von Ihnen geteilte Anzeige beworben"}';
				$action="NAVIGATE_TO_CANDIDATE";
				createPartnerPersonalNotification($partner_id, $notification_type, $payload, $action, $notification_title, $notification_content);
				sendNotification($onesignal_id, $title, $content);
			} 
			elseif($status_id == 10 && $partner_id != null){
				if($partner_row['jp_lang'] == "en"){
					$title = "Candidate changed status";
					$content = "The candidate's status has been changed to 'Work started'";
				} else {
					$title = "Kandidat";
					$content = 'Der Status des Kandidaten wurde in „Arbeitsbeginn“ geändert.';
				}
				$payload = '{"candidate_id": "'.$kandidat_id.'"}';
				$notification_title		='{ "en":"Candidate changd statuse", "de":"Kandidat" }';
				$notification_content 	='{ "en": "The candidate\'s status has been changed to \'Work started\'", "de": "Der Status des Kandidaten wurde in \'Arbeitsbeginn\' geändert." }';
				$action="NAVIGATE_TO_CANDIDATE";
				createPartnerPersonalNotification($partner_id, $notification_type, $payload, $action, $notification_title, $notification_content);
				sendNotification($onesignal_id, $title, $content);
			}
		}
		//check da li ima unesen log za prethodni projekt, posto je na pocetku sve prazno, treba unijeti, samo u slucaju kada se prebaciva sa castinga na intervju
		if($stari_projekt_id != null){
			$stari_projekt_naziv = getProjectFullName($stari_projekt_id);
		}else{
			$stari_projekt_naziv = "";
		}
		
		$novi_projekt_naziv = getProjectFullName($novi_projekt_id);
		
		if(strpos($stari_projekt_naziv, 'Casting') == true AND strpos($novi_projekt_naziv, 'Intervju') == true){
			
			$check_casting = $db->prepare("
							SELECT lsp_kandidat_id, lsp_datetime, lsp_id
							FROM idk_log_statusi_prijave 
							WHERE lsp_kandidat_id = :lsp_kandidat_id AND lsp_projekt_id = :lsp_projekt_id AND lsp_status_prijave_id = :lsp_status_prijave_id AND lsp_broj_dana is NULL
			");
			$check_casting->execute(array(
							":lsp_kandidat_id" => $kandidat_id,
							":lsp_projekt_id" => $stari_projekt_id,
							":lsp_status_prijave_id" => $status_id
			));
			if($check_casting->rowCount() > 0){
				$row_check_casting = $check_casting->fetch();
				$lsp_id = $row_check_casting['lsp_id'];
				$lsp_datetime = $row_check_casting['lsp_datetime'];
				$diff_novi = strtotime($log_date) - strtotime($lsp_datetime);
				$day_novi = floor($diff_novi/86400);
				//UPDATE BROJA DANA
				$update_days = $db->prepare("
							UPDATE idk_log_statusi_prijave
							SET lsp_broj_dana = :lsp_broj_dana
							WHERE lsp_kandidat_id = :lsp_kandidat_id AND lsp_id = :lsp_id
				");
				$update_days->execute(array(
							':lsp_id' => $lsp_id,
							':lsp_kandidat_id' => $kandidat_id,
							':lsp_broj_dana' => $day_novi
				));
			}else{
				
				$query_logs = $db->prepare("SELECT log_employeeid, log_date FROM idk_logs WHERE log_desc LIKE 'Vezao kandidata: $kandidat_id za projekt $stari_projekt_id.' ");
				$query_logs->execute();
				$row_logs = $query_logs->fetch();
				$stari_employee_id = $row_logs['log_employeeid'];
				$stari_log_date = $row_logs['log_date'];
				
				$diff_novi = strtotime($log_date) - strtotime($stari_log_date);
				$day_novi = floor($diff_novi/86400);
				
				//INSERT STAROG LOGA
				$insert_log_s = $db->prepare("
								INSERT INTO idk_log_statusi_prijave
									(lsp_kandidat_id, lsp_projekt_id, lsp_status_prijave_id, lsp_employee_id, lsp_izvor, lsp_datetime, lsp_broj_dana)
								VALUES
									(:lsp_kandidat_id,:lsp_projekt_id,:lsp_status_prijave_id,:lsp_employee_id,:lsp_izvor,:lsp_datetime,:lsp_broj_dana)
				");
				$insert_log_s->execute(array(
								':lsp_kandidat_id' => $kandidat_id,
								':lsp_projekt_id' => $stari_projekt_id,
								':lsp_status_prijave_id' => $status_id,
								':lsp_employee_id' => $stari_employee_id,
								':lsp_izvor' => 1,
								':lsp_datetime' => $stari_log_date,
								':lsp_broj_dana' => $day_novi
				));
			}
			//echo $stari_employee_id." ".$stari_log_date;
		}else{
			$normal_check = $db->prepare("
							SELECT lsp_id, lsp_datetime
							FROM idk_log_statusi_prijave
							WHERE lsp_kandidat_id = :lsp_kandidat_id AND lsp_broj_dana is NULL
			");
			$normal_check->execute(array(
							":lsp_kandidat_id" => $kandidat_id
			));
			if($normal_check->rowCount() > 0){
				$row_normal_check = $normal_check->fetch();
				$lsp_id = $row_normal_check['lsp_id'];
				$lsp_datetime = $row_normal_check['lsp_datetime'];
				$diff_novi = strtotime($log_date) - strtotime($lsp_datetime);
				$day_novi = floor($diff_novi/86400);
				//normalni updejt broja dana za stari status
				$update_days = $db->prepare("
							UPDATE idk_log_statusi_prijave
							SET lsp_broj_dana = :lsp_broj_dana
							WHERE lsp_kandidat_id = :lsp_kandidat_id AND lsp_id = :lsp_id
				");
				$update_days->execute(array(
							':lsp_id' => $lsp_id,
							':lsp_kandidat_id' => $kandidat_id,
							':lsp_broj_dana' => $day_novi
				));
			}
		}
		
		//INSERT NOVOG LOGA
		$insert_log = $db->prepare("
						INSERT INTO idk_log_statusi_prijave
							(lsp_kandidat_id, lsp_projekt_id, lsp_status_prijave_id, lsp_employee_id, lsp_izvor, lsp_datetime)
						VALUES
							(:lsp_kandidat_id,:lsp_projekt_id,:lsp_status_prijave_id,:lsp_employee_id,:lsp_izvor,:lsp_datetime)
		");
		$insert_log->execute(array(
						':lsp_kandidat_id' => $kandidat_id,
						':lsp_projekt_id' => $novi_projekt_id,
						':lsp_status_prijave_id' => $status_id,
						':lsp_employee_id' => $logged_employee_id,
						':lsp_izvor' => $izvor,
						':lsp_datetime' => $log_date,
		));
		
		$nalogId = getNalogIdForProjektR($novi_projekt_id);
		if(is_null($is_from_queue)){
			if(isReservedByProject($novi_projekt_naziv) == true OR isReservedByStatusPrijave($kandidat_id) == true){
				updateNalogIdForCandidat($kandidat_id, $nalogId);
			}else{
				resetNalogIdForCandidat($kandidat_id);
				pullFromProjectCandidateQueue($kandidat_id, $stari_projekt_id);
			}
		}
		else{
			checkCandidateInputs($kandidat_id, $nalogId);
		}
	}

	function getProjectFullName($project_id) {

		Global $db;
		Global $logged_employee_id;
	
		$query_kandidat = $db->prepare("
						SELECT project_name
						FROM idk_projects
						WHERE project_id = :project_id");
	
		$query_kandidat->execute(array(
				':project_id' => $project_id
				));
	
		$kandidat = $query_kandidat->fetch();
			$project_name = $kandidat['project_name'];
	
			return $project_name;
	
	}

	function getNalogIdForProjektR($projektId){
		Global $db; 
		$query = $db->prepare("
			SELECT 
				project_nalogid
			FROM 
				idk_projects
			WHERE 
				project_id = :project_id
		");
		$query->execute(array(
			':project_id' => $projektId
		));
		$row = $query->fetch();
		
		$project_nalogid = $row["project_nalogid"];
		
		return $project_nalogid; 
	}

	function isReservedByProject($project_name){
		$return_value	= false;
		if(		
				strpos($project_name, 'BOT - ispunjava uslove')
			OR	strpos($project_name, 'Završeni kandidati')
			OR	strpos($project_name, 'Obrađeno')
			OR	strpos($project_name, 'Intervju')
			OR	strpos($project_name, 'Casting')
			OR	strpos($project_name, '- Ugovor')
			OR	strpos($project_name, 'počeli')
			OR 	strpos($project_name, 'Baza - odgovara za nalog')
		){
			$return_value	= true;
		}
		return $return_value;
	}

	function isReservedByStatusPrijave($kandidat_id){
		Global $db;
		$rezervisanje = false;
		
		$query_get_status_prijave = $db -> prepare('
			SELECT sp.rezervisanje
			FROM (
				SELECT sqk.kandidat_status_prijave
				FROM idk_kandidati sqk
				WHERE sqk.kandidat_id = :kandidat_id
			) k
			JOIN idk_kandidat_status_prijave sp
			ON sp.status_id = k.kandidat_status_prijave
		');
		$query_get_status_prijave -> execute(array(':kandidat_id' => $kandidat_id));
		if($query_get_status_prijave -> rowCount() != 0){
			$row_get_status_prijave = $query_get_status_prijave -> fetch();
			$rezervisanje = boolval($row_get_status_prijave['rezervisanje']);
		}
		
		return $rezervisanje;
	}

	function updateNalogIdForCandidat($idKandidat, $idNalog){
		Global $db;
		$query = $db->prepare("
			UPDATE
				idk_kandidati
			SET 
				kandidat_nalog_id = :kandidat_nalog_id,
				kandidat_latest_reserved_time = :kandidat_latest_reserved_time
			WHERE 
				kandidat_id = :kandidat_id
		");
		$query->execute(array(
			':kandidat_id' => $idKandidat, 
			':kandidat_nalog_id' => $idNalog, 
			':kandidat_latest_reserved_time' => date("Y-m-d H:i:s")
		));
	}

	function resetNalogIdForCandidat($idKandidat){
		Global $db;
		$getCurrentDetails = $db->prepare("
				SELECT
				kandidat_nalog_id,
				kandidat_latest_reserved_time,
				kandidat_ppa_partner_id,
				kandidat_pp_pozicija,
				kandidat_pp_lokacija,
				kandidat_pp_plata,
				kandidat_pp_povezao_user_id,
				kandidat_pp_razlog_odbijanja,
				kandidat_pp_datum_prihvatanja
			FROM
				idk_kandidati
			WHERE
				kandidat_id = :kandidat_id
		");
		$getCurrentDetails ->execute(array(
				'kandidat_id'	=> $idKandidat
		)); 

		$currentDetails 				= $getCurrentDetails->fetch();

		$kandidat_nalog_id 				= $currentDetails["kandidat_nalog_id"];
		$kandidat_latest_reserved_time	= $currentDetails["kandidat_latest_reserved_time"];
		$kandidat_ppa_partner_id 		= $currentDetails["kandidat_ppa_partner_id"];
		$kandidat_pp_pozicija 			= $currentDetails["kandidat_pp_pozicija"];
		$kandidat_pp_lokacija		 	= $currentDetails["kandidat_pp_lokacija"];
		$kandidat_pp_plata 				= $currentDetails["kandidat_pp_plata"];
		$kandidat_pp_povezao_user_id 	= $currentDetails["kandidat_pp_povezao_user_id"];
		$kandidat_pp_razlog_odbijanja 	= $currentDetails["kandidat_pp_razlog_odbijanja"];
		$kandidat_pp_datum_prihvatanja 	= $currentDetails["kandidat_pp_datum_prihvatanja"];

		$query = $db->prepare("
			UPDATE
				idk_kandidati
			SET 
				kandidat_nalog_id = :kandidat_nalog_id,
				kandidat_latest_reserved_time = :kandidat_latest_reserved_time,
				kandidat_ppa_partner_id = :kandidat_ppa_partner_id,
				kandidat_pp_pozicija = :kandidat_pp_pozicija,
				kandidat_pp_lokacija = :kandidat_pp_lokacija,
				kandidat_pp_plata = :kandidat_pp_plata,
				kandidat_pp_povezao_user_id = :kandidat_pp_povezao_user_id,
				kandidat_pp_razlog_odbijanja = :kandidat_pp_razlog_odbijanja,
				kandidat_pp_datum_prihvatanja = :kandidat_pp_datum_prihvatanja
			WHERE 
				kandidat_id = :kandidat_id
		");
		$query->execute(array(
			':kandidat_id' 						=> $idKandidat, 
			':kandidat_latest_reserved_time'	=> NULL, 
			':kandidat_nalog_id' 				=> NULL, 
			':kandidat_ppa_partner_id' 			=> NULL,
			':kandidat_pp_lokacija' 			=> NULL,
			':kandidat_pp_plata' 				=> NULL,
			':kandidat_pp_povezao_user_id' 		=> NULL,
			':kandidat_pp_razlog_odbijanja' 	=> NULL,
			':kandidat_pp_pozicija' 			=> NULL,
			':kandidat_pp_datum_prihvatanja' 	=> NULL
		));
		$log_desc = "Resetovani podaci za kandidata ID=[".$idKandidat."]. 
					Podaci resetovani na NULL vrijednost sa vrijednosti: 
						kandidat_latest_reserved_time=".$kandidat_latest_reserved_time." ,
						kandidat_nalog_id=".$kandidat_nalog_id." ,
						kandidat_ppa_partner_id=".$kandidat_ppa_partner_id." ,
						kandidat_pp_lokacija=".$kandidat_pp_lokacija." ,
						kandidat_pp_plata=".$kandidat_pp_plata." ,
						kandidat_pp_povezao_user_id=".$kandidat_pp_povezao_user_id." ,
						kandidat_pp_razlog_odbijanja=".$kandidat_pp_razlog_odbijanja." ,
						kandidat_pp_pozicija=".$kandidat_pp_pozicija." ,
						kandidat_pp_datum_prihvatanja=".$kandidat_pp_datum_prihvatanja." 
				";
		addToLogs($log_desc, 0);
		// Global $db;
		// $query = $db->prepare("
			// UPDATE
				// idk_kandidati
			// SET 
				// kandidat_nalog_id = :kandidat_nalog_id,
				// kandidat_latest_reserved_time = :kandidat_latest_reserved_time
			// WHERE 
				// kandidat_id = :kandidat_id
		// ");
		// $query->execute(array(
			// ':kandidat_id' => $idKandidat, 
			// ':kandidat_nalog_id' => NULL, 
			// ':kandidat_latest_reserved_time' => NULL
		// ));
	}

	function pullFromProjectCandidateQueue($candidate_id, $current_project_id){
		Global $db;
		$query_get_first_in_queue = $db -> prepare('
			SELECT queue_project_id, queue_candidate_id, queue_id
			FROM idk_project_candidate_queue
			WHERE queue_is_assigned = 0
			AND queue_candidate_id = :candidate_id
			ORDER BY queue_id ASC
		');
		
		$return_value = 0;
		$query_get_first_in_queue -> execute(array(':candidate_id' => $candidate_id));
		if($query_get_first_in_queue -> rowCount()){
			$row_get_first_in_queue = $query_get_first_in_queue -> fetch();
			$queue_project_id 	= $row_get_first_in_queue['queue_project_id'];
			$queue_candidate_id = $row_get_first_in_queue['queue_candidate_id'];
			$queue_id 			= $row_get_first_in_queue['queue_id'];
			
			$query_insert_into_pk = $db -> prepare('
				INSERT INTO idk_project_kandidati
					(pk_projectid, pk_kandidatid)
				VALUES 
					(:queue_project_id, :queue_candidate_id)
			');
			$query_insert_into_pk -> execute(array(
				':queue_project_id' 	=> $queue_project_id,
				':queue_candidate_id' 	=> $queue_candidate_id
			));
			
			$update_project_candidate_queue = $db -> prepare('
				UPDATE idk_project_candidate_queue
				SET queue_is_assigned = 1
				WHERE queue_id = :queue_id
			');
			$update_project_candidate_queue -> execute(array(':queue_id' => $queue_id));
			
			
			$log_desc = "Kandidat: " .$queue_candidate_id. " prebačen iz queue u projekat " .$queue_project_id. ".";
			$log_type = "0";
			addToLogs($log_desc, $log_type);
			addToLogsStatusPrijave($current_project_id, $queue_project_id, getStatusPrijaveByProjectId($queue_project_id), $candidate_id, '4', 1);
			$return_value = 1;
		}
		return $return_value;
	}

	function getStatusPrijaveByProjectId($project_id){
		Global $db;
		$query_get_project_name = $db -> prepare('
			SELECT project_name
			FROM idk_projects
			WHERE project_id = :project_id
		');
		$query_get_project_name -> execute(array(':project_id' => $project_id));
		$status_prijave = 0;
		if($query_get_project_name -> rowCount()){
			$row_get_project_name = $query_get_project_name -> fetch();
			$project_name = $row_get_project_name['project_name'];
			if(
					strpos($project_name, 'Prijave')
				OR  strpos($project_name, 'U obradi')
				OR  strpos($project_name, 'BOT - ne ispunjava uslove')
				OR 	strpos($project_name, 'Nije došao na razgovor')
			){$status_prijave = 2;}
			else if(
					strpos($project_name, 'BOT - ispunjava uslove')
				OR  strpos($project_name, 'Obrađeno')
				OR  strpos($project_name, 'Baza - odgovara za nalog')
			){$status_prijave = 6;}
			else if(
					strpos($project_name, 'Casting')
				OR 	strpos($project_name, 'Intervju')
				 
			){$status_prijave = 3;}
			else if(strpos($project_name, 'Ugovor')){
				$status_prijave = 7;
			}
			else if(strpos($project_name, 'Odbijen')){
				$status_prijave = 5;
			}
			else if(
					strpos($project_name, 'Završeni kandidati')
				OR	strpos($project_name, 'počeli')
				
			){$status_prijave = 10;}
	
		}
		return $status_prijave;			
	}	

	function checkCandidateInputs($kandidat_id, $nalog_from_queue = null){
	
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
			
			if($nalog_struka != null){
				$query_kandidat_edukacija_srednja = $db->prepare("
						SELECT ke_id, ke_smjer_id, ss_struka_id FROM idk_kandidat_edukacija 
						LEFT JOIN idk_skole_smjerovi sm on ke_smjer_id = sm.ss_id
						WHERE ke_kandidat_id = $kandidat_id AND ss_struka_id = $nalog_struka ");
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
					addToLogsStatusPrijave($stari_projekt_id, $novi_projekt_id, $status_id, $kandidat_id, $izvor);
					
					$query_bot_project_prijave = $db->prepare("
					SELECT project_id FROM idk_projects WHERE project_nalogid = $nalog_id AND project_name LIKE '%Prijave%' ");
					$query_bot_project_prijave->execute();
					$row_project_prijave = $query_bot_project_prijave->fetch();
					$prijave_project_id = $row_project_prijave['project_id'];
					$query_delete = $db->prepare("DELETE FROM idk_project_kandidati WHERE pk_projectid = $prijave_project_id AND pk_kandidatid = $kandidat_id");
					$query_delete->execute();
				}
				
			}
		}
	}

	function addToLogs($log_desc, $log_type){
		Global $db;
		Global $logged_employee_id;
		$log_date = date('Y-m-d H:i:s');
	
		$log_query = $db->prepare("
						INSERT INTO idk_logs
							(log_employeeid, log_desc, log_date, log_type)
						VALUES
							(:log_employeeid, :log_desc, :log_date, :log_type)");
	
		$log_query->execute(array(
						':log_employeeid' => $logged_employee_id,
						':log_desc' => $log_desc,
						':log_date' => $log_date,
						':log_type' => $log_type));
	
		//Log za skladiste - $log_type = 1
		//Log za pozicije  - $log_type = 2
		//Log za biljeske  - $log_type = 3
	}

	function updateReminderUgovor($kandidat_id, $reminder_type){
		Global $db;
		Global $logged_employee_id;
		$current_date = date("Y-m-d H:i:s");
		//Kombinacije rezultata
		//101	-	Nisu poslani odgovarajući parametri funkciji
		//102	-	Status koji nije pokriven u funkciji. Pokrivene vrijednosti su: 2, 3
		//103	- 	User id = 0
		
		$get_reminder = $db -> prepare('
			SELECT 
				pr_id 
			FROM 
				idk_pp_reminders
			JOIN
				idk_pp_reminder_settings
			ON
				idk_pp_reminders.pr_reminder_setting_id = idk_pp_reminder_settings.prs_id
			WHERE
				pr_candidate_id = :candidate_id
			AND
				prs_reminder_type_id = :type_id
			AND
				prs_active = 1
		');
		$get_reminder -> execute(array(
			":candidate_id" => $kandidat_id,
			":type_id"		=> $reminder_type
		));
		if($get_reminder->rowCount() != 0){
			$result_reminder = $get_reminder -> fetch();
			$reminder_id = $result_reminder['pr_id'];
			if($reminder_type == 5){
				$update_reminder_done = $db -> prepare("
					UPDATE 
						idk_pp_reminders
					SET
						pr_status = 3,
						pr_date_completed = :current_date
					WHERE
						pr_id = :reminder_id
				");
				$update_reminder_done -> execute(array(
					":reminder_id" 	=> $reminder_id,
					":current_date" => $current_date
				));
			}
			elseif($reminder_type == 6){
				$update_reminder_done = $db -> prepare("
					UPDATE 
						idk_pp_reminders
					SET
						pr_status = 3,
						pr_date_completed = :current_date,
						pr_user_assigned = :logged_employee
					WHERE
						pr_id = :reminder_id
				");
				$update_reminder_done -> execute(array(
					":reminder_id" 		=> $reminder_id,
					":current_date" 	=> $current_date,
					":logged_employee" 	=> $logged_employee_id
				));
			}
		}	
	}
	function shouldSendPersonalNotification($partner_id,$notification_type){
		Global $db;
		
		createPersonalNotificationPreferences($partner_id);

		//check if notifications should be sent based on partner preferences, 0-false 1-true
		$get_partner_data_query=$db->prepare("SELECT preference_id,mute_all_notifications,blocked_notification_types  FROM idk_partner_personal_notifications_preferences WHERE partner_id = :partner_id");
		$get_partner_data_query->execute(array(
			':partner_id'=>$partner_id
		));
		$partner_data = $get_partner_data_query->fetch();
		$preference_id 					= $partner_data['preference_id'];
		$partner_notifications_mute 	= $partner_data['mute_all_notifications'];
		$partner_blocked_notifications 	= $partner_data['blocked_notification_types'];
		$row_count = $get_partner_data_query->rowCount();

		if(in_array($notification_type,explode(',', $partner_blocked_notifications)) OR $partner_notifications_mute==1 OR $row_count==0){
			return 0;
		}else{
			return 1;
		} 	
		
	}

	function createPartnerPersonalNotification($parnter_id, $notification_type, $payload, $action, $title, $content){
		Global $db;

		$create_notification_query = $db -> prepare("INSERT INTO idk_partner_notification(notification_type_id, notification_audience, notification_payload, 
																	notification_title, notification_content, notification_partner_id, notification_action)
														VALUES(:type_id,'PERSONAL',:payload,:title,:content,:partner_id,:action_enum);");

		$create_notification_query->execute(array(
			':type_id'=>$notification_type,
			':payload'=>$payload,
			':title'=>$title,
			':content'=>$content,
			':partner_id'=>$parnter_id,
			':action_enum'=>$action
		));
	}

	function createPartnerCompanyNotification($company_id, $notification_type, $payload, $action, $title, $content){
		Global $db;

		$company_name_query=$db->prepare("SELECT pc_name  FROM idk_partner_companies WHERE pc_id = $company_id");
		$company_name_query->execute();
		$company_data=$company_name_query->fetch();
		$company_name=$company_data['pc_name'];

		$create_notification_query = $db -> prepare("INSERT INTO idk_partner_notification(notification_type_id, notification_audience, notification_payload, 
																	notification_title, notification_content, notification_action)
														VALUES(:type_id,:company,:payload,:title,:content,:action_enum);");

		$create_notification_query->execute(array(
			':type_id'=>$notification_type,
			':company'=>$company_name,
			':payload'=>$payload,
			':title'=>$title,
			':content'=>$content,
			':action_enum'=>$action
		));

	}

	function createPartnerGlobalNotification($company_id, $notification_type, $payload, $action, $title, $content){
		Global $db;

		$company_name_query=$db->prepare("SELECT pc_name  FROM idk_partner_companies WHERE pc_id = $company_id");
		$company_name_query->execute();
		$company_data=$company_name_query->fetch();
		$company_name=$company_data['pc_name'];

		$create_notification_query = $db -> prepare("INSERT INTO idk_partner_notification(notification_type_id, notification_audience, notification_payload, 
																	notification_title, notification_content, notification_action)
														VALUES(:type_id,:company,:payload,:title,:content,:action_enum);");

		$create_notification_query->execute(array(
			':type_id'=>$notification_type,
			':company'=>$company_name,
			':payload'=>$payload,
			':title'=>$title,
			':content'=>$content,
			':action_enum'=>$action
		));


	}

	function getPartnerNotificationTypeId($notification_type){
		Global $db;

		$get_notification_type=$db->prepare("SELECT type_id  FROM idk_partner_personal_notification_types WHERE type_name LIKE '$notification_type'");
		$get_notification_type->execute();
		$notification_id=$get_notification_type->fetch();

		return $notification_id['type_id'];

	}
	function createPersonalNotificationPreferences($partner_id){
		Global $db;

		$get_notification_preferences = $db->prepare("SELECT partner_id FROM idk_partner_personal_notifications_preferences WHERE partner_id = :partner_id");
		$get_notification_preferences->execute(array(
			':partner_id'=>$partner_id
		));

		$preferences = $get_notification_preferences->fetch();

		if($preferences['partner_id'] == NULL){
			$create_preferences = $db->prepare("INSERT INTO idk_partner_personal_notifications_preferences (partner_id) VALUES (:partner_id)");
			$create_preferences->execute(array(
				':partner_id'=>$partner_id
			));
		}
	}

?>
